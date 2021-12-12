<?php
require_once( __DIR__ . "/../dbcon.php");
require_once( __DIR__ . "/../common_util.php"); 
require_once( __DIR__ . "/../function.insert_stmt.php");
require_once( __DIR__ . "/../lic_util.php");
require_once( __DIR__ . "/../push_notification/push_notification_util.php");
require_once( __DIR__ . "/../license_module/licenseClass.php");
require_once( __DIR__ . "/../license_module/addonActivationClass.php");

/**
 * @param string $ecode
 * @param string $mesg
 * @param string $mode
 * 
 * @return void
 */
function sendError($ecode,$mesg,$mode=''){
	//TODO: Request Log
	$return_array=/*. (string[string]) .*/ array();
	if($mode=='offline'){
		$return_array['status'] = "error";
		$return_array['message'] = $mesg;
	}else{
		$return_array['status']='Failure';
		$return_array['error_code']=$ecode;
		$return_array['error_message']=$mesg;
	}
	print json_encode($return_array);
}
/**
 * @param string $emp_id
 * 
 * @return string[string]
 */
function get_employee_support_group($emp_id){
	$return_array = array();
	$sql_query = "select  GSP_GROUP_ID,GSP_GROUP_NAME from gft_voicenap_group_emp_dtl ".
			" INNER JOIN gft_voicenap_group ON(GVGED_GROUP_ID=GVG_GROUP_ID AND GVG_STATUS='A') ".
			" INNER JOIN gft_support_product_group ON(GVG_SUPPORT_GROUP=GSP_GROUP_ID AND GSP_STATUS='A') ".
			" where GVGED_EMPLOYEE='$emp_id' GROUP BY GSP_GROUP_ID order by GVGED_PRIMARY_GROUP desc";
	$result  = execute_my_query($sql_query);
	while($row=mysqli_fetch_array($result)){
		$support_group['id']=$row['GSP_GROUP_ID'];
		$support_group['name']=$row['GSP_GROUP_NAME'];
		$return_array[] = $support_group;
	}
	return $return_array;
}

/**
 * @param string $pcode
 * @param string $skew_group
 * @param string $local_user
 * @param string $dft_order_no
 * 
 * @return mixed[string][int]
 */
function addon_mapping($pcode,$skew_group,$local_user='N',$dft_order_no=''){
	$addonObj = new addonActivation();
	$addonObj->PCODE=$pcode;
	$addonObj->SKEW_ADDON=$skew_group;
	$addonObj->LOCAL_USER=$local_user;
	if($dft_order_no!=''){
		$addonObj->ORDER_NO = substr($dft_order_no, 0,15);
		$addonObj->FULLFILLMENT_NO = substr($dft_order_no, 15,4);
	}
	$addonObj->get_addon_products();
	return $addonObj->ADD_PRODUCT_DETAILS;
}

addAccessControlAllowOrigin();
$auth_request_json=(isset($_REQUEST['auth'])?(string)$_REQUEST['auth']:'');
$mode = isset($_REQUEST['mode'])?(string)$_REQUEST['mode']:'';
if(empty($auth_request_json)){
	sendError("E100","Invalid params",$mode);
	exit;
}
if($mode=='offline'){
	$auth_request_json = lic_decrypt($auth_request_json, $secret);
	$auth_request_json = preg_replace('/[\x01-\x08]/u', '', $auth_request_json);
}
$auth_request_json = preg_replace("'\r?\n'"," ", $auth_request_json);
$auth_request=/*. (string[string]) .*/json_decode($auth_request_json,true);

