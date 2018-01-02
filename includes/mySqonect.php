<?php

if (!isset($_SESSION["AppSettings"])) {
	$_SESSION["AppSettings"] = new ApplicationSettings();
}
	$_SESSION["AppSettings"] = new ApplicationSettings();

	$ConecSIG = pg_connect("host=192.168.0.252 dbname=".$_SESSION["AppSettings"]->DATABASE_USERNAME." user=".$_SESSION["AppSettings"]->DATABASE_USERNAME." password=".$_SESSION["AppSettings"]->DATABASE_PASSWORD." port=5432")or die('connection failed');
	echo pg_errormessage($ConecSIG);	

?>
