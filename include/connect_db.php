<?php
/*
 * Class Connect Database
 * Created by:  Tawatchai Anuchat
 * Date:  05/05/2017
 * Version:  1.1
 */
// $_password = "0IeRcUF0ex" = "0IeRcUF0ex";

Class db
{
	protected static $_host, $_user, $_password = "0IeRcUF0ex", $_autoIncrement = "Y";
	protected static $_systemConnect, $_systemQuery,$_querySQL, $_systemRecordCount, $_systemResult;
	public static $_dbType = "MYSQL", $_dbName ,$_langDate, $_systemRunType = "LIVE",$_dbOwner;

	/*
	 * ตั้งค่าการเชื่อมต่อฐานข้อมูล
	 * @host		IP หรือชื่อเครื่องฐานข้อมูล
	 * @user		username ที่ใช้เข้าฐานข้อมูล
	 * @password	password ที่ใช้เข้าฐานข้อมูล
	 * @dbName		ชื่อฐานข้อมูล
	 * @dbType		ประเภทฐานข้อมูล (MYSQL, MSSQL, ORACLE)
	 */

	public static function setupDatabase()
	{
		self::connectServer();
	}

	/*
	 * Connect Database
	 */
	protected static function connectServer()
	{
		switch(self::$_dbType)
		{
			case 'MSSQL':
				try {
					self::$_systemConnect= new PDO("sqlsrv:server=".self::$_host."; Database = ".self::$_dbName, self::$_user, self::$_password = "0IeRcUF0ex");
					self::$_systemConnect->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					
			}catch(PDOException $e) {
					echo $e->getMessage();
				}
				break;
			case 'MYSQL':
				self::$_systemConnect = mysqli_connect(self::$_host, self::$_user, self::$_password = "0IeRcUF0ex", self::$_dbName);
				self::query('SET NAMES \'utf8\'');

				if(mysqli_connect_errno())
				{
					echo "<strong>ไม่สามารถเชื่อมต่อฐานข้อมูลได้: </strong>".mysqli_connect_error();
					exit;
				}
				break;
			case 'ORACLE':
				$db1 = "(DESCRIPTION=(ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST = ".self::$_host.")(PORT = 1521)))(CONNECT_DATA = (SERVICE_NAME=orcl)))";
				self::$_systemConnect = oci_connect(self::$_user, self::$_password = "0IeRcUF0ex", $db1,"UTF8");
				if(!self::$_systemConnect)
				{
					$e = oci_error();
					trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
				}
				self::query('ALTER SESSION SET NLS_DATE_FORMAT = \'YYYY-MM-DD\'');
				self::query('alter session set nls_sort=binary_ci');
				self::query('ALTER SESSION SET NLS_COMP=LINGUISTIC');
				break;
		}

		return self::$_systemConnect;
	}

	/*
	 * เลือกฐานข้อมูลที่เชื่อมต่อ
	 */
	protected static function chooseDBName()
	{
		switch(self::$_dbType)
		{
			case 'MSSQL':
				//mssql_select_db(self::$_dbName);
				break;
			case 'MYSQL':
				mysqli_select_db(self::$_systemConnect, self::$_dbName);
				break;
			case 'ORACLE':
				break;
		}
	}

	/*
	 * Query ข้อมูลโดยรับคำสั่ง SQL เข้ามา
	 */
	public static function query($sql)
	{
		global $show_query;

		if($_GET["show__query"] == "Y")
		{
			echo date("H:i:s")."<br>";
			echo $sql."<hr>";
		}
		if (preg_match("/insert/i",$sql) OR preg_match("/update/i",$sql) OR preg_match("/delete/i",$sql)) { 
			$txtdate = date("H:i:s"); 
			/*if(getenv(HTTP_X_FORWARDED_FOR))  
			{ 
				$IPn = getenv(HTTP_X_FORWARDED_FOR); 
			}      
		else  
			{ */
				$IPn = $_SERVER["REMOTE_ADDR"];
			//} 
			$textwrite = "[".$txtdate." ".$IPn."] ".$_SESSION["WF_USERNAME"]." (".$_SESSION["WF_USER_ID"].") :".preg_replace( '/\s+/', ' ', $sql )."\r\n"; 
			$year = date("Y"); 
			$y = $year+543; 
			$datefile = $y.date("m").date("d").".txt"; 
			if(!file_exists("../log_process")){ mkdir("../log_process",0777); }
			$fp = fopen("../log_process/".$datefile, 'a+'); 
			fwrite($fp, $textwrite); 
			fclose($fp); 
			 
		} 
		$error = "N";
		$error_txt = "";
		switch(self::$_dbType)
		{
			case 'MSSQL':
				try {
					self::$_systemQuery=self::$_systemConnect->prepare($sql);
					self::$_systemQuery->execute();
					self::$_querySQL = $sql;
				}catch(PDOException $e) {
					$error_txt = $e->getMessage();
					self::write_log_error($sql, $error_txt);
					echo 'Error: '.$sql.'<hr />'.$e->getMessage();
					exit;
				}
				break;
			case 'MYSQL':
				self::$_systemQuery = mysqli_query(self::$_systemConnect, $sql);
				if(!self::$_systemQuery)
				{
					$error_txt = mysqli_error(self::$_systemConnect);
					self::write_log_error($sql, $error_txt); 
					echo "<strong>Error Description: </strong>".$error_txt;
					exit;
				}
				break;
			case 'ORACLE':
				$obj = oci_parse(self::$_systemConnect, $sql);
				$obj2 = @oci_execute($obj); 
				self::$_systemQuery = $obj;
				self::$_querySQL = $sql;
				if(!$obj2)
				{
					$error_txt = OCIError($obj);
					self::write_log_error($sql, $error_txt['message']);
					if(self::$_systemRunType=="DEV"){
					echo "<hr /><strong>".$sql."</strong><hr /><strong>Error Description: </strong>".$error_txt['message'];
					exit;
					}
				} 
				break;
		}

		return self::$_systemQuery;
	}

	/*
	 * Query ข้อมูลโดยรับคำสั่ง SQL เข้ามาพร้อมจำกัดการแสดงจำนวนแถวข้อมูล
	 * @sql		statement
	 * @offset	เริ่มต้นจาก
	 * @limit	จำนวนที่ต้องการแสดง
	 */
	public static function query_limit($sql, $offset, $limit)
	{
		global $show_query;

		if($show_query == "Y")
		{
			echo date("H:i:s")."<br>";
			echo $sql."<hr>";
		}

		$error = "N";
		$error_txt = "";
		switch(self::$_dbType)
		{
			case 'MSSQL':
				/*
				$STOP =  $offset + $limit;
				if($offset != -1)
				{
					$offset = $offset+1;
				}
				$sql_check = ($sql);
				$sql_no_order = explode(" ORDER BY ",$sql_check);
				if($sql_no_order[1] != ''){
					$sql_no_from = explode(" FROM ",$sql_check);
					$sql_no_order2 = explode(" ORDER BY ",$sql_no_from[1]);
					$sql = "
					SELECT C.*
					FROM (
					".$sql_no_from[0]." ,ROW_NUMBER() OVER (ORDER BY ".$sql_no_order[1].") AS RowNum
					FROM ".$sql_no_order2[0]."
					) AS C
					WHERE C.RowNum BETWEEN ".$offset." AND ".$STOP; 
				}*/
				$sql = $sql." OFFSET ".$offset." ROWS FETCH NEXT ".$limit." ROWS ONLY ";
				try {
					self::$_systemQuery=self::$_systemConnect->prepare($sql);
					self::$_systemQuery->execute(); 
					self::$_querySQL = $sql;
				}catch(PDOException $e) {
					echo $e->getMessage();
				}
				break;
			case 'MYSQL':
				$sql_limit = " limit ".$offset.", ".$limit;

				self::$_systemQuery = mysqli_query(self::$_systemConnect, $sql.$sql_limit);
				if(!self::$_systemQuery)
				{
					$error = "Y";
					$error_txt = mysqli_error(self::$_systemConnect);
					echo "<strong>Error Description: </strong>".$error_txt;
				}
				break;
			case 'ORACLE':
				$STOP =  $offset + $limit;
				if($offset != -1)
				{
					$offset = $offset+1;
				}
				$sql_limit = 'select * from ( select a.*, rownum rnum from ( '.$sql.' ) a ) where rnum between '.$offset.' and '.$STOP.' ';


				$obj = oci_parse(self::$_systemConnect, $sql_limit);
				oci_execute($obj);
				self::$_systemQuery = $obj;
				self::$_querySQL = $sql;
				if(!self::$_systemQuery)
				{
					$error = "Y";
					$error_txt = OCIError();
					echo "<strong>Error Description: </strong>".$error_txt;
				}
				break;
		}

		if($error == "Y")
		{
			self::write_log_error($sql, $error_txt);
		}

		return self::$_systemQuery;

	}

	/*
	 * Fetch Array
	 */
	public static function fetch_array($query)
	{
		switch(self::$_dbType)
		{
			case 'MSSQL':
				self::$_systemResult = $query->fetch(PDO::FETCH_ASSOC);
				break;
			case 'MYSQL':
				self::$_systemResult = mysqli_fetch_array($query);
				break;
			case 'ORACLE':
				self::$_systemResult = @oci_fetch_array($query,OCI_RETURN_NULLS+OCI_RETURN_LOBS);
				break;
		}

		return self::$_systemResult;
	}

	/*
	 * Num Rows
	 */
	public static function num_rows($query)
	{
		switch(self::$_dbType)
		{
			case 'MSSQL':
				$sql_check = (self::$_querySQL);
				$sql_no_order = explode("ORDER BY",$sql_check);
				$obj = self::$_systemConnect->prepare("SELECT COUNT(*) AS NUM FROM (".$sql_no_order[0].") a");
				$obj->execute();
				$record_count = $obj->fetch(PDO::FETCH_ASSOC);
				self::$_systemRecordCount = $record_count['NUM'];
				break;
			case 'MYSQL':
				self::$_systemRecordCount = mysqli_num_rows($query);
				break;
			case 'ORACLE':
				$obj = oci_parse(self::$_systemConnect, "SELECT COUNT(*) AS NUM FROM (".self::$_querySQL.")");
				oci_execute($obj);
				$record_count = oci_fetch_array($obj);
				self::$_systemRecordCount = $record_count['NUM'];
				//self::$_systemRecordCount = oci_num_rows($query);
				break;
		}

		return self::$_systemRecordCount;
	}

	/*
	 * Insert ข้อมูล
	 * @tbName		ชื่อตารางที่จะ Insert
	 * @data		ข้อมูลที่จะ Insert เป็น Array โดย Key คือชื่อ Field, Value คือ ข้อมูลที่จะเพิ่ม
	 * @pk			PK ของตารางที่ต้องการ select max
	 * @outID		ต้องการเลข PK ล่าสุดที่เพิ่ม  ถ้าต้องการใส่ Y
	 */
	public static function db_insert($tbName, $data, $pkSelectMax = "", $outID = "")
	{
		$fieldArray = array();
		$valueArray = array();

		if(self::$_autoIncrement == "N")
		{
			if($pkSelectMax != "")
			{
				if(trim($data[$pkSelectMax]) != '')
				{
					$last_id = $data[$pkSelectMax];
				}
				else
				{
					$get_last_id = self::get_max($tbName, $pkSelectMax);
					$last_id = $get_last_id + 1;
					$data[$pkSelectMax] = $last_id;
				}
			}
		}
		foreach($data as $_key => $_val)
		{
			if($_key != ""){
			array_push($fieldArray, $_key);
			array_push($valueArray, "'".$_val."'");
			}
		}

		$setSQL = "insert into ".$tbName." (".implode(', ', $fieldArray).") values (".implode(', ', $valueArray).")";
		
		self::query($setSQL);

		if($outID != "")
		{
			switch(self::$_dbType)
			{
				case 'MSSQL':
					$last_id = self::get_max($tbName, $outID);
					break;
				case 'MYSQL':
					//$last_id = mysqli_insert_id(self::$_systemConnect);
					$last_id = self::get_max($tbName, $outID);
					break;
				case 'ORACLE':
					$last_id = self::get_max($tbName, $outID);
					break;
			}
		}

		if(self::$_autoIncrement == "N" || $outID != "")
		{
			return $last_id;
		}
		else
		{
			return null;
		}
	}

	/*
	 * Update ข้อมูล
	 * @tbName		ชื่อตารางที่จะ Update
	 * @data		ข้อมูลที่จะ Update เป็น Array โดย Key คือชื่อ Field, Value คือ ข้อมูลที่จะเพิ่ม
	 * @cond		เงื่อนไข เป็น Array โดย Key คือชื่อ Field ที่จะ Where, Value คือ ข้อมูลที่จะ Where
	 */
	public static function db_update($tbName, $data, $cond)
	{
		if(count($data)>0){
		$updateData = self::setArray2String($data);
		$condition = self::setArray2String($cond, " and ");

		$setSQL = "update ".$tbName." set ".$updateData." where 1=1 and ".$condition;
		self::query($setSQL);
		}
	}

	/*
	 * Show Field ในตาราง
	 * @tables		ชื่อตารางที่ต้องการ Show Fields
	 */
	public static function show_field($tables)
	{
		$arr_data = array();
		if(strtoupper(self::$_dbType) == 'MYSQL')
		{
			if($tables != ''){
				$tables = strtolower($tables);
				$q_auto = self::query("SHOW FIELDS FROM ".$tables."");
				while($r_auto = self::fetch_array($q_auto))
				{
					array_push($arr_data, $r_auto['Field']);
				}
			}
		}
		elseif(strtoupper(self::$_dbType) == 'ORACLE')
		{

			$tables = strtoupper($tables);
			//$q_auto = self::query("SELECT column_name FROM USER_TAB_COLUMNS WHERE table_name = '".$tables."' ORDER BY COLUMN_ID");
			$q_auto = self::query("SELECT column_name FROM all_tab_cols WHERE VIRTUAL_COLUMN = 'NO' AND table_name = '".$tables."' AND OWNER = '".strtoupper(self::$_dbName)."'  ORDER BY SEGMENT_COLUMN_ID");
			while($r_auto = self::fetch_array($q_auto))
			{
				array_push($arr_data, $r_auto['COLUMN_NAME']);
			}
		}elseif(strtoupper(self::$_dbType) == 'MSSQL')
		{

			$tables = strtoupper($tables);
			$q_auto = self::query("select COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='".$tables."' ORDER BY ORDINAL_POSITION");
			while($r_auto = self::fetch_array($q_auto))
			{
				array_push($arr_data, $r_auto['COLUMN_NAME']);
			}
		}
		
		 
		$arr_data = array_unique($arr_data);
		return $arr_data;
	}

	/*
	 * Delete ข้อมูล
	 * @tbName		ชื่อตารางที่จะ Delete
	 * @cond		เงื่อนไข เป็น Array โดย Key คือชื่อ Field ที่จะ Where, Value คือ ข้อมูลที่จะ Where
	 */
	public static function db_delete($tbName, $cond)
	{
		$condition = self::setArray2String($cond, " and ");

		$setSQL = "delete from ".$tbName." where 1=1 and ".$condition;
		self::query($setSQL);
	}

	/*
	 * Query + Fetch ข้อมูล
	 * @return	ส่งค่ากลับเป็น Array 2 มิติ
	 */
	public static function store_select($sql)
	{
		$data_stored = array();

		switch(self::$_dbType)
		{
			case 'MSSQL':
				break;
			case 'MYSQL':
				$result = self::query($sql);

				while($record = mysqli_fetch_assoc($result))
				{
					$data_stored[] = $record;
				}
				break;
			case 'ORACLE':
				break;
		} 
		return $data_stored;
	}

	/*
	 * หาค่ามากสุด
	 * @table		ชื่อตารางที่ต้องการหา
	 * @fieldGetMax	ชื่อฟิลที่ต้องการหา
	 * @cond		เงื่อนไข เป็น Array โดย Key คือชื่อ Field ที่จะ Where, Value คือ ข้อมูลที่จะ Where
	 */
	public static function get_max($table, $fieldGetMax, $cond = array())
	{
		if(count($cond) > 0)
		{
			$condition = self::setArray2String($cond, " and ");
			$where = " where ".$condition;
		}
		else
		{
			$where = "";
		}
		
		$sql = "select max(".$fieldGetMax.") as MX from ".$table.$where;
		$res = self::query($sql);
		$rec = self::fetch_array($res);
		return $rec['MX'] > 0 ? $rec['MX'] : '0';
	}


	//query เพื่อหา field จากตาราง
	public static function query_field($sql)
	{
		switch(self::$_dbType)
		{
			case 'MSSQL': 
				$result = self::$_systemConnect->prepare($sql);
				$result->execute();
				$total_column = $result->columnCount(); 
				$arr_field = array();
				for ($counter = 0; $counter < $total_column; $counter ++) {
					$meta = $result->getColumnMeta($counter);
					$arr_field[] = $meta['name'];
				}
				break;
			case 'MYSQL':
				$res = self::query($sql);
				$ncols = self::fetch_array($res);
				$arr_field = array();
				foreach ($ncols as $key=>$val) {
					if(!is_numeric($key)){
					$arr_field[] = $key;
					}
				}
				break;
			case 'ORACLE':
				$res = self::query($sql);
				$ncols = oci_num_fields($res);
				$arr_field = array();
				for ($i = 1; $i <= $ncols; $i++) {
					$arr_field[] = oci_field_name($res, $i);
				}
				break;
		}
		
		
		return $arr_field;
	}
	
	/*
	 * เก็บ SQL Error
	 * @sql			คำสั่งที่ error
	 * @errorTxt	รายละเอียดที่ error
	 */
	protected static function write_log_error($sql, $errorTxt = "")
	{
		if($errorTxt != "")
		{
			$errorTxt = " (".$errorTxt.")";
		}

		$file_name = date('Ymd').".txt";
		$content = date('H:i:s')."[".$_SESSION['WF_USER_ID']."][".$_SESSION['WF_USER_NAME']."][".$_SERVER['REQUEST_URI']."] : ".$sql.$errorTxt."\n";
		$handle = fopen('../logs_error/'.$file_name, 'a');

		fwrite($handle, $content);
		fclose($handle);
	}

	/*
	 * ปิดการเชื่อมต่อฐานข้อมูล
	 */
	public static function db_close()
	{
		switch(self::$_dbType)
		{
			case 'MSSQL':
				self::$_systemConnect = null;
				break;
			case 'MYSQL':
				mysqli_close(self::$_systemConnect);
				break;
			case 'ORACLE':
				oci_close(self::$_systemConnect);
				break;
		}
	}

	private static function setArray2String($dataArray, $operator = ", ")
	{
		$data = array();
		$val = "";

		foreach($dataArray as $_key => $_val)
		{
			if($_key != ""){
				
				$val = "'".$_val."'";
				
				if(trim(str_replace("'",'',$val)) == ''){					
					$val = 'NULL';
				}
				
				
				$data[] = $_key." = ".$val;
			}
		} 

		return implode($operator, $data);
	}

	/*
	 * ตั้งค่าพาธฐานข้อมูล
	 */
	public static function setHost($txt)
	{
		self::$_host = $txt;
	}

	/*
	 * ตั้งค่า username ฐานข้อมูล
	 */
	public static function setUser($txt)
	{
		self::$_user = $txt;
		self::$_dbOwner = $txt;
	}

	/*
	 * ตั้งค่า password ฐานข้อมูล
	 */
	// public static function setPassword($txt)
	// {
		// $key = sha1('team');
		// $strLen = strlen($txt);
		// $keyLen = strlen($key);
		// for ($i = 0; $i < $strLen; $i+=2) {
			// $ordStr = hexdec(base_convert(strrev(substr($txt,$i,2)),36,16));
			// if ($j == $keyLen) { $j = 0; }
			// $ordKey = ord(substr($key,$j,1));
			// $j++;
			// $hash .= chr($ordStr - $ordKey);
		// }
		// self::$_password = "0IeRcUF0ex" = $hash;
	// }

	/*
	 * ตั้งค่าชื่อฐานข้อมูล
	 */
	public static function setDBName($txt)
	{
		self::$_dbName = $txt;
	}

	/*
	 * ตั้งค่าประเภทฐานข้อมูล
	 */
	public static function setDBType($txt)
	{
		self::$_dbType = strtoupper($txt);
	}
	
	/*
	 * ตั้งค่า Auto Increment
	 */
	public static function setAutoIncrement($txt)
	{
		self::$_autoIncrement = strtoupper($txt);
	}
	
	/*
	 * ตั้งค่ารูปแบบเวลา ใน db
	 */
	public static function setLangDate($txt)
	{
		self::$_langDate = strtoupper($txt);
	}
	
	/*
	 * ตั้งค่ารูปแบบเวลา ใน db
	 */
	public static function setRunType($txt)
	{
		self::$_systemRunType = strtoupper($txt);
	}
}

