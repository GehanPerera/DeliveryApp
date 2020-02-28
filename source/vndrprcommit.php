<?php
session_start();

include 'dbconnect.php';



include 'functions.php';

if(isset($_POST['vendor_id'])){
	$vendor_id = $_POST['vendor_id'];
}


switch($_POST['action']) {
	
	case 'general':
	
	$error = array();
	
	$vendor_name = isset($_POST['vendor_name'])? trim($_POST['vendor_name']):'';
	if(empty($vendor_name)){
		$error[] = urlencode('Company Name field cannot be blank. ');
	}
	
	$vendor_address = isset($_POST['vendor_address'])? trim($_POST['vendor_address']):'';
	if(empty($vendor_address)){
		$error[] = urlencode('Address cannot be blank. ');
	}

	$vendor_city = isset($_POST['vendor_city'])? trim($_POST['vendor_city']):'';
	if(empty($vendor_city)){
		$error[] = urlencode('Please select a city. ');
	}

	$vendor_phone = isset($_POST['vendor_phone'])? trim($_POST['vendor_phone']):'';
	$vendor_phone = str_replace('-','',$vendor_phone);
	$vendor_phone = str_replace('(','',$vendor_phone);
	$vendor_phone = str_replace(')','',$vendor_phone);
	$vendor_phone = str_replace(' ','',$vendor_phone);
	if(!preg_match('|^\d{10}$|',$vendor_phone)){
		$error[] = urlencode('Please enter a valid phone number. ');
	} else {
		$area = substr($vendor_phone,0,3);
		$rest = substr($vendor_phone,3);
		$vendor_phone = '('.$area.')'.$rest;
	}
	
	$vendor_email = isset($_POST['vendor_email'])? trim($_POST['vendor_email']):'';  //need to validate this better
	if(empty($vendor_email)){  
		$error[] = urlencode('Email cannot be blank. ');
	}

	$comments = isset($_POST['comments'])? trim($_POST['comments']):'';  
	//Okay for comments to be empty!
/*
	$weekday_start = isset($_POST['weekday_start'])? trim($_POST['weekday_start']):'';
	if(!preg_match('|^\d{2}:\d{2}:\d{2}$|',$weekday_start)){
		$error[] = urlencode('Please enter a time in hh:mm:ss format. ');
	} else {
		list($hr,$min,$sec) = explode(':',$weekday_start); //is there a checktime() function (like checkdate)?
		if($hr>23||$min>59||$sec>59){
			$error[] = urlencode('Please enter a valid time. ');
		}
		//I don't think we need to use the mktime() function or concetanete the hr, min, sec vars (inside another else clause) because, at this point, they would already in the correct format & order within the $weekday_start variable!
	}
*/	
	$weekday_start = isset($_POST['weekday_start'])? trim($_POST['weekday_start']):'';
	if(!preg_match('|^\d{2}:\d{2}$|',$weekday_start)){
		$error[] = urlencode('Please enter a time in hh:mm format. ');
	} else {
		list($hr,$min) = explode(':',$weekday_start); //is there a checktime() function (like checkdate)?
		if($hr>23||$min>59){
			$error[] = urlencode('Please enter a valid time. ');
		} else {
			$weekday_start = $hr.':'.$min.':00'; //perhaps we could have used the mktime() function with 0s for day, month & year (See pg231)
		}
	}

	
	$weekday_end = isset($_POST['weekday_end'])? trim($_POST['weekday_end']):'';
	if(!preg_match('|^\d{2}:\d{2}$|',$weekday_end)){
		$error[] = urlencode('Please enter a time in hh:mm format. ');
	} else {
		list($hr,$min) = explode(':',$weekday_end); //is there a checktime() function (like checkdate)?
		if($hr>23||$min>59){
			$error[] = urlencode('Please enter a valid time. ');
		} else {
			$weekday_end = $hr.':'.$min.':00'; //perhaps we could have used the mktime() function with 0s for day, month & year (See pg231)
		}
	}
		
	//Good if the following fields were not made mandatory - but cannot handle now!
	
	$sat_start = isset($_POST['sat_start'])? trim($_POST['sat_start']):'';
	if(!preg_match('|^\d{2}:\d{2}$|',$sat_start)){
		$error[] = urlencode('Please enter a time in hh:mm format. ');
	} else {
		list($hr,$min) = explode(':',$sat_start); //is there a checktime() function (like checkdate)?
		if($hr>23||$min>59){
			$error[] = urlencode('Please enter a valid time. ');
		} else {
			$sat_start = $hr.':'.$min.':00'; //perhaps we could have used the mktime() function with 0s for day, month & year (See pg231)
		}
	}
		
	$sat_end = isset($_POST['sat_end'])? trim($_POST['sat_end']):'';
	if(!preg_match('|^\d{2}:\d{2}$|',$sat_end)){
		$error[] = urlencode('Please enter a time in hh:mm format. ');
	} else {
		list($hr,$min) = explode(':',$sat_end); //is there a checktime() function (like checkdate)?
		if($hr>23||$min>59){
			$error[] = urlencode('Please enter a valid time. ');
		} else {
			$sat_end = $hr.':'.$min.':00'; //perhaps we could have used the mktime() function with 0s for day, month & year (See pg231)
		}
	}

	$sun_start = isset($_POST['sun_start'])? trim($_POST['sun_start']):'';
	if(!preg_match('|^\d{2}:\d{2}$|',$sun_start)){
		$error[] = urlencode('Please enter a time in hh:mm format. ');
	} else {
		list($hr,$min) = explode(':',$sun_start); //is there a checktime() function (like checkdate)?
		if($hr>23||$min>59){
			$error[] = urlencode('Please enter a valid time. ');
		} else {
			$sun_start = $hr.':'.$min.':00'; //perhaps we could have used the mktime() function with 0s for day, month & year (See pg231)
		}
	}
		
	$sun_end = isset($_POST['sun_end'])? trim($_POST['sun_end']):'';
	if(!preg_match('|^\d{2}:\d{2}$|',$sun_end)){
		$error[] = urlencode('Please enter a time in hh:mm format. ');
	} else {
		list($hr,$min) = explode(':',$sun_end); //is there a checktime() function (like checkdate)?
		if($hr>23||$min>59){
			$error[] = urlencode('Please enter a valid time. ');
		} else {
			$sun_end = $hr.':'.$min.':00'; //perhaps we could have used the mktime() function with 0s for day, month & year (See pg231)
		}
	}

	if(!empty($error)){
		$urlparas = isset($vendor_id)? 'tab=general&id='.$vendor_id : 'tab=general';
		header('Location:vendorprofile.php?'.$urlparas.'&error='.join($error,urlencode('<br/>'))); //Do we need die() after this line?
		die();
	}
	
	//There are no input errors at this point. Now let's address the Add vs Edit situation for the general info tab
	
	switch(isset($vendor_id)){
		case true:   //Edit situation
		$query = 'UPDATE vendor SET
					vendor_name="'.$vendor_name.'",
					vendor_address="'.$vendor_address.'",
					vendor_city="'.$vendor_city.'",
					vendor_phone="'.$vendor_phone.'",
					vendor_email="'.$vendor_email.'",
					comments="'.$comments.'",
					weekday_start="'.$weekday_start.'",
					weekday_end="'.$weekday_end.'",
					sat_start="'.$sat_start.'",
					sat_end="'.$sat_end.'",
					sun_start="'.$sun_start.'",
					sun_end="'.$sun_end.'"
					WHERE
					vendor_id='.$vendor_id;
					
		mysqli_query($db,$query) or die(mysqli_error($db));
		
		header('Refresh: 5; URL=vendorprofile.php?tab=view&id='.$vendor_id);
		echo 'Company information has been successfully updated!<br>';
		echo 'You will be redirected to the profile page in 5 seconds.';
		die(); //Is this really needed given the double break below?
		break;
		
		case false:  //Add situation
		//Take values from the submitted form fields & store in the session vars
		//First unset any existing session vars
		unset($_SESSION['addvendor']);
		$join_date = date('Y-m-d');
		
		$_SESSION['addvendor']['vendor_name'] = $vendor_name;
		$_SESSION['addvendor']['vendor_address'] = $vendor_address;
		$_SESSION['addvendor']['vendor_city'] = $vendor_city;
		$_SESSION['addvendor']['vendor_phone'] = $vendor_phone;
		$_SESSION['addvendor']['vendor_email'] = $vendor_email;
		$_SESSION['addvendor']['comments'] = $comments;
		$_SESSION['addvendor']['weekday_start'] = $weekday_start;
		$_SESSION['addvendor']['weekday_end'] = $weekday_end;
		$_SESSION['addvendor']['sat_start'] = $sat_start;
		$_SESSION['addvendor']['sat_end'] = $sat_end;
		$_SESSION['addvendor']['sun_start'] = $sun_start;
		$_SESSION['addvendor']['sun_end'] = $sun_end;
		$_SESSION['addvendor']['vendor_joindate'] = $join_date;
		
		header('Location:vendorprofile.php?tab=pwrd');  //go to next part of the form
		//do we need die() here?
		die();
		break;
	}
	break;  //End of 'general' case

	case 'pwrd':
	switch(isset($vendor_id)){
		case true:   //Edit situation
		$error = array();
		$vendor_username = get_vendor_un($vendor_id);

		if(($_SESSION['usertype']=='admin')&&($_SESSION['accesslevel']==1)){
			$admin_username = $_SESSION['username'];  
			$query = 'SELECT * 
					FROM admin_login 
					WHERE admin_username = "'.$admin_username.'"
					AND admin_password = PASSWORD("'.$_POST['old_pwrd'].'")';
		} else {			
			$query = 'SELECT * 
					FROM vendor_login 
					WHERE vendor_username = "'.$vendor_username.'"
					AND vendor_password = PASSWORD("'.$_POST['old_pwrd'].'")';
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
			$query = 'UPDATE vendor_login 
						SET vendor_password = PASSWORD("'.$_POST['new_pwrd'].'") 
							WHERE vendor_username="'.$vendor_username.'"';
			mysqli_query($db,$query) or die(mysqli_error($db));
		
			header('Refresh: 5; URL=vendorprofile.php?tab=view&id='.$vendor_id);
			echo 'Password has been successfully updated!<br>';
			echo 'You will be redirected to the profile page in 5 seconds.';
			die(); 
		} else {
			header('Location:vendorprofile.php?tab=pwrd&id='.$vendor_id.'&error='.join($error,urlencode('<br/>'))); //Do we need die() after this line?
			die();
		}
		break;
		
		case false: //adding situation
		$vendor_username = isset($_POST['vendor_username'])? trim($_POST['vendor_username']):'';
				
		$error = array();
		//Need to check if the proposed username (for new vendor) already exists!
		$query = 'SELECT * 
					FROM vendor_login 
					WHERE vendor_username = "'.$vendor_username.'"';
		$result = mysqli_query($db,$query) or die(mysqli_error($db));
		$numrows = mysqli_num_rows($result);
		
		if($numrows>0){
			$error[] = urlencode('The username you entered already exists. Please try another username! ');
		} 
		elseif((strlen($vendor_username)<6)||(strlen($vendor_username)>25)){
			$error[] = urlencode('The username must be between 6 and 25 characters long! ');
		}
		elseif($_POST['new_pwrd'] != $_POST['new_pwrd2']) {  //Can we actually do this??
			$error[] = urlencode('The 2 password fields do not match. Please try again! ');
		} 
		elseif(strlen($_POST['new_pwrd'])<6) { 
			$error[] = urlencode('Your password must contain a minimum of 6 characters! ');
		}	//See pg47 in Ebook for this type of elseif example!		
		
		if(empty($error)){ //Fill the necessary Session vars
		
			$_SESSION['addvendor']['vendor_username'] = $vendor_username;
			$_SESSION['addvendor']['vendor_password'] = $_POST['new_pwrd'];
		
			header('Location:vendorprofile.php?tab=upload');  //go to next part of the form
			//do we need die() here?
			die();
		} else {
			header('Location:vendorprofile.php?tab=pwrd&error='.join($error,urlencode('<br/>'))); //Do we need die() after this line?
			die();
		}
		break;
	}
	break; //end of 'pwrd' case
	
	case 'upload': //Need to research whether we can actually store images in Session vars!
		
		if(!isset($_SESSION['logo'])||($_SESSION['logo'] != 1)){ //unless there is already an image
			if($_FILES['fileupload_logo']['error'] == UPLOAD_ERR_NO_FILE){
				$_SESSION['logo'] = 0;
			} elseif($_FILES['fileupload_logo']['error'] != UPLOAD_ERR_OK){
				$_SESSION['logo'] = -1;
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
					$_SESSION['logo'] = -1;
				} else { //there IS an image!
					$logo_width = 200;
					$logo_height = 200*($height/$width); //need to play around with these numbers
					$logo = imagecreatetruecolor($logo_width,$logo_height); //Note that this session var is common to both edit & add, it is not a part of the addvendor array!
					imagecopyresampled($logo,$image,0,0,0,0,$logo_width,$logo_height,$width,$height);
					
					imagedestroy($image);
					unset($image);
				}
			}
		}
		
		if(!isset($_SESSION['pic'])||($_SESSION['pic'] != 1)){ //unless there is already an image
			if($_FILES['fileupload_pic']['error'] == UPLOAD_ERR_NO_FILE){
				$_SESSION['pic'] = 0;
			} elseif($_FILES['fileupload_pic']['error'] != UPLOAD_ERR_OK){
				$_SESSION['pic'] = -1;
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
					$_SESSION['pic'] = -1;
				} else { //there IS an image!
					$pic_width = 1800;
					$pic_height = 1800*($height/$width); //need to play around with these numbers
					$pic = imagecreatetruecolor($pic_width,$pic_height); //Note that this session var is common to both edit & add, it is not a part of the addvendor array!
					imagecopyresampled($pic,$image,0,0,0,0,$pic_width,$pic_height,$width,$height);
					
					imagedestroy($image);
					unset($image);
				}
			}
		}
		
		switch(isset($vendor_id)){
			
			case true: //Edit situation
			
			if(isset($logo)&&($logo != false)){ 
			//Save the image in folder
			$imagename = $vendor_id.'.jpg';
			$logodir = 'images/vendors/logos';
			imagejpeg($logo, $logodir.'/'.$imagename,100);
			imagedestroy($logo);
			$_SESSION['logo'] = 1;
			}
			
			if(isset($pic)&&($pic != false)){ 
			//Save the image in folder
			$imagename = $vendor_id.'.jpg';
			$picdir = 'images/vendors';
			imagejpeg($pic, $picdir.'/'.$imagename,100);
			imagedestroy($pic);
			$_SESSION['pic'] = 1;
			}
			
			break;
			
			case false:
			
			if(isset($logo)&&($logo != false)){ 
			//Save the image in temporary location & save the destination in a session var - this way the image can even be displayed before final confimation!
			$destination = 'images/vendors/tmp/logo.jpg';
			imagejpeg($logo, $destination,100);
			$_SESSION['addvendor']['logo'] = $destination;
			imagedestroy($logo);
			$_SESSION['logo'] = 1;
			}
			
			if(isset($pic)&&($pic != false)){ 
			//Save the image in temporary location & save the destination in a session var - this way the image can even be displayed before final confimation!
			$destination = 'images/vendors/tmp/pic.jpg';
			imagejpeg($pic, $destination,100);
			$_SESSION['addvendor']['pic'] = $destination;
			imagedestroy($pic);
			$_SESSION['pic'] = 1;
			}
			
			break;
		}
		
		if($_SESSION['logo']+$_SESSION['pic']>0){//Success
			//redirect to 'view' with or without get(id) url para depending on whether add or edit!
			switch(isset($vendor_id)){
			case true: //Edit situation
				header('Refresh: 5; URL=vendorprofile.php?tab=view&id='.$vendor_id);
				echo 'Image(s) successfully saved!<br>';
				echo 'You will be redirected to the profile page in 5 seconds.';
				die();
			break;	
			case false:
				header('Location:vendorprofile.php?tab=view');  //go to summary & confirmation screen
				//do we need die() here?
				die();
			break;
			}	
		} else { //At least one attempted upload has failed OR no attempt has been made to upload either image (case of two 0s)
			//Back to 'upload' form
			$urlparas = isset($vendor_id)? 'tab=upload&id='.$vendor_id : 'tab=upload';
			header('Location:vendorprofile.php?'.$urlparas); //Do we need die() after this line?
			die();
		}
		
	break;  //End of upload case
	
	case 'confirmadd': //this is only applicable to the Adding situation!
	if($_POST['submit']=='Cancel'){
		unset($_SESSION['addvendor']);
		header('Location:vendormngt.php'); //No need for a message with 5 sec delay
		die();
	} else { // Confirm & Add
		$query = 'INSERT INTO vendor
					(vendor_name,vendor_address,vendor_city,vendor_phone,vendor_email,comments,weekday_start,weekday_end,sat_start,sat_end,sun_start,sun_end,vendor_joindate)
					VALUES
					("'.$_SESSION['addvendor']['vendor_name'].'",
					"'.$_SESSION['addvendor']['vendor_address'].'",
					"'.$_SESSION['addvendor']['vendor_city'].'",
					"'.$_SESSION['addvendor']['vendor_phone'].'",
					"'.$_SESSION['addvendor']['vendor_email'].'",
					"'.$_SESSION['addvendor']['comments'].'",
					"'.$_SESSION['addvendor']['weekday_start'].'",
					"'.$_SESSION['addvendor']['weekday_end'].'",
					"'.$_SESSION['addvendor']['sat_start'].'",
					"'.$_SESSION['addvendor']['sat_end'].'",
					"'.$_SESSION['addvendor']['sun_start'].'",
					"'.$_SESSION['addvendor']['sun_end'].'",
					"'.$_SESSION['addvendor']['vendor_joindate'].'")';

		mysqli_query($db,$query) or die(mysqli_error($db));
		$vendor_id = mysqli_insert_id($db);
		
		$query = 'INSERT INTO vendor_login
					(vendor_username,vendor_password,vendor_id)
					VALUES
					("'.$_SESSION['addvendor']['vendor_username'].'",
					PASSWORD("'.$_SESSION['addvendor']['vendor_password'].'"),
					'.$vendor_id.')';
					
		mysqli_query($db,$query) or die(mysqli_error($db));
		
		if(isset($_SESSION['addvendor']['logo'])){
			$tmploc = $_SESSION['addvendor']['logo'];
			$imagename = $vendor_id.'.jpg';
			$logodir = 'images/vendors/logos';
			rename($tmploc, $logodir.'/'.$imagename);
		}
		
		if(isset($_SESSION['addvendor']['pic'])){
			$tmploc = $_SESSION['addvendor']['pic'];
			$imagename = $vendor_id.'.jpg';
			$picdir = 'images/vendors';
			rename($tmploc, $picdir.'/'.$imagename);
		}
		
		unset($_SESSION['addvendor']);
		header('Refresh: 5; URL=vendormngt.php');
	//	header('Refresh: 5; URL=index.php');
		echo 'New vendor information has been successfully updated!<br>';
		echo 'You will be redirected to the vendor list in 5 seconds.';
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
				$_SESSION['logo'] = imagecreatetruecolor($thumb_width,$thumb_height); //Note that this session var is common to both edit & add, it is not a part of the addvendor array!
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
				$_SESSION['pic'] = imagecreatetruecolor($pic_width,$pic_height); //Note that this session var is common to both edit & add, it is not a part of the addvendor array!
				imagecopyresampled($_SESSION['pic'],$image,0,0,0,0,$pic_width,$pic_height,$width,$height);
				$pic = 1;
				$pic_alreadyprocessed=false;
			}
		}
		
		switch(isset($vendor_id)){
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
			//Save the image in session add vendor array
			}
			if(($pic==1)&&(pic_alreadyprocessed==false)){ 
			//Save the image in session add vendor array
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