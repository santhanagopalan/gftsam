<?php
$use_jquery = 1;
require_once(__DIR__ ."/inc.essentials_for_popup.php");
require_once(__DIR__ ."/common_util.php");
require_once(__DIR__ ."/audit_util.php");
require_once(__DIR__ ."/receipt_util.php");
require_once(__DIR__ ."/quote_util.php");
//global $formtype;
$include_pcs = false; //disabled temporarily. Set true to enable.
$god_order_type=0;
$balance_amt=0;
$refference_partner_name='';
$order_status='';
$splict_cust=/*. (string[int]) .*/ array();
$splict_order=0;
$lead_type=0;
$GOD_DISCOUNT_ADJ_AMT = 0.00;
$god_percentage = 0.00;
$service_type=$expense_type='0';
$collection_realized_amt=0.0;
$custom_remarks = $lead_subtype = '';
$today=date('Y-m-d');


$taxvat='';
$hq_delivery_type = 0;
$date_select_icon_image='<img alt="" src="images/date_time.gif" class="imagecur" width="16" height="16" border="0" align="middle">';
$formtype=(isset($_GET['formtype'])?(string)$_GET['formtype']:'new');
$list_discount_master=get_two_dimensinal_array_from_query("SELECT GRD_REASON_CODE,GRD_REASON FROM gft_reason_for_discount_master where GRD_REASON_CODE > 0 and GRD_STATUS='A' ",'GRD_REASON_CODE','GRD_REASON');
$date_on=date('Y-m-d');
$order_type=(isset($_GET['ordertype'])?(string)$_GET['ordertype']:'');
$order_catagory=(isset($_GET['order_catagory'])?(int)$_GET['order_catagory']:0);
$vn=(isset($_GET['vn'])?(int)$_GET['vn']:0);
$install_desible="disabled";
$server_desible="";$refference_commission='';
$advanceASA_desible="";
$service_order=false;
include_javascript_variables(3);
$js_files  = include_js_file("js/js_receipt_1.js");
$js_files .= include_js_file("js/js_order_details_1.js");
$js_files .= include_js_file("js/js_receipt_util.js");
$js_files .= include_js_file("js/js_product_util_1.js");
echo<<<END
$js_files
<script>
var disable_mode=0;
var enable_service=0;
jQuery.noConflict();	
jQuery(document).ready(function(){
	if(disable_mode==1){
		jQuery("#order_catagory0").attr("disabled",true).addClass("from_quote");
		jQuery("#order_catagory1").attr("disabled",true).addClass("from_quote");
		jQuery("#order_catagory2").attr("disabled",true).addClass("from_quote");
		jQuery("#order_catagory13").attr("disabled",true).addClass("from_quote");
		if(enable_service==0) {
			jQuery("#order_catagory12").attr("disabled",true).addClass("from_quote");
		}
		jQuery("#order_catagory5").attr("disabled",true).addClass("from_quote");
		jQuery("#advance_ass_order").attr("disabled",true).addClass("from_quote");
	}
});
</script>
END;
$title="Enter Order Details";
$approval_days="30";
require_once(__DIR__ ."/get_order_details_for_edit.php");
$is_dealer = false;
if($dealer==1){
	$is_dealer = true;
}
$skip_erp = 0;
$skip_erp_lead_arr = explode(",", get_samee_const("ERP_LEADS"));
if(in_array($lead_code, $skip_erp_lead_arr) || is_authorized_group($uid, 36)){
	$skip_erp = 1;
}
echo "<script>var skip_erp='$skip_erp';</script>";

$from_quotation=false;
$readonly_attr = $disable_attr = $disable_class = "";
$quotation_no = isset($_GET['quotation_no'])?(string)$_GET['quotation_no']:'';
$proforma_no = isset($_GET['proforma_no'])?(string)$_GET['proforma_no']:'';
$listofcat=explode(',',$arrcategory);
$listofcat=array_unique($listofcat);
$isonly_asa_order=false;
if(count($listofcat)==1 && isset($listofcat[0]) && $listofcat[0]==2){
	$isonly_asa_order=true;
}
$show_subscription_skew=check_lead_type_to_show_subscription_skew($lead_code);

$ref_partner_dtl 	= get_referral_partner_details($lead_code);
$ref_partner_code 	= isset($ref_partner_dtl['partner_lead_code'])?$ref_partner_dtl['partner_lead_code']:''; 
$ref_partner_name 	= isset($ref_partner_dtl['partner_name'])?$ref_partner_dtl['partner_name']:'';
$commission_percent = isset($ref_partner_dtl['commission_percent'])?$ref_partner_dtl['commission_percent']:'';
$ref_partner_msg = "";
if($ref_partner_name!=""){
	$ref_partner_msg = " Reference By <b>$ref_partner_name</b> . A Referral commission of <b>$commission_percent %</b> will be processed after the collection";
}
$quote_lic_cost = 1; //to allow when orders not placed from quotation
if($quotation_no!=''){
    if($approval_status!='2' && $discount_reason_code<8){
		$addt_msg = "Contact $rep_mgr";
		if($approval_status=='5'){
			$addt_msg = "Contact Sales Management Team";
		}
		show_my_alert_msg("Quotation yet to be approved. $addt_msg");
		close_the_popup();exit;
	}
	$asa_status_text=get_asa_expiry_status_for_base_product($lead_code,true,"order");
	if(!$show_subscription_skew && $asa_status_text!="" && !is_authorized_group_list($uid,array(54))){//Only for end user lead type.
		show_my_alert_msg("$asa_status_text");
		close_the_popup();exit;
	}
	$quote_lic_cost = get_license_cost_in_quotation($quotation_no);
	$from_quotation = true; 
	$readonly_attr = "readonly";
	$disable_attr	= "disabled";
	$disable_class  = "from_quote";
	echo "<script>disable_mode=1</script>";
}else if( ($proforma_no!='') || ($is_dealer && ($lead_subtype==12) ) ){
	$from_quotation = true;
	$readonly_attr = "readonly";
	$disable_attr	= "disabled";
	$disable_class  = "from_quote";
	echo "<script>disable_mode=1</script>";
}
$only_active = true;
$invoice_status = '';
if($formtype=='edit'){
	$invoice_status = get_single_value_from_single_table("god_invoice_status", "gft_order_hdr", "god_order_no", $order_no);
	$show_subscription_skew=true;
	$only_active = false;
}
$pc_details	=	check_product_delivery_status_and_get_pc_name($lead_code);
$pc_id		=	isset($pc_details[0])?$pc_details[0]:null;
$pc_name	=	isset($pc_details[1])?$pc_details[1]:null;
$query_pay_code="select concat(GPT_PAYMENT_CODE,'-',GPT_ONORDER_PERC) pay_code, GPT_PAYMENT_DESC from gft_payment_terms_master where GPT_STATUS='A' ";
if($is_dealer){
	$query_pay_code .= " and GPT_PAYMENT_CODE=1 ";
}
$payment_code=get_two_dimensinal_array_from_query($query_pay_code,'pay_code','GPT_PAYMENT_DESC');
if(!$is_dealer){
	if(is_authorized_group($uid,101) || ($lead_type=="3") ){
		array_push($payment_code, array('2-0.00','100% Delivery Payment'));
	}else if(get_customer_installation_status($lead_code)){
		array_push($payment_code, array('2-0.00','100% Delivery Payment'));
	}
}

