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
	
		$insert['DATE'] = $_POST['DATE'];
		$insert['TIME'] = $_POST['TIME'];
		$insert['SALE'] = $_POST['SALE'];
		$insert['ADVERTISE'] = $_POST['ADVERTISE'];

		db::db_insert('M_SALE_ADVERTISING', $insert);
        break;
    case 'edit':
		$insert = array(); 
	
		$insert['DATE'] = $_POST['DATE'];
		$insert['TIME'] = $_POST['TIME'];
		$insert['SALE'] = $_POST['SALE'];
		$insert['ADVERTISE'] = $_POST['ADVERTISE'];
		
		$cond['SA_ID'] = $WFR;
		db::db_update('M_SALE_ADVERTISING', $insert, $cond);
        break;
    case 'del':
		$cond['SA_ID'] = $WFR;
		db::db_delete('M_SALE_ADVERTISING', $cond);
        break;
}
	
echo '<script>';
echo 'window.location.href = "../form/SalesAndAdvertising_Disp.php";';
echo '</script>';
?> 