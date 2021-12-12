<?php
/*. forward int function generated_activity_id_for(string $emp_id); .*/
/*. forward void function updated_hdr_with_last_actid(string $GCD_COMPLAINT_ID, string $ACT_ID, string $status,boolean $status_update=,string $version=,string $product_code=,string $product_skew=,string $GCH_COMPLAINT_CODE=,string $call_type=,int $escalation_by=, string $escalation=,string $escalation_count=,string $Restore_time=,boolean $reopened=); .*/
/*. forward void function insert_into_joint_visit(string[string] $detail_arr); .*/
/*. forward string[int] function enter_joint_activity(string[string] $detail_arr); .*/
/*. forward void function update_implementation_status(int $lead_code,int $install_id,string $opcode=); .*/
/*. forward string[int] function insert_in_gft_activity_table(string[string] $detail_arr, string[string] $extra_activity_dtl=, boolean $new_lead=,string[int] $req_joint_emp=,string[int] $req_nxt_joint_emp=); .*/

require_once(__DIR__ ."/audit_util.php");
require_once(__DIR__ ."/visit_submit_in_popup.php");

/**
 * @param string $emp_id
 * 
 * @return int
 */
function generated_activity_id_for($emp_id){
	$act_id_new=0;
	$query_activity_id="select ifnull(max(GLD_ACTIVITY_ID),0) +1 from gft_activity "; 
	$result_activity_id=execute_my_query($query_activity_id,'',true,false);
	while($aid=mysqli_fetch_array($result_activity_id)){
		$act_id_new=(int)$aid[0];
	}
	return 	$act_id_new;
}

/**
 * @param string $prospect_status
 * @param int $reason_for_ps_change
 * @param string $lead_code
 * 
 * @return string[string]
 */
function get_post_update_of_lead_status($prospect_status,$reason_for_ps_change,$lead_code){
	
	if( empty($prospect_status) ) {
		return null;
	}

	$query='';
	/* first check in ps reason master */
	/* second check in ps master */
$num_rows_match=0;
if( $reason_for_ps_change!=-1 and  $reason_for_ps_change!=0  and $reason_for_ps_change!=null){
$query=<<<END
	SELECT gps_status_id,grc_id,GCS_CODE as  plead_status, GRL_ID as prlead_status 
 	FROM gft_prospects_status_master g 	 
 	join  gft_reason_for_change_in_prospect_status rps on (grc_prospect_status=gps_status_id and grc_post_update_lead_status!=0 )
 	join  gft_customer_status_master lsm on (GCS_CODE=GRC_POST_UPDATE_LEAD_STATUS )
 	left join  gft_reason_for_change_lstatus rlsm on (GRL_ID = GRC_POST_UPDATE_REASON_LEAD_STATUS and GRL_LEAD_STATUS=GCS_CODE)
 	where  GPS_STATUS_ID=$prospect_status and   GRC_ID=$reason_for_ps_change 
END;
	$result=execute_my_query($query);
	$num_rows_match=mysqli_num_rows($result);
}	
if($num_rows_match==0){
$query=<<<END
	SELECT gps_status_id,GCS_CODE as  plead_status, GRL_ID as prlead_status ,GPS_POST_UPDATE_REASON_LEAD_STATUS as actual_map_as_reason
	FROM gft_prospects_status_master g 	 
 	join  gft_customer_status_master lsm on (GCS_CODE=GPS_POST_UPDATE_LEAD_STATUS )
 	left join  gft_reason_for_change_lstatus rlsm on (GRL_ID = GPS_POST_UPDATE_REASON_LEAD_STATUS and GRL_LEAD_STATUS=GCS_CODE)
 	where  GPS_STATUS_ID=$prospect_status 
END;
   	$result=execute_my_query($query);
	$num_rows_match=mysqli_num_rows($result);
}
	
	$result=execute_my_query($query);
	if($result!==false and ($num_rows_match == 1) ){
		$data=mysqli_fetch_array($result);
		if($data['plead_status']==null or $data['plead_status']=='' or $data['plead_status']==0) return null;
		
		$change_lead_status_to=/*. (string[string]) .*/ array();
		$change_lead_status_to['lead_status']=$data['plead_status'];
		if(!empty($data['prlead_status']) and $data['prlead_status']!=null){
			$change_lead_status_to['reason_for_ls_change']=$data['prlead_status'];
		}else if(isset($data['actual_map_as_reason']) && $data['actual_map_as_reason']==-1){
			$change_lead_status_to['reason_for_ls_change']='-1';
		}else{
			$change_lead_status_to['reason_for_ls_change']='-1';
		}
		
		if($lead_code!=null){
			/* this check if prospects status already exists no need to update the lead status new */
			$query_lead_status_check="select GLH_STATUS from gft_lead_hdr where GLH_LEAD_CODE=$lead_code and " .
					"GLH_PROSPECTS_STATUS=$prospect_status ";
			if($reason_for_ps_change!='' and $reason_for_ps_change!=0){	
					 $query_lead_status_check.=" and GLH_REASON_FOR_PROSPECT_STATUS_CHANGE=$reason_for_ps_change ";
			}
			$result_lead_status_check=execute_my_query($query_lead_status_check);
			if(mysqli_num_rows($result_lead_status_check)==1){
				return null;
			}	 
		}
		return $change_lead_status_to;
	}else{
	    return null;
	}
}	

/**
 * @param int $lead_code
 * @param int $install_id
 * @param string $opcode
 * 
 * @return void
 */
