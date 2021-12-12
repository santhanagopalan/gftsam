<?php
/* *
 * 1.session checking
 * 2.add html,head,js scripts
 * 3.accesses checking, tab, menu selection
 * 4.add tab,menu table
session "uid" "uname" "ptype" "lastlogin" "group_id" "tab_id" "tabselectid"
 * 5. Cookie  setting
*/
require_once(__DIR__ ."/dbcon.php");
//global $loginid;
$request_from = basename($me);
$form_pages = array('order_details.php','saas_order_details.php');
if($global_gst_mode==1 and in_array($request_from,$form_pages)) {
	if(isset($_REQUEST['lead_code'])) {
		$is_valid = validate_state_code_gstin($_REQUEST['lead_code'],true);
		if(!$is_valid) {
			exit;
		}
	}
}
$folder_depth = substr_count($_SERVER["PHP_SELF"] , "/");
$base_relative_path=str_repeat("../", $folder_depth - 1);
$chek_user_online=true;
if(!isset($_SESSION['uid']) and  isset($_REQUEST['uname']) and (string)$_REQUEST['uname']!=''){
	/*$ip= $_SERVER['REMOTE_ADDR'];
	if(strpos(get_samee_const("LOCAL_IP_ADDRESS_SETUPWIZARD"),$_SERVER['REMOTE_ADDR'])===false && $_SERVER['HOST_NAME']!='localhost' ){
		die("Permission Denied for IP address: " . $ip);
	}*/
	if(isset($_GET['usnp']) and isset($_REQUEST['uname'])){	
		$pwd=$_GET['usnp'];
		$query="Select gem_emp_id,glm_login_name,glm_password from gft_emp_master " .
			   "join gft_login_master lm on (glm_emp_id=gem_emp_id and glm_login_name=gem_email and GLM_PASSWORD='$pwd' )" .
		       "where gem_email='".(string)$_REQUEST['uname']."' and gem_status='A' ";
		$result=execute_my_query($query);
		if((mysqli_num_rows($result) ==1)){
			$qd=mysqli_fetch_array($result);
			$emp_id=$qd['gem_emp_id'];
		}
		
	}
	session_destroy();
	$session=session_id();
	$_SESSION['uid']=$uid="".get_user_id((string)$_REQUEST['uname']);
	$_SESSION["uname"]=$_REQUEST['uname'];
	$role_dtl=get_role_emp_id((string)$_SESSION['uid']);
	$_SESSION['roleid']=$role_dtl[0];	
	$_SESSION['mobile_no']=$role_dtl[2];
	
	$query_online_user="SELECT * FROM gft_user_online " .
			"join gft_emp_master em on (gem_emp_id=guo_uid and gem_status='A') " .
			" where guo_uid=$uid ";
	$result_online_user=execute_my_query($query_online_user);
	if(mysqli_num_rows($result_online_user)!=1 && !isset($_GET['usnp'])){
		die("Please login into SAM in any one of the browser before using Intellicon ");
	}else if($qdata=mysqli_fetch_array($result_online_user)){
		$session=$qdata['guo_session'];
		$_SESSION['gfl_access_no']=$loginid=$qdata['guo_access_id'];
	}
	$chek_user_online=false;
}else if((isset($_SESSION["uid"]) and isset($_SESSION["uname"])  and isset($_SESSION["roleid"]) )){
	$uid=(string)$_SESSION["uid"];
	$query_online_user="SELECT * FROM gft_user_online " .
			"join gft_emp_master em on (gem_emp_id=guo_uid and gem_status='A') " .
			" where guo_uid=$uid ";
	$result_online_user=execute_my_query($query_online_user);
	if(mysqli_num_rows($result_online_user)!=1 && isset($_REQUEST['uname'])){
		
		header("Location: login.php");
		session_destroy();
		if(!isset($_COOKIE["Requested"]))
		setcookie("Requested",$_SERVER['REQUEST_URI'], time()+300);
		exit;	
	}else if($qdata=mysqli_fetch_array($result_online_user)){
		$session=$qdata['guo_session'];
		$_SESSION['gfl_access_no']=$loginid=$qdata['guo_access_id'];
	}
	$chek_user_online=false;
}else if(!isset($_SESSION['uid'])){
	
	header("Location: {$base_relative_path}login.php");
	session_destroy();
	if(!isset($_COOKIE["Requested"]))
		setcookie("Requested",$_SERVER['REQUEST_URI'], time()+300);
	exit;	
	
}


