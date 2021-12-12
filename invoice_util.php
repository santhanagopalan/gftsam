<?php
/*. forward string[int] function get_franchise_invoice_lead_codes_for_orders(string $order_no,string $invoice_raised); .*/
require_once(__DIR__.'/dbcon.php');
/**
 * @param string $order_no
 * @return string[int]
 */
function get_quote_proforma_dtl_for_order($order_no) {
	$check_col1 = " GQH_CONVERTED_ORDER_NO ";
	$check_col2 = " GPH_CONVERTED_ORDER_NO ";
	if(isset($_SESSION['quote_proforma_no']) and $_SESSION['quote_proforma_no']!='') {
		$order_no = $_SESSION['quote_proforma_no'];
		$check_col1 = " GQH_ORDER_NO ";
		$check_col2 = " GPH_ORDER_NO ";
	}
	$quote_qry = " select GQH_QUOTATION_TO_EMAILS contact_ids from gft_quotation_hdr where $check_col1='$order_no' union ".
     			 " select GPH_PROFORMA_TO_EMAILS contact_ids from gft_proforma_hdr where $check_col2='$order_no' ";
	$quote_res = execute_my_query($quote_qry);
	$contact_ids = '';
	while($row = mysqli_fetch_array($quote_res)) {
		$contact_ids = $row['contact_ids'];
	}
	$mail_ids = /*.(string[int]).*/array();
	if($contact_ids!='') {
		$mail_id_qry = execute_my_query(" select gcc_contact_no from gft_customer_contact_dtl where gcc_id in ".
										" ($contact_ids) and gcc_contact_type=4 ");
		while($row = mysqli_fetch_array($mail_id_qry)) {
			$mail_ids[] = $row['gcc_contact_no'];
		}
	}
	return $mail_ids;
}
/**
 * @param string $order_no
 * @param string $lead_type
 * @return boolean
 */
function check_and_update_invoice_status($order_no,$lead_type='3') {
	if($lead_type=='1') {
		$split = get_single_value_from_single_table("god_order_splict", "gft_order_hdr", "god_order_no", $order_no);
		$is_pending = false;
		if($split==1) {
			$opd_chk_qry = " select gop_product_code from gft_order_product_dtl where gop_order_no='$order_no' and ".
						   " gop_product_code in ('391') and (gop_invoice_raised=0 or gop_invoice_raised is null) and ".
						   " gop_sell_amt>0 ";
			$opd_chk_res = execute_my_query($opd_chk_qry);
			if(mysqli_num_rows($opd_chk_res)>0) {
				return false;
			} else {
				$cod_chk_qry = " select gco_product_code from gft_cp_order_dtl join gft_order_product_dtl on ".
							   " (gop_product_code=gco_product_code and gop_product_skew=gco_skew and gop_order_no=gco_order_no) ".
							   " where gco_order_no='$order_no' and gop_sell_amt>0 and gco_product_code not in ('391') ".
							   " and (gco_invoice_raised is null or gco_invoice_raised='0') ";
				$cod_chk_res = execute_my_query($cod_chk_qry);
				if(mysqli_num_rows($cod_chk_res)) { // Add this ">0"
					return false;
				} else { // Checking whether all the products are completely split
					$split_chk = " select gop_product_code from gft_order_product_dtl where gop_product_code not in ('391') and ".
							" (gop_invoice_raised is null or gop_invoice_raised=0) and gop_order_no='$order_no' ".
							" and (gop_qty-GOP_CP_USEDQTY)>0 and gop_sell_amt>0 ";
					$split_chk_res = execute_my_query($split_chk);
					if(mysqli_num_rows($split_chk_res)>0) {
						return false;
					}
					return true;
					
				}
			}
		} else {
			$opd_chk_qry = " select gop_product_code from gft_order_product_dtl where gop_order_no='$order_no' and ".
						   " (gop_invoice_raised=0 or gop_invoice_raised is null) and gop_sell_amt>0 ";
			$opd_chk_res = execute_my_query($opd_chk_qry);
			if(mysqli_num_rows($opd_chk_res)>0) {
				return false;
			} else {
				return true;
			}
		}
	} else {
		$opd_chk_qry = " select gop_product_code from gft_order_product_dtl join gft_product_master on ".
				       " (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew) ".
					   " where gop_order_no='$order_no' and ((gpm_order_type='2' and gop_product_code='308') ".
					   " or gop_product_code='391') and (gop_invoice_raised is null or gop_invoice_raised=2 or gop_invoice_raised=0) ";
		$opd_chk_res = execute_my_query($opd_chk_qry);
		if(mysqli_num_rows($opd_chk_res)>0) {
			return false;
		} else {
			$cod_chk_qry = " select gco_product_code from gft_cp_order_dtl join gft_order_product_dtl on ".
						   " (gop_product_code=gco_product_code and gop_product_skew=gco_skew and gop_order_no=gco_order_no and gop_sell_amt>0) ".
						   " where gco_order_no='$order_no' and gco_product_code not in ('391') ".
						   " and (gco_invoice_raised is null or gco_invoice_raised='2' or gco_invoice_raised='0') ";
			$cod_chk_res = execute_my_query($cod_chk_qry);
			if(mysqli_num_rows($cod_chk_res)>0) {
				return false;
			}
			// Checking whether all the products are completely split
			$split_chk = " select gop_product_code from gft_order_product_dtl where gop_product_code not in ('308','391') and ".
						 " (gop_invoice_raised is null or gop_invoice_raised=2 or gop_invoice_raised=0) and gop_order_no='$order_no' ".
						 " and (gop_qty-GOP_CP_USEDQTY)>0 and gop_sell_amt>0 ";
			$split_chk_res = execute_my_query($split_chk);
			if(mysqli_num_rows($split_chk_res)>0) {
				return false;
			}
		}
		return true;
	}
}
/**
 * @param int $invoice_id
 * @param string $order_no
 * @param boolean $addon_inv
 * @param boolean $is_manual_invoice
 * @return void
 */