if(!isset($_GET['formtype'])){
	$emp_id=isset($_GET['emp_id'])?(string)$_GET['emp_id']:$uid;
	//To track collection maintainence .Order can be placed only by sales_executives
	//$authorized=is_authorized_group_list($emp_id,$auth_gr_placing_sales_order);
	$authorized=is_authorized_privilege_group($emp_id,'1'); //check for SALES_ORDER
	if(!$authorized){
		show_my_alert_msg("Sorry.You are not authorized to place an order");
		close_the_popup();
		exit;
	}
	/*
	$resultkyc=execute_my_query(" select GLH_KYC_AUDIT_ID from gft_lead_hdr where glh_lead_code=$lead_code and GLH_KYC_AUDIT_ID!=0 and GLH_KYC_AUDIT_ID is not null ");
	if(mysqli_num_rows($resultkyc)!=1 and $order_catagory=='0'){
		show_my_alert_msg("Please update the Know your Customer first and then place the Order ! ");
		js_location_href_to("audit_process.php?audittypeid=13&lead_code=$lead_code&emp_id=$emp_id");
		exit;
	}
	*/
	if($order_catagory==2){
		$query=" select sum(GOD_BALANCE_AMT) as bal_amt	 from gft_order_hdr  where GOD_LEAD_CODE= $lead_code and GOD_ORDER_STATUS='A' ";
		$res=execute_my_query($query);
		$data=mysqli_fetch_array($res);
		if($data['bal_amt']>0 and $lead_type!="3"){
			show_my_alert_msg("Sorry.ASA order cannot be placed due to outstanding amount Rs.".$data['bal_amt']);
			close_the_popup();
			exit;
			
		}
	}
	if($order_catagory==1  or  $order_catagory==2 or $order_catagory==13){
		$install_desible="";
		$god_order_type=3;
		$advanceASA_desible="disabled";
	}else if($order_catagory==12){
		$service_order=true;
		$server_desible="disabled";
		$advanceASA_desible="disabled";
		$install_desible="";
	}
	if($formtype==''){
		//$advanceASA_desible="disabled";
	}
	$date_select_icon_image="";
	if( ($god_order_type==3) || ($ref_install_id!='') || in_array(13, $category) ){
		$install_desible="";
//		$server_desible="disabled";
		$advanceASA_desible="disabled";
	}
	if($god_order_type==2){
		$server_desible="disabled";
		$advanceASA_desible="disabled";
	}
}
if( ($formtype=='edit') && ($god_order_type==1) ){
	$install_desible="";
}
$is_marketing_partner = $is_dealer_enabled = false;
if($lead_type==2 and $formtype!='edit'){
	$is_marketing_partner = is_marketing_partner('',$lead_code);
	$is_dealer_enabled	  = is_dealer_enabled($lead_code);
	$alliance_partner = false;
	if($proforma_no!='') {
// 		$renewal_dtl_chk = execute_my_query(" select gac_id from gft_add_on_commission_dtl where gac_order_no='$proforma_no' ");
		$alliance_partner_check = execute_my_query("select glh_lead_code from gft_lead_hdr where glh_lead_type='2' and glh_lead_subtype='3' and glh_lead_code='$lead_code'");
		if(mysqli_num_rows($alliance_partner_check)>0) {
			$alliance_partner = true;
		}
	}
	if(!$is_marketing_partner && !$is_dealer_enabled && !$alliance_partner){
		show_my_alert_msg("The Customer type seems as Partner. Please do order from Partner portal ! ");
		close_the_popup();
		exit;
	}	
} 
if($is_dealer){
	$is_dealer_enabled = true;
}
$ser_readonly = $readonly_attr;
$ser_disabled = $disable_attr;
$ser_class = $disable_class;
if($is_dealer_enabled) {
	echo "<script>enable_service=1</script>";
	$ser_class = $ser_disabled = $ser_readonly = '';
}
if($lead_type==13  and $formtype!='edit'){
	show_my_alert_msg("The Customer type Seems as Corporate Client. Please place order in the name of Corporate Customer as splitable Order ! ");
	close_the_popup();
	exit;	
}
if($formtype!="edit"){
	$check_support_group_mobile_no = get_support_group_mobile_no_status($lead_code,$quotation_no,$proforma_no);
	if($check_support_group_mobile_no==1){
		show_my_alert_msg("As you are trying to place order for phone + remote support, a mobile contact with TS(Tech Support) is mandatory. Please update the same in customer contact details edit and proceed.");
		close_the_popup();
		exit;
	}
}
$approvalname_list=get_approval_name_list($emp_id);
$approval_day_list=array(0=>array(0=>'10',1=>'10'),1=>array(0=>'20',1=>'20'),2=>array(0=>'30',1=>'30'));
$approval_days_comnbo=fix_combobox_with("approval_day","approval_day",$approval_day_list,$approval_days,'','','style="width:50px"');

print_dtable_header($title);
if($order_date>$date_on){
	show_my_alert_msg("Order Date is greater than today Date.");
	close_the_popup();
	exit;
}
$ee='';
$alt_row_class=/*. (string[int]) .*/ array();
if($formtype=='new')
	$ee="Readonly='true'";
$split = array();
if( ($splict_order==1) || (($formtype=='new') && ($lead_type=='3')) ){
	$split[0] = 'checked';
	$split[1] = '';
}else{
	$split[0] = '';
	$split[1] = 'checked';
}
$vattax='';
$taxcst='';
$taxexp='';
if($tax_mode=='1' or $tax_mode==''){
	$taxvat='checked';
}else if($tax_mode=='2'){
	$taxcst='checked';
}else if($tax_mode=='3'){
	$taxexp='checked';
}
$emp_name_list=get_emp_master($emp_id,null,null,false);
$emp_name='';
if (isset($emp_name_list[0][1])){
	$emp_name=$emp_name_list[0][1];
}
$cust_dtl=customerContactDetail($lead_code);
$custemail = array_filter(explode(",",$cust_dtl['EMAIL']));
if( (count($custemail)==0) && ($formtype=='new') ){
	echo "<script>";
	echo "alert('This Customer does not have Email ID. Please update valid Email ID and then create order');";
	echo "	window.close();";
	echo "</script>";
}
$is_cp=0;
$sql_check_partner=" SELECT CGI_EMP_ID FROM gft_cp_info join gft_lead_hdr on (GLH_LEAD_CODE=CGI_LEAD_CODE) ".
				   " WHERE CGI_EMP_ID=$emp_id or (CGI_LEAD_CODE=$lead_code and GLH_LEAD_TYPE=2)";
$res_check_partner 	= 	execute_my_query($sql_check_partner);
if(mysqli_num_rows($res_check_partner)!=0){
	$is_cp=1;
}
$sql_check_cc=" select glh_lead_type from gft_lead_hdr where glh_lead_code=$lead_code and glh_lead_type=3 ";
$res_check_cc=execute_my_query($sql_check_cc);
if(mysqli_num_rows($res_check_cc)==0 && $vn==28) {  //Not a corporate customer and valid upgradation order 
	$sql_check_valid_upgradation_order="select GID_ORDER_NO from gft_install_dtl_new where GID_LEAD_CODE=$lead_code and GID_STATUS!='U' ";
	$res_check_valid_upgradation_order=execute_my_query($sql_check_valid_upgradation_order);
	if(mysqli_num_rows($res_check_valid_upgradation_order)==0){
		show_my_alert_msg('This Customer does not have Valid Installation. Upgradation Not Possible');
		close_the_popup();
		exit;
	}
}
$threshold_discount=get_samee_const('Threshold_Discount');
$custom_discount=get_samee_const('Custom_License_Discount');
$spl=0;
if(is_authorized_group($uid, 101)){
	$spl = 1;  //to place the special orders of Demo friend tool and Downgrade orders and 100% delivery payment
}

$kit_based = check_kit_based_customer($lead_code);

