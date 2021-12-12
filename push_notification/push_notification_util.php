<?php
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__.'/../connectplus/connectplus_util.php');
require_once(__DIR__.'/../function.insert_stmt.php');

function send_gcm_push_notification($request_sent,$app='mydelight'){
	
	if($app=='mydelight'){
		$api_staging_key	=	"AIzaSyBNh3nkPxkkmHh9Z3CW7019jfBzRNdrMMg";
		$api_prod_key		=	"AIzaSyACt8fX92tlIJT-SUZl08vnW6nqm5D0Njs";
	}else if($app=='mygofrugal'){
		$api_staging_key = "AIzaSyA5okFLkIAXgryhT0hFTUJey0WuLfwwm7Y";
		$api_prod_key 	= "AIzaSyAwfyvIk1G3UQ2H0K3Le1uzQbxzGH2EX_0";
	}else{
		return 'Invalid App';
	}
	
	$mode = (int)get_samee_const("SAM_API_MODE");
	$api_key = $api_staging_key;
	if($mode==0){
		$api_key = $api_prod_key;
	}
	$headers = array('Authorization: key=' . $api_key,'Content-Type: application/json');
	$ch = curl_init();
	curl_setopt( $ch,CURLOPT_URL, 'https://gcm-http.googleapis.com/gcm/send' );
	curl_setopt( $ch,CURLOPT_POST, true );
	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch,CURLOPT_POSTFIELDS, $request_sent );
	$result = curl_exec($ch );
	curl_close( $ch );
	return $result;
}

function send_apns_push_notification($registration_id,$message,$certificate_file){
	// Put your device token here (without spaces):
	$deviceToken = $registration_id;
	
	$ctx = stream_context_create();
	stream_context_set_option($ctx, 'ssl', 'local_cert', $certificate_file);
	
	try{
		// Open a connection to the APNS server
		$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err,$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		if (!$fp)
			return "Failed to connect: $err $errstr";
	}catch (Exception $ex){
		error_log("Following Exception Occured in APNS push notification service - ".$ex);
		return 'Exception';
	}
	
	// Create the payload body
	$body['aps'] = array(
			'alert' => $message,
			'sound' => 'default'
	);
	
	// Encode the payload as JSON
	$payload = json_encode($body);
	$notification_expiry =  time() + (2 * 24 * 60 * 60); //2 days
	// Build the binary notification
	$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload . chr(4) . pack('n', 4) . pack('N', $notification_expiry);
	
	// Send it to the server
	$result = fwrite($fp, $msg, strlen($msg));
	
	// Close the connection to the server
	fclose($fp);
	
	return $result;
}

/**
 * @param int $employee_id
 * @param string $certificate_path
 *
 * @return void
 */
function send_notification_to_mydelight_app_via_gcm($employee_id=0,$certificate_path="push_notification/mydelight_apple_cert.pem"){
	$emp_id_filter	=	($employee_id!=0)?"  and GPN_EMP_ID='$employee_id' " : "";
	$expity_dt		=	date('Y-m-d H:i:s',strtotime("-12 hours",strtotime(date('Y-m-d H:i:s'))));
	$sql_get_notif	=	" select GPN_ID,GPN_TITLE,ea.GEK_DEVICE_PLATFORM,ea.GEK_GCM_REGISTER_ID,GPN_MESSAGE ".
			" from  gft_gcm_push_notification ".
			" join gft_emp_auth_key ea on (ea.EMP_ID=GPN_EMP_ID) ".
			" where GPN_STATUS in (1) and GPN_FOR_APP=1 and GPN_CREATED_DATE_TIME>'$expity_dt' $emp_id_filter ";
	$res_notif		=	execute_my_query($sql_get_notif);
	while($row=mysqli_fetch_array($res_notif)){
		$gpn_id				=	$row['GPN_ID'];
		$mobile_platform	=	$row['GEK_DEVICE_PLATFORM'];
		$registration_id	=	$row['GEK_GCM_REGISTER_ID'];
		$wh_condition		= " and GPN_ID='$gpn_id' ";
		$notification_title = $row['GPN_TITLE'];
		if($mobile_platform=='1'){
			$msg 			= array('title'	=> $notification_title,'message' => strip_tags($row['GPN_MESSAGE']),'subtitle'=>'','vibrate'=> 1,'sound'=> 1,'image'=>'');
			$fields 		= array('registration_ids' => array($registration_id),'data'=> $msg, 'time_to_live'=>172800); //2 days <=> 172800 seconds
			$request_sent	= json_encode( $fields );
			$response_msg 	= send_gcm_push_notification($request_sent,'mydelight');
			execute_my_query("update gft_gcm_push_notification set GPN_GCM_REQUEST_SENT='".mysqli_real_escape_string_wrapper($request_sent)."',GPN_GCM_RESPONSE='$response_msg',GPN_STATUS=2,GPN_SENT_DATE_TIME=now() where 1 $wh_condition ");
		}else if($mobile_platform=='2'){
			$notify_msg = strip_tags($notification_title." - ".$row['GPN_MESSAGE']);
			$response_id = send_apns_push_notification($registration_id,$notify_msg,$certificate_path);
			execute_my_query("update gft_gcm_push_notification set GPN_GCM_REQUEST_SENT='".mysqli_real_escape_string_wrapper($notify_msg)."',GPN_GCM_RESPONSE='$response_id',GPN_STATUS=2,GPN_SENT_DATE_TIME=now() where 1 $wh_condition ");
		}
	}
}

/**
 * @param int $employee_id
 * @param int $ns_id
 *
 * @return void
 */
