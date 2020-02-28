<?php
session_start();

include 'dbconnect.php';

include 'functions.php';

?>
<html>
<head>
<style type="text/css">
#error {
	color: Red;
	text-align center;
	margin: 10px;
	padding: 10px;
}
#Wrapper {
	overflow: hidden;
	margin-bottom:25px;
}

#Navigation {
	width: 360px;
	float: left;
	font-size:30px;
	text-align: right;
	margin-left:25px;
	background-color: #b3b3b3;
	padding-right: 20px;
	padding-bottom: 500em;
	margin-bottom: -500em;
}

#ProfileForm {
	width: 900px;
	float:right;
	margin-right:25px;
	padding-bottom: 500em;
	margin-bottom: -500em;
}

#Navigation .selected {
	font-weight: bold;
}

#Navigation a {
	text-decoration:none;
	display: block;
	
}

#Navigation a:hover {
	background-color: green;
}

#Navigation ul {
	list-style-type: none;
	padding:0;
	
	margin-bottom:0;
}

table {
	background-color: #999;
	clear: both;
}

.CustInfo {
	background-color:#ffc0cb;
	clear: both;
	width: 90%;
	margin-left: auto;
	margin-right: auto;
}

.AddItem {
	background-color:#ffc0cb; 
	border:1px black solid;
	padding-top: 5px;
	padding-bottom: 5px;
}

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
	margin-bottom: 60px;
}

.LineButtons3 {
	width: 90%;
	margin-left: auto;
	margin-right: auto;
}

.AddItem select {
    width: 100%;
    box-sizing: border-box;
}

