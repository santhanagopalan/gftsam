<?php
require_once(__DIR__ ."/common_util.php");
require_once __DIR__ ."/ismile/ismile_util.php";
require_once(__DIR__ ."/audit_util.php");
/**
 * @param string $date
 *
 * @return boolean
 */
function isDateInHoliday($date){
	$rows=execute_my_query("select GHL_DATE from gft_holiday_list where GHL_DATE='$date'");
	if(mysqli_num_rows($rows)>0){
		return true;
	}
	return false;
}
/**
 * @param int $no_of_days
 * @param boolean $past_date
 * @param string $ref_date
 *
 * @return string
 */
function add_date_without_holidays($no_of_days,$past_date=false,$ref_date=''){
	$count=0;
	$dt = date('Y-m-d');
	if($ref_date!='') {
		$dt = db_date_format($ref_date);
	}
	$days=1;
	$date_now = $dt;
	while($count<$no_of_days){
		$diff = ($past_date)?-$days:$days;
		$date_now	=	add_date($dt, $diff);
		if(!isWeekend($date_now) && !isSecondFourthFifthSaturday($date_now) && !isDateInHoliday($date_now)){
			$count++;
		}
		$days++;
	}
	return $date_now;
}
/**
 * @param int $uid
 * @param int $month
 * @param int $year
 * @param string $field_name
 * @param int $value
 * 
 * @return void
 */
function update_value_in_pd_summary($uid,$month,$year,$field_name,$value){
	$res_check	=	execute_my_query("select GEP_EMP_ID from  gft_emp_pd_summary  where GEP_EMP_ID=$uid and GEP_MONTH=$month and GEP_YEAR=$year ");
	if(mysqli_num_rows($res_check)>0){
		execute_my_query("update gft_emp_pd_summary set $field_name=$field_name+$value where GEP_EMP_ID=$uid and GEP_MONTH=$month and GEP_YEAR=$year");
	}else{
		execute_my_query("insert into gft_emp_pd_summary(GEP_EMP_ID,GEP_MONTH,GEP_YEAR,$field_name) values($uid,$month,$year,$value)");
	}
}
/**
 * @param int $product_delivery_id
 * @param string $lead_code
 *
 * @return string
 */
function get_sop_business_overview_query($product_delivery_id,$lead_code=''){
	$join_cond = " GPA_PD_ID='$product_delivery_id' ";
	if(($product_delivery_id=='0' or $product_delivery_id=='') and $lead_code!='0' and $lead_code!='') {
		$join_cond = " GPA_LEAD_CODE='$lead_code' ";
	}
	$sql_overview = " SELECT GPQ_ID,GPQ_QUESTION,GPQ_AVAILABLE_ANS,GPQ_INPUT_TYPE,GPA_QUESTION_ANS,GPS_DESC,GPS_ID,GPQ_ID FROM gft_pcs_question_master qm ".
					" INNER JOIN gft_pcs_sop_submodule_master ss ON(GPQ_SUB_MODULE_ID=GPS_ID) ".
					" INNER JOIN gft_pcs_sop_module_master mm ON(GSOP_ID=GPS_MODULE_ID) ".
					" INNER JOIN gft_pcs_sop_master sm ON(GPM_ID=GSOP_SOP_MASTER_ID) ".
					" LEFT JOIN gft_pcs_audit_dtl pa ON(GPA_QUESTION_ID=GPQ_ID AND $join_cond)".
					" WHERE GPM_ID='1' AND GPQ_STATUS=1 AND GPQ_PRODUCT_EDITION=0 group by GPQ_ID order by GSOP_ORDER_BY, GPQ_ORDER_BY";
	return $sql_overview;
}
/**
 * @param int $product_delivery_id
 * @param string $join
 * @param int $vertical_id
 * @param string $lead_code
 * 
 * @return string
 */
