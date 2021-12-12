<?php

/*. forward void function update_lead_status(int $lead_code,int $lead_status,string $vertical=,string $doc=,string $add_onproduct=,boolean $is_customer=,int $prospect_status=); .*/
/*. forward void function update_repeated_visits(string $lead_code,string $visit_date); .*/
/*. forward void function update_lead_incharge(string $lead_code,string $lfd_emp); .*/
/*. forward string function insert_support_entry(int $lead_code,int $productcode,string $productskew,string $version,string $product_type,string $emp_id,string $reason_visit,string $summary,string $GCH_COMPLAINT_CODE,string $GCD_STATUS,string $GCD_SCHEDULE_DATE=,string $GCD_EXTRA_CHARGES=,string $GCD_PROCESS_EMP=,string $GCD_NATURE=,string $gs=,string $problemdesc=,boolean $send_sms=,string $trans_id=,string $GCD_REMARKS=,string $GCD_COMPLAINT_ID=,string $priority=,boolean $new_complaint=,string $uploaded_file_path=,string $GCD_CUST_CALL_TYPE=,string $GCD_ESTIMATED_TIME=,string $gpd=,string $gce=,string $baton_wobbling=,int $business_impact=,string $visit_reason=,string $internal_emotion=); .*/

require_once(__DIR__ ."/dbcon.php");
require_once(__DIR__ ."/file_util.php");
require_once(__DIR__ ."/support_util.php");

/**
 * @param string $lead_code
 * @param string $visit_date
 * 
 * @return void
 */
function update_repeated_visits($lead_code,$visit_date){   
			   
	$query_update_repeated_visit="update gft_activity t1," .
			" (select gld_lead_code,gld_visit_date,count(*),min(gld_activity_id) mact " .
			" from gft_activity where gld_lead_code='$lead_code'  " .
			" and gld_visit_date='$visit_date' group by gld_lead_code,gld_visit_date having count(*)>1) t2 " .
			" set t1.GLD_REPEATED_VISITS='R' " .
			" where t1.gld_lead_code=t2.gld_lead_code and t1.gld_visit_date=t2.gld_visit_date " .
			" and t1.gld_activity_id!=t2.mact ";
	execute_my_query($query_update_repeated_visit);
	$query_update_repeated_visit="update gft_activity t1," .
			" (select gld_lead_code,gld_visit_date,count(*),min(gld_activity_id) mact " .
			" from gft_activity " .
			" where gld_lead_code='$lead_code' and gld_visit_date='$visit_date'" .
			" group by gld_lead_code,gld_visit_date having count(*)>1) t2 " .
			" set t1.GLD_REPEATED_VISITS='Y' " .
			" where t1.gld_lead_code=t2.gld_lead_code and t1.gld_visit_date=t2.gld_visit_date " .
			" and t1.gld_activity_id=t2.mact ";
	execute_my_query($query_update_repeated_visit);
           
}

/**
 * @param string $GCD_COMPLAINT_ID
 * @param boolean $escalated_complaint
 * @param boolean $reopened_complaint
 * @param string $asa_status
 * @param boolean $ismygofrugal_complaint
 * 
 * @return boolean
 */
