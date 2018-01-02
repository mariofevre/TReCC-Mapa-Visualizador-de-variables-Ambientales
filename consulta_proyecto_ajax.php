<?php 
/**
* consulta_proyecto_ajax.php
*
* devuelve datos de un proyecto espec�fico solicitado.
 * 
 *  
* @package    	TReCC - Mapa Visualizador de variables Ambientales. 
* @subpackage 	proyecto
* @author     	TReCC SA
* @author     	<mario@trecc.com.ar>
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2018 TReCC SA
* @copyright	esta aplicaci�n se desarrollo sobre una publicaci�n GNU 2014 TReCC SA - http://www.trecc.com.ar/recursos/proyectoppu.htm
* @license    	http://www.gnu.org/licenses/agpl.html GNU AFFERO GENERAL PUBLIC LICENSE, version 3 
* Este archivo es parte de TReCC(tm) paneldecontrol y de sus proyectos hermanos: baseobra(tm), TReCC(tm) intraTReCC  y TReCC(tm) Procesos Participativos Urbanos.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los t�rminos de la "GNU AFFERO GENERAL PUBLIC LICENSE" 
* publicada por la Free Software Foundation, version 3
* Es decir, que debes mantener referencias a la publicaci�n original y publicar las nuevas versiones deribadas. 
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser �til, eficiente, predecible y transparente
* pero SIN NIGUNA GARANT�A; sin siquiera la garant�a impl�cita de
* CAPACIDAD DE MERCANTILIZACI�N o utilidad para un prop�sito particular.
* Consulte la "GNU AFFERO GENERAL PUBLIC LICENSE" para m�s detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aqu�: <http://www.gnu.org/licenses/>.
* 
*
*/

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

ini_set('display_errors', '1');
header("Cache-control: private");
include_once("./includes/ApplicationSettings.php");	
session_start();
include_once('./includes/mySqonect.php');	
include_once('./includes/cadenas.php');				
include_once('./includes/fechas.php');	
include('./consulta_mediciones.php');
include('./consulta_proyectos.php');
include('./consulta_localizaciones.php');
include('./consulta_relevamientos.php');


if(!isset($_POST['id'])){
	$Log['tx'][]='falta variable id';
	$Log['res']='err';
	terminar($Log);
}


//consulta categorias utilizadas para la actividad seleccionada
if($ID!=''){$where = " AND id = '".$ID."'";}else{$where='';}

$query="
	SELECT id, nombre, descripcion, fecha, zz_codacc
	FROM treccsound.base_proyectos
	WHERE zz_borrada!='1'
	AND id = '".$_POST['id']."'	
	ORDER BY fecha desc, id desc
";
$ConsultaProy = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}		
	//$Log['tx'][]='query: '.$query;	
	
$query="		
	SELECT id, id_p_base_proyectos, id_p_base_mediciones
 	 FROM treccsound.base_link_proyectos_mediciones
 	 WHERE id_p_base_proyectos = '".$_POST['id']."'
";

$ConsultaLink = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
			
while($fila=pg_fetch_assoc($ConsultaProy)){
	
	if($fila['zz_codacc']!=$_POST['cp']&&$_SESSION["UsuarioI"]<1){
		$Log['tx'][]='error: validacion de la consulta fallida';
		$Log['mg'][]=utf8_encode('Ha fallado la valiadaci�n de acceso. O bien ha caducado el per�odo de publicaci�n, o bien no ceunta con permisos de visualizaci�n de esta informaci�n. O bien a caducado la conexi� nde su usuario interno.');
		$Log['res']='err';
		terminar($Log);		
	}
	
	$Proy=$fila;
	$Proy['TS']=array();
}

$Log['tx'][]=$Proy['TS'];
$query="		
	SELECT id, id_p_base_proyectos, id_p_proproyectos
	FROM treccsound.base_proyectos_proproyectos
	WHERE id_p_base_proyectos = '".$_POST['id']."'
";

$ConsultaLink = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
	
while($fila=pg_fetch_assoc($ConsultaLink)){
	$Proy['TS'][$fila['id_p_proproyectos']]=$fila['id_p_proproyectos'];
}

$Log['data']=$Proy;
$Log['res']='exito';		
terminar($Log);

?>