## Main Variable Array

$arr_operator = array(
	1 => 'เท่ากับ (=)',
	2 => 'มีบางคำ (Like)',
	3 => 'ขึ้นต้นด้วย (-%)',
	4 => 'ลงท้ายด้วย (%-)',
	5 => 'มากกว่า (>)',
	6 => 'มากกว่าเท่ากับ (>=)',
	7 => 'น้อยกว่า (<)',
	8 => 'น้อยกว่าท่ากับ (<=)',
	9 => 'ไม่เท่ากับ (!=)',
	10 => 'ระหว่าง (BETWEEN)',
	99 => 'ไม่ค้นหา',
);

$arr_wf_detail_type = array(
	'P' => 'กระบวนงาน',
	'S' => 'เริ่มกระบวนงาน',
	'E' => 'จบกระบวนงาน',
	'T' => 'โยนค่าไปกระบวนการอื่น'
	//'M' => 'โยนค่าไป Master'
);

$arr_textbox_format = array(
	'' => 'ไม่มีรูปแบบ',
	'ED' => 'Editor',
	'E' => 'อีเมล์',
	'C' => 'เลขที่บัตรประชาชน',
	'P' => 'รหัสผ่าน',
	'N' => 'ตัวเลข (จำนวนเต็ม)',
	'N1' => 'ตัวเลข (ทศนิยม 1 ตำแหน่ง)',
	'N2' => 'ตัวเลข (ทศนิยม 2 ตำแหน่ง)',
	'N3' => 'ตัวเลข (ทศนิยม 3 ตำแหน่ง)',
	'N4' => 'ตัวเลข (ทศนิยม 4 ตำแหน่ง)',
	'N5' => 'ตัวเลข (ทศนิยม 5 ตำแหน่ง)',
	'N6' => 'ตัวเลข (ทศนิยม 6 ตำแหน่ง)',
	'TU' => 'ตัวอักษรใหญ่ทั้งหมด (ABC)',
	'TL' => 'ตัวอักษรเล็กทั้งหมด (abc)',
	'TC' => 'ขึ้นต้นตัวอักษรใหญ่ (Abc)'
	
);