function send_mailto_complaient_info($GCD_COMPLAINT_ID,$escalated_complaint=false,$reopened_complaint=false,$asa_status='',$ismygofrugal_complaint=false){
	global $address_fields,$query_contact_dtl;
	$query="select d.GCD_ACTIVITY_DATE, h.GCH_COMPLAINT_DATE, GLH_LEAD_CODE $address_fields, h.GCH_COMPLAINT_CODE, " .
			" nt.GCM_NATURE,h.GCH_VERSION, d.GCD_PROCESS_EMP, lm.GEM_EMAIL as email,GCD_NATURE, " .
			" pfm.gpm_product_abr,d.GCD_PROBLEM_SUMMARY,sv.gsm_name,d.GCD_LEVEL,st.gtm_name,pv.gpm_name,d.GCD_PROBLEM_DESC,d.GCD_REMARKS," .
			" d.GCD_UPLOAD_FILE,em.gem_emp_name,h.GCH_LAST_ACTIVITY_ID,emt.GCM_EMOTION_NAME,d.GCD_CONTACT_MAILID,K.GAM_ACTIVITY_DESC, h.GCH_COMPLAINT_ID, ".
			" if(GLH_LEAD_TYPE in (3,13), concat(pg.gpg_support_mail_id,',',pg2.gpg_support_mail_id), pg.gpg_support_mail_id) as support_mail, " .
			" pg.gpg_dev_mail_id as dev_email,h.GCH_CURRENT_STATUS as curr_status from gft_lead_hdr lh $query_contact_dtl " .
			" join gft_customer_support_hdr h on (lh.GLH_LEAD_CODE=h.GCH_LEAD_CODE and h.GCH_COMPLAINT_ID='$GCD_COMPLAINT_ID') " .
			" join gft_customer_support_dtl d on (h.GCH_COMPLAINT_ID = d.GCD_COMPLAINT_ID AND h.GCH_LAST_ACTIVITY_ID =d.GCD_ACTIVITY_ID) " .
			" join gft_product_family_master pfm on (pfm.gpm_product_code=h.GCH_PRODUCT_CODE) " .
			" join gft_product_group_master pg on(pg.gpg_product_family_code=pfm.GPM_head_family AND h.GCH_PRODUCT_SKEW =pg.gpg_skew) " .
			" join gft_product_group_master pg2 on(pg2.gpg_product_family_code=300 AND pg2.gpg_skew='03.0') " .  //hq-support for corporate customers
			" left join gft_emp_master em on em.gem_emp_id=d.GCD_EMPLOYEE_ID " .
			" left join gft_emp_master lm on lm.gem_emp_id=d.GCD_PROCESS_EMP " .
			" left join gft_customer_emotion_master emt on d.GCD_CUSTOMER_EMOTION=emt.GCM_EMOTION_ID " .
			" left join gft_status_master st on h.GCH_CURRENT_STATUS=st.gtm_code " .
			" left join gft_priority_master pv on  d.GCD_PRIORITY=pv.gpm_code " .
			" left join gft_severity_master sv on  d.GCD_SEVERITY=sv.gsm_code " .
			" left join gft_complaint_nature_master nt on GCD_NATURE=nt.GCM_NATURE_ID " .
			" LEFT JOIN gft_activity_master K ON d.GCD_VISIT_REASON=K.GAM_ACTIVITY_ID " .
			" group by GLH_LEAD_CODE ";
	$result=execute_my_query($query);
	$num_rows=mysqli_num_rows($result);
	if($num_rows==0){
		return false;	
    }
	$content="<html><title>Customer Filed an Issue</title><br>";
    $content.="Dear Team,<br><br> ";
   
    $email_to=/*. (string[int]) .*/ array();
    $email_to[] = "support-leads@gofrugal.com";
    $cmp_code=$ass_email=$customer_mailid='';$gcd_nature="";
    $lead_code = "";
    $support_team_mail_id = "";
    $noti_content_config					= array();    
    while($qdata=mysqli_fetch_array($result)){
    	$lead_code = $qdata['GLH_LEAD_CODE'];
    	$cust_name = $qdata['GLH_CUST_NAME'];
    	$ename=$qdata['gem_emp_name'];
    	$email_id=$qdata['GCD_CONTACT_MAILID'];
    	$support_team_mail_id = $qdata['support_mail'];
    	$dev_email = $qdata['dev_email'];
    	$cmp_code=$qdata['GCH_COMPLAINT_CODE'];
    	$ass_email=$qdata['email'];
    	$gcd_nature=$qdata['GCD_NATURE'];
    	$customer_mailid = $qdata['EMAIL'];
    	$filepath="";
    	$upload_file=$qdata['GCD_UPLOAD_FILE'];
    	$uploadfile=explode(',',$upload_file);
    /*	if(isset($qdata['curr_status']) and $qdata['curr_status']=='T2') {
    		array_push($email_to,$dev_email);
    	} */
    	$noti_content_config['Customer_Name']	= array($qdata['GLH_CUST_NAME']);
    	$noti_content_config['Customer_Id']		= array($lead_code);
    	$noti_content_config['comp_id']			= array($qdata['GCH_COMPLAINT_ID']);
    	$noti_content_config['problem_summary']	= array($qdata['GCD_PROBLEM_SUMMARY']);
    	$noti_content_config['problem_description']	= array($qdata['GCD_PROBLEM_DESC']);
    	$noti_content_config['Remarks']			= array($qdata['GCD_REMARKS']);   	
    	
    	foreach($uploadfile as $file1){
    		$filepath.="<a href=\"".get_samee_const("DOMAIN_NAME")."/".get_samee_const("RELATIVE_ATTACH_PATH")."/Support_Upload_Files/$file1\">$file1</a><br>";
    	}
    	if($ename==''){
    		$ename=$email_id;
		}
		$tooltip=get_necessary_data_from_query_for_tooltip($qdata);
	    $content.="<table border=1 cellspacing=5 cellpaddin=5 ><tr><td>Complaint ID</td><td>".$qdata['GCH_COMPLAINT_ID']."</td>" .
	    		  "<tr><td>Date</td><td>".$qdata['GCD_ACTIVITY_DATE']."</td>" .
	    		  "<tr><td>Complaint Date  </td><td>".$qdata['GCH_COMPLAINT_DATE']."</td></tr>" .
	    		  "<tr><td>Customer Name </td><td>".$qdata['GLH_CUST_NAME']."</td></tr>" .
	    		  "<tr><td>Customer MailID</td><td>$customer_mailid</td></tr>".
	    		  "<tr><td>Address </td><td>$tooltip</td></tr>" .
	    		  "<tr><td>Complaint Status </td><td>".$qdata['gtm_name']."</td><tr>" .
	    		  "<tr><td>Complaint Through</td><td>".$qdata['GCM_NATURE']."</td></tr>" .
	    		  "<tr><td>Version </td><td>".$qdata['GCH_VERSION']."</td></tr>" .
	    		  "<tr><td>Problem Summary</td><td>".$qdata['GCD_PROBLEM_SUMMARY']."</td></tr>" .
	              "<tr><td>Description </td><td>".$qdata['GCD_PROBLEM_DESC']."</td></tr>" .
	              "<tr><td>Remarks </td><td>".$qdata['GCD_REMARKS']."</td></tr>" .
	              ($asa_status=='1'?"<tr><td>ALR Status </td><td>ALR Valid</td></tr>":"").
	              ($asa_status=='0'?"<tr><td>ALR Status </td><td>ALR Expired</td></tr>":"").
	              "<tr><td>Attachment</td><td>$filepath</td></tr></table>";	
    	
    }  //enD OF WHILE
    $alr_status_str = "ALR Valid";
    if($asa_status=='0')
    	$alr_status_str = "ALR Expired";
    $content.="This is an automated message from SAM";
    $admin_team = get_samee_const('ADMIN_TEAM_MAIL_ID');
    if($cmp_code=='304'){   //license complaint from pos
    	$rs=send_mail_function($admin_team,$ass_email,"Customer filed a Complaint #:$GCD_COMPLAINT_ID",$content,null,get_samee_const("Sales_Mgmt_Mail"),14,true);
    }else if($cmp_code=='151'){   //Unhappy complaint through Greeting Mailer
    	$rs=send_mail_function($admin_team,$ass_email,"[$alr_status_str]I am a Unhappy Customer - Complaint #:$GCD_COMPLAINT_ID",$content,null,$support_team_mail_id,14,true);
    }else{
    	$subject_line = "Customer filed a ticket $GCD_COMPLAINT_ID ";
    	if($gcd_nature=='18'){
    		$subject_line = "Customer filed a ticket $GCD_COMPLAINT_ID through myGoFrugal app";
    		if($escalated_complaint){
    			$subject_line = "Customer escalated a ticket $GCD_COMPLAINT_ID through myGoFrugal app";
    		}else if($reopened_complaint){
    			$subject_line = "Customer reopened a ticket $GCD_COMPLAINT_ID through myGoFrugal app";
    		}
    	}
    	$rs=send_mail_function($admin_team,$email_to,$subject_line,$content,null,null,14,true);
    }
    //Check and send monitor mail
    if(($ismygofrugal_complaint) && $lead_code!=""){
    	$result_emps = execute_my_query("select GLM_EMP_ID,GEM_EMP_NAME from gft_lead_monitors ".
    								" INNER JOIN gft_emp_master ON(GLM_EMP_ID=GEM_EMP_ID) ".
    								" where GLM_LEAD_CODE='$lead_code' AND GLM_MONITOR_TYPE=2 ");
    	while ($row_emps=mysqli_fetch_array($result_emps)){    
    		$emp_id = $row_emps['GLM_EMP_ID'];
    		$noti_content_config['Employee_Name'] = array($row_emps['GEM_EMP_NAME']);
    		send_formatted_notification_content($noti_content_config,0,92,1,$emp_id,$lead_code);
    	}
    }
    return true;
}

