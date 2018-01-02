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




$Log['data']=array();
$Log['tx']=array();
$Log['mg']=array();
$Log['res']='';
function terminar($Log){
	$js=json($Log);
	if($js==''){print_r($Log);}else{
		echo $js;
	}	
}

$geotable="base_localizaciones";
$geomfield="locpg98f5";
 
function escapeJsonString($value) {
  $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
  $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
  $result = str_replace($escapers, $replacements, $value);
  return $result;
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
		  base_localizaciones.id,
		  base_localizaciones_link_proyectos.nombre, 
		  base_localizaciones.locpg98f5, 
		  base_localizaciones.descripcion,
		  st_asgeojson(".$geomfield.") AS geojson,
		  ST_Contains(caba.geom,locpg98f5) as encaba
		FROM 
		  treccsound.base_localizaciones, 
		  treccsound.base_localizaciones_link_proyectos, 
		  treccsound.base_proyectos,
		  (select geom from treccsound.base_caba) as caba
		WHERE 
		  base_localizaciones.id = base_localizaciones_link_proyectos.id_p_base_localizaciones AND
		  base_localizaciones_link_proyectos.id_p_base_proyectos = '".$_POST['id']."'
		  ;
	";
}else{
	$query="
		SELECT 
		*,
		st_asgeojson(".$geomfield.") AS geojson 
		FROM treccsound.$geotable
	";
}
$ConsultaLocalizaciones = pg_query($ConecSIG, $query);





if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	echo $query;
	$Log['res']='err';
	terminar($Log);
}

	
	
# Build GeoJSON
$output    = '';
$rowOutput = '';

while ($row = pg_fetch_assoc($ConsultaLocalizaciones)) {
    $rowOutput = (strlen($rowOutput) > 0 ? ',' : '') . '{"type": "Feature", "geometry": ' . $row['geojson'] . ', "properties": {';
    $props = '';
    $id    = '';
	$props .= '"aaa":"hola"';
	
	if(!isset($row['nombre'])){$row['nombre']="(".$row['id'].")";}
    foreach ($row as $key => $val) {
        if ($key != "geojson") {
            //$props .= (strlen($props) > 0 ? ',' : '') . '"' . $key . '":"' . escapeJsonString($val) . '"';
        }
		
        if ($key == "id") {
            $id .= '"id":"' . escapeJsonString($val) . '"';
        }

        if ($key == "nombre") {
            $nombre = '"nombre":"' . escapeJsonString($val) . '"';
        }
        
        if ($key == "descripcion") {
            $descripcion = '"descripcion":"' . escapeJsonString($val) . '"';
        }   
		if ($key == "encaba") {
            $descripcion = '"encaba":"' . escapeJsonString($val) . '"';
        } 
		  		
    }
	
	$selecto= '"selecto":"no"';
	if(isset($SELECCION)){
		if(isset($SELECCION[$row['id']])){$selecto= '"selecto":"si"';}
	}
    
    $rowOutput .= $id.", ".$nombre."," . $selecto."," . $descripcion.'}, ';	
    $rowOutput .= $id ;

    $rowOutput .= '}';
    $output .= $rowOutput;
}
$output = '{"res":"exito","data":{"type": "FeatureCollection",  "crs": {"type": "name", "properties": {"name": "EPSG:22175"}},"features": [ ' . $output . ' ]}}';
echo $output;
?>