<?php
require_once(__DIR__ ."/function.insert_stmt_order.php");
require_once(__DIR__."/function.update_in_hdr.php");
require_once(__DIR__ ."/call_center/update_current_status_cc.php");
$reinstallation_charges=0;
$installed_by_id_given=0;
/**
 * @param string $orderno
 * @param string $head_of_family
 * @param int $fullfillment_no
 * 
 * @return boolean
 */
function check_for_dublicate_license($orderno,$head_of_family,$fullfillment_no){
	$query="select gid_order_no from gft_install_dtl_new " .
			"where gid_order_no='$orderno' and GID_HEAD_OF_FAMILY='$head_of_family' " .
			"and GID_FULLFILLMENT_NO='$fullfillment_no' and GID_REF_SERIAL_NO=1 " ;
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)==0){ return true;}else { return false; }		
}

/**
 * @param string $orderno
 * @param string $productcode
 * @param string $productskew
 * @param string $lead_code
 * @param string $subscription_skew
 *
 * @return void
 */
function update_in_cp_order_dtl($orderno,$productcode,$productskew,$lead_code,$subscription_skew=null){
	$query="update gft_order_product_dtl set gop_usedqty=gop_usedqty+1 where gop_order_no='$orderno '" .
	" and  gop_product_code='$productcode' and gop_usedqty < gop_qty " .
	" and (gop_product_skew='$productskew' ".($subscription_skew!=null?" or gop_product_skew='$subscription_skew'":"").")";
	$result=execute_my_query($query,'',true,false);
	$query2=" update gft_cp_order_dtl t1,gft_order_hdr lh  set gco_usedqty=gco_usedqty+1 " .
			" where GCO_ORDER_NO ='$orderno' and GCO_PRODUCT_CODE='$productcode' " .
			" and (GCO_SKEW='$productskew'".($subscription_skew!=null?" or GCO_SKEW='$subscription_skew'":"").") " .
			" and GCO_CUST_CODE=$lead_code and gco_cust_qty>gco_usedqty ".
			" and gco_order_no=god_order_no and god_order_splict=1  ";
	execute_my_query($query2,'',true,false);
	update_client_used_qty($orderno,$productcode,$lead_code);
}


/**
 * @param string $lead_code
 * @param string $orderno
 * @param string $productcode
 * @param string $head_of_family
 * @param int $fullfillment_no
 * @param string $productskew
 * @param string $install_id
 *
 * @return void
 */
function advance_asa_order_update($lead_code,$orderno,$productcode,$head_of_family,$fullfillment_no,$productskew,$install_id){
	$query= "SELECT GOD_ORDER_DATE,GOD_EMP_ID,op.GOP_ORDER_NO, op.GOP_PRODUCT_CODE, op.GOP_PRODUCT_SKEW, pm.GPM_DEFAULT_ASS_PERIOD,GFT_SKEW_PROPERTY,GOP_SELL_AMT " .
			" FROM gft_order_product_dtl op join gft_product_master pm on (pm.gpm_product_code=op.GOP_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW and GFT_SKEW_PROPERTY in (4,15))" .
			" join gft_order_hdr on(god_order_no=op.GOP_ORDER_NO)" .
			" WHERE op.GOP_ORDER_NO='$orderno' AND op.GOP_PRODUCT_CODE=$productcode and GPM_REFERER_SKEW='$productskew' ";
	$result=execute_my_query($query);
	if($data=mysqli_fetch_array($result)){
		$GOD_ORDER_NO=$data['GOP_ORDER_NO'];
		$ass_product_skew=$data['GOP_PRODUCT_SKEW'];
		$GOP_SELL_AMT=$data['GOP_SELL_AMT'];
		$ASS_START_DATE=date('Y-m-d');
		$ASS_END_DATE=date('Y-m-d',mktime("0","0","0",date('m'),date('d')+$data['GPM_DEFAULT_ASS_PERIOD'],date('Y')));
		$asa_type=($data['GFT_SKEW_PROPERTY']==4?1:3);
		$god_emp_id=$data['GOD_EMP_ID'];
		$GOD_ORDER_DATE=$data['GOD_ORDER_DATE'];
		execute_my_query("update gft_install_dtl_new set " .
				" GID_PREV_EXPIRY_DATE=gid_install_date, GID_VALIDITY_DATE ='$ASS_END_DATE', GID_ASS_ID='$orderno',GID_EXPIRE_FOR=$asa_type " .
				" where GID_ORDER_NO='$orderno' and GID_LEAD_CODE=$lead_code and GID_HEAD_OF_FAMILY=$head_of_family and GID_FULLFILLMENT_NO=$fullfillment_no " .											
				" and GID_STATUS='A' and GID_INSTALL_ID=$install_id ");
		execute_my_query("replace into gft_ass_dtl (GAD_ASS_DATE, GAD_ASS_START_DATE, GAD_ASS_END_DATE, GAD_EMP_ID, GAD_PRODUCT_SKEW,GAD_PRODUCT_CODE, GAD_ASS_ORDER_NO,GAD_INS_REFF ) " .
			" values('$GOD_ORDER_DATE','$ASS_START_DATE','$ASS_END_DATE','$god_emp_id','$ass_product_skew','$productcode','$GOD_ORDER_NO','$install_id')" );
		$order_splict_query="SELECT god_lead_code FROM gft_order_hdr oh join gft_order_product_dtl op on (gop_order_no=god_order_no) " .
				" WHERE oh.GOD_ORDER_NO='$orderno' and oh.god_order_splict=1 and op.gop_product_code=$productcode and op.gop_product_skew='$ass_product_skew' and god_order_status='A'";
		$order_sp_result=execute_my_query($order_splict_query);
		if($order_sp_data=mysqli_fetch_array($order_sp_result)){
			$ordered_lead_code=$order_sp_data['god_lead_code'];
			insert_stmt_for_split_order_dtl($ordered_lead_code,$lead_code,1,$orderno,$productcode,$ass_product_skew,$ASS_START_DATE,$ASS_START_DATE,SALES_DUMMY_ID);
		}
		update_in_cp_order_dtl($orderno,$productcode,$ass_product_skew,$lead_code);
	}
	
}
/**
 * @return void
 */
