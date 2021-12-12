<?php
require_once(__DIR__.'/dbcon.php');
require_once(__DIR__.'/ismile/http_error.php');

$sms_id = isset($_REQUEST['msgid'])?(int)$_REQUEST['msgid']:0;
$status	= isset($_REQUEST['status'])?$_REQUEST['status']:'';
$delivered_date = isset($_REQUEST['delivered'])?$_REQUEST['delivered']:'';

if( ($sms_id==0) || ($status=='') ){
	send_failure_response(array('msg'=>'invalid request body'), HttpStatusCode::BAD_REQUEST);
	exit;
}

switch ($status){
	case 'DELIVRD':$status_id = 1;break;
	case 'UNDELIV':$status_id = 9;break;
	case 'INVALID-SUB':$status_id = 12;break;
	case 'ABSENT-SUB':$status_id = 12;break;
	case 'DNDNUMB':$status_id = 10;break;
	case 'REJECTED-MULTIPART':$status_id = 5;break;
	default:$status_id=13;break;
}

$update_query = " update gft_sending_sms set gos_status_updated_time='$delivered_date',gos_sms_status=$status_id ".
				" where gos_id='$sms_id' ";
execute_my_query("UPDATE gft_sent_sms_dtl SET GSD_GATEWAY_STATUS='$status' WHERE GSD_SMS_ID='$sms_id'");
execute_my_query($update_query);

$sql1 = "SELECT GOS_ACTIVITY_ID FROM gft_sending_sms where gos_id='$sms_id' and GOS_ACTIVITY_ID!=0 ";
$sql_activity_ref	=	execute_my_query($sql1);
if($row_acticity = mysqli_fetch_array($sql_activity_ref)){
	$act_id			=	(int)$row_acticity['GOS_ACTIVITY_ID'];
	if($act_id!=0){
		$act_sms_status	=	'Pending';
		if($status_id==1){
			$act_sms_status	=	'Sent';
		}
		update_sms_status_inactivity($act_id, $act_sms_status);
	}
}

?>
