<?php
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__.'/../log.php');
require_once(__DIR__.'/../ismile/http_error.php');

/**
 * @return string[string]
 */
function gstin_config(){
    $retarr = array(
        'validate_url'=>"https://gstin.gofrugalconnect.com/asp/search-tax-payer",
        'validate_key'=>"15aabb0f-0114-4e23-81c5-142ecfd6a0e9"
    );
    return $retarr;
}

/**
 * @return string[string]
 */
function get_connectplus_config(){
	$test_mode = get_samee_const('ENVIRONMENT'); // 0 - live, 1 - samtest, 2 - preprod
	$retarr = /*.(string[string]).*/array();
	$retarr['gosecure_lic_post']   = "/gosecure-subscription/api/license/update-license-info";
	$retarr['gosecure_price_valid']= "/gosecure-subscription/api/transaction/validate";
	$retarr['integration_emp_url'] = "https://integration-demo.gofrugalretail.com/ecommerce";
	$retarr['whitelabel_key']      = "c5177c4e1027e73147cedb048028fdfbbbb5fe2a";
	$retarr['instock_key']         = "PQGujF9bd2VQGUZ7hmXx";
	if($test_mode=='0'){ //live or prod
		$retarr['blowfish_key'] 	= "7d4a3a46f45cce45d16e56af3e45bb3d";
		$retarr['provision_url'] 	= "https://gofrugalconnect.com/provisioning_service/provisioning/create_database";
		$retarr['provision_api_key']= "P+OzwHlZ2FGcM9dnOA/+wGvBTE3EHek0zTbNBwSAppY=";
		$retarr['warehouse_url']    = "https://warehouse-manager.gofrugalconnect.com/warehouse-manager/provisioning/create";
		$retarr['warehouse_api_key']= "kZgKRdkja9NlksYlQZFLirdsm5aYVYKcrYblpe5Z";		
		$retarr['sam_api_key']		= "BjGQhh0JGAaQjp64Vo68DCOLfGuKJs/8C35DdsTNlHI=";
		$retarr['connect_api_key']	= "tK7H1eMD4ie6nHWcK7WT2s1T02dR1ehSOJp0DF337EM=";
		$retarr['pos_user_url']		= "https://{{customerId}}.gofrugalconnect.com/identity/customer/{{customerId}}";
		$retarr['cloud_domain'] 	= "https://{{customerId}}.gofrugalconnect.com";
		$retarr['service_provision']= "https://pos-payment.gofrugalconnect.com/payments/licensing/samprovision";
		$retarr['hq_setup_server']	= "http://hqns.gofrugal.com/install.php";
		$retarr['chatbot_server']	= "https://assure.gofrugal.com/s3tool";
		$retarr['sami_api_key']		= "NCBzZAkpUqsBTcxCXiqrnFICMc2m8C2Q/gdCWlD4n3w=";
		$retarr['post_token_url']	= "https://auth.gofrugalconnect.com/identity/customer/{{customerId}}/customer-addon-info";
		$retarr['trac_tour_url']	= "https://producttour.gofrugal.com/#/trac/videotour";
		$retarr['peekaboo_url']	    = "https://proboscis.delium.io/api/lead_intents/register";
		$retarr['peekaboo_key']	    = "partner_key";
		$retarr['dealer_url']       = "https://integration.gofrugal.com/ecommerce";
		$retarr['dealer_token']     = "4e248943-d818-4390-9919-4f796b737b7f";
		$retarr['gosecure_lic_key'] = "cb5dd38d-457a-4d3b-b63f-e28b6c42f143";
		$retarr['tm_key']           = "O3tppU3dzNEW6SgjzVDmqYEZ3FokTHdC2Mp4hECY";
		$retarr['integ_portal']     = "https://integration.gofrugal.com";
		$retarr['release_notes']     = "https://releasenotes.gofrugal.com/";
		$retarr['support_feedback_url']     = "https://gofrugal.com/support-feedback?";
		$retarr['google_captcha_secret_key']  = "6LcR6tMZAAAAAKL9WUWWUAB1OSwGAvOGwVADm7YC";
	}else if($test_mode=='1'){ //qa
		$retarr['blowfish_key'] 	= "46e3589eabc625e322a1be459d6e182e";
		$retarr['provision_url'] 	= "http://samtest.qaconnectplus.com/provisioning_service/provisioning/create_database";
		$retarr['provision_api_key']= "573e6536-bf4f-4837-aca6-5f85d5456ba3";
		$retarr['warehouse_url']    = "http://warehouse-manager.qaconnectplus.com/warehouse-manager/provisioning/create";
		$retarr['warehouse_api_key']= "rtF2JPy2ddSw2HDDb7W6r2s5Ch8Nil4eP3sc8jRd";		
		$retarr['sam_api_key']		= "cae21ef2-392a-11e6-ac61-9e71128cae77";
		$retarr['connect_api_key']	= "4wls2l2-sdm1ls072a-=23k30^eqp03@s861";
		$retarr['pos_user_url']		= "http://{{customerId}}.qaconnectplus.com/identity/customer/{{customerId}}";
		$retarr['cloud_domain'] 	= "http://{{customerId}}.qaconnectplus.com";
		$retarr['service_provision']= "https://pos-payment.qaconnectplus.com/payments/licensing/samprovision";
		$retarr['hq_setup_server']	= "http://connecttest11/install.php";
		$retarr['chatbot_server']   = "https://assure-labtest.gofrugal.com/s3tool";
		$retarr['sami_api_key']		= "NCBzZAkpUqsBTcxCXiqrnFICMc2m8C2Q/gdCWlD4n3w=";
		$retarr['post_token_url']	= "http://auth.qaconnectplus.com/identity/customer/{{customerId}}/customer-addon-info";
		$retarr['trac_tour_url']	= "https://labtest.gofrugal.com/video_library/#/trac/videotour";
		$retarr['peekaboo_url']	    = "https://proboscis-qa.delium.io/api/lead_intents/register";
		$retarr['peekaboo_key']	    = "db63613202b5bc49e2086e1f4a95f6a6";
		$retarr['dealer_url']       = "https://integration-qa.gofrugalretail.com/ecommerce";
		$retarr['dealer_token']     = "4e248943-d818-4390-9919-4f796b737b7f";
		$retarr['gosecure_lic_key'] = "d985d654-a8af-45e4-ae36-4170b19a532e";
		$retarr['tm_key']           = "Bj04wNUxRLE3xuOdgsUzK6Ti1tZGGJFyyeYa7Hgc";
		$retarr['integ_portal']     = "https://integration-qa.gofrugalretail.com";
		$retarr['release_notes']     = "https://labtest.gofrugal.com/release_notes/";
		$retarr['support_feedback_url']     = "https://staging.gofrugal.com/support-feedback?";
		$retarr['google_captcha_secret_key']  = "6Lc6VdMZAAAAAGsbUT0UIY0i53kEee4-fYwD0DKM";
	}else if($test_mode=='2'){ //preprod
		$retarr['blowfish_key'] 	= "46e3589eabc625e322a1be459d6e182e";
		$retarr['provision_url'] 	= "http://gofrugalretail.com/provisioning_service/provisioning/create_database";
		$retarr['provision_api_key']= "573e6536-bf4f-4837-aca6-5f85d5456ba3";
		$retarr['warehouse_url']    = "http://warehouse-manager.gofrugalretail.com/warehouse-manager/provisioning/create";
		$retarr['warehouse_api_key']= "ryiFMMrMCZeyYN1d1SidzKtLq1pnGovYUxIE0Ke4";
		$retarr['sam_api_key']		= "cae21ef2-392a-11e6-ac61-9e71128cae77";
		$retarr['connect_api_key']	= "4wls2l2-sdm1ls072a-=23k30^eqp03@s861";
		$retarr['pos_user_url']		= "http://{{customerId}}.gofrugalretail.com/identity/customer/{{customerId}}";
		$retarr['cloud_domain'] 	= "http://{{customerId}}.gofrugalretail.com";
		$retarr['service_provision']= "https://pos-payment.gofrugalretail.com/payments/licensing/samprovision";
		$retarr['hq_setup_server']	= "http://connecttest11/install.php";
		$retarr['sami_api_key']		= "NCBzZAkpUqsBTcxCXiqrnFICMc2m8C2Q/gdCWlD4n3w=";
		$retarr['post_token_url']	= "http://auth.gofrugalretail.com/identity/customer/{{customerId}}/customer-addon-info";
		$retarr['trac_tour_url']	= "https://producttour.gofrugal.com/#/trac/videotour";
		$retarr['chatbot_server']   = "https://assure-labtest.gofrugal.com/s3tool";
		$retarr['peekaboo_url']	    = "https://proboscis.delium.io/api/lead_intents/register";
		$retarr['peekaboo_key']	    = "partner_key";
		$retarr['dealer_url']       = "https://dealers-app.gofrugalretail.com/ecommerce";
		$retarr['dealer_token']     = "4e248943-d818-4390-9919-4f796b737b7f";
		$retarr['gosecure_lic_key'] = "a760d2bb-e506-4486-93e5-ee410ba51127";
		$retarr['tm_key']           = "PQYvBcUuqI0U6R9RXT5RrcgEMP7laITIrGt7EPO7";
		$retarr['integ_portal']     = "https://integration-qa.gofrugalretail.com";
		$retarr['release_notes']     = "https://labtest.gofrugal.com/release_notes/";
		$retarr['support_feedback_url']     = "https://staging.gofrugal.com/support-feedback?";
		$retarr['google_captcha_secret_key']  = "6Lc6VdMZAAAAAGsbUT0UIY0i53kEee4-fYwD0DKM";
	}
	return $retarr;
}

