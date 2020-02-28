<?php
session_start();

include 'dbconnect.php';

include 'functions.php';

if(!(isset($_GET['id'])&&($_SESSION['usertype']=='vendor')&&($_SESSION['userid']==$_GET['id']))){
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
/*
h3 {
	margin-top: 30px;
}

.LineButtons {
	width: 90%;
	margin-left: auto;
	margin-right: auto;
	margin-bottom: 60px;
}
*/
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

$vendor_id = $_GET['id'];
//Need authentication controls to prevent a vendor from changing the get variable & seeing information pertinent to another vendor!


if(isset($_POST['product_id'])){
	if(empty($_POST['vendor_response'])){
		$query = 'UPDATE order_product SET
					vendor_response = NULL,
					vendor_comments = "'.$_POST['vendor_comments'].'"
					WHERE order_id='.$_POST['order_id'].' AND product_id='.$_POST['product_id'];
	} else {
		$query = 'UPDATE order_product SET
					vendor_response = "'.$_POST['vendor_response'].'",
					vendor_comments = "'.$_POST['vendor_comments'].'"
					WHERE order_id='.$_POST['order_id'].' AND product_id='.$_POST['product_id'];
	}
	
	mysqli_query($db,$query) or die(mysqli_error($db));
}	

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

//Note: for later we need 2 hidden variables in each inline update/delete form to represent the current filter selections
?>
<h2>View Orders</h2>
<div>
<form action="vendor_ordertrack.php?id=<?php echo $vendor_id; ?>" method="POST">
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
<?php
$query = 'SELECT orders.order_id, orders.order_time, orders.order_status, order_product.order_id, order_product.product_id, order_product.quantity, order_product.cust_instructions, order_product.vendor_response, order_product.vendor_comments, order_product.pinto_comments, products.product_id, products.product_name, products.vendor_id
			FROM orders, order_product, products
				WHERE '.$status_condition.' AND '.$date_condition.' AND orders.order_id=order_product.order_id AND products.product_id=order_product.product_id
						AND products.vendor_id='.$vendor_id.
								' ORDER BY orders.order_time DESC';

$result = mysqli_query($db,$query) or die(mysqli_error($db));
?>

<table border="1" width="100%" align="center">
<tr>
	<th width="5%">Order ID</th>
	<th width="15%">Order Time</th>
	<th width="5%">Order Status</th>
	<th width="17%">Product</th>
	<th width="5%">Quantity</th>
	<th width="17%">Instructions</th>
	<th width="15%">Pinto Comments</th>
	<th width="10%">Enter Response</th>
	<th width="15%">Enter Comments</th>
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
	echo '<td>'.$product_name.'</td>';
	echo '<td>'.$quantity.'</td>';
	echo '<td>'.$cust_instructions.'</td>';
	echo '<td>'.$pinto_comments.'</td>';
	if($order_status=='Pending'){
		echo '<form action="vendor_ordertrack.php?id='.$vendor_id.'" method="POST">';
		echo '<input type="hidden" name="product_id" value="'.$product_id.'"/>';
		echo '<input type="hidden" name="order_id" value="'.$order_id.'"/>';
		echo '<input type="hidden" name="status_filter" value="'.$status_filter.'"/>';
		echo '<input type="hidden" name="date_filter" value="'.$date_filter.'"/>';
		echo '<td>';
		echo '<select name="vendor_response">';
		echo '<option value="" ';
		echo ($vendor_response===NULL)? 'selected="selected"':''; // Alternatively, we can use: is_null($result['column'])
		echo '>';
		echo 'No Response';
		echo '</option>';
		echo '<option value="Accept" ';
		echo ($vendor_response=='Accept')? 'selected="selected"':'';
		echo '>';
		echo 'Accept';
		echo '</option>';
		echo '<option value="Reject" ';
		echo ($vendor_response=='Reject')? 'selected="selected"':'';
		echo '>';
		echo 'Reject';
		echo '</option>';
		echo '</select>';
		echo '</td>';
		echo '<td>';
		echo '<textarea name="vendor_comments" rows="4">'.htmlspecialchars($vendor_comments).'</textarea>';
		echo '</td>';
		echo '<td>';
		echo '<input type="submit" name="submit" value="Update" />';
		echo '</td>';
		echo '</form>';
	} else {
		echo '<td>'.$vendor_response.'</td>';
		echo '<td>'.$vendor_comments.'</td>';
		echo '<td></td>';
	}
	echo '</tr>';
	$odd = !$odd;
}
?>
</table>

							






