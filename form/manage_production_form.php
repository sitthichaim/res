<html lang="en">
<?php 
include "../include/include.php";


@$WFR = $_GET['WFR'];
@$proc = ($WFR?"edit":"add");

$sql = "SELECT * FROM M_MANAGE_PRODUCTION_MAIN WHERE MAIN_ID = '".$WFR."'";
$query = db::query($sql);
$num = db::num_rows($query);
$rec = db::fetch_array($query);

$date = ($rec['DATE']?$rec['DATE']:date('Y-m-d'));

if($rec['BILL_TYPE']==1){
	$act_1 = "checked";
	$act_2 = "";
}else{
	$act_1 = "";
	$act_2 = "checked";	
}
// $arr_type = array('1'=>'กลม','2'=>'เหลี่ยม','3'=>'เลิฟ','4'=>'ตอติญ่า','5'=>'พิซซ่า');

?>
  <body class="antialiased">
    <div class="page">
	<?php include "../include/menu.php";?>
      <div class="content">
        <div class="container-xl">
          <!-- Page title -->
          <div class="page-header">
            <div class="row align-items-center">
              <div class="col-auto">
                <h2 class="page-title">
					จัดการ การผลิต และขนส่ง
                </h2>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <div class="card-title">จัดการจำนวนออเดอร์แต่ละวัน</div>
                </div>
                <div class="card-body">
                  <div class="row">
					<div class="col-md-12">
						<form action="../save/manage_production_save.php" method="POST">
							<input type="hidden" id="proc" name="proc" value="<?php echo $proc;?>">
							<input type="hidden" id="WFR" name="WFR" value="<?php echo $WFR;?>">
							<div class="row">
								<div class="col-md-2"> 
									<label class="control-label">วันที่</label>
								</div>
								<div class="col-md-3">
									<div class="input-icon">
										<input id="DATE" name="DATE" type="date" value="<?php echo $date;?>" class="form-control" placeholder="Select a date" />
										<span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
										</span>
									</div>
								</div>					
							</div><br>
							<div class="row">
								<div class="col-md-2">
									<label class="control-label">ประเภทบิล</label>
								</div>
								<div class="col-md-3">
									<input type="radio" id="BILL_TYPE_1" name="BILL_TYPE" <?php echo $act_1;?> value="1">&nbsp;บิลลูกค้ารายย่อย&nbsp;&nbsp;&nbsp;
									<input type="radio" id="BILL_TYPE_2" name="BILL_TYPE" <?php echo $act_2;?> value="2">&nbsp;บิลร้านค้า
								</div> <!-- /controls -->	
								<div class="col-md-2">
									<label class="control-label">ชื่อลูกค้า/ร้านค้า</label>
								</div>
								<div class="col-md-3">
									<input type="text" class="form-control" id="BILL_NAME" name="BILL_NAME" value="<?php echo $rec['BILL_NAME'];?>" >
								</div>
							</div><br>
							<hr></hr>
							<div id="data">
							<?php 
								$sql_detail = "SELECT * FROM M_MANAGE_PRODUCTION_DETAIL WHERE MAIN_ID = '".$WFR."'";
								$q_detail = db::query($sql_detail);
								$n_detail = db::num_rows($q_detail);
								
							if($n_detail>0){
								$i=1;
								while($data = db::fetch_array($q_detail)){
									if($i==1){
										$btn = '<button type="button" onclick="add_input();" class="btn btn-primary btn-sm" title=""><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>';
									}else{
										$btn = '<button type="button" onclick="del_row('.$i.');" class="btn btn-danger btn-sm" title=""><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>';
									}
									
							?>
								<div class="row" id="row1_<?php echo $i;?>">
									<div class="col-md-2">
										<label class="control-label">ประเภทผลิตภัณฑ์</label>
									</div> 
									<div class="col-md-3">
										<select name="PRODUCT_TYPE[]" id="PRODUCT_TYPE" class="form-control select2 select2-hidden-accessible" placeholder="เลือกประเภทผลิตภัณฑ์" tabindex="-1" aria-hidden="true">
											<option value="" disabled="" selected="">เลือกประเภทผลิตภัณฑ์</option>
											<?php 
										$sql_pro_type = "SELECT PRODUCT_TYPE_ID, PRODUCT_TYPE_NAME FROM M_SETUP_PRODUCT_TYPE WHERE ACTIVE_STATUS = 1 AND DELETE_FLAG = 0";
										$q_pro_type = db::query($sql_pro_type);
										while($r_pro_type = db::fetch_array($q_pro_type)){
											echo "<option value=".$r_pro_type['PRODUCT_TYPE_ID']." ".($r_pro_type['PRODUCT_TYPE_ID']==$data['PRODUCT_TYPE']?"selected":"").">".$r_pro_type['PRODUCT_TYPE_NAME']."</option>";
										} 		 				
											?>
										</select>
									</div> <!-- /controls -->	
									<div class="col-md-2">
										<label class="control-label">สต็อกเมื่อวาน</label>
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control" id="STOCK_OLD_<?php echo $i;?>" name="STOCK_OLD[]" value="<?php echo $data['STOCK_OLD'];?>" onblur="sum_order(<?php echo $i;?>);">
									</div>
									<div class="col-md-1">
										<?php echo $btn;?>
									</div>
								</div><br> 
								<div class="row" id="row2_<?php echo $i;?>">
									<div class="col-md-2">
										<label class="control-label">ออเดอร์ค้างที่ต้องส่งวันนี้</label>
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control" id="ORDER_OLD_<?php echo $i;?>" name="ORDER_OLD[]" value="<?php echo $data['ORDER_OLD'];?>" onblur="sum_order(<?php echo $i;?>);">
									</div>
									<div class="col-md-2">
										<label class="control-label">ออเดอร์วันนี้</label>
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control" id="ORDER_NEW_<?php echo $i;?>" name="ORDER_NEW[]" value="<?php echo $data['ORDER_NEW'];?>" onblur="sum_order(<?php echo $i;?>);">
									</div>
								</div><br> <!-- /controls -->		
								<div class="row" id="row3_<?php echo $i;?>">
									<div class="col-md-2">
										<label class="control-label">สรุปที่ต้องผลิตวันนี้</label>
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control" id="ORDER_SUM_<?php echo $i;?>" name="ORDER_SUM[]" value="<?php echo $data['ORDER_SUM'];?>" readonly>
									</div>
								</div> <!-- /controls -->
							</div><br>
							<?php
								$i++;							
								}
							}else{
								$n_detail = 1;
							?>
								<div class="row" id="row1_1">
									<div class="col-md-2">
										<label class="control-label">ประเภทผลิตภัณฑ์</label>
									</div> 
									<div class="col-md-3">
										<select name="PRODUCT_TYPE[]" id="PRODUCT_TYPE" class="form-control select2 select2-hidden-accessible" placeholder="เลือกประเภทผลิตภัณฑ์" tabindex="-1" aria-hidden="true">
											<option value="" disabled="" selected="">เลือกประเภทผลิตภัณฑ์</option>
											<?php 
										$sql_pro_type = "SELECT PRODUCT_TYPE_ID, PRODUCT_TYPE_NAME FROM M_SETUP_PRODUCT_TYPE WHERE ACTIVE_STATUS = 1 AND DELETE_FLAG = 0";
										$q_pro_type = db::query($sql_pro_type);
										while($r_pro_type = db::fetch_array($q_pro_type)){
											echo "<option value=".$r_pro_type['PRODUCT_TYPE_ID'].">".$r_pro_type['PRODUCT_TYPE_NAME']."</option>";
										} 		 				
											?>
										</select>
									</div> <!-- /controls -->	
									<div class="col-md-2">
										<label class="control-label">สต็อกเมื่อวาน</label>
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control" id="STOCK_OLD_1" name="STOCK_OLD[]" value="" onblur="sum_order(1);">
									</div>
									<div class="col-md-1">
										<button type="button" onclick="add_input();" class="btn btn-primary btn-sm" title=""><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>
									</div>
								</div> <br>
								<div class="row" id="row2_1">
									<div class="col-md-2">
										<label class="control-label">ออเดอร์ค้างที่ต้องส่งวันนี้</label>
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control" id="ORDER_OLD_1" name="ORDER_OLD[]" value="" onblur="sum_order(1);">
									</div>
									<div class="col-md-2">
										<label class="control-label">ออเดอร์วันนี้</label>
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control" id="ORDER_NEW_1" name="ORDER_NEW[]" value="" onblur="sum_order(1);">
									</div>
								</div><br> <!-- /controls -->		
								<div class="row" id="row3_1">
									<div class="col-md-2">
										<label class="control-label">สรุปที่ต้องผลิตวันนี้</label>
									</div>
									<div class="col-md-3">
										<input type="text" class="form-control" id="ORDER_SUM_<?php echo $i;?>" name="ORDER_SUM[]" value="<?php echo $data['ORDER_SUM'];?>" readonly>
									</div>
								</div> <!-- /controls -->	
							</div><br>  
							<?php							
							}		
							?>
							<input type="hidden" name="row_num" id="row_num" value="<?php echo $n_detail;?>">
							<div class="row">
								<div class="col-md-12" align="center">
									<button type="submit" class="btn btn-primary btn-sm" title="">บันทึก</button>
									<a type="button" href="manage_production_disp.php" class="btn btn-danger btn-sm" title="">ยกเลิก</a>
								</div> <!-- /controls -->	
							</div> 
						</form>
					</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
