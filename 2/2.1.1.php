<?php
/**
 * 使用的amqp的php扩展包
 * http://pecl.php.net/package/amqp
 */

$conn_args = array(
    'host' => 'localhost',
    'port' => '5672',
    'login' => 'guest',
    'password' => 'guest',
);

//建立连接
$connection = new AMQPConnection($conn_args);
$connection->connect();
//获取信道
$channel = new AMQPChannel($connection);

//创建队列
$q = new AMQPQueue($channel);
$q->setName('hello-exchange-queue');
$q->setFlags(AMQP_DURABLE); //持久化
$q->declareQueue();
$q->bind('hello-exchange','hola');

function processMessage($envelope, $queue) {
    $msg = $envelope->getBody();
    echo $msg."\n"; //处理消息
    echo $envelope->getDeliveryTag()."\n";
    $queue->ack($envelope->getDeliveryTag()); //手动发送ACK应答
}

while(True){
    $q->consume('processMessage');
}

$connection->disconnect();