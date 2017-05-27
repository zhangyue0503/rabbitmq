<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \PhpAmqpLib\Connection\AMQPStreamConnection;

$conn    = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $conn->channel();

$channel->exchange_declare('hello-exchange1', 'direct',false,false,false);
$channel->queue_declare('halo1',false,false,false,false,false,[["S","xa-ha-policy","S","all"]]);
$channel->queue_bind('halo1','hello-exchange1','',false);


$processMessage = function($msg) {
    echo $msg->body."\n"; //处理消息
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

};

$channel->basic_consume('halo','', false, false, false, false, $processMessage);



while(count($channel->callbacks)) {
    $channel->wait();
}



