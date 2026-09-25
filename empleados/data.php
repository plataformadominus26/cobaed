    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    date_default_timezone_set('America/Monterrey');
    include_once("../rbh/conexion1.php");
    include_once("../rbh.php");
    dump(print_r($_REQUEST, true)   , "movil.txt");

    $token = $_REQUEST["token"];
     
    

     
    $sql = "SELECT * FROM usuarios a, periodos p WHERE a.token='$token' AND a.book_id = p.book_id and p.activo=1 ";
    dump($sql, "data.txt");

    $row = $db->query($sql)->fetch_array();
    $miBook = $row["book_id"];
    $periodo = $row["periodo_id"];
    $datos = $_REQUEST;

    $orden = "u.nombre";
    $tabla = "usuarios u";
    $scope = "(u.book_id=$miBook)";
    if(isset($_REQUEST["searchInput"])){
        $scopex=$_REQUEST["searchInput"];
        if(trim($scopex)!="") $scope .= " and u.nombre like '%".$scopex."%'";
       
    }
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
            u.nivel,u.activo,
            u.usuario_id,
            u.nombre           AS maestro,
            u.token,
            SUM(CASE WHEN h.dia = 1 THEN 1 ELSE 0 END) AS lun,
            SUM(CASE WHEN h.dia = 2 THEN 1 ELSE 0 END) AS mar,
            SUM(CASE WHEN h.dia = 3 THEN 1 ELSE 0 END) AS mie,
            SUM(CASE WHEN h.dia = 4 THEN 1 ELSE 0 END) AS jue,
            SUM(CASE WHEN h.dia = 5 THEN 1 ELSE 0 END) AS vie,
            SUM(CASE WHEN h.dia = 6 THEN 1 ELSE 0 END) AS sab,
           COUNT(h.horario_id) AS total 
            FROM usuarios u
            LEFT JOIN horarios h 
                ON h.usuario_id = u.usuario_id
                AND h.periodo_id =  $periodo          
                where $scope
                    GROUP BY u.usuario_id, u.nombre
                ORDER BY $orden LIMIT $start, $length;
        ";
    dump("Datos " . $sql, "data.txt", "a");

    $res = $db->query($sql);

    $datos = '{
        "recordsTotal":' . $n1 . ',
        "recordsFiltered":' . $n1 . ',
        "data":[';
    $coma = "";

    $estados = explode(",", "inactivo,activo,vacaciones,baja");
    $puestos = explode(",", "No Especificado,Maestro,Prefecto,Intendencia,Vigilancia,Administrativo,Directivo");
    $issel = "<i class='bi bi-check-circle-fill text-primary fs-5 btEste btSi'></i><i class='btNo btEste bi bi-circle fs-5 '></i>";

    while ($row = $res->fetch_array()) {
        foreach ($row as $k => $v) {
            if ($v == "") continue;
            $v = str_replace(array("\r\n", "\n", "\r", "\t"), '|CR|', $v);
            $v = str_replace("'", '|CM|', $v);
            $v = str_replace('"', '|CM|', $v);
            $v = mb_strtolower($v);
            $row[$k] = mb_convert_case($v, MB_CASE_TITLE, "UTF-8");
        }

        $datos .= $coma . json_encode([
            "DT_RowId" => "row_" . $row["token"],
            "acciones" => "<button class='action-btn btn-edit me-1'><i class='fas fa-pencil-alt'></i></button><button class='action-btn btn-delete me-1'><i class='fas fa-trash-alt'></i></button><button class='action-btn btn-qr me-1'><i class='bi bi-qr-code'></i></button>",
            "nombre" => addslashes($row["maestro"]),
            "lun" => "<div class='circle'>".$row["lun"]."</div>",
            "mar" => "<div class='circle'>".$row["mar"]."</div>",
            "mie" => "<div class='circle'>".$row["mie"]."</div>",
            "jue" => "<div class='circle'>".$row["jue"]."</div>",
            "vie" => "<div class='circle'>".$row["vie"]."</div>",
            "sab" => "<div class='circle'>".$row["sab"]."</div>",
            "total" => "<div class='circle total'>".$row["total"]."</div>",
            "puesto" => $puestos[$row["nivel"]],
            "estado" => $estados[$row["activo"]]
        ]);
        $coma = ",";
    }

    $datos .= "]}";
     print($datos);

    $db->close();
    ?>