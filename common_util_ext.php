<?php
require_once(__DIR__.'/common_util.php');
/**
 * @param string $install_id
 *
 * @return boolean
 */
function is_trial_installtion($install_id){
	$gid_install_id = (int)$install_id;
	$sql1 = " select GID_INSTALL_ID from gft_install_dtl_new ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
			" where GID_INSTALL_ID='$gid_install_id' and GPM_LICENSE_TYPE=3 ";
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1)==1){
		return true;
	}
	return false;
}

/**
 * @param string $div_id
 * @param string $inner_div_class
 * @param string $title_val
 * @param string $data_id
 * @param string $load_data
 * 
 * @return string
 */
function modal_popup_elements($div_id,$inner_div_class,$title_val,$data_id,$load_data=''){
$ret_val = <<<END
	<div id='$div_id' class='modal-container' style='display: none'>
		<div id='rsm-detail-data' class='$inner_div_class'>
			<div class='summary-title' style='margin:10px 0px;'>$title_val</div>
			<div class='close-wrapper'><button type="button" onclick='closeModal("$div_id");' class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
			<div id='$data_id'> $load_data </div>
		</div>
	</div>
	
	<script>
	function openModal(model_id){
		jQuery('#'+model_id).css('display','block');
	}
	function closeModal(model_id){
		jQuery('#'+model_id).css('display','none');
	}
	jQuery(document).keyup(function (e) {
		if (e.keyCode == 27) {
			closeModal("$div_id");
		}
	});
	</script>
END;
	return $ret_val;
}

/**
 * @param string $name_field
 * @param string $id_field
 * @param string $src_val
 * @param string $select_function
 * 
 * @return string
 */
function get_autocomplete_ui($name_field,$id_field,$src_val,$select_function=''){
	$sel_fun = ($select_function=='')?'':"$select_function();";
	$ret = <<<END
	<input type='text' id='$name_field' name='$name_field' class='select-element' placeholder='-select-' spellcheck="false">
	<input type='hidden' id='$id_field' name='$id_field'>
	<script>
	jQuery("#$name_field").autocomplete({
      source: $src_val,
	  autoFocus: true,
	  delay:150,
	  minLength: 0,
      showNoSuggestionNotice:true,
      noSuggestionNotice:"No Matches",
      select: function( event, ui ) {
		jQuery("#$id_field").val(ui.item.id);
		$sel_fun
      },
      response: function(event, ui) {
      	if(jQuery(this).val()==""){
      		jQuery("#$id_field").val("");
      	}
        if (!ui.content.length) {
            var noResult = { id:"",label:"No Result"};
            ui.content.push(noResult);
        }
      },
      change:function(event,ui){
      	if(!ui.item){
      		jQuery("#$id_field").val("");
      	}
	  }
	}).focus(function(){
		jQuery(this).autocomplete("search");
    });
	</script>
END;
	return $ret;
}

/**
 * @param boolean $only_gft_emp
 * @param string[int] $emp_id_arr
 * 
 * @return string
 */
function employee_list_auto_complete($only_gft_emp=true,$emp_id_arr=null){
	$all_emp_arr = get_emp_master(null,'A',null,$only_gft_emp,false,0,$emp_id_arr);
	$earr = array();
	foreach($all_emp_arr as $ke => $va){
		$earr[] = array('id'=>$va[0],'label'=>$va[1]);
	}
	$emp_source = json_encode($earr);
	$ret = <<<END
	<input type='text' id='employeeName' name='employeeName' class='select-element' placeholder='Select Employee' spellcheck="false">
	<input type='hidden' id='employeeId' name='employeeId'>
	<script>
	jQuery("#employeeName").autocomplete({
      source: $emp_source,
	  autoFocus: true,
	  delay:150,
	  minLength: 0,
      showNoSuggestionNotice:true,
      noSuggestionNotice:"No Matches",
      select: function( event, ui ) {
		jQuery("#employeeId").val(ui.item.id);
      },
      response: function(event, ui) {
      	if(jQuery(this).val()==""){
      		jQuery("#employeeId").val("");	
      	}
        if (!ui.content.length) {
            var noResult = { id:"",label:"No Result"};
            ui.content.push(noResult);
        }
      },
      change:function(event,ui){
      	if(!ui.item){
      		jQuery("#employeeId").val("");	
      	}
	  }
	}).focus(function(){
		jQuery(this).autocomplete("search");
    });
	</script>
END;
	return $ret;
}


/**
 * @param string $select_function
 * 
 * @return string
 */
function customer_list_auto_complete($select_function=''){
	$sel_fun = ($select_function=='')?'':"$select_function();";
	$ret = <<<END
	<input type='text' id='customerName' name='customerName' class='select-element' placeholder='Select Customer' spellcheck="false">
	<span class="corp_span">Corp <input type='checkbox' id="corp_cust" name="corp_cust" ></span>  
    <input type='hidden' id='customerId' name='customerId'>
	<script>
	jQuery("#customerName").autocomplete({
      source: function(request, response) {
        jQuery.getJSON("service/auto_complete_customer.php?limit=20&term="+request.term+"&corp_cust="+jQuery("#corp_cust").prop("checked") , response);
      },
	  autoFocus: true,
	  delay:150,
	  minLength: 0,
	  showNoSuggestionNotice:true,
	  noSuggestionNotice:"No Matches",
      select: function( event, ui ) {
			jQuery("#customerId").val(ui.item.id);
			$sel_fun
	      },
	      response: function(event, ui) {
	      	if(jQuery(this).val()==""){
	      		jQuery("#customerId").val("");
	      	}
	        if (!ui.content.length) {
	            var noResult = { id:"",label:"No Result"};
	            ui.content.push(noResult);
	        }
	      },
	      change:function(event,ui){
	      	if(!ui.item){
	      		jQuery("#customerId").val("");
	      	}
		  }
	});
	</script>
END;
	return $ret;
}

/**
 * @param int $int_val
 * @param string $base
 *
 * @return string
 */
function getShortenedURLFromID($int_val, $base = ALLOWED_CHARS){
	$length = strlen($base);
	$out = "";
	while($int_val > $length - 1){
		$out = $base[(int)fmod($int_val, $length)] . $out;
		$int_val = floor($int_val / $length);
	}
	$return_str = "-".$base[$int_val].$out;
	return $return_str;
}
/**
 * @param string $long_url
 * @param string $ip_addr
 *
 * @return mixed[]
 */
function getShortenedURL($long_url, $ip_addr){
	$json_arr = array();
	$domain = "http://gfts.in/";
	$que1 = "select GUS_SHORT_KEY,GUS_LONG_URL from gft_url_shortner where GUS_LONG_URL='$long_url' ";
	$res1 = execute_my_query($que1);
	if($row1 = mysqli_fetch_array($res1)){
		$json_arr['short_url'] = $domain.$row1['GUS_SHORT_KEY'];
	}else{
		$ins1 = " insert into gft_url_shortner (GUS_LONG_URL,GUS_SHORT_KEY,GUS_DATE_TIME,GUS_REQUEST_IP) ".
				" values ('$long_url','',now(),'$ip_addr') ";
		$ins_res = execute_my_query($ins1);
		if($ins_res){
			$last_insert_id = mysqli_insert_id_wrapper();
			$short_key = getShortenedURLFromID(1000+$last_insert_id);
			execute_my_query("update gft_url_shortner set GUS_SHORT_KEY='$short_key' where GUS_ID='$last_insert_id' ");
			$json_arr['short_url'] = $domain.$short_key;
		}
	}
	return $json_arr;
}
/**
 * @param string $order_no
 * @return OrderProdDetails
 */
function get_order_prod_dtl_for_invoice($order_no) {
	$price_qry = execute_my_query(" select gop_product_code,gop_product_skew,gop_list_price,gop_sell_rate, ".
								  " if(gop_coupon_hour is not null and gop_coupon_hour>0,gop_coupon_hour,1) gop_coupon_hour, ".
								  " gop_sell_amt,gop_qty,GOP_ADJ_DISCOUNT,gop_cgst_per+gop_sgst_per+gop_igst_per ".
								  " total_tax_per from gft_order_product_dtl where gop_order_no='$order_no'");
	$opd = new OrderProdDetails();
	while($res = mysqli_fetch_array($price_qry)) {
		$code = $res['gop_product_code'];
		$skew = $res['gop_product_skew'];
		$codeskew = $code."-".$skew;
		if((int)((float)$res['gop_sell_rate']*(int)$res['gop_coupon_hour'])==0) {
			continue;
		}
		$opd->listprice[$codeskew] = (float)$res['gop_list_price'];
		$opd->sellamt[$codeskew] = ((float)$res['gop_sell_rate']*(int)$res['gop_coupon_hour']);
		$opd->sellamt[$codeskew] = $opd->sellamt[$codeskew]+(float)$res['GOP_ADJ_DISCOUNT']*(int)$res['gop_coupon_hour'];
		$opd->netamt[$codeskew] = $opd->sellamt[$codeskew]*(int)$res['gop_qty']*(100+$res['total_tax_per'])/100;//$res['gop_sell_amt'];
		$opd->gop_qty[$codeskew] = (int)$res['gop_qty'];
	}
	return $opd;
}
/**
 * @param string $split
 * @param string[string] $rown
 * @param InvoiceProdDetails $prod_dtl
 * @param OrderProdDetails $opd
 * @param string[string] $gst_arr
 * @param boolean $only_services
 * @return InvoiceProdDetails
 */
function get_invoice_prod_dtl_for_order($split,$rown,$prod_dtl,$opd,$gst_arr,$only_services) {
	$prod_dtl->s_tax_rate[] = '0.0';
	$prod_dtl->s_tax_amt[] = '0.0';
	$prod_dtl->serv_tax_rate[] = '0.0';
	$prod_dtl->serv_tax_amt[] = '0.0';
	if($split=='1') {
		if($only_services) {
			$prod_dtl->pcode[] 	  = $rown['gop_product_code'];
			$prod_dtl->pskew[] 	  = $rown['gop_product_skew'];
			$hr = ($rown['gop_coupon_hour']>0?$rown['gop_coupon_hour']:'1');
			$prod_dtl->coupon_hrs[] = (in_array($rown['gft_skew_property'],array('4','15'))?'0':$hr);
			$prod_dtl->qty[]		= $rown['gop_qty'];
			$prod_dtl->orderno[]	= $rown['gop_order_no'];//$order_no;
			$prod_dtl->list_price[] = $rown['gop_list_price'];
			$prod_dtl->adj_discount[] = $rown['GOP_ADJ_DISCOUNT'];
			$prod_dtl->sell_amt[]	  = $rown['gop_sell_rate']+$rown['GOP_ADJ_DISCOUNT'];
			$tax_rate 				  = $rown['tax_rate'];
			$netamt = (($rown['gop_sell_rate']+$rown['GOP_ADJ_DISCOUNT'])*($hr*$rown['gop_qty'])*(100+$tax_rate)/100);
			$prod_dtl->net_amt[]	  = $netamt;
			$gst_arr = array('cgst_per'=>$rown['GOP_CGST_PER'],'cgst_amt'=>$rown['GOP_CGST_AMT'],
					'sgst_per'=>$rown['GOP_SGST_PER'],'sgst_amt'=> $rown['GOP_SGST_AMT'],
					'igst_per'=>$rown['GOP_IGST_PER'],'igst_amt'=>$rown['GOP_IGST_AMT']);
			$prod_dtl->gst_fields[$rown['gop_product_code']."-".$rown['gop_product_skew']] = $gst_arr;
		} else {
			$pcode = $rown['GCO_PRODUCT_CODE'];
			$pskew = $rown['GCO_SKEW'];
			$codeskew = $pcode."-".$pskew;
			$is_present = isset($prod_dtl->codeskews[$codeskew]);
			$i = ($is_present?$prod_dtl->codeskews[$pcode."-".$pskew]:count($prod_dtl->pcode));
			if(!$is_present) {
				$prod_dtl->codeskews[$codeskew] = $i;
			}
			$prod_dtl->orderno[$i] = $rown['GCO_ORDER_NO'];
			$prod_dtl->pcode[$i] = $pcode;
			$prod_dtl->pskew[$i] = $pskew;
			$prod_dtl->qty[$i]   = ($rown['GCO_CUST_QTY'] + ($is_present?$prod_dtl->qty[$i]:0));
			$prod_dtl->list_price[$i] = (string)$opd->listprice[$codeskew];
			$prod_dtl->sell_amt[$i] = (string)$opd->sellamt[$codeskew];
			$prod_dtl->coupon_hrs[$i] = (($rown['GOP_COUPON_HOUR']*$rown['GCO_CUST_QTY']) + ($is_present?$prod_dtl->coupon_hrs[$i]:0));
			if($is_present) {
				$prod_dtl->gst_fields[$codeskew]['cgst_amt'] += (float)$gst_arr['cgst_amt'];
				$prod_dtl->gst_fields[$codeskew]['sgst_amt'] += (float)$gst_arr['sgst_amt'];
				$prod_dtl->gst_fields[$codeskew]['igst_amt'] += (float)$gst_arr['igst_amt'];
			} else {
				$prod_dtl->gst_fields[$codeskew] = /*.(float[string]).*/$gst_arr;
			}
			$prod_dtl->net_amt[$i] = (($opd->netamt[$codeskew]/$opd->gop_qty[$codeskew])*$rown['GCO_CUST_QTY'] + ($is_present?$prod_dtl->net_amt[$i]:0));
			$prod_dtl->adj_discount[$i] = $rown['GOP_ADJ_DISCOUNT'];
		}
	} else {
		$prod_dtl->qty[]	 			= $rown['GOP_QTY'];
		$prod_dtl->coupon_hrs[]			= $rown['GOP_COUPON_HOUR'];
		$prod_dtl->pcode[]				= $rown['GOP_PRODUCT_CODE'];
		$prod_dtl->pskew[]				= $rown['GOP_PRODUCT_SKEW'];
		$prod_dtl->orderno[] 			= $rown['GOP_ORDER_NO'];
		$prod_dtl->list_price[] 		= $rown['GOP_LIST_PRICE'];
		$prod_dtl->adj_discount[]		= $rown['GOP_ADJ_DISCOUNT'];
		$sellamt = $rown['GOP_SELL_RATE']+$rown['GOP_ADJ_DISCOUNT'];
		$prod_dtl->sell_amt[] 		= $sellamt;
		$tax_rate = $rown['GOP_SGST_PER'] + $rown['GOP_CGST_PER'] + $rown['GOP_IGST_PER'];
		$netamt = ($sellamt*$rown['GOP_QTY']*($rown['GOP_COUPON_HOUR']>0?$rown['GOP_COUPON_HOUR']:1)*(100+$tax_rate)/100);
		$prod_dtl->net_amt[]			= $netamt;
		$gst_arr = array('cgst_per'=>$rown['GOP_CGST_PER'],'cgst_amt'=>$rown['GOP_CGST_AMT'],
				'sgst_per'=>$rown['GOP_SGST_PER'],'sgst_amt'=> $rown['GOP_SGST_AMT'],
				'igst_per'=>$rown['GOP_IGST_PER'],'igst_amt'=>$rown['GOP_IGST_AMT']);
		$prod_dtl->gst_fields[$rown['GOP_PRODUCT_CODE']."-".$rown['GOP_PRODUCT_SKEW']] = $gst_arr;
	}
	return $prod_dtl;
}

