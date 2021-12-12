<?php
/*. require_module 'standard'; .*/
/*. require_module 'session'; .*/
/*. require_module 'mysql'; .*/
/*. require_module 'mysqli'; .*/
/*. require_module 'apache'; .*/
/*. require_module 'zlib'; .*/
/*. require_module 'pcre'; .*/
/*. require_module 'json'; .*/

/*. forward resource function execute_my_query(string $query,string $from_page=,bool $send_mail_alert=,bool $show_error_msg=,int $log_level=); .*/
/*. forward bool function is_authorized_group(string $user_id,int $group_id,mixed $role_id=); .*/
/*. forward bool function is_authorized_group_list(string $user_id,int[int] $group_id_s,mixed $role_id=); .*/
/*. forward int[int] function get_group_under_privilage(string $privilage_id); .*/
/*. forward string function get_samee_const(string $key_name); .*/
/*. forward int function microtimediff(string $a, string $b); .*/
/*. forward void function call_my_show_query(string $query,int $deltaTime); .*/
/*. forward void function call_my_explain_query(string $query); .*/
/*. forward string function encode_str(string $in) throws ErrorException; .*/
/*. forward string[int][int] function get_two_dimensinal_result_set_from_query(string $query); .*/
/*. forward boolean function write_to_file(string $folder_name,string $content,string $filename,string $mode=,boolean $move_onemore_folder=); .*/
/*. forward void function call_mysql_READ_UNCOMMITTED(); .*/
/*. forward string function get_user_info(boolean $content_type=); .*/
/*. forward string function getStackTraceString(); .*/
/*. forward boolean function is_holiday(string $date,boolean $check_general_shift=); .*/
/*. forward int function time_to_seconds(string $time_hms); .*/
/*. forward float function getDeltaTime(); .*/
/*. forward string function get_receipt_id(); .*/
/*. forward string function get_help_link(string $key); .*/
/*. forward void function closeDBConnection(); .*/
/*. forward string function get_samee_const_cache(string $key_name); .*/



session_start();
$session=session_id();

if (function_exists('apache_note')){
	apache_note('sessionid',session_id());
	if(isset($_SESSION['uid'])){
		apache_note('uid',$_SESSION['uid']);
		apache_note('uname',$_SESSION['uname']);
	}
}

//GLOBAL variable 
$startTime=(float)microtime(true);


//require_once(dirname(__FILE__)."/log.php");
require_once __DIR__ . '/errors.php';
require_once __DIR__ . '/log.php';
require_once(__DIR__ . "/skip_for_development.php");
require_once(__DIR__."/cache_impl.php");
require_once(__DIR__.'/FloodProtection.php');
//GLOBAL variables
$non_employee_group=array(13,14,21,37,38,31,39);
$me=$_SERVER['PHP_SELF'];
$alt_row_class=array("oddListRow","evenListRow");
$global_dealer_pcodes_arr = array('550','551');
$cp_lcode=''; //This value will be overwritten in auth_util.php
$show_google_analytics=true; //Default


/**
 * @return resource
 */
function my_connect_db(){
	global $dbname,$dbuser,$dbpass, $db_constr;
	try{
		$conn=mysqli_connect($db_constr,$dbuser,$dbpass);
		//mysqli_set_charset_wrapper('utf8',$conn); //line commented for search not found issue
		if($conn !== FALSE){
			mysqli_select_db($conn, $dbname);
			return $conn;
		}else {
			echo "Not able to Connect to Db";
			exit;
		}
	}catch(ErrorException $e){
		error_log("Exception ". $e->__toString());
		header("X-PHP-Response-Code: 503",true,503);
		die("Not able to connecto DB");
	}
}

/**
 * @return mysqli
 */
function myi_connect_db(){
	global $dbname,$dbuser,$dbpass, $db_constr;
	$conn=mysqli_connect($db_constr,$dbuser,$dbpass);
	mysqli_set_charset_wrapper($conn,'utf8');
	if($conn !== FALSE){
		mysqli_select_db($conn,$dbname);
		return $conn;
	}else {
		echo "Not able to Connect to Db";
		exit;
	}
}

/**
 * @param string $customernametype
 *
 * @return void
 */
function ajax_session_check($customernametype=''){
	//global $login_uid;
	global $non_employee_group,$me;
	global $cp_lcode;

	$login_uid='';
	$loginid=isset($_REQUEST['sam_access_login_id'])?(int)$_REQUEST['sam_access_login_id']:0;
	if($loginid!=0){
		$query="SELECT guo_uid,max(gtl_from_time)max_from_time, date_sub(now(), interval 55 minute) avl_time FROM gft_access_track_log,gft_user_online where gtl_access_no=$loginid " .
			" and guo_access_id=gtl_access_no GROUP BY gtl_access_no";

		$result=execute_my_query($query,$me,true,false,3);
		if($data=mysqli_fetch_array($result)){
			$lastacces=(int)$data['max_from_time'];
			$sessionlimit=(int)$data['avl_time'];
			$login_uid=$data['guo_uid'];
			if(is_authorized_group_list($login_uid,$non_employee_group)){
				$query="SELECT glem_emp_id, glem_leadcode,gem_role_id " .
					" FROM gft_leadcode_emp_map join gft_emp_master on(GEM_EMP_ID=glem_emp_id)" .
					" where GLEM_EMP_ID=$login_uid ";
				$result=execute_my_query($query,$me,true,false,3);
				while($data=mysqli_fetch_array($result)){
					$cp_lcode=$data['glem_leadcode'];
				}
			}
			if($lastacces < $sessionlimit){
				echo ($customernametype!=''? "<sent_keyword>".htmlentities($customernametype)."</sent_keyword> ":"");
				echo "<item><name>";
				echo htmlentities('Session Time Out');
				echo "</name><value>";
				echo htmlentities('######');
				echo "</value></item>";
				echo "</response></ajax-response>";
				exit;
			}
		}else{
			echo ($customernametype!=''? "<sent_keyword>".htmlentities($customernametype)."</sent_keyword> ":"");
			echo "<item><name>";
			echo htmlentities('Session Time Out');
			echo "</name><value>";
			echo htmlentities('######');
			echo "</value></item>";
			echo "</response></ajax-response>";
			exit;
		}

	}
}

/**
 * @param string $key_name
 *
 * @return string
 */
function get_samee_const($key_name){
	$query="select GSC_VALUE from gft_samee_const where GSC_NAME='$key_name'";
	$result=execute_my_query($query,/*$from_page=*/null,/*$send_mail_alert*/true,/*$show_error_msg*/false,/*$log_level*/4);
	if($data_key_words=mysqli_fetch_array($result)){
		return $data_key_words['GSC_VALUE'];
	}
	return '';
}

