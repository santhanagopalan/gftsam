<?php
require_once(__DIR__ ."/function.insert_stmt.php");
require_once(__DIR__ ."/report_to_mail_util.php");
/**
 * @param string[string] $followup_detail
 * @param int $GLD_VISIT_NATURE
 * @param int $foll_id
 * @param bool $lead_incharge
 * 
 * @return void
 */
function send_followup_mail($followup_detail,$GLD_VISIT_NATURE,$foll_id,$lead_incharge=false){
	global $non_employee_group;

	$lead_code=$followup_detail['GCF_LEAD_CODE']; 
	$assigned_to=$followup_detail['GCF_ASSIGN_TO'];
	$assigned_by=$followup_detail['GCF_ASSIGN_BY'];			
	$time_report=date('Y-m-d H:i:s');
	$email_from=get_email_addr($assigned_by);
	$email_to=get_email_addr($assigned_to);
	$cc[0]=get_email_addr($assigned_by);
	/* $group_email_id=get_email_id_of_group(array('27')); // Presales Team Email id 
	if(!empty($group_email_id)){
		$group_email_id_arr=explode(',',$group_email_id);
		for($gm=0;$gm<count($group_email_id_arr);$gm++){
			array_push($cc,$group_email_id_arr[$gm]);
		}
	} */
	
	if(is_authorized_group_list($assigned_to,$non_employee_group)){
		$cp_incharge_mail_id=get_cp_incharge($assigned_to);
		if(!empty($cp_incharge_mail_id)) {
			array_push($cc,$cp_incharge_mail_id);
		}
		
		$cp_rel_mgr_mail_id=get_cp_relational_incharge($assigned_to);
		if(!empty($cp_rel_mgr_mail_id)) {
			array_push($cc,$cp_rel_mgr_mail_id);
		}
	}
	$cc_arr=get_email_addr_reportingmaster($assigned_to,true,explode(",", EMP_IDS_TO_SKIP_REPORTING_MAIL));
	$cc=array_merge($cc,$cc_arr);
	$cc_arr=get_email_addr_reportingmaster($assigned_by,true,explode(",", EMP_IDS_TO_SKIP_REPORTING_MAIL));
	$cc=array_merge($cc,$cc_arr);
	$subject=" Followup Assigned by ".get_name($assigned_by);
	if($lead_incharge==true) {
		$subject="Followup and Lead Assigned by ".get_name($assigned_by);
	}
	
	$lead_type	=	/*. (int) .*/get_lead_type_for_lead_code($lead_code);
	$insterest_pro	=	check_customer_interested_product($lead_code, 300);	
	if($lead_type==3 or $insterest_pro){
		array_push($cc,"hq-sales@gofrugal.com");
	}
	$msg="Dear ".get_name($assigned_to).",<br>";
	$purpose="fixed";
	global $only_address_fields;
	$GCF_ACTION=/*. (int) .*/$followup_detail['GCF_ACTION'];
	if($GCF_ACTION==56){//escalation Support Escallation
		$cc_arr1[0]=get_samee_const("GFT_CUST_CARE_EMAILID");
		$cc=array_merge($cc,$cc_arr1);
	}
	/* Find from which Activity */
	$followup_action_query="select GAM_ACTIVITY_DESC from gft_activity_master where gam_activity_id='$GCF_ACTION' ";
	$followup_action_result=execute_my_query($followup_action_query);
	$qdfs=mysqli_fetch_array($followup_action_result);
	$GCF_ACTION_str=$qdfs[0];
	/*Find Viste nature*/
	$visit_nature_msg	=	'';
	if(isset($followup_detail['GCF_ACTIVITY_REF']) && $followup_detail['GCF_ACTIVITY_REF']!=''){
		$res_vn	=	execute_my_query("select GCM_NATURE from gft_activity 
									inner join gft_complaint_nature_master on(GLD_ACTIVITY_NATURE=GCM_NATURE_ID)
									where GLD_ACTIVITY_ID=".$followup_detail['GCF_ACTIVITY_REF']);
		if($res_row=mysqli_fetch_array($res_vn)){
			$visit_nature_msg	=	$res_row['GCM_NATURE'];
		}		
	}
	/* Find from which Activity -End*/
	
	$db_content_config=array(
		'Assigned_By'=> array($assigned_by),
		'Assigned_To'=> array($assigned_to),
		'Action'=>array($GCF_ACTION_str),
		'GFT_CUST_CARE_EMAILID'=>array(get_samee_const("GFT_CUST_CARE_EMAILID"))
	);
	$msg.="<br><b>Purpose (Activity) :</b>".$GCF_ACTION_str."<br>";
	$msg.="<b>Visit Nature :</b>".$visit_nature_msg."<br>";
	$query="  SELECT lh.GLH_LEAD_CODE ".(string)str_replace('GLH_','lh.GLH_',$only_address_fields). " ,lh.GLH_APPROX_TIMETOCLOSE," .
			" cst.GCS_NAME,GTM_VERTICAL_NAME,lh.GLH_COMPUTERIZED,GLC_COMPETE_PRODUCT_NAME," .
			" GLC_INSTALL_DATE,group_concat(distinct(gpm_product_abr)) as prod_interested," .
			" ifnull(lsm_cust.GLS_SOURCE_NAME,'Cust/Other') as 'Cust_other_Source'," .
			" ifnull(lhc.GLH_CUST_NAME,lh.GLH_REFERREDBY) as 'Cust_other_Source_Name' ," .
			" lh.GLH_REFERENCE_GIVEN ,".
			" ifnull(lsm_partner.GLS_SOURCE_NAME,'Partner') as 'Partner_Source', lhp.GLH_CUST_NAME as 'Partner_Source_Name' ,".
			" ifnull(lsm_internal.GLS_SOURCE_NAME,'Internal') as 'Internal_Source',em_internal.gem_emp_name as 'Internal_Source_Name'," .
			" lh.GLH_SHOPINFO ".
			" from gft_lead_hdr lh " .
			" left join gft_vertical_master vm on (lh.GLH_VERTICAL_CODE=vm.GTM_VERTICAL_CODE) " .
			" left join gft_lead_product_dtl lc on(lc.glc_lead_code=lh.glh_lead_code)" .
			" left join gft_product_family_master pfm on (gpm_product_code=lc.glc_product_code) ".
			" left join gft_customer_status_master cst on (cst.GCS_CODE=lh.GLH_STATUS ) " .
			" left join gft_lead_compete_dtl lcm on (lcm.GLC_LEAD_CODE=lh.glh_lead_code )" .
			" left join gft_lead_source_master lsm_cust on (lsm_cust.GLS_SOURCE_CODE = lh.GLH_LEAD_SOURCECODE )".
			" left join gft_lead_source_master lsm_partner on (lsm_partner.GLS_SOURCE_CODE = lh.GLH_LEAD_SOURCE_CODE_PARTNER )".
			" left join gft_lead_source_master lsm_internal on (lsm_internal.GLS_SOURCE_CODE = lh.GLH_LEAD_SOURCE_CODE_INTERNAL )".
			" left join gft_lead_hdr lhc on (lhc.glh_lead_code=lh.GLH_REFERENCE_GIVEN) " .
			" left join gft_lead_hdr lhp on (lhp.glh_lead_code=lh.GLH_REFERENCE_OF_PARTNER) " .
			" left join gft_emp_master em_internal on (em_internal.gem_emp_id=lh.GLH_REFERENCE_INTERNAL) " .
			" left join gft_emp_master em on (em.gem_emp_id=lh.glh_created_by_empid) " .
		    " where lh.glh_lead_code='$lead_code' " .
		    " group by lh.glh_lead_code ";
	$result=execute_my_query($query);
	
	
	$followup_status_str='';

	$qd=mysqli_fetch_array($result);
	if($followup_detail['GCF_FOLLOWUP_ACTION']!==null){
	$followup_action_query="select GAM_ACTIVITY_DESC from gft_activity_master where gam_activity_id={$followup_detail['GCF_FOLLOWUP_ACTION']}";
	$followup_action_result=execute_my_query($followup_action_query);
	$qdfs=mysqli_fetch_array($followup_action_result);
	$followup_status_str=$qdfs[0]; 
	}
	$assigned_by_name=get_name($assigned_by);
	$msg.="<br><b> Assigned  by :</b>".$assigned_by_name."<br>";
	$msg.="<br><b>Followup Action Assigned :</b> $followup_status_str  on ".$followup_detail['GCF_FOLLOWUP_DATE']." ".$followup_detail['GCF_FOLLOWUP_TIME'];
	$msg.="<br><b>Followup Detail :</b>{$followup_detail['GCF_FOLLOWUP_DETAIL']}";
	if(!empty($qd['GLH_SHOPINFO'])){
		$msg.="<br><b>Business Info :</b>{$qd['GLH_SHOPINFO']}";
	}
  	$msg.="<br>Please <a href='http://sam.gofrugal.com/visit_details.php?lead_code=$lead_code' target=_blank>Click here</a> to go to Followup Activity.<br> ";
  	$msg.="<br><center><table border='1' width='70%' >";
 	$msg.="<tr><td><b> Customer ID  </b></td><td> {$qd['GLH_LEAD_CODE']} </td></tr>";
  	$msg.="<tr><td><b> Shop  Name </b></td><td> {$qd['GLH_CUST_NAME']} </td></tr>";
  	$msg.="<tr><td><b> Door No   </b></td><td>{$qd['GLH_DOOR_APPARTMENT_NO']}</td></tr>";
  	$msg.="<tr><td><b> Block/Society Name</b></td><td>{$qd['GLH_BLOCK_SOCEITY_NAME']}</td></tr>";
  	$msg.="<tr><td><b> Street No         </b></td><td>{$qd['GLH_CUST_STREETADDR1']}</td></tr>";
  	$msg.="<tr><td><b> Street Name       </b></td><td>{$qd['GLH_CUST_STREETADDR2']}</td></tr>";
  	$msg.="<tr><td><b> Area Name         </b></td><td>{$qd['GLH_AREA_NAME']}</td></tr>";
  	$msg.="<tr><td><b> Location          </b></td><td>{$qd['GLH_CUST_STREETADDR2']}</td></tr>";
  	$msg.="<tr><td><b> City              </b></td><td>{$qd['GLH_CUST_CITY']}</td></tr>";
  	$msg.="<tr><td><b> State             </b></td><td>{$qd['GLH_CUST_STATECODE']}</td></tr>";
  	$msg.="<tr><td><b> Pincode           </b></td><td>{$qd['GLH_CUST_PINCODE']}</td></tr>";
  	$msg.="<tr><td><b> Vertical </b></td><td>{$qd['GTM_VERTICAL_NAME']}</td></tr>";
  	$msg.="<tr><td><b> Lead Status </b></td><td>{$qd['GCS_NAME']}</td></tr>";
  	$msg.="<tr><td><b> Product Interested </b></td><td>{$qd['prod_interested']}</td></tr>";
  	$msg.="</table>";
  	
  	$msg.="<br>";
  	$msg.="<table  border=\"1\" width=\"70%\"><tr><th colspan='3'>Lead Source </th></tr>";
  	$msg.="<tr><th width='20%'>{$qd['Partner_Source']}</th><td width='25%'>{$qd['Partner_Source_Name']}</td><td width='5%'></td></tr>";
  	$msg.="<tr><th>{$qd['Cust_other_Source']}</th><td>{$qd['Cust_other_Source_Name']}</td><td>ID:{$qd['GLH_REFERENCE_GIVEN']}</td></tr>";
  	$msg.="<tr><th>{$qd['Internal_Source']}</th><td>{$qd['Internal_Source_Name']}</td><td></td></tr>";
  	$msg.="</table>";
  	$message	=	"$followup_status_str  on  ".$followup_detail['GCF_FOLLOWUP_DATE']." ".$followup_detail['GCF_FOLLOWUP_TIME'].", Assigned by $assigned_by_name";
	$query_contact_dtl="select GCC_CONTACT_NAME,GCD_NAME,GCT_DESC,GCC_CONTACT_NO,group_concat(GCG_CONTACT_GROUP_NAME) as GROUP_NAME " .
			" from gft_customer_contact_dtl join gft_cust_contact_type_master on(gcc_contact_type=gct_id) " .
			" left join gft_contact_dtl_group_map cgm on (GCG_LEAD_CODE=GCC_LEAD_CODE AND GCG_CONTACT_ID=GCC_ID) " .
			" left join gft_contact_dtl_group_master cg on (GCG_CONTACT_GROUP_ID=GCG_GROUP_ID) " .
			" join gft_contact_designation_master on(gcd_code=gcc_designation) " .
			" where gcc_lead_code='".$lead_code."' " .
			" group by gcc_id  ";
	$myarr=array("S.No","Contact Name","Designation","Contact Type","Contact ","Group");
	$mysort=array("","GCC_CONTACT_NAME","GCD_NAME","GCT_DESC","GCC_CONTACT_NO","GROUP_NAME");
		
	/*$msg.=generate_reports_to_mailer($table_header=null,$query_contact_dtl,$myarr,$mysort,$sms_category=null,$email_category=null,
		$previous_months_link=null,$value_arr_align=null,$myarr_width=null,$total_query=null,$sec_field_arr=null,
		$myarr_sub=null,$rowspan_arr=null,$colspan_arr=null,$myarr_sub1=null,$rowspan_arr1=null,$colspan_arr1=null,
		$report_link=null,$show_in_popup=null,$scorallable_tbody=false,$navigation=true,$order_by='GCC_CONTACT_NAME',$heading=true,
		$value_arr_total=null,$sorting_required=true,$child=0,$trow_name=null,$emyarr=null,$emyarr_sub=null,
		$pass_only_this_arg=null,$export_index=0);*/
		
	

	if($qd['GLH_COMPUTERIZED']=='Yes')	{
		$msg.="<br>";
		$msg.="<tr><td colspan='2'><b> Computerized Details</b> </td></tr>";		
		$msg.="<tr><td><b> Computerized Since </td><td>".$qd['GLC_INSTALL_DATE']."</td></tr>";
		$msg.="<tr><td><b> Product Used </td><td>".$qd['GLC_COMPETE_PRODUCT_NAME']."</td></tr>";
	} 
	
	if($GLD_VISIT_NATURE==58){/* lead qualify action */
		$msg.="<br>".send_recent_audit_dtl($lead_code,$assigned_by,'14');
	}  
	if($GLD_VISIT_NATURE==54){
		$chat_details	=	isset($_REQUEST['chat'])?(string)$_REQUEST['chat']:"";
		if($chat_details==""){
			$chat_details="To know more details about chat, check chat history of this customer in myDelight";
		}
		$msg.="<br><table>";
		$msg.="<tr><td colspan='2' align='left'><b>Chat Transcript </b> </td></tr>";
		$msg.="<tr><td colspan='2'>".$chat_details."</td></tr></table>";
	}
	$msg.=" <br>";
	$msg.="</center><br>";
	$msg.="<hr>This is automated Message from SAM.";
	send_mail_from_sam(40,$email_from,$email_to,$subject,$msg,null,$cc,true,$cc,null,false);	
	$title	=	$qd['GLH_CUST_NAME']."-".$qd['GLH_CUST_STREETADDR2'];
	notificaton_entry((int)$followup_detail['GCF_ASSIGN_TO'],$title,$message,1,(int)$qd['GLH_LEAD_CODE'],$foll_id,1);
}
?>
