<?php

include 'dbconnect.php';

include 'functions.php';

if($_POST['submit']=='Cancel'){
	header('Location:vendor_productview.php?id='.$_POST['vendor_id']); //Do we need die() after this line?
	die();
}

//Think of the exciting uses of the $_REQUEST array! Eg: script B will receive x as $_GET['x'] if it is coming from script A & $_POST['x'] if it is coming from script c.  
//Using $_REQUEST['x'] we can catch either one!   
//validate image only if user has uploaded a file because image is not mandatory!  
//In these validation instances providing a button for the user to manually redirect to previous script seems to be necessary because it enables passing of required info (vendor & product ids) through hidden fields.
//BEST METHOD WOULD HAVE BEEN: isset product id!!! in prev form (the "middle script") to figure out whether edit or add!!! (instead of using a url parameter or checking the $_POST['submit'] values!)

if($_POST['product_name']=='' || $_POST['product_price']=='0.00') {  //Need to validate the price better!
	echo '<p>You have not filled the product name &/or price fields correctly! <br>';
	echo 'Click to go back to the form: ';
//	echo '<form action="productupdate.php?type='.$_GET['type'].'" method="POST">'; //possibly a neater way than what is actually done, but this requires even the current script to have a url parameter!
	echo '<form action="productupdate.php?type='; //won't be necessary to have any url parameter if we had used isset prod id in prev script!!!
	echo ($_POST['submit']=='Edit')? 'existing':'new';
	echo '" method="POST">';
	echo ISSET($_POST['product_id'])? '<input type="hidden" name="product_id" value="'.$_POST['product_id'].'"/>' : ''; //alternatively, can check $_POST[submit] value!
	echo '<input type="hidden" name="vendor_id" value="'.$_POST['vendor_id'].'"/>';
	echo '<input type="submit" name="submit" value="Go Back"/>';
	echo '</br>';
	echo '</form>';
	die();
}

if(($_FILES['fileupload']['error'] != UPLOAD_ERR_OK) && ($_FILES['fileupload']['error'] != UPLOAD_ERR_NO_FILE)){
	echo '<p>There was an error with the image upload! <br>';
	echo 'Click to go back to the form: ';
//	echo '<form action="productupdate.php?type='.$_GET['type'].'" method="POST">';	
	echo '<form action="productupdate.php?type=';
	echo ($_POST['submit']=='Edit')? 'existing':'new';
	echo '" method="POST">';
	echo ISSET($_POST['product_id'])? '<input type="hidden" name="product_id" value="'.$_POST['product_id'].'"/>' : '';
	echo '<input type="hidden" name="vendor_id" value="'.$_POST['vendor_id'].'"/>';
	echo '<input type="submit" name="submit" value="Go Back"/>';
	echo '</br>';
	echo '</form>';
	die();
}

if($_FILES['fileupload']['error'] == UPLOAD_ERR_OK){ //alt validation option - do like in pg 190 with a string var called $error
	list($width,$height,$type,$attr) = getimagesize($_FILES['fileupload']['tmp_name']);
	switch($type) {
		case IMAGETYPE_GIF:
			$image=imagecreatefromgif($_FILES['fileupload']['tmp_name']);
			break;
		case IMAGETYPE_JPEG:
			$image=imagecreatefromjpeg($_FILES['fileupload']['tmp_name']);
			break;
		case IMAGETYPE_PNG:
			$image=imagecreatefromjpeg($_FILES['fileupload']['tmp_name']);
			break;
		default:
			break;
	}
	if(!ISSET($image)||!($image)) {
		echo '<p>The file you uploaded was not a supported filetype! <br>';
		echo 'Click to try again: ';
//	echo '<form action="productupdate.php?type='.$_GET['type'].'" method="POST">';	
		echo '<form action="productupdate.php?type=';
		echo ($_POST['submit']=='Edit')? 'existing':'new';
		echo '" method="POST">';
		echo ISSET($_POST['product_id'])? '<input type="hidden" name="product_id" value="'.$_POST['product_id'].'"/>' : '';
		echo '<input type="hidden" name="vendor_id" value="'.$_POST['vendor_id'].'"/>';
		echo '<input type="submit" name="submit" value="Go Back"/>';
		echo '</br>';
		echo '</form>';
		die();
	}
//if we get to this point, there is an image
//What if width != height ?
	$thumb_width = 200;
	$thumb_height = 200*($height/$width);
	$thumb = imagecreatetruecolor($thumb_width,$thumb_height);
	imagecopyresampled($thumb,$image,0,0,0,0,$thumb_width,$thumb_height,$width,$height);
}

$dateadded = date("Y-m-d H:i:s"); //but in the db, the field is datetime - need to find appropriate fucntion!
	
switch($_POST['submit']){
	case 'Edit':
		$query = 'UPDATE products SET
					product_name="'.$_POST['product_name'].'",
					product_price='.str_replace(',','',$_POST['product_price']).',
					product_desc="'.$_POST['product_desc'].'"
					WHERE
					product_id = '.$_POST['product_id'];
		break;
		
	case 'Add': //Must include vendor_id value in this case!
		$query = 'INSERT INTO
					products
						(product_name,product_price,product_desc,product_dateadded,vendor_id)
					VALUES
						("'.$_POST['product_name'].'",
						'.str_replace(',','',$_POST['product_price']).',
						"'.$_POST['product_desc'].'",
						"'.$dateadded.'",
						'.$_POST['vendor_id'].')';
		break;
}

//if (isset($query)) {
		mysqli_query($db,$query) or die(mysqli_error($db));
//}

if($_FILES['fileupload']['error'] == UPLOAD_ERR_OK){ //IF an image has been successfully uploaded... (alternatively, can check for isset image)
	$imagename = ($_POST['submit']=='Edit')? $_POST['product_id'].'.jpg' : mysqli_insert_id($db).'.jpg';
	$dir = 'images/products';
	$thumbdir = $dir.'/thumbs';
	imagejpeg($image, $dir.'/'.$imagename,100);
	imagejpeg($thumb, $thumbdir.'/'.$imagename,100);
	imagedestroy($image);
	imagedestroy($thumb);
}

header('Refresh: 5; URL=vendor_productview.php?id='.$_POST['vendor_id']); //this may work in spite of all the echo statements above, because the only path to get to this point is free of any browser output!
echo 'product information has been successfully saved!<br>';
echo 'You will be redirected to the product list in 5 seconds.';

	
?>