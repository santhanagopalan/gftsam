<?php
require_once(__DIR__.'/lic_util.php');
require_once(__DIR__.'/log.php');
require_once(__DIR__.'/function.insert_stmt_for_activity.php');
header('Content-Type: application/json');
require_once(__DIR__.'/ismile/ismile_util.php');
addAccessControlAllowOrigin();
/**
 * @param string[string] $response_arr
 * @param string $callback
 * 
 * @return void
 */
function send_response_to_request($response_arr,$callback){
	global $log;
	if($callback!=""){
		echo $callback."(".json_encode($response_arr).");";
	}else{
		echo json_encode($response_arr);
	}
	$processing_time = getDeltaTime();
	$log->logInfo("Processing Time $processing_time .  Resp ".json_encode($response_arr));
}
header('Content-Type: application/json');
$request_body	=	file_get_contents("php://input");
$log->logInfo($request_body);
$data			=	/*. (string[string]) .*/json_decode($request_body,true);
$cust_id		=	isset($_REQUEST['cust_id'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['cust_id']):'';
$confirm_code	=	isset($_REQUEST['confirm_code'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['confirm_code']):'';
$mobileNumber	=	isset($_REQUEST['mobileNumber'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['mobileNumber']):'';
$otp_timestamp	=	isset($_REQUEST['otpTimestamp'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['otpTimestamp']):'';
$callback		= 	isset($_REQUEST['callback'])?(string)$_REQUEST['callback']:'';
$verifyOTP		=	isset($_REQUEST['verifyOTP'])?(int)$_REQUEST['verifyOTP']:0;
$req_auth_token = 	isset($_REQUEST['required_auth_token'])?(int)$_REQUEST['required_auth_token']:0;
if(isset($data['customerId']) && $data['customerId']!=""){
	$cust_id		=	isset($data['customerId'])?$data['customerId']:'';
	$confirm_code	=	isset($data['confirmCode'])?mysqli_real_escape_string_wrapper($data['confirmCode']):'';
	$mobileNumber	=	isset($data['mobileNumber'])?$data['mobileNumber']:'';
	$verifyOTP		=	isset($data['verifyOTP'])?(int)$data['verifyOTP']:0;
	$callback		= 	isset($data['callback'])?$data['callback']:'';
	$req_auth_token =	isset($data['requiredAuthToken'])?(int)$data['requiredAuthToken']:0;
}
$err_msg = '';
if($cust_id==''){
	$err_msg = 'Mandatory field(customerId) required';
}else if($confirm_code==''){
	$err_msg = 'Mandatory field(confirmCode) required';		
}
if($err_msg!=''){
	$return_array['status']='false';
	$return_array['error_message'] = $err_msg;
	send_response_to_request($return_array,$callback);
	exit;
}
if($verifyOTP==1){
	if(!verify_otp_and_update_status($cust_id,$confirm_code)){
		$return_array['status']='false';
		$return_array['error_message']='Invalid OTP';
		send_response_to_request($return_array,$callback);
		exit;
	}
	verify_otp_and_update_status($cust_id,$confirm_code,"update");
	$resp_arr['status'] 		= 'true';
	$resp_arr['cust_id'] 		= "$cust_id";
	send_response_to_request($resp_arr, $callback);exit;
}
$insert_activity = true;
$wh_cond = " and (GPR_CONFIRM_CODE='$confirm_code' OR GPR_OTP_CODE='$confirm_code') ";
if($otp_timestamp!=""){
	$wh_cond .= " and GPR_CREATED_DATE='$otp_timestamp' ";		
	$insert_activity = false; //backend verification not required
}else{
	$wh_cond .= " and GPR_LEAD_CODE='$cust_id' and GPR_ACTIVATION_STATUS!='I' ";
}
if($mobileNumber!=""){
    $mobileNumber = str_replace("+91","",$mobileNumber);
	$wh_cond .= " and GPR_OTP_MOBILENO='$mobileNumber' ";
}

$sql_check =" SELECT GPR_REGISTER_ID,GPR_ACTIVATION_STATUS,GPR_PCODE,GPR_EMAIL_ID,GPR_OTP_MOBILENO, ".
			" GLH_COUNTRY,GLH_CUST_NAME,GPR_CREATED_CATEGORY,GPR_LEAD_CODE,GPR_CREATED_DATE from gft_presignup_registration ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GPR_LEAD_CODE) ".
			" where 1 $wh_cond ";
$sql_res = execute_my_query($sql_check);
if(mysqli_num_rows($sql_res)==0){
	$return_array['status']='false';
	$return_array['error_message']='Invalid OTP';
	send_response_to_request($return_array,$callback);
	exit;
}

if($res_row	= mysqli_fetch_array($sql_res)){
	$pcode	= $res_row['GPR_PCODE'];
	$email	= $res_row['GPR_EMAIL_ID'];
	$mobile	= $res_row['GPR_OTP_MOBILENO'];
	$glh_country = $res_row['GLH_COUNTRY'];
	$cust_name	 = $res_row['GLH_CUST_NAME'];
	$lead_code	=	$res_row['GPR_LEAD_CODE'];
	$register_id = base64_encode($res_row['GPR_REGISTER_ID']);
	$created_category=$res_row['GPR_CREATED_CATEGORY'];
	$created_dt = $res_row['GPR_CREATED_DATE'];
	
	if( (strcasecmp("India", $glh_country)==0) && check_can_send_sms($mobile) ){
		execute_my_query("UPDATE gft_lead_hdr SET GLH_CONTACT_VERIFIED='2', GLH_CONTACT_VERIFED_BY='OTP' WHERE GLH_LEAD_CODE='$cust_id'");
		$GLD_CUST_FEEDBACK="verified contact using OTP $mobile";
	}else{
		execute_my_query("UPDATE gft_lead_hdr SET GLH_CONTACT_VERIFIED='2', GLH_CONTACT_VERIFED_BY='MAIL' WHERE GLH_LEAD_CODE='$cust_id'");
		$GLD_CUST_FEEDBACK="verified contact using email $email";
	}
	$activity_dtl = /*. (string[string]) .*/ array();
	$activity_dtl['GLD_LEAD_CODE']	=	$cust_id;
	$activity_dtl['GLD_EMP_ID']		=	'9999';
	$activity_dtl['GLD_DATE']		=	date('Y-m-d H:i:s');
	$activity_dtl['GLD_VISIT_DATE']	=	date('Y-m-d');
	$activity_dtl['GLD_NOTE_ON_ACTIVITY'] = $GLD_CUST_FEEDBACK;
	$activity_dtl['GLD_CALL_STATUS']	=	"P";
	$activity_dtl['GLD_REPEATED_VISITS']=	"N";
	$activity_dtl['GLD_VISIT_NATURE']	=	'71';
	$insert_activity = check_to_skip_activity($cust_id,$pcode, $insert_activity);
	if($insert_activity){
		insert_in_gft_activity_table($activity_dtl,null,true);
	}
	execute_my_query("UPDATE gft_presignup_registration SET GPR_ACTIVATION_STATUS='I', GPR_UPDATED_DATE=now() where 1 $wh_cond ");
	if($pcode=='705'){
		$mob_con = getContactDtlWhereCondition("GCC_CONTACT_NO", $mobile);
		$email_qry =" select GSG_PRODUCT_CODE,GSG_LEAD_CODE,GSG_ORDER_NO from gft_sms_gateway_info ".
				" left join gft_customer_contact_dtl on (GSG_LEAD_CODE=GCC_LEAD_CODE) ".
				" where (GCC_CONTACT_NO='$email' or $mob_con) and GSG_PRODUCT_CODE='$pcode' ";
		$email_check = execute_my_query($email_qry);
		$num_rows = mysqli_num_rows($email_check);
		if($num_rows==0){
			$db_content_config = array(
					'url'=>array($register_id),
					'Customer_Name'=>array($cust_name)
			);
			send_formatted_mail_content($db_content_config, 9, 250,null,null,array($email));
			$resp_arr['registerUrl'] = "http://".$_SERVER['SERVER_NAME']."/create_account.php?id=$register_id";
		}else if($row1 = mysqli_fetch_array($email_check)){
			$lead_code = $row1['GSG_LEAD_CODE'];
			$order_no  = $row1['GSG_ORDER_NO'];
			$secret1 = bin2hex("myownkey");
			$request_id = get_gateway_request_id();
			$data_sent1 = "<request_id>".$request_id."</request_id>".
						  "<SAM_id>".$lead_code."</SAM_id>".	
						  "<Email>".$email."</Email>";
			$data_sent	= "<request_id>".htmlentities(lic_encrypt($request_id,$secret1))."</request_id>" .
						  "<SAM_id>".htmlentities(lic_encrypt($lead_code,$secret1))."</SAM_id>" .
						  "<Email>".htmlentities(lic_encrypt($email,$secret1))."</Email>"	;
			$result_r = execute_my_query("insert into gft_smsgareway_request (GSR_REQUEST_ID, GSR_DATE, GSR_ORDER_NO, GSR_LEAD_CODE, GRD_REQUEST_MESSAGE,GRD_REQUEST) " .
						" values('$request_id',now(),'$order_no',$lead_code, '".mysqli_real_escape_string_wrapper($data_sent)."','".mysqli_real_escape_string_wrapper($data_sent1)."')");
			if($result_r){
				truealert_post($request_id,4);
			}
		}
	}
	//Sending welcome mail for Dukaan POS registration
	if($created_category=='39'){
		$db_sms_content_config=array(
				'Customer_Id' => array($lead_code),
				'Customer_Name' => array($cust_name)
		);
		send_formatted_mail_content($db_sms_content_config,2,254,null,null,$email);		
	}
	$ns_user_auth_token = "";
	if($req_auth_token==1){
		$ns_arr = process_notification_registration($pcode, $mobile, $email);
		$ns_user_auth_token = isset($ns_arr['user_auth_token'])?$ns_arr['user_auth_token']:'';
	}	
	$resp_arr['status'] 		= 'true';
	$resp_arr['cust_id'] 		= "$cust_id";
	$resp_arr['otp'] 			= $confirm_code;
	$resp_arr['otpTimestamp'] 	= $created_dt;
	$resp_arr['user_auth_token'] = $ns_user_auth_token;
	send_response_to_request($resp_arr, $callback);
}

?>