function update_encrypt_product_id_master(){
	$query="select gpm_product_code,gpm_product_skew from gft_product_master ";//where GPM_ENCRYPTED_PRODID='' ";
	$result=execute_my_query($query,'',true,false);
	while($qdata=mysqli_fetch_array($result)){
		$product_code=$qdata[0];
		$skew=$qdata[1];
		$product_key1=$product_code.$skew;
		$pskew0=substr($product_key1,0,5);
		$pskew1=substr($product_key1,6,3);
		$ProdID_pc=$pskew0.$pskew1;
		$eprodcode=do_encryption_prodid($ProdID_pc);
		$query_update="update gft_product_master set GPM_ENCRYPTED_PRODID='$eprodcode' " .
				"where gpm_product_code='$product_code' and gpm_product_skew='$skew' ";
		execute_my_query($query_update,'',true,false);
	
	}
}/***end**/


/**
 * using in function.insert_stmt_order.php 
 *
 * @param string $orderno
 * @param string $productcode
 * @param string $lead_code
 * @param string $install_id
 *
 * @return void 
 */
function update_client_used_qty($orderno,$productcode,$lead_code,$install_id=''){
	$query_ins_client="SELECT GID_ORDER_NO, GID_PRODUCT_CODE, GID_PRODUCT_SKEW, GID_FULLFILLMENT_NO, GID_NO_CLIENTS, GID_NO_COMPANYS, " .
			" GID_LIC_ORDER_NO, GID_LIC_PCODE, GID_LIC_PSKEW, GPM_CLIENTS, GPM_COMPANYS,GFT_SKEW_PROPERTY,GID_PREV_EXPIRY_DATE, GID_VALIDITY_DATE " .
			" FROM gft_install_dtl_new join gft_product_master on(GID_PRODUCT_CODE=GPM_PRODUCT_CODE and GID_PRODUCT_SKEW=GPM_PRODUCT_SKEW)" .
			" WHERE GID_ORDER_NO='$orderno' AND GID_PRODUCT_CODE=$productcode AND GID_LEAD_CODE=$lead_code ";
	if($install_id!=''){
		$query_ins_client.=" AND GID_INSTALL_ID=$install_id ";
	}
			
	 $result_ins_client=execute_my_query($query_ins_client);
	 while(mysqli_num_rows($result_ins_client)==1 and $data_ins_client=mysqli_fetch_array($result_ins_client)){
	 	if($data_ins_client['GID_LIC_PCODE']==$data_ins_client['GID_PRODUCT_CODE']){
	 		$default_clients=$data_ins_client['GPM_CLIENTS'];
			$default_companys=$data_ins_client['GPM_COMPANYS'];
	 		$no_of_installed_clents=$data_ins_client['GID_NO_CLIENTS'];
	 		$no_of_installed_company=$data_ins_client['GID_NO_COMPANYS'];
	 		$installed_current_pcode=$data_ins_client['GID_LIC_PCODE'];
	 		$installed_current_pskew=$data_ins_client['GID_LIC_PSKEW'];
	 		$installed_current_order_no=$data_ins_client['GID_LIC_ORDER_NO'];
	 		$installed_current_skew_property=$data_ins_client['GFT_SKEW_PROPERTY'];
	 		$prev_expiry_date=$data_ins_client['GID_PREV_EXPIRY_DATE'];
			$cur_expiry_date=$data_ins_client['GID_VALIDITY_DATE'];
	 		$client_skew_property=($installed_current_skew_property==1?3:($installed_current_skew_property==11?16:''));
	 		$company_skew_property=($installed_current_skew_property==1?14:($installed_current_skew_property==11?13:''));
	 		$no_client_query=" SELECT GOP_ORDER_NO,GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GOP_QTY, GOP_USEDQTY, god_order_splict,GFT_SKEW_PROPERTY " .
	 				"from ( (SELECT GOP_ORDER_NO,GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GOP_QTY, GOP_USEDQTY, gh.god_order_splict,GFT_SKEW_PROPERTY " .
	 				" FROM gft_order_product_dtl g " .
	 				" join gft_order_hdr gh on (g.GOP_ORDER_NO=gh.GOD_ORDER_NO AND gh.GOD_ORDER_STATUS='A' and god_order_splict=0 )" .
	 				" join gft_product_master on(GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW)" .
	 				" where GOP_PRODUCT_CODE =$productcode and GOD_LEAD_CODE=$lead_code  " .
	 				($installed_current_skew_property==1?" and GFT_SKEW_PROPERTY in ($client_skew_property,$company_skew_property) ":"") .
	 				($installed_current_skew_property==11?" and GFT_SKEW_PROPERTY in ($client_skew_property,$company_skew_property) and GOP_ORDER_NO='$installed_current_order_no' and gop_start_date='0000-00-00' and gop_ass_end_date='0000-00-00' ":"") .
	 				" )union all (" .
	 				" select GCO_ORDER_NO, GCO_PRODUCT_CODE,GCO_SKEW,GCO_CUST_QTY, GCO_USEDQTY,gh.god_order_splict,GFT_SKEW_PROPERTY " .
	 				" from gft_order_product_dtl g " .
	 				" join gft_order_hdr gh on (g.GOP_ORDER_NO=gh.GOD_ORDER_NO AND gh.GOD_ORDER_STATUS='A' and god_order_splict=1 )" .
	 				" join gft_product_master on(GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW)" .
	 				" join gft_cp_order_dtl on (GOP_ORDER_NO=GCO_ORDER_NO and GOP_PRODUCT_CODE=GCO_PRODUCT_CODE and GOP_PRODUCT_SKEW=GCO_SKEW ) " .
	 				" where GOP_PRODUCT_CODE =$productcode and GCO_CUST_CODE=$lead_code " .
	 				($installed_current_skew_property==1?" and GFT_SKEW_PROPERTY in ($client_skew_property,$company_skew_property) ":"") .
	 				($installed_current_skew_property==11?" and GFT_SKEW_PROPERTY in ($client_skew_property,$company_skew_property) and GOP_ORDER_NO='$installed_current_order_no' and gco_start_date='0000-00-00' and gco_end_date='0000-00-00' ":"") .
	 				" ) ) a ";
	 		$result_no_client=execute_my_query($no_client_query);
	 		$order_clients=0;$used_clients=0;
	 		while($data_no_clients=mysqli_fetch_array($result_no_client)){
	 			$client_order_no=$data_no_clients['GOP_ORDER_NO'];
	 			$client_pcode=$data_no_clients['GOP_PRODUCT_CODE'];
	 			$client_pskew=$data_no_clients['GOP_PRODUCT_SKEW'];
	 			$order_qty=(int)$data_no_clients['GOP_QTY'];
				$used_qty=(int)$data_no_clients['GOP_USEDQTY'];
				$order_split=$data_no_clients['god_order_splict'];
				$skew_property=$data_no_clients['GFT_SKEW_PROPERTY'];
				$order_clients+=$order_qty;
				$used_clients+=$used_qty;
	 			if($order_split==1){
	 				$subscription_update='';
	 				if($installed_current_skew_property==11){
	 					$subscription_update=", gco_start_date='$prev_expiry_date',gco_end_date='$cur_expiry_date'";	
	 				}
					execute_my_query("update gft_cp_order_dtl set GCO_USEDQTY=GCO_CUST_QTY $subscription_update where GCO_CUST_CODE=$lead_code and GCO_ORDER_NO='$client_order_no' and GCO_PRODUCT_CODE=$client_pcode and GCO_SKEW='$client_pskew' ");
					execute_my_query("update  gft_order_product_dtl set GOP_USEDQTY=(select sum(GCO_USEDQTY) from gft_cp_order_dtl where " .
							" GOP_ORDER_NO=GCO_ORDER_NO and GOP_PRODUCT_CODE=GCO_PRODUCT_CODE and GOP_PRODUCT_SKEW=GCO_SKEW  " .
							" group by GOP_ORDER_NO, GOP_PRODUCT_CODE ,GOP_PRODUCT_SKEW) where GOP_ORDER_NO='$client_order_no' and GOP_PRODUCT_CODE=$client_pcode and GOP_PRODUCT_SKEW='$client_pskew' ");
	 			}else{
	 				$subscription_update='';
	 				if($installed_current_skew_property==11){
	 					$subscription_update=", gop_start_date='$prev_expiry_date',gop_ass_end_date='$cur_expiry_date'";	
	 				}
					execute_my_query("update gft_order_product_dtl set GOP_USEDQTY=GOP_QTY $subscription_update where GOP_ORDER_NO='$client_order_no' and GOP_PRODUCT_CODE=$client_pcode and GOP_PRODUCT_SKEW='$client_pskew' ");
	 			}
	 		}
		}
	}
}