function update_implementation_status($lead_code,$install_id,$opcode=null){
	global $uid;
	$training_status=0;
	$aproval_ids=isset($_POST['aproval'])?"1":"0";
	if($install_id!='' and $install_id!=0){
		$opcodes = (isset($_REQUEST['opcode'])?mysqli_real_escape_string_wrapper($_REQUEST['opcode']):'');
		if($opcode!='' and isset($opcode) and $opcode!=null){
			$opcodes=$opcode;	
		}
		$resultfinal_status=execute_my_query("SELECT count(*) no_of_task, sum(if(GIMC_STATUS < 5 ,1,0)) pendings, sum(if(GIMC_STATUS = 5,1,0)) signed_off, " .
			" min(GIMC_STATUS) min_status,  sum(GIMC_WORKED_DURATION) total_duration, group_concat(distinct GIMC_APPROVAL) approval_status " .
			" FROM gft_cust_imp_ms_current_status_dtl g join gft_customer_support_hdr go on (g.GIMC_COMPLAINT_ID=go.GCH_COMPLAINT_ID)" .
			" WHERE gimc_opcode = '$opcodes'  ");
			//" WHERE go.GCH_LEAD_CODE=$lead_code  ");
		if( ($ms_status_data=mysqli_fetch_array($resultfinal_status)) and mysqli_num_rows($resultfinal_status)>0){
			$ms_status=1;$tot_no_of_task=0;
			if($ms_status_data['min_status']==-1){  // HOLD
				$ms_status=-1;
			}else if($ms_status_data['no_of_task']==$ms_status_data['signed_off'] and $ms_status_data['pendings']==0){ //signed off
				$ms_status=5;
			}else if($ms_status_data['no_of_task'] > $ms_status_data['signed_off'] and $ms_status_data['pendings']!=0){ //On process
				$ms_status=3;
			}else if($ms_status_data['no_of_task'] > $ms_status_data['signed_off'] and $ms_status_data['pendings']==0 and $ms_status_data['min_status']!= -1){ //Planed
				$ms_status=2;
			}else{ //Not Planed
				$ms_status=1;
			}
			$total_duration=$ms_status_data['total_duration'];
			$tot_no_of_task=$ms_status_data['no_of_task'];
			$ins_id_array=get_two_dimensinal_result_set_from_query("select GID_TRAINING_STATUS from gft_install_dtl_new where GID_INSTALL_ID=$install_id and GID_STATUS='A'");
			$pre_training_status=$ins_id_array[0][0];

			if($ms_status==-1){
				$training_status=0;
			}else if($ms_status<5){
				$training_status=2;
			}else if(($ms_status==5 and $ms_status_data['approval_status']!='Y') or 
			($ms_status==5 and $ms_status_data['approval_status']=='Y' and $pre_training_status=3)){
				$training_status=4;
				update_pd_escalation_in_lead_hrd($lead_code, "","Pending CM Approval");
			}
			//set the Training Status  
			if(isset($opcodes) && $opcodes!='' and $tot_no_of_task>0){
				if($training_status==3){$statuss='Y';}elseif($training_status==4){$statuss='Y';}else{$statuss='N';}
				if($training_status==0){$training_status=-1;}
				$qid=(isset($_POST['qid'])?$_POST['qid']:null);
				$qidans=(isset($_POST['qidans'])?$_POST['qidans']:null);
					$Sql_MS_Status = execute_my_query("SELECT GTS_STATUS_NAME FROM gft_ms_task_status WHERE GTS_STATUS_CODE = $ms_status");
					$audit_hdr=array('GAH_LEAD_CODE'=>$lead_code,
					'GAH_DATE_TIME'=>date('Y-m-d H:i:s'),
					'GAH_OPCODE'=>$opcodes,
					'GAH_AUDIT_BY'=>$uid,
					'GAH_TRAINING_STATUS'=>$training_status);
					if($aproval_ids == 1){
						$audit_hdr['GAH_MY_COMMENTS']=mysqli_real_escape_string_wrapper($_POST['gah_my_comments']);
						$audit_hdr['GAH_CUSTOMER_COMMENTS']=mysqli_real_escape_string_wrapper($_POST['gah_cust_comments']);
						$audit_hdr['GAH_AUDIT_TYPE']=18;
					}else{
						$audit_hdr['GAH_MY_COMMENTS']='MileStone Status is '.mysqli_result($Sql_MS_Status,0,"GTS_STATUS_NAME");
						$audit_hdr['GAH_CUSTOMER_COMMENTS']='';
						$audit_hdr['GAH_AUDIT_TYPE']=25;
					}
					if($training_status == 4){
					$audit_hdr['GAH_RC_APPROVAL_STATUS'] = 'Y';
					$audit_hdr['GAH_MILESTONE_STATUS'] = $statuss;
					}else{
					$audit_hdr['GAH_MILESTONE_STATUS'] = $statuss;
					}
				update_audit_details($audit_hdr,$qid,$qidans); // insert Milestone status in audit header table
			}
			execute_my_query(" update gft_install_dtl_new  set GID_TRAINING_STATUS='$training_status' where GID_INSTALL_ID=$install_id and GID_LEAD_CODE=$lead_code ");
		}else{
			execute_my_query(" update gft_install_dtl_new  set GID_TRAINING_STATUS='0' where GID_INSTALL_ID=$install_id and GID_LEAD_CODE=$lead_code ");
		}
	}else{ // suppose install id empty,then only status update in audit_header table
		$opcodes = (isset($_REQUEST['opcode'])?mysqli_real_escape_string_wrapper($_REQUEST['opcode']):'');
		if($opcode!='' and isset($opcode) and $opcode!=null){
			$opcodes=$opcode;	
		}
		$resultfinal_status=execute_my_query("SELECT count(*) no_of_task, sum(if(GIMC_STATUS < 5 ,1,0)) pendings, sum(if(GIMC_STATUS = 5,1,0)) signed_off, " .
			" min(GIMC_STATUS) min_status,  sum(GIMC_WORKED_DURATION) total_duration, group_concat(distinct GIMC_APPROVAL) approval_status " .
			" FROM gft_cust_imp_ms_current_status_dtl g join gft_customer_support_hdr go on (g.GIMC_COMPLAINT_ID=go.GCH_COMPLAINT_ID)" .
			" WHERE gimc_opcode = '$opcodes'  ");
			//" WHERE go.GCH_LEAD_CODE=$lead_code  ");
		if($ms_status_data=mysqli_fetch_array($resultfinal_status) and mysqli_num_rows($resultfinal_status)>0){
			$ms_status=1;
			if($ms_status_data['min_status']==-1){  // HOLD
				$ms_status=-1;
			}else if($ms_status_data['no_of_task']==$ms_status_data['signed_off'] and $ms_status_data['pendings']==0){ //signed off
				$ms_status=5;
			}else if($ms_status_data['no_of_task'] > $ms_status_data['signed_off'] and $ms_status_data['pendings']!=0){ //On process
				$ms_status=3;
			}else if($ms_status_data['no_of_task'] > $ms_status_data['signed_off'] and $ms_status_data['pendings']==0 and $ms_status_data['min_status']!= -1){ //Planed
				$ms_status=2;
			}else{ //Not Planed
				$ms_status=1;
			}
			$total_duration=$ms_status_data['total_duration'];
			if($install_id!=''){
				$ins_id_array=get_two_dimensinal_result_set_from_query("select GID_TRAINING_STATUS from gft_install_dtl_new where GID_INSTALL_ID=$install_id and GID_STATUS='A'");
				$pre_training_status=$ins_id_array[0][0];
			}else{
				$pre_training_status='';
			}
			if($ms_status==-1){
				$training_status=0;
			}else if($ms_status<5){
				$training_status=2;
			}else if(($ms_status==5 and $ms_status_data['approval_status']!='Y') or 
			($ms_status==5 and $ms_status_data['approval_status']=='Y' and $pre_training_status=3)){
				$training_status=4;
				update_pd_escalation_in_lead_hrd($lead_code, "","Pending CM Approval");
			}
			//set the Training Status  
			if(isset($opcodes) && $opcodes!=''){
				if($training_status==3){$statuss='Y';}elseif($training_status==4){$statuss='Y';}else{$statuss='N';}
				if($training_status==0){$training_status=-1;}
				$qid=(isset($_POST['qid'])?$_POST['qid']:null);
				$qidans=(isset($_POST['qidans'])?$_POST['qidans']:null);
					$Sql_MS_Status = execute_my_query("SELECT GTS_STATUS_NAME FROM gft_ms_task_status WHERE GTS_STATUS_CODE = $ms_status");
					$audit_hdr=array('GAH_LEAD_CODE'=>$lead_code,
					'GAH_DATE_TIME'=>date('Y-m-d H:i:s'),
					'GAH_OPCODE'=>$opcodes,
					'GAH_AUDIT_BY'=>$uid,
					'GAH_TRAINING_STATUS'=>$training_status);
					if($aproval_ids == 1){
						$audit_hdr['GAH_MY_COMMENTS']=mysqli_real_escape_string_wrapper($_POST['gah_my_comments']);
						$audit_hdr['GAH_CUSTOMER_COMMENTS']=mysqli_real_escape_string_wrapper($_POST['gah_cust_comments']);
						$audit_hdr['GAH_AUDIT_TYPE']=18;
					}else{
						$audit_hdr['GAH_MY_COMMENTS']='MileStone Status is '.mysqli_result($Sql_MS_Status,0,"GTS_STATUS_NAME");
						$audit_hdr['GAH_CUSTOMER_COMMENTS']='';
						$audit_hdr['GAH_AUDIT_TYPE']=25;
					}
					if($training_status == 4){
					$audit_hdr['GAH_RC_APPROVAL_STATUS'] = 'Y';
					$audit_hdr['GAH_MILESTONE_STATUS'] = $statuss;
					}else{
					$audit_hdr['GAH_MILESTONE_STATUS'] = $statuss;
					}
					
				update_audit_details($audit_hdr,$qid,$qidans); // insert Milestone status in audit header table
			}
		}
	}
}

