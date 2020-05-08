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

$T = $_GET['T'];
if($T==1){
	$txt = "ชนิดของแป้ง";
	$colspan = "9";
}else{
	$txt = "ชนิดขนมปัง";
	$colspan = "7";
}
$sql = "SELECT * FROM M_MANUFACTURING WHERE TYPE = '".$T."'";
$query = db::query($sql);
$num = db::num_rows($query);
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
					ติดตามการผลิต
                </h2>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <div class="card-title">รายการนวด</div>
                </div>
                <div class="card-body">
                  <div class="row">
					<form id="frm_disp" action="../save/manufacturing_save.php" method="POST">
						<input type="hidden" id="proc" name="proc" value="">
						<input type="hidden" id="WFR" name="WFR" value="">
						<input type="hidden" id="TYPE" name="TYPE" value="">
					</form>
					<div class="col-md-12">
						<div class="table-responsive">
							<div class="row">
								<div class="col-md-12" align="right">
									<a class="btn btn-primary active btn-sm" href="manufacturing_form.php?T=<?php echo $T;?>" role="button" title=""><i class="icon-add"></i> เพิ่มข้อมูล</a>
								</div>
							</div>
							<table cellspacing="0" id="tbl" class="table table-vcenter table-mobile-md card-table">
								<thead">
									<tr>
										<th style="width:5%;" class="text-center">ลำดับ</th>
										<th style="width:15%;" class="text-center">วันที่</th>
										<th style="width:10%;" class="text-center">รอบ</th>
										<th style="width:15%;" class="text-center"><?php echo $txt;?></th>
									<?php if($T==1){?>
										<th style="width:10%;" class="text-center">ปริมาณ</th>
										<th style="width:10%;" class="text-center">เวลาที่ทำ</th>
										<th style="width:10%;" class="text-center">เวลาสิ้นสุด</th>
									<?php }else{?>
										<th style="width:10%;" class="text-center">จำนวนที่ผลิตได้</th>
									<?php }?>	
										<th style="width:10%;" class="text-center">สูญเสีย</th>
										<th style="width:15%;" class="text-center">จัดการ</th>
									</tr>
								</thead>
								<tbody>
								<?php 
								if($num > 0){
									
									$i=1;
									while($rec = db::fetch_array($query)){
										$edit = '<a href="manufacturing_form.php?T='.$rec['TYPE'].'&WFR='.$rec['FACTURING_ID'].'" class="btn btn-warning btn-sm" title=""><i class="icofont icofont-tick-mark"></i>แก้ไข</a> &nbsp;'; 
										$del = '<a href="#!" class="btn btn-danger btn-sm" title="" onclick=delete_wf_main('.$rec['FACTURING_ID'].','.$rec['TYPE'].')><i class="icofont icofont-trash"></i>ลบ</a>';
										echo "<tr>";
										echo "<td class='text-center'>".$i."</td>";
										echo "<td class='text-center'>".conv_date($rec['DATE'])."</td>";
										echo "<td class='text-center'>".$rec['AROUND']."</td>";
										echo "<td class='text-center'>".$rec['KINDS']."</td>";
									if($T==1){	
										echo "<td class='text-center'>".$rec['QUANTITY']."</td>";
										echo "<td class='text-center'>".$rec['STIME']."</td>";
										echo "<td class='text-center'>".$rec['ETIME']."</td>";
									}else{
										echo "<td class='text-center'>".$rec['PRODUCE']."</td>";
									}
										echo "<td class='text-center'>".$rec['WASTE']."</td>";
										echo "<td class='text-center'>".$edit.$del."</td>";
										echo "</tr>";
									$i++;
									}
									
								}else{
									echo "<tr>";
									echo "<td colspan='".$colspan."' class='text-center'> ไม่มีข้อมูล </td>";
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
	function delete_wf_main(wfr,type){

		if(wfr != ''){
			if(confirm('ต้องการลบข้อมูลใช่หรือไม่')) {

				$('#proc').val('del');
				$('#WFR').val(wfr);
				$('#TYPE').val(type);
				$('#frm_disp').submit();             
			}  
			return false;
		}
	}
	</script>
	
  </body>
</html>