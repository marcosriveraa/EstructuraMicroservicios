<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

date_default_timezone_set('Europe/Madrid');

$connection = new AMQPStreamConnection('rabbitmq', 5672, 'admin', 'admin');
$channel = $connection->channel();

$channel->queue_declare('whatsapp', false, true, false, false);
$channel->queue_declare('logs', false, true, false, false);

$timestamp = date('Y-m-d H:i:s');
$cuerpo = "";
$mensaje_id = null;
$numero = null;

// Obtener un solo mensaje de la cola (sin esperar indefinidamente)
$msg = $channel->basic_get('whatsapp', false); // false = no auto-ack

if ($msg) {
    $datos = json_decode($msg->body, true);

    if ($datos && isset($datos['numero'], $datos['cuerpo'], $datos['id'])) {
        echo "<h2>Mensaje recibido:</h2>";
        echo "<p><strong>Numero:</strong> " . htmlspecialchars($datos['numero']) . "</p>";
        echo "<p><strong>Mensaje:</strong><br>" . nl2br(htmlspecialchars($datos['cuerpo'])) . "</p>";

        $mensaje_id = $datos['id'];
        $numero = $datos['numero'];
        $cuerpo = "whatsapp recibido";

        $log_data = [
            'id' => $mensaje_id ?? 'N/A',
            'numero' => $numero ?? 'N/A',
            'cuerpo' => $cuerpo,
            'fecha' => $timestamp
        ];
        
        $json_data_log = json_encode($log_data);
        $msglog = new AMQPMessage($json_data_log);
        $channel->basic_publish($msglog, '', 'logs');

        // Confirmar (ack) el mensaje
        $channel->basic_ack($msg->delivery_info['delivery_tag']);
    } else {
        echo "<p>Error al decodificar el mensaje.</p>";
        $channel->basic_nack($msg->delivery_info['delivery_tag'], false, false); // lo descartamos
        $cuerpo = "Error al decodificar el mensaje.";
    }
} else {
    echo "<p>No hay mensajes en la cola.</p>";
    $cuerpo = "Cola vacía";
}



$channel->close();
$connection->close();
?>
<br>
<a href="recibir.php"><button>Procesar otro mensaje</button></a>

