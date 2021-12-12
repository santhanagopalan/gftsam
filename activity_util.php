<?php
require_once(__DIR__.'/dbcon.php');
require_once(__DIR__.'/classes/LeadStatusStateMachine.php');

/**
 * @param string $g_captcha
 *
 * @return float
 */
function get_recaptcha_score($g_captcha){
    $config = get_connectplus_config();
    $recaptcha_validation_url = "https://www.google.com/recaptcha/api/siteverify?secret=".$config['google_captcha_secret_key']."&response=$g_captcha";
    $sever_response = file_get_contents($recaptcha_validation_url);
    $response = json_decode($sever_response, true);
    if( isset($response['success']) && ($response['success']) ){
        $score = isset($response['score'])?(float)$response['score']:0;
    }else{
        error_log($sever_response);
        $score = -1;
    }
    if($score < 0.5){
        $return_array = array();
        $return_array['error_message'] = "spam request ($score)";
        $return_array['googel_response'] = $sever_response;
        insert_web_request_log(0,0,'54',$return_array);
    }
    return $score;
}

/**
 * @param string $install_id
 * @param string $reason_code
 * @param string $comments
 * 
 * @return void
 */
function mark_installation_entry_as_uninstall($install_id,$reason_code,$comments){
    execute_my_query(" update gft_install_dtl_new set GID_STATUS='U' where GID_INSTALL_ID='$install_id' ");
    $date_time = date("Y-m-d H:i:s");
    $insert_arr = array(
        'GUD_UNINSTALL_DATE'=>$date_time,'GUD_REPORTED_ON'=>$date_time,
        'GUD_REASON_CODE'=>$reason_code, 'GUD_NOTE'=>$comments,
        'GUD_INSTALL_REFF'=>$install_id, 'GUD_ACTIVE_UNINSTALL'=>'U',
        'gud_executive_id'=>SALES_DUMMY_ID, 'gud_approved_by'=>SALES_DUMMY_ID
    );
    $key_arr = array('GUD_INSTALL_REFF'=>$install_id);
    array_update_tables_common($insert_arr, "gft_uninstall_dtl", $key_arr, null, SALES_DUMMY_ID,null,null,$insert_arr);
}

/**
 * @param string $lead_code
 * @param string $offer_alias
 * @param int $status
 *
 * @return void
 */
function process_lead_offer_mapping($lead_code,$offer_alias,$status){
    $que1 = " select GOM_ID,GOM_TYPE,GOM_DAYS,GOM_END_DATE,GOM_PERC from gft_offer_master ".
        " where GOM_ALIAS_CODE='".mysqli_real_escape_string_wrapper($offer_alias)."' and GOM_STATUS=1 ";
    $res1 = execute_my_query($que1);
    if($row1 = mysqli_fetch_array($res1)){
        $offer_id   = $row1['GOM_ID'];
        $offer_perc = $row1['GOM_PERC'];
        $offer_type = (int)$row1['GOM_TYPE'];
        $offer_valid = date('Y-m-d');
        if($offer_type==1){
            $offer_valid = add_date(date('Y-m-d'), $row1['GOM_DAYS']);
        }else if($offer_type==2){
            $offer_valid = $row1['GOM_END_DATE'];
        }
        if($status==2){
            execute_my_query("update gft_lead_offer_dtl set GLO_OFFER_STATUS='$status' where GLO_LEAD_CODE='$lead_code' and GLO_OFFER_ID='$offer_id'");
            return;
        }
        $chk_res = execute_my_query(" select GLO_ID from gft_lead_offer_dtl where GLO_LEAD_CODE='$lead_code' and GLO_OFFER_ID='$offer_id' ");
        if(mysqli_num_rows($chk_res) > 0){
            return;
        }
        $ins_arr = array(
            'GLO_LEAD_CODE'=>$lead_code,'GLO_OFFER_ID'=>$offer_id,
            'GLO_OFFER_VALIDITY'=>$offer_valid,'GLO_OFFER_PERCENT'=>$offer_perc,'GLO_OFFER_STATUS'=>'1'
        );
        array_insert_query("gft_lead_offer_dtl", $ins_arr);
    }
}
/**
 * @param string $lead_code
 * 
 * @return string
 */
function getOfferAvailabilityOfLead($lead_code){
    $result = execute_my_query("select GLO_LEAD_CODE,GLO_OFFER_VALIDITY from gft_lead_offer_dtl where GLO_LEAD_CODE='$lead_code' AND NOW()<=GLO_OFFER_VALIDITY AND GLO_OFFER_STATUS=1");
    if((mysqli_num_rows($result)>0) && ($row=mysqli_fetch_array($result))){
        return date("F d, Y", strtotime($row['GLO_OFFER_VALIDITY']));
    }
    return '';
}
/**
 * @param string $lead_code
 * @param string $emply_id
 * @param string $activity_note
 * @param int $visit_nature
 * @param int $next_action
 * @param string $next_action_date
 * 
 * @return void
 */
function create_appointment($lead_code,$emply_id,$activity_note,$visit_nature,$next_action,$next_action_date){
	if((int)$emply_id==0){
		return ;
	}
	if(is_next_action_exits($lead_code, $emply_id, "$next_action", $next_action_date)){
		return ;
	}
	$activity_dtl = array(
		'GLD_LEAD_CODE'=>"$lead_code",
		'GLD_EMP_ID'=>"$emply_id",
		'GLD_VISIT_DATE'=>date('Y-m-d'),
		'GLD_DATE'=>date('Y-m-d H:i:s'),
		'GLD_NOTE_ON_ACTIVITY'=>$activity_note,
		'GLD_VISIT_NATURE'=>"$visit_nature",
		'GLD_SCHEDULE_STATUS'=>'1',
		'GLD_NEXT_ACTION'=>"$next_action",
		'GLD_NEXT_ACTION_DATE'=>$next_action_date
	);
	insert_in_gft_activity_table($activity_dtl);
}

/**
 * @param string $start_dt
 * @param string $end_dt
 * @param int[int] $skip_lead_stat_arr
 * @param string $employee_id
 * 
 * @return string[int][string]
 */
function get_pending_reminders_based_on_lead_severity($start_dt,$end_dt,$skip_lead_stat_arr,$employee_id){
	$sev_sel = 	" count(distinct if(GSS_STRENGTH='hot',GLH_LEAD_CODE,null)) as hot_cnt, ".
	            " count(distinct if(GSS_STRENGTH='warm',GLH_LEAD_CODE,null)) as warm_cnt ";
	$dt_cond =	" between '$start_dt' and '$end_dt' ";
	$time_cond = " between '".time_to_seconds(date('H:i',strtotime($start_dt)))."' and '".time_to_seconds(date('H:i',strtotime($end_dt)))."' ";
	$comm_join = " join gft_sami_lead_score_dtl on (GSS_LEAD_CODE=GLH_LEAD_CODE) ";
	$comm_wh = "  and GLH_LEAD_TYPE!=8 ";
	if(count($skip_lead_stat_arr) > 0){
		$lead_stat_str = implode(",", $skip_lead_stat_arr);
		$comm_wh .= " and GLH_STATUS not in ($lead_stat_str) ";
	}
	$app_quer = " select GLD_EMP_ID emply, $sev_sel from gft_activity use index(nextdate) ".
				" join gft_activity_master on (GAM_ACTIVITY_ID=GLD_NEXT_ACTION) ".
				" join gft_lead_hdr on (GLH_LEAD_CODE=GLD_LEAD_CODE) ".
				$comm_join.
				" where GLD_SCHEDULE_STATUS in (1,3) and GLD_NEXT_ACTION_DATE $dt_cond and time_to_sec(GLD_NEXT_ACTION_TIME) $time_cond $comm_wh ";
	if((int)$employee_id!=0) $app_quer .= " and GLD_EMP_ID='$employee_id' ";
				
	$fol_quer = " select GCF_ASSIGN_TO emply, $sev_sel from gft_cplead_followup_dtl ".
				" join gft_activity_master on (GAM_ACTIVITY_ID=GCF_FOLLOWUP_ACTION) ".
				" join gft_lead_hdr on (GLH_LEAD_CODE=GCF_LEAD_CODE) ".
				$comm_join.
				" where gcf_followup_status in (1,3) and GCF_FOLLOWUP_DATE $dt_cond and time_to_sec(GCF_FOLLOWUP_TIME) $time_cond $comm_wh ";
	if((int)$employee_id!=0) $fol_quer.= " and GCF_ASSIGN_TO='$employee_id' ";
	
	$sup_quer = " select la.GCD_PROCESS_EMP emply,$sev_sel from gft_customer_support_hdr ".
				" join gft_customer_support_dtl la on (GCH_COMPLAINT_ID=la.GCD_COMPLAINT_ID and GCH_LAST_ACTIVITY_ID=la.GCD_ACTIVITY_ID) ".
				" join gft_status_master on (GTM_CODE=gch_current_status) ".
				" join gft_lead_hdr on (GLH_LEAD_CODE=GCH_LEAD_CODE) ".
				$comm_join.
				" where GTM_GROUP_ID not in (3,7) and GCD_SCHEDULE_DATE $dt_cond $comm_wh ";
	if((int)$employee_id!=0) $sup_quer.= " and GCD_PROCESS_EMP='$employee_id' ";
	
	$que1 = " select emply,sum(hot_cnt) hot_cnt,sum(warm_cnt) warm_cnt from ($app_quer union all $fol_quer union all $sup_quer) t1 ".
			" group by emply ";
	$res1 = execute_my_query($que1);
	$data_arr = /*. (string[int][string]) .*/array();
	while ($row1 = mysqli_fetch_array($res1)){
	    $data_arr[] = array('id'=>$row1['emply'],'hot_cnt'=>$row1['hot_cnt'],'warm_cnt'=>$row1['warm_cnt']);
	}
	return $data_arr;
}

/**
 * @param int $emply_id
 * 
 * @return boolean
 */
