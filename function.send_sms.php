<?php
/*. forward int function entry_sending_sms(string $mobileno,string $sms_content,int $category,string $emp_id=,int $status=,string $sender=,int $send_to_alert=,string $reply_to=); .*/
/*. forward void function send_sms_regarding_lead_status_change(string $change_to_lead_status,string $lead_code,string $user_id); .*/
/*. forward void function sending_sms_to(string $sms_content,string $category,string $emp_id,boolean $individual=,boolean $rep_mgr=,string[int] $group_arr=,string $sender_id=); .*/
/*. forward int function entry_sending_sms_to_customer(string $mobileno,string $sms_content,int $category,int $customer_id=,int $status=,string $sender=,int $send_to_alert=,string $tele_cust_code=,boolean $newly_added=,int $act_id=,string $country_code=); .*/
/*. forward void function support_sms(string $ACT_ID, string $emp_code=, boolean $send_support_ticket_noti=,string $mobile_no=,string $customer_email_id=); .*/
/*. forward boolean function check_for_complaint_exists(string $lead_code, int $GCH_COMPLAINT_CODE, int $support_id, string $status); .*/
/*. forward boolean function check_for_voicemail_open(string $gtc_number); .*/
/*. forward boolean function check_can_send_sms(string $contact_no); .*/
/*. forward string[string] function get_ticket_dtl(string $support_id); .*/

require_once(__DIR__ ."/inc.reminder_util.php");

define('CONST_MAX_NO_CHARACTER_FOR_A_SMS',320);

/*
 * @param mixed $group_id
 *
 * @return string
 */
/**
 * @param string[] $group_id
 * 
 * @return string
 */
function get_email_id_of_group($group_id){
	if(is_array($group_id) && $group_id!=null){
		$group_id_s= implode(',',$group_id);
	}else{
		$group_id_s=$group_id;
	}
	if($group_id_s!=''){
		$query="select group_concat(GGM_EMAIL_ID) email_id from gft_group_master where ggm_group_id in ($group_id_s)";
		$result=execute_my_query($query);
		$qd=mysqli_fetch_array($result);
		$email_id=$qd['email_id'];
		return $email_id;
	}
	    return null;
}


/**
 * @param string $cust_id
 * @param string $sms_category
 *
 * @return string
 */

function get_mobile_number_customer($cust_id,$sms_category=null){
	$preferered_group=/*. (string) .*/ null;$join_contact_group='';$type_of_sms='P';
	if($sms_category!=null){
		$query_contact_group="select GSP_PREFERED_CONTACT_GROUP,GSP_TYPE from gft_sms_config, " .
				"gft_sms_primary_types where gsp_id=gsc_primary_type and GSC_ID=$sms_category ";
		$result_contact_group=execute_my_query($query_contact_group);
		if(mysqli_num_rows($result_contact_group)>0){
			$qd=mysqli_fetch_array($result_contact_group);
			$preferered_group=$qd['GSP_PREFERED_CONTACT_GROUP'];
			$type_of_sms=$qd['GSP_TYPE'];
			
		}
	}
	
	if(!empty($preferered_group)){
		 $join_contact_group="join gft_contact_dtl_group_map gm on (GCG_LEAD_CODE=gcc_lead_code and GCG_CONTACT_ID=gcc_id and GCG_GROUP_ID in ($preferered_group) )";
	}	
	/* iff can send is yes and incase of promotional sms NDNC=off to be checked */ 
	$query="select group_concat(gcc_contact_no) from  gft_customer_contact_dtl $join_contact_group " .
		   " where gcc_contact_type=1 and length(gcc_contact_no)>=10 and gcc_lead_code='$cust_id'  and GCC_CAN_SEND='Y' " .
		   ($type_of_sms=='P'?" and GCC_NDNC_ON='N' ":'').
		   " group by gcc_lead_code ";
	$result=execute_my_query($query,'common_util.php',true,false,2);
	if(mysqli_num_rows($result)>0){
		$qdata=mysqli_fetch_array($result);
		$mobile_no=$qdata[0];
		return $mobile_no;	
	}
	return null; 
	
}

/**
 * @param string $cust_id
 * @param string $sms_category
 *
 * @return string
 */
function get_mobile_number_customer_with_name($cust_id,$sms_category=null){
	$preferered_group=/*. (string) .*/ null;$join_contact_group='';$type_of_sms='P';
	if($sms_category!=null){
		$query_contact_group="select GSP_PREFERED_CONTACT_GROUP,GSP_TYPE from gft_sms_config, " .
				"gft_sms_primary_types where gsp_id=gsc_primary_type and GSC_ID=$sms_category ";
		$result_contact_group=execute_my_query($query_contact_group);
		if(mysqli_num_rows($result_contact_group)>0){
			$qd=mysqli_fetch_array($result_contact_group);
			$preferered_group=$qd['GSP_PREFERED_CONTACT_GROUP'];
			$type_of_sms=$qd['GSP_TYPE'];
			
		}
	}
	
	if(!empty($preferered_group)){
		 $join_contact_group="join gft_contact_dtl_group_map gm on (GCG_LEAD_CODE=gcc_lead_code and GCG_CONTACT_ID=gcc_id and " .
		 		"GCG_GROUP_ID in ($preferered_group) )";
	}	
	/* iff can send is yes and incase of promotional sms NDNC=off to be checked */ 
	$query="select group_concat(concat(gcc_contact_name,'-',gcc_contact_no)) from  gft_customer_contact_dtl $join_contact_group " .
		   " where gcc_contact_type=1 and length(gcc_contact_no)>=10 and gcc_lead_code='$cust_id'  and GCC_CAN_SEND='Y' " .
		   ($type_of_sms=='P'?" and GCC_NDNC_ON='N' ":'').
		   " group by gcc_lead_code ";
	$result=execute_my_query($query,'common_util.php',true,false,2);
	if(mysqli_num_rows($result)>0){
		$qdata=mysqli_fetch_array($result);
		$mobile_no=$qdata[0];
		return $mobile_no;	
	}else{
		/* If contact group not updated ..take phone,Mobile no */
		$query2="select gcc_contact_name,group_concat(gcc_contact_no) as ccno from  gft_customer_contact_dtl " .
		  " where gcc_contact_type in (1,2) and length(gcc_contact_no)>=10 and gcc_lead_code='$cust_id'  and GCC_CAN_SEND='Y' " .
		  ($type_of_sms=='P'?" and GCC_NDNC_ON='N' ":'').
		  " group by gcc_lead_code,gcc_contact_name ";
		 $result2=execute_my_query($query2);
		 if(mysqli_num_rows($result2)>0){
		 	$mobile_no='';$mobile_no_concat_str='';
		 	while($qdc=mysqli_fetch_array($result2)){
		 		$mobile_no.=$mobile_no_concat_str.($qdc['gcc_contact_name']!=''?$qdc['gcc_contact_name'].'-':'').$qdc['ccno'];
		 		$mobile_no_concat_str=',';
		 	}
		 	return $mobile_no;	
		 } 
	}
	return null; 
	
}

/**
 * @param int $sms_id
 * @param int $category
 * @param int $send_intl
 * @param string $sender_id
 * 
 * @return void
 */
function send_immediate_sms($sms_id,$category,$send_intl=0,$sender_id=""){
	$immediate_category = /*. (string[int]) .*/array();
	$immediate_category = explode(',',get_samee_const('Immediate_SMS_Category'));
	if(in_array($category, $immediate_category)){
		system("sh ".__DIR__."/shscript/send_sms_now.sh $sms_id $category $send_intl $sender_id >/dev/null 3>&1 &");
	}
}

/**
 * @param string $mobileno
 * @param string $sms_content
 * @param int $category
 * @param int $customer_id
 * @param int $status
 * @param string $sender
 * @param int $send_to_alert
 * @param string $tele_cust_code
 * @param boolean $newly_added
 * @param int $act_id
 * @param string $country_code
 *
 * @return int
 */

function entry_sending_sms_to_customer($mobileno,$sms_content,$category,$customer_id=0,$status=0,$sender=null,$send_to_alert=0,$tele_cust_code=null,$newly_added=false,$act_id=0,$country_code=""){
	/* mobileno come us null in most of the case .according to $category it will send to person */
	/* if status as zero only send to customer */

	//if(get_samee_const("SMS_SEND_THROUGH")==''){
	//	return null;
	//}
		global $conn;
	$msg_sent_time		=	date('Y-m-d H:i:s');
	$sms_content_esc	=	get_valid_text($sms_content);
	$sms_content_esc	=	trim($sms_content_esc);
	$msg_updated_time	=	'';
	if($customer_id!=0){
		$leadcode=$customer_id;
	}else{
		$leadcode=0; //TODO: Need to check what to be done here ?
	}
	$sms_id = 0;
	if($country_code!="" && strtolower($country_code)!="in" && $mobileno!=""){		
		$status	=	0;
		$sender_result = execute_my_query("select GSS_SENDER_ID from gft_sms_sender_id_mapping_master where GSS_COUNTRY='$country_code' OR GSS_COUNTRY='' ORDER BY GSS_ID DESC limit 1");
		if(mysqli_num_rows($sender_result)>0 && $row_sender=mysqli_fetch_array($sender_result)){
			$sender_id	=	$row_sender['GSS_SENDER_ID'];
			$sql= " insert into gft_sending_sms (gos_msg_sent_time,gos_category,gos_receiver_mobileno, ".
					" gos_sms_content ,gos_sms_status,gos_status_updated_time,gos_customer_leadcode,gos_sent_to_alert,gos_sender_id," .
					" gos_tele_customer_id,GOS_ACTIVITY_ID) " .
					" values ('$msg_sent_time','$category','$mobileno','".mysqli_real_escape_string_wrapper($sms_content_esc)."','$status'," .
					"'$msg_updated_time','$leadcode','$send_to_alert','$sender','$tele_cust_code','$act_id')";
			$res = execute_my_query($sql,'common_util.php',true,false,2);
			if($res){
				$sms_id = mysqli_insert_id_wrapper();
			}
			send_immediate_sms($sms_id,$category,1,$sender_id);
			return $sms_id;
		}
		
	}	
 	if($customer_id!=0 and $newly_added==false){
 		$mobileno_arr=explode(',',get_mobile_number_customer($customer_id,$category));
 	}else if($newly_added==true){
 		$mobileno_arr[0]=$mobileno;
 		/* this added incase if you want to copy and send sms to customer*/
 		/* mobile activation */
 		/* from send sms screen */
 	}else{
		$mobileno_arr = /*. (string[int]) .*/ array();
	}
 	foreach($mobileno_arr as $key=>$value){
	    $mobileno=adjustMobileNumber($value);
	    if($mobileno==''){
		    continue;
	    }
	    $mobile_no_startwith=substr($mobileno,0,1);
	    if($mobile_no_startwith!='9' and $mobile_no_startwith!='8' and $mobile_no_startwith!='7' and $mobile_no_startwith!='6' ){
		    if($act_id!=0 and $act_id!=''){
		    	update_sms_status_inactivity($act_id, 'Not Applicable');
		    }
	    	continue;
	    }
	    if(strlen($mobileno)==10){
			$status=0;
		}else{
			$status=2;
			$send_to_alert=1;
		}		
		if(strlen($sms_content_esc)>CONST_MAX_NO_CHARACTER_FOR_A_SMS){
			$status=5;
		}

		$sql= " insert into gft_sending_sms (gos_msg_sent_time,gos_category,gos_receiver_mobileno, ".
			" gos_sms_content ,gos_sms_status,gos_status_updated_time,gos_customer_leadcode,gos_sent_to_alert,gos_sender_id," .
			" gos_tele_customer_id,GOS_ACTIVITY_ID) " .
			" values ('$msg_sent_time','$category','$mobileno','".mysqli_real_escape_string_wrapper($sms_content_esc)."','$status'," .
			"'$msg_updated_time','$leadcode','$send_to_alert','$sender','$tele_cust_code','$act_id')";
		$res = execute_my_query($sql,'common_util.php',true,false,2);
		if($res){
			$sms_id = mysqli_insert_id_wrapper();
		}
		if($act_id!=0 and $act_id!=''){
			$act_sms_status	=	'Pending';
			if($status==2){$act_sms_status	=	'Failed';}else if($status==5){$act_sms_status	=	'Exceed Content Limit';}
			update_sms_status_inactivity($act_id, $act_sms_status);
		}		
 	}
 	if($sms_id!=0){
 		send_immediate_sms($sms_id,$category,0,"None");
 	} 
 	return $sms_id;
}
/**
 * @param string $lead_code
 * @param string $ACT_ID
 * @param string $customer_success_status
 * 
 * @return void
 */
