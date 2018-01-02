<?php 
/**
* salida_valores.php
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
session_start();

// funciones frecuentes
include("./includes/fechas.php");
include("./includes/cadenas.php");

// función de consulta de proyectoes a la base de datos 
// include("./consulta_mediciones.php");

$ID = isset($_GET['id'])?$_GET['id'] : '';

$Hoy_a = date("Y");$Hoy_m = date("m");$Hoy_d = date("d");
$HOY = $Hoy_a."-".$Hoy_m."-".$Hoy_d;	
// medicion de rendimiento lamp 
$starttime = microtime(true);
?>
<head>
	<title>TReCC - Mapa Visualizador de variables Ambientales</title>
	<?php include("./includes/meta.php");?>
	<link href="./css/mapauba.css" rel="stylesheet" type="text/css">
	<link href="./css/BaseSonido.css" rel="stylesheet" type="text/css">
	<link href="./css/ad_navega.css" rel="stylesheet" type="text/css">	
	<link href="./css/tablarelev.css" rel="stylesheet" type="text/css">
	<link rel="manifest" href="pantallahorizontal.json">
	<link href="./css/BA_salidarelevamiento.css" rel="stylesheet" type="text/css">
	
	
	<style type='text/css'>					
		
		#modmapa {
		    position: relative;
		    width: 600px;
		    height: 600px;
		}
		
		#portamapa{
			display:inline-block;
			vertical-align:top;
		}
		#cuadrovalores{
			display:inline-block;
			vertical-align:top;
		}	
		.celda{
			display:inline-block;
			width:180px;
		}
		div.fila{
			width:360px;
			height:auto;
		}
		div.nombre[estado='activo']{
			color:#08AFD9;
			cursor:pointer;
		}
		div.nombre[estado='activo']:hover{
			color:#fff;
			background-color:#08AFD9;
		}
		#titulomapa{
			z-index:3;
			position:absolute;
			top:0.5vh;
			left:0.5vh;
			width:20vw;
			height:auto;
			border:1px solid #08AFD9;
			background-color:#fff;	
		}
		#locrangos{
			z-index:3;
			position:relative;		
			height:0;
			width:0;
			border:none;	
		}	
		
		#rangosmapa{
			z-index:3;
			position:absolute;
			top:1vw;
			left:0;			
			height:auto;
			width:10vw;
			border:1px solid #08AFD9;
			background-color:#fff;	
		}	
			
		div.nombre[estado="activo"][selecto='si']{
			background-color:lightblue;
			color:#000;
		}
		@media (max-width : 1020px){
			#pageborde {			    
			    border: none;
			    height: calc(98vh - 95px);
			    width: calc(100vw - 2vh);
			    margin: 1vh;
			    position:relative;
			    max-height:calc((102vw) / 2);
			}
			#page {
				position:absolute;
			    border: none;
			    font-size: 0.5vw;
			    height: auto;
			    margin: 2vh;
			    width: calc(100vw - 2vh - 4vh);
			    height: calc(94vh - 95px);
			    min-height:0;
			    max-height:calc((100vw - 2vh - 4vh) / 2);
			}
			body{
				margin:0;
				overflow:hidden;
			}
			#portamapa{
				width: calc(61vw - 6vh);
			}
			#modmapa {
			    position: relative;
			    width: calc(61vw - 6vh);
			    height: calc(94vh - 95px);
			    max-height:calc((100vw - 2vh - 4vh) / 2);
			}
			div#cuadrovalores{
				position:relative;
				height:100%;
				overflow-y:auto;
				width: 38.5vw;
			}
			div.fila{
				width: 38.5vw;
				font-size:1.5vw;
			}
			.celda{
				width:3vw;
				margin-right:1vw;
				text-align:right;
			}
			.nombre.celda{
				width:8vw;
				margin-right:0;
			}
			
			#valloc.celda{
				font-size:1.5vw;
			}
			#valor.celda{
				font-size:1.5vw;
			}
			#coordenadas .celda{
				width:18vw;
			}
			
			.menunav{
				display:none;
			}
			.elqr{
				display:none;
			}
			
			#titulomapa #tnombre{
				font-size:2vw;
				background-color:lightblue;
				text-align:center;
			}
			#titulomapa #tdescripcion{
				font-size:1.6vw;
			}
			
			
			#rangosmapa .rango{
				font-size:1.2vw;
				display:block;
			}
		}
		
		
.formcentral{
			position:fixed;
			top:5vh;
			height:90vh;
			left:5vw;
			width:90vw;
			background-color:#fff;
			border:3px solid #000;
			box-shadow:2vh 2vh 4vh rgba(50, 50, 50, 0.5);
			z-index:1000;
			display:none;
		}
		.formcentral a{		
			display:block;
		}
		.formcentral #carga{		
			display:none;
		}
		
			
	#shp{
		background-color:#fff;
		border:1px solid #000;
		top:calc(50vh - 50px);
		left:calc(50vw - 100px);
		width:200px;
		height:100px;
	}
	
	#shp select{
		width: 100%;
	}
	
	.cerrar{
		position:absolute;
		right:0px;
		top:0px;
	}
	
#menutablas #lista a{
	vertical-align:middle;
	margin-left:3px;
}
label.upload{
	width:130px;
	height:30px;
	display:inline-block;
	margin:3px;
}	
#uploadinput{
	position: absolute;
	display:block;
	width:130px;
	margin:0;
	color: #f55;
	background-color: #ffb;
	vertical-align: middle;

	font-family: inherit;
	margin-bottom: 5px;
	vertical-align: middle;
	height: 22px;
	
}

.upload > span{
    position: absolute;
    top: 0px;
    left: 0px;
    width: 130px;
    height: 22px;
    line-height: 11px;
    display: inline-block;
    width: 90px;
    font-size: 12px;
    font-weight: normal;
	border-top: 1px gray solid;
	border-left: 1px gray solid;
	border-right: 1px gray solid;
	border-bottom: 1px  gray solid;
}

.carga{
	background-color:#fd9;	
}

.carga[estado='subido']{
	background-color:#df9;
}

.carga[estado='fallido']{
	background-color:#f00;
}
	
#archivoscargados{
	width:200px;	
}

#archivoscargados [estado='viable']{
	background-color:#afa;
	cursor:help;
}
#archivoscargados [estado='viableobs']{
	background-color:#ffa;
	cursor:help;
}
#archivoscargados [estado='inviable']{
	background-color:#faa;
	cursor:help;
}

#procesarBoton[estado='inviable']{
	background-color:#faa;
	color:#444;	
	cursor:help;	
}
.componentecarga{
	width:250px;
	display:inline-block;
	vertical-align:top;
}
.componentecargalargo{
	width:400px;
	display:inline-block;
	vertical-align:top;
}
#camposident div>div{
	display:inline-block;
	width:80px;
	font-size:12px;
	word-wrap: break-word;
}
#camposident div input{
	height:16px;
	margin-bottom:0;
}

#camposident .enshp{
		display:inline-block;
		width:80px;
		border:1px solid #04586c;
		margin:0px;
		background-color:rgba(173, 216, 230, 0.5);
		cursor:move;
		overflow:hidden;
		vertical-align: middle;
		box-shadow: 1px 1px 1px rgba(0,0,0,0.5);
		
	}
#camposident #espacioshp{
	display:inline-block;
	width:82px;
	height:16px;
	border:1px dashed #aaa;
	margin:0px;
	overflow:hidden;
	vertical-align: middle;
	
}
#camposident > div{
	height:20px;
}
#verproccampo{
	display:none;
}
#avanceproceso{
	display:none;
	position:absolute;
	top:calc(50% - 25px);
	left:calc(50% - 50px);
	height:50px;
	width:100px;
	text-align:middle;
	border:3px solid #08afd9;
	border-radius:5px;
	font-size:30px;
	font-weight:bold;
	box-shadow:10px 10px 5px rgba(50, 50, 50, 0.5);
	z-index:2000;
}
	</style>	
</head>

<body>
	
	<!---https://jquery.com/-->
	<script type="text/javascript" src="./js/jquery/jquery-1.8.2.js"></script>
	
	<!---https://davidshimjs.github.io/qrcodejs/-->		
	<script type="text/javascript" src="./js/qrcodejs/qrcode.js"></script>
		
	<!---<script src="http://www.openlayers.org/api/OpenLayers.js"></script>-->
	<script src="./js/ol3/build/ol-debug.js"></script>
<?php

if(!isset($_GET['cp'])&&$_SESSION["UsuarioI"]<1){
	echo "El registro solicitado no se encuentra disponible. Comuníquese con su proveedor de datos.";
	exit;
}
if(!isset($_GET['cp'])){
	include('./ad_navega.php'); // carga menu de navegación entre pantallas de un mismo proyecto, solo si la consulta es realizada en modo gesetión
}


?>
<script type='text/javascript'>
	console.log('h');
	screen.orientation.lock('landscape');
	console.log('h');
	var _CodPro = '<?php echo $_GET['cp'];?>';

	var map = {};
	var _IdPro = '<?php echo $ID;?>';
	var _PRO  = {}; //array de proyecto;
	function cargarProyecto(){		
		var parametros = {
			"id" : _IdPro,
			"cp" : _CodPro
		};
		
		$.ajax({
			data:  parametros,
			url:   'consulta_proyecto_ajax.php',
			type:  'post',
			success:  function (response){
				var _res = $.parseJSON(response);
				console.log('datos proyecto:');
				console.log(_res);
				if(_res.res=='exito'){
					_PRO=_res.data;
					
					for(_ii in _res.data.TS){
						if(document.querySelector('div.menunav #Nmapa')!=null){
							document.querySelector('div.menunav #Nmapa').removeAttribute('disabled');
						}
					}
					
					_qrdiv=document.createElement('div');
					_qrdiv.setAttribute('class','elqr');
					document.querySelector('#cuadrovalores').appendChild(_qrdiv);
					_qrdiv.innerHTML='<div class="venc">venc:<?php $venc=sumames($HOY,3); echo mes($venc).'/'.ano($venc);?></div>';
					//_qrdiv.style.display='none';
					var _qrcode = new QRCode(_qrdiv, {
						width : 500,
						height : 500
					});
					_qrcode.makeCode("http://190.111.246.33/BaseSonido/salida_valores.php?id="+_IdPro+"&cp="+_res.data.zz_codacc);
					_qrdiv.querySelector('img').style.width='100px';
				}else{
					console.log('falló la actualizaicón de estados');
				}
				for(_nm in _res.mg){
					alert(_res.mg[_nm]);
				}
				
				
			}
		});	
	}
	cargarProyecto();
</script>

<div id="pageborde">
	<div id="page">
		
		<div id='portamapa'>
			<div id='titulomapa'><p id='tnombre'></p><p id='tdescripcion'></p><div id='locrangos'><div id='rangosmapa'></div></div></div>
			
			<div id='modmapa'></div>
			<div id="wrapper">
		        <div id="location"></div>
		        <div id="scale"></div>
		    </div>
		</div>
		
		<div id='cuadrovalores'>
			
			<div class='fila' id='coordenadas'>
				<div id='valloc' class='nombre celda'>Lat / Lon (media de celda)</div><div class='valor celda' id='valor'>- elegir punto -</div>
			</div>	
			
			<div id='variables'>
			</div>	
		
			
			<div id='menutablas'>
				<h1>menu de capas base</h1>
				<div id='lista'>
				</div>	
			</div>	
		</div>
	</div>	
</div>	

	
<div class='formcentral' id='formcargaverest' idver=''>
	<div id='avanceproceso'></div>
	<a class='cerrar' onclick='this.parentNode.style.display="none";'>x- cerrar</a>
	<h1>formulario para la carga de una nueva versión para una capa base</h1>
	<p>las capas base regulan la operación de la plataforma.</p>
	<p>Es muy recomendable que sepa lo que está haciendo antes de seguir.</p>
	<a id='botonformversion' onclick='formversion(this)'>cargar una nueva versión</a>
	<div id='carga'>
		<h2> usted está cargando una nueva versión con el id <span id='idnv'></span></h2>
		<p id='nomver'></p>

		<div class='componentecarga'>
			<h1>archivos cargando</h1>
			<div id='archivosacargar'>
				<form id='shp' enctype='multipart/form-data' method='post' action='./ed_ai_adjunto.php'>			
					<label style='position:relative;' class='upload'>							
					<span id='upload' style='position:absolute;top:0px;left:0px;'>arrastre o busque aquí un archivo</span>
					<input id='uploadinput' style='opacity:0;' type='file' multiple name='upload' value='' onchange='enviarSHP(event,this);'></label>
					<select id='crs' onchange='ValidarProcesarBoton()'>
						<option value=''>- elegir -</option>
						<option value='4326'>4326</option>
						<option value='3857'>3857</option>
						<option value='22171'>22171</option>
						<option value='22172'>22172</option>
						<option value='22173'>22173</option>
						<option value='22174'>22174</option>
						<option value='22175'>22175</option>
						<option value='22176'>22176</option>
						<option value='22177'>22177</option>
					</select>
					
					<div id='cargando'></div>
				</form>
			</div>
		</div>
		
		<div class='componentecarga'>
			<h1>archivos cargados</h1>
			<p id='txningunarchivo'>- ninguno -</p>
			<div id='archivoscargados'></div>
		</div>
		
		<div class='componentecargalargo'>
			<h1>campos identificados</h1>
			<p id='verproccampo'></p>
			<div id='camposident'></div>			
		</div>
		
		<div class='componentecarga'>
			<h1>Acciones</h1>
			<a onclick='eliminarCandidatoVersion();'>eliminar esta versión candidata</a>
			<a onclick='guardarVer(this.parentNode);'>guardar esta versión preliminarmente</a>
			<a id='procesarBoton' onclick='procesarVersion(this.parentNode)'>procesar la carga de esta versión</a>
		</div>
		
	</div>
	
</div>	


<script type="text/javascript">

var _Tablas={};
function consultarTablas(){

	var _parametros = {
	};
	$.ajax({
		url:   'consulta_tablas.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			var _res = $.parseJSON(response);
				//console.log(_res);
				_Tablas=_res.data.tablas;
				
				_cont=document.querySelector('#menutablas #lista');
				for(_nn in _Tablas['bas']){	
					_cont.innerHTML+='<br>';
					_aaa=document.createElement('a');
					_aaa.innerHTML=_Tablas['bas'][_nn];
					_aaa.setAttribute('tabla',_Tablas['bas'][_nn]);
					_aaa.setAttribute('onclick','mostrartabla(this)');
					_cont.appendChild(_aaa);
							
					_aaa=document.createElement('a');
					_aaa.innerHTML='<img src="./img/editar.png">';
					_aaa.setAttribute('tabla',_Tablas['bas'][_nn]);
					_aaa.setAttribute('onclick','cargarAtabla(this)');
					_cont.appendChild(_aaa);
				}
				
		}
	});
}
consultarTablas();			
					
function cargarAtabla(_this){
	limpiarfomularioversion();
	document.getElementById('formcargaverest').style.display='block';
	document.getElementById('formcargaverest').setAttribute('tabla',_this.getAttribute('tabla'));
}
	
function mostrartabla(_this){	
	
	_tabla=_this.getAttribute('tabla');
	_ExtraBaseWmsSource= new ol.source.TileWMS({
        url: 'http://190.111.246.33:8080/geoserver/BaseTReCC/wms',
        params: {
	        'VERSION': '1.1.1',
	        tiled: true,
	        LAYERS: 'BaseTReCC:'+_tabla,
	        STYLES: '',
        }
   });
	La_ExtraBaseWms.setSource(_ExtraBaseWmsSource);
	
}


</script>


<script type="text/javascript">


//operación del formulario central

function limpiarfomularioversion(){
	document.querySelector('#formcargaverest select#crs').options[1].selected;
	document.querySelector('#formcargaverest').setAttribute('idver','');
	document.querySelector('#formcargaverest #txningunarchivo').style.display='block';
	document.querySelector('#formcargaverest #archivoscargados').innerHTML='';
	document.querySelector('#formcargaverest #camposident').innerHTML='';
	document.querySelector('#formcargaverest #carga').style.display='none';
	document.querySelector('#formcargaverest #carga').style.display='none';
	document.querySelector('#formcargaverest #botonformversion').style.display='block';
}


function allowDrop(ev) {
    ev.preventDefault();
    
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev,_gg) {
	
    ev.preventDefault();
    
    var data = ev.dataTransfer.getData("text");
	
    _dest=ev.target;
    if(_dest.getAttribute('id')!='espacioshp'){
    	return;
    }
    
    if(_dest.querySelector('.enshp') != null){
    	
    	return;
    }

    
    _obj=document.getElementById(data);
    _parent=_obj.parentNode.parentNode;
    
    if(_dest.parentNode.getAttribute('origen')=='aux'){
 		_clon=_dest.parentNode.cloneNode(true);
 		_parent.parentNode.appendChild(_clon);
 		
 		_dest.parentNode.setAttribute('origen','shp');
 	}
 	    
    _dest.appendChild(_obj);    
    
    if(_parent.getAttribute('origen')=='shp'){
 		_parent.parentNode.removeChild(_parent);
 	}
 	
 	actualizarCadenaCampos();
	    	 
}

var _Procesarcampos;
function actualizarCadenaCampos(){
	ValidarProcesarBoton();
	_Procesarcampos={};
	_filas=document.querySelectorAll('#formcargaverest #carga #camposident .enshp');
	for(_nf in _filas){
		if(typeof _filas[_nf] !='object'){continue;}
		_parent=_filas[_nf].parentNode.parentNode;
		if(_parent.getAttribute('origen')=='aux'){continue;}
		_nom=_filas[_nf].getAttribute('nom');
		if(_parent.querySelector('#entabla').getAttribute('nom')==''){
			if(_parent.querySelector('#crear').checked){
				_Procesarcampos[_nom]={};
				_Procesarcampos[_nom]['acc']='crear';
				
				if(_parent.querySelector('#rename').value!=''){
					_Procesarcampos[_nom]['nom']=_parent.querySelector('#rename').value;
				}else{
					_Procesarcampos[_nom]['nom']=_nom;
				}
			}
		}else{
			_Procesarcampos[_nom]={};
			_Procesarcampos[_nom]['acc']='asignar';
			_Procesarcampos[_nom]['nom']=_parent.querySelector('#entabla').getAttribute('nom');
		}
		
	}
	document.querySelector('#verproccampo').innerHTML=JSON.stringify(_Procesarcampos);
	
}

var _checkList={
	'prj':{'s':'no','mg':'sin prj definido'},
	'shp':{'s':'no','mg':'sin shapefile definido'},
	'dbf':{'s':'no','mg':'completamiento indefinido para algunas columnas d ela base'}
};


function procesarVersion(_this){
	_stop='no';
	for(_comp in _checkList){
		if(_checkList[_comp].s=='no'){
			alert(_checkList[_comp].mg);
			_stop='si';	
		}		
	}
	if(_stop=='si'){return;}
	
	guardarVer(_this,'si');
	
}

function procesarVersion2(_this,_avance){
	var _this =_this;
	var _parametros = {
		'tabla': _this.parentNode.getAttribute('tabla'),
		'accion': 'procesar versión',
		'id': document.querySelector('#formcargaverest #carga #idnv').innerHTML,
		'avance':_avance
	};
	
	$.ajax({
		url:   'ed_version_procesa.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			if(_res.res=='exito'){
				if(_res.data.avance!='final'){
					procesarVersion2(_this,_res.data.avance);
					document.querySelector('#avanceproceso').style.display='block';
					document.querySelector('#avanceproceso').innerHTML=_res.data.avanceP+"%";
					document.querySelector('#avanceproceso').setAttribute('avance',_res.data.avanceP);
				}else{
					document.querySelector('#avanceproceso').style.display='none';
				}
			}
		}
	});
}



function ValidarProcesarBoton(){
	actualizarestadoCampos();
	_stop='no';
	_bot=document.querySelector('#ProcesarBoton');
	_bot.title=''
	_bot.removeAttribute('estado');
	for(_comp in _checkList){
		if(_checkList[_comp].s=='no'){
			_bot.setAttribute('estado','inviable');
			_bot.title+=_checkList[_comp].mg;
			_stop='si';	
		}		
	}
	if(_stop=='no'){
		_bot.setAttribute('estado','viable');
		_bot.title+='listo para procesar versión';
	}
}


function actualizarestadoCampos(){
	//actualiza en el checklist el estado de los campos asignados
	_divs=document.querySelectorAll('#camposident > div[origen="tabla"]');
	
	_checkList.dbf.s='si';
	_checkList.dbf.mg='ok';
	for(_nd in _divs){
		if(typeof _divs[_nd]!='object'){continue;}
		if(_divs[_nd].querySelector('#espacioshp > .enshp') == null){
			_checkList.dbf.s='no';
			_checkList.dbf.mg='al menos un campo de la tabla carece de un campo asociado del shapefile.';
		}
	}	
}

function eliminarCandidatoVersion(){
	if(confirm("¿Confirma que quiere eliminar este candidato a versión? \n Si lo hace se eliminarán los archivos que haya subido y se guardará registro en la papelera de los datos cargados en el formulario.")){
		consloe.log('o');
	}
}

function formversion(_this){
	limpiarfomularioversion();
	
	//intenta generar una nueva versión candidata para este usuario u esta capa
	var _parametros = {
		'tabla': _this.parentNode.getAttribute('tabla'),
		'accion': 'crear nueva versión'
	};
	$.ajax({
		url:   'ed_version_crea.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			var _res = $.parseJSON(response);
				console.log(_res);
				
				for(_nm in _res.mg){alert(_res.mg[_nm]);}
				
				if(_res.res=='exito'){
					document.querySelector('#formcargaverest #carga').style.display='block';
					//_this.style.display='none';
					
					
					if(_res.data.nid!=undefined){
						document.querySelector('#formcargaverest #carga #idnv').innerHTML=_res.data.nid;
					}else{
						document.querySelector('#formcargaverest #carga #idnv').innerHTML=_res.data.version.id;
						document.querySelector('#formcargaverest').setAttribute('idver',_res.data.version.id);
						document.querySelector('#formcargaverest #carga #nomver').innerHTML='nombre: '+_res.data.version.nombre;
					}
					
					for(_na in _res.data.archivos){
						document.querySelector('#formcargaverest #txningunarchivo').style.display='none';
						_fil=document.createElement('p');
						_fil.innerHTML=_res.data.archivos[_na].nom;
						_fil.setAttribute('fileExt',_res.data.archivos[_na].ext);				
						document.querySelector('#formcargaverest #carga #archivoscargados').appendChild(_fil);						
					}
					
					
					if(_res.data.prj.stat=='viable'){
						_checkList.prj.s='si';
						_checkList.prj.ms='ok';
						_sel=document.querySelector('#crs');
						for(_no in _sel.options){
							if(_sel.options[_no].value==_res.data.prj.def){
								_sel.options[_no].selected=true;
								
								_ppp=document.querySelectorAll('#archivoscargados [fileext="prj"], #archivoscargados [fileext="qpj"]');
								for(_np in _ppp){
									if(typeof _ppp[_np] == 'object'){
										_ppp[_np].setAttribute('estado','viable');
										_ppp[_np].title=_res.data.prj.mg;
									}
								}
							}
						}		
						
						
					}else if(_res.data.prj.stat=='viableobs'){
						_checkList.prj.s='si';
						_checkList.prj.ms='se adoptará el prj del formulario que difiere del explicitado en el archivo subido';
						_sel=document.querySelector('#crs');
						for(_no in _sel.options){
							if(_sel.options[_no].value==_res.data.prj.def){
								_sel.options[_no].selected=true;
								
								_ppp=document.querySelectorAll('#archivoscargados [fileext="prj"], #archivoscargados [fileext="qpj"]');
								for(_np in _ppp){
									if(typeof _ppp[_np] == 'object'){
										_ppp[_np].setAttribute('estado','viableobs');
										_ppp[_np].title=_res.data.prj.mg;
									}
								}
							}
						}
					}else{
						_checkList.prj.s='no';
						_checkList.prj.ms=_res.data.prj.mg;
						_ppp=document.querySelectorAll('#archivoscargados [fileext="prj"], #archivoscargados [fileext="qpj"]');
						for(_np in _ppp){
							if(typeof _ppp[_np] == 'object'){
								_ppp[_np].setAttribute('estado','inviable');
								_ppp[_np].title=_res.data.prj.mg;
							}
						}
						
						alert("crs: ",_res.data.prj.mg);
					}
					
					
					if(_res.data.shp.stat=='viable'){
						_checkList.shp.s='si';
						_checkList.shp.ms='ok';
						_ppp=document.querySelectorAll('#archivoscargados [fileext="shp"], #archivoscargados [fileext="shx"], #archivoscargados [fileext="dbf"]');
						for(_np in _ppp){
							if(typeof _ppp[_np] == 'object'){
								_ppp[_np].setAttribute('estado','viable');
								_ppp[_np].title=_res.data.shp.mg;
							}
						}
					}else if(_res.data.prj.stat=='inviable'){
						_checkList.shp.s='no';
						_checkList.shp.ms=_res.data.shp.mg;
						_ppp=document.querySelectorAll('#archivoscargados [fileext="shp"], #archivoscargados [fileext="shx"], #archivoscargados [fileext="dbf"]');
						for(_np in _ppp){
							if(typeof _ppp[_np] == 'object'){
								_ppp[_np].setAttribute('estado','inviable');
								_ppp[_np].title=_res.data.shp.mg;
							}
						}
						alert(_res.data.shp.mg);
					}
					
					
					for(_col in _res.data.columnas){
						
						if(_col=='id'){continue;}
						if(_col=='geo'){continue;}
						if(_col=='id_sis_versiones'){continue;}
						
						_fil=document.createElement('div');
						_fil.setAttribute('origen','tabla');
						
						_nom=document.createElement('div');
						_nom.setAttribute('id','entabla');
						_nom.setAttribute('nom',_col);
						_nom.innerHTML=_col;						
						_fil.appendChild(_nom);
						
						_nom=document.createElement('div');
						_nom.setAttribute('id','espacioshp');	
						_nom.setAttribute('ondrop',"drop(event)");
						_nom.setAttribute('ondragover',"allowDrop(event)");
								
						_fil.appendChild(_nom);
						/*
						_nom=document.createElement('input');
						_nom.setAttribute('id','rename');	
						_fil.appendChild(_nom);
						*/
						/*
						_nom=document.createElement('input');
						_nom.setAttribute('type','checkbox');
						_nom.setAttribute('id','crear');	
						_fil.appendChild(_nom);
						*/
						
						document.querySelector('#formcargaverest #carga #camposident').appendChild(_fil);
						
					}
					
					_c=0;
					_Icampos = $.parseJSON(_res.data.version.instrucciones);
					console.log(_Icampos);
					for(_col in _res.data.dbf.campos){
						
						_dat=_res.data.dbf.campos[_col];
						_norig=_dat.name;
						_nombre=_dat.name;
						
						if(_norig=='id'){continue;}
						if(_norig=='geo'){continue;}
						if(_norig=='id_sis_versiones'){continue;}
						
						
						_crear=false;
						if(_Icampos[_norig]!=null){
							_nombre=_Icampos[_norig].nom;
							if(_Icampos[_norig].acc=='crear'){_crear=true;}
						}
						console.log(_nombre+' '+_norig);
						_ref=document.querySelector('#formcargaverest #carga #camposident #entabla[nom="'+_nombre+'"]');
						
						
						if(_ref==null){
							
							_fil=document.createElement('div');
							_fil.setAttribute('id','entabla')
							_fil.setAttribute('origen','shp');
							
							
							_nom=document.createElement('div');
							_nom.setAttribute('id','entabla');
							_nom.setAttribute('nom','');
							_fil.appendChild(_nom);
							
							_nome=document.createElement('div');
							_nome.setAttribute('id','espacioshp');	
							_nome.setAttribute('ondrop',"drop(event)");
							_nome.setAttribute('ondragover',"allowDrop(event)");
							_fil.appendChild(_nome);
							
							_nom=document.createElement('div');
							_c++;
							_nom.setAttribute('id',_c);
							_nom.setAttribute('class','enshp');
							_nom.setAttribute('nom',_nombre);
							_nom.setAttribute("draggable","true");
							_nom.setAttribute("ondragstart","drag(event)");							
							_nom.innerHTML=_norig;			
							_nome.appendChild(_nom);
							
							_nom=document.createElement('input');
							_nom.setAttribute('id','rename');
							_nom.setAttribute('onkeyup','actualizarCadenaCampos()');
							if(_norig!=_nombre){_nom.value=_nombre;}	
							_fil.appendChild(_nom);
							
							_nom=document.createElement('input');
							_nom.setAttribute('type','checkbox');
							_nom.checked=_crear;
							_nom.setAttribute('id','crear');	
							_nom.setAttribute('onchange','actualizarCadenaCampos()');
							_fil.appendChild(_nom);
	
							
							document.querySelector('#formcargaverest #carga #camposident').appendChild(_fil);
							
						}else{
							
							_nome=_ref.parentNode.querySelector('#espacioshp');
							
							_nom=document.createElement('div');
							_c++;
							_nom.setAttribute('id',_c);
							_nom.setAttribute('class','enshp');
							_nom.setAttribute('nom',_nombre);
							_nom.setAttribute("draggable","true");
							_nom.setAttribute("ondragstart","drag(event)");							
							_nom.innerHTML=_norig;			
							_nome.appendChild(_nom);
											
							
						}
					}
					
					_fil=document.createElement('div');	
					_fil.setAttribute('origen','aux');
					
					_nom=document.createElement('div');
					_nom.setAttribute('id','entabla');
					_nom.setAttribute('nom','');					
					_fil.appendChild(_nom);
					
					_nom=document.createElement('div');
					_nom.setAttribute('id','espacioshp');	
					_nom.setAttribute('ondrop',"drop(event)");
					_nom.setAttribute('ondragover',"allowDrop(event)");
							
					_fil.appendChild(_nom);
					
					_nom=document.createElement('input');
					_nom.setAttribute('id','rename');	
					_nom.setAttribute('onkeyup','actualizarCadenaCampos()');
					_fil.appendChild(_nom);
					
					_nom=document.createElement('input');
					_nom.setAttribute('type','checkbox');
					_nom.setAttribute('onchange','actualizarCadenaCampos()');
					_nom.setAttribute('id','crear');	
					_fil.appendChild(_nom);
					
					
					document.querySelector('#formcargaverest #carga #camposident').appendChild(_fil);
				
				
					actualizarCadenaCampos();		
					
				}
				
				
				
				/*
				_Tablas=_res.data.tablas;
				
				_cont=document.querySelector('#menutablas #lista');
				for(_nn in _Tablas['est']){					
					_aaa=document.createElement('a');
					_aaa.innerHTML=_Tablas['est'][_nn];
					_aaa.setAttribute('tabla',_Tablas['est'][_nn]);
					_aaa.setAttribute('onclick','cargarAtabla(this)');
					_cont.appendChild(_aaa);
				}*/
		}
	});

	document.querySelector('#formcargaverest #carga').style.display='block';
	_this.style.display='none';
	
}