$prod_code_arr = /*. (string[int]) .*/array();
$not_in_pcode = /*.(string[int]).*/array('70');
if($is_marketing_partner){
	$prod_code_arr[] = '70';
	$not_in_pcode = array();
}
if($is_dealer_enabled){
	$prod_code_arr[] = '71';
	$not_in_pcode = array();
}
$hdr_content 			= get_header_data_for_quote($lead_code, $emp_id,'','','','',$order_no,$formtype,$split);
$order_type_ui 			= return_order_type_ui(true);
$server_order_entry_ui 	= get_server_order_ui($lead_code,$formtype,$show_subscription_skew,$kit_based,$prod_code_arr,$from_quotation,$disable_attr,$disable_class,$readonly_attr,true,$is_dealer_enabled,$not_in_pcode,$only_active);
$upgrad_order_entry_ui 	= get_upgradation_order_ui($lead_code,$formtype,$show_subscription_skew,$only_active,$from_quotation,$disable_attr,$disable_class,$readonly_attr,true);
$asa_order_entry_ui		= get_asa_order_ui($lead_code,$formtype,$prod_code_arr,$from_quotation,$disable_attr,$disable_class,$readonly_attr,true,$only_active); 
$subscription_order_ui	= get_subscription_renewal_order_ui($lead_code,$formtype,$only_active,$from_quotation,$disable_attr,$disable_class,$readonly_attr,true);
$service_order_ui		= get_service_order_ui($lead_code,$formtype,$show_subscription_skew,$kit_based,$prod_code_arr,true,$only_active,$from_quotation,$ser_disabled,$ser_class,$ser_readonly,true,$is_dealer_enabled);
$support_order_ui		= get_support_order_ui($lead_code,$formtype,$prod_code_arr,$from_quotation,$disable_attr,$disable_class,$readonly_attr,true,$only_active);
echo<<<END
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#b6e5ff" class="Formborder1">
<tbody><tr><td>
<form method="post" action="order_submit.php" name="order_submit_form" enctype="multipart/form-data" onreset="return check();">
<input type="hidden" id="formtype" name="formtype" value="$formtype">
<input type='hidden' id='inv_status' name='inv_status' value='$invoice_status'>
<input type="hidden" id="order_details" name="order_details" value="yes">
<input type="hidden" id="threshold_discount" name="threshold_discount" value="$threshold_discount">
<input type="hidden" id="custom_discount" name="custom_discount" value="$custom_discount">
<input type="hidden" id="marketing_partner" name="marketing_partner" value="$is_marketing_partner">
<input type="hidden" id="dealer_order" name="dealer_order" value="$is_dealer">
<input type="hidden" id="order_app_status" name="order_app_status" value="">
<input type="hidden" id="no_of_days" name="no_of_days" value="$approval_days">
<input type="hidden" id="spl_emp" name="spl_emp" value="$spl">
<input type="hidden" id="emp_id" name="emp_id" value="$emp_id">
<input type="hidden" id="cp_id" name="cp_id" value="$is_cp">
<input type="hidden" id="prev_status" name="prev_status" value="$order_status">
<input type="hidden" id="invoice_no" name="invoice_no" value="$invoice_no">
<input type="hidden" id="quotation_no" name="quotation_no" value="$quotation_no">
<input type="hidden" id="proforma_no" name="proforma_no" value="$proforma_no">
<input type="hidden" id="hq_delivery_type" name="hq_delivery_type" value="$hq_delivery_type">
<input type="hidden" id="dealer_entry" name="dealer_entry" value="$dealer">
<input type="hidden" name="lead_code" id="lead_code" value="$lead_code">
<input type="hidden" name="visit_date" id="visit_date" value="$order_date">
<input type="hidden" name="lead_type" id="lead_type" value="$lead_type">
<input type="hidden" name="is_kit_based" id="is_kit_based" value="$kit_based">
<input type="hidden" name="pd_expense_type" id="pd_expense_type" value="$pd_expense_type">
<input type="hidden" name="total_coupon_required" id="total_coupon_required" value="0">
<input type="hidden" name="customer_id" id="customer_id" value=''>
<input type="hidden" name="fix_adj_amt" id="fix_adj_amt" value='0'>
<TABLE border=0 cellPadding=1 cellSpacing=1 width=100% >
<thead>
<TR><td colspan=4>$hdr_content</tr></td>
<TR><td colspan=4>$order_type_ui</tr></td>
</thead><tbody>
<tr><td colspan="4" class="head_black_10">$server_order_entry_ui</td></tr>
<tr><td colspan="4" class="head_black_10">$upgrad_order_entry_ui</td></tr>
<tr><td colspan="4" class="head_black_10">$asa_order_entry_ui</td></tr>
<tr><td colspan="4" class="head_black_10">$subscription_order_ui</td></tr>
<tr><td colspan="4" class="head_black_10">$service_order_ui</td></tr>
<tr><td colspan="4" class="head_black_10">$support_order_ui</td></tr>	
</tbody><tfoot>
END;
$customer_installation=false;
if($install_desible==""){
    $additional_wh_cond = "";
    if($ref_install_id!=''){
	   $additional_wh_cond .= " and GID_INSTALL_ID in ($ref_install_id) ";
    }
    $installtion_detail = get_installation_for_selection($lead_code,$lead_type,$additional_wh_cond,false,$proforma_no,true);
    echo $installtion_detail;
}else{
	echo<<<END
	<tr><td colspan="4">
<div id="Installed_dtl">
<TABLE id="tableInstalled_dtl" border="1" cellPadding="2" cellSpacing="2" width="900px">
<input type='hidden' name='install_dtl' id='install_dtl' value='false'>
</table></div></td></tr>
END;
}
if($service_order==true){
	$dervicequery="SELECT act.GLD_VISIT_DATE,em.gem_emp_name, lh.GLH_CUST_NAME, lh.GLH_CUST_STREETADDR1, " .
			" gam_activity_desc, G.GCM_NATURE, act.GLD_NOTE_ON_ACTIVITY, act.GLD_ACTIVITY_ID, act.GLD_EMP_ID, act.GLD_LEAD_CODE,gld_reffer_id,gsd_order_no " .
			" FROM gft_activity act join gft_lead_hdr lh on(act.GLD_LEAD_CODE=lh.glh_lead_code)" .
			" join gft_emp_master em on(act.gld_emp_id=em.gem_emp_id) " .
			" join (select distinct (glh_lead_code) from " .
			" (select glh_lead_code from gft_lead_hdr where GLH_REFERENCE_GIVEN=$lead_code  " .
			" union select $lead_code as glh_lead_code from dual )dd ) ss" .
			" on(ss.glh_lead_code=lh.glh_lead_code) ".
			" left join gft_activity_master am on (act.gld_visit_nature=am.GAM_ACTIVITY_ID) " .
			" left join gft_customer_support_dtl on (gld_reffer_id=gcd_activity_id) " .
			" left join gft_complaint_nature_master G on (GCD_NATURE = G.GCM_NATURE_ID) " .
			" left join gft_service_order_dtl inv on (inv.gsd_activity_id=act.GLD_ACTIVITY_ID)".
			" where gem_role_id not in (21,22,28,27,26) order by GLD_VISIT_DATE ";
	$result=execute_my_query($dervicequery);
	if(mysqli_num_rows($result)){
echo<<<END
<tr><td colspan="4" width="100%">
<div class="unhide" id="service_dtl1" STYLE="width:900px; height:300px; overflow:auto;">
<TABLE class="Formborder1" id="tableservice_dtl" border="0" cellPadding="2" cellSpacing="2" width="900px">
<thead>
<td class="head_black_10" colspan="9" align="center">Service Details</td>
<tr style="height: 20" class="modulelisttitle">
<td class="header_without_link">S.No</td>
<td class="header_without_link">Activity Date</td>
<td class="header_without_link">Employee</td>
<td class="header_without_link">Shop Name</td>
<td class="header_without_link">Activity Desc</td>
<td class="header_without_link">Nature</td>
<td class="header_without_link">Activity Note</td>
<td class="header_without_link">Reference Id</td>
</tr></thead>
<tbody>
END;
			$alt_row_class=array("oddListRow","evenListRow");
			$sl=0;
			$s=0;
			while($qdata=mysqli_fetch_array($result)){
				$sl++;
				if($qdata['gsd_order_no']==null){
					$enabled="";
					$invoice_link="";
				}else{
					if($order_no!=$qdata['gsd_order_no']){
						$enabled="disabled";
					}else{
						$enabled="checked";
					}
					$invoice_link="<a title=\"Reffer order Details\">".$qdata['gsd_order_no']."</a>";
				}
echo<<<END
<tr class="$alt_row_class[$s]"  onMouseOver="this.style.backgroundColor='#C8DC9B';" onMouseOut="this.style.backgroundColor='';" id="{$qdata['GLD_EMP_ID']}-{$qdata['GLD_ACTIVITY_ID']}-{$qdata['GLD_LEAD_CODE']}">
<td class="content_txt" nowrap><label for="chargeable_activity$sl">$sl</label>
<input type="checkbox" id="chargeable_activity$sl" name="chargeable_activity[$sl]" value="{$qdata['GLD_EMP_ID']}-{$qdata['GLD_ACTIVITY_ID']}" onchange="td_change($sl)" $enabled>$invoice_link</td>
<td class="content_txt"><label id="datelab$sl">{$qdata['GLD_VISIT_DATE']}</label></td>
<td class="content_txt">{$qdata['gem_emp_name']}</td>
<td class="content_txt">{$qdata['GLH_CUST_NAME']}</td>
<td class="content_txt">{$qdata['gam_activity_desc']}</td>
<td class="content_txt">{$qdata['GCM_NATURE']}</td>
<td class="content_txt" id="td_activity_note$sl">{$qdata['GLD_NOTE_ON_ACTIVITY']}</td>
<td class="content_txt">{$qdata['gld_reffer_id']}</td></tr>
<script type="text/javascript" >td_change($sl)</script>	
END;
				$s=($s==1?0:1);
			}
echo<<<END
</tbody></table></div>
END;
		$service_detail="<input type=\"hidden\" name=\"service_dtl\" id=\"service_dtl\" value=\"true\">";
echo '</td></tr>';
		}else{
echo<<<END
<tr><td colspan="4">
<div class="hide" id="service_dtl1">
<TABLE id="tableservice_dtl" border="0" cellPadding="2" cellSpacing="2">
<tbody></tbody></table></div>
END;
		$service_detail="<input type=\"hidden\" name=\"service_dtl\" id=\"service_dtl\" value=\"false\">";
echo '</td></tr>';
		}
	}else{
echo<<<END
<tr><td colspan="4">
<div class="hide" id="service_dtl1">
<TABLE id="tableservice_dtl" border="0" cellPadding="2" cellSpacing="2">
<tbody></tbody></table></div>
END;
		$service_detail="<input type=\"hidden\" name=\"service_dtl\" id=\"service_dtl\" value=\"false\">";
echo '</td></tr>';
	}