/**
 * @param string[string] $data_arr
 * @param int $status_code
 *
 * @return void
 */
function send_failure_response($data_arr,$status_code){
	header("X-PHP-Response-Code: ".$status_code,true, $status_code);
	echo json_encode($data_arr);
}

/**
 * @param mixed $log
 * @param string $message
 * @param int $response_code
 * 
 * @return void
 */
function send_response_with_code_and_log($log,$message,$response_code){
	header("X-PHP-Response-Code: ".$response_code,true, $response_code);
	$data_arr = array('message'=>$message);
	$out = json_encode($data_arr);
	echo $out;
	$log->logInfo("Response => ".$out);
}

/**
 * @return string[string]
 */
function get_notification_server_config(){
	$test_mode = get_samee_const('ENVIRONMENT'); // 1 - test,  0 - live
	$arr = /*.(string[string]).*/array();
	if($test_mode=='0'){
		$arr['notification_server'] 	= "http://ns.gofrugalconnect.com";
		$arr['customers_url']			= "http://ns.gofrugalconnect.com/ui/customers";
		$arr['customer_url']            = "http://ns.gofrugalconnect.com/ui/customer";
		$arr['plan_url'] 				= "http://ns.gofrugalconnect.com/ui/customers/{{parentId}}/update_notification_plan";
		$arr['wns_url']					= "http://ns.gofrugalconnect.com/projects/mqnotifications";
		$arr['send_notification_url']	= "http://ns.gofrugalconnect.com/projects/notifications";
		$arr['users_url']				= "http://ns.gofrugalconnect.com/projects/users";
		$arr['switch_user']				= "http://ns.gofrugalconnect.com/projects/users/switch_user";
		$arr['project_url']				= "http://ns.gofrugalconnect.com/ui/projects";
		$arr['wns_report_url'] 			= "http://ns.gofrugalconnect.com/ui/wnsreport";
		$arr['wns_auth_token']			= "c18b01c3-180d-467c-8410-64ab1da3ca67";
		$arr['api_ui_key']				= "c849d810-d5ac-4df4-b1be-4434dd02316b";
		$arr['alert_domain']			= "http://alert.gofrugal.com";
		$arr['peer_domain']             = "http://peer.gofrugal.com/PeerGroupDNS";
		$arr['axis_bank_api_key']		= "e72bb2cb-4003-4e93-ba6a-abaf59a2615b";
		$arr['cloud_call']		        = "e72bb2cb-4003-4e93-ba6a-abaf59a2615b";
	}else if($test_mode=='1'){
		$arr['notification_server'] 	= "http://nsqa.gofrugalconnect.com";
		$arr['customers_url']			= "http://nsqa.gofrugalconnect.com/ui/customers";
		$arr['customer_url']            = "http://nsqa.gofrugalconnect.com/ui/customer";
		$arr['plan_url'] 				= "http://nsqa.gofrugalconnect.com/ui/customers/{{parentId}}/update_notification_plan";
		$arr['wns_url']					= "http://nsqa.gofrugalconnect.com/projects/mqnotifications";
		$arr['send_notification_url']	= "http://nsqa.gofrugalconnect.com/projects/notifications";
		$arr['users_url']				= "http://nsqa.gofrugalconnect.com/projects/users";
		$arr['switch_user']				= "http://nsqa.gofrugalconnect.com/projects/users/switch_user";
		$arr['project_url']				= "http://nsqa.gofrugalconnect.com/ui/projects";
		$arr['wns_report_url'] 			= "http://nsqa.gofrugalconnect.com/ui/wnsreport";
		$arr['wns_auth_token']			= "3816e7de-a609-400a-af7a-fcf214b07334";
		$arr['api_ui_key'] 				= "a6e61406-91cb-4ac5-a48d-7adc55b75bb1";
		$arr['alert_domain']			= "http://mysqldb:7171";
		$arr['peer_domain']             = "http://mysqldb:7373/PeerGroupDNS";
		$arr['axis_bank_api_key']		= "e106e088-41a1-463c-a86f-43880c58c944";
		$arr['cloud_call']		        = "e106e088-41a1-463c-a86f-43880c58c944";
	}else if($test_mode=='2'){
		$arr['notification_server'] 	= "http://nspreprod.gofrugalconnect.com";
		$arr['customers_url']			= "http://nspreprod.gofrugalconnect.com/ui/customers";
		$arr['customer_url']            = "http://nspreprod.gofrugalconnect.com/ui/customer";
		$arr['plan_url'] 				= "http://nspreprod.gofrugalconnect.com/ui/customers/{{parentId}}/update_notification_plan";
		$arr['wns_url']					= "http://nspreprod.gofrugalconnect.com/projects/mqnotifications";
		$arr['send_notification_url']	= "http://nspreprod.gofrugalconnect.com/projects/notifications";
		$arr['users_url']				= "http://nspreprod.gofrugalconnect.com/projects/users";
		$arr['switch_user']				= "http://nspreprod.gofrugalconnect.com/projects/users/switch_user";
		$arr['project_url']				= "http://nspreprod.gofrugalconnect.com/ui/projects";
 		$arr['wns_report_url'] 			= "http://nspreprod.gofrugalconnect.com/ui/wnsreport";
		$arr['wns_auth_token']			= "673efe92-cb0c-4490-a47b-cd87eab1d750";
		$arr['api_ui_key'] 				= "a6e61406-91cb-4ac5-a48d-7adc55b75bb1";
		$arr['alert_domain']			= "http://mysqldb:7171";
		$arr['peer_domain']             = "http://mysqldb:7373/PeerGroupDNS";
		$arr['axis_bank_api_key']		= "e106e088-41a1-463c-a86f-43880c58c944";
		$arr['cloud_call']		        = "e106e088-41a1-463c-a86f-43880c58c944";
	}
	
	return $arr;
}

