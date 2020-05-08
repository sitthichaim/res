<?php 
 
$paht = "../";

$HIDE_HEADER = "Y";

include $paht."include/include.php";

// echo "<pre>";

// print_r($_POST);

// echo "</pre>";

// exit;

$WFR = $_POST['WFR'];

$proc = $_POST['proc'];

$PRODUCT_TYPE = $_POST['PRODUCT_TYPE'];
$STOCK_OLD = $_POST['STOCK_OLD'];
$ORDER_OLD = $_POST['ORDER_OLD'];
$ORDER_NEW = $_POST['ORDER_NEW'];
$ORDER_SUM = $_POST['ORDER_SUM'];



$TBL_MAIN = "M_MANAGE_PRODUCTION_MAIN";

$TBL_DETAIL = "M_MANAGE_PRODUCTION_DETAIL";

switch ($proc) {

    case 'add':

		$insert = array(); 

		$insert['DATE'] = $_POST['DATE'];
		$insert['BILL_TYPE'] = $_POST['BILL_TYPE'];
		$insert['BILL_NAME'] = $_POST['BILL_NAME'];
		$insert['DELETE_FLAG'] = 0;

		$MAIN_ID = db::db_insert($TBL_MAIN, $insert,'MAIN_ID');

		foreach($PRODUCT_TYPE as $key=>$val){

			$insert = array(); 

			$insert['MAIN_ID'] = $MAIN_ID;
			$insert['PRODUCT_TYPE'] = $val;
			$insert['STOCK_OLD'] = $STOCK_OLD[$key];
			$insert['ORDER_OLD'] = $ORDER_OLD[$key];
			$insert['ORDER_NEW'] = $ORDER_NEW[$key];
			$insert['ORDER_SUM'] = $ORDER_SUM[$key];

		db::db_insert($TBL_DETAIL, $insert);

		} 

        break;

    case 'edit':

		$insert = array(); 

		$insert['DATE'] = $_POST['DATE'];
		$insert['BILL_TYPE'] = $_POST['BILL_TYPE'];
		$insert['BILL_NAME'] = $_POST['BILL_NAME'];

		$cond['MAIN_ID'] = $WFR;

		db::db_update($TBL_MAIN, $insert, $cond);
		
		db::db_delete($TBL_DETAIL, $cond);

		foreach($PRODUCT_TYPE as $key=>$val){

			$insert = array(); 

			$insert['MAIN_ID'] = $WFR;
			$insert['PRODUCT_TYPE'] = $val;
			$insert['STOCK_OLD'] = $STOCK_OLD[$key];
			$insert['ORDER_OLD'] = $ORDER_OLD[$key];
			$insert['ORDER_NEW'] = $ORDER_NEW[$key];
			$insert['ORDER_SUM'] = $ORDER_SUM[$key];

		db::db_insert($TBL_DETAIL, $insert);
		} 
        break;

    case 'del':

		$insert['DELETE_FLAG'] = 1;
		$cond['MAIN_ID'] = $WFR;

		db::db_update($TBL_MAIN, $insert, $cond);

        break;

}

	

echo '<script>';

echo 'window.location.href = "../form/manage_production_disp.php";';

echo '</script>';

?> 