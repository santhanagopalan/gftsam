<?php
require_once(__DIR__ ."/dbcon.php");
require_once(__DIR__ ."/function.insert_stmt.php");

/*Calling in
 * 	Receipt Submit
 *  Receipt Correction by Accounts 
 *  Order Submit New/Edit
 *  Upgrade 
 *  Ass
 * 
 */

/**
 * @param string $order_no
 * 
 * @return void
 */
function update_install_table_outstanding($order_no){
	$q2="Update gft_install_dtl_new,gft_order_product_dtl set gid_install_value=gop_sell_amt/gop_qty" .
			" where gid_order_no=gop_order_no and gid_product_code=gop_product_code" .
			" and gid_product_skew=gop_product_skew and gid_order_no='$order_no' ";
	execute_my_query($q2);

	$query="select ins.gid_order_no,ins.gid_head_of_family,ins.gid_fullfillment_no,ins.gid_install_value, GOD_COLLECTION_REALIZED " .
			" from gft_install_dtl_new ins, gft_order_hdr " .
			" where gid_order_no=god_order_no and  " .
			" GOD_BALANCE_AMT!='0.00' and god_order_no='$order_no' " .
			" order by gid_order_no,ins.gid_install_date,ins.gid_head_of_family,ins.gid_fullfillment_no  ";
	$result=execute_my_query($query);
	$balance_amt=(float)0;
	$collection=(float)0;
	$i=0;
	while($qdata=mysqli_fetch_array($result)){
		$balance_amt=(float)$qdata['gid_install_value'];
		$order_no=$qdata['gid_order_no'];
		$product=$qdata['gid_head_of_family'];	
		$fulfillment_no=$qdata['gid_fullfillment_no'];
		if($i==0){
			$collection=(float)$qdata['GOD_COLLECTION_REALIZED'];
			if(!isset($collection)){
				$collection=0;
			}
		}
		if($collection >= $balance_amt){
			$collection-=$balance_amt;
			$balance_amt_1=(float)0;
		}else{
			$balance_amt_1=$balance_amt-$collection;
			$collection=0;
		}
		$query_up=" update gft_install_dtl_new set GID_OUTSTANDING_AMT='$balance_amt_1',GID_UPDATED_TIME=now() " .
				"where gid_order_no='$order_no' and gid_head_of_family=$product and GID_FULLFILLMENT_NO='$fulfillment_no' ";
		execute_my_query($query_up);
		$i++;	
	}//end of while
}//end of function 

/**
 * @param string $order_no
 * @param string $lead_code
 * @param boolean $order_split
 * @param string $order_closed_by
 * @param string $order_date
 * 
 * @return void
 */
