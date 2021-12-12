<?php
require_once(__DIR__.'/lic_util.php');
require_once(__DIR__.'/log.php');

/**
 * @param string $message
 * @param string $request_body
 * @param string $customer_id
 * @param string[int] $user_id_arr
 * @param string[int][string] $invalid_info
 *
 * @return void
 */
function send_error_message($message,$request_body,$customer_id,$user_id_arr=null,$invalid_info=null){
	global $decrypted_data;
	$ret_json['status'] = '0';
	$ret_json['message'] = $message;
	if(is_array($user_id_arr) && count($user_id_arr)>0){
		$ret_json['invalid_users'] = $user_id_arr;
	}
	if(is_array($invalid_info) && count($invalid_info)>0){
	    $ret_json['invalid_user_info'] = $invalid_info;
	}
	$json_resp = json_encode($ret_json);
	echo $json_resp;
	log_request($request_body, $json_resp, '', $customer_id, '35','',$message,$decrypted_data);
}

try{
	$request_body = file_get_contents("php://input");
}catch(Exception $ex){
	die("Exception ". $ex);
}
$decrypted_data = lic_decrypt($request_body, $secret);
$data = /*. (string[string]) .*/json_decode($decrypted_data,true);
$item_arr = /*. (string[int][string]) .*/array();

$customer_id= isset($data['customer_id'])?$data['customer_id']:'';
$item_arr 	= isset($data['items'])?/*. (string[int][string]) .*/$data['items']:array();

$allowed_ids = explode(",",get_samee_const("Allowed_Customer_Ids"));
$allowed_ids = array_map('trim', $allowed_ids);

if( ($_SERVER['SERVER_NAME']=='sam.gofrugal.com') && (!in_array($customer_id, $allowed_ids)) ){
	$local_ip_address = get_samee_const("LOCAL_IP_ADDRESS_SETUPWIZARD");
	$received_ip_address = $_SERVER['REMOTE_ADDR'];
	if(strpos($local_ip_address, $received_ip_address)!==false){
		$ret_json['status'] 	= '0';
		$ret_json['message'] 	= "Skipping due to request from internal ip $received_ip_address";
		echo json_encode($ret_json);
		exit;
	}
}

