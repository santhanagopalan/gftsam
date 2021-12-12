<?php
/*. require_module 'standard'; .*/
/*. require_module 'mysql'; .*/
/*. require_module 'hash'; .*/

/*. forward string[int] function get_emp_name_desc(string $id); .*/
/*. forward string function get_emp_name(int $id, boolean $active=); .*/
/*. forward string[int][int] function get_eligible_demo_incentive_employees (string $customerId,boolean $only_active=,boolean $only_partner=); .*/
/*. forward string[int][int] function get_eligible_agile_incentive_employees (string $customerId,mixed $not_in_emps=); .*/
/*. forward string function get_order_type_for_agile (string $customerId,string $skip_order= ,string $to_dt_cond=); .*/
/*. forward string[int] function get_attribute_percent_in_array (string $order_number); .*/
/*. forward void function insert_orderwise_incentive_owner (string $orderNo,string $incentive_type,string[int] $attr_perc_arr,int $attr_id,int $owner_emp,string $ref_activity_id=,boolean $only_partner=); .*/
/*. forward void function update_orderwise_sales_incentive_owner (string $orderNo,string $demo_by_emp,string $helped_by_dtl=,int $corp_joint_emp=,int $corp_joint_perc=,boolean $only_partner=,string $cp_lead_code=); .*/
/*. forward void function update_incentive_earnings (string $receipt_id); .*/

require_once(__DIR__ ."/dbcon.php");
/**
 * @param string $product_code
 * @param string $vertical
 * @param string $edition
 * 
 * @return string[int]
 */
