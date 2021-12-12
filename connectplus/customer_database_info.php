<?php
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__.'/../log.php');
$received_body	= file_get_contents("php://input");
$log->logInfo("Received body - ".$received_body);

if(is_request_from_internal_ip_to_prod()){
	send_response_in_json_with_file_log("failure","skipping internal request");
	exit;
}

$request_arr= /*. (string[string]) .*/json_decode($received_body,true);
$identity	= isset($request_arr['identity'])?$request_arr['identity']:'';
$eidentity	= isset($request_arr['eIdentity'])?$request_arr['eIdentity']:'';
$dbVersion	= isset($request_arr['dbVersion'])?$request_arr['dbVersion']:'';
$isNtCust   = isset($request_arr['isNettradeAvailable'])?(int)$request_arr['isNettradeAvailable']:0;
$isHqCust   = isset($request_arr['isHqOutlet'])?(int)$request_arr['isHqOutlet']:0;
$dotNetVersion	= isset($request_arr['dotNetVersion'])?$request_arr['dotNetVersion']:'';
$dbInfo		= isset($request_arr['dbInfo'])?/*. (string[int][string]) .*/$request_arr['dbInfo']:/*. (string[int][string]) .*/array();
if(count($request_arr)==0){
	send_response_in_json_with_file_log("failure","request body is empty");
	exit;
}
if(strcasecmp(md5($identity), $eidentity)!=0){
	send_response_in_json_with_file_log("failure","identity and eidentity mismatch");
	exit;
}
if(count($dbInfo)==0){
	send_response_in_json_with_file_log("failure","DB info array is empty");
	exit;
}
$lead_dtl 	= get_details_from_idendity($identity);
$cust_id 	= isset($lead_dtl[0])?(int)$lead_dtl[0]:0;
$install_id = isset($lead_dtl[1])?(int)$lead_dtl[1]:0;
if($install_id==0){
	send_response_in_json_with_file_log("failure","invalid identity ($identity) ");
	exit;
}
$ins_val = "";
$today_date = date('Y-m-d');
$comma = "";
foreach ($dbInfo as $varr){
	$dbName 			= isset($varr['dbname'])?mysqli_real_escape_string_wrapper($varr['dbname']):'';
	$dbSize_in_bytes 	= isset($varr['dbSize'])?(int)$varr['dbSize']:0;
	$logSize_in_bytes 	= isset($varr['logSize'])?(int)$varr['logSize']:0;
	if($dbName==''){
		send_response_in_json_with_file_log("failure","DB name is empty ");
		exit;
	}
	$que1 = " select GPD_ID from gft_pos_database_info where GPD_INSTALL_ID='$install_id' and GPD_DB_NAME='$dbName' ".
			" and GPD_UPDATED_TIME between '$today_date 00:00:00' and '$today_date 23:59:59' ";
	$res1 = execute_my_query($que1);
	if($row1 = mysqli_fetch_array($res1)){
		$gpd_id = $row1['GPD_ID'];
		$up1 	= "update gft_pos_database_info set GPD_DB_SIZE='$dbSize_in_bytes',GPD_LOG_SIZE='$logSize_in_bytes',GPD_UPDATED_TIME=now() where GPD_ID='$gpd_id' ";
		execute_my_query($up1);
	}else{
		$ins_val .= $comma."('$install_id','$dbName','$dbSize_in_bytes','$logSize_in_bytes',now())";
		$comma = ",";
	}
}
if($ins_val!=''){
	$ins_query =" insert into gft_pos_database_info (GPD_INSTALL_ID,GPD_DB_NAME,GPD_DB_SIZE,GPD_LOG_SIZE,GPD_UPDATED_TIME) ".
				" values $ins_val ";
	execute_my_query($ins_query);
}
if(isset($request_arr['dbVersion'])){
	$env_arr = array(
				'GCD_DATETIME'=> date('Y-m-d H:i:s'),
				'GCD_LEAD_CODE'=>$cust_id,
				'GCD_INSTALL_ID'=>$install_id,
				'GCD_DOTNET_VER'=>$dotNetVersion,
				'GCD_DB_VER'=>$dbVersion,
	            'GCD_IS_NT'=>$isNtCust,
	            'GCD_IS_HQ'=>$isHqCust
			   );
	$key_arr['GCD_INSTALL_ID']	= $install_id;
	array_update_tables_common($env_arr, "gft_cust_env_data", $key_arr, null, SALES_DUMMY_ID,null,null,$env_arr);
}
send_response_in_json_with_file_log("success","Saved");
?>
<script type="text/javascript">
window.close();
</script>