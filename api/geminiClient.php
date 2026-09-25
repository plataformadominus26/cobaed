<?php
/**
 * Class GeminiClient (Versión 2.0 - Multi-Modelo)
 * Wrapper centralizado para interactuar con la API de Google Gemini.
 */
require_once __DIR__ . '/../config.php';
if (!defined('googleApiKey')) define('googleApiKey', cfg('gemini_api_key', '')); // Llave en config.local.php

class GeminiClient {

    // --- 1. CATÁLOGO DE MODELOS (CONSTANTES) ---
    // Usamos Flash para lo rápido/barato (Chat, UI, Resúmenes simples)
    const MODEL_FAST = "gemini-2.5-flash"; 
    
    // Usamos Pro para "Deepthink" (Matemáticas complejas, Lógica dura, Curaduría)
    const MODEL_SMART = "gemini-1.5-pro"; 

    // Futuro: Imagen (Cuando integremos generación de avatares)
    // const MODEL_CREATIVE = "imagen-3.0-generate"; 

    // Configuración Base
    private static $API_KEY = googleApiKey; 
    private static $API_BASE_URL = "https://generativelanguage.googleapis.com/v1beta/models/";

    /**
     * Función principal para consultar a la IA.
     * * @param mixed  $prompt            El texto o array de partes.
     * @param string $systemInstruction Rol del sistema.
     * @param string $outputFormat      'text' o 'json'.
     * @param float  $temperature       Creatividad (0.0 a 1.0).
     * @param string $model             QUÉ CEREBRO USAR (Usa las constantes de la clase).
     */
    public static function call($prompt, $systemInstruction = "", $outputFormat = 'text', $temperature = 0.4, $model = self::MODEL_FAST) {
        
        // 1. Construcción Dinámica de la URL según el modelo elegido
        $url = self::$API_BASE_URL . $model . ":generateContent?key=" . self::$API_KEY;

        // 2. Payload Estándar
        $payload = [
            "contents" => [
                [ "parts" => [ ["text" => $prompt] ] ]
            ],
            "generationConfig" => [
                "temperature" => $temperature,
                "maxOutputTokens" => 8192, // Aumentado para respuestas largas (Deepthink)
            ]
        ];

        // 3. Inyección de System Instruction (Rol)
        if (!empty($systemInstruction)) {
            $payload["systemInstruction"] = [
                "parts" => [ ["text" => $systemInstruction] ]
            ];
        }

        // 4. Configuración JSON
        if ($outputFormat === 'json') {
            $payload["generationConfig"]["responseMimeType"] = "application/json";
        }

        // 5. Ejecución cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Más tiempo para modelos grandes

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // 6. Manejo de Errores
        if ($curlError) return self::response(false, null, "Red: $curlError");

        $decoded = json_decode($response, true);
        
        if ($httpCode !== 200) {
            $msg = $decoded['error']['message'] ?? "Error API ($httpCode)";
            return self::response(false, null, $msg);
        }

        // 7. Extracción de Datos
        try {
            $responseText = $decoded['candidates'][0]['content']['parts'][0]['text'];

            if ($outputFormat === 'json') {
                $cleanJson = str_replace(['```json', '```'], '', $responseText);
                $jsonData = json_decode($cleanJson, true);
                return (json_last_error() === JSON_ERROR_NONE) 
                    ? self::response(true, $jsonData) 
                    : self::response(false, $responseText, "JSON inválido de IA");
            }

            return self::response(true, $responseText);

        } catch (Exception $e) {
            return self::response(false, null, "Error estructura: " . $e->getMessage());
        }
    }

    private static function response($success, $data, $error = null) {
        return ['success' => $success, 'data' => $data, 'error' => $error];
    }
}
?>