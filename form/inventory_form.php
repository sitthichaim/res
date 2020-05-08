<html lang="en">
<?php 
include "../include/include.php";


@$WFR = $_GET['WFR'];
@$proc = ($WFR?"edit":"add");


$sql = "SELECT * FROM M_INVENTORY WHERE INVENTORY_ID = '".$WFR."'";
$query = db::query($sql);
$num = db::num_rows($query);
$rec = db::fetch_array($query);

$arr_time = array('10:00','14:00','19:00','24:00');
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
					คลังวัตถุดิบ
                </h2>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <div class="card-title">คลังวัตถุดิบ</div>
                </div>
                <div class="card-body">
                  <div class="row">
					<div class="col-md-12">
						<form action="../save/inventory_save.php" method="POST">
							<input type="hidden" id="proc" name="proc" value="<?php echo $proc;?>">
							<input type="hidden" id="WFR" name="WFR" value="<?php echo $WFR;?>">
							<div class="row">
								<div class="col-md-2">
									<label class="control-label">วันที่ลงข้อมูล</label>
								</div>
								<div class="col-md-3">
									<div class="input-icon">
										<input id="INVENTORY_DATE" name="INVENTORY_DATE" type="date" value="<?php echo date('Y-m-d');?>" class="form-control" placeholder="Select a date" readonly/>
										<span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
										</span>
									</div>
								</div>
							</div><br>
							<div class="row">
								<div class="col-md-2">
									<label class="control-label">ผู้ผลิต</label>
								</div>
								<div class="col-md-3">
									<input type="text" class="form-control" id="SUPPLIER_NAME" name="SUPPLIER_NAME" value="<?php echo $rec['SUPPLIER_NAME'];?>" <?php echo $proc=='add'?'':'readonly';?>>
								</div>
							</div><br>
							<div class="row">
								<div class="col-md-2">
									<label class="control-label">ชื่อวัตถุดิบ</label>
								</div>
								<div class="col-md-3">
									<input type="text" class="form-control" id="INVENTORY_NAME" name="INVENTORY_NAME" value="<?php echo $rec['INVENTORY_NAME'];?>"  <?php echo $proc=='add'?'':'readonly';?>>
								</div>					
								<div class="col-md-2">
									<label class="control-label">หมวดหมู่</label>
								</div>
								<div class="col-md-3">
									<select name="CATEGORY_ID" id="CATEGORY_ID" class="form-control select2 select2-hidden-accessible" placeholder="เลือกอำเภอ" tabindex="-1" aria-hidden="true" <?php echo $proc=='add'?'':'readonly';?>>
										<option value="" disabled="" selected="">เลือกหมวดหมู่</option>
										<?php
										$query_c = db::query("SELECT * FROM M_CATEGORY");

										while($data = db::fetch_array($query_c)){

											echo "<option value=".$data['CATEGORY_ID']." ".($data['CATEGORY_ID']==$rec['CATEGORY_ID']?"selected":"").">".$data['CATEGORY_NAME']."</option>";

										}
										?>
									</select>
								</div> <!-- /controls -->	
							</div><br> 
							<div class="row">
								<div class="col-md-2">
									<label class="control-label">ราคาต่อหน่วย</label>
								</div>
								<div class="col-md-3">
									<input type="number" class="form-control" id="UNIT_PRICE" name="UNIT_PRICE" value="<?php echo $rec['UNIT_PRICE'];?>" >
								</div>
								<div class="col-md-2">
									<label class="control-label">หน่วย</label>
								</div>
								<div class="col-md-3">
									<select name="UNIT_TYPE" id="UNIT_TYPE" class="form-control select2 select2-hidden-accessible" placeholder="เลือกอำเภอ" tabindex="-1" aria-hidden="true" <?php echo $proc=='add'?'':'readonly';?>>
										<option value="" disabled="" selected="">เลือกหน่วย</option>

										<?php

										$query_c = db::query("SELECT * FROM M_UNIT_TYPE");

										while($data = db::fetch_array($query_c)){

											echo "<option value=".$data['UNIT_TYPE_NAME']." ".($data['UNIT_TYPE_NAME']==$rec['UNIT_TYPE']?"selected":"").">".$data['UNIT_TYPE_NAME']."</option>";

										}
										// ?>
									</select>
								</div> <!-- /controls -->	
							</div><br>
							<div class="row">
								<div class="col-md-2">
									<label class="control-label">จำนวน</label>
								</div>
								<div class="col-md-3">
									<input type="number" class="form-control" id="STOCK_QUANTITY" name="STOCK_QUANTITY" value="<?php echo $rec['STOCK_QUANTITY'];?>" <?php echo $proc=='add'?'':'readonly';?>>
								</div>
								<div class="col-md-2">
									<label class="control-label">มูลค่ารวม</label>
								</div>
								<div class="col-md-3">
									<input type="number" class="form-control" id="STOCK_VALUE" name="STOCK_VALUE" value="<?php echo $rec['STOCK_VALUE'];?>" <?php echo $proc=='add'?'':'readonly';?>>
								</div> <!-- /controls -->	
							</div><br>
							<div class="row">
								<div class="col-md-12" align="center">
									<button type="submit" class="btn btn-primary btn-sm" title="">บันทึก</button>
									<a type="button" href="inventory_disp.php" class="btn btn-danger btn-sm" title="">ยกเลิก</a>
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
  	flatpickr(document.getElementById('INVENTORY_DATE'), {
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
  </body>
</html>