$username=(isset($auth_request['username'])?$auth_request['username']:'');
$password=(isset($auth_request['password'])?$auth_request['password']:'');
$reqd_otp = (isset($auth_request['otp_required'])?$auth_request['otp_required']:'0');
$order_no=(isset($auth_request['order_no'])?$auth_request['order_no']:'');
$product_code=(isset($auth_request['product_code'])?$auth_request['product_code']:'');
$product_group=isset($auth_request['product_group'])?$auth_request['product_group']:''; 
$fullfillment_no=(isset($auth_request['fullfillment_no'])?(int)$auth_request['fullfillment_no']:0);
$change_reason = isset($auth_request['Reason'])?$auth_request['Reason']:'';
$requested_by  = isset($auth_request['Requestedby'])?$auth_request['Requestedby']:'';
$compile_type  = isset($auth_request['compile_type'])?strtoupper($auth_request['compile_type']):'';
$random_keyval = isset($auth_request['random_val'])?$auth_request['random_val']:'';
$app_version   = isset($auth_request['app_version'])?$auth_request['app_version']:'';
$uuid		   = isset($auth_request['uuid'])?$auth_request['uuid']:'';
$authorize_for = isset($auth_request['authorize_for'])?(int)$auth_request['authorize_for']:0;
callFloodProtection("auth_service_login_$product_code");
$ip_address=$_SERVER['REMOTE_ADDR'];
$error_message_array=/*. (string[string]) .*/array();
$error_message_array['E000']='Username or Password should not be empty';
$error_message_array['E001']="Username or Password Mismatch";
$error_message_array['E002']="Not able to identify the Customer";
$error_message_array['E003']="ASA  Expired";
$error_message_array['E004']="Subscription Expired";
$error_message_array['E005']="No privilege to access Admin Panel";
$error_message_array['E006']="No permission to access this Customer";
$error_message_array['E007']="Product is in uninstall status in CRM ";
$error_message_array['E009']="You are not authorized to use service user login in this product ";
$error_message_array['E010']="You are not authorized for this";
$error_message_array['C001']="You have beeen using G-Business App in another device. So if you want to continue in this device please authenticate yourself by OTP Verification";
$lead_code='';
$install_id='';
$group_id=0;
$group_name='Employee';
$return_array=/*. (string[string]) .*/ array();
$authenticated='N';
$auth_gr_service_auth=get_group_under_privilage('3');
//$remove_auth_gr_service_auth=get_group_under_privilage('4');
if($username=='' || $password==''){
	//$return_array['error_code']='E000';
	sendError("E000","Username or password should not be empty",$mode);
	exit;
}

	$emp_id=auth_user_webservice($username,$password);
	if($emp_id==''){
		//$return_array['error_code']='E001';
		sendError("E001","Username or password mismatch",$mode);
		exit;
	}
	$skip_install_dtl_check = false;
	$employee_status = 'A';
	if( ($change_reason!='') && ($requested_by!='') ){  //OrderNo Edit in POS. Should get purpose for it
		$skip_install_dtl_check 	= true;
		$employee_status	=	'';
	}else if(is_dft_order($order_no, $fullfillment_no)){
		$skip_install_dtl_check = true;
	}
	$local_user='';
	if(is_authorized_group_list($emp_id,$auth_gr_service_auth)){
		$local_user = check_for_employee_mbile_and_id($order_no,$employee_status);
		if(empty($order_no) and empty($fullfillment_no)){  // purpose of this condition ?
			$authenticated='Y';
			if(!is_service_user_login_allowed($emp_id,$product_code,$product_group)){
				$authenticated='N';
				$return_array['error_code']='E009';
			}
		}else if( ($local_user!='') || ($skip_install_dtl_check) || (($product_code=='120') && ($mode=='dft')) ){
			$authenticated='Y';
		}else {
			if($compile_type=='MP'){
				$reference_no = $order_no.substr("0000".$fullfillment_no, -4);
				$query_check = "select GML_PARTNER_ID,GML_VALIDITY_DATE from gft_mp_license_dtl where GML_REFERENCE_NO='$reference_no' ";
				$result_check = execute_my_query($query_check);
				if($row1 = mysqli_fetch_array($result_check)){
					$employee_dtl_arr	= get_emp_master($emp_id,'',null,false);
					$actual_partner_id	= ($employee_dtl_arr[0][12]!='0')?$employee_dtl_arr[0][12]:$employee_dtl_arr[0][0];
					if($row1['GML_PARTNER_ID']!=$actual_partner_id){
						$return_array['error_code']='E006';
					}elseif ( strtotime($row1['GML_VALIDITY_DATE']) < strtotime(date('Y-m-d'))) {
						$return_array['error_code']='E003';
					}else {
						$authenticated 	= 'Y';
						$local_user		= $actual_partner_id;
					}
				}else{
					$return_array['error_code']='E002';
				}
			}else{
				$query_exists_order="select GID_INSTALL_ID,GID_LEAD_CODE,GID_EXPIRE_FOR,GID_VALIDITY_DATE,if(GID_VALIDITY_DATE < date(now()),'Y','N') 'expired' ," .
						" GID_STATUS,GID_LIC_PCODE,substr(GID_LIC_PSKEW,1,4) as lic_pgroup " .
						" from gft_install_dtl_new " .
						" join gft_product_family_master pfm on (pfm.GPM_PRODUCT_CODE=GID_LIC_PCODE) " .
						" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GID_LIC_PCODE and pm.GPM_PRODUCT_SKEW=GID_LIC_PSKEW ) " .
						" join gft_product_type_master ptm on (GPT_TYPE_ID=pm.GPM_PRODUCT_TYPE and GPT_CONSIDER_AS_PRODUCT_LICENSE='Y') ".
						" where ( (GID_ORDER_NO='$order_no' and GID_FULLFILLMENT_NO=$fullfillment_no) " .
						" or (GID_LIC_ORDER_NO='$order_no' and GID_LIC_FULLFILLMENT_NO=$fullfillment_no ) )" .
						" and (GPM_HEAD_FAMILY ='$product_code' or pm.GPM_PRODUCT_CODE='$product_code' or pm.GPM_PRODUCT_CODE=120) ". //To support usage of DFT Order No in POS, Product code 120 condition included
						" order by GID_STATUS ";  
				$result_exist_order=execute_my_query($query_exists_order);
				if($result_exist_order){
					if(mysqli_num_rows($result_exist_order)>0){
						$qdord=mysqli_fetch_array($result_exist_order);
						$lead_code=$qdord['GID_LEAD_CODE'];
						$install_id=$qdord['GID_INSTALL_ID'];
						$lic_pcode 	= $qdord['GID_LIC_PCODE'];
						$lic_pgroup = $qdord['lic_pgroup'];
						if($qdord['GID_STATUS']==='U'){
							$return_array['error_code']='E007';
						}else if($qdord['expired']==='Y'){
							$return_array['error_code']=(($qdord['GID_EXPIRE_FOR']=='1' or $qdord['GID_EXPIRE_FOR']=='3' )?'E003':'E004');
						}else {
							$authenticated='Y';
							if(!is_service_user_login_allowed($emp_id,$lic_pcode,$lic_pgroup)){
								$authenticated='N';
								$return_array['error_code']='E009';
							}
						}
					}else{
						$return_array['error_code']='E002';
					}
				}		
			}
		}		
	}else{
		$return_array['error_code']='E005';
	}



