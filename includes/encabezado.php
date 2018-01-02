<?php 

	header("Cache-control: private");
	include('./includes/ApplicationSettings.php');	
	
	session_start();
	if($_SESSION["UsuarioI"]<1){
		header('Location: ../extranet/login.php');
	}else{
		include('./includes/mySqonect.php');	
		include_once('./includes/cadenas.php');				
		include_once('./includes/fechas.php');		
	}
	
	
?>
