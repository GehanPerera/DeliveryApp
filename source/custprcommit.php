<?php
session_start();

include 'dbconnect.php';

include 'functions.php';

if(isset($_POST['cust_id'])){
	$cust_id = $_POST['cust_id'];
}


switch($_POST['action']) {
	
	case 'general':
	
	$error = array();
	
	$cust_name = isset($_POST['cust_name'])? trim($_POST['cust_name']):'';
	if(empty($cust_name)){
		$error[] = urlencode('cust User Name field cannot be blank. ');
	}
	
	$cust_address = isset($_POST['cust_address'])? trim($_POST['cust_address']):'';
	if(empty($cust_address)){
		$error[] = urlencode('Address cannot be blank. ');
	}

	$cust_city = isset($_POST['cust_city'])? trim($_POST['cust_city']):'';
	if(empty($cust_city)){
		$error[] = urlencode('Please select a city. ');
	}
	
	$cust_phone = isset($_POST['cust_phone'])? trim($_POST['cust_phone']):'';
	$cust_phone = str_replace('-','',$cust_phone);
	$cust_phone = str_replace('(','',$cust_phone);
	$cust_phone = str_replace(')','',$cust_phone);
	$cust_phone = str_replace(' ','',$cust_phone);
	if(!preg_match('|^\d{10}$|',$cust_phone)){
		$error[] = urlencode('Please enter a valid phone number. ');
	} else {
		$area = substr($cust_phone,0,3);
		$rest = substr($cust_phone,3);
		$cust_phone = '('.$area.')'.$rest;
	}
	
	$cust_email = isset($_POST['cust_email'])? trim($_POST['cust_email']):'';  //need to validate this better
	if(empty($cust_email)){  
		$error[] = urlencode('Email cannot be blank. ');
	}

	$comments = isset($_POST['comments'])? trim($_POST['comments']):'';  
	//Okay for comments to be empty!

	if(!empty($error)){
		$urlparas = isset($cust_id)? 'tab=general&id='.$cust_id : 'tab=general';
		header('Location:custprofile.php?'.$urlparas.'&error='.join($error,urlencode('<br/>'))); //Do we need die() after this line?
		die();
	}
	
	//There are no input errors at this point. Now let's address the Add vs Edit situation for the general info tab
	
	switch(isset($cust_id)){
		case true:   //Edit situation
		$query = 'UPDATE customer SET
					cust_name="'.$cust_name.'",
					cust_address="'.$cust_address.'",
					cust_city="'.$cust_city.'",
					cust_phone="'.$cust_phone.'",
					cust_email="'.$cust_email.'",
					comments="'.$comments.'"
					WHERE
					cust_id='.$cust_id;
					
		mysqli_query($db,$query) or die(mysqli_error($db));
		
		header('Refresh: 5; URL=custprofile.php?tab=view&id='.$cust_id);
		echo 'User information has been successfully updated!<br>';
		echo 'You will be redirected to your profile page in 5 seconds.';
		die(); //Is this really needed given the double break below?
		break;
		
		case false:  //Add situation
		//Take values from the submitted form fields & store in the session vars
		//First unset any existing session vars
		unset($_SESSION['addcust']);
		$join_date = date('Y-m-d');
		
		$_SESSION['addcust']['cust_name'] = $cust_name;
		$_SESSION['addcust']['cust_address'] = $cust_address;
		$_SESSION['addcust']['cust_city'] = $cust_city;
		$_SESSION['addcust']['cust_phone'] = $cust_phone;
		$_SESSION['addcust']['cust_email'] = $cust_email;
		$_SESSION['addcust']['comments'] = $comments;
		$_SESSION['addcust']['cust_joindate'] = $join_date;
		
		header('Location:custprofile.php?tab=pwrd');  //go to next part of the form
		//do we need die() here?
		die();
		break;
	}
	break;  //End of 'general' case

	case 'pwrd':
	switch(isset($cust_id)){
		case true:   //Edit situation
		$error = array();		
		$cust_username = get_cust_un($cust_id);
		
		if($_SESSION['usertype']=='admin'){
			$admin_username = $_SESSION['username'];  
			$query = 'SELECT * 
					FROM admin_login 
					WHERE admin_username = "'.$admin_username.'"
					AND admin_password = PASSWORD("'.$_POST['old_pwrd'].'")';
		} else {			
			$query = 'SELECT * 
					FROM cust_login 
					WHERE cust_username = "'.$cust_username.'"
					AND cust_password = PASSWORD("'.$_POST['old_pwrd'].'")';
		}
		
		$result = mysqli_query($db,$query) or die(mysqli_error($db));
		
		$numrows = mysqli_num_rows($result);
		
		if($numrows==0){
			$error[] = urlencode('The current password you entered does not match our records! ');
		} 
		elseif($_POST['new_pwrd'] != $_POST['new_pwrd2']) {  //Can we actually do this??
			$error[] = urlencode('The 2 new password fields do not match. Please try again! ');
		} 
		elseif(strlen($_POST['new_pwrd'])<6) { 
			$error[] = urlencode('Your password must contain a minimum of 6 characters! ');
		}	//See pg47 in Ebook for this type of elseif example!		
		
		if(empty($error)){
			$query = 'UPDATE cust_login 
						SET cust_password = PASSWORD("'.$_POST['new_pwrd'].'") 
							WHERE cust_username="'.$cust_username.'"';
			mysqli_query($db,$query) or die(mysqli_error($db));
		
			header('Refresh: 5; URL=custprofile.php?tab=view&id='.$cust_id);
			echo 'Password has been successfully updated!<br>';
			echo 'You will be redirected to the profile page in 5 seconds.';
			die(); 
		} else {
			header('Location:custprofile.php?tab=pwrd&id='.$cust_id.'&error='.join($error,urlencode('<br/>'))); //Do we need die() after this line?
			die();
		}
		break;
		
		case false: //adding situation
		$cust_username = isset($_POST['cust_username'])? trim($_POST['cust_username']):'';
				
		$error = array();
		//Need to check if the proposed username (for new cust) already exists!
		$query = 'SELECT * 
					FROM cust_login 
					WHERE cust_username = "'.$cust_username.'"';
		$result = mysqli_query($db,$query) or die(mysqli_error($db));
		$numrows = mysqli_num_rows($result);
		
		if($numrows>0){
			$error[] = urlencode('The username you entered already exists. Please try another username! ');
		} 
		elseif((strlen($cust_username)<6)||(strlen($cust_username)>25)){
			$error[] = urlencode('The username must be between 6 and 25 characters long! ');
		}
		elseif($_POST['new_pwrd'] != $_POST['new_pwrd2']) {  //Can we actually do this??
			$error[] = urlencode('The 2 password fields do not match. Please try again! ');
		} 
		elseif(strlen($_POST['new_pwrd'])<6) { 
			$error[] = urlencode('Your password must contain a minimum of 6 characters! ');
		}	//See pg47 in Ebook for this type of elseif example!		
		
		if(empty($error)){ //Fill the necessary Session vars
		
			$_SESSION['addcust']['cust_username'] = $cust_username;
			$_SESSION['addcust']['cust_password'] = $_POST['new_pwrd'];
		
			header('Location:custprofile.php?tab=view');  //go to next part of the form
			//do we need die() here?
			die();
		} else {
			header('Location:custprofile.php?tab=pwrd&error='.join($error,urlencode('<br/>'))); //Do we need die() after this line?
			die();
		}
		break;
	}
	break; //end of 'pwrd' case
	
	
	case 'confirmadd': //this is only applicable to the Adding situation!
	if($_POST['submit']=='Cancel'){
		unset($_SESSION['addcust']);
		if($_SESSION['usertype']=='customer'){
			header('Location:index.php');
		} else {
			header('Location:custmngt.php'); //No need for a message with 5 sec delay
		}
		die();
	} else { // Confirm & Add
		$query = 'INSERT INTO customer
					(cust_name,cust_address,cust_city,cust_phone,cust_email,comments,cust_joindate)
					VALUES
					("'.$_SESSION['addcust']['cust_name'].'",
					"'.$_SESSION['addcust']['cust_address'].'",
					"'.$_SESSION['addcust']['cust_city'].'",
					"'.$_SESSION['addcust']['cust_phone'].'",
					"'.$_SESSION['addcust']['cust_email'].'",
					"'.$_SESSION['addcust']['comments'].'",
					"'.$_SESSION['addcust']['cust_joindate'].'")';

		mysqli_query($db,$query) or die(mysqli_error($db));
		$cust_id = mysqli_insert_id($db);
		
		
		$query = 'INSERT INTO cust_login
					(cust_username,cust_password,cust_id)
					VALUES
					("'.$_SESSION['addcust']['cust_username'].'",
					PASSWORD("'.$_SESSION['addcust']['cust_password'].'"),
					'.$cust_id.')';
					
		mysqli_query($db,$query) or die(mysqli_error($db));
				
		unset($_SESSION['addcust']);
		
		if($_SESSION['usertype']=='customer'){
			header('Refresh: 5; URL=login.php');
			echo 'Your information has been successfully saved!<br>';
			echo 'You will be redirected to the login page in 5 seconds.';
			echo 'Please enter the same username & password that you just used to register';
			die();
		} else {
			header('Refresh: 5; URL=custmngt.php');
			echo 'New user information has been successfully updated!<br>';
			echo 'You will be redirected to the customer list in 5 seconds.';
			die();
		}
		
	}
	
	break; //End of 'confirmadd' case
	
}

		
		
		
	
	
		
		

		
		
