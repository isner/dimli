<?php
$urlpatch = (strpos($_SERVER['DOCUMENT_ROOT'], 'xampp') == true)?'/dimli':'';
if(!defined('MAIN_DIR')){define('MAIN_DIR',$_SERVER['DOCUMENT_ROOT'].$urlpatch);}
require_once(MAIN_DIR.'/_php/_config/session.php');
require_once(MAIN_DIR.'/_php/_config/connection.php');
require_once(MAIN_DIR.'/_php/_config/functions.php');
confirm_logged_in();


$processToUpdate = $_POST['process'];
$status = $_POST['status'];
$orderId = trim($_POST['orderNum']);
$todaysDate = date('Y-m-d');
$UnixTime = time(TRUE);


//--------------------
//  Order digitized
//--------------------

if ($processToUpdate == 'progress_dig'
	&& $_SESSION['priv_digitize'] == 1)
{
	$sql = "UPDATE dimli.order
				SET order_digitized = '{$status}',
					order_digitized_by = '{$_SESSION['username']}',
					order_digitized_on = '{$todaysDate}',
					last_update_by = '{$_SESSION['username']}'
				WHERE id = '{$orderId}' ";

	$result = db_query($mysqli, $sql);

	if ($status == '1') { 
	// User has completed this task

		$sql = "INSERT INTO dimli.Activity 
					SET UserID = '{$_SESSION['user_id']}', 
						RecordType = 'Order', 
						RecordNumber = '{$orderId}', 
						ActivityType = 'digitized',
						UnixTime = '{$UnixTime}' ";

		$log = db_query($mysqli, $sql);
	}
}

//--------------------
//  Images edited
//--------------------

elseif ($processToUpdate == 'progress_edi'
	&& $_SESSION['priv_edit'] == 1)
{
	$sql = "UPDATE dimli.order
				SET images_edited = '{$status}',
					images_edited_by = '{$_SESSION['username']}',
					images_edited_on = '{$todaysDate}',
					last_update_by = '{$_SESSION['username']}'
				WHERE id = '{$orderId}' ";

	$result = db_query($mysqli, $sql);

	if ($status == '1') { 
	// User has completed this task

		$sql = "INSERT INTO dimli.Activity 
					SET UserID = '{$_SESSION['user_id']}', 
						RecordType = 'Order', 
						RecordNumber = '{$orderId}', 
						ActivityType = 'image-edited',
						UnixTime = '{$UnixTime}' ";

		$log = db_query($mysqli, $sql);
	}
}

//-------------------------------
//  Images exported to storage
//-------------------------------

elseif ($processToUpdate == 'progress_exp'
	&& $_SESSION['priv_exportImages'] == 1)
{
	$sql = "UPDATE dimli.order
				SET images_exported = '{$status}',
					images_exported_by = '{$_SESSION['username']}',
					images_exported_on = '{$todaysDate}',
					last_update_by = '{$_SESSION['username']}'
				WHERE id = '{$orderId}' ";

	$result = db_query($mysqli, $sql);

	if ($status == '1') { 
	// User has completed this task

		$sql = "INSERT INTO dimli.Activity 
					SET UserID = '{$_SESSION['user_id']}', 
						RecordType = 'Order', 
						RecordNumber = '{$orderId}', 
						ActivityType = 'exported',
						UnixTime = '{$UnixTime}' ";

		$log = db_query($mysqli, $sql);
	}
}

//--------------------
//  Order delivered
//--------------------

elseif ($processToUpdate == 'progress_del'
	&& $_SESSION['priv_deliver'] == 1)
{
	$sql = "UPDATE dimli.order
				SET images_delivered = '{$status}',
					images_delivered_by = '{$_SESSION['username']}',
					images_delivered_on = '{$todaysDate}',
					last_update_by = '{$_SESSION['username']}'
				WHERE id = '{$orderId}' ";

	$result = db_query($mysqli, $sql);

	if ($status == '1') { 
	// User has completed this task

		$sql = "INSERT INTO dimli.Activity 
					SET UserID = '{$_SESSION['user_id']}', 
						RecordType = 'Order', 
						RecordNumber = '{$orderId}', 
						ActivityType = 'delivered',
						UnixTime = '{$UnixTime}' ";

		$log = db_query($mysqli, $sql);
	}
}

//-------------------------			
//  Cataloguing complete
//-------------------------

elseif ($processToUpdate == 'progress_cat'
	&& $_SESSION['priv_catalog'] == 1)
{
	$sql = "UPDATE dimli.order
				SET images_catalogued = '{$status}',
					images_catalogued_by = '{$_SESSION['username']}',
					images_catalogued_on = '{$todaysDate}',
					last_update_by = '{$_SESSION['username']}'
				WHERE id = '{$orderId}' ";

	$result = db_query($mysqli, $sql);

	if ($status == '1') { 
	// User has completed this task

		$sql = "INSERT INTO dimli.Activity 
					SET UserID = '{$_SESSION['user_id']}', 
						RecordType = 'Order', 
						RecordNumber = '{$orderId}', 
						ActivityType = 'cataloged',
						UnixTime = '{$UnixTime}' ";

		$log = db_query($mysqli, $sql);
	}
}