function guardarVer(_this,_procesar){

	//intenta generar una nueva versión candidata para este usuario u esta capa
	var _this=_this;
	var _procesar=_procesar;
	var _parametros = {
		'tabla': _this.parentNode.parentNode.getAttribute('tabla'),
		'accion': 'guardar version',
		'instrucciones':_this.parentNode.querySelector('#verproccampo').innerHTML,
		'fi_prj':_this.parentNode.querySelector('select#crs').value,
		'id':_this.parentNode.querySelector('#idnv').innerHTML
	};
	$.ajax({
		url:   'ed_version_cambia.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			
			var _res = $.parseJSON(response);
			console.log(_res);
			
			if(_procesar=='si'){procesarVersion2(_this.parentNode,0);return;}
			formversion(_this.parentNode);
			
		}
	});
}	
					
</script>

<script type="text/javascript">

var _ErasterColor={}
function mapaLocalizaciones(){
	_modmapa=document.getElementById('modmapa');
	_modmapa.innerHTML='';
	_portamapa=document.getElementById('portamapa');

	var _parametros = {
		"id" : _IdPro,
		"cp" : _CodPro
	};
	$.ajax({
		url:   'cons_geojson.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			var _res = $.parseJSON(response);
				//console.log(_res);
				//return;
				_map=_res.data;
				_encaba='';
				
				for(_nn in _Mapeo){
					
						_cXMin= _Mapeo[_nn].zz_minx;
						_cXMax= _Mapeo[_nn].zz_maxx;
						_cYMin= _Mapeo[_nn].zz_miny;
						_cYMax= _Mapeo[_nn].zz_maxy;
						
						if((_cXMax-_cXMin)>(_cYMax-_cYMin)){
							//pone en horizontal el div que recibirá el mapa
							_alt=document.getElementById('modmapa').clientHeight;
							document.getElementById('modmapa').style.height=document.getElementById('modmapa').clientWidth-100;
							document.getElementById('modmapa').style.width=_alt+100;
						}
						
						document.querySelector('#cuadrovalores #coordenadas #valor.celda').innerHTML="- elegir punto -";							
						for(_nv in _Mapeo[_nn].variables){
							
							_aa=_nv.split('_');
							
							if(_aa[0]=='S2'){continue;}
							if(_aa[0]=='S6'){continue;}
							if(_aa[0]=='S7'){continue;}
							if(_aa[0]=='S9'){continue;}
							if(_aa[0]=='S12'){continue;}
							
							if(_aa[0]=='V2'){continue;}
							if(_aa[0]=='V6'){continue;}							
							if(_aa[0]=='V7'){continue;}
							if(_aa[0]=='V9'){continue;}
							if(_aa[0]=='V12'){continue;}
							
							_fn=_aa[0]+'_'+_aa[1]+'_'+_aa[2];
							
							_ErasterColor[_nv]=$.parseJSON(_Mapeo[_nn].variables[_nv].eraster_color);
							
							
							if(document.querySelector('#cuadrovalores .fila[vnom="'+_fn+'"]')!=null){
								
								_fila=document.querySelector('#cuadrovalores .fila[vnom="'+_fn+'"]');
								
							}else{
								_fila=document.createElement('div');
								_fila.setAttribute('class','fila');
								_fila.setAttribute('vnom',_fn);
							}
							
							_celda=document.createElement('div');
							_celda.setAttribute('class','nombre celda');
							_celda.setAttribute('id','link'+_nv);
							_celda.innerHTML=_nv;
							_fila.appendChild(_celda);
							
							
							_celda=document.createElement('div');
							_celda.setAttribute('class','valor celda');
							_celda.setAttribute('id',_nv);
							_celda.innerHTML='-';
							_fila.appendChild(_celda);
							
							if(_aa[3]=='x'){
								
								_celda=document.createElement('div');
								_celda.setAttribute('class','nombre celda');
								_celda.setAttribute('id','link'+_nv);
								_celda.innerHTML=_nv;
								_fila.appendChild(_celda);
								
								_celda=document.createElement('div');
								_celda.setAttribute('class','valor celda');
								_celda.setAttribute('id',_nv);
								_celda.innerHTML='-';
								_fila.appendChild(_celda);
								
								_celda=document.createElement('div');
								_celda.setAttribute('class','nombre celda');
								_celda.setAttribute('id','link'+_nv);
								_celda.innerHTML=_nv;
								_fila.appendChild(_celda);
								
								_celda=document.createElement('div');
								_celda.setAttribute('class','valor celda');
								_celda.setAttribute('id',_nv);
								_celda.innerHTML='-';
								_fila.appendChild(_celda);
							}
							
							document.querySelector('#cuadrovalores #variables').appendChild(_fila);
							
						}
				}
				
				
	            
				//map.addLayer(Vector);
				console.log('res:');
				console.log(_res);

				for(_nm in _res.mg){
					alert(_res.mg[_nm]);
				}
							
				if(_res.res=='exito'){
					mapaPngs();				
					mapaLocReporte('modmapa',_res.data,_encaba);	
				}
				
				
		}
	});	
}

