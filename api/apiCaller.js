var apiCallsBenchmark = {
    calls: [],
    stats: {
        totalCalls: 0,
        averageTime: 0,
        successRate: 0,
        lastCall: null
    }
};

async function apiCall(action, data = {}) {
    const api = "https://cobaedlomas.com/cobaed/api/index.php";
    
    const callId = Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    const startTime = performance.now();
    
    try {
        const formData = new FormData();
        const cachebuster = Date.now();
        formData.append('action', action);
        formData.append('params', JSON.stringify(data));
        formData.append("cachebuster", cachebuster);
        const token = localStorage["cobaed_token"] ?? localStorage["cobaed_tTkn"] ?? localStorage["cobaed_aToken"] ?? "sin token";
        formData.append("token", token);

        const response = await fetch(api, {
            method: 'POST',
            body: formData
        });
        
        const endTime = performance.now();
        const duration = endTime - startTime;
        const result = await response.json();
        
        // Registrar llamada exitosa
        recordApiCall({
            id: callId,
            action: action,
            data: data,
            startTime: new Date().toISOString(),
            duration: duration,
            success: true,
            responseSize: JSON.stringify(result).length,
            status: response.status
        });
        
        return result;
    } catch (error) {
        const endTime = performance.now();
        const duration = endTime - startTime;
        
        // Registrar llamada fallida
        recordApiCall({
            id: callId,
            action: action,
            data: data,
            startTime: new Date().toISOString(),
            duration: duration,
            success: false,
            error: error.message,
            status: 0
        });
        
        console.error('API Error:', error);
        throw error;
    }
}

function recordApiCall(callData) {
    // Agregar a la lista de llamadas (mantener solo las últimas 100)
    apiCallsBenchmark.calls.unshift(callData);
    if (apiCallsBenchmark.calls.length > 100) {
        apiCallsBenchmark.calls = apiCallsBenchmark.calls.slice(0, 100);
    }
    
    // Actualizar estadísticas
    updateBenchmarkStats();
}

function updateBenchmarkStats() {
    const calls = apiCallsBenchmark.calls;
    
    if (calls.length === 0) return;
    
    // Estadísticas básicas
    apiCallsBenchmark.stats.totalCalls = calls.length;
    
    // Tiempo promedio
    const totalTime = calls.reduce((sum, call) => sum + call.duration, 0);
    apiCallsBenchmark.stats.averageTime = totalTime / calls.length;
    
    // Tasa de éxito
    const successfulCalls = calls.filter(call => call.success).length;
    apiCallsBenchmark.stats.successRate = (successfulCalls / calls.length) * 100;
    
    // Última llamada
    apiCallsBenchmark.stats.lastCall = calls[0];
    
    // Estadísticas por acción
    const actions = {};
    calls.forEach(call => {
        if (!actions[call.action]) {
            actions[call.action] = {
                count: 0,
                totalTime: 0,
                successCount: 0,
                averageTime: 0,
                successRate: 0
            };
        }
        
        actions[call.action].count++;
        actions[call.action].totalTime += call.duration;
        if (call.success) actions[call.action].successCount++;
    });
    
    // Calcular promedios por acción
    Object.keys(actions).forEach(action => {
        const stats = actions[action];
        stats.averageTime = stats.totalTime / stats.count;
        stats.successRate = (stats.successCount / stats.count) * 100;
    });
    
    apiCallsBenchmark.stats.byAction = actions;
}

// Función para obtener resumen del benchmark
function getBenchmarkSummary() {
    return {
        totalCalls: apiCallsBenchmark.stats.totalCalls,
        averageResponseTime: apiCallsBenchmark.stats.averageTime.toFixed(2) + 'ms',
        successRate: apiCallsBenchmark.stats.successRate.toFixed(1) + '%',
        lastCall: apiCallsBenchmark.stats.lastCall
    };
}

// Función para limpiar el benchmark
function clearBenchmark() {
    apiCallsBenchmark.calls = [];
    apiCallsBenchmark.stats = {
        totalCalls: 0,
        averageTime: 0,
        successRate: 0,
        lastCall: null,
        byAction: {}
    };
}

// Ejemplo de uso: mostrar benchmark en consola
function logBenchmark() {
    console.log('📊 API Calls Benchmark:', getBenchmarkSummary());
    console.log('Detailed stats:', apiCallsBenchmark.stats.byAction);
}

// Auto-log cada 30 llamadas (opcional)
let callCounter = 0;
const originalApiCall = apiCall;
apiCall = async function(action, data) {
    const result = await originalApiCall(action, data);
    callCounter++;
    
    if (callCounter % 30 === 0) {
        logBenchmark();
    }
    
    return result;
};