/**
 * @param string $support_id
 * @param string $solved_by_emp
 * @return void
 */
function notify_support_owner($support_id,$solved_by_emp) {
    $create_dtl_qry = " select gcd_employee_id,gcd_status,gch_lead_code,gcd_status,gcd_complaint_code,gcd_nature, ".
                    " gcg_is_internal_complaint,gcd_problem_summary,gft_complaint_desc from gft_customer_support_dtl ".
                    " join gft_customer_support_hdr on (gch_complaint_id=gcd_complaint_id and gch_first_activity_id=gcd_activity_id) ".
                    " join gft_complaint_master on (gft_complaint_code=gcd_complaint_code) ".
                    " join gft_complaint_group_master on (gcg_group_id=gft_complaint_group) ".
                    " where gcd_complaint_id='$support_id' ";//and gcd_status in ('T25','T87') and gcd_complaint_code='168' ";
    $create_dtl_res = execute_my_query($create_dtl_qry);
    if($row = mysqli_fetch_array($create_dtl_res)) {
        $emp_id = $row['gcd_employee_id'];
        $create_status = $row['gcd_status'];
        $lead_code = $row['gch_lead_code'];
        $create_complaint = $row['gcd_complaint_code'];
        $create_nature = $row['gcd_nature'];
        $is_internal = $row['gcg_is_internal_complaint'];
        $problem_summary = $row['gcd_problem_summary'];
        $complaint_name = $row['gft_complaint_desc'];
        if(in_array($create_status, array('T25','T87')) and $create_complaint=='168') {
            $status_name = 'Pending PC';
            $reason = "Training & Implementation";
            if($create_status=='T87') {
                $status_name = 'Pending Sales';
                $reason = "Sales";
            }
            $db_content_config = array(
                'Agent_Name'=>array(get_emp_name($emp_id,false)),
                'Employee_Name'=>array(get_emp_name($solved_by_emp,false)),
                'Customer_Name'=>array(get_single_value_from_single_table("glh_cust_name", "gft_lead_hdr", "glh_lead_code", $lead_code)),
                'Customer_Id'=>array($lead_code),
                'reason'=>array($reason),
                'remarks'=>array(CURRENT_SERVER_URL),
                'comp_id'=>array($support_id),
                'status'=>array($status_name)
            );
            send_formatted_mail_content($db_content_config, 8, 345, $emp_id);
        } else if($create_nature=='25' and $is_internal=='Y') {
            $mail_content = "Your $complaint_name support ticket is solved.";
            if($solved_by_emp!=$emp_id) {
                $mail_content = "Your $complaint_name support ticket is solved by ".get_emp_name($emp_id,false).".";
            }
            $db_content_config = array(
                'Agent_Name'=>array(get_emp_name($emp_id,false)),
                'Mail_Content'=>array($mail_content),
                'comp_id'=>array("<a href='".CURRENT_SERVER_URL."/tele_support_activity.php?fcomp_id=$support_id' target='_blank'>$support_id</a>"),
                'problem_summary'=>array($problem_summary),
                'status'=>array("Your $complaint_name support ticket is solved")
            );
            send_formatted_mail_content($db_content_config,8,356,array($emp_id));
        }
    }
}
/**
 * @param string $complaint_id
 * @param string $assigned_to
 * @param string $assigned_by
 * @param string $problem_summary
 *
 * @return void
 */
function send_pending_dev_intimation_mail($complaint_id,$assigned_to,$assigned_by,$problem_summary) {
    $mail_content = "A Pending Developer ticket has been assigned to you."; 
    $assured_by = "";
    if($assigned_by!=$assigned_to) {
        $assigned_by_name = get_single_value_from_single_table("gem_emp_name", "gft_emp_master", "gem_emp_id", $assigned_by);
        $mail_content = "$assigned_by_name has assigned a Pending Developer ticket to you.";
        $assured_by = " assured by $assigned_by_name ";
    }
    $restore_time = get_single_value_from_single_table("gch_restore_time","gft_customer_support_hdr","gch_complaint_id", $complaint_id);
    $restore_time_str = "";
    if($restore_time!='' && $restore_time!='0000-00-00 00:00:00') {
        $restore_time_str = date('M d,Y', strtotime($restore_time));
        $mail_content .= " <br> Please note Restoration Time $assured_by is $restore_time_str. ";
    }
    
    $assigned_to_name = get_single_value_from_single_table("gem_emp_name", "gft_emp_master", "gem_emp_id", $assigned_to);
    $db_content_config = array(
        'Agent_Name'=>array($assigned_to_name),
        'Mail_Content'=>array($mail_content),
        'comp_id'=>array("<a href='".CURRENT_SERVER_URL."/samui/#/ticket/$complaint_id' target='_blank'>$complaint_id</a>"),
        'problem_summary'=>array($problem_summary),
        'status'=>array('Pending Developer ticket is assigned to you')
    );
    send_formatted_mail_content($db_content_config,8,356,array($assigned_to));
}
/**
 * @param string $GCD_COMPLAINT_ID
 * @param string $ACT_ID
 * @param string $status
 * @param boolean $status_update
 * @param string $version
 * @param string $product_code
 * @param string $product_skew
 * @param string $GCH_COMPLAINT_CODE
 * @param string $call_type
 * @param int $escalation_by
 * @param string $escalation
 * @param string $escalation_count
 * @param string $Restore_time
 * @param boolean $reopened
 * 
 * @return void
 */
