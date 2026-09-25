    <?php  
    ini_set('display_errors', 1); 
    ini_set('display_startup_errors', 1); 
    error_reporting(E_ALL);
    date_default_timezone_set('America/Monterrey');
    include_once("../rbh/conexion1.php");
    include_once("../rbh.php");

    $token = $_REQUEST["token"];
     $sql = "SELECT * FROM usuarios a, periodos p WHERE a.token='$token' AND a.book_id = p.book_id and p.activo=1";
    dump($sql, "data.txt");

    $row = $db->query($sql)->fetch_array();
    $miBook = $row["book_id"];
    $periodo = $row["periodo_id"];
    $datos = $_REQUEST;

    $orden = "a.nombre";
    $tabla = "areas a";
    $scope = "(a.book_id=$miBook)";

    /* TOTAL DE REGISTROS EN LA B.D */
    $sql = "SELECT count(*) x FROM $tabla WHERE $scope";
    dump("sx.." . $sql, "data.txt", "a");
    $row = $db->query($sql)->fetch_array();
    $n = $row["x"];

    /* TOTAL filtrado DE REGISTROS EN LA B.D */
    $sql = "SELECT count(*) x FROM $tabla WHERE $scope";
   


    $row = $db->query($sql)->fetch_array();
    $n1 = $row["x"];

    /* --- datos --- */
    $start = $_REQUEST["start"];
    $length = $_REQUEST["length"];
    $sql = "SELECT * FROM $tabla WHERE $scope ORDER BY $orden LIMIT $start, $length";
     $sql="SELECT 
            a.token,
            a.area_id,
            tipo_id,
            a.nombre           AS area,
            SUM(CASE WHEN h.dia = 0 THEN 1 ELSE 0 END) AS lun,
            SUM(CASE WHEN h.dia = 1 THEN 1 ELSE 0 END) AS mar,
            SUM(CASE WHEN h.dia = 2 THEN 1 ELSE 0 END) AS mie,
            SUM(CASE WHEN h.dia = 3 THEN 1 ELSE 0 END) AS jue,
            SUM(CASE WHEN h.dia = 4 THEN 1 ELSE 0 END) AS vie,
            SUM(CASE WHEN h.dia = 5 THEN 1 ELSE 0 END) AS sab,
            COUNT(h.horario_id)                         AS total
            FROM areas a
            LEFT JOIN horarios h 
                ON h.area_id   = a.area_id
                AND h.periodo_id = $periodo
            WHERE $scope 
            GROUP BY a.area_id, a.nombre
            ORDER BY $orden LIMIT $start, $length
";
    dump("Datos " . $sql, "data.txt", "a");

    $res = $db->query($sql);

    $datos = '{
        "recordsTotal":' . $n1 . ',
        "recordsFiltered":' . $n1 . ',
        "data":[';
    $coma = "";

    $tipos = explode(",", ",Aula,Laboratorio,Taller,Biblioteca,Oficina,Baños,Area Verde,Pasillo,Bodega,Otro");

    while ($row = $res->fetch_array()) {
        foreach ($row as $k => $v) {
            if ($v == "") continue;
            $v = str_replace(array("\r\n", "\n", "\r", "\t"), '|CR|', $v);
            $v = str_replace("'", '|CM|', $v);
            $v = str_replace('"', '|CM|', $v);
            $v = mb_strtolower($v);
            $row[$k] = mb_convert_case($v, MB_CASE_TITLE, "UTF-8");
        }

        $issel = "<i class='bi bi-check-circle-fill text-primary fs-5 btEste btSi'></i><i class='btNo btEste bi bi-circle fs-5 '></i>";
        $datos .= $coma . '{"DT_RowId":"row_' . $row["token"] . '"'
            . ',"acciones":"<button class=\'action-btn btn-edit me-1\'><i class=\'bi bi-pencil-fill\'></i></button><button class=\'action-btn btn-delete me-1\'><i class=\'bi bi-trash-fill\'></i></button><button class=\'action-btn btn-qr me-1\'><i class=\'bi bi-qr-code\'></i></button>"'
            . ',"nombre":"' . addslashes($row["area"]) . '"'
            . ',"lun":"<div class=\'circle\'>' . $row["lun"] . '</div>"'
            . ',"mar":"<div class=\'circle\'>' . $row["mar"] . '</div>"'
            . ',"mie":"<div class=\'circle\'>' . $row["mie"] . '</div>"'
            . ',"jue":"<div class=\'circle\'>' . $row["jue"] . '</div>"'
            . ',"vie":"<div class=\'circle\'>' . $row["vie"] . '</div>"'
            . ',"sab":"<div class=\'circle\'>' . $row["sab"] . '</div>"'
            . ',"total":"<div class=\'circle total\'>' . $row["total"] . '</div>"'
            . ',"tipo":"' . $tipos[$row["tipo_id"]] . '"'
            . '}';    $coma = ",";
    }
    $datos .= "]}";
    dump(PHP_EOL . $datos, "data.txt", "a");
    print($datos);

    $db->close();
    ?>