function update_customer_success_status($lead_code,$ACT_ID,$customer_success_status){
    $exiting_status = (int)get_single_value_from_single_table("GLE_CUSTOMER_SUCCESS_STATUS", "gft_lead_hdr_ext", "GLE_LEAD_CODE", "$lead_code");
    if($exiting_status!=$customer_success_status){
        $cst_status = array();
        $cst_status['GCS_LEAD_CODE'] = "$lead_code";
        $cst_status['GCS_ACTIVITY_ID'] = "$ACT_ID";
        $cst_status['GCS_SUCCESS_STATUS'] = $customer_success_status;
        array_insert_query("gft_customer_success_status_dtl", $cst_status);
        execute_my_query("UPDATE gft_lead_hdr_ext SET GLE_CUSTOMER_SUCCESS_STATUS='$customer_success_status' WHERE GLE_LEAD_CODE='$lead_code'");
    }    
}
/**
 * @param string $mobileno
 * @param string $sms_content
 * @param int $category
 * @param string $emp_id
 * @param int $status
 * @param string $sender
 * @param int $send_to_alert 
 * @param string $reply_to
 * @param string $customer_id
 * 
 * @return int
 */
function entry_sending_sms($mobileno,$sms_content,$category,$emp_id=null,$status=1,$sender=null,$send_to_alert=0,$reply_to=null,$customer_id=''){
 	//if(get_samee_const("SMS_SEND_THROUGH")==''){
	//	return null;
	//}
		global $conn;
	$msg_sent_time=date('Y-m-d H:i:s');
	$mobileno=adjustMobileNumber($mobileno);
 	$msg_updated_time='';
	if(strlen($mobileno)==10){
		$status=0;
	}else{
		$status=2;
		$send_to_alert=1;
	}
	if($category==0){
		$category=17;
	}
	
	$sms_content_esc=get_valid_text($sms_content);
	$max_char_sms_restrict=CONST_MAX_NO_CHARACTER_FOR_A_SMS;
    if($max_char_sms_restrict!='' and strlen($sms_content_esc)>$max_char_sms_restrict) $status=5;
	$sql=" insert into gft_sending_sms(gos_msg_sent_time,gos_emp_id,gos_category,gos_receiver_mobileno,gos_customer_leadcode, ".
		   " gos_sms_content ,gos_sms_status,gos_status_updated_time,gos_sender_id,gos_sent_to_alert,GOS_REPLY_TO_ID) " .
		   " values('$msg_sent_time','$emp_id','$category','$mobileno','$customer_id','".mysqli_real_escape_string_wrapper($sms_content_esc)."','$status','$msg_updated_time'," .
		   "'$sender','$send_to_alert','$reply_to')";
	$res = execute_my_query($sql);
	if($res){
		$sms_id = mysqli_insert_id_wrapper();
		if($reply_to!=null){
			$query_update="update gft_receiving_sms set GRS_REPLY_COUNT=GRS_REPLY_COUNT+1 where GRS_ID='$reply_to' ";
			execute_my_query($query_update,'common_util.php',true,false,2);	
		}
		send_immediate_sms($sms_id, $category,0,"None");
		return $sms_id;
	}
	return 0;
}



/**
 * @param string $sms_content
 * @param string $category
 * @param string $emp_id
 * @param boolean $individual
 * @param boolean $rep_mgr
 * @param string[int] $group_arr
 * @param string $sender_id 
 * 
 * @return void
 */
function sending_sms_to($sms_content,$category,$emp_id,$individual=true,$rep_mgr=true,$group_arr=null,$sender_id=null){
	$mobile_no_arr=/*. (string[int][int]) .*/ array();
	$emp_id_arr=/*. (string[int]) .*/ array();
	if($individual==true){
		if(is_array($emp_id)){
			foreach($emp_id as $key => $value){
				$mobile_no1=get_mobileno((string)$value);
				array_push($mobile_no_arr,array($value,$mobile_no1));
				array_push($emp_id_arr,$value);
			}
		}else{
			$mobile_no1=get_mobileno($emp_id);
			array_push($mobile_no_arr,array($emp_id,$mobile_no1));
			array_push($emp_id_arr,$emp_id);
		}
	}
	if($rep_mgr==true){
		if(is_array($emp_id)){
			foreach($emp_id as $key => $value){
				$mobile_nos=get_mobileno_reportingmaster((string)$value,/*$reporting_masters=*/true);
				//returns two dimensional array id and mobile no
				for($i=0;$i<count($mobile_nos);$i++){
					if(in_array($category,array(115,116,117)) && $mobile_nos[$i][1] == 670 || $mobile_nos[$i][1] == 1){ // removed charles mobile no for particular category
					}else{
						$mb_no=$mobile_nos[$i][0];
						$rep_id=$mobile_nos[$i][1];
						array_push($mobile_no_arr,array($rep_id,$mb_no));
						array_push($emp_id_arr,$rep_id);
					}					
				}
			}
		}else{
			$mobile_nos=get_mobileno_reportingmaster($emp_id,/*$reporting_masters=*/true);
			//returns two dimensional array id and mobile no
			for($i=0;$i<count($mobile_nos);$i++){
				$mb_no=$mobile_nos[$i][0];
				$rep_id=$mobile_nos[$i][1];
				array_push($mobile_no_arr,array($rep_id,$mb_no));
				array_push($emp_id_arr,$rep_id);
			}	
		}
	}
	if($group_arr!=''){
		$mobile_nos=get_contact_dtls_of_group($group_arr);
		for($i=0;$i<count($mobile_nos);$i++){
			$mb_no=$mobile_nos[$i][0];
			$rep_id=$mobile_nos[$i][1];
			array_push($mobile_no_arr,array($rep_id,$mb_no));
			array_push($emp_id_arr,$rep_id);
		}
	}
	$emp_id_arr=array_unique($emp_id_arr);
	foreach ( $emp_id_arr as $key => $value ) {
		$key_int = (int)$key;
		$mobileno=$mobile_no_arr[$key_int][1];
		$for_id=$mobile_no_arr[$key_int][0];
		entry_sending_sms($mobileno,$sms_content,$category,$for_id,/*$status=*/0,/*$sender=*/$sender_id,/*$send_to_alert=*/0,'');
	}
}

/**
 * 
 * @param string $contactValue
 * @param int $sms_template_id
 * @param string $cust_name
 * 
 * @return string
 */

function send_otp_to_mobile_without_custid($contactValue,$sms_template_id=143,$cust_name='') {
	$today_time = date('Y-m-d H:i:s');
	$otp_val = generate_OTP(5);
	if((int)get_samee_const("ENVIRONMENT")==1){
		$otp_val = '12345';
	}
	execute_my_query("update gft_app_otp_dtl set GAO_OTP_STATUS=0 where GAO_REF_USERNAME='$contactValue' and GAO_OTP_STATUS=1");
	$insert_arr['GAO_REF_USERNAME'] = $contactValue;
	$insert_arr['GAO_OTP'] 			= $otp_val;
	$insert_arr['GAO_OTP_STATUS'] 	= "1";
	$insert_arr['GAO_GENERATED_DATE'] = $today_time;
	$res = array_insert_query("gft_app_otp_dtl", $insert_arr,"boolean");
	if($res){
	    $content_config = array('OTP'=>array($otp_val),'Customer_Name'=>array($cust_name));
	    if(is_valid_email($contactValue)){
	        send_formatted_mail_content($content_config, 85, 372,null,null,array($contactValue));
	    }else{
	        $sms_content = get_formatted_content($content_config,$sms_template_id);
	        entry_sending_sms($contactValue, $sms_content, $sms_template_id);
	    }
	}
	return $otp_val;
}

/**
 * @param string $order_no_curr
 * @param string $lead_code
 * @param string $cheque_dt
 * @param string $cheque_no
 * @param string $bank_name
 * @param string $receipt_dt
 * @param string $collected_amount
 * @param string $sender_id
 * @param string $collect_emp_id
 *
 * @return void
 */
function send_sms_for_cheque_bounced($order_no_curr,$lead_code,$cheque_dt,$cheque_no,$bank_name,
			$receipt_dt,$collected_amount,$sender_id,$collect_emp_id){
	global $uid;

	$category=4;//CHEQUE DETAILS 
	$temp_emp_id=get_emp_id_frm_orderno($order_no_curr);
	$cinfo=customerContactDetail($lead_code);
	$cust_mobile_no=explode(',',$cinfo['mobile_no']);
	$mobile_no=get_mobileno($temp_emp_id);//$cheque_no[$i] $bank_name[$i] 
	$shop_name=$cinfo['cust_name']; 
	$area_name=$cinfo['area_name']; 
	$city_name=$cinfo['city'];
	$temp_cheque_no=$cheque_no;
	$temp_bank_name=$bank_name;
	$temp_collected_amount=$collected_amount;
	$cheque_date=local_date_format($cheque_dt);
	$receipt_date=local_date_format($receipt_dt);
	$db_sms_content_config=array(
		'cust_mobile_no'=> array($cust_mobile_no),
		'mobile_no' => array($mobile_no),
		'order_no_curr' => array($order_no_curr),
		'shop_name' => array($shop_name),
		'area_name' => array($area_name ),'city_name' => array($city_name),
		'temp_cheque_no' => array($temp_cheque_no),'temp_bank_name' => array($temp_bank_name),
		'temp_collected_amount' => array($temp_collected_amount),
		'cheque_date'  => array($cheque_date),
		'receipt_date' => array($receipt_date) );
	$temp_content=get_formatted_content($db_sms_content_config,$category);
	$temp_content=$sms_content=htmlentities($temp_content);
	entry_sending_sms_to_customer(null,$sms_content,$category,$lead_code,0,/*$sender=*/null,/*$send_to_alert=*/0,/*$tele_cust_code=*/null);
	if(!array_key_exists($mobile_no,$cust_mobile_no)){
		$result_xml="<?xml version= \"1.0\" encoding=\"UTF-8\" ?><response>" .
				"<smsdetail><empid>$temp_emp_id</empid><mobileno>$mobile_no</mobileno><message>$temp_content</message>" .
				"<senderid>$uid</senderid></smsdetail>" .
				"</response>";
		sms_xml_content_parser($result_xml,$category );
	}
	$employee_name	=	get_emp_name($collect_emp_id);
	if($employee_name!=""){
		$noti_content_config					=  array();
		$noti_content_config['Customer_Name']	=  array($shop_name);
		$noti_content_config['Employee_Name']	=  array($employee_name);
		$noti_content_config['Credit_Amount']	=  array($collected_amount);
		$noti_content_config['Customer_Id']		=  array($lead_code);
		$noti_content_config['dateon']			=  array(date("d-m-Y H:i:s"));
		send_formatted_notification_content($noti_content_config,13,47,1,$collect_emp_id);
	}
} //end of function 


/**
 * @param string $order_no_curr
 * @param string $lead_code
 * @param string $cheque_dt
 * @param string $cheque_no
 * @param string $bank_name
 * @param string $receipt_dt
 * @param string $receiptid_ref
 * @param string $collected_amount
 * @param string $sender_id
 *
 * @return void
 */ 