/**
 * @param int $lead_code
 * @param int $lead_status
 * @param string $vertical
 * @param string $doc
 * @param string $add_onproduct
 * @param boolean $is_customer
 * @param int $prospect_status
 * 
 * @return void
 */
function update_lead_status($lead_code,$lead_status,$vertical=null,$doc=null,$add_onproduct=null, $is_customer=false,$prospect_status=0){
	$update_doc='';
	global $uid;
	if($doc!=null ){
		$update_doc=",GLH_APPROX_TIMETOCLOSE='$doc' ";
	}	
	if($vertical!='' and $vertical!=0 and $vertical!=null){
		$update_doc.=",GLH_VERTICAL_CODE='$vertical' ";
	}
	if($add_onproduct!=null){
		$update_doc.=", GLH_INTEREST_ADDON='N'";
	}
	if($is_customer==true){
		$update_doc.=", GLH_IS_CUSTOMER='Y' ";
	}
	$chk_query = " select GLH_STATUS,GLH_LEAD_TYPE from gft_lead_hdr where GLH_LEAD_CODE='$lead_code' ";
	$chk_res = execute_my_query($chk_query);
	$ex_lead_status = '';
	if($row1 = mysqli_fetch_array($chk_res)){
		$ex_lead_status = $row1['GLH_STATUS'];
		$ex_lead_type	= $row1['GLH_LEAD_TYPE'];
		if( ($ex_lead_type=='2') && ($lead_status==9) ){
			return ; //for partner lead type, lead status shouldn't be customer
		}
		if( ($lead_status!='14') && in_array($ex_lead_status, array('9'))){
		    $lead_status = $ex_lead_status;
		}
	}
	if( ($lead_status==3) && ($prospect_status!=0) ){
		$update_doc.=", GLH_PROSPECTS_STATUS='$prospect_status' ";
		if($ex_lead_status!='3'){ //if 3 already a prospect
			$update_doc.=", GLH_PROSPECT_ON=now() ";
			$update_doc.=", GLH_PROSPECT_BY='$uid' ";
			$metric_id = 83; //independent prospect
			if($row1['GLH_LEAD_TYPE']=='3'){
				$metric_id= 84; //hq prospect
			}
			update_daily_achieved($uid, $metric_id, 1,'',$lead_code);
		}
		$post_upd_arr = get_post_update_of_lead_status($prospect_status, 0, $lead_code);
		$updated_lead_status = isset($post_upd_arr['lead_status'])?(int)$post_upd_arr['lead_status']:0;
		if($updated_lead_status > 0){
			$lead_status = $updated_lead_status;
		}
	}
	update_status_change_in_ext($lead_code, $lead_status, $ex_lead_status, $uid);
	$query_ls_update="update gft_lead_hdr set glh_status='$lead_status' $update_doc where glh_lead_code='$lead_code'"; 
	$result=execute_my_query($query_ls_update);
}

