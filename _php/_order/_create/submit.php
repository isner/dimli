<?php
$urlpatch = (strpos($_SERVER['DOCUMENT_ROOT'], 'xampp') == true)?'/dimli':'';
if(!defined('MAIN_DIR')){define('MAIN_DIR',$_SERVER['DOCUMENT_ROOT'].$urlpatch);}
require_once(MAIN_DIR.'/_php/_config/session.php');
require_once(MAIN_DIR.'/_php/_config/connection.php');
require_once(MAIN_DIR.'/_php/_config/functions.php');
confirm_logged_in();
require_priv('priv_orders_create');


//--------------------------------
//  PREPARE USER-SUBMITTED DATA
//--------------------------------
$source_name_type = $mysqli->real_escape_string($_POST['sourceNameType']);
$source_name_text = $mysqli->real_escape_string($_POST['sourceName']);
$source_type = $mysqli->real_escape_string($_POST['sourceType']);
$source_text = $mysqli->real_escape_string($_POST['source']);
$requestor = $mysqli->real_escape_string($_POST['client']);
$department = $mysqli->real_escape_string($_POST['department']);
$email = $mysqli->real_escape_string($_POST['email']);
$date_needed = $mysqli->real_escape_string($_POST['dateNeeded']);
$legacyIds = $_POST['legacyIds'];


	// $_POST contained no value for 'imageCount'
if (empty($_POST['imageCount'])) {

		// Determine the image count by counting the number of keys in
		// `$_POST` that begin with 'image'
	$count = 0;
	foreach ($_POST as $key => $val) {
		if ((strpos($key, 'image') > -1) && $key !== 'imageCount') {
			$count++;
		}
	}
	$imageCount = $count;

	// Image count was supplied by user
} else {
	$imageCount = $_POST['imageCount'];
}


//-------------------------------------
//  INSERT NEW IMAGES AND NEW SOURCE
//-------------------------------------

$i = 1;

	// For each image
while ($i <= $imageCount) {

	$page = trim($mysqli->real_escape_string($_POST['image'.$i]['page']));
	$fig = trim($mysqli->real_escape_string($_POST['image'.$i]['fig']));

		// User elected to supply legacy ids and filenames
	if ($legacyIds === true) {

		$sql = "INSERT INTO dimli.image
					SET legacy_id = '{$page}',
						legacy_filename = '{$fig}',
						file_format = '.jpg',
						last_update_by = '{$_SESSION['username']}' ";

	} else {

		$sql = "INSERT INTO dimli.image
					SET page = '{$page}',
						fig = '{$fig}',
						file_format = '.jpg',
						last_update_by = '{$_SESSION['username']}' ";

	}

	$result = db_query($mysqli, $sql);
	
	// Create a variable for the recently created image's ID/PK
	$thisImage = create_six_digits($mysqli->insert_id);

	
	$sql = "INSERT INTO dimli.source
					SET related_images = '{$thisImage}',
						source_name_type = '{$source_name_type}',
						source_name_text = '{$source_name_text}',
						source_type = '{$source_type}',
						source_text = '{$source_text}' ";

	$result = db_query($mysqli, $sql);

	$i++;

	$thisImage = create_six_digits($thisImage++);
}

//---------------------
//  CREATE NEW ORDER
//---------------------

$today = date('Y-m-d H:i:s');

$sql = "INSERT INTO dimli.order
			SET requestor = '{$requestor}',
				department = '{$department}',
				email = '{$email}',
				date_created = '{$today}',
				created_by = '{$_SESSION['username']}',
				date_needed = '{$date_needed}', ";

// If the user creating the order does NOT have the privilege
// to confirm the creation of new orders, set the status of
// the new order to "pending" to await approval by the VRC
$sql.= ($_SESSION['priv_orders_confirmCreation'] != '1')
	?" creation_pending = '1', "
	:"";

$sql.= " image_count = '{$imageCount}',
			last_update_by = '{$_SESSION['username']}' ";

$result = db_query($mysqli, $sql);

$newOrderId = $mysqli->insert_id;

$sql = "UPDATE dimli.image 
			SET order_id = '{$newOrderId}' 
			WHERE order_id = '0' ";

$result = db_query($mysqli, $sql);


//---------------
//  LOG ACTION
//---------------

$UnixTime = time(TRUE);

$sql = "INSERT INTO dimli.Activity
			SET UserID = '{$_SESSION['user_id']}',
				RecordType = 'Order',
				RecordNumber = {$newOrderId},
				ActivityType = 'created',
				UnixTime = '{$UnixTime}' ";

$result = db_query($mysqli, $sql);


// Clear array for new order details
$_SESSION['newOrderDetails'] = array('client'=>'','department'=>'','email'=>'','dateNeeded'=>'','imageCount'=>'','sourceNameType'=>'','sourceName'=>'','sourceType'=>'','source'=>'');

// Pad the id of the new order for use in javascript below
$newOrderId = create_four_digits($newOrderId);
?>

<script>

	var newOrder = <?php echo $newOrderId; ?>;

	$(document).ready(
		function()
		{
			open_order(newOrder);
		});

</script>