echo<<<END
<tr><td colspan="4">
<table border=0 cellPadding=1 cellSpacing=0 width="900px">
<tr><td colspan=4>
<fieldset id='net_tot_fieldset' class='group-div'><table class='group-tab'>
<legend>Order Details</legend>
END;
if($include_pcs && ($lead_type==3)){
echo<<<END
<tr><td class="datalabel" nowrap>Net Total Amount</TD>
<TD><INPUT name="nettotal" id="nettotal" class="formStyleTextarea" Readonly='True'></TD></tr>
<tr><td class="datalabel" nowrap>Goodwill Discount </TD>
<TD class="datalabel" nowrap>Amount <INPUT name="goodwill" id="goodwill" class="formStyleTextarea" value="$GOD_DISCOUNT_ADJ_AMT" onkeyup="javascript:extractNumber(this,2,false);" onblur="convert_goodwill(1);" size="10">
Percentage	<INPUT name="goodwill_per" id="goodwill_per" class="formStyleTextarea" value="$god_percentage" onkeyup="javascript:extractNumber(this,4,false);" onblur="convert_goodwill(2);" size="7"> % </TD> </tr>
END;
}
//$incentive_option	=	get_incentive_employee_list($lead_code);
$disc_fields_ui = get_discount_adjustment_fields("order",($quotation_no!=''?$quotation_no:$proforma_no));
echo<<<END
<tr><td valign="top" class="datalabel" nowrap  id='require_coupon_text1'></td><td colspan='2' id='require_coupon_text'></td></tr>
$disc_fields_ui
<tr><td class="datalabel" nowrap>Total Order Value(Rs.)</TD>
<TD><INPUT name="grandtotal" style="width:150px" id="grandtotal" class="formStyleTextarea" Readonly='True'></td></tr>
<tr><TD class="datalabel" valign="top" nowrap>Remarks</TD>
<TD valign="top"><textarea id="comments"  name="comments" cols="40" rows="3" class="formStyleTextarea">$remarks</textarea></TD></tr>
<TR><td coslpan=4>$service_detail</TD></tr>
</table></fieldset></td></tr>
<tr><td colspan=4><fieldset class='group-div' id='payment_fieldset'>
<legend>Payment Type</legend>
<table class='group-tab'>
<TR id='pay_row'><TD class="datalabel"><font color="red" size="3">*</font>Payment Type</TD><TD>
END;
echo fix_combobox_with('pay_code','pay_code',$payment_code,0,'','Select',null,false,"onchange=\"javascript:showlaptop('laptop'); return false;\"");
echo<<<END
</td></TR>
<tr><td colspan="2">
<div id="receipt_dtl" class="hide">
<table style='border: 1px solid black;' align='center'><tr><td class="head_blue" style='text-align:center;' colspan=2>Payment details</td></tr>
<TR><TD colspan="2">
<input type="hidden" name="receipt_details" id="receipt_details" value="yes">
<input type="hidden" name="ptype" value="a" id="ptype">
END;
show_advance_selection($lead_code);
echo <<<END
</table>
</div></td></tr>
END;
if($order_catagory==0){
	echo<<<END
<TR class="notForDealer"><TD class="datalabel">Approval Days</TD><TD>$approval_days_comnbo</td></tr>
END;
}
if($formtype=='edit'){
	echo<<<END
<tr id="debit" class="hide"><td class="datalabel" colspan="3">Amount to Deduct in Partner Account:</td>
<td><input name="debit_amt" id="debit_amt" class="formStyleTextarea" onkeypress="javascript:return blockNonNumbers(this, event, true, false);" onkeyup="javascript:extractNumber(this,2,false);" ></td></tr>
END;
	if((int)$collection_realized_amt > 0){
		echo<<<END
	<tr id="refund_combo" class="hide">
		<td class="datalabel" colspan="3">Refund Option:</td>
		<td><select id='re_stat' name='re_stat' class="formStyleTextarea"><option value='NO'>No Refund</option><option value='YES'>Refund</option></select></td>
	</tr>
	<tr id="refund_val" class="hide">
		<td class="datalabel" colspan="3"><input type='hidden' value='$collection_realized_amt' id='realized_amt'><span style="color:red;">*</span>Refund Amount:</td>
		<td><input name="refund_amt" id="refund_amt" class="formStyleTextarea" onkeypress="javascript:return blockNonNumbers(this, event, true, false);" onkeyup="javascript:extractNumber(this,2,false);" ></td>
	</tr>
	<tr id="refund_comm" class="hide">
		<td class="datalabel"  colspan="3"><span style="color:red;">*</span>Refund Comments: </td>
		<td><textarea name="refund_comment" id="refund_comment" class="formStyleTextarea"></textarea></td>
	</tr>
END;
	}
} else {
	echo<<<END
<tr class="notForDealer"><td class="datalabel"  nowrap><span style="color:red;">*</span>Expected Collection Date</td>
<td><INPUT id="collection_date" name="collection_date" class="formStyleTextarea" size="24" value="" readonly >&nbsp;
<img alt="" align="absmiddle" src="images/date_time.gif" class="imagecur" id="onceDateIconcd" width="16" height="16" border="0">
<script type="text/javascript"> init_date_func("collection_date","%Y-%m-%d","onceDateIconcd","Bl"); </script></td></tr>
END;
}
echo<<<END
</table></fieldset></td></tr>
END;
$delivery_type  =  get_single_value_from_single_table("GLE_DELIVERY_TYPE", "gft_lead_hdr_ext", "GLE_LEAD_CODE",$lead_code);
if($quote_lic_cost > 0){
    $incentive_fields = get_incentive_fields($isonly_asa_order,$lead_code,$uid);
    if($incentive_fields!='') {
        echo "<tr><td colspan=4><fieldset class='group-div' id='incentive_fieldset'><legend>Agile Incentive</legend>".
             "<table class='group-tab'>$incentive_fields";
        if($delivery_type == 1 && $lead_type==3){
            echo<<<END
    <tr id=""><td class="datalabel" nowrap><span style="color:red;">*</span>Delivery Management Incharge</td><td>
END;
            get_employee_list_pcs(null,"","",null,null,null,array(70,72,5),null,'off','delivery_management_name','delivery_management_incharge');
            echo "</td></tr>";
        }
        echo "</table></fieldset></td></tr>";
    }
}

//show_receipt_dtl();
$show_inv_choice = (($lead_type=='3' and check_invoice_fields_required($productcode)==true)?true:false);
$po_fields = '';
if($tax_mode=='4') {
	$po_fields .= "<tr>".get_ui_for_pdf_products_view($single_row_pdf)."</tr>";
}
	if($formtype=='new') {
if($show_inv_choice) {
	$inv_options_arr = array(array('1','Get Full invoice with Purchase Order'),array('2','Get Full invoice with Proposal'),array('3','Get manual invoice'));
	$inv_combo = fix_combobox_with("generate_invoice", "generate_invoice", $inv_options_arr,'',null,'--Select--','width:150px',false,'',1,null,'invoice_fields');
	$po_fields .=<<<END
<tr id='inv_option_row'><td class='datalabel'>Invoice Option</td>
<td>$inv_combo</td></tr><tr><td colspan='2'>
<table style='width:70%;margin:auto;display:none;border:1px solid black;' id='po_tab'>
<thead style="background-color: burlywood;"><tr><td colspan=2 style="font-weight:bold;text-align:center;">PURCHASE ORDER DETAILS</td></tr></thead>
<tr id='po_upload_row' class='po_rows'><td class='datalabel'>Select File</td>
<td><input type='file' name='po_upload' id='po_upload' class='invoice_fields'/></td></tr>
<tr class='po_rows'><td class='datalabel'>PO Number</td>
<td><INPUT name="po_number" style="width:150px" id="po_number" class="formstyletextarea invoice_fields"></td></tr>
<tr class='po_rows'><td class='datalabel'>PO Date</td>
<td><INPUT name="po_date" id="po_date" class="formStyleTextarea invoice_fields" size="24" value="" readonly size="14">
<script type="text/javascript">init_date_func("po_date","%Y-%m-%d","","");</script></td></tr>
<tr class='po_rows'><td colspan='2' style='text-align: center;'><input type='button' id='po_reset' value='Reset PO File'/></td></tr>
</table></tr></td>
END;
} else {
	$immediate_skews = get_prods_for_immediate_invoice($productcode);
	if($immediate_skews!='') {
		$po_fields .=<<<END
	<tr class='immediate_notice'><td colspan='2' style='font-size: larger;text-align: center;'><b>Note:</b> Invoice will be raised after order creation/order split for approved orders. In case of partial / full delivery payment, invoice will be raised after collection realization and order approval.</td></tr>
END;
	}
}
}
if($po_fields!='') {
	$po_fields = <<<END
	<fieldset class='group-div' id='inv_fieldset'>
	<legend>Invoice Options</legend>
	<table class='group-tab'>
	$po_fields</table></fieldset>
END;
}
echo<<<END
<tr><td colspan=4>
<fieldset id='disc_dtls_fieldset' class='group-div'>
<legend>Discount Reason</legend>
<table class='group-tab'>
<tr><td>
<div id="ask_disct_details" class="hide">
<table><tr><TD class="datalabel"  nowrap>Discount Reason</TD><TD>
END;
echo fix_combobox_with('approval_code','approval_code',$list_discount_master,$discount_reason_code,'','Select',null,false,"onClick=\"javascript:show_others_detail();\"");
echo<<<END
</td></tr><tr><TD colspan=2 style='text-align: center;'>
<div id="others_discount_visiblity" class="hide"><textarea id="others_disc_details" cols=20 rows=6 maxlength="100" name="others_disc_details" class="formStyleTextarea">$discount_reason_code_dtl</textarea>
</td></tr></div></TD></TR></table>
</div></td></tr></table></fieldset></td></tr>
END;

