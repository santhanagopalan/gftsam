<?php
require_once (__DIR__.'/../dbcon.php');
global $uid;
if ($uid == ''){
	show_my_alert_msg("Your Session has Expired");
	die("Please do login into SAM <a href='/login.php'>here</a>");
}
?>

<br>

   <a href=randuser.php>Random User Selection Tool</a>