function get_product_brand_name($product_code, $vertical, $edition='0'){
    $return_arr = array();
    $result = execute_my_query("select GPB_NAME, GPB_ID from gft_brand_product_mapping 
            inner join gft_product_brand_master ON(GBP_BRAND_ID=GPB_ID)
            where GBP_PRODUCT='$product_code' AND GBP_VERTICAL='$vertical' AND GBP_EDITION='$edition' AND GBP_STATUS=1  limit 1");
    if($row=mysqli_fetch_assoc($result)){
        $return_arr[1] = $row['GPB_NAME'];
        $return_arr[0] = $row['GPB_ID'];
    }
    return $return_arr;
}
/**
 * @param float $deltatime
 *
 * @return void
 */
function track_access($deltatime){
	global $loginid,$me;
	 $access_page=$_SERVER['SCRIPT_NAME'];
	 $access_page=substr($access_page,1);

	 $time_now=date('Y-m-d H:i:s');
	 //$remote_addr=$_SERVER['REMOTE_ADDR'];
	 //$HTTP_USER_AGENT=$_SERVER['HTTP_USER_AGENT'];
	 //$HTTP_ACCEPT_ENCODING=$_SERVER['HTTP_ACCEPT_ENCODING'];
	 $query_track="insert ignore into gft_access_track_log (gtl_access_no,gtl_access_page,gtl_from_time,gtl_server_load_time,gtl_server_load_unit) values" .
	 		"($loginid,'$access_page','$time_now','$deltatime','seconds');";
	
	 execute_my_query($query_track,$me,true,false,4);
	 
	 $query_up=" update gft_access_log set gfl_end_time='$time_now' where gfl_access_no='$loginid' ";
	 execute_my_query($query_up,'',true,false,4);
}

/**
 * @param int $id
 * @param boolean $active
 *
 * @return string
 */
function get_emp_name($id,$active=true){
	 global $me;
	 if($id=='' || $id==0){
		return '';
	 }
	 $query="select GEM_EMP_NAME from gft_emp_master where GEM_EMP_ID='$id' ";
	 if($active==true){
	 $query.=" and gem_status='A' ";
	 }
	 $result=execute_my_query($query,$me,true,false,3);
	 $name = "";
	 if($qdata=mysqli_fetch_array($result)){
        $name=$qdata['GEM_EMP_NAME'];
	 }
     return $name;
}

/**
 * @param string $received_id
 * @param string $current_location
 * 
 * @return void
 */
function validate_session($received_id,$current_location=''){
    if((int)$received_id == 0){
        if($current_location!=''){
            setcookie("Requested",$current_location, time()+3000,"/");
        }
        send_failure_response(array("message"=>"session expired"), HttpStatusCode::UNAUTHORIZED);
        exit;
    }
}

/**
 * @return string[int][string]
 */
function get_team_master_with_value_label(){
    $mysql1 =" select GEW_ID, GEW_DESC from gft_emp_web_display join gft_emp_master on (WEB_GROUP=GEW_ID) ".
        " where GEM_STATUS='A' and  length(GEW_DESC) > 1 group by GEW_ID order by GEW_DESC ";
    $myres1 = execute_my_query($mysql1);
    $team_resp_arr = /*. (string[int][string]) .*/array();
    while($myrow1 = mysqli_fetch_array($myres1)){
        $team_resp_arr[] = array('value'=>$myrow1['GEW_ID'],'label'=>$myrow1['GEW_DESC']);
    }
    return $team_resp_arr;
}

/**
 * @param string $id
 * @param string $label
 * @param string $table_name
 * @param string $where_cond
 * 
 * @return string[int][string]
 */
function get_master_with_value_label($id,$label,$table_name,$where_cond=''){
    $q1 = " select $id,$label from $table_name ";
    if($where_cond!=''){
        $q1 .= " where $where_cond ";
    }
    $q1 .= " order by $label ";
    $myres1 = execute_my_query($q1);
    $team_resp_arr = /*. (string[int][string]) .*/array();
    while($myrow1 = mysqli_fetch_array($myres1)){
        $team_resp_arr[] = array('value'=>$myrow1[$id],'label'=>$myrow1[$label]);
    }
    return $team_resp_arr;
}


/**
 * @param string $id
 *
 * @return string[int]
 */
function get_emp_name_desc($id){
	 global $me;
	 $query="SELECT GEM_EMP_NAME, GEM_MOBILE, GEM_EMAIL, GRM_ROLE_DESC,GEM_TITLE FROM gft_emp_master, gft_role_master WHERE GEM_ROLE_ID=GRM_ROLE_ID and GEM_EMP_ID='$id' and gem_status='A' ";
	 $result=execute_my_query($query,$me,true,false,3);
     $qdata=mysqli_fetch_array($result);
     $emp_dtl[0]=$qdata['GEM_EMP_NAME'];
     $emp_dtl[1]=$qdata['GEM_MOBILE'];
     $emp_dtl[2]=$qdata['GEM_EMAIL'];
     $emp_dtl[3]=$qdata['GEM_TITLE'];
     if($id=='9999'){
     	$emp_dtl[0] = 'Customer Success Team';
     	$emp_dtl[1] = '';
     	$emp_dtl[2] = 'customersuccess@gofrugal.com';
     	$emp_dtl[3] = '';
     }
     //$emp_dtl[3]=$qdata['GRM_ROLE_DESC'];
	 return $emp_dtl;
}

/**
 * @param string $pwd
 * 
 * @return string
 */
function sam_password_hash($pwd){
    return hash_hmac("sha256", $pwd, "GFTS@MH@SH");
}

/**
 * @param string $username
 * @param string $userpassword
 * @param string $auth_error_reason
 * @param boolean $need_encryption
 * 
 * @return int
 */
function auth_user($username,$userpassword,&$auth_error_reason,$need_encryption=true){
	global $skip_password,$me;
	global $conn;
	//$crypwd=crypt($userpassword,'sales');
	$crypwd_md5=($need_encryption)?md5($userpassword):$userpassword;
	$pwd_hash = sam_password_hash($crypwd_md5);
	$pos = strpos($username, '@gofrugal.com');
	if ($pos !== false) {
	    $username	=	substr($username, 0,$pos);
	}
    $query="select GLM_EMP_ID, GLM_LOGIN_NAME, GLM_PASSWORD, GLM_CREATED_DATE, GLM_UPDATED_DATE, GLM_LAST_LOGIN_DATE " .
    		" from gft_login_master join gft_emp_master on (glm_emp_id=gem_emp_id and gem_status='A') ".
    		" where GLM_LOGIN_NAME='".mysqli_real_escape_string_wrapper($username)."' ";
	if($skip_password==false){
	    $query.=" and GLM_PASSWORD='".mysqli_real_escape_string_wrapper($pwd_hash)."' ";
	}
	$res=execute_my_query($query,$me,true,false,4);
	$rows=mysqli_num_rows($res);
	if($rows==0){
		return 0;
	}else{
		$query_data=mysqli_fetch_array($res);
		if(isPartnerEmployee((int)$query_data['GLM_EMP_ID'])){
			$sql_rc_details	=	"select lem.GLEM_EMP_ID, lem.GLEM_LEADCODE, ci.cgi_lead_code, ci.CGI_EMP_ID,em.gem_emp_name,ca.GCA_CP_SUB_TYPE,ls.GLS_SUBTYPE_NAME,ci.CGI_VALIDITY,lh.GLH_PROSPECTS_STATUS from gft_leadcode_emp_map lem ".
								" inner join gft_cp_info as ci ON(lem.GLEM_LEADCODE=ci.CGI_LEAD_CODE) ".
								" inner join gft_emp_master AS em ON(em.gem_emp_id=ci.CGI_EMP_ID) ".
								" inner join gft_cp_agree_dtl as ca ON(ca.gca_lead_code=ci.CGI_LEAD_CODE) ".
								" inner join gft_lead_subtype_master as ls ON(ls.GLS_SUBTYPE_CODE=ca.GCA_CP_SUB_TYPE) ".
								" inner join gft_lead_hdr as lh ON (ci.CGI_LEAD_CODE=lh.GLH_LEAD_CODE)".
								" where GLEM_EMP_ID=".(int)$query_data['GLM_EMP_ID'];
			$result_rc_details=execute_my_query($sql_rc_details);
			if(mysqli_num_rows($result_rc_details)>0){
				$rc_data=mysqli_fetch_array($result_rc_details);
				$validity_date=$rc_data['CGI_VALIDITY'];
				$prospect_status=$rc_data['GLH_PROSPECTS_STATUS'];
				$today_date=date('Y-m-d');
				if ($validity_date<$today_date){
					$auth_error_reason="Partner agreement expired. Please do contact partner management team";
					return 0;
				}
				/* if ($prospect_status != '10' and $prospect_status != '12' and $prospect_status != '14'){
					$auth_error_reason="Partner status is not active. Please do contact partner management team";
					return 0;
				} */
				return (int)$query_data['GLM_EMP_ID'];
			}else{
				return 0;
			}
		}
  		return (int)$query_data['GLM_EMP_ID'];
	}
	
}//end of function

/**
 * @param string $userId
 * @param string $password
 * 
 * @return int
 */
function authenticate_customer($userId, $password){
	$query = " select GCL_USER_ID from gft_customer_login_master where GCL_USERNAME='".mysqli_real_escape_string_wrapper($userId)."'". 
			 " and GCL_PASSWORD='".mysqli_real_escape_string_wrapper($password)."' and GCL_USER_STATUS='1' ";
	$res_que = execute_my_query($query);
	if(mysqli_num_rows($res_que)==0){
		return 0;
	}
	if($row1 = mysqli_fetch_array($res_que)){
		return (int)$row1['GCL_USER_ID'];
	}
	return 0;
}

/**
 * @param string $uid
 * @param int $roleid
 * @param boolean $default_page
 * @param string $condition
 *
 * @return string
 */
 
function get_query_for_menu_access($uid,$roleid,$default_page=false,$condition=null){
	if(!is_authorized_group($uid,1) || ($roleid==84) ){
		$emp_access_cond = " or GMR_EMP_ID=$uid ";
		if($roleid==84){
			$emp_access_cond = "";
		}
		$menuq= "SELECT m.menu_name, m.menu_path,mid FROM gft_menu_master m, gft_menu_role_access ra " .
				" WHERE m.mid= ra.GMR_MID AND (ra.GMR_ROLE_ID=$roleid $emp_access_cond) and menu_daccess=1 ";
		if($condition!=''){ $menuq.=" and fk_tab_id is not null and  gmr_availability>=1 and menu_name like '$condition' "	; }
		if($default_page==true){$menuq.= " AND m.fk_tab_id=1 order by GMR_AVAILABILITY limit 1";		}		
	}else{
		$menuq= "SELECT m.menu_name, m.menu_path,mid FROM gft_menu_master m where  menu_daccess=1  ";
		if($condition!=''){ $menuq.=" and menu_name like '$condition' and fk_tab_id is not null "	; }
		if($default_page==true){	$menuq.= " and m.fk_tab_id=1  AND menu_order=1 limit 1";		}
	}
	return $menuq;
}

/**
 * @param string $mypath
 *
 * @return string
 */
function authendicated($mypath){
	global $roleid;
	global $uid;
	$external_access_role = false;
	if($roleid==84){
		$external_access_role = true;
	}
	if(!is_authorized_group($uid,1) || $external_access_role ){
		$role_emp_cond = " and (ra.GMR_ROLE_ID=$roleid OR ra.GMR_EMP_ID=$uid) ";
		if($external_access_role){
			$role_emp_cond = " and ra.GMR_ROLE_ID=$roleid ";
		}
		$menuq= "SELECT m.fk_tab_id,m.menu_name, m.menu_path FROM gft_menu_master m, gft_menu_role_access ra
		WHERE m.mid= ra.GMR_MID $role_emp_cond AND m.menu_path='$mypath'";
		if(is_authorized_group_list($uid,null,26)){
			$menuq= "SELECT m.fk_tab_id,m.menu_name, m.menu_path FROM gft_menu_master m, gft_menu_role_access ra, gft_cp_emp_menu_access ce
			WHERE m.mid= ra.GMR_MID AND (ra.GMR_ROLE_ID=$roleid OR ra.GMR_EMP_ID=$uid) AND m.menu_path='$mypath' AND ce.GCE_EMP_ID=$uid AND ce.GCE_STATUS='A' AND ce.GCE_MID=m.mid ";
		}		
	}else{		
		$menuq= "SELECT m.fk_tab_id, m.menu_name, m.menu_path FROM gft_menu_master m " .
				"where m.menu_path='$mypath' ";
	}

	$resultmenu=execute_my_query($menuq,'menu_util.php',true,false,4);
	if($data=mysqli_fetch_array($resultmenu)){
		return $data['fk_tab_id'];
	}else{
		if(!is_authorized_group($uid,1) || $external_access_role){
			return 'NULL';
		}else{
			return '1';
		}
	}
}

/**
 * @param string $ret_type
 * 
 * @return void
 */
function show_firstpage_login($ret_type=''){
	global $uid;
	global $roleid;
	$defaultpath = "";
	if(isset($_SESSION["uid"])){
		$menuq=get_query_for_menu_access($uid,$roleid,true);
		$resultmenu=execute_my_query($menuq);
		if(isset($_SESSION['personalize'])){
			$defaultpath="personalize.php";
		}else if($data=mysqli_fetch_array($resultmenu)){
			$defaultpath=$data['menu_path'];
			$req_path = isset($_COOKIE["Requested"]) ? (string)$_COOKIE["Requested"] : '';
			if($req_path!=''){
			    $defaultpath = $req_path;
				setcookie("Requested", "");
			}
		}
		if($ret_type=='get'){
		    return ltrim($defaultpath,"/");
		}else{
		    js_location_href_to($defaultpath);
		}
	}
}//End of function


/**
 * @param string $id
 * @param string $for_date
 * @param string $for_date2
 *
 * @return void
 */
function show_access_personwise($id,$for_date,$for_date2){
	//global $alt_row_class;
	//print_r($alt_row_class);
	$query="select gfl_login_id,ucase(glm_login_name),gfl_start_time,gfl_end_time " .
			"from gft_access_log,gft_login_master " .
			"where glm_emp_id=gfl_login_id and glm_emp_id='$id' ";
			
	if($for_date!='' and $for_date2!=''){		
	   $query.="  and gfl_start_time >='$for_date 00:00:00' and gfl_start_time<='$for_date2 23:59:59'";
	}else if($for_date!=''){
		$query.="  and gfl_start_time>='$for_date 00:00:00' and gfl_start_time<='$for_date 23:59:59'";
	}
	$query.="order by gfl_start_time desc limit 0,25";
	$result=execute_my_query($query);
echo<<<END
<table border="0" width="100%">
END;
print_dtable_header("Access Log");
echo<<<END
<table cellpadding="0" cellspacing="2" width="100%" border="0" class="FormBorder1">
END;
$myarr=array("S.No","From","Login Time","Logout Time");
sortheaders($myarr,null,null,null,null);
$i=1; 
$s=0;
if(mysqli_num_rows($result)==0){
echo<<<END
<tr style="height:20" ><td colspan=7 align =center> No Access </tr>
END;
}
while($qdata=mysqli_fetch_array($result)){
	$id=$qdata[0];
	$name=$qdata[1];
	$start_time=$qdata[2];
	$end_time=$qdata[3];
	$value_array[0]=array("".$i,$name,"".$start_time,"".$end_time);
	print_resultset($value_array);
	$i++;
	$s=($s==1?0:1);
}//end of while
echo<<<END
</table>
END;
}

/**
 * @param int $video_id
 * @param string $employee_id
 * @param int[int] $group_id_arr
 *
 * @return boolean
 */
function is_authorized_to_see_video($video_id, $employee_id, $group_id_arr ){
	$sql = "select GEM_EMP_ID,GEM_OFFICE_EMPID from gft_emp_master where GEM_EMP_ID='$employee_id'";
	$res = execute_my_query($sql);
	$is_employee = false;
	$is_partner = false;
	if($row1 = mysqli_fetch_array($res)){
		$gem_emp_id 	= (int)$row1['GEM_EMP_ID'];
		$gem_office_id 	= (int)$row1['GEM_OFFICE_EMPID'];
		if($gem_emp_id==$gem_office_id){
			$is_employee = true;
		}else{
			$is_partner = true;
		}
	}
	if($is_employee){  
		if(is_authorized_group_list($employee_id, $group_id_arr)){
           return true;
		}
	}else if($is_partner){
		 $chk_query 	= " select GUV_MASTER_REF_ID from gft_uploaded_videos where GUV_MASTER_REF_ID='$video_id' and GUV_FOR_PARTNER=1 ";
		 $chk_res	= execute_my_query($chk_query);
		 if(mysqli_num_rows($chk_res) > 0){
		 	return true;
		 }
	}
	return false;
}

/**
 * @param string $doc_id
 * @param string $employee_id
 * 
 * @return boolean
 */
function is_authorized_to_see_doc($doc_id, $employee_id ){
	$sql = "select GEM_EMP_ID,GEM_OFFICE_EMPID from gft_emp_master where GEM_EMP_ID='$employee_id'";
	$res = execute_my_query($sql);
	$is_employee = false;
	$is_partner = false;
	if($row = mysqli_fetch_array($res)){
		$gem_emp_id 	= (int)$row['GEM_EMP_ID'];
		$gem_office_id 	= (int)$row['GEM_OFFICE_EMPID'];
		if($gem_emp_id==$gem_office_id){
			$is_employee = true;
		}else{
			$is_partner = true;
		}
	}
	if($is_employee){
		$chk_query = " select GUV_VIDEO_ID from gft_uploaded_videos where GUV_VIDEO_ID='$doc_id' and GUV_FOR_EMPLOYEE=1";
	}else if($is_partner){
		$chk_query 	= " select GUV_VIDEO_ID from gft_uploaded_videos where GUV_VIDEO_ID='$doc_id' and GUV_FOR_PARTNER=1 ";
	}else{
		return false;
	}
	$chk_res	= execute_my_query($chk_query);
	if(mysqli_num_rows($chk_res) > 0){
		return true;
	}
	return false;
}

/**
 * @param int $emp_id
 * @param string $name_code_cond
 * 
 * @return string
 */
function get_customer_accessible_condition($emp_id,$name_code_cond=''){
	global $non_employee_group;
	$cust_cond_secured='';
	if(is_authorized_group_list($emp_id,$non_employee_group)){
		$cp_lead_code	=	get_cp_lead_code_for_eid($emp_id);
		$cp_emp_id		=	get_cp_emp_id_for_leadcode($cp_lead_code);
		$partner_employee	=	check_is_partner_employee($emp_id);
		$query_partner_emp	=	"";
		if(isset($partner_employee['partner_id']) and $partner_employee['partner_id']!=''){
			$downstream_partners= get_downstream_partner_emp_ids($cp_lead_code);
			if(isset($partner_employee['employee_role']) && in_array($partner_employee['employee_role'], array(79,80,81))){
				$query_partner_emp	=	" and (GLE_LEAD_OWNER_RTC=$emp_id OR GLE_PARTNER_CREATED_EMP=$emp_id)";
			}else if($downstream_partners!=""){
				$query_partner_emp	=	" and ((GLE_LEAD_OWNER_RTC NOT IN($downstream_partners) OR GLE_LEAD_OWNER_RTC IS NULL) AND (GLE_PARTNER_CREATED_EMP NOT IN($downstream_partners) OR GLE_PARTNER_CREATED_EMP IS NULL))";
			}
		}
		if(is_authorized_group($emp_id,14)){
			$cust_cond_secured.=" and ( (GLH_LEAD_SOURCECODE=7 and glh_reference_given='$cp_lead_code') or glh_lead_code='$cp_lead_code') ";
		}else{
			$cust_cond_secured.=" and ((GLH_LEAD_SOURCE_CODE_PARTNER=5 and GLH_REFERENCE_OF_PARTNER='$cp_lead_code') or " .
			"glh_lead_code='$cp_lead_code' or glh_lfd_emp_id in ($cp_emp_id) ) $query_partner_emp ";
		}
	}else {
		if (is_authorized_group_list($emp_id,array(5)) and !is_authorized_group_list($emp_id,array(1,12,8))) {
			$cust_cond_secured	=	get_territory_query_for_sales_person((int)$emp_id,true,"gcp",$name_code_cond);
		}
	}
	return $cust_cond_secured;
}

/**
 * @param string $lead_code
 * 
 * @return int
 */
function get_mygofrugal_app_installed_status($lead_code){
	$sql1 = " select GCL_DEVICE_STATUS from gft_customer_access_dtl ".
			" join gft_customer_login_master on (GCL_USER_ID=GCA_USER_ID) where GCA_ACCESS_STATUS=1 ".
			" and GCL_USER_STATUS=1 and GCA_ACCESS_LEAD='$lead_code' order by GCL_DEVICE_STATUS desc limit 1 ";
	$res1 = execute_my_query($sql1);
	$mygofrugal_app_stat = 0;
	if ($row_data = mysqli_fetch_array($res1)){
		$mygofrugal_app_stat = 1;
		if((int)$row_data['GCL_DEVICE_STATUS']==0){
			$mygofrugal_app_stat = 2;
		}
	}
	return $mygofrugal_app_stat;
}

/**
 * @param string $customer_id
 * @param string[int] $prod_arr
 * @param string[int] $qty_arr
 * @param int $emply_id
 * 
 * @return string
 */
function validate_erp_order($customer_id,$prod_arr,$qty_arr,$emply_id){
	$skip_erp_lead_arr = explode(",", get_samee_const("ERP_LEADS"));
	if(in_array($customer_id, $skip_erp_lead_arr)){
		return ""; //no validation for configured erp leads
	}
	if(is_authorized_group($emply_id, 36)){ //no validation pcs team
		return "";
	}
	$lead_type = (int)get_lead_type_for_lead_code($customer_id);
	if($lead_type!=1){
	    return "";
	}
	$err_msg = "";
	foreach ($prod_arr as $i => $val){
		$prod_id = $val;
		$qty_val = (int)$qty_arr[$i];
		$prod_id_arr = explode("-", $prod_id);
		$pcode = isset($prod_id_arr[0])?(int)$prod_id_arr[0]:0;
		$pskew = isset($prod_id_arr[1])?(string)$prod_id_arr[1]:'';
		$pm_dtl = get_product_master_dtl($pcode, $pskew);
		$skew_prop = isset($pm_dtl[4])?(int)$pm_dtl[4]:0;
		if( ($qty_val > 5) && ($pcode==500) && ($skew_prop==3) ){
			$err_msg = "As you know the product delivery effort of customers using more than 5 clients is high, we recommend to sell ERP edition and we are restricted to book orders with more than 5 clients with base products. Please contact sales / partner management team ";
		}
	}
	return $err_msg;
}
/**
 * @param string $order_no
 * @param string $type
 * @param string[int] $qty_arr
 * @param string[int] $day_arr
 * @param string[int] $date_arr
 *
 * @return void
 */
function update_prorate_alr_log($order_no,$type,$qty_arr,$day_arr,$date_arr){
    if( empty($order_no) || (count($qty_arr)==0) || (count($day_arr)==0) ){
        return;
    }
    execute_my_query("delete from gft_prorate_alr_log where GPA_ORDER_NO='$order_no' and GPA_TYPE='$type'");
    foreach ($qty_arr as $ind => $val){
        $ins_arr = array(
            'GPA_ORDER_NO'      => $order_no,
            'GPA_TYPE'          => $type,
            'GPA_CLIENT_QTY'    => $qty_arr[$ind],
            'GPA_DAYS'          => $day_arr[$ind],
            'GPA_PURCHASED_DATE'=> $date_arr[$ind],
        );
        array_insert_query("gft_prorate_alr_log", $ins_arr);
    }
}
/**
 * @param string[int][string] $server_order_arr
 * 
 * @return string
 */
function validate_kit_model_and_base_product($server_order_arr){
	$err_msg = "";
	$kit_order = false;
	$base_order = false;
	for($j=0;$j<count($server_order_arr);$j++){
		if( isset($server_order_arr[$j]['product']) and ($server_order_arr[$j]['product']!='') ){
			$prod_id_arr = explode("-", $server_order_arr[$j]['product']);
			$pcode = isset($prod_id_arr[0])?(int)$prod_id_arr[0]:0;
			if(in_array($pcode,array('200','500'))){
				$base_order = true;
			}else{
				$internal_prod = (int)get_single_value_from_single_table("GPM_IS_INTERNAL_PRODUCT", "gft_product_family_master", "GPM_PRODUCT_CODE", $pcode);
				if($internal_prod==4){
					$kit_order = true;
				}
			}
		}
	}
	if($base_order && $kit_order){
		$err_msg = "RPOS / DE Products can not be created with GCM / ERP Kit model Products in Quotation and Proforma. Create RPOS / DE Products alone or GCM / ERP Kit model Products alone.";
	}
	return $err_msg;
}
/**
 * @param string $cust_id
 * 
 * @return string
 */
function get_base_product_expiry_date($cust_id){
    $expiry_dt_que = " select GID_VALIDITY_DATE from gft_install_dtl_new ".
        " join gft_product_master on (GID_LIC_PCODE=GPM_PRODUCT_CODE and GID_LIC_PSKEW=GPM_PRODUCT_SKEW) ".
        " where GID_LEAD_CODE='$cust_id'  and GID_LIC_PCODE in (500,200) and GPM_LICENSE_TYPE!='3' and GID_STATUS!='U' ";
    return get_single_value_from_single_query("GID_VALIDITY_DATE", $expiry_dt_que);
}

/**
 *
 * @param string $lead_code
 * @param string $ins_product_code
 * @param string $ins_product_skew
 * @param string $ins_ass_expirydate
 * @param string $expiry_for
 * @param string $lead_type
 * @param string $ins_inc
 *
 * @return string[string]
 */
function get_alr_prorata_details($lead_code, $ins_product_code,$ins_product_skew, $ins_ass_expirydate ,$expiry_for,$lead_type, $ins_inc){
    $return_arr = array();
    $prorata_client = $prorate_days= 0;
    $prorata_skew_dtl = "";
    $prorata_client_dtl = array();
    $prorata_client_logs = $prorata_days_logs = "";
    $cust_country = get_single_value_from_single_table("GLH_COUNTRY","gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
    $price_column = (strcasecmp($cust_country, "India")==0) ? "GPM_NET_RATE" : "GPM_USD_RATE";
    if(in_array($ins_product_code, array('500','200')) && $lead_type == 1){
        $ins_prod_dtl = get_product_master_dtl($ins_product_code, $ins_product_skew);
        if( isset($ins_prod_dtl[5]) && ($ins_prod_dtl[5]=='18') ){
            $prorata_skew = substr($ins_product_skew, 0,4).($expiry_for==3?"ULPRERPALR":"ULPRERSALR");
        }else{
            $prorata_skew = substr($ins_product_skew, 0,4).($expiry_for==3?"ULPRPALR":"ULPRSALR");
        }
        $last_expiry_date = (date('Y-m-d',strtotime("-365 days",strtotime($ins_ass_expirydate))));
        $aque = " select GOD_LEAD_CODE,GOP_QTY,GOD_ORDER_DATE,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW from gft_order_hdr ".
                " join gft_order_product_dtl ON(GOD_ORDER_NO=GOP_ORDER_NO) ".
                " left join gft_cp_order_dtl ON(GCO_ORDER_NO=GOP_ORDER_NO AND GCO_SKEW=GOP_PRODUCT_SKEW AND GCO_PRODUCT_CODE=GOP_PRODUCT_CODE) ".
                " where GOD_ORDER_STATUS='A' and GOD_ORDER_DATE>'$last_expiry_date' AND GOD_ORDER_DATE<'$ins_ass_expirydate' AND  GOP_PRODUCT_CODE='$ins_product_code' ";
        $q1 = "$aque and god_order_splict=0 and GOD_LEAD_CODE='$lead_code' ";
        $q2 = "$aque and god_order_splict=1 and GCO_CUST_CODE='$lead_code' ";
        $sub_que = " $q1 union $q2 ";
        $que1 = " SELECT  GOD_LEAD_CODE, GOP_QTY, GOD_ORDER_DATE,GLH_COUNTRY FROM ($sub_que) pro ".
                " INNER JOIN gft_product_master ON (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE AND GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW AND GFT_SKEW_PROPERTY=3) ".
                " join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ";
        $res_prorata_client = execute_my_query($que1);  
        while($row_prorata=mysqli_fetch_assoc($res_prorata_client)){
            $cust_country = $row_prorata['GLH_COUNTRY'];
            $clients = (int)$row_prorata['GOP_QTY'];
            $order_date = $row_prorata['GOD_ORDER_DATE'];
            $prorata_client = $prorata_client + $clients;
            $days = 1 + (int)(strtotime($ins_ass_expirydate)-strtotime($order_date))/(60 * 60 * 24);
            $prorate_days = $prorate_days + ($days*$clients);
            $prorata_client_dtl[] = "$clients**$days**$order_date"; 
            $prorata_client_logs .= "<input type='hidden' id='prorata_client_logs$ins_inc' name='prorata_client_logs[$ins_inc][]' class='formStyleTextarea' value='$clients'>";
            $prorata_days_logs .= "<input type='hidden' id='prorata_days_logs$ins_inc' name='prorata_days_logs[$ins_inc][]' class='formStyleTextarea' value='$days'>";
            $prorata_days_logs .= "<input type='hidden' id='prorata_date_logs$ins_inc' name='prorata_date_logs[$ins_inc][]' class='formStyleTextarea' value='$order_date'>";
        }
        if($prorata_client>0){
            $net_rate   = get_single_value_from_single_query($price_column, "select $price_column from gft_product_master  where CONCAT(GPM_PRODUCT_CODE,GPM_PRODUCT_SKEW)='$ins_product_code$prorata_skew' ");;
            $net_price  = round(($net_rate * $prorate_days)/$prorata_client);
            $list_price = (strcasecmp($cust_country, "India")==0) ? round($net_price*(100/118),2) : $net_price;
            $prorata_skew_dtl = "$ins_product_code-$prorata_skew-$prorata_client-$list_price-$net_price";
        }
    }else if($lead_type == 1){
        $que_check = execute_my_query("select GPM_PRODUCT_CODE from gft_product_family_master where GPM_PRODUCT_CODE='$ins_product_code' and (GPM_IS_INTERNAL_PRODUCT=2 or GPM_HEAD_FAMILY=400) ");
        if(mysqli_num_rows($que_check) > 0){
            $ins_ass_expirydate = get_base_product_expiry_date($lead_code);
            $last_expiry_date = (date('Y-m-d',strtotime("-365 days",strtotime($ins_ass_expirydate))));
           
            $aque = " select GOD_LEAD_CODE,GOP_QTY,GOD_ORDER_DATE,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW from gft_order_hdr ".
                " join gft_order_product_dtl ON(GOD_ORDER_NO=GOP_ORDER_NO ) ".
                " left join gft_cp_order_dtl ON(GCO_ORDER_NO=GOP_ORDER_NO AND GCO_SKEW=GOP_PRODUCT_SKEW AND GCO_PRODUCT_CODE=GOP_PRODUCT_CODE) ".
                " where GOD_ORDER_STATUS='A' and GOD_ORDER_DATE>'$last_expiry_date' AND GOD_ORDER_DATE<'$ins_ass_expirydate' AND  GOP_PRODUCT_CODE='$ins_product_code' AND GOP_PRODUCT_SKEW='$ins_product_skew' ";
            $q1 = "$aque and god_order_splict=0 and GOD_LEAD_CODE='$lead_code' ";
            $q2 = "$aque and god_order_splict=1 and GCO_CUST_CODE='$lead_code' ";
            $sub_que = " $q1 union $q2 ";
            $res_prorate_cl = execute_my_query($sub_que);
            $prorate_days = 0;
            $info_dtl = array();
            while($row_prorate=mysqli_fetch_assoc($res_prorate_cl)){
                $clients = (int)$row_prorate['GOP_QTY'];
                $order_date = $row_prorate['GOD_ORDER_DATE'];
                $prorata_client = $prorata_client + $clients;
                $days = 1 + (int)(strtotime($ins_ass_expirydate)-strtotime($order_date))/(60 * 60 * 24);
                $prorate_days = $prorate_days + ($days*$clients);
                $info_dtl[] = "<b>$clients</b> qty for <b>$days</b> days ";
            }
            if($prorata_client){
                $pm_query = " select pro.GPM_PRODUCT_CODE,pro.GPM_PRODUCT_SKEW,pro.$price_column,GTM_TAX_PER ".
                            " from gft_product_master ins ".
                            " join gft_product_master pro on (pro.GPM_PRODUCT_CODE='$ins_product_code' and pro.GPM_PRODUCT_SKEW=ins.GPM_PRORATE_SKEW) ".
                            " join gft_hsn_vs_tax_master on (pro.GPM_TAX_ID=GHT_ID) ".
                            " join gft_tax_type_master on (GHT_TAX_ID=GTM_ID) ".
                            " where ins.GPM_PRODUCT_CODE='$ins_product_code' and ins.GPM_REFERER_SKEW='$ins_product_skew' ";
                $pm_res = execute_my_query($pm_query);
                if($pm_data=mysqli_fetch_assoc($pm_res)){
                    $prorata_skew   = $pm_data["GPM_PRODUCT_SKEW"];
                    $net_rate       = $pm_data[$price_column];
                    $tax            = ((int)$pm_data["GTM_TAX_PER"])+100;
                    $perday = round($net_rate/365,4);
                    $net_price  = round($perday * $prorate_days/$prorata_client);
                    $list_price = (strcasecmp($cust_country, "India")==0) ? round($net_price*(100/$tax),2) : $net_price;
                    $prorata_skew_dtl = "$ins_product_code-$prorata_skew-$prorata_client-$list_price-$net_price-".implode("<br>", $info_dtl);
                }
            }
        }
    }
    $return_arr['client_dtl'] = $prorata_skew_dtl;
    $return_arr['prorata_client_logs'] = $prorata_client_logs;
    $return_arr['prorata_days_logs']= $prorata_days_logs;
    $return_arr['prorata_client_dtl']= $prorata_client_dtl;
    return $return_arr;
}
/**
 *
 * @param string $lead_code
 * @param string $ins_product_code
 * @param string $ins_product_skew
 * @param string $ins_ass_expirydate
 * @param string $lead_type
 * @param string $ins_order_no
 * @param string $ins_fullfillno
 *
 * @return string[string]
 */
function get_alr_prorate_dtl_for_custom_license($lead_code, $ins_product_code,$ins_product_skew, $ins_ass_expirydate,$lead_type,$ins_order_no,$ins_fullfillno){
    $return_arr = array();
    $alr_rate = 0;
    $cl_alr_skew_dtl = $pro_cl_alr_skew_dtl = $cl_alr_skew = $pro_cl_alr_skew = "";
    $cust_country = "";$pro_cl_alr_log_str = '';
    if(in_array($ins_product_code, array('500','200')) && $lead_type == 1){
        $cust_country = get_single_value_from_single_table("GLH_COUNTRY","gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
        $price_column = (strcasecmp($cust_country, "India")==0) ? "GPM_NET_RATE" : "GPM_USD_RATE";
        $pskew = substr("$ins_product_skew",0,4);
        $last_expiry_date = (date('Y-m-d',strtotime("-365 days",strtotime($ins_ass_expirydate))));
        $aque = " select GOD_LEAD_CODE,GOP_QTY,GOD_ORDER_DATE,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW from gft_order_hdr ".
            " join gft_order_product_dtl ON(GOD_ORDER_NO=GOP_ORDER_NO) ".
            " left join gft_cp_order_dtl ON(GCO_ORDER_NO=GOP_ORDER_NO AND GCO_SKEW=GOP_PRODUCT_SKEW AND GCO_PRODUCT_CODE=GOP_PRODUCT_CODE) ".
            " where GOD_ORDER_STATUS='A' AND  GOP_PRODUCT_CODE='$ins_product_code' AND substr(GOP_PRODUCT_SKEW,1,4)='$pskew' ";
        $q1 = "$aque and god_order_splict=0 and GOD_LEAD_CODE='$lead_code' and GOP_REFERENCE_ORDER_NO='$ins_order_no' and GOP_REFERENCE_FULLFILLMENT_NO=$ins_fullfillno ";
        $q2 = "$aque and god_order_splict=1 and GCO_CUST_CODE='$lead_code' and GCO_REFERENCE_ORDER_NO='$ins_order_no' and GCO_REFERENCE_FULLFILLMENT_NO=$ins_fullfillno ";
        $sub_que = " $q1 union $q2 ";
        $que1 = " SELECT  GOD_LEAD_CODE, GOP_QTY, GOD_ORDER_DATE,GOP_PRODUCT_SKEW,GPM_SKEW_DESC FROM ($sub_que) pro ".
            " INNER JOIN gft_product_master ON (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE AND GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW AND GPM_PRODUCT_TYPE=8) ";
        $res_prorate_cl = execute_my_query($que1);
        $cl_price = $pro_cl_price = $pro_cl_amnt = 0;
        $pro_cl_alr_row = $cl_alr_skew_str = $pro_cl_alr_skew_str = array();
        $cl_percentage = (int)get_samee_const("SAM_ASA_AMT_FOR_CUSTLIC");
        while($row_prorate=mysqli_fetch_assoc($res_prorate_cl)){
            $order_date = $row_prorate['GOD_ORDER_DATE'];
            $cl_prod_skew = $row_prorate['GOP_PRODUCT_SKEW'];
            $skew_desc = $row_prorate['GPM_SKEW_DESC'];
            $net_rate   = get_single_value_from_single_query($price_column, "select $price_column from gft_product_master  where CONCAT(GPM_PRODUCT_CODE,GPM_PRODUCT_SKEW)='$ins_product_code$cl_prod_skew' ");
            $alr_rate   = (($net_rate*$cl_percentage)/100);
            $days = 1 + (int)(strtotime($ins_ass_expirydate)-strtotime($order_date))/(60 * 60 * 24);
            if($days>=365){
                $cl_alr_skew = $pskew."CLALR";
                $cl_price = $cl_price+$alr_rate;
                $cl_alr_skew_str[] =  $skew_desc;
            }else{
                $pro_cl_alr_skew = $pskew."CLPROALR";
                $pro_cl_amnt = round((($alr_rate/365)*$days),2);
                $pro_cl_price = $pro_cl_price+$pro_cl_amnt;
                $pro_cl_alr_row[] = "$days**$order_date**$skew_desc";
                $pro_cl_alr_skew_str[] =  $skew_desc.'-'.$days.' days';
            }
        }
        if($cl_alr_skew!=''){
            $cl_skew_str= "";
            if(is_array($cl_alr_skew_str) && count($cl_alr_skew_str)>0){
                $cl_skew_str = implode("<br>",$cl_alr_skew_str);
            }
            $tax_que = " select GTM_TAX_PER from gft_product_master ".
                " join gft_hsn_vs_tax_master on (GPM_TAX_ID=GHT_ID) ".
                " join gft_tax_type_master on (GHT_TAX_ID=GTM_ID) ".
                " where GPM_PRODUCT_CODE=$ins_product_code and GPM_PRODUCT_SKEW='$cl_alr_skew' ";
            $tax_ver = (int)get_single_value_from_single_query("GTM_TAX_PER", $tax_que);
            $tax = $tax_ver+100;
            $cl_price = round($cl_price);
            $list_price = (strcasecmp($cust_country, "India")==0) ? round($cl_price*(100/($tax)),2) : $cl_price;
            $cl_alr_skew_dtl = "$ins_product_code**$cl_alr_skew**1**$list_price**$cl_price**($cl_skew_str)";
        }
        if($pro_cl_alr_skew!=''){
            $pro_cl_skew_str = "";
            if(is_array($pro_cl_alr_skew_str) && count($pro_cl_alr_skew_str)>0){
                $pro_cl_skew_str = implode("<br>",$pro_cl_alr_skew_str);
            }
            $pro_cl_price = round($pro_cl_price);
            $tax_que = " select GTM_TAX_PER from gft_product_master ".
                " join gft_hsn_vs_tax_master on (GPM_TAX_ID=GHT_ID) ".
                " join gft_tax_type_master on (GHT_TAX_ID=GTM_ID) ".
                " where GPM_PRODUCT_CODE=$ins_product_code and GPM_PRODUCT_SKEW='$pro_cl_alr_skew' ";
            $tax_ver = (int)get_single_value_from_single_query("GTM_TAX_PER", $tax_que);
            $tax = $tax_ver+100;
            $list_price = (strcasecmp($cust_country, "India")==0) ? round($pro_cl_price*(100/$tax),2) : $pro_cl_price;
            $pro_cl_alr_skew_dtl = "$ins_product_code**$pro_cl_alr_skew**1**$list_price**$pro_cl_price**($pro_cl_skew_str)";
            if(is_array($pro_cl_alr_row) && count($pro_cl_alr_row)>0){
                $pro_cl_alr_log_str = implode("||",$pro_cl_alr_row);
            }
        }
    }
    $return_arr['cl_alr_dtl'] = $cl_alr_skew_dtl;
    $return_arr['pro_cl_alr_dtl']= $pro_cl_alr_skew_dtl;
    $return_arr['pro_cl_alr_log_str']= $pro_cl_alr_log_str;
    return $return_arr;
}
/**
 * @param string $emp_id
 * @param string $lead_code
 * @param string $currency
 * @param string $f_name
 * @param string $customer_name
 * @param string $order_no
 * @param string $customer_country
 * @param string $total_amount
 * @param string $customer_email_ids
 * @param string $selected_email_ids
 * @param string $mail_type
 * @param string $profroma_type
 * 
 * @return void
 */
function send_quotation_mail_to_customer($emp_id, $lead_code, $currency,$f_name, $customer_name,
    $order_no,$customer_country, $total_amount, $customer_email_ids, $selected_email_ids, $mail_type="", $profroma_type=0){
    global $attach_path;
    $Customer_Details_Update_Link_Yes = get_encrypted_cust_info($lead_code,$currency, "gst_no_update","Yes");
    $Customer_Details_Update_Link_No = get_encrypted_cust_info($lead_code,$currency, "gst_no_update","No");
    $account_ifsc = CORPORATE_ACCOUNT_IFSC;
    $folder_path = ($mail_type=="proforma"?"proforma":"quotation");
    $mail_content = array(
        "Attachment"=>array($attach_path."/$folder_path/".$f_name),
        "Type"=>array(($mail_type=="proforma"?"Proforma Invoice":"Quotation")),
        "Customer_Name"=>array($customer_name),
        "Customer_Details_Update_Link_Yes"=>array($Customer_Details_Update_Link_Yes),
        "Customer_Details_Update_Link_No"=>array($Customer_Details_Update_Link_No),
        "Account_IFSC"=>array($account_ifsc),
        "GSTIN_INFO_MESSAGE"=>array("")
    );
    $mail_template_id = ($mail_type=="proforma"?"174":"368");
    if(strcasecmp($customer_country, 'India')==0){
        $mail_template_id = ($mail_type=="proforma"?"331":"169");
        $mail_content["STORE_PAYMENT_LINK"] = array("https://store.gofrugal.com/proforma_prize_list.php?$folder_path=$order_no");
        $mail_content["Account_Number_To_Pay"] = array(check_and_generate_account_number($lead_code));
        $gstin_no = get_single_value_from_single_table("GLE_GST_NO", "gft_lead_hdr_ext", "GLE_LEAD_CODE", "$lead_code");
        if(trim($gstin_no) == ""){
            $mail_content["GSTIN_INFO_MESSAGE"] = array(get_samee_const("GSTIN_CONTENT"));
        }
    }
    $cust_email = explode(',',$customer_email_ids);
    $customer_mail_ids=get_customer_contact_array($selected_email_ids,4);
    if(count($customer_mail_ids)>0){
        $cust_email=$customer_mail_ids;
    }
    $emp_dtl_arr = get_emp_master($emp_id,'A');
    $cc_mail_ids=array($emp_id,$emp_dtl_arr[0][6]);
    if($profroma_type==6){ //asa proforma
        $cc_mail_ids[] = get_single_value_from_single_table("glh_lfd_emp_id", "gft_lead_hdr", "GLH_LEAD_CODE", "$lead_code") ;
    }
    $partner_dtl	=	get_asa_commission_partner_dtl($lead_code);
    if(isset($partner_dtl['partner_emp_id']) && $partner_dtl['partner_emp_id']!=""){
        $cc_mail_ids[]  	= $partner_dtl['partner_emp_id'];
        $partner_id			= $partner_dtl['partner_emp_id'];
        send_notification_to_partner($partner_id,$lead_code,$customer_name,$emp_dtl_arr[0][1],$total_amount,$mail_type);
    }
    send_formatted_mail_content($mail_content, 89, $mail_template_id, null, null, $cust_email,  $cc_mail_ids,null,"",$emp_id);
}
/**
 * @param string $proforma_no
 * @param string $cust_id
 *
 * @return void
 */
function send_proforma_mail_to_customer($proforma_no,$cust_id){
	global $attach_path;
	$proforma_content 	= generate_quotation_proforma_content($proforma_no,'proforma');
	$html_file_path 	= write_html_file_for_pdf($proforma_content, 'proforma', "Pro_$proforma_no.html");
	generate_pdf($html_file_path);
	$f_name = str_replace(".html", ".pdf", "Pro_$proforma_no.html");
	//$commercial_service_check_qry = " select * from gft_dev_complaints where gdc_proforma_number='$proforma_no' ";
	//$commercial_service_check_res = execute_my_query($commercial_service_check_qry);
	//$designations = null;
	//if(mysqli_num_rows($commercial_service_check_res)>0) {
	    // TODO: Add commercial service details in proforma PDF
	//    $designations = array('1','2','3');
	//}
	$cust_email_arr = customerContactDetail($cust_id,0,false,array(1,2,3,4));
	$mail_template_id	= 174;
	$mail_content = array(
	    "Attachment"=>array("$attach_path/proforma/$f_name"),
	    "Type"=>array("Proforma Invoice"),
	    "Customer_Name"=>array($cust_email_arr['cust_name']),
	    "Customer_Details_Update_Link_Yes"=>array(get_encrypted_cust_info($cust_id,'INR', "gst_no_update","Yes")),
	    "Customer_Details_Update_Link_No"=>array(get_encrypted_cust_info($cust_id,'INR', "gst_no_update","No"))
	);
	if(strcasecmp($cust_email_arr['COUNTRY_NAME'],'India')==0){
		$mail_template_id 	= 331;
		$mail_content["STORE_PAYMENT_LINK"] = array("https://store.gofrugal.com/proforma_prize_list.php?proforma=$proforma_no");
		$mail_content["Account_Number_To_Pay"] = array(check_and_generate_account_number($cust_id));
	}
	$cust_email = explode(',',$cust_email_arr['EMAIL']);
	send_formatted_mail_content($mail_content,89,$mail_template_id,null,null,$cust_email,array($cust_email_arr['LFD_EMP_ID']));
}
/**
 * @param string $cust_id
 * @param string $required_val
 * @param string $from_page
 *
 * @return string
 */
function generate_asa_proforma($cust_id,$required_val='',$from_page=''){
	$today_time = date('Y-m-d H:i:s');
	$glh_lead_type = (int)get_single_value_from_single_table("GLH_LEAD_TYPE", "gft_lead_hdr", "GLH_LEAD_CODE", $cust_id);
	$lead_code_cond = $cust_id;
	$kit_based = false;
	$skip_kit_id_cond = '';
	$kit_client_skew = $noc_qty = $kit_custom_skew = $purchased_cl = $all_cl_price = "";
	if($glh_lead_type==3){
		$que2 = "select GLH_LEAD_CODE from gft_lead_hdr where glh_reference_given='$cust_id' and glh_lead_sourcecode=7 and glh_lead_type in (3,13) ";
		$res2 = execute_my_query($que2);
		while ($rd = mysqli_fetch_array($res2)){
			$lead_code_cond .= ",".$rd['GLH_LEAD_CODE'];
		}
		$kit_based = is_kit_based_customer($cust_id);
		if($kit_based){
		    $kit_install_id_arr = get_install_id_under_kit($cust_id);
		    foreach ($kit_install_id_arr as $ins_id){
		        $skip_kit_id_cond .= " and GID_INSTALL_ID!='$ins_id' ";
		    }
		    list($kit_client_skew,$noc_qty,$kit_custom_skew,$purchased_cl,$all_cl_price) = get_additional_alr_info_for_kit_customer($cust_id);
		}
	}
	$expire_type = '1';
	$sql1 = " select GID_INSTALL_ID,GID_LEAD_CODE,GID_ORDER_NO,GID_FULLFILLMENT_NO,GID_LIC_PCODE,GID_LIC_PSKEW,pm2.GPM_PRODUCT_CODE,pm2.GPM_PRODUCT_SKEW,pm2.GPM_LIST_PRICE,pm2.GPM_NET_RATE, ".
			" pm2.GPM_SERVISE_TAX_PERC,pm2.GPM_TAX_PERC,GTM_CGST,GTM_SGST,GTM_IGST,GTM_CESS,GPM_IS_BASE_PRODUCT,GID_VALIDITY_DATE, ".
			" pm1.GPM_ORDER_TYPE as order_type,GID_NO_CLIENTS,GTM_TAX_PER,GID_EXPIRE_FOR, pm1.GPM_PRODUCT_TYPE, ".
			" pm2.GPM_CLIENTS as built_in_clients, pm2.GPM_PRODUCT_TYPE as prod_type from gft_install_dtl_new ".
			" join gft_product_master pm1 on (pm1.GPM_PRODUCT_CODE=GID_LIC_PCODE and pm1.GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
			" join gft_product_master pm2 on (pm2.GPM_PRODUCT_CODE=GID_LIC_PCODE and pm2.GPM_REFERER_SKEW=GID_LIC_PSKEW) ".
			" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=GID_LIC_PCODE) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GID_LEAD_CODE) ".
			" left join gft_hsn_vs_tax_master on (GHT_ID=pm2.GPM_TAX_ID) ".
			" left join gft_tax_type_master on (GTM_ID=GHT_TAX_ID) ".
			" where GID_LEAD_CODE in ($lead_code_cond) and GID_STATUS!='U' $skip_kit_id_cond and pm1.GPM_LICENSE_TYPE=1 ".
			" and if(GID_EXPIRE_FOR='3',pm2.GFT_SKEW_PROPERTY=15,pm2.GFT_SKEW_PROPERTY=4) and GLH_COUNTRY='India' and GLH_LEAD_TYPE!=8 ";
	$res1 = execute_my_query($sql1);
	$ln=1;
	$insert_arr = /*. (string[int][string]) .*/array();
	$install_id_arr = /*. (string[int]) .*/array();
	$additional_skew_arr = /*. (int[string]) .*/array();
	$proforma_no = strtoupper(uniqid("PI"));//get_profroma_no(date('Y-m-d'), SALES_DUMMY_ID);
	$order_amount = 0;
	$update_alr_prorata_log = false;
	$prorata_client_dtl = array();
	$same_state = is_same_state($cust_id);
	$custom_sku_remarks = array();
	$asa_amount_for_customlic =	(int)get_samee_const("SAM_ASA_AMT_FOR_CUSTLIC");
	while ($row1 = mysqli_fetch_array($res1)) {
		$list_price 	= round($row1['GPM_LIST_PRICE'],2);
		$net_rate		= round($row1['GPM_NET_RATE'],2);
		$no_of_clients	= (int)$row1['GID_NO_CLIENTS'];
		$validity_date	= $row1['GID_VALIDITY_DATE'];
		$order_type 	= $row1['order_type'];
		$pcode			= $row1['GPM_PRODUCT_CODE'];
		$gid_order_no	= $row1['GID_ORDER_NO'];
		$gid_fullfill_no= $row1['GID_FULLFILLMENT_NO'];
		$install_id_arr[] = $row1['GID_INSTALL_ID'];
		$expire_type = $row1['GID_EXPIRE_FOR'];
		$edition_type=$row1['GPM_PRODUCT_TYPE'];
		$all_tax 		= $row1['GTM_TAX_PER'];
		$built_in_clients = ($row1['prod_type']=='18') ? (int)$row1['built_in_clients'] : 0;
		$gop_qty = 1;
		$custom_net_rate   = 0;
		$custom_skews_arr = /*. (string[int]) .*/array();
		if($order_type=="1"){ //user based
		    $gop_qty = ($kit_based && ($pcode=='300')) ? 1 : $no_of_clients;
			if($gop_qty==0){
				continue;
			}
		}
	    if($glh_lead_type!=1){
	        $que_custom=" select GPM_NET_RATE,GOP_PRODUCT_CODE as pcode,GOP_PRODUCT_SKEW as pskew ".
	  		        " from gft_order_product_dtl".
	  		        " join gft_order_hdr on (GOD_ORDER_NO=GOP_ORDER_NO and GOD_ORDER_APPROVAL_STATUS=2 ) ".
	  		        " join gft_product_master on (GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW and GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_TYPE=8) ".
	  		        " where GOD_ORDER_SPLICT=0 and GOP_REFERENCE_ORDER_NO='$gid_order_no' and GOP_REFERENCE_FULLFILLMENT_NO=$gid_fullfill_no and GOP_PRODUCT_CODE='$pcode' ".
	  		        " union all ".
	  		        " select GPM_NET_RATE,GCO_PRODUCT_CODE as pcode, GCO_SKEW as pskew ".
	  		        " from gft_cp_order_dtl".
	  		        " join gft_order_hdr on (GOD_ORDER_NO=GCO_ORDER_NO and GOD_ORDER_APPROVAL_STATUS=2 ) ".
	  		        " join gft_product_master on (GPM_PRODUCT_SKEW=GCO_SKEW and GPM_PRODUCT_CODE=GCO_PRODUCT_CODE and GPM_PRODUCT_TYPE=8) ".
	  		        " where GCO_REFERENCE_ORDER_NO='$gid_order_no' and GCO_REFERENCE_FULLFILLMENT_NO=$gid_fullfill_no and GCO_PRODUCT_CODE='$pcode' ";
	        $res_custom = execute_my_query($que_custom);
	        while($data1 = mysqli_fetch_array($res_custom)){
	            $custom_net_rate += round($data1['GPM_NET_RATE']*$asa_amount_for_customlic/100,2);
	            $custom_skews_arr[]	= $data1['pcode']."-".$data1['pskew'];
	        }
	    }
	    $prorata_alt_dtl = get_alr_prorata_details($cust_id, $pcode,$row1['GID_LIC_PSKEW'], $validity_date ,$expire_type,$glh_lead_type, 0);
	    $prorata_skew_dtl = isset($prorata_alt_dtl['client_dtl'])?$prorata_alt_dtl['client_dtl']:"";
	    $prorata_skew_dtl_arr = explode('-', $prorata_skew_dtl);
	    $prorata_qty          = isset($prorata_skew_dtl_arr[2])?(int)$prorata_skew_dtl_arr[2]:0;
	    $prorata_list_price   = isset($prorata_skew_dtl_arr[3])?$prorata_skew_dtl_arr[3]:0;
	    $net_amount           = isset($prorata_skew_dtl_arr[4])?$prorata_skew_dtl_arr[4]:0;
		if(in_array($pcode, array('200','500','501','502'))){
		    if(count($prorata_skew_dtl_arr) > 1){
		        $update_alr_prorata_log = true;
		        $prorata_client_asa_sku = $prorata_skew_dtl_arr[0]."-".$prorata_skew_dtl_arr[1];
		        $additional_skew_arr[$prorata_client_asa_sku] = $prorata_qty."-".$prorata_list_price."-".$net_amount;
		        $prorata_client_dtl = isset($prorata_alt_dtl['prorata_client_dtl'])?$prorata_alt_dtl['prorata_client_dtl']:null;
		    }
		    $no_of_clients -= $built_in_clients;
		    $no_of_clients = ($prorata_qty>0?($no_of_clients-$prorata_qty):$no_of_clients);
			if($no_of_clients > 0){
			    $pm_dtl = get_product_master_dtl($pcode, $row1['GID_LIC_PSKEW'],false,$expire_type);
				if(count($pm_dtl) > 0){
					$client_asa_sku = $pm_dtl[10]."-".$pm_dtl[11];
					if(!isset($additional_skew_arr[$client_asa_sku])){
						$additional_skew_arr[$client_asa_sku] = 0;
					}
					$additional_skew_arr[$client_asa_sku] += $no_of_clients;
				}
			}
			$prorate_cl_dtl      = get_alr_prorate_dtl_for_custom_license($cust_id, $row1['GID_LIC_PCODE'],$row1['GID_LIC_PSKEW'], $validity_date,$glh_lead_type,$gid_order_no,$gid_fullfill_no);
			$cl_alr_dtl_arr      = isset($prorate_cl_dtl['cl_alr_dtl'])?explode("**",$prorate_cl_dtl['cl_alr_dtl']):array();
			$pro_cl_alr_dtl_arr  = isset($prorate_cl_dtl['pro_cl_alr_dtl'])?explode("**",$prorate_cl_dtl['pro_cl_alr_dtl']):array();
			$add_arr = array($cl_alr_dtl_arr,$pro_cl_alr_dtl_arr);
			foreach ($add_arr as $varr){
			    if(count($varr) > 4){
			        $cl_sku = $varr[0]."-".$varr[1];
			        $additional_skew_arr[$cl_sku] = $varr[2]."-".$varr[3]."-".$varr[4];
			        $custom_sku_remarks[$cl_sku] = $varr[5];
			    }
			}
		}else if(count($prorata_skew_dtl_arr) > 1){
		    $pro_sku = $prorata_skew_dtl_arr[0]."-".$prorata_skew_dtl_arr[1];
		    $additional_skew_arr[$pro_sku] = "1-".($prorata_list_price*$prorata_qty)."-".($net_amount*$prorata_qty);
		    $custom_sku_remarks[$pro_sku] = $prorata_skew_dtl_arr[5];
		    if($order_type=='1'){
		        $gop_qty -= $prorata_qty;
		        if($gop_qty<=0){
		            continue;
		        }
		    }
		}
		if($kit_client_skew!=''){
		    $additional_skew_arr[$kit_client_skew] = (int)$noc_qty;
		}
		if($kit_custom_skew!=''){
		    $additional_skew_arr[$kit_custom_skew] = "1-$all_cl_price-".round($all_cl_price*(100+$all_tax)/100);
		}
		if($row1['GPM_IS_BASE_PRODUCT']=='Y'){
			$expired_days = datediff($validity_date, date('Y-m-d'));
			if($expired_days > 90){
				$late_fee_pcode = '391';
				$late_fee_pskew = '01.0LF';
				$late_dtl = get_product_master_dtl($late_fee_pcode, $late_fee_pskew);
				$list_amt_for_late = round($expired_days * ($late_dtl[0]/365),2);
				$net_amt_for_late  = round($expired_days * ($late_dtl[8]/365),2);
				$additional_skew_arr["$late_fee_pcode-$late_fee_pskew"] = "1-$list_amt_for_late-$net_amt_for_late";
			}
		}
		$custom_list_price = round(($custom_net_rate*100/(100+$all_tax)),2);
		$list_price = $list_price+$custom_list_price;
		$dtl_arr = /*. (string[string]) .*/array();
		$dtl_arr['GPP_ORDER_NO'] 		= $proforma_no;
		$dtl_arr['GPP_PRODUCT_CODE'] 	= $pcode;
		$dtl_arr['GPP_PRODUCT_SKEW'] 	= $row1['GPM_PRODUCT_SKEW'];
		$dtl_arr['GPP_QTY'] 		  	= $gop_qty;
		$dtl_arr['GPP_LIST_PRICE']   	= $list_price;
		$dtl_arr['GPP_SELL_RATE'] 	  	= $list_price;
		if($same_state){
			$dtl_arr['GPP_CGST_PER'] 	= $row1['GTM_CGST'];
			$dtl_arr['GPP_SGST_PER'] 	= $row1['GTM_SGST'];
			$dtl_arr['GPP_CGST_AMT'] 	= round($row1['GTM_CGST']*$list_price*$gop_qty/100,2);
			$dtl_arr['GPP_SGST_AMT'] 	= round($row1['GTM_SGST']*$list_price*$gop_qty/100,2);
		}else{
			$dtl_arr['GPP_IGST_PER'] 	= $row1['GTM_IGST'];
			$dtl_arr['GPP_IGST_AMT'] 	= round($row1['GTM_IGST']*$list_price*$gop_qty/100,2);
		}
		$dtl_arr['GPP_CESS_PER'] 	= $row1['GTM_CESS'];
		$dtl_arr['GPP_CESS_AMT'] 	= round($row1['GTM_CESS']*$list_price*$gop_qty/100,2);
		$dtl_arr['GPP_SELL_AMT']	= round(($net_rate*$gop_qty)+$custom_net_rate);
		$dtl_arr['GPP_PRINT_ORDER'] = $ln++;
		$dtl_arr['GPP_CUSTOM_SKEWS']= implode("**", $custom_skews_arr);
		if($order_type=='1'){
			$dtl_arr['GPP_NO_CLIENT']	= $no_of_clients;
		}

		$order_amount += (float)$dtl_arr['GPP_SELL_AMT'];
		$insert_arr[] = $dtl_arr;
	}
	if(count($additional_skew_arr) > 0){
		foreach ($additional_skew_arr as $askew => $str_val){
			$pcode_skew_arr = explode("-", $askew);
			$arr_val = explode("-", $str_val);
			$sqty = $arr_val[0];
			$sk_query = " select GPM_LIST_PRICE,GPM_NET_RATE,GPM_SERVISE_TAX_PERC,GPM_TAX_PERC,GTM_CGST,GTM_SGST,GTM_IGST,GTM_CESS ".
					" from gft_product_master ".
					" left join gft_hsn_vs_tax_master on (GHT_ID=GPM_TAX_ID) ".
					" left join gft_tax_type_master on (GTM_ID=GHT_TAX_ID) ".
					" where GPM_PRODUCT_CODE='$pcode_skew_arr[0]' and GPM_PRODUCT_SKEW='$pcode_skew_arr[1]' ";
			$sk_res = execute_my_query($sk_query);
			if($row_data = mysqli_fetch_array($sk_res)){
				$list_price 	= isset($arr_val[1])?$arr_val[1]:$row_data['GPM_LIST_PRICE'];
				$tax_perc 		= $row_data['GPM_TAX_PERC'];
				$ser_tax_perc 	= $row_data['GPM_SERVISE_TAX_PERC'];
				$net_rate 		= isset($arr_val[2])?$arr_val[2]:$row_data['GPM_NET_RATE'];
				$dtl_arr = /*. (string[string]) .*/array();
				$dtl_arr['GPP_ORDER_NO'] 		= $proforma_no;
				$dtl_arr['GPP_PRODUCT_CODE'] 	= $pcode_skew_arr[0];
				$dtl_arr['GPP_PRODUCT_SKEW'] 	= $pcode_skew_arr[1];
				$dtl_arr['GPP_QTY'] 		  	= $sqty;
				if($same_state){
					$dtl_arr['GPP_CGST_PER'] 	= $row_data['GTM_CGST'];
					$dtl_arr['GPP_SGST_PER'] 	= $row_data['GTM_SGST'];
					$dtl_arr['GPP_CGST_AMT'] 	= round($row_data['GTM_CGST']*$list_price*$sqty/100,2);
					$dtl_arr['GPP_SGST_AMT'] 	= round($row_data['GTM_SGST']*$list_price*$sqty/100,2);
				}else{
					$dtl_arr['GPP_IGST_PER'] 	= $row_data['GTM_IGST'];
					$dtl_arr['GPP_IGST_AMT'] 	= round($row_data['GTM_IGST']*$list_price*$sqty/100,2);
				}
				$dtl_arr['GPP_CESS_PER'] 	= $row_data['GTM_CESS'];
				$dtl_arr['GPP_CESS_AMT'] 	= round($row_data['GTM_CESS']*$list_price*$sqty/100,2);
				$all_tax 					= $row_data['GTM_CGST']+$row_data['GTM_SGST']+$row_data['GTM_IGST']+$row_data['GTM_CESS'];
				$dtl_arr['GPP_LIST_PRICE']   	= $list_price;
				$dtl_arr['GPP_SELL_RATE'] 	  	= $list_price;
				$dtl_arr['GPP_SELL_AMT']		= round($net_rate*$sqty);
				$dtl_arr['GPP_PRINT_ORDER'] 	= $ln++;
				$dtl_arr['GPP_CUSTOM_SKEWS']    = isset($custom_sku_remarks[$askew])?$custom_sku_remarks[$askew]:'';
				$order_amount += $dtl_arr['GPP_SELL_AMT'];
				$insert_arr[] = $dtl_arr;
			}
		}
	}
	//to combine the same skews and increase in qty and price
	$present_arr = $sku_to_merge = array();
	foreach ($insert_arr as $mn => $sku_arr){
		$gpp_code_skew = $sku_arr['GPP_PRODUCT_CODE']."-".$sku_arr['GPP_PRODUCT_SKEW'];
		if(in_array($gpp_code_skew, $present_arr)){
			$sku_to_merge[$mn] = $gpp_code_skew."-".array_search($gpp_code_skew, $present_arr);
		}else{
			$present_arr[$mn] = $gpp_code_skew;
		}
	}
	if(count($sku_to_merge) > 0){
		foreach($sku_to_merge as $ind => $ind_val){
			$sku_split_arr = explode("-", $ind_val);
			$key_index = $sku_split_arr[2];
			
			$older_pric_qty = $insert_arr[$key_index]['GPP_SELL_RATE']*$insert_arr[$key_index]['GPP_QTY'];
			$newer_pric_qty = $insert_arr[$ind]['GPP_SELL_RATE']*$insert_arr[$ind]['GPP_QTY'];
			$cc = $insert_arr[$key_index]['GPP_QTY']+$insert_arr[$ind]['GPP_QTY'];
			$insert_arr[$key_index]['GPP_LIST_PRICE']	= $insert_arr[$key_index]['GPP_SELL_RATE']	= round( ($older_pric_qty + $newer_pric_qty)/$cc , 2);
			
			$insert_arr[$key_index]['GPP_QTY'] 			= $insert_arr[$key_index]['GPP_QTY'] + $insert_arr[$ind]['GPP_QTY'];
			$insert_arr[$key_index]['GPP_SELL_AMT'] 	= $insert_arr[$key_index]['GPP_SELL_AMT'] + $insert_arr[$ind]['GPP_SELL_AMT'];
			
			if(isset($insert_arr[$key_index]['GPP_CGST_AMT'])){
				$insert_arr[$key_index]['GPP_CGST_AMT'] = $insert_arr[$key_index]['GPP_CGST_AMT'] + $insert_arr[$ind]['GPP_CGST_AMT'];
			}
			if(isset($insert_arr[$key_index]['GPP_SGST_AMT'])){
				$insert_arr[$key_index]['GPP_SGST_AMT'] = $insert_arr[$key_index]['GPP_SGST_AMT'] + $insert_arr[$ind]['GPP_SGST_AMT'];
			}
			if(isset($insert_arr[$key_index]['GPP_IGST_AMT'])){
				$insert_arr[$key_index]['GPP_IGST_AMT'] = $insert_arr[$key_index]['GPP_IGST_AMT'] + $insert_arr[$ind]['GPP_IGST_AMT'];
			}
			if(isset($insert_arr[$key_index]['GPP_NO_CLIENT'])){
				$insert_arr[$key_index]['GPP_NO_CLIENT']= $insert_arr[$key_index]['GPP_NO_CLIENT'] + $insert_arr[$ind]['GPP_NO_CLIENT'];
			}
			if(isset($insert_arr[$key_index]['GPP_CUSTOM_SKEWS'])){
				$insert_arr[$key_index]['GPP_CUSTOM_SKEWS'] = $insert_arr[$key_index]['GPP_CUSTOM_SKEWS']."".($insert_arr[$ind]['GPP_CUSTOM_SKEWS']==''?$insert_arr[$ind]['GPP_CUSTOM_SKEWS']:','.$insert_arr[$ind]['GPP_CUSTOM_SKEWS']);
			}
			unset($insert_arr[$ind]);
		}
			
	}
	if($from_page=='store'){
		$str_install_ids = implode(",", $install_id_arr); //in store order submit
		if(isset($_COOKIE['asa_install_ids'])){
			unset($_COOKIE['asa_install_ids']);
		}
		setcookie("asa_install_ids",$str_install_ids,time()+86400);
	}
	if($required_val=='product'){
		return $insert_arr;
	}
	if(count($insert_arr) > 0){
	    $is_same_state = '0';
	    $cust_country = get_single_value_from_single_table("glh_country", "gft_lead_hdr", "GLH_LEAD_CODE", "$cust_id");
	    if(strcasecmp($cust_country,'India')==0) {
	       $tax_mode = '4';
	       $is_same_state = (is_same_state($cust_id))?'1':'2';
	    }
		$hdr_arr['GPH_ORDER_NO'] 	= $proforma_no;
		$hdr_arr['GPH_LEAD_CODE'] 	= $cust_id;
		$hdr_arr['GPH_EMP_ID'] 		= SALES_DUMMY_ID;
		$hdr_arr['GPH_ORDER_DATE'] 	= $today_time;
		$hdr_arr['GPH_ORDER_AMT']	= $order_amount;
		$hdr_arr['GPH_ORDER_STATUS']= "A";
		$hdr_arr['GPH_CREATED_DATE']= $today_time;
		$hdr_arr['GPH_CURRENCY_CODE'] = 'INR';
		$hdr_arr['GPH_VALIDITY_DATE'] = add_date(date('Y-m-d'), 30);
		$hdr_arr['GPH_APPROVAL_STATUS'] = "2";
		$hdr_arr['GPH_REMARKS'] = "Proforma Created through Automated API";
		$hdr_arr['GPH_REF_INSTALL_ID'] = implode(",", $install_id_arr);
		$hdr_arr['GPH_ORDER_NO'] = $proforma_no;
		$hdr_arr['GPH_PROFORMA_TO'] = get_contact_name_for_customer($cust_id);
		$hdr_arr['GPH_IS_SAME_STATE'] = "$is_same_state";
		$res = array_insert_query("gft_proforma_hdr", $hdr_arr,"boolean");
		if($res){
			foreach ($insert_arr as $single_arr){
				$res = array_insert_query("gft_proforma_product_dtl", $single_arr,"boolean");
				if(!$res){
					//deleting hdr data also as a rollback event for dtl failure
					execute_my_query("delete from gft_proforma_hdr where GPH_ORDER_NO='$proforma_no'");
					return '';
				}
			}
			if($update_alr_prorata_log){
			    $rec = 0;
			    $qty_arr=array();$day_arr=array();$date_arr=array();
			    while($rec<count($prorata_client_dtl)){
			        $alr_dtl_arr = explode("**", $prorata_client_dtl[$rec]);
			        $qty_arr[]=$alr_dtl_arr[0];
			        $day_arr[]=$alr_dtl_arr[1];
			        $date_arr[] = $alr_dtl_arr[2];			        
			        $rec++;
			    }
			    update_prorate_alr_log($proforma_no,'proforma',$qty_arr,$day_arr,$date_arr);
			}
			send_proforma_mail_to_customer($proforma_no,$cust_id);
			return $proforma_no;
		}
	}
	return '';
}
/**
 * 
 * @param string $uid
 * @param string $cust_id
 * @param string $product_array
 * @param string $quantity_array
 * @param string $mail_id
 * @param string $mobile_no
 * @param string $dev_complaint_id
 * 
 * @return string
 */
function generete_proforma_service($uid, $cust_id, $product_array, $quantity_array,$mail_id, $mobile_no='',$dev_complaint_id=''){
    $proforma_no = get_profroma_no(date('Y-m-d'), $uid);
    $same_state = is_same_state($cust_id);
    $GPH_CURRENCY = "INR";
    $customer_country = strtolower(get_single_value_from_single_table("glh_country", "gft_lead_hdr", "GLH_LEAD_CODE", "$cust_id"));
    if($customer_country!='india'){
        $GPH_CURRENCY = "USD";
    }
    $order_amount = 0;
    $ln = 0;
    $insert_arr = array();
    if((count($product_array)>0) && count($product_array)==count($quantity_array)){
        foreach ($product_array as $key=>$product_dtl){
            $product_skew_arr = explode("-", $product_dtl);
            $product_code     = $product_skew_arr[0];
            $product_skew     = $product_skew_arr[1];
            $quantity         = $quantity_array[$key];
            $result           =  execute_my_query("select GPM_LIST_PRICE, GPM_NET_RATE, GPM_USD_RATE,GTM_CGST, ".
                                " GTM_SGST, GTM_IGST,GTM_CESS  from gft_product_master pm2 ".
                                " left join gft_hsn_vs_tax_master on (GHT_ID=pm2.GPM_TAX_ID)".
                                " left join gft_tax_type_master on (GTM_ID=GHT_TAX_ID)".
                                " where GPM_PRODUCT_SKEW='$product_skew' AND GPM_PRODUCT_CODE='$product_code'");
            $row1           = mysqli_fetch_array($result);
            $list_price = $row1['GPM_LIST_PRICE'];
            $net_rate   = $row1['GPM_NET_RATE'];
            $cgst_rate=$row1['GTM_CGST'];
            $sgst_rate=$row1['GTM_SGST'];
            $igst_rate= $row1['GTM_IGST'];
            $cess_rate = $row1['GTM_CESS'];
            if($GPH_CURRENCY=="USD"){
                $cgst_rate=$sgst_rate=$igst_rate=$cess_rate="0";
                $list_price= $net_rate = (int)$row1['GPM_USD_RATE'];
            }
            $dtl_arr = /*. (string[string]) .*/array();
            $dtl_arr['GPP_ORDER_NO'] 		= $proforma_no;
            $dtl_arr['GPP_PRODUCT_CODE'] 	= $product_code;
            $dtl_arr['GPP_PRODUCT_SKEW'] 	= $product_skew;
            $dtl_arr['GPP_QTY'] 		  	= $quantity;
            $dtl_arr['GPP_LIST_PRICE']   	= $list_price;
            $dtl_arr['GPP_SELL_RATE'] 	  	= $list_price;            
            if($same_state){
                $dtl_arr['GPP_CGST_PER'] 	= $cgst_rate;
                $dtl_arr['GPP_SGST_PER'] 	= $sgst_rate;
                $dtl_arr['GPP_CGST_AMT'] 	= round($cgst_rate*$list_price*$quantity/100,2);
                $dtl_arr['GPP_SGST_AMT'] 	= round($sgst_rate*$list_price*$quantity/100,2);
            }else{
                $dtl_arr['GPP_IGST_PER'] 	= $igst_rate;
                $dtl_arr['GPP_IGST_AMT'] 	= round($igst_rate*$list_price*$quantity/100,2);
            }
            $dtl_arr['GPP_CESS_PER'] 	= $cess_rate;
            $dtl_arr['GPP_CESS_AMT'] 	= round($cess_rate*$list_price*$quantity/100,2);
            $dtl_arr['GPP_SELL_AMT']	= round(($net_rate*$quantity));
            $dtl_arr['GPP_PRINT_ORDER'] = $ln++;
            $dtl_arr['GPP_CUSTOM_SKEWS']= "";
            $dtl_arr['GPP_NO_CLIENT'] = "";
            $insert_arr[] = $dtl_arr;
            $order_amount += $dtl_arr['GPP_SELL_AMT'];
        }
        $date_now = date("Y-m-d H:i:s");
        $hdr_arr =  array();
        $hdr_arr['GPH_ORDER_NO'] 	= $proforma_no;
        $hdr_arr['GPH_LEAD_CODE'] 	= $cust_id;
        $hdr_arr['GPH_EMP_ID'] 		= $uid;
        $hdr_arr['GPH_ORDER_DATE'] 	= $date_now;
        $hdr_arr['GPH_ORDER_AMT']	= $order_amount;
        $hdr_arr['GPH_ORDER_STATUS']= "A";
        $hdr_arr['GPH_CREATED_DATE']= $date_now;
        $hdr_arr['GPH_CURRENCY_CODE'] = 'INR';
        $hdr_arr['GPH_VALIDITY_DATE'] = add_date(date('Y-m-d'), 30);
        $hdr_arr['GPH_APPROVAL_STATUS'] = "2";
        $hdr_arr['GPH_REMARKS'] = "Proforma Created";
        $hdr_arr['GPH_PROFORMA_TO'] = get_contact_name_for_customer($cust_id);
        $hdr_arr['GPH_IS_SAME_STATE'] = "$same_state";
        $res = array_insert_query("gft_proforma_hdr", $hdr_arr,"boolean");
        if($res){
            foreach ($insert_arr as $single_arr){
                $res = array_insert_query("gft_proforma_product_dtl", $single_arr,"boolean");
                if(!$res){
                    //deleting hdr data also as a rollback event for dtl failure
                    execute_my_query("delete from gft_proforma_hdr where GPH_ORDER_NO='$proforma_no'");
                    return 'Not created proforma';
                }
            }
            if(intval($dev_complaint_id)>0) {
                execute_my_query("update gft_dev_complaints set gdc_proforma_number='$proforma_no' where gdc_complaint_id='$dev_complaint_id'");
            }
            send_proforma_mail_to_customer($proforma_no,$cust_id);
            return $proforma_no;
        } else {
            return 'Not created proforma. Error while ';
        }
        
    }else{
        $return_response = "Mismatch in product and quantiy array or empty array";
    }
    return $return_response;
    
}
/**
 * @param int $activity_id
 * 
 * @return string[int]
 */
function get_joint_emps_for_next_action($activity_id){
	$sq = " select GNJV_JOINT_EMP_ID from gft_next_joint_visit_dtl where GNJV_ACTIVITY_ID='$activity_id' ";
	$rs = execute_my_query($sq);
	$joint_arr = /*. (string[int]) .*/array();
	while ($row1 = mysqli_fetch_array($rs)){
		$joint_arr[] = $row1['GNJV_JOINT_EMP_ID'];
	}
	return $joint_arr;
}

/**
 * @param string $productc
 * @param string $from_version
 * @param string $to_version
 *
 * @return string
 */
function from_and_to_version_conditions($productc,$from_version,$to_version){
	$wh_query = "";
	if( ($from_version!='0') || ($to_version!='0') ){
		if($from_version!='0'){
			$replaced_from_version = (string)str_replace(".", "", $from_version);
			$que1 = "select gpv_release_date from gft_product_version_master where gpv_product_code='$productc' and gpv_version in ('$from_version','$replaced_from_version') ";
			$res1 = execute_my_query($que1);
			if($row1 = mysqli_fetch_array($res1) ){
				$wh_query .= " and gpv_release_date >= '".$row1['gpv_release_date']."'";
			}
		}
		if($to_version!='0'){
			$replaced_to_version = (string)str_replace(".", "", $to_version);
			$que2 = "select gpv_release_date from gft_product_version_master where gpv_product_code='$productc' and gpv_version in ('$to_version','$replaced_to_version') ";
			$res2 = execute_my_query($que2);
			if($row2 = mysqli_fetch_array($res2) ){
				$wh_query .= " and gpv_release_date <= '".$row2['gpv_release_date']."'";
			}
		}
	}
	return $wh_query;
}

/**
 * @param string $subject_string
 * 
 * @return string
 */
function remove_br_tags($subject_string){
	$subject_string = (string)str_replace("<br>", " ", $subject_string);
	return $subject_string;
}

/**
 * @param string $id
 *
 * @return string
 */
function get_status_for_escalation($id){
	$complaint_status = 'T3';
	$hq_chk = " select GLH_LEAD_CODE from gft_lead_hdr join gft_customer_support_hdr on (GLH_LEAD_CODE=GCH_LEAD_CODE) ".
			" where GLH_MAIN_PRODUCT=6 and GCH_COMPLAINT_ID='$id' ";
	$hq_res =execute_my_query($hq_chk);
	if(mysqli_num_rows($hq_res) > 0){ //HQ
		$complaint_status 	= 'T15'; //pending sqa
	}
	return $complaint_status;
}

/**
 * @param string $support_id
 * 
 * @return string
 */
function check_for_reopen_allowed($support_id){
	$err_msg = "";
	if((int)$support_id==0){
		$err_msg = "Mandatory field Support Id is Empty";
	}else {
		$chk_que =  " select GCD_ACTIVITY_DATE from gft_customer_support_hdr sh ".
					" join gft_customer_support_dtl sd on (sh.GCH_LAST_ACTIVITY_ID=sd.gcd_activity_id) ".
					" where GCH_COMPLAINT_ID='$support_id' ";
		$chk_res = execute_my_query($chk_que);
		if($chk_row = mysqli_fetch_array($chk_res)){
			$start_date = strtotime($chk_row['GCD_ACTIVITY_DATE']);
			$end_date	= strtotime(date('Y-m-d H:i:s'));
			$min_diff 	= round(($end_date-$start_date)/60);
			if($min_diff > (48*60)){ //48 hours check
				$err_msg = "Sorry, Solved Tickets can be reopened within 48 hours only. If you want to reopen after 48 hours, create a new ticket";
			}
		}
	}
	return $err_msg;
}

/**
 * @param string $support_id
 * @param string $reopen_comments
 * @param string $nature
 * @param string $emply_id
 * @param string $mygofrugal_user_id
 * @param string $update_existing_status
 * 
 * @return void
 */
function reopen_support_ticket($support_id,$reopen_comments,$nature,$emply_id='',$mygofrugal_user_id='',$update_existing_status=false){
	$complaint_status = get_status_for_escalation($support_id);
	if($update_existing_status){
	    $complaint_status = get_single_value_from_single_table("gch_current_status", "gft_customer_support_hdr", "GCH_COMPLAINT_ID", $support_id);
	}
	$by_employee = 9999;
	if((int)$emply_id!=0){
		$by_employee = (int)$emply_id;
	}
	$old_data_query=" select GCD_COMPLAINT_ID, '', now(), $by_employee, GCD_REPORTED_DATE, '$nature', '$complaint_status', GCD_CONTACT_TYPE, ".
			" GCD_CONTACT_PERSION, GCD_CUSTOMER_EMOTION, GCD_CUST_CALL_TYPE , gcd_contact_no, GCD_CONTACT_MAILID , GCD_PROCESS_EMP, ".
			" GCD_TO_DO, now(), GCD_ESTIMATED_TIME , GCD_SEVERITY, GCD_PRIORITY, GCD_LEVEL, GCD_PROMISE_MADE, GCD_PROMISE_DATE, GCD_FEEDBACK, ".
			" GCD_COMPLAINT_CODE , GCD_PRODUCT_CODE, GCD_PROBLEM_SUMMARY, GCD_PROBLEM_DESC,'','".mysqli_real_escape_string_wrapper($reopen_comments)."', GCD_VISIT_REASON, GCD_VISIT_TIMEOUT, ".
			" GCD_EXTRA_CHARGES , GCD_VISIT_NO, GCD_RECEIVED_IN_HO, GCD_ESCALATION, GCD_ESCALATION_RESP, '$mygofrugal_user_id', 'N', ".
			" GCD_SUB_STATUS,GCD_SERVICE_TYPE,GCD_EFFORT_IN_DAYS,GCD_PRODUCT_MODULE from gft_customer_support_hdr ".
			" join gft_customer_support_dtl on (gcd_activity_id=GCH_LAST_ACTIVITY_ID) where GCH_COMPLAINT_ID='$support_id'";
	$ins_quer = " insert into gft_customer_support_dtl (GCD_COMPLAINT_ID, gcd_activity_id, GCD_ACTIVITY_DATE, GCD_EMPLOYEE_ID, GCD_REPORTED_DATE, GCD_NATURE, gcd_status, GCD_CONTACT_TYPE, ".
			" GCD_CONTACT_PERSION, GCD_CUSTOMER_EMOTION, GCD_CUST_CALL_TYPE , gcd_contact_no, GCD_CONTACT_MAILID , GCD_PROCESS_EMP, ".
			" GCD_TO_DO, GCD_SCHEDULE_DATE, GCD_ESTIMATED_TIME , GCD_SEVERITY, GCD_PRIORITY, GCD_LEVEL, GCD_PROMISE_MADE, GCD_PROMISE_DATE, GCD_FEEDBACK, ".
			" GCD_COMPLAINT_CODE , GCD_PRODUCT_CODE, GCD_PROBLEM_SUMMARY, GCD_PROBLEM_DESC, GCD_UPLOAD_FILE, GCD_REMARKS, GCD_VISIT_REASON, GCD_VISIT_TIMEOUT, ".
			" GCD_EXTRA_CHARGES , GCD_VISIT_NO, GCD_RECEIVED_IN_HO, GCD_ESCALATION, GCD_ESCALATION_RESP, GCD_CUST_USERID, GCD_LAST_ACTIVITY_OF_DAY, ".
			" GCD_SUB_STATUS,GCD_SERVICE_TYPE,GCD_EFFORT_IN_DAYS,GCD_PRODUCT_MODULE) ".
			" ($old_data_query) ";
	$result_insert=execute_my_query($ins_quer);
	if($result_insert){
		$act_id=mysqli_insert_id_wrapper();
		$reopened = true;
		updated_hdr_with_last_actid($support_id, $act_id, $complaint_status,false,null,null,null,null,null,9999,null,null,'',$reopened);
		send_mailto_complaient_info($support_id,false,$reopened);
		support_sms($act_id);
	}
}

/**
 * @param string[int] $cust_id_arr
 * @param string $type
 * 
 * @return string[int][string]
 */
function get_product_for_customer_id($cust_id_arr,$type=''){
	$prod_list = /*. (string[int][string]) .*/array();
	$lead_str = "";
	if(is_array($cust_id_arr)){
		$lead_str = implode(",", $cust_id_arr);
	}
	if($lead_str==""){
		return $prod_list;
	}
	$prod_query=" SELECT concat(family.GPM_PRODUCT_CODE,'-',gpg_skew) as prod_id, ifnull(GPB_NAME,gpg_product_name) gpg_product_name ".
			" FROM gft_install_dtl_new ".
			" join gft_product_family_master fm on (GID_LIC_PCODE=fm.GPM_PRODUCT_CODE and GID_STATUS!='U') ".
			" join gft_product_family_master family on (family.GPM_PRODUCT_CODE=fm.GPM_HEAD_FAMILY) ".
			" join gft_product_group_master on (gpg_product_family_code=family.GPM_PRODUCT_CODE and substr(GID_LIC_PSKEW,1,4)=gpg_skew and gpg_status='A') ".
			" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GID_LIC_PCODE and pm.GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GID_LEAD_CODE) ".
			" left join gft_brand_product_mapping on (GBP_VERTICAL=GLH_VERTICAL_CODE and GBP_PRODUCT=concat(pm.GPM_PRODUCT_CODE,'-',gpg_skew) and GBP_EDITION=pm.GPM_PRODUCT_TYPE and GBP_STATUS=1) ".
			" left join gft_product_brand_master on (GPB_ID=GBP_BRAND_ID) ".
			" where family.GPM_LIST_IN_SUPPORT='Y' and GID_LEAD_CODE in ($lead_str) group by prod_id ";
	$prod_res = execute_my_query($prod_query);
	if(mysqli_num_rows($prod_res)==0){
		$prod_query=" SELECT concat(GPM_PRODUCT_CODE,'-',gpg_skew) as prod_id, gpg_product_name FROM gft_product_family_master ".
				" join gft_product_group_master on (gpg_product_family_code=GPM_PRODUCT_CODE and gpg_status='A') ".
				" where GPM_LIST_IN_SUPPORT='Y' group by prod_id ";
		$prod_res = execute_my_query($prod_query);
	}
	$id_holder = "id";
	$name_holder = "name";
	if($type=='async'){
		$id_holder = "value";
		$name_holder = "label";
	}
	while($data1 = mysqli_fetch_array($prod_res)){
		$prod_list[] =	array(
							$id_holder=>$data1['prod_id'],
							$name_holder=>$data1['gpg_product_name']
						);
	}
	return $prod_list;
}

/**
 * @param int $action_group
 * 
 * @return int
 */
function get_next_action_for_group_having_single_action($action_group){
	$que_act = " select GAM_ACTIVITY_ID from gft_activity_master where GAM_MAIN_GROUP='$action_group' and GAM_ACTIVITY_STATUS='A' ";
	$res_act = execute_my_query($que_act);
	if( (mysqli_num_rows($res_act)==1) ){
		$data_act = mysqli_fetch_array($res_act);
		return (int)$data_act['GAM_ACTIVITY_ID'];
	}
	return 0;
}

/**
 * @param string $pcode
 * @param string $pskew
 * @return boolean
 */
function is_client_alr_skew($pcode,$pskew){
	$client_alr = false;
	$sql1 = " select GPM_PRODUCT_CODE from gft_product_master where GPM_CLIENT_ALR_CODE='$pcode' and GPM_CLIENT_ALR_SKEW='$pskew' ";
	$sql_res1 = execute_my_query($sql1);
	if(mysqli_num_rows($sql_res1) > 0){
		$client_alr = true;
	}
	return $client_alr;
}

/**
 * @param string $lead_code
 * 
 * @return boolean
 */
function is_erp_customer($lead_code){
	$order_dtl = get_order_dtl_of_lead($lead_code,null,false,'309');
	if(count($order_dtl) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $emply_id
 * @param string $report_date
 *
 * @return boolean
 */
function is_tomorrow_plan_exists($emply_id,$report_date){
	$sql1 = " select distinct GTH_PLAN_DATE from gft_tomorrow_plan_hdr where GTH_PLAN_DATE > '$report_date' ".
			" AND GTH_EMP_ID='$emply_id' AND GTH_PLAN_STATUS='A' ";
	$hdr_res = execute_my_query($sql1);
	if(mysqli_num_rows($hdr_res) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $emply_id
 * @param string $report_date
 *
 * @return int[string]
 */
function get_daily_report_validation_count($emply_id,$report_date){
	$out_arr = /*. (int[string]) .*/array();
	if(is_authorized_group_list($emply_id, array(66,106))) {
		$app_cnt 		= get_pending_followup_appointment(1,(int)$emply_id,$report_date);
		$followup_cnt 	= get_pending_followup_appointment(2,(int)$emply_id,$report_date);
		$out_arr['appointments'] 	= isset($app_cnt['count'])?(int)$app_cnt['count']:0;
		$out_arr['followups'] 		= isset($followup_cnt['count'])?(int)$followup_cnt['count']:0;
	}
	if(is_authorized_group_list($emply_id, array(66,70,36))){ //sales, pc, pcs
		$dtl_arr = get_customer_call_activity_details_in_array($emply_id,$report_date);
		$out_arr['call_history'] = isset($dtl_arr['pending_activity'])?(int)$dtl_arr['pending_activity']:0;

		$tomo_plan = is_tomorrow_plan_exists($emply_id,$report_date);
		$out_arr['tomorrow_plan'] = ($tomo_plan)?0:1;
	}
	$out_arr['coupons'] = (int)get_pending_approval_of_complementary_coupon($emply_id,'count');
	$out_arr['expense_submit_status'] = true;
	if($emply_id<7000){
	    $expense_result = execute_my_query("select GED_EXP_ID from gft_expense_hdr where GED_EMP_ID='$emply_id' AND GED_FROM_DATE>='$report_date 00:00:00' AND GED_TO_DATE<='$report_date 23:59:59'");
	    if(mysqli_num_rows($expense_result)==0){
	        $out_arr['expense_submit_status'] = false;
	    }
	}
	return $out_arr;
}

/**
 * @param string $product_alias
 *
 * @return string[int]
 */
function get_product_info_from_alias($product_alias){
	$que = "select gpg_product_family_code,gpg_skew from gft_product_group_master where GPG_PRODUCT_ALIAS='$product_alias' ";
	$res = execute_my_query($que);
	$data_arr = /*. (string[int]) .*/array();
	if($row1 = mysqli_fetch_array($res)){
		$data_arr[0] = $row1['gpg_product_family_code'];
		$data_arr[1] = $row1['gpg_skew'];
	}
	return $data_arr;
}

/**
 * @param string $pcode
 * @param string $pgroup
 * @param string $branch
 * @param string $version
 *
 * @return string[string]
 */
function get_latest_release_info($pcode,$pgroup,$branch='Release',$version=''){
	$sql1 = " select GPV_VERSION,GPV_BUILD_BRANCH,GPV_BUILD_NUMBER,GPV_DB_BASE_SCHEMA_URL,GPV_DB_PATCH_SCHEMA_URL,GPV_ADDITIONAL_INFO,GPV_WH_BASE_SCHEMA_URL,GPV_WH_PATCH_SCHEMA_URL ".
			" from gft_product_version_master where GPV_PRODUCT_CODE='$pcode' and gpv_version_family='$pgroup' ";
	if($branch!=''){
		$sql1 .= " and GPV_BUILD_BRANCH='$branch' ";
	}
	if($version!=''){
		$sql1 .= " and GPV_VERSION='$version' ";
	}
	$sql1 .= " order by GPV_ENTERED_ON desc, GPV_BUILD_NUMBER desc limit 1 ";
	$que_res = execute_my_query($sql1);
	$ret_arr = /*. (string[string]) .*/array();
	if($data1 = mysqli_fetch_array($que_res)){
		$ret_arr['version'] 	= $data1['GPV_VERSION'];
		$ret_arr['env'] 		= $data1['GPV_BUILD_BRANCH'];
		$ret_arr['build_number']= $data1['GPV_BUILD_NUMBER'];
		$ret_arr['base_schema']	= $data1['GPV_DB_BASE_SCHEMA_URL'];
		$ret_arr['patch_schema']= $data1['GPV_DB_PATCH_SCHEMA_URL'];
		$ret_arr['additional_info'] = $data1['GPV_ADDITIONAL_INFO'];
		//$ret_arr['warehouse_base_schema_url'] = $data1['GPV_WH_BASE_SCHEMA_URL'];
		//$ret_arr['warehouse_patch_schema_url'] = $data1['GPV_WH_PATCH_SCHEMA_URL'];
	}
	return $ret_arr;
}

/**
 * @param string $title
 * @param string $message
 * @param int $noti_type
 * @param int $lead_code
 * @param int $ref_id
 * @param string[string][int] $sms_content_config
 * @param string $send_sms_emp_part
 * @param string $send_to
 *
 * @return void
 */
function send_push_notification_for_release_note($title,$message,$noti_type,$lead_code,$ref_id,$sms_content_config,$send_sms_emp_part,$send_to='employee'){
	$insert_query	=	"";
	$title			=	mysqli_real_escape_string_wrapper($title);
	$message = str_replace(array('\r','\n'), "", $message);
	$message		=	mysqli_real_escape_string_wrapper($message);
	if($noti_type==5){
		$message	=	'';
	}
	if($send_to=='employee'){
		$emp_res_device_status	=	execute_my_query("select  EMP_ID from gft_emp_auth_key inner join gft_emp_master on(EMP_ID=gem_emp_id and GEM_STATUS='A') where GEK_GCM_REGISTER_ID!='' and GEK_DEVICE_STATUS=1 and GEK_STATUS='A' and EMP_ID<7000");
		$emp_array	=	array();
		while($row_emp=mysqli_fetch_array($emp_res_device_status)){
			$emp_id	=	$row_emp['EMP_ID'];
			$emp_array[]	=	$emp_id;
			$insert =	"('1','$emp_id','$noti_type','$ref_id','$lead_code','$title','$message',1,now())";
			$insert_query	=	$insert_query.($insert_query!=''?",$insert":"$insert");
		}
		if($send_sms_emp_part=='Y'){
			$sms_content=htmlentities(get_formatted_content($sms_content_config,160));
			$emp_query = "select GEM_MOBILE, GEM_EMP_ID from gft_emp_master where GEM_STATUS='A' and GEM_OFFICE_EMPID!='' order by GEM_EMP_ID";
			$emp_res = execute_my_query($emp_query);
			while($row2 = mysqli_fetch_array($emp_res)){
				if(!in_array($row2['GEM_EMP_ID'], $emp_array)){
					entry_sending_sms($row2['GEM_MOBILE'], $sms_content, 160, $row2['GEM_EMP_ID']);
				}
			}
		}
	}
	if($send_to=='partner'){
		$partner_res_device_status	=	execute_my_query("select  EMP_ID from gft_emp_auth_key inner join gft_emp_master on(EMP_ID=gem_emp_id and GEM_STATUS='A') where GEK_GCM_REGISTER_ID!='' and GEK_DEVICE_STATUS=1 and GEK_STATUS='A'  and EMP_ID>7000");
		$partner_array	=	array();
		while($row_emp=mysqli_fetch_array($partner_res_device_status)){
			$emp_id	=	$row_emp['EMP_ID'];
			$partner_array[]=$emp_id;
			$insert =	"('1','$emp_id','$noti_type','$ref_id','$lead_code','$title','$message',1,now())";
			$insert_query	=	$insert_query.($insert_query!=''?",$insert":"$insert");
		}
		if($send_sms_emp_part=='Y'){
			$sms_content=htmlentities(get_formatted_content($sms_content_config,159));
			$part_query =" select distinct  GEM_EMP_ID , GEM_MOBILE  from gft_emp_master ".
					" join gft_role_master on (gem_role_id = grm_role_id) ".
					" join gft_role_group_master on (grg_role_id=grm_role_id and grm_status='A') ".
					" join gft_group_master on (grg_group_id=ggm_group_id and ggm_status='A' and ggm_group_type='R') ".
					" where GEM_STATUS='A' and ggm_group_id=13 order by GEM_EMP_ID ";
			$part_res = execute_my_query($part_query);
			while($row3 = mysqli_fetch_array($part_res)){
				if(!in_array($row3['GEM_EMP_ID'], $partner_array)){
					entry_sending_sms($row3['GEM_MOBILE'], $sms_content, 159, $row3['GEM_EMP_ID']);
				}
			}
		}
	}
	if($insert_query!=''){
		$sql_qry=	" INSERT INTO gft_gcm_push_notification(GPN_FOR_APP,GPN_EMP_ID,GPN_NOTIFICATION_TYPE, GPN_NOTI_REFERENCE_ID, GPN_LEAD_CODE,".
				" GPN_TITLE, GPN_MESSAGE,GPN_STATUS, GPN_CREATED_DATE_TIME) ".
				" VALUES $insert_query ";
		execute_my_query($sql_qry);
	}
}

/**
 * @param string $prev_prod_code
 * @param string $product_group_id
 * @param string $sms_check_cust
 * @param string $sms_content
 * @param string[string][int] $mail_content_config
 * @param string $notification_title
 * @param string $notification_desc
 * @param string $noti_cat
 * @param string $ref_id
 * @param string $version
 * 
 * @return string[int]
 */
function send_notification_to_customer_for_release_note($prev_prod_code,$product_group_id,$sms_check_cust,$sms_content,$mail_content_config,$notification_title,$notification_desc,$noti_cat,$ref_id,$version){
	$now_date = date("Y-m-d");
    $list_query=" select GLH_LEAD_CODE,GLH_CUST_NAME, group_concat(if(GCC_CONTACT_TYPE=4,GCC_CONTACT_NO,null)) as email, ".
			" GPB_NAME brand_name, GPB_ID brand_id, CONCAT(GID_LIC_PCODE,'-',SUBSTR(GID_LIC_PSKEW, 1, 4)) BASE_PRODUCT from gft_install_dtl_new ".
			" join gft_lead_hdr on (GID_LEAD_CODE=GLH_LEAD_CODE) ".
			" JOIN gft_product_master ON(GID_LIC_PCODE=GPM_PRODUCT_CODE AND GID_LIC_PSKEW=GPM_PRODUCT_SKEW)".
			" LEFT JOIN gft_brand_product_mapping ON(GBP_STATUS=1 AND GBP_VERTICAL=GLH_VERTICAL_CODE AND ".
			" GBP_PRODUCT=CONCAT(GID_LIC_PCODE,'-',SUBSTR(GID_LIC_PSKEW, 1, 4)) AND GPM_PRODUCT_TYPE=GBP_EDITION)".
			" LEFT JOIN gft_product_brand_master ON(GPB_ID=GBP_BRAND_ID)".
			" join gft_customer_contact_dtl on (GLH_LEAD_CODE=GCC_LEAD_CODE) ".			
			" where GID_LIC_PCODE='$prev_prod_code' ".
			" and SUBSTR(GID_LIC_PSKEW,1,4)='$product_group_id' and GID_STATUS!='U' and GLH_LEAD_TYPE in (1)  ".
			" AND GID_VALIDITY_DATE>='$now_date'  group by GID_LEAD_CODE ";
	$mail_template_id = 190;
	if($prev_prod_code=='605'){
		$mail_template_id = 230;
	}else if( ($prev_prod_code=='500') && ($product_group_id=='07.0') ){
		$mail_template_id = 233;
	}else if($prev_prod_code=='300'){
	    $mail_template_id = 232;
		$client_sub_que=" select distinct GLH_REFERENCE_GIVEN as lead,GID_INSTALL_ID as ins_id from gft_lead_hdr ".
				" join gft_install_dtl_new on (GID_LEAD_CODE=GLH_LEAD_CODE) ".
				" where GLH_LEAD_SOURCECODE=7 AND GLH_LEAD_TYPE=13 and GID_LIC_PCODE=300 and SUBSTR(GID_LIC_PSKEW,1,4)='03.0' and GID_STATUS!='U' ";
		$list_query=" select GLH_LEAD_CODE,GLH_CUST_NAME, group_concat( distinct if(GCC_CONTACT_TYPE=4,GCC_CONTACT_NO,null)) as email, ".
				" GPB_NAME brand_name,GPB_ID brand_id, GID_BASE_PRODUCT BASE_PRODUCT from gft_lead_hdr lh".
				" join gft_customer_contact_dtl on (GLH_LEAD_CODE=GCC_LEAD_CODE) ".
				" left join gft_install_dtl_new on (GID_LEAD_CODE=lh.GLH_LEAD_CODE and GID_LIC_PCODE=300 and SUBSTR(GID_LIC_PSKEW,1,4)='03.0' and GID_STATUS!='U' ) ".
				" JOIN gft_product_master ON(GID_LIC_PCODE=GPM_PRODUCT_CODE AND GID_LIC_PSKEW=GPM_PRODUCT_SKEW)".
				" LEFT JOIN gft_brand_product_mapping ON(GBP_STATUS=1 AND GBP_VERTICAL=GLH_VERTICAL_CODE AND ".
				" GBP_PRODUCT=CONCAT(GID_LIC_PCODE,'-',SUBSTR(GID_LIC_PSKEW, 1, 4)) AND if(GPM_PRODUCT_TYPE=19,3,GPM_PRODUCT_TYPE)=GBP_EDITION)".
				" LEFT JOIN gft_product_brand_master ON(GPB_ID=GBP_BRAND_ID)".
				" left join ($client_sub_que) tt on (tt.lead=lh.GLH_LEAD_CODE) ".
				" where GLH_LEAD_TYPE in (3,13) and (GID_INSTALL_ID is not null or ins_id is not null) ".
				" AND GID_VALIDITY_DATE>='$now_date' group by GLH_LEAD_CODE ";
	}
	$notification_desc = mysqli_real_escape_string_wrapper($notification_desc);
	$whats_new_content = array();
	$brand_ref_array = array();
	$rebrand_prods = get_samee_const("PRODUCT_VERSION_NOTIFICATION");
	if(in_array($prev_prod_code, explode(",", $rebrand_prods)) && $version!=""){
	    $result_whats_new = execute_my_query("select GRN_VALUE,GRN_BRAND_REF_ID,GPB_BRAND_ID,GPB_PRODUCT_VARIANT from gft_release_note_dtl ".
                    	        " JOIN gft_product_vs_brand_mapping ON(GPB_ID=GRN_BRAND_REF_ID) ".
                                " where GRN_KEY=4 AND  GRN_VERSION='$version' AND GPB_PRODUCT='$prev_prod_code-$product_group_id'");
	    while($row_whats_new=mysqli_fetch_assoc($result_whats_new)){
	        $brand_id = $row_whats_new['GPB_BRAND_ID'];
	        $product_variant = $row_whats_new['GPB_PRODUCT_VARIANT'];
	        $whats_new_content[$brand_id][$product_variant] = mysqli_real_escape_string_wrapper($row_whats_new['GRN_VALUE']);
	        $brand_ref_array[$brand_id][$product_variant] = $row_whats_new['GRN_BRAND_REF_ID'];
	    }
	    $notification_desc = "";
	}
	$list_res = execute_my_query($list_query);
	$notification_title_arr = array();
	$notification_desc_arr = array();
	$lead_arr = /*. (string[int]) .*/array();
	$url_config = get_connectplus_config();
	$release_domain = $url_config['release_notes'];
	while($row1 = mysqli_fetch_array($list_res)){
	    $lead_code = $row1['GLH_LEAD_CODE'];
	    $brand_id = $row1['brand_id'];
	    $base_product = $row1['BASE_PRODUCT'];
	    $lead_arr[] = $lead_code;		
		$notification_title_arr[$lead_code] = $row1['brand_name']." ".$notification_title;
		$notification_desc_arr[$lead_code] = $notification_desc;
		if(isset($whats_new_content[$brand_id][$base_product]) && $whats_new_content[$brand_id][$base_product]!=""){
		    $notification_desc_arr[$lead_code] = "Whats New<br>".$whats_new_content[$brand_id][$base_product].
		    "<p><p><a href=\"$release_domain?cust_id=$lead_code&version=$version\" target=\"_blank\">Read Release Notes</a>";
		}
		if($sms_check_cust=='Y'){
			$mail_content_config['Customer_Name']	=	array($row1['GLH_CUST_NAME']);
			entry_sending_sms_to_customer(null, $sms_content, 5, $row1['GLH_LEAD_CODE']);
			send_formatted_mail_content($mail_content_config, 29, $mail_template_id, null, null, array($row1['email']));
		}
	}
	if(count($lead_arr)>0){
		$lead_str = implode(',',$lead_arr);
		$select_query = " select GCL_USER_ID, GCA_ACCESS_LEAD from gft_customer_login_master ".
				" join gft_customer_access_dtl on (GCA_USER_ID=GCL_USER_ID and GCA_ACCESS_STATUS=1) ".
				" where GCA_ACCESS_LEAD in ($lead_str) and GCL_GCM_REGISTER_ID!='' and GCL_DEVICE_STATUS=1 and GCL_EMP_ID=0 ".
				" group by GCL_USER_ID ";
		$res = execute_my_query($select_query);
		$insert_query = $put_comma='';
		while ($row_data = mysqli_fetch_array($res)){
			$user_id = $row_data['GCL_USER_ID'];
			$lead_code = $row_data['GCA_ACCESS_LEAD'];
			$push_notification_title = isset($notification_title_arr[$lead_code])?$notification_title_arr[$lead_code]:$notification_title;
			$notification_desc = isset($notification_desc_arr[$lead_code])?$notification_desc_arr[$lead_code]:$notification_desc;
			if($notification_desc!=""){
			    $insert_query .= $put_comma."('2','$user_id','$noti_cat','$ref_id','','$push_notification_title','".$notification_desc."',1,now())";
			    $put_comma = ',';
			}			
		}
		if($insert_query!=''){
			$sql_qry=	" INSERT INTO gft_gcm_push_notification(GPN_FOR_APP,GPN_EMP_ID,GPN_NOTIFICATION_TYPE, GPN_NOTI_REFERENCE_ID, GPN_LEAD_CODE,".
					" GPN_TITLE, GPN_MESSAGE,GPN_STATUS, GPN_CREATED_DATE_TIME) ".
					" VALUES $insert_query ";
			execute_my_query($sql_qry);
		}
		if($prev_prod_code=='300'){
		    $ins_values = "";
		    $comma = "";
		    foreach ($notification_desc_arr as $temp_lead => $content){
		        if($content!=''){
		            $auth_key = uniqid("PK");
		            $ins_values .= $comma."('$temp_lead','300','03.0','$version','$auth_key',now(),1)";
		            $comma = ",";
		            $release_url = "$release_domain?cust_id=$temp_lead&version=$version";
		            $ver_arr = explode(".", $version);
		            notify_pos_product($temp_lead, '', 'version_update', '','300',$ver_arr[0],false,$release_url,$version,date('Y-m-d'),$auth_key);
		        }
		    }
		    if($ins_values!=""){
		        $patch_insert = " insert into gft_auto_patch (GAP_LEAD_CODE,GAP_PRODUCT_CODE,GAP_SKEW_GROUP,GAP_VERSION,GAP_AUTH_KEY,GAP_CREATED_DATE,GAP_KEY_STATUS) values $ins_values ";
		        execute_my_query($patch_insert);
		    }
		}
	}
	//for employees and partners
	$q1 =  " select EMP_ID from gft_emp_auth_key inner join gft_emp_master on (EMP_ID=gem_emp_id and GEM_STATUS='A') ".
	   	   " where GEK_GCM_REGISTER_ID!='' and GEK_DEVICE_STATUS=1 and GEK_STATUS='A' ";
	$q1_res	= execute_my_query($q1);
	$id_arr	= array();
	while($row_emp=mysqli_fetch_array($q1_res)){
	    $id_arr[]	= $row_emp['EMP_ID'];
	}
	$emp_insert = $put_comma ='';
	if(count($whats_new_content) > 0){
	    foreach ($whats_new_content as $bid => $val){
	        $bname = get_single_value_from_single_table("GPB_NAME", "gft_product_brand_master", "GPB_ID", $bid);
	        $nf_title = " $bname $version released";
	        foreach ($val as $variant => $inner_val){
	            if($inner_val!=""){
	                $brand_ref_id = $brand_ref_array[$bid][$variant];
	                $nf_desc  = "<b>Whats New</b><br> ".$inner_val.
	                "<br><a href=\"$release_domain?brand_ref=$brand_ref_id&version=$version\" target=\"_blank\">Read Release Notes</a>";
	                foreach ($id_arr as $eid){
	                    $emp_insert .= $put_comma."('1','$eid','$noti_cat',0,0,'$nf_title','$nf_desc',1,now())";
	                    $put_comma = ",";
	                }
	            }	            
	        }
	    }
	}else if($notification_desc != ""){
	    foreach ($id_arr as $eid){
	        $emp_insert .= $put_comma."('1','$eid','$noti_cat',0,0,'$notification_title','$notification_desc',1,now())";
	        $put_comma = ",";
	    }
	}
	if($emp_insert!=""){
	    $insq = " INSERT INTO gft_gcm_push_notification(GPN_FOR_APP,GPN_EMP_ID,GPN_NOTIFICATION_TYPE, GPN_NOTI_REFERENCE_ID, ".
	   	        " GPN_LEAD_CODE, GPN_TITLE, GPN_MESSAGE,GPN_STATUS, GPN_CREATED_DATE_TIME) VALUES $emp_insert ";
	    execute_my_query($insq);
	}
	return $lead_arr;
}

/**
 * @param string $tm_cust_id
 * @param string $user_name
 * @param string $user_password
 *
 * @return string
 */
function get_task_manger_auth_token($tm_cust_id,$user_name,$user_password){
	global $log;
	$config_arr = get_connectplus_config();
	$cloud_url	= str_replace("{{customerId}}", $tm_cust_id, $config_arr['cloud_domain']);
	$key_url = "$cloud_url/task_manager/api/api_auth_token?username=$user_name&credential=$user_password";
	$ch = curl_init($key_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	$response_data 	= (string)curl_exec($ch);
	$response_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$err_msg 		= curl_error($ch);
	curl_close($ch);
	$x_auth_token = '';
	if($response_code==200){
		$x_auth_token = $response_data;
	}else{
		$log_data = /*. (string[string]) .*/array();
		$log_data['code'] = $response_code;
		$log_data['response'] = $response_data;
		if($err_msg!=''){
			$log_data['curl_error'] = $err_msg;
		}
		$log->logInfo("Error in Getting Key - ".json_encode($log_data));
	}
	return $x_auth_token;
}

/**
 * @param string $salesManIds
 * 
 * @return int[string][string]
 */
function get_location_dashboard_summary($salesManIds){
	$cp_config = get_connectplus_config();
	$key_url = $cp_config['integ_portal']."/location-tracker/employee_tracking_status?customerId=".LOCATION_TRACKER_ID."&salesManId=$salesManIds";
	$ret_arr = array();
	$req_arr = do_curl_to_connectplus(LOCATION_TRACKER_ID,$key_url,'',array("Content-Type: application/json"),"GET");
	if($req_arr['response_code']==200){
		$ret_arr = json_decode($req_arr['response_body'],true);
	}
	return $ret_arr;
}

/**
 * @param string $tm_cust_id
 * @param string $start_date
 * @param string $end_date
 * @param string $emply_id
 * @param string $connection_timeout
 * 
 * @return int[string][string]
 */
function get_task_manager_activity_summary($tm_cust_id,$start_date,$end_date,$emply_id,$connection_timeout=""){
	global $log;
	$config_arr = get_connectplus_config();
	$cloud_url	= str_replace("{{customerId}}", $tm_cust_id, $config_arr['cloud_domain']);
	$api_key    = $config_arr['tm_key'];
	$key_url = "$cloud_url/task_manager_internal/api/v3/tasks/activityReport?emp_id=$emply_id&from_date=$start_date&to_date=$end_date";
	$header_arr = array("Content-Type: application/json","x-api-key:$api_key");
	$ch = curl_init($key_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
	if($connection_timeout>0){
	    curl_setopt($ch, CURLOPT_TIMEOUT, $connection_timeout);
	}
	$response_data 	= (string)curl_exec($ch);
	$response_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$err_msg 		= curl_error($ch);
	curl_close($ch);
	$ret_arr = /*. (int[string][string]) .*/array();
	if($response_code==200){
		$ret_arr = /*. (int[string][string]) .*/json_decode($response_data,true);
	}else{
		$log_data = /*. (string[string]) .*/array();
		$log_data['code'] = $response_code;
		$log_data['response'] = $response_data;
		if($err_msg!=''){
			$log_data['curl_error'] = $err_msg;
		}
		$log->logInfo("Error in Task Manager Activity Summary - ".json_encode($log_data));
		$ret_arr['response']['error'] = "Sorry, not able to fetch details from Learning Manager";
	}
	return $ret_arr;

}
/**
 * @param string $emply_id
 * @param string $report_date
 * @param boolean $show_table_hdr
 * 
 * @return string
 */
function get_task_manager_dtl_for_daily_report($emply_id,$report_date,$show_table_hdr=true){
	global $mydelight_tm_cust_id;
	$sql1 = " select GLM_LOGIN_NAME,GLM_PASSWORD from gft_login_master where GLM_EMP_ID='$emply_id' ";
	$res1 = execute_my_query($sql1);
	$output = "";
	if($row1 = mysqli_fetch_array($res1)){
	    $red_color = "style='color:red;'";
	    $not_able_to_fetch = "";
	    $mtd_summary = array();
	    $today_summary 	= get_task_manager_activity_summary($mydelight_tm_cust_id,$report_date,$report_date,$emply_id,20);	    
	    if(isset($today_summary['response']['error']) && $today_summary['response']['error']!=''){
	        $not_able_to_fetch = "<tr><th colspan=3><font $red_color>".$today_summary['response']['error']."</font></th></tr>";
	    }else{
	        $mtd_summary 	= get_task_manager_activity_summary($mydelight_tm_cust_id,"",$report_date,$emply_id,20);
	    }
	    
		$today_arr['ass_comp'] 	 = isset($today_summary['pending_by_status']['assessment_done'])?(int)$today_summary['pending_by_status']['assessment_done']:0;
		$today_arr['learn_comp'] = isset($today_summary['pending_by_status']['completed'])?(int)$today_summary['pending_by_status']['completed']:0;
		$today_arr['learn_ip'] 	 = isset($today_summary['pending_by_status']['in_progress'])?(int)$today_summary['pending_by_status']['in_progress']:0;
		$today_arr['pend_learn'] = isset($today_summary['pending_by_status']['pending_learning'])?(int)$today_summary['pending_by_status']['pending_learning']:0;
		$today_arr['reopened'] 	 = isset($today_summary['pending_by_status']['reopened'])?(int)$today_summary['pending_by_status']['reopened']:0;
		
		$mtd_arr['ass_comp']	= isset($mtd_summary['pending_by_status']['assessment_done'])?(int)$mtd_summary['pending_by_status']['assessment_done']:0;
		$mtd_arr['learn_comp'] 	= isset($mtd_summary['pending_by_status']['completed'])?(int)$mtd_summary['pending_by_status']['completed']:0;
		$mtd_arr['learn_ip'] 	= isset($mtd_summary['pending_by_status']['in_progress'])?(int)$mtd_summary['pending_by_status']['in_progress']:0;
		$mtd_arr['pend_learn'] 	= isset($mtd_summary['pending_by_status']['pending_learning'])?(int)$mtd_summary['pending_by_status']['pending_learning']:0;
		$mtd_arr['reopened'] 	= isset($mtd_summary['pending_by_status']['reopened'])?(int)$mtd_summary['pending_by_status']['reopened']:0;
		
		$sticky_task = isset($today_summary['pending_by_importance']['pending_sticky'])?(int)$today_summary['pending_by_importance']['pending_sticky']:0; 
		
		$green_color = "style='color:green;'";
		$output .= " <table border=1 width='50%' cellpadding='5'>".
				   	($show_table_hdr?" <tr><th colspan=3 style='color:white;background-color:#009688;'>Learning Progress Report</th></tr>":"").
				   	" $not_able_to_fetch ".
				   	" <tr><th colspan=3> Pending Sticky Tasks - <font $red_color>$sticky_task</font></th></tr>".
				   	" <tr style='background-color:#80cbc4;'><th>Summary</th><th>Today</th><th>Overall</th></tr>".
				   	" <tr><td>Assessment Completed</td><td>".$today_arr['ass_comp']."</td><td>".$mtd_arr['ass_comp']."</td></tr>".
				   	" <tr><td>Learning Completed</td><td $green_color>".$today_arr['learn_comp']."</td><td $green_color>".$mtd_arr['learn_comp']."</td></tr>".
				   	" <tr><td>Learning In-progress</td><td>".$today_arr['learn_ip']."</td><td>".$mtd_arr['learn_ip']."</td></tr>".
				   	" <tr><td>Pending Learning</td><td>".$today_arr['pend_learn']."</td><td>".$mtd_arr['pend_learn']."</td></tr>".
				   	" <tr><td>Learn Again</td><td $red_color>".$today_arr['reopened']."</td><td $red_color>".$mtd_arr['reopened']."</td></tr>".
				   " </table> ";
	}
	return $output;

}

/**
 * @param string $lead_code
 * 
 * @return string[string]
 */
function get_referral_partner_details($lead_code){
	$ret_arr = /*. (string[string]) .*/array();	
	$sql1 = " select cre.GLH_LEAD_CODE as created_lc,cre.GLH_LEAD_SUBTYPE as created_lt, ".
			" rfp.GLH_LEAD_CODE as source_lc,rfp.GLH_LEAD_SUBTYPE as source_lt ".
			" from gft_lead_hdr lh ".
			" left join gft_cp_info on (CGI_EMP_ID=GLH_CREATED_BY_EMPID) ".
			" left join gft_lead_hdr cre on (cre.GLH_LEAD_CODE=CGI_LEAD_CODE) ".
			" left join gft_lead_hdr rfp on (rfp.GLH_LEAD_CODE=lh.GLH_REFERENCE_OF_PARTNER) ".
			" where lh.GLH_LEAD_CODE='$lead_code' ";
	$res1 = execute_my_query($sql1);
	$partner_lead_code = 0;
	if($row1 = mysqli_fetch_array($res1)){
		if((int)$row1['created_lt']==10){
			$partner_lead_code = (int)$row1['created_lc'];
		}elseif ((int)$row1['source_lt']==10){
			$partner_lead_code = (int)$row1['source_lc'];
		}else{ //check for lead duplicate activity for this customer id by any referral partner
			$que1 = " select CGI_EMP_ID,CGI_LEAD_CODE,GLD_DATE from gft_activity ".
					" join gft_cp_info on (CGI_EMP_ID=GLD_EMP_ID) ".
					" join gft_lead_hdr cre on (cre.GLH_LEAD_CODE=CGI_LEAD_CODE and cre.GLH_LEAD_SUBTYPE=10) ".
					" where GLD_LEAD_CODE='$lead_code' and GLD_VISIT_NATURE=70 ";
			$que_res1 = execute_my_query($que1);
			while ($que_data1 = mysqli_fetch_array($que_res1)){
				$activity_timestamp = $que_data1['GLD_DATE'];
				$ref_part_emp_id    = $que_data1['CGI_EMP_ID'];
				$start_timestamp = date('Y-m-d H:i:s',strtotime('-90 days',strtotime($activity_timestamp)));
				$chk_que =  " select GLD_ACTIVITY_ID from gft_activity where GLD_LEAD_CODE='$lead_code' and GLD_EMP_ID!='$ref_part_emp_id' ".
							" and GLD_DATE > '$start_timestamp' and GLD_DATE < '$activity_timestamp' ";
				$chk_res = execute_my_query($chk_que);
				if(mysqli_num_rows($chk_res)==0){ //checking for last activity as 90 days
					$partner_lead_code = $que_data1['CGI_LEAD_CODE'];
				}
			}
		}
	}
	if($partner_lead_code!=0){
		$ref_part_one_year = get_samee_const("Referral_Partner_One_Year");
		$one_year_arr = explode(",", $ref_part_one_year);
		array_map("trim", $one_year_arr);
		$sql2 = " select CGI_EMP_ID,GLH_LEAD_CODE,concat(GLH_CUST_NAME,'-',ifnull(GLH_CUST_STREETADDR2,'')) partner_name ".
				" from gft_lead_hdr join gft_cp_info on (GLH_LEAD_CODE=CGI_LEAD_CODE) ".
				" where GLH_LEAD_CODE='$partner_lead_code' and GLH_COUNTRY='India' and CGI_STATUS=10 "; //CGI_STATUS is for active partner
		$res2 = execute_my_query($sql2);
		if($row2 = mysqli_fetch_array($res2)){
			$cgi_emp_id = $row2['CGI_EMP_ID'];
			$order_dtl_arr = get_order_dtl_of_lead($lead_code,null,true);
			if(in_array($cgi_emp_id, $one_year_arr)){
				$first_order_date = isset($order_dtl_arr[0][1])?$order_dtl_arr[0][1]:''; 
				if( ($first_order_date!='') && (datediff($order_dtl_arr[0][1], date('Y-m-d')) > 365) ){
					return $ret_arr;
				}
			}else{
				if(count($order_dtl_arr) > 0){
					return $ret_arr;
				}				
			}
			$ret_arr['partner_lead_code'] 	= $row2['GLH_LEAD_CODE'];
			$ret_arr['partner_name'] 		= $row2['partner_name'];
			$ret_arr['commission_percent'] 	= '10';
		}
	}
	return $ret_arr;
}

/**
 * @param string $partner_lead_code
 *
 * @return string[string]
 */
function get_partner_profile_dtl($partner_lead_code){
	$sql2 = " select GPP_QUESTION_ID,GPP_ANSWER from gft_partner_profile_dtl where GPP_LEAD_CODE='$partner_lead_code' ";
	$res2 = execute_my_query($sql2);
	$ret_arr = /*. (string[string]) .*/array();
	while($row2 = mysqli_fetch_array($res2)){
		$gpp_answer = $row2['GPP_ANSWER'];
		switch ($row2['GPP_QUESTION_ID']){
			case '418':$ret_arr['pan_no'] 		= $gpp_answer;break;
			case '419':$ret_arr['account_no'] 	= $gpp_answer;break;
			case '421':$ret_arr['bank_name'] 	= $gpp_answer;break;
			case '422':$ret_arr['branch_name']	= $gpp_answer;break;
			case '457':$ret_arr['ifsc_code'] 	= $gpp_answer;break;
			case '458':$ret_arr['ac_holder_name']= $gpp_answer;break;
			default:break;
		}
	}
	return $ret_arr;
}

/**
 * @param string $god_order_no
 * 
 * @return void
 */
function send_referral_partner_notification($god_order_no){
	$sql1 = " select CGI_EMP_ID,GOD_LEAD_CODE,GOD_ORDER_REFFERER,GOD_COMMISSION_PER,GOD_COLLECTION_REALIZED,GOD_BALANCE_AMT, ".
			" lh.GLH_LEAD_CODE as cust_id,lh.GLH_CUST_NAME as cust_name, rp.GLH_CUST_NAME as part_name,cgi_incharge_emp_id,GEM_EMP_NAME ".
			" from gft_order_hdr join gft_cp_info on (CGI_LEAD_CODE=GOD_ORDER_REFFERER) ".
			" join gft_emp_master on (GEM_EMP_ID=cgi_incharge_emp_id) ".
			" join gft_lead_hdr rp on (rp.GLH_LEAD_CODE=CGI_LEAD_CODE and rp.GLH_LEAD_SUBTYPE=10) ".
			" join gft_lead_hdr lh on (lh.GLH_LEAD_CODE=GOD_LEAD_CODE) ".
			" where GOD_ORDER_NO='$god_order_no' and GOD_COMMISSION_STATUS!=1 and GOD_BALANCE_AMT <= 0 and GOD_COMMISSION_PER > 0";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$god_lead_code 	 = $row1['GOD_LEAD_CODE'];
		$god_order_refer = $row1['GOD_ORDER_REFFERER'];
		$commission_per	 = $row1['GOD_COMMISSION_PER'];
		$cgi_emp_id		 = $row1['CGI_EMP_ID'];
		$realized_amt	 = (int)$row1['GOD_COLLECTION_REALIZED'];
		$partner_incharge= $row1['cgi_incharge_emp_id'];
		$config_arr = array(
						'Partner_Name'=>array($row1['part_name']),
						'Customer_Name'=>array($row1['cust_name']),
						'Customer_Id'=>array($row1['cust_id']),
						'AMOUNT'=>array($realized_amt),
						'Employee_Name'=>array($row1['GEM_EMP_NAME'])
					  );
	}else{
		return ;
	}
	$profile_dtl 	= get_partner_profile_dtl($god_order_refer);
	$partner_ac_no 	= isset($profile_dtl['account_no'])?$profile_dtl['account_no']:'';
	if($partner_ac_no==''){
		send_formatted_notification_content($config_arr, 0, 83, 1, $cgi_emp_id);
		send_formatted_notification_content($config_arr, 0, 94, 1, $partner_incharge);
	}else{
		//no outstanding and bank details available
		send_formatted_notification_content($config_arr, 0, 95, 1, $cgi_emp_id);
		send_formatted_notification_content($config_arr, 0, 96, 1, 0);
	}
}

/**
 * @param string $customerId
 * @param boolean $only_active
 * @param boolean $only_partner
 * 
 * @return string[int][int]
 */
function get_eligible_demo_incentive_employees($customerId,$only_active=false,$only_partner=false){
	$ret_arr = /*. (string[int][int]) .*/array();
	$wh_cond = " and GLD_LEAD_CODE='$customerId' and GLD_VISIT_NATURE in (2,48) and GLD_ACTIVITY_STATUS_ID=2 and GEM_EMP_ID<7000 and GEM_STATUS='A' ";
	$sql1 = " select GEM_EMP_ID,GEM_EMP_NAME,GEM_OFFICE_EMPID,GEM_STATUS from gft_activity ".
			" join gft_emp_master em on (em.GEM_EMP_ID=GLD_EMP_ID) ".
			" where 1 $wh_cond ".
			" union ".
			" select GEM_EMP_ID,GEM_EMP_NAME,GEM_OFFICE_EMPID,GEM_STATUS from gft_activity ".
			" join gft_joint_visit_dtl jv on (jv.GJV_ACTIVITY_ID=gld_activity_id) ".
			" join gft_emp_master em on (gem_emp_id=jv.GJV_JOINT_EMP_ID or gem_emp_id=jv.GJV_EMP_ID) ".
			" where 1 $wh_cond group by GEM_EMP_ID ";
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1)==0){
		return $ret_arr;
	}
	while ($row1 = mysqli_fetch_array($res1)){
		$gem_emp_id 	= $row1['GEM_EMP_ID'];
		$gem_emp_name 	= $row1['GEM_EMP_NAME'];
		$gem_status		= $row1['GEM_STATUS'];
		if((int)$row1['GEM_OFFICE_EMPID']==0){ //to skip partners and partner employees
			$gem_status = 'I';
		}
		if( ($only_active) && ($gem_status=='I') ){
			continue;
		}
		$ret_arr[] = array($gem_emp_id,$gem_emp_name,$gem_status);
	}
	return $ret_arr;
}

/**
 * @param string $customerId
 * @param mixed $not_in_emps
 * @param boolean $incentive_req_activity
 * 
 * @return string[int][int]
 */
function get_eligible_agile_incentive_employees($customerId,$not_in_emps=null,$incentive_req_activity=true){
	$last_date = date('Y-m-d',strtotime("-90 days"));
	$wh_cond = " and GLD_LEAD_CODE='$customerId' and GLD_VISIT_DATE >= '$last_date' and GLD_ACTIVITY_STATUS_ID=2 ";
	$ord_dtl = get_order_dtl_of_lead($customerId,null,true,'','',false);
	$first_order_date = isset($ord_dtl[0][1])?$ord_dtl[0][1]:'';
	if($first_order_date!=''){
		$wh_cond .= " and GLD_VISIT_DATE >= '$first_order_date' ";
	}
	$inc_cond = $incentive_req_activity ? " and GAM_INCENTIVE_REQ='Y' " : "";
	$que1 = " select GLD_ACTIVITY_BY as emp_id,GEM_EMP_NAME,GAM_ACTIVITY_ID,GAM_ACTIVITY_DESC,GLD_ACTIVITY_ID from gft_activity ".
			" join gft_activity_master on (GLD_VISIT_NATURE=GAM_ACTIVITY_ID) ".
			" join gft_emp_master em on (GEM_STATUS='A' and gem_emp_id=GLD_ACTIVITY_BY) ".
			" where 1 $inc_cond $wh_cond GROUP BY emp_id,GAM_ACTIVITY_ID ".
			" union ".
			" select gem_emp_id as emp_id,GEM_EMP_NAME,GAM_ACTIVITY_ID,GAM_ACTIVITY_DESC,GLD_ACTIVITY_ID from gft_activity ". 
			" join gft_activity_master on(GLD_VISIT_NATURE=GAM_ACTIVITY_ID)   ".
			" join gft_joint_visit_dtl jv on (jv.GJV_ACTIVITY_ID=gld_activity_id) ".
			" join gft_emp_master em on (GEM_STATUS='A' and (gem_emp_id=jv.GJV_JOINT_EMP_ID or gem_emp_id=jv.GJV_EMP_ID) ) ".
			" where 1 $wh_cond GROUP BY emp_id,GAM_ACTIVITY_ID";
	$res_inc_dtl = execute_my_query($que1);
	$ret_arr = /*. (string[int][int]) .*/array();
	while ($row1 = mysqli_fetch_array($res_inc_dtl)){
		$gld_emp_id = $row1['emp_id'];
		$gld_act_id = $row1['GLD_ACTIVITY_ID'];
		$gem_name	= $row1['GEM_EMP_NAME'];
		$act_desc	= $row1['GAM_ACTIVITY_DESC'];
		if( is_array($not_in_emps) && (count($not_in_emps) > 0) && in_array($gld_emp_id, $not_in_emps) ){
			continue;
		}
		$val = $gld_emp_id."-".$gld_act_id;
		$lab = $gem_name." - ".$act_desc;
		$ret_arr[] = array($val,$lab);
	}
	return $ret_arr;
}

/**
 * @param string $customerId
 * @param string $orderNo
 *
 * @return boolean
 */
function is_pd_signed_off($customerId,$orderNo){
    
	$sql1 = " select GAH_AUDIT_ID from gft_audit_hdr where GAH_LEAD_CODE='$customerId' and GAH_ORDER_NO='$orderNo' ".
			" and GAH_AUDIT_TYPE=19 and GAH_TRAINING_STATUS=6 ";
	$res1 = execute_my_query($sql1);
	$res2 = execute_my_query("SELECT GPD_LEAD_CODE FROM gft_product_delivery_hdr WHERE GPD_LEAD_CODE='$customerId' AND GPD_ORDER_NO='$orderNo' AND GPD_CURRENT_STATUS=16"); 
	if((mysqli_num_rows($res1) > 0) || (mysqli_num_rows($res2) > 0)){
		return true;
	}
	return false;
}

/**
 * @param string $customerId
 * @param string $skip_order
 * @param string $to_dt_cond
 * 
 * @return string
 */
function get_order_type_for_agile($customerId,$skip_order='',$to_dt_cond=''){
	$ord_type = 'first';
	$ord_dtl_arr = get_order_dtl_of_lead($customerId,null,true,'','',false,$to_dt_cond,array('8'));
	$first_order = isset($ord_dtl_arr[0][0])?$ord_dtl_arr[0][0]:'';
	if( ($first_order!='') && ($skip_order!=$first_order) ){
		if($ord_dtl_arr[0][2]=='Yes'){
			if(is_pd_signed_off($customerId,$first_order)){
				$ord_type = 'upsell';
			}
		}else{
			$ord_type = 'upsell';
		}
	}
	return $ord_type;
}

/**
 * @param string $orderNo
 * @param int $incentive_type
 * @param string[int] $attr_perc_arr
 * @param int $attr_id
 * @param int $owner_emp
 * @param string $ref_activity_id
 * @param boolean $only_partner
 *  
 * @return void
 */
function insert_orderwise_incentive_owner($orderNo,$incentive_type,$attr_perc_arr,$attr_id,$owner_emp,$ref_activity_id='',$only_partner=false){
    $today_date_time = date('Y-m-d H:i:s');
	if(!isset($attr_perc_arr[$attr_id])){ //shouldn't come
		$attr_perc_arr[$attr_id] = "-1";
	}
	if($owner_emp>=7000){
	    return;
	}
	if($only_partner){
	    if($attr_id==4){
	        return;
	    }
	    $valid_receipt = get_valid_receipts_from_partner_agile($orderNo);
	    if(!$valid_receipt){
	       return ; // Incentive amount should give only for order adjustment value after partner effective date
	    }
	}
	$ins_arr = array(
			'GOI_ORDER_NO'=>$orderNo,'GOI_ATTRIBUTE_ID'=>$attr_id,'GOI_INCENTIVE_TYPE'=>$incentive_type,
			'GOI_ATTRIBUTE_PERCENT'=>$attr_perc_arr[$attr_id],'GOI_OWNER_EMP'=>$owner_emp,
			'GOI_CREATED_DATE'=>$today_date_time,'GOI_ACTIVITY_ID'=>$ref_activity_id
	);
	$key_arr = array('GOI_ORDER_NO'=>$orderNo,'GOI_ATTRIBUTE_ID'=>$attr_id);
	array_update_tables_common($ins_arr, "gft_orderwise_incentive_owner", $key_arr, null, SALES_DUMMY_ID,null,null,$ins_arr);
}

/**
 * @param string $order_number
 * 
 * @return string[int]
 */
function get_attribute_percent_in_array($order_number){
    $is_pd_order = is_pd_implementation_required($order_number);
	$m_res = execute_my_query("select GIA_ID,GIA_ATTRIBUTE_PERCENT,GIA_PD_PERCENT from gft_incentive_attribute_master");
	$attr_perc_arr = /*. (string[int]) .*/array();
	while ($m_row = mysqli_fetch_array($m_res)){
	    $attr_perc_arr[(int)$m_row['GIA_ID']] = ($is_pd_order) ? $m_row['GIA_PD_PERCENT'] : $m_row['GIA_ATTRIBUTE_PERCENT'];
	}
	return $attr_perc_arr;
}

/**
 * @param string $order_number
 *
 * @return boolean
 */
function is_pd_implementation_required($order_number){
    $is_pd_order = false;
    $impl_required = get_single_value_from_single_table("GOD_IMPL_REQUIRED", "gft_order_hdr", "GOD_ORDER_NO", $order_number);
    if(strcasecmp($impl_required, "Yes")==0){
        $is_pd_order = true;
    }
    return $is_pd_order;
}


/**
 * @param string $orderNo
 * @param string $demo_by_emp
 * @param string $helped_by_dtl
 * @param int $corp_joint_emp
 * @param int $corp_joint_perc
 * @param boolean $only_partner
 * @param string $custCode
 * @param string $oppr_by_dtl
 * @param string $cp_lead_code
 * @param boolean $is_dealer_order
 * 
 * @return void
 */
function update_orderwise_sales_incentive_owner($orderNo,$demo_by_emp,$helped_by_dtl='',$corp_joint_emp=0,$corp_joint_perc=0,$only_partner=false,$custCode="",$oppr_by_dtl="",$cp_lead_code="",$is_dealer_order=false){ 
    $chk1 = execute_my_query(" select GOI_ORDER_NO from gft_orderwise_incentive_owner where GOI_ORDER_NO='$orderNo' ");
    if(mysqli_num_rows($chk1) > 0){
		return;
	}
	update_license_and_service_cost_for_order($orderNo);
	$que1 = " select GOP_ORDER_NO from gft_order_product_dtl ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			" where GOP_ORDER_NO='$orderNo' and (GFT_SKEW_PROPERTY not in (4,5) or GOP_PRODUCT_CODE=703) ";
	$que_res = execute_my_query($que1);
	if(mysqli_num_rows($que_res)==0){
		return ; //only alr order, so not required
	}
	if($only_partner){
	    $valid_receipt = get_valid_receipts_from_partner_agile($orderNo);
	    if(!$valid_receipt){
	       return ; // Incentive amount should give only for order adjustment value after partner effective date
	    }
	}
	$new_inc_date = db_date_format(trim(get_samee_const("New_Incentive_Order_Date")));
	$ord_que =  " select GOD_LEAD_CODE,GOD_EMP_ID,GOD_INCHARGE_EMP_ID,GLH_CUST_NAME,GOD_ORDER_DATE,GOD_LICENSE_COST, ".
	   	        " GOD_SERVICE_WITH_DELIVERY_COST,GOD_SERVICE_WITHOUT_DELIVERY_COST from gft_order_hdr ".
				" join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ".
				" join gft_emp_master em on (GEM_EMP_ID=if(GOD_EMP_ID=9999,GOD_INCHARGE_EMP_ID,GOD_EMP_ID)) ".
				" where GOD_ORDER_NO='$orderNo' and GOD_ORDER_AMT > 0 ";
	if(!$only_partner){
	    $ord_que .= " and GEM_OFFICE_EMPID > 0 ";
	}
	$ord_res = execute_my_query($ord_que);
	$lic_cost = $ser_with_del = $ser_without_del = 0;
	if($ord_row = mysqli_fetch_array($ord_res)){
		$customerId = $ord_row['GOD_LEAD_CODE'];
		$customerName = $ord_row['GLH_CUST_NAME'];
		$orderDate    = $ord_row['GOD_ORDER_DATE'];
		$lic_cost     = $ord_row['GOD_LICENSE_COST'];
		$ser_with_del     = (int)$ord_row['GOD_SERVICE_WITH_DELIVERY_COST'] + get_pcs_entry_skew_sell_rate($orderNo);
		$ser_without_del  = (int)$ord_row['GOD_SERVICE_WITHOUT_DELIVERY_COST'];
		if($only_partner && $custCode != ""){
		    $customerId = $custCode;
		    $customerName = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", $customerId);
		}
		$order_by 	= (int)$ord_row['GOD_EMP_ID'];
		if($order_by==9999){
			$order_by = $ord_row['GOD_INCHARGE_EMP_ID'];
		}
	}else{
		return;
	}
	$is_old_incentive = (strtotime($orderDate) < strtotime($new_inc_date));
	$nc_config = /*. (string[string][int]) .*/array();
	if($helped_by_dtl=='store'){
		$nc_config = array(
				'Employee_Name'=>array(get_single_value_from_single_table("GEM_EMP_NAME", "gft_emp_master", "GEM_EMP_ID", $order_by)),
				'Customer_Name'=>array($customerName),
				'comp_id'=>array($customerId),
				'mid'=>array($orderNo)
		);
	}
	$ord_type = get_order_type_for_agile($customerId,$orderNo);
	$lead_type= get_lead_type_for_lead_code($customerId);
	// PC incentive only for first order
	/*if($only_partner && (($ord_type!='first') || ($lead_type=='3'))){
	    return;
	}*/
	$attr_perc_arr = get_attribute_percent_in_array($orderNo);
	$today_date_time = date('Y-m-d H:i:s');
	$impl_required = is_pd_implementation_required($orderNo); //PD implementation
	$business_manager_collection = ($impl_required ? (100-10) : 100); // For business manger calculation
	$business_manager = 0;
	if($is_dealer_order){
	    $business_manager = get_single_value_from_single_table('CGI_DEALER_INCHARGE','gft_cp_info','CGI_LEAD_CODE', $cp_lead_code);
	}else{
	    $business_manager = get_single_value_from_single_table('cgi_incharge_emp_id','gft_cp_info','CGI_LEAD_CODE', $cp_lead_code);
	}
	if( ($ord_type=='first') && ($is_old_incentive || ($lic_cost > 0)) ){
		$incentive_type = 1;
	    $dtl_arr = get_lead_creation_and_prospecting_owner($customerId);
	    $created_by_emp = isset($dtl_arr[0])?$dtl_arr[0]:0;
	    $prospect_by_emp= isset($dtl_arr[1])?$dtl_arr[1]:0;
	    
	    if(!$only_partner){
	        if($created_by_emp==0){
	            $created_by_emp = $order_by;
	        }
	        if($prospect_by_emp==0){
	            $prospect_by_emp = $order_by;
	        }
	        insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 1, $created_by_emp,'',$only_partner);
	        insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 2, $prospect_by_emp,'',$only_partner);
	        $credit_demo_to = (int)$demo_by_emp;
	        if($credit_demo_to==0){
	            $demo_emp_list = get_eligible_demo_incentive_employees($customerId);
	            $demo_emps = /*. (string[int]) .*/array();
	            if(count($demo_emp_list)==0){
	                $credit_demo_to = $order_by;
	            }else{
	                for ($mn=0;$mn<count($demo_emp_list);$mn++){
	                    if($demo_emp_list[$mn][2]=='A'){
	                        $demo_emps[] = $demo_emp_list[$mn][0];
	                    }
	                }
	                if(count($demo_emps)==1){
	                    $credit_demo_to = $demo_emps[0];
	                }
	            }
	        }
	        if( ($helped_by_dtl=='store') && ($lead_type=='3') ){
	            $credit_demo_to=0; // will be done in incentive mapping in store order
	        }
	        if($credit_demo_to!=0){
	            insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 3, $credit_demo_to,'',$only_partner);
	        }else if($helped_by_dtl=='store'){
	            send_formatted_notification_content($nc_config, 0, 101, 1, $order_by);
	        }
	        insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 4, $order_by,'',$only_partner);
	    }else{//partner order
	        if($created_by_emp!=0 && $created_by_emp<7000){
	            insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 1, $created_by_emp,'',$only_partner);
	            $business_manager_collection = $business_manager_collection-$attr_perc_arr[1];
	        }
	        if($prospect_by_emp!=0 && $prospect_by_emp<7000){
	            insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 2, $prospect_by_emp,'',$only_partner);
	            $business_manager_collection = $business_manager_collection-$attr_perc_arr[2];
	        }
	        if($demo_by_emp!=0 && $demo_by_emp<7000){
	            insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 3, $demo_by_emp,'',$only_partner);
	            $business_manager_collection = $business_manager_collection-$attr_perc_arr[3];
	        }
	        $attr_perc_arr[18] = $business_manager_collection;
	        insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 18, $business_manager,'',$only_partner);
	    }
	}else if( ($ord_type=='upsell') && ($is_old_incentive || ($lic_cost > 0)) ){
	    $proceed_upsell = true;
		if($helped_by_dtl=='store'){
			$emp_list_arr = get_eligible_agile_incentive_employees($customerId,array($order_by),false);
			if(count($emp_list_arr) >0){
				send_formatted_notification_content($nc_config, 0, 101, 1, $order_by);
				$proceed_upsell = false; //upsell details will be updated during incentive mapping from GoFrugal Store Order
			}
		}
		if($proceed_upsell){
    		$help_activity_id = '';
    		$incentive_type = 2;
    		if(!$only_partner){
    		    insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 5, $order_by,'');
    		    $helped_info_arr = explode("-", $helped_by_dtl);
    		    if(count($helped_info_arr) > 1){
    		        $help_by_emp 		= $helped_info_arr[0];
    		        $help_activity_id 	= $helped_info_arr[1];
    		    }else{
    		        $help_by_emp = $order_by;
    		    }
    		    insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 6, $help_by_emp,$help_activity_id);
    		    if(!$is_old_incentive){
    		        $oppr_act_id = "";
    		        $oppr_by_arr = explode("-", $oppr_by_dtl);
    		        if(count($oppr_by_arr) > 1){
    		            $oppr_by_emp  = $oppr_by_arr[0];
    		            $oppr_act_id  = $oppr_by_arr[1];
    		        }else{
    		            $oppr_by_emp  = $order_by;
    		        }
    		        insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 17, $oppr_by_emp,$oppr_act_id);
    		    }
    		}else{//partner order
    		    $helped_info_arr = explode("-", $helped_by_dtl);
    		    if(count($helped_info_arr) > 1){
    		        $help_by_emp 		= $helped_info_arr[0];
    		        $help_activity_id 	= $helped_info_arr[1];
    		        insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 6, $help_by_emp,$help_activity_id);
    		        $business_manager_collection = $business_manager_collection-$attr_perc_arr[6];
    		    }
    		    if(!$is_old_incentive && $oppr_by_dtl!=''){
    		        $oppr_act_id = "";
    		        $oppr_by_arr = explode("-", $oppr_by_dtl);
    		        if(count($oppr_by_arr) > 1){
    		            $oppr_by_emp  = $oppr_by_arr[0];
    		            $oppr_act_id  = $oppr_by_arr[1];
    		            insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 17, $oppr_by_emp,$oppr_act_id);
    		            $business_manager_collection = $business_manager_collection-$attr_perc_arr[17];
    		        }
    		    }
    		    $attr_perc_arr[18] = $business_manager_collection;
    		    insert_orderwise_incentive_owner($orderNo, $incentive_type, $attr_perc_arr, 18, $business_manager,'',$only_partner);
    		}
		}
		
	}
	
	if($only_partner){
	    if($ser_with_del > 0){
	        $attr_perc_arr[19] = 40;
	        insert_orderwise_incentive_owner($orderNo, 1, $attr_perc_arr, 19, $business_manager,'');
	    }
	    if($ser_without_del>0){
	        $attr_perc_arr[20] = 100;
	        insert_orderwise_incentive_owner($orderNo, 1, $attr_perc_arr, 20, $business_manager,'');
	    }
	}
	if(!$only_partner && $ser_with_del > 0){
	    insert_orderwise_incentive_owner($orderNo, 1, $attr_perc_arr, 14, $order_by,'');
	}
	if(!$only_partner && $ser_without_del > 0){
	    insert_orderwise_incentive_owner($orderNo, 1, $attr_perc_arr, 16, $order_by,'');
	}
}