var _CapasPngDisp={};
function mapaPngs(){

	var _parametros = {
		"id" : _IdPro,
		"cp" : _CodPro
	};
	$.ajax({
		url:   'consulta_proyecto_pngs.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			//return;
	            
			//map.addLayer(Vector);
			console.log('res:');
			console.log(_res);
			_CapasPngDisp=_res.data.pngtemp;
			
			for(_nm in _res.mg){
				alert(_res.mg[_nm]);
			}
				
			if(_res.res=='exito'){
				cargarSelectorPng();	
			}
			
			
				
		}
	});	
}

function cargarSelectorPng(){
	_sel=document.getElementById('selpng');
	
	for(_npr in	_CapasPngDisp){
		for(_npn in _CapasPngDisp[_npr]){
			
			_vvv=_CapasPngDisp[_npr][_npn];
			console.log('ppp:'+_vvv);
			_vvv=_vvv.substring(0, (_vvv.length-4));
			_celda=document.getElementById('link'+_vvv);
			_celda.setAttribute('idts',_npr);
			_celda.setAttribute('url',_CapasPngDisp[_npr][_npn]);
			_celda.setAttribute('onclick','cargarCapaPng(this);');
			_celda.setAttribute('estado','activo');
			
		}
	}
}

function cargarCapaPng(_this){
	
	_ddd=document.querySelectorAll('#variables .celda.nombre');
	for(_dn in _ddd){
		if(typeof _ddd[_dn]=='object'){
			_ddd[_dn].setAttribute('selecto','no');
		}
	}
	
	_this.setAttribute('selecto','si');
	
	_str=_this.getAttribute('idts');
	_ans = _pad.substring(0, _pad.length - _str.length) + _str;
	_url = './base/pngTS/'+_ans+'/'+_this.getAttribute('url');
	
	
	_sourceimageLayer = new ol.source.ImageStatic({		                   
	        url: _url,
	        //imageSize: [691, 541],
	        imageExtent: [_cXMin, _cYMin, _cXMax, _cYMax]
	});	
	imageLayer.setSource(_sourceimageLayer);

	_png=_this.getAttribute('url');
	_var=_png.substring(0,(_png.length-4));
	_tit=document.querySelector('#titulomapa #tnombre');
	_tit.innerHTML=_var;
	_vvv=_var.split('_');
	
	switch (_vvv[0]) {
	    case 'V1':
	        _varV = "Límite Máximo Permitido (LMP)";
	        break;
	    case 'V3':
	        _varV = "Nivel Sonoro (mapa de ruido)";
	        break;
	    case 'V4':
	        _varV = "Nivel Sonoro (mapa de ruido)";
	        break;
	    case 'V5':
	        _varV = "Variación (impacto acústico)";
	        break;
	    case 'V8':
	        _varV = "Desvíos (diferencia a LMP)";
	        break;
	}
	_des=document.querySelector('#titulomapa #tdescripcion');
	_des.innerHTML=_varV;
	_varE='';
	switch (_vvv[1]) {
	    case '0':
	        _varE = "Escenario Actual";
	        break;
	    case '1':
	        _varE = "Escenario con Proyecto Operativo";
	        break;
	    case '2':
	        _varE = "Escenario en Obra";
	        break;
	    case 'x':
	        _varE = "Para todos los escenarios";
	        break;
	}
	if(_varE!=''){
		_des.innerHTML+='<br>'+_varE;
	}

	_varP='';
	switch (_vvv[2]) {
	    case 'D':
	        _varP = "Período Diurno";
	        break;
	    case 'N':
	        _varP = "Período Nocturno";
	        break;
	    case 'x':
	        _varP = "Para todos los períodos";
	        break;
	}
	if(_varP!=''){
		_des.innerHTML+='<br>'+_varP;
	}	

	_varH='';
	
	if(_vvv[3]=='x'){
		_varH = "Para todas las alturas";
	}else{
		_varH = 'altura: '+_vvv[3]+" m";
	}
	if(_varH!=''){
		_des.innerHTML+='<br>'+_varH;
	}		
	
	if(_ErasterColor[_var]!=''){
		_rm=document.getElementById('rangosmapa');
		_rm.innerHTML='';
		for(_nrang in _ErasterColor[_var]){
			_ran=document.createElement('div');
			_ran.setAttribute('class','rango');
			_rr=_ErasterColor[_var][_nrang];
			_ran.innerHTML=_rr.desde+' a '+_rr.hasta;
			_color='background-color: rgba('+_rr.r+', '+_rr.g+', '+_rr.b+', '+(0.72*_rr.alpha)+');';
			_ran.setAttribute('style',_color);
			//_ran.style.backgroundColor=_color;
			_rm.appendChild(_ran);
		}
		
	}
}
var _cXMin=null;
var _cYMin=null;
var _cXMax=null;
var _cYMax=null;
	
