<?php
session_start();

include 'dbconnect.php';

include 'functions.php';

if($_POST['submit']=='Delete'){
	$product_dateinactive = date("Y-m-d H:i:s");
	
	$query = 'UPDATE products
				SET product_isactive = "0",
					product_dateinactive = "'.$product_dateinactive.'"
				WHERE product_id='.$_POST['product_id'];
				
	mysqli_query($db,$query) or die(mysqli_error($db));
	header('Refresh: 5; URL=vendor_productview.php?id='.$_POST['vendor_id']);

	echo 'Product successfully deleted!';
	
}			

else{ //EDIT & ADD.  We could have easily used die() function at the end of the above if clause to negate the need for the else!  

include 'NavMenu.php';

if($_GET['type']=='existing'){ //BEST METHOD WOULD HAVE BEEN to check: isset product_id to distinguish between Edit & Add!!! (instead of checking the $_POST['submit'] values OR using a url parameter as done here!)
	
	$query = 'SELECT * 
				FROM products
				WHERE product_id='.$_POST['product_id'];
	
	$result = mysqli_query($db,$query) or die(mysqli_error($db));
	
	$row = mysqli_fetch_assoc($result);
	extract($row);
} else {
	$product_name = '';
	$product_desc = '';
	$product_price = 0.00;
}//In the next script, must calculate & include product_dateadded as well!

echo '<h2> Product by '.get_vendorname($_POST['vendor_id']).'</h2>';

//Common form for both edit & add	
?>

<!--  <form action="productcommit.php?type=<?php //echo ($_GET['type']; ?>" method="POST" enctype="multipart/form-data">  -->
<form action="productcommit.php" method="POST" enctype="multipart/form-data">

<table>
<tr>
	<td>Product Name</td>
	<td><input type="text" name="product_name" value="<?php echo $product_name; ?>" /></td>
	<td style="text-align: right;">Price</td>
	<td style="text-align: right;"><input type="text" name="product_price" value="<?php echo number_format($product_price,2); ?>" /></td>
</tr><tr>
	<td>Description (Limit: 255 characters)</td>
	<td colspan="3">
	<textarea name="product_desc" rows="5" cols="50"><?php echo htmlspecialchars($product_desc); ?></textarea></td>
	</tr><tr>
	<td colspan="3" style="text-align: left; vertical-align: top;">
	Upload Image<br>
	Supported file types are: JPG,GIF & PNG only<br>
	To avoid distortion, please choose an image <br>
	with equal height & width</td>
	<td style="text-align: center;">
	<input type="file" name="fileupload" /></td>
</tr><tr>
	<td></td>
	<td><input type="submit" name="submit" value="Cancel" /></td><td></td>
	<td style="text-align:right;">
<?php
if($_GET['type']=='existing'){
	echo '<input type="hidden" name="product_id" value="'.$_POST['product_id'].'" />';
}
echo '<input type="hidden" name="vendor_id" value="'.$_POST['vendor_id'].'" />';

?>
	<input type="submit" name="submit" value="<?php echo ($_GET['type']=='existing')? 'Edit':'Add'; ?>" />
	</td>
	</tr>
	</table>
	</form>
	
<?php
} //end of elso clause
?>