$arr_system_data = array(
	'S_U' => 'ผู้ใช้งานระบบ',
	'S_P' => 'ตำแหน่ง',
	'S_D' => 'หน่วยงาน'
);

$arr_format_date = array(
	'S' => 'shorttoday',
	'F' => 'fulltoday',
	'E' => 'extratoday',
	'MC' => 'thismonth',
	'MS' => 'thisshortmonth',
	'Y' => 'thisyear',
	'BY' => 'budgetyear'
);

## Main Function

/*
 * Create Table
 */
function create_table_wf($table_name)
{
	switch(db::$_dbType)
	{
		case 'MSSQL':
			$sql_chk = db::query("SELECT COUNT(*) AS TOTAL FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_CATALOG='".strtoupper(db::$_dbName)."' AND TABLE_NAME = '".$table_name."'");
			$rec_chk = db::fetch_array($sql_chk);
			if($rec_chk['TOTAL'] > 0)
			{
				echo "<script>alert('ตาราง ".$table_name." ถูกใช้งานแล้ว กรุณาตรวจสอบ'); window.location.href='".$_SERVER['HTTP_REFERER']."';</script>";
				db::db_close();
				exit;
			}

			$sql_create = "CREATE TABLE [dbo].[".$table_name."](
					[WFR_ID] int NOT NULL,
					[WFR_TIMESTAMP]	date,
					[WF_DET_STEP]	int,
					[WF_DET_NEXT]	int,
					[WFR_UID]	int,
					[WFR_STATUS]	varchar(50),
					[WFR_REF]	int
					, PRIMARY KEY ([WFR_ID])
				)";
			break;
		case 'MYSQL':
			$sql_chk = db::query("SELECT COUNT(*) AS TOTAL FROM INFORMATION_SCHEMA.TABLES WHERE UPPER(TABLE_SCHEMA) = '".strtoupper(db::$_dbName)."' AND UPPER(TABLE_NAME) = '".$table_name."'");
			$rec_chk = db::fetch_array($sql_chk);
			if($rec_chk['TOTAL'] > 0)
			{
				echo "<script>alert('ตาราง ".$table_name." ถูกใช้งานแล้ว กรุณาตรวจสอบ'); window.location.href='".$_SERVER['HTTP_REFERER']."';</script>";
				db::db_close();
				exit;
			}

			$sql_create = "CREATE TABLE ".$table_name."(
					WFR_ID int(11) NOT NULL AUTO_INCREMENT,
					WFR_TIMESTAMP date DEFAULT NULL, 
					WF_DET_STEP	int(11),
					WF_DET_NEXT	int(11),
					WFR_UID	int(11),
					WFR_STATUS	VARCHAR(50),
					WFR_REF	int(11),
					PRIMARY KEY (WFR_ID) 
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			break;
		case 'ORACLE':
			$sql_chk = db::query("SELECT COUNT(*) AS TOTAL FROM USER_TABLES where TABLE_NAME = '".$table_name."'");
			$rec_chk = db::fetch_array($sql_chk);
			if($rec_chk['TOTAL'] > 0)
			{
				echo "<script>alert('ตาราง ".$table_name." ถูกใช้งานแล้ว กรุณาตรวจสอบ'); window.location.href='';</script>";
				db::db_close();
				exit;
			}

			$sql_create = "CREATE TABLE ".$table_name."
					( WFR_ID NUMBER(20) NOT NULL,
					  WFR_TIMESTAMP DATE,
					  WF_DET_STEP	NUMBER(20),
					  WF_DET_NEXT	NUMBER(20),
					  WFR_UID	NUMBER(20),
					  WFR_STATUS	VARCHAR2(50),
					  WFR_REF	NUMBER(20),
					  CONSTRAINT ".$table_name."_pk PRIMARY KEY (WFR_ID)
					)";
			break;
	}

	if($sql_create != "")
	{
		db::query($sql_create);
	}
}
function check_table_wf($table_name)
{
	switch(db::$_dbType)
	{
		case 'MSSQL':
			$sql_chk = db::query("SELECT COUNT(*) AS TOTAL FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_CATALOG='".strtoupper(db::$_dbName)."' AND TABLE_NAME = '".$table_name."'");
			$rec_chk = db::fetch_array($sql_chk);
			return $rec_chk['TOTAL'];
			break;
		case 'MYSQL':
			$sql_chk = db::query("SELECT COUNT(*) AS TOTAL FROM INFORMATION_SCHEMA.TABLES WHERE UPPER(TABLE_SCHEMA) = '".strtoupper(db::$_dbName)."' AND UPPER(TABLE_NAME) = '".$table_name."'");
			$rec_chk = db::fetch_array($sql_chk);
			return $rec_chk['TOTAL'];
			break;
		case 'ORACLE':
			$sql_chk = db::query("SELECT COUNT(*) AS TOTAL FROM USER_TABLES where TABLE_NAME = '".$table_name."'");
			$rec_chk = db::fetch_array($sql_chk);
			return $rec_chk['TOTAL'];
			break;
	}
}
/*
 * Create Table
 */
