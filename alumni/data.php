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

    $orden = "u.paterno, u.materno, u.nombre";
    $tabla = "alumnos u";
    $scope = "(u.book_id=$miBook)";
    if(isset($_REQUEST["searchInput"])){
        $scopex=$_REQUEST["searchInput"];
        if(trim($scopex)!="") $scope .= " and concat_ws(' ',u.paterno,u.materno,u.nombre) like '%".$db->real_escape_string($scopex)."%'";
       
    }
    if(isset($_REQUEST["fltNombre"])) {
        $v = trim($_REQUEST["fltNombre"]);
        if($v !== "") {
            $v = $db->real_escape_string($v);
            $scope .= " and concat_ws(' ',u.paterno,u.materno,u.nombre) like '%$v%'";
        }
    }
    if(isset($_REQUEST["fltSemestre"]) && $_REQUEST["fltSemestre"] !== "" && $_REQUEST["fltSemestre"] !== "-1") {
        $scope .= " and u.semestre=".(int)$_REQUEST["fltSemestre"];
    }
    if(isset($_REQUEST["fltGrupo"]) && $_REQUEST["fltGrupo"] !== "" && $_REQUEST["fltGrupo"] !== "-1") {
        $v = $db->real_escape_string(trim($_REQUEST["fltGrupo"]));
        $scope .= " and u.grupo='$v'";
    }
    if(isset($_REQUEST["fltTurno"]) && $_REQUEST["fltTurno"] !== "" && $_REQUEST["fltTurno"] !== "-1") {
        $scope .= " and u.turno_id=".(int)$_REQUEST["fltTurno"];
    }
    if(isset($_REQUEST["fltEstado"]) && $_REQUEST["fltEstado"] !== "" && $_REQUEST["fltEstado"] !== "-1") {
        $scope .= " and u.estado=".(int)$_REQUEST["fltEstado"];
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
        $ttkn=$row["token"];
        foreach ($row as $k => $v) {
            if ($v == "") continue;
            $v = str_replace(array("\r\n", "\n", "\r", "\t"), '|CR|', $v);
            $v = str_replace("'", '|CM|', $v);
            $v = str_replace('"', '|CM|', $v);
            $v = mb_strtolower($v);
            $row[$k] = mb_convert_case($v, MB_CASE_TITLE, "UTF-8");
        }

        $datos .= $coma . json_encode([
            "DT_RowId" => "row_" . $ttkn,
                        "acciones" => (strpos($row["nombre"], "eview") !== false 
                            ? "😊" 
                            : "<button class='action-btn btn-edit me-1'><i class='fas fa-pencil-alt'></i></button><button class='action-btn btn-delete me-1'><i class='fas fa-trash-alt'></i></button><button class='action-btn btn-qr me-1'><i class='bi bi-qr-code'></i></button>"),
                        "nombre" => addslashes(trim($row["paterno"]." ".$row["materno"]." ".$row["nombre"])),
            "telefono" => $row["telefono"],
            "email" => $row["email"],
            "semestre" => $row["semestre"],
            "grupo" => $row["grupo"],
            
            "sexo" => ($row["sexo"] == "1" 
                ? "<i class='bi bi-gender-male text-primary'></i>" 
                : "<i class='bi bi-gender-female text-pink'></i>"),    "estado" => $estados[$row["estado"]],
            "campus" => (strpos($row["nombre"], "eview") !== false 
                            ? "<i class='bi bi-toggle-on text-success fs-5 btEste btSi'></i>" 
                            : " <i class='bi bi-toggle-off text-secondary fs-5 btEste btNo'></i>"),
        ]);
        $coma = ",";
    }

    $datos .= "]}";
     print($datos);

    $db->close();
    ?>