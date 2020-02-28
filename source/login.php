<?php
session_start();

include 'dbconnect.php';

$username = (isset($_POST['username']))? trim($_POST['username']):'';
$password = (isset($_POST['password']))? $_POST['password']:'';

if(isset($_POST['submit'])){
	if(!isset($_SESSION['logged'])||($_SESSION['logged'] !=1)){ //this whole part will be skipped IF user was aleady logged in!
		
		$query = 'SELECT * 
					FROM cust_login 
					WHERE cust_username = "'.$_POST['username'].'"
					AND cust_password = PASSWORD("'.$_POST['password'].'")';
		$result = mysqli_query($db,$query) or die(mysqli_error($db));
		
		$numrows = mysqli_num_rows($result);
		
		if($numrows>0){
				$row = mysqli_fetch_assoc($result);
				$_SESSION['username']=$username; //var from the top
				$_SESSION['logged']=1;
				$_SESSION['userid']=$row['cust_id'];
				$_SESSION['usertype']='customer';
				$_SESSION['accesslevel']=''; // may not be necessary!
				
				header('Refresh: 5; URL=index.php');
				echo 'Login successful. You will be redirected to our home page in 5 seconds.';
				die();
			}
		
		$error = true;
		
	} //perhaps give an else block to say user has already logged in?
}

?>
<html>
	<head>
		<title>Customer Login</title>
		<style>
		#custform {
			margin-left: auto;
			margin-right: auto;
			width: 50%;
			}
			
		</style>
	</head>
	<body>
<?php
include 'NavMenu.php';

if(isset($error)&&($error==true)){
	echo '<p style="color: red;">You have supplied an invalid username &/or password!</p>';
	echo '<p>Please <a href="#">click here</a> to register if you have not done so already</p>';
}
?>

<div id="custform">
<h3>Customer Login</h3>
<form action="login.php" method="POST">
<table>
	<tr>
		<td>Username</td>
		<td><input type="text" name="username" maxlength="40" value="<?php echo $username; ?>" /></td>
		</tr><tr>
		<td>Password</td>
		<td><input type="password" name="password" maxlength="20" value="<?php echo $password; ?>" /></td>
		</tr><tr>
		<td></td>
		<td><input type="submit" name="submit" value="Login" /></td>
		</tr>
</table>
</form>

</div>

</body>
</html>

	