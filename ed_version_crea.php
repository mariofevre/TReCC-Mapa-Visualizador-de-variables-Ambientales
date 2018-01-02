<?php 
/**
* ed_version_crea.php
*
* aplicación para generar una versión candidata para la carga de archivos shapefile a una base de datos espacial
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
// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");

// función de consulta de proyectoes a la base de datos 
include("./consulta_mediciones.php");
include("./includes/encabezado.php");
include("./includes/usuarioaccesos.php");
$Usu['datos']=usuarioaccesos();

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
	global $PROCESANDO;
	$res=json_encode($Log);
	if($res==''){$res=print_r($Log,true);}
	if(isset($PROCESANDO)){
		return;	
	}else{
		echo $res;
		exit;
	}	
}


if(!isset($_POST['tabla'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para crear una nueva versión. Consulte al administrador');
	$Log['tx'][]='error, no se recibió la variable tabla';
	$Log['res']='err';
	terminar($Log);	
}

if(!isset($_POST['accion'])){
	$Log['mg'][]=utf8_encode('error en las variables enviadas para crear una nueva versión. Consulte al administrador');
	$Log['tx'][]='error, no se recibió la variable tabla';
	$Log['res']='err';
	terminar($Log);	
}

$query="
	SELECT table_name FROM information_schema.tables 
	WHERE table_schema = 'treccsound' and table_name = '".$_POST['tabla']."' 
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
	$Log['mg'][]='no cuenta con permisos para generar una nueva versión de una capa estructural de treccsound';
	$Log['res']='err';
	terminar($Log);	
}



$query="
SELECT *
FROM information_schema.columns
WHERE table_schema = 'treccsound'
  AND table_name   = '".$_POST['tabla']."'
 ";  
 $ConsultaTabl = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['res']='err';
	terminar($Log);
} 
while($fila=pg_fetch_assoc($ConsultaTabl)){
	$Log['data']['columnas'][$fila['column_name']]=$fila['data_type'];
}


$query="
SELECT 
	*
  FROM treccsound.sis_versiones
  WHERE 
  		tabla = '".$_POST['tabla']."' 
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

if(pg_num_rows($ConsultaVer)>0){
	$Log['tx'][]=utf8_encode('usted ya cuenta con una versión en proceso para esta capa');
	$Log['mg'][]=utf8_encode('usted ya cuenta con una versión en proceso para esta capa');
	
	$Log['data']['version']=pg_fetch_assoc($ConsultaVer);
	
	$carpeta='./documentos/subidas/ver/'.str_pad($Log['data']['version']['id'],8,"0",STR_PAD_LEFT);
	$dir=scandir($carpeta);	
	$Log['data']['archivos']=array();
	foreach($dir as $v){
		if($v=='..'){continue;}
		if($v=='.'){ continue;}
		
		$a['nom']=$v;
		
		$e=explode('.',$v);
		$ext=$e[(count($e)-1)];
		$a['ext']=$ext;
		
		$Log['data']['archivos'][]=$a;		
		
		$Log['data']['extarchivos'][$ext][]['nom']=$v;
	}


	$Log['data']['prj']['stat']='';
	$Log['data']['prj']['mg']='';
	$Log['data']['prj']['def']='';
	
	$SisRef[4326]='';
	$SisRef[3857]='';
	$SisRef[22171]='';
	$SisRef[22172]='';
	$SisRef[22173]='';
	$SisRef[22174]='';
	$SisRef[22175]='';
	$SisRef[22176]='';
	$SisRef[22177]='';
					
	$pj='';
	if(isset($Log['data']['extarchivos']['qpj'])){
		$pj=file_get_contents($carpeta.'/'.$Log['data']['extarchivos']['qpj'][0]['nom']);
	}elseif(isset($Log['data']['extarchivos']['prj'])){
		$pj=file_get_contents($carpeta.'/'.$Log['data']['extarchivos']['prj'][0]['nom']);
	}
	$Log['tx'][]=$pj;
	
	
	if($pj!=''){
		$t=explode(',',$pj);
		$final=",".$t[(count($t)-2)].",".$t[(count($t)-1)];
		$tf=explode('"',$final);
		if(strtoupper($tf[1])!='EPSG'){
			$Log['tx'][]='error: no reconocemos esta libreria de sistemas de referencia: '.strtoupper($tf[1]).' solo se admite EPSS';
			$Log['data']['extarchivos']['prj'][0]['mg']='libreria no reconocida';
			$Log['data']['extarchivos']['prj'][0]['stat']='inviable';
		}else{
			if(!isset($SisRef[$tf[3]])){
				$Log['tx'][]='error: no reconocemos esta proyeccion: '.$tf[3];
				$Log['data']['extarchivos']['prj'][0]['mg']='sistema de referencia no reconocida';
				$Log['data']['extarchivos']['prj'][0]['stat']='inviable';
				
				if($Log['data']['version']['fi_prj']!=''){
					$Log['data']['prj']['stat']='viable';
					$Log['data']['prj']['mg']='adoptado de base';
					$Log['data']['prj']['def']=$Log['data']['version']['fi_prj'];
				}
					
			}else{
				
				if($Log['data']['version']['fi_prj']==''){
					$Log['data']['prj']['stat']='viable';
					$Log['data']['prj']['mg']='adoptado de shp. Sin definición guardada en la base';
					$Log['data']['prj']['def']=$tf[3];
				}elseif($Log['data']['version']['fi_prj']==$tf[3]){
					$Log['data']['prj']['stat']='viable';
					$Log['data']['prj']['mg']='coincidente de shp y base';
					$Log['data']['prj']['def']=$tf[3];
				}else{
					$Log['data']['prj']['stat']='viableobs';
					$Log['data']['prj']['mg']='error. adoptado solo de la de base. '.$Log['data']['version']['fi_prj'].' vs '.$tf[3];
					$Log['data']['prj']['def']=$Log['data']['version']['fi_prj'];
				}
				
			}
		}
	}
	
	
	$Log['data']['shp']['stat']='';
	$Log['data']['shp']['mg']='';
		
	if(
		isset($Log['data']['extarchivos']['shx'])
		&&
		isset($Log['data']['extarchivos']['shp'])
		&&
		isset($Log['data']['extarchivos']['dbf'])
	){
		// Register autoloader
		
		
		try {
			
		    // Open shapefile
		    $ShapeFile = new ShapeFile($carpeta.'/'.$Log['data']['extarchivos']['shp'][0]['nom']);
			
			if($ShapeFile->valid()==1){
				$Log['tx'][]='shapefile valido: '+$ShapeFile->valid();
				$Log['data']['shp']['stat']='viable';	
				$Log['data']['shp']['cant']=$ShapeFile->getTotRecords();
				$Log['data']['shp']['tipo']=$ShapeFile->getShapeType(ShapeFile::FORMAT_STR);
				$Log['data']['shp']['mg']='reconocido '.$ShapeFile->getTotRecords(ShapeFile::FORMAT_STR).' registros '.$ShapeFile->getShapeType(ShapeFile::FORMAT_STR);
				$Log['tx'][]= get_class_methods($ShapeFile);


				$Log['data']['dbf']['campos']=$ShapeFile->getDBFFields();
				
				
				
				$instrucc=json_decode($Log['data']['version']['instrucciones'],true);
				$Log['data']['columnasCubiertas']=array();
				
				foreach($Log['data']['columnas'] as $tnom => $ttipo){
					
					$Log['data']['columnasCubiertas'][$tnom]['stat']='no';
					
					if($tnom=='id'||$tnom=='geo'||$tnom=='id_sis_versiones'){
						$Log['data']['columnasCubiertas'][$tnom]['stat']='si';
						$Log['data']['columnasCubiertas'][$tnom]['dbfref']='';
						$Log['data']['columnasCubiertas'][$tnom]['dbfnom']='';
					}
					
					foreach($Log['data']['dbf']['campos'] as $iddbf => $v){
						
						$cnom = $v['name'];
						if(isset($instrucc[$cnom])){
							$cnom=$instrucc[$v['name']]['nom'];
						}
						
						if($tnom==$cnom){
							$Log['data']['columnasCubiertas'][$tnom]['stat']='si';
							$Log['data']['columnasCubiertas'][$tnom]['dbfref']=$iddbf;
							$Log['data']['columnasCubiertas'][$tnom]['dbfnom']=$v['name'];
						}
					}
				
				}
				
				
				$Log['data']['dbf']['stat']='viable';
				$Log['data']['dbf']['mg']='';
				foreach($Log['data']['columnasCubiertas'] as $tn => $stat){
					if($stat['stat']!='si'){
						$Log['data']['dbf']['stat']='inviable';
						$Log['data']['dbf']['mg']+='no encontrado campo en shapefile para este campo en tabla '.$tn;
					}
				}
				
				
				
				/*
				foreach ($ShapeFile as $i => $record) {
					
					
					$geomTX = "ST_GeomFromText('".$record['shp']['wkt']."',".$Log['data']['prj']['def'].")";				
					$geomTX= "ST_Transform(".$geomTX.", 3857)";
					
					
					
					
					
					
					
					$query="
						INSERT INTO 
							trecc_ai.area_influencia(
								id_p_contrato, 
								geom, 
								nombre
							)
					    	VALUES (
					    		'".$_POST['idcont']."',
					    		".$geomTX.",
					    		'".$_POST['nombre']."'
					    		
					    	)
					    	
					    RETURNING id;
					    	";
							
					 *  
					   
					$Consulta = pg_query($ConecSIG,$query);
					
					$Log['tx'][]=$query;
					
					if(pg_errormessage($ConecSIG)!=''){
						$Log['res']='error';
						$Log['tx'][]='error al borrar base de datos';
						$Log['tx'][]=pg_errormessage($ConecSIG);
						$Log['tx'][]=$query;
						terminar($Log);	
					}
					
					
					$Log['data']['inserts'][]=$Consulta;
					
					
					$query="
					SELECT 
							ST_Transform(geom,".$_POST['crs'].", 3857) as geom
							
							FROM trecc_ai.area_influencia 
					    	WHERE id='".$Consulta."'
					   ";
					    	
					$Consulta = pg_query($ConecSIG,$query);
					
					$Log['tx'][]=$query;
					
					if(pg_errormessage($ConecSIG)!=''){
						$Log['res']='error';
						$Log['tx'][]='error al borrar base de datos';
						$Log['tx'][]=pg_errormessage($ConecSIG);
						$Log['tx'][]=$query;
						terminar($Log);	
					}
					
					$query="
					UPDATE
						ST_Transform(geom,".$_POST['crs'].", 3857) as geom
						
						FROM trecc_ai.area_influencia 
				    	WHERE id='".$Consulta."'
				   ";
					
	 * 
	 * */		
	
		    }else{
		    	$Log['data']['shp']['stat']='inviable';
				$Log['data']['shp']['mg']='inviable';
		    }

 
		    
		}catch (ShapeFileException $e) {
		    // Print detailed error information
		    $Log['data']['shp']['stat']='inviable';
			$Log['data']['shp']['mg']='Error '.$e->getCode().' ('.$e->getErrorType().'): '.$e->getMessage();
		    
		    
		}
				
	}
	
	$Log['res']='exito';
	terminar($Log);
}



