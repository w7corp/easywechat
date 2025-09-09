# 自定义 HTTP 客户端日志

在 6.x 版本中，虽然移除了默认的日志功能，但用户仍然可以通过自定义 HTTP 客户端来实现请求和响应的日志记录。

## 基本用法

所有的 Application 类都实现了 `LoggerAwareInterface`，你可以设置一个日志记录器，然后创建一个支持日志的 HTTP 客户端：

```php
use EasyWeChat\OfficialAccount\Application;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\HttpClient\HttpClient;
use Psr\Log\LoggerAwareInterface;

$config = [
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
    // 其他配置...
];

$app = new Application($config);

// 创建一个日志记录器
$logger = new Logger('easywechat');
$logger->pushHandler(new StreamHandler('/path/to/your/logfile.log', Logger::DEBUG));

// 设置日志记录器到应用实例
$app->setLogger($logger);

// 创建支持日志的 HTTP 客户端
$httpClient = HttpClient::create();

// 如果 HTTP 客户端支持 LoggerAwareInterface，将自动设置日志记录器
if ($httpClient instanceof LoggerAwareInterface) {
    $httpClient->setLogger($logger);
}

// 设置自定义 HTTP 客户端
$app->setHttpClient($httpClient);
```

## 使用装饰器模式的日志客户端

你也可以创建一个装饰器来包装现有的 HTTP 客户端，添加日志功能：

```php
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class LoggingHttpClient implements HttpClientInterface, LoggerAwareInterface
{
    private HttpClientInterface $client;
    private ?LoggerInterface $logger = null;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
        
        if ($this->client instanceof LoggerAwareInterface) {
            $this->client->setLogger($logger);
        }
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        // 记录请求日志
        if ($this->logger) {
            $this->logger->info('HTTP Request', [
                'method' => $method,
                'url' => $url,
                'options' => $this->sanitizeOptions($options),
            ]);
        }

        $startTime = microtime(true);
        
        try {
            $response = $this->client->request($method, $url, $options);
            
            // 记录响应日志
            if ($this->logger) {
                $duration = microtime(true) - $startTime;
                $this->logger->info('HTTP Response', [
                    'method' => $method,
                    'url' => $url,
                    'status_code' => $response->getStatusCode(),
                    'duration' => round($duration * 1000, 2) . 'ms',
                ]);
            }
            
            return $response;
        } catch (\Throwable $e) {
            // 记录错误日志
            if ($this->logger) {
                $duration = microtime(true) - $startTime;
                $this->logger->error('HTTP Request Failed', [
                    'method' => $method,
                    'url' => $url,
                    'error' => $e->getMessage(),
                    'duration' => round($duration * 1000, 2) . 'ms',
                ]);
            }
            
            throw $e;
        }
    }

    public function stream($responses, float $timeout = null): iterable
    {
        return $this->client->stream($responses, $timeout);
    }

    private function sanitizeOptions(array $options): array
    {
        // 移除敏感信息，如密码、密钥等
        if (isset($options['auth'])) {
            $options['auth'] = '[HIDDEN]';
        }
        
        if (isset($options['headers']['Authorization'])) {
            $options['headers']['Authorization'] = '[HIDDEN]';
        }
        
        return $options;
    }
}
```

## 使用自定义日志客户端

```php
use EasyWeChat\OfficialAccount\Application;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\HttpClient\HttpClient;

$config = [
    'app_id' => 'your-app-id',
    'secret' => 'your-app-secret',
];

$app = new Application($config);

// 创建日志记录器
$logger = new Logger('easywechat-http');
$logger->pushHandler(new StreamHandler('/var/log/easywechat-http.log', Logger::DEBUG));

// 创建装饰后的 HTTP 客户端
$baseClient = HttpClient::create();
$loggingClient = new LoggingHttpClient($baseClient);
$loggingClient->setLogger($logger);

// 设置到应用实例
$app->setHttpClient($loggingClient);

// 现在所有的 HTTP 请求都会被记录
$accessToken = $app->getAccessToken()->getToken();
```

## 日志输出示例

使用上述配置后，你的日志文件将包含类似以下的内容：

```
[2024-01-01 10:00:00] easywechat-http.INFO: HTTP Request {"method":"GET","url":"https://api.weixin.qq.com/cgi-bin/token","options":{"query":{"grant_type":"client_credential","appid":"your-app-id","secret":"[HIDDEN]"}}}
[2024-01-01 10:00:01] easywechat-http.INFO: HTTP Response {"method":"GET","url":"https://api.weixin.qq.com/cgi-bin/token","status_code":200,"duration":"156.75ms"}
```

## 注意事项

1. **敏感信息处理**：在记录日志时，请务必过滤掉敏感信息，如 `secret`、`access_token`、`password` 等。

2. **性能影响**：启用详细的 HTTP 日志记录可能会对性能产生影响，特别是在高并发场景下。建议在生产环境中适当调整日志级别。

3. **日志轮转**：确保配置适当的日志轮转策略，避免日志文件过大。

4. **自动设置**：当你在应用实例上设置日志记录器后，如果 HTTP 客户端实现了 `LoggerAwareInterface`，框架会自动将日志记录器设置到 HTTP 客户端上。
