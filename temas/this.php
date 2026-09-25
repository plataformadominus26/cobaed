<?php
   include_once("../rbh/conexion1.php");


    if(isset($_REQUEST["fetchAcademias"])){
        $sql="select * from usuarios where token='".$_REQUEST["fetchAcademias"]."'";
        $row=$db->query($sql)->fetch_assoc();
        $sql="select * from academias where book_id='".$row["book_id"]."' order by nombre";
        $res=$db->query($sql);
        $academias="<option value='-1' selected>Seleccione Academia...</option>";
        while($row=$res->fetch_assoc()){
            $academias.="<option value='".$row["academia_id"]."'>".$row["nombre"]."</option>";
        }
        header('Content-Type: application/json');
        $retval=[];
        $retval = [
            "ok"=>true,
            "academias"=>$academias,
            
        ];
        header('Content-Type: application/json');
        echo json_encode($retval);
        exit;
   }

    if(isset($_REQUEST["fetchMaterias"])){      
        $sql="select * from materias where academia_id='".$_REQUEST["fetchMaterias"]."' order by nombre";
        $res=$db->query($sql);
        $materias="<option value='-1' selected>Seleccione Materia...</option>";
        while($row=$res->fetch_assoc()){
            $materias.="<option value='".$row["materia_id"]."'>".$row["nombre"]."</option>";
        }
        header('Content-Type: application/json');
        $retval=[];
        $retval = [
            "ok"=>true,
            "materias"=>$materias,
            
        ];
        header('Content-Type: application/json');
        echo json_encode($retval);
        exit;
    }

    if(isset($_REQUEST["fetchUnidades"])){      
        $sql="select distinct(unidad) u from temas where materia_id='".$_REQUEST["fetchUnidades"]."' order by unidad";
        $res=$db->query($sql);
        $unidades="";
        while($row=$res->fetch_assoc()){
            $unidades.="<option value='".$row["u"]."'>".$row["u"]."</option>";
        }
        if($unidades==""){
            $unidades="<option value='-1' selected>Esta Materia aun no tiene unidades</option>";
        }
        header('Content-Type: application/json');
        $retval=[];
        $retval = [
            "ok"=>true,
            "unidades"=>$unidades,
            
        ];
        header('Content-Type: application/json');
        echo json_encode($retval);
        exit;
    }

    if(isset($_REQUEST["fetchTemas"])){      
        $sql="select * from temas where unidad='".$_REQUEST["unidad"]."'";
        $sql.=" and materia_id='".$_REQUEST["fetchTemas"]."' order by orden";
        $res=$db->query($sql);
        $temas="<option value='-1' selected>Seleccione el Tema...</option>";
        while($row=$res->fetch_assoc()){
            $temas.="<option value='".$row["tema_id"]."'>".$row["orden"].".- ".$row["titulo"]."</option>";
        }
        header('Content-Type: application/json');
        $retval=[];
        $retval = [
            "ok"=>true,
            "temas"=>$temas,
            
        ];
        header('Content-Type: application/json');
        echo json_encode($retval);
        exit;
    }

?>