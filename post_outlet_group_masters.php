<?php
require_once(__DIR__.'/dbcon.php');
require_once(__DIR__.'/lic_util.php');
require_once(__DIR__.'/log.php');
header('Content-Type: application/json');

/**
 * @param string $status
 * @param string $error_msg
 * 
 * @return void
 */
function send_response_for_master_sync($status,$error_msg=''){
	global $log;
	$resp['status'] = $status;
	if($error_msg!=''){
		$resp['message'] = $error_msg;
	}
	$output = json_encode($resp);
	echo $output;
	$log->logInfo("Response - ".$output);
}

$request_body = file_get_contents("php://input");
$decrpted_data = lic_decrypt($request_body, $secret);
$log->logInfo("Decrypted Data - ".$decrpted_data);
$json_arr = /*. (string[string]) .*/json_decode($decrpted_data,true);

$customer_id = isset($json_arr['customer_id'])?(int)$json_arr['customer_id']:0;

$chk_cust_id = get_single_value_from_single_table("GLH_LEAD_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", $customer_id);
if($chk_cust_id==''){
	send_response_for_master_sync("failure","Invalid customer id($customer_id) ");
	exit;
}
$identity = isset($json_arr['identity'])?$json_arr['identity']:'';
$identity_dtl	= get_details_from_idendity($identity);
$install_id 	= isset($identity_dtl[1])?(int)$identity_dtl[1]:0;
$iden_cust_id	= isset($identity_dtl[0])?(int)$identity_dtl[0]:0;
$base_pcode		= isset($identity_dtl[4])?(int)$identity_dtl[4]:0;
if($install_id==0){
	send_response_for_master_sync("failure","Invalid identity($identity) ");
	exit;	
}else if($iden_cust_id!=$customer_id){
	send_response_for_master_sync("failure","Customer id and identity mismatch");
	exit;
}
$gid_store_url		= isset($json_arr['store_url'])?$json_arr['store_url']:"";
$outlet_master_arr 	= isset($json_arr['outlet_master'])?/*. (string[int][string]) .*/$json_arr['outlet_master']:/*. (string[int][string]) .*/array();
$group_master_arr	= isset($json_arr['group_master'])?/*. (string[int][string]) .*/$json_arr['group_master']:/*. (string[int][string]) .*/array();

