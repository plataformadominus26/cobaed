<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
   include_once("conexion1.php");
   if(isset($_REQUEST["fetchOffline"])) {
       $token = $_REQUEST["fetchOffline"];
       // Fetch offline data from the database using the token
       $sql = "SELECT * FROM usuarios WHERE token ='$token'";
       $row = $db->query($sql)->fetch_assoc();
       $book_id = $row['book_id'];
       $sql="select * from usuarios where  book_id='$book_id' order by nombre";
       $res=$db->query($sql);
         $usuarios = array();
         while($row = $res->fetch_assoc()) {
             $usuarios[] = $row;
         }
         $sql="select * from areas where  book_id='$book_id' order by nombre";
         $res=$db->query($sql);
         $areas = array();
         while($row = $res->fetch_assoc()) {
             $areas[] = $row;
         }
        $sql="select * from horarios where usuario_id in (select usuario_id from usuarios where book_id='$book_id') order by usuario_id, area_id";
         $res=$db->query($sql);
         $horarios = array();
         while($row = $res->fetch_assoc()) {
             $horarios[] = $row;
         }
         $attendance=array();
         $sql="select * from attendance where usuario_id in (select usuario_id from usuarios where book_id='$book_id') and fecha >= curdate() - interval 7 day order by fecha, checkin";
         $res=$db->query($sql);
         while($row = $res->fetch_assoc()) {
             $attendance[] = $row;
         }

       $info= array();
       $info[] = array(
           "id" => 1,  // Add a unique identifier
           "fecha" => date("Y-m-d"),
           "hora" => date("H:i:s"),
           "book_id" => $book_id,  // Add book_id for reference
           "rKey" => "config_" . $book_id . "_" . date("Y-m-d")  // Add rKey for EasyDB
        
);



         header("Content-Type: application/json");
         echo json_encode(array("usuarios" => $usuarios, "areas" => $areas, "horarios" => $horarios, "info" => $info, "attendance" => $attendance));
         exit;
    }

?>