function generate_gst_invoice_pdf($invoice_id,$order_no,$addon_inv=false, $is_manual_invoice=false) {
	global $attach_path;
	$query = get_invoice_pdf_hdr_query($invoice_id,$order_no);
	$res = execute_my_query($query);
	$db_doc_content = /*.(string[string]).*/array();
	$lead_code = $cust_name = $customer_country = '';
	$lead_type = '';
	if(mysqli_num_rows($res)==1) {
		$row = mysqli_fetch_array($res);
		$lead_code = $row['gih_lead_code'];
		$cust_name = $row['cust_name'];
		$lead_type = $row['lead_type'];
		$customer_country = $row['GLH_COUNTRY'];
		$db_doc_content['customer_name'] = $cust_name;
		$db_doc_content['inv_no'] = $row['gih_invoice_ac_reffer_id'];
		$db_doc_content['inv_date'] = date('d-m-Y',strtotime($row['gih_invoice_date']));
		$db_doc_content['customer_address'] = $row['cusromer_address'];
		$db_doc_content['customer_state'] = $row['GLH_CUST_STATECODE'];
		$db_doc_content['customer_gstin_label'] = "GSTIN";
		$db_doc_content['customer_gstin'] = (isset($row['GIH_CUST_GSTIN']) and $row['GIH_CUST_GSTIN']!='' and $row['GIH_CUST_GSTIN']!='0')?$row['GIH_CUST_GSTIN']:'&nbsp;';
		$db_doc_content['IEC_NO_LABEL'] = "&nbsp;";
		$db_doc_content['IEC_NO'] = "&nbsp;";
	}
	$db_doc_content['Order_No'] = $order_no;
	$po_dtls = execute_my_query(" select DATE_FORMAT(GOD_PO_DATE,'%d-%m-%Y') GOD_PO_DATE,GOD_PO_ORDER_NO, GOD_TAX_MODE from gft_order_hdr ".
								" where god_order_no='$order_no' ");
	$po_date = $po_no = '&nbsp;';
	$tax_mode = 4;
	if($ord_row = mysqli_fetch_array($po_dtls)) {
		$po_date = isset($ord_row['GOD_PO_DATE'])?$ord_row['GOD_PO_DATE']:'&nbsp;';
		$po_no = isset($ord_row['GOD_PO_ORDER_NO'])?$ord_row['GOD_PO_ORDER_NO']:'&nbsp;';
		$tax_mode = (int)$ord_row['GOD_TAX_MODE'];
		//For export
		if($tax_mode==3){
		    $db_doc_content['IEC_NO_LABEL'] = "IEC No";
		    $db_doc_content['IEC_NO'] = "0408016612";
		    $db_doc_content['customer_gstin_label'] = "Country";
		    $db_doc_content['customer_gstin']= $customer_country;
		}
	}
	$db_doc_content['product_terms']   = get_product_wise_terms($invoice_id);
	$db_doc_content['PO_date'] = $po_date;
	$db_doc_content['PO_no'] = $po_no;
	$db_doc_content['company_gstin'] = get_single_value_from_single_table("GCM_GST_NO", "gft_company_master", "GCM_ID", "1");
	if($addon_inv) {
		$ui_arr = get_invoice_item_dtl_html_for_addon($order_no);
	} else {
	    $ui_arr = get_invoice_item_dtl_html("$invoice_id", "invoice", $lead_type);
	}
	$db_doc_content['invoice_detail_table'] = $ui_arr[0];
	$invoice_content=get_formatted_document_content($db_doc_content,'5','27');
	$html_file_path = write_html_file_for_pdf($invoice_content, 'invoice', "invoice_$invoice_id.html");
	generate_pdf($html_file_path);
	$alliance_proforma_no_qry = execute_my_query(" select gph_order_no from gft_proforma_hdr ".
                        	     " join gft_add_on_commission_dtl on (GAC_ORDER_NO=gph_order_no and GAC_ORDER_REF_TYPE=2) ".
                        	     " where gph_converted_order_no='$order_no' ");
	if(($lead_type!='2' or mysqli_num_rows($alliance_proforma_no_qry)>0) && $tax_mode!=3 && !($is_manual_invoice)) {
	    
		$mail_content = array(
			"Attachment"=>array("$attach_path/invoice/invoice_$invoice_id.pdf"),
			"Type"=>array("Invoice"),
			"Customer_Name"=>array($cust_name),
		    "GSTIN_INFO_MESSAGE"=>array("")
		);
		$gstin_no = get_single_value_from_single_table("GLE_GST_NO", "gft_lead_hdr_ext", "GLE_LEAD_CODE", "$lead_code");
		if(trim($gstin_no) == ""){
		    $mail_content["GSTIN_INFO_MESSAGE"] = array(get_samee_const("GSTIN_CONTENT"));
		}
		$contact_dtl = get_quote_proforma_dtl_for_order($order_no);
		$tomail_ids = array();
		if(count($contact_dtl)>0) {
			$tomail_ids = $contact_dtl;
		} else {
			$store_email_qry = execute_my_query("select goh_email from gft_online_order_hdr where GOH_ORDER_NO='$order_no'");
			if($store_row = mysqli_fetch_array($store_email_qry)) {
				$tomail_ids[] = $store_row['goh_email'];
			}
		}
		$emp_qry = execute_my_query(" select god_emp_id from gft_order_hdr join gft_emp_master on (god_emp_id=gem_emp_id and ".
				   					" gem_status='A' and gem_emp_id<7000) where god_order_no='$order_no' ");
		$to_emps = array();
		if($emp_row = mysqli_fetch_array($emp_qry)) {
			$to_emps[] = (int)$emp_row['god_emp_id']; 
		}
		$lfd_emp = get_single_value_from_single_table("glh_lfd_emp_id", "gft_lead_hdr", "glh_lead_code", $lead_code);
		if((int)$lfd_emp<7000) {
			$to_emps[] = (int)$lfd_emp;
		}
		if(count($tomail_ids)>0) {
			send_formatted_mail_content($mail_content,89,309,null,null,$tomail_ids,$to_emps);
		}
		$domain_url = (get_samee_const("STORE_PAYMENT_TEST_MODE")=='1')?'https://labtest.gofrugal.com':'https://sam.gofrugal.com';
		$noti_content_config = array('Download_link'=>array("<a href='$domain_url/ismile/download.php?id=$invoice_id&type=invoice'>invoice_$invoice_id.pdf</a>"),
									 'Type'=>array('Invoice'),'Customer_Name'=>array($cust_name));
//	 	send_formatted_notification_content($noti_content_config, 89, 93, 2, $lead_code);
	}
}
/**
 * @param InvoiceProdDetails $prod_dtl
 * @param string $franchise_lead_code
 *
 * @return void
 */
function insert_and_generate_invoice($prod_dtl,$franchise_lead_code='') {
	global $uid,$order_product_fields;
	$orderno = $prod_dtl->orderno;
	$lead_code = $prod_dtl->lead_code;
	$cust_gst_dtl = get_gstin_dtl_for_cust($lead_code);
	$gstin_status = isset($cust_gst_dtl['gst_status'])?$cust_gst_dtl['gst_status']:'3'; // 3 - Not Known
	$gstin = isset($cust_gst_dtl['gstin'])?$cust_gst_dtl['gstin']:'';
	$cust_name = isset($cust_gst_dtl['cust_name'])?mysqli_real_escape_string_wrapper($cust_gst_dtl['cust_name']):'';
	$ac_referer_id = $prod_dtl->ac_referer_id;
	$invoice_type = $prod_dtl->invoice_type;
	$ordered_emp_id =  $prod_dtl->ordered_emp_id;
	$against_cform = $prod_dtl->against_cform;
	$invoice_date = $prod_dtl->invoice_date;
	$qty = $prod_dtl->qty;
	$pcode = $prod_dtl->pcode;
	$pskew = $prod_dtl->pskew;
	$order_no = $prod_dtl->orderno;
	$list_price = $prod_dtl->list_price;
	$sell_amt = $prod_dtl->sell_amt;
	$s_tax_rate = $prod_dtl->s_tax_amt;
	$s_tax_amt = $prod_dtl->s_tax_amt;
	$serv_tax_rate = $prod_dtl->serv_tax_rate;
	$serv_tax_amt = $prod_dtl->serv_tax_amt;
	$coupon_hrs = $prod_dtl->coupon_hrs;
	$gst_fields = $prod_dtl->gst_fields;
	$net_amt = $prod_dtl->net_amt;
	$adj_discount = $prod_dtl->adj_discount;
	$fullfillment_no = (isset($prod_dtl->fullfillment_no)?$prod_dtl->fullfillment_no:'');
	$ord_split = $prod_dtl->order_split;
	$amount = $discount = 0;
	$cgst_per = $sgst_per = $igst_per = array();
	$cgst_amt = $sgst_amt = $igst_amt = $gst_sum = $gst_per = array();
	foreach ($pcode as $key => $val) {
		$pqty=$qty[$key];
		if((int)$coupon_hrs[$key]>0){
			$pqty=$pqty*$coupon_hrs[$key];
		}
		if(isset($gst_fields[$val."-".$pskew[$key]])) {
			$arr = $gst_fields[$val."-".$pskew[$key]];
			$cgst_per[$key] = isset($arr['cgst_per'])?$arr['cgst_per']:'';
			$cgst_amt[$key] = isset($arr['cgst_amt'])?$arr['cgst_amt']:'0';
			$sgst_per[$key] = isset($arr['sgst_per'])?$arr['sgst_per']:'';
			$sgst_amt[$key] = isset($arr['sgst_amt'])?$arr['sgst_amt']:'0';
			$igst_per[$key] = isset($arr['igst_per'])?$arr['igst_per']:'';
			$igst_amt[$key] = isset($arr['igst_amt'])?$arr['igst_amt']:'0';
			$gst_sum[$key] = $arr['cgst_amt'] + $arr['sgst_amt'] + $arr['igst_amt'];
			$gst_per[$key] = $arr['cgst_per'] + $arr['sgst_per'] + $arr['igst_per'];
		}
		if(isset($gst_sum[$key]) and (int)$gst_sum[$key]>0) {
			$s_tax_amt[$key] = $serv_tax_amt[$key] = '0';
		}
		$gst_total = isset($gst_sum[$key])?$gst_sum[$key]:0;
		$gst_percent = isset($gst_per[$key])?$gst_per[$key]:0;
		$disc = $adj_discount[$key]*$pqty;
		$amount += (float)$net_amt[$key]-(((100+$gst_percent)/100)*$disc);
		$discount += $disc;
	}
// 	$amount -= $discount;
	if($amount==0){
		return;
	}
	if(!isset($uid) || $uid=='0' || $uid=='') {
		$uid = '9999';
	}
	$queryhdr="insert into gft_invoice_hdr (gih_invoice_id, gih_invoice_ac_reffer_id, gih_invoice_date, gih_lead_code, gih_status," .
			" gih_net_invoice_amount, gih_emp_id,gih_ic_emp_id,gih_type,GIH_PRINT_DISCOUNT,GIH_IS_C_FORM,gih_discount_amount, ".
			" gih_cust_gstin,gih_cust_gstin_status,gih_business_name,gih_invoice_datetime,gih_invoice_src) values" .
			" ('','$ac_referer_id','$invoice_date','$lead_code','A', round($amount), $ordered_emp_id, '$uid','$invoice_type', ".
			" 'N','$against_cform','$discount','$gstin','$gstin_status','$cust_name',now(),'2') ";
	$result=execute_my_query($queryhdr);
	if($result) {
		$invoice_id = mysqli_insert_id_wrapper();
		$order_no="";
		$gst_cols = str_replace('GOP', 'GIP', $order_product_fields);
		foreach ($pcode as $key => $val) {
			$gst_insert_vals = ",0,0,0,0,0,0";
			if(isset($gst_sum[$key])) {
				$gst_insert_vals = ",'$cgst_per[$key]','$cgst_amt[$key]','$sgst_per[$key]','$sgst_amt[$key]','$igst_per[$key]','$igst_amt[$key]'";
			}
			$pqty=$qty[$key];
			if((int)$coupon_hrs[$key]>0){
				$pqty=$pqty*$coupon_hrs[$key];
			}
			//$curr_amount = ( $pqty * $sell_amt[$key]) + $s_tax_amt[$key] + $serv_tax_amt[$key] + $gst_sum[$key];
			$curr_amount = $net_amt[$key];
			$queryproduct=" insert into gft_invoice_product_dtl ".
					" (gip_invoice_id, GIP_ORDER_NO, gip_product_code, gip_product_skew, gip_list_prize, gip_rate, gip_qty, ".
					" GIP_TAX_RATE,GIP_TAX_AMT, GIP_SERTAX_RATE, GIP_SERTAX_AMT, gip_amount,GIP_COUPON_HRS$gst_cols) values " .
					" ('$invoice_id', '$orderno[$key]', $val, '$pskew[$key]', '$list_price[$key]','$sell_amt[$key]', $qty[$key], ".
					" '$s_tax_rate[$key]', '$s_tax_amt[$key]', '$serv_tax_rate[$key]', '$serv_tax_amt[$key]',$curr_amount,'$coupon_hrs[$key]'$gst_insert_vals ) ";
			execute_my_query($queryproduct);
			$order_no=$orderno[$key];
		}
		if(count($gst_fields)>0) {
			$addon_inv = false;
			$lead_type_chk = execute_my_query("select * from gft_lead_hdr where glh_lead_code='$lead_code' and glh_lead_type=2 and glh_lead_subtype=3");
			if(mysqli_num_rows($lead_type_chk)>0) {
				$proforma_no = get_single_value_from_single_table("gph_order_no", "gft_proforma_hdr", "gph_converted_order_no", $order_no);
				$commission_dtl_chk = execute_my_query("select gac_id from gft_add_on_commission_dtl where gac_order_no='$proforma_no'");
				if(mysqli_num_rows($commission_dtl_chk)>0) {
					$addon_inv = true;
				}
			}
			generate_gst_invoice_pdf($invoice_id,$order_no,$addon_inv);
		} else {
			generate_invoice($invoice_id,$order_no);
		}
	}
}
/**
 * @param string $order_no
 * @param string $pcode
 * @param string $pskew
 * @param string $cust_qty
 * @return string[string]
 */
