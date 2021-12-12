<?php
require_once( __DIR__ . "/../dbcon.php");
require_once( __DIR__ . "/../function.update_in_tables.php");
require_once(__DIR__."/../cache_impl.php");

/**
 * @param string $gcc_column_name
 * @param string $phone_no
 *
 * @return string
 */

function getContactDtlWhereCondition($gcc_column_name, $phone_no){
    $sph_no=substr($phone_no,-10);
    $phone_no = ltrim($phone_no,"00"); // to support international numbers from intellicon and mydelight call save
    //$cust_count_numbers.=" and gtc_number like '%$phone_no'";
    $where_condition=" ($gcc_column_name ='$sph_no' or $gcc_column_name ='0$sph_no' or $gcc_column_name='91$sph_no' or $gcc_column_name='00$sph_no' or $gcc_column_name='$phone_no' or $gcc_column_name='0$phone_no' or $gcc_column_name='00$phone_no' )";
    return $where_condition;
}

/**
 * @param string $username
 * 
 * @return int
 */
function get_emp_id_cache($username){
	$key="EMP_ID_".$username;
	$empid_str_val = Cache::getString($key);
	if ($empid_str_val === null){
		$query_login_master="select SQL_CACHE GLM_EMP_ID, GLM_LOGIN_NAME from gft_login_master where GLM_LOGIN_NAME='$username' ";
		$result=execute_my_query($query_login_master);
		if(mysqli_num_rows($result)==1 and $data=mysqli_fetch_array($result)){
			$emp_id=(int)$data['GLM_EMP_ID'];
		}else{
			$emp_id=-1;
		}
		Cache::putString($key,"$emp_id");
	}else{
		$emp_id=(int)$empid_str_val;
	}

	return $emp_id;
}

/**
 * @param string $date  - date is in yyyy-mm-dd format
 *
 * @return boolean
 */
function is_holiday_cache($date){
	$date=trim($date);
	$holiday="select SQL_CACHE * from gft_holiday_list where ghl_date='".$date."' and GHL_TECHSUPPORT_GENERAL_SHIFT='N' ";
	$result=execute_my_query($holiday,'',true,false,2);
	if(mysqli_num_rows($result)!=0){
		return true;
	}else{
		return false;
	}
}

/**
 * @param string $lead_code
 *
 * @return void
 */
function update_presales_subgroup($lead_code){
	$call_routing[1]=get_lead_mgmt_incharge(0,0,0,0,0,$lead_code,true);
	$encode_call_routing = json_encode($call_routing);
	execute_my_query("UPDATE gft_lead_hdr SET GLH_CALL_PREFERANCE='$encode_call_routing' where GLH_LEAD_CODE='$lead_code'");
}
/** 
 * @param string $CustomerCallerID
 * 
 * @return mixed[string]
 */
function get_call_group_of_not_installed($CustomerCallerID){
		/* check in not installed group */

$gcc_contact_no_where_condtion=getContactDtlWhereCondition('gcc_contact_no',$CustomerCallerID);
$query_not_installed=<<<END
	  select GLH_LEAD_CODE,GLH_STATUS,GLH_LEAD_TYPE,GLH_PROSPECTS_STATUS,GLH_CALL_PREFERANCE,GLH_MAIN_PRODUCT,GLH_MAIN_PRODUCT_UVALIDITITY,GLH_COUNTRY  
	  from gft_customer_contact_dtl 
	  left join gft_lead_hdr lh on (glh_lead_code=gcc_lead_code) 
	  left join gft_support_product_group sg on (gsp_group_id=GLH_MAIN_PRODUCT and GSP_STATUS='A')
	  where glh_lead_type!=8 and $gcc_contact_no_where_condtion   group by GLH_LEAD_CODE order by GSP_PREFERRED_ORDER 
END;

	  //where glh_lead_type!=8 and gcc_contact_no like '%$CustomerCallerID'  group by GLH_LEAD_CODE order by GSP_PREFERRED_ORDER 


		$result_not_installed=execute_my_query($query_not_installed);
		$count_not_installed=mysqli_num_rows($result_not_installed);
		$qd_ni=mysqli_fetch_array($result_not_installed);
		
		if($count_not_installed!=0){
			 $lead_code=$qd_ni['GLH_LEAD_CODE'];
			 $lead_type=(int)$qd_ni['GLH_LEAD_TYPE'];			 
			 if($qd_ni['GLH_MAIN_PRODUCT']=='17'){//If presales, update sub-group
			 	update_presales_subgroup($lead_code);
			 }
			 $json_call_group = get_single_value_from_single_table("GLH_CALL_PREFERANCE", "gft_lead_hdr", "GLH_LEAD_CODE", "$lead_code");
			 if($qd_ni['GLH_MAIN_PRODUCT']!='' and ($qd_ni['GLH_MAIN_PRODUCT_UVALIDITITY']!='0000-00-00' or $qd_ni['GLH_MAIN_PRODUCT_UVALIDITITY']> date('Y-m-d') )){
			 	
			 	    $return_cc_details=/*. (mixed[string]) .*/ array(
					'json_call_group'=>$qd_ni['GLH_CALL_PREFERANCE'],
			   		//'preferred_person'=>get_pre_call_preferance_new($qdata['corporate_lead']),
				   	'lead_code'=>$lead_code,
				   	'call_center_div'=>$qd_ni['GLH_MAIN_PRODUCT']
				   	);
				   	return $return_cc_details;
			 }else if($lead_type !=2){
			 	   	$return_cc_details=/*. (mixed[string]) .*/ array(
					'lead_code'=>$lead_code,
				   	'call_center_div'=>$qd_ni['GLH_MAIN_PRODUCT'],
			 	   	'json_call_group'=>($json_call_group!=""?$json_call_group:"")
				   	);
					return $return_cc_details;
 			 }else if($lead_type==2){
			 	
			 	   	$return_cc_details=/*. (mixed[string]) .*/ array(
					'lead_code'=>$lead_code,
				   	'call_center_div'=>20
				   	);
				   				   	
					return $return_cc_details;			
			 }else {
			 	
			 	   $return_cc_details=/*. (mixed[string]) .*/ array(		 
		   			'call_center_div'=>'17',
			 	   	'json_call_group'=>($json_call_group!=""?$json_call_group:"")
	   				);
	   			   return $return_cc_details;	
			 }
		}
		else {
			 	
			 	   $return_cc_details=/*. (mixed[string]) .*/ array(		 
		   			'call_center_div'=>'17'
	   				);
	   			   return $return_cc_details;	
		}
}//end of fuction		

/**
 * @param string $request
 * @param string $return_value
 * @param string $remarks
 * @param string $based_on
 * @param string $msn_no
 * 
 * @return void
 */
function enter_call_center_request($request,$return_value,$remarks,$based_on=null,$msn_no=''){
	$return_value=mysqli_real_escape_string_wrapper($return_value);
	$RREMOTE_ADDR=$_SERVER['REMOTE_ADDR'];
	$RPHP_SELF=basename($_SERVER['PHP_SELF']);
	$RREQUEST_METHOD=$_SERVER['REQUEST_METHOD'];
	$REQUEST_TIME=date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']);
	$based_on=mysqli_real_escape_string_wrapper($based_on);

	$micro_sec_diff = getDeltaTime();
	$ins_query=" INSERT INTO gft_call_center_request(GCC_REQUEST,GCC_RESPONSE,GCC_DATETIME,GCC_REMAKS,GCC_FILE_NAME,GCC_REMOTE_ADDR," .
			"GCC_REQUEST_METHOD,GCC_REQUEST_TIME,GCC_PROCESSING_TIME,GCC_MICRO_SEC_DIFF,GCC_RESULT_BASED_ON,GCC_MSN_NUMBER) values " .
			"('$request','$return_value',now(),'$remarks','$RPHP_SELF','$RREMOTE_ADDR','$RREQUEST_METHOD','$REQUEST_TIME'," .
			"TIMEDIFF(now(),'$REQUEST_TIME'),'$micro_sec_diff','$based_on','$msn_no')";			
	execute_my_query($ins_query);

	if ($micro_sec_diff>2.0){ //more than 2 seconds for processing...
		error_log("Performance problem in ".$RPHP_SELF.". Took ".$micro_sec_diff." seconds.  more than 2 second.");
	}
}

/**
 * @param string $contact_no
 * 
 * @return boolean
 */
function check_its_exists_in_SAM($contact_no){
	//$query="select gcc_lead_code from gft_customer_contact_dtl where substring(gcc_contact_no,-10) =substring('$contact_no',-10) and length(gcc_contact_no)>=10 ";	
	$query=" select gcc_lead_code from gft_customer_contact_dtl ".
	   	   " left join gft_pos_users on (GPU_CONTACT_ID=GCC_ID) ".
	       " where if(GPU_CONTACT_STATUS is null,1,GPU_CONTACT_STATUS='A') and ". getContactDtlWhereCondition('gcc_contact_no',$contact_no) . "";
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)>0){
		return true;
	}else {		
		return false;
	}
	
}

/**
 * @return mixed
 */
function its_forwarded_to_mobile_numbers(){
		$result_emergency=execute_my_query("select GMD_DEP1 from gft_mskip_dtl where GMD_CODE=5 and GMD_NAME='voicesnap_forward' and GMD_STATUS='A'");
		if(mysqli_num_rows($result_emergency)==1 and $data_check=mysqli_fetch_array($result_emergency)){
			$mobile_no_forward=array_trim(explode(',',$data_check['GMD_DEP1']));
			return $mobile_no_forward;
		}
		return null;	
}

/** 
 * @param string  $CustomerCallerID
 * 
 * @return boolean
 */
function its_employee_contact_no($CustomerCallerID){
			$query_emp="SELECT GEM_EMP_ID FROM gft_emp_master WHERE GEM_EMP_ID < 7000 and GEM_STATUS='A' " .
					" and (GEM_MOBILE like '%$CustomerCallerID' or GEM_RELIANCE_NO like '%$CustomerCallerID' or GEM_RESIDENCE_NO like '%$CustomerCallerID')";
			$result_emp=execute_my_query($query_emp);
			$num_rows=mysqli_num_rows($result_emp);
			if($CustomerCallerID=='4442237800' or $CustomerCallerID=='4439200200' or $num_rows!=0){
				return true;
			}else{
				return false;
			}
}

/** 
 * @param string $CustomerCallerID
 * @param string $email
 * 
 * @return string[string]
 */
