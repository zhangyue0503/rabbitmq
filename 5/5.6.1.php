<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \PhpAmqpLib\Connection\AMQPStreamConnection;
use \PhpAmqpLib\Message\AMQPMessage;

$conn    = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $conn->channel();


$msg = new AMQPMessage("aaa",['content_type'=>'application/json']);
$channel->basic_publish($msg, 'hello-exchange');
