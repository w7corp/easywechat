<?php

/**
 * Http.php.
 *
 * Part of EasyWeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 *
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace EasyWeChat\Support;

/**
 * Class Http.
 */
class Http
{
    /**
     * Constants for available HTTP methods.
     */
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';

    /**
     * CURL handle.
     *
     * @var resource handle
     */
    protected $curl;

    /**
     * Create the cURL resource.
     */
    public function __construct()
    {
        $this->curl = curl_init();
    }

    /**
     * Clean up the cURL handle.
     */
    public function __destruct()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }

    /**
     * Get the cURL handle.
     *
     * @return resource cURL handle
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     * Make a HTTP GET request.
     *
     * @param string $url
     * @param array  $params
     * @param array  $options
     *
     * @return array
     */
    public function get($url, $params = [], $options = [])
    {
        return $this->request($url, self::GET, $params, $options);
    }

    /**
     * Make a HTTP POST request.
     *
     * @param string $url
     * @param array  $params
     * @param array  $options
     *
     * @return array
     */
    public function post($url, $params = [], $options = [])
    {
        return $this->request($url, self::POST, $params, $options);
    }

    /**
     * Make a HTTP POST request.
     *
     * @param string $url
     * @param array  $params
     * @param array  $options
     *
     * @return array
     */
    public function json($url, $params = [], $options = [])
    {
        $options['json'] = true;

        return $this->request($url, self::POST, $params, $options);
    }

    /**
     * Make a HTTP PUT request.
     *
     * @param string $url
     * @param array  $params
     * @param array  $options
     *
     * @return array
     */
    public function put($url, $params = [], $options = [])
    {
        return $this->request($url, self::PUT, $params, $options);
    }

    /**
     * Make a HTTP PATCH request.
     *
     * @param string $url
     * @param array  $params
     * @param array  $options
     *
     * @return array
     */
    public function patch($url, $params = [], $options = [])
    {
        return $this->request($url, self::PATCH, $params, $options);
    }

    /**
     * Make a HTTP DELETE request.
     *
     * @param string $url
     * @param array  $params
     * @param array  $options
     *
     * @return array
     */
    public function delete($url, $params = [], $options = [])
    {
        return $this->request($url, self::DELETE, $params, $options);
    }

    /**
     * Make a HTTP request.
     *
     * @param string $url
     * @param string $method
     * @param array  $params
     * @param array  $options
     *
     * @return string
     */
    protected function request($url, $method = self::GET, $params = [], $options = [])
    {
        if ($method === self::GET || $method === self::DELETE) {
            $url .= (stripos($url, '?') ? '&' : '?').http_build_query($params);
            $params = [];
        }

        curl_setopt($this->curl, CURLOPT_HEADER, 1);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->curl, CURLOPT_URL, $url);

        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);

        // Check for files
        if (isset($options['files']) && count($options['files'])) {
            $this->performFiles($options);
        } else {
            if (isset($options['json'])) {
                $params = json_encode($params, JSON_UNESCAPED_UNICODE);
                $options['headers'][] = 'content-type:application/json';
            }

            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
        }

        // Check for custom headers
        if (!empty($options['headers'])) {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $options['headers']);
        }

        // Check for basic auth
        if (isset($options['auth']['type']) && 'basic' === $options['auth']['type']) {
            curl_setopt($this->curl, CURLOPT_USERPWD, $options['auth']['username'].':'.$options['auth']['password']);
        }

        return $this->perform();
    }

    /**
     * Handle files.
     *
     * @param array $options
     */
    public function performFiles($options)
    {
        foreach ($options['files'] as $index => $file) {
            $params[$index] = $this->createCurlFile($file);
        }

        version_compare(PHP_VERSION, '5.5', '<') || curl_setopt($this->curl, CURLOPT_SAFE_UPLOAD, false);

        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
    }

    /**
     * make cURL file.
     *
     * @param string $filename
     *
     * @return \CURLFile|string
     */
    protected function createCurlFile($filename)
    {
        if (function_exists('curl_file_create')) {
            return curl_file_create($filename);
        }

        return "@$filename;filename=".basename($filename);
    }

    /**
     * Split the HTTP headers.
     *
     * @param string $rawHeaders
     *
     * @return array
     */
    protected function splitHeaders($rawHeaders)
    {
        $headers = [];

        $lines = explode("\n", trim($rawHeaders));
        $headers['HTTP'] = array_shift($lines);

        foreach ($lines as $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                $headers[$h[0]] = trim($h[1]);
            }
        }

        return $headers;
    }

    /**
     * Perform the Curl request.
     *
     * @return array
     */
    protected function perform()
    {
        $response = curl_exec($this->curl);
        $curlInfo = curl_getinfo($this->curl);

        // Separate headers and body
        $headerSize = $curlInfo['header_size'];
        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        $results = [
            'curl_info' => $curlInfo,
            'content_type' => $curlInfo['content_type'],
            'status' => $curlInfo['http_code'],
            'headers' => $this->splitHeaders($header),
            'data' => $body,
        ];

        return $results;
    }
}