if($formtype=='edit'){
	 $or_status_list=get_two_dimensinal_array_from_table('gft_order_status_master','GOS_CODE_ABR','GOS_STATUS');
echo<<<END
<tr><td colspan=4>
<fieldset id='ord_status_fieldset' class='group-div'>
<legend>Order Status</legend>
<table class='group-tab'><tr><td>
<tr><td class="datalabel">Order Status</td><td>
END;

if(in_array('1',$category) or in_array('2',$category)){
echo fix_combobox_with('ord_status','ord_status',array(array('A','Active'),array('C','Cancel'),array('PC','Partner Approved')),$order_status,'',null,'width:150px',false);
}else{
	echo fix_combobox_with('ord_status','ord_status',array(array('A','Active'),array('C','Cancel'),array('PC','Partner Approved')),$order_status,'',null,'width:150px',false);
}
echo<<<END
</td></tr></table></fieldset>
</td></tr>
END;
}else{
	
echo<<<END
<tr><td colspan=4><fieldset id='prod_delivery_fieldset' class='group-div'>
<legend>Product Delivery</legend>		
<table id="date_from1" class='group-tab'"><tbody>
<tr><td class="datalabel">Is Product Delivery Required?</td><td>
END;
	$prod_delivery_selected = /*.(string[int]).*/array();
	if($order_catagory=='2'){
		$prod_delivery_selected = array('No','No');
	}
	echo fix_radio_with('pending_imp',array(array('Yes','Yes'),array('No','No')),$prod_delivery_selected,'onchange="javascript:training_required_option();"','',null,true);
	echo<<<END
</td></tr>
<tr id="pd_one" style="display: none;"><td class="datalabel" nowrap><span style="color:red;">*</span>Product Delivery by</td><td colspan="3">
<input class='pd_by' type='hidden' name='deliver_by_id' id='deliver_by_id' value='70,72,5' >
<input class='pd_by' type='radio' name='deliver_by' value='70,72,5' checked onclick="update_pcs_coupon_count();">Product Delivery
<input class='pd_by' type='radio' name='deliver_by' value='36'  onclick="update_pcs_coupon_count();">Solution Delivery
<input class='pd_by' type='radio' name='deliver_by' value='gft_partner'  onclick="update_pcs_coupon_count();">Partner
<script>
jQuery.noConflict();
jQuery(document).ready(function(){
	jQuery('.pd_by').click(function(){
		var pd_val	=	jQuery(this).val();
		if(pd_val=='70,72,5'){
			jQuery('#deliver_by_id').val(pd_val);
			document.getElementById("date_from1").style.display="";
		}else if(pd_val=='36'){
			jQuery('#deliver_by_id').val(pd_val);
			document.getElementById("date_from1").style.display="";
			document.getElementById("migration_temp").style.display="";
		}else{
			jQuery('#deliver_by_id').val(pd_val);
			document.getElementById("date_from1").style.display="";
			document.getElementById("migration_temp").style.display="";
		}
		jQuery('#product_consultant_name').val('');
		jQuery('#product_consultant').val('');
	});
	jQuery('input[name=deliver_by][value="36"]').prop('checked', false);
	jQuery('input[name=deliver_by][value="gft_partner"]').prop('checked', false);
	jQuery('input[name=deliver_by][value="70,72,5"]').prop('checked', true);
	var pd_val	=	jQuery("input:radio[name=deliver_by]").val();
	if(pd_val=='gft'){
			jQuery('#deliver_by_id').val(pd_val);
	}else if(pd_val=='gft_pcs'){
		jQuery('#deliver_by_id').val(pd_val);
	}else{
		jQuery('#deliver_by_id').val(pd_val);
	}
	jQuery('input[name=is_other]').change(function() {
		if(jQuery(this).val()=='0') {
			jQuery('.corp_incent_row').css('display','none');
		} else {
			jQuery('.corp_incent_row').css('display','');
		}
	});
});
</script>
</td><tr>
<tr id="pd_two" style="display: none;"><td class="datalabel" nowrap><span style="color:red;">*</span>Delivery Owner</td><td>
END;
	get_employee_list_pcs(null,"$pc_name","$pc_id",null,null,null,array(70,72,5),null,'off','product_consultant_name','product_consultant');
	echo<<<END
</td></tr>
END;
	
	audit_questions_ui(20);
echo<<<END
</tbody></table></fieldset></td></tr>
<tr><td colspan=4><fieldset class='group-div' id='file_upload_fieldset'>
<legend>File Uploads</legend>
<table class='group-tab'>
<tr id="migration_temp"><td class="datalabel">Migration Template upload</td>
<td colspan="1"><input name="migtemplate" id="migtemplate" size="25" type="file"></td></tr>
END;
}
$proposal_ids = /*.(string[int]).*/array();
if($lead_type=="3"){
	$proposal_ids = get_proposal_doc_dtls($lead_code);
	$fileexist = 0;
	if(count($proposal_ids)>0) {
		$fileexist = 1;
	}
	if($formtype=='edit'){
		echo<<<END
<tr><td colspan=4><fieldset class='group-div' id='file_upload_fieldset'>
<legend>File Uploads</legend>
<table class='group-tab'>
END;
	}
echo<<<END
<tr><td class="datalabel">HQ Proposal </td><td><input name="scancopy" id="scancopy" type="file" size="30">
<input type="hidden" name="proposal_exist" id="proposal_exist" value='$fileexist'></td></tr>
</tbody></table></fieldset></td></tr>
END;
} else {
	echo "</table></fieldset></td></tr>"; // to close the  migration template 
}
if($ref_partner_msg!=''){	
echo <<<END
<tr><td colspan=4>
<fieldset class='group-div' id='corp_service_fieldset'><legend>Partner Reference</legend>
<table class='group-tab'>
<tr><td class='datalabel'>$ref_partner_msg 
		<input type='hidden' name='reffer_id' id='reffer_id' value='$ref_partner_code'>
		<input type='hidden' name='commission' id='commission' value='$commission_percent'>
</td></tr></table></fieldset></td></tr>
END;
}
echo<<<END
<tr><td colspan=4>
END;
if($lead_type==3) {
	$ser_class = "class='hide'";
	if($order_catagory==12) {
		$ser_class = "class='show'";
	}
	$query = "select GST_CODE,GST_DESC from gft_service_type_master where ";
	$service_type_list = get_two_dimensinal_array_from_query($query." GST_IS_SERVICE='Y' ", 'GST_CODE', 'GST_DESC', 'GST_CODE', 'select','0');
	$expense_type_list = get_two_dimensinal_array_from_query($query." GST_IS_EXPENSE='Y' ", 'GST_CODE', 'GST_DESC', 'GST_CODE', 'select','0');
	$service_type_ui = fix_combobox_with('god_service_type','god_service_type',$service_type_list,$service_type);
	$expense_type_ui = fix_combobox_with('god_expense_type','god_expense_type',$expense_type_list,$expense_type,1,'','',false,"onchange=expense_custom();");
echo<<<END
<fieldset class='group-div' id='corp_service_fieldset'><legend>Service & Expense Type</legend>
<table class='group-tab'>
<tr name='service_ui' id='service_ui' $ser_class><td class="datalabel" nowrap><font color='red' size='3'>*</font>Service Type</td><td>$service_type_ui</td></tr>
<tr name='expense_ui' id='expense_ui' $ser_class><td class="datalabel" nowrap><font color='red' size='3'>*</font>Expense Type</td><td>$expense_type_ui</td>
<td id="custom_td" class="hide">Custom Remarks for Expense<textarea id="custom_remarks" class="formStyleTextarea" name="custom_remarks" rows="2" cols="40">$custom_remarks</textarea></td></tr>
</table></fieldset>
END;
}else{  //to avoid null error
echo <<<END
	<tr id='service_ui'></tr><tr id='expense_ui'></tr>
END;
}
echo <<<END
</td></tr>
END;
/* if(($god_order_type==1 or $god_order_type==4) and $lead_type==3){
$order_type_list=array(0=>array('1','Sales Order'),array('4','Commercial Subscription Order'));
$select_order_type=fix_combobox_with('god_order_type',$name='god_order_type',$order_type_list,$god_order_type,$tab_index=null,null,"style=\"width:200px\"",false);
echo<<<END
<tr><td class="datalabel" nowrap>Order Type</td><td>$select_order_type</td></tr>
END;
}else{ */
echo<<<END
<input type="hidden" name="god_order_type" id="god_order_type" value="$god_order_type">
END;
//}
echo<<<END
<tr><td colspan='4'>$po_fields</td></tr>
END;
$authorized=is_authorized_group_list($uid,$auth_order_approval_group);
if($formtype=='edit' and (is_authorized_group_list($uid,array(1,17)) or ($god_order_type==2 and is_authorized_group($uid,36)))){
echo<<<END
<tr id="process_area" style="display:none;"><td class="head_black_10" align="Center" colspan=2>Processing Started... Wait for some time..</td></tr>
<TR id="submit_button"><TD align="center" colspan=2><INPUT align="center" class="button" id="submit1" name="submit1" type="button" value="Submit" onclick="my_evaluate();"></TD></TR>
END;
}elseif($formtype!='edit'){
echo<<<END
<tr id="process_area" style="display:none;"><td class="head_black_10" align="Center" colspan=2>Processing Started... Wait for some time..</td></tr>
<TR id="submit_button"><TD align="right" colspan=2 style='text-align:center;'><INPUT align="right" class="button" id="submit1" name="submit1" type="button" value="Submit" onclick="my_evaluate();">
<INPUT class="button" name="reset1" type="reset" value="Reset"></TD></TR>
END;
}


$query1="select GAP_SKEW_PROPERTY_ID,GAP_PRICE,GAP_INCL_OF_TAX,GAP_SERVICE_TAX_PERC from gft_assure_price_list_master where gap_price_name='Additional' and gap_skew_property_id in (4,15)";

$result1=execute_my_query($query1);
$icount=0;
$ass_addl_client_price_without_tax	=	/*. (string[int][int]) .*/array();
while($data1=mysqli_fetch_array($result1)){
	$gap_skew_property_id=$data1['GAP_SKEW_PROPERTY_ID'];
	$gap_incl_of_tax=$data1['GAP_INCL_OF_TAX'];
	if ($gap_incl_of_tax == 'Y'){
		$t_gap_price=(float)$data1['GAP_PRICE'];
		$t_gap_service_tax_perc=(float)$data1['GAP_SERVICE_TAX_PERC'];
		$gap_price_float=  ($t_gap_price *100)/(100+$t_gap_service_tax_perc);
		//$gap_price=strval($gap_price_float);
		$gap_price=number_format($gap_price_float,2,'.','');
	}else{
		$gap_price= $data1['GAP_PRICE'];
	}
	
	$ass_addl_client_price_without_tax[$icount][0] = $gap_skew_property_id;
	$ass_addl_client_price_without_tax[$icount][1] =$gap_price;

	$icount++;
}


$hidden_server='';
$inc=0;
//installed products
$instal_que=" select glh_lead_code,GID_ORDER_NO, GID_FULLFILLMENT_NO, GID_LIC_PCODE, GID_LIC_PSKEW, GFT_SKEW_PROPERTY, GPM_PRODUCT_ABR, GPM_SKEW_DESC ".
		" from gft_install_dtl_new ".
		" join gft_lead_hdr on (glh_lead_code=gid_lead_code) ".
		" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GID_LIC_PCODE and pm.GPM_PRODUCT_SKEW=GID_LIC_PSKEW and GPM_PRODUCT_TYPE!=8 and if(GFT_SKEW_PROPERTY not in (1,11),GPM_LICENSE_TYPE!=3,1)) ".
		" join gft_product_family_master pfm on(pfm.gpm_product_code=GID_LIC_PCODE) ".
		" where GID_LEAD_CODE='$lead_code' AND GID_STATUS!='U' ";
