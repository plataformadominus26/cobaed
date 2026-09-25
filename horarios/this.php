<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once '../rbh/conexion1.php';
    include_once '../rbh.php';
    dump(print_r($_REQUEST,true),"rdata.txt");

    if(isset($_REQUEST["eliminarClase"])){
       
        list($inicio, $fin) = array_map('trim', explode('-', $_REQUEST["hora"]));
        $sql="select * from usuarios a, periodos b ";
        $sql.=" where a.token='".$_REQUEST["eliminarClase"]."' and a.book_id=b.book_id ";
        $sql.=" and b.activo=1 ";
        
        $periodo=$db->query($sql)->fetch_array();
        $periodo=$periodo["periodo_id"];

         
        $sqlDelete = "DELETE FROM horarios WHERE usuario_id = '" . $db->real_escape_string($_REQUEST["maestro"]);
        $sqlDelete .= "' AND dia = '" . $db->real_escape_string($_REQUEST["dia"]);
        $sqlDelete .= "' AND periodo_id = '" . $db->real_escape_string($periodo) . "'";
        $sqlDelete .=" AND hora='$inicio' AND hora1='$fin'";
        dump($sqlDelete,"rdata.txt","a");
        $db->query($sqlDelete);
        $response = array(
            "ok" => true,
            "message" => "Eliminación exitosa.",
            "sql" => $sqlDelete
         );
         header('Content-Type: application/json');
         echo json_encode($response);
         die();
    }

    if(isset($_REQUEST["registrarClase"])){
        $token=bin2hex(random_bytes(4));
        list($inicio, $fin) = array_map('trim', explode('-', $_REQUEST["hora"]));
        $sql="select * from usuarios a, periodos b ";
        $sql.=" where a.token='".$_REQUEST["registrarClase"]."' and a.book_id=b.book_id ";
        $sql.=" and b.activo=1 ";
        
        $periodo=$db->query($sql)->fetch_array();
        $periodo=$periodo["periodo_id"];

        $sql="select * from horarios where usuario_id<>'".$_REQUEST["maestro"]."' and dia='".$_REQUEST["dia"]."' ";
        $sql.=" and ((addtime(hora, '00:01:00') between '$inicio' and '$fin') or (subtime(hora1, '00:01:00') between '$inicio' and '$fin')) ";
        $sql.=" and periodo_id='".$periodo."' and area_id='".$_REQUEST["area"]."' ";
        dump($sql,"dato.txt","a");
        if($row=$db->query($sql)->fetch_array()){
            $response = array(
                "ok" => false,
                "message" => "El horario ya está ocupado.",
                "sql" => $sql
             );
             header('Content-Type: application/json');
             echo json_encode($response);
             die();
        }
         
        $sqlDelete = "DELETE FROM horarios WHERE usuario_id = '" . $db->real_escape_string($_REQUEST["maestro"]);
        $sqlDelete .= "' AND dia = '" . $db->real_escape_string($_REQUEST["dia"]);
        $sqlDelete .= "' AND periodo_id = '" . $db->real_escape_string($periodo) . "'";
        $sqlDelete .=" AND hora='$inicio' AND hora1='$fin'";
        dump($sqlDelete,"dato.txt","a");
        $db->query($sqlDelete);

        $sql="insert into horarios set token= '".$token."',";
        $sql.= " usuario_id='".$_REQUEST["maestro"]."', dia='".$_REQUEST["dia"]."',";
        $sql.=" hora='".$inicio."', hora1='".$fin."', area_id='".$_REQUEST["area"]."',";
        $sql.=" periodo_id='".$periodo."'";
        dump($sql,"dato.txt","a");
       
        if($db->query($sql)){
            $response = array(
                "ok" => true,
                "message" => "Registro exitoso.",
                "sql" => $sql
             );
        } else {
            $response = array(
                "ok" => false,
                "message" => "No se pudo registrar el horario.",
                $sql => $sql
            );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        die();
    }
    if(isset($_REQUEST["fetchAreas"])){
        $sql="select * from usuarios where token='".$_REQUEST["fetchAreas"]."'";
        $row= $db->query($sql)->fetch_assoc();
        $miBook=$row["book_id"];
        $hora=$_REQUEST["hora"];
        // Parse "inicio" and "fin" from the pattern "12:45 - 13:45"
        list($inicio, $fin) = array_map('trim', explode('-', $_REQUEST["hora"]));
         
        $sql = "SELECT a.*, IFNULL(b.horario_id, -1) AS ocupada, u.nombre AS usuario_nombre
            FROM areas a
            LEFT OUTER JOIN horarios b ON a.area_id = b.area_id 
                AND b.dia = '" . $db->real_escape_string($_REQUEST["dia"]) . "'
                AND ((ADDTIME(b.hora, '00:01:00') BETWEEN '$inicio' AND '$fin') OR (SUBTIME(b.hora1, '00:01:00') BETWEEN '$inicio' AND '$fin'))
            LEFT OUTER JOIN usuarios u ON b.usuario_id = u.usuario_id
            WHERE tipo_id<4 and a.activa = 1 AND a.book_id = '" . $db->real_escape_string($miBook) . "'
            ORDER BY a.nombre";
            dump($sql,"ocupados.txt");
        $result = $db->query($sql);
        $disponibles=[];
        $ocupadas=[];
        while($row = $result->fetch_assoc()) {
            if($row["ocupada"]>-1)
                $ocupadas[]=$row;
            else
                $disponibles[]=$row;
            
        }
         $maxRows = max(count($disponibles), count($ocupadas));
        for ($i = 0; $i < $maxRows; $i++) {
            echo "<tr>";
            // Disponibles
            if (isset($disponibles[$i])) {
                echo "<td class='libre' data-id='{$disponibles[$i]['area_id']}'>{$disponibles[$i]['nombre']}</td>";
            } else {
                echo "<td></td>";
            }
            // Ocupadas
            if (isset($ocupadas[$i])) {
                echo "<td>{$ocupadas[$i]['nombre']}</td><td>{$ocupadas[$i]['usuario_nombre']}</td>";
            } else {
                echo "<td></td><td></td>";
            }
            echo "</tr>";
        }
        die();
    }

    if(isset($_REQUEST["eliminar"])){
        $sql="update $tabla set activo=3 where token='".$_REQUEST["eliminar"]."'";
         $db->query($sql);
    }

    if(isset($_REQUEST['fetchMaestros'])){
        $sql="select * from usuarios where token='".$_REQUEST["fetchMaestros"]."'";
        $row= $db->query($sql)->fetch_assoc();
        $miBook=$row["book_id"];
        $sql="select * from usuarios where activo=1 and book_id='".$miBook."' order by nombre";
        $result = $db->query($sql);
        
        while($row = $result->fetch_assoc()) {
            print("<option value='".$row["usuario_id"]."'>".$row["nombre"]."</option>");
        }
        
        
        die();
    } 
    if(isset($_REQUEST["fetchListas"])){
        $sql="select * from usuarios where token='".$_REQUEST["fetchListas"]."'";
        $row= $db->query($sql)->fetch_assoc();
        $miBook=$row["book_id"];
        
            $controles=[];
            if($_REQUEST["usr"]!="x"){
                $sql="select * from usuarios where token='".$_REQUEST["usr"]."'";
                $row = $db->query($sql)->fetch_assoc();
                if($row){
                    // Get the list of fields from a comma-separated string in $controles
                     $fields = array_map('trim', explode(',',$_REQUEST["controles"]));
                    foreach ($fields as $field) {
                        if (isset($row[$field])) {
                            $controles[$field] = $row[$field];
                        }
                    }
                } 
            }

            $response = array(
                "status" => "success",
                
                "_controles_" => $controles,
            );


         
        header('Content-Type: application/json');
        echo json_encode($response);
        die();
    }
   
    
    if(isset($_REQUEST["registrar"])){
        file_put_contents(cobaed_log('request_debug.txt'), print_r($_REQUEST, true));
        $row=$db->query("select * from usuarios where token='".$_REQUEST["registrar"]."'")->fetch_assoc();
         $coma="";
        $sql="";
        $data=$_REQUEST["dta"];
        foreach($data as $key => $value){
            $data[$key] = $db->real_escape_string($value);
            $sql.= $coma.$key."='".$value."'";
            $coma=", ";
        }
        $tkn = bin2hex(random_bytes(4));
       if($_REQUEST["nuevo"] === "true" || $_REQUEST["nuevo"] === true) 
         $sql="insert into $tabla set token='$tkn', book_id='".$row["book_id"]."', ".$sql;
        else 
         $sql="update $tabla set ".$sql." where token='".$_REQUEST["token"]."'";
        file_put_contents(cobaed_log('sql_debug.txt'), $sql . PHP_EOL, FILE_APPEND);
        
        if($db->query($sql)){
            $response = array(
                "ok" => true,
                "message" => "Registro exitoso.",
                "sql" => $sql
             );
        } else {
            $response = array(
                "ok" => false,
                "message" => "No se pudo registrar el usuario.",
                $sql => $sql
            );
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        die();


    }



?>