function update_planned_orders_in_hdr($order_no=null,$lead_code=null,$order_split=false,
$order_closed_by=null,$order_date=null){
	$lh_query='';$oh_query='';
	$plannig_finished=false;  
	if($order_date==null){ $order_date=date('Y-m-d'); }
	$chk_planning_finished =" select * from gft_sales_planning_approve_incentive where " .
				"   gsp_month=month('$order_date') " .
				" and gsp_year=year('$order_date') and GSP_PLANNED_FINISHED_DATE!='0000-00-00 00:00:00' ";
	if($order_closed_by!=null){ $chk_planning_finished .=" and  gsp_emp_id='$order_closed_by' "; }
	else{return;}
	$rst_planning_finished=execute_my_query($chk_planning_finished);
	if(mysqli_num_rows($rst_planning_finished)==1)	{$plannig_finished=true; }
	else {
		$query_update_on_3rd=" update gft_track_lead_status,gft_lead_hdr left join gft_install_dtl_new " .
				" on (gid_lead_code=glh_lead_code and (GID_UPSELL='Y' or GID_UPGRADE='Y' or (GID_READY_TO_PAY_ASS='Y' and GID_VALIDITY_DATE<now() and GID_EXPIRE_FOR in (1,3))) and GID_STATUS='A') " .
				" set GTL_FREEZED_DATE=now()," .
			" GTL_WFREEZED_LEAD_STATUS=GLH_STATUS,GTL_WFREEEZED_EMP_ID=gtl_emp_id," .
			" GTL_WFREEZED_DOC=glh_approx_timetoclose ,GTL_DOC=glh_approx_timetoclose," .
			" GTL_WFREEZED_LEAD_TYPE=GLH_LEAD_TYPE," .
			" GTL_TRACK_TYPE=case when GLH_STATUS=3 then 1 when GLH_STATUS in(8,9) and GLH_BALANCE_AMOUNT>0 then 2 " .
			" when GLH_STATUS in(8,9) and (GLH_RESELL_APP='Y' or GLH_INTEREST_ADDON='Y' or GID_UPSELL='Y' or GID_UPGRADE='Y' or (GID_STATUS='A' and GID_READY_TO_PAY_ASS='Y' and GID_VALIDITY_DATE<now() and GID_EXPIRE_FOR in (1,3))) then 3 else 0 end, " .
			" GTL_WFREEZED_TRACK_TYPE=case when GLH_STATUS=3 then 1 when GLH_STATUS in(8,9) and GLH_BALANCE_AMOUNT>0 then 2 " .
			" when GID_STATUS='A' and GLH_STATUS in(8,9) and (GLH_RESELL_APP='Y' or GLH_INTEREST_ADDON='Y' or GID_UPSELL='Y' or (GID_STATUS='A' and GID_UPGRADE='Y') or (GID_STATUS='A' and GID_READY_TO_PAY_ASS='Y' and GID_VALIDITY_DATE<now() and GID_EXPIRE_FOR in (1,3))) then 3 else 0 end " .
			" where gtl_lead_code=glh_lead_code and " .
			" gtl_month=month('$order_date') and gtl_year=year('$order_date') and gtl_lead_code='$lead_code' ";
	}
			 	 
	if($order_split==false and $plannig_finished==true){
		//Direct Orders,CP value based orders,corporate orders
		 $query="update  gft_order_hdr,gft_track_lead_status " .
		 		"set GOD_PLANNED_BY=gtl_emp_id,god_planned_order='Y' " .
		 		"where gtl_lead_code=god_lead_code and god_order_status='A' " .
		 		"and gtl_month=month(god_order_date) and gtl_year=year(god_order_date) " .
		 		"and month(GTL_WFREEZED_DOC)=gtl_month and year(GTL_WFREEZED_DOC)=gtl_year and " .
				"month(GTL_FREEZED_DATE)=gtl_month and year(GTL_FREEZED_DATE)=gtl_year  ";
		 if($order_no!=''){
			$query.=" and god_order_no= '$order_no' ";
		 }	 
		 
		 $result=execute_my_query($query,'',true,false);
	 }
	 //CP product based	 
	 if($lead_code!=''){
	 	$lh_query.=" and GCO_CUST_CODE= '$lead_code' ";
	 }
	 if($order_no!=''){
			$oh_query.=" and GCO_ORDER_NO= '$order_no' ";
	}		 
	 
	 $query="update  gft_order_hdr,gft_cp_order_dtl,gft_product_master,gft_track_lead_status,gft_cp_info,gft_emp_master  " .
		 		"set GCO_PLANNED_BY=gtl_emp_id,gco_planned_order='Y'  " .
		 		"where god_order_no=GCO_ORDER_NO and god_order_splict=1 and  " .
		 		"gco_product_code=gpm_product_code and gco_skew=gpm_product_skew and " .
		 		"gft_skew_property=1 and gpm_product_type!=1 and gtl_lead_code=GCO_CUST_CODE and god_order_status='A' " .
		 		" $lh_query $oh_query and gtl_month=month(GCO_ORDERED_DATE) and gtl_year=year(GCO_ORDERED_DATE) " .
		 		"and month(GTL_WFREEZED_DOC)=gtl_month and year(GTL_WFREEZED_DOC)=gtl_year and " .
				"month(GTL_FREEZED_DATE)=gtl_month and year(GTL_FREEZED_DATE)=gtl_year and " .
				"CGI_LEAD_CODE=GCO_CP_LEAD_CODE  and CGI_EMP_ID=gem_emp_id and gem_role_id=21  ";
	
	$result=execute_my_query($query,'',true,false);
		 	
	 

}

/**
 * @param string $order_no
 * 
 * @return void
 */
function update_install_date($order_no=null){
	$whr_order_no='';
	if($order_no!=''){ $whr_order_no=" and god_order_no='$order_no' "; }
	$query="update gft_order_hdr t ,(select god_order_no,min(gid_install_date) gid_install_date,sum(GID_INSTALL_VALUE) GID_INSTALL_VALUE  " .
			" from gft_order_hdr ,gft_install_dtl_new " .
			" where god_order_no=gid_order_no $whr_order_no " .
			" group by god_order_no ) t1 set GOD_FIRST_INSTALLATION_DATE=gid_install_date " .
 			" where t.god_order_no=t1.god_order_no and t.god_order_no='$order_no'";
 	$result=execute_my_query($query,'',true,false);
}

/**
 * @param boolean $for_dist_or_terr_id_null
 * @param string $cust_pincode
 * 
 * @return void
 */
function update_cust_distict_id($for_dist_or_terr_id_null=true,$cust_pincode=null)
{
	$query="update  gft_lead_hdr,gft_pincode_master set glh_district_id=gpm_district_id," .
			"glh_territory_id=GPM_TERRITORY_ID " .
			"where GPM_PINCODE=GLH_CUST_PINCODE ";
	if($for_dist_or_terr_id_null==true){
		$query.=" and (glh_district_id=0 or glh_territory_id=0)  ";
	}
	if($cust_pincode!=null){
		   $query.=" and GPM_PINCODE='$cust_pincode' ";
	}
	$result=execute_my_query($query,'function.update_in_hdr.php',true,false);
	
}

/**
 * @param string $pincode
 * @param string $country
 * 
 * @return boolean
 */