function send_notification_to_mydelight_app($employee_id=0,$ns_id=0){
    global $mydelight_tm_cust_id;
    $app_auth_token = get_single_value_from_single_table("GPM_NS_REGISTRATION_KEY", "gft_product_family_master", "GPM_PRODUCT_CODE", "702");
    $header_arr = array("Content-Type: application/json","X-PROJECT-AUTH-TOKEN: $app_auth_token");
    $ns_config 	= get_notification_server_config();
    $send_ns_url = $ns_config['send_notification_url'];
    $id_filter	= ($employee_id!=0)?"  and GPN_EMP_ID='$employee_id' " : "";
    $id_filter	.= ($ns_id!=0)?" and GPN_ID='$ns_id' " : "";
    $expity_dt	= date('Y-m-d H:i:s',strtotime("-6 hours",strtotime(date('Y-m-d H:i:s'))));
    $sql_get	= " select GPN_ID,GPN_TITLE,GPN_LEAD_CODE,GPN_MESSAGE,GPN_NOTIFICATION_TYPE,GPN_NOTI_REFERENCE_ID, ".
        		  " GLH_STATUS,GLH_PROSPECTS_STATUS,GPV_VIDEO_LINK,GLM_LOGIN_NAME,GPN_CREATED_DATE_TIME, ".
		          " GPN_OTHER_REFERENCE_DTL,concat(GPN_NOTIFICATION_TYPE,ifnull(GPN_TITLE,''),ifnull(GPN_MESSAGE,''),ifnull(GPN_LEAD_CODE,'')) as uniq_text ".
        		  " from gft_gcm_push_notification ".
		          " join gft_emp_auth_key ea on (ea.EMP_ID=GPN_EMP_ID) ".
        		  " join gft_login_master on (GLM_EMP_ID=GPN_EMP_ID) ".
		          " left join gft_lead_hdr lh on(GPN_LEAD_CODE=glh_lead_code) ".
        		  " left join gft_product_video_master pv on(GPV_ID=GPN_NOTI_REFERENCE_ID and GPN_NOTIFICATION_TYPE=5 AND GPV_CONTENT_TYPE=1) ".
		          " where GPN_STATUS in (1) and GPN_FOR_APP=1 and GPN_CREATED_DATE_TIME>'$expity_dt' $id_filter ".
        		  " order by GPN_CREATED_DATE_TIME limit 1000 ";
    
    $res_notif	=	execute_my_query($sql_get);
    $temp_text = "";
    $arr = array();
    $mapping_arr = array();
    $i = 0;
    while($row=mysqli_fetch_array($res_notif)){
        $i++;
        $uniq_text = $row['uniq_text'];
        if($i==1){ //for first time alone
            $temp_text=$uniq_text;
        }
        if($temp_text!=$uniq_text){
            do_curl_to_ns($mydelight_tm_cust_id, $send_ns_url, json_encode($arr), $header_arr);
            $mapping_arr = array();
            $temp_text=$uniq_text;
        }
        if(count($mapping_arr)==50){ //send upto 50 users in one request for avoiding timeout issues in Notification Server
            do_curl_to_ns($mydelight_tm_cust_id, $send_ns_url, json_encode($arr), $header_arr);
            $mapping_arr = array();
        }
        $notification_id 	=	$row['GPN_ID'];
        $notification_type	=	$row['GPN_NOTIFICATION_TYPE'];
        $notification_title	=	$row['GPN_TITLE'];
        $notify_desc		=	stripslashes($row['GPN_MESSAGE']);
        $cust_id			=	(int)$row['GPN_LEAD_CODE'];
        $entity_id			=	$row['GPN_NOTI_REFERENCE_ID'];
        $created_datetime	=	$row['GPN_CREATED_DATE_TIME'];
        $gpv_video_link		=	$row['GPV_VIDEO_LINK'];
        $gpv_ref_dtl		=	$row['GPN_OTHER_REFERENCE_DTL'];
        $form_elements 		= 	array();
        $contact_arr 		= 	array();
        $extra_data_arr		=	array();
        if($gpv_ref_dtl!=''){
            $extra_data_arr = json_decode($gpv_ref_dtl,true);
        }
        if($cust_id!=0){
            $lead_dtl = get_lead_contact_detail($cust_id,4,0);
            $contact_arr = $lead_dtl["elements"];
        }
        $can_take_action = true;
        if(in_array($notification_type, array('4','5'))){
            $can_take_action = false;
        }
        if( ($notification_type=='2') && ((int)$entity_id!=0) ){
            $sql_support_dtl	=		" select  gcd_activity_id, 	 GCD_SCHEDULE_DATE,  GCD_PRIORITY,GCD_SEVERITY, GCD_CUSTOMER_EMOTION as cust_emotion,".
                " GCD_PROBLEM_SUMMARY,GCD_PROBLEM_DESC,GCD_CUST_CALL_TYPE,gcd_status,GCD_PROCESS_EMP,GCD_SCHEDULE_DATE, ".
                " GCD_ESTIMATED_TIME, GCD_PROMISE_DATE,GCH_COMPLAINT_CODE ,GDQ_BATON_WOBBLING ".
                " from gft_customer_support_hdr   ".
                " join gft_customer_support_dtl on (GCH_COMPLAINT_ID=GCD_COMPLAINT_ID and gch_last_activity_id=gcd_activity_id) ".
                " left join gft_data_quality on (GDQ_REMINDER_TYPE=2 and GDQ_REF_ID=gcd_activity_id)  ".
                " where GCH_COMPLAINT_ID=$entity_id";
            $res_support_dtl	=	execute_my_query($sql_support_dtl);
            if($sup_row=mysqli_fetch_array($res_support_dtl)){
                $form_elements = array("severity"=>$sup_row['GCD_SEVERITY'],"priority"=>$sup_row['GCD_PRIORITY'],"emotion"=>$sup_row['cust_emotion'],"call_type"=>$sup_row['GCD_CUST_CALL_TYPE'],
                    "prob_summary"=>$sup_row['GCD_PROBLEM_SUMMARY'],"prob_description"=>$sup_row['GCD_PROBLEM_DESC'],"complaint_status"=>$sup_row['gcd_status'],
                    "assigned_to"=>$sup_row['GCD_PROCESS_EMP'],"scheduled_on"=>$sup_row['GCD_SCHEDULE_DATE'],"estimated_time"=>$sup_row['GCD_ESTIMATED_TIME'],"restore_time"=>$sup_row['GCD_PROMISE_DATE'],
                    "complaint_code"=>$sup_row['GCH_COMPLAINT_CODE'],"received_baton_wobbling"=>$sup_row['GDQ_BATON_WOBBLING'],"ref_activity_id"=>$sup_row['gcd_activity_id']);
                if($sup_row['gcd_status']=='T6'){
                    $can_take_action = false;
                }
            }
        }
        
        $data_arr['notification_id']	= $notification_id;
        $data_arr['lead_status']		=	$row['GLH_STATUS'];
        $data_arr['prospect_status']	=	$row['GLH_PROSPECTS_STATUS'];
        $data_arr['customer_contacts']	=	json_encode($contact_arr);
        $data_arr['form_elements'] 		=	json_encode($form_elements);
        $data_arr['can_take_action']	=	$can_take_action;
        $data_arr['type'] 		= 	$notification_type;
        $data_arr['title']		=	$notification_title;
        $data_arr['description']=	$notify_desc;
        $data_arr['cust_id']	=	$cust_id;
        $data_arr['entity_id']	= 	$entity_id;
        $data_arr['datetime']	=	$created_datetime;
        $data_arr['video_link']	=	$gpv_video_link;
        $data_arr['extra_data'] =	json_encode($extra_data_arr,JSON_FORCE_OBJECT);
        
        $mapping_arr[] = array('cust_id'=>$mydelight_tm_cust_id,'username'=>$row['GLM_LOGIN_NAME']);
        $arr['title'] = $notification_title;
        $arr['body']  = $notify_desc;
        $arr['is_free'] = true;
        $arr['mapping'] = $mapping_arr;
        $arr['data'] 	= $data_arr;
        $arr['not_type'] = "mydelight";
        $arr['android'] = new ArrayObject();
        $arr['ios'] 	= new ArrayObject();
        execute_my_query("update gft_gcm_push_notification set GPN_STATUS=2,GPN_SENT_DATE_TIME=now() where GPN_ID='$notification_id' ");
    }
    if(count($arr) > 0){ //to send the last content formed using array
        $post_data = json_encode($arr);
        do_curl_to_ns($mydelight_tm_cust_id, $send_ns_url, $post_data, $header_arr);
    }
}

/**
 * @param int $lead_code
 * @param int $notification_id
 *
 * @return void
 */