/**
 * @param string $customerId
 * @param string $pcode
 * @param string $pgroup
 * @param string $install_id
 * @param string $analytics_arr
 * 
 * @return void
 */
function save_menu_analytics_from_pos($customerId,$pcode,$pgroup,$install_id,$analytics_arr){
	$ins_val = $put_comma = "";
	foreach ($analytics_arr as $key => $val_arr){
		$menu_id 		= $val_arr['menuId'];
		$accessed_time	= $val_arr['accessTime'];
		$ins_val	.= " $put_comma ('$customerId','$pcode','$pgroup','$install_id',now(),'$menu_id','$accessed_time') ";
		$put_comma = ",";
	}
	if($ins_val!=''){
		$ins_que = " insert into gft_product_menu_analytics (GPA_LEAD_CODE,GPA_PRODUCT_CODE,GPA_PRODUCT_GROUP, ".
				" GPA_INSTALL_ID,GPA_RECEIVED_DATE,GPA_MENU_ID,GPA_ACCESS_DATE) values $ins_val ";
		execute_my_query($ins_que);
	}
}
/**
 * @return string[string][int]
 */
function get_monthly_invoice_order_nos() {
	$orders_qry = " select god_order_no,god_lead_code,god_emp_id from gft_order_hdr where god_invoice_status='M' and god_order_status!='C' ";
	$orders_res = execute_my_query($orders_qry);
	$orders = /*.(string[string][int]).*/array();
	while($ord_rows = mysqli_fetch_array($orders_res)) {
		$orders[$ord_rows['god_order_no']] = array($ord_rows['god_lead_code'],$ord_rows['god_emp_id']);
	}
	return $orders;
}
/**
 * @param string $order_no
 * @param string $invoice_raised
 * @return string[int]
 */
function get_franchise_invoice_lead_codes_for_orders($order_no,$invoice_raised) {
	$lead_code_qry = " select gco_cust_code from gft_cp_order_dtl join gft_lead_hdr_ext on (gle_lead_code=gco_cust_code) ".
					 " where gco_invoice_raised='$invoice_raised' and gle_invoice_for_franchise='1' and gco_order_no='$order_no' ";
	$lead_code_res = execute_my_query($lead_code_qry);
	$lead_codes = /*.(string[int]).*/array();
	while($row = mysqli_fetch_array($lead_code_res)) {
		if(!in_array($row['gco_cust_code'],$lead_codes)) {
			$lead_codes[] = $row['gco_cust_code'];
		}
	}
	return $lead_codes;
}
/**
 * @param int $split
 * @param string $order_no
 * @param boolean $for_cron
 * @param boolean $only_services
 * @param string $franchise_lead_code
 * @return string
 */
function get_prod_dtl_query_for_invoice($split,$order_no,$for_cron,$only_services,$franchise_lead_code='') {
	global $order_product_fields;
	$query = '';
	$wh_cond = " and (gop_invoice_raised is null or gop_invoice_raised not in ('1','2')) ";
	if($for_cron) {
		$wh_cond = " and gop_invoice_raised='2' ";
	}
	if($split==0) {
		if($for_cron) {
			$wh_cond .= " and gop_product_code!='391' and gft_skew_property not in (4,15,8) ";
		}
		$query = " select GOP_ORDER_NO, GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GOP_QTY, GFT_SKEW_PROPERTY,GOP_SELL_AMT,pfm.gpm_is_internal_product, ".
				" pfm.gpm_category,GOP_LIST_PRICE, GOP_SELL_RATE, GOP_SERVICE_TAX_RATE, GOP_TAX_RATE, GOP_SERVICE_TAX_AMT, GOP_TAX_AMT,pm.gpm_product_type, ".
				" GOP_FULLFILLMENT_NO,GOP_COUPON_HOUR,pfm.gpm_is_base_product,gop_license_status,GOP_ADJ_DISCOUNT,gft_skew_property $order_product_fields ".
				" from gft_order_hdr oh join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) ".
				" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and pm.GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
				" left join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code) ".
				" where GOD_ORDER_NO='$order_no' $wh_cond ";
	} else {
		if($only_services) {
			$query = " select gop_product_code,gop_product_skew,gop_coupon_hour,gop_list_price,gop_sell_rate,gop_order_no, ".
					" gop_sell_amt,gop_qty,gft_skew_property,GOP_FULLFILLMENT_NO,gop_sgst_per+gop_igst_per+gop_cgst_per tax_rate, ".
					" GOP_ADJ_DISCOUNT,gft_skew_property $order_product_fields from gft_order_product_dtl ".
					" join gft_product_master on (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew) ".
					" where gop_sell_amt>0 and gop_order_no='$order_no' and (gop_product_code='391' or ".
					" gft_skew_property in (4,15,8)) $wh_cond ";
		} else {
			$wh_cond = " and (gco_invoice_raised is null or gco_invoice_raised not in ('1','2')) ";
			if($for_cron) {
				$wh_cond = " and gco_invoice_raised='2' ";
			}
			if($franchise_lead_code!='' and $franchise_lead_code!='0') {
				$wh_cond .= " and gco_cust_code='$franchise_lead_code' ";
			} else if($for_cron) {
				$skip_lead_codes = get_franchise_invoice_lead_codes_for_orders($order_no, ($for_cron?'2':'1'));
				$wh_cond .= " and gco_cust_code  not in ('".implode("','",$skip_lead_codes)."')";
			}
			$query = " select GCO_ORDER_NO, GCO_PRODUCT_CODE, GCO_SKEW, GCO_CUST_QTY,pm.GFT_SKEW_PROPERTY,gco_cust_code,pfm.gpm_category, ".
					" pfm.gpm_is_internal_product,pfm.gpm_is_base_product,GOP_COUPON_HOUR,gco_license_status,GCO_FULLFILLMENT_NO,pm.gpm_product_type,GOP_ADJ_DISCOUNT,gft_skew_property ".
					" from gft_order_hdr oh join gft_cp_order_dtl on (gco_order_no=god_order_no) ".
					" join gft_order_product_dtl on (gop_order_no=gco_order_no and gop_product_code=gco_product_code and gop_product_skew=gco_skew) ".
					" join gft_product_master pm on (GPM_PRODUCT_CODE=GCO_PRODUCT_CODE and GPM_PRODUCT_SKEW=GCO_SKEW) ".
					" left join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code) ".
					" where GOD_ORDER_NO='$order_no' and gco_product_code!='391' $wh_cond and gft_skew_property not in (4,15,8) ".
					" and gop_sell_amt>0 ";
		}
	}
	return $query;
}
/**
 * @param string $title
 * @param string $body_content
 * @param string $modal_id
 * 
 * @return void
 */
function open_popup_module($title,$body_content='',$modal_id=''){
	global $base_relative_path;
	$brp = $base_relative_path;
	echo include_css_file("{$brp}CSS/loader.css");
	echo include_css_file("{$brp}CSS/popup_model.css");
	if($body_content=='') {
		$body_content = <<<END
		<table id="popup-modal-table">
			<thead id="modal_table_header">	
			</thead>
			<tbody style="max-height: 300px;overflow-y: auto;" id="modal_table_body">								
			</tbody>
		</table>
END;
	}
	$modal_id = ($modal_id!=''?" id='$modal_id' ":'');
	echo<<<END
	<div id="show_content_in_modal" style="display: none;font-family:open sans;">		
		<div class="popup-modal-container">
			<div class='out-loader hide' style="z-index:10000;">
				<div class="loader"><span></span><span></span><span></span></div>
			</div>
			<div class="popup-modal" $modal_id>
				<div class="popup-modal-title">
					$title
				</div>
				<div class='close-wrapper'>
					<button type="button" onclick='closePopupModal();' class="close popup-close-btn" aria-label="Close"><span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="alert-box" id="alert_message"> 		
 				</div>
				$body_content
			</div>
		</div>	
	</div>
END;
}
/**
 * 
 * @param string $addon_pcode
 * @param string $addon_pskew
 * @param string $partner_lead_code
 * @return string[int][string]
 */
function get_cust_dtls_for_addon($addon_pcode,$addon_pskew,$partner_lead_code) {
	$cust_list_qry = <<<END
select gcp_lead_code,glh_cust_name,gcp_price from gft_cust_addon_price_list join gft_lead_hdr on (gcp_lead_code=glh_lead_code)
where gcp_product_code='$addon_pcode' and gcp_product_skew='$addon_pskew' and (glh_lead_type='3' or (glh_lead_type='1' 
and GLH_LEAD_SOURCECODE!='36')) and GCP_PARTNER_LEAD_CODE='$partner_lead_code'
END;
	$cust_list_res = execute_my_query($cust_list_qry);
	$addon_cust_list = /*.(string[int][string]).*/array();
	while($cust_row = mysqli_fetch_array($cust_list_res)) {
		$leadcode = $cust_row['gcp_lead_code'];
		$custname = $cust_row['glh_cust_name'];
		$price = $cust_row['gcp_price'];
		$addon_cust_list[] = array('cust_lead_code'=>$leadcode,'cust_name'=>$custname,'price'=>$price);
	}
	return $addon_cust_list;
}
/**
 * @param string $leadcode
 * @return string[int][string]
 */
function get_addon_prods_for_partner($leadcode) {
	$products_qry = <<<END
select pm.gpm_product_code,pm.gpm_product_skew,pm.gpm_skew_desc from gft_cp_info 
join gft_price_tag_master pt on (GPL_PRICE_LIST_ID=cgi_price_list_id and GPL_STATUS='A') 
join gft_price_tag_detail pd  on (pt.gpl_tag_id=pd.gpl_tag_id) 
join gft_product_master pm on (pd.gpl_product_code=pm.gpm_product_code and pd.gpl_product_skew=pm.gpm_product_skew) 
join gft_product_family_master pfm on (pm.gpm_product_code=pfm.gpm_product_code)
where pfm.GPM_IS_INTERNAL_PRODUCT='2' and pfm.GPM_STATUS='A' and pfm.GPM_ADDON_CATEGORY!='0' and cgi_lead_code='$leadcode'
and GPL_TAGGED_PRICE!=0
END;
	$products_res = execute_my_query($products_qry);
	$prods = /*.(string[int][string]).*/array();
	while($row = mysqli_fetch_array($products_res)) {
		$curr_prod = array();
		$curr_prod['pcode'] = $row['gpm_product_code'];
		$curr_prod['pskew'] = $row['gpm_product_skew'];
		$curr_prod['prod_name'] = $row['gpm_skew_desc'];
		$prods[] = $curr_prod;
	}
	return $prods;
}
/**
 * @param string $pcode
 * @param string $pskew
 * @param string $cust_Code
 * @return string[string]
 */
