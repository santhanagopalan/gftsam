<?php
require_once(__DIR__.'/../dbcon.php');
header('Content-Type: application/json');
validate_session($uid);
$status     = isset($_GET['status'])?(int)$_GET['status']:0;
$brandingId = isset($_GET['brandingId'])?(int)$_GET['brandingId']:0;
$comments   = isset($_GET['comments'])?$_GET['comments']:'';
if($brandingId==0 || (int)$status==0){
    send_response_with_code_and_log($log,"Invalid data",HttpStatusCode::BAD_REQUEST);
    exit;
}
$build_stage=(int)get_samee_const('MP_BUILD_URL');
$sel_column = ($build_stage==0) ? "GDB_LIVE_BUILD_URL" : "GDB_TEST_BUILD_URL";
$que1 = " select GWH_LEAD_CODE,GWH_PRODUCT_CODE,GWH_APP_NAME,$sel_column,GWH_ANDROID_VERSION_CODE,GWH_ANDROID_VERSION_NAME, ".
    " GWH_PRIMARY_COLOR,GWH_SECONDARY_COLOR from gft_whitelabel_hdr ".
    " join gft_dealer_build_master on (GDB_BASE_PRODUCT=GWH_PRODUCT_CODE) where GWH_ID='$brandingId' ";
$res1 = execute_my_query($que1);
if($row1 = mysqli_fetch_array($res1)){
    $updArr = array(
        'GWH_APPROVAL_STATUS'=>$status,
        'GWH_COMMENTS'=>$comments,
        'GWH_UPDATED_BY'=>$uid
    );
    if($status==2){
        $customerId     = $row1['GWH_LEAD_CODE'];
        $productCode    = $row1['GWH_PRODUCT_CODE'];
        $build_url      = $row1[$sel_column];
        $appName        = $row1['GWH_APP_NAME'];
        $primaryColor   = $row1['GWH_PRIMARY_COLOR'];
        $secondaryColor = $row1['GWH_SECONDARY_COLOR'];
        $andr_vers_code = (int)$row1['GWH_ANDROID_VERSION_CODE'] + 1;
        $andr_ver_name  = (float)$row1['GWH_ANDROID_VERSION_NAME'] + 0.1;

        $fpath = "../$attach_path/mp/$customerId/$productCode";
        $env_content = <<<END
APP_NAME=$appName
CUSTOMER_ID=$customerId
PACKAGE_NAME=com.gofrugal.ordereasy
PRIMARY_COLOR=$primaryColor
SECONDARY_COLOR=$secondaryColor
ANDROID_VERSION_CODE=$andr_vers_code
ANDROID_VERSION_NAME=$andr_ver_name
END;
        file_put_contents("$fpath/env", $env_content);

        $download_url   = str_replace("../../", "", CURRENT_SERVER_URL."/$fpath");
        $curl_url       = "$build_url?cust_id=$customerId&download_url=$download_url";
        $ch = curl_init($curl_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec ($ch);
        curl_close($ch);
        error_log($response);
        $updArr['GWH_ANDROID_VERSION_CODE'] = $andr_vers_code;
        $updArr['GWH_ANDROID_VERSION_NAME'] = $andr_ver_name;
    }
    array_update_tables_common($updArr, "gft_whitelabel_hdr", array("GWH_ID"=>$brandingId), null,$uid);
    $output['message'] = 'Updated Successfully';
    echo json_encode($output);
}

?>
