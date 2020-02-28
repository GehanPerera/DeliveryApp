<?php
session_start();

include 'dbconnect.php';

include 'functions.php';

$basis = isset($_POST['basis'])? $_POST['basis']:'ordercount';
$vid = isset($_REQUEST['vid'])? $_REQUEST['vid']:'all'; //Just need to have access controls above to ensure that the url para is always there for a vendor user so that $vid will never become initialized as "all"
$date_filter = isset($_POST['date_filter'])? $_POST['date_filter']:'day';
$start_date = ($date_filter=='custom')? $_POST['start_date']:'';
$end_date = ($date_filter=='custom')? $_POST['end_date']:date('d-m-Y'); //Good to set the default end date to today's date

$error = array();


switch($basis){
	case 'ordercount':
	$query_part = 'SUM(order_product.quantity)';
	break;
	case 'sales':
	$query_part = 'SUM(order_product.quantity * order_product.unit_price)';
	break;
}

switch($vid){
	case 'all':
	$vendor_condition = 'products.vendor_id IS NOT NULL';
	break;
	default:
	$vendor_condition = 'products.vendor_id = '.$vid;
	break;
}

switch($date_filter){
	case 'all':
	$date_condition = 'orders.order_time IS NOT NULL';
	break;
	case 'day':
	$date = date('Y-m-d H:i:s', strtotime('-24 hours'));
	$date_condition = 'orders.order_time >"'.$date.'"';
	break;
	case 'week':
	$date = date('Y-m-d H:i:s', strtotime('-168 hours'));
	$date_condition = 'orders.order_time >"'.$date.'"';
	break;
	case 'month':
	$date = date('Y-m-d H:i:s', strtotime('-1 month'));
	$date_condition = 'orders.order_time >"'.$date.'"';
	break;
	case '6months':
	$date = date('Y-m-d H:i:s', strtotime('-6 months'));
	$date_condition = 'orders.order_time >"'.$date.'"';
	break;
	case 'year':
	$date = date('Y-m-d H:i:s', strtotime('-1 year'));
	$date_condition = 'orders.order_time >"'.$date.'"';
	break;
	case 'custom':
	//Do we need to add 24 hours to the end_date to actually count the end date itself?
	if(!preg_match('|^\d{2}-\d{2}-\d{4}$|',$start_date)){
		$error[] = 'Start Date must be in dd-mm-yyyy format'; //No need for urlencode() here because processing is done in the same script!
	} else {
		list($day,$month,$year) = explode('-',$start_date);
		if(!checkdate($month,$day,$year)){
			$error[] = 'Please enter a valid start date.';
		} else {
			$start_date = date('Y-m-d H:i:s',mktime(0,0,0,$month,$day,$year));
//			$start_date = strtotime("$year-$month-$day");
		}
	}
	
	if(!preg_match('|^\d{2}-\d{2}-\d{4}$|',$end_date)){
		$error[] = 'End Date must be in dd-mm-yyyy format'; //No need for urlencode() here because processing is done in the same script!
	} else {
		list($day,$month,$year) = explode('-',$end_date);
		if(!checkdate($month,$day,$year)){
			$error[] = 'Please enter a valid end date.';
		} else {
			$end_date = date('Y-m-d H:i:s',mktime(0,0,0,$month,$day,$year));
//			$end_date = strtotime("$year-$month-$day");
		}
	}
	
	if(empty($error)){
		$end_date = date('Y-m-d H:i:s', strtotime('+24 hours', strtotime($end_date)));
		$date_condition = 'orders.order_time >"'.$start_date.'" AND orders.order_time <"'.$end_date.'"';
	}
	
	break;
}

if(isset($date_condition)){
$query = 'SELECT products.product_id, '.$query_part.' AS rankval
			FROM orders, order_product, products
				WHERE '.$date_condition.' AND '.$vendor_condition.' AND orders.order_status="Delivered" AND orders.order_id=order_product.order_id AND products.product_id=order_product.product_id
					 GROUP BY products.product_id
					 ORDER BY '.$query_part.' DESC';

$result2 = mysqli_query($db,$query) or die(mysqli_error($db));
}

?>
<html>
  <head>
  <style type="text/css">

h2, h3 {
	text-align: center;
}
/*
h3 {
	margin-top: 30px;
}

.LineButtons {
	width: 90%;
	margin-left: auto;
	margin-right: auto;
	margin-bottom: 60px;
}

table input {
    width: 100%;
    box-sizing: border-box;
}
*/
table {
	background-color: #999;
	clear: both;
}

th {
	background-color: #999;
}
.odd_row {
	background-color: #EEE;
}
.even_row {
	background-color: #FFF;
}

/*
input[type=submit] {
    width: 5em;  
	height: 3em;
}
*/

</style>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Task', 'Hours per Day'],

<?php
if(isset($date_condition)){
	$numrows = mysqli_num_rows($result2);
	$counteri = 0;
	while($row=mysqli_fetch_assoc($result2)){
		extract($row);
		$counteri = $counteri + 1;
		echo '[\''.str_replace("'","\\'",get_productname($product_id)).'\','.$rankval.']';
		echo ($counteri<$numrows)? ',':'';
	}
}
?>				
        ]);

        var options = {
          title: 'Top Products'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
      }
    </script>
	<script type="text/javascript">
