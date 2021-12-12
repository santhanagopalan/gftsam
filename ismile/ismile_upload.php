<?php
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__.'/ismile_util.php');
require_once(__DIR__ . "/http_error.php");
require_once(__DIR__ . "/../lib_ods/smart_resize_image.function.php");

$startTime	=	(float) microtime(true);

addAccessControlAllowOrigin();
$key      	=	'';
$authtoken	=	'';
$key_emp_id	=	0;
$log_operation=	17;
$receive_arg=	"";
$uid	=	(string)mobileAppAuth();
$lead_code = isset($_REQUEST['lead_code'])?(string)$_REQUEST['lead_code']:'';
$attachment_id = isset($_REQUEST['attachment_id'])?(string)$_REQUEST['attachment_id']:'';
$attachment_purpose = isset($_REQUEST['attachment_purpose'])?(string)$_REQUEST['attachment_purpose']:'';
handle_file_errors();
$lead_check = get_single_value_from_single_table("GLH_LEAD_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
if($lead_check=='' && $attachment_purpose==''){
	sendErrorWithCode($receive_arg,"Invalid Lead Code($lead_code) ",HttpStatusCode::BAD_REQUEST);
	exit;
}
$image_folder = "Customer_Photos";
$image_sub_folder = "$lead_code";
if($attachment_purpose=='expense_bills'){
	$image_folder = "Expense_Bills";
	$image_sub_folder = "$uid";
}else if($attachment_purpose=='ticket_attachment'){
	$image_folder = "Support_Upload_Files";
	$unix_timestamp=time();
	$image_sub_folder = $attachment_id."/".$unix_timestamp;
}
$img_path = $thumpnail_path = '';
if( (isset($_FILES['upfile1'])) && is_array($_FILES["upfile1"]['size']) && count($_FILES['upfile1']['name'])>0 ){
	if(is_array($_FILES['upfile1']['size'])){
		$tot_size = array_sum($_FILES['upfile1']['size']);
		if($tot_size!=0){
			$uploadDir= "$attach_path/$image_folder/".date('Y')."/$image_sub_folder";
			$attachment_file_tosend=upload_files_to($uploadDir);
			$img_path=implode(',',$attachment_file_tosend);
		}
	}
}else if(isset($_FILES['upfile1']) && ((int)$_FILES["upfile1"]['size'] > 0) ){
	$file_name = date('YmdHis_')."".(string)$_FILES['upfile1']['name'];
	$upload_path = $attach_path."/$image_folder/".date('Y')."/$image_sub_folder";
	if(!file_exists($upload_path)){
		mkdir($upload_path,"0755",true);
	}
	if(move_uploaded_file((string)$_FILES['upfile1']['tmp_name'], $upload_path. "/" .$file_name)){
		$img_path = $upload_path. "/" .$file_name;
	}
	$temp_img = smart_resize_image($img_path, null, 128, 160,false,'return',false,false,$quality=100);
	$thump_file = date('YmdHis_')."thump_".(string)$_FILES['upfile1']['name'];
	$thumpnail_path = $upload_path."/".$thump_file;
	imagejpeg($temp_img, $thumpnail_path);
}

if($img_path!='' && $attachment_purpose==''){
	$ins_que = " insert into gft_customer_photo (GCP_LEAD_CODE,GCP_THUMBNAIL_PATH,GCP_IMAGE_PATH,GCP_UPDATED_ON,GCP_IMAGE_TYPE) values ".
			" ('$lead_code','$thumpnail_path','$img_path',now(),'1') ";
	execute_my_query($ins_que);
	$ret_data['status'] = 'success';
	$ret_data['message'] = 'Image Saved';
	echo json_encode($ret_data);
	save_access((int)$uid,'','',$receive_arg,"Successfully Uploaded");
}else if($attachment_purpose=='expense_bills'){
	$insert_arr  = array();
	$insert_arr['GEB_EXPENSE_ID']="$attachment_id";
	$insert_arr['GEB_THUMBNAIL_PATH']="$thumpnail_path";
	$insert_arr['GEB_IMAGE_PATH']="$img_path";
	$insert_arr['UPDATED_ON']=date("Y-m-d H:i:s");
	array_insert_query("gft_expense_bill_images", $insert_arr);	
	$ret_data['status'] = 'success';
	$ret_data['message'] = 'Image Saved';
	echo json_encode($ret_data);
	save_access((int)$uid,'','',$receive_arg,"Successfully Uploaded");
}else if($attachment_purpose=='ticket_attachment'){
	$upd_query =" update gft_customer_support_dtl join gft_customer_support_hdr on (GCH_LAST_ACTIVITY_ID=gcd_activity_id) ".
				" set GCD_UPLOAD_FILE = if(GCD_UPLOAD_FILE is null or GCD_UPLOAD_FILE='','$img_path',concat(GCD_UPLOAD_FILE,',','$img_path')) ".
				" where GCH_COMPLAINT_ID='$attachment_id' ";
	execute_my_query($upd_query);
}else{
	sendErrorWithCode($receive_arg,"Unable to Save File",HttpStatusCode::BAD_REQUEST);
}


?>
