<?php
// Mostrar errores de PHP para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de la base de datos

    include_once '../rbh/conexion1.php';
    $dia_semana = date('w'); // PHP usa 0 para domingo
    $dia_sistema = $dia_semana + 1;
    if ($dia_sistema > 7) $dia_sistema = 1;
    $fecha_actual = date('dmy');
    $secuencia = 1;
    $sql = "SELECT a.* FROM areas_trn a, areas b  ";
    $sql.=" WHERE (a.area_id=b.area_id) and dia_$dia_sistema = 1 ";
     $result = $db->query($sql);
   
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $area_trn_id = $row['area_trn_id'];
            $token = bin2hex(random_bytes(16));
            $tarea = $row['tarea'];
            $prioridad = $row['prioridad'];
            $hora = $row['hora'];
            $usuario_id = $row['usuario_id'];
            $check_sql = "SELECT COUNT(*) as count FROM tareas 
                     WHERE area_trn_id = $area_trn_id 
                     AND DATE(fecha) = CURDATE()";
            $check_result = $db->query($check_sql);
            $check_row = $check_result->fetch_assoc();
            if ($check_row['count'] == 0) {
                $clave = $fecha_actual . str_pad($secuencia, 3, '0', STR_PAD_LEFT);
                 
                $secuencia++;                
                $fecha_completa = date('Y-m-d') . ' ' . $hora;
                $insert_sql = "INSERT INTO tareas 
                    (token, clave, area_trn_id, usuario_id, tarea, fecha, dia, estado, prioridad, rems) 
                    VALUES 
                    ('$token', '$clave', $area_trn_id, $usuario_id, '$tarea', '$fecha_completa', $dia_sistema, 0, $prioridad, '')";
                    
                    print($insert_sql."<br>");
                if ($db->query($insert_sql) === TRUE) {
                        echo "Tarea creada: $tarea (ID: $area_trn_id)\n";
                } else {
                        echo "Error al crear tarea: " . $db->error . "\n";
                }
            } else {
                echo "Ya existe una tarea para hoy en el área: $area_trn_id\n";
            }
                }
            }
        else {
        echo "No hay tareas programadas para hoy.\n";
    }

// Cerrar conexión
$db->close();
?>