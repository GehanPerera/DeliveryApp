<?php
session_start();

//print_r($_SESSION);
//echo '</br>';

if(isset($_SESSION['cart'])){
	$cartset = true;
	$cart = $_SESSION['cart'];
} else {
	$cartset = false;
}

session_unset(); 
//session_destroy();

if($cartset==true){
	$_SESSION['cart']=$cart;
}

//print_r($_SESSION);


header('Refresh: 5; URL=index.php');
echo 'Logout successful. You will be redirected to our home page in 5 seconds.';
die();
			
?>