/**
 * @param string $eprodcode0_sn
 * @param string $eprodcode1_sn
 * 
 * @return string[int]
 */
function get_product_dtl_frm_eproductid($eprodcode0_sn,$eprodcode1_sn){
 	// ***insert encrupt key in product_master***
	update_encrypt_product_id_master();
	$product_id=$eprodcode0_sn.$eprodcode1_sn;
	$find_product_skew="select pm.gpm_product_code,pm.gpm_product_skew,pm.gpm_skew_desc," .
			" pfm.gpm_product_abr,gpt_type_name ,gpm_head_family " .
			" from gft_product_master pm,gft_product_family_master pfm,gft_product_type_master ptm " .
			" where pm.gpm_product_code=pfm.gpm_product_code " .
			" and gpt_type_id=gpm_product_type " .
			" and GPM_ENCRYPTED_PRODID='$product_id' ";
	global $test;
	if($test){
		echo "<br>query ".$find_product_skew;
	}		
	$result=execute_my_query($find_product_skew,'',true,false);
	$qdata=mysqli_fetch_array($result);
	$product_dtl[0]=$qdata['gpm_product_code'];
	$product_dtl[1]=$qdata['gpm_product_skew'];
	if($qdata['gpm_product_abr']!='' and $qdata['gpm_skew_desc']!=''){
	$product_dtl[2]=$qdata['gpm_product_abr'].'-'.$qdata['gpm_skew_desc'].'-'.$qdata['gpt_type_name'] ." Edition ";
	}else {
		$product_dtl[2]="";
	}
	$product_dtl[3]=$qdata['gpm_head_family'];
	return $product_dtl;
}