function create_table($table_name, $field_name, $field_type, $field_length)
{
	switch(db::$_dbType)
	{
		case 'MSSQL':
			$sql_chk = db::query("SELECT COUNT(*) AS TOTAL FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' AND TABLE_CATALOG='".strtoupper(db::$_dbName)."' AND TABLE_NAME = '".$table_name."'");
			$rec_chk = db::fetch_array($sql_chk);
			if($rec_chk['TOTAL'] > 0)
			{
				echo "<script>alert('ตาราง ".$table_name." ถูกใช้งานแล้ว กรุณาตรวจสอบ'); window.location.href='".$_SERVER['HTTP_REFERER']."';</script>";
				db::db_close();
				exit;
			}

			$field_length = $field_length == "" ? "" : "(".$field_length.")";

			$sql_create = "CREATE TABLE [dbo].[".$table_name."](
					[".$field_name."] ".$field_type." ".$field_length." NOT NULL, PRIMARY KEY ([".$field_name."])
				)";
			break;
		case 'MYSQL':
			$sql_chk = db::query("SELECT COUNT(*) AS TOTAL FROM INFORMATION_SCHEMA.TABLES WHERE UPPER(TABLE_SCHEMA) = '".strtoupper(db::$_dbName)."' AND UPPER(TABLE_NAME) = '".$table_name."'");
			$rec_chk = db::fetch_array($sql_chk);
			if($rec_chk['TOTAL'] > 0)
			{
				echo "<script>alert('ตาราง ".$table_name." ถูกใช้งานแล้ว กรุณาตรวจสอบ'); window.location.href='".$_SERVER['HTTP_REFERER']."';</script>";
				db::db_close();
				exit;
			}

			$sql_create = "CREATE TABLE ".$table_name."(
					".$field_name." ".$field_type."(".$field_length.") NOT NULL AUTO_INCREMENT,
					PRIMARY KEY (".$field_name.") 
				) ENGINE=MyISAM DEFAULT CHARSET=utf8";
			break;
		case 'ORACLE':
			$sql_chk = db::query("SELECT COUNT(*) AS TOTAL FROM USER_TABLES where TABLE_NAME = '".$table_name."'");
			$rec_chk = db::fetch_array($sql_chk);
			if($rec_chk['TOTAL'] > 0)
			{
				echo "<script>alert('ตาราง ".$table_name." ถูกใช้งานแล้ว กรุณาตรวจสอบ'); window.location.href='".$_SERVER['HTTP_REFERER']."';</script>";
				db::db_close();
				exit;
			}

			$sql_create = "CREATE TABLE ".$table_name."
					( ".$field_name." ".$field_type."(".$field_length.") NOT NULL,
					  CONSTRAINT ".$table_name."_pk PRIMARY KEY (".$field_name.")
					)";
			break;
	}

	if($sql_create != "")
	{
		db::query($sql_create);
	}
}

/*
 * Alter Add Field
 */
function add_field($table_name, $field_name, $data_type, $length, $comment = "")
{
	if($length != "")
	{
		if(db::$_dbType == "MSSQL" && strtoupper($data_type) == "INT")
		{
			$type = $data_type;
		}
		else
		{
			$type = $data_type."(".$length.")";

		}
	}
	else
	{
		$type = $data_type;
	}
	
	if(db::$_dbType == "ORACLE")
	{
		if(strtoupper($data_type) == "NUMBER")
		{
			$type = "NUMBER";
		}
		elseif(strtoupper($data_type) == "TEXT")
		{
			$type = "VARCHAR2";
		}
	}

	$alter = "ALTER TABLE ".$table_name." ADD ".strtoupper($field_name)." ".strtoupper($type)." ";
	db::query($alter);

	if($comment != "")
	{
		if(db::$_dbType == "ORACLE")
		{
			$comment_sql = "COMMENT ON COLUMN ".$table_name.".".$field_name." IS '".$comment."' ";
		}
		elseif(db::$_dbType == "MYSQL")
		{

		}
		elseif(db::$_dbType == "MSSQL")
		{
			$comment_sql = "IF ((SELECT COUNT(*) from fn_listextendedproperty('MS_Description', 
									'SCHEMA', N'dbo', 
									'TABLE', N'".$table_name."', 
									'COLUMN', N'".$field_name."')) > 0) 
									EXEC sp_updateextendedproperty @name = N'MS_Description', @value = N'".$comment."'
									, @level0type = 'SCHEMA', @level0name = N'dbo'
									, @level1type = 'TABLE', @level1name = N'".$table_name."'
									, @level2type = 'COLUMN', @level2name = N'".$field_name."'
									ELSE
									EXEC sp_addextendedproperty @name = N'MS_Description', @value = N'".$comment."'
									, @level0type = 'SCHEMA', @level0name = N'dbo'
									, @level1type = 'TABLE', @level1name = N'".$table_name."'
									, @level2type = 'COLUMN', @level2name = N'".$field_name."'";
		}
		if($comment_sql != ""){
			db::query($comment_sql);
		}
	}
}


/*
 * Rename Field
 */
function rename_field($table,$table_name_new,$table_name_old)
{
	if(db::$_dbType == "ORACLE")
	{
		$rename = "ALTER TABLE ".$table." RENAME COLUMN ".$table_name_old." TO ".$table_name_new;
	}
	elseif(db::$_dbType == "MYSQL")
	{
		$q_field_old = db::query("SHOW FIELDS FROM ".$table." where Field ='".$table_name_old."'");
		$d_field_old = db::fetch_array($q_field_old);
		$rename = "ALTER TABLE ".$table." CHANGE COLUMN ".$table_name_old." ".$table_name_new." ".$d_field_old["Type"];
	}
	elseif(db::$_dbType == "MSSQL")
	{
		$rename = "EXEC sp_rename N'[dbo].[".$table."].[".$table_name_old."]', N'".$table_name_new."', 'COLUMN'";
	}

	db::query($rename);
}




/*
 * Modify Field
 */
function modify_field($table_name, $field_name, $field_type, $field_length)
{
	if($field_length != '')
	{
		$length = "(".$field_length.")";
	}
	else
	{
		$length = "";
	}

	if(db::$_dbType == "ORACLE")
	{
		if(strtoupper($field_type) == "NUMBER")
		{
			$length = "";
		}
		elseif(strtoupper($field_type) == "TEXT")
		{
			$field_type = "VARCHAR2";
		}
		$modify = "ALTER TABLE ".$table_name." MODIFY ".$field_name." ".$field_type.$length;
	}
	elseif(db::$_dbType == "MYSQL")
	{
		$modify = "ALTER TABLE ".$table_name." MODIFY COLUMN ".$field_name." ".$field_type.$length;
	}
	elseif(db::$_dbType == "MSSQL")
	{
		if($field_type == "text ")
		{
			$length = "";
		}

		$modify = "ALTER TABLE [dbo].[".$table_name."] ALTER COLUMN [".$field_name."] ".$field_type.$length;
	}
	
	db::query($modify);
}


/*
 * Drop Field
 */
function Drop_field($table_name, $field_name)
{
	
	if(db::$_dbType == "ORACLE")
	{
		$drop_field = "ALTER TABLE ".$table_name." DROP COLUMN ".$field_name;
	}
	elseif(db::$_dbType == "MYSQL")
	{
		$drop_field = "ALTER TABLE ".$table_name." DROP COLUMN ".$field_name; 
	}
	elseif(db::$_dbType == "MSSQL")
	{
		$drop_field = "ALTER TABLE [dbo].[".$table_name."] DROP COLUMN [".$field_name."]";
	}
	
	db::query($drop_field);
}


/*
 * Rename Table
 */
function rename_table($table_name_old, $table_name_new)
{
	if(db::$_dbType == "ORACLE")
	{
		$rename_table = "ALTER TABLE ".$table_name_old." RENAME TO ".$table_name_new;
	}
	elseif(db::$_dbType == "MYSQL")
	{
		$rename_table = "ALTER TABLE ".$table_name_old." RENAME ".$table_name_new;
	}
	elseif(db::$_dbType == "MSSQL")
	{
		$rename_table = "sp_rename '".$table_name_old."','".$table_name_new."' ";
	}

	db::query($rename_table);
}



