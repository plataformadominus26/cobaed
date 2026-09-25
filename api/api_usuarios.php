<?php

function getProfile($p){
        global $db;
        
        $token=$p['token'];
        $ok=false;
        $r=[];
        $r["foto"]="";
        $sql = "SELECT a.*, b.nombre puesto";
        $sql.=" FROM usuarios a LEFT JOIN puestos b ";
        $sql.=" ON a.nivel = b.puesto_id WHERE a.token ='" . $db->real_escape_string($p['token']) . "'";;
        $url=$_SERVER['HTTP_HOST'] . "/fotos/";
        if($row=$db->query($sql)->fetch_assoc()){
            $ok=true;
            $r["nombre"]=$row["nombre"];
            $r["tipo"]="usuario";
            $r["puesto"]=$row["puesto"];
             $foto=$url."/profiles/" . $row["token"] .".jpg";
            if(file_exists($foto)) $r["foto"]=$foto;      
        } 
        else {
            $sql ="SELECT a.*, concat (b.nombre,' ',b.paterno) tipo";
            $sql.=" FROM alumnos ";
            $sql.=" WHERE token ='" . $db->real_escape_string($p['token']) . "'";
                   
            if($row=$db->query($sql)->fetch_assoc()){
                $ok=true;
                $r["nombre"]=$row["nombre"]." ".$row["paterno"];
                $r["tipo"]="alumno";
                $foto=$url."/profiles/" . $row["token"] .".jpg";
                if(file_exists($foto))$r["foto"]=$foto;      
            } 
             
        } 
    return $ok?$r:"";
    }

    function login($p){
        global $db;
        $email = $db->real_escape_string($p["username"]); // Basic sanitization
        $password = $p["password"];
        $r = [];
    
        // Prepared statement approach (better)
        $sql="SELECT a.*,b.nombre puesto FROM usuarios a , puestos b ";
        $sql.=" where a.nivel=b.puesto_id ";
        $sql.=" and (a.email='" . $email . "' or a.celular='" . $email . "')";
        $sql.=" and pwd='$password' and  activo = 1 LIMIT 1" ;

        if($row = $db->query($sql)->fetch_assoc()) {
            $r["ok"] = true;
            $r["tkn"] = $row["token"];
            $r["nombre"] = $row["nombre"];
            $r["puesto"]=$row["puesto"];
            $r["tipo"]="usuario";
        } 
        else {
            $sql="SELECT * FROM alumnos a";
            $sql.=" where (a.email='" . $email . "' or a.telefono='" . $email . "')";
            $sql.=" and pwd='$password' and  estado> 0 LIMIT 1" ;
            if($row = $db->query($sql)->fetch_assoc()) {
                $r["ok"] = true;
                $r["tkn"] = $row["token"];
                $r["nombre"] = $row["nombre"]." ".$row["paterno"]." ".$row["materno"];
                $r["puesto"]="";
                $r["tipo"]="alumno";
            } 
            else {
                $r["ok"] = false;
                $r["mensaje"] = "Credenciales incorrectas". $sql;
            }
    
        }
        return $r;
    }

    function sendResponse($data, $debug = []) {
        $response = ['ok' => true, 'data' => $data];
        $response['debug'] = $debug; // Temporal para debugging
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    function sendResponseWithDebug($data, $debug = []) {
        $response = ['ok' => true, 'data' => $data];
        $response['debug'] = $debug; // Temporal para debugging
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
?>