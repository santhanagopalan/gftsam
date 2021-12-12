<?php
require_once(__DIR__ ."/../dbcon.php");
require_once(__DIR__ ."/../function.update_in_tables.php");
/**
 * @param string $product_groups
 * @param string $country
 * @param string $state_name
 * @param boolean $is_Premium
 * @param string $pd_product_group
 * 
 * @return mixed[string]
 */
function return_voicesnap_group_injson($product_groups,$country=null,$state_name=null,$is_Premium=false,$pd_product_group=null){
		$skip_group = get_samee_const('Skip_Support_Group_Preferance');
		 $query="SELECT group_concat(distinct(GVG_GROUP_ID) order by GSP_PREFERRED_ORDER,GVG_PREFER_ORDER ) as vgroup_id ," .
				"group_concat(distinct(GVG_SUPPORT_GROUP) order by GSP_PREFERRED_ORDER,GVG_PREFER_ORDER) mgroup_id FROM gft_voicenap_group " .
				"join gft_support_product_group sg on (gsp_group_id=GVG_SUPPORT_GROUP and GSP_STATUS='A')" .
				" WHERE GVG_STATUS='A' ".
		($product_groups!=null?" AND GVG_PRODUCT in ($product_groups) ":'') .
		($pd_product_group!=null?" and GVG_SUPPORT_GROUP in ($pd_product_group) and GSP_PD_SUPPORT_GROUP='Y' ":" and GSP_PD_SUPPORT_GROUP='N' ").
		($is_Premium!=true?" and GVG_PREMIUM in(1)":" and GVG_PREMIUM in (1,2) ").
		" and (". (($country=="India" and $state_name!='') ?" GVG_LANGUAGE like '%$state_name%' or ":"")." GVG_LANGUAGE='' ) ";
		 if ($pd_product_group != '1'){
			 $query.= ($skip_group!='')?" and gvg_support_group not in ($skip_group)" : ""; //Added this condition to exclude few support groups in call preferance
		 }

	
		$result=execute_my_query($query);
		$vgroup_id=array();
		$vgroup_id1=array();
		$call_routing=/*. (string[string]) .*/ array();
		if($result ){
			$data=mysqli_fetch_array($result);
			if($data['vgroup_id']!=''){
			$vgroup_id=explode(',',$data['vgroup_id']);
			$i=1;
			foreach ($vgroup_id as $key=>$value){
				$vgroup_id1[$i]=$value;
				$i++;		
			}
			}
			$mgroup_id=explode(',',$data['mgroup_id']);
			$call_routing['call_preferance']=(!empty($vgroup_id1)?json_encode($vgroup_id1):'');
			$call_routing['main_product']=(empty($mgroup_id[0])?'1':$mgroup_id[0]);
			return $call_routing;
		}else{
			$call_routing['call_preferance']='';
			$call_routing['main_product']=1;
			return $call_routing;
		}
		
}
/**
 * @param string $company_id
 * @param string $product_ids
 * @param int $type_of_group
 * @param string $country
 * @param string $state_name
 * 
 * @return string[string]
 */
function get_call_routing_info($company_id,$product_ids,$type_of_group,$country='india',$state_name=''){
	$sg = "GPC_SUPPORT_GROUP";
	if($type_of_group==2){
		$sg = "GPC_PD_GROUP";
	}
	$sql1 = " select group_concat(distinct gsp_group_id order by GSP_PREFERRED_ORDER desc) group_id, ".
			" group_concat(distinct(concat(gsp_group_id,'-',GVG_GROUP_ID)) order by GSP_PREFERRED_ORDER desc,GVG_PREFER_ORDER ) as vgroup_id ".
			" from gft_product_company_mapping ".
			" join gft_support_product_group on (gsp_group_id=$sg) ".
			" join gft_voicenap_group on (GVG_SUPPORT_GROUP=gsp_group_id and GVG_STATUS='A') ".
			" where GVG_PREMIUM=1 and GPC_COMPANY_ID='$company_id' and GPC_PRODUCT_ID in ($product_ids) ";
	$lang_cond = "";
	if( (strtolower($country)=='india') && ($state_name!='') ){
		$lang_cond = " or GVG_LANGUAGE like '%$state_name%' ";
	}
	$sql1 .= " and (GVG_LANGUAGE='' $lang_cond) ";
	$sql1 .= " having vgroup_id is not null ";
	$res1 = execute_my_query($sql1);
	$call_pref = $main_prod = '';
	if($row1 = mysqli_fetch_array($res1)) {
		$group_id_arr = explode(',',$row1['group_id']);
		$arr = explode(",", $row1['vgroup_id']);
		$garr = array();
		$i=1;
		foreach ($arr as $varr){
			$temp_arr = explode("-", $varr);
			if($temp_arr[0]==$group_id_arr[0]){
				$garr[$i++] = $temp_arr[1];
			}
		}
		$call_pref = (!empty($garr))?json_encode($garr):'';
		$main_prod = $group_id_arr[0];
	}
	$call_routing = array(
			'call_preferance'=>$call_pref,
			'main_product'=>$main_prod
	);
	return $call_routing;
}