/**
 * @param string $gop_order_no
 * 
 * @return string[string]
 */
function get_gop_dtl($gop_order_no){
	$sub1 = " select GAD_ASS_ORDER_NO,GAD_PRODUCT_CODE,GAD_PRODUCT_SKEW from gft_ass_dtl ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GAD_PRODUCT_CODE and GPM_PRODUCT_SKEW=GAD_PRODUCT_SKEW) ".
			" where GAD_ASS_ORDER_NO='$gop_order_no' and GFT_SKEW_PROPERTY=11 ".
			" group by GAD_ASS_ORDER_NO,GAD_PRODUCT_SKEW,GAD_PRODUCT_CODE ";
	$sql1 = " select GLE_DELIVERY_TYPE, GLH_LEAD_CODE,GLH_CUST_NAME,GOD_ORDER_AMT,GOD_ORDER_DATE,round(sum(if(GFT_SKEW_PROPERTY in (4,5) and GOP_PRODUCT_CODE not in (703),0,GOP_SELL_RATE*GOP_QTY*if(GOP_COUPON_HOUR>0,GOP_COUPON_HOUR,1))),2) sell_rate, ".
			" round(sum(if(GFT_SKEW_PROPERTY in (4,5) and GOP_PRODUCT_CODE not in (703),GOP_SELL_AMT,0)),2) alr_sell_amt, ".
			" round(sum(if(GFT_SKEW_PROPERTY in (4,5) and GOP_PRODUCT_CODE not in (703),GOP_SELL_RATE*GOP_QTY,0)),2) alr_sell_rate, ".
			" sum(if(GFT_SKEW_PROPERTY=15,1,0)) as premium_alr, ".
			" round(sum(if(GPM_IS_BASE_PRODUCT='Y' and GFT_SKEW_PROPERTY in (18,20,21),GOP_SELL_AMT,0)),2) saas_sell_amt, ".
			" round(sum(if(GPM_IS_BASE_PRODUCT='Y' and GFT_SKEW_PROPERTY in (18,20,21),GOP_SELL_RATE*GOP_QTY,0)),2) saas_sell_rate, ".
			" round(sum(if(GLH_MAIN_PRODUCT=6 and GFT_SKEW_PROPERTY in (1,11,25),GOP_SELL_AMT,0)),2) prod_enh_sell_amt, ".
			" round(sum(if(GLH_MAIN_PRODUCT=6 and GFT_SKEW_PROPERTY in (1,11,25),GOP_SELL_RATE*GOP_QTY,0)),2) prod_enh_sell_rate, ".
			" round(sum(if(st.GAD_ASS_ORDER_NO is not null,GOP_SELL_AMT,0)),2) subscr_sell_amt, ".
			" round(sum(if(st.GAD_ASS_ORDER_NO is not null,GOP_SELL_RATE*GOP_QTY,0)),2) subscr_sell_rate, ".
			" round(sum(if(GFT_SKEW_PROPERTY=2 and GFT_LOWER_PCODE=200 and GFT_LOWER_SKEW not like '06.0%',GOP_SELL_AMT,0)),2) deup_sell_amt, ".
			" round(sum(if(GFT_SKEW_PROPERTY=2 and GFT_LOWER_PCODE=200 and GFT_LOWER_SKEW not like '06.0%',GOP_SELL_RATE*GOP_QTY,0)),2) deup_sell_rate ".
			" from gft_order_product_dtl ".
			" join gft_order_hdr on (GOD_ORDER_NO=GOP_ORDER_NO) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ".
			" join gft_lead_hdr_ext on (GLH_LEAD_CODE=GLE_LEAD_CODE) ".
			" join gft_product_master pm on (GOP_PRODUCT_CODE=pm.GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
			" left join ($sub1) st on (st.GAD_ASS_ORDER_NO=GOP_ORDER_NO and st.GAD_PRODUCT_CODE=GOP_PRODUCT_CODE and st.GAD_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			" where GOP_ORDER_NO='$gop_order_no' group by GOP_ORDER_NO ";
	$res1 = execute_my_query($sql1);
	$gop_sell_rate = 0;
	$ret_arr = /*. (string[string]) .*/array();
	if($row1 = mysqli_fetch_array($res1)){
	    $std_alr_rate = $std_alr_amt = 0;
	    if((int)$row1['premium_alr'] > 0){
	        $aq1 = " select GOP_QTY,sa.GPM_LIST_PRICE,sa.GPM_NET_RATE from gft_order_product_dtl ".
	               " join gft_product_master pa on (pa.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and pa.GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
	               " join gft_product_master sa on (sa.GPM_PRODUCT_CODE=pa.GPM_PRODUCT_CODE and sa.GPM_REFERER_SKEW=pa.GPM_REFERER_SKEW and sa.GFT_SKEW_PROPERTY=4) ".
	               " where GOP_ORDER_NO='$gop_order_no' and pa.GFT_SKEW_PROPERTY=15 ";
	        $ar1 = execute_my_query($aq1);
	        while ($ad1 = mysqli_fetch_array($ar1)){
	            $std_alr_rate += round($ad1['GOP_QTY'] * $ad1['GPM_LIST_PRICE'],2);
	            $std_alr_amt  += round($ad1['GOP_QTY'] * $ad1['GPM_NET_RATE'],2);
	        }
	    }
	    $ret_arr['sell_rate'] = $row1['sell_rate'] - $std_alr_rate;
	    $ret_arr['alr_amt']	  = $row1['alr_sell_amt'] + $std_alr_amt;
	    $ret_arr['alr_rate']  = $row1['alr_sell_rate'] + $std_alr_rate;
		$ret_arr['lead_code'] = $row1['GLH_LEAD_CODE'];
		$ret_arr['lead_name'] = $row1['GLH_CUST_NAME'];
		$ret_arr['order_amt'] = $row1['GOD_ORDER_AMT'];
		
		$ret_arr['saas_rate'] = $row1['saas_sell_rate'];
		$ret_arr['pe_rate']   = $row1['prod_enh_sell_rate'];
		$ret_arr['sr_rate']   = $row1['subscr_sell_rate'];
		$ret_arr['deup_rate'] = $row1['deup_sell_rate'];
		$ret_arr['saas_amt']  = $row1['saas_sell_amt'];
		$ret_arr['pe_amt']    = $row1['prod_enh_sell_amt'];
		$ret_arr['sr_amt']    = $row1['subscr_sell_amt'];
		$ret_arr['deup_amt']  = $row1['deup_sell_amt'];
		$ret_arr['order_date']  = $row1['GOD_ORDER_DATE'];
		$ret_arr['delivery_type']  = $row1['GLE_DELIVERY_TYPE'];
	}
	return $ret_arr;
}

