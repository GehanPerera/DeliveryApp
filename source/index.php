<?php
session_start();

include 'dbconnect.php';

include 'functions.php';

?>
<html>
<head>
<style type="text/css">
td {
	font-weight: bold;
	font-size:25px;
}

td a {
	text-decoration: none;
	color: black;
	display: block;
	height: 100%;
}

td a:hover {
	background-color: red;
}
</style>
</head>

<body>
<?php
include 'NavMenu.php';

$query = 'SELECT vendor_id, vendor_name
			FROM vendor
			WHERE vendor_isactive = "1"';

$result = mysqli_query($db,$query) or die(mysqli_error($db));

?>

<img src="images/Pinto_largeimg.jpg" style="width: 80%; display: block; margin: 0 auto 0 auto;">
<br>

<h1 align="center">List of participating Vendors</h1>

<table border="0" width="90%" align="right">

<?php
$num = 1;

while($row=mysqli_fetch_assoc($result)){
	extract($row);
	echo (($num%3)==1)? '<tr>':'';
	echo '<td width="25%" align="center">';
	echo ($_SESSION['usertype']=='customer')? '<a href="cust_productview.php?id='.$vendor_id.'">':'<a href="vendor_productview.php?id='.$vendor_id.'">';
	echo '<img src="images/vendors/logos/'.$vendor_id.'.jpg" alt="Logo">';
	echo '<br>';
	echo $vendor_name.'</a></td>';
	echo '<td width="8.33%"></td>';
	echo (($num%3)==0)? '</tr>':'';
	$num = $num + 1;
}
?>

</table>
</body>

</html>