function send_sms_for_receipted_in_ofc($order_no_curr,$lead_code,$cheque_dt,$cheque_no,$bank_name,
$receipt_dt,$receiptid_ref,$collected_amount,$sender_id){
	$category=6;
	$temp_emp_id=get_emp_id_frm_orderno($order_no_curr);
	$cinfo=customerContactDetail($lead_code);
	if($cinfo['mobile_no']!=''){
		$cust_mobile_no=explode(',',$cinfo['mobile_no']);  
		$mobile_no=get_mobileno($temp_emp_id);  	
		$shop_name=$cinfo['cust_name']; 
		$area_name=$cinfo['area_name']; 
		$city_name=$cinfo['city'];
		$temp_cheque_no=$cheque_no;
		$temp_bank_name=$bank_name;
		$temp_collected_amount=$collected_amount;
		$receipt_ref_id=$receiptid_ref;
		$cheque_date=local_date_format($cheque_dt);
		$receipt_date=local_date_format($receipt_dt);
		
		$db_sms_content_config=array(
			'cust_mobile_no'=> $cust_mobile_no,
			'mobile_no'     => array($mobile_no),
			'order_no_curr' => array($order_no_curr),
			'shop_name'     => array($shop_name),
			'area_name'     => array($area_name ),
			'city_name' => array($city_name),
			'temp_cheque_no' => array($temp_cheque_no),
			'temp_bank_name' => array($temp_bank_name),
			'temp_collected_amount' => array($temp_collected_amount),
			'receipt_ref_id' => array($receipt_ref_id),
			'cheque_date'  => array($cheque_date),
			'receipt_date' => array($receipt_date));
	
		$temp_content=get_formatted_content($db_sms_content_config,$category);
		$temp_content=$sms_content=htmlentities($temp_content);
		entry_sending_sms_to_customer(null,$sms_content,$category,$lead_code,$status=1,$sender_id,$send_to_alert=0,$tele_cust_code=null);
	    //entry_sending_sms($mobileno=$mobile_no,$sms_content=$temp_content,$category,/*$emp_id=*/$temp_emp_id,$status=0,$sender=$sender_id,$send_to_alert=0);
	}
}//end of fuction

/**
 * @param string $assigned_to
 * @param string $demo_date
 * @param string $lead_name
 * @param string $location
 * @param string $city
 * @param string $buss_phno
 * @param string $follow_up_detail
 * @param string $uid
 *
 * @return void
 */
 
function send_sms_demo_fixed_to_exec($assigned_to,$demo_date,$lead_name,$location,$city,$buss_phno, $follow_up_detail,$uid){
	$category=2;
	$MOBILENO=get_mobileno($assigned_to);
	$db_sms_content_config=array(
		'demo_date' => array($demo_date),
		'lead_name'=> array($lead_name),
		'location'=> array($location),
		'city'=> array($city),
		'buss_phno'=> array($buss_phno),
		'follow_up_detail'=> array($follow_up_detail));
	$MESSAGE=get_formatted_content($db_sms_content_config,$category);
	$MESSAGE=htmlentities($MESSAGE);
	$sms_xmls="<?xml version= \"1.0\" encoding=\"UTF-8\" ?><response>" .
			"<smsdetail><empid>$assigned_to</empid><mobileno>$MOBILENO</mobileno><message>$MESSAGE</message><senderid>$uid</senderid></smsdetail>" .
			"</response>";
	sms_xml_content_parser($sms_xmls,$category);
}//end of function

/**
 * @param string $pfamily
 * @param string $version
 * @param string $rel_dt
 * @param string $feature
 * @param string $download_url
 * @param string $download_url_ph
 * @param string $sender_id
 * @param boolean $isNotInternalProduct
 * 
 * @return void
 */
function send_sms_for_release($pfamily,$version,$rel_dt,$feature,$download_url,$download_url_ph, $sender_id,$isNotInternalProduct){
	if($isNotInternalProduct){
		$tmp_productname=get_productcode_info($pfamily);
		$productname=$tmp_productname[0];
		$emps_info=get_emp_ableto_place_orders(null,'A');
		$category=5;//SAM_RELEASE
		$sms_content="";
		$db_sms_content_config=array(
			'productname'=> array($productname),
			'version'    => array($version),
			'rel_dt'     => array($rel_dt) , 'feature' => array($feature) , 
			'download_url' => array($download_url),
			'download_url_ph' => array($download_url_ph));
		$msg=get_formatted_content($db_sms_content_config,$category);
		$msg=htmlentities($msg);
		for($k=0;$k<count($emps_info);$k++){
			$eid=$emps_info[$k][0];
			$mobile_no=$emps_info[$k][3];
			$sms_content.="<smsdetail><empid>$eid</empid><mobileno>$mobile_no</mobileno><message>$msg</message><senderid>$sender_id</senderid></smsdetail>";
		}
		if($sms_content!=""){
			$result_xml="<?xml version= \"1.0\" encoding=\"UTF-8\" ?><response>$sms_content</response>";
			sms_xml_content_parser($result_xml,$category );
		}
	}
}//end of function


/**
 * @param string $lead_code
 * @param string $productname
 * @param string $tmp_installed_on
 * @param string $productcode
 * @param string $productskew
 * 
 * @return void
 */
function send_sms_after_installation($lead_code,$productname,$tmp_installed_on,$productcode,$productskew){	
/* deprecated to be removed in next license release */
	$cinfo=customerContactDetail($lead_code);
	$category=7;
	$shopname=$cinfo['cust_name'];
	$cust_mobile_no=explode(',',$cinfo['mobile_no']);
	$db_sms_content_config=array(
		'shopname' => array($shopname),
		'shop_phoneno' => array($cinfo['mobile_no']),
		'productname' => array($productname),
		'tmp_installed_on' => array($tmp_installed_on));
	$message=get_formatted_content($db_sms_content_config,$category);
	$message=htmlentities($message); //1st sms	
	entry_sending_sms_to_customer(null,$message,$category,$lead_code,0,SALES_DUMMY_ID,$send_to_alert=0,$tele_cust_code=null);
	
	$category1=15; //2nd SMS
	$message1=get_formatted_content($db_sms_content_config,$category1);
	$message1=htmlentities($message1);
	
	entry_sending_sms_to_customer(null,$message1,$category1,$lead_code,0,SALES_DUMMY_ID,$send_to_alert=0,$tele_cust_code=null);
	
	if($productcode==100 or ($productcode==500 and strpos($productskew,'06')==0)){
		$category1=39; //2nd SMS
		$message1=get_formatted_content($db_sms_content_config,$category1);
		$message1=htmlentities($message1);	
		entry_sending_sms_to_customer(null,$message1,$category,$lead_code,0,SALES_DUMMY_ID,$send_to_alert=0,$tele_cust_code=null);
	
	}
}

/* NOT USED
 * @param mixed[string] $license_dtl
 *
 * @return void
 *
function send_sms_after_product_installation($license_dtl){ 
	$cinfo=customerContactDetail($license_dtl['lead_code']);
	$shopname=$cinfo['cust_name'];
	$cust_mobile_no=explode(',',$cinfo['mobile_no']);
	$tmp_productname=get_productcode_info($license_dtl['productcode']);
	$productname=$tmp_productname[0];
	$db_sms_content_config=array(
		'shopname' => array($shopname),
		'shop_phoneno' => array($cinfo['mobile_no']),
		'productname' => array($productname),
		'tmp_installed_on' => array($license_dtl['install_date']));
	$message=get_formatted_content($db_sms_content_config,7);
	$message=htmlentities($message); //1st sms	
	entry_sending_sms_to_customer(null,$message,7,$license_dtl['lead_code'],0,SALES_DUMMY_ID,$send_to_alert=0,$tele_cust_code=null);
	
	
	$message1=get_formatted_content($db_sms_content_config,15);
	$message1=htmlentities($message1);
	entry_sending_sms_to_customer(null,$message1,15,$license_dtl['lead_code'],0,SALES_DUMMY_ID,$send_to_alert=0,$tele_cust_code=null);
	
	if($license_dtl['productcode']==100 or ($license_dtl['productcode']==500 and strpos($license_dtl['product_skew'],'06')==0)){
		$message1=get_formatted_content($db_sms_content_config,39);
		$message1=htmlentities($message1);	
		entry_sending_sms_to_customer(null,$message1,39,$license_dtl['lead_code'],0,SALES_DUMMY_ID,$send_to_alert=0,$tele_cust_code=null);
	
	}
}
*/

/**
 * @param string $lead_code
 * @param string $sender_id
 * @param string $product_code
 * @param string $skew
 * @param string $level
 *
 * @return void
 */
function send_sms_to_cust_abt_CBT($lead_code,$sender_id,$product_code,$skew,$level){
	$cinfo=customerContactDetail($lead_code);$category=39;
	$shopname=$cinfo['cust_name'];
	if(is_array($level)) {
		$level = implode(',',$level);
	}
	
	$query_slevel="select glm_send_cbt_info from gft_level_master where glm_code in ('$level') and " .
			"glm_send_cbt_info='Y' ";
	$result_slevel=execute_my_query($query_slevel);
	if(mysqli_num_rows($result_slevel)==0){		return;	}
		
	$version_dtl=get_version($product_code,$skew,$select=false,$latest='y');
	if(!isset($version_dtl[0][9])){		return;	}

	if($version_dtl[0][9]==''){		return;	}
	$db_sms_content_config=array(
	    'customer_id'=>array($lead_code)
	);
	$message=get_formatted_content($db_sms_content_config,$category);
	$sms_content=htmlentities($message);
	entry_sending_sms_to_customer(null,$sms_content,$category,$lead_code,$status=1,$sender=null,$send_to_alert=0,$tele_cust_code=null);	
	
}


/**
 * @param string $uid
 * @param string $from_date
 * @param string $to_date
 * @param string $leave_reason
 * 
 * @return void
 */
function send_leave_reporting_sms($uid,$from_date,$to_date,$leave_reason){
	$report_mobile=get_mobileno_reportingmaster($uid);
	$temp=get_emp_master($uid);
	$employeename=$temp[0][1];
	if($from_date!=$to_date){ 
		$leave_reason_dt="(".local_date_format($from_date)."  To:".local_date_format($to_date).")";
	}else{
		$leave_reason_dt="(".local_date_format($from_date).")" ; 
	}
	$db_sms_content_config=array(
		'FromDate' => array(local_date_format($from_date)),
		'ToDate' => array(local_date_format($to_date)),
		'Reason' => array($leave_reason_dt.$leave_reason),
		'Employeename' => array($employeename));
	
	$category=25;
	$message=htmlentities(get_formatted_content($db_sms_content_config,$category));
	$sender_emp_id=$uid;
	for($m=0;$m<count($report_mobile);$m++){
			$receiver_mobile=$report_mobile[$m][0];
			$receiver_empid=$report_mobile[$m][1];
			entry_sending_sms($mobileno=$receiver_mobile,$sms_content=$message,$category,$emp_id=$receiver_empid,
			$status=0,$sender=$sender_emp_id,$send_to_alert=0);
	}
}

/**
 * @param string $GLH_LEAD_CODE
 * @param string $ndnc_id
 *
 * @return void
 */