/**
 * @param string $lead_code
 * @param string $lfd_emp
 * 
 * @return void
 */
function update_lead_incharge($lead_code, $lfd_emp) {
	if($lfd_emp!=''){
		$query_update="update gft_lead_hdr set glh_lfd_emp_id='$lfd_emp' where glh_lead_code='$lead_code'";
		$result=execute_my_query($query_update);
	}
}
/**
 * @param int $GCD_NATURE
 * 
 * @return string
 */
function get_support_visit_reason($GCD_NATURE=0){
	$reason_visit="24";	
	$reason_visit = ($GCD_NATURE==1?'11':'10');
	$reason_visit = ($GCD_NATURE==6?'53':$reason_visit );
	$reason_visit = ($GCD_NATURE==12?'54':$reason_visit );
	$reason_visit = ($GCD_NATURE==9?'47':$reason_visit );
	$reason_visit = ($GCD_NATURE==8?'32':$reason_visit );
	$reason_visit = ($GCD_NATURE==7?'45':$reason_visit );
	$reason_visit = ($GCD_NATURE==3?'25':$reason_visit );
	$reason_visit = ($GCD_NATURE==13?'51':$reason_visit );
	return $reason_visit;
}
/**
 * @param int $lead_code
 * @param int $productcode
 * @param string $productskew
 * @param string $version
 * @param string $product_type
 * @param string $emp_id
 * @param string $reason_visit
 * @param string $summary
 * @param string $GCH_COMPLAINT_CODE
 * @param string $GCD_STATUS
 * @param string $GCD_SCHEDULE_DATE
 * @param string $GCD_EXTRA_CHARGES
 * @param string $GCD_PROCESS_EMP
 * @param string $GCD_NATURE
 * @param string $gs
 * @param string $problemdesc
 * @param boolean $send_sms
 * @param string $trans_id
 * @param string $GCD_REMARKS
 * @param string $GCD_COMPLAINT_ID
 * @param string $priority
 * @param boolean $new_complaint
 * @param string $uploaded_file_path
 * @param string $GCD_CUST_CALL_TYPE
 * @param string $GCD_ESTIMATED_TIME
 * @param string $gpd
 * @param string $gce
 * @param string $baton_wobbling
 * @param int $business_impact
 * @param string $visit_reason
 * @param string $internal_emotion
 * @param string $mygofrugal_user_id
 * @param string $base_product_id
 * @param boolean $get_activity_id
 * @param string $base_prod_version
 * @param string $sub_status
 * @param string $effort
 * @param string $service_type
 * @param string $product_module
 *  
 * @return string
 */