function updated_hdr_with_last_actid($GCD_COMPLAINT_ID, $ACT_ID, $status,$status_update=false,$version=null,
$product_code=null,$product_skew=null,$GCH_COMPLAINT_CODE=null,$call_type=null,$escalation_by=9999,
$escalation=null,$escalation_count=null,$Restore_time='',$reopened=false){
    global $uid;
	$query_prev_status="select GCH_CURRENT_STATUS,GCH_LAST_ACTIVITY_ID,GCH_FIRST_ACTIVITY_ID,GCH_ASSIGN_TIME from  gft_customer_support_hdr " .
			"where GCH_COMPLAINT_ID='$GCD_COMPLAINT_ID' ";
	$result_prev_status=execute_my_query($query_prev_status);
	$qd=mysqli_fetch_array($result_prev_status);
	$prev_status=$qd['GCH_CURRENT_STATUS'];
	$last_activity=$qd['GCH_LAST_ACTIVITY_ID'];
	$first_activity = (int)$qd['GCH_FIRST_ACTIVITY_ID'];
	$assign_time=$qd['GCH_ASSIGN_TIME'];
	$add_update="";
	if($escalation=='Y'){ $add_update.=",GCH_ESCALATION='Y'," .
			"GCH_ESCALATION_NTIMES=GCH_ESCALATION_NTIMES+1," .
			"GCH_ESCALATION_MARKED_ON=now() ";
			if($escalation_by!='') $add_update.=",GCH_ESCALATION_MARKED_BY='$escalation_by' ";
	}
	if($escalation_count==1 and $escalation=='Y'){
		$add_update.=",GCH_ESCALATION_START_ON =now() ";
	}
	$pending_support=get_status_master_from_group(array(1));
	if(!empty($version)){ $add_update.=",GCH_VERSION='$version' "; }
	if(!empty($product_code)){ $add_update.=",GCH_PRODUCT_CODE='$product_code' ";}
	if(!empty($product_skew)){ $add_update.=",GCH_PRODUCT_SKEW='$product_skew' "; }
	if($call_type!=''){  $add_update.=",GCH_MARK='$call_type' "; }
	if(array_search($status,$pending_support)){
		$add_update.=',GCH_READY_TO_SUPPORT=now()';
	}
	if($Restore_time!=''){$add_update.=", GCH_RESTORE_TIME='$Restore_time' ";}
	$solved_issue=get_status_master_from_group(array(3));
	if(array_search($status,$solved_issue) and $prev_status!=$status){
		$add_update.=',GCH_SOLVED_TIME=now()';
	}if($GCH_COMPLAINT_CODE!=null){$add_update.=",GCH_COMPLAINT_CODE='$GCH_COMPLAINT_CODE'";}
	if($assign_time==''){
		$aresult=execute_my_query("SELECT GCD_PROCESS_EMP, GCD_TO_DO, GCD_SCHEDULE_DATE FROM gft_customer_support_dtl where gcd_activity_id=$ACT_ID ");
		if($data_assign=mysqli_fetch_array($aresult)){
			if($data_assign['GCD_PROCESS_EMP']!='' and $data_assign['GCD_TO_DO']!='' and $data_assign['GCD_SCHEDULE_DATE']!=''){
				$add_update.=',gch_assign_time=now() ';
			}
		}
	}
	if($reopened){
		$add_update .= " ,GCH_REOPENED=1 ";
	}
	$update_query="update gft_customer_support_hdr set GCH_LAST_ACTIVITY_ID='$ACT_ID'," .
			"GCH_CURRENT_STATUS='$status'  $add_update " .
			"where GCH_COMPLAINT_ID='$GCD_COMPLAINT_ID' ";
	execute_my_query($update_query);
	if($prev_status!=$status or $first_activity==$last_activity){
	    if($prev_status!=$status and $status_update==true) {
    		$update_query="insert into gft_status_history (gsh_complaint_id,gsh_activity_id,gsh_last_activity_id," .
            			"gsh_new_status,gsh_old_status) values ('$GCD_COMPLAINT_ID','$ACT_ID','$last_activity'," .
            			"'$status','$prev_status')";
    		execute_my_query($update_query);
	    }
		if(get_single_value_from_single_table("gtm_group_id", "gft_status_master", "gtm_code", $status)=='3' or $status=='T2') { // Solved or pending dev
		    $process_emp = ''; $problem_summary = '';
		    $activity_dtl_qry = "select gcd_process_emp,gcd_problem_summary from gft_customer_support_dtl where gcd_activity_id='$ACT_ID'";
		    $activity_dtl_res = execute_my_query($activity_dtl_qry);
		    if($dtl_row = mysqli_fetch_assoc($activity_dtl_res)) {
		        $process_emp = $dtl_row['gcd_process_emp'];
		        $problem_summary = $dtl_row['gcd_problem_summary'];
		    }
		    if($status=='T2') {
		        send_pending_dev_intimation_mail($GCD_COMPLAINT_ID,$process_emp,$uid,$problem_summary);
		    } else {
		      notify_support_owner($GCD_COMPLAINT_ID, $process_emp);
		    }
		}
	}
	if($first_activity==0){
		$fir_update = " update gft_customer_support_hdr join (select GCD_COMPLAINT_ID,min(GCD_ACTIVITY_ID) as mid from gft_customer_support_dtl where GCD_COMPLAINT_ID='$GCD_COMPLAINT_ID') sb on (sb.GCD_COMPLAINT_ID=GCH_COMPLAINT_ID) ".
					  " set GCH_FIRST_ACTIVITY_ID=mid where GCH_COMPLAINT_ID='$GCD_COMPLAINT_ID' ";
		execute_my_query($fir_update);
	}
	update_dev_table($GCD_COMPLAINT_ID);
	/* $query_repeat_update="update gft_customer_support_dtl t1,(
select GCD_COMPLAINT_ID,date(gcd_activity_date) act_date ,max(gcd_activity_id) max_act,count(*) 
from gft_customer_support_dtl where gcd_complaint_id='$GCD_COMPLAINT_ID'  
group by gcd_complaint_id,date(gcd_activity_date)
having count(*)>1) t2 set t1.GCD_LAST_ACTIVITY_OF_DAY='N' where 
t1.GCD_COMPLAINT_ID=t2.GCD_COMPLAINT_ID and date(t1.GCD_ACTIVITY_DATE)=act_date and 
t1.GCD_ACTIVITY_ID!=t2.max_act ";
	
	$result_repeat_update=execute_my_query($query_repeat_update); */
	 
}

/**
 * @param string[string] $detail_arr
 * 
 * @return string[int]
 */
function enter_joint_activity($detail_arr){
	$submit_msg=/*. (string[int]) .*/ array();
	$GLD_ACTIVITY_ID=$detail_arr['GLD_ACTIVITY_ID'];
	$GLD_EMP_ID=$detail_arr['GLD_EMP_ID'];
	$GLD_LEAD_CODE=$detail_arr['GLD_LEAD_CODE'];
	$GLD_VISIT_DATE=$detail_arr['GLD_VISIT_DATE'];
	if($detail_arr['GLD_VISIT_NATURE_JOINT']!='' and is_array($detail_arr['GLD_VISIT_NATURE_JOINT'])){
		$GLD_VISIT_NATURE_JOINT=array_unique($detail_arr['GLD_VISIT_NATURE_JOINT']);
	}else{
		//return '';
		return $submit_msg; 	   
	}

	if(count($GLD_VISIT_NATURE_JOINT)>0 and is_array($GLD_VISIT_NATURE_JOINT)){
		foreach($GLD_VISIT_NATURE_JOINT as $i => $value){
	    	$query_gld_vn="insert into gft_joint_activity " .
	        			"(GJA_ACTIVITY_ID, GJA_EMP_ID, GJA_LEAD_CODE, GJA_VISIT_DATE," ." GJA_VISIT_NATURE) values " .
	        			"('$GLD_ACTIVITY_ID','$GLD_EMP_ID','$GLD_LEAD_CODE','$GLD_VISIT_DATE','".$GLD_VISIT_NATURE_JOINT[$i]."')";
			$result_gld_vn=execute_my_query($query_gld_vn,'',true,false);
			if(!$result_gld_vn){
				$submit_msg[0].="Error occured while submitting join activity";
				$submit_msg[1].="Error occured while submitting join activity. [$query_gld_vn]" ; 
			}		
		}
	}
	return $submit_msg; 	   
}

