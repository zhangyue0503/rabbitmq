<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \PhpAmqpLib\Connection\AMQPStreamConnection;
use \PhpAmqpLib\Message\AMQPMessage;

$conn    = new AMQPStreamConnection('localhost', 5672, 'alert_user', 'alertme');
$channel = $conn->channel();

$channel->exchange_declare('upload-pictures', 'fanout', false, true, false);

$message = json_encode([
    'image_id'   => 1,
    'user_id'    => 2,
    'image_path' => 'aabn'
]);

//上传图片
$msg = new AMQPMessage($message, ['content_type' => 'application/json', 'delivery_mode' => 2]);
$channel->basic_publish($msg, 'upload-pictures');