function send_notification_to_mygofrugal_app($lead_code=0,$notification_id=0){
	global $mygofrugal_task_manger_customer_id;
	$app_auth_token = get_single_value_from_single_table("GPM_NS_REGISTRATION_KEY", "gft_product_family_master", "GPM_PRODUCT_CODE", "704");
	$id_filter	= ($lead_code!=0)?" and GPN_LEAD_CODE='$lead_code' " : "";
	$id_filter	.= ($notification_id!=0)?" and GPN_ID='$notification_id' " : "";
	$expity_dt	= date('Y-m-d H:i:s',strtotime("-12 hours",strtotime(date('Y-m-d H:i:s'))));
	$id_filter	.= " and GCL_EMP_ID=0 "; //to skip role switched employee
	$sql_get_notif	=	" select GPN_EMP_ID,GCL_DEVICE_PLATFORM,GCL_GCM_REGISTER_ID,GCL_USERNAME,GPN_NOTIFICATION_TYPE,GPN_NOTI_REFERENCE_ID,GPN_ID, ".
						" GPN_MESSAGE,GPN_TITLE,GPN_CREATED_DATE_TIME,GCD_REMARKS,GCD_PROBLEM_SUMMARY,count(if(GPN_STATUS!=4,1,null)) as unread_cnt,GPV_VIDEO_LINK, ".
						" concat(ticket.GLH_CUST_NAME,'-',ifnull(ticket.GLH_CUST_STREETADDR2,'')) as lead_name, ".
						" concat(GPN_NOTIFICATION_TYPE,ifnull(GPN_TITLE,''),ifnull(GPN_MESSAGE,'')) as uniq_text,GPN_OTHER_REFERENCE_DTL ".
						" from  gft_gcm_push_notification ".
						" join gft_customer_login_master on (GCL_USER_ID=GPN_EMP_ID) ".
						" left join gft_customer_support_hdr on (GCH_COMPLAINT_ID=GPN_NOTI_REFERENCE_ID and GPN_NOTIFICATION_TYPE=2) ".
						" left join gft_customer_support_dtl on (GCD_ACTIVITY_ID=GCH_LAST_ACTIVITY_ID) ".
						" left join gft_lead_hdr ticket on (ticket.GLH_LEAD_CODE=GCH_LEAD_CODE) ".
						" left join gft_product_video_master pv on(GPV_ID=GPN_NOTI_REFERENCE_ID and GPN_NOTIFICATION_TYPE=5 AND GPV_CONTENT_TYPE=1) ".
						" where GPN_STATUS in (1) and GPN_FOR_APP=2 and GPN_CREATED_DATE_TIME>'$expity_dt' $id_filter ".
						" group by GPN_NOTI_REFERENCE_ID,if(GPN_NOTIFICATION_TYPE in (2,7),GPN_EMP_ID,GPN_ID) ".
						" order by uniq_text limit 1000 ";
	$res_notif		=	execute_my_query($sql_get_notif);
	$temp_text = "";
	$arr = array();
	$mapping_arr = array();
	$i = 0;
	while($row=mysqli_fetch_array($res_notif)){
		$i++;
		$uniq_text = $row['uniq_text'];
		if($i==1){ //for first time alone
			$temp_text=$uniq_text;
		}
		if($temp_text!=$uniq_text){
			do_curl_to_ns($mygofrugal_task_manger_customer_id, $send_ns_url, json_encode($arr), $header_arr);
			$mapping_arr = array();
			$temp_text=$uniq_text;
		}
		if(count($mapping_arr)==50){
		    do_curl_to_ns($mygofrugal_task_manger_customer_id, $send_ns_url, json_encode($arr), $header_arr);
		    $mapping_arr = array();
		}
		$notification_id 	=	$row['GPN_ID'];
		$notification_type	=	$row['GPN_NOTIFICATION_TYPE'];
		$entity_id			=	$row['GPN_NOTI_REFERENCE_ID'];
		$wh_condition		=	" and GPN_ID='$notification_id' ";
		$notification_title	=	$row['GPN_TITLE'];
		$unread_cnt 		= 	(int)$row['unread_cnt'];
		$contact_desc 		= 	stripslashes($row['GPN_MESSAGE']);
		if($notification_type=='2'){
			$contact_desc = ($row['GCD_REMARKS']!='')?$row['GCD_REMARKS']:$row['GCD_PROBLEM_SUMMARY'];
		}
		
		$data_arr['notification_id']		= $row['GPN_ID'];
		$data_arr['entity_id'] 				= $row['GPN_NOTI_REFERENCE_ID'];
		$data_arr['entity_name']			= ($notification_type=='2')?$row['lead_name']:$notification_title;
		$data_arr['contextual_description']	= $contact_desc;
		$data_arr['type'] 					= $notification_type;
		$data_arr['datetime'] 				= $row['GPN_CREATED_DATE_TIME'];
		$data_arr['video_link']				= $row['GPV_VIDEO_LINK'];
		$data_arr['unread_count']			= $unread_cnt;
		//For notification related to phone and remote support
		if($notification_type=='10'){
			$other_reference_dtl = json_decode($row['GPN_OTHER_REFERENCE_DTL'],true);
			$agent_id 						=	isset($other_reference_dtl['agent_id'])?(int)$other_reference_dtl['agent_id']:0;
			$data_arr['img_url']			= "https://labtest.gofrugal.com/service/gftEmployeeProfileImage.php?empId=$agent_id";
			$data_arr['duration']			= isset($other_reference_dtl['duration'])?(int)$other_reference_dtl['duration']:0;
			$data_arr['agent_name']			= isset($other_reference_dtl['agent_name'])?(string)$other_reference_dtl['agent_name']:"";
			$data_arr['agent_designation']	= isset($other_reference_dtl['agent_designation'])?(string)$other_reference_dtl['agent_designation']:"";
			$data_arr['agent_rating']		= null;
			$data_arr['support_status']		= isset($other_reference_dtl['support_status'])?(string)$other_reference_dtl['support_status']:"pending";
			$data_arr['support_activity_id']= isset($other_reference_dtl['support_activity_id'])?(string)$other_reference_dtl['support_activity_id']:0;
		}
		$mapping_arr[] = array('cust_id'=>$mygofrugal_task_manger_customer_id,'username'=>$row['GCL_USERNAME']);
		$ns_config 	= get_notification_server_config();
		$send_ns_url = $ns_config['send_notification_url'];
		$header_arr = array("Content-Type: application/json","X-PROJECT-AUTH-TOKEN: $app_auth_token");
		$arr = array();
		$arr['title'] = $notification_title;
		$arr['body']  = $row['GPN_MESSAGE'];
		$arr['is_free'] = true;
		$arr['mapping'] = $mapping_arr;
		$arr['data'] 	= $data_arr;
		$arr['not_type'] = "mygofrugal";
		$arr['android'] = new ArrayObject();
		$arr['ios'] 	= new ArrayObject();
		execute_my_query("update gft_gcm_push_notification set GPN_STATUS=2,GPN_SENT_DATE_TIME=now() where 1 $wh_condition ");
	}
	if(count($arr) > 0){ //to send the last content formed using array
		$post_data = json_encode($arr);
		do_curl_to_ns($mygofrugal_task_manger_customer_id, $send_ns_url, $post_data, $header_arr);
	}
}
/**
 * @return void
 */
function send_windows_push_notification() {
	$expity_dt	= date('Y-m-d H:i:s',strtotime("-1 days",strtotime(date('Y-m-d H:i:s'))));
	$lead_code=$install_id=$message_code=$prod_code=$version=$release_url=$full_version='';
	$version_release_date=$version_key=$custom_title=$wh_cond='';
	$get_alert=false;
	$type='promotion';
	$wh_cond .= " and gpn_created_date_time>'$expity_dt' ";
	$wh_cond .= " and gpn_for_app='3' ";
	$wh_cond .= " and gpn_status='1' ";
	$query = " select gpn_id,gpn_lead_code,gpn_title,gpn_message from gft_gcm_push_notification where (1) $wh_cond ";
	$res = execute_my_query($query);
	while($row = mysqli_fetch_array($res)) {
		$lead_code = $row['gpn_lead_code'];
		$message_code = $row['gpn_message'];
		$custom_title = $row['gpn_title'];
		$id = $row['gpn_id'];
		notify_pos_product($lead_code,$install_id,$type,$message_code,$prod_code,$version,$get_alert,$release_url,$full_version,
				$version_release_date,$version_key,$custom_title,$id);
	}
}
/**
 * @param string $product_type
 * @param string $title
 * @param string $message
 * @param int $noti_cat
 * @param int $video_id
 * @param string $lead_code_query
 * @param string[int] $lead_arr
 * @param int $os_platform
 * 
 * @return void
 */