var map = {};
var _lyrAuxSrc = {};
var imageLayer = {};
var _sourceimageLayer = {};
var _sourceCelda ={};

var _ExtraBaseWmsSource = {};
var La_ExtraBaseWms = {};

function mapaLocReporte(_idDiv,_geoJson,_encaba){
	var pureCoverage = false;
	// if this is just a coverage or a group of them, disable a few items,
	// and default to jpeg format
	var format = 'image/png';
	var bounds = [5634483.5, 6158841.5,5652767, 6178801.5];
	  
	if (pureCoverage) {
	    document.getElementById('filterType').disabled = true;
	    document.getElementById('filter').disabled = true;
	    document.getElementById('antialiasSelector').disabled = true;
	    document.getElementById('updateFilterButton').disabled = true;
	    document.getElementById('resetFilterButton').disabled = true;
	    document.getElementById('jpeg').selected = true;
	    format = "image/jpeg";
	}
	
	var mousePositionControl = new ol.control.MousePosition({
	    className: 'custom-mouse-position',
	    target: document.getElementById('location'),
	    coordinateFormat: ol.coordinate.createStringXY(5),
	    undefinedHTML: '&nbsp;'
	});
	  
	var La_CABA = new ol.layer.Image({
	    source: new ol.source.ImageWMS({
	      ratio: 1,
	      url: 'http://190.111.246.33:8080/geoserver/BaseTReCC/wms',
	      params: {'FORMAT': format,
	               'VERSION': '1.1.1',  
	            LAYERS: 'base limpia',
	            STYLES: '',
	      }
	    })
	});


     var La_AMBA = new ol.layer.Tile({
        visible: true,
        source: new ol.source.TileWMS({
	        url: 'http://190.111.246.33:8080/geoserver/BaseTReCC/wms',
	        params: {
		      	'FORMAT': format, 
		        'VERSION': '1.1.1',
		        tiled: true,
		        LAYERS: 'BaseTReCC:amba',
		        STYLES: '',
	        }
        })
    });

	_ExtraBaseWmsSource = new ol.source.TileWMS();
        
    La_ExtraBaseWms = new ol.layer.Tile({
        visible: true,
        source: _ExtraBaseWmsSource
    });
    
    
	var projection = new ol.proj.Projection({
	      code: 'EPSG:22175',
	      units: 'm',
	      axisOrientation: 'neu'
	});
		
	 var fill = new ol.style.Fill({
	   color: 'rgba(255,155,155,1)'
	 });
	 var stroke = new ol.style.Stroke({
	   color: '#ff3333',
	   width: 1
	 });
	 
	 
	
	if(_geoJson.features.length>0){
	  var vectorSource = new ol.source.Vector({ 	
	    	features: (new ol.format.GeoJSON()).readFeatures(_geoJson)
	  
	  });
	 }
	
		var createTextStyle = function(feature, resolution) {
	
	    return new ol.style.Text({
	        text: feature.get('nombre'),
		    textAlign: 'right',
		    textBaseline: 'middle',
		    font: 'arial',
		    fill: new ol.style.Fill({color: '#000'}),
		    stroke: new ol.style.Stroke({color: '#fff', width: 1}),
		    offsetX: -3,
		    offsetY: 0,
		    rotation: 0
	    });
	  };
	       
	  function pointStyleFunction(feature) {
	    return new ol.style.Style({
	          image: new ol.style.Circle({
		       fill: fill,
		       stroke: stroke,
		       radius: 5
		     }),
		   	fill: fill,
		   	stroke: stroke,
	      	text: createTextStyle(feature)
	    });
	  }
	  
	
	_sourceCelda = 	new ol.source.Vector();  
	var celdaLayer = new ol.layer.Vector({
		source: _sourceCelda,
		style : 	new ol.style.Style({
				   		stroke: new ol.style.Stroke({color: '#0dd', width: 2})
					})
	});   
	celdaLayer.setZIndex( 1000 ); 
		        
	var vectorLayer = new ol.layer.Vector({
	  source: vectorSource,
	  style : pointStyleFunction
	});   
	vectorLayer.setZIndex( 1001 ); 
	
	for(_nn in _Mapeo){					
		_str = "" + _nn;
		_pad = "0000000";
		_ans = _pad.substring(0, _pad.length - _str.length) + _str;


		_cXMin= _Mapeo[_nn].zz_minx-_Mapeo[_nn].celda/2;
		_cXMax= _Mapeo[_nn].zz_maxx-_Mapeo[_nn].celda/2;
		_cYMin=Number.parseFloat(_Mapeo[_nn].zz_miny)+(_Mapeo[_nn].celda/2);
		_cYMax=Number.parseFloat(_Mapeo[_nn].zz_maxy)+(_Mapeo[_nn].celda/2);
			
		_sourceimageLayer = new ol.source.ImageStatic({		                   
               // url: '//192.168.0.252/TReCCsound/base/'+_ans+'/temp/V1_x_D_x.png',
                //imageSize: [691, 541],
                imageExtent: [_cXMin, _cYMin, _cXMax, _cYMax]
        });
		
		imageLayer = new ol.layer.Image({
            opacity: 0.75,
            source: _sourceimageLayer
        });
        
   }	
   
	   var _AuxStyle = new ol.style.Style({
	          stroke: new ol.style.Stroke({
	            color: 'rgba(0, 0, 0, 1.0)',
	            width: 1
	          })
	       });
    _lyrAuxSrc = new ol.source.Vector();
    var _lyrAux = new ol.layer.Vector({
    	name:'auxiliar lineas',
	  source: _lyrAuxSrc,
	  style : _AuxStyle
	});   
	_lyrAux.setZIndex( 1001 ); 
	
	
    if(_encaba=='f'){
    	_muestralayers=	[vectorLayer, La_CABA, La_AMBA, imageLayer, _lyrAux, celdaLayer, La_ExtraBaseWms]
    }else{
    	_muestralayers=	[vectorLayer, La_CABA, imageLayer, _lyrAux, celdaLayer, La_ExtraBaseWms]
    }	
    
	map = new ol.Map({
	    controls: ol.control.defaults({
	      attribution: false
	    }).extend([mousePositionControl]),
	    interactions : ol.interaction.defaults({doubleClickZoom :false}),
	    target: _idDiv,
	    layers: _muestralayers,
	    view: new ol.View({
	       projection: projection
	    })
	});
	  

	map.on('precompose', function(evt) {
	  evt.context.imageSmoothingEnabled = false;
	  evt.context.webkitImageSmoothingEnabled = false;
	  evt.context.mozImageSmoothingEnabled = false;
	  evt.context.msImageSmoothingEnabled = false;
	});

/*
	var hoverInteraction = new ol.interaction.Select({
	    condition: ol.events.condition.pointerMove,
	    layers: [vectorLayer,]
	});
	
	
	map.addInteraction(hoverInteraction);
	
	hoverInteraction.on('select', function(evt){
	    if(evt.selected.length > 0){
	       //console.info('selected: ' + evt.selected[0].getId());
	    }
	});
	*/
	map.on("click", function(e) {
		consultarValores(_nn,e.coordinate);
		//alert(e.coordinate);		
		/*
		var arrFeat=[];
	    map.forEachFeatureAtPixel(e.pixel, function (feature, layer) {
	       //do something
	       //console.log(feature.id_);
	       arrFeat.push(feature.id_);    
	    })
	   //console.log(arrFeat);
	   	if(arrFeat.length<1){
	   		consultarValores(_nn,e.coordinate);
	   		
	   	}else if(arrFeat.length>1){
	   		consultarValores(_nn,e.coordinate);
	   		//alert("ha seleccionado mas de una ubicacion");
	   	}else{
	   	   //console.log(arrFeat[0]);
	   	   consultarValores(_nn,e.coordinate);
	   	   // localizacionSeleccionada(arrFeat[0]);//funcion en salida_reporte.php
	    }*/
	});
	
	if(_geoJson.features.length>0){
		var extent =vectorLayer.getSource().getExtent();
	}else{
		var extent=bounds;
	}
	map.getView().fit(extent, map.getSize());      
	mapaAuxiliares();
}

