<?php
function conv_date($input, $format_month='', $type=''){
	global $mont_en, $mont_en_short, $mont_th, $mont_th_short,$dow_th2;
	/*
		$input='2013-11-14 10:43:04' || '2013-11-14',
		$type='' ไม่แสดงเวลา
		$type='1' แสดงเวลา
	*/
	if(trim($input)){
		if($format_month=='long'){
			$date=(int)substr($input,8,2)." ".$mont_th[substr($input,5,2)]." ".(substr($input,0,4)+543);
		}elseif($format_month=='longs'){
			$date=(int)substr($input,8,2)." ".$mont_th[substr($input,5,2)]." ".(substr($input,0,4)+543);
		}elseif($format_month=='short'){
			$date=(int)substr($input,8,2)." ".$mont_th_short[substr($input,5,2)]." ".(substr($input,0,4)+543);
		}elseif($format_month=='shortyear'){
			$date=(int)substr($input,8,2)." ".$mont_th_short[substr($input,5,2)]." ".substr((substr($input,0,4)+543),2,2);
			
			
			
		}elseif($format_month=='year'){
			$date=(int)(substr($input,0,4)+543);
		}elseif($format_month=='full'){
			$date=(int)substr($input,8,2)." เดือน ".$mont_th[substr($input,5,2)]." พ.ศ. ".(substr($input,0,4)+543);
        }elseif($format_month=='full2'){//REPORT ประชุม 10/09/57[PG]
                        $date="วัน".$dow_th2[date('D', strtotime($input))]."ที่ ".(int)substr($input,8,2)." ".$mont_th[substr($input,5,2)]." พ.ศ. ".(substr($input,0,4)+543);;
		}elseif($format_month=='short2'){
			$date=(int)substr($input,8,2)." ".$mont_th[substr($input,5,2)]."  ".(substr($input,0,4)+543);
		}elseif($format_month=='holiday'){
			$date=(int)substr($input,8,2)." ".$mont_th[substr($input,5,2)];
		}elseif($format_month=='eng'){
			$date=(int)substr($input,8,2)." ".$mont_en[substr($input,5,2)]."  ".substr($input,0,4);
		}elseif($format_month=='pdf'){
			$date=toThaiNumber((int)substr($input,8,2)." ".$mont_th_short[substr($input,5,2)]." ".(substr($input,0,4)+543));
		}elseif($format_month=='time'){
			$date=substr($input,10,6);
		}elseif($format_month=='short3'){
			$date=(int)substr($input,8,2)." ".$mont_th_short[substr($input,5,2)]." ".(substr($input,0,4)+543);
		}else{
			$date=substr($input,8,2)."/".substr($input,5,2)."/".(substr($input,0,4)+543);
		}

		if($type=='1'){
			if($format_month!='pdf'){
				$date.=substr($input,10,9);
			}else{
				$date.=toThaiNumber(substr($input,10,9));
			}
		}
	}else{
		$date=($format_month=='')?"":"-";
	}
	// $date = "GGG"; 
	return $date;
}

//แปลงค่าวันที่ ลง DB
function conv_date_db($input){
    $input = explode("/",$input);
	$date = sizeof($input) == 3 ?($input[2]-543)."-".$input[1]."-".$input[0]:'NULL';
	//print_r($input);
	return $date;
}
?>