$instal_res = execute_my_query($instal_que);
while($row1 = mysqli_fetch_array($instal_res)){
	$cust_code		=	$row1['glh_lead_code'];
	$ref_order_no	=	$row1['GID_ORDER_NO'];
	$ref_fullfill	=	$row1['GID_FULLFILLMENT_NO'];
	$ref_pcode		=	$row1['GID_LIC_PCODE'];
	$ref_pskew		=	$row1['GID_LIC_PSKEW'];
	$ref_property	=	$row1['GFT_SKEW_PROPERTY'];
	$ref_product_name=	$row1['GPM_PRODUCT_ABR']." ".$row1['GPM_SKEW_DESC'];
	$all_value	=	$cust_code."**".$ref_order_no."**".$ref_fullfill."**".$ref_pcode."**".$ref_pskew."**".$ref_property."**".$ref_product_name."**".'Y';
	$hidden_server	.=	"<input type='hidden' class='hidden_all_install' value='$all_value' name='all_install_details[]' id='all_install_details$inc'>";
	$inc++;
}
//purchased but not installed
$server_que=" select glh_lead_code,GOP_ORDER_NO, GOP_FULLFILLMENT_NO, GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GFT_SKEW_PROPERTY,GPM_PRODUCT_ABR,GPM_SKEW_DESC ".
		" from gft_order_hdr ".
		" join gft_lead_hdr on (glh_lead_code=god_lead_code) ".
		" join gft_order_product_dtl on (GOP_ORDER_NO = GOD_ORDER_NO) ".
		" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and pm.GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW and GFT_SKEW_PROPERTY in (1,11) and GPM_PRODUCT_TYPE!=8) ".
		" join gft_product_family_master pfm on(pfm.gpm_product_code=GOP_PRODUCT_CODE and GPM_IS_INTERNAL_PRODUCT in (0,2,3)) ".
		" where GOD_ORDER_STATUS='A' and GOD_ORDER_SPLICT=0 and glh_lead_code='$lead_code' and concat(GOP_ORDER_NO,GOP_FULLFILLMENT_NO) not in ".
		" (select concat(GID_ORDER_NO,GID_FULLFILLMENT_NO) from gft_install_dtl_new where gid_lead_code='$lead_code' ) ";
$server_res = execute_my_query($server_que);
while($row1 = mysqli_fetch_array($server_res)){
	$cust_code		=	$row1['glh_lead_code'];
	$ref_order_no	=	$row1['GOP_ORDER_NO'];
	$ref_fullfill	=	$row1['GOP_FULLFILLMENT_NO'];
	$ref_pcode		=	$row1['GOP_PRODUCT_CODE'];
	$ref_pskew		=	$row1['GOP_PRODUCT_SKEW'];
	$ref_property	=	$row1['GFT_SKEW_PROPERTY'];
	$ref_product_name=	$row1['GPM_PRODUCT_ABR']." ".$row1['GPM_SKEW_DESC'];
	$all_value	=	$cust_code."**".$ref_order_no."**".$ref_fullfill."**".$ref_pcode."**".$ref_pskew."**".$ref_property."**".$ref_product_name."**".'N';
	$hidden_server	.=	"<input type='hidden' class='hidden_all_install' value='$all_value' name='all_install_details[]' id='all_install_details$inc'>";
	$inc++;
}
echo "<div>$hidden_server</div>";


$collection_amt=($collection_amt==''?0:$collection_amt);
$allowed_discount=get_samee_const("DISCOUNT_ALLOWED");
$custom_license_asa_percentage=get_samee_const("SAM_ASA_AMT_FOR_CUSTLIC");
echo<<<END
</tfoot></table></form>
</td></tr></tbody></table></td></tr></table>
<script type="text/javascript">
$script_var
init_date_func("order_date","%Y-%m-%d","onceDateIcon","Bl");
$('emp_name').value="$emp_name";
$('emp_code').value="$emp_id";
var allowed_discount="$allowed_discount";
var custom_license_asa_percentage="$custom_license_asa_percentage";
var custom_amount=0; 
var arrpcode = "$arrpcode".split(",");
var arrselrate="$arrselrate".split(",");
var arrtaxrate="$arrtaxrate".split(","); 
var arrlistprice="$arrlist_price".split(",");
var arrsertaxrate="$arrsertaxrate".split(",");
var arr_gst_component="$arrgstcomponent".split(",");
var gopqty="$gopqty".split(",");
var discountamt= "$discountamt".split(",");
var sellamt="$sellamt".split(",");
var usedqty ="$arrusedqty".split(",");
var cpusedqty= "$arrcpusedqty".split(",");
var arrcategory="$arrcategory".split(",");
var arrass_period="$arrass_period".split(",");
var arrass_start_date="$arrass_start_date".split(",");
var arrass_end_date="$arrass_end_date".split(",");
var js_pay_code="$paymentcode";
var js_approved_by="$approvedby";
var js_approval_code="$approvalcode";
var js_approval_days="$approval_days";
var js_discount_reason="$discount_reason_code";
var install_stat = "$arrinstall_status".split(",");
var susedqty ="$arrusedqty".split(",");
var scpusedqty= "$arrcpusedqty".split(",");
var sarrpcode = "$arrpcode".split(",");
var arrhourly_coupon= "$arrhourly_coupon".split(",");
var arrser_renewal_period= "$arrser_renewal_period".split(",");
var arrsub_renewal_period= "$arrsub_renewal_period".split(",");
var collected_amt=$collection_amt;
var lockdate="$lockdate";
var tax_mode = "$tax_mode";
END;
if(isset($splict_cust) and is_array($splict_cust)){
	foreach($splict_cust as $key => $value){
		print "\n splict_cust_dtl['$key']=\"".implode(',',$value)."\".split(\",\");";
	}
}
for($i=0;$i<count($ass_addl_client_price_without_tax);$i++){
	$keys=$ass_addl_client_price_without_tax[$i][0];
	$assclientprice=$ass_addl_client_price_without_tax[$i][1];
	print "\n ass_clientprice_list['$keys']='$assclientprice' ;";
}
$upgratationquery="select GFT_LOWER_PCODE, GFT_LOWER_SKEW, GFT_HIGHER_PCODE, GFT_HIGHER_SKEW, GPM_PRODUCT_CODE, GPM_PRODUCT_SKEW,GFT_SKEW_PROPERTY,GSPM_DISCOUNT_PERCENTAGE  ".
		" from GFT_PRODUCT_MASTER join gft_skew_property_master on (GSPM_CODE=gft_skew_property) where GFT_SKEW_PROPERTY in (2,24) AND GPM_STATUS='A' ";
$result_upgr=execute_my_query($upgratationquery);
while($dataupgr=mysqli_fetch_array($result_upgr)){
	print "\n upgra_lower_skew['{$dataupgr['GPM_PRODUCT_CODE']}-{$dataupgr['GPM_PRODUCT_SKEW']}-{$dataupgr['GFT_SKEW_PROPERTY']}-{$dataupgr['GSPM_DISCOUNT_PERCENTAGE']}']='{$dataupgr['GFT_LOWER_PCODE']}-{$dataupgr['GFT_LOWER_SKEW']}';";
	print "\n upgra_higher_skew['{$dataupgr['GPM_PRODUCT_CODE']}-{$dataupgr['GPM_PRODUCT_SKEW']}-{$dataupgr['GFT_SKEW_PROPERTY']}-{$dataupgr['GSPM_DISCOUNT_PERCENTAGE']}']='{$dataupgr['GFT_HIGHER_PCODE']}-{$dataupgr['GFT_HIGHER_SKEW']}';";
}
$subscripquery="select GPM_REFERER_SKEW, GPM_PRODUCT_CODE, GPM_PRODUCT_SKEW,gft_skew_property,GSPM_DISCOUNT_PERCENTAGE ".
		" from gft_product_master join gft_skew_property_master on (GSPM_CODE=gft_skew_property) ".
		" where gft_skew_property in (11,18) AND gpm_status='A' ";
$result_subscrip=execute_my_query($subscripquery);
while($datasubscrip=mysqli_fetch_array($result_subscrip)){
	print "\n referer_skew['{$datasubscrip['GPM_PRODUCT_CODE']}-{$datasubscrip['GPM_PRODUCT_SKEW']}-{$datasubscrip['gft_skew_property']}-{$datasubscrip['GSPM_DISCOUNT_PERCENTAGE']}']='{$datasubscrip['GPM_PRODUCT_CODE']}-{$datasubscrip['GPM_REFERER_SKEW']}';";	
}

$upgratationquery=" select p1.GPM_REFERER_SKEW, p1.GPM_PRODUCT_CODE, p1.GPM_PRODUCT_SKEW, p1.gft_skew_property, GSPM_DISCOUNT_PERCENTAGE, r1.GPM_ORDER_TYPE ".
				" from gft_product_master p1 ".
				" join gft_product_master r1 on (p1.GPM_PRODUCT_CODE=r1.GPM_PRODUCT_CODE and p1.GPM_REFERER_SKEW=r1.GPM_PRODUCT_SKEW) ".
				" join gft_skew_property_master on (GSPM_CODE=p1.gft_skew_property) ".
				" where p1.gft_skew_property in (4,15) AND p1.gpm_status='A' ";