/**
 * @param string $exec_id
 * @param string $zone_mgr_id
 * @param string $regional_mgr_id
 * @param string $terr_mgr_id
 * @param int $zone_id
 * @param int $region_id
 * @param int $terr_id
 * @param int $country_id
 * @param int $state_id
 * @param int $district_id
 * @param string $from_dt
 * @param string $to_dt
 * @param string $topic
 * @param string $pcode
 * @param string $cust_name
 * @param string $chk_order
 * @param string $chk_install
 * @param string $emp_code
 * @param string $order_no
 * @param string $sortbycol
 * @param string $sorttype
 *
 * @return void
 */
function show_clientinstall_dtl($exec_id,$zone_mgr_id,$regional_mgr_id,$terr_mgr_id,$zone_id,$region_id,
		$terr_id,$country_id,$state_id,$district_id,$from_dt,$to_dt,
		$topic,$pcode,$cust_name,$chk_order,$chk_install,$emp_code,$order_no,
		$sortbycol,$sorttype){
	$query='';
	$uid=$_SESSION['uid'];
	$query="";
	$query_from_dt='';
	$query_to_dt='';
	if($from_dt!='' and $to_dt!=''){
		$date_c =$from_dt;
		$newVar = explode("-", $date_c);
		$query_from_dt=date("Y-m-d", mktime(0, 0, 0,$newVar[1],$newVar[0],$newVar[2]));
		$date_c =$to_dt;
		$newVar = explode("-", $date_c);
		$query_to_dt=date("Y-m-d", mktime(0, 0, 0,$newVar[1],$newVar[0],$newVar[2]));
	}
	global $me,$address_fields,$query_contact_dtl,$export_address_fields,$area_id,$unknown_territory,$cmbRegion_multi;
	$alt_row_class=array("oddListRow","evenListRow","highlight_worker_left");
	$select_query="select em.GEM_EMP_NAME as emp_name,id1.GCD_CINSTALL_DATE as date_of_install," .
			" GLH_LEAD_CODE as leadcode,glh_territory_id, " .
			" id1.GCD_ORDER_NO as order_no, GCD_QTY," .
			" pfm.GPM_PRODUCT_ABR as prod_abr,id1.GCD_PRODUCT_SKEW as skew,pm.gpm_skew_desc as skew_desc," .
			" id1.GCD_PRODUCT_VERSION as version,GLH_LEAD_CODE, oh.GOD_ORDER_DATE as order_date" .
			" $address_fields, em.gem_status as emp_status ";

	$export_query="select em.GEM_EMP_NAME as emp_name,Date_format(id1.GCD_CINSTALL_DATE,'%d-%b-%Y') as date_of_install," .
			" lh.GLH_LEAD_CODE Customer_Id $export_address_fields, id1.GCD_ORDER_NO as order_no,oh.GOD_ORDER_DATE as order_date, " .
			" pfm.GPM_PRODUCT_ABR as prod_abr,pm.gpm_skew_desc as skew_desc,GCD_QTY," .
			" id1.GCD_PRODUCT_VERSION as version ";

	$query.=" from (gft_emp_master em,gft_clientinstall_dtl id1,gft_order_hdr oh,gft_lead_hdr lh
	,gft_product_family_master pfm,gft_product_master pm) $query_contact_dtl ";

	if($terr_id!=0 or $area_id!=0 or $region_id!=0 or $zone_id!=0 or $district_id!=0 or $state_id!=0 or $unknown_territory=='on' or count($cmbRegion_multi)>0){
		$query.=get_query_area_mapping($terr_id,$area_id,$region_id,$zone_id,$district_id,$state_id,$country_id,'','','lh.glh_territory_id','lh.glh_district_id');
	}

	if($exec_id!=0 or $terr_mgr_id!=0  or $regional_mgr_id!=0 or $zone_mgr_id!=0 or is_authorized_group_list($uid,array(5,6))){
		$query.=get_query_reporting_under();
	}
	$query.=" where em.gem_emp_id=god_emp_id and oh.god_order_no=id1.gcd_order_no and lh.GLH_LEAD_CODE=gcd_lead_code  " .
			" and  pfm.GPM_PRODUCT_CODE=id1.GCD_PRODUCT_CODE and pm.gpm_product_code=pfm.gpm_product_code " .
			" and id1.gcd_product_skew=pm.gpm_product_skew ";
	$query.=check_customer_dtl();

	if($from_dt!='' and $to_dt!='' and $cust_name==''){
		$query.=" and id1.GCD_CINSTALL_DATE >= '$query_from_dt' and id1.GCD_CINSTALL_DATE <= '$query_to_dt'";
	}
	if($order_no!=""){
		$query.=" and gcd_order_no like '%$order_no%' ";
	}
	if($emp_code){
		$query.=" and gcd_emp_id='$emp_code' ";
	}
	if($pcode!=0){ $query.=" and id1.gcd_product_code='$pcode' ";	}
	$query.=" group by glh_lead_code ";
	if($sortbycol==''){
		$query.= " ORDER BY em.GEM_EMP_NAME ";
		$sortbycol = "em.GEM_EMP_NAME ";
		$sorttype = '2';
	}else{
		$query.= " ORDER BY $sortbycol ".($sorttype=='2'?"DESC ":" ");
	}
	$result=execute_my_query($select_query.$query);
	$count_num_rows=mysqli_num_rows($result);
	$r_query=$export_query.$query;

	$nav_struct=get_dtable_navigation_struct($count_num_rows);

	$heading=implode(",",array("Order Closed by","Install date","Order No ","Order date","Cust Name",
			"Location","Authority Name","Product","Skew","Quantity","Version"));
	print_dtable_navigation($count_num_rows,$nav_struct,$me,"export_all_report.php",$r_query,$heading,$sp=null,1,true,null,null,$sms_category=84,$email_category=84);
	$myarr=array("S.No","Customer ID","Install Date","Order No","Order date","Customer Name","Location","Product","Quantity","Version");
	$mysort=array("","GLH_LEAD_CODE","GCD_CINSTALL_DATE","god_order_no","god_order_date","glh_cust_name","glh_cust_streetaddr2",
			"pfm.gpm_product_abr","GCD_QTY","GCD_PRODUCT_VERSION");
	echo<<<END
