<?php
session_start();

include 'dbconnect.php';

include 'functions.php';
//Redirect to the "view" tab if $_GET['tab'] is not set?

if(!((($_SESSION['usertype']=='admin')&&($_SESSION['accesslevel']==1))||(isset($_GET['id'])&&($_SESSION['usertype']=='admin')&&($_SESSION['userid']==$_GET['id'])))){
		header('Refresh: 5; URL=index.php'); //Amazing that header redirect was possible AFTER the include above where output was surely echoed to browser!
		echo 'Sorry you don\'t have access to view this page! You will be redirected to our home page in 5 seconds.';
		die();
}

/*
if(isset($_GET['id'])){
	if(!(($_SESSION['usertype']=='admin')&&(($_SESSION['accesslevel']==1)||($_SESSION['userid']!=$admin_id)))){
		header('Refresh: 5; URL=index.php'); //Amazing that header redirect was possible AFTER the include above where output was surely echoed to browser!
		echo 'Sorry you don\'t have access to view this page! You will be redirected to our home page in 5 seconds.';
		die();
} else {
	if(!(($_SESSION['usertype']=='admin')&&($_SESSION['accesslevel']==1))){
		header('Refresh: 5; URL=index.php'); //Amazing that header redirect was possible AFTER the include above where output was surely echoed to browser!
		echo 'Sorry you don\'t have access to view this page! You will be redirected to our home page in 5 seconds.';
		die();
}
*/