$loginid=0;
$auth_gr_to_change_rc=17;
$auth_order_approval_group=array(18);
$auth_gr_employee_details=array(19);
//order plac 
$list_to_cp="1,2,3,4,11,13,14,16,18,7,15,6,5,9,10,8,19";
$privilage_to_place_order_for_salesgroup=/*. (int[int]) .*/ array();
$lead_status_not_to_followup=array(5,6,7,23,24);
$auth_gr_edit_cust_name=array(1,19,8);
$server_skew_property="1,11,24";
define("MAIN_SERVER_ORDER_ENTRY","1,3,11,13,14,16,17,18,20,21,19,22,24,25");
define("SUBSCRIPTION_RENEWAL_ORDER_ENTRY","11,13,16,17,20,21,22,24,25,26");
define("SERVER_SKEW_PROPERTY_BH","1,11,24,18");/* included plan for saas*/
define('ADDITIONAL_CLIENT_SKEW_PROPERTY_BH','3,13,14,16,20,21');
define('LEAD_OWNER_SKIP_GROUP','13,36,66,106'); // Partner,PCS,Field Sales and Intl Sales - Emp groups to skip while assigning next action / lead to CST
$month_arr_title=array("Apr","May","Jun","QTR I","Jul","Aug","Sep","QTR II","Oct","Nov","Dec","QTR III","Jan","Feb","Mar","QTR IV","Total");
$month_arr_title_field=array("Apr","May","Jun","QTRI","Jul","Aug","Sep","QTRII","Oct","Nov","Dec","QTRIII","Jan","Feb","Mar","QTRIV","Total");
$sales_plan_list=array(1=>"Sales Plan",2=>"Outstanding Collection Plan",3=>"Repeat Revenue Plan");
$dont_display_footer=false;
define("SALES_DUMMY_ID","9999");
define("OBD_DUMMY_ID","9998");
define("PATANJALI_ID","8948");
define("LOCATION_TRACKER_ID","641280");
define("SALES_TAX_ORDER_ENTRY","1,2,3,11,13,14,16,17,18,19,20,21,22,24,25");  //used in quotation & proforma invoice for calculation of tax and listing in concern category.
define("LIVE_BATON_LABEL", "I need help to pass a live baton");
define('ALLOWED_CHARS', 'IYfVceCi20kgh5jzrAHuDat8FwnEBy3OWRoJTbUXQ6P1SxLNMZK9dlpGq4mv7s');
//Prefix for generating unique virtual account number for each customer
define("ACCOUNT_NUMBER_PREFIX", "0588GTPL");//0588 - Corporate code  AND GTPL - Short form of GOFRUGAL Technologies Pvt LTD
define("CORPORATE_ACCOUNT_IFSC", "UTIB0CCH274");
define("EMP_IDS_TO_SKIP_REPORTING_MAIL","1,37");
define("GFT_NOT_FOR_SALES_EDITION","25");
$CURRENT_SERVER_URL= "";
if(isset($_SERVER['SERVER_NAME'])){
	$protocol = empty($_SERVER['HTTPS']) ? 'http' : 'https';
	$CURRENT_SERVER_URL = $protocol."://".($_SERVER['SERVER_NAME']=='localhost'?$_SERVER['SERVER_NAME'].":8080":$_SERVER['SERVER_NAME']);
}
define('CURRENT_SERVER_URL', "$CURRENT_SERVER_URL");
putenv('TZ=Asia/Calcutta');
$conn=my_connect_db();
if(isset($_SESSION["uid"])){
	$uid=(string)$_SESSION["uid"];
	$loginid=(int)$_SESSION["gfl_access_no"];
}else if(isset($_REQUEST['sam_access_login_id']) and (string)$_REQUEST['sam_access_login_id']!=''){
	ajax_session_check();
}

$attach_path="../sales_server_support"; //Hardcoded for better perforamance
//$attach_path=get_samee_const('RELATIVE_ATTACH_PATH');

require_once(__DIR__ . "/common_util.php");
require_once(__DIR__ . "/common_util_ext.php");
require_once(__DIR__ . "/function.send_sms.php");

$cached_test_mode = Cache::getString("testMode");
if($cached_test_mode===null){
	$cached_test_mode = get_samee_const('STORE_PAYMENT_TEST_MODE'); // 0 - live, 1 - samtest
	Cache::putString("testMode",$cached_test_mode);
}
$global_sam_domain = "https://sam.gofrugal.com";
$global_web_domain = "https://www.gofrugal.com";
$global_store_domain = "https://store.gofrugal.com";
if((int)$cached_test_mode==1){
	$global_sam_domain = "http://labtest.gofrugal.com";
	$global_web_domain = "http://staging.gofrugal.com";
	$global_store_domain = "https://labteststore.gofrugal.com";
}
$global_gst_mode = 1;//(int)get_samee_const("GST_MODE");
$order_product_fields = ",GOP_CGST_PER,GOP_CGST_AMT,GOP_SGST_PER,GOP_SGST_AMT,GOP_IGST_PER,GOP_IGST_AMT";
$mygofrugal_task_manger_customer_id = "217883";
$mydelight_tm_cust_id = "179343";
$global_api_log_db = "sam_api_log";
$global_audit_log_db = "sam_audit_log";


/**
 * @param string $key_name
 *
 * @return string
 */
function get_samee_const_cache($key_name){
	$query="select SQL_CACHE GSC_VALUE from gft_samee_const where GSC_NAME='$key_name'";
	$result=execute_my_query($query,/*$from_page=*/null,/*$send_mail_alert*/true,/*$show_error_msg*/false,/*$log_level*/4);
	if($data_key_words=mysqli_fetch_array($result)){
		return $data_key_words['GSC_VALUE'];
	}
	return '';
}

/**
 * @param string $privilage_id
 *
 * @return int[int]
 */
function get_group_under_privilage($privilage_id){
	$result=execute_my_query("select group_concat(GPL_GROUP_ID) group_ids from gft_privilages_enable_master where GPL_PREVILAGE_ID=$privilage_id ",'',true,false,4);
	if($result){
		$qd=mysqli_fetch_array($result);
		$group_ids=explode(',',$qd['group_ids']);

		$return_ids = /*. (int[int]) .*/ array();
		for($i=0; $i < count($group_ids); $i++){
			$return_ids[$i] = (int)$group_ids[$i];
		}

		return $return_ids;
	}
	else {
		return null;
	}
}

/**
 * @param string $date  - date is in yyyy-mm-dd format
 * @param boolean $check_general_shift
 *
 * @return boolean
 */