/**
 * @param string $receipt_id
 * 
 * @return void
 */
function update_incentive_earnings($receipt_id){
    $inc_st_date = db_date_format(trim(get_samee_const("New_Incentive_Order_Date")));
	$sql1 = " select GOI_ID,GOI_ORDER_NO,GOI_ATTRIBUTE_PERCENT,GCR_AMOUNT,GOD_ORDER_AMT,GOI_OWNER_EMP,GOD_COMMISSION_PER, ".
			" GEM_EMP_NAME,GIA_ATTRIBUTE_NAME,GLH_LEAD_CODE,GLH_CUST_NAME,GOI_ATTRIBUTE_ID,GIA_COST_ON,GOD_ORDER_DATE, ".
			" GOD_LICENSE_COST,GOD_SERVICE_WITH_DELIVERY_COST,GOD_SERVICE_WITHOUT_DELIVERY_COST from gft_receipt_dtl ".
			" join gft_collection_receipt_dtl on (GCR_RECEIPT_ID=GRD_RECEIPT_ID) ".
			" join gft_orderwise_incentive_owner on (GOI_ORDER_NO=GCR_ORDER_NO) ".
			" join gft_order_hdr on (GOD_ORDER_NO=GOI_ORDER_NO) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ".
			" join gft_emp_master on (GEM_EMP_ID=GOI_OWNER_EMP) ".
			" join gft_incentive_attribute_master on (GIA_ID=GOI_ATTRIBUTE_ID) ".
			" where GRD_RECEIPT_ID='$receipt_id' and GRD_CHECKED_WITH_LEDGER='Y' and GOI_INCENTIVE_TYPE in (1,2,3) ".
			" and GOD_ORDER_STATUS='A' and if(GOI_ATTRIBUTE_ID=12,GRD_RECEIPT_ID=GOI_ACTIVITY_ID,1) ";
	$res1 = execute_my_query($sql1);
	$date_time = date('Y-m-d H:i:s');
	$notif_arr = /*. (string[string][string]) .*/array();
	while ($row1 = mysqli_fetch_array($res1)){
		$own_ref_id = $row1['GOI_ID'];
		$own_emp_id	= $row1['GOI_OWNER_EMP'];
		$orderNo	= $row1['GOI_ORDER_NO'];
		$attr_id	= (int)$row1['GOI_ATTRIBUTE_ID'];
		$attr_perc 	= (float)$row1['GOI_ATTRIBUTE_PERCENT'];
		$coll_amt	= (float)$row1['GCR_AMOUNT'];
		$orderAmt 	= (float)$row1['GOD_ORDER_AMT'];
		$ref_comm   = (int)$row1['GOD_COMMISSION_PER'];
		$cost_on    = (int)$row1['GIA_COST_ON'];
		$new_inc    = (strtotime($row1['GOD_ORDER_DATE']) >= strtotime($inc_st_date)) ? true: false;
		if(in_array($attr_id, array(7,8))){
		    $new_inc = false;
		}
		$gop_dtl 	= get_gop_dtl($orderNo);
		$alr_amt 	= isset($gop_dtl['alr_amt'])?(float)$gop_dtl['alr_amt']:0;
		$eligible_sell_rate = isset($gop_dtl['sell_rate'])?(float)$gop_dtl['sell_rate']:0;
		
		$eligible_order_amt = $orderAmt - $alr_amt;
		$t1_rate    = 0;
		if( in_array($attr_id, array(4,5,7)) && is_authorized_group($own_emp_id,null,59)){
			$saas_amt	= isset($gop_dtl['saas_amt'])?(float)$gop_dtl['saas_amt']:0;
			$pe_amt		= isset($gop_dtl['pe_amt'])?(float)$gop_dtl['pe_amt']:0;
			$sr_amt		= isset($gop_dtl['sr_amt'])?(float)$gop_dtl['sr_amt']:0;
			$deup_amt	= isset($gop_dtl['deup_amt'])?(float)$gop_dtl['deup_amt']:0;
			$saas_rate	= isset($gop_dtl['saas_rate'])?(float)$gop_dtl['saas_rate']:0;
			$pe_rate	= isset($gop_dtl['pe_rate'])?(float)$gop_dtl['pe_rate']:0;
			$sr_rate	= isset($gop_dtl['sr_rate'])?(float)$gop_dtl['sr_rate']:0;
			$deup_rate	= isset($gop_dtl['deup_rate'])?(float)$gop_dtl['deup_rate']:0;
			$t1_amt	 = $saas_amt + $pe_amt + $sr_amt + $deup_amt;
			$t1_rate = $saas_rate + $pe_rate + $sr_rate + $deup_rate;
			$eligible_order_amt -= $t1_amt;
			$eligible_sell_rate -= $t1_rate;
		}
		if($eligible_order_amt <= 0){
			continue;
		}
		$common_tax_perc 	= $eligible_sell_rate/$eligible_order_amt;
		
		$coll_share = $coll_amt/$orderAmt;
		
		$eligible_coll_amt = round($eligible_order_amt * $coll_share,2);
		$tax_deducted_coll_amt 	= round($eligible_coll_amt * $common_tax_perc,2);
		$ref_com_factor = ( ($attr_id <= 8) && ($ref_comm > 0) )? 0.9 : 1; //referral commission order. 10% will be given to referral partner, so only 90% is added in incentive bucket
		$inc_amt = round(($tax_deducted_coll_amt*$attr_perc*$ref_com_factor)/100,2);
		
		if($new_inc){
		    $amt_to_consider = 0;
		    if($cost_on==1){
		        $amt_to_consider = (float)$row1['GOD_LICENSE_COST'] - $t1_rate;
		    }else if($cost_on==2){
		        $amt_to_consider = $row1['GOD_SERVICE_WITH_DELIVERY_COST'] + get_pcs_entry_skew_sell_rate($orderNo);
		    }else if($cost_on==3){
		        $amt_to_consider = $row1['GOD_SERVICE_WITHOUT_DELIVERY_COST'];
		    }
		    $inc_amt = round(($amt_to_consider*$ref_com_factor*$coll_share*$attr_perc)/100,2);
		}
		
		$sql2 = " select GIE_ID,GIE_INCENTIVE_AMT from gft_incentive_earning where GIE_RECEIPT_ID='$receipt_id' and GIE_OWNER_REF_ID='$own_ref_id' ";
		$res2 = execute_my_query($sql2);
		if($row2 = mysqli_fetch_array($res2)){
			$primary_id = $row2['GIE_ID'];
			$exist_incen_amt = (float)$row2['GIE_INCENTIVE_AMT'];
			if($inc_amt!=$exist_incen_amt){
				execute_my_query("update gft_incentive_earning set GIE_INCENTIVE_AMT='$inc_amt',GIE_UPDATED_DATE='$date_time' where GIE_ID='$primary_id' ");
			}
		}else if($inc_amt > 0){
			$ins_arr = array(
					'GIE_RECEIPT_ID'=>$receipt_id,'GIE_OWNER_REF_ID'=>$own_ref_id,
					'GIE_INCENTIVE_AMT'=>$inc_amt,'GIE_CREATED_DATE'=>$date_time,'GIE_UPDATED_DATE'=>$date_time
				   );
			array_insert_query("gft_incentive_earning", $ins_arr);
			$notif_arr[$own_emp_id]['name'] 		= $row1['GEM_EMP_NAME'];
			$notif_arr[$own_emp_id]['cust_id'] 	= $row1['GLH_LEAD_CODE'];
			$notif_arr[$own_emp_id]['cust_name']	= $row1['GLH_CUST_NAME'];
			$notif_arr[$own_emp_id]['order_no']	= $row1['GOI_ORDER_NO'];
			$notif_arr[$own_emp_id]['order_amt']	= $row1['GOD_ORDER_AMT'];
			$notif_arr[$own_emp_id]['inc_amt'][] 	= $inc_amt;
			$notif_arr[$own_emp_id]['metrics'][]	= $row1['GIA_ATTRIBUTE_NAME'];
		}
	}
	foreach ($notif_arr as $ke => $varr){
		$dtl_txt =  " Customer name - ".$varr['cust_name']."<br>".
					" Customer id   - ".$varr['cust_id']."<br>".
					" Order number  - ".$varr['order_no']."<br>".
					" Order amount  - ".$varr['order_amt']."<br>".
					" Agile metrics - ".implode(",", $varr['metrics'])."<br>";
		$nf_config = array(
				'Employee_Name'=>array($varr['name']),
				'AMOUNT'=>array(array_sum($varr['inc_amt'])),
				'Summary1'=>array($dtl_txt)
		);
		send_formatted_notification_content($nf_config, 0, 98, 1, $ke);
	}
}

