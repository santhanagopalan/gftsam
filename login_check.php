<?php
require_once(__DIR__ ."/dbcon.php");
require_once(__DIR__ ."/access_util.php");
require_once(__DIR__ ."/menu_util.php");
require_once(__DIR__ ."/function.send_sms.php");
require_once(__DIR__ ."/common_util.php");
$err_msg = $ret_path = ""; $captcha_error="";
$purpose     = isset($_REQUEST['purpose'])?(string)$_REQUEST['purpose']:'';
callFloodProtection("login_check");
if($purpose=='session'){
    $session = false;
    if((isset($_SESSION["uid"]) and isset($_SESSION["uname"])  and isset($_SESSION["roleid"]))){
        $session = true;
    }
    echo json_encode($session);
    exit;
}
if( isset($_SESSION["uid"]) && isset($_SESSION["uname"]) && isset($_SESSION["roleid"]) && ($purpose!='login') ){
    show_firstpage_login();
    exit;
}
$from_page = isset($_POST['from'])?(string)$_POST['from']:'';
if(isset($_POST['action']) and $_POST['action']=='Passwordchange'){
	$user_name=mysqli_real_escape_string_wrapper($_POST['user_name']);
	$err_msg=generate_login(null,$user_name,true);
	if($from_page=='mydelight'){
		header('Content-Type: application/json');
		$resp_arr['message'] = $err_msg;
		echo json_encode($resp_arr);
		exit;
	}
}else if(isset($_POST['module']) and isset($_POST['user_name']) and isset($_POST['user_password'])){  
    $ret_arr = /*. (string[string]) .*/array();
	$auth_error_reason='';
	$g_captcha  = isset($_POST['grecaptcha'])?(string)$_POST['grecaptcha']:'';
	if(get_samee_const("google_captcha_activation")){
    	$captcha_score = get_recaptcha_score($g_captcha);
    	$username = false;
    	if($captcha_score < 0.5){
    	    $auth_error_reason  = "Uh ho! We encounter a problem processing the reCAPTCHA. Please wait till the page reloads automatically.";
    	    mail_error_alert("SAM Login spam request as per RECAPTCHA", json_encode($_POST),7);
    	    $captcha_error = "error";
    	    $return_array = $_POST;
    	    insert_web_request_log($_POST['module'],'','55',$return_array);
    	}else{
    	    $username=auth_user($_POST['user_name'],$_POST['user_password'],$auth_error_reason,false);
    	}
	}else{
	    $username=auth_user($_POST['user_name'],$_POST['user_password'],$auth_error_reason,false);
	}
	if(!$username){
		if ($auth_error_reason != ''){
			$err_msg=$auth_error_reason;
		}else{
			$err_msg="Authorization failed. Please try again else click Password Request !";
			$data = $_POST;
			if(isset($data['user_password'])){
			    $data['user_password']="";
			}
			insertLoginFailure("login_check.php","$err_msg",$data);
		}
	}else{
		$query    =   " select GEM_EMP_NAME,gem_role_id,GEM_MOBILE,GEM_OFFICE_EMPID,GEM_EXTERNAL_ACCESS,GEM_EMP_TYPE,GEM_EMP_ID,GLM_NEED_PASSWORD_RESET ".
		  		      " from gft_emp_master join gft_login_master on (GEM_EMP_ID=GLM_EMP_ID) where GEM_EMP_ID='$username' ";
		$result=execute_my_query($query,'',true,true);
		$qdata=mysqli_fetch_array($result);
		$user_name=$qdata[0];
		$roleid=$qdata[1];
		$mobile_no=$qdata[2];
		$gem_office_empid=$qdata[3];
		$gem_external_access=$qdata[4];
		$emp_type = $qdata[5];
		$need_pwd = (int)$qdata['GLM_NEED_PASSWORD_RESET'];
		if( ($need_pwd=='1') && ($purpose=='login') ){
		    echo json_encode(array('changePassword'=>true,'employeeId'=>$qdata['GEM_EMP_ID']));
		    exit;
		}
		$local_ip_list=get_samee_const("LOCAL_IP_ADDRESS_SETUPWIZARD").',127.0.0.1';
		if(!is_authorized_group($username, 148) && ($emp_type=='3') and (strpos($local_ip_list,$_SERVER['REMOTE_ADDR'])===false) and ($gem_external_access==0) ) {
	        $err_msg = "Sorry. You are not authorized to access from external network. Contact HR Team or Sysadmin Team";
		} else {
    	if ($gem_office_empid >0 && $gem_external_access == 0){
//     		echo "local_ip_list=".$local_ip_list;
//     		echo "gem_office_empid=".$gem_office_empid . " gem_external_access=".$gem_external_access . " username=".$user_name;
    		if(strpos($local_ip_list,$_SERVER['REMOTE_ADDR'])===false){
    			$roleid = '84';
// 				insertHoneyPot("login","Not authorized from external network",$qdata);
// 				die("Sorry. You are not authorized to access from external network. Contact HR Team or Sysadmin Team");
    		}
    	}
    	$query2="select max(gfl_start_time) from gft_access_log where gfl_login_id='$username'";
    	$result2=execute_my_query($query2);
    	$qdata2=mysqli_fetch_array($result2);
    	$last_login=$qdata2[0];     
    	if($user_name=='admin'){
    		$roleid='0';
    	}
    	if($_POST['user_name']==$_POST['user_password']){
    		$_SESSION['personalize']=1;
    	}
    	require_once(__DIR__ ."/build_sales_server.inc");
    	$_SESSION["uid"]=$username;
    	$_SESSION["uname"]=$user_name;
    	$_SESSION["lastlogin"]=$last_login;
    	$_SESSION["mobile_no"]=$mobile_no;
    	$_SESSION["roleid"]=(int)$roleid;
    	$_SESSION["build_date"]=BUILD_DATETIME;
    	setcookie("SAM_USER_ID",$username);
    	setcookie("SAM_USER_NAME",$user_name);
    	$date_time_now=date('Y-m-d H:i:s');
    	$remote_addr=$_SERVER['REMOTE_ADDR'];
    	$HTTP_USER_AGENT=$_SERVER['HTTP_USER_AGENT'];
    	$HTTP_ACCEPT_ENCODING=(isset($_SERVER['HTTP_ACCEPT_ENCODING'])?$_SERVER['HTTP_ACCEPT_ENCODING']:'');
    	
    	
    	$query=" SELECT a.gfl_login_id,max(a.gfl_access_no),GLM_LAST_LOGIN_DATE " .
    			"FROM gft_access_log a ,gft_login_master" .
    			" WHERE GLM_EMP_ID=gfl_login_id and  gfl_login_id='$username' group by gfl_login_id ";
    	$result=execute_my_query($query);
    	if($data=mysqli_fetch_array($result)){
    		$lastliginid=$data[1];
    		$logout_time=$data[2];
    		execute_my_query("update gft_access_log set gfl_end_time='$logout_time' " .
    				"where gfl_login_id='$lastliginid' and gfl_end_time='0000-00-00 00:00:00'");
    		execute_my_query("delete from gft_user_online where guo_access_id='$lastliginid'");  
    	}
        $insert_access_log="insert into gft_access_log(gfl_login_id,gfl_start_time,gfl_end_time,GFL_REMOTE_ADDR," .
        		"GFL_USER_AGENT,GFL_ENCODING) values ('$username','$date_time_now','0000-00-00 00:00:00'," .
        		"'$remote_addr','$HTTP_USER_AGENT','$HTTP_ACCEPT_ENCODING')";
    	$result_access_log=execute_my_query($insert_access_log);
    	
    	if($result_access_log){
    		$id_last_insert=mysqli_insert_id_wrapper();
    		$_SESSION["gfl_access_no"]= $id_last_insert; 
    		$session=session_id();
    		$time=time();
    		$uname=$_SESSION['uname'];
    		$uid=$_SESSION['uid'];
    		$sql1="INSERT INTO gft_user_online(guo_session, guo_time,guo_uid,guo_uname,guo_access_id) " .
    				"VALUES('$session','$time','$uid','$uname','$id_last_insert')";
    		$result1=execute_my_query($sql1); 
    	}
    	$ret_path = show_firstpage_login('get');
		}
	}
    if($err_msg!='') {
        $ret_arr['status'] = "failure";
        $ret_arr['error']  = $err_msg;
        $ret_arr['captcha_error']  = $captcha_error ;
    }else{
        $ret_arr['status'] 		= "success";
        $ret_arr['redirect_to'] = $ret_path;
    }
    echo json_encode($ret_arr);
    exit;
}//END of if
?>
