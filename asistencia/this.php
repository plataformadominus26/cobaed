<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once '../rbh/conexion1.php';
    $tabla = "usuarios";

    if(isset($_REQUEST["fetchListas"])){
        $sql="select * from usuarios where token='".$_REQUEST["fetchListas"]."'";
        $row= $db->query($sql)->fetch_assoc();
        if($row){
            $sql="select * from puestos where book_id='".$row["book_id"]."'";
            $sql.=" order by nombre ";
            $result = $db->query($sql);
            $roles ="";
            while($lista = $result->fetch_assoc()){
                $roles.= "<option value='".$lista["puesto_id"]."'>".$lista["nombre"]."</option>";
            }
            $sql="select * from dptos where book_id='".$row["book_id"]."'";
            $sql.=" order by nombre ";
            $result = $db->query($sql);
            $dptos ="";
            while($lista = $result->fetch_assoc()){
                $dptos.= "<option value='".$lista["dpto_id"]."'>".$lista["nombre"]."</option>";
            }
            $response = array(
                "status" => "success",
                "rol_id" =>$roles,
                "dpto_id" =>$dptos,
            );

            $sql="select * from horarios where book_id='".$row["book_id"]."'";
            $sql.=" order by nombre ";
            $result = $db->query($sql);
            $horarios ="";
            while($lista = $result->fetch_assoc()){
                $horarios.= "<option value='".$lista["horario_id"]."'>".$lista["nombre"]."</option>";
            }
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
                "puesto_id" =>$roles,
                "dpto_id" =>$dptos,
                "horario_id" =>$horarios,
                "_controles_" => $controles,
            );


        } else 
            $response = array(
                "status" => "error",
                "message" => "No data found for the provided token."
            );
        header('Content-Type: application/json');
        echo json_encode($response);
        die();
    }
   
    
    if(isset($_REQUEST["registrar"])){
        file_put_contents('request_debug.txt', print_r($_REQUEST, true));
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