function its_active_partner_no($CustomerCallerID='',$email=''){
	/* contact no check in gft_customer_contact_dtl and employee list*/
	$contact_condition=$email_condition='';
	if($CustomerCallerID!=''){
		$contact_no_cond = getContactDtlWhereCondition("gcc_contact_no", $CustomerCallerID);
		$gem_mobile_cond = getContactDtlWhereCondition("GEM_MOBILE", $CustomerCallerID);
		$contact_condition = " ($contact_no_cond or $gem_mobile_cond)";
	}else if($email!=''){
		$email_condition=" (GEM_EMAIL='$email' or gcc_contact_no='$email')";
	}
	
	
$query_non_emp=<<<END
	select GEM_EMP_ID,GEM_EMAIL,GLH_LEAD_CODE,GLH_CUST_NAME,GLH_CUST_STREETADDR2,gem_role_id,gcc_contact_name
	from gft_cp_info cp 
	join gft_leadcode_emp_map lemp on (GLEM_LEADCODE=CGI_LEAD_CODE )
	join gft_emp_master em on (CGI_EMP_ID=em.gem_emp_id and em.gem_status='A' and gem_role_id in (21,26,27,83) )
	join gft_lead_hdr lh on (lh.GLH_LEAD_CODE=CGI_LEAD_CODE )
	join gft_customer_contact_dtl ccd on (GCC_LEAD_CODE=GLH_LEAD_CODE)
	where $contact_condition $email_condition
	and CGI_EMP_ID!=7004 and 
	(CGI_STATUS=10 OR (CGI_STATUS=14 AND CGI_STATUS_TILL_DATE<=date(now()) ) )
	limit 1 
END;
		$result_non_emp=execute_my_query($query_non_emp);
		$num_rows=mysqli_num_rows($result_non_emp);
		$return_result=/*. (string[string]) .*/ array();
		$return_result['is_cp']='false';
		$return_result['is_dealer']='false';
		if($num_rows>0){
			$return_result['is_cp']='true';
			$qd=mysqli_fetch_array($result_non_emp);
			$return_result['cp_lead_code']=$qd['GLH_LEAD_CODE'];
			$return_result['cp_name']=$qd['GLH_CUST_NAME'];
			$return_result['cp_contact_name'] = $qd['gcc_contact_name'];
			$return_result['cp_email']=$qd['GEM_EMAIL'];			
			$return_result['cp_location']=$qd['GLH_CUST_STREETADDR2'];
			if($qd['gem_role_id']=='83'){
				$return_result['is_dealer']='true';
			}
		}
		return $return_result;
	
}


/**
 * @param string $lead_code
 * @param string $debug_preference
 * 
 * @return string[int]
 */

function get_pre_call_preferance_new($lead_code,&$debug_preference){

	//NOTE: This statement is to avoid $debug_preference error as it is passed as Reference argument
	if (false){echo "debug_preference=".$debug_preference;}

	//$call_group='';
	$call_group_empid=/*. (string[int]) .*/ array();
	//$SALES_DUMMY_ID=get_samee_const('SALES_DUMMY_ID');
	$SALES_DUMMY_ID_VAL=SALES_DUMMY_ID; //NOTE: SALES_DUMMY_ID is defined in dbcon.php
$query=<<<END
select glh_lead_code, pre.GLM_LOGIN_NAME pre_ip_address, dtl.gcd_employee_id, GTM_NAME, GSP_GROUP_MANAGER ,manger.GLM_LOGIN_NAME manager_ip_address, 
 group_concat(distinct(if(assign_to.gem_emp_id!='',assign_to.gem_emp_id,null)) order by GCD_ACTIVITY_ID desc) as assign_to, 
 group_concat(distinct(if(assign_to.gem_emp_id!='',assign_to.gem_emp_name,null)) order by GCD_ACTIVITY_ID desc ) as assign_to_name,
 sum(if(GCH_ESCALATION='Y',1,0)) Escalated_flag 
 from  gft_customer_support_dtl dtl 
 join gft_customer_support_hdr hdr on (dtl.gcd_complaint_id=hdr.gch_complaint_id and hdr.GCH_LAST_ACTIVITY_ID=GCD_ACTIVITY_ID) 
 join gft_lead_hdr lh on (lh.GLH_LEAD_CODE=hdr.GCH_LEAD_CODE) 
 join gft_support_product_group on (GSP_GROUP_ID=GLH_MAIN_PRODUCT) 
 join gft_emp_master em on (dtl.gcd_employee_id=em.gem_emp_id and em.GEM_STATUS='A' and em.GEM_OFFICE_EMPID!=0 ) 
 left join gft_emp_master assign_to on (dtl.GCD_PROCESS_EMP=assign_to.gem_emp_id and assign_to.GEM_STATUS='A' and assign_to.GEM_OFFICE_EMPID!=0 ) 
 JOIN gft_status_master F on (hdr.GCH_CURRENT_STATUS = F.GTM_CODE and prob=1) 
 left join gft_login_master pre on(pre.GLM_EMP_ID=dtl.gcd_employee_id ) 
 left join gft_login_master manger on(manger.GLM_EMP_ID=GSP_GROUP_MANAGER ) 
 left join gft_login_master assi_ip on(assi_ip.GLM_EMP_ID=assign_to.gem_emp_id ) 
 where hdr.GCH_LEAD_CODE='$lead_code' and GCH_COMPLAINT_CODE not in (306,307) and gch_current_status!='T2' and dtl.gcd_employee_id!=$SALES_DUMMY_ID_VAL  
 group by glh_lead_code 
END;
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)==1 and $data=mysqli_fetch_array($result)){
		//$age=$data['Age']; /* removed age check */
		$escalated_flag=(int)$data['Escalated_flag'];
		
		if($data['assign_to']!='' and $escalated_flag==0){
			$debug_preference='assign_to exists';
			$call_group_empid=explode(',',$data['assign_to']);
		}
		else if($data['manager_ip_address'] and $escalated_flag>0){
			$debug_preference='manager_ip_address exists';
				//$call_group=$data['manager_ip_address'];
				$call_group_empid[]=$data['GSP_GROUP_MANAGER'];
		
		}else if($escalated_flag>0) {
			$debug_preference='Escalated_flag greater than zero';
			$smanager_list=get_emp_list_by_group_filter(array(63)); /* support Manager */
			$smast=/*. (string[int]) .*/ array();
			for($i=0;$i<count($smanager_list);$i++){
				$smast[]=$smanager_list[$i][0];				
			}
			if(count($smast)>0){
				//$ip_address='';
				$smast_emp=implode(',',$smast);
				$query=" select GLM_LOGIN_NAME ip_address,GLM_EMP_ID from gft_voicesnap_online_user " .
						" join gft_login_master lm on (GLM_EMP_ID=GVS_EMP_ID) where GVS_EMP_ID in ($smast_emp)  limit 1  ";
				$result=execute_my_query($query);
				if($data_ip=mysqli_fetch_array($result)){ 
					$call_group_empid[]=$data_ip['GLM_EMP_ID'];					
				}
			}
		}
	}

	//if(!empty($call_group_empid)){
	//	return $call_group_empid;
	//}else{
	//	return '';	
	//}
	return $call_group_empid;
}

/* NOT USED
 * @param int $support_group
 * 
 * @return string
function get_call_groups_for_support_group($support_group){
		$query_sg=" SELECT group_concat(gvg_group_id order by gvg_prefer_order) call_groups FROM gft_voicenap_group where gvg_support_group=$support_group ";
		$result_sg=execute_my_query($query_sg);
		if($result_sg){
			if(mysqli_num_rows($result_sg)==1){
			$qd_sg=mysqli_fetch_array($result_sg);
			$call_groups=$qd_sg['call_groups'];
			return $call_groups;
			}
			 
		}
		return null;
}
*/


/**
 * @param string $CustomerCallerID
 * 
 * @return mixed[string]
 */