function customstate(s1,row){
	var s1 = document.getElementById(s1);
	var row = document.getElementById(row);
	if(s1.options[6].selected==true){
		row.style.display = 'block';
	} else {
		row.style.display = 'none';
	}

}
</script>
	
  </head>
  <body>
<?php

include 'NavMenu.php'; 

?>
<h2>Popular Products (for <?php echo ($vid=='all')? 'All Vendors)':get_vendorname($vid).')'; ?></h2>
<div>
<?php
if(!empty($error)){
	echo '<div class="LineButtons2" style="color:red; font-weight:bold;">'.join($error,'<br/>').'</div>';
}
?>	
<form action="report_topproducts.php<?php echo isset($_GET['vid'])? '?vid='.$_GET['vid']:''; ?>" method="POST">
<table width="90%" align="center">
<tr>
	<td width="12%">Ranking based on:</td>
	<td colspan="3"><input type="radio" name="basis" value="ordercount" <?php echo ($basis=='ordercount')? 'checked="checked"':''; ?> />Quantity Sold by Product
					<input type="radio" name="basis" value="sales" <?php echo ($basis=='sales')? 'checked="checked"':''; ?> />Total Sales by Product
	</td></tr>
<?php
	if(!isset($_GET['vid'])){
?>		
	<tr>
	<td><b>Select Vendor</b></td>
		<td colspan="2"><select name="vid">
			<option value="all">All</option>
<?php
	$query = 'SELECT vendor_id, vendor_name
			FROM vendor
			WHERE vendor_isactive = "1"';

	$result = mysqli_query($db,$query) or die(mysqli_error($db));
	while($row=mysqli_fetch_assoc($result)){
		if($vid==$row['vendor_id']){
			echo '<option value="'.$row['vendor_id'].'" selected="selected">'.$row['vendor_name'].'</option>';
		} else {
			echo '<option value="'.$row['vendor_id'].'">'.$row['vendor_name'].'</option>';
		}
	}
?>
			</select></td><td></td>
	</tr>
<?php
	}
?>	
	<tr>
	<td>Time Range:</td>
	<td><select id="date_filter" name="date_filter" onchange="customstate(this.id,'custom_range')">
			<option value="all" <?php echo ($date_filter=='all')? 'selected="selected"':''; ?> >All History</option>
			<option value="day" <?php echo ($date_filter=='day')? 'selected="selected"':''; ?> >Past Day</option>
			<option value="week" <?php echo ($date_filter=='week')? 'selected="selected"':''; ?> >Past Week</option>
			<option value="month" <?php echo ($date_filter=='month')? 'selected="selected"':''; ?> >Past Month</option>
			<option value="6months" <?php echo ($date_filter=='6months')? 'selected="selected"':''; ?> >Past 6 Months</option>
			<option value="year" <?php echo ($date_filter=='year')? 'selected="selected"':''; ?> >Past Year</option>
			<option value="custom" <?php echo ($date_filter=='custom')? 'selected="selected"':''; ?> >Enter Custom Range</option>
		</select></td><td></td><td></td>
	</tr>
	</table>

	<table width="90%" align="center">
	<tr id="custom_range" <?php echo ($date_filter=='custom')? '':'style="display:none"'; ?>>
	<td width="15%" style="text-align:right;">From:<br/><i>"dd-mm-yyyy" format</i></td>
	<td width="15%"><input type="text" name="start_date" value="<?php echo empty($start_date)? '':date('d-m-Y',strtotime($start_date)); ?>" /></td>
	<td width="15%" style="text-align:right;">To:<br/><i>"dd-mm-yyyy" format</i></td>
	<td><input type="text" name="end_date" value="<?php echo ($date_filter=='custom')? date('d-m-Y', strtotime('-24 hours', strtotime($end_date))):date('d-m-Y',strtotime($end_date)); ?>" /></td>
	</tr><tr>
	<td><input type="submit" name="submit" value="Apply" /></td><td></td><td></td><td></td>
	</tr>
</table>
</form>
</div>
<?php


?>
    <div id="piechart" style="width: 700px; height: 400px; margin-left: auto; margin-right: auto;"></div>
  
<div>
	<table border="1" width="50%" align="center">
<tr>
	<th>Product Name</th>
	<th><?php echo ($basis=='ordercount')? 'Number of Sold Items':'Total Sales'; ?></th>
</tr>

<?php
if(isset($date_condition)){
// Is there a way to set the pointer back to the beginning without having to rerun this query???
$query = 'SELECT products.product_id, '.$query_part.' AS rankval
			FROM orders, order_product, products
				WHERE '.$date_condition.' AND '.$vendor_condition.' AND orders.order_status="Delivered" AND orders.order_id=order_product.order_id AND products.product_id=order_product.product_id
					 GROUP BY products.product_id
					 ORDER BY '.$query_part.' DESC';

$result2 = mysqli_query($db,$query) or die(mysqli_error($db));

$odd = true;
while($row=mysqli_fetch_assoc($result2)){
	echo ($odd==true)? '<tr class="odd_row">':'<tr class="even_row">';
	extract($row);
	echo '<td>'.get_productname($product_id).'</td>';
	echo '<td>'.$rankval.'</td>';
	echo '</tr>';
	$odd = !$odd;
}
}
?>
</table>
</div>  

</body>
</html>
