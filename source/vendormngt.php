<?php
session_start();

include 'dbconnect.php';


include 'functions.php';

if(!(($_SESSION['usertype']=='admin')&&($_SESSION['accesslevel']==1))){
	header('Refresh: 5; URL=index.php'); //Amazing that header redirect was possible AFTER the include above where output was surely echoed to browser!
	echo 'Sorry you don\'t have access to view this page! You will be redirected to our home page in 5 seconds.';
	die();
}

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

#Add_Button {
	text-align: center;
}

input[type=submit] {
    width: 5em;  
	height: 3em;
}

#Add_Button input[type=submit] {
	width: 12em;  
	height: 4em;
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

<h1 align="center">List of Active Vendors</h1>
<div id="Add_Button">
<form action="vendorprofile.php?tab=general" method="POST">
<input type="submit" name="submit" value="Add New Vendor"/>
</br>
</form>
</div>

<table border="0" width="90%" align="center">

<?php
$num = 1;

while($row=mysqli_fetch_assoc($result)){
	extract($row);
	echo (($num%3)==1)? '<tr>':'';
	echo '<td width="25%" align="center"><a href="vendor_productview.php?id='.$vendor_id.'"><img src="images/vendors/logos/'.$vendor_id.'.jpg" alt="Logo">';
	echo '<br>';
	echo $vendor_name.'</a></td>';
	echo '<td width="8.33%" style="padding-right:25px">';
	echo '<form action="vendorprofile.php?tab=view&id='.$vendor_id.'" method="POST">';
	echo '<input type="submit" name="submit" value="Edit"/>';
	echo '</br>';
	echo '<input type="submit" name="submit" value="Delete" onclick="return confirm(\'Are you sure you want to delete this record?\')" />';
	echo '</form>';
	echo '</td>';
	echo (($num%3)==0)? '</tr>':'';
	$num = $num + 1;
}
?>

</table>
</body>

</html>