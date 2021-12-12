<?php
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__.'/../lic_util.php');
require_once(__DIR__ . "/ismile_util.php");
require_once(__DIR__ . "/http_error.php");
header("Content-Type: application/json");
addAccessControlAllowOrigin();

$request_body	=	'';
try{
	$request_body = file_get_contents('php://input');
}catch(Exception $e){
	die("Exception in: ".$e);
}
$data = /*. (string[string]) .*/json_decode($request_body,true);

$purpose		=	isset($data['purpose'])?$data['purpose']:'';
if($purpose==''){
	//report_issue - file as well as post data not supported in request body. so file in body and post data in post param
	$purpose=isset($_REQUEST['purpose'])?(string)$_REQUEST['purpose']:'';
	if($purpose=='report_issue'){
		$data = /*. (string[string]) .*/$_REQUEST;
	}
}
$customer_id	=	isset($data['customer_id'])?$data['customer_id']:'';
$product_code	=	isset($data['product_code'])?$data['product_code']:'';
$product_skew   =   isset($data['product_skew'])?$data['product_skew']:'01.0';
$support_stat	=	isset($data['complaint_status'])?$data['complaint_status']:'';
$description	=	isset($data['description'])?$data['description']:'';
$section		=	isset($data['page'])?$data['page']:'';
$severity		=	isset($data['severity'])?$data['severity']:'4';
$complaint_type = 	isset($data['complaint_type'])?$data['complaint_type']:'';
$support_group_id = isset($data['support_group_id'])?$data['support_group_id']:'';
$base_product_id  = isset($data['base_product_id'])?$data['base_product_id']:'';
$base_product_version  = isset($data['base_product_version'])?$data['base_product_version']:'';

$visit_nature=null;
if( ($purpose=='') || ($customer_id=='') || ($product_code=='') || ($support_stat=='') ){
	$error_msg = '';
	if($purpose==''){
		$error_msg = 'Purpose';
	}else if($customer_id==''){
		$error_msg = 'Customer Id';
	}else if($product_code==''){
		$error_msg = 'Product Code';
	}else if($support_stat==''){
		$error_msg = 'Complaint Status';
	}
	send_error($request_body,"Mandatory Field - $error_msg Required",HttpStatusCode::BAD_REQUEST,$customer_id,'30');
	exit;
}
$check_customer_id = get_single_value_from_single_table("GLH_LEAD_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", $customer_id);
if($check_customer_id==''){
	send_error($request_body,"Invalid Customer ID ($customer_id)",HttpStatusCode::BAD_REQUEST,$customer_id,'30');
	exit;
}
$received_pcode = $product_code;

if(in_array($product_code, array('703','705'))){ //to overite app product code to main product code	
	$product_dtl_arr = get_customer_installed_product_for_complaint($customer_id);
	$product_code = isset($product_dtl_arr['product_code'])?$product_dtl_arr['product_code']:$product_code;
	$product_skew = isset($product_dtl_arr['product_skew'])?$product_dtl_arr['product_skew']:$product_skew;
}
$issue_summary = "From Whatsnow for $purpose";
if($purpose=='report_issue'){
	$issue_summary = "Issue in $section";
}else if($purpose=="uberization"){
	$issue_summary = "Customer raised ticket from Uberization.";
	$complaint_type='164';
	$visit_nature = '20';
	if($product_code!=""){
		$product_arr = explode("-", $product_code);
		$product_code = isset($product_arr[0])?$product_arr[0]:"10";
		$product_skew = isset($product_arr[1])?$product_arr[1]:"01.0";
	}
	if($product_code==0){
		$product_code ="10";
		$product_skew = "01.0";
	}
	if($support_group_id==1){
		$support_stat = "T20";
	}
	if($support_group_id==17 || $support_group_id==34){
		$support_stat = "T90";
	}
	if($support_group_id==6){
		$support_stat = "T15";
	}
}
if($complaint_type==""){
	switch ($purpose){
		case 'report_issue':$complaint_type='308';
							if($received_pcode=='705'){
								$complaint_type = '321';
							}
							break;
		case 'suggest_question':$complaint_type='313';break;
		case 'report_more_result':$complaint_type='312';break;
		default:send_error($request_body,"Invalid Purpose ($purpose) ",HttpStatusCode::BAD_REQUEST,$customer_id,'30');
				exit;
	}
}
$process_emp = null;
$send_sms = false;
if($product_code=='120'){
	$process_emp = get_single_value_from_single_table("GPG_EMP_FOR_ADD_VERSION", "gft_product_group_master", "gpg_product_family_code", "120");
	$send_sms = true;
}
if($base_product_id!=''){
	$addon_prod_id = $product_code."-".$product_skew;
	$q1 = " select GCC_DEV_OWNER from gft_chat_channel_master join gft_emp_master on (GEM_EMP_ID=GCC_DEV_OWNER) ".
		  " where GCC_BASE_PRODUCT_ID='$base_product_id' and GCC_ADDON_PRODUCT_ID='$addon_prod_id' and GEM_STATUS='A' ";
	$r1 = execute_my_query($q1);
	if($d1 = mysqli_fetch_array($r1)){
		$process_emp = $d1['GCC_DEV_OWNER'];
	}
}
$new_complaint = ($product_code=='707') ? true : false;
$ticket_id = insert_support_entry($customer_id, $product_code, $product_skew, '', '', SALES_DUMMY_ID, '', $issue_summary, 
		$complaint_type, $support_stat, null,null,$process_emp,$visit_nature,$severity,$description,$send_sms,'',null,'','3',$new_complaint,
		'','','','','','',0,'','','',$base_product_id,false,$base_product_version);

$return_array['status']		=	"success";
$return_array['message']	=	"Request Registered. Your Ticket Id is $ticket_id";
$resp = json_encode($return_array); 
echo $resp;
if($request_body==''){
	$request_body = json_encode($_REQUEST);
}
log_request($request_body, $resp, '', $customer_id, '30');

?>
<script type="text/javascript">
window.close();
</script>