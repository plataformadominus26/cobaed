import { PushNotifications } from '@capacitor/push-notifications';
import { Capacitor } from '@capacitor/core';

const initPush = async () => {
  // Solo ejecutar si estamos en una app nativa (no en web normal)
  if (!Capacitor.isNativePlatform()) return;

  console.log('Inicializando Push Notifications...');

  // 1. Escuchar eventos de registro
  await PushNotifications.addListener('registration', token => {
    console.log('PUSH TOKEN:', token.value);
    alert('Push Token: ' + token.value); // Alerta temporal para verificar que funciona
    // AQUÍ es donde más tarde enviarás este token a tu base de datos PHP vía AJAX
  });

  await PushNotifications.addListener('registrationError', err => {
    console.error('Error registro Push: ', err.error);
  });

  await PushNotifications.addListener('pushNotificationReceived', notification => {
    console.log('Notificación recibida: ', notification);
    alert('Mensaje: ' + notification.title);
  });

  // 2. Solicitar permisos
  let permStatus = await PushNotifications.checkPermissions();

  if (permStatus.receive === 'prompt') {
    permStatus = await PushNotifications.requestPermissions();
  }

  if (permStatus.receive !== 'granted') {
    console.error('Permiso de notificaciones denegado');
    return;
  }

  // 3. Registrar
  await PushNotifications.register();
};

initPush();