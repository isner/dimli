<?php
$urlpatch = (strpos($_SERVER['DOCUMENT_ROOT'], 'xampp') == true)?'/dimli':'';
if(!defined('MAIN_DIR')){define('MAIN_DIR',$_SERVER['DOCUMENT_ROOT'].$urlpatch);}
require_once(MAIN_DIR.'/_php/_config/session.php');
require_once(MAIN_DIR.'/_php/_config/connection.php');
require_once(MAIN_DIR.'/_php/_config/functions.php');
confirm_logged_in();
require_priv('priv_images_delete');

//----------------------------------------
//
//		DELETE AN INDIVIDUAL IMAGE
//
//----------------------------------------

if (isset($_GET['deadImage']))
// User clicked & confirmed that they wish to delete an image
{

	$associatedOrderId = $_SESSION['order'];
	$deadImageId = $_GET['deadImage'];

	
	// Fetch the number of images on this order.
	// - The order should have at least two remaining images,
	// - or the user should be prompted to delete the entire
	// - order instead of a single image.

	$sql = "SELECT id 
				FROM dimli.image 
				WHERE order_id = '{$associatedOrderId}' ";

	$result = db_query($mysqli, $sql);

	$numImages = $result->num_rows;

	
	if ($numImages >= 2)
	// Order has at least 2 images remaining,
	// so user CAN delete an individual image.
	{
		// Create six-digit image id to compare against 'related_images' fields.
		$imageId_six = create_six_digits($deadImageId);

		// Delete the image from the IMAGE table
		$sql = "DELETE FROM dimli.image 
					WHERE id = '{$deadImageId}' ";

		$result = db_query($mysqli, $sql);
		
		// Determine the order's new image count, after the deletion
		$sql = "SELECT * FROM dimli.image 
					WHERE order_id = '{$associatedOrderId}' ";

		$result = db_query($mysqli, $sql);

		$newImageCount = $result->num_rows;

		// Update the order's image count
		$sql = "UPDATE dimli.order 
					SET image_count = '{$newImageCount}' 
					WHERE id = '{$associatedOrderId}' ";

		$result = db_query($mysqli, $sql);
		
		//----------------------------------------------
		//  Remove the deleted image's cataloging info
		//----------------------------------------------

		$tables = array('agent','culture','date','edition','inscription','location','material','measurements','rights','source','style_period','subject','technique','title','work_type');

		foreach ($tables as $table) {

			$sql = "DELETE FROM dimli.{$table} 
						WHERE related_images = {$imageId_six} ";
						
			$res = db_query($mysqli, $sql);
		}

		//--------------
		//  Log action
		//--------------
		
		$UnixTime = time(TRUE);

		$sql = "INSERT INTO dimli.activity
					SET UserID = '{$_SESSION['user_id']}',
						RecordType = 'Image',
						RecordNumber = {$deadImageId},
						ActivityType = 'deleted',
						UnixTime = '{$UnixTime}' ";

		$result = db_query($mysqli, $sql);
		
	}
	elseif ($numImages == 1) {
	// This order only has ONE image remaining,
	// so user must delete the entire order instead.
	}

} ?>