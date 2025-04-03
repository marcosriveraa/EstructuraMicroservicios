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

## Arquitectura

La arquitectura del sistema sigue un patrón **Producer-Consumer** utilizando **RabbitMQ** como middleware. Los componentes principales son:

- **Senders**: Servicios que envían mensajes a RabbitMQ. Estos servicios suelen recibir datos a través de un formulario web o API y, a continuación, formatean el mensaje en formato JSON y lo envían a una cola de RabbitMQ.
  
- **RabbitMQ**: Gestiona las colas de mensajes donde los mensajes enviados por los **senders** se almacenan temporalmente antes de ser consumidos por los **consumers**.

- **Consumers**: Servicios que consumen los mensajes de la cola y los procesan. Pueden realizar tareas como almacenar datos en una base de datos, enviar correos electrónicos, procesar pagos, entre otros.

## Diagrama de la arquitectura

![Diagrama de Arquitectura](images/arquitectura-microservicios.png)

1. El **sender** publica el mensaje en la cola de RabbitMQ.
2. El **consumer** lee el mensaje desde la cola y lo procesa.

### Comunicación entre microservicios

- Los **senders** y **consumers** se comunican a través de **RabbitMQ** mediante el uso de colas y exchanges.
- Los mensajes son enviados en formato **JSON** para asegurar la interoperabilidad.
- Los **senders** publican mensajes en una asignada a cada proceso, si esta dedicado al envio de sms, el sender publicará el mensaje en una cola llamada cola_sms
- Los **consumers** se suscriben a esta cola y procesan los mensajes en orden.

## Configuración de RabbitMQ

- **RabbitMQ** está configurado para ser tolerante a fallos con colas durables.
- Las colas están configuradas con "acknowledgement" para garantizar que los mensajes solo se marcan como procesados una vez que se han completado.

