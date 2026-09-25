<?php
// api/publicar_post.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'conexion.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recoger datos básicos del nuevo esquema
    $titulo_manual = $_POST['titulo'] ?? '';
    $resumen       = $_POST['resumen'] ?? '';
    $categoria     = $_POST['categoria'] ?? 'general';
    $estilo        = $_POST['estilo'] ?? 'standard';
    $estructura    = json_decode($_POST['contenido_json'], true); // Usamos el nombre del campo de la imagen
    
    $uploadBase = "../uploads/blog/";
    
    // Crear directorios si no existen
    if (!is_dir($uploadBase)) mkdir($uploadBase, 0755, true);        
    if (!is_dir($uploadBase . "images/")) mkdir($uploadBase . "images/", 0755, true);
    if (!is_dir($uploadBase . "docs/")) mkdir($uploadBase . "docs/", 0755, true);
   
    // 2. Procesar Archivos dentro de los bloques
    foreach ($estructura as $idx => &$bloque) {
        
        // Imágenes en Galería
        if ($bloque['type'] === 'gallery') {
            $bloque['content']['urls'] = [];
            // Buscamos archivos que coincidan con el índice del bloque (img_b0_0, img_b0_1...)
            foreach ($_FILES as $key => $file) {
                if (strpos($key, "img_b{$idx}_") === 0) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $fileName = "img_" . uniqid() . "." . $ext;
                    $path = $uploadBase . "images/" . $fileName;   
                    if (move_uploaded_file($file['tmp_name'], $path)) {
                        $bloque['content']['urls'][] = "uploads/blog/images/" . $fileName;
                    }
                }
            }
        }

        // Documentos
        if ($bloque['type'] === 'file') {
            $fileKey = "doc_b{$idx}";
            if (isset($_FILES[$fileKey])) {
                $ext = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
                $fileName = "doc_" . uniqid() . "." . $ext;
                $path = $uploadBase . "docs/" . $fileName;
                if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $path)) {
                    $bloque['content']['fileUrl'] = "uploads/blog/docs/" . $fileName;
                }
            }
        }
    }

    // 3. Determinar Título (Si no viene manual, buscamos el primer header)
    $tituloFinal = $titulo_manual;
    if (empty($tituloFinal)) {
        foreach($estructura as $b) { 
            if($b['type'] === 'header') { $tituloFinal = $b['content']['text']; break; } 
        }
    }
    if (empty($tituloFinal)) $tituloFinal = "Publicación sin título";

    // 4. Preparar JSON y Query para el nuevo esquema
    $jsonFinal = json_encode($estructura);
    
    // Escapar datos para evitar SQL Injection (Siguiendo tu estilo mysqli)
    $t = $db->real_escape_string($tituloFinal);
    $r = $db->real_escape_string($resumen);
    $e = $db->real_escape_string($estilo);
    $c = $db->real_escape_string($categoria);
    $j = $db->real_escape_string($jsonFinal);
    $autor_id = 1; // Ajustar según tu sistema de usuarios

    // Query ajustada a los 10 campos de tu imagen
    $sql = "INSERT INTO posts (titulo, resumen, estilo, categoria, contenido_json, autor_id, fecha_publicacion, estatus, vistas) 
            VALUES ('$t', '$r', '$e', '$c', '$j', $autor_id, CURRENT_TIMESTAMP, 1, 0)";

    if ($db->query($sql)) {
        echo json_encode(["status" => "ok", "id_post" => $db->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => $db->error]);
    }
}