if($authenticated==='Y'){
    if($authorize_for > 0){
        if(!is_user_authorized($emp_id,$product_code,$product_group,$authorize_for)){
            $authenticated='N';
            $return_array['error_code']='E010';
        }
    }
	if($reqd_otp=='1') {
		$otp = generate_OTP();
		$noti_content_config = array();
		$noti_content_config['Employee_Name'] = array(get_emp_name($emp_id));
		$noti_content_config['OTP'] = array($otp);
		send_formatted_notification_content($noti_content_config, 85, 54, 1, $emp_id);
		execute_my_query(" update gft_app_otp_dtl set GAO_OTP_STATUS='0' where GAO_REF_USERNAME='$username' ");
		$otp_ins = execute_my_query(" insert into gft_app_otp_dtl (GAO_REF_USERNAME,GAO_OTP,GAO_OTP_STATUS,GAO_GENERATED_DATE) values ".
						 " ('$username','$otp','1',now()) ");
	}
	$return_array['status']='Sucess';
	if(is_authorized_group_list($emp_id,array(13,29,30)) ){
		$group_id=13;
		$group_name='Partner';
	}else if(is_authorized_group_list($emp_id,array(14))){
	    $group_id=14;
	    $group_name='Corporate Customer';
	}
	
	if($lead_code!='' and $group_id==13){
					$query_corp_cust_auth="select glh_lead_code from gft_leadcode_emp_map " .
					   " join gft_lead_hdr lh on (glh_reference_of_partner=GLEM_LEADCODE)  " .
					   " where GLEM_EMP_ID=$emp_id and glh_lead_code=$lead_code ";
					$result_corp_cust_auth=execute_my_query($query_corp_cust_auth);	   
					if(mysqli_num_rows($result_corp_cust_auth)==0){
						$lfd_query =" select GLH_LEAD_CODE from gft_lead_hdr join gft_cp_info on (CGI_EMP_ID=GLH_LFD_EMP_ID) ".
									" join gft_leadcode_emp_map on (CGI_LEAD_CODE=GLEM_LEADCODE) ".
									" where GLH_LEAD_CODE='$lead_code' and GLEM_EMP_ID='$emp_id' ";
						$lfd_check = execute_my_query($lfd_query);
						if( (mysqli_num_rows($lfd_check)==0) ){
							$self_check = execute_my_query("select CGI_EMP_ID from gft_cp_info join gft_leadcode_emp_map on (CGI_LEAD_CODE=GLEM_LEADCODE) where GLEM_LEADCODE='$lead_code' and GLEM_EMP_ID='$emp_id' ");
							if(mysqli_num_rows($self_check)==0){
								$authenticated='N';
								$return_array['error_code']='E006';
							}
						}
					}
	}else if($group_id==14){
		    $query_corp_auth="select * from gft_leadcode_emp_map " .
					   "join gft_emp_master em on (gem_emp_id=GLEM_EMP_ID)  " .
					   "Join gft_customer_contact_dtl lmap ON (GCC_LEAD_CODE=GLEM_LEADCODE and GCC_CONTACT_NO=GEM_EMAIL AND GCC_CONTACT_TYPE in (1,3,4) ) " .
					   "where GLEM_EMP_ID=$emp_id  ";
				$result_corp_auth=execute_my_query($query_corp_auth);	   
			if(mysqli_num_rows($result_corp_auth)==0){
						$authenticated='N';	
						$return_array['error_code']='E005';	
			}
		    if($lead_code!=''){
					$query_corp_cust_auth="select * from gft_leadcode_emp_map " .
					   " join gft_lead_hdr lh on (glh_lead_sourcecode=7 and glh_reference_given=GLEM_LEADCODE )  " .
					   " where glh_lead_type in (3,13) and GLEM_EMP_ID=$emp_id  and glh_lead_code=$lead_code ";
					$result_corp_cust_auth=execute_my_query($query_corp_cust_auth);	   
					if(mysqli_num_rows($result_corp_cust_auth)==0){
						$authenticated='N';	
						$return_array['error_code']='E006';
					}
			}  		    
		    /* contain to check its itstaff or Admin */
		  
	}else if($emp_id!=''){
		foreach ($auth_gr_service_auth as $key=>$value){
			if($group_id==0  and is_authorized_group_list($emp_id,array($value))){
				$group_id=$value;			
			}
		}
	}
}
if($mode=='dft'){
	$ver_obj		= new licenseClass();
	if($app_version==""){
		$check_version	= $ver_obj->check_above_minimum_version($product_code,'0.0.0.0','01.0','highest');
		$return_array['error_code']='E008';
		$error_message_array['E008']="You are using very older version of G-Business App. Minimum version required is $ver_obj->Minimum_Version. So please update the latest version and try";
	}else{
		$check_version	= $ver_obj->check_above_minimum_version($product_code,$app_version,'01.0','highest');
		if(!$check_version){
			$return_array['error_code']='E008';
			$error_message_array['E008']="Minimum version required is ".$ver_obj->Minimum_Version.", but yours is $app_version. So please update the latest version and try";
		}
	}
	if($uuid!=''){
		$old_uuid = get_single_value_from_single_table("GEM_DFT_UUID", "gft_emp_master", "GEM_EMP_ID", $emp_id);
		if($old_uuid==""){
			execute_my_query("update gft_emp_master set GEM_DFT_UUID='$uuid' where GEM_EMP_ID='$emp_id'");
		}else if($old_uuid!=$uuid){
			$return_array['error_code'] = 'C001';
		}		
	}
}
if(isset($return_array['error_code']) and $return_array['error_code']!=''){
	$return_array['status']=($return_array['error_code']=='C001')?'conflict':'Failure';
	$return_array['error_message']=$error_message_array[$return_array['error_code']];
	
	$offline_resp['status'] = "error";
	$offline_resp['message'] = $error_message_array[$return_array['error_code']];	
}else{
	$return_array['user_type']	= $group_name;
	$return_array['user_id']	= $emp_id;
	$return_array['emp_name']	= get_emp_name($emp_id);
	if(strtolower($compile_type)=='chatbot'){
		$return_array['support_groups']	= get_employee_support_group($emp_id);
	}
	if($mode=='dft'){
		$dft_order_no 	= "";
		$alert_id 		= "";
		$whatsnow_registered = "NO";
		$sql1 = " select ifnull(CGI_LEAD_CODE,GEM_LEAD_CODE) as lead_code,GEM_MOBILE,GEM_EMAIL,GPE_EMAIL_ID,CGI_LEAD_CODE,GNU_MOBILE ".
				" from gft_emp_master left join gft_cp_info on (CGI_EMP_ID=GEM_EMP_ID) ".
				" left join gft_product_email_mapping on (ifnull(CGI_LEAD_CODE,GEM_LEAD_CODE)=GPE_LEAD_CODE and GPE_PRODUCT_CODE=604) ".
				" left join gft_notification_users on (GNU_MOBILE=GEM_MOBILE and GNU_APP_PCODE='703') ".
				" where GEM_EMP_ID='$emp_id' ";
		$res1 = execute_my_query($sql1);
		if($data1 = mysqli_fetch_array($res1)){
			$emp_lead_code	= $data1['lead_code'];
			if( ($emp_lead_code!="") && ($emp_lead_code!="0") ){
			$emp_email_id	= $data1['GEM_EMAIL'];
			$alert_id 		= $data1['GPE_EMAIL_ID'];
			$chk_que = " select GID_INSTALL_ID from gft_install_dtl_new ".
						" where GID_LEAD_CODE='$emp_lead_code' and GID_LIC_PCODE='604' and GID_STATUS!='U' ";
			$chk_res = execute_my_query($chk_que);
			if(mysqli_num_rows($chk_res)==0){ //register for alert id
				$alert_pcode 	= '604';
				$alert_pskew 	= '05.0RCF30';
				$order_full_no 	= check_and_place_order($alert_pcode,$alert_pskew,'y',$emp_lead_code,'05.0SL','',true);
				$alert_order_no = substr($order_full_no, 0,15);
				update_install_dtl_alert($emp_lead_code,$alert_pcode,$alert_pskew,$alert_order_no,false);
				$reg_resp = alert_new_Register($emp_lead_code,$alert_order_no,$alert_pcode,'',$emp_email_id);
				$resp_arr = json_decode($reg_resp,true);
				$request_id = isset($resp_arr['request_id'])?(int)$resp_arr['request_id']:0;
				$alert_response = get_single_value_from_single_table("GRD_RESPONSE", "gft_smsgareway_request", "GSR_REQUEST_ID", $request_id);
				$json_arr = json_decode($alert_response,true);
				$alert_id = isset($json_arr['email_id'])?$json_arr['email_id']:"";
				if($alert_id!=''){
					save_and_post_email_id($alert_id,array($emp_lead_code),604);
				}
			}
			$dft_install_id = "";
			$que1 = " select GOP_ORDER_NO,GOP_FULLFILLMENT_NO,GID_INSTALL_ID from gft_order_product_dtl ".
					" join gft_order_hdr on (GOP_ORDER_NO=GOD_ORDER_NO) ".
					" left join gft_install_dtl_new on (GID_ORDER_NO=GOP_ORDER_NO and GID_FULLFILLMENT_NO=GOP_FULLFILLMENT_NO and GID_LIC_PCODE=GOP_PRODUCT_CODE and GID_STATUS!='U') ".
					" where GOD_LEAD_CODE='$emp_lead_code' and GOP_PRODUCT_CODE=120 order by GID_INSTALL_DATE desc limit 1";
			$que_res1 = execute_my_query($que1);
			if($que_data1 = mysqli_fetch_array($que_res1)){
				$dft_order_no = $que_data1['GOP_ORDER_NO'].substr("0000".$que_data1['GOP_FULLFILLMENT_NO'], -4);
				$dft_install_id = $que_data1['GID_INSTALL_ID'];
			}else{
				$dft_order_no = check_and_place_order("120", "01.0SLS", 'y', $emp_lead_code);
			}
			if($dft_install_id==""){
			    create_installation_entry(substr($dft_order_no,0,15),substr($dft_order_no,15,4));
			}
			}
			$return_array['user_mobile'] = $data1['GEM_MOBILE'];
			$return_array['emp_lead_code'] = $emp_lead_code;
			if($data1['GNU_MOBILE']!=''){
				$whatsnow_registered = "YES";
			}
		}
		$open_db_id = $open_session_id = null;
		$db_que = " select GDT_ID,GDT_DEMO_DBID from gft_demo_tracking where GDT_EMP_ID='$emp_id' and GDT_STATUS='1' and GDT_DEMO_DBID!=0 order by GDT_ID desc limit 1";
		$db_res = execute_my_query($db_que);
		if($db_row = mysqli_fetch_array($db_res)){
			$open_db_id 		= $db_row['GDT_DEMO_DBID'];
			$open_session_id	= $db_row['GDT_ID'];
		}
		$today_date = date('Y-m-d');
		$appt_que = " select concat(GLD_ACTIVITY_ID,'-1') as rem_id,GLH_LEAD_CODE,GLH_CUST_NAME,GAM_ACTIVITY_DESC, ".
				" GLD_NOTE_ON_ACTIVITY as descrip from gft_activity join gft_lead_hdr on (GLH_LEAD_CODE=GLD_LEAD_CODE) ".
				" join gft_activity_master on (GLD_NEXT_ACTION=GAM_ACTIVITY_ID) and GLD_NEXT_ACTION in (2,48) ".
				" and GLD_EMP_ID='$emp_id' and GLD_NEXT_ACTION_DATE='$today_date' and GLD_SCHEDULE_STATUS in (1,3) ";
		$foll_que = " select concat(GCF_FOLLOWUP_ID,'-2') as rem_id,GLH_LEAD_CODE,GLH_CUST_NAME,GAM_ACTIVITY_DESC, ".
				" GCF_FOLLOWUP_DETAIL as descrip from gft_cplead_followup_dtl join gft_lead_hdr on (GLH_LEAD_CODE=GCF_LEAD_CODE) ".
				" join gft_activity_master on (GCF_FOLLOWUP_ACTION=GAM_ACTIVITY_ID) and GCF_FOLLOWUP_ACTION in (2,48) ".
				" and GCF_ASSIGN_TO='$emp_id' and GCF_FOLLOWUP_DATE='$today_date' and GCF_FOLLOWUP_STATUS in (1,3) ";
		$rem_que = $appt_que." union all ".$foll_que;
		$rem_res = execute_my_query($rem_que);
		$reminder_arr = /*. (string[int][string]) .*/array();
		$demo_lead_code = array();
		while($row_data = mysqli_fetch_array($rem_res)){
		    $booking_id = (int)get_single_value_from_single_query("GDB_ID", " select GDB_ID from gft_demo_server_booking where GDB_DEMO_TO='".$row_data['GLH_LEAD_CODE']."' and GDB_DEMO_DATE=CURDATE() and GDB_TO_TIME>CURTIME() and GDB_STATUS=1 and GDB_DEMO_BY=$emp_id ");
			$single_arr = /*. (string[string]) .*/array();
			if($booking_id>0){$demo_lead_code[] = $row_data['GLH_LEAD_CODE'];}
			$single_arr['summary'] 		= $row_data['GLH_CUST_NAME']." - ".$row_data['GAM_ACTIVITY_DESC'];
			$single_arr['description'] 	= $row_data['descrip'];
			$single_arr['customerId'] 	= $row_data['GLH_LEAD_CODE'];
			$single_arr['reminderId'] 	= $row_data['rem_id'];
			$single_arr['is_hq_demo'] 	= ($booking_id>0) ? true : false;
			$joint_emp_arr = /*. (string[int]) .*/array();
			$rem_id_arr = explode("-",$row_data['rem_id']);
			if($rem_id_arr[1]=='1'){
				$joint_emp_arr = get_joint_emps_for_next_action((int)$rem_id_arr[0]);
			}
			$single_arr['jointEmployee'] = $joint_emp_arr;
			
			$reminder_arr[] = $single_arr;
		}
		/* HQ demo server bookinng without appointment */
		$whr_que = '';
		$demo_lead_str = implode(",", $demo_lead_code);
		if($demo_lead_str!=''){
		    $whr_que = " and GDB_DEMO_TO not in ($demo_lead_str) "; 
		}
		$demo_que = execute_my_query( " select GLH_CUST_NAME,GLH_CUST_STREETADDR2,GLH_LEAD_CODE from gft_demo_server_booking  ".
		                              " join gft_lead_hdr on (GDB_DEMO_TO=glh_lead_code) ".
		                              " where GDB_DEMO_DATE=CURDATE() and GDB_TO_TIME>CURTIME() and GDB_STATUS=1 and GDB_DEMO_BY=$emp_id  $whr_que ");
		while ($demo_res=mysqli_fetch_assoc($demo_que)){
		    $single_arr = /*. (string[string]) .*/array();
		    $single_arr['summary'] 		= $demo_res['GLH_CUST_NAME'].'-'.$demo_res['GLH_CUST_STREETADDR2'];
		    $single_arr['description'] 	= "HQ demo server booking";
		    $single_arr['customerId'] 	= $demo_res['GLH_LEAD_CODE'];
		    $single_arr['reminderId'] 	= "";
		    $single_arr['is_hq_demo'] 	= true;
		    $single_arr['jointEmployee'] = /*. (string[int]) .*/array();;
		    $reminder_arr[] = $single_arr;
		}
		
		$custom_license_array = get_employee_mapped_custom_license($emp_id);
		$return_array['dft_order_no'] 	= $dft_order_no;
		$return_array['alert_id'] 		= $alert_id;
		$return_array['open_db_id'] 	= $open_db_id;
		$return_array['open_session_id']= $open_session_id;
		$return_array['reminders']		= $reminder_arr;
		$return_array['whatsnow_registered'] = $whatsnow_registered;
		$return_array['rpos6_custom_license'] = $custom_license_array['rpos6'];
		$return_array['rpos7_custom_license'] = $custom_license_array['rpos7'];
		
		$return_array['rpos6_addon']= addon_mapping('500','065PL','Y',$dft_order_no);
		$return_array['rpos7_addon']= addon_mapping('500','070PL','Y',$dft_order_no);
		$return_array['de6_addon'] 	= addon_mapping('200','060PL','Y',$dft_order_no);
	}
	
	$resp_data['label'] = "Secure Key";
	$resp_data['value'] = $random_keyval;
	$offline_resp['status'] = "success";
	$offline_resp['message'][] = $resp_data;
}