function is_active_gft_employee($emply_id){
	$que1 = " select GEM_EMP_ID from gft_emp_master where GEM_EMP_ID='$emply_id' and GEM_STATUS='A' ".
			" and GEM_EMP_ID < 7000 "; // to skip 9999, 9998 and partners
	$res1 = execute_my_query($que1);
	if(mysqli_num_rows($res1)==1){
		return true;
	}
	return false;
}

/**
 * @param string $start_dt
 * @param string $end_dt
 * @param string $employee_id
 * @param int[int] $lead_stat_arr
 * @param boolean $paid_lead_validation
 * 
 * @return int[string]
 */
function get_pending_reminders_based_on_lead_status($start_dt,$end_dt,$employee_id,$lead_stat_arr,$paid_lead_validation=false){
	$out_arr = /*. (int[string]) .*/array();
	$stat_str = implode(",", $lead_stat_arr);
	if($stat_str==''){
		return $out_arr;
	}
	$start_dt = date('Y-m-d',strtotime("$start_dt -1 day"));
	$dt_cond =	" between '$start_dt' and '$end_dt' ";
	$a_start_time_cond = " if(GLD_NEXT_ACTION_DATE='$start_dt', GLD_NEXT_ACTION_TIME>'19:30','1') ";
	$a_end_time_cond   = " if(GLD_NEXT_ACTION_DATE='$end_dt',GLD_NEXT_ACTION_TIME<'19:30','1')  ";
	$f_start_time_cond = " if(GCF_FOLLOWUP_DATE='$start_dt', GCF_FOLLOWUP_TIME>'19:30','1') ";
	$f_end_time_cond   = " if(GCF_FOLLOWUP_DATE='$end_dt',GCF_FOLLOWUP_TIME<'19:30','1')  ";
	$comm_wh = "  and GLH_CONTACT_VERIFIED=2 and GLH_LEAD_TYPE!=8 and GLH_STATUS in ($stat_str) ";
	if($paid_lead_validation){
	    $comm_wh .= " and (GLH_CREATED_CATEGORY in ('62','68') or GLH_PAID_CAMPAIGN!='0000-00-00') ";
	}
	$app_quer = " select count(distinct GLH_LEAD_CODE) as cnt from gft_activity ".
	   	" join gft_activity_master on (GAM_ACTIVITY_ID=GLD_NEXT_ACTION) ".
	   	" join gft_lead_hdr on (GLH_LEAD_CODE=GLD_LEAD_CODE) ".
	   	" where GLD_SCHEDULE_STATUS in (1,3) and GLD_NEXT_ACTION_DATE $dt_cond and $a_start_time_cond and $a_end_time_cond ".
	   	" and GLD_EMP_ID='$employee_id' $comm_wh ";
	
	$fol_quer = " select count(distinct GLH_LEAD_CODE) as cnt from gft_cplead_followup_dtl ".
				" join gft_activity_master on (GAM_ACTIVITY_ID=GCF_FOLLOWUP_ACTION) ".
				" join gft_lead_hdr on (GLH_LEAD_CODE=GCF_LEAD_CODE) ".
				" where gcf_followup_status in (1,3) and GCF_FOLLOWUP_DATE $dt_cond and $f_start_time_cond and $f_end_time_cond  ".
				" and GCF_ASSIGN_TO='$employee_id' $comm_wh ";
	
	$que1 = " select sum(cnt) cnt from ($app_quer union all $fol_quer) t1 ";
	$res1 = execute_my_query($que1);
	$data_arr = /*. (string[int][string]) .*/array();
	if($row1 = mysqli_fetch_array($res1)){
		$out_arr['pending_cnt'] = (int)$row1['cnt'];
	}
	return $out_arr;
}

/**
 * @param string $alias_name
 * 
 * @return string
 */
function get_lead_severity_condition($alias_name){
	global $lead_severity;
	$output = '';
	if($lead_severity!=''){
		if($lead_severity=='-1'){
		    $output = " and $alias_name.GSS_STRENGTH is null ";
		}else{
		    $output = " and $alias_name.GSS_STRENGTH='$lead_severity' ";
		}
	}
	return $output;
}

/**
 * @return string[int]
 */
function get_online_sales_employees(){
    $ids_quer = " select em.GEM_EMP_ID,group_concat(GEM_GROUP_ID) as gid from gft_emp_master em ".
        " join gft_emp_group_master eg on (eg.GEM_EMP_ID=em.GEM_EMP_ID) ".
        " where GEM_ROLE_ID=31 and GEM_STATUS='A' and em.GEM_EMP_ID < 7000 group by GEM_EMP_ID ".
        " having (gid like '%27%' OR gid like '%119%' ) and gid not like '%106%' ";
        // Enable Presales and cloud call group alowed group and exclude intl sales group.
    $ids_resl = execute_my_query($ids_quer);
    $emp_list = /*. (string[int]) .*/array();
    while ($ids_row = mysqli_fetch_array($ids_resl)){
        $emp_list[] = $ids_row['GEM_EMP_ID'];
    }
    return $emp_list;
}

/**
 * @param string $glh_lead_code
 * 
 * @return string[string]
 */
function get_last_activity_details($glh_lead_code){
    $que1 = " select GLD_LEAD_STATUS from gft_activity where GLD_LEAD_CODE='$glh_lead_code' ".
            " order by GLD_ACTIVITY_ID desc limit 1 ";
    $res1 = execute_my_query($que1);
    $ret_arr = /*. (string[string]) .*/array();
    if($row1 = mysqli_fetch_assoc($res1)){
        $ret_arr['GLD_LEAD_STATUS'] = $row1['GLD_LEAD_STATUS'];
    }
    return $ret_arr;
}

/**
 * @param string $ph_no
 * @param string $call_date
 * @param string $duration
 * @param string $call_type
 * @param string $agent_id
 * 
 * @return boolean
 */
function is_duplicate_call($ph_no,$call_date,$duration,$call_type,$agent_id){
    $chk_query =" select GTC_ID from gft_techsupport_incomming_call where GTC_NUMBER='$ph_no' ".
                " and GTC_DATE='$call_date' and GTC_DURATION='$duration' and GTC_CALL_STATUS='$call_type' and GTC_AGENT_ID='$agent_id' ";
    $chk_res = execute_my_query($chk_query);
    if(mysqli_num_rows($chk_res) > 0){
        return true;
    }
    return false;
}

/**
 * @param string $lead_code
 * @param string $contact_group
 *
 * @return string
 */
function get_mobile_number_based_on_contact_group($lead_code,$contact_group){
    $que1 = " select GCC_CONTACT_NO from gft_customer_contact_dtl ".
        " join gft_contact_dtl_group_map on (GCG_LEAD_CODE=GCC_LEAD_CODE and GCG_GROUP_ID='$contact_group') ".
        " where GCC_LEAD_CODE='$lead_code' ";
    $res1 = execute_my_query($que1);
    $mobile_no = "";
    if($row1 = mysqli_fetch_array($res1)){
        $mobile_no = $row1['GCC_CONTACT_NO'];
    }
    return $mobile_no;
}


/**
 * @param string $mcust
 * @param string[int] $scust
 * @param string $lfd_incharge
 * @param int $rule_val
 *
 * @return void
 */
