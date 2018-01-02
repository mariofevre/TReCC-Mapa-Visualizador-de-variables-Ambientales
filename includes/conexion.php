<?php 

header("Cache-control: private");
  include('./includes/settings.php');
  if($_SESSION['is_open'] != 'TRUE'){
	  session_start();  
	  $_SESSION['is_open'] = 'TRUE';
	  include('./includes/conexionesmysql.php');
  }
?>
