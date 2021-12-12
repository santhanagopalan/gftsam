<?php
require_once(__DIR__ ."/common_util.php");
require_once(__DIR__ ."/inc.new_lead_activity_entry.php");
require_once(__DIR__ ."/ismile/ismile_util.php");
require_once( __DIR__ . "/oauth/ZohoChat.php");
/**
 * @param string $partner_lead_code
 *
 * @return string[string]
 */
function its_active_partner_lead_code($partner_lead_code){

	$query_non_emp=<<<END
	select GEM_EMP_ID,GLH_LEAD_CODE,GLH_CUST_NAME,GLH_CUST_STREETADDR2,gem_role_id,gcc_contact_name
	from gft_cp_info cp
	join gft_leadcode_emp_map lemp on (GLEM_LEADCODE=CGI_LEAD_CODE )
	join gft_emp_master em on (CGI_EMP_ID=em.gem_emp_id and em.gem_status='A' and gem_role_id in (21,26,27,83) )
	join gft_lead_hdr lh on (lh.GLH_LEAD_CODE=CGI_LEAD_CODE )
	join gft_customer_contact_dtl ccd on (GCC_LEAD_CODE=GLH_LEAD_CODE)
	where GLH_LEAD_CODE='$partner_lead_code'
	and CGI_EMP_ID!=7004 and
	(CGI_STATUS=10 OR (CGI_STATUS=14 AND CGI_STATUS_TILL_DATE<=date(now()) ) )
	limit 1
END;
	$result_non_emp=execute_my_query($query_non_emp);
	$num_rows=mysqli_num_rows($result_non_emp);
	$return_result=/*. (string[string]) .*/ array();
	$return_result['is_cp']='false';
	$return_result['is_dealer']='false';
	if($num_rows>0){
		$return_result['is_cp']='true';
		$qd=mysqli_fetch_array($result_non_emp);
		$return_result['cp_lead_code']=$qd['GLH_LEAD_CODE'];
		if($qd['gem_role_id']=='83'){
			$return_result['is_dealer']='true';
		}
		$return_result['cp_contact_name'] = $qd['gcc_contact_name'];
		$return_result['emp_id'] = $qd['GEM_EMP_ID'];
	}
	return $return_result;

}
/**
 * @param string $id
 * @param string $question
 *
 * @return void
 */
function update_hash_question_dtl($id,$question){
	$data_question = str_replace("#Q","#q",$question);
	$qarr = explode("#q",$question);
	$insert_query = "DELETE FROM gft_chatbot_question_vs_answer WHERE GCQ_ANS_ID='$id'";
	execute_my_query($insert_query);
	for($q=0; $q < count($qarr); $q++){
		$this_question= $qarr[$q];
		$check_hash = strpos($this_question,"#");
		if ($check_hash !== false){
			//echo "<br><b>WARNING:</b> Skip this question due to error. The question contains invalid # command. [Question: $this_question] for the cateogryid: $category_id";
			continue;
		}
		if (strlen($this_question)>950){
			//echo "<br><b>WARNING:</b> Skip this question due to error. The question contains more than 1000 characters [Question: $this_question] for the cateogryid: $category_id";
			//continue;
		}
		$this_question= strtr( $this_question, array(  "\n" => "\\n",  "\r" => "\\r" , "\t" => " ","|" => " " ));
		$this_question=trim($this_question);
		if (strlen($this_question)<2){
			//skip the invalid questions
			continue;
		}
		$this_question=mysqli_real_escape_string_wrapper($this_question);
		$insert_query = "INSERT INTO gft_chatbot_question_vs_answer(GCQ_ANS_ID,GCQ_QUESTION) VALUES('$id','$this_question')";
		execute_my_query($insert_query);
	}
}
/**
 * @param string $chat_id
 *
 * @return string[int]
 */
function get_chatbot_feedback($chat_id){
	$rating_arr=array(0=>"Poor",1=>"Poor",2=>"Average",3=>"Good",4=>"Very Good",5=>"Excellent",);
	$feedback_status = "Not yet given";
	$feedback_status_subject = "";
	$feedback_html   = "";
	$common_cell_style = "style='padding: 2px;'";
	$sql_feedback_query = 	" select GEM_EMP_NAME,rating,GROUP_CONCAT(GFR_NAME SEPARATOR '--**--') FEEDBACK_REASON,date_time,".
			" feedback from chatbot.customer_feedback cf ".
			" INNER JOIN gft_emp_master em ON(GEM_EMP_ID=agent_id) ".
			" LEFT JOIN gft_rating_reason_code_dtl ON(GRR_REF_ID=cf.id AND GRR_SOURCE_TYPE=2) ".
			" LEFT JOIN gft_feedback_rating_master ON(GRR_REASON_CODE=GFR_ID) ".
			" where chat_id=$chat_id GROUP BY cf.id";
	$result_feedback =  execute_my_query($sql_feedback_query);
	while($feedback_row=mysqli_fetch_array($result_feedback)){
		$rating_no = (int)$feedback_row['rating'];
		$rating_str = $rating_arr[$rating_no];
		$feedback_reasons = explode("--**--", $feedback_row['FEEDBACK_REASON']);
		$feedback_reasons_str = "";
		$inc = 0;
		while($inc<count($feedback_reasons)){
			$feedback_reasons_str .= ($inc+1).". ".($feedback_reasons[$inc]==""?"Others":$feedback_reasons[$inc])."<br>";
			$inc++;
		}
		$feedback_time = $feedback_row['date_time'];
		$rating_given_to = $feedback_row['GEM_EMP_NAME'];
		$rating_remarks = $feedback_row['feedback'];
		$feedback_html .= "<tr><td $common_cell_style>$rating_str</td><td $common_cell_style>$rating_given_to</td>".
		                  "<td $common_cell_style>$feedback_reasons_str</td><td $common_cell_style>$rating_remarks</td>".
		                  "<td $common_cell_style>$feedback_time</td></tr>";
		$feedback_status_subject = $feedback_status_subject.($feedback_status_subject!=""?", ":"").$rating_str;
	}
	if($feedback_status_subject==""){
		$feedback_status_subject = "Not yet given";
	}
	if($feedback_html!=''){
		$feedback_status = "<table border=1><tr><th $common_cell_style>Rating</th><th $common_cell_style>Given to</th>".
		                   "<th $common_cell_style>Feedback</th><th $common_cell_style>Remarks</th>".
		                   "<th $common_cell_style>Feedback Given Time</th></tr>".$feedback_html."</table>";
	}
	$return_array[0]=$feedback_status;
	$return_array[1]=$feedback_status_subject;
	return $return_array;
}
/**
 * @param string $lead_code
 * @param string $chat_id
 * @param string $complaint_status
 * @param string $uid
 * @param int $latest_support_id
 * @param string $complaint_summary
 * @param string $solution_given
 * @param int $complaint_id
 * @param int $dev_complaint_id
 *
 * @return void
 */