function get_result_for_inbound_in_group($CustomerCallerID){
		
	$debug_preference="";
			
$gcc_contact_no_where_condtion=getContactDtlWhereCondition('gcc_contact_no',$CustomerCallerID);
$query=<<<END
	  select GLH_LEAD_CODE,GLH_STATUS,GLH_LEAD_TYPE,GLH_PROSPECTS_STATUS,GLH_CALL_PREFERANCE,GLH_MAIN_PRODUCT,
	  GCL_PAYABLE_INSTALLATION,GCL_PAYABLE_INSTALLATION_COUNT,GCL_PAYABLE_INSTALLATION_VALIDITY,
	  GCL_FREE_INSTALLATION,GCL_FREE_INSTALLATION_COUNT,GCL_EXTENED_SUPPORT,GCL_EVALUATION_CUST,
	  GCL_FREE_INSTALLATION_VALIDITY,GCL_TRAIINING_STATUS_COMPLETED,GCL_TRAIINING_STATUS_PENDING,GLH_COUNTRY,
	  GLH_CALL_PREFERANCE,GLH_MAIN_PRODUCT,GLH_MAIN_PRODUCT_UPDATE_BY,GLH_MAIN_PRODUCT_UVALIDITITY,
	  if(GCL_PAYABLE_INSTALLATION_VALIDITY < GCL_PAYABLE_INSTALLATION_COUNT,GCL_LEAD_CODE,null) as Annuity_lead_codes
	  from gft_customer_contact_dtl 
	  left join gft_lead_hdr lh on (glh_lead_code=gcc_lead_code) 
	  left join gft_customer_product_info cc on (gcl_lead_code=glh_lead_code) 
	  where glh_lead_type!=8 and $gcc_contact_no_where_condtion and gcl_lead_code!='' group by GLH_LEAD_CODE
END;
	  //where glh_lead_type!=8 and gcc_contact_no like '%$CustomerCallerID' and gcl_lead_code!='' group by GLH_LEAD_CODE


	$result=execute_my_query($query);
	$count_num_leads_match=mysqli_num_rows($result);
	
	$return_cc_details=/*. (mixed[string]) .*/ array();
	if($count_num_leads_match==0){
		$return_cc_details=get_call_group_of_not_installed($CustomerCallerID);
		return $return_cc_details;
	}
	else if($count_num_leads_match>=1){
		$qdata=mysqli_fetch_array($result);
		
		if($count_num_leads_match>1){
$query_group=<<<END
			  select GLH_LEAD_CODE,GLH_CALL_PREFERANCE,GLH_MAIN_PRODUCT,GLH_COUNTRY,if(glh_lead_type in (3,13),'Y','N') as corporate,
			  sum(if(GLH_MAIN_PRODUCT=6,1,0)) 'hq_installed','6' as 'hq_product_group',	
			  GLH_MAIN_PRODUCT_UPDATE_BY,GLH_MAIN_PRODUCT_UVALIDITITY,		  
			  if(GLH_MAIN_PRODUCT=6 and glh_lead_type in (3,13),GLH_CALL_PREFERANCE,null) as 'hq_GLH_CALL_PREFERANCE',
			  if(glh_lead_type in (3,13),GLH_LEAD_CODE,'N') as 'corporate_lead',
			  sum(GCL_PAYABLE_INSTALLATION) as GCL_PAYABLE_INSTALLATION,sum(GCL_PAYABLE_INSTALLATION_COUNT) as GCL_PAYABLE_INSTALLATION_COUNT,
			  sum(GCL_PAYABLE_INSTALLATION_VALIDITY) as GCL_PAYABLE_INSTALLATION_VALIDITY,
			  sum(GCL_FREE_INSTALLATION) as GCL_FREE_INSTALLATION ,
			  sum(GCL_FREE_INSTALLATION_COUNT) as GCL_FREE_INSTALLATION_COUNT,
			  sum(GCL_FREE_INSTALLATION_VALIDITY) as GCL_FREE_INSTALLATION_VALIDITY,
			  sum(GCL_TRAIINING_STATUS_COMPLETED) as GCL_TRAIINING_STATUS_COMPLETED,
			  sum(GCL_TRAIINING_STATUS_PENDING) as GCL_TRAIINING_STATUS_PENDING,GCL_EXTENED_SUPPORT,GCL_EVALUATION_CUST,
			  group_concat(distinct(if(GCL_PAYABLE_INSTALLATION_VALIDITY < GCL_PAYABLE_INSTALLATION_COUNT,GCL_LEAD_CODE,null))) as Annuity_lead_codes,  
			  group_concat(distinct(if(GCL_PAYABLE_INSTALLATION_VALIDITY < GCL_PAYABLE_INSTALLATION_COUNT,GLH_MAIN_PRODUCT,null))) as Annuity_Main_product
			  from gft_customer_contact_dtl 
              left join gft_pos_users on (GPU_CONTACT_ID=GCC_ID)  
			  left join gft_lead_hdr lh on (glh_lead_code=gcc_lead_code) 
              left join gft_lead_hdr_ext on (glh_lead_code=gle_lead_code)
			  left join gft_support_product_group sg on (gsp_group_id=glh_main_product)
			  left join gft_customer_product_info cc on (gcl_lead_code=glh_lead_code)
			  where glh_lead_type!=8 and $gcc_contact_no_where_condtion and gcl_lead_code!=''
              and if(GPU_CONTACT_STATUS is null,1,GPU_CONTACT_STATUS='A') and if(gle_support_mode=2,gle_available_service_mins>0,1) 
              group by glh_lead_code order by if(gle_support_mode=2,0,1),glh_lead_code
END;
			
				$result_group=execute_my_query($query_group);
				$qdata=mysqli_fetch_array($result_group);			
		}
		
        $lead_code=(isset($qdata['GLH_LEAD_CODE'])?$qdata['GLH_LEAD_CODE']:'');
        $payable_installation_count = (int)$qdata['GCL_PAYABLE_INSTALLATION_COUNT'];
        $payable_install_valid		= (int)$qdata['GCL_PAYABLE_INSTALLATION_VALIDITY'];
        $free_installation_count	= (int)$qdata['GCL_FREE_INSTALLATION_COUNT'];
        $free_installation_validity	= (int)$qdata['GCL_FREE_INSTALLATION_VALIDITY'];
        $evaluation_customer		= $qdata['GCL_EVALUATION_CUST'];
       	if($qdata['GLH_MAIN_PRODUCT']=='17'){//If presales, update sub-group
        	update_presales_subgroup($lead_code);
        }
        $json_call_group = get_single_value_from_single_table("GLH_CALL_PREFERANCE", "gft_lead_hdr", "GLH_LEAD_CODE", "$lead_code");
		//waiting for Contact Approval status - project delight
		//###############################################################
	        
	        if($qdata['GLH_MAIN_PRODUCT_UVALIDITITY']!='0000-00-00' and $qdata['GLH_MAIN_PRODUCT_UPDATE_BY']!=='9999' 
	        and $qdata['GLH_MAIN_PRODUCT']!='' and $qdata['GLH_CALL_PREFERANCE']!=''){ //if any one setted the call group manually for that customer
	        	/* set in check list call routed to where */
				$debug_condition="GLH_MAIN_PRODUCT_UVALIDITITY=".$qdata['GLH_MAIN_PRODUCT_UVALIDITITY'];
				$return_cc_details=array(
						'debug_condition'=>$debug_condition,
						'json_call_group'=>$qdata['GLH_CALL_PREFERANCE'],
						'lead_code'=>$lead_code,
						'call_center_div'=>$qdata['GLH_MAIN_PRODUCT']
				);
	        	return $return_cc_details;
	        }else if( ($payable_installation_count!=0) && ($payable_install_valid > 0) ){ //valid alr customer
				
				$debug_condition="GCL_PAYABLE_INSTALLATION_VALIDITY=".$qdata['GCL_PAYABLE_INSTALLATION_VALIDITY'].",GCL_EXTENED_SUPPORT=".$qdata['GCL_EXTENED_SUPPORT'];

			   	if(isset($qdata['hq_installed']) and (int)$qdata['hq_installed']!=0){
			   		$return_cc_details=/*. (mixed[string]) .*/ array(
			   				'debug_condition'=>$debug_condition,
			   				'json_call_group'=>$qdata['hq_GLH_CALL_PREFERANCE'],
			   				'preferred_person'=>array(),
			   				'debug_preference'=>$debug_preference,
			   				'lead_code'=>($qdata['corporate_lead']!='N'?$qdata['corporate_lead']:''),
			   				'call_center_div'=>$qdata['hq_product_group']
			   		);
			   	}else if($qdata['GCL_TRAIINING_STATUS_COMPLETED']=='N'){
			   		$return_cc_details=array(
			   				'debug_condition'=>$debug_condition,
			   				'json_call_group'=>$qdata['GLH_CALL_PREFERANCE'],
			   		        'preferred_person'=>array(),
			   				'debug_preference'=>$debug_preference,
			   				'lead_code'=>$lead_code,
			   				'call_center_div'=>$qdata['GLH_MAIN_PRODUCT']
			   		);
			    }else{
			    	$return_cc_details=array(
			    			'debug_condition'=>$debug_condition,
			    			'json_call_group'=>$qdata['GLH_CALL_PREFERANCE'],
			    	        'preferred_person'=>array(),
			    			'debug_preference'=>$debug_preference,
			    			'lead_code'=>$lead_code,
			    			'call_center_div'=>$qdata['GLH_MAIN_PRODUCT']
			    	);
			    }
				return $return_cc_details;
			}else if( ($payable_installation_count!=0 and $payable_installation_count!=$payable_install_valid) or
			      ($free_installation_count!=0 and $free_installation_count!=$free_installation_validity) ){

				$debug_condition="Annuity,GCL_FREE_INSTALLATION_COUNT=$free_installation_count";

				/* non ASA */
				/* if one lead in ASA other in NON ASA */
				if(isset($qdata['Annuity_lead_codes']) and  $qdata['Annuity_lead_codes']!=null){
					$lead_codes=explode(',',$qdata['Annuity_lead_codes']);
					$lead_code=$lead_codes[0];
				}
				

				$main_product_code=$qdata['GLH_MAIN_PRODUCT'];
				
				if(isset($qdata['Annuity_Main_product']) and  $qdata['Annuity_Main_product']!=''){
					$main_pros=explode(',',$qdata['Annuity_Main_product']);
					$main_product_code=$main_pros[0];
				}
			    $prod_support_gp = get_single_value_from_single_table("glh_main_product", "gft_lead_hdr", "glh_lead_code", $lead_code);
			   	$return_cc_details=array(
					'debug_condition'=>$debug_condition,
					'main_product_code'=>$main_product_code,
				 	'lead_code'=>$lead_code,
			   		'call_center_div'=>'1',
			   	    'installed_product_support_group'=>$prod_support_gp
			   	);
				return $return_cc_details;
			}else{
				$call_pre = ($json_call_group!=""?"$json_call_group":'{"1":"706"}');
				$return_cc_details=array(
										'lead_code'=>$lead_code,
										'json_call_group'=>"$call_pre",
										'call_center_div'=>'17'
								);
								return $return_cc_details;
			}
	}
	return $return_cc_details;
}

/* Not used now. As of 26-Dec-2014, product specific annuity team is merged into single.
 *
 * @param string $main_product_code
 *
 * @return string null if empty.
 

function getAnnuityGroupByMainProductCode($main_product_code){

	//NOTE: Hardcoded few values.

	if ($main_product_code == ''){
		return '703';
	}

	//NOTE: This soluiton is slightly complex.
	//We know only the main_product_code  . But the gft_voicenap_group is based on the FAMILY code (eg. 803-04.0).

	$query_get_gvg_product="SELECT GVG_PRODUCT from gft_voicenap_group where GVG_SUPPORT_GROUP='".$main_product_code."' and GVG_STATUS='A'";
	$result= execute_my_query($query_get_gvg_product);
	//NOTE: This above query may return multiple record. But we are taking only the first record.
	if ($qdata = mysqli_fetch_array($result)){
		$gvg_product_code=$qdata['GVG_PRODUCT'];

		$query2="SELECT GVG_GROUP_ID from gft_voicenap_group where GVG_PRODUCT='".$gvg_product_code."' and GVG_SUPPORT_GROUP='1'";
		$result2= execute_my_query($query2);
		if ($qdata2 = mysqli_fetch_array($result2)){
			return $qdata2['GVG_GROUP_ID'];
		}
	}

	return '703';
}
*/
/**
 * @param string $group_id
 * @param int $type // 1 - get call center group from product group and 2 - get product group from call center group
 * @return string
 */
function get_groups_for_support($group_id,$type) {
    $map1 = array('751'=>'5','752'=>'4','753'=>'3','754'=>'6');
    $map2 = array('3'=>'753','4'=>'752','5'=>'751','6'=>'754','23'=>'753','22'=>'753','21'=>'752','20'=>'751');
    if($type==1) {
        return isset($map2[$group_id])?$map2[$group_id]:'';
    } else {
        return isset($map1[$group_id])?$map1[$group_id]:'';
    }
}
/** 
 * @param string $CustomerCallerID
 * 
 * @return mixed[string]
 */
function get_callcenter_group_details($CustomerCallerID){
	$result_group=/*. (mixed[string]) .*/ array();
	
	$debug_group_details="";
	$temp_CustomerCallerId = substr($CustomerCallerID,-10);
	$emp_result = get_employee_info_from_contact($CustomerCallerID);
	$emply_id = isset($emp_result['GEM_EMP_ID'])?(int)$emp_result['GEM_EMP_ID']:0;
	if($emply_id > 0){/* Employees Group */
		$result_group=array(
			'call_center_div'=>'13',
			'GEM_EMP_ID'=>$emp_result['GEM_EMP_ID'],
			'GEM_EMP_NAME'=>$emp_result['GEM_EMP_NAME'],
			'GEM_LEAD_CODE'=>$emp_result['GEM_LEAD_CODE']
		);
	}else{
		$cp_result=its_active_partner_no($temp_CustomerCallerId);
		if($cp_result['is_cp']==='true'){	/* partner Group */
			$result_group=array(
					'call_center_div'=>'24',
					'lead_code'=>$cp_result['cp_lead_code'],
					'cp_name'=>$cp_result['cp_name'],
					'cp_location'=>$cp_result['cp_location']
			);
		}else if(check_its_exists_in_SAM($CustomerCallerID)) {
			$result_group=get_result_for_inbound_in_group($CustomerCallerID);
		}else{
			$result_group=array('call_center_div'=>'17');
		}		
	}
		
		$result_call_group=array();
		
		$call_center_div_val = isset($result_group['call_center_div'])?(int)$result_group['call_center_div']:0;
		
		if($call_center_div_val==19){/* general*/
			
			$debug_group_details=$debug_group_details." overwrite the group to 700 due to call_center_div is 19 (general)";
			$result_call_group[1]=700;
			$result_group['json_call_group']=json_encode($result_call_group);
		}
		else if($call_center_div_val==13){/* employee */
			$debug_group_details=$debug_group_details." overwrite the group to 702 due to call_center_div is 13 (employee)";
			$result_call_group[1]=702;
			$result_group['json_call_group']=json_encode($result_call_group);			
		}
		else if($call_center_div_val==24){/* CP */
			$debug_group_details=$debug_group_details." overwrite the group to 709 due to call_center_div is 24 (CP)";
			$result_call_group[1]=709;
			$result_group['json_call_group']=json_encode($result_call_group);			
		}
		else if($call_center_div_val==1){/*Non ASA */
			//$result_call_group[1]=703;
			
			$debug_group_details=$debug_group_details." non-asa call due to call_center_div=".(string)$result_group['call_center_div'];
			
			$main_product_code=isset($result_group['main_product_code'])?(string)$result_group['main_product_code']:'';

			//$annuity_group=getAnnuityGroupByMainProductCode($main_product_code);
			//$result_call_group[1]=$annuity_group; //May be 703
			$result_call_group[1]=703;
			if(isset($result_group['installed_product_support_group'])) {
			    $cc_gp = get_groups_for_support($result_group['installed_product_support_group'], 1);
			    $result_call_group[1] = ($cc_gp!=''?(int)$cc_gp:703);
			}
			//NOTE: As of 26-Dec-2014, the Annuity team is merged into a single team. So call forwarding is to single group of 703;

			$result_group['Reason']='Main Product code:'.$main_product_code;
			if(isset($result_group['json_call_group']) and $result_group['json_call_group']!=''){
			}else{
				$result_group['json_call_group']=json_encode($result_call_group);
			}
			
		}
		else if($call_center_div_val != 0){/* Products  */
			
			$debug_group_details=$debug_group_details." product call";
			
			if(isset($result_group['json_call_group']) and $result_group['json_call_group']!=''){
				//$result_call_group=json_decode($result_group['json_call_group'],true);
			}else {
				/* this Case arise if we wont give the support for the product  remove from gft_support_product_group 
				 * so glh_call_preference will be empty in glh_lead_hdr */
				//$result_call_group=explode(',',get_call_groups_for_support_group('1'));
				$result_group['Reason']='No call preference. No support for the product ';
				$result_call_group[1]=706;   //Pre sales group. changed from annuity(703)
				$result_group['json_call_group']=json_encode($result_call_group);
			}			
		}
		
		$result_group['debug_group_details']=$debug_group_details;
		return $result_group;
}
/**
 * @return boolean
 */