function consultarValores(_idpro,_coords){
	
	_ddd=document.querySelectorAll('#cuadrovalores .valor.celda');
	for(_dn in _ddd){
		_ddd[_dn].innerHTML='-';		
	}
	
	_celAbsPos=Math.round(_coords[0]/_Mapeo[_idpro].celda);
	_x=_celAbsPos*_Mapeo[_idpro].celda;
	_celAbsPos=Math.round(_coords[1]/_Mapeo[_idpro].celda);
	_y=_celAbsPos*_Mapeo[_idpro].celda;
	var _parametros = {
		"idproproy" : _idpro,
		"x" : _x,
		"y" : _y	
	};
	
	$.ajax({
		url:   'consulta_valoresTS_celda.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			var _res = $.parseJSON(response);
				console.log(_res);
				//return;
	            
				//map.addLayer(Vector);
				console.log('res:');
				console.log(_res);
				celdaValReporte(_res.data.valores);	
				
		}
	});		

}

function celdaValReporte(_valores){
	
	for(_idval in _valores){

		document.querySelector('#cuadrovalores #coordenadas #valor').innerHTML=_valores[_idval].lat+' / '+_valores[_idval].lon;
		for(_prop in _valores[_idval]){
			
			console.log(_prop+" : "+_valores[_idval][_prop]);
			
			if(_valores[_idval][_prop]==null||_valores[_idval][_prop]=='-9999'||_valores[_idval][_prop]=='-99999'){_valores[_idval][_prop]='-';}
			
			if(document.querySelector('#cuadrovalores #variables #'+_prop)!=null){
				document.querySelector('#cuadrovalores #variables #'+_prop).innerHTML=_valores[_idval][_prop];
			}
			
			var format = new ol.format.WKT(); 
			var _feat = format.readFeature(_valores[_idval].wktgeo, {
		        dataProjection: 'EPSG:22175',
		        featureProjection: 'EPSG:22175'
		    });
		    _feat.setProperties({
		    	'idai':_valores[_idval].id,
		    	'nombre':_valores[_idval].y+' / '+_valores[_idval].x
		    });
		    
		_sourceCelda.clear();
		_sourceCelda.addFeature(_feat);
		}		
	}
	
}	