function send_mail_to_team_lead($lead_code,$chat_id,$complaint_status,$uid,$latest_support_id,$complaint_summary,$solution_given='Null',$complaint_id=0,$dev_complaint_id=0){
	$product_dtl = '';
	$product_mail = '';
	$reporting_manager_id = 0;
	$mail_uid = 0;
	$product_mail = get_single_value_from_single_table("GSP_SUPPORT_MAIL_ID","gft_support_product_group","GSP_GROUP_ID","$latest_support_id");
	$product_support_group_name = get_single_value_from_single_table("GSP_GROUP_NAME","gft_support_product_group","GSP_GROUP_ID","$latest_support_id");
	$mail_uid = 1;
	if(!($product_mail && $product_mail!='' && $product_mail!=null)){
		$employee_group_query = execute_my_query("SELECT GVG_SUPPORT_GROUP from gft_voicenap_group_emp_dtl left join gft_voicenap_group ON (GVG_GROUP_ID = GVGED_GROUP_ID) where GVGED_EMPLOYEE='$uid' and GVGED_PRIMARY_GROUP=1 limit 1");
		if(mysqli_num_rows($employee_group_query)>0 && $employee_group_result = mysqli_fetch_array($employee_group_query)){
			$latest_support_id = $employee_group_result['GVG_SUPPORT_GROUP'];
			$product_mail = get_single_value_from_single_table("GSP_SUPPORT_MAIL_ID","gft_support_product_group","GSP_GROUP_ID","$latest_support_id");
			$mail_uid = 1;
		}
		if(!($product_mail && $product_mail!='' && $product_mail!=null)){
			$reporting_manager_query = execute_my_query("SELECT GER_REPORTING_EMPID from gft_emp_reporting where ger_emp_id='$uid' and GER_STATUS='A' limit 1");
			if(mysqli_num_rows($reporting_manager_query)>0 && $reporting_manager_result = mysqli_fetch_array($reporting_manager_query)){
				$reporting_manager_id = $reporting_manager_result['GER_REPORTING_EMPID'];
				$mail_uid = 2;
			}
		}
	}
	$all_response = array();
	$domain_name=$_SERVER['HTTP_HOST'];
	$domain_name_url = ($domain_name=="sam.gofrugal.com"?"gstbot":"labtest");
	$chat_result = execute_my_query(" select message,time_stamp,type,event,file_name,media_path,media_type from chatbot.transcripts ct".
			" LEFT JOIN chatbot.chat_attachment ca ON(ct.id=transcript_id)".
			" where ct.chat_id='$chat_id' order by ct.time_stamp asc");
	while($row=mysqli_fetch_array($chat_result)){
	    $response_array = /*.(string[string]).*/array();
		$response_array['message']	=	mb_convert_encoding($row['message'],'UTF-8','UTF-8');
		$media_path = $row['media_path'];
		if($row['media_type']=='1'){//For play audio
			$response_array['message'] .="<audio controls>
			<source src='https://$domain_name_url.gofrugal.com/s3tool/file/download?mediapath=$media_path' type='audio/ogg'>
			<source src='https://$domain_name_url.gofrugal.com/s3tool/file/download?mediapath=$media_path' type='audio/mpeg'>
			</audio>";
		}
		if($row['media_type']=='2'){//For image attachment
			$response_array['message']  .=<<<END
				<br>
				<a class="example-image-link" href="https://$domain_name_url.gofrugal.com/s3tool/file/download?mediapath=$media_path" data-lightbox="example-1" download><img class="example-image" src="http://$domain_name_url.gofrugal.com/s3tool/file/download?mediapath=$media_path" width="40%" height="40%" /></a>
END;
		}
		$response_array['time']	=$row['time_stamp'];
		$response_array['type']	=$row['type'];
		$chat_transcript= json_decode($row['event'],true);
		$emp_name = "";
		$buttons = array();
		if(isset($chat_transcript['attachments']) && $attachment=$chat_transcript['attachments']){
			if(isset($attachment[0]['content']) && $button=$attachment[0]['content']){
				if(isset($button['buttons']) && $all_buttons=$button['buttons']){
					foreach ($all_buttons as $key_button=>$value_button){
						$buttons[]=$value_button['value'];
					}
				}
			}
		}
		if(isset($chat_transcript['entities'][0]) && $obj=$chat_transcript['entities'][0]){
			if(isset($obj['agentInfo']['user']) && $user_dtl=$obj['agentInfo']['user']){
				$emp_name = isset($user_dtl['name'])?$user_dtl['name']:'';
			}
		}
		if($emp_name=="" && (isset($chat_transcript['address']['bot']['name']))){
			$emp_name = $chat_transcript['address']['bot']['name'];
		}
		$response_array['buttons']	=$buttons;
		$response_array['agent_name']	=$emp_name;
		$all_response[]=$response_array;
	}
	$table_content = '';
	$agent_name = get_single_value_from_single_table("gem_emp_name","chatbot.conversation_dtl LEFT JOIN gft_emp_master ON(gem_emp_id=agent_user_id)","id","$chat_id");
	$chat_time = get_single_value_from_single_table("created_date","chatbot.conversation_dtl","id","$chat_id");
	$chat_time = date('d/m/Y h:i:s a', strtotime($chat_time));
	$customer_name = get_single_value_from_single_table("GLH_CUST_NAME","gft_lead_hdr","GLH_LEAD_CODE","$lead_code");
	$complaint = get_single_value_from_single_table("GTM_NAME","gft_status_master","GTM_CODE","$complaint_status");
	$reviewed_by =get_single_value_from_single_table("GEM_EMP_name","gft_emp_master","GEM_EMP_ID","$uid");
	$table_content = "<table style='background-color:rgba(158, 158, 158, 0.06)'>";
	$i = 0;
	if(sizeof($all_response)>0) {
		foreach ($all_response as $key => $value) {
			if ($i <= 20) {
				$align = $value['type'] == 'R'?'right':'left';
				$bgcolor = $value['type'] == 'R'?"#cfdfff":"#ffe4e4";
				if($value['type'] == 'R'){
					$user_img = "<div style='display: inline-block; margin:5px;'><img height='40px' width='40px' SRC='https://sam.gofrugal.com/images/User.png'></div> ";
					$agent_image = '';
					$div_align = "float:right;max-width: 580px; padding-left: 60px;";
				}else{
					$agent_image = "<div style='display: inline-block;margin:5px;'><img height='40px' width='40px' SRC='https://sam.gofrugal.com/images/Agent-2.png'></div>";
					$user_img = '';
					$div_align = "float:left;max-width: 580px; padding-right: 60px;";
				}
				$table_content .= "<tr bgcolor='".$bgcolor."' align=".$align." style='border-spacing: 5em;'><td style='word-break: break-all; border-radius: 5px;'>";
				$table_content .= $agent_image."<div style='display:inline-block;width: 90%;'>".($value["type"] == 'R' ? "<font style='color: #0072ff; font-weight: bold;font-size: 11px;'>" . $customer_name . "</font>" : "<font style=' color: black;font-weight: bold;font-size: 11px;'>" .
						(string)$value["agent_name"] . "</font>") . "<br> <div style='text-align: left;$div_align'><font style='color: #292222;'>" .
						(string)$value["message"] . "</font></div><br><br><font style='font-size: 10px;color: grey;'>".(string)$value["time"]."</font></div>$user_img</td></tr>";
				$table_content .="<tr><td></td></tr>";
				$i++;
			} else {
				$table_content .= "<tr><td><a href='https://$domain_name/chatbot_transcript.php?chat_id=$chat_id'>Click</a> to view Full transcripts...</td></tr>";
				break;
			}
		}
	}else{
		$table_content .= "No Messages to display.";
	}
	$feedback_info = get_chatbot_feedback($chat_id);
	$feedback_status = isset($feedback_info[0])?$feedback_info[0]:"";
	$feedback_status_subject = isset($feedback_info[1])?$feedback_info[1]:"";
	$table_content .= "</table>";
	$complaint_id_str = "<a href='https://$domain_name/chatbot_transcript.php?chat_id=$chat_id'>$complaint_id</a>".
	($dev_complaint_id>0?" &nbsp;/&nbsp;Pending Dev Support id <a href='https://$domain_name/complaint_details.php?id=$dev_complaint_id'>$dev_complaint_id</a>":"");
	$suject = "$product_support_group_name Chat Summary [Ticket Id: $complaint_id] [Cust. Details: $customer_name # $lead_code] - $complaint_summary($complaint) - Rating:$feedback_status_subject";
	$db_content_config = /*. (string[string][int]) .*/array(
			'Customer_Id' => array($lead_code),
			'Customer_Name'=>array($customer_name),
			'Agent_Name'=>array($agent_name?$agent_name:'Self service'),
			'Employee_Name'=>array($reviewed_by),
			'problem_summary' =>array($complaint_summary),
			'comp_id'=>array($complaint_id_str),
			'status'=>array($complaint),
			'Start_Date_Time'=>array($chat_time),
			'Remarks'=>array($solution_given?$solution_given:'No Solution '),
			'Mail_Content'=>array($table_content),
			'Customer_Feedback'=>array($feedback_status),
			'Mail_Subject'=>array($suject)
	);
	if($mail_uid == 1 && $product_mail !='' && $product_mail!=null){
		send_formatted_mail_content($db_content_config,8,294,'','',array($product_mail));
	}elseif ($mail_uid == 2 && $reporting_manager_id != 1){
		send_formatted_mail_content($db_content_config,8,294,array($reporting_manager_id));
	}else{
		//do nothing
	}
}
/**
 * @param string $chat_id
 * @return string[int]
 */
function get_last_transcript_time_id($chat_id) {
    $last_transcript_qry = " select id,time_stamp from chatbot.transcripts where chat_id='$chat_id' order by id desc limit 1 ";
    $last_transcript_res = execute_my_query($last_transcript_qry);
    $return_arr = array();
    if($last_tran_row = mysqli_fetch_assoc($last_transcript_res)) {
        $return_arr[] = $last_tran_row['id'];
        $return_arr[] = $last_tran_row['time_stamp'];
    }
    return $return_arr;
}
/**
 * @param string $lead_code
 * @param string $chat_id
 * @param string $split_id
 * @param string $agent_id
 * @param string $summary
 * @param string $split_summary
 * 
 * @return void
 */