function send_sms_to_new_lead($GLH_LEAD_CODE,$ndnc_id=null){
$sql=<<<END
SELECT GLH_CUST_NAME,em.GEM_EMP_NAME 'LEAD_INCHARGE',cem.GEM_EMP_NAME 'CREATED_BY',
em.GEM_MOBILE,GLH_CUST_STREETADDR2,GLH_AREA_NAME,GLH_CUST_CITY,
group_concat(GCC_CONTACT_NO) as GCC_CONTACT_NO,GLH_LEAD_CODE,GLH_LEAD_TYPE ,
glh_reference_given,glh_lead_sourcecode, GLH_CREATED_BY_EMPID,GLH_LFD_EMP_ID 
FROM gft_lead_hdr join gft_emp_master em on (GLH_LFD_EMP_ID=em.GEM_EMP_ID and em.GEM_STATUS='A') 
join gft_emp_master cem on (GLH_CREATED_BY_EMPID=cem.GEM_EMP_ID and cem.GEM_STATUS='A')
inner join gft_customer_contact_dtl cn on(GCC_LEAD_CODE=GLH_LEAD_CODE and gcc_contact_type=1)
WHERE GCC_CAN_SEND='Y'  and GLH_LEAD_CODE='$GLH_LEAD_CODE' and GLH_LEAD_TYPE!=8 and (GLH_COUNTRY='India' or GLH_COUNTRY='INDIA')  group by glh_lead_code 
END;

	$rs=execute_my_query($sql,'',true,false); 
	while($row=mysqli_fetch_array($rs)){
		
	    $GLH_CUST_NAME=$row['GLH_CUST_NAME']==''?' ':$row['GLH_CUST_NAME']; 
	    $Exec_Mobile=$row['GEM_MOBILE']==''?' ':$row['GEM_MOBILE'];	
		$lead_type=$row['GLH_LEAD_TYPE']==''?'1':$row['GLH_LEAD_TYPE'];
		$GLH_CUST_BUSSPHNO=$row['GCC_CONTACT_NO']==''?' ':$row['GCC_CONTACT_NO'];
		$lead_code=$row['GLH_LEAD_CODE'];
		$Executive=$row['LEAD_INCHARGE'];
		$GLH_LFD_EMP_ID=$row['GLH_LFD_EMP_ID'];
		$GLH_CREATED_BY_EMPID=$row['GLH_CREATED_BY_EMPID'];
		$LEAD_CREATED_BY=$row['CREATED_BY'];
		$location=$row['GLH_CUST_STREETADDR2'];
		$area=$row['GLH_AREA_NAME'];
		$city=$row['GLH_CUST_CITY'];
		$category=9;
		if($GLH_CREATED_BY_EMPID==SALES_DUMMY_ID){/*web register*/
			$category=($lead_type=='1'?35:34);
		}
			
		$db_sms_content_config=array(
			'GLH_CUST_NAME' => array($GLH_CUST_NAME),
			'GLH_CUST_BUSSPHNO' => array($GLH_CUST_BUSSPHNO),
			'Executive' => array($Executive),
			'Exec_Mobile' => array($Exec_Mobile),
			'cust_id' =>array($lead_code),
			'GLH_CUST_CITY'=>array($city),
			'GLH_AREA_NAME'=>array($area),
			'GLH_CUST_STREETADDR2'=>array($location),
			'LEAD_CREATED_BY'=>array($LEAD_CREATED_BY));	
		$message=htmlentities(get_formatted_content($db_sms_content_config,$category));
		if($message!=''){
			entry_sending_sms_to_customer($GLH_CUST_BUSSPHNO,$message,$category,$lead_code,1,$GLH_CREATED_BY_EMPID,0,null);
			// if($GLH_LFD_EMP_ID!=$GLH_CREATED_BY_EMPID and !is_authorized_group_list($GLH_LFD_EMP_ID,array(1,27))){/*web_register/cp entered to executives*/
			// 	$message=htmlentities(get_formatted_content($db_sms_content_config,69));
			// 	entry_sending_sms($Exec_Mobile,$message,69,$GLH_LFD_EMP_ID,1,$GLH_CREATED_BY_EMPID,0,null);
			// }
		}
		
	}/*end of while*/
}

/**
 * @param string $lead_code
 * @param string $contact_no
 * @param string $uid
 * @param string $assign_emp
 * 
 * @return void
 */
function send_update_contact_no_sms($lead_code,$contact_no,$uid,$assign_emp=''){
/* check gcc_can_send also*/
	if($assign_emp!=''){
		execute_my_query(" update gft_lead_hdr set glh_lfd_emp_id='$assign_emp' where glh_lead_code='$lead_code' ");
	}
// 	$db_sms_content_config=array();
// 	$query="select glh_status,glh_lfd_emp_id,gem_emp_name,gem_mobile from " .
// 			"gft_lead_hdr,gft_emp_master where glh_lfd_emp_id=gem_emp_id and glh_lead_code='$lead_code' and GEM_STATUS='A' ";
// 	$result=execute_my_query($query);
// 	if($qdata=mysqli_fetch_array($result)){
// 		$lead_status=(int)$qdata['glh_status'];
// 		$exec_id=$qdata['glh_lfd_emp_id'];
// 		$exec_name=$qdata['gem_emp_name'];
// 		$exec_mobile=$qdata['gem_mobile'];
// 		$db_sms_content_config=array(
// 				'emp_name' => array($exec_name),
// 				'emp_mobileno' => array($exec_mobile) ,
// 				'customer_id'=> array($lead_code));
// 		if($lead_status==8 or $lead_status==9){ //customer and order won
// 			$category=71;
// 		}elseif($lead_status==26){ //lead
// 			$category=72;
// 		}else{
// 			$category=71; //Could be the contact number update...
// 		}
// 		$message=htmlentities(get_formatted_content($db_sms_content_config,$category));
// 		entry_sending_sms_to_customer($contact_no,$sms_content=$message,$category,$lead_code,0,$uid,$send_to_alert=0,$tele_cust_code=null,true);
// 	}	
}

/**
 * @param string $emp_id
 * @param string $lead_code
 * @param int $sms_thank
 * @param string $private_sms_msg
 *
 * @return void
 */
function send_sms_after_activity($emp_id,$lead_code,$sms_thank,$private_sms_msg){
	$exec_dtl=get_emp_master($emp_id);
	if (count($exec_dtl) > 0){
		$Exec_Mobile=$exec_dtl[0][3];
		$employeename=$exec_dtl[0][1];
	}else{
		//The emp_id value may be the partner. In this case we are not sending the SMS.
		$Exec_Mobile='';
		$employeename='';
	}
	if($sms_thank==1){
		$db_sms_content_config=array(
			'Executive' => array($employeename),
			'Exec_Mobile' => array($Exec_Mobile) );
		$message=htmlentities(get_formatted_content($db_sms_content_config,$category=16));
		entry_sending_sms_to_customer(null,$sms_content=$message,$category=16,$lead_code,0,$emp_id,$send_to_alert=0,$tele_cust_code=null);
	}
	if($private_sms_msg!=''){
		$sms_content_temp= array('content'=>array($private_sms_msg));
		$sms_content = htmlentities(get_formatted_content($sms_content_temp, 130));
		entry_sending_sms_to_customer(null,$sms_content,130,$lead_code,1,$emp_id,0,null);		
	}
}

/**
 * @param string[] $db_sms_content_config
 * @param int $category
 * @param string $emp_id
 * @param string $gem_mobile
 *
 * @return void
 */
function send_to_exe_support_scheduled($db_sms_content_config,$category,$emp_id,$gem_mobile){
	$category_status=get_sms_config_info($category);
	if($category_status=='A' and $gem_mobile!=''){
		$sender_id=$db_sms_content_config['sender_id'][0];
		$sms_content=get_formatted_content($db_sms_content_config,$category);
		$sms_content=htmlentities($sms_content);
		entry_sending_sms($gem_mobile,$sms_content,$category,$emp_id,0,$sender_id,0,null);
	}
}

/**
 * @param string[] $db_sms_content_config
 * @param int $category
 * @param string $training_status
 *
 * @return boolean
 */
function send_to_customer_regarding_training($db_sms_content_config,$category,$training_status){
	$category_status=get_sms_config_info($category);
	if($category_status=='A'){
		$db_sms_content_config['training_status']=array($training_status);
		$sms_content=get_formatted_content($db_sms_content_config,$category);
		$sms_content=htmlentities($sms_content);
		$cust_id=$db_sms_content_config['cust_id'][0];
		$sender_id1=$db_sms_content_config['sender_id'][0];
		entry_sending_sms_to_customer(null,$sms_content,$category,$customer_id=$cust_id,$status=0,$sender=$sender_id1,$send_to_alert=0);
	}
	return true;
}

/**
 * @param string $ACT_ID
 * @param string $emp_code
 * @param boolean $send_support_ticket_noti
 * @param string $mobile_no
 * @param string $customer_email_id
 * @param string $reply_cc_mail_ids
 * @param string $solution_given_content
 * @param string $upload_file
 * 
 * @return void
 */