/**
 * @param string $orderNo
 *
 * @return boolean
 */
function get_valid_receipts_from_partner_agile($orderNo){
    $output = false;
    $order_status = get_single_value_from_single_table("GOD_ORDER_APPROVAL_STATUS","gft_order_hdr","GOD_ORDER_NO", $orderNo);
    if($order_status==1){
        $output =  true;
    }else{
        $partner_effective_date = get_samee_const("PARTNER_AGILE_EFFECTIVE_DATE");
        $sql1 = " select GCR_RECEIPT_ID from gft_collection_receipt_dtl ".
            " join gft_receipt_dtl on (GCR_RECEIPT_ID=GRD_RECEIPT_ID) ".
            " where GCR_ORDER_NO='$orderNo' and GRD_CHECKED_WITH_LEDGER='Y' and GRD_CHEQUE_CLEARED_DATE>='$partner_effective_date' ";
        $res1 = execute_my_query($sql1);
        if(mysqli_num_rows($res1)>0){
            $output =  true;
        }
    }
    return $output;
}

/**
 * @param string $orderNo
 *
 * @return boolean
 */
function is_partner_order($orderNo){
    $output = false;
    $res = execute_my_query("select GOD_EMP_ID from gft_order_hdr where god_order_no='$orderNo' and GOD_EMP_ID>=7000 and GOD_EMP_ID<9998 ") ;
    if(mysqli_num_rows($res)>0){
        $output = true;
    }
    return $output;
}

