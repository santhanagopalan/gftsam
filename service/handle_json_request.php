<?php
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__.'/../log.php');
require_once(__DIR__.'/../connectplus/connectplus_util.php');
require_once(__DIR__ ."/../push_notification/push_notification_util.php");
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$request_body	=	file_get_contents("php://input");
$json_request	=	/*. (string[string]) .*/json_decode($request_body,true);
$return_array	=	/*. (string[string]) .*/array();
$get_purpose	=	isset($_GET['purpose'])?(string)$_GET['purpose']:'';
$emp_code		=	isset($_GET['emp_code'])?(int)$_GET['emp_code']:0;
$cust_name      =   isset($_GET['cust_name'])?(string)$_GET['cust_name']:'';
$array_count	= 	(is_array($json_request)) ?  count($json_request) : 0;
$log->logInfo("Request Body - ".$request_body);
if( ($array_count==0) && ($get_purpose=="") ){
	$error['message']="Request Body is invalid";
	send_failure_response($error,HttpStatusCode::BAD_REQUEST);
	exit;
}

$purpose	= isset($json_request['purpose'])?$json_request['purpose']:'';
$pcode		= isset($json_request['productCode'])?(int)$json_request['productCode']:0;
$pgroup		= isset($json_request['productGroup'])?(string)$json_request['productGroup']:'';
$install_id	= isset($json_request['installId'])?(int)$json_request['installId']:0;
$is_live	= isset($json_request['isLive'])?(int)$json_request['isLive']:1;
$mobile		= isset($json_request['mobile'])?$json_request['mobile']:'';
$email		= isset($json_request['email'])?$json_request['email']:'';
$customerId	= isset($json_request['customerId'])?(int)$json_request['customerId']:0;
$orderNo	= isset($json_request['orderNo'])?(string)$json_request['orderNo']:'';
$empId		= isset($json_request['employeeId'])?(int)$json_request['employeeId']:0;
$ordered_cust_id = isset($json_request['orderedCustomerId'])?$json_request['orderedCustomerId']:'';
$productId         = isset($json_request['productId'])?$json_request['productId']:'';
$customerDetails = isset($json_request['customerDetails'])?$json_request['customerDetails']:'';

$identity			= isset($json_request['identity'])?$json_request['identity']:'';
$eIdentity			= isset($json_request['eIdentity'])?$json_request['eIdentity']:'';
$fromVersion		= isset($json_request['fromVersion'])?$json_request['fromVersion']:'';
$toVersion			= isset($json_request['toVersion'])?$json_request['toVersion']:'';
$addOnProductCode	= isset($json_request['addOnProductCode'])?$json_request['addOnProductCode']:'';
$appliedTime 		= isset($json_request['appliedTime'])?$json_request['appliedTime']:date('Y-m-d H:i:s');
$remarks			= isset($json_request['remarks'])?$json_request['remarks']:'';
$updateStatus		= isset($json_request['updateStatus'])?$json_request['updateStatus']:'S'; //to support backward compatability default status updated as 'S'
$db_password		= isset($json_request['password'])?$json_request['password']:'';

$cert_id 		= isset($json_request['cert_id'])?$json_request['cert_id']:'';
$cert_status 	= isset($json_request['cert_status'])?$json_request['cert_status']:'';
$comments 		= isset($json_request['comments'])?$json_request['comments']:'';
$by_employee	= isset($json_request['by_employee'])?$json_request['by_employee']:'';
$vertical_list	= isset($json_request['vertical_list'])?$json_request['vertical_list']:array();

$policy_name = isset($json_request['policy_name'])?$json_request['policy_name']:'';
$policy_status = isset($json_request['policy_status'])?$json_request['policy_status']:1;
$policy_group = isset($json_request['policy_group'])?$json_request['policy_group']:'';
$policy_link = isset($json_request['policy_link'])?$json_request['policy_link']:'';
$policy_id =  isset($json_request['policy_id'])?$json_request['policy_id']:'';

$mantis_data_arr		= /*. (string[int][string]) .*/array();
if(isset($json_request['data'])){
	$mantis_data_arr = $json_request['data'];	
}
$replace_existing = isset($json_request['replace_existing'])?(int)$json_request['replace_existing']:0;

