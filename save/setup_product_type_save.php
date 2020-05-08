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
	
		$insert['PRODUCT_TYPE_NAME'] = $_POST['PRODUCT_TYPE_NAME'];
		$insert['ACTIVE_STATUS'] = $_POST['ACTIVE_STATUS'];
		$insert['DELETE_FLAG'] = 0;

		db::db_insert('M_SETUP_PRODUCT_TYPE', $insert);
        break;
    case 'edit':
		$insert = array(); 
	
		$insert['PRODUCT_TYPE_NAME'] = $_POST['PRODUCT_TYPE_NAME'];
		$insert['ACTIVE_STATUS'] = $_POST['ACTIVE_STATUS'];
		
		$cond['PRODUCT_TYPE_ID'] = $WFR;
		db::db_update('M_SETUP_PRODUCT_TYPE', $insert, $cond);
        break;
    case 'del':
		$insert['DELETE_FLAG'] = 1;
		$cond['PRODUCT_TYPE_ID'] = $WFR;
		db::db_update('M_SETUP_PRODUCT_TYPE', $insert, $cond);
		// $cond['PRODUCT_TYPE_ID'] = $WFR;
		// db::db_delete('M_SETUP_PRODUCT_TYPE', $cond);
        break;
}

echo '<script>';
echo 'window.location.href = "../form/setup_product_type_disp.php";';
echo '</script>';
?> 