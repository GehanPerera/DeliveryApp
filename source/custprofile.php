<?php
session_start();

include 'dbconnect.php';

include 'functions.php';
//Redirect to the "view" tab if $_GET['tab'] is not set?

if(!(($_SESSION['usertype']=='admin')||(($_SESSION['usertype']=='customer')&&($_SESSION['logged']!=1)&&(!isset($_GET['id'])))||(($_SESSION['usertype']=='customer')&&($_SESSION['logged']==1)&&isset($_GET['id'])&&($_SESSION['userid']==$_GET['id'])))){
		header('Refresh: 5; URL=index.php'); //Amazing that header redirect was possible AFTER the include above where output was surely echoed to browser!
		echo 'Sorry you don\'t have access to view this page! You will be redirected to our home page in 5 seconds.';
		die();
}


if(isset($_POST['submit'])&&($_POST['submit']=='Delete')){
	$cust_id = $_GET['id'];
	
	$cust_dateinactive = date("Y-m-d H:i:s");
	
	$query = 'UPDATE customer SET
				cust_isactive = "0",
				cust_dateinactive = "'.$cust_dateinactive.'"
				WHERE cust_id='.$cust_id;
	mysqli_query($db,$query) or die(mysqli_error($db));
	
	$query = 'DELETE FROM cust_login WHERE cust_id='.$cust_id; //Deleted cust user should not be allowed to login!
	mysqli_query($db,$query) or die(mysqli_error($db));
	
	header('Refresh: 5; URL=custmngt.php');
	echo 'User has been successfully deleted!';
	die();
	
}

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

input {
    width: 100%;
    box-sizing: border-box;
}

</style>
</head>

<body>
<?php
include 'NavMenu.php';

//Ck session usertypes & redirect if required (this includes redirecting a cust whose userid is not matching)
//Setting Default Values: Most of the parts under 'View' & 'General' tabs are common for both Edit & Add situations!  
if(isset($_GET['id'])){ //Editing situation (user can be cust or super cust)
	$cust_id = $_GET['id']; 
	$query = 'SELECT * FROM customer WHERE cust_id='.$cust_id;
	$result = mysqli_query($db,$query) or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	extract($row);
} else{ //Adding situation
	$cust_name = (isset($_SESSION['addcust']['cust_name']))? $_SESSION['addcust']['cust_name']: '';
	$cust_address = (isset($_SESSION['addcust']['cust_address']))? $_SESSION['addcust']['cust_address']: '';
	$cust_city = (isset($_SESSION['addcust']['cust_city']))? $_SESSION['addcust']['cust_city']: '';
	$cust_phone = (isset($_SESSION['addcust']['cust_phone']))? $_SESSION['addcust']['cust_phone']: ''; //Need to handle join date in processing script
	$cust_email = (isset($_SESSION['addcust']['cust_email']))? $_SESSION['addcust']['cust_email']: '';
	$comments = (isset($_SESSION['addcust']['comments']))? $_SESSION['addcust']['comments']: ''; //Rename this field in DB?
		
}		  
	
?>
<div id="Wrapper">
<div id="Navigation">

<?php

echo '<ul>';

	echo (isset($_GET['tab'])&&($_GET['tab']=='view'))? '<li class="selected">':'<li>';
	echo (isset($_GET['id']))? '<a href="custprofile.php?tab=view&id='.$_GET['id'].'">View Profile</a>':'View Profile';
	echo '</li>';
	echo (isset($_GET['tab'])&&($_GET['tab']=='general'))? '<li class="selected">':'<li>';
	echo (isset($_GET['id']))? '<a href="custprofile.php?tab=general&id='.$_GET['id'].'">Edit General Info</a>':'Edit General Info';
	echo '</li>';
	echo (isset($_GET['tab'])&&($_GET['tab']=='pwrd'))? '<li class="selected">':'<li>';
	echo (isset($_GET['id']))? '<a href="custprofile.php?tab=pwrd&id='.$_GET['id'].'">Change Password</a>':'Login Info';
	echo '</li>';
	
echo '</ul>';
//echo '</br></br>';

if($_SESSION['usertype']=='admin'){  //Even a level 2 admin user should be allowed to edit or add/delete customers
echo '<ul>';
	echo '<li><a href="custmngt.php">Return to Customer List</a></li>';
echo '</ul>';
} else {
echo '<ul>';
	echo '<li><a href="index.php">Return to Home page</a></li>';
echo '</ul>';
}

echo '</div>';
	
?>

<div id="ProfileForm">	

<?php
if(isset($_GET['id'])){ //Editing situation (user can be cust or super cust)
	echo '<h2>'.$cust_name.'</h2>';
}

//DISPLAY $ERROR WITH HIGHLIGHTS
if(isset($_GET['error'])&&($_GET['error']!='')){
	echo '<div id="error">'.$_GET['error'].'</div>';
}

