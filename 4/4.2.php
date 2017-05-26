<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \PhpAmqpLib\Connection\AMQPStreamConnection;

$conn    = new AMQPStreamConnection('localhost', 5672, 'alert_user', 'alertme');
$channel = $conn->channel();

$channel->exchange_declare('upload-pictures', 'fanout', false, true, false);

//add-points
$channel->queue_declare('add-points', false, true, false, false);
$channel->queue_bind('add-points', 'upload-pictures');
$consumer = function ($msg) {
    echo "add-points", $msg->body, "\r\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};
$channel->basic_consume('add-points', 'add-points', false, false, false, false, $consumer);

//resize-picture
$channel->queue_declare('resize-picture', false, true, false, false);
$channel->queue_bind('resize-picture', 'upload-pictures');
$consumer2 = function ($msg) {
    sleep(10);
    echo "resize-picture", $msg->body, "\r\n";
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};
$channel->basic_consume('resize-picture', 'resize-picture', false, false, false, false, $consumer2);


while (count($channel->callbacks)) {
    $channel->wait();
}