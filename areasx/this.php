<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once '../rbh/conexion1.php';
    $tabla = "areas";
    if(isset($_REQUEST["eliminar"])){
        $sql="update $tabla set activo=3 where token='".$_REQUEST["eliminar"]."'";
         $db->query($sql);
    }

    if(isset($_REQUEST["fetchListas"])){
        $sql="select * from usuarios where token='".$_REQUEST["fetchListas"]."'";
        $row= $db->query($sql)->fetch_assoc();
        $miBook=$row["book_id"];
        
            $controles=[];
            if($_REQUEST["usr"]!="x"){
                $sql="select * from $tabla where token='".$_REQUEST["usr"]."'";
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