/**
 * @param string $lead_code
 *
 * @return void
 */
function update_presales_call_groups($lead_code){
	if($lead_code!=''){
		$sql1 = " select GVG_SUPPORT_GROUP,GVG_GROUP_ID from gft_lead_hdr ".
				" join gft_assure_care_company on (GAC_REF_ID=GLH_REFERENCE_OF_PARTNER and GLH_LEAD_SOURCE_CODE_PARTNER=37) ".
				" join gft_product_company_mapping on (GPC_COMPANY_ID=GAC_ID) ".
				" join gft_voicenap_group on (GVG_SUPPORT_GROUP=GPC_PRESALES_GROUP and GVG_STATUS='A') ".
				" where GLH_LEAD_CODE='$lead_code' and GAC_ID!=1 group by GLH_LEAD_CODE ";
		$res1 = execute_my_query($sql1);
		if($row1 = mysqli_fetch_array($res1)){ //other than gofrugal company
			$main_product = $row1['GVG_SUPPORT_GROUP'];
			$call_routing[1] = $row1['GVG_GROUP_ID'];
		}else{ //gofrugal company
			$main_product 	= '17';
			$call_routing[1]= get_lead_mgmt_incharge(0,0,0,0,0,$lead_code,true);
		}
		$call_pref = json_encode($call_routing);
		$today_date = date('Y-m-d');
		$upd1 = " update gft_lead_hdr set GLH_MAIN_PRODUCT='$main_product',GLH_MAIN_PRODUCT_UVALIDITITY='0000-00-00', ".
				" GLH_MAIN_PRODUCT_UPDATE_BY=9999,GLH_CALL_PREFERANCE='$call_pref' where GLH_LEAD_CODE='$lead_code' and ( GLH_MAIN_PRODUCT_UVALIDITITY<'$today_date' or GLH_MAIN_PRODUCT_UVALIDITITY is null ) ";
		execute_my_query($upd1);
	}
}

/**
 * @param string $lead_code
 * @param boolean $splitable
 * @param boolean $from_recursive
 * 
 * @return void
 */
