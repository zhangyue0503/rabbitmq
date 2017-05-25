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

//声明交换器
$ex = new AMQPExchange($channel);
$ex->setName('hello-exchange');
$ex->setType(AMQP_EX_TYPE_DIRECT);
$ex->setFlags(AMQP_DURABLE);
$ex->bind('hello-exchange','hola');
$ex->declareExchange();

$msg = $argv[1];

$ex->publish($msg,'hola',AMQP_MANDATORY,['content-type'=>'text/plain']);


