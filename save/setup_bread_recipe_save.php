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

$INVENTORY_ID = $_POST['INVENTORY_ID'];
$INVENTORY_UNIT = $_POST['INVENTORY_UNIT'];
$INVENTORY_QUANTITY = $_POST['INVENTORY_QUANTITY'];



$TBL_MAIN = "M_SETUP_BREAD_RECIPE_MAIN";
$TBL_DETAIL = "M_SETUP_BREAD_RECIPE_DETAIL";

switch ($proc) {

    case 'add':

		$insert = array(); 
		$insert['PRODUCT_TYPE_ID'] = $_POST['PRODUCT_TYPE_ID'];
		$insert['BREAD_COUNT'] = $_POST['BREAD_COUNT'];
		$insert['ACTIVE_STATUS'] = $_POST['ACTIVE_STATUS'];
		$insert['DELETE_FLAG'] = 0;

		$MAIN_ID = db::db_insert($TBL_MAIN, $insert,'BREAD_RECIPE_ID');

		foreach($INVENTORY_ID as $key=>$val){

			$insert = array(); 

			$insert['BREAD_RECIPE_ID'] = $MAIN_ID;
			$insert['INVENTORY_ID'] = $val;
			$insert['INVENTORY_QUANTITY'] = $INVENTORY_QUANTITY[$key];
			$insert['INVENTORY_UNIT'] = $INVENTORY_UNIT[$key];
			
		db::db_insert($TBL_DETAIL, $insert);

		} 

        break;

    case 'edit':

		$insert = array(); 

		$insert['PRODUCT_TYPE_ID'] = $_POST['PRODUCT_TYPE_ID'];
		$insert['BREAD_COUNT'] = $_POST['BREAD_COUNT'];

		$cond['BREAD_RECIPE_ID'] = $WFR;

		db::db_update($TBL_MAIN, $insert, $cond);
		
		db::db_delete($TBL_DETAIL, $cond);
		
		foreach($INVENTORY_ID as $key=>$val){

			$insert = array(); 

			$insert['BREAD_RECIPE_ID'] = $WFR;
			$insert['INVENTORY_ID'] = $val;
			$insert['INVENTORY_QUANTITY'] = $INVENTORY_QUANTITY[$key];
			$insert['INVENTORY_UNIT'] = $INVENTORY_UNIT[$key];
			
		db::db_insert($TBL_DETAIL, $insert);
		}
        break;
 
    case 'del':
		$insert['DELETE_FLAG'] = 1;
		$cond['BREAD_RECIPE_ID'] = $WFR;

		db::db_update($TBL_MAIN, $insert, $cond);

        break;

}

	

echo '<script>';

echo 'window.location.href = "../form/setup_bread_recipe_disp.php";';

echo '</script>';

?> 