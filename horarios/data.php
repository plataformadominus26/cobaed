    <?php  
    ini_set('display_errors', 1); 
    ini_set('display_startup_errors', 1); 
    error_reporting(E_ALL);
    date_default_timezone_set('America/Monterrey');

    include_once("../rbh/conexion1.php");
    include_once("../rbh.php");

    // Get period info for the user
    $sql = "SELECT b.* 
        FROM usuarios a, periodos b 
        WHERE a.token = '" . $_REQUEST["token"] . "' 
        AND a.book_id = b.book_id";
    $row = $db->query($sql)->fetch_array();
    $miBook = $row["book_id"];
    $periodo = $row["periodo_id"];

    // Get horarios for selected maestro and periodo
    $sql = "SELECT 
            h.horario_id,
            h.token,
            h.usuario_id,
            u.nombre AS maestro,        
            h.dia,
            h.hora,
            h.hora1,
            CONCAT(DATE_FORMAT(h.hora, '%H:%i'), ' - ', DATE_FORMAT(h.hora1, '%H:%i')) AS rango_horas,
            h.area_id,
            a.nombre AS area,           
            h.periodo_id
        FROM horarios h
        INNER JOIN usuarios u ON h.usuario_id = u.usuario_id
        INNER JOIN areas a ON h.area_id = a.area_id
        WHERE h.usuario_id = " . $_REQUEST["selectMaestro"] . " 
        AND h.periodo_id = " . $periodo . "
        ORDER BY   h.hora;";
    dump($sql, "data.txt");

    $emptyHora = array_fill(0, 7, "&nbsp;");
    
    $horario = [];
    $hora = [];
    $res = $db->query($sql);
  
    $horas = 0;
    $iniciar=6;
    $maxHora = "0500";
    dump($sql, "data.txt", "a");

    while ($row = $res->fetch_array()) {
        $clave = date('Hi', strtotime($row['hora'])) . date('Hi', strtotime($row['hora1']));
        if (!isset($horario[$clave])) {
            $horas++;
            $emptyHora[0] = $row['rango_horas'];
            $horario[$clave] = $emptyHora;
            $iniciar = intval(date('H', strtotime($row['hora1']))) + 1;
        }
        // Assign area to correct day (1=Monday, 6=Saturday)
        $diaIndex = intval($row['dia']);
        if ($diaIndex >= 1 && $diaIndex <= 6) {
            $horario[$clave][$diaIndex] = $row['area'];
        }
    }
    $datos = '{
        "recordsTotal":' . $horas . ',
        "recordsFiltered":' . $horas . ',
        "data":[';
    $coma = "";

    // Fill up to 10 rows if needed
     
    while ($horas < 10 && $iniciar < 23) {
        $horas++;
        $clave = str_pad($iniciar, 2, '0', STR_PAD_LEFT) . "00";
        $clave .= str_pad(++$iniciar, 2, '0', STR_PAD_LEFT) . "00";

        $xh = intval(substr($clave, 0, 2));
        $emptyHora[0] = substr($clave, 0, 2) . ":00 - " . str_pad($xh + 1, 2, '0', STR_PAD_LEFT) . ":00";
        $horario[$clave] = $emptyHora;
    }

    ksort($horario);

    foreach ($horario as $k => $v) {
        

         

        $datos .= $coma . '{"DT_RowId":"row_' . $k . '"'
        . ',"nombre":"' . $v[0] . '"'
        . ',"lun":"' . $v[1] . '"'
        . ',"mar":"' . $v[2] . '"'
        . ',"mie":"' . $v[3] . '"'
        . ',"jue":"' . $v[4] . '"'
        . ',"vie":"' . $v[5] . '"'
        . ',"sab":"' . $v[6] . '"'
        . '}';
        $coma = ",";
    }
    $datos .= "]}";
    print($datos);
    $db->close();
    die();
    ?>