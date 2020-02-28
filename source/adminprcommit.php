<?php
session_start();

include 'dbconnect.php';

include 'functions.php';

if(isset($_POST['admin_id'])){
	$admin_id = $_POST['admin_id'];
}


switch($_POST['action']) {
	
	case 'general':
	
	$error = array();
	
	$admin_name = isset($_POST['admin_name'])? trim($_POST['admin_name']):'';
	if(empty($admin_name)){
		$error[] = urlencode('Admin User Name field cannot be blank. ');
	}
	
	$employee_id = isset($_POST['employee_id'])? trim($_POST['employee_id']):'';
	$admin_level = isset($_POST['admin_level'])? trim($_POST['admin_level']):'';
	
	if((!isset($admin_id))||(isset($admin_id)&&($_SESSION['usertype']=='admin')&&($_SESSION['accesslevel']==1)&&($_SESSION['userid']!=$admin_id))){
		
		$query = 'SELECT * 
					FROM admin 
					WHERE employee_id = "'.$employee_id.'"';
		$result = mysqli_query($db,$query) or die(mysqli_error($db));
		$numrows = mysqli_num_rows($result);
		
		if($numrows>0){
			//echo $numrows;
			//die();
			$row = mysqli_fetch_assoc($result);
			if(!isset($admin_id)||(isset($admin_id)&&($admin_id!=$row['admin_id']))){
				//echo 'error is there!';
				//die();
				$error[] = urlencode('The Employee ID you entered already exists! ');
			}
		} elseif(empty($employee_id)) {
			$error[] = urlencode('Employee ID cannot be blank. ');
		}

		if(empty($admin_level)){
			$error[] = urlencode('Please select an admin user type. ');
		}
	}
	
	$admin_phone = isset($_POST['admin_phone'])? trim($_POST['admin_phone']):'';
	$admin_phone = str_replace('-','',$admin_phone);
	$admin_phone = str_replace('(','',$admin_phone);
	$admin_phone = str_replace(')','',$admin_phone);
	$admin_phone = str_replace(' ','',$admin_phone);
	if(!preg_match('|^\d{10}$|',$admin_phone)){
		$error[] = urlencode('Please enter a valid phone number. ');
	} else {
		$area = substr($admin_phone,0,3);
		$rest = substr($admin_phone,3);
		$admin_phone = '('.$area.')'.$rest;
	}
	
	$admin_email = isset($_POST['admin_email'])? trim($_POST['admin_email']):'';  //need to validate this better
	if(empty($admin_email)){  
		$error[] = urlencode('Email cannot be blank. ');
	}

	$comments = isset($_POST['comments'])? trim($_POST['comments']):'';  
	//Okay for comments to be empty!

	if(!empty($error)){
		$urlparas = isset($admin_id)? 'tab=general&id='.$admin_id : 'tab=general';
		header('Location:adminprofile.php?'.$urlparas.'&error='.join($error,urlencode('<br/>'))); //Do we need die() after this line?
		die();
	}
	
	//There are no input errors at this point. Now let's address the Add vs Edit situation for the general info tab
	
	switch(isset($admin_id)){
		case true:   //Edit situation
		$query = 'UPDATE admin SET
					admin_name="'.$admin_name.'",
					employee_id="'.$employee_id.'",
					admin_level='.$admin_level.',
					admin_phone="'.$admin_phone.'",
					admin_email="'.$admin_email.'",
					comments="'.$comments.'"
					WHERE
					admin_id='.$admin_id;
					
		mysqli_query($db,$query) or die(mysqli_error($db));
		
		header('Refresh: 5; URL=adminprofile.php?tab=view&id='.$admin_id);
		echo 'User information has been successfully updated!<br>';
		echo 'You will be redirected to the profile page in 5 seconds.';
		die(); //Is this really needed given the double break below?
		break;
		
		case false:  //Add situation
		//Take values from the submitted form fields & store in the session vars
		//First unset any existing session vars
		unset($_SESSION['addadmin']);
		$join_date = date('Y-m-d');
		
		$_SESSION['addadmin']['admin_name'] = $admin_name;
		$_SESSION['addadmin']['employee_id'] = $employee_id;
		$_SESSION['addadmin']['admin_level'] = $admin_level;
		$_SESSION['addadmin']['admin_phone'] = $admin_phone;
		$_SESSION['addadmin']['admin_email'] = $admin_email;
		$_SESSION['addadmin']['comments'] = $comments;
		$_SESSION['addadmin']['admin_joindate'] = $join_date;
		
		header('Location:adminprofile.php?tab=pwrd');  //go to next part of the form
		//do we need die() here?
		die();
		break;
	}
	break;  //End of 'general' case

	case 'pwrd':
	switch(isset($admin_id)){
		case true:   //Edit situation
		$error = array();
		
		if(($_SESSION['usertype']=='admin')&&($_SESSION['accesslevel']==1)&&($_SESSION['userid']!=$admin_id)){
			$admin_username = get_admin_un($_SESSION['userid']);  //Alternatively, we could have directly used $_SESSION['username']
		} else {
			$admin_username = get_admin_un($admin_id);
		}
		
		$query = 'SELECT * 
					FROM admin_login 
					WHERE admin_username = "'.$admin_username.'"
					AND admin_password = PASSWORD("'.$_POST['old_pwrd'].'")';
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
			$query = 'UPDATE admin_login 
						SET admin_password = PASSWORD("'.$_POST['new_pwrd'].'") 
							WHERE admin_username="'.get_admin_un($admin_id).'"';
			mysqli_query($db,$query) or die(mysqli_error($db));
		
			header('Refresh: 5; URL=adminprofile.php?tab=view&id='.$admin_id);
			echo 'Password has been successfully updated!<br>';
			echo 'You will be redirected to the profile page in 5 seconds.';
			die(); 
		} else {
			header('Location:adminprofile.php?tab=pwrd&id='.$admin_id.'&error='.join($error,urlencode('<br/>'))); //Do we need die() after this line?
			die();
		}
		break;
		
		case false: //adding situation
		$admin_username = isset($_POST['admin_username'])? trim($_POST['admin_username']):'';
				
		$error = array();
		//Need to check if the proposed username (for new admin) already exists!
		$query = 'SELECT * 
					FROM admin_login 
					WHERE admin_username = "'.$admin_username.'"';
		$result = mysqli_query($db,$query) or die(mysqli_error($db));
		$numrows = mysqli_num_rows($result);
		
		if($numrows>0){
			$error[] = urlencode('The username you entered already exists. Please try another username! ');
		} 
		elseif((strlen($admin_username)<6)||(strlen($admin_username)>25)){
			$error[] = urlencode('The username must be between 6 and 25 characters long! ');
		}
		elseif($_POST['new_pwrd'] != $_POST['new_pwrd2']) {  //Can we actually do this??
			$error[] = urlencode('The 2 password fields do not match. Please try again! ');
		} 
		elseif(strlen($_POST['new_pwrd'])<6) { 
			$error[] = urlencode('Your password must contain a minimum of 6 characters! ');
		}	//See pg47 in Ebook for this type of elseif example!		
		
		if(empty($error)){ //Fill the necessary Session vars
		
			$_SESSION['addadmin']['admin_username'] = $admin_username;
			$_SESSION['addadmin']['admin_password'] = $_POST['new_pwrd'];
		
			header('Location:adminprofile.php?tab=view');  //go to next part of the form
			//do we need die() here?
			die();
		} else {
			header('Location:adminprofile.php?tab=pwrd&error='.join($error,urlencode('<br/>'))); //Do we need die() after this line?
			die();
		}
		break;
	}
	break; //end of 'pwrd' case
	
	
	case 'confirmadd': //this is only applicable to the Adding situation!
	if($_POST['submit']=='Cancel'){
		unset($_SESSION['addadmin']);
		header('Location:adminmngt.php'); //No need for a message with 5 sec delay
		die();
	} else { // Confirm & Add
		$query = 'INSERT INTO admin
					(admin_name,employee_id,admin_level,admin_phone,admin_email,comments,admin_joindate)
					VALUES
					("'.$_SESSION['addadmin']['admin_name'].'",
					"'.$_SESSION['addadmin']['employee_id'].'",
					'.$_SESSION['addadmin']['admin_level'].',
					"'.$_SESSION['addadmin']['admin_phone'].'",
					"'.$_SESSION['addadmin']['admin_email'].'",
					"'.$_SESSION['addadmin']['comments'].'",
					"'.$_SESSION['addadmin']['admin_joindate'].'")';

		mysqli_query($db,$query) or die(mysqli_error($db));
		$admin_id = mysqli_insert_id($db);
		
		$query = 'INSERT INTO admin_login
					(admin_username,admin_password,admin_id)
					VALUES
					("'.$_SESSION['addadmin']['admin_username'].'",
					PASSWORD("'.$_SESSION['addadmin']['admin_password'].'"),
					'.$admin_id.')';
					
		mysqli_query($db,$query) or die(mysqli_error($db));
				
		unset($_SESSION['addadmin']);
		header('Refresh: 5; URL=adminmngt.php');
		echo 'New admin information has been successfully updated!<br>';
		echo 'You will be redirected to the admin list in 5 seconds.';
		die();
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
				$_SESSION['logo'] = imagecreatetruecolor($thumb_width,$thumb_height); //Note that this session var is common to both edit & add, it is not a part of the addadmin array!
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
				$_SESSION['pic'] = imagecreatetruecolor($pic_width,$pic_height); //Note that this session var is common to both edit & add, it is not a part of the addadmin array!
				imagecopyresampled($_SESSION['pic'],$image,0,0,0,0,$pic_width,$pic_height,$width,$height);
				$pic = 1;
				$pic_alreadyprocessed=false;
			}
		}
		
		switch(isset($admin_id)){
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
			//Save the image in session add admin array
			}
			if(($pic==1)&&(pic_alreadyprocessed==false)){ 
			//Save the image in session add admin array
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