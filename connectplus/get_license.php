<?php
require_once(__DIR__.'/connectplus_util.php');
require_once(__DIR__.'/../log.php');

header('Content-Type: application/json');

$customerId = isset($_REQUEST['customerId'])?(string)$_REQUEST['customerId']:'';
$userId		= isset($_REQUEST['userId'])?(string)$_REQUEST['userId']:'';
$required	= isset($_REQUEST['required'])?(string)$_REQUEST['required']:'';
$require_free_flag	= isset($_REQUEST['require_free_flag'])?(string)$_REQUEST['require_free_flag']:'';
$productCode= isset($_REQUEST['productCode'])?(int)$_REQUEST['productCode']:0;

$config = get_connectplus_config();
$api_key = $config['sam_api_key'];
$headers = apache_request_headers();
$received_key 	= isset($headers['X-Api-Key'])?(string)$headers['X-Api-Key']:'';
$error_message = "";
if($api_key!=$received_key){
	$error_message = 'key check failed';
	$log->logError("Received key($received_key) is invalid");
}else if ( ($userId=='') && ($required=='') ){
	$error_message = "user id is empty";
	$log->logError($error_message);
}else{
	$check_id = get_single_value_from_single_table("GLH_LEAD_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", $customerId);
	if($check_id==''){
 	    $que_ch2 = "select gcm_lead_code from gft_cust_merger_data  where merg_slave='$customerId' limit 1";
 	    $res_ch2 = execute_my_query($que_ch2);
 	    if($row_ch2 = mysqli_fetch_array($res_ch2)){
 	        $customerId = $row_ch2['gcm_lead_code'];
 	    }else{
 	        $error_message = "invalid customer id($customerId) ";
 	        $log->logError($error_message);
 	    }
	}
}
if($error_message!=''){
	$return_arr['message'] = $error_message;
	send_failure_response($return_arr, HttpStatusCode::FORBIDDEN);
	exit;
}
if($required=='device'){
	$sql1 = " select GCD_APP_PCODE,GCD_DEVICE_ID,app.GID_VALIDITY_DATE ".
			" from gft_install_dtl_new base ".
			" join gft_customer_device_mapping on (GCD_INSTALL_ID=base.GID_INSTALL_ID and GCD_DEVICE_STATUS=1) ".
			" join gft_install_dtl_new app on (app.GID_LEAD_CODE=base.GID_LEAD_CODE and app.GID_LIC_PCODE=GCD_APP_PCODE) ".
			" where base.GID_LEAD_CODE='$customerId' and app.GID_STATUS!='U' and base.GID_STATUS!='U' ";
	if($productCode > 0){
		$sql1 .= " and GCD_APP_PCODE='$productCode' ";
	}
	$res1 = execute_my_query($sql1);
	$response_arr = /*. (string[string][int][string]) .*/array();
	while ($row1 = mysqli_fetch_array($res1)){
		$validity_date = $row1['GID_VALIDITY_DATE']." 23:59:59";
		$utc_format = gmdate('Y-m-d H:i:s',strtotime($validity_date));
		$arr['productId']  = $row1['GCD_APP_PCODE'];
		$arr['expiryDate'] = $utc_format;
		$arr['deviceName'] = $row1['GCD_DEVICE_ID'];
		$response_arr['devices'][] = $arr;
	}
	$response = json_encode($response_arr);
}else if($required=='user'){
	$sql1 = " select GPU_USER_ID,GPU_USER_NAME,app.GID_VALIDITY_DATE,GCC_CONTACT_NO from gft_install_dtl_new base ".
			" join gft_app_users on (GAU_INSTALL_ID=base.GID_INSTALL_ID) ".
			" join gft_pos_users on (GPU_INSTALL_ID=base.GID_INSTALL_ID and GPU_CONTACT_ID=GAU_CONTACT_ID) ". 
			" join gft_customer_contact_dtl on (GCC_ID=GPU_CONTACT_ID and GPU_CONTACT_TYPE=1) ".
			" join gft_install_dtl_new app on (app.GID_LEAD_CODE=base.GID_LEAD_CODE and app.GID_LIC_PCODE=GAU_APP_PCODE) ". 
			" where base.GID_LEAD_CODE='$customerId' and GAU_APP_PCODE='$productCode' and GPU_CONTACT_STATUS='A' and GAU_USER_STATUS=1 and base.GID_STATUS!='U' ".
			" group by GPU_USER_NAME ";
	$res1 = execute_my_query($sql1);
	$resp_arr = /*. (string[int][string]) .*/array();
	while ($row1 = mysqli_fetch_array($res1)){
		$validity_date = $row1['GID_VALIDITY_DATE']." 23:59:59";
		$utc_format = gmdate('Y-m-d H:i:s',strtotime($validity_date));
		$resp_arr[] = array(
		    'userId'=>$row1['GPU_USER_ID'],
		    'userName'=>$row1['GPU_USER_NAME'],
		    'expiryDate'=>$utc_format,
		    'mobileNumber'=>$row1['GCC_CONTACT_NO']
		);
	}
	if($require_free_flag=='true'){
		$free_flag = false;
		if(in_array($customerId, array($mydelight_tm_cust_id,$mygofrugal_task_manger_customer_id))){
			$free_flag = true;
		}
		$out_arr['free'] = $free_flag;
		$out_arr['license'] = $resp_arr;
		$response = json_encode($out_arr);
	}else{
		$response = json_encode($resp_arr);
	}
	
}else {
	$resp_arr = get_license_info($customerId, $userId);
	$response = json_encode($resp_arr);
}
echo $response;
$log->logInfo($response);

?>