//--------------------------
//  Cataloguing approved	
//--------------------------

elseif ($processToUpdate == 'progress_app'
	&& $_SESSION['priv_approve'] == 1)
{
	$sql = "UPDATE dimli.order
				SET cataloguing_approved = '{$status}',
					cataloguing_approved_by = '{$_SESSION['username']}',
					cataloguing_approved_on = '{$todaysDate}',
					last_update_by = '{$_SESSION['username']}'
				WHERE id = '{$orderId}' ";

	$result = db_query($mysqli, $sql);

	if ($status == '1') { 
	// User has completed this task

		$sql = "INSERT INTO dimli.Activity 
					SET UserID = '{$_SESSION['user_id']}', 
						RecordType = 'Order', 
						RecordNumber = '{$orderId}', 
						ActivityType = 'approved',
						UnixTime = '{$UnixTime}' ";

		$log = db_query($mysqli, $sql);
	}
}

//-----------------------------------------------
//  Fetch the agents of recent status updates
//-----------------------------------------------

$sql = "SELECT * 
			FROM dimli.order 
			WHERE id = '{$orderId}' ";

$result5 = db_query($mysqli, $sql);

while ($row = $result5->fetch_assoc()) {

	$orderComplete = 	$row['complete'];
	$lastUpdate = 		$row['last_update'];
	$lastUpdateBy = 	$row['last_update_by'];

	$orderDigitized = $row['order_digitized'];
		$orderDigitizedBy = $row['order_digitized_by'];
		$orderDigitizedOn = $row['order_digitized_on'];

	$imagesEdited = $row['images_edited'];
		$imagesEditedBy = $row['images_edited_by'];
		$imagesEditedOn = $row['images_edited_on'];

	$imagesExported = $row['images_exported'];
		$imagesExportedBy = $row['images_exported_by'];
		$imagesExportedOn = $row['images_exported_on'];

	$imagesDelivered = $row['images_delivered'];
		$imagesDeliveredBy = $row['images_delivered_by'];
		$imagesDeliveredOn = $row['images_delivered_on'];

	$imagesCatalogued = $row['images_catalogued'];
		$imagesCataloguedBy = $row['images_catalogued_by'];
		$imagesCataloguedOn = $row['images_catalogued_on'];

	$cataloguingApproved = $row['cataloguing_approved'];
		$cataloguingApprovedBy = $row['cataloguing_approved_by'];
		$cataloguingApprovedOn = $row['cataloguing_approved_on'];

}

//------------------------------------------------------
//  Determine if every aspect of the order is complete
//------------------------------------------------------

$orderComplete = (
	$imagesEdited == 1 &&
	$imagesExported == 1 && 
	$imagesDelivered == 1 && 
	$imagesCatalogued == 1 && 
	$cataloguingApproved == 1 )
	? '1'
	: '0';
	
//-------------------------------------------------------
//  Update the "complete" status of the modified order
//-------------------------------------------------------

$sql = "UPDATE dimli.order 
			SET complete = '{$orderComplete}' 
			WHERE id = '{$orderId}' ";

$result_completeStatus = db_query($mysqli, $sql);

$newStatus_arr = array(
	// Format: processToUpdate => new completion status
	'progress_dig'=>'orderDigitized',
	'progress_edi'=>'imagesEdited',
	'progress_exp'=>'imagesExported',
	'progress_del'=>'imagesDelivered',
	'progress_cat'=>'imagesCatalogued',
	'progress_app'=>'cataloguingApproved'
	);

$newStatus = $$newStatus_arr[$processToUpdate];

//--------------------------------------
//  Define labels for progress buttons
//--------------------------------------

$button_labels = array(
	// Format: processToUpdate => button text
	'progress_dig'=>'digitized',
	'progress_edi'=>'edited',
	'progress_exp'=>'exported',
	'progress_del'=>'delivered',
	'progress_cat'=>'cataloged',
	'progress_app'=>'approved'
	); ?>

<div id="<?php echo $processToUpdate; ?>"
	class="progress_button pointer active <?php echo ($newStatus == '1')?'complete':'';?>"
	><?php 

	echo $button_labels[$processToUpdate];

?></div>

<script id="updateOrderStatus_temp">

	$('div.progress_button.active')
		.unbind('click.progressToggle');

	$('div.progress_button.active')
		.bind('click.progressToggle',
		function(event)
		{
			order_updateProgress(event, order_num);
		});

</script>