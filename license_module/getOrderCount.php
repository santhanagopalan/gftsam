<?php 
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__.'/../log.php');
header('Content-Type: application/json');
$log->logInfo("GET Param => ".json_encode($_GET));

$identity   = isset($_GET['identity'])?(string)$_GET['identity']:'';
$eidentity  = isset($_GET['eidentity'])?(string)$_GET['eidentity']:'';
$pcode      = isset($_GET['productCode'])?(int)$_GET['productCode']:0;
$lastOrder  = isset($_GET['lastSyncedOrder'])?(string)$_GET['lastSyncedOrder']:'';

$iden_dtl   = get_details_from_idendity($identity);
$customerId = isset($iden_dtl[0])?(int)$iden_dtl[0]:0;
$installId  = isset($iden_dtl[1])?(int)$iden_dtl[1]:0;
if($customerId==0){
    send_response_with_code_and_log($log, "invalid identity ", HttpStatusCode::BAD_REQUEST);
    exit;
}
$newOrder   = false;
$ord_no = substr($lastOrder,0,15);
$ful_no = (int)substr($lastOrder, 15,4);
$myq1 = " select GPL_ORDER_NO from gft_prepaid_license join gft_order_hdr on (GPL_ORDER_NO=GOD_ORDER_NO) ".
        " where GPL_LEAD_CODE='$customerId' and GPL_PRODUCT_CODE='$pcode' order by GOD_CREATED_DATE desc limit 1 ";
$myr1 = execute_my_query($myq1);
if($myd1 = mysqli_fetch_array($myr1)){
    if($ord_no!=$myd1['GPL_ORDER_NO']){
        $newOrder   = true;
    }
}
$q1 = "select GAO_ORDER_COUNT from gft_addon_order_count where GAO_INSTALL_ID='$installId' and GAO_PRODUCT_CODE='$pcode' ";
$r1 = execute_my_query($q1);
$order_count = 0; 
if($d1 = mysqli_fetch_array($r1)){
    $order_count = (int)$d1['GAO_ORDER_COUNT'];
}
$resp = json_encode(array('balanceOrderCount'=>$order_count,'newOrderAvailable'=>$newOrder));
echo $resp;
$log->logInfo("Response => $resp");

?>
