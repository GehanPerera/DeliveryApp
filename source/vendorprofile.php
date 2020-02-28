<?php
session_start();

include 'dbconnect.php';



include 'functions.php';
//Redirect to the "view" tab if $_GET['tab'] is not set?


if(!((($_SESSION['usertype']=='admin')&&($_SESSION['accesslevel']==1))||(isset($_GET['id'])&&($_SESSION['usertype']=='vendor')&&($_SESSION['userid']==$_GET['id'])))){
		header('Refresh: 5; URL=index.php'); //Amazing that header redirect was possible AFTER the include above where output was surely echoed to browser!
		echo 'Sorry you don\'t have access to view this page! You will be redirected to our home page in 5 seconds.';
		die();
}


if(isset($_POST['submit'])&&($_POST['submit']=='Delete')){
	$vendor_id = $_GET['id'];
	
	$vendor_dateinactive = date("Y-m-d H:i:s");
	
	$query = 'UPDATE vendor SET
				vendor_isactive = "0",
				vendor_dateinactive = "'.$vendor_dateinactive.'"
				WHERE vendor_id='.$vendor_id;
	mysqli_query($db,$query) or die(mysqli_error($db));
	
	$query = 'DELETE FROM vendor_login WHERE vendor_id='.$vendor_id; //Deleted Admin user should not be allowed to login!
	mysqli_query($db,$query) or die(mysqli_error($db));
	
	header('Refresh: 5; URL=vendormngt.php');
	echo 'Vendor has been successfully deleted!';
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

//Ck session usertypes & redirect if required (this includes redirecting a vendor whose userid is not matching)
//Setting Default Values: Most of the parts under 'View' & 'General' tabs are common for both Edit & Add situations!  
if(isset($_GET['id'])){ //Editing situation (user can be vendor or super admin)
	$vendor_id = $_GET['id']; 
	echo '<img src="images/vendors/'.$vendor_id.'.jpg" style="width: 70%; display: block; margin: 0 auto 0 auto;">';
	echo '<br>';
	$query = 'SELECT * FROM vendor WHERE vendor_id='.$vendor_id;
	$result = mysqli_query($db, $query) or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	extract($row);
} else{ //Adding situation
	$vendor_name = (isset($_SESSION['addvendor']['vendor_name']))? $_SESSION['addvendor']['vendor_name']: '';
	$vendor_address = (isset($_SESSION['addvendor']['vendor_address']))? $_SESSION['addvendor']['vendor_address']: '';
	$vendor_city = (isset($_SESSION['addvendor']['vendor_city']))? $_SESSION['addvendor']['vendor_city']: '';
	$vendor_phone = (isset($_SESSION['addvendor']['vendor_phone']))? $_SESSION['addvendor']['vendor_phone']: ''; //Need to handle join date in processing script
	$vendor_email = (isset($_SESSION['addvendor']['vendor_email']))? $_SESSION['addvendor']['vendor_email']: '';
	$comments = (isset($_SESSION['addvendor']['comments']))? $_SESSION['addvendor']['comments']: ''; //Rename this field in DB?
	$weekday_start = (isset($_SESSION['addvendor']['weekday_start']))? $_SESSION['addvendor']['weekday_start']: '';
	$weekday_end = (isset($_SESSION['addvendor']['weekday_end']))? $_SESSION['addvendor']['weekday_end']: '';
	$sat_start = (isset($_SESSION['addvendor']['sat_start']))? $_SESSION['addvendor']['sat_start']: '';
	$sat_end = (isset($_SESSION['addvendor']['sat_end']))? $_SESSION['addvendor']['sat_end']: ''; //Is '' the appropriate default value for a time? If a var is assigned '', will it return true when subjected to the empty() function?  If yes, this might be okay!
	$sun_start = (isset($_SESSION['addvendor']['sun_start']))? $_SESSION['addvendor']['sun_start']: '';
	$sun_end = (isset($_SESSION['addvendor']['sun_end']))? $_SESSION['addvendor']['sun_end']: '';

	if(isset($_SESSION['addvendor']['pic'])){
		echo '<img src="'.$_SESSION['addvendor']['pic'].'" alt="Picture Not Uploaded" style="width: 70%; display: block; margin: 0 auto 0 auto;">';
		echo '<br>';
	}	
	
	
}		  

if(!empty($weekday_start)){
	$weekday_start = substr($weekday_start,0,5);
}

if(!empty($weekday_end)){
	$weekday_end = substr($weekday_end,0,5);
}

if(!empty($sat_start)){
	$sat_start = substr($sat_start,0,5);
}

if(!empty($sat_end)){
	$sat_end = substr($sat_end,0,5);
}

if(!empty($sun_start)){
	$sun_start = substr($sun_start,0,5);
}

if(!empty($sun_end)){
	$sun_end = substr($sun_end,0,5);
}


	
?>
<div id="Wrapper">
<div id="Navigation">

<?php
echo '<div>';
echo '<ul>';

	echo (isset($_GET['tab'])&&($_GET['tab']=='view'))? '<li class="selected">':'<li>';
	echo (isset($_GET['id']))? '<a href="vendorprofile.php?tab=view&id='.$_GET['id'].'">View Profile</a>':'View Profile';
	echo '</li>';
	echo (isset($_GET['tab'])&&($_GET['tab']=='general'))? '<li class="selected">':'<li>';
	echo (isset($_GET['id']))? '<a href="vendorprofile.php?tab=general&id='.$_GET['id'].'">Edit General Info</a>':'Edit General Info';
	echo '</li>';
	echo (isset($_GET['tab'])&&($_GET['tab']=='pwrd'))? '<li class="selected">':'<li>';
	echo (isset($_GET['id']))? '<a href="vendorprofile.php?tab=pwrd&id='.$_GET['id'].'">Change Password</a>':'Login Info';
	echo '</li>';
	echo (isset($_GET['tab'])&&($_GET['tab']=='upload'))? '<li class="selected">':'<li>';
	echo (isset($_GET['id']))? '<a href="vendorprofile.php?tab=upload&id='.$_GET['id'].'">Upload Images</a>':'Upload Images';
	echo '</li>';

echo '</ul>';
	
if(isset($_GET['id'])){ 
echo '<img src="images/vendors/logos/'.$vendor_id.'.jpg" align="right" alt="Logo">';
} elseif(isset($_SESSION['addvendor']['logo'])){
echo '<img src="'.$_SESSION['addvendor']['logo'].'" align="right" alt="Logo Not Uploaded">';
}		
echo '</div>';

echo '<div style="clear:both;">';
echo '<ul>';
if($_SESSION['usertype']=='vendor'){
	echo '<li><a href="vendor_productview.php?id='.$vendor_id.'">Return to Product page</a></li>'; //vendor_id has to be set IF usertype is vendor
} else {
	echo '<li><a href="vendormngt.php">Return to Vendor List</a></li>';
}
echo '</ul>';
echo '</div>';

echo '</div>';
	
?>

<div id="ProfileForm">	

<?php
if(isset($_GET['id'])){ //Editing situation (user can be vendor or super admin)
	echo '<h2>'.get_vendorname($vendor_id).'</h2>';
}

//DISPLAY $ERROR WITH HIGHLIGHTS
if(isset($_GET['error'])&&($_GET['error']!='')){
	echo '<div id="error">'.$_GET['error'].'</div>';
}

switch($_GET['tab']){
	case 'view':
	unset($_SESSION['logo']);//Better to unset the 2 numeric, image upload indicator vars here!
	unset($_SESSION['pic']);
	
	echo '<table border="1" width="100%">';	
	echo '<tr>';
	echo '<td><b>Company Name</b></td>';
	echo '<td colspan="3">'.$vendor_name.'</td></tr>';
	echo '<tr><td><b>Address</b></td>';
	echo '<td colspan="3">'.$vendor_address.'</td></tr>';
	echo '<tr><td><b>City</b></td>';
	echo '<td>'.$vendor_city.'</td>';
	echo '<td style="text-align: right;"><b>Phone</b></td>';
	echo '<td>'.$vendor_phone.'</td></tr>';
	echo '<tr><td><b>Email</b></td>';
	echo '<td>'.$vendor_email.'</td><td></td><td></td></tr>';
	echo '<tr height="80"><td><b>Comments</b></td>';
	echo '<td colspan="3">'.$comments.'</td></tr>';//issue - can admins do a separate comment?
	echo '<tr><td colspan="4"><b>Hours of Operation</b></td></tr>';
	echo '<tr><td colspan="4" style="text-align: center;"><u>Weekdays</u></td></tr>';
	echo '<tr><td style="text-align: right;">Start</td>'; 
	echo '<td>'.$weekday_start.'</td>'; //better to combine start & end time vars & have a "to" inbetween them, all inside a single cell!
	echo '<td style="text-align: right;">End</td>'; 
	echo '<td>'.$weekday_end.'</td></tr>'; 
	echo '<tr><td colspan="4" style="text-align: center;"><u>Saturdays</u></td></tr>';
	echo '<tr><td style="text-align: right;">Start</td>'; 
	echo '<td>'.$sat_start.'</td>'; //better to combine start & end time vars & have a "to" inbetween them, all inside a single cell!
	echo '<td style="text-align: right;">End</td>'; 
	echo '<td>'.$sat_end.'</td></tr>'; 
	echo '<tr><td colspan="4" style="text-align: center;"><u>Sundays</u></td></tr>';
	echo '<tr><td style="text-align: right;">Start</td>'; 
	echo '<td>'.$sun_start.'</td>'; //better to combine start & end time vars & have a "to" inbetween them, all inside a single cell!
	echo '<td style="text-align: right;">End</td>'; 
	echo '<td>'.$sun_end.'</td></tr>'; 
	
	if(!isset($_GET['id'])){ //this whole tab will be the confirm screen before saving changes (if adding a vendor)
	echo '<tr>';
	echo '<form action="vndrprcommit.php" method="POST">';
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
	<form action="vndrprcommit.php" method="POST">
	<table width="100%">
	<tr>
		<td width="20%"><b>Company Name</b></td>
		<td><input type="text" name="vendor_name" value="<?php echo $vendor_name; ?>" /></td>
		<td></td><td></td>
	</tr><tr>	
		<td><b>Address</b></td>
		<td colspan="3"><input type="text" name="vendor_address" value="<?php echo $vendor_address; ?>" /></td>
	</tr><tr>
		<td><b>City</b></td>
		<td><select name="vendor_city">
		<?php
		for($num=1; $num<16; $num++){
			$city = 'Colombo '.$num;
			if($city==$vendor_city){
				echo '<option value="'.$city.'" selected="selected">'.$city.'</option>';
			} else {
				echo '<option value="'.$city.'">'.$city.'</option>';
			}
		}
		?>
		</select></td>
		<td style="text-align: right;"><b>Phone</b></td>
		<td><input type="text" name="vendor_phone" value="<?php echo $vendor_phone; ?>" /></td>
		</tr><tr>
		<td><b>Email</b></td>
		<td><input type="text" name="vendor_email" value="<?php echo $vendor_email; ?>" /></td><td></td><td></td>
		</tr><tr height="80">
		<td><b>Comments</b></td>
		<td colspan="3"><textarea name="comments" rows="5"><?php echo htmlspecialchars($comments); ?></textarea></td></tr>
		<tr>
		<td colspan="4"><b>Hours of Operation </b>(Please enter times in 24hr clock format)</td></tr>
		<tr>
		<td colspan="4" style="text-align: center;"><u>Weekdays</u></td></tr>
		<tr>
		<td style="text-align: right;">Start Time</td>
		<td><input type="text" placeholder="hh:mm" name="weekday_start" value="<?php echo $weekday_start; ?>" /></td>
		<td style="text-align: right;">End Time</td>
		<td><input type="text" placeholder="hh:mm" name="weekday_end" value="<?php echo $weekday_end; ?>" /></td>
		</tr><tr>
		<td colspan="4" style="text-align: center;"><u>Saturdays</u></td></tr>
		<tr>
		<td style="text-align: right;">Start Time</td>
		<td><input type="text" placeholder="hh:mm" name="sat_start" value="<?php echo $sat_start; ?>" /></td>
		<td style="text-align: right;">End Time</td>
		<td><input type="text" placeholder="hh:mm" name="sat_end" value="<?php echo $sat_end; ?>" /></td>
		</tr><tr>
		<td colspan="4" style="text-align: center;"><u>Sundays</u></td></tr>
		<tr>
		<td style="text-align: right;">Start Time</td>
		<td><input type="text" placeholder="hh:mm" name="sun_start" value="<?php echo $sun_start; ?>" /></td>
		<td style="text-align: right;">End Time</td>
		<td><input type="text" placeholder="hh:mm" name="sun_end" value="<?php echo $sun_end; ?>" /></td>
		</tr>
		<tr><td></td><td></td><td></td>
		<input type="hidden" name="action" value="general"/>
	<?php
	if(isset($vendor_id)){ //same as isset(Get id)
		echo '<input type="hidden" name="vendor_id" value="'.$vendor_id.'"/>';
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
	if(isset($vendor_id)){
	?>
	<form action="vndrprcommit.php" method="POST">
	<table width="70%">
	<tr>
	<td width="35%"><b>Username</b></td>
	<td><?php echo get_vendor_un($vendor_id); ?></td>
	</tr><tr>
	<td><b>Enter Existing Password</b></br>(Enter your OWN password if admin)</td>
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
	<input type="hidden" name="vendor_id" value="<?php echo $vendor_id;?>"/></td>
	<td><input type="submit" name="submit" value="Save Changes"/></td>
	</tr>
	</table>
	</form>
	<?php
	} else { //Adding a new vendor
	?>
	<form action="vndrprcommit.php" method="POST">
	<table width="70%">
	<tr>
	<td width="35%"><b>Enter Username</b></td>
	<td><input type="text" name="vendor_username"></td> <!-- Good to have a username automatically appear here (using php session vars), if the admin user is returning (By pressing "go back") to this part of the form - look at the top part of the script, how any existing session vendor data was handled) -->
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
	
	case 'upload':
	
	if((isset($_SESSION['logo'])&&($_SESSION['logo']==0))&&(isset($_SESSION['pic'])&&($_SESSION['pic']==0))){
		echo '<p>You have not uploaded any image! If you want to skip this task, please ';
		echo isset($vendor_id)? '<a href="vendorprofile.php?tab=view&id='.$vendor_id.'">' : '<a href="vendorprofile.php?tab=view">';
		echo 'click here. </a></p>';
	}
	?>
	<form action="vndrprcommit.php" method="POST" enctype="multipart/form-data">
	<table width="100%">
	<tr>
	<td width="40%" style="text-align: left; vertical-align: top;">
	<b>Upload Image of Company Logo</b><br>
	Supported file types are: JPG,GIF & PNG only<br>
	Please choose an image with<br>
	equal or similar height & width<br>
	(Please note that any existing image will be replaced)</td>
<?php
	echo '<td width="20%" style="text-align: center; vertical-align: top;">';
	if(isset($_SESSION['logo'])&&($_SESSION['logo']==1)){
		echo 'Image successfully uploaded! </td>';
	} else {
		echo '<input type="file" name="fileupload_logo" /></td>';
	}
	
	echo '<td style="text-align: center; font-weight: Bold; color: Red;">';
	if(isset($_SESSION['logo'])&&($_SESSION['logo']==-1)){
		echo 'There was an error with your upload! <br>';
		echo 'Please try again. ';
	}
?>
	</td>
	</tr>
	<tr>
	<td style="text-align: left; vertical-align: top;">
	<b>Upload a (rectangular) Image to be displayed in your Profile page</b><br>
	Supported file types are: JPG,GIF & PNG only<br>
	Please choose an image that is<br>
	three times as wide as its height<br>
	(Please note that any existing image will be replaced)</td>
<?php
	echo '<td width="20%" style="text-align: center; vertical-align: top;">';
	if(isset($_SESSION['pic'])&&($_SESSION['pic']==1)){
		echo 'Image successfully uploaded! </td>';
	} else {
		echo '<input type="file" name="fileupload_pic" /></td>';
	}
	
	echo '<td style="text-align: center; font-weight: Bold; color: Red;">';
	if(isset($_SESSION['pic'])&&($_SESSION['pic']==-1)){
		echo 'There was an error with your upload! <br>';
		echo 'Please try again. ';
	}
?>
	</td>
	</tr>
	<tr><td></td>
		<input type="hidden" name="action" value="upload"/>
	<?php
	if(isset($vendor_id)){ //same as isset(Get id)
		echo '<input type="hidden" name="vendor_id" value="'.$vendor_id.'"/>';
		echo '<td><input type="submit" name="submit" value="Save Changes"/></td>';
	} else {
		echo '<td><input type="submit" name="submit" value="Continue"/></td>';
	}
	?>
		<td></td>
		</tr>
	</table>
	</form>
	<?php
	break;
		
}

echo '</div>';
echo '</div>';
//Remember to unset the Session array (for new vendor) where required

?>

</body>
</html>