function get_last_order_no($pcode,$pskew,$cust_Code) {
	$order_no_qry = <<<END
select last_order, ord_date from (select ifnull(gac_order_no,'') last_order,ifnull(god_order_date,'1970-01-01') ord_date 
from gft_add_on_commission_dtl join gft_order_hdr on (god_order_no=gac_order_no) 
where god_order_date in (select max(god_order_date) from gft_add_on_commission_dtl join gft_order_hdr on (god_order_no=gac_order_no) 
where gac_product_code='$pcode' and gac_product_skew='$pskew' and god_lead_code='$cust_Code' and god_order_date>='2018-03-01') 
and god_lead_code='$cust_Code') dtls 
union all 
select last_order, ord_date from (select ifnull(gac_order_no,'') last_order,ifnull(date(gph_order_date),'1970-01-01') ord_date 
from gft_add_on_commission_dtl join gft_proforma_hdr on (gac_order_no=gph_order_no) 
where gph_order_date in (select max(gph_order_date) from gft_add_on_commission_dtl 
join gft_proforma_hdr on (gac_order_no=gph_order_no) where gac_product_code='$pcode' and gac_product_skew='$pskew' and 
gph_lead_code='$cust_Code' and gph_order_date>='2018-03-01' and gph_converted_order_no!='' and gph_converted_order_no is not null) 
and gph_lead_code='$cust_Code') dtls1	
END;
	$order_no_res = execute_my_query($order_no_qry);
	$res = /*.(string[string]).*/array();
	$order_no = $ord_dt = '';
	while($row = mysqli_fetch_array($order_no_res)) {
		if($row['ord_date']=='') {
			continue;
		}
		if($order_no!='') {
			if(strtotime($row['ord_date'])>=strtotime($ord_dt)) {
				$order_no = $row['last_order'];
				$ord_dt = $row['ord_date'];
			}
		} else {
			$order_no = $row['last_order'];
			$ord_dt = $row['ord_date'];
		}
	}
	$res['order_no'] = $order_no;
	$res['order_date'] = $ord_dt;
	return $res;
}

/**
 * @param string $pcode
 * @param string $pskew
 * @param string $cust_Code
 * @return mixed[]
 */
function get_customer_price_for_addon($pcode,$pskew,$cust_Code) {
	$last_order = get_last_order_no($pcode,$pskew,$cust_Code);
	$last_order_no = $last_order['order_no'];
	$last_order_dt = $last_order['order_date'];
	if($last_order_no=='') {
		return array();
	}
	$cust_qry = " select gac_lead_code,gac_outlet_lead_codes,glh_cust_name,glh_cust_streetaddr2,glh_lead_type, ".
				" ifnull(gcp_price,if(GAC_SELL_RATE is not null and GAC_SELL_RATE>0,GAC_SELL_RATE,-999)) gcp_price ".
				" from gft_add_on_commission_dtl join gft_lead_hdr on (gac_lead_code=glh_lead_code) ".
				" left join gft_cust_addon_price_list on (gac_lead_code=gcp_lead_code and gac_product_code=gcp_product_code ".
				" and gac_product_skew=gcp_product_skew and gcp_partner_lead_code='$cust_Code' ) ".
				" where gac_order_no='$last_order_no' ";
	$cust_res = execute_my_query($cust_qry);
	$cust_list = array();
	$present_custs = array();
	while($row = mysqli_fetch_array($cust_res)) {
		$main_cust = $row['gac_lead_code'];
		$lead_type = $row['glh_lead_type'];
		if(!in_array($main_cust,$present_custs)) {
			$main_cust_name = $row['glh_cust_name']." - ".$row['glh_cust_streetaddr2'];
			$outlets = $row['gac_outlet_lead_codes'];
			$rate = $row['gcp_price'];
			$present_custs[] = $main_cust;
			$outlets_arr = array();
			if($outlets!='') {
				$outlets_qry = "select glh_lead_code,glh_cust_name,glh_cust_streetaddr2 from gft_lead_hdr where glh_lead_code in ($outlets)";
				$outlets_res = execute_my_query($outlets_qry);
				while($out_row = mysqli_fetch_array($outlets_res)) {
					$lead_code = $out_row['glh_lead_code'];
					$cust_name = $out_row['glh_cust_name']." - ".$out_row['glh_cust_streetaddr2'];
					if(!in_array($lead_code,$present_custs)) {
						$outlets_arr[] = array("lead_code"=>$lead_code,"cust_name"=>$cust_name,"cust_from"=>"Renewal");
						$present_custs[] = $lead_code;
					}
					if($lead_code==$main_cust) {
						$outlets_arr[] = array("lead_code"=>$main_cust,"cust_name"=>$main_cust_name,"cust_from"=>"Renewal");
					}
				}
			} else {
				$cond = " and glh_lead_sourcecode='7' and glh_reference_given='$main_cust' ";
				if($lead_type=='1') {
					$cond = " and glh_lead_sourcecode='36' and glh_reference_given='$main_cust' ";
				}
				$outlets_qry = "select glh_lead_code,glh_cust_name,glh_cust_streetaddr2 from gft_lead_hdr where 1 $cond ";
				$outlets_res = execute_my_query($outlets_qry);
				while($out_row = mysqli_fetch_array($outlets_res)) {
					$lead_code = $out_row['glh_lead_code'];
					$cust_name = $out_row['glh_cust_name']." - ".$out_row['glh_cust_streetaddr2'];
					if(!in_array($lead_code,$present_custs)) {
						$outlets_arr[] = array("lead_code"=>$lead_code,"cust_name"=>$cust_name,"cust_from"=>"Outlet of customer");
						$present_custs[] = $lead_code;
					}
				}
				$outlets_arr[] = array("lead_code"=>$main_cust,"cust_name"=>$main_cust_name,"cust_from"=>"Main Customer");
			}
			$cust_list[] = array("cust_id"=>"$main_cust","cust_name"=>$main_cust_name,"price"=>$rate,"outlets"=>$outlets_arr);
		}
	}
	return $cust_list;
}
/**
 * @param string $pcode
 * @param string $pskew
 * @param string $partner_emp_id
 * @param string $from_dt
 * @param string $to_dt
 * @return mixed[]
 */
function get_cust_for_add_on_orders($pcode,$pskew,$partner_emp_id,$from_dt,$to_dt) {
	$partner_leadcode = get_single_value_from_single_table("cgi_lead_code", "gft_cp_info", "cgi_emp_id", $partner_emp_id);
	$from_dt = date('Y-m-01',strtotime($from_dt));
	$outlets = array();
	$qry = <<<END
select glh_lead_code,glh_cust_name,glh_cust_streetaddr2,glh_lead_type,glh_lead_sourcecode,glh_reference_given,
ifnull(gcp_price,gop_sell_rate) renew_price from gft_order_hdr 
join gft_order_product_dtl on (god_order_no=gop_order_no) 
left join gft_cp_order_dtl on (gco_order_no=gop_order_no and gco_product_code=gop_product_code and gco_skew=gop_product_skew) 
join gft_lead_hdr on (glh_lead_code=ifnull(gco_cust_code,god_lead_code)) 
left join gft_cust_addon_price_list on (GCP_LEAD_CODE=glh_lead_code and GCP_PRODUCT_CODE=gop_product_code and 
GCP_PRODUCT_SKEW=gop_product_skew and GCP_PARTNER_LEAD_CODE='$partner_leadcode') 
where gop_product_code='$pcode' and gop_product_skew='$pskew' and god_order_status!='C'
and god_order_date<='$to_dt' and (glh_lead_type='3' or (glh_lead_type='1' and GLH_LEAD_SOURCECODE!='36')) 
and god_emp_id='$partner_emp_id' 
END;
	// and god_order_date>='$from_dt'
	$res = execute_my_query($qry);
	$present_custs = array();
	$cust_list = array();
	$main_custs = array();
	$cust_prices = array();
	while($row = mysqli_fetch_array($res)) {
		$main_cust = $row['glh_lead_code'];
		$main_cust_name = $row['glh_cust_name']." - ".$row['glh_cust_streetaddr2'];
		$price = $row['renew_price'];
		if(!in_array($main_cust,$present_custs)) {
			$present_custs[] = $main_cust;
			$main_custs[$main_cust] = $main_cust_name;
			$cust_prices[$main_cust] = $price;
			$outlets[$main_cust][] = array("lead_code"=>$main_cust,"cust_name"=>$main_cust_name,"cust_from"=>"New Order");
		}
	}
	$outlet_orders_qry = <<<END
select glh_lead_code,glh_cust_name,glh_cust_streetaddr2,glh_reference_given,gop_sell_rate from gft_order_hdr 
join gft_order_product_dtl on (god_order_no=gop_order_no) 
left join gft_cp_order_dtl on (gco_order_no=gop_order_no and gco_product_code=gop_product_code and gco_skew=gop_product_skew) 
join gft_lead_hdr on (glh_lead_code=ifnull(gco_cust_code,god_lead_code)) 
where gop_product_code='$pcode' and gop_product_skew='$pskew' and god_order_status!='C' and god_order_date<='$to_dt' and 
((glh_lead_type='13' and GLH_LEAD_SOURCECODE='7' and glh_reference_given!='' and glh_reference_given 
is not null) or (glh_lead_type='1' and GLH_LEAD_SOURCECODE='36' and glh_reference_given!='' and glh_reference_given 
is not null)) and god_emp_id='$partner_emp_id' 
END;
	// and god_order_date>='$from_dt'
	$outlet_res = execute_my_query($outlet_orders_qry);
	while($row = mysqli_fetch_array($outlet_res)) {
		$main_cust = $row['glh_reference_given'];
		$lead_code = $row['glh_lead_code'];
		$cust_name = $row['glh_cust_name']." - ".$row['glh_cust_streetaddr2'];
		if(!isset($main_custs[$main_cust])) {
			$main_custs[$main_cust] = get_single_value_from_single_table("glh_cust_name", "gft_lead_hdr", "glh_lead_code", $main_cust);
			$cust_prices[$main_cust] = $row['gop_sell_rate'];
			$no_order_price_qry = <<<END
select ifnull(gcp_price,0) gcp_price from gft_cust_addon_price_list where gcp_product_code='$pcode' and gcp_product_skew='$pskew' 
and gcp_partner_lead_code='$partner_leadcode' and gcp_lead_code='$main_cust'
END;
			$no_order_price_res = execute_my_query($no_order_price_qry);
			if($p_row = mysqli_fetch_assoc($no_order_price_res)) {
			    if(intval($p_row['gcp_price'])>0) {
			        $cust_prices[$main_cust] = $p_row['gcp_price'];
			    }
			}
		}
		$outlets[$main_cust][] = array("lead_code"=>$lead_code,"cust_name"=>$cust_name,"cust_from"=>"New Order");
	}
	foreach($main_custs as $cust_id=>$custname) {
		$cust_list[] = array("cust_id"=>"$cust_id","cust_name"=>$custname,"price"=>$cust_prices["$cust_id"],"outlets"=>(isset($outlets["$cust_id"])?$outlets["$cust_id"]:array()));
	}
	return $cust_list;
}
/**
 * @param string $pcode
 * @param string $pskew
 * @param string $total_rate
 * @param string $lead_code
 * @param string $emp_id
 * @param string $proforma_no
 * @param string $approval_status
 * @param string $approval_remarks
 * @param string $emails
 * @param string $utr_no
 * @return string
 */