function get_chat_review_status_with_split($lead_code,$chat_id,$split_id,$agent_id,$summary,$split_summary) {
    $agent_map_wh = " cust_id='$lead_code' AND review_status in (1) ";
    $conv_dtl_wh = " cust_id='$lead_code' AND review_status in (1) ";
    $review_status = '2';
    if((int)$chat_id>0) {
        $agent_map_wh = " id='$chat_id' ";
        $conv_dtl_wh = " id IN ('$chat_id') ";
    }
    if((int)$split_id>0) {
        $split_end_qry = " select end_time,end_trans_id from chatbot.split_chat_dtl where split_id='$split_id' and ".
                         " end_time is null "; // end transcript time and end transcript ID will not be present only for last split
        $split_end_res = execute_my_query($split_end_qry);
        $update_cols = '';
        if(mysqli_num_rows($split_end_res)>0) {
            $last_tran_dtl = get_last_transcript_time_id($chat_id);
            if(isset($last_tran_dtl[0]) and isset($last_tran_dtl[1])) {
                $update_cols =  ",end_time='".$last_tran_dtl[1]."',end_trans_id='".$last_tran_dtl[0]."'";
            }
        }
        execute_my_query(" update chatbot.split_chat_dtl set review_status='2',review_comment='$summary',reviewed_by='$agent_id', ".
                         " reviewed_on=now(),split_status='2',split_query='$split_summary'$update_cols where split_id='$split_id' ");
    }
    $review_result = execute_my_query("select split_id from chatbot.split_chat_dtl where review_status='1' and chat_id='$chat_id' and split_status!='3'");
    if(mysqli_num_rows($review_result)>0) {
        $review_status = '1';
    } else {
        execute_my_query("delete from chatbot.agent_current_map where chat_id IN (select id from chatbot.conversation_dtl where $agent_map_wh) ");
    }
    execute_my_query("update chatbot.conversation_dtl set review_status='$review_status',reviewed_by='$agent_id',review_comment='$summary',reviewed_on=now() where $conv_dtl_wh");
}
/**
 * @param string $lead_code
 * @param string $chat_id
 * @param string $split_id
 * @param string $agent_id
 * @param string $summary
 * @param string $description
 * @param string $complaint_id
 * @param string $complaint_status
 * @param string $assign_to_emp
 * @param string $notification_con
 * @param string $latest_support_id
 * @param int $create_complaint
 * @param string[int] $reference_complaint_id
 * @param string $product_info
 * @param string $show_call_history_chk
 * @param string $dev_cmbstatus
 * @param string $dev_cmbcomplaint
 * @param string $dev_cmb_emp_assign
 * @param int $create_new_ticket
 * 
 * @return int[int]
 */
function create_support_ticket_for_chatwrapup($lead_code,$chat_id,$split_id,$agent_id,$summary,$description,$complaint_id,$complaint_status,$assign_to_emp,$notification_con,$latest_support_id,$create_complaint=0,
    	$reference_complaint_id=null,$product_info='',$show_call_history_chk='',$dev_cmbstatus='0',$dev_cmbcomplaint='',$dev_cmb_emp_assign='',$create_new_ticket=0,$callmark=7){
	$result_chat_dtl  =  /*.(mixed).*/null;
	if($chat_id=="" && $create_complaint==0){
		$result_chat_dtl  =  execute_my_query("select  id, support_group_id, cust_id from chatbot.conversation_dtl where cust_id='$lead_code' AND review_status=1");
		get_chat_review_status_with_split($lead_code,$chat_id,$split_id,$agent_id,$description,$summary);
	}else if($create_complaint==0){
		$result_chat_dtl  =  execute_my_query("select  id, support_group_id, cust_id from chatbot.conversation_dtl where id in($chat_id)");
		get_chat_review_status_with_split($lead_code,$chat_id,$split_id,$agent_id,$summary,$summary);
	}	
	$complaint_desc = $description;
	$complaint_summary = $summary;
	$status 		= $complaint_status;
	$schedule_date 	= date("Y-m-d H:i:s");
	$cmb_emp_assign	= $assign_to_emp;
	$nature			= 12;
	$severity  = 4;
	$priority  = 4;
	if($reference_complaint_id!=null && count($reference_complaint_id)>0){
	    $support_ids_str = implode(",", $reference_complaint_id);
	    $seve_que  =  " select GCD_SEVERITY,GCD_PRIORITY from gft_customer_support_hdr ".
	   	    " join gft_customer_support_dtl on (GCH_LAST_ACTIVITY_ID=gcd_activity_id) ".
	   	    " where GCH_LEAD_CODE=$lead_code and GCH_COMPLAINT_ID in ('$support_ids_str') ";
    	$sev_res   = execute_my_query($seve_que);
    	if($sev_rw=mysqli_fetch_assoc($sev_res)){
    	    $severity = $sev_rw["GCD_SEVERITY"];
    	    $priority = $sev_rw["GCD_PRIORITY"];
    	}
	}
	
	//$callmark		= 7;
	$pcode 			= "10";
	$pskew 			= "01.0";
	$additional_info= "$notification_con";
	$separator		= "";
	$customer_emotion=15;
	$gft_chat_wrapup_dtl_qry="";
	if($latest_support_id>0){
	    if($latest_support_id==1){//If non-asa support, get customer main support group id
	        $latest_support_id = get_single_value_from_single_table("GLH_MAIN_PRODUCT", "gft_lead_hdr", "GLH_LEAD_CODE", "$lead_code");
	    }
		$product_dtl = get_single_value_from_single_table("GVG_PRODUCT", "gft_voicenap_group", "GVG_SUPPORT_GROUP", "$latest_support_id"," AND GVG_STATUS='A' order by GVG_PREFER_ORDER asc");
		if($product_dtl!=""  && $product_dtl!="0"){
			$product_dtl_arr = explode("-",$product_dtl);
			$pcode 			= isset($product_dtl_arr[0])?$product_dtl_arr[0]:"10";
			$pskew 			= isset($product_dtl_arr[1])?$product_dtl_arr[1]:"01.0";
		}
	}
	if($product_info != ''){
		$product_info_arr = explode("-",$product_info);
		$pcode 			= isset($product_info_arr[0])?$product_info_arr[0]:"10";
		$pskew 			= isset($product_info_arr[1])?$product_info_arr[1]:"01.0";
	}
	$send_sms = false;
	$ticket_id = 0;
	$old_support_ticket = '';
	for($i=0;$i<count($reference_complaint_id);$i++){
		$old_support_ticket = isset($reference_complaint_id[$i])?(int)$reference_complaint_id[$i]:0;
		if($create_complaint==1 || $old_support_ticket==0){
			$old_support_ticket = null;
		}
		$ticket_id = insert_support_entry((int)$lead_code, (int)$pcode, "$pskew", '', '', $agent_id, "0", $complaint_summary,
				(string)$complaint_id, "$status",$schedule_date,"",$cmb_emp_assign,"$nature","$severity",$complaint_desc,$send_sms,
				'',"$additional_info",$old_support_ticket,"$priority",true,'',"$callmark",'','',"$customer_emotion");
	}
	$last_act_id	=	get_last_activity_id_of_support($ticket_id);
	$is_unhappy = isset($_REQUEST['is_unhappy'])?$_REQUEST['is_unhappy']:'off';
	$unhappy_reason = isset($_REQUEST['unhappy_reason'])?$_REQUEST['unhappy_reason']:'';
	if($is_unhappy=='on' and $unhappy_reason!='') {
	    update_unhappy_customer($unhappy_reason, $last_act_id);
	}
	while($row_chat_dtl=mysqli_fetch_array($result_chat_dtl)){
		$support_group_id 	= 	$row_chat_dtl['support_group_id'];
		$chat_id			=	$row_chat_dtl['id'];
		$chat_type 			=   ($create_new_ticket==1?"2":"1");
		$gft_chat_wrapup_dtl_qry .= "$separator('$chat_id','2','$last_act_id','$chat_id','".((int)$split_id>0?$split_id:0)."','$chat_type')";
		$separator = ',';
	}
	//Send sms and mail to 
	if((!$send_sms) && $chat_id>0){
		$result_chat_id = execute_my_query("select contact_number,email_id from chatbot.conversation_dtl where id='$chat_id'");
		if((mysqli_num_rows($result_chat_id)>0) && ($row_chat=mysqli_fetch_array($result_chat_id))){
			$mobile_no = trim($row_chat['contact_number']);
			$email_value  = $row_chat['email_id'];
			if((substr($mobile_no, 0,4)=="cid:")){
				$mobile_no=null;
			}
			support_sms($last_act_id,$agent_id,true,$mobile_no,$email_value);
		}	 
	}
	$ticket_id_arr[0] = $ticket_id;
	//create pending dev complaint
	if($dev_cmbstatus!='0'){
		$emp_lead_code = get_single_value_from_single_table("GEM_LEAD_CODE", "gft_emp_master", "GEM_EMP_ID", "$agent_id");
		$uploaded_file_path = get_single_value_from_single_query("GCD_UPLOAD_FILE", "select GCD_UPLOAD_FILE from gft_customer_support_dtl where GCD_COMPLAINT_ID='$ticket_id' order by gcd_activity_id desc limit 1");
		$ticket_id1 = insert_support_entry((int)$emp_lead_code, (int)$pcode, "$pskew", '', '', $agent_id, "0", $complaint_summary,
				(string)$complaint_id, "$dev_cmbstatus",$schedule_date,"",$dev_cmb_emp_assign,"$nature","$severity",$dev_cmbcomplaint,$send_sms,
				'',"$additional_info",$old_support_ticket,"$priority",true,"$uploaded_file_path","$callmark",'','',"$customer_emotion");
		$ticket_id_arr[1] = $ticket_id1;
		$last_dev_act_id	=	get_last_activity_id_of_support($ticket_id1);
		$gft_chat_wrapup_dtl_qry .= "$separator('$chat_id','2','$last_dev_act_id','$chat_id','".((int)$split_id>0?$split_id:0)."','2')";
	}
	if($show_call_history_chk!="") {
		execute_my_query("update gft_techsupport_incomming_call set gtc_activity_id='$last_act_id' where gtc_id in ($show_call_history_chk) ");
	}
	if($gft_chat_wrapup_dtl_qry!=""){
		execute_my_query("REPLACE INTO gft_chat_wrapup_dtl(GCW_CONVERSATION_ID,GCW_ACTIVITY_TYPE,GCW_ACTIVITY_ID,chat_id,GCW_SPLIT_ID,GCW_COMPLAINT_TYPE) VALUES $gft_chat_wrapup_dtl_qry");
	}
	return $ticket_id_arr;
}
/**
 * @param string $notification_con
 * @param string $send_mygofrugal
 * @param string $send_wns
 * @param string $send_sms
 * @param string $lead_code
 * @param string $mobile_no
 * @param string $user_id
 *
 * @return void
 */
