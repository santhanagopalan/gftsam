<?php 

require_once( __DIR__ . "/../dbcon.php");
require_once( __DIR__ . "/../access_util.php");
require_once( __DIR__ . "/../lic_util.php");
require_once( __DIR__ . "/../function.insert_stmt.php");
require_once( __DIR__ . "/../function.update_in_tables.php");
require_once( __DIR__ . "/http_error.php");
require_once( __DIR__ . "/../push_notification/push_notification_util.php");
require_once( __DIR__ . "/../audit_util.php");
require_once( __DIR__ . "/../product_delivery_util.php");
require_once( __DIR__ . "/../ZohoPeople.php");
/**
 * @param int $uid
 * 
 * @return boolean
 */
function check_employee_is_reporting_of_partner($uid){
	$total_count=0;
	$sql_query= " select count(CGI_LEAD_CODE) as total_count from gft_cp_info ".
				" inner join gft_emp_master em on(CGI_EMP_ID=em.gem_emp_id AND gem_status='A')".
				" where (1) ";
	if(!is_authorized_group_list("$uid", array(34),null)){
		$sql_query .= "  AND cgi_incharge_emp_id=$uid";
	}
	$result = execute_my_query($sql_query);
	if($row=mysqli_fetch_array($result)){
		$total_count=(int)$row['total_count'];
	}
	if($total_count>0){
		return true;
	}else{
		return false;
	}
}
/**
 *@param string $tab
 *@param boolean $lead_escalation
 *
 * @return mixed[]
 */
function escalation_metalist($tab="",$lead_escalation=false){
	$definitions_arr=	array();
	/*Start Escalatioin Block */
	$escalation_group	=	array();
	$escalation_group['id']	=	"escalation_group";
	$escalation_group['type']	=	"master";
	$escalation_group['lookup']	=	"escalation.values";
	$escalation_group['key']	=	"group_name";
	$escalation_group['label']	=	"Select Group";
	$escalation_group['state']	=	"on";
	$escalation_group['tag']	=	"Escalation";
	$escalation_group['validators']	=	array("group::all_or_none");
	$escalation_group['tab']	=	$tab;
	if($lead_escalation){
		$escalation_group['lead_escalation']	=	true;
	}
	$definitions_arr[]			=	$escalation_group;


	$escalation_assign_to= 	array();
	$escalation_assign_to['id']=	"escalation_assign_to";
	$escalation_assign_to['type']=	"master";
	$escalation_assign_to['lookup']=	"escalation.list";
	$escalation_assign_to['filter_key']=	"group_id";
	$escalation_assign_to['parent']=	"escalation_group";
	$escalation_assign_to['label']=	"Assign to Employee";
	$escalation_assign_to['state']=	"on";
	$escalation_assign_to['validators']=	array("group::all_or_none");
	$escalation_assign_to['transitions']	=	array(array("type"=> "state","state"=> "on","onValue"=> array("*"),"target"=> array("escalation_baton_wobbling")));
	$escalation_assign_to['tag']=	"Escalation";
	$escalation_assign_to['tab']	=	$tab;
	if($lead_escalation){
		$escalation_assign_to['lead_escalation']	=	true;
	}
	$definitions_arr[]			=	$escalation_assign_to;

	$baton_arr			=	array();
	$baton_arr['id']	=	"escalation_baton_wobbling";
	$baton_arr['type']	=	"boolean";
	$baton_arr['label']	=	LIVE_BATON_LABEL;
	$baton_arr['state']	=	"off";
	$baton_arr['value']	=	"0";
	$baton_arr['tag']	=	"Escalation";
	$baton_arr['tab']	=	$tab;
	if($lead_escalation){
		$baton_arr['lead_escalation']	=	true;
	}
	$definitions_arr[]	=	$baton_arr;

	$escalation_edc_date	=	array();
	$escalation_edc_date['id']	=	"escalation_edc_date";
	$escalation_edc_date['type']	=	"date";
	$escalation_edc_date['label']	=	"EDC";
	$escalation_edc_date['state']	=	"on";
	$escalation_edc_date['validators']=	array("group::all_or_none");
	$escalation_edc_date['tag']	=	"Escalation";
	$escalation_edc_date['tab']	=	$tab;
	if($lead_escalation){
		$escalation_edc_date['lead_escalation']	=	true;
	}
	$definitions_arr[]			=	$escalation_edc_date;

	$escalation_desc	=	array();
	$escalation_desc['id']	=	"escalation_desc";
	$escalation_desc['type']	=	"text";
	$escalation_desc['label']	=	"Description";
	$escalation_desc['state']	=	"on";
	$escalation_desc['validators']	=	array("group::all_or_none");
	$escalation_desc['tag']	=	"Escalation";
	$escalation_desc['tab']	=	$tab;
	if($lead_escalation){
		$escalation_desc['lead_escalation']	=	true;
	}
	$definitions_arr[]			=	$escalation_desc;

	$expected_resolution	=	array();
	$expected_resolution['id']=	"expected_resolution";
	$expected_resolution['type']=	"text";
	$expected_resolution['label']=	"Expected Resolution";
	$expected_resolution['state']=	"on";
	$expected_resolution['validators']=	array("group::all_or_none");
	$expected_resolution['tag']=	"Escalation";
	$expected_resolution['tab']	=	$tab;
	if($lead_escalation){
		$expected_resolution['lead_escalation']	=	true;
	}
	$definitions_arr[]			=	$expected_resolution;

	/*End Escalatioin Block */
	return $definitions_arr;
}
/**
 *@param string $tab
 *
 * @return mixed[]
 */
function followup_metalist($tab=""){
	global $is_partner,$is_partner_emp;
	$definitions_arr=	array();
	/*Start Followup Block */

	$followup_date	=	array();
	$followup_date['id']	=	"followup_date";
	$followup_date['type']	=	"datetime_calendar";
	$followup_date['label']	=	"Followup Date";
	$followup_date['state']	=	"on";
	$followup_date['validators']	=	array("group::all_or_none");
	$followup_date['tag']	=	"Followup";
	$followup_date['tab']	=	$tab;
	$definitions_arr[]			=	$followup_date;

	$followup_action	=	array();
	$followup_action['id']	=	"followup_action";
	$followup_action['type']	=	"master";
	$followup_action['lookup']	=	"assignToEmployee.values";
	$followup_action['key']		=	"action_name";
	$followup_action['label']	=	"Followup Action";
	$followup_action['state']	=	"on";
	$followup_action['validators']	=	array("group::all_or_none","any_one_required_tag");
	$followup_action['tag']	=	"Followup";
	$followup_action['tab']	=	$tab;
	$definitions_arr[]			=	$followup_action;

	$assign_to_employee	=	array();
	$assign_to_employee['id']	=	"assign_to_employee";
	$assign_to_employee['type']	=	"searchable-combo";
	$assign_to_employee['lookup_from']	=	"local";
	$assign_to_employee['lookup']	=	"assign_to_employee_list";
	$assign_to_employee['label']	=	"Assign to Employee";
	$assign_to_employee['state']	=	"on";
	$assign_to_employee['validators']	=	array("group::all_or_none");
	$assign_to_employee['tag']	=	"Followup";
	$assign_to_employee['tab']	=	$tab;
	if($tab!=""){
		$assign_to_employee['lookup_from']	=	"master";
		$assign_to_employee['lookup']	=	"assignToEmployee.list";
		$assign_to_employee['load_on_search']	=	true;
	}
	$assign_to_employee['transitions']	=	array(array("type"=> "state","state"=> "on","onValue"=> array("*"),"target"=> array("assign_incharge","baton_wobbling","assign_responsible")));
	$definitions_arr[]			=	$assign_to_employee;
	
	if(!$is_partner_emp) {
		$assign_incharge	=	array();
		$assign_incharge['id']	=	"assign_incharge";
		$assign_incharge['type']	=	"local";
		$assign_incharge['lookup']	=	"assign_list";
		$assign_incharge['label']	=	"Assign as Lead In-charge";
		$assign_incharge['options']	=	"radio";
		//$assign_incharge['single']	=	true;
		$assign_incharge['state']	=	"off";
		$assign_incharge['tag']	=	"Followup";
		$assign_incharge['skip_group_validation']	=	false; // true
		$assign_incharge['validators']	=	array("group::all_or_none"); // none
		$assign_incharge['tab']	=	$tab;
		$assign_incharge['tip_mapper'] = array("1"=>"Assigning as lead incharge along with a followup activity","0"=>"Assigning a followup activity");
		$assign_incharge['modifications'] = array(array("type"=>"state","state"=>"off","onValue"=>array("1"),"target"=>array("assign_responsible")));
		$definitions_arr[]			=	$assign_incharge;
	}
	
	if($is_partner || $is_partner_emp) {
		$assign_responsible = array();
		$assign_responsible['id'] = "assign_responsible";
		$assign_responsible['type'] = "local";
		$assign_responsible['lookup'] = "assign_list";
		$assign_responsible['label']	=	"Assign as Responsible to Customer";
		$assign_responsible['options'] = "radio";
		$assign_responsible['state'] = "off";
		$assign_responsible['tag'] = "Followup";
		$assign_responsible['validators'] = array("group::all_or_none");
		$assign_responsible['tab'] = $tab;
		$assign_responsible['modifications'] = array(array("type"=>"state","state"=>"off","onValue"=>array("1"),"target"=>array("assign_incharge")));
		$definitions_arr[] = $assign_responsible;
	}
	
	$baton_arr['id']	=	"baton_wobbling";
	$baton_arr['type']	=	"boolean";
	$baton_arr['label']	=	LIVE_BATON_LABEL;
	$baton_arr['state']	=	"off";
	$baton_arr['value']	=	"0";
	$baton_arr['tag']	=	"Followup";
	$baton_arr['tab']	=	$tab;
	$definitions_arr[]	=	$baton_arr;

	/*End Followup Block */
	return $definitions_arr;
}
/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param boolean $default_flag
 *
 * @return string[string][string]
 */
function get_order_history_dtl($lead_code, $page_size=0, $offset=0,$default_flag=false){
	$total_element	=	array();
	$limit_qry      =    "";
	$return_array   =    /*. (string[string][string]) .*/array();
	$element_array  =    array();
	$group_title    =    'Order History';
	if($page_size!=0){
		$limit_qry  =    " LIMIT $offset, $page_size";
	}else{
		$limit_qry  =    " LIMIT 1";
	}
	$label_array   	= array("Order No","Order Date","Collection Incharge","Ordered By","Order Amount","Collected Amt",
						"Realized Amt","Balance Amt","Order Status","Order approval status");

	$datatype_array    =    array("text","date","text","text","currency","currency","currency","currency","text","text");

	$sql_order  =" select GOD_ORDER_NO,GOD_ORDER_DATE,ci.GEM_EMP_NAME,ob.GEM_EMP_NAME, GOD_ORDER_AMT, GOD_COLLECTED_AMT, ".
			" GOD_COLLECTION_REALIZED, GOD_BALANCE_AMT, if(GOD_ORDER_STATUS='A','Active','Cancel') as stat ,GOS_STATUS_NAME ".
			" from gft_order_hdr  ".
			" join gft_emp_master ci on (GOD_INCHARGE_EMP_ID=ci.gem_emp_id) ".
			" join gft_emp_master ob on (GOD_EMP_ID=ob.gem_emp_id) ".
			" left join gft_order_approval_status_master on (GOS_ID=GOD_ORDER_APPROVAL_STATUS) ".
			" where GOD_LEAD_CODE='$lead_code' order by GOD_ORDER_DATE desc ";

	$res_tot_count    =    execute_my_query($sql_order);
	$total_count    =    mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= "$total_count";
	$sql_order        =    $sql_order.$limit_qry;
	$res_elements    =    execute_my_query($sql_order);
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			$element_array['label'] =	 $label_array[$i];
			$element_array['value'] =    $row[$i];
			$element_array['type']  =    $datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value'])<=0){
					$element_array['value']=null;
				}
			}
			$total_element[]        =    $element_array;
			if($i==0){
				$group_title    =    "Order History - ".(string)$row[$i];
			}
		}
		$block_element['offset']		=	++$offset;
		$block_element['group_title']	=	$group_title;
		$block_element['elements']		=	$total_element;
		unset($total_element);
		$return_array['elements'][]    =    $block_element;
	}
	return $return_array;
}

/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param boolean $default_flag
 *
 * @return string[string][string]
 */
function get_sms_history($lead_code, $page_size=0, $offset=0,$default_flag=false){
	$limit_qry      =    "";
	$total_element	=	array();
	$return_array   =    /*. (string[string][string]) .*/array();
	$element_array  =    array();
	$group_title    =    'SMS History';
	if($page_size!=0){
		$limit_qry  =    " LIMIT $offset, $page_size";
	}else{
		$limit_qry  =    " LIMIT 1";
	}
	$label_array   	= array("Created Date","Sent Date","Recipient Moblie No","Message","Category","Status", "Sent By");

	$datatype_array    =    array("datetime","datetime","text","text","text","text","text");

	$sql_que  =" select  gos_msg_sent_time,gos_status_updated_time, gos_receiver_mobileno, gos_sms_content, GSC_DESC, GSMS_DESC, GEM_EMP_NAME ".
			" from gft_sending_sms  ".
			" join gft_sms_status_master on (GSMS_ID=gos_sms_status) ".
			" join gft_sms_config on (GSC_ID=gos_category) ".
			" left join gft_emp_master on (gos_sender_id=gem_emp_id) ".
			" where gos_customer_leadcode='$lead_code' order by gos_msg_sent_time desc ";

	$res_tot_count    =    execute_my_query($sql_que);
	$total_count    =    (int)mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= $total_count;
	$sql_que        =    $sql_que.$limit_qry;
	$res_elements    =    execute_my_query($sql_que);
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			$element_array['label'] =	 $label_array[$i];
			$element_array['value'] =    $row[$i];
			$element_array['type']  =    $datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value'])<=0){
					$element_array['value']=null;
				}
			}
			$total_element[]        =    $element_array;
			if($i==0){
				$group_title    =    "SMS - ".(string)$row[$i];
			}
		}
		$block_element['offset']		=	++$offset;
		$block_element['group_title']	=	$group_title;
		$block_element['elements']		=	$total_element;
		unset($total_element);
		$return_array['elements'][]    =    $block_element;
	}
	return $return_array;
}
/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 *
 * @return string[string][string]
 */
function get_customer_chat_dtl($lead_code, $page_size=0, $offset=0){
$limit_qry      =    "";
        $total_element  =       array();
        $return_array   =    /*. (string[string][string]) .*/array();
        $element_array  =    array();
        $group_title    =    'Chat History';
        if($page_size!=0){  
                $limit_qry  =    " LIMIT $offset, $page_size";
        }else{
                $limit_qry  =    " LIMIT 5";
        }
        $sql_que  =     " select GZC_INITIATED_DATE_TIME,GEM_EMP_NAME,GZC_TRANSCRIPT,GZC_CHAT_ID,GLH_CUST_NAME,GFA_ANSWER, '1' AS CHAT_TYPE from gft_zoho_chat_hdr ch".
                                " inner join gft_emp_master em on(ch.GZC_WRAPUP_OWNER=em.gem_emp_id) ".
                                " inner join gft_lead_hdr lh on(lh.glh_lead_code=GZC_LEAD_CODE) ".
                                " left join gft_cust_feedback_answer on(GZC_CHAT_ID=GFA_CHAT_ID AND GFA_QUE_ID=4) ".
                                " where GZC_LEAD_CODE in($lead_code) and GZC_TRANSCRIPT!='' GROUP BY GZC_CHAT_ID";
        $sql_que1 =     " select  created_date GZC_INITIATED_DATE_TIME, if(isnull(GEM_EMP_NAME),'Chitti',GEM_EMP_NAME) GEM_EMP_NAME, ".
                                " '' GZC_TRANSCRIPT, cd.id GZC_CHAT_ID,GLH_CUST_NAME, 0 as GFA_ANSWER, '2' AS CHAT_TYPE  ".
                                " from chatbot.conversation_dtl cd ".
                                        " LEFT JOIN chatbot.conversation_analysis_dtl ca  ON(cd.id=ca.ref_conv_id) ".
                                        " inner join gft_lead_hdr lh on(lh.glh_lead_code=cd.cust_id)  ".
                                        " left join gft_emp_master em on(ca.routed_to_emp=em.gem_emp_id) ".
                                        " where cust_id in($lead_code)  group by cd.id  ";
        $sql_que = "select *from ($sql_que union all $sql_que1) tt order by GZC_INITIATED_DATE_TIME desc";
        $res_tot_count    =    execute_my_query($sql_que);
        $total_count    =    (int)mysqli_num_rows($res_tot_count);
        $return_array['total_count']    = $total_count;
        $sql_que        =    $sql_que.$limit_qry;
        $res_elements    =    execute_my_query($sql_que);
        $domain_name=$_SERVER['HTTP_HOST'];
        $domain_name_url = ($domain_name=="sam.gofrugal.com"?"gstbot":"labtest");
        while(mysqli_num_rows($res_elements)>0 && $row=mysqli_fetch_array($res_elements)){
        $transcript_exist=true;
        if($row['CHAT_TYPE']=="2"){
        	$transcript_exist=false;
        	$chat_id = $row['GZC_CHAT_ID'];
        	$chat_result = execute_my_query("select message,  time_stamp,type,event,file_name,media_path,media_type from chatbot.transcripts ct".
        				" LEFT JOIN chatbot.chat_attachment ca ON(ct.id=transcript_id)".	
        				" where chat_id='$chat_id' order by time_stamp asc");
        	$new_transcripts = array();
        	$count = 1;        	
        	while($row_trans=mysqli_fetch_array($chat_result)){
        		$transcript_exist=true;
        		$message = "msg";
        		$response_array = array();
        		$response_array['type']	=$row_trans['type'];
        		if($count==1){
        			$message="question";
        			$response_array['visitor_name']	="GoFrugal Chitti";
        		}        		
        		$response_array["$message"]	=	mb_convert_encoding($row_trans['message'],'UTF-8','UTF-8');
        		$media_path = $row_trans['media_path'];
        		if((int)$row_trans['media_type']==1){//For play audio
        			$response_array['type']	="audio";
        			$response_array['src']	="https://$domain_name_url.gofrugal.com/s3tool/file/download?mediapath=$media_path";        			
        		}
        		if((int)$row_trans['media_type']==2){//For image attachment
        			$response_array['type']	="image";
        			$response_array['src']	="https://$domain_name_url.gofrugal.com/s3tool/file/download?mediapath=$media_path";        			
        		}
        		$response_array['chatinitiated_time']	=(strtotime($row_trans['time_stamp'])*1000);	
        		$response_array['time']	=(strtotime($row_trans['time_stamp'])*1000);
        		$chat_transcript= json_decode($row_trans['event'],true);
        		$emp_name = "";
        		$buttons = array();
        		$buttons_str = "";
        		$delimiter = "";
        		$total_button = 0;
        		if(isset($chat_transcript['attachments']) && $attachment=$chat_transcript['attachments']){
        			if(isset($attachment[0]['content']) && $button=$attachment[0]['content']){
        				if(isset($button['buttons']) && $all_buttons=$button['buttons']){
        					foreach ($all_buttons as $key_button=>$value_button){
        						$total_button++;
        						$buttons[]=$value_button['value'];
        						$buttons_str .= $delimiter."$total_button. ".(string)$value_button['value'];
        						$delimiter = "\n";
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
        		if($buttons_str!=""){
        			$response_array["$message"] .= "\n$buttons_str";
        		}
        		$response_array['buttons']	=$buttons;
        		$response_array['agent_name']	=$emp_name;
        		$response_array['dname']	=$emp_name;
        		if($row_trans['type']=="R"){
        			$response_array['sender']	="12123121";  
        			$response_array['dname']	="Customer";
        		}else{
        			$response_array['dname']	=($emp_name!=''?$emp_name:'Chitti');
        		}
        		$new_transcripts[] = $response_array;
        		unset($response_array);
        		$count++;
        	}
        	$full_trans['data']=$new_transcripts;
        	$row['GZC_TRANSCRIPT']=json_encode($full_trans);
        	$row['GFA_ANSWER'] = (int)get_single_value_from_single_table("rating", "chatbot.customer_feedback", "chat_id", "$chat_id","ORDER BY id DESC");
        }
        if($transcript_exist){
        	$block_element['offset']                =       ++$offset;
        	$block_element['initiated_date']=       $row['GZC_INITIATED_DATE_TIME'];
        	$block_element['cust_name']             =       $row['GLH_CUST_NAME'];
        	$block_element['responded_by']  =       $row['GEM_EMP_NAME'];
        	$block_element['chat_id']               =       $row['GZC_CHAT_ID'];
        	$block_element['rating']                =      $row['GFA_ANSWER'];
        	$block_element['chat_transcript']=      json_decode($row['GZC_TRANSCRIPT']);
        	$total_element[]                =       $block_element;
        	unset($block_element);
        }        
        }
        $return_array['elements']    =    $total_element;
        return $return_array;        
}
/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 *
 * @return string[string][string]
 */
function get_customer_photo_dtl($lead_code, $page_size=0, $offset=0){
	$limit_qry      =    "";
	$total_element	=	array();
	$return_array   =    /*. (string[string][string]) .*/array();
	$element_array  =    array();
	if($page_size!=0){
		$limit_qry  =    " LIMIT $offset, $page_size";
	}else{
		$limit_qry  =    " LIMIT 1";
	}
	$sql_que  = " select GCP_LEAD_CODE,GCP_THUMBNAIL_PATH,GCP_IMAGE_PATH from gft_customer_photo ".
				" where GCP_LEAD_CODE='$lead_code' AND GCP_IMAGE_TYPE=1 order by GCP_ID desc ";
	$res_tot_count    =    execute_my_query($sql_que);
	$total_count    =    (int)mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= $total_count;
	$sql_que        =    $sql_que.$limit_qry;
	$res_elements    =    execute_my_query($sql_que);
	while($row=mysqli_fetch_array($res_elements)){
		$element_array['thumbnail'] =	 "http://".$_SERVER['SERVER_NAME'].'/'.$row['GCP_THUMBNAIL_PATH'];
		$element_array['image'] 	=	 "http://".$_SERVER['SERVER_NAME'].'/'.$row['GCP_IMAGE_PATH'];
		$total_element[]        =    $element_array;
	}
	$return_array['elements']    =    $total_element;
	return $return_array;
}

/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param boolean $default_flag
 *
 * @return string[string][string]
 */
function get_quotation_history($lead_code, $page_size=0, $offset=0,$default_flag=false){
	$limit_qry      =   "";
	$total_element	=	array();
	$return_array   =   /*. (string[string][string]) .*/array();
	$element_array  =   array();
	$group_title    =   'Quotation';
	if($page_size!=0){
		$limit_qry  =    " LIMIT $offset, $page_size";
	}else{
		$limit_qry  =    " LIMIT 1";
	}
	$label_array   	= array("Created by","Created on","Number","Products","Order value","Status");

	$datatype_array = array("text","date","text","text","currency","text");

	$sql_que  =" select GEM_EMP_NAME, GQH_CREATED_DATE, GQH_ORDER_NO, group_concat(concat(pfm.GPM_PRODUCT_ABR,' ',pm.GPM_SKEW_DESC,'-[',GQP_QTY,']')) as pdtl ,GQH_ORDER_AMT, GQH_ORDER_STATUS ".
			" from gft_quotation_hdr  ".
			" join gft_quotation_product_dtl on (GQH_ORDER_NO=GQP_ORDER_NO) " .
			" join gft_product_master pm on(GQP_PRODUCT_CODE=pm.gpm_product_code and GQP_PRODUCT_SKEW=pm.gpm_product_skew) " .
			" join gft_product_family_master pfm on(pfm.gpm_product_code=pm.gpm_product_code) " .
			" join gft_emp_master on (GEM_EMP_ID=GQH_EMP_ID) ".
			" where GQH_LEAD_CODE='$lead_code' group by GQH_ORDER_NO having pdtl is not null order by GQH_CREATED_DATE desc ";
	$res_tot_count    =    execute_my_query($sql_que);
	$total_count    =    (int)mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= $total_count;
	$sql_que        =    $sql_que.$limit_qry;
	$res_elements    =    execute_my_query($sql_que);
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			$element_array['label'] =	 $label_array[$i];
			$element_array['value'] =    $row[$i];
			$element_array['type']  =    $datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value'])<=0){
					$element_array['value']=null;
				}
			}
			$total_element[]        =    $element_array;
			if($i==2){
				$group_title    =    "Quotation - ".(string)$row[$i];
			}
		}
		$block_element['offset']		=	++$offset;
		$block_element['group_title']	=	$group_title;
		$block_element['elements']		=	$total_element;
		unset($total_element);
		$return_array['elements'][]    =    $block_element;
	}
	return $return_array;
}

/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param boolean $default_flag
 *
 * @return string[string][string]
 */
function get_proforma_history($lead_code, $page_size=0, $offset=0,$default_flag=false){
	$limit_qry      =   "";
	$total_element	=	array();
	$return_array   =  /*. (string[string][string]) .*/ array();
	$element_array  =   array();
	$group_title    =   'Proforma';
	if($page_size!=0){
		$limit_qry  =    " LIMIT $offset, $page_size";
	}else{
		$limit_qry  =    " LIMIT 1";
	}
	$label_array   	= array("Created by","Created on","Number","Products","Order value","Status","Validity");

	$datatype_array = array("text","date","text","text","currency","text","date");

	$sql_que  =" select GEM_EMP_NAME, GPH_CREATED_DATE, GPH_ORDER_NO, group_concat(concat(pfm.GPM_PRODUCT_ABR,' ',pm.GPM_SKEW_DESC,'-[',GPP_QTY,']')) as pdtl ,".
			" GPH_ORDER_AMT, GPH_ORDER_STATUS,GPH_VALIDITY_DATE ".
			" from gft_proforma_hdr  ".
			" join gft_proforma_product_dtl on (GPH_ORDER_NO=GPP_ORDER_NO) " .
			" join gft_product_master pm on(GPP_PRODUCT_CODE=pm.gpm_product_code and GPP_PRODUCT_SKEW=pm.gpm_product_skew) " .
			" join gft_product_family_master pfm on(pfm.gpm_product_code=pm.gpm_product_code) " .
			" join gft_emp_master on (GEM_EMP_ID=GPH_EMP_ID) ".
			" where GPH_LEAD_CODE='$lead_code' group by GPH_ORDER_NO having pdtl is not null order by GPH_CREATED_DATE desc ";
	$res_tot_count    =    execute_my_query($sql_que);
	$total_count    =    (int)mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= $total_count;
	$sql_que        =    $sql_que.$limit_qry;
	$res_elements    =    execute_my_query($sql_que);
	while($row=mysqli_fetch_row($res_elements)){
		
		for($i=0;$i<count($row);$i++){
			$element_array['label'] =	 $label_array[$i];
			$element_array['value'] =    $row[$i];
			$element_array['type']  =    $datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value'])<=0){
					$element_array['value']=null;
				}
			}
			$total_element[]        =    $element_array;
			if($i==2){
				$group_title    =    "Proforma - ".(string)$row[$i];
			}
		}
		$block_element['offset']		=	++$offset;
		$block_element['group_title']	=	$group_title;
		$block_element['elements']		=	$total_element;
		unset($total_element);
		$return_array['elements'][]    =    $block_element;
	}
	return $return_array;
}

/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param boolean $default_flag
 *
 * @return string[string][string]
 */
function get_receipt_dtl($lead_code, $page_size=0, $offset=0,$default_flag=false){
	$limit_qry      =   "";
	$total_element	=	array();
	$return_array   =   /*. (string[string][string]) .*/array();
	$element_array  =   array();
	$group_title    =   'Receipt';
	if($page_size!=0){
		$limit_qry  =    " LIMIT $offset, $page_size";
	}else{
		$limit_qry  =    " LIMIT 1";
	}
	$label_array   	= array("Receipt Id","Receipt Date","Receipt No","Collected By","Receipt Type","Receipt Amount","Cheque/ DD/ UTR No",
			"Cheque/ DD/ Transfer Date","Bank","Deposited Bank","Deposited Date","Cleared Date","Status","Checked");

	$datatype_array = array("text","date","text","text","text","currency","text","date","text","text","date","date","text","text");

	$sql_que  =" select GRD_RECEIPT_ID, GRD_DATE, GRD_RECEIPT_ID_REF, GEM_EMP_NAME, GRT_TYPE_NAME, GRD_RECEIPT_AMT, ".
			" GRD_CHEQUE_DD_NO, GRD_CHEQUE_DD_DATE, GRD_BANK_NAME, GRD_DEPOSITED_BANK, GRD_DEPOSITED_DATE, GRD_CHEQUE_CLEARED_DATE ".
			" GCS_STATUS, GRD_CHECKED_WITH_LEDGER ".
			" from gft_receipt_dtl  ".
			" join gft_emp_master on (GEM_EMP_ID=GRD_EMP_ID) ".
			" join gft_receipt_type_master on (GRT_TYPE_CODE=GRD_RECEIPT_TYPE) ".
			" join gft_cheque_status_master on (GCS_STATUS_ABR=GRD_STATUS) ".
			" where GRD_LEAD_CODE='$lead_code' order by GRD_DATE desc ";
	$res_tot_count    =    execute_my_query($sql_que);
	$total_count    =    (int)mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= $total_count;
	$sql_que        =    $sql_que.$limit_qry;
	$res_elements    =    execute_my_query($sql_que);
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			$element_array['label'] =	 $label_array[$i];
			$element_array['value'] =    $row[$i];
			$element_array['type']  =    $datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value']) <= 0){
					$element_array['value']=null;
				}
			}
			$total_element[]        =    $element_array;
			if($i==2){
				$group_title    =    "Receipt - ".(string)$row[$i];
			}
		}
		$block_element['offset']		=	++$offset;
		$block_element['group_title']	=	$group_title;
		$block_element['elements']		=	$total_element;
		unset($total_element);
		$return_array['elements'][]    =    $block_element;
	}
	return $return_array;
}

/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param boolean $default_flag
 * 
 * @return string[string][string]
 */
function get_order_product_dtl($lead_code, $page_size=0, $offset=0,$default_flag=false){
	$limit_qry      =   "";
	$total_element	=	array();
	$return_array   =   /*. (string[string][string]) .*/array();
	$element_array  =   array();
	$group_title    =   'Order Product Details';
	if($page_size!=0){
		$limit_qry  =    " LIMIT $offset, $page_size";
	}else{
		$limit_qry  =    " LIMIT 1";
	}
	$label_array   	= array("Order No","Order date","Product Name & Skew","Ordered Qty","Used Qty","License Status","List Price",
				"Sell Rate","Tax Rate","Service Tax Rate","Total Amount");

	$datatype_array = array("text","date","text","text","text","text","currency","currency","text","text","currency");

	$sql_que  =" select concat(GOP_ORDER_NO,'-',SUBSTR(concat('0000',GOP_FULLFILLMENT_NO),-4)) as orderfull, GOD_ORDER_DATE, ".
			" concat(pfm.GPM_PRODUCT_ABR,' ',pm.GPM_SKEW_DESC) as pdtl,GOP_QTY,GOP_USEDQTY,GLS_STATUS_NAME,GOP_LIST_PRICE,GOP_SELL_RATE, ".
			" GOP_TAX_RATE, GOP_SERVICE_TAX_RATE, GOP_SELL_AMT ".
			" from gft_order_hdr  ".
			" join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) " .
			" join gft_product_master pm on(GOP_PRODUCT_CODE=pm.gpm_product_code and GOP_PRODUCT_SKEW=pm.gpm_product_skew) " .
			" join gft_product_family_master pfm on(pfm.gpm_product_code=pm.gpm_product_code) " .
			" left join gft_lic_status_master on (GLS_ID=GOP_LICENSE_STATUS) ".
			" where GOD_LEAD_CODE='$lead_code' order by GOD_CREATED_DATE desc ";
	$res_tot_count    =    execute_my_query($sql_que);
	$total_count    =    (int)mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= $total_count;
	$sql_que        =    $sql_que.$limit_qry;
	$res_elements    =    execute_my_query($sql_que);
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			$element_array['label'] =	 $label_array[$i];
			$element_array['value'] =    $row[$i];
			$element_array['type']  =    $datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value'])<=0){
					$element_array['value']=null;
				}
			}
			$total_element[]        =    $element_array;
			if($i==0){
				$group_title    =    "Order Product Details - ".(string)$row[$i];
			}
		}
		$block_element['offset']		=	++$offset;
		$block_element['group_title']	=	$group_title;
		$block_element['elements']		=	$total_element;
		unset($total_element);
		$return_array['elements'][]    =    $block_element;
	}
	return $return_array;
}

/**
 * @param string $message
 *
 * @return void
 */
function sendError($message) {
	$error = /*. (string[string]) .*/ array();
	$error['status']="error";
	$error['message']=$message;
	echo json_encode($error);
}

/**
 * @return void
 */
function sendMailError() {
	$mail_stat = /*. (string[string]) .*/ array();
	$mail_stat['status']="unsent";
	$mail_stat['message']="sending mail failed";
	$mail_out['mail_status']=$mail_stat;
	echo json_encode($mail_out);
}


/**
 * @param string $assignee_type
 * @param string $userId
 * @param string $custId
 *
 * @return string
 */
function get_assignee($assignee_type,$userId,$custId) {
	switch($assignee_type) {
		case 'support':
			$prod_res = execute_my_query("select GLH_MAIN_PRODUCT from gft_lead_hdr where GLH_LEAD_CODE = '$custId'");
			if(mysqli_num_rows($prod_res)!=0) {
				$row = mysqli_fetch_array($prod_res);
				$prod_code = $row['GLH_MAIN_PRODUCT'];
			}else{
				sendError("Customer code doesnot exists");
				exit;
			}
			$data =execute_my_query("select GSP_GROUP_MANAGER from gft_support_product_group where GSP_GROUP_ID='$prod_code'");
			if($source = mysqli_fetch_array($data)){
				$assignee = $source['GSP_GROUP_MANAGER'];
				return $assignee;
			}else {
				sendError("Unknown");//if group manager not found
				exit;
			}
			//break;
				
		case 'annuity':
			$data =execute_my_query("select GSP_GROUP_MANAGER from gft_support_product_group where GSP_GROUP_ID=1"); // 1 for annuity
			if($source = mysqli_fetch_array($data)){
				$assignee = $source['GSP_GROUP_MANAGER'];
				return $assignee;
			}else {
				sendError("Unknown");//if group manager not found
				exit;
			}
			//break;
				
		case 'boss':
			$data=execute_my_query("select GER_REPORTING_EMPID from gft_emp_reporting where GER_STATUS='A' and GER_EMP_ID = '$userId'");
			if($source = mysqli_fetch_array($data)){
				$assignee = $source['GER_REPORTING_EMPID'];
				return $assignee;
			}else {
				sendError("Unknown");//if reporting manager not found
				exit;
			}
			//break;
				
		default:
			sendError('Assigneetype not valid. Cannot process');
			exit;
			//break;
	}
}



/**
 * @param int $ACT_ID
 * @param string $cc_id
 * @param string $context
 * @param string $msg
 * 
 * @return void
 */
function sending_mail($ACT_ID,$cc_id,$context,$msg) {

	$email_from='';$email_to='';$cc_email='';$attach='';$support_id='';
	$query1	="select GCD_COMPLAINT_ID,GCD_EMPLOYEE_ID,GEM_EMAIL from gft_customer_support_dtl
	join gft_emp_master on (GCD_EMPLOYEE_ID=GEM_EMP_ID) where gcd_activity_id='$ACT_ID'";

	$query2 ="select GCD_PROCESS_EMP,GEM_EMAIL from gft_customer_support_dtl
	join gft_emp_master on (GCD_PROCESS_EMP=GEM_EMP_ID) where gcd_activity_id='$ACT_ID'";

	
	$cc_arr = explode(',',$cc_id);
	$cc_email_arr = /*. (string[int]) .*/array();
	for($i=0;$i<count($cc_arr);$i++) {
		$query3 = "select GEM_EMP_ID,GEM_EMAIL from gft_emp_master where GEM_EMP_ID='$cc_arr[$i]'";
		$res3 = execute_my_query($query3);
		if( (mysqli_num_rows($res3)==1) && ($row3 = mysqli_fetch_array($res3)) ) {
			$cc_email_arr[$i] = $row3['GEM_EMAIL'];
		}
	}
	

	$res1 = execute_my_query($query1);
	if( (mysqli_num_rows($res1)==1) && ($row1 = mysqli_fetch_array($res1)) ) {
		$support_id = $row1['GCD_COMPLAINT_ID'];
		$email_from = $row1['GEM_EMAIL'];
	}

	$res2 = execute_my_query($query2);
	if( (mysqli_num_rows($res2)==1) && ($row2 = mysqli_fetch_array($res2)) ) {
		$email_to = $row2['GEM_EMAIL'];
	}

	$cc_email = implode(',', $cc_email_arr);
	
	$msg=str_replace("\\n", "\n", $msg);
	$msg="Support ID : $support_id \n".$msg;
	$msg.="\n\n This is an automated Email initiated through vSmile.";
	
	$status = send_mail_function($email_from,$email_to,$context,$msg,$attach,$cc_email,$content_type=true);
	if(!$status) {
		sendMailError();
	}
}


/**
 * @param int $userId
 * @param string $zcode
 * @param string $udid
 * @param string $receive_param
 * @param string $return_message
 * @param int $response_type
 * @param int $is_detail_view
 *
 * @return void
 */
function save_access($userId=0,$zcode='',$udid='',$receive_param='', $return_message='',$response_type=0,$is_detail_view=0) {
	$access_page = $_SERVER['SCRIPT_NAME'];
	global $key_emp_id,$log_operation;
	if($userId==0){
		$userId	=	/* .(int). */$key_emp_id;
	}
	$log_operation	=	/* .(int). */$log_operation;
	$index = strrpos($access_page,'/');
	$page = substr($access_page,$index+1);
	$query_url = $_SERVER['QUERY_STRING'];
	if($receive_param!=''){
		$query_url=$receive_param;
	}
	$dec_zcode = base64_decode($zcode);
	$id = strpos($dec_zcode, ',');
	$lat = substr($dec_zcode, 0,$id-1);
	$lon = substr($dec_zcode, $id+1);
	$time_now = date('Y-m-d H:i:s');
	$micro_sec_diff = getDeltaTime();
	$return_message	=	mysqli_real_escape_string_wrapper($return_message);
	$query_url		=	mysqli_real_escape_string_wrapper($query_url);
	$que = "insert into gft_ismile_access (gia_emp_id,gia_access_page,gia_arguments,GIA_RETURN_DATA,gia_access_time,GIA_MICRO_SEC_DIFF,gia_latitude,gia_longitude,GIA_UDID,GIA_OPERATION_ID,GIA_RESPONSE_TYPE,GIA_IS_DETAIL_VIEW)
	values ('$userId','$page','$query_url','$return_message','$time_now','$micro_sec_diff','$lat','$lon','$udid','$log_operation',$response_type,$is_detail_view)";
	$res = execute_my_query($que);
}

/**
 * @param int $userId
 * @param int $operation_id
 * @param string $request_data
 * @param string $response_data
 *
 * @return void
 */
function save_mygofrugal_access($userId, $operation_id, $request_data, $response_data){
	$script_name = $_SERVER['SCRIPT_NAME'];
	$index 		= strrpos($script_name,'/');
	$access_page= substr($script_name,$index+1);
	$insert_arr['GAL_USER_ID'] 			= "$userId";
	$insert_arr['GAL_ACCESS_PAGE'] 		= $access_page;
	$insert_arr['GAL_OPERATION_ID'] 	= "$operation_id";
	$insert_arr['GAL_ACCESS_TIME'] 		= date('Y-m-d H:i:s');
	$insert_arr['GAL_REQUEST_DATA'] 	= $request_data;
	$insert_arr['GAL_RETURN_DATA'] 		= $response_data;
	$insert_arr['GAL_MICRO_SEC_DIFF'] 	= getDeltaTime();
	array_insert_query("gft_customer_app_access_log", $insert_arr);
}

/**
 * @param string $auth_token
 * @param string $auth_key
 * @param string $zcode
 * 
 * @return int
 */
function check_for_authen($auth_key,$auth_token,$zcode) {
	
	$query = "select EMP_ID,AUTH_KEY,AUTH_TOKEN from gft_emp_auth_key where AUTH_KEY='$auth_key' and AUTH_TOKEN='$auth_token' and GEK_STATUS='A'";
	
	$result = execute_my_query($query);
	
	if(mysqli_num_rows($result) == 1) {
		$row = mysqli_fetch_array($result);
		$userId = (int)$row['EMP_ID'];
		save_access($userId,$zcode); 
		return $userId;
	} else {
		return 0;
	}
}

/**
 *
 * @return void
 */
function sendAuthError() {
	$error = /*. (string[string]) .*/ array();
	$error['status']="failure";
	$error['message']="Token and Key are not valid";
	echo json_encode($error);
}

/**
 * @param string $receive_param
 * @param string $message
 * @param string $error_code
 * 
 * @return void
 */
function sendLoginError($receive_param, $message, $error_code='') {
	$error = /*. (string[string]) .*/ array();
	save_access($userId=0,$zcode='',$udid='',$receive_param, $message,0);//update error response in access tale
	$error['status']="failure";
	$error['message']=$message;
	if($error_code!=''){
		$error['error_code']=$error_code;
	}
	echo json_encode($error);
}
/**
 * @param string $receive_param
 * @param string $message
 * @param int $error_code
 * @param string $type
 * @param int $userId
 * @param string[string] $additonal_response_params
 *
 * @return void
 */
function sendErrorWithCode($receive_param, $message,$error_code,$type='',$userId=0,$additonal_response_params=null) {
	$error = /*. (string[string]) .*/ array();
	$error_code_list=array();
	header('X-PHP-Response-Code: '.$error_code, true, $error_code);
	$error['message']=$message;
	$error['info'] = "samrejected";
	if(is_array($additonal_response_params) and count($additonal_response_params)>0) {
	    $error = array_merge($error,$additonal_response_params);
	}
	echo json_encode($error);
	if($type=='customer'){
		global $log_operation;
		save_mygofrugal_access($userId, $log_operation, $receive_param, json_encode($error));
	}else if($type=="webview"){
		exit;
	}else{
		save_access($userId=0,$zcode='',$udid='',$receive_param, $return_message=$message,0);//update error response in access tale		
	}
}

/**
 * @param string $receive_param
 * @param string $message
 * @param int $error_code
 * @param string $lead_code
 * @param string $req_type
 *  
 * @return void
 */
function send_error($receive_param, $message,$error_code,$lead_code,$req_type) {
	header('X-PHP-Response-Code: '.$error_code, true, $error_code);
	$error_arr['status']="error";
	$error_arr['message']=$message;
	echo json_encode($error_arr);
	log_request($receive_param, json_encode($error_arr), '', $lead_code, $req_type,'',$message);
}

/**
 * @param string $message
 *
 * @return void
 */
function sendFileError($message) {
	$file_stat = /*. (string[string]) .*/ array();
	$file_stat['status']="upload failed";
	$file_stat['message']=$message;
	echo json_encode($file_stat);
}

/**
 * @param string $userId
 *
 * @return string[int]
 */
function get_all_terr_mapped($userId) {

	$ids = /*. (string[int]) .*/array();
	$que1 = "select GET_TERRITORY_ID from gft_emp_territory_dtl where GET_EMP_ID = '$userId'";
	$resul = execute_my_query($que1);
	while($data = mysqli_fetch_array($resul)){
		$a = $data['GET_TERRITORY_ID'];

		$que2 = "select GBT_TERRITORY_ID from gft_business_territory_master where GBT_STATUS='A' and GBT_TERRITORY_ID='$a'";
		$resul2 = execute_my_query($que2);
		if(mysqli_num_rows($resul2)>0){
			$ids[]=$a;
		}else{
			$type='';
			$que3 = "select GBM_MAP_TYPE from gft_business_map_master where GBM_MAP_ID=$a";
			$resul3 = execute_my_query($que3);
			if($data3=mysqli_fetch_array($resul3)){
				$type=$data3['GBM_MAP_TYPE'];
			}

			if($type=='Z'){
				$type="zone_id";
			}else if($type=='R'){
				$type="region_id";
			}else if($type=='A'){
				$type="area_id";
			}
			$que4 = "select terr_id from b_map_view  where $type = $a";
			$resul4 = execute_my_query($que4);
			while ($data4=mysqli_fetch_array($resul4)){
				$ids[]=$data4['terr_id'];
			}
		}
	}
	return $ids;
}


/**
 * @param int $userId
 *
 * @return int
 */
function check_for_partner($userId){
	$que1="select GEM_EMP_ID,GEM_ROLE_ID from gft_emp_master where GEM_EMP_ID=$userId and GEM_STATUS='A'";
	$res1=execute_my_query($que1);
	if( (mysqli_num_rows($res1)==1) and $data1=mysqli_fetch_array($res1) ){
		$role = (int)$data1['GEM_ROLE_ID'];
		if($role==21){  // role 21-partner
			return 1;
		}else if($role==26){  // role 26-partner employee
			return 2;
		}
	}
	return 0;
}
/**
 * @param int $userId
 * @param string $partner
 * @param string $partner_boss
 *
 * @return string
 */
function get_category($userId,$partner,$partner_boss){
	if($partner==1){
		$que2="select GCA_PARTNER_CAATEGORY_ID from gft_cp_agree_dtl join gft_cp_info on (CGI_EMP_ID=$userId and CGI_LEAD_CODE=gca_lead_code)";
	}else{
		$que2="select GCA_PARTNER_CAATEGORY_ID from gft_cp_agree_dtl join gft_cp_info on (CGI_EMP_ID=$partner_boss and CGI_LEAD_CODE=gca_lead_code)";
	}
	$res2=execute_my_query($que2);
	if(mysqli_num_rows($res2)==1 and $data2=mysqli_fetch_array($res2)){
		$categ = $data2['GCA_PARTNER_CAATEGORY_ID'];
		if($categ==''){
			sendError("Partner category is empty");
			exit;
		}
		return $categ;
	}else{
		sendError("Error in Finding Partner category");
		exit;
	}
}
/**
 * @param int $userId
 * 
 * @return string
 */
function get_lead_code_partner($userId){
	$res3=execute_my_query("select GLEM_LEADCODE from gft_leadcode_emp_map where GLEM_EMP_ID='$userId'");
	if(mysqli_num_rows($res3)==1 and $data3=mysqli_fetch_array($res3)){
		$lead_code=$data3['GLEM_LEADCODE'];
		if($lead_code==''){
			sendError("Partner Lead code is empty");
			exit;
		}
		return $lead_code;
	}else{
		sendError("Error in Finding Partner Lead code");
		exit;
	}
	
}
/**
 * @param int $userId
 * 
 * @return string
 */
function get_partner_for_emp($userId){
	$lead = get_lead_code_partner($userId);
	$que4="select GEM_EMP_ID,GLEM_LEADCODE from ".
		" gft_leadcode_emp_map join gft_emp_master on (GEM_STATUS='A' and GEM_EMP_ID=GLEM_EMP_ID) ".
		" join gft_cp_info on (CGI_EMP_ID=GEM_EMP_ID) where GLEM_LEADCODE='$lead'";
	$res4=execute_my_query($que4);
	if( (mysqli_num_rows($res4)==1) && $data4=mysqli_fetch_array($res4) ){
		$partner_owner = $data4['GEM_EMP_ID'];
		if($partner_owner==''){
			sendError("Partner boss is empty");
			exit;
		}
		return $partner_owner;
	}else{
		sendError("Error in Finding Partner boss for Partner Employee");
		exit;
	}
	
}
/**
 *
 * @return string
 */
function generate_tokengen(){
	$length = 10;
	$final_array = /*. (string[int]) .*/range('0','9');
	$token_gen = '';
	while($length--) {
		$key = (int)array_rand($final_array);
		$token_gen .= $final_array[$key];
	}
	return $token_gen;
}

/**
 * @return void
 */
function compress_api_response(){
	ob_start('ob_gzhandler');
	header('Content-Encoding: gzip');
	header('Accept-Encoding: gzip');
}
/**
 * @param int $cat_id
 * 
 * @return mixed[]
 */
function get_leadsource_list($cat_id){
	$return_ary	=	array();
	$res_list	=	execute_my_query("select GLS_SOURCE_CODE from gft_lead_source_master where GLS_STATUS='A' and GLS_SOURCE_CATEGORY='$cat_id'");
	while($row=mysqli_fetch_array($res_list)){
		$return_ary[]	=	$row['GLS_SOURCE_CODE'];
	}
	return $return_ary;
}
/**
 * @param int $cust_user_id
 * 
 * @return boolean
 */
function is_role_switched_user($cust_user_id){
	$sql1 = " select GCL_USER_ID from gft_customer_login_master ".
			" join gft_customer_access_dtl on (GCA_USER_ID=GCL_USER_ID) ".
			" where GCL_USER_ID='$cust_user_id' and GCL_EMP_ID > 0 and GCA_ROLE_SWITCH=1 ";
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1) > 0){
		return true;
	}
	return false;
}

/**
 * @param int $cust_user_id
 * @param boolean $phone_support_leads
 * @param boolean $company_leads
 *
 * @return string[int]
 */
function get_accessible_leads($cust_user_id,$phone_support_leads=false,$company_leads=false){
	$sel_que = " select GCA_ACCESS_LEAD,GLH_LEAD_TYPE from gft_customer_access_dtl ".
			   " join gft_lead_hdr on (GLH_LEAD_CODE=GCA_ACCESS_LEAD) ".
			   " where GCA_USER_ID='$cust_user_id' and GCA_ACCESS_STATUS='1' ";
	if($phone_support_leads){
		$sel_que .= " AND GLH_LEAD_TYPE IN(1,3) ";
	}
	if(is_role_switched_user($cust_user_id)){
		$sel_que .= " and GCA_ROLE_SWITCH=1 ";
	}
	$sel_que .= " group by GCA_ACCESS_LEAD ";
	$res_que = execute_my_query($sel_que);
	$ret_arr = /*. (string[int]) .*/array();
	$corporate_lead_arr = /*. (string[int]) .*/array();
	while($row1 = mysqli_fetch_array($res_que)){
		$ret_arr[] = $row1['GCA_ACCESS_LEAD'];
		if($row1['GLH_LEAD_TYPE']=='3'){
			$corporate_lead_arr[] = $row1['GCA_ACCESS_LEAD'];
		}
	}
	if($phone_support_leads){
		return $ret_arr;
	}
	if(count($corporate_lead_arr) > 0){
		$que1 = " select GLH_LEAD_CODE from gft_lead_hdr where glh_reference_given in (".implode(",", $corporate_lead_arr).") and GLH_LEAD_TYPE=13 ";
		$res1 = execute_my_query($que1);
		while ($data1 = mysqli_fetch_array($res1)){
			if(!in_array($data1['GLH_LEAD_CODE'], $ret_arr)){
				$ret_arr[] = $data1['GLH_LEAD_CODE'];
			}
		}
	}
	if( $company_leads && (count($ret_arr) > 0) ){
	    $ref_que = " select GLH_LEAD_CODE from gft_lead_hdr join gft_assure_care_company on (GLH_REFERENCE_OF_PARTNER=GAC_REF_ID) ".
	   	           " where GAC_IMPL_LEAD_CODE in (".implode(",", $ret_arr).") ";
	    $ref_res = execute_my_query($ref_que);
	    while ($d1 = mysqli_fetch_array($ref_res)){
	        $ret_arr[] = $d1['GLH_LEAD_CODE'];
	    }
	}
	return $ret_arr;
}
/**
 * @param string $authkey
 * @param string $authtoken
 * @param string $curr_vers
 * 
 * @return mixed[string]
 */
function check_authtoken_key($authkey, $authtoken, $curr_vers=''){
	$validate_key	=	execute_my_query("select EMP_ID,GEK_LAST_ACCESS_TIMESTAMP,GEK_STATUS,GEM_ROLE_ID,AUTH_TOKEN,GEK_CURRENT_VERSION,GEK_MIN_VERSION ".
										"  from gft_emp_auth_key INNER join gft_emp_master em on(gem_emp_id=EMP_ID)  ".
										" where AUTH_KEY='$authkey' and GEM_STATUS='A' ");// and AUTH_TOKEN='$authtoken'
	$return_ary	=	/*. (mixed[string]) .*/array();
	if(mysqli_num_rows($validate_key)==0){
		$return_ary['status']= false;
		$return_ary['uid']='';
		$return_ary['response_code']=2;
	}else{
		$row	=	mysqli_fetch_array($validate_key);		
		$dt1 = strtotime($row['GEK_LAST_ACCESS_TIMESTAMP']);
		$using_version = $row['GEK_CURRENT_VERSION'];
		$min_version = $row['GEK_MIN_VERSION'];
		$emp_status=$row['GEK_STATUS'];
		if($row['GEK_LAST_ACCESS_TIMESTAMP']=='' or $row['GEK_LAST_ACCESS_TIMESTAMP']==null){
			$dt1 = strtotime(date('Y-m-d H:i:s'));
		}		
		$dt2 = strtotime(date('Y-m-d H:i:s'));
		$hours = (int)($dt2 - $dt1) / (60);
		$expity_time	=	get_samee_const("Sales_App_Session_Time");
		if($expity_time==''){$expity_time=24;}
		$not_referral_partner=true;
		if($row['GEM_ROLE_ID']=='73'){
			$not_referral_partner=false;
		}
		if( ($curr_vers!='') && ($min_version!='') && (version_compare($curr_vers,$min_version,">=")!==true) ){
			$return_ary['status']= false;
			$return_ary['uid']='';
			$return_ary['response_code']=6;
		}else if( ($hours>$expity_time && $not_referral_partner) || ($row['AUTH_TOKEN']=="") || ($row['AUTH_TOKEN']==null) ){
			$return_ary['status']= false;
			$return_ary['uid']='';
			$return_ary['response_code']=3;
		}else if($emp_status=='I'){
			$return_ary['status']= false;
			$return_ary['uid']='';
			$return_ary['response_code']=4;
		}else if($row['AUTH_TOKEN']!=$authtoken){
			$return_ary['status']= false;
			$return_ary['uid']='';
			$return_ary['response_code']=5;
 		}else{
			$return_ary['status']= true;
			$return_ary['uid']=	$row['EMP_ID'];
			$return_ary['response_code']=1;
			$update_query = " update gft_emp_auth_key SET GEK_LAST_ACCESS_TIMESTAMP=now() ";
			if( ($curr_vers!='') && ($curr_vers!=$using_version) ){
				$update_query .= ",GEK_CURRENT_VERSION='$curr_vers' ";
			}
			$update_query .= " where AUTH_KEY='$authkey' and AUTH_TOKEN='$authtoken' ";
			execute_my_query($update_query);
		}				
	}
	return $return_ary;
}
/**
 * @param string $authkey
 * @param string $authtoken
 *
 * @return mixed[string]
 */
function check_customer_authtoken_key($authkey, $authtoken){
	$validate_query=" select GCL_USER_ID,GCL_USER_STATUS from gft_customer_login_master where ".
					" GCL_AUTH_KEY='".mysqli_real_escape_string_wrapper($authkey)."' and GCL_AUTH_TOKEN='".mysqli_real_escape_string_wrapper($authtoken)."' ";
	$validate_key	=	execute_my_query($validate_query);
	$return_ary	=	/*. (mixed[string]) .*/array();
	if(mysqli_num_rows($validate_key)==0){
		$return_ary['status']= false;
		$return_ary['uid']='';
		$return_ary['response_code']=2;
	}else{
		$row	=	mysqli_fetch_array($validate_key);
		if($row['GCL_USER_STATUS']=='0'){
			$return_ary['status']= false;
			$return_ary['uid']='';
			$return_ary['response_code']=4;
		}else{
			$return_ary['status']= true;
			$return_ary['uid']=	$row['GCL_USER_ID'];
			$return_ary['response_code']=1;
			//execute_my_query("update gft_emp_auth_key SET GEK_LAST_ACCESS_TIMESTAMP=now() where AUTH_KEY='$authkey' and AUTH_TOKEN='$authtoken'");
		}
	}
	return $return_ary;
}

/**
 * @param string $key
 *
 * @return int
 */
function get_empid_from_mobileapp_header($key){
	$emp_id	=	0;
	$res_emp	=	execute_my_query(" select  EMP_ID from gft_emp_auth_key where AUTH_KEY='$key' ");
	while ($row=mysqli_fetch_array($res_emp)){
		$emp_id	=	$row['EMP_ID'];
	}
	return $emp_id;
}
/**
 * @param boolean $authenticate_token
 * 
 * @return int
 */
function mobileAppAuth($authenticate_token=true){
	global $key,$authtoken,$receive_arg,$key_emp_id,$web_user;
	$headers = apache_request_headers();
	if(isset($headers['Key'])){
		$headers['key']=$headers['Key'];
	}
	if(isset($headers['Authtoken'])){
		$headers['authtoken']=$headers['Authtoken'];
	}
	$key 		= isset($headers['key'])?$headers['key']:'';
	$authtoken 	= isset($headers['authtoken'])?$headers['authtoken']:'';
	$web_user 	= isset($headers['web_user'])?$headers['web_user']:'';
	$curr_vers	= isset($headers['currentversion'])?$headers['currentversion']:'';
	$receive_arg.=	"key=$key,authtoken=$authtoken";
	if($key!=''){
		$key_emp_id	=	get_empid_from_mobileapp_header($key);
	}else{
		$key_emp_id	=	$web_user;
	}
	if(($key=='' or $authtoken=='') &&($web_user=='') ){
		$error_msg	=	($key=='')?"Key":"";
		$error_msg	.=	($authtoken=='')?(($error_msg=='')?'Authentication token':', Authentication token'):"";
		sendErrorWithCode($receive_arg,"Required fields $error_msg",HttpStatusCode::BAD_REQUEST);
		exit;
	}else if($web_user!=''){
		return (int)$web_user;		
	}
	if(!$authenticate_token){
		if($key_emp_id > 0){
			return $key_emp_id;
		}
	}
	$auth_arr	=	check_authtoken_key($key, $authtoken,$curr_vers);
	$respon_code=	isset($auth_arr['response_code'])?(int)$auth_arr['response_code']:0;
	if((isset($auth_arr['status'])) and $auth_arr['status']==false){
		if($respon_code==3){
			sendErrorWithCode($receive_arg,"User session expired",HttpStatusCode::UNAUTHORIZED);
		}else if($respon_code==4){
			sendErrorWithCode($receive_arg,"Authorization Failed. Please contact SAM Team.",HttpStatusCode::UNAUTHORIZED);
		}else if($respon_code==5){
			sendErrorWithCode($receive_arg,"Your session is found active in another device. Kindly generate OTP to login in this device.",HttpStatusCode::CONFLICT);
 		}else if($respon_code==6){
 			$err_msg = "Some features of this app will not work with this version. Please update your app to continue";
 			$platform = get_single_value_from_single_table("GEK_DEVICE_PLATFORM", "gft_emp_auth_key", "AUTH_KEY", $key);
 			if($platform=='1'){
 				$err_msg = "Please take latest update from SAM login page(sam.gofrugal.com) to continue";
 			}
 			sendErrorWithCode($receive_arg,$err_msg, HttpStatusCode::UPGRADE_REQUIRED);
 		}else{
			sendErrorWithCode($receive_arg,"Invalid Key/Authentication token",HttpStatusCode::UNAUTHORIZED);
		}
		exit;
	}
	$mobile_uid	= (int)$auth_arr['uid'];
	return $mobile_uid;
}

/**
 * @param string $key
 * @param string $authtoken
 * 
 * @return int
 */
function customerAppAuth($key='',$authtoken=''){
	global $receive_arg;
	if( ($key=='') || ($authtoken=='') ){
		$headers = apache_request_headers();
		if(isset($headers['Key'])){
			$headers['key']=$headers['Key'];
		}
		if(isset($headers['Authtoken'])){
			$headers['authtoken']=$headers['Authtoken'];
		}
		$key 		= isset($headers['key'])?$headers['key']:'';
		$authtoken 	= isset($headers['authtoken'])?$headers['authtoken']:'';
	}
	$receive_arg =	"key=$key,authtoken=$authtoken";
	if($key=='' or $authtoken==''){
		$error_msg	=	($key=='')?"Key":"";
		$error_msg	.=	($authtoken=='')?(($error_msg=='')?'Authentication token':', Authentication token'):"";
		sendErrorWithCode($receive_arg,"Required fields $error_msg",HttpStatusCode::BAD_REQUEST,"customer");
		exit;
	}
	$key_user_id=0;
	if($key!=''){
		$key_user_id = get_single_value_from_single_table("GCL_USER_ID", "gft_customer_login_master", "GCL_AUTH_KEY", $key);
	}
	$auth_arr	 =	check_customer_authtoken_key($key, $authtoken);
	$respon_code =	/* .(int). */isset($auth_arr['response_code'])?$auth_arr['response_code']:0;
	if((isset($auth_arr['status'])) and $auth_arr['status']==false){
		if((int)$respon_code==3){
			sendErrorWithCode($receive_arg,"Your session has expired. Please login to continue.",HttpStatusCode::UNAUTHORIZED,"customer",$key_user_id);
		}else if((int)$respon_code==4){
			sendErrorWithCode($receive_arg,"Authorization Failed",HttpStatusCode::UNAUTHORIZED,"customer",$key_user_id);
		}else{
			sendErrorWithCode($receive_arg,"Your session has expired. Please login to continue.",HttpStatusCode::UNAUTHORIZED,"customer",$key_user_id);
		}
		exit;
	}
	$cust_user_uid	=	/*. (int) .*/(int)$auth_arr['uid'];
	return $cust_user_uid;
}
/**
 *  @param string $username
 *  
 *  @return int 
 */
function get_emp_id($username){
	if($username==''){
		return 0;
	}
	$con_qry	=	" GLM_LOGIN_NAME='$username' ";
	$res_check_user	=	execute_my_query(" select GLM_LOGIN_NAME,GLM_EMP_ID,GEM_MOBILE,GEM_RELIANCE_NO,GEM_EMAIL from gft_login_master ".
			" inner join gft_emp_master on(gem_emp_id=glm_emp_id) ".
			" where $con_qry and gem_status='A' limit 1");	
	if(mysqli_num_rows($res_check_user)==0){
		return 0;
	}
	$emp_row	=	mysqli_fetch_array($res_check_user);
	//$mobile_no	=	$emp_row['GEM_MOBILE'];
	//$email		=	$emp_row['GEM_EMAIL'];
	$emp_id		=	(int)$emp_row['GLM_EMP_ID'];
	return $emp_id;
}

/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param int $emply_id
 * 
 * @return string[string]
 */
function get_lead_contact_detail($lead_code, $page_size=0, $offset=0, $emply_id=0){
	$limit_qry		=	"";
	$return_array	=	/*. (string[string]) .*/array();
	$total_element	=	array();
	$group_title	=	'';
	if($page_size!=0){
		$limit_qry	=	" LIMIT $offset, $page_size";
	}else{
		$limit_qry	=	" LIMIT 5";
	}
	$con_que =  " select GCC_ID,GCC_CONTACT_NAME, GCD_NAME, gct_desc,GCC_CONTACT_NO,GCG_CONTACT_GROUP_NAME,gct_id from gft_customer_contact_dtl ".
				" inner join gft_cust_contact_type_master on(gcc_contact_type=gct_id)  ".
				" left join gft_contact_dtl_group_map cd on(cd.GCG_CONTACT_ID=GCC_ID) ".
				" left join gft_contact_dtl_group_master cg on(cg.GCG_CONTACT_GROUP_ID=cd.GCG_GROUP_ID) ".
				" left join gft_contact_designation_master cdm on(cdm.GCD_CODE=gcc_designation) ".
				" where GCC_LEAD_CODE='$lead_code' order by gcc_id";
	$res_tot_count	=	execute_my_query($con_que);
	$return_array['total_count']	=	(int)mysqli_num_rows($res_tot_count);
	$con_que		=	$con_que.$limit_qry;
	$res_elements	=	execute_my_query($con_que);
	$hide_contact = ($emply_id!=0) ? restrict_contact_access($emply_id) : false;
	while ($row_data = mysqli_fetch_array($res_elements)){
		$element_array['label']	=	$row_data['gct_desc'];
		$element_array['contact_name']	=	$row_data['GCC_CONTACT_NAME'];
		$element_array['contact_designation']	=	$row_data['GCD_NAME'];
		$element_array['value']	=	$hide_contact ? "" : $row_data['GCC_CONTACT_NO'];
		$element_array['contact_group']	=	$row_data['GCG_CONTACT_GROUP_NAME'];
		$element_array['contact_id']  = $row_data['GCC_ID'];
		$element_array['hide']        = $hide_contact; 
		if(in_array($row_data['gct_id'], array(1,2,3))){
			$element_array['type']	=	array("telno","sms");
		}elseif($row_data['gct_id']=='4'){
			$element_array['type']	=	array("email");
		}else{
			$element_array['type']	=	array("text");
		}
		
		$total_element[]	=	$element_array;
		$group_title		=	"Contact Details";
	}
	$return_array['group_title']	=	$group_title;
	$return_array['elements']	=	$total_element;
	return $return_array;
}
/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param int $emply_id
 *
 * @return mixed[]
 */
function get_lead_contact_group_by_name($lead_code, $page_size=0, $offset=0, $emply_id=0){
	$return_array	=	array();
	$total_element	=	array();
	$hide_contact = ($emply_id!=0) ? restrict_contact_access($emply_id) : false;
	$que1 = " select  GCC_CONTACT_NAME, GCD_NAME, gcc_designation, gct_desc,GCC_CONTACT_NO,GCG_CONTACT_GROUP_NAME,GCG_CONTACT_GROUP_ID, ".
	        " GCC_ID,gcc_contact_type,group_concat(ifnull(GCG_CONTACT_GROUP_NAME,'')) as contact_group_name, ".
	        " group_concat(ifnull(GCG_CONTACT_GROUP_ID,'')) as contact_group_id ".
			" from gft_customer_contact_dtl join gft_cust_contact_type_master on(gcc_contact_type=gct_id) ".  
			" left join gft_contact_dtl_group_map cd on(cd.GCG_CONTACT_ID=GCC_ID) ".
			" left join gft_contact_dtl_group_master cg on(cg.GCG_CONTACT_GROUP_ID=cd.GCG_GROUP_ID) ". 
			" left join gft_contact_designation_master cdm on(cdm.GCD_CODE=gcc_designation) ".
			" where GCC_LEAD_CODE='$lead_code' group by gcc_id ";
	$res	=	execute_my_query($que1);
	$arr = array();	
	while($row=mysqli_fetch_array($res)){
	    $ind = $row['GCC_CONTACT_NAME']."||".$row['GCD_NAME']."||".$row['gcc_designation'];
	    $cont_type = $row['gcc_contact_type'];
	    $type_arr = array('text');
	    if(in_array($cont_type, array('1','2','3'))){
	        $type_arr = array('telno','sms');
	    }elseif ($cont_type=='4'){
	        $type_arr = array('email');
	    }
	    $cg_arr = ($row['contact_group_name']!='') ? explode(",",$row['contact_group_name']) : array();
	    $ci_arr = ($row['contact_group_id']!='') ? explode(",",$row['contact_group_id']) : array();
	    $arr[$ind][] = array("id"=>$row['GCC_ID'],"label"=>$row['gct_desc'],"contact_type"=>$cont_type,"value"=>$row['GCC_CONTACT_NO'],
	                       "contact_group"=>$cg_arr,"contact_group_id"=>$ci_arr,"type"=>$type_arr,"contact_id"=>$row['GCC_ID'],"hide"=>$hide_contact);
	}
	foreach ($arr as $key => $val){
	    $customer_contacts	=	array();
	    $key_arr = explode("||", $key);
	    $customer_contacts['contact_name']	  =	$key_arr[0];
	    $customer_contacts['designation']	  =	$key_arr[1];
	    $customer_contacts['designation_id']  =	$key_arr[2];
	    $customer_contacts['contacts']	      =	$val;
	    $total_element[]	=	$customer_contacts;
	}
	$return_array['total_count']       = count($arr);
	$return_array['customer_contacts'] = $total_element;
	return $return_array;
}
/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param boolean $default_flag
 * 
 * @return string[string]
 */
function get_visit_history_dtl($lead_code, $page_size=0, $offset=0, $default_flag=false){
	$limit_qry		=	"";
	$return_array	=	array();
	$total_element	=	array();
	$group_title	=	'';
	if($page_size!=0){
		$limit_qry	=	" LIMIT $offset, $page_size";
	}else{
		$limit_qry	=	" LIMIT 1";
	}
	$label_array	=	array("Visit Date","Visited By","Visit Nature","Next Action Date",
			 				"Next Action","Next Action detail","My Comments","Customer Feedback","Expected Date of Closure");
	$datatype_array	=	array("date","text","text","date","text","text","text","text","date");
	$sql_visite		=	" select GLD_VISIT_DATE,TRIM(TRAILING ',' FROM concat(em.gem_emp_name,',',ifnull(group_concat(distinct(jem.gem_emp_name)),''))) as visited_by,".
						" vn.GAM_ACTIVITY_DESC as visit_nature, GLD_NEXT_ACTION_DATE as next_action_date,nvn.GAM_ACTIVITY_DESC as next_action, ".
						" GLD_NEXT_ACTION_DETAIL as next_action_detail, concat(ifnull(mc.gmc_name,''),'.',ifnull(GLD_NOTE_ON_ACTIVITY,'')) as my_comments ,".
						" concat(ifnull(cm.gcm_feedback_desc,''),'.',ifnull(GLD_CUST_FEEDBACK,'')) as cust_feedback, GLD_APPORX_TIMETOCLOSE ".
						" from gft_activity a ".
						" join gft_emp_master em on (gld_emp_id=em.gem_emp_id) left join gft_customer_status_master cs on (GCS_CODE=gld_lead_status) ".
						" left join gft_prospects_status_master ps on (GPS_STATUS_ID=GLD_PROSPECTS_STATUS) ".
						" left join gft_reason_for_change_lstatus rcs on (GRL_ID=GLD_REASON_FOR_STATUS_CHANGE) ".
						" left join gft_reason_for_change_in_prospect_status rps on (GRC_ID=GLD_REASON_FOR_PROSPECT_STATUS_CHANGE) ".
						" left join gft_customer_feedback_master cm on cm.gcm_feedback_code=a.gld_cust_feedback_code ".
						" left join gft_my_comments_master mc on mc.GMC_CODE=a.GLD_MY_COMMENTS_CODE ".
						" left join gft_joint_visit_dtl jv on (jv.GJV_ACTIVITY_ID=a.gld_activity_id and jv.GJV_EMP_ID=a.gld_emp_id and jv.GJV_VISIT_DATE=a.gld_visit_date) ".
						" left join gft_joint_activity jact on (jact.GJA_ACTIVITY_ID=a.gld_activity_id and jact.GJA_EMP_ID=a.gld_emp_id and jact.GJA_LEAD_CODE=a.gld_lead_code and jact.GJA_VISIT_DATE=a.gld_visit_date) ".
						" left join gft_activity_master jact_m on (jact_m.gam_activity_id=jact.GJA_VISIT_NATURE) ".
						" left join gft_emp_master jem on (jem.gem_emp_id=jv.GJV_JOINT_EMP_ID) ".
						" left join gft_activity_master vn on (vn.GAM_ACTIVITY_ID=GLD_VISIT_NATURE) ".
						" left join gft_activity_master nvn on(nvn.GAM_ACTIVITY_ID=GLD_NEXT_ACTION) ".
						" where gld_lead_code='$lead_code' and GLD_VISIT_NATURE not in (70) group by a.gld_activity_id,a.gld_emp_id ORDER BY GLD_VISIT_DATE desc,gld_date DESC ";
	$res_tot_count	=	execute_my_query($sql_visite);
	$total_count	=	(int)mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= $total_count;
	$sql_visite		=	$sql_visite.$limit_qry;
	$res_elements	=	execute_my_query($sql_visite);
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			$element_array['label']	=	$label_array[$i];
			$element_array['value']	=	htmlspecialchars(stripslashes($row[$i]),ENT_COMPAT);
			$element_array['type']	=	$datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value'])<=0){
					$element_array['value']=null;
				}
			}
			$total_element[]		=	$element_array;
			if($i==2){
				$group_title	=	$row[$i];
			}
		}
		$block_element['offset']		=	++$offset;
		$block_element['group_title']	=	$group_title;
		$block_element['elements']		=	$total_element;
		unset($total_element);
		$return_array['elements'][]		= $block_element;
	}
	
	return $return_array;
}

/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param boolean $default_flag
 *
 * @return string[string]
 */
function get_call_history_dtl($lead_code, $page_size=0, $offset=0, $default_flag=false){
	$total_element	=	array();
	$limit_qry      =    "";
	$return_array   =    array();
	$element_array  =    array();
	$group_title    =    'Call History';
	if($page_size!=0){
		$limit_qry    =    " LIMIT $offset, $page_size";
	}else{
		$limit_qry    =    " LIMIT 1";
	}
	$label_array    =    array("Date & Time","Call Status","Agent Name","MSN Number","Phone Number","Duration","Ring Time",
			"Wrapup Time","Transfer Type","Recall Status","Support Group","Recording");

	$datatype_array    =    array("datetime","text","text","text","text","text","text","text","text","text","text","audio");

	$sql_call  =" select GTC_DATE,GVC_NAME,GEM_EMP_NAME,GTC_MSN_NO,GTC_NUMBER,GTC_DURATION,GTC_RING_TIME,".
			" GTC_WRAPUP_TIME,GTC_TRANSFER_TYPE,GTC_RECALL_STATUS, GVG_GROUP_NAME, GTC_PATH ".
			" from gft_techsupport_incomming_call  ".
			" join gft_emp_master em on (GTC_AGENT_ID=em.gem_emp_id) ".
			" left join gft_voicesnap_call_status_master on (GVC_ID=GTC_CALL_STATUS) ".
			" left join gft_voicenap_group on (GTC_MAIN_GROUP=GVG_GROUP_ID) ".
			" where GTC_LEAD_CODE='$lead_code' order by GTC_DATE desc ";

	$res_tot_count    =    execute_my_query($sql_call);
	$total_count    =    (int)mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= $total_count;
	$sql_call        =    $sql_call.$limit_qry;
	$res_elements    =    execute_my_query($sql_call);
	$KMU_VOICESNAP_SERVER	= get_samee_const("KMU_VOICESNAP_SERVER");
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			$element_array['label'] =	 $label_array[$i];
			if($element_array['label']=='Recording'){
				if($row[$i]==''){
					continue;
				}
				$element_array['value'] = "$KMU_VOICESNAP_SERVER/play.php?p=".$row[$i];
				if(strpos($row[$i],'http')===0){ //direct solution infini files
					$element_array['value'] = $row[$i];
				}
			}else if( ($i>=5) && ($i<=7) ){
				$hours = $minutes = $seconds = 0;
				sscanf($row[$i], "%d:%d:%d", $hours, $minutes, $seconds);
				$time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
				$element_array['value'] =    $time_seconds;
			}else{
				$element_array['value'] =    $row[$i];
			}
			$element_array['type']  =    $datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value'])<=0){
					$element_array['value']=null;
				}
			}
			$total_element[]        =    $element_array;
			if($i==0){
				$group_title    =    "Call History - ".$row[$i];
			}
		}
		$block_element['offset']		=	++$offset;
		$block_element['group_title']	=	$group_title;
		$block_element['elements']		=	$total_element;
		unset($total_element);
		$return_array['elements'][]		= $block_element;
	}
	return $return_array;
}

/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param boolean $default_flag
 * @param int $ticket_id
 * @param string $sorttype
 *
 * @return string[string]
 */
function get_support_history_dtl($lead_code, $page_size=0, $offset=0, $default_flag=false, $ticket_id=0,$sorttype=''){
	$limit_qry		=	"";
	$return_array	=	array();
	$total_element	=	array();
	$group_title	=	'';
	if($page_size!=0){
		$limit_qry	=	" LIMIT $offset, $page_size";
	}else{
		$limit_qry	=	" LIMIT 1";
	}
	$label_array	=	array("Activity Id","Activity Date","Complaint By","Complaint Id","Complaint Date","Complaint","Summary","Activity Desc",
							"Nature","Status","Scheduled To ","Schedule Date","Solution Given","Severity","Priority","Emotion","Recording");
	$datatype_array	=	array("text","datetime","text","text","datetime","text","text","text",
				"text","text","text","datetime","text","text","text","text","audio");
	$sql_total_pending	=	"select sum(if(gch_current_status='T1',1,0)) solved,sum(if(gch_current_status!='T1',1,0)) pending, sum(1) total  from gft_customer_support_hdr where GCH_LEAD_CODE=$lead_code";
	$res_total_pending	=	execute_my_query($sql_total_pending);
	if(mysqli_num_rows($res_total_pending)==1){
		$row_pending	=	mysqli_fetch_array($res_total_pending);
		$element_array['label']	=	"Pending / Total complaints";
		$element_array['value']	=	(int)$row_pending['pending']."/".(int)$row_pending['total'];
		$element_array['type']	=	'text';
		$total_element[]		=	$element_array;
	}
	$wh_cond = "";
	if($ticket_id!=0){
		$wh_cond .= " and hdr.GCH_COMPLAINT_ID='$ticket_id' ";
	}
	$sql_support	=	" SELECT dtl.GCD_ACTIVITY_ID,dtl.GCD_ACTIVITY_DATE,em.GEM_EMP_NAME, hdr.GCH_COMPLAINT_ID, hdr.GCH_COMPLAINT_DATE,  ".
						" cm.GFT_COMPLAINT_DESC,dtl.GCD_PROBLEM_SUMMARY,dtl.GCD_PROBLEM_DESC, G.GCM_NATURE,F.GTM_NAME, PE.GEM_EMP_NAME AS PEMP, ".
						" dtl.GCD_SCHEDULE_DATE,  dtl.GCD_REMARKS,C.GSM_NAME,D.GPM_NAME, CE.GCM_EMOTION_NAME,GCD_VN_TRANSID  FROM gft_customer_support_dtl dtl ".
						" join gft_customer_support_hdr hdr on(hdr.GCH_COMPLAINT_ID =dtl.GCD_COMPLAINT_ID) ".
						" join gft_lead_hdr lh on (lh.glh_lead_code=hdr.GCH_LEAD_CODE) ".
						" join gft_product_family_master B on (hdr.GCH_PRODUCT_CODE=B.GPM_PRODUCT_CODE) ".
						" join gft_product_group_master pg on (pg.gpg_product_family_code=B.GPM_head_family AND hdr.GCH_PRODUCT_SKEW =pg.gpg_skew ) ".
						" JOIN gft_status_master F on (dtl.GCD_STATUS = F.GTM_CODE) ".
						" left JOIN gft_complaint_master cm ON (hdr.GCH_COMPLAINT_CODE= cm.GFT_COMPLAINT_CODE) ".
						" left JOIN gft_severity_master C on (dtl.GCD_SEVERITY=C.GSM_CODE) ".
						" left JOIN gft_priority_master D on (dtl.GCD_PRIORITY=D.GPM_CODE) ".
						" left JOIN gft_complaint_nature_master G on (dtl.GCD_NATURE = G.GCM_NATURE_ID) ".
						" LEFT JOIN gft_emp_master em ON (dtl.GCD_EMPLOYEE_ID=em.GEM_EMP_ID) ".
						" LEFT JOIN gft_customer_emotion_master CE ON (CE.GCM_EMOTION_ID = dtl.GCD_CUSTOMER_EMOTION) ".
						" LEFT JOIN gft_emp_master PE ON (dtl.GCD_PROCESS_EMP=PE.GEM_EMP_ID) ".
						" WHERE glh_lead_code='$lead_code' $wh_cond and hdr.GCH_COMPLAINT_CODE not in (306,307) AND dtl.GCD_ACTIVITY_DATE >= '2004-11-01 00:00:00' ".
						" group by GCD_COMPLAINT_ID,gcd_activity_id ";
	if($sorttype=='pending'){
		$sql_support .= " ORDER BY if(GTM_GROUP_ID=3,1000,GTM_GROUP_ID), GCD_ACTIVITY_DATE desc ";
	}else{
		$sql_support .= " ORDER BY GCD_ACTIVITY_DATE desc "; 
	}
	$res_tot_count	=	execute_my_query($sql_support);
	$total_count	=	(int)mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= $total_count;
	$sql_support		=	$sql_support.$limit_qry;
	$res_elements	=	execute_my_query($sql_support);
	$KMU_VOICESNAP_SERVER	= get_samee_const("KMU_VOICESNAP_SERVER");
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			$element_array['label']	=	$label_array[$i];
			if($element_array['label']=='Recording'){
				if( ($row[$i]=='') || ($row[$i]=='0') ){
					continue;
				}
				$gtc_path = get_single_value_from_single_table("GTC_PATH", "gft_techsupport_incomming_call", "GTC_TRANS_ID", $row[$i]);  //due to perfomance issue on single join
				if($gtc_path==''){
					continue;
				}
				$element_array['value'] = "$KMU_VOICESNAP_SERVER/play.php?p=".$gtc_path;
			}else{
				$element_array['value']	=	htmlspecialchars(stripslashes($row[$i]),ENT_COMPAT);
			}
			$element_array['type']	=	$datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value'])<=0){
					$element_array['value']=null;
				}
			}
			$total_element[]		=	$element_array;
			if($i==5){
				$group_title	=	$row[$i];
			}
		}
		$block_element['offset']		=	++$offset;
		$block_element['group_title']	=	$group_title;
		$block_element['activity_datetime']	=	$row[1];
		$block_element['status']		=	$row[9];
		$block_element['remarks']		=	$row[12];
		$block_element['elements']		=	$total_element;
		unset($total_element);
		$return_array['elements'][]		= $block_element;
	}
	return $return_array;
}

/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param boolean $default_flag
 *
 * @return string[string]
 */
function get_installation_history($lead_code, $page_size=0, $offset=0,$default_flag=false){
	$limit_qry      =    "";
	$total_element	=	array();
	$return_array   =    array();
	$element_array  =    array();
	$group_title    =    'Installation';
	if($page_size!=0){
		$limit_qry  =    " LIMIT $offset, $page_size";
	}else{
		$limit_qry  =    " LIMIT 1";
	}
	$label_array   	= array("Product Name & Skew","Install Date","ASA / Subscription Expiry Date","License Status","Order No",
			"No. Of Clients/ Register", "Installed Status","Current Version");

	$datatype_array = array("text","date","date","text","text","text","text","text");

	$sql_que  =" select concat(pfm.GPM_PRODUCT_ABR,' ',pm.GPM_SKEW_DESC) as pname, GID_INSTALL_DATE, GID_VALIDITY_DATE, if(GOD_ORDER_APPROVAL_STATUS=1,GOS_STATUS_NAME,GLS_STATUS_NAME) as stat_name, ".
			" concat(GID_ORDER_NO,'-',substr(concat('0000',GID_FULLFILLMENT_NO),-4)) as orderfull, GID_NO_CLIENTS, GID_STATUS, GID_CURRENT_VERSION ".
			" from gft_install_dtl_new  ".
			" join gft_order_hdr oh on (god_order_no=gid_order_no) " .
			" left join gft_order_approval_status_master on (GOS_ID=oh.GOD_ORDER_APPROVAL_STATUS) ".
			" join gft_product_master pm on(GID_LIC_PCODE=pm.gpm_product_code and gid_lic_pskew=pm.gpm_product_skew) " .
			" join gft_product_family_master pfm on(pfm.gpm_product_code=pm.gpm_product_code) " .
			" left join gft_order_product_dtl opd on (opd.GOP_ORDER_NO=GID_ORDER_NO and opd.GOP_FULLFILLMENT_NO=GID_FULLFILLMENT_NO and opd.GOP_PRODUCT_CODE=GID_PRODUCT_CODE and opd.GOP_PRODUCT_SKEW=GID_PRODUCT_SKEW) ".
			" left join gft_cp_order_dtl cp on (cp.GCO_ORDER_NO=GID_ORDER_NO and cp.GCO_FULLFILLMENT_NO=GID_FULLFILLMENT_NO and cp.GCO_PRODUCT_CODE=GID_PRODUCT_CODE and cp.GCO_SKEW=GID_PRODUCT_SKEW) ".
			" left join gft_lic_status_master on (if(oh.GOD_ORDER_SPLICT=0, GLS_ID=opd.GOP_LICENSE_STATUS, GLS_ID=cp.GCO_LICENSE_STATUS) ) ".
			" where gid_lead_code='$lead_code' order by gid_install_date desc ";

	$res_tot_count    =    execute_my_query($sql_que);
	$total_count    =    (int)mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= $total_count;
	$sql_que        =    $sql_que.$limit_qry;
	$res_elements    =    execute_my_query($sql_que);
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			$element_array['label'] =	 $label_array[$i];
			$element_array['value'] =    $row[$i];
			$element_array['type']  =    $datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value'])<=0){
					$element_array['value']=null;
				}
			}
			$total_element[]        =    $element_array;
			if($i==4){
				$group_title    =    "Installation - ".$row[$i];
			}
		}
		$block_element['offset']		=	++$offset;
		$block_element['group_title']	=	$group_title;
		$block_element['elements']		=	$total_element;
		unset($total_element);
		$return_array['elements'][]    =    $block_element;
	}
	return $return_array;
}

/**
 * @param int $lead_code
 *
 * @return string[string]
 */
function get_customer_shop_details($lead_code){
	$return_array	=	/*. (string[string]) .*/array();
	$element_array	=	array();
	$total_element	=	array();
	$label_array	=	array("Customer ID","Shop Name","Door No","Block/Society Name","Street No","Street Name ",
			"Area Name","Location","City","State","Pincode");
	$datatype_array	=	array("text","text","text","text","text","text","text","text","text","text","text");	
	$sql_support		=	" select GLH_LEAD_CODE,GLH_CUST_NAME,GLH_DOOR_APPARTMENT_NO,GLH_BLOCK_SOCEITY_NAME, GLH_STREET_DOOR_NO, ".
							" GLH_CUST_STREETADDR1, GLH_AREA_NAME, GLH_CUST_STREETADDR2,GLH_CUST_CITY,GLH_CUST_STATECODE, GLH_CUST_PINCODE  ".
							" from gft_lead_hdr where GLH_LEAD_CODE='$lead_code' ";
	$res_elements	=	execute_my_query($sql_support);
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			$element_array['label']	=	$label_array[$i];
			$element_array['value']	=	$row[$i];
			$element_array['type']	=	$datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value'])<=0){
					$element_array['value']=null;
				}
			}
			$total_element[]		=	$element_array;
		}
	}
	$return_array['elements']	=	$total_element;
	return $return_array;
}
/**
 * @param int $lead_code
 *
 * @return string[string]
 */
function get_customer_type_details($lead_code){
	$return_array	=	/*. (string[string]) .*/array();
	$element_array	=	array();
	$total_element	=	array();
	$label_array	=	array("Lead created by","Lead created date","Lead created category","Installed by","Lead Status","Prospect Status",
			"Vertical","Product Interested","Lead Type","Lead Sub Type");
	$datatype_array	=	array("text","text","text","text","text","text","text","text","text","text");
	$remove_empty	=	array(1,9);
	$sql_support		=	" select ce.GEM_EMP_NAME as created_by, GLH_CREATED_DATE, GCC_NAME,em.GEM_EMP_NAME as installed_by,GCS_NAME as lead_status,GPS_STATUS_NAME as prospect_status   ,GTM_VERTICAL_DESC, group_concat(distinct(GPM_PRODUCT_ABR)) interest_product,GLD_TYPE_NAME , GLS_SUBTYPE_NAME as lead_sub_type from gft_lead_hdr  lh ".
							" inner join gft_vertical_master gv on(gv.GTM_VERTICAL_CODE=lh.GLH_VERTICAL_CODE) ".
							" inner join gft_customer_status_master cs on(cs.GCS_CODE=lh.GLH_STATUS) ".
							" inner join gft_lead_type_master lt on(lt.GLD_TYPE_CODE=lh.GLH_LEAD_TYPE) ".
							" left join gft_emp_master ce on(ce.gem_emp_id=GLH_CREATED_BY_EMPID) ".
							" left join gft_lead_create_category lc on(lh.GLH_CREATED_CATEGORY=lc.GCC_ID) ".
							" left join gft_prospects_status_master pc on(pc.GPS_STATUS_ID=lh.GLH_PROSPECTS_STATUS) ".
							" left join gft_install_dtl_new ins on(ins.GID_LEAD_CODE=lh.GLH_LEAD_CODE) ".
							" left join gft_emp_master em on (ins.GID_INSTALLED_EMP = em.GEM_EMP_ID) ".
							" left join gft_lead_product_dtl lp on(lp.GLC_LEAD_CODE=lh.GLH_LEAD_CODE) ".
							" left join gft_product_family_master pfm on (lp.GLC_PRODUCT_CODE=pfm.GPM_PRODUCT_CODE) ".
							" left join gft_lead_subtype_master ls on(ls.GLS_SUBTYPE_CODE=lh.GLH_LEAD_SUBTYPE) ".
							" where lh.GLH_LEAD_CODE='$lead_code'";
	$res_elements	=	execute_my_query($sql_support);
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			if(!(in_array($i, $remove_empty) and $row[$i]=='')){
				$element_array['label']	=	$label_array[$i];
				$element_array['value']	=	$row[$i];
				$element_array['type']	=	$datatype_array[$i];
				if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
					if(strtotime($element_array['value'])<=0){
						$element_array['value']=null;
					}
				}
				$total_element[]		=	$element_array;
			}
			
		}
	}
	$return_array['elements']	=	$total_element;
	return $return_array;
}
/**
 * @param int $lead_code
 *
 * @return string[string]
 */
function get_lead_source_details($lead_code){	
	$return_array	=	/*. (string[string]) .*/array();
	$element_array	=	array();
	$total_element	=	array();
	$label_array	=	array("Channel partner","Others","Internal");
	$datatype_array	=	array("text","text","text");
	$sql_support		=	"select lh1.glh_cust_name as partner_name, lh.GLH_REFERREDBY as others, em.gem_emp_name as internal
							from gft_lead_hdr lh
							left join gft_lead_hdr lh1 on(lh1.glh_lead_code=lh.GLH_REFERENCE_OF_PARTNER)
							left join gft_emp_master em on(em.gem_emp_id=lh.GLH_REFERENCE_INTERNAL)
							where lh.GLH_LEAD_CODE='$lead_code'";
	$res_elements	=	execute_my_query($sql_support);
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
				$element_array['label']	=	$label_array[$i];
				$element_array['value']	=	$row[$i];
				$element_array['type']	=	$datatype_array[$i];
				$total_element[]		=	$element_array;
				
		}
	}
	$return_array['elements']	=	$total_element;
	return $return_array;
}
/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param boolean $default_flag
 * 
 * @return string[string]
 */
function get_coupon_history_dtl($lead_code, $page_size=0, $offset=0,$default_flag=true){
	$limit_qry		=	"";
	$return_array	=	array();
	$total_element	=	array();
	$group_title	=	'';
	if($page_size!=0){
		$limit_qry	=	" LIMIT $offset, $page_size";
	}else{
		$limit_qry	=	" LIMIT 1";
	}
	$label_array	=	array("Coupon No","Coupon Issued by","Issued Date","Status","Signed Off date","Received by","Coupon expiry date");
	$datatype_array	=	array("text","text","date","text","date","text","date");
	$sql_coupon		=	" select GCD_COUPON_NO,em.gem_emp_name as issued_by ,GCD_GIVEN_DATE,GCD_SIGNED_OFF,GCD_RECEIVED_DATE,em1.gem_emp_name as received_by,GCD_EXPIRY_DATE  from gft_coupon_distribution_dtl ". 
						" inner join gft_emp_master em on(em.gem_emp_id=GCD_HANDLED_BY) ".
						" left join gft_emp_master em1 on(em1.gem_emp_id=GCD_RECEIVED_BY) ".
						" where GCD_TO_ID='$lead_code' order by GCD_GIVEN_DATE desc ";
	$res_tot_count	=	execute_my_query($sql_coupon);
	$total_count	=	(int)mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= $total_count;
	$sql_coupon		=	$sql_coupon.$limit_qry;
	$res_elements	=	execute_my_query($sql_coupon);
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			$element_array['label']	=	$label_array[$i];
			if($i==3){
				if($row[$i]=='Y'){
					$row[$i]	=	"Signed Off";
				}else if($row[$i]=='N'){
					$row[$i]	=	"Pending";
				}else if($row[$i]=='C'){
					$row[$i]	=	"Cancel";
				}
			}
			$element_array['value']	=	$row[$i];
			$element_array['type']	=	$datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value'])<=0){
					$element_array['value']=null;
				}
			}
			$total_element[]		=	$element_array;
			if($i==0){
				$group_title	=	"Coupon- ".$row[$i];
			}
		}
		$block_element['offset']		=	++$offset;
		$block_element['group_title']	=	$group_title;
		$block_element['elements']		=	$total_element;
		unset($total_element);
		$return_array['elements'][]    =    $block_element;
	}
	return $return_array;
}
/**
 * @param int $lead_code
 * @param int $page_size
 * @param int $offset
 * @param boolean $default_flag
 *
 * @return string[string]
 */
function get_pd_history_dtl($lead_code, $page_size=0, $offset=0,$default_flag=true){
	$limit_qry		=	"";
	$return_array	=	array();
	$total_element	=	array();
	$group_title	=	'';
	if($page_size!=0){
		$limit_qry	=	" LIMIT $offset, $page_size";
	}else{
		$limit_qry	=	" LIMIT 1";
	}
	$label_array	=	array("Audit Date","Audit Type","Customer Comments","My comments","Audit By","Regional Coordinator","Field Incharge");
	$datatype_array	=	array("datetime","text","text","text","text","text","text");
	$sql_coupon		=	" select GAH_DATE_TIME,GAT_AUDIT_DESC, GAH_CUSTOMER_COMMENTS, GAH_MY_COMMENTS, em1.gem_emp_name as audit_by, em2.gem_emp_name as rc_name, em3.gem_emp_name as pc_name from gft_audit_hdr ". 
						" inner join gft_audit_type_master am on(am.GAT_AUDIT_ID=GAH_AUDIT_TYPE) ".
						" inner join gft_emp_master em1 on(em1.gem_emp_id=GAH_AUDIT_BY) ".
						" inner join gft_emp_master em2 on(em2.gem_emp_id=GAH_L1_INCHARGE) ".
						" inner join gft_emp_master em3 on(em3.gem_emp_id=GAH_FIELD_INCHARGE) ".
						" where gah_lead_code='$lead_code' and GAH_REFFERNCE_ORDER_NO!=''  order by GAH_AUDIT_ID desc";
	$res_tot_count	=	execute_my_query($sql_coupon);
	$total_count	=	(int)mysqli_num_rows($res_tot_count);
	$return_array['total_count']	= $total_count;
	$sql_coupon		=	$sql_coupon.$limit_qry;
	$res_elements	=	execute_my_query($sql_coupon);
	while($row=mysqli_fetch_row($res_elements)){
		for($i=0;$i<count($row);$i++){
			$element_array['label']	=	$label_array[$i];
			$element_array['value']	=	$row[$i];
			$element_array['type']	=	$datatype_array[$i];
			if( ($datatype_array[$i]=="date") || $datatype_array[$i]=="datetime"){
				if(strtotime($element_array['value'])<=0){
					$element_array['value']=null;
				}
			}
			$total_element[]		=	$element_array;
			if($i==1){
				$group_title	=	$row[$i];
			}
		}
		$block_element['offset']		=	++$offset;
		$block_element['group_title']	=	$group_title;
		$block_element['elements']		=	$total_element;
		unset($total_element);
		$return_array['elements'][]    =    $block_element;
	}
	return $return_array;
}

/**
 * @param string $lead_code
 * @param int $default_id
 * @param int $group_key_id
 * @param int $page_size
 * @param int $offset
 * @param int $app_user_id
 * @param int $ticket_id
 * @param string $sorttype
 *
 * @return string[string]
 */
function get_template_content($lead_code,$default_id=0,$group_key_id=0,$page_size=0,$offset=0,$app_user_id=0,$ticket_id=0,$sorttype=''){
    global $uid;
	$return_array	=	/*. (string[string]) .*/array();
	if($default_id==0){$default_id=1;}
	$sql_get_template_list	=	" select  GRT_ID from gft_reminder_template_master where  GRT_STATUS='A' ";
	if($group_key_id!=0){
		$sql_get_template_list .= " and GRT_ID='$group_key_id' ";
	}
	$sql_get_template_list .= " order by if(GRT_ORDER_NO=$default_id,-1,GRT_ORDER_NO) ";
	$res_template_list		=	execute_my_query($sql_get_template_list);
	
	while($row=mysqli_fetch_array($res_template_list)){
		$tem_id	=	(int)$row['GRT_ID'];
		$default_flag	=	false;
		if($tem_id==$default_id){
			$default_flag	=	true;
		}
		$block_element	=	/*. (string[string]) .*/array();
		switch($tem_id){
			case 1://Lead Details
				$element	=	get_customer_type_details($lead_code);
				$block_element['group_title']	=	'Lead Details';
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'';
				$block_element['show_by_default']	=$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	$element['elements'];
				break;
			case 2://Lead Source details
				$element	=	get_lead_source_details($lead_code);
				$block_element['group_title']	=	'Lead Source details';
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'';
				$block_element['show_by_default']	=$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	$element['elements'];
				break;
			case 3://Shop Details
				$element	=	get_customer_shop_details($lead_code);
				$block_element['group_title']	=	'Shop Details';
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'';
				$block_element['show_by_default']	=$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	$element['elements'];
				break;
			case 4://Contact details
				$element	=	get_lead_contact_detail($lead_code,$page_size,$offset,$uid);
				$block_element['group_title']	=	'Contact details';
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'customer_contacts';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['show_by_default']	=$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	$element['elements'];
				break;
			case 5://Presales History
				$element = get_visit_history_dtl($lead_code,$page_size,$offset,$default_flag);
				$block_element['group_title']	=	'Presales History';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'paginated_group';
				$block_element['show_by_default']=	$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();
			/*	
				$block_element['group_title']	=	$element['group_title'];
				$block_element['offset']		=	$offset+1;
				$block_element['elements']		=	$element['elements'];*/
				break;
			case 6://Call History
				$element = get_call_history_dtl($lead_code,$page_size,$offset,$default_flag);
				$block_element['group_title']	=	'Call History';
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'paginated_group';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['show_by_default']	=$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();
			/*	
				$block_element['offset']		=	$offset+1;
				$block_element['elements']		=	$element['elements']; */
				break;
			case 7://Support History
				$element = get_support_history_dtl($lead_code,$page_size,$offset,$default_flag,$ticket_id,$sorttype);
				$block_element['group_title']	=	'Support History';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'paginated_group';
				$block_element['show_by_default']	=$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();
			/*	
				$block_element['offset']		=	$offset+1;
				$block_element['elements']		=	$element['elements']; */
				break;
			case 8://Installation History
				$element	=	get_installation_history($lead_code,$page_size,$offset,$default_flag);
				$block_element['group_title']	=	'Installation History';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'paginated_group';
				$block_element['show_by_default']=	$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();

				/* 
				$block_element['offset']		=	$offset+1;
				$block_element['elements']		=	$element['elements']; */
				break;
			case 9://Quotation History
				$element = get_quotation_history($lead_code,$page_size,$offset,$default_flag);
				$block_element['group_title']	=	'Quotation History';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'paginated_group';
				$block_element['show_by_default']=	$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();
				/* 
				$block_element['offset']		=	$offset+1;
				$block_element['elements']		=	$element['elements']; */
				break;
			case 10://Proforma History
				$element = get_proforma_history($lead_code,$page_size,$offset,$default_flag);
				$block_element['group_title']	=	'Proforma History';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'paginated_group';
				$block_element['show_by_default']=	$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();
				/*
				$block_element['offset']		=	$offset+1;
				$block_element['elements']		=	$element['elements']; */
				break;
			case 11: //Receipt Details
				$element = get_receipt_dtl($lead_code,$page_size,$offset,$default_flag);
				$block_element['group_title']	=	'Receipt History';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'paginated_group';
				$block_element['show_by_default']=	$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();

				/* 
				$block_element['offset']		=	$offset+1;
				$block_element['elements']		=	$element['elements']; */
				break;
			case 12://Order History
				$element = get_order_history_dtl($lead_code,$page_size,$offset,$default_flag);
				$block_element['group_title']	=	'Order History';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'paginated_group';
				$block_element['show_by_default']=	$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();
				/* 
				$block_element['offset']		=	$offset+1;
				$block_element['elements']		=	$element['elements']; */
				break;
			case 13://Order Product Details
				$element = get_order_product_dtl($lead_code,$page_size,$offset,$default_flag);
				$block_element['group_title']	=	'Order Product Details';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'paginated_group';
				$block_element['show_by_default']=	$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();

				/* 
				$block_element['offset']		=	$offset+1;
				$block_element['elements']		=	$element['elements']; */
				break;
			case 14://Coupon History
				$element = get_coupon_history_dtl($lead_code,$page_size,$offset,$default_flag);
				$block_element['group_title']	=	'Coupon History';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'paginated_group';
				$block_element['show_by_default']	=$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();
/* 				
				$block_element['offset']		=	$offset+1;
				$block_element['elements']		=	$element['elements']; */
				break;
			case 15://Product Delivery History
				$element  = get_pd_history_dtl($lead_code,$page_size,$offset,$default_flag);
				$block_element['group_title']	=	'Product Delivery History';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'paginated_group';
				$block_element['show_by_default']	=$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();
/* 				
				$block_element['offset']		=	$offset+1;
				$block_element['elements']		=	$element['elements']; */
				break;
			case 16://SMS History
				$element = get_sms_history($lead_code,$page_size,$offset,$default_flag);
				$block_element['group_title']	=	'SMS History';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'paginated_group';
				$block_element['show_by_default']=	$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();
/*
				$block_element['offset']		=	$offset+1;
				$block_element['elements']		=	$element['elements']; */
				break;
			case 17://Customer Photo History
				$element = get_customer_photo_dtl($lead_code,4,$offset);
				$block_element['group_title']	=	'Customer Photos';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'customer_images';
				$block_element['show_by_default']=	$default_flag;
				$block_element['cust_id']		=	$lead_code;
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();
				break;
			case 18://Customer Chat History
				if($app_user_id!=0){
					$lead_codes	=	get_accessible_leads($app_user_id);
					$lead_code	=	implode(",", $lead_codes);
				}else{
					$block_element['cust_id']		=	$lead_code;
				}
				$element = get_customer_chat_dtl($lead_code,$page_size,$offset);
				$block_element['group_title']	=	'Chat History';
				$block_element['total_count']	=	$element['total_count'];
				$block_element['group_key']		=	$tem_id;
				$block_element['group_type']	=	'chat_history';
				$block_element['show_by_default']=	$default_flag;				
				$block_element['elements']		=	isset($element['elements'])?$element['elements']:array();
				break;
			default:
				break;
		}
		 if($group_key_id!=0){
			return $block_element;
		}
		$return_array[] = $block_element;
		unset($block_element);
	}
	return $return_array;
}
/**
 * @param int $followup_id
 *  
 * @return string[string]
 */
function get_followup_primary_detail($followup_id){
	$return_array		=	/*. (string[string]) .*/array();
	$query_followup=	" select GCF_LEAD_CODE as lead_code, GCF_ACTIVITY_REF as act_id,GCF_FOLLOWUP_ACTION as action_id from gft_cplead_followup_dtl where gcf_followup_id='$followup_id'";
	$res_followup	=	 execute_my_query($query_followup);
	if(mysqli_num_rows($res_followup)==1){
		$row		=	mysqli_fetch_array($res_followup);
		$return_array['lead_code']	=	$row['lead_code'];
		$return_array['act_id']	=	$row['act_id'];
		$return_array['action_id']	=	$row['action_id'];
	}
	return $return_array;
}
/**
 * @param int $id
 *
 * @return string[string]
 */
function get_appointment_primary_detail($id){
	$return_array		=	/*. (string[string]) .*/array();
	$query_activity=	" select GLD_LEAD_CODE as lead_code, GLD_ACTIVITY_ID as act_id, GLD_NEXT_ACTION as action_id from gft_activity where GLD_ACTIVITY_ID='$id' ";
	$res_activity	=	 execute_my_query($query_activity);
	if(mysqli_num_rows($res_activity)==1){
		$row		=	mysqli_fetch_array($res_activity);
		$return_array['lead_code']	=	$row['lead_code'];
		$return_array['act_id']	=	$row['act_id'];
		$return_array['action_id']	=	$row['action_id'];
	}
	return $return_array;
}
/**
 * @param int $activity_id
 * @param int $escalation_group_id
 * @param int $assigned_to_emp
 * @param string $edc_date
 * @param string $expected_resolution
 * @param string $description
 * @param int $assigned_by
 * @param string $lead_code
 * @param string $escalation_baton_wobbling
 * 
 * @return int
 */
function update_escalation_details($activity_id,$escalation_group_id,$assigned_to_emp,$edc_date,$expected_resolution,$description,$assigned_by,$lead_code,$escalation_baton_wobbling=''){	
	$assigned_by_name	=	'';
	$res_assign_by	=	execute_my_query("select  GEM_EMP_name,GEM_MOBILE from gft_emp_master where GEM_EMP_ID='$assigned_by'");
	if(mysqli_num_rows($res_assign_by)!=0){
		$row_assigned_by	=	mysqli_fetch_array($res_assign_by);
		$assigned_by_name	=	$row_assigned_by['GEM_EMP_name'];
	}	
	$assigned_to_name	=	'';
	$mobile_no			=	'';
	$description		=	remove_special_characters($description);
	$expected_resolution=	remove_special_characters($expected_resolution);
	$res_esc_group_name	=	execute_my_query("select GEG_NAME,gpg_support_mail_id,geg_product_group from gft_escalation_group_master ".
							" left join gft_product_group_master on (concat(gpg_product_family_code,'-',gpg_skew)=GEG_PRODUCT_GROUP) ".
							" where GEG_GROUP_ID='$escalation_group_id'");
	$summary 			= 	"Escalation - ";
	$prod_support_cc = '';
	$support_pcode = '10';
	$support_pskew = '01.0';
	if(mysqli_num_rows($res_esc_group_name)!=0){
		$row_get_esca_group	=	mysqli_fetch_array($res_esc_group_name);
		$summary 			= 	"Escalation on ".$row_get_esca_group['GEG_NAME'];
		$prod_support_cc	= $row_get_esca_group['gpg_support_mail_id'];
		$escalation_product_gp = $row_get_esca_group['geg_product_group'];
	    $prod_split = explode("-",$escalation_product_gp);
	    if(count($prod_split)==2) {
    	    $support_pcode = (isset($prod_split[0])?$prod_split[0]:$support_pcode);
    	    $support_pskew = (isset($prod_split[1])?$prod_split[1]:$support_pskew);
	    }
	}
	$res_assigned_emp	=	execute_my_query("select  GEM_EMP_name,GEM_MOBILE from gft_emp_master where GEM_EMP_ID='$assigned_to_emp'");
	if(mysqli_num_rows($res_assigned_emp)!=0){
		$row_assigned_emp	=	mysqli_fetch_array($res_assigned_emp);
		$assigned_to_name	=	$row_assigned_emp['GEM_EMP_name'];
		$mobile_no			=	$row_assigned_emp['GEM_MOBILE'];;
	}
	$edc_date			=	date('Y-m-d', strtotime($edc_date));
	$cust_name = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
	$GCH_COMPLAINT_CODE=158;
	$ticket_id = insert_support_entry($lead_code, $support_pcode, $support_pskew, '', '', $assigned_by, '0', $summary,
			$GCH_COMPLAINT_CODE, 'T3',date('Y-m-d H:i:s'),$edc_date,$assigned_to_emp,null,'4',$description,false,
			'',$expected_resolution,null,'3',true,'','','','','',$escalation_baton_wobbling);
	//Map activity id and ticket id
	$message=<<<END
<br><table><tr><td height="30" style="font-weight:normal"><span style="background:#DDDDDD;font-weight:normal;padding-top:8px;padding-bottom:8px;padding-left:8px;padding-right:8px;border-radius:3px 3px 0 0;-moz-border-radius:3px 3px 0 0;-webkit-border-radius:3px 3px 0 0;">Escalation Details </span></td></tr>
				        <tr><td style="font-weight:normal"><table width="100%" cellspacing="0" cellpadding="6" border="0" style="background:#f2f2f2;border:1px solid #d8d8d8;font:bold 12px Verdana, Arial, Helvetica, sans-serif;">
				        <tr><td width="45%" style="border-bottom:1px solid #d8d8d8; border-right:1px solid #d8d8d8;text-align:right">Escalation Group :</td><td width="58%" style="border-bottom:1px solid #d8d8d8;">$summary</td></tr>
						<tr><td style="border-bottom:1px solid #d8d8d8; border-right:1px solid #d8d8d8;text-align:right">Assigned by :</td><td style="border-bottom:1px solid #d8d8d8;">$assigned_by_name</td></tr>
						<tr><td style="border-bottom:1px solid #d8d8d8; border-right:1px solid #d8d8d8;text-align:right">Descrption :</td><td style="border-bottom:1px solid #d8d8d8;">$description </td></tr>
						<tr><td style="border-bottom:1px solid #d8d8d8; border-right:1px solid #d8d8d8;text-align:right">Expected Resoluion :</td><td style="border-bottom:1px solid #d8d8d8;">$expected_resolution</td></tr>
						<tr><td style="border-bottom:1px solid #d8d8d8; border-right:1px solid #d8d8d8;text-align:right">EDC :</td><td style="border-bottom:1px solid #d8d8d8;">$edc_date</td></tr>
						<tr><td style="border-bottom:1px solid #d8d8d8; border-right:1px solid #d8d8d8;text-align:right">Customer Code :</td><td style="border-bottom:1px solid #d8d8d8;">$lead_code</td></tr>
						<tr><td style="border-bottom:1px solid #d8d8d8; border-right:1px solid #d8d8d8;text-align:right">Customer Name :</td><td style="border-bottom:1px solid #d8d8d8;">$cust_name</td></tr>
						<tr><td style="border-bottom:1px solid #d8d8d8; border-right:1px solid #d8d8d8;text-align:right">Support Ticket ID:</td><td style="border-bottom:1px solid #d8d8d8;">$ticket_id</td></tr>
						</table>
END;
	$db_sms_content_config=array('Employee_Name' => array($assigned_to_name),
		'Mail_Content' => array($message));
	$cc_emp_list	=	array();
	$query_escalation_emp=	" select es.GEM_EMP_ID from gft_escalation_master es ".
							" inner join gft_emp_master em on(em.gem_emp_id=es.GEM_EMP_ID) ".
							" where es.GEM_GROUP_ID='$escalation_group_id' and  es.GEM_STATUS='A' AND em.GEM_STATUS='A' ";
	$res_all_emp_list	=	execute_my_query($query_escalation_emp);
	while($emp_row=mysqli_fetch_array($res_all_emp_list)){
		$cc_emp_list[]=$emp_row['GEM_EMP_ID'];
	}
	$cc_emp_list[]=$assigned_by;
	insert_activity_support_in_hdr($activity_id,$ticket_id);
	send_formatted_mail_content($db_sms_content_config,8,195,array($assigned_to_emp),null,null, $cc_emp_list,array($prod_support_cc));
	$sms_content	=	get_formatted_content(array('Customer_Id'=>array($lead_code),'Customer_Name'=>array($cust_name),'Ticket_Id'=>array($ticket_id),'EDC'=>array($edc_date),'Escalation_Category'=>array($summary)),162);
	entry_sending_sms($mobile_no,$sms_content,162,$assigned_to_emp,1,$assigned_by,0,null);
	return $ticket_id;
}

/**
 * @param string $metric_id
 * @param string $achieved
 * @param int $plan
 * @param int $month_start_deviation
 * @param int $month_end_deviation
 * @param string $day
 * @param string $ret_type
 *
 * @return string
 */
function get_metric_status_color($metric_id,$achieved,$plan,$month_start_deviation,$month_end_deviation,$day='',$ret_type='flag'){
	$day = ($day=='')?date('d'):'';
	$decrease_rate = ($month_start_deviation - $month_end_deviation)/(30);
	$decrease_perc= round(($month_start_deviation-($decrease_rate*$day)),2);
	$actual_plan = round(($plan/30)*$day, 2);
	if($actual_plan<=0){
		$actual_plan = 1;
	}
	$deviation_percent = 100 - round( 100*((int)$achieved/$actual_plan),2);
	$diff_per = round($deviation_percent - $decrease_perc , 2);
	//booster during starting of month
	$boosted_diff = round($diff_per*$day/30,2);
	if($ret_type=='value'){
		return "$boosted_diff";
	}
	$amber_deviation_arr = explode('-',get_samee_const("Amber_Status_Range_For_App"));
	$green_allowable = isset($amber_deviation_arr[0])?$amber_deviation_arr[0]:0;
	$amber_allowable = isset($amber_deviation_arr[1])?$amber_deviation_arr[1]:0;
	$color = "R";
	if($boosted_diff <= $green_allowable){
		$color = "G";
	}else if($boosted_diff <= $amber_allowable){
		$color = "A";
	}
	return $color;
}
/**
 * @param string $uid
 * @param string $query_where
 * @param int $send_count
 * @param string $approval_type
 *
 * @return string
 */
function get_pending_pd_cm_approval_query($uid,$query_where='',$send_count=0,$approval_type=''){
	$where_query		=	"";
	$pd_approval_time_limit	=	get_samee_const("PD_CM_APPROVAL_TIME_LIMIT");
	if($pd_approval_time_limit=='' or $pd_approval_time_limit==0){
		$pd_approval_time_limit	=	(48*60*60);
	}else{
		$pd_approval_time_limit	=	($pd_approval_time_limit*60*60);
	}
	if (is_authorized_group_list($uid, array (54,36))) {//Annuity,PCS
		$where_query = " AND GOD_PDCM_EMP_ID=$uid  AND GAH_TRAINING_STATUS=4 ";
	}else if (is_authorized_group_list($uid, array (5))) {//Field Sales
		$where_query = " AND GAH_TRAINING_STATUS=4 AND (glh_lfd_emp_id=$uid) ";
		$emp_info	=	get_contact_dtls_of_group(array('54','36'));
		$annuity_ids=	array();
		if(count($emp_info)!=0){
			for($i=0;$i<count($emp_info);$i++){
				$annuity_ids[]	=	$emp_info[$i][1];
			}
		}
		if(trim(implode(',', $annuity_ids))!=''){
			$where_query .= " AND GOD_PDCM_EMP_ID not in(".implode(',', $annuity_ids).")";
		}
	}else if (is_authorized_group_list($uid, array (70))) {//PC
		$where_query = " AND GAH_TRAINING_STATUS=4 AND (lh.GLH_FIELD_INCHARGE=$uid)";
	}else{
		$where_query = " AND GAH_TRAINING_STATUS=4 AND (glh_lfd_emp_id=$uid)";
	}
	if($approval_type=='P'){ $where_query .= " AND GAH_CM_APPROVAL_STATUS!='N'";}
	if($approval_type=='N'){ $where_query .= " AND GAH_CM_APPROVAL_STATUS='N'";}
	$query_cm_approval	=	" select id,order_by,order_date, expiry_date, cust_id, cust_name, lead_status, prospect_status, cust_location, age, cust_emotion, approval_type, elapsed, GLE_PROSPECT_TYPE,'' as order_no,product_name,'' as amount,'' as status,'' comments  from ( ".
			" select GAH_REFFERNCE_ORDER_NO AS id, em.gem_emp_name as order_by,GOD_ORDER_DATE AS order_date,'NA' AS expiry_date,GAH_LEAD_CODE AS cust_id,GLH_CUST_NAME AS cust_name,GLH_STATUS as lead_status, GLH_PROSPECTS_STATUS as prospect_status,".
			" GLH_CUST_STREETADDR2 AS cust_location, ($pd_approval_time_limit- TIMESTAMPDIFF(SECOND, GAH_DATE_TIME, now())) as age, GLE_CUST_EMOTION AS cust_emotion, 'pd_cm_approval' as approval_type, ".
			" if(($pd_approval_time_limit- TIMESTAMPDIFF(SECOND, GAH_DATE_TIME, now()))>=0,0,1) as elapsed,GLE_PROSPECT_TYPE,'' as order_no, '' as product_name, ".
			" GROUP_CONCAT(distinct IF(GCD_SIGNED_OFF!='R',GCD_SIGNED_OFF,'')) GCD_SIGNED_OFF, GCD_IS_ECOUPON from  gft_audit_hdr ah ".
			" inner join gft_lead_hdr lh on(lh.glh_lead_code=ah.GAH_LEAD_CODE) ".
			" inner join gft_order_hdr od on(od.god_order_no=substr(GAH_REFFERNCE_ORDER_NO,1,15))".
			" inner join gft_emp_master em on(od.GOD_PDCM_EMP_ID=em.gem_emp_id)".
			" inner join gft_coupon_distribution_dtl cd on(cd.GCD_ORDER_NO=GAH_REFFERNCE_ORDER_NO)".
			" left join gft_cp_info ci on(ci.CGI_LEAD_CODE=od.GOD_LEAD_CODE)".
			" LEFT JOIN gft_lead_hdr_ext lhe on(lhe.gle_lead_code=lh.glh_lead_code)".
			" where  GAH_LAST_AUDIT='Y' and GOD_ORDER_STATUS='A'  $where_query ".
			" and (GOD_PDCM_EMP_ID=9999  and GOD_ORDER_AMT=0)!=true $query_where group by GAH_REFFERNCE_ORDER_NO order by age asc".
			" ) pd_approval where if(GCD_IS_ECOUPON=1,TRIM(BOTH ',' FROM GCD_SIGNED_OFF)='','1')";
	if($send_count==1){
		$result_count	=	execute_my_query("SELECT COUNT(*)total_count FROM ($query_cm_approval) pd_count");
		$row_count		=	mysqli_fetch_array($result_count);
		return $row_count['total_count'];
	}
	return $query_cm_approval;
}
/**
 * @param string $employee_id
 * 
 * @return string
 */
function get_employee_role_based_card($employee_id){
	$return_value		=	"";
	$user_role_details	=	get_role_emp_id($employee_id);	
	$emp_role_id		=	isset($user_role_details[0])?$user_role_details[0]:'';
	if($emp_role_id!=''){
		$result_cards	=	execute_my_query("select group_concat(GRC_CARD) as cards from gft_role_app_card where GRC_STATUS=1 and GRC_ROLE=$emp_role_id");
		$row_cards		=	mysqli_fetch_array($result_cards);
		$return_value	=	$row_cards['cards'];
	}	
	return $return_value;
}
/**
 * @param string $employee_id
 * 
 * @return string
 */
function get_territory_query_for_sales_person_using_emp_id($employee_id){
	$territory_join_query="";
	$zone_id=0;$region_id=0;$area_id=0;$terr_id=0;
	$zone_list=get_zone($employee_id);
	$zone_id=((count($zone_list)==1)?(int)$zone_list[0][0]:$zone_id);
	$region_list=get_region($employee_id,$zone_id);
	$region_id=((count($region_list)==1)?(int)$region_list[0][0]:$region_id);
	$area_list=get_area_in($employee_id,$region_id);
	$area_id=((count($area_list)==1)?(int)$area_list[0][0]:$area_id);
	$territory_list=get_territory($employee_id,$zone_id,$region_id,$area_id);
	if($terr_id!=0 or $area_id!=0 or $region_id!=0 or $zone_id!=0){
		$territory_join_query=  get_query_area_mapping($terr_id,$area_id,$region_id,$zone_id);
		
	}
	return $territory_join_query;
}
/**
 * @param string $employee_id
 * @param string $from_date
 * @param string $to_date
 * 
 * @return mixed[]
 */
function get_ind_hq_prospects_count($employee_id,$from_date,$to_date) {
	$ind_hq_prospect = 	"select GLH_PROSPECT_BY, sum(if(GLH_LEAD_TYPE=1,1,0)) ind_prospect,sum(if(GLH_LEAD_TYPE=3,1,0)) hq_prospect".
			" from gft_lead_hdr lh WHERE GLH_LEAD_TYPE IN(1,3) AND GLH_PROSPECT_BY='$employee_id'".
			" and GLH_PROSPECT_ON >='$from_date' and GLH_PROSPECT_ON <='$to_date' group by GLH_PROSPECT_BY";
	$result_prospect=execute_my_query($ind_hq_prospect);
	$hq_prospect_count = $ind_prospect_count = 0;
	if(mysqli_num_rows($result_prospect)>0 && $row_prospect=mysqli_fetch_array($result_prospect)){
		$ind_prospect_count=(int)$row_prospect['ind_prospect'];
		$hq_prospect_count=(int)$row_prospect['hq_prospect'];
	}
	$result = array();
	$result['ind'] = $ind_prospect_count;
	$result['hq'] = $hq_prospect_count;
	return $result;
}
/**
 * @param string $employee_id
 * @param string $from_date
 * @param string $to_date
 * @param boolean $with_partner
 * @return string[string]
 */
function get_daily_achieved_array($employee_id,$from_date,$to_date,$with_partner=false) {
	$achieved = /*.(string[string]).*/array();
	$coupon_date_cond = $coupon_date_cond1 = $install_date_cond = $install_date_cond1 = $visit_date_cond =$new_coupon_date_cond1 ='';
	if($from_date!='') {
		$coupon_date_cond .= " and GCD_RECEIVED_DATE>='$from_date' ";
		$coupon_date_cond1 .= " and gcr_created_date>='$from_date' ";
		$install_date_cond .= " and gai_created_datetime>='".db_date_format($from_date)." 00:00:00' ";
		$install_date_cond1 .= " and GCL_CREATED_DATE>='".db_date_format($from_date)." 00:00:00' ";
		$visit_date_cond .= " and gld_visit_date>='".db_date_format($from_date)."' ";
		$new_coupon_date_cond1.="and GPT_ACTIVITY_ON>='".db_date_format($from_date)." 00:00:00'";
	} if($to_date!='') {
		$coupon_date_cond .= " and GCD_RECEIVED_DATE<='$to_date' ";
		$coupon_date_cond1 .= " and gcr_created_date<='$to_date' ";
		$install_date_cond .= " and gai_created_datetime<='".db_date_format($to_date)." 23:59:59' ";
		$install_date_cond1 .= " and GCL_CREATED_DATE<='".db_date_format($to_date)." 23:59:59' ";
		$visit_date_cond .= " and gld_visit_date<='".db_date_format($to_date)."' ";
		$new_coupon_date_cond1 .= " and GPT_ACTIVITY_ON<='".db_date_format($to_date)." 23:59:59' ";
	}
	$joint_partner_cond = $partner_gld_emp_cond = '';
	if($with_partner) {
		$joint_partner_cond .= " and gjv_joint_emp_id>=7000 ";
		$partner_gld_emp_cond .= " and gld_emp_id>=7000 and gld_emp_id!=9999 ";
	}
	$daily_achieved_qry =<<<QUERY
select coll_amt,hrs,app_installed,joint_act_count,ind_coll,hq_coll,ind_prospect,hq_prospect,indep_order_amt,ind_ord_cnt,chain_order_amt,demo_cnt,tot_coupon_mins   
from gft_emp_master em1 

left join (select cgi_incharge_emp_id partner_coll_emp,round(sum(grd_receipt_amt)) coll_amt from gft_receipt_dtl 
inner join gft_emp_master em2 on (em2.gem_emp_id=grd_emp_id) 
inner join gft_lead_hdr lh on (GLH_LEAD_CODE=grd_lead_code) 
inner join gft_cp_info cp ON(cp.CGI_LEAD_CODE=grd_lead_code)  
where GRD_CHECKED_WITH_LEDGER='Y' and GRD_REFUND_AMT=0 AND cgi_incharge_emp_id='$employee_id' and 
GRD_CHEQUE_CLEARED_DATE>= '$from_date' and GRD_CHEQUE_CLEARED_DATE<= '$to_date' group by cgi_incharge_emp_id) partner_coll_t 
on (em1.gem_emp_id=partner_coll_emp)

left join (select $employee_id emp,sum(hrs) hrs from (select sum(gcd_coupon_hours) hrs from gft_coupon_distribution_dtl 
where ((GCD_IS_ECOUPON=1 and gcd_signed_off='R') or (GCD_IS_ECOUPON=0 and gcd_signed_off='Y')) and gcd_coupon_hours is not null 
$coupon_date_cond and GCD_RECEIVED_BY='$employee_id' group by GCD_COUPON_NO union all select sum(gcr_coupon_hours) hrs 
from gft_complementary_coupon_request where 1 $coupon_date_cond1 and gcr_emp_id='$employee_id' and gcr_request_status='I')t) coupon_hrs_t on 
(em1.gem_emp_id=emp)

left join(select $employee_id emp, SUM(GPT_ACTIVITY_TIME_SPENT) tot_coupon_mins  from gft_pd_training_feedback_dtl 
left join gft_cust_imp_ms_current_status_dtl on(GPT_TRAINING_ID=GIMC_COMPLAINT_ID)
left join gft_product_delivery_hdr ON(GIMC_PD_REF_ID=GPD_ID	)
where GPT_ACTIVITY_BY=$employee_id $new_coupon_date_cond1 AND GPT_IS_SPOC=1  AND ((GPD_ORDER_TYPE in(2,3) AND GIMC_PD_REF_ID>0) || (GIMC_MS_ID=3) ) GROUP BY GPT_ACTIVITY_BY) gcm_coupon_hrs_t on
(em1.gem_emp_id=gcm_coupon_hrs_t.emp)

left join (select $employee_id installed_emp,count(distinct concat(gai_mobile,'-',gai_app_pcode)) app_installed from 
         gft_app_installed_dtl where 1 $install_date_cond and gai_installed_emp='$employee_id' ) install_t on 
(em1.gem_emp_id=installed_emp)

left join (select $employee_id act_emp,count(*) joint_act_count from (select gld_activity_id from gft_activity 
join gft_joint_visit_dtl on (gld_activity_id=gjv_activity_id $joint_partner_cond) where gld_emp_id='$employee_id' 
$visit_date_cond union select gld_activity_id from gft_activity join gft_joint_visit_dtl on 
(gjv_joint_emp_id='$employee_id' and gld_activity_id=gjv_activity_id) where (1) $partner_gld_emp_cond $visit_date_cond) t) 
joint_act_t on (em1.gem_emp_id=act_emp)

left join (select GLD_AGILE_CONTRIBUTOR as demo_emp,count(distinct GLD_ACTIVITY_ID) as demo_cnt from gft_activity 
where GLD_AGILE_CONTRIBUTOR='$employee_id' and GLD_VISIT_NATURE in (2,48) and GLD_ACTIVITY_STATUS_ID=2 
$visit_date_cond group by GLD_AGILE_CONTRIBUTOR) demo_t on (em1.gem_emp_id=demo_emp) 

left join (select grd_emp_id coll_emp,sum(if(glh_lead_type=1,round(grd_receipt_amt),0)) ind_coll, 
sum(if(glh_lead_type in (3,13),round(grd_receipt_amt),0)) hq_coll from gft_receipt_dtl join gft_lead_hdr on 
(glh_lead_code=grd_lead_code) where grd_emp_id='$employee_id' and GRD_CHECKED_WITH_LEDGER='Y' and 
GRD_CHEQUE_CLEARED_DATE >='$from_date' and GRD_CHEQUE_CLEARED_DATE <='$to_date') coll_t on (coll_emp=em1.gem_emp_id)

left join (select GLH_PROSPECT_BY pros_by, sum(if(GLH_LEAD_TYPE=1,1,0)) ind_prospect,sum(if(GLH_LEAD_TYPE=3,1,0)) hq_prospect 
from gft_lead_hdr lh WHERE GLH_LEAD_TYPE IN(1,3) AND GLH_PROSPECT_BY='$employee_id' and GLH_PROSPECT_ON >='$from_date' and 
GLH_PROSPECT_ON <='$to_date' group by GLH_PROSPECT_BY) pros_t on (pros_by=em1.gem_emp_id)

left join (select if(em4.gem_emp_id=9999,em5.gem_emp_id,em4.gem_emp_id) ord_emp_id,sum(if(lh.glh_lead_type='1',
round(gop_sell_amt,0),0)) indep_order_amt, count(distinct if(lh.glh_lead_type='1',GOD_ORDER_NO,null)) ind_ord_cnt, sum(if(lh.glh_lead_type in ('3','13'),round(gop_sell_amt),0)) chain_order_amt 
from gft_order_hdr join gft_order_product_dtl on (god_order_no=gop_order_no)
join gft_lead_hdr lh on(lh.GLH_LEAD_CODE=GOD_LEAD_CODE and lh.glh_lead_type in (1,3,13)) 
join gft_emp_master em4 on (em4.gem_emp_id=god_emp_id) left join gft_collection_receipt_dtl 
on (GCR_ORDER_NO=GOD_ORDER_NO and GOD_EMP_ID=9999) left join gft_receipt_dtl on (GRD_RECEIPT_ID=GCR_RECEIPT_ID) 
join gft_emp_master em5 on (em5.gem_emp_id=ifnull(GRD_EMP_ID,god_emp_id)) 
where god_order_status='A' and GOD_ORDER_DATE between '$from_date' and '$to_date' 
group by ord_emp_id having ord_emp_id='$employee_id') ord_t on (ord_emp_id=em1.gem_emp_id)
		
where em1.gem_emp_id='$employee_id'
QUERY;
	$query_res = execute_my_query($daily_achieved_qry);
	while($row = mysqli_fetch_array($query_res)) {
		$tot_coupon_mins = (int)$row['tot_coupon_mins'];
		$tot_ecoupon_hrs = (int)$row['hrs'];
		$coupon_hrs = "$tot_ecoupon_hrs hrs";
		if($tot_coupon_mins>0){
			$tot_hrs = (int)($tot_coupon_mins/60);
			$tot_mins = ($tot_coupon_mins%60);
			$coupon_hrs = ($tot_ecoupon_hrs+$tot_hrs)." hrs";
			if($tot_mins>0){
				$coupon_hrs.= " $tot_mins mins";
			}
		}
		$achieved['partner_collection'] 	  = $row['coll_amt'];
		$achieved['coupon_hrs']				  = $coupon_hrs;
		$achieved['apps_installed']			  = (int)$row['app_installed'];
		$achieved['partner_joint_activities'] = (int)$row['joint_act_count'];
		$achieved['ind_coll']			 	  = $row['ind_coll'];
		$achieved['chain_coll']				  = $row['hq_coll'];
		$achieved['ind_prospect']			  = (int)$row['ind_prospect'];
		$achieved['hq_prospect']			  = (int)$row['hq_prospect'];
		$achieved['indep_order']			  = (int)$row['indep_order_amt'];
		$achieved['indep_order_cnt']          = (int)$row['ind_ord_cnt'];
		$achieved['hq_order']			      = (int)$row['chain_order_amt'];
		$achieved['demo_count']			      = (int)$row['demo_cnt'];
	}
	return $achieved;
}
/**
 * @param string $employee_id
 * @param string $from_date
 * @param string $to_date
 * @param string $window
 *
 * @return string[int][string]
 */
function get_execution_cards($employee_id, $from_date, $to_date, $window=''){
	global $receive_arg;
	$date_arr = explode('-', $from_date);
	$ret_arr	=	array();
	$where_query_for_cards	=	"";
	$year = $date_arr[0];
	$month = $date_arr[1];
	$ind_prospect_count=0;
	$hq_prospect_count=0;
	$partner_coll_amt = 0;
	$ind_coll = 0;
	$hq_coll = 0;
	$role_based_card	=	get_employee_role_based_card($employee_id);
	if($role_based_card!=''){
		$where_query_for_cards	=	" AND GSM_ID in($role_based_card)";
	}
	if($employee_id>7000){
		$where_query_for_cards	.=	" AND GSM_PARTNER_EXEC='Y' ";
	}
	$achieved_select = " select GDA_EMP_ID,GDA_METRIC_ID,sum(GDA_ACHIEVED) as achieved from gft_daily_achieved_metrics ".
			" where GDA_EMP_ID='$employee_id' and GDA_DATE >='$from_date' and GDA_DATE <='$to_date' group by GDA_EMP_ID,GDA_METRIC_ID ";
	$select_query = " select GEM_EMP_ID,GSM_ID,GSM_APP_DISPLAY_NAME,GTS_TARGET_VALUE,GTS_VALUE,ach.achieved, ".
			" GSM_MAX_DEVIATION_PERC,GSM_MIN_DEVIATION_PERC ".
			" from gft_emp_master ".
			" join gft_target_dtl on (GTD_TARGET_ID=GEM_TARGET_ID) ".
			" join gft_scorecard_metrics on (GSM_ID=GTD_METRIC_ID $where_query_for_cards) ".
			" left join ( ($achieved_select) ach ) on (ach.GDA_EMP_ID = GEM_EMP_ID and ach.GDA_METRIC_ID=GTD_METRIC_ID) ".
			" left join gft_target_scorecard_metrics on (GTD_METRIC_ID=GTS_METRIC_ID and GTS_EMP_ID=GEM_EMP_ID and GTS_MONTH='$month' and GTS_YEAR='$year') ".
			" where GEM_EMP_ID='$employee_id' order by GSM_ORDER_BY ";
	$res_set = execute_my_query($select_query);
	if(mysqli_num_rows($res_set)==0){
		sendErrorWithCode($receive_arg,"Target Not Mapped For You. Contact Sales Coordinator",HttpStatusCode::BAD_REQUEST);
		exit;
	}
	$achieved_metrics = /*.(string[string]).*/get_daily_achieved_array($employee_id,$from_date,$to_date,true);
	while($row_data = mysqli_fetch_array($res_set)){
		$achieved = (int)$row_data['achieved'];
	switch ($row_data['GSM_ID']) {
			case '83':
				$achieved = $achieved_metrics['ind_prospect'];break;
			case '84':
				$achieved = $achieved_metrics['hq_prospect'];break;
			case '89': //Partner colletion amount
				$achieved = $achieved_metrics['partner_collection'];break;
			case '88':
				$achieved = $achieved_metrics['chain_coll'];break;
			case '31':
				$achieved = $achieved_metrics['ind_coll'];break;
			case '99':
				$achieved = $achieved_metrics['partner_joint_activities'];break;
			case '100':
				$achieved = $achieved_metrics['apps_installed'];break;
			case '101':
				$achieved = $achieved_metrics['coupon_hrs'];break;
			case '12':
				$achieved = $achieved_metrics['indep_order'];break;
			case '59':
				$achieved = $achieved_metrics['hq_order'];break;
			case '6' :
				$achieved = $achieved_metrics['demo_count'];break;
			case '11' :
			    $achieved = $achieved_metrics['indep_order_cnt'];break;
			default: break;
		}
		$plan_val	 =	$row_data['GTS_VALUE'];
		$target_val	 =	$row_data['GTS_TARGET_VALUE'];
		$month_start =	(int)$row_data['GSM_MAX_DEVIATION_PERC'];
		$month_end	 =	(int)$row_data['GSM_MIN_DEVIATION_PERC'];
		$card_arr['type'] = "highlight";
		$card_arr['label'] = number_to_abbreviated_form($plan_val)."/".number_to_abbreviated_form($target_val);
		if($window=='mtd'){
			$status_color = get_metric_status_color($row_data['GSM_ID'],$achieved,$plan_val,$month_start,$month_end);
			$card_arr['status'] = $status_color;
		}
		$card_arr['text'] = number_to_abbreviated_form("$achieved");
		$card_arr['summary'] = $row_data['GSM_APP_DISPLAY_NAME'];
		$card_arr['key'] = $row_data['GSM_ID'];
		$ret_arr[] = $card_arr;
	}
	return $ret_arr;
}

/**
 * @param int $uid
 * @param string $from_date
 * @param string $to_date
 * @param string $window
 * @param string $report_type
 *
 * @return mixed[string]
 */
function get_planned_unplanned_dtl($uid,$from_date,$to_date,$window='',$report_type='sales'){
	$return_array	=	array();
	$sql_pc_training	=	"";
	if($report_type=='pc'){
	$sql_pc_training=	" union all select 'Planned' act_type, (GITC_WORKED_DURATION)  as GLD_VISIT_IN_MIN ".
						" from gft_customer_support_dtl hdr  ".
						" join gft_cust_imp_task_current_status_dtl td on(hdr.GCD_COMPLAINT_ID = GITC_COMPLAINT_ID) ". 
						" WHERE (GCD_ACTIVITY_DATE>='$from_date 00:00:00' and GCD_ACTIVITY_DATE<='$to_date 23:59:59') and ". 
						" (GITC_DATE>='$from_date' and GITC_DATE<='$to_date') and GCD_PROCESS_EMP=$uid group by GCD_COMPLAINT_ID, GITC_TASK_ID";
	}
	$sql_time_spent	=	" select act_type, sum(GLD_VISIT_IN_MIN) time_spent from ( ".
			" select if(gld_reffer_id=0 or isnull(gld_reffer_id), 'Unplanned', 'Planned') act_type,GLD_VISIT_IN_MIN   from ".
			" (gft_activity act, gft_emp_master em ) ".
			" inner join gft_lead_hdr lh on (GLH_LEAD_CODE=gld_lead_code) ".
			" inner join gft_activity_master T1 on(T1.GAM_ACTIVITY_ID=act.GLD_VISIT_NATURE) ".
			" left join gft_customer_contact_dtl ccd on (GCC_LEAD_CODE=GLH_LEAD_CODE)  ".
			" left join gft_joint_visit_dtl jv on (act.gld_activity_id=jv.GJV_ACTIVITY_ID ) ".
			" left join gft_joint_visit_dtl jvemp on (act.gld_activity_id=jvemp.GJV_ACTIVITY_ID) ".
			" where gld_emp_id=em.gem_emp_id and GLH_LEAD_CODE!='' and GLD_VISIT_DATE >= '$from_date' and ".
			" GLD_VISIT_DATE <= '$to_date'  and glh_lead_type!='8' and gld_visit_date >='$from_date' ".
			" and gld_visit_date <= '$to_date' and (em.gem_emp_id='$uid' or jvemp.GJV_JOINT_EMP_ID='$uid' )  ".
			" group by gld_activity_id,gld_emp_id,gld_lead_code $sql_pc_training ) plans group by act_type ";
	$res_time_spent	=	execute_my_query($sql_time_spent);
	$planned	=	0;
	$unplanned	=	0;
	$total_time	=	0;
	while($row=mysqli_fetch_array($res_time_spent)){
		if($row['act_type']=='Planned'){
			$planned	=	(int)$row['time_spent'];
			$total_time	=	$total_time+$planned;
		}
		if($row['act_type']=='Unplanned'){
			$unplanned	=	(int)$row['time_spent'];
			$total_time	=	$total_time+$unplanned;
		}
	}
	if($total_time==0){
		$total_time=1;
	}
	$planned	=	round(($planned/$total_time)*100);
	$unplanned	=	round(($unplanned/$total_time)*100);
	$return_array['type']	=	"highlight";
	$return_array['label']	=	"Planned Vs Unplanned";
	$return_array['text']	=	"$planned% : $unplanned%";
	if($window=='mtd'){
		$val_diff = $planned - $unplanned;
		if($val_diff < 0){
			$val_diff = $unplanned - $planned;
		}
		$color = "R";
		if($val_diff <= 20){
			$color = "G";
		}else if($val_diff <= 40){
			$color = "A";
		}
		$return_array['status']	=	$color;
	}
	$return_array['summary']=	"";
	if($unplanned < 40){
		$return_array['summary']=	"You may want to spend more time in building your pipeline";
	}
	return $return_array;
}

/**
 * @param int $uid
 * @param string $from_date
 * @param string $to_date
 * @param string $window
 *
 * @return string[string]
 */
function get_pending_pd_cm_approval_dtl($uid,$from_date,$to_date,$window=''){	
	$return_array	=	/*. (string[string]) .*/array();
	$query_cm_approval	=	/*. (int) .*/get_pending_pd_cm_approval_query($uid,"",1,"P");	
	$return_array['type']	=	"highlight";
	$return_array['label']	=	"Pending PD CM Approval";
	$return_array['text']	=	"$query_cm_approval";	
	$return_array['summary']=	"";	
	return $return_array;
}
/**
 * @param int $uid
 * @param string $from_date
 * @param string $to_date
 * @param string $window
 *
 * @return mixed[string]
 */
function get_pending_pd_cm_escalation_dtl($uid,$from_date,$to_date,$window=''){
	$return_array	=	array();
	$query_cm_approval	=	/*. (int) .*/get_pending_pd_cm_approval_query($uid,"",1,"N");
	$return_array['type']	=	"highlight";
	$return_array['label']	=	"PD CM Escalation";
	$return_array['text']	=	"$query_cm_approval";
	$return_array['summary']=	"";
	return $return_array;
}
/**
 * @param string $uid
 * @param string $window
 * @param string $from_date
 * @param string $to_date
 * @param string $report_type
 *
 * @return string[string]
 */
function get_work_efficiency($uid,$window='',$from_date='',$to_date='',$report_type='sales'){
	$return_array	=	/*. (string[string]) .*/array();
	$select_appoinment_field	=	"";
	$select_followup_field		=	"";
	$select_support_field		=	"";
	$res_efficiency				=	"";
	$date_now	=	date("Y-m-d");
	$efficiency_list	=	array("Completed"=>0,"Pending"=>0,"Rescheduled"=>0,"Canceled"=>0,"Unattemt"=>0);
	$select_appoinment_field_month	=	" select if(GLD_NEXT_ACTION_DATE='$to_date' and (GLD_SCHEDULE_STATUS=1 OR GLD_SCHEDULE_STATUS=3),'Pending',".
			" if(GLD_NEXT_ACTION_DATE<'$to_date' and (GLD_SCHEDULE_STATUS=1 OR GLD_SCHEDULE_STATUS=3),'Unattemt',".
			" if(GLD_NEXT_ACTION_DATE>'$to_date' AND GLD_SCHEDULE_STATUS=3,'Rescheduled',".
			" if(GLD_SCHEDULE_STATUS=2,'Completed',if(GLD_SCHEDULE_STATUS=5,'Canceled','Others'))))) as app_status";

	$select_followup_field_month	=	" select if(GCF_FOLLOWUP_DATE='$to_date' and (GCF_FOLLOWUP_STATUS=1 OR GCF_FOLLOWUP_STATUS=3),'Pending', ".
			" if(GCF_FOLLOWUP_DATE<'$to_date' and (GCF_FOLLOWUP_STATUS=1 OR GCF_FOLLOWUP_STATUS=3),'Unattemt', ".
			" if(GCF_FOLLOWUP_DATE>'$to_date' AND GCF_FOLLOWUP_STATUS=3,'Rescheduled', ".
			" if(GCF_FOLLOWUP_STATUS=2,'Completed', if(GCF_FOLLOWUP_STATUS=5,'Canceled','Others'))))) as app_status ";
	$select_support_field_month=	" select if(GCD_SCHEDULE_DATE='$to_date' and gcd_status='T6','Pending', ".
									" if(GCD_SCHEDULE_DATE<'$to_date' and gcd_status='T6','Unattemt', ".
									" if(GCD_SCHEDULE_DATE>'$to_date' and gcd_status='T6','Rescheduled', ".
									" if(gcd_status='T1','Completed','Others')))) as app_status";
	if($window=='mtd'){
		$select_appoinment_field	=	$select_appoinment_field_month;
		$select_followup_field		=	$select_followup_field_month;
		$select_support_field		=	$select_support_field_month;

	}else if($window=='today'){
		$select_appoinment_field	=	" select if(GLD_SCHEDULE_STATUS=2,'Completed',".
				" if(GLD_SCHEDULE_STATUS=1 OR (GLD_SCHEDULE_STATUS=3 AND GLD_NEXT_ACTION_DATE='$from_date'),'Pending',".
				" if(GLD_SCHEDULE_STATUS=3,'Rescheduled',if(GLD_SCHEDULE_STATUS=5,'Canceled','Others')))) as app_status";

		$select_followup_field		=	" select if(GCF_FOLLOWUP_STATUS=2,'Completed', ".
				" if(GCF_FOLLOWUP_STATUS=1 OR (GCF_FOLLOWUP_STATUS=3 AND GCF_FOLLOWUP_DATE='$from_date'),'Pending', ".
				" if(GCF_FOLLOWUP_STATUS=3,'Rescheduled', if(GCF_FOLLOWUP_STATUS=5,'Canceled','Others')))) as app_status";
		
		$select_support_field	=	" select if(gcd_status='T1','Completed', if(gcd_status='T6' and GCD_SCHEDULE_DATE>'$from_date','Rescheduled',".
				" if(gcd_status='T6' ,'Pending','Others'))) as app_status";
	}else{
		$select_appoinment_field	=	" select if(GLD_NEXT_ACTION_DATE<='$to_date' AND (GLD_SCHEDULE_STATUS=1 OR GLD_SCHEDULE_STATUS=3),'Unattemt',".
				" if(GLD_NEXT_ACTION_DATE>'$to_date' AND GLD_SCHEDULE_STATUS=3,'Rescheduled', ".
				" if(GLD_SCHEDULE_STATUS=2,'Completed', ".
				" if(GLD_SCHEDULE_STATUS=5,'Canceled','Others')))) as app_status";

		$select_followup_field		=	" select if(GCF_FOLLOWUP_DATE<='$to_date' AND (GCF_FOLLOWUP_STATUS=1 OR GCF_FOLLOWUP_STATUS=3),'Unattemt', ".
				" if(GCF_FOLLOWUP_DATE>'$to_date' AND GCF_FOLLOWUP_STATUS=3,'Rescheduled', ".
				" if(GCF_FOLLOWUP_STATUS=2,'Completed', ".
				" if(GCF_FOLLOWUP_STATUS=5,'Canceled','Others')))) as app_status";
		$select_support_field	=	" select if(GCD_SCHEDULE_DATE<='$to_date' AND (gcd_status='T6'),'Unattemt',".
				" if(GCD_SCHEDULE_DATE>'$to_date' AND gcd_status='T6','Rescheduled', ".
				" if(gcd_status='T1','Completed','Others'))) as app_status";
		if(strtotime($date_now)==strtotime($to_date)){
			$select_appoinment_field	=	$select_appoinment_field_month;
			$select_followup_field		=	$select_followup_field_month;
			$select_support_field		=	$select_support_field_month;
		}

	}


	$sql_appoinment	=	" select  app_status, count(*) as total_count from ( $select_appoinment_field ".
			" from gft_activity join gft_activity_master on (GLD_NEXT_ACTION=GAM_ACTIVITY_ID) ".
			" INNER JOIN gft_lead_hdr lh on(GLD_LEAD_CODE=glh_lead_code AND GLH_LEAD_TYPE!=8)	".
			" INNER join gft_customer_status_master on gcs_code=glh_status ".
			" left join gft_schedule_status_master on(GLD_SCHEDULE_STATUS=GSS_STATUS_ID)	".
			" join gft_emp_master em on(gem_emp_id=GLD_EMP_ID) where (1) and ".
			" ((GLD_ACTUAL_NEXT_ACTION_DT>='$from_date' and GLD_ACTUAL_NEXT_ACTION_DT<='$to_date') OR ".
			" (GLD_NEXT_ACTION_DATE>='$from_date' and GLD_NEXT_ACTION_DATE<='$to_date'))  ".
			" AND GLD_EMP_ID=$uid order by GLD_SCHEDULE_STATUS) app_dtl group by app_status";

	$sql_followup	=	" select  app_status, count(*) as total_count from ($select_followup_field ".
			" from gft_cplead_followup_dtl INNER JOIN gft_lead_hdr lh on(GCF_LEAD_CODE=glh_lead_code AND GLH_LEAD_TYPE!=8) ".
			" INNER join gft_customer_status_master on gcs_code=glh_status ".
			" left join gft_schedule_status_master on(gcf_followup_status=GSS_STATUS_ID) ".
			" join gft_emp_master em on (gcf_assign_by=em.gem_emp_id) ".
			" join gft_emp_master em2 on (gcf_assign_to=em2.gem_emp_id) ".
			" join gft_activity_master on(gcf_followup_action=gam_activity_id) ".
			" where (1)  and ((GCF_ACTUAL_FOLLOWUP_DATE>='$from_date' and GCF_ACTUAL_FOLLOWUP_DATE<='$to_date') OR ".
			" (GCF_FOLLOWUP_DATE>='$from_date' and GCF_FOLLOWUP_DATE<='$to_date'))  AND gcf_assign_to=$uid ".
			" order by GCF_FOLLOWUP_STATUS ) app_dtl group by app_status";
	$sql_support	=	" select  app_status, count(*) as total_count from ( $select_support_field".
			" from gft_customer_support_hdr ".
			" inner join gft_customer_support_dtl on(GCH_COMPLAINT_ID=GCD_COMPLAINT_ID and GCH_LAST_ACTIVITY_ID=gcd_activity_id) ".
			" inner join gft_cust_imp_ms_current_status_dtl on(GCH_COMPLAINT_ID=GIMC_COMPLAINT_ID) ".
			" where GCD_PROCESS_EMP=$uid and (gcd_status='T1' OR gcd_status='T6') and ".
			" ((GCH_FIRST_SCHEDULE_DATE>='$from_date 00:00:00' and GCH_FIRST_SCHEDULE_DATE<='$to_date 23:59:59') OR ".
			" (GCD_SCHEDULE_DATE>='$from_date 00:00:00' and GCD_SCHEDULE_DATE<='$to_date 23:59:59'))".
			" ) app_dtl group by app_status";
	if($report_type=='pc'){
		$res_efficiency	=	execute_my_query($sql_appoinment." union all ".$sql_followup." union all ".$sql_support);
	}else{
		$res_efficiency	=	execute_my_query($sql_appoinment." union all ".$sql_followup);
	}
	$total_work		=	0;
	while($row=mysqli_fetch_array($res_efficiency)){
		if($row['app_status']!='Others'){
			$key	=	$row['app_status'];
			$val	=	(int)$row['total_count'];
			$efficiency_list["$key"]	=	$efficiency_list["$key"]+$val;
			$total_work		=	$total_work+$val;
		}
	}
	$pecentage_list	=	array();
	$single_item	=	array();
	if($total_work==0){
		$total_work=1;
	}
	foreach ($efficiency_list as $key1=>$value){
		if($key1=='Rescheduled'){
			$key1="Reschd.";
		}
		$single_item['label']	=	"$key1";
		$single_item['value']	=	round(($value/$total_work)*100)."%";
		$pecentage_list[]	=	$single_item;
	}
	
	if($window=='mtd'){
		$weightage = array("Completed"=>1,"Pending"=>-1,"Rescheduled"=>0.5,"Canceled"=>-0.5,"Unattemt"=>-1.5);
		$stat_val = ($efficiency_list['Completed'] * $weightage['Completed']) + ($efficiency_list['Pending'] * $weightage['Pending']) 
					+ ($efficiency_list['Rescheduled'] * $weightage['Rescheduled']) + ($efficiency_list['Canceled'] * $weightage['Canceled'])  
					+ ($efficiency_list['Unattemt'] * $weightage['Unattemt']);
		$color='R';
		if($stat_val > 60){
			$color = "G";
		}else if($stat_val > 20){
			$color = "A";
		}
		$return_array['status']	=	$color;
	}
	
	$return_array['type']	=	"list";
	$return_array['label']	=	"Quality Of Work ";
	$return_array['items']	=	$pecentage_list;
	return $return_array;
}

/**
 * @param string $uid
 * @param string $from_date
 * @param string $to_date
 * @param string $window
 *
 * @return string[string]
 */
function get_pcs_time_spent_summary($uid,$from_date,$to_date,$window){
	$single_item	=	array();
	$mtd_time    	=	array();
	$return_array	=	/*. (string[string]) .*/array();
	$query_mtd_time = "select GSM_APP_DISPLAY_NAME,time_spent.GSM_ID,time_spent.percent_value from gft_scorecard_metrics ".
				      "left join (select round((sum(GLD_TIME_DURATION)/tab.tot_time*100),2) as percent_value,GSM_ID from gft_activity ".
					  "join gft_commercial_classification_master on(GLD_COMMERCIAL_CLASSIFICATION=GCCM_ID) ".
					  "join gft_scorecard_metrics on(GSM_ID=GCCM_METRIC_ID) ".
					  "join (select sum(GLD_TIME_DURATION) as tot_time from gft_activity ".
					  "where GLD_VISIT_DATE>='$from_date' and GLD_VISIT_DATE<='$to_date' and GLD_EMP_ID=$uid) as tab ".
					  "where GLD_VISIT_DATE>='$from_date' and GLD_VISIT_DATE<='$to_date' and GLD_EMP_ID=$uid ".
					  "group by GCCM_METRIC_ID ) time_spent on(time_spent.GSM_ID=gft_scorecard_metrics.GSM_ID  ) ".
					  "where GSM_PCS_EXEC='Y' and GSM_TARGET_NEEDED='Y' ";
	$res_mtd_time =execute_my_query($query_mtd_time);
	while($row=mysqli_fetch_array($res_mtd_time)){
		$label = $row['GSM_APP_DISPLAY_NAME'];
		$value = "0%";
		if($row['percent_value']!=''){
			$value = $row['percent_value']."%";
		}
		$single_item['label']	=	"$label";
		$single_item['value']	=	"$value";
		$mtd_time[]			    =	$single_item;
	}
	$return_array['type']	=	"list";
	$return_array['label']	=	"Time Spent";
	$return_array['items']	=	$mtd_time;
	return $return_array;
}
/**
 * @param string $uid
 * @param string $from_date
 * @param string $to_date
 * @param string $window
 *
 * @return string[string]
 */
function get_pd_confirmation_dtl($uid,$from_date='',$to_date='',$window=''){
	$return_array	=	/*. (string[string]) .*/array();
	$single_item	=	array("label"=>"0","value"=>"0");
	$training_dtl	=	array();
	$where_qyr	=	"";
	$training_dtl[0]			=	$single_item;
	$sql_exp	=	" select count(*) total_training, sum(if(GCD_SIGNED_OFF='R',1,0)) confirmed".
					" from gft_coupon_distribution_dtl where GCD_IS_ECOUPON=1 AND ".
					" GCD_RECEIVED_BY='$uid' and GCD_SIGNED_OFF in('R','Y') AND GCD_DISTRIBUTE_FOR='C'";
	if($window=='mtd'){
		$where_qyr	=	$where_qyr." and GCD_RECEIVED_DATE>='$from_date' and GCD_RECEIVED_DATE<='$to_date' ";
	}else if($window=='today'){
		$where_qyr	=	$where_qyr." and GCD_RECEIVED_DATE>='$from_date'";
	}else{
		$where_qyr	=	$where_qyr." and GCD_RECEIVED_DATE>='$from_date' and GCD_RECEIVED_DATE<='$to_date' ";
	}
	$sql_exp	=	$sql_exp.$where_qyr." group by GCD_RECEIVED_BY";
	$res_exp	=	execute_my_query($sql_exp);
	while($row=mysqli_fetch_array($res_exp)){		
		$single_item['label']	=	$row['total_training'];
		$single_item['value']	=	$row['confirmed'];
		$training_dtl[0]			=	$single_item;
	}
	$return_array['type']	=	"list";
	$return_array['label']	=	"PD Completed Vs Confirmation";
	$return_array['items']	=	$training_dtl;
	return $return_array;
}
/**
 * @param string $uid
 * @param string $from_date
 * @param string $to_date
 * @param string $window
 *
 * @return string[string]
 */
function get_expenses_details($uid,$from_date='',$to_date='',$window=''){
	$return_array	=	/*. (string[string]) .*/array();
	$single_item	=	array();
	$expenes_dtl	=	array();
	$where_qyr	=	"";
	$sql_exp	=	" select GED_TYPE_EXPENSE,GET_NAME, ".
					" sum(GED_TOTAL_AMOUNT+GED_LODGING_AMT	+GED_BOADING_AMT	+GED_OTHERS_AMT + GED_FOOD_AMT) as total_amount from gft_expense_hdr ". 
					" inner join gft_expense_type_master ge on(GED_TYPE_EXPENSE=GET_ID) ".
					" where GED_EMP_ID=$uid ";
	if($window=='mtd'){		
		$where_qyr	=	$where_qyr." and GED_TO_DATE>='$from_date 00:00:00' and GED_TO_DATE<='$to_date 23:59:59' ";
	}else if($window=='today'){
		$where_qyr	=	$where_qyr." and GED_FROM_DATE>='$from_date 00:00:00'";
	}else{
		$where_qyr	=	$where_qyr." and GED_TO_DATE>='$from_date 00:00:00' and GED_TO_DATE<='$to_date 23:59:59' ";
	}
	$sql_exp	=	$sql_exp.$where_qyr." group by GED_TYPE_EXPENSE";
	$res_exp	=	execute_my_query($sql_exp);
	$exp_amt_arr= array(1=>0,2=>0,3=>0);
	while($row=mysqli_fetch_array($res_exp)){
	    $exp_amt_arr[(int)$row['GED_TYPE_EXPENSE']] = (int)$row['total_amount'];
	}
    $expenes_dtl =	array(
                       array('label'=>"Local",'value'=>"$exp_amt_arr[1]"),
                       array('label'=>"Ex-Station",'value'=>"$exp_amt_arr[2]"),
                       array('label'=>"Outstation",'value'=>"$exp_amt_arr[3]")
                    );
	$return_array['type']	=	"list";
	$return_array['label']	=	"Expenses";
	$return_array['items']	=	$expenes_dtl;
	return $return_array;
}
/**
 * @param string $uid
 *
 * @return string[string]
 */
function get_completed_pd_age($uid){
	$month	=	date("m");
	$year	=	date("Y");
	$return_array	=	/*. (string[string]) .*/array();
	$all_pd_age		=	array();
	$single_pd_age	=	array();
	$sql_hand	=	" select GEP_HANDOVER1_7,GEP_HANDOVER8_15,GEP_HANDOVER16_21,GEP_HANDOVER22_30,GEP_HANDOVER30 ".
					" from gft_emp_pd_summary  where GEP_EMP_ID=$uid AND  GEP_MONTH=$month AND GEP_YEAR=$year";
	$res_hand	=	execute_my_query($sql_hand);
	if(mysqli_num_rows($res_hand)>0){
		$row_hand	=	mysqli_fetch_array($res_hand);
		$single_pd_age['label']= "Fast";
		$single_pd_age['value']=$row_hand['GEP_HANDOVER1_7'];
		$all_pd_age[]		=	$single_pd_age;
		
		$single_pd_age['label']= "Normal";
		$single_pd_age['value']=$row_hand['GEP_HANDOVER8_15'];
		$all_pd_age[]		=	$single_pd_age;
		
		$single_pd_age['label']= "Slow";
		$single_pd_age['value']=$row_hand['GEP_HANDOVER16_21'];
		$all_pd_age[]		=	$single_pd_age;
		
		$single_pd_age['label']= "Very Slow";
		$single_pd_age['value']=$row_hand['GEP_HANDOVER22_30'];
		$all_pd_age[]		=	$single_pd_age;
		
		$single_pd_age['label']= "Expired";
		$single_pd_age['value']=$row_hand['GEP_HANDOVER30'];
		$all_pd_age[]		=	$single_pd_age;
	}else{
		$single_pd_age['label']= "Fast";
		$single_pd_age['value']="0";
		$all_pd_age[]		=	$single_pd_age;
		$single_pd_age['label']= "Normal";
		$all_pd_age[]		=	$single_pd_age;
		$single_pd_age['label']= "Slow";
		$all_pd_age[]		=	$single_pd_age;
		$single_pd_age['label']= "Very Slow";
		$all_pd_age[]		=	$single_pd_age;
		$single_pd_age['label']= "Expired";
		$all_pd_age[]		=	$single_pd_age;
		
	}
	$return_array['type']	=	"list";
	$return_array['label']	=	"Completed PD Age";
	$return_array['items']	=	$all_pd_age;
	return $return_array;
}
/**
 * @param string $uid
 *
 * @return string[int]
 */
function get_pending_pd_age($uid){
	$return_array	=	/*. (string[string]) .*/array();
	$return_arr		=	array();
	$all_pd_age		=	array();
	$all_pd_age1	=	array();
	$single_pd_age	=	array();
	$hold_field		=	" GAH_PENDING_IMP='No' or GAH_ORDER_AUDIT_STATUS='N' or GAH_I_ASSURE_STATUS='N' or GAH_TRAINING_STATUS='-1' or	GAH_RC_APPROVAL_STATUS='N' or GAH_CP_CM_APPROVAL_STATUS='N' or GAH_CM_APPROVAL_STATUS='N' ";
	$sql_pending	=	"select training_incharge_id,sum(if((datediff(curdate(),tdate))<=7 and tdate!='' and ($hold_field)!=true ,1,0)) as p7,
						sum(if((datediff(curdate(),tdate))>7  and (datediff(curdate(),tdate))<=15 and tdate!=''  and ($hold_field)!=true,1,0)) as p715,
						sum(if((datediff(curdate(),tdate))>15 and (datediff(curdate(),tdate))<=21 and tdate!='' and ($hold_field)!=true,1,0)) as p1521,
						sum(if((datediff(curdate(),tdate))>21 and (datediff(curdate(),tdate))<=30 and tdate!=''  and ($hold_field)!=true,1,0)) as p2130,
						sum(if((datediff(curdate(),tdate))>30 and tdate!=''  and ($hold_field)!=true ,1,0)) as p30,
						sum(if(((isnull(tdate) or tdate = '0000-00-00' or tdate='')  and ($hold_field)!=true)  ,1,0)) as ystart,sum(if((GAH_PENDING_IMP='No' or GAH_ORDER_AUDIT_STATUS='N' or GAH_I_ASSURE_STATUS='N' or GAH_TRAINING_STATUS='-1' or	GAH_RC_APPROVAL_STATUS='N' or GAH_CP_CM_APPROVAL_STATUS='N' or GAH_CM_APPROVAL_STATUS='N'),1,0)) AS tot_hold,ename,training_incharge,GLH_LEAD_CODE, GOD_ORDER_NO, GOD_ORDER_DATE  
						from (
							select em.gem_emp_name as training_incharge,em.gem_emp_id as training_incharge_id, GCD_AGE_START_DATE as tdate,
							gah_handover_status,GAH_PENDING_IMP,GAH_ORDER_AUDIT_STATUS,GAH_I_ASSURE_STATUS,GAH_TRAINING_STATUS,GAH_RC_APPROVAL_STATUS,GAH_CP_CM_APPROVAL_STATUS,GAH_CM_APPROVAL_STATUS,
							em.gem_emp_name as ename,glh_lead_code,GOD_ORDER_NO,GOD_ORDER_DATE, GAH_DATE_TIME
							from gft_lead_hdr lh join(
								SELECT GOD_ORDER_AMT,ohdr.GOD_ORDER_NO, opd.GOP_ORDER_NO, ohdr.GOD_INCHARGE_EMP_ID AS GOD_EMP_ID, ohdr.GOD_EMP_ID AS order_emp_id, ohdr.GOD_ORDER_DATE, 
								ohdr.god_order_splict,  if(ohdr.god_order_splict=1, cpod.GCO_CUST_CODE, ohdr.GOD_LEAD_CODE) CUST_LEAD_CODE, 
								if(ohdr.god_order_splict=1, cpod.GCO_PRODUCT_CODE,opd.GOP_PRODUCT_CODE ) GOP_PRODUCT_CODE, 
								if(ohdr.god_order_splict=1, cpod.GCO_SKEW,opd.GOP_PRODUCT_SKEW ) GOP_PRODUCT_SKEW, 
								if(ohdr.god_order_splict=1, cpod.GCO_FULLFILLMENT_NO, opd.GOP_FULLFILLMENT_NO) GOP_FULLFILLMENT_NO 
								FROM gft_order_hdr ohdr 
								join gft_order_product_dtl opd on(opd.GOP_ORDER_NO=ohdr.GOD_ORDER_NO)
								left join gft_cp_order_dtl cpod on (ohdr.GOD_LEAD_CODE =cpod.GCO_CP_LEAD_CODE AND cpod.GCO_ORDER_NO=opd.GOP_ORDER_NO 
								AND cpod.GCO_PRODUCT_CODE=opd.GOP_PRODUCT_CODE AND cpod.GCO_SKEW=opd.GOP_PRODUCT_SKEW AND ohdr.god_order_splict=1) 
								where GOD_IMPL_REQUIRED='Yes' and GOD_ORDER_STATUS='A' and (gco_ordered_date<=now()  or god_order_date<=now()) 
							) t on t.cust_lead_code = glh_lead_code
							join gft_product_master pm on (pm.gpm_product_code=t.gop_product_code and pm.gpm_product_skew=t.gop_product_skew and (GFT_SKEW_PROPERTY in (1,7,2,23,11) ))
							join gft_coupon_distribution_dtl on (GCD_ORDER_NO=concat(GOP_ORDER_NO,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GOP_FULLFILLMENT_NO)) 
							join gft_audit_hdr gah on (GAH_LEAD_CODE=lh.glh_lead_code and GAH_LAST_AUDIT='Y' and GAH_OPCODE=concat(GOP_ORDER_NO,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GOP_FULLFILLMENT_NO) 
							and GAH_OPCODE=gah_reffernce_order_no)
							join gft_emp_master em on(em.gem_emp_id=glh_field_incharge)
							where glh_lead_type!='8' and (god_emp_id=9999 and GOD_ORDER_AMT=0)!=true  AND gah_handover_status!='Y' AND em.gem_status='A'  AND glh_field_incharge=$uid
							group by glh_lead_code,god_order_no,gop_product_code,GOP_PRODUCT_SKEW,gop_fullfillment_no 
						 ) as t1 group by training_incharge_id";	
	$res_pending	=	execute_my_query($sql_pending);
	if(mysqli_num_rows($res_pending)>0){
		$row_hand	=	mysqli_fetch_array($res_pending);
		$single_pd_age['label']= "New";
		$single_pd_age['value']=$row_hand['p7'];
		$all_pd_age[]		=	$single_pd_age;

		$single_pd_age['label']= "Old";
		$single_pd_age['value']=$row_hand['p715'];
		$all_pd_age[]		=	$single_pd_age;

		$single_pd_age['label']= "Older";
		$single_pd_age['value']=$row_hand['p1521'];
		$all_pd_age[]		=	$single_pd_age;

		$single_pd_age['label']= "Expiring";
		$single_pd_age['value']=$row_hand['p2130'];
		$all_pd_age[]		=	$single_pd_age;

		$single_pd_age['label']= "Expired";
		$single_pd_age['value']=$row_hand['p30'];
		$all_pd_age[]		=	$single_pd_age;
		
		$single_pd_age['label']= "Yet to Start";
		$single_pd_age['value']=$row_hand['ystart'];
		$all_pd_age1[]		=	$single_pd_age;
		
		$single_pd_age['label']= "Hold";
		$single_pd_age['value']=$row_hand['tot_hold'];
		$all_pd_age1[]		=	$single_pd_age;
	}
	$return_array['type']	=	"list";
	$return_array['label']	=	"Pending PD Age";
	$return_array['items']	=	$all_pd_age;
	$return_arr[0]			=	$return_array;
	$return_array['type']	=	"list";
	$return_array['label']	=	"Yet to Start/Hold ";
	$return_array['items']	=	$all_pd_age1;
	$return_arr[1]			=	$return_array;
	return $return_arr;
}
/**
 * @param string $uid
 *
 * @return string[string]
 */
function get_support_handover_age($uid){
	$month	=	date("m");
	$year	=	date("Y");
	$return_array	=	/*. (string[string]) .*/array();
	$all_pd_age		=	array();
	$single_pd_age	=	array();
	$sql_hand	=	" select GEP_HANDOVER_LESSTHEN_24HR,GEP_HANDOVER_MORETHEN_24HR ".
					" from gft_emp_pd_summary  where GEP_EMP_ID=$uid AND  GEP_MONTH=$month AND GEP_YEAR=$year";
	$res_hand	=	execute_my_query($sql_hand);
	if(mysqli_num_rows($res_hand)>0){
		$row_hand	=	mysqli_fetch_array($res_hand);
		$single_pd_age['label']= " &#60; 24hrs";
		$single_pd_age['value']=$row_hand['GEP_HANDOVER_LESSTHEN_24HR'];
		$all_pd_age[]		=	$single_pd_age;
	
		$single_pd_age['label']= " &#62; 24hrs";
		$single_pd_age['value']=$row_hand['GEP_HANDOVER_MORETHEN_24HR'];
		$all_pd_age[]		=	$single_pd_age;
	}else{
		$single_pd_age['label']= " &#60; 24hrs";
		$single_pd_age['value']="0";
		$all_pd_age[]		=	$single_pd_age;
		$single_pd_age['label']= "&#62; 24hrs";
		$all_pd_age[]		=	$single_pd_age;
	}
	$return_array['type']	=	"list";
	$return_array['label']	=	"Support Handover Age";
	$return_array['items']	=	$all_pd_age;
	return $return_array;
}
/**
 * @param int $uid
 * @param string $status
 * @param int $last_activity_hrs
 * @param boolean $pending_sales
 * 
 * @return string
 */
function get_pending_support_details($uid,$status="",$last_activity_hrs=0,$pending_sales=false){
	$return_str="";
	$where_query="";
	if($status!=""){
		$where_query .=" AND gch_current_status='$status'";
	}
	if($last_activity_hrs!=0 && $last_activity_hrs!=""){
		$expity_dt	=	(date('Y-m-d H:i:s',strtotime("-$last_activity_hrs hours",strtotime(date('Y-m-d H:i:s')))));
		$where_query .=" AND GCD_ACTIVITY_DATE<='$expity_dt'";
	}
	$welcome_ticket_check = ($pending_sales ? " and GCH_COMPLAINT_CODE=168 " : "") ;
	$sql_query=	" select group_concat(GCH_COMPLAINT_ID) GCH_COMPLAINT_ID,GCH_LEAD_CODE, GCD_ACTIVITY_DATE, GCD_PROCESS_EMP from gft_customer_support_hdr ".
				" inner join gft_customer_support_dtl on(GCH_COMPLAINT_ID=GCD_COMPLAINT_ID AND GCH_LAST_ACTIVITY_ID=gcd_activity_id) ".
				" where GCD_PROCESS_EMP='$uid' $welcome_ticket_check $where_query group by GCD_PROCESS_EMP ";
	$result = execute_my_query($sql_query);
	if(mysqli_num_rows($result)>0 && $row_complaint=mysqli_fetch_array($result)){
		$return_str=$row_complaint['GCH_COMPLAINT_ID'];
	}
	return $return_str;
}
/**
 * @param int $uid
 * @param string $report_date
 *
 * @return string
 */
function get_pending_training_of_pc($uid, $report_date){
    $result = execute_my_query("select GROUP_CONCAT(DISTINCT GLH_LEAD_CODE) GLH_LEAD_CODES from gft_customer_support_hdr ".
            " inner join gft_customer_support_dtl ON(gcd_activity_id=GCH_LAST_ACTIVITY_ID) ".
            " inner join gft_lead_hdr ON(GLH_LEAD_CODE=GCH_LEAD_CODE) ".
            " where GLH_FIELD_INCHARGE='$uid' AND gch_current_status='T6' AND DATE_FORMAT(GCD_SCHEDULE_DATE,'%Y-%m-%d')='$report_date'". 
            " GROUP BY GLH_FIELD_INCHARGE");
    error_log("select GROUP_CONCAT(DISTINCT GLH_LEAD_CODE) GLH_LEAD_CODES from gft_customer_support_hdr ".
        " inner join gft_customer_support_dtl ON(gcd_activity_id=GCH_LAST_ACTIVITY_ID) ".
        " inner join gft_lead_hdr ON(GLH_LEAD_CODE=GCH_LEAD_CODE) ".
        " where GLH_FIELD_INCHARGE='$uid' AND gch_current_status='T6' AND DATE_FORMAT(GCD_SCHEDULE_DATE,'%Y-%m-%d')='$report_date'".
        " GROUP BY GLH_FIELD_INCHARGE");
    if(mysqli_num_rows($result)>0 && $row=mysqli_fetch_assoc($result)){
        return $row['GLH_LEAD_CODES'];
    }
    return '';
}
/**
 * @param int $employee_id
 * @return string
 */
function pending_sales_ticket_validation($employee_id){
    $pending_sales_list = "";
    $role = get_role_emp_id($employee_id);
    if(is_authorized_group_list($employee_id, array(66),$role[0])){
        $last_activity_hrs=(int)get_samee_const("SALES_DR_APPROVAL_TIME");
        $pending_sales_list = get_pending_support_details($employee_id,"T87",$last_activity_hrs,true);
    }
    return $pending_sales_list;
}

/**
 * @param string $uid
 *
 * @return string[string]
 */
function get_pending_support_dtl($uid){
	$return_array	=	/*. (string[string]) .*/array();
	$total_support	=	0;
	$avg_age		=	0;
	$oldest_age		=	0;
	$sql_support	=	" select  count(*) as tot_count, sum(DATEDIFF(now(),GCH_COMPLAINT_DATE)) as age_diff, ".
						" DATEDIFF(now(), min(GCH_COMPLAINT_DATE)) AS oldest_age ".
						" from gft_customer_support_hdr  ".
						" join gft_customer_support_dtl on (GCH_COMPLAINT_ID=GCD_COMPLAINT_ID and gch_last_activity_id=gcd_activity_id) ".
						" join gft_lead_hdr on (GCH_LEAD_CODE=GLH_LEAD_CODE) ".
						" join gft_emp_master on (GEM_EMP_ID=GCD_PROCESS_EMP) ".
						" join gft_status_master sm on (gcd_status=GTM_CODE and GTM_STATUS='A') ".
						" join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE) ".
						" join gft_status_group_master on (GMG_GROUP_ID=GTM_GROUP_ID) ".
						" left join gft_severity_master on (GCD_SEVERITY=GSM_CODE) ".
						" where sm.prob=1 and GCD_PROCESS_EMP=$uid ";
	$res_support	=	execute_my_query($sql_support);
	if(mysqli_num_rows($res_support)>0){
		$row	=	mysqli_fetch_array($res_support);
		if($row['tot_count']>0){
			$total_support	=	$row['tot_count'];
		}
		if($total_support>0){
			$avg_age	=	(int)($row['age_diff']/$total_support);			
		}
		$oldest_age		=	$row['oldest_age'];
	}	
	$return_array['type']	=	"highlight";
	$return_array['label']	=	"Pending Support";
	$return_array['text']	=	"$total_support";
	$return_array['summary']=	"Avg age: $avg_age Oldest age: $oldest_age";	
	return $return_array;
	
} 
/**
 * @param string $employee_id
 * @param string $from_date
 * @param string $to_date
 * @param string $window
 *
 * @return string[string]
 */
function get_actual_vs_plan($employee_id, $from_date, $to_date,$window=''){
	global $receive_arg;
	$date_arr = explode('-', $from_date);
	$year = $date_arr[0];
	$month = $date_arr[1];
	$ind = $hq = 0;
	if(is_authorized_group($employee_id,null,30)){//PCS Daily Report
		$achieved_select="select GCCM_METRIC_ID,sum((REPLACE(GMM_SERVICE_TYPE,'k','')*1000/8)*GLD_TIME_DURATION) as achieved from gft_activity ".
                		 "join gft_commercial_classification_master on(GLD_COMMERCIAL_CLASSIFICATION=GCCM_ID) ".
                		 "join gft_pcs_milestone_master on(GMM_ID=GLD_MILESTONE)".
                		 "where GLD_EMP_ID=$employee_id and GLD_VISIT_DATE>='$from_date' and GLD_VISIT_DATE<='$to_date' group by GCCM_METRIC_ID";
	 	$select_query   ="select GEM_EMP_ID,GSM_ID,GSM_APP_DISPLAY_NAME,GTS_TARGET_VALUE,GTS_VALUE,ach.achieved,".
	 					 "GSM_MAX_DEVIATION_PERC,GSM_MIN_DEVIATION_PERC ".
	 					 "from gft_emp_master ".
	 					 "join gft_target_dtl on (GTD_TARGET_ID=GEM_TARGET_ID) ".
	 					 "join gft_scorecard_metrics on (GSM_ID=GTD_METRIC_ID) ".
	 					 "left join (($achieved_select) ach) on (ach.GCCM_METRIC_ID=GTD_METRIC_ID) ".
	 					 "left join gft_target_scorecard_metrics on (GTD_METRIC_ID=GTS_METRIC_ID and GTS_EMP_ID=GEM_EMP_ID and GTS_MONTH='$month' and GTS_YEAR='$year') ".
	 	                 "where GEM_EMP_ID='$employee_id' and GSM_LIST_SUMMARY='Y' ";
	}
	else{
		$achieved_select = " select GDA_EMP_ID,GDA_METRIC_ID,sum(GDA_ACHIEVED) as achieved from gft_daily_achieved_metrics ".
				" where GDA_EMP_ID='$employee_id' and GDA_DATE >='$from_date' and GDA_DATE <='$to_date' group by GDA_EMP_ID,GDA_METRIC_ID ";
		$select_query = " select GEM_EMP_ID,GSM_ID,GSM_APP_DISPLAY_NAME,GTS_TARGET_VALUE,GTS_VALUE,ach.achieved, ".
				" GSM_MAX_DEVIATION_PERC,GSM_MIN_DEVIATION_PERC ".
				" from gft_emp_master ".
				" join gft_target_dtl on (GTD_TARGET_ID=GEM_TARGET_ID) ".
				" join gft_scorecard_metrics on (GSM_ID=GTD_METRIC_ID) ".
				" left join ( ($achieved_select) ach ) on (ach.GDA_EMP_ID = GEM_EMP_ID and ach.GDA_METRIC_ID=GTD_METRIC_ID) ".
				" left join gft_target_scorecard_metrics on (GTD_METRIC_ID=GTS_METRIC_ID and GTS_EMP_ID=GEM_EMP_ID and GTS_MONTH='$month' and GTS_YEAR='$year') ".
				" where GEM_EMP_ID='$employee_id' and GSM_LIST_SUMMARY='Y' ";
		$ind_hq_achieved = get_ind_hq_prospects_count($employee_id, $from_date, $to_date);
		$ind = $ind_hq_achieved['ind'];
		$hq = $ind_hq_achieved['hq'];
	}
	$res_set = execute_my_query($select_query);
	if(mysqli_num_rows($res_set)==0){
		sendErrorWithCode($receive_arg,"Target Not Mapped For You. Contact Sales Coordinator",HttpStatusCode::BAD_REQUEST);
		exit;
	}
	$temp_arr = $status_val = /*. (string[int]) .*/array();
	while($row_data = mysqli_fetch_array($res_set)){
		$achieved = (int)$row_data['achieved'];
		$plan_value 	= (int)$row_data['GTS_VALUE'];
		$target_value 	= (int)$row_data['GTS_TARGET_VALUE'];
		$month_start 	= (int)$row_data['GSM_MAX_DEVIATION_PERC'];
		$month_end	 	= (int)$row_data['GSM_MIN_DEVIATION_PERC'];
		if((int)$row_data['GSM_ID']==83) {
			$achieved = $ind;
		}else if((int)$row_data['GSM_ID']==84) {
			$achieved = $hq;
		}else if((int)$row_data['GSM_ID']==6){
			$q1 = " select GLD_AGILE_CONTRIBUTOR,count(distinct GLD_ACTIVITY_ID) as demo_cnt from gft_activity where GLD_AGILE_CONTRIBUTOR='$employee_id' ".
				  " and GLD_VISIT_NATURE in (2,48) and GLD_ACTIVITY_STATUS_ID=2 and GLD_VISIT_DATE between '$from_date' and '$to_date' ".
				  " group by GLD_AGILE_CONTRIBUTOR ";
			$r1 = execute_my_query($q1);
			if($d1 = mysqli_fetch_array($r1)){
				$achieved = $d1['demo_cnt'];
			}
		}
		if($row_data['GSM_ID']=='12'){
		    $achieved_metrics = /*.(string[string]).*/get_daily_achieved_array($employee_id,$from_date,$to_date,true);
		    $achieved = $achieved_metrics['indep_order'];
		}else if( $row_data['GSM_ID']=='59'){
		    $achieved_metrics = /*.(string[string]).*/get_daily_achieved_array($employee_id,$from_date,$to_date,true);
		    $achieved = $achieved_metrics['hq_order'];
		}
		$status_val[] 	= get_metric_status_color($row_data['GSM_ID'],$achieved,$plan_value,$month_start,$month_end,'','value');
		$item_arr['label'] = $row_data['GSM_APP_DISPLAY_NAME'];
		$item_arr['value'] = number_to_abbreviated_form("$achieved")."/".number_to_abbreviated_form("$plan_value")."/".number_to_abbreviated_form("$target_value");
		$temp_arr[] = $item_arr;
	}
	$summary_arr['type'] 	= "list";
	$summary_arr['label'] 	= "Actual Vs Plan Vs Target";
	if($window=='mtd'){
		$color = "R";
		$avg_val = $tot_val = 0;
		foreach ($status_val as $val){
			$tot_val = $tot_val+$val;
		}
		if($tot_val > 0){
			$avg_val = round($tot_val/count($status_val));
		}
		$amber_deviation_arr = explode('-',get_samee_const("Amber_Status_Range_For_App"));
		$green_allowable = isset($amber_deviation_arr[0])?$amber_deviation_arr[0]:0;
		$amber_allowable = isset($amber_deviation_arr[1])?$amber_deviation_arr[1]:0;
		if($avg_val <= $green_allowable){
			$color = "G";
		}else if($avg_val <= $amber_allowable){
			$color = "A";
		}
		$summary_arr['status'] 	= $color;
	}
	$summary_arr['items'] 	= $temp_arr;
	return $summary_arr;
}

/**
 * @param string $uid
 * @param string $from_date
 * @param string $to_date
 *
 * @return string[string]
 */
function get_conversion_ratio($uid,$from_date,$to_date){
	$select_query = " select  count(distinct GLD_LEAD_CODE) as tot_lead, sum(if(GLH_PROSPECT_ON>='$from_date' and GLH_PROSPECT_ON<='$to_date',1,0)) as prospects ".
			" from gft_lead_hdr join gft_activity on (GLH_LEAD_CODE=GLD_LEAD_CODE) ".
			" where GLD_EMP_ID='$uid' and GLD_VISIT_DATE >= '$from_date' and GLD_VISIT_DATE <='$to_date' ".
			" and GLD_LEAD_STATUS not in (8,9) and (GLD_LEAD_STATUS!=3 or GLH_CREATED_DATE!=GLH_PROSPECT_ON) ".
			" group by GLD_EMP_ID ";
	$res1 = execute_my_query($select_query);
	$total_lead = $no_of_prospect = 0;
	$lead_to_prosp = $prosp_to_order = '0%';
	if($row1 = mysqli_fetch_array($res1)){
		$total_lead = (int)$row1['tot_lead'];
		$no_of_prospect = (int)$row1['prospects'];
	}
	if($total_lead!=0){
		$lead_to_prosp = (string)round(($no_of_prospect/$total_lead)*100, 2)."%";
	}
	$conv_arr['label'] = "Prospect";
	$conv_arr['value'] = $lead_to_prosp;
	$temp_arr[] = $conv_arr;


	$select_query = " select  count(distinct GLD_LEAD_CODE) as tot_prosp, count(distinct GOD_LEAD_CODE) as order_won ".
			" from gft_lead_hdr ".
			" join gft_activity on (GLD_LEAD_CODE=GLH_LEAD_CODE) ".
			" left join gft_order_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE and GOD_ORDER_DATE>='$from_date' and GOD_ORDER_DATE<='$to_date' and GOD_ORDER_AMT > 0) ".
			" where GLD_EMP_ID='$uid' and GLD_VISIT_DATE>='$from_date' and GLD_VISIT_DATE<='$to_date' ".
			" and (GLD_LEAD_STATUS=3 or (GLD_LEAD_STATUS=8 and GLD_VISIT_DATE=GOD_ORDER_DATE)) ".
			" group by GLD_EMP_ID ";
	$res1 = execute_my_query($select_query);
	$total_pros = $no_of_orderwon = 0;
	if($row1 = mysqli_fetch_array($res1)){
		$total_pros = (int)$row1['tot_prosp'];
		$no_of_orderwon = (int)$row1['order_won'];
	}
	if($total_pros!=0){
		$prosp_to_order = (string)round(($no_of_orderwon/$total_pros)*100, 2)."%";
	}
	$conv_arr['label'] = "Order";
	$conv_arr['value'] = $prosp_to_order;
	$temp_arr[] = $conv_arr;

	$ret_arr['type'] 	= "list";
	$ret_arr['label'] 	= "Conversion Ratio";
	//	$ret_arr['status'] 	= "A";
	$ret_arr['items'] 	= $temp_arr;
	return $ret_arr;
}

/**
 * @param string $uid
 * @param string $from_date
 * @param string $to_date
 * 
 * @return string[string]
 */
function operational_efficiency($uid,$from_date,$to_date){
	$select_query = " select  count(distinct GLD_LEAD_CODE) as tot_lead, count(distinct if(GLD_LEAD_STATUS=8, GLD_LEAD_CODE, null) ) as order_won ".
			" from gft_lead_hdr join gft_activity on (GLH_LEAD_CODE=GLD_LEAD_CODE) ".
			" where GLD_EMP_ID='$uid' and GLD_VISIT_DATE >= '$from_date' and GLD_VISIT_DATE <='$to_date' ".
			" and GLD_LEAD_STATUS in (8,7,11) group by GLD_EMP_ID ";
	$res1 = execute_my_query($select_query);
	$win_rate = 0;
	if($row1 = mysqli_fetch_array($res1)){
		$tot_lead = (int)$row1['tot_lead'];
		$order_won= (int)$row1['order_won'];
		if($tot_lead > 0){
			$win_rate = round($order_won/$tot_lead,2)*100;
		}
	}

/* 	$temp_arr['label'] = "Avg Prosp to Order";
	$temp_arr['value'] = "20 day(s)";
	$item_arr[] =$temp_arr; */
	
//	$temp_arr['label'] = "Win Rate";
//	$temp_arr['value'] = "$win_rate %";
//	$item_arr[] =$temp_arr;
	
	$ret_arr['type'] 	= "highlight";
	$ret_arr['label'] 	= "Win Rate";
	$ret_arr['text'] 	= "$win_rate %";
	$ret_arr['summary'] = "";
	
	//$ret_arr['status'] = "A";
	//$ret_arr['items'] = $item_arr;
	return $ret_arr;
}

/**
 * @param string $uid
 * @param string $from_date
 * @param string $to_date
 * 
 * @return mixed[]
 */
function get_partner_signup_count($uid,$from_date,$to_date) {
	$ret_arr = array();
	$date_cond = '';
	if($from_date!='') {
		$date_cond .= " and cgi_created_date>='".db_date_format($from_date)."' ";
	} if($to_date!='') {
		$date_cond .= " and cgi_created_date<='".db_date_format($to_date)."' ";
	}
	$qry = " select glh_lead_subtype,count(distinct cgi_lead_code) cnt ".
		   " from gft_cp_info join gft_lead_hdr on (glh_lead_code=cgi_lead_code) ".
		   " where cgi_bm_portal_creation='$uid' $date_cond and glh_lead_subtype in (7,10,11) group by glh_lead_subtype ";
	$qry_res = execute_my_query($qry);
	$items_arr = array(array("label"=>"Solution","value"=>0),array("label"=>"Sales","value"=>0),array("label"=>"Referral","value"=>0));
	$types_arr = array();
	while($row = mysqli_fetch_array($qry_res)) {
		$label = '';
		$types_arr[] = $row['glh_lead_subtype'];
		if($row['glh_lead_subtype']=='7') {
			$items_arr[0]['value'] += (int)$row['cnt'];
		}
		if($row['glh_lead_subtype']=='11') {
			$items_arr[1]['value'] += (int)$row['cnt'];
		}
		if($row['glh_lead_subtype']=='10') {
			$items_arr[2]['value'] += (int)$row['cnt'];
		}
	}
	foreach ($items_arr as $k => $varr) {
		$val = $varr['value'];
		$varr['value'] = "$val";
	}
	$ret_arr['type'] = 'list';
	$ret_arr['label'] = 'New Partner Signups';
	$ret_arr['items'] = $items_arr;
	return $ret_arr;
}
/**
 * @param string $uid
 * @param string $window
 * @param string $from_date
 * @param string $to_date
 * @param string $report_date
 *
 * @return string[int][string]
 */
function get_daily_report_summary($uid,$window='',$from_date='',$to_date='',$report_date=''){
	$return_array		=	/*. (string[int][string]) .*/array();
	if($window=='mtd'){
		$from_date	=	date("Y-m-01");
		if($report_date!=''){
			$report_date_month	=	date("m",strtotime($report_date));
			$from_date	=	date("Y-$report_date_month-01");
		}		
		$to_date	=	($report_date!=''?$report_date:date("Y-m-d"));
	}else if($window=='today'){
		$from_date	=$to_date=	($report_date!=''?$report_date:date("Y-m-d"));
	}else {
		$from_date	=	date("Y-m-d", strtotime($from_date));
		$to_date	=	date("Y-m-d", strtotime($to_date));
	}
	$expense_dtl=	get_expenses_details($uid,$from_date,$to_date,$window);
	if(is_authorized_group($uid,null,30)){//Daily Report for PCS
		$return_array[]     =   get_pcs_time_spent_summary($uid,$from_date,$to_date,$window);
		$return_array[]     =   get_actual_vs_plan($uid,$from_date,$to_date,$window);
	}else if(is_authorized_group_list($uid, array (70,36))){//Daily Report for PC/RC		
		$return_array[]		=	get_actual_vs_plan($uid,$from_date,$to_date,$window);
		$return_array[]		=	get_work_efficiency($uid,$window,$from_date,$to_date,"pc");
		/* Commented due to perpormance issue*/
		//$return_array[]		=	get_planned_unplanned_dtl($uid,$from_date,$to_date,$window,"pc");
		if($window!='today'){
			$pending_pd		=	get_pending_pd_age($uid);
			$return_array[]	=	$pending_pd[0];
			$return_array[]	=	$pending_pd[1];
			$return_array[]	=	get_completed_pd_age($uid);
			$return_array[]	=	get_support_handover_age($uid);
			//$return_array[]	=	get_pending_support_dtl($uid);	
		}
		$return_array[]		=	get_pending_pd_cm_escalation_dtl($uid,$from_date,$to_date,$window);
		$return_array[]		=	get_pd_confirmation_dtl($uid,$from_date,$to_date,$window);
    	$return_array[]	    =	$expense_dtl;
	}else{//Daily Report for Sales team
		$return_array[]		=	get_actual_vs_plan($uid,$from_date,$to_date,$window);
		/* Commented due to perpormance issue*/
		//$return_array[]		=	get_planned_unplanned_dtl($uid,$from_date,$to_date,$window);
		$return_array[]		=	get_work_efficiency($uid,$window,$from_date,$to_date);
		if((int)$uid<7000) {
			$return_array[]		= 	get_partner_signup_count($uid,$from_date,$to_date);
		}
		if($window!='today'){
			$return_array[]	=	get_conversion_ratio($uid,$from_date,$to_date);
			$return_array[]	=	operational_efficiency($uid,$from_date,$to_date);
		}
		if($window=='today'){
			$return_array[]		=	get_pending_pd_cm_approval_dtl($uid,$from_date,$to_date,$window);
			$return_array[]		=	get_pending_pd_cm_escalation_dtl($uid,$from_date,$to_date,$window);			
		}
		$return_array[]	    =	$expense_dtl;
	}	
	return $return_array;
}

/**
 * @param int $uid
 * @param string $window
 * 
 * @return string[int][string]
 */
function get_inference_dtl($uid, $window){
	$ret_arr = /*. (string[int][string]) .*/array();
	$execution_mtd_cards 	= get_execution_cards($uid, date('Y-m-01'), date('Y-m-d'), 'mtd');
	$len = count($execution_mtd_cards);
	$i=0;
	while ($i < $len) {
		$metric_id	= $execution_mtd_cards[$i]['key'];
		$flag_val 	= $execution_mtd_cards[$i]['status'];
		if($flag_val=='R'){
			$sel_que = "select GIF_RED_COUNT,GIF_FLAG from gft_inference_dtl where GIF_EMP_ID='$uid' and GIF_METRIC_ID='$metric_id' order by GIF_ID desc limit 1 ";
			$res_que = execute_my_query($sel_que);
			if($row1 = mysqli_fetch_array($res_que)){
				if($row1['GIF_FLAG']=='R'){
					$red_cnt = (int)$row1['GIF_RED_COUNT'] + 1;
					if( ($red_cnt % 5) == 0 ){
						$temp_arr['status'] 	= "bad";
						$temp_arr['title'] 		= $execution_mtd_cards[$i]['summary'];
						$temp_arr['description']= "Target Not Achieved for the Last $red_cnt days";
						$temp_arr['metric_id']	= $metric_id;
						$ret_arr[] = $temp_arr;
					}
				}
			}
		}else if($flag_val=='G'){
			$sel_que = "select GIF_GREEN_COUNT,GIF_FLAG from gft_inference_dtl where GIF_EMP_ID='$uid' and GIF_METRIC_ID='$metric_id' order by GIF_ID desc limit 1 ";
			$res_que = execute_my_query($sel_que);
			if($row1 = mysqli_fetch_array($res_que)){
				if($row1['GIF_FLAG']=='G'){
					$green_cnt = (int)$row1['GIF_GREEN_COUNT'] + 1;
					if( ($green_cnt % 5) == 0 ){
						$temp_arr['status'] 	= "good";
						$temp_arr['title'] 		= $execution_mtd_cards[$i]['summary'];
						$temp_arr['description']= "You are getting back on track, keep it up!";
						$temp_arr['metric_id']	= $metric_id;
						$ret_arr[] = $temp_arr;
					}
				}
			}
		}
		$i++;
	}
	return $ret_arr;
}

/**
 * @param string $error_no
 * @return string
 */
function get_file_error_msg_string($error_no) {
	$error_msg = '';
	switch((int)$error_no) {
		case 1 :
			$error_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
			break;
		case 2 :
			$error_msg = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
			break;
		case 3 :
			$error_msg = 'The uploaded file was only partially uploaded';
			break;
		case 4:
			$error_msg = 'No file was uploaded';
			break;
		case 6:
			$error_msg = 'Missing a temporary folder';
			break;
		case 7:
			$error_msg = 'Failed to write file to disk';
			break;
		case 8:
			$error_msg = 'A PHP extension stopped the file upload';
			break;
		default:
			$error_msg = 'Unexpected error: Error code:'. (string)$_FILES["upfile1"]["error"];
			break;
	}
	return $error_msg;
}
/**
 * @return void
 */
function handle_file_errors(){
	global $receive_arg;
	if( isset($_FILES["upfile1"]["error"]) && !is_array($_FILES["upfile1"]["error"]) && ($_FILES["upfile1"]["error"] > 0) ){
		$error_msg = get_file_error_msg_string($_FILES["upfile1"]['error']);
		sendErrorWithCode($receive_arg,$error_msg,HttpStatusCode::BAD_REQUEST);
		exit;
	}
}

/**
 * @param int $uid
 * @return string[string]
 */
function get_appointment_count($uid) {
	$qry = " select count(*) as cnt, min(GLD_NEXT_ACTION_DATE), avg(DATEDIFF(now(),GLD_NEXT_ACTION_DATE)) as avg_age, ".
			" DATEDIFF(now(), min(GLD_NEXT_ACTION_DATE)) AS oldest_age ".
			" from gft_activity join gft_activity_master on (GLD_NEXT_ACTION=GAM_ACTIVITY_ID) ".
    		" INNER JOIN gft_lead_hdr lh on(GLD_LEAD_CODE=lh.GLH_lead_code AND lh.GLH_LEAD_TYPE!=8) ".
    		" INNER join gft_customer_status_master on gcs_code=lh.GLH_status ". 
    		" join gft_emp_master em on(gem_emp_id=GLD_EMP_ID) where (1) ".
			" AND GLD_EMP_ID=$uid AND GLD_NEXT_ACTION_DATE<=now() ".
			" AND GLD_SCHEDULE_STATUS in (1,3,7) and GLD_NEXT_ACTION_DATE!='0000:00:00'";
	$result_array = /*. (string[string]) .*/array();
	if($res = execute_my_query($qry)) {
		if($r = mysqli_fetch_array($res)){
			$result_array['cnt'] = $r['cnt'];
			$result_array['avg'] = $r['avg_age'];
			$result_array['oldest'] = $r['oldest_age'];
		}
	}
	return $result_array;
}
/**
 * @param int $uid
 * @return string[string]
 */
function get_followup_count($uid) {
	$qry = " select count(*) as cnt,min(GCF_FOLLOWUP_DATE), avg(DATEDIFF(now(),GCF_FOLLOWUP_DATE)) as avg_age, ".
			" DATEDIFF(now(), min(GCF_FOLLOWUP_DATE)) AS oldest_age ".
			" from gft_cplead_followup_dtl ".
    		" INNER JOIN gft_lead_hdr lh on(GCF_LEAD_CODE=lh.GLH_lead_code AND lh.GLH_LEAD_TYPE!=8) ".
	    	" INNER join gft_customer_status_master on gcs_code=lh.GLH_status ".
	    	" join gft_emp_master em2 on (gcf_assign_to=em2.gem_emp_id) ".
     		" join gft_activity_master on(gcf_followup_action=gam_activity_id) ".
    		" where (1) AND gcf_assign_to=$uid AND GCF_FOLLOWUP_DATE<=now() and GCF_FOLLOWUP_DATE!='0000:00:00' ".
    		" AND gcf_followup_status in (1,3,7)";
	$result_array = /*. (string[string]) .*/array();
	if($res = execute_my_query($qry))
		if($r = mysqli_fetch_array($res)) {
			$result_array['cnt'] = $r['cnt'];
			$result_array['avg'] = $r['avg_age'];
			$result_array['oldest'] = $r['oldest_age'];
		}
	return $result_array;
}
/**
 * @param int $uid
 * @return string[string]
 */
function get_support_count($uid) {
	$qry = " select count(*) as cnt,min(GCH_COMPLAINT_DATE),DATEDIFF(now(), min(GCH_COMPLAINT_DATE)) AS oldest_age, ".
			" avg(DATEDIFF(now(),GCH_COMPLAINT_DATE)) as avg_age ".
			" from gft_customer_support_hdr ".
    		" join gft_customer_support_dtl on (GCH_COMPLAINT_ID=GCD_COMPLAINT_ID ".
            "    and gch_last_activity_id=gcd_activity_id) ".
    		" join gft_lead_hdr lh on (GCH_LEAD_CODE=GLH_LEAD_CODE) ".
     		" join gft_emp_master on (GEM_EMP_ID=GCD_PROCESS_EMP) ".
    		" join gft_status_master sm on (gcd_status=GTM_CODE and GTM_STATUS='A') ".
    		" join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE) ".
    		" join gft_status_group_master on (GMG_GROUP_ID=GTM_GROUP_ID) ".
			" left join gft_severity_master on (GCD_SEVERITY=GSM_CODE) ".
    		" where sm.prob=1 and GCD_PROCESS_EMP=$uid";
	$result_array= /*. (string[string]) .*/array();
	if($res = execute_my_query($qry))
		if($r = mysqli_fetch_array($res)){
			$result_array['cnt'] = $r['cnt'];
			$result_array['avg'] = $r['avg_age'];
			$result_array['oldest'] = $r['oldest_age'];
		}
	return $result_array;
}
/**
 * @param int $emp_id
 * @param boolean $for_mail
 * @return string
 */
function get_all_reminder_age_dtl($emp_id,$for_mail=false){
	
	$sql_query	=	" select SUM(tot_count) as tot_count, (SUM(age_diff)/SUM(tot_count)) as avg_age, max(oldest_age) as oldest_age from( ".
					" select  count(*) as tot_count, min(GCH_COMPLAINT_DATE), sum(DATEDIFF(now(),GCH_COMPLAINT_DATE)) as age_diff, ".
					" DATEDIFF(now(), min(GCH_COMPLAINT_DATE)) AS oldest_age ".
					" from gft_customer_support_hdr  ".
					" join gft_customer_support_dtl on (GCH_COMPLAINT_ID=GCD_COMPLAINT_ID and gch_last_activity_id=gcd_activity_id) ".
					" join gft_lead_hdr on (GCH_LEAD_CODE=GLH_LEAD_CODE) ".
					" join gft_emp_master on (GEM_EMP_ID=GCD_PROCESS_EMP) ".
					" join gft_status_master sm on (gcd_status=GTM_CODE and GTM_STATUS='A') ".
					" join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE) ".
					" join gft_status_group_master on (GMG_GROUP_ID=GTM_GROUP_ID) ".
					" left join gft_severity_master on (GCD_SEVERITY=GSM_CODE) ".
					" where sm.prob=1 and GCD_PROCESS_EMP=$emp_id ".
					" UNION ALL ".
					" select  count(*) as tot_count, min(GCF_FOLLOWUP_DATE), sum(DATEDIFF(now(),GCF_FOLLOWUP_DATE)) as age_diff, ".
					" DATEDIFF(now(), min(GCF_FOLLOWUP_DATE)) AS oldest_age  ".
					" from gft_cplead_followup_dtl ".
					" INNER JOIN gft_lead_hdr lh on(GCF_LEAD_CODE=lh.GLH_lead_code AND lh.GLH_LEAD_TYPE!=8) ".
					" INNER join gft_customer_status_master on gcs_code=lh.GLH_status ".
					" join gft_emp_master em2 on (gcf_assign_to=em2.gem_emp_id) ".
					" join gft_activity_master on(gcf_followup_action=gam_activity_id) ".
					" where  gcf_assign_to=$emp_id AND gcf_followup_status in(1,3,7) and GCF_FOLLOWUP_DATE<now() and GCF_FOLLOWUP_DATE!='0000:00:00'".
					" UNION ALL ".
					" select count(*) as tot_count, min(GLD_NEXT_ACTION_DATE), sum(DATEDIFF(now(),GLD_NEXT_ACTION_DATE)) as age_diff, ".
					" DATEDIFF(now(), min(GLD_NEXT_ACTION_DATE)) AS oldest_age  ".
					" from gft_activity ".
					" join gft_activity_master on (GLD_NEXT_ACTION=GAM_ACTIVITY_ID) ".
					" INNER JOIN gft_lead_hdr lh on(GLD_LEAD_CODE=lh.GLH_lead_code AND lh.GLH_LEAD_TYPE!=8) ".
					" INNER join gft_customer_status_master on gcs_code=lh.GLH_status join gft_emp_master em on(gem_emp_id=GLD_EMP_ID) ".
					" where GLD_EMP_ID=$emp_id AND GLD_SCHEDULE_STATUS in(1,3,7)  and GLD_NEXT_ACTION_DATE<=now() and GLD_NEXT_ACTION_DATE!='0000:00:00'".
					" ) as all_age";
	$res		=	execute_my_query($sql_query);
	if(mysqli_num_rows($res)==0){
		 return '';
	}
	$row	=	mysqli_fetch_array($res);
	$return_string	=	"";
	if($for_mail) {
		$return_string .= "<table border=1 ><tr><th colspan='4'>Reminder Summary</th></tr>";
		$return_string .= "<tr><th>Reminder</th><th>Pending</th><th>Avg age</th><th>Oldest age</th></tr>"; 
		$appointment_dtls = get_appointment_count($emp_id);
		$followup_dtls = get_followup_count($emp_id);
		$support_tkts_dtls = get_support_count($emp_id);
		$tot = 0;
		if(is_array($appointment_dtls) && count($appointment_dtls)==3) {
			$cnt = (int)$appointment_dtls['cnt'];
			$tot += $cnt;
			$avg = (int)$appointment_dtls['avg'];
			$oldest = (int)$appointment_dtls['oldest'];
			$return_string .= "<tr><td>Appointments</td><td align='right'>$cnt</td><td align='center'>$avg"."d</td><td align='center'>$oldest"."d</td></tr>";
		}
		if(is_array($followup_dtls) && count($followup_dtls)==3) {
			$cnt = (int)$followup_dtls['cnt'];
			$tot += $cnt;
			$avg = (int)$followup_dtls['avg'];
			$oldest = (int)$followup_dtls['oldest'];
			$return_string .= "<tr><td>Follow-ups</td><td align='right'>$cnt</td><td align='center'>$avg"."d</td><td align='center'>$oldest"."d</td></tr>";
		}
		if(is_array($support_tkts_dtls) && count($support_tkts_dtls)==3) {
			$cnt = (int)$support_tkts_dtls['cnt'];
			$tot += $cnt;
			$avg = (int)$support_tkts_dtls['avg'];
			$oldest = (int)$support_tkts_dtls['oldest'];
			$return_string .= "<tr><td>Support tickets</td><td align='right'>$cnt</td><td align='center'>$avg"."d</td><td align='center'>$oldest"."d</td></tr>";
		}
		$return_string .= "<tr><th>Total count</th><td align='right'>$tot</td></tr></table>";
	}
	else  {
		if($row['tot_count']>0){
			$return_string=$return_string."Total: ".(int)$row['tot_count'];
		}
		if($row['avg_age']>0){
			$return_string=$return_string.($return_string!=''?', ':'')."Avg age: ".(int)$row['avg_age']."d";
		}
		if($row['oldest_age']>0){
			$return_string=$return_string.($return_string!=''?', ':'')."Oldest age: ".(int)$row['oldest_age']."d";
		}
	}
	return $return_string;
}
/**
 * @param string $emp_id
 * 
 * @return boolean
 */
function check_emp_is_reporting_manager($emp_id){
	$sql_res	=	execute_my_query("select GER_EMP_ID from gft_emp_reporting where GER_REPORTING_EMPID='$emp_id' and GER_STATUS='A'");
	if(mysqli_num_rows($sql_res)>0){
		return true;
	}else{
		return false;
	}
}
/**
 * @param int $employee_code
 * @param string $report_date
 * 
 * @return string
 */
function get_support_activity_dtl($employee_code,$report_date){
	$gcd_date_cond = " and GCD_ACTIVITY_DATE >= '$report_date 00:00:00' and GCD_ACTIVITY_DATE <= '$report_date 23:59:59' ";
	$rep_query= " select h.GCH_COMPLAINT_ID, concat(lh.GLH_CUST_NAME,'-',lh.GLH_CUST_STREETADDR2) as lead_name, ".
			" em.gem_emp_name, st.gtm_name, if(GCD_VISIT_TIMEOUT > '0000-00-00 00:00:00', timediff(GCD_VISIT_TIMEOUT, GCD_ACTIVITY_DATE), '') as duration, ".
			" nt.GCM_NATURE, K.GAM_ACTIVITY_DESC, d.GCD_PROBLEM_DESC, d.GCD_REMARKS, sv.gsm_name, pv.gpm_name, emt.GCM_EMOTION_NAME, ".
			" if(GCH_ASS_CUST='Y','ASA Customer',if(GCH_ASS_CUST='N','Non ASA Customer','')) as IS_ASA,GCD_VN_TRANSID ".
			" from gft_customer_support_hdr h ".
			" join gft_customer_support_dtl d on (h.GCH_COMPLAINT_ID =d.GCD_COMPLAINT_ID) ".
			" join gft_lead_hdr lh on (lh.GLH_LEAD_CODE=h.GCH_LEAD_CODE) ".
			" left join gft_emp_master em on (em.gem_emp_id=d.GCD_EMPLOYEE_ID) ".
			" left join gft_customer_emotion_master emt on (d.GCD_CUSTOMER_EMOTION=emt.GCM_EMOTION_ID) ".
			" left join gft_status_master st on (d.GCD_STATUS=st.gtm_code) ".
			" left join gft_priority_master pv on  (d.GCD_PRIORITY=pv.gpm_code) ".
			" left join gft_severity_master sv on  (d.GCD_SEVERITY=sv.gsm_code) ".
			" left join gft_complaint_nature_master nt on (GCD_NATURE=nt.GCM_NATURE_ID) ".
			" LEFT JOIN gft_activity_master K ON (d.GCD_VISIT_REASON=K.GAM_ACTIVITY_ID) ".
			" where GCD_EMPLOYEE_ID=$employee_code $gcd_date_cond ".
			" order by lead_name ";
	$rep_res = execute_my_query($rep_query);
	$sl_no = 0;
	$content =  "";
	if(mysqli_num_rows($rep_res)>0) {
		$content =  "<br><b>Support History:</b><br><br><table border=1 cellspacing=2 cellpading=2><tr><th width='40px'>Sl.No</th><th>Support Id</th><th>Shop Name - Location</th><th>Support By</th><th>Status</th>".
			"<th>Duration</th><th>Nature</th><th>Reason </th><th>Problem Description </th><th>Solution Given</th>".
			"<th>Severity</th><th>Priority </th><th>Customer Emotion </th></tr>";
	}
	while ($row3 = mysqli_fetch_array($rep_res)){
		$sl_no++;
		$audio_link = "";
		$trans_id  = $row3['GCD_VN_TRANSID'];
		if( ($trans_id!='') && ($trans_id!='0') ){
			$temp_link = "http://".$_SERVER['SERVER_NAME']."/techsupport_call_refference.php?transId=$trans_id";
			$audio_link = "<a href='$temp_link' target='_blank'>[A]</a>";
		}
		$content .= "<tr><td>$sl_no $audio_link</td><td>".$row3['GCH_COMPLAINT_ID']."</td><td>".$row3['lead_name']."</td>".
				"<td>".$row3['gem_emp_name']."</td><td>".$row3['gtm_name']."</td><td>".$row3['duration']."</td>".
					"<td>".$row3['GCM_NATURE']."</td><td>".$row3['GAM_ACTIVITY_DESC']."</td><td>".$row3['GCD_PROBLEM_DESC']."</td>".
					"<td>".$row3['GCD_REMARKS']."</td><td>".$row3['gsm_name']."</td><td>".$row3['gpm_name']."</td>".
					"<td>".$row3['GCM_EMOTION_NAME']."</td></tr>";
	}	
	if($content!=''){$content=$content."</table>";}
	return $content;
}
/**
 * @param int $employee_code
 * @param string $report_date
 *
 * @return string
 */
function get_complementary_coupon_activity($employee_code,$report_date){
	$sql_query =<<<END
			select gem_emp_name as gcr_request_to,gcr_message,gcr_request_status,gcr_reject_cmd,gcr_created_date, gcr_coupon_purpose,gcp_purpose,gcr_coupon_hours from gft_complementary_coupon_request 
			inner join gft_emp_master em ON (gem_emp_id=gcr_request_to)
			left join gft_complementary_coupon_purpose ON(gcr_purpose_id=gcp_id)
			where gcr_emp_id='$employee_code' AND gcr_created_date>='$report_date 00:00:00' AND gcr_created_date<='$report_date 23:59:59' order by gcr_id desc
END;
	$result = execute_my_query($sql_query);
	$sl_no = 0;
	$content =  "";
	if(mysqli_num_rows($result)>0) {
		$content =  "<br><b>Complementary Coupon Activity:</b><br><table border=1 cellspacing=2 cellpading=2>".
				"<tr><th width='40px'>Sl.No</th><th>Request To</th><th>Purpose</th><th>Status</th><th>Coupon Hours</th><th>Purpose Type</th>".
				"<th>Reason</th><th>Date</th></tr>";
	}
	while ($row3 = mysqli_fetch_array($result)){
		$sl_no++;
		$status = ($row3['gcr_request_status']=="I"?"Approved":($row3['gcr_request_status']=="N"?"Pending":"Rejected"));
		$coupon_purpose = ($row3['gcr_coupon_purpose']=="3"?"Customer":($row3['gcr_coupon_purpose']=="2"?"Presales":"Internal"));
		$content .= "<tr><td>$sl_no</td><td>".$row3['gcr_request_to']."</td><td>".$row3['gcr_message']."</td>".
				"<td>".$status."</td><td>".$row3['gcr_coupon_hours']."</td><td>".$coupon_purpose."</td>".
				"<td>".$row3['gcp_purpose']."</td><td>".$row3['gcr_created_date']."</td></tr>";
	}
	if($content!=''){$content=$content."</table>";}
	return $content;
}
/**
 * @param int $uid
 * @param string $device_id
 * 
 * @return boolean
 */
function reset_device_dtl($uid,$device_id){
	$res	=	execute_my_query("select  EMP_ID from gft_emp_auth_key where EMP_ID='$uid' and GEK_DEVICE_ID='$device_id' and GEK_DEVICE_STATUS='1'");
	if(mysqli_num_rows($res)>0){
		return  true;
	}else{
		execute_my_query("update gft_emp_auth_key set GEK_DEVICE_STATUS='0' where GEK_DEVICE_ID='$device_id'");
		return  false;
	}
}
/**
 * @param string $id
 * @param string $label
 * @param string $type
 * @param string $lookup
 * @param string $options
 * @param string $state
 * @param string $tag
 * @param string[int] $validators
 * @param boolean $readonly
 * @param string $filter_key
 * @param string $parent_field
 * @param string $value
 * @param mixed[int][string] $transitions
 * @param string $row_height
 * @param string $submission_tag
 * @param string $lookup_from
 * @param string $tip
 * @param string[int] $tip_on_value
 * @param string $tab
 * @param string $lookup_filter
 * @param boolean $load_onchange
 * @param mixed[string][string] $query_fields
 * @param string $url
 * @param string[int] $modification_arr
 * @param boolean $load_on_search
 * @param string $async_load
 * @param string $min_date
 * @param string $max_date
 * @param boolean $saveLocal
 * @param boolean $directEdit
 * @param boolean $single_val
 * @param string $max_length
 *
 * @return string[string]
 */
function meta_detail_array($id,$label,$type,$lookup='',$options='',$state='',$tag='',$validators=null,$readonly=false,
		$filter_key='',$parent_field='',$value='', $transitions=null,$row_height='',$submission_tag='',$lookup_from='',$tip='',
		$tip_on_value=null,$tab="",$lookup_filter="",$load_onchange=false,$query_fields=null,$url='',$modification_arr=null,
    $load_on_search=false,$async_load='',$min_date='',$max_date='',$saveLocal=false, $directEdit=false, $single_val=false, $max_length=''){
	$dtl_arr			=	/*. (string[string]) .*/array();
	$dtl_arr['id']		=	$id;
	$dtl_arr['label']	=	$label;
	$dtl_arr['type']	=	$type;
	
	if($lookup!=''){
		$dtl_arr['lookup']	=	$lookup;
	}
	if($options!=''){
		$dtl_arr['options']	=	$options;
	}
	if($state!=''){
		$dtl_arr['state']	=	$state;
	}
	if($tag!=''){
		$dtl_arr['tag']		=	$tag;
	}
	if(!empty($validators)){
		$dtl_arr['validators']	= $validators;
	}
	if($readonly){
		$dtl_arr['disabled'] = $readonly;
	}
	if($filter_key!=''){
		$dtl_arr['filter_key'] = $filter_key;
	}
	if($parent_field!=''){
		$dtl_arr['parent'] = $parent_field;
	}
	if($value!=''){
		$dtl_arr['value'] = $value;
	}
	if(!empty($transitions)){
		$dtl_arr['transitions']	= $transitions;
	}
	if($row_height!=''){
		$dtl_arr['rows']	= $row_height;
	}
	if($submission_tag!=''){
		$dtl_arr['submission_tag']	= $submission_tag;
	}
	if($lookup_from!=''){
		$dtl_arr['lookup_from']	= $lookup_from;
	}
	if($tip!=''){
		$dtl_arr['tip']	= $tip;
	}
	if(!empty($tip_on_value)){
		$dtl_arr['show_tip_on_value']	= $tip_on_value;
	}
	if($tab!=""){
		$dtl_arr['tab']	= $tab;
	}
	if($lookup_filter!=""){
		$dtl_arr['lookup_filter']	= $lookup_filter;
	}
	if($load_onchange) {
		$dtl_arr['load_onchange'] = true;
	}
	if($load_on_search) {
		$dtl_arr['load_on_search'] = true;
	}
	if($saveLocal){
		$dtl_arr['saveLocal'] = $saveLocal;
	}
	if($async_load!=''){
		$dtl_arr['async_load'] = $async_load;
	}
	if(!empty($query_fields)) $dtl_arr['query_fields'] = $query_fields;
	if($url != '') $dtl_arr['url'] = $url;
	if(!empty($modification_arr)){
		$dtl_arr['modifications'] = $modification_arr;
	}
	if($min_date!='') {
		$dtl_arr['min_date'] = $min_date;
	}
	if($max_date!='') {
		$dtl_arr['max_date'] = $max_date;
	}
	if($directEdit){
	    $dtl_arr['directEdit'] = $directEdit;
	}
	if($single_val){
	    $dtl_arr['single'] = $single_val;
	}
	if($max_length!=''){
	    $dtl_arr['max_length'] = $max_length;
	}
	return $dtl_arr;
}
/**
 * @param string $tab
 *
 * @return mixed[]
 */
function nextaction_metalist($tab=""){
    global $is_partner,$is_partner_emp;
    $tran_arr = array(array("type"=> "state","state"=> "on","onValue"=> array("2"),"target"=> array("assign_to_employee")));
    $definitions_arr=	array();
    $action_validators	= array("group::all_or_none","any_one_required_tag");
    $all_or_none_valid	= array("group::all_or_none");
    $tag_name = "New Appointment";
    $definitions_arr[] = meta_detail_array("next_action","Action", "master","nextAction.values","","on",
        $tag_name,$action_validators,false,'','','', null,'','','','',null,$tab);
    $definitions_arr[] = meta_detail_array("next_action_desc","Description", "text","","","on",
        $tag_name,$all_or_none_valid,false,'','','', null,'','','','',null,$tab);
    $definitions_arr[] = meta_detail_array("next_action_date","Date & Time", "activity_date","","","on",
        $tag_name,$all_or_none_valid,false,'','','', null,'','','','',null,$tab);
    $definitions_arr[] = meta_detail_array("assign_owner_type","Assign To","local","assign_to_type_list","radio","on",
        $tag_name,array("none"),false,'','','', $tran_arr,'','','','',null,$tab,'',false,null,'',null,false,'','','',false,false,true);
    
    $assign_to_employee	=	array();
    $assign_to_employee['id']	=	"assign_to_employee";
    $assign_to_employee['type']	=	"searchable-combo";
    $assign_to_employee['lookup_from']	=	"local";
    $assign_to_employee['lookup']	=	"assign_to_employee_list";
    $assign_to_employee['label']	=	"Assign to Employee";
    $assign_to_employee['state']	=	"off";
    $assign_to_employee['validators']	=	array("group::all_or_none");
    $assign_to_employee['tag']	=	$tag_name;
    $assign_to_employee['tab']	=	$tab;
    if($tab!=""){
        $assign_to_employee['lookup_from']	=	"master";
        $assign_to_employee['lookup']	=	"assignToEmployee.list";
        $assign_to_employee['load_on_search']	=	true;
    }
    $assign_to_employee['transitions']	=	array(array("type"=> "state","state"=> "on","onValue"=> array("*"),"target"=> array("assign_incharge","baton_wobbling","assign_responsible")));
    $definitions_arr[]			=	$assign_to_employee;
    
    if(!$is_partner_emp) {
        $assign_incharge	=	array();
        $assign_incharge['id']	=	"assign_incharge";
        $assign_incharge['type']	=	"local";
        $assign_incharge['lookup']	=	"assign_list";
        $assign_incharge['label']	=	"Assign as Lead In-charge";
        $assign_incharge['options']	=	"radio";
        //$assign_incharge['single']	=	true;
        $assign_incharge['state']	=	"off";
        $assign_incharge['tag']	=	$tag_name;
        $assign_incharge['skip_group_validation']	=	false; // true
        $assign_incharge['validators']	=	array("group::all_or_none"); // none
        $assign_incharge['tab']	=	$tab;
        $assign_incharge['tip_mapper'] = array("1"=>"Assigning as lead incharge along with a followup activity","0"=>"Assigning a followup activity");
        $assign_incharge['modifications'] = array(array("type"=>"state","state"=>"off","onValue"=>array("1"),"target"=>array("assign_responsible")));
        $definitions_arr[]			=	$assign_incharge;
    }
    if($is_partner || $is_partner_emp) {
        $assign_responsible = array();
        $assign_responsible['id'] = "assign_responsible";
        $assign_responsible['type'] = "local";
        $assign_responsible['lookup'] = "assign_list";
        $assign_responsible['label']	=	"Assign as Responsible to Customer";
        $assign_responsible['options'] = "radio";
        $assign_responsible['state'] = "off";
        $assign_responsible['tag'] = $tag_name;
        $assign_responsible['validators'] = array("group::all_or_none");
        $assign_responsible['tab'] = $tab;
        $assign_responsible['modifications'] = array(array("type"=>"state","state"=>"off","onValue"=>array("1"),"target"=>array("assign_incharge")));
        $definitions_arr[] = $assign_responsible;
    }
    $baton_arr['id']	=	"baton_wobbling";
    $baton_arr['type']	=	"boolean";
    $baton_arr['label']	=	LIVE_BATON_LABEL;
    $baton_arr['state']	=	"off";
    $baton_arr['value']	=	"0";
    $baton_arr['tag']	=	$tag_name;
    $baton_arr['tab']	=	$tab;
    $definitions_arr[]	=	$baton_arr;
    
    return $definitions_arr;
}
/**
 * @param string $table
 * @param string $id_field
 * @param string $name_field
 * @param string $default_id
 * @param string $where_con
 * @param string $order_by
 * @param string[int] $array_index
 *
 * @return mixed[]
 */
function get_master_info($table,$id_field,$name_field,$default_id='',$where_con='',$order_by='',$array_index=array('id','name')){
	$data_list			=	array();
	$default_data		=	array();
	$default_arr		=	array();
	$return_array		=	array();
	$where_qry			=	'';
	if($where_con!=''){
		$where_qry	=	" where $where_con";
	}
	if($order_by!=""){
		$where_qry	.=	" order by $order_by";
	}
	$sel_fields = " $id_field, $name_field ";
	$sql_get_data   	=	execute_my_query("select $sel_fields from $table $where_qry ");
	while($row=mysqli_fetch_array($sql_get_data)){
		$data_info			=	array();
		$data_info[$array_index[0]] = $row[$id_field];
		$data_info[$array_index[1]] = $row[$name_field];
		$data_list[] = $data_info;
		if($default_id!='' and $default_id==$data_info['id']){
			$default_data[$array_index[0]] = $row[$id_field];
			$default_data[$array_index[1]] = $row[$name_field];
			$default_arr[] = $default_data;
		}
	}
	$return_array[0]=$data_list;
	$return_array[1]=$default_arr;
	return $return_array;
}
/**
 * @param string $lead_code
 * 
 * 
 * @return string[string]
 */
function get_spoc_dsl_design($lead_code){
	$pd_audit_local	=	array();
	$definitions_arr=	array();
	$cust_country=	get_single_value_from_single_table("glh_country", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
	$contact_arr	=	get_customer_spoc_list($lead_code,($cust_country=='India'?'1':'2'));
	$pd_audit_local['spoc_list']	=	isset($contact_arr[0])?$contact_arr[0]:array();
	$pd_audit_local['mark_ts_list']	=	array(array("id"=>"1","name"=>"Mark as TS in contact list"));
	$spoc_default	=	isset($contact_arr[1])?$contact_arr[1]:"";
	$transition_cont=	isset($contact_arr[2])?$contact_arr[2]:array();
	$spoc_transition=null;
	if($cust_country=='India' && count($transition_cont)>0){
		$spoc_transition	=	array(array("type"=> "state","state"=> "on","onValue"=>$transition_cont,"target"=> array("mark_ts")));
	}
	$definitions_arr[]	=	meta_detail_array("spoc","Single Point of Contact","local","spoc_list","single","on","SPOC",null,false,null,null,"$spoc_default",$spoc_transition,"","","","Note: If new SPOC or Trainee Name is not listed, please create the new SPOC or Trainee
as a User in Security manager in POS with correct contact details");
	$definitions_arr[]	=	meta_detail_array("mark_ts","","local","mark_ts_list","checkbox","off","SPOC",array("none"),false,null,null,"");	
	return array(0=>$pd_audit_local,1=>$definitions_arr);
}
/**
 * @param int $user_id
 * @param string $lead_code
 * @param boolean $skip_phone_support_menu
 * 
 * @return string[int][string]
 */
function get_mygofrugal_menu_access($user_id,$lead_code='',$skip_phone_support_menu=false){
	$switched_user_cond = "";
	$skip_menu_con = "";
	if($lead_code!=''){
		$switched_user_cond = " or GRM_ROLE=1 "; //top most role
	}
	$access_lead_str = implode(",",get_accessible_leads($user_id));
	$customer_country_arr = /*. (string[int]) .*/array();
	if($access_lead_str!=''){
		$que1 = " select GLH_LEAD_CODE,GLH_COUNTRY from gft_lead_hdr where GLH_LEAD_CODE in ($access_lead_str) ";
		$res1 = execute_my_query($que1);
		while ($row1 = mysqli_fetch_array($res1)){
			$customer_country_arr[$row1['GLH_LEAD_CODE']] = $row1['GLH_COUNTRY']; 
		}
	}
	
	if(!$skip_phone_support_menu){
		$skip_menu_con=" AND GAM_MENU_KEY NOT IN('phone_support')";
	}
	$return_array		=	/*. (string[int][string]) .*/array();
	$menu_list			=	/*. (string[string]) .*/array();
	$sql_get_menu		=	" select GAM_ID,GAM_MENU_KEY,GAM_COUNTRY_LIST,GRM_MENU_PRIVILEGE from gft_customer_login_master ".
							" join gft_role_app_menu on ( (GRM_ROLE=GCL_ACCESS_ROLE $switched_user_cond) and GRM_STATUS=1) ".
							" join gft_app_menu_master on (GAM_ID=GRM_MENU and GAM_STATUS=1) ".
							" where GAM_MENU_FOR=2 and GCL_USER_ID='$user_id' $skip_menu_con ";
	$result_menu	=	execute_my_query($sql_get_menu);
	while($row_menu=mysqli_fetch_array($result_menu)){
		$menu_name	=	$row_menu['GAM_MENU_KEY'];
		if($menu_name=="ordereasy"){
		    if(count(get_specific_product_installed_customer_ids_from_list(explode(",",$access_lead_str),742))==0){
		        continue;
		    }
		}
		$gam_country_list = trim($row_menu['GAM_COUNTRY_LIST']);
		if($gam_country_list!=''){
			$country_match = false;
			$country_arr = explode(",", $gam_country_list);
			foreach ($country_arr as $one_country){
				if(in_array(trim($one_country), $customer_country_arr)){
					$country_match = true;
				}
			}
			if(!$country_match){
				continue;
			}
		}
		$privilage	=	($row_menu['GRM_MENU_PRIVILEGE']=='W'?array("write"):($row_menu['GRM_MENU_PRIVILEGE']=='R'?array("read"):array()));
		$menu_list["$menu_name"]=	$privilage;
	}
	$return_array[0]=$menu_list;
	if($lead_code!=''){
		$def_query =" select GAM_MENU_KEY,GAM_COUNTRY_LIST from gft_app_menu_master ".
					" join gft_customer_role_master on (GAM_ID=GCR_DEFAULT_MENU) where GCR_ROLE_ID=1 ";
	}else{
		$def_query =" select GAM_MENU_KEY,GAM_COUNTRY_LIST from gft_customer_login_master ".
					" join gft_customer_role_master on (GCR_ROLE_ID=GCL_ACCESS_ROLE and GCA_ROLE_STATUS='1') ".
					" join gft_app_menu_master on (GAM_ID=GCR_DEFAULT_MENU) where GCL_USER_ID='$user_id' ";
	}
	$def_res = execute_my_query($def_query);
	if($def_row = mysqli_fetch_array($def_res)){
		$return_array[1]=$def_row['GAM_MENU_KEY'];
		$gam_country_list = trim($def_row['GAM_COUNTRY_LIST']);
		if($gam_country_list!=''){
			$country_match = false;
			$country_arr = explode(",", $gam_country_list);
			foreach ($country_arr as $one_country){
				if(in_array(trim($one_country), $customer_country_arr)){
					$country_match = true;
				}
			}
			if(!$country_match){
				$return_array[1] = "dashboard"; 
			}
		}
	}	
	return $return_array;
}
/**
 *@param string $audit_id
 *@param string $tag_name
 *@param string $state
 *@param int $vertical_code
 *@param int $product_code
 *@param int $version_code
 *@param string $lead_code
 *@param string $group_id
 *@param boolean $enable_tab
 *@param int $delivery_type
 *@param string $skip_bq_audit
 *@param boolean $disabled
 *@param string $type
 *@param int $recruitment_hdr_id
 *
 *@return string[int][string] $definitions
 */
function get_meta_audit_question($audit_id,$tag_name="",$state='on',$vertical_code=0,$product_code=0,$version_code=0,$lead_code='',$group_id='',$enable_tab=false,$delivery_type=0,$skip_bq_audit="",$disabled=false,$type='',$recruitment_hdr_id=0){
    $definitions_arr=	array();
	$activity_local	=	array();
	$definitions	=	array();
	$customer_answer=	array();
	$sat_result		=	array();
	$sat_status		="";
	$sql_cm_approval	=	get_audit_questions_query((string)$audit_id,(string)$product_code,(string)$vertical_code,(string)$version_code,$group_id,$delivery_type,$skip_bq_audit);
	$res_cm_approval	=	execute_my_query($sql_cm_approval);
	if($audit_id=='15' && $lead_code!=""){
		$customer_audit_id="";
		$query	=	get_customer_answer_for_bq_query($lead_code);
		$result_req=execute_my_query($query."  limit 1");
		while(($data_req=mysqli_fetch_array($result_req))){
			$customer_audit_id=$data_req['GAH_AUDIT_ID'];
		}
		if($customer_audit_id!=""){
			$result_audit_ans=execute_my_query(" select GAQ_QUESTION_TYPE,GAD_AUDIT_QID,GAQ_INPUT_TYPE, GAD_AUDIT_ANS " .
					" from gft_audit_dtl ad  " .
					" join gft_audit_question_master on(GAD_AUDIT_QID=GAQ_QUESTION_ID) " .
					" where ad.GAD_AUDIT_ID=$customer_audit_id ");
			if(mysqli_num_rows($result_audit_ans)>0){
				$data_arr	=	array();
				while($data_audit_ans=mysqli_fetch_array($result_audit_ans)){
					$qid	=	$data_audit_ans['GAD_AUDIT_QID'];
					$customer_answer["$qid"]	=	$data_audit_ans['GAD_AUDIT_ANS'];
				}
			}
		}
		$sql_get_hs	=	execute_my_query("select HW_ID, HW_CUST_ID,HW_STATUS,HW_XML_SYSINFO from gft_hwassessment_info where HW_CUST_ID=$lead_code  order by HW_STATUS desc limit 1");
		if(mysqli_num_rows($sql_get_hs)>0 and $row_bq=mysqli_fetch_array($sql_get_hs)){
			if($row_bq['HW_XML_SYSINFO']!=""){
				$xmlFieldNames	=	array("os_name","harddisk_size","processor","total_ram_size","available_ram_size","system_dynamic_ip","tool_result");
				$sat_result	=	give_parsed_xml($row_bq['HW_XML_SYSINFO'], $xmlFieldNames);
				foreach ($sat_result as $key=>$value){
					$customer_answer["$key"]	=	$value;
				}
				$sat_status=$row_bq['HW_STATUS'];
			}			
		}		
	}
	$partner_profile_arr	=	array();
	if($audit_id=='44' && $lead_code!=""){
		$result_partner_profile	=	execute_my_query("select GPP_QUESTION_ID, GPP_ANSWER from gft_partner_profile_dtl where GPP_LEAD_CODE='$lead_code'");
		while ($row_partner_profile=mysqli_fetch_array($result_partner_profile)){
			$partner_key	=	$row_partner_profile['GPP_QUESTION_ID'];
			$partner_profile_arr["$partner_key"]=$row_partner_profile['GPP_ANSWER'];
		}
	}
	$recruitment_request_arr = $hr_rounds = $language_skills = array();
	if($audit_id=='61' && ($type=="edit" || $type=="view") && $recruitment_hdr_id!=0){
	    // $audit_id = 61 Recruitment request
	    $recruitment_request = execute_my_query("select GRD_MASTER_ID,GRD_VALUE from gft_recruitment_dtl_table where GRD_RECRUITMENT_HDR_ID=$recruitment_hdr_id");
	    while($dtl=mysqli_fetch_assoc($recruitment_request)){
	        $req_qus_id = $dtl["GRD_MASTER_ID"];
	        $rval = $dtl["GRD_VALUE"];
	        $recruitment_request_arr["$req_qus_id"] = $rval;
	        if($req_qus_id=="506"){
	            $hiring_rounds_name = get_single_value_from_single_query("GHM_NAME", "select GHM_NAME from gft_hiring_master where GHM_STATUS=1 and GHM_TYPE=1 and GHM_ID=$rval ");
	            $hr_rounds[] = array("value"=>$rval,"label"=>$hiring_rounds_name);
	        }
	        if($req_qus_id=="500"){
	            $language_skills[] = $rval;
	        }
	    }
	}
	$group_id	=	'';
	$group_name	=	"";	
	$profile_summary_arr	=	array();
	$parnter_editable	=	false;
	while($data=mysqli_fetch_array($res_cm_approval)){
		$question_arr	=	array();
		$profile_summary	=	array();	
		$partner_profile_available=false;
		if($group_id!=$data['GAQ_GROUP_ID'])
		{
			$group_name=$data['GAQ_GROUP_NAME'];
		}
		if($tag_name!=''){
			$group_name=$tag_name;
		}
		$question_id	=	$data['GAQ_QUESTION_ID'];
		$question_desc	=	$data['GAQ_QUESTION_TYPE'];
		$mandatory		=	$data['GAQ_ANS_MANDITORY'];
		$data_answer=$data['GAQ_AVAL_ANSWER'];
		$ansswer_vals_array=explode(',',$data_answer);
		$t_inputtype = (int)$data['GAQ_INPUT_TYPE'];
		$sat_tool_tag	=	trim($data['GAQ_SAT_RASULT_TAG']);
		$question_arr['id']		=	"$question_id";
		if($audit_id=='61'){
		    $question_arr['id']	= "audit_"."$question_id";
		}
		if($data['GAQ_SHOW_LABEL']=='Y'){
			$question_arr['label']	=	"$question_desc";
			$profile_summary['label']=	"$question_desc";
		}
		if($mandatory=='N'){
			$question_arr['validators']	=	array("none");
		}else{
		    $question_arr['validators']	=	array("required");
		}
		if(isset($customer_answer[$question_id]) && $customer_answer[$question_id]!=""){
			if($t_inputtype=='1'){
				$question_arr['value']	=	explode(',',$customer_answer[$question_id]);
			}else{
				$question_arr['value']	=	$customer_answer[$question_id];
			}
		}
		if(isset($partner_profile_arr[$question_id]) && $partner_profile_arr[$question_id]!=""){
			if($t_inputtype=='1'){
				$question_arr['value']	=	explode(',',$partner_profile_arr[$question_id]);
			}else{
				$question_arr['value']	=	$partner_profile_arr[$question_id];
			}
			$partner_profile_available=true;
			$profile_summary['value']	=	$partner_profile_arr[$question_id];
		}
		
		if(isset($recruitment_request_arr[$question_id]) && $recruitment_request_arr[$question_id]!="" ){
		    if($t_inputtype=='1' && $question_id=="500"){
		        $question_arr['value']	=	$language_skills;
		    }else if($question_id=="506"){
		        $question_arr['value'] = $hr_rounds;
		    }else{
		        $question_arr['value']	=	$recruitment_request_arr[$question_id];
		    }
		}
		if(isset($customer_answer[$sat_tool_tag]) && $customer_answer[$sat_tool_tag]!=""){
			$question_arr['value']	=	$customer_answer[$sat_tool_tag];
		}
		$inc	=	0;
		$single_arr	=	array();
		$all_local_arr	=	array();
		while($inc<count($ansswer_vals_array)){
			$single_arr['id']	=	$ansswer_vals_array[$inc];
			$single_arr['name']	=	$ansswer_vals_array[$inc];
			$all_local_arr[]	=	$single_arr;
			$inc++;
		}
		if($disabled){
		    $question_arr['disabled']	= true;
		}
		switch ($t_inputtype){
			case 1://Check Box
				$local_name	=	"audit_".$question_id;
				$activity_local[$local_name]	=	$all_local_arr;
				$question_arr['type']	=	"local";
				$question_arr['lookup']	=	"$local_name";
				$question_arr['options']	=	"checkbox";
				break;
			case 2://Radio
				$local_name	=	"audit_".$question_id;
				$activity_local[$local_name]	=	$all_local_arr;
				$question_arr['type']	=	"local";
				$question_arr['lookup']	=	"$local_name";
				$question_arr['options']	=	"radio";
				break;
			case 3://Combo box
				$local_name	=	"audit_".$question_id;
				$activity_local[$local_name]	=	$all_local_arr;
				$question_arr['type']	=	"local";
				$question_arr['lookup']	=	"$local_name";
				if($audit_id=='61' && $question_id=='506'){
				    $question_arr['options']=	"multi";
				}
				break;
			case 4://Text
				$question_arr['type']	=	"text";
				break;
			case 5://TextArea
				$question_arr['type']	=	"text";
				break;
			case 6://DateTime
				$question_arr['type']	=	"datetime";
				if(!isset($question_arr['validators'])){
					$question_arr['validators']	=	array(array("min"=>date("Y-m-d H:i:s")));
				}
				break;
			case 14://Date
				$question_arr['type']	=	"date";
				if(!isset($question_arr['validators'])){
					$question_arr['validators']	=	array(array("min"=>date("Y-m-d")));
				}
				break;
			case 7://Rating
				$question_arr['type']	=	"rating";
				break;
			case 12://file
			    $question_arr['type']	=	"file";
			    if($audit_id=='61' && $question_id=='495'){
			        $question_arr['value']	=	"";
			    }
			    break;
			case 13://Check List
				$local_name	=	"audit_".$question_id;
				$activity_local[$local_name]	=	$ansswer_vals_array;
				$question_arr['type']	=	"local";
				$question_arr['lookup']	=	"$local_name";
				$question_arr['options']	=	"checklist";
				break;
			case 17://Number
			    $question_arr['type']	=	"number";
			    if($audit_id=='61' && $question_id=='492'){
			        $question_arr['validators']	= array(array("min_value"=>1));
			        $question_arr['disabled']	= true;
			    }
			    break;
			case 18://single
			    $local_name	=	"audit_".$question_id;
			    $question_arr['type']	=	"local";
			    $question_arr['lookup']	=	"$local_name";
			    $question_arr['options']=	"single";
			    break;
			default:
				//Do nothing
				break;
		}
		$question_arr['state']	=	"$state";
		if($question_id=="506"){
		    $question_arr['state']	= "on";
		    $question_arr['filter_key'] = "group_id";
		    $question_arr['parent'] = "job_domain";
		}
		if($question_id=="495" ){
		    $question_arr['single']	 =  true;
		    if(($type=='edit' || $type=='view' )){
		        if($type=='view'){
		            $question_arr['disabled'] = true;
		        }
		        $question_arr['validators']	= array("none");
    		    $file_name = get_single_value_from_single_query("GRD_VALUE", "select GRD_VALUE from gft_recruitment_dtl_table  where GRD_MASTER_ID=495 and GRD_RECRUITMENT_HDR_ID=$recruitment_hdr_id ");
    		    $exploded = explode('/', $file_name);
    		    $name = end($exploded);
    		    $question_arr['file_name']	=  array("$name");
		    }
		}
		if($enable_tab && $data['GAQ_TAB_NAME']!=""){
			$question_arr['tab']=$profile_summary['tab']=	$data['GAQ_TAB_NAME'];
		}
		if($data['GAQ_SHOW_TAG']=="Y"){
			$question_arr['tag']=$profile_summary['tag']=	"$group_name";
		}
		$question_arr['submission_tag']	=	"questions";
		if($audit_id=="61"){
		    $question_arr['submission_tag']	= "recruitment_request_dtl";
		}
		$definitions_arr[]	=	$question_arr;
		if($partner_profile_available){
			$profile_summary['type']	=	"text";
			$profile_summary_arr[]=$profile_summary;
			$parnter_editable	=	true;
		}
	}
	if(($sat_status!="" && $sat_status!="Passed") && $audit_id=='15'){
		$definitions_arr[]=meta_detail_array("sat_tool_status","SAT Result","text","","","off","$group_name",null,true,"","","SAT tool result is not passed");
	}
	if($audit_id=='61'){
	    return $definitions_arr;
	}
	$definitions[0]	=	$activity_local;
	$definitions[1]	=	$definitions_arr;
	$definitions[2]	=	$parnter_editable;
	$definitions[3]	=	$profile_summary_arr;
	return $definitions;
}

/**
 * @param string $registration_id
 * 
 * @return string
 */
function make_valid_registration_id($registration_id){
	$registration_id = trim($registration_id);
	$registration_id = ltrim($registration_id,"<");
	$registration_id = rtrim($registration_id,">");
	$registration_id = str_replace(" ", "", $registration_id);
	return $registration_id;
}

/**
 * @param int $emp_id
 *
 * @return string
 */
function get_emp_device_register_id($emp_id){
	$sql_query="select GEK_GCM_REGISTER_ID from gft_emp_auth_key
	inner join gft_emp_master on(EMP_ID=gem_emp_id and GEM_STATUS='A')
	where GEK_GCM_REGISTER_ID!='' and GEK_DEVICE_STATUS=1 and GEK_STATUS='A' and EMP_ID='$emp_id'";
	$res_device_status	=	execute_my_query($sql_query);
	if(mysqli_num_rows($res_device_status)==1){
		$row=	mysqli_fetch_array($res_device_status);
		return $row['GEK_GCM_REGISTER_ID'];
	}else{
		return "";
	}
}
/**
 * @param string $lead_code
 * @param int $contact_type
 *
 * @return boolean
 */
function is_customer_contact_exist($lead_code, $contact_type=0){
	$where_query	=	"";
	if($contact_type!=0){
		$where_query	=	" and gcc_contact_type=$contact_type";
	}
	$result		=	execute_my_query("select GCC_CONTACT_NO total_count from gft_customer_contact_dtl where GCC_LEAD_CODE=$lead_code $where_query");
	if(mysqli_num_rows($result)==0){
		return false;
	}
	return true;
}
/**
 * @param string $cust_lead_code
 * @param boolean $return_value_array
 *
 * @return mixed[]
 */
function get_customer_outstanding($cust_lead_code,$return_value_array=false){
	$query_order	=	"SELECT GOD_ORDER_NO,GOD_BALANCE_AMT FROM gft_order_hdr where god_lead_code='$cust_lead_code' and god_order_status='A' and GOD_BALANCE_AMT>0";
	$result_order	=	execute_my_query($query_order);
	$return_array	=	array();
	$return_array_object=array();	
	while($row=mysqli_fetch_array($result_order)){
		$order_dtl	=	array();
		$order_dtl['id']	=	$row['GOD_ORDER_NO'];
		$order_dtl['name']	=	$row['GOD_ORDER_NO']." - ".$row['GOD_BALANCE_AMT'];
		$return_array_object[]=$order_dtl;
		$order_dtl['outstanding_amount']	=	$row['GOD_BALANCE_AMT'];
		$return_array[]	=	$order_dtl;
	}
	if($return_value_array){
		return $return_array_object;
	}
	return $return_array;
}
/**
 * @param int $lead_code
 * @param int $type
 * @param boolean $return_count
 * @param boolean $return_quote_no
 * @param boolean $show_all_for_edit
 * 
 * @return string[int][string]
 */
function get_customer_question_proforma_list($lead_code,$type=1,$return_count=false,$return_quote_no=false,$show_all_for_edit=false){
	//if $type is 1=> Quotation, 2=>Proforma
	global $uid;
	$lead_owner = get_single_value_from_single_table("glh_lfd_emp_id", "gft_lead_hdr", "GLH_LEAD_CODE", "$lead_code");
	$country = get_single_value_from_single_table("glh_country", "gft_lead_hdr", "glh_lead_code", $lead_code);
	$quotation_arr=array();
	$order_no = "";
	$sql_query = 	" select GQH_ORDER_NO order_no, GQH_ORDER_AMT order_amt,GEM_EMP_NAME from gft_quotation_hdr  ".
					" INNER JOIN gft_emp_master em ON(GQH_EMP_ID=GEM_EMP_ID)".
					" where  GQH_ORDER_STATUS='A' AND GQH_LEAD_CODE='$lead_code' AND GQH_APPROVAL_STATUS!='4' and GQH_ORDER_AMT>0".
					($lead_owner==$uid || ($show_all_for_edit)?" ":" AND GQH_EMP_ID='$uid'");
	if($type==2){
		$show_sys_generated_proforma_con = "";
		if(!is_authorized_group_list($uid, array (54))){
			$show_sys_generated_proforma_con = " AND GPH_EMP_ID!=9999";
		}
		$sql_query = "select GPH_ORDER_NO order_no,GPH_ORDER_AMT order_amt,GEM_EMP_NAME from gft_proforma_hdr ".
					" INNER JOIN gft_emp_master em ON(GPH_EMP_ID=GEM_EMP_ID)".
					" WHERE GPH_LEAD_CODE='$lead_code'".
					" AND GPH_ORDER_STATUS='A' AND GPH_STATUS='N' AND GPH_ORDER_AMT>0 $show_sys_generated_proforma_con";
	}
	$result = execute_my_query($sql_query);
	$total_count = mysqli_num_rows($result);
	if($return_count){
		return $total_count;
	}
	$usd_inr = get_samee_const('UNIV_PRICE_CONVERSION');
	while($row=mysqli_fetch_array($result)){
		$quotation_dtl['id']=$row['order_no'];
		$amt_disp = $row['order_amt'];
		if(strcasecmp($country,'India')!=0) {
			if($type==1) {
				$amt_disp = $row['order_amt']." - &dollar; ".round($row['order_amt']/$usd_inr,2);
			} else {
				$amt_disp = " &dollar; ".$row['order_amt'];
			}
		}
		$quotation_dtl['name']=$row['order_no']." - AMT: $amt_disp - ".$row['GEM_EMP_NAME'];
		$quotation_dtl['amt'] = $row['order_amt'];
		$quotation_dtl['emp'] = $row['GEM_EMP_NAME'];
		$quotation_arr[]=$quotation_dtl;
		$order_no =$row['order_no'];
	}
	if($return_quote_no){
		return ($total_count==1?$order_no:"");
	}
	return $quotation_arr;
}
/**
 *@param string $type_of_collection
 *@param string $state
 *@param string $lead_code
 *@param int $lead_type
 *
 * @return mixed[]
 */
function collection_metalist($type_of_collection='',$state='off',$lead_code="",$lead_type=1){
	$definitions_arr=	array();
	$is_required_gstin_status_input = false;
	/*Start Collection Block */
	if($type_of_collection!='partner'){
		$return_array = array();
		$check_no_of_quotation = 0;
		$check_no_of_proforma = 0;$quotation_no=$proforma_no="";
		if($lead_code!='' && $lead_type==1){
			$return_array=get_customer_outstanding($lead_code);
			$check_no_of_quotation = get_customer_question_proforma_list($lead_code,1,true);
			$check_no_of_proforma = get_customer_question_proforma_list($lead_code,2,true);
			$quotation_no = get_customer_question_proforma_list($lead_code,1,false,true);
			$proforma_no = get_customer_question_proforma_list($lead_code,2,false,true);
			$is_required_gstin_status_input = check_gstin_update_needed($lead_code);
		}
		$receipt_entry_for	= array();
		$receipt_entry_for['id']	=	"receipt_entry_for";
		$receipt_entry_for['label']	=	"Collection Entry for";
		$receipt_entry_for['type']	=	"local";
		$receipt_entry_for['lookup']=	"receipt_enrty_reason";
		$receipt_entry_for['options']=	"radio";
		$receipt_entry_for['state']	=	"off";
		$receipt_entry_for['tag']	=	"Collection Entry";
		$receipt_entry_for['transitions']=	array(array("type"=>"state","state"=>"on","onValue"=> array("2"),"target"=> array("outstanding_order_num","customer_gstin_status")),array("type"=>"state","state"=>"on","onValue"=> array("1"),"target"=> array("collection_against","customer_gstin_status")));
		if(count($return_array)>0){
			$receipt_entry_for['valid_values']	=	array("2");
			$receipt_entry_for['validation_msg']=	"New Order Advance cannot be placed due to outstanding amount in existing order. So place Outstanding Collection Entry First";
		}
		$definitions_arr[]			=	$receipt_entry_for;		
		$collection_against_value = "";
		if($check_no_of_quotation>0 && $check_no_of_proforma==0){$collection_against_value='1';}
		if($check_no_of_proforma>0 && $check_no_of_quotation==0){$collection_against_value='2';}
		$collection_against		=	array();
		$collection_against['id']	=	"collection_against";
		$collection_against['label']	=	"Collection Against";
		$collection_against['type']		=	"local";
		$collection_against['lookup']	=	"collection_against_list";
		$collection_against['options']	=	"radio";
		$collection_against['state']	=	"$state";
		$collection_against['value']	=	"";
		$collection_against['transitions']=	array(array("type"=>"state","state"=>"on","onValue"=> array("1"),"target"=> array("quotation_collection")),array("type"=>"state","state"=>"on","onValue"=> array("2"),"target"=> array("proforma_collection")));
		$collection_against['tag']	=	"Collection Entry";
		$definitions_arr[]		=	$collection_against;
		
		$quotation_collection['id']		=	"quotation_collection";
		$quotation_collection['label']	=	"Quotation No";
		$quotation_collection['type']	=	"local";
		$quotation_collection['state']	=	"$state";
		$quotation_collection['options']=	"single";
		$quotation_collection['value']=	"";
		$quotation_collection['tag']	=	"Collection Entry";
		$quotation_collection['lookup']	=	"quotation_collection_list";
		$definitions_arr[]				=	$quotation_collection;
		
		$proforma_collection['id']		=	"proforma_collection";
		$proforma_collection['label']	=	"Proforma No";
		$proforma_collection['type']	=	"local";
		$proforma_collection['state']	=	"$state";
		$proforma_collection['options']=	"single";
		$proforma_collection['value']	=	"";
		$proforma_collection['tag']	=	"Collection Entry";
		$proforma_collection['lookup']	=	"proforma_collection_list";
		$definitions_arr[]				=	$proforma_collection;
		
		$outstanding_order_no	=	array();
		$outstanding_order_no['id']		=	"outstanding_order_num";
		$outstanding_order_no['label']	=	"Outstanding Order No";
		$outstanding_order_no['type']	=	"async_combo";
		$outstanding_order_no['state']	=	"$state";
		$outstanding_order_no['options']=	"single";
		$outstanding_order_no['url']	=	"/ismile/get_customer_outstanding_details.php";
		$outstanding_order_no['tag']	=	"Collection Entry";
		$definitions_arr[]				=	$outstanding_order_no;
	}
	

	$receipt_amount		=	array();
	$receipt_amount['id']		=	"receipt_amount";
	$receipt_amount['type']		=	"number";
	$receipt_amount['label']	=	"Collection Amount";
	$receipt_amount['state']	=	"$state";
	$receipt_amount['tag']		=	"Collection Entry";
	$definitions_arr[]			=	$receipt_amount;

	$receipt_remarks			=	array();
	$receipt_remarks['id']		=	"receipt_remarks";
	$receipt_remarks['type']	=	"text";
	$receipt_remarks['label']	=	"Collection Remarks";
	$receipt_remarks['state']	=	"$state";
	$receipt_remarks['tag']		=	"Collection Entry";
	$definitions_arr[]			=	$receipt_remarks;
	if($type_of_collection!='partner'){
		$collection_for				=	array();
		$collection_for['id']		=	"collection_for";
		$collection_for['type']		=	"local";
		$collection_for['options']	=	"multi";
		$collection_for['lookup']	=	"collection_type_list";
		$collection_for['label']	=	"Collection For";
		$collection_for['state']	=	"$state";
		$collection_for['tag']		=	"Collection Entry";
		$definitions_arr[]			=	$collection_for;
	}	
	
	$receipt_type 			=	array();
	$receipt_type['id']		=	"receipt_type";
	$receipt_type['type']		=	"local";
	$receipt_type['lookup']		=	"receipt_type_list";
	$receipt_type['options']	=	"single";
	$receipt_type['label']		=	"Collection Type";
	$receipt_type['transitions']=	array(array("type"=>"state","state"=>"on","onValue"=> array("1"),"target"=> array("cash_cheque_dd_status")),
			array("type"=>"state","state"=>"on","onValue"=> array("2","3"),"target"=> array("cash_cheque_dd_status","cheque_dd_no","cheque_dd_date","bank_name")),
			array("type"=>"state","state"=>"on","onValue"=> array("7"),"target"=> array("utr_no","transfer_date","transferer_name")),
			array("type"=>"state","state"=>"on","onValue"=> array("5"),"target"=> array("transaction_no")));
	$receipt_type['state']		=	"$state";
	$receipt_type['tag']		=	"Collection Entry";
	$definitions_arr[]			=	$receipt_type;

	$cash_cheque_dd_status	=	array();
	$cash_cheque_dd_status['id']	=	"cash_cheque_dd_status";
	$cash_cheque_dd_status['type']	=	"local";
	$cash_cheque_dd_status['lookup']	=	"cash_cheque_dd_status_list";
	$cash_cheque_dd_status['label']	=	"Collection status";
	$cash_cheque_dd_status['parent']=	"receipt_type";
	$cash_cheque_dd_status['filter_key']	=	"parent_id";
	$cash_cheque_dd_status['transitions']	=	array(array( "type"=> "state","state"=> "on","onValue"=> array("D"),"target"=> array("deposited_bank","deposited_branch","deposited_date")),
			array( "type"=> "state","state"=> "on","onValue"=> array("W"),"target"=> array("hand_over_to_emp","hand_over_date")));
	$cash_cheque_dd_status['state']	=	"off";
	$cash_cheque_dd_status['tag']	=	"Collection Entry";
	$definitions_arr[]			=	$cash_cheque_dd_status;

	$deposited_bank	=	array();
	$deposited_bank['id']	=	"deposited_bank";
	$deposited_bank['type']	=	"text";
	$deposited_bank['label']=	"Deposited Bank";
	$deposited_bank['state']=	"off";
	$deposited_bank['tag']	=	"Collection Entry";
	$definitions_arr[]	=	$deposited_bank;

	$deposited_branch	=	array();
	$deposited_branch['id']	=	"deposited_branch";
	$deposited_branch['type']	=	"text";
	$deposited_branch['label']	=	"Deposited Branch";
	$deposited_branch['state']	=	"off";
	$deposited_branch['tag']	=	"Collection Entry";
	$definitions_arr[]	=	$deposited_branch;

	$deposited_date	=	array();
	$deposited_date['id']	=	"deposited_date";
	$deposited_date['type']	=	"date";
	$deposited_date['label']	=	"Deposited Date";
	$deposited_date['state']	=	"off";
	$deposited_date['tag']	=	"Collection Entry";
	$definitions_arr[]	=	$deposited_date;

	$cheque_dd_no			=	array();
	$cheque_dd_no['id']		=	"cheque_dd_no";
	$cheque_dd_no['type']	=	"text";
	$cheque_dd_no['label']	=	"Cheque / DD No";
	$cheque_dd_no['state']	=	"off";
	$cheque_dd_no['tag']	=	"Collection Entry";
	$definitions_arr[]		=	$cheque_dd_no;

	$cheque_dd_date			=	array();
	$cheque_dd_date['id']	=	"cheque_dd_date";
	$cheque_dd_date['type']	=	"date";
	$cheque_dd_date['label']	=	"Cheque / DD Date";
	$cheque_dd_date['state']	=	"off";
	$cheque_dd_date['tag']	=	"Collection Entry";
	$definitions_arr[]		=	$cheque_dd_date;

	$bank_name			=	array();
	$bank_name['id']	=	"bank_name";
	$bank_name['type']	=	"text";
	$bank_name['label']	=	"Bank Name";
	$bank_name['state']	=	"off";
	$bank_name['tag']	=	"Collection Entry";
	$definitions_arr[]		=	$bank_name;

	$hand_over_to_emp	=	array();
	$hand_over_to_emp['id']	=	"hand_over_to_emp";
	$hand_over_to_emp['type']	=	"local";
	$hand_over_to_emp['lookup']	=	"handover_name_list";
	$hand_over_to_emp['label']	=	"Hand Over To";
	$hand_over_to_emp['state']	=	"off";
	$hand_over_to_emp['tag']	=	"Collection Entry";
	$definitions_arr[]		=	$hand_over_to_emp;

	$hand_over_date			=	array();
	$hand_over_date['id']	=	"hand_over_date";
	$hand_over_date['type']	=	"date";
	$hand_over_date['label']=	"Hand Over Date";
	$hand_over_date['state']=	"off";
	$hand_over_date['tag']	=	"Collection Entry";
	$definitions_arr[]		=	$hand_over_date;

	$utr_no			=	array();
	$utr_no['id']	=	"utr_no";
	$utr_no['type']	=	"text";
	$utr_no['label']=	"UTR No";
	$utr_no['state']=	"off";
	$utr_no['tag']	=	"Collection Entry";
	$definitions_arr[]		=	$utr_no;

	$transfer_date			=	array();
	$transfer_date['id']	=	"transfer_date";
	$transfer_date['type']	=	"date";
	$transfer_date['label']	=	"Transferred Date";
	$transfer_date['state']	=	"off";
	$transfer_date['tag']	=	"Collection Entry";
	$definitions_arr[]		=	$transfer_date;

	$transferer_name			=	array();
	$transferer_name['id']		=	"transferer_name";
	$transferer_name['type']	=	"text";
	$transferer_name['label']	=	"Transferer Name / Concern";
	$transferer_name['state']	=	"off";
	$transferer_name['tag']		=	"Collection Entry";
	$definitions_arr[]			=	$transferer_name;

	$transaction_no	=	array();
	$transaction_no['id']	=	"transaction_no";
	$transaction_no['type']	=	"text";
	$transaction_no['label']=	"Unique Transaction ID";
	$transaction_no['state']=	"off";
	$transaction_no['tag']	=	"Collection Entry";
	$definitions_arr[]		=	$transaction_no;
	if($type_of_collection!='partner' && ($is_required_gstin_status_input)){
	    $gstin_status_type	=	array();
	    $gstin_status_type['id']	=	"customer_gstin_status";
	    $gstin_status_type['type']	=	"local";
	    $gstin_status_type['label']=	"Customer GSTIN Status";
	    $gstin_status_type['lookup']=	"customer_gstin_status_list";
	    $gstin_status_type['state']=	"off";
	    $gstin_status_type['tag']	=	"Collection Entry";
	    $gstin_status_type['transitions']=	array(array("type"=>"state","state"=>"on","onValue"=> array("3"),"target"=> array("gstin_update_date")));
	    $definitions_arr[]		=	$gstin_status_type;
	    
	    $gstin_update_date = array();
	    $gstin_update_date['id']	=	"gstin_update_date";
	    $gstin_update_date['type']	=	"date";
	    $gstin_update_date['label']=	"Expected date of updating GSTIN";
	    $gstin_update_date['state']=	"off";
	    $gstin_update_date['validators']	=	array(array("min"=>date("Y-m-d")),array("max"=>date("Y-m-d", strtotime("10 days"))));
	    $gstin_update_date['tag']	=	"Collection Entry";
	    $definitions_arr[]		=	$gstin_update_date;
	}
	return $definitions_arr;
}
/**
 * @param int $emp_id
 * @param string[int] $prospect_status_arr
 *
 * @return string[string]
 */
function get_partner_details($emp_id,$prospect_status_arr=array('10','12','14')){
	$partner_details	=	/*. (string[string]) .*/array();
	if((is_array($prospect_status_arr)) && count($prospect_status_arr)>0) {
		$prospect_status_cond = " and lh.GLH_PROSPECTS_STATUS in (".implode(",",$prospect_status_arr).") ";
	} else {
		$prospect_status_cond = '';
	}
	$sql_get_partner_dtl=	" select CGI_EMP_ID,CGI_LEAD_CODE, CGI_EFF_DATE, CGI_VALIDITY, glh_cust_name,cgi_incharge_emp_id,cgi_pcs_incharge_emp_id,".
							" GLH_TERRITORY_ID,GCA_TAX_MODE,GPL_TAG_ID, GLS_SUBTYPE_NAME,GPL_INCLUSIVE_OF_TAX,GCA_BOOKING_LIMIT,".
							" GLH_PARTNER_PD_INCHARGE,GLH_OTHER_PARTNER_PD_INCHARGE,GCA_CP_SUB_TYPE from gft_cp_info ci ".
							" inner join gft_emp_master em on(em.gem_emp_id=ci.CGI_EMP_ID) ".
							" inner join gft_lead_hdr lh on(lh.glh_lead_code=ci.CGI_LEAD_CODE) ".
							" inner join gft_leadcode_emp_map le on(le.GLEM_EMP_ID=em.gem_emp_id) ".
							" inner join gft_cp_agree_dtl gc on(CGI_LEAD_CODE=gca_lead_code	and CGI_CP_AGREENO=gca_cp_agreeno) ".
							" join gft_lead_subtype_master lst on (GLS_SUBTYPE_CODE=GCA_CP_SUB_TYPE) ".
							" left join gft_price_list_master pl on (GPL_ID=CGI_PRICE_LIST_ID) ".
							" left join gft_price_tag_master pt on (GPL_PRICE_LIST_ID=GPL_ID and GPL_STATUS='A') ".
							" where em.gem_status='A' $prospect_status_cond and CGI_EMP_ID=$emp_id".
							" order by glh_cust_name ";
	$result	=	execute_my_query($sql_get_partner_dtl);
	while($row=mysqli_fetch_array($result)){
		$partner_details['name']		=	$row['glh_cust_name'];
		$partner_details['lead_code']	=	$row['CGI_LEAD_CODE'];
		$partner_details['validity']	=	$row['CGI_VALIDITY'];
		$partner_details['territory_id']=	$row['GLH_TERRITORY_ID'];
		$partner_details['tax_mode']	=	$row['GCA_TAX_MODE'];
		$partner_details['tag_id']		=	$row['GPL_TAG_ID'];
		$partner_details['inclusive_of_tax']	=	$row['GPL_INCLUSIVE_OF_TAX'];
		$partner_details['booklimit']	=	$row['GCA_BOOKING_LIMIT'];
		$partner_details['gft_rc_emp_id']	=	$row['cgi_pcs_incharge_emp_id'];
		$partner_details['partner_rc_emp_id']	=	$row['GLH_PARTNER_PD_INCHARGE'];
		$partner_details['other_partner_rc_emp_id']	=	$row['GLH_OTHER_PARTNER_PD_INCHARGE'];
		$partner_details['partner_type_id']	=	$row['GCA_CP_SUB_TYPE'];
	}
	return $partner_details;
}
/**
 *
 * @return mixed[]
 */
function get_handover_to_emp_list(){	
	$handover_emp_list		=	get_emp_list_by_group_filter(array(5,6,17,68),false,'A');
	$single_handover_name	=	array();
	$handover_name_list		=	array();
	for($i=0;$i<count($handover_emp_list);$i++){
		$single_handover_name['id']		=	$handover_emp_list[$i][0];
		$single_handover_name['name']	=	$handover_emp_list[$i][1];
		$handover_name_list[]			=	$single_handover_name;
	}
	return $handover_name_list;
}
/**
 * @param int $mobile_uid
 * @param string $receive_arg
 * 
 * @return string
 */
function validate_partner_get_lead_code($mobile_uid,$receive_arg){
	$current_date	=	date('Y-m-d');
	$partner_details	=	get_partner_details($mobile_uid,null);
	$partner_lead_code	=	isset($partner_details['lead_code'])?$partner_details['lead_code']:'';
	$validity_date		=	isset($partner_details['validity'])?$partner_details['validity']:'';
	if($partner_lead_code==''){
		sendErrorWithCode($receive_arg,"You are not a partner, contact partner management team.",HttpStatusCode::BAD_REQUEST);
		exit;
	}
	if(strtotime($validity_date)<strtotime($current_date)){
		sendErrorWithCode($receive_arg,"Agreement expired, contact partner management team.",HttpStatusCode::BAD_REQUEST);
		exit;
	}
	return $partner_lead_code;
}
/**
 * @param int $emp_id
 *
 * @return boolean
 */
function check_is_partner($emp_id){
	$is_partner		=	false;
	$partner_details	=	get_partner_details($emp_id);
	if(isset($partner_details['lead_code']) and $partner_details['lead_code']!=''){
		$is_partner		=	true;
	}
	return $is_partner;
}
/**
 * @param string $uid
 * @param string $partner_id
 * @param boolean $is_partner
 * @param boolean $is_partner_emp
 *
 * @return string
 */
function get_assign_followup_emp_details_query($uid,$partner_id,$is_partner,$is_partner_emp){
	$partner_emp_query	=	"";
	$add_partner_id		=	"";
	if($is_partner or $is_partner_emp){
		$cp_id	=$uid;
		if($partner_id!='' and $partner_id!=$uid){
			$cp_id	=	$partner_id;
			$add_partner_id=	" OR em.GEM_EMP_ID=$partner_id";
		}
		$partner_emp_query	=	" UNION ALL(select em.GEM_EMP_ID, em.GEM_EMP_name, em.GEM_STATUS from gft_emp_master em " .
				" join gft_emp_reporting ger on (ger_emp_id=gem_emp_id and ger_reporting_empid=$cp_id) " .
				" where gem_status='A' AND em.GEM_EMP_ID!=$uid group by gem_emp_id) ";
	}
	$sql_get_data   	=	"select em.GEM_EMP_ID, em.GEM_EMP_name, em.GEM_STATUS from gft_emp_master em ".
			" left join gft_role_group_master rg on (grg_role_id=gem_role_id) ".
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=em.gem_emp_id) ".
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id) ".
			" left join gft_emp_manager_relation on(gmr_emp_id=em.gem_emp_id)  ".
			" where 1 and em.gem_status='A' AND em.GEM_EMP_ID!=$uid and ( ggm_group_id in (97,5,6,62,20,27,34,35,12,23,13) $add_partner_id) group by gem_emp_id $partner_emp_query order by gem_emp_name";
	return $sql_get_data;
}

/**
 * @param string $lead_str
 *
 * @return string
 */
function get_asa_period_sub_query($lead_str){
	$query= " select GLH_LEAD_CODE as lead,ifnull(min(GID_PREV_EXPIRY_DATE),'0000-00-00') as start_date from gft_lead_hdr ".
			" join gft_install_dtl_new on (GLH_LEAD_CODE=GID_LEAD_CODE) ".
			" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
			" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
			" where GLH_LEAD_CODE in ($lead_str) and GPM_LICENSE_TYPE!=3 and GPM_IS_INTERNAL_PRODUCT!=2 and GID_STATUS!='U' group by GLH_LEAD_CODE ";
	return $query;
}

/**
 * @param string $uid
 * @param string $assign_to_employee
 * @param int $assign_incharge
 * @param string $receive_arg
 * 
 * @return void
 */
function validate_assign_followup_owner($uid,$assign_to_employee,$assign_incharge,$receive_arg){
	$partner_employee	=	check_is_partner_employee((int)$assign_to_employee);
	$partner_employee_login	=	check_is_partner_employee((int)$uid);
	if(isset($partner_employee['partner_id']) and $partner_employee['partner_id']!='' and $assign_incharge==1 and $partner_employee['partner_id']==$uid){
		sendErrorWithCode($receive_arg,"You can not assign your Employee/Downstream Partner as a lead incharge.",HttpStatusCode::BAD_REQUEST);
		exit;
	}else if((check_is_partner((int)$uid)) and $assign_incharge==2 and ((isset($partner_employee['partner_id']) and $partner_employee['partner_id']!=$uid) or (is_gft_employee($assign_to_employee)) or ($uid!=$assign_to_employee and (check_is_partner((int)$assign_to_employee))))){
		sendErrorWithCode($receive_arg,"You can not assign GFT/Other Partner Employee as a Responsible to Customer.",HttpStatusCode::BAD_REQUEST);
		exit;
	}else if(isset($partner_employee_login['partner_id']) and $partner_employee_login['partner_id']!='' and $assign_incharge==2 and $assign_to_employee!=$partner_employee_login['partner_id'] and 
	((is_gft_employee($assign_to_employee)) or (isset($partner_employee['partner_id']) and $partner_employee_login['partner_id']!=$partner_employee['partner_id']) or 
	((check_is_partner((int)$assign_to_employee))))){
		sendErrorWithCode($receive_arg,"You can not assign GFT/Other Partner Employee as a Responsible to Customer.",HttpStatusCode::BAD_REQUEST);
		exit;
	}else if(isset($partner_employee['partner_id']) and $partner_employee['partner_id']!='' and $assign_incharge==1 and (!isset($partner_employee_login['partner_id']))){
		sendErrorWithCode($receive_arg,"You can not assign Partner Employee/Downstream Partner as a lead incharge.",HttpStatusCode::BAD_REQUEST);
		exit;
	}
}
/**
 * @param int $emply_id
 * @param string $report_date
 * 
 * @return string
 */
function pcs_activity_history($emply_id,$report_date){
	$i=0;
	$pcs_activity_history = "<b>Activity History:</b><br><br><table border=1 cellspacing=2 cellpading=2>".
	                        "<tr><th>S.No.</th><th>Visit Date</th><th>Customer Name</th><th>Milestone Name</th><th>Note on Activity</th><th>Planned/UnPlanned</th><th>Duration(Hrs)</th><th>Commercial Classification</th><th>Emotion</th><th>Approver Name</th></tr>";
	$query_activity="select GLD_VISIT_DATE,GLH_CUST_NAME,GMM_NAME,GLD_IS_PLANNED,GLD_TIME_DURATION,GLD_NOTE_ON_ACTIVITY,GCCM_NAME,em2.GEM_EMP_NAME as approver_name,GCM_EMOTION_NAME,GLD_EMOTION_REMARKS from gft_activity ".
				    "join gft_emp_master em1 on (GLD_EMP_ID=em1.GEM_EMP_ID) ".
				    "join gft_lead_hdr on (gld_lead_code=glh_lead_code) ".
					"join gft_pcs_milestone_master on (GLD_MILESTONE=GMM_ID) ".
					"join gft_commercial_classification_master on (GCCM_ID=GLD_COMMERCIAL_CLASSIFICATION) ".
					"left join gft_emp_master em2 on (GLD_REVIEW_OWNER=em2.GEM_EMP_ID AND em2.GEM_STATUS='A') ".
					"join gft_customer_emotion_master on (GCM_EMOTION_ID=GLD_EMOTION)".
					"where GLD_EMP_ID=$emply_id and GLD_VISIT_DATE='$report_date' order by GLD_IS_PLANNED";
	$activity_res=execute_my_query($query_activity);
	while($activity=mysqli_fetch_array($activity_res)){
		$i++;
		$is_planned=$activity['GLD_IS_PLANNED'];
		$pcs_activity_history .="<tr><td>$i</td><td>".$activity['GLD_VISIT_DATE']."</td><td>".$activity['GLH_CUST_NAME']."</td><td>".$activity['GMM_NAME']."</td><td>".$activity['GLD_NOTE_ON_ACTIVITY']."</td><td>".(($is_planned==1)?"Planned":"UnPlanned")."</td><td>".$activity['GLD_TIME_DURATION']."</td>".
								"</td><td>".$activity['GCCM_NAME']."</td><td>".$activity['GCM_EMOTION_NAME'].'-'.$activity['GLD_EMOTION_REMARKS']."</td>";
		$pcs_activity_history .= "<td>".$activity['approver_name']."</td></tr>";
	}
	$pcs_activity_history .="</table>";
	if($i>0){
		return $pcs_activity_history;
	}else{
		return "<b>Activity History :</b>You have not entered your Activity Details in SAM";
	}
}

/**
 * @param int $length
 * 
 * @return string
 */
function get_unique_auth_key($length=10){
	$authkey = '';
	$is_valid = 0;
	while($is_valid<1){
		$authkey = generate_keygen($length);
		$sql_check_duplicate =	execute_my_query("select GCL_USER_ID from gft_customer_login_master where GCL_AUTH_KEY='$authkey'");
		if(mysqli_num_rows($sql_check_duplicate)==0){
			$is_valid++;
		}
	}
	return $authkey;
}

/**
 * @param int $length
 * 
 * @return string
 */
function get_unique_referral_code($length=5){
	$user_referral_code='';
	$is_unique = 0;
	while($is_unique<1){
		$user_referral_code = generate_keygen($length,false);
		$sql_check_duplicate =	execute_my_query("select GCL_MY_REFERRAL_CODE from gft_customer_login_master where GCL_MY_REFERRAL_CODE='$user_referral_code'");
		if(mysqli_num_rows($sql_check_duplicate)==0){
			$is_unique++;
		}
	}
	return $user_referral_code;
}

/**
 * @param string $employee_id
 * @param string $contact
 * 
 * @return int
 */
function check_and_return_customer_user_id($employee_id, $contact){
	$gcl_user_id = (int)get_single_value_from_single_table("GCL_USER_ID", "gft_customer_login_master", "GCL_EMP_ID", $employee_id);
	if($gcl_user_id!=0){
		return $gcl_user_id;
	}
	
	$gcl_user_id = (int)get_single_value_from_single_table("GCL_USER_ID", "gft_customer_login_master", "GCL_USERNAME", $contact);
	if($gcl_user_id!=0){
		execute_my_query("update gft_customer_login_master set GCL_EMP_ID='$employee_id',GCL_ACCESS_ROLE=6 where GCL_USERNAME='$contact'");
		return $gcl_user_id;
	}
	$referral_code = get_unique_referral_code(5);
	$insert_query = " insert into gft_customer_login_master (GCL_USERNAME,GCL_USER_STATUS,GCL_ACCESS_ROLE,GCL_CREATED_DATE, ".
					" GCL_UPDATED_DATE,GCL_MY_REFERRAL_CODE,GCL_EMP_ID) values ".
					" ('$contact','1','6',now(),now(),'$referral_code','$employee_id') ";
	$ins_res = execute_my_query($insert_query);
	if($ins_res){
		$gcl_user_id = mysqli_insert_id_wrapper();
		return $gcl_user_id;
	}
	return 0;
}

/**
 * @return string[string]
 */
function get_jio_payment_config(){
	$test_mode = get_samee_const('STORE_PAYMENT_TEST_MODE'); // 1 - test,  0 - live 
	if($test_mode=='0'){
		$retarr['client_id'] 	= "10000181";
		$retarr['merchant_id'] 	= "100001000056867";
		$retarr['checksum_seed']= "RpdAXiamX4WNcTv4lcMguGNGb3654BGwKN0egre6TmaxpK3vo4wgqKYHXnDBeakFV39aExbCRxlju0zSmE9I4hTxOPl9037G8LgcrgHi0qivhfa3uL6CmdhIh6jAwvTE1hbtudrg56bbWfFadjzwI78ZVpMJ8IbuWKTgZ8O7nFig9YfHE2fNS7bgcrV43owxSRDGJUbVRARRlBoJLOWgGNkx5ihXbMZed3Vm5ebzeKIQxLzyZWoOHJ1rymcxwKtM";
		$retarr['submit_url']		= "https://pp2pay.jiomoney.com/reliance-webpay/v1.0/jiopayments";
		$retarr['status_query_url'] = "https://pp2pay.jiomoney.com/reliance-webpay/v1.0/payment/status";
		$retarr['nonpayment_url'] 	= "https://pp2bill.jiomoney.com:8443/Services/TransactionInquiry";
	}else{
		$retarr['client_id'] 	= "10000002";
		$retarr['merchant_id'] 	= "100001000014146";
		$retarr['checksum_seed']= "tSLfi8BMxohvWJTCfwd1cZuARH78myF21JAdgdNhvixbj7o6+uIA38WFm7VHQ0aGu8LyQYv8tRPyN+Ba0+nRLuBLZXK4PH2gxkSvJ7Jnhof0NJr3IktRPSUZIQi6hcFZxoElSuOD8KzxCkagJfxT/u4vLeGqdskKl3p46RxJAJvOp7VutGHK2MG1HE7X68E/cKJrEUk7v0vI+kUp2Mfyh7GE8wZ9enYBrkY6olGYS9pLHLv/zGAZzAZVadblK1+3";
		$retarr['submit_url']		= "https://testpg.rpay.co.in/reliance-webpay/v1.0/jiopayments";
		$retarr['status_query_url'] = "https://testpg.rpay.co.in/reliance-webpay/v1.0/payment/status";
		$retarr['nonpayment_url'] 	= "https://testbill.rpay.co.in:8443/Services/TransactionInquiry";
	}
	return $retarr;	
}

/**
 * @param string $video_id
 *
 * @return string[int][string]
 */
function get_mapped_video_tags_in_array($video_id){
	$return_arr = /*. (string[int][string]) .*/array();
	$select_query = " select master.GVT_ID, master.GVT_DISPLAY_NAME from gft_video_tag_mapping map ".
			" join gft_video_tag_master master on (master.GVT_ID=map.GVT_TAG_ID) where map.GVT_VIDEO_ID='$video_id' ";
	$select_result = execute_my_query($select_query);
	while($row_data = mysqli_fetch_array($select_result)){
		$tag_arr['id'] 	= $row_data['GVT_ID'];
		$tag_arr['name']= $row_data['GVT_DISPLAY_NAME'];
		$return_arr[]  = $tag_arr;
	}
	return $return_arr;

}
/**
 * @param string $email
 *
 * @return int
 */
function get_emp_id_using_email($email){
	$result	=	execute_my_query("SELECT GEM_EMP_ID FROM gft_emp_master where GEM_EMAIL='$email'");
	if(mysqli_num_rows($result)==1 and $row=mysqli_fetch_array($result)){
		return (int)$row['GEM_EMP_ID'];
	}else{
		return 0;
	}
}
/**
 * @param int $uid
 *
 * @return string
 */
function get_salesperson_working_state($uid){
	$state_code	=	"";
	$sql_get_emp_state	=	" select st.GPM_MAP_NAME 'State' from (gft_pincode_master pi,gft_political_map_master pm,gft_political_map_master st, gft_political_map_master ct)".
			" left join gft_business_territory_master bm on bm.GBT_TERRITORY_ID=pi.GPM_TERRITORY_ID".
			" left join gft_emp_master on (GBT_SALES_INCHARGE=GEM_EMP_ID) ".
			" where GBT_SALES_INCHARGE=$uid and pm.gpm_map_id=gpm_district_id and pm.GPM_MAP_TYPE='D' and pm.GPM_MAP_PARENT_ID=st.gpm_map_id".
			" and st.GPM_MAP_TYPE='S' and st.GPM_MAP_PARENT_ID=ct.gpm_map_id and ct.GPM_MAP_TYPE='C' and".
			" ct.gpm_map_id='2' group by gpm_pincode,pm.gpm_map_name ,GBT_TERRITORY_NAME,st.GPM_MAP_NAME,".
			" ct.GPM_MAP_NAME,GBT_IMAGE_URL ORDER BY state,gpm_location_name limit 1 ";
	$result_get_emp_state	=	execute_my_query($sql_get_emp_state);
	if(mysqli_num_rows($result_get_emp_state)==1 and $row_state=mysqli_fetch_array($result_get_emp_state)){
		$state_code	=	trim($row_state['State']);
	}
	return $state_code;
}

/**
 * @param string[int] $access_lead_arr
 * 
 * @return string[int][string]
 */
function get_mapped_outlet_dtl_array($access_lead_arr){
	$cust_list 		 = /*. (string[int][string]) .*/array();
	$access_lead_str = implode(',', $access_lead_arr);
	if($access_lead_str!=''){
		$sel_que = "select GLH_LEAD_CODE, concat(GLH_CUST_NAME,'-',ifnull(GLH_CUST_STREETADDR2,'')) as cust_name from gft_lead_hdr where GLH_LEAD_CODE in ($access_lead_str) ";
		$sel_res = execute_my_query($sel_que);
		while($row1 = mysqli_fetch_array($sel_res)){
			$single_arr['id'] 	= $row1['GLH_LEAD_CODE'];
			$single_arr['name'] = $row1['cust_name'];
			$cust_list[] 		= $single_arr;
		}
	}
	return $cust_list;
}

/**
 * @param int $employee_id
 * @param string $cond_date
 * @param boolean $include_emp_calls
 *
 * @return string[string]
 */
function get_customer_call_activity_details_in_array($employee_id,$cond_date,$include_emp_calls=false){
	$return_arr = /*. (string[string]) .*/array();
	$emp_cond = " and (GTC_EMP_ID is null or GTC_EMP_ID=0) ";
	if($include_emp_calls) {
		$emp_cond = "";
	}
	$cond_date = date('Y-m-d',strtotime($cond_date));
	$que1 = " select count(if(GTC_ACTIVITY_ID is null,1,null)) as pending_activity from gft_techsupport_incomming_call ".
			" where GTC_AGENT_ID='$employee_id' $emp_cond and GTC_CALL_STATUS not in (3,5) ". //OM call status skipped
			" and GTC_SPECIFIC_REASON='vsmile' and GTC_DATE between '$cond_date 00:00:00' and '$cond_date 23:59:59' ".
			" and GTC_ASSIGN_TO=0 and (GTC_MAIN_GROUP!=706 or GTC_RECALL_STATUS='NR') and GTC_OFFICE_ID!=3 ";
	$res1 = execute_my_query($que1);
	if($data1 = mysqli_fetch_array($res1)){
		$return_arr['pending_activity'] = $data1['pending_activity'];
	}
	return $return_arr;
}

/**
 * @param string $lead_code
 * @param boolean $send_product_group
 *
 * @return mixed[]
 */
function get_customer_primary_chat_group($lead_code,$send_product_group=true){
	$chat_group    =    array();
	$first_product_group="";
	$prod_query=    " select GLE_ASA_STATUS,GLH_CALL_PREFERANCE from gft_lead_hdr ".
			" inner join gft_lead_hdr_ext on(gle_lead_code=glh_lead_code) ".
			" where glh_lead_code=$lead_code";
	$prod_res = execute_my_query($prod_query);
	$chat_res = execute_my_query("select GCG_ID,GCG_ZOHO_DEPT_CODE,    GCG_GROUP_NAME from gft_chat_group_master where GCG_ID=3");
	$row_chat_group=mysqli_fetch_array($chat_res);
	$chat_group['id']    =    $row_chat_group['GCG_ID'];
	$chat_group['name']    =    $row_chat_group['GCG_ZOHO_DEPT_CODE'];
	$chat_group['dept_name']    =    $row_chat_group['GCG_GROUP_NAME'];
	if(mysqli_num_rows($prod_res)==0){//Pre-sales chat group
		return $chat_group;
	}
	$row_chat_dtl    =    mysqli_fetch_array($prod_res);
	$asa_status        =    $row_chat_dtl['GLE_ASA_STATUS'];
	$chat_preferance=    $row_chat_dtl['GLH_CALL_PREFERANCE'];
	if($chat_preferance=='' && $row_chat_group=mysqli_fetch_array($chat_res)){//if call preferance not available, send Pre-sales chat group
		return $chat_group;
	}
	if($asa_status=="Expired" && $send_product_group){//if call preferance not available, send Annuity chat group
		$chat_res = execute_my_query("select GCG_ID,GCG_ZOHO_DEPT_CODE,    GCG_GROUP_NAME from gft_chat_group_master where GCG_ID=8");
		$row_chat_group=mysqli_fetch_array($chat_res);
		$chat_group['id']    =    $row_chat_group['GCG_ID'];
		$chat_group['name']    =    $row_chat_group['GCG_ZOHO_DEPT_CODE'];
		$chat_group['dept_name']    =    $row_chat_group['GCG_GROUP_NAME'];
		return $chat_group;
	}
	if(($asa_status=="Valid" || !$send_product_group) and $chat_preferance!=''){
		$call_ref    =    json_decode($chat_preferance);
		$product_groups="";
		foreach ($call_ref as $key=> $value){
			$res_group    =    execute_my_query("select GVG_PRODUCT from gft_voicenap_group where GVG_GROUP_ID=$value");
			if(mysqli_num_rows($res_group)>0 && $row=mysqli_fetch_array($res_group)){
				$pcode=$row['GVG_PRODUCT'];
				$pcode=str_replace("200-05.0", "200-06.0", $pcode);
				$pcode=str_replace("600-06.0", "601-06.0", $pcode);
				$product_groups    =    $product_groups.($product_groups!=''?",'":"'").$pcode."'";
				if($first_product_group==""){$first_product_group=$pcode;}
			}
		}
		if($product_groups!=""){
			$chat_res = execute_my_query("select GCG_ID,GCG_ZOHO_DEPT_CODE,    GCG_GROUP_NAME from gft_chat_group_master where GCG_PRODUCT in($product_groups) limit 1");
			if(mysqli_num_rows($chat_res)>0){
				$row_chat_group=mysqli_fetch_array($chat_res);
				$chat_group['id']    =    $row_chat_group['GCG_ID'];
				$chat_group['name']    =    $row_chat_group['GCG_ZOHO_DEPT_CODE'];
				$chat_group['dept_name']    =    $row_chat_group['GCG_GROUP_NAME'];
			}
		}
	}
	if(!$send_product_group && $first_product_group!=""){
		$chat_group['product_group']    =    $first_product_group;
		return $chat_group;
	}
	return $chat_group;
}
/**
 * @param string $customer_id
 *
 * @return string[string]
 */
function get_cust_installed_base_product($customer_id){
	$product_dtl = array();
	$main_prod_res  = execute_my_query("select GID_LIC_PCODE PRODUCT_CODE, SUBSTRING(GID_LIC_PSKEW,1,4) PRODUCT_GROUP, ".
			" GPG_PRODUCT_ALIAS PRODUCT_ALIAS,GID_CURRENT_VERSION from gft_install_dtl_new ".
			" JOIN gft_product_group_master pg ON(GID_LIC_PCODE=gpg_product_family_code AND  gpg_skew=SUBSTRING(GID_LIC_PSKEW,1,4)) ".
			" JOIN gft_product_family_master pf on(GID_LIC_PCODE=pf.GPM_PRODUCT_CODE AND GPM_IS_BASE_PRODUCT='Y') ".
			" JOIN gft_product_master pm ON(GID_LIC_PCODE=pm.GPM_PRODUCT_CODE AND GID_LIC_PSKEW=pm.GPM_PRODUCT_SKEW)".
			" where GID_LEAD_CODE=$customer_id and GID_STATUS!='U' order by GPM_LICENSE_TYPE, GID_VALIDITY_DATE DESC LIMIT 1");
	if($row1 = mysqli_fetch_array($main_prod_res)){
		$product_dtl['product_code'] = $row1['PRODUCT_CODE'];
		$product_dtl['product_skew'] = $row1['PRODUCT_GROUP'];
		$product_dtl['product_alias'] = $row1['PRODUCT_ALIAS'];
		$product_dtl['product_version'] = $row1['GID_CURRENT_VERSION'];
	}
	return $product_dtl;
}
/**
 * @param string $customer_id
 *
 * @return string[string]
 */
function get_customer_installed_product_for_complaint($customer_id){
	$product_dtl = array();
	$main_prod_que = 	" select GPC_PRODUCT_ID from gft_lead_hdr ".
			" INNER JOIN gft_product_company_mapping ON(GLH_MAIN_PRODUCT=GPC_SUPPORT_GROUP or GLH_MAIN_PRODUCT=GPC_PD_GROUP) ".
			" where GLH_LEAD_CODE='$customer_id' limit 1";
	$main_prod_res = execute_my_query($main_prod_que);
	if($row1 = mysqli_fetch_array($main_prod_res)){
		$product_dtl = explode('-', $row1['GPC_PRODUCT_ID']);
		$product_dtl['product_code'] = $product_dtl[0];
		$product_dtl['product_skew'] = isset($product_dtl[1])?$product_dtl[1]:"";
	}else{
		$product_dtl = get_cust_installed_base_product($customer_id);
	}
	return $product_dtl;
}
/**
 * @param string $cust_user_id
 * @param string $secret
 *
 * @return mixed[]
 */
function get_customer_chat_groups($cust_user_id, $secret){
	$chat_group    =    array();
	$enable_phone_support_menu = false;
	$access_lead_arr = get_accessible_leads($cust_user_id);
	$access_lead_str = implode(',', $access_lead_arr);
	$chatbot_url = get_single_value_from_single_table("GGM_URL", "gft_gst_menu_card_master", "GGM_ID", "6");
	if($access_lead_str==''){
		return $chat_group;
	}
	$result_cust_list    =    execute_my_query(" select GLH_LEAD_CODE, GLH_CUST_NAME,GLH_CUST_STREETADDR2,GLH_LEAD_TYPE,". 
	                                           " GLE_SUPPORT_MODE, GLE_UNIQUE_AC_NO from gft_lead_hdr ".
												" INNER JOIN gft_lead_hdr_ext ON(GLE_LEAD_CODE=glh_lead_code)".
												" where glh_lead_code in($access_lead_str)");
	while($row=mysqli_fetch_array($result_cust_list)){
		$chat_dtl    =    array();
		$chat_dtl['lead_code']    =    $lead_code    =    $row['GLH_LEAD_CODE'];
		
		$chat_dtl['cust_name']    	=    stripslashes($row['GLH_CUST_NAME']."-".$row['GLH_CUST_STREETADDR2']);
		$chat_dtl['virtual_acc_no'] =    (trim($row['GLE_UNIQUE_AC_NO'])==""?null:$row['GLE_UNIQUE_AC_NO']);
		$chat_dtl['phone_support']	=    ((($row['GLH_LEAD_TYPE']==1 || $row['GLH_LEAD_TYPE']==3) && $row['GLE_SUPPORT_MODE']==2)?true:false);
		if(($row['GLH_LEAD_TYPE']==1 || $row['GLH_LEAD_TYPE']==3) && $row['GLE_SUPPORT_MODE']==2){
			$enable_phone_support_menu=true;
		}
		$cust_encript_data = "custid=$lead_code&mygofrugal_user_id=$cust_user_id";
		$chat_dtl['chatbot_url'] = $chatbot_url."?product_data=".urlencode(lic_encrypt($cust_encript_data, $secret));
		$installed_products_dtl = execute_my_query("select GID_LIC_PCODE from gft_install_dtl_new ".
		    " where GID_LEAD_CODE=$lead_code and GID_STATUS != 'U'");
		$installed_products = array();
		while ($row1 = mysqli_fetch_array($installed_products_dtl)){
		    $installed_products[] = $row1['GID_LIC_PCODE'];
		}
		$chat_dtl['installed_products'] = $installed_products;
		$base_product = get_cust_installed_base_product($lead_code);
		$chat_dtl['base_product'] = isset($base_product['product_alias'])?$base_product['product_alias']:null;
		$chat_group[]    =    $chat_dtl;
	}
	return array($chat_group,$enable_phone_support_menu);
}
/**
 * @param string $cust_user_id
 *
 * @return mixed[]
 */
function get_user_accessible_leads($cust_user_id){
	$outlets_list	 =    array();
	$access_lead_arr = get_accessible_leads($cust_user_id);
	$access_lead_str = implode(',', $access_lead_arr);	
	if($access_lead_str==''){
		return $outlets_list;
	}
	$result_cust_list    =    execute_my_query("select GLH_LEAD_CODE, GLH_CUST_NAME,GLH_CUST_STREETADDR2,GLE_GST_NO,GLE_GST_NO_STATUS_FOR_EDIT,GLE_GST_ELIGIBLE,GLE_BUSINESS_NAME from gft_lead_hdr ".
												" INNER JOIN gft_lead_hdr_ext ON(glh_lead_code=gle_lead_code)".
												"  where glh_lead_code in($access_lead_str)");
	while($row=mysqli_fetch_array($result_cust_list)){
		$cust_dtl    =    array();	
		$lead_code = $row['GLH_LEAD_CODE'];
		$cust_dtl['cust_id']    =   $lead_code;
		$cust_dtl['cust_name']  =   stripslashes($row['GLH_CUST_NAME']." - ".$row['GLH_CUST_STREETADDR2']);	
		$cust_dtl['location']	=	$row['GLH_CUST_STREETADDR2'];
		$cust_dtl['gstin_no']	=	($row['GLE_GST_NO']==""?null:$row['GLE_GST_NO']);
		$cust_dtl['is_gstin']	=	($row['GLE_GST_ELIGIBLE']==1?true:($row['GLE_GST_ELIGIBLE']==2?false:null));
		$cust_dtl['business_name']	=	$row['GLE_BUSINESS_NAME'];
		$cust_dtl['can_edit']	=($row['GLE_GST_NO_STATUS_FOR_EDIT']=='3'?false:true);
		$cust_contact 			= 	get_contact_dtl_inarray($row['GLH_LEAD_CODE'], "1", "1");
		$cust_dtl['cust_contact']=	$cust_contact;		
		if(count($cust_contact)==0){
			$cust_dtl['message']=	"Proprietor Mobile number is not found for GSTIN verification. Kindly update the same in Security Manager in POS or Contact GoFrugal";
		}
		$outlets_list[]    		=   $cust_dtl;
	}
	return $outlets_list;
}
/**
 * @param string $target
 *
 * @return mixed[]
 */
function get_rating_lebel_msg($target){
	$modification	=	array(
			array("type"=>"label_mapper","onValue"=>array(
					"1"=>"What went wrong?",
					"2"=>"What went wrong?",
					"3"=>"Where do you think we should improve?",
					"4"=>"Tell us how we can improve.",
					"5"=>"Share your happy experience"
			),"target"=>array("$target")),
			array("type"=>"mandatory","onValue"=>array("1","2"),"target"=>array("$target"))
	);
	return $modification;
}
/**
 * @param string $rating_value
 *
 * @return string
 */
function get_rating_submit_msg($rating_value){
	$msg	=	"That is poor from us. Let us look into it, we will get back to you to win 5 stars.";
	if($rating_value=='2'){$msg="Sorry we did bad. We will get back to you and serve better.";}
	if($rating_value=='3'){$msg="Oh! That's just OK.";}
	if($rating_value=='4'){$msg="Thanks! We will do better and win 5 stars.";}
	if($rating_value=='5'){$msg="Ah Great! We are glad we made you happy!";}
	return $msg;
}
/**
 * @param string $lead_code
 * 
 * @return string
 */
function get_product_delivery_audit_log_query($lead_code){
	$sql_pd_audit	=	" select GAH_AUDIT_ID,GAT_AUDIT_DESC as title,GAH_AUDIT_TYPE, GAH_MY_COMMENTS as description, GEM_EMP_NAME AS audit_by, ".
			" GAH_DATE_TIME AS audit_on,GAH_ORDER_AUDIT_STATUS, GAH_PENDING_IMP,GAH_I_ASSURE_STATUS,GAH_MILESTONE_STATUS, ".
			" GAH_CM_APPROVAL_STATUS,GAH_HANDOVER_STATUS, GAH_TRAINING_STATUS,GAH_REFFERNCE_ORDER_NO,GAH_ORDER_NO,GPD_ID,GPD_ORDER_TYPE from gft_audit_hdr ".
			" inner join gft_audit_type_master on(GAH_AUDIT_TYPE=GAT_AUDIT_ID) ".
			" inner join gft_emp_master em on(GEM_EMP_ID=GAH_AUDIT_BY) ".
			" inner join gft_order_hdr on(GAH_ORDER_NO=GOD_ORDER_NO) ".
			" left join gft_product_delivery_hdr pd ON(GPD_ID=GAH_REFFERNCE_ORDER_NO)".
			" where GAH_LEAD_CODE='$lead_code' AND GAH_REFFERNCE_ORDER_NO!=''  AND GAH_REFFERNCE_ORDER_NO=GAH_OPCODE AND GAH_AUDIT_TYPE NOT IN(18,22,23,21,46,47) ".
			" order by GOD_ORDER_DATE desc, GAH_REFFERNCE_ORDER_NO desc, GAH_DATE_TIME desc";
	return $sql_pd_audit;
}
/**
 * @param string $encrypt_value
 * @param string $secret
 * 
 * @return string[int]
 */
function get_mygofrugal_app_userid($encrypt_value,$secret){
	$response_arr = /*. (string[int]) .*/array();
	$response_arr[0]=true;
	$decrypt_value=lic_decrypt($encrypt_value, $secret);
	if($decrypt_value!=""){
		$cust_dtl	=	/*. (string[string]) .*/json_decode($decrypt_value,true);
		$lead_code		= isset($cust_dtl['cust_id'])?(int)$cust_dtl['cust_id']:"";
		$pos_userid		= isset($cust_dtl['pos_userid'])?(int)$cust_dtl['pos_userid']:"";
		$response_arr[1]= $lead_code;
		$response_arr[2]= $pos_userid;
		$result_audit	=	execute_my_query(get_product_delivery_audit_log_query($lead_code));
		if(mysqli_num_rows($result_audit)==0){
			$response_arr[0]=false;
			$response_arr[2]="Assure delivery details not available in our CRM.";
			return $response_arr;
		}
		$result_con	=	execute_my_query("select gcc_id from gft_customer_contact_dtl inner join gft_pos_users on(gcc_id=GPU_CONTACT_ID) where GCC_LEAD_CODE='$lead_code' AND GPU_USER_ID='$pos_userid'");
		if(mysqli_num_rows($result_con)==0){
			$response_arr[0]=false;
			$response_arr[2]="Your contact is not registered in our CRM. Please update the correct contact details in Security Manager in POS and check the Support antenna status in POS";
			return $response_arr;
		}
	}
	return $response_arr;
}
/**
 *
 * @return mixed[]
 */
function get_next_action_mandatory_list(){
	$next_action_mandatory_list	=	array();
	$result_next_action_mandatory= execute_my_query("select gps_status_id from gft_prospects_status_master where gps_next_action_mandatory='Y' and gps_status='A'");
	while($row_next_action=mysqli_fetch_array($result_next_action_mandatory)){
		$next_action_mandatory_list[]	=	$row_next_action['gps_status_id'];
	}
	return $next_action_mandatory_list;
}
/**
 *
 * @return mixed[]
 */
function get_assign_to_option_list(){
	$option_list = array(array("id"=>"1","name"=>"Yes"),array("id"=>"0","name"=>"No"));
	return $option_list;
}
/**
 *
 * @return string
 */
function get_appointment_time_validation(){
	$validation_date_time = date('Y-m-d H:i:s',strtotime(date('Y-m-d')." 19:31:00"));
	if(strtotime(date('H:i:s',date(/*.(string).*/mktime(11,0,0)))) - strtotime(date('H:i:s')) > 0) {
		$time_stamp = mktime(19,31,0);
		$prev_day = strtotime('-1 day',date("$time_stamp"));
		$validation_date_time = date('Y-m-d H:i:s',$prev_day);
	}
	return $validation_date_time;
}
/**
 * @param string $employee_id
 * @param string $report_date
 * 
 * @return string[int]
 */
function get_gstin_validation_dtl($employee_id,$report_date){
    $return_array = array(0=>"",1=>"");
    $validation_date = date("Y-m-d", strtotime($report_date. ' + 3 days'));
    $result = execute_my_query("select GROUP_CONCAT(DISTINCT GRD_LEAD_CODE) ALL_LEADS, GRD_EXPECTED_TO_UPDATE_GSTIN,". 
            " if(datediff(GRD_EXPECTED_TO_UPDATE_GSTIN, '$report_date')=0,'validation','info') GSTIN_DTL from gft_receipt_dtl". 
            " where GRD_GSTIN_UPDATE_STATUS=3 AND GRD_ENTRY_BY_EMP_ID=$employee_id and GRD_EXPECTED_TO_UPDATE_GSTIN<='$validation_date'". 
            " GROUP BY GSTIN_DTL");
    while($row=mysqli_fetch_assoc($result)){
        if($row['GSTIN_DTL']=='validation'){
            $return_array[0] = $row['ALL_LEADS'];
        }else{
            $return_array[1] = $row['ALL_LEADS'];
        }
    }
    return $return_array;
}
/**
 * @param string $uid
 * @param string $type
 *
 * @return string
 */
function get_pending_approval_app_not_installation($uid, $type='validation'){
    $date	=	(date('Y-m-d H:i:s',strtotime("-24 hours",strtotime(date('Y-m-d H:i:s')))));
    $date_con = " AND GAN_REQUEST_ON>='$date' ";
    if($type=='validation'){
        $date_con = " AND GAN_REQUEST_ON<'$date' ";
    }
    $query =" select GROUP_CONCAT(GAN_LEAD_CODE) cust_ids, IF(glh_lfd_emp_id>7000 AND glh_lfd_emp_id NOT IN(9999,9998), cgi_incharge_emp_id, glh_lfd_emp_id ) cm_emp_id from gft_app_not_install_approval ".
        " INNER JOIN gft_lead_hdr lh ON(GLH_LEAD_CODE=GAN_LEAD_CODE) ".
        " left join gft_cp_info on (glh_lfd_emp_id=CGI_EMP_ID ) ".
        " where IF(glh_lfd_emp_id>7000 AND glh_lfd_emp_id NOT IN(9999,9998), cgi_incharge_emp_id, glh_lfd_emp_id )=$uid ".
        " AND GAN_STATUS=1 $date_con group by cm_emp_id";
    $result_rows=execute_my_query($query);
    if(mysqli_num_rows($result_rows)>0 && $row=mysqli_fetch_array($result_rows)){
        return $row['cust_ids'];
    }else{
        return "";
    }
}
/**
 * @param int $type
 * @param int $employee_id
 * @param string $report_date
 * @param boolean $count_all_reminders
 * @param boolean $igonre_gft_leads
 * @param boolean $ignore_planned
 * @param string $validation_date_time
 * @param string $from_date
 * @param string $prospect_from_date
 * @param boolean $is_tomorrow_pan
 * @param string[int] $skip_nature
 *
 * @return mixed[]
 */
function get_pending_followup_appointment($type, $employee_id,$report_date='',$count_all_reminders=false,$igonre_gft_leads=true,
		$ignore_planned=false,$validation_date_time=NULL,$from_date='',$prospect_from_date='',$is_tomorrow_pan=false,$skip_nature=null){
	$date_now	=	date("Y-m-d");
	$appointment_col = " ad.GLD_LEAD_CODE ";
	$followup_col = " GCF_LEAD_CODE ";
	$lead_type_cond = "";
	if($igonre_gft_leads) {
		$lead_type_cond = " AND GLH_LEAD_TYPE!=8 ";
	}
	if($count_all_reminders) {
		$appointment_col = " ad.GLD_ACTIVITY_ID ";
		$followup_col = " GCF_FOLLOWUP_ID ";
	}
	$plan_wh = $app_join_qry = $follow_join_qry = $support_join_qry = "";
	if($ignore_planned){
		$plan_wh = " and gtr_reminder_id is null ";
		$chk_date = $report_date;
		if($chk_date==''){
			$chk_date = date('Y-m-d',strtotime($validation_date_time));
		}
		$app_join_qry = " left join gft_tomorrow_plan_next_action_relation on (gtr_reminder_id=ad.GLD_ACTIVITY_ID and gtr_reminder_type=3 and GTR_PLAN_ID in (select GTH_ID from gft_tomorrow_plan_hdr where GTH_EMP_ID='$employee_id' and GTH_PLAN_DATE='$chk_date' and GTH_PLAN_STATUS='A') ) ";
		$follow_join_qry = " left join gft_tomorrow_plan_next_action_relation on (gtr_reminder_id=gcf_followup_id and gtr_reminder_type=1) and GTR_PLAN_ID in (select GTH_ID from gft_tomorrow_plan_hdr where GTH_EMP_ID='$employee_id' and GTH_PLAN_DATE='$chk_date' and GTH_PLAN_STATUS='A') ";
		$support_join_qry= " left join gft_tomorrow_plan_next_action_relation on (gtr_reminder_id=GCH_COMPLAINT_ID and gtr_reminder_type=2 and GTR_PLAN_ID in (select GTH_ID from gft_tomorrow_plan_hdr where GTH_EMP_ID='$employee_id' and GTH_PLAN_DATE='$chk_date' and GTH_PLAN_STATUS='A')) ";
	}
	$total_count=	0;
	$appt_add_date_cond = $foll_add_date_cond = "";
	$from_date = db_date_format($from_date);
	$prospect_from_date = db_date_format($prospect_from_date);
	if($from_date!='') {
		$appt_add_date_cond .= " and ad.GLD_NEXT_ACTION_DATE>='$from_date' ";
		$foll_add_date_cond .= " and GCF_FOLLOWUP_DATE>='$from_date' ";
	}
	if($prospect_from_date!=''){
		$appt_add_date_cond .= " and if(glh_status=3,ad.GLD_NEXT_ACTION_DATE='$prospect_from_date',1) ";
		$foll_add_date_cond .= " and if(glh_status=3,GCF_FOLLOWUP_DATE='$prospect_from_date',1) "; 
	}
	$pre_sales_daily_report_join = "LEFT JOIN gft_activity ad1 ON (ad.GLD_LEAD_CODE=ad1.GLD_LEAD_CODE AND ad.GLD_ACTIVITY_ID>ad1.GLD_ACTIVITY_ID AND ad1.GLD_VISIT_NATURE!=99 )";
	$pre_sales_daily_report_field = " datediff(now(),max(ad1.GLD_DATE)) last_act_days, ad.GLD_DATE, max(ad1.GLD_DATE) as das, ";
	if($is_tomorrow_pan){
		$pre_sales_daily_report_join = "";
		$pre_sales_daily_report_field="";
	}
	$other_wh = "";
	if( is_array($skip_nature) && (count($skip_nature) > 0) ){
		$other_wh .= " and ad.GLD_VISIT_NATURE not in (".implode(",", $skip_nature).") ";
	}
	$sql_appointment =  " select  ad.GLD_ACTIVITY_ID, ad.gld_lead_code, $pre_sales_daily_report_field".
			" ad.GLD_VISIT_NATURE visit_nature,GLH_CONTACT_VERIFIED,GCC_LEAD_CREATION_TYPE from gft_activity ad".
			" INNER JOIN gft_lead_hdr lh on(ad.GLD_LEAD_CODE=glh_lead_code $lead_type_cond)".
			" INNER JOIN gft_lead_create_category lc ON(GCC_ID=GLH_CREATED_CATEGORY)".
			" join gft_activity_master gam on ( ad.GLD_NEXT_ACTION=gam.GAM_ACTIVITY_ID ) ".
			" inner join gft_activity_master gam1 on ( gam1.gam_activity_id=ad.gld_visit_nature ) ".
			" INNER JOIN gft_lead_hdr_ext on (gle_lead_code = ad.gld_lead_code)".
			" $pre_sales_daily_report_join".
			$app_join_qry.
			" where ad.GLD_EMP_ID='$employee_id' $appt_add_date_cond $plan_wh $other_wh ";
	if($type==2){//For taking followup
		$sql_appointment = " select count(distinct($followup_col)) tot_pending,group_concat(distinct GCF_LEAD_CODE) lead_codes from gft_cplead_followup_dtl".
				" INNER JOIN gft_lead_hdr lh on(GCF_LEAD_CODE=glh_lead_code $lead_type_cond )".
				" INNER JOIN gft_lead_create_category lc ON(GCC_ID=GLH_CREATED_CATEGORY )".
				" join gft_activity_master gam on ( gcf_followup_action=gam_activity_id ) ".
				" INNER JOIN gft_lead_hdr_ext on (gle_lead_code = gcf_lead_code) ".
				" INNER JOIN gft_activity ad on (GCF_ACTIVITY_REF = ad.GLD_ACTIVITY_ID) ".
				$follow_join_qry.
				" WHERE gcf_assign_to='$employee_id' $foll_add_date_cond $plan_wh ";
	}
	if($type==3) { // Support tickets
		$sql_appointment =" select count(GCH_COMPLAINT_ID) tot_pending from gft_customer_support_hdr  ".
				" join gft_customer_support_dtl on (GCH_COMPLAINT_ID=GCD_COMPLAINT_ID and gch_last_activity_id=gcd_activity_id) ".
				" join gft_lead_hdr lh on (GCH_LEAD_CODE=GLH_LEAD_CODE) ".
				" join gft_emp_master on (GEM_EMP_ID=GCD_PROCESS_EMP) ".
				" join gft_status_master sm on (gcd_status=GTM_CODE and GTM_STATUS='A') ".
				" join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE) ".
				" join gft_status_group_master on (GMG_GROUP_ID=GTM_GROUP_ID) ".
				" left join gft_data_quality on (GDQ_REMINDER_TYPE=2 and GDQ_REF_ID=gcd_activity_id) ".
				$support_join_qry.
				" where sm.prob=1 and GCD_PROCESS_EMP='$employee_id' $plan_wh ";
	}
	$outer_whr_cond = "";
	if($report_date!="") {
		$from_dt = date('Y-m-d',strtotime($report_date));
		$is_intl_team = is_authorized_group_list($employee_id, null,3);
		if($type==1) {
		    $sql_appointment .= " and ad.GLD_SCHEDULE_STATUS in (1,3,7) ";
		    if($is_intl_team){
		        $sql_appointment .= " and if(lh.GLH_STATUS=26,ad.GLD_NEXT_ACTION_DATE<='$from_dt',ad.GLD_NEXT_ACTION_DATE='$from_dt') ";
		    }else{
		        $sql_appointment .= " and ad.GLD_NEXT_ACTION_DATE='$from_dt' ";
		    }
			
		} else if($type==2) {
			$sql_appointment .= " and gcf_followup_date='$from_dt' AND gcf_followup_status in (1,3,7) ";
		} else {
			$sql_appointment .= " and date(GCD_SCHEDULE_DATE)='$from_dt' ";
		}
		if(!is_null($validation_date_time)) {
			$validation_date_time = db_date_format($validation_date_time);
			$sql_appointment .= " and ad.GLD_DATE<'$validation_date_time' ";
		}
		if($is_intl_team){
		    $sql_appointment .= " and (lh.GLH_CONTACT_VERIFIED=2 or GLH_PAID_CAMPAIGN!='0000-00-00') ";
		}
	} else {
		$whr_cond = "";
		if(!$is_tomorrow_pan){
			$outer_whr_cond = " AND GLH_CONTACT_VERIFIED=2 ";
			if(!is_null($validation_date_time)) {
				$validation_date_time = db_date_format($validation_date_time);
				$whr_cond .= " and ad.GLD_DATE<'$validation_date_time' ";
			}
		}else{
			$date_now = date('Y-m-d',strtotime($validation_date_time));
		}
		if($type==1) {
			$sql_appointment .= " and ad.GLD_NEXT_ACTION_DATE<='$date_now' AND ad.GLD_SCHEDULE_STATUS in (1,3,7) $whr_cond " ;
		} else if($type==2) {
			$sql_appointment .= " and gcf_followup_date<='$date_now' AND gcf_followup_status in (1,3,7) $whr_cond ";
		} else {
			$sql_appointment .= " and GCD_SCHEDULE_DATE <= '$date_now 23:59:59' ";
		}
	}
	if($type==1) {
		 $sql_appointment = "select count(distinct(outer_tbl.GLD_ACTIVITY_ID)) tot_pending,group_concat(distinct outer_tbl.gld_lead_code) lead_codes from($sql_appointment group by ad.GLD_LEAD_CODE) outer_tbl where (1) $outer_whr_cond ";
	}
	$ret_array = array();
	$result_count	=	execute_my_query($sql_appointment);
	$ret_array['lead_codes'] = "";
	if(mysqli_num_rows($result_count)>0 && $row_count=mysqli_fetch_array($result_count)){
		$total_count	=	(int)$row_count['tot_pending'];
		$ret_array['count'] = "$total_count";
		if(isset($row_count['lead_codes'])) {
			$ret_array['lead_codes'] .= $row_count['lead_codes'];
		}
	}
	return $ret_array;
}

/**
 * @param string $employee_id
 * @param string $date
 * @param string $purpose
 * 
 * @return mixed[]
 */
function get_planned_tasks_dtl($employee_id,$date,$purpose) {
	$dt = db_date_format($date);
	$planned_tasks = array();
	$plan_que = " select GTH_ID,GTH_TASK_ID,GPT_NAME,GTH_START_TIME,pout.GTD_VALUE outcome,GTH_STATUS,GTH_END_TIME, ".
				" gtr_reminder_type,GFT_COMPLAINT_DESC,GTM_GROUP_ID,GNT_ALIAS,GEM_EMP_NAME from gft_tomorrow_plan_hdr ".
				" join gft_tomorrow_plan_tasks_master on (GTH_TASK_ID = GPT_ID and GPT_STATUS='1') ".
				" join gft_emp_master on (GEM_EMP_ID=GTH_EMP_ID) ".
				" left join gft_tomorrow_plan_next_action_relation on (GTH_ID=GTR_PLAN_ID and GTH_PLAN_STATUS='A' and GTR_MAIN_PLAN=1) ".
				" left join gft_notification_type_master on (GNT_ID=GTR_REMINDER_TYPE) ".
				" left join gft_customer_support_hdr on (GCH_COMPLAINT_ID=gtr_reminder_id and gtr_reminder_type=2 ) ".
				" left join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE) ".
				" left join gft_status_master on (GTM_CODE=gch_current_status) ".
				" left join gft_tomorrow_plan_dtl pout on (GTD_PLAN_ID=GTH_ID and GTD_FIELD_ID=4) ".
				" left join gft_tomorrow_plan_joint_exec_dtl on (GTJ_PLAN_ID=GTH_ID) ".
				" where GTH_PLAN_DATE = '$dt' and GTH_PLAN_STATUS='A' ";
	if($purpose=='joint_activities'){
		$plan_que .= " and GTJ_JOINT_EXEC_ID = '$employee_id' and GTJ_REQUEST_STATUS=1 ";
	}else{
		$plan_que .= " and GTH_EMP_ID = '$employee_id'";
	}
	$plan_que .= " order by GTH_START_TIME ";
	$plan_res = execute_my_query($plan_que);
	while($row = mysqli_fetch_array($plan_res)) {
		$plan_dtls = /*. (string[string]) .*/array();
		$plan_id = $row['GTH_ID'];
		$gpt_name = $row['GPT_NAME'];
		$reminder_type = $row['gtr_reminder_type'];
		$plan_dtls["id"] 			= $plan_id;
		$plan_dtls["task_name"] 	= $gpt_name;
		$plan_dtls["start_time"] 	= $row['GTH_START_TIME'];
		$plan_dtls["duration_in_mins"]= (strtotime($row['GTH_END_TIME']) - strtotime($row['GTH_START_TIME']))/60;
		$plan_dtls["end_time"] 		= $row['GTH_END_TIME'];
		$plan_dtls['description'] 	= $row['outcome']; // Collect this detail for different tasks
		$plan_dtls["status"] 		= $row['GTH_STATUS'];
		$plan_dtls["reminder_type"] = $row['GNT_ALIAS'];
		$plan_dtls['action']		= ($row['GTH_TASK_ID']=='13')?"confirmation":"form";
		$plan_dtls['task_id']		= $row['GTH_TASK_ID'];
		$plan_dtls["header_title"] 	= $gpt_name;
		$plan_dtls['requested_by']	= $row['GEM_EMP_NAME'];
		$plan_dtls["grouped_tasks"]	= array();
		if($reminder_type=='2'){
			$plan_dtls["task_name"] = "Support - ".$row['GFT_COMPLAINT_DESC'];
			$plan_dtls["status"] 	= ($row['GTM_GROUP_ID']=='3')?'2':'1';
		}
		$sql1 = " select GTR_REMINDER_TYPE,GTR_REMINDER_ID,GNT_ALIAS,GLH_CUST_NAME,GLD_NOTE_ON_ACTIVITY, ".
				" if(gtr_reminder_type=1,GCF_FOLLOWUP_DETAIL,GLD_NEXT_ACTION_DETAIL) as act_detail, ".
				" if(gtr_reminder_type=1,GCF_FOLLOWUP_STATUS,GLD_SCHEDULE_STATUS) as rem_status ".
				" from gft_tomorrow_plan_next_action_relation ".
				" join gft_tomorrow_plan_hdr on (GTH_ID=GTR_PLAN_ID and GTH_PLAN_STATUS='A') ".
				" join gft_notification_type_master on (GNT_ID=GTR_REMINDER_TYPE) ".
				" left join gft_cplead_followup_dtl on (gtr_reminder_id=gcf_followup_id and gtr_reminder_type=1) ".
				" left join gft_activity on (gtr_reminder_id=GLD_ACTIVITY_ID and gtr_reminder_type=3) ".
				" left join gft_lead_hdr on (GLH_LEAD_CODE=if(gtr_reminder_type=1,GCF_LEAD_CODE,GLD_LEAD_CODE)) ".
				" where GTR_PLAN_ID='$plan_id' ";
		$res1 = execute_my_query($sql1);
		if(mysqli_num_rows($res1) > 1){ //group plan
			$group_plan_status = "2";
			while ($data1 = mysqli_fetch_array($res1)){
				$act_detail = $data1['act_detail'];
				$field_arr['rem_id'] 		= $data1['GTR_REMINDER_ID'];
				$field_arr['rem_type'] 		= $data1['GNT_ALIAS'];
				$field_arr['task_name'] 	= $data1['GLH_CUST_NAME'];
				$field_arr['description'] 	= ($act_detail=='')?$data1['GLD_NOTE_ON_ACTIVITY']:$act_detail;
				$field_arr['status'] 		= $data1['rem_status'];
				$plan_dtls["grouped_tasks"][]	= $field_arr;
				if($data1['rem_status']=="1"){
					$group_plan_status = "1";
				}
			}
			$plan_dtls["task_name"] 	= "Group Plan - ".$gpt_name;
			$plan_dtls['status']		= $group_plan_status;
		}
		$planned_tasks[] = $plan_dtls;
	}
	return $planned_tasks;
}

/**
 * @param int $dealer_id
 * 
 * @return int[string]
 */
function get_inhand_qty_for_dealer($dealer_id){
	$sql1 = " select sum(GMO_ORDERED_QTY-GMO_USED_QTY) as inhand, ".
			" if(GOP_PRODUCT_SKEW like '%SALR','salr',if(GOP_PRODUCT_SKEW like '%CALR','calr',GFT_SKEW_PROPERTY)) as prop_val ".
			" from gft_order_hdr join gft_mp_order_hdr on (GOD_ORDER_NO=GMO_ORDER_NO) ".
			" join gft_order_product_dtl on (GOP_ORDER_NO=GMO_ORDER_NO and GMO_FULLFILLMENT_NO=GOP_FULLFILLMENT_NO) ".
			" join gft_product_master on (GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" where GMO_PARTNER_ID='$dealer_id' and GMO_ORDER_TYPE='dealer' and GOD_ORDER_STATUS='A' ".
			" group by prop_val ";
	$res1 = execute_my_query($sql1);
	$server_lic = $client_lic = $server_alr = $client_alr = 0;
	while($row1 = mysqli_fetch_array($res1)){
		$skew_prop = $row1['prop_val'];
		if($skew_prop=='1'){
			$server_lic = (int)$row1['inhand'];
		}else if($skew_prop=='3'){
			$client_lic = (int)$row1['inhand'];
		}else if($skew_prop=='salr'){
			$server_alr = (int)$row1['inhand'];
		}else if($skew_prop=='calr'){
			$client_alr = (int)$row1['inhand'];
		}
	}
	$ret_arr['server_lic'] = $server_lic;
	$ret_arr['client_lic'] = $client_lic;
	$ret_arr['server_alr'] = $server_alr;
	$ret_arr['client_alr'] = $client_alr;
	return $ret_arr;
}
/**
 * @param string $platform
 * 
 * @return int
 */
function get_platform_id($platform){
	$platform = strtolower($platform);
	$platform_val=0;
	switch ($platform){
		case 'android':$platform_val=1;break;
		case 'ios': $platform_val=2;break;
		default: $platform_val=0;
	}
	return $platform_val;
}

/**
 * @param string $emp_id
 * @param string $month
 * @param string $year
 *
 * @return boolean
 */
function is_plan_saved($emp_id,$month,$year){
	$que1 = " select GTS_EMP_ID from gft_target_scorecard_metrics ".
			" where GTS_EMP_ID='$emp_id' and GTS_MONTH=$month and GTS_YEAR='$year' ";
	$res1 = execute_my_query($que1);
	if(mysqli_num_rows($res1) > 0){
		return true;
	}
	return false;

}

/**
 * @param string $emp_id
 * @param string $plan_date
 * 
 * @return string
 */
function get_last_task_end_time($emp_id,$plan_date){
	$que1 = " select DATE_FORMAT(GTH_END_TIME,'%H:%i') as end_time from gft_tomorrow_plan_hdr ".
			" where GTH_EMP_ID='$emp_id' and GTH_PLAN_DATE='$plan_date' and GTH_PLAN_STATUS='A' order by GTH_ID desc limit 1 ";
	$res1 = execute_my_query($que1);
	if($row1 = mysqli_fetch_array($res1)){
		return $row1['end_time'];
	}
	return '';
}
/**
 * @param string $cust_id
 * @param string $message
 * @param string $error_code
 * @param string $web_request_log_id
 *
 * @return void
 */
function send_error_response_toweb_with_code($cust_id,$message,$error_code,$web_request_log_id){
	header('X-PHP-Response-Code: '.$error_code, true, $error_code);
	$error['message']=$message;
	echo json_encode($error);
	insert_web_request_log($cust_id,"10","$web_request_log_id",$error);
	exit;
}
/**
 * @param string $access_lead_str
 * @param string $tag_id
 *
 * @return string
 */
function update_customer_video_tags($access_lead_str,$tag_id=''){
	$tag_id_value_arr = /*. (string[string][string]) .*/array();
	$all_product_tags='';
	$separator_pro='';
	$select_tag = execute_my_query(" select GVT_ID,GVT_CATEGORY,GVT_VALUE from gft_video_tag_master where GVT_STATUS=1 ");
	while($data1 = mysqli_fetch_array($select_tag)){
		$tag_id_value_arr[$data1['GVT_CATEGORY']][$data1['GVT_VALUE']] = $data1['GVT_ID'];
	}

	$insert_column_str = " replace into gft_lead_tag_mapping (GLT_LEAD_CODE,GLT_TAG_ID,GLT_UPDATED_DATETIME) values ";
	$insert_values_str = "";
	$query_lead=" select GLH_LEAD_CODE,GLH_VERTICAL_CODE, GCL_ID, group_concat(GID_LIC_PCODE,'-',SUBSTR(GID_LIC_PSKEW,1,4)) as products from gft_lead_hdr lh ".
			" join gft_vertical_master on (GTM_VERTICAL_CODE=GLH_VERTICAL_CODE) ".
			" join gft_customer_status_master cs on (cs.GCS_CODE=lh.GLH_STATUS) " .
			" left join gft_prospects_status_master gps on (lh.GLH_STATUS=3 and GPS_STATUS_ID=GLH_PROSPECTS_STATUS and GPS_STATUS='A' ) ".
			" left join gft_customer_lifecycle_master cl1 on(cl1.GCL_ID=ifnull(GPS_CUST_LIFECYCLE,GCS_CUST_LIFECYCLE)) ".
			" left join gft_install_dtl_new on (GID_LEAD_CODE=GLH_LEAD_CODE and GID_STATUS!='U') ".
			" where GLH_LEAD_CODE in ($access_lead_str) group by GLH_LEAD_CODE ";
	$query_result = execute_my_query($query_lead);
	while($row1 = mysqli_fetch_array($query_result)){
		$glh_lead_code = $row1['GLH_LEAD_CODE'];
		$vertical_code_tag = isset($tag_id_value_arr['2'][$row1['GLH_VERTICAL_CODE']])?$tag_id_value_arr['2'][$row1['GLH_VERTICAL_CODE']]:'';
		$cust_lifecycle_tag= isset($tag_id_value_arr['3'][$row1['GCL_ID']])?$tag_id_value_arr['3'][$row1['GCL_ID']]:'';
		$separator = ($insert_values_str=='')?"":",";
		if($tag_id==""){
			$insert_values_str .= $separator." ('$glh_lead_code','$vertical_code_tag',now()), ('$glh_lead_code','$cust_lifecycle_tag',now()) ";
		}else{
			$insert_values_str .= $separator."('$glh_lead_code','$tag_id',now()) ";
		}		
		$products_arr = explode(",",$row1['products']);
		foreach ($products_arr as $val){
			$prod_tag = isset($tag_id_value_arr['1'][$val])?$tag_id_value_arr['1'][$val]:'';
			$insert_values_str .= ", ('$glh_lead_code','$prod_tag',now()) ";
			if($prod_tag!="" && $prod_tag!="0"){
				$all_product_tags .=$separator_pro."$prod_tag";
				$separator_pro=",";
			}			
		}
	}
	if($insert_values_str!=""){
		execute_my_query($insert_column_str."".$insert_values_str);
	}
	return "$all_product_tags";
}

/**
 * @param string $plan_id
 * @param string $employee_id
 * @param int $template_id
 * 
 * @return void
 */
function send_joint_activity_accept_reject_notification($plan_id,$employee_id,$template_id){
	$sql1 = " select GTH_EMP_ID,e1.GEM_EMP_NAME as planner_name,e2.GEM_EMP_NAME as joint_emp_name, GPT_NAME ".
			" from gft_tomorrow_plan_hdr join gft_emp_master e1 on (GTH_EMP_ID=e1.GEM_EMP_ID) ".
			" join gft_tomorrow_plan_tasks_master on (GPT_ID=GTH_TASK_ID) ".
			" join gft_tomorrow_plan_joint_exec_dtl on (GTJ_PLAN_ID=GTH_ID) ".
			" join gft_emp_master e2 on (GTJ_JOINT_EXEC_ID=e2.GEM_EMP_ID) ".
			" where GTH_ID='$plan_id' and GTJ_JOINT_EXEC_ID='$employee_id' ";
	$res1 = execute_my_query($sql1);
	if($data1 = mysqli_fetch_array($res1)){
		$planner_name = $data1['planner_name'];
		$joint_emp_name = $data1['joint_emp_name'];
		$task_name = $data1['GPT_NAME'];
		$noti_content_config = array(
				'reason'=>array($task_name),
				'Customer_Name'=>array($planner_name),
				'Employee_Name'=>array($joint_emp_name)
		);
		send_formatted_notification_content($noti_content_config, 1, $template_id, 1, $data1['GTH_EMP_ID']);
	}
}

/**
 * @param int $cust_user_id
 * @return void
 */
function update_mygofrugal_access($cust_user_id){
	$contact_no = get_single_value_from_single_table("GCL_USERNAME", "gft_customer_login_master","GCL_USER_ID",(string)$cust_user_id);
	if(is_numeric($contact_no)){
		$contact_cond = getContactDtlWhereCondition("gcc_contact_no", $contact_no);
	}else{
		$contact_cond = " gcc_contact_no='$contact_no' ";
	}
	$chk_cust_qry = " select gcc_lead_code,gcc_id,GLH_LEAD_TYPE from gft_customer_contact_dtl ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GCC_LEAD_CODE) ".
			" left join gft_pos_users on (GPU_CONTACT_ID=GCC_ID) ".
			" where if(GPU_CONTACT_ID is null,1,GPU_CONTACT_STATUS='A') and $contact_cond ";
	$chk_qry_res = execute_my_query($chk_cust_qry);
	execute_my_query("update gft_customer_access_dtl set gca_access_status=0 where gca_user_id='$cust_user_id' and GCA_ROLE_SWITCH!=1 ");
	while($r=mysqli_fetch_array($chk_qry_res)) {
		$lead = $r['gcc_lead_code'];
		$id = $r['gcc_id'];
		$status_code = '1';
		$contact_chk_qry = " select GCA_ID from gft_customer_access_dtl where ".
				" gca_user_id='$cust_user_id' and gca_contact_id='$id' and GCA_ACCESS_LEAD='$lead' ";
		$contact_chk_res = execute_my_query($contact_chk_qry);
		if(mysqli_num_rows($contact_chk_res)>0)
			execute_my_query(" update gft_customer_access_dtl set gca_access_status=$status_code ".
					" where gca_user_id='$cust_user_id' and gca_contact_id='$id' and GCA_ACCESS_LEAD='$lead' ");
		else
			execute_my_query("insert into gft_customer_access_dtl ".
					" (gca_user_id,gca_access_lead,gca_access_status,gca_created_date,gca_contact_id) ".
					" values ('$cust_user_id','$lead',$status_code,now(),'$id')");
	}
}
/**
 * @param int $mobile_uid
 * @param int $lead_code
 * @param int $offset
 * @param int $page_size
 * @param string $call_date
 * @param string $call_type
 *
 * @return string
 */
function get_query_for_call_history($mobile_uid,$lead_code,$offset,$page_size,$call_date='',$call_type=''){
	$date_condition = (date('Y-m-d H:i:s',strtotime("-1 days",strtotime(date('Y-m-d H:i:s')))));
	$wh_cond = "";
	if($call_date!=''){
		$wh_cond .= " and GTC_DATE between '$call_date 00:00:00' and '$call_date 23:59:59' ";
	}
	if($call_type=='customer'){
	    $wh_cond .= " and (GTC_EMP_ID is null or GTC_EMP_ID=0) and GTC_MAIN_GROUP!=709 ";
	}else if($call_type=='employee'){
	    $wh_cond .= " and GTC_EMP_ID is not null and GTC_EMP_ID > 0 ";
	}else if($call_type=='partner'){
	    $wh_cond .= " and GTC_MAIN_GROUP=709";
	}
	$sql_query	=" select GTC_ID,GTC_NUMBER,GTC_DATE,GTC_DURATION,GLH_LEAD_CODE,GLH_CUST_NAME,GLH_CUST_STREETADDR2, ".
			" GLH_STATUS,GLH_PROSPECTS_STATUS,GVC_ABR,GTC_ACTIVITY_ID,GTC_ASSIGN_TO,date(GTC_DATE) as call_dt,time(GTC_DATE) as call_time, ".
			" if( (GTC_ASSIGN_TO > 0 or GVC_ABR='OM'),9999999999,GTC_ACTIVITY_ID) as act_id,GTC_MAIN_GROUP,GTC_RECALL_STATUS,GTC_SPECIFIC_REASON,GTC_PATH ".
			" from gft_techsupport_incomming_call ".
			" join gft_voicesnap_call_status_master on (GVC_ID=GTC_CALL_STATUS) ".
			" left join gft_lead_hdr on (GLH_LEAD_CODE=GTC_LEAD_CODE) ".
			" where GTC_AGENT_ID='$mobile_uid' $wh_cond and GTC_OFFICE_ID!=3 ".($lead_code>0?" AND GLH_LEAD_CODE='$lead_code' AND GTC_DATE>='$date_condition'":" and 1 ").
			" order by call_dt desc,act_id,call_time desc limit $offset , $page_size ";
	return $sql_query;
}
/**
 * @param string $uid
 * @param string $lead_code
 *
 * @return string[string][string]
 */
function get_emp_pending_call_hostory($uid,$lead_code){
	$cust_call_history_query	=get_query_for_call_history($uid,$lead_code,0,10);
	$result_cust_call =  execute_my_query($cust_call_history_query);
	$call_histoty_list = array();
	$call_histoty['id']="0";
	$call_histoty['value']="Select";
	$call_histoty_list[] = $call_histoty;
	while($row_cust_call=mysqli_fetch_array($result_cust_call)){
		$mobile_no = $row_cust_call['GTC_NUMBER'];
		$select_name = $row_cust_call['GVC_ABR']."- $mobile_no At ".$row_cust_call['GTC_DATE'];
		$call_histoty['id']=$row_cust_call['GTC_ID'];
		$call_histoty['value']=$select_name;
		$call_histoty_list[] = $call_histoty;
	}
	return $call_histoty_list;
}
/**
 * @param string $date
 *
 * @return boolean
 */
function isWeekendAndHoliday($date) {
	return (date('N', strtotime($date)) > 5 || isDateInHoliday($date));
}
/**
 * @param string $date
 * @param string $compare_date
 * @param int $type
 *
 * @return boolean
 */
function checkDay($date,$compare_date,$type){
	if($type==1){
		return ((strtotime($date)>strtotime($compare_date)));
	}else {
		return ((strtotime($date)<strtotime($compare_date)));
	}

}
/**
 * @param string $date
 * @param int $hours
 * 
 * @return string
 */
function get_phone_support_escalation_date($date, $hours){
	$complaint_date_time =$date;
	$start_date 	= strtotime($complaint_date_time);
	$complaint_date = date("Y-m-d", $start_date);
	$current_day 	= isWeekendAndHoliday($complaint_date_time);
	$correct_date 	= date("Y-m-d",strtotime($complaint_date_time));
	$from_date_time = $complaint_date_time;
	$end_date_time 	= date("Y-m-d H:i:s", strtotime("+$hours hours", $start_date));
	if(($current_day) || (checkDay($complaint_date_time,"$complaint_date 18:00:00",1))){
		$days =1;
		$current_day = true;
		while($current_day){
			$date_now	= $correct_date=	add_date(date($complaint_date_time), $days);
			$current_day = isWeekendAndHoliday($date_now);
			$days++;
		}
		$from_date_time = date("Y-m-d 09:00:00", strtotime($correct_date));
		$end_date_time = date("Y-m-d H:i:s", strtotime("+$hours hours", strtotime($from_date_time)));
	}else if((checkDay($complaint_date_time,"$complaint_date 09:00:00",2))){
		$days =1;
		$current_day = isWeekendAndHoliday($complaint_date_time);
		$correct_date = $complaint_date_time;
		while($current_day){
			$date_now	= $correct_date=	add_date(date($complaint_date_time), $days);
			$current_day = isWeekendAndHoliday($date_now);
			$days++;
		}
		$from_date_time = date("Y-m-d 09:00:00", strtotime($correct_date));
		$end_date_time = date("Y-m-d H:i:s", strtotime("+$hours hours", strtotime($from_date_time)));
	}else if((checkDay($end_date_time,"$complaint_date 18:00:00",1))){
		$datetime1 = strtotime("$complaint_date_time");
		$datetime2 = strtotime("$complaint_date 18:00:00");
		$interval  = abs($datetime2 - $datetime1);
		$minutes   = round($interval / 60);
		$pending_mins_next_days = ($hours*60)- $minutes;
		$days =1;
		$date_now	= $correct_date=	add_date(date($complaint_date_time), $days);
		$current_day = isWeekendAndHoliday($date_now);
		while($current_day){
			$date_now	= $correct_date=	add_date(date($complaint_date_time), $days);
			$current_day = isWeekendAndHoliday($date_now);
			$days++;
		}
		$from_date_time1 = date("Y-m-d 09:00:00", strtotime($correct_date));
		$end_date_time = date("Y-m-d H:i:s", strtotime("+$pending_mins_next_days minutes", strtotime($from_date_time1)));
	}
	return $end_date_time;
}
/**
 * @param string $supported_on
 * @param string $is_escalated
 * @param string $phone_support_mins
 * @param string $remote_support_mins
 * @param string $complementary_support_mins
 * 
 * @return boolean
 */
function can_allow_to_update_supprt_duration($supported_on,$is_escalated,$phone_support_mins,$remote_support_mins,$complementary_support_mins){
	$duration_editable = true;
	$start_date = strtotime(date("Y-m-d H:i:s"));
	$end_date	= strtotime(get_phone_support_escalation_date($supported_on,4));	
	if($start_date > $end_date){ //4 hours check
		$duration_editable = false;
	}
	if($is_escalated=='R' || ($is_escalated=='N' && (($phone_support_mins+$remote_support_mins)==$complementary_support_mins))){
		$duration_editable = false;
	}
	return $duration_editable;
}
/**
 * @param string $is_escalated
 * @param string $phone_support_mins
 * @param string $remote_support_mins
 * @param string $complementary_support_mins
 * @param string $escalated_mins
 * @param string $revised_mins
 *
 * @return int
 */
function get_call_support_duration($is_escalated,$phone_support_mins,$remote_support_mins,$complementary_support_mins,$escalated_mins,$revised_mins){
	$update_duration = (($phone_support_mins+$remote_support_mins)-$complementary_support_mins);
	if($is_escalated=='Y'){
		$update_duration = (int)$escalated_mins;
	}
	if($is_escalated=='R'){
		$update_duration = (int)$revised_mins;
	}
	return $update_duration;
}

/**
 * @param int $dealer_id
 * @param string $return_type
 *
 * @return string
 */
function send_dsf_file_to_dealer($dealer_id,$return_type=''){
	global $attach_path;
	$sql1 = " select GEM_EMAIL,CGI_LEAD_CODE,GLH_CUST_NAME,CGI_SHORT_NAME from gft_emp_master ".
			" join gft_cp_info on (GEM_EMP_ID=CGI_EMP_ID) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=CGI_LEAD_CODE) ".
			" where GEM_EMP_ID='$dealer_id' ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$partner_email 		= $row1['GEM_EMAIL'];
		$partner_lead_code 	= $row1['CGI_LEAD_CODE'];
		$cust_name			= $row1['GLH_CUST_NAME'];
		$short_name			= $row1['CGI_SHORT_NAME'];
		$file_location = "../".$attach_path."/Dealer_Secret/$partner_lead_code/$short_name.dsf";
		if($return_type=='filepath'){
			return $file_location;
		}
		$mail_content = array(
				"Attachment"=>array($file_location),
				"Customer_Name"=>array($cust_name),
		);
		send_formatted_mail_content($mail_content, 89, 246, null, null, array($partner_email));
		return $partner_email;
	}
	return '';
}
/**
 * @param string $uid
 * @param boolean $apply_date_con
 * @param string $date_val
 *
 * @return string
 */
function get_query_for_complementary_coupon_approval($uid, $apply_date_con=false, $date_val=''){	
	$date_query = "";
	if($apply_date_con){
		$date_con	=	(date('Y-m-d H:i:s',strtotime("-1 days",strtotime(date('Y-m-d H:i:s')))));
		//Due to old pending, validation added from condition as 2017-12-01 00:00:00
		$date_query = " AND gcr_created_date<='$date_con' AND gcr_created_date>='2017-12-01 00:00:00'";
	}
	if($date_val!='') {
		$date_val = db_date_format($date_val);
		$date_query = " AND gcr_created_date>='$date_val 00:00:00' and gcr_created_date<='$date_val 23:59:59' ";
	}
	$sql_approval = " select gcr_id, gcr_created_date, gcr_coupon_purpose, gcr_request_status,".
			" gcp_purpose, gcr_emp_id, gem_emp_name, gcr_message, glh_cust_name".
			" from gft_complementary_coupon_request ".
			" inner join gft_complementary_coupon_purpose on (gcp_id = gcr_purpose_id) ".
			" left join gft_lead_hdr on (gcr_lead_code = glh_lead_code) ".
			" inner join gft_emp_master on (gem_emp_id = gcr_emp_id) ".
			" where gcr_request_to = '$uid' and gcr_request_status='N' $date_query order by gcr_id desc";
	return $sql_approval;
}
/**
 * @param string $uid
 * @param string $return_val
 *
 * @return string
 */
function get_pending_approval_of_complementary_coupon($uid,$return_val=''){
	$result = execute_my_query(get_query_for_complementary_coupon_approval($uid,true));
	$approval_count = mysqli_num_rows($result);
	if($return_val=='count'){
		return "$approval_count";
	}
	if($approval_count>0){
		return "You have $approval_count pending approval for complementary coupon request. Please take action from complementary coupon menu in myDelight and then submit your daily report.";
	}
	return '';
}
/**
 * @param string $field_name
 * @param boolean $from_app
 * @return mixed[string]
 */
function validate_po_file_upload($field_name,$from_app=false) {
	$msg = '';
	$success_status = true;
	$file_type = $_FILES["$field_name"]['type'];
	if($from_app) {
		$file_type = $_FILES["$field_name"]['type'][0];
	}
	if(!isset($_FILES["$field_name"]['size']) or (isset($_FILES["$field_name"]['size']) and $_FILES["$field_name"]['size']==0)) {
		$msg = "Please upload the PO file";
	} else if(!in_array($file_type,array('application/pdf','application\\/pdf','image/jpeg','image/png','image/gif'))) {
		$msg = "Please upload the PO file in PDF/jpg/png/gif format";
	} else {
		$error_no = isset($_FILES["$field_name"]['error_no'])?$_FILES["$field_name"]['error_no']:'0';
		if($error_no!='0') {
			$po_file_error = get_file_error_msg_string($error_no);
			$msg = $po_file_error;
		}
	}
	if($msg!='') {
		$success_status = false;
	}
	return array("success"=>$success_status,"error_msg"=>$msg);
}
/**
 * @param string $field_name
 * @param string $po_num
 * @param string $po_date
 * @param string $lead_code
 * @param string $order_no
 * @param boolean $update_path_only
 * @return mixed[string]
 */
function upload_po_file($field_name,$po_num,$po_date,$lead_code,$order_no,$update_path_only=false) {
	global $attach_path;
	$msg = '';
	$success_status = true;	
	$file_name = $f_temp_name = '';
	if($update_path_only) {
		$file_name = (string)str_replace(' ', '_', $_FILES["$field_name"]['name'][0]);
		$f_temp_name = $_FILES["$field_name"]['tmp_name'][0];
	}else{
		$file_name = (string)str_replace(' ', '_', $_FILES["$field_name"]['name']);
		$f_temp_name = $_FILES["$field_name"]['tmp_name'];
	}
	$file_path = "$attach_path/purchase_order";
	if(!file_exists($file_path)) {
		mkdir($file_path,0777);
	}
	$file_path = "$file_path/$lead_code";
	if(!file_exists($file_path)){
		mkdir($file_path,0777);
	}
	$po_path = "$file_path/$file_name";
	if(file_exists($po_path)){
		$msg = "Another PO with same file name already exists. ";
	} else {
		if(move_uploaded_file($f_temp_name, $po_path)){
			if(!$update_path_only) {
				execute_my_query(" update gft_order_hdr set GOD_PO_ORDER_NO='$po_num', GOD_PO_DATE='$po_date', GOD_PO_FILE_PATH='$po_path' where god_order_no='$order_no' ");
			} else {
				execute_my_query(" update gft_order_hdr set GOD_PO_FILE_PATH='$po_path' where god_order_no='$order_no' ");
			}
		} else {
			$msg = "Error occurred while moving uploaded file. ";
		}
	}
	if($msg!='') {
		$success_status = false;
	}
	return array("success"=>$success_status,"error_msg"=>$msg);
}
/**
 * @return string[string][int]
 */
function get_id_proof_types() {
	$proof_qry = " select GIP_ID,GIP_NAME from gft_id_proof_master where GIP_STATUS='1' ";
	$proof_res = execute_my_query($proof_qry);
	$ret_arr = /*.(string[string][int]).*/array();
	while($row = mysqli_fetch_array($proof_res)) {
		$ret_arr[] = array('id'=>$row['GIP_ID'],'name'=>$row['GIP_NAME']);
	}
	return $ret_arr;
}
/**
 * @return string
 */
function get_domain_url() {
	$test_mode = get_samee_const('STORE_PAYMENT_TEST_MODE');
	$domain_name = 'https://labtest.gofrugal.com';
	if($test_mode=='0') {
		$domain_name = 'https://sam.gofrugal.com';
	}
	return $domain_name;
}
/**
 * @return string
 */
function get_edit_id_for_emp_dtl() {
	$act_id = '1';
	$act_id_qry = execute_my_query("select max(GEA_ACTIVITY_ID) latest_update_id from gft_emp_dtl_audit");
	if($row = mysqli_fetch_array($act_id_qry)) {
		$act_id = $row['latest_update_id'];
		$act_id = (string)((int)$act_id+1);
	}
	return $act_id;
}
/**
 * @param string $emp_id
 * @param string $user_emp_id
 * @param string $act_id
 * @param string $table_name
 * @param string[int][int] $update_dtl
 * @param string $proof_type
 * @param string $edit_reason
 * @param int $approved_by_emp_id
 * 
 * @return void
 */
function log_emp_dtl_changes($emp_id,$user_emp_id,$act_id,$table_name,$update_dtl,$proof_type='',$edit_reason="",$approved_by_emp_id=0) {
	$update_datetime = date('Y-m-d H:i:s');
	foreach($update_dtl as $dtls) {
		if(isset($dtls[0]) and isset($dtls[1]) and isset($dtls[2])) {
			if($dtls[0]!=$dtls[1]) {
				$insert_arr = array();
				$insert_arr['GEA_ACTIVITY_ID']   = $act_id;
				$insert_arr['GEA_EMP_ID']	     = $emp_id;
				$insert_arr['GEA_TABLE']	     = $table_name;
				$insert_arr['GEA_COLUMN']	     = $dtls[2];
				$insert_arr['GEA_PREV_VAL']	     = $dtls[0];
				$insert_arr['GEA_NEW_VAL']	     = $dtls[1];
				$insert_arr['GEA_ID_PROOF_TYPE'] = $proof_type;
				$insert_arr['GEA_UPDATED_BY']  	 = $user_emp_id;
				$insert_arr['GEA_DATETIME']	   	 = $update_datetime;
				$insert_arr['GEA_REASON']	   	 = $edit_reason;
				$insert_arr['GEA_APPROVED_BY']	 = $approved_by_emp_id;
				array_insert_query("gft_emp_dtl_audit", $insert_arr);
			}
		}
	}
}
/**
 * @param string $emp_id
 * @param string $proof_type
 * @param string $proof_id
 * @param string $gender
 * @param string $edit_id
 * @return boolean
 */
function update_emp_id_proof_dtl($emp_id,$proof_type,$proof_id,$gender,$edit_id='') {
	global $uid,$mobile_uid;
	$update_dtl = /*.(string[int][string]).*/array();
	$proof_id = mysqli_real_escape_string_wrapper($proof_id); // GEA_ID_PROOF_TYPE
	if($proof_id=='') {
	    return false;
	}
	$curr_val_qry = execute_my_query("select gep_val from gft_emp_id_proof_dtl where GEP_EMP_ID='$emp_id' and GEP_TYPE='$proof_type'");
	$curr_val = '';
	if($row = mysqli_fetch_array($curr_val_qry)) {
		$curr_val = $row['gep_val'];
	}
	$update_arr = array();
	$update_arr['GEP_EMP_ID'] = $emp_id;
	$update_arr['GEP_VAL'] = $proof_id;
	$update_arr['GEP_TYPE'] = $proof_type;
	array_update_tables_common($update_arr,"gft_emp_id_proof_dtl",array('GEP_EMP_ID'=>$emp_id,"GEP_TYPE"=>$proof_type),null,$emp_id,null,null,$update_arr);
	execute_my_query(" update gft_emp_master set gem_gender='$gender' where gem_emp_id='$emp_id' ");
	if(mysqli_errno_wrapper()==0) {
		if($edit_id!='') {
			$update_dtl[] = array($curr_val,$proof_id,'GEP_VAL');
			$updated_by = ((isset($uid) and (int)$uid!=0)?$uid:((isset($mobile_uid) and (int)$mobile_uid!=0)?$mobile_uid:$emp_id));
			log_emp_dtl_changes($emp_id,$updated_by,$edit_id,'gft_emp_id_proof_dtl',$update_dtl,$proof_type);
		}
		return true;
	}
	return false;
}
/**
 * @param string $emp_code
 * @param string $from_page
 * @param mixed[] $dtl_arr
 * @return string[string]
 */
function save_emp_dtl_in_master($emp_code,$from_page,$dtl_arr) {
    global $uid,$mobile_uid;
	$response = /*.(string[string]).*/array();
	$dob1 = $mobileno1 = $paddress1 = $caddress1 = $residenceno1 = $rmobilno = $che_inter_com = $direct_no = $mobile_fwd = '';
	$courier_addr = $laptop_harddisk_id = $laptop_mac_id = '';
	$mobile_mac_address = $gender = $proof_type = $proof_id = '';
	$personal_email = $emergency_no = $blood_gp = '';
	$existing_mobile = "";
	$gem_lead_code = get_single_value_from_single_table("gem_lead_code", "gft_emp_master", "gem_emp_id", "$emp_code");
	$curr_dtl_qry = execute_my_query("select * from gft_emp_master where gem_emp_id='$emp_code'");
	if($emp_data = mysqli_fetch_array($curr_dtl_qry)) {
		$paddress1 = $emp_data['GEM_PERMENANT_ADDRESS'];
		$caddress1 = $emp_data['GEM_CURRENT_ADDRESS'];
		$existing_mobile = $emp_data['GEM_MOBILE'];
		$residenceno1 = $emp_data['GEM_RESIDENCE_NO'];
		$che_inter_com = $emp_data['GEM_IC'];
		$direct_no = $emp_data['GEM_DIRECT_NO'];
		$mobile_fwd = $emp_data['GEM_CALL_MOBILE'];
		$courier_addr = $emp_data['GEM_COURIER_ADDR'];
		$laptop_harddisk_id = $emp_data['GEM_LAPTOP_HARDDISK_ID'];
		$laptop_mac_id = $emp_data['GEM_LABTOP_MAC_ID'];
		$mobile_mac_address = $emp_data['GEM_MOBILE_MAC_ID'];
		$gem_lead_code = $emp_data['GEM_LEAD_CODE'];
		$personal_email = $emp_data['GEM_PERSONAL_EMAIL'];
		$emergency_no = $emp_data['GEM_EMERGENCY_NUMBER'];
		$blood_gp = $emp_data['GEM_BLOOD_GROUP'];
		$gender = $emp_data['GEM_GENDER'];
		$dob1 = $emp_data['GEM_DOB'];
	}
	if($from_page=='web') {
		$mobileno1 = isset($dtl_arr['mobileno'])?(string)$dtl_arr['mobileno']:'';
		$residenceno1 = (isset($dtl_arr['residence_no']) and $residenceno1!=$dtl_arr['residence_no'])?(string)$dtl_arr['residence_no']:$residenceno1;
		$rmobilno = isset($dtl_arr['rmobileno'])?(string)$dtl_arr['rmobileno']:'';
		$che_inter_com = (isset($dtl_arr['che_inter_com']) and $che_inter_com!=$dtl_arr['che_inter_com'])?(string)$dtl_arr['che_inter_com']:$che_inter_com;
		$direct_no = (isset($dtl_arr['direct_no']) and $direct_no!=$dtl_arr['direct_no'])?(string)$dtl_arr['direct_no']:$direct_no;
		$mobile_fwd = (isset($dtl_arr['mobile_fwd']) and $mobile_fwd!=$dtl_arr['mobile_fwd'])?(string)$dtl_arr['mobile_fwd']:$mobile_fwd;
		$gem_lead_code =  (isset($dtl_arr['gem_lead_code']) and $gem_lead_code!=$dtl_arr['gem_lead_code'])?(int)$dtl_arr['gem_lead_code']:$gem_lead_code;
	} else if($from_page=='app') {
		$mobileno1 = isset($dtl_arr['official_mobile'])?(string)$dtl_arr['official_mobile']:'';
		$rmobilno = isset($dtl_arr['personal_mobile'])?(string)$dtl_arr['personal_mobile']:'';
		$proof_type = isset($dtl_arr['proof_type'])?$dtl_arr['proof_type']:'';
		$proof_id = isset($dtl_arr['proof_id'])?mysqli_real_escape_string_wrapper($dtl_arr['proof_id']):'';
	}
	$edit_id = get_edit_id_for_emp_dtl();
	if($proof_type!='' and $proof_type!='0') {
		update_emp_id_proof_dtl($emp_code, $proof_type, $proof_id, $gender, $edit_id);
	}
	$gender = ((isset($dtl_arr['gender']) and $gender!=$dtl_arr['gender'])?$dtl_arr['gender']:$gender);
	$dob1 = ((isset($dtl_arr['dob']) and $dob1!=$dtl_arr['dob'])?(string)$dtl_arr['dob']:$dob1);
	$emergency_no = ((isset($dtl_arr['emergency_contact']) and $emergency_no!=$dtl_arr['emergency_contact'])?$dtl_arr['emergency_contact']:$emergency_no);
	$personal_email = ((isset($dtl_arr['personal_email']) and $personal_email!=$dtl_arr['personal_email'])?mysqli_real_escape_string_wrapper((string)$dtl_arr['personal_email']):mysqli_real_escape_string_wrapper($personal_email));
	$blood_gp = ((isset($dtl_arr['blood_gp']) and $dtl_arr['blood_gp']!=$blood_gp)?$dtl_arr['blood_gp']:$blood_gp);
	$paddress1 = (isset($dtl_arr['paddress']) and $paddress1!=$dtl_arr['paddress'])?mysqli_real_escape_string_wrapper((string)$dtl_arr['paddress']):mysqli_real_escape_string_wrapper($paddress1);
	$caddress1 = (isset($dtl_arr['caddress']) and $caddress1!=$dtl_arr['caddress'])?mysqli_real_escape_string_wrapper((string)$dtl_arr['caddress']):mysqli_real_escape_string_wrapper($caddress1);
	$courier_addr = (isset($dtl_arr['courier_addr']) and $courier_addr!=$dtl_arr['courier_addr'])?mysqli_real_escape_string_wrapper((string)$dtl_arr['courier_addr']):mysqli_real_escape_string_wrapper($courier_addr);
	$laptop_harddisk_id = (isset($dtl_arr['laptop_harddisk_id']) and $laptop_harddisk_id!=$dtl_arr['laptop_harddisk_id'])?mysqli_real_escape_string_wrapper((string)$dtl_arr['laptop_harddisk_id']):mysqli_real_escape_string_wrapper($laptop_harddisk_id);
	$laptop_mac_id = (isset($dtl_arr['laptop_mac_id']) and $laptop_mac_id!=$dtl_arr['laptop_mac_id'])?mysqli_real_escape_string_wrapper((string)$dtl_arr['laptop_mac_id']):mysqli_real_escape_string_wrapper($laptop_mac_id);
	$mobile_mac_address = (isset($dtl_arr['mobile_mac_address']) and $mobile_mac_address!=$dtl_arr['mobile_mac_address'])?mysqli_real_escape_string_wrapper((string)$dtl_arr['mobile_mac_address']):mysqli_real_escape_string_wrapper($mobile_mac_address);
	$mobileno1 = ltrim($mobileno1,"0");
	$rmobilno = (is_valid_mobile($rmobilno)?$rmobilno:'');
	$SQL1="UPDATE gft_emp_master set GEM_DOB='$dob1',GEM_MOBILE='$mobileno1',GEM_RELIANCE_NO='$rmobilno', " .
	" GEM_RESIDENCE_NO='$residenceno1' , GEM_PERMENANT_ADDRESS=\"$paddress1\", ".
	" GEM_CURRENT_ADDRESS=\"$caddress1\" , GEM_COURIER_ADDR=\"$courier_addr\",GEM_GENDER='$gender'," .
	" GEM_IC='$che_inter_com', GEM_DIRECT_NO='$direct_no', GEM_CALL_MOBILE='$mobile_fwd',".
	" GEM_LEAD_CODE='$gem_lead_code',GEM_LAPTOP_HARDDISK_ID='$laptop_harddisk_id',GEM_MOBILE_MAC_ID=REPLACE(REPLACE('$mobile_mac_address',':',''),'-',''), ".
	" GEM_LABTOP_MAC_ID=REPLACE(REPLACE('$laptop_mac_id',':',''),'-',''),GEM_EMERGENCY_NUMBER='$emergency_no', ".
	" GEM_PERSONAL_EMAIL='$personal_email',GEM_BLOOD_GROUP='$blood_gp' where GEM_EMP_ID='$emp_code'";
	if(execute_my_query($SQL1)) {
		$response['status'] = "success";
		$response['message'] = "Updated Successfully";
		sync_employees_to_authservice($emp_code);
		$emp_dtls = get_emp_master($emp_code);
		$emp_dtl = isset($emp_dtls[0])?$emp_dtls[0]:null;
		if(is_array($emp_dtl) and count($emp_dtl)>0) {
    		$z_people = new ZohoPeople();
    		$z_people->emp_id = isset($emp_dtl[24])?$emp_dtl[24]:'';
    		$z_people->name = isset($emp_dtl[1])?$emp_dtl[1]:'';
    		$z_people->email = isset($emp_dtl[4])?$emp_dtl[4]:'';
    		$z_people->mobile = $mobileno1;
    		$z_people->other_email = $personal_email;
    		$z_people->doj = isset($emp_dtl[35])?$emp_dtl[35]:'';
    		$z_people->dob = $dob1;
    		$z_people->pan = isset($emp_dtl[36])?$emp_dtl[36]:'';
    		$z_people->reporting_mgr = isset($emp_dtl[6])?$emp_dtl[6]:'';
    		$z_people->uid = ((isset($uid) and (int)$uid!=0)?$uid:((isset($mobile_uid) and (int)$mobile_uid!=0)?$mobile_uid:$emp_code));
//     		$z_people->send_emp_dtls(2);
		}
	}else {
		$response['message'] = "Error in Updating(Invalid Query)";
	}
	if($existing_mobile!=$mobileno1){
	    $_SESSION["mobile_no"] = $mobileno1;
	    execute_my_query("update gft_emp_auth_key set AUTH_TOKEN=null where EMP_ID='$emp_code'");
	}
	return $response;
}
/**
 * @param string $dt
 * @return string
 */
function get_age_in_years($dt) {
	$d1 = new DateTime(db_date_format($dt));
	$d2 = new DateTime(date('Y-m-d'));
	$diff = $d2->diff($d1);
	return (string)$diff->y;
}
/**
 * @param string $table_name
 * @param string $id_col
 * @param string $label_col
 * @param string[int] $condition_cols
 * @param string[int] $condition_vals
 * @param string $field1
 * @param string $field2
 * @return string[int][string]
 */
function get_local_list_from_master($table_name,$id_col,$label_col,$condition_cols,$condition_vals,$field1='id',$field2='name') {
	$master_list = /*.(string[int][string]).*/array();
	$master_qry = " select $id_col,$label_col from $table_name where 1 ";
	for($i=0;$i<count($condition_cols);$i++) {
		if(isset($condition_vals[$i])) {
			$master_qry .= " and ".$condition_cols[$i]." = '".$condition_vals[$i]."' ";
		}
	}
	$master_qry .= " order by $label_col ";
	$master_res = execute_my_query($master_qry);
	while($row = mysqli_fetch_array($master_res)) {
		$this_row = array();
		$this_row[$field1] = $row[$id_col];
		$this_row[$field2] = $row[$label_col];
		$master_list[] = $this_row;
	}
	return $master_list;
}
/**
 * @param string $mode_str
 * @return string[int]
 */
function get_travel_mode_labels($mode_str) {
	$mode_arr = explode(",",$mode_str);
	$labels = array();
	if(count($mode_arr)>0) {
		$modes = implode("','",$mode_arr);
		$modes_qry = "select gem_mode_code,gem_mode_name from gft_expense_mode_of_travel_master where gem_mode_code in ('$modes')";
		$modes_res = execute_my_query($modes_qry);
		while($row = mysqli_fetch_array($modes_res)) {
			$str = $row['gem_mode_name'];
			$labels[] = "$str";
		}
	}
	return /*.(string[int]).*/array_unique($labels);
}
/**
 * @return string[string]
 */
function get_booking_status_labels() {
	return array('1'=>'Pending','2'=>'Booked','3'=>'Canceled','4'=>'Pending Cancel');
}
/**
 * @param string $booking_id
 * @return string[int]
 */
function fetch_attachments_for_booking($booking_id) {
	$qry = " select GBT_PATH from gft_booking_attachments where GBT_BOOKING_ID='$booking_id' ";
	$res = execute_my_query($qry);
	$links = /*.(string[int]).*/array();
	$domain = get_domain_url();
	while($row = mysqli_fetch_array($res)) {
		$link = $row['GBT_PATH'];
		$link_arr = pathinfo($link);
		$file_name = $link_arr['basename'];
		$link = $domain."/file_download.php?file_type=bookings&filename=$booking_id/$file_name";
		$links[] = $link;
	}
	return $links;
}
/**
 * @param string $booking_id
 * @return string[string][int]
 */
function get_booking_dtls_json($booking_id) {
	$resp_arr = /*.(string[string][int]).*/array();
	$dtl_qry = " select gbh_status,GBH_COMMENTS,GBH_PREF_MODE,GBH_PREF_DEP_TIME,GBH_FROM_DT,GBH_TO_DT,GBH_DEP_PLACE, ".
			   " GBH_ARRIVAL_PLACE,gbh_type,GBH_PREF_BOARDING_PT,GBH_BOOKING_EXPENSE from gft_booking_hdr where gbh_id='$booking_id' ";
	$status_labels = get_booking_status_labels();
	$res = execute_my_query($dtl_qry);
	while($row = mysqli_fetch_array($res)) {
		$booking_type = $row['gbh_type'];
		$dep_place = $row['GBH_DEP_PLACE'];
		$arr_place = $row['GBH_ARRIVAL_PLACE'];
		$status = $status_labels[$row['gbh_status']];
		$expense = $row['GBH_BOOKING_EXPENSE'];
		$spec = $row['GBH_COMMENTS'];
		$pref_mode_arr = get_travel_mode_labels($row['GBH_PREF_MODE']);
		$pref_mode = implode("/",$pref_mode_arr);
		$from_dt = $row['GBH_FROM_DT'];
		$to_dt = $row['GBH_TO_DT'];
		$pref_dep = $row['GBH_PREF_DEP_TIME'];
		$pref_board = $row['GBH_PREF_BOARDING_PT'];
		$resp_arr[] = array('label'=>'Departure Place','value'=>$dep_place);
		$date_label = 'Date of Stay';
		$journey_dt_string = $from_dt." - ".$to_dt;
		if($booking_type=='1') {
			$resp_arr[] = array('label'=>'Arrival Place','value'=>$arr_place);
			$journey_dt_string = date('Y-m-d',strtotime($from_dt));
			$date_label = 'Date of Journey';
		}
		$resp_arr[] = array('label'=>$date_label,'value'=>$journey_dt_string);
		if($booking_type=='1') {
			$resp_arr[] = array('label'=>'Preferred Departure Time','value'=>$pref_dep);
			$resp_arr[] = array('label'=>'Preferred Boarding Point','value'=>$pref_board);
			$resp_arr[] = array('label'=>'Mode of Travel','value'=>$pref_mode);
		}
		$resp_arr[] = array('label'=>'Travel specifications','value'=>$spec);
		$resp_arr[] = array('label'=>'Status','value'=>$status);
		if($row['gbh_status']=='2') {
			$resp_arr[] = array('label'=>'Ticket Expense','value'=>"Rs ".$expense);
		}
	}
	return $resp_arr;
}
/**
 * @param string $request_id
 * @return string[string][int]
 */
function get_passenger_dtls_json($request_id) {
	$resp_arr = /*.(string[string][int]).*/array();
	$qry = " select gpd_name,gpd_age,gip_name,gpd_proof_value,gpd_gender from gft_passenger_dtl ".
		   " join gft_id_proof_master on (gpd_proof_type=gip_id) where gpd_booking_id='$request_id'";
	$res = execute_my_query($qry);
	while($row = mysqli_fetch_array($res)) {
		$this_row = array();
		$this_row['name'] = $row['gpd_name'];
		$this_row['age'] = $row['gpd_age'];
		$this_row['proof_type'] = $row['gip_name'];
		$this_row['proof_id'] = $row['gpd_proof_value'];
		$this_row['gender'] = ($row['gpd_gender']=='1'?'M':($row['gpd_gender']=='2'?'F':''));
		$resp_arr[] = $this_row;
	}
	return $resp_arr;
}
/**
 * @param string $request_id
 * @return string[string][int]
 */
function get_booking_audit_json($request_id) {
	$resp_arr = /*.(string[string][int]).*/array();
	$qry = " select GBA_CURRENT_STATUS,gem_emp_name,GBA_UPDATED_ON,GBA_COMMENTS from gft_booking_audit_dtl ".
		   " join gft_emp_master on (gem_emp_id=gba_updated_by) where GBA_BOOKING_ID='$request_id'";
	$res = execute_my_query($qry);
	$status_arr = get_booking_status_labels();
	while($row = mysqli_fetch_array($res)) {
		$this_row = array();
		$this_row['status'] = $status_arr[$row['GBA_CURRENT_STATUS']];
		$this_row['datetime'] = $row['GBA_UPDATED_ON'];
		$this_row['description'] = $row['GBA_COMMENTS'];
		$this_row['by'] = $row['gem_emp_name'];
		if($row['GBA_CURRENT_STATUS']=='2') {
			$this_row['ticket'] = fetch_attachments_for_booking($request_id);
		}
		$resp_arr[] = $this_row;
	}
	return $resp_arr;
}
/**
 * @param string $request_id
 * @param string $emp_id
 * @return string[int]
 */
function get_next_possible_actions($request_id,$emp_id) {
	$allowed_actions = /*.(string[int]).*/array();
	$req_dtl_qry = "select gbh_status,gbh_requested_by from gft_booking_hdr where gbh_id='$request_id'";
	$req_dtl_res = execute_my_query($req_dtl_qry);
	$status = $by = '';
	$is_hr = false;
	if($row = mysqli_fetch_array($req_dtl_res)) {
		$status = $row['gbh_status'];
		$by = $row['gbh_requested_by'];
	}
	$is_hr = is_authorized_group_list($emp_id, array(19)); // Human Resources
	if(!$is_hr and $by!=$emp_id) {
		return /*.(string[int]).*/array();
	} else {
		if($is_hr) {
			if(in_array($status,array('1','4','2'))) {
				$allowed_actions[] = 'cancel';
			}
			if(in_array($status,array('1'))) {
				$allowed_actions[] = 'booked';
			}
			if(in_array($status,array('1'))) {
				$allowed_actions[] = "reschedule";
			}
		} else {
			if(in_array($status,array('1'))) {
				$allowed_actions[] = 'cancel';
			}
			if(in_array($status,array('1'))) {
				$allowed_actions[] = "reschedule";
			}
			if(in_array($status,array('2'))) {
				$allowed_actions[] = "request_to_cancel";
			}
		}
	}
	return $allowed_actions;
}
/**
 * @param string $activity_label
 * @param string $emp_id
 * @param string $book_type
 * @param string $noti_date_str
 * @param string $status
 * @param string $by_emp
 * @param string $comments
 * @param string $id
 * @return void
 */
function send_notification_for_booking_status_change($activity_label,$emp_id,$book_type,$noti_date_str,$status,$by_emp,$comments,$id) {
	$status_labels = get_booking_status_labels();
	$noti_content_config = array();
	$noti_content_config['Employee_Name'] = array(get_single_value_from_single_table("gem_emp_name", "gft_emp_master", "gem_emp_id", "$emp_id"));
	$noti_content_config['Type'] = array(($book_type=='1'?'Travel':'Room'));
	$noti_content_config['dateon'] = array($noti_date_str);
	$noti_content_config['status'] = array($status_labels[$status]);
	$noti_content_config['Approved_By'] = array(get_single_value_from_single_table("gem_emp_name", "gft_emp_master", "gem_emp_id", "$by_emp"));
	$noti_content_config['Activity_Type'] = array($activity_label);
	$noti_content_config['comments'] = array(mysqli_real_escape_string_wrapper($comments));
	$is_hr = is_authorized_group_list($by_emp, array(19));
	if($is_hr) {
		send_formatted_notification_content($noti_content_config,'93','86',1,$emp_id,'0',$id);
	} else {
		$emp_id_qry = execute_my_query("select gem_emp_id from gft_emp_group_master where gem_group_id='19'");
		while($emps_row = mysqli_fetch_array($emp_id_qry)) {
			$send_to = $emps_row['gem_emp_id'];
			$noti_content_config['Employee_Name'] = array('Team');
			send_formatted_notification_content($noti_content_config,'93','86',1,$send_to,'0',$id);
		}
	}
}
/**
 * @param string $id
 * @param string $prev_status
 * @param string $curr_status
 * @param string $emp_id
 * @param string $cur_time
 * @param string $comments
 * @return int
 */
function insert_booking_audit($id,$prev_status,$curr_status,$emp_id,$cur_time,$comments) {
	$audit_arr = array();
	$audit_arr['GBA_BOOKING_ID'] = $id;
	$audit_arr['GBA_PREV_STATUS'] = $prev_status;
	$audit_arr['GBA_CURRENT_STATUS'] = $curr_status;
	$audit_arr['GBA_COMMENTS'] = $comments;
	$audit_arr['GBA_UPDATED_BY'] = $emp_id;
	$audit_arr['GBA_UPDATED_ON'] = $cur_time;
	$audit_id = /*.(int).*/array_insert_query("gft_booking_audit_dtl", $audit_arr);
	return $audit_id;
}
/**
 * @param string $id
 * @param string $mobile_uid
 * @param string $cur_time
 * @param string $comments
 * @param string $prev_status
 * @param string $last_update_time
 * @param string $last_update_emp
 * @param string $book_type
 * @param string $request_emp_id
 * @param string $noti_date_str
 * @param string $booking_expense
 * @return boolean
 */
function process_booked($id,$mobile_uid,$cur_time,$comments,$prev_status,$last_update_time,$last_update_emp,$book_type,$request_emp_id,$noti_date_str,$booking_expense) {
	execute_my_query("update gft_booking_hdr set gbh_status='2',gbh_last_updated_by='$mobile_uid',gbh_last_updated_on='$cur_time',GBH_BOOKING_EXPENSE='$booking_expense' where gbh_id='$id'");
	if(mysqli_errno_wrapper()==0) {
		if($booking_expense!='' and is_numeric($booking_expense) and (float)$booking_expense>0) {
			$comments .= "<br/>Ticket Expenses: &#x20B9 $booking_expense";
		}
		//To show the booking details in the expense report
		$booking_details = execute_my_query("select GBH_INCENTIVE_BUCKET_EMP, GBH_FROM_DT, YEAR(GBH_FROM_DT) AS 'year', MONTH(GBH_FROM_DT) AS 'month'". 
		                  "  from gft_booking_hdr where GBH_ID='$id' and GBH_INCENTIVE_BUCKET_EMP>0");
		if($row_booking=mysqli_fetch_assoc($booking_details)){
		    $month = $row_booking['month'];
		    $year = $row_booking['year'];
		    $employee_id = $row_booking['GBH_INCENTIVE_BUCKET_EMP'];
		    $sql_check	=	execute_my_query("select geh_emp_id from gft_exec_expense_hdr where geh_year='$year' and geh_month='$month' and geh_emp_id='$employee_id'");
		    if(mysqli_num_rows($sql_check)==0){
		        $insert_arr = array();
		        $insert_arr['geh_emp_id'] = "$employee_id";
		        $insert_arr['geh_month'] = "$month";
		        $insert_arr['geh_year'] = "$year";
		        array_insert_query("gft_exec_expense_hdr", $insert_arr);
		    }
		}		
		$audit_id = insert_booking_audit($id,$prev_status,'2',"$mobile_uid",$cur_time,$comments);
		if($audit_id>0) {
			send_notification_for_booking_status_change('Booked', $request_emp_id, $book_type, $noti_date_str, '2', $mobile_uid, $comments,$id);
			return true;
		} else {
			execute_my_query("update gft_booking_hdr set gbh_status='$prev_status',gbh_last_updated_by='$last_update_emp',gbh_last_updated_on='$last_update_time' where gbh_id='$id'");
		}
	}
	return false;
}
/**
 * @param string $id
 * @param string $book_type
 * @param string $mobile_uid
 * @param string $request_by
 * @param string $cur_time
 * @param string $prev_status
 * @param string $comments
 * @param string $noti_date_str
 * @return boolean
 */
function process_cancel($id,$book_type,$mobile_uid,$request_by,$cur_time,$prev_status,$comments,$noti_date_str) {
	execute_my_query("update gft_booking_hdr set gbh_status='3',gbh_last_updated_by='$mobile_uid',gbh_last_updated_on='$cur_time' where gbh_id='$id'");
	if(mysqli_errno_wrapper()==0) {
		$audit_id = insert_booking_audit($id,$prev_status,'3',"$mobile_uid",$cur_time,$comments);
		if($audit_id>0) {
			send_notification_for_booking_status_change('Cancelled', $request_by, $book_type, $noti_date_str, '3', $mobile_uid, $comments,$id);
			return true;
		}
	}
	return false;
}
/**
 * @param string $id
 * @param string $book_type
 * @param string $mobile_uid
 * @param string $request_by
 * @param string $cur_time
 * @param string $comments
 * @param string $prev_status
 * @param string $noti_date_str
 * @return boolean
 */
function process_request_cancel($id,$book_type,$mobile_uid,$request_by,$cur_time,$comments,$prev_status,$noti_date_str) {
	execute_my_query("update gft_booking_hdr set gbh_status='4',gbh_last_updated_by='$mobile_uid',gbh_last_updated_on='$cur_time' where gbh_id='$id'");
	if(mysqli_errno_wrapper()==0) {
		$audit_id = insert_booking_audit($id,$prev_status,'4',$mobile_uid,$cur_time,$comments);
		if($audit_id>0) {
			send_notification_for_booking_status_change('Request To Cancel',$request_by,$book_type,$noti_date_str,'4',$mobile_uid,$comments,$id);
			return true;
		}
	}
	return false;
}
/**
 * @param string $id
 * @param string $book_type
 * @param string $comments
 * @param string $prev_status
 * @param string $mobile_uid
 * @param string $request_by
 * @param string $check_in
 * @param string $check_out
 * @param string $cur_time
 * @param string $d1
 * @param string $d2
 * @param string $noti_date_str
 * @param string $pref_dep_time
 * @param string $prev_from
 * @param string $prev_to
 * @return boolean
 */
function process_reschedule($id,$book_type,$comments,$prev_status,$mobile_uid,$request_by,$check_in,$check_out,$cur_time,$d1,$d2,$noti_date_str,$pref_dep_time,$prev_from,$prev_to) {
	$from_dt = $to_dt = '';
	if($book_type=='1') {
		$from_dt = $check_in." 00:00:00";
		$to_dt = "";
		$comments .= "<br/>Previous date of travel : ".date('M d,Y',strtotime($prev_from));
		$comments .= "<br/>New date of travel : $d1<br/>New preferred time : $d2";
	} else {
		$from_dt = $check_in.":00";
		$to_dt = $check_out.":00";
		$comments .= "<br/>Previous check-in time : ".date('M d,Y h:i A',strtotime($prev_from));
		$comments .= "<br/>Previous check-out time : ".date('M d,Y h:i A',strtotime($prev_to));
		$comments .= "<br/>New check-in time : $d1<br/>New check-out time : $d2";
	}
	execute_my_query("update gft_booking_hdr set gbh_from_dt='$from_dt',gbh_to_dt='$to_dt',GBH_PREF_DEP_TIME='$pref_dep_time',gbh_status=1,gbh_last_updated_by='$mobile_uid',gbh_last_updated_on='$cur_time' where gbh_id='$id'");
	$audit_id = insert_booking_audit($id, $prev_status,'1',$mobile_uid,$cur_time,$comments);
	if($audit_id>0) {
		$status_labels = get_booking_status_labels();
		$noti_content_config = array();		
		$noti_content_config['Type'] = array(($book_type=='1'?'Travel':'Room'));
		$noti_content_config['dateon'] = array($noti_date_str);
		$noti_content_config['comments'] = array(mysqli_real_escape_string_wrapper($comments));
		$noti_content_config['Approved_By'] = array(get_single_value_from_single_table("gem_emp_name", "gft_emp_master", "gem_emp_id", $mobile_uid));
		$noti_content_config['status'] = array('Pending');
		$is_hr = is_authorized_group_list($mobile_uid, array(19));
		if($is_hr) {
			$noti_content_config['Employee_Name'] = array(get_single_value_from_single_table("gem_emp_name", "gft_emp_master", "gem_emp_id", $request_by));
			send_formatted_notification_content($noti_content_config,'93','87',1,$request_by,'0',$id);
		} else {
			$emp_id_qry = execute_my_query("select gem_emp_id from gft_emp_group_master where gem_group_id='19'");
			while($emps_row = mysqli_fetch_array($emp_id_qry)) {
				$send_to = $emps_row['gem_emp_id'];
				$noti_content_config['Employee_Name'] = array(get_single_value_from_single_table("gem_emp_name", "gft_emp_master", "gem_emp_id", $send_to));
				send_formatted_notification_content($noti_content_config,'93','87',1,$send_to,'0',$id);
			}
		}
		return true;
	}
	return false;
}
/**
 * @param string $emp_id
 *
 * @return string[int]
 */
function check_any_event_available($emp_id){
	$return_arr = array();
	$from_dt  = date("Y-m-d H:i:s");
	$result = execute_my_query("select GEP_EVENT_ID,GEM_EVENT_VERTICAL,GEM_EVENT_NAME,GEM_COMMERCIAL_INCHARGE from gft_event_participants ".
			" inner join gft_event_master ON(GEM_EVENT_ID=GEP_EVENT_ID)".
			" where GEP_PARTICIPANT_EMP_ID='$emp_id' AND '$from_dt'>=GEP_FROM_DATE AND '$from_dt'<=GEP_TO_DATE");
	if((mysqli_num_rows($result)>0) && ($row_event=mysqli_fetch_array($result))){
		$return_arr[0] = $row_event['GEP_EVENT_ID'];
		$return_arr[1] = $row_event['GEM_EVENT_VERTICAL'];
		$return_arr[2] = $row_event['GEM_EVENT_NAME'];
		$return_arr[3] = $row_event['GEM_COMMERCIAL_INCHARGE'];
		$return_arr[4] = "Lead generated from event - ".$row_event['GEM_EVENT_NAME'];
	}
	return $return_arr;
}
/**
 * @param string $mobile_uid
 * @return string[string]
 */
function get_all_id_vals_for_emp($mobile_uid) {
	$arr = /*.(string[string]).*/array();
	$qry = execute_my_query(" select GIP_ID,ifnull(gep_val,'') val from gft_id_proof_master ".
		   " left join gft_emp_id_proof_dtl on (GIP_ID=GEP_TYPE and gep_emp_id='$mobile_uid') group by gip_id ");
	while ($row = mysqli_fetch_array($qry)) {
		$arr[$row['GIP_ID']] = (string)$row['val'];
	}
	return $arr;
}
/**
 * @param string $emp_id
 * @return int
 */
function get_pending_training_assign_count($emp_id) {
	$count_qry = <<<END
select distinct god_order_no pending_train_lead_code from gft_lead_hdr join gft_order_hdr on (glh_lead_code=god_lead_code  
and god_emp_id='9999') left join gft_coupon_distribution_dtl on (substr(gcd_order_no,1,15)=god_order_no)  
where GOD_STORE_EMP='$emp_id' and GOD_IMPL_REQUIRED='Yes' AND GOD_ORDER_STATUS='A' and GCD_COUPON_NO is null
END;
	$count_res = execute_my_query($count_qry);
	$pending_count = mysqli_num_rows($count_res);
	return $pending_count;
}
/**
 * @param string $lead_code
 * @param string $partner_lead_code
 *
 * @return string[string][string]
 */
function get_pc_list_for_assign_coupon($lead_code, $partner_lead_code=""){
	$delivery_emp = array();
	$emp_product_delivery	=	get_emp_list_by_group_filter(array("70","36","72","5"));
	for($i=0;$i<count($emp_product_delivery);$i++){
		$single_val['id']=$emp_product_delivery[$i][0];
		$single_val['name']=$emp_product_delivery[$i][1];
		$single_val['parent_id']=($partner_lead_code!=""?"2":"1");
		$delivery_emp[]=$single_val;
	}
	if($partner_lead_code!=""){
		$partner_product_delivery=/*. (string[int][int]) .*/get_two_dimensinal_result_set_from_query(" select gem_emp_id,gem_emp_name from gft_leadcode_emp_map,gft_cp_info,gft_emp_master where " .
				" GLEM_EMP_ID=GEM_EMP_ID and CGI_LEAD_CODE=GLEM_LEADCODE and CGI_LEAD_CODE=$partner_lead_code AND GEM_STATUS='A'");
		for($i=0;$i<count($partner_product_delivery);$i++){
			$single_val['id']=$partner_product_delivery[$i][0];
			$single_val['name']=$partner_product_delivery[$i][1];
			$single_val['parent_id']="1";
			$delivery_emp[]=$single_val;
		}
		return $delivery_emp;
	}
	$emp_solution_delivery	=	get_emp_list_by_group_filter(array("36"));	
	for($i=0;$i<count($emp_solution_delivery);$i++){
		$single_val['id']=$emp_solution_delivery[$i][0];
		$single_val['name']=$emp_solution_delivery[$i][1];
		$single_val['parent_id']="2";
		$delivery_emp[]=$single_val;
	}
	
	$query_partner=	" select distinct em.gem_emp_id, concat(em.gem_emp_name, lh.GLH_CUST_NAME) as GLH_CUST_NAME from gft_cp_info AS ci ".
			" INNER JOIN gft_cp_agree_dtl AS ca ON(ca.gca_lead_code=ci.CGI_LEAD_CODE AND ca.GCA_CP_SUB_TYPE=7) ".
			" INNER JOIN gft_lead_hdr AS lh ON(lh.glh_lead_code=ci.CGI_LEAD_CODE AND lh.glh_lead_code!=$lead_code) ".
			" INNER JOIN gft_emp_master as em ON(em.gem_emp_id=lh.GLH_PARTNER_PD_INCHARGE) WHERE (1) ";
	$partner_emp	=	get_two_dimensinal_array_from_query($query_partner,'gem_emp_id','GLH_CUST_NAME');
	for($i=0;$i<count($partner_emp);$i++){
		$single_val['id']=$partner_emp[$i][0];
		$single_val['name']=$partner_emp[$i][1];
		$single_val['parent_id']="3";
		$delivery_emp[]=$single_val;
	}
	
	return $delivery_emp;
}
/**
 * @param string $emp_id
 * @param string $lead_code
 * @param string $order_no
 *
 * @return string
 */
function get_query_for_store_order_mapping($emp_id, $lead_code='', $order_no=''){
    $new_inc_date = db_date_format(trim(get_samee_const("New_Incentive_Order_Date")));
	$sql_query 	=	" select GOD_ORDER_NO id, GOD_LEAD_CODE cust_id, GOD_ORDER_NO order_no, GLH_CUST_NAME cust_name, ".
			" GOD_ORDER_AMT order_amt,  GOD_ORDER_DATE order_date, GOI_ORDER_NO incentive_order, GOD_IMPL_REQUIRED, ".
			" if($emp_id>7000, '1', (if(GOI_ORDER_NO is null and if(GOD_ORDER_DATE>='$new_inc_date',GOD_LICENSE_COST>0,1),'0','1'))) incentive_req, ".
			" if(GOD_IMPL_REQUIRED='Yes' AND GCD_COUPON_NO is null, '0', '1') coupon_req, ".
			" GOD_CREATED_DATE, GCD_COUPON_NO from gft_order_hdr ".
			" INNER JOIN gft_order_product_dtl on(GOP_ORDER_NO=GOD_ORDER_NO) ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW AND (GFT_SKEW_PROPERTY NOT IN(4) or GPM_PRODUCT_CODE=703)) ".
			" INNER JOIN gft_lead_hdr ON(god_lead_code=GLH_LEAD_CODE) ".
			" LEFT JOIN gft_orderwise_incentive_owner on(GOD_ORDER_NO=GOI_ORDER_NO and GOI_ATTRIBUTE_ID in (3,6,7) ) ".
			" LEFT JOIN gft_coupon_distribution_dtl on(substr(GCD_REF_ORDER_NO,1,15)=GOD_ORDER_NO)".
			" where GOD_EMP_ID='9999' and glh_lead_type!='8'  AND GOD_ORDER_AMT>0 AND GOD_INCHARGE_EMP_ID='$emp_id' ".
			($emp_id>7000?" AND GOD_IMPL_REQUIRED='Yes'":" ").
			($lead_code!=""?" AND GOD_LEAD_CODE='$lead_code'":"").
			($order_no!=""?" AND GOD_ORDER_NO='$order_no'":"").
			" group by GOD_ORDER_NO order by incentive_req ASC, coupon_req asc,  GOD_ORDER_DATE desc";
	return $sql_query;
}
/**
 *
 * @return string[string][string]
 */
function get_support_product_list(){
	$prod_list	=	array();
	$prod_query=" SELECT concat(GPM_PRODUCT_CODE,'-',gpg_skew) as prod_id, concat(GPM_PRODUCT_ABR,' ',gpg_version) as prod_name FROM gft_product_family_master ".
			" join gft_product_group_master on (gpg_product_family_code=GPM_PRODUCT_CODE and gpg_status='A') ".
			" where GPM_LIST_IN_SUPPORT='Y' group by prod_id ";
	$prod_res = execute_my_query($prod_query);
	while($data1 = mysqli_fetch_array($prod_res)){
		$single_status['id']	=	$data1['prod_id'];
		$single_status['name']	=	$data1['prod_name'];
		$prod_list[]		=	$single_status;
	}
	return $prod_list;
}
/**
 * @param string $lead_code
 * @param boolean $only_outstanding
 * @return string
 */
function get_order_query_for_customer($lead_code,$only_outstanding) {
	$query = " SELECT o.GOD_ORDER_NO,o.GOD_EMP_ID, o.GOD_INCHARGE_EMP_ID, o.GOD_ORDER_DATE, o.GOD_ORDER_AMT, o.GOD_ORDER_STATUS, ".
			 " GOD_COLLECTION_REALIZED, GOD_TAX_MODE, GOD_BALANCE_AMT, god_order_type, GOD_DISCOUNT_ADJ_AMT, god_invoice_status ".
			 " FROM gft_order_hdr o join gft_emp_master em on (em.gem_emp_id=o.god_emp_id) ".
			 " join gft_emp_master iem on (iem.gem_emp_id=o.GOD_INCHARGE_EMP_ID) ".
			 " where GOD_ORDER_STATUS='A' and god_lead_code='$lead_code' ";
	if($only_outstanding) {
		$query .= " and GOD_BALANCE_AMT>0 ";
	}
	$query .= " order by o.GOD_ORDER_DATE ";
	return $query;
}
/**
 * @param string $emp_id
 * @param string $from_date
 * @param string $to_date
 *
 * @return int[int]
 */
function get_pending_callback_details($emp_id, $from_date='', $to_date=''){
	$return_arr = array(0=>0,1=>0);
	$total_query =" select count(GTC_ID) total_cnt, count(if(GTC_CALL_STATUS=3 AND GTC_RECALL_STATUS='N', 1, null)) pending_callback ".
			" from gft_techsupport_incomming_call where GTC_AGENT_ID='$emp_id' AND GTC_SPECIFIC_REASON='cloud'  ".
			($from_date!=""?" AND GTC_DATE >= '$from_date'":"").
			($to_date!=""?" AND GTC_DATE <= '$to_date'":"");
	$total_res	 = execute_my_query($total_query);
	if($data1 = mysqli_fetch_array($total_res)){
		$return_arr[0] = (int)$data1['total_cnt'];
		$return_arr[1] = (int)$data1['pending_callback'];
	}
	return $return_arr;
}
/**
 * @param string $lead_code
 * @return string[string]
 */
function get_existing_customer_details($lead_code) {
    $qry = " select gsp_pd_support_group,gpg_support_group_id,sum(ifnull(god_balance_amt,0)) balance_amt,gpm_map_id,glh_lead_type, ".
           " glh_lead_sourcecode,glh_reference_given,glh_country from gft_lead_hdr  ".
           " join gft_support_product_group on (glh_main_product=gsp_group_id) ". // To take only PD completed customer details
           " join gft_product_group_master on (gpg_support_group_id=gsp_group_id) ".
           " left join gft_political_map_master on (glh_cust_statecode=gpm_map_name and gpm_map_type='S' and gpm_map_status='A') ".
           " left join gft_order_hdr on (god_lead_code=glh_lead_code and god_order_status='A' and god_balance_amt>0) ".
           " where glh_lead_code='$lead_code' group by glh_lead_code ";
    $res = execute_my_query($qry);
    $cust_dtls = /*.(string[string]).*/array();
    if($row = mysqli_fetch_assoc($res)) {
        $cust_dtls['support_group'] = $row['gpg_support_group_id'];
        $cust_dtls['is_pd_group'] = $row['gsp_pd_support_group'];
        $ref_given_by = $row['glh_reference_given'];
        $total_outstanding = floatval($row['balance_amt']);
        if($row['glh_lead_type']=='13' and $row['glh_lead_sourcecode']=='7' and (int)$ref_given_by>0) {
            $corp_cust_dtl = get_existing_customer_details($ref_given_by);
            $total_outstanding += isset($corp_cust_dtl['outstanding_amt'])?floatval($corp_cust_dtl['outstanding_amt']):0.0;
        }
        $cust_dtls['outstanding_amt'] = $total_outstanding;
        $cust_dtls['state_code'] = $row['gpm_map_id'];
        $cust_dtls['country'] = $row['glh_country'];
    }
    return $cust_dtls;
}
/**
 * @param string $lead_code
 * @param string $assign_emp_id
 * @param string $purpose
 * @return void
 */
function assign_lead_to_emp($lead_code,$assign_emp_id,$purpose) {
    $lead_owner_id = get_single_value_from_single_table("glh_lfd_emp_id", "gft_lead_hdr", "glh_lead_code", $lead_code);
    if($lead_owner_id!=$assign_emp_id) {
        execute_my_query("update gft_lead_hdr set glh_lfd_emp_id='$assign_emp_id' where glh_lead_code='$lead_code'");
        $activity_dtl = array();
        $activity_dtl["GLD_LEAD_CODE"] = $lead_code;
        $activity_dtl["GLD_EMP_ID"] = "9999";
        $activity_dtl["GLD_VISIT_DATE"] = date("Y-m-d");
        $activity_dtl["GLD_VISIT_NATURE"] = "99"; // lead owner transfer
        $activity_dtl["GLD_NOTE_ON_ACTIVITY"] = "Assigning lead owner from ".get_emp_name($lead_owner_id)." to ".get_emp_name($assign_emp_id)." during $purpose.";
        $activit_id = insert_in_gft_activity_table($activity_dtl);
    }
}
/**
 * @param string $cust_id
 * @param string $lead_owner
 * @param string $purpose
 * @return string
 */
function get_next_action_emp_for_customer($cust_id,$lead_owner,$purpose="app registration") {
    $next_action_emp = '';
    $cust_dtls = get_existing_customer_details($cust_id);
    $support_gp = (isset($cust_dtls['support_group'])?$cust_dtls['support_group']:'');
    $pd_gp = (isset($cust_dtls['is_pd_group'])?$cust_dtls['is_pd_group']:'N');
    $outstanding_amt = (isset($cust_dtls['outstanding_amt'])?(float)$cust_dtls['outstanding_amt']:0.0);
    $state_code = (isset($cust_dtls['state_code'])?$cust_dtls['state_code']:'');
    $country = (isset($cust_dtls['country'])?$cust_dtls['country']:'');
    if($outstanding_amt<=0 and $pd_gp=='N' and $support_gp!='' and 
        !is_authorized_group_list($lead_owner,explode(",", LEAD_OWNER_SKIP_GROUP))) {
            if(strtoupper($country)!='INDIA' or in_array($support_gp,array('7','36'))) {
                $next_action_emp = get_lead_mgmt_incharge('', '', '', '',100,$cust_id);
            } else {
                $next_action_emp = get_cst_agent_for_customer($state_code, $support_gp);
            }
            assign_lead_to_emp($cust_id,$next_action_emp,$purpose);
    }
    return $next_action_emp;
}
/**
 * @param string $cust_id
 * @param string $lead_created_category
 * @param string $created_by_emp
 * @param string $lead_owner
 * @param string $lmt_incharge
 * @param string $is_new_lead
 * @param string $lead_status
 * @param string $next_action
 * 
 * @return string[string]
 */
function get_next_action_emp_for_registration($cust_id,$lead_created_category,$created_by_emp,$lead_owner,$lmt_incharge,$is_new_lead,$lead_status,$next_action) {
    $next_action_emp = $lead_owner;
    if((int)$created_by_emp>0) {
        $next_action_emp = $created_by_emp;
    }
    $next_action_id = $next_action;
    $assign_appointment = true;
    $assigned_to_cst = false;
    if(is_authorized_group_list($next_action_emp,null,array(2)) and $is_new_lead) {
        $next_action_id = '102';
    } else {
        if($lead_created_category=='39') {
            $next_action_emp = $lmt_incharge;
        }
        if(!$is_new_lead and $lead_status=='9') {
            $cst_emp = get_next_action_emp_for_customer($cust_id,$lead_owner);
            if($cst_emp!='') {
                $next_action_emp = $cst_emp;
                $assigned_to_cst = true;
            }
        } 
        if(!$assigned_to_cst) {
            if(!is_authorized_group_list($next_action_emp, array(27,82))) { // Only online sales and marketing teams
                $next_action_emp = '';
                $assign_appointment = false;
            }
        }
    }
    $return_arr = /*.(string[string]).*/array();
    if($assign_appointment and !is_next_action_exits($cust_id,$next_action_emp,$next_action_id,date('Y-m-d'),false)) {
        $return_arr['next_action_emp'] = $next_action_emp;
        $return_arr['next_action'] = $next_action_id;
    }
    return $return_arr;
}

/**
 * @param string $cust_id
 * @param string $req_purpose
 * @param string $req_id
 * 
 * @return string[int][string]
 */
function get_existing_appointment_list($cust_id,$req_purpose='',$req_id=''){
    $tdate = date('Y-m-d');
    $app_que = " select concat('app-',GLD_ACTIVITY_ID) ids, concat(GLD_NEXT_ACTION_DATE,' ',ifnull(GLD_NEXT_ACTION_TIME,'')) action_time, ".
        " GAM_ACTIVITY_DESC act_desc, GEM_EMP_NAME  from gft_activity ".
        " join gft_activity_master on (GAM_ACTIVITY_ID=GLD_NEXT_ACTION) ".
        " join gft_emp_master on (GEM_EMP_ID=GLD_EMP_ID) ".
        " where GLD_LEAD_CODE='$cust_id' and GLD_SCHEDULE_STATUS in (1,3) and GLD_NEXT_ACTION_DATE >= '$tdate' ";
    if( ($req_purpose=='appointment') && ($req_id!='') ){
        $app_que .= " and GLD_ACTIVITY_ID!='$req_id' ";
    }
    $fol_que = " select concat('fol-',GCF_FOLLOWUP_ID) ids, concat(GCF_FOLLOWUP_DATE,' ',ifnull(GCF_FOLLOWUP_TIME,'')) action_time, ".
        " GAM_ACTIVITY_DESC act_desc, GEM_EMP_NAME from gft_cplead_followup_dtl ".
        " join gft_activity_master on (GAM_ACTIVITY_ID=GCF_FOLLOWUP_ACTION) ".
        " join gft_emp_master on (GEM_EMP_ID=GCF_ASSIGN_TO) ".
        " where GCF_LEAD_CODE='$cust_id' and gcf_followup_status in (1,3) and GCF_FOLLOWUP_DATE >= '$tdate' ";
    if( ($req_purpose=='followup') && ($req_id!='') ){
        $fol_que .= " and GCF_FOLLOWUP_ID!='$req_id' ";
    }
    $afq = "$app_que union all $fol_que";
    $afr = execute_my_query($afq);
    $arr = /*. (string[int][string]) .*/array();
    while ($afd = mysqli_fetch_array($afr)){
        $temp_text = $afd['act_desc']." on ".$afd['action_time']. " for ".$afd['GEM_EMP_NAME'];
        $arr[] = array("id"=>$afd['ids'],"name"=>$temp_text);
    }
    return $arr;
}

/**
 * @return string[int][string]
 */
function existing_apt_metalist(){
    $tran_arr = array(
                    array("type"=> "state","state"=>"on","onValue"=> array("2"),"target"=> array("existing_appt_input")),
                    array("type"=> "state","state"=>"on","onValue"=> array("1"),"target"=> array("next_action","next_action_desc","next_action_date","assign_owner_type"))
                );
    $desc_tran_arr = array(array("type"=> "state","state"=> "on","onValue"=> array("*"),"target"=> array("add_notes")));
    $tag_name = "Appointments";
    $definitions_arr[] = meta_detail_array("appt_type","","local","appt_type_list","radio","on",$tag_name,null,false,'','','2', $tran_arr);
    $definitions_arr[] = meta_detail_array("existing_appt_input","Existing appointments","local","existing_appt_list","checkbox","off",
                            $tag_name,null,false,'','','', $desc_tran_arr,'','','','',null,'','',false,null,'',null,false,'','','',false,false,true);
    $definitions_arr[] = meta_detail_array("add_notes","Add notes to the existing appointments", "text_block","","","off",$tag_name,null,false);
    return $definitions_arr;
}

/**
 * @return string[int][string]
 */
function get_appointment_type_master(){
    return array(
        array("id"=>"0","name"=>"Just completing the activity"),
        array("id"=>"1","name"=>"I want to create new appointment"),
        array("id"=>"2","name"=>"I want to add notes to existing appointment"),
    );
}
/**
 * @param string $cust_user_id
 * @param string $lead_code
 * @param string[int] $mygofrugal_roles_arr
 *
 * @return boolean
 */
function is_autorized_mygofrual_role($cust_user_id, $lead_code, $mygofrugal_roles_arr){
    $mygofrugal_role= implode(',',$mygofrugal_roles_arr);
    $user_mobile = get_single_value_from_single_table("GCL_USERNAME", "gft_customer_login_master", "GCL_USER_ID", $cust_user_id);
    $cont_cond = getContactDtlWhereCondition("GCC_CONTACT_NO", $user_mobile);
    $que1 = " select GCC_CONTACT_NO from gft_customer_contact_dtl ".
        " join gft_pos_users on (GPU_CONTACT_ID=GCC_ID) ".
        " join gft_install_dtl_new on (GID_INSTALL_ID=GPU_INSTALL_ID) ".
        " where GCC_LEAD_CODE='$lead_code' and $cont_cond".($mygofrugal_role!=""?" and GPU_MYGOFRUGAL_ROLE in ($mygofrugal_role)":"");
    $que_res1 = execute_my_query($que1);
    if(mysqli_num_rows($que_res1)==0){
        return false;
    }
    return true;
}
/**
 * @param string $lead_code
 * @return string[string]
 */
function get_proposal_upload_meta_arr($lead_code) {
    $proposal_ids = get_proposal_doc_dtls($lead_code);
    $fileexist = count($proposal_ids);
    $proposal_validation	=	array("file_type"=>array("application/pdf"));
    if($fileexist>0){
        $proposal_validation	=	array("none",array("file_type"=>array("application/pdf")));
    }
    return meta_detail_array("scancopy","Upload Proposal","file","","","on","HQ Proposal",$proposal_validation,false,"","","");
}
/**
 * @param string $lead_code
 * @param string $emp_id
 * @param string $order_no
 * @return string
 */
function upload_proposal($lead_code,$emp_id,$order_no) {
    global $attach_path;
    $HQ_proposal_file_name = '';
    if(isset($_FILES['scancopy']) and $_FILES["scancopy"]['size']!=0 and $order_no!=''){
        $file_name=  /*. (string[int]) .*/ $_FILES['scancopy']['name'];
        $f_temp_name=/*. (string[int]) .*/ $_FILES['scancopy']['tmp_name'];
        $size=       /*. (int[int])    .*/ $_FILES['scancopy']['size'];
        $count_files=count($_FILES['scancopy']['name']);
        $userfile=array();
        $f_name=/*. (string[int]) .*/ array();
        $lead_path=$attach_path."/HQ_Proposals";
        if(!file_exists($lead_path)){
            mkdir($lead_path, 0777);
        }
        $lead_path = $lead_path."/".(int)$lead_code;
        if(!file_exists($lead_path)){
            mkdir($lead_path, 0777);
        }
        for($k=0;$k<$count_files;$k++){
            if($file_name[$k]!='' && $size[$k]!=0){
                $f_name[$k]=basename($file_name[$k]);
                if(move_uploaded_file($f_temp_name[$k],"$lead_path/{$f_name[$k]}")) {
                    $proposal_path	=	"$lead_path/{$f_name[$k]}";
                    $HQ_proposal_file_name=$f_name[$k];
                    $doc_dtl = array();
                    $latest_version = get_latest_version_of_doc($lead_code, '2');
                    $doc_dtl['GCC_LEAD_CODE'] = "$lead_code";
                    $doc_dtl['GCC_DOC_PATH'] = "$proposal_path";
                    $doc_dtl['GCC_DOC_TYPE'] = '2';
                    $doc_dtl['GCC_UPLOADED_BY'] = "$emp_id";
                    $doc_dtl['GCC_UPLOADED_ON'] = date('Y-m-d H:i:s');
                    $doc_dtl['GCC_VERSION_ID'] = (string)((int)$latest_version + 1);
                    $doc_dtl['GCC_STATUS'] = '2';
                    $doc_dtl['GCC_MODIFIED_DATETIME'] = date('Y-m-d H:i:s');
                    $doc_dtl['GCC_ORDER_REF'] = $order_no;
                    $doc_id = array_insert_query("gft_corporate_customer_doc_dtl", $doc_dtl);
                }
            }
        }
    }
    return $HQ_proposal_file_name;
}
/**
 * @return string[int]
 */
function leave_status_arr(){
    $sts_arr = array(0=>"Select",1=>"Absent",2=>"On-Time",3=>"On-Duty",4=>"Late",5=>"Leave",6=>"Permission");
    return $sts_arr;
}
/**
 * @param string $emp_id
 * @param string $cp_lead_code
 *
 * @return string
 */
function get_partner_condition_for_lead_search($emp_id,$cp_lead_code ){
    global  $non_employee_group;
    $cust_cond_secured = "";
    if(is_authorized_group_list($emp_id,$non_employee_group)){
        $cp_lead_code=get_cp_lead_code_for_eid($emp_id);
        $cp_emp_id=get_cp_emp_id_for_leadcode($cp_lead_code);
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
    }
    return $cust_cond_secured;
}
/**
 * @return string[int]
 */
function get_recruitment_request_status(){
    $output = array(1=>"Pending",2=>"In-progress",3=>"Partially Completed",4=>"Completed");
    return $output;
}
?>
