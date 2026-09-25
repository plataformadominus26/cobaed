<?php
// api/controllers/IngestaController.php

/**
 * Recibe el texto sucio y lo pasa por Gemini
 */

include_once("geminiClient.php");
function procesar_ingesta($p) {
    // $p ya es un array asociativo gracias al index.php
    
    $rawText = $p['text'] ?? '';
    $materia = $p['materia'] ?? 'General';
    $tema = $p['tema'] ?? 'Sin tema';

    if (strlen($rawText) < 5) {
        throw new Exception("El texto es muy corto para procesar.");
    }

    // Lógica de Gemini (usando tu cliente estático)
    $systemInst = "Eres Kobai. Limpia el texto, usa HTML y LaTeX ($$..$$). Materia: $materia.";
    $prompt = "INPUT: " . $rawText;

    // Llamada a tu clase GeminiClient
    $resultado = GeminiClient::call($prompt, $systemInst, 'json', 0.3);

    if (!$resultado['success']) {
        throw new Exception("Error IA: " . $resultado['error']);
    }

    // Retornamos SOLO los datos. El index.php agregará {ok: true}
    return $resultado['data'];
}

function sugerir_ingesta($p) {
    global $db; // Asumo que usas tu conexión mysqli global
    
    // 1. LIMPIEZA Y SEGURIDAD (Sanitización básica para tu estilo mysqli)
    $temaId = intval($p['tema_id']); 
    
    // 2. SQL MEJORADO (El "Triple Join")
    // Traemos los datos del Tema, la Materia escolar y la Rama Científica experta
    $sql = "SELECT 
                t.titulo AS tema_titulo,
                m.nombre AS materia_nombre,
                rc.nombre AS rama_ciencia,
                rc.ambito AS ambito,
                rc.descripcion_ia
            FROM temas t
            JOIN materias m ON t.materia_id = m.materia_id
            LEFT JOIN ramas_ciencia rc ON m.rama_id = rc.rama_id
            WHERE t.tema_id = $temaId";

    $result = $db->query($sql);
    
    if (!$result || $result->num_rows === 0) {
        throw new Exception("No se encontró información del tema ID: $temaId");
    }

    $row = $result->fetch_assoc();

    // 3. EXTRACCIÓN DE VARIABLES DE CONTEXTO
    $tema = $row['tema_titulo'];
    $materia = $row['materia_nombre'];
    $ramaCiencia = $row['rama_ciencia'] ?? 'General';
    $ambito = $row['ambito'] ?? 'Académico';
    $instruccionesRama = $row['descripcion_ia'] ?? 'Sé didáctico y claro.';

    // 4. PROMPT CON INYECCIÓN DE CONTEXTO CIENTÍFICO
    $templatePrompt = <<<EOD
ACTUAR COMO: Kobai Architect.
OBJETIVO: Generar JSON Estructurado para el CMS de COBAED.

CONTEXTO CIENTÍFICO:
- Ámbito: $ambito
- Rama Científica: $ramaCiencia
- Asignatura Escolar: $materia
- Tema Específico: $tema

INSTRUCCIONES DE ESPECIALIDAD ($ramaCiencia):
$instruccionesRama

REGLAS PEDAGÓGICAS:
- Audiencia: Preparatoria México (15-18 años).
- Enfoque: Didáctico, claro y con andamiaje (paso a paso).

REGLAS TÉCNICAS:
- Salida: JSON Estricto.
- Fórmulas: LaTeX ($$ ... $$).
- Texto: Markdown (No HTML).

ESTRUCTURA JSON REQUERIDA:
{
  "titulo_unidad": "string (Título formal)",
  "contenido_teorico_markdown": "string (Explicación completa. Usa negritas para conceptos clave)",
  "analogia_vida_real": "string (Ejemplo situacional para enganchar al alumno)",
  "ejercicios_practicos": [
    {
      "planteamiento": "string (El problema)",
      "solucion_paso_a_paso": [
        { 
            "paso": "string (Nombre del paso)", 
            "explicacion": "string (Qué se hizo)", 
            "resultado_parcial_latex": "string (La fórmula en ese momento)" 
        }
      ],
      "resultado_final_latex": "string"
    }
  ],
  "recursos_externos": {
    "video_query_sugerido": "string (Término de búsqueda exacto para YouTube, ej: 'Derivadas facil JulioProfe')",
    "bibliografia_sugerida": { 
        "titulo": "string", 
        "autor": "string", 
        "isbn": "string (Si aplica)" 
    }
  }
}
EOD;


 

 

    // 5. LLAMADA A GEMINI
    $resultado = GeminiClient::call(
        "Genera el contenido estructurado para el tema: $tema", 
        $templatePrompt, 
        'json', 
        0.3
    );

    if (!$resultado['success']) {
        throw new Exception("Error IA: " . ($resultado['error'] ?? 'Desconocido'));
    }

    return $resultado['data'];
}

/**
 * Guarda en MySQL y sincroniza con Qdrant
 */
function guardar_conocimiento($p) {
    global $pdo; // Usamos la conexión global de db.php

    $pdo->beginTransaction();

    try {
        // ... (Aquí va la lógica de INSERT que definimos antes) ...
        // ... (Usando sentencias preparadas PDO) ...
        
        // Ejemplo resumido:
        $sql = "INSERT INTO conocimiento_chunks (contenido, estado) VALUES (:c, 'aprobado')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':c' => $p['contenido_html']]);
        
        $newId = $pdo->lastInsertId();

        // Sincronizar con Qdrant (Llamada interna a otra función PHP si quieres)
        // sync_qdrant_internal($newId); 

        $pdo->commit();

        return ["id" => $newId, "mensaje" => "Guardado exitosamente"];

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e; // Re-lanzar para que index.php lo capture
    }
}
?>