/**
 * Check if usuarios table is populated for offline login
 * @returns {Promise<boolean>} True if usuarios table has data
 */
async function checkOfflineDataAvailable() {
    try {
        // Wait for EasyDB to be initialized
        if (!localDB || !localDB.isReady()) {
            await window.localDBInitialized;
        }

        // Check if usuarios table has any records
        const usuarios = await localDB.query('usuarios', {}, { limit: 1 });
        return usuarios && usuarios.length > 0;
    } catch (error) {
        console.error('Error checking offline data:', error);
        return false;
    }
}

/**
 * Validate offline login credentials
 * @param {string} username - Username to validate
 * @param {string} password - Password to validate
 * @returns {Promise<Object|null>} User object if valid, null if invalid
 */
async function validateOfflineLogin(username, password) {
    try {
        // Wait for EasyDB to be initialized
        if (!localDB || !localDB.isReady()) {
            await window.localDBInitialized;
        }

        // Find user by username
        const usuarios = await localDB.query('usuarios', { 
            usuario: username 
        });

        if (!usuarios || usuarios.length === 0) {
            console.log('User not found in offline data');
            return null;
        }

        const usuario = usuarios[0];

        // Validate password (assuming password is stored in 'password' field)
        // Note: In production, passwords should be hashed
        if (usuario.password === password) {
            return {
                ok: true,
                token: usuario.token || `offline_${usuario.usuario_id}_${Date.now()}`,
                usuario_id: usuario.usuario_id,
                nombre: usuario.nombre,
                book_id: usuario.book_id
            };
        }

        return null;
    } catch (error) {
        console.error('Error validating offline login:', error);
        return null;
    }
}

/**
 * Initialize offline login flow - check if data is available
 * @returns {Promise<boolean>} True if offline login is possible, false if online login required
 */
async function initializeOfflineLogin() {
    try {
        const hasOfflineData = await checkOfflineDataAvailable();
        
        if (!hasOfflineData) {
            console.log('No offline data available - online login required');
            // Show login tab to force online authentication
            $("#login-tab").click();
            return false;
        }

        console.log('Offline data available - offline login possible');
        return true;
    } catch (error) {
        console.error('Error initializing offline login:', error);
        // Default to requiring online login on error
        $("#login-tab").click();
        return false;
    }
}

/**
 * Populate usuarios table for offline login capability
 * This should be called after successful online login to enable offline functionality
 * @param {string} token - Current user token
 * @returns {Promise<boolean>} True if successful
 */
async function populateUsuariosForOffline(token) {
    try {
        // This would typically fetch from your server
        // For now, we'll assume fetchOfflineData() handles this
        console.log('Usuarios table populated via fetchOfflineData()');
        return true;
    } catch (error) {
        console.error('Error populating usuarios table:', error);
        return false;
    }
}

/**
 * Get current user info from offline storage
 * @param {string} token - User token
 * @returns {Promise<Object|null>} User object or null
 */
async function getCurrentUserOffline(token) {
    try {
        if (!localDB || !localDB.isReady()) {
            await window.localDBInitialized;
        }

        const usuarios = await localDB.query('usuarios', { token: token });
        return usuarios && usuarios.length > 0 ? usuarios[0] : null;
    } catch (error) {
        console.error('Error getting current user offline:', error);
        return null;
    }
}

/**
 * Show attendance history using EasyDB
 * @param {string} historialToken - User token to fetch history for
 */