function get_gst_vals_from_gop($order_no,$pcode,$pskew,$cust_qty) {
	global $order_product_fields;
	$query = " select gop_qty$order_product_fields from gft_order_product_dtl where gop_order_no='$order_no' and gop_product_code='$pcode' and gop_product_skew='$pskew' ";
	$res = execute_my_query($query);
	$ret_arr = /*.(string[string]).*/array();
	if($row = mysqli_fetch_array($res)) {
		$ret_arr['cgst_per'] = $row['GOP_CGST_PER'];
		$ret_arr['cgst_amt'] = ((string)((float)$row['GOP_CGST_AMT']/(int)$row['gop_qty']))*(int)$cust_qty;
		$ret_arr['sgst_per'] = $row['GOP_SGST_PER'];
		$ret_arr['sgst_amt'] = ((string)((float)$row['GOP_SGST_AMT']/(int)$row['gop_qty']))*(int)$cust_qty;
		$ret_arr['igst_per'] = $row['GOP_IGST_PER'];
		$ret_arr['igst_amt'] = ((string)((float)$row['GOP_IGST_AMT']/(int)$row['gop_qty']))*(int)$cust_qty;
	}
	return $ret_arr;
}
/**
 * @param string $order_no
 * @param string $split
 * @param string $GFT_SKEW_PROPERTY
 * @param string $GPM_IS_BASE_PRODUCT
 * @param string $gco_license_status
 * @param string $GCO_PRODUCT_CODE
 * @param string $GCO_SKEW
 * @param string $GCO_FULLFILLMENT_NO
 * @param string $glh_lead_type
 * @param boolean $kit_order
 * @return boolean[int]
 */
function check_invoice_generation_for_prod($order_no,$split,$GFT_SKEW_PROPERTY,$GPM_IS_BASE_PRODUCT,$gco_license_status,$GCO_PRODUCT_CODE,$GCO_SKEW,$GCO_FULLFILLMENT_NO,$glh_lead_type='',$kit_order=true) {
	$result_arr = /*.(boolean[int]).*/array();
	$partial_invoice = $is_trial_upgrade = $check_install = false;
	$approved_by_cm = true;
	$install_date = '';
	if((in_array($GFT_SKEW_PROPERTY,array('1','11')) and $GPM_IS_BASE_PRODUCT=='Y') or in_array($GFT_SKEW_PROPERTY,array('2'))) {
		if(in_array($GFT_SKEW_PROPERTY,array('1','11'))) {
			if($GFT_SKEW_PROPERTY=='11') {
				$ass_dtl_qry = execute_my_query(" select * from gft_ass_dtl where gad_ass_order_no='$order_no' and gad_product_code='$GCO_PRODUCT_CODE' and gad_product_skew='$GCO_SKEW' ");
				if(mysqli_num_rows($ass_dtl_qry)==0) {
					$check_install = true;
				}
			} else {
				$check_install = true;
				$approve_date_res = get_data_from_table(array("date(GLA_APPROVED_ON) approve_date "), "gft_lic_approved_log", array("GLA_ORDER_NO","GLA_FULLFILLMENT_NO","GLA_PRODUCT_CODE","GLA_PRODUCT_SKEW","GLA_STATUS_CHANGEDAS"), array($order_no,$GCO_FULLFILLMENT_NO,$GCO_PRODUCT_CODE,$GCO_SKEW,'Approved by CM'));
				if(!isset($approve_date_res[0]) and !isset($approve_date_res[0]['approve_date'])) {
					$approved_by_cm = false;
					$partial_invoice = true;
				}
			}
			if($check_install and $approved_by_cm) {
				$install_date_qry = " select ifnull(gid_install_date,'') ins_date from gft_install_dtl_new where gid_order_no='$order_no' and gid_product_code='$GCO_PRODUCT_CODE' and gid_product_skew='$GCO_SKEW' and GID_FULLFILLMENT_NO='$GCO_FULLFILLMENT_NO' ";
				$install_res = execute_my_query($install_date_qry);
				if($row = mysqli_fetch_array($install_res)) {
					$install_date = $row['ins_date'];
				} else {
					if($glh_lead_type=='3' and !$kit_order) {
						if($GCO_PRODUCT_CODE=='300') {
							if(!check_hq_installations_for_invoice($order_no,$GCO_SKEW,get_single_value_from_single_table('god_lead_code', 'gft_order_hdr', 'god_order_no', $order_no))) {
								$partial_invoice = true;
							} else {
								$partial_invoice = false;
							}
						} else {
							$partial_invoice = true;
						}
					} else {
						$partial_invoice = true;
					}
				}
			}
		} else {
			$root_order_dtls = execute_my_query(" select GOU_OLD_PCODE,GPM_LICENSE_TYPE,GOU_ROOT_FULLFILLMENT_NO,GOU_ROOT_ORDER_NO from gft_order_upgradation_dtl ".
												" join gft_product_master on (gpm_product_code=GOU_OLD_PCODE and gpm_product_skew=GOU_OLD_PSKEW) ".
												" where GOU_ORDER_NO='$order_no' ");
			$root_pcode = $root_pskew = $root_ff = $root_order = $prod_type = '';
			if($row = mysqli_fetch_array($root_order_dtls)) {
				$root_order = $row['GOU_ROOT_ORDER_NO'];
				$root_pcode = $row['GOU_OLD_PCODE'];
				if($row['GPM_LICENSE_TYPE']=='3') {
					$is_trial_upgrade = true;
				} else {
					$partial_invoice = false;
				}
				$root_pskew_qry = execute_my_query(" select gpm_product_type,gou_old_pskew from gft_order_upgradation_dtl ". 
								  "	join gft_order_hdr on (gou_order_no=god_order_no) ".
								  " join gft_product_master on (gpm_product_code=gou_old_pcode and gpm_product_skew=gou_old_pskew) ".
								  " where gou_root_order_no='$root_order' and god_order_date in (select min(god_order_date) ". 
								  "	from gft_order_upgradation_dtl join gft_order_hdr on (gou_order_no=god_order_no) ". 
								  " where gou_root_order_no='$root_order') ");
				if($root_row = mysqli_fetch_array($root_pskew_qry)) {
					$root_pskew = $root_row['gou_old_pskew'];
				}
				$root_ff = $row['GOU_ROOT_FULLFILLMENT_NO'];
			}
			if($is_trial_upgrade) {
				$check_install = true;
				$approve_date_res = get_data_from_table(array("date(GLA_APPROVED_ON) approve_date "), "gft_lic_approved_log", array("GLA_ORDER_NO","GLA_FULLFILLMENT_NO","GLA_PRODUCT_CODE","GLA_PRODUCT_SKEW","GLA_STATUS_CHANGEDAS"), array($root_order,$root_ff,$root_pcode,$root_pskew,'Approved by CM'));
				if(!isset($approve_date_res[0]) and !isset($approve_date_res[0]['approve_date'])) {
					$partial_invoice = true;
				}
			}
		}
	}
	if($partial_invoice) {
		if($split=='1') {
			execute_my_query("update gft_cp_order_dtl set gco_invoice_raised='0' where gco_order_no='$order_no' and gco_product_code='".$GCO_PRODUCT_CODE."' and gco_skew='".$GCO_SKEW."' and GCO_FULLFILLMENT_NO='".$GCO_FULLFILLMENT_NO."'");
		} else {
			execute_my_query("update gft_order_product_dtl set gop_invoice_raised='0' where gop_order_no='$order_no' and gop_product_code='".$GCO_PRODUCT_CODE."' and gop_product_skew='".$GCO_SKEW."' and GOP_FULLFILLMENT_NO='".$GCO_FULLFILLMENT_NO."'");
		}
	}
	$result_arr[0] = $partial_invoice;
	$result_arr[1] = $is_trial_upgrade;
	return $result_arr;
}
/**
 * @param string $order_no
 * @return boolean
 */