<?php include "../include/footer.php";?>
<script>
  // @formatter:off
  noUiSlider.create(document.getElementById('range-simple'), {
  	start: 20,
  	connect: [true, false],
  	step: 10,
  	range: {
  		min: 0,
  		max: 100
  	}
  });
  // @formatter:on
</script>
<script>
  // @formatter:off
  noUiSlider.create(document.getElementById('range-connect'), {
  	start: [60, 90],
  	connect: [false, true, false],
  	step: 10,
  	range: {
  		min: 0,
  		max: 100
  	}
  });
  // @formatter:on
</script>
<script>
  // @formatter:off
  noUiSlider.create(document.getElementById('range-color'), {
  	start: 40,
  	connect: [true, false],
  	step: 10,
  	range: {
  		min: 0,
  		max: 100
  	}
  });
  // @formatter:on
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
  	flatpickr(document.getElementById('calendar-simple'), {
  	});
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
  	flatpickr(document.getElementById('DATE'), {
		altInput: true,
		altFormat: "d/m/Y",
		dateFormat: "Y-m-d",
	});
  });
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
  	flatpickr(document.getElementById('calendar-inline'), {
  		inline: true,
  	});
  });
</script>
<script>
  $(document).ready(function () {
  	$('#select-tags').selectize({
  		maxItems: 15,
  	});
  });
