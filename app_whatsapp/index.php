<?php
session_start();

// Dependencias
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid; 
date_default_timezone_set('Europe/Madrid');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// datos del formulario

    $numero = $_POST["numero"];
    $cuerpo = $_POST["cuerpo"];
    $timestamp = date('Y-m-d H:i:s');
    $mensaje_id = Uuid::uuid4()->toString();

// Conexion con rabbitmq

    $connection = new AMQPStreamConnection('rabbitmq', 5672, 'admin', 'admin');
    $channel = $connection->channel();

    $channel->queue_declare('whatsapp', false, true, false, false);
    $channel->queue_declare('logs', false, true, false, false);

// Procesar datos

    $datos = ['id' => $mensaje_id, 'numero' => $numero,  'cuerpo' => $cuerpo];
    $data = json_encode($datos);

// crear objeto mensaje

    $msg = new AMQPMessage($data);

// publicar mensaje en cola

    $channel->basic_publish($msg, '', 'whatsapp');

// logs

$cuerpo = "Mensaje enviado a la cola de whatsapp";

$log_data = [
    'id' => $mensaje_id,
    'numero' => $numero,
    'cuerpo' => $cuerpo,
    'fecha' => $timestamp,
    'cuerpo' => $cuerpo
];

$json_data_log = json_encode($log_data);

$msglog = new AMQPMessage($json_data_log);
$channel->basic_publish($msglog, '', 'logs');

// Cerrar la conexión

    $channel->close();
    $connection->close();

}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario whatsapp</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        form input, form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
        }

        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }

        form input::placeholder, form textarea::placeholder {
            color: #888;
        }

        form textarea {
            height: 200px; /* Aumento el tamaño del área de texto */
            resize: vertical; /* Permite al usuario cambiar el tamaño verticalmente */
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <form action="" id="form" method="POST">
        <h2>Formulario de Envío de whatsapp</h2>
        <input type="text" name="numero"  placeholder="Numero" required>
        <textarea name="cuerpo" placeholder="Mensaje" required></textarea>
        <input type="submit" value="Enviar">
    </form>
</body>
</html>
