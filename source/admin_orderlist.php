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

h2, h3 {
	text-align: center;
}

h3 {
	margin-top: 30px;
}

.LineButtons {
	width: 90%;
	margin-left: auto;
	margin-right: auto;
	margin-bottom: 50px;
}

table input {
    width: 100%;
    box-sizing: border-box;
}

table {
	background-color: #999;
	clear: both;
}

th {
	background-color: #999;
}
.odd_row {
	background-color: #EEE;
}
.even_row {
	background-color: #FFF;
}


/*
input[type=submit] {
    width: 5em;  
	height: 3em;
}
*/

</style>
</head>
<body>
<?php

include 'NavMenu.php';

$status_filter = isset($_POST['status_filter'])? $_POST['status_filter']:'all';
$date_filter = isset($_POST['date_filter'])? $_POST['date_filter']:'24hr';
switch($status_filter){
	case 'all':
	$status_condition = 'orders.order_status IS NOT NULL';
	break;
	case 'open':
	$status_condition = '(orders.order_status = "Pending" OR orders.order_status = "Confirmed")';
	break;
	case 'pending':
	$status_condition = 'orders.order_status = "Pending"';
	break;
}
switch($date_filter){
	case 'all':
	$date_condition = 'orders.order_time IS NOT NULL';
	break;
	case '24hr':
	$date = date('Y-m-d H:i:s', strtotime('-24 hour'));
	$date_condition = 'orders.order_time >"'.$date.'"';
	break;
	case '12hr':
	$date = date('Y-m-d H:i:s', strtotime('-12 hour'));
	$date_condition = 'orders.order_time >"'.$date.'"';
	break;
}


?>
<h2>View Orders</h2>
<div>
<form action="admin_orderlist.php" method="POST">
<table width="90%" align="center">
<tr style="font-weight: bold;"><td width="30%">Filter Orders By: </td>
	<td width="15%">Order Status</td>
	<td width="10%"><select name="status_filter">
			<option value="all" <?php echo ($status_filter=='all')? 'selected="selected"':''; ?> >All</option>
			<option value="open" <?php echo ($status_filter=='open')? 'selected="selected"':''; ?> >All Open</option>
			<option value="pending" <?php echo ($status_filter=='pending')? 'selected="selected"':''; ?> >Pending Only</option>
		</select></td>
	<td width="15%">Time Range</td>
	<td width="10%"><select name="date_filter">
			<option value="all" <?php echo ($date_filter=='all')? 'selected="selected"':''; ?> >Any Time</option>
			<option value="24hr" <?php echo ($date_filter=='24hr')? 'selected="selected"':''; ?> >Past Day</option>
			<option value="12hr" <?php echo ($date_filter=='12hr')? 'selected="selected"':''; ?> >Past 12 Hrs</option>
		</select></td>
	<td><input type="submit" name="submit" value="Apply" /></td>	
</tr>
</table>
</form>
</div>

<div class="LineButtons">
	<form action="ordercheckout.php?tab=cart" method="POST">
	<input type="submit" name="submit" value="Add New Order" style="float: right;"/>
	</form>
</div>
	
<?php
$query = 'SELECT * FROM orders WHERE '.$status_condition.' AND '.$date_condition.' ORDER BY orders.order_time DESC';

$result = mysqli_query($db,$query) or die(mysqli_error($db));
?>

<table border="1" width="90%" align="center">
<tr>
	<th width="5%">Order ID</th>
	<th width="15%">Order Time</th>
	<th width="5%">Order Status</th>
	<th width="15%">Customer</th>
	<th width="7%">City</th>
	<th width="8%">Phone</th>
	<th width="25%">Instructions</th>
	<th width="10%">Total</th>
	<th></th>
</tr>

<?php
$odd = true;
while($row=mysqli_fetch_assoc($result)){
	
	echo ($odd==true)? '<tr class="odd_row">':'<tr class="even_row">';
	extract($row);
	echo '<td>'.$order_id.'</td>';
	echo '<td>'.$order_time.'</td>';
	echo '<td>'.$order_status.'</td>';
	echo '<td>'.$order_cust.'</td>';
	echo '<td>'.$order_city.'</td>';
	echo '<td>'.$order_phone.'</td>';
	echo '<td>'.htmlspecialchars($order_instructions).'</td>';
	echo '<td style="text-align:right;">'.$order_total.'</td>';
	echo '<td>';
	echo '<form action="orderprocess.php?id='.$order_id.'" method="POST">';
	echo '<input type="submit" name="submit" value="Process" />';
	echo '</form>';
	echo '</td>';
	echo '</tr>';
	$odd = !$odd;
}
?>
</table>


</body>
</html>







