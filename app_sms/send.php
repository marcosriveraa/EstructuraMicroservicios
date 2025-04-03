<?php
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mensaje = $_POST['mensaje'] ?? 'Mensaje Vacio';
    $telefono = $_POST['telefono'] ?? 'Telefono Vacio';

    $data = [
        'mensaje' => $mensaje,
        'telefono' => $telefono
    ];

    $json_data = json_encode($data);

    $connection = new AMQPStreamConnection('rabbitmq', 5672, 'admin', 'admin');
    $channel = $connection->channel();

    $channel->queue_declare('cola_sms', false, false, false, false);

    $msg = new AMQPMessage($json_data);

    $channel->basic_publish($msg, '', 'cola_sms');

    $channel->close();
    $connection->close();

    echo 'Mnesaje enviado ' . htmlspecialchars($json_data)
} else {
    echo 'Metodo no permitido'
}