function isBusinessHourRouting(){
    
    $check_time=date('H:i');
    $today = getdate();
    $inbound_cache = Cache::getStringArray("inbound");
    if ($inbound_cache === null){
        $is_holiday_y_n=(is_holiday_cache(date('Y-m-d')))?'Y':'N';
        $inbound_cache['support_start_time']=(string)time_to_seconds(get_samee_const_cache('27X7_DAY_START'));
        $inbound_cache['support_end_time']=(string)time_to_seconds(get_samee_const_cache('27X7_DAY_END'));
        $inbound_cache['is_holiday']=$is_holiday_y_n;
        Cache::putStringArray("inbound",$inbound_cache);
    }
    
    $sec_time = time_to_seconds($check_time);
    //$support_start_time = time_to_seconds(get_samee_const_cache('27X7_DAY_START'));
    //$support_end_time = time_to_seconds(get_samee_const_cache('27X7_DAY_END'));
    $support_start_time = (int)$inbound_cache['support_start_time'];
    $support_end_time = (int)$inbound_cache['support_end_time'];
    $is_holiday= ($inbound_cache['is_holiday'] == 'Y')?true:false;
    $shift_timing = (($sec_time>=$support_start_time) and ($sec_time < $support_end_time));
    $business_hrs_routing = false;
    // As requested by Kadhar, commented sunday condition in below if, as the Product wise call routing will be Applicable on Sunday also
    if(!$is_holiday && $shift_timing /*and ((int)$today['wday']!=0 or date('m-d')=='04-01')*/) {
        $business_hrs_routing = true;
    }
    
    return $business_hrs_routing;
}
/**
 *  @param string $callerNumber
 *  @param string $call_from
 *  
 *  @return string 
 */
function send_sms_for_unknown_support_number($callerNumber, $call_from='intellicon'){
    $responseCode = '2';
    $ivr_to_send = 'UNKNOWN_NUMBER_IVR';
    $contact_cond = getContactDtlWhereCondition("GCC_CONTACT_NO", $callerNumber);
    $sql1 = " select GLE_SUPPORT_MODE,GCC_ENABLE_CALL_SUPPORT from gft_lead_hdr_ext ".
        " join gft_customer_contact_dtl on (GCC_LEAD_CODE=GLE_LEAD_CODE) ".
        " where GLE_SUPPORT_MODE=2 and $contact_cond ";
    $res1 = execute_my_query($sql1);
    $notify_template_id = 68;
    $sms_template_id 	= 195;
    if((mysqli_num_rows($res1) > 0) && $row=mysqli_fetch_assoc($res1)){ //aready in voice support
        $responseCode = '5';
        $notify_template_id	= 121;
        $sms_template_id 	= 218;
        if($row['GCC_ENABLE_CALL_SUPPORT']==1){
            $notify_template_id	= 69;
            $sms_template_id 	= 196;
            $ivr_to_send = "GFT_EMPLOYEE_IVR";
            $responseCode = '3';
        }
        
    }
    send_formatted_notification_content(array(), '', $notify_template_id, 2, '','0','0','','','','',$callerNumber);
    $sms_content = get_formatted_content(array(), $sms_template_id);
    entry_sending_sms_to_customer($callerNumber, $sms_content,$sms_template_id,0,0,null,0,null,true);    
    if($call_from=="intellicon"){
        return $ivr_to_send;
    }else{
        return $responseCode;
    }
}

/**
 * @param int[int] $vgroup
 * @param string $preferred_agent_id  List of Prefered agent id
 * @param boolean $mobile_check
 * @param string $ivr_mobile
 * @param boolean $skip_group
 * @param string $lead_code
 * @param string $call_from
 *
 * @return string[string]
 */

function get_agent_list_of_groups($vgroup=array(703),$preferred_agent_id=null,$mobile_check=false,$ivr_mobile='',$skip_group=false,$lead_code='', $call_from='intellicon'){
    $preferred_agent_id_cm='';
    if(is_array($vgroup)){
        $vgroup=implode(',',$vgroup);
    }
    if(!empty($preferred_agent_id) and is_array($preferred_agent_id)){
        $preferred_agent_id_cm=implode(',',$preferred_agent_id);
    }
    $login_type_con = ($call_from!='intellicon'?" AND GVA_LOGIN_TYPE=1":"");
    $debug_agent_details='';
    $agent_list_arr = array();
    $online_agents_arr = array();
    if($preferred_agent_id_cm!=''){
        $query=" SELECT GLM_EMP_ID,GLM_LOGIN_NAME,GVS_CURRENT_STATUS,GEM_MOBILE,GMA_FORWARD_TO_MOBILE,GEM_CALL_MOBILE,GAS_AGENT_STATUS,GVS_REMARKS ".
            " FROM gft_login_master ".
            " left join gft_voicesnap_online_user vo on (GVS_EMP_ID=GLM_EMP_ID $login_type_con) ". //for forward to mobile , no need to check login status of agent
            " join gft_emp_master em on (GEM_EMP_ID=GLM_EMP_ID and GEM_STATUS='A') ".
            " left join gft_msn_agent_master on (GEM_EMP_ID = GMA_AGENT_ID) ".
            " left join gft_agent_status_master m1 on (m1.GAS_ID = vo.GVS_CURRENT_STATUS) ".
            " where GEM_EMP_ID in ($preferred_agent_id_cm) group by GEM_EMP_ID,GLM_LOGIN_NAME,GVS_CURRENT_STATUS "; //TODO:Redefine the logic to remove group by
        $result=execute_my_query($query);
        $pagent_list=/*. (string[int]) .*/ array();
        if(mysqli_num_rows($result)>0){
            while($qdata=mysqli_fetch_array($result)){
                $pempid=(int)$qdata['GLM_EMP_ID'];
                $mob_fwd1 = $qdata['GMA_FORWARD_TO_MOBILE'];
                $mob_fwd2 = $qdata['GEM_CALL_MOBILE'];
                $pagent_list[$pempid]=$qdata['GLM_LOGIN_NAME'];
                $online_status = $qdata['GVS_CURRENT_STATUS'];
                $print_debug = true;
                if($mobile_check && ($mob_fwd1=='Y' && $mob_fwd1==$mob_fwd2) ){
                    $agent_list_arr[] = array('type'=>'Mobile','id'=>$qdata['GEM_MOBILE'],'agent_no'=>$qdata['GEM_MOBILE'],'agent_emp_id'=>$pempid);
                }elseif(in_array($online_status,array('4','1'))) {
                    $agent_list_arr[] = array('type'=>'Agent','id'=>$qdata['GLM_LOGIN_NAME'],'agent_no'=>$qdata['GEM_MOBILE'],'agent_emp_id'=>$pempid);
                    $print_debug = false;
                }elseif( $mobile_check && ($mob_fwd1=='Y') && ($online_status!='') ){
                    $online_agents_arr[] = array('type'=>'Mobile','id'=>$qdata['GEM_MOBILE'],'agent_no'=>$qdata['GEM_MOBILE'],'agent_emp_id'=>$pempid);
                }
                if($print_debug && ($online_status != '')){
                    $debug_agent_details.=", ".$qdata['GLM_LOGIN_NAME'] . " - " . $qdata['GAS_AGENT_STATUS']." - ".$qdata['GVS_REMARKS'];
                }
            }
        }
    }
    $add_cond = ($skip_group)?" or GVS_CURRENT_STATUS in (4,1) ":"";
    if($vgroup!=0){
        $vgps = explode(",",$vgroup);
        $joins = "";
        $order_by = 'GVG_PREFER_ORDER asc,';
        $proceed_fetch = true;
        if(isset($vgps[0]) and in_array($vgps[0],array('751','752','753','754'))) { // ALR Expired product group wise
            $state_code = '';
            $main_prod = '';
            $prod_group = get_groups_for_support($vgps[0],2);
            if(isBusinessHourRouting() and (int)date('w')>0) {
                $joins = " left join gft_cst_agent_mapping on (gca_emp_id=em.gem_emp_id) ";
                // 	            if((int)date('w')<6) {
                // 	                $prod_gp = ($prod_group!=''?$prod_group:'1');
                // 	                $joins = " join gft_cst_agent_mapping on (gca_emp_id=em.gem_emp_id and gca_support_group_id='$prod_gp') ";
                // 	            } else {
                $vgroup = "751,752,753,754"; // In case of saturday, it can be mapped to any cst member if agents in same group are unavailable
                $order_by .= "if(FIND_IN_SET('$prod_group',group_concat(gca_support_group_id)),1,0) desc,";
                // 	            }
                } else {
                    $proceed_fetch = false;
                    $debug_agent_details = "Sending ALR expired customer to VM since non working hours";
                }
                $state_code_qry = execute_my_query(" select gpm_map_id from gft_lead_hdr ".
                    " join gft_political_map_master on (gpm_map_name=glh_cust_statecode and gpm_map_type='S' ".
                    " and gpm_map_status='A') where glh_lead_code='$lead_code' ");
                if($row = mysqli_fetch_assoc($state_code_qry)) {
                    $state_code = $row['gpm_map_id'];
                    $cst_map_check = "select gca_emp_id from gft_cst_agent_mapping where gca_support_group_id='$prod_group' and gca_state_id='$state_code'";
                    $cst_map_res = execute_my_query($cst_map_check);
                    if(mysqli_num_rows($cst_map_res)==0) {
                        $state_code = 0;
                    }
                    $order_by .= "if(FIND_IN_SET('$state_code',group_concat(gca_state_id)),1,0) desc,";
                }
        }
        if($proceed_fetch) {
            $query =" SELECT GLM_EMP_ID,GLM_LOGIN_NAME,GVG_PREFER_ORDER,GEM_MOBILE,GMA_FORWARD_TO_MOBILE, ".
                " GEM_CALL_MOBILE, GVS_CURRENT_STATUS,GAS_AGENT_STATUS, GVS_REMARKS ".
                " FROM gft_voicenap_group ".
                " join gft_voicenap_group_emp_dtl on(GVG_GROUP_ID=GVGED_GROUP_ID) ".
                " left join gft_login_master lm on (GLM_EMP_ID=GVGED_EMPLOYEE  ) ".
                " left join gft_voicesnap_online_user vo on (GVS_EMP_ID=GLM_EMP_ID ) ".
                " left join gft_agent_status_master m1 on (m1.GAS_ID = vo.GVS_CURRENT_STATUS) ".
                " join gft_emp_master em on (GEM_EMP_ID=GLM_EMP_ID and GEM_STATUS='A') $joins ".
                " left join gft_msn_agent_master on (GVG_GROUP_ID = GMA_GROUP_ID) ".
                " where (GVG_GROUP_ID in ($vgroup) $add_cond)  $login_type_con".
                ($preferred_agent_id_cm!=''?" and glm_emp_id not in ($preferred_agent_id_cm) ":"").
                " group by GLM_EMP_ID order by $order_by GVA_STATUS_UPDATED_ON asc ";
                $result=execute_my_query($query);
                $record_count =1;
                while($qdata=mysqli_fetch_array($result)){
                    $mob_fwd1 = $qdata['GMA_FORWARD_TO_MOBILE'];
                    $mob_fwd2 = $qdata['GEM_CALL_MOBILE'];
                    $online_status = $qdata['GVS_CURRENT_STATUS'];
                    $emp_id = $qdata['GLM_EMP_ID'];
                    $print_debug = true;
                    $is_mobile_forward_route = false;
                    if($mobile_check && ($mob_fwd1=='Y' && $mob_fwd1==$mob_fwd2) ){
                        $agent_list_arr[] = array('type'=>'Mobile','id'=>$qdata['GEM_MOBILE'],'agent_no'=>$qdata['GEM_MOBILE'],'agent_emp_id'=>$emp_id);
                        $is_mobile_forward_route = true;
                    }elseif(in_array($online_status,array('4','1'))){
                        $agent_list_arr[] = array('type'=>'Agent','id'=>$qdata['GLM_LOGIN_NAME'],'agent_no'=>$qdata['GEM_MOBILE'],'agent_emp_id'=>$emp_id);
                        $print_debug = false;
                    }elseif( $mobile_check && ($mob_fwd1=='Y') && ($online_status!='') ){
                        $online_agents_arr[] = array('type'=>'Mobile','id'=>$qdata['GEM_MOBILE'],'agent_no'=>$qdata['GEM_MOBILE'],'agent_emp_id'=>$emp_id);
                        $is_mobile_forward_route = true;
                    }else if($mob_fwd2=='Y'){
                        $online_agents_arr[] = array('type'=>'Mobile','id'=>$qdata['GEM_MOBILE'],'agent_no'=>$qdata['GEM_MOBILE'],'agent_emp_id'=>$emp_id);
                        $is_mobile_forward_route = true;
                    }
                    //to update agent status update date and time when mobile number forwarding
                    if(($is_mobile_forward_route) && $record_count==1){
                        execute_my_query("UPDATE gft_voicesnap_online_user set GVA_STATUS_UPDATED_ON=now() where GVS_EMP_ID='$emp_id'");
                        $record_count++;
                    }
                    if($print_debug && ($online_status != '')){
                        $debug_agent_details.=", ".$qdata['GLM_LOGIN_NAME'] . " - " . $qdata['GAS_AGENT_STATUS']." - ".$qdata['GVS_REMARKS'];
                    }
                }
        }
    }
    if(count($agent_list_arr)==0){
        if($mobile_check) {
            $agent_list_arr = $online_agents_arr;
        }
        if($ivr_mobile!=''){
            $agent_list_arr[] =array('type'=>'Mobile','id'=>$ivr_mobile,'agent_no'=>$ivr_mobile,'agent_emp_id'=>0);
        }
    }
    $agent_list = "";
    $agent_emp_id = 0;
    foreach ($agent_list_arr as $key=>$agent_dtl){
        $type   = trim($agent_dtl['type']);
        $id     = trim($agent_dtl['id']);
        $agent_no = trim($agent_dtl['agent_no']);
        $agent_id = trim($agent_dtl['agent_emp_id']);
        if($call_from=="intellicon"){
            $agent_list .="<Route type='$type' id='$id'/>";
        }else{
            //$agent_list .=($agent_list!=""?",":"")."$agent_no";
            $agent_list ="$agent_no";// Return only one number.
            $agent_emp_id = $agent_id;
            break;
        }
    }
    $return_agent_list      = array();
    $return_agent_list[0]   = $agent_list;
    $return_agent_list[1]   = "<debug_agent_details>".$debug_agent_details."</debug_agent_details>";
    $return_agent_list[2]   = $agent_emp_id;
    return $return_agent_list;
}
/**
 * @param int[int] $vgroup
 * @param string $preferred_agent_id  List of Prefered agent id 
 * @param boolean $mobile_check
 * @param string $ivr_mobile
 * @param boolean $skip_group
 * @param string $lead_code
 * 
 * @return string 
 */

