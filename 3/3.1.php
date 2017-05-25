<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \PhpAmqpLib\Connection\AMQPStreamConnection;

$conn = new AMQPStreamConnection('localhost',5672,'guest','guest');
$channel = $conn->channel();

$channel->exchange_declare('logs-exchange','topic',false,true,false);

$channel->queue_declare('msg-inbox-errors',false,true,false,false);

$channel->queue_declare('msg-inbox-logs',false,true,false,false);

$channel->queue_declare('all-logs',false,true,false,false);

$channel->queue_bind('msg-inbox-errors','logs-exchange','error.msg-inbox');

$channel->queue_bind('msg-inbox-logs','logs-exchange','*.msg-inbox');


