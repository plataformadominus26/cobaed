<?php 

function right($value, $count){return substr($value, ($count*-1));}
function left($string, $count){return substr($string, 0, $count);}


function getBooks($token){ 
    global $db;
    $sql="select a.book_id,trim(b.grupo),b.* from usuarios a,usuarios b ";
    $sql.=" where (a.token='$token') and (a.book_id=b.usuario_id)";
    dump($sql,"beneficiarios.txt");
    $row=$db->query($sql)->fetch_assoc();
    $books=[];
    $books["book"]=$row["book_id"];
    $books["estado"]=$row["estado_id"];

    if($row["grupo"]){
        $sql="select book_id from usuarios where share_key='".$row["grupo"]."'";
        $books["grupo"]=$row=$db->query($sql)->fetch_assoc()?$row["book_id"]:-1;
    }
    $books["books"]=$row["grupo"]?$books["book"].",".$books["grupo"]:$books["book"];
    dump(json_encode($books),"beneficiarios.txt","a");
    return $books;
}

function book($db,$token=0){
    $sql="select * from usuarios where token='".$_REQUEST["token"]."'";
    $row=$db->query($sql)->fetch_assoc();
    return $token?$row["token"]:$row["book_id"];
}
function nocache(){
 	header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");}

function createRandomKey($length) {
    // Get current date and time components
    $year = dechex(date('y'));
    $month = dechex(date('m'));
    $day = dechex(date('d'));
    $time = dechex(date('His'));
    // Generate random 4-digit number
    $random_digits = str_pad(dechex(mt_rand(0, 0xffff)), 4, '0', STR_PAD_LEFT);
    // Combine components to form the key
    $key = $year . $month . $day . $time . $random_digits;

    return strtoupper($key);
}
  
 
function dump($Data,$File= "dump.txt",$tipo='w'){
 
    $fecha=fechacorta(getdate(),1)." ".fechacorta(getdate(),10).PHP_EOL;
    $xData="Depurar --> ".$fecha.$Data.PHP_EOL; 
    $Data=$xData.PHP_EOL;
    if (!$File)	$File = "dump.txt"; 
       $Handle = fopen($File, $tipo);
       fwrite($Handle,$Data);  
       fclose($Handle); 
}


function ceros($x){
	return str_pad($x,2,"0", STR_PAD_LEFT); 
	}

   function fechacorta($xfecha,$tipo){ 
 
      $fecha=gettype($xfecha)=='string'?getdate(strtotime($xfecha)):$xfecha;	
     
        $tempo='';
        $_mes=Array(
      'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
        $_mes1=Array(
      'January','February','March','April','May','Junie','July','August','Septimber','October','November','December');
        $_dia=Array(
      'Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'); 
        switch($tipo){
         case 0: {$tempo=$fecha["mday"].' De '.$_mes[$fecha["mon"]-1].' de '.$fecha["year"];break;}
         case 1: {$tempo=$fecha["mday"].'/'.substr($_mes[$fecha["mon"]-1],0,3).'/'.$fecha["year"];break;}
         case 100: {$tempo=$fecha["mday"].'/'.substr($_mes[$fecha["mon"]-1],0,3);break;}
         case 101: {$tempo=substr($_mes[$fecha["mon"]-1],0,3)."-".$tempo=$fecha["mday"];break;}
         case 2: {$tempo=$fecha["year"].'-'.$fecha["mon"].'-'.$fecha["mday"];break;}
         case 3: {$tempo=$_dia[$fecha["wday"]].' '.$fecha["mday"].' de '.$_mes[$fecha["mon"]-1].' de '.$fecha["year"];break;}
         case 4: {$tempo=$fecha["mday"].'/'.$_mes[$fecha["mon"]-1].'/'.$fecha["year"];break;}
         case 5: {$tempo=$fecha["mday"].'/'.substr($_mes[$fecha["mon"]-1],0,3).'/'.($fecha["year"]-2000);break;}
         case 6: {$tempo=$fecha["year"].ceros($fecha["mon"]).ceros($fecha["mday"]);break;}
         case 7: {$tempo=$fecha["year"]."~".$fecha["mon"];break;}
         case 8: {$tempo=$fecha["year"]."-".ceros($fecha["mon"])."-".ceros($fecha["mday"]);break;}
         case 10: {$tempo=($fecha["hours"]).':'.ceros($fecha["minutes"]).":".ceros($fecha["seconds"]);break;}
         case 11: {$tempo=($fecha["hours"]).':'.ceros($fecha["minutes"]);break;}
         case 13: {$tempo=(ceros($fecha["hours"])).':'.ceros($fecha["minutes"]);break;}
         case 14: {$tempo=$fecha["mday"].'/'.substr($_mes[$fecha["mon"]-1],0,3)." ".$fecha["hours"] .':'.ceros($fecha["minutes"]).":".ceros($fecha["seconds"]);break;}
         case 104: {$tempo=$fecha["mday"].'/'.substr($_mes[$fecha["mon"]-1],0,3)." ".$fecha["hours"] .':'.ceros($fecha["minutes"]);break;}
         case 12: {
                  $ap=$fecha["hours"]>12?"P.M":"A.M";
                  $fecha["hours"]-=$fecha["hours"]>12?12:0;
                   $tempo=($fecha["hours"]).':'.ceros($fecha["minutes"])." ".$ap;break;}
        case 15:
            $tempo=$fecha["year"].ceros($fecha["mon"]).ceros($fecha["mday"]);
            $tempo.="_".ceros($fecha["hours"]).ceros($fecha["minutes"]).ceros($fecha["seconds"]);break;
           
           break;
        break;
        }
        return($tempo);
        }
function secuencia($campo,$tabla,$scope=''){
	if ($scope) $scope=' where '.$scope;
	$sql="select ifnull(max($campo),0)+1 x from $tabla $scope";
    $res=mysql_query($sql);
	$row=mysql_fetch_array($res);
	return $row["x"];
	}

function ejecuta($xsql,$llave=0){
   if ((!$llave)||(unLockIt($llave)))
      mysql_query($xsql);
	} 
	
	
function lockIt(){
  $tempo=createRandomKey(20);
  echo("<input type='hidden' id='qLck' value='$tempo' />");
  $sql="insert into locks values ('$tempo', date(now()))";
  mysql_query($sql);
  $sql="delete from locks where fecha<date(now())";
  mysql_query($sql);}
  

function unLockIt($x){
	$tempo=false;
	$sql="select * from locks where hash='$x'";
	$res=mysql_query($sql);
	if (mysql_fetch_array($res)){
		$tempo=true;
		$sql="delete from locks where hash='$x'";
    	mysql_query($sql);
		}
	return($tempo);
	}  
	
	
	
	
	/*spell*/
	
//------    CONVERTIR NUMEROS A LETRAS         ---------------
//------    Máxima cifra soportada: 18 dígitos con 2 decimales
//------    999,999,999,999,999,999.99 
// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE BILLONES 
// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE MILLONES 
// NOVECIENTOS NOVENTA Y NUEVE MIL NOVECIENTOS NOVENTA Y NUEVE PESOS 99/100 M.N.
//------    Creada por:                        ---------------
//------             ULTIMINIO RAMOS GALÁN     ---------------
//------            uramos@gmail.com           ---------------
//------    10 de junio de 2009. México, D.F.  ---------------
//------    PHP Version 4.3.1 o mayores (aunque podría funcionar en versiones anteriores, tendrías que probar)
function spell($xcifra)
{ 
$xarray = array(0 => "Cero",
1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE", 
"DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE", 
"VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA", 
100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
);
//
$xcifra = trim($xcifra);
$xlength = strlen($xcifra);
$xpos_punto = strpos($xcifra, ".");
$xaux_int = $xcifra;
$xdecimales = "00";
if (!($xpos_punto === false))
   {
   if ($xpos_punto == 0)
      {
      $xcifra = "0".$xcifra;
      $xpos_punto = strpos($xcifra, ".");
      }
   $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
   $xdecimales = substr($xcifra."00", $xpos_punto + 1, 2); // obtengo los valores decimales
   }

$XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
$xcadena = "";
for($xz = 0; $xz < 3; $xz++)
   {
   $xaux = substr($XAUX, $xz * 6, 6);
   $xi = 0; $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
   $xexit = true; // bandera para controlar el ciclo del While 
   while ($xexit)
      {
      if ($xi == $xlimite) // si ya llegó al límite máximo de enteros
         {
         break; // termina el ciclo
         }
   
      $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
      $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
      for ($xy = 1; $xy < 4; $xy++) // ciclo para revisar centenas, decenas y unidades, en ese orden
         {
         switch ($xy) 
            {
            case 1: // checa las centenas
               if (substr($xaux, 0, 3) < 100) // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                  {
                  }
               else
                  {
                  $xseek = $xarray[substr($xaux, 0, 3)]; // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                  if ($xseek)
                     {
                     $xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                     if (substr($xaux, 0, 3) == 100) 
                        $xcadena = " ".$xcadena." CIEN ".$xsub;
                     else
                        $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                     $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                     }
                  else // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                     {
                     $xseek = $xarray[substr($xaux, 0, 1) * 100]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                     $xcadena = " ".$xcadena." ".$xseek;
                     } // ENDIF ($xseek)
                  } // ENDIF (substr($xaux, 0, 3) < 100)
               break;
            case 2: // checa las decenas (con la misma lógica que las centenas)
               if (substr($xaux, 1, 2) < 10)
                  {
                  }
               else
                  {
                  $xseek = $xarray[substr($xaux, 1, 2)];
                  if ($xseek)
                     {
                     $xsub = subfijo($xaux);
                     if (substr($xaux, 1, 2) == 20)
                        $xcadena = " ".$xcadena." VEINTE ".$xsub;
                     else
                        $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                     $xy = 3;
                     }
                  else
                     {
                     $xseek = $xarray[substr($xaux, 1, 1) * 10];
                     if (substr($xaux, 1, 1) * 10 == 20)
                        $xcadena = " ".$xcadena." ".$xseek;
                     else  
                        $xcadena = " ".$xcadena." ".$xseek." Y ";
                     } // ENDIF ($xseek)
                  } // ENDIF (substr($xaux, 1, 2) < 10)
               break;
            case 3: // checa las unidades
               if (substr($xaux, 2, 1) < 1) // si la unidad es cero, ya no hace nada
                  {
                  }
               else
                  {
                  $xseek = $xarray[substr($xaux, 2, 1)]; // obtengo directamente el valor de la unidad (del uno al nueve)
                  $xsub = subfijo($xaux);
                  $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                  } // ENDIF (substr($xaux, 2, 1) < 1)
               break;
            } // END SWITCH
         } // END FOR
         $xi = $xi + 3;
      } // ENDDO

      if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
         $xcadena.= " DE";
         
      if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
         $xcadena.= " DE";
      
      // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
      if (trim($xaux) != "")
         {
         switch ($xz)
            {
            case 0:
               if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                  $xcadena.= "UN BILLON ";
               else
                  $xcadena.= " BILLONES ";
               break;
            case 1:
               if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                  $xcadena.= "UN MILLON ";
               else
                  $xcadena.= " MILLONES ";
               break;
            case 2:
               if ($xcifra < 1 )
                  {
                  $xcadena = "CERO PESOS $xdecimales/100 M.N.";
                  }
               if ($xcifra >= 1 && $xcifra < 2)
                  {
                  $xcadena = "UN PESO $xdecimales/100 M.N. ";
                  }
               if ($xcifra >= 2)
                  {
                  $xcadena.= " PESOS $xdecimales/100 M.N. "; // 
                  }
               break;
            } // endswitch ($xz)
         } // ENDIF (trim($xaux) != "")
      // ------------------      en este caso, para México se usa esta leyenda     ----------------
      $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
      $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles 
      $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
      $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles 
      $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
      $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
      $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
   } // ENDFOR ($xz)
   return trim($xcadena);
} // END FUNCTION