function update_lead_hdr_from_pincode($pincode=null,$country='india'){
	/* Update territory ,district,lead field exec */
	$query="update gft_lead_hdr,gft_pincode_master set glh_territory_id=GPM_TERRITORY_ID, " .
	  		" glh_district_id=GPM_DISTRICT_ID where glh_cust_pincode=gpm_pincode " .
	  		" and glh_lead_type not  in (8) and glh_country='$country' ";
	/*8 is lead type of gft employees */ 		
	if($pincode!=null){
 		$query.=" and gpm_pincode='$pincode' ";
	}
	$result=execute_my_query($query);	
	if($pincode!=null){
	    $lead_code_arr=array();
		$GLH_TERRITORY_ID=identify_territory_from_pincode($pincode,$country='India');
		$sales_incharge=get_terr_incharge($GLH_TERRITORY_ID);
		$query_leads="select glh_lead_code,glh_lfd_emp_id  from gft_lead_hdr ".
				"where glh_lfd_emp_id!='$sales_incharge' and glh_cust_pincode='$pincode' ";
		$result=execute_my_query($query_leads);
		if($result){
			while($qd=mysqli_fetch_array($result)){
				$lead_code=$qd['glh_lead_code'];
				$glh_lfd_emp_id=$qd['glh_lfd_emp_id'];
				if(!is_territory_accessable($GLH_TERRITORY_ID,$glh_lfd_emp_id)){
					insert_lead_fexec_dtl($lead_code,$sales_incharge,true);
				}
				/*if($qd['glh_lfd_emp_id']==SALES_DUMMY_ID){
					$db_sms_content_config=send_customer_details_to_exec($lead_code);
					$email_from=get_samee_const("ADMIN_TEAM_MAIL_ID");
					$email_to=get_email_addr($sales_incharge);
					$cc=get_samee_const('PRESALES_MAIL_ID');
					$subject='Web register Customer ';
					$msg_c=get_formatted_mail_content($db_sms_content_config,$category=2,$mail_template_id=6);
					$msg=$msg_c['content'];
					$content_type=$msg_c['content_type'];
					send_mail_from_sam($category=2,$email_from,$email_to,$subject,$msg,$attachment_file_tosend=null,
					$cc,$content_type,$reply_to=$email_from,$from_page=null,$user_info_needed=false);
				}*/
			}
		}
	}
	return true;
} 						

/**
 * @return void
 */
function update_current_order_value(){
$query="update gft_install_dtl_new t1,(select GID_LIC_ORDER_NO,GID_LIC_PCODE,GID_LIC_PSKEW,GID_LIC_FULLFILLMENT_NO,GID_NO_CLIENTS,
round((pm.GPM_LIST_PRICE+(if((GID_NO_CLIENTS-pm.GPM_CLIENTS)<0,0,(GID_NO_CLIENTS-pm.GPM_CLIENTS))*price_cl)),2) as 'ORDER_VALUE' from gft_install_dtl_new 
 join gft_product_family_master pfm on (pfm.GPM_PRODUCT_CODE=GID_LIC_PCODE) 
 join gft_product_master pm on(pm.gpm_product_code=pfm.gpm_product_code and gid_lic_pskew=pm.gpm_product_skew)
 left join gft_product_master asapm on(asapm.gpm_product_code=pfm.gpm_product_code and gid_lic_pskew=asapm.GPM_REFERER_SKEW and asapm.gft_skew_property=4) 
 left join (SELECT gpm_product_code as cl_pr_code,substring(gpm_product_skew,1,4) skew_sb,
 GPM_CLIENTS as 'add_cl',(GPM_LIST_PRICE/GPM_CLIENTS) 'price_cl'
 FROM `gft_product_master` g WHERE  gft_skew_property=3) cl_pr 
 on (cl_pr_code=gid_lic_pcode and skew_sb=substring(gid_lic_pskew,1,4)) 
WHERE GID_STATUS='A' and gid_expire_for in (1,3) )t2 set gid_current_value=ORDER_VALUE 
WHERE t1.GID_LIC_ORDER_NO=t2.GID_LIC_ORDER_NO AND t1.GID_LIC_PCODE=t2.GID_LIC_PCODE  
AND t1.GID_LIC_PSKEW=t2.GID_LIC_PSKEW AND t1.GID_LIC_FULLFILLMENT_NO=t2.GID_LIC_FULLFILLMENT_NO ";
execute_my_query($query);
	
}

/**
 * @param string $vertical_code
 * @param string $merge_with
 * @param string $user_id
 * 
 * @return void
 */
function update_lead_hdr_vertical($vertical_code,$merge_with,$user_id){
	$query="select glh_lead_code from gft_lead_hdr where glh_vertical_code=$vertical_code ";
	$result=execute_my_query($query);
	while($qdata=mysqli_fetch_array($result)){
		$table_name='gft_lead_hdr';
		$updatearr=/*. (mixed[string]) .*/ array();
		$table_key_arr['GLH_LEAD_CODE']=$qdata['glh_lead_code'];
		$updatearr['GLH_VERTICAL_CODE']=$merge_with;
		array_update_tables_common($updatearr,$table_name,$table_key_arr,null, $user_id,'Vertical Merge from Master Management');
	}
	
}//end of function 

?>
