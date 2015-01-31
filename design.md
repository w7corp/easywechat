你觉得这样成不：

$options = array(
    'app_id' => 'wx80b3c9f431b8b029',
    'secret' => 'xxxxxxxxxxxxxxxxxxxx',
    'token'  => 'helloworld',
    'encodingAESKey' => 'eEqqLFPwRhf6FkIQpwraE3QDa6U3OpIQ5zYBRi0Zkcl' // optional
);

$wechat = new Wechat($options);


// server
$server = $wechat->server;
$server->message('text', function($message){
    error_log('收到文本消息：' . $message['Content']);
});

$server->run();



// client
$client = $wechat->client;
$client->xxxx