$group_val = $put_comma = "";
foreach ($group_master_arr as $val){
	$group_id 	= $val['group_id'];
	$group_name = $val['group_name'];
	$group_val .= "$put_comma('$customer_id','$install_id','$group_id','$group_name',now())";
	$put_comma = ",";
}
$outlet_val = $mapping_val = $put_comma = "";
$kit_based_customer = is_kit_based_customer($customer_id);
foreach ($outlet_master_arr as $val){
	$outlet_id 	= $val['outlet_id'];
	$outlet_name= $val['outlet_name'];
	$mapped_group = $val['mapped_group'];
	$outlet_val .= "$put_comma('$customer_id','$install_id','$outlet_id','".mysqli_real_escape_string_wrapper($outlet_name)."',now())";
	$mapping_val .= "$put_comma('$customer_id','$install_id','$mapped_group','$outlet_id',now())";
	$put_comma = ",";
	$store_type	= isset($val['store_type'])?(int)$val['store_type']:0;
	$edition	= isset($val['edition'])?$val['edition']:"";
	$contact_no = isset($val['contact_no'])?$val['contact_no']:"";
	$location	= isset($val['location'])?$val['location']:"";
	$city		= isset($val['city'])?$val['city']:"";
	$state		= isset($val['state'])?$val['state']:"";
	$country	= isset($val['country'])?$val['country']:"";
	$pincode	= isset($val['pincode'])?$val['pincode']:"";
	$outlet_stat= isset($val['outlet_status'])?$val['outlet_status']:"";
	$vat_tin	= isset($val['vattin'])?$val['vattin']:"";
	$email		= isset($val['email'])?$val['email']:"";
	$fin_year 	= isset($val['financial_year'])?$val['financial_year']:"";
	$order_no	= isset($val['order_no'])?$val['order_no']:"";
	$order_no	= (string)str_replace("-", "", $order_no);
	$pcode      = substr($edition, 0,3);
	$send_uuid_notification = false;
	if( ($base_pcode=='300') && ($outlet_id=='1') ){
		$chk_query = "select GHO_OUTLET_ID from gft_hq_outlet_master where GHO_INSTALL_ID='$install_id' and GHO_OUTLET_ID='$outlet_id' ";
		$chk_res = execute_my_query($chk_query);
		if( (mysqli_num_rows($chk_res)==0) && ($contact_no!='') ){ //first time coming. so create peergroup entry
			//peergroup entry
			$ch = curl_init(get_samee_const("Peergroup_Portcheck_Url"));
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			$order_fullfill_no = substr($identity, 0,19);
			$cust_res = execute_my_query("select GLH_CUST_NAME, GLH_CUST_STREETADDR2 from gft_lead_hdr where GLH_LEAD_CODE='$customer_id'");
			if($data1 = mysqli_fetch_array($cust_res)){
				$cust_name 		= $data1['GLH_CUST_NAME'];
				$cust_location 	= $data1['GLH_CUST_STREETADDR2'];
				$post_str 		= "purpose=get_store_url&orderNo=$order_fullfill_no&customerName=$cust_name&customerLocation=$cust_location&customerId=$contact_no&csEnabled=1&entryFor=HQNT";
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_str);
				$response_json = (string)curl_exec($ch);
				error_log($response_json);
			}
			curl_close($ch);
		}
		$insert_arr = /*. (string[string]) .*/array();
		$insert_arr["GOL_INSTALL_ID"]		=	$install_id;
		$insert_arr["GOL_OUTLET_ID"] 		=	$outlet_id;
		$insert_arr["GOL_OUTLET_CONTACT_INFO"]	=	$contact_no;
		$insert_arr["GOL_CUST_ID"]				=	$customer_id;
		$insert_arr["GOL_STORE_TYPE"]			=	$store_type;
		$insert_arr["GOL_EDITION"]				=	'';
		$insert_arr["GOL_DATETIME"]				=	date("Y-m-d H:i:s");
		$insert_arr["GOL_OUTLET_EMAIL"]			=	$email;
		$insert_arr["GOL_VAT_TIN"]				=	$vat_tin;
		$insert_arr["GOL_FINANCIAL_YEAR"]		=	$fin_year;
		$insert_arr["GOL_OUTLET_STATUS"]		=	$outlet_stat;
		$insert_arr['GOL_ORDER_NO'] 			= 	substr($identity, 0,15);
		$insert_arr['GOL_FULLFILLMENT_NO'] 		= 	(int)substr($identity, 15,4);
		$table_key_arr["GOL_INSTALL_ID"]		=	$install_id;
		$table_key_arr["GOL_OUTLET_ID"] 		=	$outlet_id;
		array_update_tables_common($insert_arr, "gft_outlet_lead_code_mapping", $table_key_arr, null, SALES_DUMMY_ID,null,null,$insert_arr);
		continue; //not required to create lead as outlet id 1 is HQ itself created for own
	}
	$outlet_lead_code = get_lead_code_for_outlet($outlet_id,$install_id);
	if($outlet_lead_code!=0){
		$updatearr = /*. (string[string]) .*/array();
		$updatearr["GOL_EDITION"]				=	$edition;
		$updatearr["GOL_OUTLET_CONTACT_INFO"]	=	$contact_no;
		$updatearr["GOL_OUTLET_EMAIL"]			=	$email;
		$updatearr["GOL_VAT_TIN"]				=	$vat_tin;
		$updatearr["GOL_FINANCIAL_YEAR"]		=	$fin_year;
		$updatearr["GOL_OUTLET_STATUS"]			=	$outlet_stat;
		if(strlen($order_no)>=19){
			$updatearr['GOL_ORDER_NO'] 			= 	substr($order_no, 0,15);
			$updatearr['GOL_FULLFILLMENT_NO'] 	= 	"".(int)substr($order_no, 15,4);
		}
		$table_key_arr["GOL_INSTALL_ID"]		=	$install_id;
		$table_key_arr["GOL_OUTLET_ID"] 		=	$outlet_id;
		$column_if_update["GOL_DATETIME"]	=	date("Y-m-d H:i:s");
		$is_updated = array_update_tables_common($updatearr, "gft_outlet_lead_code_mapping", $table_key_arr, array(), SALES_DUMMY_ID,null,$column_if_update,null,true);
		if($is_updated) $send_uuid_notification = true;
		if($kit_based_customer){
		    execute_my_query("update gft_lead_hdr set GLH_CUST_NAME='".mysqli_real_escape_string_wrapper($outlet_name)."' where GLH_LEAD_CODE='$outlet_lead_code'");
		}
	}else {
		$client_lead_code = 0;
		$gid_order_no = "";
		$gid_fullfill_no = 0;
		if( ($edition!="") && $kit_based_customer ){
			$lead_arr['GLH_CUST_NAME'] 			= $outlet_name;
			$lead_arr['GLH_CUST_STREETADDR2'] 	= $location;
			$lead_arr['GLH_LEAD_TYPE'] 			= "13";
			$lead_arr['GLH_STATUS'] 			= "26";
			$lead_arr['GLH_LEAD_SOURCECODE']	= "7";
			$lead_arr['GLH_REFERENCE_GIVEN']	= $customer_id;
			$lead_arr['GLH_FIELD_INCHARGE']     = get_single_value_from_single_table("glh_field_incharge", "gft_lead_hdr", "glh_lead_code", $customer_id);
			$lead_arr['GLH_CUST_PINCODE']		= $pincode;
			$lead_arr['GLH_CUST_CITY']			= $city;
			$lead_arr['GLH_CUST_STATECODE']		= $state;
			$lead_arr['GLH_COUNTRY']			= $country;
			$mob_len = strlen($contact_no);
			$contact_type = '2';
			if( ($mob_len >= 10) && check_can_send_sms($contact_no) ){
				$contact_type 	= '1';
			}
			$lead_created_status = array_insert_new_lead_db($lead_arr, null, array("admin","admin"), array($contact_no,$email), array("1","1"),
					array($contact_type,'4'),null,'off',null,null,null,'hq_outlet_sync');
			$client_lead_code	=	(int)$lead_created_status[1];
		}else if(strlen($order_no) >= 19){
			$gid_order_no 	 = substr($order_no, 0,15);
			$gid_fullfill_no = (int)substr($order_no, 15,4);
			$sql1 = " select GID_LEAD_CODE from gft_install_dtl_new ".
					" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=GID_LIC_PCODE) ".
					" where GID_ORDER_NO='$gid_order_no'and GID_FULLFILLMENT_NO=$gid_fullfill_no ".
					" and GID_STATUS!='U' and fm.GPM_HEAD_FAMILY!=400 ";
			$res1 = execute_my_query($sql1);
			if($row1 = mysqli_fetch_array($res1)){
				$client_lead_code = (int)$row1['GID_LEAD_CODE'];
			}
		}
		if($client_lead_code!=0){
			$tdate = date('Y-m-d H:i:s');
			$pskew = substr($edition, 3,2).".".substr($edition,5);
			$ins_arr = array(
					"GOL_INSTALL_ID"=>$install_id,
					"GOL_OUTLET_ID"=>$outlet_id,
					"GOL_OUTLET_CONTACT_INFO"=>$contact_no,
					"GOL_CUST_ID"=>$client_lead_code,
					"GOL_ORDER_NO"=>$gid_order_no,
					"GOL_FULLFILLMENT_NO"=>"$gid_fullfill_no",
					"GOL_EDITION"=>$edition,
					"GOL_DATETIME"=>date("Y-m-d H:i:s"),
					"GOL_OUTLET_EMAIL"=>$email,
					"GOL_VAT_TIN"=>$vat_tin,
					"GOL_FINANCIAL_YEAR"=>$fin_year,
					"GOL_OUTLET_STATUS"=>$outlet_stat
			);
			array_insert_query("gft_outlet_lead_code_mapping", $ins_arr);
			if(strlen($order_no) >= 19) $send_uuid_notification = true;
		}
	}
	if($send_uuid_notification){
	    notify_pos_product($customer_id, "", "outlet_uuid_update", "",$pcode,"",false,"","","","",$order_no);
	}
}
if($group_val!=''){
	$del_query = "delete from gft_hq_outlet_group_master where GHG_INSTALL_ID='$install_id'";
	execute_my_query($del_query);
	$ins_outlet=" insert into gft_hq_outlet_group_master (GHG_CUSTOMER_ID,GHG_INSTALL_ID,GHG_GROUP_ID,GHG_GROUP_NAME,GHG_UPDATED_DATE) ".
			" values $group_val ";
	$res = execute_my_query($ins_outlet);
	if($res===false){
		send_response_for_master_sync("failure","Error occured during outlet group master insertion");
		exit;
	}
}
if($outlet_val!=''){
	$del_query = "delete from gft_hq_outlet_master where GHO_INSTALL_ID='$install_id'";
	execute_my_query($del_query);
	$ins_outlet=" insert into gft_hq_outlet_master (GHO_CUSTOMER_ID,GHO_INSTALL_ID,GHO_OUTLET_ID,GHO_OUTLET_NAME,GHO_UPDATED_DATE) ".
			" values $outlet_val ";
	$res = execute_my_query($ins_outlet);
	if($res===false){
		send_response_for_master_sync("failure","Error occured during outlet master insertion");
		exit;
	}
}
if($mapping_val!=''){
	$del_query = "delete from gft_hq_outlet_group_mapping where GHM_INSTALL_ID='$install_id'";
	execute_my_query($del_query);
	$ins_outlet=" insert into gft_hq_outlet_group_mapping (GHM_CUSTOMER_ID,GHM_INSTALL_ID,GHM_GROUP_ID,GHM_OUTLET_ID,GHM_UPDATED_DATE) ".
			" values $mapping_val ";
	$res = execute_my_query($ins_outlet);
	if($res===false){
		send_response_for_master_sync("failure","Error occured during outlet group mapping insertion");
		exit;
	}
	//sync_pos_users_to_connectplus($customer_id);
}
if( $kit_based_customer && ($gid_store_url!="") && ($gid_store_url!=null) && ($gid_store_url!="null") ){
	execute_my_query("update gft_install_dtl_new set GID_STORE_URL='$gid_store_url' where GID_INSTALL_ID='$install_id'");
}
send_response_for_master_sync("success");

?>
