<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include_once '../rbh/conexion1.php';
    include_once '../rbh.php';
    $tabla = "usuarios";
    file_put_contents(cobaed_log('rq.txt'), print_r($_REQUEST, true), FILE_APPEND);


    if(isset($_REQUEST["fetchFiltros"])){

        $sql="select * from  usuarios  where token='".$_REQUEST["fetchFiltros"]."'";
        $row=$db->query($sql)->fetch_assoc();
        switch($row["nivel"]){
            case 1:
                $scope=" empresa_id='".$row["empresa_id"]."'";
                break;
            case 2:
                $scope=" book_id='".$row["book_id"]."'";
                break;
            default:
                $scope=" 1=2";
                break;
        }
        $resp=[];
        $suc=[];
        $sql="select * from empresas where $scope order by nombre";
        $res=$db->query($sql);
        while($row=$res->fetch_assoc()){
            $suc[]=[
                "id" => $row["empresa_id"],
                "text" => $row["nombre"]
            ];
        }
        $resp["sucursales"]=$suc;
        header('Content-Type: application/json');
        echo json_encode($resp);
        die();
    }

    if(isset($_GET["autocomplete"])) {
        header('Content-Type: application/json');
        $suggestions = [];
        $sql = "SELECT DISTINCT tarea
                    FROM areas_trn a, areas b, usuarios c
                    WHERE a.area_id=b.area_id and b.book_id=c.book_id and c.token='".$_REQUEST["autocomplete"]."'
                    order by tarea";
        $res = $db->query($sql);
        while ($row = $res->fetch_assoc()) {
            $suggestions[] = $row['tarea'];
        }
        $term = $_GET['term'] ?? '';    
        if (empty($term)) {
            // Return all suggestions if no term
            echo json_encode($suggestions);
            exit;
        }
        $filtered = array_filter($suggestions, function($item) use ($term) {
            return stripos($item, $term) !== false;
        });
        echo json_encode(array_values($filtered));
        exit;
    }

    if(isset($_REQUEST["eliminar"])){
        $sql="update $tabla set activo=3 where token='".$_REQUEST["eliminar"]."'";
         $db->query($sql);
    }

    
    
    if(isset($_REQUEST["registrar"])){
         $row=$db->query("select * from usuarios where token='".$_REQUEST["uid"]."'")->fetch_assoc();
        $_REQUEST["registrar"] = ($_REQUEST["registrar"] === 'true');
        if($_REQUEST["registrar"]){
            $sql=" insert into areas set token='".bin2hex(random_bytes(8))."', book_id='".$row["book_id"]."'";
            $sql.=", nombre='".$_REQUEST["nombre"]."', tipo_id='".$_REQUEST["tipo_id"]."'";
            $db->query($sql);
            $id=$db->insert_id;
        }else{
            $sql=" update areas set tipo_id='".$_REQUEST["tipo_id"]."', ";
            $sql.=" nombre='".$_REQUEST["nombre"]."' where token='".$_REQUEST["tkn"]."'";
            dump($sql,"areas.txt");
            print($sql);
             $db->query($sql);
            $sql="select * from areas where token='".$_REQUEST["tkn"]."'";
            $id=$db->query($sql)->fetch_assoc()["area_id"];
            $sql="delete from areas_trn where area_id='".$id."'";
             $db->query($sql);
        }
        if (isset($_REQUEST["tareas"]) && is_array($_REQUEST["tareas"])) {
            foreach($_REQUEST["tareas"] as $tarea){
                $sql="insert into areas_trn set area_id='".$id."', token='".bin2hex(random_bytes(8))."'";
                if($tarea["id"]!=-1)
                    $sql.=", area_trn_id='".$tarea["id"]."'";
                unset($tarea["id"]);
            foreach($tarea as $key => $value){
            $sql.=", $key='".$value."'";
            }
            dump($sql,"trn.txt");
             $db->query($sql);
            }
        }
         die();
    }

    if(isset($_REQUEST["fetch_tareas"])){
        $resp=[];
        $sql="select * from areas where token='".$_REQUEST["fetch_tareas"]."'";
        $row=$db->query($sql)->fetch_assoc();
        $resp["area"]=$row["nombre"];
        $sql="select * from areas_trn where area_id='".$row["area_id"]."' order by hora, tarea";
        
        $resp["tareas"]=[];
        $res=$db->query($sql);
        $even=1;
        while($row=$res->fetch_assoc()){
             
        switch ($row["prioridad"]) {
            case '0':
                $badge = '<span class="badge bg-success">Baja</span>';
                break;
            case '1':
                $badge = '<span class="badge bg-warning text-dark">Media</span>';
                break;
            case '2':
                $badge = '<span class="badge bg-danger">Alta</span>';
                break;
            default:
                $badge = '<span class="badge bg-secondary">Sin prioridad</span>';
                break;
        }
        $prioridad = $badge;
             $clase=$row["foto"] ? "si" : "nop";
            $tr='
                <tr class="trTarea" data-id="'.$row["area_trn_id"].'">
                    <td>
                        <button class="btn btn-sm btn-danger me-1 btEliminarTarea" title="Eliminar" ><i class="bi bi-trash"></i></button>
                        <button class="btn btn-sm btn-success me-1 btDuplicarTarea" title="Duplicar"><i class="bi bi-plus-square-dotted"></i></button>
                    </td>
                    <td>
                        <button type="button" class="'.$clase.' btn btn-sm btn-light d-flex align-items-center justify-content-center w-100 border btCamara"  title="Requiere foto">
                        <i class="bi bi-check-circle-fill text-success d-none chSi"  ></i>
                        <i class="bi bi-circle text-success chNo" ></i>
                            <i class="fas fa-camera ms-2"></i>
                        </button>
                    </td>
                    <td><input type="text" class="form-control edAccion" value="'.$row["tarea"].'"></td>';
                    for($d = 1; $d <= 7; $d++){
                        $clase=$row["dia_".$d] ? "si" : "nop";
                        $tr .= '<td class="text-center chDia '.$clase.'">
                                    <i class="bi bi-check-circle-fill text-success d-none chSi"  ></i>
                                    <i class="bi bi-circle text-success chNo" ></i>
                                    </td>';
                    }
                    $tr .= '<td class="time-picker-cell">
                    <input type="time" class="d-none time-picker h24" value="'.date("H:i", strtotime($row["hora"])).'" step="60"  pattern="[0-9]{2}:[0-9]{2}" lang="en-GB">
                    <span class="time-display sp24">'.date("H:i", strtotime($row["hora"])).'</span>
                    
                    </td>
                            <td>
                                <span class="badge bgPrioridad">'.$badge.'</span>
                            </td>
                </tr>'; 


                    




                $resp["tareas"][]=$tr;
        }
        header('Content-Type: application/json');
        echo json_encode($resp);
        die();
    }


?>