function update_not_activated_leads($lead_code='',$splitable=false,$from_recursive=false){

if(empty($lead_code) and $from_recursive==false){
	update_not_activated_leads('',false,true);
	update_not_activated_leads('',true,true);
	return;		
}
$from_order_date=get_samee_const('PD_SUPPORT_PROCESS_ORDER_DATE_CHECK_IN_DAYS');
$wh_query=(!empty($lead_code)?" and glh_lead_code= $lead_code ":'');
$wh_query.=(!empty($from_order_date)?" and datediff(date(now()),god_order_date)<$from_order_date ":'');
$on_order_hdr='';
if($splitable==true){
	$on_order_hdr=" and god_order_splict=true ";
}
if($splitable==false){
$query_cp=<<<END
	  select GLH_LEAD_CODE,GLH_STATUS,GLH_LEAD_TYPE,GLH_PROSPECTS_STATUS,GLH_CALL_PREFERANCE,GLH_MAIN_PRODUCT,GLH_MAIN_PRODUCT_UVALIDITITY,GLH_COUNTRY, 
	  GLH_CUST_STATECODE, sum(if(GOD_IMPL_REQUIRED='Yes',1,0)) as impl_required, 
	  group_concat(distinct(concat(GPM_HEAD_FAMILY,'-',substring(GPM_PRODUCT_SKEW,1,4) ) ) ) product_groups,
	  group_concat(distinct(GPG_PD_EXPERT_GROUP) order by GSP_PREFERRED_ORDER) as 'ssg',sum(if(gft_skew_property in (1,11),gop_qty,0)) qty ,
	  sum(if(gft_skew_property in (1,11),gop_usedqty,0)) used_qty , ifnull(GAC_ID,1) as company_id, 
	  COUNT(distinct(gid_install_id)) INS_COUNT 
	  from gft_lead_hdr lh  
	  left join gft_install_dtl_new ins on (gid_lead_code=glh_lead_code and  gid_status in ('A','UG'))
	  left join gft_order_hdr oh on (god_lead_code=glh_lead_code and god_order_status='A'  $on_order_hdr)
	  left join gft_order_product_dtl opd on (gop_order_no=god_order_no)
	  left join gft_product_master pm on (pm.gpm_product_code=opd.gop_product_code and pm.gpm_product_skew=opd.gop_product_skew and gft_skew_property in (1,11) )
	  left join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code )
	  left join gft_product_group_master pgm on (pgm.gpg_product_family_code=pfm.gpm_product_code AND 
 	  gpg_skew=substr(gop_product_skew,1,4) and  GPM_PRODUCT_PRIME_TYPE='Y')
 	  left join gft_support_product_group sg on (gsp_group_id=GPG_PD_EXPERT_GROUP and GSP_PD_SUPPORT_GROUP='Y')
 	  left join gft_assure_care_company on (GAC_REF_ID=GLH_REFERENCE_OF_PARTNER and GLH_LEAD_SOURCE_CODE_PARTNER=37)
 	  where glh_lead_type not in (3,13,8) $wh_query 
	  group by GLH_LEAD_CODE having INS_COUNT=0 and  product_groups!='' and impl_required > 0
END;
}else{
$query_cp=<<<END
	  select GLH_LEAD_CODE,GLH_STATUS,GLH_LEAD_TYPE,GLH_PROSPECTS_STATUS,GLH_CALL_PREFERANCE,GLH_MAIN_PRODUCT,GLH_MAIN_PRODUCT_UVALIDITITY,GLH_COUNTRY, 
	  GLH_CUST_STATECODE, sum(if(GOD_IMPL_REQUIRED='Yes',1,0)) as impl_required,
	  group_concat(distinct( concat(GPM_HEAD_FAMILY,'-',substring(GPM_PRODUCT_SKEW,1,4) ) ) ) product_groups,
	  group_concat(distinct(GPG_PD_EXPERT_GROUP) order by GSP_PREFERRED_ORDER) as 'ssg',
	  sum(if(gft_skew_property in (1,11),GCO_CUST_QTY,0)) qty ,
	  sum(if(gft_skew_property in (1,11),GCO_USEDQTY,0)) used_qty ,ifnull(GAC_ID,1) as company_id, 
	  COUNT(distinct(gid_install_id)) INS_COUNT 
	  from gft_lead_hdr lh  
	  join gft_cp_order_dtl opd on (GCO_CUST_CODE=glh_lead_code )
	  left join gft_install_dtl_new ins on (gid_lead_code=glh_lead_code and  gid_status in ('A','UG'))
	  left join gft_order_hdr oh on (god_order_no=GCO_ORDER_NO and god_order_status='A'  $on_order_hdr)
	  left join gft_product_master pm on (pm.gpm_product_code=opd.GCO_PRODUCT_CODE and pm.gpm_product_skew=opd.GCO_SKEW and gft_skew_property in (1,11) )
	  left join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code )
	  left join gft_product_group_master pgm on (pgm.gpg_product_family_code=pfm.gpm_product_code AND 
 	  gpg_skew=substr(GCO_SKEW,1,4) and  GPM_PRODUCT_PRIME_TYPE='Y')
 	  left join gft_support_product_group sg on (gsp_group_id=GPG_PD_EXPERT_GROUP and GSP_PD_SUPPORT_GROUP='Y')
 	  left join gft_assure_care_company on (GAC_REF_ID=GLH_REFERENCE_OF_PARTNER and GLH_LEAD_SOURCE_CODE_PARTNER=37) 
 	  where glh_lead_type not in (3,13,8) $wh_query 
	  group by GLH_LEAD_CODE having INS_COUNT=0 and  product_groups!='' and impl_required > 0
END;
}
			

$result_cp=execute_my_query($query_cp);

if(mysqli_num_rows($result_cp)==0 and $lead_code!='' and $splitable==false){
	update_not_activated_leads($lead_code,true);
	return;	
}
   if(mysqli_num_rows($result_cp)>0){
   	  while($qdata=mysqli_fetch_array($result_cp)){
   	  	$company_id = $qdata['company_id'];
   	  	$glh_lead	= $qdata['GLH_LEAD_CODE'];
   	    $country=$qdata['GLH_COUNTRY'];
   	  	$state_name=$qdata['GLH_CUST_STATECODE'];
   	  	$implementation_required = $qdata['impl_required'];
   	   	$product_groups=(!empty($qdata['product_groups'])?preg_replace('/\,\,+/',',',$qdata['product_groups']):'');
   	   	$product_groups=(!empty($product_groups)?"'".implode("','",explode(',',$product_groups))."'":'');
   	   	$type_of_group = 1;
   	   	if($implementation_required > 0) {
   	   		$type_of_group = 2;
   	   	}
   	   	$call_routing = get_call_routing_info($company_id, $product_groups, $type_of_group,$country,$state_name);
   	   	$main_product = $call_routing['main_product'];
   	   	$call_pref = $call_routing['call_preferance'];
   	   	if($main_product!=''){
   	   		$upd1 = " update gft_lead_hdr set GLH_MAIN_PRODUCT='$main_product',GLH_MAIN_PRODUCT_UVALIDITITY='0000-00-00', ".
   	   				" GLH_MAIN_PRODUCT_UPDATE_BY=9999,GLH_CALL_PREFERANCE='$call_pref',GLH_ROW_MODIFIED_TIME=GLH_ROW_MODIFIED_TIME where GLH_LEAD_CODE='$glh_lead' ";
   	   		execute_my_query($upd1);
   	   	}
   	  }   	
   }
	  
}