function merge_the_leads($mcust,$scust, $lfd_incharge='', $rule_val=0){
    global $uid;
    $scust_instr=implode(',',$scust);
    $tables_columns=array(array("gft_chat_entry","GCE_LEAD_CODE"),
        array("gft_joint_activity","GJA_LEAD_CODE"),
        array("gft_activity","GLD_LEAD_CODE"),
        array("gft_approved_reinstallation","GAR_LEAD_CODE"),
        array("gft_asa_letter_dispatch","GSL_LEAD_CODE"),
        array("gft_audit_hdr","GAH_LEAD_CODE"),
        array("gft_audit_log_edit_table","GUL_AUDIT_LEAD_CODE"),
        array("gft_bussexp_hdr","GBD_LEAD_CODE"),
        array("gft_competitor_approach_lead","GCA_LEAD_CODE"),
        array("gft_cp_info","CGI_LEAD_CODE"),
        array("gft_leadcode_emp_map","GLEM_LEADCODE"),
        array("gft_cp_agree_dtl","GCA_LEAD_CODE"),
        array("gft_cp_relation","GCR_RESELLER_LEAD_CODE"),
        array("gft_cp_relation","GCR_LEAD_CODE"),
        array("gft_cp_order_dtl","GCO_CUST_CODE"),
        array("gft_customer_contact_dtl","GCC_LEAD_CODE"),
        array("gft_customer_dbsize","GCDB_CUST_ID"),
        array("gft_customer_query_dtl","GCQ_LEAD_CODE"),
        array("gft_customer_quotes","GCQ_LEAD_CODE"),
        array("gft_customer_support_hdr","GCH_LEAD_CODE"),
        array("gft_demo_version_install_dtl","GID_LEAD_CODE"),
        array("gft_install_dtl_new","GID_LEAD_CODE"),
        array("gft_invoice_hdr","gih_lead_code"),
        array("gft_lead_fexec_dtl","GLF_LEAD_CODE"),
        array("gft_lead_monitors","GLM_LEAD_CODE"),
        array("gft_lead_compete_dtl","GLC_LEAD_CODE"),
        array("gft_lead_product_dtl","GLC_LEAD_CODE"),
        array("gft_order_hdr","GOD_LEAD_CODE"),
        array("gft_order_upgradation_dtl","GOU_LEAD_CODE"),
        array("gft_pasa_dtl","GPA_LEAD_CODE"),
        array("gft_pcs_project_hdr","GPH_LEAD_CODE"),
        array("gft_proforma_hdr","GPH_LEAD_CODE"),
        array("gft_patchupdate_dtl","GPD_LEAD_CODE"),
        array("gft_quotation_hdr","GQH_LEAD_CODE"),
        array("gft_receipt_dtl","GRD_LEAD_CODE"),
        array("gft_collection_receipt_dtl","GCR_ORDER_NO"),
        array("gft_receipt_letter_dispatch","GRL_LEAD_CODE"),
        array("gft_reff_commission_hdr","GCH_CPLEAD_CODE"),
        array("gft_sales_planning_redo_track","GSP_LEAD_CODE"),
        array("gft_sms_gateway_info","GSG_LEAD_CODE"),
        array("gft_track_lead_status","GTL_LEAD_CODE"),
        array("gft_upgradation_dtl","GUD_LEAD_CODE"),
        array("gft_clientinstall_dtl","GCD_LEAD_CODE"),
        array("gft_cplead_followup_dtl","GCF_LEAD_CODE"),
        array("gft_cust_group_dtl","GCG_LEAD_CODE"),
        array("gft_customer_detail","GCD_LEAD_CODE"),
        array("gft_contact_dtl_group_map","GCG_LEAD_CODE"),
        array("gft_edm_read_dtl","GER_LEAD_CODE"),
        array("gft_upgrade_letter_dispatch","GUL_LEAD_CODE"),
        array("gft_webpos_gateway_request","GSR_LEAD_CODE"),
        array("gft_techsupport_incomming_call","GTC_LEAD_CODE"),
        array("gft_sending_sms","gos_customer_leadcode"),
        array("gft_download_page_hit","GDP_LEAD_CODE"),
        array("gft_tenant_master","TENANT_SAM_ID"),
        array("gft_otp_master","gom_lead_code"),
        array("gft_coupon_distribution_dtl","GCD_TO_ID"),
        array("gft_techsupport_incomming_call_temp","GTC_LEAD_CODE"),
        array("gft_lead_hdr_ext","GLE_LEAD_CODE"),
        array("gft_cust_env_data","GCD_LEAD_CODE")
    );
    
    $contact='';
    $affected=0;
    $alert_cust_ids	= /*. (string[string]) .*/	array();
    /* Updated min of created date in master data */
    $update_lead_hdr=<<<END
	update gft_lead_hdr lh ,(select min(GLH_CREATED_DATE) mc_date,min(GLH_DATE) mg_date ,MIN(GLH_YR_MONTH) myr_month,
	max(GLH_PAID_CAMPAIGN) as inorganic_visit,max(GLH_ORGANIC_SEARCH_VISIT) as organic_visit
	from gft_lead_hdr where glh_lead_code in ($mcust,$scust_instr)) t
	set  GLH_CREATED_DATE=mc_date,GLH_DATE=mg_date ,GLH_YR_MONTH=myr_month ,GLH_PAID_CAMPAIGN=inorganic_visit ,GLH_ORGANIC_SEARCH_VISIT=organic_visit
	where glh_lead_code=$mcust
END;
    execute_my_query($update_lead_hdr);
    $res_mcust_alert	=	execute_my_query("select GSG_LEAD_CODE from gft_sms_gateway_info where GSG_LEAD_CODE=$mcust and GSG_PRODUCT_CODE=604");
    for($i=0;$i<count($scust);$i++){
        if(!($mcust == $scust[$i])){
            $bquery=" SELECT o.GOD_ORDER_NO,g.* FROM gft_lead_hdr g left join gft_order_hdr o ON (o.GOD_LEAD_CODE=g.GLH_LEAD_CODE) where " .
                " g.GLH_LEAD_CODE=".$scust[$i] ;
            $result=execute_my_query($bquery);
            if($qdata=mysqli_fetch_array($result)){
                for($j=0;$j<count($tables_columns);$j++){
                    $squery[$j]=" select * from {$tables_columns[$j][0]} where {$tables_columns[$j][1]}=".$scust[$i];
                }
                $squery[count($tables_columns)]=" select * from gft_lead_hdr where  GLH_LEAD_CODE=".$scust[$i];
                
                $time_now=date('Y-m-d H:i:s');
                for($j=0;$j<count($squery);$j++){
                    $result=execute_my_query($squery[$j]);
                    $heder_name=feach_data_header($result);
                    while($qdata = mysqli_fetch_array($result)){
                        $data_array=array();
                        for($k=0;$k<count($heder_name);$k++){
                            $data_array[$heder_name[$k]]=preg_replace('/[\x00-\x1F\x80-\xFF]/','',$qdata[$heder_name[$k]]);
                        }
                        $da = json_encode($data_array);
                        $da=mysqli_real_escape_string_wrapper($da);
                        $table_name_data=(count($squery)!=($j+1)?$tables_columns[$j][0]:'gft_lead_hdr');
                        $custdata= "insert into gft_cust_merger_data (gcm_id, gcm_lead_code, " .
                            " querydata, query_table, query_master,gcm_emp_id,merg_slave," .
                            " merg_datetime) " .
                            "values('','".$mcust."','$da','$table_name_data','$mcust'," .
                            "'$uid','{$scust[$i]}','$time_now')";
                        $result_up=execute_my_query($custdata);
                        if($table_name_data=='gft_sms_gateway_info'){
                            $res_check_alert	=	execute_my_query("SELECT GSG_LEAD_CODE FROM gft_sms_gateway_info WHERE GSG_PRODUCT_CODE=604 AND GSG_LEAD_CODE='".$scust[$i]."'");
                            if(mysqli_num_rows($res_check_alert)!=0){
                                $alert_cust_ids[$scust[$i]]	=	$scust[$i];
                            }
                        }
                        if(!$result_up) {
                            die("error".$custdata);
                        }
                    }
                }
                /* if the slave data is already mapped as master for other */
                $mergetable="SELECT group_concat(gcm_id order by gcm_id) FROM gft_cust_merger_data
						WHERE gcm_lead_code=".$scust[$i]." group by gcm_lead_code";
                $result=execute_my_query($mergetable);
                if($data=mysqli_fetch_array($result)){
                    $mergeactivity=$data[0];
                    $mergquery="insert into gft_cust_merger_data(gcm_id, gcm_lead_code, " .
                        " querydata, query_table, query_master,gcm_emp_id,merg_slave,merg_datetime) " .
                        "values('','".$mcust."','$mergeactivity','gft_cust_merger_data'," .
                        "'$mcust','$uid','{$scust[$i]}','$time_now')";
                    execute_my_query($mergquery);
                    execute_my_query(" update gft_cust_merger_data set gcm_lead_code='".$mcust."',query_master='".$mcust."', " .
                        "merg_lastupdate_time='".$time_now."' where gcm_lead_code= ".$scust[$i]);
                }
                $res_cont=execute_my_query(" select group_concat(GCC_CONTACT_NO  separator \"','\") contact from gft_customer_contact_dtl where GCC_LEAD_CODE=".$mcust);
                
                if($data=mysqli_fetch_array($res_cont)){
                    $contact=$data['contact'];
                    $last_digit = substr($contact, -1);
                    if( ($last_digit==",") || ($last_digit=="'") ){
                        $contact = substr($contact, 0, 1023);
                    }
                }
                if($contact!=''){
                    execute_my_query("delete from gft_customer_contact_dtl where GCC_LEAD_CODE=".$scust[$i]." and GCC_CONTACT_NO in ('$contact')");
                }
                for($j=0;$j<count($tables_columns);$j++){
                    $uquery[$j]=" update {$tables_columns[$j][0]} set {$tables_columns[$j][1]}=$mcust where {$tables_columns[$j][1]}={$scust[$i]}";
                }
                $uquery[count($tables_columns)]=" delete from gft_lead_hdr where  GLH_LEAD_CODE={$scust[$i]}";
                $uquery[count($tables_columns)+1]=" update gft_lead_hdr set glh_reference_given=$mcust where glh_reference_given={$scust[$i]}";
                for($j=0;$j<count($uquery);$j++){
                    $result_up=execute_my_query($uquery[$j]);
                    if(mysqli_errno_wrapper()==1062){
                        $delquery=(string)str_replace('select *','delete ',$squery[$j]);
                        execute_my_query($delquery);
                    }elseif(mysqli_error_wrapper()){
                        echo mysqli_error_wrapper();
                    }
                    $affected +=mysqli_affected_rows_wrapper();
                }
                $actuquery =" update gft_activity n,gft_lead_hdr,  " .
                    " (SELECT g.GLD_LEAD_CODE, count(g.GLD_CALL_STATUS) pre, max(g.GLD_VISIT_DATE), " .
                    " GLD_VISIT_DATE, max(GLD_LEAD_STATUS) GLD_LEAD_STATUS " .
                    " FROM gft_activity g WHERE g.GLD_CALL_STATUS='P'" .
                    " AND g.GLD_LEAD_CODE=".$mcust."  GROUP BY  g.GLD_LEAD_CODE having pre>1) g " .
                    " set n.GLD_CALL_STATUS='C'" .
                    " where n.GLD_LEAD_CODE=g.GLD_LEAD_CODE AND g.GLD_VISIT_DATE!=n.GLD_VISIT_DATE AND n.GLD_CALL_STATUS='P' AND n.GLD_LEAD_CODE=GLH_LEAD_CODE ";
                $result_up=execute_my_query($actuquery);
                
                //incase if visit date is same
                $actuquery =" update gft_activity n,gft_lead_hdr,  " .
                    " (SELECT g.GLD_LEAD_CODE, count(g.GLD_CALL_STATUS) pre, max(g.GLD_VISIT_DATE), " .
                    " GLD_VISIT_DATE, max(GLD_LEAD_STATUS) GLD_LEAD_STATUS,max(gld_activity_id) gld_activity_id " .
                    " FROM gft_activity g WHERE g.GLD_CALL_STATUS='P'" .
                    " AND g.GLD_LEAD_CODE=".$mcust."  GROUP BY  g.GLD_LEAD_CODE having pre>1) g " .
                    " set n.GLD_CALL_STATUS='C'	" .
                    " where n.GLD_LEAD_CODE=g.GLD_LEAD_CODE  " .
                    " AND n.GLD_CALL_STATUS='P' AND n.GLD_LEAD_CODE=GLH_LEAD_CODE and " .
                    " n.gld_activity_id!=g.gld_activity_id ";
                //Lead status update removed from the above query , as the Master status is only main to us. (requirement by - Gopi.T)
                $result_up=execute_my_query($actuquery);
                
                //echo $actuquery;
            }
            echo "<br>Total no of affected Rows :".$affected ;
        }
    }/* end of first for */
    $by_emp  = $uid;
    if($lfd_incharge!=''){
        $query_update="update gft_lead_hdr set glh_lfd_emp_id='$lfd_incharge' where glh_lead_code='$mcust'";
        $by_emp = $lfd_incharge;
        execute_my_query($query_update);
        
    }
    
    /* update the web registered lead is New or Existing */
    $update_gft_download_page_hit1=<<<END
	update gft_download_page_hit,gft_lead_hdr set GDP_EXISTING_LEAD=if(GLH_CREATED_DATE < GDP_HIT_DATE,'Y','N')
	where GLH_LEAD_CODE=GDP_LEAD_CODE AND GDP_LEAD_CODE=$mcust and GDP_EXISTING_LEAD='N'
END;
    
    execute_my_query($update_gft_download_page_hit1);
    //send update to TrueAlert
    $request_cust_id_list	=	$mcust;
    $required_alert_sync	=	false;
    if(((mysqli_num_rows($res_mcust_alert)>=1) and (count($alert_cust_ids)>=1)) or ((mysqli_num_rows($res_mcust_alert)==0) and (count($alert_cust_ids)>=2))) {
        foreach ($alert_cust_ids as $key1=>$value1){
            if($alert_cust_ids[$key1]!=''){
                $request_cust_id_list	=	$request_cust_id_list.','.$alert_cust_ids[$key1];
                $required_alert_sync=true;
            }
        }
    }
    if($required_alert_sync){
        $request_id	=get_gateway_request_id();
        $secret1=bin2hex("myownkey");
        $data_sent1="<request_id>".$request_id."</request_id>" .
            "<SAM_id>".$request_cust_id_list."</SAM_id>" ;
        $data_sent="<request_id>".htmlentities(lic_encrypt((string)$request_id,$secret1))."</request_id>" .
            "<SAM_id>".htmlentities(lic_encrypt($request_cust_id_list,$secret1))."</SAM_id>" ;
        $result_r=execute_my_query("insert into gft_smsgareway_request (GSR_REQUEST_ID, GSR_DATE, GSR_ORDER_NO, GSR_LEAD_CODE, GRD_REQUEST_MESSAGE,GRD_REQUEST) " .
            " values('$request_id',date(now()),'0',$mcust, '".mysqli_real_escape_string_wrapper($data_sent)."','".mysqli_real_escape_string_wrapper($data_sent1)."')");
        if($result_r){
            echo "<br>Please wait, Sending request to SMS Gateway....";
            $suc_status=truealert_post($request_id,1);
            if($suc_status){
                echo "<br>SMS Gateway request done";
            }else{
                echo"<br>SMS Gateway request failed";
            }
        }
    }
}