function generate_proforma($pcode,$pskew,$total_rate,$lead_code,$emp_id,$proforma_no='',$approval_status='1',$approval_remarks='',$emails='',$utr_no='') {
	if($proforma_no!='' and $proforma_no!='-1' and $proforma_no!='0') {
		$proforma_dt = get_single_value_from_single_table("gph_order_date", "gft_proforma_hdr", "gph_order_no", $proforma_no);
		$version_no = (int)get_single_value_from_single_table("GPH_VERSION_NO", "gft_proforma_hdr", "gph_order_no", $proforma_no) + 1;
	} else {
		$proforma_no = get_profroma_no(date('Y-m-d'), $emp_id);
		$proforma_dt = date('Y-m-d H:i:s');
		$version_no = 1;
	}
	if($emails=='') {
	    $emails = get_single_value_from_single_table("GPH_PROFORMA_TO_EMAILS", "gft_proforma_hdr", "gph_order_no", $proforma_no);
	}
	$cgst_per = $sgst_per = $igst_per = '0.0';
	$cgst_amt = $sgst_amt = $igst_amt = '0.0';
	if(is_same_state($lead_code)) {
		$cgst_per = '9.0';
		$sgst_per = '9.0';
		$cgst_amt = round((float)$total_rate*0.09,2);
		$sgst_amt = round((float)$total_rate*0.09,2);
	} else {
		$igst_per = '18.0';
		$igst_amt = round((float)$total_rate*0.18,2);
	}
	$net_amt = round(((float)$total_rate + $cgst_amt + $sgst_amt + $igst_amt),2);
	$hdr_arr = array();
	$hdr_arr["GPH_ORDER_NO"] = $proforma_no;
	$hdr_arr["GPH_LEAD_CODE"] = $lead_code;
	$hdr_arr["GPH_IS_SAME_STATE"] = (is_same_state($lead_code)?'1':'0');
	if($version_no==1) {
		$hdr_arr["GPH_EMP_ID"] = $emp_id;
		$hdr_arr["GPH_APPROVEDBY_EMPID"] = $emp_id;
		$hdr_arr["GPH_ORDER_DATE"] = $proforma_dt;
		$hdr_arr["GPH_CREATED_DATE"] = $proforma_dt;
	}
	$hdr_arr["GPH_ORDER_AMT"] = $net_amt;
	$hdr_arr["GPH_APPROVAL_CODE"] = '0';
	$hdr_arr["GPH_ORDER_STATUS"] = 'A';
	$hdr_arr["GPH_REMARKS"] = "Alliance Partner Add-on Commission";
	$hdr_arr["GPH_VERSION_NO"] = "$version_no";
	$hdr_arr["GPH_CURRENCY_CODE"] = 'INR';
	$hdr_arr["GPH_TAX_MODE"] = '3';
	$hdr_arr["GPH_TYPE"] = '5';
	$hdr_arr["GPH_VALIDITY_DATE"] = date('Y-m-d',strtotime("$proforma_dt +1 month"));
	$hdr_arr["GPH_STATUS"] = 'N';
	$hdr_arr["GPH_PROFORMA_TO"] = get_single_value_from_single_table("glh_cust_name","gft_lead_hdr","glh_lead_code",$lead_code);
	$hdr_arr["GPH_CONVERTED_ORDER_NO"] = '';
	$hdr_arr["GPH_APPROVAL_STATUS"] = $approval_status;
	$hdr_arr["GPH_APPROVED_DATE"] = '';
	$hdr_arr["GPH_PROFORMA_TO_EMAILS"] = $emails;
	if(intval($emp_id)>7000 and !in_array($emp_id,array('9999','9998'))) {
	    $hdr_arr["GPH_CUST_UTR"] = $utr_no;
	    $hdr_arr["GPH_APPROVAL_REMARKS"] = $approval_remarks;
	}
	$hdr_res = array_update_tables_common($hdr_arr,"gft_proforma_hdr",array("gph_order_no"=>$proforma_no),null,$emp_id,null,null,$hdr_arr);
	if($hdr_res) {
		$prod_arr = array();
		$prod_arr["GPP_ORDER_NO"] = $proforma_no;
		$prod_arr["GPP_PRODUCT_CODE"] = $pcode;
		$prod_arr["GPP_PRODUCT_SKEW"] = $pskew;
		$prod_arr["GPP_SELL_RATE"] = $total_rate;
		$prod_arr["GPP_SER_TAX_RATE"] = '0';
		$prod_arr["GPP_TAX_RATE"] = '0';
		$prod_arr["GPP_QTY"] = '1';
		$prod_arr["GPP_DISCOUNT_AMT"] = '0';
		$prod_arr["GPP_SELL_AMT"] = $net_amt;
		$prod_arr["GPP_SER_TAX_AMT"] = '0';
		$prod_arr["GPP_TAX_AMT"] = '0';
		$prod_arr["GPP_LIST_PRICE"] = $total_rate;
		$prod_arr["GPP_PRINT_ORDER"] = '1';
		$prod_arr["GPP_CGST_PER"] = $cgst_per;
		$prod_arr["GPP_SGST_PER"] = $sgst_per;
		$prod_arr["GPP_IGST_PER"] = $igst_per;
		$prod_arr["GPP_CESS_PER"] = '';
		$prod_arr["GPP_CESS_AMT"] = '';
		$prod_arr["GPP_IGST_AMT"] = $igst_amt;
		$prod_arr["GPP_SGST_AMT"] = $sgst_amt;
		$prod_arr["GPP_CGST_AMT"] = $cgst_amt;
		$prods_res = array_update_tables_common($prod_arr,"gft_proforma_product_dtl",array("gpp_order_no"=>$proforma_no,"gpp_product_code"=>$pcode,"gpp_product_skew"=>$pskew),null,$emp_id,null,null,$prod_arr);
		if($prods_res) {
			return $proforma_no;
		} else {
			return '-1';
		}
	} else {
		return '-1';
	}
}
/**
 * @param string $table_name
 * @param string $ref_no
 * @param string $receipt_id
 * @param string $product_code
 * @param string $pro_skew
 * @param string $column_name
 * @param string $old_value
 * @param string $new_value
 * @param string $updated_by
 * @param string $updated_on
 * @param string $script_name
 * @return void
 */
function do_audit_entry($table_name,$ref_no,$receipt_id,$product_code,$pro_skew,$column_name,$old_value,$new_value,$updated_by,$updated_on,$script_name){
	$audit_query= "insert into gft_audit_viewer_order (GAV_TABLE_NAME, GAV_ORDER_NO,GAV_RECEIPT_ID,GAV_PRODUCT_CODE,GAV_PRODUCT_SKEW, GAV_COLUMN_NAME, GAV_PREVIOUS_VALUE, GAV_UPDATED_VALUE, GAV_UPDATED_DATETIME, GAV_UPDATED_BY, GAV_FROM_PAGE)" .
			" values ('$table_name','$ref_no','$receipt_id','$product_code','$pro_skew','$column_name','$old_value','$new_value','$updated_on','$updated_by','$script_name') ";
	execute_my_query($audit_query);
}
/**
 * @param string $proforma_no
 * @param string $prod_code
 * @param string $prod_skew
 * @param mixed[string] $custs
 * @param string $from_dt
 * @param string $to_dt
 * @return boolean
 */
function insert_add_on_commission_dtls($proforma_no,$prod_code,$prod_skew,$custs,$from_dt,$to_dt) {
	global $uid;
	$proforma_version_no = get_single_value_from_single_table("gph_version_no", "gft_proforma_hdr", "gph_order_no", $proforma_no);
	$curr_dtls = array();
	if((int)$proforma_version_no>1) {
		$curr_dtls_qry = " select gac_id,gac_lead_code,gac_sell_rate,gac_outlet_lead_codes,gac_start_date,gac_end_date,gac_months ".
						 " from gft_add_on_commission_dtl where gac_order_no='$proforma_no' ";
		$curr_dtls_res = execute_my_query($curr_dtls_qry);
		while($row = mysqli_fetch_array($curr_dtls_res)) {
			$lead_code = $row['gac_lead_code'];
			$old_outlets = $row['gac_outlet_lead_codes'];
			$old_sell_rate = $row['gac_sell_rate'];
			$start_dt = $row['gac_start_date'];
			$end_dt = $row['gac_end_date'];
			$id = $row['gac_id'];
			$mn = $row['gac_months'];
			$curr_dtls[$lead_code] = array("id"=>$id,"outlets"=>$old_outlets,"price"=>$old_sell_rate,'start_dt'=>$start_dt,'end_dt'=>$end_dt,'months'=>$mn);
		}
	}
	$all_lead_codes = array();
	$dttime = date('Y-m-d H:i:s');
	$res = false;
	foreach ($custs as $dtl_row) {
		$lead_code = $dtl_row['lead_code'];
		$all_lead_codes[] = $lead_code;
		$months = (date('m',strtotime($to_dt))-date('m',strtotime($from_dt)))+1;
		$outlets = $dtl_row['outlets'];
		$rate = $dtl_row['price'];
		$ins_arr = /*.(mixed[string]).*/array();
		$ins_arr["GAC_ORDER_NO"] = $proforma_no;
		$ins_arr["GAC_ORDER_REF_TYPE"] = '2';
		$ins_arr["GAC_LEAD_CODE"] = $lead_code;
		$ins_arr["GAC_PRODUCT_CODE"] = $prod_code;
		$ins_arr["GAC_PRODUCT_SKEW"] = $prod_skew;
		$ins_arr["GAC_MONTHS"] = $months;
		$ins_arr["GAC_OUTLETS"] = count($outlets);
		$ins_arr["GAC_OUTLET_LEAD_CODES"] = implode(",",$outlets);
		$ins_arr["GAC_SELL_RATE"] = $rate;
		$ins_arr["GAC_START_DATE"] = $from_dt;
		$ins_arr["GAC_END_DATE"] = $to_dt;
		$ins_res = array_update_tables_common($ins_arr, "gft_add_on_commission_dtl", array('gac_order_no'=>$proforma_no,'gac_lead_code'=>$lead_code),null,$uid,null,null,$ins_arr);
		if((int)$proforma_version_no>1) {
			if(isset($curr_dtls[$lead_code])) {
				$id = $curr_dtls[$lead_code]['id'];
				if(implode(",",$outlets)!=$curr_dtls[$lead_code]['outlets']) {
					$old_outlet_count = count(explode(",",$curr_dtls[$lead_code]['outlets']));
					do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_OUTLETS',"$old_outlet_count",count($outlets),$uid,$dttime,"alliance_partner_data.php");
					do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_OUTLET_LEAD_CODES',$curr_dtls[$lead_code]['outlets'],implode(",",$outlets),$uid,$dttime,"alliance_partner_data.php");
					$res = true;
				}
				if($rate!=$curr_dtls[$lead_code]['price']) {
					do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_SELL_RATE',$curr_dtls[$lead_code]['price'],$rate,$uid,$dttime,"alliance_partner_data.php");
					$res = true;
				}
				if($from_dt!=$curr_dtls[$lead_code]['start_dt'] or $to_dt!=$curr_dtls[$lead_code]['end_dt']) {
					if($from_dt!=$curr_dtls[$lead_code]['start_dt']) {
						do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_START_DATE',$curr_dtls[$lead_code]['start_dt'],$from_dt,$uid,$dttime,"alliance_partner_data.php");
					}
					if($to_dt!=$curr_dtls[$lead_code]['end_dt']) {
						do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_END_DATE',$curr_dtls[$lead_code]['end_dt'],$to_dt,$uid,$dttime,"alliance_partner_data.php");
					}
					do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_MONTHS',$curr_dtls[$lead_code]['months'],$months,$uid,$dttime,"alliance_partner_data.php");
					$res = true;
				}
			} else {
				$id_qry = execute_my_query("select gac_id from gft_add_on_commission_dtl where gac_order_no='$proforma_no' and gac_lead_code='$lead_code'");
				$id = '';
				if($id_row = mysqli_fetch_array($id_qry)) {
					$id = $id_row['gac_id'];
				}
				do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_OUTLETS',"",count($outlets),$uid,$dttime,"alliance_partner_data.php");
				do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_OUTLET_LEAD_CODES',"",implode(",",$outlets),$uid,$dttime,"alliance_partner_data.php");
				do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_SELL_RATE',"",$rate,$uid,$dttime,"alliance_partner_data.php");
				do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_START_DATE',"",$from_dt,$uid,$dttime,"alliance_partner_data.php");
				do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_END_DATE',"",$to_dt,$uid,$dttime,"alliance_partner_data.php");
				do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_MONTHS',"",$months,$uid,$dttime,"alliance_partner_data.php");
				$res = true;
			}
		} else {
			$res = true;
		}
	}
	foreach(array_keys($curr_dtls) as $present_leads) {
		if(!in_array($present_leads,$all_lead_codes)) {
			$id = $curr_dtls["$present_leads"]['id'];
			execute_my_query("delete from gft_add_on_commission_dtl where gac_order_no='$proforma_no' and gac_lead_code='$present_leads'");
			do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_OUTLETS',count(explode(",",$curr_dtls["$present_leads"]['outlets'])),"",$uid,$dttime,"alliance_partner_data.php");
			do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_OUTLET_LEAD_CODES',$curr_dtls["$present_leads"]['outlets'],"",$uid,$dttime,"alliance_partner_data.php");
			do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_SELL_RATE',$curr_dtls["$present_leads"]['price'],"",$uid,$dttime,"alliance_partner_data.php");
			do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_START_DATE',$curr_dtls["$present_leads"]['start_dt'],"",$uid,$dttime,"alliance_partner_data.php");
			do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_END_DATE',$curr_dtls["$present_leads"]['end_dt'],"",$uid,$dttime,"alliance_partner_data.php");
			do_audit_entry("gft_add_on_commission_dtl",$id,$proforma_version_no,'','','GAC_MONTHS',$curr_dtls["$present_leads"]['months'],"",$uid,$dttime,"alliance_partner_data.php");
			$res = true;
		}
	}
	$proforma_content = generate_quotation_proforma_content($proforma_no,'proforma');
	$html_file_path = write_html_file_for_pdf($proforma_content, 'proforma', "Pro_$proforma_no.html");
	$f_name = str_replace(".html", ".pdf", "Pro_$proforma_no.html");
	generate_pdf($html_file_path);
	return $res;
}
/**
 * @param string $partner_cust_id
 * @param string $pcode
 * @param string $pskew
 * @param string $from_date
 * @param string $to_date
 * @return string[string]
 */
