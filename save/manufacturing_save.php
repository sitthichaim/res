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
		$insert['TYPE'] = $_POST['TYPE'];
		$insert['AROUND'] = $_POST['AROUND'];
		$insert['KINDS'] = $_POST['KINDS'];
		$insert['QUANTITY'] = $_POST['QUANTITY'];
		$insert['STIME'] = $_POST['STIME'];
		$insert['ETIME'] = $_POST['ETIME'];
		$insert['WASTE'] = $_POST['WASTE'];		$insert['DATE'] = $_POST['DATE'];		$insert['PRODUCE'] = $_POST['PRODUCE'];
		db::db_insert('M_MANUFACTURING', $insert);
        break;
    case 'edit':
		$insert = array(); 	
		$insert['TYPE'] = $_POST['TYPE'];		$insert['AROUND'] = $_POST['AROUND'];		$insert['KINDS'] = $_POST['KINDS'];		$insert['QUANTITY'] = $_POST['QUANTITY'];		$insert['STIME'] = $_POST['STIME'];		$insert['ETIME'] = $_POST['ETIME'];		$insert['WASTE'] = $_POST['WASTE'];		$insert['DATE'] = $_POST['DATE'];		$insert['PRODUCE'] = $_POST['PRODUCE'];
		$cond['FACTURING_ID'] = $WFR;
		db::db_update('M_MANUFACTURING', $insert, $cond);
        break;
    case 'del':
		$cond['FACTURING_ID'] = $WFR;
		db::db_delete('M_MANUFACTURING', $cond);
        break;
}
	
echo '<script>';
echo 'window.location.href = "../form/manufacturing.php?T="+'.$_POST['TYPE'].';';
echo '</script>';
?> 