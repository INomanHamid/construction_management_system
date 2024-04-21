<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('en/include/head');
$this->load->view('en/include/header');

?>

<body class="no-skin">

	<div class="main-container ace-save-state" id="main-container">

		<?php $this->load->view('en/include/sidebar');
		?>

		<div class="main-content">
			<div class="main-content-inner">
				<div class="breadcrumbs ace-save-state" id="breadcrumbs" style="background-color: #5baa4f; color: white; font-weight: bold;">
					<ul class="breadcrumb">
						<li>
							<i class="ace-icon fa fa-home home-icon"></i>
							<a href="<?php echo SURL . "admin"; ?>" style="color: white;">Home</a>
						</li>

						<li>
							<a href="<?php echo SURL . "Activities_progress"; ?>" style="color: white;">Activities Progress List <?php if ($arabic_check == 'Yes') { ?> (قائمة الفئات) <?php } ?> </a>
						</li>
						<li class="active" style="color: white;">Add Activity Progress <?php if ($arabic_check == 'Yes') { ?> (إضافة فئة) <?php } ?></li>
					</ul><!-- /.breadcrumb -->

					<div class="nav-search" id="nav-search">
						<form class="form-search">
							<span class="input-icon">
								<input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
								<i class="ace-icon fa fa-search nav-search-icon"></i>
							</span>
						</form>
					</div><!-- /.nav-search -->
				</div>

				<div class="page-content">
					<div class="ace-settings-container" id="ace-settings-container">
						<div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
							<i class="ace-icon fa fa-cog bigger-130"></i>
						</div>

						<div class="ace-settings-box clearfix" id="ace-settings-box">
							<div class="pull-left width-50">
								<div class="ace-settings-item">
									<div class="pull-left">
										<select id="skin-colorpicker" class="hide">
											<option data-skin="no-skin" value="#438EB9">#438EB9</option>
											<option data-skin="skin-1" value="#222A2D">#222A2D</option>
											<option data-skin="skin-2" value="#C6487E">#C6487E</option>
											<option data-skin="skin-3" value="#D0D0D0">#D0D0D0</option>
										</select>
									</div>
									<span>&nbsp; Choose Skin</span>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-navbar" autocomplete="off" />
									<label class="lbl" for="ace-settings-navbar"> Fixed Navbar</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-sidebar" autocomplete="off" />
									<label class="lbl" for="ace-settings-sidebar"> Fixed Sidebar</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-breadcrumbs" autocomplete="off" />
									<label class="lbl" for="ace-settings-breadcrumbs"> Fixed Breadcrumbs</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-rtl" autocomplete="off" />
									<label class="lbl" for="ace-settings-rtl"> Right To Left (rtl)</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2 ace-save-state" id="ace-settings-add-container" autocomplete="off" />
									<label class="lbl" for="ace-settings-add-container">
										Inside
										<b>.container</b>
									</label>
								</div>
							</div><!-- /.pull-left -->

							<div class="pull-left width-50">
								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-hover" autocomplete="off" />
									<label class="lbl" for="ace-settings-hover"> Submenu on Hover</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-compact" autocomplete="off" />
									<label class="lbl" for="ace-settings-compact"> Compact Sidebar</label>
								</div>

								<div class="ace-settings-item">
									<input type="checkbox" class="ace ace-checkbox-2" id="ace-settings-highlight" autocomplete="off" />
									<label class="lbl" for="ace-settings-highlight"> Alt. Active Item</label>
								</div>
							</div><!-- /.pull-left -->
						</div><!-- /.ace-settings-box -->
					</div><!-- /.ace-settings-container -->

					<div class="page-header">
						<h1>
							Cons
							<small>
								<i class="ace-icon fa fa-angle-double-right"></i>
								Add Activity Progress <?php if ($arabic_check == 'Yes') { ?> (إضافة فئة) <?php } ?>
							</small>
						</h1>
					</div><!-- /.page-header -->

					<div class="row">
						<div class="col-xs-12">
							<!-- PAGE CONTENT BEGINS -->

							<?php
							if ($this->session->flashdata('err_message')) {
							?>

								<div class="alert alert-danger">
									<button type="button" class="close" data-dismiss="alert">
										<i class="ace-icon fa fa-times"></i>
									</button>

									<strong>
										<i class="ace-icon fa fa-times"></i>
										Oh snap!
									</strong>

									<?php echo $this->session->flashdata('err_message'); ?>
									<br>
								</div>

							<?php
							}   ?>



							<form id="formID" class="form-horizontal" role="form" method="post" action="<?php echo SURL . "Activities_progress/add" ?>" enctype="multipart/form-data">
								<?php if ($arabic_check == 'Yes') { ?>
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Language </label>

										<div class="col-sm-3" style="margin-top: 8px;">
											<input type="radio" onclick="english_lang()" checked="checked" name="lang" id="english">
											English
											<input style="margin-left: 2%;" type="radio" onclick="urdu_lang()" name="lang" id="urdu">Arabic
										</div>
									</div>
								<?php } ?>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Select Project</label> <?php if ($arabic_check == 'Yes') { ?> (اسم الفصل)<?php } ?></label>


										<div class="col-sm-3">

											<select class="chosen-select form-control" name="project" onchange="fetchSubProjects()" id="project" data-placeholder="Choose Project..." required="">
												<!-- <option value="">Select Project</option> -->
												<?php foreach ($project_list as $key => $data) { ?>

													<option <?php
															if ($record['project'] == $data['projectid']) {
																echo "selected";
															}
															?> value="<?php echo $data['projectid']; ?>"><?php echo ucwords($data['title']); ?></option>

												<?php } ?>
											</select>

										</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label no-padding-right" for="form-field-1">Sub Project</label> <?php if ($arabic_check == 'Yes') { ?> (اسم الفصل)<?php } ?></label>


										<div class="col-sm-3">

											<select class="chosen-select form-control" onchange="get_project_date()" required name="sub_project" id="sub_project">

											</select>

										</div>
								</div>
								<div id="actvity_data"></div>

								<br>
								<div>
									<?php
									if (!empty($edit_list)) {
									?>
										<style type="text/css">
											#data_table1 {
												display: block;
											}

											#submithide {
												display: block;
											}
										</style>
									<?php
									}
									?>
									<table width="100%" class="table table-striped stock_fields" id="data_table1">
										<thead>
											<tr class="exist_rec_sb_high_main">
												<td style="background-color: #DAA03DFF; color: white;font-weight: bolder;">Sr </td>
												<td align="center" style="background-color: #DAA03DFF; color: white; font-weight: bolder;">Activity</td>
												<td align="center" style="background-color: #DAA03DFF; color: white; font-weight: bolder;">Estimated Start Date</td>
												<td align="center" style="background-color: #DAA03DFF; color: white; font-weight: bolder;">Estimated End Date</td>
												<td align="center" style="background-color: #4fb494; color: white; font-weight: bolder;">Actual Start Date</td>
												<td align="center" style="background-color: #4fb494; color: white; font-weight: bolder;">Actual End Date</td>
												<td align="center" style="background-color: #298df1; color: white; font-weight: bolder;">Status</td>
												<td align="center" style="background-color: #298df1; color: white; font-weight: bolder;">Actual&nbsp;Status</td>
											</tr>
										</thead>

										<tbody id="data_table2">

										</tbody>

									</table>
									<div class="form-group">
										<!-- <div class="form-actions center">
											<button class="btn btn-info btn-xs" id="submitbtn" style="margin-left: -20%;">
												<i class="ace-icon fa fa-check bigger-110"></i>
												Submit<?php if ($arabic_check == 'Yes') { ?>(إرسال)<?php } ?>
											</button>

										</div> -->
										<div class="form-actions center" id="chart_btn">


										</div>

										<input type="hidden" id="project_start" value="" />
										<input type="hidden" id="project_end" value="" />

									</div>

							</form>
						</div>
						<!-- PAGE CONTENT ENDS -->
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.page-content -->
		</div>
	</div><!-- /.main-content -->

	</div><!-- /.main-container -->

	<?php
	$this->load->view('en/include/footer');
	?>

	<?php
	$this->load->view('en/include/js');
	?>
	<script>
		$(document).ready(function() {

			$("#submitbtn").click(function(event) {

				event.preventDefault();


				$(".from_date, .to_date").removeAttr("disabled");


				$(this).closest("form").submit();
			});
		});
	</script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.date-picker').datepicker({
				format: "yyyy-mm-dd"

			});
		});
	</script>

	<script type="text/javascript">
		fetchSubProjects();

		function fetchSubProjects() {
			var project = $("#project").val();
			var sub_project_edit = $("#sub_project_edit").val();
			$.ajax({
				data: {
					project: project,
					sub_project_edit: sub_project_edit,
				},
				type: "POST",
				url: "<?php echo SURL ?>Activities_progress/get_subproject",
				cache: false,
				dataType: "html",
				success: function(response) {
					$("#sub_project").html(response);
					jQuery(function($) {
						$('#sub_project').trigger("chosen:updated");
						var $mySelect = $('#project');
						$mySelect.chosen();
						$mySelect.trigger('chosen:activate');
					});
					project_activity_progress()
					get_project_date()
				}
			});
		}

		function project_activity_progress() {
			var project = $("#project").val();
			var sub_project = $("#sub_project").val();

			$.ajax({
				data: {
					project: project,
					sub_project: sub_project,
				},
				type: "POST",
				url: "<?php echo SURL ?>Activities_progress/get_activity_progress",
				cache: false,
				dataType: "html",
				success: function(html) {

					$("#data_table2").html(html);

					var startDate = $("#project_start").val();
					var endDate = $("#project_end").val();

					flatpickr('.date-picker', {
						dateFormat: "Y-m-d",
						minDate: startDate,
						maxDate: endDate,
					});
					$('.date-picker').addClass('flat-picker');
					$(".flat-picker").removeClass('date-picker');
				}
			});
		}


		function get_project_date() {
			var project = $("#project").val();
			var sub_project = $("#sub_project").val();
			$.ajax({
				data: {
					project: project,
					sub_project: sub_project,
				},
				type: "POST",
				url: "<?php echo SURL ?>Activities_progress/get_project_date",
				cache: false,
				dataType: "html",
				success: function(html) {
					chart_btn()
					var response = html.split('|');
					var startDate = response[0];
					var endDate = response[1];

					flatpickr('.date-picker', {
						dateFormat: "Y-m-d",
						minDate: startDate,
						maxDate: endDate,
					});
					$('.date-picker').addClass('flat-picker');
					$(".flat-picker").removeClass('date-picker');

					$("#project_start").val(startDate);
					$("#project_end").val(endDate);
					project_activity_progress()
				}
			});
		}

		function chart_btn() {
			var project = $("#project").val();
			var sub_project = $("#sub_project").val();
			$.ajax({
				data: {
					project: project,
					sub_project: sub_project,
				},
				type: "POST",
				url: "<?php echo SURL ?>Activities_progress/chart_btn",
				cache: false,
				dataType: "html",
				success: function(html) {
					$("#chart_btn").html(html);

				}
			});
		}
	</script>
	<script>
		function updateStatus(progress, status) {

			var actual_end_date = $("#actual_end_date_" + progress).val();
			var end_date = $("#end_date_" + progress).val();
			$.ajax({
				url: "<?php echo SURL ?>Activities_progress/change_status",
				type: 'POST',
				data: {
					progress: progress,
					actual_end_date: actual_end_date,
					end_date: end_date,
					status: status,
				},
				success: function(response) {
					if (response === 'success') {
						project_activity_progress()
					}
				},
				error: function(xhr, status, error) {

				}
			});
		}
	</script>



	<?php $this->load->view('en/include/paymentreceipt_js.php'); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.js" type="text/javascript"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
	<script src="<?php echo SURL ?>assets/js/jquery.UrduEditor.js" type="text/javascript"></script>
	<script type="text/javascript">
		var test_final = jQuery.noConflict($);

		$(document).ready(function($) {

			jQuery(".urdu_class").each(function(index) {

				test_final(this).UrduEditor();
				setEnglish($(this));
				jQuery(this).removeAttr('dir');

			});
		});

		function english_lang() {

			jQuery(".urdu_class").each(function(index) {

				jQuery(this).removeAttr('dir');
				setEnglish(jQuery(this));

			});
		}

		function urdu_lang() {
			//alert('asd');
			jQuery(".urdu_class").each(function(index) {

				jQuery(this).attr("dir", "rtl");

				setUrdu(jQuery(this));

			});

		}
	</script>

</body>

</html>