/**
 * @param string $orderNo
 * 
 * @return void
 */
function update_incentive_earnings_for_order($orderNo){
    $partner_order = is_partner_order($orderNo);
    $partner_effective_condition = "";
    if($partner_order){
        $partner_effective_date = get_samee_const("PARTNER_AGILE_EFFECTIVE_DATE");
        if($partner_effective_date!=''){//partner collection displayed before partner_effective_date
            $partner_effective_condition = " and GRD_CHEQUE_CLEARED_DATE >= '$partner_effective_date' ";
        }
    }
    $sql1 = " select GCR_RECEIPT_ID from gft_collection_receipt_dtl ".
			" join gft_receipt_dtl on (GCR_RECEIPT_ID=GRD_RECEIPT_ID) ".
			" where GCR_ORDER_NO='$orderNo' and GRD_CHECKED_WITH_LEDGER='Y' $partner_effective_condition ";
	$res1 = execute_my_query($sql1);
	while ($row1 = mysqli_fetch_array($res1)){
		update_incentive_earnings($row1['GCR_RECEIPT_ID']);
	}
}

/**
 * @param string $customerId
 * @param string $referalPartnerLead
 *
 * @return string[string]
 */
function get_referral_commission_for_lead($customerId,$referalPartnerLead){
	$sql1 = " select sum(ifnull(GCD_PAYABLE_AMT,0)) as payable, sum(ifnull(GCD_TDS_DEDUCTED_AMT,0)) as tds_amt ".
			" from gft_reff_commission_dtl join gft_order_hdr on (GOD_ORDER_NO=GCD_ORDER_NO) where GOD_LEAD_CODE='$customerId' ";
	$res1 = execute_my_query($sql1);
	$ret_arr = /*. (string[string]) .*/array();
	while($row1 = mysqli_fetch_array($res1)){
		$ret_arr['paid_amt']= $row1['payable'];
		$ret_arr['tds_amt'] = $row1['tds_amt'];
	}
	$sql2 =" select if(GOD_COMMISSION_STATUS=1,'Processed',if(GOD_BALANCE_AMT > 0,'Pending Sales',if(GPP_LEAD_CODE is null,'Pending Partner','Pending Accounts'))) as com_stat ".
	       " from gft_order_hdr left join gft_partner_profile_dtl on (GPP_QUESTION_ID=419 and GPP_LEAD_CODE=GOD_ORDER_REFFERER) ".
	       " where GOD_LEAD_CODE='$customerId' and GOD_ORDER_REFFERER='$referalPartnerLead' order by GOD_ORDER_DATE ";
	$res2 = execute_my_query($sql2);
	if($row2 = mysqli_fetch_array($res2)){
	    $ret_arr['com_status']  = $row2['com_stat'];
	}
	return $ret_arr;
}

/**
 * @param int $emply_id
 * 
 * @return void
 */
function update_test_otp($emply_id){
	$test_mode = (int)get_samee_const("STORE_PAYMENT_TEST_MODE");
	if( ($emply_id==7044) || ($emply_id==9149) || ($test_mode==1) ){
		$otp	=	'12345';
		execute_my_query("update  gft_emp_auth_key set GEK_OTP='12345', GEK_OTP_STATUS=1 where EMP_ID='$emply_id'");
	}
}

/**
 * @param string $emply_id
 * @param string $otp_val
 * 
 * @return boolean
 */
function validate_login_otp($emply_id,$otp_val){
	$otp_val = mysqli_real_escape_string_wrapper($otp_val);
	$s1 = "select EMP_ID from gft_emp_auth_key where EMP_ID='$emply_id' and GEK_OTP='$otp_val' and GEK_OTP_STATUS=1 ";
	$r1 = execute_my_query($s1);
	if(mysqli_num_rows($r1) > 0){
		execute_my_query(" update gft_emp_auth_key set GEK_OTP_STATUS=2 where EMP_ID='$emply_id' and GEK_OTP='$otp_val' ");
		return true;
	}
	return false;
}

/**
 * @param string $emply_id
 *
 * @return string[int]
 */
function get_menu_analytics($emply_id){
	$sq1 =  " select GMA_MENU_KEY,GMA_CLICKS from gft_menu_analytics where GMA_EMP_ID='$emply_id' ";
	$rs1 = execute_my_query($sq1);
	$ret_arr = /*. (string[int]) .*/array();
	$all_clicks = 1;
	while($row1 = mysqli_fetch_array($rs1)){
		$gma_clicks = (int)$row1['GMA_CLICKS'];
		$ret_arr[$row1['GMA_MENU_KEY']] = $gma_clicks;
		$all_clicks += $gma_clicks;
	}
	$gem_role_id = (int)get_single_value_from_single_table("GEM_ROLE_ID", "gft_emp_master", "GEM_EMP_ID", $emply_id);
	$top_menu_id = ($gem_role_id==73)?'new_lead':'reminders';
	$ret_arr[$top_menu_id] = $all_clicks; //just to show reminders at top since it is default menu
	return $ret_arr;
}

/**
 * @param string $emply_id
 * @param int $vertical_code
 * 
 * @return boolean
 */
function is_chain_certified_employee($emply_id,$vertical_code=0){
	$sql1 = " select GEC_EMP_ID,GEC_VERTICAL_CODE from gft_emp_certificate_dtl where GEC_EMP_ID='$emply_id' ".
			" and GEC_CERTIFICATE_ID=1 and GEC_STATUS=1 ";
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1) > 0){
	    if($row = mysqli_fetch_assoc($res1)){
	        $code_list = $row['GEC_VERTICAL_CODE'];
	    }
	    if($code_list!=0 && $code_list != null){
	        $code_li_arr = explode(',', $code_list);
	        foreach ($code_li_arr as $i => $val){
	            if($vertical_code==$val){
	                return true;
	            }
	        }
	    }
	}
	return false;
}

/**
 * @param string $order_id
 * 
 * @return void
 */
function update_incentive_for_complementary_coupon($order_id){
	$sql1 = " select gcr_emp_id,gcr_coupon_hours,gcr_activity_date,GLH_LEAD_CODE,GLH_CUST_NAME,gem_emp_name from gft_complementary_coupon_request ".
			" join gft_complementary_coupon_order_dtl on (gccd_request_id=gcr_id) ".
			" join gft_emp_master on (GEM_EMP_ID=gcr_emp_id) ".
			" left join gft_lead_hdr on (GLH_LEAD_CODE=gcr_lead_code) ".
			" left join gft_orderwise_incentive_owner on (GOI_ORDER_NO=gcr_id) ".
			" where gccd_id='$order_id' and gcr_request_status='I' and GOI_ORDER_NO is null ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$gcr_emp_id = $row1['gcr_emp_id'];
		$coupon_hrs = (float)$row1['gcr_coupon_hours'];
		$current_time = $row1['gcr_activity_date'];
		$glh_lead_code = $row1['GLH_LEAD_CODE'];
		$glh_cust_name = $row1['GLH_CUST_NAME'];
		$per_hour	= 1000;
		$earn_amt 	= $coupon_hrs * $per_hour;
		$attr_id 	= '9';
		$metric_name = get_single_value_from_single_table("GIA_ATTRIBUTE_NAME", "gft_incentive_attribute_master", "GIA_ID", $attr_id);
		$ins_arr = array(
				'GOI_ORDER_NO'=>$order_id,
				'GOI_INCENTIVE_TYPE'=>'4',
				'GOI_ATTRIBUTE_ID'=>$attr_id,
				'GOI_ATTRIBUTE_PERCENT'=>'0',
				'GOI_OWNER_EMP'=>$gcr_emp_id,
				'GOI_CREATED_DATE'=>$current_time
		);
		$ref_id = (int)array_insert_query("gft_orderwise_incentive_owner", $ins_arr);
		if($ref_id!=0){
			$arr = array(
					'GIE_OWNER_REF_ID'=>$ref_id,
					'GIE_INCENTIVE_AMT'=>$earn_amt,
					'GIE_CREATED_DATE'=>$current_time,
					'GIE_UPDATED_DATE'=>$current_time
				  );
			array_insert_query("gft_incentive_earning", $arr);
			$dtl_txt = "";
			if($glh_lead_code!=''){
				$dtl_txt .=	" Customer name - $glh_lead_code <br>".
							" Customer id   - $glh_cust_name <br>";
			}
			$dtl_txt .= " Agile metrics	- $metric_name <br>".
						" Coupon Hours	- $coupon_hrs <br>";
			$nf_config = array(
					'Employee_Name'=>array($row1['gem_emp_name']),
					'AMOUNT'=>array($earn_amt),
					'Summary1'=>array($dtl_txt)
			);
			send_formatted_notification_content($nf_config, 0, 98, 1, $gcr_emp_id);
		}
	}
}
/**
 * @param string $order_no
 *
 * @return string[string]
 */
function get_order_incentive_amounts($order_no){
    $return_arr = array();
    $result = execute_my_query("select GOD_LICENSE_COST, GOD_SERVICE_WITH_DELIVERY_COST, GOD_SERVICE_WITHOUT_DELIVERY_COST".
        " from gft_order_hdr where GOD_ORDER_NO='$order_no'");
    if($row = mysqli_fetch_assoc($result)){
        $return_arr['license_cost'] = $row['GOD_LICENSE_COST'];
        $return_arr['service_delivery_cost'] = $row['GOD_SERVICE_WITH_DELIVERY_COST'];
        $return_arr['service_without_delivery_cost'] = $row['GOD_SERVICE_WITHOUT_DELIVERY_COST'];
    }
    return $return_arr;
}
/**
 * @param string $training_id
 * @param string $no_of_hrs
 * @param string $per_hr_cost
 * @param string $orderNo
 * @param string $attr_id
 * @param string $glh_lead_code
 * @param string $glh_cust_name
 * @param string $god_order_amt
 * 
 * @return void
 */
function to_update_incentive_for_pd($training_id, $no_of_hrs,$per_hr_cost,$orderNo,$attr_id,$glh_lead_code,$glh_cust_name,$god_order_amt){
    $sql2 = " select GPT_CUST_RATING,GPT_ACTIVITY_TIME_SPENT,GPT_ACTIVITY_BY,GEM_EMP_NAME ".
        " from gft_pd_training_feedback_dtl ".
        " join gft_emp_master on (GEM_EMP_ID=GPT_ACTIVITY_BY) ".
        " where GPT_TRAINING_ID='$training_id' and GPT_IS_SPOC=1 order by GPT_ACTIVITY_ON ";
    $total_val = $total_time = $activity_by = 0;
    $inc_emp_name = "";
    $res2 = execute_my_query($sql2);
    while ($row2 = mysqli_fetch_array($res2)){
        $activity_by 	= (int)$row2['GPT_ACTIVITY_BY'];
        $inc_emp_name	= $row2['GEM_EMP_NAME'];
        $rating_val 	= (int)$row2['GPT_CUST_RATING'];
        $time_spent		= (int)$row2['GPT_ACTIVITY_TIME_SPENT'];
        $total_val		+= ($rating_val*$time_spent);
        $total_time 	+= $time_spent;
    }
    if( ($total_val==0) || ($total_time==0) ){
        return;
    }
    $avg_rating = (int)($total_val / $total_time);
    if($avg_rating < 4){
        return ;
    }
    $current_time = date('Y-m-d H:i:s');
    if( ($activity_by > 0) && ($no_of_hrs > 0) ){
        if(isPartnerEmployee($activity_by)){
            return ;
        }
        $chk = execute_my_query("select GOI_ID from gft_orderwise_incentive_owner where GOI_ORDER_NO='$orderNo'". 
                                " and GOI_ACTIVITY_ID='$training_id' AND GOI_ATTRIBUTE_ID='$attr_id'");
        if(mysqli_num_rows($chk) > 0){
            return ; //already processed
        }
        $perc_arr = get_attribute_percent_in_array($orderNo);
        $metric_name = get_single_value_from_single_table("GIA_ATTRIBUTE_NAME", "gft_incentive_attribute_master", "GIA_ID", $attr_id);
        $earn_amt = round($per_hr_cost * $no_of_hrs * ($perc_arr[$attr_id]/100),2);
        $ins_arr = array(
            'GOI_ORDER_NO'=>$orderNo,
            'GOI_ACTIVITY_ID'=>$training_id,
            'GOI_INCENTIVE_TYPE'=>'4',
            'GOI_ATTRIBUTE_ID'=>$attr_id,
            'GOI_ATTRIBUTE_PERCENT'=>$perc_arr[$attr_id],
            'GOI_OWNER_EMP'=>$activity_by,
            'GOI_CREATED_DATE'=>$current_time
        );
        $ref_id = (int)array_insert_query("gft_orderwise_incentive_owner", $ins_arr);
        if($ref_id!=0){
            $arr = array(
                'GIE_OWNER_REF_ID'=>$ref_id,
                'GIE_INCENTIVE_AMT'=>$earn_amt,
                'GIE_CREATED_DATE'=>$current_time,
                'GIE_UPDATED_DATE'=>$current_time
            );
            array_insert_query("gft_incentive_earning", $arr);
            
            $training_name = $training_id;
            $que1 = " select GIM_MS_NAME from gft_cust_imp_ms_current_status_dtl ".
                " join gft_impl_mailstone_master on (GIMC_MS_ID=GIM_MS_ID) where GIMC_COMPLAINT_ID='$training_id' ";
            $qr1 = execute_my_query($que1);
            if($data1 = mysqli_fetch_array($qr1)){
                $training_name = $data1['GIM_MS_NAME'];
            }
            $dtl_txt =  " Customer name - $glh_lead_code <br>".
                " Customer id   - $glh_cust_name <br>".
                " Order number  - $orderNo <br>".
                " Order amount  - $god_order_amt <br>".
                " Activity for	- $training_name <br>".
                " Agile metrics - $metric_name <br>";
            $nf_config = array(
                'Employee_Name'=>array($inc_emp_name),
                'AMOUNT'=>array($earn_amt),
                'Summary1'=>array($dtl_txt)
            );
            send_formatted_notification_content($nf_config, 0, 98, 1, $activity_by);
        }
    }
}
/**
 * @param string $training_id
 * @param string $orderNo
 * @param string $pd_id
 *
 * @return void
 */
function update_incentive_for_product_delivery($training_id,$orderNo,$pd_id){
    $gop_dtl_arr	= get_gop_dtl($orderNo);
    $elig_sell_rate	= isset($gop_dtl_arr['sell_rate'])?(float)$gop_dtl_arr['sell_rate']:0;
    $glh_lead_code  = isset($gop_dtl_arr['lead_code'])?$gop_dtl_arr['lead_code']:'';
    $glh_cust_name	= isset($gop_dtl_arr['lead_name'])?$gop_dtl_arr['lead_name']:'';
    $god_order_amt	= isset($gop_dtl_arr['order_amt'])?$gop_dtl_arr['order_amt']:'';
    $order_date     = isset($gop_dtl_arr['order_date'])?$gop_dtl_arr['order_date']:'';
    $new_inc_effec  = get_samee_const("New_Incentive_Order_Date");
    $new_incentive  = ((strtotime($order_date) >= strtotime($new_inc_effec))) ? true : false;
    $order_incen_amounts = get_order_incentive_amounts($orderNo);
    $license_cost = isset($order_incen_amounts['license_cost'])?$order_incen_amounts['license_cost']:0;
    $service_delivery_cost = isset($order_incen_amounts['service_delivery_cost'])?$order_incen_amounts['service_delivery_cost']:0;
    if($elig_sell_rate==0){
        return;
    }
    $per_hr_cost = $total_hrs = $no_of_hrs = $total_service_hrs = 0;
    $service_product_type = array(7,8,12,23);
    $attr_id = 10;//Billable coupon
    $is_service_coupon = false;
    if($pd_id > 0){
        $pd_dtl 	= get_product_delivery_training_dtl($pd_id);
        $ms_type 	= (int)get_product_delivery_ms_type($training_id);
        $total_mins = isset($pd_dtl['total_mins'])?(int)$pd_dtl['total_mins']:0;
        $service_mins = isset($pd_dtl['service_mins'])?(int)$pd_dtl['service_mins']:0;
        if($new_incentive){
            $total_mins = ($total_mins - $service_mins);
            $pd_dtl['golive'] = isset($pd_dtl['license_mins'])?(int)$pd_dtl['license_mins']:0;
            $elig_sell_rate = $license_cost;
        }        
        $total_hrs	= round($total_mins/60,1);
        $total_service_hrs = round($service_mins/60,1);
        $mstr = "";
        if($ms_type==35){
            $mstr = 'bq';
        }else if($ms_type==36){
            $mstr = 'mdm';
        }else if($ms_type==37){
            $mstr = 'uat';
        }else if($ms_type==3){
            $mstr = 'golive';
        }        
        $applicable_mins	= isset($pd_dtl[$mstr])?(int)$pd_dtl[$mstr]:0;
        $no_of_hrs = round($applicable_mins / 60 , 2);
        $per_hr_cost = round($elig_sell_rate/$total_hrs,2);
        to_update_incentive_for_pd($training_id, $no_of_hrs,$per_hr_cost,$orderNo,10,$glh_lead_code,$glh_cust_name,$god_order_amt);
        if($mstr == 'golive' && $total_service_hrs>0 && ($new_incentive)){
            $no_of_hrs = round($service_mins / 60 , 2);
            $per_hr_cost = round($service_delivery_cost/$total_service_hrs,2);
            to_update_incentive_for_pd($training_id, $no_of_hrs,$per_hr_cost,$orderNo,15,$glh_lead_code,$glh_cust_name,$god_order_amt);
        }
        return;
    }else{
        $sql1 = " select GCD_COUPON_HOURS,GCD_TRAINING_ID, GFT_SKEW_PROPERTY from gft_coupon_distribution_dtl ".
            " JOIN gft_product_master ON( SUBSTR(GCD_REF_ORDER_NO,16, LENGTH(SUBSTR(GCD_REF_ORDER_NO,16))-1)=concat(GPM_PRODUCT_CODE,GPM_PRODUCT_SKEW))".
            " where GCD_REF_ORDER_NO like '$orderNo%' ";
        $res1 = execute_my_query($sql1);
        while($row1 = mysqli_fetch_array($res1)){
            if(in_array($row1['GFT_SKEW_PROPERTY'], $service_product_type)){
                $total_service_hrs += (float)$row1['GCD_COUPON_HOURS'];
            }else{
                $total_hrs 	+= (float)$row1['GCD_COUPON_HOURS'];
            }
            if($row1['GCD_TRAINING_ID']==$training_id){
                $no_of_hrs = (float)$row1['GCD_COUPON_HOURS'];
                if(in_array($row1['GFT_SKEW_PROPERTY'], $service_product_type) && ($new_incentive)){
                    $is_service_coupon = true;
                    $attr_id = 15;//Service Delivery by
                }
            }
            
        }
    }
    if(!$new_incentive){//To support old incentive process
        $per_hr_cost = round($elig_sell_rate/($total_hrs+$total_service_hrs),2);
        to_update_incentive_for_pd($training_id, $no_of_hrs,$per_hr_cost,$orderNo,$attr_id,$glh_lead_code,$glh_cust_name,$god_order_amt);  
        return;
    }
    if($total_hrs > 0 && (!$is_service_coupon)){
        $per_hr_cost = round($license_cost/$total_hrs,2);
    }else if($total_service_hrs > 0 && ($is_service_coupon)){
        $per_hr_cost = round($service_delivery_cost/$total_service_hrs,2);
    }else{
        return;
    }    
    to_update_incentive_for_pd($training_id, $no_of_hrs,$per_hr_cost,$orderNo,$attr_id,$glh_lead_code,$glh_cust_name,$god_order_amt);    
}
/**
 * @param string $complaint_id
 * 
 * @return void
 */
