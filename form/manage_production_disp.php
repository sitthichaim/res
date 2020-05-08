<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-alpha.7
* @link https://github.com/tabler/tabler
* Copyright 2018-2019 The Tabler Authors
* Copyright 2018-2019 codecalm.net Paweł Kuna
* Licensed under MIT (https://tabler.io/license)
-->
<html lang="en">
<?php 
include "../include/include.php";

$sql = "SELECT * FROM M_MANAGE_PRODUCTION_MAIN WHERE DELETE_FLAG = 0 ";
$query = db::query($sql);
$num = db::num_rows($query);
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
					<form id="frm_disp" action="../save/manage_production_save.php" method="POST">
						<input type="hidden" id="proc" name="proc" value="">
						<input type="hidden" id="WFR" name="WFR" value="">
					</form>
					<div class="col-md-12">
						<div class="table-responsive">
							<div class="row">
								<div class="col-md-12" align="right">
									<a class="btn btn-primary active btn-sm" href="manage_production_form.php" role="button" title=""><i class="icon-add"></i> เพิ่มข้อมูล</a>
								</div>
							</div>
							<table cellspacing="0" id="tbl" class="table table-vcenter table-mobile-md card-table">
								<thead">
									<tr>
										<th style="width:7%;" class="text-center">ลำดับ</th>
											<th style="width:7%;" class="text-center">วันที่</th>
											<th style="width:15%;" class="text-center">ชื่อลูกค้า/ร้านค้า</th>
									<?php 
										$sql_pro_type = "SELECT PRODUCT_TYPE_NAME FROM M_SETUP_PRODUCT_TYPE WHERE ACTIVE_STATUS = 1 AND DELETE_FLAG = 0";
										$q_pro_type = db::query($sql_pro_type);
										while($r_pro_type = db::fetch_array($q_pro_type)){
											echo "<th style='width:' class='text-center'>".$r_pro_type['PRODUCT_TYPE_NAME']."</th>";
										}
									?>
											<th style="width:15%;" class="text-center">รวมทั้งหมด</th>
											<th style="width:15%;" class="text-center">จัดการ</th> 
									</tr>
								</thead>
								<tbody>
								<?php
								// $num = 0;
								if($num > 0){
									
									$i=1;
									while($rec = db::fetch_array($query)){
										$edit = '<a href="manage_production_form.php?WFR='.$rec['MAIN_ID'].'" class="btn btn-warning btn-sm" title=""><i class="icofont icofont-tick-mark"></i>แก้ไข</a> &nbsp;'; 
										$del = '<a href="#!" class="btn btn-danger btn-sm" title="" onclick=delete_wf_main('.$rec['MAIN_ID'].')><i class="icofont icofont-trash"></i>ลบ</a>';
										echo "<tr>";
										echo "<td class='text-center'>".$i."</td>";
										echo "<td class='text-center'>".conv_date($rec['DATE'])."</td>";
										echo "<td class='text-center'>".$rec['BILL_NAME']."</td>";
										
										$sql_pro_type = "SELECT PRODUCT_TYPE_ID FROM M_SETUP_PRODUCT_TYPE WHERE ACTIVE_STATUS = 1 AND DELETE_FLAG = 0";
										$q_pro_type = db::query($sql_pro_type);
										while($r_pro_type = db::fetch_array($q_pro_type)){
											
											$sql_detail = db::query("SELECT ORDER_SUM FROM M_MANAGE_PRODUCTION_DETAIL WHERE MAIN_ID = '".$rec['MAIN_ID']."' AND PRODUCT_TYPE = '".$r_pro_type['PRODUCT_TYPE_ID']."'");
											$num_detail = db::num_rows($sql_detail);
										
											if($num_detail>0){
												while($detail = db::fetch_array($sql_detail)){
													echo "<td class='text-center'>".($detail['ORDER_SUM']>0?$detail['ORDER_SUM']:"-")."</td>"; 
													$sum += $detail['ORDER_SUM'];
												}
											}else{
												echo "<td class='text-center'>-</td>";
											}
										} 
										echo "<td class='text-center'>".$sum."</td>";
										// echo "<td class='text-right'>".number_format($rec['ADVERTISE'],2)."</td>";
										echo "<td class='text-center'>".$edit.$del."</td>";
										echo "</tr>";
									$i++;
									}
									
								}else{
									echo "<tr>";
									echo "<td colspan='6' class='text-center'> ไม่มีข้อมูล </td>";
									echo "</tr>";
								}?>
								</tbody>
							</table>
						</div>
					</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
<?php include "../include/footer.php";?>
    <script>
      var getClosest = function (elem, selector) {
      	for ( ; elem && elem !== document; elem = elem.parentNode ) {
      		if ( elem.matches( selector ) ) return elem;
      	}
      	return null;
      };
      (function () {
      	const elements = document.querySelectorAll('[data-toggle-icon]');
      	if (elements.length) {
      		elements.forEach(function (element) {
      			element.addEventListener('click', function(e){
      				var icon = this.dataset.toggleIcon,
      					svg = this.innerHTML,
      					editor = getClosest(this, '[data-icon-preview]');
      				editor.querySelectorAll('[data-icon-preview-icon]')[0].innerHTML = svg;
      				editor.querySelectorAll('[data-icon-preview-title]')[0].innerText = icon;
      				editor.querySelectorAll('[data-icon-preview-code]')[0].innerText = svg.trim();
      				e.preventDefault();
      				return false;
      			})
      		});
      	}
      })();
    </script>
    <script>
      document.body.style.display = "block"
    </script>
    <script>
		function delete_wf_main(wfr){
			if(wfr != ''){
				if(confirm('ต้องการลบข้อมูลใช่หรือไม่')) {
					$('#proc').val('del');
					$('#WFR').val(wfr);
					$('#frm_disp').submit();             
				}  
				return false;
			}
		}
    </script>
  </body>
</html>