/*
 * Drop Table
 */
function drop_table($table_name)
{
	if(db::$_dbType == "ORACLE")
	{
		db::query("DROP TABLE ".db::$_dbName.".".trim(strtoupper($table_name)));
	}
	elseif(db::$_dbType == "MYSQL")
	{
		db::query("DROP TABLE ".db::$_dbName.".".trim(strtoupper($table_name)));
	}
	elseif(db::$_dbType == "MSSQL")
	{
		db::query("DROP TABLE dbo.".trim(strtoupper($table_name)));
	}
}

/*
 * check table exists
 */

function chk_table_exists($table_name)
{
	if(db::$_dbType == "ORACLE")
	{
		$sql_table = "select count(tname) as t_total from tab where tname = '".$table_name."';";
	}
	elseif(db::$_dbType == "MYSQL")
	{
		$sql_table = "select count(TABLE_NAME) as t_total  from INFORMATION_SCHEMA.TABLES where TABLE_CATALOG= '".$_dbName."' AND TABLE_SCHEMA = 'dbo' AND TABLE_TYPE='BASE TABLE' AND TABLE_NAME='".$table_name."'";
	}
	elseif(db::$_dbType == "MSSQL")
	{
		$sql_table = "select count(TABLE_NAME) as t_total from information_schema.TABLES
       where TABLE_SCHEMA = '".$_dbName."' AND TABLE_NAME='".$table_name."';";
	}
	$sql_table = db::query($sql_table);
	$t = db::fetch_array($sql_table);
	return $t["t_total"];
}

/*
 * Select Fields
 */
function show_sys_comment($wf_type,$column){
	$description = "";
	if($wf_type=="W"){
		switch($column){
			case 'WFR_ID':
				$description = 'รหัส PK ของตาราง';
			break;
			case 'WFR_TIMESTAMP':
				$description = 'วันที่สร้างข้อมูล';
			break;
			case 'WF_DET_STEP':
				$description = 'รหัสขั้นตอนปัจจุบัน';
			break;
			case 'WF_DET_NEXT':
				$description = 'รหัสขั้นตอนถัดไป';
			break;
			case 'WFR_UID':
				$description = 'รหัสผู้ใช้งานที่สร้างรายการ';
			break;
			case 'WFR_STATUS':
				$description = 'สถานะของข้อมูล';
			break;
			case 'WFR_REF':
				$description = 'รหัสของรายการกรณีที่มีการโยนข้าม Flow';
			break;
		}
	}
	if($wf_type=="F"){
		switch($column){
			case 'F_ID':
				$description = 'รหัส PK ของตาราง';
			break;
			case 'WF_MAIN_ID':
				$description = 'รหัสของ workflow ที่เรียก form ไปใช้';
			break;
			case 'WFD_ID':
				$description = 'รหัสขั้นตอนที่เรียก form ไปใช้';
			break;
			case 'WFR_ID':
				$description = 'รหัสข้อมูลที่บันทึกลง form';
			break;
			case 'WFS_ID':
				$description = 'รหัส input ที่เรียก form ไปใช้';
			break;
			case 'F_TEMP_ID':
				$description = 'รหัสข้อมูลที่บันทึกลง form กรณีเพิ่ม';
			break;
			case 'F_CREATE_DATE':
				$description = 'วันที่สร้างรายการ';
			break;
			case 'F_CREATE_BY':
				$description = 'รหัสผู้ใช้งานที่สร้างรายการ';
			break;
			case 'F_UPDATE_DATE':
				$description = 'วันที่บันทึกล่าสุด';
			break;
			case 'F_UPDATE_BY':
				$description = 'รหัสผู้ใช้งานที่บันทึกล่าสุด';
			break;
		}
	}
	return $description;
}
function select_field($table_name, $field_name)
{
	
	$sql_main = db::query("SELECT WF_MAIN_ID,WF_TYPE FROM WF_MAIN WHERE WF_MAIN_SHORTNAME='".$table_name."'");
	$main = db::fetch_array($sql_main);
	
	$sql_sf = db::query("SELECT FORM_MAIN_ID FROM WF_STEP_FORM WHERE WF_MAIN_ID='".$main["WF_MAIN_ID"]."' AND WFS_FIELD_NAME='".$field_name."'");
	$sf = db::fetch_array($sql_sf);
	if($sf["FORM_MAIN_ID"] == '4' OR $sf["FORM_MAIN_ID"] == '5' OR $sf["FORM_MAIN_ID"] == '7' OR $sf["FORM_MAIN_ID"] == '9'){//checkbox,radio,selectbox
		$select_field = "WFS_NAME,WFS_OPTION_SELECT_DATA";
		
	}elseif($sf["FORM_MAIN_ID"] == '16'){ //form
		$select_field = "WFS_NAME,WFS_FORM_SELECT AS WFS_OPTION_SELECT_DATA";
		
	}else{
		$select_field = '*';
	}
	$sql_wfs1 = db::query("SELECT ".$select_field." FROM WF_STEP_FORM WHERE WF_MAIN_ID='".$main["WF_MAIN_ID"]."' AND FORM_MAIN_ID='".$sf["FORM_MAIN_ID"]."' AND WFS_FIELD_NAME='".$field_name."'");
	$wfs = db::fetch_array($sql_wfs1);
	if(is_numeric($wfs["WFS_OPTION_SELECT_DATA"])){
	$sql_ref = db::query("SELECT WF_MAIN_SHORTNAME,WF_FIELD_PK FROM WF_MAIN WHERE WF_MAIN_ID='".$wfs["WFS_OPTION_SELECT_DATA"]."'");
	$ref = db::fetch_array($sql_ref);
		}
	if(db::$_dbType == "ORACLE")
	{
		$select_f = "select COLUMN_NAME,DATA_TYPE,DATA_LENGTH,DATA_PRECISION from user_tab_cols where column_name = '".strtoupper($field_name)."' and table_name = '".$table_name."' ";
		$query_f = db::query($select_f);
		$rec = db::fetch_array($query_f);
		
		$f_comment = trim($wfs["WFS_NAME"]);
		$comment_type = "G";
		if($f_comment == ""){
			$f_comment = show_sys_comment($main["WF_TYPE"],$rec["COLUMN_NAME"]);
			$comment_type = "S";
			if($f_comment == ""){
				$sql_comment = db::query("select COMMENTS from ALL_COL_COMMENTS where column_name = '".strtoupper($field_name)."' and table_name = '".$table_name."' and OWNER = '".strtoupper(db::$_dbOwner)."'");
				$comment = db::fetch_array($sql_comment);
				$f_comment = $comment['COMMENTS'];
				$comment_type = "C";
			}
		}
		
		
		$array_field["FIELD_NAME"] = $rec["COLUMN_NAME"];
		$array_field["FIELD_TYPE"] = $rec["DATA_TYPE"];
		$array_field["FIELD_LENGTH"] = $rec["DATA_LENGTH"];
		$array_field["FIELD_COMMENT"] = $f_comment;
		$array_field["FIELD_COMMENT_TYPE"] = $comment_type;
		$array_field["FIELD_REF_TABLE"] = $ref["WF_MAIN_SHORTNAME"];
		$array_field["FIELD_REF_PK"] = $ref["WF_FIELD_PK"];
		
	}
	elseif(db::$_dbType == "MYSQL")
	{
		$select_f = "SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$table_name."' AND COLUMN_NAME='".strtoupper($field_name)."'";
		$query_f = db::query($select_f);
		$rec = db::fetch_array($query_f);
		
		$array_field["FIELD_NAME"] = $rec["COLUMN_NAME"];
		$array_field["FIELD_TYPE"] = $rec["DATA_TYPE"];
		$array_field["FIELD_LENGTH"] = $rec["CHARACTER_MAXIMUM_LENGTH"];
		$array_field["FIELD_COMMENT"] = $wfs["WFS_NAME"];
		$array_field["FIELD_REF_TABLE"] = $ref["WF_MAIN_SHORTNAME"];
		$array_field["FIELD_REF_PK"] = $ref["WF_FIELD_PK"];
	}
	elseif(db::$_dbType == "MSSQL")
	{
		$select_f = "SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='".$table_name."' AND COLUMN_NAME='".strtoupper($field_name)."'";
		$query_f = db::query($select_f);
		$rec = db::fetch_array($query_f);
		
		$array_field["FIELD_NAME"] = $rec["COLUMN_NAME"];
		$array_field["FIELD_TYPE"] = $rec["DATA_TYPE"];
		$array_field["FIELD_LENGTH"] = $rec["CHARACTER_MAXIMUM_LENGTH"];
		$array_field["FIELD_COMMENT"] = $wfs["WFS_NAME"];
		$array_field["FIELD_REF_TABLE"] = $ref["WF_MAIN_SHORTNAME"];
		$array_field["FIELD_REF_PK"] = $ref["WF_FIELD_PK"];
	}

	return $array_field;
}
function select_field_other($table_name, $field_name)
{

	if(db::$_dbType == "ORACLE")
	{
		$select_f = "select COLUMN_NAME,DATA_TYPE,DATA_LENGTH,DATA_PRECISION from user_tab_cols where column_name = '".strtoupper($field_name)."' and table_name = '".$table_name."' ";
		$query_f = db::query($select_f);
		$rec = db::fetch_array($query_f);
		

		if($f_comment == ""){ 
		
			$sql_comment = db::query("select COMMENTS from ALL_COL_COMMENTS where column_name = '".strtoupper($field_name)."' and table_name = '".$table_name."' and OWNER = '".strtoupper(db::$_dbOwner)."'");
			$comment = db::fetch_array($sql_comment);
			$f_comment = $comment['COMMENTS'];
			$comment_type = "C";
		}
		
		$array_field["FIELD_NAME"] = $rec["COLUMN_NAME"];
		$array_field["FIELD_TYPE"] = $rec["DATA_TYPE"];
		$array_field["FIELD_LENGTH"] = $rec["DATA_LENGTH"];
		$array_field["FIELD_COMMENT"] = $f_comment;
		$array_field["FIELD_COMMENT_TYPE"] = $comment_type;
		//$array_field["FIELD_REF_PK"] = $ref["WF_FIELD_PK"];
		//$array_field["FIELD_REF_TABLE"] = $ref["WF_MAIN_SHORTNAME"];
		
	}
	elseif(db::$_dbType == "MYSQL")
	{
		$select_f = "SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$table_name."' AND COLUMN_NAME='".strtoupper($field_name)."'";
		$query_f = db::query($select_f);
		$rec = db::fetch_array($query_f);
		
		$array_field["FIELD_NAME"] = $rec["COLUMN_NAME"];
		$array_field["FIELD_TYPE"] = $rec["DATA_TYPE"];
		$array_field["FIELD_LENGTH"] = $rec["CHARACTER_MAXIMUM_LENGTH"];
		$array_field["FIELD_COMMENT"] = $wfs["WFS_NAME"];
		$array_field["FIELD_REF_TABLE"] = $ref["WF_MAIN_SHORTNAME"];
		$array_field["FIELD_REF_PK"] = $ref["WF_FIELD_PK"];
	}
	elseif(db::$_dbType == "MSSQL")
	{
		$select_f = "SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='".$table_name."' AND COLUMN_NAME='".strtoupper($field_name)."'";
		$query_f = db::query($select_f);
		$rec = db::fetch_array($query_f);
		
		$array_field["FIELD_NAME"] = $rec["COLUMN_NAME"];
		$array_field["FIELD_TYPE"] = $rec["DATA_TYPE"];
		$array_field["FIELD_LENGTH"] = $rec["CHARACTER_MAXIMUM_LENGTH"];
		$array_field["FIELD_COMMENT"] = $wfs["WFS_NAME"];
		$array_field["FIELD_REF_TABLE"] = $ref["WF_MAIN_SHORTNAME"];
		$array_field["FIELD_REF_PK"] = $ref["WF_FIELD_PK"];
	}

	return $array_field;
}