function update_dev_table($complaint_id=''){
	$sql1 = " select GCH_COMPLAINT_ID,GCH_CURRENT_STATUS,min(GCD_REPORTED_DATE) rep_date,max(GCD_ACTIVITY_ID) max_act ".
			" from gft_customer_support_hdr ".
			" join gft_customer_support_dtl on (GCH_COMPLAINT_ID=GCD_COMPLAINT_ID and gcd_status='T2') ".
			" where 1 ";
	if($complaint_id!=''){
		$sql1 .= " and GCH_COMPLAINT_ID='$complaint_id' ";
	}
	$sql1 .= " group by GCH_COMPLAINT_ID ";
	$res1 = execute_my_query($sql1);
	while ($row1 = mysqli_fetch_array($res1)) {
		$comp_id 	= (int)$row1['GCH_COMPLAINT_ID'];
		$rep_date 	= $row1['rep_date'];
		$curr_stat 	= $row1['GCH_CURRENT_STATUS'];
		$max_act 	= (int)$row1['max_act'];
		$solved_date = null;
		if($curr_stat!='T2'){
			$sql2 = " select min(GCD_ACTIVITY_DATE) solved_date from gft_customer_support_dtl ".
					" where GCD_COMPLAINT_ID=$comp_id and GCD_ACTIVITY_ID > $max_act ";
			$res2 = execute_my_query($sql2);
			if($row2 = mysqli_fetch_array($res2)){
				$solved_date = $row2['solved_date'];
			}
		}
		$up1 = array('GDC_COMPLAINT_ID'=>$comp_id,'GDC_REPORTED_DATE'=>$rep_date,'GDC_SOLVED_DATE'=>$solved_date);
		$key1['GDC_COMPLAINT_ID'] = $comp_id;
		array_update_tables_common($up1, "gft_dev_complaints", $key1, null, SALES_DUMMY_ID,null,null,$up1);
		
	}
}
/**
 * @param string $order_no
 * @param string $order_incharge
 * @param string $demo_incentive_emp
 * @param string $agile_incentive_emp
 * @param string $is_other
 * @param string $corp_joint_perc
 * @param string $corp_joint_emp
 * @param string $upsell_oppr_emp
 *
 * @return void
 */
function update_incentive_for_store_order($order_no, $order_incharge, $demo_incentive_emp, $agile_incentive_emp, $is_other,$corp_joint_perc, $corp_joint_emp, $upsell_oppr_emp='' ){
    $attr_perc_arr = get_attribute_percent_in_array($order_no);
	if( ($demo_incentive_emp!='') && ($demo_incentive_emp!='0') ){
		insert_orderwise_incentive_owner($order_no, 1, $attr_perc_arr, 3, $demo_incentive_emp);
		update_incentive_earnings_for_order($order_no);
	}
	if( ($agile_incentive_emp!='') && ($agile_incentive_emp!='0') ){
	    $order_date    = get_single_value_from_single_table("GOD_ORDER_DATE", "gft_order_hdr", "GOD_ORDER_NO", $order_no);
	    $is_new_inc    = (strtotime($order_date)>=strtotime(trim(get_samee_const("New_Incentive_Order_Date"))));
		$helped_info_arr = explode("-", $agile_incentive_emp);
		$upsell_oppr_arr = explode("-", $upsell_oppr_emp);
		$oppr_by_emp = $help_by_emp = $agile_incentive_emp; //self case
		$oppr_act_id = $help_activity_id = "";
		if(count($helped_info_arr) > 1){
			$help_by_emp 		= $helped_info_arr[0];
			$help_activity_id 	= $helped_info_arr[1];
		}
		if(count($upsell_oppr_arr) > 1){
		    $oppr_by_emp 	= $upsell_oppr_arr[0];
		    $oppr_act_id 	= $upsell_oppr_arr[1];
		}
		insert_orderwise_incentive_owner($order_no, 2, $attr_perc_arr, 5, $order_incharge);
		insert_orderwise_incentive_owner($order_no, 2, $attr_perc_arr, 6, $help_by_emp,$help_activity_id);
		if($is_new_inc){
		    insert_orderwise_incentive_owner($order_no, 2, $attr_perc_arr, 17, $oppr_by_emp,$oppr_act_id);
		}
		update_incentive_earnings_for_order($order_no);
	}
	if( ($is_other=='no') || ($is_other=='yes') ){
		$attr_perc_arr[7] = 100 - $corp_joint_perc;
		$attr_perc_arr[8] = $corp_joint_perc;
		insert_orderwise_incentive_owner($order_no, 3, $attr_perc_arr, 7, $order_incharge);
		if($corp_joint_emp > 0){
			insert_orderwise_incentive_owner($order_no, 3, $attr_perc_arr, 8, $corp_joint_emp);
		}
		update_incentive_earnings_for_order($order_no);
	}
}

/**
 * @param string $channel
 * @param string $chat_message
 * 
 * @return string
 */
function do_curl_for_zoho_chat_push($channel,$chat_message){
	$arr['message'] = $chat_message;
	$post_data=json_encode($arr);
	$URL="https://chat.zoho.com/api/v1/channelsbyname/$channel/message?authtoken=f6d077a358714dde5328ce2510532d46&scope=InternalAPI";
	$ch = curl_init($URL);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
	$curl_res = curl_exec($ch);
	$curl_err = curl_error($ch);
	curl_close($ch);
	$ret_txt  = "Channel - $channel, Response - $curl_res";
	if($curl_err!=''){
		$ret_txt .= "Curl Error - $curl_err";
	}
	return $ret_txt;
}

/**
 * @param string $base_prod_id
 * @param string $addon_prod_id
 * @param string $message
 *
 * @return void
 */
function send_to_zoho_chat_channel($base_prod_id, $addon_prod_id, $message){
	$sql1 = " select GCC_CHAT_CHANNEL from gft_chat_channel_master ".
			" where GCC_BASE_PRODUCT_ID='$base_prod_id' and GCC_ADDON_PRODUCT_ID='$addon_prod_id' ";
	$res1 = execute_my_query($sql1);
	while ($row1 = mysqli_fetch_array($res1)) {
		$chat_channel = $row1['GCC_CHAT_CHANNEL'];
		do_curl_for_zoho_chat_push($chat_channel, $message);
	}
}

/**
 * @param string $number
 * 
 * @return string
 */
function get_contact_type_for_number($number){
	$contact_type = '2'; //Business phone
	$mob_len = strlen($number);
	if( (($mob_len==10) || ($mob_len==11)) && check_can_send_sms($number) ){
		$contact_type 	= '1'; // Mobile
	}
	return $contact_type;
}

/**
 * @param string $customerId
 * @param string $contactNo
 * @param string $contactName
 * @param string $contactDesignation
 * @param string $contactType
 * 
 * @return int
 */
function save_and_get_contact_id($customerId,$contactNo,$contactName,$contactDesignation,$contactType){
	$contact_cond = " and GCC_CONTACT_NO='$contactNo' ";
	if(is_numeric($contactNo)){
		$contact_cond = " and ".getContactDtlWhereCondition("GCC_CONTACT_NO", $contactNo);
	}
	$sql1 = " select GCC_ID,GCC_CONTACT_NAME from gft_customer_contact_dtl where GCC_LEAD_CODE='$customerId' $contact_cond ";
	$res1 = execute_my_query($sql1);
	$gcc_id = 0;
	if($row1 = mysqli_fetch_array($res1)){
		$gcc_id = (int)$row1['GCC_ID'];
		$gcc_contact_name = $row1['GCC_CONTACT_NAME'];
		if( ($contactName!='') && ($contactName!=$gcc_contact_name) ){
			$up1 = "update gft_customer_contact_dtl set GCC_CONTACT_NAME='".mysqli_real_escape_string_wrapper($contactName)."' where GCC_ID=$gcc_id ";
			execute_my_query($up1);
		}
	}else if($contactName!=''){
		$arr_gcc_id = insert_lead_contact_nos(array($contactName), array($contactNo), array($contactDesignation), $customerId, null, array($contactType),
				null,null,null,null,null,'off',false,null,null,null,null,null,null,null,null,'',false);
		$gcc_id = isset($arr_gcc_id[0])?$arr_gcc_id[0]:0;
	}
	return $gcc_id;
}

/**
 * @param string $gccId
 * @param string $installId
 * @param string $userName
 * @param string $password
 * @param string $mygofrugalRole
 * @param string $posRoleId
 * @param string $posRoleName
 * @param string $userStatus
 * @param string $systemAccess
 * @param string $posUserId
 * 
 * @return void
 */
function save_pos_users($gccId,$installId,$userName,$password,$mygofrugalRole,$posRoleId,$posRoleName,$userStatus,$systemAccess,$posUserId){
	$upd_arr = array(
			'GPU_CONTACT_ID'=>$gccId,
			'GPU_INSTALL_ID'=>$installId,
			'GPU_USER_NAME'=>$userName,
			'GPU_PASSWORD'=>$password,
			'GPU_MYGOFRUGAL_ROLE'=>$mygofrugalRole,
			'GPU_POS_ROLE_ID'=>$posRoleId,
			'GPU_POS_ROLE'=>$posRoleName,
			'GPU_SYSTEM_ACCESS'=>$systemAccess,
			'GPU_CONTACT_STATUS'=>$userStatus,
			'GPU_USER_ID'=>$posUserId, //just reference of last updated user id
			'GPU_CONTACT_TYPE'=>'1',
			'GPU_UPDATED_DATE'=>date('Y-m-d H:i:s')
	);
	$key_arr = array(
			'GPU_CONTACT_ID'=>$gccId,
			'GPU_INSTALL_ID'=>$installId
	);
	array_update_tables_common($upd_arr, "gft_pos_users", $key_arr, null, SALES_DUMMY_ID,null,null,$upd_arr);
}

/**
 * @param string $gccId
 * @param string $installId
 * @param string $userId
 * @param string $companyId
 * 
 * @return void
 */
function save_pos_users_company_mapping($gccId,$installId,$userId,$companyId){
	$sql1 = " select GUC_CONTACT_ID from gft_user_company_details where GUC_POS_INSTALL_ID='$installId' ".
			" and GUC_POS_EMPID='$userId' and GUC_COMPANY_ID='$companyId' ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$exist_contact_id = $row1['GUC_CONTACT_ID'];
		if($exist_contact_id!=$gccId){
			$upd1 = " update gft_user_company_details set GUC_CONTACT_ID='$gccId' where GUC_POS_INSTALL_ID='$installId' ".
				  	" and GUC_POS_EMPID='$userId' and GUC_COMPANY_ID='$companyId' ";
			execute_my_query($upd1);
			/* $upd2 = " update gft_app_users set GAU_CONTACT_ID='$gccId',GAU_UPDATED_DATE=now() ".
					" where GAU_CONTACT_ID='$exist_contact_id' and GAU_INSTALL_ID='$installId' ";
			execute_my_query($upd2);
			*/
			$qrs1 = execute_my_query("select GUC_CONTACT_ID from gft_user_company_details where GUC_CONTACT_ID='$exist_contact_id'");
			if(mysqli_num_rows($qrs1)==0){
				execute_my_query("update gft_pos_users set GPU_CONTACT_STATUS='I' where GPU_CONTACT_ID='$exist_contact_id'");
			}
		}
	}else{
		$ins_arr = array(
				'GUC_CONTACT_ID'=>$gccId,
				'GUC_POS_INSTALL_ID'=>$installId,
				'GUC_POS_EMPID'=>$userId,
				'GUC_COMPANY_ID'=>$companyId
		);
		array_insert_query("gft_user_company_details", $ins_arr);
	}
}

/**
 * @param string $gccId
 * @param string $installId
 * @param string $appPcode
 * @param string $userStatus
 * 
 * @return void
 */
function update_app_to_mobile($gccId,$installId,$appPcode,$userStatus){
	$wh_cond = " where GAU_INSTALL_ID='$installId' and GAU_CONTACT_ID='$gccId' and GAU_APP_PCODE='$appPcode' ";
	$sql1 = " select GAU_INSTALL_ID from gft_app_users $wh_cond ";
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1) > 0){
		execute_my_query("update gft_app_users set GAU_USER_STATUS='$userStatus',GAU_UPDATED_DATE=now() $wh_cond");
	}else{
		$ins_arr = array(
				'GAU_CONTACT_ID'=>$gccId,
				'GAU_INSTALL_ID'=>$installId,
				'GAU_APP_PCODE'=>$appPcode,
				'GAU_USER_STATUS'=>$userStatus,
				'GAU_UPDATED_DATE'=>date('Y-m-d H:i:s')
		);
		array_insert_query("gft_app_users", $ins_arr);
	}
}

/**
 * @param int $len
 * 
 * @return string
 */
function randomstring($len){
	$str = "";
	$chars = "ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz123456789";
	for($i=0;$i<$len;$i++){
		$str.=substr($chars,rand(0,strlen($chars)),1);
	}
	return $str;
}

/**
 * @return string
 */
function generate_private_key_password(){
    $str = "";
    $ucase  = "ABCDEFGHJKMNPQRSTUVWXYZ";
    $lcase  = "abcdefghjkmnpqrstuvwxyz";
    $digits = "123456789";
    $splcha = "@!";
    $str .= substr(str_shuffle($ucase),0,3);
    $str .= substr(str_shuffle($lcase),0,3);
    $str .= substr(str_shuffle($digits),0,3);
    $str .= substr(str_shuffle($splcha),0,1);
    return str_shuffle($str);
}

/**
 * @param string $start_date
 * @param string $end_date
 * 
 * @return int
 */
function get_month_diff($start_date,$end_date){
	$ts1 	= strtotime($start_date);
	$ts2 	= strtotime($end_date);
	$year1 	= date('Y', $ts1);
	$year2 	= date('Y', $ts2);
	$month1 = date('m', $ts1);
	$month2 = date('m', $ts2);
	$diff 	= (($year2 - $year1) * 12) + ($month2 - $month1);
	return $diff;
}

/**
 * @param int $emply_id
 * 
 * @return boolean
 */
function is_arp_entry_allowed($emply_id){
    $year_val= date('Y');
    $today = date('Y-m-d');
    $arp_mnth = (int)date('m',strtotime("-11 months",strtotime(date('Y-m-28'))));
	$qr1 = execute_my_query(" select GEM_ARP_START_DATE from gft_emp_master where GEM_ENABLE_ARP=1 and GEM_EMP_ID='$emply_id' and GEM_STATUS='A' ".
	    " and month(GEM_ARP_START_DATE)='$arp_mnth' and GEM_ARP_START_DATE < '$today' ");
	if( $qd1 = mysqli_fetch_array($qr1) ){
		$arp_sd     = (int)get_samee_const("ARP_START");
        $arp_ed     = (int)get_samee_const("ARP_END");
        $day_val    = (int)date('d');
        if( ($day_val >= $arp_sd) && ($day_val <= $arp_ed) ){
            return true;
        }
	}
	return false;
}

/**
 * @param string $status
 * @param string $message
 *
 * @return void
 */
function send_response_in_json($status, $message){
	$resp_arr['status'] = $status;
	$resp_arr['message']= $message;
	echo json_encode($resp_arr);
}

/**
 * @param string $emply_id
 * 
 * @return boolean
 */
function is_valid_emp_id($emply_id){
	$chk_id = (int)get_single_value_from_single_table("GEM_EMP_ID", "gft_emp_master", "GEM_EMP_ID", $emply_id);
	if($chk_id > 0){
		return true;
	}
	return false;
}

/**
 * @param string $act_id
 * @param string $order_no
 * @param string $pcode
 * @param string $pskew
 * @param float $used_time
 * @param int $action_taken
 * @param int $by_emp
 * 
 * @return void
 */
function update_pcs_consumed_time($act_id,$order_no,$pcode,$pskew,$used_time,$action_taken,$by_emp){
	$insert_arr = array(
					'GDT_ACTIVITY_ID'=>$act_id,
					'GDT_ORDER_NO'=>$order_no,
					'GDT_USED_PCODE'=>$pcode,
					'GDT_USED_PSKEW'=>$pskew,
					'GDT_TIME_SPENT_HRS'=>$used_time
				  );
	array_insert_query("gft_delivery_time", $insert_arr);
	if($action_taken==0){ //time not exceeded so calculate incentive
		$que1 = " select sum(GOP_QTY*GOP_SELL_RATE) as sell_rate,GPA_TOTAL_HRS from gft_order_product_dtl ".
				" join gft_pcs_activity_order_hdr on (GPA_ORDER_NO=GOP_ORDER_NO and GPA_PRODUCT_CODE=GOP_PRODUCT_CODE and GPA_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
				" where GOP_ORDER_NO='$order_no' and GOP_PRODUCT_CODE='$pcode' and GOP_PRODUCT_SKEW='$pskew' ";
		$res1 = execute_my_query($que1);
		$processing_percent = 0.60; //60%
		if($row1 = mysqli_fetch_array($res1)){
		    $tot_sell_rate 	= $row1['sell_rate'] * $processing_percent;
			$tot_hrs		= $row1['GPA_TOTAL_HRS'];
			$per_hr_cost	= round($tot_sell_rate/$tot_hrs,2);
			$inc_amt		= round($per_hr_cost * $used_time, 2);
			if($inc_amt==0){
				return ;
			}
			$datetime = date('Y-m-d H:i:s');
			$q1 = " select GOI_ID from gft_orderwise_incentive_owner join gft_incentive_earning on (GIE_OWNER_REF_ID=GOI_ID) where GOI_ATTRIBUTE_ID=11 and GOI_ACTIVITY_ID='$act_id' ";
			$r1 = execute_my_query($q1);
			if($d1 = mysqli_fetch_array($r1)){
				$goi_id = $d1['GOI_ID'];
				execute_my_query(" update gft_incentive_earning set GIE_INCENTIVE_AMT=(GIE_INCENTIVE_AMT+$inc_amt) where GIE_OWNER_REF_ID='$goi_id' ");
				return;
			}
			$act_date = get_single_value_from_single_table("GLD_VISIT_DATE", "gft_activity", "GLD_ACTIVITY_ID", $act_id);
			$ins_arr = array(
						'GOI_INCENTIVE_TYPE'=>'4',
						'GOI_ATTRIBUTE_ID'=>'11',
						'GOI_OWNER_EMP'=>$by_emp,
						'GOI_ACTIVITY_ID'=>$act_id,
						'GOI_CREATED_DATE'=>$act_date
					  );
			$insert_id = array_insert_query("gft_orderwise_incentive_owner", $ins_arr);
			if($insert_id==0){
				return;
			}
			$rec_arr = array(
					'GIE_OWNER_REF_ID'=>$insert_id,
					'GIE_INCENTIVE_AMT'=>$inc_amt,
					'GIE_CREATED_DATE'=>$act_date,
					'GIE_UPDATED_DATE'=>$datetime
			);
			array_insert_query("gft_incentive_earning", $rec_arr);
		}
	}
}

/**
 * @return void
 */
function is_request_from_internal_ip_to_prod(){
	$server_name 		= isset($_SERVER['SERVER_NAME'])?(string)$_SERVER['SERVER_NAME']:'';
	$received_ip_address= isset($_SERVER['REMOTE_ADDR'])?(string)$_SERVER['REMOTE_ADDR']:'';
	if(strcasecmp($server_name, 'sam.gofrugal.com')==0){
		$local_ip_address = get_samee_const("LOCAL_IP_ADDRESS_SETUPWIZARD");
		if(strpos($local_ip_address, $received_ip_address)!==false){
			return true;
		}
	}
	return false;
}

/**
 * @param string $emply_id
 * @param string $access_type
 * @param string $menu_path
 *
 * @return boolean
 */
function is_employee_access_allowed_in_report($emply_id,$access_type,$menu_path){
    $q1 = " select GRC_REPORT_ID from gft_report_communication_access_master ".
        " join gft_menu_master on (mid=GRC_REPORT_ID) ".
        " where GRC_EMP_ID='$emply_id' and menu_path='$menu_path' ";
    if($access_type=='sms'){
        $q1 .= " and GRC_SMS=1 ";
    }elseif($access_type=='mail'){
        $q1 .= " and GRC_MAIL=1 ";
    }elseif($access_type=='export_mobile'){
        $q1 .= " and GRC_EXPORT_MOBILE=1 ";
    }elseif($access_type=='notification'){
        $q1 .= " and GRC_NOTIFICATION=1 ";
    }
    $r1 = execute_my_query($q1);
    if(mysqli_num_rows($r1) > 0){
        return true;
    }
    return false;
}

/**
 * @param int $emply_id
 *
 * @return boolean
 */
function is_grouping_eligible($emply_id){

    $r1 = execute_my_query(" select GEM_ROLE_ID from gft_emp_master where gem_emp_id='$emply_id' and GEM_ROLE_ID in (2,4,6,23,59) ");
    if(mysqli_num_rows($r1) > 0)
        return false;
    return true;
}
/**
 * @param string[string] $update_data
 * 
 * @return boolean
 */
function sat_approval_status_update($update_data){
    $cur_date=date('Y-m-d H:i:s');
    $ins_arr['GCS_SAT_APPROVAL_STATUS'] 	    =  $update_data["status"];
    $ins_arr['GCS_APPROVED_REMARKS'] 		=  $update_data["remarks"];
    $ins_arr['GCS_APPROVED_BY'] 		=  $update_data["updated_by"];
    $ins_arr['GCS_APPROVED_ON'] 		=  $cur_date;
    $key_arr['GCS_ID'] = $update_data["id"];
    array_update_tables_common($ins_arr, "gft_customer_system_uuid_dtl", $key_arr, null, $update_data["updated_by"]);
    
    $to_emp_name='';
    $from_emp_name='';
    $country='';
    $mail_to = array();
    $managers = array();
    $group_id='';
    $approved_by_mail='';
    $approved_by_id='';
    $approval_status='';
    $approval_value='';
    $approved_qry = "select glh_lead_code,gem_group_id,gcs_sat_approval_status,gcs_approved_by,em3.gem_email as 'approved_by_mail',em3.gem_emp_name as 'approved_by_name',glh_country,glh_lead_code,glh_cust_name,GEG_NAME,GEG_PRODUCT_GROUP,".
        ".em.gem_emp_name as 'pc_name',em.gem_emp_id as 'pc_id',GEM_GROUP_ID,em2.gem_emp_id as 'Support_Lead_Id',em2.gem_email as 'Support_Lead_Mail',em.gem_email as 'pc_mail',".
        ".em2.gem_emp_name as 'Support_Lead_Name' from  gft_lead_hdr ".
        "join  gft_customer_system_uuid_dtl  on (gcs_lead_code=glh_lead_code) ".
        "join gft_product_group_master on (glh_main_product=GPG_SUPPORT_GROUP_ID or  glh_main_product=GPG_PD_EXPERT_GROUP )".
        "join gft_escalation_group_master on (GEG_PRODUCT_GROUP = concat(gpg_product_family_code,'-',gpg_skew))".
        "join gft_emp_master em on (em.gem_emp_id =GCS_APPROVAL_REQUESST_BY)".
        "join gft_emp_master em3 on (em3.gem_emp_id =GCS_APPROVED_BY)".
        "join gft_escalation_master es on (geg_group_id=es.gem_group_id)".
        "join gft_emp_master em2 on (em2.gem_emp_id =es.gem_emp_id)".
        "where es.GEM_ESCALATION_LEVEL=1  and GCS_ID='".$update_data["id"]."' ";
    $result=execute_my_query($approved_qry);
    $approval_submit_notification_content=array();
    while($row=mysqli_fetch_array($result)){
        $approval_status = $row['gcs_sat_approval_status'];
        if($approval_status==2){
            $approval_value='Approved';
        }elseif($approval_status==3){
            $approval_value='Rejected';
        }
        $cust_id = $row['glh_lead_code'];
        $group_id=$row['gem_group_id'];
        $country =$row['glh_country'];
        $managers[] =$row['Support_Lead_Id'];
        $mail_to[] = $row['Support_Lead_Mail'];
        $from_emp_name =$row['Support_Lead_Name'];
        $approved_by_name = $row['approved_by_name'];
        $mail_to[] =  $row['pc_mail'];
        $to_emp_name = $row['pc_name'];
        $managers[]  = $row['pc_id'];
        $approved_by_id =  $row['gcs_approved_by'];
        $approved_by_mail = $row['approved_by_mail'];
        $cust_name =  $row['glh_cust_name'];
        $approval_submit_notification_content = array(
            'PC_Name' => $to_emp_name,
            'Employee_Name' => $from_emp_name,
            'Approved_Person_Name' =>  array($approved_by_name),
            'Customer_Name' => array($cust_name),
            'Approval_Status' => array($approval_value),
            'Customer_Id' => array($cust_id)
        );
        
        if($country!='India'){
            $intl_qry = " select em.GEM_EMP_name,em.GEM_EMAIL as 'intlPc_mail',em.GEM_EMP_ID as 'intlPc_id' ".
                " from gft_emp_master em where em.web_group=69 and em.gem_role_id=2 and em.gem_status='A' ";
            $result_pc= execute_my_query($intl_qry);
            while($row=mysqli_fetch_array($result_pc)){
                $managers[] = $row['intlPc_id'];
                $mail_to[] = $row['intlPc_mail'];
            }
        }
    }
    $pm_qry = "select gem_group_id,em.GEM_EMP_name,em.GEM_EMP_ID as 'manager_id',em.GEM_EMAIL as 'manager_mail'  from gft_escalation_group_master".
        " join gft_escalation_master ge on(GEG_GROUP_ID=GEM_GROUP_ID)".
        " join gft_emp_master em on(em.gem_emp_id=ge.GEM_EMP_ID) ".
        " where GEM_ESCALATION_LEVEL=3  and gem_group_id='$group_id'";
    $result= execute_my_query($pm_qry);
    while($row=mysqli_fetch_array($result)){
        $managers[] = $row['manager_id'];
        $mail_to[] = $row['manager_mail'];
    }
    if($approval_status==2){
        send_formatted_mail_content($approval_submit_notification_content,92,337,null,null,$mail_to,null,null,null,null,null,null);
        foreach ($managers as $mid){
            send_formatted_notification_content($approval_submit_notification_content,92,103,1,$mid);
        }
    }elseif($approval_status==3){
        send_formatted_mail_content($approval_submit_notification_content,92,338,null,null,$mail_to,null,null,null,null,null,null);
        foreach ($managers as $mid){
            send_formatted_notification_content($approval_submit_notification_content,92,104,1,$mid);
        }
    }
    return true;
}
/**
 * @param string $lead_code
 *
 * @return string
 */