function get_agent_list_of_group_in_xml_new($vgroup=array(703),$preferred_agent_id=null,$mobile_check=false,$ivr_mobile='',$skip_group=false,$lead_code=''){
	$preferred_agent_id_cm='';
	if(is_array($vgroup)){
		$vgroup=implode(',',$vgroup);
	}
	if(!empty($preferred_agent_id) and is_array($preferred_agent_id)){
		$preferred_agent_id_cm=implode(',',$preferred_agent_id);
	}
	$agent_list='';
	$online_agents='';
	$debug_agent_details='';

	if($preferred_agent_id_cm!=''){
		$query=" SELECT GLM_EMP_ID,GLM_LOGIN_NAME,GVS_CURRENT_STATUS,GEM_MOBILE,GMA_FORWARD_TO_MOBILE,GEM_CALL_MOBILE,GAS_AGENT_STATUS,GVS_REMARKS ".
		" FROM gft_login_master ". 
		" left join gft_voicesnap_online_user vo on (GVS_EMP_ID=GLM_EMP_ID ) ". //for forward to mobile , no need to check login status of agent
		" join gft_emp_master em on (GEM_EMP_ID=GLM_EMP_ID and GEM_STATUS='A') ".
		" left join gft_msn_agent_master on (GEM_EMP_ID = GMA_AGENT_ID) ".
		" left join gft_agent_status_master m1 on (m1.GAS_ID = vo.GVS_CURRENT_STATUS) ".
		" where GEM_EMP_ID in ($preferred_agent_id_cm) group by GEM_EMP_ID,GLM_LOGIN_NAME,GVS_CURRENT_STATUS "; //TODO:Redefine the logic to remove group by
		$result=execute_my_query($query);
		$pagent_list=/*. (string[int]) .*/ array();
		if(mysqli_num_rows($result)>0){
			while($qdata=mysqli_fetch_array($result)){
				$pempid=(int)$qdata['GLM_EMP_ID'];
				$mob_fwd1 = $qdata['GMA_FORWARD_TO_MOBILE'];
				$mob_fwd2 = $qdata['GEM_CALL_MOBILE'];
				$pagent_list[$pempid]=$qdata['GLM_LOGIN_NAME'];
				$online_status = $qdata['GVS_CURRENT_STATUS'];
				$print_debug = true;
				if($mobile_check && ($mob_fwd1=='Y' && $mob_fwd1==$mob_fwd2) ){
					$agent_list.="<Route type='Mobile' id='".$qdata['GEM_MOBILE']."'/>";
				}elseif(in_array($online_status,array('4','1'))) { 
					$agent_list.="<Route type='Agent' id='".$qdata['GLM_LOGIN_NAME']."'/>";
					$print_debug = false;
				}elseif( $mobile_check && ($mob_fwd1=='Y') && ($online_status!='') ){
					$online_agents.="<Route type='Mobile' id='".$qdata['GEM_MOBILE']."'/>";
				}
				if($print_debug && ($online_status != '')){
					$debug_agent_details.=", ".$qdata['GLM_LOGIN_NAME'] . " - " . $qdata['GAS_AGENT_STATUS']." - ".$qdata['GVS_REMARKS'];
				}
			}
		}
	}
	$add_cond = ($skip_group)?" or GVS_CURRENT_STATUS in (4,1) ":"";
	if($vgroup!=0){
	    $vgps = explode(",",$vgroup);
	    $joins = "";
	    $order_by = 'GVG_PREFER_ORDER asc,';
	    $proceed_fetch = true;
	    if(isset($vgps[0]) and in_array($vgps[0],array('751','752','753','754'))) { // ALR Expired product group wise
	        $state_code = '';
	        $main_prod = '';
	        $prod_group = get_groups_for_support($vgps[0],2);
	        if(isBusinessHourRouting() and (int)date('w')>0) {
	            $joins = " left join gft_cst_agent_mapping on (gca_emp_id=em.gem_emp_id) ";
// 	            if((int)date('w')<6) {
// 	                $prod_gp = ($prod_group!=''?$prod_group:'1');
// 	                $joins = " join gft_cst_agent_mapping on (gca_emp_id=em.gem_emp_id and gca_support_group_id='$prod_gp') ";
// 	            } else {
	                $vgroup = "751,752,753,754"; // In case of saturday, it can be mapped to any cst member if agents in same group are unavailable
	                $order_by .= "if(FIND_IN_SET('$prod_group',group_concat(gca_support_group_id)),1,0) desc,";
// 	            }
	        } else {
	            $proceed_fetch = false;
	            $debug_agent_details = "Sending ALR expired customer to VM since non working hours";
	        }
	        $state_code_qry = execute_my_query(" select gpm_map_id from gft_lead_hdr ".
	                          " join gft_political_map_master on (gpm_map_name=glh_cust_statecode and gpm_map_type='S' ".
	                          " and gpm_map_status='A') where glh_lead_code='$lead_code' ");
	        if($row = mysqli_fetch_assoc($state_code_qry)) {
	            $state_code = $row['gpm_map_id'];
	            $cst_map_check = "select gca_emp_id from gft_cst_agent_mapping where gca_support_group_id='$prod_group' and gca_state_id='$state_code'";
	            $cst_map_res = execute_my_query($cst_map_check);
	            if(mysqli_num_rows($cst_map_res)==0) {
	                $state_code = 0;
	            }
	            $order_by .= "if(FIND_IN_SET('$state_code',group_concat(gca_state_id)),1,0) desc,";
	        }
	    }
	    if($proceed_fetch) {
    		$query =" SELECT GLM_EMP_ID,GLM_LOGIN_NAME,GVG_PREFER_ORDER,GEM_MOBILE,GMA_FORWARD_TO_MOBILE, ".
                    " GEM_CALL_MOBILE, GVS_CURRENT_STATUS,GAS_AGENT_STATUS, GVS_REMARKS ".
    				" FROM gft_voicenap_group ".
    				" join gft_voicenap_group_emp_dtl on(GVG_GROUP_ID=GVGED_GROUP_ID) ".
    				" left join gft_login_master lm on (GLM_EMP_ID=GVGED_EMPLOYEE  ) ".
    				" left join gft_voicesnap_online_user vo on (GVS_EMP_ID=GLM_EMP_ID ) ".				
    				" left join gft_agent_status_master m1 on (m1.GAS_ID = vo.GVS_CURRENT_STATUS) ".
    				" join gft_emp_master em on (GEM_EMP_ID=GLM_EMP_ID and GEM_STATUS='A') $joins ".
    				" left join gft_msn_agent_master on (GVG_GROUP_ID = GMA_GROUP_ID) ".
    				" where (GVG_GROUP_ID in ($vgroup) $add_cond) ".
    				($preferred_agent_id_cm!=''?" and glm_emp_id not in ($preferred_agent_id_cm) ":"").
    				" group by GLM_EMP_ID order by $order_by GVA_STATUS_UPDATED_ON asc ";
    		$result=execute_my_query($query);
    		$record_count =1;
    		while($qdata=mysqli_fetch_array($result)){
    			$mob_fwd1 = $qdata['GMA_FORWARD_TO_MOBILE'];
    			$mob_fwd2 = $qdata['GEM_CALL_MOBILE'];
    			$online_status = $qdata['GVS_CURRENT_STATUS'];
    			$emp_id = $qdata['GLM_EMP_ID'];   
    			$print_debug = true;
    			$is_mobile_forward_route = false;
    			if($mobile_check && ($mob_fwd1=='Y' && $mob_fwd1==$mob_fwd2) ){
    				$agent_list.="<Route type='Mobile' id='".$qdata['GEM_MOBILE']."'/>";
    				$is_mobile_forward_route = true;
    			}elseif(in_array($online_status,array('4','1'))){
    				$agent_list.="<Route type='Agent' id='".$qdata['GLM_LOGIN_NAME']."'/>";
    				$print_debug = false;
    			}elseif( $mobile_check && ($mob_fwd1=='Y') && ($online_status!='') ){
    				$online_agents.="<Route type='Mobile' id='".$qdata['GEM_MOBILE']."'/>";
    				$is_mobile_forward_route = true;
    			}else if($mob_fwd2=='Y'){
    			    $online_agents.="<Route type='Mobile' id='".$qdata['GEM_MOBILE']."'/>";
    			    $is_mobile_forward_route = true;
    			}
    			//to update agent status update date and time when mobile number forwarding
    			if(($is_mobile_forward_route) && $record_count==1){  
    			    execute_my_query("UPDATE gft_voicesnap_online_user set GVA_STATUS_UPDATED_ON=now() where GVS_EMP_ID='$emp_id'");
    			    $record_count++;
    			}
    			if($print_debug && ($online_status != '')){
    				$debug_agent_details.=", ".$qdata['GLM_LOGIN_NAME'] . " - " . $qdata['GAS_AGENT_STATUS']." - ".$qdata['GVS_REMARKS'];
    			}
    		}
	    }
	}
	if($agent_list==''){
		if($mobile_check) {
			$agent_list.=$online_agents;
		}
		if($ivr_mobile!=''){
			$agent_list.="<Route type='Mobile' id='".$ivr_mobile."'/>";
		}
	}

	$agent_list.="<debug_agent_details>".$debug_agent_details."</debug_agent_details>";
	return $agent_list;	
}