function insert_support_entry($lead_code,$productcode,$productskew,$version,$product_type,$emp_id,$reason_visit,
				$summary,$GCH_COMPLAINT_CODE,$GCD_STATUS,$GCD_SCHEDULE_DATE=null,$GCD_EXTRA_CHARGES=null,
				$GCD_PROCESS_EMP=null,$GCD_NATURE=null,$gs='4',$problemdesc=null,$send_sms=false,$trans_id='',
				$GCD_REMARKS=null,$GCD_COMPLAINT_ID='',$priority='3',$new_complaint=false,$uploaded_file_path='',
				$GCD_CUST_CALL_TYPE='',$GCD_ESTIMATED_TIME='',$gpd='',$gce='',$baton_wobbling='',$business_impact=0,
				$visit_reason='',$internal_emotion='',$mygofrugal_user_id='',$base_product_id='',$get_activity_id=false,
				$base_prod_version='',$sub_status='',$effort='',$service_type='',$product_module='0'){
    $GCD_ACTIVITY_DATETIME=date('Y-m-d H:i:s');
    if($GCD_NATURE==null){    $GCD_NATURE='8';}  
    $GCD_CONTACT_TYPE='1';
    $GCH_CALL_TYPE=1;
    if($GCD_STATUS=='T6'){$GCH_CALL_TYPE=2;}else if($GCD_STATUS=='T23'){$GCH_CALL_TYPE=6;}
    $GCD_PROBLEM_SUMMARY=$summary;
 	$productskew=substr($productskew,0,4);
 	$ACT_ID='';$authority_name='';$contact_pno='';$GCD_EMAIL='';
 	$gpm='';$uplf='';$grema='';$visit_no='';$GCD_VISIT_TIMEOUT='';
 	if($GCD_COMPLAINT_ID!=''){
	/*	$sql_que1 =" select GCH_COMPLAINT_ID from gft_customer_support_hdr ".
 				" join gft_status_master on (GCH_CURRENT_STATUS=GTM_CODE and GTM_GROUP_ID=3) where GCH_COMPLAINT_ID='$GCD_COMPLAINT_ID' ";
 		$sql_rows1 = mysqli_num_rows(execute_my_query($sql_que1));
 		if($sql_rows1 > 0){ //if complaint in solved group
 			$sql_que2 =" select GTM_CODE from gft_status_master where GTM_CODE='$GCD_STATUS' and GTM_GROUP_ID=3 ";
 			$sql_rows2 = mysqli_num_rows(execute_my_query($sql_que2));
 			if($sql_rows2==0){ //if status not in solved group
 				$GCD_COMPLAINT_ID = '';
 			}
 		} */
 	}
 	//$vm_im_complaint_level = 0;
	if( empty($GCD_COMPLAINT_ID) && ($new_complaint===false) ){
		
		if( in_array($GCH_COMPLAINT_CODE, array(138,150)) ){   //voice mail and missed call
			$send_sms = false;  //special sms will be sent.
		//	$vm_im_complaint_level = 156;
			/* CHECK ALREADY EXIST VM RECORD UNCLOSED 
					 * 2. REASON OF VISIT :call Recieved (24)
					 * 3. SUPPORT STATUS : New /New Non ASA 
					 * 4. GCD_NATURE : CALL RECEIVED (2)
			 */
			 /* FIND MAX OF COMPLAINT ID IN ABOVE CATEGORY : START*/
				$query_find_unclosed_vm=" select GCH_LEAD_CODE,MAX(GCH_COMPLAINT_ID) as max_GCH_COMPLAINT_ID,GCD_STATUS from gft_customer_support_hdr " .
							" join gft_customer_support_dtl on (GCD_COMPLAINT_ID=GCH_COMPLAINT_ID and GCD_ACTIVITY_ID=GCH_LAST_ACTIVITY_ID and " .
							" GCD_STATUS='$GCD_STATUS' AND GCH_COMPLAINT_CODE=$GCH_COMPLAINT_CODE AND GCD_TO_DO=24 and GCD_NATURE=2 ) " .
							" where GCH_LEAD_CODE='$lead_code'  GROUP BY  GCH_LEAD_CODE  having !isnull(max_GCH_COMPLAINT_ID) ";
				$result_find_unclosed_vm=execute_my_query($query_find_unclosed_vm);
				$GCD_COMPLAINT_ID=null;
				if($result_find_unclosed_vm){
						if(mysqli_num_rows($result_find_unclosed_vm)==1){
							$qdc=mysqli_fetch_array($result_find_unclosed_vm);
							$GCD_COMPLAINT_ID=$qdc['max_GCH_COMPLAINT_ID'];
							$GCD_STATUS=$qdc['GCD_STATUS'];
						//	$vm_im_complaint_level = 157;
						}
				}
			 /* FIND MAX OF COMPLAINT ID IN ABOVE CATEGORY : END */	
		}else if($GCH_COMPLAINT_CODE!='' and $productskew!='' and $productcode!=''){
			$query_result="select gch_complaint_id,GCH_LAST_ACTIVITY_ID,GCH_CURRENT_STATUS " .
						" from gft_customer_support_hdr " .
						" join gft_status_master on (GTM_CODE=gch_current_status) ".
						" join gft_customer_support_dtl on (GCD_COMPLAINT_ID=GCH_COMPLAINT_ID and GCD_ACTIVITY_ID=GCH_LAST_ACTIVITY_ID) ".
					  	" where gch_lead_code='$lead_code' " .
						" and GCH_CURRENT_STATUS='$GCD_STATUS' and GCH_COMPLAINT_CODE=$GCH_COMPLAINT_CODE " .
					  	" and gch_product_skew='$productskew' and gch_product_code='$productcode' ";
			//below if commented since not able to correctly compare the summary when it has escaping characters in it. so not required
			/* if($GCD_PROBLEM_SUMMARY!=''){
				$check_txt = mysqli_real_escape_string_wrapper(substr($GCD_PROBLEM_SUMMARY, 0,95));
				$query_result .= " and GCD_PROBLEM_SUMMARY like '$check_txt%' ";
			} */
			$result_query=execute_my_query($query_result);
		    if(mysqli_num_rows($result_query)>0 and $GCD_STATUS!='T6'){
				while($data=mysqli_fetch_array($result_query)){
				   	$GCD_COMPLAINT_ID=$data['gch_complaint_id'];
				   	$GCD_STATUS=$data['GCH_CURRENT_STATUS']; 
				  	$update_activty_must=true;
				  	//$send_sms = false;
				}
			}		  		
		}
		
	}
	$install_id_arr=get_install_id($lead_code,$order_no='',$productcode,$productskew,$fulfilment_no='');
	if(is_array($install_id_arr) && count($install_id_arr)==1) {
		$IS_ASA_CUST=is_asa_cust($install_id_arr[0]);
	}else {
		$IS_ASA_CUST='N';
	}
	if($GCD_COMPLAINT_ID=='' and $productcode!=0 and $productcode!='' and $productskew!='' ){
		$restore_time_ar=get_complaint(null,$GCH_COMPLAINT_CODE);
		$restore_time=($restore_time_ar[0][3]==''?1:(int)$restore_time_ar[0][3]);
		$restore_time=($GCD_STATUS=='T3'?$restore_time_ar[0][4]:$restore_time);
		$schedule_to_support=$GCD_SCHEDULE_DATE;
		if($GCD_SCHEDULE_DATE!=null and $GCD_SCHEDULE_DATE!='0000-00-00 00:00:00'){
			$restore_time=24;
			$schedule_to_support=$GCD_SCHEDULE_DATE;
		}
		$pd_status_dtl=	get_current_product_delivery_status($lead_code);
		$pd_status	=	isset($pd_status_dtl[0])?$pd_status_dtl[0]:'0';
		$pd_handover_dt	=	isset($pd_status_dtl[1])?$pd_status_dtl[1]:'';
		$lead_status	=	get_single_value_from_single_table("GLH_STATUS", "gft_lead_hdr", "GLH_LEAD_CODE", "$lead_code");
		$base_prod_arr  = explode("-", $base_product_id);
		$base_pcode = isset($base_prod_arr[0])?$base_prod_arr[0]:'';
		$base_pskew = isset($base_prod_arr[1])?$base_prod_arr[1]:'';
		$query_up= "insert into gft_customer_support_hdr (GCH_COMPLAINT_ID, GCH_LAST_ACTIVITY_ID, GCH_COMPLAINT_DATE, " .
			" GCH_LEAD_CODE,  GCH_COMPLAINT_CODE, GCH_PRODUCT_CODE,GCH_PRODUCT_SKEW,GCH_VERSION,GCH_PRODUCT_TYPE," .
			" GCH_CURRENT_STATUS,GCH_REPORTED_TIME,GCH_RESTORE_TIME,GCH_ASS_CUST,GCH_CALL_TYPE,GCH_FIRST_SCHEDULE_DATE,".
			" GCH_BUSINESS_IMPACT,GCH_CURRENT_LEAD_STATUS,GCH_CURRENT_PD_STATUS,GCH_LAST_PD_HANDOVER_ON, ".
			" GCH_BASE_PCODE,GCH_BASE_PSKEW,GCH_BASE_PVERSION) values" .
			" ('$GCD_COMPLAINT_ID','$ACT_ID','$GCD_ACTIVITY_DATETIME'," .
			" '$lead_code','$GCH_COMPLAINT_CODE','$productcode','$productskew','$version','$product_type'," .
			" '$GCD_STATUS','$GCD_ACTIVITY_DATETIME',DATE_ADD('$schedule_to_support', INTERVAL $restore_time HOUR),'$IS_ASA_CUST',$GCH_CALL_TYPE,'$schedule_to_support',".
			" '$business_impact','$lead_status','$pd_status','$pd_handover_dt','$base_pcode','$base_pskew','$base_prod_version') ";
		$result_up=execute_my_query($query_up) ;
		if($result_up){
		    $GCD_COMPLAINT_ID=mysqli_insert_id_wrapper();
		}
	}		
	if($GCD_COMPLAINT_ID!=''){
		
	/*File Uploaded start here */
 	$upload_file='';
 	if($uploaded_file_path!=''){
 		$upload_file=$uploaded_file_path;
 	}else if(isset($_FILES['upfile1']) and count($_FILES['upfile1']['name'])>0){
 		if(is_array($_FILES['upfile1']['size'])){
 			$tot_size = array_sum($_FILES['upfile1']['size']);
 		}else{
 			$tot_size = $_FILES['upfile1']['size'];
 		}
 		if($tot_size!=0){
			$yeardir=(!isset($yeardir)?date('Y'):$yeardir);
			//$attached_file=implode(',',$_POST['attached_file']);
			global $attach_path;
			$uploadDir= "$attach_path/Support_Upload_Files";
			$not_required_maintian=$uploadDir.'/';
			if(!file_exists($uploadDir)) {
				mkdir("$uploadDir",0777);
			}
		
			$uploadDir.="/$yeardir";
			if(!file_exists($uploadDir)) {
				mkdir("$uploadDir",0777);
			}
			$uploadDir.="/$GCD_COMPLAINT_ID";
			if(!file_exists($uploadDir)) {
				mkdir("$uploadDir",0777);
			}
			$unix_timestamp=time();
			$uploadDir.="/$unix_timestamp";
			if(!file_exists($uploadDir)) {
				mkdir("$uploadDir",0777);
			}
			
			$attachment_file_tosend=upload_files_to($uploadDir);
			$upload_file=implode(',',$attachment_file_tosend);
			$upload_file=(string)str_replace($not_required_maintian,'',$upload_file);
			$upload_file=mysqli_real_escape_string_wrapper($upload_file);
 		}	
		
	}
	/*File Uploaded end here here */
	if($sub_status=='' or $service_type=='' or $effort=='' or $product_module=='0') {
    	$prev_dtls = get_prev_details_of_support($GCD_COMPLAINT_ID);
    	$sub_status = ($sub_status==''?$prev_dtls['sub_status']:$sub_status);
    	$service_type = ($service_type==''?$prev_dtls['service_type']:$service_type);
    	$effort = ($effort==''?$prev_dtls['effort']:$effort);
    	$product_module = ($product_module=='0'?$prev_dtls['product_module']:$product_module);
	}
		$query_up2=" insert into gft_customer_support_dtl " .
				" (GCD_COMPLAINT_ID, GCD_ACTIVITY_ID, GCD_ACTIVITY_DATE, GCD_EMPLOYEE_ID, " .
				" GCD_NATURE, GCD_STATUS, GCD_CONTACT_TYPE, GCD_CONTACT_PERSION, " .
				" GCD_CUSTOMER_EMOTION, GCD_CONTACT_NO, GCD_CONTACT_MAILID,  GCD_PROCESS_EMP, " .
				" GCD_TO_DO, GCD_SCHEDULE_DATE, GCD_ESTIMATED_TIME, GCD_SEVERITY, " .
				" GCD_PRIORITY, GCD_LEVEL, GCD_PROMISE_MADE, GCD_PROMISE_DATE,GCD_CUST_USERID, " .
				" GCD_FEEDBACK, GCD_PROBLEM_SUMMARY, GCD_PROBLEM_DESC, GCD_UPLOAD_FILE," .
				" GCD_REMARKS, GCD_VISIT_REASON,  GCD_VISIT_TIMEOUT, GCD_EXTRA_CHARGES, GCD_INTERNAL_EMOTION, " .
				" GCD_REPORTED_DATE, GCD_VISIT_NO,GCD_VN_TRANSID,GCD_COMPLAINT_CODE,GCD_CUST_CALL_TYPE,GCD_SUB_STATUS, ".
				" GCD_SERVICE_TYPE,GCD_EFFORT_IN_DAYS,GCD_PRODUCT_MODULE) values" .
				" ('$GCD_COMPLAINT_ID','','$GCD_ACTIVITY_DATETIME','$emp_id' ," .
				" '$GCD_NATURE','$GCD_STATUS','$GCD_CONTACT_TYPE','$authority_name'," .
				" '$gce','$contact_pno','$GCD_EMAIL','$GCD_PROCESS_EMP'," .
				" '$reason_visit','$GCD_SCHEDULE_DATE','$GCD_ESTIMATED_TIME','$gs'," .
				" '$priority','L0','$gpm','$gpd','$mygofrugal_user_id'," .
				" '2','".mysqli_real_escape_string_wrapper($GCD_PROBLEM_SUMMARY)."','".mysqli_real_escape_string_wrapper($problemdesc)."','$upload_file'," .
				" '".mysqli_real_escape_string_wrapper($GCD_REMARKS)."','$visit_reason','$GCD_VISIT_TIMEOUT', '$GCD_EXTRA_CHARGES','$internal_emotion'," .
				" '$GCD_ACTIVITY_DATETIME','$visit_no','$trans_id','$GCH_COMPLAINT_CODE','$GCD_CUST_CALL_TYPE','$sub_status', ".
				" '$service_type','$effort','$product_module') ";
		$result_up2=execute_my_query($query_up2);		
		$ACT_ID=mysqli_insert_id_wrapper();
		update_lead_header_extension($lead_code, $gce,$GCD_COMPLAINT_ID);
		updated_hdr_with_last_actid($complaint_id=$GCD_COMPLAINT_ID,$act_id=$ACT_ID,
			$change_status=$GCD_STATUS,true);
		if($send_sms){    support_sms($ACT_ID,$emp_id);    }
		if($base_product_id!=''){
			$mess = " Support Id - $GCD_COMPLAINT_ID, Summary - $GCD_PROBLEM_SUMMARY";
			if($base_prod_version!=''){
				$mess .= ", Version - $base_prod_version";
			}
			$add_prod_id = $productcode."-".$productskew;
			send_to_zoho_chat_channel($base_product_id,$add_prod_id,$mess);
		}
		if($baton_wobbling!=''){
			$insert_arr['GDQ_REMINDER_TYPE']	= '2'; //Support
			$insert_arr['GDQ_CREATED_EMP'] 		= $emp_id;
			$insert_arr['GDQ_BATON_WOBBLING']	= $baton_wobbling;
			$insert_arr['GDQ_CREATED_DATE']		= date('Y-m-d H:i:s');
			$insert_arr['GDQ_LEAD_CODE']		= $lead_code;
			$insert_arr['GDQ_REF_ID'] 			= $ACT_ID;
			array_update_tables_common($insert_arr, "gft_data_quality", null, null, SALES_DUMMY_ID,null,null, $insert_arr);
		}
	}
	if($get_activity_id) {
		return $ACT_ID;
	}
	return $GCD_COMPLAINT_ID;
}

