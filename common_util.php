<?php
/*. require_module 'standard'; .*/
/*. require_module 'mysql'; .*/
/*. require_module 'hash'; .*/
/*. require_module 'curl'; .*/
/*. require_module 'pcre'; .*/
/*. require_module 'simplexml'; .*/
/*. require_module 'calendar'; .*/

/*. require_module 'extra'; .*/
require_once(__DIR__.'/invoiceProdDetails.php');
/*. forward void function generate_overall_total(string $total_query,string[int] $mysort,string[int] $report_link,string[int] $show_in_popup,string[] $value_arr_align,string[] $sec_field_arr); .*/
/*. forward string[int][int] function get_two_dimensinal_array_from_query(string $query,string $field1,string $field2,string $order_by=,string $def_name=,string $def_val=,string $order_type=); .*/
/*. forward string[int][int] function get_three_dimensinal_array_from_query(string $query,string $field1,string $field2,string $field3,string $order_by=, string $def_name=, string $def_val=); .*/
/*. forward string[int][int] function get_two_dimensinal_array_from_table(string $table_name,string $field1,string $field2,string $status_field=,string $status=,string $order_by=); .*/
/*. forward string function GetHundreds(int $MyNumber,int $Count); .*/
/*. forward string function generateinvoicepdf(int $invoice_id,string $content); .*/
/*. forward string[int][int] function get_emp_list_by_group_filter(int[int] $group_id_arr, boolean $return_query=, string $emp_status=); .*/
/*. forward string[int][string] function get_team_list(string $emp_id,boolean $return_count=,boolean $return_list=,string $status=,string $from_dt=,string $to_dt=); .*/
/*. forward string function GetDigit(int $Digit); .*/
/*. forward string[int][int] function get_emp_master(string $uid=,string $status=,string $roleid=,boolean $only_gft_emp=,boolean $select_any=,int $team_id=); .*/
/*. forward string function db_date_format(string $datei); .*/
/*. forward string[int][int] function get_extension_no_list(string $office_id,int $group,string $default_caption=,string $status=); .*/
/*. forward mixed function array_trim(mixed $array_a); .*/
/*. forward void function show_my_alert_msg(string $msg,int $goback=); .*/
/*. forward void function js_location_href_to(string $page); .*/
/*. forward void function print_dtable_header(string $table_title,string $tooltip=,string $tooltip_width=,boolean $old=,boolean $mail_link=,string $history_link=); .*/
/*. forward string function generatequotationpdf(string $quotation_no,string $content,int $version_no); .*/
/*. forward string[string] function get_formatted_mail_content(string[string][int] $db_sms_content_config,int $category,int $template_id); .*/
/*. forward string[int][int] function get_mobileno_reportingmaster(string $emp_id,boolean $reporting_masters=); .*/
/*. forward string[string] function customerContactDetail(string $lead_code,int $designation_code=,boolean $from_app=); .*/
/*. forward string[int] function get_customer_ts_number(string $lead_code); .*/
/*. forward string function get_duration_in_string(string $duration); .*/
/*. forward string[int] function get_productcode_info(string $code); .*/
/*. forward string[int][int] function get_emp_ableto_place_orders(string $uid=,string $status=); .*/
/*. forward mixed[string] function get_dtable_navigation_struct(int $cnt,int $records_per_page=); .*/
/*. forward string[int][int] function get_support_status_master(string $status_code=,string $sgroup_id=); .*/
/*. forward string function get_email_addr(string $uid); .*/
/*. forward boolean function send_formatted_mail_content(string[string][int] $db_content_config,int $category,int $mail_template_id,int[int] $employee_ids=,int[int] $customer_ids=,string[int] $tomail_ids=, string[int] $other_cc=, string[int] $cc_mail_ids=, string $reply_to=, string $from_emp_id=,string $from_mail_id=); .*/
/*. forward string function get_audit_mail_data(int $audit_id); .*/
/*. forward string[int][string] function get_detail_of_group(string $group_id); .*/
/*. forward string[string][int] function send_customer_details_to_exec(string $cust_id,string $act_id=); .*/
/*. forward string function get_vertical_name_for(string $vertical); .*/
/*. forward string function get_cp_incharge(string $emp_id=,string $cust_id=); .*/
/*. forward string function get_email_addr_customer(string $lead_code); .*/
/*. forward void function sms_xml_content_parser(string $result_xml,int $category=); .*/
/*. forward string[int][int] function get_contact_dtls_of_group(string[int] $group_arr); .*/
/*. forward string function get_emp_id_frm_orderno(string $orderno); .*/
/*. forward string function get_mobileno(string $emp_id); .*/
/*. forward string function adjustMobileNumber(string $mobileno); .*/
/*. forward string[int] function get_email_addr_reportingmaster(string $uid,boolean $reporting_masters=,int[int] $skip_emp_list=); .*/
/*. forward string function get_short_name(string $id); .*/
/*. forward string function get_valid_text(string $text); .*/
/*. forward string function get_sms_config_info(int $category); .*/
/*. forward string function local_date_format(string $datei); .*/
/*. forward string function get_name(string $id,boolean $include_partner_name=); .*/
/*. forward string function fix_combobox_with(string $id,string $name,string[int][int] $two_dim_value_arr,string $selected_value,string $tab_index=,string $default_value=,string $style=,boolean $add_opt_group=,string $event_function=,int $size=, string[int] $hidden_val_arr=,string $css_class_name=,boolean $is_reqd=,string $additional_attributes=); .*/
/*. forward string[int][int] function get_status(string $status=,boolean $select_any=,string $default_value=,string $group_filter=); .*/
/*. forward string[int] function get_status_master_from_group(string[int] $mgroup_id,boolean $not_check=); .*/
/*. forward string[int][int] function get_marketing_material_list(string $material_id, boolean $fromall=); .*/
/*. forward string[int][int] function get_complaint(string $status=,string $complaint_code=); .*/
/*. forward string function get_cp_name_info(string $lcode1); .*/
/*. forward string[int][int] function  get_print_profile_modul_list(string $parentVar); .*/
/*. forward string[int][int] function get_bh_report_list(string $bh_id=,boolean $bh_all_reports=,int $bh_tab_id=); .*/
/*. forward string[int][int] function get_financial_yr_list(boolean $upto_current=); .*/
/*. forward string[int][int] function get_list_product_family_group_master(string $activie=,string $product_code=,string $prod=,string $name_field=,string $wh_condition=); .*/
/*. forward string[int][int] function get_group_list(string $group_type=,string[int] $groups_arr=,boolean $inner_maintainence=,string $use_group_privilage_filter=,string $def_name= , string $def_val=); .*/
/*. forward string[int][int] function get_vertical_name(string $business_type=,boolean $select=,string $vertical_codes=,string $prod_sync_vertical=); .*/
/*. forward string[int][int] function get_lead_source(string $category=,string $lead_source_id=,string $with_default=); .*/
/*. forward string[int][int] function get_product_type(string $type=,boolean $add_fy=); .*/
/*. forward string[int][int] function get_lead_subtype(string $lead_type=); .*/
/*. forward string function get_current_financial_yr(int $month=,int $year=); .*/
/*. forward string[int][int] function get_lead_type(string[int] $category_types=,string $lead_type_id=); .*/
/*. forward string[int][int] function get_sms_status_master(boolean $any=); .*/
/*. forward string[int][int] function get_assign_status_master(); .*/
/*. forward string[int][int] function get_installation_status_list(); .*/
/*. forward string[int][int] function get_collection_status_list(); .*/
/*. forward string[int][int] function get_installation_status_agewiselist(); .*/
/*. forward string function get_terr_id(string $uid); .*/
/*. forward string[int][int] function get_order_approval_emplist(); .*/
/*. forward string[int][int] function get_activity_list(string $other_users=,boolean $next_visit_nature=,boolean $visit_nature=,
	boolean $sales_planning=,string $act_id=,string $activity_nature=,boolean $support_activity=); .*/
/*. forward string[int][int] function get_logical_operation_filter(); .*/
/*. forward int[int][int] function get_year_list(); .*/
/*. forward string[int][int] function get_month_name(); .*/
/*. forward string[int][int] function get_customer_status_list(string $pages=,string $reason_needed=); .*/
/*. forward string[int][int] function cheque_status_list(string $group_id=); .*/
/*. forward string function get_formatted_content(string[string][int] $db_sms_content_config, int $category); .*/
/*. forward string function get_purpose_of_the_split_order(string $order_no, string $product_code, string $lead_code); .*/
/*. forward string function get_coupon_expiry_information(string $order_no,string $lead_code=); .*/
/*. forward string function get_system_requirement_link(string $order_no); .*/
/*. forward boolean function exists_in_lead_hdr_ext(string $lead_code); .*/
/*. forward boolean function generate_gst_invoice(string $order_no,string $invoice_to,string $from_page=,string $invoice_date=,boolean $corporate_full_invoice=,boolean $monthly_invoice=); .*/
/*. forward void function generate_gst_invoice_pdf(int $invoice_id, string $order_no, boolean $addon_inv=); .*/
/*. forward void function insert_and_generate_invoice(InvoiceProdDetails $prod_dtl,string $franchise_lead_code=); .*/
/*. forward boolean function check_invoice_conditions_for_kit_orders(string $order_no); .*/
/*. forward boolean function generate_accounts_invoice(string $order_no, string $invoice_to,string $from_page=, string $invoice_date=, boolean $monthly_invoice=); .*/
/*. forward OrderProdDetails function get_order_prod_dtl_for_invoice(string $order_no); .*/
/*. forward InvoiceProdDetails function get_invoice_prod_dtl_for_order(string $split,string[string] $rown,InvoiceProdDetails $prod_dtl,OrderProdDetails $opd, string[string] $gst_arr,boolean $only_services);.*/
/*. forward int function update_audit_details(string[string] $audit_hdr,int[int] $qid,string[int] $qidans,string[int] $bq_id=); .*/
/*. forward void function update_dependent_order_dtl (string $order_no,string $cust_id,string $proforma_no=,string $install_id=,string $quotation_no=); .*/
/*. forward string[string] function get_jio_payment_config(); .*/
/*. forward void function update_order_license_given_dtl(string $lead_code, int $product_code,int $order_split=,string $ordered_cust_code=,string $install_id=,string $god_order_no=); .*/
/*. forward string function get_prod_dtl_query_for_invoice(int $split,string $order_no,boolean $for_cron,boolean $only_services,string $franchise_lead_code=); .*/
/*. forward void function update_support_ids_from_mantis(string[int][string] $dtl_arr); .*/
/*. forward void function outstanding_incentive (string $order_no_collection,string $emp_id,string $receipt_id); .*/
//require_once(__DIR__ ."/common_query_util.php");
require_once(__DIR__ ."/call_center/update_current_status_cc.php");
require_once(__DIR__ ."/sales_util.php");
require_once(__DIR__ ."/auth_util.php");
require_once(__DIR__ ."/access_util.php");
require_once(__DIR__ ."/send_mail_util.php");
require_once(__DIR__ ."/call_center/call_center_util.php");
require_once(__DIR__ ."/invoice_util.php");
require_once(__DIR__ ."/incentive_util.php");
require_once(__DIR__ ."/activity_util.php");
require_once(__DIR__ . "/classes/iActivityGroupingClass.php");

/**
 * @param string[string][int] $db_sms_content_config
 * @param int $category
 *
 * @return string
 */
function get_formatted_content($db_sms_content_config,$category){
	$content_format="";
	if($category!=0){
		$sql="SELECT GSC_CUSTOMIZED_CONTENT_INFO,GSC_STATUS FROM gft_sms_config where GSC_ID='$category'";
		$rs=execute_my_query($sql,'common_util.php',true,false,2);
		$row=mysqli_fetch_array($rs);
		$content_format=$row['GSC_CUSTOMIZED_CONTENT_INFO'];
		$isNotBlocked=$row['GSC_STATUS'];
		$max_char=get_samee_const('CONST_MAX_NO_CHARACTER_FOR_A_SMS');
		if($content_format!=""){
			$sql1="SELECT GCC_CODE_ARRAY_ID,GCC_CONTENT_ALIAS_ID FROM gft_content_config where GCC_SMS_CATEGORY='$category' " .
					" and GCC_CODE_ARRAY_ID in ('" . implode("','",array_keys($db_sms_content_config)). "')";
			$rs1=execute_my_query($sql1,'common_util.php',true,false,2);
			while($row1=mysqli_fetch_array($rs1)){
				$replace=$db_sms_content_config[$row1['GCC_CODE_ARRAY_ID']][0];
			 	$pattern='/{{'.$row1['GCC_CONTENT_ALIAS_ID'].'}}/i';
		        $content_format=preg_replace($pattern,"$replace",$content_format);
			}
		}
		$content_format=trim($content_format);
	}
	return $content_format;
}

/**
 * @param string $order_no
 * @param string $product_code
 * @param string $lead_code
 *
 * @return string
 */
function get_purpose_of_the_split_order($order_no,$product_code,$lead_code){
	$prupose='';
	$purpose_result=execute_my_query("select group_concat(GSPM_DESC) pro_desc from gft_cp_order_dtl opd " .
					" join gft_product_master pm on (opd.gco_product_code=pm.gpm_product_code and gco_skew=pm.gpm_product_skew) " .
					" join gft_skew_property_master on (GSPM_CODE=GFT_SKEW_PROPERTY and GFT_SKEW_PROPERTY in (22,21,20,18))" .
					" where gco_order_no='$order_no' and gpm_product_code=$product_code and gco_cust_code=$lead_code");
	if(mysqli_num_rows($purpose_result) >0 and $data_purpose=mysqli_fetch_array($purpose_result)){
		if($data_purpose['pro_desc']!=null){
			$prupose=$data_purpose['pro_desc'];
		}
	}
	return $prupose;			
}

/**
 * @param string $order_no
 * @param string $product_code
 *
 * @return string
 */
function get_purpose_of_the_order($order_no,$product_code){
	$prupose='';
	$purpose_result=execute_my_query("select group_concat(GSPM_STORE_DESC) pro_desc from gft_order_product_dtl opd " .
					" join gft_product_master pm on (opd.gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew) " .
					" join gft_skew_property_master on (GSPM_CODE=GFT_SKEW_PROPERTY and GFT_SKEW_PROPERTY in (22,21,20,18))" .
					" where gop_order_no='$order_no' and gpm_product_code=$product_code ");
	if($data_purpose=mysqli_fetch_array($purpose_result)){
		if($data_purpose['pro_desc']!=null){
			$prupose=$data_purpose['pro_desc'];
		}
	}
	return $prupose;			
}

/**
 * @param int $userid
 * 
 * @return boolean
 */
 function isPartnerEmployee($userid){
 	if($userid==''){
 		return false;
 	}
 	$sql_rc_details	=	"select lem.GLEM_EMP_ID, lem.GLEM_LEADCODE, ci.cgi_lead_code, ci.CGI_EMP_ID,em.gem_emp_name,ca.GCA_CP_SUB_TYPE,ls.GLS_SUBTYPE_NAME from gft_leadcode_emp_map lem ".
								" inner join gft_cp_info as ci ON(lem.GLEM_LEADCODE=ci.CGI_LEAD_CODE) ".
								" inner join gft_emp_master AS em ON(em.gem_emp_id=ci.CGI_EMP_ID) ".
								" inner join gft_cp_agree_dtl as ca ON(ca.gca_lead_code=ci.CGI_LEAD_CODE) ".
								" inner join gft_lead_subtype_master as ls ON(ls.GLS_SUBTYPE_CODE=ca.GCA_CP_SUB_TYPE) ".
								" where GLEM_EMP_ID=".$userid;
	$result_rc_details=execute_my_query($sql_rc_details);
	if(mysqli_num_rows($result_rc_details)>0){
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
 function isPartnerLeadCode($lead_code){
 	$sql_check_partner	=	" SELECT CGI_EMP_ID FROM gft_cp_info join gft_lead_hdr on (CGI_LEAD_CODE=GLH_LEAD_CODE) ".
 							" WHERE CGI_LEAD_CODE='$lead_code' and GLH_LEAD_TYPE='2'";
 	$res_check_partner 	= 	execute_my_query($sql_check_partner);
 	if(mysqli_num_rows($res_check_partner)!=0){
 		return 1;
 	}
 	return 0;
 }
/**
 * @param string $lead_code
 * @param int $install_id
 * 
 * @return string
 */
 function get_query_of_customer_installation($lead_code, $install_id=0){
     $query_check_duplicate = "select GLH_LEAD_CODE, GLH_STATUS, GLH_VERTICAL_CODE,GTM_BUSINESS_TYPE, GPM_FREE_EDITION, ".
         "  GID_VALIDITY_DATE, GID_STATUS, GID_LIC_PCODE, SUBSTRING(GID_LIC_PSKEW, 1,4) GID_LIC_PSKEW from gft_lead_hdr ".
         "  INNER JOIN gft_vertical_master vm ON(GLH_VERTICAL_CODE=GTM_VERTICAL_CODE)  ".
         "  LEFT JOIN gft_install_dtl_new ON(GLH_LEAD_CODE=GID_LEAD_CODE AND GID_LIC_PCODE IN('500','200','601','605')) ".
         "  LEFT JOIN gft_product_family_master pf ON(GID_LIC_PCODE=pf.GPM_PRODUCT_CODE AND GPM_IS_BASE_PRODUCT='Y')  ".
         "  LEFT JOIN gft_product_master pm ON(pm.GPM_PRODUCT_CODE=pf.GPM_PRODUCT_CODE  AND GID_LIC_PSKEW=GPM_PRODUCT_SKEW) ".
         "  where glh_lead_code IN($lead_code) AND GID_STATUS='A' ".
         ($install_id>0?" AND GID_INSTALL_ID != '$install_id'":"").
         " order by GLH_LEAD_CODE ASC, CAST(GPM_FREE_EDITION AS CHAR) ASC";
     
     return $query_check_duplicate;
 }
/**
 * @param int $userid
 *
 * @return string[string]
 */
function get_partner_emp_id($userid){
    $return_array    =    /*.(string[string]).*/array();
    $sql_rc_details    =    "select lem.GLEM_EMP_ID, lem.GLEM_LEADCODE, ci.cgi_lead_code, ci.CGI_EMP_ID,em.gem_emp_name,ca.GCA_CP_SUB_TYPE,ls.GLS_SUBTYPE_NAME, ".
      		" em.GEM_EMAIL,bi.GEM_EMAIL bi_email from gft_leadcode_emp_map lem ".
            " inner join gft_cp_info as ci ON(lem.GLEM_LEADCODE=ci.CGI_LEAD_CODE) ".
            " inner join gft_emp_master em ON(em.gem_emp_id=ci.CGI_EMP_ID) ".
            " left join gft_emp_master bi on (bi.gem_emp_id=ci.cgi_incharge_emp_id) ".
            " inner join gft_cp_agree_dtl as ca ON(ca.gca_lead_code=ci.CGI_LEAD_CODE) ".
            " inner join gft_lead_subtype_master as ls ON(ls.GLS_SUBTYPE_CODE=ca.GCA_CP_SUB_TYPE) ".
            " where GLEM_EMP_ID=".$userid;
    $result_rc_details=execute_my_query($sql_rc_details);
    if(mysqli_num_rows($result_rc_details)>0 and $row=mysqli_fetch_array($result_rc_details)){
        $return_array['partner_id']    		=	$row['CGI_EMP_ID'];
        $return_array['partner_name']    	=	$row['gem_emp_name'];
        $return_array['partner_lead_code']  =	$row['cgi_lead_code'];
        $return_array['partner_email']		=	$row['GEM_EMAIL'];
        $return_array['incharge_email']		=	$row['bi_email'];
        return $return_array;
    }else{
        return $return_array;
    }

} 
 /**
  * @param string $lead_code
  * 
  * @return string
  */
 function get_partner_balance_order($lead_code) {
 	$cp_query = " select CGI_EMP_ID from gft_cp_info join gft_lead_hdr on (CGI_LEAD_CODE=GLH_LEAD_CODE) ".
 				" where CGI_LEAD_CODE='$lead_code' and GLH_LEAD_TYPE=2 ";
 	$cp_res = execute_my_query($cp_query);
 	if($cp_da = mysqli_fetch_array($cp_res)){
 		$cp_id = $cp_da['CGI_EMP_ID'];
 		$balance_order_no="000".substr("0000".$cp_id,-4).substr("00000000".$lead_code,-8);
 		return $balance_order_no;
 	}
 	return '';
 }
 
 /**
 * @param int $userid
 * 
 * @return string
 */
 function getEmployeeCompanyName($userid){
 	$company_name	=	'';
 	$sql_getcom_details	=	"select lem.GLEM_EMP_ID, lem.GLEM_LEADCODE, ci.cgi_lead_code, ci.CGI_EMP_ID,em.gem_emp_name,ca.GCA_CP_SUB_TYPE,ls.GLS_SUBTYPE_NAME from gft_leadcode_emp_map lem ".
								" inner join gft_cp_info as ci ON(lem.GLEM_LEADCODE=ci.CGI_LEAD_CODE) ".
								" inner join gft_emp_master AS em ON(em.gem_emp_id=ci.CGI_EMP_ID) ".
								" inner join gft_cp_agree_dtl as ca ON(ca.gca_lead_code=ci.CGI_LEAD_CODE) ".
								" inner join gft_lead_subtype_master as ls ON(ls.GLS_SUBTYPE_CODE=ca.GCA_CP_SUB_TYPE) ".
								" where GLEM_EMP_ID=".$userid;
	$result_getcom_details=execute_my_query($sql_getcom_details);
	if(mysqli_num_rows($result_getcom_details)==1){
		$row_com		=	mysqli_fetch_array($result_getcom_details);
		if($row_com['GLEM_EMP_ID']!=$row_com['CGI_EMP_ID']){
			$company_name	.=	"&nbsp;&nbsp;(".$row_com['gem_emp_name'].")";
		}else{
			$company_name	.=	"&nbsp;&nbsp;";
		}
	}
	return $company_name;
 }
  
 /**
 * @param int $userid
 * 
 * @return string
 */
 function getEmployeeCompanyType($userid){
 	$partner_type	=	'';
 	$sql_getcom_details	=	"select lem.GLEM_EMP_ID, lem.GLEM_LEADCODE, ci.cgi_lead_code, ci.CGI_EMP_ID,em.gem_emp_name,ca.GCA_CP_SUB_TYPE,ls.GLS_SUBTYPE_NAME from gft_leadcode_emp_map lem ".
								" inner join gft_cp_info as ci ON(lem.GLEM_LEADCODE=ci.CGI_LEAD_CODE) ".
								" inner join gft_emp_master AS em ON(em.gem_emp_id=ci.CGI_EMP_ID) ".
								" inner join gft_cp_agree_dtl as ca ON(ca.gca_lead_code=ci.CGI_LEAD_CODE) ".
								" inner join gft_lead_subtype_master as ls ON(ls.GLS_SUBTYPE_CODE=ca.GCA_CP_SUB_TYPE) ".
								" where GLEM_EMP_ID=".$userid;
	$result_getcom_details=execute_my_query($sql_getcom_details);
	if(mysqli_num_rows($result_getcom_details)==1){
		$row_comtype		=	mysqli_fetch_array($result_getcom_details);
		$partner_type	.=	"<br>Authorized GoFrugal ".$row_comtype['GLS_SUBTYPE_NAME']."";
	}
	return $partner_type;
 }
 
  /**
 * @param string $order_no
 * 
 * @return string[int]
 */
 function getPartnerIdBusinessManagerId($order_no=''){ 
 	$other_cc	=/*. (string[int]) .*/	array();
 	global $uid;
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
	$sql_rc_details	=	"select lem.GLEM_EMP_ID, lem.GLEM_LEADCODE, ci.cgi_lead_code, ci.CGI_EMP_ID,em.gem_emp_name,ca.GCA_CP_SUB_TYPE,ls.GLS_SUBTYPE_NAME from gft_leadcode_emp_map lem ".
						" inner join gft_cp_info as ci ON(lem.GLEM_LEADCODE=ci.CGI_LEAD_CODE) ".
						" inner join gft_emp_master AS em ON(em.gem_emp_id=ci.CGI_EMP_ID) ".
						" inner join gft_cp_agree_dtl as ca ON(ca.gca_lead_code=ci.CGI_LEAD_CODE) ".
						" inner join gft_lead_subtype_master as ls ON(ls.GLS_SUBTYPE_CODE=ca.GCA_CP_SUB_TYPE) ".
						" where GLEM_EMP_ID=".$uid;
	$result_rc_details=execute_my_query($sql_rc_details);
	if(mysqli_num_rows($result_rc_details)==1){
		$row_partner_emp	=	mysqli_fetch_array($result_rc_details);
		if($row_partner_emp['CGI_EMP_ID']!=''){
			array_push($other_cc,$row_partner_emp['CGI_EMP_ID']);	
		}
	}	
	return $other_cc;	
 }

 /**
  * @param string $partner_id
  * 
  * @return string
  */
 function get_partner_business_mgr($partner_id){
 	$mgr_id='';
 	$partner_dtl = check_is_partner_employee($partner_id);
 	if(isset($partner_dtl['partner_id'])) {
 		$partner_id = $partner_dtl['partner_id'];
 	}
 	$que =" select cgi_incharge_emp_id from gft_cp_info ".
 	 	  " join gft_emp_master on (cgi_incharge_emp_id=gem_emp_id) ".
 		  " where CGI_EMP_ID='$partner_id' and GEM_STATUS='A' ";
 	$res = execute_my_query($que);
 	if( $data=mysqli_fetch_array($res) ) {
 		$mgr_id = $data['cgi_incharge_emp_id'];
 	}
 	return $mgr_id;
 }
 
 /**
  * @param int $partner_id
  * @param boolean $partner_also
  *
  * @return string
  */
 function get_partner_business_mgr_mail_id($partner_id,$partner_also=false){
 	$mgr_email_id='';
 	$query= " select bmgr.gem_email as bm_email ,ptr.gem_email as pt_email from gft_cp_info ". 
 			" join gft_emp_master bmgr on (bmgr.GEM_EMP_ID = cgi_incharge_emp_id) ".
 			" join gft_emp_master ptr on (ptr.GEM_EMP_ID = CGI_EMP_ID) ".
 			" where CGI_EMP_ID='$partner_id'";
 	$res = execute_my_query($query);
 	if( $data=mysqli_fetch_array($res) ) {
 		$mgr_email_id = $data['bm_email'];
 		if($partner_also) {
 			$mgr_email_id = $mgr_email_id.','.$data['pt_email'];
 		}
 	}
 	return $mgr_email_id;
 }
 /**
  * @param string $orderno
  *
  * @return string
  */
 function get_order_approval_status($orderno){
 	$stat='';
 	$que_ord_st ="select GOD_ORDER_APPROVAL_STATUS from gft_order_hdr ".
 			" where GOD_ORDER_NO='$orderno'";
 	$result_ord_st = execute_my_query($que_ord_st);
 	if( $data=mysqli_fetch_array($result_ord_st) ) {
 		$stat = $data['GOD_ORDER_APPROVAL_STATUS'];
 	}
 	return $stat;
 }
 
 /**
  * @param string $order_no
  * @param string $fullfillment_no
  * 
  * @return string
  */
 function check_for_custom_and_uat_license($order_no,$fullfillment_no){
 	$sel_que = " select GOD_ORDER_NO,GOD_ORDER_SPLICT,GPM_PRODUCT_TYPE,GPM_UAT_LICENSE, ".
 				" if(GOD_ORDER_SPLICT=1,GCO_PRODUCT_CODE,GOP_PRODUCT_CODE) as product_code, ".
 				" if(GOD_ORDER_SPLICT=1,GCO_SKEW,GOP_PRODUCT_SKEW) as product_skew, ".
 				" if(GOD_ORDER_SPLICT=1,GCO_REFERENCE_ORDER_NO,GOP_REFERENCE_ORDER_NO) as ref_order_no, ".
 				" if(GOD_ORDER_SPLICT=1,GCO_REFERENCE_FULLFILLMENT_NO,GOP_REFERENCE_FULLFILLMENT_NO) as ref_fullfillment_no ".
 				" from gft_order_hdr ".
 				" left join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO ) ".
 				" left join gft_cp_order_dtl on (GOD_ORDER_NO=GCO_ORDER_NO and GCO_PRODUCT_CODE=GOP_PRODUCT_CODE and GCO_SKEW=GOP_PRODUCT_SKEW ) ".
 				" join gft_product_master on (GPM_PRODUCT_CODE=if(GOD_ORDER_SPLICT=1,GCO_PRODUCT_CODE,GOP_PRODUCT_CODE) and  ".
 				" GPM_PRODUCT_SKEW=if(GOD_ORDER_SPLICT=1,GCO_SKEW,GOP_PRODUCT_SKEW)) ".
 				" where GOD_ORDER_NO='$order_no' and if(GOD_ORDER_SPLICT=1,GCO_FULLFILLMENT_NO,GOP_FULLFILLMENT_NO)=$fullfillment_no and GFT_SKEW_PROPERTY in (1,11) ";
 	$res_que = execute_my_query($sel_que);
 	if(mysqli_num_rows($res_que)==1){
 		$data = mysqli_fetch_array($res_que);
 		$product_type = $data['GPM_PRODUCT_TYPE'].'-'.$data['ref_order_no'].'-'.$data['ref_fullfillment_no'].'-'.$data['GPM_UAT_LICENSE'];
 		return $product_type;
 	}
	return '';
 }
 
/**
 * @param string $email_id
 * @param int $pcode
 * 
 * @return bool
 */
function check_true_alert_user_name($email_id,$pcode){	
	$result_mail_check=execute_my_query("SELECT GSG_LEAD_CODE FROM gft_sms_gateway_info  where GSG_SMS_USERID='$email_id' AND (GSG_PRODUCT_CODE=$pcode) ");
	if(mysqli_num_rows($result_mail_check)!=0){
		return false;
	}
	return true;
}

/**
 * @param string $lead_code
 *
 * @return boolean
 */
function check_true_alert_register($lead_code){
        $result_mail_check=execute_my_query("select GSG_LEAD_CODE from gft_sms_gateway_info where GSG_LEAD_CODE='$lead_code' and GSG_PRODUCT_CODE=604 ");
        if(mysqli_num_rows($result_mail_check)!=0){
                return false;
        }
        return true;
}


/**
 * @param string $lead_code
 * @param string $order_no
 * @param string $product_code
 * @param string $product_skew
 * @param string $fulfilment_no
 * 
 * @return string[int]
 */

function get_install_id($lead_code,$order_no='',$product_code='',$product_skew='',$fulfilment_no=''){
	$result=execute_my_query("SELECT group_concat(GID_INSTALL_ID) install_id_array FROM gft_install_dtl_new g " .
			" WHERE GID_LEAD_CODE='$lead_code'  and GID_STATUS='A' ".			
			($order_no!=''?" and GID_LIC_ORDER_NO='$order_no' " :'').
			($product_code!=''? " and GID_LIC_PCODE='$product_code' ":""). 
			($product_skew!=''?" and GID_LIC_PSKEW like '$product_skew%' ":"").
			($fulfilment_no!='' ?" and GID_LIC_FULLFILLMENT_NO=$fulfilment_no ":"").
			" group by GID_LEAD_CODE ");
	if(mysqli_num_rows($result)==1){
		if($data=mysqli_fetch_array($result)){
			if($data['install_id_array']!=''){
				return explode(',',$data['install_id_array']);
			}else{
				return null;
			}
		}else{
			return null;
		}
	}else {
		return null;
	}
}

/**
 * @param string $install_id
 *
 * @return string
 */

function is_asa_cust($install_id){
	if($install_id!=''){
		$result=execute_my_query("SELECT GID_LIC_ORDER_NO, GID_LIC_PCODE, GID_LIC_PSKEW, GID_LIC_FULLFILLMENT_NO, GID_LEAD_CODE, GID_EXPIRE_FOR, GID_VALIDITY_DATE FROM gft_install_dtl_new g WHERE GID_INSTALL_ID=$install_id and GID_STATUS='A' ");
		if($data=mysqli_fetch_array($result)){
			if($data['GID_VALIDITY_DATE'] >= date('Y-m-d')){
				return 'Y'; 
			}else{
				$annuty_team=get_emp_list_by_group_filter(array(54));
				$annuty_team_a=/*. (string[int]) .*/ array();
				for($i=0;$i<count($annuty_team);$i++){
					$annuty_team_a[]=$annuty_team[$i][0];
				}
				$result1=execute_my_query("SELECT GPH_VALIDITY_DATE, GID_LEAD_CODE " .
					" FROM gft_install_dtl_new join gft_lead_hdr h on (GID_LEAD_CODE=GLH_LEAD_CODE) " .
					" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GID_LIC_PCODE and pm.GPM_REFERER_SKEW=GID_LIC_PSKEW and pm.GFT_SKEW_PROPERTY=4)" .
					" join gft_proforma_hdr on (GPH_EMP_ID in (".implode(',',$annuty_team_a).") and (GPH_LEAD_CODE=GLH_LEAD_CODE or (GLH_LEAD_SOURCECODE=7 and glh_reference_given=GLH_LEAD_CODE)) and date(now()) between date(GPH_ORDER_DATE) and GPH_VALIDITY_DATE) ".
					" join gft_proforma_product_dtl on (GPH_ORDER_NO=GPP_ORDER_NO and GPP_PRODUCT_CODE =GID_LIC_PCODE and pm.GPM_PRODUCT_SKEW=GPP_PRODUCT_SKEW)" .				
					" WHERE GID_VALIDITY_DATE < date(now()) and GID_INSTALL_ID=$install_id and GID_STATUS='A' and date(now()) between date(GPH_ORDER_DATE) and GPH_VALIDITY_DATE ". 
					" group by GLH_LEAD_CODE order by GPH_VALIDITY_DATE desc ");
				if($data1=mysqli_fetch_array($result1)){
					if($data1['GPH_VALIDITY_DATE']>= date('Y-m-d')){
						return 'E';
					}
				}
			}
		}
	}
	return 'N';
}

/**
 * @param string $username
 *
 * @return int
 */
function get_user_id_from_mail($username){
	$query= "SELECT GEM_EMP_ID FROM gft_emp_master where GEM_EMAIL like '$username@gofrugal.com'";
	$res=execute_my_query($query);
	$rows=mysqli_num_rows($res);
	if($rows==0){
		return 0;
	}else{
		$query_data=mysqli_fetch_array($res);
  		return (int) $query_data['GEM_EMP_ID'];
	}
}

/**
 * @param string $productcode
 * @param string $productskew
 * 
 * @return string
 */

function get_product_type_id($productcode,$productskew){
	$result=execute_my_query("select GPM_PRODUCT_TYPE from gft_product_master where GPM_PRODUCT_CODE='$productcode' and gpm_product_skew='$productskew'");
	if($data=mysqli_fetch_array($result)){
		return $data['GPM_PRODUCT_TYPE'];
	}else{
		return '';
	}
}

/**
 * @param string $ccid
 * @param string $campaign_id
 *
 * @return string[string]
 */
function get_cust_id_from_campaign_dtl($ccid,$campaign_id){
		$campaign_dtl=/*. (string[string]) .*/ array();
		if(is_numeric($ccid) and is_numeric($campaign_id)){		
			$query_ilead_code="select GER_LEAD_CODE,GCC_CONTACT_NO from gft_edm_read_dtl" .
					" inner join gft_customer_contact_dtl cc on (gcc_lead_code=ger_lead_code and gcc_id=ger_contact_id) " .
					" where GER_CONTACT_ID=$ccid AND GER_CAMPAIGN_ID=$campaign_id ";
			$result_ilead_code=execute_my_query($query_ilead_code);
			if(mysqli_num_rows($result_ilead_code)>0){
				$qd=mysqli_fetch_array($result_ilead_code);
				$campaign_dtl['LEAD_CODE']=$qd['GER_LEAD_CODE'];
				$campaign_dtl['EMAIL_ID']=$qd['GCC_CONTACT_NO'];			
			}
		}
		return $campaign_dtl;
}

/**
 * @param string $GCH_COMPLAINT_ID
 *
 * @return string
 */
function get_last_activity_id_of_support($GCH_COMPLAINT_ID){
	$query="select GCH_LAST_ACTIVITY_ID from gft_customer_support_hdr where GCH_COMPLAINT_ID=$GCH_COMPLAINT_ID ";
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)==1){
		$qdata=mysqli_fetch_array($result);
		$last_activity_id=$qdata['GCH_LAST_ACTIVITY_ID'];
		return $last_activity_id;
	}else{
		return null;
	} 
			
}


/**
 * @param string $username
 * 
 * @return int
 */
function get_user_id($username){
	$query= "select GLM_EMP_ID, GLM_LOGIN_NAME from gft_login_master where GLM_LOGIN_NAME='$username'";
	$res=execute_my_query($query);
	$rows=mysqli_num_rows($res);
	if($rows==0){
		return 0;
	}else{
		$query_data=mysqli_fetch_array($res);
  		return (int)$query_data['GLM_EMP_ID'];
	}
}

/**
 * @param int $month
 * @param int $year
 * @param string $all
 * @param int $id
 * @param string $doj
 * @param string $dor
 * @param string $emp_status
 *
 * @return int
 */
function calculate_workingdays($month,$year,$all,$id,$doj=null,$dor=null,$emp_status=null){
	$working_days=0;

	$curr_dt=0;

    $curr_month=(int)date('m');
	$curr_year=(int)date('Y');
	$hday=/*. (string[int]) .*/ array();
    if($month!=0 and $year!=0){
    	$fmonth=$month;
    	$fyear=$year;
    	$holiday="select date_format(ghl_date,'%e') as hdate,ghl_desc " .
    			" from gft_holiday_list where month(ghl_date)=$fmonth and year(ghl_date)='$fyear' " .
    			" and ghl_date>='$doj' ";
    	if($dor!='' and $dor!='0000-00-00'){
			$holiday.= " and ghl_date<='$dor' ";
    	}		
		$res_holiday=execute_my_query($holiday);
		$j=0;
		while($qdata=mysqli_fetch_array($res_holiday)){	  
			$dt=(int)$qdata[0];
			$hday[$dt]='y';
			$desc[$dt]=$qdata[1];
		}
		//$num=date('t',date("Y-m-d", mktime(0, 0, 0, $fmonth  , 1, $fyear )));
		$num=(int)date('t',mktime(0, 0, 0, $fmonth  , 1, $fyear ));
		$date_of_join_arr=explode('-',$doj);
		$month_join=(isset($date_of_join_arr[1])?(int)$date_of_join_arr[1]:0);
		$year_join=(isset($date_of_join_arr[2])?(int)$date_of_join_arr[2]:0);

		$start_date=1;
		//echo "<br>+++$curr_month==$fmonth and $curr_year==$fyear<br>";	
		
		if($fyear==(int)substr($doj,0,4) and $fmonth==(int)substr($doj,5,2))
		{
		  $start_date=(int)substr($doj,8,2);
		  $curr_dt=$num;
		}
		
		if($curr_month==$fmonth and $curr_year==$fyear)
		{
			
		$curr_dt=(int)date('d');
		$curr_dt=$curr_dt-1;
        //echo $curr_dt;
		}
		else
		{
		$curr_dt=$num;
		}
		
		if($emp_status=='I'){
			
			if($fyear==(int)substr($dor,0,4) and $fmonth==(int)substr($dor,5,2))
		   {
		     // $start_date=substr($doj,8,2);
		      $curr_dt=(int)substr($dor,8,2);
		      $curr_dt=(int)date('d',mktime(0,0,0,$fmonth,($curr_dt-1),$fyear));
		     //echo "releived month";
		   }
			
		}
		//echo "<br>$fyear==".substr($doj,0,4)." and $fmonth==".substr($doj,5,2)." <br>";
		
		
		$cnt_s=0;
		
		//echo "(*****)$i=$start_date;$i<=$curr_dt;$i++ <br>";
		$working_days=0;
		for($i=$start_date;$i<=$curr_dt;$i++){  
			$sun=(int)date("w", mktime(0, 0, 0, $fmonth, $i, $fyear));//calculating sundays
			$date= date('Y-m-d',mktime(0, 0, 0, $fmonth, $i, $fyear));
			if($sun==0){
				$sunday[$i]='y';
				$cnt_s++;
			}else if(isset($hday[$i]) and $hday[$i]=='y'){
				$cnt_s++;
			}else{
				$working_days++; 
			 }
		}	
    	return $working_days;
    }else if($month==0 and $year!=0){
    	$fmonth=$month;
    	$fyear=$year;
    	$curr_month=(int)date('m');
		$curr_year=(int)date('Y');
		$start_month=1;
		$upto_mn=12;
		if($curr_year==(int)substr($doj,0,4)){
		  $start_month=(int)substr($doj,5,2);	
		}
		if($emp_status=='I' and $curr_year==(int)substr($dor,0,4)){
        	$month_rel=(int)substr($dor,5,2);
           	$upto_mn=$month_rel;
        }else if($curr_year==$fyear){
			$upto_mn=$curr_month;
		}
        for($mn=$start_month;$mn<=$upto_mn;$mn++){
			$working_days+=calculate_workingdays($mn,$fyear,'false',$id,$doj,$dor,$emp_status);	
    	}
    	return $working_days;
    }else if($all=='true'){
		//echo "calculate for year";
		//$query_wk="select  em.gem_doj,em.gem_dor,em.gem_status from gft_emp_master em where em.gem_emp_id='$id'";
		//$result_wk=execute_my_query($query_wk);
		//$qd=mysqli_fetch_array($result_wk);
		//$emp_status=$qd[2];
        $date_of_join=$doj;
        $date_of_releve=$dor;
        $month_join=substr($date_of_join,5,2);
        $year_join=(int)substr($date_of_join,0,4);
        $upto_yr=$curr_year;
        $upto_month=$curr_month;
        if($emp_status=='I'){
        	$month_rel=(int)substr($date_of_releve,5,2);
        	$year_rel=(int)substr($date_of_releve,0,4);
        	$upto_yr=$year_rel;	
        	$upto_month=$month_rel;
        }
        for($i=$year_join;$i<=$upto_yr;$i++){
		$mn=0;
			if($i!=$upto_yr){
				$mn=12;
			}
			if($i==$year_join){
				$start_mn=$month_join;
			}else{
				$start_mn=1;
			}
			if($i==$curr_year){
				$mn=$curr_month;
			}
			if($emp_status=='I' and $i==$upto_yr){
           	   $mn=$upto_month;
			}
			for($j=$start_mn;$j<=$mn;$j++){
				$working_days+=calculate_workingdays($j,$i,'false',$id,$doj,$dor,$emp_status);	
          
           }
		}
        return $working_days;
    }
    return 0;	
}

/**
 * @param int $currency_type
 *
 * @return float
 */
function get_conversion_rate($currency_type=1){
	$conversion_rate=(float)1;
	$result=execute_my_query("select GCT_CONVERSION_RATE from gft_currency_type_master where GCT_TYPE_ID=$currency_type ");
	if(mysqli_num_rows($result)==1){
		$qd=mysqli_fetch_array($result);
		$conversion_rate=(float)$qd['GCT_CONVERSION_RATE'];		
		if ((int)$conversion_rate == 0){
			$conversion_rate=(float)1;
		}
	}	
	return $conversion_rate; 
	
}

/**
 * @param string $project_id
 * @param string $task_id
 * @param string $Column_name
 * @param string $action
 * @param string $oldvalue
 * @param string $new_value
 * 
 * @return void
 */

function pcs_log_entry($project_id,$task_id,$Column_name,$action,$oldvalue,$new_value){		
	global $uid;
	$audit_query="insert into gft_pcs_cust_task_audit_dtl (GPTA_PROJECT_ID,GPTA_TASK_ID,GPTA_EDIT_BY,GPTA_DATE_TIME,GPTA_FIELD_NAME,GPTA_ACTION,GPTA_FIELD_OLD_VALUE,GPTA_FIELD_NEW_VALUE) " .
			" values($project_id,$task_id,$uid,now(),'$Column_name','$action','$oldvalue','$new_value' ) ";
	execute_my_query($audit_query);		
}

/**
 * @param string $ref_ptag
 * 
 * @return string[int]
 */
function get_currency_type_of_tag($ref_ptag){
		$rcurrency_type=1;

		$rptag=/*. (string[int]) .*/ array();
		if($ref_ptag!=''){
			$query="select GPL_CURRENCY_TYPE,GPL_INCLUSIVE_OF_TAX from gft_price_list_master pl,gft_price_tag_master tg " .
					"where GPL_ID=GPL_PRICE_LIST_ID and GPL_TAG_ID=$ref_ptag ";
			$result=execute_my_query($query);
			if((mysqli_num_rows($result)>0)&& $qd=mysqli_fetch_array($result)){
				$rptag[0]=$qd['GPL_CURRENCY_TYPE'];
				$rptag[1]=$qd['GPL_INCLUSIVE_OF_TAX'];
			}			
		}
		return 	$rptag;
}

/** 
 * @param int $tax_mode
 * 
 * @return float
 */
function get_percentage_of($tax_mode){
	if($tax_mode==0){
		return 0;
	}
	$query="select GTM_TAX_PER from gft_tax_type_master where GTM_ID='$tax_mode' ";
	$result=execute_my_query($query);
	$qd=mysqli_fetch_array($result);
	$tax_perc=$qd['GTM_TAX_PER'];
	return (float)$tax_perc;
}



/**
 * @param string $date
 *
 * @return int
 */
function get_week($date){
	$new_date=explode("-",$date);
	$dt 		= 	DateTime::createFromFormat('Y-m-d', $date);
	$checkDT	=	DateTime::getLastErrors();
	if((int)$checkDT['warning_count'] > 0){ return 0;}
	//$ts = mktime(0,0,0,$month,$day,$year);
	$ts = mktime(0,0,0,$new_date[1],$new_date[2],$new_date[0]);
	$week = (int)date("W",$ts);
	return $week;
}

/**
 * @param string $week
 * @param string $year
 * 
 * @return string[string]
 */
function findWeekPeriod($week, $year){
	//sunday to saterday
	$aPeriod = /*. (string[string]) .*/ array();
	$today = getdate();
	$weekday= (int) $today['wday'];
	$weekday=$weekday;
	$wend=6-$weekday;
	$aPeriod['start'] = date("Y-m-d", mktime(0, 0, 0, (int)date("m")  , (int)date("d")-$weekday, (int)date("Y")));
	$aPeriod['end'] = date("Y-m-d", mktime(0, 0, 0, (int)date("m")  , (int)date("d")+$wend, (int)date("Y")));
	return $aPeriod;
}

/**
 * @param int $week
 * @param int $year
 * 
 * @return string[string]
 */
function findWeekPeriod1($week, $year){
	//Monday to sunday
	$aPeriod = /*. (string[string]) .*/ array();
	$today = getdate();
	$weekday= (int) $today['wday'];
	if($weekday==0){
		$weekday=6;
		$wend=0;
	}else{
		$weekday=$weekday-1;
		$wend=6-$weekday;	
	}
	
	$aPeriod['start'] = date("Y-m-d", mktime(0, 0, 0, (int)date("m")  , (int)date("d")-$weekday, (int)date("Y")));
	$aPeriod['end'] = date("Y-m-d", mktime(0, 0, 0, (int)date("m")  , (int)date("d")+$wend, (int)date("Y")));
	
	return $aPeriod;
}

/**
 * @param int $seconds
 * 
 * @return string
 */
function get_time_duration($seconds){
	$time_hdrs='';
	$seconds = (int)$seconds;
	$time_hdrs=floor($seconds/3600).':'.substr('0'.floor(($seconds%3600)/60),-2).':'.substr('0'.($seconds%60),-2);
	return $time_hdrs;
}

/**
 * @param int $month
 * @param int $year
 *
 * @return int
 */
function find_lastday_in_month($month,$year){
	return cal_days_in_month(CAL_GREGORIAN,$month,$year);
	//$last_day_of_month = date( "d", mktime(0, 0, 0, $month + 1, 0, $year) ) ;
	//return $last_day_of_month;
}	

/**
 * @param int $month
 * @param int $year
 * 
 * @return string
 */
function find_lastdate_in_month($month,$year){
	if ($month > 12){
		$month=$month-12;
		$year=$year+1;
	}

	$last_day_of_month = date( "d-m-Y", mktime(0, 0, 0, $month , cal_days_in_month(CAL_GREGORIAN,$month,$year), $year) );
	return $last_day_of_month;
}	

/**
 * @param string $datefrom
 * @param string $dateto
 * @param boolean $using_timestamps
 *
 * @return int
 */
function datediff($datefrom, $dateto, $using_timestamps = false) {
	if (!$using_timestamps){
    	$datefrom = strtotime($datefrom, 0);
    	$dateto = strtotime($dateto, 0);
    }
   	$difference = $dateto - $datefrom; // Difference in seconds
   	$datediff = floor($difference / 86400);
  	return (int) $datediff;
}  

/**
 * @param int $total_hrs
 * @param int $total_min
 * @param int $total_sec
 * 
 * @return string
 */
function give_in_format($total_hrs,$total_min,$total_sec){
	$total_hrs=($total_hrs*3600)+($total_min*60)+$total_sec;
	$hoursVal=floor($total_hrs/3600);
	$difference = $total_hrs - ($hoursVal*3600);
	
	$minutesVal = floor($difference/60);
	$difference = $difference - ($minutesVal*60);

	$minutes = "00".$minutesVal;
	//$minutes ="00".substr($minutes,-2);
	$minutes =substr($minutes,-2);
	
	$seconds = "00".$difference;
	$seconds =substr($seconds,-2);
	
	$hours="00".$hoursVal;
	$hours=substr($hours,-2);
	//if(strlen($hours)==1){
	//	$hours="0".$hours;
	//}	
	$frm=$hours.":".$minutes.":".$seconds;
	return $frm;
}

/**
 * @param int $secondsVar
 *
 * @return string[string]
 */
function secondsToTime($secondsVar){
	$hours = floor($secondsVar / (60 * 60));
    $divisor_for_minutes = $secondsVar % (60 * 60);
    $minutes = "".floor($divisor_for_minutes / 60);
    $minutes =substr("00".$minutes,-2);
    //extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $seconds = "".ceil($divisor_for_seconds);
    $seconds =substr("00".$seconds,-2);
    // return the final array
    $obj = /*. (string[string]) .*/ array(
              "h" => (string)$hours,
              "m" => $minutes,
              "s" => $seconds,
    );
   return $obj;
}

/**
 * @param string $datei
 *
 * @return string
 */
function db_date_format($datei){
	if($datei!=''){
		$new1 = explode("-", $datei);
		if(strlen($new1[0])==4){
			return $datei;
		}else if(strlen($new1[2])==4){
			return $new1[2].'-'.$new1[1].'-'.$new1[0];
		}else{
			return '';
		}
	}
	return '';
}

/**
 * @param string $datei
 *
 * @return string
 */
function get_previous_date($datei){
	if($datei!=''){
		$new1 = explode("-", $datei);
		if(strlen($new1[0])==4){
			$year=$new1[0];
			$month=$new1[1];
			$day=$new1[2];			
		}else if(strlen($new1[2])==4){
			$year=$new1[2];
			$month=$new1[1];
			$day=$new1[0];
		}else{
			return '';
		}
		$prev_date=date('Y-m-d',mktime('0','0','0',(int)$month,((int)$day-1),(int)$year));
		return $prev_date;
	}
	return '';
}

/**
 * @param string $datei
 *
 * @return string
 */
function get_next_date($datei){
	if($datei!=''){
		$new1 = explode("-", $datei);
		if(strlen($new1[0])==4){
			$year=$new1[0];
			$month=$new1[1];
			$day=$new1[2];			
		}else if(strlen($new1[2])==4){
			$year=$new1[2];
			$month=$new1[1];
			$day=$new1[0];
		}else{
			return '';
		}
		$prev_date=date('Y-m-d',mktime(0,0,0,(int)$month,((int)$day+1),(int)$year));
		return $prev_date;
	}
	return '';
}

/**
 * @param string $cp_lcode
 * 
 * @return string
 */
function get_corp_purchase_pcodes($cp_lcode){
	$query= " select group_concat( distinct concat(GPM_HEAD_FAMILY,'-',substring(GOP_PRODUCT_SKEW,1,4))) pcode from gft_order_hdr" .
			" join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO)" .
			" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GOP_PRODUCT_SKEW=pm.GPM_PRODUCT_SKEW and GFT_SKEW_PROPERTY=1)" .
			" join gft_product_family_master pfm on (pm.GPM_PRODUCT_CODE=pfm.GPM_PRODUCT_CODE) " .
			" where  GOD_ORDER_STATUS='A' and god_lead_code=$cp_lcode" ;
	if($data=mysqli_fetch_array(execute_my_query($query))){
		$purchased_pcodes1=explode(',',$data['pcode']);
    	$purchased_pcodes="'".implode("','",$purchased_pcodes1)."'";
		return $purchased_pcodes;
	}else {
		return '';
	}
}

/**
 * @param string $lcode
 * @param boolean $only_base_product
 *
 * @return string
 */
function get_cust_installed_pcode($lcode,$only_base_product=false){
	$query= " select group_concat( distinct concat(GID_HEAD_OF_FAMILY,'-',substring(GID_LIC_PSKEW,1,4))) pcode " .
			" from gft_install_dtl_new " .
			" join gft_order_hdr on (GID_ORDER_NO=GOD_ORDER_NO) " .
			" join gft_product_family_master on (gpm_product_code=gid_lic_pcode) ".
			" where gid_status!='U' and GOD_ORDER_STATUS='A' and gid_lead_code=$lcode " ;
	if($only_base_product){
		$query .= " and GPM_IS_BASE_PRODUCT='Y' ";
	}
	if($data=mysqli_fetch_array(execute_my_query($query))){
		if($data['pcode']!=null){
			$purchased_pcodes1=explode(',',$data['pcode']);
	    	$purchased_pcodes="'".implode("','",$purchased_pcodes1)."'";
			return $purchased_pcodes;
		}else{
			return '';
		}
		
	}else {
		return '';
	}
}

/**
 * @param string $datei
 *
 * @return string
 */
function local_date_format($datei){
	if($datei!=''){
		$new1 = explode("-", $datei);
		if(strlen($new1[2])==4){
			return $datei;
		}else if(strlen($new1[0])==4){
			return $new1[2].'-'.$new1[1].'-'.$new1[0];
		}else{
			return '';
		}
	}
	return '';
}

/**
 * @param int $month
 * @param int $year
 * 
 * @return string
 */
function get_current_financial_yr($month=0,$year=0){
	if($year==0){	$year=(int)date('Y'); }
	if($month==0){$month=(int)date('m'); }
	$query_get_financial_yr="select financial_year from financial_year " .
			"where year_val='$year' AND month_val='$month' ";
	$result=execute_my_query($query_get_financial_yr,'',true,false,3);
	$qd=mysqli_fetch_array($result);
	$financial_yr=$qd[0];
	return $financial_yr;
}

/**
 * @param string $str_date
 *
 * @return int
 */
function str_to_date($str_date){
	$newVar = explode("-",$str_date);
	$date_date= mktime(0, 0, 0,(int)$newVar[1],(int)$newVar[2],(int)$newVar[0]);
	return $date_date;
}

/**
 * @param string $str_date
 * @param int $days
 *
 * @return string
 */
function add_date($str_date,$days){
	$newVar = explode("-",$str_date);
	$date_date=date('Y-m-d', mktime(0, 0, 0,(int)$newVar[1],(int)$newVar[2]+$days,(int)$newVar[0]));
	return $date_date;
}

/**
 * @param int $quarter
 * @param string $financial_yr
 *
 * @return string[int]
 */
function get_start_end_date_of_quarter($quarter,$financial_yr){
	$inbetween_dates=/*. (string[int]) .*/ array();
	$financial_yr_arr=explode('-',$financial_yr);
	if($quarter!=0){
		if($quarter==1){ $from_month=4; $to_month=6; $year=$financial_yr_arr[0]; }
		elseif($quarter==2){ $from_month=7; $to_month=9; $year=$financial_yr_arr[0]; }
		elseif($quarter==3){ $from_month=10; $to_month=12; $year=$financial_yr_arr[0]; }
		elseif($quarter==4){ $from_month=1; $to_month=3; $year=$financial_yr_arr[1]; }
		else{
			return $inbetween_dates;
		}
		$from_dt=date('Y-m-d',mktime('0','0','0',$from_month,1,$year));
		$to_dt=	date('Y-m-d',mktime('0','0','0',($to_month+1),0,$year));
	}else{ 
		// condition: ($quarter==0)
		$from_dt=date('Y-m-d',mktime('0','0','0',4,1,$financial_yr_arr[0]));
		$to_dt=date('Y-m-d',mktime('0','0','0',3,31,$financial_yr_arr[1]));
	}
	//$inbetween_dates=array($from_dt,$to_dt);
	$inbetween_dates[0]=$from_dt;
	$inbetween_dates[1]=$to_dt;

	return $inbetween_dates;
}



/**
 * @param int $month
 *
 * @return int 
 */
function get_current_quarter($month=0){
	if($month==0){
		$month=(int)date('m');	
	}
	if($month==1 or $month==2  or $month==3){
		$quarter=4;
	}else if($month==4 or $month==5  or $month==6){
		$quarter=1;
	}else if($month==7 or $month==8  or $month==9){
		$quarter=2;
	}else if($month==10 or $month==11  or $month==12){
		$quarter=3;
	}else {
		$quarter=0;
	}
	return $quarter;
}


/**
 * @param int $month
 * @param int $year
 * 
 * @return string[int][int]
 */
function get_week_split($month,$year){
	$wdate[0][0]='01';
	$numDays = (int)date("t",mktime(0,0,0,$month,1,$year));
	$startDay = (int)date("w",mktime(0,0,0,$month,1,$year));
	$week_days_to_add=7-$startDay;
	$ds=(int)date("d",mktime(0,0,0,$month,$week_days_to_add,$year));
	$wdate[0][1]= date("d",mktime(0,0,0,$month,$week_days_to_add,$year));
	$week_days_to_add=7;
	for($i=1;$i<6;$i++){
		if(($ds+$week_days_to_add)<=$numDays){
		}else if($ds<$numDays){
			$week_days_to_add=$numDays-$ds;
		}else{ break;} 
		$wdate[$i][0]=date("d",mktime(0,0,0,$month,$ds+1,$year));
		$wdate[$i][1]=date("d",mktime(0,0,0,$month,$ds+$week_days_to_add,$year));
		$ds=(int)date("d",mktime(0,0,0,$month,$ds+$week_days_to_add,$year));
	}
	return $wdate;
}//end of funciton

/* Find no of months between two dates */
/**
 * @param string $date1
 * @param string $date2
 *
 * @return int
 */
function months_btwn($date1,$date2){
	$ages_in_month=0;
	$query="select round(datediff('$date2','$date1')/30) ";
	$result=execute_my_query($query);
	if($result){
		$qd=mysqli_fetch_array($result);
		$ages_in_month=(int)$qd[0];
	}
	return $ages_in_month;
}

/**
 * @return void
 */
function enter_quarter_start_end_date(){
	/* it is using in career graph ****/
	$yr_now=(int)date('Y');
	$query="replace INTO gft_date_between_this_month( GDB_MONTH ,GDB_YEAR ,GDB_FROM_DATE ,GDB_TO_DATE , GDB_DIFF ,GDB_DIFF_TYPE,GDB_DESC,GDB_FYEAR) VALUES ";
	$putcomma="";
	for($yr=2004;$yr<=$yr_now;){
		$next_year=substr("".($yr+1),-2);
		$fy=$yr.'-'.$next_year;
		$first_quarter_fmdate=date('Y-m-d',mktime(0,0,0,4,1,$yr));
		$first_quarter_todate=date('Y-m-d',mktime(0,0,0,7,0,$yr));
		if($first_quarter_todate>date('Y-m-d')){
			break ;
		}
		$query.=" $putcomma ('','','$first_quarter_fmdate','$first_quarter_todate',3,'M','Q1 :April 1 - June 30','$fy')";
		$putcomma=",";
		
		$sec_quarter_fmdate=date('Y-m-d',mktime(0,0,0,7,1,$yr));
		$sec_quarter_todate=date('Y-m-d',mktime(0,0,0,10,0,$yr));
		if($sec_quarter_todate>date('Y-m-d')){
			break;
		}
		$query.=" $putcomma ('','','$sec_quarter_fmdate','$sec_quarter_todate',3,'M','Q2 : July 1 - Sep 30','$fy')";
		
		$third_quarter_fmdate=date('Y-m-d',mktime(0,0,0,10,1,$yr));
		$third_quarter_todate=date('Y-m-d',mktime(0,0,0,1,0,$yr+1));
		if($third_quarter_todate>date('Y-m-d')){
			break;
		}
		$query.=" $putcomma ('','','$third_quarter_fmdate','$third_quarter_todate',3,'M','Q3 : Oct 1 - Dec 31','$fy')";
		
		$yr=$yr+1;
		$fourth_quarter_fmdate=date('Y-m-d',mktime(0,0,0,1,1,$yr));
		$fourth_quarter_todate=date('Y-m-d',mktime(0,0,0,4,0,$yr));
		if($fourth_quarter_todate>date('Y-m-d')){
			break;
		}
		$query.=" $putcomma ('','','$fourth_quarter_fmdate','$fourth_quarter_todate',3,'M','Q4 : Jan 1 - Mar 31','$fy')";
	}
	execute_my_query($query);
}

/**
 * @return void
 */

function enter_twelve_month_start_end_date(){
	$now_month=$curr_mn=(int)date('m');
	$now_yr=$curr_yr=(int)date('Y');
	$query="replace INTO gft_date_between_this_month(GDB_MONTH ,GDB_YEAR ,GDB_FROM_DATE ,GDB_TO_DATE ,GDB_DIFF ,GDB_DIFF_TYPE) VALUES ";
	$putcomma="";
	while($now_yr>=2003){
		$date2=date('Y-m-d',mktime(0,0,0,$now_month,0,$now_yr));
		$date1=date('Y-m-d',mktime(0,0,0,$now_month-12,1,$now_yr));
	    $now_yr--;
		$query.=" $putcomma ('$curr_mn','$curr_yr','$date1','$date2',12,'M')";
		$putcomma=",";
	}
	execute_my_query($query);
}

/**
 * @return void
 */
function enter_every_month_start_and_end_date(){
	$now_month=$curr_mn=date('m');
	$now_yr=$curr_yr=date('Y');
	$query="replace INTO gft_date_between_this_month(
		GDB_MONTH ,GDB_YEAR ,GDB_FROM_DATE ,GDB_TO_DATE ,
		GDB_DIFF ,GDB_DIFF_TYPE) VALUES ";
	$putcomma="";
	$from_mn=11;
	$from_yr=2004;
	$date2=date('Y-m-d',mktime(0,0,0,$from_mn+1,0,$from_yr));
	while($date2<date('Y-m-d')){
		$date2=date('Y-m-d',mktime(0,0,0,$from_mn+1,0,$from_yr));
		$date1=date('Y-m-d',mktime(0,0,0,$from_mn,1,$from_yr));
		$query.=" $putcomma ('0','0','$date1','$date2',0,'M')";
	    $putcomma=",";
	 	$from_yr=(int)date('Y',mktime(0,0,0,$from_mn+1,1,$from_yr));
		$from_mn=(int)date('m',mktime(0,0,0,$from_mn+1,1,$from_yr));
	}
	execute_my_query($query);
}

/**
 * @param int $time_in
 * @param int $avg_of
 *
 * @return string 
 */
function get_avg_of_time($time_in,$avg_of){
     $query="select sec_to_time(time_to_sec('".$time_in."')/$avg_of)";
     $result=execute_my_query($query);
     $qd=mysqli_fetch_array($result);
     $hrs=$qd[0];
     return $hrs;
}

/**
 * @param string $month
 * @param string $year
 * @param string $date_dt
 *
 * @return string
 */
function year_month_no($month=null,$year=null,$date_dt=null){
	
	if($date_dt!=null){
		$date_dt=db_date_format($date_dt);
		$from_dt_arr=explode('-',$date_dt);
		$month=$from_dt_arr[1];
		$year=$from_dt_arr[0];
	}
	if($year==null or $month==null){
		$year=date('Y');$month=date('m');
	}
	$query="SELECT yr_month_no FROM financial_year WHERE year_val=$year AND month_val=$month";
	$result=execute_my_query($query);
    $qd=mysqli_fetch_array($result);
    $hrs=$qd[0];
    return $hrs;	
}

/**
 * @param int $primary
 * 
 * @return int[int]
 */
function get_other_user_role_ids($primary){
	if($primary==1){//is CP EMP, reseller & their employee
		return array(26,27,28);
	}else if($primary==2){//is CP or CC
		return array(21,22);
	}else if($primary==3){ //could nt create login
		return array(22,26,28);
	}else if($primary==4){ //is reseller
		return array(27);
	}else if($primary==5){ //  reseller emp ,cp emp
		return array(26,28);
	}else{
		return array(21,22,26,27,28);
	}
}

/**
 * @param string $cid
 *
 * @return string
 */
function get_empid_from_lcode($cid){
	$cid_arr=explode('-',$cid);
	$lead_code=$cid_arr[0];
	$sql="SELECT  GLEM_EMP_ID,lh.GLH_LEAD_CODE, lh.GLH_TERRITORY_ID FROM gft_leadcode_emp_map,gft_lead_hdr lh ".
		 " WHERE  lh.GLH_LEAD_CODE ='$lead_code' AND lh.GLH_LEAD_CODE=GLEM_LEADCODE ";
	$rs=execute_my_query($sql);
	$eid="";
	while($row=mysqli_fetch_array($rs)){
		$eid=$row['GLEM_EMP_ID'];
	}
	return $eid; 
}

/**
 * @param string $eid
 *
 * @return string 
 */
function get_custid_from_empid($eid){
	$custid='';
	$sql="SELECT  GLEM_EMP_ID,lh.GLH_LEAD_CODE, lh.GLH_TERRITORY_ID FROM gft_leadcode_emp_map,gft_lead_hdr lh ".
    	" WHERE GLEM_EMP_ID='$eid' AND lh.GLH_LEAD_CODE=GLEM_LEADCODE  ";
	$rs=execute_my_query($sql);
	$eid="";
	while($row=mysqli_fetch_array($rs)){
		$custid=$row['GLH_LEAD_CODE'];
	}
	return $custid; 
}

/**
 * @param string $eid
 * 
 * @return string
 */
function is_customer_a_CP($eid){
	$custid='';
	$sql="SELECT GLEM_EMP_ID,lh.GLH_LEAD_CODE, lh.GLH_TERRITORY_ID FROM gft_leadcode_emp_map,gft_lead_hdr lh".
    	" WHERE GLEM_EMP_ID='$eid' AND lh.GLH_LEAD_CODE=GLEM_LEADCODE ";
	$rs=execute_my_query($sql);
	$eid="";
	while($row=mysqli_fetch_array($rs)){
		$custid=$row['GLH_LEAD_CODE'];
	}
	return $custid; 
}

/**
 * @param string[string] $query_string
 *
 * @return string
 */
function get_necessary_data_from_query_emp_tooltip($query_string){
	$emp_mbl=isset($query_string['GEM_MOBILE'])?$query_string['GEM_MOBILE']:'';
	$emp_email=isset($query_string['GEM_EMAIL'])?$query_string['GEM_EMAIL']:'';
	$tooltip='';
	if($emp_mbl!=''){
		$tooltip="Mobile No : ".$emp_mbl;
		$tooltip.="<br>";
	}
	if($emp_email!='') {
		$tooltip.="Email  : ".$emp_email;
	}
	if(isset($query_string['GEM_IC']) && $query_string['GEM_IC']!=''){
		$tooltip.="<br> Intercom : ".$query_string['GEM_IC']."";
	}
	return $tooltip;
}

/**
 * @param array $query_string
 * @param boolean $license_mail
 *
 * @return string
 */
function get_necessary_data_from_query_for_tooltip($query_string,$license_mail=false){
	return 'Click the link to view the details';
	//Note: Do not enable without discusss with Santu 
	/*
	$street_no=(isset($query_string['GLH_STREET_DOOR_NO'])?$query_string['GLH_STREET_DOOR_NO']:'');
	$street_name=(isset($query_string['GLH_CUST_STREETADDR1'])?$query_string['GLH_CUST_STREETADDR1']:'');
	$tooltip=((isset($query_string['GLH_CUST_NAME']) and $license_mail==true and trim($query_string['GLH_CUST_NAME']))!=''?"Shop Name :".$query_string['GLH_CUST_NAME']:'').
			  ((isset($query_string['GLH_LEAD_CODE'])  and $license_mail==false  and trim($query_string['GLH_LEAD_CODE']))!=''?"Customer ID:".$query_string['GLH_LEAD_CODE']:'').
			  ((isset($query_string['GLH_AUTHORITY_NAME']) and $license_mail==false  and trim($query_string['GLH_AUTHORITY_NAME']))!=''?"<br>Authority Name:".trim($query_string['GLH_AUTHORITY_NAME']):'').
			  ((isset($query_string['GLH_DOOR_APPARTMENT_NO']) and trim($query_string['GLH_DOOR_APPARTMENT_NO']))!=''?"<br>Door Appartment No :".trim($query_string['GLH_DOOR_APPARTMENT_NO']):'').
			  ((isset($query_string['GLH_BLOCK_SOCEITY_NAME']) and trim($query_string['GLH_BLOCK_SOCEITY_NAME']))!=''?"<br>Block Soceity Name:".trim($query_string['GLH_BLOCK_SOCEITY_NAME']) :'').
			  (trim($street_no.$street_name)!=''?"<br>Street Name :".$street_no.$street_name:'').
			  ((isset($query_string['GLH_CUST_STREETADDR2']) and trim($query_string['GLH_CUST_STREETADDR2']))?"<br>Location :".trim($query_string['GLH_CUST_STREETADDR2']):'').
			  ((isset($query_string['GLH_AREA_NAME']) and trim($query_string['GLH_AREA_NAME']))?"<br>Area Name :".trim($query_string['GLH_AREA_NAME']) :'').
			  ((isset($query_string['GLH_LANDMARK']) and trim($query_string['GLH_LANDMARK']))?"<br>LandMark :".trim($query_string['GLH_LANDMARK']) :'').
			  ((isset($query_string['GLH_CUST_CITY']) and trim($query_string['GLH_CUST_CITY']))?"<br>City :".trim($query_string['GLH_CUST_CITY']) :''). 
			  ((isset($query_string['GLH_CUST_PINCODE']) and trim($query_string['GLH_CUST_PINCODE']))!=''?" PINCODE:".trim($query_string['GLH_CUST_PINCODE']):'').
			  ((isset($query_string['GLH_CUST_STATECODE']) and $query_string['GLH_CUST_STATECODE']!=null and trim($query_string['GLH_CUST_STATECODE'])!='')?"<br>State :".trim($query_string['GLH_CUST_STATECODE']):"").
			  ((isset($query_string['GLH_COUNTRY']) and $query_string['GLH_COUNTRY']!=null and trim($query_string['GLH_COUNTRY'])!='')?"<br>Country :".trim($query_string['GLH_COUNTRY']):"").
			  ((isset($query_string['GLH_CREATED_DATE']) and $license_mail==false and $query_string['GLH_CREATED_DATE']!=null and trim($query_string['GLH_CREATED_DATE'])!='')?"<br>Created Date :".trim($query_string['GLH_CREATED_DATE']):"").		  
			  ((isset($query_string['BUSSNO']) and trim($query_string['BUSSNO'])!=0 and trim($query_string['BUSSNO'])!='')?"<BR>Buss. Phone :".trim($query_string['BUSSNO']):"").
			  ((isset($query_string['MOBILE']) and trim($query_string['MOBILE'])!='')?"<BR>Mobile :".trim($query_string['MOBILE']):"").
			  ((isset($query_string['RESNO']) and trim($query_string['RESNO'])!='')?"<BR>Res. Phone :".trim($query_string['RESNO']):'').
			  ((isset($query_string['EMAIL']) and trim($query_string['EMAIL'])!='')?"<BR>Email :".trim($query_string['EMAIL']):'').
			  ((isset($query_string['FAX']) and trim($query_string['FAX'])!='')?"<BR>Fax :".trim($query_string['FAX']):'').
			  ((isset($query_string['WEBSITE']) and trim($query_string['WEBSITE'])!='')?"<BR>Website :".trim($query_string['WEBSITE']):'').
			  ((isset($query_string['CREATED_CATEGORY']) and $license_mail==false and trim($query_string['CREATED_CATEGORY'])!='')?"<br> Created Category :".$query_string['CREATED_CATEGORY']:'');
			  
	return $tooltip;
	*/
}//END OF FUNCTION

/**
 * @param string[string] $query_string
 * @param boolean $license_mail
 *
 * @return string
 */
function get_special_necessary_data_from_query_for_tooltip($query_string,$license_mail=false){
	//Note: Do not call this function without discusss with Santu 
	$street_no=(isset($query_string['GLH_STREET_DOOR_NO'])?$query_string['GLH_STREET_DOOR_NO']:'');
	$street_name=(isset($query_string['GLH_CUST_STREETADDR1'])?$query_string['GLH_CUST_STREETADDR1']:'');
	$tooltip=((isset($query_string['GLH_CUST_NAME']) and $license_mail==true and trim($query_string['GLH_CUST_NAME']))!=''?"Shop Name :".$query_string['GLH_CUST_NAME']:'').
			  ((isset($query_string['GLH_LEAD_CODE'])  and $license_mail==false  and trim($query_string['GLH_LEAD_CODE']))!=''?"Customer ID:".$query_string['GLH_LEAD_CODE']:'').
			  ((isset($query_string['GLH_AUTHORITY_NAME']) and $license_mail==false  and trim($query_string['GLH_AUTHORITY_NAME']))!=''?"<br>Authority Name:".trim($query_string['GLH_AUTHORITY_NAME']):'').
			  ((isset($query_string['GLH_DOOR_APPARTMENT_NO']) and trim($query_string['GLH_DOOR_APPARTMENT_NO']))!=''?"<br>Door Appartment No :".trim($query_string['GLH_DOOR_APPARTMENT_NO']):'').
			  ((isset($query_string['GLH_BLOCK_SOCEITY_NAME']) and trim($query_string['GLH_BLOCK_SOCEITY_NAME']))!=''?"<br>Block Soceity Name:".trim($query_string['GLH_BLOCK_SOCEITY_NAME']) :'').
			  (trim($street_no.$street_name)!=''?"<br>Street Name :".$street_no.$street_name:'').
			  ((isset($query_string['GLH_CUST_STREETADDR2']) and trim($query_string['GLH_CUST_STREETADDR2']))?"<br>Location :".trim($query_string['GLH_CUST_STREETADDR2']):'').
			  ((isset($query_string['GLH_AREA_NAME']) and trim($query_string['GLH_AREA_NAME']))?"<br>Area Name :".trim($query_string['GLH_AREA_NAME']) :'').
			  ((isset($query_string['GLH_LANDMARK']) and trim($query_string['GLH_LANDMARK']))?"<br>LandMark :".trim($query_string['GLH_LANDMARK']) :'').
			  ((isset($query_string['GLH_CUST_CITY']) and trim($query_string['GLH_CUST_CITY']))?"<br>City :".trim($query_string['GLH_CUST_CITY']) :''). 
			  ((isset($query_string['GLH_CUST_PINCODE']) and trim($query_string['GLH_CUST_PINCODE']))!=''?" PINCODE:".trim($query_string['GLH_CUST_PINCODE']):'').
			  ((isset($query_string['GLH_CUST_STATECODE']) and $query_string['GLH_CUST_STATECODE']!=null and trim($query_string['GLH_CUST_STATECODE'])!='')?"<br>State :".trim($query_string['GLH_CUST_STATECODE']):"").
			  ((isset($query_string['GLH_COUNTRY']) and $query_string['GLH_COUNTRY']!=null and trim($query_string['GLH_COUNTRY'])!='')?"<br>Country :".trim($query_string['GLH_COUNTRY']):"").
			  ((isset($query_string['GLH_CREATED_DATE']) and $license_mail==false and $query_string['GLH_CREATED_DATE']!=null and trim($query_string['GLH_CREATED_DATE'])!='')?"<br>Created Date :".trim($query_string['GLH_CREATED_DATE']):"").		  
			  ((isset($query_string['BUSSNO']) and trim($query_string['BUSSNO'])!=0 and trim($query_string['BUSSNO'])!='')?"<BR>Buss. Phone :".trim($query_string['BUSSNO']):"").
			  ((isset($query_string['MOBILE']) and trim($query_string['MOBILE'])!='')?"<BR>Mobile :".trim($query_string['MOBILE']):"").
			  ((isset($query_string['RESNO']) and trim($query_string['RESNO'])!='')?"<BR>Res. Phone :".trim($query_string['RESNO']):'').
			  ((isset($query_string['EMAIL']) and trim($query_string['EMAIL'])!='')?"<BR>Email :".trim($query_string['EMAIL']):'').
			  ((isset($query_string['FAX']) and trim($query_string['FAX'])!='')?"<BR>Fax :".trim($query_string['FAX']):'').
			  ((isset($query_string['WEBSITE']) and trim($query_string['WEBSITE'])!='')?"<BR>Website :".trim($query_string['WEBSITE']):'').
			  ((isset($query_string['CREATED_CATEGORY']) and $license_mail==false and trim($query_string['CREATED_CATEGORY'])!='')?"<br> Created Category :".$query_string['CREATED_CATEGORY']:'');
			  
	return $tooltip;
}

/**
 * @param string $lead_code
 * 
 * @return string
 */
function get_customer_address_contacts($lead_code){
		global $address_fields,$query_contact_dtl;
		$query="select GLH_LEAD_CODE $address_fields from gft_lead_hdr lh " .
				"$query_contact_dtl where GLH_LEAD_CODE='$lead_code' " .
				"group by GLH_LEAD_CODE ";
		$result=execute_my_query($query);
		$qdata=mysqli_fetch_array($result);		
		$addr=get_necessary_data_from_query_for_tooltip($qdata);
		return $addr;	
}


/**
 * @param string $emp_id
 * @param string $ename
 * @param int $link_category
 * @param string $tooltip
 * @param string[string] $query_string
 *
 * @return string
 */
function get_ename_link($emp_id,$ename,$link_category,$tooltip=null,$query_string=null){
	$status_mark='';
	if($tooltip==null and isset($query_string)){ $tooltip=get_necessary_data_from_query_emp_tooltip($query_string);}
	if($emp_id=='' and isset($query_string['GEM_EMP_ID'])){ $emp_id=$query_string['GEM_EMP_ID']; }
	if($ename=='' and isset($query_string['GEM_EMP_NAME'])){ $ename=$query_string['GEM_EMP_NAME']; }
	if(isset($query_string['GEM_STATUS'])){	$status_mark=($query_string['GEM_STATUS']!='A'?"<font color=red>*</font>":"");}
	$add_tooltip='';
	if($link_category==1){
		//Employee Master
		$link="";
	}else if($link_category==2 and $emp_id!='' and $ename!=''){	
		//IPR
		$link="executive_performance_rpt_sales.php?eid=$emp_id&ename=$ename";
	}else{
		$link='';
	}

	if($tooltip!=''){$add_tooltip=" onMouseover=\"ddrivetip('".$tooltip."','#EFEFEF', 200);\" onMouseout=\"hideddrivetip();\""; }

	if($link!=''){
		$ename_link="<a target=_blank href=\"$link\" $add_tooltip>$ename $status_mark</a>";
	}else if($link=='' and $tooltip!=''){
		$ename_link="<label $add_tooltip>$ename $status_mark</label>";
	}else{
		$ename_link=$ename;
	}
	return $ename_link;
}

/**
 * @return void
 */
function close_the_popup(){
echo<<<END
<script type="text/javascript">window.close();</script>
END;
}

/**
 * @param string $msg
 * @param int $goback
 *
 * @return void
 */
function show_my_alert_msg($msg,$goback=0){
$goback_code = "";
if($goback>0){
	$goback_code = "history.go(-1);";
}
echo<<<END
<script type="text/javascript">alert("$msg");$goback_code</script>
END;
}

/**
 * @param string $page
 *
 * @return void
 */
function js_delay_location_href_to($page){
echo<<<END
<script type="text/javascript">setTimeout("location.href='$page'",2500);</script>
END;
}

/**
 * @param string $page
 * 
 * @return void
 */
function js_location_href_to($page){
echo<<<END
<script type="text/javascript">location.href="$page";</script>
END;
}

/**
 * @return void 
 */
function js_reload_the_parent(){
echo<<<END
<script type="text/javascript">window.opener.location.reload();</script>
END;
}

/*while use iframe*/
/**
 * @param string $url
 *
 * @return void
 */
function js_open_in_parent_window($url){
echo<<<END
<script type="text/javascript">window.parent.location.href="$url";</script>
END;
}

/**
 * @return void
 */
function js_parent_reload(){
echo<<<END
<script type="text/javascript">window.parent.location.reload();</script>
END;
}

/**
 * @return string
 */
function return_additional_param() {
	if(!isset($_SERVER['REQUEST_URI'])){ 
 		$serverrequri = $_SERVER['PHP_SELF']; 
	}else{
 	$serverrequri = $_SERVER['REQUEST_URI']; }
 	$parse_url=parse_url($serverrequri); 
  	$param_url_query=isset($parse_url['query'])?$parse_url['query']:'';
	$queryParts = explode('&', $param_url_query);
    $additional_param='';
    if(isset($queryParts)){
	    foreach ($queryParts as $param) {
	        $item = explode('=', $param);
	        if($item[0]!='param' and $item[0]!='sortbycol' and $item[0]!='sorttype' and $item[0]!=''){
	        	$additional_param.="&".$item[0]."=".$item[1]."";
	        }
	    }
    }
    return $additional_param;
} 
/**
 * @param string[int][int] $tabs
 * @param string $param
 * @param string $file_name
 * @param string $extra_param
 * @param string[int] $menu_links
 *
 * @return void
 */
function get_report_tab_view($tabs,$param,$file_name,$extra_param="",$menu_links=null){
	$additional_param=return_additional_param();
	$selectable_font_color='#010101';
	$selected_font_color='#8e8c8e';
	echo<<<END
<table cellpadding="0" cellspacing="0" border="0"><tr>
END;
	for($i=0;$i<count($tabs);$i++){
		$tab_identity_font[$i]=$selectable_font_color;
		$title=$tabs[$i][1];
		$param_value=$tabs[$i][0];
		$tab_identity_font=$selectable_font_color;
		if($param_value==$param){ $tab_identity_font=$selected_font_color; }
		if($title=='')continue;
		$file_name_url = "$file_name?param={$param_value}$additional_param$extra_param";
		if(isset($menu_links[$i]) && $menu_links[$i]!=""){
			$file_name_url =($menu_links[$i])."?param={$param_value}$additional_param$extra_param";
		}
		echo<<<END
<td vAlign="top" align="right" style="height:20">
<img alt="" src="images/header_start.gif" border="0" ></td>
<td class="formHeader" style="background-image:url(images/header_tile.gif); height:20;"  align="left"  >
<a href="$file_name_url"><FONT color="$tab_identity_font">{$title}&nbsp;</font></a></td>
<td vAlign="top" align="left" style="height:20">
<img alt="" src="images/header_end.gif" border="0"></td>
END;
	}
	echo '</table>';
}
/**
 * @param string[int] $category_types
 * @param string $lead_type_id
 *
 * @return string[int][int] 
 */
function get_lead_type($category_types=null,$lead_type_id=null){
	$query_type="select GLD_TYPE_CODE, GLD_TYPE_NAME,GLD_TYPE_ABR from  gft_lead_type_master  " .
			" where GLD_STATUS='A' ".
			($category_types!=null ?" and GLD_TYPE_ABR in('".implode("','",$category_types)."') ":"").
			($lead_type_id!=null? " and GLD_TYPE_CODE=$lead_type_id ":""); 
	$type_list=get_two_dimensinal_result_set_from_query($query_type);
	return $type_list;
}

/**
 * @return string[int]
 */
function addon_product_code_list(){
	$query="SELECT GPM_PRODUCT_CODE FROM gft_product_family_master WHERE GPM_IS_INTERNAL_PRODUCT =2 ";
	$newresult=execute_my_query($query,'common_util.php',true,false,2);
	$pcode_list=/*. (string[int]) .*/ array();
	while($data=mysqli_fetch_array($newresult)){
		$pcode_list[]=$data['GPM_PRODUCT_CODE'];
	}
	return $pcode_list;
}

/**
 * @return string[int]
 */
function job_schedule_status(){
	return array(0=>"Pending",1=>"Processing",2=>"Completed",3=>"Canceled");
}

/**
 * @param string[int] $idArr
 * 
 * @return mixed[int]
 */
function skip_mandatory($idArr){
	$id=implode(',',$idArr);
	$query="select GMD_NAME,GMD_STATUS from gft_mandatory_skip_table,gft_mskip_dtl where" .
			" GMD_CODE=GMS_CODE AND GMS_STATUS='Y' and GMS_CODE in ($id) ";
	$result=execute_my_query($query);
	$detail_of_fld=/*. (string[int]) .*/ array();	$hidden_fields="";$mf=0;
	$mandatory_fields=array('mark_md_Area_Name'=>'','mark_md_City'=>'','mark_md_Contact_Dtl'=>'',
			'mark_md_Country'=>'','mark_md_Landmark'=>'','mark_md_Location'=>'','mark_md_Mobile_No'=>'',
			'mark_md_Pincode'=>'','mark_md_State'=>'','mark_md_Std_Code'=>'','mark_md_Street_Name'=>'',
			'mark_md_installed_check'=>'','mark_md_inventory_module'=>'','mark_md_allow_new_lead_entry'=>'','mark_md_support_form_no'=>'',
			'mark_md_Activity_block'=>'');
	while($qdata=mysqli_fetch_array($result)){
		$field="md_".$qdata['GMD_NAME'];
		$status=$qdata['GMD_STATUS'];
		$hidden_fields.="<input type=\"hidden\" id=\"$field\" name=\"$field\" value=\"$status\">";
		if($status=='A'){/* Mandatory*/
			$varb_field="mark_md_".$qdata['GMD_NAME'];	
			$mandatory_fields[$varb_field]="*";
			
		}
	}
	$detail_of_fld[0]=$hidden_fields;
	$detail_of_fld[1]=$mandatory_fields;
	return $detail_of_fld;
}
/**
 * @param string $start
 * @param string $end1
 * @param string $end2
 *
 * @return string
 */
function enclose($start, $end1, $end2){
	return "$start((?:[^$end1]|$end1(?!$end2))*)$end1$end2";
}


/**
 * @param string $contents
 * @param string $title
 * @param string $text
 * @param string $anchors
 * 
 * @return string
 */
function parse($contents, $title, $text, $anchors){
 //echo $contents;
	$pstring1 = "'[^']*'";
	$pstring2 = '"[^"]*"';
	$pnstring = "[^'\">]";
	$pintag   = "(?:$pstring1|$pstring2|$pnstring)*";
	$pattrs   = "(?:\\s$pintag){0,1}";
	$pcomment = enclose("<!--", "-", "->");
	$pscript  = enclose("<script$pattrs>", "<", "\\/script>");
	$pstyle   = enclose("<style$pattrs>", "<", "\\/style>");
	$pexclude = "(?:$pcomment|$pscript|$pstyle)";
	$ptitle   = enclose("<title$pattrs>", "<", "\\/title>");
	$panchor  = "<a(?:\\s$pintag){0,1}>";
	$phref    = "href\\s*=[\\s'\"]*([^\\s'\">]*)";
	$contents = preg_replace("/$pexclude/iX", " ", $contents);
	if ($title !== false)
		$title_matches = /*. (string[int]) .*/ array();
		$title = (preg_match("/$ptitle/iX",$contents, $title_matches)>0? $title_matches[1] : '');
	if ($text !== false){
		$text = preg_replace("/<$pintag>/iX",   " ", $contents);
		$text = preg_replace("/\\s+|&nbsp;/iX", " ", $text);
	}
	//NOTE: Is the following code required?
	//if ($anchors !== false){
	//	preg_match_all("/$panchor/iX", $contents, $anchors);
	//	$anchors = $anchors[0];
	//	reset($anchors);
	//	while (list($i, $x) = each($anchors)){
	//		$anchors[$i] =preg_match("/$phref/iX", $x, $x) ? $x[1] : '';
	//	}
	//	$anchors = array_unique($anchors);
	//}
	return $text;
}

/**
 * @param string[int] $mgroup_id
 * @param boolean $not_check 
 *
 * @return string[int]
 */
function get_status_master_from_group($mgroup_id,$not_check=false){
	$not=($not_check==true?'not ':' ') ;
    $group_id=implode(',',$mgroup_id);
	$query="select gtm_code from gft_status_master where gtm_group_id ".$not."in ($group_id) ";
	$result=execute_my_query($query,'common_util.php',true,false,2);
	$i=1;
	$status_list=/*. (string[int]) .*/ array();
	while($qdata=mysqli_fetch_array($result)){
		$status_list[$i]=$qdata['gtm_code'];
		$i++;
	}//end of while
	return $status_list;
}//end of fun
			

/**
 * @param string $lcode
 * @param string $terrid
 * @param string $no_js
 *
 * @return void
 */
function auto_fillup_contact_details($lcode,$terrid,$no_js=null){

	if($lcode!=""){
		if($no_js==""){
echo<<<SMS
<script type="text/javascript">
SMS;
		}
		$sql="select gcc_id,GCC_CONTACT_NAME,GCC_DESIGNATION,GCC_CONTACT_NO,gcc_contact_type " .
			 "from  gft_customer_contact_dtl ".
			 "WHERE GCC_LEAD_CODE='$lcode' order by gcc_id";
		$rs=execute_my_query($sql,'common_util.php',true,false,2);
		$lp=1;
		while($row2=mysqli_fetch_array($rs)){
			if($lp>1)
echo<<<END
addRow_contact_n();
END;
			$gcc_id=$row2['gcc_id'];
			$GCC_CONTACT_NAME=$row2['GCC_CONTACT_NAME'];
			$GCC_DESIGNATION=$row2['GCC_DESIGNATION'];
			$GCC_CONTACT_NO=$row2['GCC_CONTACT_NO'];
			$gcc_contact_type=$row2['gcc_contact_type'];
echo<<<END
			$("contact_id$lp").value='$gcc_id';
			$("contact_name$lp").value='$GCC_CONTACT_NAME';
			$("old_contact_name$lp").value='$GCC_CONTACT_NAME';
			$("designation$lp").value='$GCC_DESIGNATION';
			$("old_designation$lp").value='$GCC_DESIGNATION';
			$("contact_no$lp").value='$GCC_CONTACT_NO';
			$("old_contact_no$lp").value='$GCC_CONTACT_NO';
			$("contact_type$lp").value='$gcc_contact_type';
			$("old_contact_type$lp").value='$gcc_contact_type';
END;
			$lp++;
		}
		if($no_js=="") print " </script> ";
	}
}

/**
 * @param int $category
 * 
 * @return string
 */
function get_sms_config_info($category){
	$cg_query="select GSC_STATUS from  gft_sms_config where GSC_ID='$category'";
	$cg_result=execute_my_query($cg_query,'common_util.php',true,false,2);
	$tem_array=mysqli_fetch_array($cg_result);
	return $tem_array['GSC_STATUS'];
}

/**
 * @param boolean $any
 *
 * @return string[int][int]
 */

function get_sms_status_master($any=false){
	//get_two_dimensinal_array_from_table('gft_sms_status_master','GSMS_ID','GSMS_DESC');
	$query="select GSMS_ID, GSMS_DESC from gft_sms_status_master ";
	$result =execute_my_query($query,'common_util.php',true,false,2);
	$i=0; $temp=/*. (string[int][int]) .*/ array();
	if($any==true){
		$temp[$i]=array('-1','Any');
		$i++;
	}
	while($data=mysqli_fetch_array($result)){
		$temp[$i]=array($data['GSMS_ID'],$data['GSMS_DESC']);
		$i++;
	}
	return $temp;
}

/**
 * @return string[int][int]
 */
function get_assign_status_master(){
	$temp=/*. (string[int][int]) .*/ array(array(0,"All"), array(1,"Assigned"),array(2,"Unassigned")) ;
	return $temp;
} 

/**
 * @param string $uid
 * 
 * @return string[int]
 */
function get_role_emp_id($uid){
	$user=/*. (string[int]) .*/ array();

	$query="select gem_role_id,gem_emp_name,gem_mobile from gft_emp_master where gem_emp_id='$uid'";
	$result=execute_my_query($query);
	if($data=mysqli_fetch_array($result)){
		$user[0]=$data['gem_role_id'];
		$user[1]=$data['gem_emp_name'];	
		$user[2]=$data['gem_mobile'];
	}
	return $user;	
}

/**
 * @param int $zone_id
 * @param int $region_id
 * @param int $area_id
 *
 * @return string
 */
function get_emp_of_bmap($zone_id,$region_id,$area_id){
	$select_q="Select group_concat(distinct(gem_emp_id)) " .
			" from  b_map_view,gft_emp_master em " .
			" inner join gft_emp_territory_dtl c on (c.GET_EMP_ID=em.GEM_EMP_ID and c.GET_STATUS='A' )" .
			" inner join gft_work_area_master wam on (wam.gwm_code=c.get_work_area_type) " .
			" where ((wam.gwm_code in (1,2) and terr_id=c.GET_TERRITORY_ID) or " .
			" (wam.gwm_code=3 and area_id=c.GET_TERRITORY_ID) or (wam.gwm_code=4 and region_id=c.GET_TERRITORY_ID) or" .
			" (wam.gwm_code=5 and zone_id=c.GET_TERRITORY_ID) ) and gem_office_empid!=0  ";
	if($area_id!=0){ $select_q.=" and area_id  = '$area_id' "; }
	else if($region_id!=0){ $select_q.=" and region_id  = '$region_id' "; }
	else if($zone_id!=0){ $select_q.=" and zone_id  = '$zone_id' "; }
	$result_q=execute_my_query($select_q);
	$qdata=mysqli_fetch_array($result_q);
	$emp_list=$qdata[0];
	return $emp_list;
}

/**
* @param string $uid
* @param string $status
* @param string $roleid
* @param boolean $only_gft_emp
* @param boolean $select_any
* @param int $team_id
* @param string[int] $emp_id_arr
*
* @return string[int][int]
*/

function get_emp_master($uid=null,$status='',$roleid=null,$only_gft_emp=true,$select_any=false,$team_id=0,$emp_id_arr=null){
    //NOTE: Changed  the defualt value of $status='A' to $status=''
    $emp_list=/*. (string[int][int]) .*/ array();
    $query_emp="select em.gem_emp_id,em.GEM_EMP_HR_ID,em.gem_emp_name,em.gem_role_id,em.gem_mobile,em.gem_email,em.gem_status,GER_REPORTING_EMPID,GEW_EMAIL," .
        "em.GEM_TITLE,e.gem_email 'rep_email',e.gem_emp_name 'rep_name',em.GEM_LEAD_CODE,e.gem_mobile 'rep_mobile',em.GEM_OFFICE_EMPID, " .
        "em.GEM_DOB,em.GEM_GENDER,em.GEM_RELIANCE_NO,em.GEM_PROFILE_URL,em.GEM_EMERGENCY_NUMBER,em.GEM_PERSONAL_EMAIL, ".
        "em.GEM_BLOOD_GROUP,em.GEM_ARP_START_DATE,em.GEM_PERMENANT_ADDRESS,em.GEM_CURRENT_ADDRESS,em.GEM_CURRENT_ADDRESS, ".
        "em.GEM_LAPTOP_HARDDISK_ID,em.GEM_MOBILE_MAC_ID,em.GEM_LABTOP_MAC_ID,em.GEM_COURIER_ADDR,em.GEM_DOJ, ".
        "ifnull(GEP_VAL,'') GEP_VAL,GEW_DESC from gft_emp_master em ".
        "left join gft_emp_reporting rp on (GER_EMP_ID=em.gem_emp_id and GER_STATUS='A') ".
        "left join gft_emp_master e on e.GEM_EMP_ID=rp.GER_REPORTING_EMPID " .
        "left join gft_emp_id_proof_dtl on (GEP_EMP_ID=em.gem_emp_id and GEP_TYPE=2) ".
        "left join gft_emp_web_display on (GEW_ID=em.WEB_GROUP) where 1  ";
    if($only_gft_emp==true){
        $query_emp.=" and em.gem_office_empid!='0' ";
    }
    if($uid!=""){
        $query_emp.=" and em.gem_emp_id='$uid'";
    }
    if($team_id!=0){
        $query_emp .= " and em.WEB_GROUP='$team_id' ";
    }
    if(is_array($roleid)){
        $roleid=implode(',',/*. (string[int]) .*/ $roleid);
    }
    if($roleid!=null and trim($roleid)!=''){
        $query_emp.=" and em.gem_role_id in ($roleid) ";
    }
    if( is_array($emp_id_arr) && (count($emp_id_arr) > 0) ){
        $query_emp .= " and em.gem_emp_id in (".implode(",", $emp_id_arr).") ";
    }
    $query_emp.=($status!=''?" and em.gem_status='$status'":'');
    $query_emp.=" order by em.gem_emp_name ";
    $result_emp=execute_my_query($query_emp,'common_util.php',true,false,1);
    $i=0;
    if($select_any==true){
        $emp_list[$i][0]='0';
        $emp_list[$i][1]='Any';
        $i++;
    }
    
    $c=0;
    while($row=mysqli_fetch_array($result_emp)){
        $emp_list[$i][0]=$row['gem_emp_id'];
        $emp_list[$i][1]=$row['gem_emp_name'];
        $emp_list[$i][2]=$row['gem_role_id'];
        $emp_list[$i][3]=$row['gem_mobile'];
        $emp_list[$i][4]=$row['gem_email'];
        $emp_list[$i][5]=$row['gem_status'];
        $emp_list[$i][6]=$row['GER_REPORTING_EMPID'];
        $emp_list[$i][7]=$row['GEM_TITLE'];
        $emp_list[$i][8]=$row['rep_email'];
        $emp_list[$i][9]=$row['rep_name'];
        $emp_list[$i][10]=($row['gem_role_id']=='21')?'1':'0';  //is Channel Partner
        $emp_list[$i][11]=$is_cp_employee =($row['gem_role_id']=='26')?'1':'0';  //is CP Employee
        $emp_list[$i][12]='0'; //to update partner id if its cp employee
        if($is_cp_employee) {
            $query_cp = " select CGI_EMP_ID from gft_leadcode_emp_map ".
                " join gft_cp_info on (GLEM_LEADCODE = CGI_LEAD_CODE) ".
                " where GLEM_EMP_ID='$uid'";
            $res_cp = execute_my_query($query_cp);
            if($data_cp = mysqli_fetch_array($res_cp)) {
                $emp_list[$i][12] = $data_cp['CGI_EMP_ID'];
            }
        }
        $emp_list[$i][13]=($row['gem_role_id']=='73')?'1':'0';  //is Referral Partner
        $emp_list[$i][14]=$row['GEM_LEAD_CODE'];
        $emp_list[$i][15]=$row['rep_mobile'];
        $emp_list[$i][16]=$row['GEM_OFFICE_EMPID'];
        $emp_list[$i][17]=$row['GEW_EMAIL'];
        $emp_list[$i][18]=$row['GEM_DOB'];
        $emp_list[$i][19]=$row['GEM_GENDER'];
        $emp_list[$i][20]='';
        $emp_list[$i][21]='';
        $emp_list[$i][22]=$row['GEM_RELIANCE_NO'];
        $emp_list[$i][23]=$row['GEM_PROFILE_URL'];
        $emp_list[$i][24]=$row['GEM_EMP_HR_ID'];
        $emp_list[$i][25]=(string)$row['GEM_EMERGENCY_NUMBER'];
        $emp_list[$i][26]=(string)$row['GEM_PERSONAL_EMAIL'];
        $emp_list[$i][27]=(string)$row['GEM_BLOOD_GROUP'];
        $emp_list[$i][28]=$row['GEM_ARP_START_DATE'];
        $emp_list[$i][29]=$row['GEM_PERMENANT_ADDRESS'];
        $emp_list[$i][30]=$row['GEM_CURRENT_ADDRESS'];
        $emp_list[$i][31]=$row['GEM_COURIER_ADDR'];
        $emp_list[$i][32]=$row['GEM_LAPTOP_HARDDISK_ID'];
        $emp_list[$i][33]=$row['GEM_LABTOP_MAC_ID'];
        $emp_list[$i][34]=$row['GEM_MOBILE_MAC_ID'];
        $emp_list[$i][35]=$row['GEM_DOJ'];
        $emp_list[$i][36]=$row['GEP_VAL'];
        $emp_list[$i][37]=$row['GEW_DESC'];
        $i++;
        $c++;
    }
    
    //NOTE: Do enable the following for Debug
    //	if ($c == 0){
    //		error_log("query returns no record." . $query_emp);
    //	}
    
    return $emp_list;
}


/**
 * @param string $uid
 * @param string $status
 * @param string $roleid
 *
 * @return string[int][int]
 */
function get_all_emp_master($uid=null,$status=null,$roleid=null){
	//id,name,role,mobileno
	//$emp_list="";
	$emp_list=/*. (string[int][int]) .*/ array();
 	$query_emp="select gem_emp_id,gem_emp_name,gem_role_id,gem_mobile from gft_emp_master where 1  ";
 	
 	if($uid!=""){
 	  $query_emp.=" and gem_emp_id='$uid'";
 	}
 	
 	if($status!=""){
 	  $query_emp.=" and gem_status='$status'";
 	}
 	
 	if($roleid!=""){
 	  $query_emp.=" and gem_role_id='$roleid'";
 	}
 	
	$query_emp.=" order by 2";
	$result_emp=execute_my_query($query_emp,'common_util.php',true,false,2);
	$i=0;
	while($row=mysqli_fetch_array($result_emp)){
	  $emp_list[$i][0]=$row['gem_emp_id'];
	  $emp_list[$i][1]=$row['gem_emp_name'];
	  $emp_list[$i][2]=$row['gem_role_id'];
	  $emp_list[$i][3]=$row['gem_mobile'];
	  $i++;  
    }

    return $emp_list;
}

/**
 * @param string $text
 * 
 * @return string
 */
function get_valid_text($text){
	$text=preg_replace('/[\s\s]+ | [\n\t\r]/', ' ', trim($text));
	return preg_replace('/[^[:print:]]/','',$text);
}

/**
 * @param string $uid
 * @param string $status
 * 
 * @return string[int][int]
 */

function get_emp_ableto_place_orders($uid=null,$status=null){
	$emp_list=/*. (string[int][int]) .*/ array();
 	$query_emp="select distinct(gem_emp_id) emp_id,gem_emp_name,gem_role_id,gem_mobile from " .
 			" gft_emp_master,gft_role_group_master " .
 			" where gem_office_empid!='0' and gem_role_id=grg_role_id and grg_group_id in (5,6,36,8,20,12) ";
 	if($uid>0){
		$query_emp.=" and gem_emp_id='$uid'";
 	}
 	if($status!=""){
		$query_emp.=" and gem_status='$status'";
 	}
	$query_emp.=" order by 2";
	$result_emp=execute_my_query($query_emp,'common_util.php',true,false,2);
	$i=0;
	while($row=mysqli_fetch_array($result_emp)){
		$emp_list[$i][0]=$row['emp_id'];
		$emp_list[$i][1]=$row['gem_emp_name'];
		$emp_list[$i][2]=$row['gem_role_id'];
		$emp_list[$i][3]=$row['gem_mobile'];
		$i++;  
	}
    return $emp_list;
}

/**
 * @return string[int][int]
 */
function get_installation_status_list(){
	$temp=/*. (string[int][int]) .*/ array(array(1,"Installed"),array(2,"Not Yet Installed"));
 	return $temp;
}

/**
 * @return string[int][int]
 */
function get_collection_status_list(){
 	$temp=/*. (string[int][int]) .*/ array(array(1,"Fully Collected"),array(2,"Partially Collected"),array(3,"Outstanding"),array(4,"Nill payment Received")
 		,array("5","Outstanding Less than 30 Days"),array("6","Outstanding 30 - 60 Days")
 		,array("7","Outstanding 60 - 90 days"),array("8","Outstanding 90+ days"), array("9","Dis-Agreed by EXE"),
 	    array("10","Not Installed Outstanding"), array("11","Service Order Outstanding"),array("12","Invoice Outstanding"));
 	return $temp;
}

/**
 * @return string[int][int]
 */
function get_installation_status_agewiselist(){
	$temp=/*. (string[int][int]) .*/ array(array("1","Assigned"),array("2","Unassigned"),array("5","Pending Less than 30 Days"),array("6","Pending 30 - 60 Days")
 		,array("7","Pending 60 - 90 days"),array("8","Pending 90+ days"));
 	return $temp;
}

/**
 * @return string[int][int]
 */
function get_logical_operation_filter(){
	$temp=/*. (string[int][int]) .*/ array(0=>array("Equal to","Equal to"),array("Less than","Less than"),array("Greater than","Greater than"));
	return $temp;
}

/**
 * @return int[int][int]
 */
function get_year_list(){
	$temp=/*. (int[int][int]) .*/ array();
	for($i=2000,$j=0;$i<2030;$i++,$j++){
		$temp[$j][0]=$i;
		$temp[$j][1]=$i;
	}
	return $temp;
}

/**
 * @return string[int][int]
 */
function get_month_name(){
	$month=array(array("01", "January"),array("02" , "February"),array("03" ,"March"),
	array("04","April"),array("05","May"),array("06", "June"),
	array("07","July"),array("08","August"),array("09","September"),
	array("10","October"),array("11","November"),array("12" , "December"));
	return $month;
}

/**
 * @param string $month
 *
 * @return string
 */
function get_month_name_bh_format($month){
	switch($month){
		case '1' : return "gghs_jan01";									
		case '2' : return "gghs_feb02";
		case '3' : return "gghs_mar03";
		case '4' : return "gghs_apl04";
		case '5' : return "gghs_may05";
		case '6' : return "gghs_jun06";
		case '7' : return "gghs_jul07";
		case '8' : return "gghs_aug08";
		case '9' : return "gghs_sep09";
		case '10' : return "gghs_oct10";
		case '11' : return "gghs_nov11";
		case '12' : return "gghs_dec12";

		default : return "Default";
	}
}

/**
 * @param string $month
 * 
 * @return string
 */
function get_month_name_for($month){
	switch($month){
		case '1' : return "January";
		case '2' : return "February";
		case '3' : return "March";
		case '4' : return "April";
		case '5' : return "May";
		case '6' : return "June";
		case '7' : return "July";
		case '8' : return "August";
		case '9' : return "September";
		case '10' : return "October";
		case '11' : return "November";
		case '12' : return "December";
		
		default : return "Default";
	}
}

/**
 * @param string $id
 * @param boolean $include_partner_name
 * 
 * @return string
 */
function get_name($id,$include_partner_name=false){
	$name="Id not found";
	if($id!=''){
		$query="select gem_emp_name from gft_emp_master where gem_emp_id='$id'";
		$result=execute_my_query($query,'common_util.php',true,false,2);
		if($qdata=mysqli_fetch_array($result)){
			$name=$qdata['gem_emp_name'];
			if($include_partner_name){
				$sql1 = " select GLH_CUST_NAME from gft_leadcode_emp_map ".
						" join gft_lead_hdr on (GLH_LEAD_CODE=GLEM_LEADCODE) ".
						" join gft_cp_info on (CGI_LEAD_CODE=GLEM_LEADCODE and CGI_EMP_ID!=GLEM_EMP_ID) ".
						" where GLEM_EMP_ID='$id' ";
				$res1 = execute_my_query($sql1);
				if($row1 = mysqli_fetch_array($res1)){
					$partner_name = $row1['GLH_CUST_NAME'];
					$name .= ($partner_name!='')?" ($partner_name) ":"";
				}
			}
		}		
	}
	return $name;
}

/**
 * @param string $id
 *
 * @return string
 */
function get_short_name($id){
	if($id!=''){
		$query="select ifnull(gem_short_name,gem_emp_name) gem_emp_name from gft_emp_master where gem_emp_id='$id'";
		$result=execute_my_query($query,'common_util.php',true,false,2);
		$qdata=mysqli_fetch_array($result);
		$name=$qdata['gem_emp_name'];
		return $name;
	}else {
		$name="Id not found";
		return $name;	
	}
}

/**
 * @param string $uid
 *
 * @return string
 */
function get_email_addr($uid){
 	$query_emailid="select gem_email from gft_emp_master em  where em.gem_emp_id='$uid' and gem_status='A'";		        
 	$result_emailid=execute_my_query($query_emailid,'common_util.php',true,false,2);
 	$first_email_id = "";
 	if($email_id=mysqli_fetch_array($result_emailid)){
	   $first_email_id=$email_id[0];
 	}
    return $first_email_id;
}


/**
 * @param string $emp_id
 * @param boolean $return_count
 * @param boolean $return_list
 * @param string $status
 * @param string $from_dt
 * @param string $to_dt
 *
 * @return string[int][string]
 */

function get_team_list($emp_id,$return_count=false,$return_list=true,$status='A',$from_dt=null,$to_dt=null){
	//NOTE: $return_count not used
 	$query="select gem_emp_id,gem_emp_name,gem_email from gft_emp_master em " ;
 	if($emp_id!=null and $emp_id!='' and $emp_id!=0){
		$query.=" inner join gft_emp_manager_relation on(gmr_emp_id=em.gem_emp_id and " .
				"( gmr_emp_id='$emp_id' or (gmr_terri_m =$emp_id and gmr_terri_m_ck=true) " .
    			" or (gmr_area_m=$emp_id and gmr_area_m_ck=true) " .
    			" or (gmr_region_m='$emp_id' and gmr_region_m_ck=true) " .
    			" or (gmr_zone_m='$emp_id') ) )";
 	}
 	if($status=='A'){
	 	$query.="where gem_status='A' ";
 	}
 	if($from_dt!=null && $to_dt!=null){
 		$query.=" and ((gem_status='I' and gem_dor>'$from_dt') or (gem_doj<'$to_dt' and gem_status='A') )"; 
 	}
 	$emp_list=/*. (string[int][string]) .*/ array();
 	$result=execute_my_query($query,'common_util.php',true,false,2);
 	//$count_num_rows=mysqli_num_rows($result);
 	//if($return_count==true){
 	//	return $count_num_rows;
 	//}else if($return_list==true and $count_num_rows>0){
 		$i=0;
 		while($qdata=mysqli_fetch_array($result)){
 			$emp_list[$i]['eid']=$qdata['gem_emp_id'];
 			$emp_list[$i]['name']=$qdata['gem_emp_name'];
 			$i++;
 		}
 		return $emp_list;
 	//}else{
 	//	return null;
 	//}
}          

/**
 * @param string $emp_id
 * 
 * @return string[int]
 */
function get_team_members($emp_id) {
	$listof_emps = /*. (string[int]) .*/array();
	$query="select GER_EMP_ID from gft_emp_reporting where GER_REPORTING_EMPID='$emp_id' and GER_STATUS='A'";
	$res=execute_my_query($query);
    while($data=mysqli_fetch_array($res)){
    	$listof_emps[]=$data['GER_EMP_ID'];
    }
    return $listof_emps;
}

/**
 * @param string $email_id
 *
 * @return string
 */
function check_exist_email_id($email_id){
	$error_msg_login='';
	$validate_login_sql="select count(*) from gft_login_master where glm_login_name='$email_id'";
	$re_vlq=execute_my_query($validate_login_sql,'',true,false);
	if($rows=mysqli_fetch_array($re_vlq)){
		 $flag_lid=$rows[0];
		if($flag_lid>0){
			$error_msg_login="Already Existing Email id. ";
		}
	}
	return $error_msg_login;		
}//END of check_exist_email_id

/**
 * @param int $length
 *
 * @return string
 */
function generatePassword ($length = 8){
	$password = "";
	$possible = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
	$i = 0;
	while ($i < $length) {
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		if (!strstr($password, $char)) {
			$password .= $char;
			$i++;
		}
	}
	return $password;
}

/**
 * @param string $emp_id
 * @param string $user_name
 * @param boolean $make_emailid_as_passwd
 * @param int $mail_template_id
 * 
 * @return string
 */
function generate_login($emp_id,$user_name,$make_emailid_as_passwd=true,$mail_template_id=35){
	$password=generatePassword(8);
	$crypwd=sam_password_hash(md5($password));	
	$created_date=date('Y-m-d');
	if($user_name!=''){
		$cquery=" select GLM_EMP_ID,GLM_LOGIN_NAME from gft_login_master " .
				" join gft_emp_master em on (gem_emp_id=glm_emp_id and gem_status='A') " .
				" where (GLM_LOGIN_NAME='$user_name' or GEM_EMAIL ='$user_name' ) ".(($emp_id!='' and $emp_id!=0 )? " and glm_emp_id='$emp_id' ":"");
		$result= execute_my_query($cquery,'common_util.php',true,false,2);
		if(($data=mysqli_fetch_array($result)) and mysqli_num_rows($result)==1){
			$uname=$data['GLM_LOGIN_NAME'];
			$emp_id=$data['GLM_EMP_ID'];
			if($make_emailid_as_passwd==true){
				$query="update gft_login_master set GLM_LOGIN_NAME='$uname', glm_password='$crypwd', " .
						"GLM_NEED_PASSWORD_RESET=1 where glm_emp_id='$emp_id' ";
				$res=execute_my_query($query,'common_util.php',true,false,2);
				if(!$res) {
					die ("User name, pasword change error");
				}
				if(isset($uid)){
					$email_from=get_email_addr($uid);
				}else{
					$email_from=get_samee_const("ADMIN_TEAM_MAIL_ID");
				}
				$emp_details=get_emp_master($emp_id,'A',$roleid=null,false);
			    /*sam password reset*/	
			 	$db_sms_content_config=array(
				'emp_name'=>array($emp_details[0][1]),
				'user_name'=>array($uname),'pwd'=>array($password));
				$message2=htmlentities (get_formatted_content($db_sms_content_config,92));
			    entry_sending_sms($emp_details[0][3],$message2,92,$emp_details[0][0],0,$emp_details[0][0]);
				$db_sms_content_config=array(
		    	'Employee_Name'=>array($emp_details[0][1]),
			    'user_name'=>array($uname),
			    'user_password'=>array($password));
    	        $message=get_formatted_mail_content($db_sms_content_config,$category=16,$mail_template_id);
	            $body_message=$message['content'];
	            $content_type=$message['content_type'];
	            $subject=$message['Subject'];
	            $at_file=$message['Attachment'];
	            send_mail_function($email_from,explode(',',$emp_details[0][4]),$subject,$body_message,$at_file,null,16,$content_type,$email_from,false,false,$fromname=null);
	            $str="Your password has been reset. Please check your registered mail / sms for password";
	            return $str;
			}
		}else if($emp_id!=''){
			$query="insert into gft_login_master (glm_emp_id,glm_login_name,glm_password,glm_created_date,GLM_NEED_PASSWORD_RESET) values" .
					"('$emp_id','$user_name','$crypwd','$created_date',1);";
			$res=execute_my_query($query,'common_util.php',true,false,2);
			if(!$res) {
				die ("Login is not created");
			}
		}else{
			$password="User name not Found !.Please check Your username / Email id , Details not match.";		
		}
    }else{
			$password="User name not Found !.Please check Your username / Email id , Details not match.";
	}
    return $password;
}	

/**
 * @param string $uid
 * @param boolean $reporting_masters
 * @param int[int] $skip_emp_list
 *
 * @return string[int]
 */
function get_email_addr_reportingmaster($uid,$reporting_masters=true,$skip_emp_list=null){
	$skip_emp_q='';
 	$query_emailid="select gem_email from gft_emp_master em ";
 	if($skip_emp_list!=null){
 		$skip_emp_q=" and gem_emp_id not in (".implode(',',$skip_emp_list).")";
 	}
 	if($reporting_masters=="true"){	
 		$query_emailid.="left join gft_emp_reporting  h1 on (h1.ger_reporting_empid=em.gem_emp_id and h1.ger_status='A' ) " .
 				" left join gft_emp_reporting  h2 on (h2.ger_reporting_empid=h1.ger_emp_id and h2.ger_status='A' )" .
 				" left join gft_emp_reporting  h3 on (h3.ger_reporting_empid=h2.ger_emp_id and h3.ger_status='A' )" .
 				" where ((h1.ger_emp_id='$uid' or h2.ger_emp_id='$uid' or h3.ger_emp_id='$uid') ) " .
 				" and gem_status='A' and gem_email!='' $skip_emp_q ";
	}else {
		$query_emailid.="inner join gft_emp_reporting  h1 on (h1.ger_emp_id='$uid' and h1.ger_reporting_empid=em.gem_emp_id and h1.ger_status='A' ) ";                                     
		$query_emailid.=" where gem_status='A' $skip_emp_q "; 
	}
    $query_emailid.=" group by gem_email ";	
	$result_emailid=execute_my_query($query_emailid,'common_util.php',true,false,2);
   	$i=0;
	$email_id=/*. (string[int]) .*/ array();
	while($q_id=mysqli_fetch_array($result_emailid)){
		$email_id[$i]=$q_id[0];
		$i++;
	}
    return $email_id;
}

/** 
 * @param string $uid
 *
 * @return string[string]
 */
function get_emp_manager_level_dtl($uid){
	$man_id=/*. (string[string]) .*/ array();

	$query="SELECT tm.gem_emp_id tmanager, am.gem_emp_id amanager, rm.gem_emp_id rmanager,zm.gem_emp_id zmanager " .
			" FROM gft_emp_manager_relation " .
			" left join gft_emp_master tm on (gmr_terri_m!=gmr_emp_id and gmr_terri_m=tm.gem_emp_id) " .
			" left join gft_emp_master am on (gmr_area_m!=gmr_emp_id and gmr_area_m_ck=true and gmr_area_m=am.gem_emp_id) " .
			" left join gft_emp_master rm on (gmr_region_m!=gmr_emp_id and gmr_region_m_ck=true and gmr_region_m=rm.gem_emp_id) " .
			" left join gft_emp_master zm on (gmr_zone_m!=gmr_emp_id and gmr_zone_m=zm.gem_emp_id) " .
			" WHERE gmr_emp_id=$uid ";
	$result =execute_my_query($query,'common_util.php',true,false,2);
	while($data=mysqli_fetch_array($result)){
		$man_id['tmanager']=(isset($data['tmanager'])?$data['tmanager']:'');
		$man_id['amanager']=(isset($data['amanager'])?$data['amanager']:'');
		$man_id['rmanager']=(isset($data['rmanager'])?$data['rmanager']:'');
		$man_id['zmanager']=(isset($data['zmanager'])?$data['zmanager']:'');
	}		
	return $man_id;
}


/**
 * @param string $uid
 * @param string $status
 * @param string $roleid
 * @param boolean $only_gft_emp
 * 
 * @return string
 */
function get_emp_manager_mail_id($uid,$status='A',$roleid=null,$only_gft_emp=true){
	$str='';

	if(!empty($uid)){
		$query="SELECT tm.GEM_EMAIL tmemail, am.GEM_EMAIL amemail, rm.GEM_EMAIL rmemail,zm.GEM_EMAIL zmemail " .
				" FROM gft_emp_manager_relation " .
				" left join gft_emp_master tm on (gmr_terri_m!=gmr_emp_id and gmr_terri_m=tm.gem_emp_id and tm.GEM_STATUS='A') " .
				" left join gft_emp_master am on (gmr_area_m!=gmr_emp_id and gmr_area_m_ck=true and gmr_area_m=am.gem_emp_id and am.GEM_STATUS='A') " .
				" left join gft_emp_master rm on (gmr_region_m!=gmr_emp_id and gmr_region_m_ck=true and gmr_region_m=rm.gem_emp_id and rm.GEM_STATUS='A') " .
				" left join gft_emp_master zm on (gmr_zone_m!=gmr_emp_id and gmr_zone_m=zm.gem_emp_id and zm.GEM_STATUS='A') " .
				" WHERE gmr_emp_id=$uid ";
		$result =execute_my_query($query,'common_util.php',true,false,2);
		
		while($data=mysqli_fetch_array($result)){
			$str=(isset($data['tmemail'])?$data['tmemail'].',':'').
				(isset($data['amemail'])?$data['amemail'].',':'').
				(isset($data['rmemail'])?$data['rmemail'].',':'').
				(isset($data['zmemail'])?$data['zmemail']:'');
		}
		//If employee not mapped based on zone
		$result = execute_my_query("select GEM_EMAIL from gft_emp_reporting 
									INNER JOIN gft_emp_master em ON(GER_REPORTING_EMPID=GEM_EMP_ID)
									where GER_EMP_ID='$uid' AND GER_STATUS='A'");
		while($row=mysqli_fetch_array($result)){
			$str = $str.",".$row['GEM_EMAIL'];
		}
		$change_to_array=explode(',',$str);
		$unique_array=/*. (string[int]) .*/ array_unique($change_to_array);
		$str=implode(',',$unique_array);
	}
	return $str;
}

/**
 * @param string $emp_id
 * @param boolean $reporting_masters 
 * 
 * @return string[int][int]
 */
function get_mobileno_reportingmaster($emp_id,$reporting_masters=true){

	$email_id =/*. (string[int][int]) .*/ array();

 	$query_emailid="select gem_mobile,gem_emp_id,gem_email from gft_emp_master em ".
 				" left join gft_emp_reporting  h1 on (h1.ger_reporting_empid=em.gem_emp_id and h1.ger_status='A' )" .
 				" left join gft_emp_reporting  h2 on (h2.ger_reporting_empid=h1.ger_emp_id and h2.ger_status='A' )" .
 				" left join gft_emp_reporting  h3 on (h3.ger_reporting_empid=h2.ger_emp_id and h3.ger_status='A' )" .
 				" where (h1.ger_emp_id='$emp_id' or h2.ger_emp_id='$emp_id' or h3.ger_emp_id='$emp_id')  " .
 				" and gem_status='A' and gem_email!='' and gem_mobile!='' group by gem_emp_id ";
	$result_emailid=execute_my_query($query_emailid,'common_util.php',true,false,2);
	$i=0;
	while($q_id=mysqli_fetch_array($result_emailid)){
		$email_id[$i][0]=$q_id['gem_mobile'];//mobileno
		$email_id[$i][1]=$q_id['gem_emp_id'];//emp id
		$i++;
	}
    return $email_id;
}

/**
 * @param string $table_title
 * @param string $tooltip
 * @param string $tooltip_width
 * @param boolean $old
 * @param boolean $mail_link
 * @param string $history_link
 *
 * @return void
 */
function print_dtable_header($table_title,$tooltip=null,$tooltip_width="300",$old=true,$mail_link=true,$history_link=null){
	$access_page1=$_SERVER['SCRIPT_NAME'];
	global $group_id,$me; 
	$report_id='';$menu_name='';
	if($tooltip!=''){
		$tooltip=" onMouseover=\"ddrivetip('$tooltip','#6dffc2',$tooltip_width);\" onMouseout=\"hideddrivetip();\" ";
	}
	if($group_id==1){
	    $get_bname=basename($access_page1);	
		$query=" select menu_name,mid from gft_menu_master where menu_path='$get_bname' ";
		$result=execute_my_query($query,$me,true,false,3);
		if($data=mysqli_fetch_array($result)){
			$menu_name=$data['menu_name'];
			$report_id=$data['mid'];
		}
		$today_date=date('Y-m-d');
		$table_title="<a href=\"accesspage_report.php?menu_path=$report_id&amp;for_date=$today_date&amp;to_date=$today_date&amp;emp_name=&amp;emp_code=&amp;location=1&amp;menu_name=".urlencode($menu_name)."\" target=\"new\" $tooltip >$table_title</a>";
	}else{
		$table_title="<a href=\"\" $tooltip >$table_title</a>";
	}
	$uid=(string)$_SESSION['uid'];
	if($mail_link){
	$send_mail="send_mail_to_other.php?id=$uid";
	$send_mail_link="<a href=\"javascript:call_popup('$send_mail',4);\"><img  src=\"images/emails.gif\" alt=\"send mail\" hspace=\"3\" align=\"middle\" border=\"0\"></a>&nbsp";
	$table_title.="&nbsp;".$send_mail_link;
	}
	if($old==true){
echo<<<END
<table  cellpadding="0" cellspacing="0" border="0" width="99%" align="center" ><tbody>
<tr><td>
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
<td vAlign="top" align="left" width="5px" style="background-image:url(images/header_start.gif); height:18px;">
<img alt="" src="images/header_start.gif" border="0"></td>
<td class="formHeader" style="background-image:url(images/header_tile.gif); height:18px;" vAlign="middle" align="left" noWrap width="20%">
$table_title</td>
<td vAlign="top" align="left"  width="5px"  style="background-image:url(images/header_end.gif); height:18px;"><img alt="" src="images/header_end.gif" border="0"></td>
<td NOWRAP>&nbsp;&nbsp;<B> $history_link </B></td>
<!--<td width='100%'><img alt="" height='1' src='images/blank.gif'></td> --> 
</tr></table></td></tr><tr><td>
END;
	}else{
echo<<<END
<table  cellpadding="0" cellspacing="0" border="solid" align="center" width="100%" ><tbody>
<tr  class="top_table_Header" ><td width='75%'><a href="">$table_title</a>
<span align='right'>$history_link</span></td></tr>
<tr><td>
END;
	}
}

/**
 * @param int $cnt
 * @param int $records_per_page
 *
 * @return mixed[string]
 */
function get_dtable_navigation_random($cnt,$records_per_page=25){
	$nav_struct = /*. (mixed[string]) .*/ array();	
	$total_num_records=$cnt;
	if(empty($_GET['next_page'])){
   		$_GET['next_page']=0;
	}
	$cur_page=(int)$_GET['next_page'];
	$page_num=$cur_page+1;
	$record=$cur_page * $records_per_page+$records_per_page;
	if($record>$total_num_records){
  		$record=$total_num_records;
	}
	$total_num_page=$last_page_num=ceil($total_num_records/$records_per_page);
	$limit_str="LIMIT ".$cur_page*$records_per_page.",".$records_per_page;
	$from=$cur_page*$records_per_page;
	if($total_num_records>0){
		$from=$from+1;
	}
	$nav_struct["from"]=$from;
	$nav_struct["record"]=$record;	
	$nav_struct["total_num_records"]=$total_num_records;
	$nav_struct["total_num_page"]=$total_num_page;
	$nav_struct["page_num"]=$page_num;
	$nav_struct["cur_page"]=$cur_page;
	$nav_struct["limit_str"]=$limit_str;
	return $nav_struct;
}

/**
 * @param int $cnt
 * @param int $records_per_page
 *
 * @return mixed[string]
 */
function get_dtable_navigation_struct($cnt,$records_per_page=0){
	$total_num_page=0;
	$nav_struct = /*. (mixed[string]) .*/ array();
	//if(!isset($records_per_page)) {	
	//	$records_per_page=(isset($_REQUEST['page_limit'])?(int)$_REQUEST['page_limit']:25);
	//}
	if($records_per_page === 0){	
		$records_per_page_str = isset($_REQUEST['page_limit'])?(string)$_REQUEST['page_limit']:'25';
		if ($records_per_page_str == 'all'){
			$records_per_page=-1;
		}else{
			$records_per_page=(int)$records_per_page_str;
		}
	}
	$total_num_records=$cnt;
	if( ($records_per_page=='all' or $records_per_page == -1) and $cnt!=0){
		$records_per_page= (int)$cnt;
	}
	if(empty($_REQUEST['next_page'])){
   		$_REQUEST['next_page']=0;
	}
	$records_per_page = (int)$records_per_page;
	$cur_page=(int)$_REQUEST['next_page'];
	$page_num=$cur_page+1;
	$record=$cur_page * $records_per_page + $records_per_page;
	if($record>$total_num_records){
  		$record=$total_num_records;
	}
	if($cnt!=0){
		$total_num_page=$last_page_num=(int)ceil($total_num_records/$records_per_page);
	}
	$start_of_row = $cur_page*$records_per_page;
	$limit_str="LIMIT ". $start_of_row .",".$records_per_page;
	$from=$cur_page*$records_per_page;
	if($total_num_records>0){
		$from=$from+1;
	}
	$nav_struct['page_total']=($total_num_page<=1?false:true);
	$nav_struct["from"]=$from;
	$nav_struct["record"]=$record;	
	$nav_struct["total_num_records"]=$total_num_records;
	$nav_struct["total_num_page"]=$total_num_page;
	$nav_struct["page_num"]=$page_num;
	$nav_struct["cur_page"]=$cur_page;
	$nav_struct["limit_str"]=$limit_str;
	$nav_struct['start_of_row']=$start_of_row;
	$nav_struct['records_per_page']=$records_per_page;
	return $nav_struct;
}

/**
 * @param string $re_for
 *
 * @return string
 */
function php_extra_args($re_for){
	$pagereq=$_REQUEST;
	$php_extra_args="";
	$php_extra_args_l="";
	foreach($pagereq as $reqkey => $reqval){
		if(is_array($reqval)){
			foreach($reqval as $arva)
				$php_extra_args.="&amp;".urlencode($reqkey)."[]=".(string)$arva;
		}else{
			if($reqkey!='next_page' and $reqkey!='PHPSESSID' and $reqkey!='sortbycol' and $reqkey!='sorttype'){
				$php_extra_args.="&amp;".urlencode($reqkey)."=".urlencode((string)$reqval);
			}
			if(($reqkey=='sortbycol' or $reqkey=='sorttype') and $re_for=='navigation'){
				$php_extra_args.="&amp;".urlencode($reqkey)."=".urlencode((string)$reqval);
			}
		}
	}
	return substr($php_extra_args,5);
}
/**
 * @param string $lead_codes
 * @return string
 */
function get_mobile_nos_of_lead_codes($lead_codes) {
	$qry = '';
	if($lead_codes!='') {
		$qry = " select distinct gcc_contact_no as mobile_nos from gft_customer_contact_dtl where gcc_lead_code in ($lead_codes) and gcc_contact_type='1' and gcc_contact_status='A' ";
	}
	return $qry;
}
/** 
 * @param int $cnt
 * @param mixed[string] $nav_struct
 * @param string $php_page
 * @param string $export_page
 * @param string $e_query
 * @param string $heading
 * @param string $sp
 * @param int $htmltype
 * @param boolean $show_nav
 * @param array $post_array
 * @param string $heading2
 * @param int $sms_category
 * @param int $email_category
 * @param string $to_whom
 * @param string[int] $take_sort_array_for_export
 * @param int $export_index
 * 
 * @return void
 */
function print_dtable_navigation($cnt,$nav_struct,$php_page,$export_page=null,
$e_query=null,$heading='',$sp='',$htmltype=1,$show_nav=true,$post_array=null,$heading2=null,
$sms_category=0,$email_category=0,$to_whom=null,$take_sort_array_for_export=null,$export_index=0){
	$php_page_extra_args=php_extra_args('navigation');
	//$e_query=preg_replace('([[:cntrl:]]) {3,15}',' ',$e_query);
	if($htmltype>0)
	$e_query=htmlentities($e_query);
	$limit_show=(string)$nav_struct["from"]." - ".(string)$nav_struct["record"]." 0f ".(string)$nav_struct["total_num_records"];
	$page_num=(int)$nav_struct["page_num"];
	$cur_page=(int) $nav_struct["cur_page"];
	$total_num_page=(int)$nav_struct["total_num_page"];
	$start="[ Start ]";
	$prev="[ Prev ]";
	$next="[ Next ]";
	$end="[ End ]";
	if($page_num>1){
		$prev_page=$cur_page-1;
		$start= "<a href=\"$php_page?next_page=0&amp;$php_page_extra_args\">[ Start ]</a>";
		$prev= "<a href=\"$php_page?next_page=$prev_page&amp;$php_page_extra_args\">[ Prev ]</a>";
	}
	if($page_num<$total_num_page){
		$next_page=$cur_page+1;
		$last_page=$total_num_page-1;
		$next= "<a href=\"$php_page?next_page=$next_page&amp;$php_page_extra_args\" >[ Next ]</a>";
		$end= "<a href=\"$php_page?next_page=$last_page&amp;$php_page_extra_args\" >[ End ]</a>";
	}
	$navigation="[ ".$limit_show." ]".$start."".$prev."".$next."".$end."";
	if($start=="[ Start ]" and $prev=="[ Prev ]" and $next=="[ Next ]" and $end=="[ End ]"){
		$nav_struct['page_total']=false;
	}
	if($php_page=="daywise_activity_report.php"){
		$navigation=" ";
	}	
	if($php_page=="monthly_activity_report.php"){
		$navigation=" ";
	}
	if($php_page=="Performance_Metric.php"  or  $php_page=="report_format.php"){
	    $navigation=" ";
	}
	if($show_nav==false){
		$navigation=" Number of Rows:".$cnt." ";
	}
	if($php_page=="SalesReport_Productwise.php" or $php_page=="SalesReport_Areawise" 
		or $php_page=="SalesReport_Area_Product.php" or $php_page=="outstanding_report.php"
		or $php_page=="monthly_activity_review.php" or $php_page=="new_expense_form.php"){
	    $navigation=" ";
	}
	if(!is_numeric($cnt)){
		$navigation=" ";
	}
	$POST_VALUES="";
	if(is_array($post_array) && count($post_array)>0){
		foreach($post_array as $item => $val){
$POST_VALUES.=<<<END
<input type="hidden" name="{$item}csv" id="{$item}csv" value="$val">
END;
		}
	}
	$session_user=(string)$_SESSION['uid'];
	$session_user_name=$_SESSION['uname'];
	global $me, $only_receipts;
	$address_export_to_pdf='';
	if(in_array(basename($me), array("ass_upgrade_oppurtunity_report.php","cust_Reports.php","installation_report.php","tech_incomming_call_details.php","patch_applied_report.php","impl_training_assurance.php","customer_env_report.php","assigned_activity_schedule.php","hoto_report.php"))){
		if(!in_array(basename($me),array('impl_training_assurance.php',"customer_env_report.php","assigned_activity_schedule.php"))) {
		$address_export_to_pdf="&nbsp;&nbsp;[ <a href=\"$export_page\" id=\"exporttarget2\" class=\"formHeader\" onclick=\"pdf_address($export_index);return false;\" target=\"_blank\" title=\"Export to Pdf format \">Export Address Pdf </a> ]";
		$address_export_to_pdf.="&nbsp;&nbsp;[ <a href=\"$export_page\" id=\"exporttarget4\" class=\"formHeader\" onclick=\"pdf_address_add($export_index);return false;\" target=\"_blank\" title=\"Export to Pdf format \">Export Address Pdf Add. Barcode</a> ]";
		}
		if(basename($me)=="ass_upgrade_oppurtunity_report.php" or basename($me)=="tech_incomming_call_details.php"){
			$address_export_to_pdf.="&nbsp;&nbsp;[ <a href=\"$export_page\" id=\"exporttarget3\" class=\"formHeader\" onclick=\"pdf_asa_letter($export_index);return false;\" target=\"_blank\" title=\"Export to Pdf format \">Export ASA Letter Pdf </a> ]";
		}
		if(in_array(basename($me),array("ass_upgrade_oppurtunity_report.php","installation_report.php","patch_applied_report.php","cust_Reports.php","impl_training_assurance.php","customer_env_report.php","assigned_activity_schedule.php","hoto_report.php"))) {
			if(!in_array(basename($me),array("customer_env_report.php")) && is_employee_access_allowed_in_report($session_user, 'export_mobile', basename($me))) {
				$address_export_to_pdf.="&nbsp;&nbsp;[ <a href=\"$export_page\" id=\"exporttarget8\" class=\"formHeader\" onclick=\"cust_mobile_nos_txt($export_index);return false;\" target=\"_blank\" title=\"Export to txt format\">Export Customers Mobile Nos.</a> ]";
			}
			if( in_array(basename($me),array("ass_upgrade_oppurtunity_report.php","installation_report.php","impl_training_assurance.php","customer_env_report.php")) && is_employee_access_allowed_in_report($session_user, 'notification', basename($me)) ){
				$address_export_to_pdf.="&nbsp;&nbsp;[ <a href=\"$export_page\" id=\"exporttarget11\" class=\"formHeader\" onclick=\"push_notification($export_index);return false;\" target=\"_blank\" title=\"MyGofrugal or Windows notification\">Send Push Notification</a> ]";
			}
		}
	}
	if(in_array(basename($me),array('Emp_view.php','cp_analysis_detail.php'))){
		$address_export_to_pdf="&nbsp;&nbsp;[ <a href=\"$export_page\" id=\"exporttarget7\" class=\"formHeader\" onclick=\"pdf_emp_address($export_index);return false;\" target=\"_blank\" title=\"Export to Pdf format \">Export Address Pdf </a> ]";		
		$address_export_to_pdf .= "&nbsp;&nbsp;[ <a href=\"$export_page\" id=\"exporttarget8\" class=\"formHeader\" onclick=\"pdf_emp_address_new_format($export_index);return false;\" target=\"_blank\" title=\"Export to Pdf format \">Export Address Pdf (two in one page) </a> ]";
	}
	
	if(is_authorized_group($session_user,1) and basename($me)=="collection_report.php" and $only_receipts==true){
		$address_export_to_pdf.="&nbsp;&nbsp;[ <a href=\"$export_page\" id=\"exporttarget3\" class=\"formHeader\" onclick=\"pdf_receipts($export_index);return false;\" target=\"_blank\" title=\"PDF Receipts\">Receipt Pdf</a> ]";
	}
	if($sms_category!=0){
	    if(is_employee_access_allowed_in_report($session_user, 'sms', basename($me))){
	        $address_export_to_pdf.="&nbsp;&nbsp;<a href=\"bulk_sms_content.php\" onclick=\"javascript:sms_customer($export_index);return false;\" id=\"exporttargetsms\" class=\"formHeader\">[ Send SMS To Customer ]</a>";
	    }
	}
	if($email_category!=0 and $email_category==84){
	    if(is_employee_access_allowed_in_report($session_user, 'mail', basename($me))){
	        $address_export_to_pdf.="&nbsp;&nbsp;<a href=\"send_mail_to_customer.php\" onclick=\"javascript:email_customer($export_index);return false;\" id=\"exporttargetsms\" class=\"formHeader\">[ Send Mail To Customer ]</a>";
	    }
	}
	if(in_array(basename($me),array("prospects_followup_report.php","order_report.php"))) {
	    if(is_employee_access_allowed_in_report($session_user, 'export_mobile', basename($me))){
	        $address_export_to_pdf.="&nbsp;&nbsp;[ <a href=\"$export_page\" id=\"exporttarget8\" class=\"formHeader\" onclick=\"cust_mobile_nos_txt($export_index);return false;\" target=\"_blank\" title=\"Export to txt format\">Export Customers Mobile Nos.</a> ]";
	    }
	}
	/* sorting array to be pass to take the columns and myarr for headings */
	$column_fetch='';
	if($take_sort_array_for_export!=null){
		if(is_array($take_sort_array_for_export)){
			$column_fetch=implode(',',$take_sort_array_for_export);
		}
	}
	if(is_array($heading)){
		$heading=implode(',',/*. (string[int]) .*/ $heading);
	}
	if(is_array($heading2)){
		$heading2=implode(',',/*. (string[int]) .*/ $heading2);
	}
echo<<<END
<tr><td>
<table cellpadding="0" cellspacing="0" width="100%" border="0" class="FormBorder" width="100%">
<tbody><tr style="height: 20"><td COLSPAN="11" align="right">
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="navigation">
<tr class="formHeader"><td width="30%" nowrap><input type="hidden" id="to_whom" name="to_whom" value="$to_whom">
END;
if($export_page!=null){
echo<<<END
[ <a href="$export_page" id="exporttarget" class="formHeader" onclick="testjs($export_index);return false;" target="_blank" class="subtle" title="Export to Excel format ">Export </a> ] $address_export_to_pdf
END;
}
echo<<<END
<input type="hidden" name="querycsv[$export_index]" id="querycsv$export_index" value="$e_query">
<input type="hidden" name="headingscsv[$export_index]" id="headingscsv$export_index" value="$heading">
<input type="hidden" name="headingscsv2[$export_index]" id="headingscsv2$export_index" value="$heading2">
<input type="hidden" name="column_fetch[$export_index]" id="column_fetch$export_index" value="$column_fetch">
<input type="hidden" name="exporturlcsv[$export_index]" id="exporturlcsv$export_index" value="$export_page">
<input type="hidden" name="export_from_page[$export_index]" id="export_from_page$export_index" value="$php_page">
<input type="hidden" name="session_user" id="session_user" value="$session_user">
<input type="hidden" name="session_user_name" id="session_user_name" value="$session_user_name">
<input type="hidden" name="export_category[$export_index]" id="export_category$export_index" value="">
$POST_VALUES
END;
if($export_page!=null){
echo<<<END
<script type="text/javascript">
function pdf_address(index_no){ $("exporttarget").href="$export_page";$("export_category"+index_no).value=3; var OpenWindow=call_popup("export.php?index_no="+index_no,4); }
function pdf_emp_address(index_no){ $("exporttarget").href="$export_page";$("export_category"+index_no).value=7; var OpenWindow=call_popup("export.php?index_no="+index_no,4); }
function pdf_emp_address_new_format(index_no){ $("exporttarget").href="$export_page";$("export_category"+index_no).value=8; var OpenWindow=call_popup("export.php?index_no="+index_no,4); }
function pdf_address_add(index_no){ $("exporttarget").href="$export_page";$("export_category"+index_no).value=6; var OpenWindow=call_popup("export.php?index_no="+index_no,4); }
function pdf_receipts(index_no){ $("exporttarget").href="$export_page"; $("export_category"+index_no).value=4; var OpenWindow=call_popup("export.php?index_no="+index_no,4); }
function pdf_asa_letter(index_no){ $("exporttarget").href="$export_page"; $("export_category"+index_no).value=5; var OpenWindow=call_popup("export.php?index_no="+index_no,4); }
function cust_mobile_nos_txt(index_no){ $("exporttarget").href="$export_page"; $("export_category"+index_no).value=10; var OpenWindow=call_popup("export.php?index_no="+index_no,4);}
function testjs(index_no){ $("exporttarget").href="$export_page"; $("export_category"+index_no).value=1; var OpenWindow=call_popup("export.php?index_no="+index_no,4); }
function sms_customer(index_no){
		/*  $("exporttarget").href="bulk_sms_content.php"; $("export_category"+index_no).value=1; var OpenWindow=call_popup("export.php?index_no="+index_no,6);  */
		sms_send_view();
}
function email_customer(index_no){ $("exporttarget").href="send_mail_to_customer.php"; $("export_category"+index_no).value=1; var OpenWindow=call_popup("export.php?index_no="+index_no,6); }
function push_notification(index_no) { $("exporttarget").href="send_push_notification.php"; $("export_category"+index_no).value=11; var OpenWindow=call_popup("export.php?index_no="+index_no,6); }
</script></td>
END;
}
/*
if($sms_category!=null and $sms_category!=84){
echo<<<END
<td><input type="hidden" id="sms_category" name="sms_category" value="$sms_category">
<input type="hidden" id="sms_message" name="sms_message" value="">
<span id="open_popup_configure"><a href="javascript:call_popup('send_sms_from_report.php?category=$sms_category',5);">[Send SMS]</a></span>
<span id="send_configured_msg" style="display:none"><input type="button" id="send_sms_btn" onclick="javascript:send_sms_from_report();" value="[Send SMS2]"></span></td>
END;
}
*/
echo<<<END
<td class="formHeader1" align="right">&nbsp;$sp</td>
<td align="right" class="formHeader">&nbsp;$navigation&nbsp;</td></tr></table>
</td></tr></tbody></table><tr><td>
<link rel="stylesheet" href="CSS/bulk_sms_content.css">
<link rel="stylesheet" type="text/css" href='CSS/loader.css'>
<div id="sms_send_function" style="display: none;font-family:open sans;">		
		<div class="send-sms-container">
		<div class='out-loader hide' style="z-index:10000;">
			<div class="loader"><span></span><span></span><span></span></div>
		</div>
			<div class="send-sms">
				<div class="sms-title">
					Send Bulk SMS	
				</div>
				<div class='close-wrapper'>
					<button type="button" onclick='closeSMSSendModal();' class="close" aria-label="Close"><span aria-hidden="true">&times;</span>
					</button>
				</div>
	<div class="new-form-style">
 	<div class="alert-box" id="alert_message"> 		
 	</div>
 		<form name="sms_content_config" id="sms_content_config" action="$me" method="POST" onsubmit="return false"> 			
 		</form>
 	</div>		
	</div>
</div>	
</div>

END;

}

/**
 * @param string[int] $myarr
 * @param mixed[] $mysort
 * @param mixed[string] $nav_struct
 * @param string $sortbycol
 * @param string $sorttype 
 * @param string[int] $myarr_width
 * @param string $noheader The value can be null (or) '' (or) 'no'
 * @param string[int] $myarr_extra_link
 * @param string[int] $myarr_sub
 * @param string[int] $rowspan_arr
 * @param string[int] $colspan_arr
 * @param string[int] $myarr_sub1
 * @param string[int] $rowspan_arr1
 * @param string[int] $colspan_arr1
 * @param string[int] $myarr_style
 * @param string[int] $myarr_sub_style
 * @param boolean $sorting_required
 * 
 * @return void
 */

function sortheaders($myarr,$mysort,$nav_struct,$sortbycol,$sorttype,
$myarr_width=null,$noheader=null,$myarr_extra_link=null,$myarr_sub=null,$rowspan_arr=null,
$colspan_arr=null,$myarr_sub1=null,$rowspan_arr1=null,$colspan_arr1=null,$myarr_style=null,$myarr_sub_style=null,$sorting_required=true){
	global $from_page;
	$mysortad=array ("" , " desc");
	if($sorttype=='' or  $sorttype=='1'){
		$sortimage="<img height=10 width=10 src=\"images/up.gif\" alt=\"\" border=0>";
		$sortalter = "2";
		$sorttype=1;
	}else{
		$sortimage="<img height=10 width=10 src=\"images/down.gif\" alt=\"\" border=0>";
		$sortalter = "1";
	}
	$php_page_extra_args=php_extra_args('sortheaders');
	if($noheader==null) echo "<thead>";
echo<<<END
<tr class="modulelisttitle" >
END;
	$sub_arr_index=0;
	$sub_arr_index1=0;
	$sub_index1='';
	$sub_index='';
	for($i=0,$k=0;$i<count($myarr);$i++){
		$rowspan=(isset($rowspan_arr[$i])?"rowspan=\"".$rowspan_arr[$i]."\"":"");
		$colspan=(isset($colspan_arr[$i])?"colspan=\"".$colspan_arr[$i]."\"":"");
		$style_th=(isset($myarr_style[$i])?"style=\"".$myarr_style[$i]."\"":"");
		if(isset($myarr_width[$i]) and $myarr_width[$i]!=null){
			$width="style=\"width:".$myarr_width[$i]."\"";
		}else {$width="";}
        echo '<td '.$rowspan.' '.$colspan.' '.$width.' '.$style_th.' class="header_without_link">';
		if(isset($mysort[$k]) and $mysort[$k]!='' and (!isset($colspan_arr[$i]) or $colspan_arr[$i]==1)){
		    if($sorting_required){
		        echo "<a class=\"header_link\" href=\"$from_page?next_page="."0"."&amp;$php_page_extra_args&amp;sortbycol=".(isset($mysort[$k])?$mysort[$k]:'')."&amp;sorttype=$sortalter\" >".(( isset($mysort[$k]) and $sortbycol!='' and $sortbycol==$mysort[$k])?$sortimage:"").$myarr[$i]."</a>";
		    }else{
		        echo $myarr[$i];
		    }
	    	$k++;
		}else{
			echo $myarr[$i];
			if(!isset($colspan_arr[$i]) or $colspan_arr[$i]==1 or $colspan_arr[$i]=='') {$k++; }
		}
		if(isset($myarr_extra_link[$i]) and $myarr_extra_link[$i]!=null){
			echo $myarr_extra_link[$i];
		}
		echo " </td> ";     
		//echo "<br>".$php_page_extra_args;
		if(isset($colspan_arr[$i]) and isset($myarr_sub) and $colspan_arr[$i]>1 and $myarr_sub!=null){
			$rowspan1='';$colspan1='';$style_inth='';
	  		for($j=1;$j<=(int)$colspan_arr[$i];$sub_arr_index++){
	  			if(isset($rowspan_arr1[$sub_arr_index]) and $rowspan_arr1[$sub_arr_index]!=''){
		  			$rowspan1="rowspan=".(isset($rowspan_arr1[$sub_arr_index])?$rowspan_arr1[$sub_arr_index]:1);
	  			}
	  			if(isset($colspan_arr1[$sub_arr_index]) and $colspan_arr1[$sub_arr_index]!=''){
	  				$colspan1="colspan=".(isset($colspan_arr1[$sub_arr_index])?$colspan_arr1[$sub_arr_index]:1);
	  			}
	  			if(isset($myarr_sub_style[$sub_arr_index]) and $myarr_sub_style[$sub_arr_index]!=''){
	  				$style_inth="style=".(isset($myarr_sub_style[$sub_arr_index])?$myarr_sub_style[$sub_arr_index]:1);
	  			}	  			
				if((!isset($colspan_arr1[$sub_arr_index]) or $colspan_arr1[$sub_arr_index]==1 or $colspan_arr1[$sub_arr_index]==0) and (isset($mysort[$k]) and $mysort[$k]!="" and (isset($myarr_sub[$sub_arr_index])))){
		  			$sub_index.="<td  $rowspan1 $colspan1 $style_inth>";
		  			if($sorting_required){
		  			    $sub_index.="<a class=\"header_link\" href=\"$from_page?next_page="."0"."&amp;$php_page_extra_args&amp;sortbycol=$mysort[$k]&amp;sorttype=".(($sortbycol==$mysort[$k])?$sortalter:"")."\" >".(($sortbycol!='' and $sortbycol==$mysort[$k])?$sortimage:"").$myarr_sub[$sub_arr_index]."</a>";
		  			}else{
		  			    $sub_index.= $myarr_sub[$sub_arr_index];
		  			}
					$k++;
					$j++;
					//echo $sub_index;
				}else{
					$sub_index.="<td class=\"header_without_link\" $rowspan1 $colspan1>";
					$sub_index.=(isset($myarr_sub[$sub_arr_index])?$myarr_sub[$sub_arr_index]:'');
					if((!isset($colspan_arr1[$sub_arr_index]) or $colspan_arr1[$sub_arr_index]==1)){
						$k++;$j++;
					}else if($colspan_arr1[$sub_arr_index]>1){
						for($j1=1;$j1<=$colspan_arr1[$sub_arr_index];$j1++,$sub_arr_index++){
							$sub_index1.="<td class=\"header_without_link\">";
							$sub_index1.=(isset($mysort[$k])?"<a  class=\"header_link\" href=\"$from_page?next_page="."0"."&amp;$php_page_extra_args&amp;sortbycol=$mysort[$k]&amp;sorttype=".(($sortbycol==$mysort[$k])?$sortalter:"")."\" >".(($sortbycol!='' and $sortbycol==$mysort[$k])?$sortimage:"").$myarr_sub1[$sub_arr_index1]."</a>":$myarr_sub1[$sub_arr_index1]);
					  		$k++; $j++;
						}
					}
				}
			}//end of for 
		}
	}//end of for
echo<<<END
</tr>
END;
	if($sub_index!=''){
		echo "<tr  class=\"modulelisttitle\">$sub_index</tr>";
	}
	if($sub_index1!=''){
		echo "<tr  class=\"modulelisttitle\">$sub_index1</tr>";
	}
	if($noheader==null) echo "</thead>";
}

/**
 * @return void
 */
function print_dtable_footer(){
echo<<<END
</td></tr></table></table>
END;
}


/**
 * @param string $name
 * @param int $colspan
 * @param string $tr_name
 * @param string $tr_id
 *
 * @return void 
 */
function print_executive_name($name,$colspan=100,$tr_name='',$tr_id=''){
$trd = '';
if($tr_name!=''){
	$trd.=' name="'.$tr_name.'"';
}
if($tr_id!=''){
	$trd.=' id="'.$tr_id.'"';
}
echo<<<END
<tr $trd><td class="head_maroon" valign="top" style="padding:0px 1px 0px 1px;" colspan="$colspan">$name </td></tr>
END;
} 

/**
 * @param string $str
 * @param string $image
 * @param int $colspan
 *
 * @return void
 */
function print_date($str,$image=null,$colspan=100){
	if($image!="no"){
		$img_w='<td WIDTH="1" class="blackLine" ><IMG alt="" SRC="images/blank.gif" ></td>';
	}else{
		$img_w='';
	}
echo<<<END
<tr class="tableHead">$img_w
<td class="head_maroon" valign="top" style="padding:0px 1px 0px 1px;" colspan="$colspan">$str </td>
END;
} 

/**
 * @param string[] $in_group_arr
 * @param string[] $not_in_group_arr
 * 
 * @return string
 */
function get_roles_in_group($in_group_arr,$not_in_group_arr=null){
	$in_group_id_str=implode(',',$in_group_arr);
	
	$query="select group_concat(grm_role_id) roles  from  gft_role_master ,gft_role_group_master 
	where grm_role_id=grg_role_id and  grg_group_id in ($in_group_id_str) ";
	
	if($not_in_group_arr!=null){
	$not_in_group_id_str=implode(',',$not_in_group_arr);
	$query.=" and grm_role_id not in (
	select  grg_role_id  from gft_role_group_master where grg_group_id  in ($not_in_group_id_str)) ";
	}
	$result=execute_my_query($query);
	$qdata=mysqli_fetch_array($result);
	if(mysqli_num_rows($result)==1){
		return $qdata['roles'];
	}
	return null;
}	

/**
 * @param string $role_id
 * @param string[] $group_id_arr
 *
 * @return boolean
 */
function is_role_in_group($role_id,$group_id_arr){
	$group_id_str=implode(',',$group_id_arr);
	$qgroup="select GRG_ROLE_ID  from gft_role_group_master where grg_group_id in ($group_id_str) and grg_role_id='$role_id' ";
	$rgroup=execute_my_query($qgroup);
	if(mysqli_num_rows($rgroup)>0){ return true;}
	return false;
}

/**
 * @param string $emp_id
 *
 * @return string
 */
function get_emp_in_group($emp_id){
	$groups_arr='';
	$query="select group_concat( distinct ggm_group_id) group_ids from gft_emp_master a" .
			" left join gft_role_group_master rg on (grg_role_id=gem_role_id)" .
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id)" .
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id)" .
			" where gem_status='A' and a.gem_emp_id='$emp_id'  group by a.gem_emp_id";
	$result=execute_my_query($query,'common_util.php',true,false,2);
	if($data=mysqli_fetch_array($result)){
		$groups_arr=$data['group_ids'];
	}
	return $groups_arr;
}


/**
 * @param string $uid
 *
 * @return string
 */
function get_terr_id($uid){
	$terr_id='';

	/* check what is the use of this functon many placess used  query may give more than one value but in using places we take only one  */
	$query_terr_id="SELECT GET_TERRITORY_ID, GET_STATUS " .
			" FROM gft_emp_territory_dtl join gft_emp_master on (get_emp_id=gem_emp_id and gem_status='A' )" .
			" join gft_business_territory_master on (gbt_territory_id=GET_TERRITORY_ID and GBT_STATUS='A') " .
			" where get_emp_id='$uid' and get_status='A' and get_work_area_type='2' ";
	$result_terr_id=execute_my_query($query_terr_id,'common_util.php',true,false,2);
	if(mysqli_num_rows($result_terr_id)>0){
		while($qd=mysqli_fetch_array($result_terr_id)){
			$terr_id=$qd['GET_TERRITORY_ID'];
		}

	}else{
		$terr_id='100';
	}
	return $terr_id;
}	


/**
 * @param string $terri_id
 *
 * @return string
 */
function get_support_incharge($terri_id){
	$incharge=SALES_DUMMY_ID;
	$query="select GET_EMP_ID, GET_TERRITORY_ID,get_work_area_type,em.gem_emp_name,GET_STATUS " .
			" from gft_emp_territory_dtl join gft_emp_master em on (GET_EMP_ID=GEM_EMP_ID and GET_STATUS='A') " .
			" join gft_emp_group_master eg on (eg.gem_group_id=62 and eg.GEM_EMP_ID=em.GEM_EMP_ID) " .
			" inner join b_map_view bm on (get_work_area_type=2 and GET_TERRITORY_ID=terr_id) " .
			" where GEM_STATUS='A' and GET_TERRITORY_ID=$terri_id and GET_EMP_ID!=".SALES_DUMMY_ID ;
	$result_emp_id=execute_my_query($query);
	if($data=mysqli_fetch_array($result_emp_id)){
		$incharge=$data['GET_EMP_ID'];
	}
	return $incharge;		
}

/**
 * @param string $terri_id
 *
 * @return string
 */
function get_l1_incharge($terri_id){
	$query="select GCTI_L1_INCHARGE,GET_EMP_ID, GET_TERRITORY_ID,get_work_area_type,em.gem_emp_name,GET_STATUS " .
			" from gft_emp_territory_dtl " .
			" join gft_emp_master em on (GET_EMP_ID=GEM_EMP_ID and em.GEM_STATUS='A' and GET_STATUS='A') " .
			" join gft_emp_group_master eg on (eg.gem_group_id=62 and eg.GEM_EMP_ID=em.GEM_EMP_ID) " .
			" inner join b_map_view bm on (get_work_area_type=2 and GET_TERRITORY_ID=terr_id) " .
			" join gft_customer_training_incharge on (GCT1_SUPPORT_INCHARGE=GET_EMP_ID)" .
			" join gft_emp_master em1 on (GCT1_SUPPORT_INCHARGE=em1.GEM_EMP_ID and em1.GEM_STATUS='A') " .
			" where  terr_id=$terri_id and GET_EMP_ID!=".SALES_DUMMY_ID;
	$result_emp_id=execute_my_query($query);
	$incharge=SALES_DUMMY_ID;
	if($data=mysqli_fetch_array($result_emp_id)){
		$incharge=$data['GCTI_L1_INCHARGE'];
	}
	return $incharge;
}

/**
 * @param string $terri_id
 *
 * @return string
 */
function get_regional_coordinator($terri_id){
	$query="select GET_EMP_ID, GET_TERRITORY_ID,get_work_area_type,em.gem_emp_name,GET_STATUS " .
			" from gft_emp_territory_dtl " .
			" inner join b_map_view bm on ((get_work_area_type=2 and GET_TERRITORY_ID=terr_id)" .
			" or (get_work_area_type=3 and GET_TERRITORY_ID=area_id)" .
			" or (get_work_area_type=4 and GET_TERRITORY_ID=region_id)" .
			" or (get_work_area_type=5 and GET_TERRITORY_ID=zone_id)) " .
			" join gft_emp_master em on (GET_EMP_ID=GEM_EMP_ID and em.GEM_STATUS='A' and GET_STATUS='A' and GET_ASSIGN_TYPE='A') " .
			" join gft_role_group_master  on (GRG_ROLE_ID=em.gem_role_id and GRG_GROUP_ID=58)  " .
			" where  terr_id=$terri_id and GET_EMP_ID!=".SALES_DUMMY_ID ." group by GET_EMP_ID";
	$result_emp_id=execute_my_query($query);
	$incharge=SALES_DUMMY_ID;
	if($data=mysqli_fetch_array($result_emp_id)){
		$incharge=$data['GET_EMP_ID'];
	}
	return $incharge;
}

/**
 * @param string $emp_code
 *
 * @return string
 */
function get_support_person_buddy($emp_code){
	$result_emp_id=execute_my_query(" select GCTI_L1_INCHARGE from gft_customer_training_incharge where GCT1_SUPPORT_INCHARGE='$emp_code'");
	$incharge='';
	if($data=mysqli_fetch_array($result_emp_id)){
		$incharge=$data['GCTI_L1_INCHARGE'];	
	}
	return $incharge;
}

/**
 * @param int $lead_code
 * 
 * @return string
 */
function get_lead_field_executive($lead_code){
	$result_emp_id=execute_my_query("SELECT glh_lfd_emp_id FROM gft_lead_hdr WHERE GLH_LEAD_CODE=$lead_code ",'common_util.php',true,false,2);
	$incharge='';
	if($data=mysqli_fetch_array($result_emp_id)){
		$incharge=$data['glh_lfd_emp_id'];	
	}
	return $incharge;
}

/**
 * @return string[int][int]
 */
function get_order_approval_emplist(){
	global $auth_order_approval_group;
	$auth_order_approval_group_list=implode(',',$auth_order_approval_group);
	$query=" select a.gem_emp_id,gem_emp_name  from gft_emp_master a ,gft_emp_group_master b" .
		   " where b.gem_group_id in ($auth_order_approval_group_list) and a.gem_emp_id=b.gem_emp_id " .
		   " and gem_status='A' ";
	return get_two_dimensinal_array_from_query($query,'gem_emp_id','gem_emp_name');
}

/**
 * @param string $other_users
 * @param boolean $next_visit_nature
 * @param boolean $visit_nature
 * @param boolean $sales_planning
 * @param string $act_id
 * @param string $activity_nature
 * @param boolean $support_activity
 *
 * @return string[int][int]
 */
 
function get_activity_list($other_users=null,$next_visit_nature=false,$visit_nature=false,
	$sales_planning=false,$act_id=null,$activity_nature="0",$support_activity=false){
   // global $uid,$non_employee_group;
	$query_act="select GAM_ACTIVITY_ID,GAM_ACTIVITY_DESC from gft_activity_master " .
			"where 1 ";
	if($act_id!=null){
		$query_act.=" and GAM_ACTIVITY_ID='$act_id' ";
	}else {
	    //if(is_authorized_group_list($uid,$non_employee_group)){
			//$query_act.=" and GAM_FLAG_OTHER_EXEC=1";}
		if($next_visit_nature==true){
			$query_act.=" and GAM_APPLICAPLE_NEXT_VISIT='Y' ";
		}
		if($visit_nature==true){
			$query_act.=" and GAM_APPLICAPLE_VISIT='Y' ";
		}
		if($sales_planning==true){
			$query_act.=" and GAM_APPLICABLE_FOR_SP='Y' ";
		}
		if($support_activity==true){
			$query_act.=" and GAM_SUPPORT_ACTIVITY ='Y' ";
		}
		if($activity_nature=="1"){
			$query_act.=" and  GAM_DISTANCE_TRAVELLED_REQ='Y' ";
		}else if($activity_nature=="2"){
			$query_act.=" and  GAM_DISTANCE_TRAVELLED_REQ='N' ";
		}
	}
	return get_two_dimensinal_array_from_query($query_act,'GAM_ACTIVITY_ID','GAM_ACTIVITY_DESC');
}
/**
 * @param string $other_users
 * @param boolean $next_visit_nature
 * @param boolean $visit_nature
 * @param boolean $sales_planning
 * @param string $act_id
 * @param string $activity_nature
 * @param boolean $support_activity
 *
 * @return string[int][int]
 */

function get_activity_list_with_group($other_users=null,$next_visit_nature=false,$visit_nature=false,
		$sales_planning=false,$act_id=null,$activity_nature="0",$support_activity=false){
	// global $uid,$non_employee_group;
	$query_act="select GAM_ACTIVITY_ID,GAM_ACTIVITY_DESC,GFM_NAME from gft_activity_master " .
			" left join gft_followup_master on(GAM_FOLLOWUP_GROUP=GFM_ID) where 1 ";
	if($act_id!=null){
		$query_act.=" and GAM_ACTIVITY_ID='$act_id' ";
	}else {
		//if(is_authorized_group_list($uid,$non_employee_group)){
		//$query_act.=" and GAM_FLAG_OTHER_EXEC=1";}
		if($next_visit_nature==true){
			$query_act.=" and GAM_APPLICAPLE_NEXT_VISIT='Y' ";
		}
		if($visit_nature==true){
			$query_act.=" and GAM_APPLICAPLE_VISIT='Y' ";
		}
		if($sales_planning==true){
			$query_act.=" and GAM_APPLICABLE_FOR_SP='Y' ";
		}
		if($support_activity==true){
			$query_act.=" and GAM_SUPPORT_ACTIVITY ='Y' ";
		}
		if($activity_nature=="1"){
			$query_act.=" and  GAM_DISTANCE_TRAVELLED_REQ='Y' ";
		}else if($activity_nature=="2"){
			$query_act.=" and  GAM_DISTANCE_TRAVELLED_REQ='N' ";
		}
	}
	return get_three_dimensinal_array_from_query($query_act,'GAM_ACTIVITY_ID','GAM_ACTIVITY_DESC','GFM_NAME',"GFM_ID,GAM_ORDER_BY");
}
/**
 * @param string $pages
 * @param string $reason_needed
 * @param boolean $for_new_lead
 *
 * @return string[int][int] 
 */
function get_customer_status_list($pages=null,$reason_needed=null,$for_new_lead=false){
	$query="select GCS_CODE, GCS_NAME, GCS_DESC from gft_customer_status_master where gcs_status='A'  ";
	if($pages=="next_visit_report"){
		$query.=" and  GCS_CODE not in(10,12,24,23,22,21,20,19)  ";
	}
	if($reason_needed=='Y'){
		$query.=" and GCS_REASON_MANDATORY='Y' ";
	}
	if($for_new_lead){
		$query .= " and GCS_LIST_NEW_LEAD='Y' ";
	}
	return get_two_dimensinal_array_from_query($query,'GCS_CODE','GCS_NAME','GCS_ORDER_BY');
}

/**
 * @param string $pages
 * @param string $reason_needed
 *
 * @return string[int][int]
 */
 function get_prospect_status_list($pages=null,$reason_needed=null){
 	$query="select GPS_STATUS_ID,GPS_STATUS_NAME FROM gft_prospects_status_master  where  GPS_STATUS='A' ";
	return get_two_dimensinal_array_from_query($query,'GPS_STATUS_ID','GPS_STATUS_NAME','GPS_ORDER_BY');
} 

/**
 * @param string $visit_date
 * @param int $territory_id
 * @param string $emp_id
 * 
 * @return string
 */
function get_quotation_no($visit_date,$territory_id,$emp_id){
	$curr_year=substr($visit_date,2,2);
	$terr_id=$territory_id;
	$str="000";
	$terrid=$str.$terr_id;
	$terr_idn=substr($terrid,-3);

	$emp_id1=$emp_id;
	$emp_id1="0000".$emp_id1;

	$emp_idn=substr($emp_id1,-4);
	$quotation_no=$terr_idn.$emp_idn.$curr_year;
	
	$query_or="select max(gqh_order_no) from gft_quotation_hdr where substr(gqh_order_no,4,4)='$emp_idn' " .
			  "and gqh_order_no like '$quotation_no%' ";
	//echo $query_or;
	$result_or=execute_my_query($query_or,'common_util.php',true,false,2);
	if(mysqli_num_rows($result_or)==0)
	{
		$quotation_no=$quotation_no."000001";
	}else{
		if($querydata=mysqli_fetch_array($result_or)){
			$order_no=substr($querydata[0],-6);
			$order_no=(int)$order_no+1;
			$quotation_no.=substr("000000".$order_no,-6);
		}
	}
	return $quotation_no;
}

/**
 * @param string $visit_date
 * @param string $emp_id
 * 
 * @return string
 */
function get_profroma_no($visit_date,$emp_id){
	$curr_year=substr($visit_date,2,2);
	$emp_id1=$emp_id;
	$emp_id1="0000".$emp_id1;
	$emp_idn=substr($emp_id1,-4);
	$proforma_no="PI".$emp_idn.$curr_year;
	$query_or="select max(gph_order_no) from gft_proforma_hdr where substr(gph_order_no,3,4)='$emp_idn' " .
			  "and gph_order_no like '$proforma_no%' ";
	$result_or=execute_my_query($query_or,'common_util.php',true,false,2);
	if(mysqli_num_rows($result_or)==0){
		$proforma_no=$proforma_no."0000001";
	}else{
		if($querydata=mysqli_fetch_array($result_or)){
			$order_no=substr($querydata[0],-7);
			$order_no=(int)$order_no+"1";
			$proforma_no.=substr("000000".$order_no,-7);
		}
	}
	return $proforma_no;
}

/**
 * @param string $visit_date
 * @param string $emp_id
 *
 * @return string
 */
function get_serive_invoice_no($visit_date,$emp_id){
	$curr_year=substr($visit_date,2,2);
	$emp_id1=$emp_id;
	$emp_id1="0000".$emp_id1;
	$emp_idn=substr($emp_id1,-4);
	$service_no="SI".$emp_idn.$curr_year;
	$query_or="select max(gih_invoice_ac_reffer_id) from gft_invoice_hdr where gih_invoice_ac_reffer_id like '$service_no%' ";
	$result_or=execute_my_query($query_or,'common_util.php',true,false,2);
	if(mysqli_num_rows($result_or)==0){
		$service_no=$service_no."0000001";
	}else{
		if($querydata=mysqli_fetch_array($result_or)){
			$order_no=substr($querydata[0],-7);
			$order_no=(int)$order_no+1;
			$service_no.=substr("000000".$order_no,-7);
		}
	}
	return $service_no;
}

/**
 * @param string $type
 * @param string $curr_year
 * @param string $territory_id
 * @param string $emp_id
 * @param string $use_order_id 
 * 
 * @return string
 */
function get_order_no($type,$curr_year,$territory_id,$emp_id,$use_order_id=''){
	$order_no='';
	$terr_id=$territory_id;
	$str="000";
	$terrid=$str.$terr_id;
	$terr_idn=substr($terrid,-3);
	$emp_id1=$emp_id;
	$emp_id1="0000".$emp_id1;
	$emp_idn=substr($emp_id1,-4);
	$order_id = $terr_idn.$emp_idn;
	if(strlen($use_order_id)==7){
		$order_id = $use_order_id;
	}
	$order_id .= $curr_year;
	$query_or="select max(god_order_no) from gft_order_hdr where god_order_no like '$order_id%' ";
	if($type=="D"){
		$query_or.=" and  god_order_no not like '%M%'";
	}
	if($type=="M"){
		$query_or.=" and  god_order_no  like '%M%'";
	}
	$result_or=execute_my_query($query_or,'common_util.php',true,false,2);
	if(mysqli_num_rows($result_or)==0){	
		if($type=="D"){
			$order_no=$order_id."000001";
		}
		if($type=="M"){
			$order_no=$order_id."M00001";
		}
	}else{
		$querydata=mysqli_fetch_array($result_or);
		if($type=="D"){
			$order_no=substr($querydata[0],-6);
			$order_no=(int)$order_no+1;
			$order_no="000000".$order_no;
			$order_no=substr($order_no,-6);
			$order_no=$order_id.$order_no;
		}
		if($type=="M"){
			$order_no=substr($querydata[0],-5);
			$order_no=(int)$order_no+1;
			$order_no="000000".$order_no;
			$order_no=substr($order_no,-5);
			$order_no=$order_id."M".$order_no;
		}
	}
	return $order_no;
}
/**
 * @param string $lead_code
 * 
 * @return boolean
 */
function check_lead_type_to_show_subscription_skew($lead_code){
	$result=execute_my_query("select GLH_LEAD_CODE from gft_lead_hdr where GLH_LEAD_TYPE=3 AND GLH_LEAD_CODE='$lead_code'");
	if(mysqli_num_rows($result)>0){
		return true;
	}else{
		return false;
	}
}
/**
 * @param string $lead_code
 * @param boolean $check_today_date
 * @param string $validation_for
 * @param boolean $return_product
 * 
 * @return string
 */
function get_asa_expiry_status_for_base_product($lead_code,$check_today_date=false,$validation_for='order',$return_product=false){
	$today_date=date("Y-m-d");
	$return_string="";
	$return_message="";
	$validity_date="";
	$return_product_code_skew="";
	$asa_expity_days=get_samee_const("ASA_Expiry_Validation_Days");
	$expity_dt	=	(date('Y-m-d',strtotime("+$asa_expity_days days",strtotime(date('Y-m-d')))));
	if($check_today_date){
		$expity_dt=date("Y-m-d");
	}
	$sql_query	=	" select GID_LIC_PCODE,GID_LIC_PSKEW,GID_INSTALL_ID,GID_VALIDITY_DATE,concat(GPM_PRODUCT_ABR,'-',gpg_version) product_name from gft_install_dtl_new ".
					" INNER JOIN gft_product_master pm ON(GID_LIC_PCODE=GPM_PRODUCT_CODE AND GID_LIC_PSKEW=GPM_PRODUCT_SKEW AND GPM_FREE_EDITION='N' AND GFT_SKEW_PROPERTY!=18) ".
					" INNER JOIN gft_product_family_master pfm ON(pfm.gpm_product_code=pm.gpm_product_code AND GPM_IS_BASE_PRODUCT='Y')".
					" INNER JOIN gft_product_group_master pg ON(gpg_product_family_code=pm.GPM_PRODUCT_CODE AND substring(GPM_PRODUCT_SKEW,1,4)=gpg_skew)".
					" where GID_LEAD_CODE='$lead_code' AND GID_VALIDITY_DATE<'$expity_dt' AND GID_STATUS='A' limit 1";
	$result		=	execute_my_query($sql_query);
	while($row=mysqli_fetch_array($result)){
		$validity_date 	= $row['GID_VALIDITY_DATE'];
		$product_name	= $row['product_name'];		
		$return_product_code_skew=$row['GID_LIC_PCODE'].$row['GID_LIC_PSKEW'];
		$return_string	=	"Product Name: $product_name, Expiry Date: $validity_date";
	}
	if($return_product){
		return $return_product_code_skew;
	}
	if($return_string!=""){
		if($validation_for=="quotation" && $validity_date<$today_date){
			$return_message="ASA is already expired for this customer($return_string). You can only create new quotation but cannot create new orders.".
							" Please communicate to the customer to pay the pending ASA and only after that you will be able to punch the".
							" new order. FYI - This has been notified to Customer Success Team, and they will follow-up for pending ASA";
		}else if($validation_for=="quotation" && $validity_date==$today_date){
			$return_message="ASA expiry date is today for this customer($return_string).".
							" We have notified this to Customer success team to follow-up for ASA collection.".
							" Please advise customer to pay on-time so that you can punch the additional orders for this customer";
		}else if($validation_for=="quotation" && $validity_date>$today_date){			
			$return_message="Only a few days left for the ASA to expire for this customer($return_string).".
							" We have notified this to Customer success team to follow-up for ASA collection.".
							" Please advise customer to pay on-time so that you can punch the additional orders for this customer";
		}else if($validation_for=="collection" && $validity_date<$today_date){
			$return_message="Congrats on collection, but you cannot punch an order".
							" Since there is an outstanding ASA from this customer, you will be able to punch the order only".
							" after the customer settles the outstanding. We have notified the Customer success team".
							" for follow-up and collect the outstanding also";
		}else if($validation_for=="collection" && $validity_date==$today_date){
			$return_message="ASA expiry date is today for this customer($return_string).".
							" We have notified this to Customer success team to follow-up for ASA collection.".
							" Please advise customer to pay on-time so that you can punch the additional orders for this customer";
		}else if($validation_for=="collection" && $validity_date>$today_date){
			$return_message="Only a few days left for the ASA to expire for this customer($return_string).".
							" We have notified this to Customer success team to follow-up for ASA collection.".
							" Please advise customer to pay on-time so that you can punch the additional orders for this customer";
		}else if($validation_for=="order" && $validity_date<$today_date){
			$return_message="ASA is already expired for this customer($return_string). You cannot convert quotation to order.".
							" Please communicate to the customer to pay the pending ASA and only after that you will be able to punch the".
							" new order. FYI - This has been notified to Customer Success Team, and they will follow-up for pending ASA";
		}		
	}
	return $return_message;
}
/**
 * @param string $lead_code
 * @param string $assigned_by
 * @param string $purpose
 *
 * @return void
 */
function create_support_ticket_for_asa_expiry($lead_code,$assigned_by,$purpose){
	$emp_name=get_emp_name($assigned_by);
	$summary			=	"Notification from $emp_name on Pending ASA";
	$description		=	"$emp_name has created a $purpose for additional revenue. ".
			"Request you to collect ASA from Customer to book order.";
	$edc_date			=	date("Y-m-d");
	$assigned_to_emp	=	get_annuity_incharge_based_on_region("", "", 0, 0,(int)$lead_code);
	$pcode_skew			=	get_asa_expiry_status_for_base_product($lead_code,false,'',true);
	$pcode				=	substr($pcode_skew, 0,3);
	$pskew				=	substr($pcode_skew,3);
	$ticket_id = insert_support_entry($lead_code, "$pcode", "$pskew", '', '', $assigned_by, '0', $summary,
			39, 'T20',date('Y-m-d H:i:s'),$edc_date,$assigned_to_emp,null,'4',$description,false,
			'',"",null,'3',true);
}

/**
 * @param int $category
 * @param string[] $lpcode_lskew_arr
 * @param string $skew_property
 * @param string[int] $product_code_arr
 * @param string $formtype
 * @param int $lead_code
 * @param boolean $with_skew_propery
 * @param string $license_type
 * @param boolean $trial
 * @param string $call_from
 * @param boolean $only_active_skew
 * @param boolean $with_order_type
 * @param string[int] $not_in_pcode_arr
 * @param boolean $show_subscription_skew
 * @param boolean $kit_based
 * @param boolean $only_hourly_coupon
 * @param string $login_emply_id
 * 
 * @return string[int][int]
 */
function product_code_skew_list($category=-1,$lpcode_lskew_arr=null,$skew_property=null,$product_code_arr=null,
			$formtype=null,$lead_code=0,$with_skew_propery=true,$license_type=null,$trial=true,$call_from='',
			$only_active_skew=false,$with_order_type=false,$not_in_pcode_arr=null,$show_subscription_skew=true,$kit_based=false,
			$only_hourly_coupon=false,$login_emply_id=''){
	global $lead_type,$order_catagory,$uid,$global_gst_mode;
	if($kit_based){
		$override_kit_lead_arr = explode(",", get_samee_const("Override_Kit_Based"));
		if(in_array($lead_code, $override_kit_lead_arr)){
			$kit_based = false;
		}
	}
	$query_filter='';
	$product_list=/*. (string[int][int]) .*/ array();
	if(!empty($lead_code) and $category==2 and $order_catagory!='0'){
		execute_my_query("SET SESSION group_concat_max_len = 10240"); //need to find better logic
		$select_ins_query=" select group_concat(distinct concat(\"'\",GID_LIC_PCODE,'-', GID_LIC_PSKEW,\"'\")), group_concat(GID_LIC_PCODE), group_concat(GID_LIC_PSKEW) ".
						  " from gft_lead_hdr lh join  gft_install_dtl_new  on (GID_LEAD_CODE=lh.GLH_LEAD_CODE and gid_status!='U') ".
						  " join gft_order_hdr  on (GID_ORDER_NO=GOD_ORDER_NO and god_order_status='A')" ;
		if($lead_type==3){
			$select_ins_query .=" where ((GLH_LEAD_CODE=$lead_code) or (GLH_LEAD_SOURCECODE in (7,36) and glh_reference_given='$lead_code') and GLH_LEAD_TYPE in (3,13)) ";			
		}else{
			$select_ins_query .=" where GLH_LEAD_CODE='$lead_code' ";
		}
						
		$result_ins_query=execute_my_query($select_ins_query);
		$qd=mysqli_fetch_array($result_ins_query);
		$installed_prod=$qd[0];
		if($installed_prod!=''){
			if($order_catagory=='1'){
				$query_filter=" and pfm.gpm_product_code in (".$qd[1].")";
			}else{
				$client_cond = "";
				$lic_pcode_arr = explode(',', $qd[1]);
				$lic_pskew_arr = explode(',', $qd[2]);
				for($i=0;$i<count($lic_pcode_arr);$i++){
					$pgroup = substr($lic_pskew_arr[$i], 0,4);
					$client_cond .= " or (pm.GPM_PRODUCT_CODE='$lic_pcode_arr[$i]' and (pm.GPM_PRODUCT_SKEW REGEXP '{$pgroup}UL|{$pgroup}CL' or pm.GPM_PRODUCT_SKEW in (select GPM_PRORATE_SKEW from gft_product_master where GPM_PRORATE_SKEW!='')) ) ";
				}
				if($kit_based){
					$client_cond .= " or (pfm.GPM_IS_INTERNAL_PRODUCT=4) ";
				}
				$query_filter=" and (concat(pfm.gpm_product_code,'-',pm.GPM_REFERER_SKEW) in ($installed_prod) $client_cond)  ";
			}
		}else if($call_from=='app_asa'){
			return $product_list;
		}
	}else if(!empty($lead_code) and $category==1){
		$select_ins_query=" select group_concat(distinct concat(\"'\",gid_lic_pcode,'-',gid_lic_pskew,\"'\")) " ;
		if($lead_type==3){
			$select_ins_query .= " from gft_lead_hdr lh join  gft_install_dtl_new  on (GID_LEAD_CODE=lh.GLH_LEAD_CODE and gid_status!='U')" .
						" join gft_order_hdr  on (GID_ORDER_NO=GOD_ORDER_NO and god_order_status='A')" .
						" join gft_order_product_dtl on (GOP_ORDER_NO=GID_ORDER_NO and GOP_PRODUCT_CODE=GID_PRODUCT_CODE and GOP_PRODUCT_SKEW=GID_PRODUCT_SKEW and GOP_ORDER_FOR!='UAT') ".
						" where ((GLH_LEAD_CODE=$lead_code) or(GLH_LEAD_SOURCECODE=7 and glh_reference_given='$lead_code')) ";
		}else{
			$select_ins_query .= " from gft_install_dtl_new join gft_order_hdr on (god_order_no=gid_order_no and god_order_status='A' and gid_status!='U')" .
						" join gft_order_product_dtl on (GOP_ORDER_NO=GID_ORDER_NO and GOP_PRODUCT_CODE=GID_PRODUCT_CODE and GOP_PRODUCT_SKEW=GID_PRODUCT_SKEW and GOP_ORDER_FOR!='UAT') ".
						" where (god_lead_code='$lead_code' or gid_lead_code='$lead_code') ";
		}

		$result_ins_query=execute_my_query($select_ins_query);
		$qd=mysqli_fetch_array($result_ins_query);
		$installed_prod=$qd[0];	
		if($installed_prod!=''){
			$query_filter=" and concat(pm.gft_lower_pcode,'-',pm.gft_lower_skew) in  ($installed_prod) ";
		}else if($call_from=='app_quotation'){
			return $product_list;
		}
	}else if(!empty($lead_code) and $category==13){
		$select_ins_query=" select group_concat(distinct(concat(\"'\",gid_lic_pcode,'-',SUBSTR(GPM_REFERER_SKEW,1,4),GPM_PRODUCT_TYPE, \"'\"))), " .
						  " group_concat(distinct concat(\"'\",gid_lic_pcode,'-',SUBSTR(GPM_REFERER_SKEW,1,4),\"'\") ) as pcodeskew ".
			($lead_type==3?" from gft_lead_hdr lh join  gft_install_dtl_new  on (GID_LEAD_CODE=lh.GLH_LEAD_CODE and gid_status!='U')" .
						" join gft_order_hdr  on (GID_ORDER_NO=GOD_ORDER_NO and god_order_status='A')" .
						" join gft_product_master on (gid_lic_pcode=gpm_product_code and ((gid_lic_pskew=gpm_product_skew and GPM_REFERER_SKEW!='') or GFT_SKEW_PROPERTY in (18)) ) " .
						" where ((GLH_LEAD_CODE=$lead_code) or(GLH_LEAD_SOURCECODE=7 and glh_reference_given='$lead_code')) ":				
						" from gft_install_dtl_new join gft_order_hdr on (god_order_no=gid_order_no and god_order_status='A' and gid_status!='U') " .
						" join gft_product_master on(gid_lic_pcode=gpm_product_code and ((gid_lic_pskew=gpm_product_skew and GPM_REFERER_SKEW!='') or GFT_SKEW_PROPERTY in (18)) ) " .
						" where (god_lead_code='$lead_code' or gid_lead_code='$lead_code') ");
		$result_ins_query=execute_my_query($select_ins_query);
		$qd=mysqli_fetch_array($result_ins_query);
		$installed_prod=$qd[0];	
		$only_pcode_skew = $qd['pcodeskew'];
		if($installed_prod!=''){
			$query_filter = " and (concat(pm.gpm_product_code,'-',SUBSTR(pm.GPM_REFERER_SKEW,1,4),pm.GPM_PRODUCT_TYPE) in ($installed_prod) ".
							" or (concat(pm.gpm_product_code,'-',pm.gpm_product_skew) in ($installed_prod) and pm.GFT_SKEW_PROPERTY in (17,20,21,22)) ".
							" or (concat(pm.gpm_product_code,'-',SUBSTR(pm.gpm_product_skew,1,4)) in ($only_pcode_skew) and pm.GFT_SKEW_PROPERTY in (16,26)) ) ".
							" and pm.GPM_LIST_PRICE!=0 ";
		}else if($call_from=='app_remewal'){
			return $product_list;
		}	
		
	}
	$trial_check=$demo="";
	if(!$trial){
		$trial_check = "and pm.GPM_LICENSE_TYPE!=3";			
	}
	if(!is_authorized_group($uid, 101)){
		$demo = ' and pfm.GPM_HEAD_FAMILY != 120 '; //demo friend tool
	}
	$query_prod	=	" SELECT if(GPB_NAME is null,GPG_PRODUCT_NAME,concat(GPB_NAME,' ',GPG_PRODUCT_NAME)) GPG_PRODUCT_NAME, ".
	   	            " if(GPB_NAME is null,pm.GPM_SKEW_DESC,concat(GPB_NAME,' ',pm.GPM_SKEW_DESC)) GPM_SKEW_DESC, ".
	   	            " pm.GPM_DISPLAY_NAME,pfm.gpm_product_code,pm.gpm_product_skew, pm.GPM_PRODUCT_TYPE,". 
					" GPM_PRODUCT_ABR,pm.GPM_LIST_PRICE,pm.GFT_SKEW_PROPERTY,pm.gpm_tax_perc,pm.GPM_SERVISE_TAX_PERC,pm.GPM_NET_RATE,GPM_HEAD_FAMILY, pm.GPM_TRAINING_REQUIRED, pm.gpm_status,". 
				 	" substring(pm.GPM_PRODUCT_SKEW,1,4) pgroup,pm.GPM_COUPONS ,GPM_CATEGORY, GPM_IS_INTERNAL_PRODUCT, pm.GPM_ORDER_TYPE, GSPM_DISCOUNT_PERCENTAGE, ".
					" pm1.GFT_SKEW_PROPERTY as higher_skew_propery,GTM_CGST,GTM_SGST,GTM_IGST,pm.GPM_USD_RATE,".
					" pm.GPM_COUPON_FOR_LOCAL,pm.GPM_COUPON_FOR_EXSTATION,pm.GPM_COUPON_FOR_OUTSTATION,pm.GPM_COUPON_FOR_ONLINE,pm.GPM_TRAINING_HRS,".
					" pm.GPM_COUPON_FOR_HOURLY,pm.GPM_COUPON_FOR_PCS, pm.GPM_SUBSCRIPTION_PERIOD_TYPE, ". 
					" GSR_NAME as GPM_SUBSCRIPTION_PERIOD_TEXT, GSR_INPUT_TYPE as GPM_SUBSCRIPTION_PERIOD_INPUT_TYPE  FROM gft_product_master pm ".
				 	" join gft_product_family_master pfm on (pm.GPM_PRODUCT_CODE=pfm.GPM_PRODUCT_CODE  $trial_check $demo)". 
				 	" join gft_skew_property_master on (GFT_SKEW_PROPERTY = GSPM_CODE)".
				 	" join gft_product_group_master pg on(gpg_product_family_code= pfm.GPM_HEAD_FAMILY and gpg_skew=substring(pm.GPM_PRODUCT_SKEW,1,4))".
				 	" left join gft_product_master pm1 on(pm.GFT_HIGHER_SKEW=pm1.GPM_PRODUCT_SKEW and pm.GFT_HIGHER_PCODE=pm1.GPM_PRODUCT_CODE) ".
				 	" left join gft_hsn_vs_tax_master ht on (ht.GHT_ID=pm.GPM_TAX_ID) ".
				 	" left join gft_tax_type_master tm on (tm.GTM_ID=ht.GHT_TAX_ID) ".
				 	" left join gft_lead_hdr on (GLH_LEAD_CODE='$lead_code') ".
				 	" left join gft_vertical_master on (GTM_VERTICAL_CODE=GLH_VERTICAL_CODE) ".
				 	" left join gft_subscription_renewal_type_master ON(GSR_ID=pm.GPM_SUBSCRIPTION_PERIOD_TYPE)".
				 	" left join gft_brand_product_mapping on (GBP_VERTICAL=GLH_VERTICAL_CODE and GBP_PRODUCT=concat(pm.GPM_PRODUCT_CODE,'-',gpg_skew) and GBP_EDITION=pm.GPM_PRODUCT_TYPE and GBP_STATUS=1) ".
				 	" left join gft_product_brand_master on (GPB_ID=GBP_BRAND_ID) ".
				 	" where (1) ";
	if ($call_from=='custom') {
		$query_prod.=" and pm.gpm_status='I' and  pm.GPM_PRODUCT_TYPE=8 ";  //Inactive Custom licenses for ASA calculation
	}elseif ( ($formtype!='edit') || ($only_active_skew) ){
		$query_prod.=" and pm.gpm_status='A' $query_filter ";
	}
	/* $category values
		 0 - server, client, subscription order(renual)
		 1 - upgradation
		 2 - ass
		 5 - support incident patch updation, reinstallation
		 6 - support incident patch updation, reinstallation,services (migration, sop, reimbusment)
		13 - subscription renual
		12 - services (migration, sop, reimbusment)
		23 - complementary coupon skew
	*/
	if((int)$login_emply_id==0){
		$login_emply_id = $uid;
	}
	if($category==0){
		$subscription_skew_con="";
		if(!$show_subscription_skew){$subscription_skew_con=" and (pm.GFT_SKEW_PROPERTY NOT IN(11,16) OR pm.GPM_PRODUCT_TYPE=8 OR pm.GPM_PRODUCT_CODE=120)";}
		$query_prod.=" and pm.GFT_SKEW_PROPERTY in (".MAIN_SERVER_ORDER_ENTRY.") $subscription_skew_con ";
		$vertical_code = get_single_value_from_single_table('GLH_VERTICAL_CODE', 'gft_lead_hdr', 'GLH_LEAD_CODE', $lead_code);
		if( is_authorized_group_list($login_emply_id, array(66,106,6)) && !is_chain_certified_employee($login_emply_id,$vertical_code) ){
			$query_prod .= " and pfm.GPM_CATEGORY!=1 "; // hide HQ product list
		}
    }else if($category==1){
    	$subscription_skew_con="";
    	if(!$show_subscription_skew){$subscription_skew_con=" AND pm1.GFT_SKEW_PROPERTY not in (11)";}
    	$query_prod.=" and pm.GFT_SKEW_PROPERTY in (2,24) $subscription_skew_con ".((isset($_SESSION['uid']) and !is_authorized_group_list((string)$_SESSION['uid'],array(54)) and $formtype!='edit') ? " and pfm.gpm_status='A' ":" ");
		if(!is_authorized_group_list($uid, array(54,101))){
			$query_prod.=" and pm.GPM_LIST_PRICE!='0.00'";
		}
	}else if($category==2){
		$query_prod.=" and pm.GFT_SKEW_PROPERTY in( 4,15 ) ";
	}else if($category==5){
		$query_prod.=" and pm.GFT_SKEW_PROPERTY in (5,6,9,10) ";
	}else if($category==6){
		$query_prod.=" and pm.GFT_SKEW_PROPERTY in (18) ";
	}else if($category==13){ //supcription renewal
		$query_prod.=" and pm.GFT_SKEW_PROPERTY in(".SUBSCRIPTION_RENEWAL_ORDER_ENTRY.") and pm.GPM_PRODUCT_TYPE!=8";
	}else if($category==12){
		$query_prod.=" and pm.GFT_SKEW_PROPERTY in (7,8,12,23) ";
		if($formtype!='edit'){
			/* if($only_hourly_coupon and $kit_based) {
				$query_prod .= " and (pm.GPM_COUPON_FOR_HOURLY='1' or pm.GFT_SKEW_PROPERTY in (12) OR pm.GPM_SUPPORT_HRS>0) ";
			} */
		}
	}else if($category==23){
		$query_prod.=" and pm.GFT_SKEW_PROPERTY in (23) ";
	}else if($category==24){
		$query_prod.=" and pm.GPM_PRODUCT_CODE=71 ";
	}else{
		$query_prod.=" and pm.GFT_SKEW_PROPERTY in (".MAIN_SERVER_ORDER_ENTRY.") ";
	}
	/* for upgradation order listing only applicable upgrdation skew*/ 
	if($lpcode_lskew_arr!=null and $category==1){
		$lpcode_lskew_list="'".implode("','",$lpcode_lskew_arr)."'";
		if($lpcode_lskew_list!=''){
			$query_prod.=" and concat(pm.GFT_LOWER_PCODE,'-',pm.GFT_LOWER_SKEW) in ( $lpcode_lskew_list ) ";
		}
	}
	if(is_array($not_in_pcode_arr) && (count($not_in_pcode_arr)>0) ){
		$pcode_list="'".implode("','",$not_in_pcode_arr)."'";
		$query_prod .= " and pm.GPM_PRODUCT_CODE not in ($pcode_list) ";
	}
	if($product_code_arr!=null){
		$product_code_list=implode(',',$product_code_arr);
		if($product_code_list!=''){
			$query_prod.=" and pm.gpm_product_code in ($product_code_list) ";
		}
	}
	if($skew_property!=''){
		$query_prod.=" and pm.GFT_SKEW_PROPERTY in ($skew_property) ";
	}
	if($call_from=='quotation') {
		$query_prod.=" and pm.GPM_STORE_LIST='Y' ";
	}
	if( ($formtype!='edit') && !in_array($category,array(1,2,12)) ){
		if(!$kit_based){
			$query_prod .= " and pm.gpm_product_code!=308 ";
		}else{
			$skip_pcode_str = implode(",", array(200,500,502,400,410,420,430));
			$query_prod .= " and if(pm.gpm_product_code in ($skip_pcode_str),pm.GPM_ORDER_TYPE in (2,3,4),1) and if(pm.gpm_product_code=300,pm.GPM_PRODUCT_TYPE=8,1) ";
		}
		if($lead_type==3){
			$query_prod .= " and pm.gpm_product_code!=309 ";	
		}
		if(is_erp_customer($lead_code)){
			$query_prod .= " and if(pm.gpm_product_code in (200,500,502),pm.GPM_PRODUCT_TYPE=8,1) ";
		}
	}
	$query_prod .= " and pm.GPM_PRODUCT_TYPE NOT IN (".GFT_NOT_FOR_SALES_EDITION.") ";
	$query_prod.=" order by pfm.gpm_product_code,pgroup,if(pm.GPM_PRODUCT_TYPE=8,100,pm.GPM_PRODUCT_TYPE),pm.GFT_SKEW_PROPERTY,pm.GPM_ORDER_TYPE,pm.GPM_SKEW_DESC ";
	$result_prod=execute_my_query($query_prod,'common_util.php',true,false,2);	
	$is_same_state = /*.(boolean).*/is_same_state($lead_code);
	$glh_country = get_single_value_from_single_table("GLH_COUNTRY", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
	$i=0;
	while($qdata=mysqli_fetch_array($result_prod)){
		if($with_skew_propery==true){
			$last_field = $qdata['GSPM_DISCOUNT_PERCENTAGE'];
			if($with_order_type){
				$last_field = $qdata['GPM_ORDER_TYPE'];
			}
			$product_list[$i][0]=$qdata['gpm_product_code']."-".$qdata['gpm_product_skew']."-".$qdata['GFT_SKEW_PROPERTY']."-".$last_field;
		}else{
			$product_list[$i][0]=$qdata['gpm_product_code']."-".$qdata['gpm_product_skew'];
		}
		$product_list[$i][1]=$qdata['GPM_SKEW_DESC'];
		$product_list[$i][2]=$qdata['GPM_LIST_PRICE'];
		$product_list[$i][3]=$qdata['gpm_tax_perc'];
		$product_list[$i][4]=$qdata['GPM_SERVISE_TAX_PERC'];
		if($global_gst_mode==1){
			$product_list[$i][3]='0';
			$product_list[$i][4]='0';
		}
		$product_list[$i][5]=$qdata['GPG_PRODUCT_NAME'];
		$product_list[$i][6] = ($call_from=='quotation') ? $qdata['GPM_DISPLAY_NAME'] : $qdata['GPM_SKEW_DESC'];
		if($formtype=='edit' && $qdata['gpm_status']=='I'){  //to differentiate inactive skew in edit
			$product_list[$i][6]=$qdata['GPM_SKEW_DESC'].' *';
		}
		$product_list[$i][7]=$qdata['gpm_product_code'];
		$product_list[$i][8]=$qdata['GFT_SKEW_PROPERTY'];
		$product_list[$i][9]=$qdata['GPM_HEAD_FAMILY'];
		$product_list[$i][10]=$qdata['GPM_USD_RATE'];
		$product_list[$i][11]=$qdata['GPM_COUPONS'];
		$product_list[$i][12]=$qdata['GPM_CATEGORY'];
		$product_list[$i][13]=$qdata['GPM_TRAINING_REQUIRED'];
		$product_list[$i][14]=$qdata['GPM_PRODUCT_TYPE'];
		$product_list[$i][15]=$qdata['GPM_IS_INTERNAL_PRODUCT'];
		$product_list[$i][16]=$qdata['GPM_ORDER_TYPE'];
		$product_list[$i][17]=$qdata['GPM_DISPLAY_NAME'];
		$product_list[$i][18]=$qdata['GPM_NET_RATE'];
		$product_list[$i][19]=$qdata['higher_skew_propery'];		
		$product_list[$i][20]=$qdata['GPM_COUPON_FOR_LOCAL'];
		$product_list[$i][21]=$qdata['GPM_COUPON_FOR_EXSTATION'];
		$product_list[$i][22]=$qdata['GPM_COUPON_FOR_OUTSTATION'];
		$product_list[$i][23]=$qdata['GPM_COUPON_FOR_ONLINE'];
		$product_list[$i][24]=$qdata['GPM_TRAINING_HRS'];
		$product_list[$i][25]=$qdata['GPM_COUPON_FOR_HOURLY'];
		$product_list[$i][26]=$qdata['GPM_COUPON_FOR_PCS'];
		$cgst = $sgst = $igst = '0';
		if( (strcasecmp($glh_country, "India")==0) && ($global_gst_mode==1) ){
			if($is_same_state){
				$cgst = $qdata['GTM_CGST'];
				$sgst = $qdata['GTM_SGST'];
			}else{
				$igst = $qdata['GTM_IGST'];
			}
		}
		$product_list[$i][27]="$cgst-$sgst-$igst";
		$product_list[$i][28]=$qdata['gpm_product_code']."-".substr($qdata['gpm_product_skew'],0,4);
		$product_list[$i][29] = ($qdata['GPM_SUBSCRIPTION_PERIOD_TYPE']>0?1:0);
		$product_list[$i][30] = $qdata['GPM_SUBSCRIPTION_PERIOD_TEXT'];
		$product_list[$i][31] = $qdata['GPM_SUBSCRIPTION_PERIOD_INPUT_TYPE'];
		$i++;
	}
	return $product_list;
}

/**
 * @param string $group_id
 *
 * @return string[int][int]
 */
function cheque_status_list($group_id=null){
	global $uid;
	$query_cheque="select GCS_STATUS,GCS_STATUS_ABR from gft_cheque_status_master ";
	$query_cheque.=(!is_authorized_group($uid,1)? " where GCS_STATUS_ABR in ('H','O','D','W')":"" ) ;
	$query_cheque.=" order by 2 ";
	$result_cheque=execute_my_query($query_cheque,'common_util.php',true,false,2);
	$status_list[0][0]="0";
	$status_list[0][1]="Valid Status";
	$status_list[1][0]="-1";
	$status_list[1][1]="Invalid Status";
	$i=2;
	while($qdata=mysqli_fetch_array($result_cheque)){
		$status_list[$i][0]=$qdata['GCS_STATUS_ABR'];
		$status_list[$i][1]=$qdata['GCS_STATUS'];
		$i++;
	}
	return $status_list;
}	

/**
 * @param string $lead_type
 *
 * @return string[int][int]
 */
function get_lead_subtype($lead_type=null){
	$query_subtype="select GLS_TYPE_CODE,GLS_SUBTYPE_CODE,GLS_SUBTYPE_NAME,GLS_SUBTYPE_DESC " .
			       "from gft_lead_subtype_master where GLS_SUBTYPE_STATUS='A' "; 
	if($lead_type!=null){$query_subtype.=" and GLS_TYPE_CODE=$lead_type "; }
	$query_subtype.="  order by 3 ";
	$result_subtype=execute_my_query($query_subtype,'common_util.php',true,false,2);
	$i=0;
	$stype_list=/*. (string[int][int]) .*/ array();
	while($qdata=mysqli_fetch_array($result_subtype)){
		$stype_list[$i][0]=$qdata['GLS_SUBTYPE_CODE'];
		$stype_list[$i][1]=$qdata['GLS_SUBTYPE_NAME'];
		$stype_list[$i][2]=$qdata['GLS_TYPE_CODE'];
		$i++;
	}	
	return $stype_list;
}	


/**
 * @param string $type
 * @param boolean $add_fy
 * 
 * @return string[int][int]
 */
function get_product_type($type=null,$add_fy=true){
	$product_list=/*. (string[int][int]) .*/ array();

	$query_type="select GPT_TYPE_ID,GPT_TYPE_NAME from gft_product_type_master"; 
	$result_type=execute_my_query($query_type,'common_util.php',true,false,2);
	$i=0;
	if($type==null && $add_fy==true){
	    $product_list[$i][0]="";
	 	$product_list[$i][1]="Any";
	 	$i++;
	 	$product_list[$i][0]="N";
	 	$product_list[$i][1]="Payable";
	 	$i++;
	 	$product_list[$i][0]="Y";
	 	$product_list[$i][1]="Free Edition";
	 	$i++;
	}

	while($qdata=mysqli_fetch_array($result_type)){
		$product_list[$i][0]=$qdata['GPT_TYPE_ID'];
		$product_list[$i][1]=$qdata['GPT_TYPE_NAME'];
		$i++;
	}
	return $product_list;
}

/**
 * @param string $category
 * @param string $lead_source_id
 * @param string $with_default
 *
 * @return string[int][int]
 */

function get_lead_source($category=null,$lead_source_id=null,$with_default='Select'){
	$query_lead_source_name="SELECT GLS_SOURCE_CODE,GLS_SOURCE_NAME,GLM_NAME FROM gft_lead_source_master, gft_lead_source_category_master  " .
			"where GLS_SOURCE_CATEGORY=GLM_ID and  GLS_STATUS='A'  ";
	if($category!=''){ $query_lead_source_name.=" and  GLS_SOURCE_CATEGORY='$category' ";}		
    if($lead_source_id!=null){ 	$query_lead_source_name.= "and GLS_SOURCE_CODE='$lead_source_id' ";    }
    $query_lead_source_name.="  order by GLS_ORDER_BY ";
    $result_lead_source_name=execute_my_query($query_lead_source_name,'common_util.php',true,false,2);  
    $i=0;
    $lead_source_list=/*. (string[int][int]) .*/ array();
    if($lead_source_id==null && $with_default!=null){
	    $lead_source_list[$i][0]=0;
	 	$lead_source_list[$i][1]=$with_default;
	 	$i++;
    }
	while($qdata=mysqli_fetch_array($result_lead_source_name)){
		$lead_source_list[$i][0]=$qdata['GLS_SOURCE_CODE'];
		$lead_source_list[$i][1]=$qdata['GLS_SOURCE_NAME'];
		$lead_source_list[$i][2]=$qdata['GLM_NAME'];
		$i++;
	}
	return $lead_source_list;
}

/**
 * @param string[] $omit_list_arr
 * @param string $type_id
 *
 * @return string[int][int]
 */
function get_type_list($omit_list_arr=null,$type_id=null){
	$omit_list='';
	if($omit_list_arr!='') $omit_list=implode(',',$omit_list_arr);
	$query_type_name="SELECT GTM_TYPE_CODE,GTM_TYPE_NAME FROM gft_type_master ";
	if($type_id!=null){
		$query_type_name.=" where GTM_TYPE_CODE=$type_id ";
	}else if($omit_list!=''){
		$query_type_name.=" where GTM_TYPE_CODE not in($omit_list) ";
	}
	return get_two_dimensinal_array_from_query($query_type_name,'GTM_TYPE_CODE','GTM_TYPE_NAME');
}

/** 
 * @param string $business_type
 * @param boolean $select
 * @param string $vertical_codes
 * @param string $prod_sync_vertical
 *
 * @return string[int][int]
 */
function get_vertical_name($business_type=null,$select=true, $vertical_codes=null,$prod_sync_vertical=null){
	$vertical_type_list=/*. (string[int][int]) .*/ array();

	//For target planning vertical wise 'HQ' is added as one Vertical 
	//To prevent from  other filter GTM_SHOW_IN_VFILTER column added 
	$query_vertical_name=" SELECT distinct(vt.GTM_VERTICAL_CODE), vt.GTM_VERTICAL_NAME, vt.GTM_VERTICAL_DESC,gbr_product,vt.GTM_PROD_SYNC_CODE ," .
			"vtm.GTM_VERTICAL_NAME 'macro_name',vt.GTM_MICRO_OF " .
			" FROM gft_vertical_master vt " .
			" left join gft_vertical_master vtm on (vtm.GTM_VERTICAL_CODE=vt.GTM_MICRO_OF) ";
	$query_vertical_name.=" left join gft_bvp_relation bvp on (gbr_vertical=vt.gtm_vertical_code ".($business_type==null?" and gbr_business_id=1":"").") ";
	$query_vertical_name.=" where vt.GTM_STATUS='A' and vt.GTM_SHOW_IN_VFILTER='Y' ".  /*vt.GTM_IS_MACRO='N' and*/
	($vertical_codes!=null?" and vt.GTM_VERTICAL_CODE in ($vertical_codes) " :"");
	if($prod_sync_vertical!=null){ $query_vertical_name.=" and vt.GTM_PROD_SYNC_CODE='$prod_sync_vertical' "; }
	if($business_type!=null){
		if(is_array($business_type)){
			$business_type_str=implode(',',/*. (string[int]) .*/ $business_type);
		}else $business_type_str=$business_type;	
		$query_vertical_name.=" and gbr_business_id in ($business_type_str) ";
	}
	$query_vertical_name.=" order by macro_name asc,vt.GTM_VERTICAL_NAME ";
	$result_vertical_name=execute_my_query($query_vertical_name,'common_util.php',true,false,2);  
    $i=0;
    if($select==true){
	    $vertical_type_list[$i][0]=0;
	 	$vertical_type_list[$i][1]="Any";
	 	$vertical_type_list[$i][2]="Any";
	 	$vertical_type_list[$i][3]=0;
	 	$vertical_type_list[$i][4]=0;
	 	$i++;
    }
 	if($result_vertical_name){
		while($qdata=mysqli_fetch_array($result_vertical_name))
		{
			 $vertical_type_list[$i][0]=$qdata[0];
	 		 $vertical_type_list[$i][1]=$qdata[1];
	 		 $vertical_type_list[$i][2]=$qdata['GTM_VERTICAL_DESC'];
	 		 $product_code=substr($qdata['gbr_product'],0,3);
			 $pcode_version=substr($qdata['gbr_product'],4);
			 $vertical_type_list[$i][3]=$product_code;
			 $vertical_type_list[$i][4]=$pcode_version;
			 $vertical_type_list[$i][5]=$qdata['macro_name'];
			 $vertical_type_list[$i][6]=$qdata['GTM_MICRO_OF'];
	 		 $i++;
		}
 	}
	return $vertical_type_list;
}

/**
 * @param boolean $select_need
 * @param boolean $include_others
 *
 * @return string[int][int]
 */
function get_gft_prod_trasaction_documents($select_need=false,$include_others=true){

	$prod_trasaction_documents_list = /*. (string[int][int]) .*/ array();

	$query_prod_trasaction_documents="SELECT GPT_ID, GPT_NAME, GPT_DESC," .
			" GPT_STATUS,GPM_MODULE FROM gft_prod_trasaction_documents ,gft_prod_module_master where " .
			" GPT_STATUS='A'  and GPM_ID=GPT_MODULE order by GPM_MODULE_ORDER_BY,GPT_ORDER_BY ";
	$result_prod_trasaction_documents=execute_my_query($query_prod_trasaction_documents,'common_util.php',true,false,2);
    $i=0;
    if($select_need==true){
	    $prod_trasaction_documents_list[$i][0]='0';
	 	$prod_trasaction_documents_list[$i][1]="-Select-";
	 	$i++;
    }
	while($qdata=mysqli_fetch_array($result_prod_trasaction_documents)){
		 $prod_trasaction_documents_list[$i][0]=$qdata['GPT_ID'];
 		 $prod_trasaction_documents_list[$i][1]=$qdata['GPT_NAME'];
 		 $prod_trasaction_documents_list[$i][2]=$qdata['GPM_MODULE'];
 		 $i++;
	}
	 $prod_trasaction_documents_list[$i][0]='-1';
 	 $prod_trasaction_documents_list[$i][1]='Others';
 	 $prod_trasaction_documents_list[$i][2]='Other Documents';
	 return $prod_trasaction_documents_list;
}

/**
 * @param string $sel_mail_group
 *
 * @return string[int][int]
 */
function show_mail_id($sel_mail_group=null)
{
	$mail_list=/*. (string[int][int]) .*/ array();

	$query="select GEM_EMAILID from gft_emailid_master,gft_email_group_master " .
		   "where GEG_EGROUP_ID=GEM_EMAIL_GROUP_FK  ";
	$query_grg='';	   
	for($c=0;$c<count($sel_mail_group);$c++){
		if($c!=0) $query_grg.=" or ";
		$query_grg.=" GEG_EGROUP_ID='$sel_mail_group[$c]' ";
	}
	if($query_grg!='') $query.= "and ($query_grg) ";
	$query.= " order by GEM_EMAILID ";
	$result=execute_my_query($query,'common_util.php',true,false,2);
	$i=0;
	while($qdata=mysqli_fetch_array($result)){
		$mail_list[$i][0]=$qdata[0];
		$mail_list[$i][1]=$qdata[0];
		$i++;
	}		
	return $mail_list;			
}   


/**
 * @param int $id
 * @param boolean $show_only_approval_list
 *
 * @return string[int][int]
 */
function get_reinstall_reason($id=0,$show_only_approval_list=false){
	$query="select  GRR_CODE,GRR_NAME,GRR_DESC,GRR_DETAIL_DESC_NEEDED,GRR_APPROVAL_NEEDED " .
			" from gft_reinstall_reason_master where GRR_STATUS='A' ";
	if($id!=0){
		$query.=" and  GRR_CODE='$id' ";
   	}
   	if($show_only_approval_list==true){
   		$query.=" and GRR_APPROVAL_NEEDED='Y' ";	
   	}
   	$query.=" order by GRR_NAME ";
    $result=execute_my_query($query,'common_util.php',true,false,2);
    $i=0;
    $reinstall_reason_list=/*. (string[int][int]) .*/ array();
    while($qd=mysqli_fetch_array($result)){
    	$reinstall_reason_list[$i][0]=$qd['GRR_CODE'];
    	$reinstall_reason_list[$i][1]=$qd['GRR_NAME'];
    	$reinstall_reason_list[$i][2]=$qd['GRR_DETAIL_DESC_NEEDED'];
    	$reinstall_reason_list[$i][3]=$qd['GRR_APPROVAL_NEEDED'];
    	$i++;
    }//End of while
	return $reinstall_reason_list;
}//END of fn


/**
 * @param string $id
 * @param string $name
 * @param string[int][int] $two_dim_value_arr
 * @param string $selected_value
 * @param string $tab_index
 * @param string $default_value
 * @param string $style
 * @param boolean $add_opt_group
 * @param string $event_function
 * @param int $size
 * @param string[int] $hidden_val_arr
 * @param string $css_class_name
 * @param boolean $is_reqd
 * @param string $additional_attributes
 * @param string $default_class
 *
 * @return string 
 */
function fix_combobox_with($id,$name,$two_dim_value_arr,$selected_value,$tab_index='1',
$default_value=null,$style=null,$add_opt_group=false,$event_function='', $size=1,$hidden_val_arr=null,$css_class_name='',
$is_reqd=false,$additional_attributes='',$default_class='formStyleTextarea'){
	$multiple=($size > 1 ? "multiple=\"multiple\"":"");
	$multiplearray=($size > 1 ? "[]":"");
	$requires_str = (($is_reqd)?"required":'');
	$return_value="<select  size=\"$size\" name=\"$name$multiplearray\" id=\"$id\" tabindex=\"$tab_index\"".  
			  ($size>1?"" :" class=\"$default_class $css_class_name\""). "$multiple $style $event_function $requires_str $additional_attributes>";
	if($default_value!=''){ $return_value.="<option value=\"0\">$default_value</option>"; }			   
	$opt_group="";
    for($i=0;$i<count($two_dim_value_arr);$i++){
    	if(is_array($two_dim_value_arr[$i])){
			$svalue=$two_dim_value_arr[$i][0];
			$sname=$two_dim_value_arr[$i][1];
			if($add_opt_group==true and isset($two_dim_value_arr[$i][2]) and $two_dim_value_arr[$i][2]!=''){
				$curr_opt_group=$two_dim_value_arr[$i][2];
				$curr_opt_group_id = isset($two_dim_value_arr[$i][3])?$two_dim_value_arr[$i][3]:"";
				if($opt_group!=$curr_opt_group){
				$return_value.="<optgroup label=\"$curr_opt_group\"";
				if($curr_opt_group_id!=='') $return_value .= "id=\"$curr_opt_group_id\"";
				$return_value .= ">";
				$opt_group=$curr_opt_group;
				}
			}
		}else{
			$sname=$svalue=$two_dim_value_arr[$i];
		}
		$s='';
		if($size>1 and  is_array($selected_value)){
			$skey_name=array_search($svalue,$selected_value);
			if ($skey_name !== false){
				if($selected_value[$skey_name]==$svalue){
					$s="selected";
				}
			}   
		}else{
			$s=($svalue==$selected_value?"selected=selected":'');
		}
		if($sname=='separator'){
			$return_value.="<optgroup label=\"---------------------\">";
			continue;
		}
		$hidden = "";
		if($hidden_val_arr!=null && is_array($hidden_val_arr)) {
			if(in_array($svalue, $hidden_val_arr)){
				$hidden = "class='hide nores'";
			}
		}
		$return_value.="<option $hidden value=\"$svalue\" $s> $sname</option>";
    }//end of for
	$return_value.="</select>";   
	return $return_value;
}

/**
 * @param int $tab_index
 * @param boolean $show_only_approval_list
 *
 * @return void
 */
function product_reinstall_dtl($tab_index=1,$show_only_approval_list=false){
	$reinstall_reason_list=get_reinstall_reason(0,$show_only_approval_list);
	global $reinstall_reason_code;
	$tab_index++;
	if($show_only_approval_list==true and count($reinstall_reason_list)==1){
		$default_value='';
	}else {$default_value='-Select-';}
	$hidden_reinstall_dtl='';
	for($i=0;$i<count($reinstall_reason_list);$i++){
		$reinstall_code=$reinstall_reason_list[$i][0];
		$reinstall_dtl_needed=$reinstall_reason_list[$i][2];
		//$reinstall_app_needed=$reinstall_reason_list[$i][2];	
		$hidden_reinstall_dtl.="<input type='hidden' id='reason_vs_dtl_needed_$reinstall_code' value='$reinstall_dtl_needed' >";
	}
	$reinstall_reason_combo_list=fix_combobox_with($id='reinstall_reason',$name='reinstall_reason',
			$two_dim_value_arr=$reinstall_reason_list,$selected_value=$reinstall_reason_code,
			$tab_index,$default_value,$style="Style='width:150px'",false,"onchange='javascript:get_reinstall_dtl(this.value)'");
echo<<<END
<script type="text/javascript">
function get_reinstall_dtl(srid){
	if(document.getElementById("reason_dtl")){
		if($("reason_vs_dtl_needed_"+srid) && $("reason_vs_dtl_needed_"+srid).value=='Y'){
			document.getElementById("reason_dtl").className="unhide";
		}else {
			document.getElementById("reason_dtl").className="hide";
		}
	}
}
</script>
<tr><td class="datalabel" width="200">
<font color="red" size="3" >*</font>Reinstallation Reason $hidden_reinstall_dtl </td>
<td class="head_black">$reinstall_reason_combo_list</td></tr>	    
END;
}//end of fn	

/**
 * @return void
 */
function product_patch_updation_dtl(){
	$patch_reason_list=get_two_dimensinal_array_from_table('gft_patchupdate_reason_master','GPR_CODE','GPR_NAME','GPR_STATUS','A');
echo<<<END
<script>
function get_patchinstall_dtl(srid){
	if(document.getElementById("reason_dtl")){
		if(srid==4){ document.getElementById("reason_dtl").className="unhide";}
		else { document.getElementById("reason_dtl").className="hide";}
	}
}</script>
<tr><td class="datalabel" width="200"><font color="red" size="3" >*</font> Patch Reason </td>
<td><select name="patch_reason" id="patch_reason" onchange="javascript: get_patchinstall_dtl(this.value);" class="formStyleTextarea" style="width:150px;">
<option value=0>Select</option>
END;
	for($i=0;$i<count($patch_reason_list);$i++)	{
		$s="";
		$cmbpatch_reason_id=$patch_reason_list[$i][0];
		//if ($patch_reason_code==$cmbpatch_reason_id) $s="selected";
		$patch_reason=$patch_reason_list[$i][1];
echo<<<END
<option value=$cmbpatch_reason_id $s> $patch_reason</option>
END;
	}
echo<<<END
</select>
END;
}

/**
 * @param string $emp_id
 * @param boolean $exclude_user
 * @param boolean $all_for_partner
 *
 * @return string[int][int]
 */
function get_salesperson_name($emp_id=null,$exclude_user=false,$all_for_partner=false){
	global $uid;	
	if($emp_id==null) $emp_id=$uid;
	$without_ids=SALES_DUMMY_ID;
	if($exclude_user==true and !empty($emp_id)) $without_ids.=(empty($without_ids)?$emp_id:','.$emp_id);
	$without_dummy=" and a.gem_emp_id not in ($without_ids) " ;

    /* Query has to be changed not able to check ggm_group_id!=33  like that */
	$query="select a.gem_emp_id,gem_emp_name,gem_email from gft_emp_master a " .
			" left join gft_role_group_master rg on (grg_role_id=gem_role_id) " .
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id) " .
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id) " .
			" where ggm_group_id in(5,27,34,35,12,23,13) and grg_role_id not in (26,73) and gem_status='A' $without_dummy group by gem_emp_id   ";
	/*sales,Presales ,partner mgmt,dev-demo,business Management,Feild Team */
	if(is_authorized_group_list($emp_id,array(5)) and !is_authorized_group_list($emp_id,array(12))){ /* 12 is business Managment */
	/* not belongs to sales group and business development */
	  
	    $query2="select a.gem_emp_id,gem_emp_name,gem_email from gft_emp_master a " .
			" left join gft_role_group_master rg on (grg_role_id=gem_role_id) " .
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id) " .
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id) " .
			" where ggm_group_id in(27,34,35,12,23,13) and grg_role_id not in (26,73)  and gem_status='A' $without_dummy group by gem_emp_id   ";
	    /*Presales ,partner mgmt,dev-demo,business Management,Feild Team */
		$query1="select a.gem_emp_id,a.gem_emp_name,a.gem_email from gft_emp_manager_relation r " .
				" join gft_emp_manager_relation t on((r.gmr_terri_m =t.gmr_terri_m and t.gmr_terri_m_ck=true) " .
				" or (r.gmr_area_m=t.gmr_area_m and t.gmr_area_m_ck=true ) or " .
				"  (r.gmr_region_m=t.gmr_region_m and t.gmr_region_m_ck=true ) or " .
				"  (r.gmr_zone_m=t.gmr_zone_m and t.gmr_zone_m_ck=true and t.gmr_region_m_ck=false)) " .
				" join gft_emp_master a on(t.gmr_emp_id=a.gem_emp_id and a.gem_status='A') " .
				" where r.gmr_emp_id='$emp_id' $without_dummy   ";
		/* they can see zone level executives */		
		$query="select a.gem_emp_id,gem_emp_name,gem_email from (($query1) union ($query2))as a  group by a.gem_emp_id ";
		
	}
	else if(is_authorized_group($emp_id,13)){//cp
		global $cgi_incharge_emp_id;
		$extra_cond = ($all_for_partner)?" or a.GEM_OFFICE_EMPID!=0 ":"";
		$query= " select a.gem_emp_id,gem_emp_name,gem_email from gft_emp_master a ".
				" left join gft_role_group_master rg on (grg_role_id=gem_role_id) " .
				" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id) " .
				" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id) " .
				" where (a.gem_emp_id='$cgi_incharge_emp_id' $extra_cond) and gem_status='A' group by a.gem_emp_id " ;
	}	
	$query.="  order by gem_emp_name ";
	
	$result_sales_person_name=execute_my_query($query,'common_util.php',true,false,2);
    $i=0;
    $assigned_to_list=/*. (string[int][int]) .*/ array();
    while($qdata=mysqli_fetch_array($result_sales_person_name)){
		 $assigned_to_list[$i][0]=$qdata['gem_emp_id'];
		 $assigned_to_list[$i][1]=$qdata['gem_emp_name'];
		 $assigned_to_list[$i][2]=$qdata['gem_email'];
         $i++;
	}
	return $assigned_to_list;
} 

/**
 * @param string $group_type
 * @param string[int] $groups_arr
 * @param boolean $inner_maintainence
 * @param string $use_group_privilage_filter
 * @param string $def_name
 * @param string $def_val
 *  
 * @return string[int][int]
 */

function get_group_list($group_type=null,$groups_arr=null,$inner_maintainence=true,$use_group_privilage_filter=null,$def_name='',$def_val=''){
	$groups_to_see='';
	if($use_group_privilage_filter!=null && is_array($use_group_privilage_filter)){
		$use_group_privilage_filter_str=implode(',',/*. (string[int]) .*/$use_group_privilage_filter);
	}else {$use_group_privilage_filter_str=$use_group_privilage_filter; }
	$query="select GGM_GROUP_ID, GGM_GROUP_NAME, GGM_GROUP_DESC, GGM_GROUP_TYPE, GGM_STATUS from gft_group_master ";
	if($use_group_privilage_filter!=null){
		$query.=" join gft_privilages_enable_master pe on (GPL_GROUP_ID=GGM_GROUP_ID and GPL_PREVILAGE_ID in ($use_group_privilage_filter_str) )";
	}
	$query.=" where ggm_status='A'";	
	if($group_type!=null and ($group_type=='E' or $group_type=='R')){$query.=" and GGM_GROUP_TYPE='$group_type' ";}
	if(is_array($groups_arr)){$groups_to_see=implode(',',$groups_arr);
	}else if($groups_arr!=''){$groups_to_see=$groups_arr;}
	if($groups_to_see!=''){$query.=" and ggm_group_id in ($groups_to_see) ";}
	if($inner_maintainence!=null){ $query.=" and GGM_INNER_MAINTAINENCE='N' ";}
	return get_two_dimensinal_array_from_query($query,'GGM_GROUP_ID','GGM_GROUP_NAME',null,$def_name,$def_val);
}

/**
 * @param int $tab_index
 * @param string $default_value
 *
 * @return void
 */
function show_from_month_filter($tab_index,$default_value){
echo<<<END
<input name="from_month" type="text" class="formStyleTextarea" id="from_month" value="$default_value" readonly>&nbsp;
<img src="images/date_time.gif" class="imagecur" id="onceDateIcon1" width="16" height="16" border="0" align="middle" alt="">
<script type="text/javascript">init_date_func("from_month","%Y-%b","onceDateIcon1","Bl");</script>
END;
}  

/**
 * @param int $tab_index
 * @param string $default_value
 *
 * @return void
 */
function show_to_month_filter($tab_index,$default_value){ 
echo<<<END
<input name="to_month" type="text" class="formStyleTextarea" id="to_month" value="$default_value" readonly>&nbsp;
<img src="images/date_time.gif" class="imagecur" id="onceDateIcon2" width="16" height="16" border="0" align="middle" alt="">
<script type="text/javascript">init_date_func("to_month","%Y-%b","onceDateIcon2","Bl");</script>
END;
}

/**
 * @param string $query
 * @param string $headcontent
 *
 * @return void
 */
function exportreport($query,$headcontent=null){
	if(preg_match('^( )*select',$query))
	{
		if($result=execute_my_query($query)){
			if(isset($headcontent)){
				$content=implode(",", explode(",",$headcontent))."\r\n";
			}else{
				$content='';
				for($i=0;$i<mysqli_num_fields($result);$i++){
					$content.=($i!=0?',':'').mysqli_field_name_wrapper($result,$i);
				}
				$content.="\r\n";
			}
			while($query_data=mysqli_fetch_array($result))
			{	
				/*$data = array(count($query_data)); 
				for($i=0;$i<count($query_data);$i++){
					$data[$i]=$query_data[$i];
					$data[$i]=str_replace("\t"," ",$data[$i]);
				}*/
				//$data = array(count($query_data));
				$data = /*. (string[int]) .*/ array();
				for($i=0;$i<count($query_data);$i++){
					$data[$i]=$query_data[$i];
					$data[$i]=(string)str_replace(",", ";" ,$data[$i]);
					$data[$i]=(string)str_replace("\n", ";;" ,$data[$i]);
					$data[$i]=(string)str_replace("\r", "" ,$data[$i]);
					$data[$i]=(string)str_replace("\"", "\'" ,$data[$i]);
					$data[$i]="\"".$data[$i]."\"";
				}
				$content.= implode(",",$data)."\r\n";
			}
			header("Content-Disposition: inline; filename=report.csv");
			header("Content-Type: text/csv; charset=UTF-8");
			header("Cache-Control: post-check=0, pre-check=0", false );
			header("Content-Length: ".strlen($content));
			print $content;
			exit;
		}else{
		  		print " <br> $query <br>";
		}
	}else{
		die("<h2> Invalid Operation.. </h2>");
	}
}

/**
 * @param string $customer_name
 * @param string $authority_name
 * @param string $door_no
 * @param string $street_no
 * @param string $street_name
 * @param string $landMark
 * @param string $area_name
 * @param string $location
 * @param string $city
 * @param string $pincode
 * @param string $state
 * @param string $phone_no
 * 
 * @return string
 */
function check_lead_entry($customer_name,$authority_name,$door_no,$street_no,$street_name,$landMark,
				    				$area_name,$location,$city,$pincode,$state,$phone_no){
	$query="select * from gft_lead_hdr where glh_cust_name='$customer_name' " .
			($door_no!=''?" and  GLH_DOOR_APPARTMENT_NO='$door_no'":"").
			($street_no!=''?" and GLH_STREET_DOOR_NO='$street_no'" :"").
			($street_name!=''?" and  GLH_CUST_STREETADDR1='$street_name' ":"") .
			($location!=''?" and GLH_CUST_STREETADDR2='$location'" :"").
			($city!=''?" and GLH_CUST_CITY='$city'":"").
			($pincode!=''?" and glh_cust_pincode='$pincode' ":"") .
			($location!=''?" and glh_cust_streetaddr2 ='$location' ":'');
	$result=execute_my_query($query,'common_util.php',true,false,2);
	
	if(mysqli_num_rows($result)>0){
		if($data=mysqli_fetch_array($result)){
			return $data['GLH_LEAD_CODE'];
		}
	}
	return '';
}

/**
 * @param mixed[int][int] $value_arr   value can be int[int][int] -or- string[int][int]
 * @param string[int] $value_arr_width
 * @param string[int] $value_arr_align
 * @param string[int] $value_arr_class
 * @param string[int] $row_class_in_arr
 * @param string[int] $table_row_id
 * @param string[int] $table_row_style
 * @param string[int][int] $value_arr_rowspan
 * @param string[int][int] $value_arr_colspan
 * @param string $table_row_name
 * @param int $child
 * @param boolean $highlight_row
 * @param string $highlight_color
 *
 * @return void
 */

function print_resultset($value_arr,$value_arr_width=null,$value_arr_align=null,$value_arr_class=null,
	$row_class_in_arr=null,$table_row_id=null,$table_row_style=null,$value_arr_rowspan=null,
	$value_arr_colspan=null,$table_row_name=null,$child=0,$highlight_row=false,$highlight_color=''){
	global $alt_row_class;
	$s=0;

	foreach($value_arr as $i => $va){
		$trow_id='';
		$trow_style='';
		$is_total = isset($value_arr[0][0]) && $value_arr[0][0] == "Total" ? true : false;
		if(isset($table_row_id[$i]) and $table_row_id[$i]!=null){
			$trow_id=' id ="'.$table_row_id[$i].'"';
		}
		if(isset($table_row_style[$i]) and $table_row_style[$i]!=null){
			$trow_style=$table_row_style[$i];
		} 
		if ($child!=0){
			$trow_style.="style='Display:none'"; 
		}
		else {
			//$trow_style="";
		}
		if($table_row_name!=null && $table_row_name!=''){
			$trow_id.=' name="'.$table_row_name.'"';
		}
		if($highlight_row === true) {
			$color = 'yellow';
			if($highlight_color!='') {
				$color = $highlight_color;
			}
			echo "\n"."<tr style='background-color:$color;'>";
		}
		else if(isset($row_class_in_arr[$i]) and $row_class_in_arr[$i]!=null){
				echo "\n".'<tr class="'.$row_class_in_arr[$i].'" '.$trow_id .$trow_style.">";
		}else if($value_arr[$i][0]!=null ){
			echo "\n".'<tr class="'.$alt_row_class[$s].'" '.$trow_id.$trow_style.">";
		}else if($value_arr[$i][0]=='' and isset($value_arr[$i][1]) and !isset($value_arr[$i][2])){//zone
			echo "\n".'<tr class="'.$alt_row_class[2].'" '.$trow_id.$trow_style.">";
		}else if($value_arr[$i][0]=='' and $value_arr[$i][1]=='' and $value_arr[$i][2]!=''){//region
			echo "\n".'<tr class="'.$alt_row_class[3].'" '.$trow_id.$trow_style.">";
		}else if($value_arr[$i][0]=='' and $value_arr[$i][1]=='' and $value_arr[$i][2]=='' and $value_arr[$i][3]!=''){//area
			echo "\n".'<tr class="'.$alt_row_class[4].'" '.$trow_id.$trow_style.">";
		}else {
			echo "\n".'<tr class="'.$alt_row_class[$s].'" '.$trow_id .$trow_style.">";
		}
		for($j=0;$j<count($value_arr[$i]);$j++){
			if(!isset($value_arr[$i][$j])){$value_arr[$i][$j]='';}
		    $value=$value_arr[$i][$j];
		    if(!isset($value_arr_class[$j]) or $value_arr_class[$j]==null){
		    	$class_td='class="content_txt" ';
		    }else if(isset($value_arr_class[$j]) and $value_arr_class[$j]!=''){
		    	$class_td=' class="'.$value_arr_class[$j].'" '; 
		    }else{
			    $class_td='';
		    }
		    $align=(($value_arr_align!=null and isset($value_arr_align[$j]))?'align="'.$value_arr_align[$j].'"':'');
			$width=(($value_arr_width!=null and isset($value_arr_width[$j]))?'width="'.$value_arr_width[$j].'px"':'');
		    $boldStyle = "";
		    if($value_arr_rowspan!=null){
		    	if(isset($value_arr_rowspan[$i][$j]) and $value_arr_rowspan[$i][$j]>1){
		    		$rowspan_val=$value_arr_rowspan[$i][$j];
		    		$align.=" rowspan='$rowspan_val' valign='center' ";
		    	}
		    }
		    if($value_arr_colspan!=null){
		    	if(isset($value_arr_colspan[$i][$j]) and $value_arr_colspan[$i][$j]>1){
		    		$colspan_val=$value_arr_colspan[$i][$j];
		    		$align.=" colspan='$colspan_val' ";
		    	}
		    }
		    if($is_total){
		        $boldStyle = " style='font-weight:bold'";
		    }
		    echo "\n".'<td '.$class_td. $align . $boldStyle. $width.' wrap>'.$value.'</td>';
		}
		$s=($s==0)?1:0;
		echo '</tr>';			
	}
} 

/**
 * @param string $activie
 * @param string $product_code
 * @param string $prod
 * @param string $name_field
 * @param string $wh_condition
 * 
 * @return string[int][int]
 */
function get_list_product_family_group_master($activie=null,$product_code=null,$prod=null,$name_field='GPM_PRODUCT_NAME',$wh_condition=''){
	$query_pl =" select concat(gpm_product_code,'-',gpg_skew) pg_code ,concat($name_field,'-',gpg_version) pg_name" .
			" from gft_product_group_master,gft_product_family_master " .
			" where gpg_product_family_code=gpm_product_code $wh_condition " .
			($activie!=null?" and gpg_status='A' ":"") .
			($product_code!=null?" and '$product_code' like concat(gpm_product_code,'-',gpg_skew,'%') ":"") .
			($prod!=null?" and gpm_product_code='$prod' ":"") ;
	return get_two_dimensinal_array_from_query($query_pl,'pg_code','pg_name');
}

/**
 * @param int $vertical
 * @param int $business_id
 * 
 * @return string
 */
function get_product_from_vertical($vertical=1,$business_id=1){
	$vertical=(empty($vertical)?'1':$vertical);
	$business_id=(empty($business_id)?1:$business_id);
	$product_skew = "";
	$query="select gbr_product from gft_bvp_relation where gbr_vertical='$vertical' and gbr_business_id=$business_id ";
	$result=execute_my_query($query);
	if($result){
		if(mysqli_num_rows($result)==0){
			$query="select gbr_product from gft_bvp_relation where gbr_vertical='$vertical'  ";
			$result=execute_my_query($query);
		}
		if(mysqli_num_rows($result)>0){
			$qdata=mysqli_fetch_array($result);
			$product_skew=$qdata['gbr_product'];
		}	
		return $product_skew;
	}
	return $product_skew;		
}

/**
 * @param string $product_code
 * @param string $skew
 * 
 * @return string
 */
function get_installation_guide_for($product_code,$skew){
	$skew=substr($skew,0,4);
	$query="select GPG_INSTALLATION_GUIDE_URL from gft_product_group_master " .
			"where gpg_product_family_code='$product_code' ".($skew!=''?" and gpg_skew='$skew' ":"");
		
	$result=execute_my_query($query);
	$qdata=mysqli_fetch_array($result);
	$inst_guide=$qdata['GPG_INSTALLATION_GUIDE_URL'];
	return $inst_guide;	
}

/**
 * @param string $mobileno
 * 
 * @return string
 */
function adjustMobileNumber($mobileno){
    $validmobileno="";
    $validmobileno=substr($mobileno,-10);
    return $validmobileno;
}

/**
 * @param int $smsid
 * @param string $mobileno
 * @param int $status
 * @param string $content
 * @param string $customerid
 * @param int $emp_id
 * @param string $date
 * @param string $resend_needed
 *
 * @return void
 */
function copy_sms_sent_details($smsid,$mobileno,$status=0,$content=null,$customerid=null,$emp_id=0,
$date=null,$resend_needed=null){
	$query_msg="select gos_emp_id,gos_category, gos_receiver_mobileno, gos_sms_content," .
			" gos_customer_leadcode, gos_sender_id " .
		    " from gft_sending_sms left join gft_lead_hdr on (GLH_LEAD_CODE=gos_customer_leadcode) " .
		    " where gos_id='$smsid' ";
		    
	$result_msg=execute_my_query($query_msg,'common_util.php',true,false,2);
	$qmsg=mysqli_fetch_array($result_msg);
	$customer_id=$qmsg['gos_customer_leadcode'];
	$sms_content=$qmsg['gos_sms_content'];
	$category=$qmsg['gos_category'];
	$sender=$qmsg['gos_sender_id'];
	entry_sending_sms_to_customer($mobileno,$sms_content,$category,$customer_id,$status=0,$sender,$send_to_alert=0,null,$newly_added=true);  
	
}

/**
 * @param string $smsid
 * @param string $mobileno
 * @param string $status
 * @param string $content
 * @param string $customerid
 * @param int $emp_id
 * @param string $date
 * @param string $resend_needed
 *
 * @return void
 */
function update_sms_sent_details($smsid,$mobileno,$status="0",$content=null,$customerid=null,$emp_id=0,$date=null,$resend_needed=null){
	$lead_terri=explode('-',$customerid);
	$lead_code=(int)$lead_terri[0];
	if($date=="")
		$date=date('Y-m-d H:i:s');
		$before=date('Y-m-d H:i:s',strtotime($date)-(3600*24*3));
	if($smsid!=""){
		if($emp_id!=0){ 
			$cond_str=" and gos_emp_id='$emp_id' ";
		}else if($lead_code!=0){
			$cond_str=" and gos_customer_leadcode='$lead_code' ";
		}else{
			$cond_str='';
		}
		$sql_update3="update gft_sending_sms set gos_receiver_mobileno='$mobileno',gos_sms_status='$status' " .
			" where gos_sms_status !=1 and gos_msg_sent_time between '$before' and '$date' $cond_str ";
		execute_my_query($sql_update3,'common_util.php',true,false,1);
		$sql_update4="update gft_sending_sms set gos_receiver_mobileno='$mobileno',gos_sms_status='1',gos_sent_to_alert=1 ".
				" where gos_msg_sent_time < '$before' $cond_str ";
		execute_my_query($sql_update4,'common_util.php',true,false,1);
		$sql_update2="update gft_sending_sms set gos_receiver_mobileno='$mobileno',gos_sms_content='$content',gos_sms_status='$status' " ;
		if($resend_needed==1)
			$sql_update2.=", gos_sent_to_alert='0' ";
		else
			$sql_update2.=", gos_sent_to_alert=1 ";
		$sql_update2.=" where gos_id='$smsid'";
		execute_my_query($sql_update2,'common_util.php',true,false,1);
		$cond_str="";
	}
}

/**
 * @param string $receipt_id
 * @param string $content
 *
 * @return string
 */
function generatereceiptpdf($receipt_id,$content){
	$folder_name="receipt";
	$f_name='';
	$content=(string)str_replace('\"','"', $content);
	$content=(string)str_replace('<tbody>','', $content);
	$content=(string)str_replace('</tbody>','', $content);
	$html_fname="receipt_$receipt_id.html";
	$t=write_to_file($folder_name,$content,$html_fname,/*$mode=*/null);
	if($t){
		$file_path=realpath("../sales_server_support/receipt/");
		$f_name=(string)str_replace("html","pdf",$html_fname);
		$fr_name=$file_path.'/'.$f_name;
		$filename=$file_path.'/'.$html_fname;
	//	echo "<div class=hide>";
		passthru("htmldoc --quiet --size A4 -t pdf14 --jpeg=100 --left 1cm --right 1cm --top 1cm --bottom 1cm --linkstyle plain --pagemode fullscreen  -f $fr_name --webpage $filename ");
	//	echo "</div>";
		//chmod($fr_name,0777);
	}
	return $f_name;
}


/**
 * @param string[string] $db_document_content_config
 * @param string $category
 * @param string $template_id
 * 
 * @return string
 */
function get_formatted_document_content($db_document_content_config,$category,$template_id){
	$content_format="";
	$query="select GDT_MESSAGE from gft_document_template where GDT_ID='$template_id' ";
	$result=execute_my_query($query);
	$qd=mysqli_fetch_array($result);
	$content_format=$qd['GDT_MESSAGE'];
	if($category!=""){
		if($content_format!=""){
			$sql1="select GDT_NAME,GDT_DESC, GDT_REPLACE_VALUE,GDT_TYPE from gft_document_template_var,gft_document_template_map " .
					" where GDT_ID=GDT_TVAR_ID and GDT_CATEGORY_ID='$category' ".
					//($db_document_content_config!=null ? " and GDT_NAME in ('" . implode("','",array_keys($db_document_content_config)). "')":"").
					" order by GDT_PRIORITY ";
			$rs1=execute_my_query($sql1,'common_util.php',true,false,2);
			while($row1=mysqli_fetch_array($rs1)){
				$replace='';
				if($row1['GDT_TYPE']=='Array' and isset($db_document_content_config[$row1['GDT_NAME']])){
					foreach($db_document_content_config[$row1['GDT_NAME']] as $key=>$value){
						$ppatten=$row1['GDT_REPLACE_VALUE'];
						foreach($value as $vkey=>$vvalue){
							$ppatten=(string)preg_replace('/{{'.$vkey.'}}/i',"$vvalue",$ppatten);
						}
						$replace.=$ppatten;
					}
					$pattern='/{{'.$row1['GDT_NAME'].'}}/i';
					$content_format=preg_replace($pattern,"$replace",$content_format);
				}else if($row1['GDT_TYPE']=='isExist'){
					if(isset($db_document_content_config[$row1['GDT_DESC']]) and $db_document_content_config[$row1['GDT_DESC']]!=null and $db_document_content_config[$row1['GDT_DESC']]!=0){
						$GDT_REPLACE_VALUE=$row1['GDT_REPLACE_VALUE'];
						$pattern='/{{'.$row1['GDT_NAME'].'}}/i';
	        			$content_format=preg_replace($pattern,"$GDT_REPLACE_VALUE",$content_format);
					}else{
						$pattern='/{{'.$row1['GDT_NAME'].'}}/i';
						$content_format=preg_replace($pattern,'',$content_format);
					}
				}else if(isset($db_document_content_config["{$row1[0]}"])){
					$replace=$db_document_content_config["{$row1[0]}"];
					$pattern='/{{'.$row1['GDT_NAME'].'}}/i';
					$content_format=preg_replace($pattern,"$replace",$content_format);
				}
			}
		}
	}
	$content_format==preg_replace('/\<td\> \<\/td\>/i','\<td\>&nbsp;\<\/td\>',$content_format);
	return $content_format;
}

/**
 * @param string[string][int] $db_sms_content_config
 * @param int $category
 * @param int $template_id
 *
 * @return string[string]
 */
function get_formatted_mail_content($db_sms_content_config,$category,$template_id){
	$content_format="";$data_replace=array();
	$body_content	=	isset($_REQUEST['body_content'])?$_REQUEST['body_content']:'';
	$is_inline_attachement	=	isset($_REQUEST['is_inline_attachement'])?$_REQUEST['is_inline_attachement']:0;
	$query="select GMT_FROM_MAILID,GMT_FROM_NAME,GMT_MESSAGE,GMT_CONTENT_IS_HTML,GMT_SUBJECT,GMT_ATTACHMENT,GMT_EMB_ATTACHMENT,GMT_REPLY_TO," .
			"GMT_CCTO_MANAGER,GMT_CCTO_EMP,GMT_MAILTO,GMT_TO_OTHERS,GMT_CC_OTHERS,GMT_CATEGORY " .
			" from gft_mail_template where GMT_ID='$template_id' ";
	$result=execute_my_query($query);
	if($qd=mysqli_fetch_array($result)){
		$content_format=$qd['GMT_MESSAGE'];
		$emb_attach = $qd['GMT_EMB_ATTACHMENT'];
		if($is_inline_attachement==0){
			if($body_content!=''){
				$content_format	=	$body_content;
			}
		}
//		if($emb_attach != ''){
			$rows_common_cons = execute_my_query("select GSC_NAME, GSC_VALUE from gft_samee_const where GSC_NAME IN('GFT_CUST_CARE_NO','GFT_CUST_CARE_NO1','GFT_CUST_CARE_EMAILID','Total_Customers','Total_Countries','Total_Year_Specialization','Total_Business_Supported','Total_Field_Support','Average_Customer_Rating')");
			while ($row_common_cons=mysqli_fetch_array($rows_common_cons)){
				$param_key = $row_common_cons['GSC_NAME'];
				$db_sms_content_config["$param_key"] = array($row_common_cons['GSC_VALUE']);
			}
//		}
		$category=(int)$qd['GMT_CATEGORY'];
		$subject_format=$qd['GMT_SUBJECT'];
		if($content_format!="" or $qd['GMT_EMB_ATTACHMENT']!=null){
				$sql1="select GMT_NAME from gft_mail_template_var,gft_mail_template_map " .
						" where GMT_ID=GMT_TVAR_ID and GMT_CATEGORY_ID='$category' ".
						($db_sms_content_config!=null ? " and GMT_NAME in ('" . implode("','",array_keys($db_sms_content_config)). "')":"");
				$rs1=execute_my_query($sql1,'common_util.php',true,false,2);
				while($row1=mysqli_fetch_array($rs1)){
					if(isset($db_sms_content_config["{$row1[0]}"][0])){
						$replace=$db_sms_content_config["{$row1[0]}"][0];
						$replace = str_replace("$", "\\\$", $replace); //preg_replace treats $ specially. so to escape the $ it is replaced
				 		$pattern='/{{'.$row1[0].'}}/i';
				 		if($content_format!=''){
			        		$content_format=preg_replace($pattern,"$replace",$content_format);
			        		$subject_format=preg_replace($pattern,"$replace",$subject_format);
				 		}else if($qd['GMT_EMB_ATTACHMENT']!=null){ 
			        		$data_replace["{$row1[0]}"]=$db_sms_content_config["{$row1[0]}"][0];
			        		$subject_format=preg_replace($pattern,"$replace",$subject_format);
						}
					}
				}
		}
		if($content_format!=''){
			$content_format=preg_replace('/{{Total_Customers}}/i',$db_sms_content_config['Total_Customers'][0],$content_format);
		}else if($qd['GMT_EMB_ATTACHMENT']!=null){
			$data_replace["Total_Customers"]=$db_sms_content_config["Total_Customers"][0];
			$content_format=($data_replace!=null?json_encode($data_replace):'');
		}
	    
               
		
		$return_msg=/*. (string[string]) .*/ array();
		$return_msg['from_mailid']=$qd['GMT_FROM_MAILID'];
		$return_msg['from_name']=$qd['GMT_FROM_NAME'];
		$return_msg['content']=$content_format;
		$return_msg['content_type']=($qd['GMT_CONTENT_IS_HTML']=='Y'?true:false);
		if(isset($_REQUEST['content_mime'])){
			$return_msg['content_type']= ($_REQUEST['content_mime']=='Y')?true:false;
		}else{
			$return_msg['content_type']=($qd['GMT_CONTENT_IS_HTML']=='Y'?true:false);
		}		
		$formated_content = "";
		if($qd['GMT_EMB_ATTACHMENT']!='' && $qd['GMT_EMB_ATTACHMENT']!=null){
		    $attach_Embedded_file=json_decode($qd['GMT_EMB_ATTACHMENT'],true);
		    if(isset($attach_Embedded_file[0]['html']) and $attach_Embedded_file[0]['html']!=''){
		        /* if call from sales_server/license_module ... i need this setup --Start */
		        /* if call from sales_server/license_module ... i need this setup --END */
		        $dir_var=dirname($_SERVER['PHP_SELF']);
		        $directory=substr( $dir_var,strrpos($dir_var,'/')+1,strlen($dir_var)) ;
		        /* if we configured the like store.gofrugal.com or sam.gofrugal.com directory is empty */
		        $base_dir=(($directory!='sales_server' and $directory!='store_server' and $directory!='store' and $directory!='' )?'../':'');
		        $fname=$base_dir.$attach_Embedded_file[0]['html'];
		        if (file_exists($fname)){
		            $formated_content=file_get_contents($base_dir.$attach_Embedded_file[0]['html']);
		        }else{
		            //TODO: Need to handle this type of error .... in the UI
		            $stack_out = getStackTraceString();
		            error_log("File: $fname not exists. ". $stack_out);
		            $formated_content='';
		        }
		        
		        if(count($attach_Embedded_file)>0){
		            for($i=0;$i<count($attach_Embedded_file); $i++){
		                if(isset($attach_Embedded_file[$i]['inline'])){
		                    $formated_content=preg_replace('/'.$attach_Embedded_file[$i]['file_name'].'/i','cid:'.$attach_Embedded_file[$i]['inline'] ,$formated_content);
		                }
		            }
		        }
		        if($db_sms_content_config!=null){
		            $data_t=json_decode($content_format);
		            foreach($data_t as $key1 =>$value1){
		                    $formated_content=preg_replace('/{{'.(string)$key1.'}}/i',$value1,$formated_content);
		         }
		       }
		        
		    }
		}
		$return_msg['formated_content']=$formated_content;
		$return_msg['Subject']=$subject_format;
		$return_msg['Attachment']=($qd['GMT_ATTACHMENT']!=''?explode(',',$qd['GMT_ATTACHMENT']):null);
		//$return_msg['EMBAttachment']=($qd['GMT_EMB_ATTACHMENT']!=''?json_decode($qd['GMT_EMB_ATTACHMENT'],true):null);
		$return_msg['EMBAttachment']=($qd['GMT_EMB_ATTACHMENT']!=''?$qd['GMT_EMB_ATTACHMENT']:null);
		$return_msg['cc_to_manager']=$qd['GMT_CCTO_MANAGER'];
		$return_msg['cc_to_emp']=$qd['GMT_CCTO_EMP'];
		$return_msg['reply_to']=$qd['GMT_REPLY_TO'];
		$return_msg['mail_to']=$qd['GMT_MAILTO'];
		$return_msg['to_other_email']=$qd['GMT_TO_OTHERS'];
		$return_msg['cc_other_email']=$qd['GMT_CC_OTHERS'];
		$return_msg['category']=$qd['GMT_CATEGORY'];
		return $return_msg;
	}
	return null;
}
/**
 * @param int $emp_id
 * @param string $title
 * @param string $message
 * @param int $noti_type
 * @param int $noti_lead_code
 * @param int $noti_ref_id
 * @param int $immediate_send
 * @param int $for_app
 * @param int $cust_user_id
 * @param string $other_reference_dtl
 *
 * @return void
 */
function notificaton_entry($emp_id,$title,$message,$noti_type=0,$noti_lead_code=0,$noti_ref_id=0,$immediate_send=0,$for_app=1,$cust_user_id=0,$other_reference_dtl=""){
	$sel_que=" select  EMP_ID from gft_emp_auth_key inner join gft_emp_master on(EMP_ID=gem_emp_id and GEM_STATUS='A') ".
			" where GEK_GCM_REGISTER_ID!='' and GEK_DEVICE_STATUS=1 and EMP_ID='$emp_id' and GEK_STATUS='A' ";
	if($for_app==2){
		$sel_que=" select GCL_USER_ID from gft_customer_access_dtl join gft_customer_login_master on (GCL_USER_ID=GCA_USER_ID) ".
				" where GCA_ACCESS_LEAD='$noti_lead_code' and GCL_GCM_REGISTER_ID!='' and GCL_DEVICE_STATUS=1 and GCL_USER_STATUS=1 and GCL_EMP_ID=0 ";
	}
	$res_device_status = execute_my_query($sel_que);
	if(mysqli_num_rows($res_device_status)==0){
		return;
	}
	$title	=	mysqli_real_escape_string_wrapper($title);
	$message=	mysqli_real_escape_string_wrapper($message);
	if($for_app==2){
		$values_str = '';
		$put_comma = "";
		while( ($row1 = mysqli_fetch_array($res_device_status)) && ($cust_user_id==0) ){
			$user_id = $row1['GCL_USER_ID'];
			$values_str .= " $put_comma ($for_app,'$user_id',$noti_type,'$noti_ref_id',$noti_lead_code,'$title','$message',1,now(),'$other_reference_dtl') ";
			$put_comma = ",";
		}
		if($cust_user_id>0){
			$values_str .= " $put_comma ($for_app,'$cust_user_id',$noti_type,'$noti_ref_id',$noti_lead_code,'$title','$message',1,now(),'$other_reference_dtl') ";
		}
	}else{
		$values_str = "($for_app,'$emp_id',$noti_type,'$noti_ref_id',$noti_lead_code,'$title','$message',1,now(),'$other_reference_dtl')";
	}
	$sql_qry=	" INSERT INTO gft_gcm_push_notification(GPN_FOR_APP,GPN_EMP_ID,GPN_NOTIFICATION_TYPE, GPN_NOTI_REFERENCE_ID, GPN_LEAD_CODE,".
			" GPN_TITLE, GPN_MESSAGE,GPN_STATUS, GPN_CREATED_DATE_TIME,GPN_OTHER_REFERENCE_DTL) ".
			" VALUES $values_str ";
	$r1 = execute_my_query($sql_qry);
	if($r1){
		$ns_id = mysqli_insert_id_wrapper();
		if($immediate_send==1){
			$id = $emp_id;
			if($for_app==2){
				$id = $noti_lead_code;
			}
			system("sh ".__DIR__."/shscript/send_push_notification_now.sh $id $for_app $ns_id >/dev/null 2>&1 &");
		}
	}
}
/**
 * @param string[string][int] $noti_content_config
 * @param int $category
 * @param int $template_id
 * @param int $app_type 
 * @param string $send_to
 * @param string $lead_code
 * @param string $ref_id
 * @param int $cust_user_id
 * @param string $other_reference_dtl
 * @param boolean $send_wns_noti
 * @param string $wns_additional_info
 * @param string $user_mobile_no
 * @param int $immediate_send
 *
 * @return void
 */
function send_formatted_notification_content($noti_content_config, $category ,$template_id,$app_type,$send_to,$lead_code='0',$ref_id='0',$cust_user_id=0,$other_reference_dtl="",$send_wns_noti=false,$wns_additional_info="",$user_mobile_no="",$immediate_send=1){
	$query= " select GPN_TITLE,GPN_DESC,GPN_CATEGORY,GPN_PUSH_CATEGORY,GPN_ACTION_TYPE,GPN_TEAM_ID ".
			" from gft_push_notification_template WHERE GPN_ID='$template_id' ";
	$result=execute_my_query($query);
	if($qd=mysqli_fetch_array($result)){
		$content_format=$qd['GPN_DESC'];	
		$category		= (int)$qd['GPN_CATEGORY'];
		$subject_format	= $qd['GPN_TITLE'];
		$noti_type		= $qd['GPN_PUSH_CATEGORY'];
		$action_type	= $qd['GPN_ACTION_TYPE'];
		$team_id 		= (int)$qd['GPN_TEAM_ID'];
		$emp_to_send_arr= array($send_to);
		if($team_id > 0){
			$team_emp_list = get_emp_master(null,'A',null,true,false,$team_id);
			foreach ($team_emp_list as $ke => $va){
				array_push($emp_to_send_arr, $va[0]);
			}
		}
		
		if($content_format!=""){
			$sql1="select GMT_NAME from gft_mail_template_var,gft_mail_template_map " .
					" where GMT_ID=GMT_TVAR_ID and GMT_CATEGORY_ID='$category' ".
					($noti_content_config!=null ? " and GMT_NAME in ('" . implode("','",array_keys($noti_content_config)). "')":"");
			$rs1=execute_my_query($sql1,'common_util.php',true,false,2);
			while($row1=mysqli_fetch_array($rs1)){
				if(isset($noti_content_config["{$row1[0]}"][0])){
					$replace=$noti_content_config["{$row1[0]}"][0];
					$replace = str_replace("$", "\\\$", $replace); //preg_replace treats $ specially. so to escape the $ it is replaced
					$pattern='/{{'.$row1[0].'}}/i';
					if($content_format!=''){
						$content_format=preg_replace($pattern,"$replace",$content_format);
						$subject_format=preg_replace($pattern,"$replace",$subject_format);
					}
				}
			}
		}
		if($content_format!='' && $subject_format!=''){
			$content_format	=	mysqli_real_escape_string_wrapper($content_format);
			$subject_format	=	mysqli_real_escape_string_wrapper($subject_format);
			if($app_type==1){//myDelight
				if($other_reference_dtl==""){
					$other_reference_dtl = json_encode(array("action_type"=>$action_type));
				}
				foreach ($emp_to_send_arr as $emp_to){
					if((int)$emp_to > 0){
						notificaton_entry($emp_to, $subject_format, $content_format,$noti_type,$lead_code,$ref_id,$immediate_send,$app_type,0,$other_reference_dtl);
					}
				}
			}else if($app_type==2){//myGofrugal
				if( ($cust_user_id==0) && ($user_mobile_no!="") ){
					$contact_condition = getContactDtlWhereCondition("GCL_USERNAME", $user_mobile_no);
					$que1 = " select GCL_USER_ID,GCA_ACCESS_LEAD from gft_customer_login_master ".
							" join gft_customer_access_dtl on (GCA_USER_ID=GCL_USER_ID and GCA_ACCESS_STATUS=1) ".
							" where GCL_USER_STATUS=1 and ($contact_condition) ";
					$res1 = execute_my_query($que1);
					if($data1 = mysqli_fetch_array($res1)){
						$send_to 		= $data1['GCA_ACCESS_LEAD'];
						$cust_user_id 	= $data1['GCL_USER_ID'];
					}
				}
				notificaton_entry(9999, $subject_format, $content_format,$noti_type,$send_to,$ref_id,1,$app_type,$cust_user_id,$other_reference_dtl);
				if($send_wns_noti){
					if($wns_additional_info!=""){
						$content_format = $content_format."<br><br>$content_format";
					}
					notify_pos_product($send_to, '', 'promotion', $content_format,'','',false,'','','','',"$subject_format");
				}
			}
		}
	}
}
/**
 * @param string $partner_id
 * @param string $lead_code
 * @param string $cust_name
 * @param string $emp_name
 * @param string $total_amount
 * @param string $type
 *
 * @return void
 */
function send_notification_to_partner($partner_id,$lead_code,$cust_name,$emp_name,$total_amount,$type=""){
	$partner_name		= get_emp_name($partner_id);
	$noti_content_config					= array();
	$noti_content_config['Partner_Name']	= array($partner_name);
	$noti_content_config['Customer_Name']	= array($cust_name);
	$noti_content_config['Customer_Id']		= array($lead_code);
	$noti_content_config['Employee_Name']	= array($emp_name);
	$noti_content_config['Order_Amount']	= array($total_amount);
	if($type=='proforma'){
		send_formatted_notification_content($noti_content_config,89,41,1,$partner_id);
	}else if($type=='collection'){
		send_formatted_notification_content($noti_content_config,89,42,1,$partner_id);
	}else if($type=='collection_entry'){
		send_formatted_notification_content($noti_content_config,89,43,1,$partner_id);
	}else if($type=='order_entry'){
		send_formatted_notification_content($noti_content_config,67,44,1,$partner_id);
	}else if($type=='store_order'){
		send_formatted_notification_content($noti_content_config,67,45,1,$partner_id);
	}else{
		send_formatted_notification_content($noti_content_config,89,40,1,$partner_id);
	}
	
}
/**
 * @param string $emp_id
 * 
 * @return string
 */
function get_mobileno($emp_id){
	$sql="SELECT  GEM_EMP_ID , GEM_MOBILE  FROM gft_emp_master where GEM_EMP_ID='$emp_id' and GEM_STATUS='A' limit 1";
	$rs=execute_my_query($sql,'common_util.php',true,false,2);
	if($row=mysqli_fetch_array($rs)){
		return $row['GEM_MOBILE'];
	}
	return '';
}

/**
 * @param string $lead_code
 * @param string $order_no
 * 
 * @return string[string]
 */
function customerContactDetail_Mail($lead_code,$order_no){
	$sql_con	=	"";
	if($order_no!=''){ $sql_con	=	" AND GOD_ORDER_NO='$order_no'"; }
	$sql="SELECT GLH_LEAD_CODE,GLH_CUST_NAME ,GLH_AREA_NAME , GLE_DELIVERY_TYPE, " .
			" GLH_CUST_CITY, GLH_CUST_STREETADDR2, god_emp_id,GOD_INCHARGE_EMP_ID,GOD_PDCM_EMP_ID,GLH_LFD_EMP_ID, GLH_VERTICAL_CODE,GTM_VERTICAL_NAME,GLH_FIELD_INCHARGE, GLH_L1_INCHARGE, " .
			" group_concat(DISTINCT if(gcc_contact_type=1,GCC_CONTACT_NO,null)) MOBILE," .
			" group_concat(DISTINCT if(gcc_contact_type=1 and GCG_GROUP_ID=1,GCC_CONTACT_NO,null)) TS_MOBILE, ".
			" group_concat(DISTINCT if(gcc_contact_type=1 and GCG_GROUP_ID=1,GCC_CONTACT_NAME,null)) TS_CONTACTNAME, ".
			" group_concat(DISTINCT if(gcc_contact_type=2,GCC_CONTACT_NO,null)) BUSSNO, " .
			" group_concat(DISTINCT if(gcc_contact_type=4,GCC_CONTACT_NO,null)) EMAIL," .
			" group_concat(DISTINCT if(gcc_contact_type=4,concat(GCC_CONTACT_NAME,'(',gcd_name,')'),null)) CONTACTNAME," .
			" group_concat(DISTINCT if(gcc_contact_type=1,concat(GCC_CONTACT_NAME,'(',gcd_name,')'),null)) MCONTACTNAME," .
			" group_concat(DISTINCT if(gcc_contact_type=7,trim(GCC_CONTACT_NO),null)) GTALK, ".
			" group_concat(DISTINCT if(gcc_contact_type=8,trim(GCC_CONTACT_NO),null)) SKYPE ".
			" FROM (gft_lead_hdr,gft_order_hdr,gft_customer_contact_dtl,gft_contact_designation_master) " .
			" left join gft_vertical_master gtm on (GTM_VERTICAL_CODE=GLH_VERTICAL_CODE)" .
			" left join gft_contact_dtl_group_map on(GLH_LEAD_CODE=GCG_LEAD_CODE) ".
			" left join gft_lead_hdr_ext on (GLH_LEAD_CODE=GLE_LEAD_CODE) ".
			" where GCC_LEAD_CODE=GLH_LEAD_CODE  " . //and GOD_LEAD_CODE = GLH_LEAD_CODE 
			" and gcd_code = gcc_designation and gcd_status='A' and GLH_LEAD_CODE ='$lead_code' $sql_con " .
			//(isset($_REQUEST['order_no'])?" AND GOD_ORDER_NO = '".$_REQUEST['order_no']."'":"").
			//Commented the order_no check because from support_details.php su bmit, the order_no value contains the 
			//value with fullfilment number.
			//For example: 123344444-001
			" group by GLH_LEAD_CODE ";
	$rs_query=execute_my_query($sql,'common_util.php',true,false,2);
	$tmp=/*. (string[string]) .*/ array();
	$count=0;
	while($row=mysqli_fetch_array($rs_query)){
		$count++;
		$tmp['cust_name']=$row['GLH_CUST_NAME'];
		$tmp['area_name']=$row['GLH_AREA_NAME'];
		$tmp['city']=$row['GLH_CUST_CITY'];
		$tmp['LOCATION']=$row['GLH_CUST_STREETADDR2'];
		$tmp['mobile_no']=$row['MOBILE'];
		$tmp['ts_mobile_no']=$row['TS_MOBILE'];
		$tmp['ts_contact_name']=$row['TS_CONTACTNAME'];
		$tmp['contact_name']=$row['CONTACTNAME'];
		$tmp['mcontact_name']=$row['MCONTACTNAME'];
		$tmp['bussno']=$row['BUSSNO'];
		$tmp['EMAIL']=$row['EMAIL'];
		$tmp['GTALK']=$row['GTALK'];
		$tmp['SKYPE']=$row['SKYPE'];
		$tmp['Order_By_Whom']=$row['GOD_INCHARGE_EMP_ID'];
		$tmp['LFD_EMP_ID']=$row['GLH_LFD_EMP_ID'];
		$tmp['Reg_incharge']=$row['GLH_L1_INCHARGE'];
		$tmp['Field_incharge']=$row['GLH_FIELD_INCHARGE'];
		$tmp['vertical']=$row['GLH_VERTICAL_CODE'];
		$tmp['VERTICAL_NAME']=$row['GTM_VERTICAL_NAME'];
		$tmp['PDCM_EMP_ID']=$row['GOD_PDCM_EMP_ID'];
		$tmp['delivery_type']=$row['GLE_DELIVERY_TYPE'];
	}

	if ($count == 0){
		error_log("common_util.php : customerContactDetail_Mail() returns empty row ....for query: " . $sql);
	}
  return $tmp;
}

/**
 * @param string $lead_code
 *
 * @return string
 */
function return_latest_kyc_details($lead_code)	{	
/* KYC Questions and ANS to PC -Start*/		
$audit_questions=<<<END
	select GAQ_QUESTION_TYPE, GAD_AUDIT_ANS 
	from gft_audit_dtl ad  
   	join gft_audit_question_master on(GAD_AUDIT_QID=GAQ_QUESTION_ID) 
	where gad_audit_id=(select max(gah_audit_id) from gft_audit_hdr hd where  GAH_LEAD_CODE=$lead_code and gah_audit_type in (13,20) );
END;
	$result_audit_ans=execute_my_query($audit_questions);
	$kyc='';
	if(mysqli_num_rows($result_audit_ans)>0){
		$kyc="<table border=1><tr><td>Questions</td><td>Answers</td></tr>";
		while($data_audit_ans=mysqli_fetch_array($result_audit_ans)){
			$kyc.= "<tr><td>".$data_audit_ans['GAQ_QUESTION_TYPE']."</td><td>".$data_audit_ans['GAD_AUDIT_ANS']."</td></tr>";
		}
		$kyc.="</table></table>";
	}
	return $kyc;	
/* KYC Questions and ANS to PC -End */			
}

/**
 * @param string $order_no
 *
 * @return string
 */
function get_system_requirement_link($order_no){
	$sys_requirement	=	'';
	$rpos6=true;$rpos7=true;
	if($order_no==''){
		return $sys_requirement;
	}
	$res	=	execute_my_query("select concat(GOP_PRODUCT_CODE,substring(GOP_PRODUCT_SKEW,1,4)) as codeskew from gft_order_product_dtl where gop_order_no='$order_no'");
	while($row=mysqli_fetch_array($res)){
		if($row['codeskew']=='50006.5' and $rpos6==true){
			$sys_requirement	.=	($sys_requirement!=''?'<br><br>':'').get_samee_const("System_Requirement_RPOS65");
			$rpos6=false;
		}else if($row['codeskew']=='50007.0' and $rpos7==true){
			$sys_requirement	.=	($sys_requirement!=''?'<br><br>':'').get_samee_const("System_Requirement_RPOS70");
			$rpos7=false;
		}
	}
	return $sys_requirement;
}

/**
 * @param string $lead_code
 * @param string $uid
 * @param string $audit_id
 * @param string $complaint_id
 * @param string $order_no
 * @param string[string] $qidans
 * @param string[string] $comments_arr
 * 
 * @return void 
 */
function impl_send_sms_mail_hold_info($lead_code,$uid,$audit_id=null,$complaint_id=null,$order_no='',$qidans=null,$comments_arr=null){
	global $non_employee_group;
	$user_dtl=get_emp_master($uid,'A',null,false);
	$cust_dtl=customerContactDetail_Mail($lead_code,$order_no);
	$user_master=get_emp_master($uid,'A',null,false);
	$sales_emp_name	=9999;
	$noti_content_config	= array();
	if(is_authorized_group_list($cust_dtl['PDCM_EMP_ID'], array (54,36))){$sales_emp_name=$cust_dtl['PDCM_EMP_ID']; }else{$sales_emp_name= $cust_dtl['LFD_EMP_ID']; } 
	$jemp_master=get_emp_master((string)$cust_dtl['Field_incharge'],'',null,false,false);
	$com_jemp_master=get_emp_master($sales_emp_name,'',null,false,false);
	$RC_EMP=get_emp_master((string)$cust_dtl['Reg_incharge'],'',null,false,false);
	$audit_type='';
	$emp_company_name	= '';
	$emp_partner_type	= '';
	$push_noti_cat		= '';
	$push_template_id	= '';
	$help_us_help_you	=	get_samee_const("Help_Us_Help_You");
	$sys_requirement	=	get_system_requirement_link($order_no);
	$coupon_expiry_info	=	get_coupon_expiry_information($order_no,$lead_code);
	$other_cc			=	array($sales_emp_name);
	
	$customer_comments	=	isset($comments_arr['gah_cust_comments'])?$comments_arr['gah_cust_comments']:'';
	$gah_my_comments	=	isset($comments_arr['gah_my_comments'])?$comments_arr['gah_my_comments']:'';
	
	if(isPartnerEmployee((int)$cust_dtl['Field_incharge'])){
		$emp_company_name	=	getEmployeeCompanyName((int)$cust_dtl['Field_incharge']);
		$emp_partner_type	=	getEmployeeCompanyType((int)$cust_dtl['Field_incharge']);
		//$other_cc	=	array_merge($other_cc,getPartnerIdBusinessManagerId($order_no));
	}
	if(isPartnerEmployee((int)$sales_emp_name)){
		//$other_cc	=	array_merge($other_cc,getPartnerIdBusinessManagerId($order_no));
	}
	if(isPartnerEmployee((int)$sales_emp_name)){
		//$other_cc	=	array_merge($other_cc,getPartnerIdBusinessManagerId($order_no));
	}
	if($audit_id!=null){
		$result=execute_my_query("SELECT GAT_AUDIT_ID, GAH_ORDER_AUDIT_STATUS, GAH_PENDING_IMP, GAH_REQUIRED_TRAINING_DATE,GAH_CUSTOMER_COMMENTS,GAH_MY_COMMENTS, " .
			" concat('<table><tr><td>Questions</td><td>Answers</td></tr>', GROUP_CONCAT(concat('<tr><td>',GAQ_QUESTION_TYPE,'</td><td>',GAD_AUDIT_ANS,'</td></tr>') separator ''),'</table>') quest_ans " .
			" FROM gft_audit_hdr " .
			" left JOIN gft_audit_type_master ON(GAH_AUDIT_TYPE=GAT_AUDIT_ID)" .
			" left join gft_audit_dtl on (GAH_AUDIT_ID=GAD_AUDIT_ID)" .
			" left JOIN gft_audit_question_master on(GAD_AUDIT_QID=GAQ_QUESTION_ID)" .
			" WHERE GAH_AUDIT_ID=$audit_id AND GAH_LEAD_CODE=$lead_code group by GAH_AUDIT_ID ");
		if($data=mysqli_fetch_array($result)){
			$audit_type=$data['GAT_AUDIT_ID'];
			$db_content_config=array('user_name'=>array($user_master[0][1]),
				'Customer_Name'=>array($cust_dtl['cust_name']),
				'Customer_Id'=>array($lead_code),
				'Sales_Manager_Name'=>array($com_jemp_master[0][1]),
				'PC_Name'=>array($jemp_master[0][1].$emp_company_name.$emp_partner_type),
				'PC_Role'=>array(($jemp_master[0][7]!=''?$jemp_master[0][7]:'&nbsp;')),
				'PC_Mobile'=>array($jemp_master[0][3]),
				'audit_qans'=>array($data['quest_ans']),
				'Help_Us_Help_You'=>array($help_us_help_you),
				'System_Requirement'=>array($sys_requirement),
				'Coupon_Details'=>array($coupon_expiry_info),
				'Cust_Comments'=>array((isset($_POST['gah_cust_comments'])?(string)$_POST['gah_cust_comments']:(isset($_POST['cust_feedback'])?(string)$_POST['cust_feedback']:$customer_comments))),
				'My_Comments'=>array((isset($_POST['gah_my_comments'])?(string)$_POST['gah_my_comments']:(isset($_POST['activity_note'])?(string)$_POST['activity_note']:$gah_my_comments)))
			);			
			$reason_for_not_accept	=	'"Customer Comment: "'.$data['GAH_CUSTOMER_COMMENTS'].', PC Comment: '.$data['GAH_MY_COMMENTS'];
			$noti_content_config['CM_Name']			= array($com_jemp_master[0][1]);
			$noti_content_config['Customer_Name']	= array($cust_dtl['cust_name']);
			$noti_content_config['Customer_Id']		= array($lead_code);
			$noti_content_config['SPOC_NO']			= array(($cust_dtl['ts_mobile_no']!=""?$cust_dtl['ts_mobile_no']:"-"));
			$noti_content_config['Mail_Content']	= array($reason_for_not_accept);
			if($data['GAH_PENDING_IMP']!=''){$db_content_config['implementation_status']=array($data['GAH_PENDING_IMP']);}else {$db_content_config['implementation_status']=array('HOLD');} 
			if($data['GAH_ORDER_AUDIT_STATUS']!='')$db_content_config['order_audit_status']=array($data['GAH_ORDER_AUDIT_STATUS']);
			if($data['GAH_REQUIRED_TRAINING_DATE']!='')$db_content_config['training_target_date']=array($data['GAH_REQUIRED_TRAINING_DATE']);
		}
	}else if($complaint_id!=null){
		$result=execute_my_query("select concat('<table><tr><td>Milestone</td><td>Task Name</td><td>Remarks</td><td>Current Status</td></tr>',group_concat(concat('<tr><td>',GIM_MS_NAME,'</td><td>',GIT_TASK_NAME,'</td><td>',GITC_REMARKS,'</td><td>',GTS_STATUS_NAME,'</td></tr>')separator ''),'</table>') complaient_status " .
				"from gft_customer_support_hdr csh " .
				"join gft_cust_imp_task_current_status_dtl on (gitc_complaint_id = csh.gch_complaint_id) " .
				"join gft_cust_imp_ms_current_status_dtl imt on (csh.gch_complaint_id=imt.GIMC_COMPLAINT_ID) " .
				"join gft_impl_task_master on (GIT_TASK_ID=GITC_TASK_ID) " .
				"join gft_ms_task_status on (GTS_STATUS_CODE=GITC_STATUS) " .
				"join gft_impl_mailstone_master on (gim_ms_id=gimc_ms_id) " .
				" WHERE ".(is_array($complaint_id) ? " csh.gch_complaint_id in (" . implode(', ',$complaint_id).") ": " csh.gch_complaint_id=$complaint_id ").
				" AND GCH_LEAD_CODE=$lead_code and gts_status_code = -1 ");		
		if($data=mysqli_fetch_array($result)){
			$gch_restore_time=''; //$data['GCH_RESTORE_TIME'];
			$db_content_config=array('user_name'=>array($user_master[0][1]),
				'Customer_Id'=>array($lead_code),
				'Sales_Manager_Name'=>array($com_jemp_master[0][1]),
				'PC_Name'=>array($jemp_master[0][1].$emp_company_name.$emp_partner_type),
				'Customer_Name'=>array($cust_dtl['cust_name']),
				'PC_Role'=>array(($jemp_master[0][7]!=''?$jemp_master[0][7]:'&nbsp;')),
				'PC_Mobile'=>array($jemp_master[0][3]),
				'audit_qans'=>array($data['complaient_status']),
				'training_target_date'=>array($gch_restore_time),
				'implementation_status'=>array($complaint_id .'is HOLD'),
				'order_audit_status'=>array('Yes'),
				'Employee_Name'=>array($user_dtl[0][2]),
				'Help_Us_Help_You'=>array($help_us_help_you),
				'System_Requirement'=>array($sys_requirement),
				'Coupon_Details'=>array($coupon_expiry_info),
				'Cust_Comments'=>array((isset($_POST['gah_cust_comments'])?(string)$_POST['gah_cust_comments']:(isset($_POST['cust_feedback'])?(string)$_POST['cust_feedback']:$customer_comments))),
				'My_Comments'=>array((isset($_POST['gah_my_comments'])?(string)$_POST['gah_my_comments']:(isset($_POST['activity_note'])?(string)$_POST['activity_note']:$gah_my_comments)))
			);
		}
	}
	if(isset($db_content_config)){
		$result=execute_my_query("select concat('<table><tr><td>Milestone</td><td>Restore Time</td><td>Current Status</td></tr>',group_concat(concat('<tr><td>',csh.gch_complaint_id,'</td><td>',GCH_RESTORE_TIME,'</td><td>',GTS_STATUS_NAME,'</td></tr>') separator ''),'</table>') training_assurance   " .
				" from gft_customer_support_hdr csh " .
				" join gft_cust_imp_ms_current_status_dtl imt on (csh.gch_complaint_id=imt.GIMC_COMPLAINT_ID) " .
 				" join gft_ms_task_status on (GIMC_STATUS=GTS_STATUS_CODE )" .
				" WHERE GCH_LEAD_CODE=$lead_code group by GCH_LEAD_CODE ");
		if($data=mysqli_fetch_array($result)){
			$db_content_config['training_assurance']=array($data['training_assurance']);
		}
	}
	if(isset($db_content_config)){
		switch($audit_type){
			case '19':
				$sms_template = 116;
				$category=55;$mail_template_id=87;
				break;
			case '18':
				$sms_template = 116;
				$category=55;$mail_template_id=86;
				break;
			case '17': /* commercial Manager Feedback */
				$sms_template = 131;				
				if(is_authorized_group_list($uid,$non_employee_group)){
					$category=55;$mail_template_id=119;
				}
				$category=55;$mail_template_id=85;
				break;
			case '38': /* commercial Manager Feedback */
				$sms_template = 131;
				if(is_authorized_group_list($uid,$non_employee_group)){
					$category=55;$mail_template_id=119;
				}
				$category=55;$mail_template_id=85;
				break;
			case '15':
				$sms_template = 116;
				$category=55;$mail_template_id=84;
				$push_noti_cat=55;$push_template_id=6;
				break;
			case '21':
				$sms_template = 115;
				$category=55;$mail_template_id=114;	
				$push_noti_cat=55;$push_template_id=4;
				break;	
			default:
				$sms_template = 117;
				$category=55;$mail_template_id=115;
				break;
		}
		if($mail_template_id == '115' || $mail_template_id == '84' || $mail_template_id == '114'){
			$incharge_email=$com_jemp_master[0][4].','.$jemp_master[0][4].',training@gofrugal.com';
			$other_cc = array_merge($other_cc, get_email_addr_reportingmaster($cust_dtl['Field_incharge'],true,explode(",", EMP_IDS_TO_SKIP_REPORTING_MAIL)));
		}else{
			$incharge_email='training@gofrugal.com';
		}
		send_formatted_mail_content($db_content_config,$category,$mail_template_id,$employee_ids=null,$customer_ids=null,$tomail_ids=explode(',',$incharge_email),$other_cc);
		if($push_noti_cat!='' && $push_template_id!=''){
			send_formatted_notification_content($noti_content_config,$push_noti_cat,$push_template_id,1,$sales_emp_name);
		}
		$prob_sum_arr = /*. (string[int]) .*/ (isset($_REQUEST['prob_sum'])?$_REQUEST['prob_sum']:array());
		$prob_sum_val = '';
		if (isset($prob_sum_arr[0])){
			$prob_sum_val = $prob_sum_arr[0];
		}

		$db_sms_content_config=array('user_name'=>array($user_dtl[0][1]."[".$user_dtl[0][3]."]"));
		$db_sms_content_config=array('user_name'=>array($user_dtl[0][1]."[".$user_dtl[0][3]."]"),'lead_code'=>array($lead_code),
		'Customer_Name'=>array($cust_dtl['cust_name']),
		'Customer_Id'=>array($lead_code),
		'PC_Name'=>array($jemp_master[0][1]),
		'Sales_Manager_Name'=>array($com_jemp_master[0][1]),
		'Milestone_Name'=>array($prob_sum_val));
		$sms_content=htmlentities (get_formatted_content($db_sms_content_config,$sms_template));
		sending_sms_to($sms_content,$sms_template,array($cust_dtl['Field_incharge'],$sales_emp_name),true,true,null,$uid);
	}
}

/**
 * @param string $lead_code
 * @param int $designation_code 
 * @param string[int] $multiple_desig_arr 
 * @param boolean $from_app
 * @return string[string]
 */
function customerContactDetail($lead_code,$designation_code=0,$from_app=false,$multiple_desig_arr=null){//cust name,area,city,bussph
	$desig_cond = '';
	if($designation_code!=0) {
		$desig_cond = "and gcc_designation='$designation_code'";
	}
	if(is_array($multiple_desig_arr) && count($multiple_desig_arr) > 0 ){
		$multiple_desig_ids = implode(',',$multiple_desig_arr);
		$desig_cond = "and gcc_designation in ($multiple_desig_ids) ";
	}
	$sql="SELECT GLH_LEAD_CODE,GLH_CUST_NAME ,GLH_AREA_NAME, GLH_CUST_PINCODE , GLH_CUST_CITY,GLH_CUST_STREETADDR2," .
			"glh_order_by_whom,GLH_LFD_EMP_ID, GLH_VERTICAL_CODE,GTM_VERTICAL_NAME,GLH_FIELD_INCHARGE, GLH_L1_INCHARGE,glh_country,GLH_CUST_STATECODE, " .
			" group_concat(DISTINCT if(gcc_contact_type=1,GCC_CONTACT_NO,null)) MOBILE," .
			" group_concat(DISTINCT if(gcc_contact_type=3,GCC_CONTACT_NO,null)) RES_PHONE," .
			" group_concat(DISTINCT if(gcc_contact_type=1 and GCG_GROUP_ID=1,GCC_CONTACT_NO,null)) TS_MOBILE, ".
			" group_concat(DISTINCT if(gcc_contact_type=1 and GCG_GROUP_ID=1,GCC_CONTACT_NAME,null)) TS_CONTACTNAME, ".
			" group_concat(DISTINCT if(gcc_contact_type=2,GCC_CONTACT_NO,null)) BUSSNO, " .
			" group_concat(DISTINCT if(gcc_contact_type=4,GCC_CONTACT_NO,null)) EMAIL," .
			" group_concat(DISTINCT if(gcc_contact_type=4,concat(GCC_CONTACT_NAME,'(',gcd_name,')'),null)) CONTACTNAME," .
			" group_concat(DISTINCT if(gcc_contact_type=1,concat(GCC_CONTACT_NAME,'(',gcd_name,')'),null)) MCONTACTNAME," .
			" group_concat(DISTINCT if(gcc_contact_type=7,trim(GCC_CONTACT_NO),null)) GTALK, ".
			" group_concat(DISTINCT if(gcc_contact_type=8,trim(GCC_CONTACT_NO),null)) SKYPE,GPM_GST_STATE_CODE,GTM_TYPE_NAME, ".
			" gpm_map_id,glh_main_product ".
			" FROM (gft_lead_hdr,gft_customer_contact_dtl,gft_contact_designation_master) " .
			" left join gft_political_map_master on (glh_cust_statecode=gpm_map_name and GPM_MAP_TYPE='S') ".
			" left join gft_vertical_master gtm on (GTM_VERTICAL_CODE=GLH_VERTICAL_CODE)" .
			" left join gft_type_master on(GTM_TYPE_CODE=GLH_BTYPE_CODE)".
			" left join gft_contact_dtl_group_map on(GLH_LEAD_CODE=GCG_LEAD_CODE) ".
			" where GCC_LEAD_CODE=GLH_LEAD_CODE  " .
			" and gcd_code = gcc_designation and gcd_status='A' $desig_cond and GLH_LEAD_CODE ='$lead_code' " .
			" group by GLH_LEAD_CODE ";
	$rs_query=execute_my_query($sql,'common_util.php',true,false,2);
	$tmp=/*. (string[string]) .*/ array();
	$record_found=false;
	while($row=mysqli_fetch_array($rs_query)){
		$record_found=true;

		$tmp['cust_name']=$row['GLH_CUST_NAME'];
		$tmp['area_name']=$row['GLH_AREA_NAME'];
		$tmp['pincode']=$row['GLH_CUST_PINCODE'];
		$tmp['city']=$row['GLH_CUST_CITY'];
		$tmp['LOCATION']=$row['GLH_CUST_STREETADDR2'];
		$tmp['mobile_no']=$row['MOBILE'];
		$tmp['res_phone']=$row['RES_PHONE'];
		$tmp['ts_mobile_no']=$row['TS_MOBILE'];
		$tmp['ts_contact_name']=$row['TS_CONTACTNAME'];
		$tmp['contact_name']=$row['CONTACTNAME'];
		$tmp['mcontact_name']=$row['MCONTACTNAME'];
		$tmp['bussno']=$row['BUSSNO'];
		$tmp['EMAIL']=$row['EMAIL'];
		$tmp['GTALK']=$row['GTALK'];
		$tmp['SKYPE']=$row['SKYPE'];
		$tmp['Order_By_Whom']=$row['glh_order_by_whom'];
		$tmp['LFD_EMP_ID']=$row['GLH_LFD_EMP_ID'];
		$tmp['Reg_incharge']=$row['GLH_L1_INCHARGE'];
		$tmp['Field_incharge']=$row['GLH_FIELD_INCHARGE'];
		$tmp['vertical']=$row['GLH_VERTICAL_CODE'];
		$tmp['VERTICAL_NAME']=$row['GTM_VERTICAL_NAME'];
		$tmp['COUNTRY_NAME']=$row['glh_country'];
		$tmp['cust_state_code']	= (int)$row['GPM_GST_STATE_CODE'];
		$tmp['BUSINESS_NAME']	= $row['GTM_TYPE_NAME'];
		$tmp['state_name']	= $row['GLH_CUST_STATECODE'];		
		$tmp['state_id'] = $row['gpm_map_id'];
		$tmp['main_product'] = $row['glh_main_product'];
	}

	if (!$record_found){
		$stack_out = getStackTraceString();
		$desig = '';
		if($designation_code!=0) {
			$desig .= "with Designation as ".get_single_value_from_single_table("GCD_NAME", "gft_contact_designation_master", "GCD_CODE", $designation_code);
		}
		error_log("Contact details $desig is not available for this Customer ($lead_code). stacktrace: ". $stack_out);
		if(!$from_app) {
			show_my_alert_msg("Contact details $desig is not available for this Customer ($lead_code). Please add/update the Designation as Proprietor for Mobile number and Email id and then create Quotation / Proforma.");
			close_the_popup();
		}
	}
  return $tmp;
}
/**
 * @param string $cust_id
 * @param string $contact_type
 * @param string $desig
 *
 * @return string[string]
 */
function get_contact_dtl_inarray($cust_id,$contact_type,$desig){
	$data = /*. (string[int]) .*/array();
	$data_arr = /*. (string[int]) .*/array();
	$que = "select gcc_id as id,GCC_CONTACT_NAME as name, GCC_CONTACT_NO as contact from gft_customer_contact_dtl  where GCC_LEAD_CODE='$cust_id' ";
	if($contact_type!=''){
		$que.=" and gcc_contact_type=$contact_type ";
	}
	if($desig!=''){
		$que.="and gcc_designation=$desig ";
	}
	$que .= " order by GCC_ID ";
	$res = execute_my_query($que);
	while($row = mysqli_fetch_array($res)){
		$data['id'] 	= $row['id'];
		$data['name'] 	= $row['name'].($row['name']!=""?" - ":"").$row['contact'];
		$data['contact']= $row['contact'];
		$data_arr[]=$data;
	}
	return $data_arr;
}
/**
 * @param string $cust_id
 * @param string $contact_type
 * @param string $desig
 * 
 * @return string[int][string]
 */
function get_contact_dtl_for_designation($cust_id,$contact_type,$desig){
	$data = /*. (string[int]) .*/array();
	$que = "select GCC_CONTACT_NO from gft_customer_contact_dtl  where GCC_LEAD_CODE='$cust_id' ";
	if($contact_type!=''){
		$que.=" and gcc_contact_type in ($contact_type) ";
	}
	if($desig!=''){
		$que.="and gcc_designation in ($desig) ";
	}
	$que .= " order by GCC_ID ";
	$res = execute_my_query($que);
	while($row = mysqli_fetch_array($res)){
		$data[] = $row['GCC_CONTACT_NO']; 
	}
	return $data;
}

/* Start Order Question Send to Email for Id 90 */
/**
 * @return string[string][string]
 */
function Order_Audit_CM_Question(){
    $vertical_code=(isset($_POST['vertical_code'])?(string)$_POST['vertical_code']:'');
    $product=(isset($_GET['product_code'])?(string)$_GET['product_code']:'');
    $product_code_array=explode('-',$product);
    $product_code=$product_code_array[0];
    if(isset($_POST['audit_type']) and $_POST['audit_type']==21){
        $audittypeid = "21";
    }else{
        $audittypeid = "17";
    }
    if(!empty($vertical_code)){
    $sql = "SELECT GAQ_VERTICAL_CODE FROM gft_audit_question_master WHERE GAQ_STATUS='A' AND GAQ_AUDIT_TYPE in ($audittypeid)
             AND GAQ_VERTICAL_CODE = $vertical_code";
             $exe = execute_my_query($sql);
             $counts = mysqli_num_rows($exe);
             if($counts == 0){
                 $vertical_code = "20";
             }
    }else{
        $vertical_code = "20";
    }
	$query="select GAQ_GROUP_NAME,GAQ_QUESTION_ID,GAQ_QUESTION_TYPE " .
			" from gft_audit_question_master qm " .
			" left join gft_audit_question_group_master gm on (gm.GAQ_GROUP_ID=qm.GAQ_GROUP_ID)" .
			" left join gft_audit_question_group_map_master gmm on (gmm.GAQ_AUDIT_ID=GAQ_AUDIT_TYPE and GAQ_QGROUP_ID=gm.GAQ_GROUP_ID) " .
			" where qm.GAQ_STATUS='A' " .
			" and GAQ_PRODUCT_CODE in ('0' ".($product_code!=0 ? ",'$product_code'":'') .") " .
			" and GAQ_VERTICAL_CODE in (0".($vertical_code!=0 ? ",$vertical_code":'')." ) " .
			" and GAQ_AUDIT_TYPE =".$audittypeid." order by GAQ_QORDER_BY,GAQ_ORDER_BY ";

	$finalResult=/*. (string[string][string]) .*/ array();
	$result=execute_my_query($query);
	while($results = mysqli_fetch_assoc($result)){
		$finalResult[$results['GAQ_GROUP_NAME']][$results['GAQ_QUESTION_ID']] = $results['GAQ_QUESTION_TYPE'];
	}
	return $finalResult;
}
/**
 * @param string[string] $qidans
 * 
 * @return string
 */
function get_Order_Audit_CM_Question_Ans($qidans=null){
	global $attach_path;
	$quesAns = /*. (string[int]) .*/ isset($_REQUEST['qidans'])?$_REQUEST['qidans']:array();
	//if (count($quesAns) == 0){
	//	error_log("Return without processing as count of quesAns is zero");
		//return '';
	//}
	if(empty($quesAns)){
		$quesAns	=	$qidans;
	}
	$print_motgin="--top 0 --bottom 0 --left 0 --right 0";

	$html = "<table border=1  >";
	foreach(Order_Audit_CM_Question() as $cmKey=>$cmValue){
		$html .="<tr style=\"background-color:#EF9C30;color:#000;\"><th colspan=\"2\" style=\"color: #000000;font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 10pt;
    font-weight: bold;\" align=\"center\">$cmKey</th>";
		foreach($cmValue as $cmQKey=>$cmQValue){
			$answerValue= '';
			if(!empty($quesAns)){
			if(isset($quesAns[$cmQKey]) && is_array($quesAns[$cmQKey])){
				$answerValue=$quesAns[$cmQKey][0];
			}else{
				$answerValue=isset($quesAns[$cmQKey])?$quesAns[$cmQKey]:'';
			}
			}
			$html .="<tr><td align=\"left\" style=\"color: #000000;font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 10pt;font-weight: normal;\">$cmQValue</td>" .
					"<td align=\"left\" style=\"color: #000000;font-family: Verdana,Arial,Helvetica,sans-serif;font-size: 10pt;font-weight: normal;\">".$answerValue."</td><tr>";
		}
	}
	$html .="</table>";
	if(isset($_POST['audit_type']) and $_POST['audit_type']==21){		
		$order_no=(isset($_GET['order_no'])?(string)$_GET['order_no']:'');
		$content="<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"><html><head></head><body>".$html."</body></html>";
    	$content=str_replace('<td> </td>','<td>&nbsp;</td>', $content);
    	$content=str_replace('\"','"', $content);
		$content=str_replace('<tbody>','', $content);
		$content=str_replace('</tbody>','', $content);
		$content=str_replace("&apos;","'", $content);
    	$html_fname="report_".$order_no.".html";
    	$folder_name="product_delivery_pdf";
        $t=write_to_file($folder_name,$content,$html_fname,$mode=null);
        $file_path=realpath("$folder_name/");
		$f_name=str_replace("html","pdf",$html_fname);
		$fr_name=$attach_path."/product_delivery_pdf".'/'."$f_name";
		$filename=$attach_path."/product_delivery_pdf".'/'.$html_fname;
		passthru("htmldoc --quiet --color --size A4 -t pdf14 --jpeg --footer ... $print_motgin -f $fr_name --webpage $filename ");
	}
	return $html;
}
/* End Order Question Send to Email for Id 90 */
/**
 * @param string $pc_emp_id
 * 
 * @return string
 */
function get_pc_profile_link($pc_emp_id){
	$pc_profile_link	=	'';
	$result_profile_link	=	execute_my_query("select GEP_PROFILE_URL from gft_employee_profile_details where GEP_EMP_ID='$pc_emp_id'");
	if($pc_profile_row=mysqli_fetch_array($result_profile_link)){
		if($pc_profile_row['GEP_PROFILE_URL']!=''){
			$pc_profile_link	=	$pc_profile_row['GEP_PROFILE_URL'];					
		}
	}
	return $pc_profile_link;
}
/**
 * @param string $lead_code
 * @param string $uid
 * @param string $audit_id
 * @param string $complaint_id
 * @param string $order_no
 * @param string[string] $qidans
 * @param string $product_code
 * 
 * @return void
 */
function impl_send_sms_mail_process_info($lead_code,$uid,$audit_id=null,$complaint_id=null,$order_no='',$qidans=null,$product_code=''){
	global $attach_path;;
	global $non_employee_group;
	$cust_sms_category=0;
	$cust_mail_category=0;
	$db_content_config=/*. (mixed[string]) .*/ array();

	$user_dtl=get_emp_master($uid,'A',null,false);
	$cust_dtl=customerContactDetail_Mail($lead_code,$order_no);
	$ts_contact_no_dtl = get_customer_ts_number($lead_code);
	$spoc_no	=	isset($ts_contact_no_dtl[0])?$ts_contact_no_dtl[0]:"";
	$spoc_person=	isset($ts_contact_no_dtl[1])?$ts_contact_no_dtl[1]:"";
	if($spoc_no==''){$spoc_no="-";}
	if($spoc_person==''){$spoc_person="&nbsp;";}
	$user_master=get_emp_master($uid,'A',null,false);
	$sales_emp_name	=9999;
	if(is_authorized_group_list($cust_dtl['PDCM_EMP_ID'], array (54,36))){$sales_emp_name=$cust_dtl['PDCM_EMP_ID']; }else{$sales_emp_name= $cust_dtl['LFD_EMP_ID']; }
	$jemp_master=get_emp_master($cust_dtl['Field_incharge'],$status='',$roleid=null,$only_gft_emp=false,$select_any=false);
	$com_jemp_master=get_emp_master($sales_emp_name,$status='',$roleid=null,$only_gft_emp=false,$select_any=false);
	$other_cc			=	array($sales_emp_name);
	$pc_emp_id	=	$cust_dtl['Field_incharge'];
	$pc_profile_link	=	get_pc_profile_link($pc_emp_id);
	if($pc_profile_link!=''){
		$pc_profile_link="<a href='".$pc_profile_link."'>Read more about me</a>";
	}else{
		$pc_profile_link = "&nbsp;";
	}	
	$emp_company_name	=	'';
	$emp_partner_type	=	'';
	$push_noti_cat		= ''; 
	$push_template_id	= '';
	$push_cust_template_id="";
	$noti_content_config	= array();
	$help_us_help_you	=	get_samee_const("Help_Us_Help_You");
	$sys_requirement	=	get_system_requirement_link($order_no);
	$coupon_expiry_info	=	get_coupon_expiry_information($order_no,$lead_code);
	if(isPartnerEmployee((int)$cust_dtl['Field_incharge'])){
		$emp_company_name	=	getEmployeeCompanyName((	int)$cust_dtl['Field_incharge']);
		$emp_partner_type	=	getEmployeeCompanyType((int)$cust_dtl['Field_incharge']);
		//$other_cc	=	array_merge($other_cc,getPartnerIdBusinessManagerId($order_no));
	}
	if(isPartnerEmployee((int)$sales_emp_name)){
		//$other_cc	=	array_merge($other_cc,getPartnerIdBusinessManagerId($order_no));
	}
	if(isPartnerEmployee((int)$sales_emp_name)){
		//$other_cc	=	array_merge($other_cc,getPartnerIdBusinessManagerId($order_no));
	}
	$audit_type=0;
	$sql_rc_details	=	"select lem.GLEM_EMP_ID, lem.GLEM_LEADCODE, ci.cgi_lead_code, ci.CGI_EMP_ID,em.gem_emp_name,ca.GCA_CP_SUB_TYPE,ls.GLS_SUBTYPE_NAME from gft_leadcode_emp_map lem ".
								" inner join gft_cp_info as ci ON(lem.GLEM_LEADCODE=ci.CGI_LEAD_CODE) ".
								" inner join gft_emp_master AS em ON(em.gem_emp_id=ci.CGI_EMP_ID) ".
								" inner join gft_cp_agree_dtl as ca ON(ca.gca_lead_code=ci.CGI_LEAD_CODE) ".
								" inner join gft_lead_subtype_master as ls ON(ls.GLS_SUBTYPE_CODE=ca.GCA_CP_SUB_TYPE) ".
								" where GLEM_EMP_ID=".$uid;	
	$result_rc_details=execute_my_query($sql_rc_details);	
	$partner_type	=	'';
	$partner_name	=	'';
	if(mysqli_num_rows($result_rc_details)==1)
	{
		$row_rc		=	mysqli_fetch_array($result_rc_details);
		if($row_rc['GLEM_EMP_ID']==$row_rc['CGI_EMP_ID']){
			$partner_name	.=	$row_rc['gem_emp_name'];
		}
		$partner_type	.=	"<br>Authorized GoFrugal ".$row_rc['GLS_SUBTYPE_NAME']."";
	}
	if($audit_id!=null){
		//sms to pcs manager team
		$result=execute_my_query("SELECT GAH_ORDER_NO,GAH_ORDER_AUDIT_STATUS, GAH_PENDING_IMP,GAH_CM_APPROVAL_STATUS,GAH_HANDOVER_STATUS, GAH_REQUIRED_TRAINING_DATE, GAH_AUDIT_TYPE,GAH_CUSTOMER_COMMENTS,GAH_MY_COMMENTS, " .
			" GROUP_CONCAT(DISTINCT concat('<tr><td>',GAQ_QUESTION_TYPE,'</td><td>',GAD_AUDIT_ANS,'</td></tr>') separator '') quest_ans, group_concat(distinct GTS_STATUS_NAME) training_status " .
			" FROM gft_audit_hdr" .
			" JOIN gft_audit_type_master ON(GAH_AUDIT_TYPE=GAT_AUDIT_ID)" .
			" left join gft_audit_dtl on (GAH_AUDIT_ID=GAD_AUDIT_ID)" .
			" left JOIN gft_audit_question_master on(GAD_AUDIT_QID=GAQ_QUESTION_ID)" .
			" left join gft_install_dtl_new on (GAH_LEAD_CODE=GID_LEAD_CODE) " .
			" left join gft_training_status on (GID_TRAINING_STATUS=GTS_STATUS_CODE) " .
			" WHERE GAH_AUDIT_ID=$audit_id AND GAH_LEAD_CODE=$lead_code group by GAH_AUDIT_ID ");
		if($data=mysqli_fetch_array($result)){
			$audit_type=(int)$data['GAH_AUDIT_TYPE'];
			$GAH_ORDER_NO=$data['GAH_ORDER_NO'];
			$db_content_config=array('user_name'=>array($user_master[0][1]),
				'Customer_Id'=>array($lead_code),
				'Customer_Name'=>array($cust_dtl['cust_name']),
				'Customer_Mobile'=>array($cust_dtl['mobile_no']),
				'Customer_Mail'=>array($cust_dtl['EMAIL']),
				'PC_Name'=>array($jemp_master[0][1].$emp_company_name.$emp_partner_type),
				'PC_Mobile'=>array($jemp_master[0][3]),
				'PC_Email'=>array($jemp_master[0][4]),
				'PC_Role'=>array($jemp_master[0][7]!=''?$jemp_master[0][7]:'&nbsp;'),
				'training_target_date'=>array($data['GAH_REQUIRED_TRAINING_DATE']),
				'implementation_status'=>array($data['GAH_PENDING_IMP']),
				'training_status'=>array($data['training_status']),
				'audit_qans'=>array($data['quest_ans']),
				'Employee_Name'=>array($user_dtl[0][1]),
				'Sales_Manager_Name'=>array($com_jemp_master[0][1]),
				'Sales_Manager_Role'=>array($com_jemp_master[0][7]!=''?$com_jemp_master[0][7]:'&nbsp;'),
				'Sales_Manager_Mobile'=>array($com_jemp_master[0][3]),
				'Help_Us_Help_You'=>array($help_us_help_you),
				'System_Requirement'=>array($sys_requirement),
				'Coupon_Details'=>array($coupon_expiry_info),
				'Profile_Link'=>array($pc_profile_link),
				'training_assurance'=>array('training_assurance'=>array("<a href=\"".get_samee_const("DOMAIN_NAME")."/supporthistory.php?from_dt=01-11-2004&to_dt=&custCode=$lead_code&history_type=9\">click here for Details</a></br>")),
				'order_audit_status'=>array($data['GAH_ORDER_AUDIT_STATUS'])
			);
			$reason_for_not_accept	=	'"Customer Comment: "'.$data['GAH_CUSTOMER_COMMENTS'].', PC Comment: '.$data['GAH_MY_COMMENTS'];
			$noti_content_config['CM_Name']			= array($com_jemp_master[0][1]);
			$noti_content_config['Customer_Name']	= array($cust_dtl['cust_name']);
			$noti_content_config['Customer_Id']		= array($lead_code);			
			$noti_content_config['Help_Us_Help_You']		= array("<a href='$help_us_help_you' target='_blank'>[Link]</a>");
			$noti_content_config['PC_Name']		= array($jemp_master[0][1]);
			$noti_content_config['PC_Mobile']		= array($jemp_master[0][3]);	
			$noti_content_config['Profile_Link']		= array($pc_profile_link);
			$noti_content_config['SPOC_NO']			= array($spoc_no);
			$noti_content_config['Mail_Content']	= array($reason_for_not_accept);
			$db_cust_sms_content_config=array('Customer_Id'=>array($lead_code),
				'Customer_Name'=>array($cust_dtl['cust_name']),
				'PC_Name'=>array($jemp_master[0][1]));
			$quest_ans     ="<br><table border='1'>".$data['quest_ans']."</table><br>";				
			$db_cust_mail_content_config=array('Customer_Id'=>array($lead_code),
				'Customer_Name'=>array($cust_dtl['cust_name']),
				'SPOC_NO'=>array($spoc_no),
				'SPOC_PERSON'=>array($spoc_person),
				'Customer_Mobile'=>array(($cust_dtl['mobile_no']!=""?$cust_dtl['mobile_no']:"&nbsp;-")),
				'Customer_Mail'=>array($cust_dtl['EMAIL']),
				'PC_Name'=>array($jemp_master[0][1].$emp_company_name.$emp_partner_type),
				'PC_Mobile'=>array($jemp_master[0][3]),
				'PC_Email'=>array($jemp_master[0][4]),
				'PC_Role'=>array($jemp_master[0][7]!=''?$jemp_master[0][7]:'&nbsp;'),
				'training_target_date'=>array($data['GAH_REQUIRED_TRAINING_DATE']),
				'implementation_status'=>array($data['GAH_PENDING_IMP']),
				'training_status'=>array($data['training_status']),
				'audit_qans'=>array($quest_ans),
				'Employee_Name'=>array($user_dtl[0][1]),
				'Sales_Manager_Name'=>array($com_jemp_master[0][1]),
				'Sales_Manager_Role'=>array($com_jemp_master[0][7]!=''?$com_jemp_master[0][7]:'&nbsp;'),
				'Sales_Manager_Mobile'=>array($com_jemp_master[0][3]),
				'training_assurance'=>array("<a href=\"".get_samee_const("DOMAIN_NAME")."/supporthistory.php?from_dt=01-11-2004&to_dt=&custCode=$lead_code&history_type=9\">click here for Details</a></br>"),
				'order_audit_status'=>array($data['GAH_ORDER_AUDIT_STATUS']),
				'Help_Us_Help_You'=>array($help_us_help_you),
				'System_Requirement'=>array($sys_requirement),
				'Coupon_Details'=>array($coupon_expiry_info),
				'Profile_Link'=>array($pc_profile_link),
				'partnertypename'=>array($partner_name.$partner_type)
				);
			
			$category=55;$mail_template_id=88;
			switch ($audit_type){
				case 13:
					$db_sms_content_config=array('user_name'=>array($user_dtl[0][1]."[".$user_dtl[0][3]."]"),'lead_code'=>array($lead_code));
					$sms_content=htmlentities (get_formatted_content($db_sms_content_config,118));
					sending_sms_to($sms_content,118,array($cust_dtl['Reg_incharge'],$sales_emp_name),$individual=true,$rep_mgr=true,$group_arr=null,$uid);
					$category=55;$mail_template_id=88;
					$cust_sms_category=122;
					$cust_mail_category=123;$cust_mail_template_id=123;
					break;
				case 15:
					/* $db_content_config['audit_process']=array('Implementation Audit Completed');
					$db_sms_content_config=array('user_name'=>array($user_dtl[0][1]."[".$user_dtl[0][3]."]"));
					$message_customer=htmlentities (get_formatted_content($db_sms_content_config,117));
					entry_sending_sms_to_customer(null,$message_customer,117,$lead_code,0,$uid);
					$category=55;$mail_template_id=89;
					$cust_sms_category=122;
					$cust_mail_category=123;$cust_mail_template_id=123; */
					$push_noti_cat=55;$push_template_id=7;
					break;
				case 17:
					$db_content_config['audit_process']=array('Commercial Manager Approved implementation.');
					$db_cust_mail_content_config['CM_Feedback']=array(get_Order_Audit_CM_Question_Ans($qidans));
					$cust_mail_category=55;$cust_mail_template_id=90;
					$mail_template_id=90; /* executive */
					$cust_sms_category=125;$category=55;$mail_template_id=90;
					break;
				case 38:
					$db_content_config['audit_process']=array('Partner Commercial Manager Approved implementation.');					
					if(is_authorized_group_list($uid,$non_employee_group)){
						$cust_mail_category=55;$cust_mail_template_id=163;$mail_template_id=118;
					}else{
						$db_cust_mail_content_config['CM_Feedback']=array(get_Order_Audit_CM_Question_Ans());
						$cust_mail_category=55;$cust_mail_template_id=90;
						$mail_template_id=90; /* executive */
					}	
					$cust_sms_category=125;$category=55;$mail_template_id=90;
					break;
				case 18:
					$db_content_config['audit_process']=array('Training Coordinator Approved.');
					/*	handeled mail process in  impl_task_review.php 
					$category=55;$mail_template_id=91; 
					$cust_sms_category=122;
					$cust_mail_category=55;$cust_mail_template_id=113;
					*/
					break;
				case 19:
					$db_content_config['audit_process']=array('Training Completed. Handedover to Tech Support.');
					$category=55;$mail_template_id=92;
					$incharge_email=','.get_samee_const('GFT_CUST_CARE_EMAILID');
					$cust_sms_category=126;
					$cust_mail_category=55;$cust_mail_template_id=92;
					break;
				case 21:
					$prt_no = explode(".",$product_code);
					if(isset($prt_no[1]) && $prt_no[1] != '0T'){// if order is accepted this attachment will be send but it's not send for additional training
						$db_cust_mail_content_config['CM_Feedback']=array(get_Order_Audit_CM_Question_Ans());
						$pdf_fname="report_".$order_no.".pdf";
						$db_cust_mail_content_config['BQ_Attachment'] = array($attach_path."/product_delivery_pdf".'/'.$pdf_fname);
					}
					$db_content_config['audit_process']=array('Order Audit Completed');
					$category=55;$mail_template_id=110;
					$cust_sms_category=122;
					$cust_mail_category=55;
					$cust_mail_template_id=110;
					$push_noti_cat=55;$push_template_id=5;
					$push_cust_template_id=23;
				break;
				default:
					//Do nothing
					break;
			}
		}else if($complaint_id!=null and is_array($complaint_id)){
			$result=execute_my_query("select concat('<table border=1><tr><td>Milestone</td><td>Status</td></tr>',group_concat(concat('<tr><td>',GIM_MS_NAME,'</td><td>',GTS_STATUS_NAME,'</td></tr>') separator ''),'</table>') complaient_status " .
					" from gft_customer_support_hdr csh " .
					" join gft_cust_imp_ms_current_status_dtl imt on (csh.gch_complaint_id=imt.GIMC_COMPLAINT_ID) " .
					" join  gft_impl_mailstone_master on (GIM_MS_ID=GIMC_MS_ID) " .
					" gft_ms_task_status on (GTS_STATUS_CODE=GIMC_STATUS) " .
					" WHERE GCH_LEAD_CODE=$lead_code AND " .
					(is_array($complaint_id) ? " csh.gch_complaint_id in (" . implode(', ',/*. (string[int]) .*/ $complaint_id).") ": " csh.gch_complaint_id=$complaint_id ").
					"group by GCH_LEAD_CODE ");
			if($data=mysqli_fetch_array($result)){
				$db_content_config=array('user_name'=>array($user_master[0][1]),
					'Customer_Name'=>array($cust_dtl['cust_name']),
					'PC_Name'=>array($user_dtl[0][1]),
					'PC_Mobile'=>array($user_dtl[0][3]),
					'Employee_Role'=>array($user_dtl[0][7]),
					'audit_qans'=>array('<b>Milestone Review</b><br>'.$data['complaient_status']),
					'training_target_date'=>array($data['GCH_RESTORE_TIME']),
					'implementation_status'=>array(''),
					'order_audit_status'=>array('Yes'),
					'Help_Us_Help_You'=>array($help_us_help_you),
					'Coupon_Details'=>array($coupon_expiry_info),
					'Profile_Link'=>array($pc_profile_link),
					'System_Requirement'=>array($sys_requirement)
				);
				$db_cust_sms_content_config=array('Customer_id'=>array($lead_code));
				$db_cust_mail_content_config=array('Customer_Id'=>array($lead_code));
				$cust_sms_category=122;
				/*$cust_mail_category=55;
				$cust_mail_template_id=123;
				*/
			}
		}
		if(isset($cust_mail_template_id) and ($cust_mail_template_id == 118 || $cust_mail_template_id == 90 || $cust_mail_template_id == 92 || $cust_mail_template_id == 110)){
		$incharge_email=(isset($com_jemp_master[0][4])?$com_jemp_master[0][4] : '').','.$jemp_master[0][4].',training@gofrugal.com';
		$other_cc = array_merge($other_cc, get_email_addr_reportingmaster($cust_dtl['Field_incharge'],true,explode(",", EMP_IDS_TO_SKIP_REPORTING_MAIL)));
		}else{
		$incharge_email='training@gofrugal.com';
		}
		$result=execute_my_query("select concat('<table><tr><td>Milestone</td><td>Restore Time</td><td>Current Status</td></tr>', group_concat(concat('<tr><td>',GIM_MS_NAME,'</td></td>',GCH_RESTORE_TIME,'</td><td>',GTS_STATUS_NAME,'</td></tr>') separator ''),'</table>') training_assurance  " .
				" from gft_customer_support_hdr csh " .
				" join gft_cust_imp_ms_current_status_dtl imt on (csh.gch_complaint_id=imt.GIMC_COMPLAINT_ID) " .
				" join  gft_impl_mailstone_master on (GIM_MS_ID=GIMC_MS_ID) " .
 				" join gft_ms_task_status on (GIMC_STATUS=GTS_STATUS_CODE )" .
				" WHERE GCH_LEAD_CODE=$lead_code group by GCH_LEAD_CODE ");
		if($data=mysqli_fetch_array($result)){
			$db_content_config['training_assurance']=array($data['training_assurance']);
		}
		
		/*if(isset($db_content_config)){
			send_formatted_mail_content($db_content_config,$category,$mail_template_id,$employee_ids=null,$customer_ids=null,$tomail_ids=explode(',',$incharge_email));
		}*/
		if(isset($db_cust_sms_content_config)){
			$sms_content=htmlentities (get_formatted_content($db_cust_sms_content_config,$cust_sms_category));
			entry_sending_sms_to_customer(null,$sms_content,$cust_sms_category,$lead_code,0,$uid);
		}
		if(isset($db_cust_mail_content_config) && $cust_mail_category!=''){
		
			send_formatted_mail_content($db_cust_mail_content_config,$cust_mail_category,$cust_mail_template_id,$employee_ids=null,$customer_ids=array($lead_code),$tomail_ids=null,array_merge($other_cc,explode(',',$incharge_email)));
		}
		if($push_noti_cat!='' && $push_template_id!=''){
			send_formatted_notification_content($noti_content_config,$push_noti_cat,$push_template_id,1,$sales_emp_name);
		}
		if($push_cust_template_id!=''){			
			send_formatted_notification_content($noti_content_config,$push_noti_cat,$push_cust_template_id,2,$lead_code);
		}
	}else if($complaint_id!=null){ // On Change of Schedule
		$result=execute_my_query("select dtl.gcd_schedule_date,dtl.gcd_estimated_time,gim_ms_name from gft_customer_support_hdr csh " .
					"join gft_cust_imp_ms_current_status_dtl imt on (csh.gch_complaint_id=imt.GIMC_COMPLAINT_ID) " .
					"join  gft_impl_mailstone_master on (GIM_MS_ID=GIMC_MS_ID)  join gft_ms_task_status on (GIMC_STATUS=GTS_STATUS_CODE ) " .
					"right join gft_customer_support_dtl dtl on (GCH_COMPLAINT_ID = dtl.GCD_COMPLAINT_ID ) " .
					"WHERE GCH_LEAD_CODE=$lead_code and " .
					(is_array($complaint_id) ? " csh.gch_complaint_id in (" . implode(', ',$complaint_id).") ": " csh.gch_complaint_id=$complaint_id ").
					"order by dtl.GCD_Reported_date desc limit 1 " );
			if($data=mysqli_fetch_array($result)){
				$db_content_config=array('user_name'=>array($user_master[0][1]),
					'Customer_Name'=>array($cust_dtl['cust_name']),
					'Customer_Id'=>array($lead_code),
					'PC_Name'=>array($user_dtl[0][1]),
					'PC_Mobile'=>array($user_dtl[0][3]),
					'Employee_Role'=>array($user_dtl[0][7]),
					'Planned_Date'=>array($data['gcd_schedule_date']),
					'Rescheduled_Milestone_Name'=>array($data['gim_ms_name']),
					'Duration'=>array($data['gcd_estimated_time']),
					'Help_Us_Help_You'=>array($help_us_help_you),
					'System_Requirement'=>array($sys_requirement),
					'Coupon_Details'=>array($coupon_expiry_info),
					'Profile_Link'=>array($pc_profile_link),
					'Employee_Name'=>array($user_dtl[0][1])
				);
			}
			$cust_sms_category=123;
			$category=55;$mail_template_id=111;
			$db_cust_sms_content_config=array('Rescheduled_Milestone_Name'=>array($data['gim_ms_name']),
				'Planned_Date'=>array($data['gcd_schedule_date']));
			$sms_content=htmlentities (get_formatted_content($db_cust_sms_content_config,$cust_sms_category));
			entry_sending_sms_to_customer(null,$sms_content,$cust_sms_category,$lead_code,0);
			$incharge_email=$com_jemp_master[0][4].','.$jemp_master[0][4].',training@gofrugal.com';
			$other_cc = array_merge($other_cc, get_email_addr_reportingmaster($cust_dtl['Field_incharge'],true,explode(",", EMP_IDS_TO_SKIP_REPORTING_MAIL)));
			send_formatted_mail_content($db_content_config,$category,$mail_template_id,$employee_ids=null,$customer_ids=array($lead_code),$tomail_ids=explode(',',$incharge_email),$other_cc); // customer email			
		}
}

/**
 * @param string[string][int] $db_content_config
 * @param string $category
 * @param string $mail_template_id
 * @param array $ccemail
 * @param array $tomail_ids
 * @param string $team_cc_mail
 *
 * @return boolean
 */
function send_formatted_mail_content_leave($db_content_config,$category,$mail_template_id,$ccemail=array(),$tomail_ids=array(),$team_cc_mail=null){
	global $uid;

	$message=get_formatted_mail_content($db_content_config,$category,$mail_template_id);
	$cc=array();$from_mail_id='';
	if($message['from_mailid']=='user' and isset($_SESSION['uid'])){
		$user_master=get_emp_master($uid);
		$from_mail_id=$user_master[0][4];
	}else if($message['from_mailid']!='user'){
		$from_mail_id=$message['from_mailid'];
	}
	$message['category']=$category;
	$from_name=$message['from_name'];
	if($from_mail_id==''){
		$from_mail_id=get_samee_const("ADMIN_TEAM_MAIL_ID");
	}
	if($message['reply_to']=='user' and isset($_SESSION['uid'])){
		$user_master=get_emp_master($uid);
		$message['reply_to']=$user_master[0][4];
	}
	if($message['reply_to']==''){
		$message['reply_to']=get_samee_const("ADMIN_TEAM_MAIL_ID");
	}
	if($message['mail_to']=='E' and $ccemail!=null){
		foreach($ccemail as $key=>$employee_id){
			$employee_master=get_emp_master($employee_id);
			if($message['cc_to_emp'] || $message['cc_to_manager']){
				if(!empty($message['cc_to_emp'])){
					array_push($cc,$employee_master[0][4]);
				}
				if(!empty($message['cc_to_manager'])){
					array_push($cc,$employee_master[0][8]);
				}
			}
			if($message['cc_to_manager']){
				$mgr_email_id = get_emp_manager_mail_id($employee_id);
				if($mgr_email_id!=''){
					array_push($cc,$mgr_email_id);
				}
			}
		}
	}
	if($message['to_other_email']!='' || $message['cc_other_email']!=''){
		$toemail = explode(",",$message['to_other_email']);
		$ccemail = explode(",",$message['cc_other_email']);
		if(is_array($toemail) && count($toemail) != 0){
			foreach($toemail as $key=>$value){
			array_push($tomail_ids,$value);
			}
		}
		if(is_array($ccemail) && count($ccemail) != 0){
			foreach($ccemail as $key2=>$value2){
			array_push($cc,$value2);
			}
		}
	}
	if($team_cc_mail!=''){
		array_push($cc,$team_cc_mail);
	}
	if($tomail_ids!=null){
		if(array_key_exists("BQ_Attachment",$db_content_config)){ // if order is accepted this attachment will be send 
			$message['Attachment'] = $db_content_config["BQ_Attachment"];
		}
		$tomail_ids_arr=array_unique($tomail_ids);
		$tomail_ids=explode(',',implode(',',$tomail_ids_arr));
		$outgoing_email_id=send_mail_function($from_mail_id,$tomail_ids,$message['Subject'],$message['content'],$message['Attachment'],$cc,$message['category'],$message['content_type'],
		$message['reply_to'],$is_status_needed=false,$crontab_call=false,$from_name,$mail_template_id,$mail_compile_id=null,isset($message['EMBAttachment'])?$message['EMBAttachment']:null);
		return $outgoing_email_id;
	}else{
		return false;
	}
}

/**
 * @param string[int] $cc
 * @param string $employee_id
 *
 * @return void
 */
function add_reporting_managers_in_cc(&$cc,$employee_id){
    $mgr_email_id_arr = get_email_addr_reportingmaster($employee_id,true,explode(",", EMP_IDS_TO_SKIP_REPORTING_MAIL));
	foreach($mgr_email_id_arr as $key=>$value){
		array_push($cc,$value);
	}
}



/**
 * @param string[string][int] $db_content_config
 * @param int $category
 * @param int $mail_template_id
 * @param int[int] $employee_ids
 * @param int[int] $customer_ids
 * @param string[int] $tomail_ids
 * @param string[int] $other_cc
 * @param string[int] $cc_mail_ids
 * @param string $reply_to
 * @param string $from_emp_id
 * @param string $from_mail_id
 * @param string $mail_method
 *
 * @return boolean
 */
function send_formatted_mail_content($db_content_config,$category,$mail_template_id,$employee_ids=null,$customer_ids=null,$tomail_ids=null, 
		$other_cc=null, $cc_mail_ids=null, $reply_to='', $from_emp_id='',$from_mail_id='',$mail_method=''){
	global $uid,$mobile_uid;
	$message=get_formatted_mail_content($db_content_config,$category,$mail_template_id);
	$cc=/*. (string[int]) .*/ array();
	$user_mail_id='';

	if ($tomail_ids == null){
		$tomail_ids=/*. (string[int]) .*/ array();
	}
	if ($cc_mail_ids == null){
		$cc_mail_ids=/*. (string[int]) .*/ array();
	}
	if($tomail_ids!='' and !is_array($tomail_ids)){
		$tomail_ids=explode(',',$tomail_ids);
	}
	if($cc_mail_ids!='' and !is_array($cc_mail_ids)){
		$cc_mail_ids=explode(',',$cc_mail_ids);
	}
	if($from_emp_id!=''){
		$user_master=get_emp_master($from_emp_id,'',null,false);
		$user_name = isset($user_master[0][1]) ? $user_master[0][1] : '';
		$user_mail_id=isset($user_master[0][4]) ? $user_master[0][4] : '';		
	}else if(isset($_SESSION['uid'])){
		$user_master=get_emp_master($uid);
		$user_name = isset($user_master[0][1]) ? $user_master[0][1] : '';
		$user_mail_id=isset($user_master[0][4]) ? $user_master[0][4] : '';
	}else if(isset($mobile_uid) && $mobile_uid!=""){
		$user_master=get_emp_master($mobile_uid);
		$user_name = isset($user_master[0][1]) ? $user_master[0][1] : '';
		$user_mail_id=isset($user_master[0][4]) ? $user_master[0][4] : '';
	}
 if($message!=null){
	if( (($message['from_mailid']=='user') || $from_emp_id!='') and ($user_mail_id!='') ){
		$from_mail_id=$user_mail_id;
		if($user_name!=''){
			$message['from_name'] = $user_name;
		}
	}else if( ($message['from_mailid']!='user') && ($from_mail_id=='') ){
		$from_mail_id=$message['from_mailid'];
	}
	if($from_mail_id==''){
		$from_mail_id=get_samee_const("ADMIN_TEAM_MAIL_ID");
	}
	if($message['reply_to']=='user' and $user_mail_id!='' ){
		$message['reply_to']=$user_mail_id;
	}
	if($reply_to!=''){
		$message['reply_to'] = ($message['reply_to']!='')?$message['reply_to'].','.$reply_to:$reply_to;
	}
	if($message['reply_to']==''){
		$message['reply_to']=get_samee_const("ADMIN_TEAM_MAIL_ID");
	}
		
	if($message['mail_to']=='E' and $employee_ids!=null){
		if(!is_array($employee_ids)) $employee_ids=explode(',',$employee_ids);

		foreach($employee_ids as $key=>$employee_id){
			$employee_master=get_emp_master($employee_id,$status='',$roleid=null,$only_gft_emp=false,$select_any=false);
			if(isset($employee_master[0][4]) and $employee_master[0][4]!=''){
				array_push($tomail_ids,$employee_master[0][4]);				
			}
			if($message['cc_to_manager']){
				add_reporting_managers_in_cc($cc,$employee_id);				
			}
		}
	}
	if($message['to_other_email']!=''){ 
		
		$toemail = explode(",",$message['to_other_email']);
		if(is_array($toemail) && count($toemail) != 0){
			foreach($toemail as $key=>$value){
				if($value=='user' and $user_mail_id!=''){
					array_push($tomail_ids,$user_mail_id);
				}else {
					array_push($tomail_ids,$value);
				}			
			}
		}
    } 
    
    if($message['cc_other_email']!=''){
		$ccemail = explode(",",$message['cc_other_email']);
		if(is_array($ccemail) && count($ccemail) != 0){
			foreach($ccemail as $key=>$value){
				if($value=='user' and $user_mail_id!='' ){
					array_push($cc,$user_mail_id);
				}else {
					array_push($cc,$value);
				}
			}
		}
	}
	if($message['mail_to']=='C' and $customer_ids!=null and empty($tomail_ids)){
		foreach($customer_ids as $key=>$customer_id){
			$cust_dtl=customerContactDetail($customer_id);
				$custemail = explode(",",$cust_dtl['EMAIL']);
				$custname = explode(",",$cust_dtl['contact_name']);
				$custnamedesg = explode("-",$custname[$key]);
				$mcustname = explode(",",$cust_dtl['mcontact_name']);
				$custnames = explode('(',$custnamedesg[0]);
				if($custnames[0]!=''){
					$custnames = $custnamedesg[0];
					$message['content'] = str_replace(array("{{CUST_NAME}}"),array($custnames,$custnamedesg[0]),$message['content']);
				}else{
					$custnames = "Customer";
					$message['content'] = str_replace(array("Mr {{CUST_NAME}}"),array($custnames),$message['content']);
				}
				if(!empty($custemail) and trim($custemail[0])!=''){
					array_push($tomail_ids,$custemail[0]);					
				}
				
				
				if(empty($tomail_ids) and $user_mail_id!=''){
					array_push($tomail_ids,$user_mail_id);					
				}
				
				if($message['cc_to_emp'] and $employee_ids==null ){
					if($cust_dtl['Field_incharge']!==SALES_DUMMY_ID){
					$employee_master=get_emp_master($cust_dtl['Field_incharge'],$status='A',$roleid=null,$only_gft_emp=false,$select_any=false); 
					   if (isset($employee_master[0][4])){
					       array_push($cc,$employee_master[0][4]);
					   }
					}
					if($cust_dtl['LFD_EMP_ID']!==SALES_DUMMY_ID){
					$employee_master=get_emp_master($cust_dtl['LFD_EMP_ID'],'A');
						if (isset($employee_master[0][4])){
							array_push($cc,$employee_master[0][4]);
						}
					}
					// Currently RC is not available in the process
					/* if($cust_dtl['Reg_incharge']!==SALES_DUMMY_ID){
					$employee_master=get_emp_master($cust_dtl['Reg_incharge'],$status='',$roleid=null,$only_gft_emp=false,$select_any=false); 
					if(isset($employee_master[0][4])){
						array_push($cc,$employee_master[0][4]);
					}
					} */
					if($message['cc_to_manager']){
						add_reporting_managers_in_cc($cc,$cust_dtl['Field_incharge']);
						add_reporting_managers_in_cc($cc,$cust_dtl['LFD_EMP_ID']);				
					}
				}else if($message['cc_to_emp'] and $employee_ids!=null ){
					if(!is_array($employee_ids)) $employee_ids=explode(',',$employee_ids);
			
					foreach($employee_ids as $key=>$employee_id){
						$employee_master=get_emp_master($employee_id,$status='',$roleid=null,$only_gft_emp=false,$select_any=false); 
						if(isset($employee_master[0][4]) and $employee_master[0][4]!=''){
							array_push($cc,$employee_master[0][4]);				
						}
						if($message['cc_to_manager']){
							add_reporting_managers_in_cc($cc,$employee_id);							
						}
					}/* End of for */
				}/* end of else */
			
		}
	}
	if($other_cc!=''){
		if(!is_array($other_cc)) {
			$other_cc=explode(',',$other_cc);
		}
		foreach($other_cc as $key=>$other_id) { 
			if($other_id!=''){
				$other_emp=get_emp_master($other_id,$status='',$roleid=null,$only_gft_emp=false,$select_any=false);
				if(isset($other_emp[0][4]) and $other_emp[0][4]!=''){
					array_push($cc,$other_emp[0][4]);				
				}
			}
		}
	}
	foreach ($cc_mail_ids as $value) {
		if($value!=''){
			array_push($cc,$value);
		}
	}
	if(empty($tomail_ids) && $user_mail_id!=''){
		$tomail_ids[0]=$user_mail_id;
	}
	
	/* clean up 
	 * 1. to email id 
	 * 2. cc no repeatiaon and to email id not repeated in cc 
	 * */
	$tomail_ids=array_unique($tomail_ids);
	$cc=array_unique($cc);
	/* remove repitative */
	$cc_unique=array();
	foreach($cc as $key => $value){
		if(!in_array($value,$tomail_ids)){
			array_push($cc_unique,$value);
		}
	}
	$cc=$cc_unique;
 }
	if(!empty($tomail_ids) and trim($tomail_ids[0])!=false){
	
		if(array_key_exists("BQ_Attachment",$db_content_config)){ // if order is accepted this attachment will be send 
			$message['Attachment'] = $db_content_config["BQ_Attachment"];
		}else if(empty($message['Attachment']) and !empty($db_content_config["Attachment"])){
			$message['Attachment']= $db_content_config["Attachment"][0];
		}
		$outgoing_email_id=send_mail_function($from_mail_id,$tomail_ids,$message['Subject'],$message['content'],$message['Attachment'],$cc,$message['category'],$message['content_type'],
		$message['reply_to'],$is_status_needed=false,$crontab_call=false,$message['from_name'],$mail_template_id,$mail_compile_id=null,isset($message['EMBAttachment'])?$message['EMBAttachment']:null,$mail_method);
		return $outgoing_email_id;
	}else{
		return false;
	}
}

/**
 * @param string $orderno
 *
 * @return string
 */
function get_emp_id_frm_orderno($orderno){
	$tmp='';
	$sql="SELECT GOD_INCHARGE_EMP_ID FROM  gft_order_hdr WHERE GOD_ORDER_NO='$orderno'";
	$rs=execute_my_query($sql,'common_util.php',true,false,2);
	while($row=mysqli_fetch_array($rs)){
		$tmp=$row[0];
	}
	return $tmp;
}

/**
 * @param string $code
 *
 * @return string[int]
 */
function get_productcode_info($code){
	if(is_numeric($code)){
		$sql="SELECT GPM_PRODUCT_NAME,GPM_PRODUCT_ABR,GPM_SUPPORT_MAILID FROM  gft_product_family_master " .
			" WHERE GPM_PRODUCT_CODE='$code' ";
		$rs=execute_my_query($sql,'common_util.php',true,false,2);
		$row=mysqli_fetch_array($rs);
		$info[0]=$row[0];
		$info[1]=$row[1];
		$info[2]=$row[2];
		return $info;
	}else{
		return null;
	}
}

/**
 * @param string $status_code
 * @param string $sgroup_id
 *
 * @return string[int][int]
 */
function get_support_status_master($status_code=null,$sgroup_id=null){
	$query_status="select * from gft_status_master where gtm_status='A' ";
	if($status_code!=null){ $query_status.=" and gtm_code='$status_code' ";}
	if($sgroup_id!=null){
		if(is_array($sgroup_id)){ $sgroup_id=implode(',',$sgroup_id); }
		$query_status.=" and gtm_group_id in ($sgroup_id) ";
	}
	return get_two_dimensinal_array_from_query($query_status,'GTM_CODE','GTM_NAME');
}

/**
 * @param string $fid
 * @param string $filepath
 * @param string $isManual
 * 
 * @return string[int][int]
 */
function get_crontab_master($fid=null,$filepath=null,$isManual=null){
    $sql="select GSCM_FILE_ID,GSCM_PURPOSE,GSCM_PHP_FILE,GSCM_MANUAL_STATUS from gft_sam_crontab_master ".
    	" where 1 ";//GSCM_MANUAL_STATUS=1 "; GSCM_PHP_FILE
    if($fid!="")
		$sql.=" and GSCM_FILE_ID='$fid' ";
	if($filepath!="")
		$sql.=" and GSCM_PHP_FILE='$filepath' ";
	if($isManual!==null)
		$sql.=" and GSCM_MANUAL_STATUS='$isManual' ";
    $rs=execute_my_query($sql,'common_util.php',true,false,2);
    $array_cronmaster=/*. (string[int][int]) .*/ array();
    $k=0;
	while($row=mysqli_fetch_array($rs)){
		$array_cronmaster[$k][0]=$row['GSCM_FILE_ID'];
		$array_cronmaster[$k][1]=$row['GSCM_PURPOSE'];
		$array_cronmaster[$k][2]=$row['GSCM_PHP_FILE'];
		$array_cronmaster[$k][3]=$row['GSCM_MANUAL_STATUS'];
		$k++;
	}
	return $array_cronmaster;
}

/**
 * @param string $GAV_TABLE_NAME
 * @param string $GAV_EMP_ID
 * @param string $GAV_COLUMN_NAME
 * @param string $GAV_PREVIOUS_VALUE
 * @param string $GAV_UPDATED_VALUE
 *
 * @return void
 */
function Loginfo_audit_viewer($GAV_TABLE_NAME,$GAV_EMP_ID,$GAV_COLUMN_NAME,$GAV_PREVIOUS_VALUE,$GAV_UPDATED_VALUE){
    global $me;
    global $uid;
	$GAV_UPDATED_DATETIME=date('Y-m-d H:i:s');
	$GAV_UPDATED_BY=$uid;
	$GAV_FROM_PAGE=basename($me);
	$sql="insert into gft_audit_viewer_order (GAV_TABLE_NAME, GAV_EMP_ID ,GAV_COLUMN_NAME ,  GAV_PREVIOUS_VALUE ,  " .
		 "GAV_UPDATED_VALUE ,  GAV_UPDATED_DATETIME ,  GAV_UPDATED_BY ,  GAV_FROM_PAGE)  " .
	 	 "  values('$GAV_TABLE_NAME','$GAV_EMP_ID','$GAV_COLUMN_NAME','$GAV_PREVIOUS_VALUE','$GAV_UPDATED_VALUE'," .
	 	 " '$GAV_UPDATED_DATETIME','$GAV_UPDATED_BY','$GAV_FROM_PAGE') ";
	execute_my_query($sql,$GAV_FROM_PAGE,true,false,2);
}

/**
 * @param string $pincode
 * @param string $country
 *
 * @return int
 */
function identify_territory_from_pincode($pincode,$country='India'){
	if($country=='India'){
		$squery="select GPM_TERRITORY_ID from gft_pincode_master where gpm_pincode='$pincode' limit 1";
		$result=execute_my_query($squery,'common_util.php',true,false,2);
		$territory_id=0;
		if($sqd=mysqli_fetch_array($result)){
			$territory_id=$sqd['GPM_TERRITORY_ID'];
		}
		return $territory_id;
	}else{
		$territory_id=1;
		return $territory_id;
	}	
}

/**
 * @param string[int] $group_arr
 *
 * @return string[int][int]
 */
function get_contact_dtls_of_group($group_arr){
	$mobileno=/*. (string[int][int]) .*/ array();
	$group_arr_str=implode(',',$group_arr);
	$query_gr="select distinct(a.gem_emp_id) eid,gem_emp_name,gem_mobile from gft_emp_master a" .
			" left join gft_role_group_master rg on (grg_role_id=gem_role_id)" .
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id)" .
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id)" .
			" where ggm_group_id in ($group_arr_str) and a.gem_status='A' ";
	$result_gr=execute_my_query($query_gr,'common_util.php',true,false,2);
	$i=0;
	while($qdata=mysqli_fetch_array($result_gr)){
		$mobileno[$i][0]=$qdata['gem_mobile'];
		$mobileno[$i][1]=$qdata['eid'];
		$i++;			
	}
	return $mobileno;		
}

/**
 * @param string $cp_user_id
 *
 * @return string[int][int]
 */

function get_cp_employee_list($cp_user_id){
	$query_select_sales_incharge="select gem_emp_id,gem_emp_name from gft_emp_master " .
			"join gft_emp_reporting ger on (ger_emp_id=gem_emp_id and ger_reporting_empid=$cp_user_id) " .
			"where gem_status='A' ";

	$result_sales_incharge=execute_my_query($query_select_sales_incharge);
	$rsc_list= /*. (string[int][int]) .*/ array();
	if(mysqli_num_rows($result_sales_incharge)>=1){
		$rsc_index=0;
			
		while($rsc=mysqli_fetch_array($result_sales_incharge)){
			$rsc_list[$rsc_index][0]=$rsc['gem_emp_id'];
			$rsc_list[$rsc_index][1]=$rsc['gem_emp_name'];
			$rsc_index++;
		}
	}
	return $rsc_list;
}

/**
 * @param string $group_code
 * @param string $sms_content
 * @param string $category
 * @param string $sender_emp_id
 * @param boolean $get_list
 * 
 * @return string[int][int] 
 */
function sending_sms_to_group($group_code,$sms_content,$category,$sender_emp_id,$get_list=false){
	$query_gr="select distinct(a.gem_emp_id) eid,gem_emp_name,gem_mobile from gft_emp_master a" .
			" left join gft_role_group_master rg on (grg_role_id=gem_role_id)" .
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id)" .
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id)" .
			" where ggm_group_id in ($group_code) and a.gem_status='A'";
	//This condition is added to avoid send sms to Asian Paint
	if($group_code==13){
		$asian_cp_emp	=	get_cp_employee_list(7615);
		$asian_emp_list	=	array();
		$asian_emp_list[]=	7615;
		for($i=0;$i<count($asian_cp_emp);$i++){
			$asian_emp_list[]=	$asian_cp_emp[$i][0];
		}
		$query_gr	.=	" AND a.gem_emp_id not in(".implode(',', $asian_emp_list).")";
	}
	$result_gr=execute_my_query($query_gr,'common_util.php',true,false,2);
	$employee_list = /*. (string[int][int]) .*/array();
	$k=0;
	while($qdata=mysqli_fetch_array($result_gr)){
		$employee_id=$qdata['eid'];
		$employee_name = $qdata['gem_emp_name'];
		if($get_list) {
			$employee_list[$k][0]=$employee_id;
			$employee_list[$k][1]=$employee_name;
			$k++;
		}else{
			$mobileno=$qdata['gem_mobile'];
			$temp_sms_content = str_replace("{{Employee_Name}}", $employee_name, $sms_content);
			entry_sending_sms($mobileno,$temp_sms_content,$category,$employee_id,0,$sender_emp_id);
		}
	}
	return $employee_list;  //returns the array if function call for getting employees. No use of this return while sending sms
}//end of function

/**
 * @param string $result_xml
 * @param int $category
 * 
 * @return void
 */
function sms_xml_content_parser($result_xml,$category=0){
	$send_status=get_sms_config_info($category);
 	if($send_status=='A'){
		if($result_xml!=""){
 			$parsed_xml = simplexml_load_string($result_xml);
			foreach($parsed_xml->smsdetail as $xml_array){
				$MOBILENO=$CONTENT=$EMPID="";
			    $MOBILENO=trim($xml_array->mobileno); //receiver
			    $CONTENT=$xml_array->message; //
			    $EMPID=trim($xml_array->empid); //receiver
			    $CUSTOMERID=trim($xml_array->customerid); //receiver
			    $SENDER=trim($xml_array->senderid); //sender
	       		if($CUSTOMERID!=""){
					entry_sending_sms_to_customer($MOBILENO,$CONTENT,$category,$CUSTOMERID,0,$SENDER);
				}else{
				    entry_sending_sms($MOBILENO,$CONTENT,$category,$EMPID,0,$SENDER); 
				}
			}//end of for
		}//end of if $result_xml		
	}//not blocked
}

/**
 * @param string $mobile
 * 
 * @return string[int]
 */
function get_exec_all_details($mobile){
	//Extract Mobile no 
	$mobile=trim($mobile);
    $mobile=substr($mobile,-10);
	$query_emp="select GEM_EMP_ID,GEM_EMP_NAME,GEM_ROLE_ID,GEM_MOBILE,GET_TERRITORY_ID " .
	 	" from gft_emp_master " .
	 	" left join gft_emp_territory_dtl td on (GET_EMP_ID=GEM_EMP_ID and GET_STATUS='A' and GET_WORK_AREA_TYPE='2')" .
	 	" where GEM_OFFICE_EMPID!='0' and  " .
	 	" GEM_MOBILE like '%$mobile%' and GEM_STATUS='A' ";
	$query_emp.=" order by 2";
	$result_emp=execute_my_query($query_emp,'common_util.php',true,false,2);
	$i=0;
	$emp_list=/*. (string[int]) .*/ array();
	while($row=mysqli_fetch_array($result_emp)){
		$emp_list=array($row['GEM_EMP_ID'],$row['GEM_EMP_NAME'],$row['GEM_ROLE_ID'],$row['GEM_MOBILE'],$row['GET_TERRITORY_ID']);
	}
    return $emp_list;
}

/**
 * @param string $sender_mobileno
 *
 * @return string
 */
function get_custid_details($sender_mobileno){
	$sender_mobileno=substr(trim($sender_mobileno),-10);
	/*Is numberic check because some unwanted sms like ringtones,aircell...coming it match with some email id*/
	if(is_numeric($sender_mobileno)){
		$query="SELECT GCC_LEAD_CODE FROM gft_customer_contact_dtl WHERE " .
				"trim(GCC_CONTACT_NO) like '%$sender_mobileno' ";
		$result=execute_my_query($query,'common_util.php',true,false,2);
		$cust_id='';
		if($qd=mysqli_fetch_array($result)){
			$cust_id=$qd['GCC_LEAD_CODE'];
		}
		return $cust_id;
	}
	return '';
}    

/**
 * @param boolean $upto_current
 *
 * @return string[int][int]
 */
function get_financial_yr_list($upto_current=false){
	$query_get_financial_yr="select distinct(financial_year) from financial_year " .
			($upto_current?" WHERE year_val<=year(now()) AND month_val<=month(now()) ":"");
	$result=execute_my_query($query_get_financial_yr,'common_util.php',true,false,2);
	$sl=0;
	$financial_yr_list=/*. (string[int][int]) .*/ array();
	while($qd=mysqli_fetch_array($result)){
	$financial_yr_list[$sl][0]=$financial_yr_list[$sl][1]=$qd[0];
	$sl++;
	}
	return $financial_yr_list;
}

/**
 * @param string $bh_id
 * @param boolean $bh_all_reports
 * @param int $bh_tab_id
 * 
 * @return string[int][int]
 */
function get_bh_report_list($bh_id=null,$bh_all_reports=true,$bh_tab_id=0){
	global $uid ,$non_employee_group;
//	show_my_alert_msg("bh_tab_id-".$bh_tab_id);
	$query_bh_report="select gbh_id, gbh_header,GBH_LINK,gbh_tab_id,gbh_tab_order_by from gft_bh_summary_hdr where (1) ".
	($bh_id!=null?" and gbh_id=$bh_id ":""). 
	($bh_all_reports!=true ?" and gbh_status=true ":""). 
	($bh_tab_id!=0?" and gbh_tab_id=$bh_tab_id ":"and (gbh_tab_id is null or gbh_tab_id=0) ").
	(is_authorized_group_list($uid ,$non_employee_group)? "and gbh_cp_list=true ":"").
	($bh_tab_id!=0?" order by gbh_tab_order_by ":" order by gbh_id ");
	$result=execute_my_query($query_bh_report,'common_util.php',true,false,2);
	$sl=1;
	$bh_report=/*. (string[int][int]) .*/ array();
	while($qd=mysqli_fetch_array($result)){
		$bh_report[$sl][0]=$qd['gbh_id'];
		$bh_report[$sl][1]=$qd['gbh_header'];
		$bh_report[$sl][2]=$qd['GBH_LINK'];
		$bh_report[$sl][3]=$qd['gbh_tab_id'];
		$bh_report[$sl][4]=$qd['gbh_tab_order_by'];
		$sl++;
	}
	return $bh_report;
}

/**
 * @param string $TensText
 *
 * @return string
 */
function GetTens($TensText){
	$Result='';
	if($TensText!=''){
		if(substr($TensText,0,1)==1){
			switch ($TensText){
				case "10": $Result = "Ten";break;
				case "11": $Result = "Eleven";break;
				case "12": $Result = "Twelve";break;
				case "13": $Result = "Thirteen";break;
				case "14": $Result = "Fourteen";break;
				case "15": $Result = "Fifteen";break;
				case "16": $Result = "Sixteen";break;
				case "17": $Result = "Seventeen";break;
				case "18": $Result = "Eighteen";break;
				case "19": $Result = "Nineteen";break;
				default:
					//Do nothing
					break;
			}
		}else{
			$t=(int)substr($TensText,0,1);
			switch($t){
				case 2: $Result = "Twenty";break;
				case 3: $Result = "Thirty";break;
				case 4: $Result = "Forty";break;
				case 5: $Result = "Fifty";break;
				case 6: $Result = "Sixty";break;
				case 7: $Result = "Seventy";break;
				case 8: $Result = "Eighty";break;
				case 9: $Result = "Ninety";break;
				default:
					//Do nothing
					break;
			}
			$digitStr=GetDigit((int)substr($TensText,-1));
			if ($digitStr != ''){
				$Result = $Result.' '.$digitStr;
			}
		}
	}else{
		$Result="";
	}
	return $Result;
}

/**
 * @param int $Digit
 *
 * @return string
 */
function GetDigit($Digit){
	switch($Digit){
		case 1:
			$GetDigit = "One";
			break;
		case 2:
			$GetDigit = "Two";
			break;
		case 3:
			$GetDigit = "Three";
			break;
		case 4:
			$GetDigit = "Four";
			break;
		case 5:
			$GetDigit = "Five";
			break;
		case 6:
			$GetDigit = "Six";
			break;
		case 7:
			$GetDigit = "Seven";
			break;
		case 8:
			$GetDigit = "Eight";
			break;
		case 9:
			$GetDigit = "Nine";
			break;
		default:
			$GetDigit='';	
	}
	return $GetDigit;
}

/**
 * @param float $MyNumberInput
 * @param boolean $is_usd
 *
 * @return string
 */
function SpellNumber($MyNumberInput,$is_usd=false){
	$MyNumber=''.$MyNumberInput;
    $Place[1] = "";
    $Place[2] = " Thousand ";
    $Place[3] = " Lakhs " ;
    $Place[4] = " Crores ";
    $Place[5] = " Trillion ";
    $MyNumber = trim($MyNumber);
    if($MyNumberInput > 999999999.99 ) {
    	$SpellNumber = "Digit excced Maximum limit";
    	//return 0;
    	return '';
    }
    if($MyNumberInput <0){
    	$SpellNumber = "Digit excced Maximum limit";
    	//return 1;
    	return '';
    }
    if($MyNumberInput == 0){
    	$SpellNumber = " Zero ";
    	return $SpellNumber;
    }
    $no=explode('.',$MyNumber);
    $MyNumber=$no[0];
   
    if(isset($no[1]) and $no[1] !='' and $no[1]!=0) {
        $Paisas = GetTens(substr($no[1].'00',0,2));
    }else{
    	$Paisas="";
    }    
    $Count = 1;
    $Rupees='';
    while($MyNumber!=""){
		if ($Count >= 2) {
			$iTemp = substr($MyNumber, -2);
		}else{
			if(strlen($MyNumber) == 2){
				$iTemp = substr($MyNumber, -2);
			}else if (strlen($MyNumber) == 1){
				$iTemp = substr($MyNumber, -1);
			}else{
				$iTemp = substr($MyNumber, -3);
			}
       }
       
       if($iTemp > 99) {
            $iTemp = substr($MyNumber, -3);
            $temp = GetHundreds($iTemp,$Count);
       }else if ($iTemp < 100 and $iTemp > 9 ){
            $iTemp = substr($MyNumber, -2);
            //$temp = GetTens($iTemp,$Count);
            $temp = GetTens($iTemp);
       }else if ($iTemp < 10 ){
            $iTemp = substr($MyNumber, -2);
            //$temp = GetDigit($iTemp,$Count);
            $temp = GetDigit($iTemp);
       }else{
	       $temp='';
       }

       if($temp !="" and isset($Place[$Count]) ){
         $Rupees = $temp . $Place[$Count] . $Rupees;
       }
       if ($Count == 2) {
            if(strlen($MyNumber) == 1){
             $MyNumber = "";
            }else{
            	$MyNumber = substr($MyNumber, 0,strlen($MyNumber) - 2);
            }
       }elseif ($Count == 3){
            if (strlen($MyNumber) >= 3){
                 $MyNumber = substr($MyNumber,0, strlen($MyNumber) - 2);
       		}else{
                $MyNumber = "";
            }
       }else if( $Count == 4 ){
          $MyNumber = "";
       }else{
            if(strlen($MyNumber) <= 2){
                $MyNumber = "";
            }else{
                $MyNumber = substr($MyNumber, 0,(strlen($MyNumber)-3));
       		}
       }
	$Count = $Count + 1;
    }
    if($Paisas!=""){
           $Paisas = "and ".$Paisas.($is_usd?' cents ':" Paise ");
    }
    $SpellNumber = $Rupees .($is_usd?" ":" Rupees "). $Paisas . "Only";
    $SpellNumber = trim($SpellNumber);
    return $SpellNumber;
}

/**
 * @param int $MyNumber
 * @param int $Count
 *
 * @return string
 */
function GetHundreds($MyNumber,$Count){
	if($MyNumber== 0 )
    	return "";
    if (substr($MyNumber, 0, 1) !="0" and $Count > 2) {
     	$Result = GetDigit(substr($MyNumber, 0, 2)) ." Lac ";
    }else if (substr($MyNumber, 0, 1) !="0" and $Count > 1) {
    	$Result = GetDigit(substr($MyNumber, 0, 2)) ." Thosand ";
    } else{
     	$Result = GetDigit(substr($MyNumber, 0, 1)) . " Hundred ";
    }
    
    if(substr($MyNumber, -2) !="0" and substr($MyNumber, -2) >= 9 ){
        $Result = $Result . GetTens(substr($MyNumber, -2));
    }else if (substr($MyNumber, -1) !="0"){
        $Result = $Result . GetDigit(substr($MyNumber, -1));
    }
	$GetHundreds = $Result;
	return $Result;
}

/**
 * @param string $lead_code
 *
 * @return string
 */
function get_name_of_customer($lead_code){
    if($lead_code!=''){
		$query="select concat(glh_cust_name,'-',ifnull(glh_cust_streetaddr2,'')) from gft_lead_hdr " .
				" where GLH_LEAD_CODE='$lead_code' ";
		$result=execute_my_query($query,'common_util.php',true,false,2);
		if($qd=mysqli_fetch_array($result)){
			return $cust_name=$qd[0];
		}
    }
    return '';
}

/**
 * @param string $lead_code
 *
 * @return string
 */
function get_email_addr_customer($lead_code){
	$query_cust="select group_concat(gcc_contact_no) mail_id from gft_customer_contact_dtl where " .
			" gcc_contact_type=4 and gcc_lead_code='$lead_code' and gcc_designation in (1,2,3,4) ";
	$result_cust=execute_my_query($query_cust,'common_util.php',true,false,2);
	$qdata_cust=mysqli_fetch_array($result_cust);
	$email=$qdata_cust['mail_id'];
	return $email;
}

/**
 * @param string $lead_code
 * 
 * @return string
 */
function get_mobil_no_customer($lead_code){
	$query_cust="select group_concat(gcc_contact_no) mobil_no from gft_customer_contact_dtl where " .
			" gcc_contact_type=1 and gcc_lead_code='$lead_code' ";
	$result_cust=execute_my_query($query_cust,'common_util.php',true,false,2);
	$qdata_cust=mysqli_fetch_array($result_cust);
	$mobil_no=$qdata_cust['mobil_no'];
	return $mobil_no;
}

/**
 * @param string $map_id
 * @param string $list_fk
 * 
 * @return string[int][int]
 */
function  get_emailid_master($map_id=null,$list_fk=null){
 	$query="select GEM_EMAILID as mailid from gft_emailid_master where GEM_USE_STATUS='A' ";
 	if($list_fk!=null)$query.=" and GEM_EMAIL_GROUP_FK=$list_fk ";
 	if($map_id!=null){ $query=" select GBM_EMAILID as mailid FROM  gft_business_map_master where gbm_map_id='$map_id'";}
 	$result=execute_my_query($query);
 	$i=0;
 	$mailid_arr=/*. (string[int][int]) .*/ array();
 	while($qdata=mysqli_fetch_array($result)){
 	     $mailid_arr[$i][0]=$qdata['mailid'];
 	     $mailid_arr[$i][1]=$qdata['mailid'];
 	     $i++;
 	}
 	return $mailid_arr;
}
         				
/** 
 * @param string $eid
 * @param string $email_id
 * @param string $name
 * @param string $uid
 * @param string $uname
 * @param string $role_id
 * @param string $rep_to
 * @param boolean $is_new_comer
 * @param string $passwd
 * @param string $title
 * @param boolean $is_intern
 * @param string $mobile_mac_id
 * @param string $laptop_mac_id
 * @param string $laptop_harddisk_id
 * @param string $refid
 * @return void
 */
function send_mail_to_sysadmin_to_createmailid($eid,$email_id,$name,$uid,$uname,$role_id,$rep_to,$is_new_comer,$passwd,$title,$is_intern=false,$mobile_mac_id='',$laptop_mac_id='',$laptop_harddisk_id='',$refid=''){
	global $attachment_file_tosend,$cc;

	$rmanager_name='';

	$email_from=get_samee_const('ADMIN_TEAM_MAIL_ID');
	$email_to=get_samee_const('SYSADMIN_MAIL_ID');
	if($role_id!=''){ $role_name=get_two_dimensinal_array_from_query('select GRM_ROLE_ID, GRM_ROLE_DESC from gft_role_master where GRM_ROLE_ID='.$role_id,'GRM_ROLE_ID', 'GRM_ROLE_DESC');}
	if($rep_to!=''){ $rmanager_name=get_name($rep_to);}
	//$role=$role_name[0][1];
	if($is_new_comer==false){
		 $subject="Check Email ID exists";
		 $sysadmin_msg="Dear Team,<br> Please check Email id exists for <br>" ;
	}else {
		$subject="Create Email ID";
		$sysadmin_msg="Dear Team,<br><br> Please create Email id for <br><br>" ;
	}
	$mac_messages="";
	if( ($mobile_mac_id!="") || ($laptop_mac_id!="") || ($laptop_harddisk_id!="") ){
		$mac_messages.="<br><b>Add the following services</b><br><br><table border=1>";
		if($mobile_mac_id!=""){
			$mac_messages .= "<tr><td>Mobile mac address </td><td>".strtr($mobile_mac_id, array (':' => '','-'=> ''))."</td></tr>";
		}
		if($laptop_mac_id!=""){
			$mac_messages.="<tr><td>laptop mac address  </td><td>".strtr($laptop_mac_id, array (':' => '','-'=> ''))."</td></tr>";
		}
		if($laptop_harddisk_id!=""){
			$mac_messages.="<tr><td>Laptop HardDisk Id</td><td>$laptop_harddisk_id</td></tr>";
		}
		$mac_messages .= "</table>";
	}
	
	$msg ="<table border=1>" .
			"<tr><td>Employee id</td><td> $eid</tr>" .
			"<tr><td>Name</td><td>$name </td></tr>";
	$sys_ref_msg = "<table border=1>" .
            	   	"<tr><td>SAM Reference ID</td><td> $eid</tr>" .
            	   	"<tr><td>Employee ID:</td><td>$refid </tr>" .
            	   	"<tr><td>Name</td><td>$name </td></tr>";
	$reporting_msg ="<tr><td>Designation</td><td>$title</td></tr>" .
			"<tr><td>Reporting Manager </td><td>$rmanager_name</td></tr>".
			"<tr><td>Email id </td><td>$email_id </td></tr>";
	$msgpass="<tr><td>SAM User Name</td><td>".(string)str_replace('@'.get_samee_const("OFFICEAL_MAIL_DOMAIN"),"",$email_id)."</td></tr> ".
			 "<tr><td>SAM Password</td><td>$passwd</td></tr></table><br>".
	         "$mac_messages";
	          
	$msgfooter="<br><br><i>This is automated message from SAM</i>";
	$sysadmin_msg.=$sys_ref_msg.$reporting_msg.$msgpass;
	$reply_to=get_samee_const('HR_MAIL_ID');
	send_mail_from_sam($category=25,$email_from,$email_to,$subject,$sysadmin_msg,
	$attachment_file_tosend,get_email_addr($uid),$content_type=true,$reply_to,$from_page="Employee Master",false);
	if($is_intern==true){
		$is_new_comer=false;		
	}
	if($is_new_comer==true){
		$email_to=get_samee_const('NEWJOINER_MAIL_TO');
		$subject =" New Joiner";
		$msg .= "$reporting_msg</table>";
		$new_comer_msg=" Dear All,<br><br> Lets welcome {$name} to GoFrugal family. Wishing him/her a very successful endeavour. <br><br>".$msg . $msgfooter;	
		send_mail_from_sam($category=25,$email_from,$email_to,$subject,$new_comer_msg,
		$attachment_file_tosend,$cc,$content_type=true,$reply_to=null,$from_page="Employee Master",false);
	}
}
/**
 * @param string $uid
 * @param string $sysadmin_msg
 *
 * @return void
 */

function send_mail_to_sysadmin_update_mac_add($uid,$sysadmin_msg){
	global $attachment_file_tosend;
	$subject	=	"Required MAC address update";
	$email_from	=	get_email_addr($uid);
	$email_to	=	get_samee_const('SYSADMIN_MAIL_ID');
	$emp_name	=	get_emp_name($uid);
	$emp_details=	"<tr><td>Employee ID  </td><td>".$uid."</td></tr>".
			"<tr><td>Employee Name  </td><td>".$emp_name."</td></tr>";
	$sysadmin_msg=	"Dear Team,<br><br> The mac address has been updated now,Kindly map the new MAC address to access our office Wi-Fi<br><br>".
			"<table border=1>".$emp_details.$sysadmin_msg."<table>";
	send_mail_from_sam($category=25,$email_from,$email_to,$subject,$sysadmin_msg,
			$attachment_file_tosend,$email_from,$content_type=true,$email_from,$from_page="Personal Details",false);
}
/**
 * @param boolean $parentVar
 *
 * @return string[int][int]
 */
function  get_function_list($parentVar=false){
	$query=" select GFM_FID, GFM_FDESCRIPTION from gft_function_master " .
		($parentVar? " where GFM_FPARENTID=1 ":" where GFM_FID!=GFM_FPARENTID "); 
	return get_two_dimensinal_array_from_query($query,'GFM_FID','GFM_FDESCRIPTION','GFM_FID');
}

/**
 * @param string $parentVar
 * 
 * @return string[int][int]
 */
function  get_print_profile_modul_list($parentVar){
	$query=" SELECT GPM_MID,GPM_MPARENTID,GPM_MDESCRIPTION FROM gft_pp_module_master where (1)" .
			($parentVar==0?"  ":" and GPM_MID!=GPM_MPARENTID and GPM_MPARENTID='$parentVar'");
	return get_two_dimensinal_array_from_query($query,'GPM_MID','GPM_MDESCRIPTION','GPM_MID');
}

/**
 * @param string $emp_id
 *
 * @return string
 */
function get_cp_lead_code_for_eid($emp_id){
	$query="SELECT GLEM_LEADCODE FROM gft_leadcode_emp_map WHERE GLEM_EMP_ID='$emp_id' ";
	$result=execute_my_query($query,'common_util.php',true,false,2);
	$qdata=mysqli_fetch_array($result);
	$lead_code=$qdata['GLEM_LEADCODE'];
	return $lead_code;
}
/**
 * @param string $cp_lead_code
 *
 * @return string
 */
function get_downstream_partner_emp_ids($cp_lead_code){
	$emp_ids	=	"";
	$result_emp_ids = execute_my_query("select GROUP_CONCAT(GLEM_EMP_ID) EMP_IDS from gft_leadcode_emp_map".
										" inner join gft_emp_master on(GEM_EMP_ID=GLEM_EMP_ID AND GEM_ROLE_ID IN(79,80,81))".
										" where GLEM_LEADCODE='$cp_lead_code' AND GEM_STATUS='A' GROUP BY GLEM_LEADCODE");
	if(mysqli_num_rows($result_emp_ids)>0 && $row=mysqli_fetch_array($result_emp_ids)){
		$emp_ids = $row['EMP_IDS'];
	}
	return $emp_ids;
}
/**
 * @param string $lead_code
 *
 * @return string[mixed]
 */
function get_cp_all_emp_id($lead_code){
    $query="SELECT GLEM_EMP_ID FROM gft_leadcode_emp_map WHERE GLEM_LEADCODE='$lead_code' ";
    $result=execute_my_query($query,'common_util.php',true,false,2);
    $GLEM_EMP_ID = array();
    while($row1=mysqli_fetch_array($result)){
        $GLEM_EMP_ID[] = $row1['GLEM_EMP_ID'];
    }
    return $GLEM_EMP_ID;
}
/**
 * @param string $lead_code
 * 
 * @return int
 */
function get_cp_emp_id_for_leadcode($lead_code){
	$query="SELECT CGI_EMP_ID FROM gft_cp_info WHERE CGI_LEAD_CODE='$lead_code' ";
	$result=execute_my_query($query,'common_util.php',true,false,2);
	if($qdata=mysqli_fetch_array($result)){
		return (int)$qdata['CGI_EMP_ID'];
	}
	return 0;
}

/**
 * @param string $emp_id
 * @param string $cust_id
 * 
 * @return string
 */
function get_cp_incharge($emp_id=null,$cust_id=null){
	$query="SELECT GEM_EMAIL FROM gft_cp_info 
	inner join gft_emp_master em on (gem_emp_id=cgi_incharge_emp_id and gem_status='A') WHERE 1 ";
	if($emp_id!=null){	
		$query.=" and CGI_EMP_ID='$emp_id' "; 
	}else if($cust_id!=null){	
		$query.=" and CGI_LEAD_CODE='$cust_id' "; 
	}else{
		return "";
	}
	$result=execute_my_query($query,'common_util.php',true,false,2);
	$qdata=mysqli_fetch_array($result);
	$GEM_EMAIL=$qdata['GEM_EMAIL'];
	return $GEM_EMAIL;
	
}

/**
 * @param string $emp_id
 * @param string $cust_id
 * 
 * @return string
 */
function get_cp_relational_incharge($emp_id=null,$cust_id=null){
	$query="SELECT GEM_EMAIL FROM gft_cp_info 
	inner join gft_emp_master em on (gem_emp_id=CGI_RELATIONSHIP_MANAGER and gem_status='A') WHERE 1 ";
	if($emp_id!=null){	
		$query.=" and CGI_EMP_ID='$emp_id' "; 
	}else if($cust_id!=null){	
		$query.=" and CGI_LEAD_CODE='$cust_id' "; 
	}else{ 
		return "";
	}
	$result=execute_my_query($query,'common_util.php',true,false,2);
	$qdata=mysqli_fetch_array($result);
	$GEM_EMAIL=$qdata['GEM_EMAIL'];
	return $GEM_EMAIL;
	
}

/**
 * @param string $cp_lead_code
 *
 * @return string
 */
function get_role_from_lead_code($cp_lead_code){
	$result=execute_my_query("SELECT gem_role_id FROM gft_cp_info join gft_emp_master on (CGI_EMP_ID=gem_emp_id) WHERE CGI_LEAD_CODE=$cp_lead_code ");
	if($data=mysqli_fetch_array($result)){
		return $data['gem_role_id'];
	}else{
		return null;
	}
}

/**
 * @param string $array_to_be_block
 *
 * @return void
 */
function block_this_page($array_to_be_block){
	global $uid;
	$user_id=$uid;
	if(in_array($user_id,$array_to_be_block)){
		$me=$_SERVER['PHP_SELF'];
		echo("<script type=\"text/javascript\">location.href='".$me."';</script>");
		//return true;
	}
}

	
/**
 *@return string[int]
 */
function fab_product_avail_list(){
	$query="SELECT distinct(gbr_product) product FROM gft_bvp_relation";
	$result=execute_my_query($query,'common_util.php',true,false,2);
	$i=1;
	$bvp_product_list=/*. (string[int]) .*/ array();
	while($data=mysqli_fetch_array($result)){
		$bvp_product_list[$i]=$data['product'];
		$i++;
	}
	return $bvp_product_list;
}

/**
 * @param string $product_sync_code
 *
 * @return string
 */
function get_sam_vertical_code_for($product_sync_code=null){
	$vcode_squery="SELECT GTM_VERTICAL_CODE,GTM_VERTICAL_NAME,GTM_PROD_SYNC_CODE FROM gft_vertical_master  where 1 " ;
	$vcode_squery.=" and  GTM_PROD_SYNC_CODE ='$product_sync_code' ";
	$result=execute_my_query($vcode_squery,'common_util.php',true,false,2);
	$vcode='';
	if($qdata_vs=mysqli_fetch_array($result)){
		$vabr=$qdata_vs['GTM_VERTICAL_NAME'];
		$vcode=$qdata_vs['GTM_VERTICAL_CODE'];
	}
	return $vcode;
}

/**
 * @param string $vertical
 * 
 * @return string
 */
function get_vertical_name_for($vertical){
	$vcode_squery="SELECT GTM_VERTICAL_CODE,GTM_VERTICAL_NAME,GTM_PROD_SYNC_CODE FROM gft_vertical_master  " .
			"where GTM_VERTICAL_CODE ='$vertical' ";
	$result=execute_my_query($vcode_squery,'common_util.php',true,false,2);
	$vabr='';
	if($qdata_vs=mysqli_fetch_array($result)){
		$vabr=$qdata_vs['GTM_VERTICAL_NAME'];
	}
	return $vabr;
}

/**
 * @param int $business_type
 * @param int $pvertical
 * @param string $product
 *
 * @return string[int]
 */
function get_fab_list_oftype($business_type,$pvertical,$product){
	$query= "select gfdm_id, pf.gfm_order,cf.gfm_order,gfdm_function_code,gfdm_subfunction_code FROM (gft_fab_description_master, gft_fab_product_dtl gp)" .
		" left join gft_fab_business_dtl g on (gfdm_id=g.gfbd_fab_id)" .
		" left join gft_fab_vertical_dtl gv on (gfdm_id=gv.gfvd_fab_id)" .
		" join gft_function_master pf on (pf.GFM_FID=gfdm_function_code) " .
		" join gft_function_master cf on (cf.GFM_FID=gfdm_subfunction_code) " .
		" where gp.gfpd_fab_id=gfdm_id   " .
		($business_type!=0?" and g.gfbd_business_code ='$business_type' ":"").
		($pvertical!=0 ?" AND gv.gfvd_vertical_code='$pvertical' ":"").
		" AND gp.gfpd_product_code ='$product' " .
		" group by gfdm_id order by pf.gfm_order,cf.gfm_order, gfdm_id ";
	//echo $query;
	$newresult=execute_my_query($query);
	$fab=/*. (string[int]) .*/ array();
	if($newresult){
		$i=1;
		while($fdata=mysqli_fetch_array($newresult)){
			$fab[$i]=$fdata['gfdm_id'];
			$i++;
		}
	}
	return $fab;
}

/** 
 * @param string[int] $fab
 *
 * @return string
 */
function get_fab_preview_content($fab){
	global $functionprop,$pagebreak;
	//global $colladraCUST_NAMEl_name;
	global $vertical_name, $business_name,$product_title;
	global $attach_path;

	$fab_image = /*. (string[int]) .*/ array();
	$counter=count($fab);
	for($i=1;$i<=count($fab);$i++){
		$result=execute_my_query(" SELECT gfdm_desc, gfdm_image,gfdm_function_code, gfdm_subfunction_code,gfdm_tag_type " .
				" FROM gft_fab_description_master WHERE gfdm_id=".$fab[$i]);
		if($predata=mysqli_fetch_array($result)){
			$fab_desc[$i]=$predata['gfdm_desc'];
			$fab_image[$i]=$predata['gfdm_image'];
			$pfunctionval[$i]=$predata['gfdm_function_code'];
			$functionval[$i]=$predata['gfdm_subfunction_code'];
			$tag_type[$i]=$predata['gfdm_tag_type'];
			$rank[$i]=$i;
		}
	}
	//$counter=count($fab);
	$ul=false;
	//$colladral_name1=str_replace('-',' ',$colladral_name);
	$content_title='<table width="680px" height=\"720px\" border=0>' .
			'<tr><td height="75px"></td></tr>' .
			'<tr><td height="300px"><H1 align="center" style= "line-height: 120%">GoFrugal RayMedi Solution <Br /> For <BR />'
			.$vertical_name.' '.$business_name.'</H1></td></tr>' .
			'<tr><td height="300px"><H1 align="center" style= "line-height: 120%">Product Presented<BR>' .
			''.$product_title.' Professional Edition</H1></td></tr></table>' ;
	//'<link rel="stylesheet" type="text/css" href="../../sales_server/CSS/style.css"/>'.
	$contenthead='<table width="700" cellspacing="0" cellpadding="2" border="0">' .
				'<tr><td colspan=\"6\" width="680px" height=\"950px\" valign="top">' .
				'<table border=1 width="680px" height=\"720px\"><tr><td>' .
				$content_title .
				'<table border=0 width=\"100%\" height="190px" align="center">'.
				'<tr><td align="center" valign=top width="172px" height="93px" style="background-image:url(\'../image/twikiRobot46x50.gif\'); background-position: 45% 0%; background-repeat:no-repeat;"> </td></tr>' .
				'<tr><td width=\"100%\" align=center rowrap>'.get_samee_const('GFT_ADDRESS_PDF').'</td>' .
				'</tr></table>' .
				'</td></tr></table></td></tr>';
	$contenthead.="<tr><td colspan=\"6\">";
	$functionval[0]=1;
	$pfunctionval[0]=1;
	$level1=0;
	$level2=0;
	$content="";
	$contentitle="<font size=4><b>Table of Contents</b></font><BR>";
	for($i=1;$i<=$counter;$i++){
		if($pfunctionval[$i]!=$pfunctionval[($i-1)]){
			if($ul==true){
		 		$content.="</ul>";
		 		$ul=false;
			}
			$level1++;
			$level2=0;
			$content.="<font size=4><b><a name=\"$level1\" id=\"$level1\" style=\"color: #000000; text-decoration: none;\">".$level1." ". $functionprop[($pfunctionval[$i])]."</a></b></font><br />";
			$contentitle.="<font size=4><b><a href=\"#$level1\" style=\"color: #000000; text-decoration: none;\" >".$level1." ". $functionprop[($pfunctionval[$i])]."</a></b></font><br />";
			//$content.="<H2 VALUE=\"#\"><a name=\"$level1\" id=\"$level1\">".$level1." . ". $functionprop[($pfunctionval[$i])]."</a></H2>";
			//$contentitle.="<H2 VALUE=\"#\"><a href=\"#$level1\">".$level1." . ". $functionprop[($pfunctionval[$i])]."</a></H2>";
			$ul=false;
		}
		if($pfunctionval[$i] !=$functionval[$i] and  $functionval[$i-1]!=$functionval[$i]){
			if($ul==true){
		 		$content.="</ul>";
		 		$ul=false;
			}
			$level2++;
			$content.= "<font size=3><b><a name=\"$level1.$level2\" id=\"$level1.$level2\" style=\"color: #000000; text-decoration: none;\">".$level1.".".$level2." ". $functionprop[$functionval[$i]]."</a></b></font>";
			$contentitle.="   <font size=3><b><a href=\"#$level1.$level2\" style=\"color: #000000; text-decoration: none;\">".$level1.".".$level2." ". $functionprop[$functionval[$i]]."</a></b></font><br />";
			//$content.= "<H3 VALUE=\"#\">".$level1.".".$level2." . ". $functionprop[$functionval[$i]]."</H3";
			//$contentitle.="<H3 VALUE=\"#\">".$level1.".".$level2." . ". $functionprop[$functionval[$i]]."</H3>";
		}
		if($i==1 and $tag_type[$i]=='li'){
			$content.="<ul>";
			$ul=true;
		}else if($ul==false and $tag_type[$i]=='li'){
			$content.="<ul>";
			$ul=true;
		}
		if($tag_type[$i]=='p'){
			if(!isset($tag_type[($i-1)])){
				//Do nothing as of now...
			}else if($tag_type[($i-1)]=='li'){
				$content.="</ul>";
			}
			$ul=false;
		}
		if($i==1 and $functionval[$i]==1 and $pfunctionval[$i]==1){
			$level1++;
			$content.= "<font size=4><b><a name=\"$level1\" id=\"$level1\" style=\"color: #000000; text-decoration: none;\">".$level1." Introduction</a></b></font>";
			$contentitle.="<font size=4><b><a href=\"#$level1\" style=\"color: #000000; text-decoration: none;\">".$level1." Introduction</a></b></font><br />";			
			//$content.= "<H2 VALUE=\"#\"><a name=\"$level1\" id=\"$level1\">".$level1." . Introduction</a></H2>";
			//$contentitle.="<H2 VALUE=\"#\"><a href=\"#$level1\">".$level1.". Introduction</a></H2>";
		}
		$content.="<".$tag_type[$i]." align=\"justify\">".$fab_desc[$i]."</".$tag_type[$i].">";
		if(isset($fab_image[$i]) and $fab_image[$i]!=''){
			$content.="<img src=\"$attach_path/image/$fab_image[$i]\"/>";
		}
		if($i==count($fab) and $ul==true){
		 	$content.="</ul>";
		 	$ul=false;
		}
	}
	$note="<p><font size=1>Note: RayMedi software comes in different editions features will vary as per editions." .
			" If local language / touchscreen are needed, they are supported only in RayMedi RPOS6.5." .
			" This document is for information purpose only." .
			" GoFrugal Technologies makes no warranties, express, implied or statutory as to the information in this document." .
			" RayMedi is a trademark of GoFrugal Technologies. " .
			" All other trademarks and copyrights are the property of their respective owners.</font></p>";
	$copyrights="$note <br />&copy; 2004-".date('Y')." GoFrugal Technologies Pvt. Ltd. All rights reserved.";
	return $contenthead. $contentitle .$pagebreak. $content.($level2?"</li></ol>":"")."</li></ol>".$copyrights."</td></tr></table>";
}

/**
 * @param int $business_id
 *
 * @return string
 */
function get_business_name($business_id){
	$bresult=execute_my_query(" SELECT GTM_TYPE_NAME FROM gft_type_master where GTM_TYPE_CODE=$business_id");
	$business_name='';
	if($qdata_vs=mysqli_fetch_array($bresult)){
		$business_name=$qdata_vs['GTM_TYPE_NAME'];	
	}
	return $business_name;
}


/**
 * @param string $name
 * @param string[int][int] $two_dim_value
 * @param string[int] $selected_value
 * @param string $event_function
 * @param int $tab_index
 * @param string $style
 * @param boolean $span_required
 * @param boolean $is_reqd
 *
 * @return string 
 */
function fix_radio_with($name,$two_dim_value,$selected_value=null,$event_function='',$tab_index=1,$style=null,$span_required=false,$is_reqd=false){
	$return_value='';$span_end='';$span_start='';
	if($span_required==true){
		$span_start="<span style=\"white-space: nowrap;\">";
		$span_end="</span>";
	}
	for($i=0;$i<count($two_dim_value);$i++){
		if(is_array($two_dim_value[$i])){
			$svalue=$two_dim_value[$i][0];
			$sname=$two_dim_value[$i][1];
		}else{
			$sname=$svalue=$two_dim_value[$i];
		}
		$req_str = (($is_reqd)?"required":'');
		$element_id=$name."[$i]";
		$checked=(($selected_value!=null and $selected_value[array_search($svalue,$selected_value)]==$svalue)?"checked":'');
		$return_value.="$span_start<input type=\"radio\" name=\"$name\" id=\"$element_id\" tabindex=\"$tab_index\" class=\"formStyleTextarea\" $style $event_function $checked  value=\"$svalue\" $req_str>" .
				"<label class=\"datalabel\" for=\"$element_id\">$sname</label>$span_end";
	}
	return $return_value;
}

/**
 * @param string $name
 * @param string[int][int] $two_dim_value
 * @param string[int] $selected_value
 * @param string $event_function
 * @param int $tab_index
 * @param string $style
 * @param boolean $span_required
 * @param boolean $is_reqd
 *
 * @return string 
 */
function fix_checkbox_with($name,$two_dim_value,$selected_value=null,$event_function='',$tab_index=1,$style=null,$span_required=false,$is_reqd=false){
	$return_value='';$span_end='';$span_start='';
	if($span_required==true){
		$span_start="<span style=\"white-space: nowrap;\">";
		$span_end="</span>";
	}
	$req_str = (($is_reqd)?'required':'');
	for($i=0;$i<count($two_dim_value);$i++){
		if(is_array($two_dim_value[$i])){
			$two_dim_value_arr=$two_dim_value[$i];
		}else{
			$two_dim_value_arr=array(0=>$two_dim_value[$i],1=>$two_dim_value[$i]);
		}
		$element_id=$name."[$i]";
		$checked=(($selected_value!=null and $selected_value[array_search($two_dim_value_arr[0],$selected_value)]==$two_dim_value_arr[0])?"checked":'');
		$return_value.="$span_start<input type=\"checkbox\" name=\"$name\" id=\"$element_id\" $checked tabindex=\"$tab_index\" class=\"formStyleTextarea\" $style $event_function $checked value=\"".$two_dim_value_arr[0]."\" $req_str>" .
				"<label class=\"datalabel\" for=\"$element_id\">".$two_dim_value_arr[1]."</label>$span_end";
	}
	return $return_value;
}

/**
 * @param string $emp_id
 *
 * @return string[int][int]
 */
function get_approval_name_list($emp_id=null){
	if($emp_id==null){
		$query="select em.GEM_EMP_ID emp_id,concat(em.gem_emp_name, if(gem_status='A',_latin1'',_latin1'*')) emp_name " .
				" from gft_emp_master em where GEM_EMP_ID < 7000 and gem_status='A' and gem_email!=''";
	}else{
		$query="select distinct(em.GEM_EMP_ID) emp_id,em.gem_emp_name emp_name from gft_emp_master em ".
			" left join  gft_emp_group_master eg on (eg.gem_emp_id=em.gem_emp_id)".
			" left join gft_emp_reporting  h1 on (h1.ger_reporting_empid=em.gem_emp_id and h1.ger_status='A' )" .
 			" left join gft_emp_reporting  h2 on (h2.ger_reporting_empid=h1.ger_emp_id and h2.ger_status='A' )" .
 			" left join gft_emp_reporting  h3 on (h3.ger_reporting_empid=h2.ger_emp_id and h3.ger_status='A' )" .
 			" where (h1.ger_emp_id='$emp_id' or h2.ger_emp_id='$emp_id' or h3.ger_emp_id='$emp_id' or eg.gem_group_id=18 or em.GEM_EMP_ID=$emp_id) " .
			" and gem_status='A' and gem_email!='' ";
	}
	return get_two_dimensinal_array_from_query($query,'emp_id','emp_name');
}

/**
 * @param string $GLH_LEAD_CODE
 *
 * @return boolean
 */
function update_cust_bal($GLH_LEAD_CODE){
	$return_array=array();
	global $uid;
	if(!empty($GLH_LEAD_CODE)){
		if(!is_numeric($GLH_LEAD_CODE)){
			$return_array['Error_Message']=' Not a Valid Code';
			//return json_encode($return_array);
			return false;
		}
		$exist_lead_code="select GLH_CUST_NAME,GLH_LEAD_TYPE,GLH_LEAD_SOURCECODE,GLH_REFERENCE_GIVEN from gft_lead_hdr where glh_lead_code=$GLH_LEAD_CODE limit 1";
		$rexist_lead_code=execute_my_query($exist_lead_code);
		if(mysqli_num_rows($rexist_lead_code)==0){
			$return_array['Error_Message']=' Lead Code Doesnt Exist';
			//return json_encode($return_array);
			return false;
		}
		$qdldr=mysqli_fetch_array($rexist_lead_code);
		if($qdldr['GLH_LEAD_TYPE']==13 and $qdldr['GLH_LEAD_SOURCECODE']==7 and $qdldr['GLH_REFERENCE_GIVEN']!=0){ /* corp clients */
			update_cust_bal($qdldr['GLH_REFERENCE_GIVEN']); /* call corp customer lead coede */
		}
		$query_to_upadate_installed_amount=<<<END
	select GOD_ORDER_NO,GOD_COLLECTION_REALIZED,GOD_COLLECTED_AMT,
	sum(if(god_order_status='A',(gop_sell_rate*gop_usedqty*(100+gop_tax_rate+GOP_SERVICE_TAX_RATE)/100),0)) as Installed_Amount
	from gft_order_hdr,gft_order_product_dtl
	join gft_product_master pm on (pm.gpm_product_code=gop_product_code and gpm_product_skew=gop_product_skew)
	join gft_skew_property_master spm on (GSPM_CODE=GFT_SKEW_PROPERTY)
	where GOD_LEAD_CODE=$GLH_LEAD_CODE and god_order_no=gop_order_no and gspm_skew_type in (1)  group by GOD_ORDER_NO
END;
		$result_to_upadate_installed_amount=execute_my_query($query_to_upadate_installed_amount,'function.update_in_hdr.php',true,false);
		if($result_to_upadate_installed_amount){
		while($qdata2=mysqli_fetch_array($result_to_upadate_installed_amount)){
		$table_name_ord='gft_order_hdr';
				$table_key_arr_ord['GOD_ORDER_NO']=$qdata2['GOD_ORDER_NO'];
						$update_arr_ord=array();
						$update_arr_ord['GOD_INSTALLED_AMOUNT']=round($qdata2['Installed_Amount'],0);
						if($qdata2['GOD_COLLECTION_REALIZED']<=$update_arr_ord['GOD_INSTALLED_AMOUNT']){
							$update_arr_ord['GOD_INSTALLED_OUTSTANDING']=($update_arr_ord['GOD_INSTALLED_AMOUNT']-$qdata2['GOD_COLLECTION_REALIZED']);
						}else{
							$update_arr_ord['GOD_INSTALLED_OUTSTANDING']=0;
		}
		array_update_tables_common($update_arr_ord,$table_name_ord,$table_key_arr_ord,null,$uid,$remarks='Balance and Installed Amount Update',
		$table_column_iff_update=null,$insert_new_row=null);
		}
		}

		$query_to_update_balance_purchase=<<<END
	select god_lead_code ,sum(if(god_order_status='A',GOD_ORDER_AMT,0)) as sell_amt,
	sum(if(god_order_status='A',GOD_BALANCE_AMT,0)) as balance ,
	sum(if(god_order_status='A',GOD_INSTALLED_AMOUNT,0)) as instlalled_amount,
	sum(if(god_order_status='A',GOD_INSTALLED_OUTSTANDING,0)) as instlalled_outstanding ,
	sum(if(god_order_status='A',GOD_COLLECTED_AMT-GOD_COLLECTION_REALIZED,0)) as yet_to_realize,
	sum(if(god_order_status='A' and GOD_BALANCE_AMT > 0, GOD_ORDER_AMT,0)) as order_amt_of_outstanding_order
	from gft_order_hdr where GOD_LEAD_CODE=$GLH_LEAD_CODE group by god_lead_code
END;

		$result_to_update_balance=execute_my_query($query_to_update_balance_purchase,'function.update_in_hdr.php',true,false);
		if(($result_to_update_balance) && $qdata=mysqli_fetch_array($result_to_update_balance)){
		$update_lead_hdr_arr=/*. (mixed[string]) .*/ array();		
		$update_lead_hdr_arr['GLH_PURCHASED_AMOUNT']=round((float)$qdata['sell_amt'],0);
		$update_lead_hdr_arr['GLH_BALANCE_AMOUNT']=round((float)$qdata['balance'],0);
		$update_lead_hdr_arr['GLH_INSTALLED_AMOUNT']=round((float)$qdata['instlalled_amount'],0);
				$update_lead_hdr_arr['GLH_INSTALLED_OUTSTANDING']=round((float)$qdata['instlalled_outstanding'],0);
				$update_lead_hdr_arr['GLH_COLLECTION_YET_TO_REALIZE']=round((float)$qdata['yet_to_realize'],0);
				$order_amt_of_os_order = round((float)$qdata['order_amt_of_outstanding_order']);
				$table_name='gft_lead_hdr';
				$table_key_arr['GLH_LEAD_CODE']=$GLH_LEAD_CODE;
				array_update_tables_common($update_lead_hdr_arr,$table_name,$table_key_arr,null,$uid,$remarks='Balance and Installed Amount Update',
				$table_column_iff_update=null,$insert_new_row=null);
				execute_my_query("update gft_lead_hdr_ext set GLE_TOTAL_ORDER_AMT='$order_amt_of_os_order' where GLE_LEAD_CODE='$GLH_LEAD_CODE'");
		}
		//if($result){	$return_array['status']='Sucess'; }
		//else {$return_array['status']='No change'; }
		//return json_encode($return_array);
		return true;
	}else{
		$return_array['Error_Message']='Error Found . Argument is Null ';
		//return json_encode($return_array);
		return false;
	}
}

/**
 * @param string $order_no
 * @param boolean $discount_update
 *
 * @return void
 */
function update_collection_in_hdr($order_no=null,$discount_update=true){
	$whr_order_no=($order_no!=''?" and god_order_no='$order_no' ":'');
// 	if($discount_update){
// 		///TODO need to analyze the purpose and condition of below update.
// 		execute_my_query(" update gft_order_hdr join gft_receipt_order_realize_view on (gcr_order_no=god_order_no) " .
// 				" set GOD_DISCOUNT_ADJ_AMT = (cll_amt-god_order_amt) " .
// 				" where 1 $whr_order_no  and GOD_DISCOUNT_ADJ_AMT > 0 and god_order_amt > (cll_amt +GOD_DISCOUNT_ADJ_AMT) ");
// 	}

	$query="update gft_order_hdr t ,(select god_order_no,god_order_date, " .
			" god_order_amt,ifnull(cll_amt,0) cll_amt,ifnull(realized_amount,0) as realized_amount, " .
			" (god_order_amt-ifnull(realized_amount,0)) as outstanding," .
			" cleared_date,god_lead_code,god_order_status " .
			" from gft_order_hdr oh " .
			" left join gft_receipt_order_realize_view t on (t.gcr_order_no=god_order_no ) " .
			" where (1) $whr_order_no group by god_order_no)t1 " .
			" set GOD_COLLECTED_AMT=cll_amt ,GOD_COLLECTION_REALIZED=realized_amount, " .
			" GOD_BALANCE_AMT=if(t1.god_order_status='C',0,outstanding) " .
			" WHERE t.god_order_no=t1.god_order_no ";
	$result=execute_my_query($query);
	$query_flead_code="select GOD_LEAD_CODE from gft_order_hdr where god_order_no='$order_no' ";
	$result_flead_code=execute_my_query($query_flead_code);
	if(($result_flead_code) && $qd=mysqli_fetch_array($result_flead_code)){
		$GLH_LEAD_CODE=$qd['GOD_LEAD_CODE'];
		update_cust_bal($GLH_LEAD_CODE);
	}

}

/**
 * @param string $receipt_id
 * @param string $user_id
 *
 * @return boolean
 */
function delete_receipt_id($receipt_id,$user_id){
	$query1="select dtl.*,GRD_LEAD_CODE From gft_collection_receipt_dtl dtl,gft_receipt_dtl where " .
			" gcr_receipt_id=grd_receipt_id and gcr_receipt_id='$receipt_id' ";
	$result1=execute_my_query($query1,'common_util.php',true,false,2);
	$column_names='';
	$num_fields=0;
	for($i=0;$i<mysqli_num_fields($result1);$i++){
		$column_names.=($i!=0?',':'').mysqli_field_name_wrapper($result1,$i);
		$num_fields++;
	}
	$no_orders=0;
	$order_no=/*. (string[int]) .*/ array();
	while($qdata1=mysqli_fetch_array($result1)){
		$order_no[$no_orders]=$qdata1['GCR_ORDER_NO'];
		$data = /*. (string[int]) .*/ array();
		for($i=0;$i<$num_fields;$i++){
			$data[$i]=mysqli_real_escape_string_wrapper($qdata1[$i]);
		}
		$old_values= implode(",",$data)."\r\n";
		$lead_code=$qdata1['GRD_LEAD_CODE'];
		$query_audit="insert into gft_audit_log_edit_table(
					GUL_AUDIT_ID ,GUL_AUDIT_TABLE ,GUL_AUDIT_COLUMNS,GUL_AUDIT_OLD_VALUES,
					GUL_AUDIT_NEW_VALUES,GUL_PAGE ,GUL_TIME ,GUL_USER_ID,GUL_AUDIT_LEAD_CODE,GUL_REMARKS)values " .
					"('','gft_collection_receipt_dtl','$column_names','$old_values','Deleted','delete_receipt.php'," .
					"now(),'$user_id','$lead_code','Receipt_ID:$receipt_id')" ;
		execute_my_query($query_audit,'common_util.php',true,false,2);
		$no_orders++;			
	}
	$query2="select * From gft_receipt_dtl  where grd_receipt_id='$receipt_id' ";
	$result2=execute_my_query($query2,'',true,false);
	$column_names="";
	$num_fields2=0;
	for($i=0;$i<mysqli_num_fields($result2);$i++){
		$column_names.=($i!=0?',':'').mysqli_field_name_wrapper($result2,$i);
		$num_fields2++;
	}
	while($qdata2=mysqli_fetch_array($result2)){
		$data = /*. (string[int]) .*/ array();
		for($i=0;$i<$num_fields2;$i++){
			$data[$i]=$qdata2[$i];
		}
		$old_values= implode(",",$data)."\r\n";
		$lead_code=$qdata2['GRD_LEAD_CODE'];
		$query_audit="insert into gft_audit_log_edit_table(
					GUL_AUDIT_ID ,GUL_AUDIT_TABLE ,GUL_AUDIT_COLUMNS,GUL_AUDIT_OLD_VALUES,
					GUL_AUDIT_NEW_VALUES,GUL_PAGE ,GUL_TIME ,GUL_USER_ID,GUL_AUDIT_LEAD_CODE,GUL_REMARKS)values " .
					"('','gft_receipt_dtl','$column_names','$old_values','Deleted','delete_receipt.php'," .
					"now(),'$user_id','$lead_code','Receipt_ID:$receipt_id')" ;
		execute_my_query($query_audit,'common_util.php',true,false,1);		
	}
	// ******Update in Order hdr *********
	$delquery1="delete From gft_collection_receipt_dtl  where gcr_receipt_id='$receipt_id' ";
	$delquery2="delete From gft_receipt_dtl  where grd_receipt_id='$receipt_id' ";
	$dresult1=execute_my_query($delquery1,'common_util.php',true,false,1);
	$dresult2=execute_my_query($delquery2,'common_util.php',true,false,1);
	for($no_orders=0;$no_orders<count($order_no);$no_orders++){
		update_collection_in_hdr($order_no[$no_orders]);
	}

	return true;
}

/**
 * @param int $lead_code
 *
 * @return string 
 */
function get_territory_for_lead($lead_code){
	$query="select glh_territory_id from gft_lead_hdr where glh_lead_code='$lead_code' ";
	$result=execute_my_query($query,'common_util.php',true,false,2);
	$terr_id = "";
	if($qdata=mysqli_fetch_array($result)){
	   $terr_id=$qdata['glh_territory_id'];
	}
	return $terr_id;
}

/**
 * @param boolean $select
 * @param string[] $group_id
 * @param string $gem_role
 * @param boolean $only_office
 *
 * @return string[int][int]
 */
function get_support_exec($select=true,$group_id=null,$gem_role='',$only_office=true){
	$out_this_query=" and emp.gem_emp_id not in (".SALES_DUMMY_ID.") ";
	$query="select distinct(emp.gem_emp_id),emp.gem_emp_name from gft_emp_master emp " .
			" inner join gft_role_group_master rgm on (grg_role_id=emp.gem_role_id) " .
			" left join gft_emp_group_master gm on (gm.GEM_EMP_ID=emp.GEM_EMP_ID) ".
			" where emp.gem_status='A' $out_this_query " ;
	if($only_office) {
		$query.= " AND GEM_OFFICE_EMPID!='0' ";
	}
	if($gem_role!=''){
		$query.=" and GEM_ROLE_ID in ($gem_role) ";
	}	
	if($group_id!=null){
		if(is_array($group_id)){
			$group_id_cm=implode('',$group_id);			
		}else {
			$group_id_cm=$group_id;
		}
		$query.=" and (grg_group_id in ($group_id_cm) or gm.GEM_GROUP_ID in ($group_id_cm) )";
	}		
	$query.=" order by gem_emp_name ";		
	$result=execute_my_query($query,'Support_util.php',true,false,2);
	$i=0;
	$supp_exec_list=/*. (string[int][int]) .*/ array();
	if($select==true){
		$supp_exec_list[$i][0]=0;
		$supp_exec_list[$i][1]="Any";
		$i++;
	}
	while($qdata=mysqli_fetch_array($result)){
		$supp_exec_list[$i][0]=$qdata['gem_emp_id'];
  		$supp_exec_list[$i][1]=$qdata['gem_emp_name'];
  		$i++;
    }
   return $supp_exec_list;
}//End of fn

/**
 * @param string $field_name
 * @param string[int][int] $array_values
 * @param int $width
 * @param int $height
 * @param string $onchange_fn
 * @param boolean $add_others
 * @param boolean $return_value
 * @param string[int] $selected_array
 *
 * @return string 
 */
function show_multiselect_listbox_wcheckbox2($field_name,$array_values,$width=300,$height=75,
$onchange_fn=null,$add_others=false,$return_value=false,$selected_array=null){
	$content_list="";
	$brake_element="";
	$opt_group="";
	for($i=0;$i<count($array_values);$i++){	
		$value=$array_values[$i][0];
		$text=$array_values[$i][1];
		$rvalue=(string)str_replace(' ','_',$value);
		$cmb_id=$field_name.'_'.$rvalue;
		$cmb_name=$field_name;
		$table_ele_head	=	"";
		if(isset($array_values[$i][2]) and $array_values[$i][2]!=''){
			$curr_opt_group=$array_values[$i][2];
			if($opt_group!=$curr_opt_group){
				$table_ele_head="<tr><td><b>$curr_opt_group</b> </td></tr>";
				$opt_group=$curr_opt_group;
			}
		}
		
		$table_ele="<tr id=\"tr".$cmb_id."\"><td valign=\"top\" >";
		$content_list.="$brake_element $table_ele_head $table_ele <input id=\"$cmb_id\" name=\"$cmb_name\" value=\"$value\" ";
		if($onchange_fn!=null){
		$content_list.=" onclick=\"javascript:$onchange_fn\" ";  }
		$selected_checkbox='';
		if($selected_array!=null and is_array($selected_array)){
			if(array_search($value,$selected_array)>-1){
				$selected_checkbox="checked";
			}
		}
		$content_list.=" type=\"checkbox\" $selected_checkbox ><span nowrap>" .
			       "<label for=\"$cmb_id\" nowrap class=\"list_combo\">$text</label></span>";
	}
	if($add_others==true){
	
	}
	$height=$height.'px';
	$width=$width.'px';
$list_item=<<<END
<div style="overflow:auto;width:$width;height:$height;border:1px solid #336699;padding-left:5px;overflow-x: auto">
<table class="multicheckbox_list" width="100%">$content_list</table></div>
END;
	if($return_value){
		return $list_item;
	}else{
		echo $list_item;
		return '';
	}
}

/**
 * @param int $fmonth
 * @param int $fyear
 *
 * @return string[int][int] 
 */
function return_holiday_list_in_array($fmonth,$fyear){
	$holiday="select date_format(ghl_date,'%e') as hdate,ghl_desc from gft_holiday_list " .
			"where month(ghl_date)=$fmonth and year(ghl_date)='$fyear' ";
	$res_holiday=execute_my_query($holiday);
	$hday=/*. (string[int][int]) .*/ array();
	$j=0;
	while($qdata=mysqli_fetch_array($res_holiday)){	  
		$dt=(int)$qdata['hdate'];
		$hday[$dt][0]='y';
		$hday[$dt][1]=$qdata['ghl_desc'];
	}
	return $hday;
}

/**
 * @param string $office_id
 * @param int $group 
 * @param string $default_caption
 * @param string $status
 *
 * @return string[int][int]
 */
function get_extension_no_list($office_id,$group,$default_caption='-Select-',$status=null){
	$query="select GPE_EXT_NO, GPE_EXT_GROUP, GPE_EXT_NAME, GPE_EXT_STATUS,gpe_office_id " .
			"from gft_phone_extension where 1 ";
	if($group!=0){ $query.=" and gpe_ext_group=$group "; }
	if($status!=null){$query.=" and GPE_EXT_STATUS='$status' ";}
	if($office_id!=''){$query.=" and gpe_office_id=$office_id ";}
	$result=execute_my_query($query ." order by gpe_office_id,GPE_EXT_NO ");
	$ext_list=/*. (string[int][int]) .*/ array();
	$i=0;
	while($qdata=mysqli_fetch_array($result)){
		$ext_list[$i][0]=$qdata['GPE_EXT_NO'];
		$ext_list[$i][1]=$qdata['GPE_EXT_NAME'];
		$ext_list[$i][2]=$qdata['gpe_office_id'];
		$i++;
	}
	return $ext_list;
}

/* NOT USED 
 * @param string $status
 * @param string[] $group_id_arr
 * @param boolean $default_list
 * @param string $default_name
 *
 * @return string[int][int]
 *
function get_executive_list($status='A',$group_id_arr,$default_list=false,$default_name='-Select-'){
	global $skip_authorization;
	$query='';
	if($skip_authorization){
		return true;
	}
	if(is_array($group_id_arr)){
		$group_id=implode(',',$group_id_arr);
	}

	if($group_id!='' and $group_id!=0 ){
	$query="select a.gem_emp_id,gem_emp_name from gft_emp_master a" .
			" left join gft_role_group_master rg on (grg_role_id=gem_role_id) " .
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id)" .
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id)" .
			" where gem_status='A' and ggm_group_id in ($group_id)  group by a.gem_emp_id ";
	}
	if($query==''){
		return false;	
	}
	$result=execute_my_query($query,'common_util.php',true,false,2);
	$i=0;
	if($default_list==true){
		$emp_dtl[$i][0]=0;
		$emp_dtl[$i][1]=$default_name;
		$i++;
	}
	while($qdata=mysqli_fetch_array($result)){
		$emp_dtl[$i][0]=$qdata['gem_emp_id'];
		$emp_dtl[$i][1]=$qdata['gem_emp_name'];
		$i++;
	}
	return $emp_dtl;
}
*/


/**
 * @param string $cp_lead_code
 * @param string $emp_id
 *
 * @return string[string] 
 */
function cp_emp_lead_map_dtl($cp_lead_code=null,$emp_id=null){
	$query=" SELECT CGI_EMP_ID, CGI_LEAD_CODE " .
			" FROM gft_cp_info, gft_leadcode_emp_map " .
			" WHERE GLEM_LEADCODE=cgi_lead_code " .
			($emp_id!=null?"AND GLEM_EMP_ID=$emp_id":"").
			($cp_lead_code!=null?" and cgi_lead_code=$cp_lead_code":"");
	$result=execute_my_query($query);		
	if($data=mysqli_fetch_array($result)){
		$data_array['emp_id']=$data['CGI_EMP_ID'];
		$data_array['cp_lead_code']=$data['CGI_LEAD_CODE'];
		return $data_array;
	}else{
		return null;
	}
}

/**
 * @param string $lcode1
 *
 * @return string 
 */
function get_cp_name_info($lcode1){
	$cp_dtl=explode('-',$lcode1);
	if (!isset($cp_dtl[1])){
		$cp_dtl[1]='';
	}
	$cp_name='';
	$result=execute_my_query("SELECT A.gem_emp_name,concat(A.gem_emp_id,'-',B.cgi_lead_code,'-',A.GEM_ROLE_ID) as cp_ids " .
			" from gft_cp_info  B " .
			" join gft_emp_master  A on (A.gem_emp_id=B.cgi_emp_id and A.GEM_STATUS='A')" .
			" join gft_lead_hdr C on (B.cgi_lead_code=C.glh_lead_code) " .
			" where A.gem_emp_id=".$cp_dtl[0]." ".($cp_dtl[1]!=''?" and  B.cgi_lead_code={$cp_dtl[1]} ":""));
	if($data=mysqli_fetch_array($result)){
		$cp_name=$data['gem_emp_name'];
	}
	return $cp_name;
}

/**
 * @param string $str
 *
 * @return string 
 */
function makeValidData($str){
	$opstr='';
	$ch='';
	$str=preg_replace('/\&/','and',$str);
	//$str=preg_replace('[^A-Z a-z 0-9 /]',' ',$str);
	$str=preg_replace('/[^a-zA-Z0-9-\s\/\.]/', ' ', $str);
	//$str=eregi_replace('^[\s]+|[\s]+$',':',$str);
	//$str=eregi_replace('[\s][\s]+',':',$str);
	//$str=preg_replace('/^\s+|\s+$/','',$str);
	//$str=preg_replace('/\s\s+/',' ',$str);
	
	$str=ucwords(strtolower(trim($str)));
	
	return $str;
}

/**
 * @param string $lead_code
 *
 * @return void
 */
function update_glh_customer_date($lead_code=null){
	/* need to update */
	if($lead_code!=null){
		$whr_t=" and gid_lead_code='$lead_code' ";
		$whr_lh=" and glh_lead_code='$lead_code' ";
	}else{
		$whr_t='';
		$whr_lh='';
	}
$update_gft_lead_hdr=<<<END
	update gft_lead_hdr lh,(
	select gid_lead_code ,min(gid_install_date) cust_date,god_emp_id  
	from gft_install_dtl_new ,gft_order_hdr	 
	where god_order_no=gid_order_no and gid_status='A' $whr_t 
	group by gid_lead_code 
	)t set GLH_IS_CUSTOMER='Y' , glh_customer_from =cust_date,glh_order_by_whom=t.god_emp_id where 
	t.gid_lead_code=lh.glh_lead_code $whr_lh  
END;


	
	execute_my_query($update_gft_lead_hdr);
	
}


/**
 * @param string $group_id
 *
 * @return string
 */
function get_roles_of_group($group_id){
	if($group_id!=null){
		$query="select group_concat(GRG_ROLE_ID) from gft_role_group_master where GRG_GROUP_ID='$group_id' ";
		$result=execute_my_query($query);
		if(mysqli_num_rows($result)==1){
			$qd=mysqli_fetch_array($result);
			$roles=$qd[0];
			return $roles;
		}
	}
	//return false;
	return null;
}

/**
 * @param string $GOD_ORDER_NO
 * @param int $actual_amount
 * @param string $pdf_file
 * @param boolean $data_migrate_alert
 * @param string $datamigrateattachment
 *
 * @return void
 */
function send_discount_alert($GOD_ORDER_NO,$actual_amount,$pdf_file='',$data_migrate_alert=false,$datamigrateattachment=null){
	global $uid,$impl_required;
	$lead_code='';
	$GOD_EMP_ID='';
	$query= " SELECT GLH_LEAD_CODE, GLH_CUST_NAME, GLH_CUST_STREETADDR2, GOD_ORDER_DATE, GOD_ORDER_AMT, GOD_ORDER_STATUS," .
			" GOD_REMARKS, ordby.GEM_EMP_NAME,GOD_EMP_ID, apby.GEM_EMP_NAME approved_by, GOD_APPROVEDBY_EMPID, GOD_ORDER_APPROVAL_STATUS  " .
			" FROM gft_order_hdr join gft_emp_master ordby on (GOD_EMP_ID=ordby.gem_emp_id) " .
			" join gft_lead_hdr on(GLH_LEAD_CODE=god_lead_code) " .
			" left join gft_emp_master apby on(GOD_APPROVEDBY_EMPID=apby.gem_emp_id) " .
			" WHERE god_order_no='$GOD_ORDER_NO' ";
	$result=execute_my_query($query);
	$cust_dtl="";$cust_dtl=array();
	if(mysqli_num_rows($result)>0){
		$cust_dtl="Order Entry Details of the order No<b> $GOD_ORDER_NO <b>As Follows<br/>".
		"<table border=1><tr><th>Customer Id</th><th>Customer Name-Location</th><th>Order Date</th><th>Ordered By</th>".
		"<th>Approved By</th><th>Order Amount</th><th>Actual Price</th><th>Total Discount %</th><th>Order Approval Status</th></tr>";
		while($data=mysqli_fetch_array($result)){
			$cust_add_dtl=customerContactDetail($data['GLH_LEAD_CODE']);
			$GOD_EMP_ID=$data['GOD_EMP_ID'];
			$GOD_APPROVEDBY_EMPID=$data['GOD_APPROVEDBY_EMPID'];
			$total_amount=$data['GOD_ORDER_AMT'];
			$discount_ratio=($actual_amount!=0?round((($actual_amount - $total_amount)/$actual_amount*100),1):'');
			$actual_amount=round($actual_amount,2);
			$lead_code=$data['GLH_LEAD_CODE'];
			$order_app_status=$data['GOD_ORDER_APPROVAL_STATUS'];
			$cust_dtl.="<tr><td>".$data['GLH_LEAD_CODE']."</td>".
			"<td>".$data['GLH_CUST_NAME']."-".$data['GLH_CUST_STREETADDR2']."</td>".
			"<td>".$data['GOD_ORDER_DATE']."</td><td>".$data['GEM_EMP_NAME']."</td>".
			"<td>".$data['approved_by']."</td><td align='right'>".$data['GOD_ORDER_AMT']."</td>".
			"<td>$actual_amount</td><td>$discount_ratio %</td><td>$order_app_status</td></tr>"; 		
				
		}
		$cust_dtl.="</table>";	
	}
	$logmail="";
	$query= " select pf.GPM_PRODUCT_ABR,pm.GPM_SKEW_DESC, GOP_LIST_PRICE, GOP_SELL_RATE, GOP_QTY, GOP_SELL_AMT " .
			" from gft_order_product_dtl " .
			" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE AND pm.GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW)" .
			" join gft_product_family_master pf on (pf.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) " .
			" where GOP_ORDER_NO='$GOD_ORDER_NO' and concat(GOP_PRODUCT_CODE,'-',GOP_PRODUCT_SKEW) not in (select concat(GSK_PRODUCT_CODE,'-',GSK_PRODUCT_SKEW) from gft_skew_kit_master) order by GOP_PRINT_ORDER ";
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)>0){
		$logmail.="Order Product Details of Order no :$GOD_ORDER_NO<br/>".
		"<table border=1><tr><th>Product Name</th><th>List Price</th><th>Sell Rate</th><th>Quantity</th><th>Sell Amount</th></tr>";
		while($data=mysqli_fetch_array($result)){
			$logmail.="<tr><td>".$data['GPM_PRODUCT_ABR']."-".$data['GPM_SKEW_DESC']."</td>".
			"<td align='right'>".$data['GOP_LIST_PRICE']."</td>".
			"<td align='right'>".$data['GOP_SELL_RATE']."</td>".
			"<td align='right'>".$data['GOP_QTY']."</td>".
			"<td align='right'>".$data['GOP_SELL_AMT']."</td></tr>";
		}
		$logmail.="</table><br/>";
	}
	
	$head_query=" select GAH_CUSTOMER_COMMENTS, GAH_MY_COMMENTS,GAH_DATE_TIME, GEM_EMP_NAME " .
			" from gft_lead_hdr lh " .
			" join gft_audit_hdr ah on(ah.GAH_LEAD_CODE=lh.GLH_LEAD_CODE and lh.GLH_KYC_AUDIT_ID=ah.GAH_AUDIT_ID) " .
			" join gft_emp_master ab on (GAH_AUDIT_BY=ab.gem_emp_id)" .
			" where glh_lead_code=$lead_code ";
	$result_head=execute_my_query($head_query);
	if(mysqli_num_rows($result_head)>0){
		$cust_dtl.="<br><b>Know Your Customer</b></br><table border=1><tr><td>Entered On</td><td>Enterd By</td><td>Customer Comments</td><td>Audit Comments</td></tr>";
		while($data_audit_head=mysqli_fetch_array($result_head)){
			$cust_dtl.="<tr><td>".$data_audit_head['GAH_DATE_TIME']."</td><td>".$data_audit_head['GEM_EMP_NAME']."</td><td>".$data_audit_head['GAH_CUSTOMER_COMMENTS']."</td><td>".$data_audit_head['GAH_MY_COMMENTS']."</td></tr>";
		}
		$cust_dtl.="</table>";
	}
	$result_audit_ans=execute_my_query(" select GAQ_QUESTION_TYPE, GAD_AUDIT_ANS " .
			" from gft_lead_hdr lh " .
			" join gft_audit_hdr ah on(ah.GAH_LEAD_CODE=lh.GLH_LEAD_CODE and lh.GLH_KYC_AUDIT_ID=ah.GAH_AUDIT_ID)" .
			" join gft_audit_dtl ad on (ad.GAD_AUDIT_ID=ah.GAH_AUDIT_ID) " .
			" join gft_audit_question_master on(GAD_AUDIT_QID=GAQ_QUESTION_ID) " .
			" where glh_lead_code=$lead_code ");
	if(mysqli_num_rows($result_audit_ans)>0){
		$cust_dtl.="</br>KYC In Details <table border=1><tr><td>Questions</td><td>Answers</td></tr>";
		while($data_audit_ans=mysqli_fetch_array($result_audit_ans)){
			$cust_dtl.="<tr><td>".$data_audit_ans['GAQ_QUESTION_TYPE']."</td><td>".$data_audit_ans['GAD_AUDIT_ANS']."</td></tr>";
		}
		$cust_dtl.="</table>";
	}
	if($logmail!=""){
		$logmail.="This is a automated mail from SAM ";
		$email_to=get_emp_manager_mail_id($GOD_EMP_ID).','.get_email_addr($uid).','.get_email_addr($GOD_EMP_ID).((isset($impl_required) and $impl_required=='Yes')? ','.get_email_addr($cust_add_dtl['Reg_incharge']).','.get_samee_const("TRAINING_TEAM_MAIL_ID"):'');//",".get_samee_const("SALES_COORDINATOR_MAIL_ID").
		$email_from=get_samee_const("ADMIN_TEAM_MAIL_ID");
		$rs=send_mail_function($email_from,$email_to,'Order Entry Log',$cust_dtl.$logmail,($pdf_file!=''?"../sales_server_support/invoice/$pdf_file":null),null,'13',true);
		if($data_migrate_alert==true){
			send_mail_function($email_from,get_samee_const("DATAMIGRATION"),'Migration Order Details',$cust_dtl.$logmail ,$datamigrateattachment,'','Migration Issue Details',true);
		}
	}
}

/**
 * @param string $receit_id
 * @param int $uid
 * @return void
 */
function send_collection_receipt($receit_id,$uid=0){
	$query= " SELECT GLH_LEAD_CODE, GLH_CUST_NAME, GLH_CUST_STREETADDR2,GRD_DATE,GRD_EMP_ID, m.GEM_EMP_NAME,m.GEM_EMAIL,GRD_RECEIPT_TYPE," .
			" GRD_RECEIPT_AMT, GRD_CHEQUE_DD_NO, GRD_CHEQUE_DD_DATE, GRD_BANK_NAME, " .
			" GRD_TERMS_REGARDING_COLLECTION,GRD_HAND_OVER_DATE,GRD_DEPOSTIT_CHALLAN_NO," .
			" hd.gem_emp_name 'hand_over_to_name',cqs.GCS_STATUS as cheque_status,GRT_TYPE_ABR,GRT_TYPE_NAME" .
			" FROM gft_receipt_dtl " .
			" JOIN gft_emp_master m on (m.GEM_EMP_ID=GRD_EMP_ID)" .
			" JOIN gft_lead_hdr on(GLH_LEAD_CODE=GRD_LEAD_CODE)" .
			" join gft_receipt_type_master rtm on (rtm.GRT_TYPE_CODE=GRD_RECEIPT_TYPE)" .
			" left join gft_cheque_status_master cqs on (cqs.GCS_STATUS_ABR=GRD_STATUS)" .
			" left join gft_emp_master hd on (hd.gem_emp_id=GRD_HAND_OVER_TO) " .
			" WHERE GRD_RECEIPT_ID='$receit_id' ";
	$result=execute_my_query($query);
	$cust_dtl="";
	$GRD_EMP_ID='';
	$collected_emp='';
	$i=0;
	if(mysqli_num_rows($result)>0){
		while($data=mysqli_fetch_array($result)){
			$GRD_EMP_ID=$data['GRD_EMP_ID'];
			$collected_emp=$data['GEM_EMAIL'];
			$GRD_RECEIPT_AMT=$data['GRD_RECEIPT_AMT'];
			$GRT_TYPE_ABR=$data['GRT_TYPE_ABR'];
			if($i==0){
				$cust_dtl="Collection Entry Details As Follows<br/><table border=1><tr><td>Customer Id</td><td>Customer Name-Location</td><td>Collection Date</td>" .
							"<td>Collected By</td><td>Receipt Amount</td><td>Receipt Type</td>";
				if($GRT_TYPE_ABR=='q' or $GRT_TYPE_ABR=='d'){
					$cust_dtl.="<td>Cheque DD Number</td><td>Cheque DD Date</td><td>Bank Name</td>";
				}
				$cust_dtl.="<td>Terms Regarding Collection</td><td>Hand Over Date</td><td>Hand Over to </td>" .
							"<td>Deposited Challan No.</td><td>Cheque Status</td></tr>";
			}
			$cust_dtl.="<tr><td>".$data['GLH_LEAD_CODE']."</td><td>".$data['GLH_CUST_NAME']."-".$data['GLH_CUST_STREETADDR2']."</td>" .
			 			"<td>".$data['GRD_DATE']."</td><td>".$data['GEM_EMP_NAME']."</td><td>".$data['GRD_RECEIPT_AMT']."</td><td>".$data['GRT_TYPE_NAME']."</td>";
			if($GRT_TYPE_ABR=='q' or $GRT_TYPE_ABR=='d'){
				$cust_dtl.="<td>".$data['GRD_CHEQUE_DD_NO']."</td><td>".$data['GRD_CHEQUE_DD_DATE']."</td><td>".$data['GRD_BANK_NAME']."</td>";
			}
			$cust_dtl.="<td>".$data['GRD_TERMS_REGARDING_COLLECTION']."</td><td>".$data['GRD_HAND_OVER_DATE']."</td><td>".$data['hand_over_to_name']."</td>" .
						"<td>".$data['GRD_DEPOSTIT_CHALLAN_NO']."</td><td>".$data['cheque_status']."</td></tr>";
			$i++; 		
		}
		$cust_dtl.="</table>";	
	}
	$logmail="";
	$query= " SELECT GCR_ORDER_NO,GCR_RECEIPT_ID,GCR_AMOUNT,GCR_REASON,GOD_ORDER_NO, GOD_ORDER_DATE," .
			" GOD_ORDER_AMT, GOD_COLLECTED_AMT,GOD_COLLECTION_REALIZED, GOD_BALANCE_AMT" .
			" FROM gft_collection_receipt_dtl " .
			" join gft_order_hdr on(GOD_ORDER_NO=GCR_ORDER_NO)" .
			" where GCR_RECEIPT_ID='$receit_id'";
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)>0){
		$logmail.="Collection Details<br/><table border=1><tr><td>Order Number</td><td>Collection Amount</td><td>Order date</td><td>Order Amount</td>" .
				"<td>Order Collected Amount</td><td>Realized Amount(A/C)</td><td>Outstanding Amount(A/C)</td></tr>";
		while($data=mysqli_fetch_array($result)){
			$logmail.="<tr><td>".$data['GCR_ORDER_NO']."</td>".
			"<td>".$data['GCR_AMOUNT']."</td>".
			"<td>".$data['GOD_ORDER_DATE']."</td>".
			"<td>".$data['GOD_ORDER_AMT']."</td>".
			"<td>".$data['GOD_COLLECTED_AMT']."</td>".
			"<td>".$data['GOD_COLLECTION_REALIZED']."</td>".
			"<td>".$data['GOD_BALANCE_AMT']."</td>";
		}
		$logmail.="</table><br/>";
	}else{
		$query1=" SELECT GLH_LEAD_CODE,GCR_RECEIPT_ID,GCR_AMOUNT,GLH_CUST_NAME " .
				" FROM gft_collection_receipt_dtl " .
				" join gft_lead_hdr on(GLH_LEAD_CODE=GCR_ORDER_NO) " .
				" where GCR_RECEIPT_ID='$receit_id'";
		$result1=execute_my_query($query1);
		if(mysqli_num_rows($result1)>0){
			$logmail.="Collection Details<br/><table border=1><tr><td>Customer ID</td><td>Customer Name</td><td>Collection Amount</td><td>Collection Type</td></tr>";
			while($data1=mysqli_fetch_array($result1)){
				$logmail.="<tr><td>".$data1['GLH_LEAD_CODE']."</td>".
						"<td>".$data1['GLH_CUST_NAME']."</td>".
						"<td>".$data1['GCR_AMOUNT']."</td>".
						"<td>New Order Advance</td>";
			}
			$logmail.="</table><br/>";
		}
	}
	if($logmail!=""){
		$email_to=get_emp_manager_mail_id($GRD_EMP_ID);//.",".get_samee_const("SALES_COORDINATOR_MAIL_ID");
		$emp_id	=	($uid==0?$_SESSION['uid']:$uid);		
		$col_entry_by_emp=$email_from=get_email_addr((string)$emp_id);
		if($col_entry_by_emp==$collected_emp){
			$cc_add=$col_entry_by_emp;
		}else{
			$cc_add=$col_entry_by_emp.','.$collected_emp;
		}
		/*need to update category */
		$rs=send_mail_function($email_from,$email_to,'Collection Entry Log',$cust_dtl.$logmail,null,$cc_add	,'13',true);
	}
}

/**
 * @param string $GOD_ORDER_NO
 *
 * @return void
 */
function order_edit_report($GOD_ORDER_NO){
	$query= " SELECT GLH_LEAD_CODE, GLH_CUST_NAME, GLH_CUST_STREETADDR2, GOD_ORDER_DATE, GOD_ORDER_AMT, GOD_ORDER_STATUS," .
			" GOD_REMARKS  FROM gft_order_hdr, gft_emp_master cic, gft_emp_master ordby, gft_lead_hdr   " .
			" WHERE GOD_INCHARGE_EMP_ID=cic.gem_emp_id and GOD_EMP_ID=ordby.gem_emp_id ".
   			" and GLH_LEAD_CODE=god_lead_code and god_order_no='$GOD_ORDER_NO'  ".	
			" order by god_order_date asc,god_order_no asc ";
	$cust_id	=	'';
	$emp_id		=	'';
	$audit_type	=	39;
	$result=execute_my_query($query);
	$cust_dtl="";
	if(mysqli_num_rows($result)>0){
		$cust_dtl="Customer Details of the order $GOD_ORDER_NO <br/><table border=1><tr><td>Customer Id</td><td>Customer Name-Location</td><td>Order Date</td></tr>";
		while($data=mysqli_fetch_array($result)){
			$cust_dtl.="<tr><td>".$data['GLH_LEAD_CODE']."</td><td>".$data['GLH_CUST_NAME']."-".$data['GLH_CUST_STREETADDR2']."</td><td>".$data['GOD_ORDER_DATE']."</td></tr>"; 		
			$cust_id	=	$data['GLH_LEAD_CODE'];
		}
		$cust_dtl.="</table>";	
	}		
	$logmail="";
	$query= "select GAV_TABLE_NAME,GAV_COLUMN_NAME,GAV_PREVIOUS_VALUE,GAV_UPDATED_VALUE,GAV_UPDATED_DATETIME,GEM_EMP_NAME,GEM_EMP_ID,GAV_FROM_PAGE" .
			" from gft_audit_viewer_order join gft_emp_master on(GAV_UPDATED_BY=gem_emp_id)" .
			" where GAV_TABLE_NAME in ('gft_install_dtl_new','gft_order_hdr','gft_collection_receipt_dtl') and GAV_ORDER_NO='$GOD_ORDER_NO'".
			" order by GAV_UPDATED_DATETIME";
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)>0){
		$logmail.="order Header Audit details of Order no :$GOD_ORDER_NO<br/><table border=1><tr><td>Column name</td><td>Old value</td><td>New Value</td><td>Updated on</td><td>Updated By</td><td>From page</td></tr>";
		while($data=mysqli_fetch_array($result)){
			if($data['GAV_COLUMN_NAME']=='GOD_ORDER_APPROVAL_STATUS'){
				$temp_res1=execute_my_query("select GOS_STATUS_NAME from gft_order_approval_status_master where GOS_ID='".$data['GAV_PREVIOUS_VALUE']."'");
				$temp1=mysqli_fetch_array($temp_res1);
				$data['GAV_PREVIOUS_VALUE']=$temp1['GOS_STATUS_NAME'];
				$temp_res2=execute_my_query("select GOS_STATUS_NAME from gft_order_approval_status_master where GOS_ID='".$data['GAV_UPDATED_VALUE']."'");
				$temp2=mysqli_fetch_array($temp_res2);
				$data['GAV_UPDATED_VALUE']=$temp2['GOS_STATUS_NAME'];
			}
			$logmail.="<tr><td>".$data['GAV_TABLE_NAME'].' - '.$data['GAV_COLUMN_NAME']."</td>".
			"<td>".$data['GAV_PREVIOUS_VALUE']."</td>".
			"<td>".$data['GAV_UPDATED_VALUE']."</td>".
			"<td>".$data['GAV_UPDATED_DATETIME']."</td>".
			"<td>".$data['GEM_EMP_NAME']."</td>".
			"<td>".$data['GAV_FROM_PAGE']."</td></tr>";
			$emp_id	=	$data['GEM_EMP_ID'];
		}		
		$logmail.="</table><br/>";
	}
	$query= "select pf.GPM_PRODUCT_ABR,pm.GPM_SKEW_DESC, GAV_COLUMN_NAME, GAV_PREVIOUS_VALUE,GAV_UPDATED_VALUE,GAV_UPDATED_DATETIME,GEM_EMP_NAME,GEM_EMP_ID,GAV_FROM_PAGE " .
			" from gft_audit_viewer_order join gft_emp_master on(GAV_UPDATED_BY=gem_emp_id)" .
			" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GAV_PRODUCT_CODE AND pm.GPM_PRODUCT_SKEW=GAV_PRODUCT_SKEW)" .
			" join gft_product_family_master pf on (pf.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE)" .
			" where GAV_TABLE_NAME='gft_order_product_dtl' and GAV_ORDER_NO='$GOD_ORDER_NO'" .
			" order by GAV_UPDATED_DATETIME" ;
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)>0){
		$logmail.="Order Product Edit Details of Order no :$GOD_ORDER_NO<br/><table border=1><tr><td>Product Name</td><td>Column name</td><td>Old value</td><td>New Value</td><td>Updated on</td><td>Updated By</td><td>From page</td></tr>";
		while($data=mysqli_fetch_array($result)){
			$logmail.="<tr><td>".$data['GPM_PRODUCT_ABR']."-".$data['GPM_SKEW_DESC']."</td>".
			"<td>".$data['GAV_COLUMN_NAME']."</td>".
			"<td>".$data['GAV_PREVIOUS_VALUE']."</td>".
			"<td>".$data['GAV_UPDATED_VALUE']."</td>".
			"<td>".$data['GAV_UPDATED_DATETIME']."</td>".
			"<td>".$data['GEM_EMP_NAME']."</td>".
			"<td>".$data['GAV_FROM_PAGE']."</td>";
			$emp_id	=	$data['GEM_EMP_ID'];
		}
		$logmail.="</table><br/>";
	}
	if($logmail!=""){
		$email_to=get_samee_const("AC_MAIL_ID");
		$email_from=get_samee_const("ADMIN_TEAM_MAIL_ID");
		$rs=send_mail_function($email_from,$email_to,'Order Edit Log',$cust_dtl.$logmail,null,null,'13',true);
		$audit_id=execute_my_query("INSERT INTO gft_audit_hdr(GAH_LEAD_CODE,GAH_DATE_TIME,GAH_CUSTOMER_COMMENTS,GAH_MY_COMMENTS,GAH_AUDIT_BY,GAH_AUDIT_TYPE,GAH_LAST_AUDIT)	VALUES('$cust_id','".date('Y-m-d H:i:s')."','Order Edit','".mysqli_real_escape_string_wrapper($cust_dtl.$logmail)."',$emp_id,$audit_type,'N')");
	}
}
/**
 * @return string
 */
function get_group_selection_for_support(){
	$get_group_id_query=" select group_concat(distinct(GCG_HANDLING_GROUP)) " .
			"from gft_complaint_group_master where " .
			" GCG_STATUS='A' ";
	$get_group_id_result=execute_my_query($get_group_id_query);	
	if(mysqli_num_rows($get_group_id_result)==1){
		$qd=mysqli_fetch_array($get_group_id_result);
		$group_id_sel=$qd[0];
	}else {$group_id_sel=6;}
	return $group_id_sel;
}

/**
 * @param int[int] $group_id_arr
 * 
 * @return string
 */
function get_query_emp_list_by_group_filter($group_id_arr){
	if(is_array($group_id_arr)){ $group_code=implode(',',$group_id_arr);}
	else $group_code=$group_id_arr;
	$without_dummy=" a.gem_emp_id!= ".SALES_DUMMY_ID;
	$query_gr="select distinct(a.gem_emp_id) eid,gem_emp_name,gem_mobile from gft_emp_master a" .
			" left join gft_role_group_master rg on (grg_role_id=gem_role_id)" .
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id)" .
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id)" .
			" where $without_dummy  ";
	if($group_code!=''){ $query_gr.=" and ggm_group_id in ($group_code) "; }

	return $query_gr." order by gem_emp_name ";
}


/**
 * @param int[int] $group_id_arr
 * @param boolean $return_query
 * @param string $emp_status
 * 
 * @return string[int][int]
 */
function get_emp_list_by_group_filter($group_id_arr,$return_query=false,$emp_status='A'){
	//$return_query arg is not used.

	if(is_array($group_id_arr)){ $group_code=implode(',',$group_id_arr);}
	else $group_code=$group_id_arr;
	$without_dummy=" a.gem_emp_id!= ".SALES_DUMMY_ID;
	$query_gr="select distinct(a.gem_emp_id) eid,gem_emp_name,gem_mobile from gft_emp_master a" .
			" left join gft_role_group_master rg on (grg_role_id=gem_role_id)" .
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id)" .
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id)" .
			" where $without_dummy  ".($emp_status!=''?" and a.gem_status='A'":"");
	if($group_code!=''){ $query_gr.=" and ggm_group_id in ($group_code) "; }
	//if($return_query==true){ return $query_gr." order by gem_emp_name "; }
	return get_two_dimensinal_array_from_query($query_gr,'eid','gem_emp_name');
}

/**
 * @param int $type
 * @param boolean $select_any
 * 
 * @return string[int][int] 
 */
function get_b_map_view($type,$select_any=false){
	$i=0;$map_id=/*. (string[int][int]) .*/array();
	if($select_any==true){
		$map_id[$i][0]='0';
		$map_id[$i][1]='Any';
		$i++;
	}
	if($type==2){	
		$map_id=get_two_dimensinal_array_from_table('b_map_view','terr_id','terr');
	}else if($type==3){
		$select_query="select area_id,area from b_map_view group by area_id ";
		$map_id=get_two_dimensinal_array_from_query($select_query,'area_id','area');
	}
	return $map_id;
}

/**
 * @param string $status
 * @param bool $select_any
 * @param string $default_value
 * @param string $group_filter
 * 
 * @return string[int][int]
 */
function get_status($status=null,$select_any=true,$default_value=null,$group_filter=null){
	$query_status="select GTM_CODE, GTM_NAME, GTM_DESC, prob, GTM_STATUS, GTM_GROUP_ID,GMG_GROUP_NAME  " .
			"from gft_status_master join gft_status_group_master on (GMG_GROUP_ID=GTM_GROUP_ID) ";
	if($status=='A'){ 
	$query_status.=" where GTM_STATUS='A' ";}
	if($group_filter!=null){
		$query_status.=" and GTM_GROUP_ID in (".implode(',',$group_filter).") ";
	}
	$query_status.=" ORDER BY GTM_GROUP_ID, GTM_NAME ";
    $result_status=execute_my_query($query_status,'Support_util.php',true,false,2);  
    $i=0;
    $status_list=/*. (string[int][int]) .*/ array();
    if($select_any==true){
    	$status_list[$i][0]='0';
 		$status_list[$i][1]="-Select-";
 		$status_list[$i][2]='';
		 $status_list[$i][3]='';
  		$i++;
    }
    if($default_value!=null and is_array($default_value)){
    	$status_list[$i][0]=$default_value[0];
		 $status_list[$i][1]=$default_value[1];
		 $status_list[$i][2]='';
		 $status_list[$i][3]='';
		 $i++;
    }
	while($qdata=mysqli_fetch_array($result_status)){
		 $status_list[$i][0]=$qdata['GTM_CODE'];
		 $status_list[$i][1]=$qdata['GTM_NAME'];
		 $status_list[$i][2]=$qdata['GMG_GROUP_NAME'];
		 $status_list[$i][3]=$qdata['GTM_GROUP_ID'];
		 $i++;
	}
	return $status_list;
}

/**
 * @param string $status
 * @param string $complaint_code
 * 
 * @return string[int][int]
 */
 
function get_complaint($status=null,$complaint_code=null){
	$query_complaint="select GFT_COMPLAINT_CODE, GFT_COMPLAINT_DESC, GCG_GROUP_NAME,(GFT_RESTORATION_TIME)as restoration, " .
			" (GFT_VISIT_RESTORATION_UNITS*GFT_VISIT_RESTORATION_TIME) as restoration_visit," .
			" (GFT_RESOLUTION_TIME)as resolution,GFT_COMPLAINT_GROUP," .
			" ifnull(GCG_STATUS,'A') GCG_STATUS, gem_emp_name expert, GPM_NAME,GFT_FAIL_FAST_TIME,GFT_ENABLE_DEFAULT_CONTENT," .
			" GFT_PROBLEM_SUMMARY,GFT_PROBLEM_DESCRIPTION,GFT_ADDITIONAL_INFO".
			" from gft_complaint_master " .
			" left join gft_complaint_group_master cgm on (GCG_GROUP_ID=GFT_COMPLAINT_GROUP) " .
			" left join gft_emp_master em on (gem_emp_id=gft_emp_id and gem_status='A') " .
			" left join gft_process_master pm on (GPM_CODE=GFT_PROCESS_CODE) where 1 ";
	if($status=='A'){
		$query_complaint.=" and GFT_STATUS='A' ";
	}
	if(!empty($complaint_code)){
		$query_complaint.=" and GFT_COMPLAINT_CODE=$complaint_code ";
	}
	$query_complaint.=" order by GCG_GROUP_NAME,GFT_COMPLAINT_DESC ";
	$result_complaint=execute_my_query($query_complaint,'Support_util.php',true,false,2);  
    $i=0;
    $complaint_list=/*. (string[int][int]) .*/ array();
	while($qdata=mysqli_fetch_array($result_complaint)){
		 $complaint_list[$i][0]=$qdata['GFT_COMPLAINT_CODE'];
		 $complaint_list[$i][1]=$qdata['GFT_COMPLAINT_DESC'];
		 $complaint_list[$i][2]=$qdata['GCG_GROUP_NAME'];
		 $complaint_list[$i][3]=$qdata['restoration'];
		 $complaint_list[$i][4]=$qdata['restoration_visit'];
		 $complaint_list[$i][5]=$qdata['resolution'];
		 $complaint_list[$i][6]=$qdata['expert'];
		 $complaint_list[$i][7]=$qdata['GPM_NAME'];
		 $complaint_list[$i][8]=$qdata['GFT_FAIL_FAST_TIME'];
		 $complaint_list[$i][9]=$qdata['GFT_COMPLAINT_GROUP'];
		 $complaint_list[$i][10]=$qdata['GFT_ENABLE_DEFAULT_CONTENT'];
		 $complaint_list[$i][11]=$qdata['GFT_PROBLEM_SUMMARY'];
		 $complaint_list[$i][12]=$qdata['GFT_PROBLEM_DESCRIPTION'];
		 $complaint_list[$i][13]=$qdata['GFT_ADDITIONAL_INFO'];
		 $i++;
	}
	return $complaint_list;
}

/**
 * @param string $cust_id
 *
 * @return string
 */
function getQueryContactDtlInnerFor($cust_id){
	global $contact_fields;
	$inner_query =" inner join (select gcc_lead_code  $contact_fields " .
	"from gft_customer_contact_dtl ccd " .
	" where  GCC_LEAD_CODE='".$cust_id."' ".
	"group by gcc_lead_code )" .
	"ccd on (GCC_LEAD_CODE=GLH_LEAD_CODE) ";
	return $inner_query;
}

/**
 * @param string $cust_id
 * @param string $act_id
 * 
 * @return string[string][int]
 */
function send_customer_details_to_exec($cust_id,$act_id=''){
	global $only_address_fields,$contact_fields_list,$GLH_VERTICAL_CODE;
		   //" $query_contact_dtl_inner " .
	$vertical_con = "glh_vertical_code";
	if(isset($GLH_VERTICAL_CODE) && $GLH_VERTICAL_CODE!="" && $GLH_VERTICAL_CODE!="1001"){
		$vertical_con = "$GLH_VERTICAL_CODE";
	}
	$query="select GLH_LEAD_CODE $only_address_fields $contact_fields_list ," .
		   " GTM_VERTICAL_NAME, group_concat(GPM_PRODUCT_ABR) productname," .
		   " concat(GLS_SOURCE_NAME,'   ',GLH_REFERREDBY) lead_source,glh_lfd_emp_id ,gem_emp_name," .
		   " ccm.GCC_NAME 'CREATED_CATEGORY',if(GLH_STATUS=3,lm2.GCL_NAME,lm1.GCL_NAME) as cust_lift_cycle_status " .
		   " from gft_lead_hdr " .
		   " join gft_vertical_master vtm on ($vertical_con=gtm_vertical_code) " .
		   " join gft_lead_create_category ccm on (ccm.GCC_ID=GLH_CREATED_CATEGORY) ".
		   " join gft_customer_status_master on (GCS_CODE=GLH_STATUS) ".
		   " left join gft_prospects_status_master on (GPS_STATUS_ID=GLH_PROSPECTS_STATUS) ".
		   " left join gft_customer_lifecycle_master lm1 on (lm1.GCL_ID=GCS_CUST_LIFECYCLE) ".
		   " left join gft_customer_lifecycle_master lm2 on (lm2.GCL_ID=GPS_CUST_LIFECYCLE) ".
		   " left join gft_lead_product_dtl lp on (glc_lead_code=glh_lead_code  and GLC_INTEREST_LEVEL='Y')" .
		   " left join gft_product_family_master pfm on (glc_product_code=gpm_product_code) " .
		   " " . getQueryContactDtlInnerFor($cust_id) .
		   " left join gft_lead_source_master lsm on (GLS_SOURCE_CODE=GLH_LEAD_SOURCECODE) " .
		   " left join gft_emp_master em on (gem_emp_id=glh_lfd_emp_id ) ".
		   " where GLH_LEAD_CODE='$cust_id' group by GLH_LEAD_CODE  ";
	
	$result=execute_my_query($query);
	$qdata=mysqli_fetch_array($result);
	
	$order_detail="select god_order_no,gop_product_code,gop_product_skew ,gpm_product_abr,gpg_version,GPT_TYPE_NAME " .
			" from gft_order_hdr join gft_order_product_dtl on (gop_order_no=god_order_no)" .
			" join gft_product_family_master pfm on (gop_product_code=pfm.gpm_product_code)" .
			" join gft_product_master pm on (pfm.gpm_product_code=pm.gpm_product_code and gop_product_skew=gpm_product_skew)" .
			" join gft_product_type_master ptm on (gpm_product_type=GPT_TYPE_ID) " .
			" join gft_product_group_master on(gop_product_code=gpg_product_family_code and gop_product_skew like concat(gpg_skew,'%'))  " .
			"where god_lead_code='$cust_id' " ;
	$result1=execute_my_query($order_detail);
	$order_dtl_s=0;
	$rs_order='';
	while($qdata1=mysqli_fetch_array($result1)){
		if($order_dtl_s!=0) {$rs_order.="\n";}
		$rs_order.="Order No: ".$qdata1['god_order_no'].
				   " Product : ".$qdata1['gpm_product_abr']." ".$qdata1['gpg_version'].' '.$qdata1['GPT_TYPE_NAME'];
		$order_dtl_s++;
	}
	$next_action_dtl="";
	if($act_id!=""){
		$result_next_action = 	execute_my_query(" select GAM_ACTIVITY_DESC,GLD_CUST_FEEDBACK,GLD_NEXT_ACTION_DATE,GEM_EMP_NAME  from gft_activity ".
								" inner join gft_emp_master em ON(gem_emp_id=GLD_EMP_ID) ".
								" inner join gft_activity_master ON(GLD_NEXT_ACTION=GAM_ACTIVITY_ID) ".
								" where GLD_ACTIVITY_ID='$act_id' and GLD_NEXT_ACTION!=0");
		if($row_next_action=mysqli_fetch_array($result_next_action)){
			$next_action_dtl = "<b>Appointment Details</b><table border=1>
								<tr><th>Appointment</th><td>".$row_next_action['GAM_ACTIVITY_DESC']."</td></tr>
								<tr><th>Appointment Date</th><td>".$row_next_action['GLD_NEXT_ACTION_DATE']."</td></tr>
								<tr><th>Remarks</th><td>".$row_next_action['GLD_CUST_FEEDBACK']."</td></tr>
								<tr><th>Appointment owner</th><td>".$row_next_action['GEM_EMP_NAME']."</td></tr>
								</table>";
		}
		
	}
	$customer_details=/*. (string[string][int]) .*/ array();
	$customer_details=array('Customer_Id' => array($cust_id), 
        'Product_Name' => array((string)$qdata['productname']),
        'Vertical' => array($qdata['GTM_VERTICAL_NAME']),
        'Lead_Source'=>array($qdata['lead_source']),
        'Customer_Name'=>array($qdata['Contact_Person']),
        'Mobile' =>array($qdata['MOBILE']),
        'Bussno' =>array($qdata['BUSSNO']),
        'Resno' =>array($qdata['RESNO']),
        'Email' =>array($qdata['EMAIL']),
        'Fax' =>array ($qdata['FAX']),
        'Website'=>array($qdata['WEBSITE']),
        'Shop_Name'=>array($qdata['GLH_CUST_NAME']),
        'Door_No'=> array($qdata['GLH_DOOR_APPARTMENT_NO']),
        'Block_Society'=>array($qdata['GLH_BLOCK_SOCEITY_NAME']),
        'Street_No'=>array($qdata['GLH_STREET_DOOR_NO']),
        'Street_Name'=>array($qdata['GLH_CUST_STREETADDR1']),
        'Location'=>array($qdata['GLH_CUST_STREETADDR2']),
        'Landmark'=>array($qdata['GLH_LANDMARK']),
        'Area'=>array($qdata['GLH_AREA_NAME']),
        'City'=>array($qdata['GLH_CUST_CITY']),
        'State'=>array((string)$qdata['GLH_CUST_STATECODE']),
        'Pincode'=>array($qdata['GLH_CUST_PINCODE']),
        'Country'=>array($qdata['GLH_COUNTRY']),
        'Lead_Created_Date'=>array($qdata['GLH_CREATED_DATE']),
        'Order_Dtl'=>array($rs_order),
        'lead_field_exec' => array($qdata['glh_lfd_emp_id']),
        'Lead_Incharge'=> array($qdata['gem_emp_name']),
        'Created_Category' =>array($qdata['CREATED_CATEGORY']),
		'Lead_Life_Cycle_Status'=>array($qdata['cust_lift_cycle_status']),
		'Appointment_Details' =>array($next_action_dtl)			
        );
     
		return $customer_details;
}
/**
 * @param string $country_code
 *
 * @return string
 */
function get_country_name_using_code($country_code) {
	$country_name = $country_code;
	$query_country="SELECT GPM_MAP_ID,GPM_MAP_NAME  FROM gft_political_map_master  where GPM_MAP_TYPE='C' and GPM_MAP_ID>1 ".
			(strlen($country_code)==2? " and GPM_COUNTRY_CODE='$country_code' ": "and GPM_COUNTRY_CODE_2='$country_code' ");
	$result_rows = execute_my_query($query_country);
	if(mysqli_num_rows($result_rows)>0 && $row_country=mysqli_fetch_array($result_rows)){
		$country_name = $row_country['GPM_MAP_NAME'];
	}
	return $country_name;
}
/**
 * @param string $product_code
 *
 * @return int
 */
function get_vertical_id_using_product_code($product_code){
	$vertical_id = 0;
	$result_vertical = execute_my_query("select GPM_VERTICAL_ID from gft_product_family_master where GPM_PRODUCT_CODE='$product_code'");
	if($row_vertical=mysqli_fetch_array($result_vertical)){
		$vertical_id = (int)$row_vertical['GPM_VERTICAL_ID'];
	}
	return $vertical_id;
}
/**
 * @param string $alfa_country_code
 * 
 * @return string[int][int]
 */
function get_country_name_code($alfa_country_code) {
	$query_country="SELECT GPM_MAP_ID,GPM_MAP_NAME  FROM gft_political_map_master  where GPM_MAP_TYPE='C' and GPM_MAP_ID>1 ".
			(strlen($alfa_country_code)==2? " and GPM_COUNTRY_CODE='$alfa_country_code' ": "and GPM_COUNTRY_CODE_2='$alfa_country_code' ");
	return get_two_dimensinal_array_from_query($query_country,'GPM_MAP_ID','GPM_MAP_NAME');
}

/**
 * @param string $skew_id
 * 
 * @return string[int][int]
 */
function get_skew_master_list($skew_id=null){
	$query=" SELECT GSPM_CODE, GSPM_DESC FROM gft_skew_property_master ".  
	($skew_id!=null?" WHERE GSPM_CODE=$skew_id ":"");
	return get_two_dimensinal_array_from_query($query,'GSPM_CODE', 'GSPM_DESC');
}

/**
 * @param string $group_id
 * 
 * @return string[int][string]
 */

function get_detail_of_group($group_id){
     $query="select a.gem_emp_id,a.gem_mobile,a.gem_email from gft_emp_master a" .
			" left join gft_role_group_master rg on (grg_role_id=gem_role_id) " .
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id)" .
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id)" .
			" where gem_status='A' and ggm_group_id in ($group_id)   group by a.gem_emp_id ";
	 $result=execute_my_query($query);
	 $edtl=/*. (string[int][string]) .*/ array();
	 if($result){
	 	$i=0;
	 	while($qd=mysqli_fetch_array($result)){
	 		$edtl[$i]['eid']=$qd['gem_emp_id'];
	 		$edtl[$i]['mobile']=$qd['gem_mobile'];
	 		$edtl[$i]['email']=$qd['gem_email'];
	 		$i++;
		}
	}
	return $edtl;
	
}

/**
 * @param int $add_yrs
 * @param string $order_date
 * @param boolean $two_digits
 * @return string
 */
function get_fin_year($order_date,$add_yrs=0,$two_digits=false) {
	$y1 = date('Y',strtotime(date($order_date)));
	if($two_digits) {
		$y1 = substr($y1,-2);
	}
	$y1 = $y1;
	$y1 += $add_yrs;
	$y2 = (int)$y1 + 1;
	return "$y1-$y2";
}
/**
 * @param string $quotation_no
 * @param string $type
 * 
 * @return string
 */
function get_query_for_quotation_pdf($quotation_no,$type='') {
	$query= " SELECT GQH_ORDER_NO, GQH_LEAD_CODE, GQH_EMP_ID, GQH_ORDER_DATE, GQH_ORDER_AMT,GQH_QUOTATION_TO, ".
			" concat(GLH_CUST_NAME,'<br /> ',if(GLH_DOOR_APPARTMENT_NO!='',concat(GLH_DOOR_APPARTMENT_NO,','),''), " .
			" if(GLH_BLOCK_SOCEITY_NAME!='', concat(GLH_BLOCK_SOCEITY_NAME,','),''), " .
			" if(GLH_STREET_DOOR_NO!='',concat(GLH_STREET_DOOR_NO,','),''), " .
			" if(GLH_CUST_STREETADDR1!='',concat(GLH_CUST_STREETADDR1,','),''), '<br />  ', " .
			" if(GLH_CUST_STREETADDR2!='' and GLH_CUST_STREETADDR2!=GLH_CUST_STREETADDR1, concat(GLH_CUST_STREETADDR2,','),''), " .
			" if(GLH_AREA_NAME!='' and GLH_AREA_NAME!=GLH_CUST_STREETADDR2, concat(GLH_AREA_NAME,','),''),'<br />  ', " .
			" if(GLH_CUST_CITY!='' and GLH_AREA_NAME!=GLH_CUST_CITY, concat(GLH_CUST_CITY,','),''), " .
			" GLH_CUST_STATECODE,' Pin Code : ',GLH_CUST_PINCODE, '<br>',glh_country) customer_address, ".
			" GQH_CURRENCY_CODE,GQH_TYPE,GQM_TEMPLATE_ID,GQH_VERSION_NO, GLH_CUST_NAME,GLH_CUST_STATECODE,GLH_LEAD_TYPE,GLE_GST_NO " .
			" FROM gft_quotation_hdr join gft_lead_hdr on (GLH_LEAD_CODE=GQH_LEAD_CODE)" .
			" join gft_lead_hdr_ext on (gle_lead_code=glh_lead_code) ".
			" join gft_quotation_type_master on (GQH_TYPE=id)" .
			" WHERE GQH_ORDER_NO='$quotation_no' AND GQH_ORDER_STATUS='A'";
	if($type=='details') {
		$query = " SELECT p.GPM_DISPLAY_NAME pro_desc, concat(p.GPM_PRODUCT_CODE, p.GPM_PRODUCT_SKEW) pcode, " .
				 " round(g.GQP_SELL_RATE,2) GQP_SELL_RATE, g.GQP_TAX_RATE,g.GQP_SER_TAX_RATE, " .
				 " g.GQP_QTY, round(g.GQP_DISCOUNT_AMT,2) GQP_DISCOUNT_AMT, g.GQP_SELL_AMT, " .
				 " g.GQP_TAX_AMT,g.GQP_SER_TAX_AMT, p.GFT_SKEW_PROPERTY ,GQP_LIST_PRICE,GQP_COUPON_HOUR,GPM_TRAINING_HRS, ".
				 " if(gft_skew_property in (".MAIN_SERVER_ORDER_ENTRY.",2),'Y','N') main_server_order,".
				 " concat(p.GFT_HIGHER_PCODE, p.GFT_HIGHER_SKEW) ucode,p.GPM_PRODUCT_CODE, p.GPM_PRODUCT_SKEW,p.GPM_PRODUCT_TYPE, " .
				 " GQP_CGST_PER,GQP_SGST_PER,GQP_IGST_PER,GQP_CGST_AMT,GQP_SGST_AMT,GQP_IGST_AMT, p.GPM_TERMS ".
				 " FROM gft_quotation_product_dtl g, gft_product_master p,gft_product_family_master f " .
				 " where p.GPM_PRODUCT_CODE=g.GQP_PRODUCT_CODE and f.GPM_PRODUCT_CODE=g.GQP_PRODUCT_CODE " .
				 " AND p.GPM_PRODUCT_SKEW=g.GQP_PRODUCT_SKEW AND g.GQP_ORDER_NO='$quotation_no' " .
				 " order by GQP_PRINT_ORDER,GFT_SKEW_PROPERTY,gpm_display_order ";
	}
	return $query;
}
/**
 * @param string[int] $skew_property
 * @param string[int] $product_type
 * @param string[int] $pcode
 * @param string[int] $pskew
 * @param string[int] $up_product_code
 * @param string[int] $product_code
 * @param string[int] $qty_arr
 * @param string $lead_type
 * @param string $GQH_CURRENCY
 * @param boolean $merge_client
 * @param string $lead_code
 * 
 * @return mixed[string]
 */
function get_asa_dtls_for_pdf($skew_property,$product_type,$pcode,$pskew,$up_product_code,$product_code,$qty_arr,$lead_type,$GQH_CURRENCY,$merge_client=false,$lead_code='') {
 	global $global_gst_mode;
	$ret_arr = /*.(mixed[string]).*/array();
	$ret_arr['asa_rows'] = /*.(string[string]).*/array();
	$client_dtl_row = /*.(string[string]).*/array();
	$mn = 0;
	$server_index = -1;
	$asa_amount_for_customlic	=	get_samee_const("SAM_ASA_AMT_FOR_CUSTLIC");
	$gst_tax_joins = " left join gft_hsn_vs_tax_master on (GHT_ID=GPM_TAX_ID) ".
			" left join gft_tax_type_master on (GTM_ID=GHT_TAX_ID) ".
	        " join gft_product_family_master fm  on (fm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
	        " join gft_product_group_master on (gpg_product_family_code=gpm_head_family and gpg_skew=substr(GPM_PRODUCT_SKEW,1,4)) ".
	        " left join gft_lead_hdr on (GLH_LEAD_CODE='$lead_code') ".
	        " left join gft_brand_product_mapping on (GBP_VERTICAL=GLH_VERTICAL_CODE and GBP_PRODUCT=concat(pm.GPM_PRODUCT_CODE,'-',gpg_skew) and GBP_EDITION=pm.GPM_PRODUCT_TYPE and GBP_STATUS=1) ".
	        " left join gft_product_brand_master on (GPB_ID=GBP_BRAND_ID) ";
	$asa_sno = 1;
	$asa_total = $asa_gst_tax_amt = $asa_service_tax = 0;
	$asa_service_tax_rate=$asa_gst_tax_rate="0";
	$sel_columns = " if(GPB_NAME is null,pm.GPM_SKEW_DESC,concat(GPB_NAME,' ',pm.GPM_SKEW_DESC)) GPM_SKEW_DESC, ".
	               " GPM_LIST_PRICE, GPM_SERVISE_TAX_PERC, GPM_TAX_PERC, GPM_NET_RATE,GPM_USD_RATE,GTM_TAX_PER,pm.GPM_PRODUCT_CODE ";
	for($i=0;$i<count($skew_property);$i++) {
		$asa_product_query="";
		if( ($skew_property[$i]=='1') || ($skew_property[$i]=='3')){
			$asa_product_query=" select $sel_columns from gft_product_master pm $gst_tax_joins ".
					           " WHERE GFT_SKEW_PROPERTY=4  AND CONCAT(pm.GPM_PRODUCT_CODE,GPM_REFERER_SKEW)='$product_code[$i]' ";
		}else if($skew_property[$i]=='2'){
			$asa_product_query=	" select $sel_columns from gft_product_master pm $gst_tax_joins ".
			                    " WHERE GFT_SKEW_PROPERTY=4  AND CONCAT(pm.GPM_PRODUCT_CODE,GPM_REFERER_SKEW)='$up_product_code[$i]'";
		}else if($skew_property[$i]=='11' && $product_type[$i]=='8' ){
			$tax_column = "GPM_SERVISE_TAX_PERC";
			if($global_gst_mode==1){
				$tax_column = "GTM_TAX_PER";
			}
			$asa_product_query = " select if(GPB_NAME is null,pm.GPM_SKEW_DESC,concat(GPB_NAME,' ',pm.GPM_SKEW_DESC)) GPM_SKEW_DESC,(((GPM_NET_RATE*($asa_amount_for_customlic/100)))*(100/(100+$tax_column))) GPM_LIST_PRICE, ".
								 " GPM_SERVISE_TAX_PERC, GPM_TAX_PERC,(GPM_NET_RATE*($asa_amount_for_customlic/100)) GPM_NET_RATE,".
								 " (GPM_USD_RATE*($asa_amount_for_customlic/100)) GPM_USD_RATE,GTM_TAX_PER,pm.GPM_PRODUCT_CODE ".
								 " from gft_product_master pm $gst_tax_joins WHERE GFT_SKEW_PROPERTY=11  ".
								 " AND pm.GPM_PRODUCT_CODE=$pcode[$i] AND GPM_PRODUCT_SKEW='$pskew[$i]'";
		}
		if($asa_product_query!="" && ($lead_type=="1" || $lead_type=="3")){
			$asa_rows=execute_my_query($asa_product_query);
			while($asa_row=mysqli_fetch_array($asa_rows)){
				$asa_sell_price=$qty_arr[$i]*$asa_row['GPM_USD_RATE'];
				$asa_sell_rate=$asa_row['GPM_USD_RATE'];
				if($GQH_CURRENCY!='USD'){
					$asa_sell_price=$qty_arr[$i]*$asa_row['GPM_LIST_PRICE'];
					$asa_sell_rate=$asa_row['GPM_LIST_PRICE'];
					$asa_service_tax+=$qty_arr[$i]*($asa_row['GPM_NET_RATE']-$asa_row['GPM_LIST_PRICE']);
					$asa_gst_tax_amt+=$qty_arr[$i]*($asa_row['GPM_NET_RATE']-$asa_row['GPM_LIST_PRICE']);
					$asa_service_tax_rate=$asa_row['GPM_SERVISE_TAX_PERC'];
					$asa_gst_tax_rate=$asa_row['GTM_TAX_PER'];
				}
				if((int)$asa_sell_rate==0){
					continue;
				}
				$this_row = /*.(string[string]).*/array();
				$this_row['ASA_SNO'] = "$asa_sno";
				$this_row['ASA_PRODUCT_NAME'] = $asa_row['GPM_SKEW_DESC'];
				$this_row['ASA_GQP_SELL_RATE'] = number_format((float)$asa_sell_rate,2);
				$this_row['ASA_GQP_QTY'] = $qty_arr[$i];
				$this_row['ASA_GQP_SELL_AMOUNT'] = number_format($asa_sell_price,2);
				$this_row['GPM_PRODUCT_CODE'] = $asa_row['GPM_PRODUCT_CODE'];
				if( ($merge_client) && ($skew_property[$i]=='3') ){
					$client_dtl_row = $this_row;
				}else {
					$client_set = (isset($client_dtl_row['GPM_PRODUCT_CODE'])) && ($client_dtl_row['GPM_PRODUCT_CODE']==$this_row['GPM_PRODUCT_CODE']); 
					if( ($merge_client) && ($skew_property[$i]=='1') && ($client_set || !isset($client_dtl_row['GPM_PRODUCT_CODE'])) ){
						$server_index = $mn;
					}
					$ret_arr['asa_rows'][$mn] = $this_row;
					$mn++;
				}
				$asa_total+=$asa_sell_price;
				$asa_sno++;
			}
		}
	}
	if( $merge_client && ($server_index > -1) && (count($client_dtl_row) > 0) ){
		$ret_arr['asa_rows'][$server_index]['ASA_PRODUCT_NAME'] .=  " + ".$client_dtl_row['ASA_GQP_QTY']. " qty of ".$client_dtl_row['ASA_PRODUCT_NAME'];
		$ret_arr['asa_rows'][$server_index]['ASA_GQP_SELL_RATE'] = (float)str_replace(",","",$ret_arr['asa_rows'][$server_index]['ASA_GQP_SELL_RATE']) + ((float)str_replace(",","",$client_dtl_row['ASA_GQP_SELL_RATE'])*$client_dtl_row['ASA_GQP_QTY']);
		$ret_arr['asa_rows'][$server_index]['ASA_GQP_SELL_AMOUNT'] = (float)str_replace(",","",$ret_arr['asa_rows'][$server_index]['ASA_GQP_SELL_AMOUNT']) + (float)str_replace(",","",$client_dtl_row['ASA_GQP_SELL_AMOUNT']);
	}
	if((float)$asa_service_tax_rate>0) {
		$asa_total += $asa_service_tax;
	} else {
		$asa_total += $asa_gst_tax_amt;
	}
	$ret_arr['asa_total'] = "$asa_total";
	$ret_arr['ASA_SERVICE_TAX_PER'] = $asa_service_tax_rate;
	$ret_arr['ASA_SERVICE_TAX_AMOUNT'] = "$asa_service_tax";
	$ret_arr['ASA_GST_TAX_PER'] = $asa_gst_tax_rate;
	$ret_arr['ASA_GST_TAX_AMOUNT'] = "$asa_gst_tax_amt";
	return $ret_arr;
}
/**
 * @param string $quotation_no
 * 
 * @return string
 */
function generate_quotation($quotation_no){
	global $f_name,$global_gst_mode;
	$query = get_query_for_quotation_pdf($quotation_no,'');
	$db_document_content_config=array();		
	$result=execute_my_query($query);
	$GQH_EMP_ID= $GQH_CURRENCY = '';
	$lead_type="";
	if($data=mysqli_fetch_array($result)){
		$db_document_content_config['Quotation_No']=$data['GQH_ORDER_NO'];
		$db_document_content_config['customer_address']=$data['customer_address'];
		$db_document_content_config['tax_mode']=($data['GLH_CUST_STATECODE']=='Tamil Nadu'?'VAT':'CST');
		$GQH_LEAD_CODE=$data['GQH_LEAD_CODE'];
		$GQH_EMP_ID=$data['GQH_EMP_ID'];
		$GQH_ORDER_DATE=$data['GQH_ORDER_DATE'];
		$GQH_QUATION_TYPE=$data['GQH_TYPE'];
		$GQH_CURRENCY=$data['GQH_CURRENCY_CODE'];
		$template_id=$data['GQM_TEMPLATE_ID'];
		$GQH_VERSION_NO=$data['GQH_VERSION_NO'];
		$customer_name=$data['GQH_QUOTATION_TO'];
		$lead_type=$data['GLH_LEAD_TYPE'];
	}else{
		return 'unable to get the required data.';
	}
	$excnamedesc=get_emp_name_desc($GQH_EMP_ID);
/*	$contactquery=" SELECT distinct(GCC_CONTACT_NAME) FROM gft_customer_contact_dtl WHERE GCC_LEAD_CODE=$GQH_LEAD_CODE AND gcc_designation=1 and GCC_CONTACT_NAME!='' group by GCC_LEAD_CODE";
	$result_contact=execute_my_query($contactquery);
	if($datacontact=mysqli_fetch_array($result_contact)){
		$customer_name=$datacontact[0];
	}
	if($customer_name==''){
		$contactquery=" SELECT distinct(GCC_CONTACT_NAME) FROM gft_customer_contact_dtl WHERE GCC_LEAD_CODE=$GQH_LEAD_CODE AND gcc_designation in (2,3,4) and GCC_CONTACT_NAME!='' group by GCC_LEAD_CODE";
		$result_contact=execute_my_query($contactquery);
		if($datacontact=mysqli_fetch_array($result_contact)){
			$customer_name=$datacontact[0];
		}	
	} */
	$conversion = 1;
	$db_document_content_config['customer_gstin_label'] = "GSTIN";
	$db_document_content_config['company_gstin']=get_single_value_from_single_table("GCM_GST_NO", "gft_company_master", "gcm_id", "1");
	if($GQH_CURRENCY=='USD'){
	    $db_document_content_config['customer_gstin_label'] = "IEC No";
	    $db_document_content_config['company_gstin']=get_single_value_from_single_table("GCM_EIC_NO", "gft_company_master", "gcm_id", "1");
		$conversion = (int)get_samee_const("UNIV_PRICE_CONVERSION");
	}
	$arraystr=explode('-',$GQH_ORDER_DATE);
	$strdate=date("M j\<\s\u\p\>S\<\/\s\u\p\> Y", mktime(0, 0, 0, (int)$arraystr[1], (int)$arraystr[2], (int)$arraystr[0]));
	$strdatetoday=date("M j\<\s\u\p\>S\<\/\s\u\p\> Y", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
	$pagebreak='<div style="page-break-after: always;"><span style="display: none;"> </span></div>';
	$pagebreakpdf='<!-- NEW PAGE -->';
	$query = get_query_for_quotation_pdf($quotation_no,'details');
	$result=execute_my_query($query);
	$sno=1;
	$sno1=1;
	$asa_sno=1;
	$total1=0;
	$total2=0;
	$asa_service_tax=$asa_total=$asa_gst_tax_amt=0;
	$asa_service_tax_rate=$asa_gst_tax_rate="0";
	$GQP_TAX_RATE='';
	$GQP_SER_TAX_RATE='';

	$itemdesc1='';$itemdesc2='';
	$db_document_content_config['itemdesc1']=array();
	$db_document_content_config['itemdesc2']=array();
	$total_vat_tax_amt_license=0;$total_vat_tax_amt_service=$total_gst_amt1=$total_gst_amt2=0;
	$total_service_tax_amt_license=0;$total_service_tax_amt_service=0;$total_coupon_hrs=0;
	$skew_property = $qty_arr = $product_type = $pcode_arr = $pskew_arr = $up_product_code_arr = $product_code_arr = /*.(string[int]).*/array();
	while($data=mysqli_fetch_array($result)){
		$product_name=$data['pro_desc'];
		$product_code=$data['pcode'];
		$pcode=$data['GPM_PRODUCT_CODE'];
		$pskew=$data['GPM_PRODUCT_SKEW'];// TO get ASA price for additional client
		$up_product_code=$data['ucode'];
		$GQP_SELL_RATE=round($data['GQP_SELL_RATE']/$conversion,2);
		$GQP_SER_TAX_RATE=$data['GQP_SER_TAX_RATE'];
		$GQP_QTY=$data['GQP_QTY'];
		$GQP_SELL_AMT=round($data['GQP_SELL_AMT']/$conversion,2);
		$GQP_TAX_AMT=round($data['GQP_TAX_AMT'],2);
		$GQP_SER_TAX_AMT=round($data['GQP_SER_TAX_AMT'],2);
		$GQP_COUPON_HOUR	= (int)$data['GQP_COUPON_HOUR'];
		$GQP_SELL_AMT_WITHOUT_TAX=$GQP_SELL_RATE*$GQP_QTY;
		$skewp=$data['GFT_SKEW_PROPERTY'];
		$training_hrs=($data['GPM_TRAINING_HRS']*$GQP_QTY);
		if($GQP_QTY==0 or $GQP_QTY==''){
			$GQP_QTY='As Per Need';
			$GQP_TAX_AMT='On Actuals';
			$GQP_SELL_AMT='On Actuals';
		}else if($GQP_COUPON_HOUR>0){
			$GQP_SELL_AMT_WITHOUT_TAX=$GQP_SELL_RATE*($GQP_QTY*$GQP_COUPON_HOUR);
			$GQP_QTY="$GQP_QTY x $GQP_COUPON_HOUR Hrs Coupon";
			$training_hrs=(int)$GQP_QTY*$GQP_COUPON_HOUR;
		}
		$gst_tax_rate = (float)$data['GQP_CGST_PER']+(float)$data['GQP_SGST_PER']+(float)$data['GQP_IGST_PER'];
		$total_coupon_hrs=$total_coupon_hrs+$training_hrs;
		if($data['main_server_order']=='Y'){			
			$GQP_TAX_RATE=$data['GQP_TAX_RATE'];
			$db_document_content_config['itemdesc1'][$sno]=array('sno'=>$sno,
															'product_name'=>$data['pro_desc'],
															'GQP_SELL_RATE'=>number_format($GQP_SELL_RATE,2),
															'GQP_QTY'=>$GQP_QTY,
															'GQP_SELL_AMOUNT'=>number_format($GQP_SELL_AMT_WITHOUT_TAX,2));
			$total1+=$GQP_SELL_AMT_WITHOUT_TAX;
			$total_vat_tax_amt_license+=$GQP_TAX_AMT;
			$total_service_tax_amt_license+=$GQP_SER_TAX_AMT;
			$total_gst_amt1 += (float)$data['GQP_CGST_AMT']+(float)$data['GQP_SGST_AMT']+(float)$data['GQP_IGST_AMT'];
			$sno++;
		}else{
			$db_document_content_config['itemdesc2'][$sno1]=array('sno1'=>$sno1,
															'product_name'=>$data['pro_desc'],
															'GQP_SELL_RATE'=>number_format($GQP_SELL_RATE,2),
															'GQP_QTY'=>$GQP_QTY,
															'GQP_SELL_AMOUNT'=>number_format($GQP_SELL_AMT_WITHOUT_TAX,2));
			$total2+=$GQP_SELL_AMT_WITHOUT_TAX;
			$total_vat_tax_amt_service+=$GQP_TAX_AMT;
			$total_service_tax_amt_service+=$GQP_SER_TAX_AMT;
			$total_gst_amt2 += (float)$data['GQP_CGST_AMT']+(float)$data['GQP_SGST_AMT']+(float)$data['GQP_IGST_AMT'];
			$sno1++;
		}
		$skew_property[] = $data['GFT_SKEW_PROPERTY'];
		$product_type[] = $data['GPM_PRODUCT_TYPE'];
		$pcode_arr[] = $pcode;
		$pskew_arr[] = $pskew;
		$up_product_code_arr[] = $up_product_code;
		$product_code_arr[] = $product_code;
		$qty_arr[] = $GQP_QTY;
	}
	$asa_dtls = get_asa_dtls_for_pdf($skew_property,$product_type,$pcode_arr,$pskew_arr,$up_product_code_arr,$product_code_arr,$qty_arr,$lead_type,$GQH_CURRENCY);
	if(isset($asa_dtls['asa_rows']) and count($asa_dtls['asa_rows'])>0) {
		$db_document_content_config['ASA_DESC'] = array();
		foreach ($asa_dtls['asa_rows'] as $k=>$dtls) {
			$db_document_content_config['ASA_DESC'][] = $dtls;
		}
		$asa_total += $asa_dtls['asa_total'];
		$ret_arr['asa_total'] = $asa_total;
		$asa_gst_tax_rate = $asa_dtls['ASA_GST_TAX_PER'];
		$asa_gst_tax_amt += $asa_dtls['ASA_GST_TAX_AMOUNT'];
		$asa_service_tax_rate = $asa_dtls['ASA_SERVICE_TAX_PER'];
		$asa_service_tax += $asa_dtls['ASA_SERVICE_TAX_AMOUNT'];
	}
	if(isset($db_document_content_config['ASA_DESC']) && count($db_document_content_config['ASA_DESC'])>0){
		if($GQH_CURRENCY=='INR'){
			if($global_gst_mode==1){
				$db_document_content_config['ASA_GST_TAX_PER'] = $asa_gst_tax_rate;
				$db_document_content_config['ASA_GST_TAX_AMOUNT']=number_format($asa_gst_tax_amt,2);
				$asa_total+=$asa_gst_tax_amt;
			}else{
				$db_document_content_config['ASA_SERVICE_TAX_PER'] = $asa_service_tax_rate;
				$db_document_content_config['ASA_SERVICE_TAX_AMOUNT']=number_format($asa_service_tax,2);
				$asa_total+=$asa_service_tax;
			}
		}
		$round_off_item1=round($asa_total,0);		
		$db_document_content_config['ASA_TOTAL_AMOUNT']=number_format(round($round_off_item1,2),2);
		if($asa_total!=$round_off_item1){
			if($asa_total > $round_off_item1){
				$db_document_content_config['ASA_ROUNDOFF_SYMBOL']=' - ';
				$db_document_content_config['ASA_ROUNDOFF_AMOUNT']=number_format(($asa_total - $round_off_item1),2);
			}
			else if($asa_total < $round_off_item1){
				$db_document_content_config['ASA_ROUNDOFF_SYMBOL']=' + ';
				$db_document_content_config['ASA_ROUNDOFF_AMOUNT']=number_format(($round_off_item1-$asa_total),2);
			}
		}
	}	
	
	if(count($db_document_content_config['itemdesc1'])>0){
		if($GQH_CURRENCY=='INR'){
			if($global_gst_mode==1){
				$db_document_content_config['Item1_gst_tax_per']=$gst_tax_rate;
				$db_document_content_config['Item1_gst_tax_amount']=number_format($total_gst_amt1,2);
				$total1+=$total_gst_amt1;
			}else{
				$db_document_content_config['Item1_license_tax_per']=$GQP_TAX_RATE;
				$db_document_content_config['Item1_license_tax_amount']=number_format($total_vat_tax_amt_license,2);
				$db_document_content_config['Item1_service_tax_per']=$GQP_SER_TAX_RATE;
				$db_document_content_config['Item1_service_tax_amount']=number_format($total_service_tax_amt_license,2);
				$total1+=$total_vat_tax_amt_license+$total_service_tax_amt_license;
			}
		}
		$round_off_item1=round($total1,0);
		$db_document_content_config['Item1_total_amount_before_roundoff']=number_format($total1,2);
		$db_document_content_config['Item1_total_amount']=number_format(round($round_off_item1,2),2);
	
		if($total1!=$round_off_item1){
			if($total1 > $round_off_item1){
				$db_document_content_config['Item1_roundoff_label']=' Less ';
				$db_document_content_config['Item1_roundoff_symbol']=' - ';				
				$db_document_content_config['Item1_total_amount_roundoff']=number_format(($total1 - $round_off_item1),2);
			}
			else if($total1 < $round_off_item1){
				$db_document_content_config['Item1_roundoff_label']=' Add ';
				$db_document_content_config['Item1_roundoff_symbol']=' + ';
				$db_document_content_config['Item1_total_amount_roundoff']=number_format(($round_off_item1-$total1),2);
			}	
		}
		
	}
	if(count($db_document_content_config['itemdesc2'])>0){
		if($GQH_CURRENCY=='INR'){
			if($global_gst_mode==1){
				$db_document_content_config['Item2_gst_tax_per']=$gst_tax_rate;
				$db_document_content_config['Item2_gst_tax_amount']=number_format($total_gst_amt2,2);
				$total2+=$total_gst_amt2;
			}else{
				$db_document_content_config['Item2_service_tax_per']=$GQP_SER_TAX_RATE;
				$db_document_content_config['Item2_service_tax_amount']=number_format($total_service_tax_amt_service,2);
				$total2+=$total_service_tax_amt_service;
			}
		}
		$round_off_item2=round($total2,0);
		$db_document_content_config['Item2_total_amount_before_roundoff']=number_format($total2,2);
		$db_document_content_config['Item2_total_amount']=number_format(round($round_off_item2,2),2);
		if($total2!=$round_off_item2){
			if($total2 > $round_off_item2){
				$db_document_content_config['Item2_roundoff_label']=' Less ';
				$db_document_content_config['Item2_roundoff_symbol']=' - ';
				$db_document_content_config['Item2_total_amount_roundoff']=number_format(($total2 - $round_off_item2),2);
			}
			else if($total2 < $round_off_item2){
				$db_document_content_config['Item2_roundoff_label']=' Add ';
				$db_document_content_config['Item2_roundoff_symbol']=' + ';
				$db_document_content_config['Item2_total_amount_roundoff']=number_format(($round_off_item2-$total2),2);
			}	
		}
	}
	/*
	$query_fc="select  GCT_IMAGE_PATH from gft_currency_type_master where gct_type='$GQH_CURRENCY' ";
	$result_fc=execute_my_query($query_fc);
	if($result_fc){
		$qd=mysqli_fetch_array($result_fc);
		$GQH_CURRENCY_IMAGE=$qd['GCT_IMAGE_PATH'];
	}
	*/
	
	$db_document_content_config['customer_name']=$customer_name;
	$db_document_content_config['customer_id']=$GQH_LEAD_CODE;
	$db_document_content_config['Quotation_date']=$strdate;
	$db_document_content_config['CURRENCY']=$GQH_CURRENCY;
	$db_document_content_config['financial_year']=get_fin_year($GQH_ORDER_DATE,1);
	$db_document_content_config['CURRENCY_IMAGE']=$GQH_CURRENCY;/* later we can add image */
	$db_document_content_config['execName']=$excnamedesc[0];
	$db_document_content_config['execMobileNo']=$excnamedesc[1];
	$db_document_content_config['execMailId']=$excnamedesc[2];
	$db_document_content_config['exceDesignation']=$excnamedesc[3];
	$key_name = 'Bank_Details_INR';
	if($GQH_CURRENCY=='USD'){
		$key_name = 'Bank_Details_USD';
	}
	$db_document_content_config['bankDetails']=get_samee_const($key_name);
	$db_document_content_config['total_training_hours']="";
	if($total_coupon_hrs>0){
		$db_document_content_config['total_training_hours']="Total service hours alloted : $total_coupon_hrs<br><b>Note:</b>Beyond the alloted service hours customers can pay and buy additional hours. Contact your sales manager for the same. ";
	} 	
	$quotation_content=get_formatted_document_content($db_document_content_config,1,$template_id);
	$f_name=generatequotationpdf($quotation_no,$quotation_content,$GQH_VERSION_NO);
	return $quotation_content;	
}

/**
 * @param int $receipt_id
 * @param bool $pre_print
 * 
 * @return string
 */
function generate_receipt_content($receipt_id,$pre_print){
	$query="SELECT glh_lead_code,date_format(GRD_DATE,'%d-%m-%Y') as GRD_DATE, rtm.GRT_TYPE_NAME receipt_type, rd.GRD_RECEIPT_TYPE, " .
			" GRD_RECEIPT_AMT, GRD_CHEQUE_DD_NO,date_format(GRD_CHEQUE_DD_DATE,'%d-%m-%Y') as GRD_CHEQUE_DD_DATE,GRD_BANK_NAME, " .
			" GRD_RECEIPT_ID_REF, date_format(GRD_COLLECTION_DATE,'%d-%m-%Y') as GRD_COLLECTION_DATE, GLH_CUST_NAME, GLH_CUST_STREETADDR2," .
			" group_concat(distinct if(gct_name is not null and gct_name!='',gct_name,GCM_COLLECTION_ABR)) reason,date_format(GRD_DEPOSITED_DATE,'%d-%m-%Y') as GRD_DEPOSITED_DATE, " .
			" GLH_LEAD_CODE, GRD_RECEIPT_ID_REF, concat(GLH_CUST_NAME,'<br />  ', " .
			" if(GLH_DOOR_APPARTMENT_NO!='',concat(GLH_DOOR_APPARTMENT_NO,','),''), " .
			" if(GLH_BLOCK_SOCEITY_NAME!='', concat(GLH_BLOCK_SOCEITY_NAME,',<br /> '),''), " .
			" if(GLH_STREET_DOOR_NO!='',concat(GLH_STREET_DOOR_NO,','),''), " .
			" if(GLH_CUST_STREETADDR1!='',concat(GLH_CUST_STREETADDR1,',<br />'),''), " .
			" if(GLH_CUST_STREETADDR2!='' and GLH_CUST_STREETADDR2!=GLH_CUST_STREETADDR1, concat(GLH_CUST_STREETADDR2,',<br />'),''), " .
			" if(GLH_AREA_NAME!='' and GLH_AREA_NAME!=GLH_CUST_STREETADDR2, concat(GLH_AREA_NAME,',<br />'),''), " .
			" if(GLH_CUST_CITY!='' and GLH_AREA_NAME!=GLH_CUST_CITY, concat(GLH_CUST_CITY,',<br />'),''), " .
			" GLH_CUST_STATECODE,' Pin Code : ',GLH_CUST_PINCODE, '<br>',glh_country) cusromer_address,GRT_TEMPLATE_ID,GRT_PREPRINT_TEMPLATE_ID, ".
			" glh_country,GRD_USD_AMT FROM gft_receipt_dtl rd " .
			" join gft_lead_hdr on (rd.grd_lead_code=glh_lead_code )" .
			" join gft_collection_receipt_dtl on (grd_receipt_id= gcr_receipt_id)" .
			" join gft_collection_reason_master on(gcr_reason=gcm_code) " .
			" left join gft_collection_type_master on (GRD_COLLECTION_TYPE=gct_id) ".
			" join gft_receipt_type_master rtm on (rtm.GRT_TYPE_CODE=rd.GRD_RECEIPT_TYPE) " .
			" WHERE GRD_STATUS IN ('P','C') AND GRD_CHECKED_WITH_LEDGER='Y' and GRD_REFUND_AMT=0 " .
			" and (GRD_RECEIPT_ID_REF!=0 and GRD_RECEIPT_ID_REF is not null) " .
			"  and grd_receipt_id=$receipt_id group by grd_receipt_id  ";
	$db_document_content_config=/*. (string[string]) .*/ array();
	$barcode_gen_url=get_samee_const('BARCODE_PATH');
	if($data=mysqli_fetch_array(execute_my_query($query))){
		$bar_code = "<br><IMG SRC=\"$barcode_gen_url?barcode=".$data['glh_lead_code']."-".$data['GRD_RECEIPT_ID_REF']."&width=210&height=25&text=1\" alt=\"barcode\" />";
		$db_document_content_config['receipt_date']=$data['GRD_DATE'];
		$db_document_content_config['receiptid_ref']=$data['GRD_RECEIPT_ID_REF'];
		$db_document_content_config['customer_id']=$data['GLH_LEAD_CODE'];
		$db_document_content_config['customer_name']=$data['GLH_CUST_NAME'];
		$db_document_content_config['customer_address']=$data['cusromer_address'];
		$db_document_content_config['total_amount']=$data['GRD_RECEIPT_AMT'];
		$db_document_content_config['total_amount_text']=SpellNumber($data['GRD_RECEIPT_AMT']);
		$db_document_content_config['collection_reason']=$data['reason'];
		$db_document_content_config['receipt_type']=$data['receipt_type'];
		$template_id = $data['GRT_TEMPLATE_ID'];
		if(strcasecmp($data['glh_country'],'India')!=0) {
			$db_document_content_config['company_gstin']=get_single_value_from_single_table("GCM_EIC_NO", "gft_company_master", "gcm_id", "1");
			$db_document_content_config['total_amount']=$data['GRD_USD_AMT'];
			$db_document_content_config['total_amount_text']=SpellNumber($data['GRD_USD_AMT'],true);
			$template_id = '35';
		}
		$db_document_content_config['collection_date']=($data['GRD_COLLECTION_DATE']!='')?$data['GRD_COLLECTION_DATE']:$data['GRD_DEPOSITED_DATE'];
		$reason_query="select group_concat(distinct GSPM_DESC) reason FROM gft_collection_receipt_dtl " .
			" left join gft_order_product_dtl on (gcr_order_no= gop_order_no) " .
			" left join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW)" .
			" left join gft_skew_property_master on (GSPM_CODE=GFT_SKEW_PROPERTY) " .
			" where gcr_receipt_id =$receipt_id and gcr_reason=1 having reason is not null ";
		$result_reason=execute_my_query($reason_query);	
		if($data_reason=mysqli_fetch_array($result_reason)){
			$db_document_content_config['receipt_reason']=$data_reason['reason'];
		}else{
			$db_document_content_config['receipt_reason']=$data['reason'];
		}
		if($data['GRD_RECEIPT_TYPE']!=1 and $data['GRD_RECEIPT_TYPE']!=4){ 
			$db_document_content_config['cheque_dd_no']=$data['GRD_CHEQUE_DD_NO'];
			$db_document_content_config['cheque_dd_date']=$data['GRD_CHEQUE_DD_DATE'];
			$db_document_content_config['bank_name']=$data['GRD_BANK_NAME'];
		}
	}
	if($db_document_content_config!=null){
		return get_formatted_document_content($db_document_content_config,3,($pre_print==true?$data['GRT_PREPRINT_TEMPLATE_ID']:$template_id));
	}else{
		return null;
	}
}

/**
 *
 * @param string $GPH_LEAD_CODE
 *
 * @return string
 */
function get_outstanding_details_as_table($GPH_LEAD_CODE){

	$outstanding_query= " SELECT GOD_ORDER_NO,GOD_ORDER_DATE,GOD_ORDER_AMT,GOD_ORDER_STATUS,cic.gem_emp_name incharge,ordby.gem_emp_name orderedby,GOD_COLLECTED_AMT, " .
			" GOD_BALANCE_AMT,GOD_COLLECTION_REALIZED,GOD_DISCOUNT_ADJ_AMT FROM gft_order_hdr ".
			" join gft_emp_master cic on (GOD_INCHARGE_EMP_ID=cic.gem_emp_id) ".
			" join gft_emp_master ordby on (GOD_EMP_ID=ordby.gem_emp_id) " .
			" WHERE god_lead_code='$GPH_LEAD_CODE' and GOD_BALANCE_AMT>0 and GOD_EMP_ID!=9999 and god_order_status!='C' order by god_order_date asc ";
	$res=execute_my_query($outstanding_query);
	$num_rows=$count=mysqli_num_rows($res);
	$sno=0;$out_table='';$os_details='';
	while($num_rows>0){
		$data=mysqli_fetch_array($res);
		$sno++;
		$order_no=$data['GOD_ORDER_NO'];
		$order_date=$data['GOD_ORDER_DATE'];
		$order_amt=$data['GOD_ORDER_AMT'];
		$god_collected_amt=$data['GOD_COLLECTION_REALIZED'];
		$god_balance_amt=$data['GOD_BALANCE_AMT'];
		$out_table.="<tr><td>$sno</td><td>$order_no</td><td>$order_date</td><td>$order_amt</td><td>$god_collected_amt</td><td>$god_balance_amt</td></tr>";
		$num_rows--;
	}

	if($count>0){
		$os_details="
		<div id='outstanding_dtl' class='unhide'>
		<br><table id='table_asa_collection' border='1'><thead>
		<tr class='modulelisttitle'><td colspan='6' class='highlight_repeated_visits' align='center'  width='550' ><font size=2.5px><b>Outstanding Details</b></font></td></tr>
		<tr class='modulelisttitle'>
		<td class='head_black_10' width='50'>S.No</td>
		<td class='head_black_10' width='150'>Order No</td>
		<td class='head_black_10' width='100'>Order Date</td>
		<td class='head_black_10' width='100'>Order Amount</td>
		<td class='head_black_10' width='100'>Recieved Amount</td>
		<td class='head_black_10' width='100'>Balance Amount</td>
		</tr></thead>
		<tbody>$out_table</tbody>
		</table></div><br>";
		return $os_details;
	}else {
		return $os_details;
	}
}



/**
 * @param string $GPH_ORDER_NO
 * @return string
 */
function get_proforma_hdr_query($GPH_ORDER_NO) {
	$query= " SELECT ph.GPH_ORDER_NO, ph.GPH_LEAD_CODE, ph.`GPH_EMP_ID`, date(ph.GPH_ORDER_DATE) GPH_ORDER_DATE, ph.`GPH_ORDER_AMT`,ph.GPH_PROFORMA_TO, ".
			" concat(GLH_CUST_NAME,'<br />',if(GLH_DOOR_APPARTMENT_NO!='',concat(GLH_DOOR_APPARTMENT_NO,','),'')," .
			" if(GLH_BLOCK_SOCEITY_NAME!='', concat(GLH_BLOCK_SOCEITY_NAME,','),'')," .
			" if(GLH_STREET_DOOR_NO!='',concat(GLH_STREET_DOOR_NO,','),'')," .
			" if(GLH_CUST_STREETADDR1!='',concat(GLH_CUST_STREETADDR1,','),''), '<br />'," .
			" if(GLH_CUST_STREETADDR2!='' and GLH_CUST_STREETADDR2!=GLH_CUST_STREETADDR1 and GLH_CUST_CITY!=GLH_CUST_STREETADDR2, concat(GLH_CUST_STREETADDR2,','),'')," .
			" if(GLH_AREA_NAME!='' and GLH_AREA_NAME!=GLH_CUST_STREETADDR2 and GLH_CUST_CITY!=GLH_AREA_NAME  , concat(GLH_AREA_NAME,',<br />'),'')," .
			" if(GLH_CUST_CITY!='', concat(GLH_CUST_CITY,',<br />'),'')," .
			" GLH_CUST_STATECODE,' PIN NO - ',GLH_CUST_PINCODE) cusromer_address,ph.`GPH_CURRENCY_CODE`, ".
			" ph.`GPH_APPROVEDBY_EMPID`, ph.`GPH_APPROVAL_CODE`, ph.`GPH_ORDER_STATUS`,".
			" ph.`GPH_REMARKS`, ph.`GPH_REASON_FOR_DISCOUNT`, ph.`GPH_REASON_FOR_DISCOUNT_DTL`, ph.`GPH_CREATED_DATE`,".
			" ph.`GPH_COLLATERAL_ADDED`, ph.`GPH_VERSION_NO`,GLH_CUST_STATECODE,GOM_TEMPLATE_ID,GPH_TYPE,GPH_VALIDITY_DATE,GLH_LEAD_TYPE, ".
			" GLE_GST_NO FROM gft_proforma_hdr ph " .
			" join gft_lead_hdr lh on( GLH_LEAD_CODE=ph.GPH_LEAD_CODE) " .
			" join gft_lead_hdr_ext on (gle_lead_code=glh_lead_code) ".
			" left join gft_order_type_master on (GOM_ID=ph.GPH_TYPE) " .
			" where ph.GPH_ORDER_NO='$GPH_ORDER_NO'";
	return $query;
}
/**
 * @param string $GPH_ORDER_NO
 * 
 * @return string
 */

function generateproforma_invoice_wothout_order($GPH_ORDER_NO){
	global $global_gst_mode;
	$pagebreak='<div style="page-break-after: always;"><span style="display: none;">&nbsp;</span></div>';
	$pagebreakpdf='<!-- NEW PAGE -->';
	$query= get_proforma_hdr_query($GPH_ORDER_NO);
	$db_document_content_config=array();
	$template_id='7';
	$GPH_CURRENCY='INR';
	$GPH_ORDER_DATE='';
	$GPH_LEAD_CODE='';
	$GPH_EMP_ID='';
	$customer_name='';
	$lead_type="";
	$result=execute_my_query($query);
	//$num_rows=mysqli_num_rows($result);
	//if($num_rows==0 ) return;
		if($data=mysqli_fetch_array($result)){
		$db_document_content_config['proforma_invoice_no']=$data['GPH_ORDER_NO'];
		$db_document_content_config['customer_address']=$data['cusromer_address'];
		$db_document_content_config['tax_mode']=($data['GLH_CUST_STATECODE']=='Tamil Nadu'?'VAT':'CST');
		$db_document_content_config['validity_date']=$data['GPH_VALIDITY_DATE'];
		$GPH_LEAD_CODE=$data['GPH_LEAD_CODE'];
		$GPH_EMP_ID=$data['GPH_EMP_ID'];
		$GPH_ORDER_DATE=$data['GPH_ORDER_DATE'];
		$GPH_QUATION_TYPE=$data['GPH_TYPE'];
		$GPH_CURRENCY=$data['GPH_CURRENCY_CODE'];
		//$template_id=$data['GOM_TEMPLATE_ID'];
		$GPH_VERSION_NO=$data['GPH_VERSION_NO'];
		$customer_name=$data['GPH_PROFORMA_TO'];
		$lead_type=$data['GLH_LEAD_TYPE'];
	 }
	$asa_proforma = false;
	$excnamedesc=get_emp_name_desc($GPH_EMP_ID);
	
	/*$contactquery=" SELECT distinct(GCC_CONTACT_NAME) FROM gft_customer_contact_dtl WHERE GCC_LEAD_CODE=$GPH_LEAD_CODE AND gcc_designation=1 and GCC_CONTACT_NAME!='' group by GCC_LEAD_CODE";
	$result_contact=execute_my_query($contactquery);
	if($datacontact=mysqli_fetch_array($result_contact)){
		$customer_name=$datacontact[0];
	}
	if($customer_name==''){
		$contactquery=" SELECT distinct(GCC_CONTACT_NAME) FROM gft_customer_contact_dtl WHERE GCC_LEAD_CODE=$GPH_LEAD_CODE AND gcc_designation in (2,3,4) and GCC_CONTACT_NAME!='' group by GCC_LEAD_CODE";
		$result_contact=execute_my_query($contactquery);
		if($datacontact=mysqli_fetch_array($result_contact)){
			$customer_name=$datacontact[0];
		}	
	}*/
	$arraystr=explode('-',$GPH_ORDER_DATE);
	$strdate=date("M j\<\s\u\p\>S\<\/\s\u\p\> Y", mktime(0, 0, 0, (int)$arraystr[1], (int)$arraystr[2], (int)$arraystr[0]));
	$strdatetoday=date("M j\<\s\u\p\>S\<\/\s\u\p\> Y", mktime(0, 0, 0, date('m'), date('d'), date('Y')));

	/*$contactquery=" SELECT distinct(GCC_CONTACT_NAME) FROM gft_customer_contact_dtl WHERE GCC_LEAD_CODE=$GPH_LEAD_CODE AND gcc_designation=1 and GCC_CONTACT_NAME!='' group by GCC_LEAD_CODE";
	$result_contact=execute_my_query($contactquery);
	if($datacontact=mysqli_fetch_array($result_contact)){
		$customer_name=$datacontact[0];
	}
	if($customer_name==''){
		$contactquery=" SELECT distinct(GCC_CONTACT_NAME) FROM gft_customer_contact_dtl WHERE GCC_LEAD_CODE=$GPH_LEAD_CODE AND gcc_designation in (2,3,4) and GCC_CONTACT_NAME!='' group by GCC_LEAD_CODE";
		$result_contact=execute_my_query($contactquery);
		if($datacontact=mysqli_fetch_array($result_contact)){
			$customer_name=$datacontact[0];
		}	
	}*/
	$mailquery=" SELECT distinct(GCC_CONTACT_NO) mail FROM gft_customer_contact_dtl WHERE GCC_LEAD_CODE=10948 AND gcc_designation=1 and gcc_contact_type='4' ";
	$resultmail=execute_my_query($mailquery);
	if($datamailid=mysqli_fetch_array($resultmail)){
		$mailid=$datamailid[0];
	}
	$arraystr=explode('-',$GPH_ORDER_DATE);
	$strdate=date("M j\<\s\u\p\>S\<\/\s\u\p\> Y", mktime(0, 0, 0, (int)$arraystr[1], (int)$arraystr[2], (int)$arraystr[0]));
	$strdatetoday=date("M j\<\s\u\p\>S\<\/\s\u\p\> Y", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
	
	$query=" SELECT concat(f.GPM_DESC,' - ',p.GPM_SKEW_DESC ) pro_desc, concat(p.GPM_PRODUCT_CODE, p.GPM_PRODUCT_SKEW) pcode, f.GPM_DESC, " .
			" round(g.GPP_SELL_RATE,2) GPP_SELL_RATE, g.GPP_TAX_RATE,g.GPP_SER_TAX_RATE, g.GPP_COUPON_HOUR," .
			" g.GPP_QTY, round(g.GPP_DISCOUNT_AMT) GPP_DISCOUNT_AMT, g.GPP_SELL_AMT, GPP_NO_CLIENT, GPP_AMT_PERCLIENT, GPP_CUSTOM_SKEWS," .
			" g.GPP_TAX_AMT,g.GPP_SER_TAX_AMT, p.GFT_SKEW_PROPERTY ,GPP_LIST_PRICE, ref.GPM_ORDER_TYPE,p.GPM_TRAINING_HRS, " .
			" concat(p.GFT_HIGHER_PCODE, p.GFT_HIGHER_SKEW) ucode,p.GPM_PRODUCT_CODE, p.GPM_PRODUCT_SKEW,p.GPM_PRODUCT_TYPE, " .
			" GPP_CGST_PER,GPP_SGST_PER,GPP_IGST_PER,GPP_CESS_PER FROM gft_proforma_product_dtl g ".
			" join gft_product_master p on (p.GPM_PRODUCT_CODE=g.GPP_PRODUCT_CODE and p.GPM_PRODUCT_SKEW=g.GPP_PRODUCT_SKEW) ".
			" left join gft_product_master ref on (p.GPM_PRODUCT_CODE=ref.GPM_PRODUCT_CODE and p.GPM_REFERER_SKEW=ref.GPM_PRODUCT_SKEW) ".
			" join gft_product_family_master f on (f.GPM_PRODUCT_CODE=g.GPP_PRODUCT_CODE) " .
			" where g.GPP_ORDER_NO='$GPH_ORDER_NO' " .
			" order by GPP_PRINT_ORDER, p.GFT_SKEW_PROPERTY, gpm_display_order ";
	
	$GPP_SER_TAX_RATE=0;
	$GPP_TAX_RATE=0;
	$vat_rate	=0;

	$result=execute_my_query($query);
	$sno1=1; $sno2=1; $sno3=1; $total1=0; $total2=0; $total3=0;$total_training_hrs=0;
	$itemdesc1='';$itemdesc2='';$itemdesc3='';
	$asa_sno=1;
	$asa_service_tax=$asa_total=0;
	$asa_amount_for_customlic	=	get_samee_const("SAM_ASA_AMT_FOR_CUSTLIC");
	$gst_rate = 0;
	while($data=mysqli_fetch_array($result)){
		$product_name=$data['pro_desc'];
		$product_code=$data['pcode'];
		$pcode=$data['GPM_PRODUCT_CODE'];
		$pskew=$data['GPM_PRODUCT_SKEW'];
		$up_product_code=$data['ucode'];
		$GPP_SELL_RATE=$data['GPP_SELL_RATE'];
		$GPP_SER_TAX_RATE=$data['GPP_SER_TAX_RATE'];
		$GPP_TAX_RATE=$data['GPP_TAX_RATE'];
		if($vat_rate<=0){ 
			$vat_rate=$GPP_TAX_RATE; 
		}
		$gst_rate = $data['GPP_CGST_PER']+$data['GPP_SGST_PER']+$data['GPP_IGST_PER']+$data['GPP_CESS_PER'];
		$GPP_QTY=$GPP_QTY1=$data['GPP_QTY'];
		$GPP_SELL_AMT=round($data['GPP_SELL_AMT'],2);
		$GPP_TAX_AMT=round($data['GPP_TAX_AMT'],2);
		$GPP_SER_TAX_AMT=round($data['GPP_SER_TAX_RATE'],2);
		$tax_total = $GPP_SER_TAX_RATE+$GPP_TAX_RATE+$gst_rate;
		$skewp=$data['GFT_SKEW_PROPERTY'];
		$noof_client=$data['GPP_NO_CLIENT'];
		$amount_per_client=$data['GPP_AMT_PERCLIENT'];
		$custom_lic_skews=$data['GPP_CUSTOM_SKEWS'];
		$gpm_order_type	=(int)$data['GPM_ORDER_TYPE'];
		$GPP_COUPON_HOUR=(int)$data['GPP_COUPON_HOUR'];	
		$training_hrs=($GPP_QTY*(int)$data['GPM_TRAINING_HRS']);
		if($GPP_QTY==0 or $GPP_QTY==''){
			$GPP_QTY='As Per Need';
			$GPP_TAX_AMT='On Actuals';
			$GPP_SELL_AMT='On Actuals';
		}else if($GPP_COUPON_HOUR>0){
			$GPP_QTY1=$training_hrs=$GPP_QTY*$GPP_COUPON_HOUR;
			$GPP_QTY="$GPP_QTY x $GPP_COUPON_HOUR Hrs Coupon";
		}
		$total_training_hrs=$total_training_hrs+$training_hrs;
		if(in_array($skewp, explode(',', SALES_TAX_ORDER_ENTRY))){
			$db_document_content_config['PI_itemdesc1'][$sno1]=array('sno1'=>$sno1,
															'product_name'=>$data['pro_desc'],
															'GPP_SELL_RATE'=>number_format($GPP_SELL_RATE,2),
															'GPP_QTY'=>$GPP_QTY,
															'GPP_SELL_AMOUNT'=>number_format($GPP_SELL_RATE*$GPP_QTY,2));
			$total1+=($GPP_SELL_RATE*$GPP_QTY1);
			$sno1++;
		}else if(in_array($skewp,array(4,15))){
			$asa_proforma = true;
			$all_custom_license_skew=/*. (string[int]) .*/ array();
			$asa_for_custom			=/*. (string[int]) .*/ array();
			$custom_name			=/*. (string[int]) .*/ array();
			$custom_license_total	=	0;
			$mainasa_amount			=	$GPP_SELL_AMT;
			$cl_disp = "";
			if($custom_lic_skews!='' && (strpos($custom_lic_skews,"**")>0)){
				$all_custom_license_skew	=	explode('**',$custom_lic_skews);
				for($i=0;$i<count($all_custom_license_skew);$i++){
					if($all_custom_license_skew[$i]!=''){
						$productinfo		=	explode('-',$all_custom_license_skew[$i]);
						$pcodeskew			=	$productinfo[0].'-'.$productinfo[1];
						$sql_custom_product	=	" select  gpm.GPM_PRODUCT_CODE, gpm.GPM_PRODUCT_SKEW , gpm.GPM_SKEW_DESC , gpm.GPM_NET_RATE, gpf.GPM_PRODUCT_NAME,  gpf.GPM_PRODUCT_ABR  from gft_product_master AS gpm
												 inner join gft_product_family_master AS gpf ON (gpm.GPM_PRODUCT_CODE=gpf.GPM_PRODUCT_CODE)
												 where concat(gpm.GPM_PRODUCT_CODE,'-',gpm.GPM_PRODUCT_SKEW)='$pcodeskew' ";	
						$res_custom_product	=	execute_my_query($sql_custom_product);
						if(mysqli_num_rows($res_custom_product)==1){
							$row					=	mysqli_fetch_array($res_custom_product);							
							if($GPH_CURRENCY=='USD'){
								$custom_net_rate = (int)$row['GPM_NET_RATE']/(int)get_samee_const("UNIV_PRICE_CONVERSION");
							}else{
								$custom_net_rate = (int)$row['GPM_NET_RATE'];
							}
							$asa_for_custom[$i]		=	($asa_amount_for_customlic/100)*$custom_net_rate;
							$custom_name[$i]		=	$row['GPM_PRODUCT_ABR'].' '.$row['GPM_SKEW_DESC'];							
							$custom_license_total	=	($custom_license_total)+($asa_for_custom[$i]);	
						}	
					}
				}
			}else if( (strpos($custom_lic_skews,"(")===0) || (strpos($custom_lic_skews,"qty for")>0) ){
			        $cl_disp = "<br>$custom_lic_skews";
			}
			if($gpm_order_type==0){
				if($noof_client!=0 and $noof_client!=''){
					$mainasa_amount	=	($mainasa_amount-(($noof_client)*$amount_per_client));
				}
				if($custom_license_total!=0 and $custom_license_total!=''){
					$mainasa_amount	=	($mainasa_amount-$custom_license_total);
				}
			}
			if( ((int)$mainasa_amount) == ((int)$GPP_SELL_AMT) ){
				$mainasa_sell_amt	=	$GPP_SELL_RATE;
			}else{
				$mainasa_sell_amt	=	((100/(100+$tax_total))*$mainasa_amount)/$GPP_QTY;
			}
			if($gpm_order_type==0){
				$db_document_content_config['PI_itemdesc3'][$sno3]=array('sno3'=>$sno3,
				        'product_name'=>$data['pro_desc'].$cl_disp,
						'GPP_SELL_RATE'=>number_format($mainasa_sell_amt,2),
						'GPP_QTY'=>$GPP_QTY,
						'GPP_SELL_AMOUNT'=>number_format($mainasa_sell_amt*$GPP_QTY,2));
			}else if($noof_client!=0){
				$db_document_content_config['PI_itemdesc3'][$sno3]=array('sno3'=>$sno3,
				        'product_name'=>$data['pro_desc'].$cl_disp,
						'GPP_SELL_RATE'=>number_format($mainasa_sell_amt*$GPP_QTY/$noof_client,2),
						'GPP_QTY'=>$noof_client,
						'GPP_SELL_AMOUNT'=>number_format($mainasa_sell_amt*$GPP_QTY,2));				
			}
			$total3+=($mainasa_sell_amt*$GPP_QTY);
			$sno3++;			
 			if($noof_client!=0 and $noof_client!='' && ($gpm_order_type==0) ){				
				$client_sell_rate	=	(100/(100+$tax_total)*$amount_per_client);
				$db_document_content_config['PI_itemdesc3'][$sno3]=array('sno3'=>$sno3,
															'product_name'=>'Additional Clients of '.$data['GPM_DESC'],
															'GPP_SELL_RATE'=>number_format($client_sell_rate,2),
															'GPP_QTY'=>($noof_client),
															'GPP_SELL_AMOUNT'=>number_format($client_sell_rate*($noof_client),2));
				$sno3++;
				$total3+=($client_sell_rate*($noof_client));
			}
			if($custom_license_total!=0 and $custom_license_total!=''){
				for($j=0;$j<count($asa_for_custom);$j++){
					if($asa_for_custom[$j]!='' and $asa_for_custom[$j]!=0){
						$custom_sell_rate	=	(100/(100+$tax_total)*$asa_for_custom[$j]);
						$db_document_content_config['PI_itemdesc3'][$sno3]=array('sno3'=>$sno3,
															'product_name'=>'Custom License - '.$custom_name[$j],
															'GPP_SELL_RATE'=>number_format($custom_sell_rate,2),
															'GPP_QTY'=>($GPP_QTY),
															'GPP_SELL_AMOUNT'=>number_format($custom_sell_rate*($GPP_QTY),2));
						$sno3++;
						$total3+=($custom_sell_rate*($GPP_QTY));
					}
				}
			}	
		}else{
			$db_document_content_config['PI_itemdesc2'][$sno2]=array('sno2'=>$sno2,
															'product_name'=>$data['pro_desc'],
															'GPP_SELL_RATE'=>number_format($GPP_SELL_RATE,2),
															'GPP_QTY'=>$GPP_QTY,
															'GPP_SELL_AMOUNT'=>number_format($GPP_SELL_RATE*$GPP_QTY1,2));
			$total2+=($GPP_SELL_RATE*$GPP_QTY1);
			$sno2++;
		}
		$gst_tax_joins = " left join gft_hsn_vs_tax_master on (GHT_ID=GPM_TAX_ID) ".
				" left join gft_tax_type_master on (GTM_ID=GHT_TAX_ID) ";
		$asa_product_query="";
		if($data['GFT_SKEW_PROPERTY']=='1'){
			$asa_product_query=	"select GPM_DISPLAY_NAME,GPM_LIST_PRICE, GPM_SERVISE_TAX_PERC, GPM_TAX_PERC, GPM_NET_RATE,GPM_USD_RATE,GTM_TAX_PER ".
					" from gft_product_master $gst_tax_joins WHERE GFT_SKEW_PROPERTY=4  AND CONCAT(GPM_PRODUCT_CODE,GPM_REFERER_SKEW)='$product_code'";
		}else if($data['GFT_SKEW_PROPERTY']=='2'){
			$asa_product_query=	"select GPM_DISPLAY_NAME,GPM_LIST_PRICE, GPM_SERVISE_TAX_PERC, GPM_TAX_PERC, GPM_NET_RATE,GPM_USD_RATE,GTM_TAX_PER ".
					" from gft_product_master $gst_tax_joins WHERE GFT_SKEW_PROPERTY=4  AND CONCAT(GPM_PRODUCT_CODE,GPM_REFERER_SKEW)='$up_product_code'";
		}else if($data['GFT_SKEW_PROPERTY']=='3'){
		    $asa_product_query=	" select GPM_DISPLAY_NAME,GPM_LIST_PRICE, GPM_SERVISE_TAX_PERC, GPM_TAX_PERC, GPM_NET_RATE,GPM_USD_RATE,GTM_TAX_PER ".
		  		                " from gft_product_master $gst_tax_joins WHERE GFT_SKEW_PROPERTY=4  AND GPM_PRODUCT_CODE='$pcode' AND GPM_REFERER_SKEW='$pskew' ";
		}else if($data['GFT_SKEW_PROPERTY']=='11' && $data['GPM_PRODUCT_TYPE']=='8' ){
			$tax_column = "GPM_SERVISE_TAX_PERC";
			if($global_gst_mode==1){
				$tax_column = "GTM_TAX_PER";
			}
			$asa_product_query=	"select GPM_DISPLAY_NAME,(((GPM_NET_RATE*($asa_amount_for_customlic/100)))*(100/(100+$tax_column))) GPM_LIST_PRICE, GPM_SERVISE_TAX_PERC, GPM_TAX_PERC, ".
					"(GPM_NET_RATE*($asa_amount_for_customlic/100)) GPM_NET_RATE,".
					"(GPM_USD_RATE*($asa_amount_for_customlic/100)) GPM_USD_RATE, GTM_TAX_PER ".
					" from gft_product_master $gst_tax_joins WHERE GFT_SKEW_PROPERTY=11  AND GPM_PRODUCT_CODE=$pcode AND GPM_PRODUCT_SKEW='$pskew'";
		}
		if($asa_product_query!="" && ($lead_type=="1" || $lead_type=="3")){
			$asa_rows=execute_my_query($asa_product_query);
			if(mysqli_num_rows($asa_rows)>0 && $asa_row=mysqli_fetch_array($asa_rows)){
				$asa_sell_price=$asa_row['GPM_USD_RATE'];
				$asa_sell_amt = $GPP_QTY1*$asa_sell_price;
				$db_document_content_config['ASA_SERVICE_TAX_PER']="0";
				if($GPH_CURRENCY!='USD'){
					$asa_sell_price=$asa_row['GPM_LIST_PRICE'];
					$asa_sell_amt = $GPP_QTY1*$asa_sell_price;
					$asa_service_tax+=$GPP_QTY1*($asa_row['GPM_NET_RATE']-$asa_row['GPM_LIST_PRICE']);
					if($global_gst_mode==1){
						$db_document_content_config['ASA_GST_TAX_PER']=$asa_row['GTM_TAX_PER'];
						$db_document_content_config['ASA_GST_TAX_AMOUNT']=number_format($asa_service_tax,2);
					}else{
						$db_document_content_config['ASA_SERVICE_TAX_PER']=$asa_row['GPM_SERVISE_TAX_PERC'];
						$db_document_content_config['ASA_SERVICE_TAX_AMOUNT']=number_format($asa_service_tax,2);
					}
				}
				$db_document_content_config['ASA_DESC'][$asa_sno]=array('ASA_SNO'=>$asa_sno,
						'ASA_PRODUCT_NAME'=>$asa_row['GPM_DISPLAY_NAME'],
						'ASA_GQP_SELL_RATE'=>number_format($asa_sell_price,2),
						'ASA_GQP_QTY'=>$GPP_QTY1,
						'ASA_GQP_SELL_AMOUNT'=>number_format($asa_sell_amt,2));
				$asa_total+=$asa_sell_amt;
				$asa_sno++;
			}
		}
		
	}
	if(isset($db_document_content_config['ASA_DESC']) && count($db_document_content_config['ASA_DESC'])>0){
		$asa_total+=$asa_service_tax;
		$round_off_item1=round($asa_total,0);
		$db_document_content_config['ASA_TOTAL_AMOUNT']=number_format(round($round_off_item1,2),2);
		if($asa_total!=$round_off_item1){
			if($asa_total > $round_off_item1){
				$db_document_content_config['ASA_ROUNDOFF_SYMBOL']=' - ';
				$db_document_content_config['ASA_ROUNDOFF_AMOUNT']=number_format(($asa_total - $round_off_item1),2);
			}
			else if($asa_total < $round_off_item1){
				$db_document_content_config['ASA_ROUNDOFF_SYMBOL']=' + ';
				$db_document_content_config['ASA_ROUNDOFF_AMOUNT']=number_format(($round_off_item1-$asa_total),2);
			}
		}
	}
	if(isset($db_document_content_config['PI_itemdesc1']) and count($db_document_content_config['PI_itemdesc1'])>0){
		if($GPH_CURRENCY=='INR'){
			$db_document_content_config['Item1_license_tax_per']=$vat_rate;
			$db_document_content_config['Item1_license_tax_amount']=number_format($total1*$vat_rate/100,2);
			$db_document_content_config['Item1_service_tax_per']=$GPP_SER_TAX_RATE;
			$db_document_content_config['Item1_service_tax_amount']=number_format($total1*$GPP_SER_TAX_RATE/100,2);
			$db_document_content_config['Item1_gst_tax_per']=$gst_rate;
			$db_document_content_config['Item1_gst_tax_amount']=number_format($total1*$gst_rate/100,2);
			$total1+=($total1*($vat_rate+$GPP_SER_TAX_RATE+$gst_rate)/100);
		}
		$round_off_item1=round($total1,0);
		$db_document_content_config['Item1_total_amount_before_roundoff']=number_format($total1,2);
		$db_document_content_config['Item1_total_amount']=number_format(round($round_off_item1,2),2);
	
		if($total1!=$round_off_item1){
			if($total1 > $round_off_item1){
				$db_document_content_config['Item1_roundoff_label']=' Less ';
				$db_document_content_config['Item1_roundoff_symbol']=' - ';				
				$db_document_content_config['Item1_total_amount_roundoff']=number_format(($total1 - $round_off_item1),2);
			}
			else if($total1 < $round_off_item1){
				$db_document_content_config['Item1_roundoff_label']=' Add ';
				$db_document_content_config['Item1_roundoff_symbol']=' + ';
				$db_document_content_config['Item1_total_amount_roundoff']=number_format(($round_off_item1-$total1),2);
			}	
		}
	}
	if(isset($db_document_content_config['PI_itemdesc2']) and count($db_document_content_config['PI_itemdesc2'])>0){
		if($GPH_CURRENCY=='INR'){
			$db_document_content_config['Item2_service_tax_per']=$GPP_SER_TAX_RATE;
			$db_document_content_config['Item2_service_tax_amount']=number_format($total2*$GPP_SER_TAX_RATE/100,2);
			$db_document_content_config['Item2_gst_tax_per']=$gst_rate;
			$db_document_content_config['Item2_gst_tax_amount']=number_format($total2*$gst_rate/100,2);
			$total2+=($total2*($GPP_SER_TAX_RATE+$gst_rate)/100);
		}
		$round_off_item2=round($total2,0);
		$db_document_content_config['Item2_total_amount_before_roundoff']=number_format($total2,2);
		$db_document_content_config['Item2_total_amount']=number_format(round($round_off_item2,2),2);
		if($total2!=$round_off_item2){
			if($total2 > $round_off_item2){
				$db_document_content_config['Item2_roundoff_label']=' Less ';
				$db_document_content_config['Item2_roundoff_symbol']=' - ';
				$db_document_content_config['Item2_total_amount_roundoff']=number_format(($total2 - $round_off_item2),2);
			}
			else if($total2 < $round_off_item2){
				$db_document_content_config['Item2_roundoff_label']=' Add ';
				$db_document_content_config['Item2_roundoff_symbol']=' + ';
				$db_document_content_config['Item2_total_amount_roundoff']=number_format(($round_off_item2-$total2),2);
			}	
		}
	}
	if(isset($db_document_content_config['PI_itemdesc3']) and count($db_document_content_config['PI_itemdesc3'])>0){
		if($GPH_CURRENCY=='INR'){
			$db_document_content_config['Item3_service_tax_per']=$GPP_SER_TAX_RATE;
			$db_document_content_config['Item3_service_tax_amount']=number_format($total3*$GPP_SER_TAX_RATE/100,2);
			$db_document_content_config['Item3_gst_tax_per']=$gst_rate;
			$db_document_content_config['Item3_gst_tax_amount']=number_format($total3*$gst_rate/100,2);
			$total3+=($total3*($GPP_SER_TAX_RATE+$gst_rate)/100);
		}
		$round_off_item3=round($total3,0);
		$db_document_content_config['Item3_total_amount_before_roundoff']=number_format($total3,2);
		$db_document_content_config['Item3_total_amount']=number_format(round($round_off_item3,2),2);
		if($total3!=$round_off_item3){
			if($total3 > $round_off_item3){
				$db_document_content_config['Item3_roundoff_label']=' Less ';
				$db_document_content_config['Item3_roundoff_symbol']=' - ';
				$db_document_content_config['Item3_total_amount_roundoff']=number_format(($total3 - $round_off_item3),2);
			}
			else if($total3 < $round_off_item3){
				$db_document_content_config['Item3_roundoff_label']=' Add ';
				$db_document_content_config['Item3_roundoff_symbol']=' + ';
				$db_document_content_config['Item3_total_amount_roundoff']=number_format(($round_off_item3-$total3),2);
			}	
		}
	}
	if($customer_name==''){
		$customer_name = get_contact_name_for_customer($GPH_LEAD_CODE);
	}
	$proforma_subject = "GoFrugal Product Enquiry";
	$db_document_content_config['expiry_message'] = "";
	if($asa_proforma){
		$proforma_subject = "GoFrugal Product Expiry";
		$inst_que = " select GID_VALIDITY_DATE from gft_install_dtl_new ".
					" join gft_product_master pm on (GID_LIC_PCODE=pm.GPM_PRODUCT_CODE and GID_LIC_PSKEW=pm.GPM_PRODUCT_SKEW) ".
					" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
					" where GID_LEAD_CODE='$GPH_LEAD_CODE' and GID_STATUS!='U' and GPM_LICENSE_TYPE='1' and GPM_IS_BASE_PRODUCT='Y' ".
					" and datediff(GID_VALIDITY_DATE,now()) < 60 ";
		$inst_res = execute_my_query($inst_que);
		if($row1 = mysqli_fetch_array($inst_res)){
			$date_str = date('d M, Y',strtotime($row1['GID_VALIDITY_DATE']));
			$db_document_content_config['expiry_message'] 	= "Kind Attention : ".$customer_name.
			                                                  " . Your License Expires on $date_str ";
		}
	}
	$db_document_content_config['customer_name']=$customer_name;
	$db_document_content_config['customer_id']=$GPH_LEAD_CODE;
	$db_document_content_config['proforma_subject'] = $proforma_subject;
	$db_document_content_config['proforma_invoice_date']=$strdate;
	$db_document_content_config['CURRENCY']=$GPH_CURRENCY;
	$db_document_content_config['financial_year']=get_fin_year($GPH_ORDER_DATE,1);
	$db_document_content_config['execName']=$excnamedesc[0];
	$db_document_content_config['execMobileNo']=(is_authorized_group_list($GPH_EMP_ID,array(54))?get_samee_const('GFT_CUST_CARE_NO'):$excnamedesc[1]);
	$db_document_content_config['execMailId']=$excnamedesc[2];
	$db_document_content_config['exceDesignation']=$excnamedesc[3];
	$db_document_content_config['customer_gstin_label'] = "GSTIN";
	$db_document_content_config['company_gstin']=get_single_value_from_single_table("GCM_GST_NO", "gft_company_master", "gcm_id", "1");
	$key_name = 'Bank_Details_INR';
	if($GPH_CURRENCY=='USD'){
		$key_name = 'Bank_Details_USD';
		$db_document_content_config['customer_gstin_label'] = "IEC No";
		$db_document_content_config['company_gstin']=get_single_value_from_single_table("GCM_EIC_NO", "gft_company_master", "gcm_id", "1");
		
	}
	$db_document_content_config['bankDetails']=get_samee_const($key_name);
	$db_document_content_config['os_details'] = get_outstanding_details_as_table($GPH_LEAD_CODE);
	$db_document_content_config['total_training_hours']="";
	if($total_training_hrs>0){
		$db_document_content_config['total_training_hours']="Total service hours alloted : $total_training_hrs<br><b>Note:</b>Beyond the alloted service hours customers can pay and buy additional hours. Contact your sales manager for the same. ";
	}	
	$proforma_content=get_formatted_document_content($db_document_content_config,4,$template_id);
	return $proforma_content;	
}

/**
 * @param string $quotation_no
 * @param string $content
 * @param int $version_no
 * 
 * @return string File Name 
 */
function generatequotationpdf($quotation_no,$content,$version_no){
	global $pagebreak,$pagebreakpdf,$attach_path;
	$folder_name="quotation";
	$content=(string)str_replace('\"','"', $content);
	$content=(string)str_replace('<tbody>','', $content);
	$content=(string)str_replace('</tbody>','', $content);
	$content=(string)str_replace("&apos;","'", $content);
	$content=(string)str_replace($pagebreak,$pagebreakpdf,$content);
	$html_fname="Qua_$quotation_no.html";
	if(file_exists("$attach_path.'/'.$folder_name.'/'.$html_fname")){
		unlink($attach_path.'/'.$folder_name.'/'.$html_fname);
	}
	$f_name='';

	$t=write_to_file($folder_name,$content,$html_fname,$mode=null);
	if($t){
		$upquery="update gft_quotation_hdr set GQH_VERSION_NO=$version_no where GQH_ORDER_NO='$quotation_no' ";
		execute_my_query($upquery);
		if($version_no!=1){
			$version_no=$version_no-1;
			$renamefile=rename("../sales_server_support/quotation/Qua_$quotation_no.pdf","../sales_server_support/quotation/Qua_$quotation_no-".$version_no.".pdf");
		}
		$file_path=realpath("../sales_server_support/quotation/");
		$f_name=(string)str_replace("html","pdf",$html_fname);
		$fr_name=$file_path.'/'.$f_name;
		$filename=$file_path.'/'.$html_fname;
		//echo "<div class=hide>";
		passthru("htmldoc --quiet --size A4 --top 2cm --bottom 2cm --left 1.5cm --right 1.5cm -t pdf14 --jpeg --footer d./  --linkstyle plain --pagemode fullscreen  -f $fr_name --webpage $filename ");
		//echo "</div>";
		//chmod($fr_name,777);
		//unlink($filename);
	}
	return $f_name;
}

/**
 * @param string $proforma_no
 * @param string $content
 * @param int $version_no
 * 
 * @return string 
 */
function generateproformapdf($proforma_no,$content,$version_no){
	global $pagebreak,$pagebreakpdf,$attach_path;
	$folder_name="proforma";
	$content=(string)str_replace('\"','"', $content);
	$content=(string)str_replace('<tbody>','', $content);
	$content=(string)str_replace('</tbody>','', $content);
	$content=(string)str_replace("&apos;","'", $content);
	$content=(string)str_replace($pagebreak,$pagebreakpdf,$content);
	$html_fname="Pro_$proforma_no.html";
	if(file_exists("$attach_path.'/'.$folder_name.'/'.$html_fname")){
		unlink($attach_path.'/'.$folder_name.'/'.$html_fname);
	}
	$f_name='';

	$t=write_to_file($folder_name,$content,$html_fname,$mode=null);
	if($t){
		$upquery="update gft_proforma_hdr set GPH_VERSION_NO=$version_no where GPH_ORDER_NO='$proforma_no' ";
		execute_my_query($upquery);
		if($version_no!=1){
			$version_no=$version_no-1;
			$renamefile=rename("../sales_server_support/proforma/Pro_$proforma_no.pdf","../sales_server_support/proforma/Pro_$proforma_no-".$version_no.".pdf");
		}
		$file_path=realpath("../sales_server_support/proforma/");
		$f_name=(string)str_replace("html","pdf",$html_fname);
		$fr_name=$file_path.'/'.$f_name;
		$filename=$file_path.'/'.$html_fname;
		//echo "<div class=hide>";
		passthru("htmldoc --quiet --size A4 --top 2cm --bottom 2cm --left 1.5cm --right 1.5cm -t pdf14 --jpeg --footer d./  --linkstyle plain --pagemode fullscreen  -f $fr_name --webpage $filename ");
		//echo "</div>";
		//chmod($fr_name,777);
		//unlink($filename);
	}
	return $f_name;
}
/**
 * @param string $order_no
 * @param string $type
 * 
 * @return string
 */
function get_product_wise_terms($order_no, $type=''){
    $return_string = "";
    $terms_arr = array();
    $query  =   " select GPM_TERMS from gft_invoice_product_dtl g ".
                " INNER JOIN gft_product_master ON(gip_product_code=GPM_PRODUCT_CODE AND gip_product_skew=GPM_PRODUCT_SKEW) ".
                " INNER JOIN gft_product_family_master f  ON(f.GPM_PRODUCT_CODE=g.gip_product_code) ".
                " where gip_invoice_id='$order_no' order by GFT_SKEW_PROPERTY,gpm_display_order";
    if($type=="proforma"){
        $query =    " select GPM_TERMS from gft_proforma_product_dtl g ".
                    " INNER JOIN gft_product_master ON(GPP_PRODUCT_CODE=GPM_PRODUCT_CODE AND GPP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
                    " INNER JOIN gft_product_family_master f  ON(f.GPM_PRODUCT_CODE=g.GPP_PRODUCT_CODE)" .
                    " where GPP_ORDER_NO='$order_no' order by GPP_PRINT_ORDER,GFT_SKEW_PROPERTY,gpm_display_order";
    }else if($type=="quotation"){
        $query = " SELECT p.GPM_TERMS FROM gft_quotation_product_dtl g ".
                 " INNER JOIN gft_product_master p ON(p.GPM_PRODUCT_CODE=g.GQP_PRODUCT_CODE AND p.GPM_PRODUCT_SKEW=g.GQP_PRODUCT_SKEW) ".
                 " INNER JOIN gft_product_family_master f  ON(f.GPM_PRODUCT_CODE=g.GQP_PRODUCT_CODE)" .
                 " Where  g.GQP_ORDER_NO='$order_no' order by GQP_PRINT_ORDER,GFT_SKEW_PROPERTY,gpm_display_order ";
    }
    $details_res = execute_my_query($query);
    while($rowdet = mysqli_fetch_array($details_res)){
        $terms_arr[] = $rowdet['GPM_TERMS'];
    }
    if(count($terms_arr)>0){
        $arr = array_unique($terms_arr);
        foreach($arr as $k=>$v){
            $return_string .= $v;
        }
    }
    return $return_string;
}
/**
 * @param int $invoice_id
 * @param string $order_no
 *
 * @return string
 */
function get_invoice_pdf_hdr_query($invoice_id,$order_no) {
	$query = " SELECT lh.GLH_CUST_NAME, ih.gih_invoice_ac_reffer_id, ih.gih_invoice_date, ih.gih_lead_code, ih.gih_net_invoice_amount, ih.gih_type, " .
			" ih.GIH_PRINT_DISCOUNT,lh.glh_lead_type lead_type,concat(lh.GLH_CUST_NAME,'<br />', " .
			" if(lh.GLH_DOOR_APPARTMENT_NO!='',concat(lh.GLH_DOOR_APPARTMENT_NO,','),''), " .
			" if(lh.GLH_BLOCK_SOCEITY_NAME!='', concat(lh.GLH_BLOCK_SOCEITY_NAME,','),''), " .
			" if(lh.GLH_STREET_DOOR_NO!='',concat(lh.GLH_STREET_DOOR_NO,','),''), " .
			" if(lh.GLH_CUST_STREETADDR1!='',concat(lh.GLH_CUST_STREETADDR1,','),''), '<br />', " .
			" if(lh.GLH_CUST_STREETADDR2!='' and lh.GLH_CUST_STREETADDR2!=lh.GLH_CUST_STREETADDR1 and lh.GLH_CUST_CITY!=lh.GLH_CUST_STREETADDR2 , concat(lh.GLH_CUST_STREETADDR2,','),''), " .
			" if(lh.GLH_AREA_NAME!='' and lh.GLH_AREA_NAME!=lh.GLH_CUST_STREETADDR2 and lh.GLH_CUST_CITY!=lh.GLH_AREA_NAME, concat(lh.GLH_AREA_NAME,','),''),'<br />', " .
			" lh.GLH_CUST_CITY, ' - ', lh.GLH_CUST_PINCODE, '<br />'," .
			" lh.GLH_CUST_STATECODE) cusromer_address,lh.GLH_CUST_STATECODE,GIH_IS_C_FORM, GIM_TEMPLATE_ID, ".
			" GIH_CUST_GSTIN,ifnull(GIH_BUSINESS_NAME,lh.GLH_CUST_NAME) cust_name, GLH_COUNTRY ".
			" FROM gft_invoice_hdr ih " .
			" join gft_lead_hdr lh on (lh.glh_lead_code=gih_lead_code)" .
			" join gft_lead_hdr_ext on (lh.glh_lead_code=gle_lead_code) ".
			" join gft_invoice_type_master on(GIM_ID=gih_type)" .
// 			" left join gft_cp_order_dtl cp on(gco_order_no= '$order_no')".
// 			" left join gft_lead_hdr lh1 on(lh1.GLH_LEAD_CODE = GCO_CUST_CODE)".
			" WHERE ih.gih_invoice_id=$invoice_id and ih.gih_status='A'";
	return $query;
}
/**
 * @param int $invoice_id
 * @param string $order_no
 * 
 * @return void
 */
function generate_invoice($invoice_id,$order_no){
	global $result;
	$pagebreak='<div style="page-break-after: always;"><span style="display: none;"> </span></div>';
	$pagebreakpdf='<!-- NEW PAGE -->';
	$query=get_invoice_pdf_hdr_query($invoice_id, $order_no);
	$result=execute_my_query($query);
	$db_document_content_config=array();
	$db_document_content_config['discount_print']="";
	$db_document_content_config['against_c_form']='';
	$barcode_gen_url=get_samee_const('BARCODE_PATH');
	$template_id='';
	$inv_type=0;
	$invoice_file_name = '';
	if($data=mysqli_fetch_array($result)){
		$invoice_file_name = $data['gih_invoice_ac_reffer_id'];
		$db_document_content_config['inv_no']=$invoice_file_name;
		$db_document_content_config['inv_date']=$data['gih_invoice_date'];
		$db_document_content_config['Order_No']=$order_no;
		$db_document_content_config['customer_name']=$data['GLH_CUST_NAME'];
		$db_document_content_config['total_amount']=$data['gih_net_invoice_amount'];
		$db_document_content_config['customer_address']=$data['cusromer_address']."<br><IMG SRC=\"$barcode_gen_url?barcode=".$data['gih_lead_code']."-".$data['gih_invoice_ac_reffer_id']."&width=210&height=25&text=1\" alt=\"barcode\" />";
		$db_document_content_config['tax_mode']=($data['GLH_CUST_STATECODE']=='Tamil Nadu'?'VAT':'CST');
		$db_document_content_config['CURRENCY']='INR';
		$template_id=$data['GIM_TEMPLATE_ID'];
		if($data['GIH_IS_C_FORM']=='Y'){
			$db_document_content_config['against_c_form']=true;
		}
		if($data['GIH_PRINT_DISCOUNT']=='Y'){
			$db_document_content_config['discount_print']=true;
		}
		$inv_type=(int)$data['gih_type'];
		$lead_code=$data['gih_lead_code'];
	}
	$db_document_content_config['service_date']='';
	$service_date='';
	$order_no_dtl=/*. (string[int][int]) .*/ array();

	if($inv_type==2){
		$query= "SELECT ip.GIP_ORDER_NO from gft_invoice_product_dtl ip where ip.gip_invoice_id=$invoice_id group by ip.GIP_ORDER_NO ";
		$result=execute_my_query($query);
		while($data=mysqli_fetch_array($result)){
			$order_no_dtl[]=$data['GIP_ORDER_NO'];
		}
		$order_nos="'".implode("', '",$order_no_dtl)."'";
		$select_query=" select min(act.GLD_VISIT_DATE) from_dt, max(act.GLD_VISIT_DATE) max_date ".
						" from gft_service_order_dtl sod join gft_activity act on (act.GLD_ACTIVITY_ID=sod.gsd_activity_id)" .
						" join gft_lead_hdr lh on (act.GLD_LEAD_CODE=lh.GLH_LEAD_CODE)" .
						" left join gft_activity_master am on (act.gld_visit_nature=am.GAM_ACTIVITY_ID) " .
						" where sod.gsd_order_no in ($order_nos) group by am.GAM_ACTIVITY_ID " ;
		$resultact=execute_my_query($select_query);
		if($data=mysqli_fetch_array($resultact)){
			$db_document_content_config['service_date']=$data['from_dt'].' to '.$data['max_date'];	
		}
		$select_query="Select act.GLD_VISIT_DATE, lh.GLH_CUST_NAME, lh.GLH_CUST_STREETADDR2, lh.GLH_CUST_CITY, GLD_NOTE_ON_ACTIVITY,gam_activity_desc,lh.glh_lead_code, gem_emp_name " .
						" from gft_service_order_dtl sod " .
						" join gft_activity act on (act.GLD_ACTIVITY_ID=sod.gsd_activity_id)" .
						" join gft_lead_hdr lh on (act.GLD_LEAD_CODE=lh.GLH_LEAD_CODE)" .
						" left join gft_activity_master am on (act.gld_visit_nature=am.GAM_ACTIVITY_ID) " .
						" join gft_emp_master on (gem_emp_id=act.gld_emp_id)".
						" where sod.gsd_order_no in ($order_nos) " ;
		$resultact=execute_my_query($select_query);
		$db_document_content_config['OPI_Activity_details']=array();
		if(mysqli_num_rows($resultact)>0){
			$i=0;
			while($actdata=mysqli_fetch_array($resultact)){
				$db_document_content_config['OPI_Activity_details'][$i]=array('GLD_VISIT_DATE'=>$actdata['GLD_VISIT_DATE'],
																	'A_cust_dtl'=>($actdata['GLH_CUST_NAME']==''?'&nbsp;':$actdata['GLH_CUST_NAME']). '<br>'.($actdata['GLH_CUST_STREETADDR2']==''?'&nbsp;':$actdata['GLH_CUST_STREETADDR2'].' - ').($actdata['GLH_CUST_CITY']==''?'&nbsp;':$actdata['GLH_CUST_CITY']),
																	'gem_emp_name'=>$actdata['gem_emp_name'],
																	'GLD_NOTE_ON_ACTIVITY'=>($actdata['GLD_NOTE_ON_ACTIVITY']==''?' ':$actdata['GLD_NOTE_ON_ACTIVITY']),
																	'gam_activity_desc'=>($actdata['gam_activity_desc']==''?' ':$actdata['gam_activity_desc']));
				$i++;
			}
		}
	}
	$query="SELECT ip.GIP_ORDER_NO, ip.gip_product_code, ip.gip_product_skew,pfm.GPM_PRODUCT_NAME, pm.GPM_SKEW_DESC, ".
			" ip.gip_list_prize, ip.gip_rate, ip.gip_qty, ip.gip_amount, ip.GIP_TAX_RATE, ip.GIP_TAX_AMT, ip.GIP_SERTAX_RATE, ".
			" ip.GIP_SERTAX_AMT,GFT_SKEW_PROPERTY,ip.GIP_COUPON_HRS " .
			" FROM gft_invoice_product_dtl ip join gft_product_master pm on (ip.gip_product_code=pm.gpm_product_code and ip.gip_product_skew=pm.gpm_product_skew)" .
			" join gft_product_family_master pfm on (pm.gpm_product_code=pfm.gpm_product_code) " .
			" WHERE ip.gip_invoice_id=$invoice_id group by ip.gip_invoice_id, ip.GIP_ORDER_NO, ip.gip_product_code, ip.gip_product_skew ";
	$result=execute_my_query($query);
	$inv_sno=1;
	$total1=0;
	$total=0;
	$discount=0;
	$saltax_per=0.0;
	$sertax_per=0.0;
	 
	while($data=mysqli_fetch_array($result)){
		$order_no_dtl[$inv_sno]=$data['GIP_ORDER_NO'];
		$qty	=	$data['gip_qty'];		
		$coupon_hrs=$data['GIP_COUPON_HRS'];
		$coupon_hrs_text="$qty";
		if($coupon_hrs>0){
			$coupon_hrs_text="$qty x $coupon_hrs Hrs Coupon";
			$qty=$qty*$coupon_hrs;
		}
		$db_document_content_config['INVOICE_item_desc'][$inv_sno]=array('sno'=>$inv_sno,
																	'product_name'=>$data['GPM_PRODUCT_NAME'].' - '.$data['GPM_SKEW_DESC'],
																	'gip_rate'=>number_format(($db_document_content_config['discount_print']==true?$data['gip_list_prize']:$data['gip_rate']),2,".",""),
																	'gip_qty'=>"$coupon_hrs_text",
																	'gip_sell_amt'=>number_format($qty* ($db_document_content_config['discount_print']==true?$data['gip_list_prize']:$data['gip_rate']),2,".","")
																	);
		$saltax_per=$data['GIP_TAX_RATE'];
		$sertax_per=$data['GIP_SERTAX_RATE'];
		$total1+=$qty* ($db_document_content_config['discount_print']==true?$data['gip_list_prize']:$data['gip_rate']);
		$discount+=$qty*(round($data['gip_list_prize'],2)-round($data['gip_rate'],2));
		$inv_sno++;
	}
	if($db_document_content_config['discount_print']==true){
		$db_document_content_config['befoure_discount_amount']=number_format($total1,2,".","");
		$db_document_content_config['discount_amount']=number_format($discount,2,".","");
		$total=$total1-$discount;
	}else{
		$total=$total1;
	}
	$db_document_content_config['ground_total_amt']=number_format($total,2,".","");
	$sales_tax_amt=($saltax_per!=0?round(($total*$saltax_per/100),2):0);
	$service_tax_amt=($sertax_per!=0?round(($total*$sertax_per/100),2):0);
	if($sales_tax_amt!=0){
		$db_document_content_config['INVOICE_license_tax_amt']=number_format($sales_tax_amt,2,".","");
		$db_document_content_config['INVOICE_license_tax_per']=number_format($saltax_per,2,".","");
	}
	if($service_tax_amt!=0){
		$db_document_content_config['INVOICE_service_tax_amt']=number_format($service_tax_amt,2,".","");
		$db_document_content_config['INVOICE_service_tax_per']=number_format($sertax_per,2,".","");
	}
	$total_amt=$total+$sales_tax_amt+$service_tax_amt;
	if( (round($total_amt)<$total_amt)  or (round($total_amt)>$total_amt)){
		$db_document_content_config['net_total_amt']=number_format($total_amt,2,".","");
		$db_document_content_config['round_off_action']=((round($total_amt)>$total_amt)?'Add':'Less');
		$db_document_content_config['round_off_amt']=number_format(((round($total_amt)>$total_amt)?(round($total_amt)-$total_amt):($total_amt-round($total_amt))),2,".","");
	}
	$db_document_content_config['total_amount']=number_format(round($total_amt),2,".","");
	$db_document_content_config['total_amount_text']=SpellNumber(number_format(round($total_amt),2,".",""));
	$invoice_content=get_formatted_document_content($db_document_content_config,5,$template_id);
	$f_name=generateinvoicepdf($invoice_id,$invoice_content);
}

/**
 * @param int $invoice_id
 * @param string $content
 * @param string $other_file_format
 * 
 * @return string
 */
function generateinvoicepdf($invoice_id,$content, $other_file_format=""){
	global $pagebreak,$pagebreakpdf;
	$folder_name="invoice";
	$content=(string)str_replace('\"','"', $content);
	$content=(string)str_replace('<tbody>','', $content);
	$content=(string)str_replace('</tbody>','', $content);
	$content=(string)str_replace($pagebreak,$pagebreakpdf,$content);
	$html_fname="invoice_$invoice_id.html";

	$f_name= /*. (string) .*/ NULL;
	$t=write_to_file($folder_name,$content,$html_fname,$mode=null);
	if($other_file_format!=''){
	    $t1=write_to_file($folder_name,$content,"invoice_$invoice_id.$other_file_format",$mode=null);
	}	
	if($t){
		$file_path=realpath("../sales_server_support/$folder_name/");
		$f_name=(string)str_replace("html","pdf",$html_fname);
		$fr_name=$file_path.'/'.$f_name;
		$filename=$file_path.'/'.$html_fname;
// 		echo "<div class=hide>";
		generate_pdf($filename);
// 		passthru("htmldoc --quiet --size A4 -t pdf14 --jpeg=100 --linkstyle plain --pagemode fullscreen  -f $fr_name --webpage $filename ");
// 		echo "</div>";
// 		chmod($fr_name,777);
// 		unlink($filename);
	}
	return $f_name;
}

/**
 * @param string $material_id
 * @param boolean $fromall
 * 
 * @return string[int][int]
 */
function get_marketing_material_list($material_id, $fromall=false){
	global $uid;
	$sql	=	"SELECT CGI_EMP_ID FROM gft_cp_info WHERE CGI_EMP_ID=$uid LIMIT 1";
	$result =	execute_my_query($sql);
	if(mysqli_num_rows($result)!=0){
		$query="select GMM_ID,GMM_NAME from gft_marketing_material where gmm_status='A'  ";
		if($fromall==false){
			$query.=	" AND GMM_FOR_PARTNER='Y' ";
		}
		$query.=($material_id!=null?" and GMM_ID='$material_id' ":'');
	}else{
		$query="select GMM_ID,GMM_NAME from gft_marketing_material where gmm_status='A' ";
		if($fromall==false){
			$query.=	" AND GMM_FOR_EMP='Y' ";
		}
		$query.=($material_id!=null?" and GMM_ID='$material_id' ":'');	
	}	
	return get_two_dimensinal_array_from_query($query,"GMM_ID","GMM_NAME");
}

/**
 * @param string $selected_type
 *
 * @return string[int][int]
 */

function get_emotion_list($selected_type=""){
	if($selected_type=="emp"){
		$query="select GCM_EMOTION_ID,GCM_EMOTION_NAME from gft_customer_emotion_master where GCM_EMOTION_STATUS='A' and GCM_FOR_EMP='Y'";
	}else{
		$query="select GCM_EMOTION_ID,GCM_EMOTION_NAME from gft_customer_emotion_master where GCM_EMOTION_STATUS='A' and GCM_FOR_CUSTOMER='Y'";
	}
	return get_two_dimensinal_array_from_query($query,"GCM_EMOTION_ID","GCM_EMOTION_NAME");
}

/**
 * @param string $emp_id
 * 
 * @return string
 */
function get_courier_addr($emp_id){
	$query="select GEM_COURIER_ADDR from gft_emp_master where  gem_emp_id=$emp_id ";
	$result=execute_my_query($query);
	$qd=mysqli_fetch_array($result);
	$courier_addr=$qd[0];
	return 	$courier_addr;
}

/**
 * @param int $tab_index
 * @param boolean $show_save_button
 * @param boolean $show_extra
 *
 * @return void
 */
 
function add_contact_details(&$tab_index=0,$show_save_button=false,$show_extra=false){
	global $non_employee_group,$uid;
	$show_save_btn='';
	if($show_save_button==true){
		$show_save_btn="<input name=\"save_button\"  onclick=\"save_contact_dtl();\" value=\"Save Customer details\" type=\"button\" class=\"button\"> ";
	}
	if(!is_authorized_group_list($uid,$non_employee_group)){
		$show_save_btn="<span align='right'><input type='checkbox' id='send_acc_info' name='send_acc_info' value='' > Account Info " .
			 " <input type='checkbox' id='send_addr_info' name='send_addr_info' value='' > Address " .
			 " <input type='button' class='button' value='Send SMS/ Mail ' id='send_sms_mail' onclick=\"javascript:send_sms_mail_accinfo();\">" .
		     " <input type='button' class='button' value='Send Push Notification' id='send_wns' onclick=\"javascript:open_wns_window();\">" .
		  	 " <br><br><span id='notify_cust_div' style='text-align:center;'>".
		   	 " <input type='button' class='button' value='Notify Customer' id='send_wns' onclick=\"javascript:open_notify_window();\"></span> ".
			 " <span id='' style='text-align:center;'>".
			 " <input type='button' class='button' value='Send Account No' id='send_virtual_account' onclick=\"javascript:send_virtual_account_no();\"></span>".
		     " </span>";
	}
$autocorrect_fieldcon=<<<END
onkeypress="javascript:return removeInvalidChar(this,event);" onblur="javascript:makeStdWords(this);"
END;

$autocorrect_num_field=<<<END
onblur="javascript:extractMobileNumber(this);" onkeypress="javascript:return forMobileNumbers(this,event,true);"
END;
$array_contact_type=get_two_dimensinal_array_from_table('gft_cust_contact_type_master','gct_id', 'gct_desc');
$array_contact_designation=get_two_dimensinal_array_from_table("gft_contact_designation_master","GCD_CODE","GCD_NAME",'GCD_STATUS','A');
$contact_edit = 'Y';
if(is_authorized_group($uid, 70)){  //PC and RC roles
	$contact_edit = 'N';
}
echo<<<END
<div style="max-height:264px;overflow-y:auto">
<table  cellspacing="1" border="1" id="table1" width="100%">
<thead><tr class="modulelisttitle1">
<td class="head_black_10" Colspan="2">S.No <input type='hidden' name='contact_edit' id='contact_edit' value='$contact_edit'></td>
<td class="head_black_10">Name</td>
<td class="head_black_10">Designation</td>
<td class="head_black_10">Contact Type</td>
<td class="head_black_10" nowrap>Contact</td>
END;
$show_tech_group=false;
$show_annuity_group=false;
$show_sales_group=false;
//if(is_authorized_group_list($uid,array(1,5,20)) or is_authorized_group_list($uid,array(14)) ){
  echo	'<td class="head_black_10" title="Whom to Contact for Support ">TS</td>';
  $show_tech_group=true;
//}
//if(is_authorized_group_list($uid,array(1,5,54))){
  echo	'<td class="head_black_10" title="Whom to Contact for Annuity ">A</td>';
  $show_annuity_group=true;
//}
//if(is_authorized_group_list($uid,array(1,5))){	
  echo	'<td class="head_black_10" title="Whom to Contact for Sales ">S</td>';
  $show_sales_group=true;
//}

if($show_extra==true){
	echo '<td class="head_black_10" >Valid</td>';
	echo '<td class="head_black_10" >Can Send</td>';
	echo '<td class="head_black_10" >Phone Support</td>';
}
echo<<<END
</tr></thead><tbody>
<tr><td><input type="checkbox" name="visitcontact_id[1]" id="visitcontact_id1" value=""></td>
<td align="right"><font color="red" size="3" >*</font>1</td> 
<td><input type="hidden" name="contact_id[1]" id="contact_id1" value="">
<input type="text"  maxlength="49"  name="contact_name[1]" id="contact_name1" tabindex="$tab_index" size="20" class="formStyleTextarea" $autocorrect_fieldcon>
<input type="hidden" name="old_contact_name[1]" id="old_contact_name1"></TD><td>
END;
$tab_index++;
echo fix_combobox_with("designation1","designation[1]",$array_contact_designation,'',$tab_index,'Select',"style=\"width:80px\"",false);
$tab_index++;
echo<<<END
<input type="hidden" name="old_designation[1]" id="old_designation1"></td><td>
END;
echo  fix_combobox_with("contact_type1","contact_type[1]",$array_contact_type,'',$tab_index,'Select',"style=\"width:80px\"",false,"onchange='javascript:contact_nochange(this);'");
$tab_index++;
echo<<<END
<input type="hidden" name="old_contact_type[1]" id="old_contact_type1"/></td>
<td><input class='button hide' type='button' value='show' id='contact_show_btn1'><input type="hidden" name="old_contact_no[1]" id="old_contact_no1">
<input type="text" name="contact_no[1]" id="contact_no1" tabindex="$tab_index" size="14"  maxlength="49" class="formStyleTextarea" $autocorrect_num_field/>
</td>
END;

$query_gcg="select GCG_CONTACT_GROUP_ID,GCG_ACCEPT_SINGLE from gft_contact_dtl_group_master where GCG_CONTACT_GROUP_STATUS='A' ";
$result_gcg=execute_my_query($query_gcg);
$cgdata=array();
while($qdcg=mysqli_fetch_array($result_gcg)){
	$gcg_group_id=$qdcg['GCG_CONTACT_GROUP_ID'];
	$cgdata[$gcg_group_id]['accept_single']=$qdcg['GCG_ACCEPT_SINGLE'];
}
if($show_tech_group==true){
	if($cgdata[1]['accept_single']=='Y') {$type_of_element='radio';}
	else {$type_of_element='checkbox';}
echo "<td ><input type='$type_of_element' id='tech_group1' name='contact_group1[1]' value=1 onclick='chkMobileNo(\"tech_group\",1)'><input type='hidden' id='old_tech_group1' name='old_contact_group1[1]'>" .
		"<span style='display:none'> <input type='$type_of_element' id='tech_group0' name='contact_group1[0]'></span> </td>"; }
if($show_annuity_group==true){
	if($cgdata[2]['accept_single']=='Y') {$type_of_element='radio';}
	else {$type_of_element='checkbox';}
echo "<td ><input type='$type_of_element' id='annuity_group1' name='contact_group2[1]' value=1 onclick='chkMobileNo(\"annuity_group\",1)'>" .
		"<input type='hidden' id='old_annuity_group1' name='old_contact_group2[1]'>" .
		"<span style='display:none'> <input type='$type_of_element' id='annuity_group0' name='contact_group2[0]'></span></td>"; }
if($show_sales_group==true){
	if($cgdata[3]['accept_single']=='Y') {$type_of_element='radio';}
	else {$type_of_element='checkbox';}
echo "<td ><input type='$type_of_element' id='sales_group1' name='contact_group3[1]' value=1 onclick='chkMobileNo(\"sales_group\",1)'>" .
		"<input type='hidden' id='old_sales_group1' name='old_contact_group3[1]'>" .
		"<span style='display:none'> <input type='$type_of_element' id='sales_group0' name='contact_group3[0]'></span>" .
		"</td>"; }
if($show_extra==true){
	echo "<td ><input type='checkbox' id='is_valid1' name='is_valid[1]'></td>";
	echo "<td ><input type='checkbox' id='can_send1' name='can_send[1]'></td>";
	echo "<td ><input type='checkbox' class='validate_phone_support' id='phone_support1' name='phone_support[1]' onclick='chkPhoneSupportNo(\"phone_support\",1)'></td>";
}
if(is_enabled_for_click_to_call($uid)){
	echo "<td ><img class='hide' id='click_call1' name='click_call[1]' src='images/calls.gif' alt='c2c' onclick='trigger_click_to_call(this);'></td>";			
}else{
	echo "<td ><img class='hide' id='click_call1' name='click_call[1]'></td>";
}

	
	
$preferred_clist=get_two_dimensinal_array_from_table('gft_preferred_communication','GPF_ID','GPF_NAME','GPF_STATUS',$status='A',$order_by=null);
$preferred_select=fix_combobox_with("preferred_comm","preferred_comm",$preferred_clist,$selected_value=0,$tab_index,$default_value='-Select-',
		$style="Style='width:150px'",$add_opt_group=false);
echo<<<END
</tr></tbody><tfoot><tr></tr></tfoot></table></div>
<table cellspacing="1" border="1" width="100%">
<tfoot>
<tr><td align="center" colspan="9">
<input type="hidden" name="removed_contact_id" id="removed_contact_id" value=""/>
<input id='add_contact' name="button"   onclick="addRow_contact_n();" value="Add" type="button" class="button"/> 
<input value="Remove" onclick="removeselected_Row_contact_n()" type="button" class="button"/>
$show_save_btn
</td></tr>
</tfoot>
</table>
<table  width="100%" cellspacing="1" border="1">
<tr class="modulelisttitle1">
<td class="head_black_10" align='center'>Preferred Communication</td>
<td class="head_black_10" align='center'>Address Verified</td>
<td class="head_black_10" align='center'> Verified Date </td>
<tr>
	<td align='center'>$preferred_select</td>
	<td align='center'><span id='show_chk_addr_verified' style='display:none'>
	<input type='checkbox' name='addr_verified' id='addr_verified' >  </span></td>
	<td align='center'><span id='addr_last_updated'></span></td>
</tr>
</table>
<script>
function trigger_click_to_call(obj){
	var contid=obj.id.replace("click_call","");
	var to_caller = jQuery("#contact_no"+contid).val();
	console.log(to_caller);
	jQuery.ajax({
		url:"call_center/click_to_call.php?to_caller="+to_caller,
		type:'GET',
		headers:{'web_user':'$uid'},
		async:false,
		success:function(resp){
					var msg = " Call Initiated.";
					var obj = JSON.parse(resp);
					console.log(obj);
					msg += " \\n Response Message : "+obj.message;
					msg += " \\n Transaction Id : "+obj.data.id;
					alert(msg);
				},
		failure:function(err){
					alert("Error in Ajax");
				}
	});		
}
</script>
END;
}


/**
 * @param int $reason_visit
 *
 * @return int
 */
function get_complaint_code_reason_visit($reason_visit){
	switch($reason_visit){
		case 7:
		$GCH_COMPLAINT_CODE=6;
		break;
		case 8:
		$GCH_COMPLAINT_CODE=1;
		break;
		case 13:
		$GCH_COMPLAINT_CODE=15;
		break;
		case 14:
		$GCH_COMPLAINT_CODE=19;
		break;
		case 12:
		$GCH_COMPLAINT_CODE=20;
		break;
		case 28:
		$GCH_COMPLAINT_CODE=40;
		break;
		default:
		$GCH_COMPLAINT_CODE=1;
		break;
	}
	return $GCH_COMPLAINT_CODE;
}

/**
 * @param string $zone_id
 * @param string $region_id
 * @param string $area_id
 * @param string $terr_id
 * @param string $country_id
 * @param string $state_id
 * @param string $district_id
 *
 * @return string
 */

function get_employee_in($zone_id=null,$region_id=null,$area_id=null,$terr_id=null,$country_id=null,$state_id=null,$district_id=null){
	$query="select group_concat(distinct(get_emp_id)) " .
			"from gft_emp_territory_dtl " ;
	if($zone_id!='' or $region_id!='' or $area_id!='' or $terr_id!=''){
		$query.="inner join b_map_view bmv on ((terr_id=get_territory_id and get_work_area_type=2) " .
				" or (area_id=get_territory_id and get_work_area_type=3) " .
				" or (region_id=get_territory_id and get_work_area_type=4)" .
				" or (zone_id=get_territory_id and get_work_area_type=5) )  ";
	} 
	if($country_id!='' or $state_id!='' or $district_id!=''){
	}
	$query.="where get_emp_id!='' ";
	
	if($terr_id!="0" and $terr_id!='')			{$query.=" and bmv.terr_id='$terr_id' ";	}
	else if($area_id!="0" and $area_id!='')		{$query.=" and bmv.area_id='$terr_id' ";	}
	else if($region_id!="0" and $region_id!='') {$query.=" and bmv.region_id='$region_id' ";  }	
	else if($zone_id!="0" and $zone_id!='')     {$query.=" and bmv.zone_id='$zone_id' ";    }
	//echo $query;	       
	$result=execute_my_query($query,'',true,false);
	$qdata=mysqli_fetch_array($result);
	$employee_list=$qdata[0];
	return $employee_list;
}

/**
 * @param int $zone_id
 * @param int $region_id
 * @param int $area_id
 * @param int $terr_id
 * @param int $country_id
 * @param int $state_id
 * @param int $district_id
 * @param string[int] $group_arr
 *
 * @return string[int][int]
 */
function get_employee_in_array($zone_id=0,$region_id=0,$area_id=0,$terr_id=0,
$country_id=0,$state_id=0,$district_id=0,$group_arr=null){
	global $uid;
	$query="select distinct(get_emp_id) as emp_id,gem_emp_name,gem_email " .
			"from (gft_emp_territory_dtl,gft_emp_master em) " ;
	if($zone_id!=0 or $region_id!=0 or $area_id!=0 or $terr_id!=0){
		$query.="inner join b_map_view bmv on ((terr_id=get_territory_id and get_work_area_type=2) " .
				" or (area_id=get_territory_id and get_work_area_type=3) " .
				" or (region_id=get_territory_id and get_work_area_type=4)" .
				" or (zone_id=get_territory_id and get_work_area_type=5) )  ";
	}
	if($country_id!=0 or $state_id!=0 or $district_id!=0){
		
	}
	$whr_group_ids="";
	if($group_arr!=null){
		if(is_array($group_arr)){$group_ids=implode(',',$group_arr);}else {$group_ids=$group_arr;}
		$query.=" left join gft_role_group_master rg on (grg_role_id=gem_role_id) " .
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=em.gem_emp_id)" .
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id)" ;
		$whr_group_ids	="  and ggm_group_id in ($group_ids) "; 
		
	}
	$query.="where get_emp_id!='' and get_emp_id=em.gem_emp_id and  gem_status='A' and get_status='A' $whr_group_ids ";
	
	if(is_authorized_group_list($uid,array(2,3,4)) and !is_authorized_group_list($uid,array(8,1))){
		$query.=" and get_emp_id=$uid ";
	}
	if($terr_id!="0" and $terr_id!=''){$query.=" and bmv.terr_id='$terr_id' ";}
	else if($area_id!="0" and $area_id!=''){$query.=" and bmv.area_id='$area_id' ";}
	else if($region_id!="0" and $region_id!=''){$query.=" and bmv.region_id='$region_id' ";}	
	else if($zone_id!="0" and $zone_id!=''){$query.=" and bmv.zone_id='$zone_id' ";}

	$employee_list= /*. (string[int][int]) .*/ array();
	$result=execute_my_query($query,'',true,false);
	$i=0;
	while($qdata=mysqli_fetch_array($result)){
		$employee_list[$i][0]=$qdata['emp_id'];
		$employee_list[$i][1]=$qdata['gem_emp_name'];
		$employee_list[$i][2]=$qdata['gem_email'];
		$i++;
	}
	return $employee_list;
}



/**
 * @param string $table_name
 * @param string $field1
 * @param string $field2
 * @param string $status_field
 * @param string $status
 * @param string $order_by
 *
 * @return string[int][int]
 */
function get_two_dimensinal_array_from_table($table_name,$field1,$field2,$status_field=null,$status=null,$order_by=null){
	$query="select $field1,$field2 FROM $table_name ";
	if(is_array($status)){$condition=" $status_field in ('".implode("','",$status)."')";}
	else {$condition= " $status_field='$status'"; }
	if($status_field!=null and $status!=null){ $query.=" where $condition "; }
	return get_two_dimensinal_array_from_query($query,$field1,$field2,$order_by);
}
/**
 * @param string $table_name
 * @param string $field1
 * @param string $field2
 * @param string $status_field
 * @param string $status
 * @param string $order_by
 *
 * @return string[string][string]
 */
function get_local_dsl_from_table($table_name,$field1,$field2,$status_field=null,$status=null,$order_by=null){
	$loca_ls	=	/*. (string[string][string]) .*/array();
	$query="select $field1,$field2 FROM $table_name ";
	if(is_array($status)){$condition=" $status_field in ('".implode("','",$status)."')";}
	else {$condition= " $status_field='$status'"; }
	if($status_field!=null and $status!=null){ $query.=" where $condition "; }
	$all_list	=	 get_two_dimensinal_array_from_query($query,$field1,$field2,$order_by);	
	for($i=0;$i<count($all_list);$i++){
		$single_group['id']=$all_list[$i][0];;
		$single_group['name']=$all_list[$i][1];;
		$loca_ls[]=$single_group;
	}
	return $loca_ls;
}
/**
 * @param string $query
 * @param string $field1
 * @param string $field2
 * @param string $status_field
 * @param string $status
 * @param string $order_by
 *
 * @return string[string][string]
 */
function get_local_dsl_from_query($query,$field1,$field2,$status_field=null,$status=null,$order_by=null){
	$loca_ls	=	/*. (string[string][string]) .*/array();
	$query="$query ";
	if(is_array($status)){$condition=" $status_field in ('".implode("','",$status)."')";}
	else {$condition= " $status_field='$status'"; }
	if($status_field!=null and $status!=null){ $query.=" where $condition "; }
	$all_list	=	 get_two_dimensinal_array_from_query($query,$field1,$field2,$order_by);
	for($i=0;$i<count($all_list);$i++){
		$single_group['id']=$all_list[$i][0];;
		$single_group['name']=$all_list[$i][1];;
		$loca_ls[]=$single_group;
	}
	return $loca_ls;
}
/**
 * @param string $query
 * @param string $field1
 * @param string $field2
 * @param string $order_by
 * @param string $def_name
 * @param string $def_val
 * @param string $order_type
 *
 * @return string[int][int]
 */
function get_two_dimensinal_array_from_query($query,$field1,$field2,$order_by=null,$def_name='',$def_val='',$order_type=''){
	global $me;
	$nquery="$query order by ".($order_by!=null? $order_by:$field2). " $order_type";

	$result=execute_my_query($nquery,$me,true,false,3);
	$i=0;
	$fd_group=/*. (string[int][int]) .*/ array();
	if($def_name!='' && $def_val!=''){
		$fd_group[$i][0]=$def_val;
		$fd_group[$i][1]=$def_name;
		$i++;
	}
	if($result  and mysqli_num_rows($result) >0){
		while($qdata=mysqli_fetch_array($result)){
			$fd_group[$i][0]=$qdata["$field1"];
			$fd_group[$i][1]=$qdata["$field2"];
			$i++;
		}
		return $fd_group;
	}else{
		return $fd_group;
		//return null;
	}
}
/**
 * @param string $query
 * @param string $field1
 * @param string $field2
 * @param string $field3
 * @param string $order_by
 * @param string $def_name
 * @param string $def_val
 *
 * @return string[int][int]
 */
function get_three_dimensinal_array_from_query($query,$field1,$field2,$field3,$order_by=null,$def_name='',$def_val=''){
	global $me;
	$nquery="$query order by ".($order_by!=null? $order_by:$field3);

	$result=execute_my_query($nquery,$me,true,false,3);
	$i=0;
	$fd_group=/*. (string[int][int]) .*/ array();
	if($def_name!='' && $def_val!=''){
		$fd_group[$i][0]=$def_val;
		$fd_group[$i][1]=$def_name;
		$i++;
	}
	if($result  and mysqli_num_rows($result) >0){
		while($qdata=mysqli_fetch_array($result)){
			$fd_group[$i][0]=$qdata["$field1"];
			$fd_group[$i][1]=$qdata["$field2"];
			$fd_group[$i][2]=$qdata["$field3"];
			$i++;
		}
		return $fd_group;
	}else{
		return $fd_group;
		//return null;
	}
}
/**
 * @param int $num
 *
 * @return string
 */
function numberToRoman($num){
     // Make sure that we only use the integer portion of the value
     $n = intval($num);
     $result = '';
 
     // Declare a lookup array that we will use to traverse the number:
     $lookup = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
     'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
     'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
 
     foreach ($lookup as $roman => $value){
         // Determine the number of matches
         $matches = intval($n / $value);
          // Store that many characters
         $result .= str_repeat($roman, $matches);
         // Substract that from the number
         $n = $n % $value;
     }
     // The Roman numeral should be built, return it
     return $result;
}

/**
 * @param mixed $array_a
 * 
 * @return mixed
 */
function array_trim($array_a){
	foreach($array_a as $key =>$value){
		$array_a[$key]=trim($value);
	}
	return $array_a;
}

/**
 * @param string $default_order_by
 * @param int $default_order_by_type
 * @param boolean $sorting_required
 * @param string[int] $mysort
 *
 * @return string
 */

function add_order_by_query($default_order_by=null,$default_order_by_type=1,$sorting_required=true,$mysort=null){
    global $sortbycol,$sorttype;
     if($sortbycol=='' and $sorttype=='' and $sorting_required==true){
    	$sortbycol=(isset($_REQUEST['sortbycol'])?(string)$_REQUEST['sortbycol']:'');
    	$sorttype=(isset($_REQUEST['sorttype'])?(string)$_REQUEST['sorttype']:'');	
    }
    if($default_order_by_type=='') {
    	$default_order_by_type=2;
    }
    $orderby_str="";
    
    if($mysort!=null && !$mysort[array_search($sortbycol,$mysort)]==$sortbycol){
			if($default_order_by==null or $default_order_by==''){
				return NULL;
    		}else{
    			$sortbycol='';$sorttype='';
    		}
	}
	
    if($sortbycol=='' and $sorttype=='' and $default_order_by!=null){
    	$default_order_by=implode(',',array_trim(explode(',',$default_order_by)));
    	$default_order_by_ch=$default_order_by;
    	if(strpos($default_order_by,',')>0){
    	//	$default_order_by_ch="`".str_replace(",","`,`",trim($default_order_by))."`";
    		$default_order_by_ch=trim($default_order_by);
	    }else{
	    	$default_order_by_ch="`".trim($default_order_by)."`";
	    }
		$orderby_str= " ORDER BY $default_order_by_ch ".($default_order_by_type=='2'?"DESC ":" ");
		$sortbycol = "$default_order_by";
		$sorttype = "$default_order_by_type";
	}else if($sortbycol!='' && $sorting_required==true) {
		$sortbycol_ch=$sortbycol;
		if(strpos($sortbycol,',')>0){
    		$sortbycol_ch="`".str_replace(",","`,`",$sortbycol)."`";
	    }else{
	    	$sortbycol_ch=trim($sortbycol);
	    }
		$orderby_str= " ORDER BY $sortbycol_ch ".($sorttype=='2'?"DESC ":" ");
	}
	
	return $orderby_str;
}

/**
 * @param string[int][int] $tabs
 * @param int $param
 * @param string $file_name
 *
 * @return string
 */
function print_header_in_tabs($tabs,$param,$file_name){
	$selected_font_color='#010101';
	$selectable_font_color='#8e8c8e';
	$ret_str = "<table cellpadding='0' cellspacing='0' border='0'><tr>";
	$request_param=return_additional_param();
	for($i=0;$i<count($tabs);$i++){
		$title=$tabs[$i][1];
		$param_value=(int)$tabs[$i][0];
		$tab_identity_font=$selectable_font_color;
		if($param_value==$param){ $tab_identity_font=$selected_font_color; }
		$additional_param = $request_param."&param=$param_value";
		$ret_str .= "<td valign='top' align='right' style='height:20'>".
				"<img alt='' src='images/header_start.gif' border='0' ></td>".
				"<td class='formHeader' style='background-image:url(images/header_tile.gif); height:20;'  align='left'>".
				"<a href='$file_name?$additional_param'><FONT color='$tab_identity_font'> $title &nbsp;</font></a></td>".
				"<td vAlign='top' align='left' style='height:20'><img alt='' src='images/header_end.gif' border='0'></td>";
	}
	$ret_str .='</table>';
	return $ret_str;
}

/**
 * @param string $table_header
 * @param string $query
 * @param string[int] $myarr
 * @param string[int] $mysort
 * @param string $sms_category
 * @param string $email_category
 * @param string $previous_months_link
 * @param string[int] $value_arr_align
 * @param string[int] $myarr_width
 * @param string $total_query
 * @param string[int] $sec_field_arr
 * @param string[int] $myarr_sub
 * @param string[int] $rowspan_arr
 * @param string[int] $colspan_arr
 * @param string[int] $myarr_sub1
 * @param string[int] $rowspan_arr1
 * @param string[int] $colspan_arr1
 * @param string[int] $report_link
 * @param string[int] $show_in_popup
 * @param boolean $scorallable_tbody
 * @param boolean $navigation
 * @param string $order_by
 * @param boolean $heading
 * @param string[int] $value_arr_total
 * @param boolean $sorting_required
 * @param int $child
 * @param string $trow_name
 * @param string[int] $emyarr
 * @param string[int] $emyarr_sub
 * @param string[int][string] $pass_only_this_arg
 * @param int $export_index
 * @param string[int][int] $table_header_array
 * @param string[int] $total_report_link 
 * @param string[int] $val_arr_class
 * 
 * @return void
 */
function generate_reports($table_header,$query,$myarr,$mysort,$sms_category=null,$email_category=null,
		$previous_months_link=null,$value_arr_align=null,$myarr_width=null,$total_query=null,$sec_field_arr=null,
		$myarr_sub=null,$rowspan_arr=null,$colspan_arr=null,$myarr_sub1=null,$rowspan_arr1=null,$colspan_arr1=null,
		$report_link=null,$show_in_popup=null,$scorallable_tbody=false,$navigation=true,$order_by=null,$heading=true,
		$value_arr_total=null,$sorting_required=true,$child=0,$trow_name=null,$emyarr=null,$emyarr_sub=null,
		$pass_only_this_arg=null,$export_index=0,$table_header_array=null,$total_report_link=null,$val_arr_class=null){
     /*    $sec_field_arr=SECONDS fILED NOTIFICATION Y/N */
			
	$row_class_in_arr=/*. (string[int]) .*/ null;
	global $reference_group,$reference_list,$reference_list_code,$noheader,$me;
		global $sortbycol,$sorttype,$emp_id,$ename,$support_calls,
				$GEM_EMP_ID,$GLH_LEAD_SOURCECODE,$GLH_REFERENCE_GIVEN,
				$indiv_order,$chain_order,$value_arr_width,$vertical;
		$value_arr_class = /*. (string[int][int]) .*/ array();
		$value_arr=/*. (string[int][int]) .*/ array();
		//$value_arr_total=/*. (string[int]) .*/ array(); No need to redeclare here. Already passed via function parameter.
		$value_arr_total_value=/*. (mixed[int][int]) .*/ array();
	$query.=add_order_by_query($order_by,$sorttype,$sorting_required,$mysort); 
	$result=execute_my_query($query);
	$count_num_rows=mysqli_num_rows($result);
	if($navigation==true){
		$nav_struct=get_dtable_navigation_struct($count_num_rows);
	}else {
		$nav_struct=get_dtable_navigation_struct($count_num_rows,$count_num_rows); 
	}
	
	$tooltip=/*. (string) .*/ null;
	if($heading==true){	
		if($table_header!=null){
			print_dtable_header($table_header,$tooltip=null,$tooltip_width="300",$old=true,$mail_link=false,$previous_months_link);
		}else if(is_array($table_header_array) && count($table_header_array)>1){
			$param=isset($_REQUEST['param'])?(int)$_REQUEST['param']:1;
			echo print_header_in_tabs($table_header_array, $param, $me);
		}
		
		if($emyarr==null){  $emyarr=$myarr;}
		if($emyarr_sub==null){  $emyarr_sub=$myarr_sub;}
		print_dtable_navigation($count_num_rows,$nav_struct,'',"export_all_report.php",$query,$emyarr,
				$sp=null,1,$navigation,null,$emyarr_sub,$sms_category,$email_category,null,$mysort,$export_index);
echo<<<END
<table cellpadding="0" cellspacing="2" width="100%" border="0" class="FormBorder1">
END;
	sortheaders($myarr,$mysort,$nav_struct,$sortbycol,$sorttype,$myarr_width,$noheader,$myarr_extra_link=null,
	$myarr_sub,$rowspan_arr,$colspan_arr,$myarr_sub1,$rowspan_arr1,$colspan_arr1,null,null,$sorting_required);
	}	
	$s=0;
	$id=$prev_date="";$tbody_class="";
	$start_of_row=(int)$nav_struct['start_of_row'];
	$MAX_NO_OF_ROWS=(int)$nav_struct['records_per_page'];
	global $uid;
	if($count_num_rows){
		mysqli_data_seek($result,$start_of_row);	
		$sl=0;$array_count=0;
		if($scorallable_tbody==true){
			$tbody_class="class='scrollable'";
		}
		if($heading==true){
			echo "<tbody $tbody_class>";
		}
		$serails=$start_of_row;
		$total_row_no=($MAX_NO_OF_ROWS+1);$sl=0;
		$financial_yr='';
		while( ($query_data=mysqli_fetch_array($result)) and $MAX_NO_OF_ROWS > $sl){
			$sl++;$serails++;
			if($child==1){$row_class_in_arr[$array_count]="highlight_orange";}
			$sid = isset($query_data['GSM_ID'])?$query_data['GSM_ID']:'';
			$visit = isset($query_data['GSM_VISIT_ID'])?$query_data['GSM_VISIT_ID']:'';
			$campus_student_email = isset($query_data['eoi_email'])?$query_data['eoi_email']:'';
			//extract($query_data);
			$GLH_LEAD_CODE=isset($query_data['GLH_LEAD_CODE']) ? $query_data['GLH_LEAD_CODE'] : '';
			$GLH_CUST_NAME=isset($query_data['GLH_CUST_NAME']) ? $query_data['GLH_CUST_NAME'] : '';
			if(isset($query_data['GPL_FULLFILLMENT_NO'])){
				$query_data['GPL_FULLFILLMENT_NO']=substr('0000'.$query_data['GPL_FULLFILLMENT_NO'],-4);
			}
			for($i=0;$i<count($mysort);$i++){
				$link="";
				$value_arr_class[$array_count][$i] = isset($val_arr_class[$i])?$val_arr_class[$i]:'';
				if($mysort[$i]=='' && ($myarr[$i]=='S.No' || $myarr[$i]=='S.No.')){
					$serial_no_dis=$serails;
					if($count_num_rows<=26 && ($child==1 or $child==2)){
						if($child==1){$serial_no_dis=chr($sl+64);}//capital letter
						else if($child==2){$serial_no_dis=chr($sl+96);}//small letter
					}else{
						if($child==1 or $child==2){
							$serial_no_dis=numberToRoman($sl);
						} 
					}
					$value_arr[$array_count][$i]=$serial_no_dis;
					continue;
				}else if($mysort[$i]=='GEM_EMP_NAME'){
					 	$link=get_ename_link($emp_id,$ename,$link_category=1,$tooltip=null,$query_data);
					 	$value_arr[$array_count][$i]=$link;
					 	continue;
				}else if($mysort[$i]=='activity_nature'){
					 	if(isset($query_data['GZC_CHAT_ID']) && $query_data['GZC_CHAT_ID']!=''){
					 		$chat_id=$query_data['GZC_CHAT_ID'];
					 		$link	=	$query_data['activity_nature']."<br><a href=\"javascript:call_popup('chat_history_details.php?chatId=$chat_id',4);\"><img src=\"images/chat_new.png\" height=\"20px\" width=\"20px\"></a>";
					 		$value_arr[$array_count][$i]=$link;
					 	}else{
					 		$value_arr[$array_count][$i]=$query_data['activity_nature'];
					 	}					 	
					 	continue;
				}else if($mysort[$i]=='GLD_VISIT_IN_MIN'){
				    $today_timestamp = strtotime(date('Y-m-d'));
				    $value_arr[$array_count][$i]= get_duration_in_string(date('H:i:s',$today_timestamp+($query_data['GLD_VISIT_IN_MIN']*60)));
				}else if($mysort[$i]=='GLH_CUST_NAME'){
				    $hist_type = 3;
				    if(in_array($table_header,array('Precheck Failure report'))){
				        $hist_type = 1;
				    }
					     if($query_data['GLH_LEAD_CODE']!=''){
					 		$tooltip=get_necessary_data_from_query_for_tooltip($query_data);
    						$link="<a  onMouseover=\"ddrivetip('".$tooltip."','#EFEFEF', 200)\";" .
			   			   " onMouseout=\"hideddrivetip()\"; " .
             		       " href=\"javascript:call_popup('edit_cust_details.php?lcode=$GLH_LEAD_CODE&call_from=popup&uid=$uid',7);\" class=\"subtle\">$GLH_CUST_NAME</a>" .
             		       " <a href=\"javascript:call_popup('supporthistory.php?call_from=popup&history_type=$hist_type&from_dt=01-11-2004&custCode=$GLH_LEAD_CODE',7);\" class=\"subtle\"><img alt=\"\" src=\"images/history.jpeg\" border=0 width=20 height=20></a>"; 
    
					 		$value_arr[$array_count][$i]=$link;
					     }else {
					     	$value_arr[$array_count][$i]='';
					     }
					 	continue;
				}else if($mysort[$i]=='GOD_ORDER_NO'){
						
						 if(is_authorized_group_list($uid,array(1))){
							$url_link_or="order_details.php?order_no=".$query_data['GOD_ORDER_NO']."&formtype=edit";
							$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$url_link_or',7);\" title='Order Details' >".$query_data['GOD_ORDER_NO']."</a>";
						 }else {
						 	$value_arr[$array_count][$i]=$query_data['GOD_ORDER_NO'];
						 	$value_arr[$array_count][$i].="<a href=\"javascript:call_popup('order_releated_details.php?order_no=".$query_data['GOD_ORDER_NO']."',5);\">[O]</a>";
						 }
						continue;
				}
				else if($mysort[$i]=='GER_NO_CALL_BACK'){
					$event_id=(isset($query_data['event_id'])?$query_data['event_id']:'');
					$link=" <a href=\"javascript:call_popup('supporthistory.php?call_from=popup&history_type=7&from_dt=01-11-2004&custCode=$GLH_LEAD_CODE&event_id=$event_id',7);\" class=\"subtle\">".$query_data['GER_NO_CALL_BACK']."</a>";
					$value_arr[$array_count][$i]=$link;
					continue; 
				}else if($mysort[$i]=='GDT_DURATION_SECS'){
					$duration_secs=isset($query_data['GDT_DURATION_SECS'])?(int)$query_data['GDT_DURATION_SECS']:0;
					$duration_val = "";
					if($duration_secs > 0){
						$duration_val = get_duration_in_string(get_time_duration($duration_secs));
					}
					$value_arr[$array_count][$i] = $duration_val;
					continue;
				}else if($mysort[$i]=='no_of_years'){
				    $no_of_years=isset($query_data['no_of_years'])?(string)$query_data['no_of_years']:'';
				    $no_of_years_str = "";
				    if($no_of_years!=""){
				        $no_of_years_str = since_date($query_data['no_of_years']);
				    }
				    $value_arr[$array_count][$i] = $no_of_years_str;
				    continue;
				}else if($mysort[$i]=='demo_joint_emp'){
					$demo_joint_emp=isset($query_data['demo_joint_emp'])?$query_data['demo_joint_emp']:'';
					$demo_joint_emp = str_replace(",", ",<br>", $demo_joint_emp);
					$value_arr[$array_count][$i] = $demo_joint_emp;
					continue;
				} else if($mysort[$i]=='GSM_STUDENT_NAME') {
					$student_name = isset($query_data['GSM_STUDENT_NAME'])?("<a href='student_performance.php?drive=$visit&GSM_ID=$sid' target=_blank>".$query_data['GSM_STUDENT_NAME']."</a>"):'';
					$kps_val = isset($query_data['kps_result'])?$query_data['kps_result']:'';
					if($kps_val=='Selected') {
						$student_name .= "&nbsp;<a href='javascript:call_popup(\"edit_campus_drive_dtls.php?GSM_ID=$sid&visit=$visit&purpose=update_gd_result\",7);'>[GD]</a>";
					}
					$student_name .= "&nbsp;<a href='https://www.guvi.in/assessment-results?company_id=38&email=$campus_student_email' target=_blank>[Guvi]</a>";
					$value_arr[$array_count][$i] = $student_name;
				} else if(in_array($mysort[$i],array('GSM_WORKSHEET_PATH','GSM_RESUME_PATH'))) {
					$pth = isset($query_data[$mysort[$i]])?$query_data[$mysort[$i]]:'';
					$dirs = explode("/",$pth);
					array_shift($dirs);
					$pth = urlencode(implode("/",$dirs));
					$gd_passed = isset($query_data['gd_result'])?$query_data['gd_result']:'';
					$upload_link = "&nbsp;<a href=\"javascript:call_popup('edit_campus_drive_dtls.php?purpose=upload_resume&GSM_ID=$sid&visit=$visit',5);\"'><img src='/images/file_upload.png' width='28' height='28' alt='Upload icon' title='Upload Worksheet and Resume'/></a>";
					if($pth!='') {
						$value_arr[$array_count][$i] = "<a href='file_download.php?type=view&filename=$pth&file_type=campus_drive' target=_blank><img src='/images/file_view.ico' alt='view' height='24' width='24' title='View/Download File'/></a>";
					} else {
						$value_arr[$array_count][$i] = "Result: $gd_passed";
					}
					$value_arr[$array_count][$i] .= $upload_link;
				}  else if($mysort[$i]=='kps_status') {
					$status = isset($query_data[$mysort[$i]])?$query_data[$mysort[$i]]:'';
					$class_name = (($status=='In-Progress')?'highlight-orange':($status=='Pending'?'highlight-lightsalmon':'highlight-green'));
					$link = "student_performance.php?GSM_ID=$sid&drive=$visit&start_selection=1";
					$value_arr[$array_count][$i] = "<a href='$link' target='_blank'>$status</a>";
					$value_arr_class[$array_count][$i] = "content_txt $class_name";
				} else {
					$var_mysort_i=$mysort[$i];
					$x_var_mysort_i=isset($query_data[$var_mysort_i])?$query_data[$var_mysort_i]:''; //NOTE: $ -of- $

					$field_value=$x_var_mysort_i; 
					if(isset($sec_field_arr[$i]) and $sec_field_arr[$i]=='Y'){
						$hrs_convert=secondsToTime($field_value);
						$field_value=$hrs_convert['h'].':'.$hrs_convert['m'].':'.$hrs_convert['s'];								
					}
					if(!isset($show_in_popup[$i])) {
						$show_in_popup[$i]='N';
					}

					if (!isset($pass_only_this_arg[$i])){
						$pass_only_this_arg[$i]=null;
					}
					
					if(isset($report_link[$i]) && $report_link[$i]!='' && $pass_only_this_arg[$i]==null){
						$link=$report_link[$i];
						if(isset($query_data['country_id']) and $query_data['country_id']!=''){	$link.="&cmbCountry=".$query_data['country_id']; }
			   			if(isset($query_data['state_id']) and $query_data['state_id']!=''){	$link.="&cmbState=".$query_data['state_id'];}
			   			if(isset($query_data['district_id']) and $query_data['district_id']!=''){	$link.="&cmbDist=".$query_data['district_id'];}			   		
						if(isset($query_data['zone_mgr_id']) && $query_data['zone_mgr_id']!=''){$link.="&cmb_zone_mgr=".$query_data['zone_mgr_id'];}
						if(isset($query_data['regional_mgr_id']) && $query_data['regional_mgr_id']!=''){$link.="&cmb_reg_mgr=".$query_data['regional_mgr_id'];}
						if(isset($query_data['terr_mgr_id']) && $query_data['terr_mgr_id']!=''){$link.="&cmb_terr_mgr=".$query_data['terr_mgr_id'];}
						if(isset($GEM_EMP_ID) && $GEM_EMP_ID!=''){$link.="&cmb_ex_name=$GEM_EMP_ID";}
						if(isset($query_data['GTM_VERTICAL_CODE']) && $query_data['GTM_VERTICAL_CODE']!=''){
							$link.="&vertical=".$query_data['GTM_VERTICAL_CODE'];
						}else if(isset($vertical) && isset($query_data['vertical']) and $query_data['vertical']!=0){
							$link.="&vertical=$vertical";
						} 
						if(isset($query_data['GPM_PINCODE']) && $query_data['GPM_PINCODE']!=''){
							$link.="&cmb_pincode=".$query_data['GPM_PINCODE']; 
						}
						if(isset($query_data['GLC_PRODUCT_CODE']) && $query_data['GLC_PRODUCT_CODE']!=''){
							$link.="&productct_shown[]=".$query_data['GLC_PRODUCT_CODE']; 
						}
						if(isset($query_data['reference_group'])){
							$link.="&reference_group=".$query_data['reference_group'];
						}
						else if(isset($reference_group) and $reference_group){$link.="&reference_group=$reference_group";}
						if(isset($query_data['reference_list'])){	$link.="&reference_list=".$query_data['reference_list'];}
						else if(isset($reference_list) and $reference_list){$link.="&reference_list=$reference_list";}
						if(isset($query_data['reference_list_code'])){	$link.="&reference_list_code=".$query_data['reference_list_code'];}
						else if(isset($reference_list_code) and $reference_list_code){$link.="&reference_list_code=$reference_list_code";}
						if(isset($query_data['GLH_LEAD_SOURCECODE'])){
							$link.="&reference_group=$GLH_LEAD_SOURCECODE&reference_list_code=$GLH_REFERENCE_GIVEN";
						}
						if(isset($query_data['financial_year']) and $query_data['financial_year']!=''){
							$link.="&financial_yr=".$query_data['financial_year'];
						}else if($financial_yr!=''){ $link.="&financial_yr=$financial_yr";} 
						if(isset($query_data['zone_id']) and $query_data['zone_id']!=''){$link.="&cmbZone=".$query_data['zone_id']; }
				   		if(isset($query_data['region_id']) and $query_data['region_id']!=''){$link.="&cmbRegion=".$query_data['region_id'];}
				   		if(isset($query_data['area_id']) and $query_data['area_id']!=''){$link.="&cmbArea=".$query_data['area_id'];}
				   		if(isset($query_data['terr_id']) and $query_data['terr_id']!=''){$link.="&cmbterr=".$query_data['terr_id'];}
						if(isset($query_data['from_dt']) and $query_data['from_dt']!=''){$from_date=local_date_format($query_data['from_dt']);
							$link.="&from_dt=$from_date";}
						if(isset($query_data['to_dt']) and $query_data['to_dt']!=''){	$to_date=local_date_format($query_data['to_dt']);
						$link.="&to_dt=$to_date";}	
						if(isset($query_data['audit_sp']) and $query_data['audit_sp']!=''){ 
							$link.="&audit_sp=".$query_data['audit_sp']; 
						}
						if(isset($query_data['pcode']) and $query_data['pcode']!='' && $query_data['pskew']!=''){
							$pcode=$query_data['pcode'];
							$pskew=$query_data['pskew'];
							$link.="&prod=$pcode-$pskew";}
						if(isset($query_data['prod']) and $query_data['prod']!='' && $query_data['prod']!=''){
							$link.="&prod=".$query_data['prod']; }	
						if(isset($query_data['support_calls']) and $query_data['support_calls']!=''){
							$link.="&support_calls=$support_calls";
						}
						if(isset($query_data['GLH_CREATED_CATEGORY'])){
							$link.="&created_lead_type=".$query_data['GLH_CREATED_CATEGORY'];
						}
						if(isset($query_data['gdp_create_category'])){
							$link.="&registered_category=".$query_data['gdp_create_category']."&registered_date=on";
						}
						global $emp_code,$team;	
						if(isset($query_data['GEM_EMP_ID'])){
							$link.="&emp_code=".$query_data['GEM_EMP_ID'];
						}else if($emp_code!=''){
							$link.="&emp_code=$emp_code&team=$team";
						}
						if(isset($query_data['indiv_order']) && $indiv_order=='on'){
							$link.="&indiv_order=".$indiv_order;
						}
						if(isset($query_data['chain_order']) && $chain_order=='on'){
							$link.="&chain_order=".$chain_order;
						}
						if(isset($query_data['GSM_ID'])){
							$link.="&metric_id=".$query_data['GSM_ID'];
						}
						$title="";						

						$var_mysort_i=$mysort[$i];
						$x_var_mysort_i=isset($query_data[$var_mysort_i])?$query_data[$var_mysort_i]:''; //NOTE: $ -of- $
						if($x_var_mysort_i=="[P]"){
							$title=" title='Productwise' ";
						}else if($x_var_mysort_i =="[V]"){
							$title=" title='Verticalwise' ";
						}else if($x_var_mysort_i=="[E]"){
							$title=" title='Executivewise' ";
						}
						if(isset($query_data['GCH_COMPLAINT_ID'])){
							$link.="&id=".$query_data['GCH_COMPLAINT_ID'];					
						}
						if(isset($query_data['lead_code'])){
						    $link.="&lead_code=".$query_data['lead_code'];
						}
						if(isset($query_data['GLH_LEAD_CODE'])){
							$link.="&lcode=".$query_data['GLH_LEAD_CODE'];
						}
						if(isset($query_data['lcode1'])){
							$link.="&lcode1=".$query_data['lcode1'];
						}
						if(isset($query_data['GCA_PARTNER_RELATIONSHIP'])){
							$link.="&cp_relationship=".$query_data['GCA_PARTNER_RELATIONSHIP'];
						}
						if(isset($query_data['CGI_INCHARGE_EMP_ID'])){
							$link.="&incharge_emp_code=".$query_data['CGI_INCHARGE_EMP_ID'];
						}
						if(isset($query_data['CGI_RELATIONSHIP_MANAGER'])){
							$link.="&relational_emp_code=".$query_data['CGI_RELATIONSHIP_MANAGER'];
						}
						if(isset($query_data['GLS_SUBTYPE_CODE'])){
							$link.="&lead_sub_type=".$query_data['GLS_SUBTYPE_CODE'];
						}
						if(isset($query_data['GPL_TAG_NAME']) && isset($query_data['GPL_TAG_ID']) && isset($query_data['GPL_PRICE_LIST_ID'])){
							$link.="&tag_id=".$query_data['GPL_TAG_ID']."&pl_code=".$query_data['GPL_PRICE_LIST_ID'];
						}
						if(isset($query_data['add_pincode'])  && $query_data['add_pincode']==1 ){
							$link.="&add_pincode=".$query_data['GLH_CUST_PINCODE'];
						}
						if((isset($query_data['event_id']) &&  $query_data['event_id']!='' ) or (isset($_REQUEST['event_id']) &&  $_REQUEST['event_id']!='' && $_REQUEST['event_id']!=0)){
							$event_id=(isset($query_data['event_id'])?$query_data['event_id']:$_REQUEST['event_id']);
							$link.="&event_id=".$event_id;
						}
						if(isset($query_data['level'])){
							if($query_data['level']=='1'){
								$res_que = execute_my_query("select group_concat(GCS_CODE) as leads from gft_customer_status_master where GCS_STATUS='A' and GCS_CUST_LIFECYCLE in (1,10)");
								if($row1 = mysqli_fetch_array($res_que)){
									$stat_lead_arr = explode(',', $row1['leads']);
									foreach ($stat_lead_arr as $val){
										$link.="&lead_status[]=$val";
									}
								}
							}
							if($query_data['level']=='2'){
								$link.="&lead_status[]=3";
							}
							if($query_data['level']=='3'){
								$link.="&lead_status[]=8&lead_status[]=9";
							}
						}
						if(isset($query_data['GCL_USER_ID'])){
							$link .= "&app_user_id=".$query_data['GCL_USER_ID'];
						}
						if(isset($query_data['GAL_OPERATION_ID'])){
							$link .= "&app_log_id=".$query_data['GAL_OPERATION_ID'];
						}
						if($link!='' && ($show_in_popup[$i]=='N' || $show_in_popup[$i]=='')){	
							$value_arr[$array_count][$i]="<a href=\"$link\" target=_blank>".$field_value."</a>";
						}else if($link!='' && $show_in_popup[$i]=='Y'){	
						 	$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$link',4);\" $title >".$field_value."</a>";
						}
							 	
					}
					else if(isset($report_link[$i]) && $report_link[$i]!='' && $pass_only_this_arg[$i]!=null){
						if(array_key_exists('json',$pass_only_this_arg[$i]) ){
							$link=$report_link[$i];
							$title=(isset($title)?$title:'');
							if((is_array($pass_only_this_arg[$i]))){
								for($j=0;$j<count($pass_only_this_arg[$i]);$j++){
									$pass_key = array_keys($pass_only_this_arg[$i]);
									$var_to_pass=$pass_only_this_arg[$i][$pass_key[$j]];
									$json = encode_str($query_data[$var_to_pass]); 
									$link.="?".$pass_key[$j]."=".(isset($query_data[$var_to_pass])?$json:(isset($_REQUEST[$var_to_pass])?$_REQUEST[$var_to_pass]:$var_to_pass));
								}
								if($link!='' && $show_in_popup[$i]=='N'){	
									$value_arr[$array_count][$i]="<a href=\"$link\" target=_blank>".$field_value."</a>";
								}else if($link!='' && $show_in_popup[$i]=='Y'){	
								 	$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$link',4);\" $title >".$field_value."</a>";
								}
							}else if($pass_only_this_arg[$i]=='Y'){/* value become a link */
								if($link!='' && ($show_in_popup[$i]=='N' || $show_in_popup[$i]=='')){	
									$value_arr[$array_count][$i]="<a href=\"$field_value\" target=_blank>".$field_value."</a>";
								}else if($link!='' && $show_in_popup[$i]=='Y'){	
								 	$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$field_value',4);\" $title >".$field_value."</a>";
								}
							}
						}else{
							$link=$report_link[$i];
							$title=(isset($title)?$title:'');
							if((is_array($pass_only_this_arg[$i]))){
								for($j=0;$j<count($pass_only_this_arg[$i]);$j++){
									$var_to_pass=$pass_only_this_arg[$i][$j];
									$link.="&".$var_to_pass."=".(isset($query_data[$var_to_pass])?$query_data[$var_to_pass]:(isset($_REQUEST[$var_to_pass])?$_REQUEST[$var_to_pass]:$var_to_pass));
								}
								if($link!='' && $show_in_popup[$i]=='N'){	
									$value_arr[$array_count][$i]="<a href=\"$link\" target=_blank>".$field_value."</a>";
								}else if($link!='' && $show_in_popup[$i]=='Y'){	
								 	$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$link',4);\" $title >".$field_value."</a>";
								}
							}else if($pass_only_this_arg[$i]=='Y'){/* value become a link */
								if($link!='' && ($show_in_popup[$i]=='N' || $show_in_popup[$i]=='')){	
									$value_arr[$array_count][$i]="<a href=\"$field_value\" target=_blank>".$field_value."</a>";
								}else if($link!='' && $show_in_popup[$i]=='Y'){	
								 	$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$field_value',4);\" $title >".$field_value."</a>";
								}
							}
						}
					}
					else{
						$value_arr[$array_count][$i]=stripslashes($field_value);
					}
					if(isset($value_arr_total[$i]) and $value_arr_total[$i]=='Y' and isset($mysort[$i])){
						$var_mysort_i=$mysort[$i]; 
						//$x_var_mysort_i=$$var_mysort_i; //NOTE: $ -of- $
						$x_var_mysort_i=isset($query_data[$var_mysort_i])?$query_data[$var_mysort_i]:0;
						$value_arr_total_value[0][$i]=(isset($value_arr_total_value[0][$i])?(int)$value_arr_total_value[0][$i]:0)+(int)$x_var_mysort_i;
					}else {
						$value_arr_total_value[0][$i]='';
					}
				}//end of else
				
			}//end of for
			$array_count++;
		}//end of while
		foreach ($value_arr as $i=>$vals){
		print_resultset(array($vals),$value_arr_width,$value_arr_align,$value_arr_class[$i],
				$row_class_in_arr,$table_row_id=null,$table_row_style=null,$value_arr_rowspan=null,
				$value_arr_colspan=null,$trow_name,$child);
		}
	
		
		if(isset($value_arr_total) && $count_num_rows>1){
		/*	for($pt=0;$pt<count($value_arr_total);$pt++){
				if($value_arr_total[$pt]=='Y'){
					$value_arr_total_value[0][0] ='Page Total'; $colspan_arr[0][0]=$pt;
				break;
				}
		}*/	
		if($sec_field_arr!=null){
			for($i=0;$i<count($mysort);$i++){
				if(isset($sec_field_arr[$i]) and $sec_field_arr[$i]=='Y'){
					$hrs_convert=secondsToTime($value_arr_total_value[0][$i]);
					$tfield_value=$hrs_convert['h'].':'.$hrs_convert['m'].':'.$hrs_convert['s'];		
					$value_arr_total_value[0][$i]=$tfield_value;						 		
				}
			}
		}
		if((!isset($value_arr_total_value[0][1]) or $value_arr_total_value[0][1]=='') && $navigation==true){
			$value_arr_total_value[0][1]='Page Total';
		}else if((!isset($value_arr_total_value[0][1]) or $value_arr_total_value[0][1]=='') && $navigation==false){
			$value_arr_total_value[0][1]='Total';
		}
		if(isset($value_arr_total_value[0])){
			foreach ($value_arr_total_value[0] as $key=>$val){
				if(isset($total_report_link[$key])){
					$value_arr_total_value[0][$key] = "<a target='_blank' href='$total_report_link[$key]'>$val</a>";
				}
			}
		}
		print_resultset($value_arr_total_value,$value_arr_width,$value_arr_align,null,array("highlight_blue"),'page_total',null,$colspan_arr,null,$trow_name,$child);
		}
		if($heading==true){
			echo "</tbody>";
		}
	}//end of if
	if($total_query!=null && $count_num_rows>1){
		echo '<tfoot>';
		generate_overall_total($total_query,$mysort,$report_link,$show_in_popup,$value_arr_align,$sec_field_arr);
		echo '</tfoot>';	
	}
	if($heading==true){
		if($count_num_rows>25){
		sortheaders($myarr,$mysort,$nav_struct,$sortbycol,$sorttype,$myarr_width,$noheader,$myarr_extra_link=null,
	$myarr_sub,$rowspan_arr,$colspan_arr,$myarr_sub1,$rowspan_arr1,$colspan_arr1);
		echo "</table></td></tr><tr><td>";
		print_dtable_navigation($count_num_rows,$nav_struct,'',"export_all_report.php",$query,$emyarr,
		$sp=null,1,$navigation,null,$emyarr_sub,$sms_category,$email_category,null,$mysort);
		echo "</table>";
		}else {
			echo "</table></td></tr></table>";
		}
		print_dtable_footer();	
	}
}//end of function


/**
 * @param string $total_query
 * @param string[int] $mysort
 * @param string[int] $report_link
 * @param string[int] $show_in_popup
 * @param string[] $value_arr_align
 * @param string[] $sec_field_arr
 *
 * @return void
 */
function generate_overall_total($total_query,$mysort,$report_link,$show_in_popup,$value_arr_align,$sec_field_arr){
	global $from_dt,$to_dt;

	$value_arr=/*. (mixed[int][int]) .*/ array();
	$result_total_query=execute_my_query($total_query);
	while($query_data=mysqli_fetch_array($result_total_query) ){
		//extract($query_data);
		
		$zone_id=isset($query_data['zone_id'])?$query_data['zone_id']:'';
		$GLC_PRODUCT_CODE=isset($query_data['GLC_PRODUCT_CODE'])?$query_data['GLC_PRODUCT_CODE']:'';
		$GLH_REFERENCE_GIVEN=isset($query_data['GLH_REFERENCE_GIVEN'])?$query_data['GLH_REFERENCE_GIVEN']:'';
		$GLH_LEAD_SOURCECODE=isset($query_data['GLH_LEAD_SOURCECODE'])?$query_data['GLH_LEAD_SOURCECODE']:'';
		$GLH_VERTICAL_CODE=isset($query_data['GLH_VERTICAL_CODE'])?$query_data['GLH_VERTICAL_CODE']:'';
		$GTM_VERTICAL_CODE=isset($query_data['GTM_VERTICAL_CODE'])?$query_data['GTM_VERTICAL_CODE']:'';
		$GPM_PINCODE=isset($query_data['GPM_PINCODE'])?$query_data['GPM_PINCODE']:'';
		$GEM_EMP_ID=isset($query_data['GEM_EMP_ID'])?$query_data['GEM_EMP_ID']:'';
		$terr_mgr_id=isset($query_data['terr_mgr_id'])?$query_data['terr_mgr_id']:'';
		$regional_mgr_id=isset($query_data['regional_mgr_id'])?$query_data['regional_mgr_id']:'';
		$zone_mgr_id=isset($query_data['zone_mgr_id'])?$query_data['zone_mgr_id']:'';

		$array_count=0; 
		for($i=0;$i<count($mysort);$i++){
			$link="";

			$var_mysort_i=$mysort[$i];
			$x_var_mysort_i=isset($query_data[$var_mysort_i])?$query_data[$var_mysort_i]:''; 

			    if($x_var_mysort_i=='') continue;
				$field_value=$x_var_mysort_i;
				if( isset($sec_field_arr[$i]) && $sec_field_arr[$i]=='Y'){
					$hrs_convert=secondsToTime($field_value);
					$field_value=$hrs_convert['h'].':'.$hrs_convert['m'];		 		
				}
		     	if(!isset($show_in_popup[$i])) {
				$show_in_popup[$i]='N';
			}

			if (!isset($report_link[$i])){
				$report_link[$i]='';
			}

				if($report_link[$i]!=''){
					$link=$report_link[$i]."&cmb_zone_mgr=$zone_mgr_id&cmb_reg_mgr=$regional_mgr_id&cmb_terr_mgr=$terr_mgr_id" .
			   		"&cmb_ex_name=$GEM_EMP_ID&emp_code=$GEM_EMP_ID&cmb_pincode=$GPM_PINCODE".
			   		"&vertical=$GTM_VERTICAL_CODE&reference_group=$GLH_LEAD_SOURCECODE&reference_list=$GLH_REFERENCE_GIVEN".
			   		"&productct_shown[]=$GLC_PRODUCT_CODE";
			   		if(isset($query_data['country_id']) and $query_data['country_id']!=''){	$link.="&cmbCountry=".$query_data['country_id']; }
			   		if(isset($query_data['state_id']) and $query_data['state_id']!=''){	$link.="&cmbState=".$query_data['state_id'];}
			   		if(isset($query_data['district_id']) and $query_data['district_id']!=''){	$link.="&cmbDist=".$query_data['district_id'];}			   		
					if(isset($query_data['zone_id']) and $query_data['zone_id']!=''){	$link.="&cmbZone=$zone_id"; }
			   		if(isset($query_data['region_id']) and $query_data['region_id']!=''){	$link.="&cmbRegion=".$query_data['region_id'];}
			   		if(isset($query_data['area_id']) and $query_data['area_id']!=''){	$link.="&cmbArea=".$query_data['area_id'];}
			   		if(isset($query_data['terr_id']) and $query_data['terr_id']!=''){	$link.="&cmbterr=".$query_data['terr_id'];}
					if(isset($query_data['from_dt']) and $query_data['from_dt']!=''){	$from_date=local_date_format($from_dt);
						$link.="&from_dt=$from_date";}
					if(isset($query_data['to_dt']) and $query_data['to_dt']!=''){	$to_date=local_date_format($to_dt);$link.="&from_dt=$to_date";}	
				}
				if($link!='' && $show_in_popup[$i]=='N'){	
				 	$value_arr[$array_count][$i]="<a href=\"$link\" target=_blank>".$field_value."</a>";
				}
				else if($link!='' && $show_in_popup[$i]=='Y'){	
				 	$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$link',4);\">".$field_value."</a>";
				}	 	
 				else{
					$value_arr[$array_count][$i]=$field_value;
				}
			 
		}//end of for
		if(empty($value_arr[0][1])){$value_arr[0][1]='Overall Total';}
		print_resultset($value_arr,null,$value_arr_align,null,array("highlight_green"),	null,'overarall_total',null,null);
	}//end of while
}//end of function	

/**
 * @param string $type
 * @param string $req_data
 * @param string $pcode
 * @param string $version
 * @param string $start_date
 * @param string $end_date
 *
 * @return void
 */
function webservice_to_mantis($type,$req_data='',$pcode='',$version='',$start_date='',$end_date=''){		
	$post_data = "&type=$type&req_data=".urlencode($req_data)."&pcode=$pcode&version=$version&start_date=$start_date&end_date=$end_date";
	global $global_sam_domain,$cached_test_mode;
	if((int)$cached_test_mode==1){
		$global_sam_domain = "http://samtest";
	}
	$path = "$global_sam_domain/issuemanager/Mantis_Integration/intimation_new_version.php";
	$ch = curl_init($path);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$curl_resp = (string)curl_exec($ch);
	$resp_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$curl_error = curl_error($ch);
	curl_close($ch);	
	$req_arr = array('url'=>$path,'post_data'=>$post_data);
	$resp_arr = array('response'=>$curl_resp,'code'=>$resp_code);
	if($curl_error!=''){
		$resp_arr['curl_error'] = $curl_error;
	}
	
	if($resp_code==200 && $type!="validate_complaint"){
		$json_arr = json_decode($curl_resp,true);
		if(count($json_arr) > 0){
			update_support_ids_from_mantis($json_arr);
		}
		insert_web_request_log(0, '', '53', $resp_arr,$req_arr);
	}else if($type=="validate_complaint"){
		$json_arr = json_decode($curl_resp,true);
		$severity_mismatch_list = (isset($json_arr['severity_mismatch_list'])?$json_arr['severity_mismatch_list']:null);
		if($severity_mismatch_list!=null){
			foreach ($severity_mismatch_list as $key => $val_arr){
				$comp_id 			= isset($val_arr['complaint_id'])?(int)$val_arr['complaint_id']:0;
				$severity 			= isset($val_arr['severity'])?mysqli_real_escape_string_wrapper($val_arr['severity']):'';
				$priority 			= isset($val_arr['priority'])?mysqli_real_escape_string_wrapper($val_arr['priority']):'';
				$edc				= isset($val_arr['edc'])?($val_arr['edc']):'';
				$assign_to_emp		= isset($val_arr['assign_to_emp'])?($val_arr['assign_to_emp']):'';
				$query = "select GCD_NATURE from gft_customer_support_hdr
						INNER JOIN gft_customer_support_dtl on(GCH_COMPLAINT_ID=GCD_COMPLAINT_ID AND GCH_LAST_ACTIVITY_ID=gcd_activity_id)
						where GCH_COMPLAINT_ID='$comp_id'";
				$gcd_nature = get_single_value_from_single_query("GCD_NATURE", $query);
				$severity_id = get_single_value_from_single_table("GSM_CODE", "gft_severity_master", "GSM_MANTIS_CODE", "$severity");
				$priority_id = get_single_value_from_single_table("GPM_CODE", "gft_priority_master", "GPM_MANTIS_CODE", "$priority");
				$assign_to_emp_id = get_single_value_from_single_table("GEM_EMP_ID", "gft_emp_master", "GEM_EMAIL", "$assign_to_emp");
				$update_query = "";
				if($severity_id!="")
					$update_query .= ", GCD_SEVERITY='$severity_id'";
				if($priority_id!="")
					$update_query .= ", GCD_PRIORITY='$priority_id'";
				if($assign_to_emp_id!="")
					$update_query .= ", GCD_PROCESS_EMP='$assign_to_emp_id'";
				if($edc!='')
					$update_query .= ", GCH_RESTORE_TIME='$edc'";
				if($gcd_nature=='24'){					
					if($update_query!=""){
						$update_query  =trim($update_query, ',');
						execute_my_query("UPDATE gft_customer_support_hdr 
						INNER JOIN gft_customer_support_dtl on(GCH_COMPLAINT_ID=GCD_COMPLAINT_ID AND GCH_LAST_ACTIVITY_ID=gcd_activity_id)
						SET $update_query where GCH_COMPLAINT_ID='$comp_id'");
					}
				}else{
					if($edc!="")
						execute_my_query("UPDATE gft_customer_support_hdr SET GCH_RESTORE_TIME='$edc' WHERE GCH_COMPLAINT_ID='$comp_id'");
					$compl_status = get_single_value_from_single_table("gch_current_status", "gft_customer_support_hdr", "GCH_COMPLAINT_ID", "$comp_id");
					$old_data_query=" select GCH_COMPLAINT_ID, '', now(), '9999', GCD_REPORTED_DATE, 24, gcd_status, GCD_CONTACT_TYPE, ".
							" GCD_CONTACT_PERSION, GCD_CUSTOMER_EMOTION, GCD_CUST_CALL_TYPE , gcd_contact_no, GCD_CONTACT_MAILID , ".($assign_to_emp_id!=''?"$assign_to_emp_id":"GCD_PROCESS_EMP").", ".
							" GCD_TO_DO, GCD_SCHEDULE_DATE, GCD_ESTIMATED_TIME , ".($severity_id!=''?"$severity_id":"GCD_SEVERITY").", ".($priority_id!=''?"$priority_id":"GCD_PRIORITY").", ".
							" GCD_LEVEL, GCD_PROMISE_MADE, ".($edc!=''?"'$edc'":"GCD_PROMISE_DATE").", GCD_FEEDBACK, ".
							" GCD_COMPLAINT_CODE , GCD_PRODUCT_CODE, GCD_PROBLEM_SUMMARY, GCD_PROBLEM_DESC, GCD_REMARKS, GCD_VISIT_REASON, GCD_VISIT_TIMEOUT, ".
							" GCD_EXTRA_CHARGES , GCD_VISIT_NO, GCD_RECEIVED_IN_HO, GCD_LAST_ACTIVITY_OF_DAY ".
							" from gft_customer_support_hdr join gft_customer_support_dtl on (gcd_activity_id=GCH_LAST_ACTIVITY_ID) where GCH_COMPLAINT_ID='$comp_id'";
					$ins_quer = " insert into gft_customer_support_dtl (GCD_COMPLAINT_ID, gcd_activity_id, GCD_ACTIVITY_DATE, GCD_EMPLOYEE_ID, GCD_REPORTED_DATE, GCD_NATURE, gcd_status, GCD_CONTACT_TYPE, ".
							" GCD_CONTACT_PERSION, GCD_CUSTOMER_EMOTION, GCD_CUST_CALL_TYPE , gcd_contact_no, GCD_CONTACT_MAILID , GCD_PROCESS_EMP, ".
							" GCD_TO_DO, GCD_SCHEDULE_DATE, GCD_ESTIMATED_TIME , GCD_SEVERITY, GCD_PRIORITY, GCD_LEVEL, GCD_PROMISE_MADE, GCD_PROMISE_DATE, GCD_FEEDBACK, ".
							" GCD_COMPLAINT_CODE , GCD_PRODUCT_CODE, GCD_PROBLEM_SUMMARY, GCD_PROBLEM_DESC, GCD_REMARKS, GCD_VISIT_REASON, GCD_VISIT_TIMEOUT, ".
							" GCD_EXTRA_CHARGES , GCD_VISIT_NO, GCD_RECEIVED_IN_HO, GCD_LAST_ACTIVITY_OF_DAY) ".
							" ($old_data_query) ";
					$result_insert=execute_my_query($ins_quer);
					$act_id = mysqli_insert_id_wrapper();
					updated_hdr_with_last_actid($comp_id,$act_id,$compl_status,false);
				}
			}
		}
	}
}



/**
 * @param string $lead_code
 * @param string $emp_id
 *
 * @return void
 */
function send_lead_monitor_mail($lead_code=null,$emp_id=null){
	global $address_fields,$query_contact_dtl;
	$limit_string='';$order_by_emp='';
	if($lead_code!=null){
		$chk1 = execute_my_query("select GLM_EMP_ID from gft_lead_monitors where GLM_LEAD_CODE='$lead_code' AND GLM_MONITOR_TYPE=1");
		if(mysqli_num_rows($chk1)==0){
			return ;
		}
		$limit_string=" limit 10";
		$addl_condition="and gld_lead_code='$lead_code' ";
	}else{
		$order_by_emp="em.gem_emp_name,";
		$fromtime=date('Y-m-d H:i:s', mktime('8','0','0', date("m"),date("d")-1,date("Y")));
		$totime=date('Y-m-d H:i:s', mktime('8','0','0', date("m"),date("d"),date("Y")));
		$addl_condition=" and GLD_DATE between '$fromtime' and '$totime' and vn.GAM_DISTANCE_TRAVELLED_REQ='Y' ";
	}
	$query="select em.gem_emp_id, GLH_LEAD_CODE $address_fields,GLD_VISIT_DATE,concat(em.gem_emp_name,'<br>',ifnull(group_concat(distinct(jem.gem_emp_name)),'')) as visited_by,". 
		" vn.GAM_ACTIVITY_DESC as visit_nature, " .
 		" GLD_NEXT_ACTION_DATE as next_action_date,nvn.GAM_ACTIVITY_DESC as next_action," .
 		" GLD_NEXT_ACTION_DETAIL as next_action_detail," .
 		" concat(ifnull(mc.gmc_name,''),'.',ifnull(GLD_NOTE_ON_ACTIVITY,'')) as my_comments ,".
 		" concat(ifnull(cm.gcm_feedback_desc,''),'.',ifnull(GLD_CUST_FEEDBACK,'')) as cust_feedback,
 		 GLD_APPORX_TIMETOCLOSE," .
 		" group_concat(jact_m.gam_activity_desc) as joint_act,gcs_name," .
 		" gld_license_value,gld_service_value,gld_date,
 		 concat(ifnull(GRL_NAME,''),' ',ifnull(GLD_REASON_FOR_STATUS_CHANGE_DTL,'')) reason,GPS_STATUS_NAME,".
 		" GLD_VISIT_IN_MIN as GLD_VISIT_IN_MIN" .
 		" from gft_activity a " .
 		" join gft_lead_hdr on (gld_lead_code=glh_lead_code) $query_contact_dtl " .
 		" join gft_emp_master em on (gld_emp_id=em.gem_emp_id)" .
 		" left join gft_customer_status_master cs on (GCS_CODE=gld_lead_status) ".
	    " left join gft_prospects_status_master ps on (GPS_STATUS_ID=GLD_PROSPECTS_STATUS) ".
 		" left join gft_reason_for_change_lstatus rcs on (GRL_ID=GLD_REASON_FOR_STATUS_CHANGE) ".
 		" left join gft_customer_feedback_master cm on cm.gcm_feedback_code=a.gld_cust_feedback_code " .
 		" left join gft_my_comments_master mc on mc.GMC_CODE=a.GLD_MY_COMMENTS_CODE " .
 		" left join gft_joint_visit_dtl jv on (jv.GJV_ACTIVITY_ID=a.gld_activity_id " .
 		" and jv.GJV_EMP_ID=a.gld_emp_id and jv.GJV_VISIT_DATE=a.gld_visit_date) " .
 		" left join gft_joint_activity jact on (jact.GJA_ACTIVITY_ID=a.gld_activity_id " .
 		" and jact.GJA_EMP_ID=a.gld_emp_id and  jact.GJA_LEAD_CODE=a.gld_lead_code and jact.GJA_VISIT_DATE=a.gld_visit_date)" .
 		" left join gft_activity_master jact_m on (jact_m.gam_activity_id=jact.GJA_VISIT_NATURE) " .
 		" left join gft_emp_master jem on (jem.gem_emp_id=jv.GJV_JOINT_EMP_ID) " .
 		" left join gft_activity_master vn on (vn.GAM_ACTIVITY_ID=GLD_VISIT_NATURE) " .
 		" left join gft_activity_master nvn on(nvn.GAM_ACTIVITY_ID=GLD_NEXT_ACTION) " .
 		" where (1) ". $addl_condition .
		" group by a.gld_activity_id,a.gld_emp_id order by $order_by_emp gld_visit_date desc $limit_string ";
	
	$result=execute_my_query($query);
	$content=/*. (string[int]) .*/ array();
	$thead='<tr><td>S.No</td><td>Customer Name and Address</td><td>Visit Date</td>' .
	'<td>Visited By</td><td>Visit Nature</td><td>Joint Action</td><td>Next Action Date</td>' .
	'<td>Next Action</td><td>Next Action detail</td><td>My Comments</td><td>Customer Feedback</td>' .
	'<td>Expected Date of Closure</td><td>Lead Status</td><td>Prospect Status</td><td>Reason</td><td>License Value</td>' .
	'<td>Service Value</td><td>Reported Date</td></tr>';
	$i=1;
	while($query_data=mysqli_fetch_array($result)){
		//$tooltip=get_necessary_data_from_query_for_tooltip($query_data);
		$lead_code=$query_data['GLH_LEAD_CODE'];
		$link_url="http://sam.gofrugal.com/visit_details.php?lead_code=$lead_code";
			$mail_data=array("$i",
			"Lead Code : <a href='$link_url' target=_blank>".$lead_code."</a><br>".
			$query_data['GLH_CUST_NAME'],
			$query_data['GLD_VISIT_DATE'].'<br>time spent :'.$query_data['GLD_VISIT_IN_MIN'].' min',
			$query_data['visited_by'],
			$query_data['visit_nature'],
			$query_data['joint_act'],
			$query_data['next_action_date'],
			$query_data['next_action'],
			$query_data['next_action_detail'],
			$query_data['my_comments'],
			$query_data['cust_feedback'],
			$query_data['GLD_APPORX_TIMETOCLOSE'],
			$query_data['gcs_name'],
			$query_data['GPS_STATUS_NAME'],
			$query_data['reason'],
			$query_data['gld_license_value'],
			$query_data['gld_service_value'],
			$query_data['gld_date']);
		if($lead_code!=null){
			if(!isset($content[0]))$content[0]='';
			$content[0].="<tr><td>".implode(" </td><td>",$mail_data)." </td></tr>\r\n";
		}else{
			$i=1;
			$gem_emp_id = (int)$query_data['gem_emp_id'];
			if(!isset($content[$gem_emp_id]))$content[$gem_emp_id]='';
			$content[$gem_emp_id].="<tr><td>".implode("&nbsp;</td><td>",$mail_data)."&nbsp;</td></tr>\r\n";
		}
		$i++;
	}
	if($lead_code==null){
		$emp_list_array=get_emp_list_by_group_filter(array(5,6));
		for($k=0;$k<count($emp_list_array);$k++){  
			$key=$emp_list_array[$k][0];
			$value=(isset($content[$key])?$content[$key]:'');
			$lead_dtl="";
			$emp_detail=get_emp_master($key,'');
			$dateon=date('Y-m-d');
			if(isset($emp_detail[0][2]) && $emp_detail[0][2]!=21 && $key!=SALES_DUMMY_ID){
				$cc='';
				$email_to=$emp_detail[0][4];
				$team=get_team_list($key,false,true,'A');
				if(count($team)>1){
					for($i=0;$i<count($team);$i++){
						if(isset($content[(int)$team[$i]['eid']]) and $content[(int)$team[$i]['eid']]!=''){
							$lead_dtl.="<tr><td colspan=\"18\" bgcolor=\"#8DDE70\">Activity Entry by Mr.".$team[$i]['name']."</td></tr>". $content[(int)$team[$i]['eid']];
						}
					}
				}else{
					$lead_dtl=$value;
				}
				if($lead_dtl!=''){
					$db_sms_content_config=array(
		            'lead_dtl'=>array($thead.$lead_dtl));
		            $message=get_formatted_mail_content($db_sms_content_config,$category=1,$mail_template_id=69);
		            $body_message=$message['content'];
		            $content_type=$message['content_type'];
		            $subject="Activity Entry Details";
		            $at_file=$message['Attachment'];
		            $rs=send_mail_function(get_samee_const("ADMIN_TEAM_MAIL_ID"),$email_to,$subject,$body_message,'',$cc,1,true,get_samee_const("PRESALES_MAIL_ID"));
				}
			}
		}
	}else{
		$add_check=(($emp_id!=null and $emp_id!='') ? " and gem_emp_id!=$emp_id ":" ");
		$result=execute_my_query(" SELECT group_concat(em.GEM_EMAIL) mail_ids FROM gft_lead_monitors join gft_emp_master em on (GLM_EMP_ID=gem_emp_id) " .
				" WHERE GLM_LEAD_CODE=$lead_code AND em.GEM_STATUS='A' AND GLM_MONITOR_TYPE=1 $add_check ");
		if($data=mysqli_fetch_array($result)){
			if($data['mail_ids']!='' && $data['mail_ids']!=null){
				$db_sms_content_config=array(
		            'lead_dtl'=>array($thead.$content[0]));
		        $message=get_formatted_mail_content($db_sms_content_config,$category=1,$mail_template_id=70);    
            	$body_message=$message['content'];
            	$content_type=$message['content_type'];
            	$subject="Activity Entry Details";
            	$at_file=$message['Attachment'];
				$rs=send_mail_function(get_samee_const("ADMIN_TEAM_MAIL_ID"),$data['mail_ids'],'Lead Monitor',$body_message,'',null,1,true,get_samee_const("PRESALES_MAIL_ID"));
			}
		}		
	}
}



/**
 * @param string $emp_id
 * @param boolean $send_mail
 * 
 * @return void
 */
function generate_login_for($emp_id,$send_mail=true){
	global $attach_path;;
	$query=" select GEM_EMAIL,GEM_ROLE_ID,GEM_EMP_NAME FROM gft_emp_master 
	where GEM_EMP_ID=$emp_id ";	
	$result=execute_my_query($query);
	$qd=mysqli_fetch_array($result);
			$GEM_EMAIL=$qd['GEM_EMAIL'];
			$GEM_ROLE_ID=(int)$qd['GEM_ROLE_ID'];
			$GEM_EMP_NAME=$qd['GEM_EMP_NAME'];
 	$passwd=generate_login($emp_id,$GEM_EMAIL);
 	$email_from=get_samee_const("PARTNER_MAIL_ID");
 	if($GEM_ROLE_ID==22){
 		$db_sms_content_config=array(
             'cp_name'=>array($GEM_EMP_NAME),
             'bce_email'=>array($GEM_EMAIL),
             'cp_password'=>array($passwd));
                    $message=get_formatted_mail_content($db_sms_content_config,$category=17,$mail_template_id=39);
                    $body_message=$message['content'];
                    $content_type=$message['content_type'];
                    $subject=$message['Subject'];
                    $at_file=$message['Attachment'];
	    send_formatted_mail_content($db_sms_content_config,17,39,null,null,$GEM_EMAIL,
	    		null, null, $email_from,'',$email_from);
 	}else{
 		$mail_template_id=40;
 		if($GEM_ROLE_ID==83){
 			$mail_template_id = 245;
 		}		
		$business_mager_id	=	get_partner_business_mgr($emp_id);
		$business_manager_emailid	=	get_email_addr($business_mager_id);
		$cc_emails	=	$email_from.",".$business_manager_emailid;
		$db_sms_content_config=array(
            'cp_name'=>array($GEM_EMP_NAME),
            'bce_email'=>array($GEM_EMAIL),
            'cp_password'=>array($passwd));
            $at_file="$attach_path/webcpagreement/cp-portal.doc";  
                   $message=get_formatted_mail_content($db_sms_content_config,$category=17,$mail_template_id);
                   $body_message=$message['content'];
                   $content_type=$message['content_type'];
                   $subject=$message['Subject'];
                   $at_file=$message['Attachment'];
           if($send_mail){
           	send_formatted_mail_content($db_sms_content_config,17,$mail_template_id,null,null,$GEM_EMAIL,
           			null, $cc_emails, $email_from,'',$email_from);
           }                          
 	}
}

/**
 * @param string $product_code
 * @param int $server_id
 *
 * @return void
 */
function truepos_connection_verification($product_code,$server_id=0){

	$Url='';
	$query_tennant= "SELECT SERVER_ID, GSM_DOMAIN_NAME FROM gft_tenant_master" .
		" join gft_server_master gs on(GSM_SERVER_ID=SERVER_ID) " . 
		" WHERE 1 ".((isset($server_id) and $server_id!='0')? " and SERVER_ID=$server_id" :" and GSM_IS_PRODUCTION='0' and TENANT_PRODUCT=$product_code ") .
		" ORDER BY SERVER_ID, TENANT_ID limit 1";
	$result_tennant=execute_my_query($query_tennant);
	if(mysqli_num_rows($result_tennant)!=0){
		if($data_tennant=mysqli_fetch_array($result_tennant)){
			$server_id=$data_tennant['SERVER_ID'];
			$Url=(string)str_replace('http://','',$data_tennant['GSM_DOMAIN_NAME']);
			$Url=(string)str_replace('/service','',$Url);
		}
	}
	if($Url!=''){
		$curl_proxyid='';
		$update_ch = curl_init();
		curl_setopt($update_ch, CURLOPT_URL,$Url);
		if($curl_proxyid!="")  curl_setopt($update_ch, CURLOPT_PROXY, $curl_proxyid);
		curl_setopt($update_ch, CURLOPT_POST,1);
		curl_setopt($update_ch,CURLOPT_POSTFIELDS,"xmldata=");
		curl_setopt($update_ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result= curl_exec($update_ch);
		if(!$result==true){
			echo "Server Down ! Please Try later ";
			exit;
		}
	}else{
		echo "Server not available ! Please Try later ";
		exit;
	}
}

/**
 * @param string $order_no
 *
 * @return string
 */
function order_no_for_product($order_no){
	$product_code="";
	$product_skew="";
	$order_no=substr($order_no,0,15);
	$query="select god_order_no, gop_product_code,gop_product_skew from gft_order_product_dtl,gft_order_hdr " .
				" where gop_order_no=god_order_no and god_order_no='$order_no' ";
	$result=execute_my_query($query,'web_custdateils.php',true,false);
	if(mysqli_num_rows($result)>0){
		if($data=mysqli_fetch_array($result)){
		$order_no=$data['god_order_no'];
		$order_no_split0=substr($order_no,0,5);
		$order_no_split1=substr($order_no,5,5);
		$order_no_split2=substr($order_no,10,5);
		$order_no_split3=substr($order_no,15,4);
		$product_code=$data['gop_product_code'];
		$product_skew=$data['gop_product_skew'];
		$registered_user_visit=true;
		}
	}
	$product=$product_code.$product_skew;
	return $product;
}


/**
 * @return string[int]
 */
function js_cointry_list(){
	$query_india_state= " select distinct(pm1.gpm_map_id),pm1.gpm_map_name from gft_political_map_master pm1" .
			" where pm1.gpm_map_type='S'  and pm1.GPM_MAP_PARENT_ID=2 order by pm1.gpm_map_name ";
	$result_india_state=execute_my_query($query_india_state);
	$stateoption="";
	$i=0;
	while($data=mysqli_fetch_array($result_india_state)){
		$stateoption.="<option value='".$data[1]."'>".$data[1]."</option>";
		$i++;
	}
	$country_list=get_country(false,true);
	$country_list_cmb="";
	$country_code="var country_code=new Array();";
	for($i=1;$i<count($country_list);$i++){
		$country_name=$country_list[$i][1];
		$country_list_cmb.="<option value=\"".$country_name."\">$country_name</option>";
		$country_code.= "country_code['".$country_list[$i][2]."']=\"$country_name\";";
	}
	return array($country_code,$country_list_cmb,$stateoption);
}


/**
 * @param int $audit_id
 * 
 * @return string
 */
function get_audit_mail_data($audit_id){
	//global $query_contact_dtl_inner,$only_address_fields;
	/*Note :   Using in Call back , Request Demo ,Contact Us submit  */
	$message="";
	$query="SELECT lh.GLH_LEAD_CODE,lh.GLH_CUST_NAME,GROUP_CONCAT(distinct concat(GAQ_QUESTION_TYPE,'? </td><td>Ans:',GAD_AUDIT_ANS) SEPARATOR '</td></tr><tr><td>') quest, " .
			" GAT_AUDIT_DESC, group_concat(distinct GPM_PRODUCT_NAME) product, a.GEM_EMP_NAME as name,GAH_CUSTOMER_COMMENTS,GAH_MY_COMMENTS,GAH_AUDIT_TYPE,GAH_DATE_TIME," .
			"  ccm.GCC_NAME 'CREATED_CATEGORY' ".// $only_address_fields,ccd.* " .
			" FROM gft_audit_hdr" .
			" JOIN gft_lead_hdr lh ON (lh.GLH_LEAD_CODE=GAH_LEAD_CODE)" .// $query_contact_dtl_inner .
			" JOIN gft_audit_type_master ON(GAH_AUDIT_TYPE=GAT_AUDIT_ID)" .
			" JOIN gft_emp_master a on(a.GEM_EMP_ID=GAH_AUDIT_BY) " .
			" join gft_lead_create_category ccm on (ccm.GCC_ID=GLH_CREATED_CATEGORY)" .
			" left join gft_lead_product_dtl on (GLC_LEAD_CODE=glh_lead_code) " .
			" left join gft_product_family_master on (GLC_PRODUCT_CODE=gpm_product_code)".
			" left join gft_audit_dtl on (GAH_AUDIT_ID=GAD_AUDIT_ID) " .
			" left JOIN gft_audit_question_master on(GAD_AUDIT_QID=GAQ_QUESTION_ID) " .
			" where GAH_AUDIT_ID=$audit_id GROUP BY GAH_LEAD_CODE,GAH_AUDIT_ID " ;
	$result_req=execute_my_query($query);
	$myarr=array("S.No.","Customer Name","Intersted Product","Audit Date","Audit Question-Answer","Customer comments","Audited By","Audit Type");
	$s=0;	
	if($data_req=mysqli_fetch_array($result_req)){
		//$tooltip=get_necessary_data_from_query_for_tooltip($data_req);
	    $customer_lead_code=$data_req['GLH_LEAD_CODE'];
	    $sales_cust_name=$data_req['GLH_CUST_NAME'];
		$sales_audit_date=$data_req['GAH_DATE_TIME']; 
		$sales_audit_que=($data_req['quest']!=''?"<table border=1><tr><td>".$data_req['quest']."</td></tr></table>":''); 
		$product=$data_req['product'];
		$sales_cust_comments=$data_req['GAH_CUSTOMER_COMMENTS'];
		$sales_my_comments=$data_req['GAH_MY_COMMENTS'];
        $sales_audit_by=$data_req['name'];
		$sales_audit_type=$data_req['GAT_AUDIT_DESC'];
		$s++;
		$sales_cust_name.="<br> Lead Code : <a href='http://sam.gofrugal.com/visit_details.php?lead_code=$customer_lead_code' target=_blank>".$customer_lead_code."</a>";
		//$sales_cust_name.='<br>'.$tooltip;
		$value_arr=array("$s",$sales_cust_name,$product,$sales_audit_date,$sales_audit_que,$sales_cust_comments,$sales_audit_by,$sales_audit_type);		
		$message='<table border=1>'.'<tr><td>'.implode('</td><td>',$myarr).'</td><tr>'.'<tr><td>'.implode('</td><td>',$value_arr).'</td><tr></table>';
	}
	return $message;
}


/**
 * @param int $cp_id
 * 
 * @return string[string]
 */
function get_sales_incharge_of_non_employee_group($cp_id=0){
	$incharge=/*. (string[string]) .*/ array();
	if($cp_id!=0){
		$select="select CGI_INCHARGE_EMP_ID,GEM_EMAIL from gft_cp_info " .
				"inner join gft_emp_master em on (gem_emp_id=CGI_INCHARGE_EMP_ID) where cgi_emp_id=$cp_id ";
		$result=execute_my_query($select);
		$qd=mysqli_fetch_array($result);
		$incharge['emp_id']=$qd['CGI_INCHARGE_EMP_ID'];
		$incharge['email_id']=$qd['GEM_EMAIL'];
	}
	return $incharge;
}

/**
 * @param string $cp_lcode
 *
 * @return string
 */
function get_partner_type($cp_lcode){
			$query="select GCA_CP_SUB_TYPE from gft_cp_info " .
					"inner join gft_cp_agree_dtl cg on (GCA_LEAD_CODE=CGI_LEAD_CODE AND CGI_CP_AGREENO=GCA_CP_AGREENO) " .
					"inner join gft_emp_master em on (gem_emp_id=cgi_emp_id AND GEM_STATUS='A' ) " .
					"where cgi_lead_code =$cp_lcode ";
			$result=execute_my_query($query);
			$qd=mysqli_fetch_array($result);
			$cp_type=$qd['GCA_CP_SUB_TYPE'];
			return $cp_type;	
}

/**
 * @param string $custCode
 * @param string $from_dt
 * @param string $to_dt
 * @param string $event_id
 *
 * @return void
 */
function show_Request_callback_from_edm($custCode,$from_dt,$to_dt,$event_id){
	$query="select GAH_CUSTOMER_COMMENTS,GAH_MY_COMMENTS,GAH_DATE_TIME from gft_audit_hdr where gah_lead_code=$custCode and GAH_AUDIT_TYPE=8 ";
	if($event_id!=''){
		$query.=" and GAH_CAMPAIGN_ID=$event_id ";
	}
	if($from_dt!=''){
		$query.=" and date(GAH_DATE_TIME) >= '".db_date_format($from_dt)."'";	
	}
	if($to_dt!=''){
		$query.=" and date(GAH_DATE_TIME) <= '".db_date_format($to_dt)."'";	
	}
	$myarr=array("S.No","Customer Comments","Information given while Registered ","Date & Time");
	$mysort=array("","GAH_CUSTOMER_COMMENTS","GAH_MY_COMMENTS","GAH_DATE_TIME");
	generate_reports('Call Back Registered Details',$query,$myarr,$mysort,$sms_category=null,$email_category=null,
		$previous_months_link=null,$value_arr_align=null,$myarr_width=null,$total_query=null,$sec_field_arr=null,
		$myarr_sub=null,$rowspan_arr=null,$colspan_arr=null,$myarr_sub1=null,$rowspan_arr1=null,$colspan_arr1=null,
		$report_link=null,$show_in_popup=null,$scorallable_tbody=false,$navigation=true,$order_by=null,$heading=true,
		$value_arr_total=null,$sorting_required=true,$child=0,$trow_name=null,$emyarr=null,$emyarr_sub=null,
		$pass_only_this_arg=null,$export_index=0);
		
	
}

/**
 *  @return string
 */
function get_latest_register_information_from_request(){
				$message2='';
				$message2.="<br><b>Latest Received Contact information </b><br>";
				$message2.="<br>Shop Name:".(isset($_REQUEST['shopName'])?(string)$_REQUEST['shopName']:'');
				$message2.="<br>Name :".(isset($_REQUEST['contactName'])?(string)$_REQUEST['contactName']:'');
				$message2.="<br>Mobile No :".(isset($_REQUEST['mob_no'])?(string)$_REQUEST['mob_no']:'');
				$message2.="<br>Email id :".(isset($_REQUEST['emailID'])?(string)$_REQUEST['emailID']:'');
				$message2.="<br>Country :".(isset($_REQUEST['country'])?(string)$_REQUEST['country']:'');
				$message2.="<br>State :".(isset($_REQUEST['state_name'])?(string)$_REQUEST['state_name']:'');
				$message2.="<br>City :".(isset($_REQUEST['city_name'])?(string)$_REQUEST['city_name']:'');
				$message2.="<br>Area :".(isset($_REQUEST['area_name'])?(string)$_REQUEST['area_name']:'');
				$message2.="<br>Pincode :".(isset($_REQUEST['pin_code'])?(string)$_REQUEST['pin_code']:'');
				if(isset($_REQUEST['lead_source']) and (string)$_REQUEST['lead_source']!=''){
					$ls=get_lead_source(null,(string)$_REQUEST['lead_source'],null);
					$lead_source=(isset($ls[0][1])?$ls[0][1]:'');
					$message2.="<br>Lead Source :".(isset($lead_source)?$lead_source:'');
				}
				if(isset($_REQUEST['lead_svalue']) and (string)$_REQUEST['lead_svalue']!=''){
					$message2.="<br>Others(ifany) :".(isset($_REQUEST['lead_svalue'])?(string)$_REQUEST['lead_svalue']:'');
				}
				if(isset($_REQUEST['vertical'])){
					$message2.="<br>Vertical:".get_vertical_name_for((string)$_REQUEST['vertical']);
				}
				$message2.="<br> Website :".(isset($_REQUEST['from_Nebula'])?'Nebula':'Existing');
				global $GLH_CREATED_CATEGORY;
				$resultc=execute_my_query("select GCC_NAME from gft_lead_create_category where gcc_id=$GLH_CREATED_CATEGORY ");
				if($resultc){
					$qdc=mysqli_fetch_array($resultc);
					$message2.="<br>Created Category :".$qdc['GCC_NAME'];
				}
				$message2.="<br>"; 
		return 	$message2;	
}				

/**
 * @param string[int][int] $tabs
 * @param int $default_param
 * 
 * @return void
 */

function add_inner_tabs($tabs,$default_param=1){
	global $me;
$param=isset($_REQUEST['param'])?(string)$_REQUEST['param']:$default_param;
$selectable_font_color='#010101';
$selected_font_color='#8e8c8e';
$additional_param='';
foreach($_REQUEST as $type => $value){
	if($type!='param'){
			$additional_param.="&".$type."=".(string)$value;
	}		
}
echo<<<END
<table cellpadding="0" cellspacing="0" border="0"><tr>
END;
for($i=0;$i<count($tabs);$i++){
	$tab_identity_font=$selectable_font_color;
	$title=$tabs[$i][1];
	$param_value=$tabs[$i][0];
	$tab_identity_font=$selectable_font_color;
	if($param_value==$param){ 
		$tab_identity_font=$selected_font_color; 
	}
	if($title=='')continue;
	
echo<<<END
<td vAlign="top" align="right" style="height:20">
<img alt="" src="images/header_start.gif" border="0" ></td>
<td class="formHeader" style="background-image:url(images/header_tile.gif); height:20;"  align="left"  >
<a href="$me?param={$param_value}$additional_param"><FONT color="$tab_identity_font">{$title}&nbsp;</font></a></td>
<td vAlign="top" align="left" style="height:20"> 
<img alt="" src="images/header_end.gif" border="0"></td>
END;
}
echo '</table>';
	
}

/**
 * @param string $domain_name
 * 
 * @return boolean
 */
function is_proper_domain_name($domain_name){
	$spl_pattern = '/[a-z]+\.[a-zA-Z0-9\-\.\_]+\.[a-z]/';
	$out=array();
	if(preg_match_all($spl_pattern,$domain_name,$out)){
		return true;
	}
	else{
		return false;
	}
}

/**
 * @param string $domain_name
 * 
 * @return boolean
 */
function is_proper_sub_domain_name($domain_name){
	$spl_pattern = '/^[a-zA-Z][a-zA-Z0-9\_]+$/';
	$out=array();
	if(preg_match_all($spl_pattern,$domain_name,$out)){
		return true;
	}else{
		return false;
	}
}

/**
 * @param string $domain_name
 * @param string $product_code
 * @param int $server_id
 * 
 * @return string[string]
 */
function check_availability_domain_name($domain_name,$product_code,$server_id=0){
    $product_code = $product_code=='762'?'601':$product_code;
	$query_tennant="SELECT SERVER_ID, TENANT_ID,TENANT_DOMAIN_NAME, TENANT_NAME,GSM_WPOS_ADDR FROM gft_tenant_master " .
			" join gft_server_master gs on(GSM_SERVER_ID=SERVER_ID ) " .
			" WHERE (substring(TENANT_DOMAIN_NAME,1,(LOCATE('.',TENANT_DOMAIN_NAME)-1))='".mysqli_real_escape_string_wrapper($domain_name)."' OR TENANT_DOMAIN_NAME='".mysqli_real_escape_string_wrapper($domain_name)."') ".
	       " and TENANT_PRODUCT=$product_code and TENANT_STATUS=1 ".($server_id>0?"AND SERVER_ID=$server_id":"") ;
	$result_tennant=execute_my_query($query_tennant);
	$return_array=/*. (string[string]) .*/ array();
	if(mysqli_num_rows($result_tennant)>=1 and $data_tennant=mysqli_fetch_array($result_tennant)){
		    $return_array['error_code']='601_Domain_Error_102';
		    $return_array['error_message']=get_samee_const('601_Domain_Error_102');
			return $return_array;
	}
	else {
		    
		    $query_tennant_space= "SELECT SERVER_ID, TENANT_ID,TENANT_DOMAIN_NAME, TENANT_NAME, GSM_WPOS_ADDR FROM gft_tenant_master" .
				" join gft_server_master gs on(GSM_SERVER_ID=SERVER_ID  and GSM_IS_PRODUCTION='0' ) " . 
				" WHERE TENANT_STATUS=0 and TENANT_PRODUCT=$product_code ".($server_id>0?" AND SERVER_ID=$server_id":"").
		        " ORDER BY SERVER_ID, TENANT_ID limit 1";
			$result_tennant_avail=execute_my_query($query_tennant_space);
			if(mysqli_num_rows($result_tennant_avail)==0){
				$return_array['error_code']='601_Domain_Error_101';
		    	$return_array['error_message']=get_samee_const('601_Domain_Error_101');
				return $return_array;				
			}	
	}
	$return_array['success']='true';
	return $return_array;
}

/**
 * @param string $email_id
 * @param string $product_code
 * @param string $mob_no
 *
 * @return mixed[string]
 */

function check_exist_user($email_id,$product_code,$mob_no=''){
	$total_mobile_rec	=	0;
	$query=" select  gcc_lead_code from gft_customer_contact_dtl " .
		   " join gft_install_dtl_new ins on (gid_lead_code=gcc_lead_code and gid_status='A' ) " .
		   " where gcc_contact_no='$email_id' and gid_product_code=$product_code limit 1 ";
	$result=execute_my_query($query);
	$return_array= /*. (mixed[string]) .*/ array();
	$qd=mysqli_fetch_array($result);
	if($mob_no!=''){
		$sph_no=substr($mob_no,-10);
		$where_con=" (gcc_contact_no ='$sph_no' or gcc_contact_no ='0$sph_no' or gcc_contact_no='91$sph_no' or gcc_contact_no='00$sph_no' or gcc_contact_no='$mob_no' or gcc_contact_no='0$mob_no' or gcc_contact_no='00$mob_no' )";
		$query_mob=	" select  gcc_lead_code from gft_customer_contact_dtl " .
					" join gft_install_dtl_new ins on (gid_lead_code=gcc_lead_code and gid_status='A' ) " .
					" where $where_con and gid_product_code=$product_code limit 1 ";
		$result_mob=execute_my_query($query_mob);
		if(mysqli_num_rows($result_mob)>=1){
			$total_mobile_rec	=	1;
		}
	}
	if(mysqli_num_rows($result)>=1 or $total_mobile_rec>=1)
	{
		$return_array['error_code']='601_EMAIL_ADDR_101';
		$return_array['error_message']=get_samee_const('601_EMAIL_ADDR_101');
		if(mysqli_num_rows($result)>=1 and $total_mobile_rec>=1){
			$return_array['error_code']='601_EMAIL_ADDR_103';
			$return_array['error_message']="Given Mobile no and Email Address are already registered with us. Use another valid Mobile no and Email Address";
		}else if(mysqli_num_rows($result)>=1){
			$return_array['error_code']='601_EMAIL_ADDR_101';
			$return_array['error_message']=get_samee_const('601_EMAIL_ADDR_101');
		}else if($total_mobile_rec>=1){
			$return_array['error_code']='601_EMAIL_ADDR_102';
			$return_array['error_message']="Given Mobile no is already registered with us. Use another valid Mobile no";
		}
		/*Given Email address is already registered with us. Use another valid Email address*/		
		
	}else {
		//$return_array['error_code']=0;
		//$return_array['error_message']='';
		//$return_array['lead_code']=$qd['gcc_lead_code'];
		
		$return_array['success']=true;
		
	}
	return $return_array;
}

/**
 * @return string
 */
function tech_support_incomming_call_filter(){
	global $vs_call_group,$support_product_group,$product,$call_center_shift,$support_product_group_multi;
	$query='';
		$query.=($vs_call_group!=0? ' and gvg_group_id='.$vs_call_group :'');
	    if(isset($support_product_group_multi) and $support_product_group_multi!=null and count($support_product_group_multi)>0) {
	    	$gps = implode("','",$support_product_group_multi);
	    	$query .= " and gvg_support_group in ('$gps') ";
	    } else if($support_product_group!='' and $support_product_group!='0') {
	    	$query .= " and gvg_support_group = '$support_product_group' ";
	    }
	    $query.=(($product!=0 and $product!='') ?" and GVG_PRODUCT='$product' ":'');
	    $query.=((!empty($call_center_shift))?" and GTC_BASED_ON='".$call_center_shift."'" :'');
	return $query;   
}

/**
 * @param string $scontact_no
 * 
 * @return string
 */
function generate_contact_string_check($scontact_no){


	if($scontact_no!=''){
		$contact_check='';
		$return_string="GCC_CONTACT_NO like '".$scontact_no."'";
		if(is_numeric($scontact_no)){
			$return_string=$return_string . " or GCC_CONTACT='".substr($scontact_no,-10)."'";
		}
		return " and ( $return_string ) ";
	}

/* OLD Implementation. Commented due to performance problem.
	if($scontact_no!=''){

		if(is_numeric($scontact_no)){
			$scontact_no=substr($scontact_no,-10);
		}

		$query="select concat(GCT_CHECK_WITH_PREFIX,'$scontact_no',GCT_CHECK_WITH_SUFFIX) CHECK_STRING ,group_concat(GCT_ID) CONTACT_TYPES " .
				"from gft_cust_contact_type_master group by CHECK_STRING ";
		$result=execute_my_query($query);
		$i=0;
		$return_string='';
		$num_rows=mysqli_num_rows($result);
		while($qdata=mysqli_fetch_array($result)){

			$check_string_val=mysqli_real_escape_string_wrapper($qdata['CHECK_STRING']);

			if($num_rows==1){
				$return_string=" and GCC_CONTACT_NO LIKE '".$check_string_val."' ";
				return $return_string;
			}
			if($i!=0){
				$return_string.=' or ';					
			}
				$return_string.=" ( GCC_CONTACT_NO LIKE '".$check_string_val."' and GCC_CONTACT_TYPE in (".$qdata['CONTACT_TYPES'].") )";
			$i++;			
		}
		return " and ( $return_string ) ";
		
	}
*/
	return '';
}

/**
 * @param string $xmlRaw
 * @param string[int] $xmlFieldNames
 * 
 * @return string[string]
 */
function give_parsedxml($xmlRaw,$xmlFieldNames){
	$parsedXML=/*. (string[string]) .*/ array();
	foreach ($xmlFieldNames as $xmlField) {
		if(strpos($xmlRaw,$xmlField)!==false){
			$parsedXML[$xmlField]=substr($xmlRaw,
				  strpos($xmlRaw,"<$xmlField>")+strlen("<$xmlField>"),
	       		  strpos($xmlRaw,"</$xmlField>")-strlen("<$xmlField>")
	       		  -strpos($xmlRaw,"<$xmlField>"));
		}
	}
	return $parsedXML;
}

/**
 * @param string $code
 * @param string $skew
 * @param boolean $from_store
 * 
 * @return boolean
 */
function is_this_user_based($code,$skew,$from_store=false){  
	$q1=" select GPM_ORDER_TYPE from gft_product_master ".
		" where GPM_PRODUCT_CODE='$code' and GPM_ORDER_TYPE=1 ";
	if(!$from_store){  //from store complete skew cannot be received in service_index
		$q1.=" and GPM_PRODUCT_SKEW='$skew'  ";
	}
	$r1 = execute_my_query($q1);
	if(mysqli_num_rows($r1) > 0){
		return true;
	}
	return false;
}

/**
 * @param int $length
 * 
 * @return string
 */
function generate_confirm_code_OTP($length = 5){
	$password = "";
	$possible = "0123456789";
	$i = 0;
	while ($i < $length) {
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
		if (!strstr($password, $char)) {
			$password .= $char;
			$i++;
		}
	}
	return $password;
}

/**
 * @param string $me
 * @param int $uid
 * @param string $user_query
 * @param int $affected_rows
 * @param string $comments
 *
 * @return void
 */
function take_log_for_userquery($me,$uid,$user_query,$affected_rows,$comments) {
	$time=date('Y-m-d H:i:s');
	$str_query=mysqli_real_escape_string_wrapper($user_query);
	$str_comments=mysqli_real_escape_string_wrapper($comments);
	$query_log="insert into gft_query_log(GQL_FILENAME,GQL_EMP_ID,GQL_DATE,GQL_QUERY,GQL_AFFECTED_QUERY,GQL_COMMENTS)
	values('$me','$uid','$time','$str_query','$affected_rows','$str_comments')";
	execute_my_query($query_log);
}

/**
 * @param string $GOD_ORDER_NO
 * @param string $debit_amt
 *
 * @return void
 */
function partner_order_cancel_credit_note($GOD_ORDER_NO,$debit_amt) {
	global $me,$uid;
	$ord_query = "select GOD_LEAD_CODE, GOD_EMP_ID, GOD_COLLECTION_REALIZED from gft_order_hdr where GOD_ORDER_NO='$GOD_ORDER_NO' ";
	$ord_res = execute_my_query($ord_query);
	if($data = mysqli_fetch_array($ord_res)){
		$GOD_LEAD_CODE=$data['GOD_LEAD_CODE'];
		$order_amt=(float)$data['GOD_COLLECTION_REALIZED'];
		$order_by=$data['GOD_EMP_ID'];
	}else{
		show_my_alert_msg("Order Not Found in Header");
		return;
	}
	$balance_order_no = get_partner_balance_order($GOD_LEAD_CODE);
	if($debit_amt!='0' && $debit_amt!=''){
		$order_amt = $order_amt - (float)$debit_amt;
		//debit note entry log
		$que=" insert into gft_credit_note_log (GCN_ID, GCN_TYPE, GCN_PARTNER_ID, GCN_LEAD_CODE, GCN_ORDER_NO, ".
				" GCN_AMT_GIVEN, GCN_GIVEN_BY, GCN_GIVEN_DATE, GCN_EMP_COMMENTS) values ".
				" ('','D','$order_by','$GOD_LEAD_CODE','$GOD_ORDER_NO','$debit_amt','$uid',now(),'Deduction for cancelled Order $GOD_ORDER_NO') ";
		if(!execute_my_query($que)){
			show_my_alert_msg('Error occured while saving Debit Note Log');
		}
	}
	if((int)$order_amt!=0){
		$grd_receipt=get_receipt_id();
		$now=date('Y-m-d');
		$reported_date=date('Y-m-d H:i:s',time());
		$COMMENTS='For cancelled order '.$GOD_ORDER_NO.' ';
		if($debit_amt!='0'){
			$COMMENTS.='with a deduction of '.$debit_amt.' ';
		}
		$query_ins1="insert into gft_receipt_dtl (GRD_RECEIPT_ID,GRD_DATE, GRD_EMP_ID, GRD_RECEIPT_TYPE, GRD_RECEIPT_AMT," .
				"GRD_CHEQUE_DD_NO, GRD_CHEQUE_DD_DATE, GRD_BANK_NAME, GRD_DEPOSITED_BANK, " .
				"GRD_DEPOSITED_DATE, GRD_DEPOSITED_BRANCH_NAME, GRD_DEPOSITED_BRANCH_CODE," .
				"GRD_DEPOSTIT_CHALLAN_NO, GRD_CHEQUE_CLEARED_DATE, GRD_HAND_OVER_TO, GRD_STATUS," .
				"GRD_TERMS_REGARDING_COLLECTION,  GRD_CHECKED_WITH_LEDGER,GRD_HAND_OVER_DATE, GRD_LEAD_CODE, GRD_REPORTED_DATE, ".
				" GRD_ENTRY_BY_EMP_ID,grd_entry_date) values ".
				"('$grd_receipt','$now','$uid','8','$order_amt','','','','','','','','',now(),'','P','$COMMENTS','Y','', ".
				" '$GOD_LEAD_CODE','$reported_date',$uid,now())";
		$result_ins1=execute_my_query($query_ins1,$me,true,true);
		$reason_e=-1;
		$query_ins2="insert into gft_collection_receipt_dtl  (GCR_ORDER_NO,GCR_RECEIPT_ID, GCR_PAYMENT_FORID, GCR_AMOUNT, GCR_REASON ) values" .
				" ('$balance_order_no','$grd_receipt','a','$order_amt','$reason_e')";
		$result_ins2=execute_my_query($query_ins2,$me,true,true);
		//credit note entry log
		$que=" insert into gft_credit_note_log (GCN_ID, GCN_TYPE, GCN_PARTNER_ID, GCN_LEAD_CODE, GCN_ORDER_NO, ".
				" GCN_AMT_GIVEN, GCN_GIVEN_BY, GCN_GIVEN_DATE, GCN_EMP_COMMENTS) values ".
				" ('','C','$order_by','$GOD_LEAD_CODE','$GOD_ORDER_NO','$order_amt','$uid',now(),'for cancelled Order $GOD_ORDER_NO') ";
		if(!execute_my_query($que)){
			show_my_alert_msg('Error occured while saving Credit Note Log');
		}
		if(!$result_ins1 || !$result_ins2){
			show_my_alert_msg('Error occured in giving Credit Note to Partner');
		}else{
			show_my_alert_msg('A Credit Note of Rs.'.$order_amt.' given to partner');
		}
	}
}

/**
 * @param string $lead_code
 * 
 * @return string
 */
function get_lead_type_for_lead_code($lead_code) {
	$que = "select GLH_LEAD_TYPE from gft_lead_hdr where GLH_LEAD_CODE='$lead_code' ";
	$res = execute_my_query($que);
	if($dat = mysqli_fetch_array($res)){
		$lead_type = $dat['GLH_LEAD_TYPE'];
		return $lead_type;
	}
	return '';   //invalid lead code
}


/**
 * @param string $lead_code
 * 
 * @return string
 */
function get_lead_type_for_hq_lead_code($lead_code) {
	$que = "select GLH_LEAD_TYPE from gft_lead_hdr where GLH_LEAD_CODE='$lead_code' and GLH_MAIN_PRODUCT=6 ";
	$res = execute_my_query($que);
	if($dat = mysqli_fetch_array($res)){
		$lead_type = $dat['GLH_LEAD_TYPE'];
		return $lead_type;
	}
	return '';   //invalid lead code
}

/**
 * @param string $group_code
 * @param string $sms_content
 * @param string $category
 * @param string $sender_emp_id
 * @param string $cron_date
 *      
 * @return void
 */
function update_sms_cron($group_code,$sms_content,$category,$sender_emp_id,$cron_date){
	$escape_sms=mysqli_real_escape_string_wrapper($sms_content);
        $sql="insert into gft_cron_sms_schedule (GROUP_CODE,SMS_CONTENT ,CATEGORY , SENDER_EMP_ID ,  " .
                 "CRON_DATE)  " .
                 "  values('$group_code','$escape_sms','$category','$sender_emp_id'," .
                 " '$cron_date') ";
        execute_my_query($sql);
}

/**
 * @param string $complaint_id
 * 
 * @return int
 */
function get_no_response_count($complaint_id) {
	$no_resp=0;
	$query = " select sum(if(GCD_NATURE='14',1,0)) as no_resp ".
			" from gft_customer_support_dtl where GCD_COMPLAINT_ID='$complaint_id' group by GCD_COMPLAINT_ID";
	$res = execute_my_query($query);
	if($row = mysqli_fetch_array($res)){
		$no_resp = (int)$row['no_resp'];
	}
	return $no_resp;
}

/**
 * @param string $lead_code
 * @param string $number
 * 
 * @return int
 */
function get_contact_type_of_phone_no($lead_code,$number){
	$cont_type = 0;
	$query = "select GCC_CONTACT_TYPE from gft_customer_contact_dtl where GCC_LEAD_CODE='$lead_code' and GCC_CONTACT_NO='$number'";
	$res = execute_my_query($query);
	if($dat = mysqli_fetch_array($res)) {
		$cont_type = (int)$dat['GCC_CONTACT_TYPE'];
	}
	return $cont_type;
}

/**
 * @param string $ins_root_order_no
 * @param string $ins_root_fullfillno
 * @param string $ins_product_code
 * 
 * @return string
 */
function get_custom_license_query($ins_root_order_no,$ins_root_fullfillno, $ins_product_code){
	$custm_que= " select GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GFT_SKEW_PROPERTY,GSPM_DISCOUNT_PERCENTAGE,GPM_NET_RATE  from gft_order_hdr " .
				" join gft_order_product_dtl on (GOP_ORDER_NO = GOD_ORDER_NO and GOD_ORDER_STATUS='A') ".
				" left join gft_cp_order_dtl on (GOP_ORDER_NO=GCO_ORDER_NO and GOP_PRODUCT_CODE=GCO_PRODUCT_CODE and GOP_PRODUCT_SKEW=GCO_SKEW) ".
				" inner join gft_product_master ON(GPM_PRODUCT_CODE=GOP_PRODUCT_CODE AND GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW ) " .
				" join gft_skew_property_master on (GSPM_CODE = GFT_SKEW_PROPERTY) " .
				" where GPM_PRODUCT_TYPE=8 and if(GOD_ORDER_SPLICT=0,GOP_REFERENCE_ORDER_NO,GCO_REFERENCE_ORDER_NO)='$ins_root_order_no' ".
				" and if(GOD_ORDER_SPLICT=0,GOP_REFERENCE_FULLFILLMENT_NO,GCO_REFERENCE_FULLFILLMENT_NO)=$ins_root_fullfillno and GOP_PRODUCT_CODE='$ins_product_code'";	
	return $custm_que;
}

/**
 * @param string $gem_emp_id
 * @param string $gem_lead_code
 * 
 * @return boolean
 */
function update_employee_lead_code($gem_emp_id, $gem_lead_code){
	
	$update_que = " update gft_emp_master set GEM_LEAD_CODE = '$gem_lead_code' where GEM_EMP_ID='$gem_emp_id' ";
	$res = execute_my_query($update_que);
	if($res) {
		return true;
	}
	return false;
	
}
/**
 * @param string $order_no
 * @param string $ord_status
 *
 * @return void
 */
function update_coupon_status_when_edit_order($order_no,$ord_status){
	if($ord_status=='C'){
		$res_check_coupon	=	execute_my_query(" SELECT GCD_COUPON_NO FROM gft_coupon_distribution_dtl WHERE (SUBSTR(GCD_REF_ORDER_NO,1,15)='$order_no' OR SUBSTR(GCD_ORDER_NO,1,15)='$order_no') AND GCD_SIGNED_OFF='N' ");
		if(mysqli_num_rows($res_check_coupon)>0){
			execute_my_query("UPDATE gft_coupon_distribution_dtl SET GCD_SIGNED_OFF='C' WHERE (SUBSTR(GCD_REF_ORDER_NO,1,15)='$order_no' OR SUBSTR(GCD_ORDER_NO,1,15)='$order_no') AND GCD_SIGNED_OFF='N'");
			$sql_check_task		=	execute_my_query("SELECT GIMC_COMPLAINT_ID FROM gft_cust_imp_ms_current_status_dtl WHERE GIMC_OPCODE LIKE '%$order_no%' and GIMC_APPROVAL!='Y'");
			while($get_row=mysqli_fetch_array($sql_check_task)){
				execute_my_query("UPDATE gft_customer_support_hdr SET gch_current_status='T1' WHERE GCH_COMPLAINT_ID='".$get_row['GIMC_COMPLAINT_ID']."' AND  gch_current_status='T6'");
			}
		}
		
	}else if($ord_status=='A'){ // Changed coupon status as active if order moved from cancel to active
		$res_check_coupon	=	execute_my_query(" SELECT GCD_COUPON_NO FROM gft_coupon_distribution_dtl WHERE (SUBSTR(GCD_REF_ORDER_NO,1,15)='$order_no' OR SUBSTR(GCD_ORDER_NO,1,15)='$order_no') AND GCD_SIGNED_OFF='C' ");
		if(mysqli_num_rows($res_check_coupon)>0){
			execute_my_query("UPDATE gft_coupon_distribution_dtl SET GCD_SIGNED_OFF='N' WHERE (SUBSTR(GCD_REF_ORDER_NO,1,15)='$order_no' OR SUBSTR(GCD_ORDER_NO,1,15)='$order_no') AND GCD_SIGNED_OFF='C'");
			$sql_check_task		=	execute_my_query("SELECT GIMC_COMPLAINT_ID FROM gft_cust_imp_ms_current_status_dtl WHERE GIMC_OPCODE LIKE '%$order_no%' and GIMC_APPROVAL!='Y'");
			while($get_row=mysqli_fetch_array($sql_check_task)){
				execute_my_query("UPDATE gft_customer_support_hdr SET gch_current_status='T6' WHERE GCH_COMPLAINT_ID='".$get_row['GIMC_COMPLAINT_ID']."' AND gch_current_status='T1'");
			}
		}
	}	
}

/**
 * @param string $return_value
 * @param string $tablename
 * @param string $conditon
 * @param string $value
 * @param string $order_by
 *
 * @return string
 *
 */
function get_single_value_from_single_table($return_value,$tablename,$conditon,$value,$order_by=""){
	$query= "select $return_value from $tablename where $conditon = '$value'  $order_by LIMIT 1 ";
	$qdata=execute_my_query($query);
	if($data=mysqli_fetch_array($qdata)){
		return $data[$return_value];
	}
	return '';
}
/**
 * @param string $return_value
 * @param string $query_string
 *
 * @return string
 */
function get_single_value_from_single_query($return_value,$query_string){
	$qdata=execute_my_query($query_string);
	if($data=mysqli_fetch_array($qdata)){
		return $data[$return_value];
	}
	return '';
}
/**
 * @param string $return_value
 * @param string $tablename
 *
 * @return string[int]
 *
 */
function get_one_dimensional_array_from_single_table($return_value,$tablename){
	$ret_arr = /*. (string[int]) .*/array();
	$query= "select $return_value from $tablename";
	$qdata=execute_my_query($query);
	while($data=mysqli_fetch_array($qdata)){
		$ret_arr[] =  $data[$return_value];
	}
	return $ret_arr;
}
/**
 * @param string $key_field
 * @param string $value_field
 * @param string $tablename
 *
 * @return string[string]
 *
 */
function get_one_dimensional_array_from_single_table_with_key($key_field,$value_field,$tablename){
	$ret_arr = /*. (string[string]) .*/array();
	$query= "select $key_field,$value_field from $tablename";
	$qdata=execute_my_query($query);
	while($data=mysqli_fetch_array($qdata)){
		$key=$data[$key_field];
		$ret_arr["$key"] =  $data[$value_field];
	}
	return $ret_arr;
}

/**
 * @param string $lead_code
 * @param string $root_order
 * @param string $root_fulfil
 * @param string $product_code
 * @param string $product_skew
 * @param string $remarks
 * @param int $status_id
 * @param boolean $get_root
 * @param int $no_of_days
 * @param string $approved_by
 * @param string $prev_status_id
 * 
 * @return string
 */
function get_insert_query_for_lic_approval_log($lead_code,$root_order,$root_fulfil,$product_code,$product_skew,$remarks,$status_id=0,$get_root=false,$no_of_days=0,$approved_by=SALES_DUMMY_ID,$prev_status_id='1'){
	if($status_id==0){
		$query = "select GLA_STATUS_ID from gft_lic_approved_log where GLA_LEAD_CODE=$lead_code and GLA_ORDER_NO='$root_order'".
				" and GLA_FULLFILLMENT_NO=$root_fulfil and GLA_PRODUCT_CODE=$product_code and GLA_PRODUCT_SKEW='$product_skew' order by GLA_ID desc limit 1";
		$res = execute_my_query($query);
		if($row_data = mysqli_fetch_array($res)){
			$status_id = (int)$row_data['GLA_STATUS_ID'];
		}
	}
	$prev_status_name = get_single_value_from_single_table('GLS_STATUS_NAME', 'gft_lic_status_master', 'GLS_ID', (string)$prev_status_id);
	$status_name = get_single_value_from_single_table('GLS_STATUS_NAME', 'gft_lic_status_master', 'GLS_ID', (string)$status_id);
	if($get_root){
		$root_query=" select GID_PRODUCT_CODE,GID_PRODUCT_SKEW from gft_install_dtl_new where GID_ORDER_NO='$root_order' ".
					"  and GID_FULLFILLMENT_NO=$root_fulfil and GID_LIC_PCODE=$product_code and GID_LIC_PSKEW='$product_skew' ";
		$root_res = execute_my_query($root_query);
		if($res_data = mysqli_fetch_array($root_res)){
			$product_code = $res_data['GID_PRODUCT_CODE'];
			$product_skew = $res_data['GID_PRODUCT_SKEW'];
		}
	}
	$query =" INSERT INTO GFT_LIC_APPROVED_LOG(GLA_LEAD_CODE, GLA_ORDER_NO, GLA_FULLFILLMENT_NO, GLA_PRODUCT_CODE, GLA_PRODUCT_SKEW, ".
	" GLA_NOOF_DAYS, GLA_OLD_STATUS, GLA_STATUS_CHANGEDAS, GLA_STATUS_ID, GLA_APPROVED_ON, GLA_EXPIRE_DATE, GLA_APPROVED_BY, GLA_EMP_COMMENTS) ".
	" VALUES ('$lead_code', '$root_order', '$root_fulfil', '$product_code', '$product_skew', ".
	" '$no_of_days','$prev_status_name', '$status_name', '$status_id', now(), now(), '$approved_by', '$remarks')";
	return $query;
}

/**
 * @param string $contact_info
 * @param string $not_in_lead
 * @param boolean $corp_lead_type
 * @param string $ref_lead
 * @param string $primary_lead_code
 * 
 * @return string[int]
 */
function get_lead_code_for_contact_no($contact_info, $not_in_lead='', $corp_lead_type=false, $ref_lead='', $primary_lead_code=''){
	$lead_code_arr = /*. (string[int]) .*/array();
	if($contact_info==''){ //dont proceed
		return $lead_code_arr;  
	}
	$wh_cond = '';
/*  	if( (is_numeric(substr($contact_info, -10))) && (strlen($contact_info) > 9) ){
 		$wh_cond .= " and GCC_CONTACT='".substr($contact_info, -10)."'";
	}else{
		$wh_cond .= " and GCC_CONTACT_NO='$contact_info'";
	} */
	if(is_numeric($contact_info)){ 
 		$wh_cond .= "and ".getContactDtlWhereCondition('GCC_CONTACT_NO', $contact_info);
	}else{
		$wh_cond .= " and GCC_CONTACT_NO='$contact_info' ";
	}
	if($not_in_lead!=''){
		$wh_cond .= " and GLH_LEAD_CODE!='$not_in_lead' and (GLH_REFERENCE_GIVEN!='$not_in_lead' or GLH_REFERENCE_GIVEN is null) ";
	}
	if($corp_lead_type){
		$wh_cond .= " and GLH_LEAD_TYPE not in (3,13) ";
	}
	if( ($ref_lead!='') && ($ref_lead!='0') ){
		$wh_cond .=" and GLH_LEAD_CODE!='$ref_lead' and (GLH_REFERENCE_GIVEN!='$ref_lead' or GLH_REFERENCE_GIVEN is null) ";
	}
	if($primary_lead_code!='' && $primary_lead_code!='0'){
	    $wh_cond .=" and GLH_LEAD_CODE='$primary_lead_code'";
	}
	//88473 - unknown leadcode to skip
	$query = " select GLH_LEAD_CODE, GLH_STATUS, GLH_LFD_EMP_ID,GLH_LMT_EMP_ID,GCC_ID from gft_customer_contact_dtl ".
			 " join gft_lead_hdr on (GCC_LEAD_CODE = GLH_LEAD_CODE) ".
			 " left join gft_pos_users on (GPU_CONTACT_ID=GCC_ID) ".
			 " where 1 $wh_cond and GLH_LEAD_CODE!='88473' and GLH_LEAD_TYPE!=7 and if(GPU_CONTACT_ID is null,1,GPU_CONTACT_STATUS='A') ".
			 " group by GCC_LEAD_CODE ";
	$con_res = execute_my_query($query);
	if($con_row = mysqli_fetch_array($con_res)){
		$lead_code_arr[0] = $con_row['GLH_LEAD_CODE'];
		$lead_code_arr[1] = $con_row['GLH_STATUS'];
		$lead_code_arr[2] = $con_row['GLH_LFD_EMP_ID'];
		$lead_code_arr[3] = $con_row['GLH_LMT_EMP_ID'];
		$lead_code_arr[4] = $con_row['GCC_ID'];
	}
	return $lead_code_arr;
} 

/**
 * @param string $lead_code
 * @param string $old_status
 * @param string $old_lfd
 * @param string[string] $new_lead_details
 * @param string $contact_ui
 * @param string $status_reason
 * @param string $owner_reason
 * @param string $activity_sumbit_type
 * @param int $lmt_incharge
 * 
 * @return void
 */
function save_duplicate_lead_activity_entry($lead_code, $old_status, $old_lfd, $new_lead_details,$contact_ui='',$status_reason='',$owner_reason='',$activity_sumbit_type='',$lmt_incharge=0){
	global $uid;
	$activity_emp = $uid;
	if($activity_emp==''){  //through web service and registration
		$activity_emp = SALES_DUMMY_ID;
	}
	$activity_dtl	= /*. (string[string]) .*/array();
	$glh_cust_name = isset($new_lead_details['GLH_CUST_NAME'])?$new_lead_details['GLH_CUST_NAME']:'';
	$glh_door_no = isset($new_lead_details['GLH_DOOR_APPARTMENT_NO'])?$new_lead_details['GLH_DOOR_APPARTMENT_NO']:'';
	$glh_block_name = isset($new_lead_details['GLH_BLOCK_SOCEITY_NAME'])?$new_lead_details['GLH_BLOCK_SOCEITY_NAME']:'';
	$glh_addr1 = isset($new_lead_details['GLH_CUST_STREETADDR1'])?$new_lead_details['GLH_CUST_STREETADDR1']:'';
	$glh_addr2 = isset($new_lead_details['GLH_CUST_STREETADDR2'])?$new_lead_details['GLH_CUST_STREETADDR2']:'';
	$glh_area = isset($new_lead_details['GLH_AREA_NAME'])?$new_lead_details['GLH_AREA_NAME']:'';
	$glh_city = isset($new_lead_details['GLH_CUST_CITY'])?$new_lead_details['GLH_CUST_CITY']:'';
	$glh_state = isset($new_lead_details['GLH_CUST_STATECODE'])?$new_lead_details['GLH_CUST_STATECODE']:'';
	$glh_pincode = isset($new_lead_details['GLH_CUST_PINCODE'])?$new_lead_details['GLH_CUST_PINCODE']:'';
	$glh_vertical = isset($new_lead_details['GLH_VERTICAL_CODE'])?$new_lead_details['GLH_VERTICAL_CODE']:'';
	$glh_status = isset($new_lead_details['GLH_STATUS'])?$new_lead_details['GLH_STATUS']:'';
	$glh_lfd = isset($new_lead_details['GLH_LFD_EMP_ID'])?$new_lead_details['GLH_LFD_EMP_ID']:'9999';
	$lead_type_id = isset($new_lead_details['GLH_LEAD_TYPE'])?(int)$new_lead_details['GLH_LEAD_TYPE']:0;
	$lead_subtype_id = isset($new_lead_details['GLH_LEAD_SUBTYPE'])?(int)$new_lead_details['GLH_LEAD_SUBTYPE']:0;
	$prospect_subtype_id = isset($new_lead_details['GLH_PROSPECTS_STATUS'])?(int)$new_lead_details['GLH_PROSPECTS_STATUS']:0;
	$lead_type = $lead_subtype = $prospect_subtype = '';
	if($lead_type_id!=0){
		$lead_type = get_single_value_from_single_table("GLD_TYPE_NAME", "gft_lead_type_master", "GLD_TYPE_CODE", $lead_type_id);
	}
	if($lead_subtype_id!=0){
		$lead_subtype = get_single_value_from_single_table("GLS_SUBTYPE_NAME", "gft_lead_subtype_master", "GLS_SUBTYPE_CODE", $lead_subtype_id);
	}
	if($prospect_subtype_id!=0){
		$prospect_subtype = get_single_value_from_single_table("GPS_STATUS_NAME", "gft_prospects_status_master", "GPS_STATUS_ID", $prospect_subtype_id);
	}
	
	$msg ="<table border='1' width='70%' >";
	$msg.="<tr><td><b> Shop  Name </b></td><td>$glh_cust_name</td></tr>";
	$msg.="<tr><td><b> Door No   </b></td><td>$glh_door_no</td></tr>";
	$msg.="<tr><td><b> Block/Society Name</b></td><td>$glh_block_name</td></tr>";
	$msg.="<tr><td><b> Street No         </b></td><td>$glh_addr1</td></tr>";
	$msg.="<tr><td><b> Street Name       </b></td><td>$glh_addr2</td></tr>";
	$msg.="<tr><td><b> Area Name         </b></td><td>$glh_area</td></tr>";
	$msg.="<tr><td><b> Location          </b></td><td>$glh_addr2</td></tr>";
	$msg.="<tr><td><b> City              </b></td><td>$glh_city</td></tr>";
	$msg.="<tr><td><b> State             </b></td><td>$glh_state</td></tr>";
	$msg.="<tr><td><b> Pincode           </b></td><td>$glh_pincode</td></tr>";
	$vertical_name = get_single_value_from_single_table("GTM_VERTICAL_NAME", "gft_vertical_master", "GTM_VERTICAL_CODE", $glh_vertical);
	$msg.="<tr><td><b> Vertical </b></td><td>$vertical_name</td></tr>";
	$lead_status_name = get_single_value_from_single_table("GCS_NAME", "gft_customer_status_master", "GCS_CODE", $glh_status);
	$msg.="<tr><td><b> Lead Status </b></td><td>$lead_status_name</td></tr>";
	$msg.="<tr><td><b> Prospect Status </b></td><td>$prospect_subtype</td></tr>";
	$msg.="<tr><td><b> Lead Type </b></td><td>$lead_type</td></tr>";
	$msg.="<tr><td><b> Lead Subtype </b></td><td>$lead_subtype</td></tr>";
	$msg.="</table>";
	$msg .= $contact_ui;
	
	$data_query =" select c1.GCS_NAME old_stat, c2.GCS_NAME new_stat, e1.GEM_EMP_NAME old_name, e2.GEM_EMP_NAME new_name ".
			" from gft_lead_hdr ".
			" left join gft_emp_master e1 on (e1.GEM_EMP_ID=$old_lfd) ".
			" left join gft_emp_master e2 on (e2.GEM_EMP_ID=$glh_lfd) ".
			" left join gft_customer_status_master c1 on (c1.GCS_CODE=$old_status) ".
			" left join gft_customer_status_master c2 on (c2.GCS_CODE=$glh_status) ".
			" where GLH_LEAD_CODE='$lead_code'";
	$data_res = execute_my_query($data_query);
	if($data_dtl = mysqli_fetch_array($data_res)){
		$old_stat_name = $data_dtl['old_stat'];
		$new_stat_name = $data_dtl['new_stat'];
		$old_emp_name = $data_dtl['old_name'];
		$new_emp_name = $data_dtl['new_name'];
		$msg .="<table border='1'>".
				"<tr><th>Field</th><th>Old Value</th><th>New Value</th><th>Action Taken</th></tr>".
				"<tr><td>Lead Status</td><td>$old_stat_name</td><td>$new_stat_name</td><td>$status_reason</td></tr>".
				"<tr><td>Lead Owner</td><td>$old_emp_name</td><td>$new_emp_name</td><td>$owner_reason</td></tr>".
				"</table>";
	}
	
	$activity_dtl['GLD_LEAD_CODE']	=	$lead_code;
	$activity_dtl['GLD_DATE']		=	date('Y-m-d H:i:s');
	$activity_dtl['GLD_VISIT_DATE']	=	date('Y-m-d');
	$activity_dtl['GLD_EMP_ID']		=	$activity_emp;
	$activity_dtl['GLD_NOTE_ON_ACTIVITY']=$msg;
	$activity_dtl['GLD_VISIT_NATURE']='70';
	$activity_dtl['GLD_VISITED_TYPE']=($activity_sumbit_type==''?"0":"$activity_sumbit_type");
	$next_action = '73';
	$action_date = date('Y-m-d');
	if($lmt_incharge!=0){
		if(!is_next_action_exits($lead_code,$lmt_incharge,$next_action,$action_date)){
			$activity_dtl['GLD_NEXT_ACTION_DATE']=	$action_date;
			$activity_dtl['GLD_NEXT_ACTION']	 =	$next_action;
			$activity_dtl['GLD_SCHEDULE_STATUS'] =	1;
			$activity_dtl['GLD_EMP_ID']			 =	$lmt_incharge;
		}
	}
	insert_in_gft_activity_table($activity_dtl,null,true);
}

/**
 * @param string $lead_code
 * @param string $by_employee
 *
 * @return int
 */
function get_last_activity_date_diff($lead_code, $by_employee) {
	$vt_days = 0;
	$query = "select min(datediff(now(),GLD_VISIT_DATE)) as vt_days from gft_activity where GLD_LEAD_CODE='$lead_code' and GLD_EMP_ID='$by_employee' ";
	$res = execute_my_query($query);
	if($row_data = mysqli_fetch_array($res)){
		$vt_days = (int)$row_data['vt_days'];
	}
	return $vt_days;
}

/**
 * @param int $act_id
 * @param string $sms_status
 * 
 * @return void
 */
function update_sms_status_inactivity($act_id, $sms_status){
	if($act_id!=0 and $act_id!=''){
		execute_my_query("UPDATE gft_activity SET GLD_SMS_DELIVERY_STATUS='$sms_status' WHERE GLD_ACTIVITY_ID=$act_id");
	}
}

/**
 * @return boolean
 */
function is_working_and_business_hrs(){
	$sec_time = time_to_seconds(date('H:i'));
	$support_start_time = time_to_seconds(get_samee_const('27X7_DAY_START'));
	$support_end_time = time_to_seconds(get_samee_const('27X7_DAY_END'));
	$shift_timing = (($sec_time>=$support_start_time) and ($sec_time < $support_end_time));
	$today = getdate();
	if(!is_holiday(date('Y-m-d'),false) and $shift_timing and ($today['wday']!=0 or date('m-d')=='04-01')) {
		return true;
	}
	return false;
}
/**
 *
 * @param string $user_id
 * @param string $role_id
 *
 * @return mixed[]
 */
function get_emp_for_reporting_mgr($user_id,$role_id='0') {
	$role_cond = "";
	if($role_id!='0') {
		$role_cond = " and gem_role_id='$role_id' ";
	}
	$pc_qry = " select ger_emp_id,gem_emp_name,gem_email from gft_emp_reporting ".
			" join gft_emp_master on (ger_emp_id=gem_emp_id) ".
			" where ger_reporting_empid='$user_id' and ger_status='A' $role_cond";
	$pc_res = execute_my_query($pc_qry);
	$pc_list = array();
	$i = 0;
	while($row = mysqli_fetch_array($pc_res)) {
		$pc_list[$i][0] = $row['ger_emp_id'];
		$pc_list[$i][1] = $row['gem_emp_name'];
		$pc_list[$i][2] = $row['gem_email'];
		$i++;
	}
	return $pc_list;
}
/**
 * @param int $user_id
 * @param boolean $chk_partner
 * @param string $partner_alaise
 * @param string $name_code_cond
 * 
 * @return string
 */
function get_territory_query_for_sales_person($user_id,$chk_partner=false,$partner_alaise="",$name_code_cond = ""){
	$query_terr='';
	$result_terrcheck=execute_my_query("select get_territory_id from gft_emp_territory_dtl where get_emp_id= '$user_id' and GET_STATUS='A' and get_work_area_type >2");
	if(mysqli_num_rows($result_terrcheck)>0){
		$query_concat_terri_id=" select group_concat(DISTINCT terr_id) area_terr_id FROM  gft_emp_territory_dtl,b_map_view b" .
				" where GET_STATUS='A' and ((get_work_area_type=5 and b.zone_id=get_territory_id) " .
				" or (get_work_area_type=4 and  b.region_id=get_territory_id) " .
				" or (get_work_area_type=3 and  b.area_id=get_territory_id ) " .
				" or (get_work_area_type=2 and  b.terr_id=get_territory_id ))" .
				" and get_emp_id='$user_id' and terr_id!=100 ";
	}else{
		$query_concat_terri_id=" select group_concat(DISTINCT get_territory_id) area_terr_id FROM  gft_emp_territory_dtl join gft_business_territory_master bt1 on (GET_STATUS='A' and bt1.gbt_territory_id=get_territory_id and get_work_area_type=2) " .
				" join b_map_view b on  (b.area_id =bt1.gbt_map_id )" .
				" where get_emp_id='$user_id' and terr_id!=100 ";
	}
	$result_terri=execute_my_query($query_concat_terri_id);
	$territory_id_str='';
	if($data_terri=mysqli_fetch_array($result_terri)){
		$territory_id_str=$data_terri['area_terr_id'];
	}
	/* if($territory_id_str!=''){
		$territory_id_str.=",100";
	}else{
		$territory_id_str="100";
	} */
	if($territory_id_str==''){
		$query_terr=" and ( GLH_LFD_EMP_ID = $user_id ";
	}else{
		$query_terr=" and ( GLH_TERRITORY_ID in ($territory_id_str) or GLH_LFD_EMP_ID = $user_id ";
	}
	$union_query = "";
	if($chk_partner) {
		//$alias = ($partner_alaise!=="")?$partner_alaise.".":"";
		//$query_terr .= " or ".$alias."cgi_incharge_emp_id='$user_id' ) ";
		$created_by_select = "select GLH_LEAD_CODE from gft_lead_hdr join gft_cp_info cp1 on (glh_created_by_empid=cp1.cgi_emp_id) where cp1.cgi_incharge_emp_id='$user_id' ";
		$lfd_by_select	   = "select GLH_LEAD_CODE from gft_lead_hdr join gft_cp_info cp2 on ( GLH_LFD_EMP_ID=cp2.cgi_emp_id) where cp2.cgi_incharge_emp_id='$user_id' ";
		$union_query .= " select GLH_LEAD_CODE from ($created_by_select union $lfd_by_select ";
	}
	$pc_for_rm_arr = get_emp_for_reporting_mgr("$user_id",'2');
	$pc_for_rm = array();
	for($i=0;$i<count($pc_for_rm_arr);$i++) {
		$pc_for_rm[] = $pc_for_rm_arr[$i][0];
	}
	$pc_list_for_rm = implode(",",$pc_for_rm);
	$pc_rm_qry = "";
	if($pc_list_for_rm!=""){
		$pc_rm_qry	   = " select GLH_LEAD_CODE from gft_lead_hdr  where GLH_LFD_EMP_ID in ($pc_list_for_rm) ";
		if($union_query!='') {
			$union_query .= " union $pc_rm_qry ";
		} else {
			$union_query .= " select GLH_LEAD_CODE from ( $pc_rm_qry ";
		}
	}
	$created_emp_qry = "";
	$reporting_emps = get_emp_for_reporting_mgr("$user_id");
	$emps_arr = array();
	for($i=0;$i<count($reporting_emps);$i++) {
		$emps_arr[] = $reporting_emps[$i][0];
	}
	$emp_list = implode(",",$emps_arr);
	if(is_authorized_group_list("$user_id", null,array(6))) {
		$rsm_terr = get_terr_id("$user_id");
		$region_emps = get_employee_in(null,null,null,$rsm_terr,null,null,null);
		if($region_emps!='') {
			$emp_list .= (($emp_list!="")?",":"");
			$emp_list .= $region_emps;
		}
	}
	if($emp_list != "") {
		$created_emp_qry = " select GLH_LEAD_CODE from gft_lead_hdr where (GLH_CREATED_BY_EMPID in ($emp_list) OR glh_lfd_emp_id in ($emp_list)) and (glh_cust_pincode is null or glh_cust_pincode='' or GLH_TERRITORY_ID='100') ";
		if($union_query!='') {
			$union_query .= " union $created_emp_qry ";
		} else {
			$union_query .= " select GLH_LEAD_CODE from ( $created_emp_qry ";
		}
	}
	if($union_query!='') {
		$union_query .= ") t";
		$query_terr .= " or GLH_LEAD_CODE in ($union_query) ";
	}
	$query_terr .= " $name_code_cond )";
	return $query_terr;
}
/**
 * @param string $purpose_name
 * 
 * @return void
 */
function update_timestamp_master($purpose_name){
	$timestamp	= date('YmdHis');
	execute_my_query("update gft_timestamp_master SET GTM_PURPOSE_TIMESTAMP='$timestamp' where GTM_PURPOSE_NAME='$purpose_name'");
}

/**
 * @param string $emp_id
 * 
 * @return void
 */
function reset_mygofrugal_app_authkey_token($emp_id){
	$update_ids = $emp_id;
	$partner_chk = execute_my_query("select GLEM_EMP_ID from gft_cp_info join gft_leadcode_emp_map on (CGI_LEAD_CODE=GLEM_LEADCODE) where CGI_EMP_ID='$emp_id'");
	if(mysqli_num_rows($partner_chk) > 0){
		$emp_arr = /*. (string[int]) .*/array();
		while ($row1 = mysqli_fetch_array($partner_chk)){
			$emp_arr[] = $row1['GLEM_EMP_ID'];
		}
		$update_ids = implode(',',$emp_arr);
	}
	if($update_ids!=''){
		execute_my_query("update gft_customer_login_master set GCL_AUTH_KEY=null,GCL_AUTH_TOKEN=null where GCL_EMP_ID in ($update_ids) ");
		execute_my_query("UPDATE gft_emp_master SET GEM_STATUS='I' where GEM_EMP_ID IN($update_ids)");
	}
}

/**
 * @param int $act_id
 * @param int $support_id
 * 
 * @return void
 */
function insert_activity_support_in_hdr($act_id,$support_id){
	if($act_id!=0 and $support_id!=0){
		execute_my_query("insert into gft_activity_support_hdr(GAS_ACTIVITY_ID,GAS_SUPPORT_ID) values($act_id,$support_id)");
	}
}
/**
 * @param string $order_no
 * @param string $lead_code
 *
 * @return string
 */
function get_coupon_expiry_information($order_no,$lead_code=''){
	//send only age started
	//do not sent after expired.
	$return_str	=	"";
	$curr_date	=	date("Y-m-d");
	$sql_coupon_dtl	=	" select count(GCD_COUPON_NO) as no_of_coupon, group_concat(GCD_COUPON_NO) as coupon_list, GCD_GIVEN_DATE,GCD_EXPIRY_DATE, GEM_EMP_NAME  from gft_coupon_distribution_dtl ".
						" inner join gft_emp_master em on(em.gem_emp_id=GCD_HANDLED_BY) ".
						" where substr(GCD_ORDER_NO,1,15) ='$order_no' and GCD_TO_ID=$lead_code and (GCD_EXPIRY_DATE!='0000-00-00' and !isnull(GCD_EXPIRY_DATE)) and GCD_EXPIRY_DATE>='$curr_date'  group by GCD_ORDER_NO";
	$res_coupon_dtl	=	execute_my_query($sql_coupon_dtl);
	if(mysqli_num_rows($res_coupon_dtl)==1){
		$no_of_coupon	=	mysqli_result($res_coupon_dtl, 0, 'no_of_coupon');
		$coupon_list	=	mysqli_result($res_coupon_dtl, 0, 'coupon_list');
		$given_date		=	mysqli_result($res_coupon_dtl, 0, 'GCD_GIVEN_DATE');
		$expity_date	=	mysqli_result($res_coupon_dtl, 0, 'GCD_EXPIRY_DATE');
		$given_by		=	mysqli_result($res_coupon_dtl, 0, 'GEM_EMP_NAME');
		$return_str=<<<END
		<table class="tg" border='1' width='50%'>
		  <tr>
		    <th style="font-weight:bold;background-color:#6cb8f4" colspan="2" align='left'>Coupon Information<br></th>
		  </tr>
		  <tr>
		    <td style='font-size:small'>No of coupon<br></td>
		    <td style='font-size:small'>$no_of_coupon</td>
		  </tr>
		  <tr>
		    <td style='font-size:small'>Coupons</td>
		    <td style='font-size:small'>$coupon_list</td>
		  </tr>
		  <tr>
		    <td style='font-size:small'">Given Date<br></td>
		    <td style='font-size:small'">$given_date</td>
		  </tr>
		  <tr>
		    <td style='font-size:small'>Expiry Date<br></td>
		    <td style='font-size:small'>$expity_date</td>
		  </tr>
		  <tr>
		    <td style='font-size:small'>Given By<br></td>
		    <td style='font-size:small'>$given_by</td>
		  </tr>
		</table><br>
END;
	}
	return $return_str;
}

/**
 * @param string $otp
 * @param string $lead_code
 * @param int $pcode
 * 
 * @return void
 */
function send_otp_mail_sms($lead_code,$otp,$pcode=515){
	$installation_url = get_single_value_from_single_table("GPG_INSTALLATION_GUIDE_URL", "gft_product_group_master", "gpg_product_family_code", "515");
	$sel_quer = " select GPG_INSTALLATION_GUIDE_URL, GPV_DOWNLOAD_HLINK from gft_product_version_master ".
				" join gft_product_group_master on (gpg_product_family_code=GPV_PRODUCT_CODE and gpv_version_family=gpg_skew) ".
				" where GPV_PRODUCT_CODE=$pcode and gpv_current_version=1 limit 1 ";
	$sel_res = execute_my_query($sel_quer);
	if($row_data = mysqli_fetch_array($sel_res)){
		$installation_url = $row_data['GPG_INSTALLATION_GUIDE_URL'];
		$download_url = $row_data['GPV_DOWNLOAD_HLINK'];
		$db_mail_content_config=/*. (string[string][int]) .*/array(
				"OTP"=>array($otp),
				"Customer_Id"=>array($lead_code),
				"Customer_ID"=>array($lead_code),
				"Download_link"=>array($download_url),
				"Installation_Guide_Link"=>array($installation_url)
		);
		send_formatted_mail_content($db_mail_content_config,85,155,null,array($lead_code));
		if($pcode==515){
			send_formatted_mail_content($db_mail_content_config,9,219,null,array($lead_code));
		}
		$mobile_arr = get_contact_dtl_for_designation($lead_code, 1, '');
		if(count($mobile_arr) > 0){
			$sms_content = htmlentities(get_formatted_content($db_mail_content_config, 143));
			entry_sending_sms_to_customer($mobile_arr[0], $sms_content, 143, $lead_code,0,null,0,null,true);
		}
	}
}
/**
 * @param string $lead_code
 * @param int $emotion
 * @param string $last_complaint_id
 * 
 * @return void
 */
function update_lead_header_extension($lead_code, $emotion, $last_complaint_id=''){
	$hdr_upd_que = $hdr_ins_que = "";
	if($last_complaint_id!=''){
		$sel_quer = " select count(GCH_COMPLAINT_ID) as pend_supp, datediff(now(),min(GCH_COMPLAINT_DATE)) as support_age ".
					" from gft_customer_support_hdr where GCH_LEAD_CODE='$lead_code' and gch_current_status not in ('T1','T14','T19','T41','T43') ";
		$res_quer = execute_my_query($sel_quer);
		if($row = mysqli_fetch_array($res_quer)){
			$pend_supp = $row['pend_supp'];
			$support_age = $row['support_age'];
		}
		$hdr_upd_que = "update  gft_lead_hdr_ext set GLE_PENDING_SUPPORT='$pend_supp', GLE_SUPPORT_AGE='$support_age',GLE_LAST_COMPLAINT_ID='$last_complaint_id' where GLE_LEAD_CODE='$lead_code' ";
	}
	if(exists_in_lead_hdr_ext($lead_code)){
		execute_my_query("update  gft_lead_hdr_ext set GLE_CUST_EMOTION='$emotion' where GLE_LEAD_CODE='$lead_code'");
	}else{
		execute_my_query("INSERT INTO gft_lead_hdr_ext(GLE_LEAD_CODE,GLE_CUST_EMOTION) VALUES('$lead_code','$emotion')");
	}
	if($hdr_upd_que!=""){
		execute_my_query($hdr_upd_que);
	}	
}

/**
 * @param string $order_no
 * @param string $emp_status
 *
 * @return string
 */
function check_for_employee_mbile_and_id($order_no,$emp_status='A'){
	if(strlen($order_no)!=15){
		return '';
	}
	$mobile_val = substr($order_no,0,10);
	$contact_cond	 = contact_info_where_condition("GEM_MOBILE", $mobile_val);
	$emp_id_val = (int)substr($order_no, 10,5);
	$query = " select GEM_EMP_ID from gft_emp_master where 1 and ($contact_cond) and GEM_EMP_ID=$emp_id_val ";
	if($emp_status!=''){
		$query .= " and GEM_STATUS='$emp_status' ";	
	}
	$que_res = execute_my_query($query);
	if($que_data = mysqli_fetch_array($que_res)){
		return $que_data['GEM_EMP_ID'];
	}
	return '';
}

/**
 * @param string $lead_code
 * 
 * @return boolean
 */
function exists_in_lead_hdr_ext($lead_code){
	$num_row = mysqli_num_rows(execute_my_query("select GLE_LEAD_CODE from gft_lead_hdr_ext where GLE_LEAD_CODE='$lead_code'"));
	if($num_row==1){
		return true;	
	}
	return false;
}
/**
 * @param int $lead_code
 * @param string $esc_msg
 * @param string $pd_status
 *
 * @return void
 */
function update_pd_escalation_in_lead_hrd($lead_code, $esc_msg='',$pd_status=''){
	if(exists_in_lead_hdr_ext($lead_code)){
		execute_my_query("update gft_lead_hdr_ext set GLE_PD_ESCALATION='$esc_msg',GLE_PD_STATUS='$pd_status' where GLE_LEAD_CODE='$lead_code'");
	}else{
		execute_my_query("insert into gft_lead_hdr_ext (GLE_LEAD_CODE,GLE_PD_ESCALATION,GLE_PD_STATUS) values ('$lead_code','$esc_msg','$pd_status')");
	}
}
/**
 * @param int $lead_code
 * 
 * @return void
 */
function update_coupon_status_in_lead_hdr($lead_code){
	$res_coupon_dtl	=	execute_my_query("select  sum(if(GCD_SIGNED_OFF='N',1,0)) as not_signed, sum(if(GCD_SIGNED_OFF='Y',1,0)) as signedoff from gft_coupon_distribution_dtl where gcd_to_id='$lead_code' and  GCD_SIGNED_OFF in('Y','N') group by GCD_SIGNED_OFF");
	while($coupon_row=mysqli_fetch_array($res_coupon_dtl)){
		$not_signed	=	$coupon_row['not_signed'];
		$signedoff	=	$coupon_row['signedoff'];
		if(exists_in_lead_hdr_ext($lead_code)){
			execute_my_query("update gft_lead_hdr_ext set GLE_PENDING_COUPON='$not_signed', GLE_COLLECTED_COUPON='$signedoff' where GLE_LEAD_CODE='$lead_code'");
		}else{
			execute_my_query("insert into gft_lead_hdr_ext (GLE_LEAD_CODE,GLE_PENDING_COUPON,GLE_COLLECTED_COUPON) values ('$lead_code','$not_signed','$signedoff')");
		}
	}
}

/**
 * @param int $lead_code
 * @param int $pcode
 *
 * @return boolean
 */
function check_customer_interested_product($lead_code, $pcode){
	$result    =    execute_my_query("select GLC_LEAD_CODE from gft_lead_product_dtl where GLC_LEAD_CODE='$lead_code'  and GLC_PRODUCT_CODE=$pcode and GLC_INTEREST_LEVEL='Y'");
	if(mysqli_num_rows($result)>0){
		return true;
	}
	return false;
}

/**
 * @param string $employee_id
 * @param int $metric_id
 * @param int $completed_value
 * @param string $today
 * @param string $ref_lead_code
 * 
 * @return void
 */
function update_daily_achieved($employee_id, $metric_id, $completed_value, $today='',$ref_lead_code=''){
	$today = ($today=='')?date('Y-m-d'):$today;
	$select_que =" select GDA_ID,GDA_ACHIEVED from gft_daily_achieved_metrics ".
				 " where GDA_EMP_ID='$employee_id' and GDA_DATE='$today' and GDA_METRIC_ID='$metric_id' ";
	$res_que = execute_my_query($select_que);
	if($row_data = mysqli_fetch_array($res_que)){
		$achieved 	= (int)$row_data['GDA_ACHIEVED'] + $completed_value;
		$gda_id 	= (int)$row_data['GDA_ID'];
		$upd_que 	= " update gft_daily_achieved_metrics set GDA_ACHIEVED='$achieved' where GDA_ID='$gda_id' ";
		$res = execute_my_query($upd_que);
	}else{
		$ins_que = " insert into gft_daily_achieved_metrics (GDA_EMP_ID,GDA_DATE,GDA_METRIC_ID,GDA_ACHIEVED) values ".
					" ('$employee_id','$today','$metric_id','$completed_value') ";
		$res = execute_my_query($ins_que);
		$gda_id = mysqli_insert_id_wrapper();
	}
	if( (in_array($metric_id,array(83,84))) && ($ref_lead_code!='') ){
		if($res){
			$ref_query = "insert into gft_daily_metric_log (GDM_REF_ID,GDM_LEAD_CODE,GDM_DATE) values ('$gda_id','$ref_lead_code',now()) ";
			execute_my_query($ref_query);
		}
	}	
}

/**
 * @param string $ORDER_NO
 * @return void
 */
function update_invoice_for_order($ORDER_NO) {
	$chk_qry = execute_my_query(" select distinct gip_invoice_id inv_id from gft_invoice_product_dtl where gip_order_no='$ORDER_NO'");
	$invoice_ids = array();
	while($row = mysqli_fetch_array($chk_qry)) {
		$invoice_ids[] = $row['inv_id'];
	}
	if(count($invoice_ids)>0) {
		execute_my_query(" update gft_invoice_hdr set gih_status='C' where gih_invoice_id in ('".implode("','",$invoice_ids)."')");
	}
}
/**
 * @param string $num
 *
 * @return string
 */
function number_to_abbreviated_form($num){
	if((int)$num > 99999){
		$num = round($num/100000, 1)."L";
	}elseif ((int)$num > 999){
		$num = round($num/1000, 1)."K";
	}
	return $num;
}

/**
 * @param string $orderid
 * 
 * @return void
 */
function update_internal_coupon_daily_achieved($orderid){
	$sql_quer = " select GCD_RECEIVED_BY,gccd_coupon_purpose from gft_complementary_coupon_order_dtl ".
			" join gft_coupon_distribution_dtl on (GCD_ORDER_NO=GCCD_ID) where GCCD_ID='$orderid' ";
	$sql_resl = execute_my_query($sql_quer);
	if($sql_data = mysqli_fetch_array($sql_resl)){
		$metric_id = '';
		$coupon_purp = $sql_data['gccd_coupon_purpose'];
		if($coupon_purp=='1'){
			$metric_id = '95';
		}else if($coupon_purp=='2'){
			$metric_id = '94';
		}
		if($metric_id!=''){
			update_daily_achieved($sql_data['GCD_RECEIVED_BY'], $metric_id, 1);
		}
	}
}

/**
 * @param string $gcc_column_name
 * @param string $phone_no
 *
 * @return string
 */

function contact_info_where_condition($gcc_column_name, $phone_no){
	$sph_no=substr($phone_no,-10);
	$where_condition=" ($gcc_column_name ='$sph_no' or $gcc_column_name ='0$sph_no' or $gcc_column_name='91$sph_no' or $gcc_column_name='00$sph_no' or $gcc_column_name='$phone_no' or $gcc_column_name='0$phone_no' or $gcc_column_name='00$phone_no' )";
	return $where_condition;
}
/**
 * @param string $start_dt
 * @param string $end_dt
 * @param int $allow_days
 *
 * @return boolean
 */
function allow_form_and_to_date_diff($start_dt,$end_dt,$allow_days){
	if($start_dt==''){
		return false;
	}else{
		$last_date = $end_dt;
		if($last_date==''){
			$last_date = date('Y-m-d');
		}
		$diff = datediff($start_dt, $last_date);
		if($diff > $allow_days){
			return false;
		}
	}
	return true;
}

/**
 * @param string $rated_emp
 * @param string $rated_date
 * @param string $baton_pass_quality
 * @param string $support_ref_id
 * @param int $reminder_type
 * 
 * @return void
 */
function update_data_quality($rated_emp,$rated_date,$baton_pass_quality,$support_ref_id,$reminder_type){
	$rated_date = $rated_date==''?date('Y-m-d H:i:s'):$rated_date;
	$update_data="update gft_data_quality set GDQ_RATED_EMP='$rated_emp', GDQ_RATED_DATE='$rated_date', " .
	" GDQ_RATING_ID='$baton_pass_quality' where GDQ_REF_ID='$support_ref_id' and GDQ_REMINDER_TYPE='$reminder_type' ";
	execute_my_query($update_data);
}

/**
 * @param int $reminder_type
 * @param string $created_emp
 * @param string $baton_wobbling
 * @param string $created_date
 * @param string $lead_code
 * @param string $ref_id
 * 
 * @return void
 */
function insert_for_data_quality($reminder_type,$created_emp,$baton_wobbling,$created_date,$lead_code,$ref_id){
	$insert_arr['GDQ_REMINDER_TYPE']	= $reminder_type;
	$insert_arr['GDQ_CREATED_EMP'] 		= $created_emp;
	$insert_arr['GDQ_BATON_WOBBLING']	= $baton_wobbling;
	$insert_arr['GDQ_CREATED_DATE']		= ($created_date=='')?date('Y-m-d H:i:s'):$created_date;
	$insert_arr['GDQ_LEAD_CODE']		= $lead_code;
	$insert_arr['GDQ_REF_ID'] 			= $ref_id;
	array_update_tables_common($insert_arr, "gft_data_quality", null, null, SALES_DUMMY_ID,null,null, $insert_arr);
}
/**
 * @param string $lead_code
 * 
 * @return boolean
 */
function get_customer_installation_status($lead_code){
	$res	=	execute_my_query(" select count(GID_INSTALL_ID) tot_count from gft_install_dtl_new ". 
								 " inner join gft_product_master on (GPM_PRODUCT_SKEW=GID_LIC_PSKEW and GID_LIC_PCODE=GPM_PRODUCT_CODE) ".
								 " where GPM_FREE_EDITION='N' and GID_LEAD_CODE='$lead_code' and GID_STATUS='A'");
	$row	=	mysqli_fetch_array($res);
	if((int)$row['tot_count']>0){
		return true;
	}else{
		return false;
	}
}
/**
 * @param string $GQH_LEAD_CODE
 *
 * @return void
 */
function update_quotation_dtl_in_lead_hdr($GQH_LEAD_CODE){
	$table_name='gft_lead_hdr';
	$lead_hdr_key=/*. (mixed[string]) .*/ array();
	$lead_hdr_key['GLH_LEAD_CODE']=$GQH_LEAD_CODE;
	$update_lead_hdr=/*. (mixed[string]) .*/ array();
	$update_lead_hdr['GLH_NO_OF_QUOTATIONS']=0;
	$update_lead_hdr['GLH_LAST_QUOTATION_GIVEN_ON']='0000-00-00';
	$query_num_quotation_generated="select count(*) cnt,max(GQH_ORDER_DATE) as GQH_ORDER_DATE  from gft_quotation_hdr where GQH_LEAD_CODE=$GQH_LEAD_CODE ";
	$result_num_quotation_generated=execute_my_query($query_num_quotation_generated);
	if($result_num_quotation_generated){
		$qd_num=mysqli_fetch_array($result_num_quotation_generated);
		$update_lead_hdr['GLH_NO_OF_QUOTATIONS']=$qd_num['cnt'];
		$update_lead_hdr['GLH_LAST_QUOTATION_GIVEN_ON']=$qd_num['GQH_ORDER_DATE'];
	}
	global $uid;
	array_update_tables_common($update_lead_hdr,$table_name,$lead_hdr_key,null, $uid,$remarks='Quotation Generated/ Cancelled ',
	$table_column_iff_update=null,$insert_new_row=null);

	$sel_quer = " select GQH_ORDER_NO,  group_concat(GQP_QTY,' ',GPM_SKEW_DESC) as prod_desc ".
			" from (select GQH_ORDER_NO as last_quote_no from gft_quotation_hdr where GQH_LEAD_CODE='$GQH_LEAD_CODE' order by GQH_ORDER_DATE desc,GQH_ORDER_NO desc limit 1) t1 ".
			" join gft_quotation_hdr on (GQH_ORDER_NO = t1.last_quote_no) ".
			" join gft_quotation_product_dtl on (GQP_ORDER_NO=GQH_ORDER_NO) ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GQP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GQP_PRODUCT_SKEW) ".
			" where GQH_LEAD_CODE='$GQH_LEAD_CODE' group by GQH_ORDER_NO ";
	$res_quer = execute_my_query($sel_quer);
	if($row_data=mysqli_fetch_array($res_quer)){
		$last_quote_no 	= $row_data['GQH_ORDER_NO'];
		$prod_desc		= $row_data['prod_desc'];
		execute_my_query("update gft_lead_hdr_ext set GLE_LAST_QUOTE_NO='$last_quote_no',GLE_QUOTE_PRODUCT_DTL='$prod_desc' where GLE_LEAD_CODE='$GQH_LEAD_CODE'");
	}

}
/**
 * @param int $cp_emp_id
 * @param int $cp_lcode
 * @param string $user_account
 *
 * @return mixed[]
 */
function get_partner_advance_amount($cp_emp_id,$cp_lcode,$user_account=''){
	$balance_order_no	=	"000".substr("0000".$cp_emp_id,-4).substr("00000000".$cp_lcode,-8);
	if($user_account=='dealer'){
		$balance_order_no	=	"71".substr($balance_order_no,-13); //71 - dealer product code
	}
	$balance_details	=	array();
	$balance_details['balance']	=	"0";
	$balance_details['ledger']	=	"0";
	$query				=	" SELECT SUM(if((GRD_STATUS='D' or GRD_STATUS='O' or GRD_STATUS='W'),GCR_AMOUNT,0)) ledger, " .
			" SUM(if(GRD_CHECKED_WITH_LEDGER='Y',GCR_AMOUNT,0)) balance  " .
			" FROM gft_collection_receipt_dtl,gft_receipt_dtl WHERE grd_receipt_id=gcr_receipt_id AND ".
			" GRD_REFUND_AMT=0 and GCR_ORDER_NO='$balance_order_no' ";
	$result=execute_my_query($query);
	if($data=mysqli_fetch_array($result)){
		$balance_details['balance']	=	$data['balance'];
		$balance_details['ledger']	=	$data['ledger'];
	}
	return $balance_details;
}
/**
 * @param int $minutes
 * @param boolean $change_format
 * @param boolean $only_days
 *
 * @return string
 */
function get_age_in_days_hour_minutes($minutes,$change_format=false,$only_days=false){
	if($minutes==0){
		return '';
	}
	$sday = (int)($minutes/1440);
	$minutes = $minutes%1440;
	$shrs = (int)($minutes/60);
	$minutes = round($minutes%60);
	$age = '';
	if($sday > 0) {
		$age .= $sday.' Days, ';
		if($change_format){return "Updated $sday day".($sday>1?"s":"")." ago";}
	}
	if(!$only_days) {
		if($shrs > 0) {
			$age .= $shrs.' Hrs, ';
			if($change_format){return "Updated $shrs hr ago";}
		}
		if($change_format){return "Updated $minutes min ago";}
		$age .= $minutes.' Mins';
	}
	return $age;
}

/**
 * @param int $userId
 *
 * @return boolean
 */
function check_emp_status($userId){
	$sel_que="select GEM_STATUS from gft_emp_master where GEM_EMP_ID='$userId'";
	$res_que=execute_my_query($sel_que);
	if( (mysqli_num_rows($res_que)==1) && $data=mysqli_fetch_array($res_que) ){
		if($data['GEM_STATUS']=='A'){
			return true;
		}else{
			return false;
		}
	}
	return false;
}
/**
 * @param int $length
 * @param boolean $include_additional_character
 *
 * @return string
 */
function generate_keygen($length = 5, $include_additional_character=true){
	$alphabets = range('A','Z');
	$numbers = range('0','9');
	$additional_characters = array();
	if($include_additional_character){
		$additional_characters = array('_','.');
	}
	$final_array = /*. (string[int]) .*/array_merge($alphabets,$numbers,$additional_characters);
	$key_gen = '';
	while($length--) {
		$key = (int)array_rand($final_array);
		$key_gen .= $final_array[$key];
	}
	return $key_gen;
}
/**
 * 
 * @param string $emp_id
 * @param string $mb_no
 * @param boolean $send_sms
 * @param string $pwd
 * @param string $personal_mail_id
 * 
 * @return void
 */
function generate_mydelight_login($emp_id,$mb_no,$send_sms=true,$pwd='',$personal_mail_id=''){
    $auth_key    =    '';    
    $is_valid = 0;
    $authtoken = generate_tokengen();
    while($is_valid < 1){
        $auth_key    =    generate_keygen();
        $sql_check_duplicate=    execute_my_query(" select AUTH_KEY from gft_emp_auth_key where AUTH_KEY='$auth_key'");
        if(mysqli_num_rows($sql_check_duplicate)==0){
            $is_valid++;
        }
        
    }
    execute_my_query("insert into gft_emp_auth_key (EMP_ID,AUTH_KEY,AUTH_TOKEN,GEK_STATUS) values ('$emp_id','$auth_key','$authtoken','A') ");
    if($send_sms && $mb_no!=''){
        $emp_dtl = execute_my_query("select gem_emp_name,gem_email from gft_emp_master where gem_emp_id='$emp_id'");
        $emp_name = $email = '';
        if($row = mysqli_fetch_array($emp_dtl)) {
            $emp_name = $row['gem_emp_name'];
            $email = $row['gem_email'];
        }
        if((int)$emp_id<7000) {
            $sms_config = array();
            $sms_config['Emp_name'] = array($emp_name);
            $sms_config['Employee_Name'] = array($emp_name);
            $sms_config['Emp_email'] = array($email);
            $sms_config['Employee_Mail'] = array($email);
            $sms_config['password1'] = array('gftmail123');
            $sms_config['user_password'] = array('gftmail123');
            $sms_config['login_id'] = array(str_replace('@'.get_samee_const("OFFICEAL_MAIL_DOMAIN"),'',$email));
            $sms_config['password2'] = array($pwd);
            entry_sending_sms($mb_no, get_formatted_content($sms_config,205), 205, $emp_id);
            send_formatted_mail_content($sms_config, 25, 310, array($emp_id),null,($personal_mail_id!=''?array($personal_mail_id):null));
        } else {
            entry_sending_sms($mb_no, get_formatted_content(array(),175), 175, $emp_id);
        }
    }
}
/**
 * @param string $lead_code
 * @param string $uid
 * @param string $prospect_type
 * 
 * @return void
 */
function update_hot_prospect_status($lead_code,$uid,$prospect_type='1'){
	$lead_arr_ext['GLE_PROSPECT_TYPE']=$prospect_type;
	$lead_arr_ext['GLE_PROSPECT_TYPE_ON']=date("Y-m-d H:i:s");
	$lead_arr_ext['GLE_PROSPECT_TYPE_BY']=$uid;
	array_update_tables_common($lead_arr_ext,"gft_lead_hdr_ext",array("GLE_LEAD_CODE"=>$lead_code),null,$uid,null);
}
/**
 * @param string[int] $groups
 *
 * @return string
 */
function get_employee_list_using_group($groups){
	$emp_info	=	get_contact_dtls_of_group($groups);
	$emp_ids_arr=	array();
	$emp_ids	=	"";
	if(count($emp_info)!=0){
		for($i=0;$i<count($emp_info);$i++){
			$emp_ids_arr[]	=	$emp_info[$i][1];
		}
	}
	if(trim(implode(',', $emp_ids_arr))!=''){
		$emp_ids = implode(',', $emp_ids_arr);
	}
	return $emp_ids;
}
/**
 * @param string $date
 * 
 * @return string
 */
function get_date_in_indian_format($date){
	return date("M dS, Y h:i A",strtotime($date));
}
/**
 * @param string $date
 *
 * @return string
 */
function get_date_in_indian_format_without_time($date){
	return date("M dS, Y",strtotime($date));
}
/**
 * @param string $lead_code
 * @param string $incentive_id
 * 
 * @return string
 */
function get_incentive_employee_query($lead_code,$incentive_id='0'){
	$date_con	=	date('Y-m-d',strtotime("-90 days",strtotime(date('Y-m-d'))));
	$last_order_date_qry	=	"";
	$last_act_date_query	=	"";
	if($incentive_id!='0'){
		$rows	=	execute_my_query("select GOI_DATE_ON from gft_order_incentive_dtl where GOI_ID='$incentive_id'");
		$row	=	mysqli_fetch_array($rows);
		$last_order_date	=	$row['GOI_DATE_ON'];
		$last_order_date_qry=	" AND GOI_DATE_ON<'$last_order_date'";
		$last_act_date_query=	" AND GLD_VISIT_DATE<='$last_order_date'";
	}
	$result_last_incentive=execute_my_query("select GOI_DATE_ON from gft_order_incentive_dtl where GOI_LEAD_CODE='$lead_code' $last_order_date_qry order by GOI_DATE_ON desc limit 1");
	if(mysqli_num_rows($result_last_incentive)>0 && $row=mysqli_fetch_array($result_last_incentive)){
		$last_date=$row['GOI_DATE_ON'];
		$now = time();
		$last_date_incen_date = strtotime($last_date);
		$datediff = $now - $last_date_incen_date;
		$total_no_days= floor($datediff/(60*60*24));
		if($total_no_days<=90){
			$date_con	=	date('Y-m-d',strtotime($last_date));
		}
	}
	$sql_incentive_dtl	=	" select GLD_EMP_ID,GEM_EMP_NAME,GAM_ACTIVITY_ID,GAM_ACTIVITY_DESC,GAM_INCENTIVE_PERCENTAGE,GLD_ACTIVITY_ID from gft_activity ".
			" inner join gft_activity_master on(GLD_VISIT_NATURE=GAM_ACTIVITY_ID) ".
			" inner join gft_emp_master em on(gem_emp_id=GLD_EMP_ID AND GEM_ROLE_ID in(2,4,23,30) AND GEM_STATUS='A') ".
			" where gld_lead_code='$lead_code' and GLD_ACTIVITY_STATUS_ID=2 AND GLD_VISIT_DATE>'$date_con' $last_act_date_query  AND GAM_INCENTIVE_REQ='Y' GROUP BY GLD_EMP_ID,GAM_ACTIVITY_ID ".
			" union ".
			" select gem_emp_id as GLD_EMP_ID,GEM_EMP_NAME,GAM_ACTIVITY_ID,GAM_ACTIVITY_DESC,GAM_INCENTIVE_PERCENTAGE,GLD_ACTIVITY_ID from gft_activity ". 
			" inner join gft_activity_master on(GLD_VISIT_NATURE=GAM_ACTIVITY_ID)   ".
			" inner join gft_joint_visit_dtl jv on (jv.GJV_ACTIVITY_ID=gld_activity_id   and jv.GJV_EMP_ID=gld_emp_id and jv.GJV_VISIT_DATE=gld_visit_date) ".
			" inner join gft_emp_master em on(gem_emp_id=jv.GJV_JOINT_EMP_ID AND GEM_ROLE_ID in(2,4,23,30) AND GEM_STATUS='A')   ".
			" where gld_lead_code='$lead_code' and GLD_ACTIVITY_STATUS_ID=2 AND GLD_VISIT_DATE>'$date_con' $last_act_date_query   AND GAM_INCENTIVE_REQ='Y' GROUP BY GLD_EMP_ID,GAM_ACTIVITY_ID";
	return $sql_incentive_dtl;
}
/**
 *
 * @param string $lead_code
 *
 * @return string
 */
function get_incentive_employee_list($lead_code){
	$res_incentive_dtl	=	execute_my_query(get_incentive_employee_query($lead_code));
	$incentive_option=$select_default=	"";
	$total_incentive	=	mysqli_num_rows($res_incentive_dtl);
	while($row_inc=mysqli_fetch_array($res_incentive_dtl)){
		$incentive_option	=	$incentive_option."<option value='".$row_inc['GLD_EMP_ID']."-".$row_inc['GAM_ACTIVITY_ID']."-".$row_inc['GLD_ACTIVITY_ID']."' ".($total_incentive==1?" selected='selected'":"").">".$row_inc['GEM_EMP_NAME']." - ".$row_inc['GAM_ACTIVITY_DESC']."</option>";
	}
	if($incentive_option==""){
		$select_default="selected='selected'";
	}
	$incentive_option	=	"<option value='0-0-0' $select_default>Not Applicable</option>".$incentive_option;
	return $incentive_option;
}
/**
 * @return string
 */
function query_param_for_area_mapping_fiters(){
	global $zone_id,$region_id,$terr_id,$area_id,$country_id,$state_id,$district_id,$multiple_terr;
	$query_param = "&cmbZone=$zone_id&cmbRegion=$region_id&cmbArea=$area_id&cmbterr=$terr_id&cmbCountry=$country_id&cmbState=$state_id&cmbDist=$district_id";
	$multiple_terr_arr = explode(',', $multiple_terr);
	foreach ($multiple_terr_arr as $key){
		$query_param .= "&multiple_terr[]=$key";
	}
	return $query_param;
}

/**
 * @param string $duration
 *
 * @return string
 */
function get_duration_in_string($duration){
	$duration_arr = explode(":",$duration);
	$duration_str = " 0 Secs";
	if(count($duration_arr)==3){
		if( ($duration_arr[0]=='0') && ($duration_arr[1]=='0') && ($duration_arr[2]=='0') ){
			$duration_str = " 0 Secs";
		}else{
			$hrs = (int)$duration_arr[0];
			$mins = (int)$duration_arr[1];
			$secs = (int)$duration_arr[2];
			$duration_str = "";
			$duration_str .= ($hrs > 0) ? $hrs." Hrs ":"";
			$duration_str .= ($mins > 0) ? $mins." Mins ":"";
			$duration_str .= ($secs > 0) ? $secs." Secs ":"";
		}
	}
	return $duration_str;
}

/**
 * @param string $emply_id
 * @param string $report_date
 * @param string $open_time
 * @param string $close_time
 * @param boolean $check_for_joint
 *
 * @return string[string]
 */
function get_activity_history_for_daily_report($emply_id,$report_date,$open_time,$close_time,$check_for_joint){
    
    $ret_arr = /*. (string[string]) .*/array();
    $activity_txt = "";
    $activity_grouping_table = "";
    $joint_employees = /*. (string[int]) .*/array();
    
    if($report_date!=''){
        $date_condition = " and GLD_VISIT_DATE='$report_date' ";
        $prev_activity_condition = " and GLD_VISIT_DATE < '$report_date'";
    }else{
        $date_condition = " and GLD_DATE between '$open_time' and '$close_time' ";
        $prev_activity_condition = " and GLD_DATE < '$open_time'";
    }
    $sql1 = " select gce_id,gce_name,gce_order_by,gce_is_productive,gce_color_code ".
        " from gft_customer_engagement_master where gce_status='A' order by gce_order_by desc";
    $res1 = execute_my_query($sql1);
    
    $activity_grp_arr = array();
    while ($row1 = mysqli_fetch_array($res1)){
        $activity_grp_arr[$row1['gce_id']] = array(
            'id'           =>  $row1['gce_id'],
            'label'        =>  $row1['gce_name'],
            'isProductive' =>  $row1['gce_is_productive'],
            'color'        =>  $row1['gce_color_code']
        );
    }
    $lead_code_count_arr=/*. (int[string]) .*/array();
    $is_eligible_for_grouping = is_grouping_eligible($emply_id);
    $activity_que = " select GLH_LEAD_CODE,concat(GLH_CUST_NAME,' - ',ifnull(GLH_CUST_STREETADDR2,'')) as cust_name,GLD_VISIT_DATE, concat(em.gem_emp_name,'<br>',ifnull(group_concat(distinct(jem.gem_emp_name)),'')) as visited_by, ".
        " vn.GAM_ACTIVITY_DESC as activity_nature, gcn.GCM_NATURE as visit_nature,gcn.GCM_NATURE_ID as visit_nature_id, sec_to_time(GLD_VISIT_IN_MIN*60) as spent_time,sec_to_time(SUM(time_to_sec(GTC_DURATION))) as actual_time,GLD_NOTE_ON_ACTIVITY my_comments , ".
        " GLD_NEXT_ACTION_DATE as next_action_date, nvn.GAM_ACTIVITY_DESC as next_action, GLD_NEXT_ACTION_DETAIL,GJV_JOINT_EMP_ID,gld_emp_id,GLD_ACTIVITY_ID, gld_lead_status, GLD_VISIT_DATE, GLH_DATE, GLH_PROSPECT_ON, csm.GCS_NAME as lead_stat, GCS_STATUS_LEVEL, GPS_STATUS_NAME, GPS_STATUS_LEVEL, GLD_TRANS_ID ".
        " from gft_activity a ".
        " join gft_lead_hdr on (GLH_LEAD_CODE=a.GLD_LEAD_CODE) ".
        " left join gft_prospects_status_master on (GLD_PROSPECTS_STATUS=GPS_STATUS_ID) ".
        " join gft_customer_status_master csm on(GCS_CODE=gld_lead_status) ".
        " join gft_emp_master em on ( gld_emp_id=em.gem_emp_id ) ".
        " left join gft_cp_info on(GLH_LEAD_CODE=CGI_LEAD_CODE) ".
        " left join gft_complaint_nature_master gcn on( gcn.GCM_NATURE_ID=GLD_ACTIVITY_NATURE ) ".
        " left join gft_my_comments_master mc on mc.GMC_CODE=a.GLD_MY_COMMENTS_CODE ".
        " left join gft_joint_visit_dtl jv on ( jv.GJV_ACTIVITY_ID=a.gld_activity_id and jv.GJV_EMP_ID=a.gld_emp_id and jv.GJV_VISIT_DATE=a.gld_visit_date ) ".
        " left join gft_joint_activity jact on ( jact.GJA_ACTIVITY_ID=a.gld_activity_id and jact.GJA_EMP_ID=a.gld_emp_id and jact.GJA_LEAD_CODE=a.gld_lead_code and jact.GJA_VISIT_DATE=a.gld_visit_date ) ".
        " left join gft_activity_master jact_m on ( jact_m.gam_activity_id=jact.GJA_VISIT_NATURE ) ".
        " left join gft_emp_master jem on ( jem.gem_emp_id=jv.GJV_JOINT_EMP_ID ) ".
        " left join gft_activity_master vn on ( vn.GAM_ACTIVITY_ID=GLD_VISIT_NATURE ) ".
        " left join gft_techsupport_incomming_call on (GTC_ACTIVITY_ID=a.GLD_ACTIVITY_ID and GTC_LEAD_CODE=a.GLD_LEAD_CODE ) ".
        " left join gft_activity_master nvn on( nvn.GAM_ACTIVITY_ID=GLD_NEXT_ACTION ) ".
        " where (GLD_ACTIVITY_BY='$emply_id' or GJV_JOINT_EMP_ID='$emply_id') $date_condition and GLD_VISIT_NATURE not in (99) and GLH_LEAD_TYPE!=8 and if(GLH_LEAD_TYPE=2,CGI_LEAD_CODE is null,1) ".
        " group by a.gld_activity_id, a.gld_emp_id ORDER BY GLH_LEAD_CODE,GLD_VISIT_DATE,GLD_DATE ";
    $res_que = execute_my_query($activity_que);
    $url = CURRENT_SERVER_URL."/visit_details.php?lead_code=";
    $activity_lead_codes = array();
    $current_activity = /*. (string[string][string]) .*/array();
    $call_nature = array("2", "3", "14");
    $total_activites = 0;
    $col_span = ($is_eligible_for_grouping)? 12 : 11;
    $activity_txt  = 	"<tr><th>S.No</th>";
    $activity_txt .=    ($is_eligible_for_grouping)?"<th style='text-align: center;'> Engagement<br>Type</th>":"";
    $activity_txt .=	"<th>Customer Id</th><th>Customer Name</th><th>Previous Lead Status</th><th>Current Lead Status</th><th>Activity Nature</th>".
        "<th>Visit Nature</th><th>Time spent <br> Entered by user</th><th>Actual Time <br> From call history</th><th>My Comments</th><th>Next Action</th></tr>";
    if(mysqli_num_rows($res_que)>0){
        $total_activites = mysqli_num_rows($res_que);
        while ($temp_data = mysqli_fetch_array($res_que)){
            $this_lead_code = $temp_data['GLH_LEAD_CODE'];
            if(!isset($lead_code_count_arr[$this_lead_code])){
                $lead_code_count_arr[$this_lead_code]=0;
            }
            $lead_code_count_arr[$this_lead_code]++;
            $current_activity[$this_lead_code]['id'] = $temp_data['GLD_ACTIVITY_ID'];
            $current_activity[$this_lead_code]['status'] = $temp_data['gld_lead_status'];
            $current_activity[$this_lead_code]['date'] = $temp_data['GLD_VISIT_DATE'];
            $current_activity[$this_lead_code]['status_level'] = $temp_data['GCS_STATUS_LEVEL'];
            $current_activity[$this_lead_code]['prospect_status_level'] = $temp_data['GPS_STATUS_LEVEL'];
            $current_activity[$this_lead_code]['visit_nature'] = $temp_data['visit_nature_id'];
            $current_activity[$this_lead_code]['time_spent'] = time_to_seconds($temp_data['actual_time']);
            if(!in_array($temp_data['visit_nature_id'], $call_nature)){
                $current_activity[$this_lead_code]["other_than_call_nature"] = true;
            }else if(time_to_seconds($temp_data['actual_time']) >= 10){
                $current_activity[$this_lead_code]["atleast_one_call_productive"] = true;
            }
        }
        mysqli_data_seek($res_que,0);
        $prev_lead_code = "";
        $unique_lead_sno = 0;
        while ($row_data = mysqli_fetch_array($res_que)){
            $this_lead_code = $row_data['GLH_LEAD_CODE'];
            if($prev_lead_code!=$this_lead_code){
                $unique_lead_sno++;
                $prev_lead_code = $this_lead_code;
            }
            $rowcount=$lead_code_count_arr[$this_lead_code];
            $lead_code_count_arr[$this_lead_code]=-1; //Reset the previous value to skip next loop.
            $temp_url = $url.$row_data['GLH_LEAD_CODE'];
            $c_id=$row_data['GLH_LEAD_CODE'];
            $previous_activity = array();
            $aq = " select GLD_ACTIVITY_ID, gld_lead_status, GCS_STATUS_LEVEL, GCS_NAME, GPS_STATUS_NAME,GPS_STATUS_LEVEL from gft_activity ".
                " join gft_customer_status_master on (gld_lead_status=GCS_CODE) ".
                " left join gft_prospects_status_master on (GLD_PROSPECTS_STATUS=GPS_STATUS_ID) ".
                " where GLD_LEAD_CODE='$c_id' $prev_activity_condition order by GLD_VISIT_DATE desc, GLD_DATE desc limit 1 ";
            $aqr = execute_my_query($aq);
            $prev_lead_status = "NA";
            if($ar = mysqli_fetch_assoc($aqr)){
                $previous_activity = array(
                    'id'          => (int)$ar['GLD_ACTIVITY_ID'],
                    'status'      => $ar['gld_lead_status'],
                    'status_level'=> $ar['GCS_STATUS_LEVEL'],
                    'prospect_status_level' => $ar['GPS_STATUS_LEVEL']
                );
                $prev_lead_status = $ar['GCS_NAME'];
                if($ar['gld_lead_status'] == "3")
                    $prev_lead_status .= "<br>( " . $ar['GPS_STATUS_NAME']. " )";
            }
            $color_code='';
            if($is_eligible_for_grouping){
                $obj1 = new iActivityGrouping($row_data['GLH_DATE'],$row_data['GLH_PROSPECT_ON']);
                $activity_lead_codes[$c_id]['type'] = $obj1->classify($current_activity[$c_id], $previous_activity);
                $engagement_type = $activity_grp_arr[$activity_lead_codes[$c_id]['type']]['label'];
                $color_code = $activity_grp_arr[$activity_lead_codes[$c_id]['type']]['color'];
            }
            $activity_txt .= "<tr>";
            $p_lead_status="";
            if ($rowcount == 1){
                $activity_txt .="<td>$unique_lead_sno </td>";
                $p_lead_status = "<td>".$prev_lead_status. "</td>";
                if($is_eligible_for_grouping){
                    $activity_txt .="<td  style='padding-left:5px; background-color:". $color_code .";'>". $engagement_type ."</td>";
                }
            }else if ($rowcount != -1){
                $activity_txt .="<td rowspan=$rowcount>$unique_lead_sno <br><br>(Activities: $rowcount)</td>";
                $p_lead_status = "<td rowspan=$rowcount>".$prev_lead_status. "</td>";
                if($is_eligible_for_grouping){
                    $activity_txt .="<td rowspan=$rowcount style='padding-left: 5px; background-color:". $color_code .";'>". $engagement_type ."</td>";
                }
            }
            $nextActionInfo = "";
            if($row_data['next_action']){
                $nextActionInfo = "<b>Date :</b>".$row_data['next_action_date']." <br>".
                    "<b>Type: </b>".$row_data['next_action']."<br> <b>Detail: </b>".$row_data['GLD_NEXT_ACTION_DETAIL'];
            }
            $time_spent = in_array ($row_data['visit_nature_id'], array("2", "3")) ? get_duration_in_string($row_data['actual_time']) : ' NA ';
            
            $curr_lead_status = $row_data['lead_stat'];
            
            $audio_link = "";
            $trans_id  = $row_data['GLD_TRANS_ID'];
            $domain_name	= CURRENT_SERVER_URL."/";
            if( ($trans_id!='') && ($trans_id!='0') ){
                $temp_link = $domain_name."techsupport_call_refference.php?transId=$trans_id";
                $audio_link = "&nbsp;<a href='$temp_link' target='_blank'>[A]</a>";
            }
            
            if($row_data['gld_lead_status'] == "3")
                $curr_lead_status .= "<br>( " . $row_data['GPS_STATUS_NAME']. " )";
                $activity_txt .= "<td><a href='$temp_url' target='_blank'>".$row_data['GLH_LEAD_CODE']."</a> $audio_link </td><td>".$row_data['cust_name']."</td>".
                    $p_lead_status.
                    "<td>".$curr_lead_status.
                    "</td><td>".$row_data['activity_nature']."</td><td>".$row_data['visit_nature']."</td>".
                    "<td>".get_duration_in_string($row_data['spent_time'])."</td><td>".$time_spent."</td><td>".$row_data['my_comments']."</td><td>". $nextActionInfo ."</td></tr>";
                    if( $check_for_joint && ($row_data['GJV_JOINT_EMP_ID']!='') && ($row_data['GJV_JOINT_EMP_ID']!=$emply_id) ){
                        $joint_employees[] = $row_data['GJV_JOINT_EMP_ID'];
                    }
        }
    }else{
        $activity_txt .= "<tr><td colspan='$col_span' align='center'>No Activity History</td></tr>";
    }
    $activity_txt = "<b>Activity History:</b><br><table border=1 cellspacing=2 cellpading=2> <tr><td colspan='$col_span' style='padding: 5px;'>Total Unique Customers: ". count($lead_code_count_arr)." , Total Activities : $total_activites </td></tr>".$activity_txt;
    if($activity_txt!=''){$activity_txt	=	$activity_txt."</table>";}
    $out_arr = array();
    foreach ($activity_lead_codes as $c_id => $tarr){
        if(!isset($out_arr[$tarr['type']]))$out_arr[$tarr['type']]=0;
        $out_arr[$tarr['type']]++;
    }
    
    $act_total = 0;
    
    if($is_eligible_for_grouping && (count($out_arr)>0)){
        $activity_grouping_table ="<td style='width:50%'><br><table border=1><tr><th colspan=2 style='padding:5px;'>Unique Customer Engagements</th></tr><tr><th style='padding:5px;'>Engagement Type</th><th>Count</th></tr>";
        foreach ($activity_grp_arr as $activity_arr){
            $cnt = isset($out_arr[$activity_arr['id']])?$out_arr[$activity_arr['id']]:0;
            $activity_grouping_table .= "<tr style='background-color:". $activity_arr['color'].";'><td>".$activity_arr['label']."</td><td>".$cnt."</td> </tr>";
            if($activity_arr['isProductive'])
                $act_total = $act_total + $cnt;
        }
        $activity_grouping_table .="<tr><th style='padding: 5px;'>Total Productive Engagements</th><td>$act_total</td></tr></table><br></td>";
    }
    
    $ret_arr["uniq_cust_activity"] = count($lead_code_count_arr);
    $ret_arr["activity_txt"] = $activity_txt;
    $ret_arr["joint_employees"] = $joint_employees;
    $ret_arr["activity_grouping_table"] = $activity_grouping_table;
    return $ret_arr;
}

/**
 * @param string $emply_id
 * @param string $report_date
 * @param string $open_time
 * @param string $close_time
 *
 * @return string
 */
function get_visit_summary_for_daily_report($emply_id,$report_date,$open_time,$close_time){
	if($report_date!=''){
		$date_condition = " and GLD_VISIT_DATE='$report_date' ";
		$call_history_dt = " and gtc_date between '$report_date 00:00:00' and '$report_date 23:59:59' ";
	}else{
		$date_condition = " and GLD_DATE between '$open_time' and '$close_time' ";
		$call_history_dt = " and gtc_date between '$open_time' and '$close_time' ";
	}
	$sql3 = " select GLD_ACTIVITY_BY,ifnull(gcn.GCM_NATURE,'Automated') as visit_nature,vn.GAM_ACTIVITY_DESC as activity_nature,count(*) as cnt,sum(GLD_VISIT_IN_MIN) as time_in_min".
			" from gft_activity a join gft_lead_hdr on (GLH_LEAD_CODE=GLD_LEAD_CODE) ".
			" left join gft_complaint_nature_master gcn on( gcn.GCM_NATURE_ID=GLD_ACTIVITY_NATURE ) ".
			" left join gft_activity_master vn on ( vn.GAM_ACTIVITY_ID=GLD_VISIT_NATURE ) ".
			" left join gft_joint_visit_dtl jv on ( jv.GJV_ACTIVITY_ID=a.gld_activity_id and jv.GJV_EMP_ID=a.gld_emp_id and jv.GJV_VISIT_DATE=a.gld_visit_date ) ".
			" where (GLD_ACTIVITY_BY='$emply_id' or GJV_JOINT_EMP_ID='$emply_id') $date_condition and GLH_LEAD_TYPE!=8 and GLD_VISIT_NATURE not in (99) ".
			" group by GLD_ACTIVITY_NATURE,GLD_VISIT_NATURE order by GCM_ORDER_BY ";
	$res3 = execute_my_query($sql3);
	$call_que = " select ifnull(gcn.GCM_NATURE,'Automated') as visit_nature,vn.GAM_ACTIVITY_DESC as activity_nature,sum(time_to_sec(GTC_DURATION)) as actual_time".
        	   	" from gft_techsupport_incomming_call  ".
        	   	" left join gft_activity on (gtc_activity_id=gld_activity_id and gld_lead_code=gtc_lead_code) ".
        	   	" left join gft_complaint_nature_master gcn on( gcn.GCM_NATURE_ID=GLD_ACTIVITY_NATURE ) ".
        	   	" left join gft_activity_master vn on ( vn.GAM_ACTIVITY_ID=GLD_VISIT_NATURE ) ".
        	   	" where GTC_AGENT_ID='$emply_id' $call_history_dt group by GLD_VISIT_NATURE,GLD_ACTIVITY_NATURE ";
	$call_res = execute_my_query($call_que);
	$actual_time = array();
	while($call_data = mysqli_fetch_assoc($call_res)){
	    $call_visit_nature = $call_data['visit_nature'];
	    $call_activity_nature = $call_data['activity_nature'];
	    $actual_time[$call_visit_nature][$call_activity_nature] = $call_data['actual_time'];
	}
	
	$visit_arr = /*. (int[string][string][int]) .*/array();
	while($visit_data = mysqli_fetch_array($res3)){
		$visit_nature = $visit_data['visit_nature'];
		$activity_nature = $visit_data['activity_nature'];
		$cnt = (int)$visit_data['cnt'];
		$visit_arr[$visit_nature][$activity_nature][] = $cnt;
		$visit_arr[$visit_nature][$activity_nature][] = (int)$visit_data['time_in_min'];
	}
	$visit_total = $time_total  = $actual_time_tot = 0;
	$today_timestamp = strtotime(date('Y-m-d'));
	$visit_summary_tab ="<table border=1><tr><th colspan=5>Customer Visit Summary</th></tr><tr><th>Visit Nature</th><th>Activity Nature</th><th>Total Engagements</th><th>Total Time Spent <br> Entered by user</th><th>Actual Time <br> From call history</th></tr>";
	$color_code = array('#dcfafc','#f1dcfc');
	$mn = 0;
	foreach ($visit_arr as $vkey => $activity_arr){
		$rowspan = count($visit_arr[$vkey]);
		$visit_summary_tab .= "<tr style='background-color:$color_code[$mn]'><td rowspan=$rowspan>$vkey</td>";
		$put_tr = false;
		foreach ($activity_arr as $akey => $aval){
			$time_str = get_duration_in_string(date('H:i:s',$today_timestamp+($aval[1]*60)));
			$call_dur = isset($actual_time[$vkey][$akey])?$actual_time[$vkey][$akey]:0;
			$actual_time_str = get_duration_in_string(date('H:i:s',$today_timestamp+$call_dur));
			if($put_tr){
			    $visit_summary_tab .= "<tr style='background-color:$color_code[$mn]'><td>$akey</td><td align='center'> $aval[0] </td><td>$time_str</td><td>$actual_time_str</td></tr>";
			}else {
			    $visit_summary_tab .= "<td>$akey</td><td align='center'> $aval[0] </td><td>$time_str</td><td>$actual_time_str</td></tr>";
			}
			$put_tr = true;
			$visit_total += $aval[0];
			$time_total  += $aval[1];
			$actual_time_tot += $call_dur;
		}
		$mn = ($mn==0)?1:0;
	}
	$time_total_str = get_duration_in_string(date('H:i:s',$today_timestamp+($time_total*60)));
	$actual_time_tot_str = get_duration_in_string(date('H:i:s',$today_timestamp+($actual_time_tot)));
	$visit_summary_tab .="<tr><th colspan=2>Total</th><td align='center'>$visit_total</td><td>$time_total_str</td><td>$actual_time_tot_str</td></tr></table>";
	return $visit_summary_tab;
}

/**
 * @param string $emply_id
 * @param string $open_time
 * @param string $close_time
 *
 * @return string
 */
function get_pd_recording_tool_summary($emply_id,$open_time,$close_time){
	$sql_pd_tool = 	get_query_for_pd_recording_tool();	
	$sql_pd_tool .= " AND GPD_STATUS_ON<='$close_time'";
	$sql_pd_tool .= " AND GPD_UPDATED_BY='$emply_id'";
	$sql_pd_tool1 = $sql_pd_tool;
	$sql_pd_tool .= " AND GPD_STATUS_ON>='$open_time'";
	$sql_pd_tool .= " GROUP BY GPD_LEAD_CODE, GPD_TRAINING_ID ";
	$rows		=	execute_my_query($sql_pd_tool);
	$status_str = array('1'=>"Started",'2'=>"Completed",'3'=>"Google Drive Upload started",'4'=>"Google Drive Upload completed");
	$pd_tool_summary_tab ="<table border=1><tr><th colspan=3>Product Delivery Recording Tool Summary</th></tr><tr><th>Customer Name</th><th>Customer Id</th><th>Log Details</th></tr>";
	$color_code = array('#dcfafc','#f1dcfc');
	$mn = 0;
	$sl = 0;
	while($row=mysqli_fetch_array($rows)){
		$customer_name 	= $row['GLH_CUST_NAME'];
		$customer_id	= $row['GPD_LEAD_CODE'];
		$sl++;
		$status_list = explode('**-**', $row['GPD_STATUS_ID']);
		$status_list_on = explode('**-**', $row['GPD_STATUS_ON']);
		$status_dtl = "";
		$inc=0;
		if(!(in_array('1', $status_list))){
			$previous_dt	=	(date('Y-m-d H:i:s',strtotime("-1 days",strtotime($open_time))));
			$rows1		=	execute_my_query($sql_pd_tool1." AND GPD_STATUS_ON>='$previous_dt' AND GPD_LEAD_CODE='$customer_id' GROUP BY GPD_LEAD_CODE, GPD_TRAINING_ID ");
			if($row1=mysqli_fetch_array($rows1)){
				$status_list = explode('**-**', $row1['GPD_STATUS_ID']);
				$status_list_on = explode('**-**', $row1['GPD_STATUS_ON']);
			}
		}
		while($inc<count($status_list)){
			$status_id = $status_list[$inc];
			$status_str1 = isset($status_str[$status_id])?$status_str[$status_id]:"$status_id";
			$status_dtl .="<tr><td>".$status_str1."</td><td>".$status_list_on[$inc]."</td></tr>";
			$inc++;
		}
		if($status_dtl!=""){
			$status_dtl ="<table border='1' width='100%'><tr><th>Status</th><th>Updated At</th></tr>$status_dtl</table>";
		}
		$pd_tool_summary_tab .= "<tr style='background-color:$color_code[$mn]'><td align='left'>$customer_name</td><td>$customer_id</td><td>$status_dtl</td></tr>";
		$mn = ($mn==0)?1:0;
	}
	$pd_tool_summary_tab .= "</table>";
	return $pd_tool_summary_tab;
}
/**
 * @param string $emply_id
 * @param string $open_time
 * @param string $close_time
 *
 * @return string
 */
function get_internal_call_summary($emply_id,$open_time,$close_time){
	$sql4 = " select  if( GTC_EMP_ID>0, 1, if(GTC_MAIN_GROUP=709,2, if( GTC_LEAD_CODE > 0 ,3,4) ) ) as user_type, ".
			" count(distinct if(GTC_EMP_ID>0,GTC_EMP_ID,null)) as 1_uniq_calls, ".
			" count(if(GTC_EMP_ID>0,GTC_EMP_ID,null)) as 1_total_calls, ".
			" count(distinct if(GTC_MAIN_GROUP=709,GTC_LEAD_CODE,null)) as 2_uniq_calls, ".
			" count(if(GTC_MAIN_GROUP=709,GTC_LEAD_CODE,null)) as 2_total_calls, ".
			" count(distinct if(GTC_LEAD_CODE > 0 and GTC_MAIN_GROUP!=709,GTC_LEAD_CODE,null)) as 3_uniq_calls, ".
			" count(if(GTC_LEAD_CODE > 0 and GTC_MAIN_GROUP!=709,GTC_LEAD_CODE,null)) as 3_total_calls, ".
			" count(distinct if(GTC_EMP_ID=0 and GTC_LEAD_CODE=0,GTC_NUMBER,null)) as 4_uniq_calls, ".
			" count(if(GTC_EMP_ID=0 and GTC_LEAD_CODE=0,GTC_NUMBER,null)) as 4_total_calls, ".
			" SEC_TO_TIME(SUM(TIME_TO_SEC(if(GTC_CALL_STATUS=1,GTC_DURATION,0)))) as total_ic_duration, ".
			" SEC_TO_TIME(SUM(TIME_TO_SEC(if(GTC_CALL_STATUS=4,GTC_DURATION,0)))) as total_oc_duration, ".
			" SEC_TO_TIME(SUM(TIME_TO_SEC(GTC_DURATION))) as total_duration ".
			" from gft_techsupport_incomming_call ".
			" where GTC_AGENT_ID='$emply_id' and GTC_CALL_STATUS in (1,4) and ".
			" GTC_DATE >= '$open_time' and GTC_DATE <= '$close_time' group by user_type ";
	$res4 = execute_my_query($sql4);
	$summary_tab =	"<table border=1><tr><th colspan=6>Call Summary</th></tr>".
					"<tr><th>Group</th><th>Total calls</th><th>Unique calls</th><th>Incomming call duration</th><th>Outgoing call duration</th><th>Total</th></tr>";
	$sum_ic_duration = $sum_oc_duration = 0;
	while($row4 = mysqli_fetch_array($res4)){
		$total_ic_duration_str = get_duration_in_string($row4['total_ic_duration']);
		$total_oc_duration_str = get_duration_in_string($row4['total_oc_duration']);
		$total_duration_str = get_duration_in_string($row4['total_duration']);
		$user_type = (int)$row4['user_type'];
		$group_name_arr = array(1=>'Employees',2=>'Partners',3=>'Customers',4=>'Unknown');
		$group_name = $group_name_arr[$user_type];
		$summary_tab .= "<tr><td>$group_name</td>".
				"<td>".$row4["{$user_type}_total_calls"]."</td>".
				"<td>".$row4["{$user_type}_uniq_calls"]."</td>".
				"<td>".$total_ic_duration_str."</td>".
				"<td>$total_oc_duration_str</td><td>$total_duration_str</td></tr>";
		$sum_ic_duration += time_to_seconds($row4['total_ic_duration']);
		$sum_oc_duration += time_to_seconds($row4['total_oc_duration']);
	}
	$sum_total_duration = $sum_ic_duration + $sum_oc_duration;
	if($sum_total_duration > 0){
		$today_timestamp = strtotime(date('Y-m-d'));
		$summary_tab .= "<tr><td colspan='3' align='center'>Total</td>".
						"<td>".get_duration_in_string(date('H:i:s',$today_timestamp+$sum_ic_duration))."</td>".
						"<td>".get_duration_in_string(date('H:i:s',$today_timestamp+$sum_oc_duration))."</td>".
						"<td>".get_duration_in_string(date('H:i:s',$today_timestamp+$sum_total_duration))."</td></tr>";
	}
	$summary_tab .= "</table>";
	return $summary_tab;
}

/**
 * @param string $lead_code
 * 
 * @return string
 */
function get_sms_gateway_passcode($lead_code){
	$res1 = execute_my_query("select GSG_PASS_CODE from gft_sms_gateway_info where GSG_LEAD_CODE='$lead_code' and GSG_PASS_CODE!='' ");
	if($row1=mysqli_fetch_array($res1)){
		$passcode = $row1['GSG_PASS_CODE'];
	}else{
		$passcode = generatePassword(8);
	}
	return $passcode;
}

/**
 * @param string $lead_code
 * @param string $order_no
 * @param string $callback
 * @param string $prod_code
 * @param string $registered_email_id
 * @param string $prod_skew
 * @param string $plan_skew
 *
 * @return string
 */

function alert_new_Register($lead_code,$order_no,$prod_code,$callback='',$registered_email_id='',$prod_skew='',$plan_skew=''){
	$order_no = substr($order_no,0,15);
	if( in_array($prod_code,array('604','705','708','522')) ){
		if($prod_code=='604'){
			$prod_skew = '05.0SL';
			$plan_skew = '05.0RCF30';
		}else if($prod_code=='705'){
			$prod_skew = '01.0TPE';
			$plan_skew = '01.0TPE';
		}else if($prod_code=='522'){
			$prod_skew = '01.0PLT';
		}
		$passcode = get_sms_gateway_passcode($lead_code);
		//$sms_userid=$email_id; customer id as user id
		$query_check="select GSG_LEAD_CODE from gft_sms_gateway_info where GSG_LEAD_CODE='$lead_code' and GSG_PRODUCT_CODE=$prod_code ";
		$result_check=execute_my_query($query_check);
		if(mysqli_num_rows($result_check)==0){
			$query="insert into gft_sms_gateway_info (GSG_ORDER_NO,GSG_PRODUCT_CODE,GSG_PRODUCT_SKEW,GSG_LEAD_CODE,GSG_FULLFILLMENT_NO,GSG_START_DATE,GSG_END_DATE,GSG_ROOT_PRODUCT,GSG_PASS_CODE,GSG_SAAS_PLAN,GSG_SMS_USERID) " .
					" values('$order_no','$prod_code','$prod_skew',$lead_code,1,date(now()),date(now()),'','$passcode','$plan_skew','$lead_code')";
			execute_my_query($query);
			$req_id='';
			if($prod_code=='522'){
				zepogatewaycall($order_no,$lead_code,$prod_code,1);
			}else{
				$req_id = smsgatewaycall($order_no,$lead_code,$registered_email_id,'',false,'','request_id');
			}			
			$response['message']="success";
			$response['request_id'] = $req_id;
		}
		else{
			$response['error_code']="008";
			$response['message']="Email id already registered ";
		}
	}else{
		$response['error_code']="001";
		$response['message']="Invalid product code";
	}

	if ($callback!=""){
		$resp_msg = $callback . '(' . json_encode($response) . ')';
	}else{
		$resp_msg = json_encode($response);
	}
	return $resp_msg;
}
/**
 * @param string $lead_code
 * 
 * @return string[int]
 */
function get_current_product_delivery_status($lead_code){
	$pd_status	=	0;
	$last_handover_date="";	
	$return_array =  array();
	$coupon_rows	=	execute_my_query("select GCD_IS_ECOUPON,GCD_TO_ID,GCD_SIGNED_OFF,GCD_GIVEN_DATE,GCD_RECEIVED_DATE,".
			" GCD_ORDER_NO, GCD_REF_ORDER_NO, GCD_AGE_START_DATE,GCD_EXPIRY_DATE from gft_coupon_distribution_dtl".
			" where GCD_TO_ID='$lead_code'  order by GCD_GIVEN_DATE desc, GCD_COUPON_NO desc limit 1");
	if((mysqli_num_rows($coupon_rows)>0) && $coupon_row=mysqli_fetch_array($coupon_rows)){
		$coupon_type	=	$coupon_row['GCD_IS_ECOUPON'];
		$received_date	=	$coupon_row['GCD_RECEIVED_DATE'];
		$age_date		=	$coupon_row['GCD_AGE_START_DATE'];
		$ref_order_no	=	$coupon_row['GCD_REF_ORDER_NO'];
		if(($received_date=="" || $received_date=="0000-00-00") && ($age_date=="" || $age_date=="0000-00-00")){
			$pd_status	=	1;
		}else if(($received_date=="" || $received_date=="0000-00-00") && ($age_date!="" && $age_date!="0000-00-00")){
			$pd_status	=	2;
		}else if(($received_date!="" && $received_date!="0000-00-00") && ($age_date!="" && $age_date!="0000-00-00")){
			$pd_status	=	3;
			$last_handover=execute_my_query("select GAH_DATE_TIME from gft_audit_hdr where GAH_LEAD_CODE='$lead_code' AND GAH_AUDIT_TYPE='19' order by GAH_AUDIT_ID desc");
			if($coupon_type!="1"){
				$handover_status= execute_my_query("select GAH_AUDIT_ID,GAH_DATE_TIME from gft_audit_hdr where GAH_REFFERNCE_ORDER_NO='$ref_order_no' and GAH_AUDIT_TYPE=19 and GAH_LEAD_CODE='$lead_code'");
				if(mysqli_num_rows($handover_status)==0){
					$pd_status	=	2;
				}
			}
			if(mysqli_num_rows($last_handover)>0 && $row=mysqli_fetch_array($last_handover)){
				$last_handover_date	=	$row['GAH_DATE_TIME'];
			}
		}
	}
	$return_array[0]=$pd_status;
	$return_array[1]=$last_handover_date;
	return $return_array;
}
/**
 * @param string $contact_ids
 * @param int $contact_type
 *
 * @return string[int]
 */
function get_customer_contact_array($contact_ids,$contact_type){
	$contacts=array();
	if($contact_ids!=""){
		$rows=execute_my_query("select GCC_CONTACT_NO from gft_customer_contact_dtl where gcc_id in($contact_ids) AND gcc_contact_type=$contact_type");
		while($row=mysqli_fetch_array($rows)){
			$contacts[]=$row['GCC_CONTACT_NO'];
		}
	}
	return $contacts;
}
/**
 * @param string $cp_id
 * 
 * @return string[int]
 */
function get_mobile_app_key_authtoken($cp_id){
	$return_array=array();
	$result=execute_my_query("select AUTH_KEY,AUTH_TOKEN  from gft_emp_auth_key where EMP_ID='$cp_id'");
	if(mysqli_num_rows($result)==1 && $row=mysqli_fetch_array($result)){
		$return_array[0]=$row['AUTH_KEY'];
		$return_array[1]=$row['AUTH_TOKEN'];
	}
	return $return_array;
}

/**
 * @param string $employee_id
 * 
 * @return mixed[]
 */
function get_pending_support_by_max_hrs($employee_id) {
	$pending = false;
	$return_arr = array();
	$pending_tkts_qry = " select GCH_COMPLAINT_DATE,GCD_COMPLAINT_ID,GCD_ACTIVITY_DATE,gcd_status,GTM_USE_ACTIVITY_DATE, ".
			" gtm_max_hrs from gft_customer_support_hdr hdr ".
			" join gft_customer_support_dtl dtl on (GCH_LAST_ACTIVITY_ID=gcd_activity_id) ".
			" join gft_status_master on (dtl.gcd_status=gtm_code) ".
			" where dtl.gcd_process_emp='$employee_id' and GTM_GROUP_ID not in (3) "; //3 - solved group
	$pending_complaints = "";
	$pending_count = 0;
	$pending_tkts_res = execute_my_query($pending_tkts_qry);
	while($row = mysqli_fetch_array($pending_tkts_res)) {
		$complaint_id = $row['GCD_COMPLAINT_ID'];
		$reported_date = $row['GCH_COMPLAINT_DATE'];
		$activity_date = $row['GCD_ACTIVITY_DATE'];
		$use_activity_date = $row['GTM_USE_ACTIVITY_DATE'];
		$status = $row['gcd_status'];
		$max_time = (int)$row['gtm_max_hrs'];
		if($max_time > 0) {
			$age = 0;
			if($use_activity_date=='N') {
				$age = strtotime(date('Y-m-d H:i:s'))-strtotime($reported_date);
			}else {
				$age = strtotime(date('Y-m-d H:i:s'))-strtotime($activity_date);
			}
			$age /= 3600;
			if(floor($age)>(float)$max_time) {
				$pending_complaints .= ($pending_complaints!=='')?",$complaint_id":$complaint_id;
				$pending_count++;
				$pending = true;
			}
		}
	}
	$return_arr['tkt_ids'] = $pending_complaints;
	$return_arr['count'] = $pending_count;
	$return_arr['pending'] = $pending;
	return $return_arr;
}

/**
 * @param string $emp_id
 * @param boolean $reporting_under_rm
 * 
 * @return string[int][int]
 */
function get_reporting_hierarchy($emp_id,$reporting_under_rm=false,$roles=array()) {
	$reporting_mgr_qry = " select ger_reporting_empid,gem_emp_name,gem_email,gem_role_id from gft_emp_reporting ".
						 " join gft_emp_master on (ger_reporting_empid=gem_emp_id) ".
						 " where ger_status='A' ";
	$reporting_mgrs = array();
	$curr_emp_id = $emp_id;
	$prev = '0';
	$i = 0;
	while($curr_emp_id!='1' and $prev!=$curr_emp_id) {
		$prev = $curr_emp_id;
		$reporting_mgr_qry_cond = " and ger_emp_id='$curr_emp_id' ";
		$reporting_mgr_res = execute_my_query($reporting_mgr_qry.$reporting_mgr_qry_cond);
		if($row = mysqli_fetch_array($reporting_mgr_res)) {
		    $role_id = $row['gem_role_id'];
		    if(count($roles)>0 and !in_array($role_id,$roles)) {
		        continue;
		    }
			$reporting_mgrs[$i][0] = $row['ger_reporting_empid'];
			$reporting_mgrs[$i][1] = $row['gem_emp_name'];
			$reporting_mgrs[$i][2] = $row['gem_email'];
			$curr_emp_id = $row['ger_reporting_empid'];
			$i++;
		}
	}
	$j = $i;
	if($reporting_under_rm) {
		$reporting_under_emps = get_emp_for_reporting_mgr($emp_id);
		for(;$i<$j+count($reporting_under_emps);$i++) {
			$reporting_mgrs[$i][0] = $reporting_under_emps[$i][0];
			$reporting_mgrs[$i][1] = $reporting_under_emps[$i][1];
			$reporting_mgrs[$i][2] = $reporting_under_emps[$i][2];
		}
	}
	return $reporting_mgrs;
}

/**
 * @param string $cust_id
 *
 * @return string
 */
function get_corporate_customer_for_client($cust_id) {
	$lead_type = get_lead_type_for_lead_code($cust_id);
	if($lead_type=='13') {
		return get_single_value_from_single_table("glh_reference_given", "gft_lead_hdr", "glh_lead_code", $cust_id);
	}
	return $cust_id;
}

/**
 * @param string $lead_code
 * @return void
 */
function capture_first_order_date($lead_code) {
    $update_cust_id = $lead_code;
	if($lead_code!='') {
		$lead_type = get_lead_type_for_lead_code($lead_code);
		if($lead_type=='13') {
			$corp_cust_lead_code = get_corporate_customer_for_client($lead_code);
			if($corp_cust_lead_code!='') {
				$lead_code = $corp_cust_lead_code;
			}
		}
		$order_close_date = get_single_value_from_single_table("glh_order_close_date","gft_lead_hdr","glh_lead_code",$lead_code);
		$min_dt = $emp_id = '';
		if($order_close_date=='' or strtotime($order_close_date)>strtotime(date('Y-m-d')) or $order_close_date=='0000-00-00') {
			$first_order_qry = " select t.dt dt, t.cust_id god_lead_code,god_emp_id from ".
							   " (select min(god_created_date) as dt,god_lead_code cust_id,god_emp_id ".
							   " from gft_order_hdr  join gft_leaD_hdr on (god_lead_code=glh_lead_code) ".
		   					   " where god_lead_code in (".trim($lead_code).") and god_order_amt>0 ".
							   " and god_order_status='A' group by cust_id union all select min(god_created_date) dt,gco_cust_code cust_id,god_emp_id ".
							   " from gft_cp_order_dtl join gft_order_hdr on (gco_order_no=god_order_no) where ".
							   " GCO_CUST_CODE in (".trim($lead_code).") and GOD_ORDER_STATUS='A' and GOD_ORDER_AMT>0 group by cust_id) t";
			$first_order_res = execute_my_query($first_order_qry);
			while($row = mysqli_fetch_array($first_order_res)) {
				if($min_dt=='' or strtotime(date($min_dt))>strtotime(date($row['dt']))) {
					$min_dt = date('Y-m-d',strtotime($row['dt']));
					$emp_id = $row['god_emp_id'];
				}
			}
			if($min_dt!='0000-00-00') {
				execute_my_query("update gft_lead_hdr set GLH_ORDER_CLOSE_DATE='$min_dt',glh_order_close_by='$emp_id' where glh_lead_code='$update_cust_id'");
			}
		}
	}
}

/**
 * @param string $uid
 * @param string $comments
 * @param string $video_id
 * @return void
 */
function send_mail_to_marketing_team($uid,$comments,$video_id){
	$emp_arr = get_emp_master($uid,'',null,false);
	$email_to	= get_samee_const('PULL_LEAD_FEXECUTIVE');
	$email_from = $emp_arr[0][1];
	$ename 		= $emp_arr[0][1];
	$video_name=get_single_value_from_single_table("GPV_VIDEO_NAME", "gft_product_video_master", "GPV_ID", $video_id);
	$db_mail_content_config['Employee_Name'][0]=$ename;
	$db_mail_content_config['Video_Comments'][0]=$comments;
	$db_mail_content_config['Video_Name'][0]=$video_name;
	send_formatted_mail_content($db_mail_content_config,6,257,$email_from,$email_to);
}

/**
 * @param string $order_no
 * @param string $split
 * @param string $lead_code
 * @param string $pcode
 * @param string $pskew
 * @param string $fulfil_no
 * @param string $curr_order_status
 * @return boolean
 */
function check_and_approve_license($order_no,$split,$lead_code,$pcode,$pskew,$fulfil_no,$curr_order_status='1') {
	$split = get_single_value_from_single_table("god_order_splict", "gft_order_hdr", "god_order_no", "$order_no");
	if($split=='0') {
	$qry = " update gft_order_product_dtl join gft_product_master on (gop_product_code=GPM_PRODUCT_CODE and gop_product_skew=GPM_PRODUCT_SKEW) ".
		   " set gop_license_status='2' where GFT_SKEW_PROPERTY in ('1','11') and gop_order_no='$order_no'";
	} else {
		$qry = " update gft_cp_order_dtl join gft_product_master on (gco_product_code=GPM_PRODUCT_CODE and gco_skew=GPM_PRODUCT_SKEW) ".
			   " set gco_license_status='2' where GFT_SKEW_PROPERTY in ('1','11') and gco_order_no='$order_no'";
	}
	$res = execute_my_query($qry);
	if($res and mysqli_affected_rows_wrapper()>0) {
		$lic_qry = get_insert_query_for_lic_approval_log($lead_code, $order_no,$fulfil_no,$pcode,$pskew,"Automatic license approval on 0 outstanding amount.",2,false,0,SALES_DUMMY_ID,$curr_order_status);
		$res1 = execute_my_query($lic_qry);
		if($res1) {
			return true;
		}
	}
	return false;
}

/**
 * @param string $q_no
 * @return string[string]
 */
function check_quotation_dtls($q_no) {
	$quote_dtls_qry = "select * from gft_quotation_hdr where gqh_order_no = '".mysqli_real_escape_string_wrapper($q_no)."'";
	$ret_arr = array();
	$quote_res = execute_my_query($quote_dtls_qry);
	if(mysqli_num_rows($quote_res)==0) {
		return $quote_res;
	} else {
		$row = mysqli_fetch_array($quote_res);
		foreach ($row as $k => $v) {
			if(is_numeric($k)) {
				continue;
			} else {
				$ret_arr["$k"] = $v;
			}
		}
	}
	return $ret_arr;
}

/**
 * @param string $lead_code
 * @param string $quotation_no
 * @return mixed[string]
 */
function check_for_hq_proposal_doc($lead_code,$quotation_no) {
	$ret_arr = array();
	$statuses = array("1"=>"In-progress","2"=>"Document created","3"=>"Document sent to customer","4"=>"Discarded");
	$doc_id = '';
	$doc_id_qry = execute_my_query(" select gcc_id,gcc_status,gcc_doc_path,gcc_version_id from gft_corporate_customer_doc_dtl ".
			" where gcc_lead_code='$lead_code' and gcc_doc_type='2' and GCC_VERSION_ID is not null and gcc_status!='4' and  ".
			" GCC_UPLOADED_ON =(select max(GCC_UPLOADED_ON) from gft_corporate_customer_doc_dtl where gcc_lead_code='$lead_code' ".
			" and gcc_doc_type='2')");
	if(mysqli_num_rows($doc_id_qry)>0) {
		$row 	= mysqli_fetch_array($doc_id_qry);
		$doc_id = $row['gcc_id'];
		$doc_status = $statuses[$row['gcc_status']];
		$path = $row['gcc_doc_path'];
		$path_contents = explode("/",$path);
		$ret_arr['status'] 		= true;
		$ret_arr['doc_id'] 		= $doc_id;
		$ret_arr['doc_status'] 	= $doc_status;
		$ret_arr['doc_path']	= $path;
		$ret_arr['doc_name']	= $path_contents[count($path_contents)-1];
		$ret_arr['doc_status_id'] 	= $row['gcc_status'];
		$ret_arr['doc_version']		= $row['gcc_version_id'];
	} else {
		$ret_arr['status'] = false;
	}
	return $ret_arr;
}
/**
 * @return mixed[]
 */
function get_predefined_topics_list() {
	$predefined_topics = array();
	$contents_qry = execute_my_query(" select ghc_topic from gft_hq_proposal_contents where ghc_topic not in ('23','24','25') ");
	if(mysqli_num_rows($contents_qry)>0) {
		while ($r = mysqli_fetch_array($contents_qry)) {
			$predefined_topics[] = $r['ghc_topic'];
		}
	}
	return $predefined_topics;
}
/**
 * @param string $topic
 * @param string $vertical
 * @return string
 */
function get_predefined_content($topic,$vertical='0') {
	$content = '';
	$whr_cond = '';
	if($vertical!='0') {
		$whr_cond .= " and (ghc_vertical_id='$vertical' or ghc_vertical_id='0')";
	}
	$qry = execute_my_query(" select ghc_content from gft_hq_proposal_contents where ghc_topic='$topic' and ghc_status='1' $whr_cond order by ghc_vertical_id desc ");
	if(mysqli_num_rows($qry)>=1) {
		$row = mysqli_fetch_array($qry);
		$content .= $row['ghc_content'];
	}
	return $content;
}
/**
 * @param string $lead_code
 * @return string
 */
function get_cust_testimonials_images($lead_code) {
	$lead_vertical = get_single_value_from_single_table("glh_vertical_code","gft_lead_hdr","glh_lead_code",$lead_code);
	$macro_vertical = get_single_value_from_single_table("if(gtm_is_macro='Y',gtm_vertical_code,gtm_micro_of)", "gft_vertical_master", "gtm_vertical_code", $lead_vertical);
	$testimonial_imgs = array();
	$str = '';
	$testimonial_qry = " select GCT_CONTENT,GCT_VIDEO_URL from gft_cust_testimonials where GCT_VERTICAL_ID='$macro_vertical' and GCT_STATUS='1' ";
	$res = execute_my_query($testimonial_qry);
	if(mysqli_num_rows($res)==0) {
		$mocro_product_qry = execute_my_query(" select gbr_product from gft_bvp_relation where gbr_vertical='$macro_vertical' ");
		if($row = mysqli_fetch_array($mocro_product_qry)) {
			$prod = $row['gbr_product'];
			$verts = execute_my_query(" select gbr_vertical from gft_bvp_relation where gbr_product='$prod' ");
			$vertical_list = $comma = "";
			while($row1 = mysqli_fetch_array($verts)) {
				$vertical_list .= $comma.$row1['gbr_vertical'];
				$comma = ",";
			}
			$paths = execute_my_query(" select GCT_CONTENT,GCT_VIDEO_URL from gft_cust_testimonials where gct_vertical_id in ($vertical_list) and GCT_CONTENT!='' and GCT_CONTENT is not null ");
			while($row2 = mysqli_fetch_array($paths)) {
				$testimonial_imgs[] = array($row2['GCT_CONTENT'],(string)$row2['GCT_VIDEO_URL']);
			}
		}
	} else {
		while ($row = mysqli_fetch_array($res)) {
			$testimonial_imgs[] = array($row['GCT_CONTENT'],(string)$row['GCT_VIDEO_URL']);
		}
	}
	if(count($testimonial_imgs)==0) {
		die('Unable to fetch testimonial details.');
	}
	$line_breaks = '';
	foreach ($testimonial_imgs as $dtl) {
		$path = $dtl[0];
		$str .= "<img style='display: block; margin-left: auto; margin-right: auto; width: 60%; border: 1px solid black;' ".
				"src=".get_samee_const("DOMAIN_NAME")."$path hieght=450 alt='Testimonial_Image'>";
		if($dtl[1]!='') {
			$link = $dtl[1];
			$str = "<a href='$link' target=_blank>$str</a>";
		}
		$str .= $line_breaks;
		$line_breaks = '<br/><br/>';
	}
	return $str;
}
/**
 * @param string $topic_id
 * @param string $cust_id
 * @param string $topic_name
 * @param string $quotation_no
 * @param string $template_id
 * @param string $kit_type
 * @param boolean $warehouse
 * @return string
 */
function get_hq_proposal_predefined_content($topic_id,$topic_name,$cust_id,$quotation_no,$template_id='0',$kit_type='0',$warehouse=false) {
	$predefined_topics = get_predefined_topics_list();
	$doc_content = $image_path = $image_alt = '';
	$lead_vertical = get_single_value_from_single_table("glh_vertical_code","gft_lead_hdr","glh_lead_code",$cust_id);
	if(in_array($topic_id,$predefined_topics)) {
		if($lead_vertical!='' or $lead_vertical!='0') {
			$doc_content .= get_predefined_content($topic_id,$lead_vertical);;
		} else {
			die("Vertical information is not available in SAM for $cust_id. Contact SAM.");
		}
	} else if($template_id!='0') {
		$image_height = '800';
		$image_width = '500';
		$image_alt = "hq_proposal_img_$topic_id";
		if($kit_type!='0' and in_array($topic_id,array('8'))) {
			$image_path = get_single_value_from_single_table("GPT_PLAN_IMG_PATH", "gft_product_type_master", "GPT_TYPE_ID", $kit_type);
			$image_height = '350';
			$image_width = '500';
		}
		if($topic_id=='4') {
			$samee_const = "HQ_PROPOSAL_SOLUTION_OVERVIEW_NO_WAREHOUSE";
			if($warehouse) {
				$samee_const = "HQ_PROPOSAL_SOLUTION_OVERVIEW";
    			if(in_array($lead_vertical,array('13','28'))) {
    			    $samee_const = "HQ_PROPOSAL_SOLUTION_OVERVIEW_CENTRAL_KITCHEN";
    			}
			}
			$image_path = "/images/".get_samee_const($samee_const);
			$image_height = '800';
			$image_width = '500';
		}
		if($topic_id=='18') {
			$image_path = "/images/partial_list_of_customer.png";
			$image_height = '1000';
			$image_width = '700';
		}
		$content_vars = array();
		$created_by = get_single_value_from_single_table("gqh_emp_id", "gft_quotation_hdr", "gqh_order_no", $quotation_no);
		$content_vars['customer_name'] 	= get_single_value_from_single_table("glh_cust_name","gft_lead_hdr","glh_lead_code",$cust_id);
		$content_vars['execName']		= get_emp_name((int)$created_by);
		$content_vars['execMailId'] 	= get_email_addr($created_by);
		$content_vars['execMobileNo']	= get_mobileno($created_by);
		$content_vars['image_path']		= get_samee_const("DOMAIN_NAME").$image_path;
		$content_vars['image_height']	= $image_height;
		$content_vars['image_width']	= $image_width;
		$content_vars['image_alt']		= $image_alt;
		$content_vars['vertical_name']	= get_vertical_name_for($lead_vertical);
		$doc_content = get_formatted_document_content(/*.(string[string]).*/$content_vars,'8',$template_id);
	} else {
		die("Content for '$topic_name' is not found. Contact SAM.");
	}
	return $doc_content;
}

/**
 * @param string $quotation_no
 * @return mixed[string]
 */
function get_current_tax_mode($quotation_no) {
	$ret_arr = /*.(mixed[string]).*/array();
	$dtls_qry = " select gqh_tax_mode,gqh_lead_code,gqp_sgst_per+gqp_igst_per+gqp_cgst_per as total_tax_per ".
				" from gft_quotation_hdr join gft_quotation_product_dtl on (gqh_order_no=gqp_order_no) ".
				" where gqh_order_no='$quotation_no' ";
	$qry_res = execute_my_query($dtls_qry);
	if($row = mysqli_fetch_array($qry_res)) {
		$ret_arr['tax_mode'] = $row['gqh_tax_mode'];
		$ret_arr['same_state'] = false;
		$ret_arr['gst_per'] = $row['total_tax_per'];
		$lead_code = $row['gqh_lead_code'];
		if($ret_arr['tax_mode']=='4') {
			if(is_same_state($lead_code)) {
				$ret_arr['same_state'] = true;
			}
		}
	}
	return $ret_arr;
}
/**
 * @param string $quotation_no
 * @param string $tax_mode
 * @param boolean $get_only_additionals
 * @param boolean $skip_pd_expense_type
 * @return mixed[][string]
 */
function get_quotation_product_dtl($quotation_no,$tax_mode,$get_only_additionals=false,$skip_pd_expense_type=false) {
	$tax_cols = " GQP_SER_TAX_RATE+GQP_TAX_RATE ";
	if($tax_mode=='4') {
		$tax_cols = " gqp_cgst_per + gqp_sgst_per + gqp_igst_per ";
	}
	$dtls_arr = array();
	$query = " select round(GQP_SELL_AMT) sell_amt,GQP_QTY qty,pm.GPM_ORDER_TYPE order_type,GQP_PRODUCT_CODE pcode, ".
		   " GQP_PRODUCT_SKEW pskew,pm.GFT_SKEW_PROPERTY skew_ppty,GQP_SELL_RATE sell_rate,pm.GPM_DISPLAY_NAME disp, ".
		   " $tax_cols tax_rate,GQP_COUPON_HOUR,GPM_PRODUCT_TYPE,GPM_IS_INTERNAL_PRODUCT, ".
		   " concat(GFT_HIGHER_PCODE,GFT_HIGHER_SKEW) ucode,GQP_ADJ_DISCOUNT from gft_quotation_product_dtl ".
		   " join gft_product_master pm on (GPM_PRODUCT_CODE=GQP_PRODUCT_CODE and GQP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
		   " join gft_product_family_master pfm on (pm.gpm_product_code=pfm.gpm_product_code) ".
		   " where GQP_ORDER_NO='$quotation_no' ";
	if($get_only_additionals) {
		$query .= " and pm.GPM_PRODUCT_CODE='391' ";
	} else {
		$query .= " and pm.GPM_PRODUCT_CODE!='391' ";
	}
	if($skip_pd_expense_type) {
		$query .= "and concat(pm.gpm_product_code,'-',pm.gpm_product_skew) not in ('391-01.0EE','391-01.0OE') ";
	}
	$qry = execute_my_query($query);
	if(mysqli_num_rows($qry)>0) {
		while($row = mysqli_fetch_array($qry)) {
			$row_dtls = array();
			$row_dtls['sell_amt'] 	= $row['sell_amt'];
			$row_dtls['sell_rate'] 	= $row['sell_rate'];
			$row_dtls['tax_rate']	= $row['tax_rate'];
			$row_dtls['pcode'] 		= $row['pcode'];
			$row_dtls['pskew'] 		= $row['pskew'];
			$row_dtls['qty'] 		= $row['qty'];
			$row_dtls['order_type'] = $row['order_type'];
			$row_dtls['skew_ppty'] 	= $row['skew_ppty'];
			$row_dtls['display']	= $row['disp'];
			$row_dtls['hours']		= $row['GQP_COUPON_HOUR'];
			$row_dtls['prod_type']	= $row['GPM_PRODUCT_TYPE'];
			$row_dtls['ucode']		= $row['ucode'];
			$row_dtls['internal_prod'] = $row['GPM_IS_INTERNAL_PRODUCT'];
			$row_dtls['adj_amt']    = $row['GQP_ADJ_DISCOUNT']; // *$row['qty']*((int)$row['GQP_COUPON_HOUR']>0?$row['GQP_COUPON_HOUR']:1);
			$dtls_arr[] = $row_dtls;
		}
	}
	return $dtls_arr;
}
/**
 * @param string $quotation_no
 * @return mixed[string]
 */
function get_location_counts($quotation_no) {
	$res_arr = array();
	$res_arr['total'] = 0;
	$chk_qry = execute_my_query(" select sum(GSK_PRODUCT_QTY*GQP_QTY) as qty_val,GPM_PRODUCT_TYPE,GPT_TYPE_NAME from gft_quotation_product_dtl ".
				" join gft_skew_kit_master on (gqp_product_code=gsk_kit_pcode and gqp_product_skew=gsk_kit_pskew) ".
				" join gft_product_master on (gsk_product_code=gpm_product_code and gsk_product_skew=gpm_product_skew) ".
				" join gft_product_type_master on (gpm_product_type=gpt_type_id) ".
				" where GQP_ORDER_NO='$quotation_no' and GPM_PRODUCT_TYPE in (11,12,13) group by GPM_PRODUCT_TYPE");
	if(mysqli_num_rows($chk_qry)>0) {
		while($row = mysqli_fetch_array($chk_qry)) {
			$count = (int)$row['qty_val'];
			$res_arr['total'] += $count;
			$res_arr[$row['GPM_PRODUCT_TYPE']]['count'] = $count;
			$res_arr[$row['GPM_PRODUCT_TYPE']]['disp_name'] = $row['GPT_TYPE_NAME'];
		}
	}
	return $res_arr;
}
/**
 * @param string $gpm_prod_code
 * @param string $gpm_prod_skew
 * @return string
 */
function get_prod_dtl_for_kit($gpm_prod_code,$gpm_prod_skew) {
	return " select GSK_PRODUCT_CODE,GSK_PRODUCT_SKEW,pm.GPM_PRODUCT_TYPE,pm.GFT_SKEW_PROPERTY,GSK_PRODUCT_QTY, ".
			" concat(pm.gpm_product_code,pm.gpm_product_skew) pcode,concat(pm.GFT_HIGHER_PCODE, pm.GFT_HIGHER_SKEW) ucode ".
			" from gft_skew_kit_master ".
			" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GSK_PRODUCT_CODE and pm.GPM_PRODUCT_SKEW=GSK_PRODUCT_SKEW) ".
			" left join gft_product_master sk on (sk.GPM_PRODUCT_CODE=GSK_KIT_PCODE and sk.GPM_PRODUCT_SKEW=GSK_KIT_PSKEW) ".
			" where GSK_KIT_PCODE='$gpm_prod_code' and GSK_KIT_PSKEW='$gpm_prod_skew' and if(GSK_KIT_PCODE=308 and sk.GPM_ORDER_TYPE=2,GSK_PRODUCT_CODE=300,1) ";
}
/**
 * @param string $quotation_no
 * @param string $tax_mode
 * @return mixed[]
 */
function get_asa_dtls_for_quotation($quotation_no,$tax_mode) {
	$ret_arr = array();
	$tax_cols = " GAP_SERVICE_TAX_PERC ";
	$tax_per = 15;
	$currency = 'INR';
	if($tax_mode=='4') {
		$tax_cols = " GAP_SERVICE_TAX_PERC ";
		$tax_per = 18;
	} else if($tax_mode=='3') {
		$currency = 'USD';
	}
	$qpd = get_quotation_product_dtl($quotation_no,$tax_mode);
	$assure_price_res = execute_my_query(" select GAP_PRICE,$tax_cols as tax from gft_assure_price_list_master where GAP_SKEW_PROPERTY_ID=4 and GAP_PRICE_NAME='Additional' and GAP_PRODUCT_CODE='0' ");
	$asa_price = $asa_tax_rate = 0.0;
	if(mysqli_num_rows($assure_price_res)==1) {
		$r = mysqli_fetch_array($assure_price_res);
		$asa_price = (float)$r['GAP_PRICE'];
		$asa_tax_rate = (float)$r['tax'];
	}
	$total_tax = $total_asa = $total_asa_no_tax = 0;
	$perpetual_asa_amt = $additional_asa_amt = 0.0;
	$additional_cnt = 0;
	foreach ($qpd as $prod) {
		$asa_amt = $asa_tax = 0;
		$prod_code_skew = $prod['pcode'].$prod['pskew'];
		$skew_ppty = $prod['skew_ppty'];
		$prod_type = $prod['prod_type'];
		$pcode = $prod['pcode'];
		$pskew = $prod['pskew'];
		$qty = $prod['qty'];
		if($prod['internal_prod']=='4' and !($prod['pcode']=='308' and ($skew_ppty=='3' or $prod_type=='8'))) {
			$sql1 = get_prod_dtl_for_kit($prod['pcode'],$prod['pskew']);
			$sql_res = execute_my_query($sql1);
			if($row = mysqli_fetch_array($sql_res)) {
				$prod_code_skew = $row['pcode'];
				$skew_ppty = $row['GFT_SKEW_PROPERTY'];
				$prod_type = $row['GPM_PRODUCT_TYPE'];
				$pcode = $row['GSK_PRODUCT_CODE'];
				$pskew = $row['GSK_PRODUCT_CODE'];
				if($row['GFT_SKEW_PROPERTY']=='3'){
					$qty = $qty * (int)$row['GSK_PRODUCT_QTY'];
				}
			}
		}
		$asa_dtl = get_asa_dtls_for_pdf(array($skew_ppty),array($prod_type),array($pcode),
		array($pskew),array($prod['ucode']),array($prod_code_skew),array($qty),'3',$currency);
		if(isset($asa_dtl['asa_rows']) and count($asa_dtl['asa_rows'])>0) {
			$asa_tax = (float)str_replace(",","",$asa_dtl['ASA_GST_TAX_AMOUNT']);
			$asa_amt = (float)str_replace(",","",$asa_dtl['asa_rows'][0]['ASA_GQP_SELL_AMOUNT']);
			if($prod['skew_ppty']=='3') {
				$additional_asa_amt += $asa_amt;
				$additional_cnt += $prod['qty'];
			} else {
				if(in_array($prod['order_type'],array('2','3'))) {
					$perpetual_asa_amt += $asa_amt;
				} else {
					$prod_id = $prod['pcode']."-".$prod['pskew'];
					$ret_arr['add_on'][$prod_id]['asa_amt'] = $asa_amt;
					$ret_arr['add_on'][$prod_id]['qty'] = $prod['qty'];
					$ret_arr['add_on'][$prod_id]['display'] = $prod['display'];
				}
			}
			$total_tax += $asa_tax;
			$total_asa += $asa_amt + $asa_tax;
			$total_asa_no_tax += $asa_amt;
		}
	}
	$ret_arr['additional_client']['asa_amt'] = $additional_asa_amt;
	$ret_arr['additional_client']['cnt'] = $additional_cnt;
	$ret_arr['perpetual']['asa_amt'] = $perpetual_asa_amt;
	$ret_arr['asa_sell_rate'] = $total_asa_no_tax;
	$ret_arr['asa_tax'] = $total_tax;
	$ret_arr['total_asa'] = $total_asa;
	return $ret_arr;
}
/**
 * @param string $quotation_no
 * @param string $subsection_id
 * @param string $vertical_id
 * @param string $tax_mode
 * @param string $total_tax_per
 * @param string $currency
 * @param string $products_in_one_row
 * @return mixed[]
 */
function get_hq_proposal_commercials($quotation_no,$subsection_id,$vertical_id,$tax_mode,$total_tax_per,$currency='INR',$products_in_one_row='0') {
	$ret_arr = array();
	$conv = 1;
	$tax_included_str = '<br>[Exclusive of Taxes]';
	//setlocale(LC_MONETARY, 'en_IN');
	$format_type = "en_IN";
	$currencyType = "INR";
	if($currency!='INR') {
		//setlocale(LC_MONETARY, 'en_US');
		$format_type = "en_US";
		$currencyType = "USD";
		$conv = (int)get_samee_const("UNIV_PRICE_CONVERSION");
		$tax_included_str = '';
	}
	$fmt = new NumberFormatter($format_type, NumberFormatter::CURRENCY );	
	$commercials_html =<<<END
	<table border='1' width=90% style='border-collapse: collapse;'>
END;
	$success = false;
	$asa_amt = 0;
	$total_amt = get_single_value_from_single_table("round(gqh_order_amt)", "gft_quotation_hdr", "gqh_order_no", $quotation_no);
	$vertical_name = get_single_value_from_single_table("gtm_chain_name", "gft_vertical_master", "GTM_VERTICAL_CODE", $vertical_id);
	$locations_count 			 = get_location_counts($quotation_no);
	$warehouse_locations 		 = isset($locations_count['13']['count'])?intval($locations_count['13']['count']):0;
	$warehouse_product_type		 = isset($locations_count['13']['disp_name'])?$locations_count['13']['disp_name']:"";
	$manage_outlet_locations	 = isset($locations_count['12']['count'])?intval($locations_count['12']['count']):0;
	$manage_product_type		 = isset($locations_count['12']['disp_name'])?$locations_count['12']['disp_name']:"";
	$sell_outlet_locations		 = isset($locations_count['11']['count'])?intval($locations_count['11']['count']):0;
	$sell_product_type			 = isset($locations_count['11']['disp_name'])?$locations_count['11']['disp_name']:"aa";
	$outlet_cnt 				 = $locations_count['total'];
	$display_dtls = "";
	if($warehouse_locations>0) {
		$display_dtls .= " For $warehouse_locations $warehouse_product_type ";
	}
	if($sell_outlet_locations>0) {
		$display_dtls .= ($display_dtls!=''?($manage_outlet_locations>0?",":" and "):" For")." $sell_outlet_locations $sell_product_type outlet(s) ";
	}
	if($manage_outlet_locations>0) {
		$display_dtls .= ($display_dtls!=''?" and ":" For")." $manage_outlet_locations $manage_product_type outlet(s) ";
	}
	$asa_dtls = get_asa_dtls_for_quotation($quotation_no,$tax_mode);
	$quotation_prod_dtl = get_quotation_product_dtl($quotation_no,$tax_mode,false,true);
	$quote_dtls = array(0,0,0,0,0,0); // Kit & additional outlet,additional client,add-ons,total,tax,adjustment
	$total_rate_no_tax = 0.0;
	$add_on_dtls = array(); // pcode-pskew => array(qty,display_name,$sell_rate,tax_rate)
	$additional_clients = 0;
	$rate_without_adjustment = 0;
	for($i=0;$i<count($quotation_prod_dtl);$i++) {
		$is_add_on = false;
		$rate = round(($quotation_prod_dtl[$i]['sell_rate']+$quotation_prod_dtl[$i]['adj_amt'])*$quotation_prod_dtl[$i]['qty']);
		$rate_without_adjustment += (floatval($quotation_prod_dtl[$i]['sell_rate'])*floatval($quotation_prod_dtl[$i]['qty']));
		if(in_array($quotation_prod_dtl[$i]['order_type'],array('2','3')) and $quotation_prod_dtl[$i]['skew_ppty']!='3') {
			$quote_dtls[0] += $rate;
			$total_rate_no_tax += $rate;
		} else if($quotation_prod_dtl[$i]['skew_ppty']=='3') {
			$quote_dtls[1] += $rate;
			$total_rate_no_tax += $rate;
			$additional_clients += $quotation_prod_dtl[$i]['qty'];
		} else {
			$is_add_on = true;
			$quote_dtls[2] += $rate;
			$total_rate_no_tax += $rate;
			if(!in_array($quotation_prod_dtl[$i]['pcode']."-".$quotation_prod_dtl[$i]['pskew'],array_keys($add_on_dtls))) {
				$add_on_dtls[$quotation_prod_dtl[$i]['pcode']."-".$quotation_prod_dtl[$i]['pskew']] =
				array($quotation_prod_dtl[$i]['qty'],$quotation_prod_dtl[$i]['display'],
				    $quotation_prod_dtl[$i]['sell_rate']+$quotation_prod_dtl[$i]['adj_amt'],$quotation_prod_dtl[$i]['tax_rate'],
				    $quotation_prod_dtl[$i]['adj_amt']);
			}
		}
		if(!$is_add_on) {
		    $quote_dtls[4] += round(($quotation_prod_dtl[$i]['sell_rate'])*($quotation_prod_dtl[$i]['tax_rate']/100)*$quotation_prod_dtl[$i]['qty']);
		    $quote_dtls[3] += ($rate-$quotation_prod_dtl[$i]['adj_amt']*$quotation_prod_dtl[$i]['qty']);
		}
		$quote_dtls[5] += $quotation_prod_dtl[$i]['adj_amt']*$quotation_prod_dtl[$i]['qty'];
	}
	$service_rate = 0;
	if(in_array($subsection_id,array('15','27'))) {
    	$quotation_service_prod_dtl = get_quotation_product_dtl($quotation_no,$tax_mode,true);
    	for($j=0;$j<count($quotation_service_prod_dtl);$j++) {
    	    $service_hrs = ((isset($quotation_service_prod_dtl[$j]['hours']) and intval($quotation_service_prod_dtl[$j]['hours'])>0)?intval($quotation_service_prod_dtl[$j]['hours']):1);
    	    $service_rate += (floatval($quotation_service_prod_dtl[$j]['sell_rate'])*intval($quotation_service_prod_dtl[$j]['qty'])*intval($service_hrs));
    	}
	}
	foreach ($add_on_dtls as $k=>$d) {
		$quote_dtls[4] += round(($d[2]-$d[4])*($d[3]/100)*$d[0]);
		$quote_dtls[3] += round(($d[2]-$d[4])*$d[0]);
	}
	$asa_price = $asa_dtls['asa_sell_rate'];
	$asa_amt = $asa_dtls['total_asa'];
	$right_align = 'align="right"';
	$center_align = 'align="center"';
	switch ($subsection_id) {
		case '15':
			if($total_amt!='') {
				$commercials_html .= "<tr bgcolor='#99ccff'>";
				$commercials_html .= "<th>No. of Locations</th>";
				$commercials_html .= "<th>Software License Fee<br>[$currency]$tax_included_str</th>";
				if($service_rate>0) {
				    $commercials_html .= "<th>Service Fee<br>[$currency]$tax_included_str</th>";
				}
				$commercials_html .= "<th>ALR Charges<br>[$currency Per Year]<br>(Applicable from the 2nd year)$tax_included_str</th></tr>";
				$commercials_html .= "<tr><td $center_align>$outlet_cnt</td>";
				if(isset($quote_dtls[3]) and intval($quote_dtls[3])>0) {
				    $commercials_html .= "<td $center_align>".$fmt->formatCurrency(ceil((float)$quote_dtls[3]/$conv),$currencyType)."</td>";
				} else {
					$commercials_html .= "<td $center_align>".$fmt->formatCurrency(ceil((float)$rate_without_adjustment/$conv),$currencyType)."</td>";
				}
				if($service_rate>0) {
					$commercials_html .= "<td $center_align>".$fmt->formatCurrency(ceil((float)$service_rate/$conv),$currencyType)."</td>";
				}
				if($currency=='INR') {
					$commercials_html .= "<td $center_align>".$fmt->formatCurrency(round($asa_price),$currencyType)."</td>";
				} else {
					$commercials_html .= "<td $center_align>".$fmt->formatCurrency(round($asa_amt),$currencyType)."</td>";
				}
				$success = true;
			}
			break;
		case '16':
			$commercials_html .= "<tr bgcolor='#99ccff'>";
			$commercials_html .= "<th>Item Description</th>";
			$commercials_html .= "<th>Value [$currency]</th></tr>";
			$product_dtls_ui = '';
			$all_products_amt = 0.00; 
			if($quote_dtls[0]>0) {
			    if($products_in_one_row=='0') {
    				$commercials_html .= "<tr><td><strong>$vertical_name</strong><br>$display_dtls</td>";
					$commercials_html .= "<td $right_align>".$fmt->formatCurrency(ceil($quote_dtls[0]/$conv),$currencyType)."</td></tr>";
			    } else {
			        $product_dtls_ui .= "<strong>$vertical_name</strong>,<br>$display_dtls";
			        $all_products_amt += floatval($quote_dtls[0]/$conv);
			    }
			}
			if($quote_dtls[2]>0) {
				foreach ($add_on_dtls as $k=>$v) {
				    if($products_in_one_row=='0') {
    					$commercials_html .= "<tr><td>$v[0] $v[1]</td>";
						$commercials_html .= "<td $right_align>".$fmt->formatCurrency(ceil(round($v[2]*$v[0])/$conv),$currencyType)."</td>";
				    } else {
				        $product_dtls_ui .= (($product_dtls_ui!=''?',<br/>':'').$v[0]." Qty ".$v[1]);
				        $all_products_amt += floatval(($v[2]*$v[0])/$conv);
				    }
				}
			}
			if($quote_dtls[1]>0) {
			    if($products_in_one_row=='0') {
    				$commercials_html .= "<tr><td>$additional_clients Additional Clients license access</td>";
					$commercials_html .= "<td $right_align>".$fmt->formatCurrency(ceil($quote_dtls[1]/$conv),$currencyType)."</td></tr>";
			    } else {
			        $product_dtls_ui .= (($product_dtls_ui!=''?',<br/>':'')."$additional_clients Additional Clients license access");
			        $all_products_amt += floatval($quote_dtls[1]/$conv);
			    }
			}
			if($products_in_one_row=='1') {
				$commercials_html .= "<tr><td>$product_dtls_ui</td><td $right_align>".$fmt->formatCurrency(ceil($all_products_amt),$currencyType)."</td></tr>";
			}
			if($quote_dtls[5]>0) {
			    $commercials_html .= "<tr><td>Deducting Stand Alone Payment</td>";
				$commercials_html .= "<td $right_align>".$fmt->formatCurrency(round($quote_dtls[5]/$conv),$currencyType)."</td></tr>";
			}
			if($currency=='INR') {
			    if($products_in_one_row=='0' or $quote_dtls[5]>0) {
    				$commercials_html .= "<tr><td>Total License Value</td>";
					$commercials_html .= "<td $right_align>".$fmt->formatCurrency(ceil($quote_dtls[3]/$conv),$currencyType)."</td></tr>";
			    }
				$tax_str = "VAT 5% + Service Tax 15%";
				if($tax_mode=='4') {
					$tax_str = "GST - $total_tax_per %";
				}
				$commercials_html .= "<tr><td>Add:GST [$tax_str]</td>";
				$commercials_html .= "<td $right_align>".$fmt->formatCurrency(ceil($quote_dtls[4]/$conv),$currencyType)."</td></tr>";
			}
			$license_total_amt = ceil($quote_dtls[3]/$conv) + ceil($quote_dtls[4]/$conv);
			$commercials_html .= "<tr><td>Gross Software License Fee</td>";
			$commercials_html .= "<td $right_align>".$fmt->formatCurrency($license_total_amt,$currencyType)."</td></tr>";
			$success = true;
			break;
		case '17':
			$commercials_html .= "<tr bgcolor='#99ccff'>";
			$commercials_html .= "<th>Item Description</th>";
			$commercials_html .= "<th>ALR Charges [$currency per year]</th></tr>";
			$prod_dtls_ui = '';
			$total_alr_amt = 0;
			if((int)$asa_dtls['perpetual']['asa_amt']>0) {
			    if($products_in_one_row=='0') {
    				$commercials_html .= "<tr><td><strong>ALR for $vertical_name</strong>";
    				$commercials_html .= "<br>$display_dtls</td>";
					$commercials_html .= "<td $right_align>".$fmt->formatCurrency(round((int)$asa_dtls['perpetual']['asa_amt']),$currencyType)."</td></tr>";
			    } else {
			        $prod_dtls_ui .= (($prod_dtls_ui!=''?',<br/>':'')."<strong>$vertical_name</strong><br/>$display_dtls");
			        $total_alr_amt += floatval($asa_dtls['perpetual']['asa_amt']);
			    }
			}
			if(isset($asa_dtls['add_on'])) {
				foreach ($asa_dtls['add_on'] as $add_ons) {
					if($add_ons['asa_amt']>0) {
						$qty = $add_ons['qty'];
						$disp_name = $add_ons['display'];
						$asa_amt = $add_ons['asa_amt'];
						if($products_in_one_row=='0') {
    						$commercials_html .= "<tr><td>ALR for ".$qty." Qty ".$disp_name."</td>";
							$commercials_html .= "<td $right_align>".$fmt->formatCurrency(round($asa_amt),$currencyType)."</td></tr>";
						} else {
						    $prod_dtls_ui .= (($prod_dtls_ui!=''?',<br/>':'').$qty." Qty ".$disp_name);
						    $total_alr_amt += floatval($asa_amt);
						}
					}
				}
			}
			if((int)$asa_dtls['additional_client']['asa_amt']>0) {
			    if($products_in_one_row=='0') {
    			    $commercials_html .= "<tr><td>ALR for ".$asa_dtls['additional_client']['cnt']." Qty Additional clients</td>";
					$commercials_html .= "<td $right_align>".$fmt->formatCurrency(round($asa_dtls['additional_client']['asa_amt']),$currencyType)."</td></tr>";
			    } else {
			        $prod_dtls_ui .= (($prod_dtls_ui!=''?',<br/>':'').$asa_dtls['additional_client']['cnt']." Qty Additional clients");
			        $total_alr_amt += floatval($asa_dtls['additional_client']['asa_amt']);
			    }
			}
			if($products_in_one_row=='1' and $prod_dtls_ui!='') {
				$commercials_html .= "<tr><td>ALR for<br/>$prod_dtls_ui</td><td $right_align>".$fmt->formatCurrency(round($total_alr_amt),$currencyType)."</td></tr>";
			} else {
    			$commercials_html .= "<tr><td>Total ALR Amount</td>";
    			$asa_sell_rate = $asa_dtls['asa_sell_rate'];
				$commercials_html .= "<td $right_align>".$fmt->formatCurrency(round($asa_sell_rate),$currencyType)."</td></tr>";
			}
			if($currency=='INR') {
				$tax_str = "Service Tax - 15%";
				if($tax_mode=='4') {
					$tax_str = "GST - $total_tax_per%";
				}
				$commercials_html .= "<tr><td>$tax_str on ALR Amount</td>";
				$asa_tax = $asa_dtls['asa_tax'];
				$commercials_html .= "<td $right_align>".$fmt->formatCurrency(round($asa_tax),$currencyType)."</td></tr>";
				$commercials_html .= "<tr><td>Total ALR Charges (ALR Amount + GST)</td>";
				$total_asa = $asa_dtls['total_asa'];
				$commercials_html .= "<td $right_align>".$fmt->formatCurrency(round($total_asa),$currencyType)."</td></tr>";
			}
			$success = true;
			break;
		case '27':
			$commercials_html .= "<tr bgcolor='#99ccff'>";
			$commercials_html .= "<th>Item Description</th>";
			$commercials_html .= "<th>Value [$currency]</th></tr>";
			$total = $total_tax = 0;
			$adj_amt = 0.00;
			$rate_with_discount = 0;
			$prod_dtls_ui = '';
			$total_service_amt = 0;
			foreach ($quotation_service_prod_dtl as $prod_row) {
				if($prod_row['hours']!='0' and $prod_row['hours']!='') {
				    if($products_in_one_row=='0') {
				        $commercials_html .= "<tr><td>".$prod_row['display']." for ". intval($prod_row['hours'])*intval($prod_row['qty'])." hours</td>";
				    } else {
				        $prod_dtls_ui .= (($prod_dtls_ui!=''?',<br/>':'').$prod_row['display']." for ". intval($prod_row['hours'])*intval($prod_row['qty'])." hours");
				    }
					$sell_rate = intval($prod_row['sell_rate']+$prod_row['adj_amt'])*intval($prod_row['qty'])*intval($prod_row['hours']);
					$rate_with_discount = intval($prod_row['sell_rate'])*intval($prod_row['qty'])*intval($prod_row['hours']);
				} else {
				    if($products_in_one_row=='0') {
					   $commercials_html .= "<tr><td>".$prod_row['qty']." Qty ".$prod_row['display']."</td>";
				    } else {
				        $prod_dtls_ui .= (($prod_dtls_ui!=''?',<br/>':'').$prod_row['qty']." Qty ".$prod_row['display']);
				    }
					$sell_rate = intval($prod_row['sell_rate']+$prod_row['adj_amt'])*intval($prod_row['qty']);
					$rate_with_discount = intval($prod_row['sell_rate'])*intval($prod_row['qty']);
				}
				$total += intval(round($sell_rate));
				$tax = round($rate_with_discount*$prod_row['tax_rate']/100);
				$total_tax += intval($tax);
				if($products_in_one_row=='0') {
					$commercials_html .= "<td $right_align>".$fmt->formatCurrency(ceil($sell_rate/$conv),$currencyType)."</td></tr>";
				} else {
				    $total_service_amt += floatval($sell_rate/$conv);
				}
				$adj_amt += $prod_row['adj_amt']*intval($prod_row['qty'])*(intval($prod_row['hours'])?intval($prod_row['hours']):1);
			}
			if($products_in_one_row=='1' and $prod_dtls_ui!='') {
			    $commercials_html .= "<tr><td>$prod_dtls_ui</td>".
									 "<td $right_align>".$fmt->formatCurrency(ceil($total_service_amt),$currencyType)."</td></tr>";
			}
			if($adj_amt>0) {
			    $total -= $adj_amt;
			    $commercials_html .= "<tr><td>Deducting Stand Alone Payment</td>";
				$commercials_html .= "<td $right_align>".$fmt->formatCurrency(ceil($adj_amt/$conv),$currencyType)."</td></tr>";
			}
			if($products_in_one_row=='0' or $adj_amt>0) {
    			$commercials_html .= "<tr><td>Total </td>";
				$commercials_html .= "<td $right_align>".$fmt->formatCurrency(ceil($total/$conv),$currencyType)."</td></tr>";
			}
			if($currency=='INR') {
				$tax_str = "Service Tax - ".$prod_row['tax_rate'];
				if($tax_mode=='4') {
					$tax_str = "GST - $total_tax_per";
				}
				$commercials_html .= "<tr><td>Add $tax_str %</td>";
				$commercials_html .= "<td $right_align>".$fmt->formatCurrency(ceil($total_tax/$conv),$currencyType)."</td></tr>";
				$commercials_html .= "<tr><td>Total for Services</td>";
				$commercials_html .= "<td $right_align>".$fmt->formatCurrency(ceil(($total+$total_tax)/$conv),$currencyType)."</td></tr>";
			}
			$success = true;
			break;
		default: 
			die("Invalid section ID ($subsection_id) for commercials.");
	}
	$commercials_html .= "</table>";
	$ret_arr['success'] 		= $success;
	$ret_arr['commercial_html'] = $commercials_html;
	return $ret_arr;
}
/**
 * @param string $receive_param
 * @param string $message
 * @param int $error_code
 * @param string $type
 * @param int $userId
 *
 * @return void
 */
function send_mobile_error_msg($receive_param, $message,$error_code,$type='',$userId=0) {
	sendErrorWithCode($receive_param, $message,$error_code,$type,$userId);
}
/**
 * @param string $lead_code
 * @param boolean $check_country
 * @return mixed[string]
 */
function check_valid_state_code_gstin($lead_code,$check_country=true) {
	$country_cond = '';
	if($check_country) {
		$country_cond = " and glh_country='India' ";
	}
	$chk_qry = execute_my_query(" select gpm_gst_state_code,GLE_GST_NO,GLE_GST_ELIGIBLE from gft_lead_hdr ".
								" join gft_lead_hdr_ext on (gle_lead_code=glh_lead_code) ".
								" join gft_political_map_master on (glh_cust_statecode=gpm_map_name and gpm_map_type='S') ".
								" where glh_lead_code='$lead_code' $country_cond");
	$state_code = $gstin = '';
	$valid_state = $valid_gstin = $gstin_empty = false;
	$cust_country = get_single_value_from_single_table("glh_country", "gft_lead_hdr", "glh_lead_code", $lead_code);
	if(strcasecmp($cust_country,'India')==0) {
		if(mysqli_num_rows($chk_qry)==1) {
			$row = mysqli_fetch_array($chk_qry);
			$state_code = $row['gpm_gst_state_code'];
			if(is_numeric($state_code) and $state_code!='' and $state_code!='0') {
				$valid_state = true;
			}
			if($row['GLE_GST_ELIGIBLE']!='2') {
				$gstin = $row['GLE_GST_NO'];
				$gstin_state_code = substr(trim($gstin),0,2);
				if($gstin=='' or (int)$gstin_state_code!=(int)$state_code) {
					$valid_gstin = false;
					if($gstin=='' ) {
						$gstin_empty = true;
					}
				} else {
					$valid_gstin = true;
				}
			} else {
				$valid_gstin = true;
			}
		}
	} else {
		$valid_state = $valid_gstin = true;
	}
	return array("state_code"=>$state_code,"valid_state"=>$valid_state,"gstin"=>$gstin,"valid_gstin"=>$valid_gstin,"gstin_empty"=>$gstin_empty);
}
/**
 * @param string $lead_code
 * @param boolean $close_window
 * @param boolean $for_mobile_app
 * @param string $receive_arg
 * @param boolean $show_alert
 * @return boolean
 */
function validate_state_code_gstin($lead_code,$close_window=false,$for_mobile_app=false,$receive_arg='',$show_alert=true) {
	global $uid;
	return true;
	$state_code_dtl = check_valid_state_code_gstin($lead_code);
	$alert_msg = '';
	$lead_type = 'customer';
	$gstin_update_step = "Please inform customer to update their GSTIN registration stauts i.e Registered or Not registered in myGoFrugal app.";
	if((int)$uid>=7000 || get_lead_type_for_lead_code($lead_code)=='2') {
		$lead_type = 'partner';
		$gstin_update_step = "Please inform partner to update their GSTIN registration stauts i.e Registered or Not registered in myDelight app.";
	}
	$gstin_not_available = "The GSTIN for this $lead_type is not available.";
	if((int)$uid>=7000) {
		$gstin_not_available = "Your GSTIN is not available in SAM.";
		$gstin_update_step = "Please update the GSTIN details from myDelight.";
	}
	if(!/*.(boolean).*/$state_code_dtl['valid_state']) {
		$alert_msg = "The state code of this $lead_type is not available. Please update the $lead_type details with correct state information.";
	} else if(!/*.(boolean).*/$state_code_dtl['valid_gstin']) {
		$alert_msg = "There is a mismatch in State code of $lead_type GSTIN and $lead_type State Name. Please correct the same and proceed. $lead_type GSTIN is: ".$state_code_dtl['gstin'].", State code: ".$state_code_dtl['state_code'];
		if(/*.(boolean).*/$state_code_dtl['gstin_empty']) {
			$alert_msg = "$gstin_not_available $gstin_update_step";
		}
	}
	if($alert_msg!='') {
		if($show_alert) {
			if($for_mobile_app) {
				sendErrorWithCode($receive_arg,$alert_msg, HttpStatusCode::BAD_REQUEST);
			} else {
				show_alert_and_close($alert_msg,$close_window);
			}
		}
		return false;
	}
	return true;
}
/**
 * @param string $lead_code
 * @param string $currency_type
 * @param string $purpose
 * @param string $type
 *
 * @return string
 */
function get_encrypted_cust_info($lead_code,$currency_type, $purpose,$type){
	global $secret;
	if($currency_type!="INR"){
		return "";
	}
	$value_arr['cust_id']="$lead_code";
	$value_arr['purpose']="$purpose";
	$value = json_encode($value_arr);
	$encription_value = lic_encrypt($value,$secret);
	$link =	get_samee_const("Customer_Details_Update_Link")."?".$encription_value;
	if($type=="Yes"){
		return "To update your GSTIN details, "."<a href='$link#Yes' target='_blank'>Click Here</a>";
	}else{
		return "And not having GSTIN, "."<a href='$link#No' target='_blank'>Click Here</a>";
	}
	
}
/**
 * @param string $lead_code
 * @param string $action_php
 * @param string[int] $contact_types
 * @param boolean $valid
 * @param string $submit_button_label
 * @param string $onsubmit_js
 * @return string
 */
function get_customer_contacts_ui($lead_code,$action_php,$contact_types=array('1'),$valid=true,$submit_button_label='Submit',$onsubmit_js='eval_contacts(); return false;') {
	$ui = "<form name='contacts_select' id='contacts_select' method='POST' action='$action_php'>";
	$ui .= "<input type='hidden' name='notify_only' value='yes'>";
	$ui .= "<input type='hidden' name='cust_id' value='$lead_code'>";
	$table_style = "border: 1px solid black;";
	$ui .= "<table width=40% class='FormBorder1' style='border: 1px solid black; border-collapse: collapse;'>";
	$select_all = "<input type='checkbox' name='select_all_contacts' id='select_all_contacts'>";
	$ui .= "<thead><tr><th style='text-align:left;'>$select_all</th><th>S.no</th><th>Name</th><th>Designation</th><th>Contact No.</th></thead>";
	$other_conditions = '';
	if($valid) {
		$other_conditions =  " and GCC_VALID='Y'";
	}
	$contacts_qry = " select gcc_id,gcc_contact_name,GCD_NAME,gcc_contact_no from gft_customer_contact_dtl ".
					" join gft_contact_designation_master on (GCD_CODE=gcc_designation) ".
					" where gcc_lead_code='$lead_code' and gcc_contact_type in (".implode(",",$contact_types).") ".
					" $other_conditions order by gcc_contact_name ";
	$contacts_res = execute_my_query($contacts_qry);
	if(mysqli_num_rows($contacts_res)>0) {
		$ui .= "</tbody>";
		$i = 0;
		while($row = mysqli_fetch_array($contacts_res)) {
			$i++;
			$id = $row['gcc_id'];
			$contact_no = $row['gcc_contact_no'];
			$ui .= "<tr class='content'>";
			$ui .= "<td class='content'><input class='contact_selection' type='checkbox' name='contact[$i]' id='contact$i' value='$id'></td>";
			$ui .= "<td class='content'>$i</td>";
			$ui .= "<td class='content'>".$row['gcc_contact_name']."</td>";
			$ui .= "<td class='content'>".$row['GCD_NAME']."</td>";
			$ui .= "<td class='content'>$contact_no<input type='hidden' id='num$i' value='$contact_no'></td>";
			$ui .= "</tr>";
		}
		$ui .= "</tbody>";
	}
	$ui .= " <tfoot><tr><td colspan='5' style='text-align:center;'><input type='hidden' name='numbers' id='numbers' value=''>".
		   " <input type='button' value='$submit_button_label' onclick='javascript:$onsubmit_js'></td></tr></tfoot> ";
	$ui .= "</table></form>";
	$ui .= "<style>table th{ $table_style } .content { $table_style }</style>";
	$js =<<<JS
		<script type='text/javascript'>
			var jq = jQuery.noConflict();
			jq('document').ready(function() {
				jq('#select_all_contacts').change(function() {
					if(jq('input[id=select_all_contacts]:checked').length>0) {
						jq('.contact_selection').attr('checked',true);
					} else {
						jq('.contact_selection').attr('checked',false);
					}
				});
			});
		</script>
JS;
	$ui .= $js;
	return $ui;
}
/**
 * @param string $content
 * @param string $purpose
 * @param string $file_name
 * @param string $create_in_folder
 * @return string
 */
function write_html_file_for_pdf($content,$purpose,$file_name,$create_in_folder='') {
	global $pagebreak,$pagebreakpdf,$attach_path;
	$content=(string)str_replace('\"','"', $content);
	$content=(string)str_replace('<tbody>','', $content);
	$content=(string)str_replace('</tbody>','', $content);
	$content=(string)str_replace($pagebreak,$pagebreakpdf,$content);
	$folder_name = get_folder_name_for_purpose($purpose);
	if($create_in_folder!='') {
		$folder_name .= "/$create_in_folder";
	}
	$t=write_to_file($folder_name,$content,$file_name,null);
	if($t) {
		return $attach_path."/".$folder_name."/".$file_name;
	} else {
		return "";
	}
}
/**
 * @param string $html_file_path
 * @param string $title_page_path
 * @param boolean $add_toc
 * @param string $hdr_html_path
 * 
 * @return void
 */
function generate_pdf($html_file_path,$title_page_path='',$add_toc=false,$hdr_html_path='') {
	$file_path=realpath($html_file_path);
	$dirs = explode("/",$file_path);
	$html_fname = end($dirs);
	array_pop($dirs);
	$f_name=(string)str_replace("html","pdf",$html_fname);
	$fr_name=implode("/",$dirs).'/'.$f_name;
	$filename=$file_path;
	$switches = "";
	if($title_page_path!='') {
		$title_page_path = realpath($title_page_path);
		$switches .= " cover $title_page_path ";
	}
	if($add_toc) {
		$switches .= " toc ";
	}
	if($hdr_html_path!='') {
		$hdr_html_path = realpath($hdr_html_path);
		$switches .= " --header-html $hdr_html_path ";
	}
	if(file_exists($fr_name)){
		unlink($fr_name);
	}
	system("wkhtmltopdf -q -O Portrait -T 20mm $switches $filename $fr_name");
}
/**
 * @param string $check_against
 * @param string $id
 * @return string
 */
function get_lead_code_from_hdr($check_against,$id) {
	$lead_code = '';
	switch ($check_against) {
		case 'invoice':
			$lead_code = get_single_value_from_single_table("gih_lead_code", "gft_invoice_hdr", "gih_invoice_id", "$id");
			break;
		case 'quotation':
			$lead_code = get_single_value_from_single_table("gqh_lead_code", "gft_quotation_hdr", "gqh_order_no", "$id");
			break;
		case 'proforma':
			$lead_code = get_single_value_from_single_table("gph_lead_code", "gft_proforma_hdr", "gph_order_no", "$id");
			break;
		case 'order':
			$lead_code = get_single_value_from_single_table("god_lead_code", "gft_order_hdr", "god_order_no", "$id");
			break;
		default: return '';
	}
	return $lead_code;
}
/**
 * @param string $invoice_id
 * @param string $purpose
 * @param string $lead_type
 * 
 * @return string[int]
 */
function get_invoice_item_dtl_html($invoice_id,$purpose='invoice', $lead_type='') {
	$order_product_fields = ", GOP_CGST_PER cgst_per, GOP_CGST_AMT cgst_amt, GOP_SGST_PER sgst_per, GOP_SGST_AMT sgst_amt, ".
							" GOP_IGST_PER igst_per, GOP_IGST_AMT igst_amt ";
	$replace_str = 'GIP';
	$fields = " ip.GIP_ORDER_NO ord_no, ip.gip_product_code p_code, ip.gip_product_skew p_skew,ip.gip_rate rate, ip.gip_qty qty, ".
			  " ip.gip_amount amt, ip.gip_list_prize list_pricing, GOP_DISCOUNT_AMT discount_amount, ip.GIP_TAX_RATE tax_rate,". 
			  " ip.GIP_TAX_AMT tax_amt, if(GOD_REASON_FOR_DISCOUNT=4,CONCAT(GRD_REASON,'-',GOD_REASON_FOR_DISCOUNT_DTL),GRD_REASON) discount_reason, ip.GIP_SERTAX_RATE sertax_rate, ".
			  " ip.GIP_SERTAX_AMT sertax_amt,ip.GIP_COUPON_HRS coupon_hrs,god_tax_mode tax_mode,". 
	          " GOP_ADJ_DISCOUNT adj_disc, GAM_NAME adjustment_label, GOD_SINGLE_ROW_PDF pdf_single_row, GSR_NAME renewal_type ";
	$joins = " FROM gft_invoice_product_dtl ip ".
			" join gft_product_master pm on (ip.gip_product_code=pm.gpm_product_code and ip.gip_product_skew=pm.gpm_product_skew) ".
			" join gft_order_product_dtl on (ip.gip_order_no=gop_order_no and ip.gip_product_code=gop_product_code and ip.gip_product_skew=gop_product_skew ) ".
			" join gft_order_hdr on (gop_order_no=god_order_no) join gft_lead_hdr on (god_lead_code=glh_lead_code) ". 
			" left join gft_reason_for_discount_master ON(GRD_REASON_CODE=GOD_REASON_FOR_DISCOUNT AND GOD_REASON_FOR_DISCOUNT>0)". 
	        " left join gft_adjustment_type_master ON(GAM_ID=GOD_DISCOUNT_TYPE)".
	        " left join gft_subscription_renewal_type_master ON(GSR_ID=pm.GPM_SUBSCRIPTION_PERIOD_TYPE)";
	$print_order = " GOP_PRINT_ORDER ";
	$id_col_name = "GIP_INVOICE_ID";
	$sell_amt_cond = " and gip_amount>0 ";
	$last_cols = " ,'' as custom_skews ";
	$single_row_prods = 0;
	$quote_proforma_no = $invoice_id;
	if($purpose=='quotation') {
		$sell_amt_cond = "";
		$replace_str = 'GQP';
		$fields = " ip.GQP_ORDER_NO ord_no, ip.gqp_product_code p_code, ip.gqp_product_skew p_skew,(ip.gqp_sell_rate+GQP_ADJ_DISCOUNT) rate, ".
				  " ip.gqp_qty qty, ip.GQP_SELL_AMT amt, ip.GQP_LIST_PRICE list_pricing, ip.GQP_DISCOUNT_AMT discount_amount,". 
				  " ip.GQP_TAX_RATE tax_rate, ip.GQP_TAX_AMT tax_amt, if(GQH_REASON_FOR_DISCOUNT=4,CONCAT(GRD_REASON,'-',GQH_REASON_FOR_DISCOUNT_DTL),GRD_REASON) discount_reason, ".
				  " ip.GQP_SER_TAX_RATE sertax_rate,ip.GQP_SER_TAX_AMT sertax_amt,ip.GQP_COUPON_HOUR coupon_hrs, ".
				  " GQH_TAX_MODE tax_mode,GQP_ADJ_DISCOUNT adj_disc, '' adjustment_label, gqh_single_row_pdf pdf_single_row, GSR_NAME renewal_type  ";
		$joins = " FROM gft_quotation_product_dtl ip ".
				" join gft_product_master pm on (ip.gqp_product_code=pm.gpm_product_code and ip.gqp_product_skew=pm.gpm_product_skew) ".
				" join gft_quotation_hdr on (gqh_order_no=ip.gqp_order_no) join gft_lead_hdr on (glh_lead_code=gqh_lead_code) ". 
		        " left join gft_reason_for_discount_master ON(GRD_REASON_CODE=GQH_REASON_FOR_DISCOUNT AND GQH_REASON_FOR_DISCOUNT>0)".
		        " left join gft_subscription_renewal_type_master ON(GSR_ID=pm.GPM_SUBSCRIPTION_PERIOD_TYPE)";
		$print_order = "GQP_PRINT_ORDER";
		$id_col_name = "gqp_order_no";
	} else if($purpose=='proforma') {
		$sell_amt_cond = "";
		$replace_str = 'GPP';
		$last_cols = ",GPP_CUSTOM_SKEWS as custom_skews ";
		$fields = " ip.GPP_ORDER_NO ord_no, ip.gpp_product_code p_code, ip.gpp_product_skew p_skew,ip.gpp_sell_rate rate, ".
				  " ip.gpp_qty qty, ip.GPP_SELL_AMT amt, ip.GPP_LIST_PRICE as list_pricing, ip.GPP_DISCOUNT_AMT discount_amount,". 
				  " if(GPH_REASON_FOR_DISCOUNT=4,CONCAT(GRD_REASON,'-',GPH_REASON_FOR_DISCOUNT_DTL),GRD_REASON) discount_reason, ip.GPP_TAX_RATE tax_rate, ip.GPP_TAX_AMT tax_amt, ".
				  " ip.GPP_SER_TAX_RATE sertax_rate,ip.GPP_SER_TAX_AMT sertax_amt,ip.GPP_COUPON_HOUR coupon_hrs, ".
				  " GPH_TAX_MODE tax_mode, '0' adj_disc, '' adjustment_label,gph_single_row_pdf pdf_single_row, GSR_NAME renewal_type  ";
		$joins = " FROM gft_proforma_product_dtl ip ".
				" join gft_product_master pm on (ip.gpp_product_code=pm.gpm_product_code and ip.gpp_product_skew=pm.gpm_product_skew) ".
				" join gft_proforma_hdr on (gph_order_no=ip.gpp_order_no) join gft_lead_hdr on (gph_lead_code=glh_lead_code) ".
		        " left join gft_reason_for_discount_master ON(GRD_REASON_CODE=GPH_REASON_FOR_DISCOUNT AND GPH_REASON_FOR_DISCOUNT>0)".
		        " left join gft_subscription_renewal_type_master ON(GSR_ID=pm.GPM_SUBSCRIPTION_PERIOD_TYPE)";
		$print_order = "GPP_PRINT_ORDER";
		$id_col_name = "gpp_order_no";
	}
	$gst_cols = str_replace('GOP', $replace_str, $order_product_fields);
	$lead_code = get_lead_code_from_hdr($purpose, $invoice_id);
	$country = get_single_value_from_single_table('glh_country', 'gft_lead_hdr', 'glh_lead_code', "$lead_code");
	$same_state = /*.(boolean).*/is_same_state($lead_code);
	$vert_column = "GLH_VERTICAL_CODE";
	if($purpose=='invoice'){
	    $vque = " select lh.GLH_VERTICAL_CODE from gft_invoice_product_dtl ".
	            " join gft_order_hdr on (GOD_ORDER_NO=GIP_ORDER_NO) ".
	            " join gft_cp_order_dtl on (GCO_ORDER_NO=GOD_ORDER_NO) ".
	            " join gft_lead_hdr part on (part.GLH_LEAD_CODE=GOD_LEAD_CODE and part.GLH_LEAD_TYPE=2) ".
	            " join gft_lead_hdr lh on (lh.GLH_LEAD_CODE=GCO_CUST_CODE) ".
	            " where gip_invoice_id='$invoice_id' group by GOD_ORDER_NO ";
	    $vres = execute_my_query($vque);
	    if($vrow = mysqli_fetch_array($vres)){
	        $vert_column = $vrow['GLH_VERTICAL_CODE'];
	    }
	}
	$query= " SELECT $fields$gst_cols,GFT_SKEW_PROPERTY,pfm.GPM_PRODUCT_NAME,if(GPB_NAME is null,pm.GPM_SKEW_DESC,concat(GPB_NAME,' ',pm.GPM_SKEW_DESC)) GPM_SKEW_DESC,GHM_CODE,pfm.GPM_IS_INTERNAL_PRODUCT, ".
			" pm.gpm_product_code,pm.gpm_product_skew,pm.gpm_product_type,concat(pm.gpm_product_code,pm.gpm_product_skew) pcode,".
			" concat(pm.GFT_HIGHER_PCODE, pm.GFT_HIGHER_SKEW) ucode $last_cols ".
			" $joins left join gft_hsn_vs_tax_master on (GHT_ID=GPM_TAX_ID) ".
			" left join gft_product_hsn_master on (GHT_HSN_ID=GHM_ID) ".
			" join gft_product_family_master pfm on (pm.gpm_product_code=pfm.gpm_product_code) " .
			" join gft_product_group_master on (GPG_PRODUCT_FAMILY_CODE=GPM_HEAD_FAMILY and GPG_SKEW=substr(pm.gpm_product_skew,1,4)) ".
			" left join gft_brand_product_mapping on (GBP_VERTICAL=$vert_column and GBP_PRODUCT=concat(pm.GPM_PRODUCT_CODE,'-',GPG_SKEW) and GBP_EDITION=pm.GPM_PRODUCT_TYPE and GBP_STATUS=1) ".
			" left join gft_product_brand_master on (GPB_ID=GBP_BRAND_ID) ".
			" WHERE ip.$id_col_name='$invoice_id' $sell_amt_cond group by ip.$id_col_name, ip.".$replace_str."_ORDER_NO, ".
			" ip.".$replace_str."_product_code, ip.".$replace_str."_product_skew order by $print_order ";
	$result=execute_my_query($query);
	$ui_arr = /*.(string[int]).*/array();
	$html = "";
	$sl = 0;
	$rate_tot = $qty_tot = $val_tot = $cgst_tot = $sgst_tot = $igst_tot = $net_tot = 0.0;
	$total_discount_amount = 0;
	$skew_property = $qty_arr = $product_type = $pcode_arr = $pskew_arr = $up_product_code_arr = $product_code_arr = /*.(string[int]).*/array();
	$right_align = 'align=right';
	$merge_client = false;
	$ord_no = '';
	$discount_reason = "Discount ";
	if(mysqli_num_rows($result)>0) {
		$ai = 0;
		$inv_discount_tot = 0;
		$tax_dtls = array();
		$single_row_prod_names = '';
		$taxhdrs = '';
		$subrow = '';
		$html .= "<table border='1' style='border-collapse: collapse;' width='100%'><thead><tr>";
		$print_hdr = true;
		while($row = mysqli_fetch_array($result)) {
		    $single_row_prods = $row['pdf_single_row'];
		    if($print_hdr) {
		        if($single_row_prods=='0') {
		            $html .=<<<END
<th style='width: 10%;'>SAC</th>
<th style='width: 50%;'>Name of Product and/or Service</th>
<th style='width: 15%;'>Rate</th>
<th style='width:  5%;'>Qty</th>
<th style='width: 25%;'>Value</th>
</tr></thead><tbody>
END;
                } else {
		          $html .=<<<END
<th style='width: 15%;'>SAC</th>
<th style='width: 50%;'>Name of Product and/or Service</th>
<th style='width: 35%;'>Value</th>
</tr></thead><tbody>
END;
                }
		    }
		    $print_hdr = false;
			$ord_no = $row['ord_no'];
			$cgst_per = $row['cgst_per'];
			$sgst_per = $row['sgst_per'];
			$igst_per = $row['igst_per'];
			if($cgst_per>0.0) {
				if(!isset($tax_dtls["CGST-$cgst_per"])) {
					$tax_dtls["CGST-$cgst_per"] = $row['cgst_amt'];
				} else {
					$tax_dtls["CGST-$cgst_per"] += $row['cgst_amt'];
				}
			}
			if($sgst_per>0.0) {
				if(!isset($tax_dtls["SGST-$sgst_per"])) {
					$tax_dtls["SGST-$sgst_per"] = $row['sgst_amt'];
				} else {
					$tax_dtls["SGST-$sgst_per"] += $row['sgst_amt'];
				}
			}
			if($igst_per>0.0) {
				if(!isset($tax_dtls["IGST-$igst_per"])) {
					$tax_dtls["IGST-$igst_per"] = $row['igst_amt'];
				} else {
					$tax_dtls["IGST-$igst_per"] += $row['igst_amt'];
				}
			}
			$sl++;
			$tax_vals = '';
			$coupon_hrs_qty = 1;
			if((int)$row['coupon_hrs']>0) {
			    $hrs_disp = "($row[coupon_hrs] ".($row['renewal_type']!=''?$row['renewal_type']:'hrs').")";
				$coupon_hrs_qty = (int)$row['coupon_hrs'];
			} else {
				$hrs_disp = "";
			}
			$curr_rate = (float)$row['rate'] * (((int)$row['coupon_hrs']>0)?(int)$row['coupon_hrs']:1);			
			$qty_tot  += (float)$row['qty'];
			$val = ($row['qty']*(((int)$row['coupon_hrs']>0)?(int)$row['coupon_hrs']:1))*(float)$row['rate'];
			if($lead_type == '1'){
			    $list_price = (float)($row['rate']>$row['list_pricing']?$row['rate']:$row['list_pricing']);
			    $curr_rate = $list_price * (((int)$row['coupon_hrs']>0)?(int)$row['coupon_hrs']:1);
			    $val = ($row['qty']*(((int)$row['coupon_hrs']>0)?(int)$row['coupon_hrs']:1))*$list_price;
			    $discount_amount = $row['discount_amount']>0?floatval($row['discount_amount'])*($row['qty'])*(((int)$row['coupon_hrs']>0)?(int)$row['coupon_hrs']:1):0.0;
			    $total_discount_amount = $total_discount_amount + $discount_amount;
			    $discount_reason = $row['discount_reason'];
			}
			$rate_tot += ($curr_rate);
			$val_tot  += $val;
			$cgst_tot += (float)$row['cgst_amt'];
			$sgst_tot += (float)$row['sgst_amt'];
			$igst_tot += (float)$row['igst_amt'];
			$val_disp = number_format($val,2,".","");
			$rate_disp = number_format($curr_rate,2,".","");
			$net_disp = number_format((float)$row['amt'],2,".","");
			$inv_discount_tot += $row['adj_disc']*$row['qty']*(((int)$row['coupon_hrs']>0)?(int)$row['coupon_hrs']:1);
			$hsn = $row['GHM_CODE'];
			$custom_skews = $row['custom_skews'];
			if( ($row['GPM_IS_INTERNAL_PRODUCT']=='4') && (strlen($custom_skews) > 1) ){
				$cl_skew_arr = explode("**", $custom_skews);
				foreach ($cl_skew_arr as $temp_val){
					$temp_arr = explode("-", $temp_val);
					if(count($temp_arr) > 3){
						$cl_pcode 	= $temp_arr[0];
						$cl_pskew 	= $temp_arr[1];
						$cl_qty	  	= $temp_arr[2];
						$cl_price 	= $temp_arr[3];
						$one_price	= round($cl_price / $cl_qty,2);
						$pm_dtl 	= get_product_master_dtl($cl_pcode, $cl_pskew);
						$skew_desc 	= isset($pm_dtl[3])?$pm_dtl[3]:'';
						$skew_desc .= " ALR";
						$cl_cgst_per = $row['cgst_per'];
						$cl_sgst_per = $row['sgst_per'];
						$cl_igst_per = $row['igst_per'];
						$cl_cgst_amt = round($cl_price * $cl_cgst_per/100,2);
						$cl_sgst_amt = round($cl_price * $cl_sgst_per/100,2);
						$cl_igst_amt = round($cl_price * $cl_igst_per/100,2);
						$cl_net_amt = $cl_price + $cl_cgst_amt + $cl_sgst_amt + $cl_igst_amt;
						if($row['tax_mode']!='3') {
							if($same_state){
								$tax_vals ="<td>$cl_cgst_per</td><td $right_align>$cl_cgst_amt</td>".
											"<td>$cl_sgst_per</td><td $right_align>$cl_sgst_amt</td>";
							}else{
								$tax_vals ="<td>$cl_igst_per</td><td $right_align>$cl_igst_amt</td>";
							}
						}
						$net_tot  += $cl_net_amt;
						if($single_row_prods=='0') {
						  $html .=<<<END
						<tr>
							<td align=center>$hsn</td>
							<td>$skew_desc</td>
							<td $right_align>$one_price</td>
							<td $right_align>$cl_qty</td>
							<td $right_align>$cl_price</td>
						</tr>
END;
						} else {
						    $single_row_prod_names .= (($single_row_prod_names!=''?',<br/>':'').$row['qty']." Qty $skew_desc");
						}
					}
				}
			}else{
				$net_tot  += (float)$row['amt'];
				$cl_disp = "";
				if( (strpos($custom_skews,"(")===0) || (strpos($custom_skews,"qty for")>0) ){
				    $cl_disp = "<br>$custom_skews";
				}
				if($single_row_prods=='0') {
    				$html .=<<<END
<tr>
	<td align=center>$hsn</td>
	<td>$row[GPM_SKEW_DESC] $hrs_disp $cl_disp</td>
	<td $right_align>$rate_disp</td>
	<td $right_align>$row[qty]</td>
	<td $right_align>$val_disp</td>
</tr>
END;
				} else {
				    $single_row_prod_names .= (($single_row_prod_names!=''?',<br/>':'').$row['qty']." Qty ".$row['GPM_SKEW_DESC']." $hrs_disp $cl_disp");
				}
			}
			$gpm_prod_code = $row['gpm_product_code'];
			$gpm_prod_skew = $row['gpm_product_skew'];
			if( ($row['GPM_IS_INTERNAL_PRODUCT']=='4') && !(($gpm_prod_code=='308') && (($row['GFT_SKEW_PROPERTY']=='3') || ($row['gpm_product_type']=='8'))) ){
				$sql1 = get_prod_dtl_for_kit($gpm_prod_code,$gpm_prod_skew);
				$sq_res1 = execute_my_query($sql1);
				while($sq_row1 = mysqli_fetch_array($sq_res1)){
					$qty_val = (int)$row['qty'];
					$gsk_product_qty = (int)$sq_row1['GSK_PRODUCT_QTY'];
					if($sq_row1['GFT_SKEW_PROPERTY']=='3'){
						$qty_val = $qty_val * $gsk_product_qty;
					}
					$array_index = array_search($sq_row1['pcode'], $product_code_arr);
					if($array_index!==false){
						$qty_arr[$array_index] += $qty_val;						 
					}else{
						$pcode_arr[$ai] = $sq_row1['GSK_PRODUCT_CODE'];
						$pskew_arr[$ai] = $sq_row1['GSK_PRODUCT_SKEW'];
						$product_type[$ai] = $sq_row1['GPM_PRODUCT_TYPE'];
						$skew_property[$ai] = $sq_row1['GFT_SKEW_PROPERTY'];
						$up_product_code_arr[$ai] = $sq_row1['ucode'];
						$product_code_arr[$ai] = $sq_row1['pcode'];
						$qty_arr[$ai] = $qty_val;
						$ai++;
						if( ($gpm_prod_code=='309') && ($sq_row1['GFT_SKEW_PROPERTY']=='3') ){
							$merge_client = true;
						}
					}
				}
			}else{
				$pcode_arr[$ai] = $row['gpm_product_code'];
				$pskew_arr[$ai] = $row['gpm_product_skew'];
				$product_type[$ai] = $row['gpm_product_type'];
				$skew_property[$ai] = $row['GFT_SKEW_PROPERTY'];
				$up_product_code_arr[$ai] = $row['ucode'];
				$product_code_arr[$ai] = $row['pcode'];
				$qty_arr[$ai] = $row['qty'];
				$ai++;
			}
		}
		$val_tot_disp = number_format($val_tot,2,".","");
		$totals_colspan = 4;
		if($single_row_prods=='1' and $single_row_prod_names!='') {
		    $html .= "<tr><td>$hsn</td><td>$single_row_prod_names</td><td style='text-align:right;'>$val_tot_disp</td></tr>";
		    $totals_colspan = 2;
		}
		$cgst_disp = number_format($cgst_tot,2,".","");
		$sgst_disp = number_format($sgst_tot,2,".","");
		$igst_disp = number_format($igst_tot,2,".","");
		$tax_totals = '';
		$label_colspan1 = 2;
		if($same_state) {
			$label_colspan1 = 4;
		}
		$html .= "</tbody><tfoot>";
		if($single_row_prods=='0') {
		    $html .= "<tr><th colspan=$totals_colspan>Total</th><td $right_align>$val_tot_disp</td></tr>";
		}
		$net_amt = $val_tot - $inv_discount_tot - $total_discount_amount;
		$adjustment_label = isset($row['adjustment_label'])?$row['adjustment_label']:"";
		if($purpose=='invoice' && $inv_discount_tot>0) {
			$html .= "<tr><th colspan=$totals_colspan>Adjustment for $adjustment_label</th><td $right_align>".number_format($inv_discount_tot,2,'.','')."</td></tr>";
			if($total_discount_amount>0){
			    $html .= "<tr><th colspan=$totals_colspan>Discount($discount_reason)</th><td $right_align>$total_discount_amount</td></tr>";
			}			
			$html .= "<tr><th colspan=$totals_colspan>Net Amount</th><td $right_align>".number_format($net_amt,2,'.','')."</td></tr>";			
		}else if($total_discount_amount>0){
		    $html .= "<tr><th colspan=$totals_colspan>Discount($discount_reason)</th><td $right_align>$total_discount_amount</td></tr>";
		    $html .= "<tr><th colspan=$totals_colspan>Sell Rate</th><td $right_align>".($val_tot_disp-$total_discount_amount)."</td></tr>";
		}
		$total_tax = $grand_total = 0;
		foreach ($tax_dtls as $slab=>$amt) {
			$gstdtl = explode("-",$slab);
			$gst_type = $gstdtl[0];
			$gst_per = $gstdtl[1];
			$html .= "<tr><th colspan=$totals_colspan>$gst_type @ $gst_per %</th><td $right_align>".number_format($amt,2,'.','')."</td></tr>";
			$total_tax += $amt;
		}
		$grand_total += $net_amt + $total_tax;
		$round_off_val = number_format(((round($grand_total)>$grand_total)?(round($grand_total)-$grand_total):($grand_total-round($grand_total))),2,".","");
		$round_off_action = (round($grand_total)>$grand_total)?'(+)':"(-)";
		$total = ($round_off_action=='(+)')?($grand_total+(float)$round_off_val):($grand_total-(float)$round_off_val);
		$total_disp = number_format(round($total),2,".","");
		if((float)$round_off_val!=0.00) {
			$html .= "<tr><th colspan=$totals_colspan>Roundoff$round_off_action</th><td $right_align>$round_off_val</td></tr>";
		}
		$html .= "<tr><th colspan=$totals_colspan>Grand Total</th><td $right_align>".number_format($total_disp,2,'.','')."</td></tr>";
		$amt_words = SpellNumber(round($total));
		$grand_total_colspan = 5;
		if($single_row_prods=='1') {
		    $grand_total_colspan = 3;
		}
		$html .= "<tr><th colspan=$grand_total_colspan>$amt_words</th></tr>";
		$html .= "</tfoot></table>";
	}
	$ui_arr[0] = $html;
	if(in_array($purpose,array('quotation','proforma'))) {
		$asa_html = '';
		$dtl_qry = " select glh_lead_type lead_type,gqh_currency_code currency_code from gft_quotation_hdr join gft_lead_hdr on (gqh_lead_code=glh_lead_code) ".
				" where gqh_order_no='$invoice_id' ";
		$fin_yr = get_fin_year(get_single_value_from_single_table("gqh_order_date", "gft_quotation_hdr", "gqh_order_no", "$invoice_id"),1);
		if($purpose=='proforma') {
			$dtl_qry = " select glh_lead_type lead_type,gph_currency_code currency_code from gft_proforma_hdr join gft_lead_hdr on (gph_lead_code=glh_lead_code) ".
					" where gph_order_no='$invoice_id' ";
			$fin_yr = get_fin_year(get_single_value_from_single_table("gph_order_date", "gft_proforma_hdr", "gph_order_no", "$invoice_id"),1);
		}
		$res = execute_my_query($dtl_qry);
		$lead_type = $GQH_CURRENCY = '';
		if($row = mysqli_fetch_array($res)) {
			$lead_type = $row['lead_type'];
			$GQH_CURRENCY = $row['currency_code'];
		}
		$asa_dtls = get_asa_dtls_for_pdf($skew_property,$product_type,$pcode_arr,$pskew_arr,$up_product_code_arr,$product_code_arr,$qty_arr,$lead_type,$GQH_CURRENCY,$merge_client,$lead_code);
		if(isset($asa_dtls['asa_rows']) and count($asa_dtls['asa_rows'])>0) {
			$asa_html .= "<p><strong>Second year ALR payable for the Year of $fin_yr.</strong></p>";
			$asa_html .= "<table border='1' style='border-collapse: collapse;' width='100%'>";
			$totals_colspan = 4;
			if($single_row_prods=='1') {
			    $asa_html .= "<tr><th>Name of Product and/or Service</th><th>Net Amount</th></tr>";
			    $totals_colspan = 1;
			} else {
			    $asa_html .= "<tr><th>Sl no.</th><th>Name of Product and/or Service</th><th>Rate</th><th>Qty</th><th>Net Amount</th></tr>";
			}
			$asa_single_row_prod_names = '';
			$asa_single_row_prod_amt = 0.0;
			foreach ($asa_dtls['asa_rows'] as $k=>$vals) {
			    if($single_row_prods=='0') {
    				$asa_html .= "<tr>";
    				$asa_html .= "<td>".(string)$vals['ASA_SNO']."</td>";
    				$asa_html .= "<td>".(string)$vals['ASA_PRODUCT_NAME']."</td>";
    				$asa_html .= "<td style='text-align:right;'>".(string)$vals['ASA_GQP_SELL_RATE']."</td>";
    				$asa_html .= "<td style='text-align:right;'>".(string)$vals['ASA_GQP_QTY']."</td>";
    				$asa_html .= "<td style='text-align:right;'>".(string)$vals['ASA_GQP_SELL_AMOUNT']."</td>";
    				$asa_html .= "</tr>";
			    } else {
			        $asa_single_row_prod_names .= (($asa_single_row_prod_names!=''?',<br/>':'').(string)$vals['ASA_GQP_QTY']." Qty ".(string)$vals['ASA_PRODUCT_NAME']);
			        $asa_single_row_prod_amt += floatval(str_replace(",","",$vals['ASA_GQP_SELL_AMOUNT']));
			    }
			}
			if($single_row_prods=='1' and $asa_single_row_prod_names!='' and $asa_single_row_prod_amt>0) {
			    $asa_html .= "<tr><td>$asa_single_row_prod_names</td><td style='text-align:right;'>$asa_single_row_prod_amt</td></tr>";
			}
			$asa_html .= "<tr><td colspan=$totals_colspan style='text-align:center;'>Add GST @ ".(string)$asa_dtls['ASA_GST_TAX_PER']." %</td><td style='text-align:right;'>".number_format((float)$asa_dtls['ASA_GST_TAX_AMOUNT'],2,".","")."</td>";
			$round_total = round((float)$asa_dtls['asa_total']);
			if($round_total>(float)$asa_dtls['asa_total']) {
				$diff = $round_total - (float)$asa_dtls['asa_total'];
				$asa_html .=  "<tr><td colspan=$totals_colspan style='text-align:center;'>Roundoff (+)</td><td style='text-align:right;'>".number_format($diff,2,".","")."</td>";
			} else if($round_total<(float)$asa_dtls['asa_total']) {
				$diff = (float)$asa_dtls['asa_total'] - $round_total;
				$asa_html .=  "<tr><td colspan=$totals_colspan style='text-align:center;'>Roundoff (-)</td><td style='text-align:right;'>".number_format($diff,2,".","")."</td>";
			}
			$asa_html .= "<tr><td colspan=$totals_colspan style='text-align:center;'>Total Amount</td><td style='text-align:right;'>".number_format($round_total,2,".","")."</td>";
			$asa_html .= "</table>";
		}
		$ui_arr[] = $asa_html;
	}
	return $ui_arr;
}
/**
 * @param string $quotation_no
 * @param string $purpose
 * @param string $user_id
 *
 * @return string
 */
function generate_quotation_proforma_content($quotation_no,$purpose='quotation',$user_id='') {
	$query = get_query_for_quotation_pdf($quotation_no);
	if($purpose=='proforma') {
		$query = get_proforma_hdr_query($quotation_no);		
	}
	$query_res = execute_my_query($query);
	$db_document_content_config = /*.(string[string]).*/array();
	$GQH_CURRENCY='INR';
	$emp_id = $LEAD_CODE = '0';
	$lead_type = "";
	if($row = mysqli_fetch_array($query_res)) {
		$db_document_content_config['customer_address'] = $row['6'];
		$db_document_content_config['Quotation_No'] 	= $row['0'];
		$db_document_content_config['Quotation_date']	= $row['3'];
		$db_document_content_config['customer_name']	= $row['5'];
		$db_document_content_config['customer_id']	    = $row['1'];
		$db_document_content_config['customer_gstin']	= (isset($row['GLE_GST_NO']) and $row['GLE_GST_NO']!='')?$row['GLE_GST_NO']:'Not Available';
		$GQH_CURRENCY=$row['7'];
		$emp_id = $row['2'];
		$LEAD_CODE = $row['1'];
		$lead_type = $row['GLH_LEAD_TYPE'];
	} else {
		return '';
	}
	$db_document_content_config['product_terms']   = get_product_wise_terms($quotation_no, $purpose);	
	$db_document_content_config['company_gstin']   = get_single_value_from_single_table("GCM_GST_NO", "gft_company_master", "GCM_ID", "1");
	$db_document_content_config['os_details']      = get_outstanding_details_as_table($LEAD_CODE);
	if($purpose=='quotation') {
	    $quote_dtl = /*.(string[int]).*/get_invoice_item_dtl_html($quotation_no,'quotation', $lead_type);
	} else if($purpose=='proforma') {
	    $quote_dtl = /*.(string[int]).*/get_invoice_item_dtl_html($quotation_no,'proforma', $lead_type);
		$commission_dtl_chk = execute_my_query("select gac_id from gft_add_on_commission_dtl where gac_order_no='$quotation_no'");
		if(mysqli_num_rows($commission_dtl_chk)>0) {
			$quote_dtl = /*.(string[int]).*/get_invoice_item_dtl_html_for_addon($quotation_no,true);
			$db_document_content_config['os_details'] = "&nbsp;";
		}
	}
	$db_document_content_config['invoice_detail_table'] = $quote_dtl[0];
	$total_hours = 0;
	if($purpose=='quotation') {
		$total_hours_qry = execute_my_query(" select sum(if(gqp_coupon_hour is not null and gqp_coupon_hour>0 and gpm_training_required='Y',gqp_coupon_hour*gqp_qty,if(gpm_training_required='Y' and gpm_training_hrs is not null and gpm_training_hrs>0,gpm_training_hrs*gqp_qty,0))) as hours ".
				" from gft_quotation_product_dtl join gft_product_master on (gpm_product_code=gqp_product_code and gpm_product_skew=gqp_product_skew) where gqp_order_no='$quotation_no' group by gqp_order_no ");
		if($row = mysqli_fetch_array($total_hours_qry)) {
			$total_hours += $row['hours'];
		}
		if($total_hours>0) {
			$db_document_content_config['total_training_hours'] = "Total service hours alloted : $total_hours<br><b>Note:</b>Beyond the alloted service hours customers can pay and buy additional hours. Contact your sales manager for the same. ";;
		} else {
			$db_document_content_config['total_training_hours'] = '';
		}
	}
	if(isset($quote_dtl[1]) and $quote_dtl[1]!='') {
		$db_document_content_config['asa_detail_table'] = $quote_dtl[1];
	} else {
		$db_document_content_config['asa_detail_table'] = '&nbsp;';
	}
	$key_name = 'Bank_Details_INR';
	if($GQH_CURRENCY=='USD'){
		$key_name = 'Bank_Details_USD';
	}
	$db_document_content_config['bankDetails']=get_samee_const($key_name);
	if(get_single_value_from_single_table("gem_status", "gft_emp_master", "gem_emp_id", $emp_id)!='A' and (int)$user_id>0) {
		$emp_id = $user_id;
	}
	$excnamedesc=get_emp_name_desc($emp_id);
	$db_document_content_config['execName'] = $excnamedesc[0];
	$db_document_content_config['execMobileNo'] = $excnamedesc[1];
	$db_document_content_config['execMailId'] = $excnamedesc[2];
	$db_document_content_config['exceDesignation'] = $excnamedesc[3];
	$category_id = '1';
	$template_id = '28';
	$proforma_subject = "GoFrugal Product Enquiry";
	$db_document_content_config['expiry_message'] 	= "";
	if($purpose=='proforma') {
		$category_id = '4';
		$template_id = '29';
		$que1 = " select GPP_ORDER_NO from gft_proforma_product_dtl ".
				" join gft_product_master on (GPM_PRODUCT_CODE=GPP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GPP_PRODUCT_SKEW) ".
				" where GPP_ORDER_NO='$quotation_no' and GFT_SKEW_PROPERTY in (4,15) ";
		$res1 = execute_my_query($que1);
		if(mysqli_num_rows($res1) > 0){ //asa proforma
			$proforma_subject = "GoFrugal Product Expiry";
			$inst_que = " select GID_VALIDITY_DATE from gft_install_dtl_new ".
					" join gft_product_master pm on (GID_LIC_PCODE=pm.GPM_PRODUCT_CODE and GID_LIC_PSKEW=pm.GPM_PRODUCT_SKEW) ".
					" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
					" where GID_LEAD_CODE='$LEAD_CODE' and GID_STATUS!='U' and GPM_LICENSE_TYPE='1' and GPM_IS_BASE_PRODUCT='Y' ".
					" and datediff(GID_VALIDITY_DATE,now()) < 60 ";
			$inst_res = execute_my_query($inst_que);
			if($row1 = mysqli_fetch_array($inst_res)){
				$date_str = date('d M, Y',strtotime($row1['GID_VALIDITY_DATE']));
				$db_document_content_config['expiry_message'] 	= "Kind Attention : ".$db_document_content_config['customer_name'].
				                                                  ", Your License Expires on $date_str ";
			}
		}
	}
	$db_document_content_config['proforma_subject'] = $proforma_subject;
	$customer_country = get_single_value_from_single_table("glh_country", "gft_lead_hdr", "GLH_LEAD_CODE", "$LEAD_CODE");
	if(($emp_id<7000 || $emp_id=='9999') && (strtolower($customer_country)=='india')){
	    $db_document_content_config['Virtual_Account_Number_Details'] 	= true;	
	    $db_document_content_config['Virtual_Account_Number']=check_and_generate_account_number($LEAD_CODE);
	}
	$content = get_formatted_document_content($db_document_content_config, $category_id, $template_id);
	return $content;
}
/**
 * @param string $ac_ref
 * @param string $invoice_date
 * @return string
 */
function get_invoice_ac_ref($ac_ref,$invoice_date) {
	$financial_year_start = ((int)date('m') < 4) ? date('Y-04-01',strtotime('-1 year')) : date('Y-04-01');
	$fin_yr = get_fin_year($financial_year_start,0,true);
	$id_res = execute_my_query(" select gih_invoice_ac_reffer_id max_id from gft_invoice_hdr ".
			" where gih_invoice_date >= '$financial_year_start' and gih_invoice_ac_reffer_id like '$fin_yr/$ac_ref%' ".
			" order by gih_invoice_ac_reffer_id desc limit 1 ");
	$month_id = '';
	if(strtotime(date('Y-m-d H:i:s'))>=strtotime('2018-04-01 00:00:00')) {
		$month_start = date('Y-m-01');
		$month_ids = array('01'=>'10','02'=>'11','03'=>'12','04'=>'01','05'=>'02','06'=>'03','07'=>'04','08'=>'05','09'=>'06','10'=>'07','11'=>'08','12'=>'09');
		$curr_month = date('m');
		$month_id = strtoupper(date('M'));//$month_ids[$curr_month];
		$id_res = execute_my_query(" select gih_invoice_ac_reffer_id max_id from gft_invoice_hdr ".
				" where gih_invoice_date >= '$month_start' and gih_invoice_ac_reffer_id like '$fin_yr/$month_id/$ac_ref%' ".
				" order by gih_invoice_ac_reffer_id desc limit 1 ");
	}
	$ac_ref_id = '';
	if(mysqli_num_rows($id_res)==0){
		$ac_ref_id = $fin_yr."/".$ac_ref."00001";
		if(strtotime(date('Y-m-d H:i:s'))>=strtotime('2018-04-01 00:00:00')) {
			$ac_ref_id = "$fin_yr/$month_id/$ac_ref"."0001";
		}
	}else if($querydata=mysqli_fetch_array($id_res)){
		$id_parts = explode("/",$querydata['max_id']);
		$num = isset($id_parts[1])?$id_parts[1]:$id_parts[0];
		$max_id = (int)preg_replace("/[^0-9]/","",$num);
		$max_id = $max_id+1;
		$ac_ref_id = $fin_yr."/".$ac_ref.substr("00000".$max_id,-5);
		if(strtotime(date('Y-m-d H:i:s'))>=strtotime('2018-04-01 00:00:00')) {
			$num = isset($id_parts[2])?$id_parts[2]:$id_parts[0];
			$max_id = (int)preg_replace("/[^0-9]/","",$num);
			$max_id = $max_id+1;
			$ac_ref_id = "$fin_yr/$month_id/$ac_ref".substr("0000".$max_id,-4);
		}
	}
	return $ac_ref_id;
}
/**
 * @param string[string][string] $gst_split
 * @param string[string] $sell_amt_arr
 * @param string[int] $qty
 * @param string[int] $coupon_hrs
 * @param string[int] $list_price
 * @return string[string][string]
 */
function get_gst_values_for_products($gst_split,$sell_amt_arr,$qty,$coupon_hrs,$list_price) {
	$i = 0;
	$gst_vals = /*.(string[string][string]).*/array();
	foreach ($sell_amt_arr as $k=>$v) {
		$cgst = $gst_split[$k]['cgst'];
		$sgst = $gst_split[$k]['sgst'];
		$igst = $gst_split[$k]['igst'];
		$total_tax_p = $cgst+$sgst+$igst;
		$list_price[$i] = ($v/$qty[$i]) * (100/(100+$total_tax_p));
		$tot_qty = $qty[$i]*((isset($coupon_hrs[$i]) and (int)$coupon_hrs[$i]>0)?(int)$coupon_hrs[$i]:1);
		$igst_amt = $list_price[$i]*($igst/100)*$tot_qty;
		$sgst_amt = $list_price[$i]*($sgst/100)*$tot_qty;
		$cgst_amt = $list_price[$i]*($cgst/100)*$tot_qty;
		$gst = array('cgst_per'=>$cgst,'cgst_amt'=>$cgst_amt,
				'sgst_per'=>$sgst,'sgst_amt'=>$sgst_amt,
				'igst_per'=>$igst,'igst_amt'=>$igst_amt);
		$gst_vals[$k] = $gst;
		$i++;
	}
	return $gst_vals;
}
/**
 * @param string $order_no
 * @return string
 */
function get_invoice_in_gst_format($order_no) {
	$order_dtls = get_data_from_table(array('god_tax_mode','god_lead_code','god_emp_id'), 'gft_order_hdr', array('god_order_no'), array($order_no));
	$row = isset($order_dtls[0])?$order_dtls[0]:array();
	$tax_mode = isset($row['god_tax_mode'])?$row['god_tax_mode']:'';
	$prod_dtl = new InvoiceProdDetails();
	$prod_dtl->lead_code = isset($row['god_lead_code'])?$row['god_lead_code']:'';
	$prod_dtl->ordered_emp_id = isset($row['god_emp_id'])?$row['god_emp_id']:'';
	$prod_dtl->invoice_date = date('Y-m-d');
	$prod_dtl->against_cform ='N';
	if(in_array($tax_mode,array('1','2'))) {
		$prod_dtls_qry = " select concat(gop_product_code,'-',gop_product_skew) prod,gop_sell_amt,gop_sell_rate,gop_product_code,gop_product_skew,gop_qty,gop_sell_rate,gop_sell_amt,GOP_COUPON_HOUR from gft_order_product_dtl where gop_order_no='$order_no' and gop_sell_amt>0 ";
		$res = execute_my_query($prod_dtls_qry);
		$prods=$qty=$list_price=$coupon_hrs= /*.(string[int]).*/array();
		$sell_amt_arr = /*.(string[string]).*/array();
		while($row1 = mysqli_fetch_array($res)) {
			$prod_dtl->orderno[] = $order_no;
			$prod_dtl->net_amt[] = $row1['gop_sell_amt'];
			$prods[] = $row1['prod'];
			$qty[] = $row1['gop_qty'];
			$prod_dtl->qty[] = $row1['gop_qty'];
			$prod_dtl->pcode[] = $row1['gop_product_code'];
			$prod_dtl->pskew[] = $row1['gop_product_skew'];
			$prod_dtl->sell_amt[] = $row1['gop_sell_rate'];
			$prod_dtl->s_tax_amt[] = $prod_dtl->s_tax_amt[] = $prod_dtl->serv_tax_rate[] = $prod_dtl->serv_tax_amt[] = '0.0';
			$prod_dtl->list_price[] = '0.0';
			$coupon_hrs[] = $row1['GOP_COUPON_HOUR'];
			$prod_dtl->coupon_hrs[] = $row1['GOP_COUPON_HOUR'];
			$prod_dtl->adj_discount[] = '0.0';
			$sell_amt_arr[$row1['prod']] = $row1['gop_sell_amt'];
		}
		$gst_split = get_gst_split_for_lead_code($prod_dtl->lead_code, $prods);
		$prod_dtl->gst_fields = /*.(float[string][string]).*/get_gst_values_for_products($gst_split,$sell_amt_arr,$qty,$coupon_hrs,$list_price);
		$prod_dtl->ac_referer_id = get_invoice_ac_ref("CS", date('Y-m-d'));
		$prod_dtl->invoice_type = '1';
		insert_and_generate_invoice($prod_dtl);
		execute_my_query("update gft_order_hdr set god_invoice_status='Y' where god_order_no='$order_no'");
		return $prod_dtl->ac_referer_id;
	}
	return '';
}
/**
 * @param string $lead_code
 * @param string $lead_status
 * @param string $ex_lead_status
 * @param string $emp_id
 * @return void
 */
function update_status_change_in_ext($lead_code,$lead_status,$ex_lead_status,$emp_id) {
	global $uid;
	$status_cols = array("14"=>array("GLE_CUSTOMER_LOST_ON","GLE_CUSTOMER_LOST_BY"),
			"7"=>array("GLE_OPPORTUNITY_LOST_ON","GLE_OPPORTUNITY_LOST_BY"),"11"=>array("GLE_ORDER_LOST_ON","GLE_ORDER_LOST_BY"));
	$update_arr = array();
	if(in_array($lead_status,array('14','7','11')) and $ex_lead_status!=$lead_status and !empty($lead_code)) {
		$update_arr[$status_cols[$lead_status][0]] = date('Y-m-d');
		$update_arr[$status_cols[$lead_status][1]] = $emp_id;
		$table_column_iff_update = $update_arr;
		$key_arr = array("gle_lead_code"=>$lead_code);
		array_update_tables_common($update_arr,"gft_lead_hdr_ext",$key_arr,'',$uid,null,$table_column_iff_update);
	}
}
/**
 * @param string $order_no
 * @param string $lead_code
 *
 * @return void
 */
function update_phone_call_support_dtl($order_no,$lead_code){

	$result = execute_my_query("select (GOP_QTY*GPM_SUPPORT_HRS) as support_hrs from gft_order_product_dtl
			INNER JOIN gft_product_master pm ON(GOP_PRODUCT_CODE=GPM_PRODUCT_CODE AND GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW)
			where GOP_ORDER_NO='$order_no' AND GFT_SKEW_PROPERTY in (1,7,15) AND GPM_SUPPORT_HRS>0");
	while($row=mysqli_fetch_array($result)){
		$return_value = (int)$row['support_hrs'];
		$return_value = $return_value*60;
		execute_my_query("UPDATE gft_lead_hdr_ext SET GLE_SUPPORT_MODE=2, GLE_AVAILABLE_SERVICE_MINS=(GLE_AVAILABLE_SERVICE_MINS+$return_value) WHERE GLE_LEAD_CODE='$lead_code'");
	}
}
/**
 * @param string $lead_code
 * @param string $order_no
 * 
 * @return void
 */
function send_voice_support_purchased_notification($lead_code,$order_no){
	$result_support = execute_my_query("select GPM_SUPPORT_HRS,GOP_QTY from gft_order_product_dtl ".
					"INNER JOIN gft_product_master pm ON(GPM_PRODUCT_CODE=GOP_PRODUCT_CODE AND GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
					"where GOP_PRODUCT_CODE=392 and GOP_ORDER_NO='$order_no'");
	$support_hrs = 0;
	while($row=mysqli_fetch_array($result_support)){
		$qty = (int)$row['GOP_QTY'];
		$support_hrs = $support_hrs+((int)$row['GPM_SUPPORT_HRS'])*$qty;
	}
	if((int)$support_hrs>0){
		$noti_content_config = array();
		$noti_content_config['Payable_Support_Duration'] =array("$support_hrs");
		send_formatted_notification_content($noti_content_config,0,78,2,$lead_code,$lead_code,0,0);
		entry_sending_sms_to_customer(null,get_formatted_content(array('Payable_Support_Duration'=>array($support_hrs),'Customer_ID'=>array($lead_code)),198),198,$lead_code,1,'9999');
	}
}
/**
 * @param string $lead_code
 * @param string $spoc
 * @param string $contact_no
 *
 * @return void
 */
function udpate_ts_contact_group($lead_code,$spoc,$contact_no=''){
	if($contact_no!=""){
		$contact_res = execute_my_query("select gcc_id from gft_customer_contact_dtl where GCC_LEAD_CODE='$lead_code' and GCC_CONTACT_NO in ('$contact_no')");
		if((mysqli_num_rows($contact_res)>0) &&($row_con=mysqli_fetch_array($contact_res))){
			$spoc = $row_con['gcc_id'];
		}
	}
	if($spoc!=""){
		$del_query="delete from gft_contact_dtl_group_map where GCG_LEAD_CODE=$lead_code and GCG_CONTACT_ID!=$spoc and GCG_GROUP_ID=1";
		execute_my_query($del_query);
		$ins_query="insert ignore into gft_contact_dtl_group_map (GCG_LEAD_CODE,GCG_CONTACT_ID,GCG_GROUP_ID,GCG_UPDATED_DATE) values ($lead_code,$spoc,1,now())";
		execute_my_query($ins_query);
	}	
}
/**
 * @param string $lead_code
 * 
 * @return string[int]
 */
function get_customer_ts_number($lead_code){
	$return_arr[0] = "";
	$return_arr[1] = "";
	$result = execute_my_query("select GCC_CONTACT_NO,GCC_CONTACT_NAME from gft_contact_dtl_group_map ".
							" INNER JOIN gft_customer_contact_dtl cc ON(GCG_CONTACT_ID=gcc_id) ".
							" where GCG_LEAD_CODE='$lead_code' AND GCG_GROUP_ID='1'");
	if((mysqli_num_rows($result)>0) && $row_con=mysqli_fetch_array($result)){
		$return_arr[0] = $row_con['GCC_CONTACT_NO'];
		$return_arr[1] = $row_con['GCC_CONTACT_NAME'];
	}
	return $return_arr;
}
/**
 * @param string $ts_contact_no
 * 
 * @return int
 */
function get_mygofrugal_user_id($ts_contact_no){
	$return_id = 0;
	$where_con = contact_info_where_condition("GCL_USERNAME", "$ts_contact_no");
	$result = execute_my_query("select GCL_USER_ID from gft_customer_login_master WHERE (1) and $where_con");
	if((mysqli_num_rows($result)>0) && $row_id=mysqli_fetch_array($result)){
		$return_id = (int)$row_id['GCL_USER_ID'];
	}
	return $return_id;
}
/**
 * @param string $email
 * @param string $mob_no
 *
 * @return string
 */
function get_query_to_check_employee_lead_exist($email, $mob_no){
    $mob_cond = ($mob_no!='') ? contact_info_where_condition("GEM_MOBILE", $mob_no) : " 0 ";
    $email_cond = ($email!='')?" or GEM_EMAIL='$email' ":"";
    $sql_query =" SELECT GEM_LEAD_CODE as GCC_LEAD_CODE,GLH_LFD_EMP_ID,glh_status from gft_emp_master ". 
                " INNER JOIN gft_lead_hdr lh ON(GLH_LEAD_CODE=GEM_LEAD_CODE) ".
                " where ($mob_cond $email_cond)AND GEM_LEAD_CODE!='' AND GEM_STATUS='A'   limit 1 ";
    return $sql_query;
}
/**
 * @param string $email
 * @param string $mob_no
 *
 * @return string
 */
function get_query_to_check_lead_exist($email, $mob_no){
    $mob_cond = ($mob_no!='') ? contact_info_where_condition("GCC_CONTACT_NO", $mob_no) : " 0 ";
	$email_cond = ($email!='')?" or GCC_CONTACT_NO='$email' ":"";
	$sql_query =" select GCC_LEAD_CODE,GLH_LFD_EMP_ID,glh_status from gft_customer_contact_dtl ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GCC_LEAD_CODE) ".
			" left join gft_pos_users on (GPU_CONTACT_ID=GCC_ID) ".
			" left join gft_install_dtl_new on (GID_LEAD_CODE=GCC_LEAD_CODE) ".
			" where ($mob_cond $email_cond) and if(GPU_CONTACT_ID is null,1,GPU_CONTACT_STATUS='A') ".
			" order by GPU_INSTALL_ID desc, GID_LEAD_CODE desc, GCC_LEAD_CODE limit 1 ";
	return $sql_query;
}
/**
 * @param string $email
 * @param string $mob_no
 *
 * @return string
 */
function get_existing_leads($email, $mob_no){
    $mob_cond = ($mob_no!='') ? contact_info_where_condition("GCC_CONTACT_NO", $mob_no) : " 0 ";
    $email_cond = ($email!='')?" or GCC_CONTACT_NO='$email' ":"";
    $sql_query =" select GROUP_CONCAT(DISTINCT GCC_LEAD_CODE ORDER BY GCC_LEAD_CODE ASC) LEADS, '1' GROUP_FIELD from gft_customer_contact_dtl ".
                " join gft_lead_hdr on (GLH_LEAD_CODE=GCC_LEAD_CODE) ".
                " left join gft_pos_users on (GPU_CONTACT_ID=GCC_ID) ".
                " where ($mob_cond $email_cond) and if(GPU_CONTACT_ID is null,1,GPU_CONTACT_STATUS='A') ".
                " group by GROUP_FIELD ";
    $res = execute_my_query($sql_query);
    if($row=mysqli_fetch_assoc($res)){
        return  $row['LEADS'];
    }
    return '';
    
}
/**
 * @param string $exising_leads
 * @param string $product_code
 * @param string $product_skew
 * 
 * @return string
 */
function get_existing_lead_for_unique_installation($exising_leads,$product_code,$product_skew){
    $lead_code_to_return = "";
    $lead_ins_info = array();
    $lead_only_paid_pro = array();
    $date_now	=	date("Y-m-d", strtotime("-30 days"));
    $result = execute_my_query("select GLH_LEAD_CODE, GLH_STATUS, GLH_VERTICAL_CODE,GTM_BUSINESS_TYPE, GPM_FREE_EDITION, ". 
                            " GID_VALIDITY_DATE, GID_STATUS, GID_LIC_PCODE, SUBSTRING(GID_LIC_PSKEW, 1,4) GID_LIC_PSKEW from gft_lead_hdr ". 
                            " INNER JOIN gft_vertical_master vm ON(GLH_VERTICAL_CODE=GTM_VERTICAL_CODE) ".
                            " LEFT JOIN gft_install_dtl_new ON(GLH_LEAD_CODE=GID_LEAD_CODE AND GID_LIC_PCODE IN('500','200','601','605')) ".
                            " LEFT JOIN gft_product_family_master pf ON(GID_LIC_PCODE=pf.GPM_PRODUCT_CODE AND GPM_IS_BASE_PRODUCT='Y') ".
                            " LEFT JOIN gft_product_master pm ON(pm.GPM_PRODUCT_CODE=pf.GPM_PRODUCT_CODE  AND GID_LIC_PSKEW=GPM_PRODUCT_SKEW) ".
                            " where glh_lead_code IN($exising_leads) AND GLH_LEAD_TYPE!=7  order by GLH_LEAD_CODE ASC, CAST(GPM_FREE_EDITION AS CHAR) ASC ");
    while($row=mysqli_fetch_assoc($result)){
        $lead_code = $row['GLH_LEAD_CODE'];
        if(($row['GPM_FREE_EDITION']=='')){
            //no installation
            $lead_ins_info[$lead_code]['NP'] = isset($lead_ins_info[$lead_code]['NP'])?($lead_ins_info[$lead_code]['NP']+1):1;
            $lead_only_paid_pro[$lead_code]['NP'] = isset($lead_only_paid_pro[$lead_code]['NP'])?($lead_only_paid_pro[$lead_code]['NP']+1):1;
        }else if(($row['GPM_FREE_EDITION']=='Y') && (strtotime($row['GID_VALIDITY_DATE'])<strtotime($date_now) || $row['GID_STATUS']=='U')){
            //Trial expired 
            $lead_ins_info[$lead_code]['TE'] = isset($lead_ins_info[$lead_code]['TE'])?($lead_ins_info[$lead_code]['TE']+1):1; 
        }else if(($row['GPM_FREE_EDITION']=='Y') && $product_code == $row['GID_LIC_PCODE'] && $product_skew == $row['GID_LIC_PSKEW']){
            // Existing lead code is returned if Existing Trial product and registerd product is same
            $lead_ins_info[$lead_code]['TV'] = isset($lead_ins_info[$lead_code]['TV'])?($lead_ins_info[$lead_code]['TV']+1):1;
        }else if(($row['GPM_FREE_EDITION']=='N') && $product_code == $row['GID_LIC_PCODE'] && $product_skew == $row['GID_LIC_PSKEW']){
            $lead_ins_info[$lead_code]['PV'] = isset($lead_ins_info[$lead_code]['PV'])?($lead_ins_info[$lead_code]['PV']+1):1;
        }
        if($row['GPM_FREE_EDITION']=='N'){
            $lead_only_paid_pro[$lead_code]['PV'] = isset($lead_only_paid_pro[$lead_code]['PV'])?($lead_only_paid_pro[$lead_code]['PV']+1):1;
        }else if($row['GPM_FREE_EDITION']=='Y'){
            $lead_only_paid_pro[$lead_code]['TE'] = isset($lead_only_paid_pro[$lead_code]['TE'])?($lead_only_paid_pro[$lead_code]['TE']+1):1; 
        }
    }
    $trial_expired_lead_code = $trial_valid_lead_code  =  "";
    if(count($lead_ins_info)>0 && $product_code!='762'){        
        foreach ($lead_ins_info as $lead=>$lead_dtl){
            if(isset($lead_dtl['NP']) && $lead_dtl['NP']>0){
                return $lead;
            }else if(isset($lead_dtl['TE']) && $lead_dtl['TE']>0 && (!isset($lead_dtl['PV']))){
                $trial_expired_lead_code = $lead;
            }else if(isset($lead_dtl['TV']) && $lead_dtl['TV']>0 && (!isset($lead_dtl['PV']))){
                $trial_valid_lead_code =  $lead;
            }else if(isset($lead_dtl['PV']) && $lead_dtl['PV']>0){
                $lead_code_to_return = $lead;
            }
        }
        return ($trial_expired_lead_code!=""?"$trial_expired_lead_code":($trial_valid_lead_code!=""?$trial_valid_lead_code:$lead_code_to_return));
    }  
    
    if(count($lead_only_paid_pro)>0 && $product_code=='762'){
        foreach ($lead_only_paid_pro as $lead=>$lead_dtl){
            if(isset($lead_dtl['NP']) && $lead_dtl['NP']>0){
                return $lead;
            }else if(isset($lead_dtl['TE']) && $lead_dtl['TE']>0){
                $trial_expired_lead_code = $lead;
            }
        }
        return $trial_expired_lead_code;
    }   
    return $lead_code_to_return;
}
/**
 * @param int $dbreceiptid
 * @param boolean $download_file
 * @param boolean $from_web
 * @param string $duplicate
 * @return void
 */
function generate_receipt_content_and_pdf($dbreceiptid,$download_file=false,$from_web=false,$duplicate='') {
	global $uid;
	$receipt_content=generate_receipt_content($dbreceiptid,false);
	if($receipt_content!=''){
		$receipt_content='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">' .
				'<html><head></head><body>' .$receipt_content.'</body></html>';
		$filename=generatereceiptpdf($dbreceiptid.($duplicate!=''?"-D":""),$receipt_content);
		$receipt_by_update = "";
		if(isset($uid) and $uid>0) {
			$receipt_by_update = " , grd_receipted_by='$uid' ";
		}
		if($duplicate==''){
			execute_my_query("update gft_receipt_dtl set GRD_LEDGER_REFERENCE='$filename' $receipt_by_update where grd_receipt_id=$dbreceiptid ");
		}
		if($download_file) {
			js_location_href_to("file_download.php?file_type=receipt&filename=$filename");
		}
	} else if($from_web) {
		echo "<h2>Check the receipt details, receipt no! or Contact A/C Team.</h2>";
		exit;
	}
}
/**
 * @param string $cust_id
 * @param string $curr_version
 * @return string[string]
 */
function create_proposal_dir_for_cust($cust_id,$curr_version='') {
	global $attach_path;
	$doc_path = "$attach_path/HQ_Proposals/$cust_id/";
	$doc_name = "RCM_Proposal_$cust_id-V1.0.pdf";
	$next_version = "1.0";
	if($curr_version!='') {
		$next_version = number_format((float)$curr_version + 1.0,1,".","");
		$doc_name = "RCM_Proposal_$cust_id-V$next_version.pdf";
	}
	$doc_path .= $doc_name;
	if(!file_exists("$attach_path/HQ_Proposals/$cust_id/")) {
		mkdir("$attach_path/HQ_Proposals/$cust_id/",0777);
	}
	return array('doc_dir'=>"$doc_path","version"=>$next_version,'doc_name'=>$doc_name);
}
/**
 * @param string $cust_id
 * @param string $doc_path
 * @param string $emp_id
 * @param string $next_version
 * @param string $quotation_id
 * @param string $doc_name
 * @return string
 */
function insert_update_doc_dtl_in_tables($cust_id,$doc_path,$emp_id,$next_version,$quotation_id,$doc_name) {
	$insert_qry = " insert into gft_corporate_customer_doc_dtl (GCC_LEAD_CODE,GCC_DOC_PATH,GCC_DOC_TYPE,GCC_UPLOADED_BY, ".
			" GCC_UPLOADED_ON,GCC_VERSION_ID,GCC_STATUS) values ('$cust_id','$doc_path','2','$emp_id',now(),'$next_version','1') ";
	$insert_res = execute_my_query($insert_qry);
	$new_doc_id = '';
	if($insert_res) {
		$new_doc_id = (string)mysqli_insert_id_wrapper();
		$update_res = execute_my_query("update gft_quotation_hdr set GQH_HQ_PROPOSAL_DOC_ID='$new_doc_id' where gqh_order_no='$quotation_id'");
		if($update_res) {
			show_my_alert_msg('New HQ proposal document created.\nFile Name: '.$doc_name);
		}
	}
	return $new_doc_id;
}
/**
 * @param string $cust_id
 * @return string
 */
function get_quote_proposal_id($cust_id) {
	$sel_qry = " select gqh_order_no from gft_quotation_hdr join gft_corporate_customer_doc_dtl on (GQH_HQ_PROPOSAL_DOC_ID=gcc_id) ".
			" where gcc_status in (1,2,3) and GCC_UPLOADED_ON in (select max(GCC_UPLOADED_ON) from gft_corporate_customer_doc_dtl ".
			" where gcc_lead_code='$cust_id' and gcc_status in (1,2,3) and GCC_DOC_TYPE='2') and gcc_lead_code='$cust_id' and ".
			" GCC_DOC_TYPE='2' and GQH_APPROVAL_STATUS not in (4) and gqh_order_status not in ('C')";
	$sel_res = execute_my_query($sel_qry);
	if(mysqli_num_rows($sel_res)>0) {
		$row = mysqli_fetch_array($sel_res);
		$quote_no = $row['gqh_order_no'];
		return "$quote_no";
	} else {
		return '0';
	}
}
/**
 * @param string $row_cnt
 * @param string $topic_id
 * @param string $submit_msg_div
 * @param string $buttons_html
 * @param boolean $show_fetch_all_button
 * @return string
 */
function common_for_table_ui($row_cnt,$topic_id,$submit_msg_div,$buttons_html,$show_fetch_all_button=false) {
	$fetch_all_button = "";
	if($show_fetch_all_button) {
		$fetch_all_button = "<button class='btn-info center fetch_all_row'>Fetch Requirements From Master</button>";
	}
	return <<<END
				<br><input type='hidden' id='row_cnt_$topic_id' value='$row_cnt'>
				<div class='col-md-12 text-center'>
					<button class='btn-info center add_row'>Add</button>
					<button class='btn-info center del_row'>Remove</button>
					$fetch_all_button
				</div><br>$submit_msg_div<br>
				$buttons_html
END;
}
/** 
 * @param string $cust_id
 * @param string $topic_id
 * @param string $submit_msg_div
 * @param string $buttons_html
 * @return string
 */
function show_challenges_and_hurdles($cust_id,$topic_id,$submit_msg_div,$buttons_html) {
	$contents_qry = execute_my_query(" select ghc_challenge,ghc_requirement from gft_hq_proposal_challenge ".
									 " where ghc_lead_code='$cust_id' ");
	$form_html =<<<END
					<table id='challenges' width=90% border=1 style='border-collapse: collapse;' align='center'>
					<tbody><tr><th>S.no</th><th>Challenge</th><th>Required Solution</th>
END;
	$row_cnt = 0;
	while($row_data = mysqli_fetch_array($contents_qry)) {
		$challenge = $row_data['ghc_challenge'];
		$soln	   = $row_data['ghc_requirement'];
		$row_cnt += 1;
		$form_html .=<<<END
			<tr style='text-align: center;'>
			<td style='text-align: right;'>$row_cnt<br><input type='checkbox' name='rem' id='rem' row_num='$row_cnt'></td>
			<td><textarea id='challenge_$row_cnt' name='challenge[]' cols=40 rows=5>$challenge</textarea></td>
			<td><textarea id='req_$row_cnt' name='req[]' cols=40 rows=5>$soln</textarea></td></tr>
END;
	}
	$form_html .= "</table>";
	$form_html .= common_for_table_ui("$row_cnt",$topic_id,$submit_msg_div,$buttons_html);
	return $form_html;
}
/**
 * @param string $cust_id
 * @param string $topic_id
 * @return string
 */
function get_qry_for_operational_requirements($cust_id,$topic_id) {
	return " select GSC_SUBDIV,GSC_CONTENT,GSC_ID from gft_proposal_subdivisions_content ".
		 " left join gft_proposal_subdivisions on (GSC_SUBDIV=GCD_SUBDIV_TOPIC and GCD_TOPIC=GSC_TOPIC_ID  ".
		 " and GCD_STATUS='1') where GSC_TOPIC_ID='$topic_id' and GSC_LEAD_CODE='$cust_id' ";
}
/**
 * @param string $cust_id
 * @param string $topic_id
 * @param string $submit_msg_div
 * @param string $buttons_html
 * @return string
 */
function get_table_ui_with_data($cust_id,$topic_id,$submit_msg_div,$buttons_html) {
	$qry = get_qry_for_operational_requirements($cust_id, $topic_id);
	$res = execute_my_query($qry);
	$table_id_arr = array("23"=>"ho_req","24"=>"ro_req","25"=>"warehouse_req");
	$table_id = $table_id_arr[$topic_id];
	$form_html =<<<END
	<table id='$table_id' width=90% border=1 style='border-collapse: collapse;' align='center'>
	<tbody><tr class='hdr_row'><th>S.no</th><th>Requirement</th><th>Description</th>'
END;
	$row_cnt = mysqli_num_rows($res);
	$count = 1;
	$name_suffix = $topic_id."[]";
	while ($row = mysqli_fetch_array($res)) {
		$id_suffix = $topic_id."_".$count;
		if($row[1]!='') {
		$form_html .=<<<END
			<tr style='text-align: center;'>
			<td style='text-align: right;'>$count<br><input type='checkbox' name='rem' id='rem' row_num='$count'></td>
			<td><textarea id='req_$id_suffix' name='req_$name_suffix' cols=40 rows=9 readonly>$row[0]</textarea></td>
			<td><textarea class='shorteditor' id='req_desc_$id_suffix' name='req_desc_$name_suffix' cols=40 rows=5>$row[1]</textarea></td></tr>
END;
		$count++;
		}
	}
	$form_html .= "</table>";
	$form_html .= common_for_table_ui("$row_cnt",$topic_id,$submit_msg_div,$buttons_html,true);
	return $form_html;
}
/**
 * @param string $lead_code
 * @param string $topic
 * @param boolean $for_bq
 * @return string[int][string]
 */
function get_all_requirement_details($lead_code,$topic,$for_bq=false) {
	$bq_join = '';
	if($for_bq) {
		$bq_join = " join gft_proposal_subdivisions on (GCD_SUBDIV_TOPIC=GSC_SUBDIV) ";
	}
	$all_dtl_qry = " select GCD_SUBDIV_TOPIC topic,'' content from gft_proposal_subdivisions where GCD_SUBDIV_TOPIC ".
			" not in (select GSC_SUBDIV from gft_proposal_subdivisions_content where gsc_lead_code='$lead_code') ".
			" and gcd_status='1' and GCD_TOPIC='$topic' union select GSC_SUBDIV topic,if(GSC_CONTENT is null or GSC_CONTENT='','***',GSC_CONTENT) content ".
			" from gft_proposal_subdivisions_content $bq_join where gsc_lead_code='$lead_code' and GSC_TOPIC_ID='$topic' ";
	$res_arr = array();
	$all_dtl_res = execute_my_query($all_dtl_qry);
	while($dtl_row = mysqli_fetch_array($all_dtl_res)) {
		$dtl = array();
		$dtl['req'] = $dtl_row['topic'];
		if($dtl_row['content']=='***') {
			if($for_bq) {
				$dtl['desc'] = $dtl_row['content'];
			} else {
				$dtl['desc'] = "";
			}
		} else {
			$dtl['desc'] = $dtl_row['content'];
		}
		$res_arr[] = $dtl;
	}
	return $res_arr;
}
/**
 * @param string $lead_code
 * @return mixed[]
 */
function get_operational_requirements_data($lead_code) {
	$qry =<<<QRY
select GPM_DESC cat,GSOP_DESC module,GPS_DESC sub_mod,GPQ_QUESTION qns,GPA_REQUIREMENT req,GPA_SUGGESTION suggestion,
GPA_GAP_TYPE,GPA_QUESTION_ANS FROM gft_pcs_audit_dtl join gft_pcs_question_master qm on (GPA_QUESTION_ID=GPQ_ID) 
INNER JOIN gft_pcs_sop_submodule_master ss ON (GPQ_SUB_MODULE_ID=GPS_ID) 
INNER JOIN gft_pcs_sop_module_master mm ON(GSOP_ID=GPS_MODULE_ID) INNER JOIN gft_pcs_sop_master sm ON(GPM_ID=GSOP_SOP_MASTER_ID) 
WHERE GPA_LEAD_CODE='$lead_code' and GSOP_SOP_MASTER_ID='2' and GPA_QUESTION_ANS in ('Yes') union all 
select GPM_DESC cat,GSOP_DESC module,GPS_DESC sub_mod,GPQ_QUESTION qns,GUB_QUESTION
req,GUB_ANSWER suggestion,'' GPA_GAP_TYPE,'Yes' GPA_QUESTION_ANS FROM gft_user_bq_question_dtl
join gft_pcs_question_master qm on (GUB_QUESTION_ID=GPQ_ID) INNER JOIN gft_pcs_sop_submodule_master ss 
ON (GPQ_SUB_MODULE_ID=GPS_ID) INNER JOIN gft_pcs_sop_module_master mm ON(GSOP_ID=GPS_MODULE_ID) 
INNER JOIN gft_pcs_sop_master sm ON(GPM_ID=GSOP_SOP_MASTER_ID) WHERE GUB_LEAD_CODE='$lead_code' and GSOP_SOP_MASTER_ID='2'
QRY;
	$qry_res = execute_my_query($qry);
	$answers = array();
	$gap_reason_list=array("1"=>"Customer agreed for Commercial","2"=>"Customer agreed for Work-around","3"=>"Customer told nice to have");
	while($row = mysqli_fetch_array($qry_res)) {
		$mod = $row['module'];
		$sub_mod = $row['sub_mod'];
		$qn = $row['qns'];
		$gap_type = isset($row['GPA_GAP_TYPE'])?$row['GPA_GAP_TYPE']:'';
		$req = $row['req'];
		$sug = $row['suggestion'];
		$ans = $row['GPA_QUESTION_ANS'].(($gap_type!='' and $gap_type!='0' and isset($gap_reason_list[$gap_type]))?" - ".$gap_reason_list[$gap_type]:'');
		if(!isset($answers[$mod])) {
			$answers[$mod] = array();
		}
		if(!isset($answers[$mod][$sub_mod])) {
			$answers[$mod][$sub_mod] = array();
		}
		if(!isset($answers[$mod][$sub_mod][$qn])) {
			$answers[$mod][$sub_mod][$qn] = array();
		}
		$answers[$mod][$sub_mod][$qn]['requirements'][] = array("requirement"=>$req,"suggestion"=>$sug);
		$answers[$mod][$sub_mod][$qn]['answer'] = $ans;
	}
	return $answers;
}
/**
 * @param string $lead_code
 * @param string $doc_type
 * @return string
 */
function get_latest_version_of_doc($lead_code,$doc_type) {
	$qry = " select max(gcc_version_id) ver from gft_corporate_customer_doc_dtl where gcc_lead_code='$lead_code' ".
		   " and gcc_doc_type='$doc_type' and gcc_status='3' ";
	$res = execute_my_query($qry);
	$ver = '';
	if($row = mysqli_fetch_array($res)) {
		$ver = $row['ver'];
	}
	return $ver;
}
/**
 * @param string $lead_code
 * @return string[int]
 */
function get_proposal_doc_dtls($lead_code) {
	$ids = /*.(string[int]).*/array();
	$proposal_chk_qry = execute_my_query(" select gcc_id from gft_corporate_customer_doc_dtl where gcc_doc_type='2' ".
										 " and gcc_lead_code='$lead_code' and gcc_status not in ('1','4') ");
	while($row = mysqli_fetch_array($proposal_chk_qry)) {
		$ids[] = $row['gcc_id'];
	}
	return $ids;
}
/**
 * @param string $blood_gp
 * @return string
 */
function get_blood_gp_combo($blood_gp='') {
    $blood_gp_arr = array(array('O+','O+'),array('O-','O-'),array('A+','A+'),array('A-','A-'),array('B+','B+'),array('B-','B-'),array('AB+','AB+'),array('AB-','AB-'),array('A1-','A1-'),array('A1+','A1+'));
	$blood_gp_combo = fix_combobox_with("blood_gp", "blood_gp", $blood_gp_arr,$blood_gp,'','-Select-','Style="Width:150px"');
	return $blood_gp_combo;
}
/**
 * @param string $from_dt
 * @param string $to_dt
 * @param string $type
 * @param string $lead_condition
 * @return string
 */
function get_hq_support_pending_query($from_dt,$to_dt,$type,$lead_condition='') {
	$con1=" and GCH_COMPLAINT_DATE > '$from_dt 00:00:00' and GCH_COMPLAINT_DATE < '$to_dt 23:59:59' ";
	$one_year_date = add_date($from_dt,-365);
	$pending_dev_col = "sum(if(GTM_CODE in ('T2') AND GLH_LEAD_TYPE in (3, 13), 1, 0)) ";
	$total_pending_col = "sum(if(GTM_CODE='T15' AND GLH_LEAD_TYPE in (3, 13), 1, 0))+sum(if(GTM_CODE='T16' AND GLH_LEAD_TYPE in (3, 13), 1, 0))+sum(if(GTM_CODE in ('T17') AND GLH_LEAD_TYPE in (3, 13), 1, 0))+sum(if(GTM_CODE in ('T2') AND GLH_LEAD_TYPE in (3, 13), 1, 0)) ";
	$resolution_time_devloper_pending_col = "sum(if(position=1 and GTM_CODE in ('T2'), 1, 0))";
	$total_resolution_pending_col = " sum(if(position=1 and GTM_CODE='T15', 1, 0))+sum(if(position=1 and GTM_CODE in ('T17'), 1, 0))+sum(if(position=1 and GTM_CODE in ('T2'), 1, 0))";
	$status_cond = '';
	if($type=='1') {
		$status_cond = " and (GTM_GROUP_ID in (1,2,3,5,9) or GTM_CODE in ('T36')) ";
	} else {
		$status_cond = " and GTM_GROUP_ID in (1,2,3,9) ";
	}
	$query1=" select C.GPM_PRODUCT_ABR as product_name, gpg_version as version,C.GPM_PRODUCT_CODE as pro_code, GCH_PRODUCT_SKEW, ".
			" sum(if(GTM_CODE in ('T2') and if(gdc_mantis_status is null,1,gdc_mantis_status not in (80,90)) and position in (1,5),1,0)) tot_pending_dev, ".
			" sum(if(GTM_CODE in ('T15','T3') and position in (1,5),1,0)) tot_pending_suppoort, ".
			" sum(if(GTM_CODE in ('T17') and position in (1,5),1,0)) tot_pending_dev_suppoort, ".
			" sum(if(GTM_CODE in ('T2') and if(gdc_mantis_status is null,1,gdc_mantis_status not in (80,90)) and position in (5),1,0)) pending_dev_safe, ".
			" sum(if(GTM_CODE in ('T15','T3') and position in (5),1,0)) pending_support_safe, ".
			" sum(if(GTM_CODE in ('T17') and position in (5),1,0)) pending_dev_support_safe, ".
			" sum(if(GTM_CODE in ('T2') and if(gdc_mantis_status is null,1,gdc_mantis_status not in (80,90)) and position in (1),1,0)) pending_dev_resolution, ".
			" sum(if(GTM_CODE in ('T15','T3') and position in (1),1,0)) pending_support_resolution, ".
			" sum(if(GTM_CODE in ('T17') and position in (1),1,0)) pending_dev_support_resolution, ".
			" sum(if(GTM_CODE='T1' $con1, 1, 0)) resolved,  ".
			" sum(if(GTM_CODE in ('T1','T15', 'T16', 'T17', 'T2','T3') $con1 , 1, 0))-sum(if(GTM_CODE='T1' $con1, 1, 0)) as pending_diff, ".
			" sum(if(GTM_CODE in ('T36'),1,0)) as 'patch_update',concat(B.GPM_head_family, pg.gpg_skew) as psk, ".
			" sum(if(GTM_CODE='T16', 1, 0)) 'pending_customer',GCD_PROCESS_EMP,GEM_EMP_NAME from ( " .
			
			" select  gch_complaint_id,  GCH_PRODUCT_CODE,  GCH_PRODUCT_SKEW,  GCH_LEAD_CODE,  GTM_CODE,GCH_COMPLAINT_DATE,GCD_PROCESS_EMP,  " .
			" case when(now() >= GCH_RESTORE_TIME) then '1' when(GCH_RESTORE_TIME is null or now() < GCH_RESTORE_TIME) then '5' ".
			" else '6' end as position,GCH_CALL_TYPE,gdc_mantis_status from  gft_customer_support_hdr hdr ".
			" join gft_customer_support_dtl dtl on ( hdr.GCH_COMPLAINT_ID = dtl.GCD_COMPLAINT_ID ".
			" AND dtl.GCD_ACTIVITY_ID= hdr.GCH_LAST_ACTIVITY_ID) ".
			" left join  gft_complaint_master on(GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE) ".
			" join gft_lead_hdr on (glh_lead_code=gch_lead_code and glh_lead_type in (3,13)) ".
			" join gft_support_product_group on (gsp_group_id=glh_main_product) ".
			" left join gft_dev_complaints on (GDC_COMPLAINT_ID=hdr.GCH_COMPLAINT_ID) ".
			" join  gft_status_master on (gch_current_status=gtm_code $status_cond) ".
			" where GCH_COMPLAINT_DATE>='$one_year_date 00:00:00' and GCH_OWNERSHIP!=1 and glh_main_product=6 ".
			" and gsp_company_id=1) hdr ".
			
			" join gft_product_family_master B on(hdr.GCH_PRODUCT_CODE=B.GPM_PRODUCT_CODE) ".
			" join gft_product_group_master pg on( pg.gpg_product_family_code=B.GPM_head_family AND hdr.GCH_PRODUCT_SKEW =pg.gpg_skew)  ".
			" join gft_product_family_master C on (pg.gpg_product_family_code=C.GPM_PRODUCT_CODE) ".
			" join gft_lead_hdr lh on(lh.glh_lead_code=hdr.GCH_LEAD_CODE) ".
			" left join gft_emp_master on (GEM_EMP_ID=hdr.GCD_PROCESS_EMP) where GLH_MAIN_PRODUCT=6 $lead_condition";
	
	if($type=='1') {
		$query1 .= " group by psk having patch_update+pending_customer+tot_pending_dev+tot_pending_suppoort+tot_pending_dev_suppoort > 0 ";
	} else {
		$query1 .= " group by gem_emp_id having tot_pending_suppoort+tot_pending_dev_suppoort > 0 ";
	}
	return $query1;
}

/**
 * @param string $order_no
 * @param string $cust_id
 * @param string $proforma_no
 * @param string $install_id
 * @param string $quotation_no
 *
 * @return void
 */
function update_dependent_order_dtl($order_no,$cust_id,$proforma_no='',$install_id='',$quotation_no=''){
	global $log;
	$skew='';
	$additional_update='';

	$upgradation_query=/*. (string[int]) .*/ array();
	$ass_dtl=/*. (string[int]) .*/ array();
	$assupdate=/*. (string[int]) .*/ array();
	$sql_orderno_change=/*. (string[int]) .*/ array();
	$order_p_update=/*. (string[int]) .*/ array();

	$emp_id=SALES_DUMMY_ID;
	$query="select GOP_ORDER_NO, GOP_FULLFILLMENT_NO, GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GOP_SELL_RATE, " .
			" GOP_TAX_RATE,GOP_SERVICE_TAX_RATE, GOP_QTY, GOP_DISCOUNT_AMT, GOP_COMMENTS,GOD_ORDER_SPLICT, " .
			" GOP_SELL_AMT, GOP_TAX_AMT,GOP_SERVICE_TAX_AMT, GOP_USEDQTY, GOP_CP_APPROVED,pm.GPM_CLIENTS, " .
			" GOP_CP_USEDQTY,  GOP_APPROVAL_QTY, GOP_APPROVAL_BY, GOP_VALIDITY_DATE,pm.GPM_ORDER_TYPE, " .
			" GOP_APPROVED_DATE, GOP_LIST_PRICE, ass_period, gop_start_date,  gop_ass_end_date, pm.GFT_SKEW_PROPERTY,pm.GPM_REFERER_SKEW,pm.GPM_DEFAULT_ASS_PERIOD,pm.GPM_PRODUCT_TYPE, " .
			" pm.GFT_LOWER_PCODE,pm.GFT_LOWER_SKEW,pm.GFT_HIGHER_PCODE, pm.GFT_HIGHER_SKEW,pm2.GPM_CLIENTS as new, pm3.GPM_CLIENTS as old,".
			" pm.GPM_DEFAULT_ASS_PERIOD, pm.GPM_LICENSE_TYPE, pm.GPM_SUBSCRIPTION_PERIOD,god_tax_mode " .
			" from gft_order_hdr join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) " .
			" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and pm.GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			" left join gft_product_master pm2 on (pm.GFT_HIGHER_SKEW =pm2.GPM_PRODUCT_SKEW and pm.GFT_HIGHER_PCODE=pm2.GPM_PRODUCT_CODE) ".
			" left join gft_product_master pm3 on (pm.GFT_LOWER_SKEW=pm3.gpm_product_skew and pm.GFT_LOWER_PCODE=pm3.gpm_product_code)".
			" where gop_order_no='$order_no' and GOP_PRODUCT_CODE!=706 "; //skipping GoSecure product since it's relevant update are taken care in provision_connectplus function
	$result=execute_my_query($query);
	//print $query;
	$data_install_id="";$install_id_cond="";
	$asa_client_order = false;
	$bought_client = $prorated_clients = 0;
	$ulasa_check = execute_my_query("select GOP_ORDER_NO from gft_order_product_dtl join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and pm.GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			" where GOP_ORDER_NO='$order_no' and (GOP_PRODUCT_SKEW like '%ULASA%' or GFT_SKEW_PROPERTY in (3,16)) ");
	if(mysqli_num_rows($ulasa_check) > 0){
		$asa_client_order = true;
	}
	$main_cond = " and gid_lead_code=$cust_id ";
	$asa_install_id = isset($_COOKIE['asa_install_ids'])?(string)$_COOKIE['asa_install_ids']:'';
	if($asa_install_id!=''){
		$main_cond = " and GID_INSTALL_ID in ($asa_install_id) ";
		unset($_COOKIE['asa_install_ids']);
		setcookie("asa_install_ids",null,time()-3600);
	}
	$no_of_clients = $log_info = $surrendered_clients = "";
	$builtin_clients= 0;
	$glh_lead_type = get_single_value_from_single_table("GLH_LEAD_TYPE", "gft_lead_hdr", "GLH_LEAD_CODE", $cust_id);
	$cst_support_product_code = ''; $cst_support_product_skew = ''; $cst_support_start_date = '';
	while($data=mysqli_fetch_array($result)){
	    $tax_mode = $data['god_tax_mode'];
		$GOP_PRODUCT_CODE = $data['GOP_PRODUCT_CODE'];
		$GOP_PRODUCT_SKEW = $data['GOP_PRODUCT_SKEW'];
		$god_order_splict = $data['GOD_ORDER_SPLICT'];
		$skew_group = substr($GOP_PRODUCT_SKEW,0,4);
		$gop_fullfill_no = $data['GOP_FULLFILLMENT_NO'];
		$product_type = $data['GPM_PRODUCT_TYPE'];
		$clients_to_add = ($product_type=='18') ? (int)$data['GPM_CLIENTS'] : 0;
		$skew_pro = (int)$data['GFT_SKEW_PROPERTY'];
		$GOP_QTY = $data['GOP_QTY'];
		if(in_array($skew_pro, array('3','13'))){
			$bought_client = $GOP_QTY;
		}
		if(array_search($data['GFT_SKEW_PROPERTY'],array(1=>2,3,13,16,15,14,4,11,18))){
			$GOP_CP_USEDQTY=0;$GOP_USEDQTY=0;
			$data_install_id=isset($_COOKIE['prod_install_id'])?(string)$_COOKIE['prod_install_id']:'';
			if($install_id!=''){
				$data_install_id = $install_id;
			}
			if($data_install_id != ""){
			    $install_id_cond="and GID_INSTALL_ID=$data_install_id";
			}
			$ref_install_id = "";
			if($proforma_no!=''){
				$ref_install_id = get_single_value_from_single_table("GPH_REF_INSTALL_ID","gft_proforma_hdr","GPH_ORDER_NO","$proforma_no");
			}else if($quotation_no!=''){
				$ref_install_id = get_single_value_from_single_table("GQH_REF_INSTALL_ID","gft_quotation_hdr","GQH_ORDER_NO","$quotation_no");
			}
			if($ref_install_id!=''){
				$install_id_cond = " and GID_INSTALL_ID in ($ref_install_id) ";
				$main_cond = ""; // if from proforma and quotation
			}
			$data_que = "select GID_ORDER_NO,GID_FULLFILLMENT_NO, GID_VALIDITY_DATE, GID_LIC_ORDER_NO, GID_LIC_PCODE,GID_EXPIRE_FOR, " .
					"GID_LIC_PSKEW, GID_LIC_FULLFILLMENT_NO, GID_INSTALL_ID,GID_HEAD_OF_FAMILY,GID_PRODUCT_CODE,GID_PRODUCT_SKEW,GID_NO_CLIENTS,GID_LEAD_CODE from gft_install_dtl_new " .
					"where 1 $main_cond  and GID_LIC_PCODE=$GOP_PRODUCT_CODE  $install_id_cond";
			$log->logInfo(" ==storealrlog== $cust_id ** ".$data_que. " **** ".json_encode($_COOKIE));
			$result1=execute_my_query($data_que);
			if(mysqli_num_rows($result1) == 0){ //no installation found
			    if($product_type=='8'){
			        $root_order = $root_fullfill = "";
			        $gop_query =" select GOP_ORDER_NO order_no,GOP_FULLFILLMENT_NO fullfillment_no from gft_order_hdr ".
			 			        " join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
			 			        " join gft_product_master on (GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			 			        " where GOD_ORDER_SPLICT=0 and GOD_LEAD_CODE='$cust_id' and GFT_SKEW_PROPERTY in (1,11) and GPM_PRODUCT_TYPE!=8 ".
			 			        " and GPM_LICENSE_TYPE in (1,2) and GOP_PRODUCT_CODE='$GOP_PRODUCT_CODE' and GOP_PRODUCT_SKEW like '$skew_group%' ";
			        $gco_query =" select GCO_ORDER_NO order_no,GCO_FULLFILLMENT_NO fullfillment_no from gft_order_hdr ".
			 			        " join gft_cp_order_dtl on (GCO_ORDER_NO=GOD_ORDER_NO) ".
			 			        " join gft_product_master on (GCO_PRODUCT_CODE=GPM_PRODUCT_CODE and GCO_SKEW=GPM_PRODUCT_SKEW) ".
			 			        " where GOD_ORDER_SPLICT=1 and GCO_CUST_CODE='$cust_id' and GFT_SKEW_PROPERTY in (1,11) and GPM_PRODUCT_TYPE!=8 ".
			 			        " and GPM_LICENSE_TYPE in (1,2) and GCO_PRODUCT_CODE='$GOP_PRODUCT_CODE' and GCO_SKEW like '$skew_group%'";
			        $chk_res = execute_my_query("select order_no,fullfillment_no from ($gop_query) gop union all ($gco_query) ");
			        if(mysqli_num_rows($chk_res)==1){
			            $chk_data 		= mysqli_fetch_array($chk_res);
			            $root_order 	= $chk_data['order_no'];
			            $root_fullfill 	= $chk_data['fullfillment_no'];
			            execute_my_query("update gft_order_product_dtl set GOP_REFERENCE_ORDER_NO='$root_order', GOP_REFERENCE_FULLFILLMENT_NO='$root_fullfill' ".
			                "where GOP_ORDER_NO='$order_no' and GOP_FULLFILLMENT_NO='$gop_fullfill_no' ");
			        }
			    }
			}else{
			while($data1=mysqli_fetch_array($result1)){
				$ref_order_no=$data1['GID_ORDER_NO'];
				$ref_fullfill_no=$data1['GID_FULLFILLMENT_NO'];
				$ref_pcode = $data1['GID_PRODUCT_CODE'];
				$ref_pskew = $data1['GID_PRODUCT_SKEW'];
				$gid_lead_code = $data1['GID_LEAD_CODE'];

				switch($skew_pro){
					case 2:
						if($data['GPM_DEFAULT_ASS_PERIOD']!='' and $data['GPM_DEFAULT_ASS_PERIOD']!=0){
							$additional_update=",GID_VALIDITY_DATE=DATE_ADD(now(),INTERVAL ".$data['GPM_DEFAULT_ASS_PERIOD']." DAY)," .
									" GID_PREV_EXPIRY_DATE=GID_VALIDITY_DATE ";
						}
						if($data1['GID_LIC_PCODE']==$data['GFT_LOWER_PCODE'] and $data['GFT_LOWER_SKEW']==$data1['GID_LIC_PSKEW']){
							$old_head_of_family=$data1['GID_HEAD_OF_FAMILY'];
							$new_head_of_family=get_head_of_family($data['GFT_HIGHER_PCODE']);

							$new_expire_for_value=get_expire_for_value($data['GFT_HIGHER_PCODE'],$data['GFT_HIGHER_SKEW']);
							$clients=(int)$data['new']-(int)$data['old'];
							$new_fullfillment_no=1;
							$sql_orderno_change[]=" UPDATE gft_install_dtl_new " .
									" SET GID_NO_CLIENTS=GID_NO_CLIENTS+$clients,GID_LIC_ORDER_NO='$order_no',GID_LIC_PCODE='".$data['GFT_HIGHER_PCODE']."',GID_LIC_PSKEW='".$data['GFT_HIGHER_SKEW']."'," .
									" GID_LIC_FULLFILLMENT_NO='$new_fullfillment_no'," .
									" GID_EXPIRE_FOR='$new_expire_for_value'," .
									" GID_UPGRADATION_DONE='N' $additional_update,GID_HEAD_OF_FAMILY=$new_head_of_family  " .
									" WHERE GID_INSTALL_ID=".$data1['GID_INSTALL_ID']." and gid_status!='U' ";
							$GOP_CP_USEDQTY++;
							$GOP_USEDQTY++;
							$upgradation_query[]="update gft_order_product_dtl set GOP_LICENSE_STATUS=2,GOP_REFERENCE_ORDER_NO='$ref_order_no',GOP_REFERENCE_FULLFILLMENT_NO='$ref_fullfill_no' where ".
									" GOP_ORDER_NO = '$order_no' and GOP_FULLFILLMENT_NO = '$new_fullfillment_no' and GOP_PRODUCT_CODE='$GOP_PRODUCT_CODE'";
							$upgradation_query[]="update gft_order_product_dtl set GOP_LICENSE_STATUS=2 where GOP_ORDER_NO='$ref_order_no' and ".
									" GOP_FULLFILLMENT_NO='$ref_fullfill_no' ";
							$upgradation_query[]=insert_smt_upgrade_dtl($order_no,$cust_id,$data['GOP_PRODUCT_CODE'],$data['GOP_PRODUCT_SKEW'],
									$data['GFT_LOWER_PCODE'],$data['GFT_LOWER_SKEW'],$new_fullfillment_no,$data['GFT_HIGHER_PCODE'],$data['GFT_HIGHER_SKEW'],
									$new_fullfillment_no,$data1['GID_ORDER_NO'],$new_head_of_family,$data1['GID_FULLFILLMENT_NO']);
							if($god_order_splict=='1'){
								$gco_fullfill = get_max_fullfillment_no($order_no);
								$upgradation_query[] = " insert into gft_cp_order_dtl (GCO_CP_LEAD_CODE,GCO_CUST_CODE,GCO_CUST_QTY,GCO_USEDQTY,GCO_ORDER_NO,GCO_PRODUCT_CODE, ".
										" GCO_SKEW,GCO_ORDERED_DATE,GCO_CREATED_DATE,GCO_LIQUIDATED_BY,GCO_FULLFILLMENT_NO,GCO_HEAD_OF_FAMILY ) values ".
										" ('$cust_id','$gid_lead_code',1,1,'$order_no','$GOP_PRODUCT_CODE', ".
										" '$GOP_PRODUCT_SKEW',now(),now(),9999,'$gco_fullfill','$new_head_of_family') ";
							}
								
							$upgradation_query[]= get_insert_query_for_lic_approval_log($cust_id,$ref_order_no,$ref_fullfill_no,$ref_pcode,$ref_pskew,"Store Upgradation",2);
						}
						break;
					case 3:
					case 13:
					case 14:
					case 16:
					case 11:
						update_order_license_given_dtl($cust_id,$data['GOP_PRODUCT_CODE'],0,null,$data_install_id,$order_no);
						if($product_type=='8'){ //custom license update
							$install_sql=" select GID_ORDER_NO,GID_FULLFILLMENT_NO from gft_install_dtl_new ".
									" join gft_product_master on (GID_LIC_PCODE=GPM_PRODUCT_CODE and GID_LIC_PSKEW=GPM_PRODUCT_SKEW and GPM_LICENSE_TYPE in (1,2)) ".
									" where GID_LEAD_CODE='$cust_id' and GID_LIC_PCODE='$GOP_PRODUCT_CODE' and GID_LIC_PSKEW like '$skew_group%' and GID_STATUS!='U' ";
							$install_res = execute_my_query($install_sql);
							$install_rows = mysqli_num_rows($install_res);
							$root_order = $root_fullfill = "";
							if($install_rows==1){
								$chk_data 		= mysqli_fetch_array($install_res);
								$root_order 	= $chk_data['GID_ORDER_NO'];
								$root_fullfill 	= $chk_data['GID_FULLFILLMENT_NO'];
							}else if($install_rows==0){
								/* $gop_query =" select GOP_ORDER_NO order_no,GOP_FULLFILLMENT_NO fullfillment_no from gft_order_hdr ".
										" join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
										" join gft_product_master on (GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
										" where GOD_ORDER_SPLICT=0 and GOD_LEAD_CODE='$cust_id' and GFT_SKEW_PROPERTY in (1,11) and GPM_PRODUCT_TYPE!=8 ".
										" and GPM_LICENSE_TYPE in (1,2) and GOP_PRODUCT_CODE='$GOP_PRODUCT_CODE' and GOP_PRODUCT_SKEW like '$skew_group%' ";
								$gco_query =" select GCO_ORDER_NO order_no,GCO_FULLFILLMENT_NO fullfillment_no from gft_order_hdr ".
										" join gft_cp_order_dtl on (GCO_ORDER_NO=GOD_ORDER_NO) ".
										" join gft_product_master on (GCO_PRODUCT_CODE=GPM_PRODUCT_CODE and GCO_SKEW=GPM_PRODUCT_SKEW) ".
										" where GOD_ORDER_SPLICT=1 and GCO_CUST_CODE='$cust_id' and GFT_SKEW_PROPERTY in (1,11) and GPM_PRODUCT_TYPE!=8 ".
										" and GPM_LICENSE_TYPE in (1,2) and GCO_PRODUCT_CODE='$GOP_PRODUCT_CODE' and GCO_SKEW like '$skew_group%'";
								$chk_res = execute_my_query("select order_no,fullfillment_no from ($gop_query) gop union all ($gco_query) ");
								if(mysqli_num_rows($chk_res)==1){
									$chk_data 		= mysqli_fetch_array($chk_res);
									$root_order 	= $chk_data['order_no'];
									$root_fullfill 	= $chk_data['fullfillment_no'];
								} */
							}
							if( ($root_order!='') && ($root_fullfill!='') ){
								execute_my_query("update gft_order_product_dtl set GOP_REFERENCE_ORDER_NO='$root_order', GOP_REFERENCE_FULLFILLMENT_NO='$root_fullfill' ".
										"where GOP_ORDER_NO='$order_no' and GOP_FULLFILLMENT_NO='$gop_fullfill_no' ");
							}
						}else{
							$log_query = get_insert_query_for_lic_approval_log($cust_id,$ref_order_no,$ref_fullfill_no,$ref_pcode,$ref_pskew,"Store Subscription",2);
							execute_my_query($log_query);
						}
						break;
					case 4:
					case 15:
						$intallable_reff_skew=$data['GPM_REFERER_SKEW'];
						$gpm_order_type = $data['GPM_ORDER_TYPE'];
						$orderfullfill = $ref_order_no.(string)substr("0000".$ref_fullfill_no,-4);
						if($data['GOP_PRODUCT_CODE']==$data1['GID_LIC_PCODE']){
							$gid_validity_date = $data1['GID_VALIDITY_DATE'];
							// Get installed validity date, to update same date for prorated skew
							$intallable_reff_skew = $gpm_order_type = ""; 
							$ique = " SELECT ifnull(p.GPM_REFERER_SKEW,t.GPM_REFERER_SKEW) ref_sku,t.GPM_ORDER_TYPE,if(p.GPM_PRORATE_SKEW is null,0,1) prorate_sku FROM gft_product_master t ".
											" left join gft_product_master p on (p.GPM_PRODUCT_CODE=t.GPM_PRODUCT_CODE and p.GPM_PRORATE_SKEW=t.GPM_PRODUCT_SKEW) ".
											" WHERE t.GPM_PRODUCT_CODE=$GOP_PRODUCT_CODE AND t.GPM_PRODUCT_SKEW='$GOP_PRODUCT_SKEW' " ;
							$result_reffskew=execute_my_query($ique);
							$validity_date = " DATE_ADD(GID_VALIDITY_DATE,INTERVAL ".$data['GPM_DEFAULT_ASS_PERIOD']." DAY) ";
							if($data_reffskew=mysqli_fetch_array($result_reffskew)){
							    $intallable_reff_skew=$data_reffskew['ref_sku'];
							    $gpm_order_type = $data_reffskew['GPM_ORDER_TYPE'];
							    if($data_reffskew['prorate_sku']=='1'){
							        $ASS_START_DATE = get_base_product_expiry_date($gid_lead_code);
							        $validity_date = " DATE_ADD('$ASS_START_DATE',INTERVAL ".$data['GPM_DEFAULT_ASS_PERIOD']." DAY) " ;
							    }
							}
							// end of the validity date logic
							if($intallable_reff_skew==$data1['GID_LIC_PSKEW']){
								$asa_type=((int)$data['GFT_SKEW_PROPERTY']==15?3:1);
								$assupdate[]="update gft_install_dtl_new set " .
										" GID_PREV_EXPIRY_DATE='".$data1['GID_VALIDITY_DATE']."'," .
										" GID_VALIDITY_DATE = $validity_date , " .
										" GID_ASS_ID='$order_no',GID_EXPIRE_FOR=$asa_type " .
										" where GID_STATUS!='U' and GID_INSTALL_ID='".$data1['GID_INSTALL_ID']."' ";
								$ass_dtl[]="insert into gft_ass_dtl (GAD_ASS_DATE,  GAD_ASS_START_DATE, GAD_ASS_END_DATE, GAD_EMP_ID, GAD_PRODUCT_SKEW,GAD_PRODUCT_CODE, GAD_ASS_ORDER_NO,GAD_INS_REFF ) " .
										" values(date(now()),'$gid_validity_date',DATE_ADD('$gid_validity_date',INTERVAL ".$data['GPM_DEFAULT_ASS_PERIOD']." DAY),'$emp_id','$skew','$GOP_PRODUCT_CODE','$order_no','".$data1['GID_INSTALL_ID']."')" ;
								$GOP_USEDQTY++;
								$ref_order_no=$data1['GID_ORDER_NO'];
								if(in_array($data['GOP_PRODUCT_CODE'], array('200','500'))){
								    if( ($gpm_order_type=='0') && !$asa_client_order ){
								        $no_of_clients 		= '0';
								        $surrendered_clients= $data1['GID_NO_CLIENTS'];
								        $log_info 			= $data1['GID_INSTALL_ID']."-$orderfullfill-".$data1['GID_LIC_PCODE']."-".$data1['GID_LIC_PSKEW']."-".$data1['GID_LEAD_CODE']."-".$data1['GID_NO_CLIENTS'];
								    }
								    $builtin_clients = $clients_to_add;
								}
								if($glh_lead_type=='1' and $asa_type=='3' and $tax_mode=='4') {
								    $cst_support_product_code = $data['GOP_PRODUCT_CODE'];
								    $cst_support_product_skew = $intallable_reff_skew;
								    $cst_support_start_date = (strtotime($data1['GID_VALIDITY_DATE']." 00:00:00")>strtotime(date('Y-m-d 00:00:00'))?$data1['GID_VALIDITY_DATE']:date('Y-m-d'));
								}
							}else if( ($proforma_no!='') && ($bought_client==0) ){
							    if(is_prorated_client_alr_skew($GOP_PRODUCT_SKEW)){
							        $prorated_clients = $GOP_QTY;
							    }else if(strpos($data['GOP_PRODUCT_SKEW'],"ULASA") > 0){
							        $actual_clients  = $data1['GID_NO_CLIENTS'];
							        $no_of_clients = $GOP_QTY;
							        if((int)$no_of_clients > (int)$actual_clients){ //this shouldn't happen. Extra check added to prevent updating higher clients than actual
							            $no_of_clients = $actual_clients;
							        }
							        $surrendered_clients = $actual_clients - $GOP_QTY;
							        $log_info 			= $data1['GID_INSTALL_ID']."-$orderfullfill-".$data1['GID_LIC_PCODE']."-".$data1['GID_LIC_PSKEW']."-".$data1['GID_LEAD_CODE']."-".$data1['GID_NO_CLIENTS'];
							        $GOP_USEDQTY = $GOP_USEDQTY + $GOP_QTY;
							    }
							}
							$GOP_CP_USEDQTY++;
						}
						break;
					case 18:
						$additional_update=",GID_VALIDITY_DATE=DATE_ADD(if(GID_VALIDITY_DATE > now(),GID_VALIDITY_DATE,now()),INTERVAL ".$data['GPM_SUBSCRIPTION_PERIOD']." DAY)," .
								" GID_PREV_EXPIRY_DATE=GID_VALIDITY_DATE ";
						$sql_orderno_change[]=" UPDATE gft_install_dtl_new " .
								" SET GID_LIC_ORDER_NO='$order_no',GID_LIC_PSKEW='".$data['GOP_PRODUCT_SKEW']."'," .
								" GID_LIC_FULLFILLMENT_NO='".$data['GOP_FULLFILLMENT_NO']."' $additional_update" .
								" WHERE GID_INSTALL_ID=".$data1['GID_INSTALL_ID']." and gid_status!='U' ";
						break;
				}
				if(!in_array($skew_pro, array(3,13,14,16,11))) {  //not in skew property
					$order_p_update[]="update gft_order_product_dtl set GOP_CP_USEDQTY=$GOP_CP_USEDQTY, GOP_USEDQTY=$GOP_USEDQTY where gop_order_no='$order_no' and gop_product_code=".$data['GOP_PRODUCT_CODE']." and gop_product_skew='".$data['GOP_PRODUCT_SKEW']."'";
				}
			}
			}
		}
	}
	if($cst_support_product_code!='' and $cst_support_product_skew!='' and $cst_support_start_date!='') {
	    assign_palr_support_tickets($cust_id,$cst_support_product_code,$cst_support_product_skew,$cst_support_start_date);
	}
	for($i=0;$i<count($upgradation_query);$i++){
		$result=execute_my_query($upgradation_query[$i],'order_submit.php',true,true);
	}
	for($i=0;$i<count($sql_orderno_change);$i++){
		$result=execute_my_query($sql_orderno_change[$i],'order_submit.php',true,true);
	}
	for($i=0;$i<count($assupdate);$i++){
		$result=execute_my_query($assupdate[$i],'order_submit.php',true,true);
	}
	for($i=0;$i<count($ass_dtl);$i++){
		$result=execute_my_query($ass_dtl[$i],'order_submit.php',true,true);
	}
	$gph_surrendered_clients=0;
	if( ($proforma_no!='') && ($glh_lead_type==1) && $asa_client_order ){
	    $gph_surrendered_clients = get_single_value_from_single_query("GPH_SURRENDERED_CLIENTS", "select GPH_SURRENDERED_CLIENTS from gft_proforma_hdr where GPH_ORDER_NO='$proforma_no'");
	}
	if($gph_surrendered_clients>0){
	    if( ($no_of_clients>=0) && ($log_info!='') && $actual_clients>=0){
	        $log_arr = explode('-', $log_info);
	        $no_clients = $actual_clients + $gph_surrendered_clients;
	        $old_sur_clnt =get_single_value_from_single_query("GID_SURRENDERED_CLIENTS", " select GID_SURRENDERED_CLIENTS from gft_install_dtl_new where GID_INSTALL_ID='$log_arr[0]' ");
	        $new_sur_clnt = $old_sur_clnt - $gph_surrendered_clients;
	        $update_que = "update gft_install_dtl_new set GID_NO_CLIENTS='$no_clients', GID_SURRENDERED_CLIENTS=$new_sur_clnt where GID_INSTALL_ID='$log_arr[0]' ";
	        execute_my_query($update_que);
	        
	        $cur_time = date('Y-m-d H:i:s');
	        execute_my_query(" insert into gft_audit_log_edit_table (GUL_AUDIT_TABLE,GUL_AUDIT_KEY_FIELDS,GUL_AUDIT_COLUMNS,GUL_AUDIT_OLD_VALUES,GUL_AUDIT_NEW_VALUES,GUL_PAGE,GUL_TIME,GUL_USER_ID,GUL_REMARKS) ".
	            " values ('gft_install_dtl_new','GID_INSTALL_ID=$log_arr[0]','GID_NO_CLIENTS',$actual_clients,$no_clients,'order_submit.php','$cur_time',$emp_id,'Proforma to order conversion') , ".
	            " ('gft_install_dtl_new','GID_INSTALL_ID=$log_arr[0]','GID_SURRENDERED_CLIENTS',$old_sur_clnt,$new_sur_clnt,'order_submit.php','$cur_time',$emp_id,'Proforma to order conversion') ");
	        
	    }
	}else{
    	if( ($proforma_no!='') && ($glh_lead_type!=3) && ($log_info!='') ){
    		$proforma_date = get_single_value_from_single_table("GPH_ORDER_DATE", "gft_proforma_hdr", "GPH_ORDER_NO", $proforma_no);
    		if(strtotime($proforma_date) > strtotime('2016-03-25')){
    			$log_arr = explode('-', $log_info);
    			$surrendered_clients = $surrendered_clients - $prorated_clients - $builtin_clients;
    			$no_of_clients = $no_of_clients + $prorated_clients + $builtin_clients;
    			if($surrendered_clients > 0){
    			    execute_my_query("update gft_install_dtl_new set GID_NO_CLIENTS='$no_of_clients', GID_SURRENDERED_CLIENTS=GID_SURRENDERED_CLIENTS+$surrendered_clients where GID_INSTALL_ID='$log_arr[0]'");
    				$ins_log=" insert into gft_lic_surrender (GLS_PURPOSE,GLS_ORDER_WITH_FILL_NO,GLS_PRODUCT_CODE,GLS_PRODUCT_SKEW,GLS_LEAD_CODE,GLS_SUR_STATUS,GLS_CODE_STATUS,GLS_CREATE_DATE,GLS_UPDATE_DATE,GLS_CURRENT_CLIENTS,GLS_SURRENDERED_CLIENTS,GLS_REASON) ".
    						" values ('3','$log_arr[1]','$log_arr[2]','$log_arr[3]','$log_arr[4]','Y','I',now(),now(),'$log_arr[5]','$surrendered_clients','Client surrendered by Proforma to Order conversion via Store') ";
    				execute_my_query($ins_log);
    			}
    		}
    	}
	}
	for($i=0;$i<count($order_p_update);$i++){
		$result=execute_my_query($order_p_update[$i],'order_submit.php',true,true);
	}
}
/**
 * @param string $order_no
 * @param string $cust_id
 * 
 * @return void
 */
function update_gosecure_addon_plan_dtl($order_no,$cust_id){
	$resultInstallDtl = execute_my_query("select GID_LIC_PCODE, GID_LIC_PSKEW from gft_install_dtl_new where GID_LEAD_CODE='$cust_id'  AND GID_LIC_PCODE='706' AND GID_STATUS='A'");
	$purchasedAddonQuery = "";
	$canUpdatePreviousActiveAddon = false;
	if($row=mysqli_fetch_assoc($resultInstallDtl)){
		$planProductCode = $row['GID_LIC_PCODE'];
		$planProductSkew = $row['GID_LIC_PSKEW'];
		$resultOrder = execute_my_query("select GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW, GFT_SKEW_PROPERTY,GOP_QTY  from gft_order_hdr".
				" join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
				" join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW)".
				" where GOD_ORDER_NO='$order_no' and GOP_PRODUCT_CODE in (706)");
		while($rowGosecure=mysqli_fetch_assoc($resultOrder)){
		    $pqty = $rowGosecure['GOP_QTY'];
		    $prod_code_ref = $rowGosecure['GOP_PRODUCT_CODE']."-";
		    execute_my_query("delete from gft_addon_plan_suggestion_to_customer where GAP_LEAD_CODE='$cust_id' and GAP_PRODUCT_CODE like '$prod_code_ref%' ");
			if($rowGosecure['GFT_SKEW_PROPERTY']=='17'){//Additional addon
				$purchasedAddonQuery .= "('$cust_id', '".$rowGosecure['GOP_PRODUCT_CODE']."','".$rowGosecure['GOP_PRODUCT_SKEW']."','$pqty','Y',NOW()),";
			}else if($planProductSkew!=$rowGosecure['GOP_PRODUCT_SKEW']){
				$canUpdatePreviousActiveAddon = true;
			}
		}
	}
	if($canUpdatePreviousActiveAddon){
		execute_my_query("UPDATE gft_customer_purchased_addon SET GCP_IS_ACTIVE_PLAN='N', GCP_LAST_UPDATE=NOW() WHERE GCP_LEAD_CODE='$cust_id'");
	}
	if($purchasedAddonQuery!=""){
		execute_my_query("INSERT INTO gft_customer_purchased_addon(GCP_LEAD_CODE,GCP_PRODUCT_CODE,GCP_PRODUCT_SKEW,GCP_QUANTITY,GCP_IS_ACTIVE_PLAN,GCP_LAST_UPDATE)".
						" VALUES".trim($purchasedAddonQuery,','));
	}	
}
/**
* @param string $order_no_collection
* @param string $emp_id
* @param string $receipt_id
* 
* @return void
*/
function outstanding_incentive($order_no_collection,$emp_id,$receipt_id){
    $odr_Dt_que = execute_my_query("select GOD_ORDER_NO from gft_order_hdr where GOD_ORDER_NO='$order_no_collection' and GOD_ORDER_DATE < '2018-03-01' ");
    if($data_date=mysqli_fetch_array($odr_Dt_que)){
        $perc_arr = get_attribute_percent_in_array($order_no_collection);
        $attr_id = 12;
        $ins_arr = array(
            'GOI_ORDER_NO'=>$order_no_collection,
            'GOI_ACTIVITY_ID'=>$receipt_id,
            'GOI_INCENTIVE_TYPE'=>'2',
            'GOI_ATTRIBUTE_ID'=>$attr_id,
            'GOI_ATTRIBUTE_PERCENT'=>$perc_arr[$attr_id],
            'GOI_OWNER_EMP'=>$emp_id,
            'GOI_CREATED_DATE'=>date('Y-m-d H:i:s')
        );
        array_insert_query("gft_orderwise_incentive_owner", $ins_arr); // Insert function
    }
}

/**
 * @param string $group_id
 * @param string $role_id
 *
 * @return string[int][int]
 */
function get_emp_list_by_group_and_role($group_id,$role_id){
    $query= " select a.gem_emp_id id,a.gem_emp_name name from gft_emp_master a ".
        " left join gft_role_group_master rg on (grg_role_id=gem_role_id) ".
        " left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id) ".
        " left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id) ".
        " where gem_status='A' and (ggm_group_id in ($group_id) or gem_role_id in ($role_id))  and a.gem_emp_id<9998  group by a.gem_emp_id ";
    $result = execute_my_query($query);
    $emp_list = array();
    $i=0;
    while($row=mysqli_fetch_assoc($result)){
        $emp_list[$i][0] =  $row['id'];
        $emp_list[$i][1] =  $row['name'];
        $i++;
    }
    return $emp_list;
}
/**
 * @param string $pcodeskew
 * 
 * @return string[int]
 */
function validate_to_allow_skew_discount($pcodeskew){
	$skew_name = array();
	$prod_discount = execute_my_query("select GPM_DISPLAY_NAME from gft_product_master where ".
	" CONCAT(GPM_PRODUCT_CODE,'-',GPM_PRODUCT_SKEW) IN($pcodeskew) and GPM_ALLOW_DISCOUNT=1");
	while($row = mysqli_fetch_array($prod_discount)){
		$skew_name[] = $row['GPM_DISPLAY_NAME'];
	}
	return $skew_name;
}
/**
 * @param string $lead_code
 * @return boolean
 */
function check_gstin_update_needed($lead_code) {
    $update = false;
    $country = get_single_value_from_single_table('glh_country','gft_lead_hdr','glh_lead_code',$lead_code);
    if($country=='India') {
        $gst_qry = " select gle_gst_no, gle_gst_eligible,glh_country from gft_lead_hdr_ext ".
            " join gft_lead_hdr on (gle_lead_code=glh_lead_code) where gle_lead_code='$lead_code' ";
        $res = execute_my_query($gst_qry);
        $gst_no = $gst_eligible = $country = '';
        if($row = mysqli_fetch_array($res)) {
            $gst_no = $row['gle_gst_no'];
            $gst_eligible = $row['gle_gst_eligible'];
            $country = $row['glh_country'];
        }
        if(($gst_no=='' or $gst_no=='0') and $gst_eligible!='2' and $country=='India') {
            $update = true;
        }
    }
    return $update;
}
/**
 * @param string $type
 * 
 * @return string
 */
function get_gstin_validation_alert_msg($type){
    $message = "";
    if($type=='quotation'){
        $message = "Customer is not updated the GSTIN. You can only create quotation but cannot create new orders.".
            " Please communicate to the customer to update the GSTIN in myGOFRUGAL app and only after".
            " that you will be able to punch the new order.";
    }else if($type=="proforma"){
        $message = "Customer is not updated the GSTIN. You can only create proforma but cannot create new orders.".
            " Please communicate to the customer to update the GSTIN in myGOFRUGAL app and only after".
            " that you will be able to punch the new order.";
    }else if($type=="collection"){
        $message = "Please inform Customer to update the GSTIN status in myGOFRUGAL app and then create the collection entry or order.";
    }
    return $message;
}
?>