function support_sms($ACT_ID,$emp_code=null,$send_support_ticket_noti=true,$mobile_no=null,$customer_email_id='',$reply_cc_mail_ids='',  $solution_given_content="",$upload_file=""){
    global $secret;
   // $mgroup_id="1,2,6,8,3,5,15";
	$mgroup_id = get_samee_const("Sms_Sending_Status_Group");
	$sms_query="SELECT GCD_COMPLAINT_ID,GCD_PROCESS_EMP,GAM_ACTIVITY_DESC,GCD_PROBLEM_SUMMARY, " .
    	" lh.GLH_CUST_NAME,lh.GLH_CUST_STREETADDR2,GPM_PRODUCT_ABR,em.gem_mobile,GTM_NAME, " .
    	" lh.GLH_LEAD_CODE,GCD_EMPLOYEE_ID,GCD_EXTRA_CHARGES,GCD_STATUS,GCD_REMARKS,GTM_GROUP_ID,GFT_COMPLAINT_ABR, " .
    	" dtl.gcd_activity_id, em.gem_emp_name as process_emp_name, em1.GEM_SHORT_NAME as activity_emp_name, " .
		" gi.GIM_MS_NAME male_stone, GTS_STATUS_NAME training_status, " .
    	" if(GCD_SCHEDULE_DATE >= date(now()), Date_format(GCD_SCHEDULE_DATE,'%d-%m-%Y'),'') 'scheduled_on', " .
    	" GCD_NATURE,GCD_VISIT_REASON,GCH_REPORTED_TIME,GCD_REPORTED_DATE,GCH_COMPLAINT_DATE,GCH_COMPLAINT_CODE,GCH_VERSION, date_format(GCH_RESTORE_TIME,'%d-%b %h:%i %p') GCH_RESTORE_TIME " .
		" FROM gft_customer_support_hdr hdr " .
		" join gft_customer_support_dtl dtl on(dtl.GCD_COMPLAINT_ID =hdr.GCH_COMPLAINT_ID) " .
		" join gft_status_master sm on (gtm_code=GCD_STATUS) ".
		" join gft_lead_hdr lh on(lh.GLH_LEAD_CODE=hdr.GCH_LEAD_CODE)" .
		" join gft_product_family_master on (GPM_PRODUCT_CODE=GCH_PRODUCT_CODE) " .
		" join gft_emp_master em1 on (dtl.gcd_employee_id=em1.gem_emp_id)" .
		" left join gft_complaint_master comp_m on (GFT_COMPLAINT_CODE=GCD_COMPLAINT_CODE) ".
		" left join gft_cust_imp_ms_current_status_dtl g on (GIMC_COMPLAINT_ID=hdr.GCH_COMPLAINT_ID) " . 
		" left join gft_impl_mailstone_master gi on (g.GIMC_MS_ID=gi.GIM_MS_ID ) ".
		" left join gft_ms_task_status on (GTS_STATUS_CODE=GIMC_STATUS) ".
		" left join gft_activity_master am on (GCD_TO_DO=GAM_ACTIVITY_ID )".
		" left join gft_emp_master em on(em.gem_emp_id=GCD_PROCESS_EMP)" .
		" where gtm_group_id in ($mgroup_id) AND dtl.gcd_activity_id in ($ACT_ID) " ;
		
		$sms_result=execute_my_query($sms_query,'Support_util.php');
		if($row=mysqli_fetch_array($sms_result)){
		$complaintid=$row['GCD_COMPLAINT_ID'];   
		$cust_id=$row['GLH_LEAD_CODE'];
		
		$temp_array=customerContactDetail($cust_id);
		$contact_no=$cust_mobile_no=$temp_array['mobile_no'];
		if($contact_no!='' and $temp_array['bussno']!=''){$contact_no.=',';}
		$contact_no.=$temp_array['bussno'];
		$contact_no_ar=explode(',',$contact_no);
		if(count($contact_no_ar)>2){
			$contact_no=$contact_no_ar[0].','.$contact_no_ar[1];	
		}
		$scheduledto=$emp_id=$row['GCD_PROCESS_EMP'];
		$cust_details=$row['GLH_CUST_NAME'].'-'.$row['GLH_CUST_STREETADDR2'];
		$productname=$row['GPM_PRODUCT_ABR'];
		$problem=$row['GCD_PROBLEM_SUMMARY'];
		$req_activity=$row['GAM_ACTIVITY_DESC'];
		$gem_mobile=$row['gem_mobile'];
		$sender_id=$row['GCD_EMPLOYEE_ID'];
		$extrach=$row['GCD_EXTRA_CHARGES'];
		$gcd_status=$row['GCD_STATUS'];
		$training_status=$row['training_status'];
		$training_session_activity=$row['gcd_activity_id'];
		$process_emp_name=$row['process_emp_name'];
		$activity_emp_name=$row['activity_emp_name'];
		$scheduled_on=$row['scheduled_on'];
		$GCD_NATURE=$row['GCD_NATURE'];
		$GCD_VISIT_REASON=$row['GCD_VISIT_REASON'];
		$complaintdate=$row['GCH_COMPLAINT_DATE'];	
		$version=$row['GCH_VERSION'];
		$shopname= $row['GLH_CUST_NAME'];
		$remarks=$row['GCD_REMARKS'];
		$complaint_abr=($row['male_stone']!=''?$row['male_stone']:$row['GFT_COMPLAINT_ABR']);
		$restoration_time=$row['GCH_RESTORE_TIME'];
		$GCH_COMPLAINT_CODE=$row['GCH_COMPLAINT_CODE'];
		$gtm_status_name = $row['GTM_NAME'];
		$db_sms_content_config=array('complaintid' => array($complaintid), 
			'comp_id'=>array($complaintid),
			'complaint_id'=>array($complaintid),
	        'scheduledto' => array($scheduledto),
	        'scheduledto_name' => array($process_emp_name),
	        'cust_details' => array($cust_details), 
	        'productname' => array($productname),
	        'gem_mobile' => array($gem_mobile),
	        'contact_no' => array($contact_no),
	        'shopname'=> array($shopname),
	        'problem' => array($problem), 
		    'problem_summary'=> array($problem),
	        'req_activity' => array($req_activity),
	        'extra_charges'=>array($extrach),
	        'cust_id'=>array($cust_id),
	        'sender_id'=>array($sender_id),
	        'scheduled_by'=>array($activity_emp_name),
	        'scheduled_on'=>array($scheduled_on),
	        'complaintdate'=> array($complaintdate),
	        'version'=> array($version),
	        'remarks' => array($remarks),
	        'employee_name'=>array($activity_emp_name),
		    'Employee_Name' => array($activity_emp_name),
	        'complaint_abr'=>array($complaint_abr),
	        'restoration_time'=>array($restoration_time),
	        'cust_name'=>array($row['GLH_CUST_NAME']),
	        'duration'=>array( (isset($_REQUEST['task_duration'])?array_sum($_REQUEST['task_duration']):'') ),
	        'Planned_Date'=>array($scheduled_on),
	        'Milestone_Name' => array($problem), );
		$category=3;
		$noti_type	=	2;
		$complaint_list = get_complaint('',$GCH_COMPLAINT_CODE);
		$title	=	$row['GLH_CUST_NAME']."-".$row['GLH_CUST_STREETADDR2'];
		$message=	$complaint_list[0][1]." Ticket Assigned to you which is scheduled on $scheduled_on .";
		if($sender_id!='9999'){
			$message .= " Assigned by $activity_emp_name ";
		}
		if($gcd_status!='T1' && $gcd_status!='T6'){ // For Employee
			notificaton_entry((int)$emp_id,$title,$message,$noti_type,(int)$cust_id,(int)$complaintid,1);
		}
		if($gcd_status!='T6' && ($send_support_ticket_noti) && ($GCD_NATURE!='22') ){
		    notificaton_entry(0,$problem,$remarks,$noti_type,(int)$cust_id,(int)$complaintid,1,2); //FOR CUSTOMER
			$ticket_dtl = get_ticket_dtl($complaintid);
			$created_by 		= isset($ticket_dtl['created_emp_id'])?(int)$ticket_dtl['created_emp_id']:9999;
			$created_emp_name 	= isset($ticket_dtl['created_emp_name'])?(string)$ticket_dtl['created_emp_name']:'';
			if( ($created_by!=9999) && ($created_by!=$sender_id) ){
				$creator_message = " Hi $created_emp_name, <br><br> For this Support Id <b>$complaintid</b>, $activity_emp_name has updated the remarks as <b>$remarks</b> and the support status is <b>$gtm_status_name</b> ";
				notificaton_entry($created_by,$title,$creator_message,11,(int)$cust_id,(int)$complaintid,1);
			}
		}
		if($row['GTM_GROUP_ID']=='6' and $emp_id!='' and $scheduled_on!='' ){  /*training scheduled*/
		  	$category_temp=51;
			$sms_status=get_sms_config_info($category_temp);
			if($sms_status=='A'){
				$category=$category_temp;
			}
			//send_to_exe_support_scheduled($db_sms_content_config,$category,$emp_id,$gem_mobile);
		  	send_to_customer_regarding_training($db_sms_content_config,128,$training_status);
        }else if($row['GTM_GROUP_ID']=='6' and $training_status!='' and $scheduled_on==''){ /*training not scheduled*/
			if($scheduled_on==''){
		  		send_to_customer_regarding_training($db_sms_content_config,54,$training_status);
			}
		}else if($row['GTM_GROUP_ID']=='3' and $row['male_stone']!=''){//solved issue of training
			$category=128;
			$mobile_number_arr=explode(',',$cust_mobile_no);
			$sms_content=get_formatted_content($db_sms_content_config,$category);
			entry_sending_sms_to_customer($mobile_no,$sms_content,$category,$cust_id,0,$sender_id,$send_to_alert=0,null,($mobile_no==null?false:true));
		}else if($row['GTM_GROUP_ID']=='3'){/* if issues solved */
		    if($GCD_NATURE==2){ //solved over the phone itself
		        $category_temp=55;
		        $sms_status=get_sms_config_info($category_temp);
		        if($sms_status='A'){
		            $category=$category_temp;
		        }
		        $sms_content=get_formatted_content($db_sms_content_config,$category);
		        entry_sending_sms_to_customer($mobile_no,$sms_content,$category,$cust_id,0,$sender_id,$send_to_alert=0,null,($mobile_no==null?false:true));
		    }else{
		        $category=13;
		        if($gcd_status!=""){
		            $sms_template_id = (int)get_single_value_from_single_table("GTM_SMS_TO_CUSTOMER", "gft_status_master", "GTM_CODE", "$gcd_status");
		            if($sms_template_id>0){
		                $category = $sms_template_id;
		            }
		        }
		        $sms_content=get_formatted_content($db_sms_content_config,$category);
		        entry_sending_sms_to_customer($mobile_no,$sms_content,$category,$cust_id,0,$sender_id,$send_to_alert=0,null,($mobile_no==null?false:true));
		        if(($customer_email_id!="" && $customer_email_id!=null) && ($gcd_status=='T43' || $gcd_status=='T41')){
		            $lead_owner = get_lead_field_executive($cust_id);
		            $mail_template_id = 311;//No internet closed
		            if($gcd_status=='T41'){
		                $mail_template_id = 314;//No response closed
		            }
		            send_formatted_mail_content($db_sms_content_config,0,$mail_template_id,'','',array($customer_email_id),array($emp_code,$lead_owner));
		        }
		    }
			$mailRefId =  (int)get_single_value_from_single_table("GMS_MAIL_HDR_ID", "gft_mail_support", "GMS_SUPPORT_ID", "$complaintid");
			if($mailRefId>0){
			    //Short_Link Summary1 Summary5
			    $feedback_link = get_connectplus_config();
			    $link  = isset($feedback_link['support_feedback_url'])?$feedback_link['support_feedback_url']:"";
			    $support_params = array("supportId"=>"$complaintid","purpose"=>"reopen");			    
			    $db_sms_content_config['Short_Link'] = array($link.urlencode(lic_encrypt(json_encode($support_params), $secret)));
			    $support_params['purpose'] = "feedback";
			    for($inc=1;$inc<=5;$inc++){
			        $support_params['selectedRating'] = "$inc";
			        $db_sms_content_config["Summary$inc"] = array($link.urlencode(lic_encrypt(json_encode($support_params), $secret)));
			    }	
			    $db_sms_content_config["remarks"]=array($solution_given_content!=""?"<p class='reportname'>Given Solution :<br><span>$solution_given_content</span></p>":"");
			    $mailContentList=get_formatted_mail_content($db_sms_content_config,6,367);
			    $mailContent = isset($mailContentList['formated_content'])?$mailContentList['formated_content']:"";
			    check_and_send_customer_mail($complaintid,$mailContent,$sender_id,$upload_file,$customer_email_id,$reply_cc_mail_ids);
			}
		}else if($row['GTM_GROUP_ID']==2){ /*pending customer */
			$category=12; 
			$sms_content=get_formatted_content($db_sms_content_config,$category);
			entry_sending_sms_to_customer($mobile_no,$sms_content,$category,$cust_id,0,$sender_id,$send_to_alert=0,null,($mobile_no==null?false:true));
			
		}else if($row['GTM_GROUP_ID']==1){ /*pending support */
			$category=29; 
			$sms_content=get_formatted_content($db_sms_content_config,$category);
			entry_sending_sms_to_customer($mobile_no,$sms_content,$category,$cust_id,0,$sender_id,$send_to_alert=0,null,($mobile_no==null?false:true));
			
			if($emp_id!='' and $emp_id!='0'){
				//send_to_exe_support_scheduled($db_sms_content_config,3,$emp_id,$gem_mobile);
			}
			if($emp_id!=0 and $emp_id!=''){
				$sql=query_ass_expiry();
				$sql.= " WHERE GLH_LEAD_CODE=$cust_id and GID_VALIDITY_DATE<date(now()) and GID_EXPIRE_FOR in (1,3) ";
				$result=execute_my_query($sql);
				$rs=execute_my_query($sql);
				while($row=mysqli_fetch_array($rs)){
					$db_sms_content_config1=array(
						'productname' => array($row['GPM_PRODUCT_NAME']),
						'abr' => array($row['GPM_PRODUCT_ABR']),
						'ass_order' => array($row['GPM_PRODUCT_ABR']),
						'installed_date' => array(local_date_format($row['GID_INSTALL_DATE'])),
						'cust_name' => array($row['GLH_CUST_NAME']),
						'customer_id' => array($row['GLH_LEAD_CODE']),
						'ass_end' => array(local_date_format($row['GID_VALIDITY_DATE'])),
						'shopname' => array($row['GLH_CUST_NAME']),
						'emp_name' => array(get_short_name($emp_id)), 
						'ass_value' =>array($row['ass_value']),
						'emp_mobileno'=>array($row['GEM_MOBILE']),
						'sender_id'=>array($sender_id) 
					);
					send_to_exe_support_scheduled($db_sms_content_config1,105,$emp_id,$gem_mobile);
				}
			}
		}else if($row['GTM_GROUP_ID']!='3' and $row['GCH_REPORTED_TIME']==$row['GCD_REPORTED_DATE']){ /* issue filled now only and not solved*/
			$category=8; 
			$sms_content=get_formatted_content($db_sms_content_config,$category);
			if($GCD_NATURE!=22) { // 22 -> IOT nature.. Don't send sms to IOT complaint  
			    entry_sending_sms_to_customer($mobile_no,$sms_content,$category,$cust_id,0,$sender_id,$send_to_alert=0);
			}
			if($emp_id!='' and $emp_id!=0){
				//send_to_exe_support_scheduled($db_sms_content_config,3,$emp_id,$gem_mobile);
				$sql=query_ass_expiry();
$sql.=<<<END
 WHERE GLH_LEAD_CODE=$cust_id and GID_VALIDITY_DATE<date(now()) and GID_EXPIRE_FOR in (1,3)
END;
				$result=execute_my_query($sql);
				if($result){
					while($row=mysqli_fetch_array($result)){
						$db_sms_content_config1=array(
							'productname' => array($row['GPM_PRODUCT_NAME']),
							'abr' => array($row['GPM_PRODUCT_ABR']),
							'ass_order' => array($row['GPM_PRODUCT_ABR']),
							'installed_date' => array(local_date_format($row['GID_INSTALL_DATE'])),
							'cust_name' => array($row['GLH_CUST_NAME']),
							'customer_id' => array($row['GLH_LEAD_CODE']),
							'ass_end' => array(local_date_format($row['GID_VALIDITY_DATE'])),
							'shopname' => array($row['GLH_CUST_NAME']),
							'emp_name' => array(get_short_name($emp_id)), 
							'ass_value' =>array($row['ass_value']),
							'sender_id'=>array($sender_id)
						);
						send_to_exe_support_scheduled($db_sms_content_config1,105,$emp_id,$gem_mobile);
					}
				}
			}
		}else{
			if($emp_id!='' and $emp_id!='0'){
				//send_to_exe_support_scheduled($db_sms_content_config,3,$emp_id,$gem_mobile);
			}
		}
		//return true;
	}
}