table input {
    width: 100%;
    box-sizing: border-box;
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

if($_SESSION['usertype']=='vendor'){
	header('Refresh: 5; URL=index.php'); //Will this work inside the body part???
	echo 'Sorry you don\'t have access to view this page! You will be redirected to our home page in 5 seconds.';
	die();
}


$cust = (isset($_SESSION['addorder']['cust']))? $_SESSION['addorder']['cust']: '';
$address = (isset($_SESSION['addorder']['address']))? $_SESSION['addorder']['address']: '';
$city = (isset($_SESSION['addorder']['city']))? $_SESSION['addorder']['city']: '';
$phone = (isset($_SESSION['addorder']['phone']))? $_SESSION['addorder']['phone']: ''; //Need to handle join date in processing script
$instructions = (isset($_SESSION['addorder']['instructions']))? $_SESSION['addorder']['instructions']: ''; //customer's instructions for the entire order as a whole

if((!isset($_SESSION['addorder']))&&($_SESSION['logged']==1)&&($_SESSION['usertype']=='customer')&&(isset($_GET['tab']))&&($_GET['tab']=='custinfo')){ //last two only needed because user can close browser & try to go to a specific tab that should come after the custinfo tab!
	$query = 'SELECT * FROM customer WHERE cust_id='.$_SESSION['userid'];
	$result = mysqli_query($db,$query) or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	
	$cust = $row['cust_name'];
	$address = $row['cust_address'];
	$city = $row['cust_city'];
	$phone = $row['cust_phone'];
}

?>

<div id="Wrapper">
<div id="Navigation">

<?php

echo '<ul>';

	echo (isset($_GET['tab'])&&($_GET['tab']=='cart'))? '<li class="selected">':'<li>';
	echo 'Shopping Cart';
	echo '</li>';
	echo (isset($_GET['tab'])&&($_GET['tab']=='custinfo'))? '<li class="selected">':'<li>';
	echo 'Customer Info';
	echo '</li>';
	echo (isset($_GET['tab'])&&($_GET['tab']=='confirm'))? '<li class="selected">':'<li>';
	echo 'Verify Order';
	echo '</li>';
	echo (isset($_GET['tab'])&&($_GET['tab']=='summary'))? '<li class="selected">':'<li>';
	echo 'Order Summary';
	echo '</li>';
	
echo '</ul>';

?>

	<ul>
<?php
if($_SESSION['usertype']=='customer'){
	echo '<li><a href="index.php">Return to Home page</a></li>';
} else {
	echo '<li><a href="admin_orderlist.php">Return to Order List</a></li>';
}
?>
	</ul>

</div>

<div id="ProfileForm">

<?php
//DISPLAY $ERROR WITH HIGHLIGHTS
if(isset($_GET['error'])&&($_GET['error']!='')){
	echo '<div id="error">'.$_GET['error'].'</div>';
}

switch($_GET['tab']){
	case 'cart':

	if(($_SESSION['usertype']=='admin')&&isset($_POST['submit'])&&($_POST['submit']=='Add')){
		$product_id = isset($_POST['product_id'])? $_POST['product_id']:'';
		if(empty($product_id)){
			$error = 'Please select an item to add';
		} elseif(isset($_SESSION['cart'][$product_id])){
			$error = 'The selected item already exists in the cart!';
		} else {
			$_SESSION['cart'][$product_id]['quantity'] = $_POST['quantity'];
			$_SESSION['cart'][$product_id]['cust_instructions'] = $_POST['cust_instructions'];
		}
	}

	if(isset($_POST['submit'])&&($_POST['submit']=='Empty Cart')){
		unset($_SESSION['cart']);
		unset($_SESSION['cart_total']);
	}
	
	if(isset($_POST['product_id'])&&(($_POST['submit']=='Update')||($_POST['submit']=='Delete'))){
		$product_id = $_POST['product_id'];
		if($_POST['submit']=='Update'){
			$_SESSION['cart'][$product_id]['quantity'] = $_POST['quantity'];
			$_SESSION['cart'][$product_id]['cust_instructions'] = $_POST['cust_instructions'];
		} else { //DELETE ITEM
			unset($_SESSION['cart'][$product_id]);
		}
	
	}
	
	echo '<h2>Shopping Cart</h2>';
	if(isset($_SESSION['cart'])&&(count($_SESSION['cart'])>0)){
?>
	<div class="LineButtons">
	<form action="ordercheckout.php?tab=cart" method="POST">
	<input type="submit" name="submit" value="Empty Cart" style="float: left;" onclick="return confirm('Are you sure you want to delete all contents?')"/>
	</form>
	
	<form action="ordercheckout.php?tab=custinfo" method="POST">
	<input type="submit" name="submit" value="Checkout" style="float: right;"/>
	</form>
	</div>
	
	<table border="1" width="90%" align="center">
	<tr>
		<th width="15%">Item</th>
		<th width="15%"></th>
		<th width="30%">Instructions</th>
		<th width="15%">Quantity</th>
		<th width="10%">Price</th>
		<th width="15%">Action</th>
	</tr>

	<?php
	$odd = true;
	$cart_total = 0;
	foreach ($_SESSION['cart'] as $product_id => $details){ //$_SESSION['cart'][$product_id] = $details
		//Run query for product information
		$query = 'SELECT * FROM products WHERE product_id='.$product_id;
		$result = mysqli_query($db,$query) or die(mysqli_error($db));
		$row = mysqli_fetch_assoc($result);
		
		echo ($odd==true)? '<tr class="odd_row">':'<tr class="even_row">';
		extract($row);
		echo '<td><b>'.$product_name.'</b><br/>(From: '.get_vendorname($vendor_id).')</td>';
		echo '<td><img src="images/products/thumbs/'.$product_id.'.jpg"></td>';
		echo '<form action="ordercheckout.php?tab=cart" method="POST">';
		echo '<input type="hidden" name="product_id" value="'.$product_id.'"/>';
		echo '<td>';
	//	echo '<textarea name="cust_instructions" rows="4">'.htmlspecialchars($_SESSION['cart'][$product_id]['cust_instructions']).'</textarea>';
		echo '<textarea name="cust_instructions" rows="4">'.htmlspecialchars($details['cust_instructions']).'</textarea>';
		echo '</td>';
		echo '<td>';
		echo '<select name="quantity">';
		for($num=1; $num<31; $num++){
			if($num==$details['quantity']){
				echo '<option value="'.$num.'" selected="selected">'.$num.'</option>';
			} else {
				echo '<option value="'.$num.'">'.$num.'</option>';
			}
		}
		echo '</select>';
		echo '</td>';
		echo '<td>';
		echo number_format(($details['quantity'])*$product_price,2);
		$cart_total = $cart_total +  (($details['quantity'])*$product_price);
		echo '</td>';
		echo '<td>';
		echo '<input type="submit" name="submit" value="Update" />';
		echo '<br/>';
		echo '<input type="submit" name="submit" value="Delete" onclick="return confirm(\'Are you sure you want to delete this record?\')" />';
		echo '</td>';
		echo '</form>';
		echo '</tr>';
		$odd = !$odd;
		}

	$_SESSION['cart_total'] = $cart_total; //without the delivery charges
	echo '<tr>';
	echo '<td colspan="4"><b>Total</b></td>';
	echo '<td><b>'.number_format($_SESSION['cart_total'],2).'</b></td><td></td>';
	echo '</tr>';
		
	echo '</table>';
	
	} else {
		echo '<div class="LineButtons3" style="color:red; font-weight:bold;">Your cart has no items in it!</div>';
		$_SESSION['cart_total'] = 0;
	}

if($_SESSION['usertype']=='admin'){

if((isset($_POST['submit'])&&($_POST['submit']=='Add New Item'))||isset($error)){
	echo '<h3>Add New Item</h3>';
	if(isset($error)){
		echo '<div class="LineButtons3" style="color:red; font-weight:bold;">'.$error.'</div>';
	}
?>
	<table width="90%" align="center" class="AddItem">
		<form action="ordercheckout.php?tab=cart" method="POST">
	<tr>
		<td width="14%"><b>Select Vendor</b></td>
		<td width="25%"><select id="vendor" name="vendor_id" onchange="populate(this.id,'product')">
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
		<td colspan="3"><textarea name="cust_instructions" rows="4" cols="63"></textarea></td>
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
	<form action="ordercheckout.php?tab=cart" method="POST">
	<input type="submit" name="submit" value="Add New Item" style="float: left;"/>
	</form>
	</div>
<?php
}

}

	break;

	case 'custinfo':
	?>
	<h2>Please Enter the Delivery Details</h2>
	
	<div class="LineButtons2">
	<form action="ordercheckout.php?tab=cart" method="POST">
	<input type="submit" name="submit" value="Go Back" style="float: left;"/>
	</form>
	
	<form action="ordercommit.php" method="POST">
	<input type="hidden" name="action" value="custinfo"/>
	<input type="submit" name="submit" value="Continue" style="float: right;" />
	</div>
	
	<table width="100%" style="background-color:#ffc0cb;">
	<tr>
		<td width="17%"><b>Customer Name</b></td>
		<td><input type="text" name="cust" value="<?php echo $cust; ?>" /></td>
		<td></td><td></td>
	</tr><tr>	
		<td><b>Address</b></td>
		<td colspan="3"><input type="text" name="address" value="<?php echo $address; ?>" /></td>
	</tr><tr>
		<td><b>City</b></td>
		<td><select name="city">
		<?php
		for($num=1; $num<16; $num++){
			$cityname = 'Colombo '.$num;
			if($cityname==$city){
				echo '<option value="'.$cityname.'" selected="selected">'.$cityname.'</option>';
			} else {
				echo '<option value="'.$cityname.'">'.$cityname.'</option>';
			}
		}
		?>
		</select></td>
		<td style="text-align: right;"><b>Phone</b></td>
		<td><input type="text" name="phone" value="<?php echo $phone; ?>" /></td>
		</tr>
		<tr height="80">
		<td><b>Special Instructions</b></td>
		<td colspan="3"><textarea name="instructions" rows="5"><?php echo htmlspecialchars($instructions); ?></textarea></td></tr>
	</table>
	</form>
	<?php
	break;
	
	case 'confirm':
	?>
	<h2>Please Confirm Your Order Details</h2>
	
	<div class="LineButtons">
	<form action="ordercheckout.php?tab=custinfo" method="POST">
	<input type="submit" name="submit" value="Go Back" style="float: left;"/>
	</form>
	
	<form action="ordercommit.php" method="POST">
	<input type="hidden" name="action" value="confirm"/>
	<input type="submit" name="submit" value="Confirm" style="float: right;" />
	</form>
	</div>
	
	<table border="1" width="90%" align="center">
	<tr>
		<th width="15%">Item</th>
		<th width="20%"></th>
		<th width="40%">Instructions</th>
		<th width="15%">Quantity</th>
		<th width="10%">Price</th>
	</tr>

	<?php
	$odd = true;
	$cart_total = 0;
	foreach ($_SESSION['cart'] as $product_id => $details){ //$_SESSION['cart'][$product_id] = $details
		//Run query for product information
		$query = 'SELECT * FROM products WHERE product_id='.$product_id;
		$result = mysqli_query($db,$query) or die(mysqli_error($db));
		$row = mysqli_fetch_assoc($result);
		
		echo ($odd==true)? '<tr class="odd_row">':'<tr class="even_row">';
		extract($row);
		echo '<td><b>'.$product_name.'</b><br/>(From: '.get_vendorname($vendor_id).')</td>';
		echo '<td><img src="images/products/thumbs/'.$product_id.'.jpg"></td>';
		echo '<td>';
	//	echo '<textarea name="cust_instructions" rows="4">'.htmlspecialchars($_SESSION['cart'][$product_id]['cust_instructions']).'</textarea>';
		echo '<b>'.$details['cust_instructions'].'</b></td>';
		echo '<td>'.$details['quantity'].'</td>';
		echo '<td>';
		echo number_format(($details['quantity'])*$product_price,2);
		$cart_total = $cart_total + (($details['quantity'])*$product_price);
		echo '</td>';
		echo '</tr>';
		$odd = !$odd;
		}

	//Need to display the total in an additional row
	$_SESSION['cart_total'] = $cart_total + 250; //Recalculating the total (for the last time) in order to use the most updated prices AND to include delivery!
	//Need to FORMAT the last row properly
	echo '<tr>';
	echo '<td colspan="4"><b>Delivery Cost</b></td>';
	echo '<td><b>'.number_format(250,2).'</b></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td colspan="4"><b>Total</b></td>';
	echo '<td><b>'.number_format($_SESSION['cart_total'],2).'</b></td>';
	echo '</tr>';
	
	
	echo '</table>';
	?>
	<h3>Customer Details</h3>
	<table class="CustInfo">
	<tr>
		<td width="20%"><b>Customer Name</b></td>
		<td><?php echo $cust; ?></td>
		<td></td><td></td>
	</tr><tr>	
		<td><b>Address</b></td>
		<td colspan="3"><?php echo $address; ?></td>
	</tr><tr>
		<td><b>City</b></td>
		<td><?php echo $city; ?></td>
		<td style="text-align: right;"><b>Phone</b></td>
		<td><?php echo $phone; ?></td>
		</tr>
		<tr height="80">
		<td><b>Special Instructions</b></td>
		<td colspan="3"><?php echo htmlspecialchars($instructions); ?></td></tr>
	</table>
	
	<?php
	break;
	
	case 'summary':
	//Need to give a reference number (order id) retrieved from the database - send it back to to this pg as a GET var
	//unset the SESSION addorder vars here!  We need them right up to this point!  
	
	?>
	<h2>Your order has been successfully submitted!</h2>
	<h3>Your Order reference number is: <?php echo $_GET['id']; ?></h3>
	
	<table border="1" width="90%" align="center">
	<tr>
		<th width="15%">Item</th>
		<th width="20%"></th>
		<th width="40%">Instructions</th>
		<th width="15%">Quantity</th>
		<th width="10%">Price</th>
	</tr>

	<?php
	$odd = true;
	
	foreach ($_SESSION['cart'] as $product_id => $details){ //$_SESSION['cart'][$product_id] = $details
		//Run query for product information
		$query = 'SELECT * FROM products WHERE product_id='.$product_id;
		$result = mysqli_query($db,$query) or die(mysqli_error($db));
		$row = mysqli_fetch_assoc($result);
		
		echo ($odd==true)? '<tr class="odd_row">':'<tr class="even_row">';
		extract($row);
		echo '<td><b>'.$product_name.'</b><br/>(From: '.get_vendorname($vendor_id).')</td>';
		echo '<td><img src="images/products/thumbs/'.$product_id.'.jpg"></td>';
		echo '<td>';
	//	echo '<textarea name="cust_instructions" rows="4">'.htmlspecialchars($_SESSION['cart'][$product_id]['cust_instructions']).'</textarea>';
		echo '<b>'.$details['cust_instructions'].'</b></td>';
		echo '<td>'.$details['quantity'].'</td>';
		echo '<td>';
		echo number_format(($details['quantity'])*$product_price,2);
		
		echo '</td>';
		echo '</tr>';
		$odd = !$odd;
		}

	//Need to display the total in an additional row
	
	//Need to FORMAT the last row properly
	echo '<tr>';
	echo '<td colspan="4"><b>Delivery Cost</b></td>';
	echo '<td><b>'.number_format(250,2).'</b></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td colspan="4"><b>Total</b></td>';
	echo '<td><b>'.number_format($_SESSION['cart_total'],2).'</b></td>';
	echo '</tr>';
	
	echo '</table>';
	?>
	<h3>Customer Details</h3>
	<table class="CustInfo">
	<tr>
		<td width="20%"><b>Customer Name</b></td>
		<td><?php echo $cust; ?></td>
		<td></td><td></td>
	</tr><tr>	
		<td><b>Address</b></td>
		<td colspan="3"><?php echo $address; ?></td>
	</tr><tr>
		<td><b>City</b></td>
		<td><?php echo $city; ?></td>
		<td style="text-align: right;"><b>Phone</b></td>
		<td><?php echo $phone; ?></td>
		</tr>
		<tr height="80">
		<td><b>Special Instructions</b></td>
		<td colspan="3"><?php echo htmlspecialchars($instructions); ?></td></tr>
	</table>
	
	<?php
	unset($_SESSION['addorder']);
	unset($_SESSION['cart']);
	unset($_SESSION['cart_total']);
	
	break;

}
?>
</div>
</div>

</body>
</html>