/**
 * @param string $cust_id
 * @param string $post_url
 * @param string $post_data
 * @param string[string] $header_arr
 * @param string $http_method
 *
 * @return string[string]
 */
function do_curl_to_connectplus($cust_id,$post_url,$post_data,$header_arr,$http_method="POST"){
	global $log;
	$ch = curl_init($post_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $http_method);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$log_arr['response_body'] = (string)curl_exec($ch);
	$log_arr['response_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curl_error = curl_error($ch);
	if($curl_error!=''){
	    $log_arr['curl_error'] = $curl_error;
	}
	$log_arr['customer_id'] = $cust_id;
	$log_arr['post_url']    = $post_url;
	$log_arr['post_data']   = $post_data;
	curl_close($ch);
	$log->logInfo($log_arr,null,true);
	return $log_arr;
} 

/**
 * @param string $lead_code
 * @param boolean $only_active
 *
 * @return void
 */
function post_to_connectplus_authservice($lead_code,$only_active=false){
	$wh_cond = ""; 
	if($only_active){ //at the time of order creation alone it will be true
		$wh_cond = " and GAU_USER_STATUS=1 ";
	}
	$sql1 = " select GPU_USER_NAME uname,GAU_APP_PCODE pcode,app.GID_VALIDITY_DATE edate,GAU_USER_STATUS,GPU_CONTACT_STATUS ".
			" from gft_install_dtl_new base ".
			" join gft_pos_users on (GPU_INSTALL_ID=base.GID_INSTALL_ID) ".
			" join gft_app_users on (GAU_INSTALL_ID=GPU_INSTALL_ID and GPU_CONTACT_ID=GAU_CONTACT_ID) ".
			" join gft_install_dtl_new app on (app.GID_LEAD_CODE=base.GID_LEAD_CODE and app.GID_LIC_PCODE=GAU_APP_PCODE) ".
			" where base.GID_LEAD_CODE='$lead_code' $wh_cond group by GAU_CONTACT_ID,GAU_APP_PCODE ";
	
	$sql2 = " select GPU_USER_NAME uname,app.GID_LIC_PCODE pcode,app.GID_VALIDITY_DATE edate,1 as GAU_USER_STATUS,GPU_CONTACT_STATUS ".
			" from gft_install_dtl_new base ".
			" join gft_pos_users on (GPU_INSTALL_ID=base.GID_INSTALL_ID) ".
			" join gft_install_dtl_new app on (app.GID_LEAD_CODE=base.GID_LEAD_CODE and app.GID_STATUS!='U') ".
			" join gft_product_family_master on (GPM_PRODUCT_CODE=app.GID_LIC_PCODE and GPM_LICENSE_MAPPING=2) ".
			" where base.GID_LEAD_CODE='$lead_code' and GPU_CONTACT_STATUS='A' group by GPU_USER_NAME,app.GID_LIC_PCODE ";
	
	$merge_que = "$sql1 union all $sql2 ";
	$res1 = execute_my_query($merge_que);
	$data_arr = /*. (string[string][string]) .*/array();
	while ($row1 = mysqli_fetch_array($res1)){
		$pos_user_name = $row1['uname'];
		$pcode = $row1['pcode'];
		$validity_date = $row1['edate']." 23:59:59";
		$utc_format = gmdate('Y-m-d H:i:s',strtotime($validity_date));
		if(!isset($data_arr[$pos_user_name]))
			$data_arr[$pos_user_name] = array();
		if( ($row1['GAU_USER_STATUS']=='1') && ($row1['GPU_CONTACT_STATUS']=='A') ){
			$lic_arr = array('applicationId'=>$pcode,'expiryDate'=>$utc_format);
			array_push($data_arr[$pos_user_name], $lic_arr);
		}
	}
	$post_arr = array();
	foreach ($data_arr as $user_name => $val_arr){
		$post_arr[] = array('userId'=>$user_name,'licenses'=>$val_arr);
	}
	$configs 	= get_connectplus_config();
	$cp_domain 	= (string)str_replace("{{customerId}}", $lead_code, $configs['cloud_domain']);
	if(count($post_arr) > 0){
		$post_data 	= json_encode($post_arr);
		$api_key 	= $configs['connect_api_key'];
		$header_arr = array("Content-Type: application/json","X-Api-Key: $api_key");
		$post_url  	= "$cp_domain/auth/customer/$lead_code/license";
		do_curl_to_connectplus($lead_code, $post_url, $post_data, $header_arr,"PUT");
	}
	
	$que3 = " select GID_LEAD_CODE,GID_VALIDITY_DATE from gft_lead_hdr_ext ".
	        " join gft_install_dtl_new on (GID_LEAD_CODE=GLE_LEAD_CODE and GID_STATUS!='U') ".
	        " where GID_LEAD_CODE='$lead_code' and GLE_USER_SYNC_TYPE='STANDALONE' and GID_LIC_PCODE=707 ";
	$res3 = execute_my_query($que3);
	if($row3 = mysqli_fetch_array($res3)){
	    $val_dt = $row3['GID_VALIDITY_DATE']." 23:59:59";
	    $post_arr = array("customer_id"=>$lead_code,"expiry_at"=>gmdate('Y-m-d H:i:s',strtotime($val_dt)));
	    $header_arr = array("Content-Type: application/json","X-Api-Key: ".$configs['tm_key']);
	    $post_url  	= "$cp_domain/task_manager/api/license";
	    do_curl_to_connectplus($lead_code, $post_url, json_encode($post_arr), $header_arr,"PUT");
	}
}
/**
 * @param string $resp
 * @param boolean $print_update_info
 * @return void
 */
function insert_update_wns_data($resp,$print_update_info=false) {
	global $uid;
	if($resp!='') {
		$time_stamp = date('Y-m-d H:i:s');
		$resp_arr = json_decode($resp,true);
		if(count($resp_arr)>0) {
			$insert_arr = /*.(string[string]).*/array();
			foreach ($resp_arr as $record) {
				$status = '1';
				$first_ack_dt = $ack_time = '';
				if(strcasecmp((string)$record['status'],'ACK')!=0) {
					$status = '0';
				} else {
					$first_ack_dt = db_date_format($record['last_ack_date']);
				}
				$first_ack_qry = execute_my_query("select GWD_LAST_ACK_TIME,gwd_first_ack_time,gwd_status from gft_wns_usage_data where gwd_lead_code='".(string)$record['customer_id']."'");
				if($row = mysqli_fetch_array($first_ack_qry)) {
					if($row['gwd_status']=='1') {
						$first_ack_dt = $row['gwd_first_ack_time'];
						$ack_time = $row['GWD_LAST_ACK_TIME'];
					}
				}
				$update_arr = /*.(string[string]).*/array();
				$update_arr['GWD_EXEC_ID'] = "0";
				$update_arr['GWD_LEAD_CODE'] = $record['customer_id'];
				$update_arr['GWD_ROUTING_KEY'] = $record['routing_key'];
				if(strtotime($ack_time)>=strtotime(db_date_format($record['last_ack_date']))) {
					continue;
				}
				$update_arr['GWD_LAST_ACK_TIME'] = db_date_format($record['last_ack_date']);
				if($first_ack_dt!='') {
					$update_arr['GWD_FIRST_ACK_TIME'] = $first_ack_dt;
				}
				$update_arr['GWD_STATUS'] = $status;
				$update_arr['GWD_DATETIME'] = "$time_stamp";
				array_update_tables_common($update_arr, "gft_wns_usage_data", array("GWD_LEAD_CODE"=>$record['customer_id']), null, $uid, null, null, $update_arr);
			}
		}
	}
}
/**
 * @param string $url
 * @param string $options
 * @param string $get_params
 * @param string $call_back_fn_name Response of curl request will be passed to call_back function
 * @return void
 */
function do_curl_request($url,$options,$get_params='',$call_back_fn_name='') {
	$url .= $get_params;
	$ch = curl_init($url);
	curl_setopt_array($ch,$options);
	$resp = curl_exec($ch);
	if($call_back_fn_name!='') {
		if(curl_getinfo($ch, CURLINFO_HTTP_CODE)=='200') {
			call_user_func($call_back_fn_name,$resp);
		}
	}
	curl_close($ch);
}

/**
 * @param int $no_of_days
 *
 * @return string
 */
function get_plan_type_based_on_days($no_of_days){
	$name = "";
	switch ($no_of_days){
		case 30 : $name = "MONTHLY";break;
		case 365 : $name = "YEARLY";break;
		default:$name="";break;
	}
	return $name;

}
/**
 * @param int $product_code
 * 
 * @return boolean
 */
function checkProductInPilot($product_code){
	$isAddonInPilot = false;
	$resultPilot = execute_my_query("select GPM_PRODUCT_CODE from gft_product_master where GPM_PRODUCT_CODE='$product_code' and GPM_PILOT_LICENSE='Y'");
	if(mysqli_num_rows($resultPilot)>0){
		$isAddonInPilot = true;
	}
	return $isAddonInPilot;
}
/**
 * @param string $glh_lead_code
 * @param string $product_code
 *
 * @return string
 */
function query_to_get_installed_addon_dtl($glh_lead_code,$product_code){
	$que3 = " select GPM_PRODUCT_CODE,GPM_PRODUCT_SKEW,GPM_SUBSCRIPTION_PERIOD,GPT_TYPE_DESC,GPMA_VALUE,".
			" GPM_NET_RATE,GPM_USD_RATE,GPM_PILOT_LICENSE,GPM_FREE_EDITION, GID_VALIDITY_DATE, GID_LIC_PCODE, ".
			" GID_LIC_PSKEW, GLH_CUST_NAME, GLH_COUNTRY, ifnull(GCO_CUST_QTY,GOP_QTY) as qty,GID_NO_CLIENTS from gft_install_dtl_new ".
			" join gft_product_master on (GID_LIC_PCODE=GPM_PRODUCT_CODE and GID_LIC_PSKEW=GPM_PRODUCT_SKEW) ".
			" join gft_product_master_attributes on (GPMA_PRODUCT_CODE=GPM_PRODUCT_CODE and GPMA_PRODUCT_SKEW=GPM_PRODUCT_SKEW and GPMA_ATTRIBUTE=9) ".
			" join gft_product_type_master on (GPT_TYPE_ID=GPM_PRODUCT_TYPE) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GID_LEAD_CODE) ".
			" left join gft_order_product_dtl on (GOP_ORDER_NO=GID_LIC_ORDER_NO and GOP_FULLFILLMENT_NO=GID_LIC_FULLFILLMENT_NO and GOP_PRODUCT_CODE=GID_LIC_PCODE and GOP_PRODUCT_SKEW=GID_LIC_PSKEW) ".
			" left join gft_cp_order_dtl on (GCO_ORDER_NO=GID_LIC_ORDER_NO and GCO_FULLFILLMENT_NO=GID_LIC_FULLFILLMENT_NO and GCO_PRODUCT_CODE=GID_LIC_PCODE and GCO_SKEW=GID_LIC_PSKEW) ".
			" where GID_LEAD_CODE='$glh_lead_code' and GID_LIC_PCODE='$product_code' and GID_STATUS!='U' ";
	return $que3;
}
/**
 * @param int $glh_lead_code
 * @param int $product_code
 * @param int $base_pcode
 * @param string $base_pgroup
 * @param boolean $isAddonInPilot
 *
 * @return mixed[]
 */
function get_customer_addon_dtl($glh_lead_code,$product_code,$base_pcode,$base_pgroup,$isAddonInPilot){
	$currency_format = "INR";
	$current_vers = $install_date = "";
	$que1 = "select GLH_LEAD_CODE,GLH_CUST_NAME,GLH_COUNTRY from gft_lead_hdr where GLH_LEAD_CODE='$glh_lead_code' ";
	$res1 = execute_my_query($que1);
	$row1 = mysqli_fetch_assoc($res1);
	$glh_cust_name = $row1['GLH_CUST_NAME'];
	$glh_country   = $row1['GLH_COUNTRY'];
	if(strcasecmp($glh_country, 'India')!=0){
		$currency_format = "USD";
	}
	$que2 = " select GPG_PRODUCT_ALIAS from gft_product_group_master where gpg_product_family_code='$base_pcode' and gpg_skew='$base_pgroup' ";
	$prod_alias = get_single_value_from_single_query("GPG_PRODUCT_ALIAS", $que2);
	$addon_plan_arr = array();
	$que3 = query_to_get_installed_addon_dtl($glh_lead_code,$product_code);
	$res3 = execute_my_query($que3);
	$hasAgreedTermsAndConditions = false;
	$resultAgreement = execute_my_query("select GCL_ID from gft_customer_license_agreement_dtl where GCL_PRODUCT_CODE='$product_code' AND GCL_LEAD_CODE='$glh_lead_code' limit 1");
	if(mysqli_num_rows($resultAgreement)>0){
		$hasAgreedTermsAndConditions = true;
	}
	$base_plan_dtl = null;
	$licenseType   = "NA";
	$expiryDate		= null;
	if($row3 = mysqli_fetch_array($res3)){
		$sam_sku 	= $row3['GPM_PRODUCT_CODE']."-".$row3['GPM_PRODUCT_SKEW'];
		$plan_type 	= get_plan_type_based_on_days((int)$row3['GPM_SUBSCRIPTION_PERIOD']);
		$price		= ($currency_format=='INR')?(int)$row3['GPM_NET_RATE']:(int)$row3['GPM_USD_RATE'];
		$expiryDate = ($row3['GID_VALIDITY_DATE']!=""?$row3['GID_VALIDITY_DATE']:null);
		$licenseType = ($row3['GPM_PILOT_LICENSE']=='Y'?"PILOT":($row3['GPM_FREE_EDITION']=='Y'?"TRIAL":"SUBSCRIPTION"));
		$base_plan_dtl = array(
		        'name'=>($row3['GPM_FREE_EDITION']=='Y') ? 'Trial': $row3['GPT_TYPE_DESC'],
				'maxByte'=>(float)$row3['GPMA_VALUE'],
				'quantity'=>$row3['GID_NO_CLIENTS'],
				'type'=>$plan_type,
				'price'=>$price,
				'samSKU'=>$sam_sku
		);
	}
	$baseProductQuery =	" select GID_INSTALL_DATE,GID_CURRENT_VERSION from gft_install_dtl_new ".
                	   	" JOIN gft_product_master pm ON(GPM_PRODUCT_CODE=GID_LIC_PCODE AND GPM_PRODUCT_SKEW=GID_LIC_PSKEW)".
                	   	" where gid_lead_code=$glh_lead_code".
                	   	" AND GID_LIC_PCODE='$base_pcode' AND GID_LIC_PSKEW LIKE '$base_pgroup%'".
                	   	" AND GID_STATUS!='U' order by GPM_LICENSE_TYPE limit 1";
	$baseResult = execute_my_query($baseProductQuery);
	if($brow = mysqli_fetch_array($baseResult)){
	    $current_vers  = $brow['GID_CURRENT_VERSION'];
	    $install_date  = $brow['GID_INSTALL_DATE'];
	}
	$addonPlans			=	array();
	$suggestedBasePlan	=	array();
	$suggestedAddonPlan	=	array();
	$q1 =  " select CONCAT(GCP_PRODUCT_CODE,'-',GCP_PRODUCT_SKEW) PRODUCT,GCP_QUANTITY,GPMA_VALUE,GPM_SUBSCRIPTION_PERIOD,GPM_NET_RATE,GPM_USD_RATE ".
	   	   " from gft_customer_purchased_addon ".
	   	   " join gft_product_master on (GPM_PRODUCT_CODE=GCP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GCP_PRODUCT_SKEW) ".
	   	   " join gft_product_master_attributes on (GPMA_PRODUCT_CODE=GPM_PRODUCT_CODE and GPMA_PRODUCT_SKEW=GPM_PRODUCT_SKEW and GPMA_ATTRIBUTE=9) ".
	   	   " join gft_product_type_master on (GPT_TYPE_ID=GPM_PRODUCT_TYPE) ".
	       " where GCP_IS_ACTIVE_PLAN='Y' AND GCP_LEAD_CODE='$glh_lead_code' ";
	$resultAddon 		= 	execute_my_query($q1);
	while($rowAddon=mysqli_fetch_assoc($resultAddon)){
		$addonPlans[]	=	array(
		    "name"=>"1 GB",
		    "quantity"=>$rowAddon['GCP_QUANTITY'],
		    "maxByte"=>(int)$rowAddon['GPMA_VALUE'],
		    "type"=>get_plan_type_based_on_days((int)$rowAddon['GPM_SUBSCRIPTION_PERIOD']),
		    "price"=>($currency_format=='INR')?(int)$rowAddon['GPM_NET_RATE']:(int)$rowAddon['GPM_USD_RATE'],
		    "samSKU"=>$rowAddon['PRODUCT'], 
		);
	}
	$resultSuggestedPlans = execute_my_query("select GAP_PRODUCT_CODE,GFT_SKEW_PROPERTY,GAP_NUMBER_OF_MONTHS, GAP_QUANTITY  from gft_addon_plan_suggestion_to_customer ".
			" INNER JOIN gft_product_master ON(GAP_PRODUCT_CODE=CONCAT(GPM_PRODUCT_CODE,'-',GPM_PRODUCT_SKEW)) ".
			" where GAP_PAYMENT_STATUS='N' AND GAP_LEAD_CODE='$glh_lead_code'");
	while($rowSuggestion=mysqli_fetch_assoc($resultSuggestedPlans)){
		if($rowSuggestion['GFT_SKEW_PROPERTY']=='18'){
		    $suggestedBasePlan[] = array("samSKU"=>$rowSuggestion['GAP_PRODUCT_CODE'],"requiredPeriod"=>(int)$rowSuggestion['GAP_NUMBER_OF_MONTHS'],"requiredUnits"=>(int)$rowSuggestion['GAP_QUANTITY']);	
		}else{
			$suggestedAddonPlan[]= array("samSKU"=>$rowSuggestion['GAP_PRODUCT_CODE'],"requiredPeriod"=>(int)$rowSuggestion['GAP_NUMBER_OF_MONTHS'],"requiredUnits"=>(int)$rowSuggestion['GAP_QUANTITY']);
		}
	}
	$suggestedUpgradePlan = array();
	if(count($suggestedBasePlan)>0 || count($suggestedAddonPlan)>0){		
		$suggestedUpgradePlan = array("basePlans"=>$suggestedBasePlan,"addonPlans"=>$suggestedAddonPlan);
	}
	$order_dtl = get_order_dtl_of_lead($glh_lead_code,null,false,$product_code);
	$canAvailTrial = (count($order_dtl) > 0) ? false : true;
	
	$out_arr = array(
			'customerId'=>$glh_lead_code,
			'customerName'=>$glh_cust_name,
	        'contactDetails'=>get_contact_dtl_for_designation($glh_lead_code, "1,4", "1,2"),
			'currency'=>$currency_format,
			'baseProduct'=>$prod_alias,
			'baseProductInstallationDate'=>$install_date,
	        'baseProductVersion'=>$current_vers,
			'addonProductCode'=>$product_code,
			'isAddonInPilot'=>$isAddonInPilot,
			'licenseType'=>$licenseType,
	        'canAvailTrial'=>$canAvailTrial,
			'expiryDate'=>$expiryDate,
			'hasAgreedTermsAndConditions'=>$hasAgreedTermsAndConditions,
			'basePlan'=>$base_plan_dtl,
			'addonPlans'=>$addonPlans,
			'suggestedUpgradePlan'=>(count($suggestedUpgradePlan)>0?$suggestedUpgradePlan:null)
		);
	return $out_arr;
}

/**
 * @param string $customerId
 * @param string $userId
 * 
 * @return string[int][string]
 */
function get_license_info($customerId,$userId){
    global $mydelight_tm_cust_id,$mygofrugal_task_manger_customer_id;
    $sql1 = " select GAU_APP_PCODE,app.GID_VALIDITY_DATE,base.GID_VALIDITY_DATE as base_validity ".
        " from gft_install_dtl_new base ".
        " join gft_app_users on (GAU_INSTALL_ID=base.GID_INSTALL_ID) ".
        " join gft_pos_users on (GPU_INSTALL_ID=base.GID_INSTALL_ID and GPU_CONTACT_ID=GAU_CONTACT_ID) ".
        " join gft_install_dtl_new app on (app.GID_LEAD_CODE=base.GID_LEAD_CODE and app.GID_LIC_PCODE=GAU_APP_PCODE) ".
        " where base.GID_LEAD_CODE='$customerId' and GPU_USER_NAME='$userId' and GAU_USER_STATUS=1 and GPU_CONTACT_STATUS='A' ".
        " group by GAU_APP_PCODE ";
    $res1 = execute_my_query($sql1);
    $resp_arr = /*. (string[int][string]) .*/array();
    while ($row1 = mysqli_fetch_array($res1)){
        $validity_date = $row1['GID_VALIDITY_DATE']." 23:59:59";
        $utc_format = gmdate('Y-m-d H:i:s',strtotime($validity_date));
        $arr['productId'] = $row1['GAU_APP_PCODE'];
        $arr['expiryDate'] = $utc_format;
        $resp_arr[] = $arr;
    }
    
    //Licenses other than user based. That is for all users under that customer id
    $sql2 = " select GID_LIC_PCODE,GID_VALIDITY_DATE from gft_install_dtl_new ".
        " join gft_product_family_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE) ".
        " where GID_LEAD_CODE='$customerId' and GPM_LICENSE_MAPPING=2 and GID_STATUS!='U' ";
    $res2 = execute_my_query($sql2);
    while($row2 = mysqli_fetch_array($res2)){
        $gid_lic_pcode  = $row2['GID_LIC_PCODE'];
        $gid_valid_date = $row2['GID_VALIDITY_DATE'];
        $resp_arr[] = array('productId'=>$gid_lic_pcode,'expiryDate'=>"$gid_valid_date 18:29:59");
    }
    $resp_arr[] = array('productId'=>'716','expiryDate'=>'2020-12-30 18:29:59');
    if( in_array($customerId, array($mydelight_tm_cust_id,$mygofrugal_task_manger_customer_id,'121769')) || (is_product_installed($customerId, '707')) ){ //task manager permission for all gofrugal employees and mygofrugal users
        $utc_format = gmdate('Y-m-d H:i:s',strtotime('2099-12-31 23:59:59'));
        $arr['productId'] = '902';
        $arr['expiryDate'] = $utc_format;
        $resp_arr[] = $arr;
    }
    return $resp_arr;
}

/**
 * @param string $cust_id
 * @param string $plan_duration
 * 
 * @return string[string]
 */
function get_gosecure_suggested_bucket($cust_id,$plan_duration='YEARLY'){
    $pcode = '706';
    $suggested_bucket_dtl = array();
    $q1 = " select GID_ORDER_NO,GID_FULLFILLMENT_NO,GID_LIC_PCODE,GID_LIC_PSKEW ".
        " from gft_install_dtl_new ".
        " join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
        " where GID_LEAD_CODE='$cust_id' and GID_STATUS!='U' ".
        " and GID_LIC_PCODE in (200,500) order by GPM_LICENSE_TYPE, GID_VALIDITY_DATE desc ";
    $r1 = execute_my_query($q1);
    if($d1 = mysqli_fetch_array($r1)){
        $identity = $d1['GID_ORDER_NO'].substr("0000".$d1['GID_FULLFILLMENT_NO'], -4).$d1['GID_LIC_PCODE'].substr(str_replace(".","",$d1['GID_LIC_PSKEW']),0,5);
        $eidentity = strtoupper(md5($identity));
        $header_arr = array("X-identity:$identity","X-eidentity:$eidentity","X-product-code:$pcode");
        $config = get_connectplus_config();
        $cloud_url = str_replace("{{customerId}}", $cust_id, $config['cloud_domain'])."/gosecure-subscription/api/suggested-plans";
        $ch = curl_init($cloud_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
        $resp_body = (string)curl_exec($ch);
        $resp_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($resp_code==200){
            $json_arr = json_decode($resp_body,true);
            $suggest_arr = isset($json_arr['suggestedPlans']) ? $json_arr['suggestedPlans'] : array();
            foreach ($suggest_arr as $varr){
                if( isset($varr['type']) && (strcasecmp($varr['type'], $plan_duration)==0) ){
                    $name   = isset($varr['minimumBucket']['name'])?$varr['minimumBucket']['name']:"";
                    $qty    = isset($varr['minimumBucket']['planPrices'][0]['requiredUnits'])?$varr['minimumBucket']['planPrices'][0]['requiredUnits']:"";
                    $id     = get_single_value_from_single_table("GPT_TYPE_ID", "gft_product_type_master", "GPT_TYPE_DESC", $name);
                    $suggested_bucket_dtl = array("name"=>$name,"id"=>$id,"qty"=>$qty);
                }
            }
        }else{
            error_log("Gosecure suggested bucket log => code : $resp_code , body $resp_body");
        }
    }
    return $suggested_bucket_dtl;
}

/**
 * 
 * @param string $lead_code
 * @param string $pskew
 * @param int $ordered_qty
 * 
 * @return string
 */
function validate_gosecure_suggested_bucket($lead_code,$pskew,$ordered_qty){
    $err_msg = "";
    if(strpos(get_samee_const("Skip_Gosecure_Validation"),$lead_code)!==false){
        return $err_msg;
    }
    $que_res = execute_my_query(" select GPM_PRODUCT_TYPE,GPM_SUBSCRIPTION_PERIOD from gft_product_master where GPM_PRODUCT_CODE=706 and GPM_PRODUCT_SKEW='$pskew' and GFT_SKEW_PROPERTY=18 ");
    if($que_row = mysqli_fetch_array($que_res)){
        $plan_days       = (int)$que_row['GPM_SUBSCRIPTION_PERIOD'];
        $ordered_edition = (int)$que_row['GPM_PRODUCT_TYPE'];
        $plan_duration   = ($plan_days==30) ? "MONTHLY" : "YEARLY";
        $suggested_bucket = get_gosecure_suggested_bucket($lead_code,$plan_duration);
        $bucket_edition = isset($suggested_bucket['id']) ? (int)$suggested_bucket['id'] : 0;
        $bucket_name    = isset($suggested_bucket['name']) ? (string)$suggested_bucket['name'] : '';
        $bucket_qty     = isset($suggested_bucket['qty']) ? (int)$suggested_bucket['qty'] : 0;
        if( ($bucket_edition!=0) && (($ordered_edition < $bucket_edition) || ($ordered_qty < $bucket_qty)) ){
            $err_msg = "Gosecure suggested bucket is $bucket_name and qty is $bucket_qty, you can't order gosecure less than that.";
        }
    }
    return $err_msg;
}

/**
 * @param string $outlet_lead_code
 * 
 * @return void
 */
function post_order_split_info_to_integration_portal($outlet_lead_code){
    $lic_arr = /*. (string[int][string]) .*/array();
    $que1 = " select GID_LIC_PCODE,GPG_PRODUCT_NAME,GID_ORDER_NO,GID_FULLFILLMENT_NO,GID_VALIDITY_DATE ".
            " from gft_install_dtl_new ".
            " join gft_product_family_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE) ".
            " join gft_product_group_master on (gpg_product_family_code=gpm_head_family and gpg_skew=substr(GID_LIC_PSKEW,1,4)) ".
            " where GID_LEAD_CODE='$outlet_lead_code' and GPM_CATEGORY=6 and GID_STATUS!='U' ";
    $res1 = execute_my_query($que1);
    while($row1 = mysqli_fetch_array($res1)){
        $lic_arr[] = array(
            "skewCode"      => $row1['GID_LIC_PCODE'],
            "skewName"      => $row1['GPG_PRODUCT_NAME'],
            "addOnOrderNo"  => $row1['GID_ORDER_NO'].substr("0000".$row1['GID_FULLFILLMENT_NO'],-4),
            "expriyDate"    => $row1['GID_VALIDITY_DATE'],
            "status"        => "Active"
        );
    }
    $que2 = " select GLH_CUST_NAME,GLH_CUST_STREETADDR2,GLH_CUST_CITY,GLH_REFERENCE_GIVEN ".
            " from gft_lead_hdr where GLH_LEAD_CODE='$outlet_lead_code' ";
    $res2 = execute_my_query($que2);
    if($row2 = mysqli_fetch_array($res2)){
        $corporate_id = ((int)$row2['GLH_REFERENCE_GIVEN'] > 0) ? $row2['GLH_REFERENCE_GIVEN'] : $outlet_lead_code;
        $split_arr= array(
            "customerId" => $outlet_lead_code,
            "shopName"   => $row2['GLH_CUST_NAME'],
            "city"       => $row2['GLH_CUST_CITY'],
            "address"    => $row2['GLH_CUST_STREETADDR2'],
            "status"     => "Active",
            "orderNo"    => "0",
            'licenseDetails'=>$lic_arr,
        );
        $configs 	= get_connectplus_config();
        $data_arr   = array('corporateCustomerId'=>$corporate_id,"orderSplit"=>array($split_arr));
        $header_arr = array("Content-Type: application/json");
        $post_url  	= $configs["integ_portal"]."/location-tracker/order-split";
        do_curl_to_connectplus($outlet_lead_code, $post_url, json_encode($data_arr), $header_arr);
    }
}

/**
 * @param string $cust_id
 * 
 * @return string[int]
 */
function get_base_installation_dtl($cust_id){
    $que2 = " select GID_ORDER_NO,GID_FULLFILLMENT_NO,GID_STORE_URL,GID_LIC_PCODE,GID_LIC_PSKEW from gft_install_dtl_new ".
        " join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
        " join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
        " where GID_LEAD_CODE='$cust_id' and GPM_IS_BASE_PRODUCT='Y' and GID_STATUS!='U' ".
        " order by GPM_LICENSE_TYPE limit 1 ";
    $res2 = execute_my_query($que2);
    $ret_arr = /*. (string[int]) .*/array();
    if($row2 = mysqli_fetch_array($res2)){
        $ret_arr[0] = $row2['GID_ORDER_NO'].substr("0000".$row2['GID_FULLFILLMENT_NO'],-4);
        $ret_arr[1] = $row2['GID_LIC_PCODE'];
        $ret_arr[2] = $row2['GID_LIC_PSKEW'];
        $ret_arr[3] = $row2['GID_STORE_URL'];
    }
    return $ret_arr;
}

/**
 * @param string $customerId
 * @param string $productCode
 * @param string $domain_password
 * @param string $email_id
 * @param string $ret_type
 *
 * @return void
 */
function account_posting_to_integration_portal($customerId,$productCode,$domain_password='',$email_id='',$ret_type=''){
    $supported_addons = array('732','522','762','538');
    if(!in_array($productCode, $supported_addons)){
        return;
    }
    $resp = array();
    $que1 = " select GLH_LEAD_CODE,GLH_CUST_NAME,GLH_CUST_STREETADDR2,GLH_AREA_NAME,GLH_CUST_CITY,GLH_CUST_PINCODE, ".
        " GLH_CUST_STATECODE,GLH_COUNTRY,GTM_TYPE_NAME,if(GLH_LEAD_TYPE=13 and GLH_LEAD_SOURCECODE=7,glh_reference_given,'') corporate_id ".
        " from gft_lead_hdr ".
        " join gft_vertical_master on (GTM_VERTICAL_CODE=GLH_VERTICAL_CODE) ".
        " join gft_type_master on (GTM_TYPE_CODE=GTM_BUSINESS_TYPE) ".
        " where GLH_LEAD_CODE='$customerId' ";
    $res1 = execute_my_query($que1);
    if($row1 = mysqli_fetch_array($res1)){
        $base_install_dtl = get_base_installation_dtl($customerId);
        $root_order = isset($base_install_dtl[0])?$base_install_dtl[0]:'';
        $root_pcode = isset($base_install_dtl[1])?$base_install_dtl[1]:'';
        $root_pskew = isset($base_install_dtl[2])?$base_install_dtl[2]:'';
        $domain_url = isset($base_install_dtl[3])?$base_install_dtl[3]:'';
        $license_arr = /*. (string[int][string]) .*/array();
        
        $que3 = " select GID_LIC_PCODE,GPM_PRODUCT_NAME,GID_ORDER_NO,GID_FULLFILLMENT_NO,GID_VALIDITY_DATE ".
            " from gft_install_dtl_new join gft_product_family_master on (GID_LIC_PCODE=GPM_PRODUCT_CODE) ".
            " where GID_LEAD_CODE='$customerId' and GID_STATUS!='U' and GID_LIC_PCODE in (".implode(",", $supported_addons).") ";
        $res3 = execute_my_query($que3);
        while ($row3 = mysqli_fetch_array($res3)){
            $license_arr[] = array(
                "skewName" => $row3['GPM_PRODUCT_NAME'],
                "skewCode" => $row3['GID_LIC_PCODE'],
                "subscriptionValidity" => $row3['GID_VALIDITY_DATE'],
                "subscriptionOrderNumber" => $row3['GID_ORDER_NO'].substr("0000".$row3['GID_FULLFILLMENT_NO'],-4),
                "status" => "active",
                "module" => "bank gateway",
            );
        }
        if((count($license_arr)==0) && ($ret_type!='data')){
            return;
        }
        if($email_id==""){
            $email_id = get_single_value_from_single_query("GCC_CONTACT_NO", "select GCC_CONTACT_NO from gft_customer_contact_dtl where gcc_contact_type=4 AND GCC_LEAD_CODE='$customerId' order by gcc_designation ASC limit 1");
        }
        $passcode = generatePassword(8);
        $que4 = "select GSG_PASS_CODE from gft_sms_gateway_info where GSG_LEAD_CODE='$customerId' and GSG_PASS_CODE!='' order by GSG_START_DATE limit 1 ";
        $res4 = execute_my_query($que4);
        if($row4 = mysqli_fetch_array($res4)){
            $passcode = $row4['GSG_PASS_CODE'];
        }else if($ret_type!='data'){
            $insert_arr = array(
                "GSG_ORDER_NO"=>substr($root_order, 0,15),
                "GSG_FULLFILLMENT_NO"=>(int)substr($root_order,15,4),
                "GSG_PRODUCT_CODE"=>$root_pcode,"GSG_PRODUCT_SKEW"=>$root_pskew,
                "GSG_LEAD_CODE"=>$customerId,"GSG_PASS_CODE"=>$passcode
            );
            array_insert_query("gft_sms_gateway_info", $insert_arr);
        }
        $corporate_dtl = null;
        $corporate_id = $row1['corporate_id'];
        if($corporate_id!=''){
            $cque = " select GLH_CUST_NAME,GLH_CUST_STREETADDR2,GLH_AREA_NAME,GLH_CUST_CITY,GLH_CUST_PINCODE, ".
                    " GLH_CUST_STATECODE,GLH_COUNTRY,GTM_TYPE_NAME from gft_lead_hdr ".
                    " join gft_vertical_master on (GTM_VERTICAL_CODE=GLH_VERTICAL_CODE) ".
                    " join gft_type_master on (GTM_TYPE_CODE=GTM_BUSINESS_TYPE) ".
                    " where GLH_LEAD_CODE='$corporate_id' ";
            $cres = execute_my_query($cque);
            if($crow = mysqli_fetch_array($cres)){
                $corp_base_dtl = get_base_installation_dtl($corporate_id);
                $corporate_dtl = array(
                    'corporateCustomerId'=> $corporate_id,
                    'shopName'          =>  $crow['GLH_CUST_NAME'],
                    'streetName'        =>  $crow['GLH_CUST_STREETADDR2'],
                    'areaName'          =>  $crow['GLH_AREA_NAME'],
                    'city'              =>  $crow['GLH_CUST_CITY'],
                    'pincode'           =>  $crow['GLH_CUST_PINCODE'],
                    'state'             =>  $crow['GLH_CUST_STATECODE'],
                    'country'           =>  $crow['GLH_COUNTRY'],
                    'businessType'      =>  $crow['GTM_TYPE_NAME'],
                    'status'            =>  "active",
                    'rootOrderNumber'   =>  isset($corp_base_dtl[0])?$corp_base_dtl[0]:'',
                    'domainUrl'         =>  isset($corp_base_dtl[3])?$corp_base_dtl[3]:'',
                );
            }
        }
        $post_arr = array(
            'outletCustomerId'  =>  $row1['GLH_LEAD_CODE'],
            'corporateCustomerId'=> $corporate_id,
            'shopName'          =>  $row1['GLH_CUST_NAME'],
            'streetName'        =>  $row1['GLH_CUST_STREETADDR2'],
            'areaName'          =>  $row1['GLH_AREA_NAME'],
            'city'              =>  $row1['GLH_CUST_CITY'],
            'pincode'           =>  $row1['GLH_CUST_PINCODE'],
            'state'             =>  $row1['GLH_CUST_STATECODE'],
            'country'           =>  $row1['GLH_COUNTRY'],
            'businessType'      =>  $row1['GTM_TYPE_NAME'],
            'emailId'           =>  $email_id,
            'status'            =>  "active",
            'passcode'          =>  $passcode,
            'rootOrderNumber'   =>  $root_order,
            'domainUrl'         =>  $domain_url,
            'domainPassword'    =>  $domain_password,
            'licenseDetails'    =>  $license_arr,
            'corporateOutletDetails' => $corporate_dtl
        );
        if($ret_type=='data'){
            return $post_arr;
        }
        $portal_config = get_connectplus_config();
        $post_url = $portal_config['integ_portal']."/accounts/integration-provisioning";
        $auth_token = $portal_config['dealer_token'];
        $resp = do_curl_to_connectplus($customerId, $post_url, json_encode($post_arr), array("Content-Type:application/json","X-Auth-Token:$auth_token"));
    }
    return $resp;
}

/**
 * @param string $cust_id
 * 
 * @return void
 */
function post_instock_custom_license($cust_id){
    $que1 = " select GLH_CUST_NAME,fm.GPM_PRODUCT_CODE,GPM_PRODUCT_NAME,GAF_CODE,GAF_NAME,GAI_NO_OF_DEVICE,GPM_LICENSE_TYPE, ".
        " date(GAI_INSTALLED_DATETIME) GAI_INSTALLED_DATETIME, date(GAI_VALIDITY_DATETIME) GAI_VALIDITY_DATETIME ".
        " from gft_addon_feature_install_dtl ".
        " join gft_addon_feature_master on (GAF_ID=GAI_FEATURE_ID) ".
        " join gft_lead_hdr on (GLH_LEAD_CODE=GAI_LEAD_CODE) ".
        " join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=GAI_PRODUCT_CODE) ".
        " join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GAI_PRODUCT_CODE and GPM_PRODUCT_SKEW=GAI_PRODUCT_SKEW) ".
        " where GLH_LEAD_CODE='$cust_id' and fm.GPM_PRODUCT_CODE='526' and GAI_STATUS='A' ";
    $res1 = execute_my_query($que1);
    $custom_license = array();
    $cust_name = $prod_code = $prod_name = "";
    $vdate_arr = /*. (string[int]) .*/array();
    while ($row1 = mysqli_fetch_array($res1)){
        $cust_name = $row1['GLH_CUST_NAME'];
        $prod_code = $row1['GPM_PRODUCT_CODE'];
        $prod_name = $row1['GPM_PRODUCT_NAME'];
        $vdate_arr[] = date("Y-m-d",strtotime($row1['GAI_VALIDITY_DATETIME']));
        $custom_license[] = array(
            "product_code"          => $prod_code,
            "custom_license_code"   => $row1['GAF_CODE'],
            "custom_license_name"   => $row1['GAF_NAME'],
            "license_count"         => (int)$row1['GAI_NO_OF_DEVICE'],
            "license_type"          => ($row1['GPM_LICENSE_TYPE']=='3') ? "trial" : "live",
            "install_date"          => $row1['GAI_INSTALLED_DATETIME'],
            "expiry_date"           => $row1['GAI_VALIDITY_DATETIME']
        );
    }
    if(count($vdate_arr) > 0){
        $max_vd = max($vdate_arr);
        $selque = " select GID_INSTALL_ID,GID_ORDER_NO,GID_FULLFILLMENT_NO from gft_install_dtl_new where GID_LEAD_CODE='$cust_id' and GID_LIC_PCODE=526 and GID_STATUS!='U' ";
        $queres = execute_my_query($selque);
        if($querow = mysqli_fetch_assoc($queres)){
            execute_my_query("update gft_install_dtl_new set GID_VALIDITY_DATE='$max_vd' where GID_INSTALL_ID='".$querow['GID_INSTALL_ID']."' ");
            post_customer_and_plan_to_notification_server($cust_id,$querow['GID_ORDER_NO'],$querow['GID_FULLFILLMENT_NO'],$max_vd);
        }
    }
    $info_arr = array();
    if(count($custom_license) > 0){
        $info_arr[] = array(
            'customer_id'=>$cust_id,
            'customer_name'=>$cust_name,
            'product_code'=>$prod_code,
            'product_name'=>$prod_name,
            'validation_type'=>"Device",
            'custom_licenses'=>$custom_license
        );
    }
    $api_config = get_connectplus_config();
    $cp_domain 	= (string)str_replace("{{customerId}}", $cust_id, $api_config['cloud_domain']);
    $api_key    = $api_config['instock_key'];
    $post_url   = "$cp_domain/instock/api/v1/licenseinfo";
    $header_arr = array("Content-Type:application/json","X-Api-Key:$api_key");
    do_curl_to_connectplus($cust_id,$post_url, json_encode($info_arr), $header_arr);
}

/**
 * @param string $domain_name
 *
 * @return boolean
 */
function is_connectplus_domain_available($domain_name){
    $domain_name = trim($domain_name);
    if(strlen($domain_name) < 3){
        return false;
    }
    $que1 = " select GCD_DOMAIN from gft_connectplus_domains where GCD_DOMAIN='".mysqli_real_escape_string_wrapper($domain_name)."' ";
    $res1 = execute_my_query($que1);
    if(mysqli_num_rows($res1)==0){
        return true;
    }
    return false;
}

?>
