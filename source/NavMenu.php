<?php
//Not starting session as this is an inc. file

include 'dbconnect.php';

if(!isset($_SESSION['logged'])||($_SESSION['logged'] !=1)){
	$_SESSION['usertype']='customer';
	$_SESSION['logged']=0;
	unset($_SESSION['username']); //is it better to set these to blank??
	unset($_SESSION['userid']);
	unset($_SESSION['accesslevel']);
}


//The following local vars can be used in any script that starts with this script! Should we have more vars? (eg: for accesslevel etc)
$usertype = $_SESSION['usertype'];//let's try to simulate the user type with a hard coded variable
$logged = $_SESSION['logged'];
$accesslevel = isset($_SESSION['accesslevel'])? $_SESSION['accesslevel']:'';


$query = 'SELECT vendor_id, vendor_name
			FROM vendor
			WHERE vendor_isactive = "1"';

$result = mysqli_query($db,$query) or die(mysqli_error($db));

			
?>


<link href="style.css" rel="stylesheet">

<div id="topdiv">
<img src="Pinto.png" align="right">

<ul id="Login">
<?php
if($logged==0){
?>
	<li><a href="#">Login</a>
	<ul>
		<li><a href="login.php">Customer</a></li>
		<li><a href="login2.php">Admin</a></li>
		<li><a href="login2.php">Vendor</a></li>
	</ul>
	</li>
<?php
}
else{
	echo '<li><a href="logout.php">Logout</a></li>'; //simple script to be written
}
?>	
</ul>
<?php
if($logged==0){ //usertype is sure to be customer in this case, owing to the above controls! (But note that even though: logged=0 >>> customer, the reverse situation is not necessarily true!)
	echo	'<ul id="Register">';
	echo	'<li><a href="custprofile.php?tab=general">Register</a></li>';
	echo	'</ul>';
} else { //Not sure about this!
	echo	'<ul id="Register">';
	echo	'<li><a href="#">';
	echo 	$_SESSION['username'];
	echo 	($usertype=='vendor')? ' (from '.get_vendorname($_SESSION['userid']).')':'';
	echo 	'</a></li>';
	echo	'</ul>';
}
?>
<br>
<br>
<br>
<br>
<br>

<ul>
	<li><a href="index.php">Vendors</a>
	<ul>
	<?php
	while($row=mysqli_fetch_assoc($result)){
		extract($row);
		if($usertype=='customer'){
			echo '<li><a href="cust_productview.php?id='.$vendor_id.'">'.$vendor_name.'</a></li>'; //may need a separate url (script) if usertype is customer!
		} else {
			echo '<li><a href="vendor_productview.php?id='.$vendor_id.'">'.$vendor_name.'</a></li>'; //may need a separate url (script) if usertype is customer!
		}
	}
	?>
	</ul>
	</li>
	
	<li><a href="#">Account</a>
	<ul>
<?php 
	switch($usertype){
	case 'customer':
	echo '<li><a href="';
	echo ($logged==1)? 'custprofile.php?tab=view&id='.$_SESSION['userid'] : '#';
	echo '">Personal Profile</a></li>';
	break;
	
	case 'admin':
	echo '<li><a href="adminprofile.php?tab=view&id='.$_SESSION['userid'];
	echo '">Personal Profile</a></li>';
	break;
	
	case 'vendor':
	echo '<li><a href="vendorprofile.php?tab=view&id='.$_SESSION['userid'];
	echo '">Personal Profile</a></li>';
	break;
}
	
	if(($usertype=='admin')&&($accesslevel==1)){  //and if accesslevel=1?
		?>
		<li><a href="adminmngt.php">Manage Admins</a></li>
		<li><a href="vendormngt.php">Manage Vendors</a></li>
		<?php
	}
	
	if($usertype=='admin'){
		?>
		<li><a href="custmngt.php">Customer List</a></li>
		<?php
	}
	
	echo '</ul>';
	echo '</li>';
	

switch($usertype){
	case 'customer':
	echo (isset($_SESSION['cart'])&&(count($_SESSION['cart'])>0))? '<li class="highlight">':'<li>';
	echo '<a href="ordercheckout.php?tab=cart">Shopping Cart</a></li>';
	break;
	
	case 'admin':
	echo '<li><a href="admin_orderlist.php">View Orders</a></li>';
	break;
	
	case 'vendor':
	echo '<li><a href="vendor_ordertrack.php?id='.$_SESSION['userid'];
	echo '">View Orders</a></li>';
	break;
}


switch($usertype){
	case 'customer':
	echo '<li><a href="report_topproducts.php">Trending</a></li>';
	break;
	default:
	echo '<li><a href="#">Reports</a>';
	echo '<ul>';
	
	if($usertype=='admin'){
		echo '<li><a href="report_topvendors.php">Popular Vendors</a></li>';
		echo '<li><a href="report_topproducts.php">Popular items</a></li>';
		echo '<li><a href="#">Frequent Customers</a></li>';
	} else {
		echo '<li><a href="report_topproducts.php?vid='.$_SESSION['userid'].'">Popular items</a></li>';
	}
	echo '</ul>';
	echo '</li>';
	break;
}

?>

	<li><a href="#">Contact Us</a></li>
</ul>

</div>

<?php
unset($vendor_id);
unset($vendor_name);
unset($result);
?>