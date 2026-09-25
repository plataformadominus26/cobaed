# Push Service Usage

Servicio reusable agregado: assets/services/push_service.php

## Funciones disponibles
- cobaedPushCreateMessageId()
- cobaedPushEnqueueMessageRtdb($rtdbUrl, $serviceAccountPath, $bookId, $alumnoToken, $msgId, $titulo, $mensaje, $fromToken)
- cobaedPushReadConsumerKey($alumniDir)
- cobaedPushTriggerConsumerAsync($consumerUrl, $consumerKey, $limit)

## Uso minimo desde cualquier PHP
1. include_once __DIR__ . '/assets/services/push_service.php';
2. $msgId = cobaedPushCreateMessageId();
3. Encolar por alumno:
   - cobaedPushEnqueueMessageRtdb(...)
4. Disparar consumer:
   - $key = cobaedPushReadConsumerKey(__DIR__ . '/alumni');
   - $trigger = cobaedPushTriggerConsumerAsync('https://cobaedlomas.com/cobaed/alumni/consume_cred_messages.php', $key, 50);

## Integracion actual
- this.php en accion sendBatchPush ya usa el servicio.
- sendBatchPush ahora dispara consumer automaticamente (auto_consume=1 por default).
- Se puede desactivar por request:
  - auto_consume=0

## Respuesta JSON nueva en sendBatchPush
- consumer_triggered: true/false
- consumer: objeto con estado del trigger

## Notas
- El trigger del consumer es asincrono corto (timeout bajo), para no bloquear UI.
- Si el servidor esta lento, el mensaje ya queda en cola y se consumira en llamada posterior.