function is_holiday($date,$check_general_shift=false){
	$date=trim($date);
	if($check_general_shift==true){
		$holiday="select * from gft_holiday_list where ghl_date='".$date."' and GHL_TECHSUPPORT_GENERAL_SHIFT='N' ";
	}else{
		$holiday="select * from gft_holiday_list where ghl_date='".$date."' and GHL_OPTIONAL!='Y'";
	}
	$result=execute_my_query($holiday,'',true,false,2);
	if(mysqli_num_rows($result)!=0){
		return true;
	}else{
		return false;
	}
}

/**
 * @param string $time_hms
 *
 * @return int
 */
function time_to_seconds($time_hms){
	$time=explode(':',$time_hms);
	return (isset($time[0])?(int)$time[0]*3600:0)+(isset($time[1])?(int)$time[1]*60:0)+(isset($time[2])?(int)$time[2]:0);
}

/**
 * @param string $uid
 *
 * @return boolean
 */
function is_call_center_changeable_group($uid){
	if(!empty($uid)){
		$query="SELECT gsp_group_id FROM gft_voicenap_group_emp_dtl 
	left join gft_voicenap_group vg on (GVG_GROUP_ID=GVGED_GROUP_ID)
	left join gft_support_product_group spg on (GSP_GROUP_ID=GVG_SUPPORT_GROUP)
	where GVGED_EMPLOYEE=$uid and  GSP_EDIT_OPTION='Y' ";

		$result=execute_my_query($query);
		if($result){
			if(mysqli_num_rows($result)>0){
				return true;
			}else{
				return false;
			}
		}
		return false;
	}
	return false;
}

/**
 * @return string
 */
function getStackTraceString(){
	$stack = debug_backtrace();

	$stack_out='';
	//NOTE: skip the first row....
	for($sj=1; $sj < count($stack); $sj++){
		$file=isset($stack[$sj]['file'])?(string)$stack[$sj]['file']:'-';
		$line=isset($stack[$sj]['line'])?(string)$stack[$sj]['line']:'-';
		$stack_out .= $file.":".$line.":".(string)$stack[$sj]['function']." , ";
	}
	return $stack_out;

}

/**
 * @param string $mesg
 *
 * @return void
 */
function printStackTrace($mesg){
	error_log($mesg." :: " . getStackTraceString());
}


/**
 * @param string $user_id
 * @param int $group_id
 * @param mixed $role_id
 *
 * @return boolean
 */
function is_authorized_group($user_id,$group_id,$role_id=null){

	$group_id_arr=/*. (int[int]) .*/ array();

	if ($group_id == 0){
		//Do nothing
	}else if(is_array($group_id)){
		//throw new ErrorException("wrong usage of is_authorized_group. Check is_authorized_group_list method for array. ");
		$stack_out = getStackTraceString();
		error_log("wrong usage of is_authorized_group. Check is_authorized_group_list method for array. ". $stack_out);
		die("wrong usage of is_authorized_group. Check is_authorized_group_list method for array. ");
		/*
		   $group_id_arr=$group_id; //NOTE: WARNING will be thrown by PhpLint 

		   $stack_out = getStackTraceString();
		   error_log("wrong usage of is_authorized_group. Check is_authorized_group_list method for array. ". $stack_out);
		 */
	}else{
		$group_id_arr[0]=$group_id;
	}


	return is_authorized_group_list($user_id,$group_id_arr, $role_id);

}

/**
 * @param string $user_id
 * @param int[int] $group_id_arr
 * @param mixed $role_id
 *
 * @return boolean
 */
function is_authorized_group_list($user_id,$group_id_arr,$role_id=null){
	global $skip_authorization;
	$query='';
	if($skip_authorization){
		return true;
	}

	if ($user_id == ''){
		return false;
	}

	$role_id_s='';

	if(is_array($role_id)){
		$role_id_s= implode(',',$role_id);
	}else{
		$role_id_s=$role_id;
	}

	if ($group_id_arr === null){
		$group_id_s='';
	}else if (!is_array($group_id_arr)){
		$group_id_s=$group_id_arr; //NOTE: Warning will be thrown by PhpLint
		$stack_out = getStackTraceString();
		error_log("wrong usage of is_authorized_group_list. Check is_authorized_group method for group_id with int ". $stack_out);
	}else{
		$group_id_s= implode(',',$group_id_arr);
	}

	if($group_id_s!='' and $group_id_s!=0 and $group_id_s!=null){
		$query="select a.gem_emp_id from gft_emp_master a" .
			" left join gft_role_group_master rg on (grg_role_id=gem_role_id) " .
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id)" .
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id)" .
			" where gem_status='A' and ggm_group_id in ($group_id_s) and a.gem_emp_id='$user_id'  group by a.gem_emp_id ";
	}
	if($role_id_s!='' and $role_id_s!='0' and $role_id_s!=null){
		$query="select * from gft_emp_master a where gem_role_id in($role_id_s) and gem_emp_id='$user_id' ";
	}
	if($query==''){
		return false;
	}
	$result=execute_my_query($query,'common_util.php',true,false,2);
	if(mysqli_num_rows($result)!=0){
		return true;
	}else{
		return false;
	}
}

/**
 * @param string $user_id
 * @param string $priv_id
 *
 * @return boolean
 */
function is_authorized_privilege_group($user_id,$priv_id){
	$auth_gr = get_group_under_privilage($priv_id);
	return is_authorized_group_list($user_id,$auth_gr);
}

/**
 * @param string $user_id
 *
 * @return boolean
 */
function is_gft_employee($user_id){
	$query="SELECT em.GEM_EMP_ID from gft_emp_master em where em.GEM_EMP_ID='$user_id' and em.GEM_OFFICE_EMPID!=0 and em.gem_status='A'";
	$result=execute_my_query($query);
	if ($qd=mysqli_fetch_array($result)){
		//empname=$qd['GEM_EMP_NAME'];
		return true;
	}
	return false;
}





/**
 * @return void
 */
function call_mysql_READ_UNCOMMITTED(){
	execute_my_query(" set session transaction isolation level READ UNCOMMITTED");
}

/**
 * @param string $query
 *
 * @return string
 */
function getMySQLExplainQueryInHtml($query){
	global $conn;
	$result=mysqli_query($conn, "explain ".$query);
	if ($result === false){
		$out="error: Problem in the query .... return without explain";
		return $out;
	}


	$count=0;
	$out="<table border=1>";

	$out.="<tr>";
	for($i=0;$i<mysqli_num_fields($result);$i++){
		$colname=mysqli_field_name_wrapper($result,$i);
		$out.="<td>".$colname."</td>";
		$count++;
	}
	$out.="</tr>";


	while($data = mysqli_fetch_array($result)){
		$out.="<tr>";
		for($i=0;$i<$count; $i++){
			$out.="<td>".$data[$i]."</td>";
		}
		$out.="</tr>";
	}
	$out.="</table>";

	return $out;
}


/**
 *
 * @return string
 */