function send_push_notification_and_sms_for_chat_wrapup($notification_con,$send_mygofrugal,$send_wns,$send_sms,$lead_code,$mobile_no,$user_id=null){
	if((substr($mobile_no, 0,3))=="cid"){
		$mobile_no="";
	}
	$contact_dtls = get_active_contact_for_customer($mobile_no,$lead_code);
	if($user_id==null){
		$user_id	 = $contact_dtls['mygofrugal_user_id'];
	}	
	$notify_cust_ids = /*.(string).*/$contact_dtls['wns_cust_ids'];
	if($send_mygofrugal==1){
		$user_id_arr = explode(",",$user_id);
		foreach ($user_id_arr as $user_id1) {
			if(($user_id1 > 0) ){
				$notify_content_config['message'] = array($notification_con);
				$user_id1 = (int)$user_id1;
				$access_lead = get_single_value_from_single_table("GCA_ACCESS_LEAD", "gft_customer_access_dtl", "GCA_USER_ID", "$user_id1");
				send_formatted_notification_content($notify_content_config, 6, 59, 2, $access_lead,'0','0',$user_id1);
			}
		}
	}
	if($send_sms==1 && $mobile_no!=""){
		$db_sms_content_config['content'] = array($notification_con);
		$sms_content = get_formatted_content($db_sms_content_config, 130);
		entry_sending_sms_to_customer($mobile_no, $sms_content, 130,0,0,null,0,null,true);
	}
	if( ($send_wns==1) && ($notify_cust_ids!='') ){
		$cust_id_arr = explode(",", $notify_cust_ids);
		$custom_title = get_single_value_from_single_table("GPN_TITLE", "gft_push_notification_template", "GPN_ID", "59");
		foreach ($cust_id_arr as $single_cust){
			notify_pos_product($single_cust, '', 'promotion', $notification_con,'','',false,'','','','',$custom_title);
		}
	}
}
/**
 * @param string $lead_code
 *
 * @return string
 */
function get_customer_input_box($lead_code){
	global $uid;
	$cust_name="";
	if($lead_code!=""){
		$cust_name=get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", "$lead_code");
	}
	$return_string=<<<END
	<input id="cust_name" name="cust_name" type="text" size="30" tabindex="5"
			class="normal_autocomplete" value="$cust_name"
			onblur='setStyleClassName(this,"normal_autocomplete")'
			onfocus='setStyleClassName(this,"focus_autocomplete")' onkeydown="javascript: check_empty_cust();" />
			Corp <input type="checkbox" name="list_corp_cust" id="list_corp_cust" value="3"/>
			<input id="emp_id" name="emp_id" type="hidden" value="$uid">
			<input id="custCode" name="custCode" type="hidden" value="$lead_code">
			<script type="text/javascript">
			new AjaxJspTag.Autocomplete(
			"list_customername.php", {minimumCharacters: "1",
			parameters: "customername={cust_name}&user_id={emp_id}&lead_type={list_corp_cust}",
			progressStyle: "throbbing",target: "custCode",className: "autocomplete",
			emptyFunction: "emptyFunction",	source: "cust_name",
			postFunction: updateLeadStatus});
			function emptyFunction(){
				document.support_history_form.custCode.value!='';
			}
			function check_empty_cust(){
			 
			   	if(document.getElementById("cust_name").value=="")
			   	{
			   		document.getElementById("custCode").value="";
			   	}
			}		
			</script>
END;
	return $return_string;
}
/**
 * @param string $activity_date
 * @param string $chat_desc
 * @param string $activity_group
 *
 * @return string
 */
