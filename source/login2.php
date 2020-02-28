<?php
session_start();

include 'dbconnect.php';



$username = (isset($_POST['username']))? trim($_POST['username']):'';
$password = (isset($_POST['password']))? $_POST['password']:'';


if(isset($_POST['submit'])){
	
	if(!isset($_SESSION['logged'])||($_SESSION['logged'] !=1)){ //this whole part will be skipped IF user was aleady logged in!
		switch($_GET['usertype']){
		
		case 'admin':
		
		$query = 'SELECT * 
					FROM admin_login 
					WHERE admin_username = "'.$_POST['username'].'"
					AND admin_password = PASSWORD("'.$_POST['password'].'")';
		$result = mysqli_query($db,$query) or die(mysqli_error($db));
		
		$numrows = mysqli_num_rows($result);
		
		if($numrows>0){
			$row = mysqli_fetch_assoc($result);
			extract($row);
				$query = 'SELECT admin_level
							FROM admin
							WHERE admin_id='.$admin_id;
				$result = mysqli_query($db,$query) or die(mysqli_error($db));
				$row = mysqli_fetch_assoc($result);
				
				$_SESSION['username']=$username; //var defined at the top
				$_SESSION['logged']=1;
				$_SESSION['userid']=$admin_id;
				$_SESSION['usertype']='admin';
				$_SESSION['accesslevel']=$row['admin_level'];
				
				header('Refresh: 5; URL=index.php');
				echo 'Login successful. You will be redirected to our home page in 5 seconds.';
				die();
			}
		
		$error = true; //can include this line inside an else, but makes no difference.  
		break;
		
		case 'vendor':
		
		$query = 'SELECT * 
					FROM vendor_login 
					WHERE vendor_username = "'.$_POST['username'].'"
					AND vendor_password = PASSWORD("'.$_POST['password'].'")';
		$result = mysqli_query($db,$query) or die(mysqli_error($db));
		
		$numrows = mysqli_num_rows($result);
		
		if($numrows>0){
				$row = mysqli_fetch_assoc($result);
				$_SESSION['username']=$username; //var from the top
				$_SESSION['logged']=1;
				$_SESSION['userid']=$row['vendor_id'];
				$_SESSION['usertype']='vendor';
				$_SESSION['accesslevel']=2; // may not be necessary!
				
				header('Refresh: 5; URL=vendor_productview.php?id='.$row['vendor_id']);
				echo 'Login successful. You will be redirected to your profile page in 5 seconds.';
				die();
			}
		
		$error = true; //this line is reached if $numrows=0. This is actually the else block, but "else" is not needed since the if block ended with die()
		break;
	}
}//perhaps give an else block to say that the user is already logged in as a particular usertype?
}
?>
<html>
	<head>
		<title>Login</title>
		<style>
		#container {
			margin-left: auto;
			margin-right: auto;
			width: 60%;
			}
			
		#adminform {
			float: left;
			width:30%;	
			}
			
		#vendorform {
			float: right;
			width:30%;	
			}
		</style>
	</head>
	<body>
<?php
include 'NavMenu.php';

if(isset($error)&&($error==true)){
	echo '<p style="color: red;">You have supplied an invalid username &/or password!</p>';
	echo '<p>If you are a customer, please click <a href="#">here</a> to login</p>';
}
?>
<div id="container">

<div id="adminform">
<h3>Administrator Login</h3>
<form action="login2.php?usertype=admin" method="POST">
<table>
	<tr>
		<td>Username</td>
		<td><input type="text" name="username" maxlength="40" value="<?php echo (isset($_GET['usertype'])&&($_GET['usertype']=='admin'))? $username:''; ?>" /></td>
		</tr><tr>
		<td>Password</td>
		<td><input type="password" name="password" maxlength="20" value="<?php echo (isset($_GET['usertype'])&&($_GET['usertype']=='admin'))? $password:''; ?>" /></td>
		</tr><tr>
		<td></td>
		<td><input type="submit" name="submit" value="Login" /></td>
		</tr>
</table>
</form>
</div>

<div id="vendorform">
<h3>Vendor Login</h3>
<form action="login2.php?usertype=vendor" method="POST">
<table>
	<tr>
		<td>Username</td>
		<td><input type="text" name="username" maxlength="40" value="<?php echo (isset($_GET['usertype'])&&($_GET['usertype']=='vendor'))? $username:''; ?>" /></td>
		</tr><tr>
		<td>Password</td>
		<td><input type="password" name="password" maxlength="20" value="<?php echo (isset($_GET['usertype'])&&($_GET['usertype']=='vendor'))? $password:''; ?>" /></td>
		</tr><tr>
		<td></td>
		<td><input type="submit" name="submit" value="Login" /></td>
		</tr>
</table>
</form>
</div>

</div>

</body>
</html>
