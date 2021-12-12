<?php
/*. forward boolean function is_mydelight_app_active(string $employee_id); .*/
/*. forward boolean function is_next_action_exits(string $lead_code,string $employee_id,string $next_action,string $action_date,boolean $check_status=); .*/
/*. forward void function assign_palr_support_tickets(string $lead_code, string $product_code, string $product_skew, string $start_date); .*/
require_once(__DIR__ ."/common_util.php");
require_once(__DIR__ ."/connectplus/connectplus_util.php");
require_once(__DIR__ ."/lic_util.php");

/**
 * @param string $lead_code
 * @param string[int][int] $table_header_arr
 * @param boolean $return_query
 * @param string $limit
 * 
 * @return string
 */
function show_visit_history_sales($lead_code,$table_header_arr=null,$return_query=false,$limit=''){
	$query="select GLD_VISIT_DATE,concat(em.gem_emp_name,if(lh.glh_cust_name is not null,concat(' [',lh.glh_cust_name,']'),''),".
		"'<br>',ifnull(group_concat(distinct(jem.gem_emp_name)),''),if(lh1.glh_cust_name is not null,concat(' [',lh1.glh_cust_name,']'),'')) as visited_by,". 
		" vn.GAM_ACTIVITY_DESC activity_nature, gcn.GCM_NATURE as visit_nature," .
 		" if(GLD_NEXT_ACTION_DATE='0000-00-00','',concat(GLD_NEXT_ACTION_DATE,' ',ifnull(GLD_NEXT_ACTION_TIME,''))) as next_action_date,nvn.GAM_ACTIVITY_DESC as next_action," .
 		" GLD_NEXT_ACTION_DETAIL as next_action_detail," .
 		" concat(ifnull(mc.gmc_name,''),if(mc.gmc_name is null,'','. '),ifnull(GLD_NOTE_ON_ACTIVITY,'')) as my_comments ,".
 		" concat(ifnull(cm.gcm_feedback_desc,''),if(cm.gcm_feedback_desc is null,'','. '),ifnull(GLD_CUST_FEEDBACK,'')) as cust_feedback, GLD_SMS_DELIVERY_STATUS, 
 		 GLD_APPORX_TIMETOCLOSE,na.GEM_EMP_NAME next_action_owner, " .
 		" group_concat(jact_m.gam_activity_desc) as joint_act,gcs_name," .
 		" gld_license_value,gld_service_value,gld_date,
 		 concat(ifnull(GRL_NAME,''),' ',ifnull(GLD_REASON_FOR_STATUS_CHANGE_DTL,'')) reason," .
 		 "GPS_STATUS_NAME,concat(ifnull(GRC_NAME,''),ifnull(GLD_REASON_FOR_PROSPECT_STATUS_CHANGE_DTL,'')) as Reason_of_PSStatus_change ,".
 		" GLD_VISIT_IN_MIN,GZC_CHAT_ID,GCG_ID, if(GZC_CHAT_ID!='' and GCG_ID!='',GZC_CHAT_ID,'') GZC_CHAT_ID " .
 		" from gft_activity a join gft_emp_master em on (gld_activity_by=em.gem_emp_id) " .
 		" left join gft_leadcode_emp_map gle on(gle.GLEM_EMP_ID=em.gem_emp_id and em.GEM_ROLE_ID!=21) ".
		" left join gft_lead_hdr lh on(gle.GLEM_LEADCODE=lh.glh_lead_code)".
 		" left join gft_customer_status_master cs on (GCS_CODE=gld_lead_status) ".
	    "left join gft_prospects_status_master ps on (GPS_STATUS_ID=GLD_PROSPECTS_STATUS) ".
 		" left join gft_reason_for_change_lstatus rcs on (GRL_ID=GLD_REASON_FOR_STATUS_CHANGE) ".
 		" left join gft_reason_for_change_in_prospect_status rps on (GRC_ID=GLD_REASON_FOR_PROSPECT_STATUS_CHANGE) ".
 		" left join gft_customer_feedback_master cm on cm.gcm_feedback_code=a.gld_cust_feedback_code " .
 		" left join gft_complaint_nature_master gcn on(gcn.GCM_NATURE_ID=GLD_ACTIVITY_NATURE) ".
 		" left join gft_my_comments_master mc on mc.GMC_CODE=a.GLD_MY_COMMENTS_CODE " .
 		" left join gft_joint_visit_dtl jv on (jv.GJV_ACTIVITY_ID=a.gld_activity_id " .
 		" and jv.GJV_EMP_ID=a.gld_emp_id and jv.GJV_VISIT_DATE=a.gld_visit_date) " .
 		" left join gft_joint_activity jact on (jact.GJA_ACTIVITY_ID=a.gld_activity_id " .
 		" and jact.GJA_EMP_ID=a.gld_emp_id and  jact.GJA_LEAD_CODE=a.gld_lead_code and jact.GJA_VISIT_DATE=a.gld_visit_date)" .
 		" left join gft_activity_master jact_m on (jact_m.gam_activity_id=jact.GJA_VISIT_NATURE) " .
 		" left join gft_emp_master jem on (jem.gem_emp_id=jv.GJV_JOINT_EMP_ID) " .
 		" left join gft_leadcode_emp_map gle1 on(gle1.GLEM_EMP_ID=jem.gem_emp_id and jem.GEM_ROLE_ID!=21) ".
		" left join gft_lead_hdr lh1 on(gle1.GLEM_LEADCODE=lh1.glh_lead_code)".
 		" left join gft_activity_master vn on (vn.GAM_ACTIVITY_ID=GLD_VISIT_NATURE) " .
 		" left join gft_activity_master nvn on(nvn.GAM_ACTIVITY_ID=GLD_NEXT_ACTION) " .
		" left join gft_zoho_chat_hdr gc on(gc.GZC_SUPPORT_ID=a.gld_activity_id)".
		" left join gft_chat_group_master cg on(cg.GCG_ID=gc.GZC_AGENT_GROUP_ID and GCG_ACTIVITY_TYPE=2)".
		" left join gft_emp_master na on (na.GEM_EMP_ID=GLD_EMP_ID and GLD_NEXT_ACTION > 0) ".
 		" where gld_lead_code='$lead_code' " . 
		" group by a.gld_activity_id,a.gld_emp_id ".($limit!=""?" ORDER BY GLD_VISIT_DATE desc, gld_date limit $limit":"");
		if($return_query){
			return $query;
		}
		$myarr=array("S.No.","Visit Date","Visited By","Activity Type","Visit Nature","TIME SPENT (mins)","Next action owner","Next Action Date",
				"Next Action","Next Action detail","My Comments","Customer Feedback","SMS Delivery Status",
				"Expected Date of Closure","Lead Status","Reason for Lead Status Change","Prospect Status","Reason for Prospect Status Change","Reported Date");
 		$mysort=array("","GLD_VISIT_DATE","visited_by","activity_nature","visit_nature","GLD_VISIT_IN_MIN","next_action_owner","next_action_date","next_action",
							"next_action_detail","my_comments","cust_feedback","GLD_SMS_DELIVERY_STATUS","GLD_APPORX_TIMETOCLOSE","gcs_name","reason","GPS_STATUS_NAME",
							"Reason_of_PSStatus_change","gld_date");
 		$value_arr_total=array("N","N","N","N","N","Y","N","N");
		$value_arr_align=array("Right","Center","left","left","left","left","left","left","left");
		$sorttype="2";
		generate_reports(null,$query,$myarr,$mysort,null,null,null,$value_arr_align,null,null,null,null,null,null,null,
				null,null,null,null,$scorallable_tbody=false,$navigation=false,$order_by="GLD_VISIT_DATE desc, gld_date desc, gld_activity_id",$heading=true,
				$value_arr_total,true,0,null,null,null,null,0,$table_header_arr);
		return '';
			 
}
/**
 * @param string $lead_code
 *
 * @return void
 */
function show_assigned_followup($lead_code){
	$query	=	" select  GCF_ASSIGNED_DATE,em.gem_emp_name as 'assign_by',em2.gem_emp_name as 'assign_to',gam_activity_desc,". 
				" gcf_followup_detail ,gcf_followup_date,GCF_FOLLOWUP_TIME,GSS_STATUS_NAME as gcf_followup_status from gft_cplead_followup_dtl ".
				" INNER JOIN gft_lead_hdr lh on(GCF_LEAD_CODE=glh_lead_code) ".
				" left join gft_schedule_status_master on(gcf_followup_status=GSS_STATUS_ID) ". 
				" join gft_emp_master em on (gcf_assign_by=em.gem_emp_id) ".
				" join gft_emp_master em2 on (gcf_assign_to=em2.gem_emp_id) ". 
				" join gft_activity_master on(gcf_followup_action=gam_activity_id) ". 
				" where (1) and GCF_LEAD_CODE=$lead_code ";
	$myarr=array("S.No.","Assigned Date","Assigned By","Assigned To","Action","Description","Followup Date","Followup Time","Status");
	$mysort=array("","GCF_ASSIGNED_DATE","assign_by","assign_to","gam_activity_desc","gcf_followup_detail","gcf_followup_date","GCF_FOLLOWUP_TIME","gcf_followup_status");
	$value_arr_align=array("Right","left","left","left","left","left","left","left","left");
	$myarr_width=array("","75","100","100","75","","75","50","100");
	$sorttype="2";
	generate_reports($table_header="Assigned Followup",$query,$myarr,$mysort,$sms_category=null,$email_category=null,
	$previous_months_link=null,$value_arr_align,$myarr_width,$noheader=null,null,
	$myarr_sub=null,null,null,$myarr_sub1=null,$rowspan_arr1=null,$colspan_arr1=null,$report_link=null,
	$show_in_popup=null,$scorallable_tbody=false,$navigation=false,$order_by="gcf_followup_date",$heading=true,
	null,true);
}
/**
 * @param int $custCode
 * @param string $from_dt
 * @param string $to_dt
 * 
 * @return void
 */
function show_lead_field_details($custCode,$from_dt,$to_dt){
	$query="SELECT gem_emp_name, glf_from_date, glf_to_date,glf_status " .
			" FROM gft_lead_fexec_dtl join gft_emp_master on (glf_emp_id=gem_emp_id)" .
			" WHERE GLF_LEAD_CODE=$custCode " ;
	$myarr=array("S.No.","Emp Name","From Date","To date","Status");
 		$mysort=array("","gem_emp_name", "glf_from_date", "glf_to_date","glf_status");
		$value_arr_align=array("left","left","left","left");
		$sorttype="2";
		generate_reports($table_header="Visit History- Sales",$query,$myarr,$mysort,$sms_category=null,$email_category=null,
$previous_months_link=null,$value_arr_align,$myarr_width=null,$noheader=null,$sec_field_arr=null,
$myarr_sub=null,$rowspan=null,$colspan=null,$myarr_sub1=null,$rowspan_arr1=null,$colspan_arr1=null,$report_link=null,
$show_in_popup=null,$scorallable_tbody=false,$navigation=false,$order_by="glf_from_date",$heading=true,
$value_arr_total=null,true);	
}

/**
 * @param string $order_no
 * @param string $lead_code
 *
 * @return void
 */
function credit_reward_points($order_no,$lead_code){
	$check_res = execute_my_query("select GOD_ORDER_NO from gft_order_hdr where GOD_LEAD_CODE='$lead_code' and GOD_ORDER_AMT > 0 order by GOD_CREATED_DATE limit 1 ");
	if($chk_data = mysqli_fetch_array($check_res)){
		if($order_no != $chk_data['GOD_ORDER_NO']){
			return;  //not a first order
		}
	}
	global $uid;
	$sql_query = " select GOD_ORDER_NO,GOD_ORDER_STATUS, GOD_BALANCE_AMT, GRL_CUST_USERID, sum(GOP_SELL_RATE*GOP_QTY) as order_amt, ".
			" GLH_VERTICAL_CODE,GLH_CUST_STATECODE from gft_order_hdr ".
			" join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) ".
			" join gft_reference_lead_mapping on (GRL_REFERRED_LEAD_CODE=GOD_LEAD_CODE) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ".
			" where GOD_ORDER_NO='$order_no' group by GOD_ORDER_NO";
	$res_query = execute_my_query($sql_query);
	if($row1 = mysqli_fetch_array($res_query)){
		$order_amt 	= floatval($row1['order_amt']);
		$balance_amt = (int)$row1['GOD_BALANCE_AMT']; 
		if( ($row1['GOD_ORDER_STATUS']=='C') || ($balance_amt!=0) ){
			$order_amt = 0.0;
		}
		$cust_userid= $row1['GRL_CUST_USERID'];
		$vertical_id= $row1['GLH_VERTICAL_CODE'];
		$state_name = $row1['GLH_CUST_STATECODE'];
		$state_name	=	mysqli_real_escape_string_wrapper(trim($state_name));
		$data1 = mysqli_fetch_array(execute_my_query("select state_id from p_map_view where state='$state_name' group by state_id"));
		$state_code = $data1['state_id'];
		$reward_que=" select GRP_PROGRAM_ID, GRP_ORDER_VALUE, GRP_REWARD_POINTS from gft_reward_program ".
				" where GRP_STATUS='A' and (GRP_STATE_CODE='$state_code' or GRP_STATE_CODE=0) and (GRP_VERTICAL_CODE='$vertical_id' or GRP_VERTICAL_CODE=0) ".
				" order by GRP_STATE_CODE desc, GRP_VERTICAL_CODE desc, GRP_PROGRAM_PRIORITY asc limit 1";
		if($sql_data = mysqli_fetch_array(execute_my_query($reward_que))){
			$program_id	= $sql_data['GRP_PROGRAM_ID'];
			$order_val	= $sql_data['GRP_ORDER_VALUE'];
			$reward_pts = $sql_data['GRP_REWARD_POINTS'];
			$earned_pts = round( $order_amt * ($reward_pts/$order_val) );
				
			$update_arr['GRPD_CUST_USERID'] 		= $cust_userid;
			$update_arr['GRPD_PROGRAM_ID']			= $program_id;
			$update_arr['GRPD_EARNED_POINTS']		= "".$earned_pts;
			$update_arr['GRPD_ORDER_NO']			= $order_no;
			$update_arr['GRPD_REFERRED_LEAD_CODE']	= $lead_code;
			$update_arr['GRPD_CREATED_DATE']		= date('Y-m-d H:i:s');
			$key_arr['GRPD_ORDER_NO'] = $order_no;
			array_update_tables_common($update_arr, "gft_reward_points_dtl", $key_arr, null, $uid,null,null,$update_arr);
		}
	}
}

/**
 * @param string $lead_code
 * @param string $emp_id
 * 
 * @return boolean
 */
function is_dealer_enabled($lead_code, $emp_id=''){
	$select_query = " select CGI_LEAD_CODE from gft_cp_info where CGI_ENABLE_DEALER=1 ";
	if($lead_code!=''){
		$select_query .= "and CGI_LEAD_CODE='$lead_code' ";
	}else if($emp_id!=''){
		$select_query .= " and CGI_EMP_ID='$emp_id' ";
	}else {
		return false;
	}
	$query_result = execute_my_query($select_query);
	if(mysqli_num_rows($query_result) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $employee_id
 * @param string $lead_code
 * 
 * @return boolean
 */
function is_marketing_partner($employee_id='', $lead_code=''){
	if( ($employee_id=='') && ($lead_code=='') ){
		return false;
	}
	$select_query = " select CGI_LEAD_CODE from gft_cp_info where CGI_MARKETING_PARTNER=1 ";
	if($employee_id!=''){
		$select_query .= " and CGI_EMP_ID='$employee_id' ";
	}
	if($lead_code!=''){
		$select_query .= " and CGI_LEAD_CODE='$lead_code' ";
	}
	$query_result = execute_my_query($select_query);
	if(mysqli_num_rows($query_result) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $lead_code
 * 
 * @return string[int]
 */
function get_install_id_under_kit($lead_code){
	$ins_id_arr = /*. (string[int]) .*/array();
	$que1 = " select GSK_PRODUCT_CODE,GSK_PRODUCT_SKEW,GSK_PRODUCT_QTY from gft_order_product_dtl ".
			" join gft_order_hdr on (GOD_ORDER_NO=GOP_ORDER_NO) ".
			" join gft_product_master pm on (GOP_PRODUCT_CODE=pm.GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=pm.GPM_PRODUCT_SKEW) ".
			" join gft_skew_kit_master on (GSK_KIT_PCODE=GOP_PRODUCT_CODE and GSK_KIT_PSKEW=GOP_PRODUCT_SKEW) ".
			" join gft_product_master kt on (GSK_PRODUCT_CODE=kt.GPM_PRODUCT_CODE and GSK_PRODUCT_SKEW=kt.GPM_PRODUCT_SKEW) ".
			" where GOD_LEAD_CODE='$lead_code' and pm.GPM_ORDER_TYPE=2 and kt.GFT_SKEW_PROPERTY=1 and GSK_PRODUCT_CODE!=300 ";
	$res1 = execute_my_query($que1);
	$pcode_skew_arr = $skip_qty_arr = /*. (string[int]) .*/array();
	while($row1 = mysqli_fetch_array($res1)){
		$pcode_skew_arr[] 	= $row1['GSK_PRODUCT_CODE']."-".$row1['GSK_PRODUCT_SKEW'];
		$skip_qty_arr[] 	= $row1['GSK_PRODUCT_QTY'];
	}
	$que2 = " select GID_INSTALL_ID,GID_PRODUCT_CODE,GID_PRODUCT_SKEW from gft_lead_hdr lh ".
			" join gft_install_dtl_new on (GID_LEAD_CODE=lh.GLH_LEAD_CODE) where GID_STATUS!='U' ".
			" and (GLH_LEAD_CODE='$lead_code' or (glh_reference_given='$lead_code' and GLH_LEAD_SOURCECODE in (7,36) and GLH_LEAD_TYPE in (3,13))) ".
			" order by GID_INSTALL_ID ";
	$res2 = execute_my_query($que2);
	while($row2 = mysqli_fetch_array($res2)){
		$ins_pcode_skew = $row2['GID_PRODUCT_CODE']."-".$row2['GID_PRODUCT_SKEW'];
		$search_ind = array_search($ins_pcode_skew, $pcode_skew_arr);
		if($search_ind!==false){
			if($skip_qty_arr[$search_ind] > 0){
				$skip_qty_arr[$search_ind]--;
				$ins_id_arr[] = $row2['GID_INSTALL_ID'];
			}
		}
	}
	return $ins_id_arr;
}

/**
 * @param string $lead_code
 * @param int $lead_type
 * @param string $additional_wh_cond
 * @param boolean $kit_based
 * @param boolean $skip_trial_installation
 * 
 * @return string
 */
function get_install_detail_query($lead_code,$lead_type,$additional_wh_cond='',$kit_based=false, $skip_trial_installation=false){
	if($lead_type==3){
		$wh_cond = " and (GLH_LEAD_CODE='$lead_code' or (glh_reference_given='$lead_code' and GLH_LEAD_SOURCECODE in (7,36) and GLH_LEAD_TYPE in (3,13))) ";
	}else{
		$wh_cond = " and GLH_LEAD_CODE='$lead_code' ";
	}
	if($kit_based){
		$kit_install_id_arr = get_install_id_under_kit($lead_code);
		foreach ($kit_install_id_arr as $ins_id){
			$wh_cond .= " and GID_INSTALL_ID!='$ins_id' ";
		}
	}
	if($skip_trial_installation){
	    $wh_cond .= " and pm.GPM_FREE_EDITION='N' ";
	}
	$wh_cond .= " and pm.GPM_PRODUCT_TYPE!=8 and GPM_UAT_LICENSE!='Y' and GOP_ORDER_FOR!='UAT' and pm.gpm_product_code!=526 $additional_wh_cond "; //8 for custom license , 526 - instock since it is custom license model
	
	$install_dtl="select lh.GLH_LEAD_CODE,concat(GLH_CUST_NAME,'-',GLH_CUST_STREETADDR2) cust_name, GID_ORDER_NO,GID_HEAD_OF_FAMILY,GID_FULLFILLMENT_NO," .
			" GID_LIC_ORDER_NO,GID_LIC_PCODE,GID_LIC_PSKEW,GID_LIC_FULLFILLMENT_NO, GID_VALIDITY_DATE," .
			" if(GFT_SKEW_PROPERTY=1,'Perpetual','Subscription') as license_type ,GPM_LICENSE_TYPE," .
			" GID_INSTALL_DATE, GID_NO_CLIENTS,GID_NO_COMPANYS, GPM_PRODUCT_ABR, GPM_SKEW_DESC, gem_emp_name," .
			" GFT_SKEW_PROPERTY,GSPM_DISCOUNT_PERCENTAGE,GPM_PRODUCT_TYPE, CONCAT(pm.GPM_PRODUCT_CODE,'-',if(GFT_SKEW_PROPERTY=1,GID_LIC_PSKEW,pm.GPM_REFERER_SKEW)) REFERER_SKEW,GID_EXPIRE_FOR ,GET_TYPE_NAME,GID_INSTALL_ID " .
			" from gft_lead_hdr lh join gft_install_dtl_new on (GID_LEAD_CODE=lh.GLH_LEAD_CODE) ".
			" join gft_order_product_dtl on (GOP_ORDER_NO=GID_ORDER_NO and GOP_PRODUCT_CODE=GID_PRODUCT_CODE and GOP_PRODUCT_SKEW=GID_PRODUCT_SKEW) ".
			" join gft_expire_type_master on (GET_TYPE_ID=GID_EXPIRE_FOR) ".
			" join gft_order_hdr on (GID_ORDER_NO=GOD_ORDER_NO and god_order_status='A') " .
			" join gft_product_family_master pfm on(pfm.gpm_product_code=GID_LIC_PCODE  and GPM_IS_INTERNAL_PRODUCT in (0,2,3))" .
			" join gft_product_master pm on (GID_LIC_PCODE=pm.gpm_product_code and gid_lic_pskew=pm.gpm_product_skew)" .
			" join gft_emp_master on (GOD_EMP_ID=gem_emp_id)" .
			" join gft_skew_property_master on (GSPM_CODE = GFT_SKEW_PROPERTY) ".
			" where gid_status!='U'  $wh_cond " .
			" order by GID_VALIDITY_DATE,GLH_LEAD_CODE,if(GPM_LICENSE_TYPE=3,100,1) ";
	return $install_dtl;
}

/**
 * @param int $ref_install_id
 * @param int $ref_user_id
 * 
 * @return string[string][int][string]
 */
function get_outlet_group_dtl($ref_install_id,$ref_user_id){
	$outlet_arr = $group_arr = /*. (string[int][string]) .*/array();
	$sql1 = " select GHO_OUTLET_ID,GHO_OUTLET_NAME,GHM_GROUP_ID,GHG_GROUP_NAME from gft_hq_user_outlet_mapping ".
			" join gft_hq_outlet_master on (GHU_INSTALL_ID=GHO_INSTALL_ID and GHU_OUTLET_ID=GHO_OUTLET_ID) ".
			" join gft_hq_outlet_group_mapping on (GHU_INSTALL_ID=GHM_INSTALL_ID and GHU_OUTLET_ID=GHM_OUTLET_ID) ".
			" join gft_hq_outlet_group_master on (GHG_INSTALL_ID=GHU_INSTALL_ID and GHG_GROUP_ID=GHM_GROUP_ID) ".
			" where GHU_INSTALL_ID='$ref_install_id' and GHU_USER_ID='$ref_user_id' ";
	$res1 = execute_my_query($sql1);
	while($row1 = mysqli_fetch_array($res1)){
		$field_arr['id'] 		= $row1['GHO_OUTLET_ID'];
		$field_arr['name'] 		= $row1['GHO_OUTLET_NAME'];
		$field_arr['group_id'] 	= $row1['GHM_GROUP_ID'];
		$outlet_arr[] 			= $field_arr;
		
		$arr['id']	 = $row1['GHM_GROUP_ID'];
		$arr['name'] = $row1['GHG_GROUP_NAME'];
		$group_arr[] = $arr;
	}
	$ret_arr['outlet_group'] = $group_arr;
	$ret_arr['outlet'] = $outlet_arr;
	return $ret_arr;
}

/**
 * @param string $pcode
 * @param string $pgroup
 *
 * @return string
 */
function get_product_name_with_version($pcode,$pgroup){
	$product_name = '';
	$que2 = " select concat(GPM_PRODUCT_ABR,' ',gpg_version) as pname from gft_product_family_master ".
			" join gft_product_group_master on (gpg_product_family_code=GPM_HEAD_FAMILY) ".
			" where gpg_product_family_code='$pcode' and gpg_skew='$pgroup' ";
	$res2 = execute_my_query($que2);
	if($row2 = mysqli_fetch_array($res2)){
		$product_name = $row2['pname'];
	}
	return $product_name;
}

/**
 * @param string $mobile_no
 * @param string $email_id
 * @param string $product_code
 *
 * @return string[string][int][string]
 */
function get_live_url_details($mobile_no, $email_id, $product_code){
    global $global_dealer_pcodes_arr;
	$return_arr = /*. (string[string][int][string]) .*/array();
	$contact_no_cond = /*.(string).*/getContactDtlWhereCondition("GCC_CONTACT_NO", $mobile_no);
	$select_query = " select if(wn.GAU_APP_PCODE is not null,GPU_USER_NAME,'') as GPU_USER_NAME,base.GID_SERVER_IP,base.GID_SERVER_NAME,GLH_CUST_NAME,GLH_CUST_STREETADDR2,GFT_SKEW_PROPERTY,GPM_FREE_EDITION, ".
			" GPM_LICENSE_TYPE,base.GID_LIC_PCODE,base.GID_STORE_URL,base.GID_PORT_NUMBER,base.GID_INTERNET_PORT,base.GID_PORT_STATUS,base.GID_VALIDITY_DATE, app.GID_VALIDITY_DATE as app_validity, ".
			" GPU_INSTALL_ID,GPU_USER_ID,base.GID_LEAD_CODE,base.GID_LIC_PSKEW,tm.GID_VALIDITY_DATE as tm_validity,apptm.GAU_APP_PCODE as tm_code,GPU_PASSWORD, ".
			" base.GID_CURRENT_VERSION,CGI_SHORT_NAME ".
			" from gft_customer_contact_dtl ".
			" join gft_pos_users on (GPU_CONTACT_ID=GCC_ID and GPU_CONTACT_STATUS='A') ".
			" join gft_install_dtl_new base on (base.GID_INSTALL_ID=GPU_INSTALL_ID and base.GID_STATUS in ('A','S')) ".
			" join gft_product_master on (base.GID_LIC_PCODE=GPM_PRODUCT_CODE and base.GID_LIC_PSKEW=GPM_PRODUCT_SKEW) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=base.GID_LEAD_CODE) ".
			" left join gft_app_users wn on (wn.GAU_INSTALL_ID=GPU_INSTALL_ID and wn.GAU_CONTACT_ID=GPU_CONTACT_ID and wn.GAU_USER_STATUS=1 and wn.GAU_APP_PCODE='$product_code') ".
			" left join gft_app_users apptm on (apptm.GAU_INSTALL_ID=GPU_INSTALL_ID and apptm.GAU_CONTACT_ID=GPU_CONTACT_ID and apptm.GAU_USER_STATUS=1 and apptm.GAU_APP_PCODE=707) ".
			" left join gft_install_dtl_new app on (app.GID_LEAD_CODE=GLH_LEAD_CODE and app.GID_LIC_PCODE=wn.GAU_APP_PCODE and app.GID_STATUS!='U') ".
			" left join gft_install_dtl_new tm on (tm.GID_LEAD_CODE=GLH_LEAD_CODE and tm.GID_LIC_PCODE=707 and tm.GID_STATUS!='U') ".
			" left join gft_cp_info on (CGI_EMP_ID=base.GID_EMP_ID) ".
			" where base.GID_WEB_REPORTER_INSTALL_STATUS=1 and ($contact_no_cond) ". //removed email id condition
			" group by base.GID_INSTALL_ID,GPU_USER_NAME order by GPM_LICENSE_TYPE desc, base.GID_VALIDITY_DATE, GPU_USER_NAME ";
	$que_res = execute_my_query($select_query);
	$mode 		= 'demo';
	$toast_msg 	= "Welcome to Demo session. Your store is not provisioned to connect with APP. Please follow steps given in Switch to live to connect with your store.";
	$today_date = date('Y-m-d');
	$today_timestamp = strtotime($today_date);
	$live_customers = "";
	if(mysqli_num_rows($que_res) > 0){
		while($row1 = mysqli_fetch_array($que_res)){
			$val_arr = /*. (string[string]) .*/array();
			$gid_lead_code = $row1['GID_LEAD_CODE'];
			$GLH_CUST_NAME			= $row1['GLH_CUST_NAME'];
			$val_arr['shopName']	= $GLH_CUST_NAME;
			$val_arr['shopLocation']= $row1['GLH_CUST_STREETADDR2'];
			$val_arr['url']	 		= $row1['GID_STORE_URL'];
			$internet_port = (int)$row1['GID_INTERNET_PORT'];
			$intranet_port = (int)$row1['GID_PORT_NUMBER']; 
			if($internet_port > 0){
				$val_arr['url'] .= ":$internet_port";
			}else if($intranet_port > 0){
				$val_arr['url'] .= ":$intranet_port";
			}
			$val_arr['username'] 	= $row1['GPU_USER_NAME'];
			$val_arr['server_name'] = $row1['GID_SERVER_NAME'];
			$val_arr['system_ip']	= $row1['GID_SERVER_IP'];
			$val_arr['customer_id']	= $gid_lead_code;
			$tm_url = "";
			$tm_status = "EXPIRED";
			if($row1['GPU_PASSWORD']==''){  //if password is empty, that user is not synced to connectplus
				$tm_status = "DISABLED";
			}else if($row1['tm_validity']==""){
				$tm_status = "TRIAL_NOT_TRIED";
			}else if((int)$row1['tm_code']==0){
				$tm_status = "DISABLED";
			}else  if( (strtotime($row1['tm_validity']) >= $today_timestamp) ){
				$config_arr = get_connectplus_config();
				$cloud_url	= str_replace("{{customerId}}", $gid_lead_code, $config_arr['cloud_domain']);
				$tm_status 	= "ENABLED";
				$tm_url		= "$cloud_url/task_manager";
			}
		    $gid_lic_pcode			= $row1['GID_LIC_PCODE'];
		    $gid_lic_pgroup         = substr($row1['GID_LIC_PSKEW'], 0,4);
			$feature_arr= get_addon_feature_for_base_product($gid_lead_code,$gid_lic_pcode,$gid_lic_pgroup,$row1['GID_CURRENT_VERSION']);
			$peakaboo_view_url = get_peekaboo_auto_login_url($gid_lead_code,$mobile_no);
			$val_arr['taskmanager_status'] 	= $tm_status;
			$val_arr['taskmanager_url']		= $tm_url;
			$val_arr['is_peekaboo_enabled']	= ($peakaboo_view_url!='') ? true : false;
			$val_arr['peekaboo_url']		= $peakaboo_view_url;
			$val_arr['base_product']= get_product_name_with_version($gid_lic_pcode,$gid_lic_pgroup);
			$val_arr['features']    = $feature_arr;
			$val_arr['is_rebranded']    = in_array($gid_lic_pcode,$global_dealer_pcodes_arr)?true:false;
			$val_arr['rebranded_name']  = in_array($gid_lic_pcode,$global_dealer_pcodes_arr)?$row1['CGI_SHORT_NAME']:"";
			$validity_date 			= $row1['GID_VALIDITY_DATE'];
			$validity_str 			= date('M d,Y',strtotime($validity_date));
			$license_type 			= $row1['GPM_LICENSE_TYPE'];
			$app_validity			= $row1['app_validity'];
			$skew_property			= $row1['GFT_SKEW_PROPERTY'];
			$free_edition			= $row1['GPM_FREE_EDITION'];
			$ref_install_id			= $row1['GPU_INSTALL_ID'];
			$ref_user_id			= $row1['GPU_USER_ID'];
			$sass_mode = '';
			if($skew_property=='18'){ //plan for sass
				$sass_mode = ($free_edition=='Y')?'trial':'live';
			}
			$app_mapped = $row1['GPU_USER_NAME'];
			if( ($mode=='demo') && ($row1['GPU_USER_NAME']=='') ){
				$mode = 'demo';
				$toast_msg = "You are in demo version now, configure WhatsNow in server and connect to your live store";
			}else if( ($app_mapped!='') && (in_array($license_type,array('1','2')) || ($sass_mode=='live')) ){
				if(strtotime($validity_date) >= $today_timestamp){
					if( ($app_validity=='') || ($skew_property=='18') || ((int)strtotime($app_validity) >= $today_timestamp) ){
						$mode		= 'live';
						$live_customers .= ($live_customers=='')?$GLH_CUST_NAME:",$GLH_CUST_NAME";
						$toast_msg 	= "Congratulations you are now successfully connected to your store $live_customers";
						if($gid_lic_pcode=='300'){
							$outlet_dtl_arr = get_outlet_group_dtl($ref_install_id,$ref_user_id);
							$val_arr['is_hq'] 	= true;
							$val_arr['hq_outlets'] 	= $outlet_dtl_arr['outlet'];
							$val_arr['hq_outlet_groups'] = $outlet_dtl_arr['outlet_group'];
						}
						$return_arr['live_store_url'][] = $val_arr;
					}else if($mode!='live'){
						$mode 		= "demo";
						$toast_msg 	= "We regret to inform you that APP validity period is expired and you are no longer entitled to use the app, Please renew your license. Until then you will be on demo session only.";
					}
				}else if($mode!='live'){
					$mode 		= "demo";
					$toast_msg 	= "Cannot connect to your store. ASA is expired on $validity_str, please renew it. Till then you will be on demo session only";
				}
			}else if( ($app_mapped!='') && (($license_type=='3') || ($sass_mode=='trial')) && (strtotime($validity_date) >= $today_timestamp) ){
				$mode 		= 'trial';					
				$toast_msg 	= "Welcome to Trial session. App will expire on $validity_str. Please purchase the license for uninterrupted service";
				$return_arr['live_store_url'][] = $val_arr;
			}
		}
	}
	$return_arr['mode'] = $mode;
	$return_arr['toast_message'] = $toast_msg;
	return $return_arr;
}


/**
 * @param string $mobile_no
 * @param string $email_id
 * @param string $product_code
 *
 * @return string[string][int][string]
 */
function get_store_url_for_device_specific($mobile_no, $email_id, $product_code){
	$return_arr = /*. (string[string][int][string]) .*/array();
	$mode = 'demo';
	$toast_msg = 'Welcome to Demo Session. To experience with your own questions, select "Change Questions?" and signup for Trial';
	$contact_no_cond = getContactDtlWhereCondition("GCC_CONTACT_NO", $mobile_no);
	$select_query = " select base.GID_SERVER_IP,base.GID_SERVER_NAME,GLH_CUST_NAME,GLH_CUST_STREETADDR2, ".
			" pm.GPM_LICENSE_TYPE,base.GID_STORE_URL,base.GID_PORT_NUMBER,app.GID_VALIDITY_DATE as app_validity,GLH_LEAD_CODE, ".
			" app_pm.GPM_LICENSE_TYPE as app_lic_type from gft_customer_contact_dtl ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GCC_LEAD_CODE) ".
			" left join gft_install_dtl_new base on (GLH_LEAD_CODE=base.GID_LEAD_CODE and base.GID_STATUS in ('A','S')) ".
			" left join gft_product_master pm on (base.GID_LIC_PCODE=pm.GPM_PRODUCT_CODE and base.GID_LIC_PSKEW=pm.GPM_PRODUCT_SKEW) ".
			" left join gft_install_dtl_new app on (app.GID_LEAD_CODE=GLH_LEAD_CODE and app.GID_STATUS!='U' and app.GID_LIC_PCODE='$product_code') ".
			" left join gft_product_master app_pm on (app.GID_LIC_PCODE=app_pm.GPM_PRODUCT_CODE and app.GID_LIC_PSKEW=app_pm.GPM_PRODUCT_SKEW) ".
			" where (GCC_CONTACT_NO='$email_id' or $contact_no_cond) ".
			" group by base.GID_INSTALL_ID order by app.GID_INSTALL_ID ";
	$que_res = execute_my_query($select_query);
	$today_timestamp = strtotime(date('Y-m-d'));
	$app_validity = $cust_location = $gid_lead_code = "";
	$app_license_type = $live_customers = "";
	$app_arr = /*. (string[string]) .*/array();
	while($row1 = mysqli_fetch_array($que_res)){
		$GLH_CUST_NAME	= $row1['GLH_CUST_NAME'];
		$store_url		= $row1['GID_STORE_URL'];
		$cust_location	= $row1['GLH_CUST_STREETADDR2'];
		$gid_lead_code	= $row1['GLH_LEAD_CODE'];
		if( ($app_validity=='') && ($row1['app_validity']!='') ){
			$app_validity	= $row1['app_validity'];
			$app_license_type = $row1['app_lic_type'];
			$app_arr = /*. (string[string]) .*/array();
			$app_arr['shopName']	= $GLH_CUST_NAME;
			$app_arr['shopLocation']= $cust_location;
			$app_arr['customer_id']	= $gid_lead_code;
			$app_arr['username'] 	= $app_arr['server_name'] = "";
			$app_arr['system_ip']	= $app_arr['url'] = "";
			$return_arr['app_installed_cust_id'] = $gid_lead_code;
		}
		if($store_url!=''){
			$val_arr = /*. (string[string]) .*/array();
			$val_arr['shopName']	= $GLH_CUST_NAME;
			$val_arr['shopLocation']= $cust_location;
			$val_arr['customer_id']	= $gid_lead_code;
			$val_arr['url']	 		= $store_url;
			if((int)$row1['GID_PORT_NUMBER']!=0){
				$val_arr['url'] .= ":".$row1['GID_PORT_NUMBER'];
			}
			$val_arr['username'] 	= $mobile_no;
			$val_arr['server_name'] = $row1['GID_SERVER_NAME'];
			$val_arr['system_ip']	= $row1['GID_SERVER_IP'];
			$live_customers .= ($live_customers=='')?$GLH_CUST_NAME:",$GLH_CUST_NAME";
			$return_arr['live_store_url'][] = $val_arr;
		}
	}
	if($app_validity!=''){
		if(strtotime($app_validity) >= $today_timestamp){
			if($app_license_type=='3'){
				$mode		= 'trial';
				$validity_str = date('M d,Y',strtotime($app_validity));
				$toast_msg = "Welcome to Trial login. Your Trial expires on $validity_str , login to your myPulse web app and buy the license.";
			}else{
				$mode		= 'live';
				$toast_msg 	= "Congratulations you are now successfully connected to your store $live_customers";
			}
			if( !isset($return_arr['live_store_url']) ){
				$return_arr['live_store_url'][] = $app_arr;
			}
		}else{
			$mode 		= "demo";
			$toast_msg 	= "We regret to inform you that APP validity period is expired and you are no longer entitled to use the app, Please renew your license. Until then you will be on demo session only.";
		}
	}
	$return_arr['mode'] = $mode;
	$return_arr['toast_message'] = $toast_msg;
	return $return_arr;
}

/**
 * @param int $customer_id
 * 
 * @return string[int][string]
 */
function get_mypulse_demo_url($customer_id){
	$sql1 = "select GLH_CUST_NAME,GLH_CUST_STREETADDR2 from gft_lead_hdr where GLH_LEAD_CODE ='$customer_id' ";
	$res1 = execute_my_query($sql1);
	$return_arr = /*. (string[int][string]) .*/array();
	if($row1 = mysqli_fetch_array($res1)){
		$val_arr['customer_id']	= "$customer_id";
		$val_arr['shopName']	= $row1['GLH_CUST_NAME'];
		$val_arr['shopLocation']= $row1['GLH_CUST_STREETADDR2'];
		$val_arr['username'] 	= $val_arr['server_name'] = "";
		$val_arr['system_ip']	= $val_arr['url'] = "";
		$return_arr[] = $val_arr;
	}
	return $return_arr;
}

/**
 * @param string $customer_id
 * @param string[string] $update_arr
 * @param string $gcc_id
 *
 * @return void
 */
function save_contact_detail_audit_log($customer_id,$update_arr,$gcc_id){
	global $me;
	$audit_columns = $new_values = $old_values = '';
	foreach ($update_arr as $key => $val){
		$audit_columns 	.= ($audit_columns=='')?"$key":",$key";
		$new_values 	.= ($new_values=='')?"$val":",$val";
	}
	$old_val_res = execute_my_query(" select $audit_columns from gft_customer_contact_dtl where GCC_ID='$gcc_id' ");
	if($row1 = /*. (string[string]) .*/mysqli_fetch_array($old_val_res)){
		for($i=0;$i<count($row1)/2;$i++){
			$old_values 	.= ($old_values=='')?"$row1[$i]":",$row1[$i]";
		}
	}else{
		$old_values = 'Newly Saved';
	}
	$audit_columns 	= mysqli_real_escape_string_wrapper($audit_columns);
	$old_values 	= mysqli_real_escape_string_wrapper($old_values);
	$new_values 	= mysqli_real_escape_string_wrapper($new_values);
	if($old_values!=$new_values){
		$audit_query  = " insert into gft_audit_log_edit_table (GUL_AUDIT_TABLE,GUL_AUDIT_LEAD_CODE,GUL_AUDIT_COLUMNS,GUL_AUDIT_OLD_VALUES,".
				" GUL_AUDIT_NEW_VALUES,GUL_PAGE,GUL_TIME,GUL_USER_ID) values ('gft_customer_contact_dtl','$customer_id','$audit_columns',".
				" '$old_values','$new_values','$me',now(),9999) ";
		execute_my_query($audit_query);
	}
}


/**
 * @param int $mobile_gcc_id
 * @param int $contact_type
 * @param string $install_id
 * @param string $ref_emp_id
 * @param string $user_name
 * @param string $user_status
 * @param string $user_gf_role
 * @param string $user_pos_role
 * @param boolean $from_app_users
 * @param string $pos_role_id
 * @param string $user_password
 * @param int $system_access
 * 
 * @return void
 */
function update_pos_user($mobile_gcc_id,$contact_type,$install_id,$ref_emp_id,$user_name,$user_status,$user_gf_role,$user_pos_role,$from_app_users=false,$pos_role_id='',$user_password='',$system_access=0){
	$upd_arr['GPU_CONTACT_ID'] 		= "$mobile_gcc_id";
	$upd_arr['GPU_CONTACT_TYPE']	= "$contact_type";
	$upd_arr['GPU_INSTALL_ID'] 		= $install_id;
	$upd_arr['GPU_USER_ID'] 		= $ref_emp_id;
	$upd_arr['GPU_CONTACT_STATUS'] 	= $user_status;
	$upd_arr['GPU_UPDATED_DATE'] 	= date('Y-m-d H:i:s');
	if($from_app_users){
		$upd_arr['GPU_USER_NAME'] 		= $user_name;
	}
	if($user_password!=""){
		$upd_arr['GPU_PASSWORD'] 		= $user_password;
		$upd_arr['GPU_MYGOFRUGAL_ROLE'] = $user_gf_role;
		$upd_arr['GPU_POS_ROLE'] 		= $user_pos_role;
		$upd_arr['GPU_POS_ROLE_ID'] 	= $pos_role_id;
		$upd_arr['GPU_SYSTEM_ACCESS'] 	= $system_access;
	}
	$key_arr['GPU_CONTACT_TYPE']= $contact_type;
	$key_arr['GPU_INSTALL_ID'] 	= $install_id;
	$key_arr['GPU_USER_ID'] 	= $ref_emp_id;	
	
	array_update_tables_common($upd_arr, "gft_pos_users", $key_arr, null, SALES_DUMMY_ID,null,null,$upd_arr);
}

/**
 * @param string $install_id
 * @param string $ref_emp_id
 * @param string $app_pcode
 * @param string $user_status
 * 
 * @return void
 */
function update_app_user($install_id,$ref_emp_id,$app_pcode,$user_status){
	$insert_arr['GAU_INSTALL_ID']	= $install_id;
	$insert_arr['GAU_USER_ID']		= $ref_emp_id;
	$insert_arr['GAU_APP_PCODE'] 	= $app_pcode;
	$insert_arr['GAU_USER_STATUS']	= $user_status;
	$insert_arr['GAU_UPDATED_DATE']	= date('Y-m-d H:i:s');
	
	$key_arr['GAU_INSTALL_ID'] 	= $install_id;
	$key_arr['GAU_USER_ID']		= $ref_emp_id;
	$key_arr['GAU_APP_PCODE']	= $app_pcode;
	array_update_tables_common($insert_arr, "gft_app_users", $key_arr, null, SALES_DUMMY_ID,null,null,$insert_arr);
}

/**
 * @param string $install_id
 * @param string $device_id
 * @param string $app_pcode
 * @param string $device_status
 *
 * @return void
 */
function update_device_id($install_id,$device_id,$app_pcode,$device_status){
	$insert_arr['GCD_INSTALL_ID']	= $install_id;
	$insert_arr['GCD_DEVICE_ID']	= $device_id;
	$insert_arr['GCD_APP_PCODE'] 	= $app_pcode;
	$insert_arr['GCD_DEVICE_STATUS']= $device_status;
	$insert_arr['GCD_UPDATED_DATE']	= date('Y-m-d H:i:s');
	
	$key_arr['GCD_INSTALL_ID'] 	= $install_id;
	$key_arr['GCD_DEVICE_ID']	= $device_id;
	$key_arr['GCD_APP_PCODE']	= $app_pcode;
	array_update_tables_common($insert_arr, "gft_customer_device_mapping", $key_arr, null, SALES_DUMMY_ID,null,null,$insert_arr);
}

/**
 * @param string $customer_id
 * @param string $install_id
 * @param string $ref_emp_id
 * @param string $user_mobile
 * @param string $user_email
 * @param string $user_name
 * @param string $user_status
 * @param string $user_gf_role
 * @param string $user_pos_role
 * @param boolean $from_app_users
 * @param string $pos_role_id
 * @param string $user_password
 * @param int $system_access
 * @param string $contact_name
 *
 * @return void
 */
function save_pos_user_contacts($customer_id,$install_id,$ref_emp_id,$user_mobile,$user_email,$user_name,$user_status='A',$user_gf_role='5',$user_pos_role='',$from_app_users=false,$pos_role_id='',$user_password='',$system_access=0,$contact_name=''){
	$lead_type = get_single_value_from_single_table("GLH_LEAD_TYPE", "gft_lead_hdr", "GLH_LEAD_CODE", $customer_id);
	$lead_subtype = (int)get_single_value_from_single_table("GLH_LEAD_SUBTYPE", "gft_lead_hdr", "GLH_LEAD_CODE", $customer_id);
	if( ($lead_type=='2') && ($lead_subtype!=10) ){ //to skip partner
		return;
	}
	$new_contact = false;
	$contact_desig_arr = array('5');
	$mobile_contact_status = $email_contact_status = $user_status;
	//Mobile Number Detail
	$wh_cond = getContactDtlWhereCondition("GCC_CONTACT_NO", $user_mobile);
	$mob_que_res = execute_my_query("select GPU_CONTACT_ID as gcc_id from gft_pos_users where GPU_INSTALL_ID='$install_id' and GPU_USER_ID='$ref_emp_id' and GPU_CONTACT_TYPE=1 ");
	if(mysqli_num_rows($mob_que_res)==0){
		$user_exists = false;
		$mob_que_res = execute_my_query("select GCC_ID as gcc_id from gft_customer_contact_dtl where GCC_LEAD_CODE='$customer_id' and gcc_contact_type in (1,2) and $wh_cond ");			
	}else{
		$user_exists = true;
	}
	$mob_len = strlen($user_mobile);
	$contact_type = '2';
	if( (($mob_len==10) || ($mob_len==11)) && check_can_send_sms($user_mobile) ){
		$contact_type 	= '1'; // Mobile
	}
	if($data1 = mysqli_fetch_array($mob_que_res)){
		$mobile_gcc_id = (int)$data1['gcc_id'];
	}else{
		$new_contact = true;
		$arr_gcc_id = insert_lead_contact_nos(array($user_name), array($user_mobile), $contact_desig_arr, $customer_id, null, array($contact_type),
				null,null,null,null,null,'off',false,null,null,null,null,null,null,null,null,'',false);
		$mobile_gcc_id = isset($arr_gcc_id[0])?$arr_gcc_id[0]:0;
	}
	if($mobile_gcc_id > 0){
		if($user_exists){
			if($user_mobile==''){
				$mobile_contact_status = 'I';
			}else{
				$existing_gcc_id = 0;
				$mob_que_res = execute_my_query("select GCC_ID from gft_customer_contact_dtl where GCC_LEAD_CODE='$customer_id' and gcc_contact_type in (1,2) and $wh_cond ");
				if($row1 = mysqli_fetch_array($mob_que_res)){
					$existing_gcc_id = $row1['GCC_ID'];
				}
				if($existing_gcc_id==0){
					execute_my_query(" update gft_customer_contact_dtl set GCC_CONTACT_NO='$user_mobile',GCC_CONTACT_TYPE='$contact_type' where GCC_ID='$mobile_gcc_id' ");
				}else if($existing_gcc_id!=$mobile_gcc_id){
					$mobile_gcc_id = $existing_gcc_id;
					//for pd approval - trainee
					$pd_upd_que =" update gft_pd_training_feedback_dtl join gft_pos_users on (GPU_CONTACT_ID=GPT_CONTACT_ID) ".
								 " set GPT_CONTACT_ID='$mobile_gcc_id' where GPU_INSTALL_ID='$install_id' and GPU_USER_ID='$ref_emp_id' ".
								 " and GPT_IS_SPOC=1 and GPT_ACK_STATUS=0 and GPU_CONTACT_TYPE=1 ";
					execute_my_query($pd_upd_que);
				}
			}
		}
		update_pos_user($mobile_gcc_id,1,$install_id,$ref_emp_id,$user_name,$mobile_contact_status,$user_gf_role,$user_pos_role,$from_app_users,$pos_role_id,$user_password,$system_access);
	}
	
	//Email Id Detail
	$email_query_res = execute_my_query("select GPU_CONTACT_ID as gcc_id from gft_pos_users where GPU_INSTALL_ID='$install_id' and GPU_USER_ID='$ref_emp_id' and GPU_CONTACT_TYPE=4");
	if(mysqli_num_rows($email_query_res)==0){
		$user_exists = false;
		$email_query_res = execute_my_query("select GCC_ID as gcc_id from gft_customer_contact_dtl where GCC_LEAD_CODE='$customer_id' and GCC_CONTACT_NO='$user_email'");
	}else{
		$user_exists = true;
	}
	if($data2 = mysqli_fetch_array($email_query_res)){
		$email_gcc_id = (int)$data2['gcc_id'];
	}else{
		$arr_gcc_id = insert_lead_contact_nos(array($user_name), array($user_email), $contact_desig_arr, $customer_id, null, array('4'),
						null,null,null,null,null,'off',false,null,null,null,null,null,null,null,null,'',false);
		$email_gcc_id = isset($arr_gcc_id[0])?$arr_gcc_id[0]:0;
	}
	if($email_gcc_id > 0){
		if($user_exists){
			if($user_email==''){
				$email_contact_status = 'I';
			}else{
				$email_query_res = execute_my_query("select GCC_ID from gft_customer_contact_dtl where GCC_LEAD_CODE='$customer_id' and GCC_CONTACT_NO='$user_email'");
				$existing_gcc_id = 0;
				if($row1 = mysqli_fetch_array($email_query_res)){
					$existing_gcc_id = (int)$row1['GCC_ID'];
				}
				if($existing_gcc_id==0){
					execute_my_query(" update gft_customer_contact_dtl set GCC_CONTACT_NO='$user_email' where GCC_ID='$email_gcc_id' ");					
				}else if($existing_gcc_id!=$email_gcc_id){
					$email_gcc_id = $existing_gcc_id;
					//for pd approval - trainee
					$pd_upd_que =" update gft_pd_training_feedback_dtl join gft_pos_users on (GPU_CONTACT_ID=GPT_CONTACT_ID) ".
							" set GPT_CONTACT_ID='$email_gcc_id' where GPU_INSTALL_ID='$install_id' and GPU_USER_ID='$ref_emp_id' ".
							" and GPT_IS_SPOC=1 and GPT_ACK_STATUS=0 and GPU_CONTACT_TYPE=4 ";
					execute_my_query($pd_upd_que);
				}
			}
		}
		update_pos_user($email_gcc_id,4,$install_id,$ref_emp_id,$user_name,$email_contact_status,$user_gf_role,$user_pos_role,$from_app_users,$pos_role_id,$user_password,$system_access);
	}
	
	if($new_contact && ($user_status=='A') && check_can_send_sms($user_mobile) ){
		$category = 176;
		$sms_content_config = array('Name'=>array($user_name));
		$sms_content = get_formatted_content($sms_content_config, $category);
		entry_sending_sms($user_mobile, $sms_content, $category, null,1,SALES_DUMMY_ID,0,null,$customer_id);
	}
	
	if($user_status=='I'){
		$contact_cond = getContactDtlWhereCondition("GCL_USERNAME", $user_mobile);
		$upd_que = " update gft_customer_login_master join gft_customer_access_dtl on (GCA_USER_ID=GCL_USER_ID) ".
				   " set GCA_ACCESS_STATUS=0 ".
				   " where GCA_ACCESS_LEAD='$customer_id' and ($contact_cond or GCL_USERNAME='$user_mobile') ";
		execute_my_query($upd_que);
	}
	$gcc_contact_name = $user_name;
	if($contact_name!=''){
		$gcc_contact_name = $contact_name;
	}
	if( ($ref_emp_id!='1') && (!$from_app_users || ($contact_name!='')) && (($mobile_gcc_id > 0) || ($email_gcc_id > 0)) ){
		$name_update_query = "update gft_customer_contact_dtl set GCC_CONTACT_NAME='$gcc_contact_name' where GCC_ID in ($mobile_gcc_id,$email_gcc_id) ";
		execute_my_query($name_update_query);
	}
}

/**
 * @param string $from_date_time
 * @param string $to_date_time
 * @param string $emp_code
 * @param boolean $for_daily_report
 * 
 * @return void
 */
function show_support_availablity($from_date_time,$to_date_time,$emp_code,$for_daily_report=false){
	global $sortbycol,$sorttype;
	$document_time_mins = (int)get_samee_const("Documentation_Time_In_Minutes");
	$support_sub_query =" select GCD_EMPLOYEE_ID, count(GCD_EMPLOYEE_ID) as total_activities, count(distinct GCH_LEAD_CODE) as unique_customers, ".
			" sum(if(GCD_NATURE=12 and GCD_VISIT_TIMEOUT>'0000-00-00 00:00:00', TIMESTAMPDIFF(MINUTE,GCD_ACTIVITY_DATE,GCD_VISIT_TIMEOUT), '0')) as chat_duration ".
			" from gft_customer_support_hdr join gft_customer_support_dtl on (GCH_COMPLAINT_ID=gcd_complaint_id) ".
			" where GCD_ACTIVITY_DATE >= '$from_date_time' and GCD_ACTIVITY_DATE <= '$to_date_time' ".
			" group by GCD_EMPLOYEE_ID ";

	$time_spent_subque =" select g1.GVA_LOGIN_ID as emp_id, sum(time_to_sec( timediff(case when g2.GVA_TIME is null then g1.GVA_TIME ".
			" when g2.GVA_TIME > '$to_date_time' then '$to_date_time' else g2.GVA_TIME  end , ".
			" if(g1.GVA_TIME < '$from_date_time', '$from_date_time', g1.GVA_TIME) )) ) time_spent ".
			" from gft_voicesnap_access_log g1 ".
			" left join  gft_voicesnap_access_log g2 on( g2.GVA_ACCESS_NO=g1.GAV_NEXT_ID) ".
			" join gft_agent_status_master m1 on (m1.GAS_ID = g1.GVA_STATUS) ".
			" join gft_agent_status_master m2 on (m2.GAS_ID = g2.GVA_STATUS) ".
			" WHERE g1.GVA_STATUS  in (1,4,5)  and g1.GVA_TIME between '$from_date_time' and '$to_date_time' ".
			" group by g1.GVA_LOGIN_ID ";

	$call_sub_query	= " select GTC_AGENT_ID, sum(if(GTC_CALL_STATUS=1,1,0)) as ic, sum(if(GTC_CALL_STATUS=3,1,0)) as im, sum(if(GTC_CALL_STATUS=4,1,0)) as oc, ".
			" sum(if(GTC_CALL_STATUS=1 and GTC_RING_TIME > '00:00:10',1,0)) as ic_after_10_sec, ".
			" sum(if(GTC_WRAPUP_TIME>'00:02:00',1,0)) as abnormal_wrapup, sum(if(GTC_DURATION > '00:10:00',1,0)) as more_than_10_mins ".
			" from gft_techsupport_incomming_call where GTC_DATE between '$from_date_time' and '$to_date_time' and (GTC_SPECIFIC_REASON is null or GTC_SPECIFIC_REASON!='vsmile') group by GTC_AGENT_ID ";
		
	$select_query = " select GEM_EMP_ID,GEM_EMP_NAME,s1.total_activities,s1.unique_customers,(s1.total_activities*$document_time_mins) as doc_time,s1.chat_duration,round(ifnull(s2.time_spent,0)/60) as online, ".
			" ((ifnull(s1.total_activities,0)*$document_time_mins)+ifnull(s1.chat_duration,0)+round(ifnull(s2.time_spent,0)/60)) as total_time,  ".
			" s3.ic, s3.im, s3.oc, s3.ic_after_10_sec, s3.abnormal_wrapup, s3.more_than_10_mins ".
			" from gft_emp_master ".
			" left join ($support_sub_query) s1 on (s1.GCD_EMPLOYEE_ID=GEM_EMP_ID) ".
			" left join ($time_spent_subque) s2 on (s2.emp_id = GEM_EMP_ID) ".
			" left join ($call_sub_query) s3 on (s3.GTC_AGENT_ID = GEM_EMP_ID) ".
			" where GEM_STATUS='A' and gem_emp_id!=9999 and (s1.GCD_EMPLOYEE_ID is not null or s2.emp_id is not null or s3.GTC_AGENT_ID is not null) ";

	if( ($emp_code!='0') && ($emp_code!='') ){
		$select_query .= " and GEM_EMP_ID='$emp_code' ";
	}
	if($sortbycol!=''){
		$select_query .= " order by $sortbycol ";
		$select_query .= ($sorttype=='2')?"desc ":"";
	}else{
		$select_query .= " order by GEM_EMP_NAME ";
	}

	$result1 = execute_my_query($select_query);
	$num_rows = mysqli_num_rows($result1);
	$myarr = array("S.No","Employee Name","No. of Activities","No. of Unique Customers","Online Time (Mins)","Document Time (Mins)",/*"Chat Duration (Mins)","Total Time (Mins)","Target (Mins)","Difference (Mins)","Efficiency %",*/
			"Incomming Calls", "IC picked after 10 secs","Abnormal Wrapup","Calls More than 10 mins","Incomming Missed","Outgoing Calls","Outgoing call %");
	$mysort = array("","GEM_EMP_NAME","total_activities","unique_customers","online","doc_time",/*"chat_duration","total_time","","","total_time",*/
			"ic","ic_after_10_sec","abnormal_wrapup","more_than_10_mins","im","oc","");
	$align_arr = array("","","right","right","right","right",/*"right","right","right","right","right",*/"right","right","right","right","right","right","right","right","right","right");
	$nav = get_dtable_navigation_struct($num_rows,$num_rows);
	$table_border=1;
	if(!$for_daily_report){
		$table_border =  0;
		print_dtable_header("Support Availability Dashboard");
		print_dtable_navigation($num_rows, $nav, "support_availability_report.php");
	}
	echo<<<END
<table cellpadding="0" cellspacing="2"  border="$table_border" class="FormBorder1" width="100%">
END;
	sortheaders($myarr, $mysort, $nav, $sortbycol, $sorttype);
	$sl=0;
	$online_target_in_minutes = (int)get_samee_const("Online_Target_In_Minutes");
	while($row_data = mysqli_fetch_array($result1)){
		$sl++;
		$employee_id		= $row_data['GEM_EMP_ID'];
		$call_detail_url = "http://".$_SERVER['SERVER_NAME']."/tech_incomming_call_details.php?from_dt_time=$from_date_time&to_dt_time=$to_date_time&vs_call_agent=$employee_id";
		$target_in_minutes = $online_target_in_minutes;
		if(!$for_daily_report){
			$target_in_minutes 	= $online_target_in_minutes * ((int)datediff($from_date_time, $to_date_time) + 1);
		}
		$total_activities 	= (int)$row_data['total_activities'];
		$unique_customers 	= (int)$row_data['unique_customers'];
		$doc_time		  	= (int)$row_data['doc_time'];
		$chat_duration		= (int)$row_data['chat_duration'];
		$total_time			= (int)$row_data['total_time'];
		$differene_time		= $target_in_minutes - $total_time;
		$efficiency_percent = round($total_time*100/$target_in_minutes, 2);
		$ic_calls			= (int)$row_data['ic'];
		$ic_pick_after_10_sec=(int)$row_data['ic_after_10_sec'];
		$abnormal_wrapup	= (int)$row_data['abnormal_wrapup'];
		$ic_duration_10_mins= (int)$row_data['more_than_10_mins'];
		$im_calls			= (int)$row_data['im'];
		$oc_calls			= (int)$row_data['oc'];
		$oc_percent			= /*. (float) .*/0;
		$ic_and_oc_calls	= $ic_calls + $oc_calls;
		if($ic_and_oc_calls > 0){
			$oc_percent = round($oc_calls/$ic_and_oc_calls,4)*100;
		}
		$ic_call_link = "<a target='_blank' href='$call_detail_url&vs_call_sytatus=1'>$ic_calls</a>";
		$im_call_link = "<a target='_blank' href='$call_detail_url&vs_call_sytatus=3'>$im_calls</a>";
		$oc_call_link = "<a target='_blank' href='$call_detail_url&vs_call_sytatus=4'>$oc_calls</a>";
		$ic_pick_10_sec_link 	= "<a target='_blank' href='$call_detail_url&vs_call_sytatus=1&custom_filter=1'>$ic_pick_after_10_sec</a>";
		$abnormal_wrapup_link 	= "<a target='_blank' href='$call_detail_url&custom_filter=2'>$abnormal_wrapup</a>";
		$ic_duration_10_min_link= "<a target='_blank' href='$call_detail_url&custom_filter=3'>$ic_duration_10_mins</a>";

		$value_arr[0] = array($sl,$row_data['GEM_EMP_NAME'],$total_activities,$unique_customers,$row_data['online'],$doc_time,
				/*$chat_duration,$total_time,$target_in_minutes,$differene_time,"$efficiency_percent%",*/$ic_call_link,
				$ic_pick_10_sec_link,$abnormal_wrapup_link,$ic_duration_10_min_link,$im_call_link,$oc_call_link,"$oc_percent%");
		print_resultset($value_arr,null,$align_arr);
	}
	echo "</table>";
	if(!$for_daily_report){
		echo<<<END
<tr><td><font size='3'><pre>
 <b>Note:</b>
	1. Online Time (Mins)   : Total duration in Online, Free and Busy Status
	2. Document Time (Mins) : No of Activities * 2 Minutes
	3. Chat Duration (Mins) : Total time spent on Chat Received activities
	4. Difference (Mins)    : Total Time (Mins) - Target (Mins)
	5. Efficiency %         : (Total Time (Mins) * 100) / Target (Mins)
	6. Outgoing call %      : Outgoing Calls *100 / (Outgoing Calls + Incomming Calls)
</pre></font>
</td></tr>
END;
	}
}

/**
 * @param int $emp_id
 *
 * @return string[string]
 */
function check_is_partner_employee($emp_id){
	$partner_emp_details	=	/*. (string[string]) .*/array();
	$sql_query	=	" select GLEM_EMP_ID,CGI_EMP_ID,GLEM_LEADCODE,gem_emp_name,GEM_REPORTING_MGR_NAME,GEM_ROLE_ID from gft_leadcode_emp_map ".
			" inner join gft_emp_master on(gem_emp_id=GLEM_EMP_ID and gem_emp_id=$emp_id) ".
			" inner join gft_cp_info on(GLEM_LEADCODE=CGI_LEAD_CODE and GLEM_EMP_ID!=CGI_EMP_ID) ".
			" where GLEM_EMP_ID=$emp_id and gem_status='A'";
	$result_emp	=	execute_my_query($sql_query);
	if($row=mysqli_fetch_array($result_emp)){
		$partner_emp_details['partner_id']		=	/*. (string) .*/$row['CGI_EMP_ID'];
		$partner_emp_details['partner_lead_code']=	/*. (string) .*/$row['GLEM_LEADCODE'];
		$partner_emp_details['employee_role']	=	/*. (string) .*/$row['GEM_ROLE_ID'];
	}
	return $partner_emp_details;
}

/**
 * @param string $contact_no
 * 
 * @return string[string]
 */
function get_installed_employee_from_contact($contact_no){
	$ret_arr = /*. (string[string]) .*/array();
	if($contact_no==''){
		return $ret_arr;
	}
	$gem_mobile_cond = getContactDtlWhereCondition("GEM_MOBILE", $contact_no);
	$gem_reliance_cond = getContactDtlWhereCondition("GEM_RELIANCE_NO", $contact_no);
	
	$ins_by_que=" select GEM_EMP_ID from gft_emp_master where GEM_STATUS='A' and ($gem_mobile_cond or $gem_reliance_cond) ";
	$ins_by_res=execute_my_query($ins_by_que);
	if($data=mysqli_fetch_array($ins_by_res)){
		$gem_emp_id	= $data['GEM_EMP_ID'];
		$partner_arr = check_is_partner_employee($gem_emp_id);
		$partner_id = isset($partner_arr['partner_id'])?(int)$partner_arr['partner_id']:0;
		if($partner_id > 0){
			$ret_arr['GEM_EMP_ID'] 	= $partner_id;
			$ret_arr['RTC_ID'] 		= $gem_emp_id;
		}else{
			$ret_arr['GEM_EMP_ID'] = $gem_emp_id;
		}
	}
	return $ret_arr;	
}
/**
 * @param string $event_code
 *
 * @return int
 */
function check_is_valid_event_code($event_code){
	$event_code= strtolower($event_code);
	$result = execute_my_query("select GEM_EVENT_ID  from gft_event_master where LOWER(GEM_EVENT_CODE)='$event_code'");
	if($row_event=mysqli_fetch_array($result)){
		return (int)$row_event['GEM_EVENT_ID'];
	}
	return 0;
}
/**
 * @param string $cust_id
 * @param string $event_id
 * @param string $emp_id
 * @param int $lead_type
 * @param int $lead_subtype
 * @param int $category
 * @param string $qtype
 *
 * @return void
 */
function do_leads_mapping_with_event($cust_id,$event_id,$emp_id,$lead_type=1,$lead_subtype=0,$category=0,$qtype=''){
    $is_exist = execute_my_query("select GEL_ID from gft_event_leads_dtl where GLE_LEAD_CODE='$cust_id' AND GEL_EVENT_ID='$event_id'");
    if(mysqli_num_rows($is_exist) == 0){
        $event_leads["GEL_EVENT_ID"] = $event_id;
        $event_leads["GLE_CREATED_BY"] = "$emp_id";
        $event_leads["GLE_CREATED_ON"] = date("Y-m-d H:i:s");
        $event_leads['GLE_LEAD_CODE']=$cust_id;
        $event_leads['GEL_CATEGORY_NAME']=$category;
        $event_leads['GEL_QUERY_TYPE'] = $qtype;
        array_insert_query("gft_event_leads_dtl", $event_leads);
        $sms_content = get_single_value_from_single_table("GEM_NEW_LEAD_SMS", "gft_event_master", "GEM_EVENT_ID", $event_id);
        if($lead_type==2 && $lead_subtype==10){	//For referral partner
            $sms_content = get_single_value_from_single_table("GEM_REF_PARTNER_SMS", "gft_event_master", "GEM_EVENT_ID", $event_id);
        }else if($lead_type==2){//Other than referral partner
            $sms_content = get_single_value_from_single_table("GEM_PARTNER_SMS", "gft_event_master", "GEM_EVENT_ID", $event_id);
        }
        if($sms_content!=""){
            entry_sending_sms_to_customer("",get_formatted_content(array('content'=>array($sms_content),'Customer_ID'=>array($cust_id)),204),204,$cust_id,1,'9999',0,null,false);
        }
    }		
}
/**
 * @param string $table_name
 * @param string[string] $insert_arr
 * @param string $ret_type 
 *
 * @return mixed
 */
function array_insert_query($table_name, $insert_arr,$ret_type=""){
	$lr = 0;
	$column_name = $column_value = "";
	foreach($insert_arr as $key => $value){
		$column_name.=($lr!=0?",":"")."$key";
		$column_value.=($lr!=0?",":"")."'".mysqli_real_escape_string_wrapper($value)."'";
		$lr++;
	}
	$insert_query="insert into $table_name ($column_name) value ($column_value)";
	$res = execute_my_query($insert_query);
	if($ret_type=="boolean"){
		return $res;
	}
	if($res){
		return mysqli_insert_id_wrapper();
	}
	return 0;
}

/**
 * @param string $partner_id
 *
 * @return void
 */
function send_mail_for_dealer_order_entry($partner_id){
	$order_sql = " select sum(if(GFT_SKEW_PROPERTY in (1),GMO_ORDERED_QTY-GMO_USED_QTY,0)) as server_inhand, ".
			" sum(if(GFT_SKEW_PROPERTY in (3),GMO_ORDERED_QTY-GMO_USED_QTY,0)) as client_inhand, ".
			" count(distinct GMO_ORDER_NO) as order_cnt, GEM_MOBILE, GEM_EMAIL,GLH_CUST_NAME,GLH_LEAD_CODE ".
			" from gft_order_hdr join gft_mp_order_hdr on (GOD_ORDER_NO=GMO_ORDER_NO) ".
			" join gft_order_product_dtl on (GOP_ORDER_NO=GMO_ORDER_NO and GMO_FULLFILLMENT_NO=GOP_FULLFILLMENT_NO) ".
			" join gft_product_master on (GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" join gft_emp_master on (GEM_EMP_ID=GMO_PARTNER_ID) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ".
			" where GMO_PARTNER_ID='$partner_id' and GMO_ORDER_TYPE='dealer' and GOD_ORDER_STATUS='A' ".
			" group by GMO_PARTNER_ID ";
	$result_sql = execute_my_query($order_sql);
	if($row1 = mysqli_fetch_array($result_sql)){
		$server_inhand = (int)$row1['server_inhand'];
		$client_inhand = (int)$row1['client_inhand'];
		$partner_mail  = $row1['GEM_EMAIL'];
		$mail_content = array(
				'msg'=>array($server_inhand),
				'No_Clients'=>array($client_inhand),
				'Customer_Name'=>array($row1['GLH_CUST_NAME']),
				'Customer_Id'=>array($row1['GLH_LEAD_CODE']),
				'Bussno'=>array($row1['GEM_MOBILE']),
				'Mail_conformation_id'=>array($partner_mail),
		);
		$mail_template = 248;
		if($row1['order_cnt']=='1'){
			$mail_template = 247;
		}
		send_formatted_mail_content($mail_content, 9, $mail_template,null,null,array($partner_mail));
	}
}

/**
 * @param string $gid_lead_code
 * @param string $install_id
 *
 * @return void
 */
function update_connectplus_token($gid_lead_code, $install_id){
	$connectplus_token = uniqid(null,true);
	execute_my_query("update gft_install_dtl_new set GID_CONNECTPLUS_TOKEN='$connectplus_token' where GID_INSTALL_ID='$install_id' ");
	post_token_to_authservice($gid_lead_code,$install_id);
}

/**
 * @param string $order_no
 * @param string $gco_cust_code
 *
 * @return void
 */
function update_kit_based_dependency($order_no,$gco_cust_code=''){
	$query1 =" select GOD_LEAD_CODE,GOD_ORDER_DATE,GOP_ORDER_NO,GOP_FULLFILLMENT_NO,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GPM_ORDER_TYPE,GOP_QTY, ".
			" god_order_splict from gft_order_hdr join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
			" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
			" where GOD_ORDER_NO='$order_no' and GOD_ORDER_STATUS='A' and fm.GPM_IS_INTERNAL_PRODUCT=4 order by GPM_ORDER_TYPE ";
	$que_res = execute_my_query($query1);
	$create_skew_arr = /*. (int[string]) .*/array();
	$lead_code = $order_date = "";
	$add_hq_qty = 0;
	$split_the_kit = false;
	while($row1 = mysqli_fetch_array($que_res)){
		$pcode 		= $row1['GOP_PRODUCT_CODE'];
		if($pcode=='309'){
			$split_the_kit = true;
		}
		$pskew 		= $row1['GOP_PRODUCT_SKEW'];
		$lead_code	= $row1['GOD_LEAD_CODE'];
		$order_date = $row1['GOD_ORDER_DATE'];
		$order_type = $row1['GPM_ORDER_TYPE'];
		$order_split= (int)$row1['god_order_splict'];
		$ordered_qty= (int)$row1['GOP_QTY'];
		$sql1 = " select GSK_PRODUCT_CODE,GSK_PRODUCT_SKEW,($ordered_qty*GSK_PRODUCT_QTY) as GSK_PRODUCT_QTY ".
				" from gft_skew_kit_master where GSK_KIT_PCODE='$pcode' and GSK_KIT_PSKEW='$pskew' ";
		if($order_type=='3'){
			$add_hq_qty += $ordered_qty;
		}
		$res1 = execute_my_query($sql1);
		while ($data1 = mysqli_fetch_array($res1)){
			$gsk_pcode_skew = $data1['GSK_PRODUCT_CODE']."-".$data1['GSK_PRODUCT_SKEW'];
			if(!isset($create_skew_arr[$gsk_pcode_skew])){
				$create_skew_arr[$gsk_pcode_skew] = 0;
			}
			$create_skew_arr[$gsk_pcode_skew] += (int)$data1['GSK_PRODUCT_QTY'];
		}
	}
	if(count($create_skew_arr)==0){
		return ;
	}
	$gop_product_code_arr = $gop_product_skew_arr = /*. (string[int]) .*/array();
	$gop_qty_arr = $pvalue_arr = $listvalue_arr = /*. (string[int]) .*/array();
	foreach ($create_skew_arr as $sk => $qty_val){
		$arr = explode('-', $sk);
		$gop_product_code_arr[] = $arr[0];
		$gop_product_skew_arr[] = $arr[1];
		$gop_qty_arr[]			= "$qty_val";
		$pvalue_arr[]			= "0";
		$listvalue_arr[]		= "0";
	}
	$order_res = insert_stmt_for_order_product_dtl($order_no, $lead_code, $gop_product_code_arr, $gop_product_skew_arr, $gop_qty_arr, $pvalue_arr,true,$listvalue_arr);
	if($order_res){
		for($n=0;$n<count($gop_product_code_arr);$n++){
			if($gop_product_code_arr[$n]=='300'){
				if(is_this_user_based($gop_product_code_arr[$n], $gop_product_skew_arr[$n])){
					insert_stmt_for_split_order_dtl($lead_code, $lead_code, $gop_qty_arr[$n], $order_no,$gop_product_code_arr[$n], $gop_product_skew_arr[$n], $order_date, date('Y-m-d H:i:s'), SALES_DUMMY_ID);
				}
			}else{
				$use_lead_code = $lead_code;
				if( ($split_the_kit) && ($order_split==1) && ((int)$gco_cust_code!=0) ){
					insert_stmt_for_split_order_dtl($lead_code, $gco_cust_code, $gop_qty_arr[$n], $order_no,$gop_product_code_arr[$n], $gop_product_skew_arr[$n], $order_date, date('Y-m-d H:i:s'), SALES_DUMMY_ID);
					$use_lead_code = $gco_cust_code;
				}
				update_order_license_given_dtl($use_lead_code, $gop_product_code_arr[$n],$order_split);
			}
		}
		if($add_hq_qty > 0){
			$ins_chk =" select GID_LIC_PCODE,GID_LIC_PSKEW,GID_INSTALL_ID,GPM_PRODUCT_TYPE from gft_install_dtl_new ".
					" join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
					" where GID_LEAD_CODE='$lead_code' and GID_LIC_PCODE=300 and GPM_UAT_LICENSE='N' and GID_STATUS in ('A','S') ";
			$res_chk = execute_my_query($ins_chk);
			if(mysqli_num_rows($res_chk) > 0){ //installed
				$row_data 	= mysqli_fetch_array($res_chk);
				$install_id = $row_data['GID_INSTALL_ID'];
				$upd_query 	= "update gft_install_dtl_new set GID_NO_CLIENTS=GID_NO_CLIENTS+$add_hq_qty where GID_INSTALL_ID='$install_id' ";
			}else{ //not installed
				$upd_query =" update gft_cp_order_dtl join gft_order_hdr on (GOD_ORDER_NO=GCO_ORDER_NO) ".
							" join gft_product_master on (GCO_PRODUCT_CODE=GPM_PRODUCT_CODE and GCO_SKEW=GPM_PRODUCT_SKEW) ".
							" set GCO_ADD_CLIENTS = GCO_ADD_CLIENTS+$add_hq_qty where GOD_LEAD_CODE='$lead_code' and GCO_PRODUCT_CODE=300 ".
							" and GPM_ORDER_TYPE=1 and (GCO_REFERENCE_ORDER_NO is null or GCO_REFERENCE_ORDER_NO='') and GOD_ORDER_STATUS='A' ";
			}
			execute_my_query($upd_query);
		}
	}
}

function pcs_entry_condition(){
    return " and GFT_SKEW_PROPERTY IN(8,25) and GPM_TRAINING_PCS_HRS > 0 ";
}

/**
 * @param string $god_order_no
 * 
 * @return void
 */
function update_pcs_entry_hrs($god_order_no){
    $pcs_cond = pcs_entry_condition();
	$sql1 = " select GOP_ORDER_NO,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW, (GOP_QTY * GPM_TRAINING_PCS_HRS) as tot_hrs ".
			" from gft_order_hdr ".
			" join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
			" left join gft_cp_order_dtl on (GCO_ORDER_NO=GOP_ORDER_NO and GCO_PRODUCT_CODE=GOP_PRODUCT_CODE and GOP_PRODUCT_SKEW=GCO_SKEW) ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=ifnull(GCO_CUST_CODE,GOD_LEAD_CODE)) ".
			" where GOD_ORDER_NO='$god_order_no' $pcs_cond ";
	$res1 = execute_my_query($sql1);
	while ($row1 = mysqli_fetch_array($res1)) {
		$insert_arr	= array(
						'GPA_ORDER_NO' 		=> $row1['GOP_ORDER_NO'],
						'GPA_PRODUCT_CODE' 	=> $row1['GOP_PRODUCT_CODE'],
						'GPA_PRODUCT_SKEW' 	=> $row1['GOP_PRODUCT_SKEW'],
			  			'GPA_TOTAL_HRS' 	=> $row1['tot_hrs'],
					  );
		$key_arr = array(
					'GPA_ORDER_NO' 		=> $row1['GOP_ORDER_NO'],
					'GPA_PRODUCT_CODE' 	=> $row1['GOP_PRODUCT_CODE'],
					'GPA_PRODUCT_SKEW' 	=> $row1['GOP_PRODUCT_SKEW'],
				   );
		array_update_tables_common($insert_arr, "gft_pcs_activity_order_hdr", $key_arr, null, '9999',null,null,$insert_arr);
	}
}

/**
 * @param string $order_no
 * 
 * @return void
 */
function order_creation_post_process($order_no){
    $que1 = " select GLH_LEAD_CODE,GOD_ORDER_DATE,GLH_CUST_NAME,GFT_SKEW_PROPERTY,GOP_QTY,fm.GPM_PRODUCT_NAME, ".
            " GPM_ORDER_ALERT_TEMPLATE,GPM_ORDER_ALERT_TEMPLATE_EMAIL,GPM_SKEW_DESC, ".
            " t1.GEM_EMP_NAME ordBy from gft_order_hdr ".
            " join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
            " left join gft_cp_order_dtl on (GCO_ORDER_NO=GOP_ORDER_NO and GCO_PRODUCT_CODE=GOP_PRODUCT_CODE and GCO_SKEW=GOP_PRODUCT_SKEW) ".
            " join gft_lead_hdr on (GLH_LEAD_CODE=ifnull(GCO_CUST_CODE,GOD_LEAD_CODE)) ".
            " join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
            " join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
            " left join gft_emp_master t1 on (t1.GEM_EMP_ID=GOD_EMP_ID) ".
            " where GOD_ORDER_NO='$order_no' and GOD_ORDER_STATUS='A' and GOD_ORDER_AMT > 0 ";
    $res1 = execute_my_query($que1);
    while ($row1 = mysqli_fetch_array($res1)){
        $lead_code      = $row1['GLH_LEAD_CODE'];
        $skew_property  = $row1['GFT_SKEW_PROPERTY'];
        $customer_name  = $row1['GLH_CUST_NAME'];
        $emp_name       = $row1['ordBy'];
        $order_date     = $row1['GOD_ORDER_DATE'];
        $gop_qty        = $row1['GOP_QTY'];
        $prod_name      = $row1['GPM_PRODUCT_NAME'];
        $gpm_temp_id    = (int)$row1['GPM_ORDER_ALERT_TEMPLATE'];
        $gpm_temp_email = $row1['GPM_ORDER_ALERT_TEMPLATE_EMAIL'];
        $skew_desc      = $row1['GPM_SKEW_DESC'];
        /* Send mail notification to current product team when created order */
        if( ($gpm_temp_id > 0) && !in_array($skew_property,array('4','15')) ){
            $mail_content_config = array(
                'Customer_Name'=>array($customer_name),
                'Employee_Id'=>array($emp_name),
                'Customer_Id'=>array($lead_code),
                'dateon'=>array($order_date),
                'Qty'=>array($gop_qty),
                'Product_Name'=>array($prod_name),
                'Skew_Desc'=>array($skew_desc)
            );
            send_formatted_mail_content($mail_content_config,68,$gpm_temp_id,null,null,array($gpm_temp_email),null,null,null,null,null,null);
        }
    }
}

/**
 * @param string $order_no
 * @param string $gco_cust_code
 *
 * @return void
 */
function update_dealer_dependency($order_no,$gco_cust_code=''){
	update_kit_based_dependency($order_no,$gco_cust_code);
	update_pcs_entry_hrs($order_no);
	order_creation_post_process($order_no);
	$query1 =" select GOP_ORDER_NO,GOP_FULLFILLMENT_NO,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GOD_ORDER_STATUS,GOP_QTY,CGI_EMP_ID,CGI_LEAD_CODE ".
			" from gft_order_hdr join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			" join gft_cp_info on (GOD_LEAD_CODE=CGI_LEAD_CODE) ".
			" where GOD_ORDER_NO='$order_no' and GOP_PRODUCT_CODE in (71) and GFT_SKEW_PROPERTY in (1,3,4) ".
			" and GOD_ORDER_STATUS='A' ";
	$res1 = execute_my_query($query1);
	$cgi_lead_code = $cgi_emp_id = "";
	while($row1 = mysqli_fetch_array($res1)){
		$pcode = $row1['GOP_PRODUCT_CODE'];
		$pskew = $row1['GOP_PRODUCT_SKEW'];
		$fullfillment_no = $row1['GOP_FULLFILLMENT_NO'];
		$chk_res = execute_my_query("select GMO_ORDER_NO from gft_mp_order_hdr where GMO_ORDER_NO='$order_no' and GMO_FULLFILLMENT_NO=$fullfillment_no ");
		if(mysqli_num_rows($chk_res) > 0){
			return; //already exists. to prevent update during order edit
		}
		$cgi_lead_code = $row1['CGI_LEAD_CODE'];
		$cgi_emp_id	= $row1['CGI_EMP_ID'];
		$attr_que = " select GPMA_ATTRIBUTE,GPMA_VALUE from gft_product_master_attributes ".
				" where GPMA_PRODUCT_CODE='$pcode' and GPMA_PRODUCT_SKEW='$pskew' ";
		$attr_res = execute_my_query($attr_que);
		$no_of_units = 0;
		if($attr_row = mysqli_fetch_array($attr_res)){
			$no_of_units = (int)$attr_row['GPMA_VALUE'];
		}
		$ordered_qty = $no_of_units * (int)$row1['GOP_QTY'];
		$insert_arr['GMO_PARTNER_ID'] 		= $cgi_emp_id;
		$insert_arr['GMO_ORDER_NO'] 		= $order_no;
		$insert_arr['GMO_FULLFILLMENT_NO'] 	= $fullfillment_no;
		$insert_arr['GMO_ORDERED_QTY'] 		= $ordered_qty;
		$insert_arr['GMO_USED_QTY'] 		= '0';
		$insert_arr['GMO_UPDATED_DATETIME'] = date('Y-m-d H:i:s');
		$insert_arr['GMO_ORDER_TYPE'] 		= 'dealer';
		array_update_tables_common($insert_arr, "gft_mp_order_hdr", null, null, '9999',null,null,$insert_arr);
	}
	if($cgi_lead_code!=''){
		//to update dealership enabled
		execute_my_query("update gft_cp_info set CGI_ENABLE_DEALER=1 where CGI_LEAD_CODE='$cgi_lead_code'");
		send_mail_for_dealer_order_entry($cgi_emp_id);
	}
}

/**
 * @param string $order_no
 * @param string $use_this_token
 * @param int $fullfill_no
 *
 * @return void
 */
function provision_connectplus($order_no,$use_this_token='',$fullfill_no=0){
	$today = date('Y-m-d');
	$gop_query = " select GOD_LEAD_CODE as lead, GOP_PRODUCT_CODE as pcode, GOP_PRODUCT_SKEW as pskew,GOP_FULLFILLMENT_NO as fullfill, GPG_PRODUCT_ALIAS,GOP_QTY*GPM_CLIENTS as qty,GPM_SUBSCRIPTION_PERIOD, ".
			" if(gft_skew_property=1,1,2) as GID_EXPIRE_FOR,if(gft_skew_property in (11,18),DATE_ADD('$today',INTERVAL GPM_SUBSCRIPTION_PERIOD DAY),DATE_ADD('$today',INTERVAL GPM_DEFAULT_ASS_PERIOD DAY)) as validity_date, ".
			" GOP_QTY*GPM_COMPANYS as company_qty,GLH_CUST_NAME,GTM_VERTICAL_NAME, gft_skew_property, GOP_COUPON_HOUR ".
			" from gft_order_hdr join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) ".
			" join gft_product_group_master on (gpg_product_family_code=GOP_PRODUCT_CODE and gpg_skew=substr(GOP_PRODUCT_SKEW,1,4)) ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ".
			" left join gft_vertical_master on (GTM_VERTICAL_CODE=GLH_VERTICAL_CODE) ".
			" where GOD_ORDER_NO='$order_no' and GOD_ORDER_SPLICT=0 ";
	$gco_query = " select GCO_CUST_CODE as lead, GCO_PRODUCT_CODE as pcode,GCO_SKEW as pskew,GCO_FULLFILLMENT_NO as fullfill, GPG_PRODUCT_ALIAS, GCO_CUST_QTY*GPM_CLIENTS as qty,GPM_SUBSCRIPTION_PERIOD, ".
			" if(gft_skew_property=1,1,2) as GID_EXPIRE_FOR,if(gft_skew_property in (11,18),DATE_ADD('$today',INTERVAL GPM_SUBSCRIPTION_PERIOD DAY),DATE_ADD('$today',INTERVAL GPM_DEFAULT_ASS_PERIOD DAY)) as validity_date, ".
			" GCO_CUST_QTY*GPM_COMPANYS as company_qty,GLH_CUST_NAME,GTM_VERTICAL_NAME, gft_skew_property,GCO_COUPON_HOUR as GOP_COUPON_HOUR ".
			" from gft_order_hdr  ". 
			" join gft_cp_order_dtl on (GOD_ORDER_NO=GCO_ORDER_NO) ".
			" join gft_product_group_master on (gpg_product_family_code=GCO_PRODUCT_CODE and gpg_skew=substr(GCO_SKEW,1,4)) ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GCO_PRODUCT_CODE and GPM_PRODUCT_SKEW=GCO_SKEW) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GCO_CUST_CODE) ".
			" left join gft_vertical_master on (GTM_VERTICAL_CODE=GLH_VERTICAL_CODE) ".
			" where GOD_ORDER_NO='$order_no' and GOD_ORDER_SPLICT=1 ";
	if($fullfill_no!=0){
	    $gco_query .= " and GCO_FULLFILLMENT_NO=$fullfill_no ";
	}
	$que1 = " select lead,pcode,pskew,fullfill,GPG_PRODUCT_ALIAS,qty,GPM_SUBSCRIPTION_PERIOD,GID_EXPIRE_FOR,validity_date,company_qty,GLH_CUST_NAME,GTM_VERTICAL_NAME,gft_skew_property, GOP_COUPON_HOUR ".
	        " from ($gop_query) gop union all ($gco_query) ORDER BY gft_skew_property desc ";
	$res = execute_my_query($que1);
	$config_data 		= get_connectplus_config();
	$provision_url 		= $config_data['provision_url'];
	$provision_api_key 	= $config_data['provision_api_key'];
	$gosecure_lead_code = "";
	$gosecure_dtl       = array();
	$requried_mypulse_sync = false;
	$required_saas_addon_sync = false;
	$saas_addon_product_code = array();
	while($data1 = mysqli_fetch_array($res)){
		$product_code = $data1['pcode'];
		$product_skew = $data1['pskew'];
		$lead_code	  = $data1['lead'];
		$glh_cust_name= $data1['GLH_CUST_NAME'];
		$vertical_name= $data1['GTM_VERTICAL_NAME'];
		$fullfill_no  = $data1['fullfill'];
		$client_qty  = $data1['qty'];
		$company_qty = $data1['company_qty'];
		$prod_alias   = $data1['GPG_PRODUCT_ALIAS'];
		$validity_date = $data1['validity_date'];
		$subscrip_days = (int)$data1['GPM_SUBSCRIPTION_PERIOD'];
		$renewal_months = (int)$data1['GOP_COUPON_HOUR'];
		$gft_skew_property = (int)$data1['gft_skew_property'];
		check_and_create_free_product($lead_code,$product_code,$product_skew);		
		$is_saas_addon_pro = 0;
		if(is_active_saas_customer($lead_code)){
		    $is_saas_addon_pro = (int)get_single_value_from_single_query("GAP_ADDON_PRODUCT_CODE", "select GAP_ADDON_PRODUCT_CODE from gft_addon_product_map where  GAP_PRODUCT_CODE IN('601-06.0','605-01.0') AND GAP_STATUS='A' AND GAP_ADDON_PRODUCT_CODE='$product_code'");
		}
		if(in_array($product_code, array('306','526','707','711','712','714','705','734','762','765')) || ($is_saas_addon_pro>0)){ //need to get from master
			$call_provisioning = false;
			$base_schema = '';
			$current_version = "";
			$sel_query = " select GPV_DB_BASE_SCHEMA_URL from gft_product_version_master where GPV_PRODUCT_CODE='$product_code' order by GPV_ENTERED_ON desc limit 1 ";
			$sel_res = execute_my_query($sel_query);
			if($row2 = mysqli_fetch_array($sel_res)){
				$base_schema = $row2['GPV_DB_BASE_SCHEMA_URL'];
			}
			if($renewal_months>0){
			    $subscrip_days = $subscrip_days*$renewal_months;
			    $validity_date = date("Y-m-d", strtotime("+$subscrip_days days"));
			}
			$sql3 = " select GID_INSTALL_ID,GID_CONNECTPLUS_TOKEN,GID_NO_CLIENTS,GPM_FREE_EDITION from gft_install_dtl_new ".
			 		" join gft_product_master on (GID_LIC_PCODE=GPM_PRODUCT_CODE AND GID_LIC_PSKEW=GPM_PRODUCT_SKEW)".
					" where GID_LEAD_CODE='$lead_code' and GID_STATUS in ('A','S') and GID_LIC_PCODE='$product_code' ";
			$res3 = execute_my_query($sql3);
			if($is_saas_addon_pro>0){
			    $required_saas_addon_sync = true;
			    $saas_addon_product_code[] = $product_code;
			}
			if(mysqli_num_rows($res3)==0){ //insert new install entry
				$ins_arr['GID_EMP_ID'] = $ins_arr['GID_SALESEXE_ID'] = SALES_DUMMY_ID;
				$ins_arr['GID_ORDER_NO'] = $ins_arr['GID_LIC_ORDER_NO']	= $order_no;
				$ins_arr['GID_FULLFILLMENT_NO'] = $ins_arr['GID_LIC_FULLFILLMENT_NO'] = $fullfill_no;
				$ins_arr['GID_PRODUCT_CODE'] = $ins_arr['GID_LIC_PCODE'] = $ins_arr['GID_HEAD_OF_FAMILY'] = $product_code;
				$ins_arr['GID_PRODUCT_SKEW'] = $ins_arr['GID_LIC_PSKEW'] = $product_skew;
				$ins_arr['GID_CREATED_TIME'] = $ins_arr['GID_CREATED_TIME'] = date('Y-m-d H:i:s');
				$ins_arr['GID_INSTALL_DATE'] 	= $today;
				$ins_arr['GID_STATUS'] 		 	= 'A';
				$ins_arr['GID_LEAD_CODE'] 	 	= $lead_code;
				$ins_arr['GID_NO_CLIENTS'] 		= $client_qty;
				$ins_arr['GID_NO_COMPANYS']		= $company_qty;
				$ins_arr['GID_EXPIRE_FOR'] 		= $data1['GID_EXPIRE_FOR'];
				$free_date = get_single_value_from_single_table("GPM_FREE_TILL", "gft_product_family_master", "GPM_PRODUCT_CODE", $product_code);
				if( ($free_date!='') && ($free_date!='0000-00-00') && (strtotime($free_date) > strtotime($today)) ){
					$validity_date = $free_date;
				}
				$ins_arr['GID_VALIDITY_DATE']	= $validity_date;
				$install_id = array_insert_query("gft_install_dtl_new", $ins_arr);
				if($product_code=='705'){
				    $requried_mypulse_sync = true;
					continue;
				}else if(in_array($product_code,array('762','765'))){
					continue;
				}
				if($is_saas_addon_pro>0){
				    continue;
				}
				update_connectplus_token($lead_code, $install_id);
				$call_provisioning =true;
			}else if($row3 = mysqli_fetch_array($res3)){
			    $gid_install_id = $row3['GID_INSTALL_ID'];
			    $is_free_edition = $row3['GPM_FREE_EDITION'];
			    if($product_code=='705'){
			        $validity_query = ", GID_VALIDITY_DATE=if(GID_VALIDITY_DATE<'$today','$validity_date',DATE_ADD(GID_VALIDITY_DATE , INTERVAL $subscrip_days DAY))";
			        $update_field = " GID_NO_COMPANYS=(GID_NO_COMPANYS + $company_qty), GID_NO_CLIENTS=(GID_NO_CLIENTS + $client_qty)";
			        if($gft_skew_property=='26' || $is_free_edition=='Y'){//On renewal and trial to upgrade
			            $update_field = " GID_NO_COMPANYS='$company_qty', GID_NO_CLIENTS='$client_qty' $validity_query ";
			            if($is_free_edition=='Y'){
			                $update_field .= " , GID_LIC_PSKEW='$product_skew' ";
			            }
			        }
			        $up1 = " update gft_install_dtl_new set  $update_field where GID_INSTALL_ID='$gid_install_id'";
			        execute_my_query($up1);
			        $requried_mypulse_sync = true;
			        continue;
			    }else if(in_array($product_code, array('711','712'))){ //update renewals only
			        $client_qty = $row3['GID_NO_CLIENTS'];
			        $call_provisioning =true;
			    }
			}
			if($call_provisioning){
			    if(in_array($product_code, array('711','712','714'))){
			        $provision_url = $config_data['service_provision'];
			    }
			    $data_arr = /*. (string[string]) .*/array();
			    $data_arr['user'] = $lead_code;
			    $data_arr['product'] = $prod_alias;
			    $data_arr['parent'] = ($product_code=='734') ? "rpos6" : null;
			    $data_arr['bucket'] = "small";
			    $data_arr['base_schema'] = $base_schema;
			    $data_arr['product_version'] = $current_version;
			    $data_arr['customer_id'] = $lead_code;
			    $data_arr['customer_name'] = $glh_cust_name;
			    $data_arr['created_at']	 = date('Y-m-d H:i:s');
			    $data_arr['no_of_license'] = $client_qty;
			    if($product_code=='526'){
			        $data_arr['domain_name'] = get_single_value_from_single_query("GCD_DOMAIN", "select GCD_DOMAIN from gft_connectplus_domains where GCD_LEAD_CODE='$lead_code' and GCD_PRODUCT_CODE='$product_code'");
			    }
			    $post_data	= json_encode($data_arr);
			    $header_arr = array("Content-Type: application/json","x-api-key: $provision_api_key");
			    $ch = curl_init();
			    curl_setopt($ch, CURLOPT_URL, $provision_url);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			    curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
			    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			    $resp_arr['response_json'] = (string)curl_exec($ch);
			    $resp_arr['response_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			    $curl_error = curl_error($ch);
			    if($curl_error!=''){
			        $resp_arr['curl_error'] = $curl_error;
			    }
			    //log
			    $insert_arr['GLC_ONLINE_CONTENT'] = $post_data;
			    $insert_arr['GLC_RETURN_DATA'] = json_encode($resp_arr);
			    $insert_arr['GLC_REQUEST_TIME'] = date('Y-m-d H:i:s');
			    $insert_arr['GLC_LEAD_CODE'] = $lead_code;
			    $insert_arr['GLC_REQUEST_PURPOSE_ID'] = 43;
			    array_insert_query("gft_lic_request", $insert_arr);
			    curl_close($ch);
			}
			update_feature_install_dtl($lead_code,$product_code,$product_skew,$client_qty);
			if($product_code=='734'){
			    $post_body = json_encode(array('customer_id'=>$lead_code,'customer_name'=>$glh_cust_name,'business_vertical'=>$vertical_name));
			    $harr = array("DEMO-REGISTRATION-ACCESS-KEY: ".$config_data['peekaboo_key'],"Content-Type: application/json");
			    $dh = curl_init($config_data['peekaboo_url']);
			    curl_setopt($dh, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($dh, CURLOPT_SSL_VERIFYPEER, false);
			    curl_setopt($dh, CURLOPT_CUSTOMREQUEST, "POST");
			    curl_setopt($dh, CURLOPT_POSTFIELDS, $post_body);
			    curl_setopt($dh, CURLOPT_HTTPHEADER, $harr);
			    $resp_arr['response_json'] = (string)curl_exec($dh);
			    $resp_arr['response_code'] = curl_getinfo($dh, CURLINFO_HTTP_CODE);
			    $curl_error = curl_error($dh);
			    curl_close($dh);
			    if($curl_error!=''){
			        $resp_arr['curl_error'] = $curl_error;
			    }
			    $insert_arr = array(
			        'GLC_ONLINE_CONTENT'=>$post_body,'GLC_RETURN_DATA'=>json_encode($resp_arr),
			        'GLC_REQUEST_TIME'=>date('Y-m-d H:i:s'),'GLC_LEAD_CODE'=>$lead_code,'GLC_REQUEST_PURPOSE_ID'=>'43'
			    );
			    array_insert_query("gft_lic_request", $insert_arr);
			}
			if(in_array($product_code.$product_skew, array('70701.0PLST','52601.0PRSSTT'))){
			    execute_my_query("update gft_lead_hdr_ext set GLE_USER_SYNC_TYPE='STANDALONE' where GLE_LEAD_CODE='$lead_code'");
			}
		}else if($product_code=='717'){
			zepogatewaycall($order_no, $lead_code, $product_code,$fullfill_no);
		}else if($product_code=='706'){
		    $gosecure_dtl[] = array($product_code,$product_skew,$client_qty);
		    $gosecure_lead_code = $lead_code;
		}else if($product_code=='744'){
		    post_order_split_info_to_integration_portal($lead_code);
		}
		account_posting_to_integration_portal($lead_code,$product_code);
	}
	if($requried_mypulse_sync){
	    smsgatewaycall($order_no, $lead_code);
	}	
	if($required_saas_addon_sync){
	    send_addon_product_dtl_to_saas_server($lead_code, $order_no,$saas_addon_product_code);
	}
	if($gosecure_lead_code!=""){
	    update_gosecure_addon_plan_dtl($order_no, $gosecure_lead_code);
	    foreach ($gosecure_dtl as $arr){
	        update_for_addon($gosecure_lead_code, $arr[0], $arr[1], $arr[2]);
	    }
	}
}
/**
 * @param int $lead_code
 * @param int $product_code
 * @param string $return_type
 * @param boolean $check_validity
 *
 * @return mixed
 */
function is_product_installed($lead_code, $product_code, $return_type='',$check_validity=false){
	$current_date = date("Y-m-d");
    $whr = ($check_validity==true ? " and GID_VALIDITY_DATE>='$current_date'" : "");
    $que1 = " select GID_INSTALL_ID,GID_VALIDITY_DATE,GID_CONNECTPLUS_TOKEN from gft_install_dtl_new where GID_LEAD_CODE='$lead_code' ".
			" and GID_LIC_PCODE='$product_code' and GID_STATUS!='U' $whr ";
	$res1 = execute_my_query($que1);
	if($row1 = mysqli_fetch_array($res1)){
		if($return_type=='detail'){
			$ret_arr = array(
					'GID_INSTALL_ID'=>$row1['GID_INSTALL_ID'],
					'GID_VALIDITY_DATE'=>$row1['GID_VALIDITY_DATE'],
			        'GID_CONNECTPLUS_TOKEN'=>$row1['GID_CONNECTPLUS_TOKEN']
			);
			return $ret_arr;
		}
		return true;
	}
	return false;
}
/**
 * @param int $lead_code
 * @param int $product_code
 * @param string $purpose
 * @param string $use_token
 * 
 * @return boolean
 */
function check_for_connectplus_provisioning($lead_code,$product_code,$purpose='',$use_token=''){
	if(!is_product_installed($lead_code, $product_code)){ //backup and restore
		$gop_sql=" select GOD_ORDER_NO,GOD_CREATED_DATE from gft_order_hdr ".
				 " join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) ".
				 " where GOD_LEAD_CODE='$lead_code' and GOD_ORDER_STATUS='A' and GOP_PRODUCT_CODE='$product_code' ";
		$gco_sql=" select GOD_ORDER_NO,GOD_CREATED_DATE from gft_order_hdr ".
				 " join gft_cp_order_dtl on (GOD_ORDER_NO=GCO_ORDER_NO) ".
				 " where GOD_LEAD_CODE='$lead_code' and GOD_ORDER_STATUS='A' and GCO_PRODUCT_CODE='$product_code' ";
		$sql1 = "select GOD_ORDER_NO,GOD_CREATED_DATE from ($gop_sql union all $gco_sql) t1 order by GOD_CREATED_DATE ";
		$res1 = execute_my_query($sql1);
		if($row1 = mysqli_fetch_array($res1)){
			if($purpose=='do_provision'){
				provision_connectplus($row1['GOD_ORDER_NO'],$use_token);
			}
			return true;
		}
	}
	return false;
}

/**
 * @param string $order_no
 * @param string $lead_code
 * 
 * @return void
 */
function update_provisioning_order_dtl($order_no,$lead_code){
	$sql1 = " select GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GOP_QTY,GPM_ORDER_TYPE,GPM_ADDON_CATEGORY,GOP_FULLFILLMENT_NO,GFT_SKEW_PROPERTY ".
			" from gft_order_product_dtl join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE) ".
			" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			" where GOP_ORDER_NO='$order_no' ";
	$res1 = execute_my_query($sql1);
	$SaaS_gateway_recharge=false;
	$SaaS_pcode = $SaaS_pskew = $SaaS_property = /*. (string[int]) .*/array();
	while ($row1 = mysqli_fetch_array($res1)){
		$product_code 	= $row1['GOP_PRODUCT_CODE'];
		$product_skew 	= $row1['GOP_PRODUCT_SKEW'];
		$gop_qty 		= $row1['GOP_QTY'];
		$addon_category	= $row1['GPM_ADDON_CATEGORY'];
		$fullfill_no	= $row1['GOP_FULLFILLMENT_NO'];
		$skew_property  = $row1['GFT_SKEW_PROPERTY'];
		if($row1['GPM_ORDER_TYPE']=='1'){ //user based
			if(update_for_addon($lead_code, $product_code, $product_skew, $gop_qty)){
				$used_update = " update gft_order_product_dtl set GOP_USEDQTY=$gop_qty where GOP_ORDER_NO='$order_no' ".
						" and GOP_PRODUCT_CODE='$product_code' and GOP_PRODUCT_SKEW='$product_skew'";
				execute_my_query($used_update);
			}
		}
		if(in_array($addon_category,array('1','2'))){
			zepogatewaycall($order_no,$lead_code,$product_code,$fullfill_no);
		}
		if(in_array($skew_property, array(17,18,19,20,21,22))){
			$SaaS_gateway_recharge=true;
			$SaaS_pcode[]	= $product_code;
			$SaaS_pskew[]	= $product_skew;
			$SaaS_property[]= $skew_property;
		}
	}
	$alert_branding	=	false;
	$alert_send_flag=	true;
	if($SaaS_gateway_recharge){
		if(in_array('19', $SaaS_property)){
			$alert_branding	=	true;
		}
	}
	for($i=0;$i<count($SaaS_pcode);$i++){
		if($SaaS_pcode[$i]!=804 and $SaaS_property[$i]!=19){
			saas_module_update(array($SaaS_pcode[$i]),$lead_code,$order_no,'',1,$alert_branding);
			if($SaaS_pcode[$i]=='604'){
				$alert_send_flag=false;
			}
		}
	}
	if($alert_branding and $alert_send_flag){
		for($i=0;$i<count($SaaS_pcode);$i++){
			if($SaaS_property[$i]=='19' and $SaaS_pcode[$i]=='604'){
				smsgatewaycall_for_branding($order_no,$lead_code,$SaaS_pcode[$i],$SaaS_pskew[$i]);
			}
		}
	}
	provision_connectplus($order_no);
	update_dealer_dependency($order_no);
	generate_pos_notification($order_no);
}

/**
 * @param string[string] $order_hdr_arr
 * @param string[int] $gop_product_code_arr
 * @param string[int] $gop_product_skew_arr
 * @param string[int] $gop_qty_arr
 * @param string[int] $amount_arr
 * @param string $proforma_no
 * @param string $quotation_no
 * @param string $gid_install_id
 * @param string[int] $gop_reference_no_arr  
 * @param string[int] $used_qty_arr
 * @param boolean $reverse_calc
 * @param string $purpose
 * @param string $lead_code
 * @param string $order_no
 * @param float $order_amt
 * @param string[int] $order_list_price_arr
 * @return string
 */
function array_insert_new_order($order_hdr_arr,$gop_product_code_arr,$gop_product_skew_arr,$gop_qty_arr,$amount_arr,
						$proforma_no='',$quotation_no='',$gid_install_id='',$gop_reference_no_arr=null,$used_qty_arr=null,$reverse_calc=false,
    $purpose='',$lead_code='', $order_no='',$order_amt=0.0,$order_list_price_arr=null){
	$order_hdr_arr = array_change_key_case($order_hdr_arr,CASE_UPPER);
	$GOD_ORDER_NO = isset($order_hdr_arr['GOD_ORDER_NO'])?$order_hdr_arr['GOD_ORDER_NO']:'';
	$GOD_LEAD_CODE = isset($order_hdr_arr['GOD_LEAD_CODE'])?$order_hdr_arr['GOD_LEAD_CODE']:'';
	$datetime = date('Y-m-d H:i:s');
	if(!isset($order_hdr_arr['GOD_UPDATED_TIME'])) 	$order_hdr_arr['GOD_UPDATED_TIME'] = $datetime;
	if(!isset($order_hdr_arr['GOD_CREATED_DATE'])) 	$order_hdr_arr['GOD_CREATED_DATE'] = $datetime;
	if(!isset($order_hdr_arr['GOD_ORDER_DATE'])) 	$order_hdr_arr['GOD_ORDER_DATE'] = $datetime;
	if(!isset($order_hdr_arr['GOD_TAX_MODE']))	 	$order_hdr_arr['GOD_TAX_MODE'] = '4';
	if(!isset($order_hdr_arr['GOD_PAYMENT_CODE'])) 	$order_hdr_arr['GOD_PAYMENT_CODE'] = '1';
	if(!isset($order_hdr_arr['GOD_ORDER_STATUS']))  $order_hdr_arr['GOD_ORDER_STATUS'] = 'A';
	if(!isset($order_hdr_arr['GOD_EMP_ID']))        $order_hdr_arr['GOD_EMP_ID'] = SALES_DUMMY_ID;
	if(!isset($order_hdr_arr['GOD_INCHARGE_EMP_ID']))       $order_hdr_arr['GOD_INCHARGE_EMP_ID'] = SALES_DUMMY_ID;
	if(!isset($order_hdr_arr['GOD_ORDER_APPROVAL_STATUS'])) $order_hdr_arr['GOD_ORDER_APPROVAL_STATUS'] = '2';
	if( (strlen($GOD_ORDER_NO)!=15) || (strlen($GOD_LEAD_CODE) < 5) ){
		return '';
	}
	$res = array_update_tables_common($order_hdr_arr, "gft_order_hdr", null, null, SALES_DUMMY_ID,null,null,$order_hdr_arr);
	if($res){
		$rs_product_dtl = insert_stmt_for_order_product_dtl($GOD_ORDER_NO,$GOD_LEAD_CODE,$gop_product_code_arr,$gop_product_skew_arr,
		    $gop_qty_arr,$amount_arr,false,$order_list_price_arr,$gop_reference_no_arr,$used_qty_arr,$reverse_calc);
		if($rs_product_dtl){
			if($purpose!='saas_order_submit') {
				update_dependent_order_dtl($GOD_ORDER_NO,$GOD_LEAD_CODE,$proforma_no,$gid_install_id);
				update_provisioning_order_dtl($GOD_ORDER_NO,$GOD_LEAD_CODE);
			} else {
				deduct_from_advance($lead_code, $order_no, /*. (float) .*/$order_amt);
				update_collection_in_hdr($GOD_ORDER_NO);
				update_cust_bal($GOD_LEAD_CODE);
			}
			if($proforma_no!=''){
				execute_my_query("update gft_proforma_hdr set GPH_CONVERTED_ORDER_NO='$GOD_ORDER_NO',GPH_ORDER_STATUS='P',GPH_STATUS='P' WHERE GPH_ORDER_NO='$proforma_no'");
			}
			if($quotation_no!=''){
				execute_my_query("update gft_quotation_hdr set GQH_CONVERTED_ORDER_NO='$GOD_ORDER_NO',GQH_APPROVAL_STATUS='4' WHERE GQH_ORDER_NO='$quotation_no'");
			}
			update_call_preferance($GOD_LEAD_CODE);
			return $GOD_ORDER_NO;
		}
	}
	return '';
}

/**
 * @param string $transaction_id
 * @param string $net_amt
 * @param string $jio_ref_no
 * 
 * @return string[int]
 */
function complete_transaction($transaction_id,$net_amt,$jio_ref_no){
	$ret_arr = /*. (string[int]) .*/array();
	$ret_arr[0] = 'failure';
	$check_que = " select GPT_LEAD_CODE,GPT_ORDER_TYPE,GPT_ORDER_REF_ID,GPT_NET_AMT from gft_payment_transaction_log where GPT_TRANS_ID='$transaction_id' ";
	$check_res = execute_my_query($check_que);
	if($row1 = mysqli_fetch_array($check_res)){
		$lead_code		= $row1['GPT_LEAD_CODE'];
		$order_type 	= $row1['GPT_ORDER_TYPE'];
		$order_ref_id 	= $row1['GPT_ORDER_REF_ID'];
		$trans_net_amt	= $row1['GPT_NET_AMT'];
		if((int)$trans_net_amt!=(int)$net_amt){
			$ret_arr[1] = 'Error: Payment Details Mismatch';
			return $ret_arr;
		}
	}else{
		$ret_arr[1] = 'Unknown Transaction';
		return $ret_arr;
	}
	$quotation_no = "";
	$proforma_no = "";
	if($order_type=='1'){
		$sel_query =" select GQH_EMP_ID as emp,GQP_PRODUCT_CODE pcode,GQP_PRODUCT_SKEW pskew,GQP_QTY qty,GQP_SELL_RATE sell_rate,GQH_ORDER_AMT order_amt " .
					" from gft_quotation_hdr join gft_quotation_product_dtl on (GQP_ORDER_NO=GQH_ORDER_NO) " . 
					" join gft_product_master on (GQP_PRODUCT_CODE=GPM_PRODUCT_CODE and GQP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
					" where GQH_ORDER_NO='$order_ref_id' ";
		$quotation_no = $order_ref_id;
	}else if($order_type=='2'){
		$sel_query =" select GPH_EMP_ID as emp,GPP_PRODUCT_CODE pcode,GPP_PRODUCT_SKEW pskew,GPP_QTY qty,GPP_SELL_RATE sell_rate,GPH_ORDER_AMT order_amt " .
				" from gft_proforma_hdr join gft_proforma_product_dtl on (GPP_ORDER_NO=GPH_ORDER_NO) " .
				" join gft_product_master on (GPP_PRODUCT_CODE=GPM_PRODUCT_CODE and GPP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
				" where GPH_ORDER_NO='$order_ref_id' ";
		$proforma_no = $order_ref_id;
	}else{
		$ret_arr[1]='Invalid Type';
		return $ret_arr;
	}
	
	$sel_res = execute_my_query($sel_query);
	if(mysqli_num_rows($sel_res)==0){
		$ret_arr[1] = 'Error: Record Not Found';
		return $ret_arr;
	}
	$emply_id = $order_amt = '';
	$gop_product_code_arr = $gop_product_skew_arr = $gop_qty_arr = $amount_arr = /*. (string[int]) .*/array();
	while($row2 = mysqli_fetch_array($sel_res)){
		$emply_id	= $row2['emp'];
		$order_amt	= $row2['order_amt'];
		$gop_product_code_arr[]	= $row2['pcode'];
		$gop_product_skew_arr[]	= $row2['pskew'];
		$gop_qty_arr[] 			= $row2['qty'];
		$amount_arr[] 			= $row2['sell_rate'];
	}
	
	//Order Entry start
	$order_hdr_arr = /*. (string[string]) .*/array();
	$sql_lead = "select GLH_LEAD_CODE,GLH_TERRITORY_ID,GLH_PAID_CAMPAIGN,GLH_ORGANIC_SEARCH_VISIT from gft_lead_hdr where GLH_LEAD_CODE='$lead_code' ";
	$res_lead = execute_my_query($sql_lead);
	if($row3 = mysqli_fetch_array($res_lead)){
		$territory_id 	= $row3['GLH_TERRITORY_ID'];
		$order_hdr_arr['GOD_PAID_CAMPAIGN'] 		= $row3['GLH_PAID_CAMPAIGN'];
		$order_hdr_arr['GOD_ORGANIC_SEARCH_VISIT'] 	= $row3['GLH_ORGANIC_SEARCH_VISIT'];
	}else{
		$ret_arr[1] = 'Error: Lead Code Not Found';
		return $ret_arr;
	}
	
	$datetime = date('Y-m-d H:i:s');
	$order_hdr_arr['GOD_LEAD_CODE'] 	= $lead_code;
	$order_hdr_arr['GOD_EMP_ID'] 		= $emply_id;
	$order_hdr_arr['GOD_STORE_EMP'] 	= $emply_id;
	$order_hdr_arr['GOD_INCHARGE_EMP_ID']= $emply_id;
	$order_hdr_arr['GOD_ORDER_STATUS'] 	= 'A';
	$order_hdr_arr['GOD_ORDER_DATE'] 	= date('Y-m-d');
	$order_hdr_arr['GOD_ORDER_AMT'] 	= $order_amt;
	$order_hdr_arr['GOD_COLLECTED_AMT'] = '0';
	$order_hdr_arr['GOD_BALANCE_AMT'] 	= $net_amt;
	$order_hdr_arr['GOD_UPDATED_TIME'] 	= $datetime;
	$order_hdr_arr['GOD_CREATED_DATE'] 	= $datetime;
	$order_hdr_arr['GOD_ORDER_SPLICT']	= '0'; // need to redefine
	$order_hdr_arr['GOD_ORDER_APPROVAL_STATUS'] = '2';
	$order_hdr_arr['GOD_TAX_MODE'] 		= '4'; //GST
	$order_hdr_arr['GOD_PAYMENT_CODE'] 	= '1'; //100% Advance payment
	$order_hdr_arr['GOD_ORDER_NO'] 		= /*.(string).*/get_order_no("", date('y'), $territory_id, $emply_id);
	
	$order_no = array_insert_new_order($order_hdr_arr, $gop_product_code_arr, $gop_product_skew_arr, $gop_qty_arr, $amount_arr,$proforma_no,$quotation_no);
	if($order_no==''){
		$ret_arr[1] = 'Technical Error Occured in Order Entry. Try after sometime';
		return $ret_arr;
	}
	
	//Collection Entry --- Start
	$today_date = date('Y-m-d H:i:s');
	$receipt_arr['GRD_RECEIPT_ID'] 		= "";
	$receipt_arr['GRD_DATE'] 			= $today_date;
	$receipt_arr['GRD_EMP_ID'] 			= $emply_id;
	$receipt_arr['GRD_LEAD_CODE'] 		= $lead_code;
	$receipt_arr['GRD_RECEIPT_TYPE']	= "14"; //JIO
	$receipt_arr['GRD_RECEIPT_AMT'] 	= $net_amt;
	$receipt_arr['GRD_CHEQUE_DD_NO']	= $jio_ref_no;
	$receipt_arr['GRD_STATUS'] 			= "D"; //Deposited in Bank
	$receipt_arr['GRD_CHEQUE_DD_DATE'] 	= $today_date;
	$receipt_arr['GRD_DEPOSITED_DATE'] 	= $today_date;
	$receipt_arr['GRD_REPORTED_DATE'] 	= $today_date;
	$receipt_arr['GRD_COLLECTION_DATE'] = $today_date;
	$receipt_arr['GRD_CHECKED_WITH_LEDGER'] 		= "N";
	$receipt_arr['GRD_TERMS_REGARDING_COLLECTION'] 	= "From myGoFrugal App";
	
	$receipt_id = array_insert_query("gft_receipt_dtl", $receipt_arr);
	if($receipt_id==0){
		$ret_arr[1] = 'Technical Error Occured in Receipt Entry. Try after sometime';
		return $ret_arr;
	}
	
	$collection_arr['GCR_ORDER_NO'] 	= "$order_no";
	$collection_arr['GCR_RECEIPT_ID'] 	= "$receipt_id";
	$collection_arr['GCR_PAYMENT_FORID']= "d";
	$collection_arr['GCR_AMOUNT'] 		= "$net_amt";
	$collection_arr['GCR_REASON'] 		= "1";
	array_insert_query("gft_collection_receipt_dtl", $collection_arr);
	update_collection_in_hdr($order_no);
	$ret_arr[0] = 'success';
	$ret_arr[1] = $order_no;
	generate_accounts_invoice($order_no, 'customer', 'order_submit');
	return $ret_arr;
}

/**
 * @param string $numb
 *
 * @return string
 */
function moneyFormatIndia($numb){
	$num_array 	= explode('.',$numb);
	$num 		= $num_array[0];
	$thecash 	= $num;
	$explrestunits = "";
	if(strlen($num)>3){
		$lastthree = substr($num,strlen($num)-3,strlen($num));
		$restunits = substr($num,0,strlen($num)-3); // last three digits
		$restunits = (strlen($restunits)%2==1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
		$expunit   = str_split($restunits,2);
		for($i=0;$i<sizeof($expunit);$i++){
			if($i==0){
				$explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
			}else{
				$explrestunits .= $expunit[$i].",";
			}
		}
		$thecash = $explrestunits . $lastthree;
	}
	if(isset($num_array[1])){
		$thecash .= "." . $num_array [1];
	}
	return $thecash;
}

/**
 * @param string $proforma_no
 * @param string $approval_stat
 * @param string $reason
 * 
 * @return void
 */
function update_proforma_approval_status($proforma_no,$approval_stat,$reason){
	global $uid,$attach_path;
	$addt_upd = "";
	if($approval_stat==2){
		$que=" select GPH_EMP_ID,GPH_LEAD_CODE,GPH_TYPE,GLH_CUST_NAME,GPH_PROFORMA_TO_EMAILS,GPH_CURRENCY_CODE, GPH_ORDER_AMT from gft_proforma_hdr ".
			 " join gft_lead_hdr on (GLH_LEAD_CODE=GPH_LEAD_CODE) ".
			 " where GPH_ORDER_NO='$proforma_no' and GPH_MAIL_STATUS=1";
		$res = execute_my_query($que);
		if($row1 = mysqli_fetch_array($res)){
			$GPH_EMP_ID = $row1['GPH_EMP_ID'];
			$cust_email_arr = customerContactDetail($row1['GPH_LEAD_CODE']);
			$proforma_to_email_str=$row1['GPH_PROFORMA_TO_EMAILS'];
			$currency_type = $row1['GPH_CURRENCY_CODE'];
			$total_amount =  $row1['GPH_ORDER_AMT'];
			send_quotation_mail_to_customer($GPH_EMP_ID, $row1['GPH_LEAD_CODE'], $currency_type,"Pro_$proforma_no.pdf", $row1['GLH_CUST_NAME'],
			    $proforma_no,$cust_email_arr['COUNTRY_NAME'], $total_amount, $cust_email_arr['EMAIL'], $proforma_to_email_str, "proforma",$row1['GPH_TYPE']);
			
			$addt_upd = " ,GPH_MAIL_STATUS=2 ";
		}
	}
	$update1 =	" update gft_proforma_hdr set GPH_APPROVAL_STATUS='$approval_stat', GPH_APPROVEDBY_EMPID='$uid', ".
				" GPH_APPROVAL_REMARKS='$reason' $addt_upd where GPH_ORDER_NO='$proforma_no' ";
	execute_my_query($update1);
}

/**
 * @param string $cust_id
 * @param string[int][int] $table_header_arr
 * 
 * @return void
 */
function show_installer_status_history($cust_id,$table_header_arr=null){
	$query=" select GLH_LEAD_CODE, GIH_ID, GLH_CUST_NAME, concat(GPM_PRODUCT_ABR,' ',gpv_version_family) as product_name, ".
			" GSM_NAME , GID_REMARKS, GIH_CREATED_DATE, GIH_UPDATE_DATE, TIMESTAMPDIFF(SECOND,GIH_CREATED_DATE,GIH_UPDATE_DATE) as diff, GID_IP_ADDRESS ".
			" from gft_installer_info_hdr ".
			" join gft_installer_info_dtl on (GIH_LAST_DTL_ID=GID_ID) ".
			" join gft_lead_hdr lh on (GIH_LEAD_CODE=GLH_LEAD_CODE) ".
			" join gft_installation_status_master on (GSM_ID=GIH_CURRENT_STATE) ".
			" left join gft_product_version_master on (GIH_PRODUCT_VERSION=GPV_VERSION) ".
			" left join gft_product_family_master on (GPM_PRODUCT_CODE=GPV_PRODUCT_CODE) ".
			" where GIH_ACCESS_TYPE=1 and GLH_LEAD_CODE='$cust_id' ";
	$myarr=array("S.No","Customer ID","Customer Name","Current Status", "Remarks" ,"Product Name", "Created On", "Last Updated On","Total Time Taken","IP Address");
	$mysort=array("","GLH_LEAD_CODE","GLH_CUST_NAME","GSM_NAME","GID_REMARKS","product_name","GIH_CREATED_DATE","GIH_UPDATE_DATE","diff","GID_IP_ADDRESS");
	$order_by=" GIH_UPDATE_DATE ";
	generate_reports(null,$query,$myarr,$mysort,null,null,null,null,null,null,null,null,null,null,null,
			null,null,null,null,false,$navigation=false,$order_by,$heading=true,
			null,true,0,null,null,null,null,0,$table_header_arr);
}

/**
 * @param string $empl_id
 * @param string $email_id
 * @param string $name
 * @param int $role_id
 * @param int $rep_to
 * @param string $status
 * @param string $uname
 * @param string $emp_hr_id
 *
 * @return void
 */
function send_status_change_mail_to_sysadmin($empl_id,$email_id,$name,$role_id,$rep_to,$status,$uname=null,$emp_hr_id=""){
	if($role_id!=0) {
		$role_name= get_two_dimensinal_array_from_query('select GRM_ROLE_ID, GRM_ROLE_DESC from gft_role_master where GRM_ROLE_ID='.$role_id,'GRM_ROLE_ID', 'GRM_ROLE_DESC');
	}
	if($rep_to!=0) {
		$rmanager_name=get_name((string)$rep_to);
	}
	$role=isset($role_name[0][1])?$role_name[0][1]:'';
	$db_mail_content_config=/*. (string[string][int]) .*/ array();
	$db_mail_content_config['Employee_Status'][0]=($status=='A')?'ACTIVE':'INACTIVE';
	$db_mail_content_config['Employee_Id'][0]=(string)$empl_id;
	$db_mail_content_config['HR_ID'][0]=(string)$emp_hr_id;
	$db_mail_content_config['Employee_Name'][0]=$name;
	$db_mail_content_config['Role'][0]=$role;
	$db_mail_content_config['Reporting_Manager'][0]=(isset($rmanager_name)?$rmanager_name:'');
	$db_mail_content_config['Email'][0]=$email_id;
	$db_mail_content_config['ex_role_name'][0]=$uname;
	send_formatted_mail_content($db_mail_content_config,26,164,null,null);
}

/**
 * @param string $emp_id
 * 
 * @return int
 */
function get_lead_created_category($emp_id){
	$GLH_CREATED_CATEGORY = 0;
	if(is_authorized_group_list($emp_id ,array(5))){
		$GLH_CREATED_CATEGORY=1; // Sales Team
	}else if(is_authorized_group_list($emp_id ,array(70))){
		$GLH_CREATED_CATEGORY=44; // PC
	}else if(is_authorized_group_list($emp_id,array(13,31,39))){
		$GLH_CREATED_CATEGORY=3;//partner
	}else if(is_authorized_group_list($emp_id,array(14))){
		$GLH_CREATED_CATEGORY=24;//corporate
	}else if(is_authorized_group_list($emp_id ,array(27)) and !is_authorized_group_list($emp_id ,array(100))){
		$GLH_CREATED_CATEGORY=29; // presales
	}else if(is_authorized_group_list($emp_id ,array(54)) and !is_authorized_group_list($emp_id ,array(99))){
		$GLH_CREATED_CATEGORY=7; // Annuity
	}
	return $GLH_CREATED_CATEGORY;
}
/**
 * @param string $all_leads
 *
 * @return string
 */
function query_to_get_customer_installation_dtl($all_leads){
	$vers_check = " (GID_CURRENT_VERSION=GPV_VERSION or GID_CURRENT_VERSION=REPLACE(concat(GPV_MAJOR_VERSION,GPV_MINOR_VERSION,GPV_PATCH_VERSION,GPV_EXE_VERSION),'_','.') or GPV_VERSION=concat('3.0.0.RC',GID_CURRENT_VERSION)) ";
	$sql_installed_dtl	=	" select GID_VALIDITY_DATE, GID_LEAD_CODE,GID_LIC_PCODE,GID_LIC_PSKEW,CONCAT(GID_LIC_PCODE,'-',SUBSTRING(GID_LIC_PSKEW,1,4)) PCODE_SKEW,GPM_IS_BASE_PRODUCT, ".
			" GLH_LEAD_CODE,GLH_MAIN_PRODUCT,if(GID_PRODUCT_CODE='500',concat(GPM_PRODUCT_ABR,'-',substring(GID_PRODUCT_SKEW,1,4)),GPM_PRODUCT_ABR) GPM_PRODUCT_ABR,GID_CURRENT_VERSION, ".
			" GPV_VERSION,GPV_WELCOME_CONTENT,GID_STATUS,GID_LIC_PCODE,GPM_LICENSE_TYPE,GPM_FREE_EDITION from gft_install_dtl_new ".
			" INNER JOIN gft_product_master pm ON(pm.GPM_PRODUCT_CODE=GID_LIC_PCODE AND pm.GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
			" INNER JOIN gft_product_family_master gfm ON(gfm.GPM_PRODUCT_CODE=GID_LIC_PCODE )".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GID_LEAD_CODE) ".
			" left join gft_product_version_master on (GID_STATUS='A' AND GPV_PRODUCT_CODE=GID_LIC_PCODE and GPM_LICENSE_TYPE!=3 and $vers_check )".
			" where GID_LEAD_CODE IN($all_leads) ORDER BY GID_STATUS ";
	//santu: No need to check FREE_EDITION -
	// AND pm.GPM_FREE_EDITION='N' ";
	return $sql_installed_dtl;
}
/**
 * @param string $contact_no
 * @param string $email
 *
 * @return string[string]
 */
function get_employee_info_from_contact($contact_no='',$email=''){
	$ret_arr = /*. (string[string]) .*/array();
	$emp_wh_cond_mob1 = $emp_wh_cond_mob2=$email_condition=$contact_condition ='';
	if($contact_no!=''){
	$emp_wh_cond_mob1 = getContactDtlWhereCondition("GEM_MOBILE", $contact_no);
	$emp_wh_cond_mob2 = getContactDtlWhereCondition("GEM_RELIANCE_NO", $contact_no);
	$contact_condition = " and ($emp_wh_cond_mob1 or $emp_wh_cond_mob2)";
	}else if($email!=''){
		$email_condition=" and GEM_EMAIL='$email' ";
	}else{
	    return $ret_arr;
	}
	$sel_emp_query =" SELECT GEM_EMP_ID,GEM_EMP_NAME,GEM_LEAD_CODE,GEM_EMAIL from gft_emp_master WHERE  GEM_OFFICE_EMPID > 0 and GEM_STATUS='A' AND GEM_EMP_ID!='9999' ".
			" $contact_condition $email_condition";
	$res_emp = execute_my_query($sel_emp_query);
	if($emp_data = mysqli_fetch_array($res_emp)){
		$ret_arr['GEM_EMP_ID']	 = $emp_data['GEM_EMP_ID'];
		$ret_arr['GEM_EMP_NAME'] = $emp_data['GEM_EMP_NAME'];
		$ret_arr['GEM_LEAD_CODE']= $emp_data['GEM_LEAD_CODE'];
		$ret_arr['GEM_EMAIL']	= $emp_data['GEM_EMAIL'];
	}
	return $ret_arr;
}

/**
 * @param string $proforma_no
 * 
 * @return void
 */
function check_and_update_tax_changes($proforma_no){
	$tax_query =" select GPP_ORDER_NO from gft_proforma_product_dtl join gft_proforma_hdr on (GPH_ORDER_NO=GPP_ORDER_NO) ".
			" join gft_product_master on (GPP_PRODUCT_CODE=GPM_PRODUCT_CODE and GPP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" where GPP_ORDER_NO='$proforma_no' and GPH_CURRENCY_CODE='INR' and GPP_SER_TAX_RATE!=GPM_SERVISE_TAX_PERC ";
	$tax_result = execute_my_query($tax_query);
	if(mysqli_num_rows($tax_result) > 0){
		$upd_query	=" update gft_proforma_hdr g1 inner join gft_proforma_product_dtl g2 on(g1.GPH_ORDER_NO=g2.GPP_ORDER_NO) ".
				" inner join gft_product_master pm on(g2.GPP_PRODUCT_CODE=pm.GPM_PRODUCT_CODE AND g2.GPP_PRODUCT_SKEW=pm.GPM_PRODUCT_SKEW ) ".
				" SET g2.GPP_LIST_PRICE=GPM_LIST_PRICE,g2.GPP_SER_TAX_RATE=GPM_SERVISE_TAX_PERC, ".
				" g2.GPP_SELL_RATE=(((GPP_SELL_AMT*100)/(100+GPM_SERVISE_TAX_PERC+GPM_TAX_PERC))/GPP_QTY), ".
				" g2.GPP_SER_TAX_AMT=(((((GPP_SELL_AMT*100)/(100+GPM_SERVISE_TAX_PERC+GPM_TAX_PERC))/GPP_QTY)*GPP_QTY*GPM_SERVISE_TAX_PERC)/100), ".
				" g2.GPP_TAX_AMT=(((((GPP_SELL_AMT*100)/(100+GPM_SERVISE_TAX_PERC+GPM_TAX_PERC))/GPP_QTY)*GPP_QTY*GPM_TAX_PERC)/100), ".
				" g2.GPP_DISCOUNT_AMT=(GPM_LIST_PRICE-((((GPP_SELL_AMT*100)/(100+GPM_SERVISE_TAX_PERC+GPM_TAX_PERC))))) ".
				" where GPH_ORDER_NO='$proforma_no' ";
		execute_my_query($upd_query);
	}
}

/**
 * @param string $pcode
 * @param string $pgroup
 * @param string $version
 *
 * @return string
 */
function get_version_entered_date($pcode,$pgroup,$version){
	$que1 = " select GPV_ENTERED_ON from gft_product_version_master where gpv_product_code='$pcode' and gpv_version_family='$pgroup' ".
			" and (GPV_VERSION='$version' or concat(GPV_MAJOR_VERSION,GPV_MINOR_VERSION,GPV_PATCH_VERSION,GPV_EXE_VERSION)='$version') ";
	$res1 = execute_my_query($que1);
	if($data1 = mysqli_fetch_array($res1)){
		return $data1['GPV_ENTERED_ON'];
	}
	return '';
}

/**
 * @param string $lead_code
 * @param string $employee_id
 * @param string $next_action
 * @param string $action_date
 * @param boolean $check_status
 *
 * @return boolean
 */
function is_next_action_exits($lead_code,$employee_id,$next_action,$action_date,$check_status=true){
	$query =" select GLD_ACTIVITY_ID from gft_activity where GLD_LEAD_CODE='$lead_code' and GLD_EMP_ID='$employee_id' ".
			" and GLD_NEXT_ACTION='$next_action' and GLD_NEXT_ACTION_DATE='$action_date' ";
	if($check_status){
		$query .= " and GLD_SCHEDULE_STATUS in (1,3) ";
	}
	$res = execute_my_query($query);
	if(mysqli_num_rows($res) > 0){
		return true;
	}
	return false;
}
/**
 * @param string $lead_code
 * @param string $employee_id
 * @param string $next_action
 * @param string $action_date
 * @param boolean $check_status
 *
 * @return int
 */
function get_existing_next_action_id($lead_code,$employee_id,$next_action,$action_date,$check_status=true){
    $query =" select GLD_ACTIVITY_ID from gft_activity where GLD_LEAD_CODE='$lead_code' and GLD_EMP_ID='$employee_id' ".
        " and GLD_NEXT_ACTION='$next_action' and GLD_NEXT_ACTION_DATE='$action_date' ";
    if($check_status){
        $query .= " and GLD_SCHEDULE_STATUS in (1,3) ";
    }
    $res = execute_my_query($query);
    if((mysqli_num_rows($res) > 0) && ($row=mysqli_fetch_array($res))){
        return (int)$row['GLD_ACTIVITY_ID'];
    }
    return 0;
}
/**
 * @param int $emply_id
 * @param int $lead_code
 * @param int $assign_group_id
 * @param string $phone_no
 * @return void
 */
function create_appointment_for_assign_to($emply_id,$lead_code,$assign_group_id,$phone_no){
	$next_action_date = date('Y-m-d');
	$next_action = "49";
	if($assign_group_id==1){
		if(!is_next_action_exits($lead_code, $emply_id, $next_action, $next_action_date,true)){
			$activity_dtl					=	/*. (string[string]) .*/array();
			$activity_dtl['GLD_LEAD_CODE']	=	"$lead_code";
			$activity_dtl['GLD_EMP_ID']		=	"$emply_id";
			$activity_dtl['GLD_DATE']		=	date('Y-m-d H:i:s');
			$activity_dtl['GLD_VISIT_DATE']	=	date('Y-m-d');
			$activity_dtl['GLD_NOTE_ON_ACTIVITY']	= "vSmile Assign To Activity. I will call back.";
			$activity_dtl['GLD_VISIT_NATURE']		= "24";
			$activity_dtl['GLD_SCHEDULE_STATUS']	= "1";
			$activity_dtl['GLD_NEXT_ACTION_DATE']	= $next_action_date;
			$activity_dtl['GLD_NEXT_ACTION']		= $next_action;
			insert_in_gft_activity_table($activity_dtl);
			$sms_config = /*. (string[string][int]) .*/array();
			$sms_content = get_formatted_content($sms_config, 179);
			entry_sending_sms($phone_no, $sms_content, 179,null,1,null,0,null,"$lead_code");
		}
	}
}

/**
 * @param string $phone_number
 * 
 * @return boolean
 */
function is_already_marked_as_unofficial($phone_number){
	$number_cond = getContactDtlWhereCondition("GTC_NUMBER", $phone_number);
	$que = "select GTC_ID from gft_techsupport_incomming_call where GTC_LEAD_CODE=0 and ($number_cond) and GTC_OFFICE_ID=3 ";
	$res = execute_my_query($que);
	if(mysqli_num_rows($res) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $lead_code
 * @param int $schedule_status
 * @param int $emp_id
 * 
 * @return void
 */
function change_all_followup_status($lead_code, $schedule_status,$emp_id=0){
	$update_act =" update gft_cplead_followup_dtl join gft_activity_master on (GAM_ACTIVITY_ID=GCF_FOLLOWUP_ACTION) ".
				 " set GCF_FOLLOWUP_STATUS=$schedule_status,GCF_LAST_UPDATED_TIME=now(),GCF_UPDATED_BY=9999 " .
				 " where GCF_LEAD_CODE in ($lead_code) and GCF_FOLLOWUP_STATUS in (1,3) ".($emp_id>0?" AND GCF_ASSIGN_TO='$emp_id'":"");
	execute_my_query($update_act);
}

/**
 * @param string $lead_code
 * @param int $schedule_status
 * @param int $emp_id
 *
 * @return void
 */
function change_all_appointment_status($lead_code, $schedule_status,$emp_id=0){
	$update_act =" update gft_activity join gft_activity_master on (GAM_ACTIVITY_ID=GLD_NEXT_ACTION) ".
	" set GLD_SCHEDULE_STATUS=$schedule_status,GLD_LAST_UPDATED_TIME=now(),GLD_LAST_UPDATED_BY=9999 " .
	" where GLD_LEAD_CODE in ($lead_code) and GLD_SCHEDULE_STATUS in (1,3) ".($emp_id>0?" AND GLD_EMP_ID='$emp_id'":"");
	execute_my_query($update_act);
}

/**
 * @param string $lead_code
 * @param string $action
 * @param string $assigned_to
 * @param string $skip_appt_id
 * @param string $skip_foll_id
 * @param string $after_date
 * 
 * @return boolean
 */
function is_reminder_exists($lead_code,$action='',$assigned_to='',$skip_appt_id='',$skip_foll_id='',$after_date=''){
	$appoint_query =" select GAM_ACTIVITY_ID from gft_activity join gft_activity_master on (GAM_ACTIVITY_ID=GLD_NEXT_ACTION) ".
					" where GLD_LEAD_CODE in ($lead_code) and GLD_SCHEDULE_STATUS in (1,3)  ";
	$followup_query=" select GAM_ACTIVITY_ID from gft_cplead_followup_dtl join gft_activity_master on (GAM_ACTIVITY_ID=GCF_FOLLOWUP_ACTION) ".
					" where GCF_LEAD_CODE in ($lead_code) and GCF_FOLLOWUP_STATUS in (1,3) ";
	if($action!=''){
	    $appoint_query .= " and GLD_NEXT_ACTION='$action' ";
	    $followup_query .= " and GCF_FOLLOWUP_ACTION='$action' ";
	}
	if($assigned_to!=''){
	    $appoint_query .= " and GLD_EMP_ID='$assigned_to' ";
	    $followup_query .= " and GCF_ASSIGN_TO='$assigned_to' ";
	}
	if($after_date!=''){
	    $appoint_query .= " and GLD_NEXT_ACTION_DATE >= '$after_date' ";
	    $followup_query .= " and GCF_FOLLOWUP_DATE >= '$after_date' ";
	}
	if($skip_appt_id!=''){
	    $appoint_query .= " and GLD_ACTIVITY_ID not in ($skip_appt_id) ";
	}
	if($skip_foll_id!=''){
	    $followup_query .= " and GCF_FOLLOWUP_ID not in ($skip_foll_id) ";
	}
	$que1 = "select GAM_ACTIVITY_ID from ($appoint_query) app union all ($followup_query) ";
	$res1 = execute_my_query($que1);
	if(mysqli_num_rows($res1) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $lead_code_arr
 * @param int[int] $ignore_group_arr
 * @param int $emp_id
 * 
 * @return string[string][string]
 */
function get_closest_reminder_dtl($lead_code_arr,$ignore_group_arr=null,$emp_id=0){
	$resp_arr = /*. (string[string][string]) .*/array();
	$today_date = date('Y-m-d');
	$emply_cond = "";
	if(count($ignore_group_arr) > 0){
		$group_arr_str=implode(',',$ignore_group_arr);
		$query_gr=" select distinct(a.gem_emp_id) eid from gft_emp_master a ".
				" left join gft_role_group_master rg on (grg_role_id=gem_role_id) ".
				" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id) ".
				" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id) ".
				" where ggm_group_id in ($group_arr_str) ";
		$emply_cond = " and GEM_EMP_ID not in ($query_gr) ";
	}
	if($emp_id>0){
		$emply_cond = " and GEM_EMP_ID='$emp_id' ";
	}
	foreach ($lead_code_arr as $lead_code) {
		$ret_arr = /*. (string[string]) .*/array();
		$appoint_query =" select GLD_LEAD_CODE as lead_code, GLD_NEXT_ACTION as action, GLD_NEXT_ACTION_DATE as action_date, GLD_DATE as activity_date, ".
				" GLD_NEXT_ACTION_DETAIL as action_detail from gft_activity ".
				" join gft_activity_master on (GAM_ACTIVITY_ID=GLD_NEXT_ACTION and GAM_APPLICAPLE_NEXT_VISIT='Y') ".
				" join gft_emp_master on (GEM_EMP_ID=GLD_EMP_ID) ".
				" join gft_lead_hdr on (GLH_LEAD_CODE=GLD_LEAD_CODE) ".
				" where GLD_LEAD_CODE in ($lead_code) and GLD_SCHEDULE_STATUS in (1,3) and GLD_VISIT_NATURE not in (99) $emply_cond ".
				" and if(GLH_STATUS=9 and GLH_BALANCE_AMOUNT < 1, GAM_ACTIVITY_ID!=6, 1) ";  //6 for collection
		$followup_query=" select GCF_LEAD_CODE as lead_code, GCF_FOLLOWUP_ACTION as action, GCF_FOLLOWUP_DATE as action_date,GLD_DATE as activity_date, ".
				" GCF_FOLLOWUP_DETAIL as action_detail from gft_cplead_followup_dtl ".
				" join gft_activity on (GLD_ACTIVITY_ID=GCF_ACTIVITY_REF) ".
				" join gft_activity_master on (GAM_ACTIVITY_ID=GCF_FOLLOWUP_ACTION and GAM_APPLICAPLE_NEXT_VISIT='Y') ".
				" join gft_emp_master on (GEM_EMP_ID=GCF_ASSIGN_TO) ".
				" join gft_lead_hdr on (GLH_LEAD_CODE=GCF_LEAD_CODE) ".
				" where GCF_LEAD_CODE in ($lead_code) and GCF_FOLLOWUP_STATUS in (1,3) $emply_cond ".
				" and if(GLH_STATUS=9 and GLH_BALANCE_AMOUNT < 1, GAM_ACTIVITY_ID!=6, 1) "; //6 for collection
		$future_que = " select lead_code,action,action_date,activity_date,action_detail from ($appoint_query union all $followup_query ) t1 where action_date > '$today_date' order by activity_date desc limit 1 ";
		$future_res = execute_my_query($future_que);
		if($future_row = mysqli_fetch_array($future_res)){
			$ret_arr['action'] 		= $future_row['action'];
			$ret_arr['action_date']	= $future_row['action_date'];
			$ret_arr['action_detail'] = $future_row['action_detail'];
		}else{
			$past_que = "select lead_code,action,action_date,activity_date,action_detail from ($appoint_query union all $followup_query) t1 where action_date <= '$today_date' order by activity_date desc limit 1 ";
			$past_res = execute_my_query($past_que);
			if($past_row = mysqli_fetch_array($past_res)){
				$ret_arr['action'] 		= $past_row['action'];
				$ret_arr['action_date']	= '';
				$ret_arr['action_detail'] = $past_row['action_detail'];
			}
		}
		$resp_arr["$lead_code"] = $ret_arr;
	}
	return $resp_arr;
}

/**
 * @param string $partner_sub_type
 * @param string $partner_status
 * 
 * @return string
 */
function get_partner_list($partner_sub_type,$partner_status){
	$query =" select GLH_LEAD_CODE from gft_lead_hdr join gft_cp_info on (GLH_LEAD_CODE=CGI_LEAD_CODE) ".
			" where CGI_STATUS in ($partner_status) and GLH_LEAD_SUBTYPE in ($partner_sub_type) ";
	$res = execute_my_query($query);
	$partner_arr = array();
	while($row1 = mysqli_fetch_array($res)){
		$partner_arr[] = $row1['GLH_LEAD_CODE'];
	}
	$partner_str = implode(",", $partner_arr);
	return $partner_str;
}

/**
 * @param string $order_no
 * @param string $fullfill_no
 * @param string $pcode
 * @param string $pskew
 * 
 * @return string
 */
function get_previous_expiry_date($order_no,$fullfill_no,$pcode,$pskew){
	$query =" select GID_SENT_EXPIRY_DATE from gft_install_dtl_new where GID_ORDER_NO='$order_no' ".
			" and GID_FULLFILLMENT_NO=$fullfill_no and GID_PRODUCT_CODE='$pcode' and GID_PRODUCT_SKEW='$pskew' ";
	$res = execute_my_query($query);
	if($row1 = mysqli_fetch_array($res)){
		return $row1['GID_SENT_EXPIRY_DATE'];
	}
	return '';
}

/**
 * @param string $dealer_id
 * 
 * @return void
 */
function send_notification_to_dealer_for_trial_license($dealer_id){
	$sql1 = " select count(GID_INSTALL_ID) as tot_cnt from gft_install_dtl_new join gft_lead_hdr on (GLH_LEAD_CODE=GID_LEAD_CODE) ".
			" where GLH_LEAD_TYPE=7 and GID_EMP_ID='$dealer_id' group by GID_EMP_ID ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$cnt = $row1['tot_cnt'];
		$template_id = 19;
		if($cnt==1){
			$template_id = 18;		
		}
		send_formatted_notification_content(null,9,$template_id,1,$dealer_id);
	}
}

/**
 * @param int $dealer_id
 *
 * @return void
 */
function send_notification_to_dealer_for_approved_license($dealer_id){
	$sql1 = " select count(GID_INSTALL_ID) as tot_cnt from gft_install_dtl_new ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GID_LEAD_CODE) ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
			" where GLH_LEAD_TYPE=7 and GID_EMP_ID='$dealer_id' and GPM_LICENSE_TYPE not in (3) group by GID_EMP_ID ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$cnt = $row1['tot_cnt'];
		$template_id = 21;
		if($cnt==1){
			$template_id = 20;
		}
		send_formatted_notification_content(null,9,$template_id,1,$dealer_id);
	}
}

/**
 * @param string $customer_id
 * @param string $install_id
 * @param string $ref_emp_id
 * @param string[int] $mapped_outlet_arr
 * 
 * @return void
 */
function insert_mapped_outlets($customer_id,$install_id,$ref_emp_id,$mapped_outlet_arr){
	$insert_val = $put_comma = "";
	foreach ($mapped_outlet_arr as $val){
		$insert_val .= " $put_comma('$customer_id','$install_id','$ref_emp_id','$val',now()) ";
		$put_comma = ",";
	}
	if($insert_val!=''){
		$del1 = "delete from gft_hq_user_outlet_mapping where GHU_INSTALL_ID='$install_id' and GHU_USER_ID='$ref_emp_id' ";
		execute_my_query($del1);
		$ins1 = " insert into gft_hq_user_outlet_mapping (GHU_CUSTOMER_ID,GHU_INSTALL_ID,GHU_USER_ID,GHU_OUTLET_ID,GHU_UPDATED_DATE) ".
				" values $insert_val ";
		execute_my_query($ins1);
	}
}

/**
 * @param string $install_id
 * @param string[int] $mapped_outlets
 *
 * @return string[string][int]
 */
function get_group_and_outlet_name_from_outlets($install_id,$mapped_outlets){
	$id_str = "'".implode("','",$mapped_outlets)."'";
	$outlet_master = /*. (string[string]) .*/array();
	$ret_arr = /*. (string[string][int]) .*/array();
	$sql_master = "select GHO_OUTLET_ID,GHO_OUTLET_NAME from gft_hq_outlet_master where GHO_INSTALL_ID='$install_id' and GHO_OUTLET_ID in ($id_str)  ";
	$res_master = execute_my_query($sql_master);
	while($row1 = mysqli_fetch_array($res_master)){
		$outlet_master[$row1['GHO_OUTLET_ID']] = $row1['GHO_OUTLET_NAME'];
	}
	execute_my_query("SET SESSION group_concat_max_len = 10240");
	$sql1 = " select GHG_GROUP_ID,group_concat(distinct GHM_OUTLET_ID) as outlet_ids,GHG_GROUP_NAME ".
			" from gft_hq_outlet_group_mapping ".
			" join gft_hq_outlet_group_master on (GHG_INSTALL_ID=GHM_INSTALL_ID and GHG_GROUP_ID=GHM_GROUP_ID) ".
			" where GHM_INSTALL_ID='$install_id' and  GHM_OUTLET_ID in ($id_str) group by GHG_GROUP_ID ";
	$res1 = execute_my_query($sql1);
	$temp_group = "";
	while ($row1 = mysqli_fetch_array($res1)){
		$outlet_arr = $main_arr = array();
		$id_arr = explode(',', $row1['outlet_ids']);
		foreach ($id_arr as $oid){
			$arr['id']		= $oid;
			$arr['name']	= $outlet_master[$oid];
			$outlet_arr[] 	= $arr;
		}
		$main_arr['id'] 	= $row1['GHG_GROUP_ID'];
		$main_arr['name'] 	= $row1['GHG_GROUP_NAME'];
		$main_arr['outlets']= $outlet_arr;
		$ret_arr[] = $main_arr;
	}
	return $ret_arr;
}

/**
 * @param string $start_date
 * @param string $end_date
 * @param string $empl_id
 * 
 * @return int[int][int]
 */
function get_leave_date_with_status_in_array($start_date,$end_date,$empl_id){
	$query =" select gelr_from_date, gelr_to_date, gels_status ".
			" from gft_emp_leave_request join gft_emp_leave_req_status stat on (stat.gels_lr_id=gelr_id) ".
			" where gelr_emp_id='$empl_id' and gelr_halfday!='Y' and gelr_leave_type!='P' and gelr_leave_type!='OD' and ".
			" ((gelr_from_date >= '$start_date' and gelr_from_date <= '$end_date') or (gelr_to_date >= '$start_date' and gelr_to_date <= '$end_date') or (gelr_from_date <= '$start_date' and gelr_to_date >= '$end_date')) ";
	$ret_date = /*. (int[int][int]) .*/array();
	$que_res = execute_my_query($query);
	while($row1 = mysqli_fetch_array($que_res)){
		$current = strtotime($row1['gelr_from_date']);
		$last = strtotime($row1['gelr_to_date']);
		$leave_stat = (int)$row1['gels_status'];
		while( $current <= $last ) {
			$ret_date[0][] = (int)date('d', $current);
			$ret_date[1][] = $leave_stat;
			$current = strtotime('+1 day', $current);
		}
	}
	return $ret_date;
}

/**
 * @param string $app_pcode
 * @param string $base_product_id
 *
 * @return string[string]
 */
function get_demo_dtl_arr($app_pcode,$base_product_id){
    $ret_arr = array();       
	$sql1 = " select GWU_TRIAL_URL,GWU_DEMO_CUSTOMER_ID from gft_whatsnow_url_master where GWU_APP_PCODE='$app_pcode' and GWU_STATUS='A' ".
			" and (GWU_PRODUCT_CODE='$base_product_id' or GWU_PRODUCT_CODE=0) order by GWU_PRODUCT_CODE desc limit 1 ";
	$res1 = execute_my_query($sql1);
	$demo_url 		= '';
	$demo_cust_id 	= '';
	if($row1 = mysqli_fetch_array($res1)){
		$demo_url 		= $row1['GWU_TRIAL_URL'];
		$demo_cust_id 	= $row1['GWU_DEMO_CUSTOMER_ID'];
	}
	$ret_arr['demo_url'] 		= $demo_url;
	$ret_arr['demo_cust_id'] 	= $demo_cust_id;
	return $ret_arr;
}
/**
 * @param string $customer_id
 * 
 * @return string[string]
 */
function get_demo_url_for_employee_addon($customer_id,$product_code,$business_type){
    $field_arr = array();
    if(get_samee_const("SHOW_EMPLOYEE_BASED_STORE_URL")==0){
        return $field_arr;
    }
    if($customer_id!=""){
        $date_con = date("Y-m-d H:i:s", strtotime('-24 hours'));
        $sql1 = " select GDT_LEAD_CODE,GEM_LEAD_CODE, GEM_EMP_ID,GEM_EMP_NAME, GEM_LEAD_CODE, GID_STORE_URL, ". 
            " GID_INSTALL_ID,GID_SERVER_NAME,GID_SERVER_IP,GLH_CUST_STREETADDR2,GLH_CUST_NAME from gft_emp_master em ".
            " INNER JOIN gft_install_dtl_new ON(GID_LEAD_CODE=GEM_LEAD_CODE) ".
            " INNER JOIN gft_lead_hdr lh ON(GID_LEAD_CODE=GLH_LEAD_CODE)".
            " JOIN gft_demo_tracking dt ON(GDT_EMP_ID=GEM_EMP_ID AND GDT_STATUS=1 AND GDT_START_DATE>'$date_con') ".
            " where (GEM_LEAD_CODE=$customer_id OR GDT_LEAD_CODE=$customer_id) AND GID_STATUS!='U' AND GID_STORE_URL!='' ORDER BY GID_STORE_URL DESC LIMIT 1";
        $res1 = execute_my_query($sql1);
        if((mysqli_num_rows($res1)>0) && $row1=mysqli_fetch_assoc($res1)){
            $short_app_name = get_single_value_from_single_table("GPM_PRODUCT_NAME", "gft_product_family_master", "GPM_PRODUCT_CODE", $product_code);
            $toast_msg 	= "$short_app_name is connected to your store ".$row1['GLH_CUST_NAME'].". Happy Billing";	
            $field_arr['customerId'] = $customer_id;
            $field_arr['userName'] 	= "admin";
            $field_arr['userType'] 	= "live";
            $field_arr['shopName'] 	= $row1['GLH_CUST_NAME'];
            $field_arr['shopLocation'] 	= $row1['GLH_CUST_STREETADDR2'];
            $field_arr['internetUrl'] 	= $row1['GID_STORE_URL'];
            $field_arr['intranetUrl'] 	= $row1['GID_STORE_URL'];
            $field_arr['serverName'] 	= $row1['GID_SERVER_NAME'];
            $field_arr['systemIp'] 		= $row1['GID_SERVER_IP'];
            $field_arr['verticalType'] 	= $business_type;
            $field_arr['isLive'] 		= true;
            $field_arr['welcomeMessage']= $toast_msg;
            $field_arr['installId']		= $row1['GID_INSTALL_ID'];
            $field_arr['appExpiryDate'] = date("Y-m-d", strtotime("+1 day", strtotime(date('Y-m-d'))));
            $field_arr['appInstallDate'] = date("Y-m-d");
            $field_arr['appLicenseType']= "live";
            $field_arr['customLicenses'] = get_addon_features_license_details($product_code, $customer_id,'app');
            $field_arr['emailIdList']   = get_contact_dtl_for_designation($customer_id, '4', '1,2,3,4');
            $field_arr['locTracker']    = (Object)array();
        }
    } 
    return $field_arr;
}
/**
 * @param string $employee_id
 * 
 * @return boolean
 */
function is_mydelight_app_active($employee_id){
	$sql1 = " select GEM_EMP_ID from gft_emp_auth_key inner join gft_emp_master on (EMP_ID=gem_emp_id) ".
			" where GEM_EMP_ID='$employee_id' and GEM_STATUS='A' and GEK_GCM_REGISTER_ID!='' and GEK_DEVICE_STATUS=1 and GEK_STATUS='A' ";
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $pcode
 * 
 * @return string
 */
function get_url_extension($pcode){
	$url_extension = '';
	if(in_array($pcode,array('200','500','501','502'))){
		$url_extension = "/WebReporter";
	}else if( ($pcode=='600') || ($pcode=='601') ){
		$url_extension = "/TruePOS";
	}else if($pcode=='605'){
		$url_extension = "/servquick";
	}else if($pcode=='999'){
	    $url_extension = "/ZohoPOS";
	}
	return $url_extension;
}

/**
 * @return string
 */
function gen_uuid() {
	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
	);
}

/**
 * @param string $transaction_id
 * @param string $jm_ref_no
 * 
 * @return boolean
 */
function is_valid_jio_transaction($transaction_id,$jm_ref_no){
	$req_uuid = gen_uuid();
	$jio_config 	= get_jio_payment_config();
	$checksum_seed 	= $jio_config['checksum_seed'];
	$client_id		= $jio_config['client_id'];
	$merchant_id 	= $jio_config['merchant_id'];
	$post_url		= $jio_config['status_query_url'];
	$data = "$client_id|$merchant_id|STATUSQUERY|$transaction_id";
	$checksum_value = hash_hmac('SHA256',$data, $checksum_seed);
	$arr['request_header'] = array('version'=>'1.0','api_name'=>'STATUSQUERY');
	$arr['payload_data'] = array("client_id"=>$client_id,"merchant_id"=>$merchant_id,"tran_ref_no"=>$transaction_id);
	$arr['checksum'] = $checksum_value;
	$post_data = json_encode($arr);
	$header_arr = array("Content-Type: application/json","Accept: application/json");
	$ch = curl_init($post_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
	$api_response = (string)curl_exec($ch);
	$json_arr = json_decode($api_response,true);
	$resp_arr['json'] = $api_response;
	$resp_arr['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curl_error = curl_error($ch);
	if($curl_error!=''){
		$resp_arr['curl_error'] = $curl_error;
	}
	curl_close($ch);
	$rec_status = isset($json_arr['response_header']['api_status'])?$json_arr['response_header']['api_status']:'';
	if($rec_status=='1'){
		$rec_jm_ref_no = isset($json_arr['payload_data']['jm_tran_ref_no'])?$json_arr['payload_data']['jm_tran_ref_no']:'';
		if($jm_ref_no==$rec_jm_ref_no){
			return true;
		}
	}
	return false;
}

/**
 * @param string $employee_id
 * @param string $start_date
 * @param string $end_date
 * 
 * @return int[string]
 */
function get_number_of_appointments_datewise($employee_id,$start_date,$end_date){
	$ret_arr = /*. (int[string]) .*/array();
	$appoint_query =" select GLD_NEXT_ACTION_DATE as action_date from gft_activity ".
			" join gft_activity_master on (GAM_ACTIVITY_ID=GLD_NEXT_ACTION) ".
			" where GLD_EMP_ID='$employee_id' and GLD_SCHEDULE_STATUS in (1,3) ".
			" and GLD_NEXT_ACTION_DATE between '$start_date' and '$end_date' ";
	$followup_query=" select GCF_FOLLOWUP_DATE as action_date from gft_cplead_followup_dtl ".
			" join gft_activity_master on (GAM_ACTIVITY_ID=GCF_FOLLOWUP_ACTION) ".
			" where GCF_ASSIGN_TO='$employee_id' and GCF_FOLLOWUP_STATUS in (1,3) ".
			" and GCF_FOLLOWUP_DATE between '$start_date' and '$end_date' ";
	$sql1 = " select action_date, count(action_date) as cnt from ($appoint_query union all $followup_query) t1 ".
			" where action_date not in (select GHL_DATE from gft_holiday_list where GHL_DATE > '$start_date' ) group by action_date ";
	$res1 = execute_my_query($sql1);
	$data_arr = /*.(int[string]).*/array();
	while($row1 = mysqli_fetch_array($res1)){
		$naction_date = $row1['action_date'];
		$data_arr[$naction_date] = (int)$row1['cnt'];
	}
	$date_list = /*. (string[int]) .*/array();
	$start_time = strtotime($start_date);
	$end_time = strtotime($end_date);
	for($i=$start_time; $i<$end_time; $i+=86400){
		$temp_date = date('Y-m-d', $i);
		if(date('N',$i)=='7'){ //sunday
			continue;
		}else if( (date('N',$i)=='6') && (in_array(ceil(date('d',$i)/7),array(1,3))) ){ //saturday
			continue;
		}
		$ret_arr[$temp_date] = isset($data_arr[$temp_date])?$data_arr[$temp_date]:0;
	}
	return $ret_arr;
}

/**
 * @param string $y
 * @param string $m
 * @param string $day
 * 
 * @return DatePeriod
 */
function getDays($y, $m, $day){
	return new DatePeriod(
			new DateTime("first $day of $y-$m"),
			DateInterval::createFromDateString("next $day"),
			new DateTime("next month $y-$m-01")
	);
}

/**
 * @param string[int] $connect_post_arr
 * @param string $customer_id
 * @param KLogger $log
 * 
 * @return void
 */
function post_user_arr_to_connectplus($connect_post_arr,$customer_id,$log){
	if(count($connect_post_arr) > 0){
		$cp_config = get_connectplus_config();
		$cp_api_key = $cp_config['provision_api_key'];
		$header_arr = array("Content-Type: application/json","x-api-key: $cp_api_key");
		$post_url	= $cp_config['pos_user_url'];
		$post_url	= (string)str_replace("{{customerId}}", $customer_id, $post_url);
		$post_data	= json_encode($connect_post_arr);
		$req_method = "PUT";
		$suspend_sync = (int)get_samee_const("Suspend_Pos_User_Sync");
		if($suspend_sync==1){
			insert_into_sync_queue($post_url,$post_data,json_encode($header_arr),$req_method);
			return;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $post_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $req_method);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
		$resp_body = (string)curl_exec($ch);
		$resp_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$resp_arr['response_json'] = $resp_body;
		$resp_arr['response_code'] = $resp_code;
		$curl_error = curl_error($ch);
		if($curl_error!=''){
			$resp_arr['curl_error'] = $curl_error;
		}
		curl_close($ch);
		if(!empty($log)){
			$log->logInfo("Request - ".$post_data);
			$log->logInfo("Response - ".json_encode($resp_arr));
		}
		if($resp_code!=200){
			insert_into_sync_queue($post_url,$post_data,json_encode($header_arr),$req_method,$resp_code,$resp_body);
		}
	}
}

/**
 * @param string $mobile_no
 * @param string $email_id
 * @param string $pcode
 * @param string $pgroup
 *
 * @return boolean
 */
function is_order_exists($mobile_no,$email_id,$pcode,$pgroup){
	$mob_cond = contact_info_where_condition("GCC_CONTACT_NO", $mobile_no);
	$lead_contact = " select GCC_LEAD_CODE from gft_customer_contact_dtl where GCC_CONTACT_NO='$email_id' or $mob_cond ";
	$sql1 = " select GOD_ORDER_NO from gft_order_hdr join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
			" where GOD_ORDER_STATUS='A' and GOD_ORDER_AMT > 0 and GOP_PRODUCT_CODE='$pcode' and substr(GOP_PRODUCT_SKEW,1,4)='$pgroup' ".
			" and GOD_LEAD_CODE in ($lead_contact) ";
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $customer_id
 * 
 * @return boolean
 */
function is_kit_based_customer($customer_id){
	$sql1 = " select GOD_ORDER_NO from gft_order_hdr join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) ".
			" join gft_product_master on (GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" where GOD_LEAD_CODE='$customer_id' and GOD_ORDER_STATUS='A' and GPM_ORDER_TYPE in (2,3) group by GOD_ORDER_NO ";
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1) > 0){
		return true;
	}
	return false;
	
}

/**
 * @param string $order_no
 * @param string $fullfillment_no
 * 
 * @return boolean
 */
function is_auto_split_order($order_no,$fullfillment_no){
	$fullfill_no = (int)$fullfillment_no;
	$que1 = " select GOL_ORDER_NO from gft_outlet_lead_code_mapping where GOL_ORDER_NO='$order_no' and GOL_FULLFILLMENT_NO=$fullfill_no ";
	$res1 = execute_my_query($que1);
	if(mysqli_num_rows($res1) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $target_dir
 * @param KLogger $log
 * 
 * @return string
 */
function get_temporay_amazon_url($target_dir,$log){
	$post_arr['fileKey'] = $target_dir;
	$post_arr['bucket'] = 'video-audio-stream';
	$post_arr['ttl'] 	= get_samee_const("VIDEO_URL_VALIDITY_IN_SECONDS");
	$request_body = json_encode($post_arr);
	//$post_url = "http://localhost:9090/generateUrl";
	$post_url = "https://assure.gofrugal.com/s3tool/generateUrl";
	$header_arr = array('Content-Type: application/json');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $post_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
	$response = (string)curl_exec($ch);
	$http_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$resp_arr['response_json'] = $response;
	$resp_arr['response_code'] = "$http_response_code";
	$curl_error = curl_error($ch);
	if($curl_error!=''){
		$resp_arr['curl_error'] = $curl_error;
	}
	$log->logInfo($request_body);
	$log->logInfo(json_encode($resp_arr));
	$decoded_arr = /*. (string[string]) .*/json_decode($response,true);
	$temporary_url = isset($decoded_arr['url'])?$decoded_arr['url']:'';
	curl_close($ch);
	return $temporary_url;
}

/**
 * @param string $outlet_id
 * @param string $install_id
 * 
 * @return int
 */
function get_lead_code_for_outlet($outlet_id,$install_id){
	$sql1 = "select GOL_CUST_ID from gft_outlet_lead_code_mapping where GOL_OUTLET_ID='$outlet_id' and GOL_INSTALL_ID='$install_id' ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		return (int)$row1['GOL_CUST_ID'];
	}
	return 0;
}

/**
 * @param string $employee_id
 * 
 * @return boolean
 */
function is_enabled_for_click_to_call($employee_id){
	$sql1 = "select GEM_CALL_APIKEY from gft_emp_master where GEM_EMP_ID='$employee_id' and GEM_STATUS='A' and GEM_CALL_APIKEY!='' and GEM_CALL_APIKEY is not null ";
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1)==1){
		return true;
	}
	return false;
}
/**
 * @param string $time_ago
 *
 * @return string
 */
function timeago($time_ago){
	$out_string='';
	$cur_time 	= time();
	$time_elapsed 	= $cur_time - $time_ago;
	$seconds 	= $time_elapsed ;
	$minutes 	= round($time_elapsed / 60 );
	$hours 		= round($time_elapsed / 3600);
	$days 		= round($time_elapsed / 86400 );
	$weeks 		= round($time_elapsed / 604800);
	$months 	= round($time_elapsed / 2600640 );
	$years 		= round($time_elapsed / 31207680 );
	// Seconds
	if($seconds <= 60){
		$out_string .= "$seconds seconds ago";
	}
	//Minutes
	else if($minutes <=60){
		if($minutes==1){
			$out_string .=  "one minute ago";
		}
		else{
			$out_string .=  "$minutes minutes ago";
		}
	}
	//Hours
	else if($hours <24){
		if($hours==1){
			$out_string .=  "an hour ago";
		}else{
			$out_string .=  "$hours hours ago";
		}
	}
	//Days
	else if($days <= 7){
		if($days==1){
			$out_string .=  "yesterday";
		}else{
			$out_string .=  "$days days ago";
		}
	}
	//Weeks
	else if($weeks <= 4.3){
		if($weeks==1){
			$out_string .=  "a week ago";
		}else{
			$out_string .=  "$weeks weeks ago";
		}
	}
	//Months
	else if($months <=12){
		if($months==1){
			$out_string .=  "a month ago";
		}else{
			$out_string .=  "$months months ago";
		}
	}
	//Years
	else{
		if($years==1){
			$out_string .=  "one year ago";
		}else{
			$out_string .=  "$years years ago";
		}
	}
	return $out_string;
}
/**
 * @param string $date
 * @param int $years
 * @param int $months
 *
 * @return string
 */
function since_date($date,$years='',$months='',$days=''){
    $out_string='';
    if($date!=''){
        $time_elapsed 	= time() - strtotime($date);
        $years 		= floor($time_elapsed / 31536000);
        $month_cnt 	= floor($time_elapsed / 2592000);
        $months = ($month_cnt % 12);
    }
    if($years==1){
        $out_string .=  "1 year ";
    }else if($years>1){
        $out_string .=  "$years years ";
    }
    if($months==1){
        $out_string .=  "1 month";
    }else if($months>1){
        $out_string .=  " $months months";
    }
    if($days==1){
        $out_string .=  "1 day";
    }else if($days>1){
        $out_string .=  " $days days";
    }
    return $out_string;
}
/**
 * @param string $master_video_id
 *
 * @return string[int][string]
 */
function get_video_comments($master_video_id){
	global $uid;
	$que1 = " select GEM_EMP_NAME,GVC_COMMENTS,GVC_ID,GVC_DATETIME,GVC_STATUS from gft_video_comments ".
			" left join gft_video_analytics_hdr va on (GVH_GROUP_ID=GVC_GROUP_ID) ".
			" left join gft_emp_master em on (GVH_EMP_ID=GEM_EMP_ID) ".
			" where GVH_VIDEO_ID='$master_video_id'";
	if(!is_authorized_group($uid, '114')){
		$que1 .= " and GVC_STATUS='A'";
	}
	$que1 .= " order by GVC_DATETIME asc";
	$res3 = execute_my_query($que1);
	$return_arr = /*. (string[int][string]) .*/array();
	while($query_data3=mysqli_fetch_array($res3)){
		$ename=$query_data3['GEM_EMP_NAME'];
		$comments=$query_data3['GVC_COMMENTS'];
		$id1=$query_data3['GVC_ID'];
		$currenttime=$query_data3['GVC_DATETIME'];
        $time_ago =strtotime($currenttime);
        $comment_arr['id']           = $id1;
        $comment_arr['name']         = $ename;
        $comment_arr['comment']      = $comments;
        $comment_arr['time_ago']     = timeago($time_ago);
        $comment_arr['status']     	 = $query_data3['GVC_STATUS'];
        $return_arr[]                = $comment_arr;
	}
	return $return_arr;
}

/**
 * @param int $pcode
 *
 * @return string
 */
function return_trial_skew($pcode){
	$pskew = "";
	$que1 = " select GPM_PRODUCT_SKEW from gft_product_master where GPM_PRODUCT_CODE='$pcode' ".
			" and gpm_license_type=3 and GPM_STATUS='A' and GPM_FREE_EDITION='Y' ";
	$res1 = execute_my_query($que1);
	if($row1 = mysqli_fetch_array($res1)){
		$pskew = $row1['GPM_PRODUCT_SKEW'];
	}
	return $pskew;
}

/**
 * @param string $status
 * @param string $message
 * @param string $callback
 * @param string[string] $extra_json_array
 * @param string $assign_key_val
 * @param string[string] $appand_array
 *
 * @return void
 */
function send_response_in_json_with_file_log($status,$message='',$callback='',$extra_json_array=null,$assign_key_val="",$appand_array=null){
	global $log;
	$ret_json['status'] = $status;
	$ret_json['message'] = $message;	
	if(is_array($extra_json_array)){
		foreach ($extra_json_array as $ak=>$av){
			$ret_json[$ak] = $av;
		}
	}
	if($assign_key_val!="" && $appand_array!=null){
		$ret_json["$assign_key_val"]=$appand_array;
	}
	$json_resp = json_encode($ret_json);
	if($callback!=""){
		$json_resp = $callback."($json_resp);";
	}
	echo $json_resp;
	$log->logInfo("Response - ".$json_resp);
}

/**
 * @param string $customer_id
 * 
 * @return void
 */
function sync_pos_users_to_connectplus($customer_id){
	global $log,$cached_test_mode;
	if($customer_id==''){
		return;
	}
	$sql1 = " select GCC_LEAD_CODE,GPU_INSTALL_ID from gft_customer_contact_dtl ".
	   	    " join gft_install_dtl_new on (GID_LEAD_CODE=GCC_LEAD_CODE) ".
			" join gft_pos_users on (GPU_CONTACT_ID=GCC_ID and GPU_INSTALL_ID=GID_INSTALL_ID) ".
			" where GPU_PASSWORD!='' and GCC_LEAD_CODE in ($customer_id) and GPU_CONTACT_STATUS='A' and GID_STATUS!='U' ".
			" group by GCC_LEAD_CODE,GPU_INSTALL_ID ";
	$res1 = execute_my_query($sql1);
	while($row1 = mysqli_fetch_array($res1)){
		$lead_code = $row1['GCC_LEAD_CODE'];
		$install_id = $row1['GPU_INSTALL_ID'];
		$cust_type = null;
		$query_result = execute_my_query("select GLH_LEAD_CODE,GLH_COUNTRY,GLH_CUST_NAME,GLE_USER_SYNC_TYPE from gft_lead_hdr join gft_lead_hdr_ext on (GLE_LEAD_CODE=GLH_LEAD_CODE) where GLH_LEAD_CODE='$lead_code' ");
		if($row1 = mysqli_fetch_array($query_result)){
		    $cust_type      = $row1['GLE_USER_SYNC_TYPE'];
		}
		$que1 = " select GPU_INSTALL_ID,GPU_USER_ID,GPU_USER_NAME,GPU_PASSWORD,GPU_CONTACT_STATUS,GPU_POS_ROLE_ID,GPU_POS_ROLE,GPU_SYSTEM_ACCESS, ".
				" group_concat(distinct mob.GCC_CONTACT_NO) as mobile,group_concat(distinct ema.GCC_CONTACT_NO) as email, ".
				" GLH_CUST_NAME,GID_LIC_PCODE,GID_LIC_PSKEW,ifnull(mob.GCC_CONTACT_NAME,ema.GCC_CONTACT_NAME) as contact_name, ".
				" group_concat(distinct GHU_OUTLET_ID) as outlet_ids ".
				" from gft_pos_users join gft_install_dtl_new on (GID_INSTALL_ID=GPU_INSTALL_ID) ".
				" join gft_lead_hdr on (GLH_LEAD_CODE=GID_LEAD_CODE) ".
				" left join gft_customer_contact_dtl mob on (mob.GCC_ID=GPU_CONTACT_ID and GPU_CONTACT_TYPE=1) ".
				" left join gft_customer_contact_dtl ema on (ema.GCC_ID=GPU_CONTACT_ID and GPU_CONTACT_TYPE=4) ".
				" left join gft_hq_user_outlet_mapping on (GHU_INSTALL_ID=GPU_INSTALL_ID and GHU_USER_ID=GPU_USER_ID) ".
				" where GPU_INSTALL_ID in ($install_id) and GPU_PASSWORD!='' and GPU_CONTACT_STATUS='A' group by GPU_USER_ID ";
		$qres1 = execute_my_query($que1);
		$connect_post_arr = /*. () .*/array();
		while ($qrow1 = mysqli_fetch_array($qres1)){
			$arr = /*. (string[string]) .*/array();
			$arr['empId'] 			= $qrow1['GPU_USER_ID'];
			$arr['userId'] 			= $qrow1['GPU_USER_NAME'];
			$arr['userName']		= $qrow1['contact_name'];
			$arr['password'] 		= $qrow1['GPU_PASSWORD'];
			$arr['customerId'] 		= $lead_code;
			$arr['customerName'] 	= $qrow1['GLH_CUST_NAME'];
			$arr['roleId'] 			= $qrow1['GPU_POS_ROLE_ID'];
			$arr['role'] 			= $qrow1['GPU_POS_ROLE'];
			$arr['hasSystemAccess'] = ($qrow1['GPU_SYSTEM_ACCESS']=='1')?true:false;
			$arr['enabled'] 		= ($qrow1['GPU_CONTACT_STATUS']=='A')?true:false;
			$arr['mobileNumber'] 	= $qrow1['mobile'];
			$arr['emailId'] 		= $qrow1['email'];
			$arr['baseProductId'] 	= $qrow1['GID_LIC_PCODE']."-".substr($qrow1['GID_LIC_PSKEW'], 0, 4);
			$arr['customerType']    = $cust_type;
			$arr['groups'] 			= get_group_and_outlet_name_from_outlets($qrow1['GPU_INSTALL_ID'],explode(',', $qrow1['outlet_ids']));
			$connect_post_arr[]		= $arr;
		}
		post_user_arr_to_connectplus($connect_post_arr, $lead_code, $log);
	}
}

/**
 * @param string $lead_code
 * @param int[int] $skew_prop_arr
 * @param boolean $only_paid_order
 * @param string $only_pcode
 * @param string $only_pskew
 * @param boolean $include_split
 * @param string $to_dt_cond
 * @param int[int] $not_skew_prop
 * @param string[int] $pcode_arr
 * @param string $free_edition_flag
 * @param string $base_product_flag
 * 
 * @return string[int][int]
 */
function get_order_dtl_of_lead($lead_code,$skew_prop_arr=null,$only_paid_order=false,$only_pcode='',$only_pskew='',$include_split=true,$to_dt_cond='',$not_skew_prop=null,$pcode_arr=null,
            $free_edition_flag='',$base_product_flag=''){
	$que1 = " select GOD_ORDER_NO,GOD_ORDER_DATE,GOD_IMPL_REQUIRED,GPM_SKEW_DESC,GOD_CREATED_DATE,if(GOD_ORDER_SPLICT=1,GCO_FULLFILLMENT_NO,GOP_FULLFILLMENT_NO) fullfill_no ".
	   	    " from gft_order_hdr ".
			" join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
			" left join gft_cp_order_dtl on (GCO_ORDER_NO=GOP_ORDER_NO and GCO_PRODUCT_CODE=GOP_PRODUCT_CODE and GCO_SKEW=GOP_PRODUCT_SKEW) ".
			" join gft_product_master pm on (GOP_PRODUCT_CODE=pm.GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
			" where GOD_ORDER_STATUS='A' ";
	$wh_cond = "";
	if($only_paid_order){
		$wh_cond .= " and GOD_ORDER_AMT > 0 ";
	}
	if(is_array($skew_prop_arr) && count($skew_prop_arr) > 0){
		$wh_cond .= " and GFT_SKEW_PROPERTY in ('".implode("','", $skew_prop_arr)."')";
	}
	if(is_array($not_skew_prop) && count($not_skew_prop) > 0){
		$wh_cond .= " and GFT_SKEW_PROPERTY not in ('".implode("','", $not_skew_prop)."')";
	}
	if($only_pcode!='') {
		$wh_cond .= " and pm.gpm_product_code='$only_pcode' ";
	}
	if(is_array($pcode_arr) && count($pcode_arr) > 0){
		$pcode_str = implode(",", $pcode_arr);
		$wh_cond .= " and pm.gpm_product_code in ($pcode_str) ";
	}
	if($only_pskew!='') {
		$wh_cond .= " and gpm_product_skew='$only_pskew' ";
	}
	$wh_cond .= ($free_edition_flag!='') ? " and GPM_FREE_EDITION='$free_edition_flag' " : "";
	$wh_cond .= ($base_product_flag!='') ? " and GPM_IS_BASE_PRODUCT='$base_product_flag' " : "";
	$wh_cond .= ($to_dt_cond!='') ? " and GOD_CREATED_DATE <= '$to_dt_cond' " : "";
	$sql1 		= " $que1 and GOD_LEAD_CODE='$lead_code' $wh_cond ";
	$split_que 	= " $que1 and GCO_CUST_CODE='$lead_code' $wh_cond ";
	
	$union_que 	= " $sql1 union $split_que order by GOD_ORDER_DATE,GOD_CREATED_DATE ";
	$res1 = execute_my_query($union_que);
	$arr = /*. (string[int][int]) .*/array();
	$i=0;
	while ($row1 = mysqli_fetch_array($res1)) {
		$arr[$i][0] = $row1['GOD_ORDER_NO'];
		$arr[$i][1] = $row1['GOD_ORDER_DATE'];
		$arr[$i][2] = $row1['GOD_IMPL_REQUIRED'];
		$arr[$i][3] = $row1['GPM_SKEW_DESC'];
		$arr[$i][4] = $row1['fullfill_no'];
		$i++;
	}
	return $arr;
}

/**
 * @param string $lead_code
 * 
 * @return string[int]
 */
function get_not_allowed_product_for_vertical($lead_code){
	$not_allowed_arr = /*. (string[int]) .*/array();
	$ver_map_query=" select concat(GPPM_PRODUCT_ABR,'-',gpg_version) as product_group_name,gbr_product,GTM_VERTICAL_NAME  from gft_lead_hdr join gft_vertical_master on ( GLH_VERTICAL_CODE=GTM_VERTICAL_CODE)  " .
			" join gft_bvp_relation on (if(GTM_IS_MACRO='Y',GTM_VERTICAL_CODE,GTM_MICRO_OF)=gbr_vertical) join  gft_product_group_master on " .
			" (gpg_product_family_code=substring_index(gbr_product,'-',1) and gpg_skew=substring_index(gbr_product,'-',-1)) join gft_product_primary_master on (GPPM_PRODUCT_CODE=gpg_product_family_code) where glh_lead_code = $lead_code  ";
	$res=execute_my_query($ver_map_query);
	if($data=mysqli_fetch_array($res)){
		$gbr_product=$data['gbr_product'];
		if($gbr_product=='500-07.0'){
		    $not_allowed_arr = array('502-06.5','200-06.0','500-06.5','308-02.0','308-03.0','309-02.0','309-03.0');
		}else if ($gbr_product=='500-06.5'){
		    $not_allowed_arr = array('502-07.0','200-06.0','500-07.0','308-01.0','308-03.0','309-01.0','309-03.0');
		}else if($gbr_product=='200-06.0'){
		    $not_allowed_arr = array('502-06.5','500-06.5','500-07.0','308-01.0','308-02.0','309-01.0','309-02.0');
		}
	}
	return $not_allowed_arr;
}

/**
 * @param string $cust_id
 * @param string $post_url
 * @param string $http_method
 * @param string $post_data
 * @param string[int] $header_arr
 *
 * @return void
 */
function do_curl_to_alert($cust_id,$post_url,$http_method,$post_data,$header_arr){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $post_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $http_method);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr);
	$resp_arr['response_json'] = (string)curl_exec($ch);
	$resp_arr['response_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curl_error = curl_error($ch);
	if($curl_error!=''){
		$resp_arr['curl_error'] = $curl_error;
	}
	curl_close($ch);
	log_request($post_data, json_encode($resp_arr), '', $cust_id, 46);
}

/**
 * @param int $time_in_min
 * @return string
 */
function get_duration_string($time_in_min) {
	$hrs = (int)($time_in_min/60);
	$mins = (int)($time_in_min%60);
	$duration = "0";
	if($time_in_min>0) {
		$duration = "";
		if($hrs>0) {
			$duration .= $hrs." Hrs";
		}
		if($mins>0) {
			$duration .= ($duration=="")?$mins." Mins":",".$mins." Mins";
		}
	}
	return $duration;
}

/**
 * @param int $inputSeconds
 * 
 * @return void
 */
function get_duration_in_string_from_seconds($inputSeconds){
	
	$secondsInAMinute = 60;
	$secondsInAnHour  = 60 * $secondsInAMinute;
	$secondsInADay    = 24 * $secondsInAnHour;
	
	$days = floor($inputSeconds / $secondsInADay);
	
	$hourSeconds = $inputSeconds % $secondsInADay;
	$hours = floor($hourSeconds / $secondsInAnHour);
	
	$minuteSeconds = $hourSeconds % $secondsInAnHour;
	$minutes = floor($minuteSeconds / $secondsInAMinute);
	
	$remainingSeconds = $minuteSeconds % $secondsInAMinute;
	$seconds = ceil($remainingSeconds);

	$duration = "";
	if($days > 0) {
		$duration = "$days Days ";
	}
	if($hours > 0) {
		$duration .= "$hours Hrs ";
	}
	if($minutes > 0) {
		$duration .= "$minutes Mins ";
	}
	if($seconds > 0) {
		$duration .= "$seconds Secs ";
	}
	return $duration;
}

/**
 * @param string $emply_id
 * @param string $report_date
 * 
 * @return string
 */
function get_tomorrow_plan_for_daily_report($emply_id,$report_date){
	$sql1 = "select distinct GTH_PLAN_DATE from gft_tomorrow_plan_hdr where GTH_PLAN_DATE > '$report_date' AND GTH_EMP_ID='$emply_id' AND GTH_PLAN_STATUS='A' ";
	$hdr_res = execute_my_query($sql1);
	$output = "";
	while($data1 = mysqli_fetch_array($hdr_res)){
		$plan_date = $data1['GTH_PLAN_DATE'];
		if($plan_date==""){
			continue;
		}
		$plan_date_formatted = date('jS M, Y',strtotime($plan_date));
		$detail =	"<tr style='background-color:#80cbc4;'><th width='20%'>Task Timing</th><th>Task Name</th>".
					"<th>Work Mode</th><th>Plan & Purpose</th><th>Outcome</th></tr>";
		$sql1 = " select GTH_ID,GTH_START_TIME,GTH_END_TIME,GPT_NAME,pout.GTD_VALUE as outcome,phow.GTD_VALUE as work_mode,pdur.GTD_VALUE as duration_in_mins ".
				" from gft_tomorrow_plan_hdr ".
				" join gft_tomorrow_plan_tasks_master on (GPT_ID=GTH_TASK_ID) ".
				" left join gft_tomorrow_plan_dtl pout on (pout.GTD_PLAN_ID=GTH_ID and pout.GTD_FIELD_ID=4) ".
				" left join gft_tomorrow_plan_dtl phow on (phow.GTD_PLAN_ID=GTH_ID and phow.GTD_FIELD_ID=7) ".
				" left join gft_tomorrow_plan_dtl pdur on (pdur.GTD_PLAN_ID=GTH_ID and pdur.GTD_FIELD_ID=6) ".
				" where GTH_EMP_ID='$emply_id' and GTH_PLAN_DATE='$plan_date' and GTH_PLAN_STATUS='A' order by GTH_START_TIME ";
		$res1 = execute_my_query($sql1);
		$duration_arr = /*. (int[string]) .*/array();
		$work_mode_val_arr = array('1'=>"Online",'2'=>"Field");
		while($data1 = mysqli_fetch_array($res1)){
			$start_time	= date('h:i A',strtotime($data1['GTH_START_TIME']));
			$end_time 	= date('h:i A',strtotime($data1['GTH_END_TIME']));
			$timing		= $start_time . " to ". $end_time;
			$task_name	= $data1['GPT_NAME'];
			$outcome_val= $data1['outcome'];
			$plan_id	= $data1['GTH_ID'];
			$work_mode	= isset($work_mode_val_arr[$data1['work_mode']])?$work_mode_val_arr[$data1['work_mode']]:'';
			if(!isset($duration_arr[$task_name])){
				$duration_arr[$task_name] = 0;
			}
			$duration_arr[$task_name] += (int)$data1['duration_in_mins'];
			$additional_info = "";
			$dtl_query = " select GPF_NAME,GTD_VALUE,GTD_FIELD_ID ".
						 " from gft_tomorrow_plan_dtl ".
						 " join gft_tomorrow_plan_fields_master on (GTD_FIELD_ID=GPF_ID) ".
						 " where GTD_PLAN_ID='$plan_id' and GTD_VALUE!='' and GTD_FIELD_ID not in (4,5,6,7) ";
			$dtl_res = execute_my_query($dtl_query);
			while ($dtl_row = mysqli_fetch_array($dtl_res)){
				$key_value 	= $dtl_row['GTD_VALUE'];
				$field_id	= (int)$dtl_row['GTD_FIELD_ID']; 
				if($field_id==1){
					$key_value = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", $key_value);
				}else if($field_id==16){
					$key_value = get_single_value_from_single_table("GTM_VERTICAL_NAME", "gft_vertical_master", "GTM_VERTICAL_CODE", $key_value);
				}else if($field_id==17){
					$value_arr = explode('-',$key_value);
					if(count($value_arr) > 2){
						$pr_que=" SELECT concat(GPM_PRODUCT_ABR,' ',gpg_version,'-',GPT_TYPE_NAME) as name ".
								" from gft_product_family_master fm ".
								" join gft_product_group_master on (gpg_product_family_code=GPM_PRODUCT_CODE and gpg_status='A') ".
								" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=fm.GPM_PRODUCT_CODE and GFT_SKEW_PROPERTY=1 and pm.GPM_STATUS='A') ".
								" join gft_product_type_master on (GPT_TYPE_ID=GPM_PRODUCT_TYPE) ".
								" where fm.GPM_PRODUCT_CODE='$value_arr[0]' and gpg_skew='$value_arr[1]' and GPM_PRODUCT_TYPE='$value_arr[2]' ";
						$pr_res = execute_my_query($pr_que);
						if($pr_row = mysqli_fetch_array($pr_res)){
							$key_value = $pr_row['name'];
						}
					}
				}else if($field_id==8){
					if($key_value=='-1'){
						continue;
					}
					$sql2 = " select group_concat(GEM_EMP_NAME) as joint_emp from gft_tomorrow_plan_joint_exec_dtl ".
							" join gft_emp_master on (GEM_EMP_ID=GTJ_JOINT_EXEC_ID) where GTJ_PLAN_ID='$plan_id' ";
					$res2 = execute_my_query($sql2);
					if($row2 = mysqli_fetch_array($res2)){
						$key_value = $row2['joint_emp'];
					}
				}
				$additional_info .= "<b>".$dtl_row['GPF_NAME']." : </b> ".$key_value." <br> ";
			}
			
			$group_check = "select GTR_REMINDER_ID from gft_tomorrow_plan_next_action_relation where GTR_PLAN_ID='$plan_id'";
			$group_res = execute_my_query($group_check);
			$group_count = mysqli_num_rows($group_res);
			if($group_count > 1){
				$task_name = $task_name." <br> (Group Plan - $group_count Reminders) ";
			}
			$detail .= "<tr style='background-color:#e0f2f1;'><td style='padding:0 5px 0 10px'>$timing</td>".
						"<td style='padding:0 5px 0 10px'>$task_name</td>".
						"<td style='padding:0 5px 0 10px'>$work_mode</td>".
						"<td style='padding:0 5px 0 10px'>$additional_info</td>".
						"<td style='padding:0 5px 0 10px'>$outcome_val</td></tr>";
		}
		$total_mins = 0;
		$others = "<tr><td colspan=3><table align='center'>";
		foreach ($duration_arr as $key=>$val){
			$formatted_val = get_duration_string($val);
			$others .= "<tr><td>$key</td><td> : </td><td>$formatted_val</td></tr>";
			$total_mins += $val;
		}
		$others .= "</table></td></tr>";
		$total_formatted = get_duration_string($total_mins);
		$summary = " <tr style='background-color:#e0f2f1;'><td colspan=5><table align='center'><tr><th>Total Planned Duration</th><td> : </td><th>$total_formatted</th></tr>".
					" <tr><td style='font-style:italic;text-decoration:underline;' align='center' colspan=5>SUMMARY</td></tr> $others <tr><td colspan=3><br></td></tr> </table></td></tr>";
		$output .= " <table border=1 width='90%'><tr><th colspan=5 style='color:white;background-color:#009688;padding:5px;'>Plan for Next day ($plan_date_formatted) </th></tr>".
				  " $summary $detail </table>";
	}
	return $output;
	
}
/**
 * @param string $lead_code
 * @return boolean
 */
function check_kit_based_customer($lead_code) {
	$kit_based = false;
	$lead_type = get_lead_type_for_lead_code($lead_code);
	if($lead_type=='3') {
		if(is_kit_based_customer($lead_code)){
			$kit_based = true;
		}else{
			$delivery_type = (int)get_single_value_from_single_table("GLE_DELIVERY_TYPE", "gft_lead_hdr_ext", "GLE_LEAD_CODE", $lead_code);
			if($delivery_type==2){
				$kit_based = true;
			}
		}
	}
	return $kit_based;
}

/**
 * @param string $lead_code
 * @param string $vertical_code
 *
 * @return boolean
 */
function is_vertical_supported_for_kit_based($lead_code,$vertical_code=''){
	if($vertical_code!=''){
		$sql1 = " select GTM_KIT_ENABLED from gft_vertical_master ".
				" where GTM_VERTICAL_CODE='$vertical_code' and GTM_KIT_ENABLED=1 ";
	}else{
		$sql1 = " select GTM_KIT_ENABLED from gft_lead_hdr ".
				" join gft_vertical_master on (GTM_VERTICAL_CODE=GLH_VERTICAL_CODE) ".
				" where GLH_LEAD_CODE='$lead_code' and GTM_KIT_ENABLED=1 ";
	}
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $mobile_number
 * @param int $app_code
 *
 * @return boolean
 */
function is_app_installed_number($mobile_number, $app_code){
	$mobile_cond = getContactDtlWhereCondition("GAI_MOBILE", $mobile_number);
	$sql1 = " select GAI_MOBILE from gft_app_installed_dtl where GAI_APP_PCODE='$app_code' and ($mobile_cond) ";
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1) > 0){
		return true;
	}
	return false;
}
/**
 * @param string $employee_id
 * @param string $time_diff_hrs
 * @param string $last_submitted
 * 
 * @return void
 */
function get_last_submitted_time_of_dcr($employee_id,&$time_diff_hrs,&$last_submitted){
	$que_res = execute_my_query("select GDR_DATETIME, TIMESTAMPDIFF(HOUR, GDR_DATETIME, now()) as diff from gft_daily_report where GDR_EMP_ID='$employee_id' order by GDR_ID desc limit 1");
	if($row = mysqli_fetch_array($que_res)){
		$last_submitted = $row['GDR_DATETIME'];
		$time_diff_hrs	= (int)$row['diff'];
	}
}
/**
 * @param string $emply_id
 * @param string $last_submitted
 * @param string $report_date
 *
 * @return string
 */
function get_pending_order_creation_leads($emply_id,$last_submitted,$report_date){
	$receipt_ids = get_samee_const("Skip_Receipt_Id_For_DR_Validation");
	$order_pending_text="";
	$advance_amt = 0;
	$sql_order_pending	=	" select GLH_LEAD_CODE, GLH_CUST_NAME, a.GEM_EMP_NAME,if(GOD_ORDER_NO is null,GCR_AMOUNT,0) advance_amt, ".
			" GRD_REALIZED_DATE from gft_receipt_dtl ".
			" join gft_lead_hdr on ( GLH_LEAD_CODE=GRD_LEAD_CODE) ".
			" join gft_collection_receipt_dtl on ( GCR_RECEIPT_ID=GRD_RECEIPT_ID) ".
			" join  gft_emp_master a  on ( a.GEM_EMP_ID=GRD_EMP_ID ) ".
			" left join gft_order_hdr on (GCR_ORDER_NO=GOD_ORDER_NO) ".
			" where GCR_REASON='-1' and GLH_LEAD_TYPE in ( 1,3,13) and GCR_AMOUNT > 0 and a.gem_emp_id='$emply_id' ".
			($receipt_ids!=""?" AND GRD_RECEIPT_ID NOT IN($receipt_ids)":"").
			" AND GRD_REALIZED_DATE<='$report_date 23:59:59' AND GRD_CHECKED_WITH_LEDGER='Y' and GOD_ORDER_NO is null ".
			" group by GRD_RECEIPT_ID,GOD_ORDER_NO";
	$result_order_pending = execute_my_query($sql_order_pending);
	if(mysqli_num_rows($result_order_pending)>0){
		while($row_order_pending=mysqli_fetch_array($result_order_pending)){
		    $pending_adv_amnt = (int)$row_order_pending['advance_amt'];
		    if($pending_adv_amnt>99){
		        $order_pending_text.=	$row_order_pending['GLH_LEAD_CODE'].",";
		        $advance_amt = $advance_amt+$pending_adv_amnt;
		    }
		}
		$order_pending_text=rtrim($order_pending_text,',');
		if($advance_amt<100){
			$order_pending_text = "";
		}
	}
	return $order_pending_text;
}

/**
 * @param string $employee_id_list
 *
 * @return void
 */
function sync_employees_to_authservice($employee_id_list){
	global $log,$mydelight_tm_cust_id;
	if($employee_id_list==""){
		return;
	}
	$connect_post_arr = /*. (string[string]) .*/array();
	$customer_name = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "glh_lead_code", $mydelight_tm_cust_id);
	$que1 = " select lm.GLM_LOGIN_NAME as userName,lm.GLM_PASSWORD,GEM_EMP_ID,GEM_EMP_NAME,GEM_MOBILE,GEM_EMAIL,GEM_STATUS, ".
			" GEM_ROLE_ID,GRM_ROLE_DESC,GER_REPORTING_EMPID,ifnull(rm.GLM_LOGIN_NAME,0) as repUserName from gft_emp_master ".
			" join gft_login_master lm on (lm.GLM_EMP_ID=GEM_EMP_ID) ".
			" join gft_role_master on (GRM_ROLE_ID=GEM_ROLE_ID) ".
			" left join gft_emp_reporting on (GER_EMP_ID=GEM_EMP_ID and GER_STATUS='A') ".
			" left join gft_login_master rm on (rm.GLM_EMP_ID=GER_REPORTING_EMPID) ".
			" where GEM_EMP_ID in ($employee_id_list) ";
	$res1 = execute_my_query($que1);
	while ($data1 = mysqli_fetch_array($res1)){
		$arr = /*. (string[string]) .*/array();
		$arr['empId']			= $data1['GEM_EMP_ID'];
		$arr['userId'] 			= $data1['userName'];
		$arr['userName']		= $data1['GEM_EMP_NAME'];
		$arr['password'] 		= "secured"; //this won't be used in authservice. They will check back again in API only
		$arr['customerId'] 		= $mydelight_tm_cust_id;
		$arr['customerName'] 	= $customer_name;
		$arr['roleId'] 			= $data1['GEM_ROLE_ID'];
		$arr['role'] 			= $data1['GRM_ROLE_DESC'];
		$arr['hasSystemAccess'] = true;
		$arr['enabled'] 		= ($data1['GEM_STATUS']=='A')?true:false;
		$arr['mobileNumber'] 	= $data1['GEM_MOBILE'];
		$arr['emailId'] 		= $data1['GEM_EMAIL'];
		$arr['baseProductId'] 	= '702-01.0';
		$arr['reportingTo']		= $data1['repUserName'];
		$arr['reportingType']	= "USER";
		$arr['groups'] 			= array();
		$arr['customerType']    = "CRM";
		$connect_post_arr[]		= $arr;
	}
	post_user_arr_to_connectplus($connect_post_arr,$mydelight_tm_cust_id,$log);
}

/**
 * @param string $user_id_list
 *
 * @return void
 */
function sync_mygofrugal_users_to_authservice($user_id_list){
	global $log,$mygofrugal_task_manger_customer_id;
	if($user_id_list==""){
		return;
	}
	$user_id_arr = explode(",", $user_id_list);
	$id_cond = "";
	if(count($user_id_arr) > 0){
		$id_cond = "'".implode("','", $user_id_arr)."'";
	}
	if($id_cond==""){
		return;
	}
	$connect_post_arr = /*. (string[string]) .*/array();
	$customer_name = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "glh_lead_code", $mygofrugal_task_manger_customer_id);
	$que1 = " select GCL_USER_ID,GCL_USERNAME,GCL_PASSWORD,GCL_FIRST_NAME,GCL_USER_STATUS, ".
			" GCL_ACCESS_ROLE,GCA_ROLE_NAME from gft_customer_login_master ".
			" join gft_customer_role_master on (GCR_ROLE_ID=GCL_ACCESS_ROLE) ".
			" where GCL_USERNAME in ($id_cond) ";
	$res1 = execute_my_query($que1);
	while ($data1 = mysqli_fetch_array($res1)){
		$gcl_username = $data1['GCL_USERNAME'];
		$arr = /*. (string[string]) .*/array();
		$arr['empId']           = $data1['GCL_USER_ID'];
		$arr['userId'] 			= $gcl_username;
		$arr['userName']		= (string)$data1['GCL_FIRST_NAME'];
		$arr['password'] 		= md5(generate_OTP(4)); //random string
		$arr['customerId'] 		= $mygofrugal_task_manger_customer_id;
		$arr['customerName'] 	= $customer_name;
		$arr['roleId'] 			= $data1['GCL_ACCESS_ROLE'];
		$arr['role'] 			= $data1['GCA_ROLE_NAME'];
		$arr['hasSystemAccess'] = true;
		$arr['enabled'] 		= false;//($data1['GCL_USER_STATUS']=='1')?true:false;  //enabled sent as false temporarily as requested by Saravanan and Partha for their whatsnow task manager release
		$arr['mobileNumber'] 	= is_numeric($gcl_username)?$gcl_username:'';
		$arr['emailId'] 		= is_valid_email($gcl_username)?$gcl_username:'';
		$arr['baseProductId'] 	= '704-01.0';
		$arr['groups'] 			= array();
		$arr['customerType']    = "CRM";
		$connect_post_arr[]		= $arr;
	}
	if(count($connect_post_arr) > 0){
		$admin_user_arr = /*. (string[string]) .*/array();
		$admin_user_arr['empId']            = '1';
		$admin_user_arr['userId'] 			= "admin";
		$admin_user_arr['userName']			= "Administrator";
		$admin_user_arr['password'] 		= "gft12345";
		$admin_user_arr['customerId'] 		= $mygofrugal_task_manger_customer_id;
		$admin_user_arr['customerName'] 	= $customer_name;
		$admin_user_arr['roleId'] 			= "100";
		$admin_user_arr['role'] 			= "admin";
		$admin_user_arr['hasSystemAccess'] 	= true;
		$admin_user_arr['enabled'] 			= true;
		$admin_user_arr['mobileNumber'] 	= '4466200200';
		$admin_user_arr['emailId'] 			= '';
		$admin_user_arr['baseProductId'] 	= '704-01.0';
		$admin_user_arr['groups'] 			= array();
		$admin_user_arr['customerType']     = "CRM";
		$connect_post_arr[] = $admin_user_arr; 
		post_user_arr_to_connectplus($connect_post_arr,$mygofrugal_task_manger_customer_id,$log);
	}
}

/**
 * 
 * @param int $size
 * @param int $precision
 * 
 * @return string
 */
function formatBytes($size, $precision = 2){
	if($size==0){
		return '0';
	}
	$base = log($size, 1024);
	$suffixes = array('Bytes', 'KB', 'MB', 'GB', 'TB');
	$val = round(pow(1024, $base - floor($base)), $precision);
	$suf = isset($suffixes[(int)floor($base)])?$suffixes[(int)floor($base)]:"";
	return  "$val ".$suf;
}

/**
 * @param string $msg
 * @param boolean $close_window
 * @return void
 */
function show_alert_and_close($msg,$close_window=true) {
	show_my_alert_msg($msg);
	if($close_window) {
		close_the_popup();
	}
}

/**
 * @param string $quotation_no
 * @param string $status
 * @param string $purpose
 * @return boolean
 */
function check_tax_mode_after_gst($quotation_no,$status,$purpose='quotation') {
	global $global_gst_mode;
	$valid = true;
	$to_fetch = "GQH_TAX_MODE";
	$table = "gft_quotation_hdr";
	$cond_col = "gqh_order_no";
	if($purpose == 'proforma') {
		$to_fetch = "GPH_TAX_MODE";
		$table = "gft_proforma_hdr";
		$cond_col = "gph_order_no";
	}
	if($global_gst_mode==1) {
		if($quotation_no!='') {
			$tax_type = get_single_value_from_single_table("$to_fetch", "$table", "$cond_col", "$quotation_no");
			if(in_array($tax_type,array('1','2')) and $status!='C') {
				$valid = false;
			}
		} else {
			$valid = false;
		}
	}
	return $valid;
}
/**
 * @param string $lead_code
 * @param string $lead_type
 * @param string $emp_id
 * @param string $quotation_no
 * @param string $formtype
 * @param boolean $from_app
 * @param string $receive_param
 * @param string $purpose 
 *
 * @return mixed[string]
 */
function validate_quotation_new($lead_code,$lead_type,$emp_id,$quotation_no='',$formtype='new',$from_app=false,$receive_param='',$purpose='quotation') {
	global $global_gst_mode;
	$alert_msg 	= '';
	$status		= true;
	$kit_based = false;
	$ret_arr = /*.(mixed[string]).*/array();
	$cust_dtl=customerContactDetail($lead_code,1,$from_app);
	if(count($cust_dtl)>0) {
		$custemail = array_filter(explode(",",$cust_dtl['EMAIL']));
		$mobile_no = array_filter(explode(",",$cust_dtl['mobile_no']));
		$cust_pincode = $cust_dtl['pincode'];
		$cust_country = $cust_dtl['COUNTRY_NAME'];
		$cust_state_code = (int)$cust_dtl['cust_state_code'];
		if($formtype=='new'){
			if(count($custemail)==0) {
				$alert_msg = 'Email ID with Designation as Proprietor is not available for this Customer. Please add/update the Designation as Proprietor for Email ID and then create Quotation / Proforma.';
			} 
			if(strcasecmp($cust_country,"India")==0) {
				if($cust_state_code==0 && $global_gst_mode==1) {
					$alert_msg = "The state code of this customer is not available. Please update the customer details with correct state information.";
				} else if(count($mobile_no)==0) {
					$alert_msg = 'Mobile Number with Designation as Proprietor is not available for this Customer. Please add/update the Designation as Proprietor for Mobile number and then create Quotation / Proforma.';
				}
			}
		}
		$vertical_code = get_single_value_from_single_table('GLH_VERTICAL_CODE', 'gft_lead_hdr', 'GLH_LEAD_CODE', $lead_code);
		if( ($emp_id!=SALES_DUMMY_ID) && is_authorized_group_list($emp_id, array(66,106,6)) && !is_chain_certified_employee($emp_id,$vertical_code) ){
			$ord_dtl = get_order_dtl_of_lead($lead_code,array('1','11'),true,'','',false,'',null,array('300','308'));
			if(count($ord_dtl) > 0){
				$alert_msg = "You are not authorized to place quotation for this customer since you are not an HQ certified executive for this vertical";
			}
		}
		if($purpose=='quotation') {
			$quotation_status=(int)get_single_value_from_single_table("GQH_APPROVAL_STATUS","gft_quotation_hdr","GQH_ORDER_NO",$quotation_no);
			if($quotation_status==4){
				$alert_msg = "Sorry.You cannot Edit the Quotation that is converted to Order";
			} else if($lead_type=='13' and $formtype!='edit'){
				$alert_msg = "The Customer type Seems as Corporate Client. Please place Quotation in the name of Corporate Customer as splitable Order ! ";
			} else if($lead_type=='3'){
				$kit_based = check_kit_based_customer($lead_code);
				if($kit_based){
					if(!is_vertical_supported_for_kit_based($lead_code)){
						$alert_msg = "Vertical of this customer is not supported for Kit based RCM orders. So please update proper vertical and continue the quotation entry ";
					}
				}
			} else if(strcasecmp($cust_country,"India")==0 and ($cust_pincode=='' or strlen($cust_pincode)<6)) {
				$alert_msg = 'This customer does not have pin code. Please update valid pin code and then create quotation.';
			} else if($formtype=='new'){
				$chk_que = "select GQH_ORDER_NO from gft_quotation_hdr where GQH_LEAD_CODE='$lead_code' and GQH_EMP_ID='$emp_id' and GQH_ORDER_STATUS='A' and GQH_APPROVAL_STATUS is not null and (GQH_CONVERTED_ORDER_NO is null or GQH_CONVERTED_ORDER_NO='') ";
				$chk_res = execute_my_query($chk_que);
				if(mysqli_num_rows($chk_res)==1 and $lead_type!='3'){
					$row1 = mysqli_fetch_array($chk_res);
					$quote_no = $row1['GQH_ORDER_NO'];
					$alert_msg = "Quotation (Number: $quote_no) already created. So create new Quotation after the Quotation to Order conversion.";
				}
			}
		}
	} else {
		$alert_msg = "Contact details with Designation as Proprietor is not available for this Customer. Please add/update the Designation as Proprietor for Mobile number and Email id and then create Quotation / Proforma.";
	}
	if($alert_msg!='') {
		if($from_app) {
			send_mobile_error_msg($receive_param, $alert_msg, HttpStatusCode::BAD_REQUEST);
		} else {
			show_alert_and_close($alert_msg);
		}
		$status = false;
	}
	$ret_arr['valid'] 		=	$status;
	$ret_arr['kit_based']	=	$kit_based;
	return $ret_arr;
}

/**
 * @param string $customer_id
 *
 * @return string
 */
function get_existing_installation_detail($customer_id) {
	$ret_value = '';
	if( ($customer_id!='') && is_numeric($customer_id)){
		$sel_query =" select GID_LIC_PCODE, GID_LIC_PSKEW from ".
				" gft_install_dtl_new ".
				" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GID_LIC_PCODE and pm.GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
				" where GID_LEAD_CODE='$customer_id' and GID_STATUS!='U' and GPM_ORDER_TYPE=0 and GPM_LICENSE_TYPE=3 ";
		$res = execute_my_query($sel_query);
		$i=0;
		while($row_data = mysqli_fetch_array($res)){
			if($i!=0){
				$ret_value .= "**";
			}
			$ret_value .= $row_data['GID_LIC_PCODE'].'-'.$row_data['GID_LIC_PSKEW'];
			$i++;
		}
	}
	return $ret_value;
}

/**
 * @param string $lead_code
 * @param string[int] $product_dtls
 * @param boolean $skip_country_condition
 * @return string[string][string]
 */
function get_gst_split_for_lead_code($lead_code,$product_dtls, $skip_country_condition=false) {
	global $global_gst_mode;
	$gst_split = /*.(string[string][string]).*/array();
	$cust_country = get_single_value_from_single_table("GLH_COUNTRY", "gft_lead_hdr", "glh_lead_code", $lead_code);
	if( ((strcasecmp($cust_country,"India")==0) || ($skip_country_condition)) && ($global_gst_mode==1) ){
		$is_same_state = is_same_state($lead_code);
		foreach ($product_dtls as $vals) {
			$prod_dtl = explode("-",$vals);
			$pcode 	  = $prod_dtl[0];
			$pskew	  = $prod_dtl[1];
			$tax_master_qry = execute_my_query(" select GTM_CGST,GTM_SGST,GTM_IGST,GTM_CESS from gft_tax_type_master ".
											   " join gft_hsn_vs_tax_master on (GHT_TAX_ID=GTM_ID) ".
											   " join gft_product_master on (GPM_TAX_ID=GHT_ID) ".
											   " where GPM_PRODUCT_CODE='$pcode' and GPM_PRODUCT_SKEW='$pskew' ");
			$cgst = $sgst = $igst = $cess = '0';
			if($row = mysqli_fetch_array($tax_master_qry)){
				$cess = $row['GTM_CESS'];
				if($is_same_state){
					$sgst = $row['GTM_SGST'];
					$cgst = $row['GTM_CGST'];
				}else{
					$igst = $row['GTM_IGST'];
				}
			}
			$gst_split[$vals]['cgst'] = $cgst;
			$gst_split[$vals]['sgst'] = $sgst;
			$gst_split[$vals]['igst'] = $igst;
			$gst_split[$vals]['cess'] = $cess;
		}
	}
	return $gst_split;
}
/**
 * @param string $lead_code
 * @return boolean
 */
function is_same_state($lead_code) {
	$company_state 	= get_single_value_from_single_table("gcm_state_code", "gft_company_master", "gcm_id", "1");
	$cust_state_code_qry = execute_my_query(" select gpm_gst_state_code from gft_political_map_master ".
											" join gft_lead_hdr on (glh_cust_statecode=gpm_map_name and gpm_map_type='S') ".
											" where glh_lead_code='$lead_code' and glh_country='India' ");
	$cust_state_code = '0';
	while($row = mysqli_fetch_array($cust_state_code_qry)) {
		$cust_state_code = $row['gpm_gst_state_code'];
	}
	if($cust_state_code==$company_state) {
		return true;
	}
	return false;
}
/**
 * @param string $state_code
 * 
 * @return string
 */
function check_gstin_state_code($state_code){
	$cust_state_code_qry = execute_my_query(" select gpm_gst_state_code from gft_political_map_master ".
			" join gft_lead_hdr on (glh_cust_statecode=gpm_map_name and gpm_map_type='S') ".
			" where gpm_gst_state_code='$state_code' and glh_country='India' ");
	$cust_state_code = '0';
	while($row = mysqli_fetch_array($cust_state_code_qry)) {
		$cust_state_code = $row['gpm_gst_state_code'];
	}
	return $cust_state_code;
}
/**
 * @param string $quotation_no
 * @param string $quotation_status
 * @param boolean $close_window
 * @param boolean $from_app
 * @param string $receive_arg
 * @param string $purpose
 * @return boolean
 */
function quotation_submit_check($quotation_no,$quotation_status,$close_window=true,$from_app=false,$receive_arg='',$purpose='quotation') {
	$ok_to_submit = check_tax_mode_after_gst($quotation_no,$quotation_status,$purpose);
	if(!$ok_to_submit) {
		$alert_msg = "It is not allowed to edit old quotation/proforma when GST is live. Please cancel this quotation/proforma and create a new one with required details.";
		if($from_app) {
			send_mobile_error_msg($receive_arg, $alert_msg, HttpStatusCode::BAD_REQUEST);
		} else {
			show_alert_and_close($alert_msg);
		}
	}
	return $ok_to_submit;
}

/**
 * @param string $contact_no
 * 
 * @return string[int]
 */
function get_all_leadcode_for_contact_info($contact_no){
	$contact_cond = " and GCC_CONTACT_NO='$contact_no' ";
	if(is_numeric($contact_no)){
		$contact_cond = " and ".getContactDtlWhereCondition("GCC_CONTACT_NO", $contact_no);
	}
	$sql1 = "select GCC_LEAD_CODE from gft_customer_contact_dtl where 1 $contact_cond ";
	$res1 = execute_my_query($sql1);
	$lead_arr = /*. (string[int]) .*/array();
	while ($row1 = mysqli_fetch_array($res1)){
		$lead_arr[] = $row1['GCC_LEAD_CODE'];
	}
	return $lead_arr;
}
/**
 * @param string[int] $contact_no_arr
 * @param string[int] $lead_codes
 * @return string[int]
 */
function check_eligibility_for_wns($contact_no_arr=null,$lead_codes=null) {
	$chk_val = (is_array($lead_codes) and $lead_codes!=null)?$lead_codes:((is_array($contact_no_arr) and $contact_no_arr!=null)?$contact_no_arr:null);
	$ns_id_arr = /*. (string[int]) .*/array();
	if($chk_val!==null and is_array($chk_val)) {
		foreach ($chk_val as $val) {
			$contact_lead_condition = '';
			if(is_array($lead_codes) and count($lead_codes)>0) {
				$contact_lead_condition = " gid_lead_code='$val' ";
			} else if(is_array($contact_no_arr) and count($contact_no_arr)>0) {
				$contact_lead_condition = getContactDtlWhereCondition("GCC_CONTACT_NO", $val);
			}
			$sql2 = " select GID_LEAD_CODE,GID_LIC_PCODE,GID_LIC_PSKEW,GID_CURRENT_VERSION from gft_install_dtl_new ".
					" join gft_customer_contact_dtl on (GCC_LEAD_CODE=GID_LEAD_CODE) ".
					" where GID_STATUS!='U' and $contact_lead_condition and GID_LIC_PCODE in (200,500,300) GROUP BY GID_LEAD_CODE,GID_LIC_PCODE";
			$res2 = execute_my_query($sql2);
			while ($row2 = mysqli_fetch_array($res2)){
				$lic_pcode 		= (int)$row2['GID_LIC_PCODE'];
				$lic_pskew 		= substr($row2['GID_LIC_PSKEW'], 0,4);
				$current_ver	= get_valid_version($row2['GID_CURRENT_VERSION']);
				$wns_enabled = false;
				if($lic_pcode==200){
					$wns_enabled = version_compare($current_ver, "6.1.1.2",">=");
				}else if($lic_pcode==300){
					$wns_enabled = version_compare($current_ver, "63.53",">=");
				}else if( ($lic_pcode==500) && ($lic_pskew=='06.5') ){
					if(substr_count($current_ver, ".") < 2){ //since some versions stored as 6587.3
						$current_ver = substr($current_ver, 0,1).".".substr($current_ver, 1,1).".".substr($current_ver, 2,1).".".substr($current_ver, 3);
					}
					$wns_enabled = version_compare($current_ver, "6.5.8.7.1",">=");
				}else if( ($lic_pcode==500) && ($lic_pskew=='07.0') ){
					$wns_enabled = version_compare($current_ver, "7.0.0.98.9",">=");
				}
				if($wns_enabled){
					$ns_id_arr[] = $row2['GID_LEAD_CODE'];
				}
			}
		}
	}
	return $ns_id_arr;
}
/**
 * @param string $contact_no
 * @param string $lead_code
 * 
 * @return mixed[string]
 */
function get_active_contact_for_customer($contact_no,$lead_code="") {
	$contact_no_arr = explode(",",$contact_no);
	$resp_arr = /*.(mixed[string]).*/array();
	$mygofrugal_str = "Not Using";
	$mygofrugal_user_id = '0';
	$wns_str = "Not Using";
	$sms_str="Using";
	$notify_cust_ids = '';
	$ns_id_arr = /*. (string[int]) .*/array();
	$separator = '';
	foreach ($contact_no_arr as $contact_no1) {
	if(is_numeric(str_replace("+", "", $contact_no1))){
		$contact_login_condition = getContactDtlWhereCondition("GCL_USERNAME", $contact_no1);
		$sql1 = " select GCL_USER_ID from gft_customer_login_master where 1 and $contact_login_condition ";
		$res1 = execute_my_query($sql1);
		if($row1 = mysqli_fetch_array($res1)){
			$mygofrugal_user_id .= "$separator".(int)$row1['GCL_USER_ID'];
			$separator = ",";
			$mygofrugal_str = "Using";
		}
	}
	}
	$ns_id_arr = check_eligibility_for_wns($contact_no_arr,array($lead_code));
	if($contact_no=="" && $lead_code!=""){
		$sql1 = " select GCA_USER_ID from gft_customer_access_dtl where GCA_ACCESS_LEAD='$lead_code' AND GCA_ACCESS_STATUS=1 group by GCA_USER_ID ";
		$res1 = execute_my_query($sql1);
		while($row1 = mysqli_fetch_array($res1)){
			$mygofrugal_user_id .= "$separator".(int)$row1['GCA_USER_ID'];
			$separator = ",";
			$mygofrugal_str = "Using";
		}
		$ns_id_arr = check_eligibility_for_wns(null,array($lead_code));
		$sql_contact = execute_my_query("select  GCC_CONTACT_NAME,GCC_CONTACT_NO from gft_customer_contact_dtl where gcc_contact_type=1 AND GCC_LEAD_CODE='$lead_code'");
		if(mysqli_num_rows($sql_contact)==0){
			$sms_str="Not Using";
		}
		while($row_con=mysqli_fetch_array($sql_contact)){
			$contact_no_arr[] = $row_con['GCC_CONTACT_NO'];
		}
	}
	if(count($ns_id_arr)>0) {
		$wns_str = "Using";
	}
	$notify_cust_ids = implode(",", $ns_id_arr);
	$resp_arr['sms'] = $sms_str;
	$resp_arr['mobile_no_list'] = $contact_no_arr;
	$resp_arr['mygofrugal'] = $mygofrugal_str;
	$resp_arr['mygofrugal_user_id'] = "$mygofrugal_user_id";
	$resp_arr['wns'] = $wns_str;
	$resp_arr['wns_cust_ids'] = $notify_cust_ids;
	return $resp_arr;	
}

/**
 * @param string $mobile_no
 * 
 * @return void
 */
function update_recall_status_for_cloud_call($mobile_no){
	$trimmed_mob_no = ltrim($mobile_no,'0');
	execute_my_query("update gft_techsupport_incomming_call set GTC_RECALL_STATUS='Y',GTC_RECALL_REMARKS='Chatbot' ".
			" where GTC_AGENT_ID=9999 and GTC_NUMBER in ('$mobile_no','$trimmed_mob_no') and GTC_CALL_STATUS=1 and GTC_RECALL_STATUS='N' ");
}

/**
 * @param string $lead_code
 * @param string $contact_no
 * 
 * @return string
 */
function get_contact_name_for_customer($lead_code, $contact_no=""){
	$where_con = "";
	if($contact_no!=""){
		$where_con = " AND".contact_info_where_condition("GCC_CONTACT_NO", $contact_no);
	}	
	$sql1 = "select distinct GCC_CONTACT_NAME from gft_customer_contact_dtl where GCC_LEAD_CODE='$lead_code'  and GCC_CONTACT_NAME!='' $where_con order by gcc_designation ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		return 	$row1['GCC_CONTACT_NAME'];
	}
	return '';
}

/**
 * @param string $cust_id
 * @param string $mobile_no
 * @param string $pcode
 * 
 * @return string[string]
 */
function get_otp_and_timestamp_for_authservice($cust_id,$mobile_no,$pcode){
	$sql1 = " select GPR_OTP_CODE,GPR_CREATED_DATE from gft_presignup_registration ".
			" where GPR_LEAD_CODE='$cust_id' and GPR_OTP_MOBILENO='$mobile_no' order by GPR_CREATED_DATE desc limit 1 ";
	$res1 = execute_my_query($sql1);
	$ret_arr = /*. (string[string]) .*/array();
	if($row1 = mysqli_fetch_array($res1)){
		$ret_arr['otp'] 			= $row1['GPR_OTP_CODE'];
		$ret_arr['otp_timestamp'] 	= $row1['GPR_CREATED_DATE'];
	}else{
		$today_date = date('Y-m-d H:i:s');
		$otp_val = generate_OTP(5);
		$ins_arr['GPR_LEAD_CODE'] 			= $cust_id;
		$ins_arr['GPR_OTP_CODE'] 			= $otp_val;
		$ins_arr['GPR_OTP_MOBILENO']		= $mobile_no;
		$ins_arr['GPR_PCODE']				= $pcode;
		$ins_arr['GPR_CREATED_DATE']		= $today_date;
		$ins_arr['GPR_ACTIVATION_STATUS'] 	= "I";
		array_insert_query("gft_presignup_registration", $ins_arr);
		$ret_arr['otp'] 			= $otp_val;
		$ret_arr['otp_timestamp'] 	= $today_date;
	}
	return $ret_arr;
}

/**
 *
 * @return string
 */
function get_product_version_above(){
	$return_string = "";
	$seperator="";
	$pcode1 = array('500','500','300','200');
	$version1 = array('7.0.0.RC99.5','6.5.8.8_2','3.0.0.RC63.53','6.1.2.9');
	$major_version1= array('7','6','3','6');
	for($inc=0;$inc<count($pcode1);$inc++){
		$pcode=$pcode1[$inc];
		$major_version=$major_version1[$inc];
		$version=$version1[$inc];
		$sql_query = "select pv1.GPV_VERSION from gft_product_version_master pv1
		left join gft_product_version_master pv2 ON(pv2.GPV_PRODUCT_CODE='$pcode' AND pv2.GPV_MAJOR_VERSION='$major_version'".
		" AND pv2.GPV_VERSION='$version') ".
		" where pv1.GPV_PRODUCT_CODE='$pcode' AND pv1.GPV_MAJOR_VERSION='$major_version' and pv1.GPV_RELEASE_DATE>pv2.GPV_RELEASE_DATE";
		$result = execute_my_query($sql_query);
		while($row=mysqli_fetch_array($result)){
			$return_string .="$seperator'".$row['GPV_VERSION']."'";
			$seperator = ",";
		}
	}
	return $return_string;
}
/**
 * @param string[int] $columns
 * @param string $table_name
 * @param string[int] $condition_columns
 * @param string[int] $condition_values
 * @return string[int][string]
 */
function get_data_from_table($columns,$table_name,$condition_columns,$condition_values) {
	$select_columns = implode(",",$columns);
	$conditions = '';
	$result_arr = /*.(string[int][string]).*/array();
	if(count($columns)==0 || $table_name=='' || count($condition_columns)!=count($condition_values)) {
		$result_arr[0]['error'] = 'query error';
	} else {
		foreach($condition_columns as $k=>$v) {
			$conditions .= " and $v='".mysqli_real_escape_string_wrapper($condition_values[$k])."' ";
		}
		$query = " select $select_columns from $table_name where (1) $conditions ";
		$query_res = execute_my_query($query);
		if(!$query_res) {
			$result_arr[0]['error'] = 'query/DB error';
		} else {
			while($row = mysqli_fetch_array($query_res)) {
				$this_row = /*.(string[string]).*/array();
				foreach ($row as $key=>$v) {
					if(is_numeric($key)) {
						continue;
					}
					$this_row["$key"] = $v;
				}
				$result_arr[] = $this_row;
			}
		}
	}
	return $result_arr;
}
/**
 * @param string $order_no
 * @return boolean[string]
 */
function get_product_types_in_order($order_no) {
	$query = " select distinct gft_skew_property from gft_order_product_dtl ".
			 " join gft_product_master on (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew) ".
			 " where gop_order_no='$order_no' ";
	$server_only_ppty = array('1','11');
	$res = execute_my_query($query);
	$result_arr = array('server'=>false,'non_server'=>false,'upgradation'=>false);
	while($row = mysqli_fetch_array($res)) {
		if($row['gft_skew_property']!==null and $row['gft_skew_property']!='' and $row['gft_skew_property']!='0') {
			if(in_array($row['gft_skew_property'],$server_only_ppty)) {
				$result_arr['server'] = true;
			} else if($row['gft_skew_property']=='2') {
				$result_arr['upgradation'] = true;
			} else {
				$result_arr['non_server'] = true;
			}
		}
	}
	return $result_arr;
}
/**
 * @param string $order_no
 * @param string $gpm_product_skew
 * @param string $lead_code
 * 
 * @return boolean
 */
function check_hq_installations_for_invoice($order_no,$gpm_product_skew,$lead_code) {
	$hq_lead_codes_qry = execute_my_query(" select GCO_CP_LEAD_CODE, GCO_CUST_CODE from gft_cp_order_dtl ".
						" join gft_install_dtl_new on (GID_LEAD_CODE = GCO_CUST_CODE  and GCO_PRODUCT_CODE = GID_LIC_PCODE and GCO_SKEW = GID_LIC_PSKEW) ".
						" where GID_LIC_PCODE=300 and GCO_CP_LEAD_CODE='$lead_code' and GID_STATUS!='U' and GID_LIC_PSKEW = '$gpm_product_skew' ");
	$cond = (mysqli_num_rows($hq_lead_codes_qry)>0)?" and gid_lead_code in (":'';
	$comma = '';
	while ($row = mysqli_fetch_array($hq_lead_codes_qry)) {
		$cond .= "$comma'".$row['GCO_CUST_CODE']."'";
		$comma = ',';
	}
	$cond .= (mysqli_num_rows($hq_lead_codes_qry)>0)?")":'';
	if($cond!='') {
		$hq_instaleld_qry = execute_my_query(" select * from gft_install_dtl_new where gid_lic_pcode='300' and gid_lic_pskew='$gpm_product_skew' and gid_status='A' $cond ");
		if(mysqli_num_rows($hq_instaleld_qry)>0) {
			return true;
		}
	} else {
		return false;
	}
	return false;
}
/**
 * @param string $from_dt
 * @param string $to_dt
 * @param string[int] $lead_types
 * @return string[string][string]
 */
function get_order_no_date_for_invoice_data_patch($from_dt,$to_dt,$lead_types=array('1')) {
	global $log;
	$result_arr = /*.(string[string][string]).*/array('generate'=>array(),'later'=>array(),'no_generate'=>array());
	$from_dt = db_date_format($from_dt);
	$to_dt = db_date_format($to_dt);
	$lead_types_cond = $lead_code = '';
	if(count($lead_types)>0) {
		$lead_types_cond .= " and glh_lead_type in ('".implode("','",$lead_types)."') ";
	}
	$qry = " select god_order_no,god_lead_code,group_concat(if(pm.gft_skew_property='11' and pm.gpm_product_type='8','-1', ".
		   " if(pm.gft_skew_property='1' and pfm.gpm_is_base_product='N','-1',gft_skew_property))) skew_ppty,god_order_date, ".
		   " god_order_splict,group_concat(pm.gpm_product_code) prod_codes,glh_lead_type from gft_order_hdr ".
		   " join gft_order_product_dtl on (gop_order_no=god_order_no and gop_sell_amt>0) ".
		   " join gft_lead_hdr on (god_lead_code=glh_lead_code) ".
		   " join gft_product_master pm on (gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew) ".
		   " join gft_product_family_master pfm on (pm.gpm_product_code=pfm.GPM_PRODUCT_CODE) ".
		   " where glh_country='India' and god_order_amt>0 and god_order_date >= '$from_dt' and god_order_date <= '$to_dt' and god_invoice_status in ('S','N') ".
		   " and god_order_status='A' and god_tax_mode='4' $lead_types_cond group by god_order_no order by god_order_date ";
	$qry_res = execute_my_query($qry);
	while($row = mysqli_fetch_array($qry_res)) {
		$order_no = $row['god_order_no'];
		$skew_ppty_list = $row['skew_ppty'];
		$order_date = $row['god_order_date'];
		$split = $row['god_order_splict'];
		$lead_code = $row['god_lead_code'];
		$kit_based = (in_array('308',explode(",",$row['prod_codes'])) or check_kit_based_customer($lead_code))?true:false;
		$lead_type = $row['glh_lead_type'];
		$inv_date = '0';
		$existing_corporate = false;
		if($kit_based and $lead_type=='3') {
			if(!check_invoice_conditions_for_kit_orders($order_no)) {
				$result_arr['no_generate'][$order_no] = '0';
				continue;
			}
		}
		if($lead_type=='3' and !$kit_based) {
			$existing_corporate = true;
		}
		$skew_ppty_arr = explode(",",$skew_ppty_list);
		if(!in_array('1',$skew_ppty_arr) and !in_array('2',$skew_ppty_arr)  and !in_array('11',$skew_ppty_arr)) {
			$inv_date = ((strtotime($order_date)-strtotime($inv_date))>0)?$order_date:$inv_date;
			$log->logInfo("Invoice generation Order no: $order_no Invoice date:".$inv_date);
		} else {
			$join = " join gft_order_product_dtl on (god_order_no=gop_order_no) ".($split=='0'?" left":"").
					" join gft_cp_order_dtl on (god_order_no=gco_order_no and gop_product_code=gco_product_code and gop_product_skew=gco_skew) ".
					" join gft_product_master pm on (gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew) ".
					" left join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code) ";
			$cols = " ,GOP_FULLFILLMENT_NO as ff,gop_license_status lic_status ";
			$cond = " and (gop_invoice_raised!='1' or gop_invoice_raised is null) ";
			if($split=='1' or $existing_corporate) {
				$cols = " ,GCO_FULLFILLMENT_NO as ff,GCO_ORDERED_DATE,gco_license_status lic_status ";
				$cond = " and (gco_invoice_raised!='1' or gco_invoice_raised is null) and pm.gpm_product_code not in (391) and pm.gft_skew_property not in ('4','15','8') ";
			}
			$prod_dtl_qry = " select pm.gpm_product_code,pm.gpm_product_skew,pm.gft_skew_property,pm.gpm_product_type,pfm.gpm_is_base_product$cols from gft_order_hdr ".
					        " $join where god_order_no='$order_no' $cond ";
			$prod_res = execute_my_query($prod_dtl_qry);
			while($data_row = mysqli_fetch_array($prod_res)) {
				if(in_array($data_row['gft_skew_property'],array('1','11')) and $data_row['gpm_product_type']!='8' and $data_row['gpm_is_base_product']=='Y') {
					$check_install = false;
					if($data_row['gft_skew_property']=='11') {
						$ass_dtl_qry = execute_my_query(" select * from gft_ass_dtl where gad_ass_order_no='$order_no' and gad_product_code='".$data_row['gpm_product_code']."' and gad_product_skew='".$data_row['gpm_product_skew']."' ");
						if(mysqli_num_rows($ass_dtl_qry)==0) {
							$check_install = true;
						}
					} else {
						$check_install = true;
					}
					if($check_install) {
						$install_dt_qry = " select ifnull(gid_install_date,0) inv_date from gft_install_dtl_new where gid_order_no='$order_no' and gid_product_code='".$data_row['gpm_product_code']."' ".
										  " and gid_product_skew='".$data_row['gpm_product_skew']."' and GID_FULLFILLMENT_NO='".$data_row['ff']."'";
						$install_res = execute_my_query($install_dt_qry);
						if($ins_row = mysqli_fetch_array($install_res)) {
							$approve_date_res = get_data_from_table(array("date(GLA_APPROVED_ON) approve_date "), "gft_lic_approved_log", array("GLA_ORDER_NO","GLA_FULLFILLMENT_NO","GLA_PRODUCT_CODE","GLA_PRODUCT_SKEW","GLA_STATUS_CHANGEDAS"), array($order_no,$data_row['ff'],$data_row['gpm_product_code'],$data_row['gpm_product_skew'],'Approved by CM'));
							$approval_date = (isset($approve_date_res[0]) and isset($approve_date_res[0]['approve_date']))?$approve_date_res[0]['approve_date']:'0';
							if($approval_date=='0') {// || $data_row['lic_status']!='2') {
								if($data_row['gpm_product_code']=='300' and $existing_corporate) {	
									if(!check_hq_installations_for_invoice($order_no,$data_row['gpm_product_skew'],$lead_code)) {
										continue;
									}
								} else {
// 									if($existing_corporate) {
// 										continue;
// 									}
									if($approval_date=='0') {
										//$result_arr['no_generate'][$order_no] = '0';
										continue;
									}
								}
							}
							$dt = ((strtotime($ins_row['inv_date'])-strtotime($approval_date))>0)?$ins_row['inv_date']:$approval_date;
							$inv_date = ((strtotime($dt)-strtotime($inv_date))>0)?$dt:$inv_date;
							$log->logInfo("Invoice generation Order no: $order_no. ".$data_row['gpm_product_code']."-".$data_row['gpm_product_skew']."-".$data_row['ff']." Install date:".$ins_row['inv_date']." Approval date:".$approval_date);
						} else {
							if($data_row['gpm_product_code']=='300' and $existing_corporate) {
								if(!check_hq_installations_for_invoice($order_no,$data_row['gpm_product_skew'],$lead_code)) {
									continue;
								} else {
									$inv_date = ((strtotime($order_date)-strtotime($inv_date))>0)?$order_date:$inv_date;
									$log->logInfo("Invoice generation HQ Order no: $order_no. ".$data_row['gpm_product_code']."-".$data_row['gpm_product_skew']."-".$data_row['ff']." Invoice date:".$inv_date);
								}
							}
						}
					} else {
						$inv_date = ((strtotime($order_date)-strtotime($inv_date))>0)?$order_date:$inv_date;
						$log->logInfo("Invoice generation Order no: $order_no. ".$data_row['gpm_product_code']."-".$data_row['gpm_product_skew']."-".$data_row['ff']." No install dtl check. Order sate:".$order_date." Invoice date:".$inv_date);
					}
				} else if($data_row['gft_skew_property']=='2') {
					$root_order_qry = execute_my_query(" select GOU_OLD_PCODE,GPM_LICENSE_TYPE,GOU_ROOT_FULLFILLMENT_NO,GOU_ROOT_ORDER_NO from gft_order_upgradation_dtl ".
							" join gft_product_master on (gpm_product_code=GOU_OLD_PCODE and gpm_product_skew=GOU_OLD_PSKEW) ".
							" where GOU_ORDER_NO='$order_no' "); 
					if($up_row = mysqli_fetch_array($root_order_qry)) {
						if($up_row['GPM_LICENSE_TYPE']=='3') {
							$root_order = $up_row['GOU_ROOT_ORDER_NO'];
							$root_pskew = '';
							$root_pskew_qry = execute_my_query(" select gou_old_pskew from gft_order_upgradation_dtl ".
									" join gft_order_hdr on (gou_order_no=god_order_no) ".
									" join gft_product_master on (gpm_product_code=gou_old_pcode and gpm_product_skew=gou_old_pskew) ".
									" where gou_root_order_no='$root_order' and god_order_date in (select min(god_order_date) ".
									" from gft_order_upgradation_dtl join gft_order_hdr on (gou_order_no=god_order_no) ".
									" where gou_root_order_no='$root_order') ");
							if($root_row = mysqli_fetch_array($root_pskew_qry)) {
								$root_pskew = $root_row['gou_old_pskew'];
							}
							if($root_pskew!='') {
								$root_ff = $up_row['GOU_ROOT_FULLFILLMENT_NO'];
								$root_pcode = $up_row['GOU_OLD_PCODE'];
								$lic_status_qry = execute_my_query("select if(god_order_splict=1,gco_license_status,gop_license_status) stat from gft_order_hdr".
											" join gft_order_product_dtl on (god_order_no = gop_order_no and gop_product_code='$root_pcode' and gop_product_skew='$root_pskew' and gop_fullfillment_no='$root_ff') ".
											" left join gft_cp_order_dtl on (god_order_no = gco_order_no and gco_product_code='$root_pcode' and gco_skew='$root_pskew' and gco_fullfillment_no='$root_ff') ".
											" where god_order_no='$root_order'");
								$approve_date_res = get_data_from_table(array("date(GLA_APPROVED_ON) approve_date "), "gft_lic_approved_log", array("GLA_ORDER_NO","GLA_FULLFILLMENT_NO","GLA_PRODUCT_CODE","GLA_PRODUCT_SKEW","GLA_STATUS_CHANGEDAS"), array($root_order,$root_ff,$root_pcode,$root_pskew,'Approved by CM'));
								//if($root_dtl = mysqli_fetch_array($lic_status_qry)) {
								$approval_date = (isset($approve_date_res[0]) and isset($approve_date_res[0]['approve_date']))?$approve_date_res[0]['approve_date']:'0';
									if($approval_date!='0') {//$root_dtl['stat']=='2') {
// 										$date_qry = get_data_from_table(array("date(GLA_APPROVED_ON) approve_date"), "gft_lic_approved_log", array("GLA_ORDER_NO","GLA_FULLFILLMENT_NO","GLA_PRODUCT_CODE","GLA_PRODUCT_SKEW","GLA_STATUS_CHANGEDAS"), array($root_order,$root_ff,$root_pcode,$root_pskew,'Approved by CM'));
// 										$approve_date = isset($date_qry[0]["approve_date"])?$date_qry[0]["approve_date"]:'0';
										$approve_date = $approval_date;
										$inv_date = (strtotime($approve_date)-strtotime($inv_date)>0)?$approve_date:$inv_date;
										$log->logInfo("Invoice generation Order no: $order_no (upgradation) Approval on:".$approve_date." Invoice date:".$inv_date);
									} else {
// 										if($existing_corporate) {
// 											continue;
// 										}
// 										$result_arr['no_generate'][$order_no] = '0';
										continue;
									}
								//}
							}
						} else {
							$inv_date = (strtotime($order_date)-strtotime($inv_date)>0)?$order_date:$inv_date;
							$log->logInfo("Invoice generation Order no: $order_no (upgradation Edition-Edition) Order on:".$order_date." Invoice date:".$inv_date);
						}
					}
				} else {
					if($split=='1' or $existing_corporate) {
						$dt = $data_row['GCO_ORDERED_DATE'];
						$str = " Split ";
					} else {
						$dt = $order_date;
						$str = " Order ";
					}
					$inv_date = (strtotime($dt)-strtotime($inv_date)>0)?$dt:$inv_date;
					$log->logInfo("Invoice generation Order no: $order_no$str date:".$order_date." Invoice date:".$inv_date);
				}
			}
		}
		if($inv_date!='0') {
			if(strtotime($inv_date)<=strtotime($to_dt)) {
				$result_arr['generate'][$order_no] = $inv_date;
			} else {
				$result_arr['later'][$order_no] = $inv_date;
			}
		} else {
			$result_arr['no_generate'][$order_no] = $inv_date;
		}
	}
	return $result_arr;
}
/**
 * @param string $order_no
 * @return boolean
 */
function is_kit_based_order($order_no) {
	$qry = " select gop_order_no from gft_order_product_dtl ".
		   " join gft_product_master on (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew) ".
		   " where gop_order_no='$order_no' and gpm_order_type in ('3','2') ";
	$qry_res = execute_my_query($qry);
	if(mysqli_num_rows($qry_res)) {
		return true;
	}
	return false;
}
/**
 * @param string $order_no
 * @return boolean
 */
function is_erp_kit_based_order($order_no) {
	$qry = " select gop_order_no from gft_order_product_dtl ".
			" join gft_product_master on (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew) ".
			" JOIN gft_product_family_master pf ON(gop_product_code=pf.GPM_PRODUCT_CODE)".
			" where gop_order_no='$order_no'  and GPM_IS_INTERNAL_PRODUCT=4 ";
	$qry_res = execute_my_query($qry);
	if(mysqli_num_rows($qry_res)) {
		return true;
	}
	return false;
}
/**
 * @param string $order_no
 * 
 * @return int
 */
function get_gcm_order_edition($order_no){
	$qry = " select GPM_PRODUCT_TYPE from gft_order_product_dtl ".
			" join gft_product_master on (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew) ".
			" JOIN gft_product_family_master pf ON(gop_product_code=pf.GPM_PRODUCT_CODE)".
			" JOIN gft_product_type_master on(GPT_TYPE_ID=GPM_PRODUCT_TYPE AND GPT_APPLICABLE_FOR_BQ='Y')".
			" where gop_order_no='$order_no' limit 1";
	$qry_res = execute_my_query($qry);
	if($row=mysqli_fetch_array($qry_res)){
		return (int)$row['GPM_PRODUCT_TYPE'];
	}
	return 0;
}
/**
 * @param string[string] $arr
 * @param string $hdr
 * 
 * @return void
 */
function print_array_as_table($arr,$hdr) {
	echo "<table border=1><tr><th>S.no</th>$hdr</tr>";
	$i = 1;
	foreach ($arr as $k=>$v){
		echo "<tr><td>".$i++."</td><td>$k</td><td>$v</td></tr>";
	}
	echo "</table>";
}
/**
 * @param boolean $do_print
 * @param string $hdr
 * 
 * @return string[string][int]
 */
function get_invoice_list($do_print=false,$hdr='') {
	$print_arr = array();
	$invoices = /*.(string[string][int]).*/array("sa"=>array());
	$qry = execute_my_query(" select gih_invoice_id,gip_order_no,gih_invoice_ac_reffer_id,gih_invoice_date from gft_invoice_hdr join gft_invoice_product_dtl on (gih_invoice_id=gip_invoice_id) ".
			" where gih_invoice_date>'2017-07-31' and gih_invoice_ac_reffer_id like '17-18/SA%' ".
			" group by gip_invoice_id order by gih_invoice_date ");
	while($row = mysqli_fetch_array($qry)) {
		$invoices["sa"][] = array($row['gih_invoice_id'],$row['gip_order_no']);
		$print_arr[$row['gip_order_no']] =$row['gih_invoice_ac_reffer_id']." - ".$row['gih_invoice_date'];
	}
	if($do_print) {
		print_array_as_table($print_arr, $hdr);
	}
	return $invoices;
}
/**
 * @param string $lead_code
 * @return void
 */
function regenerate_gst_invoice_pdfs($lead_code) {
	$qry = " select gih_invoice_id,gip_order_no from gft_invoice_hdr ".
		   " join gft_invoice_product_dtl on (gih_invoice_id=gip_invoice_id) ".
		   " where gih_lead_code='$lead_code' and gih_status='A' and gih_invoice_date>='2017-07-01' group by gip_order_no,gih_invoice_id ";
	$res = execute_my_query($qry);
	$inv_dtls = array();
	while($row = mysqli_fetch_array($res)) {
		$add_on_inv = false;
		$chk_qry = execute_my_query("select * from gft_add_on_commission_dtl where gac_order_no='".$row['gip_order_no']."'");
		if(mysqli_num_rows($chk_qry)>0) {
			$add_on_inv = true;
		}
		$inv_dtls[] = array($row['gih_invoice_id'],$row['gip_order_no'],$add_on_inv);
	}
	foreach ($inv_dtls as $arr) {
		generate_gst_invoice_pdf($arr[0], $arr[1], $arr[2]);
	}
}

/**
 * @param string $display_name
 * @param string $file_path
 * @param string $emply_id
 * @param string $product_id
 * @param string $comments
 * @param int $purpose_type
 * @param int $media_type
 *
 * @return void
 */
function save_print_profile_uploads($display_name,$file_path,$emply_id,$product_id,$comments,$purpose_type,$media_type){
	$prod_arr = explode("-", $product_id);
	$insert_arr['GPP_DISPLAY_NAME'] = $display_name;
	$insert_arr['GPP_EMP_ID'] 		= $emply_id;
	$insert_arr['GPP_FILE_PATH'] 	= $file_path;
	$insert_arr['GPP_REMARKS'] 		= $comments;
	$insert_arr['GPP_PRODUCT_CODE'] = $prod_arr[0];
	$insert_arr['GPP_PRODUCT_GROUP']= isset($prod_arr[1])?$prod_arr[1]:'';
	$insert_arr['GPP_CREATED_DATE'] = date('Y-m-d H:i:s');
	$insert_arr['GPP_PURPOSE_ID'] = $purpose_type;
	$insert_arr['GPP_MEDIA_TYPE'] = $media_type;
	array_insert_query("gft_media_master", $insert_arr);
}

/**
 * @param float $amt
 *
 * @return float
 */
function get_overall_minute_for_alr_savings($amt){
	$parsed_amt = (int)$amt;
	if($parsed_amt==0){
		return 0;
	}else if($parsed_amt <= 1500){
		$per_min_cost = 12.5;
	}else if ($parsed_amt <= 3000){
		$per_min_cost = 10.0;
	}else{
		$per_min_cost = 8.33;
	}
	$overall_minutes = (int)round($amt/$per_min_cost);
	$nearest_adder = 0;
	if( ($overall_minutes % 5) > 0 ){
		$nearest_adder = 5-($overall_minutes % 5);
	}
	$overall_minutes = $overall_minutes + $nearest_adder;
	return $overall_minutes;
}

/**
 * @param string $order_date
 *
 * @return int[int]
 */
function get_applicable_percent_and_days($order_date){
	$order_time = strtotime($order_date);
	$ret_arr = array(0,0);
	if( ($order_time >= strtotime("2017-01-01")) && ($order_time <= strtotime("2017-03-31")) ){
		$ret_arr = array(5,30);
	}else if( ($order_time >= strtotime("2017-04-01")) && ($order_time <= strtotime("2017-06-30")) ){
		$ret_arr = array(10,60);
	}else if($order_time >= strtotime("2017-07-01")){
		$ret_arr = array(15,90);
	}
	return $ret_arr;
}

/**
 * @param string $cust_id
 *
 * @return float
 */
function get_voice_minutes_for_ordered_value($cust_id){
	$order_start_date 	= "2017-01-01";
	$order_end_date 	= "2017-10-16";
	$sql1 = " select GOD_ORDER_NO,GOD_ORDER_DATE,sum(GOP_SELL_RATE*GOP_QTY) as order_val from gft_order_hdr ".
			" join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) ".
			" join gft_product_master on (GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ".
			" where GOD_LEAD_CODE='$cust_id' and GFT_SKEW_PROPERTY in (4,15) and GOP_SELL_AMT > 0 and GOP_DISCOUNT_AMT <= 10 and GOD_ORDER_STATUS='A' ".
			" and GOD_ORDER_DATE between '$order_start_date' and '$order_end_date' and GLH_COUNTRY='India' ".
			" group by GOD_ORDER_NO having order_val > 0 ";
	$res1 = execute_my_query($sql1);
	$credit_amt = 0.00;
	while($row1 = mysqli_fetch_array($res1)){
		$order_date = $row1['GOD_ORDER_DATE'];
		$order_amt	= $row1['order_val'];
		$credit_dtl = get_applicable_percent_and_days($order_date);
		$credit_amt	+= round($order_amt*($credit_dtl[0]/100),2);
	}
	$credit_mins = get_overall_minute_for_alr_savings($credit_amt);
	return $credit_mins;
}

/**
 * @param string $cust_id
 *
 * @return int[int]
 */
function get_extended_days_for_alr($cust_id){
	$order_start_date 	= "2017-01-01";
	$order_end_date 	= "2017-10-16";
	$sql1 = " select GOD_ORDER_NO,GOD_ORDER_DATE,GID_INSTALL_ID,GID_VALIDITY_DATE from gft_order_hdr ".
			" join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) ".
			" join gft_product_master on (GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ".
			" join gft_ass_dtl on (GAD_ASS_ORDER_NO=GOD_ORDER_NO) ".
			" join gft_install_dtl_new on (GID_INSTALL_ID=GAD_INS_REFF and GID_STATUS!='U') ".
			" where GOD_LEAD_CODE='$cust_id' and GFT_SKEW_PROPERTY in (4,15) and GOP_SELL_AMT > 0 and GOP_DISCOUNT_AMT <= 10 and GOD_ORDER_STATUS='A' ".
			" and GOD_ORDER_DATE between '$order_start_date' and '$order_end_date' and GLH_COUNTRY='India' group by GOD_ORDER_NO,GID_INSTALL_ID ".
			" order by GID_INSTALL_ID desc ";
	$res1 = execute_my_query($sql1);
	$dtl_arr = /*. (int[int]) .*/array();
	while ($row1 = mysqli_fetch_array($res1)){
		$god_order_no = $row1['GOD_ORDER_NO'];
		$install_id	  = (int)$row1['GID_INSTALL_ID'];
		$credit_dtl   = get_applicable_percent_and_days($row1['GOD_ORDER_DATE']);
		if(!isset($dtl_arr[$install_id])){
			$dtl_arr[$install_id] = 0;
		}
		$dtl_arr[$install_id] += $credit_dtl[1];
	}
	return $dtl_arr;
}

/**
 * @param string $long_url
 *
 * @return string
 */
function get_shortened_url($long_url){
	global $global_sam_domain;
	$post_url = $global_sam_domain."/url_shortner/shorten.php?long_url=".urlencode($long_url);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $post_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	$response_json = (string)curl_exec($ch);
	curl_close($ch);
	$json_arr = json_decode($response_json,true);
	$surl = isset($json_arr['short_url'])?/*.(string).*/$json_arr['short_url']:'';
	return $surl;
}

/**
 * @param int $pcode
 * @param string $type
 * 
 * @return string
 */
function get_minimum_version($pcode,$type='highest'){
	$sql1 = " select GPV_VERSION,GPV_IS_MINIMUM_VERSION from gft_product_version_master ".
			" where GPV_PRODUCT_CODE='$pcode' and GPV_IS_MINIMUM_VERSION=1 ".
			" order by GPV_RELEASE_DATE desc,GPV_ENTERED_ON desc limit 1 ";
	$res1 = execute_my_query($sql1);
	$gpv_version = "";
	if($row1 = mysqli_fetch_array($res1)){
		$gpv_version = $row1['GPV_VERSION'];
	}
	return $gpv_version;
}

/**
 * @param string $cust_id
 * 
 * @return void
 */
function send_communication_for_support_mode_migration($cust_id){
	global $secret,$global_web_domain;
	$que1 = " select GML_ID from gft_migration_log where GML_LEAD_CODE='$cust_id' and GML_OTP_VERIFIED=1 ";
	$res1 = execute_my_query($que1);
	if(mysqli_num_rows($res1)==0){
		$voice_min = get_voice_minutes_for_ordered_value($cust_id);
		if($voice_min > 0){
			$encrypted_cid 	= urlencode(lic_encrypt(json_encode(array('cid'=>$cust_id)), $secret));
			$cust_name 		= get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", $cust_id);
			$long_url 		= "$global_web_domain/confirm-digital-assure-care.html?data=$encrypted_cid";
			$short_url 		= get_shortened_url($long_url);
			$alr_arr 		= get_extended_days_for_alr($cust_id);
			$extend_day 	= "";
			foreach ($alr_arr as $ins_id => $days_to){
				$extend_day = $days_to;
			}
			$content_config = array(
					'mid'=>array($extend_day),
					'Short_Link'=>array($short_url),
					'STORE_PAYMENT_LINK'=>array($long_url),
					'Customer_Name'=>array($cust_name)
			);
			$sms_category = 197;
			$sms_content = get_formatted_content($content_config, $sms_category);
			entry_sending_sms_to_customer('', $sms_content, $sms_category,$cust_id);
			$lang_que = " select GPM_PREFERRED_LANGUAGE from gft_lead_hdr ".
						" left join gft_political_map_master on (GPM_MAP_NAME=GLH_CUST_STATECODE) ".
						" where GLH_LEAD_CODE='$cust_id' ";
			$lang_res = execute_my_query($lang_que);
			$mail_template = 288;
			if($data1 = mysqli_fetch_array($lang_res)){
				$pref_lang = (int)$data1['GPM_PREFERRED_LANGUAGE'];
				if($pref_lang==2){
					$mail_template = 292;
				}else if($pref_lang==3){
					$mail_template = 293;
				}
			}
			$mail_id_arr = get_contact_dtl_for_designation($cust_id, '4', '');
			send_formatted_mail_content($content_config, '', $mail_template,null,null,$mail_id_arr);
				
			send_formatted_notification_content($content_config, '', 77, 2, $cust_id);
		}
	}
}

/**
 * @param string $order_no
 * @param string $lead_code
 * @param string $install_id
 * @return boolean
 */
function check_for_alr_exclusion($order_no,$lead_code,$install_id='') {
	$no_alr = false;
	if($install_id!='') {
		$order_no = get_single_value_from_single_table("GID_LIC_ORDER_NO", "gft_install_dtl_new", "GID_INSTALL_ID", $install_id);
	}
	$partner_chk_qry = " select ifnull(CGI_LEAD_CODE,-1) partner_id from gft_order_hdr join gft_cp_info on (CGI_EMP_ID=god_emp_id) ".
					   " where god_order_no = '$order_no' ";
	$partner_res = execute_my_query($partner_chk_qry);
	$partner_id = '';
	if($row = mysqli_fetch_array($partner_res)) {
		$partner_id = $row['partner_id'];
	}
	$country = '';
	$country_qry = " select gpm_map_id from gft_political_map_master join gft_lead_hdr on (glh_country=gpm_map_name and gpm_map_type='C' and GPM_MAP_STATUS='A') ".
				   " where glh_lead_code='$lead_code' ";
	$country_res = execute_my_query($country_qry);
	if($data_row = mysqli_fetch_array($country_res)) {
		$country = $data_row['gpm_map_id'];
	}
	if($country!='2') {
		$alr_chk_qry = " select gae_id from gft_alr_excluded_dtl where ((gae_lead_region_id = '$lead_code' and gae_type='1') ".
					   " or (gae_type='2' and gae_lead_region_id='$partner_id') or (gae_type='3' and gae_lead_region_id='$country')) ".
					   " and gae_status='1' ";
		$alr_chk_res = execute_my_query($alr_chk_qry);
		if(mysqli_num_rows($alr_chk_res)>0) {
			$no_alr = true;
		}
	}
	return $no_alr;
}
/**
 * @param string $customerId
 *
 * @return boolean
 */
function check_support_migration_status($customerId){
	$exist_chk = " select GML_LEAD_CODE from gft_migration_log where GML_LEAD_CODE='$customerId' and GML_OTP_VERIFIED=1 ";
	$exist_res = execute_my_query($exist_chk);
	if(mysqli_num_rows($exist_res) > 0){
		return true;
	}
	return false;
}
/**
 * @param string $lead_code
 * @param string[int] $contact_type_arr
 *
 * @return string
 */
function get_customer_contact_query($lead_code,$contact_type_arr){
	$contact_type_str = implode(',', $contact_type_arr);
	$que1 = " select GLH_COUNTRY,GCC_CONTACT_NAME,GCC_CONTACT_NO,GCG_GROUP_ID,GCC_ENABLE_CALL_SUPPORT,GCC_ID from gft_customer_contact_dtl ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GCC_LEAD_CODE) ".
			" left join gft_contact_dtl_group_map on (GCG_CONTACT_ID=GCC_ID and GCG_GROUP_ID=1) ".
			" where GCC_LEAD_CODE='$lead_code' and gcc_contact_type in ($contact_type_str) order by GCC_ID ";
	return $que1;
}
/**
 * @param int $available_support_hrs
 * @param int $return_format
 *
 * @return string
 */
function get_remining_support_balance_in_text($available_support_hrs,$return_format=0){
	$hours = floor($available_support_hrs / 60);
	$minutes = ($available_support_hrs % 60);
	$available_support_hrs_text = "";
	$available_support_hrs_text_short = "";
	$available_support_hrs		= "00";
	$available_support_mins		= "00";
	if($hours!=0){
		$available_support_hrs_text .= $hours." Hours ";
		$available_support_hrs_text_short .= sprintf("%02d", $hours)." Hrs ";
		$available_support_hrs = sprintf("%02d", $hours);
	}
	if($minutes!=0){
		$available_support_hrs_text .= $minutes. " Minutes ";
		$available_support_hrs_text_short .= sprintf("%02d", $minutes). " Mins ";
		$available_support_mins		= sprintf("%02d", $minutes);
	}
	if($return_format==1){
		$available_support_hrs_text = "$available_support_hrs:$available_support_mins";
		return $available_support_hrs_text;
	}
	if($return_format==2){
		return $available_support_hrs_text_short;
	}
	return $available_support_hrs_text;
}
/**
 * @param string[int] $quote_id
 * @return boolean
 */
function is_kit_quote($quote_id) {
	if(count($quote_id)>0) {
		$quotes = implode("','",$quote_id);
		$prod_dtls = " select gqp_product_code,gqp_product_skew from gft_quotation_product_dtl ".
					 " join gft_quotation_hdr on (gqh_order_no=gqp_order_no) ".
					 " join gft_product_master on (gpm_product_code=gqp_product_code and gpm_product_skew=gqp_product_skew) ".
					 " where gqp_order_no in ('$quotes') and GPM_PRODUCT_TYPE in ('14','15','16','17') and GQH_ORDER_STATUS='A' ";
		$prod_res = execute_my_query($prod_dtls);
		if(mysqli_num_rows($prod_res)>0) {
			return true;
		}
		return false;
	}
	return false;
}
/**
 * @param string $quote_id
 * @param string $lead_code
 * @param boolean $check_proposal_doc
 * 
 * @return boolean
 */
function check_first_kit_quote($quote_id,$lead_code,$check_proposal_doc=false) {
	if(is_kit_quote(array($quote_id))) {
		$cond = '';
		if($check_proposal_doc) {
			$cond .= " and GQH_HQ_PROPOSAL_DOC_ID is not null and GQH_HQ_PROPOSAL_DOC_ID!='0' ";
		}
		$all_quotes_qry = " select gqh_order_no from gft_quotation_hdr where gqh_lead_code='$lead_code' and GQH_ORDER_STATUS='A' ".
						  " and gqh_order_no!='$quote_id' $cond ";
		$all_quotes_res = execute_my_query($all_quotes_qry);
		$quotes = array();
		while($row = mysqli_fetch_array($all_quotes_res)) {
			$quotes[] = $row['gqh_order_no'];
		}
		$contins_kit_product = is_kit_quote($quotes);
		if($contins_kit_product) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}
/**
 * @param string $emp_id
 * @param string $search_text
 * @param string $offset
 * @param string $page_size
 * @param boolean $send_error_resp
 * @param boolean $for_order_split
 * @return mixed[]
 */
function get_accessible_leads_for_emp($emp_id,$search_text='',$offset='0',$page_size='20',$send_error_resp=false,$for_order_split=false) {
	global $non_employee_group,$receive_arg;
	$cust_arr = array();
	$joins = '';
	if($for_order_split) {
		$joins .= " join gft_order_hdr on (glh_lead_code=god_lead_code and god_order_status='A' and god_order_splict='1') ".
				  " join gft_order_product_dtl on (gop_order_no=god_order_no and (gop_qty-gop_cp_usedqty)>0) ".
				  " join gft_product_master on (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew and GFT_SKEW_PROPERTY in (1,2,3,7,11,13,14,16,17,18,19))";
	}
	$qry = " select glh_lead_code,glh_cust_name,GLH_CUST_STREETADDR2,glh_status,glh_prospects_status from gft_lead_hdr ".
		   " join gft_emp_master em on(em.gem_emp_id=GLH_LFD_EMP_ID) ".
		   " join gft_emp_master em1 on(em1.gem_emp_id=GLH_CREATED_BY_EMPID) ".$joins.
		   " LEFT JOIN gft_lead_hdr_ext lhe on(lhe.gle_lead_code=glh_lead_code) ";
	$limit_query	=	" limit $offset,$page_size";
	$name_code_cond = '';
	if(is_numeric($search_text)){
		$query_ch1="select glh_lead_code from gft_lead_hdr where glh_lead_code='$search_text' ";
		$result_ch1=execute_my_query($query_ch1);
		if(mysqli_num_rows($result_ch1)==0){
			/* try in merge data */
			$query_ch2="SELECT gcm_lead_code FROM gft_cust_merger_data  where merg_slave='$search_text' limit 1 ";
			$result_ch2=execute_my_query($query_ch2);
			if(mysqli_num_rows($result_ch2)==1){
				$qd_ch2=mysqli_fetch_array($result_ch2);
				$search_text=$qd_ch2[0];
			}
		}
		$name_code_cond .= " and glh_lead_code='$search_text' ";
	}else if($search_text!='') {
		$name_code_cond	.= " AND  GLH_CUST_NAME like  '%$search_text%'";
	}
	$qry .= " where (1) $name_code_cond ";
	$cust_cond_secured='';
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
	if (is_authorized_group_list($emp_id,array(5)) and !is_authorized_group_list($emp_id,array(1,12,8))) {
		$cust_cond_secured	=	get_territory_query_for_sales_person((int)$emp_id,true,$name_code_cond);
	}
	$having = '';
	if($for_order_split) {
		$having .= " having sum((gop_qty-gop_cp_usedqty))>0 ";
	}
	$group_by_query = " group by glh_lead_code $having order by glh_cust_name ";
	$security_query = $qry.$cust_cond_secured.$group_by_query.$limit_query;
	$result = $result_secured = execute_my_query($qry.$group_by_query.$limit_query,'',true,true);
	$tot_count= mysqli_num_rows(execute_my_query($qry));
	$count = mysqli_num_rows($result);
	$count_secured=-1; //default value
	if($count>=1 and $cust_cond_secured!=''){
		$result_secured=execute_my_query($security_query);
		$count_secured =mysqli_num_rows($result_secured);
		$tot_secure_count	=	mysqli_num_rows(execute_my_query($security_query));
	}
	if($count_secured == 0){
		if($send_error_resp) {
			sendErrorWithCode($receive_arg,"You can't access this customer details. Please contact Sales / Partner Management Team to get the access",HttpStatusCode::BAD_REQUEST,'',$emp_id);
			exit;
		} else {
			die("You can't access this customer details. Please contact Sales / Partner Management Team to get the access");
		}
	} else if($count_secured>=1){
		while($row=mysqli_fetch_array($result_secured)) {
			$single_row = array();
			$single_row['cust_name'] = $row['glh_cust_name'];
			$single_row['location'] = $row['GLH_CUST_STREETADDR2'];
			$single_row['lead_status'] = $row['glh_status'];
			$single_row['prospect_status'] = $row['glh_prospects_status'];
			$cust_arr[$row['glh_lead_code']] = $single_row;
		}
	} else if($count>=1) {
		while($row=mysqli_fetch_array($result)) {
			$single_row = array();
			$single_row['cust_name'] = $row['glh_cust_name'];
			$single_row['location'] = $row['GLH_CUST_STREETADDR2'];
			$single_row['lead_status'] = $row['glh_status'];
			$single_row['prospect_status'] = $row['glh_prospects_status'];
			$cust_arr[$row['glh_lead_code']] = $single_row;
		}
	} else {
		if($send_error_resp) {
			sendErrorWithCode($receive_arg,"No records found",HttpStatusCode::BAD_REQUEST,'',$emp_id);
			exit;
		} else {
			die("No records found");
		}
	}
	return $cust_arr;
}
/**
 * @param string[int] $contact_no_arr
 * @return string[string][int][string]
 */
function get_customers_for_contact_no($contact_no_arr) {
	$contact_no_str = '';
	$outlet_info = /*.(string[string][int][string]).*/array();
	$query_or = '';
	foreach ($contact_no_arr as $num) {
		$or_conditions = getContactDtlWhereCondition('gcc_contact_no', $num);
		$contact_no_str .= $query_or.$or_conditions;
		$query_or = ' or ';
	}
	if($contact_no_str!='') {
		$contact_no_str = " and ($contact_no_str) ";
	}
	$leads_qry = " select gcc_contact_no,gcc_lead_code,glh_cust_name,GLH_CUST_STREETADDR2,glh_status,glh_prospects_status ".
				 " ,gcc_lead_code,concat(ifnull(glh_cust_name,''),' - ',ifnull(GLH_CUST_STREETADDR2,'')) cust_name ".
				 " from gft_customer_contact_dtl join gft_lead_hdr on (gcc_lead_code=glh_lead_code) ".
				 " where 1 $contact_no_str order by glh_cust_name,glh_cust_streetaddr2, glh_lead_code ";
	$leads_res = execute_my_query($leads_qry);
	while($row = mysqli_fetch_array($leads_res)) {
		$contact_no = $row['gcc_contact_no'];
		$curr = /*.(string[string]).*/array();
		$curr['cust_id'] = $row['gcc_lead_code'];
		$curr['cust_name'] = $row['cust_name'];
		$curr['location'] = $row['GLH_CUST_STREETADDR2'];
		$curr['lead_status'] = $row['glh_status'];
		$curr['prospect_status'] = $row['glh_prospects_status'];
		$outlet_info[$contact_no][] = $curr;
	}
	return $outlet_info;
}
/**
 * @param string $cp_lead_code
 * @param string[int] $itemname
 * @param string[int] $inhand
 * @param string[int] $aquantity
 * @return string[int][int]
 */
function get_order_split_basket($cp_lead_code,$itemname,$inhand,$aquantity) {
	$maxl=count($inhand);
	$sql1="SELECT gop_order_no,concat(gop_product_code,'-',gop_product_skew,'-',gft_skew_property,'-',GPM_ORDER_TYPE)," .
			"(sum(gop_qty)-sum(gop_cp_usedqty)) as inhand,sum(gop_qty),sum(gop_cp_usedqty),god_order_date " .
			" FROM  gft_order_hdr A join gft_order_product_dtl B on (god_order_no=gop_order_no)" .
			" join gft_product_master pm on (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew)" .
			" join gft_product_family_master pfm on (pm.gpm_product_code=pfm.gpm_product_code) " .
			" WHERE god_order_splict=true and (gop_qty-gop_cp_usedqty)>0 and gop_cp_approved=1 " .
			" and god_order_status='A' and god_lead_code=$cp_lead_code ".
			" group by gop_order_no, gop_product_code,gop_product_skew " .
			" order by gop_product_code, gop_product_skew, god_order_date ";
	$rs1=execute_my_query($sql1,'',true,false);
	$tot_orders=mysqli_num_rows($rs1);
	$orders=/*. (string[int][int]) .*/array();
	$tc=0;
	while($qrow=mysqli_fetch_array($rs1)){
		$orders[$tc][0]=$qrow[0];//order no
		$orders[$tc][1]=$qrow[1];//product id
		$orders[$tc][2]=$qrow[2];//in hand
		$orders[$tc][3]=$qrow[3];
		$tc++;
	}
	$basket=/*. (string[int][int]) .*/array();
	//insert [0] - orderid,[1]-productid(code-skew),[2]-inhand
	//$order_product=array();//update [0] - orderid,[1]-productid(code-skew),[2]-inhand
	$lc=0;
	$qc=0;
	$bc=0;
	while($lc<$maxl){
		if( $inhand[$lc]>0 and $itemname[$lc]!="select" and $aquantity[$lc]>0){
			$qc=$aquantity[$lc];//user
			for($lp=0;$lp<$tc;$lp++){
				if($itemname[$lc]==$orders[$lp][1]){
					$val=$orders[$lp][2];//db
					if($val==$qc){
						$basket[$bc][0]=$orders[$lp][0];
						$basket[$bc][1]=$itemname[$lc];
						$basket[$bc][2]=$val;
						$bc++;
						break;
					}else if($val >$qc){
						$basket[$bc][0]=$orders[$lp][0];
						$basket[$bc][1]=$itemname[$lc];
						$val=$val-$qc;
						$basket[$bc][2]=$qc;
						$bc++;
						break;
					}else if($val < $qc){
						$basket[$bc][0]=$orders[$lp][0];
						$basket[$bc][1]=$itemname[$lc];
						$qc=$qc-$val;
						$basket[$bc][2]=$val;
						$bc++;
					}
				}
			}
		}
		$lc++;
	}
	return $basket;
}
/**
 * @param string $customer_id
 * @param string $lead_code
 * @param string[int][int] $basket
 * @param string[int] $itemname
 * @param string[int] $additional_client
 * @param string[int] $additional_cli_order
 * @param string[int] $total_coupon_required
 * @param string $pc_emp_id
 * @param string $mobile_uid
 * @param boolean $inv_for_franchise
 * @param string $receive_arg
 * @return boolean
 */
function do_order_split($customer_id,$lead_code,$basket,$itemname,$additional_client,$additional_cli_order,$total_coupon_required,$pc_emp_id,$mobile_uid,$inv_for_franchise=false,$receive_arg='') {
	global $obj,$uid;
	$resp_arr = array();
	$inst_dtl = $disp_name = $coupon_count = array();
	$split_product_code = /*. (mixed[int]) .*/array();
	$split_product_skew = /*. (mixed[int]) .*/array();
	$split_skew_prop = /*. (mixed[int]) .*/array();
	$split_order_type = /*. (mixed[int]) .*/array();
	$ref_order_query = $ref_order_value = $ref_opcode = '';
	$chk_params = array();  // pcode-pskew
	$date_of_visit = date('Y-m-d');
	$new_product_delivery = (int)get_samee_const("Enable_New_Product_Delivery");
	if($pc_emp_id!='9999' and $pc_emp_id!=0 and $pc_emp_id!='' ){
		execute_my_query("UPDATE gft_lead_hdr SET GLH_FIELD_INCHARGE='$pc_emp_id' where GLH_LEAD_CODE=$lead_code");
	}
	$ext_update_str = '';
	if($inv_for_franchise) {
		$ext_update_str .= (($ext_update_str!=''?",":'')." GLE_INVOICE_FOR_FRANCHISE='1' ");
	} else {
		$ext_update_str .= (($ext_update_str!=''?",":'')." GLE_INVOICE_FOR_FRANCHISE='0' ");
	}
	if($ext_update_str!='') {
		execute_my_query(" update gft_lead_hdr_ext set $ext_update_str where gle_lead_code='$lead_code' ");
	}
	$split_fail_msgs = $ordernos = '';
	$all_custom_update = $client_order_no_arr = $all_client_update = array();
	$cno = $i = 0;
	$order_nos = array();
	$pd_expense_type = 1;
	$hq_installed = get_hq_installed_cust_id($customer_id);
	if($hq_installed!=$lead_code and get_lead_type_for_lead_code($lead_code)!='3' and $receive_arg!='') {
		foreach($basket as $items){
			$t1=explode('-',$items[1]);
			if($t1[0]=='300') {
				sendErrorWithCode($receive_arg, "Please split HQ to HQ installed outlet ($hq_installed)", HttpStatusCode::BAD_REQUEST);
				exit;
			}
		}
	}
	foreach($basket as $items){
		$ordernos=$items[0];
		$t1=explode('-',$items[1]);
		$code=$t1[0];
		$skew=$t1[1];
		$skewproperty=$t1[2];
		$qty=$items[2];
		$ref_order_query	=	'';
		$ref_order_value	=	'';
		$sindex = /*.(int).*/array_search($code.'-'.$skew,$itemname);
		$res=execute_my_query("select GPM_PRODUCT_TYPE from gft_product_master where GPM_PRODUCT_CODE ='$code' AND GPM_PRODUCT_SKEW='$skew'");
		if($data=mysqli_fetch_array($res)){
			$prod_type=$data['GPM_PRODUCT_TYPE'];
			if($prod_type=='8'){
				$ref_order_query	=	",GCO_REFERENCE_ORDER_NO,GCO_REFERENCE_FULLFILLMENT_NO";
				if($sindex!==false and (int)$additional_client[$sindex]==0 or (int)$additional_cli_order[$sindex]==0){
					$ref_order_value	=	",'$ordernos',1";
					for($j=0;$j<count($itemname);$j++){
						$cust_val	=	explode('-',$itemname[$j]);
						$cust_pcode	=	$cust_val[0];
						$cust_pskew	=	substr($cust_val[1], 0,4);
						$cust_skew_pro= 	$cust_val[2];
						$res=execute_my_query("select GPM_PRODUCT_TYPE from gft_product_master where GPM_PRODUCT_CODE ='$cust_pcode' AND GPM_PRODUCT_SKEW='".$cust_val[1]."' AND GPM_PRODUCT_TYPE=8 ");
						if(mysqli_num_rows($res)==0){
							if($cust_pcode==$code and $cust_pskew==substr($skew,0,4) and ($cust_skew_pro==1 or $cust_skew_pro==11)){
								$all_custom_update[]	=	"$ordernos-$cust_pcode-".$cust_val[1]."-$qty-$code-$skew";
							}
						}
					}
				}else{
					$split_val	=	explode('-',$additional_cli_order[$sindex]);
					$ref_order_value	=	",'".$split_val[0]."',".$split_val[1];
				}
			}
		}
		$search = array("3","13","14","16");
		if(in_array($skewproperty,$search)){
			$server_skew = 0;
			if($skewproperty=='3' or $skewproperty=='14' ){
				$server_skew	=	1;
			}else if($skewproperty=='13' or $skewproperty=='16' ){
				$server_skew	=	11;
			}
			$ref_order_query	=	",GCO_REFERENCE_ORDER_NO,GCO_REFERENCE_FULLFILLMENT_NO";
			if($sindex!==false and (int)$additional_client[$sindex]==0 or (int)$additional_cli_order[$sindex]==0){
				$ref_order_value	=	",'$ordernos',1";
				for($j=0;$j<count($itemname);$j++){
					$add_val	=	explode('-',$itemname[$j]);
					$add_pcode	=	$add_val[0];
					$add_pskew	=	substr($add_val[1], 0,4);
					$add_skew_pro= 	$add_val[2];
					$res=execute_my_query("select GPM_PRODUCT_TYPE from gft_product_master where GPM_PRODUCT_CODE ='$add_pcode' AND GPM_PRODUCT_SKEW='".$add_val[1]."' AND GPM_PRODUCT_TYPE=8 ");
					if(mysqli_num_rows($res)==0){
						if($add_pcode==$code and $add_pskew==substr($skew,0,4) and $add_skew_pro==$server_skew){
							$all_client_update[]	=	"$ordernos-$add_pcode-".$add_val[1]."-$qty-$code-$skew";
						}
					}
				}
			}else{
				$split_val	=	explode('-',$additional_cli_order[$sindex]);
				$client_order_no_arr[] = $ordernos.'-'.$split_val[0].'-'.$split_val[1];
				$res_checkinstall	=	execute_my_query("SELECT GID_ORDER_NO FROM gft_install_dtl_new WHERE GID_ORDER_NO='".$split_val[0]."'");
				if(mysqli_num_rows($res_checkinstall)==0){
					execute_my_query("UPDATE gft_cp_order_dtl SET GCO_ADD_CLIENTS=GCO_ADD_CLIENTS+$qty WHERE GCO_ORDER_NO='".$split_val[0]."' AND GCO_FULLFILLMENT_NO=".$split_val[1]." ");
				}
				$ref_order_value	=	",'".$split_val[0]."',".$split_val[1];
			}
		}
		$insert_order_split=insert_stmt_for_split_order_dtl($customer_id,$lead_code,$qty,$ordernos,$code,$skew,$date_of_visit,$date_of_visit,
				$mobile_uid,null,$skewproperty,'',$ref_opcode,$ref_order_query,$ref_order_value);
		if($insert_order_split==""){
			show_my_alert_msg("Check the Lead datils Duplicate  ");
			exit;
		}
		if($total_coupon_required>0){
			$coupon_nos=	array();
			$query6="SELECT (cm.GCD_COUPON_NO) coupon_no " .
					" FROM gft_coupon_distribution_dtl cm " .
					" WHERE cm.GCD_TO_ID='$customer_id' AND cm.GCD_DISTRIBUTE_FOR='C' AND cm.GCD_SIGNED_OFF='N'".
					" and GCD_SPLITABLE='1' AND GCD_REF_ORDER_NO like '$ordernos%' limit $total_coupon_required ";
			$result6= execute_my_query($query6);
			if(mysqli_num_rows($result6)>0){
				while($data6=mysqli_fetch_array($result6)){
					$coupon_nos[]=$data6['coupon_no'];
				}
			}
			$res_pd	=	execute_my_query("select GOD_PD_EXPENSE_TYPE from gft_order_hdr where god_order_no='$ordernos'");
			if(mysqli_num_rows($res_pd)>0 && $row_pd=mysqli_fetch_array($res_pd)){
				$pd_expense_type	=	(int)$row_pd['GOD_PD_EXPENSE_TYPE'];
			}
			if(count($coupon_nos)>0 && $new_product_delivery==0){
				$result=execute_my_query(" select (GCO_CUST_QTY*if($pd_expense_type=1,GPM_COUPON_FOR_LOCAL,if($pd_expense_type=2,GPM_COUPON_FOR_EXSTATION,if($pd_expense_type=3,GPM_COUPON_FOR_OUTSTATION,if($pd_expense_type=4,GPM_COUPON_FOR_ONLINE,if($pd_expense_type=5,GPM_COUPON_FOR_PCS,GPM_COUPONS)))))) as no_ofcoupons, ".
						" concat(GCO_ORDER_NO, GCO_PRODUCT_CODE,GCO_SKEW, GCO_FULLFILLMENT_NO) opcode,GPM_TRAINING_REQUIRED, " .
						" GPM_TRAINING_HRS AS TOTAL_TRAINING_HRS,GPM_BQ_HOURS,GPM_MDM_HOURS,GPM_UAT_HOURS,GPM_GO_LIVE_HOURS,GPM_ORDER_TYPE".
						" from gft_order_product_dtl " .
						" join gft_cp_order_dtl on (GOP_ORDER_NO=GCO_ORDER_NO and GOP_PRODUCT_CODE=GCO_PRODUCT_CODE and GOP_PRODUCT_SKEW=GCO_SKEW AND GCO_CUST_CODE=$lead_code )".
						" join gft_product_master on (gpm_product_code=gop_product_code and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW AND GPM_COUPONS>0) " .
						" WHERE GOP_ORDER_NO='$ordernos' AND GOP_PRODUCT_CODE='$code' AND GPM_PRODUCT_SKEW='$skew' ");
				if($data_check=mysqli_fetch_array($result)){
					for($ci=0;$ci<$data_check['no_ofcoupons'];$ci++,$cno++){
						if(isset($coupon_nos[$cno]) and $coupon_nos[$cno]!=0){
							if($ref_opcode==''){
								$ref_opcode	=	$data_check['opcode'];
							}
							execute_my_query("UPDATE gft_coupon_distribution_dtl SET GCD_TO_ID='$lead_code',GCD_SPLITABLE='0', GCD_REF_ORDER_NO='".$data_check['opcode']."',GCD_ORDER_NO='".$ref_opcode."'  WHERE GCD_TO_ID='$customer_id' and GCD_COUPON_NO=".$coupon_nos[$cno]." AND GCD_SPLITABLE='1' ");
						}
					}
				}
				update_pd_escalation_in_lead_hrd($lead_code, "","Pending Order Audit");
				update_coupon_status_in_lead_hdr($lead_code);
			}
		}
		$i++;
		$order_nos[] = $ordernos;
	}
	if($new_product_delivery) {
		$cust_dtl=customerContactDetail($lead_code);
		$audit_hdr=/*. (string[string]) .*/ array(
				'GAH_LEAD_CODE'=>''.$lead_code,
				'GAH_DATE_TIME'=>date('Y-m-d H:i:s'),
				'GAH_PENDING_IMP'=>"",
				'GAH_REQUIRED_TRAINING_DATE'=>'',
				'GAH_MY_COMMENTS'=>'',
				'GAH_AUDIT_TYPE'=>'20',
				'GAH_AUDIT_BY'=>$uid,
				'GAH_TRAINING_STATUS'=>'0',
				'GAH_L1_INCHARGE'=>$cust_dtl['Reg_incharge'],
				'GAH_FIELD_INCHARGE'=>$cust_dtl['Field_incharge'],
				'GAH_ORDER_NO'=>$ordernos);
		update_product_delivery_dtl($ordernos,$lead_code,$pd_expense_type,$audit_hdr,null,null,0,$pc_emp_id,'',true);
	}
	for($i=0;$i<count($all_client_update);$i++){
		$single_client_info	=	explode('-',$all_client_update[$i]);
		$add_single_order_no	=	$single_client_info[0];
		$add_single_pcode		=	$single_client_info[1];
		$add_single_skew		=	$single_client_info[2];
		$add_single_qty			=	$single_client_info[3];
		$add_main_pcode			=	$single_client_info[4];
		$add_main_skew			=	$single_client_info[5];
		$sql_addclient_qty		=	" UPDATE gft_cp_order_dtl SET GCO_ADD_CLIENTS=$add_single_qty WHERE GCO_ORDER_NO='$add_single_order_no' AND GCO_PRODUCT_CODE=$add_single_pcode AND GCO_SKEW='$add_single_skew' ";
		execute_my_query($sql_addclient_qty);
		$sql_getadd_fullfill	=	execute_my_query(" SELECT GCO_FULLFILLMENT_NO FROM gft_cp_order_dtl WHERE GCO_ORDER_NO='$add_single_order_no' AND GCO_PRODUCT_CODE=$add_single_pcode AND GCO_SKEW='$add_single_skew' LIMIT 1 ");
		if($row	=	mysqli_fetch_array($sql_getadd_fullfill)){
			execute_my_query(" UPDATE gft_cp_order_dtl SET GCO_REFERENCE_FULLFILLMENT_NO=".$row['GCO_FULLFILLMENT_NO']." WHERE GCO_ORDER_NO='$add_single_order_no' AND GCO_PRODUCT_CODE=$add_main_pcode AND GCO_SKEW='$add_main_skew' ");
		}
	}
	foreach ($client_order_no_arr as $val){
		$split_arr = explode('-', $val);
		update_additional_client_split_for_installed_order($split_arr[0],$split_arr[1],$split_arr[2]);
	}
	for($i=0;$i<count($all_custom_update);$i++){
		$single_cust_info	=	explode('-',$all_custom_update[$i]);
		$cust_single_order_no	=	$single_cust_info[0];
		$cust_single_pcode		=	$single_cust_info[1];
		$cust_single_skew		=	$single_cust_info[2];
		$cust_main_pcode		=	$single_cust_info[4];
		$cust_main_skew			=	$single_cust_info[5];
		$sql_getadd_fullfill	=	execute_my_query(" SELECT GCO_FULLFILLMENT_NO FROM gft_cp_order_dtl WHERE GCO_ORDER_NO='$cust_single_order_no' AND GCO_PRODUCT_CODE=$cust_single_pcode AND GCO_SKEW='$cust_single_skew' LIMIT 1 ");
		if($row = mysqli_fetch_array($sql_getadd_fullfill)){
			execute_my_query(" UPDATE gft_cp_order_dtl SET GCO_REFERENCE_FULLFILLMENT_NO=".$row['GCO_FULLFILLMENT_NO']." WHERE GCO_ORDER_NO='$cust_single_order_no' AND GCO_PRODUCT_CODE=$cust_main_pcode AND GCO_SKEW='$cust_main_skew' ");
		}
	}
	update_lead_status($lead_code, 8);
	if($inv_for_franchise) {
		execute_my_query(" update gft_lead_hdr_ext set GLE_INVOICE_FOR_FRANCHISE='1' where gle_lead_code='$lead_code' ");
	} else {
		execute_my_query(" update gft_lead_hdr_ext set GLE_INVOICE_FOR_FRANCHISE='0' where gle_lead_code='$lead_code' ");
	}
	$order_nos = array_unique($order_nos);
	foreach ($order_nos as $o) {
		generate_accounts_invoice($o, 'customer', 'order_split');
	}
	if($split_fail_msgs!='' and $receive_arg!='') {
		sendErrorWithCode($receive_arg, "Couldn't update split details for $split_fail_msgs products.", HttpStatusCode::INTERNAL_SERVER_ERROR);
		exit;
	}
	return true;
}
/**
 * @param string $login_emp_id
 * @param string $type
 * @param int $include_report_fields
 * @param int $req_mig
 * @param int $update_type
 * @param int $only_obd
 *
 * @return string
 */
function get_agile_team_chagnes_query($login_emp_id,$type,$include_report_fields=0,$req_mig=0,$update_type=0,$only_obd=0){
	global $uid;
	$only_obd = (int)get_samee_const("Enable_OBD_Migration");
	$set_update = "";
	$sales_solution_partners = get_partner_list('7,11','10');
	$hot_prospect_status_ls 		= "4,21,28,30,19";
	$eval_prospect_status_ls 		= "23,22,20";
	$pre_eval_prospect_status_ls 	= "24,25,12,14,1";
	$others_prospect_status_ls 		= "3,5,11,18,26,27,31,32";
	$report_join = 	" join gft_customer_status_master on (GLH_STATUS=GCS_CODE) ".
					" left join gft_business_territory_master on (GBT_TERRITORY_ID=GLH_TERRITORY_ID)  ".
					" left join gft_lead_create_category on (GCC_ID=GLH_CREATED_CATEGORY)  ".
					" left join gft_lead_type_master on (GLD_TYPE_CODE=GLH_LEAD_TYPE)  ".
					" left join gft_political_map_master pl on (pl.GPM_MAP_NAME=GLH_CUST_STATECODE)  ".
					" left join gft_customer_contact_dtl on (GCC_LEAD_CODE=GLH_LEAD_CODE)";
	$select_query = "select em.GEM_EMP_NAME,GLH_LFD_EMP_ID,GAT_AGILE_LMT_OWNER from gft_lead_hdr";
	if($include_report_fields==1){
		$select_query = " select GLH_LEAD_CODE,GLH_CUST_NAME,GBT_TERRITORY_NAME,GLH_LFD_EMP_ID,GAT_AGILE_LMT_OWNER,GLH_DATE,GLH_LEAD_TYPE, ".
						" GCS_NAME,em.GEM_EMP_NAME,em1.GEM_EMP_NAME as agile_owner_name,GCC_NAME,GLD_TYPE_NAME,GAT_IS_MIGRATION_DONE,GLH_VERTICAL_CODE,".
						" count(DISTINCT ga.GLD_ACTIVITY_ID) tot_app,count(DISTINCT GCF_FOLLOWUP_ID) tot_foll, ".
						" GROUP_CONCAT(DISTINCT gam1.gam_activity_desc) tot_app_desc,GROUP_CONCAT(DISTINCT gam2.gam_activity_desc) tot_foll_desc,".
						" GROUP_CONCAT(DISTINCT GCF_FOLLOWUP_DETAIL) foll_old_desc, GROUP_CONCAT(DISTINCT ga.GLD_NEXT_ACTION_DETAIL) app_old_desc,".
						" MAX(ga1.GLD_DATE) last_act_date, GLH_CONTACT_VERIFIED as GCC_VERIFIED_TYPE  from gft_lead_hdr";
	}
	if($update_type==1){//Update lfd Owner
		$select_query = "update gft_lead_hdr ";
		$set_update = " SET GLH_LFD_EMP_ID=GAT_AGILE_LMT_OWNER,GAT_IS_MIGRATION_DONE=1,GAT_UPDATED_ON=now(),GAT_IS_MIGRATION_DONE_BY='$uid' ";
	}else if($update_type==2){//Update appointment and followup completed
		$select_query = "update gft_lead_hdr ";
		$set_update = " SET ga.GLD_SCHEDULE_STATUS='4',ga.GLD_LAST_UPDATED_TIME=now(), ga.GLD_LAST_UPDATED_BY='$login_emp_id',  ".
					  " GCF_FOLLOWUP_STATUS=4,GCF_LAST_UPDATED_TIME=now(), GCF_UPDATED_BY='$login_emp_id' ";
	}
	$common_query = " $select_query".
					" INNER JOIN gft_lead_hdr_ext on(GLH_LEAD_CODE=GLE_LEAD_CODE)".
					" JOIN gft_emp_master em ON(em.GEM_EMP_ID=glh_lfd_emp_id)".
					" LEFT JOIN gft_agile_team_migration on(GAT_LEAD_CODE=GLH_LEAD_CODE)".
					" LEFT JOIN gft_emp_master em1 ON(em1.GEM_EMP_ID=GAT_AGILE_LMT_OWNER)".
					" left join gft_emp_master em_c on (GLH_CREATED_BY_EMPID=em_c.GEM_EMP_ID) ".
					" left join gft_leadcode_emp_map on (GLEM_EMP_ID=em_c.GEM_EMP_ID) ".
					" left join gft_cp_info on (GLEM_LEADCODE=CGI_LEAD_CODE) ".
					($include_report_fields==1?"$report_join":"").
					" LEFT JOIN gft_activity ga ON(ga.GLD_LEAD_CODE=GLH_lead_code AND ga.GLD_SCHEDULE_STATUS in(1,3,7)  AND ga.GLD_EMP_ID=GLH_LFD_EMP_ID AND ga.GLD_NEXT_ACTION>0)".
					" LEFT JOIN gft_cplead_followup_dtl pf ON(gcf_assign_to=GLH_LFD_EMP_ID AND gcf_followup_status in(1,3,7) AND GCF_LEAD_CODE=GLH_lead_code AND gcf_followup_action>0)".
					" left join gft_activity_master gam1 on (gam1.gam_activity_id=ga.gld_visit_nature)".
					" left join gft_activity_master gam2 on (gam2.gam_activity_id=pf.gcf_followup_action)".
					" LEFT JOIN gft_activity ga1 ON(ga1.GLD_LEAD_CODE=GLH_lead_code AND ga1.gld_visit_nature!=99)".					
					" $set_update".
					" where glh_lfd_emp_id=$login_emp_id AND LOWER(glh_country)='india' AND (ISNULL(GAT_IS_MIGRATION_DONE) OR GAT_IS_MIGRATION_DONE=0) AND GLH_LEAD_TYPE IN(1,3,13)".
					" and if(CGI_LEAD_CODE is null,1,CGI_LEAD_CODE not in ($sales_solution_partners)) ";
	$return_query = "";
	if($type==1){//Hot Prospect
		$return_query 		= " $common_query AND ( (GLH_STATUS=3 AND GLH_PROSPECTS_STATUS IN($hot_prospect_status_ls) )) ";
	}else if($type==2){//Prospect in Evaluations
		$return_query 		= " $common_query AND (GLH_STATUS=3 AND GLH_PROSPECTS_STATUS IN($eval_prospect_status_ls) ) ";
	}else if($type==3){//Prospect in Pre-Evaluation
		$return_query 	= " $common_query AND (GLH_STATUS=3 AND GLH_PROSPECTS_STATUS IN($pre_eval_prospect_status_ls) ) ";
	}else if($type==4){//Prospect status in other than above
		$return_query 		=  " $common_query AND (GLH_STATUS=3 AND GLH_PROSPECTS_STATUS IN($others_prospect_status_ls) ) ";
	}else if($type==5){//Customer
		$return_query 		=  " $common_query AND GLH_STATUS in(9,8) ";
	}else if($type==6){//Order Lost/ Opportunity lost/ Customer lost
		$return_query 		=  " $common_query AND GLH_STATUS IN(7,11,14) ".($only_obd==1?" AND GAT_OBD_REQUIRED=1":" AND GAT_OBD_REQUIRED=0");
	}else if($type==7){//New/ Qualify later/ Re-qualify/ Trying to reach
		$return_query 		=  " $common_query AND (GLH_STATUS IN(26,19,5,24)) ".($only_obd==1?" AND GAT_OBD_REQUIRED=1":" AND GAT_OBD_REQUIRED=0");
	}else if($type==8){//Others such as Not Interested, Not a valid lead and etc
		$return_query 		=  " $common_query AND GLH_STATUS IN(2,1,4,6,10,12,13,15,16,17,18,20,21,22,23,25)".($only_obd==1?" AND GAT_OBD_REQUIRED=1":" AND GAT_OBD_REQUIRED=0");
	}
	if($req_mig==1 && $include_report_fields==1 && $type!=7 && $type!=8  && $type!=6){
		$return_query =$return_query."  AND GLH_LFD_EMP_ID<>GAT_AGILE_LMT_OWNER";
	}else if($req_mig==2 && $include_report_fields==1 && $type!=7 && $type!=8  && $type!=6){
		$return_query =$return_query."  AND GLH_LFD_EMP_ID=GAT_AGILE_LMT_OWNER";
	}
	if($include_report_fields==1 && $update_type==0){
		$return_query =$return_query."  GROUP BY GLH_LEAD_CODE";
	}else if($update_type==0){
		$return_query =$return_query."  GROUP BY GLH_LEAD_CODE";
	}
	if($include_report_fields!=1){
		$return_query = "select GEM_EMP_NAME,GLH_LFD_EMP_ID, SUM(if(GLH_LFD_EMP_ID=GAT_AGILE_LMT_OWNER,1,0)) not_required_migration, ".
						"SUM(if(GLH_LFD_EMP_ID<>GAT_AGILE_LMT_OWNER,1,0)) required_migration from ($return_query) tmp_tbl GROUP BY GLH_LFD_EMP_ID";
	}
	return $return_query;
}
/**
 * @param string $order_no
 * @param boolean $is_proforma
 * @return string[int]
 */
function get_invoice_item_dtl_html_for_addon($order_no,$is_proforma=false) {
	if(!$is_proforma) {
		$proforma_no = get_single_value_from_single_table("gph_order_no", "gft_proforma_hdr", "gph_converted_order_no", $order_no);
	} else {
		$proforma_no = $order_no;
	}
	$prod_dtl_qry =<<<QRY
select gpm_skew_desc,glh_cust_name,GAC_SELL_RATE,GAC_MONTHS,GAC_OUTLETS,GAC_START_DATE,GAC_END_DATE,GHM_CODE,
gpp_cgst_per,gpp_sgst_per,gpp_igst_per from gft_add_on_commission_dtl join gft_proforma_product_dtl on (gac_order_no=gpp_order_no 
and gac_product_code=gpp_product_code and gac_product_skew=gpp_product_skew) join gft_product_master on 
(gac_product_code=gpm_product_code and gac_product_skew=gpm_product_skew) left join gft_hsn_vs_tax_master on (GHT_ID=GPM_TAX_ID)
left join gft_product_hsn_master on (GHT_HSN_ID=GHM_ID) join gft_lead_hdr on (gac_lead_code=glh_lead_code)
where gac_order_no='$proforma_no'
QRY;
	$prod_res = execute_my_query($prod_dtl_qry);
	$html = '';
	if(mysqli_num_rows($prod_res)>0) {
		$html .= "<table border='1' style='border-collapse: collapse;' width='100%'>";
		$html .=<<<END
		<thead>
			<tr>
				<th style='width: 5%;'>SAC</th>
				<th style='width: 50%;'>Name of Product and/or Service</th>
				<th style='width:  6%;'>Rate</th>
				<th style='width:  6%;'>No. of Months</th>
				<th style='width:  6%;'>No. of Outlets</th>
				<th style='width:  6%;'>Duration</th>
				<th style='width: 21%;'>Value</th>
			</tr>
		</thead><tbody>
END;
		$tax_dtls = /*.(float[string]).*/array();
		$total_val = 0;
		while($row = mysqli_fetch_array($prod_res)) {
			$sac = $row['GHM_CODE'];
			$prod_name = $row['gpm_skew_desc'];
			$prod_name .= "<br/>for <i>".$row['glh_cust_name']."</i>";
			$rate = $row['GAC_SELL_RATE'];
			$months = $row['GAC_MONTHS'];
			$outlets = $row['GAC_OUTLETS'];
			$start_dt = $row['GAC_START_DATE'];
			$end_dt = $row['GAC_END_DATE'];
			$cgst_per = $row['gpp_cgst_per'];
			$sgst_per = $row['gpp_sgst_per'];
			$igst_per = $row['gpp_igst_per'];
			$val = $rate*$months*$outlets;
			$total_val += $val;
			if($cgst_per>0.0) {
				$tax_amt = $val*$cgst_per/100;
				$gst_amt = number_format($tax_amt,2,".","");
				if(!isset($tax_dtls["CGST-$cgst_per"])) {
					$tax_dtls["CGST-$cgst_per"] = (float)$gst_amt;
				} else {
					$tax_dtls["CGST-$cgst_per"] += (float)$gst_amt;
				}
			}
			if($sgst_per>0.0) {
				$tax_amt = $val*$sgst_per/100;
				$gst_amt = number_format($tax_amt,2,".","");
				if(!isset($tax_dtls["SGST-$sgst_per"])) {
					$tax_dtls["SGST-$sgst_per"] = (float)$gst_amt;
				} else {
					$tax_dtls["SGST-$sgst_per"] += (float)$gst_amt;
				}
			}
			if($igst_per>0.0) {
				$tax_amt = $val*$igst_per/100;
				$gst_amt = number_format($tax_amt,2,".","");
				if(!isset($tax_dtls["IGST-$igst_per"])) {
					$tax_dtls["IGST-$igst_per"] = (float)$gst_amt;
				} else {
					$tax_dtls["IGST-$igst_per"] += (float)$gst_amt;
				}
			}
			$duration = '';
			if($start_dt!=$end_dt) {
				$duration = date('M Y',strtotime($start_dt))." - ".date('M Y',strtotime($end_dt));
			} else {
				$duration = date('M Y',strtotime($start_dt));
			}
			$val_disp = number_format($val,2,".","");
			$rate_disp = number_format($rate,2,".","");
			$html .= "<tr><td align=center>$sac</td>".
					 "<td align=center>$prod_name</td>".
					 "<td align=right>$rate_disp</td>".
					 "<td align=right>$months</td>".
					 "<td align=right>$outlets</td>".
					 "<td align=center>$duration</td>".
					 "<td align=right>$val_disp</td></tr>";
		}
		$total_val_disp = number_format($total_val,2,".","");
		$html .= "</tbody><tfoot><tr><th align=center colspan=6>Total Value</th><td align=right>$total_val_disp</td></tr>";
		$total_tax = 0;
		foreach ($tax_dtls as $slab=>$amt) {
			$gstdtl = explode("-",$slab);
			$gst_type = $gstdtl[0];
			$gst_per = $gstdtl[1];
			$html .= "<tr><th colspan=6>$gst_type [$gst_per %]</th><td align=right>".number_format($amt,2,'.','')."</td></tr>";
			$total_tax += $amt;
		}
		$grand_total = $total_val + $total_tax;
		$round_off_val = number_format(((round($grand_total)>$grand_total)?(round($grand_total)-$grand_total):($grand_total-round($grand_total))),2,".","");
		$round_off_action = (round($grand_total)>$grand_total)?'(+)':"(-)";
		$total = ($round_off_action=='(+)')?($grand_total+(float)$round_off_val):($grand_total-(float)$round_off_val);
		$total_disp = number_format($total,2,".","");
		$html .= "<tr><th colspan=6>Roundoff$round_off_action</th><td align=right>$round_off_val</td></tr>";
		$html .= "<tr><th colspan=6>Grand Total</th><td align=right>".number_format($total_disp,2,'.','')."</td></tr>";
		$html .= "</tfoot></table>";
	}
	$dtl = /*.(string[int]).*/array();
	$dtl[] = $html;
	return $dtl;
}
/**
 * @param string $emp_id
 * @param string $lead_code
 * @param string $prod_dtl
 * @param string $order_type
 * @param string $order_no
 * @return string
 */
function check_existing_quote_proforma($emp_id,$lead_code,$order_type,$order_no,$prod_dtl) {
	$codes = '';
	$comma = "";
	$team = ''; // 23 - field team 54 - annuity team
	$today = date('Y-m-d');
	$start_dt = date('Y-m-d',strtotime('-15 days'));
	$wh_cond1 = " and GQH_ORDER_STATUS!='C' and gqh_lead_code='$lead_code' and (GQH_CONVERTED_ORDER_NO='' or GQH_CONVERTED_ORDER_NO is null) and gqh_emp_id!=9999 and GQH_ORDER_DATE>='$start_dt' ";
	$wh_cond2 = " and GPH_ORDER_STATUS!='C' and gph_lead_code='$lead_code' and (GPH_CONVERTED_ORDER_NO='' or GPH_CONVERTED_ORDER_NO is null) and gph_emp_id!=9999 and GPH_VALIDITY_DATE>='$today' and GPH_ORDER_DATE>='$start_dt' ";
	if(is_authorized_group_list($emp_id, array(23))) {
		$team = '54';
	} else if(is_authorized_group_list($emp_id, array(54))) {
		$team = '23';
	}
	if($order_type=='1') { // from quotation creation/submit
		$wh_cond1 .= " and gqh_order_no!='$order_no' ";
	} else if($order_type=='2') { // from proforma creation/submit
		$wh_cond2 .= " and gph_order_no!='$order_no' ";
	}
	if(count($prod_dtl)>0) {
		foreach ($prod_dtl as $k=>$code_skew) {
			$codes .= "$comma'$code_skew'";
			$comma = ",";
		}
		if($codes!='') {
			$wh_cond1 .= " and concat(gqp_product_code,'-',gqp_product_skew) in ($codes) ";
			$wh_cond2 .= " and concat(gpp_product_code,'-',gpp_product_skew) in ($codes) ";
		}
		$quote_qry = " select 'Quotation' as type,gqh_order_no order_no,gpm_skew_desc prod_name, ".
					 " gem_emp_name from gft_quotation_hdr join gft_quotation_product_dtl on (gqp_order_no=gqh_order_no) ".
					 " join gft_product_master on (gqp_product_code=gpm_product_code and gqp_product_skew=gpm_product_skew) ".
					 " join gft_emp_master em on (gqh_emp_id=em.gem_emp_id and em.gem_status='A') left join gft_role_group_master rg on (grg_role_id=gem_role_id) ".
					 " left join gft_emp_group_master rg1 on (rg1.gem_emp_id=em.gem_emp_id) ".
					 " left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id) ".
					 " where g1.ggm_group_id='$team' $wh_cond1 group by gqh_order_no,gqp_product_code,gqp_product_skew ";
		$proforma_qry = " select 'Proforma' as type,gph_order_no order_no,gpm_skew_desc prod_name, ".
						" gem_emp_name from gft_proforma_hdr join gft_proforma_product_dtl on (gpp_order_no=gph_order_no) ".
						" join gft_product_master on (gpp_product_code=gpm_product_code and gpp_product_skew=gpm_product_skew) ".
						" join gft_emp_master em on (gph_emp_id=em.gem_emp_id and em.gem_status='A') left join gft_role_group_master rg on (grg_role_id=gem_role_id) ".
						" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=em.gem_emp_id) ".
						" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id) ".
						" where g1.ggm_group_id='$team' $wh_cond2 group by gph_order_no,gpp_product_code,gpp_product_skew ";
		$check_qry = $quote_qry." union all ".$proforma_qry;
		$check_res = execute_my_query($check_qry);
		$existing_dtls = array();
		while($row = mysqli_fetch_array($check_res)) {
			$dtl = array();
			$type = $row['type'];
			$dtl['order_no'] = $row['order_no'];
			$dtl['skew_desc'] = $row['prod_name'];
			$dtl['emp'] = $row['gem_emp_name'];
			if(!isset($existing_dtls[$type])) {
				$existing_dtls[$type] = array();
			}
			$existing_dtls[$type][] = $dtl;
		}
		if(count($existing_dtls)>0) {
			return json_encode(array('status'=>'failed','message'=>$existing_dtls));
		} else {
			return json_encode(array('status'=>'success','message'=>''));
		}
	} else {
		return json_encode(array("status"=>"error","message"=>"No product details sent"));
	}
}
/**
 * @param string $query
 * @param string $lead_code_field
 *
 * @return string
 */
function get_query_for_sms_mail_send($query, $lead_code_field){
	global $contact_fields;
	$query = "select GLH_COUNTRY, gcc_lead_code as GLH_LEAD_CODE, gcc_lead_code as Customer_Id,".
	" GLH_CUST_NAME AS Customer_Name, GEM_EMAIL, gem_status  $contact_fields from  gft_lead_hdr " .
	" INNER JOIN gft_customer_contact_dtl ccd ON(gcc_lead_code=GLH_LEAD_CODE)".
	" LEFT JOIN gft_cp_info ci ON(CGI_LEAD_CODE=GLH_LEAD_CODE)".
	" LEFT JOIN gft_emp_master em ON(CGI_EMP_ID=GEM_EMP_ID) ".
	" where GLH_LEAD_CODE IN(SELECT $lead_code_field FROM ($query) tbl) " .	
	" group by GLH_LEAD_CODE" ;
	return $query;
}
/**
 * @param boolean $for_combo
 * @return string[string] | string[int][int]
 * 
 */ 
function get_leave_type_labels($for_combo=false) {
	$types = array('CL'=>'Casual Leave','SL'=>'Sick Leave','P'=>'Permission','CSL'=>'Casual/Sick Leave',"ML"=>"Maternity Leave","PL"=>"Paternity Leave",'PRL'=>'Privileged Leave','OD'=>'On-Duty','IB'=>'Internship Break','PR'=>'Project Review');
	if($for_combo) {
		$types = array(array('CL','Casual Leave'),array('SL','Sick Leave'),array('P','Permission'),array('CSL','Casual/Sick Leave'),array("ML","Maternity Leave"),array("PL","Paternity Leave"),array('PRL','Privileged Leave'),array('IB','Internship Break'),array('PR','Project Review'));
	}
	return $types;
}
/**
 * @param string[int] $productcode
 * @return boolean
 */
function check_invoice_fields_required($productcode) {
	$wh_vals = $comma = '';
	foreach ($productcode as $pcode) {
		$prod_arr = explode("-",$pcode);
		$wh_vals .= $comma."'".$prod_arr[0]."-".$prod_arr[1]."'";
		$comma = ",";
	}
	$show_inv_option = false;
	$chk_qry = execute_my_query(" select pm.gft_skew_property,pfm.gpm_is_base_product,pm.gpm_product_code,pm.gpm_order_type, ".
			" gpm_product_type from gft_product_master pm join gft_product_family_master pfm on (pm.gpm_product_code=pfm.gpm_product_code) ".
			" where concat(pm.gpm_product_code,'-',pm.gpm_product_skew) in ($wh_vals) ");
	while($row = mysqli_fetch_array($chk_qry)) {
		$skew_ppty  = $row['gft_skew_property'];
		$base_prod  = $row['gpm_is_base_product'];
		$prod_code  = $row['gpm_product_code'];
		$order_type = $row['gpm_order_type'];
		$prod_type = $row['gpm_product_type'];
		if($skew_ppty=='2' or (in_array($skew_ppty,array('1','11')) and $base_prod=='Y' and $prod_type!='8') or ($prod_code=='308' and $order_type=='2')) {
			$show_inv_option = true;
		}
	}
	return $show_inv_option;
}
/**
 * @param string[int] $productcode
 * @return string
 */
function get_prods_for_immediate_invoice($productcode) {
	$i = 1;
	$prod_name = '';
	foreach ($productcode as $pcode) {
		if(!check_invoice_fields_required(array($pcode))) {
			$prod_arr = explode("-",$pcode);
			$wh_vals = $prod_arr[0]."-".$prod_arr[1];
			$prod_name .= "<br/>$i. ".get_single_value_from_single_table("gpm_skew_desc", "gft_product_master", "concat(gpm_product_code,'-',gpm_product_skew)", $wh_vals);
			$i++;
		}
	}
	return $prod_name;
}
/**
 * 
 * @param string $purpose
 * @return string[][] | string[]
 */
function get_feedback_req_status_array($purpose) {
    $status_rows = array();
    $status_rows['1'] = 'Requested by Agent';
    $status_rows['2'] = 'Request Failure';
    $status_rows['3'] = 'Pending Feedback from Customer';
    $status_rows['4'] = 'Feedback Given by Customer';
    if($purpose=='filter_combo') {
        $return_array = array();
        foreach ($status_rows as $id=>$label) {
            $return_array[] = array($id,$label);
        }
        return $return_array;
    }
    return $status_rows;
}
/**
 * @param string $lead_code
 * @param string $product_code
 * @param string $product_skew
 * @param string $start_date
 * @return void
 */
function assign_palr_support_tickets($lead_code,$product_code,$product_skew,$start_date) {
    $state_id = ''; $product_group = ''; $cust_name = '';
    $cust_dtls_qry = " select glh_main_product,gpm_map_id,glh_cust_name,ifnull(glh_cust_streetaddr2,'') glh_cust_streetaddr2 ".
                     " from gft_lead_hdr ".
                     " left join gft_political_map_master on (glh_cust_statecode=gpm_map_name and gpm_map_type='S' ".
                     " and gpm_map_status='A') where glh_lead_code='$lead_code' ";
    $cust_dtls_res = execute_my_query($cust_dtls_qry);
    while($row = mysqli_fetch_assoc($cust_dtls_res)) {
        $state_id = $row['gpm_map_id'];
        $product_group = $row['glh_main_product'];
        $cust_name = $row['glh_cust_name'].($row['glh_cust_streetaddr2']!=''?"-".$row['glh_cust_streetaddr2']:'');
    }
    $edition = get_single_value_from_single_table("GPM_PRODUCT_TYPE", "gft_product_master", "concat(gpm_product_code,'-',gpm_product_skew)", "$product_code-$product_skew");
    $assign_to_emp = get_cst_agent_for_customer($state_id, $product_group);
    $tickets = /*.(string[int][int]).*/array();
    $support_qry = " select gts_interval_days,gts_complaint from gft_cst_ticket_schedule ".
                   " where gts_status=1 and gts_edition='$edition' order by gts_interval_days ";
    $support_res = execute_my_query($support_qry);
    while($support_row = mysqli_fetch_assoc($support_res)) {
        $day_count = $support_row['gts_interval_days'];
        $complaint = $support_row['gts_complaint'];
        $status = 'T20';
        $schedule_dt = add_date($start_date, $day_count);
        while(is_holiday($schedule_dt) or isSecondFourthFifthSaturday($schedule_dt) or isWeekend($schedule_dt)) {
            $schedule_dt = add_date($schedule_dt, 1);
        }
        $summary = "Customer Success Call scheduled for premium ALR customer during license renewal";
        if($complaint=='454') {
            $summary = "Customer Success Visit scheduled for premium ALR customer during license renewal";
        }
        $support_id = insert_support_entry($lead_code,$product_code,$product_skew,'','','9999','',$summary,$complaint,$status,
                                           $schedule_dt,null,$assign_to_emp,null,'4',$summary,false,'',null,'','3',true);
        $tickets[] = array($support_id,$complaint,$schedule_dt);
    }
    $comp_dtls = "&nbsp;";
    if(count($tickets)>0) {
        $hdr_cell_style = "style='text-align:center;border:1px solid black;border-collapse:collpase;padding:3px;'";
        $table_row_style = "style='border:1px solid black;border-collapse:collpase;padding:3px;'";
        $comp_dtls = "<table style='width:60%;border-collapse:collpase;'>".
                     "<tr $table_row_style><th $hdr_cell_style>Support ID</th>".
                     "<th $hdr_cell_style>Complaint</th>".
                     "<th $hdr_cell_style>Scheduled Date</th></tr>";
        foreach($tickets as $ti=>$tdtls) {
            $comp_id = "<a href='".CURRENT_SERVER_URL."/telesupport.php?c_id=".$tdtls[0]."&from_dt=&to_dt=&skip_auto_refresh=1' target=_blank>".$tdtls[0]."</a>";
            $comp = ($tdtls[1]=='453'?'Customer success call':'Customer Success Visit');
            $schedule_date = date('M d,Y',strtotime($tdtls[2]));
            $comp_dtls .= "<tr><td $hdr_cell_style>$comp_id</td><td $table_row_style>$comp</td><td $table_row_style>$schedule_date</td></tr>";
        }
        $comp_dtls .= "</table>";
    }
    $db_content_config = array(
        'Agent_Name'=>array(get_single_value_from_single_table("gem_emp_name", "gft_emp_master", "gem_emp_id", $assign_to_emp)),
        'Customer_Name'=>array($cust_name),
        'Customer_Id'=>array($lead_code),
        'comp_id'=>array($comp_dtls)
    );
    send_formatted_mail_content($db_content_config,8,358,array($assign_to_emp));
}
/**
 * @param string $chatbot_brand_id
 *
 * @return string
 */
function get_chatbot_product_query($chatbot_brand_id){
    $query = "select topic_id ,concat(product_name,' ',COALESCE(product_name_alias,'')) product_name from gft_chatbot_topic_master".
        " where product_name!='' ".($chatbot_brand_id>0?"and brand_id=$chatbot_brand_id":"");
    return $query;
}
/**
 * @param string $chatbot_product_id
 *
 * @return string
 */
function get_chatbot_uber_topic_query($chatbot_product_id){
    $query = "select GUT_ID id, CONCAT(product_name,' - ',GUT_NAME) name from gft_uberization_topic_master ".
        " INNER JOIN gft_chatbot_topic_master ON(GUT_PRODUCT_ID=topic_id) ".
        " where 1".($chatbot_product_id>0?" AND GUT_PRODUCT_ID='$chatbot_product_id'":"");
    return $query;
}
/**
 * @param string $chatbot_question_topic
 *
 * @return string
 */
function get_chatbot_uber_category($chatbot_question_topic){
    $query = "select GUC_ID id, CONCAT(product_name,' - ',GUT_NAME,' - ',GUC_NAME) name from gft_uberization_category_master ".
        " INNER JOIN gft_uberization_topic_master ON(GUC_TOPIC_ID=GUT_ID) ".
        " INNER JOIN gft_chatbot_topic_master on(topic_id=GUT_PRODUCT_ID)".
        " where 1  ".($chatbot_question_topic>0?"AND GUC_TOPIC_ID='$chatbot_question_topic'":"");
    return $query;
}
/**
 * @param int $report_id
 * @param int $filter_id
 *
 * @return mixed
 **/
function get_filter_display_name($report_id,$filter_id){
    $display_name = get_single_value_from_single_query("GFG_DISPLAY_NAME"," select GFG_DISPLAY_NAME from gft_filter_generate where GFG_FILTER_ID=$filter_id and GFG_REPORT_ID=$report_id ");
    return $display_name;
}
/**
 * @param int $customer_code
 **/
function renewal_payment_pattern_update($customer_code)
{
	$renewal_info = array();
	$renewal_sub = " select count(1) as total, GOD_LEAD_CODE, GOD_ORDER_DATE, GAD_ASS_START_DATE, DATEDIFF(GAD_ASS_START_DATE, GOD_ORDER_DATE), ".
               " SUM(if(DATEDIFF(GAD_ASS_START_DATE, GOD_ORDER_DATE)>0,1,null)) as active, ".
               " SUM(if(DATEDIFF(GAD_ASS_START_DATE,GOD_ORDER_DATE)=0,1,null)) as modr, ".
               " SUM(if(DATEDIFF(GAD_ASS_START_DATE,GOD_ORDER_DATE)<0,1,null)) as lactive ".
               " from gft_order_hdr join gft_order_product_dtl on(GOD_ORDER_NO=GOP_ORDER_NO) ".
               " join gft_ass_dtl on (GAD_ASS_ORDER_NO=GOD_ORDER_NO and GAD_PRODUCT_CODE=GOP_PRODUCT_CODE) ".
               " join gft_product_family_master f on (GOP_PRODUCT_CODE=f.GPM_PRODUCT_CODE) ".
               " join gft_product_master m on (GOP_PRODUCT_CODE=m.GPM_PRODUCT_CODE AND m.GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			   " where GPM_IS_BASE_PRODUCT='Y' and GFT_SKEW_PROPERTY in (15,4) ";
			   
	if($customer_code != ""){
		$renewal_sub .= " and GOD_LEAD_CODE=$customer_code ";
	}
	$renewal_sql = "select GOD_LEAD_CODE, sum(active) as active, sum(modr) as modr, sum(lactive) as lactive, sum(total) as total from (".$renewal_sub." group by GOD_ORDER_NO ) data group by GOD_LEAD_CODE ";
	$renewal_res = execute_my_query($renewal_sql);

	while($row = mysqli_fetch_array($renewal_res))
	{
		$array_val = array("active"=>$row['active'], "moderate"=>$row['modr'], "lessactive"=>$row['lactive']);
		if(count(array_keys($array_val, max($array_val)))>1){ 
			$renewal_new = $renewal_sub." and GOD_LEAD_CODE='".$row['GOD_LEAD_CODE']."' group by GOD_ORDER_NO order by GOD_ORDER_DATE DESC limit 1";
			$renewal_sub_res = execute_my_query($renewal_new);
			if($result = mysqli_fetch_array($renewal_sub_res)){
				$pattern = ($result['active'] != null) ? "1" : (($result['modr'] != null) ? "2" : (($result['lactive']!= null) ? "3" : ""));
				$renewal_info[$pattern][] = $row['GOD_LEAD_CODE'];
			}
		}else{
			$array_index = array_search(max($array_val),$array_val);
			$pattern = ($array_index=="active") ? "1" : ($array_index=="moderate" ? "2" : "3");    
			$renewal_info[$pattern][] = $row['GOD_LEAD_CODE'];
		}
	}
	
	#update renewal pattern in lead extension table
	foreach($renewal_info as $key=>$val){
		if(count($val)>0){
			$cust_codes = implode(",",$val);
			execute_my_query("update gft_lead_hdr_ext set GLE_RENEWAL_PAYMENT_PATTERN='$key',GLE_ROW_MODIFIED_TIME=GLE_ROW_MODIFIED_TIME where GLE_LEAD_CODE in ($cust_codes) ");
		}
	}
}
?>