if(isset($_POST['submit'])&&($_POST['submit']=='Delete')){
	$admin_id = $_GET['id'];
	
	$admin_dateinactive = date("Y-m-d H:i:s");
	
	$query = 'UPDATE admin SET
				admin_isactive = "0",
				admin_dateinactive = "'.$admin_dateinactive.'"
				WHERE admin_id='.$admin_id;
	mysqli_query($db,$query) or die(mysqli_error($db));
	
	$query = 'DELETE FROM admin_login WHERE admin_id='.$admin_id; //Deleted Admin user should not be allowed to login!
	mysqli_query($db,$query) or die(mysqli_error($db));
	
	header('Refresh: 5; URL=adminmngt.php');
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

//Ck session usertypes & redirect if required (this includes redirecting a admin whose userid is not matching)
//Setting Default Values: Most of the parts under 'View' & 'General' tabs are common for both Edit & Add situations!  
if(isset($_GET['id'])){ //Editing situation (user can be admin or super admin)
	$admin_id = $_GET['id']; 
	$query = 'SELECT * FROM admin WHERE admin_id='.$admin_id;
	$result = mysqli_query($db,$query) or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($result);
	extract($row);
} else{ //Adding situation
	$admin_name = (isset($_SESSION['addadmin']['admin_name']))? $_SESSION['addadmin']['admin_name']: '';
	$employee_id = (isset($_SESSION['addadmin']['employee_id']))? $_SESSION['addadmin']['employee_id']: '';
	$admin_level = (isset($_SESSION['addadmin']['admin_level']))? $_SESSION['addadmin']['admin_level']: ''; //This is an integer, do we need a better default value?
	$admin_phone = (isset($_SESSION['addadmin']['admin_phone']))? $_SESSION['addadmin']['admin_phone']: ''; //Need to handle join date in processing script
	$admin_email = (isset($_SESSION['addadmin']['admin_email']))? $_SESSION['addadmin']['admin_email']: '';
	$comments = (isset($_SESSION['addadmin']['comments']))? $_SESSION['addadmin']['comments']: ''; //Rename this field in DB?
		
}		  
	
?>
<div id="Wrapper">
<div id="Navigation">

<?php

echo '<ul>';

	echo (isset($_GET['tab'])&&($_GET['tab']=='view'))? '<li class="selected">':'<li>';
	echo (isset($_GET['id']))? '<a href="adminprofile.php?tab=view&id='.$_GET['id'].'">View Profile</a>':'View Profile';
	echo '</li>';
	echo (isset($_GET['tab'])&&($_GET['tab']=='general'))? '<li class="selected">':'<li>';
	echo (isset($_GET['id']))? '<a href="adminprofile.php?tab=general&id='.$_GET['id'].'">Edit General Info</a>':'Edit General Info';
	echo '</li>';
	echo (isset($_GET['tab'])&&($_GET['tab']=='pwrd'))? '<li class="selected">':'<li>';
	echo (isset($_GET['id']))? '<a href="adminprofile.php?tab=pwrd&id='.$_GET['id'].'">Change Password</a>':'Login Info';
	echo '</li>';
	
echo '</ul>';
//echo '</br></br>';

if((!isset($admin_id))||(isset($admin_id)&&($_SESSION['usertype']=='admin')&&($_SESSION['accesslevel']==1)&&($_SESSION['userid']!=$admin_id))){
echo '<ul>';
	echo '<li><a href="adminmngt.php">Return to Admin List</a></li>';
echo '</ul>';
}

echo '</div>';
	
?>

<div id="ProfileForm">	

<?php
if(isset($_GET['id'])){ //Editing situation (user can be admin or super admin)
	echo '<h2>'.$admin_name.'</h2>';
}

//DISPLAY $ERROR WITH HIGHLIGHTS
if(isset($_GET['error'])&&($_GET['error']!='')){
	echo '<div id="error">'.$_GET['error'].'</div>';
}

switch($_GET['tab']){
	case 'view':
	
	echo '<table border="1" width="100%">';	
	echo '<tr>';
	echo '<td><b>Admin User Name</b></td>';
	echo '<td colspan="3">'.$admin_name.'</td></tr>';
	echo '<tr><td><b>Employee ID</b></td>';
	echo '<td colspan="3">'.$employee_id.'</td></tr>';
	echo '<tr><td><b>Acess Type </b></td>';
	echo '<td>';
	echo ($admin_level==1)? 'Mngr/Exec':'Staff';
	echo '</td>';
	echo '<td style="text-align: right;"><b>Phone</b></td>';
	echo '<td>'.$admin_phone.'</td></tr>';
	echo '<tr><td><b>Email</b></td>';
	echo '<td>'.$admin_email.'</td><td></td><td></td></tr>';
	echo '<tr height="80"><td><b>Comments</b></td>';
	echo '<td colspan="3">'.$comments.'</td></tr>';//issue - can admins do a separate comment?
		
	if(!isset($_GET['id'])){ //this whole tab will be the confirm screen before saving changes (if adding a admin)
	echo '<tr>';
	echo '<form action="adminprcommit.php" method="POST">';
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
	<form action="adminprcommit.php" method="POST">
	<table width="100%">
	<tr>
		<td width="20%"><b>Admin User Name</b></td>
		<td><input type="text" name="admin_name" value="<?php echo $admin_name; ?>" /></td>
		<td></td><td></td>
	</tr>
	<?php
	if((!isset($admin_id))||(isset($admin_id)&&($_SESSION['usertype']=='admin')&&($_SESSION['accesslevel']==1)&&($_SESSION['userid']!=$admin_id))){ //Adding a new admin or Editing existing admin by ANOTHER authorized admin
	?>
	<tr>	
		<td><b>Employee ID</b></td>
		<td><input type="text" name="employee_id" value="<?php echo $employee_id; ?>" /></td>
		<td style="text-align: right;"><b>Select User Type </b></td>
		<td><select name="admin_level">
		<?php //perhaps it would have been better to have a radio button element instead of a select element since we have only 2 possibilities!
		for($num=1; $num<3; $num++){
			if($num==$admin_level){
				echo '<option value="'.$num.'" selected="selected">';
				echo ($num==1)? 'Mngr/Exec':'Staff';
				echo '</option>';
			} else {
				echo '<option value="'.$num.'">';
				echo ($num==1)? 'Mngr/Exec':'Staff';
				echo '</option>';
			}
		}
		?>
		</select></td>
	</tr>
	<?php
	} else {		
		echo '<input type="hidden" name="employee_id" value="'.$employee_id.'"/>';
		echo '<input type="hidden" name="admin_level" value="'.$admin_level.'"/>';
	}	
	?>
	<tr>
		<td><b>Phone</b></td>
		<td><input type="text" name="admin_phone" value="<?php echo $admin_phone; ?>" /></td><td></td><td></td>
		</tr><tr>
		<td><b>Email</b></td>
		<td><input type="text" name="admin_email" value="<?php echo $admin_email; ?>" /></td><td></td><td></td>
		</tr><tr height="80">
		<td><b>Comments</b></td>
		<td colspan="3"><textarea name="comments" rows="5"><?php echo htmlspecialchars($comments); ?></textarea></td></tr>
		<tr><td></td><td></td><td></td>
		<input type="hidden" name="action" value="general"/>
	<?php
	if(isset($admin_id)){ //same as isset(Get id)
		echo '<input type="hidden" name="admin_id" value="'.$admin_id.'"/>';
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
	if(isset($admin_id)){
	?>
	<form action="adminprcommit.php" method="POST">
	<table width="70%">
	<tr>
	<td width="35%"><b>Username</b></td>
	<td><?php echo get_admin_un($admin_id); ?></td>
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
	<input type="hidden" name="admin_id" value="<?php echo $admin_id;?>"/></td>
	<td><input type="submit" name="submit" value="Save Changes"/></td>
	</tr>
	</table>
	</form>
	<?php
	} else { //Adding a new admin
	?>
	<form action="adminprcommit.php" method="POST">
	<table width="70%">
	<tr>
	<td width="35%"><b>Enter Username</b></td>
	<td><input type="text" name="admin_username"></td> <!-- Good to have a username automatically appear here (using php session vars), if the admin user is returning (By pressing "go back") to this part of the form - look at the top part of the script, how any existing session admin data was handled) -->
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
//Remember to unset the Session array (for new admin) where required

?>

</body>
</html>