function check_invoice_conditions_for_kit_orders($order_no) {
	$hq_dtls_qry = execute_my_query(" select gco_skew,gco_fullfillment_no,gco_license_status from gft_cp_order_dtl ".
			" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GCO_PRODUCT_CODE and pm.GPM_PRODUCT_SKEW=GCO_SKEW) ".
			" where gco_order_no='$order_no' and gco_product_code='300' and gft_skew_property='1'");
	$gco_fullfill = $gco_skew = $hq_lic_status = '';
	if(mysqli_num_rows($hq_dtls_qry)==0) {
		return true;
	}
	if($split_row = mysqli_fetch_array($hq_dtls_qry)) {
		$gco_fullfill = $split_row['gco_fullfillment_no'];
		$gco_skew = $split_row['gco_skew'];
		$hq_lic_status = $split_row['gco_license_status'];
	}
	$stat = check_invoice_generation_for_prod($order_no, '0', '1', 'Y', $hq_lic_status, '300', $gco_skew, $gco_fullfill);
	if($stat[0]) {
		return false;
	}
	return true;
}
/**
 * @param string $order_no
 * @param string $split
 * @return boolean
 */
function check_invoice_status_based_on_used_qty($order_no,$split='1') {
	if($split=='0') {
		$gop_prods_qry = execute_my_query(" select * from gft_order_product_dtl where gop_order_no='$order_no' and (gop_invoice_raised is null or gop_invoice_raised='0') and gop_sell_amt>0 ");
		if(mysqli_num_rows($gop_prods_qry)>0) {
			return false;
		}
	} else {
	    // asdded sell amt condition for checking whether all the products are split
		$gop_prods_qry = execute_my_query(" select gop_qty,gop_cp_usedqty from gft_order_product_dtl where gop_order_no='$order_no' and gop_sell_amt>0 ");
		while($row = mysqli_fetch_array($gop_prods_qry)) {
			if($row['gop_qty']!=$row['gop_cp_usedqty']) {
				return false;
			}
		}
		// additional services and ALR skews are invoiced from order product dtl and all others are invoiced from CP order details 
		$chk_qry = " select gco_order_no from gft_cp_order_dtl ".
            	   " join gft_order_product_dtl on (gop_order_no=gco_order_no and ".
            	   " gop_product_code=gco_product_code and gop_product_skew=gco_skew) ".
            	   " join gft_product_master on (gpm_product_code=gco_product_code and ".
            	   " gpm_product_skew=gco_skew) where gco_order_no='$order_no' and ".
            	   " (gco_invoice_raised is null or gco_invoice_raised='0') and gop_sell_amt>0 and ".
            	   " gpm_product_code!='391' and gft_skew_property not in (4,8,15) ".
            	   " union all ".
            	   " select gop_order_no from gft_order_product_dtl ".
            	   " join gft_product_master on (gpm_product_code=gop_product_code and ".
            	   " gpm_product_skew=gop_product_skew) where gop_order_no='$order_no' and ".
            	   " (gop_invoice_raised is null or gop_invoice_raised='0') and gop_sell_amt>0 and ".
            	   " (gpm_product_code='391' or gft_skew_property in (4,8,15)) ";
		$gco_prods_qry = execute_my_query($chk_qry);
		if(mysqli_num_rows($gco_prods_qry)>0) {
			return false;
		}
	}
	return true;
}
/**
 * @param string $order_no
 * @param string $invoice_to
 * @param string $from_page
 * @param string $invoice_date
 * @param boolean $corporate_full_invoice
 * @param boolean $monthly_invoice
 * @return boolean
 */