</script>
<script>
  $(document).ready(function () {
  	$('#select-tags-advanced').selectize({
  		maxItems: 15,
  		plugins: ['remove_button'],
  	});
  });
</script>
<script>
  $(document).ready(function () {
  	$('#select-users').selectize({
  	});
  });
</script>
<script>
  $(document).ready(function () {
  	$('#select-people').selectize({
  		render: {
  			option: function (data, escape) {
  				return '<div class="option">' + data.avatar + '' + escape(data.text) + '</div>';
  			},
  			item: function (data, escape) {
  				return '<div class="d-flex align-items-center">' + data.avatar + '' + escape(data.text) + '</div>';
  			}
  		}
  	});
  });
</script>
<script>
  $(document).ready(function () {
  	$('#select-countries').selectize({
  		render: {
  			option: function (data, escape) {
  				return '<div class="option"><span class="flag flag-country-' + data.flag + ' mr-2 ml-n1"></span>' + escape(data.text) + '</div>';
  			},
  			item: function (data, escape) {
  				return '<div class="d-flex align-items-center"><span class="flag flag-country-' + data.flag + ' mr-2 ml-n1"></span>' + escape(data.text) + '</div>';
  			}
  		}
  	});
  });
</script>
<script>
  $(document).ready(function () {
  	$('#select-states').selectize({
  		render: {
  			option: function (data, escape) {
  				return '<div class="option"><span class="flag flag-country-' + data.flag + ' mr-2 ml-n1"></span>' + escape(data.text) + '</div>';
  			},
  			item: function (data, escape) {
  				return '<div class="d-flex align-items-center"><span class="flag flag-country-' + data.flag + ' mr-2 ml-n1"></span>' + escape(data.text) + '</div>';
  			}
  		}
  	});
  });
</script>
<script>
  (function () {
  	/**
  	 * Input mask
  	 */
  	var maskElementList = [].slice.call(document.querySelectorAll('[data-mask]'))
  	maskElementList.map(function (maskEl) {
  		return new IMask(maskEl, {
  			mask: maskEl.dataset.mask,
  			lazy: maskEl.dataset['mask-visible'] === 'true'
  		})
  	});
  })();