function getMySQLInnoDBStatusInHtml(){
	$result=execute_my_query("show engine innodb status");

	$out="<table border=1>";
	$out.="<tr><td>Status</td></tr>";
	while($data = mysqli_fetch_array($result)){
		$a_status=$data["Status"];

		$a_status=nl2br($a_status);

		$out.="<tr><td>$a_status</td></tr>";
	}
	$out.="</table>";

	return $out;
}


/**
 *
 * @return string
 */
function getMySQLProcessListInHtml(){
	$result=execute_my_query("show full processlist");

	$out="<table border=1>";
	$out.="<tr><td>db</td><td>Command</td><td>Time</td><td>State</td><td>Info</td></tr>";
	while($data = mysqli_fetch_array($result)){
		$a_db=$data["db"];
		$a_command=$data["Command"];
		$a_time=$data["Time"];
		$a_state=$data["State"];
		$a_info=$data["Info"];

		$out.="<tr><td>$a_db</td><td>$a_command</td><td>$a_time</td><td>$a_state</td><td>$a_info</td></tr>";
	}
	$out.="</table>";

	return $out;
}

/**
 * @return string[string]
 */
function getSystemMemInfo(){
	$data = explode("\n", file_get_contents("/proc/meminfo"));
	$meminfo = /*. (string[string]) .*/array();
	foreach ($data as $line) {
		$ar = explode(":",$line);
		if(count($ar) > 1){
			$meminfo[$ar[0]] = $ar[1];
		}
	}
	return $meminfo;
}

/**
 * @param string $query
 * @param string $from_page
 * @param boolean $send_mail_alert
 * @param boolean $show_error_msg
 * @param int $log_level
 *
 * @return resource
 */
function execute_my_query($query,$from_page=null,$send_mail_alert=true,$show_error_msg=false,$log_level=1){
	global $conn,$compulsory_show_error,$compulsory_show_explain,$me;
	global $log,$show_query,$show_explain;
	$result=/*. (resource) .*/ null;
	if($from_page==null){
		$from_page=basename($me);
	}
	if($query!=''){
		try{
			$qsttime = microtime();
			$result=mysqli_query($conn, $query);
			$qendtime = microtime();
			$deltaTime = microtimediff($qsttime, $qendtime);
//error_log(" time::# " .round($deltaTime,5). " #ms  query # ".$query);
			$deltaTime=(round($deltaTime,2));

			if($deltaTime>60){
				$numrows=-1;
				if ($result === false){
					$numrows=-1;
				}else if($result===true){
					$numrows = mysqli_affected_rows_wrapper();
				}else{
					$numrows=mysqli_num_rows($result);
				}
				$mail_msg="Automated Mail <br> Processing Delay in ".$from_page." $deltaTime Sec for the bellow Query<br><br> ".htmlspecialchars($query)."</br>No of Records : ".$numrows;
				$mail_msg.=getMySQLProcessListInHtml();
				$mail_msg.=getMySQLExplainQueryInHtml($query);
				mail_error_alert("Processing Delay $deltaTime Sec - $from_page",$mail_msg,null,null,1);
			}
			if(!$result){
				$errormesg=mysqli_error_wrapper();

				$stack_out = getStackTraceString();

				error_log("Problem in execute_my_query :: ". $query . "[mysqlerror::".$errormesg."]". $stack_out);

				$log->logInfo("Problem in execute_my_query :: ",$query);
				$log->logInfo("Error: ",$errormesg);
				if($show_error_msg==true or $compulsory_show_error==true){
					echo "<br><font color='red'>".$errormesg."</font><br>";
				}
				if($send_mail_alert==true){
					$isRecusive=false;
					if (strpos($stack_out,"mail_error_alert")>0){
						$isRecusive=true;
					}
					if ($isRecusive){
						error_log("recurive call.... returns....");
					}else{
						$mail_msg="Automated Mail <br> Error found in ".$from_page." <br>" .
							"<br>".mysqli_error_wrapper()."<br>" .
							"<br>".htmlspecialchars($query);

						$msg="<table border=1><caption> REQUEST Variables </caption><thead><th>Key</th><th>Value</th></thead>";
						$print_req_body = true;
						foreach($_REQUEST as $Key => $Value) {
							$print_req_body = false;
							if(is_array($_REQUEST[$Key])){
								foreach($_REQUEST[$Key] as $Key2 => $Value2) {
									$msg.="<tr><td>".$Key."</td><td>".$Value2."</td></tr>";
								}
							}else {
								$msg.="<tr><td>".$Key."<td>".$Value."</td></tr>";
							}
						}
						if($print_req_body){
							try{
								$request_body = file_get_contents('php://input');
								if($request_body!=''){
									$msg.="<tr><td>Request Body</td><td>".$request_body."</td></tr>";
								}
							}catch(Exception $e){
								die("Exception in: ".$e);
							}
						}
						$msg.="</table>";

						$msg.=getMySQLProcessListInHtml();

						$mail_msg.=$msg;

						if( (strpos($errormesg,"Deadlock") !== false) || (strpos($errormesg, "storage engine") !== false) ){
							$ar = getSystemMemInfo();
							$mail_msg .= "<br><br>".json_encode($ar)."<br><br>";
							$innodb_msg = getMySQLInnoDBStatusInHtml();
							$mail_msg.=$innodb_msg;
						}
						mail_error_alert("Error found ".$from_page."",$mail_msg);
					}
				}
			}

			if($show_query==true ){
				if($log_level<=$_REQUEST['show_query'] or $show_query==true){
					call_my_show_query($query,$deltaTime);
				}
			}
			if($show_explain==true or $compulsory_show_explain==true) {
				if($log_level<=$_GET['show_explain'] or $compulsory_show_explain==true){
					call_my_explain_query($query);
				}
			}
		}catch(ErrorException $e){
			error_log("Exception ". $e->__toString() . " mysqli_error=".mysqli_error_wrapper()." , query = $query ");
			//TODO: May need to throw exception?
		}
	}
	return $result;
}

/**
 * @param resource $result
 *
 * @return string[]
 */
function feach_data_header($result){
	try{
		$length=mysqli_num_fields($result);
		$header_name=/*. (string[int]) .*/ array();
		$i = 0;
		while ($finfo = mysqli_fetch_field($result)) {
			$header_name[$i]=$finfo->name;
			$i++;
		}
		return $header_name;
	}catch(Exception $e){
		error_log("Exception ". $e->__toString());
		return null;
	}
}

/**
 * @param string $query
 * @param int $deltaTime
 *
 * @return void
 */
function call_my_show_query($query,$deltaTime){
	echo "<div id=\"show_query\" class=\"unhide\">";
	echo "<table border=1 width=\"800px\">";
	echo "<tr><td>".htmlspecialchars($query)."<br/><font color=\"red\">Executed in $deltaTime Seconds</font></td></tr>";
	echo "</table>";
	echo "</div>";
}


