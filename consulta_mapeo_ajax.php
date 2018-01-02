<?php 
/**
* actividades_consulta.php
*
* aplicación que consulta el listado de actividades presentadas.
* 
* @package    	Plataforma Colectiva de Información Territorial: UBATIC2014
* @subpackage 	BASE
* @author     	Universidad de Buenos Aires
* @author     	<mario@trecc.com.ar>
* @author    	http://www.uba.ar/
* @author    	http://www.trecc.com.ar/recursos/proyectoubatic2014.htm
* @author		based on TReCC SA Procesos Participativos Urbanos, development. www.trecc.com.ar/recursos
* @copyright	2015 Universidad de Buenos Aires
* @copyright	esta aplicación se desarrollo sobre una publicación GNU 2014 TReCC SA
* @license    	http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 (GPL-3.0)
* Este archivo es parte de TReCC(tm) paneldecontrol y de sus proyectos hermanos: baseobra(tm), TReCC(tm) intraTReCC  y TReCC(tm) Procesos Participativos Urbanos.
* Este archivo es software libre: tu puedes redistriburlo 
* y/o modificarlo bajo los términos de la "GNU General Public License" 
* publicada por la Free Software Foundation, version 3
* 
* Este archivo es distribuido por si mismo y dentro de sus proyectos 
* con el objetivo de ser útil, eficiente, predecible y transparente
* pero SIN NIGUNA GARANTÍA; sin siquiera la garantía implícita de
* CAPACIDAD DE MERCANTILIZACIÓN o utilidad para un propósito particular.
* Consulte la "GNU General Public License" para más detalles.
* 
* Si usted no cuenta con una copia de dicha licencia puede encontrarla aquí: <http://www.gnu.org/licenses/>.
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
include_once('./includes/conexionesmysql.php');
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
if(pg_num_rows($ConsultaProy)<1){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='no se encontró el proyecto solicitado en la base de datos';
	$Log['res']='err';
	terminar($Log);	
}
while($fila=pg_fetch_assoc($ConsultaProy)){	
	if($fila['zz_codacc']!=$_POST['cp']&&$_SESSION["UsuarioI"]<1){
		$Log['tx'][]='error: validacion de la consulta fallida';
		$Log['mg'][]=utf8_encode('Ha fallado la valiadación de acceso. O bien ha caducado el período de publicación, o bien no ceunta con permisos de visualización de esta información. O bien a caducado la conexió nde su usuario interno.');
		$Log['res']='err';
		terminar($Log);		
	}
}	


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
if(pg_num_rows($ConsultaLink)==0){
	$Log['tx'][]='error: no se encontro el proyecto'.$_POST['id'];
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);	
}

$where="";

while($fila=pg_fetch_assoc($ConsultaLink)){
	$Proy['TS'][$fila['id_p_proproyectos']]=$fila['id_p_proproyectos'];
	$where.=" id = '".$fila['id_p_proproyectos']."' OR";
}
$where=substr($where, 0,-3);

$query = "
	SELECT 
		`PROproyectos`.`id`,
	    `PROproyectos`.`nombre`,
	    `PROproyectos`.`celda`,
	    `PROproyectos`.`zz_AUTOCREADOR`,
	    `PROproyectos`.`zz_AUTOFECHACREACION`,
	    `PROproyectos`.`grillas`,
	    `PROproyectos`.`zz_reticula`,
	    `PROproyectos`.`zz_minx`,
	    `PROproyectos`.`zz_maxx`,
	    `PROproyectos`.`zz_miny`,
	    `PROproyectos`.`zz_maxy`
	FROM `TReCCsound`.`PROproyectos`
	WHERE $where
";
$ConsultaMap = mysql_query($query,$Conec1);

if(mysql_error($Conec1)!=''){
	$Log['tx'][]='error: '.mysql_error($Conec1);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
if(mysql_num_rows($ConsultaMap)==0){
	$Log['tx'][]='error: no se encontro el proyecto'.$_POST['id'];
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);	
}
	
while($fila=mysql_fetch_assoc($ConsultaMap)){

	if(
		  $fila['zz_minx']==''
		||$fila['zz_maxx']==''
		||$fila['zz_miny']==''
		||$fila['zz_maxy']==''
	){
		
		$query="
			SELECT id, id_p_proproyectos, lat, lon
			FROM treccsound.valores
			where id_p_proproyectos = '".$fila['id']."'
		";
		$ConsultaVals = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['res']='err';
			terminar($Log);
		}
		
		while($ffila=pg_fetch_assoc($ConsultaVals)){			
			if(!isset($xmin)){$xmin=$ffila['lon'];}
			if(!isset($xmax)){$xmax=$ffila['lon'];}
			if(!isset($ymin)){$ymin=$ffila['lat'];}
			if(!isset($ymax)){$ymax=$ffila['lat'];}
			
			$xmin=min($xmin,$ffila['lon']);
			$xmax=max($xmax,$ffila['lon']);
			$ymin=min($ymin,$ffila['lat']);
			$ymax=max($ymax,$ffila['lat']);			
		}
		
		
		$query="
			UPDATE
				TReCCsound`.`PROproyectos`
			SET
				`zz_minx` = '".$xmin."',
				`zz_maxx` = '".$xmax."',
				`zz_miny` = '".$ymin."',
				`zz_maxy` = '".$ymax."'
			WHERE `id` = '".$fila['id']."'
		";
		$ConsultaVals = mysql_query($query, $Conec1);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.mysql_error($Conec1);
			$Log['tx'][]='query: '.$query;
			$Log['res']='err';
			terminar($Log);
		}	
		
		$fila['zz_minx']=$xmin;
		$fila['zz_maxx']=$xmax;
		$fila['zz_miny']=$ymin;
		$fila['zz_maxy']=$ymax;
		
		unset($xmin);
		unset($xmax);
		unset($ymin);
		unset($ymax);
	}
	
	foreach($fila as $k => $v){
		$Mapas[$fila['id']][$k]=utf8_encode($v);
	}
}

$where='';
foreach($Mapas as $idppr =>$v){
	$where .= " id_p_PROproyectos = $idppr OR";
}
$Log['tx'][]=$where;
$where = substr($where, 0 , -3);
$query = "
SELECT 
	`VARestados`.`id`,
    `VARestados`.`documento`,
    `VARestados`.`esig`,
    `VARestados`.`eraster`,
    `VARestados`.`eraster_color`,
    `VARestados`.`estado`,
    `VARestados`.`id_p_PROproyectos`,
    `VARestados`.`id_p_VARvariables_nombre`,
    `VARestados`.`id_p_VARperiodos_nombre`,
    `VARestados`.`id_p_VARalturas_altura`,
    `VARestados`.`id_p_VARescenarios_nombre`,
    `VARestados`.`resultadoOk`,
    `VARestados`.`resultadoNo`,
    `VARestados`.`resultado`,
    `VARestados`.`desde`
FROM `TReCCsound`.`VARestados`
	WHERE $where
	order by `id_p_VARvariables_nombre`, `id_p_VARescenarios_nombre`, `id_p_VARperiodos_nombre`, `id_p_VARalturas_altura`
";
$ConsultaVar = mysql_query($query,$Conec1);

if(mysql_error($Conec1)!=''){
	$Log['tx'][]='error: '.mysql_error($Conec1);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}
	$Log['tx'][]='query: '.$query;
while($row=mysql_fetch_assoc($ConsultaVar)){
	$var=$row['id_p_VARvariables_nombre'];
	$Log['tx'][]=
	$var.="_".$row['id_p_VARescenarios_nombre'];
	$var.="_".$row['id_p_VARperiodos_nombre'];
	$var.="_".$row['id_p_VARalturas_altura'];
	
	$Mapas[$row['id_p_PROproyectos']]['variables'][$var]=$row;
}

$Log['data']=$Mapas;
$Log['res']='exito';		
terminar($Log);

?>