<?php
require_once(__DIR__.'/dbcon.php');
require_once(__DIR__.'/log.php');

$order_no = isset($_REQUEST['order_no'])?(string)$_REQUEST['order_no']:'';
$log->logInfo("Request Body - ".json_encode($_REQUEST));
$rec_order = $order_no;
if($order_no !== ''){
    $order_no = str_replace(array("-", "WR"), "", $order_no);
    $license_no = substr($order_no, 0, 15);
    $fullfill_no = (int)substr($order_no, 15, 4);
    $qry = " select GID_SYS_HKEY uuid from gft_install_dtl_new join gft_product_family_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE) ".
           " where GID_ORDER_NO='$license_no' and GID_FULLFILLMENT_NO='$fullfill_no' and gid_status!='U' and GID_SYS_HKEY!='' ";
    $num_rows = mysqli_num_rows(execute_my_query($qry));
    if($num_rows > 1){
        if(strpos($rec_order,"WR")===false){
            $qry .= " and GPM_HEAD_FAMILY=400 ";
        }else {
            $qry .= " and GPM_HEAD_FAMILY!=400 ";
        }
    }else if ($num_rows==0){
        $emply_id = (int)check_for_employee_mbile_and_id(substr($order_no,0,15));
        if($emply_id > 0){
            $qry =  " select GLU_UUID uuid from gft_local_user_activation_dtl where GLU_EMP_ID='$emply_id' order by GLU_DATE_OF_ACTIVATION desc limit 1 ";
        }
    }
    $qres = execute_my_query($qry);
    if ($row = mysqli_fetch_array($qres)){
        $resp['uuid'] = $row['uuid'];
        $log->logInfo("Response - ".json_encode($resp));
        echo json_encode($resp);
    }else{
        send_response_with_code_and_log($log, "Order number doesn't match", HttpStatusCode::BAD_REQUEST);
        exit;
    }
}else{
    send_response_with_code_and_log($log, "Order number is missing in the request param", HttpStatusCode::BAD_REQUEST);
    exit;
}

?>
<script type="text/javascript">
window.close();
</script>