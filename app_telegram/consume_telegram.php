<?php
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

date_default_timezone_set('Europe/Madrid');

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->queue_declare('cola_telegram', false, true, false, false);
$channel->queue_declare('logs', false, true, false, false);

$mensajes = "";
$log_data = [];

while ($msg = $channel->basic_get('cola_telegram')) {
    if ($msg) {
        $data = json_decode($msg->body, true);

        // Mostrar los mensajes consumidos
        $mensajes .= "📩 <strong>ID:</strong> {$data['id']} <br>";
        $mensajes .= "✉️ <strong>Mensaje:</strong> {$data['mensaje']} <br>";
        $mensajes .= "📞 <strong>Teléfono:</strong> {$data['telefono']} <br>";
        $mensajes .= "⏳ <strong>Fecha:</strong> {$data['fecha']} <br><hr>";
        
        // Acknowledge el mensaje para confirmar que ha sido procesado
        $msg->ack();
        
        // Registrar el consumo del mensaje en la cola de logs
        $timestamp = date('Y-m-d H:i:s');
        $log_data = [
            'id' => $data['id'],
            'mensaje' => $data['mensaje'],
            'telefono' => $data['telefono'],
            'fecha' => $timestamp,
            'cuerpo' => 'Registro consumido por un consumidor(TELEGRAM)',
            'fecha_consumo' => $timestamp
        ];

        $json_log_data = json_encode($log_data);
        $msg_log = new AMQPMessage($json_log_data);
        $channel->basic_publish($msg_log, '', 'logs');
    }
}
 $channel->close();
$connection->close();

echo $mensajes ?: "<p>No hay mensajes en la cola.</p>";