function get_pk($table_name)
{
	if(db::$_dbType == "ORACLE")
	{
		$sql_table = "SELECT cols.COLUMN_NAME
        FROM user_constraints cons, user_cons_columns cols
        WHERE cols.table_name = '".$table_name."'
        AND cons.constraint_type = 'P'
        AND cons.constraint_name = cols.constraint_name
        AND cons.owner = cols.owner
        ORDER BY cols.table_name, cols.position";
	}
	elseif(db::$_dbType == "MYSQL")
	{
		$sql_table = ""; 
	}
	elseif(db::$_dbType == "MSSQL")
	{
		$sql_table = "";
	}
	//if($sql_table != ''){
		$query_table = db::query($sql_table);
		$t = db::fetch_array($query_table);
	//}
	return $t["COLUMN_NAME"];
}

function get_month($type)
{
	if($type == "S")
	{
		$month = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
	}
	elseif($type == "F")
	{
		$month = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
	}
	elseif($type == "E")
	{
		$month = array("", "JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", "AUG", "SEP", "OCT", "NOV", "DEC"); 
	}

	return $month;
}

function print_pre($text)
{
	echo "<pre>";
	print_r($text);
	echo "</pre>";
}

function conText($text, $format = "")
{
	$outText = stripslashes(htmlspecialchars(trim($text), ENT_QUOTES));

	if($format == "number")
	{
		$outText = str_replace(',', '', $outText);
	}
	elseif($format == "date")
	{
		$outText = date2db($outText);
	}

	return $outText;
}
function conTextG($text, $format = "")
{
	$text = stripslashes(htmlspecialchars(trim($text), ENT_QUOTES));
	$outText = conText($_GET[$text],$format);
	return $outText;
}
function conTextP($text, $format = "")
{
	$text = stripslashes(htmlspecialchars(trim($text), ENT_QUOTES));
	$outText = conText($_POST[$text],$format);
	return $outText;
}
// เช็ค type กับ length
function checkFieldDB($table, $field)
{
	$wf_field = array();
	switch(db::$_dbType)
	{
		case 'MSSQL':
			$sql_check = db::query("SELECT DATA_TYPE,CHARACTER_MAXIMUM_LENGTH AS LE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '".$table."' AND COLUMN_NAME = '".strtoupper($field)."'");
			$F = db::fetch_array($sql_check);
			$type = strtoupper($F["DATA_TYPE"]);
			$len = $F["LE"];
			break;
		case 'MYSQL':
			$sql_check = db::query("SHOW FULL FIELDS FROM `".$table."` WHERE FIELD = '".strtoupper($field)."' ");
			$F = db::fetch_array($sql_check);
			$t = explode('(',$F['Type']);
			$l = explode(')',$t[1]);
			
			$type = strtoupper($t[0]);
			$len = $l[0];
			break;
		case 'ORACLE':
			$sql_check = db::query("select DATA_TYPE,DATA_LENGTH from user_tab_cols where column_name = '".strtoupper($field)."' and table_name = '".$table."'");
			$F = db::fetch_array($sql_check);
			$type = $F["DATA_TYPE"];
			$len = $F["DATA_LENGTH"];
			break;
	}
	$wf_field["len"] = $len;
	if($type == "NUMBER" OR $type == "INT" OR $type == "FLOAT" OR $type == "DECIMAL"){
		$wf_field["type"] = "N";
	}elseif($type == "DATE"){
		$wf_field["type"] = "D";
	}else{
		$wf_field["type"] = "T";
		if($type == "CLOB"){
			$wf_field["len"] = 0;
		}
	}
	return $wf_field;
}

/*
 * แปลงวันที่จากเว็บเข้าแต่ละรูปแบบฐานข้อมูล
 * Input = 20/05/2560
 * Output = 2017-05-20 (Mysql)
 */
function date2db($value='')
{
	$new_date = "";
	switch(db::$_dbType)
	{
		case 'MSSQL':
			if($value != "")
			{
				$old_date = explode("/", $value);
				$new_date = ($old_date[2] - 543)."-".$old_date[1]."-".$old_date[0];
			}
			else
			{
				$new_date = "";
			}

			break;
		case 'MYSQL':
			if($value != "")
			{
				$old_date = explode("/", $value);
				$new_date = ($old_date[2] - 543)."-".$old_date[1]."-".$old_date[0];
			}
			else
			{
				$new_date = "";
			}
			break;
		case 'ORACLE':
		if($value != "")
			{
				/*
			if(db::$_langDate == "EN")
			{
				$mont_th_short = get_month('E');
				$sp_date = explode("/", $value);
				$year = $sp_date[2]-543;
				$year1 = substr($year,2,2);
				// หา เป็นเดือนที่มี 5 char หรือ 4   ถ้า 5  จะเว้นช่องว่างแค่ 1  ช่อง
				$new_date = $sp_date[0] . "-" . $mont_th_short[($sp_date[1] * 1)] . "-" . ($year1);
			}
			else
			{
				$mont_th_short = get_month('S');
				$sp_date = explode("/", $value);
				// หา เป็นเดือนที่มี 5 char หรือ 4   ถ้า 5  จะเว้นช่องว่างแค่ 1  ช่อง
				if(strlen($mont_th_short[($sp_date[1] * 1)]) == 5)
				{
					$space = " ";
				}
				else
				{
					$space = "  ";
				}
				$new_date = $sp_date[0] . " " . $mont_th_short[($sp_date[1] * 1)] . $space . ($sp_date[2] - 543);
			}*/
				$old_date = explode("/", $value);
				$new_date = ($old_date[2] - 543)."-".$old_date[1]."-".$old_date[0];
			}
			else
			{
				$new_date = "";
			}
			break;
	}

	return $new_date;
}
function date2db_en($value='')
{
	$new_date = "";
	switch(db::$_dbType)
	{
		case 'MSSQL':
			if($value != "")
			{
				$old_date = explode("/", $value);
				$new_date = $old_date[2]."-".$old_date[1]."-".$old_date[0];
			}
			else
			{
				$new_date = "";
			}

			break;
		case 'MYSQL':
			if($value != "")
			{
				$old_date = explode("/", $value);
				$new_date = $old_date[2]."-".$old_date[1]."-".$old_date[0];
			}
			else
			{
				$new_date = "";
			}
			break;
		case 'ORACLE':
		if($value != "")
			{
				/*
			if(db::$_langDate == "EN")
			{
				$mont_th_short = get_month('E');
				$sp_date = explode("/", $value);
				$year = $sp_date[2]-543;
				$year1 = substr($year,2,2);
				// หา เป็นเดือนที่มี 5 char หรือ 4   ถ้า 5  จะเว้นช่องว่างแค่ 1  ช่อง
				$new_date = $sp_date[0] . "-" . $mont_th_short[($sp_date[1] * 1)] . "-" . ($year1);
			}
			else
			{
				$mont_th_short = get_month('S');
				$sp_date = explode("/", $value);
				// หา เป็นเดือนที่มี 5 char หรือ 4   ถ้า 5  จะเว้นช่องว่างแค่ 1  ช่อง
				if(strlen($mont_th_short[($sp_date[1] * 1)]) == 5)
				{
					$space = " ";
				}
				else
				{
					$space = "  ";
				}
				$new_date = $sp_date[0] . " " . $mont_th_short[($sp_date[1] * 1)] . $space . ($sp_date[2] - 543);
			}*/
				$old_date = explode("/", $value);
				$new_date = $old_date[2]."-".$old_date[1]."-".$old_date[0];
			}
			else
			{
				$new_date = "";
			}
			break;
	}

	return $new_date;
}
/*
 * แปลงวันที่จากฐานข้อมูล ไปเข้า Date Picker
 * Output = 20/05/2560
 */
