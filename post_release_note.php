<?php 
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__.'/../ismile/ismile_util.php');
addAccessControlAllowOrigin();
header('Content-Type: application/json');
$request_body   =       file_get_contents("php://input");
$log->logInfo("Req ".$request_body);
$data     = /*. (string[string]) .*/json_decode($request_body,true);
$formtype = isset($data['formtype'])?$data['formtype']:'';
$rn_dtl   = isset($data['data'])?$data['data']:array();

$exec_res= false;
foreach ($rn_dtl as $brand_dtl){
    $brand_ref_id = isset($brand_dtl['brand_ref_id'])?(int)$brand_dtl['brand_ref_id']:0;
    $version  = isset($brand_dtl['version'])?(string)$brand_dtl['version']:"";
    $content = isset($brand_dtl['content'])?$brand_dtl['content']:null;
    if($formtype=="new"){
        $cqr = execute_my_query("select GRN_VERSION from gft_release_note_dtl where GRN_VERSION='$version' and GRN_BRAND_REF_ID='$brand_ref_id'");
        if(mysqli_num_rows($cqr) > 0){
            send_response_with_code_and_log($log, "Version already exists. Please give new one or edit the existing version", HttpStatusCode::BAD_REQUEST);
            exit;
        }
    }
    if($content!=null && count($content)>0 && $version!=""){
        $can_update = false;
        foreach ($content as $content_value){
            $section_id = isset($content_value['section_id'])?$content_value['section_id']:"";
            $value      = isset($content_value['value'])?$content_value['value']:"";
            if( ($section_id=='4') && ($value!="") ){
                $can_update = true;
            }
        }
        if($can_update){
            foreach ($content as $content_value){
                $section_id = isset($content_value['section_id'])?$content_value['section_id']:"";
                $value = $value_to_insert = isset($content_value['value'])?$content_value['value']:"";
                if(is_array($value)){
                    $value_to_insert = json_encode($value);
                }
                $upd_arr = array(
                    'GRN_BRAND_REF_ID'=>$brand_ref_id,
                    'GRN_VERSION'=>$version,
                    'GRN_KEY'=>$section_id,
                    'GRN_VALUE'=>$value_to_insert
                );
                $key_arr = array(
                    'GRN_BRAND_REF_ID'=>$brand_ref_id,'GRN_VERSION'=>$version,'GRN_KEY'=>$section_id
                );
                $exec_res = array_update_tables_common($upd_arr, "gft_release_note_dtl", $key_arr, null, SALES_DUMMY_ID,null,null,$upd_arr);
            }
        }
    }
}
if($exec_res){
    $response_arr['message'] = "Successfully updated the release note details";
    echo json_encode($response_arr);
    exit;
}else{
    send_response_with_code_and_log($log, "Not updated", HttpStatusCode::BAD_REQUEST);
    exit;
}

?>
