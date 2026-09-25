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
            $sql="SELECT * FROM alumnos WHERE (email='".$_REQUEST["username"]."'";
            $sql.=" or telefono='".$_REQUEST["username"]."'";
            $sql.=" or matricula='".$_REQUEST["username"]."')";
            $sql.=" AND pwd='".$_REQUEST["password"]."'";
            $row=$db->query($sql)->fetch_assoc();
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