$result_upgr=execute_my_query($upgratationquery);
while($dataupgr=mysqli_fetch_array($result_upgr)){
	print "\n referer_skew['{$dataupgr['GPM_PRODUCT_CODE']}-{$dataupgr['GPM_PRODUCT_SKEW']}-{$dataupgr['gft_skew_property']}-{$dataupgr['GSPM_DISCOUNT_PERCENTAGE']}']='{$dataupgr['GPM_PRODUCT_CODE']}-{$dataupgr['GPM_REFERER_SKEW']}';";
	print "\n order_type['{$dataupgr['GPM_PRODUCT_CODE']}-{$dataupgr['GPM_REFERER_SKEW']}']='{$dataupgr['GPM_ORDER_TYPE']}';";
}
if($order_catagory!=0){
echo<<<END
$("order_catagory{$_GET['order_catagory']}").checked=true;
show_order_catagory($("order_catagory$order_catagory"));
END;
if($order_catagory==2 and $formtype!='edit'){
echo<<<END
set_asa_values();
END;
}
}else if( ($formtype!='edit') && !$from_quotation){
echo<<<END
$("order_catagory0").checked=true;
show_order_catagory($("order_catagory0"));
END;
}
echo<<<END
setvalues();
show_others_detail();
</script>

<script> //service_order support_order  lease_order
jQuery.noConflict();
var client_skew_type = new Array('3','13','14','16');
function expense_custom(){
	var expense_type = jQuery("#god_expense_type").val();
	if(expense_type==4) {
		jQuery("#custom_td").removeClass("hide").addClass("show");		
	}else{
		jQuery("#custom_td").removeClass("show").addClass("hide");	
	}			
}

function set_goodwill_value_to_default(){
	if(jQuery("#goodwill") && jQuery("#goodwill_per")){
		jQuery("#goodwill").val(0.00);
		jQuery("#goodwill").removeAttr("disabled"); 
		jQuery("#goodwill_per").val(0.00);
		jQuery("#goodwill_per").removeAttr("disabled"); 
	}
}

function convert_goodwill(type){
	var net_tt = parseFloat(jQuery("#nettotal").val());
	var given_amt = jQuery("#goodwill").val();
	var given_percent = jQuery("#goodwill_per").val();
	if(given_amt!=''){
		given_amt = parseFloat(given_amt);
	}else{
		given_amt = 0;
	}
	if(given_percent!=''){
		given_percent = parseFloat(given_percent);
	}else{
		given_percent = 0;
	}
	if(type==1){
		if(given_amt > net_tt){
			alert("Discount is greater than total amount. Cannot be allowed");
			jQuery("#goodwill").val(0.00);
			return false;
		}
		var perc = ((given_amt/net_tt)*100).toFixed(4);
		jQuery("#goodwill_per").val(perc);
		if(given_amt > 0){
			jQuery("#goodwill_per").attr("disabled","disabled");
		}else{
			jQuery("#goodwill_per").removeAttr("disabled"); 
		}
	}else if(type==2){
		if( given_percent > 100.00 ){
			alert("Discount is greater than total amount. Cannot be allowed");
			jQuery("#goodwill_per").val(0);
			return false;
		}
		var amt = ((net_tt*given_percent)/100).toFixed(4);
		jQuery("#goodwill").val(amt);
		if(given_percent > 0){
			jQuery("#goodwill").attr("disabled","disabled");
		}else{
			jQuery("#goodwill").removeAttr("disabled");
		}
	}
	emailcheck123();
}

function check_pro_list(obj){
	var tot_install_info	=	jQuery('.hidden_all_install').length;
	var j=0;
	var sel_pro = jQuery(obj).val();
	if(sel_pro==0) {
		return;
	}
	var currr_val= sel_pro.split('-');
	var id_no	=	(jQuery(obj).attr('id')).substring(12);
	var pcode	=	currr_val[0];
	var skew	=	currr_val[1].substring(0,4);
	var skew_type=	currr_val[2];
	var choose_root_info	=	'';
	var choose_root_infor_cus=	'';
	jQuery('#additional_custom'+id_no).val('0');
	while(j<tot_install_info){
		var ins_val = jQuery('#all_install_details'+j).val().split('**');
		var server_skew	=	0;
		if(skew_type==3 || skew_type==14){
			server_skew	=	1;
		}
		if(skew_type==13 || skew_type==16){
			server_skew	=	11;
		}
		var order_type_name	=	''
			if(ins_val[7]=='N'){
				order_type_name	=	'Not installed Order'
			}else{
				order_type_name	=	'Installed Order'
			}
		var order_type_name	=	''
			if(ins_val[7]=='N'){
				order_type_name	=	'Not installed Order'
			}else{
				order_type_name	=	'Installed Order'
			}
		var is_custom_pro	=	false;
		if(jQuery.inArray(sel_pro,custom_skews) >= 0) {
			is_custom_pro	=	true;
		}
		var skewtype_check = false;
		if(server_skew==ins_val[5]){
			skewtype_check = true;
		}
		if(jQuery("#order_catagory0").is(":checked") && jQuery("#order_catagory1").is(":checked") && !is_custom_pro ){
			skewtype_check = true;
		}
		if( (pcode==ins_val[3]) && (skew==(ins_val[4]).substring(0,4)) && skewtype_check ){
			choose_root_info	+=	'<input type="radio" class="clientref_order" id_no="'+id_no+'" name="clientref_order_no'+id_no+'" value="'+ins_val[1]+'-'+ins_val[2]+'-'+ins_val[3]+'-'+ins_val[4]+'">'+order_type_name+' - '+ins_val[1]+'  - '+ins_val[6]+'<br>'; //ins_val[1]+"+++";
			jQuery('#additional_custom'+id_no).val('1');
		}
		if((skew_type==1 || skew_type==11) && is_custom_pro && pcode==ins_val[3] && skew==(ins_val[4]).substring(0,4)){
			choose_root_infor_cus	+=	'<input type="radio" class="clientref_order" id_no="'+id_no+'" name="clientref_order_no'+id_no+'" value="'+ins_val[1]+'-'+ins_val[2]+'-'+ins_val[3]+'-'+ins_val[4]+'">'+order_type_name+' - '+ins_val[1]+'  - '+ins_val[6]+'<br>'; //ins_val[1]+"+++";
			jQuery('#additional_custom'+id_no).val('1');
		}					
	
		j++;
	}
	if(choose_root_info!=''){
		var this_order_info	=	'<br><input type="radio" class="clientref_order" id_no="'+id_no+'" name="clientref_order_no'+id_no+'" value="0">It is for current order<br>';
		choose_root_info	= 	this_order_info+choose_root_info;
	}
	if(choose_root_infor_cus!=''){
		var this_order_info	=	'<br><input type="radio" class="clientref_order" id_no="'+id_no+'" name="clientref_order_no'+id_no+'" value="0">It is for current order<br>';
		choose_root_infor_cus	= 	this_order_info+choose_root_infor_cus;
	}
	if(skew_type!=18 && skew_type!=20 && skew_type!=21 &&  (jQuery('#server_product_list'+id_no).html()=="")){
		jQuery('#server_product_list'+id_no).html(choose_root_info+choose_root_infor_cus);
	}	
}