/* NOT USED 
 * @param int $emp_id
 * @param int $main_product
 * 
 * @return boolean
function check_support_product_group_new($emp_id,$main_product){
	$query="SELECT group_concat(distinct GVG_SUPPORT_GROUP) support_group " .
		" FROM gft_voicenap_group " .
		" join gft_voicenap_group_emp_dtl on(GVG_GROUP_ID=GVGED_GROUP_ID) " .
		" where GVGED_EMPLOYEE=$emp_id and GVG_PRODUCT!=0 and GVG_SUPPORT_GROUP=$main_product ";
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)==1){
		return true;
	}
	return false;
}
*/

/**
 * @param int $sms_id
 * @param string $CustomerCallerID
 * @param string $lead_code
 * @param string $groups
 * @param string $employe
 * 
 * @return void
 */
function send_escalation_sms($sms_id,$CustomerCallerID,$lead_code='',$groups='',$employe='' ) {
	$emp_name = $group_name ='';
	$mobile_no_arr = /*. (string[int]) .*/ array();
	
	$sel_que = " select GEM_EMP_NAME from gft_emp_master where GEM_STATUS='A' and ".
			   " (GEM_MOBILE like '%$CustomerCallerID' or GEM_RELIANCE_NO like '%$CustomerCallerID') ";
	if($row = mysqli_fetch_array(execute_my_query($sel_que))){
		$emp_name = $row['GEM_EMP_NAME']; //called Employee name
	}
	
	if($groups!='') {
		$comm_que = " select em.GEM_MOBILE, em.GEM_EMAIL, spg.GSP_GROUP_NAME ".
				" from gft_voicenap_group_emp_dtl vg ".
				" join gft_voicenap_group on (GVG_GROUP_ID = vg.GVGED_GROUP_ID) ".  //minor to major support group
				" join gft_support_product_group spg on (spg.GSP_GROUP_ID = GVG_SUPPORT_GROUP) ". //major support group name
				" join gft_emp_master em on (vg.GVGED_EMPLOYEE=em.GEM_EMP_ID) ".
				" where vg.GVGED_GROUP_ID in ($groups) and em.GEM_STATUS='A' ";
		$comm_res = execute_my_query($comm_que) ;
		while( ($groups!='0') && ($data = mysqli_fetch_array($comm_res)) ) {
			$group_name = $data['GSP_GROUP_NAME'];
			$mobile_no_arr[] = substr($data['GEM_MOBILE'],-10);
		}
	}
	
	if($employe!='') {
		$emp_que = " select GEM_MOBILE from gft_emp_master where GEM_EMP_ID in ($employe) and GEM_STATUS='A' ";
		$emp_res = execute_my_query($emp_que) ;
		while($data = mysqli_fetch_array($emp_res)) {
			$mobile_no_arr[] = substr($data['GEM_MOBILE'],-10);
		}
	}
	
	$sms_content_temp= array(
			'Caller_No'=>array($CustomerCallerID),
			'Employee_Name'=>array($emp_name),
			'Group'=>array($group_name),
			'Customer_ID'=>array($lead_code)
	);
	$sms_content = htmlentities(get_formatted_content($sms_content_temp, $sms_id));
	$mobile_no_arr = array_unique($mobile_no_arr);
	foreach ($mobile_no_arr as $mobile) {
		entry_sending_sms($mobile, $sms_content, $sms_id);
	}
}

/**
 * @param string $username
 * @param string $userpassword
 *
 * @return int
 */
function callcenter_auth_user($username,$userpassword){
	$crypwd=crypt($userpassword,'sales');
	$crypwd_md5=md5($userpassword);
	$query="select GLM_EMP_ID, GLM_LOGIN_NAME, GLM_PASSWORD, GLM_CREATED_DATE, GLM_UPDATED_DATE, GLM_LAST_LOGIN_DATE " .
		" from gft_login_master  " ;
	$query.=" , gft_emp_master em ";
	$query.=" where GLM_LOGIN_NAME='".mysqli_real_escape_string_wrapper($username)."' ";
	$query.=" and ( GLM_PASSWORD='".mysqli_real_escape_string_wrapper($crypwd)."' or GLM_PASSWORD='".mysqli_real_escape_string_wrapper($crypwd_md5)."' )  ";
	$query.=" and glm_emp_id=gem_emp_id and gem_status='A' ";
	$res=execute_my_query($query);
	if($query_data=mysqli_fetch_array($res)){
		return (int)$query_data['GLM_EMP_ID'];
	}
	return 0;
}//end of function

/**
 * @param mixed[string] $updatearr
 * @param string $table_name
 * @param mixed[string] $table_key_arr
 * @param mixed[string] $extra_field_to_update
 * @param string $user_id
 * @param string $remarks
 * @param mixed[string] $table_column_iff_update
 * @param mixed[string] $insert_new_row
 * 
 * @return boolean
 */
function cc_array_update_tables_common($updatearr,$table_name,$table_key_arr,$extra_field_to_update,$user_id,$remarks=null,
$table_column_iff_update=null,$insert_new_row=null){

	$me=$_SERVER['PHP_SELF'];

	$result_old=/*. (resource) .*/ NULL;

	$up_query = /*. (string[int]) .*/array();
	$audit_q = /*. (string[int]) .*/array();
	//$call_function_later=0;
	//$lead_code='';
	//$lead_status='';
	
	if(count($updatearr)>0){
		$i=0;
		$select_name="";
		foreach($updatearr  as $key => $value){
			$select_name.=($i!=0?', ':"").$key;
			$i++;
		}
		$i=0;
		$where_name='';
		if(isset($table_key_arr) && $table_key_arr!=null){
			foreach($table_key_arr  as $key => $value){
				$where_name.=($i!=0?' and ':"").$key."='".(string)$value."' ";
				$i++;
			}
		}
		if($where_name=='' && $insert_new_row==null){ 
			//show_my_alert_msg("Sorry No Key field found to update data ");
			return false;
		}
		if($table_name==''){
			show_my_alert_msg("Sorry No table name mentioned to update data ");
			return false;
		}
		$num_rows_to_updated=0;
		if($where_name!==''){
		$select_q="Select $select_name from $table_name where $where_name ";
		$result_old=execute_my_query($select_q); 
		$num_rows_to_updated=mysqli_num_rows($result_old);
		}
		$j=0;
		
		if($insert_new_row!=null and $num_rows_to_updated==0){
			$column_name=$column_value='';
			$lr=0;
			foreach($insert_new_row as $key => $value){
				$column_name.=($lr!=0?",":"")."$key";
				$column_value.=($lr!=0?",":"")."'$value'";
				$lr++;
			}
			$insert_query="insert ignore into $table_name ($column_name) value ($column_value)";
			execute_my_query($insert_query);
			return true;
		}
		if($where_name==''){ 
			return false; 
		}

		while($data_old=mysqli_fetch_array($result_old)){
			$i=0;
			foreach($updatearr  as $key => $value){
				if($data_old[$key]!=$value){
					//$call_function_later=0;
					if(in_array(mysqli_field_type_wrapper($result_old,$i), array('249','250','251','252','253','254'))){
						$audit_q[]="('','$table_name','$key','".mysqli_real_escape_string_wrapper(trim(isset($data_old[$key])?$data_old[$key]:''))."'," .
								"'".mysqli_real_escape_string_wrapper(trim(isset($value)?(string)$value:''))."','$me',now(),'$user_id'," .
								"'".mysqli_real_escape_string_wrapper(trim($where_name))."','$remarks')";
						$up_query[]= " $key = '".mysqli_real_escape_string_wrapper(trim(isset($value)?(string)$value:''))."' ";
					}else{
						$audit_q[]="('','$table_name','$key','".$data_old[$key]."','$value','$me',now(),'$user_id'," .
								"'".mysqli_real_escape_string_wrapper(trim($where_name))."','$remarks')";
						$up_query[]= " $key = '$value' ";
					}
					$j++;
				}
				$i++;
			}
			if($extra_field_to_update!=''){
				foreach($extra_field_to_update  as $key1 => $value1){
					$up_query[]= " $key1 = '$value1' ";
				}	
			}
		}
		if($j>0){
			if($table_column_iff_update!=''){
				foreach($table_column_iff_update as $key1 => $value1){
					$up_query[]= " $key1 = '$value1' ";
				}	
			}
			$query="update $table_name set ".implode(',',$up_query). " where $where_name ";
			$result_edit=execute_my_query($query);
			//if(mysqli_affected_rows_wrapper()>0){
			//	$query_audit_col="insert into gft_audit_log_edit_table(
			//					GUL_AUDIT_ID ,GUL_AUDIT_TABLE ,GUL_AUDIT_COLUMNS,GUL_AUDIT_OLD_VALUES,
			//					GUL_AUDIT_NEW_VALUES,GUL_PAGE ,GUL_TIME ,GUL_USER_ID,GUL_AUDIT_KEY_FIELDS," .
			//					"GUL_REMARKS)values ".implode(',',$audit_q);
			//	$resultaudit=execute_my_query($query_audit_col);
			//}
			//if($call_function_later=="1"){
				//send_sms_regarding_lead_status_change($lead_status,$lead_code,$user_id);
			//}
			if($result_edit){
				return true;				
			}
		}
	}
	return false;
}

