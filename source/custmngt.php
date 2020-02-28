<?php
session_start();

include 'dbconnect.php';

include 'functions.php';

if(!($_SESSION['usertype']=='admin')){
	header('Refresh: 5; URL=index.php'); //Amazing that header redirect was possible AFTER the include above where output was surely echoed to browser!
	echo 'Sorry you don\'t have access to view this page! You will be redirected to our home page in 5 seconds.';
	die();
}

?>
<html>
<head>
<style type="text/css">

th {
	background-color: #999;
}
.odd_row {
	background-color: #EEE;
}
.even_row {
	background-color: #FFF;
}

#Add_Button {
	text-align: center;
}
/*
input[type=submit] {
    width: 5em;  
	height: 3em;
}
*/
#Add_Button input[type=submit] {
	width: 12em;  
	height: 4em;
}


</style>
</head>
<body>
<?php
//Body section from here
include 'NavMenu.php';

echo '<h1 align="center">Manage Customer Accounts</h1>';

$query = 'SELECT *
			FROM customer
			WHERE cust_isactive="1"';

$result = mysqli_query($db,$query) or die(mysqli_error($db));

?>
<div id="Add_Button">
<form action="custprofile.php?tab=general" method="POST">
<input type="submit" name="submit" value="Add New Customer"/>
</br>
</form>
</div>

<table border="1" width="90%" align="center">
<tr>
	<th width="20%">Name</th>
	<th width="20%">Address</th>
	<th width="50%">City</th>
	<th width="10%">Phone</th>
	<th width="20%"></th>
</tr>

<?php
$odd = true;
while($row=mysqli_fetch_assoc($result)){
	
	echo ($odd==true)? '<tr class="odd_row">':'<tr class="even_row">';
	extract($row);
	echo '<td>'.$cust_name.'</td>';
	echo '<td>'.$cust_address.'</td>';
	echo '<td>'.$cust_city.'</td>';
	echo '<td>'.$cust_phone.'</td>';
	echo '<td>';
	echo '<form action="custprofile.php?tab=view&id='.$cust_id.'" method="POST">';
	echo '<input type="submit" name="submit" value="Edit"/>';
	echo '</br>';
	echo '<input type="submit" name="submit" value="Delete" onclick="return confirm(\'Are you sure you want to delete this record?\')" />';
	echo '</form>';
	echo '</td>';
	echo '</tr>';
	$odd=!$odd;
}


echo '</table>';

?>
</body>
</html>