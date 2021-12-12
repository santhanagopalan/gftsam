<?php
$api_key=isset($_GET['API_TOKEN'])?$_GET['API_TOKEN']:'';
global $uid;
if ($api_key != ""){
//NOTE: This API key is used by gitserverrpl - tool 
   if ($api_key != "1x2g2ym"){
	   die("Invalid api key");
   }
}else if ($uid == ''){
	die("Please do login into SAM <a href='/login.php'>here</a>");
}
?>