/**
 * @param string[] $current_status
 * @param int $lead_code
 * @param string $by
 * @param string $vist_date
 * @param int $product_code
 * @param string $change_status
 * 
 * @return void
 */
function support_activity_mv_status($current_status,$lead_code,$by,$vist_date,$product_code,$change_status){
	if($by==''){ $by=SALES_DUMMY_ID;}
	if(is_array($current_status)){
		$current_status=implode("','",$current_status);
	}
	if(!isset($ExtraCharges))$ExtraCharges=0;
	$daquery = "SELECT g.GCD_COMPLAINT_ID, '', '$vist_date', '$by'," .
			" '$vist_date', '8', '$change_status', g.GCD_CONTACT_TYPE," .
			" g.GCD_CONTACT_PERSION, g.GCD_CUSTOMER_EMOTION, g.GCD_CONTACT_NO, g.GCD_CONTACT_MAILID," .
			" g.GCD_PROCESS_EMP, g.GCD_TO_DO, g.GCD_SCHEDULE_DATE, g.GCD_ESTIMATED_TIME," .
			" g.GCD_SEVERITY, g.GCD_PRIORITY, g.GCD_LEVEL, g.GCD_PROMISE_MADE," .
			" g.GCD_PROMISE_DATE, g.GCD_FEEDBACK, g.GCD_PROBLEM_SUMMARY, g.GCD_PROBLEM_DESC," .
			" g.GCD_UPLOAD_FILE, g.GCD_REMARKS,  '$ExtraCharges' " .
			" FROM gft_customer_support_dtl g, gft_customer_support_hdr where " .
			" g.GCD_COMPLAINT_ID =GCH_COMPLAINT_ID AND GCD_ACTIVITY_ID=GCH_LAST_ACTIVITY_ID " .
			" and GCH_LEAD_CODE='$lead_code' and  GCH_PRODUCT_CODE='$product_code' " .
			" and GCH_CURRENT_STATUS in ('$current_status') ";
	$result_get_comp=execute_my_query($daquery);
	if($result_get_comp){
		while($qd=mysqli_fetch_array($result_get_comp)){
			$complaint_id=$qd['GCD_COMPLAINT_ID'];
			$insert = "insert into gft_customer_support_dtl (GCD_COMPLAINT_ID, gcd_activity_id, GCD_ACTIVITY_DATE, " .
					" GCD_EMPLOYEE_ID, " .
					" GCD_REPORTED_DATE, GCD_NATURE, GCD_STATUS, GCD_CONTACT_TYPE, " .
					" GCD_CONTACT_PERSION, GCD_CUSTOMER_EMOTION, gcd_contact_no, GCD_CONTACT_MAILID, " .
					" GCD_PROCESS_EMP, GCD_TO_DO, GCD_SCHEDULE_DATE, GCD_ESTIMATED_TIME, " .
					" GCD_SEVERITY, GCD_PRIORITY, GCD_LEVEL, GCD_PROMISE_MADE, " .
					" GCD_PROMISE_DATE, GCD_FEEDBACK, GCD_PROBLEM_SUMMARY, GCD_PROBLEM_DESC, " .
					" GCD_UPLOAD_FILE, GCD_REMARKS, GCD_EXTRA_CHARGES) " .
					"(".$daquery.") ";
			$result_insert=execute_my_query($insert);
			$act_id=mysqli_insert_id_wrapper();
			updated_hdr_with_last_actid($complaint_id,"$act_id",$change_status,true);		
		}
	}
}				     
?>
