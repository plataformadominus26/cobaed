<?php
/*
hudochenkov/sshpass/sshpass;     
eval "$(/opt/homebrew/bin/brew shellenv)"
./ok.sh
*/


    error_reporting(E_ALL); 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-API-Key");
    header("Access-Control-Max-Age: 86400");
    include_once("conexion.php");
    date_default_timezone_set('America/Mexico_City');
    
    /* autenticación por API key - DESACTIVADA TEMPORALMENTE
    if(!isset($_REQUEST["api_key"]) ) {
        echo json_encode(['ok' => false, 'error' => 'API key inválida']);
        exit();
    }
    $sql="select * from usuarios where api_key='".$db->real_escape_string($_REQUEST["api_key"])."'";
    if(!$row=$db->query($sql)){
        echo json_encode(['ok' => false, 'error' => 'API key inválida']);
        exit();
    }

    */


$MODULES = [

    // ─────────────────────────────
    //  AGENDA / CALENDARIO
    // ─────────────────────────────
    'api_ingesta' => [
        'procesar_ingesta',        // Procesa texto sucio con IA y devuelve HTML limpio
        'guardar_conocimiento',        // Guarda conocimiento procesado en BD y sincroniza con Qdrant
        'sugerir_ingesta'
         
    ] 
    // -─────────────────────────────
    // PERSONAS / USUARIOS
    // ─────────────────────────────
    ,'api_usuarios' => [
        'login', // pendiente
        'getProfile',
        'cambiarPassword', // pendiente
        'registrarUsuario', // pendiente
        'recuperarPassword', // pendiente
        'validarToken', // pendiente
        'actualizarPerfil' // pendiente
    ]
    // -─────────────────────────────
    // GENERALES
    // ─────────────────────────────
    ,'api_general' => [
        'getMascot',
        
    ]
    // -─────────────────────────────
    // BLOG / POSTS
    // ─────────────────────────────
    ,'api_blog' => [
        'fetchPosts',
    ]   
    // -─────────────────────────────

];

   // include_once("rbh.php");
    header('Content-Type: application/json; charset=utf-8');
    // Convertir errores PHP a excepciones para que el manejador de excepciones los procese
    set_error_handler(function($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            return false; // respetar nivel de error actual
        }
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

  
    // Configurar manejo de errores
    function apiExceptionHandler($e) {
        error_log("API Exception: " . $e->getMessage());
        echo json_encode([
            'ok' => false,
            'error' => 'Error interno del servidor',
            'debug' => true ,// Cambia a false en producción
            'message' => $e->getMessage()

        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    set_exception_handler('apiExceptionHandler');
    
    $action = $_REQUEST['action'] ?? null;
    $params = parseParams($_REQUEST['params'] ?? $_REQUEST);

    if (!$action) {
         
        sendError('Parámetro "action" es requerido '.json_encode($_REQUEST), []);
    }

    // Debug logging
    file_put_contents(__DIR__ . "/api_debug.txt", 
        "Action: $action\nParams: " . json_encode($params) . "\nModules: " . json_encode($MODULES) . "\n\n", 
        LOCK_EX
    );

    loadModuleForAction($action, $MODULES); 
 
    if (!function_exists($action)) {
        $logFile = __DIR__ . '/api_action.txt';
        $defined = get_defined_functions();
        $userFuncs = $defined['user'] ?? [];
        $entry = [
            'timestamp' => date('c'),
            'action' => $action,
            'user_functions' => $userFuncs,
            'loaded_modules' => array_keys($MODULES)
        ];
        file_put_contents($logFile, json_encode($entry, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);

        sendError("Acción '$action' no disponible", [
            'available_actions' => array_merge(...array_values($MODULES)),
            'user_functions' => $userFuncs
        ]);
    }

    try {
        $result = $action($params);
        sendSuccess($result, ['action' => $action]);
        
    } catch (Exception $e) {
        sendError($e->getMessage(), ['trace' => $e->getTraceAsString()]);
    }

    // ===== FUNCIONES AUXILIARES =====

    function parseParams($params) {
        if (is_string($params)) {
            $decoded = json_decode($params, true);
            return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $params;
        }
        return $params;
    }

    function loadModuleForAction($action, $modules) {
        foreach ($modules as $moduleFile => $actions) {
            if (in_array($action, $actions)) {
                $file = __DIR__ . "/$moduleFile.php";
                
                
                // Debug logging
                file_put_contents(__DIR__ . "/api_load.txt", 
                    "Checking: $file for action: $action\nExists: " . (file_exists($file) ? 'YES' : 'NO') . "\n\n", 
                    FILE_APPEND | LOCK_EX
                );
                
                if (file_exists($file)) {
                    include_once $file;
                      
                    
                    // Verificar si la función existe después de incluir
                   /* if (function_exists($action)) {
                        file_put_contents(__DIR__ . "/api_success.txt", 
                            "SUCCESS: Loaded $file - Function $action now exists\n", 
                            FILE_APPEND | LOCK_EX
                        );
                    }*/
                    
                    return true;
                }
            }
        }
        return false;
    }

    function sendSuccess($data, $debug = []) {
        $response = ['ok' => true, 'data' => $data];
        $response['debug'] = $debug; // Temporal para debugging
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
/*        file_put_contents(__DIR__ . "/api.txt", 
            date('Y-m-d H:i:s') . " - Response: " . json_encode($response, JSON_UNESCAPED_UNICODE) . "\n", 
            FILE_APPEND | LOCK_EX
        );*/
        exit;
    }

    function sendError($message, $debug = []) {
        $response = ['ok' => false, 'error' => $message];
        $response['debug'] = $debug; // Temporal para debugging
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ===== FUNCIONES GLOBALES (que no están en módulos) =====

         
    
      function conexiones($db){

        switch($db){
            case 'sebised': 
                $localhost = "localhost";
                require_once __DIR__ . '/../config.php';
                $pwd= cfg('db_sebised_pwd', '');
                $user= "uobra24_dbaksm";
                $database= "uobra24_obranet";
                break;
        }
         $db = new mysqli(
            $localhost, 
            $user, 
            $pwd, 
            $database
        );
        // Check connection with better error handling
        if ($db->connect_errno) {
            throw new Exception(
                "Database connection failed: (" . $db->connect_errno . ") " . $db->connect_error
            );
        }     
        // Set character set to UTF-8 for proper encoding
        $db->set_charset("utf8mb4");
        return $db;
    }
    

    if(isset($_REQUEST["fcmToken"])){
        global $db;
        $p=$_REQUEST;
        $token=$p["token"];
        $usuario_id=$p["usuario_id"];
        $dispositivo=$p["dispositivo"];
        $sql="update usuarios set fcm_token='".$db->real_escape_string($token)."' ";
        $sql.=", fcm_dispositivo='".$db->real_escape_string($dispositivo)."' ";
        $sql.=" where token='".$db->real_escape_string($usuario_id)."'"; 
        dump($sql,"fcm.txt","a");  
        $res=$db->query($sql);
        return $sql;
        }
    
    ?>