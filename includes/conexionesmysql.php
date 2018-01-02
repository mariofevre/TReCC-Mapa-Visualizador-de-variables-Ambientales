<?php


if (!isset($_SESSION['sigsao'])) {
	$_SESSION['sigsao'] = new ApplicationSettings();
}

//echo $_SESSION['sigsao']->DATABASE_HOST.$_SESSION['sigsao']->DATABASE_USERNAME.	$_SESSION['sigsao']->DATABASE_PASSWORD;
$Conec1 = mysql_connect(
	$_SESSION['sigsao']->DATABASE_HOST, 
	$_SESSION['sigsao']->DATABASE_USERNAME, 
	$_SESSION['sigsao']->DATABASE_PASSWORD
)or die(
	"falló la conexión: ".mysql_error()
);

mysql_select_db(
	$_SESSION['sigsao']->DATABASE_NAME,$Conec1
)or die(
	mysql_error()
);
?>
