<?php

require_once(__DIR__ ."/../dbcon.php");
require_once(__DIR__ ."/../ismile/ismile_util.php");
$id=(int)$argv[1];
$for_app = $argv[2];
$notifcation_id = $argv[3];
if($for_app==1){
	send_notification_to_mydelight_app($id,$notifcation_id);
}else if($for_app==2){
	send_notification_to_mygofrugal_app($id,$notifcation_id);
}
exit;

?>
