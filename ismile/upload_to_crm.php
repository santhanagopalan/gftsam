<?php
require_once (__DIR__."/ismile_util.php");
addAccessControlAllowOrigin();
compress_api_response();
header('Content-Type: application/json');

$log_operation = 42;
$purpose =	isset($_REQUEST['purpose'])?(string)$_REQUEST['purpose']:'';
$ref_id	 =	isset($_REQUEST['id'])?(string)$_REQUEST['id']:'';
$uberization	=	isset($_REQUEST['uberization'])?(int)$_REQUEST['uberization']:0;
$cust_user_id = 0;
$type			= 	"webview";
if($uberization!=1){
	$type			= 	"customer";
	$cust_user_id = customerAppAuth();
}
if($purpose==''){
	sendErrorWithCode("purpose=$purpose", "Required Purpose for the Request", HttpStatusCode::BAD_REQUEST,"$type",$cust_user_id);
	exit;
}
$resp = array();
$receive_arg	=	"id=$ref_id,purpose=$purpose";
if($purpose=='new_ticket'){
	if($ref_id==''){
		sendErrorWithCode($receive_arg,"Mandatory field Support Id is Empty",HttpStatusCode::BAD_REQUEST,"$type",$cust_user_id);
		exit;
	}
	$last_activity_id = get_single_value_from_single_table("GCH_LAST_ACTIVITY_ID","gft_customer_support_hdr","GCH_COMPLAINT_ID",$ref_id);
	if($last_activity_id==''){
		sendErrorWithCode($receive_arg,"Support Id not available in detail.",HttpStatusCode::BAD_REQUEST,"$type",$cust_user_id);
		exit;
	}
	if(isset($_FILES['upfile1']) and ((int)$_FILES["upfile1"]['size'] > 0) ){
		$file_name = time()."_".(string)$_FILES['upfile1']['name'];
		$file_name = (string)str_replace(" ", "_", $file_name);
		$img_path = date('Y')."/$ref_id";
		$upload_path= "$attach_path/Support_Upload_Files/".$img_path;
		
		if(!file_exists($upload_path)){
			mkdir($upload_path,0777, true);
		}
		if(move_uploaded_file((string)$_FILES['upfile1']['tmp_name'], $upload_path. "/" .$file_name)){
			$img_path = $img_path."/".$file_name;
			$upd_query =" update gft_customer_support_dtl set GCD_UPLOAD_FILE = if(GCD_UPLOAD_FILE is null or GCD_UPLOAD_FILE='','$img_path',concat(GCD_UPLOAD_FILE,',','$img_path')) ".
					" where GCD_ACTIVITY_ID='$last_activity_id' ";
			execute_my_query($upd_query);
		}
	}
	$resp['message'] = "Uploaded";
	echo json_encode($resp);
	if($uberization!=1){
		save_mygofrugal_access($cust_user_id, $log_operation, "", "Uploaded");
	}
}else if($purpose=='rebrand_app_logo'){
    $lead_code = (int)get_single_value_from_single_table("GCA_LEAD_CODE", "gft_customer_app_dtl", "GCA_ID", $ref_id);
    if($lead_code==0){
        sendErrorWithCode($receive_arg,"invalid Id",HttpStatusCode::BAD_REQUEST,"$type",$cust_user_id);
        exit;
    }
    if(isset($_FILES['upfile1']) and ((int)$_FILES["upfile1"]['size'] > 0) ){
        $file_name = time()."_".(string)str_replace(" ", "_", $_FILES['upfile1']['name']);
        $upload_path= "../$attach_path/mp/$lead_code";
        if(!file_exists($upload_path)){
            mkdir($upload_path,0777, true);
        }
        $upload_path .= "/$file_name";
        if(move_uploaded_file((string)$_FILES['upfile1']['tmp_name'], $upload_path)){
            execute_my_query(" update gft_customer_app_dtl set GCA_APP_LOGO='$upload_path' where GCA_ID='$ref_id' ");
        }
    }
    $resp['message'] = "Uploaded";
    echo json_encode($resp);
    save_mygofrugal_access($cust_user_id, $log_operation, "", "Uploaded");
}
?>
