<?php 
$paht = "../";
$HIDE_HEADER = "Y";
include $paht."include/include.php";

// print_r($_POST);
// exit; 
$WFR = $_POST['WFR'];
$proc = $_POST['proc'];

switch ($proc) {
    case 'add':
		$insert = array(); 
	
		$insert['CATEGORY_NAME'] = $_POST['CATEGORY_NAME'];
		$insert['ACTIVE_STATUS'] = $_POST['ACTIVE_STATUS'];
		$insert['DELETE_FLAG'] = 0;

		db::db_insert('M_CATEGORY', $insert);
        break;
    case 'edit':
		$insert = array(); 
	
		$insert['CATEGORY_NAME'] = $_POST['CATEGORY_NAME'];
		$insert['ACTIVE_STATUS'] = $_POST['ACTIVE_STATUS'];
		
		$cond['CATEGORY_ID'] = $WFR;
		db::db_update('M_CATEGORY', $insert, $cond);
        break;
    case 'del':
		$insert['DELETE_FLAG'] = 1;
		$cond['CATEGORY_ID'] = $WFR;
		db::db_update('M_CATEGORY', $insert, $cond);
		// $cond['PRODUCT_TYPE_ID'] = $WFR;
		// db::db_delete('M_SETUP_PRODUCT_TYPE', $cond);
        break;
}

echo '<script>';
echo 'window.location.href = "../form/setup_category_disp.php";';
echo '</script>';
?> 