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

$sql = "SELECT * FROM M_SALE_ADVERTISING WHERE SA_ID = '".$WFR."'";
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
					รายการบัญชี
                </h2>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <div class="card-title">บันทึกยอดการขายและค่าใช้จ่ายโฆษณา</div>
                </div>
                <div class="card-body">
                  <div class="row">
					<div class="col-md-12">
						<form action="../save/SalesAndAdvertising_Save.php" method="POST">
							<input type="hidden" id="proc" name="proc" value="<?php echo $proc;?>">
							<input type="hidden" id="WFR" name="WFR" value="<?php echo $WFR;?>">
							 <div class="row">
								<div class="col-md-2">
									<label class="control-label">วันที่ลงข้อมูล</label>
								</div>
								<div class="col-md-3">
									<div class="input-icon">
										<input id="DATE" name="DATE" type="date" value="" class="form-control" placeholder="Select a date" />
										<span class="input-icon-addon"><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z"/><rect x="4" y="5" width="16" height="16" rx="2" /><line x1="16" y1="3" x2="16" y2="7" /><line x1="8" y1="3" x2="8" y2="7" /><line x1="4" y1="11" x2="20" y2="11" /><line x1="11" y1="15" x2="12" y2="15" /><line x1="12" y1="15" x2="12" y2="18" /></svg>
										</span>
									</div>
								</div>					
								<div class="col-md-2">
									<label class="control-label">เวลาที่ลงข้อมูล</label>
								</div>
								<div class="col-md-3">
									<select name="TIME" id="TIME" class="form-select" placeholder="เลือกอำเภอ">
										<option value="" disabled="" selected="">เลือกเวลา</option>
										<?php 
										foreach($arr_time as $val){
											echo "<option value=".$val." ".($val==$rec['TIME']?"selected":"").">".$val."</option>";
										}
										
										?>

									</select>
								</div> <!-- /controls -->	
							</div><br>
							 <div class="row">
								<div class="col-md-2">
									<label class="control-label">ยอดขาย</label>
								</div>
								<div class="col-md-3">
									<input type="number" class="form-control" id="SALE" name="SALE" value="<?php echo $rec['SALE'];?>" >
								</div>					
								<div class="col-md-2">
									<label class="control-label">ยอดใช้จ่ายโฆษณา</label>
								</div>
								<div class="col-md-3">
									<input type="number" class="form-control" id="ADVERTISE" name="ADVERTISE" value="<?php echo $rec['ADVERTISE'];?>">
								</div> <!-- /controls -->	
							</div><br> 
							 <div class="row">
								<div class="col-md-12" align="center">
									<button type="submit" class="btn btn-primary btn-sm" title="">บันทึก</button>
									<a type="button" href="SalesAndAdvertising_Disp.php" class="btn btn-danger btn-sm" title="">ยกเลิก</a>
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
  </body>
</html>