function mapaAuxiliares(){

	var _parametros = {
		"id" : _IdPro,
		"cp" : _CodPro
	};
	$.ajax({
		url:   'cons_pro_aux.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			//return;
            
			//map.addLayer(Vector);
			console.log('res:');
			console.log(_res);
			if(_res.res=='exito'){
				mapaAuxReporte(_res.data.aux);	
			}
		}
	});	
}


function mapaAuxReporte(_auxlineas){
	
	_hayaux='no';
		
	for(_naux in _auxlineas){
		
		var format = new ol.format.WKT();
	
	    var _feat = format.readFeature(_auxlineas[_naux].geo, {
	        dataProjection: 'EPSG:22175',
	        featureProjection: 'EPSG:22175'
	    });
	    _feat.setProperties({
	    	'idai':'_ngeo.id',
	    	'nombre':'_ngeo.nombre'
	    });
		_lyrAuxSrc.addFeature(_feat);
		_hayaux='si';	
	}
	

	if(_hayaux=='si'){
		map.getView().fit(_lyrAuxSrc.getExtent(), map.getSize());
	}	
	_lyrAuxSrc
	
}


var _Mapeo = {}; 
function consultaMapeo(){
	var parametros = {
		"id" : _IdPro,
		"cp" : _CodPro
	};
	
	$.ajax({
		data:  parametros,
		url:   'consulta_mapeo_ajax.php',
		type:  'post',
		success:  function (response){
			var _res = $.parseJSON(response);
			console.log(_res);
			if(_res.res=='exito'){
				_Mapeo = _res.data;
				mapaLocalizaciones();
			}else{
				console.log('falló la actualizaicón de estados');
			}
			
			
		}
	});	
}
consultaMapeo();	