$glh_country = $customer_name = $cust_type = "";
$query_result = execute_my_query("select GLH_LEAD_CODE,GLH_COUNTRY,GLH_CUST_NAME,GLE_USER_SYNC_TYPE from gft_lead_hdr join gft_lead_hdr_ext on (GLE_LEAD_CODE=GLH_LEAD_CODE) where GLH_LEAD_CODE='$customer_id' ");
if($row1 = mysqli_fetch_array($query_result)){
	$glh_country 	= $row1['GLH_COUNTRY'];
	$customer_name 	= $row1['GLH_CUST_NAME'];
	$cust_type      = $row1['GLE_USER_SYNC_TYPE'];
}else{
	send_error_message("Customer Id($customer_id) not found in CRM",$request_body,$customer_id);
	exit;
}
$connect_post_arr = /*. (string[string]) .*/array();
$invalid_user_id_arr = /*. (string[int]) .*/array();
$invalid_info = /*. (string[int][string]) .*/array();
$install_id = '';
for($i=0;$i<count($item_arr);$i++){
	$ref_id 		= isset($item_arr[$i]['user_id'])?$item_arr[$i]['user_id']:'';  //idendity-employee_id
	$user_name		= isset($item_arr[$i]['user_name'])?$item_arr[$i]['user_name']:'';
	$user_mobile 	= isset($item_arr[$i]['user_mobile'])?$item_arr[$i]['user_mobile']:'';
	$user_email  	= isset($item_arr[$i]['user_email'])?strtolower($item_arr[$i]['user_email']):'';
	$user_gf_role  	= isset($item_arr[$i]['user_gf_role'])?$item_arr[$i]['user_gf_role']:'';
	$user_status 	= isset($item_arr[$i]['user_status'])?$item_arr[$i]['user_status']:'';
	$user_pos_role	= isset($item_arr[$i]['user_pos_role'])?$item_arr[$i]['user_pos_role']:'';
	$contact_name	= isset($item_arr[$i]['contact_name'])?$item_arr[$i]['contact_name']:'';
	$user_password	= isset($item_arr[$i]['user_password'])?$item_arr[$i]['user_password']:'';
	$company_id		= isset($item_arr[$i]['company_id'])?(int)$item_arr[$i]['company_id']:1;
	$user_pos_role_id = isset($item_arr[$i]['user_role_id'])?$item_arr[$i]['user_role_id']:'';
	$rec_sys_access = isset($item_arr[$i]['system_access'])?$item_arr[$i]['system_access']:'';
	$mapped_outlets = isset($item_arr[$i]['mapped_outlets'])?/*. (string[int]) .*/$item_arr[$i]['mapped_outlets']:/*. (string[int]) .*/array();
	$default_prod_arr = isset($item_arr[$i]['mapped_products'])?$item_arr[$i]['mapped_products']:array();   
	
	$system_access = 0;
	if($rec_sys_access=='YES'){
		$system_access = 1;
	}
	$ref_id_arr = explode('-', $ref_id);
	$idendity = isset($ref_id_arr[0])?$ref_id_arr[0]:'';
	$ref_emp_id  = isset($ref_id_arr[1])?$ref_id_arr[1]:'';
	if($install_id==''){
		$idendity_arr = get_details_from_idendity($idendity);
		if(isset($idendity_arr[1])){
			$install_id = $idendity_arr[1];
		}else{
			send_error_message("Invalid Idendity sent in request ($idendity) ",$request_body,$customer_id);
			exit;
		}
	}
	$invalid_contact = false;
	if($user_email!=''){
		if(!is_valid_email($user_email)){
			$invalid_user_id_arr[] = $ref_emp_id;
			$invalid_info[] = array('user'=>$ref_emp_id,'reason'=>'invalid email id');
			$invalid_contact = true;
		}
	}
	if($user_mobile!=''){
		if(!is_valid_mobile($user_mobile)){
			$invalid_user_id_arr[] = $ref_emp_id;
			$invalid_info[] = array('user'=>$ref_emp_id,'reason'=>'invalid mobile number');
			$invalid_contact = true;
		}
	}
	if($invalid_contact){
		continue;
	}
	
	$mob_type	= get_contact_type_for_number($user_mobile);
	$mobile_id 	= save_and_get_contact_id($customer_id,$user_mobile,$user_name,'5',$mob_type);
	$gcc_em_id	= save_and_get_contact_id($customer_id,$user_email,$user_name,'5','4');
	if($mobile_id > 0){
		save_pos_users($mobile_id,$install_id,$user_name,$user_password,$user_gf_role,$user_pos_role_id,$user_pos_role,$user_status,$system_access,$ref_emp_id);
		save_pos_users_company_mapping($mobile_id,$install_id,$ref_emp_id,$company_id);
		if(is_array($default_prod_arr) && (count($default_prod_arr) > 0) ){
		    foreach ($default_prod_arr as $prod){
		        update_app_to_mobile($mobile_id, $install_id, $prod, '1');
		    }
		}
	}
	insert_mapped_outlets($customer_id,$install_id,$ref_emp_id,$mapped_outlets);
	$product_dtl = get_details_from_idendity($idendity);
	$product_code 	= isset($product_dtl[4])?$product_dtl[4]:"";
	$product_skew	= isset($product_dtl[5])?$product_dtl[5]:"";
	$arr = /*. (string[string]) .*/array();
	$arr['empId']			= $ref_emp_id;
	$arr['userId'] 			= $user_name;
	$arr['userName']		= $contact_name;
	$arr['password'] 		= $user_password;
	$arr['customerId'] 		= $customer_id;
	$arr['customerName'] 	= $customer_name;
	$arr['roleId'] 			= $user_pos_role_id;
	$arr['role'] 			= $user_pos_role;
	$arr['hasSystemAccess'] = ($system_access==1)?true:false;
	$arr['enabled'] 		= ($user_status=='A')?true:false;
	$arr['mobileNumber'] 	= $user_mobile;
	$arr['emailId'] 		= $user_email;
	$arr['baseProductId'] 	= $product_code."-".substr($product_skew, 0, 4);
	$arr['groups'] 			= get_group_and_outlet_name_from_outlets($install_id,$mapped_outlets);
	$arr['customerType']    = $cust_type;
	$connect_post_arr[]		= $arr;
}
post_user_arr_to_connectplus($connect_post_arr,$customer_id,$log);

execute_my_query(" UPDATE gft_customer_contact_dtl join gft_lead_hdr on (GLH_LEAD_CODE=GCC_LEAD_CODE and glh_country='India') ".
		" SET GCC_CONTACT=substring(gcc_contact_no,-10) " .
		" WHERE GLH_LEAD_CODE='$customer_id' and gcc_contact_type in (1,2,3) and GCC_CONTACT_NO REGEXP '[A-z]$'=0 ");

if(count($invalid_user_id_arr)==0){
	$ret_json['status'] = '1';
	$json_resp = json_encode($ret_json);
	echo $json_resp;
	log_request($request_body, $json_resp, '', $customer_id, '35','','',$decrypted_data);
}else{
	$invalid_user_id_arr = /*. (string[int]) .*/array_values(array_unique($invalid_user_id_arr));
	send_error_message("Invalid Contacts Found", $request_body, $customer_id,$invalid_user_id_arr,$invalid_info);
	exit;
}

?>
<script type="text/javascript">
window.close();
</script>