/** 
 * @param string $change_to_lead_status
 * @param string $lead_code
 * @param string $user_id
 * 
 * @return void
 */
function send_sms_regarding_lead_status_change($change_to_lead_status,$lead_code,$user_id){
	$query_q2="select glh_status,GLH_REASON_FOR_STATUS_CHANGE_DTL,GRL_NAME,glh_lfd_emp_id," .
			" GCS_SMS_TO_MGMT,GCS_SMS_TO_REP_MGR,GCS_NAME,GLH_CUST_NAME from gft_lead_hdr " .
			" inner join gft_customer_status_master cs on (GCS_CODE=glh_status) ".
			" left join gft_reason_for_change_lstatus ls on (GRL_ID=GLH_REASON_FOR_STATUS_CHANGE) " .
			" where glh_lead_code='$lead_code' and glh_status='$change_to_lead_status' " .
			" and (GCS_SMS_TO_MGMT='Y' or GCS_SMS_TO_REP_MGR='Y') ";
	$result_q2=execute_my_query($query_q2);
	if((mysqli_num_rows($result_q2)>0) && $qd=mysqli_fetch_array($result_q2)){
		$lead_status=$qd['GCS_NAME'];
		$reason=$qd['GRL_NAME'];
		$reason.=" ".$qd['GLH_REASON_FOR_STATUS_CHANGE_DTL'];
		$customer_name=$qd['GLH_CUST_NAME'];
		if(trim($reason)=='') return;
		$emp_mobile_no=get_mobileno($user_id);
		$employee=get_short_name($user_id);
		$category=83;
		$db_sms_content_config=array('customer_id' => array(0=>"$lead_code"), 
			'lead_status' => array(0=>"$lead_status"),
			'reason' => array(0=>"$reason"),
			'employee'=>array(0=>"$employee"),
			'employee_mobile_no'=>array(0=>"$emp_mobile_no"),
			'customer_name'=>array($customer_name));   	
		$sms_content=get_formatted_content($db_sms_content_config,$category);
		if($qd['GCS_SMS_TO_MGMT']=='Y'){
			$edtl=get_detail_of_group('24');
			for($i=0;$i<count($edtl);$i++){
				$mobile_no= $edtl[$i]['mobile'];
				$to_emp=$edtl[$i]['eid'];
				entry_sending_sms($mobile_no,$sms_content,$category,$to_emp, $status=0,$user_id,$send_to_alert=0);
			}       	 
		}
		if($qd['GCS_SMS_TO_REP_MGR']=='Y'){
			$edtl2=get_mobileno_reportingmaster($user_id);
			for($i=0;$i<count($edtl2);$i++){
				$mobile_no= $edtl2[$i][0];
				$to_emp=$edtl2[$i][1];
				entry_sending_sms($mobile_no,$sms_content,$category,$to_emp,$status=0,$user_id,$send_to_alert=0);
			}
		}
	}	
}

/**
 * @param string $cp_id
 * @param string $review_id
 *
 * @return void
 */
function send_cp_review_mail($cp_id,$review_id){
	$category=49;
	$message='TODO: NOT YET DEFINED!!!';
	$subject='TODO: NOT YET DEFINED!!!';
	$query="select GPR_REVIEW_ID,GPR_CP_ID,GPR_HOT_LEAD,GPR_HOT_LEAD_POTENTIAL,GPR_PIPELINE_LEADS,
	GPR_PIPELINE_POTENTIAL,GPR_COMMENTS,GPR_RECOMMENDATION,GPR_DATE,GPR_REVIEW_EMP_ID,
	em.GEM_EMP_NAME,em.GEM_EMAIL,em1.GEM_EMP_NAME as cp_name
	from gft_partner_review_detail 
	inner join gft_emp_master em on (em.GEM_EMP_ID=GPR_REVIEW_EMP_ID) 
	inner join gft_emp_master em1 on (em1.GEM_EMP_ID=GPR_CP_ID)
	Where  GPR_REVIEW_EMP_ID='$review_id' ";
	$result=execute_my_query($query);
	$qd=mysqli_fetch_array($result);
	$db_sms_content_config=array('cp_name' => array(0=>$qd['cp_name']), 
		'hot_lead' => array(0=>$qd['GPR_HOT_LEAD']),
		'hot_lead_potential' => array(0=>$qd['GPR_HOT_LEAD_POTENTIAL']),
		'warm_lead'=>array(0=>$qd['GPR_PIPELINE_LEADS']),
		'warm_lead_potential'=>array(0=>$qd['GPR_PIPELINE_POTENTIAL']),
		'comments'=>array(0=>$qd['GPR_COMMENTS']),
		'recommendation'=>array(0=>$qd['GPR_RECOMMENDATION']),
		'review_date'=>array(0=>$qd['GPR_DATE']),
		'review_by'=>array(0=>$qd['GEM_EMP_NAME'])
		);   	
	$sms_content=get_formatted_content($db_sms_content_config,$category);
	 $cc=get_samee_const('PARTNER_MAIL_ID');
	  send_mail_function(get_samee_const("ADMIN_TEAM_MAIL_ID"),get_samee_const("PARTNER_MAIL_ID"),$subject,$message,'',$cc,$category,true,get_samee_const("PARTNER_MAIL_ID"));	
	/*pending */
}

/**
 * @param int $audit_id
 * @param int $audit_type
 * @param string $cust_id
 * 
 * @return void
 */
function send_sms_mail_regarding_cp_audit($audit_id,$audit_type,$cust_id){
	   if($audit_type==11){
		   $subject='Partner Move to Active Status on Condtion'; $category=47; 
	   }else if($audit_type==12){ 	
		   $subject='Partner under Notice Period'; $category=48; 
	   }else{
		   $subject='Unknown'; $category=48;  //TODO: Need to check the subject/category value ....
	   }
	   $message=get_audit_mail_data($audit_id);
	   $cc[0]=get_samee_const('PARTNER_MAIL_ID');
	   $cc[1]=get_cp_incharge(null,$cust_id);
	   send_mail_function(get_samee_const("ADMIN_TEAM_MAIL_ID"),get_samee_const("PARTNER_MAIL_ID"),$subject,$message,'',$cc,$category,true,get_samee_const("PARTNER_MAIL_ID"));	
}

/**
 * @param string $cust_id
 * @param string $product_skew
 * @param string $free_edition
 * @param string $existing_lead_code
 * @param string $ntimes_visited_edm
 * @param string $ntimes_visited
 * @param string $message2
 * @param string $sub_text
 * @param string $to_emp_id
 * @param string $mail_subject
 * @param string $act_id
 * @param int $numberOfStores
 *
 * @return void
 */
