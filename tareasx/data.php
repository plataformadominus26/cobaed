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
    file_put_contents("r.txt",PHP_EOL."Book: ".print_r($_REQUEST,true));
 
    $datos=$_REQUEST;
 
    $orden=" fecha desc, b.nombre";
    $tabla=" tareas a, usuarios b, empresas c , areas_trn d, areas e";
    $scope="  (a.usuario_id=b.usuario_id) and (b.empresa_id=c.empresa_id) and (d.area_id=e.area_id) ";
    $scope.=" and (a.area_trn_id=d.area_trn_id) and (c.book_id='".$miBook."' ) ";
    if($_REQUEST["cbSucursal"]!="-1") {
        $scope.=" and b.empresa_id='".$_REQUEST["cbSucursal"]."' ";
    }

    
 
    /*    TOTAL DE REGISTROS EN LA B.D    */
    $sql="SELECT count(*) x FROM $tabla WHERE $scope ";
     dump("sx..".$sql,"data.txt","a");   
 
     $row=$db->query($sql)->fetch_array();
    $n=$row["x"];
    $sql="SELECT count(*) x FROM $tabla  WHERE $scope ";
    $row=$db->query($sql)->fetch_array();
    $n1=$row["x"];
    $sql="SELECT c.nombre sucursal, e.nombre area, b.nombre usuario, a.tarea, a.fecha hora, a.estado, a.rems, a.token
        FROM  $tabla  WHERE $scope
        ORDER BY $orden LIMIT ".$_REQUEST["start"].",".$_REQUEST["length"]; 
     dump("Datos ".$sql,"data.txt","a");   
    $res=$db->query($sql);
    $datos='{
        "recordsTotal":'.$n1.',
        "recordsFiltered":'.$n1.',
        "data":[';
    $coma="";
    $estados=explode(",","activo,ok,vencido,cancelado");
    while($row=$res->fetch_array()){
	    foreach($row as $k => $v) {
            if ($v == "") continue;
            $v = str_replace(array("\r\n", "\n", "\r", "\t"), '|CR|', $v);
            $v = str_replace("'", '|CM|', $v);
            $v = str_replace('"', '|CM|', $v);
            $v = mb_strtolower($v);
            $row[$k] = mb_convert_case($v, MB_CASE_TITLE, "UTF-8");
        }
        /*
        const columns = "acciones,sucursal,area,usuario,tarea,hora,estado,rems".split(",")
        */
        $datos.=$coma.'{"DT_RowId":"row_'.$row["token"].'"
        ,"acciones":"<button class=\"action-btn btn-view me-1\"><i class=\"bi bi-eye\"></i></button><button class=\"action-btn btn-cancel me-1\"><i class=\"bi bi-x\"></i></button>"
        ,"sucursal":"'.addslashes($row["sucursal"]).'"
        ,"area":"'.addslashes($row["area"]).'"
        ,"usuario":"<button type=\"button\" class=\"btn btn-primary shadow w-100 btUsr\">'.addslashes($row["usuario"]).'</button>"
        ,"tarea":"'.addslashes($row["tarea"]).'"
        ,"hora":"'.addslashes($row["hora"]).'"
        ,"estado":"'.addslashes($estados[$row["estado"]]).'"
        ,"rems":"'.addslashes($row["rems"]).'"
        
	}';
   $coma=",";
}
$datos.="]}";
dump(PHP_EOL.$datos,"data.txt","a");
print($datos);
$db->close();
 
?>