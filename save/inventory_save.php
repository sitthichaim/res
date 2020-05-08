<?php 

$paht = "../";

$HIDE_HEADER = "Y";

include $paht."include/include.php";



// print_r($_POST);

$WFR = $_POST['WFR'];

$proc = $_POST['proc'];



switch ($proc) {

    case 'add':

		$insert = array(); 

	

		$insert['INVENTORY_DATE'] = $_POST['INVENTORY_DATE'];

		$insert['SUPPLIER_NAME'] = $_POST['SUPPLIER_NAME'];

		$insert['INVENTORY_NAME'] = $_POST['INVENTORY_NAME'];

		$insert['CATEGORY_ID'] = $_POST['CATEGORY_ID'];

		$insert['UNIT_PRICE'] = $_POST['UNIT_PRICE'];

		$insert['UNIT_TYPE'] = $_POST['UNIT_TYPE'];

		$insert['STOCK_QUANTITY'] = $_POST['STOCK_QUANTITY'];

		$insert['STOCK_VALUE'] = $_POST['STOCK_VALUE'];

		

		// print_r($_POST);

		// exit;



		db::db_insert('M_INVENTORY', $insert);

        break;

    case 'edit':

		$insert = array(); 

	

		$insert['INVENTORY_DATE'] = $_POST['INVENTORY_DATE'];

		$insert['SUPPLIER_NAME'] = $_POST['SUPPLIER_NAME'];

		$insert['INVENTORY_NAME'] = $_POST['INVENTORY_NAME'];

		$insert['CATEGORY_ID'] = $_POST['CATEGORY_ID'];

		$insert['UNIT_PRICE'] = $_POST['UNIT_PRICE'];

		$insert['UNIT_TYPE'] = $_POST['UNIT_TYPE'];

		$insert['STOCK_QUANTITY'] = $_POST['STOCK_QUANTITY'];

		$insert['STOCK_VALUE'] = $_POST['STOCK_VALUE'];

		

		$cond['INVENTORY_ID'] = $WFR;

		db::db_update('M_INVENTORY', $insert, $cond);

        break;

    case 'del':

		$cond['INVENTORY_ID'] = $WFR;

		db::db_delete('M_INVENTORY', $cond);

        break;

}

	

echo '<script>';

echo 'window.location.href = "../form/inventory_disp.php";';

echo '</script>';

?> 