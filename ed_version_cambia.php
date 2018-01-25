<?php 
/**
* ed_version_cambia.php
*
* aplicación para actualizar una versión candidata para la carga de archivos shapefile a una base de datos espacial
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

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificación de seguridad 
//include('./includes/conexion.php');

//if($_SERVER[SERVER_ADDR]=='192.168.0.252')ini_set('display_errors', '1');ini_set('display_startup_errors', '1');ini_set('suhosin.disable.display_errors','0'); error_reporting(-1);

// verificación de seguridad 
//include('./includes/conexion.php');
ini_set('display_errors', '1');

session_start();

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");
include("./includes/pgqonect.php");
include_once("./usu_validacion.php");
$Usu = validarUsuario(); // en ./usu_valudacion.php

require_once('./classes/php-shapefile/src/ShapeFileAutoloader.php');
\ShapeFile\ShapeFileAutoloader::register();
// Import classes
use \ShapeFile\ShapeFile; 
use \ShapeFile\ShapeFileException;

$ID = isset($_GET['id'])?$_GET['id'] : '';

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	

$Log['data']=array();
$Log['tx']=array();
$Log['mg']=array();
$Log['res']='';
function terminar($Log){
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	echo $res;
	exit;	
}


if(!isset($_POST['tabla'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para guardar una versión. Consulte al administrador');
	$Log['tx'][]='error, no se recibió la variable tabla';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['accion'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para guardar una versión. Consulte al administrador');
	$Log['tx'][]='error, no se recibió la variable tabla';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['id'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para guardar una versión. Consulte al administrador');
	$Log['tx'][]='error, no se recibió la variable id de varsión';
	$Log['res']='err';
	terminar($Log);	
}


$query="
	SELECT table_name FROM information_schema.tables 
	WHERE table_schema = 'geogec' and table_name = '".$_POST['tabla']."' 
	order by table_name
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
if(pg_num_rows($ConsultaProy)<1){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='no se encontró la tabla solicitada en la base de datos';
	$Log['res']='err';
	terminar($Log);	
}


if($Usu['acc']['est']['gral']<3){
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='no cuenta con permisos para generar una nueva versión de una capa estructural de la plataforma geoGEC';
	$Log['res']='err';
	terminar($Log);	
}



$query="
SELECT 
	*
  FROM geogec.sis_versiones
  WHERE 
  		tabla = '".$_POST['tabla']."' 
  	AND
 	 	zz_borrada = '0'
  	AND
 	 	publicada = '0'
  	AND
  		usu_autor = '".$Usu['datos']['id']."'
  	AND
  		id = '".$_POST['id']."'
 ";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if(pg_num_rows($ConsultaVer)<0){
	$Log['mg'][]='error interno no se encontro la versión con el id enviado';
	$Log['res']='err';
	terminar($Log);	
}


$query="
UPDATE geogec.sis_versiones
   SET 
       instrucciones='".$_POST['instrucciones']."', fi_prj='".$_POST['fi_prj']."'
 WHERE 
 	id='".$_POST['id']."'
";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}


$Log['res']='exito';
terminar($Log);		
?>


