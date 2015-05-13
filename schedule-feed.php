<?php 
//date_default_timezone_set("Asia/Kolkata");
ini_set("display_errors",1);
include "config.php";
if($_REQUEST["action"]=="get"){
extract($_REQUEST);
	if(isset($clinic_id) && $clinic_id!=""){
		echo get_events($did,$clinic_id,$clashed_events=0);
	}
	else{
		echo '{"status":0,"msg":"Select Clinic"}';
	}
}
if($_REQUEST["action"]=="save"){
	extract($_REQUEST);
	if(isset($clinic_id) && $clinic_id!=""){
		$sql ="DELETE FROM doctorschedulingconfig WHERE `DoctorID`='$did' AND `ClinicID`='$clinic_id'";
		mysql_query($sql) or die(mysql_error());
		$clashing=0;
		foreach($events as $event){
			extract($event);
			$starttime=$start_hour.":".$start_minute.":"."00";
			$endtime=$end_hour.":".$end_minute.":"."00";
			$endtime=($starttime==$endtime)?date ("H:i:s", strtotime("+30 minutes", strtotime($starttime))):$endtime;
			$count=mysql_num_rows(mysql_query("SELECT * FROM doctorschedulingconfig WHERE  `Day`='$start_day' AND  `StartTime`< '$endtime' AND `EndTime` >'$starttime' "));
			if(!$count){
				$sql = "INSERT INTO `doctorschedulingconfig` (`DoctorID`,`ClinicID`, `Day`, `StartTime`, `EndTime`, `TypeOfScheduling`) VALUES ('$did','$clinic_id', '$start_day', '$starttime', '$endtime',  '$title');";
				mysql_query($sql) or die(mysql_error());
			}
			else $clashing++;
		}
		echo get_events($did,$clinic_id,$clashing);
	}
	else{
		echo '{"status":0,"msg":"Select Clinic"}';
	}
}
function get_events($did,$clinic_id,$clashed_events=0){
		$data=array();
		$events=array();
		$query=mysql_query("
				SELECT * FROM doctorclinicconnection WHERE `DoctorID`='$did' AND `ClinicID`='$clinic_id' GROUP BY `ClinicID`
		");
		if(mysql_num_rows($query)){//Background Events
			$row=mysql_fetch_assoc($query);
			extract($row);
			$days=explode(",",$DoctorAvailabilityDayFrom);
			//print_r($days);
			for($i=0;$i<count($days);$i++){
				$StartTime=DATE("H:i:s",strtotime($DoctorAvailabilityMorningStartTime));
				$EndTime=DATE("H:i:s",strtotime($DoctorAvailabilityMorningEndTime));
				$event=create_events($days[$i]-1,$StartTime,$EndTime,"aash","white","",$isbackground=true);
				array_push($events,$event);
				$StartTime=DATE("H:i:s",strtotime($DoctorAvailabilityAfternoonStartTime));
				$EndTime=DATE("H:i:s",strtotime($DoctorAvailabilityAfternoonEndTime));
				$event=create_events($days[$i]-1,$StartTime,$EndTime,"ash","white","",$isbackground=true);
				array_push($events,$event);
				$StartTime=DATE("H:i:s",strtotime($DoctorAvailabilityEveningStartTime));
				$EndTime=DATE("H:i:s",strtotime($DoctorAvailabilityEveningTime));
				$event=create_events($days[$i]-1,$StartTime,$EndTime,"ash","white","",$isbackground=true);
				array_push($events,$event);
			}
		}
		$query=mysql_query(//Live Events
			"
				SELECT *,
				CASE  
					WHEN TypeOfScheduling='Walk In' THEN '#0091EA'
					WHEN TypeOfScheduling='Video Chat' THEN '#00C853'
					WHEN TypeOfScheduling='Face to Face' THEN '#FF5252'
					ELSE 'black'
				END as background_color,
				if(TypeOfScheduling='Walk In' ,'#FFF','white') as color
				FROM `doctorschedulingconfig` WHERE `DoctorID`='$did' AND `ClinicID`='$clinic_id'
			"
		);
		if(mysql_num_rows($query)){
			while($row=mysql_fetch_assoc($query)){
				extract($row);
				$event=create_events($Day,$StartTime,$EndTime,$background_color,$color,$TypeOfScheduling,$isbackground=false);
				array_push($events,$event);
			}
		}
		if($events){
			$data["status"]=1;
			$data["events"]=$events;
			$data["clashed_events"]=$clashed_events;
			$data["msg"]="Events available";
		}
		else{
			$data["status"]=0;
			$data["clashed_events"]=$clashed_events;
			$data["msg"]="Events not available";
		}
		return json_encode($data);
}
function get_date_time_using_day_no($day,$time){
	$days=['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
	return ($days[$day]!='Sunday')?Date("Y-m-d",strtotime("$days[$day] this week"))."T$time": Date("Y-m-d",strtotime("$days[$day] last week"))."T$time";
}
function create_events($Day,$StartTime,$EndTime,$background_color,$color,$TypeOfScheduling,$isbackground){
	$event["start"]=get_date_time_using_day_no($Day,$StartTime);
	$event["end"]=get_date_time_using_day_no($Day,$EndTime);
	$event["backgroundColor"]=$background_color;
	$event["color"]=$color;
	$event["title"]=$TypeOfScheduling;
	if($isbackground){
		$event["rendering"]="background";
	}
	return $event;
}
