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
 
    $orden=" fecha desc ";
    $tabla=" attendance a left join usuarios b on a.usuario_id=b.usuario_id ";
    $tabla.=" join areas c on a.area_id=c.area_id ";
    $tabla.=" join usuarios d on a.maestro_id=d.usuario_id ";
    $scope="  (b.book_id=$miBook) ";

    
 
    /*    TOTAL DE REGISTROS EN LA B.D    */
    $sql="SELECT count(*) x FROM $tabla WHERE $scope ";
     dump("sx..".$sql,"data.txt","a");   
    $row=$db->query($sql)->fetch_array();
    $n=$row["x"];
    $sql="SELECT count(*) x FROM $tabla  WHERE $scope ";
    $row=$db->query($sql)->fetch_array();
    $n1=$row["x"];
    /*    DATOS A MOSTRAR EN LA PANTALLA    */

    $sql="SELECT a.*,b.nombre reviso, c.nombre area, d.nombre maestro
        FROM  $tabla  WHERE $scope
        ORDER BY $orden LIMIT  ".$_REQUEST["start"].",".$_REQUEST["length"]; 
    dump("Datos ".$sql,"data.txt","a");   
    $res=$db->query($sql);
    $datos='{
        "recordsTotal":'.$n1.',
        "recordsFiltered":'.$n1.',
        "data":[';
    $coma="";
     $estados=explode(",",",falta,retraso,asistencia");
    while($row=$res->fetch_array()){
        foreach($row as $k => $v) {
            if ($v == "") continue;
            $v = str_replace(array("\r\n", "\n", "\r", "\t"), '|CR|', $v);
            $v = str_replace("'", '|CM|', $v);
            $v = str_replace('"', '|CM|', $v);
            $v = mb_strtolower($v);
            $row[$k] = mb_convert_case($v, MB_CASE_TITLE, "UTF-8");
        }
        
        $datos.=$coma.'{"DT_RowId":"row_'.$row["token"].'"
        ,"acciones":"<button class=\'action-btn btn-edit me-1\'><i class=\'fas fa-pencil-alt\'></i></button><button class=\'action-btn btn-delete me-1\'><i class=\'fas fa-trash-alt\'></i></button>"
        ,"fecha":"'.addslashes($row["fecha"]).'"
        ,"maestro":"'.addslashes($row["maestro"]).'"
        ,"area":"'.addslashes($row["area"]).'"
        ,"estado":"'.addslashes($estados[$row["estado"]]).'"
        ,"comentarios":"'.addslashes($row["rems"]).'"
        ,"reviso":"'.addslashes($row["reviso"]).'"
	}';
   $coma=",";
}
$datos.="]}";
dump(PHP_EOL.$datos,"data.txt","a");
 print( $datos);
$db->close();
 
?>