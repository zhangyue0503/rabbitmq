<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \PhpAmqpLib\Connection\AMQPStreamConnection;
use \PhpAmqpLib\Message\AMQPMessage;

$conn    = new AMQPStreamConnection('localhost', 5672, 'alert_user', 'alertme');
$channel = $conn->channel();

$result = $channel->queue_declare('', false, false, true);

//上传图片
$msg = new AMQPMessage("aaa", ['reply_to' => $result[0]]);
$channel->basic_publish($msg, 'rpc', 'ping');

echo "Sent 'ping' RPC call.Waiting for reply...";

$reply_callback = function ($msg) {
    echo "RPC Reply --- ", $msg->body;
};
$channel->basic_consume($result[0], $result[0], false, false, false, false, $reply_callback);

$channel->wait();