function generate_gst_invoice($order_no,$invoice_to,$from_page='',$invoice_date='',$corporate_full_invoice=false,$monthly_invoice=false) {
	if( ($invoice_to=='') || ($order_no=='') ){
		return false;
	}
	$select_invoice_date_str = " GOD_ORDER_DATE ";
	if(($invoice_to=='customer' and $from_page!='store') or ($from_page=='order_approval' and $invoice_to=='partner') ){
		//$select_invoice_date_str = " if(GOD_ORDER_DATE > receipt_date, GOD_ORDER_DATE,receipt_date) ";
		$select_invoice_date_str = " date(now()) ";
		if($from_page='data_patch' and $invoice_date!='') {
			$select_invoice_date_str = "'".$invoice_date."'";
		}
	}
	$receipt_type = 0;
	$ac_ref = $split = $lead_code = $lead_type = '';
	$god_bal_amt = $is_invoiced = '';
	$receipt_sub_query =" select GCR_ORDER_NO,max(GRD_CHEQUE_CLEARED_DATE) as receipt_date,GRD_RECEIPT_TYPE from gft_collection_receipt_dtl ".
			" join gft_receipt_dtl on (GCR_RECEIPT_ID=GRD_RECEIPT_ID) where GCR_ORDER_NO='$order_no' ";
	$hdr_que=" select god_order_splict,GOD_ORDER_AMT, GOD_BALANCE_AMT, GOD_EMP_ID, GOD_LEAD_CODE, GOD_TAX_MODE, god_invoice_status, GLH_LEAD_TYPE,GOD_PAYMENT_CODE, ".
			" $select_invoice_date_str as inv_dt,GRD_RECEIPT_TYPE,group_concat(gpm_product_code) product_codes,glh_lead_type from gft_order_hdr ". 
			" join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ".
			" join gft_order_product_dtl on (god_order_no=gop_order_no) ".
			" join gft_product_master on (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew) ".
			" left join gft_order_approve_log on (GOA_ORDER_NO=GOD_ORDER_NO and GOA_STATUS_CHANGEDAS='Order Approved') ".
			" left join ($receipt_sub_query) receipt_dtl on (GOD_ORDER_NO=receipt_dtl.GCR_ORDER_NO) ".
			" where GOD_ORDER_NO='$order_no' group by god_order_no ";
	$hdr_res = execute_my_query($hdr_que);
	$payment_code = $glh_lead_type = 0;
	$kit_order = false;
	$ordered_emp_id = '';
	$invoice_date 	= date('Y-m-d');
	if($hdr_data = mysqli_fetch_array($hdr_res)){
		$god_order_amt 	= (int)$hdr_data['GOD_ORDER_AMT'];
		$glh_lead_type 	= (int)$hdr_data['GLH_LEAD_TYPE'];
		$god_bal_amt	= (int)$hdr_data['GOD_BALANCE_AMT'];
		$tax_mode		= (int)$hdr_data['GOD_TAX_MODE'];
		$is_invoiced	= $hdr_data['god_invoice_status'];
		$receipt_type	= (int)$hdr_data['GRD_RECEIPT_TYPE'];
		$ordered_emp_id = $hdr_data['GOD_EMP_ID'];
		$product_codes 	= explode(",",$hdr_data['product_codes']);
		$lead_code 		= $hdr_data['GOD_LEAD_CODE'];
		$lead_type 		= $hdr_data['glh_lead_type'];
// 		$adj_discount 	= $hdr_data['GOD_DISCOUNT_ADJ_AMT'];
		if($glh_lead_type==3 and (in_array('308',$product_codes) or check_kit_based_customer($lead_code))) {
			$kit_order = true;
		}
		$is_saas_order = false;
		if(in_array('601',$product_codes) || in_array('605',$product_codes)){
		    $is_saas_order = true;
		}
		if(($kit_order || $is_saas_order) and (int)$ordered_emp_id>7000 and (int)$ordered_emp_id!='9999') {
			$lead_code = get_single_value_from_single_table("cgi_lead_code", "gft_cp_info", "cgi_emp_id", $ordered_emp_id);
			$lead_type = '2';
		}
		if($glh_lead_type=='2') {
			$invoice_to = 'partner';
		}
		$split = $hdr_data['god_order_splict'];
		if( ($god_order_amt==0) || ($tax_mode==3) || ($is_invoiced=='Y') ){
			return false;
		}
		$against_cform 	= ($tax_mode==2)?'Y':'N';
		$invoice_date 	= $hdr_data['inv_dt'];
		$payment_code	= (int)$hdr_data['GOD_PAYMENT_CODE'];
	}else{
		return false; //order not found
	}
	$pcode = $adj_discount = $pskew = $orderno = $sales_tax_per = $sales_tax_amt = $service_tax_per = $service_tax_amt = /*. (string[int]) .*/array();
	$gst_arr = /*. (string[string]) .*/array();
	$skip_check = false;
	$inv_raised = '1';
	if($monthly_invoice) {
		$inv_raised = '2';
	} else {
		if($is_invoiced=='M') {
			$inv_raised = '2';
			$monthly_invoice = true;
		} else {
			if($god_bal_amt==0 and (($glh_lead_type==1 and (($split=='1' and $is_invoiced=='N') or $split=='0')) or $kit_order)) {
				/*
				 * When 100% payment is received and invoices not raised for any product in order,
				 * then full invoice is generated based on order product details for end users
				 */
				$split = '0';
				$skip_check = true;
				if($kit_order) {
					$corporate_full_invoice = true;
				}
			}
		}
	}
	$opd = new OrderProdDetails();
	if($split=='1' and !($glh_lead_type==3 and $kit_order) and $from_page!='store' and $invoice_to!='partner' and !$corporate_full_invoice) {
		$coupon_hrs = $sell_amt = $net_amt = /*. (float[int]) .*/array();
		$selectq1 = get_prod_dtl_query_for_invoice(1,$order_no,false,false);
		$opd = get_order_prod_dtl_for_invoice($order_no);
	} else {
		$coupon_hrs = $sell_amt = $net_amt = /*. (string[int]) .*/array();
		$selectq1 = get_prod_dtl_query_for_invoice(0,$order_no,false,false);
	}
	$result1 = execute_my_query($selectq1);
	$pcode_pskews = array();
	$i = 0;
	$partial_invoice = $hq_clear = false;
	if($glh_lead_type==3 and $kit_order and !$corporate_full_invoice and $invoice_to!='partner') {
		if(!check_invoice_conditions_for_kit_orders($order_no)) {
			if($monthly_invoice) {
				execute_my_query("update gft_order_hdr set god_invoice_status='M' where god_order_no='$order_no'");
			}
			return false;
		}
	}
	$done_qry = execute_my_query(" select gco_product_code,gco_skew,gco_fullfillment_no from gft_cp_order_dtl where gco_order_no='$order_no' and gco_invoice_raised='1' ");
	$skip = array();
	while($dtl = mysqli_fetch_array($done_qry)) {
		$skip[] = "'".$dtl['gco_product_code']."-".$dtl['gco_skew']."-".$dtl['gco_fullfillment_no']."'";
	}
	$inv_lead_code = '';
	$prod_dtl = new InvoiceProdDetails();
	$prod_dtl->lead_code = $lead_code;
	$prod_dtl->ordered_emp_id = $ordered_emp_id;
	$prod_dtl->against_cform = $against_cform;
	$prod_dtl->invoice_date = $invoice_date;
	while($row1 = mysqli_fetch_array($result1)){
		if($split=='0' or ($glh_lead_type==3 and $kit_order) or $from_page=='store' or $invoice_to=='partner' or $corporate_full_invoice) {
			if((int)$row1['GOP_SELL_AMT']==0) {
				continue;
			}
			if($glh_lead_type==3 and $kit_order) {
				$skip_check = true;
			}
			if($invoice_to=='customer' and !$skip_check and !in_array($from_page,array('store','dealer_order')) and !in_array($row1['gpm_product_type'],array('8')) and in_array($row1['GFT_SKEW_PROPERTY'],array('1','2','11')) and !$corporate_full_invoice) {
				$lic_applicable = ($row1['gpm_is_internal_product']=='2' || $row1['gpm_category']=='6')?'N':'Y';
				$chk = check_invoice_generation_for_prod($order_no,$split,$row1['GFT_SKEW_PROPERTY'],$lic_applicable,$row1['gop_license_status'],$row1['GOP_PRODUCT_CODE'],$row1['GOP_PRODUCT_SKEW'],$row1['GOP_FULLFILLMENT_NO']);
				if($chk[0]==true) {
					if(in_array($row1['GFT_SKEW_PROPERTY'],array('1','11')) or ($row1['GFT_SKEW_PROPERTY']=='2' and $chk[1]==true)) {
						execute_my_query(" update gft_order_product_dtl set gop_invoice_raised='0' where gop_order_no='$order_no' ");
						if($monthly_invoice) {
							array_update_tables_common(array('god_invoice_status'=>'M'),'gft_order_hdr',array('dog_order_no'=>$order_no),null,'9999',null,null,array('god_invoice_status'=>'M'));
						}
						return false;
					}
					$partial_invoice = true;
					continue;
				}
			}
			$prod_dtl = get_invoice_prod_dtl_for_order('0',/*.(string[string]).*/$row1, $prod_dtl, null, null,false);
			execute_my_query("update gft_order_product_dtl set gop_invoice_raised='$inv_raised' where gop_order_no='".$row1['GOP_ORDER_NO']."' and gop_product_code='".$row1['GOP_PRODUCT_CODE']."' and GOP_PRODUCT_SKEW='".$row1['GOP_PRODUCT_SKEW']."' and GOP_FULLFILLMENT_NO='".$row1['GOP_FULLFILLMENT_NO']."'");
		} else {
			$done_qry = execute_my_query(" select gco_product_code,gco_skew,gco_fullfillment_no from gft_cp_order_dtl where gco_order_no='$order_no' and gco_invoice_raised='1' ");
			$skip = array();
			while($dtl = mysqli_fetch_array($done_qry)) {
				$skip[] = "'".$dtl['gco_product_code']."-".$dtl['gco_skew']."-".$dtl['gco_fullfillment_no']."'";
			}
			$codeskew = $row1['GCO_PRODUCT_CODE']."-".$row1['GCO_SKEW'];
			$gst_arr = get_gst_vals_from_gop($row1['GCO_ORDER_NO'],$row1['GCO_PRODUCT_CODE'],$row1['GCO_SKEW'],$row1['GCO_CUST_QTY']);
			if($invoice_to=='customer' and !in_array($from_page,array('store','dealer_order')) and !in_array($row1['gpm_product_type'],array('8')) and in_array($row1['GFT_SKEW_PROPERTY'],array('1','2','11'))) {
				$lic_applicable = ($row1['gpm_is_internal_product']=='2' || $row1['gpm_category']=='6')?'N':'Y';
				$chk = check_invoice_generation_for_prod($order_no,$split,$row1['GFT_SKEW_PROPERTY'],$lic_applicable,$row1['gco_license_status'],$row1['GCO_PRODUCT_CODE'],$row1['GCO_SKEW'],$row1['GCO_FULLFILLMENT_NO'],$glh_lead_type,$kit_order);
				if($chk[0]==true) {
					if(in_array($row1['GFT_SKEW_PROPERTY'],array('1','11')) or ($row1['GFT_SKEW_PROPERTY']=='2' and $chk[1]==true)) {
						$cond = '';
						if(count($skip)>0) {
							$cond = " and concat(gco_product_code,'-',gco_skew,'-',gco_fullfillment_no) in (".implode(",",$skip).") ";
						}
						execute_my_query(" update gft_cp_order_dtl set gco_invoice_raised='0' where gco_order_no='$order_no' and gco_product_code='".$row1['GCO_PRODUCT_CODE']."' and gco_skew='".$row1['GCO_SKEW']."' and gco_fullfillment_no='".$row1['GCO_FULLFILLMENT_NO']."' $cond ");
						continue;
					}
					$partial_invoice = true;
					continue;
				}
				execute_my_query(" update gft_cp_order_dtl set gco_invoice_raised='0' where gco_order_no='$order_no' and gco_product_code='391' ");
			}
			$prod_dtl = get_invoice_prod_dtl_for_order('1',$row1, $prod_dtl, $opd, $gst_arr,false);			
			if($lead_type=='3') {
				if(get_single_value_from_single_table("GLE_INVOICE_FOR_FRANCHISE", "GFT_LEAD_HDR_EXT", "GLE_LEAD_CODE", $row1['gco_cust_code'])=='1') {
					$inv_lead_code = $row1['gco_cust_code'];
				}
			}
			execute_my_query("update gft_cp_order_dtl set gco_invoice_raised='$inv_raised' where gco_order_no='".$row1['GCO_ORDER_NO']."' and gco_product_code='".$row1['GCO_PRODUCT_CODE']."' and gco_skew='".$row1['GCO_SKEW']."' and GCO_FULLFILLMENT_NO='".$row1['GCO_FULLFILLMENT_NO']."'");
		}
	}
	if($split=='1') {
		$additional_services_qry = get_prod_dtl_query_for_invoice(1,$order_no,false,true);		
		$additional_res = execute_my_query($additional_services_qry);
		while ($row = mysqli_fetch_array($additional_res)) { 
			$prod_dtl = get_invoice_prod_dtl_for_order('1', $row, $prod_dtl, null, null, true);
			$used_qty_update = "";
			if(($row['gop_product_code']=='391' and in_array($row['gop_product_skew'],array('01.0EE','01.0OE'))) or in_array($row['gft_skew_property'],array('4','15'))) {
				$used_qty_update = ", gop_cp_usedqty=gop_qty ";
			}
			execute_my_query("update gft_order_product_dtl set gop_invoice_raised='$inv_raised'$used_qty_update where gop_order_no='".$order_no."' and gop_product_code='".$row['gop_product_code']."' and GOP_PRODUCT_SKEW='".$row['gop_product_skew']."' and GOP_FULLFILLMENT_NO='".$row['GOP_FULLFILLMENT_NO']."'");
		}
	}
	$invoice_type=1;
	$prod_dtl->ac_referer_id = "";
	if(count($prod_dtl->pcode) > 0){
		$uniq_arr = array_unique($prod_dtl->pcode);
		$ac_ref = "SA";
		if(count($uniq_arr)==1 and in_array("71",$uniq_arr)){
			$ac_ref = "DA";
		} else if($invoice_to=='partner'){
			$ac_ref = "CA";
		} else if( ($receipt_type==6) || ($from_page=='saas_order') ){ //EBS
			$ac_ref = "OA";
		} else if($glh_lead_type=='3') { // Corporate customers
			$ac_ref = "GS";
		}
	}
	if($split=='1' and $invoice_to=='customer' and !in_array($from_page,array('store','dealer_order')) and !$skip_check and !$corporate_full_invoice) {
		if(!check_invoice_status_based_on_used_qty($order_no)) {
			$partial_invoice = true;
		}
	}
	$upd_invoice_status = ($monthly_invoice?'M':$is_invoiced);
	if( (count($prod_dtl->pcode) > 0)){
		if($monthly_invoice) {
			$upd_invoice_status = 'M';
		} else {
			$upd_invoice_status = 'Y';
			if($partial_invoice){
				$upd_invoice_status = 'S';
			}
		}
	}
	execute_my_query("update gft_order_hdr set god_invoice_status='$upd_invoice_status' where god_order_no='$order_no'");
	if(count($prod_dtl->pcode)==0) {
		return false;
	}
	$prod_dtl->ac_referer_id = get_invoice_ac_ref($ac_ref, $invoice_date);
	$prod_dtl->invoice_type = $invoice_type;
	if($inv_lead_code!='') {
		$prod_dtl->lead_code = $inv_lead_code;
	}
	if(!$monthly_invoice and $is_invoiced!='M') {
		insert_and_generate_invoice($prod_dtl);
	}
	return true;
}