function get_active_proforma_for_partner_addon($partner_cust_id,$pcode,$pskew,$from_date,$to_date) {
    global $uid;
	$pi_no = '-1';
	$pi_status = '';
	$renewal_start_dt = '';
	$renewal_end_dt = '';
	$approval_remarks = '';
	$utr_no = '';
	$pi_chk_qry = <<<END
select gph_order_no,gph_approval_status,gac_end_date,gac_start_date,ifnull(gph_cust_utr,'') gph_cust_utr,
ifnull(gph_approval_remarks,'') gph_approval_remarks from gft_proforma_hdr 
join gft_add_on_commission_dtl on (gac_order_no=gph_order_no and gac_order_ref_type='2') 
where gph_lead_code='$partner_cust_id' and gac_product_code='$pcode' and gac_product_skew='$pskew' and gph_order_status='A'
and (gph_converted_order_no='' or gph_converted_order_no is null)
END;
	if($from_date!='' or $to_date!='') {
	    $pi_chk_qry .= " and (";
	    if($from_date!='') {
	        $pi_chk_qry .= " (GAC_START_DATE<='$from_date' and GAC_END_DATE>='$from_date') ";
        }
        if($to_date!='') {
            $to_date = date('Y-m-01',strtotime($to_date." 00:00:00"));
            $pi_chk_qry .= (($from_date!=""?" or ":"")." (GAC_START_DATE<='$to_date' and GAC_END_DATE>='$to_date') ");
        }
        $pi_chk_qry .= ")";
	}
	$pi_chk_qry .= " group by gph_order_no";
	$pi_check_res = execute_my_query($pi_chk_qry);
	$pi_dtls = array();
	if(mysqli_num_rows($pi_check_res)>0) {
    	while($row = mysqli_fetch_array($pi_check_res)) {
    		$pi_no = $row['gph_order_no'];
    		$pi_status = $row['gph_approval_status'];
    		$renewal_start_dt = $row['gac_start_date'];
    		$renewal_end_dt = $row['gac_end_date'];
    		$approval_remarks = $row['gph_approval_remarks'];
    		$utr_no = $row['gph_cust_utr'];
    		$pi_dtls[] = array('pi_no'=>$pi_no,'approval_status'=>$pi_status,'renewal_start_date'=>$renewal_start_dt,'renewal_end_date'=>$renewal_end_dt,'approval_remarks'=>$approval_remarks,'utr_no'=>$utr_no);
    	}
	} else if(intval($uid)<7000) {
	    $pi_dtls[] = array('pi_no'=>$pi_no,'approval_status'=>$pi_status,'renewal_start_date'=>$renewal_start_dt,'renewal_end_date'=>$renewal_end_dt,'approval_remarks'=>$approval_remarks,'utr_no'=>$utr_no);
	}
	return $pi_dtls;
}
/**
 * @param string $pi_no
 * @return mixed[]
 */
function get_selected_outlet_dtls($pi_no) {
	$qry = <<<END
select gac_lead_code,gac_sell_rate,gac_outlet_lead_codes,gpp_cgst_amt,gpp_sgst_amt,gpp_igst_amt,gpp_sell_rate,gph_order_amt 
from gft_add_on_commission_dtl join gft_lead_hdr on (glh_lead_code=gac_lead_code) 
join gft_proforma_hdr on (gac_order_no=gph_order_no) join gft_proforma_product_dtl on (gpp_order_no=gph_order_no) 
where gph_order_no='$pi_no'
END;
	$res = execute_my_query($qry);
	$selected_dtls = array();
	$selected_dtls['customers'] = array();
	$custs = array();
	$grand_total = '0';
	$cgst = '0';
	$sgst = '0';
	$igst = '0';
	$sell_rate = '0';
	while($row = mysqli_fetch_array($res)) {
		$cust_id = $row['gac_lead_code'];
		$price = $row['gac_sell_rate'];
		$outlets = $row['gac_outlet_lead_codes'];
		$grand_total = $row['gph_order_amt'];
		$cgst = $row['gpp_cgst_amt'];
		$sgst = $row['gpp_sgst_amt'];
		$igst = $row['gpp_igst_amt'];
		$sell_rate = $row['gpp_sell_rate'];
		$custs[$cust_id] = array('price'=>$price,'outlets'=>explode(",",$outlets));
	}
	$selected_dtls['customers'] = $custs;
	$selected_dtls['grand_total'] = $grand_total;
	$selected_dtls['net_rate'] = $sell_rate;
	$selected_dtls['cgst'] = $cgst;
	$selected_dtls['sgst'] = $sgst;
	$selected_dtls['igst'] = $igst;
	return $selected_dtls;
}
/**
 * @return string[string]
 */
function get_roles() {
	$roles_master = /*.(string[string]).*/array();
	$all_roles_qry = execute_my_query("select grm_id,grm_name from gft_campus_roles_master");
	while($roles_row = mysqli_fetch_array($all_roles_qry)) {
		$roles_master[$roles_row['grm_id']] = $roles_row['grm_name'];
	}
	return $roles_master;
}
/**
 * 
 * @return string[string]
 */
function get_departments() {
	
	$department_master = /*.(string[string]).*/array();
	$all_department_qry = execute_my_query("select GDM_DEPT_ID,GDM_DEPT_NAME,GDM_DEPT_DESC from gft_career_dept_master");
	while($dept_row = mysqli_fetch_array($all_department_qry)) {
		$department_master[$dept_row['GDM_DEPT_ID']] = $dept_row['GDM_DEPT_NAME'];
	}
	return $department_master;
	
}
/**
 *
 * @return string[string]
 */
function  get_testimonial ()  {
	$testimonial_master = /*.(string[string]).*/array();
	$all_testimonial_qry = execute_my_query("select GDM_DEPT_ID,GDM_DEPT_NAME from gft_career_dept_master");
	while($test_row = mysqli_fetch_array($all_testimonial_qry)) {
		$testimonial_master[$test_row['GDM_DEPT_ID']] = $test_row['GDM_DEPT_NAME'];
	}
	$all_testimonial_qry = execute_my_query("select GEM_EMP_ID,GEM_EMP_name from gft_emp_master where gem_emp_id<7000 and gem_status='A' order by gem_emp_name");
	while($test_row = mysqli_fetch_array($all_testimonial_qry)) {
		$testimonial_master[$test_row['GEM_EMP_ID']] = $test_row['GEM_EMP_name'];
	}
	return $testimonial_master;
}
/**
 * @param string $column_name
 * @param string $contact_num
 * @return string
 */
function get_contact_num_where($column_name,$contact_num) {
	$wh_cond = " ( $column_name like '$contact_num%' or $column_name like '0$contact_num%' or ".
			   "  $column_name like '00$contact_num%' or $column_name like '91$contact_num%' ";
	$trimmed_num = ltrim(ltrim($contact_num,'00'),'0');
	$wh_cond .= " or $column_name like '$trimmed_num%' or $column_name like '0$trimmed_num%' or ".
				" $column_name like '00$trimmed_num%' or $column_name like '91$trimmed_num%' ";
	if(strlen($contact_num)>10) {
		$c_num = substr($contact_num,-10);
		$wh_cond .= " or $column_name like '$c_num%' or $column_name like '0$c_num%' or $column_name like '00$c_num%' ".
					" or $column_name like '91$c_num%' ";
	}
	$wh_cond .= " ) ";
	return $wh_cond;
}

/**
 * @param string $cust_id
 * @param string $partner_lead_code
 * @param string $partner_emp_id
 * @param string $source_code
 * 
 * @return boolean
 */