function check_to_allow_vertical_to_edit($lead_code){
    $allow_vertical_to_edit = "false";
    $result_allow_vertical_edit = execute_my_query("select  GID_ORDER_NO from gft_install_dtl_new
                                    INNER JOIN gft_product_master pm ON(GPM_PRODUCT_CODE=GID_LIC_PCODE AND GPM_PRODUCT_SKEW=GID_LIC_PSKEW AND GPM_LICENSE_TYPE IN(1,2) )
                                    INNER JOIN gft_product_family_master pf ON(GID_LIC_PCODE=pf.GPM_PRODUCT_CODE AND pf.GPM_IS_BASE_PRODUCT='Y')
                                    where gid_lead_code=".$lead_code." AND GID_STATUS='A'");
    if(mysqli_num_rows($result_allow_vertical_edit)==0){
        $allow_vertical_to_edit = "true";
    }
    return $allow_vertical_to_edit;
}
/**
 * @param int $uid
 * @return string
 */
function get_pipeline_dashboard($uid){
    $role_arr       =   get_two_dimensinal_array_from_query(" select GRM_ROLE_ID,GRM_ROLE_DESC from gft_role_master where GRM_STATUS='A'  ", 'GRM_ROLE_ID','GRM_ROLE_DESC');
    $team_arr       =   get_two_dimensinal_array_from_query("select GEW_ID,GEW_DESC  from gft_emp_web_display where GEW_STATUS=1 and GEW_DESC!='' ", 'GEW_ID','GEW_DESC');
    $channel_arr    =   get_two_dimensinal_array_from_query('select GCM_ID,GCM_NAME from gft_interview_channel_master where GCM_STATUS=1 ','GCM_ID', 'GCM_NAME');
    $get_cache = " select GPD_ROLE_ID,GPD_TEAM_ID,GPD_CHANNEL_ID,GPD_SCREENING_CALL, ".
        " GPD_SHORTLIST,GPD_REMARKS from gft_pipeline_dashboard ".
        " where GPD_DAILY_REPORT_ID=0 and GPD_EMP_ID=$uid  ";
    $cache_data = execute_my_query($get_cache);
    $result = <<<END
        <!DOCTYPE html><html><body>
        <link rel="stylesheet" type="text/css" href="CSS/bootstrap.min.css">
        <script src="js/interview_dashboard_common_function.js"></script>
        <div class="" id="pipeline_div">
        <div class="row clearfix">
        <div class="col-md-12 table-responsive">
        <table class="table table-bordered table-hover table-sortable" id="pipeline_table">
        <thead>
        <tr class="th_row">
        <th class="text-center">Role</th>
        <th class="text-center">Team</th>
        <th class="text-center">Channel</th>
        <th class="text-center">Pre-screening call</th>
        <th class="text-center">Shortlist</th>
        <th class="text-center">Remarks</th>
        <th class="text-center" style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
        </th>
        </tr>
        </thead>
        <tbody>
END;
    $i=0;
    if(mysqli_num_rows($cache_data)==0){
        $role_cmb       =   fix_combobox_with($id="emp_role$i",$name="emp_role$i",$role_arr,'','',$default_value='Select',$style="style='width:150px' ",$add_opt_group=false,'','','','form-control emp_role',true,'autocomplete="off"');
        $team_cmb       =   fix_combobox_with($id='',$name="emp_team$i",$team_arr,'','',$default_value='Select',$style="style='width:150px' ",$add_opt_group=false,'','','','form-control emp_team',true,'autocomplete="off"');
        $channel_cmb    =   fix_combobox_with($id='',$name="channel$i",$channel_arr,'','',$default_value='Select',$style="style='width:150px' ",$add_opt_group=false,'','','','form-control channel',true,'autocomplete="off"');
       $result .= <<<END
         <tr id="addr$i" data-id=$i class="">
			<td data-name="emp_role">$role_cmb</td>
			<td data-name="emp_team">$team_cmb</td>
			<td data-name="channel">$channel_cmb</td>
			<td data-name="call">
				<input type="number" min="0" autocomplete="off" id="call$i" name="call$i" placeholder=0 class="form-control int_input call" />
			</td>
			<td data-name="short_list">
				<input type="number" min="0" autocomplete="off"  name="short_list$i" placeholder=0 class="form-control int_input short_list"/>
			</td>
			<td data-name="remarks">
				<textarea name="remarks$i" autocomplete="off" placeholder="Description" class="form-control remarks"></textarea>
			</td>
            <td data-name="del">
                <button name="del$i" class="btn btn-danger row-remove"><span aria-hidden="true">x</span></button>
            </td>
		</tr>
END;
    }else{
        while($row=mysqli_fetch_assoc($cache_data)){
            $call_cnt       = $row['GPD_SCREENING_CALL'];
            $shortlist_Cnt  = $row['GPD_SHORTLIST'];
            $remarks        = $row['GPD_REMARKS'];
            $role_cmb       =   fix_combobox_with($id='',$name="emp_role$i",$role_arr,$row['GPD_ROLE_ID'],'',$default_value='Select',$style="style='width:150px' ",$add_opt_group=false,'','','','form-control emp_role',true,'autocomplete="off"');
            $team_cmb       =   fix_combobox_with($id='',$name="emp_team$i",$team_arr,$row['GPD_TEAM_ID'],'',$default_value='Select',$style="style='width:150px' ",$add_opt_group=false,'','','','form-control emp_team',true,'autocomplete="off"');
            $channel_cmb    =   fix_combobox_with($id='',$name="channel$i",$channel_arr,$row['GPD_CHANNEL_ID'],'',$default_value='Select',$style="style='width:150px' ",$add_opt_group=false,'','','','form-control channel',true,'autocomplete="off"');
		$result .= <<<END
	       <tr id="addr$i" data-id=$i >
				<td data-name="emp_role">$role_cmb</td>
				<td data-name="emp_team">$team_cmb</td>
				<td data-name="channel">$channel_cmb</td>
				<td data-name="call">
					<input type="number" min="0" autocomplete="off" value=$call_cnt name="call$i" placeholder="0" class="form-control int_input call" />
				</td>
				<td data-name="short_list">
					<input type="number"  min="0" autocomplete="off" name="short_list$i" value=$shortlist_Cnt  placeholder="0" class="form-control int_input short_list"/>
				</td>
				<td data-name="remarks">
					<textarea name="remarks$i" autocomplete="off" placeholder="Description" class="form-control remarks">$remarks</textarea>
				</td>
                <td data-name="del">
                    <button name="del$i" class="btn btn-danger row-remove"><span aria-hidden="true">x</span></button>
                </td>
			</tr>
END;
            $i++;
        }
        }
		$result .= <<<END
	</tbody></table></div></div>
	   <div class="container-fluid float-right">
      	 <a tableId="pipeline_table" class="btn btn-primary add_row">Add</a>
    	 <a id="pipeline_submit" class="btn btn-success ">submit</a>
         <a class='btn btn-danger cancel' hide="pipeline_modal"> Close</a>
    </div></div></body></html> 
END;
        return $result;
}
/**
 * @param int $uid
 * @return string
 */
function get_interview_dashboard($uid){
    $item_arr    =   get_two_dimensinal_array_from_query('select GIM_ID,GIM_NAME from gft_interview_item_master where GIM_STATUS=1 ','GIM_ID', 'GIM_NAME');
    $get_cache = " select  GID_ITEM_ID,GID_COUNT,GID_REMARKS from gft_interview_dashboard ".
        " where GID_DAILY_REPORT_ID=0 and GID_EMP_ID=$uid  ";
    $cache_data = execute_my_query($get_cache);
$result = <<<END
<!DOCTYPE html><html><body>
<div class="" id="interview_div">
    <div class="row clearfix">
    	<div class="col-md-12 table-responsive">
			<table class="table table-bordered table-hover table-sortable" id="interview_table">
				<thead>
					<tr class='th_row'>
						<th class="text-center">Particulars</th>
						<th class="text-center">Count</th>
						<th class="text-center">Remarks</th>
    					<th class="text-center" width='50px' style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
						</th>
					</tr>
				</thead>
				<tbody>
END;
    $i=0;
    if(mysqli_num_rows($cache_data)==0){
        $item_cmb       =   fix_combobox_with($id='',$name="item$i",$item_arr,'','',$default_value='Select',$style="style='width:250px' ",$add_opt_group=false,'','','','form-control item',true,'autocomplete="off"');
$result .= <<<END
		<tr id="addr$i" data-id=$i class="">
			<td data-name="item" width='250px'>$item_cmb</td>
			<td data-name="count" width='100px'>
				<input type="number" autocomplete="off" min="0" name="count$i" placeholder='0' class="form-control int_input count" />
			</td>
			<td data-name="remarks" width='300px'>
				<textarea name="remarks$i" autocomplete="off" placeholder="Description" class="form-control remarks"></textarea>
			</td>
            <td data-name="del" width='50px'>
                <button name="del$i" class="btn btn-danger row-remove"><span aria-hidden="true">x</span></button>
            </td>
		</tr>
END;
    }else{
        while($row=mysqli_fetch_assoc($cache_data)){
            $count          = $row['GID_COUNT'];
            $remarks        = $row['GID_REMARKS'];
            $item_cmb       =   fix_combobox_with($id='',$name="item$i",$item_arr,$row['GID_ITEM_ID'],'',$default_value='Select',$style="style='width:250px' ",$add_opt_group=false,'','','','form-control item',true,'autocomplete="off"');
$result .= <<<END
			<tr id="addr$i" data-id=$i class="">
				<td data-name="item" width='250px'>$item_cmb</td>
				<td data-name="count" width='100px'>
					<input type="number"  min="0" autocomplete="off"  value=$count name="count$i" placeholder='0' class="form-control int_input count" />
				</td>
				<td data-name="remarks" width='300px'>
					<textarea name="remarks$i" autocomplete="off" placeholder="Description" class="form-control remarks">$remarks</textarea>
				</td>
                <td data-name="del" width='50px'>
                    <button name="del$i" class="btn btn-danger row-remove"><span aria-hidden="true">x</span></button>
                </td>
			</tr>
END;
            $i++;
        }
        }
$result .= <<<END
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="container-fluid float-right">
        
      	<a tableId="interview_table" class="btn btn-primary add_row">Add</a>
    	<a id="interview_submit" class="btn btn-success ">submit</a>
        <a class='btn btn-danger cancel' hide="interview_modal"> Close</a>
    </div>
</div>
</body>
</html>
END;
return $result;
}
/**
 * @param int $uid
 * @return string
 */
function get_offer_dashboard($uid){
    $item_arr    =   get_two_dimensinal_array_from_query('select GOM_ID,GOM_NAME from gft_offer_item_master where GOM_STATUS=1 ','GOM_ID', 'GOM_NAME');
    $get_cache = " select  GOD_ITEM_ID,GOD_COUNT,GOD_REMARKS from gft_offer_dashboard ".
        " where GOD_DAILY_REPORT_ID=0 and GOD_EMP_ID=$uid  ";
    $cache_data = execute_my_query($get_cache);
$result = <<<END
<!DOCTYPE html><html><body>
<div class="">
    <div class="row clearfix">
    	<div class="col-md-12 table-responsive">
			<table class="table table-bordered table-hover table-sortable" id="offer_table">
				<thead>
					<tr class='th_row'>
						<th class="text-center">Particulars</th>
						<th class="text-center">Count</th>
						<th class="text-center">Remarks</th>
    					<th class="text-center" width='50px' style="border-top: 1px solid #ffffff; border-right: 1px solid #ffffff;">
						</th>
					</tr>
				</thead>
				<tbody>
END;
    $i=0;
    if(mysqli_num_rows($cache_data)==0){
        $item_cmb       =   fix_combobox_with($id='',$name="item$i",$item_arr,'','',$default_value='Select',$style="style='width:250px' ",$add_opt_group=false,'','','','form-control item',true,'autocomplete="off"');
$result .= <<<END
		<tr id="addr$i" data-id=$i class="">
			<td data-name="item" width='250px'>$item_cmb</td>
			<td data-name="count" width='100px'>
				<input type="number" autocomplete="off"  min="0"  name="count$i" placeholder='0' class="form-control int_input count" />
			</td>
			<td data-name="remarks" width='300px'>
				<textarea name="remarks$i" autocomplete="off" placeholder="Description" class="form-control remarks"></textarea>
			</td>
            <td data-name="del" width='50px'>
                <button name="del$i" class='btn btn-danger row-remove'><span aria-hidden="true">x</span></button>
            </td>
		</tr>
END;
    }else{
        while($row=mysqli_fetch_assoc($cache_data)){
            $count          = $row['GOD_COUNT'];
            $remarks        = $row['GOD_REMARKS'];
            $item_cmb       =   fix_combobox_with($id='',$name="item$i",$item_arr,$row['GOD_ITEM_ID'],'',$default_value='Select',$style="style='width:250px' ",$add_opt_group=false,'','','','form-control item',true,'autocomplete="off"');
$result .= <<<END
			<tr id="addr$i" data-id=$i class="">
				<td data-name="item" width='250px'>$item_cmb</td>
				<td data-name="count" width='100px'>
					<input type="number" autocomplete="off"  min="0"  value=$count name="count$i" placeholder='0' class="form-control int_input count" />
				</td>
				<td data-name="remarks" width='300px'>
					<textarea name="remarks$i" autocomplete="off" placeholder="Description" class="form-control remarks">$remarks</textarea>
				</td>
                <td data-name="del" width='50px'>
                    <button name="del$i" class='btn btn-danger row-remove'><span aria-hidden="true">x</span></button>
                </td>
			</tr>
END;
            $i++;
        }
       }
$result .= <<<END
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="container-fluid float-right">
      	<a tableId="offer_table" class="btn btn-primary add_row">Add</a>
    	<a id="offer_submit" class="btn btn-success ">submit</a>
        <a class='btn btn-danger cancel' hide="offer_modal"> Close</a>
    </div>
</div>
</body>
</html>
END;
return $result;
}
/**
 * @param int $uid
 * @return string
 */
function get_conversion_dashboard($uid){
    $get_cache = " select  GCD_OFFERS,GCD_JOINERS,GCD_INTERN_TO_EMP,GCD_OFFER_DECLINE from gft_conversion_dashboard ".
        " where GCD_DAILY_REPORT_ID=0 and GCD_EMP_ID=$uid  ";
    $cache_data = execute_my_query($get_cache);
$result = <<<END
<!DOCTYPE html><html><body>
<div class="">
    <div class="row clearfix">
    	<div class="col-md-12 table-responsive">
			<table class="table table-bordered table-hover table-sortable" id="conversion_table">
				<thead>
					<tr class='th_row'>
						<th class="text-center">Offers</th>
						<th class="text-center">Joiners</th>
						<th class="text-center">Intern to employee</th>
                        <th class="text-center">Offer Decline</th>
						</th>
					</tr>
				</thead>
				<tbody>
END;
    $i=0;
    $gcd_offers= $gcd_joiners = $gcd_intern_to_emp = $gcd_offer_decline = 0;
    if($row=mysqli_fetch_assoc($cache_data)){
        $gcd_offers =  $row['GCD_OFFERS'];
        $gcd_joiners =  $row['GCD_JOINERS'];
        $gcd_intern_to_emp = $row['GCD_INTERN_TO_EMP'];
        $gcd_offer_decline = $row['GCD_OFFER_DECLINE'];
    }
$result .= <<<END
            <tr id="addr$i" data-id=$i class="">
    			<td data-name="offer" width='250px'>
                    <input type="number" autocomplete="off" min="0"  value="$gcd_offers" name="offer$i" placeholder='0' class="form-control int_input offer" />
                </td>
    			<td data-name="joiner" width='100px'>
    				<input type="number" autocomplete="off" min="0"  value="$gcd_joiners" name="joiner$i" placeholder='0' class="form-control int_input joiner" />
    			</td>
    			<td data-name="int_to_emp" width='300px'>
    				<input type="number" autocomplete="off" min="0"  value="$gcd_intern_to_emp" name="int_to_emp$i" placeholder='0' class="form-control int_input int_to_emp" />
    			</td>
                <td data-name="offer_dec" width='300px'>
    				<input type="number" autocomplete="off" min="0"  value="$gcd_offer_decline" name="offer_dec$i" placeholder='0' class="form-control int_input offer_dec" />
    			</td>
             </tr>
				</tbody>
			</table>
		</div>
	</div>
	
	<div class="container-fluid float-right">
    	<a id="conversion_submit" class="btn btn-success ">submit</a>
        <a class='btn btn-danger cancel' hide="conversion_modal"> Close</a>
    </div>
</div>
</body>
</html>
END;
return  $result;
}
/**
 * @param int $uid
 * @param boolean $edit
 * @return string
 */
function get_pipeline_summary($uid,$edit){
    $edit_content = '';
    $get_cache = " select GRM_ROLE_DESC,GEW_DESC,GCM_NAME,GPD_SCREENING_CALL, ".
        " GPD_SHORTLIST,GPD_REMARKS from gft_pipeline_dashboard ".
        " join gft_role_master on (GRM_ROLE_ID=GPD_ROLE_ID) ".
        " join gft_emp_web_display on (GEW_ID=GPD_TEAM_ID) ".
        " join gft_interview_channel_master on (GCM_ID=GPD_CHANNEL_ID) ".
        " where GPD_DAILY_REPORT_ID=0 and GPD_EMP_ID=$uid  ";
    $cache_data = execute_my_query($get_cache);
    $output = "<table border='1' class='dashboard_tbl'><thead><tr style='background-color:#53babf;'><td>Role</td><td>Team/Region</td><td>Channel</td><td>Screening call</td><td>Shorlist</td><td>Remarks</td></tr></thead> <tbody>";
    if(mysqli_num_rows($cache_data)==0){
        $output .= "<tr><td colspan='6' align='center'>No records found</td></tr>";
    }else{
        while($row=mysqli_fetch_assoc($cache_data)){
            $role = $row['GRM_ROLE_DESC'];
            $team = $row['GEW_DESC'];
            $channel = $row['GCM_NAME'];
            $call_cnt       = $row['GPD_SCREENING_CALL'];
            $shortlist_Cnt  = $row['GPD_SHORTLIST'];
            $remarks        = $row['GPD_REMARKS'];
            $output .= "<tr><td>$role</td><td>$team</td><td>$channel</td><td>$call_cnt</td><td>$shortlist_Cnt</td><td>$remarks</td></tr>";
            
        }
    }
    if($edit) $edit_content = '<tr><td colspan="6" align="center"><input type="button" class="dash_edit" onclick="modal(\'pipeline_modal\')" value="Edit"></td></tr>';
    $output .= "$edit_content</tbody></table>";
    return ($output);
}
/**
 * @param int $uid
 * @param boolean $edit
 * @return string
 */
function get_interview_summary($uid,$edit){
    $edit_content = '';
    $get_cache = " select  GIM_NAME,GID_COUNT,GID_REMARKS ".
        " from gft_interview_dashboard ".
        " join gft_interview_item_master on (GIM_ID=GID_ITEM_ID) ".
        " where GID_DAILY_REPORT_ID=0 and GID_EMP_ID=$uid  ";
    $cache_data = execute_my_query($get_cache);
    $output = "<table border='1' class='dashboard_tbl'><thead><tr style='background-color:#53babf'><td>Particulars</td><td>Count</td><td>Remarks</td></tr></thead> <tbody>";
    if(mysqli_num_rows($cache_data)==0){
        $output .= "<tr><td colspan='3' align='center'>No records found</td></tr>";
    }else{
        while($row=mysqli_fetch_assoc($cache_data)){
            $item   = $row['GIM_NAME'];
            $count  = $row['GID_COUNT'];
            $remarks= $row['GID_REMARKS'];
            $output .= "<tr><td>$item</td><td>$count</td><td>$remarks</td></tr>";
            
        }
    }
    if($edit) $edit_content = '<tr><td colspan="3" align="center"><input type="button" class="dash_edit" onclick="modal(\'interview_modal\')" value="Edit"></td></tr>';
    $output .= "$edit_content</tbody></table>";
    return ($output);
}
/**
 * @param int $uid
 * @param boolean $edit
 * @return string
 */
function get_offer_summary($uid,$edit){
    $edit_content = '';
    $get_cache = " select  GOM_NAME,GOD_COUNT,GOD_REMARKS ".
        " from gft_offer_dashboard ".
        " join gft_offer_item_master on (GOM_ID=GOD_ITEM_ID) ".
        " where GOD_DAILY_REPORT_ID=0 and GOD_EMP_ID=$uid  ";
    $cache_data = execute_my_query($get_cache);
    $output = "<table border='1' class='dashboard_tbl'><thead><tr style='background-color:#53babf'><td>Particulars</td><td>Count</td><td>Remarks</td></tr></thead> <tbody>";
    if(mysqli_num_rows($cache_data)==0){
        $output .= "<tr><td colspan='3' align='center'>No records found</td></tr>";
    }else{
        while($row=mysqli_fetch_assoc($cache_data)){
            $item   = $row['GOM_NAME'];
            $count  = $row['GOD_COUNT'];
            $remarks= $row['GOD_REMARKS'];
            $output .= "<tr><td>$item</td><td>$count</td><td>$remarks</td></tr>";
        }
    }
    if($edit) $edit_content = '<tr><td colspan="3" align="center"><input type="button" class="dash_edit" onclick="modal(\'offer_modal\')" value="Edit"></td></tr>';
    $output .= "$edit_content</tbody></table>";
    return ($output);
}
/**
 * @param int $uid
 * @param boolean $edit
 * 
 * @return string
 */
function get_conversion_summary($uid,$edit){
    $edit_content = '';
    $get_cache = " select  GCD_OFFERS,GCD_JOINERS,GCD_INTERN_TO_EMP,GCD_OFFER_DECLINE from gft_conversion_dashboard ".
        " where GCD_DAILY_REPORT_ID=0 and GCD_EMP_ID=$uid  ";
    $cache_data = execute_my_query($get_cache);
    $output = "<table border='1' class='dashboard_tbl'><thead><tr style='background-color:#53babf'><td>Offers</td><td>Joiners</td><td>Intern to employee</td><td>Offer decline</td></tr></thead> <tbody>";
    if(mysqli_num_rows($cache_data)==0){
        $output .= "<tr><td colspan='4' align='center'>No records found</td></tr>";
    }else{
        if($row=mysqli_fetch_assoc($cache_data)){
            $gcd_offers =  $row['GCD_OFFERS'];
            $gcd_joiners =  $row['GCD_JOINERS'];
            $gcd_intern_to_emp = $row['GCD_INTERN_TO_EMP'];
            $gcd_offer_decline = $row['GCD_OFFER_DECLINE'];
            $output .= "<tr><td>$gcd_offers</td><td>$gcd_joiners</td><td>$gcd_intern_to_emp</td><td>$gcd_offer_decline</td></tr>";
        }
    }
    if($edit) $edit_content = '<tr><td colspan="4" align="center"><input type="button" class="dash_edit" onclick="modal(\'conversion_modal\')" value="Edit"></td></tr>';
    $output .= "$edit_content</tbody></table>";
    return ($output);
}
/**
 * @param string $receipt_id
 * @param string $lead_code
 * @param string $collect_emp_id
 * @param int $send_mail
 * @param string $payment_email_id
 * 
 * @return void
 */
function send_receipt_to_customer($receipt_id,$lead_code, $collect_emp_id, $send_mail=0, $payment_email_id=""){    
    global $attach_path;
    $today_date_time = date('Y-m-d H:i:s');
    $receipt_content=generate_receipt_content($receipt_id,false);
    if($receipt_content!=''){
        $receipt_content='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">' .
            '<html><head></head><body>' .$receipt_content.'</body></html>';
        $filename=generatereceiptpdf($receipt_id,$receipt_content);
        $cust_id=$cust_name='';
        $cust_email=/*. (string[int]) .*/array();
        $que_dtl=" select glh_lead_code,glh_cust_name,GEM_EMP_ID, GEM_ROLE_ID, GEM_EMP_NAME, GEM_MOBILE, ".
            " GRD_RECEIPT_AMT,GOD_BALANCE_AMT,GOD_ORDER_NO,GRD_USD_AMT,glh_country,GRD_COLLECTION_AGAINST_QUOTE,GRD_COLLECTION_AGAINST_TYPE from gft_receipt_dtl ".
            " join gft_emp_master on (GEM_EMP_ID=GRD_EMP_ID) ".
            " join gft_lead_hdr on (glh_lead_code=grd_lead_code) ".
            " join gft_collection_receipt_dtl on (GCR_RECEIPT_ID=GRD_RECEIPT_ID) ".
            " left join gft_order_hdr on (GOD_ORDER_NO=GCR_ORDER_NO) ".
            " where grd_receipt_id=".$receipt_id;
        $lead_dtl=execute_my_query($que_dtl);
        if($data_lead=mysqli_fetch_array($lead_dtl)){
            $cust_id = $data_lead['glh_lead_code'];
            $cust_name = $data_lead['glh_cust_name'];
            $empl_id = $data_lead['GEM_EMP_ID'];
            $empl_name = $data_lead['GEM_EMP_NAME'];
            $rec_amt = $data_lead['GRD_RECEIPT_AMT'];
            $collection_type = $data_lead['GRD_COLLECTION_AGAINST_TYPE'];
            $collection_quote = $data_lead['GRD_COLLECTION_AGAINST_QUOTE'];
            if(strcasecmp($data_lead['glh_country'],'India')!=0) {
                $rec_amt = $data_lead['GRD_USD_AMT'];
            }
            $os_amt = (float)$data_lead['GOD_BALANCE_AMT'];
            $order_no = isset($data_lead['GOD_ORDER_NO'])?$data_lead['GOD_ORDER_NO']:'0';
            if($order_no!='0') {
                generate_pos_notification($order_no,false);
                notify_pos_product($lead_code, '', 'license_sync', 'N009');
            }
            $cust_email_arr = customerContactDetail($cust_id);
            $cust_email = explode(',',$cust_email_arr['EMAIL']);
            $mail_content = array(
                "Attachment"=>array($attach_path."/receipt/".$filename),
                "Type"=>array("Payment Acknowledgement Receipt"),
                "Customer_Name"=>array($cust_name),
                "Employee_Name"=>array($empl_name),
                "Collection_Amt"=>array($rec_amt),
                "Credit_Amount"=>array($rec_amt),
                "Outstanding_Amt"=>array($os_amt),
                "AMOUNT"=>array($os_amt),
                "Customer_Id"=>array($cust_id),
                "dateon"=>array($today_date_time)
            );
            $sms_category = 163;
            if($data_lead['GEM_ROLE_ID']=='21'){
                $sms_category = 170;
            }
            if(is_mydelight_app_active($empl_id)){
                send_formatted_notification_content($mail_content, 13, 26, 1, $empl_id);
            }else{
                $sms_content=htmlentities(get_formatted_content($mail_content, $sms_category));
                entry_sending_sms($data_lead['GEM_MOBILE'], $sms_content, $sms_category,  $empl_id);
            }
            if($send_mail){
                $cc_mail_id		=	array();
                $cc_mail_id[]  	= $collect_emp_id;
                $partner_dtl	=	get_asa_commission_partner_dtl($cust_id);
                $email_id = array();
                if($payment_email_id!=""){
                    $email_id[] = $payment_email_id;
                }
                $sl_email_que = '';
                if($collection_type==1 && $collection_quote!=''){
                    $sl_email_que = " select GQH_QUOTATION_TO_EMAILS as emails from gft_quotation_hdr where GQH_ORDER_NO='$collection_quote' ";
                    
                }else if($collection_type==2 && $collection_quote!=''){
                    $sl_email_que = " select GPH_PROFORMA_TO_EMAILS as emails from gft_proforma_hdr where GPH_ORDER_NO='$collection_quote' ";
                }else if($collection_quote==''){
                    $sl_email_que = " select GQH_QUOTATION_TO_EMAILS as emails from gft_quotation_hdr where  GQH_CONVERTED_ORDER_NO='$order_no' ".
                        " union ".
                        " select GPH_PROFORMA_TO_EMAILS as emails from gft_proforma_hdr where  GPH_CONVERTED_ORDER_NO='$order_no' ";
                }
                if($collection_type != ''){
                    $email_que = execute_my_query($sl_email_que);
                    if($row=mysqli_fetch_assoc($email_que)){
                        $toemail = explode(",",$row['emails']);
                        if(is_array($toemail) && count($toemail) != 0){
                            foreach($toemail as $key=>$value){
                                $sl_que = execute_my_query("select GCC_CONTACT_NO from gft_customer_contact_dtl where gcc_id='$value'");
                                while($eml = mysqli_fetch_assoc($sl_que)){
                                    $email_id[] = $eml['GCC_CONTACT_NO'];
                                }
                            }
                        }
                    }
                }
                $partner_emp_id = get_cp_emp_id_for_leadcode($cust_id);
                $email_id[] = get_single_value_from_single_table('gem_email', 'gft_emp_master', 'gem_emp_id', $partner_emp_id);
                if(isset($partner_dtl['partner_emp_id']) && $partner_dtl['partner_emp_id']!=""){
                    $cc_mail_id[]  	= $partner_dtl['partner_emp_id'];
                    $partner_id		= $partner_dtl['partner_emp_id'];
                    $emp_dtl_arr = get_emp_master($empl_id,'A',null,false);
                    send_notification_to_partner($partner_id,$cust_id,$cust_email_arr['cust_name'],$emp_dtl_arr[0][1],$rec_amt,"collection");
                }
                send_formatted_mail_content($mail_content, 89, 175, null , null , $email_id, $cc_mail_id);
            }
        }
    }
    
}
/**
 * @param string $product_skew
 * 
 * @return int
 */
function is_prorated_client_alr_skew($product_skew){
    $skew_string = substr($product_skew, 4);
    $ret = (in_array($skew_string,array("ULPRPALR","ULPRSALR","ULPRERSALR","ULPRERPALR"))) ? 1 : 0;
    return $ret;
}

/**
 * @param int $emply_id
 * @param string $hashed_password
 *
 * @return void
 */
function update_employee_password($emply_id,$hashed_password){
    $new_password = mysqli_real_escape_string_wrapper(sam_password_hash($hashed_password));
    $up_que = " update gft_login_master set GLM_PASSWORD='$new_password',GLM_NEED_PASSWORD_RESET=0,GLM_UPDATED_DATE=now() ".
        " where GLM_EMP_ID='$emply_id' ";
    execute_my_query($up_que);
    execute_my_query("update gft_emp_auth_key set AUTH_TOKEN=null where EMP_ID='$emply_id'");
}

/**
 * @param string $emply_id
 *
 * @return boolean
 */
function restrict_contact_access($emply_id){
    if(is_authorized_group($emply_id, 152)){
        if(!is_authorized_group($emply_id, 151)){
            return true;
        }
    }
    return false;
}

/**
 * @param mixed $array_list
 * @param string $key1
 * @param string $key2
 *
 * @return mixed[string][string]
 */
function form_dynamic_array($array_list,$key1,$key2){
    $res = array();
    foreach ($array_list as $key => $val){
        $res[] = array($key1=>$val[0],$key2=>$val[1]);
    }
    return $res;
}
/**
 * @param string $query
 * @param string $id
 * @param string $label
 * @param string $key1
 * @param string $key2
 *
 * @return string
 */
function get_two_dimensional_array_with_id_label_from_query($query,$id,$label,$key1="value",$key2="label"){
    if($query == '' || $id == '' || $label == ""){
        send_failure_response(array("message"=>"Invalid arguements"), HttpStatusCode::BAD_REQUEST);
        exit;
    }
    $output = array();
    $result = execute_my_query($query);
    while ($row = mysqli_fetch_assoc($result)){
        $output[] = array($key1=>$row[$id],$key2=>$row[$label]);
    }
    return $output;
}


/**
 * @param string $query
 * @param string $id
 * @param string $label
 * @param string $status_condition
 * @param string $order_by
 *
 * @return string
 */
function get_two_dimensional_array_with_id_label_from_table($table_name,$id,$label,$status_condition='',$order_by=''){
    if($table_name == '' || $id == '' || $label == ""){
        send_failure_response(array("message"=>"Invalid arguements"), HttpStatusCode::BAD_REQUEST);
        exit;
    }
    $output = array();
    $where_qry = ($status_condition!='' ? ' and '.$status_condition : '');
    $order_qry = ($order_by!='' ? ' order by '.$order_by : '');
    
    $query = " select $id,$label from $table_name where 1 $where_qry $order_qry  ";
    $result = execute_my_query($query);
    while ($row = mysqli_fetch_assoc($result)){
        $output[] = array("value"=>$row[$id],"label"=>$row[$label]);
    }
    return ($output);
}

?>
