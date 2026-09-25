<?php  
    ini_set('display_errors', 1); 
    ini_set('display_startup_errors', 1); 
    error_reporting(E_ALL);
    date_default_timezone_set('America/Monterrey');
    include_once("../rbh/conexion1.php");
    include_once("../rbh.php");
    $sql="select * from usuarios where token='".$_REQUEST["token"]."'";
    dump($sql,"data.txt");
    $row=$db->query($sql)->fetch_array();
    $miBook=$row["book_id"];
 
    $datos=$_REQUEST;
 
    $orden=" nombre";
    $tabla=" dptos";
    $scope="  (book_id=1)";

    
 
    /*    TOTAL DE REGISTROS EN LA B.D    */
    $sql="SELECT count(*) x FROM $tabla WHERE $scope ";
  
    dump("sx..".$sql,"data.txt","a");   
    $row=$db->query($sql)->fetch_array();
    $n=$row["x"];
    $sql="SELECT count(*) x FROM $tabla  WHERE $scope ";
    $row=$db->query($sql)->fetch_array();
    $n1=$row["x"];
    $sql="SELECT *
        FROM  $tabla  WHERE $scope
        ORDER BY $orden LIMIT ".$_REQUEST["start"].",".$_REQUEST["length"]; 
    dump("Datos ".$sql,"data.txt","a");   
    $res=$db->query($sql);
    $datos='{
        "recordsTotal":'.$n1.',
        "recordsFiltered":'.$n1.',
        "data":[';
    $coma="";
    $estados=explode(",","activo,inactivo,vacaciones");
    while($row=$res->fetch_array()){
	    foreach($row as $k => $v) {
            if ($v == "") continue;
            $v = str_replace(array("\r\n", "\n", "\r", "\t"), '|CR|', $v);
            $v = str_replace("'", '|CM|', $v);
            $v = str_replace('"', '|CM|', $v);
            $v = mb_strtolower($v);
            $row[$k] = mb_convert_case($v, MB_CASE_TITLE, "UTF-8");
        }
        $sql="select count(*) x from usuarios where dpto_id='".$row["dpto_id"]."'";
        $row2=$db->query($sql)->fetch_array();

        $datos.=$coma.'{"DT_RowId":"row_'.$row["token"].'"
        ,"acciones":"<button class=\'action-btn btn-edit me-1\'><i class=\'fas fa-pencil-alt\'></i></button><button class=\'action-btn btn-delete me-1\'><i class=\'fas fa-trash-alt\'></i></button>"
        ,"nombre":"'.addslashes($row["nombre"]).'"
        ,"empleados":"'.addslashes($row2["x"]).'"        
	}';
   $coma=",";
}
$datos.="]}";
dump(PHP_EOL.$datos,"data.txt","a");
print($datos);
$db->close();
 
?>