//error_reporting(0); 
//error_reporting(E_ALL ^ E_NOTICE);
if($chek_user_online==true){
	$query_online_user="SELECT * FROM gft_user_online where guo_session='$session' and guo_access_id='$loginid'";
	if(!$data=mysqli_fetch_array(execute_my_query($query_online_user,'',true,false,4))){
		header("Location: login.php");
		session_destroy();
		setcookie("Requested",  substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],"/")+1), time()+300);
		exit;
	}
}

//3.accesses checking, page forward, tab,menu selection
$uid=(string)$_SESSION["uid"];
$i_date_on=date('d-m-Y');
$date_on=date('Y-m-d');
$roleid=(int)$_SESSION["roleid"];
$me=$_SERVER['PHP_SELF'];
$mypath=substr($me,strrpos($me,"/")+1);
$menu_name='';
$menuq= "SELECT m.menu_name,m.mid FROM gft_menu_master m where m.menu_path='$mypath' ";
$resultmenu=execute_my_query($menuq,$me,true,false,4);
if($data=mysqli_fetch_array($resultmenu)){
	$menu_name=$data['menu_name'];
	$report_id=$data['mid'];
}
$API_TITLE=($menu_name!=''?$menu_name.' - ':''). get_samee_const('SAMEE_TITLE');
//global $cp_lcode,$cp_terrid,$cp_roleid,$cp_name,$cp_user_id,$gft_partner,$non_employee_group;
//global $admin_group,$non_emp_group;
 $admin_group=false;$non_emp_group=false;
if(is_authorized_group($uid,1)){
	$admin_group=true;
}
if(is_authorized_group_list($uid,$non_employee_group)){
	$non_emp_group=true;
}
$show_query_val=isset($_REQUEST['show_query'])?(int)$_REQUEST['show_query']:0;
$show_explain_val=isset($_REQUEST['show_explain'])?(int)$_REQUEST['show_explain']:0;
$show_query     =($show_query_val==1 && is_authorized_group($uid, 147))?true:false;
$show_explain   =($show_explain_val==1 && is_authorized_group($uid, 147))?true:false;
/* moved to inc.cp_admin.php 
   -- Need to check it up proper...
$cp_lcode='';
if(is_authorized_group_list($uid,$non_employee_group)){
	$sql1=" SELECT CGI_LEAD_CODE,GCA_CP_TYPE,GCA_CP_SUB_TYPE,cgi_incharge_emp_id,GEM_ROLE_ID,GEM_EMP_NAME,glh_territory_id,CGI_PARTNER_RELATION, GCR_LEAD_CODE " .
		" FROM gft_leadcode_emp_map " .
		" join gft_emp_master em on(GLEM_EMP_ID=GEM_EMP_ID)" .
		" join gft_cp_info gcp on (GLEM_LEADCODE=CGI_LEAD_CODE) " .
		" join gft_cp_agree_dtl cg on (CGI_lead_code=gca_lead_code AND CGI_CP_AGREENO=gca_cp_agreeno) " .
		" join gft_lead_hdr lh on(CGI_LEAD_CODE=glh_lead_code)" .
		" left join gft_cp_relation cr on(gcr_reseller_lead_code=cgi_lead_code and gcr_cp_level=1) " .
		" where GEM_EMP_ID='$uid'";
	$rs1=execute_my_query($sql1,$me,true,false,4);
	if($row1=mysqli_fetch_array($rs1)){
		$cp_lcode =$row1['CGI_LEAD_CODE'];
		$cp_terrid=$row1['glh_territory_id'];
		$cgi_incharge_emp_id=$row1['cgi_incharge_emp_id'];
		$cp_roleid=$row1['GEM_ROLE_ID'];
		$cp_name=$row1['GEM_EMP_NAME'];
		$cp_relation=(int)$row1['CGI_PARTNER_RELATION'];
		$CP_TYPE=$row1['GCA_CP_TYPE'];
		$CP_SUB_TYPE=$row1['GCA_CP_SUB_TYPE'];
		if($cp_relation==2){
			$gft_partner=$row1['GCR_LEAD_CODE'];
		}else{
			$gft_partner=$cp_lcode;	
		}
		$cp_user_id=$uid;
	}
}
*/
/*why this again already in two  pages */
$date_format_query="%d-%m-%Y";
$lockdate=date("Y-m-d", mktime(0, 0, 0, (int)date("m"), (int)date("d")-6, (int)date("Y")));
$collection_lockdate=date("Y-m-d", mktime(0, 0, 0, (int)date("m")+1, (int)date("d"), (int)date("Y")));
$today_date=date('Y-m-d');
$today_datentime=date('Y-m-d H:i:s');
$alt_row_class=array("oddListRow","evenListRow","redListRow","roseListRow","blueListRow","yellowListRow","greenListRow","asscustbg","");
/* Activity block */
$select_activity_que="select GMD_DEP1 from gft_mskip_dtl where GMD_CODE=4 AND GMD_NAME='Activity_block' and gmd_status='A' ";
$result_activity=execute_my_query($select_activity_que,$me,true,false,4);
$num_rows=mysqli_num_rows($result_activity);
$block_activity="";
$accept_from_activity_date='';
/* 11 - Development Group ,36 -PCS Team */
if(isset($_SESSION['uid'])  and $num_rows==1 and !is_authorized_group_list($uid,array(11,36))){
	$qd=mysqli_fetch_array($result_activity);
	$block_activity="true";
	$activity_block_date=(int)$qd[0];
	$cd = strtotime($today_datentime);
	$retDAY = date('Y-m-d', mktime(0,0,0,(int)date('m',$cd),(int)date('d',$cd)-$activity_block_date,(int)date('Y',$cd))); 
	$accept_from_activity_date=$retDAY;
}
//global $cp_lcode,$loginid;
//<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml2/DTD/xhtml1-strict.dtd">
header("Cache-Control: no-cache, must-revalidate");
header("Expires: ". gmdate("D, d M Y H:i:s", time() + 3600) . " GMT");
$sales_skew_type = SALES_TAX_ORDER_ENTRY;

	$css_files = include_css_file("{$base_relative_path}CSS/style.css"); //to overcome caching when modified
	$css_files .= include_css_file("{$base_relative_path}CSS/ajax.css");
	$css_files .= include_css_file("{$base_relative_path}CSS/calendar-blue.css");
	$css_files .= include_css_file("{$base_relative_path}CSS/jquery-ui.css");
	$css_files .= include_css_file("{$base_relative_path}libs/jquery.datetimepicker/jquery.datetimepicker.css");
	$js_files =  include_js_file("{$base_relative_path}js/js_common_util.js");
	
	