var _LOCs  = new Array(); //array de localizaciones;

function activarMapaValores(){
	_modmapa=document.getElementById('modmapa');
	_modmapa.innerHTML='';
	_portamapa=document.getElementById('portamapa');

	var _parametros = {
		"id" : _IdPro,
		"cp" : _CodPro
	};
	$.ajax({
		url:   'cons_geojson.php',
		type:  'post',
		data: _parametros,
		success:  function (response){
			
			var _resSel = $.parseJSON(response);
			var _resSel = _resSel.data;
			
			if(_CodPro==''){
				$.ajax({
					url:   'cons_geojson.php',
					type:  'post',
					success:  function (response){
						
						var _resTodo = $.parseJSON(response);
						var _resTodo = _resTodo.data;
					
						if(_resSel.features[0]!=undefined){
							_cXMin= _resSel.features[0].geometry.coordinates[0];
							_cXMax= _resSel.features[0].geometry.coordinates[0];
							_cYMin= _resSel.features[0].geometry.coordinates[1];
							_cYMax= _resSel.features[0].geometry.coordinates[1];
							
							for(_nn in _resSel.features){
								_coor=_resSel.features[_nn].geometry.coordinates;
								_cXMin=Math.min(_cXMin,_coor[0]);_cXMax=Math.max(_cXMax,_coor[0]);
								_cYMin=Math.min(_cYMin,_coor[1]);_cYMax=Math.max(_cYMax,_coor[1]);
							}
							if((_cXMax-_cXMin)>(_cYMax-_cYMin)){
								//pone en horizontal el div que recibirá el mapa
								_alt=document.getElementById('modmapa').clientHeight;
								document.getElementById('modmapa').style.height=document.getElementById('modmapa').clientWidth-100;
								document.getElementById('modmapa').style.width=_alt+100;
							}
						}
						
						//map.addLayer(Vector);
						console.log('res:');
						console.log(_resSel);
						console.log(_resTodo);
						
						for(_nm in _res.mg){
							alert(_res.mg[_nm]);
						}
						if(_res.res=='exito'){
							mapaLocSelec('modmapa',_resSel,_resTodo);
						}
							
					
					}
				});	
			}
		}
	});	
}

