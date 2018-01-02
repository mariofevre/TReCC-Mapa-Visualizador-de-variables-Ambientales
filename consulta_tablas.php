<?php 
/**
* consulta_tablas.php
*
* aplicación para visualizar datos ambientales en un mapa
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
ini_set('display_errors', '1');

session_start();

// funciones frecuentes
include_once("./includes/ApplicationSettings.php");	
session_start();
include_once('./includes/mySqonect.php');	
include_once('./includes/cadenas.php');				
include_once('./includes/fechas.php');	
include('./consulta_mediciones.php');
include('./consulta_proyectos.php');
include('./consulta_localizaciones.php');
include('./consulta_relevamientos.php');


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



$query="
	SELECT table_name FROM information_schema.tables 
	WHERE table_schema = 'treccsound'
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
	$Log['mg'][]='no se encontró el proyecto solicitado en la base de datos';
	$Log['res']='err';
	terminar($Log);	
}

while($fila=pg_fetch_assoc($ConsultaProy)){
	$pre=substr($fila['table_name'],0,3);
	if($pre=='sis'){continue;}
	$Log['data']['tablas'][$pre][]=$fila['table_name'];
}	

$Log['res']='exito';
terminar($Log);		
?>


