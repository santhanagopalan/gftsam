<?php
require_once(__DIR__ ."/dbcon.php");

$session=session_id(); 
$time=time();
$uid=(string)$_SESSION['uid'];
$tbl_name="gft_user_online";
$sql2="UPDATE $tbl_name SET guo_time='$time' WHERE guo_session = '$session' and guo_uid='$uid' ";
$result2=execute_my_query($sql2,$me,true,false,4);
?>
