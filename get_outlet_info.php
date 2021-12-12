<?php
require_once(__DIR__.'/lic_util.php');
require_once(__DIR__.'/log.php');
header('Content-Type: application/json');

$idendity	= isset($_REQUEST['identity'])?(string)$_REQUEST['identity']:'';
$log->logInfo("Request Params in JSON - ".json_encode($_REQUEST));
$today_date = date('Y-m-d H:i:s');

$dtl_arr = get_details_from_idendity($idendity);
$lead_code 	= isset($dtl_arr[0])?(int)$dtl_arr[0]:0;
$install_id	= isset($dtl_arr[1])?(int)$dtl_arr[1]:0;
if($install_id==0){
	send_response_in_json_with_file_log("failure", "Invalid Identity");
	exit;
}
$outlet_arr = /*. (string[int][string]) .*/array();
$sql1 = " select GOL_OUTLET_ID,GOL_CUST_ID,GID_VALIDITY_DATE,GID_ORDER_NO,GID_FULLFILLMENT_NO, ".
		" GID_LIC_PCODE,GID_LIC_PSKEW from gft_outlet_lead_code_mapping ".
		" left join gft_install_dtl_new on (GOL_ORDER_NO=GID_ORDER_NO and GOL_FULLFILLMENT_NO=GID_FULLFILLMENT_NO and GID_LIC_PCODE not in (400,410,420,430) and GID_STATUS!='U') ".
		" where GOL_INSTALL_ID='$install_id' and GOL_OUTLET_STATUS='A' ";
$res1 = execute_my_query($sql1);
while($row1 = mysqli_fetch_array($res1)){
	$gid_order_no = $row1['GID_ORDER_NO'];
	$gid_fullfillment_no = $row1['GID_FULLFILLMENT_NO'];
	$full_order_no = "";
	if($gid_order_no!=""){
		$full_order_no = $gid_order_no.substr("0000$gid_fullfillment_no", -4);	
	}
	$data_arr['outletId'] 	= $row1['GOL_OUTLET_ID'];
	$data_arr['customerId'] = $row1['GOL_CUST_ID'];
	$data_arr['expiryDate']	= (string)$row1['GID_VALIDITY_DATE'];
	$data_arr['orderNo']	= $full_order_no;
	$data_arr['skewCode']	= $row1['GID_LIC_PCODE'].(string)str_replace(".", "", $row1['GID_LIC_PSKEW']);
	$outlet_arr[] = $data_arr;
}
$resp_arr['storeUrl']		= $global_store_domain."/checkout.php?purpose=asa&customerId=$lead_code";
$resp_arr['outletDetails']	= $outlet_arr;
$resp = json_encode($resp_arr);
echo $resp;
$log->logInfo("Response - ".$resp);

?>
<script type="text/javascript">
window.close();
</script>