$query="
SELECT id, tabla, nombre, fechau, zz_borrada, publicada, 
       superada
  FROM treccsound.sis_versiones
  WHERE 
   tabla = '".$_POST['tabla']."'
   AND
  	zz_borrada = '0'
  	AND
  	publicada = '1'
  	AND
  	superada = '0'
 ";
$ConsultaVer = pg_query($ConecSIG, $query);
if(pg_errormessage($ConecSIG)!=''){
	$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
	$Log['tx'][]='query: '.$query;
	$Log['mg'][]='error interno';
	$Log['res']='err';
	terminar($Log);	
}

if(pg_num_rows($Consultaver)<1){
	$Nombre='1';
}else{
	$fila=pg_fetch_assoc($ConsultaVer);	
	$num = $fila['nombre'];
	preg_replace('/[^0-9]/', '', $num);
	$Vsuperar=$fila['id'];
	
	if($num>0){
		
		$Nombre = $num + 1;	
		
	}else{
		
		$query="
			SELECT id, tabla, nombre, fechau, zz_borrada, publicada, 
			       superada
			  FROM treccsound.sis_versiones
			  WHERE 
			  tabla = '".$_POST['tabla']."'
			  	zz_borrada = '0'
			  	AND
			  	publicada = '1'
		 ";
		$ConsultaVers = pg_query($ConecSIG, $query);
		if(pg_errormessage($ConecSIG)!=''){
			$Log['tx'][]='error: '.pg_errormessage($ConecSIG);
			$Log['tx'][]='query: '.$query;
			$Log['mg'][]='error interno';
			$Log['res']='err';
			terminar($Log);	
		}
		
		$Nombre = pg_num_rows($Consultaver) + 1;
		
	}
	
}

$query="
	INSERT INTO 
		treccsound.sis_versiones(
            tabla, 
            nombre, 
            usu_autor, 
            fechau)
    	VALUES (
    		'".$_POST['tabla']."', 
    		'".$Nombre."', 
    		'".$Usu['datos']['id']."',
    		'".time()."')
    	RETURNING id
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
$Nid=$fila['id'];
$Log['tx'][]='version creada, id:'.$Nid;
$Log['data']['nid']=$Nid;


$Log['res']='exito';
terminar($Log);		
?>