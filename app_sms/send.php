<?php
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid; 
date_default_timezone_set('Europe/Madrid');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mensaje = $_POST['mensaje'] ?? 'Mensaje Vacío';
    $telefono = $_POST['telefono'] ?? 'Teléfono Vacío';
    $timestamp = date('Y-m-d H:i:s');
    $mensaje_id = Uuid::uuid4()->toString();

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

    // Declarar la cola de SMS como durable para que persista
    $channel->queue_declare('cola_sms', false, true, false, false);

    // Declarar la cola de logs como durable
    $channel->queue_declare('logs', false, true, false, false);

    // Enviar mensaje a la cola de SMS (sin persistencia)
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

    // Enviar mensaje a la cola de logs (sin persistencia)
    $msglog = new AMQPMessage($json_data_log);
    $channel->basic_publish($msglog, '', 'logs');

    // Cerrar conexión
    $channel->close();
    $connection->close();

    echo 'Mensaje enviado: ' . htmlspecialchars($json_data); 
} else {
    echo 'Método no permitido';
}
