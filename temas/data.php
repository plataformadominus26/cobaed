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
    $tabla = "alumnos u";
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
            *
            FROM alumnos u
             
                where $scope
                   
                ORDER BY $orden LIMIT $start, $length;
        ";
    dump("Datos " . $sql, "data.txt", "a");

    $res = $db->query($sql);

    $datos = '{
        "recordsTotal":' . $n1 . ',
        "recordsFiltered":' . $n1 . ',
        "data":[';
    $coma = "";

    $estados = explode(",", "inactivo,activo,vacaciones");
    $puestos = explode(",", ",Maestro,Prefecto,Intendencia");
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
            "nombre" => addslashes($row["nombre"]),
            "telefono" => $row["telefono"],
            "email" => $row["email"],
            "semestre" => $row["semestre"],
            "grupo" => $row["grupo"],
            
            "sexo" => ($row["sexo"] == "M" 
                ? "<i class='bi bi-gender-male text-primary'></i>" 
                : "<i class='bi bi-gender-female text-pink'></i>"),    "estado" => $estados[$row["estado"]]
        ]);
        $coma = ",";
    }

    $datos .= "]}";
     print($datos);

    $db->close();
    ?>