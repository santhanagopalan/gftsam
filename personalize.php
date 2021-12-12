<?php
require_once(__DIR__ ."/menu.php");
$date_on=date('Y-m-d');
//global $uid=$_SESSION["uid"];
$uname='';
$query= "select GLM_LOGIN_NAME from gft_login_master where GLM_EMP_ID='$uid'"; 
$result=execute_my_query($query);
$err_msg='';
if($data=mysqli_fetch_array($result)){
	$uname=$data['GLM_LOGIN_NAME'];
}
echo "<font  size=\"2\" class=\"mandatory_marker_red\" >";
if((isset($_POST['module'])=="password_change")){
    $username   = $_POST['user_name'];
	$passwd     = $_POST['user_password'];
	$newpasswd  = $_POST['new_password'];
	$newpasswd1 = $_POST['new_password1'];
	$validation_msg    = "";
	$auth_error_reason = "";
	$login_emp_id      = 0;
	$password_to_update = sam_password_hash(md5($newpasswd));
	$is_passed_validation   = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*&(#)_])[A-Za-z\d@$!%*&(#)_]{8,}$/', $newpasswd);
	if( empty($username) || empty($passwd) || empty($newpasswd) || empty($newpasswd1) ){
	    $validation_msg = "Please fill all the fields";
	}else if($username==$newpasswd){
	    $validation_msg = "Username and new password should not be the same";
	}else if(strlen($newpasswd)<=7){
	    $validation_msg = "Minimum 8 characters required for password";
	}else if($newpasswd!=$newpasswd1){
	    $validation_msg = "New and Confirm password should be same";
	}else if(!$is_passed_validation){
	    $validation_msg = "Password must contain atleast 8 characters including uppercase, lowercase, number and a special character. <br />Special characters allowed ! @ # $ % & * ( ) _";
	}else{
	    $login_emp_id = (int)auth_user($username, $passwd, $auth_error_reason);
	    $current_password = get_single_value_from_single_table("GLM_PASSWORD", "gft_login_master", "GLM_EMP_ID", $login_emp_id);
	    if($login_emp_id==0){
	        $validation_msg = "Incorrect old password";
	    }else if($current_password==$password_to_update){
	        $validation_msg = "New password shouldn't be your current password. Please change it";
	    }
	}
	if($validation_msg!=''){
	    echo "<font  size=\"2\" class=\"mandatory_marker_red\" >";
	    echo "<B><CENTER>$validation_msg</CENTER></B>";
	    echo "</font>";
	}else{
	    update_employee_password($login_emp_id, md5($newpasswd));
	    sync_employees_to_authservice($uid);
	    show_alert_and_close("New password updated successfully.", false);
	    echo "<script>location.replace('logout.php');</script>";	    
	    session_destroy();
	}
}
echo<<<END
<form action="personalize.php" method="post" name="personalizedform" id="personalizedform">
	<table cellpadding="2" width="100%" cellspacing="0" border="0">
		<tr><td align="center">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr><td><span class="dstyle"><font color="red">$err_msg</font></style></td></tr>
					<tr><td><input type="hidden" name="module" value="password_change">
							<input type="hidden" name="action" value="Authenticate">
							<input type="hidden" name="return_module" value="Users">
							<input type="hidden" name="return_action" value="Login">
							<table cellpadding="0" width="100%" cellspacing="0" border="0" class="leftFormHeader">
								<tr><td class="content_txt"><span class="sprite_users"></span>&nbsp;Change Password</td></tr>
							</table>
					</td></tr>
					<tr><td>
						<table cellspacing="5" border="0" class="leftFormBorder2" bgcolor="#FFFFFF">
							<tr><td class="content_txt">User Name:</td>
								<td><input type="text" size='50' name="user_name"  value="$uname" readonly></td></tr> 
							<tr><td class="content_txt">Old Password:</td>
								<td><input type="password" size=50  name="user_password" value="" maxlength="150" ></td></tr>
							<tr><td class="content_txt">New Password:</td>
								<td><input type="password"  size='50' name="new_password" value="" maxlength="150"></td></tr>
							<tr><td class="content_txt">Confirm New Password:</td>
								<td><input type="password" size='50' name="new_password1" value="" maxlength="150"></td></tr>
							<tr><td>&nbsp;</td>
								<td><input title="" accesskey="L" class="button" type="submit" name="Login" value=" CHANGE PASSWORD "></td></tr>
							<tr><td colspan='2'>
								<font  size="2" class="mandatory_marker_red" >*</font>Username and Password should not be same. <br/> 
								<font  size="2" class="mandatory_marker_red" >*</font>Password must contain atleast 8 characters including uppercase, lowercase, number and a special character.<br />
                                <font  size="2" class="mandatory_marker_red" >*</font>Special characters allowed ! @ # $ % & * ( ) _
								</td></tr>
						</table>
					</td></tr>
				</table>
		</td></tr>
	</table>
</form>
END;
require_once(__DIR__ ."/footer.php");
?>