/**
 * @param string $query
 *
 * @return void
 */
function call_my_explain_query($query){
	global $conn;
	echo "<div class=\"unhide\">";
	echo "<table border=1>";
	try{
		$result=mysqli_query($conn, "Explain ".$query);
		$headings=/*. (string[int][int]) .*/ array();
		for($i=0;$i<mysqli_num_fields($result);$i++){
			$headings[0][$i]=mysqli_field_name_wrapper($result,$i);
		}
		print_resultset($headings);
		$i=0;
		while($eqdata[$i][]=mysqli_fetch_array($result)){
			print_resultset($eqdata[$i]);
			$i++;
		}
	}catch(Exception $e){
		error_log("Exception ". $e->__toString());
		echo "<tr><td>".$e->__toString()."</td></tr>";
	}
	echo "</table>";
	echo "</div>";
}

/**
 * @param string $type
 * @param string $mesg
 * @param array $paramsArray
 *
 * @return void
 */
function insertHoneyPot($type,$mesg,$paramsArray){
	$params_out=json_encode($paramsArray);
	if (strlen($params_out) >500){
		$params_out=substr($params_out,0,500);
	}
	$remote_address = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
	$query="insert into gft_honeypot(CREATED_DATE,REMOTE_ADDR,TYPE,MESSAGE,PARAMS) values (now(),'$remote_address','".mysqli_real_escape_string_wrapper($type)."','".mysqli_real_escape_string_wrapper($mesg)."','".mysqli_real_escape_string_wrapper($params_out)."')";
	//echo $query;
	execute_my_query($query);
}
/**
 * @param string $type
 * @param string $mesg
 * @param array $paramsArray
 *
 * @return void
 */
function insertLoginFailure($type,$mesg,$paramsArray){
	$params_out=json_encode($paramsArray);
	if (strlen($params_out) >500){
		$params_out=substr($params_out,0,500);
	}
	$remote_address = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
	$query="insert into gft_login_failure(GLF_DATE,GLF_REMOTE_ADDR,GLF_FILE_NAME,GLF_MESSAGE,GLF_PARAMS) values (now(),'$remote_address','".mysqli_real_escape_string_wrapper($type)."','".mysqli_real_escape_string_wrapper($mesg)."','".mysqli_real_escape_string_wrapper($params_out)."')";
	execute_my_query($query);
}
/**
 * @return void
 */
function add_internal_access_check(){
	$local_ip_list=get_samee_const("LOCAL_IP_ADDRESS_SETUPWIZARD").',127.0.0.1';
	$remote_address = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
	$type			= basename($_SERVER['PHP_SELF']);
	if( ($remote_address!='') && (strpos($local_ip_list,$remote_address)===false) ){
		insertHoneyPot($type,"Not authorized from external network",$_REQUEST);
		die("Access Denied from this ip $remote_address");
	}
}

/*  NOT USED
 * @param string $p_OnOff
 * 
 * @return void
 *
function catchFatalErrors($p_OnOff='On'){
	$me=$_SERVER['PHP_SELF'];
	$from_page=basename($me);
	ini_set('display_errors','On');
	$phperror='><div id="phperror" style="display:none">';
    ini_set('error_prepend_string',$phperror);
    $phperror='</div><form name="catcher" method="post" action="error_informer_log.php" >' .
    		'<input type="hidden" name="fatal" value="">' .
    		'<input type="text" name="from_page" value='.$from_page.'>' .
    		'</form>' .
    		'<script type="text/javascript">' .
    		' document.catcher.fatal.value = document.getElementById("phperror").innerHTML;' .
    		' document.catcher.submit();' .
    		'</script>';
	ini_set('error_append_string',$phperror);
}
*/

/**
 * @param string $a
 * @param string $b
 *
 * @return int
 */

function microtimediff($a, $b){
	$a_dec=0;
	$a_sec=0;
	$b_dec=0;
	$b_sec=0;
	list($a_dec, $a_sec) = explode(" ", $a);
	list($b_dec, $b_sec) = explode(" ", $b);
	return $b_sec - $a_sec + $b_dec - $a_dec;
}

/**
 * @return string[int][int]
 */

function get_version_dtl(){
	$query=" select CONCAT(GPV_MAJOR_VERSION,'.',GPV_MINOR_VERSION,'.', GPV_PATCH_VERSION,'.', GPV_EXE_VERSION) version," .
		" GPV_RELEASE_DATE, GPV_RELEASE_NOTE_HLINK from gft_product_version_master where gpv_product_code=1 AND " .
		" GPV_RELEASE_DATE = (select max(GPV_RELEASE_DATE) from gft_product_version_master " .
		" where gpv_product_code=1) ";
	//$ver_dtl=get_two_dimensinal_result_set_from_query($query);
	$result=execute_my_query($query);
	$qd=mysqli_fetch_array($result);
	$ver_dtl=/*. (string[int][int]) .*/ array();
	$ver_dtl[0][0]=$qd['version'];
	$ver_dtl[0][1]=$qd['GPV_RELEASE_DATE'];
	$ver_dtl[0][2]=$qd['GPV_RELEASE_NOTE_HLINK'];
	return $ver_dtl;
}

/**
 * @param boolean $content_type
 *
 * @return string
 */
function get_login_user_info($content_type=true){
	global $uid;
	$login_info='';
	if(isset($login_uid) and $content_type==true){
		$emp_detail=get_emp_master($login_uid);
		$login_info.=" <br><b>User ID:</b>".$emp_detail[0][0];
		$login_info.=" <br><b>Posted By:</b> ".$emp_detail[0][1];
		$login_info.=" <br><b>Contact No:</b> ".$emp_detail[0][3];
	}else if(isset($login_uid) and $content_type!=true){
		$emp_detail=get_emp_master($login_uid);
		$login_info.="\nUser ID:".$emp_detail[0][0];
		$login_info.="\nPosted By: ".$emp_detail[0][1];
		$login_info.="\nContact No: ".$emp_detail[0][3];
	}else if(isset($_SESSION['uid']) and $content_type!=true ){
		$login_info.=(isset($_SESSION['uid'])?" \nUser ID:".(string)$_SESSION['uid']:"");
		$login_info.=(isset($_SESSION['uname'])?" \nPosted By: ".(string)$_SESSION['uname']:"");
		$login_info.=(isset($_SESSION['mobile_no'])?" \nContact No: ".(string)$_SESSION['mobile_no']:"");
	}else if(isset($_SESSION['uid'])){
		$login_info.=" <br><b>User ID:</b>".(string)$_SESSION['uid'];
		$login_info.=" <br><b>Posted By:</b> ".(string)$_SESSION['uname'];
		$login_info.=" <br><b>Contact No:</b> ".(string)$_SESSION['mobile_no'];
	}else if( ($uid!='') && ($uid!='0') ){ //From Mobile App
		$emp_detail=get_emp_master($uid,'A',null,false);
		$login_info.=" <br><b>User ID:</b>".$emp_detail[0][0];
		$login_info.=" <br><b>Posted By:</b> ".$emp_detail[0][1];
		$login_info.=" <br><b>Contact No:</b> ".$emp_detail[0][3];
	}

	return $login_info;
}

