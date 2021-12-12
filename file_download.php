<?php
/*. require_module 'session'; .*/
session_cache_limiter('none'); //*Use before session_start()
require_once(__DIR__ ."/dbcon.php");
$filename=(string)$_REQUEST['filename'];
$file_type=(string)$_REQUEST['file_type'];
$lead_code=isset($_REQUEST['lead_code'])?(string)$_REQUEST['lead_code']:'';
$type = isset($_REQUEST['type'])?(string)$_REQUEST['type']:'';
$content_dispostion = 'attachment';
if($type=='view'){
	$content_dispostion = 'inline';
}
$file_ext = '';
switch($file_type){
	case 'invoice':
		$f_location_path="$attach_path/invoice/$filename";
		$path_arr = pathinfo($filename);
		$file_ext = $path_arr['extension'];
		break;
	case 'quotation':
		$f_location_path="$attach_path/quotation/$filename";
		break;
	case 'receipt':
		$f_location_path="$attach_path/receipt/$filename";
		break;
	case 'collateral':
		$f_location_path="$attach_path/collateral/$filename";
		break;
	case 'onlinequote':
		$f_location_path="$attach_path/onlinequote/$filename";
		break;
	case 'proforma':
		$f_location_path="$attach_path/proforma/$filename";
		break;
	case 'addr_pdf':
		$f_location_path="$attach_path/temp_pdf_generator/$filename";
		break;
	case 'mail_sent':
		$f_location_path="$attach_path/$filename";
		break;
	case 'ndnc_request_files':
		$f_location_path="$attach_path/ndnc_request_files/$filename";
		break;
	case 'Feedback_Uploaded_Files':
		$f_location_path="$attach_path/Feedback_Uploaded_Files/$filename";
		break;
	case 'migration':
		$f_location_path="$attach_path/Migration_Template/$filename";
		break;
	case 'patch':
		$f_location_path="$attach_path/Patch_Log/$filename";
		break;
	case 'HQ_Proposals':
		$f_location_path="$attach_path/HQ_Proposals/$lead_code/$filename";
		break;
	case 'Dealer_Secret':
		$f_location_path="$attach_path/Dealer_Secret/$filename";
		break;
	case "migration_log":
		$f_location_path="$attach_path/Migration_dtl/$filename";
		break;
	case "print_profile":
		$f_location_path="$attach_path/print_profile/$filename";
		break;
	case "uberization":
		$f_location_path="$attach_path/uberization/$filename";
		break;
	case 'gst_registration':
		$f_location_path="$attach_path/gst_reg/$lead_code/$filename";
		break;
	case 'campus_drive':
		$f_location_path="$filename";
		$path_arr = pathinfo($filename);
		$file_ext = $path_arr['extension'];
		break;
	case 'bookings':
		$f_location_path="$attach_path/bookings/$filename";
		$path_arr = pathinfo($filename);
		$file_ext = $path_arr['extension'];
		break;
	case 'leave_summary':
	    $f_location_path="$attach_path/leave_summary/$filename";
	    $path_arr = pathinfo($filename);
	    $file_ext = $path_arr['extension'];
	    break;
	case 'installer_log':
	    $f_location_path="$attach_path/installer_log/$filename";
	    $path_arr = pathinfo($filename);
	    $file_ext = $path_arr['extension'];
	    break;
	default:
		$f_location_path='';
		break;
}

$f_location=realpath($f_location_path);
if ($f_location === FALSE){
	error_log("File not found: ".$f_location_path);
	die("File not found. Errorcode: 100-200");
}
$content_type_str = array('jpg'=>'image/jpg','jpeg'=>'image/jpg','png'=>'image/png','docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document','doc'=>'application/msword');
if($file_type=="ndnc_request_files" or $file_type=='mail_sent'){
	header('Content-Type: application/octet-stream');
}else if($file_type=="Feedback_Uploaded_Files" || $file_type=='migration_log' || $file_type=='installer_log'){
         header('Content-Type: application/zip');
}else if( ($file_type=='Dealer_Secret') || ($file_type=='print_profile') ){
	header('Content-Type: text/plain');
}else{
	header('Content-type: application/pdf');
	if(($file_type=='campus_drive' or $file_type=='bookings') and strcasecmp($file_ext,'pdf')!=0) {
		if(isset($content_type_str[$file_ext])) {
			header('Content-Type: '.$content_type_str[$file_ext]);
		}
	}
	if($file_ext == 'xls'){
	    header("Content-type: application/x-ms-download");
	}
}
header('Pragma: public'); 
header('Expires: 0'); 
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private',false);
header('Content-Description: File Transfer');
header("Content-Disposition: $content_dispostion; filename=".basename($filename));
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($f_location));
readfile($f_location);
?>
