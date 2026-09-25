# Push Bookmark Prod and Release

## Punto de retorno rapido a produccion estable
Archivo: ../--id/assets/js/main.js

Mantener:
- ID_PUSH_REGISTER_NATIVE_GUARD_DEFAULT = true

Resultado esperado:
- Sin crash por register nativo.
- Stage esperado en log: register_skipped_native_crash_guard.

## Punto de activacion para pruebas controladas
Sin editar codigo, en consola WebView o flujo debug:
- localStorage.setItem('id_push_allow_native_register', '1')

Resultado esperado:
- Se permite ejecutar register() aunque guard este true.

## Punto de activacion definitiva post release
Archivo: ../--id/assets/js/main.js

Cambiar:
- ID_PUSH_REGISTER_NATIVE_GUARD_DEFAULT = false

Opcional limpieza:
- Eliminar/ocultar boton manual de permiso si ya no se requiere.
- Mantener logs pushDiag para observabilidad al menos una version mas.

## Checklist de verificacion post release
1. check_permissions con granted.
2. register_called.
3. registration_ok.
4. register_backend_ok.
5. En push_devices RTDB aparece device del alumno.
6. Flusher: sent > 0 y failed por no_registered_devices = 0 para casos registrados.

## Rollback inmediato si reaparece crash
1. Volver ID_PUSH_REGISTER_NATIVE_GUARD_DEFAULT a true.
2. Limpiar override local si existe:
   - localStorage.removeItem('id_push_allow_native_register')
3. Validar en log stage register_skipped_native_crash_guard.

## Notas de seguridad operativa
- Este proyecto se despliega via SimpleFTP en caliente.
- Evitar cambios de alto riesgo sin guard.
- Confirmar siempre con push_register_log.txt antes y despues de cada ajuste.