/* ALTERNATIVE WAY		
	case 'upload': //Need to research whether we can actually store images in Session vars!
		
		if(isset($_SESSION['logo'])&&($_SESSION['logo'] != false)){ //there is already an image
			$logo = 1;
			$logo_alreadyprocessed = true;
		} elseif($_FILES['fileupload_logo']['error'] == UPLOAD_ERR_NO_FILE){
			$logo = 0;
		}
		} elseif($_FILES['fileupload_logo']['error'] != UPLOAD_ERR_OK){
			$logo = -1;
		} elseif($_FILES['fileupload_logo']['error'] == UPLOAD_ERR_OK) { 
			list($width,$height,$type,$attr) = getimagesize($_FILES['fileupload_logo']['tmp_name']); //Ideally, we should also check whether height==width
			switch($type) {
				case IMAGETYPE_GIF:
				$image=imagecreatefromgif($_FILES['fileupload_logo']['tmp_name']);
				break;
				case IMAGETYPE_JPEG:
				$image=imagecreatefromjpeg($_FILES['fileupload_logo']['tmp_name']);
				break;
				case IMAGETYPE_PNG:
				$image=imagecreatefromjpeg($_FILES['fileupload_logo']['tmp_name']);
				break;
				default:
				break;
			}
			if(!ISSET($image)||!($image)) {	
				$logo = -1;
			} else { //there IS an image!
				$thumb_width = 200;
				$thumb_height = 200*($height/$width); //need to play around with these numbers
				$_SESSION['logo'] = imagecreatetruecolor($thumb_width,$thumb_height); //Note that this session var is common to both edit & add, it is not a part of the addcust array!
				imagecopyresampled($_SESSION['logo'],$image,0,0,0,0,$thumb_width,$thumb_height,$width,$height);
				$logo = 1;
				$logo_alreadyprocessed=false;
			}
		}
			
		if(isset($_SESSION['pic'])&&($_SESSION['pic'] != false)){ //there is already an image
			$pic = 1;
			$pic_alreadyprocessed = true;
		} elseif($_FILES['fileupload_pic']['error'] == UPLOAD_ERR_NO_FILE){
			$pic = 0;
		}
		} elseif($_FILES['fileupload_pic']['error'] != UPLOAD_ERR_OK){
			$pic = -1;
		} elseif($_FILES['fileupload_pic']['error'] == UPLOAD_ERR_OK) { 
			list($width,$height,$type,$attr) = getimagesize($_FILES['fileupload_pic']['tmp_name']); //Ideally, we should also check whether height==width
			switch($type) {
				case IMAGETYPE_GIF:
				$image=imagecreatefromgif($_FILES['fileupload_pic']['tmp_name']);
				break;
				case IMAGETYPE_JPEG:
				$image=imagecreatefromjpeg($_FILES['fileupload_pic']['tmp_name']);
				break;
				case IMAGETYPE_PNG:
				$image=imagecreatefromjpeg($_FILES['fileupload_pic']['tmp_name']);
				break;
				default:
				break;
			}
			if(!ISSET($image)||!($image)) {	
				$pic = -1;
			} else { //there IS an image!
				$pic_width = 800;
				$pic_height = 800*($height/$width); //need to play around with these numbers
				$_SESSION['pic'] = imagecreatetruecolor($pic_width,$pic_height); //Note that this session var is common to both edit & add, it is not a part of the addcust array!
				imagecopyresampled($_SESSION['pic'],$image,0,0,0,0,$pic_width,$pic_height,$width,$height);
				$pic = 1;
				$pic_alreadyprocessed=false;
			}
		}
		
		switch(isset($cust_id)){
			case true: //Edit situation
			if(($logo==1)&&(logo_alreadyprocessed==false)){ 
			//Save the image in folder
			}
			if(($pic==1)&&(pic_alreadyprocessed==false)){ 
			//Save in folder
			}
			break;
			
			case false:
			if(($logo==1)&&(logo_alreadyprocessed==false)){ 
			//Save the image in session add cust array
			}
			if(($pic==1)&&(pic_alreadyprocessed==false)){ 
			//Save the image in session add cust array
			}
			break;
		}
		
		if($logo+$pic>0){
			//redirect to view with or without get(id) url para depending on whether add or edit!
		} else {
			//redirect back to image upload form with $logo & $pic values as additional url paras, also need to consider whether to send get(id) para...
		}
		
		
*/		
		






//For the 'upload' case, all errors (including NO UPLOAD) can be considered as errors, because only images are submitted in the corresponding form!
//Also in this case, we would ideally need to split the error array into 2 arrays, and the form needs to be updated to include a new column to display any error
//either in the Logo upload row or for the regular image's row, as required.  This way if 1 upload succeeds & the other fails, the user only has to reupload
//the failed image, & the user will know exactly which one it is!  (The successful image will have to be stored in the session, before redirecting to form)


?>