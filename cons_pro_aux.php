<?php
ini_set('display_errors', '1');
// carga conexxion a baseSIG
//include('./includes/conexion.php');
//include('./includes/encabezado.php');
header("Cache-control: private");	
include_once("./includes/ApplicationSettings.php");	
session_start();
include_once('./includes/mySqonect.php');	

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");

$Log['res']='';
$Log['tx']=array();
$Log['mg']=array();
$Log['data']=array();

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



if(!isset($_POST['id'])){$_POST['id']=0;}
if($_POST['id']>0){

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
		SELECT 
		  base_localizaciones.id
		  
		FROM 
		  treccsound.base_localizaciones, 
		  treccsound.base_localizaciones_link_proyectos
		 
		WHERE 
		  base_localizaciones.id = base_localizaciones_link_proyectos.id_p_base_localizaciones AND
		  base_localizaciones_link_proyectos.id_p_base_proyectos = '".$_POST['id']."'
		  ;
	";
}else{
	if($_SESSION["UsuarioI"]<1){
		$Log['tx'][]='error: validacion de la consulta fallida';
		$Log['mg'][]=utf8_encode('Ha fallado la valiadación de acceso. O bien ha caducado el período de publicación, o bien no ceunta con permisos de visualización de esta información. O bien a caducado la conexió nde su usuario interno.');
		$Log['res']='err';
		terminar($Log);		
	}
}
$ConsultaLocalizaciones = pg_query($ConecSIG, $query);

if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
}


$where='';
while ($row = pg_fetch_assoc($ConsultaLocalizaciones)) {
 	$where.="id_p_base_localizaciones = '".$row['id']."' OR ";
}
$where = substr($where,0,-3);

$query="
	SELECT 
	id, info,
	ST_AsText(geo) geo, 
	id_p_base_localizaciones
	FROM treccsound.base_loc_aux_lineas
	WHERE $where
 ";
//echo $query;
$ConsultaLocalizaciones  = pg_query($ConecSIG, $query);	
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
}

$enlimipio=array();
while($fila=pg_fetch_assoc($ConsultaLocalizaciones)){
	if(isset($enlimipio[$fila['geo']])){continue;}
	$enlimipio[$fila['geo']]='set';
	$Log['data']['aux'][]=$fila;		
}	


$Log['res']='exito';
terminar($Log);	
?>