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

@$WFR = $_GET['WFR'];
@$proc = ($WFR?"edit":"add");
 
$sql = "SELECT * FROM M_SETUP_BREAD_RECIPE_MAIN WHERE BREAD_RECIPE_ID = '".$WFR."'";
$query = db::query($sql);
$num = db::num_rows($query);
$rec = db::fetch_array($query);

if($rec['ACTIVE_STATUS']==1){
	$act_1 = "checked";
	$act_2 = "";
}else{
	$act_1 = "";
	$act_2 = "checked";	
}
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
					ตั้งค่า
                </h2> 
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <div class="card-title">สูตรขนมปัง</div>
                </div>
                <div class="card-body">
                  <div class="row">
					<div class="col-md-12">
						<form action="../save/setup_bread_recipe_save.php" method="POST">
							<input type="hidden" id="proc" name="proc" value="<?php echo $proc;?>">
							<input type="hidden" id="WFR" name="WFR" value="<?php echo $WFR;?>">
							<div class="row">
								<div class="col-md-1"></div>
								<div class="col-md-2">
									<label class="control-label">ผลิตภัณฑ์</label>
								</div>
								<div class="col-md-3">
									<select name="PRODUCT_TYPE_ID" id="PRODUCT_TYPE_ID" class="form-control select2 select2-hidden-accessible" placeholder="เลือกประเภทผลิตภัณฑ์" tabindex="-1" aria-hidden="true">
										<option value="" disabled="" selected="">เลือกผลิตภัณฑ์</option>
										<?php 
										$query_product = db::query("SELECT PRODUCT_TYPE_ID, PRODUCT_TYPE_NAME FROM M_SETUP_PRODUCT_TYPE WHERE ACTIVE_STATUS = 1 AND DELETE_FLAG = 0");
										while($r_roduct = db::fetch_array($query_product)){
											echo "<option value=".$r_roduct['PRODUCT_TYPE_ID']." ".($r_roduct['PRODUCT_TYPE_ID']==$rec['PRODUCT_TYPE_ID']?"selected":"").">".$r_roduct['PRODUCT_TYPE_NAME']."</option>";
										}						
										?>
									</select>
								</div> <!-- /controls -->		
								<div class="col-md-1"></div>	
								<div class="col-md-2">
									<label class="control-label">ปริมาณสูตร</label>
								</div>
								<div class="col-md-3">
									<input type="text" class="form-control" id="BREAD_COUNT" name="BREAD_COUNT" value="<?php echo $rec['BREAD_COUNT'];?>" >
								</div>	
							</div><br> 
							<div class="row">
								<div class="col-md-1"></div>										
								<div class="col-md-2">
									<label class="control-label">สถานะการใช้งาน</label>
								</div>	
								<div class="col-md-3">
									<input type="radio" id="ACTIVE_STATUS_1" name="ACTIVE_STATUS" value="1" <?php echo @$act_1;?>>&nbsp;ใช้งาน&nbsp;&nbsp;&nbsp;
									<input type="radio" id="ACTIVE_STATUS_2" name="ACTIVE_STATUS" value="0" <?php echo @$act_2;?>>&nbsp;ไม่ใช้งาน
								</div> <!-- /controls -->	
							</div><br>
							<div class="row">
								<div class="col-md-12" align="right">
									<a class="btn btn-primary active btn-sm" href="#!" data-toggle="modal" data-target="#modal-report"><i class="icon-add"></i> จัดการวัตถุดิบ</a>
								</div>
							</div>
							<hr></hr>
							<div class="row">
							  <div class="col-lg-12">
								<div class="table-responsive">
									<table cellspacing="0" id="tbl" class="table table-vcenter table-mobile-md card-table">
										<thead">
											<tr>
												<th style="width:7%;" class="text-center">
													ลำดับ
												</th>
												<th style="width:15%;" class="text-center">ชื่อวัตถุดิบ</th>
												<th style="width:15%;" class="text-center">ประเภท</th>
												<th style="width:15%;" class="text-center">ปริมาณในคลัง</th>
												<th style="width:10%;" class="text-center">ปริมาณที่ใช้</th>
												<th style="width:10%;" class="text-center"></th>
											</tr>
										</thead>
										<tbody id="show_inventory">
										<?php 
										$sql_detail = "SELECT A.*,B.*,C.CATEGORY_NAME 
														FROM 
															M_SETUP_BREAD_RECIPE_DETAIL A
															INNER JOIN M_INVENTORY B ON B.INVENTORY_ID = A.INVENTORY_ID
															INNER JOIN M_CATEGORY C ON C.CATEGORY_ID = B.CATEGORY_ID
														WHERE 
															A.BREAD_RECIPE_ID = '".$rec['BREAD_RECIPE_ID']."'";
										$query_detail = db::query($sql_detail);
										$num_detail = db::num_rows($query_detail);
										if($num_detail > 0){ 
											$i=1;
											while($r_detail = db::fetch_array($query_detail)){
											
												$del = '<a href="#!" class="btn btn-danger btn-sm" onclick="del_row('.$i.');" title="" ><i class="icofont icofont-trash" ></i>ลบ</a>';
												echo "<tr id=".$i.">";
												echo "<td class='text-center'>".$i."</td>";
												echo "<td class='text-center'>".@$r_detail['INVENTORY_NAME']."</td>";
												echo "<td class='text-center'>".@$r_detail['CATEGORY_NAME']."</td>";
												echo "<td class='text-center'>".@$r_detail['STOCK_QUANTITY']."/".@$r_detail['UNIT_TYPE']."</td>";
												echo "<td class='text-center'>";
												echo "<input type='hidden' class='form-control' id='INVENTORY_ID' name='INVENTORY_ID[]' value=".$r_detail['INVENTORY_ID']." >";
												echo "<input type='hidden' class='form-control' id='INVENTORY_UNIT' name='INVENTORY_UNIT[]' value=".$r_detail['INVENTORY_UNIT']." >";
												echo "<input type='text' class='form-control' id='INVENTORY_QUANTITY' name='INVENTORY_QUANTITY[]' value=".$r_detail['INVENTORY_QUANTITY']." ></td>";
												echo "</td>";
												echo "<td class='text-center'>".$del."</td>";
												echo "</tr>";
											$i++;
											}	
										}?>
										</tbody>
									</table>
								</div>
							  </div>
							</div>
							 <div class="row">
								<div class="col-md-12" align="center">
									<button type="submit" class="btn btn-primary btn-sm" title="">บันทึก</button>
									<a type="button" href="setup_bread_recipe_disp.php" class="btn btn-danger btn-sm" title="">ยกเลิก</a>
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
<div class="modal modal-blur fade" id="modal-report" tabindex="-1" role="dialog" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">New report</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"></path><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <h2>ค้นหา</h2>
            </div>
            <div class="row">
              <div class="col-lg-8">
                <div class="mb-3">
                  <label class="form-label">ชื่อวิตถุดิบ</label>
                    <input type="text" class="form-control pl-0" value="">
                </div>
              </div>
              <div class="col-lg-4">
                <div class="mb-3">
                  <label class="form-label">ประเภทวัตถุดิบ</label>
                  <select class="form-select">
                    <option value="1" disabled="" selected="">เลือกประเภทวัตถุดิบ</option>
                    <option value="2">Public</option>
                    <option value="3">Hidden</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-12">
				<div class="table-responsive">
					<table cellspacing="0" id="tbl" class="table table-vcenter table-mobile-md card-table">
						<thead">
							<tr>
								<th style="width:7%;" class="text-center">
									<input class="form-check-input" type="checkbox" >
								</th>
								<th style="width:15%;" class="text-center">ชื่อวัตถุดิบ</th>
								<th style="width:15%;" class="text-center">ประเภท</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$query_inventory = db::query("SELECT * 
														FROM 
															M_INVENTORY B
															INNER JOIN M_CATEGORY C ON C.CATEGORY_ID = B.CATEGORY_ID");
						$num_model = db::num_rows($query_inventory);
						if($num_model > 0){ 
							// $i=1;
							while($rec_inven = db::fetch_array($query_inventory)){
								$PK = $rec_inven['INVENTORY_ID'];
								$NAME = $rec_inven['INVENTORY_NAME'];
								$CATEG = $rec_inven['CATEGORY_NAME'];
								$U_PRICE = $rec_inven['STOCK_QUANTITY'];
								$U_TYPE = $rec_inven['UNIT_TYPE'];
								echo "<tr>";
								echo "<td class='text-center'><input class='form-check-input' type='checkbox' name='data[".$PK."]' value=".$PK.",".$NAME.",".$CATEG.",".$U_PRICE.",".$U_TYPE."></td>";
								echo "<td class='text-center'>".@$rec_inven['INVENTORY_NAME']."</td>";
								echo "<td class='text-center'>".@$rec_inven['CATEGORY_ID']."</td>";
								echo "</tr>";
							// $i++;
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
          <div class="modal-footer">
            <a href="#" class="btn btn-link link-secondary" data-dismiss="modal">
              Cancel
            </a>
            <a href="#!" onclick="send_data();" class="btn btn-primary ml-auto" data-dismiss="modal">
				ยืนยัน
            </a>
          </div>
        </div>
      </div>
    </div>
<?php include "../include/footer.php";?>
	</div>
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
 function send_data(){
	 var html = '';
	 var i = 1; 
	$("input:checkbox[name^=data]:checked").each(function(){
		var test_data = $(this).val();
		test_data = test_data.split(",");
		html += '<tr id="'+i+'">';
		html += '<td class="text-center">'+i+'</td>';
		html += '<td class="text-center">'+test_data[1]+'</td>';
		html += '<td class="text-center">'+test_data[2]+'</td>';
		html += '<td class="text-center">'+test_data[3]+'/'+test_data[4]+'</td>';
		html += '<td class="text-center">';
		html += '<input type="hidden" class="form-control" id="INVENTORY_ID" name="INVENTORY_ID[]" value="'+test_data[0]+'" >';
		html += '<input type="hidden" class="form-control" id="INVENTORY_UNIT" name="INVENTORY_UNIT[]" value="'+test_data[4]+'" >';
		html += '<input type="text" class="form-control" id="INVENTORY_QUANTITY" name="INVENTORY_QUANTITY[]" value="" ></td>';
		html += '<td class="text-center"><a href="#!" class="btn btn-danger btn-sm" onclick="del_row('+i+');" title="" ><i class="icofont icofont-trash" ></i>ลบ</a></td>';
		html += '</tr>';
		$('#show_inventory').append(html);
		console.log(test_data);
	i++;
	}); 
 }
 
 function del_row(id_tr){
	 // alert(ele);
	$('#'+id_tr).remove();
 }
</script>
  </body>
</html>