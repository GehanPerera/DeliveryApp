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
	margin-bottom: 60px;
}

.LineButtons2 {
	width: 90%;
	margin-left: auto;
	margin-right: auto;
}

table select {
    width: 100%;
    box-sizing: border-box;
}


table input {
    width: 100%;
    box-sizing: border-box;
}

table {
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
<script type="text/javascript">
function populate(s1,s2){
	var s1 = document.getElementById(s1);
	var s2 = document.getElementById(s2);
	s2.options[0].selected=true;
	for(var i = 0; i<s2.options.length; i++){
		s2.options[i].style.display = 'none';
	}

	var vendorclass = 'v'+s1.value;
	var validoptions = document.getElementsByClassName(vendorclass);
    for (var i = 0; i < validoptions.length; i++){
        validoptions[i].style.display = 'block';
    }
}
</script>

</head>
<body>
<?php

include 'NavMenu.php';

$order_id = $_GET['id'];

if(isset($_POST['comments'])||isset($_POST['order_status'])){
	$status_updatetime = date("Y-m-d H:i:s");
	
	$query = 'UPDATE orders SET
				order_status = "'.$_POST['order_status'].'",
				comments = "'.$_POST['comments'].'",
				status_updatetime = "'.$status_updatetime.'"
				WHERE order_id='.$order_id;
	mysqli_query($db,$query) or die(mysqli_error($db));
	
}

if(isset($_POST['product_id'])&&($_POST['submit']!='Add')&&($_POST['submit']!='Process')&&($_POST['submit']!='Cancel')){ //Update or Delete an order_product record
	switch($_POST['submit']){
		case 'Update':
			$query = 'UPDATE order_product SET
						quantity ='.$_POST['quantity'].',
						pinto_comments ="'.$_POST['pinto_comments'].'"
						WHERE order_id='.$order_id.' AND product_id='.$_POST['product_id'];
						
			mysqli_query($db,$query) or die(mysqli_error($db));
			
			$price_diff = ($_POST['quantity']-$_POST['orig_quant'])*$_POST['unit_price'];
			$query = 'UPDATE orders SET
						order_total = order_total + '.$price_diff.' 
						WHERE order_id='.$order_id; 
			mysqli_query($db,$query) or die(mysqli_error($db));
			
			break;
		
		case 'Delete':
			$query = 'DELETE FROM order_product WHERE order_id='.$order_id.' AND product_id='.$_POST['product_id'];
			
			mysqli_query($db,$query) or die(mysqli_error($db));
			
			$price_diff = $_POST['orig_quant']*$_POST['unit_price'];
			$query = 'UPDATE orders SET
						order_total = order_total - '.$price_diff.' 
						WHERE order_id='.$order_id; 
			mysqli_query($db,$query) or die(mysqli_error($db));
			
			if(num_orderitems($order_id)<1){
			$query = 'UPDATE orders SET
						order_total = 0 
						WHERE order_id='.$order_id; 
			mysqli_query($db,$query) or die(mysqli_error($db));
			}
			break;
	}
}	

if(isset($_POST['submit'])&&($_POST['submit']=='Add')){
	$product_id = isset($_POST['product_id'])? $_POST['product_id']:'';
	if(!empty($product_id)){
	$query = 'SELECT * FROM order_product WHERE product_id='.$product_id.' AND order_id='.$order_id;
	$result = mysqli_query($db,$query) or die(mysqli_error($db));
	}
	if(!empty($product_id)&&(mysqli_num_rows($result)>0)){
		$error = 'This product is already part of the order. Please adjust the quantity as needed from the Item List table above.';
	} elseif(empty($product_id)) {
		$error = 'Please select an item to Add!';
	} else {
		$unit_price = get_price($product_id);
		$query = 'INSERT INTO order_product
					(order_id,product_id,unit_price,quantity,cust_instructions)
					VALUES
					('.$order_id.',
					'.$product_id.',
					'.$unit_price.',
					'.$_POST['quantity'].',
					"'.$_POST['cust_instructions'].'")';

		mysqli_query($db,$query) or die(mysqli_error($db));
		
		$price_diff = $_POST['quantity']*$unit_price;
		$query = 'UPDATE orders SET
						order_total = order_total + '.$price_diff.' 
						WHERE order_id='.$order_id; 
		mysqli_query($db,$query) or die(mysqli_error($db));
			
		if(num_orderitems($order_id)==1){
			$query = 'UPDATE orders SET
						order_total = order_total + 250 
						WHERE order_id='.$order_id; 
			mysqli_query($db,$query) or die(mysqli_error($db));
			}	
	}

}

$query = 'SELECT * FROM orders WHERE order_id='.$order_id;

$result = mysqli_query($db,$query) or die(mysqli_error($db));

$row = mysqli_fetch_assoc($result);
extract($row);
?>
<h3>Customer Details</h3>
	<div class="LineButtons">
	<form action="admin_orderlist.php" method="POST">
	<input type="submit" name="submit" value="Go Back to Order List" style="float: left;"/>
	</form>
	</div>

	<table width="90%" align="center" cellpadding="5">
	<tr>
		<td width="14%"><b>Order ID:</b></td>
		<td width="20%"><?php echo $order_id; ?></td>
		<td width="12%"><b>Customer Name</b></td>
		<td><?php echo $order_cust; ?><br/><?php echo ($cust_id===NULL)? '':'(Cust ID: '.$cust_id.')'; ?></td>
		<td width="10%"><b>Order Time:</b></td>
		<td><?php echo $order_time; ?></td>
	</tr><tr>	
		<td><b>Address</b></td>
		<td colspan="3"><?php echo $order_address; ?></td>
		<td><b>City</b></td>
		<td><?php echo $order_city; ?></td>
	</tr><tr>
		<tr height="80">
		<td><b>Special Instructions</b></td>
		<td colspan="3"><?php echo htmlspecialchars($order_instructions); ?></td>
		<td><b>Phone</b></td>
		<td><?php echo $order_phone; ?></td>
	</tr></table>
	
	<table width="90%" align="center">
	<tr style="vertical-align:top;">
		<form action="orderprocess.php?id=<?php echo $order_id; ?>" method="POST">
		<td width="14%"><b>Enter Comments:</b></td>
		<td width="30%"><textarea name="comments" rows="4" cols="67"><?php echo htmlspecialchars($comments); ?></textarea></td>
		<td style="text-align:center;"><b>Order Status:</b></td>
		<td><select name="order_status">
			<option value="Pending" <?php echo ($order_status=='Pending')? 'selected="selected"':''; ?> >Pending</option>
			<option value="Confirmed" <?php echo ($order_status=='Confirmed')? 'selected="selected"':''; ?> >Confirmed</option>
			<option value="Cancelled" <?php echo ($order_status=='Cancelled')? 'selected="selected"':''; ?> >Cancelled</option>
			<option value="Delivered" <?php echo ($order_status=='Delivered')? 'selected="selected"':''; ?> >Delivered</option>
			</select>
		</td><td>
			<input type="submit" name="submit" value="Update" />
		</td>
		</form>
	</tr>
	</table>

<?php
$query = 'SELECT order_product.*, products.product_id, products.product_name, products.product_price, products.vendor_id FROM order_product, products 
				WHERE order_product.product_id=products.product_id AND order_id='.$order_id;	
$result = mysqli_query($db,$query) or die(mysqli_error($db));
	
?>	
<h3>Items Ordered</h3>
	<table border="1" width="100%" align="center" style="background-color: #999;">
	<tr>
		<th width="10%">Item</th>
		<th width="20%"></th>
		<th width="20%">Instructions</th>
		<th width="7%">Vendor Response</th>
		<th width="15%">Vendor Comments</th>
		<th width="8%">Price</th>
		<th width="8%">Quantity</th>
		<th width="15%">Pinto Comments</th>
		<th width="7%">Action</th>	
	</tr>
<?php
if(mysqli_num_rows($result)<1){
	echo '<tr style="color:red; font-weight:bold;">';
	echo '<td colspan="8">There are no remaining items! Please add a new item OR change the order status to "Cancelled"';
	echo '</td></tr>';
}

$odd = true;
while($row=mysqli_fetch_assoc($result)){
	
	echo ($odd==true)? '<tr class="odd_row">':'<tr class="even_row">';
	extract($row);
	echo '<td><b>'.$product_name.'</b><br/>(From: '.get_vendorname($vendor_id).')</td>';
	echo '<td><img src="images/products/thumbs/'.$product_id.'.jpg"></td>';
	echo '<td>'.$cust_instructions.'</td>';
	echo ($vendor_response=='Reject')? '<td style="color:red;">':'<td>';
	echo '<b>'.$vendor_response.'</b></td>';
	echo '<td>'.$vendor_comments.'</td>';
	echo '<td>'.number_format($quantity*$unit_price,2).'</td>'; //NOT DOING THIS NOW: Need to change the unit_price in the order_product table itself IFF quantity is revised!
	
	if(($order_status=='Pending')||($order_status=='Confirmed')){
		echo '<form action="orderprocess.php?id='.$order_id.'" method="POST">';
		echo '<input type="hidden" name="product_id" value="'.$product_id.'"/>';
		echo '<input type="hidden" name="orig_quant" value="'.$quantity.'"/>';
		echo '<input type="hidden" name="unit_price" value="'.$unit_price.'"/>';
	//	echo '<input type="hidden" name="current_unit_price" value="'.$product_price.'"/>';
		echo '<td>';
		echo '<select name="quantity">';
		for($num=1; $num<31; $num++){
			if($num==$quantity){
				echo '<option value="'.$num.'" selected="selected">'.$num.'</option>';
			} else {
				echo '<option value="'.$num.'">'.$num.'</option>';
			}
		}
		echo '</select>';
		echo '</td>';
		echo '<td>';
		echo '<textarea name="pinto_comments" rows="3">'.htmlspecialchars($pinto_comments).'</textarea>';
		echo '</td>';
		echo '<td>';
		echo '<input type="submit" name="submit" value="Update" />';
		echo '<br/>';
		echo '<input type="submit" name="submit" value="Delete" onclick="return confirm(\'Are you sure you want to delete this record?\')"/>';
		echo '</td>';
		echo '</form>';
	} else {
		echo '<td>'.$quantity.'</td>';
		echo '<td>'.$pinto_comments.'</td><td></td>';		
	}
	
	echo '</tr>';
	$odd = !$odd;
}

if(mysqli_num_rows($result)>0){
	echo '<tr>';
	echo '<td colspan="5"><b>Delivery Cost</b></td>';
	echo '<td><b>'.number_format(250,2).'</b></td>';
	echo '<td colspan="3"></td>';
	echo '</tr>';
}
	echo '<tr>';
	echo '<td colspan="5"><b>Total</b></td>';
	echo '<td><b>'.number_format($order_total,2).'</b></td>';
	echo '<td colspan="3"></td>';
	echo '</tr>';


echo '</table>';

if(!(($order_status=='Pending')||($order_status=='Confirmed'))){
	die();
}

if((isset($_POST['submit'])&&($_POST['submit']=='Add New Item'))||isset($error)){
	echo '<h3>Add New Item</h3>';
	if(isset($error)){
		echo '<div class="LineButtons2" style="color:red; font-weight:bold;">'.$error.'</div>';
	}
?>
	<table width="90%" align="center">
		<form action="orderprocess.php?id=<?php echo $order_id; ?>" method="POST">
	<tr>
		<td><b>Select Vendor</b></td>
		<td width="30%"><select id="vendor" name="vendor_id" onchange="populate(this.id,'product')">
			<option value=""></option>
<?php
	$query = 'SELECT vendor_id, vendor_name
			FROM vendor
			WHERE vendor_isactive = "1"';

	$result = mysqli_query($db,$query) or die(mysqli_error($db));
	while($row=mysqli_fetch_assoc($result)){
		extract($row);
		echo '<option value="'.$vendor_id.'">'.$vendor_name.'</option>';
	}
?>
			</select></td>
		<td width="10%" style="text-align:right;"><b>Select Item</b></td>
		<td width="30%"><select id="product" name="product_id">
			<option value=""></option>
<?php
	$query = 'SELECT *
			FROM products
			WHERE product_isactive="1"';

	$result = mysqli_query($db,$query) or die(mysqli_error($db));
	while($row=mysqli_fetch_assoc($result)){
		extract($row);
		echo '<option value="'.$product_id.'" class="v'.$vendor_id.'">'.$product_name.'</option>'; //adding class is just experiemntal - perhaps we could hide products of all other vendors when a particular vendor has been selected?
	}
?>
			</select></td>
		<td style="text-align:right;"><b>Select Quantity</b></td>
		<td><select name="quantity">
<?php
	for($num=1; $num<31; $num++){
			echo '<option value="'.$num.'">'.$num.'</option>';
	}
?>	
			</select></td>
	</tr><tr>
		<td><b>Customer Instructions</b></td>
		<td colspan="3"><textarea name="cust_instructions" rows="4" cols="107"></textarea></td>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td><input type="submit" name="submit" value="Cancel" /></td>
		<td colspan="3"></td>
		<td colspan="2"><input type="submit" name="submit" value="Add" /></td>
	</tr>
	</form>
	</table>
<?php
//BUT WE SHOULD IDEALLY DISPLAY THE PRICE DYNAMICALLY ONCE A PRODUCT HAS BEEN SELECTED
} else {
?>
	<div class="LineButtons">
	<form action="orderprocess.php?id=<?php echo $order_id; ?>" method="POST">
	<input type="submit" name="submit" value="Add New Item" style="float: left;"/>
	</form>
	</div>
<?php
}

		
	
		
