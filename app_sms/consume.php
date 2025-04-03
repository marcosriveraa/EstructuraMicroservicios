<?php
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->queue_declare('cola_sms', true, false, false, false);

$mensajes = "";
while ($msg = $channel->basic_get('cola_sms')) {
    $data = json_decode($msg->body, true);
    $mensajes .= "📩 <strong>ID:</strong> {$data['id']} <br>";
    $mensajes .= "✉️ <strong>Mensaje:</strong> {$data['mensaje']} <br>";
    $mensajes .= "📞 <strong>Teléfono:</strong> {$data['telefono']} <br>";
    $mensajes .= "⏳ <strong>Fecha:</strong> {$data['fecha']} <br><hr>";
    $msg->ack();
}

$channel->close();
$connection->close();

echo $mensajes ?: "<p>No hay mensajes en la cola.</p>";