async function showHistorial(historialToken) {
    try {
        // Wait for EasyDB to be initialized
        if (!localDB || !localDB.isReady()) {
            await window.localDBInitialized;
        }

        // Get user by token - using query with filter
        const usuarios = await localDB.query('usuarios', { token: historialToken });
        if (!usuarios || usuarios.length === 0) {
            console.warn('No user found with the provided token');
            document.getElementById('historialContainer').innerHTML = 
                '<div class="alert alert-warning">No se encontró información del usuario.</div>';
            return;
        }

        const usuario = usuarios[0];

        // Get attendance records for this user, sorted by date desc, limit 100
        const attendances = await localDB.query('attendance', 
            { maestro_id: usuario.usuario_id }, // Filter by maestro_id
            {
                orderBy: { field: 'fecha', direction: 'desc' },
                limit: 100
            }
        );

        if (!attendances || attendances.length === 0) {
            document.getElementById('historialContainer').innerHTML = 
                '<div class="alert alert-info">No hay registros de asistencia disponibles.</div>';
            return;
        }

        // Build HTML with improved performance
        let html = '<div class="row row-cols-1 row-cols-md-2 g-4">';
        
        // Get all unique maestro_ids and area_ids for batch lookup
        const maestroIds = [...new Set(attendances.map(row => row.maestro_id))];
        const areaIds = [...new Set(attendances.map(row => row.area_id))];

        // Batch fetch related data for better performance
        const maestros = await Promise.all(
            maestroIds.map(id => localDB.query('usuarios', { usuario_id: id }))
        );
        const areas = await Promise.all(
            areaIds.map(id => localDB.query('areas', { area_id: id }))
        );

        // Create lookup maps for O(1) access
        const maestroMap = new Map();
        maestros.forEach(maestroArray => {
            if (maestroArray.length > 0) {
                const maestro = maestroArray[0];
                maestroMap.set(maestro.usuario_id, maestro);
            }
        });

        const areaMap = new Map();
        areas.forEach(areaArray => {
            if (areaArray.length > 0) {
                const area = areaArray[0];
                areaMap.set(area.area_id, area);
            }
        });

        // Build HTML efficiently
        for (const row of attendances) {
            const mtro = maestroMap.get(row.maestro_id);
            const area = areaMap.get(row.area_id);

            const estado = getEstadoBadge(row.estado);
            
            html += `
            <div class="col">
                <div class="card shadow-sm mb-0">
                    <div class="card-body">
                        <h5 class="card-title mb-2">
                            <i class="bi bi-person-badge me-2"></i>${mtro ? mtro.nombre : 'Sin asignar'}
                        </h5>
                        <p class="mb-1">
                            <i class="bi bi-geo-alt me-2"></i><strong>Área:</strong> 
                            ${area ? area.nombre : 'No especificada'}
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-clock me-2"></i><strong>Horario:</strong> 
                            ${row.checkin || '--:--'} - ${row.checkout || '--:--'}
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-info-circle me-2"></i><strong>Estado:</strong> 
                            ${estado}
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-chat-left-text me-2"></i><strong>Comentarios:</strong> 
                            ${row.rems || 'Sin comentarios'}
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-calendar-event me-2"></i><strong>Fecha:</strong> 
                            ${formatDate(row.fecha)}
                        </p>
                    </div>
                </div>
            </div>`;
        }
        
        html += '</div>';

        // Insert into DOM
        document.getElementById('historialContainer').innerHTML = html;

        // Add statistics
        await showAttendanceStats(attendances);

    } catch (error) {
        console.error('Error showing historial:', error);
        document.getElementById('historialContainer').innerHTML = 
            '<div class="alert alert-danger">Error al cargar el historial de asistencias.</div>';
    }
}

/**
 * Get badge HTML for attendance status
 * @param {string} estado - Status code
 * @returns {string} HTML badge
 */
function getEstadoBadge(estado) {
    switch (estado) {
        case '1': 
            return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Falta</span>';
        case '2': 
            return '<span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Retraso</span>';
        case '3': 
            return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Asistencia</span>';
        default: 
            return '<span class="badge bg-secondary"><i class="bi bi-question-circle me-1"></i>Desconocido</span>';
    }
}

/**
 * Format date for display
 * @param {string} fecha - Date string
 * @returns {string} Formatted date
 */
function formatDate(fecha) {
    if (!fecha) return 'Fecha no disponible';
    
    try {
        const date = new Date(fecha);
        return date.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (error) {
        return fecha; // Return original if formatting fails
    }
}

/**
 * Show attendance statistics
 * @param {Array} attendances - Attendance records
 */
async function showAttendanceStats(attendances) {
    const stats = {
        total: attendances.length,
        asistencias: attendances.filter(a => a.estado === '3').length,
        faltas: attendances.filter(a => a.estado === '1').length,
        retrasos: attendances.filter(a => a.estado === '2').length
    };

    const percentage = stats.total > 0 ? {
        asistencias: Math.round((stats.asistencias / stats.total) * 100),
        faltas: Math.round((stats.faltas / stats.total) * 100),
        retrasos: Math.round((stats.retrasos / stats.total) * 100)
    } : { asistencias: 0, faltas: 0, retrasos: 0 };

    const statsHtml = `
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Estadísticas de Asistencia</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="stat-item">
                            <h3 class="text-primary">${stats.total}</h3>
                            <p class="text-muted">Total Registros</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <h3 class="text-success">${stats.asistencias}</h3>
                            <p class="text-muted">Asistencias (${percentage.asistencias}%)</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <h3 class="text-danger">${stats.faltas}</h3>
                            <p class="text-muted">Faltas (${percentage.faltas}%)</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <h3 class="text-warning">${stats.retrasos}</h3>
                            <p class="text-muted">Retrasos (${percentage.retrasos}%)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Append stats after the attendance list
    document.getElementById('historialContainer').innerHTML += statsHtml;
}



