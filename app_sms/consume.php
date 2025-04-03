<?php
require 'vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

date_default_timezone_set('Europe/Madrid');

// Conectar a RabbitMQ
$connection = new AMQPStreamConnection('rabbitmq', 5672, 'admin', 'admin');
$channel = $connection->channel();

// Declarar la cola de SMS y la cola de logs como durables (persistentes)
$channel->queue_declare('cola_sms', false, true, false, false);
$channel->queue_declare('logs', false, true, false, false);

$mensajes = "";
$log_data = [];

while ($msg = $channel->basic_get('cola_sms')) {
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
            'cuerpo' => 'Registro consumido por un consumidor',
            'fecha_consumo' => $timestamp
        ];

        // Crear el mensaje de log y enviarlo a la cola de logs (sin persistencia)
        $json_log_data = json_encode($log_data);
        $msg_log = new AMQPMessage($json_log_data);
        $channel->basic_publish($msg_log, '', 'logs');
    }
}

// Cerrar la conexión
$channel->close();
$connection->close();

// Mostrar los mensajes consumidos
echo $mensajes ?: "<p>No hay mensajes en la cola.</p>";
?>