/**
 * @param string[string] $detail_arr
 * 
 * @return void
 */
function insert_into_joint_visit($detail_arr){

	$GLD_ACTIVITY_ID=$detail_arr['GLD_ACTIVITY_ID'];
	$GLD_EMP_ID=$detail_arr['GLD_EMP_ID'];
	$GLD_LEAD_CODE=$detail_arr['GLD_LEAD_CODE'];
	$GLD_VISIT_DATE=$detail_arr['GLD_VISIT_DATE'];
	for($i=0;$i<count($detail_arr['join_emp']);$i++){
		$jeid=$detail_arr['join_emp'][$i];
		if($jeid!=''){
		    $insert_joint_visit="replace into gft_joint_visit_dtl (GJV_ACTIVITY_ID, GJV_EMP_ID,GJV_VISIT_DATE,GJV_JOINT_EMP_ID) values " .
		  		    "('$GLD_ACTIVITY_ID','$GLD_EMP_ID','$GLD_VISIT_DATE','$jeid') ";
		    $result_jv=execute_my_query($insert_joint_visit,'',true,false);
		    enter_reporting_dtl($jeid,$GLD_VISIT_DATE,'N','Y',date('Y-m-d H:i:s'));
		}
	}
}

/**
 * @param string $GLD_LEAD_CODE
 * @param string $GLH_LAST_ACTIVITY_ID
 * 
 * @return void
 */
