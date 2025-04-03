# Proyecto de Microservicios utilizando RabbitMQ

Este proyecto implementa una arquitectura de microservicios donde múltiples aplicaciones **sender** envían peticiones a una 
cola de mensajes en **RabbitMQ**, y múltiples aplicaciones **consumer** leen y procesan estas peticiones.

El objetivo de este sistema es permitir la transmisión asíncrona de mensajes entre servicios utilizando **RabbitMQ** 
como un intermediario para asegurar la correcta entrega y procesamiento de las peticiones.

## Componentes del Sistema
- **Sender**: Aplicación que genera y envía mensajes a la cola de **RabbitMQ**.
- **Consumer**: Aplicacón que consume los mensajes de la cola de **RabbitMQ** y realiza las tareas necesarias.
- **RabbbitMQ**: Servidor de mensajería que actúa como intermediario entre los **Senders** y los **Consumers**.

## Flujo de trabajo
1. **Sender** recibe las peticiones.
2. **Sender** envia las peticiones a **RabbitMQ**.
3. **Consumer** consume los mensajes de la cola y procesa las peticiones.
4. **Resultado** el consumidor puede almacenar los resultado, enviar notificaciones, o realizar otras acciones según el caso.
