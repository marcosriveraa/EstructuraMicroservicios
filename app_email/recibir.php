<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->queue_declare('correos', false, true, false, false);
$channel->queue_declare('logs', false, true, false, false);

$timestamp = date('Y-m-d H:i:s');

$cuerpo = "";

// Obtener un solo mensaje de la cola (sin esperar indefinidamente)
$msg = $channel->basic_get('correos', false); // false = no auto-ack

if ($msg) {
    $datos = json_decode($msg->body, true);

    if ($datos && isset($datos['destinatario'], $datos['asunto'], $datos['cuerpo'])) {
        echo "<h2>Mensaje recibido:</h2>";
        echo "<p><strong>Destinatario:</strong> " . htmlspecialchars($datos['destinatario']) . "</p>";
        echo "<p><strong>Asunto:</strong> " . htmlspecialchars($datos['asunto']) . "</p>";
        echo "<p><strong>Cuerpo:</strong><br>" . nl2br(htmlspecialchars($datos['cuerpo'])) . "</p>";

        $destinatario = $datos['destinatario'];
        $asunto = $datos['asunto'];
        $cuerpo_correo = $datos['cuerpo'];

        $cuerpo = "correo recibido";

        $mensaje_id = $datos['id'];

        $log_data = [
            'id' => $mensaje_id,
            'destinatario' => $destinatario ?? 'N/A',
            'asunto' => $asunto ?? 'N/A',
            'cuerpo_correo' => $cuerpo_correo ?? 'N/A',
            'cuerpo' => $cuerpo,
            'fecha_consumo' => $timestamp
        ];
    
        $json_data_log = json_encode($log_data);
    
        $msglog = new AMQPMessage($json_data_log);
        $channel->basic_publish($msglog, '', 'logs');

        

        // Acknowledge del mensaje para sacarlo de la cola
        $channel->basic_ack($msg->delivery_info['delivery_tag']);
    } else {
        echo "<p>Error al decodificar el mensaje.</p>";
        $channel->basic_nack($msg->delivery_info['delivery_tag'], false, false); // lo descartamos
        $cuerpo = "Error al decodificar el mensaje.";
    }
} else {
    echo "<p>No hay mensajes en la cola.</p>";
}

$channel->close();
$connection->close();
?>
<br>
<a href="recibir.php"><button>Procesar otro mensaje</button></a>
