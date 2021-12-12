<?php 
require_once(__DIR__.'/dbcon.php');
require_once(__DIR__.'/receipt_util.php');
require_once(__DIR__.'/common_filter.php');

/**
 * @param int $screen_type
 * 
 * @return void
 */
function include_javascript_variables($screen_type=1){//1-Quote, 2-Proforma, 3-Order
	global $global_gst_mode;
	if($screen_type!=3){
		echo include_js_file("js/js_quotation_new.js");
	}
	echo include_js_file("js/js_quote_util.js");
	echo<<<END
<script>
var GLOBAL_GST_MODE = "$global_gst_mode";
var product_price=new Array();
var product_uprice=new Array();
var extra_price=new Array();
var referer_skew=new Array();
var order_type=new Array();
var product_tax=new Array();
var product_sertax=new Array();
var extra_tax=new Array();
var extra_sertax=new Array();
var upgra_lower_skew=new Array();
var upgra_higher_skew=new Array();
var family_head=new Array();
var type_of_prod=new Array();
var gpm_category=new Array();
var custom_skews=new Array();
var ass_usd_clientprice_list=new Array();
var ass_clientprice_list=new Array();
var server_prod = new Array();
var gpm_per_user = new Array();
var hourly_coupon= new Array();
var gst_tax_val = new Array();
var no_off_coupons=new Array();
var coupon_for_local=Array();
var coupon_for_exstation=Array();
var coupon_for_outstation=Array();
var coupon_for_online=Array();
var coupon_for_pcs=Array();
var total_training_hrs=Array();
var saas_register_reference=Array();
var saas_user_reference=Array();
var saas_register_reference_amt=Array();
var saas_user_reference_amt=Array();
var edit_saas_register_list=Array();
var edit_saas_user_list=Array();
var product_type = Array();
var show_renewal_period = Array();
var renewal_period_label = Array();
var renewal_period_input_type = Array();
var splict_cust_dtl=new Array();
</script>
END;
}
/**
 * @param boolean $include_asa
 * @param string $order_type
 * 
 * @return string
 */
function return_order_type_ui($include_asa=true,$order_type=''){
	$js_condition = "onchange='javascript:show_order_catagory(this);'";
	$saas_order_option = "";
	if($order_type=="partner"){
		$js_condition="";
		$saas_order_option = "<td id='saas_order' class=''>	<input name='order_catagory[]' id='order_catagory6' onchange='javascript:show_saas_order_entry(this);' value='6' type='checkbox'><label for='order_catagory6'>SaaS Order</label>&nbsp;&nbsp;	</td>";
	}
	$ret_txt = "<table border='0'><tr>
					<td>Order type&nbsp;&nbsp;</td>
					<td id='server_order'>		<input name='order_catagory[]' id='order_catagory0' $js_condition value='0' type='checkbox'><label for='order_catagory0'>Server Order</label>&nbsp;&nbsp; </td>
					<td id='upgradation_order'>	<input name='order_catagory[]' id='order_catagory1' $js_condition value='1' type='checkbox'><label for='order_catagory1'>Upgradation Order</label>&nbsp;&nbsp;</td>";
	if($include_asa){
		$ret_txt .= "<td id='ass_order'><input name='order_catagory[]' id='order_catagory2' $js_condition value='2' type='checkbox'><label for='order_catagory2'>ALR Order</label>&nbsp;&nbsp; ";
	}else {
		//$ret_txt .= "<input id='ordertype_catagory2' type='hidden'>";		
		$ret_txt .= "<td class='hide' id='ass_order'><input name='order_catagory[]' id='order_catagory2' $js_condition value='2' type='checkbox'><label for='order_catagory2'>ALR Order</label>&nbsp;&nbsp; ";
	}
	$ret_txt .= "<td id='subscription_renewal'>		<input name='order_catagory[]' id='order_catagory13' $js_condition value='13' type='checkbox'><label for='order_catagory13'>Subscription Renewal </label>&nbsp;&nbsp; </td>
				<td id='service_incidenent_order'	><input name='order_catagory[]' id='order_catagory12' $js_condition value='12' type='checkbox'><label for='order_catagory12'>Service Incident Order</label>&nbsp;&nbsp;</td>
				<td id='support_incidendent_order'>	<input name='order_catagory[]' id='order_catagory5' $js_condition value='5' type='checkbox'><label for='order_catagory5'>Support Incident Order</label>&nbsp;&nbsp;	</td>
                $saas_order_option				
                </tr></table>";
	
	return $ret_txt;
}
/**
 * @param string $lead_code
 * @param string $tax_mode
 * 
 * @return string
 */
function get_applicable_tax_ui($lead_code,$tax_mode){
	global $global_gst_mode;
	$taxvat= $taxcst = $taxexp = '';
	if($tax_mode=='1' or $tax_mode==''){
		$taxvat='checked';
	}else if($tax_mode=='2'){
		$taxcst='checked';
	}else if($tax_mode=='3'){
		$taxexp='checked';
	}
	$ret_txt = "GST<INPUT type='hidden' name='tax_mode' id='tax_mode' value='4'>";
	$sql1 = " select GLH_COUNTRY from gft_lead_hdr where GLH_LEAD_CODE='$lead_code' ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$country = $row1['GLH_COUNTRY'];
		if(strcasecmp($country, "India")!=0){
			$taxexp='checked';
			$ret_txt = "Export<INPUT type='hidden' name='tax_mode' id='tax_mode' value='3'>";
		}
	}
	if($global_gst_mode==0 or (in_array($tax_mode,array('1','2')))){
		$ret_txt = "<INPUT type='radio' name='tax_mode' id='tax_mode_vat' value='1' $taxvat onchange='javascript:tax_mode_change()'/><label for='tax_mode_vat'>VAT</label>".
				"<INPUT type='radio' name='tax_mode' id='tax_mode_cst' value='2' $taxcst onchange='javascript:tax_mode_change()'/><label for='tax_mode_cst'>CST C&quot; Form</label>".
				"<INPUT type='radio' name='tax_mode' id='tax_mode_exp' value='3' $taxexp onchange='javascript:tax_mode_change()'/><label for='tax_mode_exp'>Export</label>";
	}
	return $ret_txt;
}

/**
 * @param string $lead_code
 * 
 * @return string[string]
 */
