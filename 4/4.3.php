<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \PhpAmqpLib\Connection\AMQPStreamConnection;
use \PhpAmqpLib\Message\AMQPMessage;

$conn    = new AMQPStreamConnection('localhost', 5672, 'alert_user', 'alertme');
$channel = $conn->channel();

$channel->exchange_declare('rpc', 'direct', false, false, false);
$channel->queue_declare('ping', false, false, false, false);
$channel->queue_bind('ping', 'rpc', 'ping');

$api_ping = function ($msg) use ($channel) {
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    echo "Received API call...replying..." . $msg->get('reply_to');
    $reply = new AMQPMessage("Pong" . $msg->body);
    sleep(5);
    $channel->basic_publish($reply, '', $msg->get('reply_to'));

};
$channel->basic_consume('ping', 'ping', false, false, false, false, $api_ping);

while (count($channel->callbacks)) {
    $channel->wait();
}



