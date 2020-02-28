<?php
$dbserver = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "pinto";

$db = mysqli_connect($dbserver,$dbuser,$dbpass) or die('cannot connect to the server'); 

mysqli_select_db($db,$dbname) or die(mysqli_error($db));

?>