function mail_to_know_web_registered_dtl($cust_id,$product_skew=null,$free_edition=null,$existing_lead_code=null,$ntimes_visited_edm=null,$ntimes_visited=null,$message2=null,$sub_text='',$to_emp_id="",$mail_subject="",$act_id="",$numberOfStores=0){  
	/*Start Mail sending */
	
	$email_to= /*. (string[int]) .*/ array();

	$email_from=get_samee_const("REGISTER_WELCOME_MAIL_ID");
	$rp[0]=get_samee_const('PRESALES_MAIL_ID');
	//commented below email id, reason : To stop sending web registration mails to Kumar, we removed the marketing-team mail alias after discussed with Anupama and Venkat
	//$email_to[0]=get_samee_const('MARKETING_MAIL_ID');
	
	if(strpos($product_skew,'ST') or $free_edition=='y'){
		$download_edition="Starter Edition ";
	}else {
		$download_edition="Trial Edition ";
	}
	$ip_address=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
	$db_sms_content_config=$noti_content_config=send_customer_details_to_exec($cust_id,$act_id);
	$product_name=$db_sms_content_config['Product_Name'][0];
	$additional_subject	=	"$product_name - $download_edition";
	if($sub_text!=""){
		$additional_subject	=	$sub_text;
	}
	if($db_sms_content_config['Country'][0]!='India'){
		$subject="Foreign customer registration-$additional_subject";
	}else {
		$subject="Indian customer registration-$additional_subject";
	}
	if($mail_subject!=""){
		$subject = $mail_subject;
	}
	$territory_incharge=$db_sms_content_config['lead_field_exec'][0];
	if($to_emp_id!=""){
		$territory_incharge=$to_emp_id;
	}
	$cc=/*. (string[int]) .*/ null;global $GLH_CREATED_CATEGORY;
	$db_sms_content_config['Now_Registered_in'][0]='';	
	$db_sms_content_config['No_of_times_contacted_edm'][0]='';
	$db_sms_content_config['No_of_times_contacted'][0]='';
	if($existing_lead_code!=null && isset($GLH_CREATED_CATEGORY) and $GLH_CREATED_CATEGORY!=''){
		$result=execute_my_query("select GCC_NAME from gft_lead_create_category where gcc_id=$GLH_CREATED_CATEGORY ");
		if($result){
			$qd=mysqli_fetch_array($result);
			$db_sms_content_config['Now_Registered_in'][0]="<tr><td>Now Registered In</td><td>".$qd['GCC_NAME']."</td><tr>";
		}
	}
	if($ntimes_visited_edm!=null && $ntimes_visited_edm!=0){
		  $db_sms_content_config['No_of_times_contacted_edm'][0]="<tr bgcolor='teal'><td>No. of times Contacted (From EDM)</td><td align='center'>".$ntimes_visited_edm."</td><tr>";   
	}
	if($ntimes_visited_edm!=null && $ntimes_visited_edm!=0){
		  $db_sms_content_config['No_of_times_contacted'][0]="<tr bgcolor='teal'><td>No. of times Contacted </td><td align='center'>".$ntimes_visited."</td><tr>";   
	}
	if($territory_incharge!='' and $territory_incharge!=SALES_DUMMY_ID)
	{
		$email_to[0]=get_email_addr($territory_incharge);
		if(is_authorized_group_list($territory_incharge,array(5)) || (in_array($GLH_CREATED_CATEGORY, array(70,71)))){
			$cc=get_email_addr_reportingmaster($territory_incharge,true,explode(",", EMP_IDS_TO_SKIP_REPORTING_MAIL));
		}else if(is_authorized_group_list($territory_incharge,array(13,34,39))){
			//$cc[0]=get_samee_const("PARTNER_MAIL_ID");
			$cc[0]=get_cp_incharge($territory_incharge);
		}
	}
	if($GLH_CREATED_CATEGORY==52){//ERP registration include sales management mail id		
		//$cc[] = get_samee_const("Sales_Mgmt_Mail");
		$noti_content_config['Lead_Life_Cycle_Status'] = array($noti_content_config['Lead_Life_Cycle_Status'][0]);
		$noti_content_config['Lead_Incharge'] = array(get_emp_name($territory_incharge));
		send_formatted_notification_content($noti_content_config, 2, 81, 1, $territory_incharge);
		$sms_content=get_formatted_content($noti_content_config,199);
		$sms_content=htmlentities($sms_content);
		$emp_dtl = get_emp_master($territory_incharge);
		$reporting_manager_id = isset($emp_dtl[0][6])?(int)$emp_dtl[0][6]:0;
		$emobile_no = isset($emp_dtl[0][3])?(int)$emp_dtl[0][3]:0;
		$rep_emobile_no = isset($emp_dtl[0][15])?(int)$emp_dtl[0][15]:0;
		entry_sending_sms($emobile_no,$sms_content,199,$territory_incharge,$status=1,$sender=0,$send_to_alert=0);
		if($reporting_manager_id>0){
			send_formatted_notification_content($noti_content_config, 2, 81, 1, $reporting_manager_id);
			entry_sending_sms($rep_emobile_no,$sms_content,199,$reporting_manager_id,$status=1,$sender=0,$send_to_alert=0);
		}		
	}
	if($GLH_CREATED_CATEGORY==52 || $GLH_CREATED_CATEGORY==53 || $numberOfStores>1){//ERP and Corporate lead registration include consultant mail id
		$result_business_incharge = execute_my_query("select GLI_BUSINESS_CONSULTANT,GEM_EMAIL from gft_lmt_incharge_master ".  
								" INNER JOIN gft_emp_master ON(GLI_BUSINESS_CONSULTANT=GEM_EMP_ID) ".
								" where GLI_CATEGORY='".($numberOfStores>1?"52":$GLH_CREATED_CATEGORY)."' AND GLI_ACTIVE_STATE='A' AND GEM_STATUS='A' limit 1");
		if($row_busi=mysqli_fetch_array($result_business_incharge)){
			$cc[] = $row_busi['GEM_EMAIL'];
		}
	}
	$db_sms_content_config['download_edition'][0]=$download_edition;
	$msg_c=get_formatted_mail_content($db_sms_content_config,$category=2,$mail_template_id=6);
	$msg=$msg_c['content'];
	$content_type=$msg_c['content_type'];
	if($existing_lead_code!=null and $existing_lead_code==$cust_id and $message2!=null){
		$msg.=$message2;
	}
	send_mail_from_sam($category=2,$email_from,$email_to,$subject,$msg,$attachment_file_tosend=null,
	$cc,$content_type,$reply_to=$rp,$from_page=null,$user_info_needed=true,null,null,6);
	/*End Mail sending */
}

/**
 * @param string $cust_id
 * @param string $product_code
 * @param string $product_skew
 * @param string $free_edition
 * @param string $order_no
 * @param string $contact_name
 * @param string $email 
 * @param string $vertical
 * @param boolean $is_order_no
 * @param string $confirm_code
 *
 * @return void
 */
 
function mail_to_webcustomer_product_info($cust_id,$product_code,$product_skew,$free_edition,$order_no,$contact_name,$email,$vertical=null,$is_order_no=false,$confirm_code=''){
	$email_from=get_samee_const("REGISTER_WELCOME_MAIL_ID");
	$subject="Welcome to GoFrugal Technologies";
	$subject="Thanks for Registration ";
	$email_to=$email;
//	$rp[0]=get_samee_const("PRESALES_MAIL_ID");
//	$rp[1]=get_samee_const("GFT_CUST_CARE_EMAILID");
	$step_by_step_instruction='';$sample_db_link_str='';
	$version_dtl=get_version($product_code,$product_skew,$select=false,$latest='y');
	$step_by_step_instruction_file=(isset($version_dtl[0][4])?$version_dtl[0][4]:'');
	$support_email=(isset($version_dtl[0][16])?$version_dtl[0][16]:'');
	$live_chat_url=(isset($version_dtl[0][17])?$version_dtl[0][17]:'');
	$sample_db_link=(($vertical!='')?get_sample_db_link($vertical,$product_code,$product_skew):'');
	$sample_db_link=(($sample_db_link=='' && isset($version_dtl[0][11]))?$version_dtl[0][11]:$sample_db_link);
	$CRASH_RESTORE_LINK=(isset($version_dtl[0][15])?$version_dtl[0][15]:'');
	if($free_edition!='y' && $sample_db_link!=''){
		$readme_link=$version_dtl[0][3];		
		$sample_db_link_str='<tr><td>Sample Database</td><td>: <a href='.$sample_db_link.' target=_blank>Click here to download</a></td></tr>';
		if($CRASH_RESTORE_LINK!=''){
		$sample_db_link_str.="<tr><td colspan='2'><i>Note: Before Apply Sample database Download <a href='".$CRASH_RESTORE_LINK."'>Crash restore .Exe </a> and " .
				"read the <a href='".$readme_link."'>Readme </a> file to know the procedure.</i>";
		}
	}
	
		
		$installation_guide_url=(isset($version_dtl[0][6]) and $version_dtl[0][6]!=''?'<tr><td>Installation Video </td><td>:<a href="'.$version_dtl[0][6].'">Click Here </td>':'');
		$download_link=(isset($version_dtl[0][1])?$version_dtl[0][1]:'');
		$vertical_name=get_vertical_name_for($vertical);
	if($step_by_step_instruction_file!=''){
		//$step_by_step_instruction="Please see the below link to generate the Activation key and Installation help <br>";
		$step_by_step_instruction="".$step_by_step_instruction_file." <br>  ";
	}
	if(($product_code=="500" or $product_code=="100") and $free_edition=='y'){
		$traning_magerial_link="http://www.gofrugal.com/pos/point-of-sale-training-material.html";
	}else {
		$traning_magerial_link="http://www.gofrugal.com/training-material.html";
	}	
	$pr_info=get_product_list_family($launch_on=null,$status=null,$internal_pr_not_in=null,
	$except_pr=null,$show_only_head=false,$product_code);
	$product_name=$pr_info[0][1];
	$orderno1=substr($order_no,0,5);
	$orderno2=substr($order_no,5,5);
	$orderno3=substr($order_no,10,5);
	$order_no_str='';$username_pwd='';
	$default_user	=	"";
	$default_pass	=	"";
	if($order_no!=''){
		$order_no_str=$orderno1."-".$orderno2."-".$orderno3."-0001 ";
	}
	if($product_code=="100" or $product_code=="500" or $product_code=="900"){
		$username_pwd="<tr><td>Username </td><td>:</td> <td> admin </td></tr> <tr><td> Password </td><td>:</td><td>admin </td></tr> ";
		$default_user	=	"admin";
		$default_pass	=	"admin";
	}else if($product_code=="200"){
		$username_pwd="<tr><td>Username </td><td>:</td> <td> SUPERUSER </td></tr> <tr><td>Password </td><td>:</td><td>SUPER </td></tr> ";
		$default_user	=	"SUPERUSER";
		$default_pass	=	"SUPER";
	}
	if($free_edition=='y' and $product_skew!='' and (in_array($product_code, array('100','200','500','900'))) ){
		$url_domain_name='';
		if($_SERVER['SERVER_ADDR']=='10.0.1.248'){
			$au=(isset($_POST['au'])?(string)$_POST['au']:'');
			$url_domain_name="gofrugal.com$au";
		}
		$param="?newreg=hide&cust_id=$cust_id&emailID1=$email&order_no1={$order_no}0001".
			"&product={$product_code}{$product_skew}&free_edition=y";
		if(($product_code=='500' or $product_code=='100') and $url_domain_name!=''){
			$url="http://$url_domain_name/pos/free-license-user-download-point-of-sale.html$param";
		}else if($product_code=='900'  and $url_domain_name!=''){
			$url="http://$url_domain_name/accounts/free-license-accounts.html$param";
		}else if($product_code=='200'  and $url_domain_name!=''){
			$url="http://$url_domain_name/de/wholesalesoftware-free-license-de.html$param";
		}else if($product_code!='' and $url_domain_name!=''){
			$url="http://$url_domain_name/thankyou.html$param";
		}else{
			//Not used now. 
			//$url="http:sam.gofrugal.com/website/web_cust_request_actkey.php$param";
		}
		//$traning_magerial_link.="\n<br>Please Click the bellow link to get Activation Key.\n<br> $url";
	}

	$mail_template_id=7;
	if($product_code==601){
		$email_from=get_samee_const($product_code."_WELCOME_MAIL_ID");
		$username_pwd="http://www.true-pos.com/signup.html?lcode=$cust_id&order_no=$order_no&product=60106.0RC30";
		//$mail_template_id=19;
	}
	if($product_code==602){
		$email_from=get_samee_const($product_code."_WELCOME_MAIL_ID");
		$username_pwd="http://www.true-register.com/signup.html?lcode=$cust_id&order_no=$order_no&product=60206.0RC30";
		$mail_template_id=25;
	}
	if($product_code==603){
		$email_from=get_samee_const($product_code."_WELCOME_MAIL_ID");
		$username_pwd="http://www.true-store.com/signup.html?lcode=$cust_id&order_no=$order_no&product=60305.0RC30";
		$mail_template_id=66;
	}	
	$db_sms_content_config=/*. (string[string][int]) .*/ array();
	$db_sms_content_config=array(
		'Customer_Name' => array($contact_name),
		'Customer_Id' => array($cust_id),
		'Order_No' => array($order_no_str),
		'Default_Username_Password' => array($username_pwd),
		'Default_Username' => array($default_user),
		'Default_Password' => array($default_pass),
		'Step_By_Step_Instruction' => array($step_by_step_instruction),
		'Training_Material_Link' => array($traning_magerial_link),
		'Customer_Support_No' => array(get_samee_const('GFT_CUST_CARE_NO')),
		'installation_guide_url'=>array($installation_guide_url),
		'Product_Name'=>array($product_name),
		'sample_db'=>array($sample_db_link_str),
		'Vertical'=>array($vertical_name),
		'CRASH_RESTORE_LINK'=>array($CRASH_RESTORE_LINK),
		'SAMPLE_DB_LINK'=>array($sample_db_link),
		'Download_link'=>array($download_link),
		'Support_Mail'=>array($support_email),
		'Live_Chat_Link'=>array($live_chat_url));
	if($confirm_code!=''){
		$db_sms_content_config['Email_Activation_Link']=array($confirm_code);
	}
	$msg_c=get_formatted_mail_content($db_sms_content_config,$category=31,$mail_template_id);
	$msg=$msg_c['content'];
	$content_type=$msg_c['content_type'];
	$subject=$msg_c['Subject'];
	$reply_to=$msg_c['reply_to'];

	//send_mail_from_sam($category=31,$email_from,$email_to,$subject,$msg,$attachment_file_tosend=null,
	//$cc=null,$content_type,$reply_to=$rp,$from_page=null,$user_info_needed=false);

	$EMBAttachment=isset($msg_c['EMBAttachment'])?$msg_c['EMBAttachment']:null;
	if($mail_template_id!='7'){
		send_mail_function($email_from,$email_to,$subject,$msg,$attachment_file_tosend=null,$cc=null,$category,$content_type,
				$reply_to,$is_status_needed=false,$crontab_call=false,$fromname=null,$mail_template_id,$mail_compile_id=null,$EMBAttachment);
	}
}/*end of fn */

