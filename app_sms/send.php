<?php
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mensaje = $_POST['mensaje'] ?? 'Mensaje Vacío';
    $telefono = $_POST['telefono'] ?? 'Teléfono Vacío';
    $timestamp = date('Y-m-d H:i:s');
    $mensaje_id = uniqid('msg_', true);

    // Datos del mensaje principal
    $data = [
        'id' => $mensaje_id,
        'mensaje' => $mensaje,
        'telefono' => $telefono,
        'fecha' => $timestamp 
    ];

    $json_data = json_encode($data);

    // Conectar con RabbitMQ
    $connection = new AMQPStreamConnection('rabbitmq', 5672, 'admin', 'admin');
    $channel = $connection->channel();

    // Declarar colas (se pueden unificar si ya están creadas en otro script)
    $channel->queue_declare('cola_sms', true, false, false, false);
    $channel->queue_declare('logs', true, false, false, false);

    // Enviar mensaje a la cola de SMS
    $msg = new AMQPMessage($json_data);
    $channel->basic_publish($msg, '', 'cola_sms');
    $cuerpo = 'Petición enviada por un sender';
    // Datos del log
    $log_data = [
        'id' => $mensaje_id,
        'mensaje' => $mensaje,
        'telefono' => $telefono,
        'fecha' => $timestamp,
        'cuerpo' => $cuerpo
    ];

    $json_data_log = json_encode($log_data);

    // Enviar mensaje a la cola de logs
    $msglog = new AMQPMessage($json_data_log);
    $channel->basic_publish($msglog, '', 'logs');

    // Cerrar conexión
    $channel->close();
    $connection->close();

    echo 'Mensaje enviado: ' . htmlspecialchars($json_data); // 
} else {
    echo 'Método no permitido';
}