</script>
<script>
  (function () {
  	const elements = document.querySelectorAll('[data-toggle="autosize"]');
  	if (elements.length) {
  		elements.forEach(function (element) {
  			autosize(element);
  		});
  	}
  })();
</script>
<script>
  document.body.style.display = "block"
</script>
<script>
function add_input(){
	var row = $('#row_num').val();
	row++;
	$('#row_num').val(row);
	var html = '';

	html += '<br>';

	html += '<div class="row"  id="row1_'+row+'">';
		html += '<div class="col-md-2">';
			html += '<label class="control-label">ประเภทผลิตภัณฑ์</label>';
		html += '</div>';
		html += '<div class="col-md-3">';
			html += '<select name="PRODUCT_TYPE[]" id="PRODUCT_TYPE" class="form-control select2 select2-hidden-accessible" placeholder="เลือกประเภทผลิตภัณฑ์" tabindex="-1" aria-hidden="true">';
				html += '<option value="" disabled="" selected="">เลือกประเภทผลิตภัณฑ์</option>';		
					<?php 
					$sql_pro_type = "SELECT PRODUCT_TYPE_ID, PRODUCT_TYPE_NAME FROM M_SETUP_PRODUCT_TYPE WHERE ACTIVE_STATUS = 1 AND DELETE_FLAG = 0";
					$q_pro_type = db::query($sql_pro_type);
					while($r_pro_type = db::fetch_array($q_pro_type)){
						echo "html +='<option value=".$r_pro_type['PRODUCT_TYPE_ID']." >".$r_pro_type['PRODUCT_TYPE_NAME']."</option>';";
					}						
					?>
			html += '</select>';
		html += '</div>';	
		html += '<div class="col-md-2">';
			html += '<label class="control-label">สต็อกเมื่อวาน</label>';
		html += '</div>';
		html += '<div class="col-md-3">';
			html += '<input type="text" class="form-control" id="STOCK_OLD_'+row+'" name="STOCK_OLD[]" value="" onblur="sum_order('+row+');">';
		html += '</div>';		
		html += '<div class="col-md-1">';
			html += '<button type="button" onclick="del_row('+row+');" class="btn btn-danger btn-sm" title=""><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-md" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="5" y1="12" x2="19" y2="12"></line></svg></button>';
		html += '</div>';
	html += '</div><br>';
	html += '<div class="row" id="row2_'+row+'">';
		html += '<div class="col-md-2">';
			html += '<label class="control-label">ออเดอร์ค้างที่ต้องส่งวันนี้</label>';
		html += '</div>';
		html += '<div class="col-md-3">';
			html += '<input type="text" class="form-control" id="ORDER_OLD_'+row+'" name="ORDER_OLD[]" value="" onblur="sum_order('+row+');">';
		html += '</div>';
		html += '<div class="col-md-2">';
			html += '<label class="control-label">ออเดอร์วันนี้</label>';
		html += '</div>';
		html += '<div class="col-md-3">';
			html += '<input type="text" class="form-control" id="ORDER_NEW_'+row+'" name="ORDER_NEW[]" value="" onblur="sum_order('+row+');" >';
		html += '</div>';
	html += '</div><br>';
	html += '<div class="row" id="row3_'+row+'">';
		html += '<div class="col-md-2">';
			html += '<label class="control-label">ออเดอร์ค้างที่ต้องส่งวันนี้</label>';
		html += '</div>';
		html += '<div class="col-md-3">';
			html += '<input type="text" class="form-control" id="ORDER_SUM_'+row+'" name="ORDER_SUM[]" value=""readonly>';
		html += '</div>';
	html += '</div>'; 

	$('#data').append(html);

}
 function del_row(id_tr){
	 // alert(ele);
	$('#row1_'+id_tr).remove();
	$('#row2_'+id_tr).remove();
 }
 
function sum_order(row){
	var STOCK_OLD = $('#STOCK_OLD_'+row).val();
	var ORDER_OLD = $('#ORDER_OLD_'+row).val();
	var ORDER_NEW = $('#ORDER_NEW_'+row).val();
	var SUM = eval('('+ORDER_NEW+'+'+ORDER_OLD+')-'+STOCK_OLD);
	// alert(SUM);
	if(SUM<=0){
		$('#ORDER_SUM_'+row).val(0); 
	}else{
		$('#ORDER_SUM_'+row).val(SUM); 
	}
}

</script>
  </body>
</html>