function send_push_notification_mygofrugal($product_type,$title, $message,$noti_cat,$video_id,$lead_code_query='',$lead_arr=null,$os_platform=0){
	$sql_user_list	=	"";
	$where_query	=	"";
	$list_query		=	"";
	$insert_query = '';$put_comma='';
	if($lead_code_query=='') {
	if($product_type=='1'){//To all mygofrugal app user
		$where_query="";		
	}else if($product_type=='500-07.0' || $product_type=='500-06.5' || $product_type=='200-06.0'){
		$product_group_id	=	substr($product_type,4);
		$product_code 		=	substr($product_type,0,3);
		$list_query=" select GLH_LEAD_CODE,GLH_CUST_NAME, group_concat(if(GCC_CONTACT_TYPE=4,GCC_CONTACT_NO,null)) as email ".
				" from gft_install_dtl_new ".
				" join gft_lead_hdr on (GID_LEAD_CODE=GLH_LEAD_CODE) ".
				" join gft_customer_contact_dtl on (GLH_LEAD_CODE=GCC_LEAD_CODE) ".
				" where GID_LIC_PCODE='$product_code' ".
				" and SUBSTR(GID_LIC_PSKEW,1,4)='$product_group_id' and GID_STATUS!='U' and GLH_LEAD_TYPE in (1) ".
				" group by GID_LEAD_CODE ";
	}else if($product_type=='300-03.0'){
		$product_group_id	=	substr($product_type,4);
		$client_sub_que=" select distinct GLH_REFERENCE_GIVEN as lead,GID_INSTALL_ID as ins_id from gft_lead_hdr ".
				" join gft_install_dtl_new on (GID_LEAD_CODE=GLH_LEAD_CODE) ".
				" where GLH_LEAD_SOURCECODE=7 AND GLH_LEAD_TYPE=13 and GID_LIC_PCODE='300' and SUBSTR(GID_LIC_PSKEW,1,4)='$product_group_id' and GID_STATUS!='U' ";
		$list_query=" select GLH_LEAD_CODE,GLH_CUST_NAME, group_concat( distinct if(GCC_CONTACT_TYPE=4,GCC_CONTACT_NO,null)) as email ".
				" from gft_lead_hdr lh".
				" join gft_customer_contact_dtl on (GLH_LEAD_CODE=GCC_LEAD_CODE) ".
				" left join gft_install_dtl_new on (GID_LEAD_CODE=lh.GLH_LEAD_CODE and GID_LIC_PCODE='300' and SUBSTR(GID_LIC_PSKEW,1,4)='$product_group_id' and GID_STATUS!='U' ) ".
				" left join ($client_sub_que) tt on (tt.lead=lh.GLH_LEAD_CODE) ".
				" where GLH_LEAD_TYPE=3 and (GID_INSTALL_ID is not null or ins_id is not null)  group by GLH_LEAD_CODE ";
	}else if( ($product_type=='cust') ){
		$cust_id_str = implode(",", $lead_arr);
		if($cust_id_str!=''){
			$list_query = " select GLH_LEAD_CODE,GLH_CUST_NAME from gft_lead_hdr where GLH_LEAD_CODE in ($cust_id_str) ";
		}
	}
	} else {
		$list_query = $lead_code_query;
	}
	if($list_query!=""){
		$list_res = execute_my_query($list_query);
		$lead_arr	=	array();
		while($row1 = mysqli_fetch_array($list_res)){
			$lead_code = isset($row1['GLH_LEAD_CODE'])?$row1['GLH_LEAD_CODE']:(isset($row1['Customer_Id'])?$row1['Customer_Id']:'');
			if($lead_code=='') {
				continue;
			}
			$lead_arr[] = $lead_code;
		}
		$lead_str = implode(',',$lead_arr);
		if($lead_str!=''){
			$where_query	=	" AND GCA_ACCESS_LEAD in ($lead_str) ";
		}
	}
	if( ($product_type=='cust') && ($where_query=="") ){ //no given customers matched the criteria
		return;
	}
	//$os_platform
	$sql_user_list	=	" select GCL_USER_ID from gft_customer_login_master ".
						" join gft_customer_access_dtl on (GCA_USER_ID=GCL_USER_ID and GCA_ACCESS_STATUS=1) ".
						" where (1) $where_query AND GCL_GCM_REGISTER_ID!='' and GCL_DEVICE_STATUS=1 and GCL_EMP_ID=0  ";
	if($os_platform!=0){
		$sql_user_list .= " and GCL_DEVICE_PLATFORM='$os_platform' ";
	}
	$sql_user_list .= " group by GCL_USER_ID";
	$title = mysqli_real_escape_string_wrapper($title);
	$message = mysqli_real_escape_string_wrapper($message);
	$res_user_list	=	execute_my_query($sql_user_list);
	while ($row_data = mysqli_fetch_array($res_user_list)){
		$user_id = $row_data['GCL_USER_ID'];
		$insert_query .= $put_comma."('2','$user_id','$noti_cat','$video_id','','$title','$message',1,now())";
		$put_comma = ',';
	}
	if($insert_query!=''){
		$sql_qry=	" INSERT INTO gft_gcm_push_notification(GPN_FOR_APP,GPN_EMP_ID,GPN_NOTIFICATION_TYPE, GPN_NOTI_REFERENCE_ID, GPN_LEAD_CODE,".
				" GPN_TITLE, GPN_MESSAGE,GPN_STATUS, GPN_CREATED_DATE_TIME) ".
				" VALUES $insert_query ";
		execute_my_query($sql_qry);
	}
}

/**
 * @param string $cust_id
 * @param string $post_url
 * @param string $post_data
 * @param string[int] $header_arr
 * @param boolean $show_status_alert
 * @param string $update_notiifcaation_table_id
 * 
 * @return void
 */
function do_curl_to_ns($cust_id,$post_url,$post_data,$header_arr,$show_status_alert=false,$update_notiifcaation_table_id=''){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $post_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
	$resp_body = (string)curl_exec($ch);
	$resp_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$resp_arr = array('response_json'=>$resp_body,'response_code'=>$resp_code);
	$curl_error = curl_error($ch);
	if($curl_error!=''){
		$resp_arr['curl_error'] = $curl_error;
		if($show_status_alert) {
			show_my_alert_msg("Error occurred while sending push notification.");
		}
	} else {
		if($show_status_alert) {
			show_my_alert_msg("Push notification sent successfully");
		}
	}
	curl_close($ch);
	$resp_json = json_encode($resp_arr);
	if($update_notiifcaation_table_id!='' and is_numeric($update_notiifcaation_table_id)) {
		execute_my_query(" update gft_gcm_push_notification set gpn_sent_date_time=now(),gpn_gcm_request_sent='".mysqli_real_escape_string_wrapper($post_data)."', ".
				" gpn_gcm_response='".mysqli_real_escape_string_wrapper($resp_json)."',gpn_status=2 where gpn_id='$update_notiifcaation_table_id' and gpn_for_app='3' ");
	}
	log_request($post_data, $resp_json, '', $cust_id, 44);
	if($resp_code!=200){
	    insert_into_sync_queue($post_url, $post_data, json_encode($header_arr), "POST",$resp_code,$resp_body);
	}
}

/**
 * @param string $mobile
 * @param string $cust_id
 * @param string $pcode
 * 
 * @return void
 */
function post_to_notification_server($mobile,$cust_id,$pcode){
	global $mydelight_tm_cust_id,$mygofrugal_task_manger_customer_id;
	$outlet_arr = /*. (string[int]) .*/array();
	if( (is_employee_contact($mobile, 'gftemail')) || (in_array($cust_id, array($mydelight_tm_cust_id,$mygofrugal_task_manger_customer_id))) ){
		$outlet_arr[] = $cust_id;
	}else{
		$mobile_cond = getContactDtlWhereCondition("GCC_CONTACT_NO", $mobile);
		$sel_query =" select GID_LEAD_CODE from gft_customer_contact_dtl ".
				" join gft_pos_users on (GPU_CONTACT_ID=GCC_ID) ".
				" join gft_app_users on (GAU_INSTALL_ID=GPU_INSTALL_ID and GAU_CONTACT_ID=GPU_CONTACT_ID) ".
				" join gft_install_dtl_new on (GID_INSTALL_ID=GPU_INSTALL_ID) ".
				" where GAU_APP_PCODE='$pcode' and ($mobile_cond) ".
				" group by GID_LEAD_CODE ";
		$res_query = execute_my_query($sel_query);
		while($row1 = mysqli_fetch_array($res_query)){
			$outlet_arr[] = $row1['GID_LEAD_CODE'];
		}
	}
	$post_arr['customer_outlet_ids'] = $outlet_arr;
	$post_arr['parent_id'] = $cust_id;
	$post_data = json_encode($post_arr);
	$ns_config = get_notification_server_config();
	$post_url = $ns_config['customers_url'];
	$ns_api_ui_key 	= $ns_config['api_ui_key'];
	$header_arr = array("Content-Type: application/json","X-UI-AUTH-TOKEN: $ns_api_ui_key");
	do_curl_to_ns($cust_id, $post_url, $post_data, $header_arr);
}

/**
 * @param string $lead_code
 * @param string $order_no
 * @param string $fullfill_no
 * @param string $validity_date
 * 
 * @return void
 */
function post_customer_and_plan_to_notification_server($lead_code,$order_no,$fullfill_no,$validity_date){
	$que1 = " select GID_LEAD_CODE from gft_install_dtl_new where GID_LEAD_CODE='$lead_code' and GID_LIC_PCODE='708' ".
			" and GID_STATUS!='U' and GID_VALIDITY_DATE > '$validity_date' ";
	$res1 = execute_my_query($que1);
	if(mysqli_num_rows($res1)==0){
		$order_fullfill_no = $order_no.substr("0000".$fullfill_no,-4);
		$outlet_arr[] = $lead_code;
		$post_arr['parent_id'] = $lead_code;
		$post_arr['customer_outlet_ids'] = $outlet_arr;
		$post_arr['notification_plan'] = array("order_number"=>$order_fullfill_no,"expiry_date"=>$validity_date);
		$post_data = json_encode($post_arr);
		$ns_config = get_notification_server_config();
		$post_url = $ns_config['customers_url'];
		$ns_api_ui_key 	= $ns_config['api_ui_key'];
		$header_arr = array("Content-Type: application/json","X-UI-AUTH-TOKEN: $ns_api_ui_key");
		do_curl_to_ns($lead_code, $post_url, $post_data, $header_arr);
	}
}