/**
 * @param string $id
 * @param string $text_val
 * @param string $user_id
 * 
 * @return void
 */
function add_notes_to_appointment($id,$text_val,$user_id){
    $today = date("M dS, Y h:i A");
    $user_name= get_emp_name($user_id);
    $text_val = mysqli_real_escape_string_wrapper($text_val);
    $qres =execute_my_query(" select GLD_EMP_ID,GLH_LEAD_CODE,GLH_CUST_NAME from gft_activity join gft_lead_hdr on (GLH_LEAD_CODE=GLD_LEAD_CODE) where GLD_ACTIVITY_ID='$id' ");
    if($qrow = mysqli_fetch_assoc($qres)){
        $send_to = $qrow['GLD_EMP_ID'];
        $cust_name = $qrow['GLH_CUST_NAME'];
        $cust_id = $qrow['GLH_LEAD_CODE'];
        execute_my_query("update gft_activity set GLD_NEXT_ACTION_DETAIL=concat(GLD_NEXT_ACTION_DETAIL,'<hr> On $today $user_name added comment as \"$text_val\"') where GLD_ACTIVITY_ID=$id ");
        $noti_content_config = array(
            'Mail_Subject'=>array("$user_name has added a note to your existing appointment"),
            'Mail_Content'=>array("$user_name has added a note '$text_val' to your existing appointment of customer $cust_name (id:$cust_id) ")
        );
        send_formatted_notification_content($noti_content_config, 0, 105, 1, $send_to);
    }
}

/**
 * @param string $id
 * @param string $text_val
 * @param string $user_id
 *
 * @return void
 */
function add_notes_to_followup($id,$text_val,$user_id){
    $today = date("M dS, Y h:i A");
    $user_name= get_emp_name($user_id);
    $text_val = mysqli_real_escape_string_wrapper($text_val);
    $qres =execute_my_query(" select GCF_ASSIGN_TO,GLH_LEAD_CODE,GLH_CUST_NAME from gft_cplead_followup_dtl join gft_lead_hdr on (GLH_LEAD_CODE=GCF_LEAD_CODE) where GCF_FOLLOWUP_ID='$id' ");
    if($qrow = mysqli_fetch_assoc($qres)){
        $send_to = $qrow['GCF_ASSIGN_TO'];
        $cust_name = $qrow['GLH_CUST_NAME'];
        $cust_id = $qrow['GLH_LEAD_CODE'];
        execute_my_query("update gft_cplead_followup_dtl set GCF_FOLLOWUP_DETAIL=concat(GCF_FOLLOWUP_DETAIL,'<hr> On $today $user_name added comment as \"$text_val\"') where GCF_FOLLOWUP_ID=$id ");
        $noti_content_config = array(
            'Mail_Subject'=>array("$user_name has added a note to your existing appointment"),
            'Mail_Content'=>array("$user_name has added a note '$text_val' to your existing appointment of customer $cust_name (id:$cust_id)")
        );
        send_formatted_notification_content($noti_content_config, 0, 105, 1, $send_to);
    }
}

/**
 * @param string $lead_code
 * 
 * @return string[int][string]
 */