/**
 * @param boolean $content_type
 *
 * @return string
 */
function get_user_info($content_type=true){
	$user_info='';
	$user_info.=get_login_user_info($content_type);
	$ver_dtl=get_version_dtl();
	if($content_type==true){
		$user_info.=(isset($_SERVER['HTTP_USER_AGENT'])?"<br><b>User Agent:</b> ".$_SERVER['HTTP_USER_AGENT']:"");
		$user_info.=(isset($_SERVER['REMOTE_ADDR'])?"<br><b>IP Address: </b>".$_SERVER['REMOTE_ADDR']:"");
		$user_info.="<br><b>Time : </b>".date('Y-m-d H:i:s');
		$user_info.=(isset($_SERVER['SERVER_SOFTWARE'])?"<br><b>Server Software: </b>".$_SERVER['SERVER_SOFTWARE']:"");
		$user_info.="<br><b>Version : </b>".$ver_dtl[0][0];
		$user_info.="<br><b>Release Date: </b>".$ver_dtl[0][1];
		$user_info="<pre>".$user_info."</pre>";
	}
	else {
		$user_info.=(isset($_SERVER['HTTP_USER_AGENT'])?"\nUser Agent: ".$_SERVER['HTTP_USER_AGENT']:"");
		$user_info.=(isset($_SERVER['REMOTE_ADDR'])?"\nIP Address: ".$_SERVER['REMOTE_ADDR']:"");
		$user_info.="\nTime : ".date('Y-m-d H:i:s');
		$user_info.=(isset($_SERVER['SERVER_SOFTWARE'])?"\nServer Software: ".$_SERVER['SERVER_SOFTWARE']:"");
		$user_info.="\nVersion : ".$ver_dtl[0][0];
		$user_info.="\nRelease Date: ".$ver_dtl[0][1];
		$user_info="\n".$user_info;
	}
	return $user_info;
}

/**
 * @param string $query
 *
 * @return string[int][int]
 */
function get_two_dimensinal_result_set_from_query($query){
	global $me;
	$result=execute_my_query($query,$me,true,false,3);
	$i=0;
	$fd_group=/*. (string[int][int]) .*/ array();
	if($result  and (mysqli_num_rows($result) >0)){
		$heder_name=feach_data_header($result);
		while($qdata=mysqli_fetch_array($result)){
			for($j=0;$j<count($heder_name);$j++){
				$fd_group[$i][$j]=$qdata[$heder_name[$j]];
			}
			$i++;
		}
		return $fd_group;
	}else{
		return null;
	}
}

/**
 * @param string $purpose
 * @return string
 */
function get_folder_name_for_purpose($purpose) {
	$f_location_path = '';
	switch($purpose) {
		case 'invoice':
			$f_location_path="invoice";
			break;
		case 'quotation':
			$f_location_path="quotation";
			break;
		case 'receipt':
			$f_location_path="receipt";
			break;
		case 'collateral':
			$f_location_path="collateral";
			break;
		case 'onlinequote':
			$f_location_path="onlinequote";
			break;
		case 'proforma':
			$f_location_path="proforma";
			break;
		case 'addr_pdf':
			$f_location_path="temp_pdf_generator";
			break;
		case 'ndnc_request_files':
			$f_location_path="ndnc_request_files";
			break;
		case 'Feedback_Uploaded_Files':
			$f_location_path="Feedback_Uploaded_Files";
			break;
		case 'migration':
			$f_location_path="Migration_Template";
			break;
		case 'patch':
			$f_location_path="Patch_Log";
			break;
		case 'HQ_Proposals':
			$f_location_path="HQ_Proposals";
			break;
		case 'Dealer_Secret':
			$f_location_path="Dealer_Secret";
			break;
		case "migration_log":
			$f_location_path="Migration_dtl";
			break;
		default:
			$f_location_path='';
			break;
	}
	return $f_location_path;
}
/**
 * @param string $folder_name
 * @param string $content
 * @param string $filename
 * @param string $mode
 * @param boolean $move_onemore_folder
 *
 * @return boolean
 */
function write_to_file($folder_name,$content,$filename,$mode=null,$move_onemore_folder=false){
	if($mode===null){
		$mode='w';
	}
	global $attach_path;
	if($move_onemore_folder==true){
		$attach_path1="../".$attach_path;
	}else{
		$attach_path1=__DIR__."/".$attach_path;
	}
	$folder_name=$attach_path1."/".$folder_name;

	try{
		if(!file_exists($folder_name)) {
			$rs = mkdir("$folder_name",0777);
			if(!$rs){
				return false;
			}
		}
		$fp = fopen($folder_name."/".$filename, $mode);
		fwrite($fp,$content);
		fclose($fp);
		$exist_permission = (int)substr(sprintf('%o', fileperms($folder_name.'/'.$filename)), -3);
		if($exist_permission!=777){
			chmod($folder_name."/".$filename,0777);
		}

		return true;
	}catch(Exception $e){
		error_log("Exception ". $e->__toString());
		return false;
	}
}

/**
 * @param string $folder_name
 * @param string $filename
 *
 *  @return void
 */
function write_a_request_in_file($folder_name,$filename){
	$RREMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
	$RPHP_SELF=$_SERVER['PHP_SELF'];
	$RREQUEST_METHOD=$_SERVER['REQUEST_METHOD'];
	$RQUERY_STRING=json_encode($_REQUEST);

	//$REQUEST=implode(',',$_REQUEST);
	$content='Request Time :'.date('d-m-Y H:i:s').' IP :'.$RREMOTE_ADDR.': Request From: '.$RPHP_SELF."  Request Method : ".$RREQUEST_METHOD ." ".$RQUERY_STRING;
	$content.=PHP_EOL;


	write_to_file($folder_name,$content,$filename,/*$mode*/'a',/*$move_onemore_folder*/true);
}

/**
 * @param string $str
 *
 * @return string
 */
function remove_special_characters($str){
	$str=(string)str_replace('&','and',$str);
	$str=strip_tags($str,"");
	$retstr= preg_replace('/[^A-Za-z0-9\s.\s\-\s:]/','',$str);
	return $retstr;
}

/**
 * @return float
 */
