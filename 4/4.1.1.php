<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \PhpAmqpLib\Connection\AMQPStreamConnection;
use \PhpAmqpLib\Message\AMQPMessage;

$conn    = new AMQPStreamConnection('localhost', 5672, 'alert_user', 'alertme');
$channel = $conn->channel();

$message  = isset($argv[1]) ? $argv[1] : 'nope';
$routeKey = isset($argv[2]) ? $argv[2] : 'critical.mywebapp';

$msg = new AMQPMessage($message, ['content_type' => 'application/json']);
$channel->basic_publish($msg, 'alerts', $routeKey);

echo "Sent message ",$message," tagged with routing key ",$routeKey," to exchange '/'.*\r\n";