$return_array_json=json_encode($return_array);
$table_name='gft_product_service_login_log';
$insert_column=/*. (string[string]) .*/ array();

$insert_column['GPS_REQUEST_JSON']=$auth_request_json;
$insert_column['GPS_LOGIN_ID']=(isset($emp_id)?$emp_id:'');
$insert_column['GPS_LOGIN_GROUP']="$group_id";
$insert_column['GPS_REQUEST_TIME']=date('Y-m-d H:i:s');
$insert_column['GPS_LEAD_CODE']=$lead_code;
if($local_user!=''){
	$insert_column['GPS_LEAD_CODE']=$local_user;	
}
$insert_column['GPS_INSTALL_ID']=$install_id;
$insert_column['GPS_PRODUCT_CODE']=$product_code;
$insert_column['GPS_RESPONSE_JSON']=$return_array_json;
$insert_column['GPS_IP_ADDRESS']=$ip_address;
$insert_column['GPS_LOGIN_STATUS']=($return_array['status']=='Sucess'?'Y':'N');
$insert_column['GPS_ERROR_CODE']=(isset($return_array['error_code'])?$return_array['error_code']:'');
$insert_column['GPS_ERROR_MESSAGE']=(isset($return_array['error_message'])?$return_array['error_message']:'');
$insert_column['GPS_REASON']		= $change_reason;
$insert_column['GPS_REQUESTED_BY']	= $requested_by;
array_update_tables_common($insert_column, $table_name, null, null, SALES_DUMMY_ID,null,null,$insert_column);
if($local_user!=''){
	insert_web_request_log($local_user,$product_code,'10',$return_array);
}else{
	insert_web_request_log($lead_code,$product_code,'10',$return_array);
}
if($mode=='offline'){
   if(isset($_GET['callback'])){
	echo $_GET['callback']. '(' . json_encode($offline_resp) . ');';
   }else{
	echo json_encode($offline_resp);	
   }
}else{
   if(isset($_GET['callback'])){
	echo $_GET['callback']. '(' . $return_array_json . ');';
   }else{
	echo $return_array_json;
   }
}
?>