switch($_GET['tab']){
	case 'view':
	
	echo '<table border="1" width="100%">';	
	echo '<tr>';
	echo '<td><b>Customer Name</b></td>';
	echo '<td colspan="3">'.$cust_name.'</td></tr>';
	echo '<tr><td><b>Address</b></td>';
	echo '<td colspan="3">'.$cust_address.'</td></tr>';
	echo '<tr><td><b>City </b></td>';
	echo '<td>'.$cust_city.'</td>';
	echo '<td style="text-align: right;"><b>Phone</b></td>';
	echo '<td>'.$cust_phone.'</td></tr>';
	echo '<tr><td><b>Email</b></td>';
	echo '<td>'.$cust_email.'</td><td></td><td></td></tr>';
	echo '<tr height="80"><td><b>Comments</b></td>';
	echo '<td colspan="3">'.$comments.'</td></tr>';//issue - can custs do a separate comment?
		
	if(!isset($_GET['id'])){ //this whole tab will be the confirm screen before saving changes (if adding a cust)
	echo '<tr>';
	echo '<form action="custprcommit.php" method="POST">';
	echo '<input type="hidden" name="action" value="confirmadd"/>'; //or else, we need a url para in the form's action script
	echo '<td><input type="submit" name="submit" value="Cancel"/></td>'; // need to test the value in the processing script!
	echo '<td></td><td></td>';
	echo '<td><input type="submit" name="submit" value="Confirm & Save"/></td>'; // need to test the value in the processing script!
	echo '</form>';
	echo '</tr>';
	}
	
	echo '</table>';
	//let's end the div outside of the whole switch statement
	break;
	
	case 'general':
	?>
	<form action="custprcommit.php" method="POST">
	<table width="100%">
	<tr>
		<td width="20%"><b>Customer Name</b></td>
		<td><input type="text" name="cust_name" value="<?php echo $cust_name; ?>" /></td>
		<td></td><td></td>
	</tr><tr>	
		<td><b>Address</b></td>
		<td colspan="3"><input type="text" name="cust_address" value="<?php echo $cust_address; ?>" /></td>
	</tr><tr>
		<td><b>City</b></td>
		<td><select name="cust_city">
		<?php
		for($num=1; $num<16; $num++){
			$city = 'Colombo '.$num;
			if($city==$cust_city){
				echo '<option value="'.$city.'" selected="selected">'.$city.'</option>';
			} else {
				echo '<option value="'.$city.'">'.$city.'</option>';
			}
		}
		?>
		</select></td>
		<td style="text-align: right;"><b>Phone</b></td>
		<td><input type="text" name="cust_phone" value="<?php echo $cust_phone; ?>" /></td>
		</tr><tr>
		<td><b>Email</b></td>
		<td><input type="text" name="cust_email" value="<?php echo $cust_email; ?>" /></td><td></td><td></td>
		</tr><tr height="80">
		<td><b>Comments</b></td>
		<td colspan="3"><textarea name="comments" rows="5"><?php echo htmlspecialchars($comments); ?></textarea></td></tr>
		
		<tr><td></td><td></td><td></td>
		<input type="hidden" name="action" value="general"/>
	<?php
	if(isset($cust_id)){ //same as isset(Get id)
		echo '<input type="hidden" name="cust_id" value="'.$cust_id.'"/>';
		echo '<td><input type="submit" name="submit" value="Save Changes"/></td>';
	} else {
		echo '<td><input type="submit" name="submit" value="Continue"/></td>';
	}
	?>
		</tr>
	</table>
	</form>
	<?php
	break;
	//lets end the div outside the whole switch statement!
	
	case 'pwrd':
	if(isset($cust_id)){
	?>
	<form action="custprcommit.php" method="POST">
	<table width="70%">
	<tr>
	<td width="35%"><b>Username</b></td>
	<td><?php echo get_cust_un($cust_id); ?></td>
	</tr><tr>
	<td><b>Enter Existing Password</b></br>(Enter your OWN password if Admin)</td>
	<td><input type="password" maxlength="20" name="old_pwrd"></td>
	</tr><tr>
	<td><b>Enter New Password</b></td>
	<td><input type="password" maxlength="20" name="new_pwrd"></td>
	</tr><tr>
	<td><b>Reenter New Password</b></td>
	<td><input type="password" maxlength="20" name="new_pwrd2"></td>
	</tr>
	<tr><td>
	<input type="hidden" name="action" value="pwrd"/>
	<input type="hidden" name="cust_id" value="<?php echo $cust_id;?>"/></td>
	<td><input type="submit" name="submit" value="Save Changes"/></td>
	</tr>
	</table>
	</form>
	<?php
	} else { //Adding a new cust
	?>
	<form action="custprcommit.php" method="POST">
	<table width="70%">
	<tr>
	<td width="35%"><b>Enter Username</b></td>
	<td><input type="text" name="cust_username"></td> <!-- Good to have a username automatically appear here (using php session vars), if the cust user is returning (By pressing "go back") to this part of the form - look at the top part of the script, how any existing session cust data was handled) -->
	</tr><tr>
	<td><b>Enter Password</b></td>
	<td><input type="password" maxlength="20" name="new_pwrd"></td>
	</tr><tr>
	<td><b>Reenter Password</b></td>
	<td><input type="password" maxlength="20" name="new_pwrd2"></td>
	</tr>
	<tr><td>
	<input type="hidden" name="action" value="pwrd"/></td>
	<td><input type="submit" name="submit" value="Continue"/></td>
	</tr>
	</table>
	</form>
	<?php
	}
	break;
			
}

echo '</div>';
echo '</div>';
//Remember to unset the Session array (for new cust) where required

?>

</body>
</html>