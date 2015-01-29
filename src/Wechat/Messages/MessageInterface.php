<?php Overtrue\Wechat\Messages;

interface MessageInterface
{
    public function formatToClient();
    public function formatToServer();
}