function get_sop_operational_req_query($product_delivery_id,$join="LEFT",$vertical_id=0,$lead_code=''){
	$where_con = "  AND GPA_REQ_ID=GPR_ID";
	if($join==''){
		$where_con='';
	}if($join=='Partial'){
		$where_con='';$join="LEFT";
	}
	$wh_con = " GPA_PD_ID='$product_delivery_id' ";
	$gcm_edition = 0;
	if(($product_delivery_id=='0' or $product_delivery_id=='') and $lead_code!='0' and $lead_code!='') {
		$wh_con = " gpa_lead_code='$lead_code' ";
	}
	$pd_dtls			=	get_product_delivery_training_dtl($product_delivery_id);
	$order_no			=   isset($pd_dtls['orderno'])?$pd_dtls['orderno']:"";
	if($order_no!=""){
		$gcm_edition       = (int)get_gcm_order_edition($order_no);
	}else{
		$gcm_edition = -1;// Apply this condition for corporate customer when doing BQ before creating order. i.e show all question except ERP edition.
	}	
	if($lead_code!='0' && $lead_code!=''){
		$lead_type = get_single_value_from_single_table("GLH_LEAD_TYPE", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
		if($lead_type==1){
			$gcm_edition = "18";//ERP edition to show only for end user lead type.
		}
	}
	$sql_operation = " GPQ_ID,GPM_DESC,GSOP_ID, GSOP_DESC,GPS_ID,GPS_DESC,GPQ_QUESTION,GPQ_AVAILABLE_ANS,".
			" GPQ_INPUT_TYPE FROM gft_pcs_question_master qm ".
			" INNER JOIN gft_pcs_sop_submodule_master ss ON(GPQ_SUB_MODULE_ID=GPS_ID)".
			" INNER JOIN gft_pcs_sop_module_master mm ON(GSOP_ID=GPS_MODULE_ID)".
			" INNER JOIN gft_pcs_sop_master sm ON(GPM_ID=GSOP_SOP_MASTER_ID)".
			" LEFT JOIN gft_bq_mapping_with_vertical bv on(GBM_BQ_ID=GPQ_ID)".
			" LEFT JOIN gft_bq_mapping_with_edition be ON(GBE_BQ_ID=GPQ_ID)".
			" LEFT JOIN  gft_pcs_requirement_master pr ON(GPR_QUESTION_ID=GPQ_ID)".
			" LEFT JOIN gft_bq_requirement_with_vertical_map on(GPR_ID=GBR_REQ_ID)".
			" $join	JOIN gft_pcs_audit_dtl pa ON($wh_con AND GPA_QUESTION_ID=GPQ_ID $where_con)".
			" WHERE GPM_ID=2 AND GPQ_STATUS=1 ".//AND GPQ_PRODUCT_TYPE IN(0".($pd_order_type==3?",2":",1").")".
			" AND (GPQ_VERTICAL_CODE=0 ".($vertical_id!=0?" OR (GPQ_VERTICAL_CODE=1 AND GBM_VERTICAL_ID=$vertical_id)":" ")." ) ".
			" AND (GPQ_PRODUCT_EDITION=0 ".($gcm_edition>0?" OR (GPQ_PRODUCT_EDITION=1 AND GBE_EDITION_ID=$gcm_edition)":($gcm_edition==-1?" OR (GPQ_PRODUCT_EDITION=1 AND GBE_EDITION_ID!=18)":" "))." ) ".
			" AND (GPR_VERTICAL_TYPE=0 ".($vertical_id!=0?" OR (GPR_VERTICAL_TYPE=1 AND GBR_VERTICAL_ID=$vertical_id)":" ")." ) ";
	return $sql_operation;
}
/**
 * @param string $lead_code
 * @param string $selected
 *
 * @return string
 */
function get_spoc_design($lead_code,$selected=''){
	$cust_country=	get_single_value_from_single_table("glh_country", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
	$contact_arr	=	get_customer_spoc_list($lead_code,($cust_country=='India'?'1':'2'));
	$spoc_list_arr	=	isset($contact_arr[0])?$contact_arr[0]:array();
	$spoc_default	=	isset($contact_arr[1])?$contact_arr[1]:"";
	$spoc_options	="";
	$option_tag		="";
	$optgroup=false;
	foreach($spoc_list_arr as $key1=>$value1){
		$option_val	=	$spoc_list_arr[$key1]['id'];
		$option_sel	=	$spoc_list_arr[$key1]['name'];
		$contact_type=$spoc_list_arr[$key1]['type'];
		if(isset($spoc_list_arr[$key1]['tag']) && $spoc_list_arr[$key1]['tag']!=$option_tag){
			if($option_tag!=''){$spoc_options	.=	"</optgroup>";}
			$option_tag =$spoc_list_arr[$key1]['tag'];
			$spoc_options	.=	"<optgroup label='$option_tag'>";
			$optgroup=true;
		}
		if($selected==$option_val) {
			$spoc_default = $option_val;
		}
		$spoc_options .= "<option value='$option_val'  ".($spoc_default==$option_val?'Selected':"contact_type='$contact_type'").">$option_sel</option>";
	}
	if($optgroup){
		$spoc_options	.=	"</optgroup>";
	}
	return $spoc_options;
}
/**
 * @param string $lead_code
 *
 * @return string
 */
function get_spoc_html_design($lead_code){
	$spoc_options 	= 	get_spoc_design($lead_code);
	$time_spent_option = "";$inc=1;
	while($inc<=8){
		$time_spent_option .="<option value='$inc'>$inc Hour</option>";
		$inc++;
	}
	$return_html=<<<END
	<tr><td align="left" class='head_blue'><span class="">Time Spent:</span><span class="mandatory">*</span>&nbsp;</td><td><select name='timespent' id='timespent'><option value=''>Select</option>$time_spent_option</select></td></tr>
				<tr><td align="left" class='head_blue'><span class="">Select SPOC:</span><span class="mandatory">*</span>&nbsp;</td><td><select name='spoc' id='spoc'><option value=''>Select</option>$spoc_options</select></td></tr>
				<tr><td align="left" valign='center' class='head_blue'><span>Select Trainees:</span>&nbsp;</td><td><select name='trainees[]' id='trainees' multiple><option value=''>Select</option>$spoc_options</select></td></tr>
				
END;
	return $return_html;
}
/** 
 * @param int $uid
 * @param int $lead_code
 * @param string $order_no
 * 
 * @return void
 */
function update_pd_age_in_summary($uid, $lead_code,$order_no){
	$month	=	date("m");
	$year	=	date("Y");
	$date_now	=	date("Y-m-d H:i:s");
	$first_date	=	date('Y-m-d H:i:s',strtotime("-31 days"));
	$sql_query	=	" select GCD_AGE_START_DATE from gft_coupon_distribution_dtl where gcd_to_id=$lead_code and GCD_REF_ORDER_NO like '%$order_no%' limit 1";
	$res_hand	=	execute_my_query($sql_query);
	if(mysqli_num_rows($res_hand)>0){
		$row_act	=	mysqli_fetch_array($res_hand);		
		if($row_act['GCD_AGE_START_DATE']!=''){
			$first_date	=	$row_act['GCD_AGE_START_DATE'];
		}
	}
	$tot_second	=	(strtotime($date_now)-strtotime($first_date));
	$field_name	=	"GEP_HANDOVER30";
	if($tot_second<=604800){//<=7 days
		$field_name	=	"GEP_HANDOVER1_7";
	}else if($tot_second<=1296000){// >7 and <=15
		$field_name	=	"GEP_HANDOVER8_15";
	}else if($tot_second<=1814400){// >15 and <=21
		$field_name	=	"GEP_HANDOVER16_21";
	}else if($tot_second<=2592000){// >21 and <=30
		$field_name	=	"GEP_HANDOVER22_30";
	}else{// >30
		$field_name	=	"GEP_HANDOVER30";
	}
	update_value_in_pd_summary($uid,(int)$month,(int)$year,$field_name,1);
}
/**
 * @param int $uid
 * @param string $last_audit_date
 *
 * @return void
 */
function update_pd_handover_age_in_summary($uid, $last_audit_date){
	$date_now	=	date("Y-m-d H:i:s");
	$month	=	date("m");
	$year	=	date("Y");
	$field_name	=	"";
	if($last_audit_date==''){
		$last_audit_date	=	date('Y-m-d H:i:s',strtotime("-1 days"));
	}
	$tot_second	=	(strtotime($date_now)-strtotime($last_audit_date));
	if($tot_second<=86400){// <24Hrs
		$field_name	=	"GEP_HANDOVER_LESSTHEN_24HR";
	}else{// >24Hrs
		$field_name	=	"GEP_HANDOVER_MORETHEN_24HR";
	}
	update_value_in_pd_summary($uid,(int)$month,(int)$year,$field_name,1);
}
/**
 * @param string $reference_no
 *
 * @return int
 */
function get_product_delivery_id($reference_no){
	$product_delivery_id=0;
	$result	=	execute_my_query("select GPD_ID from gft_product_delivery_hdr where GPD_ID='$reference_no'");
	if($row=mysqli_fetch_array($result)){
		$product_delivery_id= (int)$row['GPD_ID'];
	}
	return $product_delivery_id;
}
/**
 * @param string $opcode
 * @param string $pcode
 * @param string $pskew
 * 
 * @return string
 */
function get_product_delivery_details_query($opcode,$pcode,$pskew){
	$query=" select GCD_ORDER_NO,GPM_PRODUCT_CODE,GPM_PRODUCT_SKEW, group_concat(imt.GIMC_COMPLAINT_ID) training_ids,GLH_VERTICAL_CODE,pm.GPM_PRODUCT_TYPE, GFT_SKEW_PROPERTY " .
			" from gft_coupon_distribution_dtl join gft_lead_hdr lh on (lh.GLH_LEAD_CODE=GCD_TO_ID) " .
			" join gft_product_master pm on (gpm_product_code=$pcode and GPM_PRODUCT_SKEW='$pskew') ".
			" left join gft_cust_imp_ms_current_status_dtl imt on (GIMC_OPCODE=GCD_ORDER_NO) ".
			" left join gft_customer_support_hdr csh on (csh.gch_complaint_id=imt.GIMC_COMPLAINT_ID ) " .
			" where GCD_ORDER_NO='$opcode' group by GCD_ORDER_NO " ;
	return $query;
}
/**
 * @param string $lead_code
 * @param string $opcode
 * @param string $pcode
 * @param string $pskew
 * @param string $skew_property
 * @param string $vertical_id
 * 
 * @return string
 */
function get_pd_training_details_query($lead_code,$opcode,$pcode,$pskew,$skew_property,$vertical_id){
	$order_no	=	substr($opcode,0,15);
	$order_type	=	get_single_value_from_single_table("GOD_PD_EXPENSE_TYPE", "gft_order_hdr", "GOD_ORDER_NO", "$order_no");
	$task_table	=	"gft_impl_template_task_dtl";
	if($order_type=='5'){
		$task_table	=	"gft_pcs_impl_template_task_dtl";
	}
	$query_nmap_check="select GIT_MS_ID, GIM_MS_NAME, sum(GITD_DURATION_MINS) durations, GIM_MS_TYPE,GITD_PRODUCT_CODE_SKEW " .
			" from $task_table " .
			" join gft_impl_task_master on (GITD_TASK_ID=GIT_TASK_ID and GIS_STATUS='A')" .
			" join gft_impl_mailstone_master on (GIM_MS_ID=GIT_MS_ID and GIM_MS_STATUS='A') " .
			" where GITD_PRODUCT_CODE_SKEW='".$pcode.'-'.$pskew."' " .
			((in_array($skew_property,array(1,2,7,23,11,8)))  ?" and GITD_VERTICAL_ID in ($vertical_id) " :" ").
			" group by GIT_MS_ID order by GIT_MS_ID ";
	$exes = execute_my_query($query_nmap_check);
	if(mysqli_num_rows($exes)!=0){
		$vertical_code = $vertical_id;
	}else{
		$vertical_code = "0";
	}
	$query_insert = "select GOP_QTY, GIT_MS_ID, GIM_MS_NAME, sum( GITD_DURATION_MINS) durations, GIM_MS_TYPE,".
			" GITD_PRODUCT_CODE_SKEW, opcodes,GCD_COUPON_NO,GCD_COUPON_HOURS,GCD_IS_ECOUPON from( ".
			"select GOP_QTY,GIT_MS_ID, GIM_MS_NAME, GITD_DURATION_MINS, GIM_MS_TYPE,GITD_PRODUCT_CODE_SKEW," .
			"concat(GOD_ORDER_NO,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GOP_FULLFILLMENT_NO) as opcodes,gah_audit_id, god_created_date,".
			" GROUP_CONCAT(GCD_COUPON_NO) GCD_COUPON_NO,GCD_COUPON_HOURS,GCD_IS_ECOUPON  " .
			"from (SELECT god_created_date,ohdr.GOD_ORDER_NO,if(ohdr.god_order_splict=1, cpod.GCO_CUST_CODE, ohdr.GOD_LEAD_CODE) CUST_LEAD_CODE, " .
			"if(ohdr.god_order_splict=1, cpod.GCO_PRODUCT_CODE,opd.GOP_PRODUCT_CODE ) GOP_PRODUCT_CODE, " .
			"if(ohdr.god_order_splict=1, cpod.GCO_SKEW,opd.GOP_PRODUCT_SKEW ) GOP_PRODUCT_SKEW, " .
			"if(ohdr.god_order_splict=1, cpod.GCO_CUST_QTY,opd.GOP_QTY) GOP_QTY, " .
			"if(ohdr.god_order_splict=1, cpod.GCO_USEDQTY, opd.gop_usedqty) gop_usedqty, " .
			"if(ohdr.god_order_splict=1, cpod.GCO_FULLFILLMENT_NO, opd.GOP_FULLFILLMENT_NO) GOP_FULLFILLMENT_NO " .
			"FROM gft_order_hdr ohdr join gft_order_product_dtl opd on(opd.GOP_ORDER_NO=ohdr.GOD_ORDER_NO) " .
			"left join gft_cp_order_dtl cpod on (ohdr.GOD_LEAD_CODE =cpod.GCO_CP_LEAD_CODE AND cpod.GCO_ORDER_NO=opd.GOP_ORDER_NO " .
			"AND cpod.GCO_PRODUCT_CODE=opd.GOP_PRODUCT_CODE AND cpod.GCO_SKEW=opd.GOP_PRODUCT_SKEW AND ohdr.god_order_splict=1) " .
			"where GOD_IMPL_REQUIRED='Yes' and GOD_ORDER_STATUS='A') as t " .
			"join gft_product_master pm on ( gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew) " .
			"left join $task_table on (gitd_product_code_skew = concat(t.gop_product_code,'-',t.gop_product_skew)) " .
			"join gft_impl_task_master on (GITD_TASK_ID=GIT_TASK_ID and GIS_STATUS='A') " .
			"join gft_impl_mailstone_master on (GIM_MS_ID=GIT_MS_ID and GIM_MS_STATUS='A') " .
			"join gft_audit_hdr on (GAH_LEAD_CODE=CUST_LEAD_CODE and GAH_LAST_AUDIT='Y' and gah_handover_status!='Y' " .
			"and GAH_OPCODE=concat(GOD_ORDER_NO,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GOP_FULLFILLMENT_NO)) " .
			" join gft_coupon_distribution_dtl cd on(cd.GCD_REF_ORDER_NO=GAH_OPCODE) ".
			"where CUST_LEAD_CODE=$lead_code and GFT_SKEW_PROPERTY in (1,2,11,23,7,8) and GITD_VERTICAL_ID in (".$vertical_code.") " .
			"and gah_reffernce_order_no = '$opcode' group by GITD_TASK_ID,opcodes order by GITD_TASK_ID".
			" ) t2 group by opcodes,GIT_MS_ID,gah_audit_id ORDER BY god_created_date,GIT_MS_ID asc";
	return $query_insert;
}
/**
 * @param string $uid
 * @param string $condition_query
 *
 * @return string
 */
function get_product_delivery_summary_query_golive($uid,$condition_query=''){
	$query	=	"SELECT GOD_ORDER_NO,GAH_ORDER_AUDIT_STATUS,GAH_PENDING_IMP,approval_status,least_ms_status,max_ms_status, ".
			" GAH_CM_APPROVAL_STATUS,id,cust_name,cust_location,cust_id,cust_emotion,GLH_DATE, ".
			" lead_status_text,prospect_status_text,lead_status,prospect_status,created_by,followed_by, ".
			" GEM_EMP_NAME,GLE_CUST_LATITUDE,GLE_CUST_LONGITUDE,pcode,pskew,GPT_TYPE_ID,pd_status,god_order_date".
			" FROM (select GOD_ORDER_NO,'' AS GAH_ORDER_AUDIT_STATUS, GAH_PENDING_IMP AS GAH_PENDING_IMP, group_concat(distinct GIMC_APPROVAL separator ', ') approval_status, ".
			" min(imt.GIMC_STATUS) least_ms_status, max(imt.GIMC_STATUS) max_ms_status, '' AS GAH_CM_APPROVAL_STATUS,GPD_ID as id, ".
			" if(GPD_ORDER_TYPE=2,lh.GLH_CUST_NAME,lh1.GLH_CUST_NAME) as cust_name, ".
			" if(GPD_ORDER_TYPE=2,lh.GLH_CUST_STREETADDR2,lh1.GLH_CUST_STREETADDR2) as cust_location, ". 
			" if(GPD_ORDER_TYPE=2,lh.GLH_LEAD_CODE,lh1.GLH_LEAD_CODE) as cust_id, ".
			" if(GPD_ORDER_TYPE=2,lhe.GLE_CUST_EMOTION,lhe1.GLE_CUST_EMOTION) AS cust_emotion, ".
			" if(GPD_ORDER_TYPE=2,lh.GLH_DATE,lh1.GLH_DATE) GLH_DATE, ".
			" GCS_NAME AS lead_status_text,GPS_STATUS_NAME as prospect_status_text, ".
			" if(GPD_ORDER_TYPE=2,lh.GLH_STATUS,lh1.GLH_STATUS) as lead_status,  ".
			" if(GPD_ORDER_TYPE=2,lh.GLH_PROSPECTS_STATUS,lh1.GLH_PROSPECTS_STATUS) as prospect_status, ".
			" em1.gem_emp_name as created_by,em1.gem_emp_name as followed_by,em.GEM_EMP_NAME, ".
			" if(GPD_ORDER_TYPE=2,lhe.GLE_CUST_LATITUDE,lhe1.GLE_CUST_LATITUDE) GLE_CUST_LATITUDE, ".
			" if(GPD_ORDER_TYPE=2,lhe.GLE_CUST_LONGITUDE,lhe1.GLE_CUST_LONGITUDE) GLE_CUST_LONGITUDE, ".
			" '' as pcode, '' pskew, '' as GPT_TYPE_ID,GPD_CURRENT_STATUS as pd_status,god_order_date from gft_product_delivery_hdr pd ".
			" INNER JOIN gft_lead_hdr lh1 ON(lh1.GLH_LEAD_CODE=pd.GPD_LEAD_CODE)   ".
            " LEFT JOIN gft_lead_hdr_ext lhe1 ON(lh1.GLH_LEAD_CODE=lhe1.GLE_LEAD_CODE)   ".
            " INNER JOIN gft_emp_master em1 on(em1.GEM_EMP_ID=lh1.glh_lfd_emp_id)   ".
			" INNER JOIN gft_order_hdr oh ON(GPD_ORDER_NO=GOD_ORDER_NO)   ".
            " INNER JOIN gft_emp_master em on(em.GEM_EMP_ID=if(GOD_EMP_ID=9999,lh1.glh_lfd_emp_id,GOD_EMP_ID))   ".
            " LEFT JOIN gft_install_dtl_new ins ON(GPD_LEAD_CODE=GID_LEAD_CODE)  ".
            " LEFT JOIN gft_outlet_lead_code_mapping om ON(GOL_INSTALL_ID=GID_INSTALL_ID AND (GPD_ORDER_NO=GOL_ORDER_NO OR ISNULL(GOL_ORDER_NO)))   ".		
            " LEFT JOIN gft_lead_hdr lh ON(GOL_CUST_ID=lh.GLH_LEAD_CODE)       ".                    
			" LEFT JOIN gft_lead_hdr_ext lhe ON(lh.GLH_LEAD_CODE=lhe.GLE_LEAD_CODE)   ".
			" left join gft_customer_status_master cs on (GCS_CODE=lh.GLH_STATUS)   ".
			" left join gft_prospects_status_master gps on (lh.GLH_STATUS=3 and GPS_STATUS_ID=lh.GLH_PROSPECTS_STATUS and GPS_STATUS='A' )   ".
			" left join gft_customer_lifecycle_master cl1 on(cl1.GCL_ID=ifnull(GPS_CUST_LIFECYCLE,GCS_CUST_LIFECYCLE))   ".
			" left join gft_cust_imp_ms_current_status_dtl imt ON(pd.GPD_ID=imt.GIMC_PD_REF_ID)   ".
			" left join gft_audit_hdr ah ON(GAH_LEAD_CODE=pd.GPD_LEAD_CODE and GAH_REFFERNCE_ORDER_NO=GPD_ID and GAH_LAST_AUDIT='Y') ".
			" where GOD_IMPL_REQUIRED='Yes' AND GOD_ORDER_STATUS='A' AND lh1.GLH_FIELD_INCHARGE=$uid ".
			" AND ((GOL_DELIVERY_GOLIVE_STATUS!=3 AND GPD_ORDER_TYPE=2) || (GPD_ORDER_TYPE=3))  GROUP BY GPD_ID,lh.GLH_LEAD_CODE)new_pd where (1) $condition_query ";
	return $query;
}
/**
 * @param string $uid
 * @param string $condition_query
 *
 * @return string
 */
function get_product_delivery_summary_query_new($uid,$condition_query=''){
	$query	=	"SELECT GOD_ORDER_NO,GAH_ORDER_AUDIT_STATUS,GAH_PENDING_IMP,approval_status,least_ms_status,max_ms_status, ".
				" GAH_CM_APPROVAL_STATUS,id,cust_name,cust_location,cust_id,cust_emotion,GLH_DATE, ".
				" lead_status_text,prospect_status_text,lead_status,prospect_status,created_by,followed_by, ".
				" GEM_EMP_NAME,GLE_CUST_LATITUDE,GLE_CUST_LONGITUDE,pcode,pskew,GPT_TYPE_ID,pd_status,god_order_date,'' as sign_off_status,'' as exp_dt,'' age_start_date " .
				" FROM (select GOD_ORDER_NO,'' AS GAH_ORDER_AUDIT_STATUS, GAH_PENDING_IMP AS GAH_PENDING_IMP, group_concat(distinct GIMC_APPROVAL separator ', ') approval_status, ".
				" min(imt.GIMC_STATUS) least_ms_status, max(imt.GIMC_STATUS) max_ms_status, '' AS GAH_CM_APPROVAL_STATUS,GPD_ID as id, ".
				" GLH_CUST_NAME as cust_name,GLH_CUST_STREETADDR2 as cust_location, GLH_LEAD_CODE as cust_id,GLE_CUST_EMOTION AS cust_emotion,GLH_DATE, ".
				" GCS_NAME AS lead_status_text,GPS_STATUS_NAME as prospect_status_text,GLH_STATUS as lead_status, GLH_PROSPECTS_STATUS as prospect_status, ".
				" em1.gem_emp_name as created_by,em1.gem_emp_name as followed_by,em.GEM_EMP_NAME,GLE_CUST_LATITUDE,GLE_CUST_LONGITUDE, ".
				" '' as pcode, '' pskew, '' as GPT_TYPE_ID,GPD_CURRENT_STATUS as pd_status,god_order_date,GLH_LEAD_CODE, GLH_CUST_NAME from gft_product_delivery_hdr pd ".
				" INNER JOIN gft_lead_hdr lh ON(lh.GLH_LEAD_CODE=pd.GPD_LEAD_CODE) ".
				" INNER JOIN gft_order_hdr oh ON(GPD_ORDER_NO=GOD_ORDER_NO) ".
				" INNER JOIN gft_lead_hdr_ext lhe ON(lh.GLH_LEAD_CODE=lhe.GLE_LEAD_CODE) ".
				" INNER JOIN gft_emp_master em on(em.GEM_EMP_ID=if(GOD_EMP_ID=9999,lh.glh_lfd_emp_id,GOD_EMP_ID)) ".
				" INNER JOIN gft_emp_master em1 on(em1.GEM_EMP_ID=glh_lfd_emp_id) ".
				" left join gft_customer_status_master cs on (GCS_CODE=GLH_STATUS) ".
				" left join gft_prospects_status_master gps on (GLH_STATUS=3 and GPS_STATUS_ID=GLH_PROSPECTS_STATUS and GPS_STATUS='A' ) ".
				" left join gft_customer_lifecycle_master cl1 on(cl1.GCL_ID=ifnull(GPS_CUST_LIFECYCLE,GCS_CUST_LIFECYCLE)) ".
				" left join gft_cust_imp_ms_current_status_dtl imt ON(pd.GPD_ID=imt.GIMC_PD_REF_ID) ".
				" left join gft_audit_hdr ah ON(GAH_LEAD_CODE=pd.GPD_LEAD_CODE and GAH_REFFERNCE_ORDER_NO=GPD_ID and GAH_LAST_AUDIT='Y')".
				" where GOD_IMPL_REQUIRED='Yes' AND GOD_ORDER_STATUS='A' AND GLH_FIELD_INCHARGE=$uid GROUP BY GPD_ID)new_pd where (1) $condition_query ";
	return $query;
}
/**
 * @param string $uid
 * @param string $condition_query
 * 
 * @return string
 */
function get_product_delivery_summary_query($uid,$condition_query=''){
	$query	=	" select GOD_ORDER_NO,GAH_ORDER_AUDIT_STATUS,GAH_PENDING_IMP,approval_status,least_ms_status,max_ms_status,GAH_CM_APPROVAL_STATUS".
				" ,gah_reffernce_order_no as id,GLH_CUST_NAME as cust_name,GLH_CUST_STREETADDR2 as cust_location,".
				" GLH_LEAD_CODE as cust_id,GLE_CUST_EMOTION AS cust_emotion,GLH_DATE, GCS_NAME AS lead_status_text,".
				" GPS_STATUS_NAME as prospect_status_text,GLH_STATUS as lead_status, GLH_PROSPECTS_STATUS as prospect_status,".
				" em1.gem_emp_name as created_by,em1.gem_emp_name as followed_by,em.GEM_EMP_NAME,GLE_CUST_LATITUDE,".
				" GLE_CUST_LONGITUDE,pfm.gpm_product_code as pcode,opd.gop_product_skew as pskew,GPT_TYPE_ID,'' as pd_status,god_order_date,sign_off_status,exp_dt,gcd_age_start_date from (".
			" select GTS_STATUS_NAME,glh_lfd_emp_id, GLH_L1_INCHARGE,glh_lead_type,GLH_LEAD_CODE,GLH_FIELD_INCHARGE,GLH_CUST_NAME,GLH_CUST_STREETADDR2,".
			" GLH_DATE,GLH_STATUS,GLH_PROSPECTS_STATUS,GIMC_SESSION_1_CDATE,group_concat(distinct gcd_order_no ,'-',concat(gop_qty) separator ',') as group_order,".
			" GOD_ORDER_NO, GOD_LEAD_CODE, GOP_ORDER_NO, if(GOD_EMP_ID=9999,lh.glh_lfd_emp_id,GOD_EMP_ID) GOD_EMP_ID , ".
			" GOD_ORDER_DATE,  GOD_ORDER_AMT,    CUST_LEAD_CODE, GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW,GLH_CREATED_BY_EMPID,  ".
			" if(ORDER_BY=9999,lh.glh_lfd_emp_id,ORDER_BY) ORDER_BY,  gop_usedqty, GOP_FULLFILLMENT_NO, GOP_ADD_CLIENTS, ".
			" GOP_ADD_COMPANYS,splited, order_lead_code, GOP_VALIDITY_DATE,GOD_REMARKS,	min(imt.GIMC_STATUS) least_ms_status,max(imt.GIMC_STATUS) max_ms_status,".
			" group_concat(distinct GIMC_APPROVAL  separator ', ') approval_status, pm.gpm_product_type,GFT_SKEW_PROPERTY,GCD_ORDER_NO,group_concat(distinct gcd_signed_off) sign_off_status,gcd_expiry_date exp_dt,gcd_age_start_date from (".
			" SELECT ohdr.GOD_ORDER_NO, ohdr.GOD_LEAD_CODE, opd.GOP_ORDER_NO, ohdr.GOD_INCHARGE_EMP_ID AS GOD_EMP_ID,ohdr.GOD_EMP_ID AS ORDER_BY,".
			" ohdr.GOD_ORDER_DATE, ohdr.GOD_ORDER_AMT, ohdr.god_order_splict,  if(ci.cgi_incharge_emp_id!='',ci.cgi_incharge_emp_id,'') as business_incharge, (gop_sell_amt/gop_qty) as gop_sell_amt, ".
			" if(ohdr.god_order_splict=1, cpod.GCO_CUST_CODE, ohdr.GOD_LEAD_CODE) CUST_LEAD_CODE, ".
			" if(ohdr.god_order_splict=1, cpod.GCO_PRODUCT_CODE,opd.GOP_PRODUCT_CODE ) GOP_PRODUCT_CODE, ".
			" if(ohdr.god_order_splict=1, cpod.GCO_SKEW,opd.GOP_PRODUCT_SKEW ) GOP_PRODUCT_SKEW, ".
			" if(ohdr.god_order_splict=1, cpod.GCO_CUST_QTY,opd.GOP_QTY) GOP_QTY, ".
			" if(ohdr.god_order_splict=1, cpod.GCO_USEDQTY, opd.gop_usedqty) gop_usedqty, ".
			" if(ohdr.god_order_splict=1, cpod.GCO_FULLFILLMENT_NO, opd.GOP_FULLFILLMENT_NO) GOP_FULLFILLMENT_NO, ".
			" if(ohdr.god_order_splict=1, cpod.GCO_ADD_CLIENTS, opd.GOP_ADD_CLIENTS) GOP_ADD_CLIENTS, ".
			" if(ohdr.god_order_splict=1, cpod.GCO_ADD_COMPANYS, opd.GOP_ADD_COMPANYS) GOP_ADD_COMPANYS, ".
			" 'Y' as 'splited',GOD_LEAD_CODE as order_lead_code,  GOP_VALIDITY_DATE,GOD_REMARKS ".
			" FROM gft_order_hdr ohdr ".
			" left join gft_cp_info ci on(god_lead_code=ci.CGI_LEAD_CODE) ".
			" join gft_order_product_dtl opd on(opd.GOP_ORDER_NO=ohdr.GOD_ORDER_NO) ".
			" left join gft_cp_order_dtl cpod on (ohdr.GOD_LEAD_CODE =cpod.GCO_CP_LEAD_CODE AND cpod.GCO_ORDER_NO=opd.GOP_ORDER_NO ".
			" AND cpod.GCO_PRODUCT_CODE=opd.GOP_PRODUCT_CODE AND cpod.GCO_SKEW=opd.GOP_PRODUCT_SKEW AND ohdr.god_order_splict=1) ".
			" where GOD_IMPL_REQUIRED='Yes' and GOD_ORDER_STATUS='A' ) t ".
			" inner join gft_lead_hdr lh on(CUST_LEAD_CODE=GLH_LEAD_CODE) ".
			" join gft_product_master pm on (pm.gpm_product_code=t.gop_product_code and pm.gpm_product_skew=t.gop_product_skew and (GFT_SKEW_PROPERTY in (1,7,2,23,11,18)  or (GFT_SKEW_PROPERTY=8 or pm.gpm_product_skew like '%D' ) )) ".
			" join gft_coupon_distribution_dtl on (GCD_ORDER_NO=concat(GOP_ORDER_NO,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GOP_FULLFILLMENT_NO)) ".
			" left join gft_cust_imp_ms_current_status_dtl imt on (GIMC_OPCODE=concat(GOP_ORDER_NO,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GOP_FULLFILLMENT_NO)) ".
			" left join gft_customer_support_hdr csh on (csh.gch_complaint_id=imt.GIMC_COMPLAINT_ID ) ".
			" left join gft_impl_mailstone_master msm on (GIM_MS_ID=GIMC_MS_ID) ".
			" left join gft_impl_mailstone_type_master on (GIT_TYPE=GIM_MS_TYPE) ".
			" left join gft_ms_task_status mst on ( mst.GTS_STATUS_CODE= imt.GIMC_STATUS) where (1) AND glh_field_incharge=$uid group by CUST_LEAD_CODE, GOP_ORDER_NO,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW, GOP_FULLFILLMENT_NO ".
			" ) opd  left join gft_product_type_master prm on (GPT_TYPE_ID=opd.gpm_product_type)  ".
			" left join gft_skew_property_master on (GSPM_CODE=opd.GFT_SKEW_PROPERTY) ".
			" left join gft_product_family_master pfm on(opd.gop_product_code=pfm.gpm_product_code) ".
			" left join gft_product_master pm on(opd.gop_product_code=pm.gpm_product_code and opd.gop_product_skew=pm.gpm_product_skew) ".
			" join gft_product_group_master pg on(gpg_product_family_code=pfm.GPM_HEAD_FAMILY and substr(opd.gop_product_skew,1,4) =gpg_skew) ".
			" join gft_product_family_master pfm2 on(pfm.GPM_HEAD_FAMILY=pfm2.GPM_PRODUCT_CODE) ".
		    " join gft_audit_hdr on (GAH_LEAD_CODE=glh_lead_code and GAH_LAST_AUDIT='Y' and GAH_OPCODE=GCD_ORDER_NO  and GAH_OPCODE=gah_reffernce_order_no) ".
			" INNER JOIN gft_emp_master em on(em.GEM_EMP_ID=ORDER_BY) ".
			" INNER JOIN gft_emp_master em1 on(em1.GEM_EMP_ID=glh_lfd_emp_id) ".
			" join gft_emp_master em2 on(em2.gem_emp_id=GLH_CREATED_BY_EMPID) ".
			" INNER JOIN gft_lead_hdr_ext le on(le.GLE_LEAD_CODE=GLH_LEAD_CODE) ".
			" left join gft_customer_status_master cs on (GCS_CODE=GLH_STATUS) " .
			" left join gft_prospects_status_master gps on (GLH_STATUS=3 and GPS_STATUS_ID=GLH_PROSPECTS_STATUS and GPS_STATUS='A' ) ".
			" left join gft_customer_lifecycle_master cl1 on(cl1.GCL_ID=ifnull(GPS_CUST_LIFECYCLE,GCS_CUST_LIFECYCLE))".
			" left join gft_install_dtl_new itl on (itl.gid_lead_code=glh_lead_code and ( ".
			" (opd.GFT_SKEW_PROPERTY in (1,11,24,18) and opd.GOD_ORDER_NO=itl.gid_order_no and opd.GOP_PRODUCT_CODE=itl.GID_PRODUCT_CODE and opd.GOP_PRODUCT_SKEW=itl.GID_PRODUCT_SKEW and opd.GOP_FULLFILLMENT_NO=itl.GID_FULLFILLMENT_NO) ".
			" or (opd.GFT_SKEW_PROPERTY=11 and opd.GOP_PRODUCT_SKEW like'%CL%')) and itl.gid_status in('A','S'))  ".
			" where 1  AND IF(itl.gid_status!='',itl.gid_status in('A','S'),'1')  and glh_lead_type!='8' ".
			" and ( god_emp_id=9999 and GOD_ORDER_AMT=0 )!=true and ( GAH_HANDOVER_STATUS!='Y' or GAH_HANDOVER_STATUS is null) $condition_query ".
			" group by glh_lead_code,god_order_no,gop_product_code,gpg_skew,opd.gop_fullfillment_no ";
	return $query;
}
/**
* @param string $order_no
*
 * @return boolean
*/
function check_order_to_skip_bqaudit($order_no){
	$is_only_custom_license=true;
	$sql_check_only_custom	=	execute_my_query("SELECT GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW FROM gft_order_product_dtl WHERE GOP_ORDER_NO='$order_no' ");
	while($row=mysqli_fetch_array($sql_check_only_custom)){
		$sql_check_custom	=	execute_my_query("SELECT GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GPM_PRODUCT_TYPE,GFT_SKEW_PROPERTY,GPM_CATEGORY FROM gft_order_product_dtl" .
				" INNER JOIN gft_product_master AS gpm ON(GOP_PRODUCT_CODE=GPM_PRODUCT_CODE AND GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW)" .
				" left join gft_product_family_master pf on(pf.gpm_product_code=gpm.GPM_PRODUCT_CODE)".
				" WHERE GOP_ORDER_NO='$order_no' AND GOP_PRODUCT_CODE=".$row['GOP_PRODUCT_CODE']." AND".
				" GOP_PRODUCT_SKEW='".$row['GOP_PRODUCT_SKEW']."' AND GPM_TRAINING_REQUIRED='Y'");
		if(mysqli_num_rows($sql_check_custom)==0){
			continue;
		}
		$row1	=	mysqli_fetch_array($sql_check_custom);
		if($row1['GPM_PRODUCT_TYPE']!='8' && $row1['GFT_SKEW_PROPERTY']!='7' && $row1['GFT_SKEW_PROPERTY']!='12'  && $row1['GFT_SKEW_PROPERTY']!='8' && $row1['GFT_SKEW_PROPERTY']!='4'  && $row1['GFT_SKEW_PROPERTY']!='23' && $row1['GPM_CATEGORY']!=6 ){
			$is_only_custom_license	=	false;
		}
	}
	return $is_only_custom_license;
}
/**
 * @param string $lead_code
 * @param string $uid
 * @param string[string] $audit_hdr
 * @param string[int] $qid
 * @param string[string] $qidans
 * @param string $order_audit
 * @param string $order_no
 * @param string $refcodes
 * @param string $pskew
 *
 * @return void
 */
function update_order_acceptance_dtl($lead_code,$uid,$audit_hdr,$qid,$qidans,$order_audit,$order_no,$refcodes,$pskew){	
	$is_only_custom_license	=	check_order_to_skip_bqaudit($order_no);
	if($order_audit=='Y' && $is_only_custom_license){
		$audit_hdr['GAH_PENDING_IMP'] = 'Yes';
	}
	$audit_id=/*. (string) .*/update_audit_details($audit_hdr,$qid,$qidans);
	$Sql_Audit_CC = "SELECT gah_opcode FROM gft_audit_hdr " .
			"where gah_lead_code = $lead_code and gah_reffernce_order_no = '$refcodes' " .
			"and gah_reffernce_order_no!=gah_opcode and gah_last_audit='Y' group by gah_opcode";
	$exe = 	execute_my_query($Sql_Audit_CC);
	while($results = mysqli_fetch_array($exe)){
		$audit_hdr['GAH_OPCODE'] = $results['gah_opcode'];
		$audit_id=(string)update_audit_details($audit_hdr,$qid,$qidans);
	}
	$order_no1=substr($refcodes, 0,15);
	if($order_no!=""){
		$order_no1=$order_no;
	}
	if($order_audit=='N'){
		impl_send_sms_mail_hold_info($lead_code,$uid,$audit_id,null,$order_no1);
		update_pd_escalation_in_lead_hrd((int)$lead_code, "Order not Accepted", "Order not Accepted");		
	}else if($order_audit=='Y'){
		impl_send_sms_mail_process_info($lead_code,$uid,$audit_id,null,$order_no1,null,$pskew);
		update_pd_escalation_in_lead_hrd((int)$lead_code, "","Pending BQ & hardware Approval");
	}else if($order_audit=='No'){
		impl_send_sms_mail_hold_info($lead_code,$uid,$audit_id,null,$order_no1);
		update_pd_escalation_in_lead_hrd((int)$lead_code, " BQ & Hardware HOLD"," BQ & Hardware HOLD");
	}else if($order_audit=='Yes'){
		impl_send_sms_mail_process_info($lead_code,$uid,$audit_id,null,$order_no1);
		update_pd_escalation_in_lead_hrd((int)$lead_code, "","I Asure pending");
	}
}
/**
 * @param string $lead_code
 * @param string $reference_no
 * @param string $pcode
 * @param string $pskew
 * @param string $return_val
 *
 * @return mixed[]
 */
function get_iassure_training_details($lead_code,$reference_no,$pcode,$pskew,$return_val='DSL'){
	$sl	=	1;
	$return_array	=	array();
	$definitions_arr	=	array();
	$pd_audit_local		=	array();
	$product_code_skew	=	array();
	$all_opcodes		=	array();
	$all_ms_ids			=	array();
	$all_ms_names			=	array();
	$vertical	=	$edition="";
	$query	=	get_product_delivery_details_query("$reference_no",$pcode,$pskew);
	$result_training_check=execute_my_query($query);
	if( ($data_training_check=mysqli_fetch_array($result_training_check)) and mysqli_num_rows($result_training_check)==1){
		$vertical	=	$data_training_check['GLH_VERTICAL_CODE'];
		$edition	=	$data_training_check['GPM_PRODUCT_TYPE'];
		if($data_training_check['training_ids']==''){
			$min_collection_date	=	date("Y-m-d");
			$max_collection_date	=	date('Y-m-d',strtotime("30 days",strtotime(date('Y-m-d'))));
			$collection_date_validator=	array(array("min"=>$min_collection_date,"max"=>$max_collection_date));
			$mail_stone_status_list=get_two_dimensinal_result_set_from_query("select GTS_STATUS_CODE,GTS_STATUS_NAME from gft_ms_task_status where GTS_STATUS='A' and GTS_STATUS_CODE in(2) ");
			$all_status_list	=	array();
			for($i=0;$i<count($mail_stone_status_list);$i++){
				$single_val['id']=$mail_stone_status_list[$i][0];
				$single_val['name']=$mail_stone_status_list[$i][1];
				$all_status_list[]=$single_val;
			}
			$pd_audit_local['schedule_status_list']	=	$all_status_list;
			$query_insert	=	get_pd_training_details_query($lead_code,$reference_no,$data_training_check['GPM_PRODUCT_CODE'],$data_training_check['GPM_PRODUCT_SKEW'],$data_training_check['GFT_SKEW_PROPERTY'],$data_training_check['GLH_VERTICAL_CODE']);
			$result_insert=execute_my_query($query_insert);
			$all_training	=	array();
			$opcodes	=	"";
			$coupon_count	=	0;
			$total_rec	=	0;
			$opcode_arr=$ms_ids=$ms_name=$durations=$pcode_skew=array();
			while($data_insert=mysqli_fetch_array($result_insert)){
				$coupon_list=	$data_insert['GCD_COUPON_NO'];
				$t_gop_qty=(int)$data_insert['GOP_QTY'];
				if($opcodes!=$data_insert['opcodes']){
					$coupon_count	=	(count(explode(',', $coupon_list)));
					$opcodes=$data_insert['opcodes'];
				}
				if($coupon_count>0){
					for($l=0;$l<$t_gop_qty;$l++){
						if($coupon_count>0){
							$opcode_arr[$total_rec]	=	$data_insert['opcodes'];
							$ms_ids[$total_rec]		=	$data_insert['GIT_MS_ID'];
							$ms_name[$total_rec]	=	$data_insert['GIM_MS_NAME'];
							$durations[$total_rec]	=	(((int)$data_insert['durations'])/60)." hrs";
							if($data_insert['GCD_IS_ECOUPON']=='1' and (int)$data_insert['GCD_COUPON_HOURS']>0){
								$durations[$total_rec]	=	(int)$data_insert['GCD_COUPON_HOURS']." hrs";
							}
							$pcode_skew[$total_rec]	=	$data_insert['GITD_PRODUCT_CODE_SKEW'];
							$all_training[($sl-1)]	=	"schedule_status-$sl";
							$sl++;
							$total_rec++;
							$coupon_count--;
						}
					}
				}else if($opcodes==$data_insert['opcodes'] && $total_rec>0){
					$ms_ids[$total_rec-1]		=	isset($ms_ids[$total_rec-1])?($ms_ids[$total_rec-1].",".$data_insert['GIT_MS_ID']):($data_insert['GIT_MS_ID']);
				}
			}
			$sl=1;
			foreach ($opcode_arr as $key=>$values){
				$tag_name	=	"$sl. ".$ms_name[$key];
				$training_duration=$durations[$key];
				$submition_tag	=	"trainings";
				$on_conjunction	=	array();
				for($in=0;$in<count($all_training);$in++){
					if($all_training[$in]!="schedule_status-$sl"){
						$conjunction["key"]	=	$all_training[$in];
						$conjunction["value"]=	array("-1");
						$on_conjunction[]		=$conjunction;
					}
				}
				$training_status_transition	= array(array("type"=>"state","state"=> "on","onValue"=>array("-1"), "target"=>array("customer_comments","my_comments"), "or_conjunction"=>$on_conjunction));
				$definitions_arr[]	=	meta_detail_array("training_duration-$sl","Training Duration","text","","","on","$tag_name",null,true,"","","$training_duration",null,null,$submition_tag);
				$definitions_arr[]	=	meta_detail_array("schedule_date-$sl","Schedule Date","datetime","","","on","$tag_name",$collection_date_validator,false,null,null,"",null,null,$submition_tag);
				//$definitions_arr[]	=	meta_detail_array("schedule_status-$sl","Schedule Status","local","schedule_status_list","single","on","$tag_name",null,false,null,null,"2",$training_status_transition,null,$submition_tag);
				$product_code_skew[]=	$pcode_skew[$key];
				$all_opcodes[]		=	$opcode_arr[$key];
				$all_ms_ids[]		=	$ms_ids[$key];
				$all_ms_names[]		=	$tag_name;
				$sl++;
			}
		}
	}
	if($return_val=='DSL'){
		$return_array['local']=	$pd_audit_local;
		$return_array['definitions']=	$definitions_arr;
	}else{
		$return_array['product_code_skew']=	$product_code_skew;
		$return_array['all_opcodes']=	$all_opcodes;
		$return_array['all_ms_ids']=$all_ms_ids;
		$return_array['all_ms_names']=$all_ms_names;
		$return_array['vertical']=$vertical;
		$return_array['edition']=$edition;
	}
	return $return_array;
}
function get_base_product_for_creating_training($order_no){
	$product_dtl	=	array();
	$sql_query	=	" SELECT GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW FROM gft_order_product_dtl ".
					" INNER JOIN gft_product_master pm ON(PM.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE AND GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
					" INNER JOIN gft_product_family_master pfm ON(pfm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
					" WHERE GOP_ORDER_NO='$order_no' ORDER BY GPM_PRODUCT_PRIME_TYPE, GPM_IS_BASE_PRODUCT LIMIT 1";
	$result_rows	=	execute_my_query($sql_query);
	if($row=mysqli_fetch_array($result_rows)){
		$product_dtl[0]	=	$row['GOP_PRODUCT_CODE'];
		$product_dtl[1]	=	$row['GOP_PRODUCT_SKEW'];
	}
	return $product_dtl;
}
/**
 * @param string $order_no
 * @param int $product_delivery_id
 *
 * @return void
 */
function update_pd_incentive($order_no, $product_delivery_id=0){
    $order_date = get_single_value_from_single_table("GOD_ORDER_DATE", "gft_order_hdr", "GOD_ORDER_NO", "$order_no");
    $new_inc_effec  = get_samee_const("New_Incentive_Order_Date");
    $new_incentive  = (strtotime($order_date) >= strtotime($new_inc_effec)) ? true : false;
    if((!$new_incentive)){
        return;
    }
    $result_order = execute_my_query("select GOI_OWNER_EMP from gft_orderwise_incentive_owner where GOI_ORDER_NO='$order_no' AND GOI_ATTRIBUTE_ID IN(4,5,18)");
    $order_condition = " GIMC_REFOPCODE LIKE '$order_no%' ";
    if($product_delivery_id>0){
        $order_condition = " GIMC_PD_REF_ID=$product_delivery_id ";
    }
    $query_to_check_rating =    " select (SUM(GPT_CUST_RATING)/COUNT(GPT_CUST_RATING)) AVG_RATING, '1' group_val from gft_cust_imp_ms_current_status_dtl ".
        " INNER JOIN gft_pd_training_feedback_dtl tf ON(GIMC_COMPLAINT_ID=GPT_TRAINING_ID AND GPT_IS_SPOC=1 AND GPT_ACK_STATUS=1) ".
        " where $order_condition  group by group_val";
    $avg_rating = 0;
    $result_check_rating = execute_my_query($query_to_check_rating);
    if($row_rating=mysqli_fetch_assoc($result_check_rating)){
        $avg_rating = $row_rating['AVG_RATING'];
    }
    if(( (mysqli_num_rows($result_order)>0) && ($row_order = mysqli_fetch_assoc($result_order) ) )  && $avg_rating>=4){
        $business_manager = get_single_value_from_single_query("cgi_incharge_emp_id","select cgi_incharge_emp_id  from gft_order_hdr join gft_cp_info on (god_lead_code=CGI_LEAD_CODE) where god_order_no='$order_no' ");
        $order_owner = "";
        $only_partner = false;
        if($business_manager!=''){
            $order_owner = $business_manager;
            $only_partner = true;
        }else{
            $order_owner = (int)$row_order['GOI_OWNER_EMP'];
            $delivery_type = (int)get_single_value_from_single_query("GLE_DELIVERY_TYPE", "SELECT GLE_DELIVERY_TYPE FROM gft_order_hdr INNER JOIN gft_lead_hdr_ext ON(GLE_LEAD_CODE=GOD_LEAD_CODE) where GOD_ORDER_NO='$order_no'");
            if($delivery_type==1){
                $order_owner  =  (int)get_single_value_from_single_table("GOD_DELIVERY_INCHARGE", "gft_order_hdr", "GOD_ORDER_NO", "$order_no");
            }
        }
        $attr_perc_arr = get_attribute_percent_in_array($order_no);
        insert_orderwise_incentive_owner($order_no, 1, $attr_perc_arr, 13, $order_owner,'',$only_partner);
        update_incentive_earnings_for_order($order_no);
    }
}
/**
 * @param int $product_delivery_id
 * 
 * @return string[string]
 */
function get_product_delivery_training_dtl($product_delivery_id){
	$pd_hours_dtl	=	array();
	$result_rows	=	execute_my_query("select GPD_BQ_MINS,GPD_MDM_MINS,GPD_UAT_MINS,GPD_GO_LIVE_MINS,GPD_TOTAL_CUSTOMER_VISIT,".
								" GPD_COMPLETED_CUSTOMER_VISIT,GPD_ORDER_TYPE,GPD_MIGRATION_REQUIRED,GPD_ORDER_NO,".
								" GPD_CURRENT_STATUS,GPD_IS_SPLIT_ORDER,GPD_TOTAL_TRAINING_MINS,".
	                            " GPD_TOTAL_SERVICE_MINS, GPD_TOTAL_LICENSE_MINS from gft_product_delivery_hdr where GPD_ID='$product_delivery_id'");
	if($row=mysqli_fetch_array($result_rows)){
		$pd_hours_dtl['bq'] 	= $row['GPD_BQ_MINS'];
		$pd_hours_dtl['mdm'] 	= $row['GPD_MDM_MINS'];
		$pd_hours_dtl['uat'] 	= $row['GPD_UAT_MINS'];
		$pd_hours_dtl['golive'] = $row['GPD_GO_LIVE_MINS'];
		$pd_hours_dtl['totalcustomervisit'] = $row['GPD_TOTAL_CUSTOMER_VISIT'];
		$pd_hours_dtl['totalcompletedvisit'] = $row['GPD_COMPLETED_CUSTOMER_VISIT'];
		$pd_hours_dtl['ordertype'] = $row['GPD_ORDER_TYPE'];
		$pd_hours_dtl['migrationrequired'] = $row['GPD_MIGRATION_REQUIRED'];
		$pd_hours_dtl['orderno'] = $row['GPD_ORDER_NO'];
		$pd_hours_dtl['current_pd_status'] = $row['GPD_CURRENT_STATUS'];
		$pd_hours_dtl['is_split'] = $row['GPD_IS_SPLIT_ORDER'];
		$pd_hours_dtl['total_mins'] = $row['GPD_TOTAL_TRAINING_MINS'];
		$pd_hours_dtl['service_mins'] = $row['GPD_TOTAL_SERVICE_MINS'];
		$pd_hours_dtl['license_mins'] = $row['GPD_TOTAL_LICENSE_MINS'];
	}
	return $pd_hours_dtl;
}
/**
 * @param string $uid
 * @param string $lead_code
 * @param string $order_no
 * @param string $training_msg
 * @param string $spoc
 * @param string[int] $trainingid_arr
 * 
 * @return void
 */
function send_iassure_notification_to_customer($uid,$lead_code,$order_no,$training_msg,$spoc,$trainingid_arr){
	$help_us_help_you	=	get_samee_const("Help_Us_Help_You");
	$sys_requirement	=	get_system_requirement_link($order_no);
	$coupon_expiry_info	=	get_coupon_expiry_information($order_no,$lead_code);
	$result_contact=execute_my_query("select GCC_CONTACT_NAME,GCC_CONTACT_NO from gft_customer_contact_dtl where gcc_id='$spoc'");
	$SPOC_PERSON = $SPOC_NO = "";
	if($row_contact=mysqli_fetch_array($result_contact)){
		$SPOC_PERSON=$row_contact['GCC_CONTACT_NAME'];
		$SPOC_NO=$row_contact['GCC_CONTACT_NO'];
	}
	$cust_dtl=customerContactDetail_Mail($lead_code,$order_no);
	$noti_content_config['Customer_Name']	=	array($cust_dtl['cust_name']);
	$noti_content_config['Mail_Content']	=	array($training_msg);
	$noti_content_config['SPOC_NO']	=	array($SPOC_NO);
	$noti_content_config['SPOC_PERSON']	=	array($SPOC_PERSON);
	send_formatted_notification_content($noti_content_config,55,9,2,$lead_code);
	
	$sales_emp_name	='9999';
	if(is_authorized_group_list($cust_dtl['Order_By_Whom'], array (5))){$sales_emp_name=$cust_dtl['LFD_EMP_ID']; }else{$sales_emp_name= $cust_dtl['PDCM_EMP_ID']; }
	$user_master=get_emp_master($uid,'',null,false,false);
	$jemp_master=get_emp_master($cust_dtl['Field_incharge'],'',null,false,false);
	if($SPOC_NO==''){$SPOC_NO="&nbsp;";}
	if($SPOC_PERSON==''){$SPOC_PERSON="&nbsp;";}
	$pc_profile_link	=	get_pc_profile_link($uid);
	if($pc_profile_link!=''){
	    $pc_profile_link="<a href='".$pc_profile_link."'>Read more about me</a>";
	}else{
	    $pc_profile_link = "&nbsp;";
	}	
	$db_content_config=array('user_name'=>array($user_master[0][1]),
			'Customer_Name'=>array($cust_dtl['cust_name']),
			'Customer_Id'=>array($lead_code),
			'SPOC_NO'=>array($SPOC_NO),
			'SPOC_PERSON'=>array($SPOC_PERSON),
			'Customer_Mobile'=>array($cust_dtl['mobile_no']),
			'Customer_Mail'=>array($cust_dtl['EMAIL']),
			'PC_Mobile'=>array($user_master[0][3]),
			'PC_Name'=>array($user_master[0][1]),
			'Employee_Role'=>array($jemp_master[0][7]!=''?$jemp_master[0][7]:'&nbsp;'),
			'training_status'=>array('Planed'),
			'Employee_Name'=>array($jemp_master[0][1]),
			'order_audit_status'=>array(''),
			'Help_Us_Help_You'=>array($help_us_help_you),
			'System_Requirement'=>array($sys_requirement),
			'Coupon_Details'=>array($coupon_expiry_info),
			'implementation_status'=>array(''),
	        'Profile_Link'=>array($pc_profile_link)
	);
	$other_cc	=	/*. (mixed[]) .*/array();
	$other_cc[]	=	$sales_emp_name;
	$other_cc[]	=	$cust_dtl['Field_incharge'];
	$result=execute_my_query("select group_concat(concat('<tr><td>',GIM_MS_NAME,'</td><td>',DATE_SUB(GCH_RESTORE_TIME,INTERVAL 1 DAY),'</td><td>',GTS_STATUS_NAME,'</td></tr>'))training_assurance " .
			" from gft_customer_support_hdr csh " .
			" join gft_cust_imp_ms_current_status_dtl imt on (csh.gch_complaint_id=imt.GIMC_COMPLAINT_ID) " .
			" join  gft_impl_mailstone_master on (GIM_MS_ID=GIMC_MS_ID) " .
			" join gft_ms_task_status on (GIMC_STATUS=GTS_STATUS_CODE )" .
			" WHERE GCH_LEAD_CODE=$lead_code and csh.gch_complaint_id in (".implode(',' ,$trainingid_arr ).") group by GCH_LEAD_CODE ");
	if($data=mysqli_fetch_array($result)){
		$db_content_config['training_assurance']=array('<table><tr><td>Milestone</td><td>Planned Date & Time</td><td>Current Status</td></tr>'.$data['training_assurance'].'</table>');
	}
	send_formatted_mail_content($db_content_config,55,80,array(0=>$uid),array(0=>(int)$lead_code),null, $other_cc); // customer email
}
/**
 * @param int $product_delivery_id
 * 
 * @return string[string]
 */
function get_product_delivery_ms_ids($product_delivery_id){
	$milestone_arr = /*. (string[string]) .*/array();
	$result	=	execute_my_query("select GIMC_COMPLAINT_ID, GIMC_MS_ID from gft_cust_imp_ms_current_status_dtl where GIMC_PD_REF_ID='$product_delivery_id'");
	while($row=mysqli_fetch_array($result)){
		$milestone_arr[$row['GIMC_MS_ID']]=$row['GIMC_COMPLAINT_ID'];
	}
	return $milestone_arr;
}
/**
 * @param int $product_delivery_id
 * @param string $lead_code
 * 
 * @return mixed[]
 */
function get_dynamically_added_question($product_delivery_id,$lead_code){
	$return_arr	=	array();	
	$result	=	execute_my_query("select GUB_ID,GUB_QUESTION,GUB_ANSWER from gft_user_bq_question_dtl where GUB_PD_ID='$product_delivery_id' and GUB_LEAD_CODE='$lead_code'");
	while($row=mysqli_fetch_array($result)){
		$question['id']=$row['GUB_ID'];
		$question['question']=$row['GUB_QUESTION'];
		$question['answer']=$row['GUB_ANSWER'];
		$return_arr[]=$question;
	}
	return $return_arr;
}
/**
 * @param string $product_delivery_id
 * 
 * @return string
 */
function get_pd_opcode($product_delivery_id){
	$order_opcode	=	"";
	$result=execute_my_query("select GPO_ORDER_NO from gft_pd_order_mapping where GPO_PD_ID='$product_delivery_id' limit 1");
	if($row=mysqli_fetch_array($result)){
		$order_opcode = $row['GPO_ORDER_NO'];
	}
	return $order_opcode;
}
/**
 * @param string $lead_code
 * @param int $training_id
 *
 * @return boolean
 */
function check_product_delivery_audit_approval_status($lead_code, $training_id){
	$sql_pd = 	" select GPT_ID from gft_pd_training_feedback_dtl where GPT_LEAD_CODE='$lead_code' ".
			" AND GPT_TRAINING_ID='$training_id' AND GPT_IS_SPOC=1 AND GPT_ACK_STATUS=0";
	$result = execute_my_query($sql_pd);
	if(mysqli_num_rows($result)>0){
		return false;
	}
	return true;
}
/**
 * @param int $training_id
 * 
 * @return int
 */
function get_product_delivery_ms_type($training_id){
	$ms_type =0;
	$result	=	execute_my_query("select GIMC_MS_ID from gft_cust_imp_ms_current_status_dtl where GIMC_COMPLAINT_ID='$training_id'");
	if(mysqli_num_rows($result)>0 && $row=mysqli_fetch_array($result)){
		$ms_type = (int)$row['GIMC_MS_ID'];
	}
	return $ms_type;
}
/**
 * @param int $product_delivery_id
 * @param int $status_no
 * @param int $time_spent
 * @param string $update_hrs_for
 * @param int $milestone_status
 * @param boolean $update_visit
 *
 * @return void
 */
function update_product_delivery_status($product_delivery_id, $status_no,$time_spent=0,$update_hrs_for="",$milestone_status=2,$update_visit=true){
	$pd_dtl	=	get_product_delivery_training_dtl($product_delivery_id);
	$pd_order_type 		= 	isset($pd_dtl['ordertype'])?(int)$pd_dtl['ordertype']:0;
	$update_query	=	"GPD_CURRENT_STATUS=$status_no, GPD_TOTAL_USED_MINS=(IFNULL(GPD_TOTAL_USED_MINS, 0)+$time_spent)";
	if($update_hrs_for=='bq'){
		$update_query .= ", GPD_USED_BQ_MINS=(IFNULL(GPD_USED_BQ_MINS, 0)+$time_spent)";
	}
	if($update_hrs_for=='mdm'){
		$update_query .= ", GPD_USED_MDM_MINS=(IFNULL(GPD_USED_MDM_MINS, 0)+$time_spent)";
	}
	if($update_hrs_for=='uat'){//Traning activity for standalon product delivery
		$update_query .= ", GPD_USED_UAT_MINS=(IFNULL(GPD_USED_UAT_MINS, 0)+$time_spent)";
		if($milestone_status!='-1' && $pd_order_type!=3){
			$update_query .= ", GPD_COMPLETED_CUSTOMER_VISIT=(IFNULL(GPD_COMPLETED_CUSTOMER_VISIT, 0)+1)";
		}
	}
	if($update_hrs_for=='golive'){
		$update_query .= ", GPD_USED_GO_LIVE_MINS=(IFNULL(GPD_USED_GO_LIVE_MINS, 0)+$time_spent)".
							(($pd_order_type==3 && ($update_visit))?" , GPD_COMPLETED_CUSTOMER_VISIT=(IFNULL(GPD_COMPLETED_CUSTOMER_VISIT, 0)+1)":" ");
	}	
	execute_my_query("UPDATE gft_product_delivery_hdr SET $update_query WHERE GPD_ID=$product_delivery_id");
}
/**
 * @param string $lead_code
 * @param string $training_id
 * @param string $spoc
 * @param string $trainee_name
 * @param string $mark_ts
 * @param string $uid
 * @param string $time_spent
 * 
 * @return void
 */
function update_spoc_for_pd_training($lead_code,$training_id,$spoc,$trainee_name,$mark_ts,$uid='0',$time_spent='0'){
	$spoc_query	=	"('$lead_code','$training_id','$spoc','1','0',now(),'$uid','$time_spent')";
	if($trainee_name!=""){
		$all_trainees	=	explode(',', $trainee_name);
		for($inc=0;$inc<count($all_trainees);$inc++){
			if(trim($all_trainees[$inc])!=""){
				$spoc1 = $all_trainees[$inc];
				if($spoc1!=$spoc){
					$spoc_query	.=	",('$lead_code','$training_id','$spoc1','2','0',now(),'$uid','$time_spent')";
				}				
			}
		}
	}
	if($spoc_query!=""){
		$sql_insert	=	" INSERT INTO gft_pd_training_feedback_dtl(GPT_LEAD_CODE,GPT_TRAINING_ID,GPT_CONTACT_ID,GPT_IS_SPOC,GPT_ACK_STATUS,GPT_ACTIVITY_ON,GPT_ACTIVITY_BY,GPT_ACTIVITY_TIME_SPENT) VALUES $spoc_query";
		execute_my_query($sql_insert);
	}
	if($spoc!='' && $mark_ts=='1'){
		udpate_ts_contact_group($lead_code,$spoc);
	}
}
/**
 * @param string $lead_code
 *
 * @return boolean
 */
function check_bq_completeness($lead_code){
	$sql_query = 	" select GPD_ORDER_NO from gft_product_delivery_hdr ".
			" inner join gft_order_hdr oh ON(god_order_no=GPD_ORDER_NO and GOD_ORDER_STATUS='A') ".
			" inner join gft_pcs_audit_dtl on(GPD_ID=GPA_PD_ID) ".
			" where GPD_LEAD_CODE=$lead_code AND GPD_ORDER_TYPE=2 AND GPD_CURRENT_STATUS>11 LIMIT 1";
	$result = execute_my_query($sql_query);
	if(mysqli_num_rows($result)>0){
		return true;
	}
	return false;
}
/** 
 * @param string $uid
 * @param int $product_delivery_id
 * @param string $lead_code
 * @param string $GOD_ORDER_NO
 * @param string $milestone1_date
 * @param string $milestone2_date
 * @param string $milestone3_date
 * @param string $milestone4_date
 * @param string $spoc
 * @param string $mark_ts
 * @param int $create_golive_training
 * @param int $product_consultant
 * 
 * @return void
 */
function update_iassure_details_new($uid,$product_delivery_id,$lead_code,$GOD_ORDER_NO,$milestone1_date,$milestone2_date,$milestone3_date,$milestone4_date,$spoc,$mark_ts, $create_golive_training=0,$product_consultant=0){
	$support_status	=	'T6';
	$pcode	=	"10";
	$pskew	=	"01.0";
	$pd_status=13;
	$product_dtl	=	get_base_product_for_creating_training($GOD_ORDER_NO);
	if(isset($product_dtl[0]) && isset($product_dtl[1])){
		$pcode	=	$product_dtl[0];
		$pskew	=	$product_dtl[1];
	} 
	$training_hours_dtl = get_product_delivery_training_dtl($product_delivery_id);
	$bq_duration		= isset($training_hours_dtl['bq'])?$training_hours_dtl['bq']:'0';
	$mdm_duration		= isset($training_hours_dtl['mdm'])?$training_hours_dtl['mdm']:'0';
	$uat_duration		= isset($training_hours_dtl['uat'])?$training_hours_dtl['uat']:'0';
	$golive_duration	= isset($training_hours_dtl['golive'])?$training_hours_dtl['golive']:'0';	
	$pd_order_type 		= isset($training_hours_dtl['ordertype'])?(int)$training_hours_dtl['ordertype']:0;
	$GCH_COMPLAINT_ID2=$GCH_COMPLAINT_ID4=$GCH_COMPLAINT_ID1=0;
	$required_bq	=	check_bq_completeness($lead_code);
	if(!$required_bq && $create_golive_training==0){
		$GCH_COMPLAINT_ID1	=	insert_support_entry((int)$lead_code,(int)$pcode,substr($pskew,0,4),'','',$uid,'42',"Business Questionnaire & Hardware Assessment",1,$support_status,$milestone1_date,'0',$uid,null,'4',null);
		$last_activity_id	=	get_last_activity_id_of_support($GCH_COMPLAINT_ID1);
		execute_my_query(" insert into gft_cust_imp_ms_current_status_dtl (GIMC_COMPLAINT_ID,GIMC_MS_ID,GIMD_ACTUAL_DURATION_MINS,GIMC_WORKED_DURATION,GIMC_STATUS,GIMC_OPCODE,GIMC_REFOPCODE,GIMC_PD_REF_ID)" .
				" values ($GCH_COMPLAINT_ID1,35,'$bq_duration',0,2,'','','$product_delivery_id')");
		$trainingid_arr[]	=	$GCH_COMPLAINT_ID1;
		$pd_status=11;
	}	
	if($pd_order_type>1  && $create_golive_training==0){
		$GCH_COMPLAINT_ID2	=	insert_support_entry((int)$lead_code,(int)$pcode,substr($pskew,0,4),'','',$uid,'8',"Master Data Migration",1,$support_status,$milestone2_date,'0',$uid,null,'4',null);
		$last_activity_id	=	get_last_activity_id_of_support($GCH_COMPLAINT_ID2);
		execute_my_query(" insert into gft_cust_imp_ms_current_status_dtl (GIMC_COMPLAINT_ID,GIMC_MS_ID,GIMD_ACTUAL_DURATION_MINS,GIMC_WORKED_DURATION,GIMC_STATUS,GIMC_OPCODE,GIMC_REFOPCODE,GIMC_PD_REF_ID)" .
				" values ($GCH_COMPLAINT_ID2,36,'$mdm_duration',0,2,'','','$product_delivery_id')");
		$trainingid_arr[]	=	$GCH_COMPLAINT_ID2;
	}	
	if($create_golive_training==0){
		$GCH_COMPLAINT_ID3 	= 	insert_support_entry((int)$lead_code,(int)$pcode,substr($pskew,0,4),'','',$uid,'8',"UAT",1,$support_status,$milestone3_date,'0',$uid,null,'4',null);
		$last_activity_id 	= 	get_last_activity_id_of_support($GCH_COMPLAINT_ID3);
		execute_my_query(" insert into gft_cust_imp_ms_current_status_dtl (GIMC_COMPLAINT_ID,GIMC_MS_ID,GIMD_ACTUAL_DURATION_MINS,GIMC_WORKED_DURATION,GIMC_STATUS,GIMC_OPCODE,GIMC_REFOPCODE,GIMC_PD_REF_ID)" .
				" values ($GCH_COMPLAINT_ID3,37,'$uat_duration',0,2,'','','$product_delivery_id')");
		$trainingid_arr[]	=	$GCH_COMPLAINT_ID3;
	}	
	if($pd_order_type>1){
		$GCH_COMPLAINT_ID4	=	insert_support_entry((int)$lead_code,(int)$pcode,substr($pskew,0,4),'','',$uid,'8',"Go Live",1,$support_status,$milestone4_date,'0',$uid,null,'4',null);
		$last_activity_id	=	get_last_activity_id_of_support($GCH_COMPLAINT_ID4);
		execute_my_query(" insert into gft_cust_imp_ms_current_status_dtl (GIMC_COMPLAINT_ID,GIMC_MS_ID,GIMD_ACTUAL_DURATION_MINS,GIMC_WORKED_DURATION,GIMC_STATUS,GIMC_OPCODE,GIMC_REFOPCODE,GIMC_PD_REF_ID)" .
				" values ($GCH_COMPLAINT_ID4,3,'$golive_duration',0,2,'','','$product_delivery_id')");
		$trainingid_arr[]	=	$GCH_COMPLAINT_ID4;
	}	
	$pc_emp_id = $uid;
	$spoc_query = "";
	if($spoc!='' && $create_golive_training==0){
		$spoc_query	=	(!$required_bq)?"('$lead_code','$GCH_COMPLAINT_ID1','$spoc','3','0',now(),'$uid')":"".
		$spoc_query .=	($pd_order_type>1)?",('$lead_code','$GCH_COMPLAINT_ID2','$spoc','3','0',now(),'$uid')":"";
		$spoc_query .=	($spoc_query!=""?",":"")."('$lead_code','$GCH_COMPLAINT_ID3','$spoc','3','0',now(),'$uid')";
		$spoc_query .=	($pd_order_type>1)?",('$lead_code','$GCH_COMPLAINT_ID4','$spoc','3','0',now(),'$uid')":"";
	}else if($create_golive_training==1){
		$spoc_query .=	($pd_order_type>1)?"('$lead_code','$GCH_COMPLAINT_ID4','$spoc','3','0',now(),'$uid')":"";
		$pc_emp_id = $product_consultant;
	}
	$training_msg	=	"Business Questionnaire & Hardware Assessment scheduled on ".get_date_in_indian_format($milestone1_date);
	$training_msg	.=	($pd_order_type>1)?"<br>Master Data Migration scheduled on ".get_date_in_indian_format($milestone2_date):"";
	$training_msg	.=	"<br>Training scheduled on ".get_date_in_indian_format($milestone3_date);
	$training_msg	.=	($pd_order_type>1)?"<br>Go Live scheduled on ".get_date_in_indian_format($milestone4_date):"";	
	send_iassure_notification_to_customer($pc_emp_id,$lead_code,$GOD_ORDER_NO,$training_msg,$spoc,$trainingid_arr);	
	if($spoc_query!=""){
		$sql_insert	=	" INSERT INTO gft_pd_training_feedback_dtl(GPT_LEAD_CODE,GPT_TRAINING_ID,GPT_CONTACT_ID,GPT_IS_SPOC,GPT_ACK_STATUS,GPT_ACTIVITY_ON,GPT_ACTIVITY_BY) VALUES $spoc_query";
		execute_my_query($sql_insert);
	}
	if($spoc!='' && $mark_ts=='1'){
		udpate_ts_contact_group($lead_code,$spoc);
	}
	$audit_hdr=array('GAH_LEAD_CODE'=>$lead_code,
			'GAH_DATE_TIME'=>date('Y-m-d H:i:s'),
			'GAH_OPCODE'=>$product_delivery_id,
			'GAH_REFFERNCE_ORDER_NO'=>$product_delivery_id,
			'GAH_AUDIT_BY'=>$uid,
			'GAH_MY_COMMENTS'=>"MileStone Status is  Scheduled",
			'GAH_CUSTOMER_COMMENTS'=>"",
			'GAH_I_ASSURE_STATUS'=>'Y',
			'GAH_TRAINING_STATUS'=>0,
			'GAH_ORDER_NO'=>"$GOD_ORDER_NO",
			'GAH_AUDIT_TYPE'=>24);
	update_audit_details($audit_hdr,array(),array());
	if($create_golive_training==0)
		update_product_delivery_status($product_delivery_id,$pd_status);
}
/**
 *
 * @param string $uid
 * @param string $lead_code
 * @param string $order_no
 * @param string $opcodes
 * @param string $pcode
 * @param string $pskew
 * @param string[int] $ms_status
 * @param string[int] $ms_date
 * @param string $my_comments
 * @param string $customer_comments
 * @param string $spoc
 * @param string $mark_ts
 *
 * @return void
 */
function update_iassure_details($uid,$lead_code,$order_no,$opcodes,$pcode,$pskew,$ms_status,$ms_date,$my_comments,$customer_comments,$spoc,$mark_ts){
	$order_type	=	get_single_value_from_single_table("GOD_PD_EXPENSE_TYPE", "gft_order_hdr", "GOD_ORDER_NO", "$order_no");
	$task_table	=	"gft_impl_template_task_dtl";
	if($order_type=='5'){
		$task_table	=	"gft_pcs_impl_template_task_dtl";
	}	
	$status_id	=	0;
	$status_name=	"";
	$trainingid_arr	=	/*. (string[]) .*/array();
	$status_name = 'Scheduled';
	update_pd_escalation_in_lead_hrd((int)$lead_code, "","Scheduled Training");
	$iassure_dtl	=	get_iassure_training_details($lead_code,$opcodes,$pcode,$pskew,"");
	$ms_id_arr		=	/*. (string[int]) .*/$iassure_dtl['all_ms_ids'];
	$all_ms_names	=	/*. (string[int]) .*/$iassure_dtl['all_ms_names'];
	$gopcodes		=	/*. (string[int]) .*/$iassure_dtl['all_opcodes'];
	$gproduct		=	/*. (string[int]) .*/$iassure_dtl['product_code_skew'];
	$vertical		=	$iassure_dtl['vertical'];
	$edition		=	$iassure_dtl['edition'];
	$training_msg	=	"";
	$spoc_query		=	"";
	$SPOC_PERSON="";
	$SPOC_NO="";
	foreach($ms_id_arr as $i=>$ms_id){
		$audit_hdr=array('GAH_LEAD_CODE'=>$lead_code,
				'GAH_DATE_TIME'=>date('Y-m-d H:i:s'),
				'GAH_OPCODE'=>$gopcodes[$i],
				'GAH_AUDIT_BY'=>$uid,
				'GAH_MY_COMMENTS'=>"MileStone Status is  Scheduled",
				'GAH_CUSTOMER_COMMENTS'=>"",
				'GAH_I_ASSURE_STATUS'=>'Y',
				'GAH_TRAINING_STATUS'=>$status_id,
				'GAH_AUDIT_TYPE'=>24);
		update_audit_details($audit_hdr,array(),array()); // insert i assure status in audit header table		
		$query1=" select GIT_MS_ID, sum(GITD_DURATION_MINS) durations, GIM_MS_TYPE,GIM_MS_NAME,GIT_COMPLAIENT_TYPE, GIT_COUPON_REQUIRED " .
				" from $task_table " .
				" join gft_impl_task_master on (GITD_TASK_ID=GIT_TASK_ID and GIS_STATUS='A')" .
				" join gft_impl_mailstone_master on (GIM_MS_ID=GIT_MS_ID and GIM_MS_STATUS='A') " .
				" join gft_impl_mailstone_type_master on (GIT_TYPE=GIM_MS_TYPE and GIT_TYPE_STATUS='A')" .
				" where 1 ". ($pcode=='500' ?" and ( GITD_VERTICAL_ID=0 or GITD_VERTICAL_ID=$vertical) " :" and GITD_VERTICAL_ID=0 ").
				" and GITD_PRODUCT_CODE_SKEW='$gproduct[$i]' and GIT_MS_ID in ($ms_id) group by GIT_MS_ID limit 1";
		$result_insert=execute_my_query($query1);
		if($data_insert=mysqli_fetch_array($result_insert)){
			$ms_id_new	=	$data_insert['GIT_MS_ID'];
			$support_status='T6';
			$GCH_COMPLAINT_ID=insert_support_entry((int)$lead_code,(int)$pcode,substr($pskew,0,4),'',(string)$edition,$uid,'16',$data_insert['GIM_MS_NAME'],$data_insert['GIT_COMPLAIENT_TYPE'],$support_status,
					$ms_date[$i],'0',$uid,null,'S4',null);
			$last_activity_id=get_last_activity_id_of_support($GCH_COMPLAINT_ID);
			execute_my_query(" insert into gft_cust_imp_ms_current_status_dtl (GIMC_COMPLAINT_ID,GIMC_MS_ID,GIMD_ACTUAL_DURATION_MINS,GIMC_WORKED_DURATION,GIMC_STATUS,GIMC_OPCODE,GIMC_REFOPCODE)" .
					" values ($GCH_COMPLAINT_ID,$ms_id_new,{$data_insert['durations']},0,2,'$opcodes','$gopcodes[$i]')");
			execute_my_query("insert into gft_cust_imp_training_ms_log (GCM_ACTIVITY_ID, GCM_MS_ID, GCM_DATE, GCM_DURATION, GCM_STATUS) " .
					"value ($last_activity_id,$ms_id_new,'".$ms_date[$i]."',0,2) ");
			execute_my_query("insert into gft_cust_imp_task_current_status_dtl " .
					" (GITC_COMPLAINT_ID,GITC_MS_ID,GITC_TASK_ID,GITC_DATE,GITC_ACTUAL_DURATION_MINS,GITC_WORKED_DURATION,GITC_STATUS)" .
					" (select $GCH_COMPLAINT_ID,$ms_id_new,GITD_TASK_ID, '".$ms_date[$i]."', GITD_DURATION_MINS,0,2".
					" from $task_table" .
					" join gft_impl_task_master on (GITD_TASK_ID=GIT_TASK_ID) " .
					" where 1 " .($pcode=='500' ?" and ( GITD_VERTICAL_ID=0 or GITD_VERTICAL_ID=$vertical) " :" and GITD_VERTICAL_ID=0 ").
					" and GITD_PRODUCT_CODE_SKEW='$gproduct[$i]' ".
					" and GIT_MS_ID in($ms_id) )");
			execute_my_query("insert into gft_cust_imp_training_task_log (GCT_ACTIVITY_ID, GCT_MS_ID, GCT_TASK_ID, GCT_DATE, GCT_DURATION, GCT_STATUS,GCT_REMARKS)  " .
					"(select $last_activity_id,GITC_MS_ID,GITC_TASK_ID,GITC_DATE,0,GITC_STATUS,'' from gft_cust_imp_task_current_status_dtl where GITC_COMPLAINT_ID =$GCH_COMPLAINT_ID and GITC_MS_ID=$ms_id_new )");
			$trainingid_arr[]=$GCH_COMPLAINT_ID;
			$training_msg	=	$training_msg."<br>".$all_ms_names[$i]." scheduled on ".get_date_in_indian_format($ms_date[$i]);	
			if($spoc!=''){
				$spoc_query	=	($spoc_query!=''?"$spoc_query,":"")."('$lead_code','$GCH_COMPLAINT_ID','$spoc','3','0',now(),'$uid')";				
			}
		}
	}
	if($spoc_query!=""){
		$sql_insert	=	" INSERT INTO gft_pd_training_feedback_dtl(GPT_LEAD_CODE,GPT_TRAINING_ID,GPT_CONTACT_ID,GPT_IS_SPOC,GPT_ACK_STATUS,GPT_ACTIVITY_ON,GPT_ACTIVITY_BY) VALUES $spoc_query";
		execute_my_query($sql_insert);
	}
	if($spoc!='' && $mark_ts=='1'){
		udpate_ts_contact_group($lead_code,$spoc);
	}	
	if($training_msg!=''){		
		send_iassure_notification_to_customer($uid,$lead_code,$order_no,$training_msg,$spoc,$trainingid_arr);
	}	
}
/**
 * @param string $uid
 * @param string $lead_code
 * @param string $order_no
 * @param string $opcode
 * @param string $assign_product_consultant
 * @param string $purpose
 *
 * @return void
 */
function update_field_incharge($uid,$lead_code,$order_no,$opcode,$assign_product_consultant,$purpose){
	$help_us_help_you	=	get_samee_const("Help_Us_Help_You");
	$user_id_name_arr=get_emp_master($uid,'',null,false,false);
	$user_id_name=$user_id_name_arr[0][1];
	$user_mobile=$user_id_name_arr[0][3];
	$user_role = $user_id_name_arr[0][7];
	$exit_field_incharge=$exit_field_incharge_name=$new_field_incharge_name='';
	$lead_type = 1; $corp_lead_code = "";
	$other_cc	=	array();
	$sys_requirement	=	get_system_requirement_link($order_no);
	$coupon_expiry_info	=	get_coupon_expiry_information($order_no,$lead_code);
	$result_exist=execute_my_query("select GLH_FIELD_INCHARGE,gem_emp_name,GLH_L1_INCHARGE,GLH_LEAD_TYPE,glh_reference_given from gft_lead_hdr,". 
	                           " gft_emp_master where glh_lead_code='$lead_code' " .
			                   "and gem_emp_id = GLH_FIELD_INCHARGE");
	if($data_exist=mysqli_fetch_array($result_exist)){
		$exit_field_incharge=$data_exist['GLH_FIELD_INCHARGE'];
		$exit_field_incharge_name=$data_exist['gem_emp_name'];
		$lead_type = $data_exist['GLH_LEAD_TYPE'];
		$corp_lead_code = $data_exist['glh_reference_given'];
	}
	execute_my_query("update gft_lead_hdr set GLH_FIELD_INCHARGE='$assign_product_consultant' where glh_lead_code='$lead_code' ");
    $lead_type = get_single_value_from_single_table("GLH_LEAD_TYPE", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
    if($lead_type=='13' && $corp_lead_code>0){
        execute_my_query("update gft_lead_hdr set GLH_FIELD_INCHARGE='$assign_product_consultant' where glh_lead_code='$corp_lead_code' ");
    }
	$result_exist=execute_my_query("select gem_emp_name from gft_lead_hdr,gft_emp_master where glh_lead_code='$lead_code' " .
			"and gem_emp_id = GLH_FIELD_INCHARGE");
	execute_my_query("update gft_coupon_distribution_dtl
	inner join gft_cust_imp_ms_current_status_dtl on(GIMC_OPCODE=GCD_ORDER_NO)
	inner join gft_customer_support_hdr on(GIMC_COMPLAINT_ID=GCH_COMPLAINT_ID)
	inner join gft_customer_support_dtl  on(GCH_COMPLAINT_ID=GCD_COMPLAINT_ID and GCH_LAST_ACTIVITY_ID=gcd_activity_id)
	set GCD_PROCESS_EMP='$assign_product_consultant'
	where GCD_ORDER_NO='$opcode' and GCH_LEAD_CODE='$lead_code' and gcd_status='T6'");
	if($data_exist=mysqli_fetch_array($result_exist)){
		$new_field_incharge_name=$data_exist['gem_emp_name'];
	}
	$audit_hdr=array('GAH_LEAD_CODE'=>$lead_code,
			'GAH_DATE_TIME'=>date('Y-m-d H:i:s'),
			'GAH_OPCODE'=>$opcode,
			'GAH_AUDIT_BY'=>$uid,
			'GAH_MY_COMMENTS'=>$exit_field_incharge_name.' Replaced to '.$new_field_incharge_name,
			'GAH_CUSTOMER_COMMENTS'=>'',
			'GAH_L1_INCHARGE'=>0,
			'GAH_FIELD_INCHARGE'=>$assign_product_consultant,
			'GAH_AUDIT_TYPE'=>'22');
	update_audit_details($audit_hdr,/*. (string[int]) .*/ array(),/*. (string[int]) .*/ array());
	$transfer_to_name_arr=get_emp_master($assign_product_consultant,'',null,false,false);
	$emp_mail_list	=	array($assign_product_consultant);
	$transfer_to_name=$transfer_to_name_arr[0][1];
	$transfer_to_mobile=$transfer_to_name_arr[0][3];
	if($purpose=='imp_training'){
		$cust_dtl=customerContactDetail($lead_code);
		$sales_arr=get_emp_master($cust_dtl['LFD_EMP_ID']);
		if (!isset($sales_arr[0][1])){
			$sales_arr[0][1]='';
			$sales_arr[0][3]='';
		}
		$sql_partner_bm_dtl=	" select od.GOD_ORDER_NO, od.GOD_EMP_ID, cp.CGI_EMP_ID,cp.cgi_incharge_emp_id  from gft_order_hdr od ".
				" inner join gft_cp_info as cp on(cp.CGI_EMP_ID=od.GOD_EMP_ID) ".
				" where god_order_no='$order_no'";
		$result_partner_bm_dtl=execute_my_query($sql_partner_bm_dtl);
		if(mysqli_num_rows($result_partner_bm_dtl)==1){
			$row_part_bm	=	mysqli_fetch_array($result_partner_bm_dtl);
			if($row_part_bm['GOD_EMP_ID']!=''){
				array_push($other_cc,$row_part_bm['GOD_EMP_ID']);
			}
			if($row_part_bm['cgi_incharge_emp_id']!=''){
				array_push($other_cc,$row_part_bm['cgi_incharge_emp_id']);
			}
		}
		$sales_incharge_name=$sales_arr[0][1];
		$sales_incharge_mobile=$sales_arr[0][3];
		$link="<a href=\"".get_samee_const("DOMAIN_NAME")."/impl_training_assurance.php?emp_code=$assign_product_consultant&incharge_type=Field Incharge\">click here for Details</a></br>";
		$kyc=return_latest_kyc_details($lead_code);
		$db_content_config=array(
				'Employee_Name'=>array($transfer_to_name),
				'PC_Mobile'=>array($transfer_to_mobile),
				'user_name'=>array($user_id_name),
		        'Mobile'=>array($user_mobile),
		        'Employee_Role'=>array($user_role),
				'Customer_Name'=>array($cust_dtl['cust_name']),
				'Customer_Id'=>array($lead_code),
				'Help_Us_Help_You'=>array($help_us_help_you),
				'sales_incharge_name'=>array("$sales_incharge_name [$sales_incharge_mobile]"),
				'assigned_for'=>array('Implementation & Training '. $link ),
				'KYC'=>array($kyc),
				'System_Requirement'=>array($sys_requirement),
				'Coupon_Details'=>array($coupon_expiry_info),
				'training_assurance'=>array("<a href=\"".get_samee_const("DOMAIN_NAME")."/supporthistory.php?from_dt=01-11-2004&to_dt=&custCode=$lead_code&history_type=9\">click here for Details</a></br>")
		);
		send_formatted_mail_content($db_content_config,55,148,$emp_mail_list,$customer_ids=array($lead_code),null,$other_cc,null,null,$uid);/* To Employee */
	}
}
/**
 * @param string $lead_code
 * @param string $check_first_only | to check whether only one PD handover is done for customer 
 * 
 * @return boolean
 */
function check_is_first_pd_handover($lead_code,$check_first_only=true){
	$rows=execute_my_query("select DISTINCT GAH_REFFERNCE_ORDER_NO from gft_audit_hdr  where GAH_TRAINING_STATUS=6 ".
							"and GAH_HANDOVER_STATUS='Y' and GAH_LEAD_CODE='$lead_code'");
	if(($check_first_only and mysqli_num_rows($rows)==1) or (!$check_first_only and mysqli_num_rows($rows)>0)){
		return true;
	}else{
		return false;
	}
}
/**
 * @param string $lead_code
 * 
 * @return int
 */
function get_product_delivery_status($lead_code){
	$response_val = 0;
	$rows=execute_my_query("select DISTINCT GAH_REFFERNCE_ORDER_NO from gft_audit_hdr  where GAH_TRAINING_STATUS=6 ".
			"and GAH_HANDOVER_STATUS='Y' and GAH_LEAD_CODE='$lead_code'");
	if(mysqli_num_rows($rows)>0){
		return 2;//Completed
	}else{
		$old_delivery = execute_my_query("select GCD_COUPON_NO from gft_coupon_distribution_dtl where GCD_TO_ID='$lead_code'");
		$new_delivery = execute_my_query("select GPD_ID from gft_product_delivery_hdr where GPD_LEAD_CODE='$lead_code'");
		$new_outlet_delivery = execute_my_query("select GPD_ID from gft_product_delivery_hdr ".
												" INNER JOIN gft_outlet_lead_code_mapping on(GOL_ORDER_NO=GPD_ORDER_NO)".
												" where GOL_CUST_ID='$lead_code'");
		if((mysqli_num_rows($old_delivery)>0) || (mysqli_num_rows($new_delivery)>0) || (mysqli_num_rows($new_outlet_delivery)>0)){
			return 1;//Pending
		}
	}
	return $response_val;
}
/**
 * @param string $lead_code
 * @return integer
 */
function check_for_cst_welcome_ticket($lead_code) {
	$chk_qry = " select gch_complaint_id from gft_customer_support_hdr where gch_complaint_code='168' and gch_lead_code='$lead_code' ";
	$chk_res = execute_my_query($chk_qry);
	return mysqli_num_rows($chk_res);
}
/**
 * @param string $lead_code
 * @param string $pcode
 * @param string $pskew
 * @param string $uid
 * @param string $assign_emp_id
 * @param boolean $is_partner_order
 * 
 * @return void
 */
function assign_welcome_call($lead_code,$pcode,$pskew,$uid,$assign_emp_id,$is_partner_order=false) {
    $welcome_ticket_count = check_for_cst_welcome_ticket($lead_code);
    if($welcome_ticket_count==0) {
        $curr_datetime = date("Y-m-d H:i:s");
        $schedule_time = date("Y-m-d H:i:s",strtotime("$curr_datetime +1 hour"));
        $schedule_dt = date('Y-m-d');
        if($is_partner_order){
            $curr_datetime = $schedule_time = date("Y-m-d H:i:s",strtotime("$curr_datetime +20 day"));
            $schedule_dt   = date("Y-m-d",strtotime("$schedule_dt +20 day"));
        }
        if( (!$is_partner_order && (int)date('H',strtotime($curr_datetime))>=18) or isWeekend($schedule_dt) or isSecondFourthFifthSaturday($schedule_dt) or isDateInHoliday($schedule_dt)) {
            $schedule_dt = add_date_without_holidays(1,false,$schedule_dt);
            $schedule_time = date("$schedule_dt H:i:s");
        }
        $updated_by = $uid==""?"9999":$uid;
        $emp_name = get_emp_name((int)$assign_emp_id);
        $complaint_id = insert_support_entry((int)$lead_code,(int)$pcode,"$pskew", '', '',$updated_by,"0","Welcome call ticket",
            '168',"T100",$schedule_time,date('Y-m-d H:i:s'),"$assign_emp_id",null,"4",
            "Product Consultant successfully completed the Product delivery for this Customer. Please speak with Customer for KYC.",
            true,'',"","","3",true);
        update_welcome_status($complaint_id);
        $domain_name = get_samee_const("DOMAIN_NAME");
        $db_content_config = array(
            "Agent_Name"=>array($emp_name),
            "Customer_Name"=>array(get_single_value_from_single_table("glh_cust_name", "gft_lead_hdr", "glh_lead_code", $lead_code)),
            "Customer_Id"=>array($lead_code),
            "remarks"=>array($domain_name),
            "reason"=>array(get_product_name_with_version($pcode,$pskew)),
            "status"=>array(get_single_value_from_single_table("GTM_NAME","gft_status_master","GTM_CODE","T100")),
            "comp_id"=>array($complaint_id)
        );
        $cust_dtl = /*.(string[string]).*/get_existing_customer_details($lead_code);
        $outstanding_amt = (isset($cust_dtl['outstanding_amt'])?(float)$cust_dtl['outstanding_amt']:0.0);
        $lead_owner_id = get_single_value_from_single_table("glh_lfd_emp_id", "gft_lead_hdr", "glh_lead_code", $lead_code);
        if($outstanding_amt<=0 and !is_authorized_group_list($lead_owner_id, explode(",", LEAD_OWNER_SKIP_GROUP))) {
            assign_lead_to_emp($lead_code, $assign_emp_id, "CST welcome call process");
        }
        send_formatted_mail_content($db_content_config,8,330,(int)$assign_emp_id);
    }
}
/**
 *
 * @param string $uid
 * @param string $lead_code
 * @param string $refcodes
 * @param string[string] $audit_hdr
 * @param string[int] $qid
 * @param string[string] $qidans
 * @param string $install_id
 * @param string $last_audit_date
 * @param boolean $is_only_custom_license
 * @param string $order_no
 *
 * @return void
 */
function move_pd_handover($uid,$lead_code,$refcodes,$audit_hdr,$qid,$qidans,$install_id,$last_audit_date,$is_only_custom_license=false,$order_no=''){
	$audit_hdr['GAH_AUDIT_TYPE']	=	"19";
	$audit_hdr['GAH_HANDOVER_STATUS']=	"Y";
	$audit_id=/*. (string) .*/update_audit_details($audit_hdr,$qid,$qidans);
	$Sql_Audit_CC = "SELECT gah_opcode FROM gft_audit_hdr " .
			"where gah_lead_code = $lead_code and gah_reffernce_order_no = '$refcodes' " .
			"and gah_reffernce_order_no!=gah_opcode and gah_last_audit='Y' group by gah_opcode";
	$exe = 	execute_my_query($Sql_Audit_CC);
	$audit_hdr['GAH_TRAINING_STATUS']='6';
	while($results = mysqli_fetch_array($exe)){
		$audit_hdr['GAH_OPCODE'] = $results['gah_opcode'];
		$audit_id=/*. (string) .*/update_audit_details($audit_hdr,$qid,$qidans);
	}
	execute_my_query("update gft_audit_hdr set GAH_TRAINING_STATUS=6 where GAH_LEAD_CODE=$lead_code and GAH_AUDIT_ID=$audit_id and GAH_LAST_AUDIT='Y' "); // audit header table update the status
	if($install_id!=''){
		execute_my_query("update gft_install_dtl_new set GID_TRAINING_STATUS=6 where GID_LEAD_CODE=$lead_code and GID_INSTALL_ID IN($install_id) ");
	}
	if(!$is_only_custom_license){
		impl_send_sms_mail_process_info($lead_code,$uid,$audit_id,null,$order_no);
	}
	update_call_preferance($lead_code); /* Move the Call Routing From PD Support Group to Techsupport */
	update_pd_age_in_summary((int)$uid, (int)$lead_code,$order_no);/*Update complete PD age in summary*/
	update_pd_handover_age_in_summary((int)$uid, (string)$last_audit_date);/*Update complete PD age in summary*/
	update_pd_escalation_in_lead_hrd((int)$lead_code, ""," HandOver to 24X7");
	update_implementation_current_status($lead_code, 9);
	$order_balance_amt = get_single_value_from_single_table("god_balance_amt", "gft_order_hdr", "god_order_no", substr($order_no,0,15));
	if(intval($order_balance_amt)<=0 and check_is_first_pd_handover($lead_code)){
		if($install_id!='') {
			$cust_dtls = execute_my_query(" select if(lh1.glh_lead_type=13,lh1.glh_reference_given,lh1.glh_lead_code) assign_lead, ".
					     " if(lh1.glh_lead_type=13,gpm2.gpm_map_id,gpm1.gpm_map_id) state_id,gpm1.gpm_map_id client_state_id,lh1.glh_lead_type lead_type, ".
						 " if(lh1.glh_lead_type=13,lh2.glh_main_product,lh1.glh_main_product) prod_gp,lh1.glh_country country from gft_lead_hdr lh1 ".
						 " left join gft_political_map_master gpm1 on (gpm1.gpm_map_name=lh1.glh_cust_statecode and gpm1.gpm_map_type='S') ".
						 " left join gft_lead_hdr lh2 on (lh2.glh_lead_code=lh1.glh_reference_given) ".
						 " left join gft_political_map_master gpm2 on (gpm2.gpm_map_name=lh2.glh_cust_statecode and gpm2.gpm_map_type='S') ".
						 " where lh1.glh_lead_code='$lead_code' ");
			$assign_lead_code = '';
			$assign_emp_id = '';
			$client_assign_emp = '';
			$country = '';
			if($cust_row = mysqli_fetch_array($cust_dtls)) {
				$assign_lead_code = $cust_row['assign_lead'];
				$state_id = $cust_row['state_id'];
				$client_state_id = $cust_row['client_state_id'];
				$assign_emp_id = get_cst_agent_for_customer($state_id,$cust_row['prod_gp']);
				$country = $cust_row['country'];
				if($cust_row['lead_type']=='13') {
					$client_assign_emp = get_cst_agent_for_customer($client_state_id,$cust_row['prod_gp']);
				}
			}
			$order_by_emp = get_order_by_emp($install_id,$order_no);
			$country = strtolower($country);
			if(($country=="india") || ($order_by_emp<7000 && $country!="india")){
			    $assign_qry = " select GID_LIC_PCODE pcode,substring(GID_LIC_PSKEW,1,4) as pskew from gft_install_dtl_new ".
			 			    " where gid_install_id='$install_id' ";
			    $pcode='';$pskew='';
			    $assign_res = execute_my_query($assign_qry);
			    if($assign_row = mysqli_fetch_array($assign_res)) {
			        $pcode = $assign_row['pcode'];
			        $pskew = $assign_row['pskew'];
			    }
			    assign_welcome_call($assign_lead_code,$pcode,$pskew,$uid,$assign_emp_id);
			    if($assign_lead_code!=$lead_code) {
			        assign_welcome_call($lead_code,$pcode,$pskew,$uid,$client_assign_emp);
			    }
			}
		}
		$pc_emp_id = get_single_value_from_single_table("GLH_FIELD_INCHARGE", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
		update_monitor_lead($lead_code,$pc_emp_id,1,2);
	}
}
/**
 * @param string $lead_code
 * @param string $order_no
 * 
 * @return string
 */
function get_pd_milestone_query($lead_code,$order_no){
	$where_qry	=	"";
	if($order_no!=""){
		$where_qry	=	" and GIMC_OPCODE like '$order_no%' ";
	}
	$query4=" select GIMC_COMPLAINT_ID,GIMC_REFOPCODE, GIMC_MS_ID, GIM_MS_NAME,GIMC_STATUS, concat(GCH_PRODUCT_CODE,'-',GCH_PRODUCT_SKEW) sproduct,GCH_PRODUCT_CODE, GIT_COUPON_REQUIRED, GCD_COUPON_NO,GCH_PRODUCT_TYPE, GCD_RECEIVED_BY, GIMC_OPCODE " .
				" from gft_customer_support_hdr h " .
				" join gft_cust_imp_ms_current_status_dtl on (GIMC_COMPLAINT_ID=GCH_COMPLAINT_ID)" .
				" join gft_impl_mailstone_master on (GIM_MS_ID=GIMC_MS_ID)" .
				" join gft_impl_mailstone_type_master on (GIT_TYPE=GIM_MS_TYPE)" .
				" left join gft_coupon_distribution_dtl on (GIMC_OPCODE=GCD_ORDER_NO and GCD_TRAINING_ID=GIMC_COMPLAINT_ID and GCD_TO_ID=GCH_LEAD_CODE and GCD_SPLITABLE='0' AND GCD_DISTRIBUTE_FOR='C') " .
				" where h.GCH_LEAD_CODE='$lead_code' $where_qry group by GIMC_COMPLAINT_ID ";
	return $query4;
}
/**
 * @param string $milestones
 * 
 * @return int
 */
function get_training_milestone_status($milestones){
	$return_status =2;
	$result = execute_my_query("select min(GIMC_STATUS) min_status from gft_cust_imp_ms_current_status_dtl where GIMC_COMPLAINT_ID in($milestones)");
	if($row=mysqli_fetch_array($result)){
		$return_status=$row['min_status'];
	}
	return $return_status;
}
/**
 * @param int $product_delivery_id
 * @param string $lead_code
 * @param boolean $visit_completed
 * 
 * @return string[int]
 */
function get_uat_checklist_for_signoff($product_delivery_id,$lead_code,$visit_completed){
	$return_response[0] =	"error";
	$milestone_ids		=	get_product_delivery_ms_ids($product_delivery_id);
	$complaint_id   	= 	isset($milestone_ids['36'])?(int)$milestone_ids['36']:0;
	$complaint_id1   	= 	isset($milestone_ids['37'])?(int)$milestone_ids['37']:0;
	$complaint_id2   	= 	isset($milestone_ids['3'])?(int)$milestone_ids['3']:0;
	$milestone_status	=	get_training_milestone_status("$complaint_id,$complaint_id1");
	if((!check_product_delivery_audit_approval_status($lead_code, $complaint_id) || 
	!check_product_delivery_audit_approval_status($lead_code, $complaint_id1) || 
	($complaint_id2>0 && (!check_product_delivery_audit_approval_status($lead_code, $complaint_id2)))) && $milestone_status!='-1'){
		$return_response[1] = 	"Previous Training session is not acknowledged by Customer. ".
								"You are not allowed to do activity entry for next training until ".
								"complete the acknowledge by customer for previous Training. ";
		return $return_response;		
	}
	if(($visit_completed) && $milestone_status!='-1'){
		$return_response[1] = 	"All training session are completed and handover to Assure Care.";
		return $return_response;
	}
	$sql_get_opcode = "select substring(SUBSTRING(GPO_ORDER_NO, 1, (LENGTH(GPO_ORDER_NO)-1)),16) as opcode  from gft_pd_order_mapping where GPO_PD_ID='$product_delivery_id'";
	$result_opcode = execute_my_query($sql_get_opcode);
	if(mysqli_num_rows($result_opcode)<1){
		$return_response[1] = "Required product mapping not available contact sam team.";
		return $return_response;	
	}
	while($row_opcode=mysqli_fetch_array($result_opcode)){
		$opcode 		=	$row_opcode['opcode'];
		$all_opcode[] 	= 	"'".substr($opcode, 0,3)."-".substr($opcode, 3)."'";
	}
	$return_response[0] =	"success";
	$return_response[1]	=	implode(',', $all_opcode);
	$return_response[2]	=	$complaint_id1;
	$return_response[3] =$complaint_id2;
	return $return_response;
}
/**
 * @param string $lead_code
 * @param string $order_no
 * 
 * @return mixed[]
 */
function get_pd_milestone_dtl($lead_code,$order_no){
	$return_arr	=	array();
	$query	=	get_pd_milestone_query($lead_code,$order_no);
	$result	=	execute_my_query($query);
	$complaint_id=	"";
	$complaint_ids=	"";
	$ms_ids		=	"";
	$ms_id		=	"";
	$ms_status	=	"";
	$complaint_name="";
	$training_no	= 0;
	$current_training=0;
	while($row_training_dtl=mysqli_fetch_array($result)){
		$training_no++;
		$complaint_ids=	$complaint_ids.(($complaint_ids!=''?",":"")).$row_training_dtl['GIMC_COMPLAINT_ID'];
		$ms_ids=	$ms_ids.(($ms_ids!=''?",":"")).$row_training_dtl['GIMC_MS_ID'];
		if($complaint_id=='' && $row_training_dtl['GIMC_STATUS']!='5'){
			$complaint_id=	$row_training_dtl['GIMC_COMPLAINT_ID'];
			$complaint_name=$row_training_dtl['GIM_MS_NAME'];
			$ms_id		=	$row_training_dtl['GIMC_MS_ID'];
			$ms_status	=	$row_training_dtl['GIMC_STATUS'];
			$current_training=$training_no;
		}
	}	
	$return_arr['complaint_ids']	=	$complaint_ids;
	$return_arr['ms_ids']			=	$ms_ids;
	$return_arr['complaint_id']		=	$complaint_id;
	$return_arr['complaint_name']	=	$complaint_name;
	$return_arr['ms_id']			=	$ms_id;
	$return_arr['ms_status']		=	$ms_status;
	$return_arr['current_training']	=	$current_training;
	$return_arr['total_training']	=	$training_no;
	return $return_arr;
}
/**
 * @param string $complaint_id
 * @param string $ms_id
 * @param string $ACT_ID
 * @param string $training_tasks
 * @param string $lead_code
 * @param string $duration
 * @param string $order_no
 * @param int $product_delivery_id
 *
 * @return void
 */
function update_coupon_age_milestone_status($complaint_id,$ms_id,$ACT_ID,$training_tasks,$lead_code,$duration,$order_no,$product_delivery_id){
	$total_duration=0;
	$ms_status=1;
	$first_activity=false;
	$pd_validity_date = add_date_without_holidays((int)get_samee_const("PD_COUPON_EXPIRY_DAYS"));
	$coupon_age_date	=	date('Y-m-d H:i:s');
	$GCD_ACTIVITY_DATETIME	=	date("Y-m-d");
	$session1_date = date_create($GCD_ACTIVITY_DATETIME);
	$session1_date = date_format($session1_date,"Y-m-d");
	$resultfinal_status=execute_my_query("select count(*) no_of_task, sum(if(GITC_STATUS < 5 ,1,0)) pendings, " .
			" sum(if(GITC_STATUS = 5,1,0)) signed_off, min(GITC_STATUS) min_status, sum(GITC_WORKED_DURATION) total_duration " .
			" from gft_cust_imp_task_current_status_dtl where GITC_COMPLAINT_ID=$complaint_id group by GITC_COMPLAINT_ID ");
	if( ($ms_status_data=mysqli_fetch_array($resultfinal_status)) and mysqli_num_rows($resultfinal_status)>0){
		if($ms_status_data['min_status']==-1){
			$ms_status=-1;
		}else if($ms_status_data['no_of_task'] > $ms_status_data['signed_off'] and $ms_status_data['pendings']!=0){
			$ms_status=$ms_status_data['min_status'];
		}else if($ms_status_data['no_of_task'] == $ms_status_data['signed_off']){
			$ms_status=$ms_status_data['min_status'];
		}else{
			$ms_status=1;
		}
		$total_duration=$ms_status_data['total_duration'];
		$res_first_activity=execute_my_query("select GIMC_STATUS,GIMC_SESSION_1_CDATE,GIMC_OPCODE from gft_cust_imp_ms_current_status_dtl where GIMC_COMPLAINT_ID=$complaint_id and GIMC_MS_ID='$ms_id' and GIMC_STARTING_ACTIVITY_ID=0 and GIMC_STATUS in (-1,1,2) " );
		if(mysqli_num_rows($res_first_activity)==1 and $ms_status>=3){
			$first_activity=true;
		}
		if(mysqli_num_rows($res_first_activity)==1){
			$coupon_opcode	=	mysqli_result($res_first_activity,0,"GIMC_OPCODE");
			if($coupon_opcode!=''){				
				$is_only_custom_license	=	check_order_to_skip_bqaudit($order_no);
				if(!$is_only_custom_license){
					execute_my_query("update gft_coupon_distribution_dtl SET GCD_EXPIRY_DATE='$pd_validity_date',GCD_FIRST_EXPIRY_DATE='$pd_validity_date', GCD_AGE_START_DATE='$coupon_age_date' where GCD_ORDER_NO='$coupon_opcode' and (GCD_EXPIRY_DATE='0000-00-00' or isnull(GCD_EXPIRY_DATE)) and GCD_TO_ID!=0 and GCD_HANDLED_BY!=0");
				}
				if(exists_in_lead_hdr_ext($lead_code)){
					execute_my_query("update gft_lead_hdr_ext set GLE_PD_AGE='$pd_validity_date' where GLE_LEAD_CODE='$lead_code'");
				}else{
					execute_my_query("insert into gft_lead_hdr_ext (GLE_LEAD_CODE,GLE_PD_AGE) values ('$lead_code','$pd_validity_date')");
				}				
			}
		}
		$duration	= (int)$duration;
		execute_my_query(" update gft_cust_imp_ms_current_status_dtl set GIMC_STATUS='5',GIMC_APPROVAL='P',".
				"GIMC_SESSION_1_CDATE='$session1_date', GIMC_STARTING_ACTIVITY_ID='$ACT_ID', GIMC_WORKED_DURATION=GIMC_WORKED_DURATION+$duration " .
				" where GIMC_COMPLAINT_ID=$complaint_id and GIMC_MS_ID='$ms_id'");
	}
	if($product_delivery_id>0){
		execute_my_query("update gft_product_delivery_hdr set GPD_AGE_STARTED_DATE='$coupon_age_date',GPD_EXPIRY_DATE='$pd_validity_date'  where GPD_ID=$product_delivery_id AND (isnull(GPD_AGE_STARTED_DATE OR GPD_AGE_STARTED_DATE=''))");
	}
	if($training_tasks!=null && count($training_tasks)>0){
		$j=0;
		while($j<count($training_tasks)){
			execute_my_query("insert into gft_cust_imp_training_task_log (GCT_ACTIVITY_ID, GCT_MS_ID, GCT_TASK_ID, GCT_DATE, GCT_DURATION, GCT_STATUS, GCT_REMARKS) values " .
					"($ACT_ID,'$ms_id','".$training_tasks[$j]."','$session1_date','0',5,'')");
			$j++;
		}
		if($first_activity){
			execute_my_query(" update gft_cust_imp_ms_current_status_dtl set GIMC_STARTING_ACTIVITY_ID=$ACT_ID " .
					" where GIMC_COMPLAINT_ID=$complaint_id and GIMC_MS_ID='$ms_id' and GIMC_STARTING_ACTIVITY_ID=0 ");
		}
		execute_my_query("insert into gft_cust_imp_training_ms_log (GCM_ACTIVITY_ID, GCM_MS_ID, GCM_DATE, GCM_DURATION, GCM_STATUS) " .
				"value ($ACT_ID,$ms_id, date(now()),(select ifnull(sum(GCT_DURATION),0) " .
				" from gft_cust_imp_training_task_log where GCT_ACTIVITY_ID=$ACT_ID and GCT_MS_ID=$ms_id),5) ");
		update_pd_escalation_in_lead_hrd($lead_code, ""," Milestone on process");

	}
}
/**
 * @param int $current_training
 * @param int $total_training
 * @param boolean $is_reschedule
 * 
 * @return string
 */
function get_current_training_msg($current_training,$total_training,$is_reschedule=false){
	$training_details="Training";
	if($current_training==0 && $total_training==1){
		$training_details="Training Session";
	}
	if($current_training==0 && $total_training>1){
		$training_details="Final Session";
	}
	if($current_training>1 && $total_training>1){		
		if($is_reschedule){
			$training_details="Session - $current_training";
		}else{
			$current_training	=	($current_training-1);
			$training_details="Session - $current_training";
		}
	}
	return $training_details;
}
/**
 * @param string $training_id
 * 
 * @return string
 */
function get_training_scheduled_date($training_id){
	$result_training_dt	=	execute_my_query("select GCD_SCHEDULE_DATE from gft_customer_support_dtl ".
			" inner join gft_customer_support_hdr on(GCD_COMPLAINT_ID=GCH_COMPLAINT_ID AND gcd_activity_id=GCH_LAST_ACTIVITY_ID)".
			" where GCD_COMPLAINT_ID=$training_id");
	$actual_dt	=	"";
	if(mysqli_num_rows($result_training_dt)>0 && $dt_row=mysqli_fetch_array($result_training_dt)){
		$actual_dt	=	date('d-m-Y',strtotime($dt_row['GCD_SCHEDULE_DATE']));
	}
	return $actual_dt;
}
/**
 * @param string $task_ids
 * @param string $complaint_ids
 * @param string $activities_id
 * @param string $order_no
 * @param string $lead_code
 * @param int $current_training
 * @param int $total_training
 * @param string $duration
 *
 * @return void
 */
function send_product_delivery_training_mail($task_ids,$complaint_ids,$activities_id,$order_no,$lead_code,$current_training,$total_training,$duration){
	global $uid;
	$help_us_help_you	=	get_samee_const("Help_Us_Help_You");
	$sql = "select GIM_MS_NAME,GCD_TO_DO,GITC_REMARKS,GIT_MS_ID,GIT_TASK_TYPE,GIT_TASK_NAME,GTS_STATUS_NAME," .
			"GTS_STATUS_DESC,GITC_MS_ID,GITC_TASK_ID,GITC_DATE,GITC_ACTUAL_DURATION_MINS,GITC_WORKED_DURATION,GITC_STATUS " .
			"from gft_customer_support_dtl " .
			"join gft_cust_imp_task_current_status_dtl on (GCD_COMPLAINT_ID=GITC_COMPLAINT_ID) " .
			"join gft_impl_mailstone_master on (GIM_MS_ID=GITC_MS_ID) " .
			"join gft_impl_task_master on (GIT_TASK_ID=GITC_TASK_ID) " .
			"join gft_ms_task_status on (GTS_STATUS_CODE=GITC_STATUS) " .
			"where GCD_COMPLAINT_ID in ($complaint_ids) and gcd_activity_id in ($activities_id) and GITC_TASK_ID in ($task_ids) and GCD_TO_DO = 8";
	$exe = execute_my_query($sql);
	$ii = 1;
	$milestone_name = array();
	$datas='';
	while($results = mysqli_fetch_assoc($exe)){
		$milestone_name[$results['GIM_MS_NAME']]=$results['GIM_MS_NAME'];
		$datas .= '<tr><td>'.$ii++.'</td><td>'.$results['GIT_TASK_NAME'].'</td></tr>';
	}
	array_unique($milestone_name);
	$cust_dtl=customerContactDetail_Mail($lead_code,$order_no);
	$sales_emp_name	=9999;
	if(is_authorized_group_list($cust_dtl['Order_By_Whom'], array (54))){$sales_emp_name=$cust_dtl['Order_By_Whom']; }else{$sales_emp_name= $cust_dtl['LFD_EMP_ID']; }
	$jemp_master=get_emp_master($cust_dtl['Field_incharge'],$status='',$roleid=null,$only_gft_emp=false,$select_any=false);
	$com_jemp_master=get_emp_master($sales_emp_name,$status='',$roleid=null,$only_gft_emp=false,$select_any=false);
	//check is this order created by annuity
	$sys_requirement	=	get_system_requirement_link(substr($lead_code, 0,15));
	$milestones = implode(",",$milestone_name);
	$emp_company_name	=	'';
	$emp_partner_type	=	'';
	$other_cc			=	array();
	if(isPartnerEmployee((int)$cust_dtl['Field_incharge'])){
		$emp_company_name	=	getEmployeeCompanyName((int)$cust_dtl['Field_incharge']);
		$emp_partner_type	=	getEmployeeCompanyType((int)$cust_dtl['Field_incharge']);
	}
	$ts_contact_no_dtl = get_customer_ts_number($lead_code);
	$spoc_no	=	isset($ts_contact_no_dtl[0])?$ts_contact_no_dtl[0]:"";
	$spoc_person=	isset($ts_contact_no_dtl[1])?$ts_contact_no_dtl[1]:"";
	if($spoc_no==''){$spoc_no=$cust_dtl['mobile_no'];}
	if($spoc_person==''){$spoc_person=$cust_dtl['contact_name'];}
	$db_content_config =array('Milestone' => array('<table><tr><td>S.No</td><td>Task Name</td></tr>'.$datas.'</table>'),
			'Milestone_Name' => array($milestones),
			'Employee_Name' => array($jemp_master[0][1].$emp_company_name.$emp_partner_type),
			'Employee_Role' => array($jemp_master[0][7]!=''?$jemp_master[0][7]:'&nbsp;'),
			'PC_Mobile' => array($jemp_master[0][3]),
			'CM_Name'=>array($com_jemp_master[0][1]),
			'Customer_Id'=>array($lead_code),
			'Help_Us_Help_You'=>array($help_us_help_you),
			'System_Requirement'=>array($sys_requirement),
			'PC_Name'=>array($jemp_master[0][1]),
			'SPOC_NO'=>array($spoc_no),
			'SPOC_PERSON'=>array($spoc_person),
			'Duration'=>array($duration),
			'Customer_Mobile'=>array($cust_dtl['mobile_no']),
			'Customer_Mail'=>array($cust_dtl['EMAIL']),
			'Customer_Name'=>array($cust_dtl['cust_name'])
	);
	$other_cc	=	get_email_addr_reportingmaster($cust_dtl['Field_incharge'],true,explode(",", EMP_IDS_TO_SKIP_REPORTING_MAIL));
	$incharge_email=$com_jemp_master[0][4].','.$jemp_master[0][4].',training@gofrugal.com';
	$other_cc	=	array_merge(/*. (array) .*/$other_cc,explode(',',$incharge_email));
	send_formatted_mail_content($db_content_config,55,112,$employee_ids=array($uid),$customer_ids=array($lead_code),$tomail_ids=null, $other_cc=$other_cc); // customer email
	$training_details=get_current_training_msg($current_training,$total_training);
	$noti_content_config=array();
	$order_type	=	get_single_value_from_single_table("GOD_PD_EXPENSE_TYPE", "gft_order_hdr", "GOD_ORDER_NO", "$order_no");
	if($order_type=='5'){
		$training_details="$milestones";
	}
	$noti_content_config['Training_Details']=	array($training_details);
	$noti_content_config['CM_Name']			=	array($com_jemp_master[0][1]);
	$noti_content_config['Customer_Name']	=	array($cust_dtl['cust_name']);
	$noti_content_config['Customer_Id']		=	array($lead_code);
	$noti_content_config['SPOC_NO']			=	array($spoc_no);
	send_formatted_notification_content($noti_content_config,55,11,2,$lead_code);
	send_formatted_notification_content($noti_content_config,55,10,1,$sales_emp_name);
}
/**
 * @param string $training_id
 * @param boolean $last_act_task
 * 
 * @return mixed[]
 */
function get_training_task_logs($training_id,$last_act_task=false){
	$task_log	=	array();
	$act_id="";
	$act_id_group="group_concat";
	if($last_act_task){$act_id_group="max";}
	$result	=	execute_my_query("select $act_id_group(gcd_activity_id) gcd_activity_id from gft_customer_support_dtl where GCD_COMPLAINT_ID='$training_id' group by GCD_COMPLAINT_ID order by gcd_activity_id");
	if(mysqli_num_rows($result)>0 && $row=mysqli_fetch_array($result)){
		$act_id	=	$row['gcd_activity_id'];
	}
	if($act_id!=''){
		$sql_log_dtl	=	" select GIT_TASK_NAME,GCT_DATE from gft_cust_imp_training_task_log ".
				" inner join gft_impl_mailstone_master on(GCT_MS_ID=GIM_MS_ID) ".
				" inner join gft_impl_task_master on(GCT_TASK_ID=GIT_TASK_ID) ".
				" where GCT_ACTIVITY_ID in($act_id) and GCT_STATUS=5 group by GCT_TASK_ID";
		$res_log=	execute_my_query($sql_log_dtl);
		while($row_log=mysqli_fetch_array($res_log)){
			$task_log[]	=	$row_log['GIT_TASK_NAME'];
		}
	}
	if(count($task_log)==0 && $act_id!=""){
		$res_log=execute_my_query("select GPA_REQUIREMENT from gft_pcs_audit_dtl  where GPA_ACTIVITY_ID in($act_id)");
		while($row_log=mysqli_fetch_array($res_log)){
			$task_log[]	=	$row_log['GPA_REQUIREMENT'];
		}
	}
	return $task_log;
}
/**
 * @param string $product_delivery_id
 * @param string $lead_code
 *
 * @return mixed[]
 */
function get_golive_audit_logs($product_delivery_id,$lead_code){
	$task_log	=	array();
	$result = execute_my_query("select GAH_AUDIT_ID from gft_audit_hdr where GAH_LEAD_CODE='$lead_code' AND ".
						"GAH_REFFERNCE_ORDER_NO='$product_delivery_id' AND GAH_AUDIT_TYPE=47 order by GAH_AUDIT_ID desc limit 1");
	if(mysqli_num_rows($result)>0 && $row=mysqli_fetch_array($result)){
		$audit_id = $row['GAH_AUDIT_ID'];
		$result_dtl = execute_my_query("select GAD_AUDIT_ANS from gft_audit_dtl ". 
									" inner join  gft_audit_question_master ON(GAD_AUDIT_QID=GAQ_QUESTION_ID) ".
									" where GAD_AUDIT_ID='$audit_id' AND GAQ_INPUT_TYPE=13");
		if(mysqli_num_rows($result_dtl)>0 && $row1=mysqli_fetch_array($result_dtl)){
			$data_answer=$row1['GAD_AUDIT_ANS'];
			$task_log=explode(',',$data_answer);
		}
	}
	return $task_log;
}
/**
 * @param string $app_user_id
 * @param string $training_id
 * @param string $reschedule_emp
 * @param boolean $is_from_gofrugal
 * @param string $lead_code
 * 
 * @return string[int]
 */
function get_customer_training_approval_status($app_user_id,$training_id,$reschedule_emp='',$is_from_gofrugal=false,$lead_code=''){
	$return_arr	=	array();
	$query="";
	if($is_from_gofrugal && $lead_code!=''){
		$query	=	" select  GPT_IS_SPOC,GPT_ACK_STATUS,GPT_ACTIVITY_ON,GPT_ACTIVITY_TIME_SPENT from gft_customer_contact_dtl ".
					" inner join gft_pos_users on(gcc_id=GPU_CONTACT_ID) ".
					" left join gft_pd_training_feedback_dtl on(GPT_CONTACT_ID=gcc_id) ".
					" where GPU_USER_ID='$app_user_id' and GCC_LEAD_CODE='$lead_code'".
					" and GPT_TRAINING_ID='$training_id'  ".($reschedule_emp!=""?" AND GPT_IS_SPOC=3":" ")."  order by GPT_IS_SPOC ASC, GPT_ID DESC limit 1";
	}else{
		$query	=	" select GPT_IS_SPOC,GPT_ACK_STATUS,GPT_ACTIVITY_ON,GPT_ACTIVITY_TIME_SPENT from gft_customer_access_dtl ".
				" inner join gft_pd_training_feedback_dtl on(GCA_CONTACT_ID=GPT_CONTACT_ID) ".
				" where GCA_USER_ID='$app_user_id' and GPT_TRAINING_ID='$training_id' ".($reschedule_emp!=""?" AND GPT_IS_SPOC=3":" ")." order by GPT_IS_SPOC ASC,GPT_ID DESC limit 1";
	}
	$result	=	execute_my_query($query);
	if(mysqli_num_rows($result)>0 && $row=mysqli_fetch_array($result)){
		$return_arr[0]=$row['GPT_IS_SPOC'];
		$return_arr[1]=$row['GPT_ACK_STATUS'];
		$return_arr[2]=$row['GPT_ACTIVITY_ON'];
		$return_arr[3]=$row['GPT_ACTIVITY_TIME_SPENT'];
	}
	return $return_arr;
}
/**
 * @param string $training_id
 * @param string $custCode
 *
 * @return string[string]
 */
function get_training_milestone_logs($training_id,$custCode=''){
	$return_array = /*. (string[string]) .*/array();
	$whare_con	=	"";
	if($custCode!=""){$whare_con=" AND GCH_LEAD_CODE=$custCode";}
	$query=" select hdr.GCH_COMPLAINT_ID,GCM_ACTIVITY_ID,sc.GEM_EMP_NAME schedule_to, GCM_DATE, GIM_MS_NAME, GCD_ACTIVITY_DATE,".
			" GTS_STATUS_NAME, GCM_DURATION,hdr.GCH_FIRST_SCHEDULE_DATE first_schedule_date" .
			" From  gft_customer_support_hdr hdr " .
			" join gft_customer_support_dtl dtl on (hdr.GCH_COMPLAINT_ID=dtl.GCD_COMPLAINT_ID) " .
			" left join gft_cust_imp_training_ms_log td on(dtl.GCD_ACTIVITY_ID = GCM_ACTIVITY_ID)" .
			" left join gft_impl_mailstone_master on(GIM_MS_ID=GCM_MS_ID) " .
			" left join gft_ms_task_status s1 on (s1.GTS_STATUS_CODE= td.GCM_STATUS) " .
			" left join gft_emp_master sc on (sc.gem_emp_id=GCD_PROCESS_EMP) " .
			" WHERE  GCH_COMPLAINT_ID='$training_id' $whare_con" .
			" order by GCM_ACTIVITY_ID desc  limit 1";
	$result = execute_my_query($query);
	while($row=mysqli_fetch_array($result)){
		$return_array['training_given_by']=$row['schedule_to'];
		$return_array['training_given_on']=$row['GCD_ACTIVITY_DATE'];
		$return_array['first_schedule_date']=$row['first_schedule_date'];
	}
	return $return_array;
}
/**
 * @param string $lead_code
 * @param string $opcode
 * @param string $app_user_id
 * @param boolean $is_from_gofrugal
 *
 * @return mixed[]
 */
function get_training_approval_status($lead_code,$opcode,$app_user_id,$is_from_gofrugal){
	$all_traininig_details	=	array();
	$sql_training_dtl	=	" select GIMC_COMPLAINT_ID,GIMC_STATUS,GIMC_REFOPCODE,GIMC_OPCODE,GCD_RECEIVED_DATE, GCD_RECEIVED_BY, ".
			" em.gem_emp_name,em1.gem_emp_name as training_incharge, GCD_SIGNED_OFF, GCD_SCHEDULE_DATE,".
			" GIMC_STARTING_ACTIVITY_ID, GCD_ACTIVITY_DATE,GAN_STATUS  from  gft_cust_imp_ms_current_status_dtl  ".
			" inner join gft_customer_support_hdr csh on(csh.GCH_COMPLAINT_ID=GIMC_COMPLAINT_ID AND csh.GCH_LEAD_CODE=$lead_code) ".
			" inner join gft_customer_support_dtl on(csh.GCH_COMPLAINT_ID=GCD_COMPLAINT_ID and gcd_activity_id=GCH_LAST_ACTIVITY_ID) ".
			" inner join gft_lead_hdr lh on(lh.glh_lead_code=$lead_code) ".
			" left join gft_coupon_distribution_dtl cd on(GCD_TRAINING_ID=GIMC_COMPLAINT_ID and GCD_IS_ECOUPON=1) ".
			" left join gft_emp_master em on(em.gem_emp_id=GCD_RECEIVED_BY) ".
			" left join gft_emp_master em1 on(em1.gem_emp_id=GLH_FIELD_INCHARGE) ".
			" left join gft_app_not_install_approval ON(GIMC_COMPLAINT_ID=GAN_TRAINING_ID AND GAN_STATUS=1)".
			" where GIMC_OPCODE='$opcode' order by GIMC_COMPLAINT_ID";
	$result_training	=	execute_my_query($sql_training_dtl);
	$training_no	=	0;
	while($row_training=mysqli_fetch_array($result_training)){
		$act_id		=	$row_training['GIMC_STARTING_ACTIVITY_ID'];
		$training_id=	$row_training['GIMC_COMPLAINT_ID'];
		$task_log	=	get_training_task_logs($training_id);
		$ack_dtl	=	get_customer_training_approval_status($app_user_id,$training_id,"",$is_from_gofrugal,$lead_code);
		$trainee_role=isset($ack_dtl[0])?$ack_dtl[0]:"";
		$trainee_ack_status=isset($ack_dtl[1])?$ack_dtl[1]:"";
		if($row_training['GIMC_STATUS']=='5' && $row_training['GCD_SIGNED_OFF']=='R'){
			$training_dtl	=	array();
			$training_no++;
			$training_dtl['title']	=	"Training session $training_no";
			$training_dtl['description']	=	"Training session $training_no completed and confirmed";
			$training_dtl['date']	=	$row_training['GCD_ACTIVITY_DATE'];
			$training_dtl['action']	=	array("label"=>"View history",
					"type"=>"audit",
					"sub_type"=>"history",
					"id"=>"$training_id");
			if($trainee_role=='2' && $trainee_ack_status=='0'){
				$training_dtl['action']	=	array("label"=>"Feedback",
						"type"=>"action",
						"sub_type"=>"acknowledge",
						"end_point"=>"/ismile/pd_training_feedback.php",
						"id"=>"$training_id");
			}
			$training_dtl['training_history']=array(array("label"=>"Tasks covered","type"=>"list","value"=>$task_log),
					array("label"=>"Product Consultant:","type"=>"text","value"=>$row_training['training_incharge']),
					array("label"=>"Training completed on:","type"=>"text","value"=>$row_training['GCD_RECEIVED_DATE']));
			$all_traininig_details[]	=	$training_dtl;
		}
		if(($row_training['GIMC_STATUS']=='5' or $row_training['GIMC_STATUS']=='-1') and $row_training['GCD_SIGNED_OFF']=='Y'){
			$training_no++;
			$cm_approval_pending_msg = "Waiting for CM approval";
			if($row_training['GAN_STATUS']=='1'){//Whatsnow not installed case, check installation details.
			    $is_installed_whatsnow = (int)get_single_value_from_single_query("GID_PRODUCT_CODE", "select GID_PRODUCT_CODE from gft_install_dtl_new where GID_PRODUCT_CODE=703 AND GID_LEAD_CODE='$lead_code'");
			    if($is_installed_whatsnow>0){
			        execute_my_query("update gft_app_not_install_approval set GAN_STATUS=2, GAN_APPROVED_BY=9999, GAN_REMARKS='Approved automatically because installed whatsnow app'  where GAN_TRAINING_ID='$training_id' AND GAN_STATUS=1 AND GAN_LEAD_CODE='$lead_code'");
			        $row_training['GAN_STATUS']='2';
			    }
			    if($row_training['GAN_STATUS']=='1'){
			        $lead_owner_query = " select  IF(glh_lfd_emp_id<7000 AND glh_lfd_emp_id NOT IN(9999,9998), em.GEM_EMP_NAME , em1.GEM_EMP_NAME ) lead_owner from gft_lead_hdr ".
			 			                " inner join gft_emp_master em ON(em.GEM_EMP_ID=glh_lfd_emp_id) ".
                                        " LEFT join gft_cp_info on (glh_lfd_emp_id=CGI_EMP_ID ) ".
                                        " left join gft_emp_master em1 ON(em1.GEM_EMP_ID=cgi_incharge_emp_id) ".
                                        " where glh_lead_code='$lead_code'";
			        $lead_owner_name = get_single_value_from_single_query("lead_owner", $lead_owner_query);
			        $cm_approval_pending_msg = "Waiting for $lead_owner_name approval";
			    }
			}
			$training_dtl	=	array();
			$training_dtl['title']	=	"Training session $training_no";
			$training_dtl['description']	=	"Training session $training_no by Mr ".$row_training['training_incharge'];
			$training_dtl['date']	=	$row_training['GCD_ACTIVITY_DATE'];
			$training_dtl['action']	=	array("label"=>($row_training['GAN_STATUS']=='1'?"$cm_approval_pending_msg":"View history"),
					"type"=>"audit",
					"sub_type"=>"history",
					"id"=>"$training_id");			
			if(($trainee_role=='2' || $trainee_role=='1') && $trainee_ack_status=='0' && $row_training['GAN_STATUS']!='1'){
				$training_dtl['action']	=	array("label"=>($trainee_role=='2'?"Feedback":"Confirmation"),
						"type"=>"action",
						"sub_type"=>"acknowledge",
						"end_point"=>"/ismile/pd_training_feedback.php",
						"id"=>"$training_id");
			}
			$training_dtl['training_history']=array(array("label"=>"Tasks covered","type"=>"list","value"=>$task_log),
					array("label"=>"Product Consultant:","type"=>"text","value"=>$row_training['training_incharge']),
					array("label"=>"Training completed on:","type"=>"text","value"=>$row_training['GCD_RECEIVED_DATE']));
			$all_traininig_details[]	=	$training_dtl;
			break;
		}
		if($row_training['GIMC_STATUS']!='5' and $row_training['GCD_SIGNED_OFF']==''){
			$ack_dtl1	=	get_customer_training_approval_status($app_user_id,$training_id,"reschedule_emp",$is_from_gofrugal,$lead_code);
			$trainee_role=isset($ack_dtl1[0])?$ack_dtl1[0]:"";
			$training_no++;
			$training_dtl	=	array();
			$training_dtl['title']	=	"Training session $training_no";
			$training_dtl['description']	=	"Training session $training_no by Mr ".$row_training['training_incharge'];
			$training_dtl['date']	=	$row_training['GCD_ACTIVITY_DATE'];
			$schedule_on=get_date_in_indian_format($row_training['GCD_SCHEDULE_DATE']);
			if($trainee_role=='3'){
				$training_dtl['action']	=	array("label"=>"Check details",
						"type"=>"action",
						"sub_type"=>"reschedule",
						"end_point"=>"/ismile/reschedule_pd_training.php",
						"id"=>"$training_id");
			}
			$training_dtl['list']	=	array(array("label"=>"Implementation specialist:","type"=>"text","value"=>$row_training['training_incharge']),
					array("label"=>"Scheduled on:","type"=>"text","value"=>$schedule_on));
			$all_traininig_details[]	=	$training_dtl;
			break;
		}
	}
	$reversed = array_reverse($all_traininig_details);
	return $reversed;
}
/**
 * @param int $product_delivery_id
 * 
 * @return int
 */
function get_product_delivery_bq_audit_id($product_delivery_id){
	$audit_act_id = 0;
	$result = execute_my_query("SELECT GAH_AUDIT_ID FROM gft_audit_hdr WHERE GAH_AUDIT_TYPE=15 AND GAH_REFFERNCE_ORDER_NO='$product_delivery_id' ORDER BY GAH_AUDIT_ID DESC LIMIT 1");
	if(mysqli_num_rows($result)>0 && $row=mysqli_fetch_array($result)){
		$audit_act_id = $row['GAH_AUDIT_ID'];
	}
	return $audit_act_id;
}
/**
 * @param string $audit_act_id
 *
 * @return string[int]
 */
function get_bq_audit_logs($audit_act_id){
	$result_bq	=	execute_my_query("select GAQ_QUESTION_TYPE,GAD_AUDIT_ANS from gft_audit_dtl ".
			" INNER JOIN gft_audit_question_master ON(GAD_AUDIT_QID=GAQ_QUESTION_ID) ".
			" where GAD_AUDIT_ID in($audit_act_id)");
	$task_log	=	array();
	while($row_bq=mysqli_fetch_array($result_bq)){
		$task_log[]=$row_bq['GAQ_QUESTION_TYPE'].": ".$row_bq['GAD_AUDIT_ANS'];
	}
	return $task_log;
}
/**
 * 
 * @param string $app_user_id
 * @param string $complaint_id
 * @param boolean $is_from_gofrugal
 * @param string $lead_code
 * @param string $activity_type
 * @param int $check_first_activity
 * @param int $product_delivery_id
 * 
 * @return mixed[]
 */
function bq_audit_history($app_user_id,$complaint_id,$is_from_gofrugal,$lead_code,$activity_type,$check_first_activity=0,$product_delivery_id=0){
	$ack_dtl	=	get_customer_training_approval_status($app_user_id,$complaint_id,"",$is_from_gofrugal,$lead_code);
	$trainee_role=isset($ack_dtl[0])?$ack_dtl[0]:"";
	$trainee_ack_status=isset($ack_dtl[1])?$ack_dtl[1]:"";
	$activity_on	=	isset($ack_dtl[2])?$ack_dtl[2]:"";
	$all_traininig_details['status']	=	true;
	$task_covered="Tasks covered";
	$training_dtl	=	array();
	$task_log	=	get_training_task_logs($complaint_id);
	$training_log = get_training_milestone_logs($complaint_id,$lead_code);
	$training_given_by = isset($training_log['training_given_by'])?$training_log['training_given_by']:"";
	$training_given_on = isset($training_log['training_given_on'])?$training_log['training_given_on']:"";
	$first_schedule_date = isset($training_log['first_schedule_date'])?$training_log['first_schedule_date']:"";	
	$training_dtl['title']	=	"BQ Activity";
	$training_dtl['description']=	"Business Questionnaire & Hardware Assessment";
	$training_dtl['action']	=	array("label"=>"View history",
			"type"=>"audit",
			"sub_type"=>"history",
			"id"=>"$complaint_id");
	if($activity_type=="bq" && $product_delivery_id>0){
		$task_covered="BQ Checklist";
		$audit_act_id 	= 	get_product_delivery_bq_audit_id($product_delivery_id);
		if($audit_act_id>0){
			$task_log 		=	get_bq_audit_logs($audit_act_id);
		}
	}else if($activity_type=="bq"){
		$task_covered="";
	}
	if($activity_type=="mdm"){
		$training_dtl['title']	=	"MDM Activity";
		$training_dtl['description']=	"Master Data Migration";
	}
	if($activity_type=="uat"){
		$training_dtl['title']	=	"Training Activity";
		$training_dtl['description']=	"Training Activity by Mr $training_given_by";
	}
	if($activity_type=="golive"){
		$training_dtl['title']	=	"Go-Live";
		$training_dtl['description']=	"Go Live Confirmation";
		if($product_delivery_id>0){
			$task_log	=	get_golive_audit_logs($product_delivery_id,$lead_code);
		}
	}
	if($activity_type=="confirm-golive"){
		$training_dtl['title']	=	"Confirm Go-Live Readiness";
		$training_dtl['description']=	"Confirm Go-Live Readiness";
		$training_dtl['action']	=	array("label"=>($trainee_role=='2'?"Feedback":"Confirmation"),
				"type"=>"action",
				"sub_type"=>"acknowledge",
				"end_point"=>"/ismile/pd_training_feedback.php",
				"id"=>"$complaint_id");
	}
	$training_dtl['date']	=	$activity_on;	
	if(($trainee_role=='2' || $trainee_role=='1') && $trainee_ack_status=='0'){
		$training_dtl['action']	=	array("label"=>($trainee_role=='2'?"Feedback":"Confirmation"),
				"type"=>"action",
				"sub_type"=>"acknowledge",
				"end_point"=>"/ismile/pd_training_feedback.php",
				"id"=>"$complaint_id");
		$all_traininig_details['status']	=	false;
	}else{
		$ack_dtl1	=	get_customer_training_approval_status($app_user_id,$complaint_id,"reschedule_emp",$is_from_gofrugal,$lead_code);
		$trainee_role=isset($ack_dtl1[0])?$ack_dtl1[0]:"";
		if($trainee_role=='3' && $check_first_activity==1){
			$training_dtl['action']	=	array("label"=>"Check details",
					"type"=>"action",
					"sub_type"=>"reschedule",
					"end_point"=>"/ismile/reschedule_pd_training.php",
					"id"=>"$complaint_id");
		}
		$training_dtl['list']	=	array(array("label"=>"Implementation specialist:","type"=>"text","value"=>"$training_given_by"),
				array("label"=>"Scheduled on:","type"=>"text","value"=>$first_schedule_date));
		
	}	
	$training_dtl['training_history']=array(array("label"=>($activity_type=="golive"?"Confirmed Go Live Check List":"$task_covered"),"type"=>"list","value"=>$task_log),
	 array("label"=>"Product Consultant:","type"=>"text","value"=>$training_given_by),
	 array("label"=>"Completed on:","type"=>"text","value"=>$training_given_on));
	$all_traininig_details['Training']	=	$training_dtl;
	return $all_traininig_details;
}
/**
 * @param string $lead_code
 * @param string $opcode
 * @param string $app_user_id
 * @param boolean $is_from_gofrugal
 * @param string $order_no
 *
 * @return mixed[]
 */
function get_training_approval_status_new($lead_code,$opcode,$app_user_id,$is_from_gofrugal,$order_no){	
	$all_traininig_details	=	array();
	$sql_pd_dtl = 	" select  GPD_ID,GPO_ORDER_NO,GPD_CURRENT_STATUS,GPD_BQ_MINS,GPD_USED_BQ_MINS,GPD_MDM_MINS,GPD_USED_MDM_MINS,".
					" GPD_UAT_MINS,GPD_USED_UAT_MINS,GPD_GO_LIVE_MINS,GPD_USED_GO_LIVE_MINS	 from gft_pd_order_mapping ".
					" INNER JOIN gft_product_delivery_hdr pd ON(GPO_PD_ID=GPD_ID)".
					" where GPO_PD_ID='$opcode' AND GPD_LEAD_CODE='$lead_code' group by GPD_ID";
	$result_pd_dtl	=	execute_my_query($sql_pd_dtl);
	/* Start: For getting golive delivery history for outlet*/
	if(mysqli_num_rows($result_pd_dtl)==0){
		$result_outlet_dtl = execute_my_query("select GOL_GOLIVE_MILESTONE_ID from gft_outlet_lead_code_mapping where GOL_CUST_ID='$lead_code' AND GOL_ORDER_NO='$order_no'");
		if(mysqli_num_rows($result_outlet_dtl)>0 && $row_outlet=mysqli_fetch_array($result_outlet_dtl)){
			$golive_complaint_id = $row_outlet['GOL_GOLIVE_MILESTONE_ID'];
			$golive_dtl  	=	bq_audit_history($app_user_id,$golive_complaint_id,$is_from_gofrugal,$lead_code,"golive");
			$all_traininig_details[]=$golive_dtl['Training'];
			$reversed = array_reverse($all_traininig_details);
			return $reversed;
		}
	}
	/* Start: For getting golive delivery history for outlet*/
	while($rows=mysqli_fetch_array($result_pd_dtl)){
		$product_delivery_id= 	$rows['GPD_ID'];
		$current_status 	= 	(int)$rows['GPD_CURRENT_STATUS'];
		$milestone_ids		=	get_product_delivery_ms_ids($product_delivery_id);
		$pd_dtls			=	get_product_delivery_training_dtl($product_delivery_id);
		$order_no 			= 	isset($pd_dtls['orderno'])?$pd_dtls['orderno']:"";
		$pd_order_type 		= 	isset($pd_dtls['ordertype'])?(int)$pd_dtls['ordertype']:0;
		$bq_complaint_id   	= 	isset($milestone_ids['35'])?(int)$milestone_ids['35']:0;
		$mdm_complaint_id   = 	isset($milestone_ids['36'])?(int)$milestone_ids['36']:0;
		$uat_complaint_id   = 	isset($milestone_ids['37'])?(int)$milestone_ids['37']:0;
		$golive_complaint_id= 	isset($milestone_ids['3'])?(int)$milestone_ids['3']:0;
		$training_dtl		=	array();		
		$is_only_custom_license	=	check_order_to_skip_bqaudit($order_no);
		if($current_status==11 && $pd_order_type>1){
				$bq_dtl  	=	bq_audit_history($app_user_id,$bq_complaint_id,$is_from_gofrugal,$lead_code,"bq",1);
				$all_traininig_details[]=$bq_dtl['Training'];				
		}else if($current_status==11){
			$bq_dtl  	=	bq_audit_history($app_user_id,$bq_complaint_id,$is_from_gofrugal,$lead_code,"bq",1,$product_delivery_id);
			$all_traininig_details[]=$bq_dtl['Training'];
		}else if($current_status==12 && $pd_order_type>1){
			$bq_dtl  	=	bq_audit_history($app_user_id,$bq_complaint_id,$is_from_gofrugal,$lead_code,"bq");
			$all_traininig_details[]=$bq_dtl['Training'];
			if(isset($bq_dtl['status']) && $bq_dtl['status']==true){
				$mdm_dtl  	=	bq_audit_history($app_user_id,$mdm_complaint_id,$is_from_gofrugal,$lead_code,"mdm",1);
				$all_traininig_details[]=$mdm_dtl['Training'];
			}
		}else if($current_status==13 && $pd_order_type>1){
			$bq_dtl  	=	bq_audit_history($app_user_id,$bq_complaint_id,$is_from_gofrugal,$lead_code,"bq");
			$all_traininig_details[]=$bq_dtl['Training'];
			$mdm_dtl  	=	bq_audit_history($app_user_id,$mdm_complaint_id,$is_from_gofrugal,$lead_code,"mdm");
			$all_traininig_details[]=$mdm_dtl['Training'];
			if(isset($mdm_dtl['status']) && $mdm_dtl['status']==true){
				$uat_dtl  	=	bq_audit_history($app_user_id,$uat_complaint_id,$is_from_gofrugal,$lead_code,"uat",1);
				$all_traininig_details[]=$uat_dtl['Training'];
			}			
		}else if($current_status==13){
			$bq_dtl  	=	bq_audit_history($app_user_id,$bq_complaint_id,$is_from_gofrugal,$lead_code,"bq",0,$product_delivery_id);
			if(!$is_only_custom_license){				
				$all_traininig_details[]=$bq_dtl['Training'];
			}			
			if(isset($bq_dtl['status']) && $bq_dtl['status']==true){
				$uat_dtl  	=	bq_audit_history($app_user_id,$uat_complaint_id,$is_from_gofrugal,$lead_code,"uat",1);
				$all_traininig_details[]=$uat_dtl['Training'];
			}			
		}else if($current_status==14 && $pd_order_type>1){
			$bq_dtl  	=	bq_audit_history($app_user_id,$bq_complaint_id,$is_from_gofrugal,$lead_code,"bq");
			$all_traininig_details[]=$bq_dtl['Training'];
			$mdm_dtl  	=	bq_audit_history($app_user_id,$mdm_complaint_id,$is_from_gofrugal,$lead_code,"mdm");
			$all_traininig_details[]=$mdm_dtl['Training'];
			$uat_dtl  	=	bq_audit_history($app_user_id,$uat_complaint_id,$is_from_gofrugal,$lead_code,"uat");
			$all_traininig_details[]=$uat_dtl['Training'];
			if(isset($uat_dtl['status']) && $uat_dtl['status']==true){
				$golive_dtl  	=	bq_audit_history($app_user_id,$golive_complaint_id,$is_from_gofrugal,$lead_code,"confirm-golive");
				$all_traininig_details[]=$golive_dtl['Training'];
			}
		}else if($current_status==15){
			$bq_dtl  	=	bq_audit_history($app_user_id,$bq_complaint_id,$is_from_gofrugal,$lead_code,"bq");
			$all_traininig_details[]=$bq_dtl['Training'];
			$mdm_dtl  	=	bq_audit_history($app_user_id,$mdm_complaint_id,$is_from_gofrugal,$lead_code,"mdm");
			$all_traininig_details[]=$mdm_dtl['Training'];
			$uat_dtl  	=	bq_audit_history($app_user_id,$uat_complaint_id,$is_from_gofrugal,$lead_code,"uat");
			$all_traininig_details[]=$uat_dtl['Training'];
			$golive_dtl  	=	bq_audit_history($app_user_id,$golive_complaint_id,$is_from_gofrugal,$lead_code,"golive",0,$product_delivery_id);
			$all_traininig_details[]=$golive_dtl['Training'];
		}else if($current_status==16 && $pd_order_type>1){
			$bq_dtl  	=	bq_audit_history($app_user_id,$bq_complaint_id,$is_from_gofrugal,$lead_code,"bq");
			$all_traininig_details[]=$bq_dtl['Training'];
			$mdm_dtl  	=	bq_audit_history($app_user_id,$mdm_complaint_id,$is_from_gofrugal,$lead_code,"mdm");
			$all_traininig_details[]=$mdm_dtl['Training'];
			$uat_dtl  	=	bq_audit_history($app_user_id,$uat_complaint_id,$is_from_gofrugal,$lead_code,"uat");
			$all_traininig_details[]=$uat_dtl['Training'];
			$golive_dtl  	=	bq_audit_history($app_user_id,$golive_complaint_id,$is_from_gofrugal,$lead_code,"golive",0,$product_delivery_id);
			$all_traininig_details[]=$golive_dtl['Training'];
		}else if($current_status==16){
			$bq_dtl  	=	bq_audit_history($app_user_id,$bq_complaint_id,$is_from_gofrugal,$lead_code,"bq",0,$product_delivery_id);
			if(!$is_only_custom_license){				
				$all_traininig_details[]=$bq_dtl['Training'];
			}			
			$uat_dtl  	=	bq_audit_history($app_user_id,$uat_complaint_id,$is_from_gofrugal,$lead_code,"uat");
			$all_traininig_details[]=$uat_dtl['Training'];
			
		}				
	}
	$reversed = array_reverse($all_traininig_details);
	return $reversed;
}
/**
 * @param string $order_no
 * @param string $lead_code
 * @param string $return_type
 * 
 * @return string
 */
function check_product_delivery_installation_dtl($order_no,$lead_code,$return_type=''){
	$install_status= 'Y';
	$install_id		=	"";
	$res_product_dtl=execute_my_query("select if(god_order_splict=1,GCO_CUST_CODE,GOD_LEAD_CODE) GOD_LEAD_CODE,if(god_order_splict=1,GCO_PRODUCT_CODE, ".
				" GOP_PRODUCT_CODE )  GOP_PRODUCT_CODE,if(god_order_splict=1,GCO_SKEW,GOP_PRODUCT_SKEW ) GOP_PRODUCT_SKEW,GID_STATUS, ".
				" GFT_SKEW_PROPERTY,GID_INSTALL_ID from gft_order_hdr  join gft_order_product_dtl on (gop_order_no=god_order_no) ". 
				" left join gft_cp_order_dtl on (gco_order_no=gop_order_no and  gco_product_code=gop_product_code and ". 
				" gco_skew=gop_product_skew) join gft_product_master on (gpm_product_code=gop_product_code and ". 
				" gpm_product_skew=gop_product_skew) left join gft_install_dtl_new on (((gid_order_no=god_order_no and ". 
				" (gid_product_skew=gop_product_skew or  gid_lic_pskew=gop_product_skew)) or (gid_lic_pskew=gft_higher_skew and ". 
				" gid_lic_order_no=god_order_no)) and  gid_product_code=gop_product_code) where god_order_no='$order_no' ". 
				" and if(god_order_splict=1,GCO_CUST_CODE,GOD_LEAD_CODE)='$lead_code' and gop_product_code in (500,200) ".
				" and  gft_skew_property in (1,2,11) AND GPM_PRODUCT_TYPE not in(8,9,10) ");
	while($row_pro=mysqli_fetch_array($res_product_dtl)){
		if(($row_pro['GFT_SKEW_PROPERTY']=='1' or $row_pro['GFT_SKEW_PROPERTY']=='11') and $row_pro['GID_STATUS']==''){
			$install_status= 'N';
		}
		if($row_pro['GID_INSTALL_ID']!='' && $install_id==''){
			$install_id=$row_pro['GID_INSTALL_ID'];
		}
	}
	if($return_type=='install_id'){
		return $install_id;
	}
	return $install_status;
}
/**
 * @param string $lead_code
 * 
 * @return string
 */
function get_customer_answer_for_bq_query($lead_code){
	$query="SELECT GLH_LEAD_CODE, GAH_AUDIT_ID,  GAT_AUDIT_DESC, GAH_DATE_TIME, " .
			" a.GEM_EMP_NAME as audit,b.GEM_EMP_NAME as incharge,c.GEM_EMP_NAME as field,GAH_CUSTOMER_COMMENTS,GAH_MY_COMMENTS,GAH_AUDIT_TYPE " .
			" FROM gft_lead_hdr" .
			" JOIN gft_audit_hdr ON (GLH_LEAD_CODE=GAH_LEAD_CODE)" .
			" JOIN gft_audit_type_master ON(GAH_AUDIT_TYPE=GAT_AUDIT_ID) " .
			" left JOIN gft_emp_master c on (c.GEM_EMP_ID=GAH_FIELD_INCHARGE)" .
			" left JOIN gft_emp_master b on (b.GEM_EMP_ID=GAH_L1_INCHARGE)" .
			" left JOIN gft_emp_master a on (a.GEM_EMP_ID=GAH_AUDIT_BY) " .
			" where GLH_LEAD_CODE=$lead_code AND GAH_AUDIT_TYPE='41'" .
			"GROUP BY GAH_AUDIT_ID order by GAH_DATE_TIME desc";
	return $query;
}
/**
 * @param string $lead_code
 * @param string $type
 * 
 * @return mixed[]
 */
function get_customer_spoc_list($lead_code,$type='1'){
	$spoc_default	=	"";
	$contact_ids	=	array();
	$contact_arr	=	array();
	$contact_type	=	"1,4,2";
	if($type=='2' || $type=='1'){//send both mobile and email
		$contact_type	=	"1,2";
	}
	$sql_cust_contact_list	=	" select  gcc_id,GCC_CONTACT_NAME,GCC_CONTACT_NO,GCG_GROUP_ID,GCA_ID,gcc_contact_type from gft_customer_contact_dtl ".
			" left join gft_contact_dtl_group_map on(gcc_id=GCG_CONTACT_ID and GCG_GROUP_ID=1) ".
			" left join gft_customer_access_dtl on(gcc_id=GCA_CONTACT_ID)".
			" where GCC_LEAD_CODE='$lead_code' and gcc_contact_type in($contact_type)  order by GCA_ID desc";
	$res_cust_contact	=	execute_my_query($sql_cust_contact_list);
	while($row_con=mysqli_fetch_array($res_cust_contact)){
		$single_contact_arr['id']=$row_con['gcc_id'];
		$single_contact_arr['name']=$row_con['GCC_CONTACT_NAME']." - ".$row_con['GCC_CONTACT_NO'];
		$single_contact_arr['type']=$row_con['gcc_contact_type'];
		$single_contact_arr['tag']="myGoFrugal Installed";
		if($row_con['GCA_ID']==""){
			$single_contact_arr['tag']="App Not Installed";
		}
		$contact_arr[]	=	$single_contact_arr;
		if($row_con['GCG_GROUP_ID']!=''){
			$spoc_default=$row_con['gcc_id'];
		}else if($row_con['gcc_contact_type']=='1'){
			$contact_ids[]	= $row_con['gcc_id'];	
		}
		
	}
	$return_arr[0]=$contact_arr;
	$return_arr[1]=$spoc_default;
	$return_arr[2]=	$contact_ids;
	return $return_arr;
}
/**
 * @param string $contact_id
 * 
 * @return string[int]
 */
function get_customer_contact_name_and_value($contact_id){
	$return_arr=array(0=>"",1=>"");
	$rows=execute_my_query("select GCC_CONTACT_NAME,GCC_CONTACT_NO from gft_customer_contact_dtl where gcc_id in($contact_id)");
	if(mysqli_num_rows($rows)>0 && $row=mysqli_fetch_array($rows)){
		$return_arr[0]=$row['GCC_CONTACT_NAME'];
		$return_arr[1]=$row['GCC_CONTACT_NO'];
	}
	return $return_arr;
}
/**
 * @param string $lead_code
 *
 * @return string
 */
function get_common_field_for_training_activity($lead_code){
	$spoc_options 	= 	get_spoc_design($lead_code);
	$time_spent_option = "";$second_spent_option = "";$inc=1;
	$second		=	5;
	while($inc<=10){
		$time_spent_option .="<option value='$inc'>$inc Hour</option>";
		$inc++;
	}
	while($second<=55){
		$second_spent_option .="<option value='$second'>$second Min</option>";
		$second=$second+5;
	}
	$html_content=<<<START
		<div class="form-group">
	   		<label class="required">*</label>
			<strong for="time_spent"  >Time Spent:</strong>
			<div class="row">
			<select name='timespent' id='timespent'  class="form-control" style='width:35%;float:left;margin-left:15px;'><option value='0'>Hours</option>$time_spent_option</select>
			&nbsp;&nbsp;
			<select name='timespent_minutes' id='timespent_minutes'  class="form-control"  style='width:40%;float:left;'><option value='0'>Mins</option>$second_spent_option</select>
			</div>
		</div>
		<div class="form-group"><br>
	   		<label class="required">*</label>
			<label for="spoc">Select SPOC:</label>
			<select name='spoc' id='spoc' class="form-control"><option value=''>Select</option>$spoc_options</select>
		</div>
		<div class="form-group">
	   		<label class="required">*</label>
			<label for="time_spent">Select Trainees:</label>
			<select name='trainees' id='trainees' multiple  class="form-control"><option value=''>Select</option>$spoc_options</select>
		</div>
		<div class="form-group">
	   		<label class="required">*</label>
			<label for="time_spent">Customer Comments:</label>
			<textarea name='customer_comments' id='customer_comments' class="form-control"></textarea>
		</div>
		<div class="form-group">
	   		<label class="required">*</label>
			<label for="time_spent">My Comments:</label>
			<textarea name='my_commments' id='my_commments' class="form-control"></textarea>
		</div>
START;
	return $html_content;
}
/**
 * @param int $product_delivery_id
 * @param string $lead_code
 *
 * @return string
 */
function get_golive_customer_details_query($product_delivery_id,$lead_code=''){
	$sql_outlet_dtl		=	" select CONCAT(GLH_CUST_NAME,' - ',GLH_CUST_STREETADDR2) outlet_name, GOL_OUTLET_ID, GPD_ORDER_NO,".
					" GPD_LEAD_CODE,GOL_CUST_ID,GOL_ORDER_NO, GOL_FULLFILLMENT_NO,GOL_DELIVERY_GOLIVE_STATUS,GOL_EDITION,".
					" GOL_GOLIVE_MILESTONE_ID,glh_country,gpm_map_id,gid_lic_pcode,gid_lic_pskew from gft_product_delivery_hdr ".
					" INNER JOIN gft_install_dtl_new ins ON(GPD_LEAD_CODE=GID_LEAD_CODE) ".
					" INNER JOIN gft_outlet_lead_code_mapping om ON(GOL_INSTALL_ID=GID_INSTALL_ID) ".
					" INNER JOIN gft_lead_hdr lh ON(GOL_CUST_ID=GLH_LEAD_CODE) ".
					" left join gft_political_map_master on (gpm_map_name=lh.glh_cust_statecode and gpm_map_type='S') ".
					" where GPD_ID='$product_delivery_id' AND (GPD_ORDER_NO=GOL_ORDER_NO OR ISNULL(GOL_ORDER_NO))".
					($lead_code!=""?" AND GLH_LEAD_CODE='$lead_code'":"");
	return $sql_outlet_dtl;
}
/**
 * @param int $complaint_id
 * @param string $tasklist
 * @param int $status
 *
 * @return void
 */
function update_ms_current_status($complaint_id,$tasklist,$status){
	$sql_update	=	" update  gft_cust_imp_task_current_status_dtl  set GITC_WORKED_DURATION='1', GITC_STATUS=$status,".
			" GITC_REMARKS='Go Live Activity' where GITC_COMPLAINT_ID='$complaint_id' and GITC_TASK_ID IN ($tasklist) ";
	execute_my_query($sql_update);
}
/**
 * @param int $complaint_id
 * @param string $tasklist
 * @param int $status
 *
 * @return void
 */
function insert_ms_current_status($complaint_id,$tasklist,$status){
	$schedule_date  = date("Y-m-d");
	$sql_update	=	"insert into gft_cust_imp_task_current_status_dtl " .
			" (GITC_COMPLAINT_ID,GITC_MS_ID,GITC_TASK_ID,GITC_DATE,GITC_ACTUAL_DURATION_MINS,GITC_WORKED_DURATION,GITC_STATUS)" .
			" (select $complaint_id,3,GITD_TASK_ID, '".$schedule_date."', GITD_DURATION_MINS,0,$status".
			" from gft_pcs_impl_template_task_dtl" .
			" join gft_impl_task_master on (GITD_TASK_ID=GIT_TASK_ID) " .
			" where 1 AND GITD_TASK_ID in($tasklist) ".
			" and GIT_MS_ID in(3) group by GITD_TASK_ID )";
	execute_my_query($sql_update);
}
/**
 * @param int $product_delivery_id
 * @param string $goliveStatus
 * @param int $complaint_id
 * @param string $lead_code
 * @param int $employeeId
 * @param string $my_comments
 * @param string $customer_comments
 * @param int $time_spent
 * @param string $pcode
 * @param string $pskew
 * @param string $signed_off
 *
 * @return void
 */
function update_go_live_training_dtl($product_delivery_id,$goliveStatus,&$complaint_id,$lead_code,$employeeId,$my_comments,$customer_comments,$time_spent,$pcode,$pskew,$signed_off){
	$order_no = get_single_value_from_single_table("GPD_ORDER_NO", "gft_product_delivery_hdr", "GPD_ID", $product_delivery_id);
	$support_status		=	'T6';
	$ms_task_current_status=2;
	$outlet_golive_status=1;
	if($goliveStatus=='Yes'){
		$support_status	=	'T0';
		$ms_task_current_status=5;
		$outlet_golive_status=2;
	}
	$schedule_date  = date("Y-m-d");
	$cust_vertical =  get_single_value_from_single_table("GLH_VERTICAL_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", "$lead_code");
	$sql_query		=	" select GROUP_CONCAT(GIT_TASK_ID) GIT_TASK_ID from gft_pcs_impl_template_task_dtl ".
			" join gft_impl_task_master on (GITD_TASK_ID=GIT_TASK_ID) ".
			" where GIT_MS_ID in(3) AND GIT_TASK_ID NOT IN($signed_off)  and GITD_VERTICAL_ID=0 AND GITD_PRODUCT_CODE_SKEW='$pcode-$pskew' and GIS_STATUS='A'".
			($pcode=='500' ?" and ( GITD_VERTICAL_ID=0 or GITD_VERTICAL_ID=$cust_vertical) " :" and GITD_VERTICAL_ID=0 ").
			" GROUP BY GIT_MS_ID ";
	$result_task_list = execute_my_query($sql_query);
	$not_signed_off="";
	if($notsignoff_row=mysqli_fetch_array($result_task_list)){
		$not_signed_off=$notsignoff_row['GIT_TASK_ID'];
	}
	if($complaint_id>0){
		insert_support_entry((int)$lead_code,0,0,'','',$employeeId,'42',"$my_comments",
				1,$support_status,date('Y-m-d H:i:s'),'0',$employeeId,null,'4',null,false,'',"$my_comments",$complaint_id);
		$last_activity_id	=	get_last_activity_id_of_support($complaint_id);
		if($signed_off!=""){
			update_ms_current_status($complaint_id,$signed_off,5);
		}
		/* if($not_signed_off!=''){
			update_ms_current_status($complaint_id,$not_signed_off,2);
		} */
		execute_my_query(" update gft_cust_imp_ms_current_status_dtl set GIMC_STATUS='$ms_task_current_status',GIMC_APPROVAL='P',".
				" GIMC_SESSION_1_CDATE='$schedule_date', GIMC_STARTING_ACTIVITY_ID='$last_activity_id', ".
				" GIMC_WORKED_DURATION=GIMC_WORKED_DURATION+$time_spent " .
				" where GIMC_COMPLAINT_ID=$complaint_id and GIMC_MS_ID='3'");
		execute_my_query("insert into gft_cust_imp_training_ms_log (GCM_ACTIVITY_ID, GCM_MS_ID, GCM_DATE, GCM_DURATION, GCM_STATUS) " .
				"value ($last_activity_id,3, date(now()),(select ifnull(sum(GCT_DURATION),0) " .
				" from gft_cust_imp_training_task_log where GCT_ACTIVITY_ID=$last_activity_id and GCT_MS_ID=3),5) ");
	}else{
		$complaint_id	=	insert_support_entry((int)$lead_code,(int)$pcode,substr($pskew,0,4),'','',$employeeId,'8',"Go Live",1,$support_status,$schedule_date,'0',$employeeId,null,'4',null,true);
		$last_activity_id	=	get_last_activity_id_of_support($complaint_id);
		execute_my_query(" insert into gft_cust_imp_ms_current_status_dtl (GIMC_COMPLAINT_ID,GIMC_MS_ID,GIMD_ACTUAL_DURATION_MINS,GIMC_WORKED_DURATION,GIMC_STATUS,GIMC_OPCODE,GIMC_REFOPCODE,GIMC_PD_REF_ID)" .
				" values ($complaint_id,3,'0',0,'$ms_task_current_status','','','')");
		execute_my_query("insert into gft_cust_imp_training_ms_log (GCM_ACTIVITY_ID, GCM_MS_ID, GCM_DATE, GCM_DURATION, GCM_STATUS) " .
				"value ($last_activity_id,3,'".$schedule_date."',0,2) ");
		if($signed_off!=""){
			insert_ms_current_status($complaint_id,$signed_off,5);
		}
		if($not_signed_off!=''){
			insert_ms_current_status($complaint_id,$not_signed_off,2);
		}
	}
	execute_my_query("insert into gft_cust_imp_training_task_log (GCT_ACTIVITY_ID, GCT_MS_ID, GCT_TASK_ID, GCT_DATE, GCT_DURATION, GCT_STATUS,GCT_REMARKS)  " .
			"(select $last_activity_id,GITC_MS_ID,GITC_TASK_ID,GITC_DATE,0,GITC_STATUS,'' from gft_cust_imp_task_current_status_dtl where GITC_COMPLAINT_ID =$complaint_id and GITC_MS_ID=3 AND GITC_TASK_ID in($signed_off) )");
	$audit_hdr=array(
			'GAH_LEAD_CODE'=>$lead_code,
			'GAH_DATE_TIME'=>date('Y-m-d H:i:s'),
			'GAH_CUSTOMER_COMMENTS'=>$customer_comments,
			'GAH_MY_COMMENTS'=>$my_comments,
			'GAH_AUDIT_BY'=>$employeeId,
			'GAH_OPCODE'=>"$product_delivery_id",
			'GAH_REFFERNCE_ORDER_NO'=>"$product_delivery_id",
			'GAH_PENDING_IMP'=>$goliveStatus,
			'GAH_TRAINING_STATUS'=>0,
			'GAH_ORDER_NO'=>$order_no,
			'GAH_AUDIT_TYPE'=>25
	);
	execute_my_query("UPDATE gft_outlet_lead_code_mapping set GOL_DELIVERY_GOLIVE_STATUS='$outlet_golive_status',GOL_DELIVERY_ID='$product_delivery_id',".
			"GOL_GOLIVE_MILESTONE_ID='$complaint_id'  where GOL_CUST_ID='$lead_code' AND GOL_ORDER_NO='$order_no'");
	$audit_id=/*. (string) .*/update_audit_details($audit_hdr,null,null);
	update_product_delivery_status($product_delivery_id,15,$time_spent,"golive");
}
/**
 * @param string $order_no
 * @param string $lead_code
 * @param string $is_split
 *
 * @return mixed[]
 */
function get_order_details_with_quantity($order_no, $lead_code,$is_split){
	$product_list_array	=	array();
	$product_query 	=	" select GPM_SKEW_DESC, GPM_DISPLAY_NAME, GOP_QTY from gft_order_hdr ".
			" inner join gft_order_product_dtl ON (gop_order_no=god_order_no) ".
			" inner join gft_product_master ON(GOP_PRODUCT_CODE=GPM_PRODUCT_CODE AND GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" where god_order_no='$order_no' AND GOD_LEAD_CODE='$lead_code'".
			" and (concat(GOP_PRODUCT_CODE,'-',GOP_PRODUCT_SKEW) not in (select concat(GSK_PRODUCT_CODE,'-',GSK_PRODUCT_SKEW) from gft_skew_kit_master))";
	if($is_split==1){
		$product_query 	=	" select GPM_SKEW_DESC, GPM_DISPLAY_NAME, GCO_CUST_QTY as GOP_QTY from gft_order_hdr ".
				" inner join gft_cp_order_dtl ON (GCO_ORDER_NO=god_order_no) ".
				" inner join gft_product_master ON(GCO_PRODUCT_CODE=GPM_PRODUCT_CODE AND GCO_SKEW=GPM_PRODUCT_SKEW) ".
				" where god_order_no='$order_no' AND GCO_CUST_CODE='$lead_code'".
				" and (concat(GCO_PRODUCT_CODE,'-',GCO_SKEW) not in (select concat(GSK_PRODUCT_CODE,'-',GSK_PRODUCT_SKEW) from gft_skew_kit_master))";
	}
	$row_product	=	execute_my_query($product_query);
	while($row=mysqli_fetch_array($row_product)){
		$product_list_array[]=$row['GPM_SKEW_DESC']." - (".$row['GOP_QTY'].")qty";
	}
	return $product_list_array;
}
/**
 * @param string $migrationrequired
 * @param string $product_delivery_id
 *
 * @return mixed[]
 */
function get_order_instructions_to_pc($migrationrequired,$product_delivery_id){
	$order_ins = array();
	if($migrationrequired==1){
		$order_ins[]	=	"Migration Required";
	}
	$result	=	execute_my_query("select  GAD_AUDIT_ANS,GAD_AUDIT_QID from gft_audit_hdr ".
			" INNER JOIN gft_audit_dtl ON(GAH_AUDIT_ID=GAD_AUDIT_ID)".
			" where GAH_REFFERNCE_ORDER_NO='$product_delivery_id' AND GAH_AUDIT_TYPE=20 AND".
			" GAD_AUDIT_QID IN(305,304) AND GAD_AUDIT_ANS!='' GROUP BY GAH_REFFERNCE_ORDER_NO,GAD_AUDIT_QID" );
	while(($row=mysqli_fetch_array($result)) && $product_delivery_id>0){
		if(trim($row['GAD_AUDIT_ANS'])!='' && $row['GAD_AUDIT_QID']=='305'){
			$order_ins[]	=	"Customer Pain Points: ".$row['GAD_AUDIT_ANS'];
		}
		if(trim($row['GAD_AUDIT_ANS'])!='' && $row['GAD_AUDIT_QID']=='304'){
			$order_ins[]	=	"Go-Live Date: ".$row['GAD_AUDIT_ANS'];
		}
	}
	return $order_ins;
}
/**
 *
 * @return string
 */
function get_query_for_pd_recording_tool(){
	$sql_pd_tool = 	" select GPD_LEAD_CODE,GLH_CUST_NAME,GPD_TRAINING_ID,MIN(GPD_STATUS_ON) RUN_ON, GEM_EMP_NAME, ".
			" GROUP_CONCAT(GPD_STATUS_ID  ORDER BY GPD_ID ASC SEPARATOR '**-**' ) GPD_STATUS_ID, ".
			" GROUP_CONCAT(GPD_STATUS_ON ORDER BY GPD_ID ASC SEPARATOR '**-**' ) GPD_STATUS_ON  from gft_product_delivery_tool_log".
			" INNER JOIN gft_lead_hdr lh ON(lh.GLH_LEAD_CODE=GPD_LEAD_CODE)".
			" INNER JOIN gft_emp_master em ON(em.GEM_EMP_ID=GPD_UPDATED_BY)".
			" WHERE (1)";
	return $sql_pd_tool;
}
/**
 * @param string $order_no
 *
 * @return int
 */
function get_total_coupon_for_store_order($order_no){
	$no_of_coupon = 0;
	$result = execute_my_query("select (GPM_COUPON_FOR_LOCAL*GOP_QTY) total_coupon from gft_order_product_dtl ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW)".
			" where GOP_ORDER_NO='$order_no'");
	while($row=mysqli_fetch_array($result)){
		$no_of_coupon  += (int)$row['total_coupon'];
	}
	return $no_of_coupon;
}
/**
 * @param string $uid
 * @param string $lead_code
 * @param string $order_no
 * @param string $product_consultant
 * @param string $required_coupon
 * @param string[string] $qid
 * @param string[string] $qidans
 * @param string $order_header_emp_com
 * @param string $emp_con
 *
 * @return void
 */
function get_query_for_store_order_product_list($uid, $lead_code, $order_no,$product_consultant, $required_coupon, $qid, $qidans, $order_header_emp_com='',$emp_con="" ){
	$sql_order_dtl	=	" select god_emp_id,oh.GOD_ORDER_DATE as order_date, oh.GOD_ORDER_NO as order_no,oh.GOD_STORE_EMP,pfm.GPM_PRODUCT_ABR as product ,opd.GOP_PRODUCT_SKEW as skew, ".
			"  opd.GOP_QTY as qty, if(GOD_PD_EXPENSE_TYPE=1,GPM_COUPON_FOR_LOCAL,if(GOD_PD_EXPENSE_TYPE=2,GPM_COUPON_FOR_EXSTATION,if(GOD_PD_EXPENSE_TYPE=3,GPM_COUPON_FOR_OUTSTATION,if(GOD_PD_EXPENSE_TYPE=4,GPM_COUPON_FOR_ONLINE,if(GOD_PD_EXPENSE_TYPE=5,GPM_COUPON_FOR_PCS,GPM_COUPON_FOR_LOCAL))))) as GPM_COUPONS,GPM_DISPLAY_NAME,GOP_PRODUCT_CODE,GOP_FULLFILLMENT_NO, ".
			" GPM_TRAINING_HRS,GOP_COUPON_HOUR,GOD_PD_EXPENSE_TYPE from gft_order_hdr oh join gft_order_product_dtl opd on  ".
			" (oh.god_order_no=opd.gop_order_no $order_header_emp_com) join gft_product_family_master pfm on (opd.gop_product_code=pfm.gpm_product_code) ".
			" join gft_product_master pm on (pfm.gpm_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew) join gft_product_group_master ".
			" pg on (pm.gpm_product_skew like concat(pg.gpg_skew,'%') and gpg_product_family_code=GPM_HEAD_FAMILY) join gft_lead_hdr lh on  ".
			" (god_lead_code=GLH_LEAD_CODE ) inner join gft_skew_property_master spm on (GSPM_CODE=GFT_SKEW_PROPERTY) join gft_credit_note_status_master on ".
			" (GOD_CREDIT_NOTE_STATUS = GCN_ID) left join gft_product_type_master ptm on (pm.GPM_PRODUCT_TYPE=ptm.GPT_TYPE_ID) ".
			" join gft_lead_type_master lt on (GLD_TYPE_CODE=lh.GLH_LEAD_TYPE)  where (1) ".
			" and glh_lead_code='$lead_code' and GOD_ORDER_NO='$order_no' and glh_lead_type!='8' order by GFT_SKEW_PROPERTY";
	$res_order_dtl	=	execute_my_query($sql_order_dtl);
	$coupon_nos	=	generate_ecoupon($uid,$required_coupon);
	mysqli_data_seek($res_order_dtl,0);
	$main_opcode='';
	$coupon_index=0;
	$splict_order=get_single_value_from_single_table("god_order_splict", "gft_order_hdr", "god_order_no", $order_no);
	$lead_type = get_lead_type_for_lead_code($lead_code);
	if($lead_type==3){
		$splict_order=1;
	}
	$GOD_ORDER_DATE = (isset($qidans[115])?$qidans[115]:(date('Y-m-d')));
	$audit_hdr=/*. (string[string]) .*/ array(
			'GAH_LEAD_CODE'=>''.$lead_code,
			'GAH_DATE_TIME'=>date('Y-m-d H:i:s'),
			'GAH_PENDING_IMP'=>"",
			'GAH_REQUIRED_TRAINING_DATE'=>(isset($qidans[115])?$qidans[115]:''),
			'GAH_MY_COMMENTS'=>mysqli_real_escape_string_wrapper("Coupon mapping for store order"),
			'GAH_AUDIT_TYPE'=>'20',
			'GAH_AUDIT_BY'=>$uid,
			'GAH_TRAINING_STATUS'=>'0',
			'GAH_ORDER_NO'=>$order_no);
	execute_my_query("update gft_order_hdr SET GOD_IMPL_REQUIRED='Yes',GOD_PDCM_EMP_ID=$uid where god_order_no='$order_no'");
	execute_my_query("UPDATE gft_lead_hdr SET GLH_FIELD_INCHARGE='$product_consultant' where GLH_LEAD_CODE=$lead_code");
	while($row1=mysqli_fetch_array($res_order_dtl)){
		$pcode=$row1['GOP_PRODUCT_CODE'];
		$pskew=$row1['skew'];
		$pfullfill=$row1['GOP_FULLFILLMENT_NO'];
		$coupon	=	((int)$row1['GPM_COUPONS'])*((int)$row1['qty']);
		if($coupon>0){
			if($main_opcode==''){
				$main_opcode=$order_no.$pcode.$pskew.$pfullfill;
			}
			$opcode=$order_no.$pcode.$pskew.$pfullfill;
			$audit_hdr['GAH_REFFERNCE_ORDER_NO'] = $main_opcode;
			$audit_hdr['GAH_OPCODE'] =$opcode;
			$gop_hour_coupon=(int)$row1['GOP_COUPON_HOUR'];
			$total_training_hrs=((int)$row1['GPM_TRAINING_HRS'])*((int)$row1['qty']);
			if($gop_hour_coupon<1){
				$gop_hour_coupon	= ($total_training_hrs/$coupon);
			}
			for($j=0;$j<$coupon;$j++){
				$new_coupon	=	isset($coupon_nos[$coupon_index])?$coupon_nos[$coupon_index]:'';$coupon_index++;
				if($new_coupon!=''){
					execute_my_query("UPDATE gft_coupon_distribution_dtl SET GCD_COUPON_HOURS='$gop_hour_coupon',GCD_REF_ORDER_NO='$opcode',".
							"GCD_ORDER_NO=".($splict_order==0?"'{$main_opcode}'":"''").", GCD_HANDLED_BY=$uid, GCD_DISTRIBUTE_FOR='C', GCD_TO_ID=$lead_code,".
							" GCD_GIVEN_DATE='$GOD_ORDER_DATE', GCD_SPLITABLE='".($splict_order?1:0)."' WHERE GCD_COUPON_NO=$new_coupon");
				}
			}
			update_audit_details($audit_hdr,$qid,$qidans);
		}
	}
	$cust_dtl=customerContactDetail($lead_code);
	$emp_ids=array();
	array_push($emp_ids,$uid);
	if($cust_dtl['LFD_EMP_ID']!=SALES_DUMMY_ID and $cust_dtl['LFD_EMP_ID']!=$uid){
		array_push($emp_ids,$cust_dtl['LFD_EMP_ID']);
	}
	if($cust_dtl['Field_incharge']!=SALES_DUMMY_ID and $cust_dtl['Field_incharge']!=$uid ){
		array_push($emp_ids,$cust_dtl['Field_incharge']);
	}
	if($cust_dtl['Reg_incharge']!=SALES_DUMMY_ID and $cust_dtl['Reg_incharge']!=$uid ){
		array_push($emp_ids,$cust_dtl['Reg_incharge']);
	}
	$help_us_help_you	=	get_samee_const("Help_Us_Help_You");
	$sys_requirement	=	get_system_requirement_link($order_no);
	$emplouee_dtl=get_emp_master($uid,'',null,false);
	$mail_content_config=array();
	$mail_content_config['Order_No']=array($order_no);
	$mail_content_config['Customer_Name']=array($cust_dtl['cust_name']);
	$mail_content_config['Customer_Mobile']=array($cust_dtl['mobile_no']);
	$mail_content_config['Customer_Id']=array($lead_code);
	$mail_content_config['Employee_Name']=array($emplouee_dtl[0][1]);
	$mail_content_config['Employee_Role']=array($emplouee_dtl[0][7]);
	$mail_content_config['Employee_Cell']=array($emplouee_dtl[0][3]);
	$mail_content_config['Employee_Mail']=array($emplouee_dtl[0][4]);
	$mail_content_config['Help_Us_Help_You']=array($help_us_help_you);
	$mail_content_config['System_Requirement']=array($sys_requirement);
	$mail_content_config_cust=array();
	$mail_content_config_cust=$mail_content_config;
	$mail_content_config['KYC']=array(get_Order_Question_Ans($qidans,'E',$coupon_nos));
	$mail_content_config_cust['KYC']=array(get_Order_Question_Ans($qidans,'C',$coupon_nos));

	send_formatted_mail_content($mail_content_config,67,147,$emp_ids,array($lead_code)); // Mail Send  to Employee
	send_formatted_mail_content($mail_content_config_cust,66,82,$emp_ids,array($lead_code)); // Mail Send to Customer
}
/**
 * @param string $order_no
 * @param int $number_of_coupon
 * @param int $pd_expense_type
 * @param string $lead_code
 * @param string $pcode
 * @param string $pskew
 * 
 * @return void
 */
function generate_product_delivery($order_no, $number_of_coupon, $pd_expense_type, $lead_code,$pcode,$pskew){
	$coupon_no	=	generate_ecoupon(PATANJALI_ID, $number_of_coupon);
	$cust_dtl=customerContactDetail($lead_code);
	$emp_id = PATANJALI_ID;
	$date_now = date('Y-m-d H:i:s');
	$audit_hdr=/*. (string[string]) .*/ array(
			'GAH_LEAD_CODE'=>"$lead_code",
			'GAH_DATE_TIME'=>$date_now,
			'GAH_REQUIRED_TRAINING_DATE'=>$date_now,
			'GAH_MY_COMMENTS'=>"Delivery assigned for Patajali customer",
			'GAH_AUDIT_TYPE'=>'24',
			'GAH_AUDIT_BY'=>PATANJALI_ID,
			'GAH_TRAINING_STATUS'=>'0',
			'GAH_L1_INCHARGE'=>$cust_dtl['Reg_incharge'],
			'GAH_FIELD_INCHARGE'=>$cust_dtl['Field_incharge'],
			'GAH_PENDING_IMP'=>'Yes',		
			'GAH_ORDER_AUDIT_STATUS'=>'Y',
			'GAH_I_ASSURE_STATUS'=>'Y',			
			'GAH_ORDER_NO'=>$order_no);
	if($coupon_no!=null and count($coupon_no)>0){
		$result=execute_my_query(" select if(substring(GOP_PRODUCT_SKEW,-2)='ST',substring(GOP_PRODUCT_SKEW,-2),1) as skews,".
				"(GOP_QTY*if($pd_expense_type=1,GPM_COUPON_FOR_LOCAL,if($pd_expense_type=2,GPM_COUPON_FOR_EXSTATION,if($pd_expense_type=3,GPM_COUPON_FOR_OUTSTATION,if($pd_expense_type=4,GPM_COUPON_FOR_ONLINE,if($pd_expense_type=5,GPM_COUPON_FOR_PCS,GPM_COUPONS)))))) as no_ofcoupons,".
				" concat(GOP_ORDER_NO, GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GOP_FULLFILLMENT_NO) opcode,".
				" GOP_COUPON_HOUR,(GOP_QTY*if($pd_expense_type=5,GPM_TRAINING_PCS_HRS,GPM_TRAINING_HRS)) GPM_TRAINING_HRS from gft_order_product_dtl " .
				" left join gft_product_master pm on (pm.gpm_product_code=gop_product_code and GOP_PRODUCT_SKEW=pm.GPM_PRODUCT_SKEW) " .
				" left join gft_product_family_master pf on(pf.gpm_product_code=pm.GPM_PRODUCT_CODE)".
				" WHERE GOP_ORDER_NO='$order_no' AND GPM_COUPONS>0 order by GPM_CATEGORY,  GFT_SKEW_PROPERTY ASC, no_ofcoupons desc ");
		$opcodess='';
		$ref_opcode='';
		$cno=0;
		while($data_coupon=mysqli_fetch_array($result)){
			$opcodes='';
			if($data_coupon['skews'] == 'ST'){
				$opcodes = $data_coupon['opcode'];
			}
			if($opcodess!=''){
				$opcodes = $opcodess;
			}
			if($data_coupon['no_ofcoupons']!=''){
				$gop_hour_coupon=(int)$data_coupon['GOP_COUPON_HOUR'];
				$total_training_hrs=(int)$data_coupon['GPM_TRAINING_HRS'];
				if($gop_hour_coupon<1){
					$gop_hour_coupon	= ($total_training_hrs/(int)$data_coupon['no_ofcoupons']);
				}
				for($ci=0;$ci<(int)$data_coupon['no_ofcoupons'];$ci++,$cno++){
					if(isset($coupon_no[$cno]) and $coupon_no[$cno]!=0){
						if($opcodes!=''){$opcodess = $opcodes;$refopcode = $data_coupon['opcode'];}else{$opcodess = $data_coupon['opcode'];$refopcode = $data_coupon['opcode'];}
						execute_my_query("UPDATE gft_coupon_distribution_dtl SET GCD_COUPON_HOURS='$gop_hour_coupon', GCD_REF_ORDER_NO='$refopcode',GCD_ORDER_NO='$opcodess', GCD_HANDLED_BY=$emp_id,".
								" GCD_DISTRIBUTE_FOR='C', GCD_TO_ID=$lead_code, GCD_GIVEN_DATE='$date_now', GCD_SPLITABLE='0' WHERE GCD_COUPON_NO={$coupon_no[$cno]}");
						if($ref_opcode!=$refopcode){
							$audit_hdr["GAH_OPCODE"] = $refopcode;
							$audit_hdr["GAH_REFFERNCE_ORDER_NO"] = $opcodess;
							try{
								update_audit_details($audit_hdr,null,null);
							}catch (Exception $e){
								die("'Caught exception: ',". $e->getMessage());
							}
							$ref_opcode=$refopcode;
						}
					}
				}
			}
			update_iassure_details(PATANJALI_ID,$lead_code,$order_no,$opcodess,$pcode,$pskew,null,date('Y-m-d H:i:s'),'','','',0);
		}
	}
}
