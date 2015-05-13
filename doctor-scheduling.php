<?php 
include "header1.php";
include "config.php";?>  

<meta charset='utf-8' />
<link href='fullcalender_assets/fullcalendar.css' rel='stylesheet' />
<link rel="stylesheet" href="fullcalender_assets/bootstrap.min.css">
<link rel="stylesheet" href="fullcalender_assets/bootstrap-theme.min.css">
<link href='fullcalender_assets/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='fullcalender_assets/lib/moment.min.js'></script>
<script src='fullcalender_assets/lib/jquery.min.js'></script>
<script src="fullcalender_assets/bootstrap.min.js"></script>
<script src='fullcalender_assets/lib/jquery-ui.custom.min.js'></script>
<script src='fullcalender_assets/fullcalendar.min.js'></script>
<script src='fullcalender_assets/spinner.js'></script>
<script src='fullcalender_assets/full_calender_custom_functions.js'></script>
<script src='fullcalender_assets/bootstrap-timepicker.min.js'></script>
<style>

	body {
		 /* overflow-y: hidden; */
		text-align: center;
		 
	}
		
	#wrap {
		/*width: 1100px;*/
		margin: 10.5% 0 0 0;
	}
		
	#external-events .fc-event {
		margin: 10px 5px;
		cursor: pointer;
		padding: 10px;font-weight:600;
  		width: 110px; border:0;
	}
	.ajax_loader {background: url("fullcalender_assets/spinner_squares_circle.gif") no-repeat center center transparent;width:100%;height:100%;}
	.blue-loader .ajax_loader {background: url("fullcalender_assets/ajax-loader_blue.gif") no-repeat center center transparent;}
.modal-open .modal  {
 background:none;
 width:auto;
 overflow-y: hidden;
 margin-left:auto;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;}
  #inner-page {
  margin-top: 6%;
}
label{color:#333; }
</style>
</head>
<?php 
/* ---------------------------------- Query Box   -------------------------------------------- */
		$q=mysql_query("SELECT * FROM `clinicdetail` WHERE DoctorID ='$_REQUEST[did]'");
		$ds=mysql_query("SELECT * FROM `doctorheader` WHERE DoctorID ='$_REQUEST[did]'");
		if(!mysql_num_rows($q) || !mysql_num_rows($ds)) exit;
/* ---------------------------------- !Query Box   -------------------------------------------- */
?>
<body  >
	<section id="inner-page">

  <div class="container">
 <div class="appointment-gray1" style="margin-top:0px; display:;">
					<form style=" border:0; padding:0;" > 
					
		<div class="col-md-12">
			<div class="col-md-4">
             <h4> <div style="color:#666; padding: 14px 0 8px; border-bottom:#5bcba7 solid 1px; width: 74%;text-align: left;font-size: 22px;">Setup Master Schedule </div></h4>         
				<?php /*?><?php $dsrow=mysql_fetch_assoc($ds);?>
				<label><?php echo $dsrow['DoctorID']."-".$dsrow['DoctorFirstName'];?></label><?php */?>
			</div>
		
			<div class="col-md-4">
				<input type="hidden" value="<?php echo $_REQUEST[did]?>" id="did">
				<select class="form-control" id="clinics" style="  margin: 17px 0 0 0;">
					<option value="">Select Clinic</option>
					<?php while($row=mysql_fetch_assoc($q)){?>
						<option value="<?php echo $row['ClinicID']?>"><?php echo $row['ClinicID']?> - <?php echo $row['ClinicName']?></option>
					<?php }?>
				</select>
			</div>
			<div class="col-md-4">
				<button type="button" class="color-btn1" id="save_scheduling" style="font-size:22px; margin:0px;  float:right; text-align:center; padding:5px 10px">
                <i class="icon-ok"></i> 
                <p style="font-size:12px; display:block;"> Save changes</p>
               </button>
			</div>
		</div>
        </form>
        </div>
		<div class="col-md-12">
			<div id='external-events' class="col-md-2">
				<h4 style="font-size:14px; text-align:left;">Type of Schedule</h4>
				<div class='fc-event' style="background-color:#FF5252;color:white" id="Face_to_Face"> Face to Face </div>
				<div class='fc-event' style="background-color:#00C853;color:white" id="Video_Chat">Video Chat </div>
				<div class='fc-event' style="background-color:#0091EA;color:#FFF" id="Walking"> Walk In </div>
                <p style="text-align: left;font-size: 13px;">*Pick and drag colours to schedule</p>
			</div>
			<div id='calendar' class="col-md-10 " style="  margin-top: -30px;"></div>
		</div>
		
		
	</div>
	<br>
<div class="modal fade" id="EventDetail">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" ><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Change of Schedule</h4>
      </div>
      <div class="modal-body appointment-gray1">
			<form style="padding:0; margin:0; border:0;">
				<div class="bootstrap-timepicker col-lg-4" style="text-align:left;">
					<label>Type of Schedule</label>
					<select class="form-control" id="TypesOfSchedule">
					<option value="Face to Face">Face to Face</option>
					<option value="Video Chat" >Video Chat</option>
					<option value="Walk Ino">Walk In</option>
					</select>
				</div>
				<div class="bootstrap-timepicker col-lg-4" style="text-align:left;">
					<label>Start Time</label>
					<input id="timepicker5" type="text" class="input-small form-control" style="height:35px;">
				</div>
				<div class="bootstrap-timepicker col-lg-4" style="text-align:left;">
					<label>End Time</label>&nbsp;
					<input id="timepicker6" type="text" class="input-small form-control"  style="height:35px;">
				</div>
			</form>
      </div>
      <div class="modal-footer">
		<button type="button" class="btn btn-default" id="UpdateEvent">Update</button>
      	 <button type="button" id="delete_event" class="btn btn-default" >Delete</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
</section>
 
<script>
$('#timepicker5').timepicker({
                template: false,
                showInputs: false,
                minuteStep: 5
});
$('#timepicker6').timepicker({
                template: false,
                showInputs: false,
                minuteStep: 5
});
</script>
</div>
<?php include "footer2.php";?>  