jQuery(document).ready(function(){
	expense_custom();
	jQuery("#adv").val(advance_val);
	jQuery('.pro_list').live('change',function(){
		checkAllOrderPD();
		check_pro_list(this);
	});
	if(GLOBAL_GST_MODE!="1" || (tax_mode!='4' && tax_mode!='3')){
		jQuery(".vat").removeClass("hide").addClass("show");
		jQuery(".gst").removeClass("show").addClass("hide");
	} else {
		jQuery(".vat").removeClass("show").addClass("hide");
		jQuery(".gst").removeClass("hide").addClass("show");
	}
		jQuery(".clientref_order").live("click",function(){
		var id_no	=	jQuery(this).attr('id_no');
		var sel_pro = jQuery("#product_code"+id_no).val();
		var current_val	=	(jQuery("#product_code"+id_no).val()).split('-');
		var cur_skew_type=	current_val[2];
		if(cur_skew_type==3 || cur_skew_type==14 || cur_skew_type==11){
				cur_skew_type	=	1;
		}else if(cur_skew_type==13 || cur_skew_type==16){
			cur_skew_type	=	11;
		}
		if(jQuery(this).val()==0){
			jQuery('#additional_custom_order'+id_no).val(jQuery('#order_no').val());
			var numItems = jQuery('.pro_list').length;
			var i	=	1;
			var item_selected	=	false;
			var cur_pcode	= current_val[0];	
			var cur_skew	=	(current_val[1]).substring(0,4);
			var it_is_custom_lice	=	false;
			if(jQuery.inArray(sel_pro,custom_skews) >= 0) {
				it_is_custom_lice	=	true;
			}
			while(i<=numItems){		
				var item_byone	=	(jQuery("#product_code"+i).val()).split('-');
				var is_custom_namechange	=	true;
				if(jQuery.inArray(jQuery("#product_code"+i).val(),custom_skews) >= 0) {
					is_custom_namechange	=	false;
				}
				if(is_custom_namechange){
					if(it_is_custom_lice){
						if(item_byone[0]==cur_pcode && ((item_byone[1]).substring(0,4))==cur_skew && (item_byone[2]==1 || item_byone[2]==11)){
							item_selected	=	true;
						}
					}else{
						if(item_byone[0]==cur_pcode && ((item_byone[1]).substring(0,4))==cur_skew && item_byone[2]==cur_skew_type){
							item_selected	=	true;
						}
					}					
				}
				i++;
			}
			if(!item_selected){
				alert("Not selected Server product for this product(Additional Client or Custom License)");
				jQuery('#additional_custom_order'+id_no).val("");
				jQuery(this).attr('checked', false);
			}
			
		}else{
			jQuery('#additional_custom_order'+id_no).val(jQuery(this).val());
			if( jQuery("#order_catagory0").is(":checked") && jQuery("#order_catagory1").is(":checked") ){
				if(client_skew_type.indexOf(current_val[2]) >= 0 ){
					var matched = false;
					for(var x=1;x<=upgradationcounter;x++){
						var s_product = jQuery("#upproduct_code"+x).val();
						if(s_product!='0'){
							var up_skew = upgra_higher_skew[s_product].split('-');
							jQuery.ajax({
								type:'POST',
								url:'service/process_the_request.php',
								data:'purpose=get_skew_type&pcode='+up_skew[0]+'&pskew='+up_skew[1],
								dataType:'text',
								async:false,
								success:function(ret){
									if(ret==cur_skew_type){
										matched = true;
									}
								}
							});
						}
					}
					if(matched==false){
						alert("Mismatch in License Type(Perpetual/Subscription) of selected Client and Upgradation product. Choose the correct Product");
						jQuery('#additional_custom_order'+id_no).val("");
						jQuery(this).attr('checked', false);
					}
				}
			}
		}
	});	
		
		
	jQuery('.upproduct_code').live('change',function(){
		checkAllOrderPD();		
	});
	
	jQuery('.assproduct_code').live('change',function(){
		checkAllOrderPD();		
	});
	jQuery('.service_order').live('change',function(){
		checkAllOrderPD();		
	});
	jQuery('.support_order').live('change',function(){
		checkAllOrderPD();		
	});
	
	jQuery("input[name='pending_imp'][value=No]").click(function(){
		checkAllOrderPD();
	});
	jQuery("input[name='pending_imp'][value=Yes]").click(function(){
		checkOrderOptionalPD();
	});	
	function checkOrderOptionalPD(){
				
		var status=	'N';
		if(jQuery('#divorder_catagory0').attr('class')=='unhide'){
			var numItems = jQuery('.pro_list').length
			var i	=	1;
			while(i<=numItems){
				var id	='product_code'+i;				
				if(jQuery('option:selected', jQuery('#'+id)).attr('pdoption')=='Y'){
					status=	'Y';
				}			
				i++;
			}
		}
		if(jQuery('#divorder_catagory1').attr('class')=='unhide'){
			var numItems1 = jQuery('.upproduct_code').length
			i	=	1;
			while(i<=numItems1){
				var id	='upproduct_code'+i;
				if(jQuery('option:selected', jQuery('#'+id)).attr('pdoption')=='Y'){
					status=	'Y';
				}
				i++;
			}
		}
		if(jQuery('#divorder_catagory2').attr('class')=='unhide'){
			var numItems2 = jQuery('.assproduct_code').length
			i	=	1;
			while(i<=numItems2){
				var id	='assproduct_code'+i;
				if(jQuery('option:selected', jQuery('#'+id)).attr('pdoption')=='Y'){
					status=	'Y';
				}
				i++;
			}
		}
		if(jQuery('#divorder_catagory12').attr('class')=='unhide'){
			var numItems3 = jQuery('.service_order').length
			i	=	1;
			while(i<=numItems3){
				var id	='serviceproduct_code'+i;
				if(jQuery('option:selected', jQuery('#'+id)).attr('pdoption')=='Y'){
					status=	'Y';
				}
				i++;
			}
		}
		if(jQuery('#divorder_catagory5').attr('class')=='unhide'){
			var numItems4 = jQuery('.support_order').length
			i	=	1;
			while(i<=numItems4){
				var id	='supportproduct_code'+i;
				if(jQuery('option:selected', jQuery('#'+id)).attr('pdoption')=='Y'){
					status=	'Y';
				}
				i++;
			}
		}
		if(status=='N'){
			jQuery("input[name=pending_imp][value=No]").attr('checked', 'checked');
			training_required_option();
		}else{
			jQuery("input[name=pending_imp][value=Yes]").attr('checked', 'checked');
			training_required_option();	
		}	
	}
	function checkAllOrderPD(){
		if(jQuery("input[name=pending_imp][value=Yes]").length == 0){
			return;
		}
		var status=	'N';
		if(jQuery('#divorder_catagory0').attr('class')=='unhide'){
			var numItems = jQuery('.pro_list').length
			var i	=	1;
			while(i<=numItems){
				var id	='product_code'+i;
				if(jQuery('option:selected', jQuery('#'+id)).attr('pdoption')=='Y'){
					status=	'Y';
				}			
				i++;
			}
		}
		if(jQuery('#divorder_catagory1').attr('class')=='unhide'){
			var numItems1 = jQuery('.upproduct_code').length
			i	=	1;
			while(i<=numItems1){
				var id	='upproduct_code'+i;
				if(jQuery('option:selected', jQuery('#'+id)).attr('pdoption')=='Y'){
					status=	'Y';
				}
				i++;
			}
		}
		if(jQuery('#divorder_catagory2').attr('class')=='unhide'){
			var numItems2 = jQuery('.assproduct_code').length
			i	=	1;
			while(i<=numItems2){
				var id	='assproduct_code'+i;
				if(jQuery('option:selected', jQuery('#'+id)).attr('pdoption')=='Y'){
					status=	'Y';
				}
				i++;
			}
		}
		if(jQuery("#service_dtl1").length != 0 && jQuery('#service_dtl1').attr('class')=='hide') {
 			if(jQuery('#divorder_catagory12').attr('class')=='unhide'){
			var numItems3 = jQuery('.service_order').length
			i	=	1;
			while(i<=numItems3){
				var id	='serviceproduct_code'+i;
				if(jQuery('option:selected', jQuery('#'+id)).attr('pdoption')=='Y'){
					status=	'Y';
				}
				i++;
			}
		}
		}		
		if(jQuery('#divorder_catagory5').attr('class')=='unhide'){
			var numItems4 = jQuery('.support_order').length
			i	=	1;
			while(i<=numItems4){
				var id	='supportproduct_code'+i;
				if(jQuery('option:selected', jQuery('#'+id)).attr('pdoption')=='Y'){
					status=	'Y';
				}
				i++;
			}
		}
		if(status=='Y'){
			jQuery("input[name=pending_imp][value=Yes]").attr('checked', 'checked');
			jQuery("input[name=deliver_by][value='70,72,5']").attr('checked', true);
			training_required_option();	
		}else{
			jQuery("input[name=pending_imp][value=No]").attr('checked', 'checked');
			training_required_option();			
		}
	}
	jQuery("#ord_status").change(function() {
		var stat=jQuery("#ord_status").val();
		var cp = jQuery("#cp_id").val();
		if(stat=='C'){
			if(cp=='1'){
				jQuery("#debit").removeClass("hide").addClass("show");
			}else if(jQuery("#refund_combo")){
				jQuery("#refund_combo").removeClass("hide").addClass("show");
			}
		}
	});
	jQuery("#refund_combo").change(function() {
		var refund_stat = jQuery("#re_stat").val();
		if(refund_stat=="YES"){
			jQuery("#refund_val").removeClass("hide").addClass("show");
			jQuery("#refund_comm").removeClass("hide").addClass("show");
		}else{
			jQuery("#refund_val").removeClass("show").addClass("hide");
			jQuery("#refund_comm").removeClass("show").addClass("hide");
		}
	});
	jQuery("#pay_code").change(function() {
		var asa = jQuery("#order_catagory2").is(':checked');
		var pay = jQuery("#pay_code").val();
		if(asa) {
			if(pay=='4-1.00'){
				jQuery("#pay_code").val('0');
				alert("For ASA order, Token advance payment type not applicable");
				jQuery("#receipt_dtl").removeClass("unhide").addClass("hide");
				return false;
			}
		}
		if(pay=='1-100.00'){
			jQuery("#collection_date").val("$today");
			jQuery("#ramt").val(Math.round(jQuery("#grandtotal").val()));
		}else{
			jQuery("#collection_date").val("");
			jQuery("#ramt").val("");
		}
	});
	if(disable_mode==1){
		checkAllOrderPD();
		for(i=1;i<=jQuery(".pro_list").length;i++) {
			check_pro_list(jQuery("#product_code"+i));
		}					
	}
	if(jQuery("#dealer_order").val()=='1'){
		jQuery("#pay_code").val('1-100.00');
		jQuery(".notForDealer").addClass('hide');
		jQuery("#collection_date").val("$today");
		showlaptop('laptop');
	}
	var ref_install_id_arr = "$ref_install_id".split(',');
	for(i=1;i<=jQuery(".install_table").length;i++){
		if(ref_install_id_arr.indexOf(jQuery("#ins_reff_id"+i).val()) >= 0){
			jQuery("#splitupdate"+i).attr("checked",true);
		}
	}
});
jQuery('#fix_adj_amt').val('1');
</script>
END;
require_once(__DIR__ ."/footer.php");
?>
<style>
.group-tab { 
	width: 70%;
	margin:auto;
}
.group-div {
	width: 100%;
	margin: auto;
	border: 1px solid black;
	border-radius: 5px;
}
.group-tab td {
	width: 50%;
}
legend {
	font-size: larger;
	font-weight: bold; 
}
</style>