/**
 * @param string $lead_code
 * 
 * @return void 
 */
function update_installation_details($lead_code){
	$wh_query=(!empty($lead_code)?" and gid_lead_code= $lead_code ":'');
	$in_cond = $not_in_cond = '';
	$annuty_team=get_emp_list_by_group_filter(array(54));
	$annuty_team_a=/*. (string[int]) .*/ array();
	for($i=0;$i<count($annuty_team);$i++){
	  			$annuty_team_a[]=$annuty_team[$i][0];	  			
	}
	$annuty_team_cm=implode(',',$annuty_team_a);
	$gmp_product_code_not_in=get_samee_const('NON_ASA_PRODUT_SKIP_CC_SOLUTION');
	if($gmp_product_code_not_in!=''){
		$in_cond = " or gid_lic_pcode in ($gmp_product_code_not_in) ";
		$not_in_cond = " and gid_lic_pcode not in ($gmp_product_code_not_in) ";
	}


$query=<<<END
	 select GID_LEAD_CODE,group_concat(distinct(if(gpm_free_edition='N' $not_in_cond,concat(gid_lic_pcode,'-',substring(gid_lic_pskew,1,4)) ,null))) payable_installation,
	 sum(if(gpm_free_edition='N' $not_in_cond ,1,0)) num_payable_installation,
	 sum(if(gpm_free_edition='N' and gid_validity_date>=date(now()) $not_in_cond,1,0)) validity_payable_installation,
	 
	 group_concat(distinct(if((gpm_free_edition='Y' $in_cond),concat(gid_lic_pcode,'-',substring(gid_lic_pskew,1,4)),null))) free_installation ,
	 sum(if( (gpm_free_edition='Y' $in_cond),1,0)) num_free_installation,
	 sum(if(gpm_free_edition='Y' and (gid_validity_date>=date(now()) $in_cond),1,0)) validity_free_installation,
	 sum(if(gpm_license_type=3,1,0)) num_trial_installation,
	 sum(if(gid_training_status=6,1,0)) training_status_completed,
	 sum(if(gid_training_status>0 and gid_training_status<6,1,0)) training_status_incomplete,
	 sum(if(gid_expire_for=3 and gid_validity_date>=date(now()),1,0)) as 'premium_cust',
	 GLH_LEAD_TYPE, GLH_REFERENCE_GIVEN, sum(if(pfm.gpm_product_code in (300,303),1,0)) as hq_installation 
	 from gft_install_dtl_new 
	 join gft_product_master pm on (pm.gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew) 
	 join gft_product_type_master ptm on (GPT_TYPE_ID=pm.GPM_PRODUCT_TYPE and GPT_CONSIDER_AS_PRODUCT_LICENSE='Y')
	 join gft_lead_hdr on (GLH_LEAD_CODE=GID_LEAD_CODE)   
	 left join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code) 	  
	 where gid_status in ('A','S','UG')  and (pfm.gpm_status='A' or pfm.gpm_product_code=100) $wh_query group by GID_LEAD_CODE 
END;
/* we not went to check gpm_status='A' only because for RE we giving support it is in Inactive status 
 * some of the addon we not going to consider of Annuity */