/**
 * @param string $order_no
 * @param string $invoice_to
 * @param string $from_page
 * @param string $invoice_date
 * @param boolean $monthly_invoice
 *
 * @return boolean
*/
function generate_accounts_invoice($order_no, $invoice_to, $from_page='',$invoice_date='',$monthly_invoice=false){
	$order_dtls = /*.(string[int][string]).*/get_data_from_table(array('god_tax_mode','god_order_date','god_emp_id','god_order_approval_status'), "gft_order_hdr", array("god_order_no"), array("$order_no"));
	$order_tax_mode = '';
	$order_date = $order_emp = '0';
	$approval_status = '';
	if(isset($order_dtls[0])) {
		$order_date = $order_dtls[0]["god_order_date"];;
		$order_tax_mode = $order_dtls[0]["god_tax_mode"];
		$order_emp = $order_dtls[0]["god_emp_id"];
		$approval_status = $order_dtls[0]["god_order_approval_status"];
	}
	$dealer_check = get_data_from_table(array("distinct gop_product_code as pcode"), "gft_order_product_dtl", array('gop_order_no'), array($order_no));
	$unique_pcode = (isset($dealer_check[0]) and $dealer_check[0]['pcode'])?$dealer_check[0]['pcode']:'';
	$receipt_sub_query =" select GCR_ORDER_NO,max(GRD_CHEQUE_CLEARED_DATE) as receipt_date,GRD_RECEIPT_TYPE,god1.god_emp_id from gft_collection_receipt_dtl ".
			" join gft_receipt_dtl on (GCR_RECEIPT_ID=GRD_RECEIPT_ID) left join gft_order_hdr god1 on (god1.god_order_no=GCR_ORDER_NO) where GCR_ORDER_NO='$order_no' ";
	$receipt_res = execute_my_query($receipt_sub_query);
	$receipt_type = '0';
	if($row = mysqli_fetch_array($receipt_res)) {
		$receipt_type = $row['GRD_RECEIPT_TYPE'];
	}
	$allow_cust = true; // Make this false to disable automation
	if($invoice_to=='customer') {
		if($from_page=='data_patch') {
			$allow_cust = true;
		}
	}
	if(in_array($order_tax_mode,array('3','4')) and ($invoice_to=='partner' or $unique_pcode=='71' or (($receipt_type=='6' || $receipt_type=='16') and $order_emp=='9999') or $allow_cust)){ //GST Mode
	    if(($receipt_type=='6' || $receipt_type=='16') and $order_emp=='9999') {
			$from_page = 'store';
		}
		if($unique_pcode=='71') {
			$from_page = 'dealer_order';
		}
		$alliance_proforma_no_qry = execute_my_query(" select gph_order_no from gft_proforma_hdr ".
                		                    " join gft_add_on_commission_dtl on (GAC_ORDER_NO=gph_order_no and GAC_ORDER_REF_TYPE=2) ".
                		                    " where gph_converted_order_no='$order_no' ");
		if($approval_status=='1' and mysqli_num_rows($alliance_proforma_no_qry)==0) {
			return false;
		}
		return generate_gst_invoice($order_no,$invoice_to,$from_page,$invoice_date,false,$monthly_invoice);
	} else if(in_array($order_tax_mode,array('1','2')) and strtotime($order_date)<strtotime("01 July 2017")) {
	if( ($invoice_to=='') || ($order_no=='') ){
		return false;
	}
	/*$select_invoice_date_str = " ifnull(date(GOA_APPROVED_ON), GOD_ORDER_DATE) ";
	if($invoice_to=='customer'){
		$select_invoice_date_str = " if(GOD_ORDER_DATE > receipt_date, GOD_ORDER_DATE,receipt_date) ";
	}*/
	$select_invoice_date_str = ' "2017-06-30" ';
	$financial_year_start = ((int)date('m') < 4) ? date('Y-04-01',strtotime('-1 year')) : date('Y-04-01');
	$hdr_que=" select god.GOD_ORDER_AMT, god.GOD_BALANCE_AMT, god.GOD_EMP_ID, god.GOD_LEAD_CODE, god.GOD_TAX_MODE, god.god_invoice_status, GLH_LEAD_TYPE,god.GOD_PAYMENT_CODE, ".
			 " $select_invoice_date_str as inv_dt,GRD_RECEIPT_TYPE from gft_order_hdr god ".
			 " join gft_lead_hdr on (GLH_LEAD_CODE=god.GOD_LEAD_CODE) ".
			 " left join gft_order_approve_log on (GOA_ORDER_NO=god.GOD_ORDER_NO and GOA_STATUS_CHANGEDAS='Order Approved') ".
			 " left join ($receipt_sub_query) receipt_dtl on (god.GOD_ORDER_NO=receipt_dtl.GCR_ORDER_NO) ".
			 " where god.GOD_ORDER_NO='$order_no'";
	$hdr_res = execute_my_query($hdr_que);
	$payment_code = 0;
	if($hdr_data = mysqli_fetch_array($hdr_res)){
		$god_order_amt 	= (int)$hdr_data['GOD_ORDER_AMT'];
		$glh_lead_type 	= (int)$hdr_data['GLH_LEAD_TYPE'];
		$god_bal_amt	= (int)$hdr_data['GOD_BALANCE_AMT'];
		$tax_mode		= (int)$hdr_data['GOD_TAX_MODE'];
		$is_invoiced	= $hdr_data['god_invoice_status'];
		$receipt_type	= $hdr_data['GRD_RECEIPT_TYPE'];
		$ordered_emp_id = $hdr_data['GOD_EMP_ID'];
		//stopping the invoice generation for the following cases
		//1.Order Value is Zero,
		//2.Corporates(HQ),
		//3.Order has outstanding,
		//4.Export Tax Mode,
		//5.Already invoice raised
		if( ($god_order_amt==0) || (in_array($glh_lead_type,array(3,13)) && ($ordered_emp_id!='9999')) || ($god_bal_amt > 0) || ($tax_mode==3) || ($is_invoiced=='Y') ){
			return false;
		}
		$lead_code 		= $hdr_data['GOD_LEAD_CODE'];
		$against_cform 	= ($tax_mode==2)?'Y':'N';
		$invoice_date 	= $hdr_data['inv_dt'];
		$payment_code	= (int)$hdr_data['GOD_PAYMENT_CODE'];
	}else{
		return false; //order not found
	} 
	$wh_condition = "";
	if( ($invoice_to=='customer') && ($from_page!='saas_order') ){ //for independent customer currently only ASA invoice, Order from Store through EBS. Later when all invoices come, remove this condition
		$payment_cond='';
		$chk_que =" select GOP_PRODUCT_CODE from gft_order_product_dtl join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
				  " where GOP_ORDER_NO='$order_no' and  GFT_SKEW_PROPERTY not in (2,3,16,13,14) ";
		$chk_res = execute_my_query($chk_que);
		if(mysqli_num_rows($chk_res)==0){ //Order contains only additional client and upgradation skews 
			$payment_cond = " or GOD_PAYMENT_CODE=1 "; //100% advance payment
		}
		$wh_condition = " and (GFT_SKEW_PROPERTY in (4,15) or GOD_EMP_ID=9999 or gop_product_code='71' $payment_cond) ";
	}
	$sa_qty = $sa_pcode = $sa_pskew = $sa_orderno = $sa_list_price = $sa_coupon_hrs = /*. (string[int]) .*/array();
	$sa_sell_amt = $sa_s_tax_rate = $sa_s_tax_amt = $sa_serv_tax_rate = $sa_serv_tax_amt = $sa_net_amt = /*. (string[int]) .*/array();
	
	$ss_qty = $ss_pcode = $ss_pskew = $ss_orderno = $ss_list_price = $ss_coupon_hrs= /*. (string[int]) .*/array();
	$ss_sell_amt = $ss_s_tax_rate = $ss_s_tax_amt = $ss_serv_tax_rate = $ss_serv_tax_amt = $ss_net_amt = /*. (string[int]) .*/array();
	
	$selectq1 = " select GOP_ORDER_NO, GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GOP_QTY, GFT_SKEW_PROPERTY,GOP_SELL_AMT, ".
		" GOP_LIST_PRICE, GOP_SELL_RATE, GOP_SERVICE_TAX_RATE, GOP_TAX_RATE, GOP_SERVICE_TAX_AMT, GOP_TAX_AMT,GOP_COUPON_HOUR ".
		" from gft_order_hdr oh ".
		" join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) ".
		" join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
		" where GOD_ORDER_NO='$order_no' $wh_condition ";
	$result1 = execute_my_query($selectq1);
	while($row1 = mysqli_fetch_array($result1)){
		$sales_tax = (int)$row1['GOP_TAX_RATE'];
		$service_tax_amt = (int)$row1['GOP_SERVICE_TAX_AMT'];
		if($sales_tax > 0){
			$sa_qty[] 			= $row1['GOP_QTY'];
			$sa_coupon_hrs[]	= $row1['GOP_COUPON_HOUR'];			
			$sa_pcode[]			= $row1['GOP_PRODUCT_CODE'];
			$sa_pskew[]			= $row1['GOP_PRODUCT_SKEW'];
			$sa_orderno[] 		= $row1['GOP_ORDER_NO'];
			$sa_list_price[] 	= $row1['GOP_LIST_PRICE'];
			$sa_sell_amt[] 		= $row1['GOP_SELL_RATE'];
			$sa_s_tax_rate[] 	= $row1['GOP_TAX_RATE'];
			$sa_s_tax_amt[] 	= $row1['GOP_TAX_AMT'];
			$sa_serv_tax_rate[] = $row1['GOP_SERVICE_TAX_RATE'];
			$sa_serv_tax_amt[] 	= $row1['GOP_SERVICE_TAX_AMT'];
			$sa_net_amt[]		= $row1['GOP_SELL_AMT'];
		}else if($service_tax_amt > 0){
			$ss_qty[] 			= $row1['GOP_QTY'];
			$ss_coupon_hrs[]	= $row1['GOP_COUPON_HOUR'];
			$ss_pcode[]			= $row1['GOP_PRODUCT_CODE'];
			$ss_pskew[]			= $row1['GOP_PRODUCT_SKEW'];
			$ss_orderno[] 		= $row1['GOP_ORDER_NO'];
			$ss_list_price[] 	= $row1['GOP_LIST_PRICE'];
			$ss_sell_amt[] 		= $row1['GOP_SELL_RATE'];
			$ss_s_tax_rate[] 	= $row1['GOP_TAX_RATE'];
			$ss_s_tax_amt[] 	= $row1['GOP_TAX_AMT'];
			$ss_serv_tax_rate[] = $row1['GOP_SERVICE_TAX_RATE'];
			$ss_serv_tax_amt[] 	= $row1['GOP_SERVICE_TAX_AMT'];
			$ss_net_amt[]		= $row1['GOP_SELL_AMT'];
		}
	}
	$partial_invoice = false;
	if(count($sa_pcode) > 0){
		$uniq_arr = array_unique($sa_pcode);
		$invoice_type=1;
		$ac_ref_id = "";
		$ac_ref = "SA";
		if(count($uniq_arr)==1 and in_array("71",$uniq_arr)){
			$ac_ref = "DA";
		}else if($invoice_to=='partner'){
			$ac_ref = "CA";
		}else if( ($receipt_type=='6') || ($from_page=='saas_order') || ($payment_code==1) ){ //EBS
			$ac_ref = "OA";
		}else{
			$partial_invoice = true;
		}
		if(!$partial_invoice){
			$id_res = execute_my_query("select max(gih_invoice_ac_reffer_id) max_id from gft_invoice_hdr where gih_invoice_date >= '$financial_year_start' and gih_invoice_ac_reffer_id like '$ac_ref%' having max_id is not null ");
			if(mysqli_num_rows($id_res)==0){
				$ac_ref_id = $ac_ref."0001";
			}else if($querydata=mysqli_fetch_array($id_res)){
				$max_id = (int)preg_replace("/[^0-9]/","",$querydata['max_id']);
				$max_id=$max_id+1;
				$ac_ref_id = $ac_ref."".substr("0000".$max_id,-4);
			}
// 			insert_and_generate_invoice($lead_code, $ac_ref_id,$invoice_type,$sa_qty,$sa_pcode,$sa_pskew,$sa_orderno,$sa_list_price,$sa_sell_amt,$sa_s_tax_rate, $sa_s_tax_amt, $sa_serv_tax_rate, $sa_serv_tax_amt, $ordered_emp_id, $against_cform,$invoice_date,$sa_coupon_hrs,null,$sa_net_amt);
		}
	}
	if(count($ss_pcode) > 0){
		$uniq_ss_pcode = array_unique($ss_pcode);
		$invoice_type=2;
		$ac_ref_id = "";
		$ac_ref = "SS";
		if(count($uniq_ss_pcode)==1 and in_array("71",$uniq_ss_pcode)){
			$ac_ref = "DS";
		}else if($invoice_to=='partner'){
			$ac_ref = "CS";
		}else if($receipt_type=='6'){ //EBS
			$ac_ref = "OS";
		}
		$id_res = execute_my_query("select max(gih_invoice_ac_reffer_id) max_id from gft_invoice_hdr where gih_invoice_date >= '$financial_year_start' and gih_invoice_ac_reffer_id like '$ac_ref%' having max_id is not null ");
		if(mysqli_num_rows($id_res)==0){
			$ac_ref_id = $ac_ref."0001";
		}else if($querydata=mysqli_fetch_array($id_res)){
			$max_id = (int)preg_replace("/[^0-9]/","",$querydata['max_id']);
			$max_id=$max_id+1;
			$ac_ref_id = $ac_ref."".substr("0000".$max_id,-4);
		}
// 		insert_and_generate_invoice($lead_code, $ac_ref_id,$invoice_type,$ss_qty,$ss_pcode,$ss_pskew,$ss_orderno,$ss_list_price,$ss_sell_amt,$ss_s_tax_rate, $ss_s_tax_amt, $ss_serv_tax_rate, $ss_serv_tax_amt, $ordered_emp_id, $against_cform,$invoice_date,$ss_coupon_hrs,null,$ss_net_amt);
	}
	if( (count($sa_pcode) > 0) || (count($ss_pcode) > 0) ){
		$upd_invoice_status='Y';
		if($partial_invoice){
			$upd_invoice_status='S';
		} 
		execute_my_query("update gft_order_hdr set god_invoice_status='$upd_invoice_status' where god_order_no='$order_no'");
	}
	return true;
	} else {
		return false;
	}
}
/**
 *
 * @return string[]|array[][]
 */