function spell1($xcifra)
{ 
$xarray = array(0 => "Cero",
1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE", 
"DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE", 
"VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA", 
100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
);
//
$xcifra = trim($xcifra);
$xlength = strlen($xcifra);
$xpos_punto = strpos($xcifra, ".");
$xaux_int = $xcifra;
$xdecimales = "00";
if (!($xpos_punto === false))
   {
   if ($xpos_punto == 0)
      {
      $xcifra = "0".$xcifra;
      $xpos_punto = strpos($xcifra, ".");
      }
   $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
   $xdecimales = substr($xcifra."00", $xpos_punto + 1, 2); // obtengo los valores decimales
   }

$XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
$xcadena = "";
for($xz = 0; $xz < 3; $xz++)
   {
   $xaux = substr($XAUX, $xz * 6, 6);
   $xi = 0; $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
   $xexit = true; // bandera para controlar el ciclo del While 
   while ($xexit)
      {
      if ($xi == $xlimite) // si ya llegó al límite máximo de enteros
         {
         break; // termina el ciclo
         }
   
      $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
      $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
      for ($xy = 1; $xy < 4; $xy++) // ciclo para revisar centenas, decenas y unidades, en ese orden
         {
         switch ($xy) 
            {
            case 1: // checa las centenas
               if (substr($xaux, 0, 3) < 100) // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                  {
                  }
               else
                  {
                  $xseek = $xarray[substr($xaux, 0, 3)]; // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                  if ($xseek)
                     {
                     $xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                     if (substr($xaux, 0, 3) == 100) 
                        $xcadena = " ".$xcadena." CIEN ".$xsub;
                     else
                        $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                     $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                     }
                  else // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                     {
                     $xseek = $xarray[substr($xaux, 0, 1) * 100]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                     $xcadena = " ".$xcadena." ".$xseek;
                     } // ENDIF ($xseek)
                  } // ENDIF (substr($xaux, 0, 3) < 100)
               break;
            case 2: // checa las decenas (con la misma lógica que las centenas)
               if (substr($xaux, 1, 2) < 10)
                  {
                  }
               else
                  {
                  $xseek = $xarray[substr($xaux, 1, 2)];
                  if ($xseek)
                     {
                     $xsub = subfijo($xaux);
                     if (substr($xaux, 1, 2) == 20)
                        $xcadena = " ".$xcadena." VEINTE ".$xsub;
                     else
                        $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                     $xy = 3;
                     }
                  else
                     {
                     $xseek = $xarray[substr($xaux, 1, 1) * 10];
                     if (substr($xaux, 1, 1) * 10 == 20)
                        $xcadena = " ".$xcadena." ".$xseek;
                     else  
                        $xcadena = " ".$xcadena." ".$xseek." Y ";
                     } // ENDIF ($xseek)
                  } // ENDIF (substr($xaux, 1, 2) < 10)
               break;
            case 3: // checa las unidades
               if (substr($xaux, 2, 1) < 1) // si la unidad es cero, ya no hace nada
                  {
                  }
               else
                  {
                  $xseek = $xarray[substr($xaux, 2, 1)]; // obtengo directamente el valor de la unidad (del uno al nueve)
                  $xsub = subfijo($xaux);
                  $xcadena = " ".$xcadena." ".$xseek." ".$xsub;
                  } // ENDIF (substr($xaux, 2, 1) < 1)
               break;
            } // END SWITCH
         } // END FOR
         $xi = $xi + 3;
      } // ENDDO

      if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
         $xcadena.= " DE";
         
      if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
         $xcadena.= " DE";
      
      // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
      if (trim($xaux) != "")
         {
         switch ($xz)
            {
            case 0:
               if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                  $xcadena.= "UN BILLON ";
               else
                  $xcadena.= " BILLONES ";
               break;
            case 1:
               if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                  $xcadena.= "UN MILLON ";
               else
                  $xcadena.= " MILLONES ";
               break;
            
            } // endswitch ($xz)
         } // ENDIF (trim($xaux) != "")
      // ------------------      en este caso, para México se usa esta leyenda     ----------------
      $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
      $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles 
      $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
      $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles 
      $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
      $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
      $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
   } // ENDFOR ($xz)
   $xcadena=$xcadena=="UN"?"UNO":$xcadena;
   return trim($xcadena);
} // END FUNCTION


function subfijo($xx) 
{ // esta función regresa un subfijo para la cifra
	$xx = trim($xx);
	$xstrlen = strlen($xx);
	if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
	  $xsub = "";
	// 
	if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
	  $xsub = "MIL";
	//
	return $xsub;
} // END FUNCTION

function pesos($x){
	return(money_format('%#10n', $x)); 
}

function config($db,$x,$numerico){
	$sql="select * from config where nombre='$x'";
	$row=$db->query($sql)->fetch_assoc();
	if($numerico)
	  return($row["valorn"]);
	else
	  return($row["valors"]);
	  
	}


function checkDir($x){
    $x=explode("/",$x);
    $tempo="";
    for($i=0;$i<count($x);$i++){
        $tempo.=$x[$i]."/";
        if ($x[$i]!="."&&$x[$i]!=".."&!is_dir($tempo)) mkdir($tempo);
    }

}
?>