$result=execute_my_query($query);
$num_rows=mysqli_num_rows($result);
if($result){
	if($num_rows==0 && !empty($lead_code)){
		$del_query="delete from gft_customer_product_info where GCL_LEAD_CODE='$lead_code' ";
		$result_del=execute_my_query($del_query);
	}
	while($qdata=mysqli_fetch_array($result)){
		$num_payable_install		= $qdata['num_payable_installation'];
		$valid_payable_install		= $qdata['validity_payable_installation'];
		$free_installation_count	= $qdata['num_free_installation'];
		$free_installation_validity	= $qdata['validity_free_installation'];
		$table_name='gft_customer_product_info';
		$column_name_arr=array();
		$gid_lead_code = $qdata['GID_LEAD_CODE'];
		$column_name_arr['GCL_LEAD_CODE']=$gid_lead_code;
		$column_name_arr['GCL_PAYABLE_INSTALLATION']=$qdata['payable_installation'];
		$column_name_arr['GCL_PAYABLE_INSTALLATION_COUNT']=$num_payable_install;
		$column_name_arr['GCL_PAYABLE_INSTALLATION_VALIDITY']=$valid_payable_install;
		$column_name_arr['GCL_FREE_INSTALLATION']=$qdata['free_installation'];
		$column_name_arr['GCL_FREE_INSTALLATION_COUNT']=$free_installation_count;
		$column_name_arr['GCL_FREE_INSTALLATION_VALIDITY']=$free_installation_validity;
		$column_name_arr['GCL_EVALUATION_CUST'] = ((int)$qdata['num_trial_installation'] > 0)?'Y':'N';
		$column_name_arr['GCL_TRAIINING_STATUS_COMPLETED']=($qdata['training_status_incomplete']>=1?'N':'Y');
		$column_name_arr['GCL_TRAIINING_STATUS_PENDING']=$qdata['training_status_incomplete'];
		$column_name_arr['GCL_EXTENED_SUPPORT']='N';
		$column_name_arr['GCL_PREMIUM_CUST']=($qdata['premium_cust']>=1?'Y':'N');
		$column_name_arr['GCL_UPDATED_ON']=date('Y-m-d H:i:s');
		$i=0;$column_name='';$values='';
		foreach($column_name_arr as $key => $value){
			$column_name.=($i!=0?",":"")."$key";
			$values.=($i!=0?",":"")."'$value'";
			$i++;
		}
		if($num_payable_install==0){			
			continue;
		}
		$query1="replace into $table_name ($column_name) values ($values)"; 
		$result1=execute_my_query($query1,'',$send_mail_alert=true,true);
		
		$hq_installation = (int)$qdata['hq_installation'];
		if( ($qdata['GLH_LEAD_TYPE']=='13') && ($hq_installation > 0) ){
			$corporate_lead = $qdata['GLH_REFERENCE_GIVEN'];
			$corporate_lead_type = get_single_value_from_single_table("GLH_LEAD_TYPE", "gft_lead_hdr", "GLH_LEAD_CODE", $corporate_lead);
			if($corporate_lead_type=='3'){
				$column_name_arr['GCL_LEAD_CODE']=$corporate_lead;
				$i=0;$column_name='';$values='';
				foreach($column_name_arr as $key => $value){
					$column_name.=($i!=0?",":"")."$key";
					$values.=($i!=0?",":"")."'$value'";
					$i++;
				}
				$query1="replace into $table_name ($column_name) values ($values)";
				$result1=execute_my_query($query1);
			}
		}
		
		$asa_stat = "Valid";
		if($num_payable_install!=$valid_payable_install){
			$asa_stat = "Expired";
		}
		execute_my_query("update gft_lead_hdr_ext set GLE_ASA_STATUS='$asa_stat' where GLE_LEAD_CODE='".$qdata['GID_LEAD_CODE']."'");
	}//end of while
	$add_whr_condition='';
	if($lead_code!=null){
		$add_whr_condition=" and gph_lead_code=$lead_code ";
	}
}//end of if	

}//end of function

/**
 * @param string $lead_code optional param
 * 
 * @return void
 */
