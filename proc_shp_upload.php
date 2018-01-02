<?php
/**
* proc_shp_upload.php
*
* genera carga archivos shapefile en una carpeta asignada por n´çumero de id
 * 
 *  
* @package    	TReCC - Mapa Visualizador de variables Ambientales. 
* @subpackage 	proyecto
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar>
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2018 TReCC SA
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2014 TReCC SA - http://www.trecc.com.ar/recursos/proyectoppu.htm
* @license    	http://www.gnu.org/licenses/agpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 
* Este archivo es parte de TReCC(tm) paneldecontrol y de sus proyectos hermanos: baseobra(tm), TReCC(tm) intraTReCC  y TReCC(tm) Procesos Participativos Urbanos.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* Es decir, que debes mantener referencias a la publicación original y publicar las nuevas versiones deribadas. 
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU AFFERO GENERAL PUBLIC LICENSE" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
* 
*
*/

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");

// función de consulta de proyectoes a la base de datos 
include("./consulta_mediciones.php");
include("./includes/encabezado.php");
include("./includes/usuarioaccesos.php");


function terminar($Log){
	$res=json_encode($Log);
	if($res==''){
		echo "Error al codificar en json el resultado".PHP_EOL;		
		print_r($Log);
	}
	else{
		echo $res;
	}
	exit;	
}



$Log['data']['ncont']=$_POST['cont'];

if(!isset($_POST['idver'])){
	$Log['res']='error';
	$Log['tx'][]='falta id de version';	
	terminar($Log);
}			


$query="
SELECT 
	*
  FROM treccsound.sis_versiones
  WHERE 
  		id='".$_POST['idver']."'
  	AND
 	 	zz_borrada = '0'
  	AND
 	 	publicada = '0'
  	AND
  		usu_autor = '".$Usu['datos']['id']."'
 ";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}




$fila=pg_fetch_assoc($ConsultaVer);

if($fila['zz_borrada']=='1'){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='esta versión figura como borrada. no puede proseguir';
	$Log['res']='err';
	terminar($Log);	
}
if($fila['publicada']=='1'){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='esta versión ya figura como publicada. no puede proseguir';
	$Log['res']='err';
	terminar($Log);	
}
if($fila['usu_autor']!=$Usu['datos']['id']){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='usted no figura como el autor de esta versión: '.$Usu['datos']['id'];
	$Log['res']='err';
	terminar($Log);	
}

$Hoy_a = date("Y");
$Hoy_m = date("m");	
$Hoy_d = date("d");
$HOY = date("Y-m-d");
	


$carpeta='./documentos/subidas/ver/'.str_pad($_POST['idver'],8,"0",STR_PAD_LEFT);
if(!file_exists($carpeta)){
	$Log['tx'][]="creando carpeta $carpeta";mkdir($carpeta, 0777, true);chmod($carpeta, 0777);	
}


$nombre = $_FILES['upload']['name'];
$Log['data']['nombreorig']=$nombre;
$b = explode(".",$nombre);
$extO =$b[(count($b)-1)];
$ext = strtolower($extO);
$nombreSinExt=str_replace(".".$extO, "", $nombre);

$extVal['prj']='1';
$extVal['qpj']='1';
$extVal['dbf']='1';
$extVal['shp']='1';
$extVal['shx']='1';
$extVal['cpg']='1';


if(!isset($extVal[strtolower($ext)])){	
	$Log['tx'][]="solo se aceptan los formatos:";
	foreach($extVal as $k => $v){$Log['tx'][]=" $k,";}
	$Log['tx'][]="archivo cargado: ".$nombre;
	$Log['res']='err';
	terminar($Log);
}


$Log['tx'][]="cargando archivo en modo shapefile.";
$Log['data']['cargado']='shapefile parcial';

$fn = $nombre;	
$nom=substr($fn,0,-4);
$nom=str_replace("-","_",$nom);
$nom=str_replace(" ","_",$nom);
$e=explode("_",$nom);

$nuevonombre= $carpeta.'/'.$nombreSinExt.'.'.strtolower($ext);
//echo "<br>var:".$e[0] ."esc:".$e[1]."per:".$e[2].alt:".$e[3];	

if (!copy($_FILES['upload']['tmp_name'], $nuevonombre)){
    $Log['tx'][]="Error al copiar $nuevonombre";
	$Log['res']="err";
	terminar($Log);
}

$Log['tx'][]="copiado archivo punto ".strtolower($ext);
$Log['res']="exito";
terminar($Log);