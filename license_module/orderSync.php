<?php 
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__.'/../log.php');
header('Content-Type: application/json');

$req_body   = file_get_contents("php://input");
$log->logInfo("Request body => $req_body");
$json_arr   = json_decode($req_body,true);
$identity   = isset($json_arr['identity'])?(string)$json_arr['identity']:'';
$eidentity  = isset($json_arr['eidentity'])?(string)$json_arr['eidentity']:'';
$pcode      = isset($json_arr['productCode'])?(int)$json_arr['productCode']:0;

$iden_dtl   = get_details_from_idendity($identity);
$customerId = isset($iden_dtl[0])?(int)$iden_dtl[0]:0;
$installId  = isset($iden_dtl[1])?(int)$iden_dtl[1]:0;
if($customerId==0){
    send_response_with_code_and_log($log, "invalid identity ", HttpStatusCode::BAD_REQUEST);
    exit;
}

$allowed_ids = explode(",",get_samee_const("Allowed_Customer_Ids"));
$allowed_ids = array_map('trim', $allowed_ids);
if( ($_SERVER['SERVER_NAME']=='sam.gofrugal.com') && (!in_array($customerId, $allowed_ids)) ){
    $local_ip_address = get_samee_const("LOCAL_IP_ADDRESS_SETUPWIZARD");
    $received_ip_address = $_SERVER['REMOTE_ADDR'];
    if(strpos($local_ip_address, $received_ip_address)!==false){
        send_response_with_code_and_log($log, "Skipping due to request from internal ip $received_ip_address", HttpStatusCode::BAD_REQUEST);
        exit;
    }
}
if(isset($json_arr['balanceOrderCount'])){
    $balCount = (int)$json_arr['balanceOrderCount'];
    $ins_arr = array('GAO_INSTALL_ID'=>$installId, 'GAO_PRODUCT_CODE'=>$pcode, 'GAO_ORDER_COUNT'=>$balCount);
    $key_arr = array('GAO_INSTALL_ID'=>$installId, 'GAO_PRODUCT_CODE'=>$pcode);
    array_update_tables_common($ins_arr, "gft_addon_order_count", $key_arr, null, SALES_DUMMY_ID, null,null,$ins_arr);
}
closeDBConnection();
?>
<script type="text/javascript">
window.close();
</script>