function update_call_preferance($lead_code){
	global $server_skew_property;
	$lead_code_cond = " and GLH_LEAD_CODE='$lead_code' ";
	if (empty($lead_code)){
		execute_my_query(" UPDATE gft_customer_contact_dtl,gft_lead_hdr SET GCC_CONTACT=substring(gcc_contact_no,-10) " .
				" WHERE GLH_LEAD_CODE=GCC_LEAD_CODE AND glh_country='India' and gcc_contact_type in (1,2,3) and GCC_CONTACT is null " .
				"AND GCC_CONTACT_NO REGEXP '[A-z]$'=0 ");
		
		$two_days_back = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-2,date("Y")));
		execute_my_query("delete from gft_techsupport_incomming_call_temp where GTC_DATE<'".$two_days_back."'");
		
		$lead_code_cond = "";
	}
	$today_date = date('Y-m-d');
	/* Reset the GLH_MAIN_PRODUCT AND CALL_PREFERENCE --Start */
	$clear_main_product_condition=" and ( GLH_MAIN_PRODUCT_UVALIDITITY<'$today_date' or GLH_MAIN_PRODUCT_UVALIDITITY is null ) ";
	
	//for updating presales as main product alone.  Subgroup will be updated during call / chat routing time
	$result_update1="update gft_lead_hdr set GLH_CALL_PREFERANCE='',GLH_MAIN_PRODUCT=17,GLH_MAIN_PRODUCT_UVALIDITITY='0000-00-00',GLH_ROW_MODIFIED_TIME=GLH_ROW_MODIFIED_TIME where 1  $lead_code_cond $clear_main_product_condition  " ;
	execute_my_query($result_update1);
		
	//for updating customers whose all installations are in uninstalled status
	
	$sub1 = " select gid_lead_code,count(gid_install_id) cnt ,sum(if(gid_status='U',1,0)) uninstall_count  ".
			" from  gft_install_dtl_new ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GID_LEAD_CODE) ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
			" where GPM_FREE_EDITION='N' $lead_code_cond group by  gid_lead_code having cnt=uninstall_count ";
	
	$upd_uninstall= " update gft_lead_hdr lh ,($sub1) t ".
					" set GLH_IS_CUSTOMER='N' ,GLH_CALL_PREFERANCE='{\"1\":\"703\"}',GLH_MAIN_PRODUCT=1,GLH_MAIN_PRODUCT_UVALIDITITY='0000-00-00',GLH_ROW_MODIFIED_TIME=GLH_ROW_MODIFIED_TIME ".
					" where lh.glh_lead_code=gid_lead_code $lead_code_cond $clear_main_product_condition ";  
	execute_my_query($upd_uninstall);
		
	//update presales group and subgroup for each assure care company
	$sql1 = " select GAC_REF_ID,GPC_PRESALES_GROUP,GVG_GROUP_ID ".
			" from gft_assure_care_company ".
			" join gft_product_company_mapping on (GPC_COMPANY_ID=GAC_ID) ".
			" join gft_voicenap_group on (GVG_SUPPORT_GROUP=GPC_PRESALES_GROUP and GVG_STATUS='A') ".
			" where GAC_ID!=1 group by GAC_REF_ID ";
	$res1 = execute_my_query($sql1);
	while ($row1 = mysqli_fetch_array($res1)){
		$ref_lead 		= $row1['GAC_REF_ID'];
		$pre_main_prod 	= $row1['GPC_PRESALES_GROUP'];
		$subgroup[1]	= $row1['GVG_GROUP_ID'];
		$pre_call_pref	= json_encode($subgroup);
		$upd_pres = " update gft_lead_hdr set GLH_MAIN_PRODUCT='$pre_main_prod',GLH_CALL_PREFERANCE='$pre_call_pref',GLH_MAIN_PRODUCT_UVALIDITITY='0000-00-00',GLH_ROW_MODIFIED_TIME=GLH_ROW_MODIFIED_TIME ".
					" where GLH_REFERENCE_OF_PARTNER='$ref_lead' and GLH_LEAD_SOURCE_CODE_PARTNER=37 $lead_code_cond $clear_main_product_condition ";
		execute_my_query($upd_pres);
	}
		
	//order created but not installed
	update_not_activated_leads($lead_code);

	//Going to update for installation customers
	update_installation_details($lead_code);
	
	$query_head="SELECT GID_LEAD_CODE,glh_country, GLH_CUST_STATECODE, GLH_MAIN_PRODUCT ,GLH_CALL_PREFERANCE,GLH_MAIN_PRODUCT_UVALIDITITY ," .
		" group_concat(distinct if(GPM_PRODUCT_PRIME_TYPE='Y' and gpm_free_edition='N',concat(GID_HEAD_OF_FAMILY,'-', substring(GID_LIC_PSKEW,1,4)),null)) product_groups," .
		" group_concat(distinct if((GPM_PRODUCT_PRIME_TYPE='Y'),concat(GID_HEAD_OF_FAMILY,'-', substring(GID_LIC_PSKEW,1,4)),null)) product_groups2," .
		" if( (glh_lead_type='3' or (glh_lead_type=13 and GLH_LEAD_SOURCECODE=7 and glh_reference_given!='') ),'corp','end_user') lead_type ," .
		" sum(if(gid_training_status>0 and gid_training_status<6,1,0)) training_status_incomplete,glh_lead_type, " .
		" GCL_PAYABLE_INSTALLATION,GCL_PAYABLE_INSTALLATION_COUNT,GCL_PAYABLE_INSTALLATION_VALIDITY,GCL_FREE_INSTALLATION_VALIDITY,GCL_PREMIUM_CUST," .
		" count(distinct(if(GOD_IMPL_REQUIRED='Yes',GOD_ORDER_NO,null))) as GOD_IMPL_REQUIRED ,GID_TRAINING_STATUS ," .
		" group_concat(distinct(if(GOD_IMPL_REQUIRED='Yes',GPG_PD_EXPERT_GROUP,null)) order by GSP_PREFERRED_ORDER) as 'ssg', " .
		" ifnull(GAC_ID,1) as company_id FROM gft_lead_hdr " .
		" join gft_customer_product_info cpi on (gcl_lead_code=glh_lead_code) ".
		" join gft_install_dtl_new ins on (GID_LEAD_CODE=GLH_LEAD_CODE ) " .
		" join gft_order_hdr oh on (god_order_no=gid_order_no and god_order_status='A' )" .
		" join gft_product_master pm on (pm.gpm_product_code=gid_lic_pcode and pm.gpm_product_skew=gid_lic_pskew ) " .
		" join gft_product_family_master pfm on (GID_HEAD_OF_FAMILY=pfm.gpm_product_code) " .
		" left join gft_assure_care_company on (GAC_REF_ID=GLH_REFERENCE_OF_PARTNER and GLH_LEAD_SOURCE_CODE_PARTNER=37) ".
		" left join gft_product_group_master pgm on (pgm.gpg_product_family_code=pfm.gpm_product_code AND gpg_skew=substr(gid_lic_pskew,1,4) and  GPM_PRODUCT_PRIME_TYPE='Y') ".
 	  	" left join gft_support_product_group sg on (GSP_GROUP_ID=GPG_PD_EXPERT_GROUP and GSP_PD_SUPPORT_GROUP='Y') ".
 		" where gid_status!='U' ".($lead_code!=''? " and GLH_LEAD_CODE=$lead_code":"")." group by GLH_LEAD_CODE ";
	$result_head=execute_my_query($query_head);
	while($data_head=mysqli_fetch_array($result_head)){
		$main_product='';
		$call_preference='';
		$glh_lead_type='';
		$hq_installed=0;
		$corporate_lead=0;
		$lead_code=$data_head['GID_LEAD_CODE'];
		$product_groups=(($data_head['product_groups2']!='' and $data_head['product_groups']=='')?$data_head['product_groups2']:$data_head['product_groups']);
		$country=$data_head['glh_country'];
		$state_name=$data_head['GLH_CUST_STATECODE'];
		$lead_type=$data_head['lead_type'];
		$company_id = $data_head['company_id'];
		$product_groups=(!empty($product_groups)?preg_replace('/\,\,+/',',',$product_groups):'');
		$product_groups=(!empty($product_groups)?"'".implode("','",explode(',',$product_groups))."'":'');
		$is_Premium=( (isset($data_head['GCL_PREMIUM_CUST']) and $data_head['GCL_PREMIUM_CUST']=='Y') ? true : false  );
		if($lead_type=='corp' and !empty($lead_code)){
		    $all_ids = implode(",", get_corporate_chain_customer_ids($lead_code));
		    if($all_ids!=''){
		        $query_corp_check="select lh.GLH_LEAD_SOURCECODE, lh.glh_reference_given,corp.glh_lead_type corp_type,ifnull(GAC_ID,1) as company_id, " .
		  		        " sum(if(GPM_HEAD_FAMILY in (300,303), 1,0)) as hq_installed from  gft_lead_hdr lh " .
		  		        " join gft_install_dtl_new on (GID_LEAD_CODE=lh.GLH_LEAD_CODE) ".
		  		        " join gft_product_family_master pfm on (gid_lic_pcode=pfm.GPM_PRODUCT_CODE and GPM_HEAD_FAMILY in (300,303))   " .
		  		        " join gft_product_master pm on (gid_lic_pcode=pm.GPM_PRODUCT_CODE and pm.GPM_PRODUCT_SKEW=gid_lic_pskew and pm.GFT_SKEW_PROPERTY in ($server_skew_property) )" .
		  		        " left join gft_assure_care_company on (GAC_REF_ID=lh.GLH_REFERENCE_OF_PARTNER and lh.GLH_LEAD_SOURCE_CODE_PARTNER=37) ".
		  		        " left join gft_lead_hdr corp on (lh.glh_reference_given=corp.GLH_LEAD_CODE)" .
		  		        " where lh.GLH_LEAD_CODE in ($all_ids) and GID_STATUS!='U' having hq_installed > 0 ";
		        $result_corp_check=execute_my_query($query_corp_check);
		        if($data_check=mysqli_fetch_array($result_corp_check)){
		            $glh_lead_type = $data_head['glh_lead_type'];
		            $hq_installed = (int)$data_check['hq_installed'];
		            $corporate_lead = get_corporate_customer_for_client($lead_code);
		            $call_routing = get_call_routing_info($data_check['company_id'],'300',1,$country,$state_name);
		            $call_preference=$call_routing['call_preferance'];
		            $main_product=$call_routing['main_product'];
		        }else{
		            $lead_type='end_user';
		        }
		    }else{
		        $lead_type='end_user';
		    }
		}
		if( ($lead_type!='corp') && !empty($product_groups) ){ 
			$ROUTE_PD_SUPPORT_GROUP=get_samee_const('ROUTE_TO_PD_SUPPORT_PROCESS_TILL_24X7HANDOVER');
			$impl_required = (int)$data_head['GOD_IMPL_REQUIRED'];
			$gid_training_Status = (int)$data_head['GID_TRAINING_STATUS'];
			$training_incomplete = (int)$data_head['training_status_incomplete'];
			$type_of_group = 1; //core support groups
			if($ROUTE_PD_SUPPORT_GROUP=='Y' and $impl_required>=1 and $gid_training_Status!=6){
				$type_of_group = 2; //PD support groups
			}
			$call_routing = get_call_routing_info($company_id, $product_groups,$type_of_group,$country,$state_name);
			$call_preference = $call_routing['call_preferance'];
			$main_product	 = $call_routing['main_product'];
	    }
		if( ($main_product!='') && ($hq_installed==0 || $glh_lead_type=='3') ){
		    $lead_hdr_update="update  gft_lead_hdr set " .
				" GLH_CALL_PREFERANCE='$call_preference', GLH_MAIN_PRODUCT='$main_product'," .
				" GLH_MAIN_PRODUCT_UPDATE_BY=9999 ," .
				" GLH_MAIN_PRODUCT_UVALIDITITY='0000-00-00',GLH_ROW_MODIFIED_TIME=GLH_ROW_MODIFIED_TIME " .
				" where  GLH_LEAD_CODE=$lead_code  $clear_main_product_condition ";
			$result_update=execute_my_query($lead_hdr_update);
		}
		if($lead_type=='corp'){
			if($glh_lead_type=='3'){
				$query_corp_clients=" update gft_lead_hdr corp,gft_lead_hdr cc set cc.glh_main_product=corp.glh_main_product ," .
				        " cc.GLH_CALL_PREFERANCE=corp.GLH_CALL_PREFERANCE ,".
						" cc.GLH_MAIN_PRODUCT_UPDATE_BY=corp.GLH_MAIN_PRODUCT_UPDATE_BY  ," .
						" cc.GLH_MAIN_PRODUCT_UVALIDITITY=corp.GLH_MAIN_PRODUCT_UVALIDITITY, " .
						" cc.GLH_ROW_MODIFIED_TIME=cc.GLH_ROW_MODIFIED_TIME ".
						" where cc.glh_reference_given=corp.glh_lead_code and cc.glh_lead_sourcecode=7 and " .
						" corp.glh_lead_code=$lead_code and corp.glh_lead_type=3 and cc.glh_lead_type=13 and cc.GLH_MAIN_PRODUCT_UVALIDITITY='0000-00-00' ";
				execute_my_query($query_corp_clients);
			}else if( ($glh_lead_type=='13') && ($hq_installed>0) ){
				$query_corp_customer=" update gft_lead_hdr corp,gft_lead_hdr cc set corp.glh_main_product=cc.glh_main_product ," .
						" corp.GLH_CALL_PREFERANCE=cc.GLH_CALL_PREFERANCE ,".
						" corp.GLH_MAIN_PRODUCT_UPDATE_BY=cc.GLH_MAIN_PRODUCT_UPDATE_BY  ," .
						" corp.GLH_MAIN_PRODUCT_UVALIDITITY=cc.GLH_MAIN_PRODUCT_UVALIDITITY, " .
						" corp.GLH_ROW_MODIFIED_TIME=corp.GLH_ROW_MODIFIED_TIME ".
						" where corp.GLH_CALL_PREFERANCE='' and cc.glh_reference_given=corp.glh_lead_code and cc.glh_lead_sourcecode=7 and " .
						" cc.glh_lead_code=$lead_code  and corp.glh_lead_type=3 and cc.glh_lead_type=13 and corp.GLH_MAIN_PRODUCT_UVALIDITITY='0000-00-00' ";
				execute_my_query($query_corp_customer);
				$query_corp_clients=" update gft_lead_hdr corp,gft_lead_hdr cc set cc.glh_main_product=corp.glh_main_product ," .
						" cc.GLH_CALL_PREFERANCE=corp.GLH_CALL_PREFERANCE ,".
						" cc.GLH_MAIN_PRODUCT_UPDATE_BY=corp.GLH_MAIN_PRODUCT_UPDATE_BY  ," .
						" cc.GLH_MAIN_PRODUCT_UVALIDITITY=corp.GLH_MAIN_PRODUCT_UVALIDITITY, " .
						" cc.GLH_ROW_MODIFIED_TIME=cc.GLH_ROW_MODIFIED_TIME ".
						" where cc.glh_reference_given=corp.glh_lead_code and cc.glh_lead_sourcecode=7 and " .
						" corp.glh_lead_code=$corporate_lead  and corp.glh_lead_type=3 and cc.glh_lead_type=13 and cc.GLH_MAIN_PRODUCT_UVALIDITITY='0000-00-00' ";
				execute_my_query($query_corp_clients);
			}
		}
	}/* end of while*/	
	
}//end of function	

	
?>
