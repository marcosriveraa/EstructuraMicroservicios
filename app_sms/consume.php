<?php
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->queue_declare('cola_sms', true, false, false, false);

$mensajes = [];

while ($msg = $channel->basic_get('cola_sms')) {
    $data = json_decode($msg->body, true);
    $mensajes = $data;
    $msg->ack();
}

$channel->close();
$connection->close();

echo json_encode($mensajes);