/**
 * @param string $cust_id
 * @param string $order_fullfill_no
 * 
 * @return void
 */
function post_notification_plan($cust_id,$order_fullfill_no){
	$ns_config 		= get_notification_server_config();
	$ns_api_ui_key 	= $ns_config['api_ui_key'];
	$ns_post_url 	= $ns_config['plan_url'];
	$alert_domain	= $ns_config['alert_domain'];
	$header_arr 	= array("Content-Type: application/json","X-UI-AUTH-TOKEN: $ns_api_ui_key");
	
	$que1 = " select GID_VALIDITY_DATE,GID_NO_CLIENTS,GPM_PRODUCT_TYPE,GPM_FREE_EDITION,GEM_LEAD_CODE ".
			" from gft_install_dtl_new join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
			" left join gft_emp_master on (GID_LEAD_CODE=GEM_LEAD_CODE) ".
			" where GID_LIC_PCODE=708 and GID_LEAD_CODE='$cust_id' and GID_STATUS!='U' order by GID_VALIDITY_DATE desc ";
	$res1 = execute_my_query($que1);
	if($row1 = mysqli_fetch_array($res1)){
		$expiry_date 	= $row1['GID_VALIDITY_DATE'];
		$ns_arr['order_number']	= $order_fullfill_no;
		$ns_arr['expiry_date'] 	= $expiry_date;
		$ns_post_url 		= str_replace("{{parentId}}", $cust_id, $ns_post_url);
		do_curl_to_ns($cust_id, $ns_post_url, json_encode($ns_arr), $header_arr);
	}
	mysqli_data_seek($res1, 0);
	$purchase_arr = /*. (string[int][string]) .*/array();
	while ($data1 = mysqli_fetch_array($res1)){
// 		$lic_type = "";
// 		if($data1['GPM_FREE_EDITION']=='Y'){
// 			$lic_type = "Know";
// 		}else if ($data1['GPM_PRODUCT_TYPE']=='1'){
// 			$lic_type = "Spot";
// 		}else if ($data1['GPM_PRODUCT_TYPE']=='3'){
// 			$lic_type = "Fix";
// 		}
// 		if((int)$data1['GEM_LEAD_CODE'] > 0){ //employee account
// 			$lic_type = "Fix";
// 		}
		$temp_arr['licenseType'] 	= "Fix";
		$temp_arr['planValidity'] 	= $data1['GID_VALIDITY_DATE'];
		$temp_arr['userCount']		= $data1['GID_NO_CLIENTS'];
		$purchase_arr[] = $temp_arr;
	}
	if(count($purchase_arr) > 0){
		$alert_arr['customerId'] 	= $cust_id;
		$alert_arr['orderNumber']	= $order_fullfill_no;
		$alert_arr['plansPurchased']= $purchase_arr;
		$alert_post_url = $alert_domain."/Alert/rest/sam/notification/license";
		do_curl_to_alert($cust_id, $alert_post_url, "POST", json_encode($alert_arr), $header_arr);
	}
}

/**
 * @param string $pcode
 * @param string $mobile
 * @param string $email
 * 
 * @return string[string]
 */