var _map={};





function localizacionSeleccionada(_idLocalizacion){
	
	$(".tablarel").removeClass("selecta");	
	if(_idLocalizacion==''){return;}
	_srtIdFila="TdL"+_idLocalizacion;

	
	$("#"+_srtIdFila).addClass("selecta");
	_loc=_LOCs['l'+_idLocalizacion];

	
    $('html, body').animate({
        scrollTop: $("#"+_srtIdFila).offset().top-200
    }, 1000);
	    
	_fila=document.getElementById(_srtIdFila);
	_form=document.getElementById('modformubic');
	_topo=$("#"+_srtIdFila).offset().top;
	//_form.style.top=_topo;
	//$("#mapin").css({ top: _topo+'px' });
	_fila.appendChild(_form);
	_form.style.display='block';
	_form.setAttribute('idloc',_idLocalizacion);
	document.getElementById('mapin').innerHTML='';	
	mapaLocMuestra('mapin',_loc.x,_loc.y);
	        
}


</script>



<script type="text/javascript">

//funciones para la gestión de archivos uploads
var _contUp=0;
var _Cargas={};

function enviarSHP(_event,_this){	
	ValidarProcesarBoton();
	var files = _this.files;		
	for (i = 0; i < files.length; i++) {
		
    	_contUp++;
    	_Cargas[_contUp]='subiendo';
		var parametros = new FormData();
		parametros.append("upload",files[i]);
		parametros.append("idver",document.querySelector('#formcargaverest').getAttribute('idver'));
		parametros.append("crs",_this.parentNode.parentNode.querySelector('#crs').value);
		parametros.append("cont",_contUp);
		
		cargando(files[i].name,_contUp);
		
		//Llamamos a los puntos de la actividad
		$.ajax({
				data:  parametros,
				url:   'proc_shp_upload.php',
				type:  'post',
				processData: false, 
				contentType: false,
				success:  function (response) {
					var _res = $.parseJSON(response);
					
					if(_res.res=='exito'){
						archivoSubido(_res);
					}else{
						archivoFallido(_res)
					}
					
					_Cargas[_res.data.ncont]='terminado';
					
					_pendientes=0;
					for(_nn in _Cargas){
						if(_Cargas[_nn]=='subiendo'){_pendientes++;}
					}
					
				}
		});
	}
}


function intentarProcesarSHP(){
	_datos={};
	_datos["idcont"]=_idcont;
	_datos["crs"]=document.querySelector('#shp #crs').value;
	
	$.ajax({
		data: _datos,
		url:   'proc_shp_ddbb.php',
		type:  'post',
		success:  function (response){
			cargaContrato();
			var _res = $.parseJSON(response);
			//console.log(_res);
			for(_nmg in _res.mg){
				alert(_res.mg[_nmg]);
			}
			if(_res.res=='error'){
				_this.parentNode.innerHTML='ERROR AL ACTUALIZAR LOS DATOS';
			}else{
				cargaContrato();
			}
		}
	})		
}

function cargando(_nombre,_con){
	
	_ppp=document.createElement('p');
	_ppp.innerHTML=_nombre;
	_ppp.setAttribute('ncont',_con);
	_ppp.setAttribute('class','carga');
	
	document.querySelector('#shp #cargando').appendChild(_ppp);
	
}	
	
function archivoSubido(_res){
	
	document.querySelector('#shp #cargando p[ncont="'+_res.data.ncont+'"]').innerHTML+=' ...subido';
	document.querySelector('#shp #cargando p[ncont="'+_res.data.ncont+'"]').setAttribute('estado','subido');
	
}	

function archivoFallido(_res){
		
	document.querySelector('#shp #cargando p[ncont="'+_res.data.ncont+'"]').innerHTML+=' ...fallido';
	document.querySelector('#shp #cargando p[ncont="'+_res.data.ncont+'"]').setAttribute('estado','fallido');
	
}	
				
	
	
</script>


</body>