/**
 * @param mixed[string] $updatearr
 * @param string $table_name
 * @param mixed[string] $table_key_arr
 * @param mixed[string] $insert_new_row
 * 
 * @return boolean
 */
function cc_array_insert_tables_common($updatearr,$table_name,$table_key_arr,$insert_new_row=null){
	//$me=$_SERVER['PHP_SELF'];

	//$result_old=/*. (resource) .*/ NULL;

	//$up_query = /*. (string[int]) .*/array();
	//$audit_q = /*. (string[int]) .*/array();
	//$call_function_later=0;
	//$lead_code='';
	//$lead_status='';
	
	if(count($updatearr)>0){
		$i=0;
		$select_name="";
		foreach($updatearr  as $key => $value){
			$select_name.=($i!=0?', ':"").$key;
			$i++;
		}
		$i=0;
		$where_name='';
		if(isset($table_key_arr) && $table_key_arr!=null){
			foreach($table_key_arr  as $key => $value){
				$where_name.=($i!=0?' and ':"").$key."='". (string)$value."' ";
				$i++;
			}
		}
		if($where_name=='' && $insert_new_row==null){ 
			//show_my_alert_msg("Sorry No Key field found to update data ");
			return false;
		}
		if($table_name==''){
			show_my_alert_msg("Sorry No table name mentioned to update data ");
			return false;
		}
		$num_rows_to_updated=0;
		//if($where_name!==''){
		//$select_q="Select $select_name from $table_name where $where_name ";
		//$result_old=execute_my_query($select_q); 
		//$num_rows_to_updated=mysqli_num_rows($result_old);
		//}
		//$j=0;
		
		if($insert_new_row!=null and $num_rows_to_updated==0){
			$column_name=$column_value='';
			$lr=0;
			foreach($insert_new_row as $key => $value){
				$column_name.=($lr!=0?",":"")."$key";
				$column_value.=($lr!=0?",":"")."'$value'";
				$lr++;
			}
			$insert_query="insert ignore into $table_name ($column_name) value ($column_value)";
			execute_my_query($insert_query);
			return true;
		}
		if($where_name==''){ 
			return false; 
		}

	}
	return false;
}


/**
 * @param int $lead_code
 * @param int $productcode
 * @param string $productskew
 * @param string $version
 * @param string $product_type
 * @param string $emp_id
 * @param string $reason_visit
 * @param string $summary
 * @param string $GCH_COMPLAINT_CODE
 * @param string $GCD_STATUS
 * @param string $GCD_SCHEDULE_DATE
 * @param string $GCD_EXTRA_CHARGES
 * @param string $GCD_PROCESS_EMP
 * @param string $GCD_NATURE
 * @param string $gs
 * @param string $problemdesc
 * @param boolean $send_sms
 * @param string $trans_id
 * @param string $GCD_REMARKS
 * @param string $GCD_COMPLAINT_ID
 * 
 * @return string
 */
function cc_insert_support_entry($lead_code,$productcode,$productskew,$version,$product_type,$emp_id,$reason_visit,
				$summary,$GCH_COMPLAINT_CODE,$GCD_STATUS,$GCD_SCHEDULE_DATE=null,$GCD_EXTRA_CHARGES=null,
				$GCD_PROCESS_EMP=null,$GCD_NATURE=null,$gs='S4',$problemdesc=null,$send_sms=false,$trans_id='',
				$GCD_REMARKS=null,$GCD_COMPLAINT_ID=null){
    $GCD_ACTIVITY_DATETIME=date('Y-m-d H:i:s');
    if($GCD_NATURE==null){    $GCD_NATURE='8';}    
    $GCD_CONTACT_TYPE='1';
    $GCH_CALL_TYPE=1;
    if($GCD_STATUS=='T6'){$GCH_CALL_TYPE=2;}else if($GCD_STATUS=='T23'){$GCH_CALL_TYPE=6;}
    $GCD_PROBLEM_SUMMARY=$summary;
 	$productskew=substr($productskew,0,4);
 	$GCD_COMPLAINT_ID='';$ACT_ID='';$authority_name='';$gce='';$contact_pno='';$GCD_EMAIL='';
 	$GCD_ESTIMATED_TIME='';$gpm='';$gpd='';
	//$uplf='';
	//$grema='';
	$visit_no='';$GCD_VISIT_TIMEOUT='';

 	//$vm_im_complaint_level = 0;
	if(empty($GCD_COMPLAINT_ID) ){	
		if( in_array($GCH_COMPLAINT_CODE, array(138,150)) ){   //voice mail and missed call
			$send_sms = false;  //special sms will be sent.
			$query_find_unclosed_vm=" select GCH_LEAD_CODE,MAX(GCH_COMPLAINT_ID) as max_GCH_COMPLAINT_ID,GCD_STATUS from gft_customer_support_hdr " .
						" join gft_customer_support_dtl on (GCD_COMPLAINT_ID=GCH_COMPLAINT_ID and GCD_ACTIVITY_ID=GCH_LAST_ACTIVITY_ID and " .
						" GCD_STATUS='$GCD_STATUS' AND GCH_COMPLAINT_CODE=$GCH_COMPLAINT_CODE AND GCD_TO_DO=24 and GCD_NATURE=2 ) " .
						" where GCH_LEAD_CODE='$lead_code'  GROUP BY  GCH_LEAD_CODE  having !isnull(max_GCH_COMPLAINT_ID) ";
			$result_find_unclosed_vm=execute_my_query($query_find_unclosed_vm);
			$GCD_COMPLAINT_ID=null;
			if($result_find_unclosed_vm){
					if(mysqli_num_rows($result_find_unclosed_vm)==1){
						$qdc=mysqli_fetch_array($result_find_unclosed_vm);
						$GCD_COMPLAINT_ID=$qdc['max_GCH_COMPLAINT_ID'];
						$GCD_STATUS=$qdc['GCD_STATUS'];
					}
			}
		}	
	}
	$install_id_arr=/*.(string[int]).*/get_install_id("$lead_code",/*$order_no=*/'',"$productcode",$productskew,/*$fulfilment_no=*/'');
	if(count($install_id_arr)==1 and $install_id_arr!=null){
		$IS_ASA_CUST=is_asa_cust($install_id_arr[0]);
	}else {
		$IS_ASA_CUST='N';
	}
	if($GCD_COMPLAINT_ID=='' and $productcode!=0 and $productcode!='' and $productskew!='' ){
		$restore_time_ar=get_complaint(null,$GCH_COMPLAINT_CODE);
		$restore_time=($restore_time_ar[0][3]==''?1:(int)$restore_time_ar[0][3]);
		$restore_time=($GCD_STATUS=='T3'?(int)$restore_time_ar[0][4]:$restore_time);
		$schedule_to_support=$GCD_SCHEDULE_DATE;
		if($GCD_SCHEDULE_DATE!=null and $GCD_SCHEDULE_DATE!='0000-00-00 00:00:00'){
			$restore_time=24;
			$schedule_to_support=$GCD_SCHEDULE_DATE;
		}
		$query_up= "insert into gft_customer_support_hdr (GCH_COMPLAINT_ID, GCH_LAST_ACTIVITY_ID, GCH_COMPLAINT_DATE, " .
			" GCH_LEAD_CODE,  GCH_COMPLAINT_CODE, GCH_PRODUCT_CODE,GCH_PRODUCT_SKEW,GCH_VERSION,GCH_PRODUCT_TYPE," .
			" GCH_CURRENT_STATUS,GCH_REPORTED_TIME,GCH_RESTORE_TIME,GCH_ASS_CUST,GCH_CALL_TYPE) values" .
			" ('$GCD_COMPLAINT_ID','$ACT_ID','$GCD_ACTIVITY_DATETIME'," .
			" '$lead_code','$GCH_COMPLAINT_CODE','$productcode','$productskew','$version','$product_type'," .
			" '$GCD_STATUS','$GCD_ACTIVITY_DATETIME',DATE_ADD('$schedule_to_support', INTERVAL $restore_time HOUR),'$IS_ASA_CUST',$GCH_CALL_TYPE) ";
		execute_my_query($query_up) ;
   		$GCD_COMPLAINT_ID="".mysqli_insert_id_wrapper();
	}		
	
	if($GCD_COMPLAINT_ID!=''){
	/*File Uploaded end here here */
		$query_up2=" insert into gft_customer_support_dtl " .
				" (GCD_COMPLAINT_ID, GCD_ACTIVITY_ID, GCD_ACTIVITY_DATE, GCD_EMPLOYEE_ID, " .
				" GCD_NATURE, GCD_STATUS, GCD_CONTACT_TYPE, GCD_CONTACT_PERSION, " .
				" GCD_CUSTOMER_EMOTION, GCD_CONTACT_NO, GCD_CONTACT_MAILID,  GCD_PROCESS_EMP, " .
				" GCD_TO_DO, GCD_SCHEDULE_DATE, GCD_ESTIMATED_TIME, GCD_SEVERITY, " .
				" GCD_PRIORITY, GCD_LEVEL, GCD_PROMISE_MADE, GCD_PROMISE_DATE, " .
				" GCD_FEEDBACK, GCD_PROBLEM_SUMMARY, GCD_PROBLEM_DESC, " .
				" GCD_REMARKS, GCD_VISIT_REASON,  GCD_VISIT_TIMEOUT, GCD_EXTRA_CHARGES, " .
				" GCD_REPORTED_DATE, GCD_VISIT_NO,GCD_VN_TRANSID,GCD_COMPLAINT_CODE) values" .
				" ('$GCD_COMPLAINT_ID','','$GCD_ACTIVITY_DATETIME','$emp_id' ," .
				" '$GCD_NATURE','$GCD_STATUS','$GCD_CONTACT_TYPE','$authority_name'," .
				" '$gce','$contact_pno','$GCD_EMAIL','$GCD_PROCESS_EMP'," .
				" '$reason_visit','$GCD_SCHEDULE_DATE','$GCD_ESTIMATED_TIME','$gs'," .
				" 'P1','L0','$gpm','$gpd'," .
				" '2','".mysqli_real_escape_string_wrapper($GCD_PROBLEM_SUMMARY)."','".mysqli_real_escape_string_wrapper($problemdesc)."'," .
				" '".mysqli_real_escape_string_wrapper($GCD_REMARKS)."','','$GCD_VISIT_TIMEOUT', '$GCD_EXTRA_CHARGES'," .
				" '$GCD_ACTIVITY_DATETIME','$visit_no','$trans_id','$GCH_COMPLAINT_CODE') ";
		execute_my_query($query_up2);
		$ACT_ID=(string)mysqli_insert_id_wrapper();
		update_lead_header_extension($lead_code, $gce, $GCD_COMPLAINT_ID);
		$update_query="update gft_customer_support_hdr set GCH_LAST_ACTIVITY_ID='$ACT_ID'," .
		"GCH_CURRENT_STATUS='$GCD_STATUS'   " .
		"where GCH_COMPLAINT_ID='$GCD_COMPLAINT_ID' ";
		execute_my_query($update_query);
		
		if($send_sms){    support_sms($ACT_ID,$emp_id);    }
	}
	return $GCD_COMPLAINT_ID;
}
/**
 * @param int $lead_code
 * @param string $groupid
 * @param string $call_type
 * @param string $called_number
 * @param string $support_id
 * @param string $support_status
 * @param string $process_emp
 * @param string $gvg_group
 * 
 * @return void
 */
