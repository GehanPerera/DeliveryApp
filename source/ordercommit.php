<?php
session_start();

include 'dbconnect.php';

include 'functions.php';


switch($_POST['action']) {
	
	case 'custinfo':
	
	$error = array();
	
	$cust = isset($_POST['cust'])? trim($_POST['cust']):'';
	if(empty($cust)){
		$error[] = urlencode('Please enter your name ');
	}
	
	$address = isset($_POST['address'])? trim($_POST['address']):'';
	if(empty($address)){
		$error[] = urlencode('Address cannot be blank. ');
	}

	$city = isset($_POST['city'])? trim($_POST['city']):'';
	if(empty($city)){
		$error[] = urlencode('Please select a city. ');
	}
	
	$phone = isset($_POST['phone'])? trim($_POST['phone']):'';
	$phone = str_replace('-','',$phone);
	$phone = str_replace('(','',$phone);
	$phone = str_replace(')','',$phone);
	$phone = str_replace(' ','',$phone);
	if(!preg_match('|^\d{10}$|',$phone)){
		$error[] = urlencode('Please enter a valid phone number. ');
	} else {
		$area = substr($phone,0,3);
		$rest = substr($phone,3);
		$phone = '('.$area.')'.$rest;
	}
	
	$instructions = isset($_POST['instructions'])? trim($_POST['instructions']):'';  
	//Okay for instructions to be empty!

	if(!empty($error)){
		header('Location:ordercheckout.php?tab=custinfo&error='.join($error,urlencode('<br/>'))); //Do we need die() after this line?
		die();
	}
	
	//Take values from the submitted form fields & store in the session vars
	//First unset any existing session vars
	unset($_SESSION['addorder']);
	//$join_date = date('Y-m-d');
	
	$_SESSION['addorder']['cust'] = $cust;
	$_SESSION['addorder']['address'] = $address;
	$_SESSION['addorder']['city'] = $city;
	$_SESSION['addorder']['phone'] = $phone;
	$_SESSION['addorder']['instructions'] = $instructions;
	//$_SESSION['addorder']['cust_joindate'] = $join_date;
	
	header('Location:ordercheckout.php?tab=confirm');  //go to next part of the form
	//do we need die() here?
	die();
	
	break;  //End of 'custinfo' case
	
	case 'confirm':
	//Order time
	$order_time = date("Y-m-d H:i:s");
	
	//First add the orderinfo to the orders table
	$query = 'INSERT INTO orders
					(order_cust,order_address,order_city,order_phone,order_instructions,order_time,order_total)
					VALUES
					("'.$_SESSION['addorder']['cust'].'",
					"'.$_SESSION['addorder']['address'].'",
					"'.$_SESSION['addorder']['city'].'",
					"'.$_SESSION['addorder']['phone'].'",
					"'.$_SESSION['addorder']['instructions'].'",
					"'.$order_time.'",
					'.$_SESSION['cart_total'].')';

		mysqli_query($db,$query) or die(mysqli_error($db));
		$order_id = mysqli_insert_id($db);
		
	
//Handle cust_id if logged==1
	if(($_SESSION['usertype']=='customer')&&isset($_SESSION['logged'])&&($_SESSION['logged']==1)){
		$query = 'UPDATE orders SET
				cust_id ='.$_SESSION['userid'].'
				WHERE order_id='.$order_id;
		mysqli_query($db,$query) or die(mysqli_error($db));
	}
	
	$cart_total = 0;
	foreach ($_SESSION['cart'] as $product_id => $details){ //$_SESSION['cart'][$product_id] = $details
		$unit_price = get_price($product_id);
		$cart_total = $cart_total + (($details['quantity'])*$unit_price);
		$query = 'INSERT INTO order_product
					(order_id,product_id,unit_price,quantity,cust_instructions)
					VALUES
					('.$order_id.',
					'.$product_id.',
					'.$unit_price.',
					'.$details['quantity'].',
					"'.$details['cust_instructions'].'")';

		mysqli_query($db,$query) or die(mysqli_error($db));
	}
	
	$cart_total = $cart_total + 250;
	
	$error = array();

	if($_SESSION['cart_total']!=$cart_total){
		$error[] = urlencode('Order could not be submitted most likely due to a recent price change.  Please confirm order again. ');
	}
	
	if(empty($error)){ //Fill the necessary Session vars
		//Need to send the order_id as a url para!
			header('Refresh: 5; URL=ordercheckout.php?tab=summary&id='.$order_id);
			echo 'Your order has been successfully saved!<br>';
			echo 'You will be redirected to your order summary in 5 seconds.';
			die();
		} else {
			$query = 'DELETE FROM orders WHERE order_id='.$order_id;
			mysqli_query($db,$query) or die(mysqli_error($db));
			$query = 'DELETE FROM order_product WHERE order_id='.$order_id;
			mysqli_query($db,$query) or die(mysqli_error($db));
			
			header('Location:ordercheckout.php?tab=confirm&error='.join($error,urlencode('<br/>'))); //Do we need die() after this line?
			die();
		}
		
	break;
	
}




?>
	
	
	
	