function activity_based_update_on_lead_hdr($GLD_LEAD_CODE,$GLH_LAST_ACTIVITY_ID=null){
	/*update first activity date in lead_hdr:first activity_date,last_activity_date,cnt */
	/* 43 and 45 is sales planning and sales_planning-assign its not consider as activity*/
	execute_my_query("update  gft_lead_hdr t
	inner join (select gld_lead_code,min(gld_visit_date) fd,count(distinct(gld_visit_date)) cnt,max(gld_visit_date) ld
	from gft_activity
	inner join gft_emp_master em on (gem_emp_id=gld_emp_id )
	inner join gft_role_group_master rg on(grg_role_id=gem_role_id and grg_group_id in (5,13) and gem_emp_id!=9999 and gem_role_id not in (14,50))
	where gld_lead_code='$GLD_LEAD_CODE' and (gld_visit_nature not in (43,45) or (gld_visit_nature in (43,45) and GLD_NOTE_ON_ACTIVITY!='' and GLD_NOTE_ON_ACTIVITY!='.'))
	group by gld_lead_code ) t1 on ( t1.gld_lead_code=t.glh_lead_code )
	set GLH_FIRST_ACTIVITY_DATE=fd,GLH_VISIT_COUNT=cnt ,GLH_LAST_ACTIVITY_DATE=ld ,GLH_NDAYS_FOR_FIRST_ACTIVITY=DATEDIFF(GLH_FIRST_ACTIVITY_DATE,GLH_DATE) ");

	/*Purpose : Updating Demo count and last demo given date in lead hdr */
	$query_demo=<<<END
		select GLD_LEAD_CODE, count(if(gld_visit_nature=2 or GJA_VISIT_NATURE=2,GLD_VISIT_DATE,null)) demo_count ,
		max(if(gld_visit_nature=2 or GJA_VISIT_NATURE=2,GLD_VISIT_DATE,'0000-00-00')) demo_date from gft_activity
		left join gft_joint_activity act_j on (gld_activity_id=gja_activity_id and  gja_lead_code=gld_lead_code and GJA_VISIT_NATURE=2)
		where GLD_LEAD_CODE='$GLD_LEAD_CODE'  GROUP BY GLD_LEAD_CODE
END;
	$result_demo=execute_my_query($query_demo);
	$update_lead_hdr=array();
	if($result_demo){
	$qd_demo=mysqli_fetch_array($result_demo);
	$update_lead_hdr['GLH_DEMO_COUNT']=$qd_demo['demo_count'];
			$update_lead_hdr['GLH_LAST_DEMO_ON']=$qd_demo['demo_date'];
	}
	if($GLH_LAST_ACTIVITY_ID!=null){
	$update_lead_hdr['GLH_LAST_ACTIVITY_ID']=$GLH_LAST_ACTIVITY_ID;
	/* this is necessary incase of finding last activity dtl to check next action... */
	}
	$table_name='gft_lead_hdr';
	$lead_hdr_key=array();
	$lead_hdr_key['GLH_LEAD_CODE']=$GLD_LEAD_CODE;
	global $uid;
	array_update_tables_common($update_lead_hdr,$table_name,$lead_hdr_key,null, $uid,$remarks='info after activity in hdr  ',
	$table_column_iff_update=null,$insert_new_row=null);

	/*end of update in lead hdr */

}
/**
 * @param string $cust_id
 * @param string $product_code
 * @param boolean $default_value
 * @param string $mobile_no
 * @param string $email
 *
 * @return boolean
 */
function check_to_skip_activity($cust_id, $product_code, $default_value=true,$mobile_no='',$email=''){
    $create_activity = $default_value;
    if(in_array($product_code, array('601','605','523','524'))){
        $con_pcode = ($product_code=='523' || $product_code=='524')?'605':$product_code;
        $domain_exist = (int)get_single_value_from_single_query("TENANT_SAM_ID", "select TENANT_SAM_ID from gft_tenant_master where TENANT_PRODUCT='$con_pcode' AND TENANT_SAM_ID='$cust_id' AND TENANT_STATUS=1");
        $create_activity = ($domain_exist>0?false:true);
    }
    if($mobile_no!=""){
        $emp_result = get_employee_info_from_contact($mobile_no,$email);
        $emply_id = isset($emp_result['GEM_EMP_ID'])?(int)$emp_result['GEM_EMP_ID']:0;
        if($emply_id>0){
            return false;
        }
        $cp_result=its_active_partner_no($mobile_no,$email);
        if(isset($cp_result['is_cp']) && $cp_result['is_cp']==='true'){
            return false;
        }
    }    
    return $create_activity;
}
/**
 * @param string[string] $detail_arr
 * @param string[string] $extra_activity_dtl
 * @param boolean $new_lead
 * @param string[int] $req_joint_emp
 * @param string[int] $req_nxt_joint_emp
 * 
 * @return string[int]
 */
function insert_in_gft_activity_table($detail_arr,$extra_activity_dtl=null,$new_lead=false,$req_joint_emp=null,$req_nxt_joint_emp=null){
	global $remarks,$conn,$uid,$mobile_uid;

	$result_act=/*. (resource) .*/ null;
	$act_by = (int)$uid;
	if($act_by==0){
	    $act_by = (int)$mobile_uid;
	}
	if($act_by==0){
	    $act_by = SALES_DUMMY_ID;
	}
	if(!isset($detail_arr['GLD_ACTIVITY_BY'])){
		$detail_arr['GLD_ACTIVITY_BY'] = $act_by;
	}
	$agile_contributor = (isset($detail_arr['GLD_AGILE_CONTRIBUTOR'])?(int)$detail_arr['GLD_AGILE_CONTRIBUTOR']:0);
	if($agile_contributor==0) {
	    $detail_arr['GLD_AGILE_CONTRIBUTOR'] = $act_by;
	}
	$detail_arr['GLD_DATE']=date('Y-m-d H:i:s');
	$detail_arr['GLD_CALL_STATUS']='P';
	$submit_msg=/*. (string[int]) .*/ array();
	$submit_msg[0]='';
	$submit_msg[1]='';
	$call_status='P';

	$lead_code=isset($detail_arr['GLD_LEAD_CODE'])?$detail_arr['GLD_LEAD_CODE']:'';
	$submited_activity_id=isset($detail_arr['GLD_ACTIVITY_ID'])?$detail_arr['GLD_ACTIVITY_ID']:'';
    
	$date_condition = date("Y-m-d H:i:s", strtotime("-90 days"));
	$today_dt = date("Y-m-d"); 
	$customer_status_que = "select GLH_STATUS,GLD_DATE from gft_activity ".
	   	" join gft_activity_master on (GAM_ACTIVITY_ID=GLD_VISIT_NATURE) ".
	   	" join gft_lead_hdr on (GLH_LEAD_CODE = GLD_LEAD_CODE) ".
	   	" left join gft_cp_info on (CGI_LEAD_CODE = GLD_LEAD_CODE) ".
	   	" where GLD_LEAD_CODE=$lead_code and GLD_ACTIVITY_BY not in (9998,9999) and GAM_AUTOMATIC_ACTIVITY='N' and CGI_EMP_ID is null ".
	   	" order by gld_activity_id desc limit 1" ;
	$customer_status = execute_my_query($customer_status_que);
	if($dta=mysqli_fetch_assoc($customer_status)){
	    if($dta['GLH_STATUS']=='3' && strtotime($dta['GLD_DATE']) < strtotime($date_condition) && $uid!=''){
	        execute_my_query(" update gft_lead_hdr set GLH_PROSPECT_BY=$uid,GLH_PROSPECT_ON='$today_dt' where GLH_LEAD_CODE=$lead_code ");
	    }
	}
	if($lead_code==''){
		$submit_msg[0]="Lead Code Not Available";
		$submit_msg[1]="Lead Code Not Available";
		$submit_msg[2]="error";
		return $submit_msg;
	}
	$sql_check_lead	=	execute_my_query("SELECT glh_lead_code FROM gft_lead_hdr where glh_lead_code=$lead_code");
	if(mysqli_num_rows($sql_check_lead)==0){
		$submit_msg[0]="Lead Code Not Available in Lead Header";
		$submit_msg[1]="Lead Code Not Available";
		$submit_msg[2]="error";
		return $submit_msg;
	}

	$update_act=false;
	if(!empty($detail_arr['GLD_ACTIVITY_ID']) ){/* incase of edit check with employee id and lead code */
    	$query_activity_id1 ="select GLD_LEAD_CODE,GLD_CALL_STATUS from gft_activity " .
			" where GLD_ACTIVITY_ID=".$detail_arr['GLD_ACTIVITY_ID']." and 
			  GLD_EMP_ID=".$detail_arr['GLD_EMP_ID']." " .
			 " and GLD_LEAD_CODE=".$detail_arr['GLD_LEAD_CODE']." ";
		$result_activity_id1=execute_my_query($query_activity_id1,'',true,false);
		if(mysqli_num_rows($result_activity_id1)!=0){
			$update_act=true;	
			$qd_act=mysqli_fetch_array($result_activity_id1);
			$call_status=$qd_act['GLD_CALL_STATUS'];
			$submit_msg[2]=$detail_arr['GLD_ACTIVITY_ID'];
		}else {
			$update_act=false;
			$submit_msg[0].="Not able to identify Activity.";
    		$submit_msg[1].="Not able to identify Activity. $query_activity_id1 ";
			return $submit_msg;
		}		
	}	
	$resultstatus=execute_my_query("select GLH_STATUS,GLH_PROSPECTS_STATUS,GLH_APPROX_TIMETOCLOSE from gft_lead_hdr where glh_lead_code=".$detail_arr['GLD_LEAD_CODE']."");
	if($data_sta=mysqli_fetch_array($resultstatus)){
		if( empty($detail_arr['GLD_LEAD_STATUS']) ){	$detail_arr['GLD_LEAD_STATUS']=$data_sta['GLH_STATUS'];			}
		if( empty($detail_arr['GLD_APPORX_TIMETOCLOSE']) ){	$detail_arr['GLD_APPORX_TIMETOCLOSE']=$data_sta['GLH_APPROX_TIMETOCLOSE'];			}
		if( empty($detail_arr['GLD_PROSPECTS_STATUS']) && ($detail_arr['GLD_LEAD_STATUS']=='3') ){
			$detail_arr['GLD_PROSPECTS_STATUS'] = $data_sta['GLH_PROSPECTS_STATUS'];
		}
	}
	if(isset($detail_arr['GLD_NEXT_ACTION_DETAIL'])){
		$detail_arr['GLD_NEXT_ACTION_DETAIL']=mysqli_real_escape_string_wrapper($detail_arr['GLD_NEXT_ACTION_DETAIL']);
  	}
  	if(isset($detail_arr['GLD_NEXT_ACTION_DATE'])){
  		$detail_arr['GLD_ACTUAL_NEXT_ACTION_DT']=$detail_arr['GLD_NEXT_ACTION_DATE'];
  	}	
	$detail_arr['GLD_NOTE_ON_ACTIVITY']=mysqli_real_escape_string_wrapper(stripslashes($detail_arr['GLD_NOTE_ON_ACTIVITY']));
	$detail_arr['GLD_CUST_FEEDBACK']=(isset($detail_arr['GLD_CUST_FEEDBACK'])?mysqli_real_escape_string_wrapper($detail_arr['GLD_CUST_FEEDBACK']):'');
	$detail_arr['GLD_SMS_MSG']=(isset($detail_arr['GLD_SMS_MSG'])?mysqli_real_escape_string_wrapper($detail_arr['GLD_SMS_MSG']):'');
	if(isset($detail_arr['GLD_REASON_FOR_STATUS_CHANGE_DTL'])){	
		$detail_arr['GLD_REASON_FOR_STATUS_CHANGE_DTL']=mysqli_real_escape_string_wrapper($detail_arr['GLD_REASON_FOR_STATUS_CHANGE_DTL']);
	}	
	if( !isset($detail_arr['GLD_NEXT_ACTION_TIME']) && isset($detail_arr['GLD_NEXT_ACTION_DATE']) ){
		if(date('Y-m-d')==$detail_arr['GLD_NEXT_ACTION_DATE']){
			$detail_arr['GLD_NEXT_ACTION_TIME'] = date('H:i');
		}
	}
	$gld_lead_status   = isset($detail_arr['GLD_LEAD_STATUS'])?(int)$detail_arr['GLD_LEAD_STATUS']:0;
	$last_act_dtl      = get_last_activity_details($lead_code);
	if( isset($last_act_dtl['GLD_LEAD_STATUS']) && ($last_act_dtl['GLD_LEAD_STATUS']!=$gld_lead_status) ){
	    $state_obj = LeadStatusStateMachine::getInstance();
	    $current_gld_stat = $last_act_dtl['GLD_LEAD_STATUS'];
	    if(!$state_obj->isPossibleNextState($current_gld_stat, $gld_lead_status)){
// 	        $mail_log = "Lead Code : $lead_code , conflict becuase of  ".$state_obj->getStateName($current_gld_stat) ." to ".$state_obj->getStateName($gld_lead_status)." <br><br>";
// 	        $mail_log .= getStackTraceString();
// 	        send_mail_function("sam-support@gofrugal.com","sam-team@gofrugal.com","Invalid Lead status transition during activity Entry",$mail_log,null,null,null,true);
	    }
	}
   	$i=0;$column_name='';$values='';
   	foreach($detail_arr as $key => $value){
		$column_name.=($i!=0?",":"")."$key";
		$values.=($i!=0?",":"")."'$value'";
		$i++;
	}
  	$query1="insert into gft_activity($column_name) values ($values)"; 
	$result_act=execute_my_query($query1,'',$send_mail_alert=true,true);
	$new_activity_id	=	mysqli_insert_id_wrapper();
	$detail_arr['GLD_ACTIVITY_ID']=$new_activity_id;
	if($result_act){
		$submit_msg[2]=$new_activity_id;
		/*Sending sms start */
		if( (isset($detail_arr['GLD_THANK_SMS_NEEDED']) and $detail_arr['GLD_THANK_SMS_NEEDED']==1 ) or $detail_arr['GLD_SMS_MSG']!='' ){	
			$send_sms_dtl[]=$detail_arr['GLD_SMS_MSG'];
			send_sms_after_activity($detail_arr['GLD_EMP_ID'],$detail_arr['GLD_LEAD_CODE'],$detail_arr['GLD_THANK_SMS_NEEDED'],$detail_arr['GLD_SMS_MSG']);
		}
		/*sending sms End*/
	}
	if(!$result_act){ 
		$submit_msg[0].=" $query1 Error occured while entering in Activity details . [updating=".$update_act."]";
		$str=mysqli_error_wrapper();
		$submit_msg[1].="[updating=".$update_act."] .Error occured while saving Activity details .$query1 $str";
		return $submit_msg;
	}
	if($result_act){
		if($detail_arr['GLD_VISIT_NATURE']==54 ){
		    $chat_message=isset($extra_activity_dtl['chat'])?$extra_activity_dtl['chat']:"";
			if($chat_message!=''){			
				execute_my_query("insert into gft_chat_entry (GCE_LEAD_CODE,GCE_CHAT_MSG,GCE_ACTIVITY_ID) values " .
						"($lead_code,'".mysqli_real_escape_string_wrapper(str_replace('%26','&',$chat_message))."',$new_activity_id)");
			}			
		}
		if($detail_arr['GLD_VISIT_NATURE']!=37){/* import from excel */
        	enter_reporting_dtl($detail_arr['GLD_EMP_ID'],$detail_arr['GLD_VISIT_DATE'],'N','Y',$detail_arr['GLD_DATE']);
      	}
		else if($detail_arr['GLD_VISIT_NATURE']==58){	/* Purpose ==lead Qualify */
			update_monitor_lead($detail_arr['GLD_LEAD_CODE'],$detail_arr['GLD_EMP_ID'],$status=1);
		}
		
	
	} 
	$detail_arr['GLD_VISIT_NATURE_JOINT']=isset($extra_activity_dtl['GLD_VISIT_NATURE_JOINT'])?$extra_activity_dtl['GLD_VISIT_NATURE_JOINT']:'';
    enter_joint_activity($detail_arr);
   	$GLD_LEAD_CODE=$detail_arr['GLD_LEAD_CODE'];
	$GLD_EMP_ID=$detail_arr['GLD_EMP_ID'];
	$GLD_VISIT_DATE=$detail_arr['GLD_VISIT_DATE'];
    //JOINT ACTIVITY	

  	if(isset($detail_arr['GLD_VISITED_TYPE']) and ($detail_arr['GLD_VISITED_TYPE']==1) ){ 
  	    if( is_array($req_joint_emp) && (count($req_joint_emp) > 0) ){
  	        $joint_emp=array_unique($req_joint_emp);		
			$detail_arr_ja=array();
  			$detail_arr_ja['GLD_ACTIVITY_ID']=$new_activity_id;
			$detail_arr_ja['GLD_EMP_ID']=$detail_arr['GLD_EMP_ID'];
			$detail_arr_ja['GLD_LEAD_CODE']=$detail_arr['GLD_LEAD_CODE'];
			$detail_arr_ja['GLD_VISIT_DATE']=$detail_arr['GLD_VISIT_DATE'];
			$detail_arr_ja['join_emp']=$joint_emp;
  			insert_into_joint_visit($detail_arr_ja);
  			if(in_array($detail_arr['GLD_VISIT_NATURE'],array('2','48')) && ($detail_arr['GLD_ACTIVITY_STATUS_ID']=='2') ){
  				foreach ($joint_emp as $jemp){
  					update_daily_achieved($jemp, 6, 1, $detail_arr['GLD_VISIT_DATE']);
  				}
  			}
  		}
  	}//END OF IF
  	
  	
	if(isset($detail_arr['GLD_NEXT_VISIT_TYPE']) and ($detail_arr['GLD_NEXT_VISIT_TYPE']==1) and (isset($detail_arr['GLD_NEXT_ACTION_DATE'])) ){
	    if(is_array($req_nxt_joint_emp) && (count($req_nxt_joint_emp)>0) ){
		    $nxt_joint_emp = array_unique($req_nxt_joint_emp);
	        $GLD_ACTION_DATE=$detail_arr['GLD_NEXT_ACTION_DATE'];
	        for($i=0;$i<count($nxt_joint_emp);$i++){
	            $njeid=$nxt_joint_emp[$i];
				$insert_joint_visit="replace into gft_next_joint_visit_dtl (GNJV_ACTIVITY_ID, GNJV_EMP_ID, GNJV_VISIT_DATE, " .
						"GNJV_JOINT_EMP_ID) values ('$new_activity_id','$GLD_EMP_ID','$GLD_ACTION_DATE','$njeid') ";
	    		$result_njv=execute_my_query($insert_joint_visit,'',true,false);
				if(!$result_njv){
	            	$submit_msg[0].="Error occurred while submitting the next joint visit detail.[updating=".$update_act."]"; 
	                $submit_msg[1].="[updating=".$update_act."] \nError occurred while submitting the next joint visit detail.".$insert_joint_visit;
				}
	        }
		}/* end of if */
	}
	//DEMO VERSION INSTALLED
	if(isset($extra_activity_dtl['count_demo_installed']) and $extra_activity_dtl['count_demo_installed']!="0"){
		$count_demo_installed=$extra_activity_dtl['count_demo_installed'];
		$demo_install_products=isset($extra_activity_dtl['gft_demo_product_code'])?$extra_activity_dtl['gft_demo_product_code']:"";
		$demo_install_version=isset($extra_activity_dtl['version'])?$extra_activity_dtl['version']:"";
		for($j=0;$j<$count_demo_installed;$j++){
			if($demo_install_products[$j]!=0 and $demo_install_products[$j]!=''){
				$query_demo_installed="replace into gft_demo_version_install_dtl(GID_ACTIVITY_ID,GID_EMP_ID, GID_LEAD_CODE," .
						" GID_INSTALL_DATE, GID_PRODUCT_CODE, GID_VERSION) values " .
						"('$new_activity_id','$GLD_EMP_ID','$GLD_LEAD_CODE','$GLD_VISIT_DATE'," .
						"'$demo_install_products[$j]','$demo_install_version[$j]')";
				execute_my_query($query_demo_installed,'',true,false);
			}
		}
	}
	if(!$new_lead){
		//update_repeated_visits($GLD_LEAD_CODE,$GLD_VISIT_DATE);
		if(!empty($extra_activity_dtl['assign_to']) and $extra_activity_dtl['assign_lead']=='on' ){
			insert_lead_fexec_dtl($GLD_LEAD_CODE,$extra_activity_dtl['assign_to'],true);
		}
		insert_gft_track_lead_status($GLD_LEAD_CODE,$GLD_VISIT_DATE,$detail_arr['GLD_APPORX_TIMETOCLOSE'],$detail_arr['GLD_LEAD_STATUS'],
		$remarks,(isset($extra_activity_dtl['assign_lead'])?$extra_activity_dtl['assign_lead']:''));		
	} 
	activity_based_update_on_lead_hdr($GLD_LEAD_CODE,$new_activity_id); 
	
	/*End of update to lead hdr*/
	send_lead_monitor_mail($GLD_LEAD_CODE);
	return $submit_msg;
}

/**
 * @param int $emp_id
 * @param int $lead_code
 * @param int $new_lead_status
 * @param boolean $new_lead
 * 
 * @return int
 */
function get_revisit_type($emp_id,$new_lead_status,$lead_code,$new_lead=false) {
	$prospect_cust= array(3,8,9);
	$is_sales_partner = is_authorized_group_list((string)$emp_id, array(5,13));
	$revisit_type = 0;
	$old_lead_status = 0;
	$prev_act_qry = " select glh_status,GLD_EMP_ID,GLD_VISIT_DATE from gft_activity ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GLD_LEAD_CODE) ".
			" join gft_activity_master on (GAM_ACTIVITY_ID=GLD_ACTIVITY_NATURE) ".
			" where GLD_LEAD_CODE='$lead_code' and GAM_AUTOMATIC_ACTIVITY = 'N'".
			" order by GLD_VISIT_DATE desc limit 1 ";
	$prev_act_res = execute_my_query($prev_act_qry);
	$prev_act_count = mysqli_num_rows($prev_act_res);
	$age = 0;
	$visit_date='';
	$lead_type=(int)get_single_value_from_single_table("glh_lead_type", "gft_lead_hdr", "glh_lead_code", $lead_code);
	$sales_partner = false;
	while($row=mysqli_fetch_array($prev_act_res)) {
		$old_lead_status = (int)$row['glh_status'];
		$activity_by = $row['GLD_EMP_ID'];
		$sales_partner = is_authorized_group_list($activity_by, array(5,13));
		$visit_date = $row['GLD_VISIT_DATE'];
	}
	if($prev_act_count===0) {
		if($new_lead_status==26 and $new_lead) {
			$revisit_type = 1;
		} else if($new_lead_status==3 and $new_lead) {
			$revisit_type = 2;
		}
	}else {
		if(!in_array($old_lead_status,$prospect_cust)) {
			if($new_lead_status==3) {
				$revisit_type = 2;
			} else if(!in_array($new_lead_status,$prospect_cust)) {
				if(!$new_lead) {
					$revisit_type = 10;
				} else {
					$revisit_type = 3;
					update_daily_achieved((string)$emp_id, 96, 1);
				}
			}
		} else if($old_lead_status==3) {
			if($sales_partner and $is_sales_partner) {
				if($visit_date!=='') {
					$present_date = date('Y-m-d');
					$age=datediff($visit_date,$present_date);
				}
				if(!in_array($new_lead_status,$prospect_cust) and $age<=90 and $new_lead) {
					if($lead_type===1) {
						$revisit_type = 4;
						update_daily_achieved((string)$emp_id, 97, 1);
					} else if($lead_type == 3) {
						$revisit_type = 9;
						update_daily_achieved((string)$emp_id, 98, 1);
					}
				} else if($new_lead_status==3) {
					$revisit_type = 5;
					if($emp_id == $activity_by and !$new_lead and $age<90) {
						if($lead_type===1) {
							$revisit_type = 4;
							update_daily_achieved((string)$emp_id, 97, 1);
						} else if($lead_type == 3) {
							$revisit_type = 9;
							update_daily_achieved((string)$emp_id, 98, 1);
						}
					}
				} else if(in_array($new_lead_status,array(3,24,7))) {
					if(!$new_lead and $age<=90) {
						$revisit_type = 6;
					}
					else if(in_array($new_lead_status,array(24,7)) and $new_lead and $age>90) {
						if($lead_type===1) {
							$revisit_type = 4;
							update_daily_achieved((string)$emp_id, 97, 1);
						} else if($lead_type == 3) {
							$revisit_type = 9;
							update_daily_achieved((string)$emp_id, 98, 1);
						}
					}
				}
			}
		} else if($old_lead_status==8 or $old_lead_status==9) {
			if(!in_array($new_lead_status,$prospect_cust) and $new_lead) {
				$revisit_type = 7;
			} else if(($new_lead_status==8 or $new_lead_status==9) and !$new_lead) {
				$revisit_type = 8;
			}
		}
	}
	return $revisit_type;
}
/**
 * @param string $chat_id
 * @param string $activity_type
 * @param string $submited_activity_id
 * @param string $agent_id
 * @param string $wrapup_summary
 * @param string $lead_code
 *
 * @return void
 */
function update_chat_wrapup_status($chat_id,$activity_type,$submited_activity_id,$agent_id,$wrapup_summary,$lead_code){
	execute_my_query("INSERT INTO gft_chat_wrapup_dtl(GCW_CONVERSATION_ID,GCW_ACTIVITY_TYPE,GCW_ACTIVITY_ID,chat_id,GCW_COMPLAINT_TYPE) VALUES ('$chat_id','$activity_type','$submited_activity_id','$chat_id','1')");
	execute_my_query("UPDATE  chatbot.conversation_dtl SET cust_id='$lead_code',review_status='2',reviewed_by='$agent_id', review_comment='$wrapup_summary',reviewed_on=now()  where chat_id='$chat_id'");
}
?>