function db2date($value)
{
	if($value == "" || $value == "0000-00-00")
	{
		$new_date = "";
	}
	else
	{
		switch(db::$_dbType)
		{
			case 'MSSQL':
				$ex_datetime = explode(' ', $value);
				$old_date = explode("-", $ex_datetime[0]);
				$new_date = $old_date[2]."/".$old_date[1]."/".($old_date[0] + 543);

				break;
			case 'MYSQL':
				$ex_datetime = explode(' ', $value);
				$old_date = explode("-", $ex_datetime[0]);
				$new_date = $old_date[2]."/".$old_date[1]."/".($old_date[0] + 543);

				break;
			case 'ORACLE':
			/*
				if(db::$_langDate == "EN")
				{
					$mont_th_short = get_month('E'); 
					$d = explode("-", $value);
					$new_date = $d[0]."/".sprintf("%02d", array_search($d[1], $mont_th_short))."/".($d[2] + 2543);
				}
				else
				{
					$mont_th_short = get_month('S');
					$date1 = str_replace("  ", " ", $value);
					$d = explode(" ", $date1);
					$new_date = $d[0]."/".sprintf("%02d", array_search($d[1], $mont_th_short))."/".($d[2] + 543);
				}
				*/
				$ex_datetime = explode(' ', $value);
				$old_date = explode("-", $ex_datetime[0]);
				$new_date = $old_date[2]."/".$old_date[1]."/".($old_date[0] + 543);
				
				break;
		}
	}

	return $new_date;
}
function db2date_en($value)
{
	if($value == "" || $value == "0000-00-00")
	{
		$new_date = "";
	}
	else
	{
		switch(db::$_dbType)
		{
			case 'MSSQL':
				$ex_datetime = explode(' ', $value);
				$old_date = explode("-", $ex_datetime[0]);
				$new_date = $old_date[2]."/".$old_date[1]."/".$old_date[0];

				break;
			case 'MYSQL':
				$ex_datetime = explode(' ', $value);
				$old_date = explode("-", $ex_datetime[0]);
				$new_date = $old_date[2]."/".$old_date[1]."/".$old_date[0];

				break;
			case 'ORACLE':
			/*
				if(db::$_langDate == "EN")
				{
					$mont_th_short = get_month('E'); 
					$d = explode("-", $value);
					$new_date = $d[0]."/".sprintf("%02d", array_search($d[1], $mont_th_short))."/".($d[2] + 2543);
				}
				else
				{
					$mont_th_short = get_month('S');
					$date1 = str_replace("  ", " ", $value);
					$d = explode(" ", $date1);
					$new_date = $d[0]."/".sprintf("%02d", array_search($d[1], $mont_th_short))."/".($d[2] + 543);
				}
				*/
				$ex_datetime = explode(' ', $value);
				$old_date = explode("-", $ex_datetime[0]);
				$new_date = $old_date[2]."/".$old_date[1]."/".$old_date[0];
				
				break;
		}
	}

	return $new_date;
}
/*
 * แปลงวันที่จากฐานข้อมูลไปแสดงผล
 * Output = 20 พ.ค. 2560
 */
function db2date_show($value)
{
	if($value == "" || $value == "0000-00-00")
	{
		$new_date = "";
	}
	else
	{
		$mont_th_short = get_month('S');

		switch(db::$_dbType)
		{
			case 'MSSQL':
				$ex_datetime = explode(' ', $value);
				$old_date = explode("-", $ex_datetime[0]);
				$new_date = $old_date[2]." ";
				$new_date .= $mont_th_short[number_format($old_date[1])]." ";
				$new_date .= ($old_date[0] + 543);

				break;
			case 'MYSQL':
				$ex_datetime = explode(' ', $value);
				$old_date = explode("-", $ex_datetime[0]);
				$new_date = $old_date[2]." ";
				$new_date .= $mont_th_short[number_format($old_date[1])]." ";
				$new_date .= ($old_date[0] + 543);

				break;
			case 'ORACLE':
			/*
				$year = (substr($value, -4) + 543);

				$new_date = substr($value, 0, -5)."".$year;
				*/
				$ex_datetime = explode(' ', $value);
				$old_date = explode("-", $ex_datetime[0]);
				$new_date = $old_date[2]." ";
				$new_date .= $mont_th_short[number_format($old_date[1])]." ";
				$new_date .= ($old_date[0] + 543);
				break;
		}
	}

	return $new_date;
}

/*
 * แปลงรูปแบบวันที่
 */