function process_notification_registration($pcode,$mobile,$email){
	$return_array = /*. (string[string]) .*/array();
	$lead_code_arr= /*. (string[int]) .*/array();
	$mobile_cond = getContactDtlWhereCondition("GCC_CONTACT_NO", $mobile);
	$response_arr = get_live_url_details($mobile, $email, $pcode);
	$user_mode = isset($response_arr['mode'])?$response_arr['mode']:'';
	$store_urls = isset($response_arr['live_store_url'])?$response_arr['live_store_url']:array();
	foreach ($store_urls as $arr){
		$temp_id = isset($arr['customer_id'])?(int)$arr['customer_id']:0;
		if(!in_array($temp_id, $lead_code_arr)){
			$lead_code_arr[] = $temp_id;
		}
	}
	$is_employee = is_employee_contact($mobile, $email);
	if($is_employee){
		$gem_lead_code = (int)get_single_value_from_single_table("GEM_LEAD_CODE", "gft_emp_master", "GEM_EMAIL", $email);
		if($gem_lead_code!=0){
			$lead_code_arr = array($gem_lead_code);
		}
	}
	$lead_str = implode(',', $lead_code_arr);
	if( ($lead_str!='') && (($user_mode=='live') || ($user_mode=='trial') || $is_employee ) ){
		$sql1 = " select GID_LEAD_CODE from gft_install_dtl_new where GID_LEAD_CODE in ($lead_str) ".
				" and GID_LIC_PCODE=708 and GID_STATUS in ('A','S') ";
		$res1 = execute_my_query($sql1);
		if(mysqli_num_rows($res1)==0){
			$cust_id = isset($lead_code_arr[0])?$lead_code_arr[0]:0;
			$alert_que =" select GID_LEAD_CODE from gft_install_dtl_new where GID_LEAD_CODE in ($lead_str) ".
					" and GID_LIC_PCODE='604' and GID_STATUS in ('A','S') order by GID_VALIDITY_DATE desc ";
			$alert_res = execute_my_query($alert_que);
			if($row1 = mysqli_fetch_array($alert_res)){
				$cust_id = $row1['GID_LEAD_CODE'];
			}
			$notification_pcode 		= '708';
			$notification_pskew 		= '01.0RCT';
			$notification_ref_skew 		= '01.0PL';
			$notification_order_full_no = check_and_place_order($notification_pcode,$notification_pskew,'y',$cust_id,$notification_ref_skew);
			$notification_order_no 		= substr($notification_order_full_no, 0,15);
			update_install_dtl_alert($cust_id,$notification_pcode,$notification_pskew,$notification_order_no,false);
			$reg_resp = alert_new_Register($cust_id,$notification_order_no,$notification_pcode,'',$email,$notification_ref_skew,$notification_pskew);
			$resp_arr = json_decode($reg_resp,true);
			$request_id = isset($resp_arr['request_id'])?(int)$resp_arr['request_id']:0;
			$alert_response = get_single_value_from_single_table("GRD_RESPONSE", "gft_smsgareway_request", "GSR_REQUEST_ID", $request_id);
			$json_arr = json_decode($alert_response,true);
			$root_email_id = isset($json_arr['email_id'])?$json_arr['email_id']:"";
			if($root_email_id!=''){
				save_and_post_email_id($root_email_id,$lead_code_arr,604);
			}
			post_to_notification_server($mobile,$cust_id,$pcode);
			post_notification_plan($cust_id,$notification_order_full_no);
		}else{
			$sql2 = " select GID_LEAD_CODE,GID_LIC_PCODE,GID_STORE_URL,GPE_EMAIL_ID from gft_install_dtl_new ".
					" join gft_product_email_mapping on (GPE_LEAD_CODE=GID_LEAD_CODE and GPE_PRODUCT_CODE=604) ".
					" where GID_LEAD_CODE in ($lead_str) and GID_STATUS in ('A','S') and GID_STORE_URL!='' ";
			$res2 = execute_my_query($sql2);
			while($row2 = mysqli_fetch_array($res2)){
				$prod_code = (int)$row2['GID_LIC_PCODE'];
				$gid_lead_code = $row2['GID_LEAD_CODE'];
				$user_email_id = $row2['GPE_EMAIL_ID'];
				if(in_array($prod_code, array('300','601','605'))){
					$gid_store_url = $row2['GID_STORE_URL'];
					$call_url 	= "$gid_store_url/servquick/rest/alert/register?autoProvision=true";
					$post_data 	= "userId=$user_email_id";
					$ch = curl_init($call_url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					$resp_arr['response'] = (string)curl_exec($ch);
					$resp_arr['status_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					$curl_error = curl_error($ch);
					if($curl_error!=''){
						$resp_arr['curl_error'] = $curl_error;
					}
					curl_close($ch);
					error_log(json_encode($resp_arr));
				}else{
					notify_pos_product($gid_lead_code, '', 'tacc', '');
				}
			}
		}
		$user_auth_token = uniqid("WN");
		update_auth_token_to_users($user_auth_token,$mobile,$email,$pcode);
	}else{
		$user_auth_token = 'AT57c982005fcca'; //demo token
	}
	$return_array['user_auth_token'] = $user_auth_token;
	return $return_array;
}

/**
 * @param string $email_id
 * @param string[int] $lead_code_arr
 * @param string $pcode
 * 
 * @return void
 */
function save_and_post_email_id($email_id,$lead_code_arr,$pcode){
	$ins_values = "";
	$put_comma = "";
	foreach ($lead_code_arr as $lead){
		$ins_values .= $put_comma."('$lead','$email_id','$pcode',now())";
		$put_comma = ",";
	}
	if($ins_values!=''){
		$ins_que	=	" replace into gft_product_email_mapping (GPE_LEAD_CODE,GPE_EMAIL_ID,GPE_PRODUCT_CODE ".
						" ,GPE_UPDATED_DATE) values $ins_values ";
		$res = execute_my_query($ins_que);
		if($res){
			foreach ($lead_code_arr as $lead){
				notify_pos_product($lead, '', 'tacc', '');
			}
		}
	}	
}

/**
 * @param string $msg_code
 * 
 * @return string
 */
function get_message_for_code($msg_code){
	switch ($msg_code){
		case 'N001' : $message =" Congrats! your ALR has been renewed. Enjoy uninterrupted GOFRUGAL services ";break;
		case 'N002' : $message =" Congrats on software upgrade. Experience our upgraded features, be ahead of competition & profit more ";break;
		case 'N004' : $message =" Congrats, your license has been renewed. Enjoy uninterrupted GOFRUGAL services ";break;
		case 'N006' : $message =" Dear Customer, Your License has been Extended. Happy Selling ";break;
		case 'N007' : $message =" Congrats, you have customized your software with additional feature. Please restart your product and experience it ";break;
		case 'N008' : $message =" Purchase of additional client has been activated. Thanks for choosing GOFRUGAL as your partner in growth ";break;
		case 'N009' : $message =" You now own licensed version of GOFRUGAL product. Enjoy uninterrupted GOFRUGAL services";break;
		case 'N010' : $message =" Congrats, you have customized your software with Addons. Please restart your product and experience it ";break;
		case 'N011' : $message = "Its our pleasure to make our valuable customers experience the benefits of instant notifications relevant to your business growth. - GoDigital !! GOFRUGAL !!";break;
		default		: $message = $msg_code;break;
	}
	return $message;
}

/**
 * @param string $lead_code
 * @param string $install_id
 * @param string $type
 * @param string $message_code
 * @param string $prod_code
 * @param string $version
 * @param boolean $get_alert
 * @param string $release_url
 * @param string $full_version
 * @param string $version_release_date
 * @param string $version_key
 * @param string $custom_title
 * @param string $update_notiifcaation_table_id
 * 
 * @return void
 */
function notify_pos_product($lead_code,$install_id,$type,$message_code,$prod_code='',$version='',$get_alert=false,$release_url='',$full_version='',
							$version_release_date='',$version_key='',$custom_title='',$update_notiifcaation_table_id=''){
	$other_data = /*. (string[string]) .*/array();
	$title = $body = $routing_key = "";
	if(in_array($type,array("license_sync","addon_sync","gosecure_activated","gosecure_unsubscribe"))){
		$title = "GOFRUGAL license update";
		$body = get_message_for_code($message_code);
		$routing_key = "/customers/$lead_code";
	}else if($type=='tacc'){
		$title = "Auto notifications on WhatsNow";
		$body  = "Your notification service is being set-up, sit back and get all the information you need to know about your business";
		$routing_key = "/customers/$lead_code";
	}else if ($type=='version_update'){
		$product_name="";
		if( ($prod_code=='500') && ($version=='7') ){
			$product_name = "RPOS7";
		}else if($prod_code=='300'){
			$product_name = "hq";
			$other_data['hyperLink'] = $release_url;
			$other_data['productId'] = "5";
			$other_data['productName'] = "GOFRUGAL HQ";
			$other_data['version'] = $full_version;
			$other_data['releaseDate'] = $version_release_date;
			$other_data['versionKey'] = $version_key;
			$other_data['productType'] = 'hq';
		}else{
			return;
		}
		$title = "Hurray, new version available";
		$body  = "Your product has new features and improvements, download now and experience the latest";
		$routing_key = "/products/$product_name";
		if($lead_code!=''){
			$routing_key = "/customers/$lead_code/patch";
		}
	}else if($type=='connectplus_token'){
		$title = "Your own cloud!";
		$body  = "Congrats, You now have your own cloud backup account, all your data will be synced automatically. Experience the future now";
		$routing_key =  "/customers/$lead_code";
	}else if($type=='promotion'){
		$title = "Let`s dive into world of instant notification.!!";
		if($custom_title!=''){
			$title = $custom_title;
		}
		$body = get_message_for_code($message_code);
		$routing_key = "/customers/$lead_code";
	}else if( ($type=='outlet_order_update') || ($type=='outlet_uuid_update') ){
		$title 		= "GOFRUGAL Notification Service";
		$body 		= ($type=='outlet_order_update') ? "A new outlet is ready for registration" : "";
		$routing_key= "/customers/$lead_code";
		$lic_no = substr($custom_title,0,5)."-".substr($custom_title,5,5)."-".substr($custom_title,10,5)."-".substr($custom_title,15,4);
		if($prod_code=='200'){
		    $lic_no .= "1"; //company for de
		}
		$other_data['orderNo'] = $lic_no;
	}else if($type=='alr_payment_sync'){
		$title 		= "GOFRUGAL Notification Service";
		$body 		= "Congrats! ALR for your Outlet is updated";
		$routing_key= "/customers/$lead_code";
	}
	$post_arr['type'] = $type;
	$post_arr['title'] = $title;
	$post_arr['body'] = $body;
	$post_arr['routing_key'] = $routing_key;
	$post_arr['content_type'] = "text";
	$post_arr['customer_id'] = $lead_code;
	$post_arr["other_data"]  = (count($other_data) > 0)?$other_data:new ArrayObject();
	$ns_config = get_notification_server_config();
	$wns_auth_token	= $ns_config['wns_auth_token'];
	$post_url 		= $ns_config['wns_url'];
	$header_arr = array("Content-Type: application/json","X-PROJECT-AUTH-TOKEN: $wns_auth_token");
	$post_data = json_encode($post_arr);
	do_curl_to_ns($lead_code, $post_url, $post_data, $header_arr,$get_alert,$update_notiifcaation_table_id);
}


/**
 * @param int $template_id
 * @param string[int][string] $mapping_arr
 * @param string $app_auth_token
 *
 * @return void
 */
function send_notification_via_notification_server($template_id,$mapping_arr,$app_auth_token){
	$ns_config 	= get_notification_server_config();
	$send_ns_url = $ns_config['send_notification_url'];
	$header_arr = array("Content-Type: application/json","X-PROJECT-AUTH-TOKEN: $app_auth_token");
	$query="select GPN_TITLE,GPN_DESC,GPN_CATEGORY,GPN_PUSH_CATEGORY from gft_push_notification_template WHERE GPN_ID='$template_id' ";
	$result=execute_my_query($query);
	if($qd=mysqli_fetch_array($result)){
		$arr['title'] = $qd['GPN_TITLE'];
		$arr['body']  = $qd['GPN_DESC'];
		$arr['is_free'] = true;
		$arr['mapping'] = $mapping_arr;
		$arr['not_type'] = "default";
		$arr['data'] 	= new ArrayObject();
		$arr['android'] = new ArrayObject();
		$arr['ios'] 	= new ArrayObject();
		$post_data = json_encode($arr);
		do_curl_to_ns('', $send_ns_url, $post_data, $header_arr);
	}
}

/**
 * @param string $order_no
 * @param boolean $send_notification
 * @param string $fullfillment_no
 * 
 * @return void
 */
function generate_pos_notification($order_no,$send_notification=true,$fullfillment_no=''){
	$god_lead_code = get_single_value_from_single_table("GOD_LEAD_CODE", "gft_order_hdr", "GOD_ORDER_NO", $order_no);
	$lead_type = get_lead_type_for_lead_code($god_lead_code);
	$lead_code_cond = " if(god_order_splict=1,GCO_CUST_CODE,GOD_LEAD_CODE) ";
	$kit_ins_id = /*. (string[int]) .*/array();
	if(is_kit_based_customer($god_lead_code)){
		$lead_code_cond = " GOD_LEAD_CODE ";
		$kit_ins_id = get_install_id_under_kit($god_lead_code);
	}
	$sql1 = " select $lead_code_cond as lead_code,GOD_LEAD_CODE, god_emp_id,god_order_splict,god_balance_amt, pm.GFT_SKEW_PROPERTY,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW, ".
			" pm.GPM_PRODUCT_TYPE,GPM_IS_INTERNAL_PRODUCT,GAD_ASS_START_DATE,GAD_ASS_END_DATE,GID_VALIDITY_DATE,GOU_ROOT_ORDER_NO,GOU_ROOT_FULLFILLMENT_NO, ".
			" if(god_order_splict=1,GCO_FULLFILLMENT_NO,GOP_FULLFILLMENT_NO) ff,GOU_OLD_PCODE,GOU_OLD_PSKEW,lm.GPM_FREE_EDITION is_free, ".
			" fm.GPM_PRODUCT_NAME prod_name,GOD_ORDER_DATE,GOP_QTY from gft_order_hdr ".
			" join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) ".
			" left join gft_cp_order_dtl on (GCO_ORDER_NO=GOP_ORDER_NO and GCO_PRODUCT_CODE=GOP_PRODUCT_CODE and GCO_SKEW=GOP_PRODUCT_SKEW) ".
			" join gft_product_master pm on (GOP_PRODUCT_CODE=pm.GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=pm.GPM_PRODUCT_SKEW) ".
			" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
			" left join gft_ass_dtl on (GAD_ASS_ORDER_NO=GOD_ORDER_NO and GAD_PRODUCT_CODE=GOP_PRODUCT_CODE and GAD_PRODUCT_CODE in (200,300,500)) ".
			" left join gft_order_upgradation_dtl on (GOU_ORDER_NO=GOD_ORDER_NO and GOU_NEW_PCODE in (200,300,500)) ".
			" left join gft_install_dtl_new on (GID_ORDER_NO=GOU_ROOT_ORDER_NO and GID_FULLFILLMENT_NO=GOU_ROOT_FULLFILLMENT_NO and GID_LIC_PCODE=GOU_NEW_PCODE) ".
			" left join gft_product_master lm on (lm.GPM_PRODUCT_CODE=pm.GFT_LOWER_PCODE and lm.GPM_PRODUCT_SKEW=pm.GFT_LOWER_SKEW) ".
			" where GOD_ORDER_NO='$order_no' and pm.GPM_ORDER_TYPE not in (2,3,4) ";
	if(intval($fullfillment_no)>0) {
	    $sql1 .= " and GCO_FULLFILLMENT_NO='$fullfillment_no' ";
	}
	$res1 = execute_my_query($sql1);
	$lead_code = '';
	$ordered_customer_id = '';
	$generate_invoice = false;
	$asa_order = false;
	while ($row1 = mysqli_fetch_array($res1)){
		$lead_code = $row1['lead_code'];
		$ordered_customer_id = $row1['GOD_LEAD_CODE'];
		if((int)$lead_code>0) {
		$skew_property 	= $row1['GFT_SKEW_PROPERTY'];
		$asa_start_date = $row1['GAD_ASS_START_DATE'];
		$asa_end_date 	= $row1['GAD_ASS_END_DATE'];
		$base_validity 	= $row1['GID_VALIDITY_DATE'];
		$product_code	= $row1['GOP_PRODUCT_CODE'];
		$split 		= $row1['god_order_splict'];
		$balance 	= $row1['god_balance_amt'];
		$pcode 		= $row1['GOP_PRODUCT_CODE'];
		$pskew 		= $row1['GOP_PRODUCT_SKEW'];
		$fulfil_no  = $row1['ff'];
		$old_pcode 	= $row1['GOU_OLD_PCODE'];
		$old_skew 	= $row1['GOU_OLD_PSKEW'];
		$emp 		= $row1['god_emp_id'];
		$prod_name  = $row1['prod_name'];
		$god_order_date = $row1['GOD_ORDER_DATE'];
		$qty = $row1['GOP_QTY'];
		if($send_notification){
		if(in_array($skew_property,array('4','15'))){
			$asa_order = true;
			notify_pos_product($lead_code,'','license_sync','N001');
/* 			if( ($asa_start_date!='') && (strtotime($asa_start_date)>=strtotime(date('Y-m-d'))) ){
				$up_query = " update gft_install_dtl_new set GID_VALIDITY_DATE='$asa_end_date' where GID_LEAD_CODE='$lead_code' ".
							" and GID_LIC_PCODE=703 and GID_STATUS in ('A','S') and GID_NO_CLIENTS=0 ";
				execute_my_query($up_query);
			} */
			if($pcode=='300'){
				foreach ($kit_ins_id as $gid_ins_id){
					execute_my_query("update gft_install_dtl_new set GID_VALIDITY_DATE='$asa_end_date' where GID_INSTALL_ID='$gid_ins_id'");
				}	
			}
		}else if(in_array($skew_property, array('3','16','13','14'))){
			notify_pos_product($lead_code,'','license_sync','N008');
		}else if($skew_property=='2'){
			notify_pos_product($lead_code,'','license_sync','N002');
			//update whatsnow asa
			if( ($base_validity!='') && ($row1['is_free']=='Y') ){
				$up_query = " update gft_install_dtl_new set GID_VALIDITY_DATE='$base_validity' where GID_LEAD_CODE='$lead_code' ".
						" and GID_LIC_PCODE=703 and GID_STATUS in ('A','S') and GID_NO_CLIENTS=1 ";
				execute_my_query($up_query);
			}
		}else if(in_array($skew_property, array('1','11','18'))){
			if($row1['GPM_PRODUCT_TYPE']=='8'){
				notify_pos_product($lead_code,'','license_sync','N007');
			}else if($row1['GPM_IS_INTERNAL_PRODUCT']=='2'){
				if($pcode=='706'){
					notify_pos_product($lead_code,'','gosecure_activated','N010');
				}else{
					notify_pos_product($lead_code,'','addon_sync','N010');
				}
			}else{
				notify_pos_product($lead_code,'','license_sync','');
			}
		}
		if(in_array($product_code,array('306'))){
			post_to_connectplus_authservice($lead_code,true);
		}
		}
		if((int)$balance==0 and (int)$emp<7000) {
			if($skew_property=='2'){
				$lic_status_qry = execute_my_query("select if(god_order_splict=1,gco_license_status,gop_license_status) stat from gft_order_hdr".
						" join gft_order_product_dtl on (god_order_no = gop_order_no and gop_product_code='$old_pcode' and gop_product_skew='$old_skew' and gop_fullfillment_no='".$row1['GOU_ROOT_FULLFILLMENT_NO']."') ".
						" left join gft_cp_order_dtl on (god_order_no = gco_order_no and gco_product_code='$old_pcode' and gco_skew='$old_skew' and gco_fullfillment_no='".$row1['GOU_ROOT_FULLFILLMENT_NO']."') ".
						" where god_order_no='".$row1['GOU_ROOT_ORDER_NO']."'");
				$stat_res = mysqli_fetch_array($lic_status_qry);
				$prev_stat = $stat_res['stat'];
				$generate_invoice = check_and_approve_license($row1['GOU_ROOT_ORDER_NO'],$split,$lead_code,$old_pcode,$old_skew,$row1['GOU_ROOT_FULLFILLMENT_NO'],$prev_stat);
			}else if(in_array($skew_property, array('1','11')) and isset($fulfil_no) and $fulfil_no!='') {
			    $generate_invoice = check_and_approve_license($order_no,$split,$lead_code,$pcode,$pskew,$fulfil_no);
			}
		}
	}
	}
	if( ($asa_order) && ($lead_type=='3') ){
		$hq_lead = get_hq_installed_cust_id($god_lead_code);
		notify_pos_product($hq_lead,'','alr_payment_sync','');
	}
	if($generate_invoice) {
		generate_accounts_invoice($order_no, 'customer', 'lic_approval');
	}
	$lead_type = get_lead_type_for_lead_code($ordered_customer_id);
	capture_first_order_date(($lead_type=='2'?$lead_code:$ordered_customer_id)); // For updating lead hdr with first order date for the customer.
	update_for_prepaid_license($order_no);
}

/**
 * @param string $lead_code
 * @param string $pcode
 * @param string $type
 * 
 * @return string
 */
function create_and_update_ns_token($lead_code, $pcode, $type){
    $ns_config = get_notification_server_config();
    $post_url = $ns_config['customer_url']."/generate-token?request_type=$type";
    $ui_token = $ns_config['api_ui_key'];
    $post_data = json_encode(array('cust_id'=>$lead_code,'project_code'=>$pcode));
    $header_arr = array("Content-Type: application/json","X-UI-AUTH-TOKEN: $ui_token");
    $ch = curl_init($post_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
    $resp = (string)curl_exec($ch);
    $resp_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    $json_data = json_decode($resp,true);
    $resp_json = json_encode(array('url'=>$post_url,'response'=>$resp,'response_code'=>$resp_code));
    log_request($post_data, $resp_json, '', $lead_code, 44);
    $ns_token = isset($json_data['token'])?$json_data['token']:'';
    execute_my_query("update gft_install_dtl_new set GID_NS_TOKEN='$ns_token' where GID_LEAD_CODE='$lead_code' and GID_LIC_PCODE='$pcode' and GID_STATUS!='U' ");
    return $ns_token;
}

/**
 * @param string $parent_cust_id
 * @param string[int] $outlet_arr
 * 
 * @return void
 */
function create_customer_account_in_ns($parent_cust_id,$outlet_arr){
    $post_data = json_encode(array('parent_id'=>$parent_cust_id,'customer_outlet_ids'=>$outlet_arr));
    $ns_config = get_notification_server_config();
    $post_url = $ns_config['customers_url'];
    $ns_api_ui_key 	= $ns_config['api_ui_key'];
    $header_arr = array("Content-Type: application/json","X-UI-AUTH-TOKEN: $ns_api_ui_key");
    do_curl_to_ns($parent_cust_id, $post_url, $post_data, $header_arr);
}

/**
 * @param string $contact_no
 * @param string $lead_code
 * @param string $action_page
 * @param boolean $full_form
 *
 * @return void
 */
function send_sms_and_push_notification_desing($contact_no,$lead_code='',$action_page='',$full_form=false){
	$form_tag = $submit_button = $title = '';
	if($full_form) {
		$form_tag =<<<tag
		<form method="post" action="$action_page" id="date_from1" name="notify_cust_form" enctype="multipart/form-data" >
tag;
		$submit_button =<<<button
		<tr id="submit_button">
			<td align='center' colspan='2'><br><INPUT type="button" class="button" id="submit1" name="submit1" value="Send" onclick="my_evaluate();">
				&nbsp;&nbsp;<INPUT  class="button" id="reset1" name="reset1" type="reset" value="Reset" onclick="my_reset();"></td>
		</tr>
button;
		$title =<<<TITLE
		<tr><td class='head_blue' nowrap>Notification title</td><td>
		<input tpe='text' id='custom_title' name='custom_title' size=50></td></tr>
TITLE;
	}
	$contact_arr = explode(",",$contact_no);
	$comma = '';
	$contacts = '';
	$contact_no_selection="";
	foreach ($contact_arr as $num) {
		if(is_numeric(str_replace("+", "", $num))) {
			$contacts .= "$comma$num";
			$comma = ',';
		}
	}
	if($contacts!='' || $lead_code!=""){
		if((substr($contact_no, 0,3))=="cid"){
			$contacts="";
		}
		$contact_dtls = get_active_contact_for_customer($contacts,$lead_code);
		$mygofrugal_user_id = $contact_dtls['mygofrugal_user_id'];
		$mygofrugal_str = $contact_dtls['mygofrugal'];
		$wns_str = $contact_dtls['wns'];
		$sms_str = $contact_dtls['sms'];
		$notify_cust_ids = /*.(string).*/$contact_dtls['wns_cust_ids'];
		$mygofrugal_noti_check = ($mygofrugal_str=="Not Using"?" disabled":"");
		$wns_noti_check = ($wns_str=="Not Using"?" disabled":"");
		$contact_arr = $contact_dtls['mobile_no_list'];
		if($contacts=="" && count($contact_arr)>1){
			for($inc=0;$inc<count($contact_arr);$inc++){
				if(trim($contact_arr[$inc])!="")
				$contact_no_selection .= "<option value='".$contact_arr[$inc]."'>".$contact_arr[$inc]."</option>";
			}	
			$contact_no_selection = "Select Mobile No: <select name='contact_list' id='contact_list' onchange='javascript:update_send_to_number(this.value);'>".
									"<option value='' selected='selected'>Select</option>".
									$contact_no_selection."</select>";
		}else if($contacts=="" && count($contact_arr)==1){
			$contacts =$contact_arr[0];
		}
		echo<<<END
	<script src="js/nicEdit-latest.js" type="text/javascript"></script>
	<script type="text/javascript">
		bkLib.onDomLoaded(function() {
    		nicEditors.editors.push(
	        	new nicEditor().panelInstance(
	            	document.getElementById('content')
	        	)
    		);
		});
	</script>
		$form_tag
		<table class=" highlight_blue" width='100%'>
		<input type='hidden' name='notify_details' id='notify_details' value='yes'>
		<input type='hidden' name='send_to_number' value='$contacts' id='send_to_number'>
		<input type='hidden' name='mygofrugal_user_id' value='$mygofrugal_user_id' id='mygofrugal_user_id'>
		<input type='hidden' name='notify_cust_ids' value='$notify_cust_ids' id='notify_cust_ids'>
		<tr><td colspan="2"align='left'><h3>Notify Customer</h3></td></tr>
		$title
		<tr id="pd_two" style=""><td class="head_blue" nowrap>Additional<br>Info</td><td>
		<textarea id='content' name='content' rows='10' cols='75'></textarea>
		</td></tr>
		<tr id="pd_two" style=""><td class="head_blue">Notify via </td><td>
				<input type='checkbox' id='mygofrugal' name='mygofrugal' value='1' $mygofrugal_noti_check>myGoFrugal App ($mygofrugal_str)<br>
				<input type='checkbox' id='wns' name='wns' value='1' $wns_noti_check>Windows Notification ($wns_str)<br>
				<input type='checkbox' id='sms' name='sms' value='1'>SMS ($sms_str)$contact_no_selection
		</td></tr>
		$submit_button
		</table>
END;
		$my_eval = '';
		if($full_form) {
			echo "</form>";
			$my_eval =<<<EVAL
			var jq = jQuery.noConflict();
			function my_evaluate() {
				if(jq('#custom_title').val()=='') {
					alert("Notification title is required.");
					return false;
				}
				var nicInstance = nicEditors.findEditor('content');
    			var messageContent = nicInstance.getContent();
				if(messageContent=='<br>' || messageContent=='<br/>' || messageContent=='') {
					alert('Notification content is mandatory');
					return false;
				} else {
					jq('#content').val(messageContent);
				}
				if(jq('input[name=mygofrugal]:checked').length==0 && jq('input[name=wns]:checked').length==0 && jq('input[name=sms]:checked').length==0) {
					alert("Choose atleast one option in 'Notify via'.");
					return false;
				}
				document.notify_cust_form.submit();
				return true;
			}
			function my_reset() {
				var nicInstance = nicEditors.findEditor('content');
    			var messageContent = nicInstance.setContent("<br>");
			}
EVAL;
		}
		echo<<<END
	<script>
	function update_send_to_number(value){
		document.getElementById('send_to_number').value = value;
	}
	$my_eval
	</script>
	<style>	.nicEdit-main {	background-color: white;} </style>
END;
	}else{
		echo "<h3>Not a valid Contact Number</h3>";
	}
}

?>