function send_sms_content_from_master($lead_code, $groupid, $call_type, $called_number='',$support_id='',$support_status='',$process_emp='',$gvg_group=''){
	$sms_content_temp = $support_group = '';
	$customer_cycle = 0;
	if(in_array($groupid,array('13','24'))){  //employee or partner
		$customer_cycle = 5;
		if($gvg_group!=''){
			$group_quer = " select GSP_GROUP_NAME from gft_voicenap_group join gft_support_product_group on".
						  "  (GVG_SUPPORT_GROUP=GSP_GROUP_ID) where GVG_GROUP_ID=$gvg_group";
			$g_res = execute_my_query($group_quer);
			if($group_data = mysqli_fetch_array($g_res)){
				$support_group = $group_data['GSP_GROUP_NAME'];
			}
		}
	}else if(in_array($groupid,array('20','21','22','23'))){  //post sales - product delivery
		$customer_cycle = 4;
		//$pc_query = " select GEM_MOBILE from gft_lead_hdr ".
		//			" join gft_emp_master on (if(GLH_FIELD_INCHARGE!=9999,GLH_FIELD_INCHARGE,GLH_LFD_EMP_ID)) ".
		//			" where GLH_LEAD_CODE='$lead_code'";
		//$pc_res = execute_my_query($pc_query);
		//if($pc_data = execute_my_query($pc_query)){
		//	$pc_mobile = $pc_data['GEM_MOBILE'];
		//}
		
	}else if($lead_code==0){ //unknown number
		$customer_cycle = 6;
	}else if($groupid==1){ //non asa
		$customer_cycle = 2;
	}else{
		$asa_query = " select GCL_LEAD_CODE, GCL_PAYABLE_INSTALLATION_COUNT, GCL_PAYABLE_INSTALLATION_VALIDITY, ".
					 " GCL_FREE_INSTALLATION_COUNT, GCL_FREE_INSTALLATION_VALIDITY, GCL_EXTENED_SUPPORT, GCL_PREMIUM_CUST ".
					 " from gft_customer_product_info where  GCL_LEAD_CODE='$lead_code' ";
		$que_res = execute_my_query($asa_query);
		if(mysqli_num_rows($que_res)==0){
			$customer_cycle = 3;  //not installed
		}else{
			$row_data = mysqli_fetch_array($que_res);
			$payable_count = $row_data['GCL_PAYABLE_INSTALLATION_COUNT'];
			$payable_validity = $row_data['GCL_PAYABLE_INSTALLATION_VALIDITY'];
			$free_count = $row_data['GCL_FREE_INSTALLATION_COUNT'];
			$free_validity = $row_data['GCL_FREE_INSTALLATION_VALIDITY'];
			if( ($payable_count!=0) and ($payable_count==$payable_validity) ){
				$customer_cycle = 1; //standard asa
				if($row_data['GCL_PREMIUM_CUST']=='Y'){
					$customer_cycle = 7; //premium asa
				}
			}elseif ( ($payable_count==0) && ($free_count!=0) && ($free_count==$free_validity) ){
				$customer_cycle = 1;
			}
		}
	}
	//$today = getdate();
	if(isBusinessHourRouting()){  // day start and end hrs
		$shift = 1;
	}else{  //holiday and non business hrs
		$shift = 2;
	}
	//global $night_shift,$show_sms;
	//if($night_shift){
	//	$shift = 2;
	//}
	$sms_query =" select GCS_VMFST_MSG, GCS_VMSEC_MSG, GCS_MISSED_CALL_MSG from gft_callcenter_sms_master ".
				" where (GCS_CUSTOMER_LIFECYCLE=$customer_cycle or GCS_CUSTOMER_LIFECYCLE=0) and ".
				" (GCS_PRODUCT_GROUP=$groupid or GCS_PRODUCT_GROUP=0) and (GCS_SHIFT=$shift or GCS_SHIFT=0) ".
				" order by GCS_CUSTOMER_LIFECYCLE desc, GCS_PRODUCT_GROUP desc, GCS_SHIFT desc limit 1 ";
	$sms_res = execute_my_query($sms_query);
	if($sms_data = mysqli_fetch_array($sms_res)){
		$vm_fisrt = $sms_data["GCS_VMFST_MSG"];
		$vm_second = $sms_data["GCS_VMSEC_MSG"];
		if($call_type==3){  //missed call
			$sms_content_temp = $sms_data["GCS_MISSED_CALL_MSG"];
		}else{
			$sms_content_temp = $vm_fisrt;  //new. first attempt
			if(check_for_complaint_exists($lead_code, 138, $support_id, $support_status) || (in_array($customer_cycle,array(3,4,5,6)) && check_for_voicemail_open($called_number))){
				$sms_content_temp =$vm_second;  //complaint exists or second attempt
			} 
		}
	}
	//if($show_sms){
	//	echo $sms_content_temp;
	//}
	if($sms_content_temp!='') {
		$sms_content_temp = (string)str_replace("{{Support_Id}}", $support_id, $sms_content_temp);
		$sms_content_temp = (string)str_replace("{{Support_Group}}", $support_group, $sms_content_temp);
		if($support_id!=''){
			$exe_name = get_single_value_from_single_table("GEM_SHORT_NAME", "gft_emp_master", "GEM_EMP_ID", $process_emp);
			$sms_content_temp = (string)str_replace("{{Executive_Name}}", $exe_name, $sms_content_temp);
		}
		if($customer_cycle==6){  //unknown
			entry_sending_sms($called_number, $sms_content_temp, 87);
		}else{
			entry_sending_sms_to_customer($called_number, $sms_content_temp, 87, $lead_code,0,null,0,null,/*$newly_added=*/true);
		}
	}
	//if($pc_mobile!=''){
	//	//entry_sending_sms($pc_mobile, $sms_content, $category);
	//}
}
/**
 * @param string $transactionid
 * @param string[string] $call_dtls
 * 
 * @return void
 */
function update_customer_cloud_call_details($transactionid,$call_dtls){
	$check_lead_dtl = execute_my_query("select GCD_LEAD_CODE from gft_customer_cloud_call_dtl where GCD_CALL_TRANS_ID='$transactionid'");
	if((mysqli_num_rows($check_lead_dtl)>0) && $row_lead=mysqli_fetch_array($check_lead_dtl)){
		$lead_code = $row_lead['GCD_LEAD_CODE'];
		$starttime=isset($call_dtls['starttime'])?$call_dtls['starttime']:"";
		$endtime=isset($call_dtls['endtime'])?$call_dtls['endtime']:"";
		$cust_call_dtl = array();
		$cust_call_dtl['GCH_LEAD_CODE']		= $lead_code;
		$cust_call_dtl['GCH_CALL_TRANS_ID']	= $transactionid;
		$cust_call_dtl['GCH_CUST_NO']		= isset($call_dtls['customerno'])?$call_dtls['customerno']:"";
		$cust_call_dtl['GCH_AGENT_NO']		= isset($call_dtls['agentno'])?$call_dtls['agentno']:"";
		$cust_call_dtl['GCH_CALL_DURATION']	= isset($call_dtls['duration'])?$call_dtls['duration']:"";
		$cust_call_dtl['GCH_BILL_SEC']		= isset($call_dtls['billsec'])?$call_dtls['billsec']:"";
		$cust_call_dtl['GCH_START_TIME']	= date("Y-m-d H:i:s",$starttime);
		$cust_call_dtl['GCH_END_TIME']		= date("Y-m-d H:i:s",$endtime);
		$cust_call_dtl['GCH_PALY_URL']		= isset($call_dtls['path'])?$call_dtls['path']:"";
		$cust_call_dtl['GCH_UPDATED_ON']	= date("Y-m-d H:i:s");		
		array_update_tables_common($cust_call_dtl, "gft_customer_cloud_call_history", null, null, SALES_DUMMY_ID,null,null,$cust_call_dtl);
	}
}

/**
 * @param string $contact_no
 * 
 * @return boolean
 */
function is_voice_support_allowed($contact_no){
	$contact_cond = getContactDtlWhereCondition("GCC_CONTACT_NO", $contact_no);
	$que1 = " select GLE_LEAD_CODE,GLE_AVAILABLE_SERVICE_MINS from gft_customer_contact_dtl ".
			" join gft_lead_hdr_ext on (GLE_LEAD_CODE=GCC_LEAD_CODE) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GLE_LEAD_CODE) ".
			" left join gft_assure_care_company on (GAC_REF_ID=GLH_REFERENCE_OF_PARTNER and GLH_LEAD_SOURCE_CODE_PARTNER=37) ".
			" where ( (GCC_ENABLE_CALL_SUPPORT=1 and GLE_AVAILABLE_SERVICE_MINS > 0) or GAC_VOICE_SUPPORT=1 ) and $contact_cond ";
	$res1 = execute_my_query($que1);
	if($row1 = mysqli_fetch_array($res1)){
		return true;
	}
	return false;
}

/**
 * @param string $mob_no
 * 
 * @return boolean
 */
function is_employee_or_partner_contact($mob_no){
    $empDtl = get_employee_info_from_contact($mob_no);
    if(isset($empDtl['GEM_EMP_ID'])){
        return true;
    }else {
        $cp_result=its_active_partner_no($mob_no);
        if($cp_result['is_cp']==='true'){
            return true;
        }
    }
    return false;
}

/**
 * @param string $lead_code
 * 
 * @return string[int]
 */
function get_corporate_chain_customer_ids($lead_code){
    $corp_lead = get_corporate_customer_for_client($lead_code);
    $q1 = " select GLH_LEAD_CODE from gft_lead_hdr where GLH_LEAD_TYPE in (3,13) and ".
        " (GLH_LEAD_CODE='$corp_lead' or (glh_reference_given='$corp_lead' and GLH_LEAD_SOURCECODE=7) ) ";
    $r1 = execute_my_query($q1);
    $ret_arr = /*. (string[int]) .*/array();
    while ($d1 = mysqli_fetch_array($r1)){
        $ret_arr[] = $d1['GLH_LEAD_CODE'];
    }
    return $ret_arr;
}

?>