if($purpose=='mark_as_live'){
	if(in_array($pcode,array(520,521))){
		$pcode = 520;
	}
	if( ($pcode > 0) && ($install_id > 0) ){
		$que1 = " replace into gft_app_live_customer (GAL_APP_PCODE,GAL_INSTALL_ID,GAL_IS_LIVE,GAL_UPDATED_DATE) ".
				" values ('$pcode','$install_id','$is_live',now()) ";
		execute_my_query($que1);
	}else{
		$error['message']=" productCode or installId is invalid ";
		send_failure_response($error,HttpStatusCode::BAD_REQUEST);
		exit;
	}
	$return_array['message'] = "Updated Successfully";
}else if($purpose=='notification_service_migration'){
	$ns_data = process_notification_registration($pcode,$mobile,$email);
	$return_array['user_auth_token'] = isset($ns_data['user_auth_token'])?$ns_data['user_auth_token']:'';
}else if($purpose=="get_last_otp"){
	$sql1 = "select GPR_OTP_CODE,GPR_CREATED_DATE from gft_presignup_registration where GPR_OTP_MOBILENO='$mobile' and GPR_PCODE='$pcode' ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$otp_code 		= $row1['GPR_OTP_CODE'];
		$otp_timestamp 	= $row1['GPR_CREATED_DATE'];
		$return_array['otp'] 			= $otp_code;
		$return_array['otpTimestamp'] 	= $otp_timestamp;
	}else{
		$error['message'] = "OTP not generated for this product with the given mobile number";
		send_failure_response($error, HttpStatusCode::BAD_REQUEST);
		exit;
	}
	
}else if($purpose=="activate_trial"){
	$chk_lead = (int)get_single_value_from_single_table("GLH_LEAD_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", $customerId);
	if($chk_lead == 0){
		$error['message'] = "Invalid customer id ($customerId) ";
		send_failure_response($error, HttpStatusCode::BAD_REQUEST);
		exit;
	}
	$headers = apache_request_headers();
	$user_auth_token = isset($headers['X-Auth-Token'])?$headers['X-Auth-Token']:'';
	$chk_query = " select GNU_MOBILE,GNU_EMAIL,GNU_APP_PCODE from gft_notification_users where GNU_AUTHTOKEN='$user_auth_token' ";
	$chk_res   = execute_my_query($chk_query);
	$ref_install_id = $ref_user_id = "";
	if($chk_data = mysqli_fetch_array($chk_res)){
		$app_code = $chk_data['GNU_APP_PCODE'];
		$mobile_no_cond = getContactDtlWhereCondition("GCC_CONTACT_NO", $chk_data['GNU_MOBILE']);
		$user_query =" select GPU_INSTALL_ID,GPU_USER_ID from gft_pos_users ".
					 " join gft_customer_contact_dtl on (GCC_ID=GPU_CONTACT_ID) ".
					 " join gft_app_users on (GPU_INSTALL_ID=GAU_INSTALL_ID and GAU_CONTACT_ID=GPU_CONTACT_ID) ".
					 " where GAU_APP_PCODE='$app_code' and $mobile_no_cond ";
		$user_res = execute_my_query($user_query);
		if($user_data = mysqli_fetch_array($user_res)){
			$ref_install_id = $user_data['GPU_INSTALL_ID'];
			$ref_user_id 	= $user_data['GPU_USER_ID'];
		}
	}else{
		$error['message'] = "Invalid User Auth Token ($user_auth_token) ";
		send_failure_response($error, HttpStatusCode::BAD_REQUEST);
		exit;
	}
	$pskew = return_trial_skew($pcode);
	if($pskew==''){
		$error['message']="Trial Skew Not Available";
		send_failure_response($error,HttpStatusCode::BAD_REQUEST);
		exit;
	}
	if(is_product_installed($customerId,$pcode)){
		$error['message']="Already Product Installed";
		send_failure_response($error,HttpStatusCode::BAD_REQUEST);
		exit;
	}
	$order_fullfill_no = check_and_place_order($pcode, $pskew, 'y', $customerId);
	$order_no = substr($order_fullfill_no, 0,15);
	//update_install_dtl_alert($customerId, $pcode, $pskew, $order_no,false,false);
	provision_connectplus($order_no);
	if( ($ref_install_id!="") && ($ref_user_id!="") ){
		//update_app_user($ref_install_id,$ref_user_id,$pcode,'1');
	}
	$return_array['message'] = "Trial Registered successfully";
}else if($purpose=='customer_contacts'){
	$chk_emp_id = (int)get_single_value_from_single_table("GEM_EMP_ID", "gft_emp_master", "GEM_EMP_ID", $empId);
	if($chk_emp_id==0){
		$err_msg = "Invalid Employee Id";
		send_response_in_json_with_file_log("failure",$err_msg);
		exit;
	}
	$chk_lead = (int)get_single_value_from_single_table("GLH_LEAD_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", $customerId);
	if($chk_lead > 0){
		$acccess_condition = get_customer_accessible_condition($empId,"and glh_lead_code='$chk_lead'");
		$contact_query =" select GLH_CUST_NAME,GLH_STATUS,GLH_PROSPECTS_STATUS,GCC_CONTACT_NAME,GCC_CONTACT_NO from gft_customer_contact_dtl ".
						" join gft_lead_hdr on (GLH_LEAD_CODE=GCC_LEAD_CODE) ".
						" join gft_lead_hdr_ext on (GLH_LEAD_CODE=GLE_LEAD_CODE) ".
						" left join gft_pos_users on (GPU_CONTACT_ID=GCC_ID) ".
						" where GCC_LEAD_CODE='$customerId' and GCC_CONTACT_TYPE in (1,2) and if(GPU_CONTACT_ID is null,1,GPU_CONTACT_STATUS='A') $acccess_condition ";
		$contact_query_res = execute_my_query($contact_query);
		if(mysqli_num_rows($contact_query_res)==0){
			$err_msg = "You are not the lead owner for this Customer Id, So you can't access it. Please contact Sales / Partner Management team to get the access";
			send_response_in_json_with_file_log("failure",$err_msg);
			exit;
		}else{
			$contact_arr = /*. (string[int][string]) .*/array();
			$joint_employee_list = $next_action_list = $lead_status_master = $prospect_status_master = /*. (string[int][string]) .*/array();
			$cust_name = $lead_status = $prospect_status = "";
			while ($row_contact = mysqli_fetch_array($contact_query_res)){
				$cust_name = $row_contact['GLH_CUST_NAME'];
				$lead_status 	 = $row_contact['GLH_STATUS'];
				$prospect_status = $row_contact['GLH_PROSPECTS_STATUS'];
				$temp_arr['name'] 	= $row_contact['GCC_CONTACT_NAME'];
				$temp_arr['mobile'] = $row_contact['GCC_CONTACT_NO'];
				$contact_arr[] = $temp_arr;				
			}
			$joint_employee_arr = get_salesperson_name($empId,true);
			foreach ($joint_employee_arr as $val){
				$field_arr = /*. (string[string]) .*/array();
				$field_arr['id'] 		= $val[0];
				$field_arr['name'] 		= $val[1];
				$field_arr['type']		= ((int)$val[0] < 7000)?"gft":"partner";
				$joint_employee_list[] 	= $field_arr;
			}
			$activity_list = get_activity_list_with_group(null,true);
			foreach ($activity_list as $act_val){
				$field_arr = /*. (string[string]) .*/array();
				$field_arr['id'] 		= $act_val[0];
				$field_arr['name'] 		= $act_val[1];
				$field_arr['type']		= $act_val[2];
				$next_action_list[] 	= $field_arr;
			}
			$sql_que1 = " select GCS_CODE,GCS_NAME,GCS_CAN_CHANGE_MANUALLY,GCS_STATUS_CHANGE_ACTIVITY from gft_customer_status_master where GCS_STATUS='A'  order by GCS_ORDER_BY ";
			$sql_res1 = execute_my_query($sql_que1);
			while ($sql_row1 = mysqli_fetch_array($sql_res1)){
				$gcs_code = $sql_row1['GCS_CODE'];
				$sub_status_master = $sub_status_label = "";
				if($gcs_code=='3'){
					$sub_status_master = "prospect_status_master";
					$sub_status_label = "Prospect Status";
				}
				$field_arr = /*. (string[string]) .*/array();
				$field_arr['id'] 		= $gcs_code;
				$field_arr['name'] 		= $sql_row1['GCS_NAME'];
				$field_arr['can_change']= $sql_row1['GCS_CAN_CHANGE_MANUALLY'];
				$field_arr['default_change'] = is_authorized_group_list($empId, array(27))?"Y":"N";
				if($sql_row1['GCS_STATUS_CHANGE_ACTIVITY']=='N'){
					$field_arr['default_change'] = "N";
				}
				$field_arr['sub_status_master']	= $sub_status_master;
				$field_arr['sub_status_label']	= $sub_status_label;
				$lead_status_master[] = $field_arr;
			}
			$prop_que = " select GPS_STATUS_ID,GPS_STATUS_NAME from gft_prospects_status_master where GPS_STATUS='A' and GPS_LEAD_TYPE=1 ";
			$prop_res = execute_my_query($prop_que);
			while ($prop_row = mysqli_fetch_array($prop_res)){
				$field_arr = /*. (string[string]) .*/array();
				$field_arr['id'] 		= $prop_row['GPS_STATUS_ID'];
				$field_arr['name'] 		= $prop_row['GPS_STATUS_NAME'];
				$field_arr['sub_status_master']	= "";
				$field_arr['sub_status_label']	= "";
				$prospect_status_master[] = $field_arr;
			}
			$return_array['status']			= "success";
			$return_array['customerName'] 	= $cust_name;
			$return_array['lead_status'] 	= $lead_status;
			$return_array['prospect_status']= $prospect_status;
			$return_array['CONTACTS'] 		= $contact_arr;
			$return_array['jointEmployeeList'] 	= $joint_employee_list;
			$return_array['nextActionList'] 	= $next_action_list;
			$return_array['lead_status_master'] = $lead_status_master;
			$return_array['prospect_status_master'] = $prospect_status_master;
		}
	}else{
		$err_msg = "Customer Id not found in CRM. Please enter a valid customer id";
		send_response_in_json_with_file_log("failure",$err_msg);
		exit;
	}
}else if($get_purpose=='support_group'){
	$sql2 = 	" select GSP_GROUP_ID,GSP_GROUP_NAME,GAC_NAME, GSP_COMPANY_ID from gft_support_product_group".
				" inner join gft_assure_care_company ON(GAC_ID=GSP_COMPANY_ID)".
				" where GSP_STATUS='A'  ";
	//For avoiding to show employee call group for pre-sales and internation sales
	if($emp_code!="" && is_authorized_group_list($emp_code, null, array(31,3))){
		$sql2 .= " AND GSP_GROUP_ID!=13 ";
	}
	$sql2 .=	" order by GSP_COMPANY_ID, GSP_GROUP_NAME ";
	$res2 = execute_my_query($sql2);
	while($row2 = mysqli_fetch_array($res2)){
		$arr = /*. (string[string]) .*/array();
		$arr['id'] 		= $row2['GSP_GROUP_ID'];
		$arr['name'] 	= $row2['GSP_GROUP_NAME'];
		$arr['groupId'] 	= $row2['GSP_COMPANY_ID'];
		$arr['groupName'] 	= $row2['GAC_NAME'];
		$return_array[] = $arr; 
	}
}else if($get_purpose=='hq_domains'){
	$sql3 = " select GID_LEAD_CODE,GID_STORE_URL,GID_CURRENT_VERSION,GID_VALIDITY_DATE from gft_install_dtl_new ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
			" where GID_LIC_PCODE=300 and GID_STATUS!='U' and GPM_LICENSE_TYPE in (1,2) ";
	$res3 = execute_my_query($sql3);
	$today_timestamp = strtotime(date('Y-m-d'));
	while($row3 = mysqli_fetch_array($res3)){
		$arr = /*. (string[string]) .*/array();
		$domain 		= $row3['GID_STORE_URL'];
		$version 		= $row3['GID_CURRENT_VERSION'];
		if(strpos($version, "RC")===false){
			$version = "RC".$version;
		}
		$domain 		= (string)str_replace(array("http://","https://","//RayMedi_HQ","/RayMedi_HQ"), "", $domain);
		$domain_arr = explode(":", $domain); //to remove port number if any
		$domain = $domain_arr[0];
		$arr['domain'] 	= $domain;
		$arr['version'] = $version;
		$arr['customerId'] = (int)$row3['GID_LEAD_CODE'];
		$arr['customerStatus'] = (strtotime($row3['GID_VALIDITY_DATE']) >= $today_timestamp) ? 1 : 0;
		$return_array[] = $arr; 
	}
}else if($purpose=='validateCustomerId'){
	$gid_order_no 	= substr($orderNo,0,15);
	$fullfill_no	= (int)substr($orderNo,15,4);
	$sql1 = " select GLH_CUST_NAME,GLH_CUST_STREETADDR2 from gft_lead_hdr join gft_install_dtl_new on (GLH_LEAD_CODE=GID_LEAD_CODE) ".
			" where GID_LEAD_CODE='$customerId' and GID_ORDER_NO='$gid_order_no' and GID_FULLFILLMENT_NO=$fullfill_no and GID_STATUS='A' ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$return_array['status'] = "success";
		$return_array['customerName'] = $row1['GLH_CUST_NAME']. ' - '.$row1['GLH_CUST_STREETADDR2'];
	}else{
		send_response_in_json_with_file_log("failure","Customer Id ($customerId) and Order Number ($orderNo) doesn't match.");
		exit;
	}
}else if($purpose=='splitProduct'){
    if( is_array($customerDetails) && (count($customerDetails)>0) ){
        foreach ($customerDetails as $single_arr){
            $temp_cust_id  = $single_arr['customerId'];
            $temp_order_no = $single_arr['orderNo'];
            $gid_order_no     = substr($temp_order_no,0,15);
            $fullfill_no    = (int)substr($temp_order_no,15,4);
            $sql1 = " select GLH_CUST_NAME,GLH_CUST_STREETADDR2 from gft_lead_hdr join gft_install_dtl_new on (GLH_LEAD_CODE=GID_LEAD_CODE) ".
                    " where GID_LEAD_CODE='$temp_cust_id' and GID_ORDER_NO='$gid_order_no' and GID_FULLFILLMENT_NO=$fullfill_no and GID_STATUS='A' ";
            $res1 = execute_my_query($sql1);
            if(mysqli_num_rows($res1)==0){
                send_response_in_json_with_file_log("failure","Customer Id ($customerId) and Order Number ($orderNo) doesn't match.");
                exit;
            }        
        }
        $product_id_arr = explode("-", $productId);
        $pcode = $product_id_arr[0];
        $pskew = $product_id_arr[1];
        $tdate = date('Y-m-d H:i:s');
        $success_msg = "";
        foreach ($customerDetails as $single_arr){
            $split_cust_id  = $single_arr['customerId'];
            $ord_que1 = " select GOP_ORDER_NO,GOP_FULLFILLMENT_NO from gft_order_product_dtl join gft_order_hdr on (GOD_ORDER_NO=GOP_ORDER_NO) ".
                    " where GOP_PRODUCT_CODE='$pcode' and GOP_PRODUCT_SKEW='$pskew' and GOD_LEAD_CODE='$ordered_cust_id' and GOD_ORDER_STATUS='A' ".
                    " and god_order_splict=1 and (GOP_QTY - GOP_CP_USEDQTY) > 0 ";
            $ord_res = execute_my_query($ord_que1);
            if($data1 = mysqli_fetch_array($ord_res)){
                $gop_order_no = $data1['GOP_ORDER_NO'];
                $of_no = insert_stmt_for_split_order_dtl($ordered_cust_id, $split_cust_id, 1, $gop_order_no, $pcode, $pskew, $tdate, $tdate, SALES_DUMMY_ID);
                $success_msg .= " (Customer Id - $split_cust_id, Order No - $of_no) ";
            }
        }
        $return_array['status'] = "success";
        $return_array['message'] = $success_msg;
    }
}else if($get_purpose=='customerDetails'){
	$customerId = isset($_REQUEST['customerId'])?(int)$_REQUEST['customerId']:0;
	$productCode= isset($_REQUEST['productCode'])?(int)$_REQUEST['productCode']:0;
	$que1 = " select GLH_VERTICAL_CODE,GLH_COUNTRY,GLH_CUST_NAME,ifnull(GPM_COUNTRY_CODE,'IN') GPM_COUNTRY_CODE,GLE_USER_SYNC_TYPE ".
	   	    " from gft_lead_hdr join gft_lead_hdr_ext on (GLH_LEAD_CODE=GLE_LEAD_CODE) ".
	   	    " left join gft_political_map_master on (GPM_MAP_TYPE='C' and GPM_MAP_NAME=GLH_COUNTRY) ".
	        " where GLH_LEAD_CODE='$customerId' ";
	$res1 = execute_my_query($que1);
	$today_date = date('Y-m-d');
	if($row1 = mysqli_fetch_array($res1)){
	    $customer_identity = "";
		$ins_que =" select GID_LIC_PCODE,substr(GID_LIC_PSKEW,1,4) as pgroup,GID_VALIDITY_DATE,GID_CURRENT_VERSION,GPM_PRODUCT_ABR,gpg_version, ".
		  		  " GID_ORDER_NO,GID_FULLFILLMENT_NO,GID_LIC_PSKEW ".
				  " from gft_install_dtl_new join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=GID_LIC_PCODE) ".
				  " join gft_product_group_master on (gpg_product_family_code=GID_LIC_PCODE and gpg_skew=substr(GID_LIC_PSKEW,1,4)) ".
				  " join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GID_LIC_PCODE and pm.GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
				  " where GPM_IS_BASE_PRODUCT='Y' and GID_LEAD_CODE='$customerId' and GID_STATUS!='U' and if(GPM_LICENSE_TYPE=3,GID_VALIDITY_DATE>='$today_date',1) order by GID_VALIDITY_DATE desc ";
		$res_que = execute_my_query($ins_que);
		$product_arr = /*. (string[int][string]) .*/array();
		while($data1 = mysqli_fetch_array($res_que)){
			$single_arr['productName'] 	= $data1['GPM_PRODUCT_ABR']." ".$data1['gpg_version'];
			$single_arr['version']		= $data1['GID_CURRENT_VERSION'];
			$single_arr['expiryDate'] 	= $data1['GID_VALIDITY_DATE'];
			$single_arr['productCode'] 	= $data1['GID_LIC_PCODE']."-".$data1['pgroup'];
			$product_arr[] = $single_arr;
			if( ($customer_identity=="") && in_array($data1['GID_LIC_PCODE'], array('200','500','300')) ){
			    $customer_identity = $data1['GID_ORDER_NO'].substr("0000".$data1['GID_FULLFILLMENT_NO'], -4).$data1['GID_LIC_PCODE'].substr(str_replace(".","",$data1['GID_LIC_PSKEW']),0,5);
			}
		}
		$prod_validity = null;
		$store_url 	= "";
		$domain 	= "";
		$ins_que 	= " select GID_STORE_URL,GID_INTERNET_PORT from gft_install_dtl_new where GID_LEAD_CODE='$customerId' ".
					  " and GID_STATUS!='U' and GID_STORE_URL!='' order by GID_VALIDITY_DATE desc ";
		$ins_res = execute_my_query($ins_que);
		if($ins_row = mysqli_fetch_array($ins_res)){
			$domain = $ins_row['GID_STORE_URL'];
			$store_url = $domain.":".$ins_row['GID_INTERNET_PORT'];
		}
		if($productCode > 0){
		    $validity = get_single_value_from_single_query("GID_VALIDITY_DATE", "select GID_VALIDITY_DATE from gft_install_dtl_new where GID_LEAD_CODE='$customerId' and GID_LIC_PCODE='$productCode' and GID_STATUS!='U'");
		    if($validity!=''){
		        $prod_validity = gmdate('Y-m-d H:i:s',strtotime($validity." 23:59:59"));
		    }
		}
		$brand_dtl = array();
		if($customer_identity!=""){
		    $prod_id = substr($customer_identity, 19,3)."-".substr($customer_identity, 22,2).".".substr($customer_identity, 24,1);
		    $brand_dtl = get_product_brand_name($prod_id, $row1['GLH_VERTICAL_CODE']);
		}
		$return_array['customerName'] 	= $row1['GLH_CUST_NAME'];
		$return_array['vertical'] 		= $row1['GLH_VERTICAL_CODE'];
		$return_array['country'] 		= $row1['GLH_COUNTRY'];
		$return_array['countryCode']	= $row1['GPM_COUNTRY_CODE'];
		$return_array['productDetails']	= $product_arr;
		$return_array['storeUrl'] 		= $store_url;
		$return_array['domain'] 		= $domain;
		$return_array['identity']       = $customer_identity;
		$return_array['customerType']   = $row1['GLE_USER_SYNC_TYPE'];
		$return_array['isHQOutlet']     = is_hq_outlet($customerId);
		$return_array['brandName']      = isset($brand_dtl[1])?$brand_dtl[1]:"";
		$return_array['mobileNumber']   = get_contact_dtl_for_designation($customerId, 1, '');
		$return_array['email']          = get_contact_dtl_for_designation($customerId, 4, '');
		$return_array['expiryAt']       = $prod_validity;
	}else{
		send_response_in_json_with_file_log("failure"," Invalid Customer Id ($customerId)");
		exit;
	}
}else if($purpose=='patchUpdate'){
	if(strcasecmp(md5($identity), $eIdentity)!=0){
		send_response_in_json_with_file_log("failure"," Mismatch between identity($identity) and eIdentity($eIdentity) ");
		exit;
	}
	$iden_dtl = get_details_from_idendity($identity);
	if(!isset($iden_dtl[0])){
		send_response_in_json_with_file_log("failure"," Invalid identity($identity)");
		exit;
	}
	$insert_arr['GPD_LEAD_CODE'] 		= $iden_dtl[0];
	$insert_arr['GPD_INSTALL_ID_REFF'] 	= $iden_dtl[1];
	$insert_arr['GPD_ORDER_NO'] 		= $iden_dtl[2];
	$insert_arr['GPD_FULLFILLMENT_NO'] 	= $iden_dtl[3];
	$insert_arr['GPD_PRODUCT_CODE'] 	= $iden_dtl[4];
	$insert_arr['GPD_PRODUCT_SKEW'] 	= $iden_dtl[5];
	$insert_arr['GPD_ADDON_PCODE']		= $addOnProductCode;
	$insert_arr['GPD_PATCHRUN_DATETIME']= $appliedTime;
	$insert_arr['GPD_CURRENT_VERSION'] 	= $fromVersion;
	$insert_arr['GPD_UPDATED_VERSION'] 	= $toVersion;
	$insert_arr['GPD_REMARKS'] 			= $remarks;
	$insert_arr['GPD_PATCH_STATUS'] 	= $updateStatus;
	$insert_arr['GPD_RECEIVED_DATETIME']= date('Y-m-d H:i:s');
	$insert_arr['GPD_REMOTE_IPADDR'] 	= isset($_SERVER['REMOTE_ADDR'])?(string)$_SERVER['REMOTE_ADDR']:'';
	$insert_arr['GPD_INSTALL_TYPE'] 	= '1';
	array_insert_query("gft_patch_dtl", $insert_arr);
	$return_array['message'] = "saved";
}else if( ($purpose=='product_analytics') || ($purpose=='menu_analytics') ){
	$analytics_arr = isset($json_request['analytics'])?$json_request['analytics']:array();
	if(count($analytics_arr)==0){
		send_response_in_json_with_file_log("failure","No Analytics Detail found");
		exit;
	}
	if($pcode=='535' && strlen($customerId)<5){
		$return_array['status'] = 'success';
		$return_array['message'] = "Addon analytics is skipped for product id 535, when sending with employee id in lead code param.";
		$resp =  json_encode($return_array);
		echo $resp;
		$log->logInfo("Response - ".$resp);exit;
	}
	$chk_lead = (int)get_single_value_from_single_table("GLH_LEAD_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", $customerId);
	if($chk_lead==0){
		send_response_in_json_with_file_log("failure","Invalid customer id ($customerId) ");
		exit;
	}
	if($purpose=='menu_analytics'){
		$iden_dtl = get_details_from_idendity($identity);
		if(!isset($iden_dtl[1])){
			send_response_in_json_with_file_log("failure"," Invalid identity($identity)");
			exit;
		}
		save_menu_analytics_from_pos($customerId,$pcode,$pgroup,$iden_dtl[1],$analytics_arr);
	}else{
		$mq_res = execute_my_query(" select GPS_ID,GPS_ALIAS from gft_product_analytics_subcategory");
		$mq_arr = /*. (string[string]) .*/array();
		while ($mq_row = mysqli_fetch_array($mq_res)){
			$mq_arr[$mq_row['GPS_ALIAS']] = $mq_row['GPS_ID'];
		}
		foreach ($analytics_arr as $key => $val_arr){
			$category 		= $val_arr['category'];
			$anal_date		= $val_arr['date'];
			$sub_categ_arr 	= isset($val_arr['subcategory'])?$val_arr['subcategory']:array();
			if(isset($val_arr['subvalues'])){
				foreach ($val_arr['subvalues'] as $dtl_arr){
					$label = isset($dtl_arr['label'])?$dtl_arr['label']:'';
					$value = isset($dtl_arr['value'])?$dtl_arr['value']:'';
					if($label!=''){
						$sub_categ_arr[$label] = $value;
						if(!isset($mq_arr[$label])){
							$insert_arr = array(
									'GPS_ALIAS'=>$label,
									'GPS_NAME'=>$label,
									'GPS_STATUS'=>'A'
							);
							$mas_id = array_insert_query("gft_product_analytics_subcategory", $insert_arr);
							if($mas_id!=0){
								$mq_arr[$label] = $mas_id;
							}
						}
					}
				}	
			}
			if(count($sub_categ_arr)==0){
				continue;
			}
			$ins_arr = array('GAH_LEAD_CODE'=>$customerId,'GAH_PRODUCT_CODE'=>$pcode,'GAH_PRODUCT_GROUP'=>$pgroup,
					'GAH_CATEGORY'=>$category,'GAH_ANALYTICS_DATE'=>$anal_date,'GAH_RECEIVED_DATE'=>date('Y-m-d H:i:s'));
			$key_id = (int)array_insert_query("gft_analytics_hdr", $ins_arr);
			if($key_id!=0){
				foreach ($sub_categ_arr as $alias => $val){
					if(isset($mq_arr[$alias])){
						$sid = $mq_arr[$alias];
						$insert_arr = array('GAD_HDR_ID'=>$key_id,'GAD_SUBCATEGORY_ID'=>$sid,'GAD_SUBCATEGORY_VALUE'=>$val);
						$key_arr = array('GAD_HDR_ID'=>$key_id,'GAD_SUBCATEGORY_ID'=>$sid);
						array_update_tables_common($insert_arr, "gft_analytics_dtl", $key_arr, null, SALES_DUMMY_ID,null,null,$insert_arr);
					}
				}
			}
		}
	}
	$return_array['status'] = 'success';
}else if($get_purpose=='support_history'){
	$customerId = isset($_REQUEST['customerId'])?(int)$_REQUEST['customerId']:0;
	$start_time	= isset($_REQUEST['start_time'])?(int)$_REQUEST['start_time']:0;
	$end_time	= isset($_REQUEST['end_time'])?(int)$_REQUEST['end_time']:0;
	$sub_categ	= isset($_REQUEST['complaint_type'])?(string)$_REQUEST['complaint_type']:'';
	$lead_name = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", $customerId);
	if($lead_name==''){
		send_response_in_json_with_file_log("failure"," Invalid Customer Id ($customerId)");
		exit;
	}
	$wh_cond = " and GLH_LEAD_CODE='$customerId' ";
	if(trim($sub_categ)!=''){
		$sub_categ_arr = explode(",", $sub_categ);
		$sub_categ_str = "";
		$put_comma = "";
		foreach ($sub_categ_arr as $va){
			$sub_categ_str .= $put_comma."'".mysqli_real_escape_string_wrapper($va)."'";
			$put_comma = ",";
		}
		if($sub_categ_str!=''){
			$wh_cond .= " and GCD_COMPLAINT_CODE in ($sub_categ_str) ";
		}
	}
	if($start_time!=0){
		$start_date= date('Y-m-d',$start_time);
		$wh_cond .= " and GCD_ACTIVITY_DATE >= '$start_date 00:00:00' ";
	}
	if($end_time!=0){
		$end_date = date('Y-m-d',$end_time);
		$wh_cond .= " and GCD_ACTIVITY_DATE <= '$end_date 23:59:59' ";
	}
	$sql1 = " select GLH_LEAD_CODE,GLH_CUST_NAME,GCD_COMPLAINT_ID,GCD_ACTIVITY_ID,GFT_COMPLAINT_CODE,GFT_COMPLAINT_DESC, ".
			" GCD_PROBLEM_SUMMARY,GCD_PROBLEM_DESC,GCD_ACTIVITY_DATE from gft_lead_hdr ".
			" join gft_customer_support_hdr on (GCH_LEAD_CODE=GLH_LEAD_CODE) ".
			" join gft_customer_support_dtl on (GCD_COMPLAINT_ID=GCH_COMPLAINT_ID) ".
			" join gft_complaint_master on (GFT_COMPLAINT_CODE=GCD_COMPLAINT_CODE) ".
			" where 1 $wh_cond ";
	$res1 = execute_my_query($sql1);
	$dtl_arr = /*. (string[int][string]) .*/array();
	while ($row1 = mysqli_fetch_array($res1)){
		$complaint_id 	= $row1['GCD_COMPLAINT_ID'];
		$activity_id 	= $row1['GCD_ACTIVITY_ID'];
		$dtl_arr[] = array(
					'complaint_id'=>$complaint_id,
					'activity_id'=>$activity_id,
					'activity_date'=>$row1['GCD_ACTIVITY_DATE'],
					'complaint_type'=>$row1['GFT_COMPLAINT_CODE'],
					'complaint_type_name'=>$row1['GFT_COMPLAINT_DESC'],
					'complaint_summary'=>$row1['GCD_PROBLEM_SUMMARY'],
					'complaint_description'=>$row1['GCD_PROBLEM_DESC']
					);
	}
	$return_array['status'] = 'success';
	$return_array['customerId'] = $customerId;
	$return_array['customerName'] = $lead_name;
	$return_array['support_dtl'] = $dtl_arr;
}else if($get_purpose=='password_update'){
	if(strcasecmp(md5($identity), $eIdentity)!=0){
		send_response_in_json_with_file_log("failure"," Mismatch between identity($identity) and eIdentity($eIdentity) ");
		exit;
	}
	$iden_dtl = get_details_from_idendity($identity);
	$install_id = isset($iden_dtl[1])?(int)$iden_dtl[1]:0;
	if($install_id==0){
		send_response_in_json_with_file_log("failure"," Invalid identity($identity)");
		exit;
	}
	if($db_password!=''){
		$decrypt_password = lic_decrypt($db_password, $secret);
		execute_my_query("update gft_install_dtl_new set GID_DB_PASSWORD='".mysqli_real_escape_string_wrapper($decrypt_password)."' where GID_INSTALL_ID='$install_id' ");
		$return_array['status'] = 'success';
	}else{
		send_response_in_json_with_file_log("failure"," password not obtained");
		exit;
	}
}else if($get_purpose=='hq_certificate'){
	$sql1 = " select GEM_EMP_ID,GEC_CERTIFICATE_ID,GEC_STATUS,GEC_COMMENTS,GEC_VERTICAL_CODE ".
			" from gft_emp_certificate_dtl ".
			" join gft_emp_master on (GEM_EMP_ID=GEC_EMP_ID) ".
			" where GEM_STATUS='A' and GEC_CERTIFICATE_ID=1 ";
	$res1 = execute_my_query($sql1);
	$cerf_arr = /*. (string[string][int]) .*/array();
	while ($row1 = mysqli_fetch_array($res1)){
		$cerf_arr[$row1['GEM_EMP_ID']][] = array(
				'id'=>$row1['GEC_CERTIFICATE_ID'],
				'name'=>'HQ Certification',
				'comments'=>$row1['GEC_COMMENTS'],
				'statusId'=>$row1['GEC_STATUS'],
				'statusName'=>($row1['GEC_STATUS']=='1')?'Active':'Inactive',
		        'vertical_list'=>$row1['GEC_VERTICAL_CODE']
		);
	}
	$data_arr = /*. (string[int][string]) .*/array();
	foreach ($cerf_arr as $key_id => $val_arr){
		$que1 = " select GEM_EMP_ID,GEM_EMP_NAME,GEM_PROFILE_URL,GEM_TITLE from gft_emp_master where GEM_EMP_ID='$key_id' ";
		$vertical_code = get_single_value_from_single_query('GEC_VERTICAL_CODE', "select GEC_VERTICAL_CODE from gft_emp_certificate_dtl where GEC_CERTIFICATE_ID=1 and GEC_EMP_ID='$key_id' ");
		$que_res1 = execute_my_query($que1);
		if($que_row1 = mysqli_fetch_array($que_res1)){
			$gem_emp_id 	= $que_row1['GEM_EMP_ID'];
			$gem_emp_name 	= $que_row1['GEM_EMP_NAME'];
			$title          = $que_row1['GEM_TITLE'];
			$gem_profile	= ($que_row1['GEM_PROFILE_URL']=="")?"images/Profile.png":$que_row1['GEM_PROFILE_URL'];
			if($que_row1['GEM_TITLE']==null){
			    $title = 'Partner';
			}
			$temp_arr = array(
					'employeeId'		=> $gem_emp_id,
					'employeeName'		=> $gem_emp_name,
					'employeeImage'		=> $gem_profile,
			        'employeeDesig'		=> $title,
					'certificateDetail'	=> $val_arr,
			        'vertical_list'     => $vertical_code
			);
			$data_arr[] = $temp_arr;
		}
	}
	$return_array['data'] = $data_arr;
}else if($get_purpose=='save_hq_certificate'){
	$sql1 = " select GEC_ID from gft_emp_certificate_dtl where GEC_EMP_ID='$empId' and GEC_CERTIFICATE_ID=1 ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$upd_arr = array(
				'GEC_STATUS'=>$cert_status,
				'GEC_COMMENTS'=>$comments,
				'GEC_UPDATE_DATE'=>date('Y-m-d H:i:s'),
				'GEC_CERTIFIED_BY'=>$by_employee,
		        'GEC_VERTICAL_CODE'=>implode(',', $vertical_list)
		);
		$key_arr = array('GEC_EMP_ID'=>$empId,"GEC_CERTIFICATE_ID"=>1);
		array_update_tables_common($upd_arr,"gft_emp_certificate_dtl",$key_arr,null,$by_employee);
	}else{
		$insarr = array(
				'GEC_EMP_ID'		=> $empId,
				'GEC_CERTIFICATE_ID'=> $cert_id,
				'GEC_STATUS'		=> $cert_status,
				'GEC_COMMENTS'		=> $comments,
				'GEC_CREATED_DATE'	=> date('Y-m-d H:i:s'),
				'GEC_UPDATE_DATE'	=> date('Y-m-d H:i:s'),
				'GEC_CERTIFIED_BY'	=> $by_employee,
		        'GEC_VERTICAL_CODE' => implode(',', $vertical_list)
		);
		array_insert_query("gft_emp_certificate_dtl", $insarr);
	}
}else if($get_purpose == 'get_hr_policies'){
    $sql1 = " select ghp_id,ghp_policy_label,ghp_policy_link,ghp_group_name, ghp_updated_date,ghp_status ".
        " from gft_hr_policies";
    $res1 = execute_my_query($sql1);
    $policies_arr = array();
    while ($row1 = mysqli_fetch_array($res1)){
        $policies_arr[]= array(
            'id'=>$row1['ghp_id'],
            'label'=>$row1['ghp_policy_label'],
            'group'=>$row1['ghp_group_name'],
            'link'=>$row1['ghp_policy_link'],
            'status'=>$row1['ghp_status'],
            'updated_datetime'=>date("d/m/y  h:i a",strtotime($row1['ghp_updated_date']))
        );
    }
    $return_array['data'] = $policies_arr;
}else if($get_purpose=='save_hr_policy'){
    $upd_arr = array(
        'ghp_policy_label'=> $policy_name,
        'ghp_group_name'  => $policy_group,
        'ghp_policy_link' => $policy_link,
        'ghp_status'      => $policy_status
    );
    $key_arr = array('ghp_id'=>$policy_id);
    array_update_tables_common($upd_arr,"gft_hr_policies",$key_arr,null,$by_employee,null,null,$upd_arr);
}else if($purpose=='mantis_custom_sync'){
	if(count($mantis_data_arr)==0){
		send_response_in_json_with_file_log("failure","Data array is empty");
		exit;
	}
	$severity_arr = $priority_arr = /*. (string[string]) .*/array();
	$my1 = execute_my_query("select GSM_CODE,GSM_MANTIS_CODE from gft_severity_master where GSM_STATUS='A'");
	while ($da1 = mysqli_fetch_array($my1)){
		$severity_arr[$da1['GSM_MANTIS_CODE']] = $da1['GSM_CODE'];
	}
	$my2 = execute_my_query("select GPM_CODE,GPM_MANTIS_CODE from gft_priority_master where GPM_STATUS='A'");
	while ($da2 = mysqli_fetch_array($my2)){
		$priority_arr[$da2['GPM_MANTIS_CODE']] = $da2['GPM_CODE'];
	}
	$curr_stat = 'T2';
	foreach ($mantis_data_arr as $compl_id => $val_arr){
		$chk_que =  " select GCH_COMPLAINT_ID from gft_customer_support_hdr ".
					" where GCH_COMPLAINT_ID='$compl_id' and GCH_CURRENT_STATUS='$curr_stat' ";
		$chk_res = execute_my_query($chk_que);
		if(mysqli_num_rows($chk_res)==0){
			continue;
		}
		foreach ($val_arr as $by_emp_eamil => $dtl_arr){
			$assign_to_email	= isset($dtl_arr['assign_to_emp_email'])?$dtl_arr['assign_to_emp_email']:'';
			$complaint_edc		= isset($dtl_arr['edc'])?db_date_format($dtl_arr['edc']):'';
			$severity			= isset($dtl_arr['severity'])?$dtl_arr['severity']:'';
			$priority			= isset($dtl_arr['priority'])?$dtl_arr['priority']:'';
			
			$by_emp_id = (int)get_single_value_from_single_table("GEM_EMP_ID", "gft_emp_master", "GEM_EMAIL", $by_emp_eamil);
			$assign_to = 0;
			if(strpos($assign_to_email, "@")!==false){
				$assign_to = (int)get_single_value_from_single_table("GEM_EMP_ID", "gft_emp_master", "GEM_EMAIL", $assign_to_email);
			}
			if($by_emp_id==0){
				continue;
			}
			$comp_sev = isset($severity_arr[$severity])?$severity_arr[$severity]:'';
			$comp_pri = isset($priority_arr[$priority])?$priority_arr[$priority]:'';
			$comp_assign_to = ($assign_to > 0)?$assign_to:'';
			$gcd_promise_date = ($complaint_edc!='')?"$complaint_edc":'';
			if($complaint_edc!=''){
				$comp_remarks = "EDC for this ticket id is ".date('M d, Y',strtotime($complaint_edc));
			}else{
				$comp_remarks = "changes synced from mantis";
			}
			$update_dtl = array();
			$update_dtl['GCD_EMPLOYEE_ID'] = $by_emp_id;
			$update_dtl['GCD_NATURE'] = '24';
			if($comp_assign_to!='') {
			    $update_dtl['GCD_PROCESS_EMP'] = $comp_assign_to;
			}
			if($comp_sev!='') {
			    $update_dtl['GCD_SEVERITY'] = $comp_sev;
			}
			if($comp_pri!='') {
			    $update_dtl['GCD_PRIORITY'] = $comp_pri;
			}
			if($gcd_promise_date!='') {
			    $update_dtl['GCD_PROMISE_DATE'] = $gcd_promise_date;
			}
			$update_dtl['GCD_REMARKS'] = $comp_remarks;
			$ins_quer = get_support_dtl_query_from_previous($compl_id,$update_dtl);
			$result_insert=execute_my_query($ins_quer);
			if($result_insert){
				$act_id=mysqli_insert_id_wrapper();
				updated_hdr_with_last_actid($compl_id, $act_id, $curr_stat,false,null,null,null,null,null,0,null,null,$complaint_edc);
				//support_sms($act_id);
			}
		}
	}
	send_response_in_json_with_file_log("success");
	exit;
}else if($purpose=='mantis_status_sync'){
	if(count($mantis_data_arr)==0){
		send_response_in_json_with_file_log("failure","Data array is empty");
		exit;
	}
	foreach ($mantis_data_arr as $dtl_arr){
		$sid  = $dtl_arr['complaint_id'];
		$stat = $dtl_arr['status'];
		$time = $dtl_arr['time'];
		$extra_upd = " ,GDC_MANTIS_SOLVED_DATE=null ";
		if( ($stat=='80') || ($stat=='90') ){
			$extra_upd = ",GDC_MANTIS_SOLVED_DATE=if(GDC_MANTIS_SOLVED_DATE is null,'$time',GDC_MANTIS_SOLVED_DATE) ";
		}
		execute_my_query("update gft_dev_complaints set GDC_MANTIS_STATUS='$stat' $extra_upd where GDC_COMPLAINT_ID='$sid' ");
	}
	send_response_in_json_with_file_log("success");
	exit;
}else if($get_purpose=="get_cust_id"){
    $json_arr = json_decode(get_samee_const("TASK_MANAGER_CUSTOMER_ID_JSON"),true);
    if(isset($json_arr[$cust_name])){
        echo json_encode(array("cust_id"=>$json_arr[$cust_name]));
        exit;
    }else{
        send_response_with_code_and_log($log, "Customer name not configured", HttpStatusCode::BAD_REQUEST);
        exit;
    }
}else if($purpose=="save_tour_suggestion_video"){
	$tour_model_id	=	isset($json_request['tour_model_id'])?(int)$json_request['tour_model_id']:0;
	$tour_model_name=	isset($json_request['tour_model_name'])?$json_request['tour_model_name']:'';
	$tour_model_desc=	isset($json_request['tour_model_desc'])?$json_request['tour_model_desc']:'';
	$tour_product	=	isset($json_request['tour_product'])?$json_request['tour_product']:'';
	$tour_status	=	isset($json_request['tour_status'])?$json_request['tour_status']:'';
	$by_employee	=	isset($json_request['by_employee'])?(int)$json_request['by_employee']:0;
	$menus 			=	isset($json_request['menus'])?$json_request['menus']:array();
	$insert_arr = array();
	$insert_arr['GPT_NAME'] 			= $tour_model_name;
	$insert_arr['GPT_DESCRIPTION'] 		= $tour_model_desc;
	$insert_arr['GPT_PRODUCT_TYPE'] 	= $tour_product;
	$insert_arr['GPT_MODEL_IMAGE_URL'] 	= "";
	$insert_arr['GPT_STATUS'] 			= $tour_status;
	$message = "";
	if($tour_model_id>0){// Update
		$table_key_arr['GPT_ID'] = $tour_model_id;
		array_update_tables_common($insert_arr, "gft_product_tour_model_hdr", $table_key_arr, null, $by_employee);
		$message = "Successfully updated";
	}else {//Insert new record		
		array_insert_query("gft_product_tour_model_hdr", $insert_arr);
		$tour_model_id=mysqli_insert_id_wrapper();		
		$message = "Successfully saved";
	}
	$inc = 0;
	$insert_query = "";
	while($inc<count($menus)){
		$order_by = $inc+1;
		$menu_id = $menus[$inc];
		$insert_query = ($insert_query!=""?"$insert_query,":"")."('$tour_model_id','$menu_id','$order_by')";
		$inc++;
	}
	if($insert_query!=""){
		execute_my_query("DELETE FROM gft_product_tour_model_dtl WHERE GPM_REF_ID='$tour_model_id'");
		execute_my_query("INSERT INTO gft_product_tour_model_dtl VALUES $insert_query");
	}
	send_response_in_json_with_file_log("success","$message");
	exit;
}else if($get_purpose=="product_tour_model"){
	$model_list = array();
	$result = execute_my_query("select GPT_ID, GPT_NAME, GPT_DESCRIPTION, GPT_PRODUCT_TYPE, GPT_MODEL_IMAGE_URL,".
								" GPT_STATUS, GROUP_CONCAT(GPM_MENU_IN) menus from gft_product_tour_model_hdr ".
								" INNER JOIN gft_product_tour_model_dtl on(GPM_REF_ID=GPT_ID) ".
								" GROUP BY GPT_ID");
	while($row_model=mysqli_fetch_array($result)){
		$model['tour_model_id'] 	= $row_model['GPT_ID'];
		$model['tour_model_name'] 	= $row_model['GPT_NAME'];
		$model['tour_model_desc'] 	= $row_model['GPT_DESCRIPTION'];
		$model['tour_product'] 		= $row_model['GPT_PRODUCT_TYPE'];
		$model['tour_status'] 		= $row_model['GPT_STATUS'];
		$model['tour_model_image'] 	= $row_model['GPT_MODEL_IMAGE_URL'];
		$model['menus'] 			= explode(',', $row_model['menus']);	
		$model['tour_display_name'] 	= get_single_value_from_single_query("GPG_PRODUCT_ALIAS", "select UPPER(GPG_PRODUCT_ALIAS) GPG_PRODUCT_ALIAS  from gft_product_group_master where concat(gpg_product_family_code, '-', gpg_skew)='".$row_model['GPT_PRODUCT_TYPE']."'");
		$model_list[]	=	$model;
	}
	echo json_encode($model_list);
	exit;
}else if($get_purpose=="save_tour_image"){    
    $edition_string    = isset($json_request['editionList'])?$json_request['editionList']:array();
    $record_id      = isset($json_request['record_id'])?(int)$json_request['record_id']:0;
    $emp_id = isset($json_request['by_employee'])?$json_request['by_employee']:"9999";
    $is_warehouse = isset($json_request['isWarehouse'])?(int)$json_request['isWarehouse']:0;
    $insert_arr     = array();
    $insert_arr['GPT_PRODUCT_CODE'] = isset($json_request['productName'])?$json_request['productName']:"";
    $insert_arr['GPT_VERTICAL_ID'] = isset($json_request['verticalName'])?$json_request['verticalName']:"";
    $insert_arr['GPT_EDITIONS'] = "$edition_string";
    $insert_arr['GPT_IMAGE_URL'] = isset($json_request['image_link'])?$json_request['image_link']:"";
    $insert_arr['GPT_IS_WAREHOUSE'] = $is_warehouse;
    $insert_arr['GPT_UPDATED_ON'] = date("Y-m-d H:i:s");
    $insert_arr['GPT_UPDATED_BY'] = $emp_id;
    if($record_id>0){
        $table_key_arr['GPT_ID'] = $record_id;
        $return_array['message'] = "Successfully updated your product tour image";
        array_update_tables_common($insert_arr, "gft_product_tour_bg_images", $table_key_arr, null, $emp_id);
    }else{
        array_insert_query("gft_product_tour_bg_images", $insert_arr);
        $return_array['message'] = "Successfully saved your product tour image";
    }
}else if($get_purpose == "get_product_tour_image"){
    $productNameFilter = isset($_GET['productNameFilter'])?$_GET['productNameFilter']:"";
    $verticalNameFilter = isset($_GET['verticalNameFilter'])?$_GET['verticalNameFilter']:"";
    $editionListFilter = isset($_GET['editionListFilter'])?$_GET['editionListFilter']:"";
    $all_eidtion = get_one_dimensional_array_from_single_table_with_key("GPT_TYPE_ID","GPT_TYPE_NAME", "gft_product_type_master");
    $sql1 =     " select GPT_ID, GPT_PRODUCT_CODE, GPT_VERTICAL_ID, GPT_EDITIONS, GPT_IMAGE_URL, GPT_UPDATED_ON, 
                  gpg_product_name, GTM_VERTICAL_NAME, GPT_IS_WAREHOUSE
                  from gft_product_tour_bg_images 
                  INNER JOIN gft_product_group_master ON(CONCAT(gpg_product_family_code,'-', gpg_skew)=GPT_PRODUCT_CODE)
                  INNER JOIN gft_vertical_master ON(GPT_VERTICAL_ID=GTM_VERTICAL_CODE)
                  where 1 ";
    if($productNameFilter != '' && $productNameFilter!='0'){
        $sql1 .= " AND GPT_PRODUCT_CODE='$productNameFilter'";
    }
    if($verticalNameFilter != '' && $verticalNameFilter!='0'){
        $sql1 .= " AND GPT_VERTICAL_ID='$verticalNameFilter'";
    }
    if($editionListFilter != '' && $editionListFilter!='0'){
        $sql1 .= " AND GPT_EDITIONS='$editionListFilter'";
    }
    $sql1 .= " order by 1 desc";
    $res1 = execute_my_query($sql1);
    $images_arr = array();
    while ($row1 = mysqli_fetch_array($res1)){
        $selected_edition = explode(",", $row1['GPT_EDITIONS']);
        $selected_edition_string = "";
        foreach ($all_eidtion as $key=>$value){
            if(in_array($key, $selected_edition))
                $selected_edition_string .= $value.", ";
        }
        $images_arr[]= array(
            'id'=>$row1['GPT_ID'],
            'productName'=>$row1['GPT_PRODUCT_CODE'],
            'productNameLabel'=>$row1['gpg_product_name'],
            'verticalName'=>$row1['GPT_VERTICAL_ID'],
            'verticalNameLabel'=>$row1['GTM_VERTICAL_NAME'],
            'editionList'=>$row1['GPT_EDITIONS'],
            'editionListLabel'=>trim($selected_edition_string,', '),
            'link'=>$row1['GPT_IMAGE_URL'],
            'isWarehouse'=>$row1['GPT_IS_WAREHOUSE'],
            'updated_datetime'=>date("d/m/y  h:i a",strtotime($row1['GPT_UPDATED_ON']))
        );
    }
    $return_array['data'] = $images_arr;
}else{
	$error['message']="invalid purpose";
	send_failure_response($error,HttpStatusCode::BAD_REQUEST);
	exit;
}
$resp = json_encode($return_array);
echo $resp;
$log->logInfo("Response - ".$resp);

?>