function get_currency_ui($lead_code){
	$country = get_single_value_from_single_table("GLH_COUNTRY", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
	$inr_select = $usd_select = '';
	if(strcasecmp($country, "India")==0){
		$inr_select = "selected";
		$currency_val="INR";
	}else{
		$usd_select = "selected";
		$currency_val="USD";
	}
	$ret_arr['html_txt'] = "<select name='currency_temp' id='currency_temp' disabled=true class='formStyleTextarea'>".
						"<option value='INR' $inr_select>INR</option><option value='USD' $usd_select>USD</option>".
						"<input type='hidden' value='$currency_val' id='currency' name='currency'></select>";
	$ret_arr['currency_val'] = $currency_val;
	return $ret_arr;
}

/**
 * @param string $lead_code
 * @param string $employee_id
 * @param string $quotation_no
 * @param string $proforma_no
 * @param string $existing_p_no
 * @param string $existing_p_type
 * @param string $order_no
 * @param string $order_form_type
 * @param string[int] $split_arr
 * 
 * @return string
 */
function get_header_data_for_quote($lead_code,$employee_id,$quotation_no='',$proforma_no='',$existing_p_no='',$existing_p_type='',$order_no='',$order_form_type='new',$split_arr=null){
	global $balance_amt;
	$emp_name = get_single_value_from_single_table("GEM_EMP_NAME", "gft_emp_master", "GEM_EMP_ID", $employee_id);
	$cust_name = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
	$str_label = $str_val = $tax_mode = $order_date = '';
	$additional_row = "";
	$advance_cell = $balance_cell = $split_order_cell = '';
	$emp_name_cell =<<<END
	<td><input type='text' id='emp_name' name='emp_name' value='$emp_name' readonly>
	<input type='hidden' id='emp_code' name='emp_code' value='$employee_id'></TD>
END;
	$js = '';
	if($quotation_no!=''){
		$str_label = 'Quotation';
		$str_val = $quotation_no;
		$order_date = get_single_value_from_single_table("GQH_ORDER_DATE", "gft_quotation_hdr", "GQH_ORDER_NO", $quotation_no);
		$tax_mode = get_single_value_from_single_table("GQH_TAX_MODE", "gft_quotation_hdr", "GQH_ORDER_NO", $quotation_no);
	}else if($proforma_no!=''){
		$str_label = 'Proforma';
		$str_val = $proforma_no;
		$order_date = get_single_value_from_single_table("GPH_ORDER_DATE", "gft_proforma_hdr", "GPH_ORDER_NO", $proforma_no);
		if($order_date==''){
		    $order_date = date('Y-m-d H:i:s');
		}
		$additional_row .= "<tr><td class='datalabel' width='175'>Status</td>
								<td><select name='q_status' id='q_status' class='formStyleTextarea'><option value='A'>Active</option><option value='C'>Cancel</option></select></td>
								<td><input type='hidden' name='existing_p_no' id='existing_p_no' value='$existing_p_no'></td>
							</tr>";
		$js =<<<JS
		<script type='text/javascript'>
			jQuery('document').ready(function() {	
				jQuery("#q_status").val(jQuery("#q_status option:first").val());
			});
		</script>
JS;
	}else if($order_no!='') {
		$str_label = "Order";
		$str_val   = $order_no;
		$advance_amount = get_customer_advance($lead_code);
		$advance_cell 	=<<<END
		<td class="datalabel">Advance:</td><td>Rs $advance_amount</td>
END;
		$balance_cell 	=<<<END
		<TD class="datalabel">Outstandings:</td><td>Rs $balance_amt<a href="javascript:call_popup('order_releated_details.php?lcode=$lead_code',7);">[O]</a></TD>
END;
		if($order_form_type=='edit') {
			if(is_authorized_group_list($employee_id,array(1,17))) {
				$em_names = get_employee_list(0,null,null,null,null,null,null,null,'off','emp_name','emp_code',true);
				$emp_name_cell =<<<END
					<td>
					<script  type="text/javascript">
					function empEmptyFunction(){}
					function empDetails(){}
					function checks(){ return true;}
					</script>$em_names</td>
END;
			}
		}
		$tax_mode = get_single_value_from_single_table("GOD_TAX_MODE", "gft_order_hdr", "GOD_ORDER_NO", $order_no);
		$split_order_cell =<<<END
		<TD class="datalabel">Splitable Order:</td><td>
		<INPUT type="radio" name="splictorder" id="splictorderyes" value=1 $split_arr[0]><label for="splictorderyes">Yes</label>
		<INPUT type="radio" name="splictorder" id="splictorderno" value=0 $split_arr[1]><label for="splictorderno">No</label></td>
END;
		$order_date = get_single_value_from_single_table("god_order_date", "gft_order_hdr", "god_order_no", $order_no);
	}
	if($order_date==''){
		$order_date = date('Y-m-d');
	}
	$print_tax = get_applicable_tax_ui($lead_code,$tax_mode);
	$currency_dtl = get_currency_ui($lead_code);
	$print_currency = $currency_dtl['html_txt'];
	
	$ret_txt = "<TABLE border=0 cellPadding=1 cellSpacing=1 width='900px' >
					<TR><TD class='datalabel' >Customer Name</TD>
						<TD><input type='text' name='lead_name'  size='35' value='$cust_name' class='formStyleTextarea' Readonly='true'></TD>
						<td class='datalabel' align='left' nowrap>$str_label Date </td>
						<td><input name='order_date' id='order_date' type='text' class='formStyleTextarea'  value='$order_date' readonly></td>
						$advance_cell
					</tr>
					<TR><TD class='datalabel'>$str_label No</TD>
						<TD><INPUT TYPE='text' id='order_no' name='order_no' class='formStyleTextarea' value='$str_val' Readonly='true'></TD>
						<TD class='datalabel'>Employee Name </td>
						$emp_name_cell$balance_cell
					</TR>
					<tr><TD class='datalabel'>Tax Mode</TD><TD>$print_tax</TD>
						<TD class='datalabel'>Currency</TD>	<TD>$print_currency</TD>
						$split_order_cell
					</tr>
					$additional_row
				</table>";
	return $ret_txt.$js;
}

/**
 * @param string $add_function
 * @param string $remove_function
 * @param boolean $skip_this
 * 
 * @return string
 */
function get_add_remove_row($add_function,$remove_function,$skip_this=false){
	$output1 = "";
	if(!$skip_this) {
		$output1 = "<tr><td colspan='14' align='center'>
						<input type='button' class='button' onclick='$add_function();' value='Add'> 
						<input type='button' class='button' onclick='$remove_function();' value='Remove'>
					</td></tr>";
	}
	return $output1;
}

/**
 * @param string $title
 * @param string $qtylabel
 * 
 * @return string
 */
function column_header_row($title,$qtylabel='Quantity'){
	$output1 = "<tr class='modulelisttitle'><td colspan='14' class='head_black_11' align='center'>$title</td></tr>
				<tr class='modulelisttitle' >
					<TD class='head_black_11'>S.No</TD>
					<TD class='head_black_11'>Product Name</TD>
					<TD class='head_black_11' width='50'>List Price</TD>
					<TD class='head_black_11' width='50'>$qtylabel</TD>
					<TD class='head_black_11' width='50'>Sales-Rate</TD>
					<TD class='head_black_11' width='50'>Discount Amount</TD>
					<TD class='head_black_11' width='50'>Discount %</TD>
					<TD class='head_black_11 vat' width='50'>Tax %</TD>
					<TD class='head_black_11 vat' width='50'>Service Tax %</TD>
					<TD class='head_black_11 gst' width='50'>CGST %</TD>
					<TD class='head_black_11 gst' width='50'>SGST %</TD>
					<TD class='head_black_11 gst' width='50'>IGST %</TD>
					<TD class='head_black_11' width='50'>Net Amount</TD>
				</tr>";
	return $output1;
}

/**
 * @param string $category_str
 * @param string $formtype
 * @param string $readonly_attr
 * @param boolean $from_order
 * 
 * @return string
 */
function column_for_tax_and_discount($category_str,$formtype,$readonly_attr='',$from_order=false){
	global $uid;
	$net_amt_readonly = "";
	if(!is_authorized_group_list($uid, array(101,106,36)) and $from_order) {
		$net_amt_readonly = "readonly";
	}
	$cust_column = "";
	if($category_str=='ass'){
		$cust_column = "<INPUT type='hidden' name='custom_skew[1]' id='custom_skew1' value=''><INPUT type='hidden' name='no_of_client[1]' id='no_of_client1' value=''>";	
	}
	$number_function = "onkeyup='javascript:extractNumber(this,2,false);' onkeypress='javascript:return blockNonNumbers(this, event, false, false);'";
	$ee=($formtype=='new')?"Readonly='true'":'';
	$output1 = "<TD valign='top'><INPUT  name='{$category_str}listprice[1]' id='{$category_str}listprice1' size='10' class='formStyleTextarea' onblur='emailcheck(this,0);' $ee $number_function></TD>
				<TD valign='top'><INPUT  name='{$category_str}qty[1]' id='{$category_str}qty1' size='4'  class='formStyleTextarea' $readonly_attr onblur='return emailcheck(this,\"$category_str\");'  $number_function >
					<INPUT type=hidden name='{$category_str}usedqty[1]' id='{$category_str}usedqty1' value='0' >
					<INPUT type=hidden id='{$category_str}cpusedqty1' name='{$category_str}cpusedqty[1]' value='0' >
				</TD>
				<TD valign='top'><INPUT class='formStyleTextarea' $readonly_attr name='{$category_str}sell[1]' 	 id='{$category_str}sell1' 	size='9'  onblur='return emailcheck(this,\"$category_str\");' onkeyup='javascript:extractNumber(this,2,false);' onkeypress='javascript:return blockNonNumbers(this, event, true, false);' ></TD>
				<TD valign='top'><INPUT class='formStyleTextarea'  name='{$category_str}dis[1]' 	 id='{$category_str}dis1' 		size='8' Readonly></TD>
				<TD valign='top'><INPUT class='formStyleTextarea' name='{$category_str}dis_per[1]'  id='{$category_str}dis_per1' 	size='5' Readonly></TD>
				<TD valign='top' class='vat'><INPUT class='formStyleTextarea' name='{$category_str}tax[1]' 		id='{$category_str}tax1' 	size='4'  Readonly></TD>
				<TD valign='top' class='vat'><INPUT class='formStyleTextarea' name='{$category_str}sertax[1]' 	id='{$category_str}sertax1' size='4' Readonly></TD>
				<TD valign='top' class='gst'><INPUT class='formStyleTextarea' name='{$category_str}cgst[1]' 	id='{$category_str}cgst1' 	size='4'  Readonly></TD>
				<TD valign='top' class='gst'><INPUT class='formStyleTextarea' name='{$category_str}sgst[1]' 	id='{$category_str}sgst1' 	size='4'  Readonly></TD>
				<TD valign='top' class='gst'><INPUT class='formStyleTextarea' name='{$category_str}igst[1]' 	id='{$category_str}igst1' 	size='4'  Readonly></TD>
				<TD valign='top'><INPUT class='formStyleTextarea' name='{$category_str}total[1]' 	id='{$category_str}total1'  $net_amt_readonly size='10' onblur='return emailcheck(this,\"$category_str\");' onkeyup='javascript:extractNumber(this,2,false);' onkeypress='javascript:return blockNonNumbers(this, event, true, false);' >$cust_column</TD>";
	return $output1;
}

/**
 * @param string[int][int] $price_list_arr
 * @param string $product_property
 * 
 * @return string
 */
function return_quote_script_varibles_from_array($price_list_arr,$product_property=''){
	global $order_no;
	$ser_tax = $sal_tax = array();
	if($order_no!='') {
		$tax_qry = " select concat(gop_product_code,'-',gop_product_skew) k,GOP_TAX_RATE,GOP_SERVICE_TAX_RATE from gft_order_product_dtl where gop_order_no='$order_no' ";
		$res = execute_my_query($tax_qry);
		while($row = mysqli_fetch_array($res)) {
			$ser_tax[$row['k']] = $row['GOP_SERVICE_TAX_RATE'];
			$sal_tax[$row['k']] = $row['GOP_TAX_RATE'];
		}
	}
	$script_var = "<script>";
	$c = 0;
	for($i=0;$i<count($price_list_arr);$i++){
		$keys			=	$price_list_arr[$i][0];
		$prices			=	$price_list_arr[$i][2];
		$uprices		=	$price_list_arr[$i][10];
		$tax			=	(isset($sal_tax[$keys]) and (float)$sal_tax[$keys]>0.0)?$sal_tax[$keys]:$price_list_arr[$i][3];
		$sertax			=	(isset($ser_tax[$keys]) and (float)$ser_tax[$keys]>0.0)?$ser_tax[$keys]:$price_list_arr[$i][4];
		$family_head	=	$price_list_arr[$i][9];
		$gpm_category	=	$price_list_arr[$i][12];
		$type_of_prod	=	$price_list_arr[$i][15];
		$gpm_per_user	=	$price_list_arr[$i][16];
		$gst_tax_val	=	$price_list_arr[$i][27];
		$hourly_coupon	=	$price_list_arr[$i][25];
		$total_training_hrs 	= $price_list_arr[$i][24];
		$coupon_for_pcs			= $price_list_arr[$i][26];
		$coupon_for_local		= $price_list_arr[$i][20];
		$coupon_for_exstation	= $price_list_arr[$i][21];
		$coupon_for_outstation	= $price_list_arr[$i][22];
		$no_coupons				= $price_list_arr[$i][11];
		$product_type = $price_list_arr[$i][14];
		$show_renewal_period = $price_list_arr[$i][29];
		$renewal_period_label = $price_list_arr[$i][30];
		$renewal_period_input_type = $price_list_arr[$i][31];
		$coupon_for_online=$price_list_arr[$i][23];
		$script_var .= "\n product_price['$keys']='$prices';";
		$script_var .= "\n product_tax['$keys']='$tax';";
		$script_var .= "\n product_sertax['$keys']='$sertax';";
		$script_var .= "\n family_head['$keys']='$family_head';";
		$script_var .= "\n type_of_prod['$keys']='$type_of_prod';";
		$script_var .= "\n product_uprice['$keys']='$uprices' ;";
		$script_var .= "\n gpm_per_user['$keys']='$gpm_per_user';";
		$script_var .= "\n gpm_category['$keys']='$gpm_category';";
		$script_var .= "\n gst_tax_val['$keys']='$gst_tax_val';";
		$script_var .= "\n hourly_coupon['$keys']='$hourly_coupon';";
		$script_var .= "\n no_off_coupons['$keys']='$no_coupons';";
		$script_var .= "\n coupon_for_local['$keys']='$coupon_for_local';";
		$script_var .= "\n coupon_for_exstation['$keys']='$coupon_for_exstation';";
		$script_var .= "\n coupon_for_outstation['$keys']='$coupon_for_outstation';";
		$script_var .= "\n coupon_for_online['$keys']='$coupon_for_online';";
		$script_var .= "\n coupon_for_pcs['$keys']='$coupon_for_pcs';";
		$script_var .= "\n total_training_hrs['$keys']='$total_training_hrs';";
		$script_var .= "\n product_type['$keys']='$product_type';";
		$script_var .= "\n show_renewal_period['$keys']='$show_renewal_period';";
		$script_var .= "\n renewal_period_label['$keys']='$renewal_period_label';";
		$script_var .= "\n renewal_period_input_type['$keys']='$renewal_period_input_type';";
		if($price_list_arr[$i][14]==8){
			$script_var .= "\n custom_skews['$c']='$keys';";
			$c++;
		}
	}
	if($product_property=='saas'){
		$saas_query = 	"select pm.GPM_PRODUCT_CODE, pm.GPM_PRODUCT_SKEW, pm.GFT_SKEW_PROPERTY, pm.GPM_ORDER_TYPE,".
						" pm.GPM_REFERER_SKEW, pm.GPM_SKEW_DESC, pm1.GPM_PRODUCT_CODE ref_pcode, pm1.GPM_PRODUCT_SKEW ref_skew,".
						" pm1.GFT_SKEW_PROPERTY ref_property, pm1.GPM_ORDER_TYPE ref_order_type, pm.GPM_NET_RATE from gft_product_master pm ".
						" INNER JOIN gft_saas_product_reference_mapping on(GSP_REF_SKEW_NAME=CONCAT(pm.GPM_PRODUCT_CODE,'-',pm.GPM_PRODUCT_SKEW)) ".
						" INNER JOIN gft_product_master pm1 ON(GSP_SKEW_NAME=CONCAT(pm1.GPM_PRODUCT_CODE,'-',pm1.GPM_PRODUCT_SKEW))".
						" where (1)";
		$result_register = execute_my_query($saas_query." AND pm.GFT_SKEW_PROPERTY=20");
		while($row_register=mysqli_fetch_array($result_register)){
			$reg_key = $row_register['ref_pcode']."-".$row_register['ref_skew']."-".$row_register['ref_property']."-".$row_register['ref_order_type'];
			$reg_skew= $row_register['GPM_PRODUCT_CODE']."-".$row_register['GPM_PRODUCT_SKEW']."-".$row_register['GFT_SKEW_PROPERTY']."-".$row_register['GPM_ORDER_TYPE'];
			$net_rate = (int)$row_register['GPM_NET_RATE'];
			$script_var .= "\n saas_register_reference['$reg_key']='$reg_skew';";
			$script_var .= "\n saas_register_reference_amt['$reg_skew']='$net_rate';";
		}
		$result_user = execute_my_query($saas_query." AND pm.GFT_SKEW_PROPERTY=21");
		while($row_user=mysqli_fetch_array($result_user)){
			$reg_key = $row_user['ref_pcode']."-".$row_user['ref_skew']."-".$row_user['ref_property']."-".$row_user['ref_order_type'];
			$reg_skew= $row_user['GPM_PRODUCT_CODE']."-".$row_user['GPM_PRODUCT_SKEW']."-".$row_user['GFT_SKEW_PROPERTY']."-".$row_user['GPM_ORDER_TYPE'];
			$net_rate = (int)$row_user['GPM_NET_RATE'];
			$script_var .= "\n saas_user_reference['$reg_key']='$reg_skew';";
			$script_var .= "\n saas_user_reference_amt['$reg_skew']='$net_rate';";
		}
	}
	$script_var .= "</script>";
	return $script_var;
}

/**
 * @param string $lead_code
 * @param string $formtype
 * @param boolean $show_subscription_skew
 * @param boolean $kit_based
 * @param string[int] $prod_code_arr
 * @param boolean $from_quotation
 * @param string $disable_attr
 * @param string $disable_class
 * @param string $readonly_attr
 * @param boolean $for_order
 * @param boolean $is_dealer_enabled
 * @param string[int] $not_in_pcode
 * @param boolean $only_active
 * 
 * @return string
 */
function get_server_order_ui($lead_code,$formtype,$show_subscription_skew,$kit_based,$prod_code_arr=null,$from_quotation=false,$disable_attr='',$disable_class='',$readonly_attr='',$for_order=false,$is_dealer_enabled=false,$not_in_pcode=array('70'),$only_active = true){
	$not_allowed_arr = get_not_allowed_product_for_vertical($lead_code);
	$ver_map_query=" select concat(GPPM_PRODUCT_ABR,'-',gpg_version) as product_group_name,gbr_product,GTM_VERTICAL_NAME,GTM_VERTICAL_CODE,GTM_KIT_ENABLED  from gft_lead_hdr join gft_vertical_master on ( GLH_VERTICAL_CODE=GTM_VERTICAL_CODE)  " .
			" join gft_bvp_relation on (if(GTM_IS_MACRO='Y',GTM_VERTICAL_CODE,GTM_MICRO_OF)=gbr_vertical) join  gft_product_group_master on " .
			" (gpg_product_family_code=substring_index(gbr_product,'-',1) and gpg_skew=substring_index(gbr_product,'-',-1)) join gft_product_primary_master on (GPPM_PRODUCT_CODE=gpg_product_family_code) where glh_lead_code = $lead_code  ";
	$res=execute_my_query($ver_map_query);
	$gbr_product = $vertical_name = $product_group_name = $vertical_id=$gcm_kit_enabled= "";
	if($data=mysqli_fetch_array($res)){
		$gbr_product=$data['gbr_product'];
		$vertical_name=$data['GTM_VERTICAL_NAME'];
		$product_group_name=$data['product_group_name'];
		$vertical_id= $data['GTM_VERTICAL_CODE'];
		$vertical_id= $data['GTM_VERTICAL_CODE'];
		$gcm_kit_enabled = $data['GTM_KIT_ENABLED'];
	}
	if(is_array($prod_code_arr) and count($prod_code_arr)>0) {
		if(count(array_intersect($prod_code_arr,$not_in_pcode))>0) {
			$not_in_pcode = null;
		}
	}
	$pr_list	= product_code_skew_list(0,null,'',$prod_code_arr,$formtype,$lead_code,true,null,false,'',$only_active,false,$not_in_pcode,$show_subscription_skew,$kit_based);
	$pgroupc	= "";
	$cusgroup	= "";
	$option_list = "";
	for($i=0;$i<count($pr_list);$i++){
		$pr_value=$pr_list[$i][0];
		if( in_array(substr($pr_value, 0,8),$not_allowed_arr) ){
			continue; //not applicable based on the vertical
		}
		if($pr_list[$i][14]=='8'){
			$custom_title	=	" Custom License";
			if($cusgroup!=$pr_list[$i][5].'-'.$pr_list[$i][14]){
				if($cusgroup!='') {
					$option_list .= "</optgroup>";
				}
				$option_list .= "<optgroup label='".$pr_list[$i][5].$custom_title."'>";
				$cusgroup=$pr_list[$i][5].'-'.$pr_list[$i][14];
			}
		}else{
			if($pgroupc!=$pr_list[$i][5]){
				if($pgroupc!='') {
					$option_list .= "</optgroup>";
				}
				$option_list .= "<optgroup label=\"{$pr_list[$i][5]}\">";
				$pgroupc=$pr_list[$i][5];
			}
		}
		$option_list .= "<option pdoption=\"{$pr_list[$i][13]}\" value=\"$pr_value\">{$pr_list[$i][6]} </option>";
	}
	$option_list .= "</optgroup>";
	$previous_installed = get_existing_installation_detail($lead_code);
	$column_row = column_header_row("Server Order");
	$skip_this = $from_quotation;
	if($from_quotation || $is_dealer_enabled) {
		$skip_this = true;
	}
	$add_remove_row = get_add_remove_row("addserverRow","removeserverRow",$skip_this);
	$common_columns = column_for_tax_and_discount("",$formtype,$readonly_attr,$for_order);
	$ret_txt = <<<END
	<div id="divorder_catagory0" class="hide">
	<table id="table0" border="1" cellpadding="2" cellspacing="1" width="900px">
		<thead>$column_row</thead>
		<tbody>
			<tr><td valign='top'><label for="sno1" id="lablesno1">1</label><input type="checkbox" name="sno[1]" id="sno1" value=1></td>
				<TD valign='top'><select size="1" name="product_code[1]" id='product_code1' $disable_attr class="formStyleTextarea pro_list $disable_class" onchange="return updateproduct_prize(this,0);" style="width:350px">
						<option value="0"  >Select</option>$option_list</select><input type="hidden" name="additional_custom[1]" value="0" id="additional_custom1">
						<input type="hidden" name="additional_custom_order[1]" value="" id="additional_custom_order1">
						<INPUT type="hidden" id="product_group" name="product_group" value="$gbr_product">
						<INPUT type="hidden" id="vertical_name" name="vertical_name" value="$vertical_name">
						<INPUT type="hidden" id="vertical_id" name="vertical_id" value="$vertical_id">
						<INPUT type="hidden" id="gcm_kit_enabled" name="gcm_kit_enabled" value="$gcm_kit_enabled">
						<INPUT type="hidden" id="product_group_name" name="product_group_name" value="$product_group_name">
						<input type="hidden" id="prev_install" name="prev_install"  value="$previous_installed">
						<div id="server_product_list1"></div>
				</td>
				$common_columns
			</tr>
		</tbody>
		<tfoot>$add_remove_row</tfoot>
	</table>
	</div>
END;
	$inactive_custom_license_list = product_code_skew_list('0',null,'',null,$formtype,$lead_code,true,null,false,'custom');
	$arr_list = array_merge($pr_list,$inactive_custom_license_list);
	$ret_txt .= return_quote_script_varibles_from_array($arr_list);
	return $ret_txt;
}

/**
 * @param string $lead_code
 * @param string $formtype
 * @param boolean $show_subscription_skew
 * @param boolean $only_active_skew
 * @param boolean $from_quotation
 * @param string $disable_attr
 * @param string $disable_class
 * @param string $readonly_attr
 * @param boolean $for_order
 * @return string
 */
function get_upgradation_order_ui($lead_code,$formtype,$show_subscription_skew,$only_active_skew=true,$from_quotation=false,$disable_attr='',$disable_class='',$readonly_attr='',$for_order=false){
	$sel_list = "<option value='0'>Select</option>";
	$upgradeproduct_list=product_code_skew_list(1,null,null,null,$formtype,(int)$lead_code,true,null,false,'',$only_active_skew,false,null,$show_subscription_skew);
	$pgroupc="";
	for($i=0;$i<count($upgradeproduct_list);$i++){
		$pr_value=$upgradeproduct_list[$i][0];
		if($pgroupc!=$upgradeproduct_list[$i][5]){
			$pgroupc=$upgradeproduct_list[$i][5];
			if($pgroupc=='') {
				$sel_list .= "</optgroup>";
			}
			$sel_list .= "<optgroup label=\"{$upgradeproduct_list[$i][5]}\">";
		}
		$sel_list .="<option pdoption=\"{$upgradeproduct_list[$i][13]}\" value=\"$pr_value\">{$upgradeproduct_list[$i][6]}</option>";
	}
	$sel_list .= "</optgroup>";
	$column_row = column_header_row("Upgradation order");
	$add_remove_row = get_add_remove_row("addupgradationRow","removeupgradationRow",$from_quotation);
	$common_columns = column_for_tax_and_discount("up",$formtype,$readonly_attr,$for_order);
$ret_txt = <<<END
	<div id="divorder_catagory1"  class="hide">
	<table id="table1" border="1" cellpadding="2" cellspacing="1" width="900px">
		<thead>$column_row</thead>
		<tbody>
			<tr><td><label for="upsno1" id="uplablesno1">1</label><input type="checkbox" name="upsno[1]" id="upsno1" value=1></td>
				<TD valign='top'><select size="1" name="upproduct_code[1]" id='upproduct_code1' class="formStyleTextarea upproduct_code $disable_class"	$disable_attr onchange="return updateproduct_prize(this,1);" style="width:350px">
					$sel_list</select></td>
				$common_columns
			</tr>
		</tbody>
		<tfoot>$add_remove_row</tfoot>
	</table>
	</div>
END;
	$ret_txt .= return_quote_script_varibles_from_array($upgradeproduct_list);
	$ret_txt .= "<script>";
	$upgratationquery="select GFT_LOWER_PCODE, GFT_LOWER_SKEW, GFT_HIGHER_PCODE, GFT_HIGHER_SKEW, GPM_PRODUCT_CODE, GPM_PRODUCT_SKEW,GFT_SKEW_PROPERTY,GSPM_DISCOUNT_PERCENTAGE  ".
			" from gft_product_master join gft_skew_property_master on (GSPM_CODE=gft_skew_property) where GFT_SKEW_PROPERTY in (2,24) AND GPM_STATUS='A' ";
	$result_upgr=execute_my_query($upgratationquery);
	while($dataupgr=mysqli_fetch_array($result_upgr)){
		$ret_txt .= "\n upgra_lower_skew['{$dataupgr['GPM_PRODUCT_CODE']}-{$dataupgr['GPM_PRODUCT_SKEW']}-{$dataupgr['GFT_SKEW_PROPERTY']}-{$dataupgr['GSPM_DISCOUNT_PERCENTAGE']}']='{$dataupgr['GFT_LOWER_PCODE']}-{$dataupgr['GFT_LOWER_SKEW']}';";
		$ret_txt .= "\n upgra_higher_skew['{$dataupgr['GPM_PRODUCT_CODE']}-{$dataupgr['GPM_PRODUCT_SKEW']}-{$dataupgr['GFT_SKEW_PROPERTY']}-{$dataupgr['GSPM_DISCOUNT_PERCENTAGE']}']='{$dataupgr['GFT_HIGHER_PCODE']}-{$dataupgr['GFT_HIGHER_SKEW']}';";
	}
	$ret_txt .= "</script>";
	return $ret_txt;
}

/**
 * @param string $lead_code
 * @param string $formtype
 * @param string[int] $prod_code_arr
 * @param boolean $from_quotation
 * @param string $disable_attr
 * @param string $disable_class
 * @param string $readonly_attr
 * @param boolean $for_order
 * @param boolean $only_active
 * @param boolean $kit_based
 *
 * @return string
 */
function get_asa_order_ui($lead_code,$formtype,$prod_code_arr=null,$from_quotation=false,$disable_attr='',$disable_class='',$readonly_attr='',$for_order=false,$only_active=true,$kit_based=false){
	$sel_list = "<option value='0'>Select</option>";
	$pgroupc="";
	$asspr_list=product_code_skew_list(2,null,null,$prod_code_arr,null,(int)$lead_code,true,null,true,'',$only_active,false,null,true,$kit_based);
	for($i=0;$i<count($asspr_list);$i++){
		$asspr_value=$asspr_list[$i][0];
		if($pgroupc!=$asspr_list[$i][5]){
			$pgroupc=$asspr_list[$i][5];
			if($pgroupc=='') { 
				$sel_list .= "</optgroup>";
			}
			$sel_list .= "<optgroup label=\"{$asspr_list[$i][5]}\">";
		}
		$sel_list .= "<option pdoption=\"{$asspr_list[$i][13]}\" value=\"$asspr_value\">{$asspr_list[$i][6]}</option>";
	}
	$sel_list .= "</optgroup>";
	$add_remove_row = get_add_remove_row("addassRow","removeassRow",$from_quotation);
	$column_row = column_header_row("ALR Order");
	$common_columns = column_for_tax_and_discount("ass",$formtype,$readonly_attr,$for_order);
	$ret_txt = <<<END
	<div id="divorder_catagory2"  class="hide">
	<table id="table2" border="1" cellpadding="2" cellspacing="1" width="900px">
		<thead>	$column_row</thead>
		<tbody>
			<tr><td><label for="asssno1" id="asslablesno1">1</label><input type="checkbox" name="asssno[1]" id="asssno1" value=1></td>
				<TD valign='top'><select size="1" name="assproduct_code[1]" id='assproduct_code1' class="formStyleTextarea assproduct_code $disable_class" $disable_attr onchange="return updateproduct_prize(this,2);" style="width:350px">
						$sel_list</select><div id='client_alr_prorate_info1'></div></td>
				$common_columns
			</tr>
		</tbody>
		<tfoot>$add_remove_row</tfoot>
	</table>
	</div>
END;
	$ret_txt .= return_quote_script_varibles_from_array($asspr_list);
	$asaquery=" select p1.GPM_REFERER_SKEW, p1.GPM_PRODUCT_CODE, p1.GPM_PRODUCT_SKEW, p1.gft_skew_property, GSPM_DISCOUNT_PERCENTAGE, r1.GPM_ORDER_TYPE, ".
			" p1.GPM_CLIENT_ALR_CODE, p1.GPM_CLIENT_ALR_SKEW,p1.GPM_CLIENTS,p1.GPM_PRODUCT_TYPE,if(pro.code is null,0,1) prorate_sku from gft_product_master p1 ".
			" left join (select GPM_PRODUCT_CODE code, GPM_PRORATE_SKEW skew from gft_product_master where GPM_PRORATE_SKEW!='') pro on (pro.code=p1.GPM_PRODUCT_CODE and pro.skew=p1.GPM_PRODUCT_SKEW) ".
			" join gft_product_master r1 on (p1.GPM_PRODUCT_CODE=r1.GPM_PRODUCT_CODE and r1.GPM_PRODUCT_SKEW=ifnull(pro.skew,p1.GPM_REFERER_SKEW)) ".
			" join gft_skew_property_master on (GSPM_CODE=p1.gft_skew_property) ".
			" where p1.gft_skew_property in (4,15) AND p1.gpm_status='A' ";
	$result_asa=execute_my_query($asaquery);
	$ret_txt .="<script> var client_alr_skew = new Array();".
	           "var other_alr_info = new Array();";
	while($asaupgr=mysqli_fetch_array($result_asa)){
		$ps = $asaupgr['GPM_PRODUCT_CODE']."-".$asaupgr['GPM_REFERER_SKEW'];
		$key = $asaupgr['GPM_PRODUCT_CODE']."-".$asaupgr['GPM_PRODUCT_SKEW']."-".$asaupgr['gft_skew_property']."-".$asaupgr['GSPM_DISCOUNT_PERCENTAGE'];
		$ret_txt .= "\n referer_skew['$key']='{$asaupgr['GPM_PRODUCT_CODE']}-{$asaupgr['GPM_REFERER_SKEW']}';";
		$ret_txt .= "\n other_alr_info['$key'] = '{$asaupgr['GPM_PRODUCT_TYPE']}-{$asaupgr['GPM_CLIENTS']}-{$asaupgr['prorate_sku']}'";
		$ret_txt .= "\n order_type['$ps']='{$asaupgr['GPM_ORDER_TYPE']}';";
	}
	$cl_alr_que= " select pm.GPM_PRODUCT_CODE,pm.GPM_PRODUCT_SKEW,ser_pm.gpm_product_skew server_skew,pm.gpm_referer_skew from gft_product_master pm ".
	             " join gft_product_master cl_pm on (cl_pm.gpm_product_code=pm.gpm_product_code and cl_pm.gpm_product_skew=pm.gpm_referer_skew) ".
	             " join gft_product_master ser_pm on (ser_pm.GPM_CLIENT_ALR_CODE=cl_pm.gpm_product_code and ser_pm.GPM_CLIENT_ALR_SKEW=cl_pm.gpm_product_skew) ".
				 " where pm.gft_skew_property in (4) and pm.gpm_referer_skew is not null and pm.gpm_referer_skew!='' and ".
	             " cl_pm.gft_skew_property in (3) ";
	$cl_alr_res = execute_my_query($cl_alr_que);
	while($cl_alr_data = mysqli_fetch_array($cl_alr_res)){
		$ps = $cl_alr_data['GPM_PRODUCT_CODE']."-".$cl_alr_data['server_skew'];
		$cl_ar = $cl_alr_data['GPM_PRODUCT_CODE']."-".$cl_alr_data['GPM_PRODUCT_SKEW'];
		$ret_txt .= "\n client_alr_skew['$ps']='$cl_ar';";
	}
	$ret_txt .="</script>";
	return $ret_txt;
}

/**
 * @param string $lead_code
 * @param string $formtype
 * @param boolean $show_subscription_skew
 * @param boolean $kit_based
 * @param string[int] $prod_code_arr
 * @param boolean $show_trial
 * @param boolean $only_active
 * @param boolean $from_quotation
 * @param string $disable_attr
 * @param string $disable_class
 * @param string $readonly_attr
 * @param boolean $for_order
 * @param boolean $is_dealer_enabled
 *
 * @return string
 */
function get_service_order_ui($lead_code,$formtype,$show_subscription_skew,$kit_based,$prod_code_arr=null,$show_trial=false,$only_active=true,$from_quotation=false,$disable_attr='',$disable_class='',$readonly_attr='',$for_order=false,$is_dealer_enabled=false){
	$sel_list = "<option value='0' >Select</option>";
	$servicepr_list=product_code_skew_list(12,null,null,$prod_code_arr,$formtype,$lead_code,true,null,$show_trial,'',$only_active,false,null,true,$kit_based,true);
	$pgroupc="";
	for($i=0;$i<count($servicepr_list);$i++){
		$s="";
		$cmbproduct_id=$servicepr_list[$i][0];
		$productl=$servicepr_list[$i][1];
		if($pgroupc!=$servicepr_list[$i][5]){
			$pgroupc=$servicepr_list[$i][5];
			if($pgroupc=='') {
				$sel_list .= "</optgroup>";
			}
			$sel_list .= "<optgroup label=\"{$servicepr_list[$i][5]}\">";
		}
		$sel_list .= "<option pdoption=\"{$servicepr_list[$i][13]}\" value=\"$cmbproduct_id\" $s>{$servicepr_list[$i][6]} </option>";
	}
	$sel_list .= "</optgroup>";
	$skip_this = $from_quotation;
	if($is_dealer_enabled) {
		$skip_this = false;
	}
	$add_remove_row = get_add_remove_row("addserviceRow","removeserviceRow",$skip_this);
	$column_row = column_header_row("Service Incident Order");
	$common_columns = column_for_tax_and_discount("service",$formtype,$readonly_attr,$for_order);
	$ret_txt = <<<END
	<div id="divorder_catagory12"  class="hide">
	<table border="1" id="table12" width="900px">
		<thead>$column_row</thead>
		<tbody>
			<tr><td valign='top'><label for="servicesno1" id="servicelablesno1">1</label><input type=checkbox name="servicesno[1]" id="servicesno1" value=1></td>
				<td valign='top'><SELECT name="serviceproduct_code[1]" id="serviceproduct_code1" class="formStyleTextarea service_order $disable_class" $disable_attr onchange="updateproduct_prize(this,12);" style="width:350px">
								$sel_list</SELECT><div id="service_product_list1"></div></TD>
				$common_columns
			</tr>
		</tbody>
		<tfoot>$add_remove_row</tfoot>
	</table>
	</div>
END;
	$ret_txt .= return_quote_script_varibles_from_array($servicepr_list);
	return $ret_txt;
}

/**
 * @param string $lead_code
 * @param string $formtype
 * @param string[int] $prod_code_arr
 * @param boolean $from_quotation
 * @param string $disable_attr
 * @param string $disable_class
 * @param string $readonly_attr
 * @param boolean $for_order
 * @param boolean $only_active_skew
 *
 * @return string
 */
function get_support_order_ui($lead_code,$formtype,$prod_code_arr=null,$from_quotation=false,$disable_attr='',$disable_class='',$readonly_attr='',$for_order=false,$only_active_skew=true){
	$sel_list = "<option value='0' >Select</option>";
	$pgroupc="";
	$supportproduct_list=product_code_skew_list(5,null,null,$prod_code_arr,$formtype,$lead_code,true,null,false,'',$only_active_skew);
	for($i=0;$i<count($supportproduct_list);$i++){
		$cmbproduct_id=$supportproduct_list[$i][0];
		if($pgroupc!=$supportproduct_list[$i][5]){
			$pgroupc=$supportproduct_list[$i][5];
			if($pgroupc=='') {
				$sel_list .= "</optgroup>";
			}
			$sel_list .= "<optgroup label=\"".$supportproduct_list[$i][5]."\">";
		}
		$sel_list .= "<option pdoption=\"{$supportproduct_list[$i][13]}\" value=\"$cmbproduct_id\">".$supportproduct_list[$i][6]."</option>";
	}
	$sel_list .= "</optgroup>";
	$column_row = column_header_row("Support Incident Charges");
	$add_remove_row = get_add_remove_row("addsupportRow", "removesupportRow",$from_quotation);
	$common_columns = column_for_tax_and_discount("support",$formtype,$readonly_attr,$for_order);
	$ret_txt = <<<END
	<div id="divorder_catagory5"  class="hide">
	<table border="1"  id="table5" width="900px">
		<thead>$column_row</thead>
		<tbody>
			<tr><td><label for="supportsno1" id="supportlablesno1">1</label><input type="checkbox" name="supportsno[1]" id="supportsno1" value=1></td>
				<td valign='top'><SELECT name="supportproduct_code[1]" id="supportproduct_code1" class="formStyleTextarea support_order $disable_class" $disable_attr onchange="updateproduct_prize(this,5);" style="width:350px">
					$sel_list</SELECT></TD>
				$common_columns
			</tr>
		</tbody>
		<tfoot>$add_remove_row</tfoot>
	</table>
	</div>
END;
	$ret_txt .= return_quote_script_varibles_from_array($supportproduct_list);
	return $ret_txt;
}

/**
 * @param string $lead_code
 * @param string $formtype
 * @param boolean $only_active
 * @param boolean $from_quotation
 * @param string $disable_attr
 * @param string $disable_class
 * @param string $readonly_attr
 * @param boolean $for_order
 *
 * @return string
 */
function get_subscription_renewal_order_ui($lead_code,$formtype,$only_active=true,$from_quotation=false,$disable_attr='',$disable_class='',$readonly_attr='',$for_order=false){
	$sel_list = "<option value='0'>Select</option>";
	$pgroupc="";
	$subscriptionpr_list=product_code_skew_list(13,null,null,null,$formtype,(int)$lead_code,true,array(2,3),false,'',$only_active);
	for($i=0;$i<count($subscriptionpr_list);$i++){
		$pr_value=$subscriptionpr_list[$i][0];
		if($pgroupc!=$subscriptionpr_list[$i][5]){
			$pgroupc=$subscriptionpr_list[$i][5];
			if($pgroupc=='') {
				$sel_list .= "</optgroup>";
			}
			$sel_list .= "<optgroup label=\"{$subscriptionpr_list[$i][5]}\">";
		}
		$sel_list .= "<option pdoption=\"{$subscriptionpr_list[$i][13]}\" value=\"$pr_value\">{$subscriptionpr_list[$i][6]}</option>";
	}
	$sel_list .= "</optgroup>";
	$column_row = column_header_row("Subscription renewal Order");
	$add_remove_row = get_add_remove_row("addsubscripRow", "removesubscripRow",$from_quotation);
	$common_columns = column_for_tax_and_discount("subscrip",$formtype,$readonly_attr,$for_order);
	$ret_txt=<<<END
	<div id="divorder_catagory13"  class="hide">
	<table id="table13" border="1" cellpadding="2" cellspacing="1" width="900px">
		<thead>$column_row</thead>
		<tbody>
			<tr><td><label for="subscripsno1" id="subscriplablesno1">1</label><input type="checkbox" name="subscripsno[1]" id="subscripsno1" value="1"></td>
				<TD valign='top'><select size="1" name="subscripproduct_code[1]" id="subscripproduct_code1" class="formStyleTextarea lease_order $disable_class" $disable_attr onchange="return updateproduct_prize(this,13);" style="width:350px">
						$sel_list</select><div id="subscription_product_list1"></div></td>
				$common_columns
			</tr>
		</tbody>
		<tfoot>$add_remove_row</tfoot>
	</table>
	</div>	
END;
	$ret_txt .= return_quote_script_varibles_from_array($subscriptionpr_list);
	$subscripquery =" select GPM_REFERER_SKEW, GPM_PRODUCT_CODE, GPM_PRODUCT_SKEW,gft_skew_property,GSPM_DISCOUNT_PERCENTAGE ".
					" from gft_product_master join gft_skew_property_master on (GSPM_CODE=gft_skew_property) ".
					" where gft_skew_property in (11,18,26) AND gpm_status='A' ";
	$result_subscrip=execute_my_query($subscripquery);
	$ret_txt .= "<script>";
	while($datasubscrip=mysqli_fetch_array($result_subscrip)){
		$ret_txt .= "\n referer_skew['{$datasubscrip['GPM_PRODUCT_CODE']}-{$datasubscrip['GPM_PRODUCT_SKEW']}-{$datasubscrip['gft_skew_property']}-{$datasubscrip['GSPM_DISCOUNT_PERCENTAGE']}']='{$datasubscrip['GPM_PRODUCT_CODE']}-{$datasubscrip['GPM_REFERER_SKEW']}';";
	}
	$ret_txt .= "</script>";
	return $ret_txt;
}

/**
 * @param string $lead_code
 *
 * @return string
 */
function get_installed_for_root_order_selection($lead_code){
	$hidden_server='';
	$inc=0;
	//installed products
	$instal_que=" select glh_lead_code,GID_ORDER_NO, GID_FULLFILLMENT_NO, GID_LIC_PCODE, GID_LIC_PSKEW, GFT_SKEW_PROPERTY, GPM_PRODUCT_ABR, GPM_SKEW_DESC ".
			" from gft_install_dtl_new ".
			" join gft_lead_hdr on (glh_lead_code=gid_lead_code) ".
			" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GID_LIC_PCODE and pm.GPM_PRODUCT_SKEW=GID_LIC_PSKEW and GPM_PRODUCT_TYPE!=8 and if(GFT_SKEW_PROPERTY not in (1,11),GPM_LICENSE_TYPE!=3,1)) ".
			" join gft_product_family_master pfm on(pfm.gpm_product_code=GID_LIC_PCODE) ".
			" where GID_LEAD_CODE='$lead_code' ";
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
	return $hidden_server;
}

/**
 * @param string $lead_code
 * @param string $lead_type
 * 
 * @return string
 */
function get_feature_installation_details($lead_code,$lead_type){
    $detail_ui = "";
    if($lead_type=='3'){
        $wh_cond = " and (GLH_LEAD_CODE='$lead_code' or (glh_reference_given='$lead_code' and GLH_LEAD_SOURCECODE in (7,36) and GLH_LEAD_TYPE in (3,13))) ";
    }else{
        $wh_cond = " and GLH_LEAD_CODE='$lead_code' ";
    }
    $que1 =" select GLH_LEAD_CODE,GLH_CUST_NAME,GLH_CUST_STREETADDR2,GPM_SKEW_DESC,GAI_NO_OF_DEVICE,GAI_PRODUCT_CODE,GAI_PRODUCT_SKEW, ".
           " date(GAI_VALIDITY_DATETIME) expiry_dt from gft_addon_feature_install_dtl ".
           " join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GAI_PRODUCT_CODE and pm.GPM_PRODUCT_SKEW=GAI_PRODUCT_SKEW) ".
           " join gft_lead_hdr on (GLH_LEAD_CODE=GAI_LEAD_CODE) ".
           " where GAI_STATUS='A' and GPM_LICENSE_TYPE!=3 $wh_cond order by GLH_LEAD_CODE ";
    $res1 = execute_my_query($que1);
    if(mysqli_num_rows($res1) > 0){
        $detail_ui = <<<END
        <div style='width:900px;margin-bottom:10px;'><fieldset class='group-div'>
            <legend><b>ADDON FEATURE INSTALLATION DETAILS</b></legend>
            <table id="addon_feature_install_table" border='1' width='900px'>
            <thead><tr class="modulelisttitle">
                 <td class="head_black_11">S.No</td>
                 <td class="head_black_11">Customer Id</td>
                 <td class="head_black_11">Customer Name</td>
                 <td class="head_black_11">Product</td>
                 <td class="head_black_11">Qty</td>
                 <td class="head_black_11">Expiry Date</td>
            </tr></thead>
            <tbody>
END;
        $sl_no = 1;
        while ($row1 = mysqli_fetch_array($res1)){
            $ref = $row1['GAI_PRODUCT_CODE']."-".$row1['GAI_PRODUCT_SKEW'];
            $detail_ui .= "<tr class='addon_feature_row'><td>$sl_no <input type='checkbox' id='feature_ins$sl_no' name='feature_ins[$sl_no]' class='feature_table_row' ref='$ref'></td>".
                            "<td>".$row1['GLH_LEAD_CODE']."</td>".
                            "<td>".$row1['GLH_CUST_NAME']." - ".$row1['GLH_CUST_STREETADDR2']."</td>".
                            "<td>".$row1['GPM_SKEW_DESC']."</td>".
                            "<td style='text-align: center;'>".$row1['GAI_NO_OF_DEVICE']."</td>".
                            "<td style='text-align: center;'>".$row1['expiry_dt']."</td>".
                          "</tr>";
            $sl_no++;
        }
        $detail_ui .= "<tbody></table></fieldset></div>";
    }
    return $detail_ui;
    
}

/**
 * @param string $lead_code
 * @param string $lead_type
 * @param string $additional_wh_cond
 * @param boolean $kit_based
 * @param string $proforma_no
 * @param boolean $surrender_disabled
 *
 * @return string
 */
function get_installation_for_selection($lead_code,$lead_type,$additional_wh_cond='',$kit_based=false,$proforma_no='',$surrender_disabled=false){
	global $customer_installation;
	$install_dtl = get_install_detail_query($lead_code,(int)$lead_type,$additional_wh_cond,$kit_based);
	$resultinstalldtl=execute_my_query($install_dtl);
	$is_installed = "false";
	list($alr_skew,$noc_qty,$cl_skew,$purchased_cl) = get_additional_alr_info_for_kit_customer($lead_code);
	$clients_select = "";
	$surrender_clients = 0;
	if($lead_type==1){
	    $clients_dtl = get_surrender_clients_dtl($lead_code);
	    if(count($clients_dtl)>0){
	        $gph_surrendered_clients = 0;
	        if($proforma_no!=''){
	            $gph_surrendered_clients = get_single_value_from_single_query("GPH_SURRENDERED_CLIENTS", "select GPH_SURRENDERED_CLIENTS from gft_proforma_hdr where GPH_ORDER_NO='$proforma_no' and GPH_LEAD_CODE='$lead_code' ");
	        }
	        $clients = $clients_dtl['client'];
	        $surrender_clients = $clients_dtl['surrender_client'];
	        if($surrender_clients>0){
	            $disabled = ($surrender_disabled) ? "disabled=true" : "";
    	        $clients_select = "<span id='surrender_clients_span'>Renewal from surrendered clients  <select id='surrender_clients' name='surrender_clients' $disabled><option value=0>Select</option>";
    	        for($i=1;$i<=$surrender_clients;$i++){
    	            $selected = ($gph_surrendered_clients==$i) ? "selected=selected" : "";
    	            $clients_select .= "<option value=$i $selected>$i</option>";
    	        }
    	        $clients_select .= "</select> &nbsp; &nbsp;&nbsp;";
    	        $clients_select .= "<input type='hidden' id='clients_cnt' name='clients_cnt' value=$clients> </span>";
	        }
	    }
	}
	$installation_detail=<<<END
<script>var expire_for_arr = new Array();</script>
	<tr><td colspan="4">
<div id="Installed_dtl" style='width:900px'>
 <fieldset class='group-div'>
    <legend><b>INSTALLATION DETAILS</b></legend>
<input type='hidden' value='$noc_qty' id='purchased_noc' name='purchased_noc' alr_skew='$alr_skew'>
<input type='hidden' value='$purchased_cl' id='purchased_cl' name='purchased_cl' cl_skew='$cl_skew'>
<div style="text-align: center;margin-bottom: 10px;font-size: 12px;">
    <input type="hidden" id="old_surrendered_client" name="old_surrendered_client" value=$surrender_clients >
    $clients_select
	Expiry From Date <input id="expiry_from" name="expiry_from" class="formStyleTextarea" value="" readonly="true" size="10" type="text" ondblclick="javascript:this.value='';"> 
	Expiry To Date <input id="expiry_to" name="expiry_to" class="formStyleTextarea" value="" readonly="true" size="10" type="text" ondblclick="javascript:this.value='';">
	<input id='show_btn' name='show_btn' value='Show' class='button' type='button' size='5' onclick="apply_filters();">
	<script>init_date_func("expiry_from","%Y-%m-%d","expiry_from","Bl");init_date_func("expiry_to","%Y-%m-%d","expiry_to","Bl");</script>
</div>
<TABLE id="tableInstalled_dtl" border="1" cellPadding="2" cellSpacing="2" width="900px">
END;
	if(mysqli_num_rows($resultinstalldtl)>0){
		$customer_installation = true;
		$is_installed = "true";
		$installation_detail.=<<<END
		<thead>
			<tr class="modulelisttitle">
				<td class="head_black_11">S.No <input type='hidden' name='ref_install_id' id='ref_install_id' value=''>
				<input type="checkbox" id="splitupdate0" name="splitupdate1[0]" value="1" class='install_table' checked></td>
				<td class="head_black_11">Customer Id</td>
				<td class="head_black_11">Customer Name</td>
				<td class="head_black_11">Order no</td>
				<td class="head_black_11">Fullfillment No</td>
				<td class="head_black_11">Product</td>
				<td class="head_black_11">No of Clients</td>
				<td class="head_black_11">No of Companies</td>
				<td class="head_black_11">License Type</td>
				<td class="head_black_11">ASA /Subscription Expiry Date</td>
			</tr>
		</thead>
		<tbody>
END;
		$ins_i=0;
		$splict_cust=/*. (string[int]) .*/ array();
		while($data_installdtl=mysqli_fetch_array($resultinstalldtl)){
			$ins_i++;
			$clead_code=$data_installdtl['GLH_LEAD_CODE'];
			$c_ins_name=$data_installdtl['cust_name'];
			$ins_product_name=$data_installdtl['GPM_PRODUCT_ABR'].' '.$data_installdtl['GPM_SKEW_DESC'];
			$ins_order_no=$data_installdtl['GID_ORDER_NO'];
			$ins_product_code=$data_installdtl['GID_LIC_PCODE'];
			$ins_product_skew=$data_installdtl['GID_LIC_PSKEW'];
			$ins_root_order_no=$data_installdtl['GID_ORDER_NO'];
			$ins_fullfillno=$data_installdtl['GID_LIC_FULLFILLMENT_NO'];
			$ins_root_fullfillno=$data_installdtl['GID_FULLFILLMENT_NO'];
			$ins_head_family=$data_installdtl['GID_HEAD_OF_FAMILY'];
			$ins_ass_expirydate=$data_installdtl['GID_VALIDITY_DATE'];
			$no_of_clients= $data_installdtl['GID_NO_CLIENTS'];
			$no_of_company= $data_installdtl['GID_NO_COMPANYS'];
			$skew_property=$data_installdtl['GFT_SKEW_PROPERTY'];
			$skew_discount=$data_installdtl['GSPM_DISCOUNT_PERCENTAGE'];
			$referer_skew= $data_installdtl['REFERER_SKEW'];
			$license_type=$data_installdtl['license_type'].'-'.$data_installdtl['GET_TYPE_NAME'];
			$edition_type=$data_installdtl['GPM_PRODUCT_TYPE'];
			$install_reff_id=$data_installdtl['GID_INSTALL_ID'];
			$gpm_lic_type = $data_installdtl['GPM_LICENSE_TYPE'];
			$gid_expire_for = $data_installdtl['GID_EXPIRE_FOR'];
			$split_done=0;
			foreach($splict_cust as $key => $value){
				if($key==$ins_product_code.'-'.$ins_product_code and $splict_cust[$key]==$clead_code){
					$split_done=1;
				}
			}
			$prorata_alt_dtl = $prorate_cl_dtl = array();
			if($gpm_lic_type!=3){
			    $prorata_alt_dtl = get_alr_prorata_details($lead_code, $ins_product_code,$ins_product_skew, $ins_ass_expirydate ,$data_installdtl['GID_EXPIRE_FOR'],$lead_type, $ins_i);
			    $prorate_cl_dtl  = get_alr_prorate_dtl_for_custom_license($lead_code, $ins_product_code,$ins_product_skew, $ins_ass_expirydate,$lead_type,$ins_order_no,$ins_fullfillno);
			}			
			$prorata_skew_dtl = isset($prorata_alt_dtl['client_dtl'])?$prorata_alt_dtl['client_dtl']:"";
			$prorata_client_logs = isset($prorata_alt_dtl['prorata_client_logs'])?$prorata_alt_dtl['prorata_client_logs']:"";
			$prorata_days_logs = isset($prorata_alt_dtl['prorata_days_logs'])?$prorata_alt_dtl['prorata_days_logs']:"";
			$prorata_dtls = "<input type='hidden' id='prorata_dtls$ins_i' name='prorata_dtls[$ins_i]' class='formStyleTextarea' value='$prorata_skew_dtl'>";
			
			$cl_skew_dtl = isset($prorate_cl_dtl['cl_alr_dtl'])?$prorate_cl_dtl['cl_alr_dtl']:"";
			$pro_cl_skew_dtl = isset($prorate_cl_dtl['pro_cl_alr_dtl'])?$prorate_cl_dtl['pro_cl_alr_dtl']:"";
			$pro_cl_alr_log_str = isset($prorate_cl_dtl['pro_cl_alr_log_str'])?$prorate_cl_dtl['pro_cl_alr_log_str']:"";
			$cl_skew_input_dtl = "<input type='hidden' value='$cl_skew_dtl' id='cl_alr$ins_i' name='cl_alr[$ins_i]'>" ;
			$pro_cl_skew_input_dtl   = "<input type='hidden' value='$pro_cl_skew_dtl' id='pro_cl_alr$ins_i' name='pro_cl_alr[$ins_i]'>" ;
			$pro_cl_alr_log_dtl   = "<input type='hidden' value='$pro_cl_alr_log_str' id='pro_cl_alr_log$ins_i' name='pro_cl_alr_log[$ins_i]'>" ;
			$custom_license_val		=	'';
			if($lead_type!='1'){
			    $custom_license_info	=	array();
			    $custom_licence_list	= get_custom_license_query($ins_root_order_no,$ins_root_fullfillno,$ins_product_code);
			    $resultcustom_product = execute_my_query($custom_licence_list);
			    while($rows=mysqli_fetch_array($resultcustom_product)){
			        $custom_license_info[]	=	$rows['GOP_PRODUCT_CODE'].'-'.$rows['GOP_PRODUCT_SKEW'].'-'.$rows['GFT_SKEW_PROPERTY'].'-'.$rows['GSPM_DISCOUNT_PERCENTAGE'];
			    }
			    $custom_license_val		=	implode(',',$custom_license_info);
			}
			$installation_detail	.= <<<END
<TR id='qrow$ins_i' class='ass_row'><td>$ins_i.<input type="checkbox" id="splitupdate$ins_i" name="splitupdate[$ins_i]" value="1" onchange="javascript:check_ordersplit($ins_i);" class='install_table'>
		<input type="hidden" id="splitupdateedit$ins_i" name="splitupdateedit[$ins_i]" value="$split_done"></td>
	<td><input type="text" id="ins_lead_code$ins_i" name="ins_lead_code[$ins_i]" value="$clead_code" class="formStyleTextarea ins_count" size=6 readonly="true">
		<input type="hidden" id="ins_reff_id$ins_i" name="ins_reff_id[$ins_i]" value="$install_reff_id" class="formStyleTextarea" size=6 readonly="true"></td>
	<td width="60px">$c_ins_name</td>
	<TD><input type="text" id="order_nos$ins_i" name="order_nos[$ins_i]" value="$ins_order_no" class="formStyleTextarea" readonly="true" size=15></TD>
	<TD><input type="text" id="fullfillment_no$ins_i" name="fullfillment_no[$ins_i]" class="formStyleTextarea" value="$ins_fullfillno" readonly="true" size=5></td>
	<TD>$ins_product_name</TD>
	<TD><input type="text" id="clients_installed$ins_i" name="clients_installed[$ins_i]" class="formStyleTextarea" value="$no_of_clients" readonly="true" size=5></TD>
	<TD><input type="text" id="no_of_companies$ins_i" name="no_of_companies[$ins_i]" class="formStyleTextarea" value="$no_of_company" readonly="true" size=5></TD>
	<td>$license_type
		<input type="hidden" id="products_pcode$ins_i" name="products_pcode[$ins_i]" class="formStyleTextarea" value="$ins_product_code">
		<input type="hidden" id="products_pskew$ins_i" name="products_pskew[$ins_i]" class="formStyleTextarea" value="$ins_product_skew">
		<input type="hidden" id="skew_discount$ins_i" name="skew_discount[$ins_i]" class="formStyleTextarea" value="$skew_discount">
		<input type="hidden" id="root_order_no$ins_i" name="root_order_no[$ins_i]" class="formStyleTextarea" value="$ins_root_order_no">
		<input type="hidden" id="root_fullfillment_no$ins_i" name="root_fullfillment_no[$ins_i]" class="formStyleTextarea" value="$ins_root_fullfillno">
		<input type="hidden" id="head_of_family$ins_i" name="head_of_family[$ins_i]" class="formStyleTextarea" value="$ins_head_family">
		<input type="hidden" id="upgraded_dtl$ins_i" name="upgraded_dtl[$ins_i]" class="formStyleTextarea" value="">
		<input type="hidden" id="skew_property$ins_i" name="skew_property[$ins_i]" class="formStyleTextarea" value="$skew_property">
		<input type="hidden" id="referer_skew$ins_i" name="referer_skew[$ins_i]" class="formStyleTextarea" value="$referer_skew">
		<input type="hidden" id="cust_lic_skew$ins_i" name="cust_lic_skew[$ins_i]" class="formStyleTextarea" value="$custom_license_val">
		<input type="hidden" id="edition_type$ins_i" name="edition_type[$ins_i]" class="formStyleTextarea" value="$edition_type">
		<input type="hidden" id="gpm_lic_type$ins_i" name="gpm_lic_type[$ins_i]" value="$gpm_lic_type">
        $prorata_dtls
        $prorata_client_logs
        $prorata_days_logs
        $cl_skew_input_dtl
        $pro_cl_skew_input_dtl
        $pro_cl_alr_log_dtl
        <script>expire_for_arr[$ins_i] = "$gid_expire_for";</script>
	</TD>
	<TD><input type="text" id="assexpiry$ins_i" name="assexpiry[$ins_i]" class="formStyleTextarea" value="$ins_ass_expirydate" readonly="true" size=10></TD>
</TR>
END;
		}
		$installation_detail.="</tbody>";
	
	}
	$installation_detail.="<input type='hidden' name='install_dtl' id='install_dtl' value='$is_installed'>".
			"</table></fieldset>";
	$feature_dtl = get_feature_installation_details($lead_code,$lead_type);
	if($feature_dtl!=''){
	    $installation_detail .= " <br> $feature_dtl";
	}
	$installation_detail .= "</div></td></tr>";
	
	$installation_detail .= <<<END
<script type="text/javascript">
	var GLOBAL_KIT_BASED = "$kit_based";
	function apply_filters(){
		var expiry_from = jQuery("#expiry_from").val();
		var expiry_to 	= jQuery("#expiry_to").val();
		var row_length  = jQuery(".ass_row").length;
		for(j=1;j<=row_length;j++){
			jQuery("#qrow"+j).removeClass("hide").addClass("show");
			var ins_exp = jQuery("#assexpiry"+j).val();
			var this_time = new Date(ins_exp).getTime();
			if(expiry_from!=""){
				var from_timestamp = new Date(expiry_from).getTime();
				if(this_time < from_timestamp){
					jQuery("#qrow"+j).removeClass("show").addClass("hide");
				}				
			}
			if(expiry_to!=""){
				var to_timestamp = new Date(expiry_to).getTime();
				if(this_time > to_timestamp){
					jQuery("#qrow"+j).removeClass("show").addClass("hide");
				}
			}
		}
	}
	jQuery('document').ready(function(){
		jQuery('#splitupdate0').click(function(){
			var tot_ins = jQuery(".install_table").length;
			if(jQuery(this).is(':checked')){
                var ccn = 1;
                while(ccn<tot_ins){
                    if(!jQuery("#qrow"+ccn).hasClass("hide")){
                        jQuery("#splitupdate"+ccn).attr('checked', true);
                    }
					ccn++;
                }
			}else {
				jQuery(".install_table").attr('checked', false);
			}
			var inc = 1;
			while(inc<tot_ins){
				check_ordersplit(inc, true);
				inc++;
			}
		});
	});
</script>
END;
	return $installation_detail;
}
/**
 * 
 * @param string $purpose
 * @param string $quotation_no
 * @return string
 */
function get_discount_adjustment_fields($purpose,$quotation_no) {
    $number_function = "onkeyup='javascript:extractNumber(this,2,false);' onkeypress='javascript:return blockNonNumbers(this, event, false, false);'";
    $cust_select = '';
    $adj_readonly = '';
    if($purpose=='quote') {
        $cust_select = <<<END
<table id='adj_cust_table' style='cell-padding:2px;border:1px solid black;margin:auto;width:300px;'>
    <caption class="datalabel" style='text-align:center;'>If you want to do upgrdation adjustment from stand-alone to Delight, select existing customers using stand-alone license</caption>
    <thead><tr><th colspan='2'>Customer</th></tr></thead>
    <tbody>
    <tr row_num='0'><td class='row_no' style='text-align:right;'>1</td><td><input type='text' id='customerName0' name='customerName[0]' class='select-element' placeholder='Select Customer' spellcheck='false'>
    <input type='hidden' id='customerId0' name='customerId[0]' class='adj_cust'>
    <input type='hidden' id='adjAmt0' name='adjAmt[0]' class='adj_amt'>
    &nbsp;&nbsp;<i class='fa fa-times' aria-hidden='true' id='remove_btn0' onclick="remove_adj_row('0');" style='color:red;cursor:pointer;'></i>
    </td></tr>
    </tbody>
    <tfoot><tr><th colspan='2'><input type='button' id='add_adj_cust' onclick='add_adj_cust_row();' value='ADD'></th></tr><tfoot>
</table>
END;
    } else if($purpose=='order' and $quotation_no!='') {
        $adj_readonly = "readonly='readonly'";        
    }
        $adj_fields =<<<END
<tr><td class="datalabel" nowrap>Adjustment Amount</td><td>
<input name="order_adj_amt" size=10 id="order_adj_amt" class="formStyleTextarea" value='0.00' $number_function onblur='check_adj_amt();' $adj_readonly>
<input name="max_adj_amt" size=10 id="max_adj_amt" type='hidden' readonly='true' value='0.00'>
<input name="prev_adj_amt" size=10 id="prev_adj_amt" type='hidden' readonly='true' value='0.00'></td></tr>
<tr><td class="datalabel" nowrap>Total Amount without Tax</td><td>
<input name="taxable_amt" size=10 id="taxable_amt" class="formStyleTextarea" value='0.00' readonly='true'></td></tr><tr>
<td class="datalabel" nowrap>Total Tax</td><td>
<input name="total_tax" size=10 id="total_tax" class="formStyleTextarea" value='0.00' readonly='true'></td></tr>
END;
    $ui_str = <<<END
<div id='adj_cust' style='margin:auto;width:100%'>
    $cust_select
    <table border="0" cellpadding="1" cellspacing="0" width="320px"><tbody><tr>
    <td>$adj_fields</td></tr></tbody></table>
</div>
END;
    if($purpose=='order') {
        $ui_str = $adj_fields;
    }
    $ui_str .=<<<END
<script type='text/javascript'>
jQuery('document').ready(function () {
    var tax_mode = jQuery('input[name=tax_mode]').val();
    get_autocomplete_config('customerName0','customerId0',[],"service/auto_complete_customer.php",{'limit':'20','purpose':'order_adj_cust','quotation_no':'$quotation_no','tax_mode':tax_mode},'calculate_adj_amt');
});
function check_and_set_vals(cust_count) {
    var valid_cust = true;
    var new_adj_amt = 0.0;
    var curr_lead_codes = [];
    var cust_count = jQuery('#adj_cust_table tbody tr').length;
    if(cust_count>0) {
        jQuery('.adj_cust').each(function() {
            var cust_val = jQuery(this).val();
            var cust_dtl_arr = cust_val.split('-');
            var cust_id = cust_dtl_arr[0];
            curr_lead_codes.push(cust_id);
        });
        curr_row = 0;
        jQuery('.adj_cust').each(function() {
            curr_row++;
            var cust_val = jQuery(this).val();
            if(cust_val!==undefined && cust_val!='') {
                var cust_dtl_arr = cust_val.split('-');
                if(curr_row==cust_count && jQuery.inArray(cust_dtl_arr[0],curr_lead_codes)>-1 && jQuery.inArray(cust_dtl_arr[0],curr_lead_codes)!=curr_row-1) {
                    alert(cust_dtl_arr[1]+" - "+cust_dtl_arr[2]+" is an already selected customer");
                    valid_cust = false;
                    jQuery('tr[row_num='+cust_count+']').remove();
                    return false;
                }
            }
        });
        if(valid_cust) {
            jQuery('.adj_amt').each(function() {
                new_adj_amt += parseFloat(jQuery(this).val());
            });
            jQuery('#max_adj_amt').val(new_adj_amt);
            jQuery('#order_adj_amt').val(round(new_adj_amt,2));
            emailcheck123();
        } else {
            jQuery('#customerId'+(cust_count-1)).val('');
            jQuery('#customerName'+(cust_count-1)).val('');
        }
    } else {
        jQuery('#max_adj_amt').val('0.00');
        jQuery('#order_adj_amt').val('0.00');
        jQuery('#prev_adj_amt').val('0.00');
        emailcheck123();
    }
    return true;
}
function calculate_adj_amt(event,ui) {
    var cust_count = jQuery('#adj_cust_table tbody tr').length;
    jQuery('#customerId'+(cust_count-1)).val(ui.item.id);
    var cust_dtl_arr = ui.item.id.split('-');
    var cust_id = cust_dtl_arr[0];
    var params = "purpose=get_adj_amt_for_customer&custCode="+cust_id+"&quotation_no=$quotation_no"
    jQuery.ajax({
    	url: '/service/process_the_request.php',
    	type: "POST",
    	data: params,
    	contentTyepe: 'json',
    	success: function(data,status,xhr) {
			var dat = JSON.parse(data);
            jQuery('#adjAmt'+(cust_count-1)).val(dat['message']);
            var update_res = check_and_set_vals(cust_count);
            if(!update_res) {
                jQuery('tr[row_num='+cust_count+']').remove();
            }
    	},
    	error: function(jqXHR, textStatus, errorThrown) {
    		var json_response = JSON.parse(jqXHR.responseText);
    		alert(json_response['message']);
            jQuery('tr[row_num='+cust_count+']').remove();
    	}
	});
}
function remove_adj_row(indx) {
    jQuery('tr[row_num='+indx+']').remove();
    var i = 0;
    jQuery('.row_no').each(function() {
        var old_no = parseInt(jQuery(this).text())-1;
        jQuery(this).html(i+1);
        jQuery(this).parent().attr('row_num',i);
        jQuery('#customerName'+old_no).attr('name','customerName['+i+']');
        jQuery('#customerName'+old_no).attr('id','customerName'+i);
        jQuery('#customerId'+old_no).attr('name','customerId['+i+']');
        jQuery('#customerId'+old_no).attr('id','customerId'+i);
        jQuery('#adjAmt'+old_no).attr('name','adjAmt['+i+']');
        jQuery('#adjAmt'+old_no).attr('id','adjAmt'+i);
        jQuery('#remove_btn'+old_no).attr('onclick',"remove_adj_row('"+i+"')");
        jQuery('#remove_btn'+old_no).attr('id','remove_btn'+i);
        i++;
    });
    check_and_set_vals();
}
function check_adj_amt() {
    var allowed_amt = jQuery('#max_adj_amt').val();
    var new_amt = jQuery('#order_adj_amt').val();
//     if(parseFloat(allowed_amt)<parseFloat(new_amt)) {
//         alert('Adjustment amount cannot exceed Rs. '+allowed_amt);
//         jQuery('#order_adj_amt').val(jQuery('#prev_adj_amt').val());
//         return false;
//     }
    jQuery('#prev_adj_amt').val(jQuery('#order_adj_amt').val());
    emailcheck123();
}
function add_adj_cust_row() {
    var cust_count = jQuery('#adj_cust_table tbody tr').length;
    var prev_id = cust_count - 1;
    if(cust_count>0 && (jQuery('#customerId'+prev_id).val()===undefined || jQuery('#customerId'+prev_id).val().trim()=='')) {
        alert('Please select order adjustment customer for existing fields and then add new row');
        return false;
    }
    var tax_mode = jQuery('input[name=tax_mode]').val();
    var new_cust_row = "<tr row_num="+cust_count+"><td style='text-align:right;' class='row_no'>"+
                       (cust_count+1)+"</td><td><input type='text' id='customerName"+cust_count+"' "+
                       " name='customerName["+cust_count+"]' class='select-element' placeholder='Select Customer' spellcheck='false'>"+
    	               "<input type='hidden' id='customerId"+cust_count+"' name='customerId["+cust_count+"]' class='adj_cust'>"+
                       "<input type='hidden' id='adjAmt"+cust_count+"' name='adjAmt["+cust_count+"]' class='adj_amt'>"+
                       "&nbsp;&nbsp;<i class='fa fa-times' aria-hidden='true' id='remove_btn"+cust_count+"' onclick=\"remove_adj_row('"+cust_count+"');\" style='color:red;cursor:pointer;'></i>";
                       "</td></tr>";
    jQuery('#adj_cust_table tbody').append(new_cust_row);
    get_autocomplete_config('customerName'+cust_count,'customerId'+cust_count,[],"service/auto_complete_customer.php",{'limit':'20','purpose':'order_adj_cust','quotation_no':'$quotation_no','tax_mode':tax_mode},'calculate_adj_amt');
}
</script>
END;
    return $ui_str;
}
/**
 * @param string $selected_value
 * @return string
 */
function get_ui_for_pdf_products_view($selected_value) {
    $no_selected = ''; $yes_selected = '';
    if($selected_value=='0') {
        $no_selected = "checked=checked";
    } else if($selected_value=='1') {
        $yes_selected = "checked=checked";
    }
    $ui = <<<END
<td class="datalabel" width='180'>Do you want Product description in single row in PDF?</td>
<td><input type='radio' name='prod_pdf' id='prod_pdf_0' value='0' $no_selected>&nbsp;No&nbsp;&nbsp;&nbsp;<input type='radio' name='prod_pdf' id='prod_pdf_1' value='1' $yes_selected>&nbsp;Yes</td> 
END;
    return $ui;
}

?>