function get_lead_status_list_for_local_dsl($lead_code){
    $lead_stat = get_single_value_from_single_table("GLH_STATUS", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
    $next_stat_ids = LeadStatusStateMachine::getInstance()->getNextPossibleStateWithName($lead_stat);
    if(in_array($lead_stat,array(8,9))){
        return $next_stat_ids;
    }else{
        $allow_stat_arr = /*. (string[int][string]) .*/array();
        foreach ($next_stat_ids as $varr){
            if(!in_array($varr['id'],array(8,9))){
                $allow_stat_arr[] = $varr;  
            }
        }
        return $allow_stat_arr;
    }
}

/**
 * @param string $emply_id
 * @param string $end_date
 * @param string $employee_ids
 * @param string[int] $stored_arr
 * 
 * @return string[int]
 */
function get_reporting_employees_recursively_for_date($emply_id,$end_date,$employee_ids='',$stored_arr=array()){
    $ids = $emply_id;
    if($employee_ids!=''){
        $ids = $employee_ids;
    }
    $que1 = " select GER_EMP_ID,substring_index(group_concat(GER_REPORTING_EMPID order by GER_REPORTING_START_DATE desc,GER_STATUS,GER_REPORTING_END_DATE desc),',',1) as rep_mgr ".
        " from gft_emp_reporting join gft_emp_master on (GEM_EMP_ID=GER_EMP_ID) ".
        " where  GER_REPORTING_START_DATE <= '$end_date' and (GEM_DOR is null or GEM_DOR>='$end_date') and GER_EMP_ID!=1 ".
        " group by GER_EMP_ID having rep_mgr in ($ids) ";
    $res1 = execute_my_query($que1);
    $curr_arr = array();
    while ($row1 = mysqli_fetch_array($res1)){
        $curr_arr[] = $row1['GER_EMP_ID'];
        $stored_arr[] = $row1['GER_EMP_ID'];
    }
    if(count($curr_arr)==0){
        return $stored_arr;
    }
    $employee_ids= implode(",", $curr_arr);
    return get_reporting_employees_recursively_for_date($emply_id,$end_date,$employee_ids,$stored_arr);
}

/**
 * @param string $s
 * 
 * @return string
 */
function makeClickableLinks($s) {
    $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
    return preg_replace($url, '<a href="$0" style="word-break: break-all;color: #337ab7;" target="_blank">$0</a>', $s);
}

/** 
 * @param string $lead_code
 * @param string[int] $product_list_array
 * @param int $request_type
 * @param string[int] $skip_order_fullfill
 * 
 * @return int
 */
function is_duplicate_order_split($lead_code,$product_list_array,$request_type=0,$skip_order_fullfill=array()){//request type=1 if the function call is made from a php page
	$is_duplicate       =   false;
	$return_list		=	/*. (mixed[string]) .*/array();
	$base_prod_arr = array('500','501','502','300','200','308','309');
	for($i=0;$i<count($product_list_array);$i++){
		$product_name_split =   explode('-',$product_list_array[$i]);
		$product_code		=	$product_name_split[0];
		$product_skew		=	isset($product_name_split[1]) ? $product_name_split[1] : "";
		$query_skew_property=	execute_my_query("select GFT_SKEW_PROPERTY from gft_product_master where GPM_PRODUCT_CODE='$product_code' and GPM_PRODUCT_SKEW='$product_skew' and GPM_PRODUCT_TYPE!='8' and GPM_ORDER_TYPE in (0,2) and GFT_SKEW_PROPERTY in (1,2,11) ");
		if(mysqli_num_rows($query_skew_property)>0){//If Product is of Custom License Type or if the order is of user type,just allow the order split
			$skew_version=substr($product_skew, 0,4);
			$product_code_cond = $product_code;
			if(in_array($product_code,$base_prod_arr)){
				$product_code_cond = implode(",", $base_prod_arr);
			}
				$sql1 = " select GOD_ORDER_NO from gft_order_hdr ".
						" join gft_order_product_dtl on(GOD_ORDER_NO=GOP_ORDER_NO and GOD_ORDER_STATUS='A' and GOD_ORDER_SPLICT=0) ".
						" join gft_product_master pm1 on(pm1.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and pm1.GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW and pm1.GPM_LICENSE_TYPE!=3 and pm1.GPM_PRODUCT_TYPE!='8' and pm1.GPM_ORDER_TYPE in (0,1,2) and pm1.GFT_SKEW_PROPERTY in (1,2,11))".//License type 3 for trial version
						" join gft_product_master pm2 on(pm2.GPM_PRODUCT_CODE='$product_code' and pm2.GPM_PRODUCT_SKEW='$product_skew' and pm2.GPM_PRODUCT_CODE not in (71) )".
						" left join gft_product_master pm3 on (pm3.GPM_PRODUCT_CODE=pm2.GFT_LOWER_PCODE and pm3.GPM_PRODUCT_SKEW=pm2.GFT_LOWER_SKEW) ". //to allow edition to edition upgrade and block Trial to Edition orders
						" where GOD_LEAD_CODE='$lead_code' and GOP_PRODUCT_CODE in ($product_code_cond) and (pm2.GFT_LOWER_SKEW!=GOP_PRODUCT_SKEW) and if(pm3.GPM_LICENSE_TYPE is null, 1, pm3.GPM_LICENSE_TYPE=3) ";
				if(count($skip_order_fullfill) > 0){
				    $sql1 .= " and concat(GOP_ORDER_NO,GOP_FULLFILLMENT_NO) not in ('".implode("','", $skip_order_fullfill)."') ";
				}
				
				$sql2 = " select GOD_ORDER_NO from gft_order_hdr ".
						" join gft_cp_order_dtl on(GOD_ORDER_NO=GCO_ORDER_NO and GCO_CP_LEAD_CODE=GOD_LEAD_CODE and GOD_ORDER_STATUS='A' and GOD_ORDER_SPLICT=1) ".
						" join gft_product_master pm1 on(pm1.GPM_PRODUCT_CODE=GCO_PRODUCT_CODE and pm1.GPM_PRODUCT_SKEW=GCO_SKEW and pm1.GPM_LICENSE_TYPE!=3 and pm1.GPM_PRODUCT_TYPE!='8' and pm1.GPM_ORDER_TYPE in (0,1,2) and pm1.GFT_SKEW_PROPERTY in (1,2,11))".//License type 3 for trial version
						" join gft_product_master pm2 on(pm2.GPM_PRODUCT_CODE='$product_code' and pm2.GPM_PRODUCT_SKEW='$product_skew' and pm2.GPM_PRODUCT_CODE not in (71) )".
						" left join gft_product_master pm3 on (pm3.GPM_PRODUCT_CODE=pm2.GFT_LOWER_PCODE and pm3.GPM_PRODUCT_SKEW=pm2.GFT_LOWER_SKEW) ". //to allow edition to edition upgrade and block Trial to Edition orders
						" where GCO_CUST_CODE='$lead_code' and GCO_PRODUCT_CODE in ($product_code_cond) and (pm2.GFT_LOWER_SKEW!=GCO_SKEW) and if(pm3.GPM_LICENSE_TYPE is null, 1, pm3.GPM_LICENSE_TYPE=3)";
				if(count($skip_order_fullfill) > 0){
				    $sql2 .= " and concat(GCO_ORDER_NO,GCO_FULLFILLMENT_NO) not in ('".implode("','", $skip_order_fullfill)."') ";
				}
				$check_query = "$sql1 union all $sql2";
				$query_check_duplicate=execute_my_query($check_query);
			if(mysqli_num_rows($query_check_duplicate)>0){
				$is_duplicate = true ;
				break;
			}
		}
	}
	/* New lead - Check duplicate base product */
	$lc=0;
	if(count($product_list_array)>1){
    	for($i=0;$i<count($product_list_array);$i++){
    	    $pcode_list = explode("-", $product_list_array[$i]);
    	    $pcode =  $pcode_list[0];
    	    $get_skew_property = execute_my_query("select GFT_SKEW_PROPERTY from gft_product_master where GPM_PRODUCT_CODE='$pcode_list[0]' and GPM_PRODUCT_SKEW='$pcode_list[1]' and GPM_PRODUCT_TYPE!='8' and GPM_ORDER_TYPE in (0,1,2) and GFT_SKEW_PROPERTY in (1,2,11) ");
    	    if($res = mysqli_fetch_assoc($get_skew_property)){
        	    $pskew_property =  $res['GFT_SKEW_PROPERTY'];
        	    if($pskew_property==1 && in_array($pcode,$base_prod_arr)){
        	        if($lc>=1) {
        	            $is_duplicate = true;
        	            break;
        	        }
        	        $lc++;
        	    }
    	    }
    	}
	}
	
	if($request_type==0){
		$return_list['is_duplicate']	=	$is_duplicate;
		$return_list['duplicate_product_index'] = $i;
		echo  json_encode($return_list);
		exit;
	} else if($request_type==2) {
		if($is_duplicate) {
			return -1;
		}
		return $i;
	} else{
		return $i;
	}
}
/**
 * @param string $GLH_CREATED_CATEGORY
 * @param string $request_from
 * @param string $product_code
 * @param string $numberOfStores
 * @param string $lead_type
 * @param string $vertical_name
 * @param string $duplicate_entry
 * @param string $registrationNote
 *
 * @return string[string]
 */
function getVisitEntryDesc($GLH_CREATED_CATEGORY, $request_from, $product_code, $numberOfStores, $lead_type, $vertical_name, $duplicate_entry,$registrationNote=''){
    $return_arr = array();
    $next_action=62;
    $visit_nature = 1;
    if($duplicate_entry){
        $visit_nature=94;
    }
    $GLD_CUST_FEEDBACK="Trial download from website";
    $next_action=49;
    if((int)$GLH_CREATED_CATEGORY==17){
        $next_action=49;
        $GLD_CUST_FEEDBACK="Request a call back from website";
    }else if((int)$GLH_CREATED_CATEGORY==50){
        $next_action=49;
        $GLD_CUST_FEEDBACK="Gofrugal blog subscription";
    }else if((int)$GLH_CREATED_CATEGORY==18){
        $GLD_CUST_FEEDBACK="Online demo from website";
        $next_action=48;
    }else if((int)$GLH_CREATED_CATEGORY==34){
        $next_action=2;
        $GLD_CUST_FEEDBACK="Onsite demo from website";
    }else if((int)$GLH_CREATED_CATEGORY==51){
        $next_action=73;
        $GLD_CUST_FEEDBACK="Live chat from website";
    }else if((int)$GLH_CREATED_CATEGORY==48){
        $next_action=49;
        $GLD_CUST_FEEDBACK="Registration from Social Media";
    }else if((int)$GLH_CREATED_CATEGORY==40){
        $next_action=49;
        $GLD_CUST_FEEDBACK="myPulse registration from website";
    }else if((int)$GLH_CREATED_CATEGORY==46){
        $next_action=49;
        $GLD_CUST_FEEDBACK="EarnSmart registration from web";
    }else if((int)$GLH_CREATED_CATEGORY==70){
        $visit_nature=94;
        $next_action=49;
        $GLD_CUST_FEEDBACK="GoDeliver + OrderEasy implementation";
    }else if((int)$GLH_CREATED_CATEGORY==71){
        $visit_nature=91;
        $next_action=49;
        $GLD_CUST_FEEDBACK="COVID-19 resources download from the website";
    }else if((int)$GLH_CREATED_CATEGORY==73){
        $visit_nature=91;
        $next_action=49;
        $GLD_CUST_FEEDBACK="Order Easy Standalone registration from web and entered number of store is '$numberOfStores' during the registration";
    }else if(in_array($GLH_CREATED_CATEGORY, array('54','63','64','65'))){
        $next_action=49;
        $short_app_name = get_single_value_from_single_table("GPM_PRODUCT_NAME", "gft_product_family_master", "GPM_PRODUCT_CODE", $product_code);
        $short_app_name = str_replace(array("Android","iOS","-"), "", $short_app_name);
        $GLD_CUST_FEEDBACK="$short_app_name registration from web";
    }else if((int)$GLH_CREATED_CATEGORY==59){
        $next_action=49;
        $GLD_CUST_FEEDBACK="Connected Banking registration from website. $registrationNote";
    }else if(in_array($GLH_CREATED_CATEGORY, array(43,45,47,57))){
        $next_action=49;
        $GLD_CUST_FEEDBACK = get_single_value_from_single_table("gpg_product_name", "gft_product_group_master", "gpg_product_family_code", $product_code)." registration from web";
    }else if(($request_from=='ipad' or $request_from=='android') and $product_code=='605'){
        $next_action=49;
        $GLD_CUST_FEEDBACK="ServQuick registration from $request_from";
    }else if($product_code=='605'){
        $GLD_CUST_FEEDBACK="ServQuick registration from website";
        if($GLH_CREATED_CATEGORY==32){$GLD_CUST_FEEDBACK="ServQuick Callback from website";}
        $next_action=49;
    }else if($product_code=='601'){
        $GLD_CUST_FEEDBACK="TruePOS registration from website";
        if((int)$GLH_CREATED_CATEGORY==32){$GLD_CUST_FEEDBACK="TruePOS Callback from website";}
        $next_action=49;
    }else if($product_code=='263' || $product_code=='264'){
        $GLD_CUST_FEEDBACK="SellSmart registration from website";
        $next_action=49;
    }else if((int)$GLH_CREATED_CATEGORY==52){
        $next_action=103;
        $GLD_CUST_FEEDBACK="Customer registered ERP enquiry for $vertical_name vertical.";
	$visit_nature=103;
    }else if((int)$GLH_CREATED_CATEGORY==61){
        $next_action=49;
        $GLD_CUST_FEEDBACK="Restaurant registration from website with interest in food aggregator integration";
    }
    if($lead_type=='2'){
        $next_action=50;
        $GLD_CUST_FEEDBACK="Partner Registration from Website";
    }
    if((int)$numberOfStores>0){
        $GLD_CUST_FEEDBACK = $GLD_CUST_FEEDBACK." and entered number of store is '$numberOfStores' during the registration";
    }
    $return_arr[0] = $next_action;
    $return_arr[1] = $GLD_CUST_FEEDBACK;
    $return_arr[2] = $visit_nature;
    return $return_arr;
    
}
/**
 * @param int $cust_id
 * @param int $type
 * @param boolean $status
 * @param int $user_id
 * @param string $prime_reason
 * 
 * @return void
 */
function update_lead_attribute($cust_id,$type,$status,$user_id,$prime_reason=''){
    $enabled_by = $enabled_create_time =  $enabled_update_time = '';
    $disabled_by = $disabled_create_time = $disabled_update_time = '';
    if($status){
        $enabled_by = $user_id;
        $enabled_create_time = $enabled_update_time = date('Y-m-d H:i:s');
        $reason_col = "GLR_ENABLED_REASON";
    }else{
        $disabled_by = $user_id;
        $disabled_create_time = $disabled_update_time = date('Y-m-d H:i:s');
        $reason_col = "GLR_DISABLED_REASON";
    }
    $que1 = " select GLR_ID,GLR_ENABLED_CREATED_TIME,GLR_DISABLED_CREATED_TIME from gft_lead_registration ".
            " where GLR_LEAD_CODE='$cust_id' and GLR_TYPE='$type' ";
    $res1 = execute_my_query($que1);
    if($row1 = mysqli_fetch_array($res1)){ //update
        $enabled_create_time = $row1['GLR_ENABLED_CREATED_TIME'];
        $disabled_create_time = $row1['GLR_DISABLED_CREATED_TIME'];
        $update_columns = "";
        $update = false;
        if($status){
            $update_columns .= "GLR_STATUS=1,GLR_ENABLED_UPDATED_TIME=now(),GLR_ENABLED_BY=$user_id,GLR_ENABLED_REASON='$prime_reason'";
            $update_columns .= ((int)$enabled_create_time==0) ? ",GLR_ENABLED_CREATED_TIME=now()" : "";
            $update = true;
        }else{
            if(is_authorized_group($user_id, 127)){
                $update_columns .= "GLR_STATUS=0,GLR_DISABLED_UPDATED_TIME=now(),GLR_DISABLED_BY=$user_id,GLR_DISABLED_REASON='$prime_reason'";
                $update_columns .= ($disabled_create_time==0) ? ",GLR_DISABLED_CREATED_TIME=now()" : "";
                $update = true;
            }
        }
        if($update) execute_my_query(" update gft_lead_registration set $update_columns where GLR_ID='".$row1['GLR_ID']."' ");
    }else{ // insert
        $new_lead = array(
            'GLR_LEAD_CODE'             => $cust_id,
            'GLR_TYPE'                  => $type,
            'GLR_STATUS'                => $status,
            'GLR_ENABLED_BY'            => $enabled_by,
            'GLR_ENABLED_CREATED_TIME'  => $enabled_create_time,
            'GLR_ENABLED_UPDATED_TIME'  => $enabled_update_time,
            'GLR_DISABLED_BY'           => $disabled_by,
            'GLR_DISABLED_UPDATED_TIME' => $disabled_update_time,
            'GLR_DISABLED_CREATED_TIME' => $disabled_create_time,
            $reason_col=>$prime_reason
        );
        if($status){
            array_insert_query('gft_lead_registration',$new_lead);
        }else{
            if(is_authorized_group($user_id, 127)){
                array_insert_query('gft_lead_registration',$new_lead);
            }
        }
    }
}
/**
 * @param int $lead_code
 * @param int $type
 * @param boolean $status
 *
 * @return string
 */
function is_valid_lead_registration($lead_code,$type,$status){
    $is_valid = 'N';
    $exe_que = execute_my_query(" select glr_lead_code from gft_lead_registration where glr_type=$type and glr_lead_code='$lead_code' and glr_status=$status ");
    if(mysqli_num_rows($exe_que)>0){
        $res = mysqli_fetch_assoc($exe_que);    
        if($res['glr_lead_code']){
            $is_valid = 'Y';
        }
    }
    return $is_valid;
}
/**
 * @param int $partner_id
 * @param boolean $is_emp_id
 * @param int $type
 *
 * @return boolean
 */
function check_partner_with_sub_type($partner_id=0,$is_emp_id=false,$type=0){
    // $partner_id = Partner emp_id or partner lead code
    // $is_emp_id =  True-Emp id False-Lead code
    // $type = partner sub type
    if($is_emp_id){
        $whr_que = " CGI_EMP_ID=$partner_id ";
    }else{
        $whr_que = " CGI_LEAD_CODE=$partner_id ";
    }
    $que  =   " select GLH_LEAD_SUBTYPE from gft_cp_info  ".
              " join gft_lead_hdr on (GLH_LEAD_CODE=CGI_LEAD_CODE) ".
              " where $whr_que and GLH_LEAD_SUBTYPE=$type ";
    $exe_que = execute_my_query($que);
    $result = (mysqli_num_rows($exe_que) > 0 ? true : false); 
    return $result;
}

/**
 * @param string $lead_code
 * 
 * @return void
 */
function post_to_integration_demo_portal($lead_code){
    $que1 = " select GLH_LEAD_CODE,GLH_CUST_NAME,GEM_MOBILE,GEM_EMAIL,GID_STORE_URL,GID_ORDER_NO,GID_FULLFILLMENT_NO ".
            " from gft_lead_hdr join gft_emp_master on (GLH_LEAD_CODE=GEM_LEAD_CODE) ".
            " join gft_install_dtl_new on (GID_LEAD_CODE=GLH_LEAD_CODE) ".
            " where GLH_LEAD_CODE='$lead_code' and GID_LIC_PCODE=120 and GID_STATUS!='U' ".
            " and GID_STORE_URL!='' and GID_STORE_URL is not null ";
    $res1 = execute_my_query($que1);
    if($row1 = mysqli_fetch_array($res1)){
        $arr = array(
            'customerId'=>$row1['GLH_LEAD_CODE'],
            'webReporterUrl'=>$row1['GID_STORE_URL']."/",
            'outletName'=>$row1['GLH_CUST_NAME'],
            'customerName'=>$row1['GLH_CUST_NAME'],
            'customerMobile'=>$row1['GEM_MOBILE'],
            'customerEmail'=>$row1['GEM_EMAIL'],
            "orderNo"=>$row1['GID_ORDER_NO'].substr("0000".$row1['GID_FULLFILLMENT_NO'],-4),
            "outletId"=>1,
            "baseProduct"=>"rpos6"
        );
        $post_data = json_encode(array("body"=>$arr));
        $config = get_connectplus_config();
        $url_to_hit = $config['integration_emp_url']."/do/auto-configure";
        do_curl_to_connectplus($lead_code, $url_to_hit, $post_data, array("Content-Type:application/json","X-Auth-Token:4e248943-d818-4390-9919-4f796b737b7f"));
    }
}

/**
 * @param string $emply_id
 * @param string $search_txt
 *
 * @return string[int]
 */
function get_allowed_employees_for_role_switch($emply_id,$search_txt=""){
    $allowed_emp_arr = /*. (string[int]) .*/array();
    if(is_authorized_group_list($emply_id, array(19,24,96))){ //access to all
        $aqr = execute_my_query(" select gem_emp_id from gft_emp_master where gem_status='A' and gem_emp_name like '%$search_txt%' ");
        while ($da = mysqli_fetch_assoc($aqr)){
            $allowed_emp_arr[] = $da['gem_emp_id'];
        }
    }else{ //access based on reporting
        $reporting_till_bottom = get_reporting_hierarchy_top_to_bottom($emply_id);
        $till_arr = explode(",", $reporting_till_bottom);
        foreach ($till_arr as $va) {
            $allowed_emp_arr[] = $va;
        }
        $cpq = " select GLEM_EMP_ID from gft_leadcode_emp_map ".
            " join gft_emp_master on (GEM_EMP_ID=GLEM_EMP_ID and GEM_STATUS='A') ".
            " join gft_cp_info on (CGI_LEAD_CODE=GLEM_LEADCODE) ".
            " where cgi_incharge_emp_id in ($reporting_till_bottom) ";
        $cpr = execute_my_query($cpq);
        while ($cpd = mysqli_fetch_array($cpr)){
            $allowed_emp_arr[] = $cpd['GLEM_EMP_ID'];
        }
        if(is_authorized_group($emply_id, null,67)){ // Field coordinator role
            $pc_res = execute_my_query(" select gem_emp_id from gft_emp_master where gem_role_id=2 and gem_status='A' and gem_emp_name like '%$search_txt%' ");
            while ($pc_data = mysqli_fetch_assoc($pc_res)){
                $allowed_emp_arr[] = $pc_data['gem_emp_id'];
            }
        }
        // CST team web group - 109
        $team_id = (int)get_single_value_from_single_query("WEB_GROUP", "select WEB_GROUP from gft_emp_master where web_group='109' and gem_status='A' and gem_emp_id=$emply_id");
        if($team_id==109){
            $cst = execute_my_query(" select gem_emp_id from gft_emp_master where web_group='109' and gem_status='A' and gem_emp_id!=$emply_id and gem_emp_name like '%$search_txt%' ");
            while ($cst_emp = mysqli_fetch_assoc($cst)){
                $allowed_emp_arr[] = $cst_emp['gem_emp_id'];
            }
        }
    }
    return $allowed_emp_arr;
}

/**
 * @param string $start_day
 * @param string $end_day
 *
 * @return mixed[]
 */
function get_holiday_between_two_date($start_day,$end_day){
    $holiday_arr = array();
    $startDate  = strtotime($start_day);
    $endDate    = strtotime($end_day);
    for($temp = $startDate; $temp <= $endDate; $temp+=86400){
        if(date('N',$temp)=='7'){ //sunday
            $holiday_arr[] = date('Y-m-d',$temp);
        }else if( (date('N',$temp)=='6') && (in_array(ceil(date('d',$temp)/7),array(1,3,5))) ){ //saturday
            $holiday_arr[] = date('Y-m-d',$temp);
        }
    }
    $whr_condition = "";
    if(count($holiday_arr)>0 && is_array($holiday_arr)){
        $date_str = implode(",",$holiday_arr);
        $whr_condition = " and DAY(GHL_DATE) not in ($date_str) ";
    }
    $holiday_res = execute_my_query("select (GHL_DATE) as holiday from gft_holiday_list where GHL_DATE >= '$start_day' and GHL_DATE <= '$end_day' $whr_condition ");
    while($holiday_data = mysqli_fetch_array($holiday_res)){
        $holiday_arr[] = $holiday_data['holiday'];
    }
    return $holiday_arr;
}

/**
 * @param string $emply_id
 *
 * @return boolean
 */
function is_team_lead($emply_id){
    $que1 = " select GER_EMP_ID from gft_emp_reporting join gft_emp_master on (GEM_EMP_ID=GER_EMP_ID and GEM_STATUS='A') ".
        " where GER_STATUS='A' and GER_REPORTING_EMPID='$emply_id' ";
    $res1 = execute_my_query($que1);
    if(mysqli_num_rows($res1) > 0){
        return true;
    }
    return false;
}

/**
 * @param string $learning_month
 * @param string $learning_year
 * @return string[string]
 */
function get_kpi_date_range($learning_month='',$learning_year=''){
    $month =  ($learning_month!='' ? $learning_month : date('m')) ;
    $year  =  ($learning_year!=''  ? $learning_year  : date('Y')) ;
    $date_range = get_two_dimensinal_array_from_query("select GKD_FROM_DATE,GKD_TO_DATE from gft_kpi_date_range where  GKD_MONTH=$month and GKD_YEAR=$year ","GKD_FROM_DATE","GKD_TO_DATE");
    return $date_range;
}
/**
 * @param string $first_day_this_month
 *
 * @return string
 */
function get_kpi_enable_end_date($first_day_this_month){
    $last_day_this_month = date("Y-m-t",strtotime($first_day_this_month));
    $holiday_arr = get_holiday_between_two_date($first_day_this_month,$last_day_this_month);
    $ldt = strtotime($last_day_this_month);
    $fdt = strtotime($first_day_this_month);
    $buffer_day = (int)get_samee_const("KPI_BUFFER_DAY");
    $buffer_seconds = strtotime("$buffer_day day",0);
    for($time=$ldt;$time>=$fdt;$time-=86400){
        if(!in_array(date("Y-m-d",$time), $holiday_arr)){
            return (date("Y-m-d",($time-$buffer_seconds)));
        }
    }
    return "";
}

/**
 * @param string $emp_id
 * 
 * @return void
 */
function sync_employees_to_integration_portal($emp_id){
    $query = " select GLM_LOGIN_NAME,GEM_MOBILE,GEM_STATUS,GEM_EMP_ID ".
             " from gft_login_master join gft_emp_master on (gem_emp_id=GLM_EMP_ID) ".
             " where gem_emp_id in ($emp_id) ";
    $res = execute_my_query($query);
    $loc_cust_id =  LOCATION_TRACKER_ID;
    while($row=mysqli_fetch_assoc($res)){
        $sales_man_arr = array(
            "userName"      => $row["GLM_LOGIN_NAME"],
            "mobileNumber"  => $row["GEM_MOBILE"],
            "userId"        => $row["GEM_EMP_ID"],
            "status"        => ($row["GEM_STATUS"]=="A") ? "Active" : "Inactive",
            "expriyDate"    => "2025-03-01",
        );
        $post_data = array(
            "corporateCustomerId"=>$loc_cust_id,
            "outletCustomerId"=>$loc_cust_id,
            "skewCode"=>"702",
            "salesMan"=>$sales_man_arr
        );
        $config = get_connectplus_config();
        $post_url = $config['integ_portal']."/location-tracker/create-salesman";
        $location_tracker = get_single_value_from_single_table("gem_location_tracker","gft_emp_master","gem_emp_id",$row["GEM_EMP_ID"]);
        if($location_tracker){
            do_curl_to_alert($loc_cust_id, $post_url, "POST", json_encode($post_data), array("Content-Type: application/json"));
        }
    }
}

/**
 * @param string $rid
 *
 * @return void
 */
function create_welcome_ticket($rid){
	global $uid;
	$order_no_qry = "select GCR_ORDER_NO from gft_collection_receipt_dtl where GCR_RECEIPT_ID='$rid'";
	$order_no_res = execute_my_query($order_no_qry);
	while($ord_row = mysqli_fetch_assoc($order_no_res)) {
		$order_no = $ord_row['GCR_ORDER_NO'];
		$outstanding_amt = get_single_value_from_single_table("god_balance_amt","gft_order_hdr","god_order_no",$order_no);
		if($outstanding_amt<=0) {
			$order_product_qry = " select god_lead_code,glh_main_product,gpm_map_id,gpg_product_family_code,gpg_skew,glh_country from gft_order_hdr ".
					" join gft_order_product_dtl on (god_order_no=gop_order_no) ".
					" join gft_product_master pm on (gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew and pm.gft_skew_property=1) ".
					" join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code and pfm.gpm_is_base_product='Y') ".
					" join gft_lead_hdr on (god_lead_code=glh_lead_code) ".
					" join gft_product_group_master on (gpg_support_group_id=glh_main_product and gpg_product_family_code=gop_product_code and gpg_skew=substr(gop_product_skew,1,4)) ".
					" left join gft_political_map_master on (gpm_map_name=glh_cust_statecode and gpm_map_type='S') ".
					" where god_order_no='$order_no' and god_order_splict=0 and gop_sell_amt>0 ";
			$order_product_res = execute_my_query($order_product_qry);
			if(mysqli_num_rows($order_product_res)>0) {
				while($prod_row = mysqli_fetch_assoc($order_product_res)) {
					$cust_code = $prod_row['god_lead_code'];
					$prod_gp = $prod_row['glh_main_product'];
					$state_id = $prod_row['gpm_map_id'];
					$pcode = $prod_row['gpg_product_family_code'];
					$pskew = $prod_row['gpg_skew'];
					$country = $prod_row['glh_country'];
 					if(check_is_first_pd_handover($cust_code,false)) {
						$assign_emp = get_cst_agent_for_customer($state_id,$prod_gp);
						assign_welcome_call($cust_code,$pcode,$pskew,$uid,$assign_emp);
					}
				}
			} else {
				if(is_kit_based_order($order_no)) {
					$pd_id = get_single_value_from_single_table("gpd_id", "gft_product_delivery_hdr", "gpd_order_no", $order_no);
					$golive_query = execute_my_query(get_golive_customer_details_query($pd_id));
					$ordered_state_id = ''; $ordered_cust_id = '';
					$order_dtl_qry = " select god_lead_code,gpm_map_id from gft_order_hdr ".
							" join gft_order_product_dtl on (gop_order_no=god_order_no and gop_product_code=308 and gop_sell_amt>0) ".
							" join gft_lead_hdr on (god_lead_code=glh_lead_code) ".
							" left join gft_political_map_master on (gpm_map_name=glh_cust_statecode and gpm_map_type='S') ".
							" where god_order_no='$order_no' ";
					$order_dtl_res = execute_my_query($order_dtl_qry);
					if($ord_row = mysqli_fetch_assoc($order_dtl_res)) {
						$ordered_cust_id = $ord_row['god_lead_code'];
						$ordered_state_id = $ord_row['gpm_map_id'];
					}
					while($gcm_row = mysqli_fetch_assoc($golive_query)) {
						$outlet_cust_code = $gcm_row['GOL_CUST_ID'];
						$state_id = $gcm_row['gpm_map_id'];
						$pcode = $gcm_row['gid_lic_pcode'];
						$pskew = substr($gcm_row['gid_lic_pskew'],0,4);
						if(check_is_first_pd_handover($outlet_cust_code,false)) {
							$assign_emp = get_cst_agent_for_customer($state_id,'6');
							assign_welcome_call($outlet_cust_code,$pcode,$pskew,$uid,$assign_emp);
						}
					}
					$welcome_ticket_count = check_for_cst_welcome_ticket($ordered_cust_id);
					if(is_pd_signed_off($ordered_cust_id, $order_no) && $welcome_ticket_count==0) {
    					$assign_emp = get_cst_agent_for_customer($ordered_state_id,'6');
    					assign_welcome_call($ordered_cust_id,'300','03.0',$uid,$assign_emp);
					}
				} else {
					$order_product_qry = " select god_lead_code,gco_cust_code,cplh.glh_main_product split_prog_gp,gpm1.gpm_map_id split_stete_id, ".
							" pgm1.gpg_product_family_code split_pcode,pgm1.gpg_skew split_pskew, ".
							" olh.glh_main_product ordered_prog_gp,gpm2.gpm_map_id ordered_stete_id, ".
							" pgm2.gpg_product_family_code ordered_pcode,pgm2.gpg_skew ordered_pskew, ".
							" cplh.glh_country split_country,olh.glh_country ordered_country,olh.glh_lead_type ordered_lead_type ".
							" from gft_order_hdr join gft_order_product_dtl on (gop_order_no=god_order_no and gop_sell_amt>0) ".
							" join gft_cp_order_dtl on (gop_order_no=gco_order_no and gop_product_code=gco_product_code and gop_product_skew=gco_skew) ".
							" join gft_product_master pm on (gco_product_code=pm.gpm_product_code and gco_skew=pm.gpm_product_skew and pm.gft_skew_property=1) ".
							" join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code and pfm.gpm_is_base_product='Y') ".
							" join gft_lead_hdr cplh on (gco_cust_code=cplh.glh_lead_code) ".
							" join gft_lead_hdr olh on (god_lead_code=olh.glh_lead_code) ".
							" join gft_product_group_master pgm1 on (pgm1.gpg_support_group_id=cplh.glh_main_product and pgm1.gpg_product_family_code=gco_product_code and pgm1.gpg_skew=substr(gco_skew,1,4)) ".
							" left join gft_political_map_master gpm1 on (gpm1.gpm_map_name=cplh.glh_cust_statecode and gpm1.gpm_map_type='S') ".
							" join gft_product_group_master pgm2 on (pgm2.gpg_support_group_id=cplh.glh_main_product and pgm2.gpg_product_family_code=gco_product_code and pgm2.gpg_skew=substr(gco_skew,1,4)) ".
							" left join gft_political_map_master gpm2 on (gpm2.gpm_map_name=cplh.glh_cust_statecode and gpm2.gpm_map_type='S') ".
							" where god_order_no='$order_no' and god_order_splict=1 and (god_emp_id<7000 or god_emp_id in (9999,9998)) ".
							" group by gco_cust_code ";
					$order_product_res = execute_my_query($order_product_qry);
					$ordered_cust = '';
					while($split_row = mysqli_fetch_assoc($order_product_res)) {
						$ordered_cust = $split_row['god_lead_code'];
						$split_cust = $split_row['gco_cust_code'];
						$split_state_id = $split_row['split_stete_id'];
						$split_prod_gp = $split_row['split_prog_gp'];
						$split_pcode = $split_row['split_pcode'];
						$split_pskew = $split_row['split_pskew'];
						$split_country = $split_row['split_country'];
						if($ordered_cust!=$split_cust) {
							$ordered_main_prod = $split_row['ordered_prog_gp'];
							$ordered_pcode = $split_row['ordered_pcode'];
							$ordered_pskew = $split_row['ordered_pskew'];
							$ordered_state_id = $split_row['ordered_stete_id'];
							$ordered_country = $split_row['ordered_country'];
							if($split_row['ordered_lead_type']=='3' || ($split_row['ordered_lead_type']=='1' and check_is_first_pd_handover($ordered_cust,false))) {
								$assign_emp = get_cst_agent_for_customer($ordered_state_id,$ordered_main_prod);
								assign_welcome_call($ordered_cust,$ordered_pcode,$ordered_pskew,$uid,$assign_emp);
							}
						}
                        if(check_is_first_pd_handover($split_cust,false)) {
							$client_assign_emp = get_cst_agent_for_customer($split_state_id,$split_prod_gp);
							assign_welcome_call($split_cust,$split_pcode,$split_pskew,$uid,$client_assign_emp);
						}
					}
				}
			}
		}
	}
}

/**
 * @param int $lead_code
 * @param int $pcode
 * @param string $pskew
 * @param int $lead_type
 *
 * @return int
 */
function get_customer_client_count($lead_code,$pcode,$pskew,$lead_type=1){
    $pcode_arr = array("200","500");
    $pskew_arr = array("07.0","06.0","06.5");
    $license_code = substr($pskew,4,5);
    $client = 0;
    if((in_array($pcode,$pcode_arr) && in_array(substr($pskew,0,4), $pskew_arr)) && $license_code=='UL'){
        $op_que =  " select sum(GOP_QTY) as client ".
            " from  gft_lead_hdr ".
            " join gft_order_hdr on (GOD_LEAD_CODE=glh_lead_code and GOD_ORDER_STATUS='A'  and GOD_ORDER_SPLICT=0) ".
            " join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) ".
            " where glh_lead_code=$lead_code and GLH_LEAD_TYPE=$lead_type and GOP_PRODUCT_CODE=$pcode and GOP_PRODUCT_SKEW='$pskew' ".
            " group by GOP_PRODUCT_code,GOP_PRODUCT_SKEW  ";
        
        $cp_que =  " select sum(GCO_CUST_QTY) as client ".
            " from  gft_lead_hdr ".
            " join gft_cp_order_dtl on (glh_lead_code=GCO_CUST_CODE) ".
            " join gft_order_hdr on (GOD_ORDER_NO=GCO_ORDER_NO and GOD_ORDER_STATUS='A'  and GOD_ORDER_SPLICT=1) ".
            " where GCO_CUST_CODE=$lead_code and GLH_LEAD_TYPE=$lead_type and GCO_PRODUCT_CODE=$pcode and GCO_SKEW='$pskew' ".
            " group by GCO_PRODUCT_CODE,GCO_SKEW ";
        
        $que = " select sum(client) as tot_client ".
            " from ($op_que union all $cp_que)  tt  ";
        
        $client  = (int)get_single_value_from_single_query('tot_client',$que);
    }
    return $client;
}

/**
 * @param string $cust_code 
 * @return string
 */
function get_num_of_outlets_query($cust_code=""){
    $whr = "";
    if($cust_code!=""){
        $whr = " and GHO_CUSTOMER_ID=$cust_code ";
    }
    $output =  " select count(GHO_CUSTOMER_ID) as outlet,GHO_CUSTOMER_ID from gft_hq_outlet_master ".
               " join gft_outlet_lead_code_mapping on (GHO_INSTALL_ID=GOL_INSTALL_ID and GOL_OUTLET_ID=GHO_OUTLET_ID) ". 
               " where 1 $whr group by GHO_CUSTOMER_ID ";
    if($cust_code!=""){
        $res = execute_my_query($output);
        if(mysqli_num_rows($res)>0){
            if($row=mysqli_fetch_assoc($res)){
                $output = $row["outlet"];
            }
        }else{
            $output = "0";
        }
        
    }
    return $output;
}

/**
 * @param string $cust_code
 * @return string
 */
function get_vertical_name_of_customer($cust_code){
  $que =  " select GTM_VERTICAL_NAME from gft_lead_hdr ".
    " join gft_vertical_master on (GLH_VERTICAL_CODE=GTM_VERTICAL_CODE) ".
    " where glh_lead_code='$cust_code' ";
  $res = execute_my_query($que);
  $vertical_name = "";
  if($row=mysqli_fetch_assoc($res)){
      $vertical_name = $row["GTM_VERTICAL_NAME"];
  }
  return $vertical_name;
}

/**
 * @param int $hrs
 *
 * @return string
 */
function get_hours_mins_with_string($hrs=0){
    $duration = '';
    if($hrs!=0){
        $hours = floor($hrs);
        $m = $hrs - $hours;
        $minutes = round($m*60);
        //$duration = ($hours > 0) ? $hours." hour".(($hours>1) ? "s":"") : " $minutes"." min".(($minutes>1) ? "s":"");
        if($hours>0){
            $duration .= $hours." hour";
            if($hours>1){
                $duration .= "s";
            }
        }
        if($minutes>0){
            $duration .= " $minutes"." min";
            if($minutes>1){
                $duration .= "s";
            }
        }
    }
    return $duration;
}

/**
 * @param int $install_id
 * @param string $order_no
 * @return int
 */
function get_order_by_emp($install_id="",$order_no=""){
    $where = "";
    if($install_id!=""){
        $where = " and gid_install_id=$install_id ";
    }
    if($order_no!=""){
        $where = " and god_order_no=$order_no ";
    }
     
   $que = " select god_emp_id from gft_order_hdr ".
    " join gft_install_dtl_new on (gid_order_no=god_order_no) ".
    " where 1 $where ";
   return (int)get_single_value_from_single_query("god_emp_id", $que);
}
/**
 * @param int $GLH_LEAD_SOURCECODE
 * @param int $GLH_REFERENCE_GIVEN
 * @return string
 */
function get_lead_source_reference_name($GLH_LEAD_SOURCECODE,$GLH_REFERENCE_GIVEN){
    $lead_source_c_name = "";
    if ($GLH_LEAD_SOURCECODE == '7' or $GLH_LEAD_SOURCECODE == '36') {
        $lead_source_c_name = get_name_of_customer($GLH_REFERENCE_GIVEN);
    }else if ($GLH_LEAD_SOURCECODE == '19') {
        $query1 = "select GEM_EVENT_ID,GEM_EVENT_NAME from gft_event_master " .
            " where GEM_EVENT_NATURE=2 AND GEM_EVENT_ID='$GLH_REFERENCE_GIVEN' ";
        $result1 = execute_my_query($query1);
        if($qdata1 = mysqli_fetch_array($result1)){
            $lead_source_c_name = $qdata1['GEM_EVENT_NAME'];
        }
    }else if ($GLH_LEAD_SOURCECODE == '38') {//social media
        $query1 = "select GEM_EVENT_ID,GEM_EVENT_NAME from gft_event_master " .
            " where GEM_EVENT_NATURE=10 AND GEM_EVENT_ID='$GLH_REFERENCE_GIVEN' ";
        $result1 = execute_my_query($query1);
        if($qdata1 = mysqli_fetch_array($result1)){
            $lead_source_c_name = $qdata1['GEM_EVENT_NAME'];
        }
    }elseif ($GLH_LEAD_SOURCECODE == '4' or $GLH_LEAD_SOURCECODE == '3') { //assoc
        $query1 = "select GEM_EVENT_ID,GEM_EVENT_NAME from gft_event_master " .
            " where GEM_EVENT_NATURE=$GLH_LEAD_SOURCECODE AND GEM_EVENT_ID='$GLH_REFERENCE_GIVEN' ";
        $result1 = execute_my_query($query1);
        if($qdata1 = mysqli_fetch_array($result1)){
            $lead_source_c_name = $qdata1['GEM_EVENT_NAME'];
        }
    }
    return $lead_source_c_name;
}


?>