<table cellpadding="0" cellspacing="2" width="100%" border="0" class="FormBorder1">
END;
		
	sortheaders($myarr,$mysort,$nav_struct,$sortbycol,$sorttype );
	$s=0;
	$id="";
	$start_of_row=(int)$nav_struct['start_of_row'];
	$MAX_NO_OF_ROWS=(int)$nav_struct['records_per_page'];
	if($count_num_rows){
		mysqli_data_seek($result,$start_of_row);
		$i=0;
		$sl=0;
		while( ($query_data=mysqli_fetch_array($result)) and $MAX_NO_OF_ROWS > $sl ){
			$sl++;
			$name=$query_data['emp_name'];
			$date_i=$query_data['date_of_install'];
			$date_o=$query_data['order_date'];
			$order_number=$query_data['order_no'];
			$leadcode=$query_data['leadcode'];
			$customername=wordwrap($query_data['GLH_CUST_NAME'],20,"<br />\n",1);
			$location=$query_data['GLH_CUST_STREETADDR2'];
			$skew=$query_data['skew'];
			$product=$query_data['prod_abr']."-".$query_data['skew_desc'];
			$version=$query_data['version'];
			$emp_status=$query_data['emp_status'];
			$lead_code=$query_data['GLH_LEAD_CODE'];
			$territory_id=$query_data['glh_territory_id'];
			$quantity=$query_data['GCD_QTY'];
			$order_number.="&nbsp;&nbsp;<a href=\"javascript:call_popup('order_releated_details.php?order_no=$order_number&lcode=$lead_code',7);\" " .
			"title=\"Order Details \">[O]</a>";
			$tooltip=get_necessary_data_from_query_for_tooltip($query_data);
			$customername="<a onMouseover=\"ddrivetip('{$tooltip}','#EFEFEF', 200);\" onMouseout=\"hideddrivetip();\" " .
			" href=\"javascript:call_popup('edit_cust_details.php?lcode=$leadcode&amp;call_from=popup&amp;uid=$uid',7)\" class=\"subtle\">$customername</a>  " .
			"<a href=\"javascript:call_popup('supporthistory.php?call_from=popup&history_type=3&from_dt=01-11-2004&custCode=$leadcode',7);\" class=\"subtle\"><img alt=\"\" src=\"images/history.jpeg\" border=0 width=20 height=20></a>";
			$value_arr[0]=array($sl,$lead_code,$date_i,$order_number,$date_o,$customername,$location,
					$product,$quantity,$version);
			print_resultset($value_arr);
		}//End of while
	}//End of if
	echo "</table>";
}