function getDeltaTime(){
	global $startTime;
	$endTime = (float) microtime(true);
	$deltaTime = round($endTime - $startTime, 3);
	//$deltaTime=round($deltaTime,2);
	return $deltaTime;
}


/**
 * @return void
 */
function closeDBConnection(){
	global $conn;
	try{
		mysqli_close($conn);
	}catch(Exception $e){
		error_log("Exception ". $e->__toString());
	}
}

///**
// * @return void
// */
//function setReporting() {
//	if(!defined("DEVELOPMENT_ENVIRONMENT")) define("DEVELOPMENT_ENVIRONMENT",false);
//	if (DEVELOPMENT_ENVIRONMENT == true) {
//	error_reporting(E_ALL);
//	ini_set('display_errors','On');
//	ini_set('log_errors', 'On');
//	ini_set('error_log', 'logs/error.log');
//	} else {
//	error_reporting(E_ALL);
//	ini_set('display_errors','Off');
//	ini_set('log_errors', 'On');
//	ini_set('error_log', 'logs/error.log');
//	}
//}

/**
 * @param string $in
 *
 * @return string
 */
function encode_str ($in) /*. throws ErrorException .*/{
	return base64_encode(rawurlencode(serialize(gzcompress($in))));
}

/**
 *
 * @param string $in
 *
 * @return string
 */
function decode_str ($in) /*. throws ErrorException .*/{
	return gzuncompress((string)unserialize(rawurldecode(base64_decode(($in)))));
}
//setReporting();

/**
 * @param string $varname
 *
 * @return string
 */
function getStringRequestVariable($varname){
	if (isset($_REQUEST[$varname])){
		return (string)$_REQUEST[$varname];
	}

	return '';
}

/**
 * @param string $varname
 *
 * @return boolean
 */
function getBooleanRequestVariable($varname){
	if (isset($_REQUEST[$varname])){
		$v=(string)$_REQUEST[$varname];
		if ($v == 'true'){
			return true;
		}
	}

	return false;
}

/**
 * @return string
 */
function get_receipt_id(){
	$query_max="select ifnull(max(grd_receipt_id),0)+1 from gft_receipt_dtl ";
	$result_max=execute_my_query($query_max,'cp_paymants.php',false,false);
	$qdr=mysqli_fetch_array($result_max);
	$grd_receipt_id=$qdr[0];
	return $grd_receipt_id;
}

/**
 * @param resource $result1
 *
 * @return string
 */

function print_selct_query_result($result1){
	$details = "<br><TABLE border=1>";
	$details.= "<tr>";
	$lenth=mysqli_num_fields($result1);
	$heder_name = /*. (string[int]) .*/ array();
	for($i=0;$i<$lenth;$i++){
		$heder_name[$i]=mysqli_field_name_wrapper($result1,$i);
		$details.= "<th>".$heder_name[$i]."</th>";
	}
	$details.= "</tr>";
	while($qdata=mysqli_fetch_array($result1))
	{
		$details.= "<tr>";
		for($i=0;$i<count($heder_name);$i++){
			$details.= "<td>".trim($qdata[$heder_name[$i]])."</td>";
		}
		$details.= "</tr>";
	}
	$details.= "</TABLE>";
	return $details;
}

/**
 * @param string $key
 *
 * @return string
 */
function get_help_link($key){
	$sel_value='';
	$que = "select menu_help_link from gft_menu_master where menu_path='$key' ";
	$res = execute_my_query($que);
	if($dat = mysqli_fetch_array($res)){
		$sel_value = $dat['menu_help_link'];
	}
	return $sel_value;
}

/**
 * @param int $number
 *
 * @return string
 */
function get_ordinal_suffix_for_number($number) {
	$suffix = array('th','st','nd','rd','th','th','th','th','th','th');
	if (($number %100) >= 11 && ($number%100) <= 13) {
		$val = $number. 'th';
	}else {
		$val = $number. $suffix[$number % 10];
	}
	return $val;
}

/**
 * @param int $length
 * @return string
 */
function generate_OTP($length = 5){
	$password = "";
	$possible = "123456789";
	$i = 0;
	while ($i < $length) {
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		if (!strstr($password, $char)) {
			$password .= $char;
			$i++;
		}
	}
	return $password;
}
/**
 * @param string $lead_code
 * @param string $email_id
 * @param string $mobile_no
 *
 * @return string
 */
function generate_sms_otp($lead_code,$email_id,$mobile_no){
	$new_otp 		= 	generate_OTP(5);
	$test_mode 		= (int)get_samee_const("STORE_PAYMENT_TEST_MODE");
	if(in_array($test_mode,array(1,2))){ // qa and preprod
		$new_otp = '12345';
	}
	execute_my_query("UPDATE gft_otp_master SET GOM_OTP_STATUS='I' WHERE GOM_LEAD_CODE='$lead_code' and GOM_OTP_SMS_TO='$mobile_no' and GOM_OTP_MAIL_TO='$email_id' AND GOM_WEB_REQUEST_TYPE='50' AND GOM_OTP_STATUS='A' ");
	execute_my_query("insert into gft_otp_master (GOM_LEAD_CODE,GOM_OTP_MAIL_TO,GOM_OTP_SMS_TO,GOM_OTP,GOM_OTP_STATUS,GOM_GEN_DATE_TIME,GOM_WEB_REQUEST_TYPE) ".
		" VALUES('$lead_code','$email_id','$mobile_no','$new_otp','A',now(),'50')");

	return $new_otp;
}

/**
 * @param string $otp_value
 * @param string $mobile_no
 *
 * @return void
 */
function validate_otp($otp_value, $mobile_no){
	$sql1 = " select GOM_ID from gft_otp_master where GOM_OTP_SMS_TO='".mysqli_real_escape_string_wrapper($mobile_no)."'".
		" and GOM_OTP='".mysqli_real_escape_string_wrapper($otp_value)."' and GOM_OTP_STATUS='A' ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$prime_id = $row1['GOM_ID'];
		execute_my_query(" update gft_otp_master set GOM_OTP_STATUS='I' where GOM_ID='$prime_id' ");
		return true;
	}
	return false;
}

/**
 * @param string $lead_code
 * @param string $otp
 * @param string $operation
 * @param string $mobileNo
 * @param string $emailId
 * @param int $timeLimitMins
 *
 * @return boolean
 */