function invoice_audit_dtl(){
    global $month_year;
    $return_arr = array();
    $mn_yr_arr = explode("-", $month_year);
    $mn_val = $mn_yr_arr[0];
    $year_val = isset($mn_yr_arr[1])?$mn_yr_arr[1]:date('Y');
    $start_date = $year_val."-".$mn_val."-01";
    $end_date = date("Y-m-t", strtotime($start_date));
    $only_rec_cond = " and GRD_RECEIPT_TYPE not in (11) and GRD_CHECKED_WITH_LEDGER='Y' ";
    $common_receipt_cond = " $only_rec_cond and GRD_CHEQUE_CLEARED_DATE between '$start_date' and '$end_date' ";
    
    $previous_month_invoice_qry =" select GIP_ORDER_NO, round(sum(gip_amount)) as inv_amt, sum(DISTINCT gih_discount_amount) as pmda from gft_invoice_hdr ".
        " join gft_invoice_product_dtl on (gih_invoice_id=gip_invoice_id)  ".
        " join gft_lead_hdr on (GLH_LEAD_CODE=GIH_LEAD_CODE and GLH_COUNTRY='India') ".
        " where gih_invoice_date<'$start_date' and gih_status='A' group by GIP_ORDER_NO";
    $current_month_collection_qry = " select GCR_ORDER_NO,round(sum(GCR_AMOUNT)) as coll_amt from gft_collection_receipt_dtl ".
        " join gft_receipt_dtl on (GRD_RECEIPT_ID=GCR_RECEIPT_ID)  ".
        " join gft_lead_hdr on (GLH_LEAD_CODE=GRD_LEAD_CODE and GLH_COUNTRY='India') ".
        " where 1  $common_receipt_cond group by GCR_ORDER_NO";
    $previous_month_collection_qry =" select GCR_ORDER_NO,round(sum(GCR_AMOUNT)) as coll_amt,group_concat(GRD_CHEQUE_CLEARED_DATE,' : ',round(GCR_AMOUNT) order by GRD_CHEQUE_CLEARED_DATE) realized_dates from gft_collection_receipt_dtl ".
        " join gft_receipt_dtl on (GRD_RECEIPT_ID=GCR_RECEIPT_ID)  ".
        " join gft_lead_hdr on (GLH_LEAD_CODE=GRD_LEAD_CODE) ".
        " where 1  $only_rec_cond ".
        " and GRD_CHEQUE_CLEARED_DATE < '$start_date' group by GCR_ORDER_NO";
    $total_invoice_month = "  select gih_invoice_id Invoice_id,group_concat(DISTINCT gih_invoice_ac_reffer_id,'- Amt: ',gih_net_invoice_amount SEPARATOR '<br />') gih_invoice_ac_reffer_id,".
        " cmi.GIP_ORDER_NO,GIH_LEAD_CODE, GLH_CUST_NAME, GLE_GST_NO,GLH_CUST_STATECODE,GLH_COUNTRY, ".
        " round(sum(gip_amount)) as current_month_invoice, sum(DISTINCT gih_discount_amount) as cmda, pmda , ".
        " MAX(pmi.inv_amt) as previous_month_invoice,".
        " MAX(cmc.coll_amt) as current_month_collection,  ".
        " MAX(pmc.coll_amt) as previous_month_collection,pmc.realized_dates from gft_invoice_hdr ".
        " join gft_invoice_product_dtl cmi on (gih_invoice_id=gip_invoice_id) ".
        " join gft_lead_hdr on (GLH_LEAD_CODE=GIH_LEAD_CODE) ".
        " join gft_lead_hdr_ext on (GLH_LEAD_CODE=GLE_LEAD_CODE)".
        " left join ($previous_month_invoice_qry) pmi ON(pmi.GIP_ORDER_NO=cmi.GIP_ORDER_NO)".
        " left join ($current_month_collection_qry) cmc ON(cmc.GCR_ORDER_NO=cmi.GIP_ORDER_NO)".
        " left join ($previous_month_collection_qry)pmc ON (pmc.GCR_ORDER_NO=cmi.GIP_ORDER_NO) ".
        " where gih_invoice_date between '$start_date' and '$end_date' and gih_status='A' group by GIP_ORDER_NO";
    $result = execute_my_query($total_invoice_month);
    $invoice_pmc_arr = array();
    $invoice_cmc_arr = array();
    $invoice_woc_arr = array();
    $invoice_without_collection_tot = 0;
    $invoice_with_same_month_tot = 0;
    $invoice_with_pre_month_coll_tot = 0;
    while ($row=mysqli_fetch_assoc($result)) {
        $cmi = (int)$row['current_month_invoice'];
        $pmi = (int)$row['previous_month_invoice'];
        $cmc = (int)$row['current_month_collection'];
        $pmc = (int)$row['previous_month_collection'];
        if($row['cmda']>0){
            $cmi = $cmi - ($row['cmda']+round($row['cmda']*0.18));
        }
        if($row['pmda']>0){
            $pmi = $pmi - ($row['pmda']+round($row['pmda']*0.18));
        }
        $invoice_id = $row['Invoice_id'];
        $invoice_ref_id = $row['gih_invoice_ac_reffer_id'];
        $order_no = $row['GIP_ORDER_NO'];
        $lead_code = $row['GIH_LEAD_CODE'];
        $customer_name = $row['GLH_CUST_NAME'];
        $realized_dates = $row['realized_dates'];
        $invoice_without_collection = 0;
        $invoice_with_same_month = 0;
        $invoice_with_pre_month_coll = 0;
        $previous_month_advance = $pmi-$pmc;
        if($previous_month_advance==0){
            if($cmc==0){
                $invoice_without_collection = $invoice_without_collection+$cmi;
            }else if($cmc>=$cmi){
                $invoice_with_same_month = $invoice_with_same_month+$cmi;
            }else if($cmc<$cmi){
                $invoice_with_same_month = $invoice_with_same_month+($cmc);
                $invoice_without_collection = $invoice_without_collection+($cmi-$cmc);
            }
        }else if($previous_month_advance<0){ //Extra advance available
            $previous_month_advance = abs($previous_month_advance);
            $invoice_with_pre_month_coll = $invoice_with_pre_month_coll+
            (($previous_month_advance-$cmi)<=0?($previous_month_advance):(0));
            $invoice_with_pre_month_coll = $invoice_with_pre_month_coll+
            (($previous_month_advance-$cmi)>0?($cmi):(0));
            $required_coll = ($previous_month_advance-$cmi);
            if(($required_coll)<0)
            {
                $required_coll = abs($required_coll);
                $invoice_with_same_month = $invoice_with_same_month+(($cmc-$required_coll)>=0?($required_coll):(0));
                $invoice_without_collection = $invoice_without_collection+(($cmc-$required_coll)<0?($required_coll-$cmc):(0));
                $invoice_with_same_month = $invoice_with_same_month+(($cmc-$required_coll)<0?($cmc):(0));
            }else if($required_coll>0){
                //$invoice_with_same_month = $invoice_with_same_month+$cmi;
                //$invoice_without_collection = $invoice_without_collection+($cmi-$cmc>0?($cmi-$cmc):(0));
            }
        }else if($previous_month_advance>0){ // Advance required for previous month invoice
            if($cmc==0){
                $invoice_without_collection = $invoice_without_collection+$cmi;
            }else{
                $balance = $cmc-$previous_month_advance;
                if($balance<=0){
                    $invoice_without_collection = $invoice_without_collection+$cmi;
                }else {
                    $invoice_with_same_month = $invoice_with_same_month+(($balance-$cmi)>=0?($cmi):(0));
                    $invoice_with_same_month = $invoice_with_same_month+(($balance-$cmi)<0?($balance):(0));
                    $invoice_without_collection = $invoice_without_collection+(($balance-$cmi)<0?(($cmi-$balance)):(0));;
                }
            }
        }
        $invoice_without_collection_tot = $invoice_without_collection_tot+$invoice_without_collection;
        $invoice_with_same_month_tot = $invoice_with_same_month_tot+$invoice_with_same_month;
        $invoice_with_pre_month_coll_tot = $invoice_with_pre_month_coll_tot+$invoice_with_pre_month_coll;
        $invoice_dtl_arr = array(
            'invoice_id' =>$invoice_id,
            'customer_id' =>$lead_code,
            'customer_name' =>$customer_name,
            'gstin_no'=>$row['GLE_GST_NO'],
            'order_no' =>$order_no,
            'invoice_ref_id'=>$invoice_ref_id,
            'country'=>$row['GLH_COUNTRY'],
            'state'=>$row['GLH_CUST_STATECODE']
        );
        if($invoice_with_pre_month_coll>0){
            $invoice_pmc_arr[] = array_merge($invoice_dtl_arr,array('amount' =>$invoice_with_pre_month_coll,'realized_date'=>$realized_dates));
        }
        if($invoice_with_same_month>0){
            $invoice_cmc_arr[] = array_merge($invoice_dtl_arr,array('amount' =>$invoice_with_same_month));
        }
        if($invoice_without_collection>0){
            $invoice_woc_arr[] = array_merge($invoice_dtl_arr,array('amount' =>$invoice_without_collection));
        }
    }
    $return_arr[0] = round($invoice_with_pre_month_coll_tot);
    $return_arr[1] = round($invoice_with_same_month_tot);
    $return_arr[2] = round($invoice_without_collection_tot);
    $return_arr[3] = $invoice_pmc_arr;
    $return_arr[4] = $invoice_cmc_arr;
    $return_arr[5] = $invoice_woc_arr;
    return $return_arr;
}
?>