/**
 * @param string $exec_id
 * @param string $zone_mgr_id
 * @param string $regional_mgr_id
 * @param string $terr_mgr_id
 * @param int $zone_id
 * @param int $region_id
 * @param int $terr_id
 * @param int $country_id
 * @param int $state_id
 * @param int $district_id
 * @param string $from_dt
 * @param string $to_dt
 * @param string $topic
 * @param string $pcode
 * @param string $cust_name
 * @param string $chk_order
 * @param string $chk_install
 * @param string $emp_code
 * @param string $order_no
 * @param string $sortbycol
 * @param string $sorttype
 * @param string $or_status
 *
 * @return void
 */
function show_upgradation_dtl_report($exec_id,$zone_mgr_id,$regional_mgr_id,$terr_mgr_id,$zone_id,$region_id,
		$terr_id,$country_id,$state_id,$district_id,$from_dt,$to_dt,
		$topic,$pcode,$cust_name,$chk_order,$chk_install,$emp_code,$order_no,
		$sortbycol,$sorttype,$or_status){
	$query="";
	$query_from_dt='';
	$query_to_dt='';

	if($from_dt!='' and $to_dt!='')
	{
		$date_c =$from_dt;
		$newVar = explode("-", $date_c);
		$query_from_dt=date("Y-m-d", mktime(0, 0, 0,(int)$newVar[1],(int)$newVar[0],(int)$newVar[2]));
		$date_c =$to_dt;
		$newVar = explode("-", $date_c);
		$query_to_dt=date("Y-m-d", mktime(0, 0, 0,(int)$newVar[1],(int)$newVar[0],(int)$newVar[2]));
	}

	$alt_row_class=array("oddListRow","evenListRow","highlight_worker_left");
	global $me,$address_fields,$query_contact_dtl,$export_address_fields,$unknown_territory,$cmbRegion_multi;
	global $area_id;
	$select_query="select em.GEM_EMP_NAME as emp_name," .
			" id1.GUD_UPGRADATION_DATE as date_of_install," .
			" id1.GUD_ORDER_NO as order_no, " .
			" pfm.GPM_PRODUCT_ABR as prod_abr,id1.GUD_PRODUCT_SKEW as skew,pm.gpm_skew_desc as skew_desc," .
			" id1.GUD_UPDATED_VERSION as version,GLH_LEAD_CODE $address_fields," .
			" oh.GOD_ORDER_DATE as order_date," .
			" em.gem_status as emp_status ";

	$export_query="select em.GEM_EMP_NAME as Order_By," .
			" id1.GUD_UPGRADATION_DATE as 'Date_of_Upgradation'," .
			" id1.GUD_ORDER_NO as 'order_no',oh.GOD_ORDER_DATE as 'order_date', GLH_LEAD_CODE Customer_Id $export_address_fields, " .
			" pfm.GPM_PRODUCT_ABR as 'Product',pm.gpm_skew_desc as 'skew_desc'," .
			" id1.GUD_UPDATED_VERSION as 'version' ";
	
	$query.=" from (gft_emp_master em,gft_upgradation_dtl id1,gft_order_hdr oh,gft_lead_hdr b, gft_product_family_master pfm,gft_product_master pm) " .
			"  $query_contact_dtl ";
	
	
	if($terr_id!='' or $area_id!='' or $region_id!='' or $zone_id!='' or $district_id!='' or $state_id!=''  or
	$terr_id!=0 or $area_id!=0 or $region_id!=0 or $zone_id!=0 or $district_id!=0 or $state_id!=0 or $unknown_territory=='on' or count($cmbRegion_multi)>0){
		$query.=get_query_area_mapping($terr_id,$area_id,$region_id,$zone_id,$district_id,$state_id,$country_id,'','','b.glh_territory_id', 'b.glh_district_id');
	}
	global $uid;
	if(!is_authorized_group_list($uid,array(28,23,20)) && ($exec_id!=0 or $terr_mgr_id!=0  or $regional_mgr_id!=0 or $zone_mgr_id!=0 or is_authorized_group_list($uid,array(5,6)))){
		$query.=get_query_reporting_under();
	}
	//$query.=" left join gft_emp_master r on h1.GER_REPORTING_EMPID=r.GEM_EMP_ID ";
	$query.="where em.gem_emp_id=god_emp_id and oh.god_order_no=id1.gud_order_no ".
			" and b.GLH_LEAD_CODE=gud_lead_code and pfm.GPM_PRODUCT_CODE=id1.GUD_PRODUCT_CODE " .
			" and pm.gpm_product_code=pfm.gpm_product_code and id1.gud_product_skew=pm.gpm_product_skew ";
	
	if($or_status!="" and $or_status!="0" ) {
		$query.="  AND GOD_ORDER_STATUS='$or_status' ";
	}
	
	if($from_dt!='' and $to_dt!='' and $cust_name==''){
		$query.=" and id1.GUD_UPGRADATION_DATE >= '$query_from_dt' and id1.GUD_UPGRADATION_DATE <= '$query_to_dt'";
	}
	if($cust_name!=''){
		$query.= " and GLH_CUST_NAME like '$cust_name%'";
	}
	if($order_no!=""){
		$query.=" and gud_order_no like '%$order_no%' ";
	}
	if($emp_code){
		$query.=" and gud_emp_id='$emp_code' ";
	}
	if($pcode!=0){
		$query.=" and id1.gud_product_code='$pcode' ";
	}
	$query.=" AND GUD_DUBLICATE='N' ";
	$query.=" group by GLH_LEAD_CODE,gud_upgradation_id ";
	if($sortbycol==''){
		$query.= " ORDER BY em.GEM_EMP_NAME ";
		$sortbycol = "em.GEM_EMP_NAME ";
		$sorttype = '2';
	}else{
		$query.= " ORDER BY $sortbycol ".($sorttype=='2'?"DESC ":" ") ;
	}
	$result=execute_my_query($select_query.$query);
	$count_num_rows=mysqli_num_rows($result);
	$r_query=$export_query.$query;
	$nav_struct=get_dtable_navigation_struct($count_num_rows);
	$heading=implode(",",array("Order Closed by","Upgradation date","Order No ","Order date","Cust Name",
			"Location","Authority Name","Product","Skew","Version"));
	print_dtable_navigation($count_num_rows,$nav_struct,$me,"export_all_report.php",$r_query,'',$sp=null,1,true,null,null,$sms_category=84,$email_category=84);
	$myarr=array("S.No","Upgradation Date","Order No","Order date","Customer Name","Location","Product","Version");
	$mysort=array("","GUD_UPGRADATION_DATE","god_order_no","god_order_date","glh_cust_name","glh_cust_streetaddr2","pfm.gpm_product_abr","GUD_PRODUCT_SKEW");
	echo<<<END
		<table cellpadding="0" cellspacing="2" width="100%" border="0" class="FormBorder1">
END;
	sortheaders($myarr,$mysort,$nav_struct,$sortbycol,$sorttype);
	$start_of_row=(int)$nav_struct['start_of_row'];
	$MAX_NO_OF_ROWS=(int)$nav_struct['records_per_page'];
	$s=0;
	$id="";
	if($count_num_rows>0)
	{
		mysqli_data_seek($result,$start_of_row);
		$i=0;$sl=0;
		while(($query_data=mysqli_fetch_array($result)) and ($MAX_NO_OF_ROWS > $sl))
		{
			$sl++;
			$name=$query_data['emp_name'];
			$date_i=$query_data['date_of_install'];
			$date_o=$query_data['order_date'];
			$order_number=$query_data['order_no'];
			$customer_details=$query_data['GLH_CUST_NAME'];
			$location=$query_data['GLH_CUST_STREETADDR2'];
			$skew=$query_data['skew'];
			$product=$query_data['prod_abr'];//."-".$query_data['skew_desc'];
			$version=$query_data['version'];
			$emp_status=$query_data['emp_status'];
			$lead_code=$query_data['GLH_LEAD_CODE'];
			$order_number.="&nbsp;&nbsp;<a href=\"javascript:call_popup('order_releated_details.php?order_no=$order_number',7);\" " .
			"title=\"Order Details \">[O]</a>";
			$tooltip=get_necessary_data_from_query_for_tooltip($query_data);
			 
			$customer_details="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
			"onMouseout=\"hideddrivetip();\" " .
			"href=\"javascript:call_popup('edit_cust_details.php?call_from=1&lcode=$lead_code',7);\">".$customer_details."</a>";
			$customer_details.="&nbsp;&nbsp;<a href=\"javascript:call_popup('supporthistory.php?call_from=popup&history_type=3&from_dt=01-11-2004&custCode=$lead_code');\" class=\"subtle\">" .
			"<img alt=\"\" src=\"images/history.jpeg\" border=0 width=20 height=20></a>";
	
			$value_arr[0]=array($sl,$date_i,$order_number,$date_o,$customer_details,$location,$product,$version);
			print_resultset($value_arr);
		}//End of while
		echo '</div>';
	}//End of if
	echo "</table><tr></td>";
	print_dtable_navigation($count_num_rows,$nav_struct,"upgradation_report.php","export_all_report.php",$r_query,$heading);
	echo "</td></tr>";
	echo "</table>";
}
?>