function verify_otp_and_update_status($lead_code,$otp,$operation='',$mobileNo='',$emailId='',$timeLimitMins=0){
	$wh_cond = " GOM_LEAD_CODE='$lead_code' and GOM_OTP='$otp' and GOM_OTP_STATUS='A' ";
	$wh_cond .= ($mobileNo!='') ? " and GOM_OTP_SMS_TO='$mobileNo' " : "";
	$wh_cond .= ($emailId!='') ? " and GOM_OTP_MAIL_TO='$emailId' " : "";
	if($timeLimitMins > 0){
		$st_time = date('Y-m-d H:i:s', strtotime("-$timeLimitMins minutes"));
		$wh_cond .= " and GOM_GEN_DATE_TIME >= '$st_time' ";
	}
	$check_quey=" select GOM_LEAD_CODE,GOM_PCODE,GOM_PSKEW from gft_otp_master where $wh_cond ";
	$check_res = execute_my_query($check_quey);
	if(mysqli_num_rows($check_res)==1){
		if($operation=='update'){
			execute_my_query("update gft_otp_master set GOM_OTP_STATUS='I' where $wh_cond  ");
		}
		return true;
	}else{
		return false;
	}
}

/**
 * @param int $length
 * @return string
 */
function generate_presignup_OTP($length = 5){

	$otp_code ='';
	//$is_valid =	0;

	$otp_code	=	generate_OTP($length);

	/*
	while($is_valid<1){
		$otp_code	=	generate_OTP($length);
		$sql_check_duplicate=	execute_my_query(" SELECT  GPR_REGISTER_ID FROM gft_presignup_registration WHERE GPR_ACTIVATION_STATUS='N' AND GPR_OTP_CODE='$otp_code' ");
		if(mysqli_num_rows($sql_check_duplicate)==0){
			$is_valid++;
		}
	}*/

	return $otp_code;
}

/**
 * @param string $username
 * @param string $userpassword
 *
 * @return string
 */
function auth_user_webservice($username,$userpassword){
	$crypwd = (strlen($userpassword)<=32) ? sam_password_hash(strtolower($userpassword)) : $userpassword; //since from old product service user login, md5 hash will come and for learning manager auto login, sha hash will come
	$query= " select GLM_EMP_ID from gft_login_master " .
		" join gft_emp_master on (glm_emp_id=gem_emp_id and gem_status='A') ".
		" where GLM_LOGIN_NAME='".mysqli_real_escape_string_wrapper($username)."' and GLM_PASSWORD='".mysqli_real_escape_string_wrapper($crypwd)."' ";
	$res=execute_my_query($query);
	if($query_data=mysqli_fetch_array($res)){
		return $query_data['GLM_EMP_ID'];
	}
	return '';
}

/**
 * @param string $mobile_no
 * @param int $min_len
 * @param int $max_len
 *
 * @return boolean
 */
function is_valid_mobile($mobile_no,$min_len=9,$max_len=20){
	$reg_exp_len = '{'.$min_len.','.$max_len."}";
	if(preg_match("/^[\+0-9][0-9]$reg_exp_len$/", $mobile_no)==0){
		return false;
	}
	return true;
}

/**
 * @param string $email
 *
 * @return boolean
 */
function is_valid_email($email){
	if(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$/", strtolower($email))==0){
		return false;
	}
	return true;
}

/**
 * @param string $css_path
 *
 * @return string
 */
function include_css_file($css_path){
	$last_modified_time = 0;
	try{
		$last_modified_time = filemtime($css_path);
	}catch (Exception $e){}
	$ret = "<link rel='stylesheet' type='text/css' href='$css_path?t=$last_modified_time'/>";
	return $ret;
}

/**
 * @param string $js_path
 *
 * @return string
 */
function include_js_file($js_path){
	$last_modified_time = 0;
	try{
		$last_modified_time = filemtime($js_path);
	}catch (Exception $e){}
	$ret = "<script type='text/javascript' src='$js_path?t=$last_modified_time'></script>";
	return $ret;
}

/**
 * @param string $mobile
 * @param string $email
 *
 * @return void
 */
function is_employee_contact($mobile,$email){
	if( ($mobile=='') || ($email=='') ){
		return false;
	}
	$sql1 = " select GEM_EMP_ID from gft_emp_master where GEM_STATUS='A' and (GEM_MOBILE='$mobile' ".
		" or GEM_RELIANCE_NO='$mobile' or GEM_EMAIL='$email') ";
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1) > 0){
		return true;
	}
	return false;
}
/**
 * @param string $mpath
 *
 * @return string
 */
function udpate_user_credential_menu($mpath){
	global $uid;
	$que_res = execute_my_query("select GLM_LOGIN_NAME,GLM_PASSWORD from gft_login_master where GLM_EMP_ID='$uid' ");
	if($data1 = mysqli_fetch_array($que_res)){
		$mpath = str_replace("{{username}}", $data1['GLM_LOGIN_NAME'], $mpath);
		$mpath = str_replace("{{credential}}", $data1['GLM_PASSWORD'], $mpath);
	}
	return $mpath;
}
/**
 * @param boolean $json_content_type
 *
 * @return void
 */
function addAccessControlAllowOrigin($json_content_type=false){
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST,OPTIONS');
	header('Access-Control-Allow-Headers: content-type,x-api-key');
	if($json_content_type){
		header("Content-Type:application/json");
	}
}
/**
 * @param string $page_name
 *
 * @return void
 */
function callFloodProtection($page_name){
	global $log;
	$FloodProtection = new FloodProtection($page_name, 10, 30);
	//labtest IP address configured for tesing purpose
	if($FloodProtection->check($_SERVER['REMOTE_ADDR']) && !in_array($_SERVER['REMOTE_ADDR'], array('49.249.235.103'))){
		header("HTTP/1.1 429 Too Many Requests");
		$log->logInfo("Response: Too Many Requests from ".$_SERVER['REMOTE_ADDR']);
		exit("Too Many Requests");
	}
}
/**
 * @param string $message
 *
 * @return string
 */
function mysqli_real_escape_string_wrapper($message){
	global $conn;
	return mysqli_real_escape_string($conn, $message);
}
function mysqli_result($res,$row=0,$col=0){
	$numrows = mysqli_num_rows($res);
	if ($numrows && $row <= ($numrows-1) && $row >=0){
		mysqli_data_seek($res,$row);
		$resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
		if (isset($resrow[$col])){
			return $resrow[$col];
		}
	}
	return false;
}
function mysqli_insert_id_wrapper(){
	global $conn;
	return mysqli_insert_id($conn);
}
function mysqli_affected_rows_wrapper(){
	global $conn;
	return mysqli_affected_rows($conn);
}
function mysqli_set_charset_wrapper($charset){
	global $conn;
	mysqli_set_charset ($conn , $charset );
}
function mysqli_error_wrapper(){
	global $conn;
	return mysqli_error($conn);
}
function mysqli_errno_wrapper(){
	global $conn;
	return mysqli_errno($conn);
}
function mysqli_field_name_wrapper($result,$i){
	return mysqli_fetch_field_direct($result, $i)->name;
}
function mysqli_field_type_wrapper($result,$i){
	return mysqli_fetch_field_direct($result, $i)->type;
}
?>