function get_support_entry_fields($activity_date,$chat_desc,$activity_group=""){
	$red_asterik 		= 	'<font color="red" size="3" >*</font>';
	$severity_list = get_two_dimensinal_array_from_table('gft_severity_master','GSM_CODE','GSM_NAME','GSM_STATUS','A');
	$complaint_severity_combo_list=fix_combobox_with('cmbseverity','cmbseverity',$severity_list,"","1",'-Select-',"Style='width:100px'",false);
	$emotion_query = "select GCM_EMOTION_ID,GCM_EMOTION_NAME FROM gft_customer_emotion_master  where  GCM_EMOTION_STATUS='A' and GCM_FOR_CUSTOMER='Y'";
	$customer_emotion_list = get_two_dimensinal_array_from_query($emotion_query, "GCM_EMOTION_ID", "GCM_EMOTION_NAME");
	$complaint_emotion_combo_list=fix_combobox_with('cmbcustomer_emotion','cmbcustomer_emotion',$customer_emotion_list,"","1",'-Select-',"Style='width:100px'",false);
	$priority_list = get_two_dimensinal_array_from_table('gft_priority_master','GPM_CODE','GPM_NAME','GPM_STATUS','A');
	$complaint_priority_combo_list=fix_combobox_with('cmbpriority','cmbpriority',$priority_list,"","1",'-Select-',"Style='width:100px'",false);
	$nature_list = get_two_dimensinal_array_from_table('gft_complaint_nature_master','GCM_NATURE_ID','GCM_NATURE','GCM_NATURE_STATUS','A');
	$visit_nature_combo	=	 fix_combobox_with('cmbnature','cmbnature',$nature_list,'12',"1",'',"Style='width:120px'",false,"onchange='javascript:document.tele_support_activity_form.support_mins.value=0;'");
	$call_mark_list = get_two_dimensinal_array_from_table('gft_cust_call_master','GCC_ID','GCC_DESC','GCC_STATUS','A');
	$complaint_callmark_combo_list=fix_combobox_with('cmbcallmark','cmbcallmark',$call_mark_list,'0',"1",'-Select-',"Style='width:200px'",false);
	$complaint_list = get_complaint('A');
	$complaint_combo_list=fix_combobox_with('cmbcomplaint','cmbcomplaint',$complaint_list,'',"1",'-Select-',"Style='width:200px'",true,"onchange='javascript:onchange_complaint_code(this.value)'");
	$status_list = get_status('A',false);
	$complaint_status_combo_list=fix_combobox_with('cmbstatus','cmbstatus',$status_list,'',"1",'-Select-',"Style='width:180px'",true,"onchange='javascript:onchange_complaint_status(this.value);'",1,array('T41'));
	$product_selection="";
	if($activity_group=="5"){
		$product_selection=<<<END
	<div class="form-group">
					    	<div class='col-sm-6 text-right'><b>$red_asterik Product:</b></div>
							<div class='col-sm-6 text-left'><input id="product_name" tabindex="1" name="product_name" value=""
			type="text" style="width:160px" class="normal_autocomplete" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
			<input type="hidden" name="product_code" id="product_code" value = "">
			<input type="hidden" name="pageType" id="pageType" value = "">
			<input type="hidden" name="product_skew" id="product_skew" value=""></div>
	</div>
END;
	}

	$return_string=<<<END
	<div class="form-group">
					    	<div class='col-sm-6 text-left text-info'><b style="font-size:14px;">Support Entry:</b></div>
							<div class='col-sm-6 text-left'>&nbsp;</div>
					  	</div>
						<div class="form-group">
					    	<div class='col-sm-6 text-right'><b> Date & Time:</b></div>
							<div class='col-sm-6 text-left'>
								<INPUT id="visit_date" name="visit_date" tabindex="1" class="formStyleTextarea" size="18" value="$activity_date" onchange="javascript:date_check_with_current_date('visit_date');"readonly>&nbsp;
								<img alt="" src="images/date_time.gif" align="absmiddle" class="imagecur" id="onceDateIconvd" width="16" height="16" border="0">
							</div>
					  	</div>$product_selection
						<div class="form-group">
					    	<div class='col-sm-6 text-right'><b>$red_asterik Severity:</b></div>
							<div class='col-sm-6 text-left'>$complaint_severity_combo_list</div>
					  	</div>
						<div class="form-group">
					    	<div class='col-sm-6 text-right'><b>$red_asterik Emotion:</b></div>
							<div class='col-sm-6 text-left'>$complaint_emotion_combo_list</div>
					  	</div>
						<div class="form-group">
					    	<div class='col-sm-6 text-right'><b> Priority:</b></div>
							<div class='col-sm-6 text-left'>$complaint_priority_combo_list</div>
					  	</div>
						<div class="form-group">
					    	<div class='col-sm-6 text-right'><b>$red_asterik Customer Call Type:</b></div>
							<div class='col-sm-6 text-left'>$complaint_callmark_combo_list</div>
					  	</div>
						<div class="form-group">
					    	<div class='col-sm-6 text-right'><b>$red_asterik Visit Nature:</b></div>
							<div class='col-sm-6 text-left'>$visit_nature_combo</div>
					  	</div>
						<div class="form-group">
					    	<div class='col-sm-6 text-right'><b>$red_asterik Complaint:</b></div>
							<div class='col-sm-6 text-left'>$complaint_combo_list</div>
					  	</div>
						<div class="form-group">
					    	<div class='col-sm-6 text-right'><b>$red_asterik Summary:</b></div>
							<div class='col-sm-6 text-left'><textarea name="complaint_summary" id="complaint_summary" rows="2" cols="30">$chat_desc</textarea></div>
					  	</div>
						<div class="form-group">
					    	<div class='col-sm-6 text-right'><b>$red_asterik Description:</b></div>
							<div class='col-sm-6 text-left'><textarea name="complaint_desc" id="complaint_desc" rows="4"  cols="25"></textarea></div>
					  	</div>
						<div class="form-group">
					    	<div class='col-sm-6 text-right'><b>Additional Info:</b></div>
							<div class='col-sm-6 text-left'><textarea name="additional_info" id="additional_info" rows="5"  cols="25"></textarea></div>
					  	</div>
					  	<div class="form-group">
					    	<div class='col-sm-6 text-right'><b>$red_asterik My feeling to avoid this engagement</b></div>
							<div class='col-sm-6 text-left'><textarea name="my_emotion" id="my_emotion" rows="2"  cols="25"></textarea></div>
					  	</div>
						<div class="form-group">
					    	<div class='col-sm-6 text-right'><b>$red_asterik Status:</b></div>
							<div class='col-sm-6 text-left'>$complaint_status_combo_list</div>
					  	</div>
END;
	return $return_string;
}
/**
 * @param string $activity_date
 * @param string $chat_desc
 *
 * @return string
 */
function get_followup_entry_fields($activity_date,$chat_desc){
	global $uid;
	$red_asterik 		= 	'<font color="red" size="3" >*</font>';
	$feedback_master_list=get_two_dimensinal_array_from_table('gft_customer_feedback_master','gcm_feedback_code','gcm_feedback_desc','gcm_feedback_status','A');
	$my_comments_list=get_two_dimensinal_array_from_table('gft_my_comments_master','GMC_CODE','GMC_NAME','GMC_STATUS','A');
	$fd_cmb=fix_combobox_with("cmb_feedback_master","cmb_feedback_master",$feedback_master_list,"","1",'-Select-',
			null,false,"onchange='javascript:showFeedbackEntry();'");
	$comments_cmb=fix_combobox_with("cmb_comments_master","cmb_comments_master",$my_comments_list,"","1",'-Select-',
			null,false);
	$auth_gr_edit_lead_status=get_group_under_privilage('6');
	$privilage_to_edit_lead_status='N';
	if(is_authorized_group_list($uid,$auth_gr_edit_lead_status)){
		$privilage_to_edit_lead_status='Y';
	}
	$lead_status_design=customer_lead_status('false',$privilage_to_edit_lead_status);
	$return_string=<<<END
	<script type="text/javascript" src="js/js_leadsub_type.js"></script>
	<div class="form-group">
					    <div class='col-sm-6 text-left text-info'><b style="font-size:14px;">Followup Entry:</b></div>
							<div class='col-sm-6 text-left'>&nbsp;</div>
					  	</div>	
						<div class="form-group">
							<div class='col-sm-12 text-left'>
								<table width="100%">
									<tr><td width="50%">&nbsp;</td><td width="50%">&nbsp;</td></tr>
									<tr>
										<td class="datalabel">$red_asterik Date & Time</td>
										<td><INPUT id="visit_date" name="visit_date" tabindex="1" class="formStyleTextarea" size="18" value="$activity_date" onchange="javascript:date_check_with_current_date('visit_date');"readonly>&nbsp;
										<img alt="" src="images/date_time.gif" align="absmiddle" class="imagecur" id="onceDateIconvd" width="16" height="16" border="0"></td>
									</tr>
									<tr><td width="50%">&nbsp;</td><td width="50%">&nbsp;</td></tr>
									<tr>
										<td class="datalabel">$red_asterik Customer Feedback</td>
										<td>$fd_cmb</td>
									</tr>
									<tr><td width="50%">&nbsp;</td><td width="50%">&nbsp;</td></tr>
									<tr>
										<td class="datalabel">$red_asterik My Comments</td>
										<td>$comments_cmb</td>
									</tr>
									<tr><td width="50%">&nbsp;</td><td width="50%">&nbsp;</td></tr>
									<tr>
										<td class="datalabel">$red_asterik My Comments (In Detail)</td>
										<td><textarea name="complaint_desc" id="complaint_desc" rows="4"  cols="25">$chat_desc</textarea></td>
									</tr>
									<tr><td width="50%">&nbsp;</td><td width="50%">&nbsp;</td></tr>
									$lead_status_design
								</table>
							</div>
					  	</div>
END;
	return $return_string;
}
/**
 * @param string $chatId
 *
 * @return string[int]
 */
