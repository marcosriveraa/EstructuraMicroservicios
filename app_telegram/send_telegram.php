<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid; 
date_default_timezone_set('Europe/Madrid');

if ($SERVER['REQUEST_METHOD'] == 'POST') {
$mensaje = $_POST['mensaje'] ?? 'Mensaje Vacío';
$telefono = $_POST['numerotelef'] ?? 'Teléfono Vacío';
$timestamp = date('Y-m-d H:i:s');
$mensaje_id = Uuid::uuid4()->toString();

$data = [ 
    'id' => $mensaje_id,
    'mensaje' => $mensaje,
    'telefono' => $telefono,
    'fecha' => $timestamp
]

$json_data = json_encode($data);

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'admin', 'admin');
$channel = 

$channel->queue_declare('cola_telegram', false, true, false, false);
$channel->queue_declare('logs', false, true, false, false);

$msg = new AMQPMessage($json_data);
$channel->basic_publish($msg, '', 'cola_telegram');
$cuerpo = 'Petición enviada por un sender (TELEGRAM)';

$log_data = [
    'id' => $mensaje_id,
    'mensaje' => $mensaje,
    'telefono' => $telefono,
    'fecha' => $timestamp,
    'cuerpo' => $cuerpo
];

$json_data_log = json_encode($log_data);
$msglog = new AMQPMessage($json_data_log);
$channel->basic_publish($msglog, '', 'logs');

$channel->close();
$connection->close();

echo 'Mensaje enviado: ' . htmlspecialchars($json_data); 
} else {
    echo 'Método no permitido. Solo se permite POST.';
    http_response_code(405); // Método no permitido
}