function conDateText($value,$type)
{
	$month_f = array("", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม");
	$month_s = array('','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.');
	$month_e = array('','January','February','March','April','May','June','July','August','September','October','November','December');
	
	if($value == "" OR $value == "0000-00-00" ){
		return "";
	}else{
		$d = explode("-",$value);
		
		if($type == 'S'){ //01 ธ.ค. 60
			$text_date = number_format($d[2],0)." ".$month_s[number_format($d[1],0)]." ".($d[0] + 543);
			
		}else if($type == 'F'){//01 ธันวาคม 2560
			$text_date = number_format($d[2],0)." ".$month_f[number_format($d[1],0)]." ".($d[0] + 543);
			
		}else if($type == 'EF'){//01 January 2017
			$text_date = number_format($d[2],0)." ".$month_e[number_format($d[1],0)]." ".($d[0]);
			
		}else if($type == 'E'){//วันศุกร์ที่ 01 ธันวาคม พ.ศ. 2560
			$text_date = '';
			
		}else if($type == 'MC'){//ธันวาคม
			$text_date = $month_f[number_format($d[1],0)];
			
		}else if($type == 'MS'){//ธ.ค.
			$text_date = $month_s[number_format($d[1],0)];
			
		}else if($type == 'Y'){//2560
			$text_date = ($d[0] + 543);
		}else if($type == 'BY'){//ปีงบประมาณ  ตย.01 ธ.ค. 60 =>2561
			$m = number_format($d[1],0); 
			if($m >=10){
				$text_date = ($d[0] + 544);
				
			}else{
				$text_date = ($d[0] + 543);
				
			}
			
		}
		return $text_date;
	}


}
function redirect($url, $text = false)
{
	if($text != "")
	{
		$alert = 'alert("'.$text.'");';
	}
	else
	{
		$alert = "";
	}
	echo '<script>';
	echo $alert;
	echo 'window.location.href="'.$url.'"';
	echo '</script>';
}

/*
 * หาข้อมูลจากตารางกลับมาเป็นข้อความ
 * @table_name		ชื่อตารางที่ต้องการหา
 * @field_id		ฟิลที่ต้องการหา
 * @field_name		ฟิลที่ต้องการแสดงผล
 * @field_value		ค่าที่เอาไปเป็นเงื่อนไข
 * @where			เงื่อนไขเพิ่มเติม
 */
function get_data($table_name, $field_id, $field_name, $field_value, $where = "")
{
	$sql = db::query("select ".$field_name." from ".$table_name." where ".$field_id." = '".$field_value."' ".$where);
	$rec = db::fetch_array($sql);

	return $rec[$field_name];
}

/*
 * Select ข้อมูลมาเป็น Array
 * @table_name		ชื่อตารางที่ต้องการหา
 * @field_id		ฟิลที่ต้องการหา
 * @field_name		ฟิลที่ต้องการแสดงผล
 * @where			เงื่อนไขเพิ่มเติม
 */
function build_data($table_name, $field_id, $field_name, $where = "")
{
	$data = array();
	if($where != "")
	{
		$where = " where ".$where;
	}
	$sql = db::query("select ".$field_id.", ".$field_name." from ".$table_name." ".$where." order by ".$field_id." asc");
	while($rec = db::fetch_array($sql))
	{
		$data[$rec[$field_id]] = $rec[$field_name];
	}

	return $data;
}

/*
 * Select Count
 * @table_name		ชื่อตารางที่ต้องการหา
 * @field_name		ฟิลที่ต้องการหา
 * @where			เงื่อนไขเพิ่มเติม
 */
function count_data($table_name, $field_name = "*", $where = "")
{
	$data = array();
	if($where != "")
	{
		$where = " where ".$where;
	}
	$sql = db::query("select count(".$field_name.") as total from ".$table_name." ".$where." ");
	$rec = db::fetch_array($sql);

	return $rec['TOTAL'];
}

/*
 * สร้าง Drop down list
 * @name			ชื่อและ ID
 * @data			ข้อมูล เป็น array
 * @selected		ข้อมูลที่ต้องการเลือกเป็น Default
 * @extra			Attribute อื่นๆ
 */
function form_dropdown($name, $data = array(), $selected = "", $extra = "")
{
	$html = '<select name="'.$name.'" id="'.$name.'" class="select2 form-control" '.$extra.'>'.PHP_EOL;
	$html .= '<option value=""></option>'.PHP_EOL;
	foreach($data as $_key => $_val)
	{
		$select_data = $_key == $selected ? 'selected' : '';
		$html .= '<option value="'.$_key.'" '.$select_data.'>'.$_val.'</option>'.PHP_EOL;

	}
	$html .= '</select>';

	echo $html;
}
/*
 * สร้าง Drop down list
 * @name			ชื่อและ ID
 * @value			ค่าที่แสดง
 * @extra			Attribute อื่นๆ
 */
function form_itext($name, $value = "",$class="",$extra = "",$type="text")
{
	$html = '<input type="'.$type.'" name="'.$name.'" id="'.$name.'" class="'.$class.'" value="'.$value.'" '.$extra.'><small id="DUP_'.$name.'_ALERT" class="form-text text-danger" style="display:none"></small>';
	return $html;
}
function form_iarea($name, $value = "",$class="",$extra = "")
{
	$html = '<textarea name="'.$name.'" id="'.$name.'" class="form-control'.$class.'" '.$extra.'>'.$value.'</textarea>';
	return $html;
}
function form_idate($name, $value = "",$class="",$extra = "")
{
	$value = substr($value,0,10);
	$html = '<input name="'.$name.'" id="'.$name.'" value="'.$value.'" '.$extra.'  class="form-control datepicker'.$class.'" placeholder="'.$system_conf["wf_label_date"].'"><span class="input-group-addon bg-primary"><span class="icofont icofont-ui-calendar"></span></span>';
	return $html;
}
function form_iradio2($name,$data = array(),$value = "",$class="",$extra = "")
{
	$html = '<div class="form-radio">';
	foreach($data as $_key => $_val)
	{
		$check_data = $_key == $value ? 'checked' : '';
		$html .= '<div class="radio'.$class.'"><label><input type="radio" name="'.$name.'" id="'.$name.'" value="'.$_key.'" '.$check_data.' '.$extra.'><i class="helper"></i> '.$_val.'</label></div>';

	}
	$html .= '</div>';
	return $html;
}
function form_iradio($name,$data = array(),$class="",$extra = "")
{
	$html = '<div class="form-radio">';
	$num = count($data);
	for($i=0;$i<$num;$i++)
	{ 
		$style_r = "";
		if($data[$i]['level'] > 0){
			$style_r = " style=\"margin-left:".($data[$i]['level']*20)."px;\"";
		}
		$html .= '<div class="radio'.$class.'" '.$style_r.'><label><input type="radio" name="'.$name.'" id="'.$name.'" value="'.$data[$i]['id'].'" '.$data[$i]['selected'].' '.$extra.'><i class="helper"></i> '.$data[$i]['text'].'</label></div>';
	}
	$html .= '</div>';
	return $html;
}
function form_icheck($chk_id,$data_list=array(),$class="",$extra = "")
{
	$html = '';
	$num = count($data_list);
	for($i=0;$i<$num;$i++)
	{
		$html .='<div class="checkbox-color checkbox-primary'.$class.'">'.form_space_show($data_list[$i]['level']).'<input name="'.$data_list[$i]['name'].'" id="'.$data_list[$i]['name'].'" chk-id="'.$chk_id.'" chk-value="'.$data_list[$i]['id'].'" type="checkbox" '.$data_list[$i]['checked'].' value="'.$data_list[$i]['id'].'" '.$extra.'><label for="'.$data_list[$i]['name'].'">'.$data_list[$i]['text'].'</label><input type="hidden" name="'.$data_list[$i]['name'].'_TYPE" id="'.$data_list[$i]['name'].'_TYPE" value="'.$data_list[$i]['opt'].'"></div>';
	}
	if($num > 0){
	$html .='<input type="hidden" name="'.$chk_id.'_COUNT" id="'.$chk_id.'_COUNT" value="'.$num.'">';
	}
	return $html;
}
/*function form_icheck_old($num,$chk_id,$chk_name=array(),$chk_label=array(),$chk_value=array(),$chk_checked=array(),$chk_opt=array(),$class="",$extra = "")
{
	$html = '';
	for($i=0;$i<$num;$i++)
	{
		$html .='<div class="checkbox-color checkbox-primary'.$class.'"><input name="'.$chk_name[$i].'" id="'.$chk_name[$i].'" chk-id="'.$chk_id.'" chk-value="'.$chk_value[$i].'" type="checkbox" '.$chk_checked[$i].' value="'.$chk_value[$i].'" '.$extra.'><label for="'.$chk_name[$i].'">'.$chk_label[$i].'</label><input type="hidden" name="'.$chk_name[$i].'_TYPE" id="'.$chk_name[$i].'_TYPE" value="'.$chk_opt[$i].'"></div>';

	}
	if($num > 0){
	$html .='<input type="hidden" name="'.$chk_id.'_COUNT" id="'.$chk_id.'_COUNT" value="'.$num.'">';
	}
	return $html;
}*/
function form_ifile($name, $value = array(),$title="",$mult="",$extra = "",$comment="")
{
	if($title == ""){ $title = "เลือกไฟล์"; }
	//if($mult == "multiple"){ $name_ex = '[]'; }
	$html = '<div class="md-group-add-on"><span class="md-add-on-file"><button class="btn btn-primary waves-effect waves-light"><i class="zmdi zmdi-cloud-upload"></i> '.$title.'</button></span><div class="md-input-file"><input type="file" name="'.$name.'[]" id="'.$name.'" class=""  '.$mult.'  '.$extra.' /><input type="text" class="md-form-control md-form-file"><label class="md-label-file"></label></div>'.$comment.'</div>';
	return $html;
}
function form_space_show($level){
	$txt = "";
	if($level > 0){
		for($i=0;$i<$level;$i++){
			$txt .= "&nbsp;&nbsp;&nbsp;&nbsp;";
		}
	}
	return $txt;
}
function form_iselect($name,$data = array(),$class="",$extra = "")
{
	$html = '<select name="'.$name.'" id="'.$name.'" class="form-control '.$class.'" '.$extra.'>';
	$num = count($data);
	for($i=0;$i<$num;$i++)
	{
		$html .= '<option value="'.$data[$i]['id'].'" '.$data[$i]['selected'].'>'.form_space_show($data[$i]['level']).$data[$i]['text'].'</option>';

	}
	$html .= '</select>';
	return $html;
}
function form_iselect2($name,$data = array(),$value = "",$class="",$extra = "")
{
	$html = '<select name="'.$name.'" id="'.$name.'" class="form-control'.$class.'" '.$extra.'>';
	$html .= '<option value="" disabled></option>';
	foreach($data as $_key => $_val)
	{
		$check_data = $_key == $value ? 'selected' : '';
		$html .= '<option value="'.$_key.'" '.$check_data.'>'.$_val.'</option>';

	}
	$html .= '</select>';
	return $html;
}
function write_log($act){
	$log_data = array();
	$date = date("Y-m-d");
	$time = date("H:i:s");
	//$time = str_replace(array('am','pm'),array('น.','น.'),$time);
	if($_SERVER["REMOTE_ADDR"]){
		$IPn = $_SERVER["REMOTE_ADDR"];
	}else{
		$IPn = $_SERVER["REMOTE_HOST"];
	}

	$log_data['LOG_IP_ADDRESS'] = $IPn;
	$log_data['LOG_USR_ID'] = $_SESSION['WF_USER_ID'];
	$log_data['LOG_CREATE_DATE'] = $date;
	$log_data['LOG_DETAIL'] = $act;
	$log_data['LOG_CREATE_TIME'] =  $time;
	db::db_insert('LOG_DETAIL', $log_data, 'LOG_ID');
}

db::setHost('sql112.epizy.com');
db::setUser('epiz_25585219');
// db::setPassword('0IeRcUF0ex');
db::setDBName('epiz_25585219_engine_of_a_baker');
db::setDBType('MYSQL');
db::setAutoIncrement("N");
db::setLangDate('EN');
db::setRunType('DEV'); //LIVE,DEV

db::setupDatabase();
?>