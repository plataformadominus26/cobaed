<?php

   if(!isset($_REQUEST["action"])) {
       echo "error";
       exit();
   }
   include_once("../rbh/conexion1.php");
    switch($_REQUEST["action"]) {
       case "getConfig":
            $retVal=getConfig();
        break;
        case "login":
            $username=$_REQUEST["username"];
            $password=$_REQUEST["password"];
            $username=(string)$username;
            $password=(string)$password;
            $st=$db->prepare("SELECT * FROM alumnos WHERE (email=? OR telefono=? OR matricula=?) AND pwd=?");
            $st->bind_param("ssss", $username, $username, $username, $password);
            $st->execute();
            $row = trim($username) !== "" ? $st->get_result()->fetch_assoc() : null;
            if($row) {
                $row["ok"]="true";
                $retVal=json_encode($row);

            } else  
                $retVal=json_encode(array("ok"=>"error","message"=>"Clave o usuario incorrecto"));
             
    }
    header('Content-Type: application/json');
    echo $retVal;

    function getConfig() {
         $config = array();
         $kobaiDir="../assets/img/kobai";
         $randomPng=scandir($kobaiDir);
        $randomPng = array_filter(scandir($kobaiDir), function($file) {
            return preg_match('/\.(png|jpg|jpeg|gif)$/i', $file);
        });
         $randomIndex=array_rand($randomPng);
         $config["mascotImg"]="../assets/img/kobai/".$randomPng[$randomIndex];
        //
         return json_encode($config);
    }
?>