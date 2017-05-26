<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \PhpAmqpLib\Connection\AMQPStreamConnection;

$conn    = new AMQPStreamConnection('localhost', 5672, 'alert_user', 'alertme');
$channel = $conn->channel();

$channel->exchange_declare('alerts', 'topic', false, false, false);

//critical队列和critical.* topic绑定
$channel->queue_declare('critical', false, false, false, false);
$channel->queue_bind('critical','alerts','critical.*');

//rate_limit队列和*.rate_limit topic绑定
$channel->queue_declare('rate_limit', false, false, false, false);
$channel->queue_bind('rate_limit','alerts','*.rate_limit');

$critical_notify = function($msg){
    $message = $msg->body;
    echo "Sent alert via e-mail! Alert Text1: ",$message," Recipients: zyblog@qq.com\r\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

$rate_limit_notify = function($msg){
    $message = $msg->body;
    echo "Sent alert via e-mail! Alert Text2: ",$message," Recipients: zyblog@qq.com\r\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

//将报警附加到处理器上
$channel->basic_consume('critical','critical', false, false, false, false, $critical_notify);
$channel->basic_consume('rate_limit','rate_limit', false, false, false, false, $rate_limit_notify);



while(count($channel->callbacks)) {
    $channel->wait();
}



