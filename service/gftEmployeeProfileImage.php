<?php
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__.'/../chat_util.php');
header('Content-Type: application/json');
addAccessControlAllowOrigin();
$data = $_REQUEST;
$empId 	=	isset($data['empId'])?(int)($data['empId']):0;
$default_image = "./../images/Profile.png";
if($empId>0){
	$emp_url = get_single_value_from_single_table("GEM_PROFILE_URL", "gft_emp_master", "GEM_EMP_ID", "$empId");
	if($emp_url!=""){
		$default_image = $emp_url;
	}
}
$file_extension = strtolower(substr(strrchr($default_image,"."),1));
$fp = fopen($default_image, 'rb');
header("Content-Type: image/$file_extension");
header("Content-Length: " . filesize($default_image));
fpassthru($fp);
exit;
?>
