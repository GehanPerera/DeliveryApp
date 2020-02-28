<?php
session_start();

//IDEA: vendor_id can be a session variable that in the case of admin, gets updated when a vendor name is pressed & this page is opened for the first time.  
//Already figured that the vendor_id has to be a session var if the user were a vendor
//OR pass the vendor_id in the url para to the processing page (for both user types) so that it can easily redirect back to this page with same url para!

include 'dbconnect.php';

include 'functions.php';

if(isset($_POST['submit'])){
	$product_id = $_POST['product_id'];
	if(($_POST['submit']=='Add to Cart')||($_POST['submit']=='Change Quantity')){
		$_SESSION['cart'][$product_id]['quantity'] = $_POST['quantity'];
		$_SESSION['cart'][$product_id]['cust_instructions'] = '';
	} else {
		unset($_SESSION['cart'][$product_id]);
	}
	
}

?>
<html>
<head>
<style type="text/css">

h2, h3 {
	text-align: center;
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

.AddToCartBtn {
	background-color: #66ff66;
}

.RemoveFromCartBtn {
	background-color: #66ccff;
	margin-left: 40px;
}

table select {
	width: 40px;
}
/*
table input[type=submit] {
	display: block;
	margin: auto;
}

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

if($_SESSION['usertype']!='customer'){
	header('Refresh: 5; URL=index.php'); //Amazing that header redirect was possible AFTER the include above where output was surely echoed to browser!
	echo 'Sorry you don\'t have access to view this page! You will be redirected to our home page in 5 seconds.';
	die();
}

$vendor_id = $_GET['id']; //at least for admin user.  Need to take this from session if the user is a vendor!

$query = 'SELECT *
			FROM products
			WHERE product_isactive="1" AND vendor_id ='.$vendor_id;

$result = mysqli_query($db,$query) or die(mysqli_error($db));

echo '<h2>'.get_vendorname($vendor_id).'</h2>';

?>

<img src="images/vendors/<?php echo $vendor_id; ?>.jpg" style="width: 70%; display: block; margin: 0 auto 0 auto;">
<br>

<?php
echo '<h3>'.get_vendorname($vendor_id).' - Product List</h3>';
?>

<table border="1" width="90%" align="center">
<tr>
	<th width="20%">Item</th>
	<th width="30%">Description</th>
	<th width="10%">Price</th>
	<th width="20%"></th>
	<th></th>
</tr>

<?php
$odd = true;
while($row=mysqli_fetch_assoc($result)){
	
	echo ($odd==true)? '<tr class="odd_row">':'<tr class="even_row">';
	extract($row);
	echo '<td>'.$product_name.'</td>';
	echo '<td>'.$product_desc.'</td>';
	echo '<td>'.$product_price.'</td>';
	echo '<td><img src="images/products/thumbs/'.$product_id.'.jpg"></td>';
	echo '<td>';
	echo '<form action="cust_productview.php?id='.$vendor_id.'" method="POST">';
	echo '<input type="hidden" name="product_id" value="'.$product_id.'"/>';
	//echo '<input type="hidden" name="vendor_id" value="'.$vendor_id.'"/>';
	if(!isset($_SESSION['cart'][$product_id])){
		echo '<select name="quantity">';
		for($num=1; $num<31; $num++){
				echo '<option value="'.$num.'">'.$num.'</option>';
		}
		echo '</select>';		
		echo '<input type="submit" class="AddToCartBtn" name="submit" value="Add to Cart"/>';
	} else {		
		echo '<select name="quantity">';
		for($num=1; $num<31; $num++){
			if($num==$_SESSION['cart'][$product_id]['quantity']){
				echo '<option value="'.$num.'" selected="selected">'.$num.'</option>';
			} else {
				echo '<option value="'.$num.'">'.$num.'</option>';
			}
		}
		echo '</select>';
		echo '<input type="submit" class="AddToCartBtn" name="submit" value="Change Quantity"/>';
		echo '<br/>';
		echo '<input type="submit" class="RemoveFromCartBtn" name="submit" value="Remove from Cart"/>';
	}
	
	echo '</form>';
	echo '</td>';
	echo '</tr>';
	$odd = !$odd;
	}

?>

</table>