function chat_conversation_text($chatId){
	global $uid;
	$return_string="";	
	$question_msg	=	"";
	$return_arr	=	/*. (string[int]) .*/array();
	$result	=	execute_my_query("select GZC_TRANSCRIPT from gft_zoho_chat_hdr where GZC_WRAPUP_OWNER='$uid' AND GZC_CHAT_ID='$chatId'");
	if(mysqli_num_rows($result)==0){
		$return_arr[0]="Chat Transcript not available";
		return $return_arr;
	}
	$return_string=<<<END
	<style>
		.arrow_box { position: relative;  border: 4px solid #fff; min-height:20px;padding:4px;background: #ffb74d;} .arrow_box:after, .arrow_box:before { left: 100%; bottom: 0%; border: solid transparent; content: " "; height: 0; width: 0; position: absolute; pointer-events: none; } .arrow_box:after { border-color: rgba(136, 183, 213, 0); border-left-color: #ffb74d; /*border-width: 10px;*/ margin-top: -10px; } .arrow_box:before { border-color: rgba(194, 225, 245, 0); border-left-color: #fff; border-width: 5px; margin-top: -16px; }
		.arrow_box1 { position: relative;  border: 4px solid #fff; min-height:20px;padding:4px;background: #9bd5c2;} .arrow_box1:after, .arrow_box1:before { right: 100%; bottom: 0%; border: solid transparent; content: " "; height: 0; width: 0; position: absolute; pointer-events: none; } .arrow_box1:after { border-color: rgba(136, 183, 213, 0); border-right-color: #9bd5c2; /*border-width: 10px;*/ margin-top: -10px; } .arrow_box1:before { border-color: rgba(194, 225, 245, 0); border-right-color: #fff; border-width: 5px; margin-top: -16px; }
		.chat_block{height:auto; overflow: hidden;position: relative; right:15% padding-top:5px;font-size:13px;}
		.chat_block_left{float:left;height: auto; width:18%;display: inline-block;}
		.chat_block_right{float:left;background:blue;height: auto;max-width:60%;}
		.chat_block_right1{float:right;background:blue;height: auto;max-width:60%;}
		.isa_info {color: #00529B;background-color: #BDE5F8;margin: 10px 0px;padding:12px;}
		.isa_info i{font-size:2em;vertical-align:middle;}
	</style>
END;
	$row	=	mysqli_fetch_array($result);
	$chat_transcript_json	=	trim($row['GZC_TRANSCRIPT']);
	if($chat_transcript_json==''){
		$return_arr[0]="No Chat History";
		return $return_arr;
	}
	$chat_transcript= json_decode($chat_transcript_json,true);
	if(isset($chat_transcript['error'])){
		$return_arr[0]=$chat_transcript['error']['message'];
		return $return_arr;
	}
	if($chat_transcript==null){
		$return_arr[0]="No Chat History";
		return $return_arr;
	}
	foreach ($chat_transcript as $key=>$value){
		foreach($value as $key1=>$value1){
			$msg		=	"";
			$message	=	"";
			$name		=	"";
			$arrow_box	=	"arrow_box";
			$style		=	"style='float:right;'";
			$timestamb	=	0;
			$deleted_user_name="";
			$chat_block_right="chat_block_right1";
			if(isset($value1['chatinitiated_time']) and $value1['chatinitiated_time']!=''){
				$msg		=	$value1['question'];
				$question_msg=$msg;
				//$name		=	$value1['visitor_name'];
				$name		=	"Customer:";
				$timestamb	=	$value1['chatinitiated_time'];
				$arrow_box	=	"arrow_box1";
				$chat_block_right="chat_block_right";
				$style		=	"";
			}else{
				$msg		=	"";
				$message	=	isset($value1['msg'])?/*. (string[string]) .*/$value1['msg']:"";
				//$name		=	isset($value1['dname'])?$value1['dname']:"";
				$name		=	"Agent:";
				$timestamb	=	isset($value1['time'])?$value1['time']:"";
				$arrow_box	=	"arrow_box";
				if((!isset($value1['sender']) or (isset($value1['sender']) and $value1['sender']=='null')) and !is_array($message)){
					$arrow_box	=	"arrow_box1";
					$chat_block_right="chat_block_right";
					$style		=	"";
					$name		=	"Customer:";
				}
				if(!is_array($message)){
					$msg		=	$message;
				}
				$mode		=	isset($value1['mode'])?$value1['mode']:"";
				if(is_array($message) and $mode=='att'){
					$image_name	=	isset($message['fName'])?$message['fName']:"";
					$image_url	=	isset($message['url'])?$message['url']:"";
					$image_size	=	isset($message['size'])?$message['size']:"";
					$fileId		=	isset($message['fileId'])?$message['fileId']:"";
					$content	=	isset($message['content'])?$message['content']:"";
					$zohoChat	=	new ZohoChat((int)get_samee_const("SAM_API_MODE"));
					$attachment_url	=	$zohoChat->getAttachementLink($chatId,$image_url);
					if(substr($content, 0,5)=='image' && $attachment_url!=''){
						$msg		=	"<img src='$attachment_url' width='200px' height='150px'/><br>".
								"<a href='$attachment_url' download='proposed_file_name' >Download</a>";
					}else if($attachment_url!=''){
						$msg		=	"File Name: $image_name<br>".
								"<a href='$attachment_url' download='proposed_file_name'>Download</a>";
					}else{
						$msg		=	"Attachment link not available";
					}
				}else if(is_array($message)){
					$msg_mode		=	isset($message['mode'])?$message['mode']:"";
					$msg_transfer	=	isset($message['msg'])?$message['msg']:"";
					$dname			=	"";
					$dname_joint_user=	"";
					if($msg_mode=='TRANSFER'){
						$transfername	=	"";
						$type			=	isset($message['type'])?$message['type']:"";
						$transferdetails=	isset($message['transferdetails'])?$message['transferdetails']:array();
						$opruser		=	isset($message['opruser'])?$message['opruser']:array();
						if(count($transferdetails)>0 and isset($transferdetails['transfername']) and $transferdetails['transfername']!=''){
							$transfername	=	$transferdetails['transfername'];
						}
						if(count($opruser)>0 and isset($opruser['dname']) and $opruser['dname']!=''){
							$dname	=	$opruser['dname'];
						}
						if($type=='DEPARTMENT' or $type=='USER'){
							$msg	=	$msg_transfer." to $transfername ".($type=='DEPARTMENT'?"Department":"")." by $dname";
							$deleted_user_name	=	"$dname";
						}
					}else if($msg_mode=='USER DELETED'){
						$msg	=	$msg_transfer." User $deleted_user_name deleted ";
					}else if($msg_mode=='ACCEPT_TRANSFER'){
						$opruser		=	isset($message['opruser'])?$message['opruser']:array();
						if(count($opruser)>0 and isset($opruser['dname']) and $opruser['dname']!=''){
							$dname	=	$opruser['dname'];
						}
						$msg	=	$msg_transfer." by  $dname";
					}else if($msg_mode=='ADDSUPPORTREP'){
						$userlist	=	isset($message['userlist'])?$message['userlist']:array();
						$opruser	=	isset($message['opruser'])?$message['opruser']:array();
						if(count($opruser)>0 and isset($opruser['dname']) and $opruser['dname']!=''){
							$dname	=	$opruser['dname'];
						}
						if(count($userlist)>0 and isset($userlist['dname']) and $userlist['dname']!=''){
							$dname_joint_user	=	$userlist['dname'];
						}
						$msg	=	"$dname has invited $dname_joint_user to this chat. ".$msg_transfer;
					}
				}
			}
			$timestamb	=	round($timestamb/1000);
			$timestamb1=	date("d-m-Y H:i:s", $timestamb);
			$return_string.=<<<END
			<div class='chat_block'>
				<div class='chat_block_left' $style>
					$name<br><span style='color:#7b828f; font-size:10px;'>$timestamb1</span></div>
				<div class='$chat_block_right'>

					<div class='$arrow_box' >$msg</div>
				</div>
			</div>
END;
		}
	}
	$return_arr[0]	=	$return_string;
	$return_arr[1]	=	$question_msg;
	return $return_arr;
}
/**
 * @param string $chatId
 * @param string $lead_code
 * @param string $emailId
 * 
 * @return string[int]
 */
function get_customer_chat_list($chatId,$lead_code,$emailId){
	global $uid;
	$list_of_chats="Chat Details not available.";
	$chat_query="";
	$question="";
	if($lead_code!="" && $lead_code!="0" && $chatId!="" && $chatId!="0"){
		$chat_rows = execute_my_query("select GZC_CHAT_ID from gft_zoho_chat_hdr WHERE GZC_WRAPUP_OWNER='$uid' AND  GZC_LEAD_CODE='$lead_code' and GZC_WRAPUP_STATUS !=1 order by GZC_CHAT_ID desc");
		while($chat_row=mysqli_fetch_array($chat_rows)){
			$chat_id=$chat_row['GZC_CHAT_ID'];
			$chat_text_arr=chat_conversation_text($chat_id);
			$chat_text		=	isset($chat_text_arr[0])?$chat_text_arr[0]:"";
			$chat_question	=	isset($chat_text_arr[1])?$chat_text_arr[1]:"";
			$checked="";
			$collapse="collapse";
			if($chatId==$chat_id){
				$checked="checked='checked'";
				$collapse="";
				$question	=	$chat_question;
			}
			$list_of_chats.=<<<END
		<h4 class="panel-title" style="border-bottom:#dcb900 1px solid;">
			<input type="checkbox" name="wrapup_chats[]" value="$chat_id" $checked>
			<a data-toggle="collapse" href="#collapse$chat_id">Chat - $chat_id </a>
			<div id="collapse$chat_id" class="panel-collapse $collapse">
				<img src='images/new_refresh.png' width='20' height='20' align='right' chat_id="$chat_id" class="update_transcript"/>
				$chat_text
 			</div>
		</h4>
END;
		}
	}else if($chatId!="" && $chatId!="0"){
		$chat_text_arr=chat_conversation_text($chatId);
		$chat_text		=	isset($chat_text_arr[0])?$chat_text_arr[0]:"";
		$question		=	isset($chat_text_arr[1])?$chat_text_arr[1]:"";
		$list_of_chats=<<<END
		<h4 class="panel-title" style="border-bottom:#dcb900 1px solid;">
			<input type="checkbox" name="wrapup_chats[]" value="$chatId" checked='checked'>
			<a data-toggle="collapse" href="#collapse$chatId">Chat - $chatId </a>
			<div id="collapse$chatId" class="panel-collapse">
			<img src='images/new_refresh.png' width='20' height='20' align='right' chat_id="$chatId"  class="update_transcript"/>
				$chat_text
 			</div>
		</h4>
END;
	}else if($lead_code!="" && $lead_code!="0"){
		$chat_query = "select GZC_CHAT_ID from gft_zoho_chat_hdr WHERE GZC_WRAPUP_OWNER='$uid' AND  GZC_LEAD_CODE='$lead_code' and GZC_WRAPUP_STATUS !=1 order by GZC_CHAT_ID desc";		
	}else if($emailId!="" && $emailId!="0"){
		$chat_query = "select GZC_CHAT_ID from gft_zoho_chat_hdr WHERE GZC_WRAPUP_OWNER='$uid' AND  GZC_VISITER_EMAIL='$emailId' and GZC_WRAPUP_STATUS !=1 order by GZC_CHAT_ID desc";		
	}else{
		$chat_query = "select GZC_CHAT_ID from gft_zoho_chat_hdr WHERE GZC_WRAPUP_OWNER='$uid' AND  GZC_WRAPUP_STATUS !=1 order by GZC_CHAT_ID desc limit 1";		
	}
	if($chat_query!=""){
		$chat_rows = execute_my_query($chat_query);
		while($chat_row=mysqli_fetch_array($chat_rows)){
			$chat_id=$chat_row['GZC_CHAT_ID'];
			$chat_text_arr=chat_conversation_text($chat_id);
			$chat_text		=	isset($chat_text_arr[0])?$chat_text_arr[0]:"";
			$chat_question	=	isset($chat_text_arr[1])?$chat_text_arr[1]:"";
			$checked="";
			$collapse="collapse";
			if($chatId==""){$chatId=$chat_id;}
			if($chatId==$chat_id){
				$checked="checked='checked'";
				$collapse="";
				$question	=	$chat_question;
			}
			$list_of_chats.=<<<END
		<h4 class="panel-title" style="border-bottom:#dcb900 1px solid;">
			<input type="checkbox" name="wrapup_chats[]" value="$chat_id" $checked>
			<a data-toggle="collapse" href="#collapse$chat_id">Chat - $chat_id </a>
			<div id="collapse$chat_id" class="panel-collapse $collapse">
				<img src='images/new_refresh.png' width='20' height='20' align='right' chat_id="$chat_id" class="update_transcript"/>
				$chat_text
 			</div>
		</h4>
END;
		}
	}
	$return_string=<<<END
	<div class="form-group top-buffer">
		<div class='col-sm-6 text-left text-info'><b style="font-size:14px;">&nbsp;Chats:</b></div>
		<div class='col-sm-6 text-left'>&nbsp;</div>
  	</div>
	<div class="form-group center top-buffer" id="chat_container">
		<div class='col-sm-10 center' style="margin:0% 8%;">
			  <div class="panel-group">
				    <div class="panel panel-default">
				      <div class="panel-heading">
				        $list_of_chats
					</div>
				</div>
			</div>
		</div>
	</div>
END;
	$return_arr[0]=$return_string;
	$return_arr[1]=$question;
	return $return_arr;
}
/**
 * @param string $chatId
 *
 * @return string
 */
function get_customer_single_chat($chatId){
	$return_string="Chat Details not available.";
	if($chatId!="" && $chatId!="0"){
		$chat_text_arr=chat_conversation_text($chatId);
		$chat_text		=	isset($chat_text_arr[0])?$chat_text_arr[0]:"";
		$return_string=<<<END
			<img src='images/new_refresh.png' width='20' height='20' align='right' chat_id="$chatId"  class="update_transcript"/>
				$chat_text
END;
	}	
	return $return_string;
}
/**
 *
 * @return string
 */
function get_next_action_design(){
	global $uid;
	$next_activtiy_list	=	get_activity_list_with_group(null,true);
	$employee_list		=	get_salesperson_name($uid,true);
	$return_string=<<<END
<fieldset><legend align="center" >
<font color="red" size="2" align="center"> Next Action</font></legend>
<table width="100%" cellpadding="0" cellspacing="1" border="0" id="next_visit_dtl">
<tbody><tr><td width="50%" valign="top" class="datalabel"><font class="mandatory_marker_red" size="3" >*</font> Next Action </td><TD>
END;
	$return_string.= fix_combobox_with('na','na',$next_activtiy_list,"","1",'Select',null,true);
	$return_string.=<<<END
</td></tr>
<tr><td  width="141" align="right" class="datalabel" >
<font class="mandatory_marker_red" size="3" >*</font>Next Action Date </td>
<td nowrap><input name="donv" type="text" class="formStyleTextarea"  id="donv" Readonly='true'
 ondblclick="javascript:this.value='';" onchange="javascript:date_check_should_be_greater('donv');">&nbsp;
<a href="javascript:makeClick(this);" tabindex="1"  id="onceDateIcon1">
<img alt="" src="images/date_time.gif" width="16" border="0" align="middle"></a></td></tr>
<tr id="next_visit_type">
<td valign="top"  class="datalabel"><font class="mandatory_marker_red" size="3" >*</font>Visit Type</td>
<td><select id="nxt_visited_type" name="nxt_visited_type" onchange="show_nxt_joint_visit_dtl();" tabindex="1" class="formStyleTextarea">
<option value='0'>Individual </option>
<option value="1">Joint</option></select></td></tr>
<tr><td></td><td>
<div id="nxt_joint_visit_dtl" class="hide">
<table class="solid_border"><tr><td width="70%">
<table  id="nxt_joint_visit"><tbody>
<!--<tr class="moduleListTitle"><td class="head_black_10"> No. </td><td class="head_black_10" align="center" > Name </td>-->
<tr><td> 1.</td><td>
END;
	$return_string.= fix_combobox_with('nxt_joint_emp0','nxt_joint_emp[0]',$employee_list,'0',"1",'Select');
	$return_string.=<<<END
</td></tr></tbody></table></td></tr>
<tr><td width="30%" style="valign:center">
<table><tr><td><input tabindex="1" name="button" onclick="addRow_nxtvisit();" value="Add " type="button" class="button" ></td>
<td><input tabindex="1"  value="Remove" onclick="removeRowFromTable_nxtvisit();" type="button" class="button" ></td></tr></table>
</td></tr></table></div></td></tr>
<tr><td width="141" valign="top"   class="datalabel" > 	Detail </td>
<td><textarea id="noa"  name="noa" rows="3" cols="30"   tabindex="1"  MAXLENGTH="500" onkeyup="return ismaxlength(this)"
class="formStyleTextarea"  onkeyup="return ismaxlength(this)"></textarea></td></tr>
</TABLE></fieldset>
<script type="text/javascript">
	init_date_func("donv","%Y-%m-%d %H:%M:%S","onceDateIcon1","Bl");
</script>
END;
	return $return_string;
}
/**
 *
 * @return string
 */
function get_support_assign_task(){
	global $uid;
	$red_asterik 		= 	'<font color="red" size="3" >*</font>';
	$baton_label = LIVE_BATON_LABEL;
	$supp_employee_list = 	get_support_exec();
	$reason_list 		= 	get_activity_list(null,false,false,false,null,"0",true);
	$complaint_assign_emp_combo_list=fix_combobox_with('cmb_emp_assign','cmb_emp_assign',$supp_employee_list,$uid,"1",'-Select-',"Style='width:160px'",false);
	$complaint_assign_reason_combo_list=fix_combobox_with('cmb_reason_assign','cmb_reason_assign',$reason_list,'',"1",'-Select-',"Style='width:160px'",false);
	$return_string=<<<END
	<TABLE class="solid_border" align="center">
	<tr>
		<TD  class="datalabel"  width="130" nowrap>$red_asterik Assign To</TD>
		<TD> $complaint_assign_emp_combo_list </TD>
	</tr>
	<tr id='tr_baton' class='hide'>
		<TD align='right'><input type='hidden' name='enable_baton' id='enable_baton' value='0'>
			<input type='checkbox' name='baton_wobbling' id='baton_wobbling'></TD>
		<TD class="datalabel" style='text-align:left;' width="130" nowrap>$baton_label
	</tr>
	<tr>
		<TD  class="datalabel"  width="130" nowrap> Assign Reason </TD>
		<TD >$complaint_assign_reason_combo_list</TD>
	</tr>
	<tr>
		<TD  class="datalabel" width="130" nowrap>$red_asterik Schedule Date</TD>
		<TD width="260" nowrap><INPUT name="schedule_date"   VALUE="" class="formStyleTextarea" id="schedule_date"  size="20"  onchange="javascript:date_check_should_be_greater('schedule_date');" readonly>
		<img alt="Date Picker"  src="images/date_time.gif" class="imagecur" id="onceDateIconsd" width="16" height="16" style='margin-bottom:-5px'></TD>
	</tr>
</TABLE>
<script type="text/javascript">
	init_date_func("schedule_date","%Y-%m-%d %H:%M:%S","onceDateIconsd","Bl");
</script>
END;
	return $return_string;
}
/**
 * @param string $chatId
 * @param string $lead_code
 * @param string $email_id
 * 
 * @return string[int]
 */
function get_chat_activity_type($chatId,$lead_code,$email_id){
	global $uid;
	$return_array	= array();
	$return_array[0]=1;
	$sql_query="";
	if($chatId!="" && $chatId!="0"){
		$sql_query	=	" select GCG_ACTIVITY_TYPE,GZC_INITIATED_DATE_TIME,GCG_ID from gft_zoho_chat_hdr ". 
						" inner join gft_chat_group_master on(GZC_AGENT_GROUP_ID=GCG_ID) ".
						" where GZC_CHAT_ID='$chatId'";
	}else if($lead_code!="" && $lead_code!="0"){
		$sql_query = 	" select GCG_ACTIVITY_TYPE,GZC_INITIATED_DATE_TIME,GCG_ID from gft_zoho_chat_hdr ".
						" inner join gft_chat_group_master on(GZC_AGENT_GROUP_ID=GCG_ID) ".
						" WHERE GZC_WRAPUP_OWNER='$uid' AND  GZC_LEAD_CODE='$lead_code' and GZC_WRAPUP_STATUS!=1 order by GZC_CHAT_ID desc  limit 1";
	}else if($email_id!="" && $email_id!="0"){
		$sql_query = 	" select GCG_ACTIVITY_TYPE,GZC_INITIATED_DATE_TIME,GCG_ID from gft_zoho_chat_hdr ".
						" inner join gft_chat_group_master on(GZC_AGENT_GROUP_ID=GCG_ID) ".
						" where GZC_VISITER_EMAIL='$email_id' order by GZC_CHAT_ID desc  limit 1";
	}	
	if($sql_query!=""){
		$result_row	=	execute_my_query($sql_query);
		if(mysqli_num_rows($result_row)>0 && $row=mysqli_fetch_array($result_row)){
			$return_array[0]=$row['GCG_ACTIVITY_TYPE'];
			$return_array[1]=$row['GZC_INITIATED_DATE_TIME'];
			$return_array[2]=$row['GCG_ID'];
		}	
	}	
	return $return_array;
	
}
/**
 * @param string $chat_id
 * @param string $lead_code
 * 
 * @return string
 */
function get_chat_product_group_info($chat_id,$lead_code){
	$return_val="";
	$department_id	=	"";
	$rows	=	execute_my_query("select  GCG_ID, GCG_PRODUCT from gft_zoho_chat_hdr 
								inner join gft_chat_group_master on(GZC_AGENT_GROUP_ID=GCG_ID)
								where GZC_CHAT_ID='$chat_id';");
	if(mysqli_num_rows($rows)>0 && $row=mysqli_fetch_array($rows)){
		$return_val=$row['GCG_PRODUCT'];
		$department_id=$row['GCG_ID'];
	}
	if($department_id=='8' && $lead_code!=""){
		$product_dtl=	get_customer_primary_chat_group($lead_code,false);
		$return_val	=	isset($product_dtl['product_group'])?$product_dtl['product_group']:"";
	}
	if($return_val==""){
		$return_val="10-01.0";
	}
	return $return_val;
}
/**
 * @param string $employee_id
 * @param string $open_time
 * @param string $close_time
 * 
 * @return boolean
 */
function check_chat_wrapup_status($employee_id,$open_time,$close_time){
	//$rows	=	execute_my_query("select GZC_CHAT_ID from gft_zoho_chat_hdr WHERE GZC_WRAPUP_OWNER='$employee_id' AND  GZC_WRAPUP_STATUS !=1 ");
	$rows	=	execute_my_query(" select cc.id from chatbot.conversation_dtl  cc ".
	                " left join chatbot.split_chat_dtl scd on (scd.chat_id=cc.id and scd.split_status!='3') ".
					" where (1) and cc.created_date>='$open_time' AND cc.created_date<='$close_time' ".
					" and chat_status=3 AND ((scd.split_id is null and cc.agent_user_id='$employee_id') or ".
	                " (scd.split_id is not null and scd.support_agent_id='$employee_id' and scd.review_status=1)) ".
	                " and cc.review_status='1' ");
	if(mysqli_num_rows($rows)>0){
		return true;
	}
	return false;
}
/**
 * @param string $agent_group_id
 * 
 * @return int
 */
function get_chat_group_id($agent_group_id){
	$group_id=0;
	$result	=	execute_my_query("select GCG_ID from gft_chat_group_master where GCG_ZOHO_DEPT_CODE='$agent_group_id'");
	if(mysqli_num_rows($result)>0 && $row=mysqli_fetch_array($result)){
		$group_id	=	(int)$row['GCG_ID'];
	}
	return $group_id;
}
/**
 * @param string $message
 * @param string $error_code
 *
 * @return void
 */
function send_Error_With_Code($message,$error_code) {
	$error = /*. (string[string]) .*/ array();
	header('X-PHP-Response-Code: '.$error_code, true, $error_code);
	$error['message']=$message;
	echo json_encode($error);exit;

}
/**
 *
 * @return void
 */
function check_authentication_for_chat_emotion_tool(){
	$headers 	= apache_request_headers();
	$key 		= isset($headers['Auth-Key'])?$headers['Auth-Key']:'';
	if($key!="SaMcHaT"){
		send_Error_With_Code("Unauthorized request. ","401");
	}
}
/**
 * @param int $topic_id
 * @param int $kb_type
 *
 * @return string
 */
function getKbId($topic_id,$kb_type){
	$query = "select * from gft_chatbot_topic_master where topic_id='$topic_id' and status=1";
	$result=execute_my_query($query);
	if($row=mysqli_fetch_array($result)){
		$kb_id=$row["kb_id"];
		if($kb_type==2){//Secondary One
			$kb_id=$row["secondary_kp_id"];
		}else if($kb_type==4){// Secondary Two
			$kb_id=$row["secondary_kp_1"];
		}else if($kb_type==3){
			$kb_id=$row["staging_kp_id"];
		}
	}

	if ($kb_id == ''){
		echo "Invalid kb id ...";exit;
	}

	return $kb_id;

}
/**
 * @param int $kb_type
 *
 * @return string
 */
function getQnaSubscriptionId($kb_type){
	//return "7878d4be7b5847cc9363364878cbaf73";
	if($kb_type==1){//Primary
		return "d62e1a37860f4d8a86bd767fa5303be6";
	}else if($kb_type==2){//Secondary One		
		return "fd53861c08a04a63819b322c984a5a0a";
	}else if($kb_type==4){//Secondary Two
		return "f3f8ae9a5c0346bc864fd7c480376105";
	}else if($kb_type==3){//Staging
		return "93ea8fead8a64d198eaaf15a29325de6";
	}else{
		die("Invalid KB type");
	}
}
/**
 * @param string $all_leads
 *
 * @return string[string]
 */
function get_preferred_support_group($all_leads){
	$support_group_id = '17';
	$cust_id = $all_leads;
	$query1=" select GLH_LEAD_CODE,GLH_CUST_NAME,GLH_MAIN_PRODUCT from gft_lead_hdr ".
			" INNER JOIN gft_support_product_group ON(GLH_MAIN_PRODUCT=GSP_GROUP_ID)".
			" where GLH_LEAD_CODE IN($all_leads) ORDER BY GSP_PREFERRED_ORDER DESC LIMIT 1";
	$result1=execute_my_query($query1);
	if((mysqli_num_rows($result1)>0) && $row=mysqli_fetch_array($result1)){
		$support_group_id = $row['GLH_MAIN_PRODUCT'];
		$cust_id = $row['GLH_LEAD_CODE'];
	}
	$return_arr['cust_id'] = $cust_id;
	$return_arr['support_group_id'] =$support_group_id;
	return $return_arr;
}
/**
 * @param string $onlineSalesGroup
 * 
 * @return string[int][string]
 */
function get_online_sales_executive($onlineSalesGroup){
	$agentDetails = array();
	$rows_lmt_owner = 	execute_my_query(" select GLI_EMP_ID,GEM_EMP_NAME from gft_lmt_incharge_master ".
			" INNER JOIN gft_emp_master em ON(GLI_EMP_ID=GEM_EMP_ID) ".
			" where ".($onlineSalesGroup=='706'?"":" GLI_PRESALES_SUPPORT_GROUP='$onlineSalesGroup' AND ")." GLI_ACTIVE_STATE='A' GROUP BY GLI_EMP_ID");
	while ($row_lmt_owner=mysqli_fetch_array($rows_lmt_owner)){
		$agent_list[] = $row_lmt_owner['GLI_EMP_ID'];
		$agent_name_list[] = $row_lmt_owner['GEM_EMP_NAME'];
	}
	$agentDetails[0] = $agent_list;
	$agentDetails[1] = $agent_name_list;
	return $agentDetails;
}
?>