/**
 * @param string $cust_id
 * @param string $registration_from
 * 
 * @return void
 */
function send_cp_registration_mail($cust_id,$registration_from=""){	
		 		$email_from=get_samee_const('PARTNER_MAIL_ID');
		 		$email_to[0]=get_email_addr_customer($cust_id);
				$rp[0]=get_samee_const('PARTNER_MAIL_ID');
				$db_sms_content_config=$noti_content_config=send_customer_details_to_exec($cust_id);
				$db_sms_content_config['buss_info_link']=array("http://www.gofrugal.com/partners-sign-up.html?cust_id=$cust_id");
				send_formatted_mail_content($db_sms_content_config,50,77,null,null,$email_to,null, null, '', '',"$email_from");
				$email_from=get_samee_const('PARTNER_MAIL_ID');
				$subject_add=$db_sms_content_config['Country'][0];
				$cc=array();
				//$email_to[0]=get_samee_const('PARTNER_MAIL_ID');
				if($db_sms_content_config['lead_field_exec']!=''){
					$email_to[0]=get_email_addr($db_sms_content_config['lead_field_exec'][0]);
					$cc=get_email_addr_reportingmaster($db_sms_content_config['lead_field_exec'][0],true,explode(",", EMP_IDS_TO_SKIP_REPORTING_MAIL));
				}
				if(isset($email_to[0]) && $email_to[0]!=""){	
					send_formatted_notification_content($noti_content_config,50,36,1,$db_sms_content_config['lead_field_exec'][0]);
				}
			    $message=get_formatted_mail_content($db_sms_content_config,$category=51,$mail_template_id=78);
                $body_message=$message['content'];
                $content_type=$message['content_type'];
                $subject=$message['Subject'].$registration_from.'-'.$subject_add;
                $at_file=$message['Attachment'];
                if(isset($email_to[0]) && $email_to[0]!=""){
				    send_mail_function($email_from,$email_to,$subject,$body_message,$at_file,$cc,51,$content_type,$rp,false,false,/*$fromname=*/null);
                }
				
 }//end of function
 
 
/**
 * @param string $lead_code
 * @param string $order_no
 * @param string $domin_name
 * @param string $customer_name
 * @param string $product_code
 * @param string $email
 * @param string $user_password
 *
 * @return boolean
 */
 
function mail_to_saas_customer($lead_code,$order_no,$domin_name,$customer_name,$product_code,$email,$user_password=''){
		$website_url=get_samee_const('Unsubscribe_Mail_URL');
		$unsubscribe_link	=	$website_url."&emailID=".md5($email);
	   $db_sms_content_config=array(
				'Customer_Name' => array($customer_name),
				'Customer_Id' => array($lead_code),
				'Order_No' => array($order_no),
				'Customer_Support_No' => array(get_samee_const('GFT_CUST_CARE_NO')),
				'domin_name'=>array($domin_name),
	   			'user_name'=>array($email),
	   			'user_password'=>array($user_password),
	   			'Unsubscribe_Link'=>array($unsubscribe_link),
				'Email'=>array($email));
		switch($product_code){
			case '601':		$mail_template_id=99;	break; /*truepos */
			case '602':		$mail_template_id=28; 	break; //28  -trueregister
			case '603':		$mail_template_id=67;	break;	//67  -truestore
			case '605':		$mail_template_id=177;	break;	//ServQuick
			default:		$mail_template_id=99;	break; /*Default assumed to be TruePOS*/
			
		}
		$outgoing_email_id=send_formatted_mail_content($db_sms_content_config,$category=37,$mail_template_id,null,null,array($email));
		return $outgoing_email_id;
}

/**
 * @param string $lead_code
 * @param string $order_no
 * @param string $domin_name
 * @param string $customer_name
 * @param string $product_code
 * @param string $email
 *
 * @return boolean
 */

function regret_mail_to_webpos_customer($lead_code,$order_no,$domin_name,$customer_name,$product_code,$email){
	   
	   $db_sms_content_config=array(
				'Customer_Name' => array($customer_name),
				'Customer_Id' => array($lead_code),
				'Order_No' => array($order_no),
				'Customer_Support_No' => array(get_samee_const('GFT_CUST_CARE_NO')),
				'domin_name'=>array($domin_name));
		switch($product_code){
			case '601':		$mail_template_id=100;	break; /*truepos */
			case '602':		$mail_template_id=28; 	break; //28  -trueregister
			case '603':		$mail_template_id=67;	break;	//67  -truestore
			case '605':		$mail_template_id=181;	break; /*servQuick */
			default:		$mail_template_id=100;	break; // Assumed to be truepos
			
		}
		$outgoing_email_id=send_formatted_mail_content($db_sms_content_config,$category=37,$mail_template_id,null,array($lead_code));
		return $outgoing_email_id;
}

/**
 * @param string $lead_code
 * @param int $GCH_COMPLAINT_CODE
 * @param int $support_id
 * @param string $status
 *
 * @return boolean
 */
function check_for_complaint_exists($lead_code, $GCH_COMPLAINT_CODE, $support_id, $status){
	$query =" select GCH_LEAD_CODE,count(GCD_ACTIVITY_ID) as no_of_act from gft_customer_support_hdr " .
			" join gft_customer_support_dtl on (GCD_COMPLAINT_ID=GCH_COMPLAINT_ID and GCD_STATUS = '$status' AND GCH_COMPLAINT_CODE=$GCH_COMPLAINT_CODE ) " .
			" where GCH_LEAD_CODE='$lead_code' and GCH_COMPLAINT_ID='$support_id' GROUP BY  GCH_LEAD_CODE  having no_of_act > 1 ";
	$res = execute_my_query($query);
	if(mysqli_num_rows($res) > 0){
		return true;
	}else{
		return false;
	}
}
/**
 * @param string $gtc_number
 *
 * @return boolean
 */
function check_for_voicemail_open($gtc_number){
	$query_res = execute_my_query("select GTC_ID from gft_techsupport_incomming_call where GTC_CALL_STATUS=2 and GTC_RECALL_STATUS='N' and GTC_NUMBER='".$gtc_number."'");
	if(mysqli_num_rows($query_res) > 1){  //greater than one because this check happens after current entry saved
		return true;
	}
	return false;
}

/** * 
 * @param string $contact_no
 * 
 * @return boolean
 */
function check_can_send_sms($contact_no){
	$len = strlen($contact_no); 
	if($len != 10){
		if($len==11){
			if(substr($contact_no,0,1)!='0'){
				return false;
			}
		}else if($len==12){
			if(substr($contact_no,0,2)!='91'){
				return false;
			}
		}else if($len==13){
		    if(substr($contact_no,0,3)!='+91'){
		        return false;
		    }
		}else{
			return false;
		}
	}
	$mobileno=adjustMobileNumber($contact_no);
	if($mobileno==''){
		return false;
	}
	$mobile_no_startwith=substr($mobileno,0,1);
	if($mobile_no_startwith!='9' and $mobile_no_startwith!='8' and $mobile_no_startwith!='7' and $mobile_no_startwith!='6' ){
		return false;
	}
	return true;
}

/**
 * @return string[string]
 */
function get_solution_infinity_account_dtl(){
	$arr['end_point'] 	= "http://api-alerts.solutionsinfini.com/v3/?method=sms";
	$arr['sender']		= "GOFRUG";
    $arr['api_key'] 	= "Add0e766dea83da7b6a3d6780264b999f";
	return $arr;
}
/**
 * @return string[string]
 */
function get_solution_infinity_account_for_global(){
	$arr['end_point'] 	= "http://api-global.solutionsinfini.com/v4/?method=sms";
	$arr['sender']		= "GOFRUG";
	$test_mode = get_samee_const('STORE_PAYMENT_TEST_MODE');
	if($test_mode=='0'){
		$arr['api_key'] 	= "A504a4b65819b43597cbe5ce9246271ed";
	}else{
		$arr['api_key'] 	= "A504a4b65819b43597cbe5ce9246271ed";
	}
	return $arr;
}
/**
 * @param string $sms_id
 * @param int $intl_sms
 * @param string $sender_id
 * 
 * @return void
 */
function send_sms_by_api($sms_id,$intl_sms,$sender_id){
	$sql=" select gos_id,gos_sms_content,gos_msg_sent_time,gos_receiver_mobileno,gos_sent_to_alert,gsc_priority," .
			" GSC_VALIDITY_PERIOD_HRS,GSC_DESC,GOS_ACTIVITY_ID " .
			" FROM gft_sending_sms " .
			" join gft_sms_config on(gos_category=GSC_ID and GSC_STATUS='A') " .
			" where gos_sent_to_alert=0 and gos_sms_content!='' and gos_receiver_mobileno > 1000000000 and gos_sms_status=0 " .
			" and gos_id='$sms_id' ";
	$resultsms=execute_my_query($sql);
	if(mysqli_num_rows($resultsms)==0){
		return;
	}
	$account_dtl = get_solution_infinity_account_dtl();
	$gateway_type = 1;
	if($intl_sms==1){
		$account_dtl = get_solution_infinity_account_for_global();
		$gateway_type = 2;
		if($sender_id!=""){
			$account_dtl['sender']=$sender_id;
		}
	}
	$main_url = $account_dtl['end_point'];
	$main_url .= "&api_key=".$account_dtl['api_key'];
	$main_url .= "&sender=".$account_dtl['sender'];
	$test_mode = get_samee_const('STORE_PAYMENT_TEST_MODE');
	$domain = "labtest.gofrugal.com";
	if($test_mode=='0'){
		$domain = 'sam.gofrugal.com';
	}
	$delivery_url = "https://$domain/sms_delivery_update.php?delivered={delivered}";
	$arr_of_sms_id = array();
	while($row1 = mysqli_fetch_array($resultsms)){
		$to_contact 	= $row1['gos_receiver_mobileno'];
		$sms_content 	= urlencode($row1['gos_sms_content']);
		$sms_id 		= $row1['gos_id'];
		$sms_count = ceil((strlen($sms_content)/160));
		$sms_count_dtl = array('GSD_SMS_ID'=>$sms_id,'GSD_COUNT'=>"$sms_count",'GSD_GATEWAY_TYPE'=>"$gateway_type");
		array_insert_query("gft_sent_sms_dtl", $sms_count_dtl);
		$post_url 		= $main_url."&to=".trim($to_contact)."&message=$sms_content";
		$post_url 		.= "&custom=$sms_id&dlrurl=".urlencode($delivery_url);
		$ch = curl_init($post_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$api_response = (string)curl_exec($ch);
		
		$json_arr = json_decode($api_response,true);
		$resp_status = isset($json_arr['status'])?$json_arr['status']:"";
		$data    = isset($json_arr['data'])?$json_arr['data']:null;
		if($resp_status=="OK"){
		    $tid = isset($data[0]['id'])?$data[0]['id']:"";
			$update_query = " update gft_sending_sms set gos_sent_to_alert=1,gos_sms_status=7 where gos_id='$sms_id' ";
			execute_my_query($update_query);
			execute_my_query("UPDATE gft_sent_sms_dtl SET GSD_GATEWAY_STATUS='AWAITED-DLR',GSD_TID='$tid'  WHERE GSD_SMS_ID='$sms_id'");
		}
		$resp_arr['json'] = $api_response;
		$resp_arr['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curl_error = curl_error($ch);
		if($curl_error!=''){
			$resp_arr['curl_error'] = $curl_error;
		}
		curl_close($ch);
		//log sms request and response
	}
}

?>