function check_partner_lead_source($cust_id,$partner_lead_code='', $partner_emp_id='',$source_code='37'){
	if($partner_lead_code!=''){
		$sql1 = " select GLH_LEAD_CODE from gft_lead_hdr where GLH_LEAD_CODE='$cust_id' ".
				" and GLH_LEAD_SOURCE_CODE_PARTNER='$source_code' and GLH_REFERENCE_OF_PARTNER='$partner_lead_code' ";
	}else if($partner_emp_id!=''){
		$sql1 = " select GLH_LEAD_CODE from gft_lead_hdr ".
				" join gft_cp_info on (CGI_LEAD_CODE=GLH_REFERENCE_OF_PARTNER) ".
				" where GLH_LEAD_CODE='$cust_id' and GLH_LEAD_SOURCE_CODE_PARTNER='$source_code' ".
				" and CGI_EMP_ID='$partner_emp_id' ";
	}else{
		return false;		
	}
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $cust_id
 * @param string $mobile_no
 * 
 * @return boolean
 */
function send_otp_to_mobile($cust_id,$mobile_no){
	if(!check_can_send_sms($mobile_no) || !is_valid_lead_code($cust_id)){
		return false;
	}
	$new_otp = generate_OTP(5);
	execute_my_query("UPDATE gft_otp_master SET GOM_OTP_STATUS='I' WHERE GOM_LEAD_CODE='$cust_id' and GOM_OTP_SMS_TO='$mobile_no' ");
	$ins_arr = array(
			'GOM_LEAD_CODE'=>$cust_id,'GOM_OTP_SMS_TO'=>$mobile_no,
			'GOM_OTP'=>$new_otp,'GOM_OTP_STATUS'=>'A','GOM_GEN_DATE_TIME'=>date('Y-m-d H:i:s')
	);
	array_insert_query("gft_otp_master", $ins_arr);
	$template_id = 143;
	$sms_content = get_formatted_content(array('OTP'=>array($new_otp)),$template_id);
	entry_sending_sms_to_customer($mobile_no,$sms_content,$template_id,$cust_id,1,'9999',0,null,true);
	return true;
}
/**
 * @param string $lead_code
 * @param string $uid
 * 
 * @return string[string]
 */
function get_rsm_of_lead($lead_code, $uid){
	$rsm_qry =<<<END
	select em1.gem_emp_id approval_emp,em1.gem_emp_name approval_emp_name,em1.gem_email approval_emp_email
	from gft_lead_hdr left join b_map_view on (terr_id=glh_territory_id)
	join gft_emp_territory_dtl on (region_id=get_territory_id and get_work_area_type='4' and get_status='A')
	left join gft_emp_master em1 on (em1.gem_emp_id=get_emp_id and em1.gem_role_id='6')
	where glh_lead_code='$lead_code' and em1.gem_status='A' and glh_territory_id!='100'
END;
	$rsm_res = execute_my_query($rsm_qry);
	$rsm_id = $rsm_name = $rsm_mail ='';
	if($rsm_row = mysqli_fetch_array($rsm_res)) {
		$rsm_id = $rsm_row['approval_emp'];
		$rsm_name = $rsm_row['approval_emp_name'];
		$rsm_mail = $rsm_row['approval_emp_email'];
	} else {
		$rm_qry =<<<END
		select em2.gem_emp_id approval_emp,em2.gem_emp_name approval_emp_name,em2.gem_email approval_emp_email from gft_emp_reporting
		join gft_emp_master em2 on (em2.gem_emp_id=GER_REPORTING_EMPID) where GER_EMP_ID='$uid' and GER_STATUS='A'
END;
		$rm_res = execute_my_query($rm_qry);
		$rsm_id = $rsm_name = '';
		if($rm_row = mysqli_fetch_array($rm_res)) {
			$rsm_id = $rm_row['approval_emp'];
			$rsm_name = $rm_row['approval_emp_name'];
			$rsm_mail = $rm_row['approval_emp_email'];
		}
	}
	$rsm_dtl['emp_id']="$rsm_id";
	$rsm_dtl['emp_name']="$rsm_name";
	$rsm_dtl['emp_email']="$rsm_mail";
	return $rsm_dtl;
}
/**
 * @param string $complaint_id
 * @param int[int] $status_array
 * @return void
 */
function update_welcome_status($complaint_id,$status_array=null) {
	global $uid;
	if(!is_array($status_array) or count($status_array)==0) {
		$audit_types_qry = execute_my_query(" select gat_audit_id from gft_audit_type_master ".
					   					    " join gft_audit_question_group_map_master on (GAQ_AUDIT_ID=GAT_AUDIT_ID) ".
											" where GAQ_QGROUP_ID='25' ");
		while($row = mysqli_fetch_array($audit_types_qry)) {
			$status_array[(int)$row['gat_audit_id']] = 1;
		}
	}
	foreach ($status_array as $audit_type=>$status) {
		$dtl_arr = array();
		$dtl_arr['GWS_SUPPORT_ID'] = $complaint_id;
		$dtl_arr['GWS_AUDIT_TYPE_ID'] = $audit_type;
		$dtl_arr['GWS_STATUS'] = $status;
		array_update_tables_common($dtl_arr,"gft_cst_welcome_status",array("gws_support_id"=>$complaint_id,"gws_audit_type_id"=>$audit_type),null,$uid,null,null,$dtl_arr);
	}
}
/**
 * @param int $audit_type
 * @param boolean $only_mandatory
 * @return int[int]
 */
function get_questions_in_audit_type($audit_type,$only_mandatory) {
	$qry = " select gaq_question_id from gft_audit_question_master where gaq_audit_type='$audit_type' and gaq_status='A' ";
	if($only_mandatory) {
		$qry .= " and GAQ_ANS_MANDITORY='Y' ";
	}
	$res = execute_my_query($qry);
	$qns = /*.(int[int]).*/array();
	while($row = mysqli_fetch_array($res)) {
		$qns[] = (int)$row['gaq_question_id'];
	}
	return $qns;
}
/**
 * @param string $support_id
 * @param string $audit_type_id
 * @return boolean
 */
function check_is_audit_complete($support_id,$audit_type_id) {
	$qns = get_questions_in_audit_type($audit_type_id, true);
	$chk_qry = execute_my_query(" select gwd_response from gft_cst_welcome_dtl where GWD_QUESTION_ID in ('".implode("','",$qns)."') ".
			   					" and gwd_support_id='$support_id' ");
	if(mysqli_num_rows($chk_qry)==count($qns)) {
		return true;
	}
	return false;
}
/**
 * @param string $support_id
 * @return boolean
 */
function update_cst_support_status($support_id) {
	global $uid;
	$audit_types_qry = execute_my_query("select gaq_audit_id from gft_audit_question_group_map_master where GAQ_QGROUP_ID='25'");
	$audit_type = array();
	while($row = mysqli_fetch_array($audit_types_qry)) {
		$audit_type[] = $row['gaq_audit_id'];
	}
	$completed = true;
	foreach ($audit_type as $type_id) {
		$chk_qry = execute_my_query("select gws_status from gft_cst_welcome_status where gws_support_id='$support_id' and gws_audit_type_id='$type_id'");
		if($row = mysqli_fetch_array($chk_qry)) {
			if($row['gws_status']!='3') {
				$completed = false;
				break;
			}
		}
	}
	if(mysqli_num_rows($audit_types_qry)==0) {
		$completed = false;
	}
	if($completed) {
		$support_dtls_qry = "select gch_lead_code,gch_product_code,gch_product_skew from gft_customer_support_hdr where gch_complaint_id='$support_id'";
		$support_dtls_res = execute_my_query($support_dtls_qry);
		$lead_code = '';$productcode = '';$productskew = '';
		if($row = mysqli_fetch_assoc($support_dtls_res)) {
			$lead_code = $row['gch_lead_code'];
			$productcode = $row['gch_product_code'];
			$productskew = $row['gch_product_skew'];
		}
		insert_support_entry($lead_code,$productcode,$productskew,'','',$uid,'0',"CST Welcome call activity completed",
							 168,'T1',null,null,$uid,null,'4',null,false,'',null,$support_id);
	}
	return $completed;
}
/**
 * @return string[string]
 */
function get_months_of_an_yr() {
    return  array(
        "01" => "JANUARY", "02" => "FEBRUARY", "03" => "MARCH", "04" => "APRIL",
        "05" => "MAY", "06" => "JUNE", "07" => "JULY", "08" => "AUGUST",
        "09" => "SEPTEMBER", "10" => "OCTOBER", "11" => "NOVEMBER", "12" => "DECEMBER"
    );
}
/**
 * @param int $start_yr
 * @param int $end_yr
 * @return string[int]
 */
function range_of_years($start_yr=0,$end_yr=0) {
    if($start_yr == 0)
        $start_yr = date('Y');
    if($end_yr == 0)
        $end_yr = date('Y');
    return range($start_yr, $end_yr);
}
/**
 * @param string $emp_id
 * @param boolean $with_domain
 * @return string
 */
function get_profile_pic_url($emp_id, $with_domain=false){
    global $global_sam_domain;
    $emp_dtl_que = "select GEM_PROFILE_URL from gft_emp_master where GEM_EMP_ID='$emp_id' ";
    $emp_dtl_res = execute_my_query($emp_dtl_que);
    $gem_prof_url = "";
    if($emp_dtl_row = mysqli_fetch_array($emp_dtl_res)){
        $gem_prof_url = $emp_dtl_row['GEM_PROFILE_URL'];
        if($gem_prof_url==""){
            $gem_prof_url = "images/User.png";
        }
    }
    if($with_domain)
        return $global_sam_domain."/".$gem_prof_url;
    return $gem_prof_url;
}
/**
 * @return string
 */
function get_base_relative_path() {
    $folder_depth = substr_count($_SERVER["PHP_SELF"] , "/");
    $base_relative_path=str_repeat("../", $folder_depth - 1);
    return $base_relative_path;
}
/**
 * @param string $emp_id
 * @param int $roleid
 * @return string[string]
 */
function get_shortcut_menus($emp_id, $roleid) {
    $menus_arr = array();
    $base_relative_path = get_base_relative_path();
    
    /* Partner store login detail*/
    if($roleid == 21){
        $pq = " select CGI_LEAD_CODE,GCC_CONTACT_NO from gft_cp_info ".
            " join gft_customer_contact_dtl on (GCC_LEAD_CODE=CGI_LEAD_CODE AND gcc_contact_type=1) ".
            " where CGI_EMP_ID='$emp_id'" ;
        $pexe = execute_my_query($pq);
        if($pdata = mysqli_fetch_assoc($pexe)){
            $add_params = "&cid=".base64_encode($pdata['CGI_LEAD_CODE'])."&cont=".base64_encode($pdata['GCC_CONTACT_NO']);
            $menus_arr[] = array(
                "label" => "Store",
                "icon"  => "fas fa-shopping-cart",
                "link"  => "https://store.gofrugal.com/sign_in.php?action=signin$add_params",
                "relative_path" => false
            );
        }
    }
    
    /* Other common Shortcut menus for all users */
    
    $menus_arr[] = array(
        "label" => "Help",
        "icon"  => "fab fa-hire-a-helper",
        "link"  => isPartnerEmployee($emp_id) ? "http://help-sam.gofrugal.com/Partner.html" : "http://help-sam.gofrugal.com",
        "relative_path" => false
    );
    
    $menus_arr[] = array(
        "label" => "Forum",
        "icon"  => "fab fa-forumbee",
        "link"  => "https://mydiscussion.gofrugal.com",
        "relative_path" => false
    );
    
    $menus_arr[] = array(
        "label" => "My Profile",
        "icon"  => "fas fa-user",
        "link"  => "{$base_relative_path}exec_profile.php",
        "relative_path" => true
    );
    
    $menus_arr[] = array(
        "label"  => "Report a Problem",
        "icon"   => "fas fa-bug",
        "link"   => "{$base_relative_path}report_an_issue.php?id=$emp_id",
        "popup" => true,
        "relative_path" => true
    );
    
    $menus_arr[] = array(
        "label"      => "Print",
        "icon"       => "fas fa-print",
        "link"       => null,
        "popup"     => true,
        "custom_tag" => "print"
    );
    
    $menus_arr[] = array(
        "label"   => "Logout",
        "icon"    => "fas fa-sign-out-alt",
        "link"    => "{$base_relative_path}logout.php",
        "same_tab" => true,
        "relative_path" => true
    );
    
    return $menus_arr;
}
/**
 * @param boolean $isonly_asa_order
 * @param int $lead_code
 * @param int $uid
 * @param boolean $only_partner 
 * @param boolean $only_app
 * 
 * @return mixed
 */
function get_incentive_fields($isonly_asa_order,$lead_code,$uid,$only_partner=false,$only_app=false){
    $pmy_td = '';
    $tr_open  = "<tr>";
    $tr_close = "</tr>";
	if($only_partner == true){
        $pmy_td = "<td width='20px'></td>";
        $tr_close = $tr_open  = "";
    }
    $incentive_emps= array();
    $mandatory_star = "<font color='red' size='3'>*</font>";
    $incentive_fields = "";
    if($isonly_asa_order){
        return $incentive_fields;
    }
    $agile_order_type = get_order_type_for_agile($lead_code);
    if($agile_order_type=='first'){
        $dtl_arr = get_lead_creation_and_prospecting_owner($lead_code);
        $created_by_emp = isset($dtl_arr[0])?$dtl_arr[0]:0;
        $prospect_by_emp= isset($dtl_arr[1])?$dtl_arr[1]:0;
        if($created_by_emp==0){
            $created_by_emp = $uid;
        }
        if($prospect_by_emp==0){
            $prospect_by_emp = $uid;
        }
        if(!$only_partner){
            $created_name = get_single_value_from_single_table("GEM_EMP_NAME", "gft_emp_master", "GEM_EMP_ID", $created_by_emp);
            $prospec_name = get_single_value_from_single_table("GEM_EMP_NAME", "gft_emp_master", "GEM_EMP_ID", $prospect_by_emp);
            $incentive_fields   = "<tr><td class='datalabel'>Lead generation (10%)</td><td>$created_name</td>";
            $incentive_fields  .= "<tr><td class='datalabel'>Prospecting (15%)</td><td>$prospec_name</td>";
        }
        $demo_emp_list = get_eligible_demo_incentive_employees($lead_code,false,$only_partner);
        $demo_emp_arr = /*. (string[int][int]) .*/array();
        $incentive_emps= array();
        for ($mn=0;$mn<count($demo_emp_list);$mn++){
            if($demo_emp_list[$mn][2]=='A'){
                $demo_emp_arr[] = array($demo_emp_list[$mn][0],$demo_emp_list[$mn][1]);
                if($only_app){
                    $only_app_val['id']=$demo_emp_list[$mn][0];
                    $only_app_val['name']=$demo_emp_list[$mn][1];
                    $incentive_emps[] = $only_app_val;
                }
            }
        }
        $cnt_to_check = 1;
        if($only_partner) $cnt_to_check = 0;
        if(count($demo_emp_arr) > $cnt_to_check){
            $demo_employee_combo = fix_combobox_with("demo_incentive_emp", "demo_incentive_emp", $demo_emp_arr, '','','-select-');
            $incentive_fields .= $tr_open.$pmy_td."<TD class='datalabel' nowrap>Primary Demo Owner</TD><TD>$demo_employee_combo</td>$tr_close";
        }
        
        if($only_app){$incentive_fields = $incentive_emps;}
    }else if($agile_order_type=='upsell'){
        $new_inc    = (strtotime(date('Y-m-d')) >= strtotime(trim(get_samee_const("New_Incentive_Order_Date"))));
        $emp_list_arr       = get_eligible_agile_incentive_employees($lead_code,null,true);
        $oppr_emp_list_arr  = get_eligible_agile_incentive_employees($lead_code,null,false);
        if($only_app){
            $helped_by_inc_emps = $oppr_by_inc_emps = array();
            for ($mn=0;$mn<count($emp_list_arr);$mn++){
                $helped_by_app_val['id']=$emp_list_arr[$mn][0];
                $helped_by_app_val['name']=$emp_list_arr[$mn][1];
                $helped_by_inc_emps[] = $helped_by_app_val;
            }
            for ($mn=0;$mn<count($oppr_emp_list_arr);$mn++){
                $oppr_by_app_val['id']=$oppr_emp_list_arr[$mn][0];
                $oppr_by_app_val['name']=$oppr_emp_list_arr[$mn][1];
                $oppr_by_inc_emps[] = $oppr_by_app_val;
            }
            $incentive_fields = array("helped_by"=>$helped_by_inc_emps,"opportunity_by"=>$oppr_by_inc_emps);
        }else{
            if($new_inc && (count($oppr_emp_list_arr) > 0)){
                $oppr_combo = fix_combobox_with("upsell_oppr_emp", "upsell_oppr_emp", $oppr_emp_list_arr, '','','-select-');
                $incentive_fields = $tr_open.$pmy_td."<TD class='datalabel' nowrap>Opportunity identified by</TD><TD>$oppr_combo</td> $tr_close";
            }
            if(count($emp_list_arr) >0){
                $agile_incen_employee_combo = fix_combobox_with("agile_incentive_emp", "agile_incentive_emp", $emp_list_arr, '','','-select-');
                $incentive_fields .= $tr_open.$pmy_td."<TD class='datalabel' nowrap>Demo / Helped by</TD><TD>$agile_incen_employee_combo</td> $tr_close";
            }
        }
    }else if($agile_order_type=='corporate'){
        $corp_emp_list = get_emp_list_by_group_filter(array(5,6,8));
        $corp_incent_emp_combo = fix_combobox_with("corp_joint_emp", "corp_joint_emp", $corp_emp_list, '','','-select-');
        
        $incentive_fields = "<tr>".
            "<td class='datalabel'>$mandatory_star Is other team member eligible for Incentive ?</td>".
            "<td><input type='radio' name='is_other' id='is_other_yes' value='1'> Yes <input type='radio' name='is_other' id='is_other_no' value='0'> No </td>".
            "</tr>".
            "<tr id='corp_incent_row' class='corp_incent_row' style='display:none;'>".
            "<td class='datalabel'>$mandatory_star Agile Incentive Employee </td>".
            "<td>$corp_incent_emp_combo</td></tr>".
            "<tr class='corp_incent_row' style='display:none;'>
				<td class='datalabel'>$mandatory_star Percentage (1 to 50) </td>
				<td><input type='text' size=5 name='corp_joint_perc' id='corp_joint_perc' onkeypress='javascript:return blockNonNumbers(this, event, false, false);'> % </td>".
			"</tr>";
    }
    return $incentive_fields;
}
/**
 * @param string $cp_lcode
 * @param string $roleid
 * @return string
 */
function get_marquee($cp_lcode, $roleid){
    $sql1=raymedi_releases($cp_lcode);
    $rs1=execute_my_query($sql1);
    $marq="*";
    $c=1;
    $flag_news=1;
    while($row1=mysqli_fetch_array($rs1)){
        $flag_news=0;
        $PRODUCT=$row1[0];
        $BASEVERSION=$row1[1];
        $PATCHVERSION=$row1[2];
        $DLINK=$row1[3];
        $PH_DLINK=$row1[4];
        $UPGRADING=$row1[6];
        $ISCSP=(int)$row1[7];
        $CSP=$row1[8];
        $DATE=$row1[9];
        $hyperlink="";
        $upgradpatch="";
        $csppatch="";
        $latest="";
        if($DLINK){
            $latest="<a href=\"$DLINK\" title=\"click here to download\" target=_blank>Installer Download</a>&nbsp;";
        }
        if($PH_DLINK){
            $latest.="&nbsp;<a href=\"$PH_DLINK\"  title=\"click here to download\"  target=_blank>Patch Download</a>&nbsp;";
        }
        if($UPGRADING){
            $latest.="&nbsp;<a href=\"$UPGRADING\"  title=\"click here to download\"  target=_blank>Upgradation download</a>&nbsp;";
        }
        if($ISCSP==1){
            $latest.="&nbsp;<a href=\"$CSP\"  title=\"click here to download\"  target=_blank>Special Patch Download</a>&nbsp;";
        }
        $marq.="&nbsp;&nbsp;$PRODUCT&nbsp;Version:$PATCHVERSION&nbsp;Released on $DATE $latest &nbsp;&nbsp; *";
        $c++;
    }
    if($roleid!=21 and $roleid!=22){
        $sql_rt=raymedi_news();
        $news_detail='';
        $rs_rt=execute_my_query($sql_rt,'raymedi_today.php',true,false,4);
        if(0<mysqli_num_rows($rs_rt)){
            $flag_news=0;
            $news_detail="News Today : ";
            while($rows1=mysqli_fetch_array($rs_rt))
            {
                $news_detail.="  <font color=red>*</font>$rows1[1] says : $rows1[2] *";
            }
        }
        $marq= " $news_detail  $marq ";
    }
    if($flag_news>0){
        $marq="";
    }
    return $marq;
}
/**
 * @param string $install_id
 * @param string $gid_lead_code
 * @param string $status
 * @return void
 */
function update_gosecure_provision($install_id, $gid_lead_code, $status){
    $upd_arr = array(
        'gcd_provision_status'=> $status,
        'gcd_provision_datetime'  => date('Y-m-d H:i:s'),
        'GCD_INSTALL_ID' => $install_id,
        'GCD_LEAD_CODE'=>$gid_lead_code
    );
    $key_arr = array('GCD_INSTALL_ID'=>$install_id);
    array_update_tables_common($upd_arr,"gft_cust_env_data",$key_arr,null,SALES_DUMMY_ID,null,null,$upd_arr);
}
/**
 * @param string $install_id
 * @return void
 */
function update_precheck_status_as_success($install_id){
    $sql_res = execute_my_query("select gps_precheck_status from gft_precheck_status where gps_install_id='$install_id' and gps_precheck_status='success'");
    if(mysqli_num_rows($sql_res) > 0){
        return;
    }
    execute_my_query("delete from gft_precheck_status where gps_install_id='$install_id'");
    $insert_arr = array(
        'gps_install_id' => $install_id,
        'gps_precheck_status'=> 'success',
        'gps_product_code' => 706
    );
    array_insert_query('gft_precheck_status', $insert_arr);
}
/**
 * @param string $prospect_from_dt
 * @param string $prospect_to_dt
 * @param string $emp_code
 * @param string[int] $status_codes
 * @return string
 */
function get_downgrade_counts_query($prospect_from_dt,$prospect_to_dt,$emp_code,$status_codes) {
    $act_dt_qry = '';
    if($prospect_from_dt!='') {
        $act_dt_qry .= " and glh_prospect_on>='$prospect_from_dt' ";
    }
    if($prospect_to_dt!='') {
        $act_dt_qry .= " and glh_prospect_on<='$prospect_to_dt' ";
    }
    if((int)$emp_code>0) {
        $act_dt_qry .= " and glh_prospect_by='$emp_code' ";
    }
    $lead_status_filter = '';
    if(is_array($status_codes) && count($status_codes)>0 and !in_array('0',$status_codes)) {
        $lead_status_filter .= " and gld_lead_status in ('".implode("','",$status_codes)."') ";
    }
    $downgrade_qry = " select gld_lead_status,glh_lead_code,glh_prospect_by prospect_by,gld_visit_date,gem_emp_name gld_activity_by, ".
                     " gcs_name downgrade_status,gld_note_on_activity,gld_activity_id activity_id,gld_date from gft_activity ".
                     " join gft_lead_hdr on (glh_lead_code=gld_lead_code) ".
                     " left join gft_emp_master on (gem_emp_id=gld_activity_by) ".
                     " left join gft_customer_status_master on (gcs_code=gld_lead_status) ".
                     " where gld_activity_by not in ('9999','9998') and gld_activity_id in ".
                     " (select min(gld_activity_id) from gft_activity join gft_lead_hdr on (gld_lead_code=glh_lead_code) ".
                     " where gld_visit_date>glh_prospect_on and glh_prospect_on!='0000-00-00' and ".
                     " gld_lead_status not in (3,8,9) $act_dt_qry group by glh_lead_code) $act_dt_qry $lead_status_filter ";
    return $downgrade_qry;
}
/**
 * @return string[int]
 */
function get_agile_incentive_req_activities() {
    $qry = " select gam_activity_id from gft_activity_master where gam_incentive_req='Y' and gam_activity_status='A' ";
    $res = execute_my_query($qry);
    $activities = array();
    while($row = mysqli_fetch_array($res)) {
        $activities[] = $row['gam_activity_id'];
    }
    return $activities;
}
/**
 * @param string $order_no
 * @return string
 */
function get_max_adj_discount($order_no) {
    $max_adj_amt = " select sum(gop_sell_rate*gop_qty*if(gop_coupon_hour is not null and gop_coupon_hour>0,gop_coupon_hour,1)) ".
        " tot_order_amt from gft_customer_order_adjustment ".
        " join gft_order_hdr on (god_lead_code=goa_lead_code and god_order_status='A') ".
        " join gft_order_product_dtl on (gop_order_no=god_order_no) ".
        " join gft_product_master on (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew ".
        " and gft_skew_property in (1,3,11)) where goa_quotation_no='$order_no' ";
    $max_adj_res = execute_my_query($max_adj_amt);
    $madj_amt = '0.0';
    if($adj_row = mysqli_fetch_assoc($max_adj_res)) {
        $madj_amt = $adj_row['tot_order_amt'];
    }
    return $madj_amt;
}
/**
 * @param string $district_id
 * @return string
 */
function get_country_name_for_district($district_id) {
    $pin_country_qry = " select GPM_MAP_NAME from gft_political_map_master where GPM_MAP_ID in (select ".
        " GPM_MAP_PARENT_ID from gft_political_map_master where GPM_MAP_ID='$district_id' and GPM_MAP_TYPE='D') ".
        " and GPM_MAP_TYPE='C' ";
    $pin_country_res = execute_my_query($pin_country_qry);
    $pin_country = 'India';
    if($pc_row = mysqli_fetch_assoc($pin_country_res)) {
        $pin_country = /*.(string).*/$pc_row['GPM_MAP_NAME'];
    }
    return $pin_country;
}
/**
 * @param string $lead_code
 * 
 * @return string
 */
function check_and_generate_account_number($lead_code){
    global $uid;
    $accountNo = "";
    $accountNo = get_single_value_from_single_table("GLE_UNIQUE_AC_NO", "gft_lead_hdr_ext", "GLE_LEAD_CODE", "$lead_code");
    if($accountNo==""){
        $accountNo = ACCOUNT_NUMBER_PREFIX.str_pad($lead_code, 7, '0', STR_PAD_LEFT);
        $updatearr['GLE_UNIQUE_AC_NO'] 	= "$accountNo";
        $table_key_arr['GLE_LEAD_CODE'] 				= "$lead_code";
        array_update_tables_common($updatearr, "gft_lead_hdr_ext", $table_key_arr, null, $uid);
    }
    return $accountNo;
}
/**
 * @param string $assigned_by
 * @param string $assigned_to
 * @param string $assign_status
 * @param string $assign_complaint_code
 * @param string $check_from_date
 * @param string $lead_code
 * @param string $purpose
 *
 * @return string[int][string]
 */
function get_existing_supprot_tictet_dtl($assigned_by,$assigned_to,$assign_status,$assign_complaint_code,$check_from_date,$lead_code,$purpose='all_dtls') {
    $wh = "";
    if($purpose=='validation') {
        $wh = " and gch_current_status!='T14' and sm2.gtm_group_id!='3' ";
    }
    $chk_qry = " select gch_complaint_id,f.gcd_problem_summary,gch_complaint_date,sm1.gtm_name assigned_status,sm2.gtm_name cur_status, ".
        " l.gcd_process_emp curr_owner,f.gcd_problem_desc problem_desc from gft_customer_support_hdr ".
        " join gft_customer_support_dtl f on (f.gcd_complaint_id=gch_complaint_id and gch_first_activity_id=f.gcd_activity_id) ".
        " join gft_customer_support_dtl l on (l.gcd_complaint_id=gch_complaint_id and gch_first_activity_id=l.gcd_activity_id) ".
        " join gft_status_master sm1 on (sm1.gtm_code=f.gcd_status) ".
        " join gft_status_master sm2 on (sm2.gtm_code=gch_current_status) ".
        " where f.gcd_employee_id='$assigned_by' and f.gcd_process_emp='$assigned_to' and l.gcd_process_emp='$assigned_to' and ".
        " f.gcd_complaint_code='$assign_complaint_code' and gch_complaint_date>='$check_from_date' and gch_lead_code='$lead_code' ".
        " and f.gcd_status='$assign_status' $wh order by gch_complaint_id ";
    $chk_res = execute_my_query($chk_qry);
    $support_dtl = /*.(string[int][string]).*/array();
    while($row = mysqli_fetch_array($chk_res)) {
        $tkt_dtl = array();
        $tkt_dtl['support_id'] = $row['gch_complaint_id'];
        $tkt_dtl['support_summary'] = $row['gcd_problem_summary'];
        $tkt_dtl['problem_desc'] = $row['problem_desc'];
        $tkt_dtl['created_time'] = $row['gch_complaint_date'];
        $tkt_dtl['status_name'] = $row['assigned_status'];
        $tkt_dtl['curr_status'] = $row['cur_status'];
        $support_dtl[] = $tkt_dtl;
    }
    return $support_dtl;
}
/**
 * @param string $type
 * @param string $curr_status
 *
 * @return string[int]
 */
function get_allowed_status_change($type,$curr_status) {
    $fetch_qry = " select gltm_customer_status from gft_lead_transition_master ".
                 " where gltm_master_id='$type' and gltm_status='$curr_status' ";
    $fetch_res = execute_my_query($fetch_qry);
    $allowed_states = /*.(string[int]).*/array();
    while($row = mysqli_fetch_assoc($fetch_res)) {
        $allowed_states[] = $row['gltm_customer_status'];
    }
    return $allowed_states;
}
/**
 * @param string $notification_to
 * @param string $app_type
 * @param string $complaint_id
 * @param string $service_type
 * @param string $sub_status
 * @param string $effort
 * @param string $edc
 * @param string $proforma_no
 * @param string $problem_summary
 * @param string[int] $mail_to_emps
 * @param string $remarks
 * @param string $prev_sub_status
 * 
 * @return void
 */
function send_mail_and_notification_for_dev_lifecycle($notification_to,$app_type,$complaint_id,$service_type,$sub_status,$effort,$edc,$proforma_no,$problem_summary,$mail_to_emps,$remarks="&nbsp;",$prev_sub_status='') {
    $template_id = 0;
    $mail_template_id = 0;
    $emotion = "happy";
    $cust_action = "accepted";
    $explanation = "&nbsp;";
    if(in_array($service_type,array('4','5'))) {
        $template_id = 113;
        $mail_template_id = 353;
        $explanation = " This requirement will not be taken up for development by GOFRUGAL.";
        if($service_type=='5') {
            $explanation = " We will add this feature in our product in the future based on market need.";
        }
    } else {
        if($prev_sub_status=='20' or $sub_status=='20') {
            return; // Notification not sent when QA reassigned the ticket to developer
        }
        if(in_array($sub_status,array('1','2','4','5','6','7','9','10','11','14','19'))) {
            $template_id = 110;
            $mail_template_id = 350;
        } else if(in_array($sub_status,array('3','16'))) { // From myGofrugal
            $template_id = 111;
            $mail_template_id = 351;
            if($sub_status=='16') {
                $emotion = "sad";
                $cust_action = "not accepted";
            }
        } else if(in_array($sub_status,array('17','18'))) { // From myGofrugal
            $template_id = 112;
            $mail_template_id = 352;
            $cust_action = "signed off";
            if($sub_status=='17') {
                $emotion = "sad";
                $cust_action = "not signed off";
            }
        }
    }
    if($template_id>0) {
        $to_name = '&nbsp;';
        $complaint_id_link = "$complaint_id";
        if($app_type==1) {
            $to_name = "Team";
            $complaint_id_link = "<a href='".CURRENT_SERVER_URL."/samui/#/ticket/$complaint_id' target=_blank>$complaint_id</a>";
        } else {
            $to_name = get_single_value_from_single_table("glh_cust_name", "gft_lead_hdr", "glh_lead_code", $notification_to);
        }
        $download_link = '&nbsp;';
        if($proforma_no!='') {
            $download_link = "<br/><br/><a href='".CURRENT_SERVER_URL."/ismile/download.php?type=proforma&id=$proforma_no' target=_blank>Click here to download proforma invoice</a>";
        }
        $edc_effort_dtl = '&nbsp;';
        if(intval($effort)>0) {
            $edc_effort_dtl .= ("Service effort is $effort day".(intval($effort)>1?"s":"").".");
        }
        if(strtotime($edc)>0) {
            $edc_effort_dtl .= " EDC is ".date('M d,Y',strtotime($edc)).".";
        }
        $cust_name = '';
        // Statuses 3,4 and 16 will come only after sending proforma invoice. So, we can take cust name from gph_lead_code
        $proforma_emp_qry = " select gph_emp_id,glh_cust_name from gft_proforma_hdr ".
                            " join gft_dev_complaints on (gph_order_no=gdc_proforma_number) ".
                            " join gft_lead_hdr on (glh_lead_code=gph_lead_code) ".
                            " where gdc_complaint_id='$complaint_id' ";
        $proforma_emp_res = execute_my_query($proforma_emp_qry);
        if($p_row = mysqli_fetch_assoc($proforma_emp_res)) {
            $mail_to_emps[] = $p_row['gph_emp_id'];
            $cust_name = $p_row['glh_cust_name'];
        }
        $mail_to_emps = array_unique($mail_to_emps);
        $cc_mail_ids = array();
        $cc_emails_qry = execute_my_query("select gem_email from gft_emp_master where gem_emp_id in ('".implode("','",$mail_to_emps)."')");
        while($cc_row = mysqli_fetch_assoc($cc_emails_qry)) {
            $cc_mail_ids[] = $cc_row['gem_email'];
        }
        $service_type_label = get_single_value_from_single_table("gds_name", "gft_dev_service_type_master", "gds_id", "$service_type");
        $sub_status_label = get_single_value_from_single_table("gss_desc", "gft_sub_status_master", "gss_id", "$sub_status");
        $noti_content_config = array(
            "Agent_Name"=>array($to_name),
            "comp_id"=>array($complaint_id_link),
            "Employee_Name"=>array("GOFRUGAL"),
            "service_type"=>array($service_type_label),
            "sub_status"=>array($sub_status_label),
            "effort"=>array($edc_effort_dtl),
            "Download_link"=>array($download_link),
            "problem_summary"=>array($problem_summary),
            "Emotion"=>array($emotion),
            "Customer_Name"=>array($cust_name),
            "Customer_Feedback"=>array($cust_action),
            "Mail_Content"=>array($explanation),
            "remarks"=>array($remarks)
        );
        $tomail_ids = array();
        $user_id = array();
        $lead_type = get_single_value_from_single_table("glh_lead_type","gft_lead_hdr","glh_lead_code", $notification_to);
        $corporate_arr = array(3,13);
        if($app_type==2) {
            $que1 = " select gcl_user_id,gcc_contact_no,gcc_contact_type from gft_customer_login_master ".
                    " join gft_customer_access_dtl on (gca_user_id=gcl_user_id and gca_access_status=1) ".
                    " join gft_customer_contact_dtl on (gcc_lead_code=gca_access_lead and gcc_contact_type in ('1','4')) ".
                    " join gft_pos_users on (GPU_CONTACT_ID=GCC_ID) ".
                    " where gcl_user_status=1 and gcc_lead_code='$notification_to' and gpu_mygofrugal_role in ('1','2') ";
            $res1 = execute_my_query($que1);
            while($row = mysqli_fetch_assoc($res1)) {
                if($row['gcc_contact_type']=='4') {
                    $tomail_ids[] = $row['gcc_contact_no'];
                } else {
                    if(!in_array($row['gcl_user_id'],$user_id)) {
                        $user_id[] = $row['gcl_user_id'];
                    }
                }
            }
            if(in_array($lead_type, $corporate_arr)){
                $cc_mail_ids[] = 'pcs-team@gofrugal.com';
            }
        } else {
            $tomail_ids = array('pcs-team@gofrugal.com');
        }
        if($app_type==2) {
            foreach ($user_id as $u_id) {
                send_formatted_notification_content($noti_content_config,8,$template_id,$app_type,$notification_to,'0',$complaint_id,$u_id);
            }
        } else {
            send_formatted_notification_content($noti_content_config,8,$template_id,$app_type,$notification_to,'0',$complaint_id);
        }
        if(count($tomail_ids)>0) {
            if(in_array($sub_status, array("16","17"))){//If commercial/UAT not accepted
                $cc_mail_ids[] = "customersuccess@gofrugal.com";
            }
            send_formatted_mail_content($noti_content_config,8,$mail_template_id,$mail_to_emps,null,$tomail_ids,null,$cc_mail_ids);
        }
    }
}
/**
 * @param string $cust_id
 * @return boolean
 */
function is_pd_completed_customer($cust_id) {
    $pd_completed = false;
    $pd_status_qry = " select gsp_pd_support_group from gft_support_product_group ".
                     " join gft_lead_hdr on (gsp_group_id=glh_main_product) ".
                     " where glh_lead_code='$cust_id' ";
    $pd_status_res = execute_my_query($pd_status_qry);
    if($cust_pd_row = mysqli_fetch_assoc($pd_status_res)) {
        if($cust_pd_row['gsp_pd_support_group']=='Y') {
            $pd_completed = true;
        }
    }
    return $pd_completed;
}
/**
 * @param string $proforma_no
 * @param string $new_sub_status
 * @param string $receipt_amount
 *
 * @return void
 */
function update_collection_pending_ticket($proforma_no,$new_sub_status,$receipt_amount) {
    $complaint_id_qry = " select gdc_complaint_id,gcd_process_emp,gcd_problem_summary,gcd_service_type,gph_emp_id,gch_lead_code, ".
                        " gph_order_amt,god_order_no,god_balance_amt,god_collected_amt,god_order_amt from gft_dev_complaints ".
                        " join gft_customer_support_hdr on (gdc_complaint_id=gch_complaint_id) ".
                        " join gft_customer_support_dtl on (gcd_complaint_id=gch_complaint_id and gcd_activity_id=gch_last_activity_id) ".
                        " join gft_proforma_hdr on (gph_order_no=gdc_proforma_number) ".
                        " left join gft_order_hdr on (god_order_no=gph_converted_order_no and god_order_status='A') ".
                        " where gdc_proforma_number='$proforma_no' and gch_current_status='T2' and (gcd_sub_status='2' ";
    if($new_sub_status=='19') { // Payment in progress
        $complaint_id_qry .= " or gcd_sub_status='3' ";
    } else { // Payment received and EDC update pending
        $complaint_id_qry .= " or gcd_sub_status='19' ";
    }
    $complaint_id_qry .= ")"; // Customer might pay proforma before updating status in myGofrugal
    $complaint_id_res = execute_my_query($complaint_id_qry);
    if($row = mysqli_fetch_assoc($complaint_id_res)) {
        if($row['god_order_no']!='') {
            if((($new_sub_status=='4' and floatval($row['god_balance_amt'])>0.00)
                or ($new_sub_status=='19' and floatval($row['god_order_amt'])-floatval($row['god_collected_amt'])>0.00))) {
                    return;
                }
        } else if(abs(floatval($receipt_amount)-floatval($row['gph_order_amt']))>0) { // No columns in proforma table for collected, and balance amounts 
            return;
        }
        $complaint_id = $row['gdc_complaint_id'];
        $assigned_to = $row['gcd_process_emp'];
        $proforma_emp = $row['gph_emp_id'];
        $mail_to_emps = array($assigned_to,$proforma_emp);
        $summary = $row['gcd_problem_summary'];
        $service_type = $row['gcd_service_type'];
        $cust_id = $row['gch_lead_code'];
        $update_dtl = array();
        $update_dtl['GCD_SUB_STATUS'] = $new_sub_status;
        $remarks = "Payment received from customer for commercial feature development (Proforma Invoice No. $proforma_no)";
        if($new_sub_status=='3') {
            $remarks = "Payment for commercial feature development is realized by accounts team (Proforma Invoice No. $proforma_no)";
        }
        $update_dtl['GCD_REMARKS'] = $remarks;
        $support_activity_qry = get_support_dtl_query_from_previous($complaint_id, $update_dtl);
        $support_activity_res = execute_my_query($support_activity_qry);
        if($support_activity_res!==false) {
            $act_id = mysqli_insert_id_wrapper();
            updated_hdr_with_last_actid($complaint_id,$act_id,'T2');
            $notification_to = $cust_id;
            $app_type = 2;
            if(is_pd_completed_customer($cust_id)) {
                $notification_to = $assigned_to;
                $app_type = 1;
            }
            send_mail_and_notification_for_dev_lifecycle($notification_to,$app_type,$complaint_id,$service_type,$new_sub_status,'','','',$summary,$mail_to_emps);
        }
    }
}
/**
 * @param string $lead_code
 * 
 * @return string[string]
 */
function get_gstin_dtl_for_cust($lead_code) {
    $gst_no_dtls_qry = " select gle_gst_no gst_no,gle_gst_eligible gst_eligible, ".
                       " if(gle_business_name is null or gle_business_name='',glh_cust_name,gle_business_name) cust_name ".
                       " from gft_lead_hdr_ext join gft_lead_hdr on (gle_lead_code=glh_lead_code) ".
                       " where GLE_LEAD_CODE='$lead_code'";
    $gst_no_res = execute_my_query($gst_no_dtls_qry);
    $gst_no_dtl = array();
    if($row = mysqli_fetch_assoc($gst_no_res)) {
        $gst_no_dtl['gstin'] = $row['gst_no'];
        $gst_no_dtl['gst_status'] = $row['gst_eligible'];
        $gst_no_dtl['cust_name'] = $row['cust_name'];
    }
    return $gst_no_dtl;
}
/**
 * @param string $date
 *
 * @return boolean
 */
function isWeekend($date) {
    return (date('N', strtotime($date)) > 6);
}
/**
 * @param string $date
 *
 * @return boolean
 */
function isSecondFourthFifthSaturday($date){
    $month=date("F",strtotime($date));
    $year=date("Y",strtotime($date));
    if(strtotime("first sat of $month $year")==strtotime($date)){
        return true;
    }
    if(strtotime("third sat of $month $year")==strtotime($date)){
        return true;
    }
    if(strtotime("fifth sat of $month $year")==strtotime($date)){
        return true;
    }
    return false;
}
/**
 * @param string $state_id
 * @param string $product_gp
 * @param boolean $fetch_all
 * @return string
 */
function get_cst_agent_for_customer($state_id,$product_gp,$fetch_all=false) {
    $limit_qry = " limit 0,1 ";
    $columns = " gca_emp_id ";
    if($fetch_all) {
        $limit_qry = "";
        $columns = " group_concat(distinct(gca_emp_id) order by gca_state_id desc) as gca_emp_id ";
    }
    $state_cond = " and (gca_state_id='$state_id' or gca_state_id=0) ";
    $order_by = " gca_support_group_id desc ,gca_state_id desc ";
    if($state_id=='-1') {
        $state_cond = "";
        $order_by = " gca_support_group_id desc ";
        $columns = " group_concat(distinct(gca_emp_id) order by gca_state_id) as gca_emp_id ";
    }
    $qry = execute_my_query(" select $columns from gft_cst_agent_mapping join gft_emp_master on (gem_emp_id=gca_emp_id and gem_status='A') ".
        " where (gca_support_group_id='$product_gp' or gca_support_group_id=0) $state_cond ".
        " order by $order_by $limit_qry ");
    $agent_id = '';
    if($row = mysqli_fetch_array($qry)) {
        $agent_id = $row['gca_emp_id'];
    } else {
        $agent_id = get_samee_const("WELCOME_CALL_DEFAULT_EMP");
    }
    return $agent_id;
}

/**
 * @param int $reportId
 * @param int $reduceHeight
 *
 * @return void
 */
function smart_reports_frame($reportId,$reduceHeight=155){
    echo<<<END
<iframe id='sr_frame' src='/smartreport/#/reports?reportId=$reportId&productId=5'></iframe>
<script>
jQuery(document).ready(function () {
    jQuery("#sr_frame").attr("height",(window.innerHeight-$reduceHeight)+"px");
    jQuery("#sr_frame").attr("width",(window.innerWidth-20)+"px");
});
</script>
END;
}
?>
