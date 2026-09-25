import { Geolocation } from '@capacitor/geolocation';

// Exponemos la función para que jQuery la pueda usar
window.obtenerUbicacion = async () => {
  try {
    // 1. Verificar/Solicitar permisos
    const check = await Geolocation.checkPermissions();
    if (check.location !== 'granted') {
      const request = await Geolocation.requestPermissions();
      if (request.location !== 'granted') {
        throw new Error('Permiso de ubicación denegado');
      }
    }

    // 2. Obtener coordenadas
    const coordinates = await Geolocation.getCurrentPosition();
    console.log('Posición:', coordinates);
    
    // Devolvemos el objeto para usarlo en tu lógica
    return {
      lat: coordinates.coords.latitude,
      lng: coordinates.coords.longitude
    };

  } catch (err) {
    console.error('Error GPS:', err);
    alert('Error obteniendo ubicación: ' + err.message);
    return null;
  }
};

console.log('Sistema de Geolocalización Nativa listo.');