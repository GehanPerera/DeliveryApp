<?php
//There should also be a function to get vendor_id given a product_id

function get_vendorname($id){	
	global $db;
	$query = 'SELECT vendor_name FROM vendor WHERE vendor_id='.$id;
	$result = mysqli_query($db,$query)or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	extract($row);
	return $vendor_name;
}

function get_productname($id){	
	global $db;
	$query = 'SELECT product_name FROM products WHERE product_id='.$id;
	$result = mysqli_query($db,$query)or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	extract($row);
	return $product_name;
}

function get_price($id){	
	global $db;
	$query = 'SELECT product_price FROM products WHERE product_id='.$id;
	$result = mysqli_query($db,$query)or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	extract($row);
	return $product_price;
}

function num_orderitems($id){
	global $db;
	$query = 'SELECT * FROM order_product WHERE order_id='.$id;
	$result =mysqli_query($db,$query)or die(mysqli_error($db));
	$num = mysqli_num_rows($result);
	return $num;
}	

function get_vendorid($pid){	
	global $db;
	$query = 'SELECT vendor_id FROM products WHERE product_id='.$pid;
	$result = mysqli_query($db,$query)or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	extract($row);
	return $vendor_id;
}

function get_vendor_un($id){
	global $db;
	$query = 'SELECT vendor_username 
					FROM vendor_login
						WHERE vendor_id='.$id;
		
	$result = mysqli_query($db,$query)or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	extract($row);
	return $vendor_username;
}

function get_admin_un($id){
	global $db;
	$query = 'SELECT admin_username 
					FROM admin_login
						WHERE admin_id='.$id;
		
	$result = mysqli_query($db,$query)or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	extract($row);
	return $admin_username;
}	

function get_cust_un($id){
	global $db;
	$query = 'SELECT cust_username 
					FROM cust_login
						WHERE cust_id='.$id;
		
	$result = mysqli_query($db,$query) or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	extract($row);
	return $cust_username;
}	


?>