try {
	$last_modified_time = filemtime("{$base_relative_path}CSS/style.css");
}catch(Exception $e){
	die("Exception in: ".$e);
}
echo<<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html><head><title>$API_TITLE</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
$css_files
<script type="text/javascript">
var Start = new Date(); //to find page load time start
var test=false;
var today_date='$today_date';
var today_datentime='$today_datentime';
var nextmonth_date='$collection_lockdate';
var date_format_filter="%d-%m-%Y";
var lockdate="$lockdate";
var order_block=false;
var activity_block=false;
var sam_access_login_id ="$loginid";
var cp_lead_map_code="$cp_lcode";
var API_title="$API_TITLE";
var sales_skew_type='$sales_skew_type';
var originalKeys = Object.keys;
</script>
END;
$use_jquery=isset($use_jquery)?$use_jquery:0;

$use_prototype	= isset($use_prototype)?$use_prototype:1;

//Not a pure  JQuery.... ie $() will not run
if ($use_jquery == 0){
echo <<<END
<script type="text/javascript" src="{$base_relative_path}js/jquery.js"></script>
END;
}
if($use_prototype==1){
	echo <<<END
<script type="text/javascript" src="{$base_relative_path}js/prototype.js"></script>
<script type="text/javascript" src="{$base_relative_path}js/ajaxtags-1.1.js"></script>
<script type="text/javascript" src="{$base_relative_path}js/ajax_init.js"></script>
END;
}

echo <<<END
<script type="text/javascript" src="{$base_relative_path}js/calender_bundle.js"></script>
<script type="text/javascript" src="{$base_relative_path}js/js_menu.js"></script>
</head>
END;
if($use_jquery == 1){
echo <<<END
<script type="text/javascript" src="{$base_relative_path}js/jquery.js"></script>
<script type="text/javascript" src="{$base_relative_path}js/jquery-ui.js"></script>
 <script>
     jQuery.noConflict();
 </script>
END;
}
flush();
$js_files .=  include_js_file("{$base_relative_path}libs/jquery.datetimepicker/jquery.datetimepicker.full.min.js" );
echo<<<END
$js_files
<link rel='stylesheet' href='{$base_relative_path}CSS/fontawesome-all.css'>
<body>
<div id="dhtmltooltip"></div>
<script>
 /* Date time picker */
     jQuery(document).ready(function(){
        if(jQuery('#donv').length>0 || jQuery('#demo_date').length>0) {
        	jQuery('#donv,#demo_date').datetimepicker({
        		format:'Y-m-d H:i',
        		step: 15 
        	});
        }
    });
</script>
END;
require_once(__DIR__ ."/access_util.php");
require_once(__DIR__ ."/session_maintain.php");
require_once(__DIR__ ."/common_util.php");
?>
