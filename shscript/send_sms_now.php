<?php

require_once(__DIR__ ."/../dbcon.php");
require_once(__DIR__ ."/../file_util.php");
require_once(__DIR__ ."/../netcore_api/netcore_util.php");

$send_via_others = true;  //set it to false to always send via netcore

$sms_id=$argv[1];
$category=isset($argv[2])?(int)$argv[2]:0;
$intl_sms=isset($argv[3])?(int)$argv[3]:0;
$sender_id=isset($argv[4])?(string)$argv[4]:"";

$otp_sms = false;
if(in_array($category, array(143,161,172,195,196,197,198,204,205,208,211,212,214))){
	$otp_sms = true;	
}
if( ($send_via_others) && ($otp_sms) ){
	send_sms_by_api($sms_id,$intl_sms,$sender_id);
}else{
    $use_kaleyra_gateway = (int)get_samee_const("ENABLE_KALEYRA_SMS_GATEWAY");
    if($use_kaleyra_gateway==1){
        send_sms_kaleyra_by_smsid('Y',$sms_id);
        send_sms_kaleyra_by_smsid('N',$sms_id);
    }else{
        send_sms_netcore_by_smsid('Y',$sms_id);
        send_sms_netcore_by_smsid('N',$sms_id);
    } 
    
}
exit;


?>
