# Push Context Checkpoint

Fecha base: 2026-03-16

## Entorno
- Workspace en vivo por SimpleFTP (sin SSH en servidor CentOS7).
- Cambios en este workspace se suben inmediato.
- Wrapper Capacitor separado en ruta local (cobaed-id), pero el codigo web real de app vive en ../--id.

## Rutas clave
- App web en vivo: ../--id/assets/js/main.js
- Backend push en vivo: alumni/push_device.php
- API app (externa invitada): ../--id/assets/api/api.php
- Conexion app (externa invitada): ../--id/assets/api/conexion.php
- Flusher/consumer backend: alumni/consume_cred_messages.php
- Log operativo push: https://cobaedlomas.com/cobaed/alumni/push_register_log.txt

## Estado funcional actual
- Backend de cola y flush: funcionando.
- Insercion en RTDB: funcionando.
- App: estable (sin crash) con guard activo para evitar register nativo.
- Suscripcion push nativa: desactivada temporalmente para proteger produccion.

## Problema raiz confirmado
- Con permisos granted, al ejecutar PushNotifications.register() la app se cerraba.
- Evidencia en log:
  - stage=register_called aparece.
  - No aparece register_resolved ni register_rejected.
  - Cierre inmediato de app.
- Conclusión: crash nativo del wrapper al registrar push (no backend).

## Cambios aplicados en main.js
1) Diferido de request de permisos en runtime para evitar crash durante login.
2) Flujo con boton manual de permiso por gesto de usuario.
3) Bypass de lock global controlado para reintento manual.
4) Guard de produccion para saltar register nativo.

Variable clave actual:
- ID_PUSH_REGISTER_NATIVE_GUARD_DEFAULT = true

Comportamiento actual del guard:
- Si guard true y localStorage id_push_allow_native_register != 1:
  - No llama PushNotifications.register().
  - Log stage: register_skipped_native_crash_guard.

## Lectura rapida de logs (stages)
- check_permissions: estado del permiso.
- permissions_result: estado final evaluado por app.
- request_permissions_deferred: prompt detectado, se difiere solicitud en runtime.
- request_permissions_button_click/result: intento manual por gesto.
- register_scheduled: planificado para registrar.
- register_called: se invoco register nativo.
- register_skipped_native_crash_guard: register bloqueado por seguridad.
- registration_ok: token FCM recibido en callback.
- register_backend_ok: token guardado en backend.

## Situacion actual de negocio
- Produccion estable sin cierres.
- Sin dispositivos registrados mientras guard siga activo.
- En flush puede salir reason=no_registered_devices.

## Milestone 2026-03-17 (madrugada)
- Wrapper Android corregido (package Firebase alineado a la app ID).
- Registro push confirmado en logs:
  - register_called
  - register_resolved
  - registration_ok
  - register_backend_ok
- Flush confirmado con entrega:
  - sent=1
  - failed=0
- En primer plano se observó stage push_received.

## Ajustes recientes para UX de notificaciones
- main.js:
  - Listener pushNotificationReceived con:
    - navigator.vibrate([120,80,120])
    - ding local (WebAudio) en foreground
  - Creación de canal Android `default` (importance alta, vibration y sound default).
- consume_cred_messages.php:
  - Payload FCM v1 Android con:
    - priority HIGH
    - notification.channel_id = default
    - sound/default_sound
    - default_vibrate_timings
    - notification_priority PRIORITY_HIGH

## Siguiente fase (post release wrapper)
- Corregir wrapper nativo (Android/iOS).
- Publicar release.
- Rehabilitar register en app web (ver documento bookmark).
- Verificar secuencia final:
  - register_called
  - registration_ok
  - register_backend_ok
  - flush sent > 0

## Cierre del dia 2026-03-16 (app publica)
- Estado general: OK para produccion en app publica (flujo Android validado de punta a punta).
- Validado hoy:
  - Sin crash en registro push.
  - Registro de dispositivo en backend.
  - Envio por consumer con entrega (sent > 0).
  - Recepcion en foreground con feedback visible/sonoro.

## Pendiente para manana 2026-03-17
- Ejecutar proceso iOS completo (permiso, register, token, backend, recepcion).
- Generar y publicar nuevo release con validacion final de push.

## Checkpoint operativo (cierre de sesion)
- Context ID canonico: CTX-COBAED-ID-IOS-RELEASE
- Alias corto: cobaedid
- Estado de cierre: produccion publica estable en Android y lista para fase iOS/release.
- Pendientes activos para manana:
  - P1: Validacion end-to-end iOS (permiso -> token -> backend -> recepcion).
  - P1: Nuevo release posterior a validacion iOS.
