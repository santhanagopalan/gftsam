<?php

/*. forward string function get_terr_incharge(int $terri_id,boolean $gft_emp_only=,int $stage=,string $created_category=,string $created_by=); .*/
/*. forward string function send_recent_audit_dtl(string $lead_code,string $emp_id,string $audittypeid); .*/
/*. forward void function enter_reporting_dtl(string $grr_emp_id, string $grr_date, string $grr_leave, string $grr_received, string $grr_received_date); .*/
/*. forward void function insert_gft_track_lead_status(string $lead_code,string $effective_date,string $doc,string $lead_status,string $remarks,string $assign_to=); .*/

require_once(__DIR__ ."/common_query_util.php");
require_once(__DIR__ ."/track_lead_status.php");
require_once(__DIR__ ."/common_util.php");
require_once(__DIR__ ."/auth_util.php");
require_once(__DIR__ ."/function.send_sms.php");
require_once(__DIR__ ."/function.update_in_tables.php");
require_once(__DIR__ ."/saas_webpos/wpgateway_info.php");
require_once(__DIR__ ."/send_mail_followup.php");
require_once(__DIR__ ."/function.license_util.php");
require_once(__DIR__ ."/call_center/call_center_util.php");
require_once(__DIR__ ."/push_notification/push_notification_util.php");

/**
 * @param string $lead_code
 * @param string $effective_date
 * @param string $doc
 * @param string $lead_status
 * @param string $remarks
 * @param string $assign_to
 * 
 * @return void
 */
function insert_gft_track_lead_status($lead_code,$effective_date,$doc,$lead_status,$remarks,$assign_to=null){
	$GTL_TRACK_TYPE=0;
	$effective_date=substr($effective_date,0,10);
	if($effective_date==''){
		$effective_date=date('Y-m-d');
	}

	$balance='';
	$field_incharge='';
	$sales_emp_id='';
	$GLH_PROSPECTS_STATUS='';
	$potential_amt='';
	$query="select glh_lfd_emp_id,GLH_FIELD_INCHARGE, GLH_BALANCE_AMOUNT,GLH_INTEREST_ADDON,GLH_APPROX_TIMETOCLOSE,GLH_STATUS,GLH_PROSPECTS_STATUS," .
			"GLH_POTENTIAL_AMT from gft_lead_hdr " .
			"where glh_lead_code='$lead_code'  ";
	$result=execute_my_query($query,'',true,false);
	if($qdata=mysqli_fetch_array($result)){
		$sales_emp_id=$qdata['glh_lfd_emp_id'];
		$balance=$qdata['GLH_BALANCE_AMOUNT'];
		$intrest_addon=$qdata['GLH_INTEREST_ADDON'];
		$field_incharge=$qdata['GLH_FIELD_INCHARGE'];
		if($doc=='' or $doc=='0000-00-00'){
			$doc=$qdata['GLH_APPROX_TIMETOCLOSE'];
		}
		$elead_status=$qdata['GLH_STATUS'];
		$potential_amt=$qdata['GLH_POTENTIAL_AMT'];
		if($lead_status=='' or ($elead_status!=$lead_status and ($elead_status==8 or $elead_status==9))){
			$lead_status=$elead_status;
		}
		$GLH_PROSPECTS_STATUS=$qdata['GLH_PROSPECTS_STATUS'];
	}
	$emp_id=$sales_emp_id;
	if($lead_status=='3'){
		$GTL_TRACK_TYPE=1;
		if($doc=='' or $doc=='0000-00-00'){
			$doc=date('Y-m-d',mktime('0','0','0',date('m')+1,date('d'),date('Y')));
		}
	}else if($balance>0 and ($lead_status=='8' or $lead_status=='9')){
		$GTL_TRACK_TYPE=2;
	}else if($lead_status=='8' or $lead_status=='9'){
		$result_resel=execute_my_query("SELECT GID_LEAD_CODE FROM gft_install_dtl_new join gft_lead_hdr on (GID_LEAD_CODE=GLH_LEAD_CODE) WHERE GID_LEAD_CODE='$lead_code' and GID_STATUS='A' " .
				"and ((GID_EXPIRE_FOR in (1,3)  and GID_VALIDITY_DATE<now() and GID_READY_TO_PAY_ASS='Y') or GID_UPGRADE='Y' or GID_UPSELL='Y' or GLH_INTEREST_ADDON='Y')");
		if(mysqli_num_rows($result_resel)>0 and $data_f=mysqli_fetch_array($result_resel)){
			if($data_f['GID_LEAD_CODE']==$lead_code) $GTL_TRACK_TYPE=3;
		}
		$emp_id=(($field_incharge!='' and $field_incharge!=0 )?$field_incharge:$sales_emp_id);
	}else{
		$GTL_TRACK_TYPE=0;
	}
	if($doc!='' and $doc!='0000-00-00'){
		$query="select * from gft_track_lead_status where gtl_lead_code='$lead_code' " .
				" and gtl_month=month('$effective_date')" .
				" and gtl_year=year('$effective_date')";
		$result=execute_my_query($query,'',true,false);
		$num_rows=mysqli_num_rows($result);
		if($num_rows==0 and $doc!='0000-00-00' and $GTL_TRACK_TYPE!=0){
			$year_month_no=year_month_no(null,null,$effective_date);
			$query_trac_lead_status="insert into gft_track_lead_status(GTL_LEAD_CODE,GTL_EMP_ID, " .
					"GTL_MONTH, GTL_YEAR, GTL_DOC, GTL_LEAD_STATUS,GTL_PROSPECT_STATUS, GTL_REMARKS, GTL_UPDATED_ON,GTL_TRACK_TYPE,GTL_POTENTIAL_AMT,GTL_YR_MONTH) values " .
					"('$lead_code',$emp_id,month('$effective_date'),year('$effective_date'),'$doc'," .
					"'$lead_status','$GLH_PROSPECTS_STATUS','".mysqli_real_escape_string_wrapper($remarks)."',now(),$GTL_TRACK_TYPE,'$potential_amt','$year_month_no')";
			execute_my_query($query_trac_lead_status,'',true,false);
		}else {			
			$query_update="update gft_track_lead_status set GTL_ASSIGN_FROM=if(GTL_EMP_ID!=$emp_id,GTL_EMP_ID,GTL_ASSIGN_FROM),GTL_EMP_ID='$emp_id',GTL_LEAD_STATUS='$lead_status',GTL_PROSPECT_STATUS='$GLH_PROSPECTS_STATUS'," .
					" GTL_DOC='$doc',GTL_REMARKS='".mysqli_real_escape_string_wrapper($remarks)."',GTL_UPDATED_ON=now(),GTL_TRACK_TYPE=$GTL_TRACK_TYPE,GTL_POTENTIAL_AMT='$potential_amt' " .
					" where gtl_lead_code='$lead_code' and gtl_month=month('$effective_date') " .
				    " and gtl_year=year('$effective_date') ";
			execute_my_query($query_update,'',true,false);
		}
	}
}

/**
 * @param string $lead_code
 * @param mixed[] $product_arr
 * @param boolean $deselect_prod
 *
 * @return string[int]
 */
function insert_intrested_products($lead_code,$product_arr,$deselect_prod=false){
	$submit_msg=array('','');
	$product_not_interested_arr=/*. (string[int]) .*/ array();
	if(isset($_REQUEST['product_not_interested'])){
		$product_not_interested=(string)$_REQUEST['product_not_interested'];
		if(!is_array($product_not_interested)){
			$product_not_interested_arr=explode(',',$product_not_interested);
		}
		foreach($product_not_interested_arr as $npr){
			if($npr!=''){
				$query_p="replace into gft_lead_product_dtl(GLC_LEAD_CODE,GLC_PRODUCT_CODE," .
						"GLC_INTEREST_LEVEL)values('$lead_code','$npr','N')";
				$result_p=execute_my_query($query_p,'',true,false);
				if(!$result_p){
					$submit_msg[0].="Product detail is not submitted";
					$submit_msg[1].="\n Product detail is not submitted \n $query_p";
				}
			}
		}/* end of For */
	}else if($product_arr==null && isset($_REQUEST['products_shown'])){
		$product_arr=explode(',',(isset($_REQUEST['products_shown'])?(string)$_REQUEST['products_shown']:""));//PRODUCT INTERESTED
	}
	if(is_array($product_arr)){
		foreach($product_arr as $pr){	
			if((int)$pr!=0){
				$ins_arr = array('GLC_LEAD_CODE'=>$lead_code,'GLC_PRODUCT_CODE'=>$pr,'GLC_INTEREST_LEVEL'=>'Y');
				$key_arr = array('GLC_LEAD_CODE'=>$lead_code,'GLC_PRODUCT_CODE'=>$pr);
				array_update_tables_common($ins_arr,"gft_lead_product_dtl",$key_arr,null,SALES_DUMMY_ID,null,null,$ins_arr);
			}
		}
	}
	
	$inter_que =" select group_concat(GPM_PRODUCT_ABR) as inter_prod, group_concat(GLC_PRODUCT_CODE) as inter_pcode ".
			" from gft_lead_product_dtl join gft_product_family_master on (GPM_PRODUCT_CODE=GLC_PRODUCT_CODE) ".
			" where GLC_INTEREST_LEVEL='Y' and GLC_LEAD_CODE='$lead_code' group by GLC_LEAD_CODE ";
	$inter_res = execute_my_query($inter_que);
	if($prod_data = mysqli_fetch_array($inter_res)){
		$interes_prod = $prod_data['inter_prod'];
		execute_my_query("update gft_lead_hdr_ext set GLE_INTERESTED_PRODUCT='$interes_prod' where GLE_LEAD_CODE='$lead_code'");
		if($deselect_prod && is_array($product_arr)){
			$interes_pcode = explode(',', $prod_data['inter_pcode']);
			$deselect_arr  = /*.(string[int]).*/array_diff($interes_pcode, $product_arr);
			foreach($deselect_arr as $npr){
				if($npr!=''){
					$query_p="replace into gft_lead_product_dtl(GLC_LEAD_CODE,GLC_PRODUCT_CODE," .
							"GLC_INTEREST_LEVEL)values('$lead_code','$npr','N')";
					$result_p=execute_my_query($query_p);
				}
			}
		}
	}
	return $submit_msg;	
}

/**
 * @param string $lead_code
 * @param string $user_id
 * @param int $status
 * @param int $monitor_type
 *
 * @return void
 */
function update_monitor_lead($lead_code,$user_id,$status=1,$monitor_type=1){
	if($status==0 and $user_id!=''){
		execute_my_query(" delete from gft_lead_monitors where GLM_LEAD_CODE=$lead_code and GLM_EMP_ID=$user_id AND GLM_MONITOR_TYPE=$monitor_type ");
	}else if($status==1 and $lead_code!=null and $lead_code!='' and $user_id!=''){
		execute_my_query("insert ignore into gft_lead_monitors (GLM_LEAD_CODE,GLM_EMP_ID,GLM_MONITOR_TYPE) values ($lead_code,$user_id,$monitor_type)");
		/*$query="SELECT GEM_EMP_ID FROM gft_lead_monitors, gft_emp_master where GEM_EMP_ID=GLM_EMP_ID and GEM_EMP_ID=$user_id and GLM_LEAD_CODE=$lead_code and GEM_STATUS='A' ";
		$result_monitor=execute_my_query($query);
		$monitor_for_emp_id=false;
		if(mysqli_num_rows($result_monitor)>0){
			$monitor_for_emp_id=true;
		}
		if($monitor_for_emp_id==false){
			execute_my_query("insert into gft_lead_monitors (GLM_LEAD_CODE,GLM_EMP_ID) values ($lead_code,$user_id )");
		}
		*/
	}
}

/**
 * @param int $territory_id
 *
 * @return int
 */
function generate_lead_code($territory_id){
	$query_lead_code="select max(glh_lead_code)+1 from gft_lead_hdr ";
	$result_lead_code=execute_my_query($query_lead_code,'',true,false);
	$qdata=mysqli_fetch_array($result_lead_code);
	$new_lead_code=(int)$qdata[0];	
	return $new_lead_code;
}

/**
 * @param string $lead_code
 * @param string $contact_number
 * @param boolean $corp_check
 * @param string $ref_lead
 * @return string
 */
function check_for_contact_conflict($lead_code,$contact_number,$corp_check,$ref_lead) {
	$exist_lead = '';
	$pos_contact_chk_qry = " select gcc_id from gft_customer_contact_dtl ".
			" join gft_pos_users on (gcc_id=gpu_contact_id) ".
			" where gcc_lead_code='$lead_code' and GCC_CONTACT_NO='$contact_number' ";
	$pos_contacts_res = execute_my_query($pos_contact_chk_qry);
	if(mysqli_num_rows($pos_contacts_res)==0) {
		$exist_lead_arr = get_lead_code_for_contact_no($contact_number,$lead_code,$corp_check,$ref_lead);
		$exist_lead = isset($exist_lead_arr[0])?$exist_lead_arr[0]:'';
	}
	return $exist_lead;
}
/**
 * @param string[int] $contact_name
 * @param string[int] $contact_no
 * @param string[int] $contact_desig
 * @param string $lead_code
 * @param string $removecontactid
 * @param string[int] $contact_type
 * @param string[int] $contact_id
 * @param string[int] $old_contact_name
 * @param string[int] $old_contact_no
 * @param string[int] $old_contact_desig
 * @param string[int] $old_contact_type
 * @param string $import_without_activity
 * @param boolean $new_lead
 * @param string[int] $is_valid
 * @param string $can_send
 * @param string[int] $contact_group1
 * @param string[int] $contact_group2
 * @param string[int] $contact_group3
 * @param string[string] $old_contact_group1
 * @param string[string] $old_contact_group2
 * @param string[string] $old_contact_group3
 * @param string $ref_lead
 * @param boolean $check_duplicate
 * @param string[int] $phone_support
 * 
 * @return int[int]
 */
function insert_lead_contact_nos($contact_name,$contact_no,$contact_desig,$lead_code,$removecontactid,
$contact_type,$contact_id=null,$old_contact_name=null,$old_contact_no=null,
$old_contact_desig=null,$old_contact_type=null,$import_without_activity='off',
$new_lead=false,$is_valid=null,$can_send=null,$contact_group1=null,$contact_group2=null,$contact_group3=null,
$old_contact_group1=null,$old_contact_group2=null,$old_contact_group3=null,$ref_lead='',$check_duplicate=true,$phone_support=null){

	global $uid;

    //print_r($contact_name);
    $submit_msg='';
    $array_contact_id_ins=/*. (int[int]) .*/ array();
   	if(!is_array($contact_id)){
		$contact_id = (array) $contact_id;
		$contact_id[1]="";
	}
	
	$table_columns="GCC_id,GCC_LEAD_CODE,GCC_CONTACT_NAME,GCC_DESIGNATION,GCC_CONTACT_NO, gcc_contact_type," .
			"GCC_CAN_SEND,GCC_SENT_NEWLEAD_SMS,GCC_ENABLE_CALL_SUPPORT ";
	$table_name="gft_customer_contact_dtl";
	$insert_parm="insert into $table_name ($table_columns)";
	$referred_id_column="GCC_ID";
	$concat_values=" concat(GCC_id,',',ifnull(GCC_CONTACT_NAME,''),',',ifnull(GCC_DESIGNATION,''),','," .
				   "ifnull(GCC_CONTACT_NO,''),',',ifnull(gcc_contact_type,'')) ";
	if($removecontactid!=null){
		$query_f=" select '','$table_name','$table_columns'," .
				" $concat_values," .
				"'','',now(),'$uid','$lead_code'  " .
				"from $table_name where $referred_id_column in ($removecontactid) ";
		$query_audit="insert into gft_audit_log_edit_table(" .
				" GUL_AUDIT_ID ,GUL_AUDIT_TABLE ,GUL_AUDIT_COLUMNS,GUL_AUDIT_OLD_VALUES," .
				" GUL_AUDIT_NEW_VALUES,GUL_PAGE ,GUL_TIME ,GUL_USER_ID,GUL_AUDIT_LEAD_CODE) " .
				" ($query_f)";
		//echo $query_audit;   
		$result2=execute_my_query($query_audit,'',true,false);
		execute_my_query("delete from $table_name where $referred_id_column in ($removecontactid)",'',true,false);
	}
	$corp_check = false;
	$lead_type = get_single_value_from_single_table("GLH_LEAD_TYPE", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
	if(in_array($lead_type, array('3','13'))){
		$corp_check = true;
	}
	
	//array start with 1 
	$ci=0;
	foreach($contact_no as $cn => $value ){
		$c=$cn;
		if($contact_no[$c]!=''){
			$contact_no[$c]=str_replace("'", "", trim($contact_no[$c]));
			$contact_number=str_replace("'", "", trim($contact_no[$c]));
			if($contact_type[$c]=='' or $contact_type[$c]=='0'){
				if(strpos($contact_no[$c],'@')>1 and !is_numeric($contact_no[$c])){
			    	$contact_type[$c]='4';
				}else if((strpos($contact_no[$c],'www.')==0 or strpos($contact_no[$c],'http:')==0) and !is_numeric($contact_no[$c])){
			    	$contact_type[$c]='6';
				}else if(is_numeric($contact_no[$c])){
					if(strlen($contact_no[$c])==14 and strpos($contact_no[$c],'0091')===0){
			    		$contact_no[$c]=substr($contact_no[$c],-10);
			    	}
			    	if(check_can_send_sms($contact_no[$c])){
				    	$contact_type[$c]='1';
					}else{
				    	$contact_type[$c]='2';
			    	}
				}
			}
			$update_techsupp='';
			if( $check_duplicate && ($lead_type!='7') ){ //dealer
				$exist_lead = check_for_contact_conflict($lead_code,$contact_number,$corp_check,$ref_lead);
				if($exist_lead!=''){
					$array_contact_id_ins[0] = 0;
					$array_contact_id_ins[1] = $exist_lead;
					$array_contact_id_ins[2] = $contact_number;
					return $array_contact_id_ins;
				}
			}
			if($contact_desig[$c]==0 or $contact_desig[$c]==''){
				$contact_desig[$c]=1;
			}
			if(!isset($contact_id[$c]) or $contact_id[$c]==''){
				$contact_id[$c]=(isset($contact_id[$c])?$contact_id[$c]:'');
				$contact_person=trim($contact_name[$c]);
				$contact_type_str=$contact_type[$c];
				if(is_numeric($contact_number)){
					$temp_contact_no = $contact_number;
					$contact_qry	=	getContactDtlWhereCondition('GCC_CONTACT_NO', $temp_contact_no);
					$contact_qry1	=	getContactDtlWhereCondition('gtc_number', $temp_contact_no);
					$checkquery="select * from $table_name where gcc_lead_code=$lead_code and $contact_qry ";
					$tech_whr_cond = " gtc_lead_code=0 and $contact_qry1 ";
					$sel_res = execute_my_query("select GTC_ID from gft_techsupport_incomming_call where $tech_whr_cond");
					if(mysqli_num_rows($sel_res) > 0){
					    execute_my_query("update gft_techsupport_incomming_call set gtc_lead_code='$lead_code' where $tech_whr_cond ");
					}
				}else{
				    $contact_qry    =   " GCC_CONTACT_NO='$contact_number' ";
					$checkquery="select * from $table_name where gcc_lead_code=$lead_code and GCC_CONTACT_NO='$contact_number'";
				}
				$q1 = " select GPU_CONTACT_ID from gft_customer_contact_dtl join gft_pos_users on (GCC_ID=GPU_CONTACT_ID) ".
					  " where GPU_CONTACT_STATUS='I' and gcc_lead_code='$lead_code' and $contact_qry ";
				$r1 = execute_my_query($q1);
				if($d1 = mysqli_fetch_array($r1)){
				    $cont_id = $d1['GPU_CONTACT_ID'];
				    execute_my_query("delete from gft_pos_users where GPU_CONTACT_ID='$cont_id'");
				}
				$ckeck_result=execute_my_query($checkquery,'',true,false);
				
				if(mysqli_num_rows($ckeck_result)<1){
					if($import_without_activity=='on'){ $can_send='Y';$new_lead_sms='N';}
					else {$can_send='Y';$new_lead_sms='Y'; }
					$phone_support_1 = isset($phone_support[$c])?(int)$phone_support[$c]:'0';
					$contact_person=remove_special_characters($contact_person); 
					$query_contact=" $insert_parm values ('$contact_id[$c]','$lead_code'," .
			       				"'$contact_person','$contact_desig[$c]','$contact_number'," .
			       				"'$contact_type_str','$can_send','$new_lead_sms','$phone_support_1')";
			     
			        $result_contact=execute_my_query($query_contact,'',true,false);
			        $array_contact_id_ins[$ci]=$new_contact_id=mysqli_insert_id_wrapper();
			  		$user_id = $uid;
			  		if($user_id==''){
			  			$user_id = SALES_DUMMY_ID;  //incase of webservice
			  		}
			        //audit log FOR NEW Contact Save
			        $new_values = mysqli_real_escape_string_wrapper("$contact_name[$c],$contact_desig[$c],$contact_no[$c],$contact_type[$c]");
			        $query_audit="insert into gft_audit_log_edit_table(" .
			        		" GUL_AUDIT_ID ,GUL_AUDIT_TABLE ,GUL_AUDIT_COLUMNS,GUL_AUDIT_OLD_VALUES," .
			        		" GUL_AUDIT_NEW_VALUES,GUL_PAGE ,GUL_TIME ,GUL_USER_ID,GUL_AUDIT_LEAD_CODE)values" .
			        		" ('','$table_name','$table_columns','Newly Saved'," .
			        		" '$new_values','',now(),'$user_id','$lead_code')";
			        $result2=execute_my_query($query_audit,'',true,false);
			       
			        /* setting the value of contact in contact group incase of new
			         * if selected in ui the default value get set */
		            if(is_array($contact_group1)){
			        	if(isset($contact_group1[$c]) and $contact_group1[$c]!=''){ $contact_group1[$c]=$new_contact_id; }
			        }
			        if(is_array($contact_group2)){
			        	if(isset($contact_group2[$c]) and $contact_group2[$c]!=''){ $contact_group2[$c]=$new_contact_id; }
			        }
			        if(is_array($contact_group3)){
			        	if(isset($contact_group3[$c]) and $contact_group3[$c]!=''){ $contact_group3[$c]=$new_contact_id; }
			        }
			        /*end of update contact id in contact_group field incase of new */
			        if($new_lead==false and $contact_type[$c]==1 and isset($_SESSION['uid'])){
			        	send_update_contact_no_sms($lead_code,$contact_number,$uid);
			        }
			        $ci++;
				}else{
					if(is_array($contact_group1)){
			        	if(isset($contact_group1[$c]) and $contact_group1[$c]!=''){ $contact_group1[$c]=mysqli_result($ckeck_result,0,'gcc_id'); }
			        }
			        if(is_array($contact_group2)){
			        	if(isset($contact_group2[$c]) and $contact_group2[$c]!=''){ $contact_group2[$c]=mysqli_result($ckeck_result,0,'gcc_id'); }
			        }
			        if(is_array($contact_group3)){
			        	if(isset($contact_group3[$c]) and $contact_group3[$c]!=''){ $contact_group3[$c]=mysqli_result($ckeck_result,0,'gcc_id'); }
			        }
				}
			}else{
				//Proceed only if the variable exists.
				if(isset($old_contact_desig[$c]) &&  ($old_contact_name[$c]!=$contact_name[$c]  or  $contact_desig[$c]!=$old_contact_desig[$c] or 
						$old_contact_no[$c]!=$contact_no[$c] or  $old_contact_type[$c]!=$contact_type[$c])){							
							
					$contact_name[$c]=remove_special_characters($contact_name[$c]); 
					$query_contact="update $table_name set " .
								" GCC_CONTACT_NAME= trim('$contact_name[$c]'), GCC_DESIGNATION='$contact_desig[$c]',GCC_CONTACT=0, " .
								" GCC_CONTACT_NO=trim('$contact_no[$c]'), gcc_contact_type='$contact_type[$c]' " .
								" where gcc_id='$contact_id[$c]' and gcc_lead_code='$lead_code'";
					$submit_msg.="$query_contact";
					$result=execute_my_query($query_contact,'',true,false);
			       	//echo $query_contact;
			       	if($old_contact_no[$c]!=$contact_no[$c] and $contact_type[$c]==1){
			       		$assign_emp='';
			       		if( isset($_REQUEST['assign_lead']) && (string)$_REQUEST['assign_lead']=='on' ){
			       			$assign_emp=isset($_REQUEST['femp_code'])?(string)$_REQUEST['femp_code']:'';
			       		}
			       		send_update_contact_no_sms($lead_code,$contact_no[$c],$uid,$assign_emp);
			       	}
			       	$r_row=trim(mysqli_error_wrapper());
					if($r_row!=""){
						$submit_msg.="Contact details in row $c not saved " ;
					}else{			
						$query_audit="insert into gft_audit_log_edit_table(" .
								" GUL_AUDIT_ID ,GUL_AUDIT_TABLE ,GUL_AUDIT_COLUMNS,GUL_AUDIT_OLD_VALUES," .
								" GUL_AUDIT_NEW_VALUES,GUL_PAGE ,GUL_TIME ,GUL_USER_ID,GUL_AUDIT_LEAD_CODE)values" .
								" ('','$table_name','$table_columns','$contact_id[$c],$old_contact_name[$c]," .
								"$old_contact_desig[$c],$old_contact_no[$c],$old_contact_type[$c]'," .
								"'$contact_id[$c],$contact_name[$c],$contact_desig[$c],$contact_no[$c],$contact_type[$c]',''," .
								"now(),'$uid','$lead_code')";
						//echo $query_audit;   
						$result2=execute_my_query($query_audit,'',true,false);
					}
				}//end of  if 
				
				if(isset($is_valid[$c]) and $is_valid[$c]!=''){/* update valid or not */
					$table_name_c='gft_customer_contact_dtl';
					$table_key_arr=array();
					$update_column=array();
					$table_key_arr['GCC_ID']=$contact_id[$c];
					$table_key_arr['GCC_LEAD_CODE']=$lead_code;
					$update_column['GCC_VALID']=$is_valid[$c];
					$update_column['GCC_CAN_SEND']=$can_send[$c];
					$update_column['GCC_ENABLE_CALL_SUPPORT']=isset($phone_support[$c])?$phone_support[$c]:"0";
					array_update_tables_common($update_column,$table_name_c,$table_key_arr,null,$uid,$remarks=null,
					$table_column_iff_update=null);
				}
				
				
			}//end of else 
			
		}
	}//end of for
	if($lead_code!=''){
		execute_my_query(" UPDATE gft_customer_contact_dtl,gft_lead_hdr SET GCC_CONTACT=substring(gcc_contact_no,-10) " .
				" WHERE GLH_LEAD_CODE='$lead_code' and GLH_LEAD_CODE=GCC_LEAD_CODE AND glh_country='India' " .
				" and gcc_contact_type in (1,2,3) and (GCC_CONTACT=0 or GCC_CONTACT is null) AND GCC_CONTACT_NO REGEXP '[A-z]$'=0 ");
	}
	
	$query_gcg="select GCG_CONTACT_GROUP_ID,concat('contact_group',GCG_CONTACT_GROUP_ID) field_name," .
			"concat('old_contact_group',GCG_CONTACT_GROUP_ID) old_field_name, GCG_ACCEPT_SINGLE from gft_contact_dtl_group_master " .
			"where GCG_CONTACT_GROUP_STATUS='A' ";
	$result_gcg=execute_my_query($query_gcg);
	$cgdata=array();
	
	while($qdcg=mysqli_fetch_array($result_gcg)){
		$gcg_group_id=$qdcg['GCG_CONTACT_GROUP_ID'];
		$temp_v_field_name=$qdcg['field_name'];
		$temp_v_old_field_name=$qdcg['old_field_name'];
		$field_name=$$temp_v_field_name; //NOTE: $ -of- $
		$old_field_name=$$temp_v_old_field_name; //NOTE: $ -of- $
		$cgdata[$gcg_group_id]['accept_single']=$qdcg['GCG_ACCEPT_SINGLE'];
		//echo $qdcg['field_name'];print_r($field_name);
		if(!empty($field_name)){
			foreach($field_name as $key=>$value){
				
			    if(isset($old_field_name[$key]) && $old_field_name[$key]=='Y' && $value=='' && isset($contact_id[$key]) && $contact_id[$key]!=''){
					/* remove from group*/
					$del_query="delete from gft_contact_dtl_group_map where GCG_LEAD_CODE=$lead_code and GCG_CONTACT_ID=$contact_id[$key] and GCG_GROUP_ID=$gcg_group_id ";
					execute_my_query($del_query);					
				}				
			    if($value==''){ continue; } 
				if($qdcg['GCG_ACCEPT_SINGLE']=='Y'){
					$del_query="delete from gft_contact_dtl_group_map where GCG_LEAD_CODE=$lead_code and GCG_CONTACT_ID!=$value and GCG_GROUP_ID=$gcg_group_id";
					execute_my_query($del_query);
				}
				$ins_query="insert ignore into gft_contact_dtl_group_map (GCG_LEAD_CODE,GCG_CONTACT_ID,GCG_GROUP_ID,GCG_UPDATED_DATE) values ($lead_code,$value,$gcg_group_id,now())";
				execute_my_query($ins_query);
			}
		}	
			
	}
	return $array_contact_id_ins;
}

/**
 * @param string $lead_code_raw
 * @param string $emp_id
 * @param boolean $update_hdr
 *
 * @return void
 */
function insert_lead_fexec_dtl($lead_code_raw,$emp_id,$update_hdr=false){	
	global $me;
	$start_date=date('Y-m-d');
	$end_date="0000-00-00";
	$status='A';
	$emp_exist='';
	
	if($emp_id==''){
		return;
	}
	
	if(!is_array($lead_code_raw)){
		$lead_code_arr=array($lead_code_raw);
	}else {$lead_code_arr=$lead_code_raw; }
	
	for($i=0;$i<count($lead_code_arr);$i++){
		$lead_code=$lead_code_arr[$i];
		$reemp_exist=execute_my_query("select glh_lfd_emp_id from gft_lead_hdr " .
				" join gft_lead_fexec_dtl on (GLF_LEAD_CODE=glh_lead_code and GLF_STATUS='A') " .
				" where glh_lead_code=$lead_code", $me,true,false);
		if($data=mysqli_fetch_array($reemp_exist)){
			$emp_exist=$data['glh_lfd_emp_id'];
		}
		if($emp_exist!=$emp_id){
			$query_emp_terr_update="Update gft_lead_fexec_dtl set glf_to_date='$start_date',glf_status='I' " .
				" where glf_status='A' and glf_lead_code='$lead_code'  ";
			execute_my_query($query_emp_terr_update,$me,true,false);
			$query_emp_terr="replace into gft_lead_fexec_dtl(GLF_LEAD_CODE,GLF_EMP_ID,GLF_FROM_DATE," .
							"GLF_TO_DATE,GLF_STATUS) values('$lead_code','$emp_id','$start_date'," .
							"'$end_date','$status')";
			execute_my_query($query_emp_terr,'',true,false);
			if($update_hdr==true){
				execute_my_query("update gft_lead_hdr set glh_lfd_emp_id=$emp_id where glh_lead_code=$lead_code",$me,
				true,false);
			}
		}
	}/*end of for*/
}
/**
 * 
 * @return int
 */
function get_order_easy_owner($GLH_CREATED_CATEGORY,$cust_id){
    $currect_lead_owner = 0;
    if(in_array($GLH_CREATED_CATEGORY, array(64,70,73))){
        $query_to_take_lead_owner = " SELECT glh_lfd_emp_id FROM gft_lead_hdr ".
            " INNER JOIN gft_emp_master ON(GEM_EMP_ID=GLH_LFD_EMP_ID) ".
            " WHERE GLH_LEAD_CODE='$cust_id' AND GEM_STATUS='A' AND GEM_EMP_ID NOT IN('9999','9998')";
        $currect_lead_owner = (int)get_single_value_from_single_query("glh_lfd_emp_id", $query_to_take_lead_owner);        
    }
    return $currect_lead_owner;
}
/**
 * @param string $country
 * @param string $state
 * @param int $created_category
 * @param int $vertical_id
 * @param int $terr_id
 * @param int $customer_id
 * @param boolean $send_call_pre
 * @param boolean $return_business_emp
 * @param int $business_type
 *
 * @return string
 */
function get_lead_mgmt_incharge($country,$state,$created_category,$vertical_id,$terr_id=100,$customer_id=0,
		$send_call_pre=false,$return_business_emp=false,$business_type=0){
	$business_type_id = $business_type;
	if($customer_id > 0){
		$sql1 = " select glh_country,GLH_CUST_STATECODE,GLH_CREATED_CATEGORY,GLH_VERTICAL_CODE,GLH_TERRITORY_ID ".
				" from gft_lead_hdr where GLH_LEAD_CODE='$customer_id' ";
		$res1 = execute_my_query($sql1);
		if($data1 = mysqli_fetch_array($res1)){
			$country	 		= $data1['glh_country'];
			$state 				= $data1['GLH_CUST_STATECODE'];
			$created_category 	= $data1['GLH_CREATED_CATEGORY'];
			$vertical_id 		= $data1['GLH_VERTICAL_CODE'];
			$terr_id 			= $data1['GLH_TERRITORY_ID'];
		}
	}
	$country_code	=	0;
	$state_id	=	0;
	$incharge		=	'';
	$business_incharge_id = 0;
	$call_preference_group= '706';
	//1. To identify country code
	if($country!=''){
			$country_code	=	2;//India
			$country = mysqli_real_escape_string_wrapper($country);
			$sql_country	=	"select GPM_MAP_ID from gft_political_map_master where GPM_MAP_TYPE='C' " .
								"and GPM_MAP_STATUS='A' and GPM_MAP_NAME='".trim($country)."' LIMIT 0,1";
			$result_country	=	execute_my_query($sql_country,'function.insert_stmt.php',true,false,2);
			if(mysqli_num_rows($result_country)!=0){
				$row			=	mysqli_fetch_array($result_country);
				$country_code	=	$row['GPM_MAP_ID'];
			}else{
				$country_code	=	34;//Other country
			}
	}	
	//2. To identify state code 
	if($state!=''){
		$state	=	mysqli_real_escape_string_wrapper(trim($state));
		$res_get_stateid	=	execute_my_query("select state_id from p_map_view where state='$state' limit 1 ");
		if(mysqli_num_rows($res_get_stateid)==0){
			$state_id	=	0	;
		}else {
			$row_state	=	mysqli_fetch_array($res_get_stateid);
			$state_id	=	$row_state['state_id'];
		}	
	}else{
		$state_id	=	0	;
	}
	if($created_category!=''){
		$created_category	=	(int)$created_category;
	}else{
		$created_category	=	0	;
	}
	if($vertical_id!=''){
		$vertical_id	=	(int)$vertical_id;
		//Is it micro vertical?
		$res_check_is_micro	=	execute_my_query("select GTM_MICRO_OF from gft_vertical_master where GTM_VERTICAL_CODE=$vertical_id");
		if(mysqli_num_rows($res_check_is_micro)==1){
			$row_macro	=	mysqli_fetch_array($res_check_is_micro);
			if($row_macro['GTM_MICRO_OF']!=0 and $row_macro['GTM_MICRO_OF']!=''){
				$vertical_id	=	$row_macro['GTM_MICRO_OF'];
			}
		}
	}else{
		$vertical_id	=	0	;
	}
	if((int)$vertical_id>0){
		$business_type_id = (int)get_single_value_from_single_table("gbr_business_id", "gft_bvp_relation", "gbr_vertical", "$vertical_id");
	}
	if($country_code==2 or $country_code==0){
		
		$query_lead_incharge=	" select GLI_EMP_ID,GLI_PRESALES_SUPPORT_GROUP,GLI_BUSINESS_INCHARGE from gft_lmt_incharge_master where (GLI_COUNTRY=$country_code or GLI_COUNTRY=0)".
								" and (GLI_STATE=$state_id or GLI_STATE=0) and (GLI_CATEGORY=$created_category or GLI_CATEGORY=0) and ".
								" (GLI_BUSINESS_TYPE=$business_type_id OR GLI_BUSINESS_TYPE=0) AND ".
								" (GLI_VERTICAL=$vertical_id or GLI_VERTICAL=0)  and GLI_ACTIVE_STATE='A' order by GLI_BUSINESS_TYPE desc, GLI_CATEGORY desc, GLI_VERTICAL desc,".
								"  GLI_STATE desc, GLI_COUNTRY desc limit 1";	
		$result_lead_in			=	execute_my_query($query_lead_incharge,'function.insert_stmt.php',true,false,2);		
	}else{		
		$result3=execute_my_query("select *from gft_lmt_incharge_master where GLI_COUNTRY=$country_code");
		if(mysqli_num_rows($result3)==0){
			$country_code=34;
			$result31=execute_my_query("select *from gft_lmt_incharge_master where GLI_COUNTRY=$country_code");
			if(mysqli_num_rows($result31)==0){
				$country_code=0;
			}
		}
		$result0	=	execute_my_query("select *from gft_lmt_incharge_master where GLI_COUNTRY=$country_code AND GLI_STATE=$state_id");
		if(mysqli_num_rows($result0)==0){
			$state_id	=	0;
		}
 		/* $result1=execute_my_query("select *from gft_lmt_incharge_master where GLI_COUNTRY=$country_code AND GLI_STATE=$state_id AND GLI_CATEGORY=$created_category");
		if(mysqli_num_rows($result1)==0){
			$created_category=0;
		}
		$result2=execute_my_query("select *from gft_lmt_incharge_master where GLI_STATE=$state_id AND GLI_CATEGORY=$created_category and GLI_VERTICAL=$vertical_id and GLI_COUNTRY=$country_code");
		if(mysqli_num_rows($result2)==0){
			$vertical_id=0;
		} */
		$query_lead_incharge	=	" SELECT GLI_EMP_ID,GLI_PRESALES_SUPPORT_GROUP,GLI_BUSINESS_INCHARGE FROM gft_lmt_incharge_master WHERE (1) ";
		$query_lead_incharge	.=	" AND (GLI_COUNTRY=$country_code OR GLI_COUNTRY in (0,34))  AND (GLI_STATE=$state_id OR GLI_STATE=0) ".
									" AND (GLI_CATEGORY=$created_category OR GLI_CATEGORY=0) ".
									" AND (GLI_BUSINESS_TYPE=$business_type_id OR GLI_BUSINESS_TYPE=0)  ".
									" AND (GLI_VERTICAL=$vertical_id OR GLI_VERTICAL=0) and GLI_ACTIVE_STATE='A' ".
									" order by GLI_BUSINESS_TYPE desc, GLI_CATEGORY desc, GLI_VERTICAL desc,  GLI_STATE desc, GLI_COUNTRY desc LIMIT 0,1";
		$result_lead_in			=	execute_my_query($query_lead_incharge,'function.insert_stmt.php',true,false,2);
	}
	if(mysqli_num_rows($result_lead_in)!=0){
		$row			=	mysqli_fetch_array($result_lead_in);
		$incharge		=	$row['GLI_EMP_ID'];
		$call_preference_group=((int)$row['GLI_PRESALES_SUPPORT_GROUP']>0?$row['GLI_PRESALES_SUPPORT_GROUP']:$call_preference_group);
		$business_incharge_id = (int)$row['GLI_BUSINESS_INCHARGE'];
	}else{
		$query_lead_incharge1=	" SELECT GLI_EMP_ID,GLI_PRESALES_SUPPORT_GROUP,GLI_BUSINESS_INCHARGE FROM gft_lmt_incharge_master WHERE (1) AND GLI_ID=1 LIMIT 0,1";
		$result_lead_in1	=	execute_my_query($query_lead_incharge1,'function.insert_stmt.php',true,false,2);
		if($row=mysqli_fetch_array($result_lead_in1)){
			$incharge		=	$row['GLI_EMP_ID'];
			$call_preference_group=((int)$row['GLI_PRESALES_SUPPORT_GROUP']>0?$row['GLI_PRESALES_SUPPORT_GROUP']:$call_preference_group);
			$business_incharge_id = (int)$row['GLI_BUSINESS_INCHARGE'];
		}
	}
	if($send_call_pre){
		return $call_preference_group;
	}
	if($return_business_emp){
		return $business_incharge_id;
	}
	return $incharge;
}

/**
 * @param string $country
 * @param string $state
 * @param int $created_category
 * @param int $vertical_id
 * @param int $customer_id
 *
 * @return string
 */
function get_annuity_incharge_based_on_region($country,$state,$created_category,$vertical_id,$customer_id=0){
	if($customer_id > 0){
		$sql1 = " select glh_country,GLH_CUST_STATECODE,GLH_CREATED_CATEGORY,GLH_VERTICAL_CODE,GLH_TERRITORY_ID ".
				" from gft_lead_hdr where GLH_LEAD_CODE='$customer_id' ";
		$res1 = execute_my_query($sql1);
		if($data1 = mysqli_fetch_array($res1)){
			$country	 		= $data1['glh_country'];
			$state 				= $data1['GLH_CUST_STATECODE'];
			$created_category 	= $data1['GLH_CREATED_CATEGORY'];
			$vertical_id 		= $data1['GLH_VERTICAL_CODE'];
		}
	}
	$country_code = $state_id = 0;
	$incharge = '';
	if($country!=''){
		$country_code	=	2;
		$sql_country	=	"select GPM_MAP_ID from gft_political_map_master where GPM_MAP_TYPE='C' " .
				"and GPM_MAP_STATUS='A' and GPM_MAP_NAME='".trim($country)."' LIMIT 0,1";
		$result_country	=	execute_my_query($sql_country,'function.insert_stmt.php',true,false,2);
		if(mysqli_num_rows($result_country)!=0){
			$row			=	mysqli_fetch_array($result_country);
			$country_code	=	$row['GPM_MAP_ID'];
		}else{
			$country_code	=	34;
		}
	}
	if($state!=''){
		$state	=	mysqli_real_escape_string_wrapper(trim($state));
		$res_get_stateid	=	execute_my_query("select state_id from p_map_view where state='$state' limit 1 ");
		if($row_state	=	mysqli_fetch_array($res_get_stateid)){
			$state_id	=	$row_state['state_id'];
		}
	}
	if($created_category!=''){
		$created_category	=	(int)$created_category;
	}else{
		$created_category	=	0	;
	}
	if($vertical_id!=''){
		$vertical_id	=	(int)$vertical_id;
		//Is it micro vertical?
		$res_check_is_micro	=	execute_my_query("select GTM_MICRO_OF from gft_vertical_master where GTM_VERTICAL_CODE=$vertical_id");
		if(mysqli_num_rows($res_check_is_micro)==1){
			$row_macro	=	mysqli_fetch_array($res_check_is_micro);
			if($row_macro['GTM_MICRO_OF']!=0 and $row_macro['GTM_MICRO_OF']!=''){
				$vertical_id	=	$row_macro['GTM_MICRO_OF'];
			}
		}
	}else{
		$vertical_id	=	0	;
	}
	if($country_code==2 or $country_code==0){
		$query_lead_incharge=	" select GLI_ANNUITY_INCHARGE from gft_lmt_incharge_master where (GLI_COUNTRY=$country_code or GLI_COUNTRY=0)".
				" and (GLI_STATE=$state_id or GLI_STATE=0) and (GLI_CATEGORY=$created_category or GLI_CATEGORY=0) and ".
				" (GLI_VERTICAL=$vertical_id or GLI_VERTICAL=0)  and GLI_ACTIVE_STATE='A' order by GLI_VERTICAL desc,".
				" GLI_CATEGORY desc, GLI_STATE desc, GLI_COUNTRY desc limit 1";
		$result_lead_in			=	execute_my_query($query_lead_incharge,'function.insert_stmt.php',true,false,2);
	}else{
		$result3=execute_my_query("select *from gft_lmt_incharge_master where GLI_COUNTRY=$country_code");
		if(mysqli_num_rows($result3)==0){
			$country_code=34;
			$result31=execute_my_query("select *from gft_lmt_incharge_master where GLI_COUNTRY=$country_code");
			if(mysqli_num_rows($result31)==0){
				$country_code=0;
			}
		}
		$result0	=	execute_my_query("select *from gft_lmt_incharge_master where GLI_COUNTRY=$country_code AND GLI_STATE=$state_id");
		if(mysqli_num_rows($result0)==0){
			$state_id	=	0;
		}
		$result1=execute_my_query("select *from gft_lmt_incharge_master where GLI_COUNTRY=$country_code AND GLI_STATE=$state_id AND GLI_CATEGORY=$created_category");
		if(mysqli_num_rows($result1)==0){
			$created_category=0;
		}
		$result2=execute_my_query("select *from gft_lmt_incharge_master where GLI_STATE=$state_id AND GLI_CATEGORY=$created_category and GLI_VERTICAL=$vertical_id and GLI_COUNTRY=$country_code");
		if(mysqli_num_rows($result2)==0){
			$vertical_id=0;
		}
		$query_lead_incharge	=	" SELECT GLI_ANNUITY_INCHARGE FROM gft_lmt_incharge_master WHERE (1) ";
		$query_lead_incharge	.=	" AND (GLI_COUNTRY=$country_code OR GLI_COUNTRY=0)  AND (GLI_STATE=$state_id OR GLI_STATE=0) ".
				" AND (GLI_CATEGORY=$created_category OR GLI_CATEGORY=0)".
				" AND (GLI_VERTICAL=$vertical_id OR GLI_VERTICAL=0) and GLI_ACTIVE_STATE='A' ".
				" order by GLI_VERTICAL desc, GLI_CATEGORY desc, GLI_STATE desc, GLI_COUNTRY desc LIMIT 0,1";
		$result_lead_in			=	execute_my_query($query_lead_incharge,'function.insert_stmt.php',true,false,2);
	}
	if($row = mysqli_fetch_array($result_lead_in)){
		$incharge		=	$row['GLI_ANNUITY_INCHARGE'];
	}else{
		$query_lead_incharge1=	" SELECT GLI_ANNUITY_INCHARGE FROM gft_lmt_incharge_master WHERE GLI_ID=1 ";
		$result_lead_in1	=	execute_my_query($query_lead_incharge1,'function.insert_stmt.php',true,false,2);
		if($row=mysqli_fetch_array($result_lead_in1)){
			$incharge		=	$row['GLI_ANNUITY_INCHARGE'];
		}
	}
	return $incharge;
}

/**
 * @param string $lead_code
 * @param string $country
 * @param string $state
 * @param string $pincode
 * @param string $city
 * @param int $current_territory_id
 * @param string $current_lead_incharge
 * @param int $lead_type
 * @param int $created_by
 * @param string $created_category
 * @param int $vertical_id
 *
 * @return mixed[string]
 */
function find_territory_id_and_incharge($lead_code,$country,$state,$pincode,$city,$current_territory_id,$current_lead_incharge,$lead_type,
		$created_by,$created_category,$vertical_id){ 
//error_log("lead_code=".$lead_code. " country=".$country. " state=".$state . " pincode=".$pincode." city=".$city." current_territory_id=".$current_territory_id . " current_lead_incharge=".$current_lead_incharge. " lead_type=".$lead_type." created_by=".$created_by." created_category=".$created_category." vertical_id=".$vertical_id);

	$lead_terr_dtl=/*. (mixed[string]) .*/ array();
	if($country=='India' and isset($pincode) and empty($current_territory_id) ){
		$resultpincode=execute_my_query("SELECT GPM_DISTRICT_ID, GPM_TERRITORY_ID,gbt_sales_incharge FROM gft_pincode_master " .
				" left join gft_business_territory_master on (GBT_TERRITORY_ID=GPM_TERRITORY_ID) where GPM_PINCODE='".$pincode."' group by GPM_PINCODE");
		if(($datapincode=mysqli_fetch_array($resultpincode)) and mysqli_num_rows($resultpincode)==1){
			$lead_terr_dtl['GLH_DISTRICT_ID']=(($datapincode['GPM_DISTRICT_ID']!=null and $datapincode['GPM_DISTRICT_ID']!=0)?$datapincode['GPM_DISTRICT_ID']:0);
			$lead_terr_dtl['GLH_TERRITORY_ID']=(($datapincode['GPM_TERRITORY_ID']!=null and $datapincode['GPM_TERRITORY_ID']!=0)?$datapincode['GPM_TERRITORY_ID']:0);
		}
	}

	if(empty($lead_terr_dtl['GLH_TERRITORY_ID']) and empty($current_territory_id) ){
		/* Pincode not in our master or pincode not given */
		if($country!='' or $state!='' or $city!='' ){
			$city = mysqli_real_escape_string_wrapper($city);
			$state = mysqli_real_escape_string_wrapper($state);
			$query_city="Select country_id ,district_id,district_default_terr_id,state_default_terr_id,country_default_terr_id from p_map_view " .
					" where ( country='$country'  or district='$city' or state='$state' ) limit 1";

			$result_city=execute_my_query($query_city);
			if(mysqli_num_rows($result_city)==1){
				$qdcity=mysqli_fetch_array($result_city);
				$lead_terr_dtl['GLH_DISTRICT_ID']=$qdcity['district_id'];
				if(!empty($qdcity['district_default_terr_id']) )
					$lead_terr_dtl['GLH_TERRITORY_ID']=$qdcity['district_default_terr_id'];
				if(!empty($qdcity['state_default_terr_id']) )
					$lead_terr_dtl['GLH_TERRITORY_ID']=$qdcity['state_default_terr_id'];
				if(!empty($qdcity['country_default_terr_id']) )
					$lead_terr_dtl['GLH_TERRITORY_ID']=$qdcity['country_default_terr_id'];
			}
		}

		if(empty($lead_terr_dtl['GLH_TERRITORY_ID']) and $country!=''){
			$query_cc="select GPM_DEFAULT_TERRITORY from  gft_political_map_master  where !isnull(GPM_DEFAULT_TERRITORY) and GPM_DEFAULT_TERRITORY!=0 and " .
					" (GPM_MAP_NAME= '$country' or GPM_COUNTRY_CODE='$country' or GPM_COUNTRY_CODE_2='$country') limit 1 ";
			$result_cc=execute_my_query($query_cc);
			if(mysqli_num_rows($result_cc)==1){
				$qdcc=mysqli_fetch_array($result_cc);
				$lead_terr_dtl['GLH_TERRITORY_ID']=$qdcc['GPM_DEFAULT_TERRITORY'];
			}
		}
		if(empty($lead_terr_dtl['GLH_TERRITORY_ID']) and ($country!='' or $country=='') ){
			$query_cc="select GPM_DEFAULT_TERRITORY from  gft_political_map_master  where !isnull(GPM_DEFAULT_TERRITORY) and GPM_DEFAULT_TERRITORY!=0 and " .
					" (GPM_MAP_ID=34) limit 1 ";
			$result_cc=execute_my_query($query_cc);
			if(mysqli_num_rows($result_cc)==1){
				$qdcc=mysqli_fetch_array($result_cc);
				$lead_terr_dtl['GLH_TERRITORY_ID']=$qdcc['GPM_DEFAULT_TERRITORY'];
			}
		}
	}
	$lmt_incharge = get_lead_mgmt_incharge($country,$state,$created_category,$vertical_id,$lead_terr_dtl['GLH_TERRITORY_ID']);
	$territory_id = isset($lead_terr_dtl['GLH_TERRITORY_ID'])?$lead_terr_dtl['GLH_TERRITORY_ID']:'';
	if( ($territory_id!='') and ( ( empty($lead_code) and empty($current_lead_incharge) )  or (!empty($current_lead_incharge) and $current_lead_incharge==SALES_DUMMY_ID ) ) ){
	//	if($territory_id=='100'){ //default territory
			$lead_terr_dtl['GLH_LFD_EMP_ID'] = $lmt_incharge;
	//	}else{
	//		$lead_terr_dtl['GLH_LFD_EMP_ID']=get_terr_incharge($territory_id,false,1,$created_category,$created_by);
	//	}
	}else if($current_lead_incharge!=SALES_DUMMY_ID){
		$lead_terr_dtl['GLH_LFD_EMP_ID']=$current_lead_incharge;
	}
	$lead_terr_dtl['GLH_LMT_EMP_ID'] = $lmt_incharge; 
	if($lead_type==2){
		$lead_terr_dtl['GLH_LMT_EMP_ID']=$lead_terr_dtl['GLH_LFD_EMP_ID'];//Assign to Lead Management Team, what mapped in picode master for default territory
	}
	return $lead_terr_dtl;
}

/**
 * @param string[int] $conperson_arr
 * @param string[int] $contact_no_arr
 * @param string[int] $con_designation_arr
 * @param string[int] $con_type_arr
 * @param string $check_lead
 *
 * @return string
 */
function get_contact_detail_in_ui($conperson_arr,$contact_no_arr, $con_designation_arr,	$con_type_arr, $check_lead=''){
	$tab_dtl = "<table border='1' width='80%' ><tr><th>Contact Name</th><th>Contact No</th><th>Designation</th><th>Contact Type</th></tr>";
	foreach ($contact_no_arr as $key_id => $val){
		$name = isset($conperson_arr[$key_id])?$conperson_arr[$key_id]:'';
		$con_no = isset($contact_no_arr[$key_id])?$contact_no_arr[$key_id]:'';
		$desig = isset($con_designation_arr[$key_id])?get_single_value_from_single_table("GCD_NAME", "gft_contact_designation_master", "GCD_CODE", $con_designation_arr[$key_id]):'';
		$con_type = isset($con_type_arr[$key_id])?get_single_value_from_single_table("gct_desc", "gft_cust_contact_type_master", "gct_id", $con_type_arr[$key_id]):'';
		$tab_dtl .= "<tr><td>$name</td><td>$con_no</td><td>$desig</td><td>$con_type</td></tr>";
		if($check_lead!=''){
			insert_lead_contact_nos(array($name), array($con_no), array($con_designation_arr[$key_id]), $check_lead, null, array($con_type_arr[$key_id]));
		}
	}
	$tab_dtl.="</table>";
	return $tab_dtl;
}

/**
 * @param string $purpose
 * @param string $to_emp
 * @param string $old_lead_code
 * @param string[string] $new_lead_details
 * @param string $status_reason
 * @param string $owner_reason
 * @param string $mail_cc
 * @param string $cc_ids
 * @param boolean $send_mail
 *
 * @return int
 */
function create_ticket_for_lead_duplicate($purpose,$to_emp, $old_lead_code, $new_lead_details, $status_reason='', $owner_reason='',$mail_cc='E',$cc_ids='',$send_mail=true){
	global $uid;
	if($uid==''){
		$uid = '9999';  // web order in the case of trial from product / website
	}
	$glh_status = isset($new_lead_details['GLH_STATUS'])?$new_lead_details['GLH_STATUS']:'';
	$glh_lfd = isset($new_lead_details['GLH_LFD_EMP_ID'])?$new_lead_details['GLH_LFD_EMP_ID']:'9999';
	$summary = $desc = "";
	$data_query =" select c1.GCS_NAME old_stat, c2.GCS_NAME new_stat, e1.GEM_EMP_NAME old_name, e2.GEM_EMP_NAME new_name ".
			" from gft_lead_hdr ".
			" left join gft_emp_master e1 on (e1.GEM_EMP_ID=GLH_LFD_EMP_ID) ".
			" left join gft_emp_master e2 on (e2.GEM_EMP_ID=$glh_lfd) ".
			" left join gft_customer_status_master c1 on (c1.GCS_CODE=GLH_STATUS) ".
			" left join gft_customer_status_master c2 on (c2.GCS_CODE=$glh_status) ".
			" where GLH_LEAD_CODE='$old_lead_code'";
	$data_res = execute_my_query($data_query);
	if($data_dtl = mysqli_fetch_array($data_res)){
		$old_stat_name = $data_dtl['old_stat'];
		$new_stat_name = $data_dtl['new_stat'];
		$old_emp_name = $data_dtl['old_name'];
		$new_emp_name = $data_dtl['new_name'];
		$desc .="<table border='1'>".
				"<tr><th>Field</th><th>Old Value</th><th>New Value</th><th>Action Taken</th></tr>".
				"<tr><td>Lead Status</td><td>$old_stat_name</td><td>$new_stat_name</td><td>$status_reason</td></tr>".
				"<tr><td>Lead Owner</td><td>$old_emp_name</td><td>$new_emp_name</td><td>$owner_reason</td></tr>".
				"</table>";
	}
	$cc_mail = '';
	$GCH_COMPLAINT_CODE = 0;
	if($purpose=="lead_status"){
		$summary = "Conflict in Lead Status";
		$GCH_COMPLAINT_CODE=306;
		$cc_mail= get_samee_const('PRESALES_TEAM_MAIL');
	}elseif($purpose="lead_owner"){
		$summary = "Conflict Lead Ownership";
		$GCH_COMPLAINT_CODE=307;
		if($mail_cc=='E'){
			$cc_mail = get_samee_const('Sales_Mgmt_Mail');
		}else{
			$cc_mail = get_samee_const('PARTNER_MGMT_TEAM_MAIL');
		}
	}
	$ticket_id = insert_support_entry($old_lead_code, '10', '01.0', '', '', $uid, $reason_visit='70', $summary,
					$GCH_COMPLAINT_CODE, 'T0',date('Y-m-d H:i:s'),null,$GCD_PROCESS_EMP=$to_emp,null,'S4',$desc,true);
	$mail_content = /*. (string[int]) .*/array();
	
	$emp_name = get_single_value_from_single_table("GEM_EMP_NAME", "gft_emp_master", "GEM_EMP_ID", $to_emp);
	if($send_mail) {
		$db_content_config = /*. (string[string][int]) .*/array(
			"Employee_Name"=>array($emp_name),
			"problem_description"=>array($desc),
			"comp_id"=>array($ticket_id),
			"problem_summary"=>array($summary)
		);
		send_formatted_mail_content($db_content_config, 6, 192, array($to_emp), null,null,null, array($cc_mail,$cc_ids));
	}
	return intval($ticket_id);
}

/**
 * @param string $old_lead_code
 * @param string $old_lfd_emp
 * @param string[string] $new_lead_dtl
 *
 * @return string[int]
 */
function check_for_pincode_and_update($old_lead_code, $old_lfd_emp, $new_lead_dtl){
	$add_msg = /*. (string[int]) .*/array();
	$ticket_id=0;
	$new_lfd_id = $new_lead_dtl['GLH_LFD_EMP_ID'];
	$to_emp = '';
	$query = " select gbt_sales_incharge from  gft_lead_hdr ".
			" join gft_pincode_master on (GPM_PINCODE=GLH_CUST_PINCODE) ".
			" join gft_business_territory_master on (GBT_TERRITORY_ID=GPM_TERRITORY_ID) ".
			" where GPM_PIN_STATUS='A' and GLH_LEAD_CODE='$old_lead_code' ";
	$res = execute_my_query($query);
	if(mysqli_num_rows($res)==0){
// No ticket 
//		$to_emp = get_samee_const("LMT_Incharge");
//		$add_msg[1] = "No Changes. Ticket Raised due to Unknown Pincode";
		 
	}else if($dat = mysqli_fetch_array($res)){
		if($dat['gbt_sales_incharge']==$old_lfd_emp){ //territory owner
			//Ticket to Sales Coordinator
			$to_emp = get_samee_const("Sales_Incharge");
			$add_msg[1] = "No Changes. Ticket Raised due to Old Lead Owner is a Territory Incharge";
		}else{
			//change lead owner to new
			$add_msg[1] = "Changed to New Value because Old Lead Owner is not a Territory Incharge";
			update_lead_incharge($old_lead_code,$new_lfd_id);
		}
	}
	if($to_emp!='') {
		$ticket_id	=	create_ticket_for_lead_duplicate($purpose="lead_owner",$to_emp,$old_lead_code, $new_lead_dtl, '', $add_msg[1],'E');
		$add_msg[0]  = "Your Reporting Manager / Sales Coordinator will discuss with you";
	}
	$add_msg[2]=$ticket_id;
	return $add_msg;
}

/**
 * 
 * @param string $old_lead_code
 * @param string $old_lfd_emp
 * 
 * @return void
 */
function notify_lead_owner_conflict($old_lead_code,$old_lfd_emp) {
	$cust_name = get_single_value_from_single_table("glh_cust_name", "gft_lead_hdr", "glh_lead_code", $old_lead_code);
	$noti_content_config = array();
	$noti_content_config['Customer_Name'] = array("$old_lead_code - $cust_name");
	send_formatted_notification_content($noti_content_config, 62, 37, 1, $old_lfd_emp,$old_lead_code);
	
	$mobileno = get_single_value_from_single_table("gem_mobile", "gft_emp_master", "gem_emp_id", $old_lfd_emp);
	$sms_content = "Your prospect or customer ($cust_name - $old_lead_code) is approached by another Gofrugal partner or executive. ".
			"Contact partner management team if you have any objection.";
	$sms_id = entry_sending_sms($mobileno, $sms_content, 184,$old_lfd_emp);
	
	$db_content_config = /*. (string[string][int]) .*/array(
			"Customer_Name"=>array("$cust_name - $old_lead_code")
	);
	send_formatted_mail_content($db_content_config, 6, 253, array($old_lfd_emp));
}

/**
 * @param string $old_lead_code
 * @param string[string] $new_lead_dtl
 * @param string $from_page
 *
 * @return string[int]
 */
function update_lead_status_and_lead_owner_for_conflict($old_lead_code, $new_lead_dtl, $from_page='') {
	$msg=/*. (string[int]) .*/array();  
	//0 - alert msg, 1 - status reason msg,  2 - owner reason msg, 3- ticket id, 4- lmt incharge, 5- Business type, 6- created category
	$ticket_id=0;
	$query = " select GLH_STATUS, GCS_DUPLICATE_CASE, GLH_LFD_EMP_ID, CGI_EMP_ID, GEM_EMP_ID, GEM_OFFICE_EMPID, ".
			 " GEM_STATUS, CGI_STATUS,GLH_CREATED_CATEGORY,GLH_BTYPE_CODE ".
			" from gft_lead_hdr ".
			" join gft_customer_status_master on (GLH_STATUS=GCS_CODE) ".
			" left join gft_emp_master on (GEM_EMP_ID=GLH_LFD_EMP_ID) ".  // employee
			" left join gft_cp_info on (CGI_EMP_ID=GLH_LFD_EMP_ID) ".   // partner
			" where GLH_LEAD_CODE='$old_lead_code' ";
	$res = execute_my_query($query);
	if($row_lead = mysqli_fetch_array($res)){
		$duplicate_status = $row_lead['GCS_DUPLICATE_CASE'];
		$old_lfd_emp	  = $row_lead['GLH_LFD_EMP_ID'];
		$old_emp_status	  = $row_lead['GEM_STATUS'];
		$old_partner_stat = ($row_lead['CGI_STATUS']=='10')?'A':'I';  //10 - active
		$old_lead_status  = $row_lead['GLH_STATUS'];
		$new_lead_status = $new_lead_dtl['GLH_STATUS'];
		$new_lead_lfd	 = $new_lead_dtl['GLH_LFD_EMP_ID'];
		$cust_pincode = $new_lead_dtl['GLH_CUST_PINCODE'];
		$new_prospect_satus = isset($new_lead_dtl['GLH_PROSPECTS_STATUS'])?(int)$new_lead_dtl['GLH_PROSPECTS_STATUS']:0;
		$msg[5] =(int)$row_lead['GLH_BTYPE_CODE'];
		$msg[6] =(int)$row_lead['GLH_CREATED_CATEGORY'];
		if($old_lead_status != $new_lead_status){
			if($from_page=='website') {
				if($duplicate_status=="From New") {
					update_lead_status($old_lead_code,$new_lead_status,null,null,null,false,$new_prospect_satus);
					$msg[1] = "Changed to new lead status.";
				} else {
					$msg[1] = "No Changes due to Website.";
				}
			}else if($duplicate_status=="From New"){
				update_lead_status($old_lead_code,$new_lead_status,null,null,null,false,$new_prospect_satus);
				$msg[1] = "Changed to New Lead Entry Status";
			}elseif ($duplicate_status=="From Old"){
				$cc_emp='';$cc_type='E';
				if($old_lead_status=='3'){ //prospect
					if( ($from_page=='website') && ($old_emp_status=="A") ){
						$to_emp = $old_lfd_emp;
						if(is_gft_employee($to_emp)){
							$cc_emp = implode(',', get_email_addr_reportingmaster($to_emp));
						}else{
							$cc_emp = get_partner_business_mgr_mail_id($to_emp);
							$cc_type='P';
						}
					}else{
						$to_emp = get_samee_const("LMT_Incharge");
					}
				}else if($old_lead_status=='24'){ //qualify later
					$msg[0] = "Lead Management Representative will discuss with you";
					$msg[1] = "No changes. Appointment created to LMT incharge";
					$msg[4] = get_lead_mgmt_incharge('', '', '', '',100,$old_lead_code);
					$msg[7] = get_lead_single_value($old_lead_code,'glh_lfd_emp_id','A');
					$msg[8] = $old_lead_status;
					return $msg;
				}else{
					$to_emp = get_samee_const("Annuity_Incharge");
				}
				$msg[1] = "No changes. Ticket Raised due to Lead Status Conflict";
				$ticket_id	=	create_ticket_for_lead_duplicate($purpose="lead_status",$to_emp,$old_lead_code, $new_lead_dtl, $msg[1],'',$cc_type,$cc_emp);
			}elseif ($duplicate_status=="Conflict Prospect"){
				$conf_que = "select GCS_CODE from gft_customer_status_master where GCS_CODE='$new_lead_status' and GCS_DUPLICATE_CASE='From Old' ";
				$conf_res = execute_my_query($conf_que);
				if($conf_data = mysqli_fetch_array($conf_res)){
					$msg[1] = "Changed to New Lead Entry Status";
					update_lead_status($old_lead_code, $new_lead_status);
				}
			}
		}else{
			$msg[1] = "Changed to New Lead Entry Status";
		}
		$ticket = false;
		$send_mail = true;
		if($old_lfd_emp!=$new_lead_lfd){
			if($from_page=='website'){
				$msg[2] = "No Changes due to Website and From Old status";
			}else{
				$notified_to_owner = false;
				$old_emp_categ = (((int)$row_lead['GEM_OFFICE_EMPID'])!=0)?'E':'P';
				if($old_emp_categ=="E"){
					if(is_gft_employee($new_lead_lfd)){  //employee
						if( ($old_emp_status=="A")){ //active
							 $reason_msg = check_for_pincode_and_update($old_lead_code,$old_lfd_emp,$new_lead_dtl);
							 $msg[0] = isset($reason_msg[0])?$reason_msg[0]:'';
							 $msg[2] = isset($reason_msg[1])?$reason_msg[1]:'';
							 $ticket_id	=isset($reason_msg[2])?$reason_msg[2]:'';
													
						}else{  //inactive
							update_lead_incharge($old_lead_code,$new_lead_lfd);
							$msg[2] = "Changed to New Value due to Inactive of Old Employee";
						}	
					}else {  //partner
						$msg[0] = "Please discuss with partner Management team for accessing the lead.";
						$msg[2] = "No Changes. Ticket raised due to Old Owner is Employee and New Owner is Partner";
						$ticket = true;
						$to_emp = get_partner_business_mgr($new_lead_lfd);
						$terri_id=get_territory_for_lead($old_lead_code);
						$sales_incharge = get_terr_incharge($terri_id);
						if($sales_incharge==$old_lfd_emp) {
							notify_lead_owner_conflict($old_lead_code, $old_lfd_emp);
							$send_mail = false;
						}
					}
				}elseif ($old_emp_categ=="P") {
					$no_of_day = get_last_activity_date_diff($old_lead_code,$old_lfd_emp);
					$max_days = (int)get_samee_const("ACTIVITY_DAYS_LEAD_OWNER_CHANGE");
					$activity_count_qry = execute_my_query("select gld_activity_id from gft_activity ".
										" where gld_lead_code='$old_lead_code' and ".
										" gld_emp_id='$old_lfd_emp' and datediff(now(),gld_visit_date)<=90");
					$activity_count = mysqli_num_rows($activity_count_qry);
					if( ($no_of_day==0 && $activity_count==0) || ($no_of_day > $max_days) || ($old_partner_stat=='I') ) {
						update_lead_incharge($old_lead_code, $new_lead_lfd);
						$msg[2] = "Changed to New Value because of Old Partner is Inactive / Last Activity > $max_days days";
					}else{ //ticket
						$ticket = true;
						$msg[0] = "Please discuss with partner Management team for accessing the lead.";
						if(is_gft_employee($new_lead_lfd)) {
							$to_emp = get_samee_const("Sales_Incharge");
						}else{
							$to_emp = get_partner_business_mgr($new_lead_lfd);
						}
						$msg[2] = "No Changes. Ticket Raised due to Old Partner Activity is <= $max_days days";
						notify_lead_owner_conflict($old_lead_code, $old_lfd_emp);
						$send_mail = false;
					}
				}
				if($ticket){
					$to_emp = ($to_emp!='')?$to_emp : get_samee_const("Partner_Incharge");  //BM may be inactive so partner inchrage intaken
					$ticket_id	=	create_ticket_for_lead_duplicate($purpose="lead_owner",$to_emp,$old_lead_code, $new_lead_dtl,'',$msg[2],'P',$send_mail);
				}
			}
		}else{
			$msg[2] = "Changed to New Lead Owner";
		}
	}
	$msg[3]	=	$ticket_id;
	return $msg;
}

/**
 * @param string $ref_lead
 *
 * @return void
 */
function update_reference_count_in_hdr($ref_lead){
	$ref_lead_exists = get_single_value_from_single_table("GLH_LEAD_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", $ref_lead);
	if($ref_lead_exists!=''){
		$sel_res = execute_my_query("select count(GLH_LEAD_CODE) as ref_cnt from gft_lead_hdr where GLH_REFERENCE_GIVEN='$ref_lead' and glh_lead_sourcecode=7");
		if($row1 = mysqli_fetch_array($sel_res)){
			$ref_count = $row1['ref_cnt'];
			if(exists_in_lead_hdr_ext($ref_lead)){
				execute_my_query("update gft_lead_hdr_ext set GLE_TOTAL_REF_GIVEN='$ref_count' where GLE_LEAD_CODE='$ref_lead'");
			}else{
				execute_my_query("insert into gft_lead_hdr_ext (GLE_LEAD_CODE,GLE_TOTAL_REF_GIVEN) values ('$ref_lead','$ref_count')");
			}
		}
	}
}
/**
 * @param string $lead_code
 * 
 * @return void
 */
function update_lead_contact_verified($lead_code){
	execute_my_query("update gft_lead_hdr set GLH_CONTACT_VERIFIED=2 where GLH_LEAD_CODE='$lead_code' ");
}
/**
 * @param string[int] $chk_contact_type
 * @param string[int] $chk_contact_desig
 * @param string[int] $designation
 * @param string[int] $contact_type
 * @param string $lead_code
 * @return boolean
 */
function check_for_proper_contact($chk_contact_type,$chk_contact_desig,$designation,$contact_type,$lead_code='') {
	$cnt = 0;
	if($lead_code!='' and (count($designation)==0 || count($contact_type)==0)) {
		$cond = '';
		if(count($chk_contact_desig)>0) {
			$cond .= " and gcc_designation in ('".implode("','",$chk_contact_desig)."') ";
		}
		if(count($chk_contact_type)>0) {
			$cond .= " and gcc_contact_type in ('".implode("','",$chk_contact_type)."') ";
		}
		$qry = " select gcc_designation,gcc_contact_type from gft_customeR_contact_dtl where gcc_lead_code='$lead_code' $cond ";
		$res = execute_my_query($qry);
		while($row = mysqli_fetch_array($res)) {
			$designation[] = $row['gcc_designation'];
			$contact_type[] = $row['gcc_contact_type'];
		}
	}
	$result = true;
	if((is_array($chk_contact_type) && count($chk_contact_type)>0) || (is_array($chk_contact_desig) && count($chk_contact_desig)>0)) {
		for($i=0;$i<count($chk_contact_type);$i++) {
			for($j=0;$j<count($chk_contact_desig);$j++) {
				foreach ($contact_type as $key=>$val) {
					if($val==$chk_contact_type[$i] and $designation[intval($key)]==$chk_contact_desig[$j]) {
						$cnt++;
						break;
					}
				}
			}
		}
		if($cnt<(count($chk_contact_desig)*count($chk_contact_type))) {
			$result = false;
		}
	}
	return $result;
}
/**
 * @param string[string] $lead_arr
 * @param string[int] $product_arr
 * @param string[int] $array_conper
 * @param string[int] $array_cno
 * @param string[int] $designation
 * @param string[int] $contact_type
 * @param string $removecontactid
 * @param string $import_without_activity
 * @param string[int] $contact_group1
 * @param string[int] $contact_group2
 * @param string[int] $contact_group3
 * @param string $from_page
 * @param string $sumbit_type
 * @param string[int] $chk_contact_type
 * @param string[int] $chk_contact_desig
 * @param boolean $skip_lead_duplicate_check
 * 
 * @return string[int]
 */
function array_insert_new_lead_db($lead_arr,$product_arr,$array_conper,$array_cno,$designation,	$contact_type,
$removecontactid=null,$import_without_activity='off',$contact_group1=null,$contact_group2=null,$contact_group3=null,
		$from_page='',$sumbit_type='',$chk_contact_type=null,$chk_contact_desig=null, $skip_lead_duplicate_check=false){
	global $uid;

	$lead_created_status = /*. (string[int]) .*/ array();
	$submit_msg='';
	$submit_msg_mail='';

	if(!isset($uid)) {$uid=SALES_DUMMY_ID; }
	$pull_leads_category=array("6","10","13","17","18","19");	
	$lead_arr['GLH_CREATED_DATE']=date('Y-m-d H:i:s');
	$lead_arr['GLH_RESPONSE_EFFECT_FROM']=date('Y-m-d H:i:s');
	$lead_arr['GLH_CUST_PINCODE']=(isset($lead_arr['GLH_CUST_PINCODE'])?trim($lead_arr['GLH_CUST_PINCODE']):'');
	if(!isset($lead_arr['GLH_LEAD_TYPE']) or $lead_arr['GLH_LEAD_TYPE']=='') $lead_arr['GLH_LEAD_TYPE']=1;
    if(!isset($lead_arr['GLH_DATE']) or $lead_arr['GLH_DATE']=='') $lead_arr['GLH_DATE']=date('Y-m-d H:i:s');
	if(empty($lead_arr['GLH_COUNTRY'])){$lead_arr['GLH_COUNTRY']='India';}
	if(!isset($lead_arr['GLH_CREATED_BY_EMPID'])){$lead_arr['GLH_CREATED_BY_EMPID']=SALES_DUMMY_ID;}
	if(!isset($lead_arr['GLH_RESPONSE_GROUP']) or $lead_arr['GLH_RESPONSE_GROUP']==''){$lead_arr['GLH_RESPONSE_GROUP']=23;}
	if(!isset($lead_arr['GLH_VERTICAL_CODE']) or $lead_arr['GLH_VERTICAL_CODE']=='' or $lead_arr['GLH_VERTICAL_CODE']=='0'){$lead_arr['GLH_VERTICAL_CODE']=51;}
	if(!isset($lead_arr['GLH_STATUS']) or $lead_arr['GLH_STATUS']==''){$lead_arr['GLH_STATUS']=3;}
	if($lead_arr['GLH_CREATED_BY_EMPID']==SALES_DUMMY_ID){$lead_arr['GLH_RESPONSE_GROUP']=40;}/*move to auto response group */	
	$received_lead_status = (int)$lead_arr['GLH_STATUS'];
	$is_valid_start = LeadStatusStateMachine::getInstance()->isStartingState($received_lead_status);
	if(!$is_valid_start){
		$lead_arr['GLH_STATUS'] = 26;
	}
	
	
	$lead_arr['GLH_INITIAL_RESPONSE_GROUP']=$lead_arr['GLH_RESPONSE_GROUP'];
	$lead_arr['GLH_DISTRICT_ID']=0;
	
	if($lead_arr['GLH_STATUS']==3){
			$lead_arr['GLH_PROSPECT_ON']=date('Y-m-d');
			$lead_arr['GLH_PROSPECT_BY']=$uid;
	}
	$lead_status = $lead_arr['GLH_STATUS'];
		
	/* adding reference -start*/
	if(isset($lead_arr['GLH_CREATED_BY_EMPID']) && $lead_arr['GLH_CREATED_BY_EMPID']!=SALES_DUMMY_ID 
	&& is_authorized_group_list($lead_arr['GLH_CREATED_BY_EMPID'],array(13,31,39))){
			$lead_arr['GLH_LEAD_SOURCE_CODE_PARTNER']=5;
			$cp_lead_code=get_cp_lead_code_for_eid($lead_arr['GLH_CREATED_BY_EMPID']);
		    $lead_arr['GLH_REFERENCE_OF_PARTNER']=$cp_lead_code;
		    $lead_arr['GLH_CREATED_CATEGORY']=3;
	}
	else if(isset($lead_arr['GLH_CREATED_BY_EMPID']) && $lead_arr['GLH_CREATED_BY_EMPID']!=SALES_DUMMY_ID 
	&& is_authorized_group_list($lead_arr['GLH_CREATED_BY_EMPID'],array(14))){
			$lead_arr['GLH_LEAD_SOURCECODE']=7;
			$cp_lead_code=get_cp_lead_code_for_eid($lead_arr['GLH_CREATED_BY_EMPID']);
		    $lead_arr['GLH_REFERENCE_GIVEN']=$cp_lead_code;
		    $lead_arr['GLH_CREATED_CATEGORY']=24;//need to separate as corporate
	}
	/* adding reference -end*/
	$lead_source_code = isset($lead_arr['GLH_LEAD_SOURCECODE'])?(int)$lead_arr['GLH_LEAD_SOURCECODE']:0;
    if((empty($lead_arr['GLH_CREATED_CATEGORY'])) || (in_array($lead_source_code,array(3,4,19,34)))){
    	$lead_arr['GLH_CREATED_CATEGORY'] = 2; /* Created by others */
		if( in_array($lead_source_code,array(3,4,19,34)) ){
			$lead_arr['GLH_CREATED_CATEGORY']=9; /* Event import */
		}else{
			$created_categ = get_lead_created_category($uid);
			if($created_categ!=0){
				$lead_arr['GLH_CREATED_CATEGORY'] = $created_categ;
			}
		}
	}
	if($lead_arr['GLH_CREATED_CATEGORY']!='' and $lead_arr['GLH_CREATED_CATEGORY']!=0){
		$gcc_id	=	$lead_arr['GLH_CREATED_CATEGORY'];
		$res_verified_type	=	execute_my_query("select GCC_VERIFIED_TYPE from gft_lead_create_category where GCC_ID='$gcc_id'");
		if(mysqli_num_rows($res_verified_type)==1 and $row_verified=mysqli_fetch_array($res_verified_type)){
			$lead_arr['GLH_CONTACT_VERIFIED']=	$row_verified['GCC_VERIFIED_TYPE'];
		}
	}
	$lead_arr['GLH_CUST_CITY']=(isset($lead_arr['GLH_CUST_CITY'])?$lead_arr['GLH_CUST_CITY']:'');
	$lead_terr_dtl=find_territory_id_and_incharge(null,$lead_arr['GLH_COUNTRY'],(isset($lead_arr['GLH_CUST_STATECODE'])?$lead_arr['GLH_CUST_STATECODE']:'')
	,$lead_arr['GLH_CUST_PINCODE'],
	$lead_arr['GLH_CUST_CITY'],0,(isset($lead_arr['GLH_LFD_EMP_ID'])?$lead_arr['GLH_LFD_EMP_ID']:''),$lead_arr['GLH_LEAD_TYPE'],
	$lead_arr['GLH_CREATED_BY_EMPID'],$lead_arr['GLH_CREATED_CATEGORY'],$lead_arr['GLH_VERTICAL_CODE']);

	$lead_arr['GLH_LFD_EMP_ID']=(empty($lead_terr_dtl['GLH_LFD_EMP_ID'])?SALES_DUMMY_ID:$lead_terr_dtl['GLH_LFD_EMP_ID']);
	$lead_arr['GLH_TERRITORY_ID']=(empty($lead_terr_dtl['GLH_TERRITORY_ID'])?100:$lead_terr_dtl['GLH_TERRITORY_ID']);
	$lead_arr['GLH_DISTRICT_ID']=(isset($lead_terr_dtl['GLH_DISTRICT_ID'])?$lead_terr_dtl['GLH_DISTRICT_ID']:'');	
	$lead_arr['GLH_LMT_EMP_ID']= $lead_terr_dtl['GLH_LMT_EMP_ID'];
	$lead_arr['GLH_FIELD_INCHARGE']=(isset($lead_arr['GLH_FIELD_INCHARGE'])?$lead_arr['GLH_FIELD_INCHARGE']:SALES_DUMMY_ID); //get_support_incharge($lead_arr['GLH_TERRITORY_ID']);
	$lead_arr['GLH_L1_INCHARGE']=$L1_incharge=get_regional_coordinator($lead_arr['GLH_TERRITORY_ID']); //get_l1_incharge($lead_arr['GLH_TERRITORY_ID']);

    if(empty($lead_arr['GLH_APPROX_TIMETOCLOSE']) or trim($lead_arr['GLH_APPROX_TIMETOCLOSE'])=='' or $lead_arr['GLH_APPROX_TIMETOCLOSE']=='0000-00-00'){
    	$lead_arr['GLH_APPROX_TIMETOCLOSE']=date('Y-m-d',mktime('0','0','0',date('m')+1,date('d'),date('Y')));
    }
	$lead_arr['GLH_BB_UPDATED_BY']='';$lead_arr['GLH_BB_UPDATED_ON']='';
	$lead_arr['GLH_LS_UPDATED_BY']='';$lead_arr['GLH_LS_UPDATED_ON']='';
	$lead_arr['GLH_LS_INTERNAL_UPDATED_BY']='';$lead_arr['GLH_LS_PARTNER_UPDATED_BY']='';
	$lead_arr['GLH_LS_INTERNAL_UPDATED_ON']='';$lead_arr['GLH_LS_PARTNER_UPDATED_ON']='';
	if(isset($lead_arr['GLH_BROADBAND']) and $lead_arr['GLH_BROADBAND']=='Y'){
		$lead_arr['GLH_BB_UPDATED_BY']=$lead_arr['GLH_CREATED_BY_EMPID'];
		$lead_arr['GLH_BB_UPDATED_ON']=date('Y-m-d');}
	/* if(isset($lead_arr['GLH_LEAD_SOURCECODE']) and isset($lead_arr['GLH_REFERREDBY'])){
		$lead_arr['GLH_LS_UPDATED_ON']=$lead_arr['GLH_CREATED_BY_EMPID'];
		$lead_arr['GLH_LS_UPDATED_ON']=date('Y-m-d');
	} */
	if(isset($lead_arr['GLH_LEAD_SOURCE_CODE_PARTNER']) and isset($lead_arr['GLH_REFERENCE_OF_PARTNER'])){
		$lead_arr['GLH_LS_PARTNER_UPDATED_BY']=$lead_arr['GLH_CREATED_BY_EMPID'];
		$lead_arr['GLH_LS_PARTNER_UPDATED_ON']=date('Y-m-d');
	}
	if(isset($lead_arr['GLH_REFERENCE_INTERNAL']) and isset($lead_arr['GLH_LEAD_SOURCE_CODE_INTERNAL'])){
		$lead_arr['GLH_LS_INTERNAL_UPDATED_BY']=$lead_arr['GLH_CREATED_BY_EMPID'];
		$lead_arr['GLH_LS_INTERNAL_UPDATED_ON']=date('Y-m-d');
	}
	/* lead source set */
	if(!isset($lead_arr['GLH_LEAD_SOURCE_CODE_INTERNAL'])){$lead_arr['GLH_LEAD_SOURCE_CODE_INTERNAL']=0;	}
	if(!isset($lead_arr['GLH_LEAD_SOURCE_CODE_PARTNER'])){$lead_arr['GLH_LEAD_SOURCE_CODE_PARTNER']=0;	}
	if(!isset($lead_arr['GLH_LEAD_SOURCECODE'])){$lead_arr['GLH_LEAD_SOURCECODE']=0;	}
	
	if($lead_arr['GLH_LEAD_SOURCE_CODE_INTERNAL']=='0' and $lead_arr['GLH_LEAD_SOURCE_CODE_PARTNER']==0 and $lead_arr['GLH_LEAD_SOURCECODE']==0){
		$lead_arr['GLH_LEAD_SOURCE_CODE_INTERNAL']=13;//
		$lead_arr['GLH_LS_INTERNAL_UPDATED_BY']=$lead_arr['GLH_CREATED_BY_EMPID'];
		$lead_arr['GLH_LS_INTERNAL_UPDATED_ON']=date('Y-m-d');
	}
	else if($lead_arr['GLH_LEAD_SOURCE_CODE_INTERNAL']==13 and ($lead_arr['GLH_LEAD_SOURCECODE']!=0 or $lead_arr['GLH_LEAD_SOURCE_CODE_PARTNER']!=0)){
		$lead_arr['GLH_LEAD_SOURCE_CODE_INTERNAL']=0;
	
	}
	/*lead source end */
	if(isset($lead_arr['GLH_SHOPINFO'])) $lead_arr['GLH_SHOPINFO']=mysqli_real_escape_string_wrapper($lead_arr['GLH_SHOPINFO']);
	$lead_arr['GLH_SPECIAL_REMARKS']=(isset($lead_arr['GLH_SPECIAL_REMARKS'])?mysqli_real_escape_string_wrapper($lead_arr['GLH_SPECIAL_REMARKS']):'');
	if(isset($lead_arr['GLH_CUST_NAME']))$lead_arr['GLH_CUST_NAME']=str_replace("'","",trim($lead_arr['GLH_CUST_NAME']));
	if(trim($lead_arr['GLH_CUST_NAME'])==''){
		$submit_msg="Data is not inserted into gft_lead_hdr";
		$submit_msg_mail="\nData is not inserted into gft_lead_hdr";
		$lead_created_status[0]=false;
		$lead_created_status[1]="";
		$lead_created_status[2]="";
		mail_error_alert("Error found ".$_SERVER['REQUEST_URI']."",json_encode($lead_arr));
		return $lead_created_status;
	}
	if(isset($lead_arr['GLH_DOOR_APPARTMENT_NO']))$lead_arr['GLH_DOOR_APPARTMENT_NO']=mysqli_real_escape_string_wrapper($lead_arr['GLH_DOOR_APPARTMENT_NO']);
	if(isset($lead_arr['GLH_BLOCK_SOCEITY_NAME']))$lead_arr['GLH_BLOCK_SOCEITY_NAME']=mysqli_real_escape_string_wrapper($lead_arr['GLH_BLOCK_SOCEITY_NAME']);
	if(isset($lead_arr['GLH_STREET_DOOR_NO']))$lead_arr['GLH_STREET_DOOR_NO']=mysqli_real_escape_string_wrapper($lead_arr['GLH_STREET_DOOR_NO']);
	if(isset($lead_arr['GLH_CUST_STREETADDR1']))$lead_arr['GLH_CUST_STREETADDR1']=mysqli_real_escape_string_wrapper(trim($lead_arr['GLH_CUST_STREETADDR1']));
	if(isset($lead_arr['GLH_CUST_STREETADDR2']))$lead_arr['GLH_CUST_STREETADDR2']=(trim($lead_arr['GLH_CUST_STREETADDR2'])!=''?mysqli_real_escape_string_wrapper(trim($lead_arr['GLH_CUST_STREETADDR2'])):$lead_arr['GLH_CUST_STREETADDR1']);
	if(isset($lead_arr['GLH_AREA_NAME']))$lead_arr['GLH_AREA_NAME']=mysqli_real_escape_string_wrapper(trim($lead_arr['GLH_AREA_NAME']));
	if(isset($lead_arr['GLH_CUST_CITY']))$lead_arr['GLH_CUST_CITY']=mysqli_real_escape_string_wrapper(trim($lead_arr['GLH_CUST_CITY']));
	if(isset($lead_arr['GLH_CUST_STATECODE']))$lead_arr['GLH_CUST_STATECODE']=mysqli_real_escape_string_wrapper(trim($lead_arr['GLH_CUST_STATECODE']));
	if(isset($lead_arr['GLH_CUST_PINCODE']))$lead_arr['GLH_CUST_PINCODE']=mysqli_real_escape_string_wrapper(trim($lead_arr['GLH_CUST_PINCODE']));
	if(isset($lead_arr['GLH_REFERREDBY']))$lead_arr['GLH_REFERREDBY']=mysqli_real_escape_string_wrapper(trim($lead_arr['GLH_REFERREDBY']));
	if(isset($lead_arr['GLH_AUTHORITY_NAME']))$lead_arr['GLH_AUTHORITY_NAME']=mysqli_real_escape_string_wrapper(trim($lead_arr['GLH_AUTHORITY_NAME']));
	if(isset($lead_arr['GLH_REASON_FOR_PROSPECT_STATUS_CHANGE_DTL']))$lead_arr['GLH_REASON_FOR_PROSPECT_STATUS_CHANGE_DTL']=mysqli_real_escape_string_wrapper(trim($lead_arr['GLH_REASON_FOR_PROSPECT_STATUS_CHANGE_DTL']));
	if(isset($lead_arr['GLH_REASON_FOR_STATUS_CHANGE_DTL']))$lead_arr['GLH_REASON_FOR_STATUS_CHANGE_DTL']=mysqli_real_escape_string_wrapper(trim($lead_arr['GLH_REASON_FOR_STATUS_CHANGE_DTL']));
	$corp_check=false;
	if(in_array($lead_arr['GLH_LEAD_TYPE'],array('3','13'))){
		$corp_check=true;
	}	
	$lead_arr['GLH_LEAD_CODE']=(!isset($lead_arr['GLH_LEAD_CODE'])?'':$lead_arr['GLH_LEAD_CODE']);
	$ref_lead = isset($lead_arr['GLH_REFERENCE_GIVEN'])?$lead_arr['GLH_REFERENCE_GIVEN']:'';
	if( ($lead_arr['GLH_LEAD_TYPE']!='7') && ($from_page!='hq_outlet_sync')  && (!$skip_lead_duplicate_check)){ //to skip dukaan pos lead type
		foreach ($array_cno as $key_val) {
		    $existing_lead_arr = get_lead_code_for_contact_no($key_val,'',$corp_check,$ref_lead,$lead_arr['GLH_LEAD_CODE']);
			$existing_lead_code = isset($existing_lead_arr[0])?$existing_lead_arr[0]:'';
			if($existing_lead_code!=''){
				$old_stat = $existing_lead_arr[1];
				$old_lfd = $existing_lead_arr[2];
				$contact_dtl = get_contact_detail_in_ui($array_conper,$array_cno,$designation,$contact_type,$existing_lead_code);
				$add_msg = update_lead_status_and_lead_owner_for_conflict($existing_lead_code, $lead_arr, $from_page);
				$status_reason = isset($add_msg[1])?$add_msg[1]:'';
				$owner_reason = isset($add_msg[2])?$add_msg[2]:'';
				$ticket_id		=	isset($add_msg[3])?(int)$add_msg[3]:0;
				$lmt_emp_id		=	isset($add_msg[4])?(int)$add_msg[4]:0;
				$lfd_emp_id     =   isset($add_msg[7])?(int)$add_msg[7]:0;
				$old_lead_status     =   isset($add_msg[8])?(int)$add_msg[8]:0;
				if($old_lead_status!=0){
    				if($lfd_emp_id!=0 && $lfd_emp_id<9998){
    			        $lmt_emp_id=$lfd_emp_id;
    			    }else{
    			        $lmt_emp_id=$uid;
    			    }
				}
				save_duplicate_lead_activity_entry($existing_lead_code, $old_stat, $old_lfd, $lead_arr, $contact_dtl,$status_reason, $owner_reason,$sumbit_type,$lmt_emp_id);
				$lead_created_status[0]=false;
				$lead_created_status[1]=$existing_lead_code;
				$lead_created_status[5]=$existing_lead_arr[3];
				$lead_created_status[6]=isset($add_msg[0])?$add_msg[0]:''; //alert message
				$lead_created_status[7]=$key_val; //for info used in alert message
				$lead_created_status[8]=isset($add_msg[5])?$add_msg[5]:0; //existing business type
				$lead_created_status[9]=isset($add_msg[6])?$add_msg[6]:0; //existing created category
				return $lead_created_status;
			}
		}
	}
	
	$i=0;$column_name='';$values='';
	
	foreach($lead_arr as $key => $value){
		if(substr($key, 0,4)!="GLE_"){
			$column_name.=($i!=0?",":"")."$key";
			$value=($value);
			$values.=($i!=0?",":"")."'$value'";
			$i++;
		}
	}
	$contacts_ok = check_for_proper_contact($chk_contact_type, $chk_contact_desig, $designation, $contact_type);
	if(!$contacts_ok) {
		$lead_created_status[0] = false;
		$lead_created_status[1] = "Proprietor contact required";
		return $lead_created_status;
	}
	$GLH_LEAD_CODE='';
	$query1="insert into gft_lead_hdr($column_name) values ($values)";
	$result=execute_my_query($query1,'',$send_mail_alert=true,true);
	if($result){
		$GLH_LEAD_CODE=mysqli_insert_id_wrapper();
		update_status_change_in_ext($GLH_LEAD_CODE, $lead_status, '', $uid);
	}
	
	$column_name_ext='GLE_LEAD_CODE';$values_ext="$GLH_LEAD_CODE";
	foreach($lead_arr as $key => $value){
		if(substr($key, 0,4)=="GLE_"){
			$column_name_ext.=($column_name_ext!=''?",":"")."$key";
			$value=($value);
			$values_ext.=($values_ext!=''?",":"")."'$value'";
		}
	}
	if($GLH_LEAD_CODE!=''){
		$query2="insert into gft_lead_hdr_ext($column_name_ext) values ($values_ext)";
		$result1=execute_my_query($query2);
	}
	$glh_created_emp = $lead_arr['GLH_CREATED_BY_EMPID'];
	if( ($glh_created_emp!='') && ($glh_created_emp!='9999') ){
		$glh_lead_status = $lead_arr['GLH_STATUS'];
		$glh_lead_type	 = $lead_arr['GLH_LEAD_TYPE'];
		$metric_id = 1; //lead
		update_daily_achieved($glh_created_emp,$metric_id,1);
		if($glh_lead_status=='3'){
			$metric_id = 83; //independent prospect
			if( ($glh_lead_type=='3') || (is_array($product_arr) && in_array('300', $product_arr)) ) {
				$metric_id= 84; //hq prospect
			}
		}
		if(in_array($lead_arr['GLH_LEAD_SOURCECODE'],array('7','36'))){
			$metric_id = 4;
		}
		if($metric_id!=1){
			update_daily_achieved($glh_created_emp,$metric_id,1,'',$GLH_LEAD_CODE);
		}
		
		$sel_query1=" select GCA_CP_SUB_TYPE,cgi_incharge_emp_id from gft_cp_info ".
					" join gft_cp_agree_dtl on (gca_lead_code=CGI_LEAD_CODE and gca_cp_agreeno=CGI_CP_AGREENO) ".
					" where CGI_EMP_ID='$glh_created_emp' and GCA_CP_SUB_TYPE in (7,11,10) "; //solution, sales and referral
		$res1 = execute_my_query($sel_query1);
		if($row1 = mysqli_fetch_array($res1)){
			$partner_sub_type = $row1['GCA_CP_SUB_TYPE'];
			$bussines_incharge = $row1['cgi_incharge_emp_id'];
			if($partner_sub_type=='10'){ //referral partner
				update_daily_achieved($bussines_incharge,90,1);
			}else{
				$territory_inchg = get_single_value_from_single_table("gbt_sales_incharge", "gft_business_territory_master", "GBT_TERRITORY_ID", get_territory_for_lead($GLH_LEAD_CODE));
				if($glh_lead_status=='3'){ //propect
					update_daily_achieved($bussines_incharge,85,1);
				}else{
					update_daily_achieved($territory_inchg, $metric_id, 1);
				}
			}
			
		}
	}
	if(!$result){			
		$submit_msg.="Data is not inserted into gft_lead_hdr";
		$submit_msg_mail.="\nData is not inserted into gft_lead_hdr";
		$lead_created_status[0]=false;
		$lead_created_status[1]="";
		$lead_created_status[2]="";
		mail_error_alert("Error found ".$_SERVER['REQUEST_URI']."",json_encode($lead_arr));
		return $lead_created_status;
	}
	
	if($result){
		$contact_group1=(isset($contact_group1)?$contact_group1:'');
		$contact_group2=(isset($contact_group2)?$contact_group2:'');
		$contact_group3=(isset($contact_group3)?$contact_group3:'');
		$check_duplicate = true;
		if($from_page=='hq_outlet_sync' || ($skip_lead_duplicate_check)){
			$check_duplicate = false;
		}
	    $array_contact_id = insert_lead_contact_nos($array_conper,$array_cno,$designation,$GLH_LEAD_CODE,$removecontactid,
								$contact_type,$contact_id='',null,null,null,null,$import_without_activity,true,null,null,
								$contact_group1,$contact_group2,$contact_group3,null,null,null,$ref_lead,$check_duplicate);
		$str_contact_id="";
		if(count($array_contact_id)>0){
 			$str_contact_id=implode(',',$array_contact_id);
 		}
 		/*
		 update the lead type as gft based on the mobile no given in the contact details
	    */
	    /* lead_type 8 config */
	    $set_while_set_lead_type_as_gft=get_samee_const('while_set_lead_type_as_gft');
	    if(!empty($set_while_set_lead_type_as_gft)) $set_while_set_lead_type_as_gft=",".$set_while_set_lead_type_as_gft;
	    
		execute_my_query("update gft_lead_hdr, gft_customer_contact_dtl gc,gft_emp_master set GLH_LEAD_TYPE=8 $set_while_set_lead_type_as_gft " .
		 		" WHERE GLH_LEAD_CODE=$GLH_LEAD_CODE and GCC_LEAD_CODE=GLH_LEAD_CODE AND gcc_contact_type=1 " .
		 		" and GEM_MOBILE=GCC_CONTACT_NO and gem_status='A' and gem_emp_id < 7000 and GLH_LEAD_TYPE!=8 "); 
		execute_my_query("update gft_lead_hdr, gft_customer_contact_dtl gc,gft_emp_master set GLH_LEAD_TYPE=8 $set_while_set_lead_type_as_gft " .
		 		" WHERE GLH_LEAD_CODE=$GLH_LEAD_CODE and GCC_LEAD_CODE=GLH_LEAD_CODE AND gcc_contact_type=4 " .
		 		" and GEM_EMAIL=GCC_CONTACT_NO and gem_status='A' and gem_emp_id < 7000 and GLH_LEAD_TYPE!=8 "); 		
	    insert_lead_fexec_dtl($GLH_LEAD_CODE,$lead_arr['GLH_LFD_EMP_ID'],false);
        insert_intrested_products($GLH_LEAD_CODE,$product_arr);
       
        insert_gft_track_lead_status($GLH_LEAD_CODE,$lead_arr['GLH_CREATED_DATE'],$lead_arr['GLH_APPROX_TIMETOCLOSE'],$lead_arr['GLH_STATUS'],$remarks='');
		if($import_without_activity=='off'){ send_sms_to_new_lead($GLH_LEAD_CODE); }
		if($ref_lead!=''){
			update_reference_count_in_hdr($ref_lead);
		}
		update_call_preferance($GLH_LEAD_CODE);/* update call preference */
		if(!$is_valid_start){
			$mail_log = "Lead Code : $GLH_LEAD_CODE , Received Lead Status : $received_lead_status <br><br>";
			$mail_log .= getStackTraceString();
			send_mail_function("sam-support@gofrugal.com","sam-team@gofrugal.com","Lead status conflict in new lead creation",$mail_log,null,null,null,true);
		}
		$lead_created_status[0]=true;
		$lead_created_status[1]=$GLH_LEAD_CODE;
		$lead_created_status[2]=$lead_arr['GLH_TERRITORY_ID'];
		$lead_created_status[3]=$str_contact_id;
		$lead_created_status[4]=$lead_arr['GLH_LFD_EMP_ID'];
		$lead_created_status[5]=$lead_arr['GLH_LMT_EMP_ID'];
		return $lead_created_status;
	}
	return null;
}
/**
 * @param string $cust_id
 * @param string $lifecycle_ids
 *
 * @return boolean
 */
function check_customer_life_cycle($cust_id,$lifecycle_ids){

	$sql_lead_lifecycle = 	" select GLH_LEAD_CODE from gft_lead_hdr INNER JOIN gft_customer_status_master ON(GLH_STATUS=GCS_CODE) ".
			" where GLH_LEAD_CODE='$cust_id' and GCS_CUST_LIFECYCLE IN($lifecycle_ids)";
	$result_lead_lifecycle = execute_my_query($sql_lead_lifecycle);
	if(mysqli_num_rows($result_lead_lifecycle)>0){
		return true;
	}else{
		return false;
	}
}

/**
 * @param mixed[string] $followup_detail
 * @param string $GLD_VISIT_NATURE
 * @param string $baton_wobbling
 * @param bool $lead_incharge
 *
 * @return void
 */
function process_for_followup_assigned($followup_detail,$GLD_VISIT_NATURE=null,$baton_wobbling='',$lead_incharge=false){
	global $emp_id,$assigned_followup_id; 		
	if( !isset($followup_detail['GCF_ASSIGN_TO']) || !isset($followup_detail['GCF_ASSIGN_BY']) ){
		return;
	}
	if($followup_detail['GCF_ASSIGN_TO']==$followup_detail['GCF_ASSIGN_BY'] or 
		$followup_detail['GCF_ASSIGN_TO']=='' or $followup_detail['GCF_ASSIGN_TO']==0){
		return;
	}
	if(!isset($followup_detail['GCF_ACTIVITY_REF']) or 
	   (isset($followup_detail['GCF_ACTIVITY_REF']) and ($followup_detail['GCF_ACTIVITY_REF']=='' or $followup_detail['GCF_ACTIVITY_REF']=='0'))) {
	   	return;
	}
	$followup_detail['GCF_FOLLOWUP_DETAIL']=(isset($followup_detail['GCF_FOLLOWUP_DETAIL'])?mysqli_real_escape_string_wrapper($followup_detail['GCF_FOLLOWUP_DETAIL']):'');
	$followup_detail['GCF_SMS']=(isset($followup_detail['GCF_SMS'])?mysqli_real_escape_string_wrapper($followup_detail['GCF_SMS']):"");
	$demo_date_arr=explode(' ',$followup_detail['GCF_FOLLOWUP_DATE']);
	if($demo_date_arr[0]==''){
	    return;
	}
	$followup_detail['GCF_FOLLOWUP_DATE']=$demo_date_arr[0];
	$followup_detail['GCF_FOLLOWUP_TIME']=(isset($demo_date_arr[1])?$demo_date_arr[1]:'');
	$followup_detail['GCF_FOLLOWUP_STATUS']=1;
	$followup_detail['GCF_ACTUAL_FOLLOWUP_DATE']=	$followup_detail['GCF_FOLLOWUP_DATE'];
	$i=0;$column_name='';$values='';
	foreach($followup_detail as $key => $value){
		$column_name.=($i!=0?",":"")."$key";
		$values.=($i!=0?",":"")."'$value'";
		$i++;
	}
	$query1="insert into gft_cplead_followup_dtl($column_name) values ($values)"; 
	$result=execute_my_query($query1);
	$followup_id = mysqli_insert_id_wrapper();
	$assigned_followup_id = $followup_id;
	send_followup_mail($followup_detail,$GLD_VISIT_NATURE,(int)$followup_id,$lead_incharge);
	
	$gem_mobile=get_mobileno($followup_detail['GCF_ASSIGN_TO']);
	$cust_contact_dtl=get_mobile_number_customer_with_name($followup_detail['GCF_LEAD_CODE'],$category=111);
	$cinfo=customerContactDetail($followup_detail['GCF_LEAD_CODE']);
	$shop_name=$cinfo['cust_name'];
	$assign_to_name=get_name($followup_detail['GCF_ASSIGN_TO']);
	$assign_by_name=get_name($followup_detail['GCF_ASSIGN_BY']);
	
	$db_sms_content_config=array(
		'Cust_Mobile_With_Name'=> array($cust_contact_dtl),
		'Shop_Name'=>array($shop_name),
		'Customer_Id'=>array($followup_detail['GCF_LEAD_CODE']),
		'Followup_Date'=>array($followup_detail['GCF_FOLLOWUP_DATE']),
		'Followup_Time'=>array($followup_detail['GCF_FOLLOWUP_TIME']),
		'Assigned_By'=>array($assign_by_name),
		'Assigned_To'=>array($assign_to_name),
		'Executive_Number'=>array($gem_mobile),
		'Location'=>array($cinfo['LOCATION']),
		'Vertical'=>array($cinfo['VERTICAL_NAME'])		
	 );
	if($cust_contact_dtl!='' && $gem_mobile!=''){
		$message=get_formatted_content($db_sms_content_config,111);
		$message=htmlentities($message); //1st sms	
		entry_sending_sms($gem_mobile,$message,111,$followup_detail['GCF_ASSIGN_TO'],1,$emp_id,0,null);
	}
	
	$message=get_formatted_content($db_sms_content_config,141);
	$message=htmlentities($message); //2nd sms	
	entry_sending_sms_to_customer(null,$message,$category=141,$followup_detail['GCF_LEAD_CODE'],0,$followup_detail['GCF_ASSIGN_BY'],$send_to_alert=0,$tele_cust_code=null);
	if($followup_detail['GCF_SMS']!='' && $gem_mobile!=''){
		entry_sending_sms($gem_mobile,$followup_detail['GCF_SMS'],111,$followup_detail['GCF_ASSIGN_TO'],0,$followup_detail['GCF_ASSIGN_BY'],0,null);
	}
	if($baton_wobbling!=''){
		$insert_arr['GDQ_REMINDER_TYPE']	= '1'; //Followup
		$insert_arr['GDQ_CREATED_EMP'] 		= $followup_detail['GCF_ASSIGN_BY'];
		$insert_arr['GDQ_BATON_WOBBLING']	= $baton_wobbling;
		$insert_arr['GDQ_CREATED_DATE']		= date('Y-m-d H:i:s');
		$insert_arr['GDQ_LEAD_CODE']		= $followup_detail['GCF_LEAD_CODE'];
		$insert_arr['GDQ_REF_ID'] 			= $followup_id;
		array_update_tables_common($insert_arr, "gft_data_quality", null, null, SALES_DUMMY_ID,null,null, $insert_arr);
	}
	
}//end of assigned to function	



/**
 * @param string $from_emp_id
 * @param string $to_emp_id
 *
 * @return void
 */
function transfer_lead_exec_resigned($from_emp_id,$to_emp_id=null){
	$trans_fr_date=date('Y-m-d');
	if(empty($to_emp_id)) return;
	//$to_emp_id=($to_emp_id!=null?$to_emp_id:"ger_reporting_empid "); 
	
	$query="update  gft_lead_fexec_dtl t1,(SELECT g.*,$to_emp_id FROM " .
			"gft_lead_fexec_dtl g,gft_emp_master,gft_emp_reporting  " .
			" where glf_emp_id=gem_emp_id and glf_status='A' " .
			" and ger_emp_id=gem_emp_id and ger_status='A' and glf_emp_id='$from_emp_id' ) t2 set t1.glf_to_date='$trans_fr_date'," .
			" t1.glf_status='I' where t2.glf_emp_id=t1.glf_emp_id and t1.glf_lead_code=t2.glf_lead_code " .
			" and t1.glf_to_date='0000-00-00' ";
	$result_up=execute_my_query($query,'',true,false);
	$query_ins="insert ignore into gft_lead_fexec_dtl (GLF_EMP_ID,  " .
			"GLF_LEAD_CODE, GLF_FROM_DATE, GLF_TO_DATE, GLF_STATUS)" .
			"(SELECT $to_emp_id , GLF_LEAD_CODE,'$trans_fr_date'," .
			" '0000-00-00', 'A' FROM gft_lead_fexec_dtl g,gft_emp_master,gft_emp_reporting " .
			" where glf_emp_id=gem_emp_id and glf_status='I' and " .
			" ger_emp_id=gem_emp_id and ger_status='A' and glf_to_date='$trans_fr_date' " .
			" and glf_emp_id='$from_emp_id')";	
	$result_ins=execute_my_query($query_ins,'',true,false);
	execute_my_query(" update gft_lead_hdr set glh_lfd_emp_id=$to_emp_id where glh_lfd_emp_id=$from_emp_id ",'',true,false);
	execute_my_query(" update gft_lead_hdr set GLH_FIELD_INCHARGE=$to_emp_id where GLH_FIELD_INCHARGE=$from_emp_id  ",'',true,false);
	execute_my_query(" update gft_lead_hdr set GLH_L1_INCHARGE=$to_emp_id where GLH_L1_INCHARGE=$from_emp_id  " ,'',true,false);
	if($to_emp_id!=null){
		execute_my_query("update gft_track_lead_status set GTL_EMP_ID=$to_emp_id,GTL_ASSIGN_FROM=$from_emp_id " .
				" WHERE GTL_EMP_ID=$from_emp_id AND GTL_MONTH=month(now()) AND GTL_YEAR=year(now())");		
	}
	execute_my_query("update ignore gft_lead_monitors set GLM_EMP_ID=$to_emp_id where GLM_EMP_ID=$from_emp_id AND GLM_MONITOR_TYPE=1"); 
	execute_my_query("delete from gft_lead_monitors where GLM_EMP_ID=$from_emp_id AND GLM_MONITOR_TYPE=1");
}//end of fn

/**
 * @param string $grr_emp_id
 * @param string $grr_date
 * @param string $grr_leave
 * @param string $grr_received
 * @param string $grr_received_date
 * 
 * @return void 
 */

function enter_reporting_dtl($grr_emp_id,$grr_date,$grr_leave,$grr_received,$grr_received_date){
	$grr_date=trim($grr_date);
	$query="select grr_received_date from gft_report_received where grr_emp_id='$grr_emp_id' and grr_date=date('$grr_date')  " ;
	$result=execute_my_query($query);
	$report_received=date('Y:m:d H:i:s');
	if( ($data=mysqli_fetch_array($result)) and mysqli_num_rows($result)>0){
		$report_received=$data['grr_received_date'];
	}else{
		$holyday_report=is_holiday($grr_date);
        $holyday_report=($holyday_report?'Y':'N');
		$str="replace into gft_report_received (grr_emp_id,grr_date,grr_leave,grr_received,grr_received_date,grr_holiday)" .
			" values('$grr_emp_id',date('$grr_date'),'$grr_leave','$grr_received','$grr_received_date','$holyday_report');";
		$result=execute_my_query($str,'',true,false);
	}
}

/**
 * @param string $customer_id
 * @param string $assigned_to
 * @param string $assined_by
 * @param string $demo_date
 * @param string $purpose
 * @param string $ename
 * @param string $emobile_no
 * @param string $cust_name
 * @param string $cust_mobile_no
 * @param string $cust_buss_no
 * @param string $category
 * @param int $amount
 * @param string $GCD_COMPLAINT_ID
 *
 * @return void
 */
function send_sms_to_exec_followup($customer_id,$assigned_to,$assined_by,$demo_date,$purpose,$ename,$emobile_no,$cust_name,$cust_mobile_no,
		$cust_buss_no,$category,$amount,$GCD_COMPLAINT_ID){
	$db_sms_content_config=array(
		'customer_id' => array($customer_id),
		'customer_name' => array($cust_name),
		'customer_mobile'=>array($cust_mobile_no),
		'customer_bussno'=> array ($cust_buss_no),
		'support_id'=>array($GCD_COMPLAINT_ID),
		'amount'=> array ($amount),
	    'employee_name' => array($ename),
		'assigned_date'  => array($demo_date),
		'purpose' => array($purpose) );
	$sms_content=get_formatted_content($db_sms_content_config,$category);
	$sms_content=htmlentities($sms_content);   
	entry_sending_sms($emobile_no,$sms_content,$category,$emp_id=$assigned_to,$status=1,$sender=$assined_by,$send_to_alert=0);
}

/**
 * @param string $customer_id
 * @param string $assigned_to
 * @param string $emp_id
 * @param string $demo_date
 * @param string $purpose
 * @param string $mobile_no
 * @param string $ename
 * @param string $emobile_no
 * @param string $cust_name
 * @param string $cust_mobile_no
 * @param string $cust_buss_no
 * @param string $category
 * @param int $amount
 * @param string $GCD_COMPLAINT_ID
 *
 * @return void
 */
function send_sms_cust_followup($customer_id,$assigned_to,$emp_id,$demo_date,$purpose,$mobile_no,$ename,$emobile_no,$cust_name,
		$cust_mobile_no,$cust_buss_no,$category,$amount,$GCD_COMPLAINT_ID){
	$db_sms_content_config=array(
		'customer_id' => array($customer_id),
		'customer_name' => array($cust_name),
		'amount'=> array ($amount),
		'support_id'=>array($GCD_COMPLAINT_ID),
	    'employee_name' => array($ename),
	    'employee_mobile'=> array($emobile_no),
		'assigned_date'  => array($demo_date),
		'purpose' => array($purpose) );
	$sms_content=get_formatted_content($db_sms_content_config,$category);
	$sms_content=htmlentities($sms_content);
	entry_sending_sms_to_customer(null,$sms_content,$category,$customer_id,$status=0,$sender=$emp_id,$send_to_alert=0,$tele_cust_code='');
	
}

/**
 * @param string $uid
 * @param string $schedule_id
 * @param string $schedule_prev_status
 * @param string $schedule_curr_status
 * @param string $reschedule_dt
 * 
 * @return void
 */
function update_scheduler($uid,$schedule_id,$schedule_prev_status,$schedule_curr_status,$reschedule_dt=''){
	if($schedule_prev_status!=$schedule_curr_status){
		$date_on=date('Y-m-d H:i:s');
		/*
		$update_act="update gft_activity_schedule_dtl set GAS_SCHEDULE_STATUS=$schedule_curr_status," .
				" GAS_LAST_UPDATED_TIME='$date_on', GAS_LAST_UPDATED_BY='$uid' " .
				" where GAS_SCHEDULE_ID='$schedule_id' ";
				*/
		$where_qry	=	'';
		if($reschedule_dt!=''){
			$where_qry	=	", GLD_NEXT_ACTION_DATE='$reschedule_dt' ";
		}
		$update_act="update gft_activity set GLD_SCHEDULE_STATUS=$schedule_curr_status," .
				" GLD_LAST_UPDATED_TIME='$date_on', GLD_LAST_UPDATED_BY='$uid' $where_qry " .
				" where GLD_ACTIVITY_ID='$schedule_id' ";		
		execute_my_query($update_act);
		$plan_chk = " update gft_tomorrow_plan_hdr join gft_tomorrow_plan_next_action_relation on (GTR_PLAN_ID=GTH_ID) ".
					" set GTH_STATUS='$schedule_curr_status' where GTR_REMINDER_TYPE=3 and GTR_REMINDER_ID='$schedule_id' and GTH_STATUS=1 "; //GTH_STATUS=1 for updating only pending reminders
		execute_my_query($plan_chk);
	}
}

/**
 * @param string $uid
 * @param string $schedule_id
 * @param string $schedule_prev_status
 * @param string $schedule_curr_status
 * @param string $rescheduled_dt
 * @param string $baton_pass_quality
 * 
 * @return void
 */
function update_followup($uid,$schedule_id,$schedule_prev_status,$schedule_curr_status, $rescheduled_dt='', $baton_pass_quality=''){
	if($schedule_prev_status!=$schedule_curr_status){
		$date_on=date('Y-m-d H:i:s');
		$where_qry	=	'';
		if($rescheduled_dt!=''){
			$where_qry	=	", GCF_FOLLOWUP_DATE='$rescheduled_dt' ";
		}
		$update_act="update gft_cplead_followup_dtl set GCF_FOLLOWUP_STATUS=$schedule_curr_status," .
				" GCF_LAST_UPDATED_TIME='$date_on', GCF_UPDATED_BY='$uid' $where_qry " .
				" where GCF_FOLLOWUP_ID='$schedule_id' ";
		execute_my_query($update_act);
		if($baton_pass_quality!=''){
			$update_data="update gft_data_quality set GDQ_RATED_EMP='$uid', GDQ_RATED_DATE='$date_on', " .
			" GDQ_RATING_ID=$baton_pass_quality where GDQ_REMINDER_TYPE=1 and GDQ_REF_ID='$schedule_id' ";
			execute_my_query($update_data);
		}
		$plan_chk = " update gft_tomorrow_plan_hdr join gft_tomorrow_plan_next_action_relation on (GTR_PLAN_ID=GTH_ID) ".
					" set GTH_STATUS='$schedule_curr_status' where GTR_REMINDER_TYPE=1 and GTR_REMINDER_ID='$schedule_id' ";
		execute_my_query($plan_chk);
	}
}

/**
 * @param string $email
 * @param string $mob_no
 * @param string $phone_no
 * @param string $contact_name
 * @param string[int] $array_cno
 * @param string[int] $array_conper
 * @param string[int] $designation
 * @param string[int] $contact_type
 * @param string $product
 *
 * @return string
 */
function to_check_dublicate($email,$mob_no,$phone_no,$contact_name,$array_cno,
		$array_conper,$designation,$contact_type,$product=''){
	$cust_id='';$cust_id_al='';
	if($email!=''){
		$query=" SELECT group_concat(distinct gcc_lead_code) cust_id_al FROM gft_customer_contact_dtl WHERE GCC_CONTACT_NO='$email'";
		if($mob_no!='' ){
			$query_add_mob=$query." and gcc_contact_no like '%$mob_no' ";
			$result_add_mob=execute_my_query($query_add_mob,'web_custdateils.php',true,false);
			if($result_add_mob){
				$data_ae=mysqli_fetch_array($result_add_mob);
				$cust_id_al=$data_ae['cust_id_al'];
			}
		}

		if($cust_id_al==''){
			$result=execute_my_query($query,'web_custdateils.php',true,false);
			if($result){
				$data=mysqli_fetch_array($result);
				$cust_id_al=$data['cust_id_al'];
			}
		}

		if($cust_id_al!=''){

			if($product!='' and !is_array($product)){
				$queryproduct_check= "select god_lead_code,concat(gop_product_code,gop_product_skew) from gft_order_hdr, gft_order_product_dtl " .
						"where god_order_no=gop_order_no and god_lead_code in ($cust_id_al) and concat(gop_product_code,gop_product_skew)='$product' ";
			}else {
				$queryproduct_check= "select god_lead_code,concat(gop_product_code,gop_product_skew) from gft_order_hdr, gft_order_product_dtl " .
						"where god_order_no=gop_order_no and god_lead_code in ($cust_id_al)  ";
			}
			$resultproduct_check=execute_my_query($queryproduct_check);
			if(mysqli_num_rows($resultproduct_check)>0){
				if($dataproductcheck=mysqli_fetch_array($resultproduct_check)){
					$cust_id=$dataproductcheck['god_lead_code'];
				}
			}else{
				$cust_id_ar=explode(',',$cust_id_al);
				$cust_id=$cust_id_ar[0];
			}
			$thanksreg="unhide";
			$query="select GCC_CONTACT_NO,gcc_contact_type from gft_customer_contact_dtl where " .
					"gcc_lead_code='$cust_id' and GCC_CONTACT_NO in ('".implode("', '",$array_cno)."') ";
			$res_con_ver=execute_my_query($query);
			if(mysqli_num_rows($res_con_ver)==0){
				insert_lead_contact_nos($array_conper,$array_cno,$designation,$cust_id,$removecontactid=null,$contact_type);
			}else{
				$mob_no_avl=false;$phone_no_avl=false;
				while($data_con_ver=mysqli_fetch_array($res_con_ver)){
					if($data_con_ver['GCC_CONTACT_NO']==trim($mob_no)){
						$mob_no_avl=true;
					}
					if($data_con_ver['GCC_CONTACT_NO']==trim($phone_no)){
						$phone_no_avl=true;
					}
				}
				if($phone_no_avl==false){
					insert_lead_contact_nos(array(1 => $contact_name),array(1 => $phone_no),array(1 => '1'),$cust_id,$removecontactid=null,array(1 => 2));
				}
				if($mob_no_avl==false){
					insert_lead_contact_nos(array(1 => $contact_name),array(1 => $mob_no),array(1 => '1'),$cust_id,$removecontactid=null,array(1 => 1));
				}
			}
			return $cust_id;
		}
	}
	return $cust_id; /* it returns zero */
}

/**
 * @param string $product_code
 * @param string $product_skew
 * @param string $free_edition
 * @param string $cust_id
 * @param string $reffproduct_skew
 * @param string $LIC_HARD_DISK_ID
 * @param boolean $only_free_order
 *
 * @return string
 */
function check_and_place_order($product_code,$product_skew,$free_edition,$cust_id,$reffproduct_skew='',$LIC_HARD_DISK_ID=null,$only_free_order=false){
	/* check in install_dtl */


	if($product_code!='' and (strpos($product_skew,'ST')>0 or $free_edition=='y')){
		$query="select god_order_no, gop_product_code,gop_product_skew from gft_order_product_dtl,gft_order_hdr " .
				" where gop_order_no=god_order_no and god_lead_code=$cust_id " .
				" and gop_product_code='$product_code' and god_order_status='A' and gop_usedqty=0 ";
		if($reffproduct_skew!=''){
			$query.=" and gop_product_skew='$reffproduct_skew'";
		}else if($product_skew!=''){
			$query.=" and GOP_PRODUCT_SKEW='$product_skew' ";
		}
		if($only_free_order){
			$query .= " and GOD_ORDER_AMT=0 ";
		}
		$order_no='';
		$result=execute_my_query($query);
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
		}else{
			$territory_id='';
			$vertical='';

			$query=" select glh_territory_id,glh_vertical_code from gft_lead_hdr where glh_lead_code=$cust_id";
			$result=execute_my_query($query);
			if($data=mysqli_fetch_array($result)){
				$territory_id=$data['glh_territory_id'];
				$vertical=$data['glh_vertical_code'];
			}
			$order_date=date('Y-m-d H:i:s');
			$year=date('y');
			$order_no=get_order_no('D',$year,$territory_id,$emp_id=SALES_DUMMY_ID);
			if($order_no!=''){
				if($product_skew=='' and $product_code==500){
					$get_product_from_vertical=get_product_from_vertical($vertical);
					$product_skew=$get_product_from_vertical.'ST';
					$product_code=substr($product_skew,0,3);
					$product_skew=substr($product_skew,4);
				}else if($product_code==100){
					$product_skew='06.0ST';
				}
				if($product_skew==''){
					return null;
				}
				/* if customer enter into register page  no order for pos software*/
				$query="insert into gft_order_hdr (GOD_ORDER_NO, GOD_LEAD_CODE, GOD_EMP_ID, GOD_ORDER_DATE," .
						" GOD_ORDER_AMT, GOD_RAW_ORDER_AMT, GOD_PAYMENT_CODE, GOD_APPROVEDBY_EMPID," .
						" GOD_APPROVAL_CODE, GOD_ORDER_STATUS, GOD_OFFICIAL_APPROVAL, GOD_REMARKS," .
						" GOD_VALIDITY_DATE, GOD_APPROVED_DATE, GOD_REASON_FOR_DISCOUNT, GOD_REASON_FOR_DISCOUNT_DTL," .
						" GOD_OFFICIAL_REMARKS, god_order_splict, GOD_CREATED_DATE,god_order_type,god_incharge_emp_id,god_order_approval_status)" .
						" values('$order_no', '$cust_id','$emp_id',date('$order_date')," .
						" 0,0,1,1," .
						"1,'A','1','Starter Order'," .
						"date('$order_date'),date('$order_date'),1,'Starer Order'," .
						"'Starter Order',0, '$order_date',1,'$emp_id','2')";
				execute_my_query($query);
				$query="insert into gft_order_product_dtl (GOP_ORDER_NO, GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GOP_SELL_RATE, " .
						"GOP_TAX_RATE, GOP_QTY, GOP_DISCOUNT_AMT, GOP_COMMENTS, " .
						"GOP_SELL_AMT, GOP_TAX_AMT, GOP_USEDQTY, GOP_CP_APPROVED, " .
						"GOP_CP_USEDQTY,  GOP_APPROVAL_QTY, GOP_APPROVAL_BY, GOP_VALIDITY_DATE," .
						" GOP_APPROVED_DATE, GOP_LIST_PRICE, ass_period, gop_start_date," .
						" gop_ass_end_date ) " .
						"values('$order_no','$product_code','$product_skew','0'," .
						"0,1,0,'Starter Order'," .
						"0,0,0,1," .
						"0,1,1,date('$order_date')," .
						"date('$order_date'),0,0,date('$order_date')," .
						"date('$order_date') ) ";
				execute_my_query($query);
				if($reffproduct_skew!='' && (!in_array($product_code, array('705','762')))){
					$query="insert into gft_order_product_dtl (GOP_ORDER_NO, GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GOP_SELL_RATE, " .
							"GOP_TAX_RATE, GOP_QTY, GOP_DISCOUNT_AMT, GOP_COMMENTS, " .
							"GOP_SELL_AMT, GOP_TAX_AMT, GOP_USEDQTY, GOP_CP_APPROVED, " .
							"GOP_CP_USEDQTY,  GOP_APPROVAL_QTY, GOP_APPROVAL_BY, GOP_VALIDITY_DATE," .
							" GOP_APPROVED_DATE, GOP_LIST_PRICE, ass_period, gop_start_date," .
							" gop_ass_end_date ) " .
							"values('$order_no','$product_code','$reffproduct_skew','0'," .
							"0,1,0,'Starter Order'," .
							"0,0,0,1," .
							"0,1,1,date('$order_date')," .
							"date('$order_date'),0,0,date('$order_date')," .
							"date('$order_date') ) ";
					execute_my_query($query);
					execute_my_query("update gft_order_product_dtl set GOP_REFERENCE_ORDER_NO='$order_no' where GOP_ORDER_NO='$order_no' and GOP_PRODUCT_CODE='$product_code' and GOP_PRODUCT_SKEW='$product_skew' ");
				}
			}
		}
		if($order_no!=''){ $order_no.='0001';}
		return $order_no;
	}
	return null;
}

/**
 * @param string[string] $dtls_array
 *
 * @return string[string]
 */
function generate_customer_id_order_no($dtls_array){
	$array_cno=/*. (string[int]) .*/ array();
	$array_conper=/*. (string[int]) .*/ array();
	$designation=/*. (string[int]) .*/ array();
	$contact_type=/*. (string[int]) .*/ array();
	$orderno='';
	$territory_incharge='';

	$k=1;
	if($dtls_array['BUS_PHONE']!=''){
		$array_conper[$k]=$dtls_array['CONTACT_NAME'];
		$array_cno[$k]=trim($dtls_array['BUS_PHONE']);
		$contact_type[$k]='2';
		$designation[$k]='1';
		$k++;
	}
	if($dtls_array['PER_PHONE']!=''){
		$array_conper[$k]=$dtls_array['CONTACT_NAME'];
		$array_cno[$k]=trim($dtls_array['PER_PHONE']);
		$contact_type[$k]='1';
		$designation[$k]='1';
		$k++;
	}
	if($dtls_array['EMAIL']!=''){
		$array_conper[$k]=$dtls_array['CONTACT_NAME'];
		$array_cno[$k]=trim($dtls_array['EMAIL']);
		$contact_type[$k]='4';
		$designation[$k]='1';
	}
	$GLH_LEAD_SOURCECODE=$dtls_array['FIND_US'];
	$GLH_REFERREDBY=$dtls_array['FIND_US'].$dtls_array['DETAILS'];
	$product_arr[0]=$product_code=$dtls_array['productcode'];
	$product_skew=substr($dtls_array['PRODUCT_ID'],3,5);
	$product_skew=substr($product_skew,0,2).'.'.substr($product_skew,2,3);
	$GLH_CREATED_DATE=date('Y-m-d');
	$GLH_TERRITORY_ID='100';
	$vertical_dtl=get_vertical_name($business_type=null,$select=false, $vertical_codes=null,$dtls_array['VERTICAL']);
	$dtls_array['VERTICAL']=$vertical_dtl[0][0];
	$duplicate_enyty=false;
	$cust_id=to_check_dublicate(strtolower(trim($dtls_array['EMAIL'])),trim($dtls_array['PER_PHONE']),$dtls_array['BUS_PHONE'],$dtls_array['CONTACT_NAME'],$array_cno,$array_conper,$designation,$contact_type,$product_code.$product_skew);
	if($cust_id!=0){
		$duplicate_enyty=true;
		$product_arr[0]=$product_code;
		insert_intrested_products($cust_id,$product_arr);
	}else{
		$lead_arr['GLH_CUST_NAME']=$dtls_array['SHOP_NAME'];
		$lead_arr['GLH_LANDMARK']=$dtls_array['GLH_LANDMARK'];
		$lead_arr['GLH_CUST_STREETADDR1']=$dtls_array['ADDRESS'];
		$lead_arr['GLH_CUST_STREETADDR2']=$dtls_array['address_2'];
		$lead_arr['GLH_AREA_NAME']=$dtls_array['area_name'];
		$lead_arr['GLH_CUST_CITY']=$dtls_array['CITY'];
		$lead_arr['GLH_CUST_STATECODE']=$dtls_array['STATE'];
		$lead_arr['GLH_CUST_PINCODE']=$dtls_array['PINCODE'];
		$lead_arr['GLH_STATUS']=8;
		$lead_arr['GLH_COUNTRY']=$dtls_array['COUNTRY'];
		$lead_arr['GLH_LEAD_SOURCECODE']=$dtls_array['FIND_US'];
		$lead_arr['GLH_REFERREDBY']=$dtls_array['FIND_US'].$dtls_array['DETAILS'];
		$lead_arr['GLH_AUTHORITY_NAME']=$dtls_array['CONTACT_NAME'];
		$lead_arr['GLH_VERTICAL_CODE']=$dtls_array['VERTICAL'];
		$lead_arr['GLH_CREATED_CATEGORY']=4;
		$lead_create_status=array_insert_new_lead_db($lead_arr,$product_arr,$array_conper,$array_cno,$designation,$contact_type,$removecontactid=null);
		$cust_id=$lead_create_status[1];
		$territory_id=$lead_create_status[2];
		$territory_incharge=$lead_create_status[4];
	}
	if($product_code!='' and (strpos($product_skew,'ST')>0 ) ){
		$query_chk1="select GID_LIC_ORDER_NO,GID_LIC_FULLFILLMENT_NO,GID_EXPIRE_FOR,GID_INSTALL_ID from gft_install_dtl_new ins where GID_LEAD_CODE=$cust_id and " .
		"GID_LIC_PCODE=$product_code and GID_LIC_PSKEW='$product_skew' and GID_STATUS='A' ";
		if(!empty($dtls_array['ENCRYPTED_HDD_KEY'])) {  $query_chk1.="  and GID_LIC_HARD_DISK_ID='".$dtls_array['ENCRYPTED_HDD_KEY']."' ";}
		$result_chk1=execute_my_query($query_chk1);
		if($result_chk1){
			if(mysqli_num_rows($result_chk1)>0){
				$qdc1=mysqli_fetch_array($result_chk1);
				$orderno= $generated_detail['orderno']=$qdc1['GID_LIC_ORDER_NO'];
				$generated_detail['fullfillment_no']=$qdc1['GID_LIC_FULLFILLMENT_NO'];
				$generated_detail['installation_type']=2;
				$generated_detail['EXPIRY_TYPE']=$qdc1['GID_EXPIRE_FOR'];
				$generated_detail['GID_INSTALL_ID']=$qdc1['GID_INSTALL_ID'];
			}
		}

	}

	$free_edition='Y';
	if($orderno==''){
		$orderno=check_and_place_order($product_code,$product_skew,$free_edition,$cust_id,'',$dtls_array['ENCRYPTED_HDD_KEY']);
		$generated_detail['fullfillment_no']='0001';
		$generated_detail['orderno']=substr($orderno,0,15);
		$generated_detail['installation_type']=1;

	}
	if(!$duplicate_enyty){
		mail_to_know_web_registered_dtl($cust_id,$product_skew,$free_edition);
	}
	$generated_detail['status']='K';
	$generated_detail['orderno']=substr($orderno,0,15);
	$generated_detail['lead_code']=$cust_id;
	$generated_detail['product_code']=$product_code;
	$generated_detail['product_skew']=$product_skew;
	$generated_detail['territory_incharge']=$territory_incharge;
	$generated_detail['err_msg']="";
	return $generated_detail;
}
/**
 * @param string $cust_id
 * @param string $product_code
 * @param string $domain_name
 * @param string $server_id
 * 
 * @return string[string]
 */
function update_domain_name($cust_id,$product_code,$domain_name,$server_id=null){
	$return_array=/*. (string[string]) .*/ array();
			if($domain_name!=''){
				if(!is_proper_sub_domain_name($domain_name)){
					$return_array['error_code']='D002';
					$return_array['error_message']='Domain Name Invalid';
					return $return_array;
				}

					$return_check=check_availability_domain_name($domain_name,$product_code);
					$error_code=isset($return_check['error_code'])?$return_check['error_code']:'0';
				    if($error_code == '0'){
				    /* Tenant space avail and get min tennant id is ready to use */
				    $query_tennant= "SELECT SERVER_ID, TENANT_ID,TENANT_DOMAIN_NAME, TENANT_NAME,GSM_WPOS_ADDR,GSM_PROTOCOL_TYPE FROM gft_tenant_master" .
						" join gft_server_master gs on(GSM_SERVER_ID=SERVER_ID ".($server_id!=null? " and GSM_SERVER_ID=$server_id":" and GSM_IS_PRODUCTION='0' ").") " . 
						" WHERE TENANT_STATUS=0 and TENANT_PRODUCT=$product_code ORDER BY SERVER_ID, TENANT_ID limit 1";
					$result_tennant=execute_my_query($query_tennant);
					if(mysqli_num_rows($result_tennant)!=0){
						if($data_tennant=mysqli_fetch_array($result_tennant)){
							$server_id=$data_tennant['SERVER_ID'];
							$tenant_id=$data_tennant['TENANT_ID'];
							$default_domain_name=$data_tennant['TENANT_DOMAIN_NAME'];
							$tenant_name=$data_tennant['TENANT_NAME'];
							$base_domain_name=$data_tennant['GSM_WPOS_ADDR'];
							$protocol_type=$data_tennant['GSM_PROTOCOL_TYPE'];
							/*update domain name */
							$tenant_query= "update gft_tenant_master set TENANT_DOMAIN_NAME='".mysqli_real_escape_string_wrapper($domain_name)."', TENANT_SAM_ID=$cust_id,TENANT_STATUS=1 " .
							" WHERE SERVER_ID=$server_id and TENANT_ID=$tenant_id and TENANT_PRODUCT=$product_code and TENANT_STATUS=0 ";
							$result_tenant_update=execute_my_query($tenant_query);
							if($result_tenant_update){
								$return_array['tenant_id']=$tenant_id;
								$return_array['server_id']=$server_id;
								$return_array['protocol_type']=$protocol_type;
								$return_array['domain_name_url']=$domain_name.".".$data_tennant['GSM_WPOS_ADDR'];													
							}else{
								/* if domain name not available in case of entry */	
								$return_array['tenant_id']='';
								$return_array['error_message']=get_samee_const('601_Domain_Error_102');
								$return_array['error_code']='601_Domain_Error_102';
							}	
						}
					  }else {
						/*if space not available */
						$return_array['tenant_id']='';
						$return_array['error_message']=get_samee_const('601_Domain_Error_101');
						$return_array['error_code']='601_Domain_Error_101';
					  }
				  }else{
				   		return $return_check;
				  }
				   
					
			}
			
			return $return_array;
}

/**
 * @param int $cust_id
 * @param int $product_code
 * @param string $product_skew
 * @param string $order_no
 * @param string[string] $domain_related_dtl
 * @param boolean $new_lead
 * @param string $email
 * @param string $mobile
 * @param string $validity_date
 * 
 * @return boolean
 */
function update_install_dtl_saas($cust_id,$product_code,$product_skew,$order_no,$domain_related_dtl,$new_lead=false,$email='',$mobile='',$validity_date=''){
	$server_id=(isset($domain_related_dtl['server_id'])?$domain_related_dtl['server_id']:'');
	$tenant_id=(isset($domain_related_dtl['tenant_id'])?$domain_related_dtl['tenant_id']:'');
	$domain_url=isset($domain_related_dtl['domain_name_url'])?$domain_related_dtl['domain_name_url']:'';
	$post_to_saas_server = isset($domain_related_dtl['post_to_saas_server'])?(int)$domain_related_dtl['post_to_saas_server']:1;
	if($tenant_id=='' || $server_id==''){
		return false;
	}
	$order_no=substr($order_no,0,15);
	
		$return_array=array();$da_subscription_period=0;$GPM_CLIENTS=0;$GPM_COMPANYS=0;
		$order_find_query= "SELECT GOD_ORDER_SPLICT,if(GOD_ORDER_SPLICT=1,gco_cust_code,god_lead_code) lead_code," .
							" if(GOD_ORDER_SPLICT=1,GCO_FULLFILLMENT_NO,GOP_FULLFILLMENT_NO) as GOP_FULLFILLMENT_NO, " .
							" if(GOD_ORDER_SPLICT=1,GCO_CUST_QTY,GOP_QTY) as GOP_MONTHS ," .
							" gop_product_code, gop_product_skew,gop_qty,GPM_SUBSCRIPTION_PERIOD, pfm.GPM_HEAD_FAMILY,GPM_REFERER_SKEW," .
							" GPM_CLIENTS,GPM_COMPANYS,GPM_PRODUCT_TYPE " .
						" FROM gft_order_hdr " .
						" JOIN gft_order_product_dtl on(GOP_ORDER_NO=GOD_ORDER_NO and god_order_status='A')" .
						" left join gft_cp_order_dtl on ( god_order_splict=1 and GOP_ORDER_NO=gco_order_no and gop_product_code=gco_product_code and gco_skew=gop_product_skew and gco_cust_code=$cust_id)". 
						" join gft_product_master pm on(gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew and pm.GFT_SKEW_PROPERTY=18) " .
						" join gft_product_family_master pfm on (pm.gpm_product_code=pfm.gpm_product_code)".
						" where ((god_order_splict=false and god_lead_code=$cust_id) or (god_order_splict=1 and gco_cust_code=$cust_id)) and gop_product_code=$product_code " .
						" and gop_order_no= '$order_no' ";
		$saas_skew='';			
		$GID_EXPIRE_FOR='';
		$GOP_FULLFILLMENT_NO='';
		$reffproduct_skew='';
		$head_of_family='';
		$no_of_bills=0;
		$num_months=0;

		$order_find_result=execute_my_query($order_find_query);
		if($order_find_data=mysqli_fetch_array($order_find_result)){
						$order_split=$order_find_data['GOD_ORDER_SPLICT'];
						$saas_skew=$order_find_data['gop_product_skew'];
						$reffproduct_skew=$order_find_data['GPM_REFERER_SKEW'];
						$da_subscription_period=$order_find_data['GPM_SUBSCRIPTION_PERIOD'];
						$head_of_family=$order_find_data['GPM_HEAD_FAMILY'];
						$GID_EXPIRE_FOR=($order_find_data['GPM_PRODUCT_TYPE']==1?1:2);
						$GOP_FULLFILLMENT_NO=$order_find_data['GOP_FULLFILLMENT_NO'];
						$num_months=$order_find_data['GOP_MONTHS'];
		} 
		$query_bill_count=" select GPMA_VALUE from gft_product_master_attributes where GPMA_PRODUCT_CODE=$product_code and GPMA_PRODUCT_SKEW='$saas_skew'  and GPMA_ATTRIBUTE=2 ";
		
		$result_bill_count=execute_my_query($query_bill_count);
		if($data_bill_count=mysqli_fetch_array($result_bill_count)){
			$no_of_bills=$data_bill_count['GPMA_VALUE'];
			if($no_of_bills=='')
				$no_of_bills=0;
		}
		 
		if(!check_for_dublicate_license($order_no,$head_of_family,1)){
			return false ;
		}
		else {
			
		$version_dtl=get_version($product_code,$reffproduct_skew,$select=false,$latest='y');
		$version=(isset($version_dtl[0][0])?$version_dtl[0][0]:'');
		if( ($product_code=='601') or ($product_code=='605') ){
		    $domain_url = "https://$domain_url";
		}
		$number_of_days =  $num_months * $da_subscription_period;
		$validity_value = date("Y-m-d", strtotime("+$number_of_days day", strtotime(date('Y-m-d'))));
		if($validity_date!=""){
		    $validity_value = $validity_date;
		}
		$query_install="insert into gft_install_dtl_new " .
		"(gid_emp_id,gid_order_no,gid_product_code, gid_product_skew," .
		" gid_install_date, gid_product_version,GID_CURRENT_VERSION,GID_STATUS," .
		" GID_LEAD_CODE, GID_FULLFILLMENT_NO, GID_NO_CLIENTS, GID_SALESEXE_ID," .
		" GID_VALIDITY_DATE, GID_GRACE_PERIOD, GID_SUBSCRIPTION_STATUS, GID_HEAD_OF_FAMILY," .
		" GID_LIC_ORDER_NO,GID_LIC_PCODE,GID_LIC_PSKEW,GID_LIC_FULLFILLMENT_NO," .
		" GID_LIC_HARD_DISK_ID, GID_CREATED_TIME,GID_LIC_GEN_TYPE,GID_NO_COMPANYS,GID_NO_BILLS,".
		" GID_EXPIRE_FOR,GID_REF_SERIAL_NO,GID_TENANT_ID,GID_SERVER_ID,GID_CURRENT_LICENSE,".
		" GID_WEB_REPORTER_INSTALL_STATUS,GID_STORE_URL,GID_SERVER_UDPATED_DATE)" .
		" values (".SALES_DUMMY_ID.",'$order_no', '$product_code','$reffproduct_skew'," .
		" date(now()), '$version','$version','A'," .
		" '$cust_id','1','$GPM_CLIENTS',".SALES_DUMMY_ID."," .
    	" '$validity_value' ,'0','Y','$head_of_family'," .
    	" '$order_no','$product_code','$saas_skew',$GOP_FULLFILLMENT_NO," .
    	" '', now(),1,'$GPM_COMPANYS',$no_of_bills,$GID_EXPIRE_FOR,1,'$tenant_id','$server_id','ACTUAL',".
		" '1','$domain_url',now()) ";
		$result_install=execute_my_query($query_install);
		if($result_install){
			$install_id=mysqli_insert_id_wrapper();
			$opcode="{$order_no}{$product_code}{$reffproduct_skew}{1}";
			//update_implementation_status($cust_id,$install_id,$opcode);
		    if($order_find_data['GOD_ORDER_SPLICT']==0){
			execute_my_query(" update gft_order_product_dtl set GOP_USEDQTY=1, GOP_CP_USEDQTY=1 where gop_order_no='$order_no' and gop_product_code=$product_code and gop_product_skew='$reffproduct_skew' and GOP_QTY=1");
			execute_my_query(" update gft_order_product_dtl set GOP_LICENSE_STATUS=8 where gop_order_no='$order_no' ");
		    }else{
				execute_my_query("update " .
							" gft_order_product_dtl opd join gft_cp_order_dtl cpd on (god_order_splict and opd.gop_order_no=cpd.gco_order_no and opd.gop_product_code=cpd.gco_product_code and gop_product_skew=cpd.gco_skew and gco_cust_code=$cust_id)". 
							" join gft_product_master pm on (opd.gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew) " .
							" set GCO_USEDQTY=GCO_CUST_QTY " .
							" where gop_order_no='$order_no' and opd.gop_product_code=$product_code and gop_product_skew='$reffproduct_skew' ");
				execute_my_query(" update gft_product_master set opd.GOP_USEDQTY=select sum(GCO_USEDQTY) where gco_order_no=gop_order_no and opd.gop_product_code=gco_product_code and gco_skew=gop_product_skew group by gco_product_code,gco_skew ) " .
							" where gop_order_no='$order_no' and opd.gop_product_code=$product_code and gop_product_skew='$reffproduct_skew'");
				execute_my_query(" update gft_order_product_dtl set GOP_LICENSE_STATUS=8 where gop_order_no='$order_no' ");
		    }
			execute_my_query(" replace into gft_sms_gateway_info (GSG_ORDER_NO,GSG_PRODUCT_CODE,GSG_PRODUCT_SKEW,GSG_LEAD_CODE,GSG_FULLFILLMENT_NO,GSG_START_DATE,GSG_END_DATE,GSG_SAAS_PLAN,GSG_INSTALL_ID) " .
				" values('$order_no','$product_code','$reffproduct_skew',$cust_id,1,date(now()),adddate(date(now()), Interval ($num_months * $da_subscription_period) day),'$saas_skew','$install_id')");
			$lead_status = 9;  //customer
			if($new_lead){
				$lead_status = 26;  //new lead
			}
			//update_lead_status($cust_id,$lead_status,null,$doc='0000-00-00',null,true);
			update_call_preferance($cust_id);
			$action="new";
			if( ($email!='') && ($mobile!='') ){
				$user_id = 1;
				$user_name = "admin";
				if($product_code=='605'){ //servquick
					$user_name = $email;
				}
				$sel_que = " select GCC_ID from gft_customer_contact_dtl where GCC_CONTACT_NO='$mobile' and GCC_LEAD_CODE='$cust_id' ";
				$sel_res = execute_my_query($sel_que);
				if($row1 = mysqli_fetch_array($sel_res)){
					$mobile_gcc_id = (int)$row1['GCC_ID'];
					save_pos_users($mobile_gcc_id, $install_id, $user_name, '', '5', '', '', 'A', 1, $user_id);
					update_app_to_mobile($mobile_gcc_id, $install_id, '703', '1');
					save_pos_users_company_mapping($mobile_gcc_id, $install_id, $user_id, 1);
				}
			}
			$send_data=($server_id==7 || $post_to_saas_server==0?true:false);
			if($server_id!='7' && $post_to_saas_server!=0){
				$send_data=webposgatewaycall($order_no,$cust_id,$server_id,$tenant_id,$action,$product_code,'new',$email);
			}			
			if($send_data==true){
		 		return true;					
			}else {
				return false;
			}
		}
		else{
			return false;
		}	
	}			
}
/**
 * @param string $cust_id
 * @param string $product_code
 * @param string $product_skew
 * @param string $order_no
 * @param boolean $new_lead
 * @param boolean $only_alert
 * 
 * @return void
 */
function update_install_dtl_alert($cust_id,$product_code,$product_skew,$order_no,$new_lead=false,$only_alert=true){
	
	$order_no=substr($order_no,0,15);

	$return_array=array();$da_subscription_period=0;$GPM_CLIENTS=0;$GPM_COMPANYS=0;
	$order_find_query= "SELECT GOD_ORDER_SPLICT,if(GOD_ORDER_SPLICT=1,gco_cust_code,god_lead_code) lead_code," .
			" if(GOD_ORDER_SPLICT=1,GCO_FULLFILLMENT_NO,GOP_FULLFILLMENT_NO) as GOP_FULLFILLMENT_NO, " .
			" if(GOD_ORDER_SPLICT=1,GCO_CUST_QTY,GOP_QTY) as GOP_MONTHS ," .
			" gop_product_code, gop_product_skew,gop_qty,GPM_SUBSCRIPTION_PERIOD, pfm.GPM_HEAD_FAMILY,GPM_REFERER_SKEW," .
			" GPM_CLIENTS,GPM_COMPANYS,GPM_PRODUCT_TYPE " .
			" FROM gft_order_hdr " .
			" JOIN gft_order_product_dtl on(GOP_ORDER_NO=GOD_ORDER_NO and god_order_status='A')" .
			" left join gft_cp_order_dtl on ( god_order_splict=1 and GOP_ORDER_NO=gco_order_no and gop_product_code=gco_product_code and gco_skew=gop_product_skew and gco_cust_code=$cust_id)".
			" join gft_product_master pm on(gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew) " .
			" join gft_product_family_master pfm on (pm.gpm_product_code=pfm.gpm_product_code)".
			" where ((god_order_splict=false and god_lead_code=$cust_id) or (god_order_splict=1 and gco_cust_code=$cust_id)) and gop_product_code=$product_code " .
			" and gop_order_no= '$order_no' ";
	if($only_alert){
		$order_find_query .= " and pm.GFT_SKEW_PROPERTY=18 ";
	}
	$saas_skew='';
	$GID_EXPIRE_FOR='';
	$GOP_FULLFILLMENT_NO='';
	$reffproduct_skew='';
	$head_of_family='';
	$no_of_bills=0;
	$num_months=0;
	$order_find_result=execute_my_query($order_find_query);
	if($order_find_data=mysqli_fetch_array($order_find_result)){
		$order_split=$order_find_data['GOD_ORDER_SPLICT'];
		$saas_skew=$order_find_data['gop_product_skew'];
		if($only_alert){
			$reffproduct_skew=$order_find_data['GPM_REFERER_SKEW'];			
		}
		$da_subscription_period=$order_find_data['GPM_SUBSCRIPTION_PERIOD'];
		$head_of_family=$order_find_data['GPM_HEAD_FAMILY'];
		$GID_EXPIRE_FOR=($order_find_data['GPM_PRODUCT_TYPE']==1?1:2);
		$GOP_FULLFILLMENT_NO=$order_find_data['GOP_FULLFILLMENT_NO'];
		$num_months=$order_find_data['GOP_MONTHS'];
		$GPM_CLIENTS=$order_find_data['GPM_CLIENTS'];
	}
	if($GOP_FULLFILLMENT_NO==''){
		$GOP_FULLFILLMENT_NO=1;
	}
	if($reffproduct_skew==""){
		$reffproduct_skew=$product_skew;
	}
	$query_bill_count=" select GPMA_VALUE from gft_product_master_attributes where GPMA_PRODUCT_CODE=$product_code and GPMA_PRODUCT_SKEW='$saas_skew'  and GPMA_ATTRIBUTE=1 ";
	$result_bill_count=execute_my_query($query_bill_count);
	if($data_bill_count=mysqli_fetch_array($result_bill_count)){
		$no_of_bills=$data_bill_count['GPMA_VALUE'];
	}
	if(!check_for_dublicate_license($order_no,$head_of_family,1)){
		return;
	}
	$reffproduct_skew = ($reffproduct_skew!="" && $product_code=="705"?"$saas_skew":"$reffproduct_skew");
	$version_dtl=get_version($product_code,$reffproduct_skew,$select=false,$latest='y');
	$version=(isset($version_dtl[0][0])?$version_dtl[0][0]:'');
	$query_install="insert into gft_install_dtl_new " .
			"(gid_emp_id,gid_order_no,gid_product_code, gid_product_skew," .
			" gid_install_date, gid_product_version,GID_CURRENT_VERSION,GID_STATUS," .
			" GID_LEAD_CODE, GID_FULLFILLMENT_NO, GID_NO_CLIENTS, GID_SALESEXE_ID," .
			" GID_VALIDITY_DATE, GID_GRACE_PERIOD, GID_SUBSCRIPTION_STATUS, GID_HEAD_OF_FAMILY," .
			" GID_LIC_ORDER_NO,GID_LIC_PCODE,GID_LIC_PSKEW,GID_LIC_FULLFILLMENT_NO," .
			" GID_LIC_HARD_DISK_ID, GID_CREATED_TIME,GID_LIC_GEN_TYPE,GID_NO_COMPANYS,GID_NO_BILLS,".
			" GID_EXPIRE_FOR,GID_REF_SERIAL_NO,GID_TENANT_ID,GID_SERVER_ID)" .
			" values (".SALES_DUMMY_ID.",'$order_no', '$product_code','$reffproduct_skew'," .
			" date(now()), '$version','$version','A'," .
			" '$cust_id','1','$GPM_CLIENTS',".SALES_DUMMY_ID."," .
			" adddate(date(now()), Interval ($num_months * $da_subscription_period) day) ,'0','Y','$head_of_family'," .
			" '$order_no','$product_code','$saas_skew',$GOP_FULLFILLMENT_NO," .
			"'', now(),1,'$GPM_COMPANYS',$no_of_bills,$GID_EXPIRE_FOR,1,'','')";
	$result_install=execute_my_query($query_install);
	if($result_install){
		$install_id=mysqli_insert_id_wrapper();
		//$opcode="{$order_no}{$product_code}{$reffproduct_skew}{1}";
		//update_implementation_status($cust_id,$install_id,$opcode);
		if($product_code=='708'){
			$product_master = get_product_master_dtl($product_code, $reffproduct_skew);
			if( isset($product_master[7]) && ($product_master[7]=='Y') ){
				$sql_res = execute_my_query("select GID_VALIDITY_DATE from gft_install_dtl_new where GID_LEAD_CODE='$cust_id' and GID_LIC_PCODE=703 and GID_STATUS!='U'");
				if($sql_row = mysqli_fetch_array($sql_res)){
					$wn_validity = $sql_row['GID_VALIDITY_DATE'];
					execute_my_query("update gft_install_dtl_new set GID_VALIDITY_DATE='$wn_validity' where GID_INSTALL_ID='$install_id'");
				}
			}
			$upd1 = " update gft_install_dtl_new join gft_emp_master on (GEM_LEAD_CODE=GID_LEAD_CODE) set GID_VALIDITY_DATE='2030-12-31' where GID_INSTALL_ID='$install_id' ";
			execute_my_query($upd1);
		}
		if($product_code=='705'){ // For myPulse the quantity update not required.
		    return;
		}
		if($order_find_data['GOD_ORDER_SPLICT']==0){
			execute_my_query(" update gft_order_product_dtl set GOP_USEDQTY=1, GOP_CP_USEDQTY=1 where gop_order_no='$order_no' and gop_product_code=$product_code and gop_product_skew='$reffproduct_skew' and GOP_QTY=1");
		}else{
			execute_my_query("update " .
					" gft_order_product_dtl opd join gft_cp_order_dtl cpd on (god_order_splict and opd.gop_order_no=cpd.gco_order_no and opd.gop_product_code=cpd.gco_product_code and gop_product_skew=cpd.gco_skew and gco_cust_code=$cust_id)".
					" join gft_product_master pm on (opd.gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew) " .
					" set GCO_USEDQTY=GCO_CUST_QTY " .
					" where gop_order_no='$order_no' and opd.gop_product_code=$product_code and gop_product_skew='$reffproduct_skew' ");
			execute_my_query(" update gft_product_master set opd.GOP_USEDQTY=select sum(GCO_USEDQTY) where gco_order_no=gop_order_no and opd.gop_product_code=gco_product_code and gco_skew=gop_product_skew group by gco_product_code,gco_skew ) " .
					" where gop_order_no='$order_no' and opd.gop_product_code=$product_code and gop_product_skew='$reffproduct_skew'");
		}
		if($only_alert){
			execute_my_query("update gft_sms_gateway_info set GSG_INSTALL_ID='$install_id' where GSG_LEAD_CODE='$cust_id'");
		}
		insert_intrested_products($cust_id,$product_arr=array($product_code));
		update_call_preferance($cust_id);
	}
}

/**
 * @return void
 */
function update_letter_recieved_acknowledgement(){		
		if((string)$_POST['install_id']!='' &&  isset($_POST['asa_letter_received'])  && (string)$_POST['asa_letter_ack_status']!=(string)$_POST['asa_letter_received']){
			$ack_status=$_POST['asa_letter_received'];
			$install_id=$_POST['install_id'];
			$asa_letter_sent_date=$_POST['asa_letter_sent_date'];
			$update_install_dtl="update gft_install_dtl_new set GID_ASA_LETTER_ACK='$ack_status' where gid_install_id=$install_id ";
			execute_my_query($update_install_dtl);
			$update_asa_letter_sent="update gft_asa_letter_dispatch set GSA_DISPATCH_AUDIT='$ack_status' where GSL_DISPATCH_DATE='$asa_letter_sent_date' and GSL_INSTALL_ID=$install_id ";
			execute_my_query($update_asa_letter_sent);
		}
}
/**
 * @return int
 */
function enter_gft_website_hit_register(){
		global $cust_id,$GLH_CREATED_CATEGORY,$product_code,$duplicate_entry,$GLH_VERTICAL_CODE,$device_type;
		$ntimes_visited=0;
		$table_name_dbhit="gft_download_page_hit";
		$column_dbhit['GDP_LEAD_CODE']=$cust_id;
	    $column_dbhit['GDP_CREATE_CATEGORY']=$GLH_CREATED_CATEGORY;
	    $column_dbhit['GDP_HIT_DATE']=date('Y-m-d H:i:s');
	    $column_dbhit['GDP_PRODUCT_CODE']=$product_code;
	    $column_dbhit['GDP_REGISTRATION_DEVICE']=$device_type;	    
	    if($duplicate_entry==true){
	    		$query_chk_created_date="select glh_lead_code from gft_lead_hdr where glh_lead_code=$cust_id and date(glh_created_date)=date(now()) ";
	    		$result_chk_created_date=execute_my_query($query_chk_created_date);
	    		if(mysqli_num_rows($result_chk_created_date)==1){
	    			$duplicate_entry=false;
	    		}
	    }
	    $campaign_source = ( isset($_REQUEST['campaign_source']) && (string)$_REQUEST['campaign_source']!='undefined' )?(string)$_REQUEST['campaign_source']:'';
	    $gclid = (isset($_REQUEST['gclid']) and (string)$_REQUEST['gclid']!='undefined') ? (string)$_REQUEST['gclid'] : '';
	    $column_dbhit['GDP_EXISTING_LEAD']=($duplicate_entry==false?'N':'Y');
	    $column_dbhit['GDP_BUSINESS_ID']=(isset($_REQUEST['business'])?(string)$_REQUEST['business']:'1');
	    $column_dbhit['GDP_VERTICAL_CODE']=$GLH_VERTICAL_CODE;
	    $paid_campaign_flag = ( ($gclid!='') || in_array($campaign_source, array('capterra')) ) ?'Y':'N';
	    $column_dbhit['GDP_PAID_CAMPAIGN'] = $paid_campaign_flag;
	    $column_dbhit['GDP_PAID_CAMPAIGN_ID']=$gclid;
	    $column_dbhit['GDP_IP_ADDRESS']=$_SERVER['REMOTE_ADDR'];
	    
	    $column_dbhit['GDP_CAMPAIGN_NAME'] 		= ( isset($_REQUEST['campaign_name']) && (string)$_REQUEST['campaign_name']!='undefined' )?(string)$_REQUEST['campaign_name']:'';
	    $column_dbhit['GDP_CAMPAIGN_SOURCE']	= $campaign_source;
	    $column_dbhit['GDP_CAMPAIGN_KEYWORD'] 	= ( isset($_REQUEST['campaign_keyword']) && (string)$_REQUEST['campaign_keyword']!='undefined' )?(string)$_REQUEST['campaign_keyword']:'';
	    $column_dbhit['GDP_CAMPAIGN_MEDIUM'] 	= ( isset($_REQUEST['campaign_medium']) && (string)$_REQUEST['campaign_medium']!='undefined' )?(string)$_REQUEST['campaign_medium']:'';
	    
	    $ccid=((isset($_REQUEST['ccid']) and (string)$_REQUEST['ccid']!='' and is_numeric((string)$_REQUEST['ccid']))?(string)$_REQUEST['ccid']:'');
	    $campaign_id=((isset($_REQUEST['campaign_id']) and (string)$_REQUEST['campaign_id']!='' and is_numeric((string)$_REQUEST['campaign_id']))?(string)$_REQUEST['campaign_id']:'');
	 	$column_dbhit['GDP_CONTACT_ID']=$ccid;
		$column_dbhit['GDP_CAMPAIGN_ID']=$campaign_id;
		array_update_tables_common($column_dbhit,$table_name_dbhit,null,null,9999,$remarks=null,$table_column_iff_update=null,$column_dbhit);
	    if($duplicate_entry=='Y'){
	    	$result=execute_my_query("select count(*) cnt from gft_download_page_hit where GDP_LEAD_CODE=$cust_id ");
	    	$qd=mysqli_fetch_array($result);
	    	$ntimes_visited=$qd['cnt'];
	    }
	    
	 
	    	$table_key_arr=/*. (mixed[string]) .*/ array();
		$update_column=/*. (mixed[string]) .*/ array();
	    	$table_name="gft_lead_hdr";
	    if($paid_campaign_flag=='Y'){	
	    	$update_column['GLH_PAID_CAMPAIGN']=date('Y-m-d'); }
	     else{
	     	$update_column['GLH_ORGANIC_SEARCH_VISIT']=date('Y-m-d'); 
	     }	
	    	$table_key_arr['GLH_LEAD_CODE']=$cust_id;
	    	array_update_tables_common($update_column,$table_name,$table_key_arr,null,9999,$remarks=null,$table_column_iff_update=null,null);
	    /* Insert number of outlet selected by user during the registration	*/
	    $numberOfStores	=	isset($_REQUEST['numberOfStores'])?(int)$_REQUEST['numberOfStores']:0;
	    if($numberOfStores>0){
	    	$insert_arr['GLD_LEAD_CODE'] = "$cust_id";
	    	$insert_arr['GLD_NO_OF_OUTLET'] = "$numberOfStores";
	    	$insert_arr['GLD_CREATED_ON'] = date("Y-m-d H:i:s");
	    	array_insert_query("gft_lead_outlet_dtl", $insert_arr);
	    }
	    return $ntimes_visited;
}

/* NOT USED
 * @param int $region_id
 * @param int $terr_id
 * 
 * @return boolean
 *
function is_territory_id_in($region_id,$terr_id){
	    if(is_array($region_id)){
	    	$region_id=implode(',',$region_id);
	    }
		$query="SELECT * FROM b_map_view b where region_id in ($region_id) and terr_id =$terr_id";
		$result=execute_my_query($query);
		if(mysqli_num_rows($result)>0){
			return true;
		}
		return false;
}
*/

/**
 * @param string $lead_code
 * @param string $emp_id
 * @param string $audittypeid
 * 
 * @return string
 */
function send_recent_audit_dtl($lead_code,$emp_id,$audittypeid){
	$audit_id='';
	$query_get_max="select max(GAH_AUDIT_ID) audit_id from  gft_audit_hdr where GAH_AUDIT_TYPE=$audittypeid and GAH_LEAD_CODE=$lead_code and GAH_AUDIT_BY=$emp_id and date(GAH_DATE_TIME)=date(now()) ";
	$result_get_max=execute_my_query($query_get_max);
	if($qd=mysqli_fetch_array($result_get_max)){
		$audit_id=$qd['audit_id'];
	}

	if($audit_id==''){
		return '';
	}

	$query="select GAQ_GROUP_NAME,GAQ_QUESTION_ID,GAQ_QUESTION_TYPE,GAD_AUDIT_ANS,GAQ_AVAL_ANSWER  from gft_audit_question_master qm " .
		  "left join gft_audit_question_group_master gm on (gm.GAQ_GROUP_ID=qm.GAQ_GROUP_ID)" .
		  "left join gft_audit_question_group_map_master gmm on (gmm.GAQ_AUDIT_ID=GAQ_AUDIT_TYPE and GAQ_QGROUP_ID=gm.GAQ_GROUP_ID) " .
		  " JOIN gft_audit_hdr ah ON (GAH_AUDIT_TYPE=GAQ_AUDIT_TYPE  and GAH_AUDIT_ID=$audit_id) " .
		  " join gft_audit_dtl ad on (GAH_AUDIT_ID=GAD_AUDIT_ID and GAD_AUDIT_QID=GAQ_QUESTION_ID )".
		  "where GAH_LEAD_CODE=$lead_code and GAH_AUDIT_BY=$emp_id and date(GAH_DATE_TIME)=date(now()) and qm.GAQ_STATUS='A' ".
		 ($audittypeid!='' ?" and GAQ_AUDIT_TYPE in ($audittypeid) ":"")." order by GAQ_QORDER_BY,GAQ_ORDER_BY,GAH_DATE_TIME desc ";
	$result=execute_my_query($query);
	$group_name='';
	if(mysqli_num_rows($result)<0){
		return '';
	}
	
	$result_audit_name=execute_my_query("select group_concat(GAT_AUDIT_DESC) audit_name from gft_audit_type_master where  GAT_AUDIT_ID in ($audittypeid) 	");
	$qd=mysqli_fetch_array($result_audit_name);
	$audit_name=$qd['audit_name'];
	
	$content='<table border=1><tr><td colspan="3"> '.$audit_name.'</td></tr>';
	$row_num=1;
	while($data=mysqli_fetch_array($result)){
		if($data['GAQ_GROUP_NAME']!=$group_name){		
		$group_name=$data['GAQ_GROUP_NAME'];
		$row_num=1;
$content.=<<<END
		<tr style="background-color: #0971BB; height:20px;">
		<td colspan='3' style="FONT-SIZE: 14px; color: #ffffff; padding-left :5px; text-align: left;">$group_name</td></tr>
END;
		}
$content.=<<<END
<tr><td>$row_num</td><td style="FONT-SIZE: 14px; color: blue;" nowrap>{$data['GAQ_QUESTION_TYPE']}</td>
<td nowrap>{$data['GAD_AUDIT_ANS']}</td></tr>		 
END;
	$row_num++;
	}//end of while
	$content.='</table>';
	return $content;
}//end of function

/**
 * @param string $GLH_CREATED_CATEGORY
 * 
 * @return int
 */
function edm_tracker($GLH_CREATED_CATEGORY){
		$ntimes_visited_edm=0;
		if(isset($_REQUEST['ccid']) and (string)$_REQUEST['ccid']!='' and isset($_REQUEST['campaign_id']) and (string)$_REQUEST['campaign_id']!='' 
		and is_numeric($_REQUEST['ccid'])  and is_numeric($_REQUEST['campaign_id']) ){
			$ccid=$_REQUEST['ccid'];
			$campaign_id=$_REQUEST['campaign_id'];
			$query=" select GER_NO_STARTER,GER_NO_TRIAL,GER_NO_CALL_BACK,GER_NO_REQUEST_DEMO,GER_NO_CONTACT_US," .
					" (GER_NO_STARTER + GER_NO_TRIAL + GER_NO_CALL_BACK + GER_NO_REQUEST_DEMO + GER_NO_CONTACT_US) ncontacted " .
					" from gft_edm_read_dtl where GER_CONTACT_ID=$ccid and GER_CAMPAIGN_ID=$campaign_id ";
			$result=execute_my_query($query);
			while($qdata=mysqli_fetch_array($result)){
				$table_name='GFT_EDM_READ_DTL';
				$update_column=/*. (mixed[string]) .*/ array();
				$table_key_arr['GER_CONTACT_ID']=$ccid;
				$table_key_arr['GER_CAMPAIGN_ID']=$campaign_id;
				$ntimes_visited_edm=$qdata['ncontacted']+1;
				switch ($GLH_CREATED_CATEGORY){
					
					case '4':
					case '16': /* starter */
						$update_column['GER_NO_STARTER']=$qdata['GER_NO_STARTER']+1; 
						array_update_tables_common($update_column,$table_name,$table_key_arr,null,null,$remarks=null,null,null);
						execute_my_query("update gft_event_master_evaluate t1,(select count(*) cnt ,GER_CAMPAIGN_ID from gft_edm_read_dtl where GER_CAMPAIGN_ID=$campaign_id  and GER_NO_STARTER>0 group by GER_CAMPAIGN_ID ) t2 " .
						"set GEM_NO_STARTER=cnt where gem_event_id=GER_CAMPAIGN_ID ");
						break;
					
					case '14':
					case '15': /* trial edition */
					$update_column['GER_NO_TRIAL']=$qdata['GER_NO_TRIAL']+1; 
					array_update_tables_common($update_column,$table_name,$table_key_arr,null,null,$remarks=null,null,null);
					execute_my_query("update gft_event_master_evaluate t1,(select count(*) cnt ,GER_CAMPAIGN_ID from gft_edm_read_dtl where GER_CAMPAIGN_ID=$campaign_id  and GER_NO_TRIAL>0 group by GER_CAMPAIGN_ID ) t2 " .
						"set GEM_NO_TRIAL=cnt where gem_event_id=GER_CAMPAIGN_ID ");
					break;
					
					case '6':
					case '17': /* call back */
						$update_column['GER_NO_CALL_BACK']=$qdata['GER_NO_CALL_BACK']+1; 
						if($qdata['GER_NO_CALL_BACK']==0){
							$update_column['GER_NO_CALL_BACK_TIME']=date('Y-m-d');
						}
						array_update_tables_common($update_column,$table_name,$table_key_arr,null,null,$remarks=null,null,null);
						execute_my_query("update gft_event_master_evaluate t1,(select count(*) cnt ,GER_CAMPAIGN_ID from gft_edm_read_dtl where GER_CAMPAIGN_ID=$campaign_id  and GER_NO_CALL_BACK>0 group by GER_CAMPAIGN_ID ) t2 " .
						"set GEM_NO_CALLBACK_REGISTERED=cnt where gem_event_id=GER_CAMPAIGN_ID ");
					break;
					
					case '10':
					case '18': /* demo */
						$update_column['GER_NO_REQUEST_DEMO']=$qdata['GER_NO_REQUEST_DEMO']+1; 
						array_update_tables_common($update_column,$table_name,$table_key_arr,null,null,$remarks=null,null,null);
						execute_my_query("update gft_event_master_evaluate t1,(select count(*) cnt ,GER_CAMPAIGN_ID from gft_edm_read_dtl where GER_CAMPAIGN_ID=$campaign_id  and GER_NO_REQUEST_DEMO>0 group by GER_CAMPAIGN_ID ) t2 " .
							"set GEM_NO_REQUEST_DEMO=cnt where gem_event_id=GER_CAMPAIGN_ID ");
					break;
					
					case '13':
					case '19': /*contact us */
						$update_column['GER_NO_CONTACT_US']=$qdata['GER_NO_CONTACT_US']+1; 
						array_update_tables_common($update_column,$table_name,$table_key_arr,null,null,$remarks=null,null,null);
						execute_my_query("update gft_event_master_evaluate t1,(select count(*) cnt ,GER_CAMPAIGN_ID from gft_edm_read_dtl where GER_CAMPAIGN_ID=$campaign_id  and GER_NO_CONTACT_US>0 group by GER_CAMPAIGN_ID ) t2 " .
							"set GEM_NO_CONTACT_US=cnt where gem_event_id=GER_CAMPAIGN_ID ");
					break;
									
				}//end of switch
				execute_my_query("update gft_event_master_evaluate set GEM_TOTAL_CONVERSION=(GEM_NO_STARTER + GEM_NO_TRIAL +GEM_NO_CALLBACK_REGISTERED+GEM_NO_REQUEST_DEMO+GEM_NO_CONTACT_US) WHERE gem_event_id=$campaign_id ");
			}/* end of while*/
			
		}//end of ccid/campaign id 
			
		
		return $ntimes_visited_edm;
}/* end of function */

/**
 * @param string $customer_id
 * @param string $product_code
 * @param string $REQUEST_PURPOSE_ID
 * @param mixed[] $response_msg
 * @param mixed $request_msg
 * 
 * @return void
 */
function insert_web_request_log($customer_id,$product_code,$REQUEST_PURPOSE_ID,$response_msg,$request_msg=null){
			
	$micro_sec_diff=getDeltaTime();
	$table_name='gft_lic_request';
	$insert_arr=/*. (mixed[string]) .*/ array();
	$insert_arr['GLC_FROM_ONLINE']='Y';
	$insert_arr['GLC_ONLINE_CONTENT']=mysqli_real_escape_string_wrapper(json_encode($_REQUEST));
	$insert_arr['GLC_DECRYPTED_CONTENT']=mysqli_real_escape_string_wrapper(json_encode($_REQUEST));
	if($request_msg!=null){
		$insert_arr['GLC_ONLINE_CONTENT']=mysqli_real_escape_string_wrapper(json_encode($request_msg));
		$insert_arr['GLC_DECRYPTED_CONTENT']=mysqli_real_escape_string_wrapper(json_encode($request_msg));
	}
	$insert_arr['GLC_REQUEST_TIME']=date('Y-m-d H:i:s');
	$insert_arr['GLC_RETURN_DATA']=(isset($response_msg)?mysqli_real_escape_string_wrapper(json_encode($response_msg)):'');
	$insert_arr['GLC_ERROR_MESSAGE']=(isset($response_msg['error_message'])?mysqli_real_escape_string_wrapper((string)$response_msg['error_message']):'');
	$insert_arr['GLC_STATUS']=(isset($response_msg['error_message'])?'S':'F');
	$insert_arr['GLC_LEAD_CODE']=$customer_id;
	$insert_arr['GLC_IP_ADDRESS']=isset($_SERVER['REMOTE_ADDR'])?(string)$_SERVER['REMOTE_ADDR']:'';
	$insert_arr['GLC_REQUEST_PURPOSE_ID']=$REQUEST_PURPOSE_ID;
	$insert_arr['GLC_PROCESSING_TIME']=$micro_sec_diff;
	$insert_arr['GLC_PRODUCT_KEY']=$product_code;
	array_update_tables_common($insert_arr,$table_name,null,null,SALES_DUMMY_ID,$remarks=null,null,$insert_arr);
	
}

/**
 * @param int $terri_id
 * @param boolean $gft_emp_only
 * @param int $stage
 * @param string $created_category
 * @param string $created_by
 *
 * @return string
 */
function get_terr_incharge($terri_id,$gft_emp_only=false,$stage=1,$created_category=null,$created_by=null){
	
	$incharge_category=''; /* by default */
	if($created_category!=null){
			$query_lc="select GCC_INITIAL_LEAD_INCHARGE_ASSIGN_TO from  gft_lead_create_category lc where  GCC_ID=$created_category ";
			$result_lc=execute_my_query($query_lc);
			if(mysqli_num_rows($result_lc)==1){
				$qd_lc=mysqli_fetch_array($result_lc);
				$incharge_category=$qd_lc['GCC_INITIAL_LEAD_INCHARGE_ASSIGN_TO'];
			}else{
				$incharge_category='X';
			}
	}
	
	if($incharge_category=='X' and $created_by!=null and  $created_by!=SALES_DUMMY_ID and is_authorized_group_list($created_by,array(5,27)) )	{
		/* Sales Employee Created Lead */
		 $incharge=$created_by;
		 return $incharge;
	}else if($created_by!=null and is_authorized_group_list($created_by,array(13,31,39)) and ($incharge_category=='X' or $incharge_category=='') ){/* Partner groups */
		 /*check he is a solution partner or not */
		 $cp_lead_code=get_cp_lead_code_for_eid($created_by);
		 $cp_emp_id=get_cp_emp_id_for_leadcode($cp_lead_code);
		 if(get_partner_type($cp_lead_code)==7){	
			$incharge=$cp_emp_id;
		 } else{
			$incharge_arr=get_sales_incharge_of_non_employee_group($cp_emp_id);
			$incharge=(isset($incharge_arr['emp_id'])?$incharge_arr['emp_id']:'');
		 }
		 if($incharge!=null) return $incharge;
	}	
			
	$query_incharge_emp_id="SELECT GBT_SALES_INCHARGE,GBT_LMT_INCHARGE,GBT_PARTNER_QUALIFY_INCHARGE  FROM gft_business_territory_master " .
			" join gft_emp_master em on (gem_emp_id=gbt_sales_incharge and gem_status='A') " .
			" where GBT_TERRITORY_ID=$terri_id  and GEM_OFFICE_EMPID!=0 ";
		
	$result_emp_id=execute_my_query($query_incharge_emp_id,'common_util.php',true,false,2);
	$incharge='';
	if($data=mysqli_fetch_array($result_emp_id)){
		if($stage==1 and $incharge_category!=''){
			/*initial Stage*/
			if($incharge_category=='L' and !empty($data['GBT_LMT_INCHARGE']) and $data['GBT_LMT_INCHARGE']!=SALES_DUMMY_ID ){
				$incharge=$data['GBT_LMT_INCHARGE'];
				return $incharge;
			}
			else if($incharge_category=='P' and !empty($data['GBT_PARTNER_QUALIFY_INCHARGE']) and $data['GBT_PARTNER_QUALIFY_INCHARGE']!=SALES_DUMMY_ID ){
				$incharge=$data['GBT_PARTNER_QUALIFY_INCHARGE'];
				return $incharge;
			}
			else if($incharge_category=='F' and !empty($data['GBT_SALES_INCHARGE']) and $data['GBT_SALES_INCHARGE']!=SALES_DUMMY_ID){
				$incharge=$data['GBT_SALES_INCHARGE'];
				return $incharge;
			}else if($incharge_category=='X' or $incharge_category==''){
				$incharge=(is_authorized_group_list($created_by,array(5,27))?$created_by:$data['GBT_SALES_INCHARGE']);
	 			return $incharge;			
			}
		}else {
			$incharge=$data['GBT_SALES_INCHARGE'];
			return $incharge;
		}
		
	}
	/*
	 * Need to avoid we have on trace of emp id ....in  gft_business_territory_master 
	 * 
	 */
	if($incharge==null or $incharge==0){
		$query_emp_id="SELECT get_emp_id, GET_STATUS FROM gft_emp_territory_dtl, gft_emp_master " .
				" where get_emp_id=gem_emp_id and gem_status='A' " .
				" and get_status='A' and get_work_area_type='2' " .
				" and GET_TERRITORY_ID =$terri_id and GEM_OFFICE_EMPID!=0 ";
		$result_emp_id=execute_my_query($query_emp_id,'common_util.php',true,false,2);
		if($data=mysqli_fetch_array($result_emp_id)){
			$incharge=$data['get_emp_id'];
		}	
	}
	return $incharge;


}//end of fuction 

/**
 * @param string $district_id
 * @param string $role
 *
 * @return string
 */
function get_incharge_of_district($district_id,$role=null){
		 	 /* Not using now. earlier if we used */
			 $query=" SELECT gpm_territory_id,gbt_sales_incharge,ger_reporting_empid,gem_role_id FROM b_p_map_view 
			 join gft_business_territory_master bt on (GBT_TERRITORY_ID=GPM_TERRITORY_ID) 
			 join gft_emp_reporting emp_r on (ger_emp_id=gbt_sales_incharge and ger_status='A')
			 join gft_emp_master em on (gem_emp_id=ger_reporting_empid and gem_status='A')	
			 where gpm_district_id=$district_id ";
			 if($role!=null){ $query.=" and gem_role_id=$role ";}
			 $result=execute_my_query($query);
			 if(mysqli_num_rows($result)>0){
			 $qdata=mysqli_fetch_array($result);
		 	 $GLH_LFD_EMP_ID=$qdata['ger_reporting_empid'];
		 	 return $GLH_LFD_EMP_ID; 
			 }
			 return null;
}
/**
 * @param string $lead_code
 * @param string $fieldname
 * @param string $emp_status
 *
 * @return string
 */
function get_lead_single_value($lead_code,$fieldname,$emp_status=''){
	$sql_lead_query	=	" select $fieldname from gft_lead_hdr lh ";
	if($emp_status=='A'){
		$sql_lead_query	.=	" inner join gft_emp_master em on(lh.$fieldname=em.gem_emp_id)";
	}
	$sql_lead_query	.=	" where lh.glh_lead_code=$lead_code";
	if($emp_status=='A'){
		$sql_lead_query	.=	" AND em.gem_status='A'";
	}
	$result=execute_my_query($sql_lead_query);
	if(mysqli_num_rows($result)==0){
		return 0;
	}
	$row=	mysqli_fetch_array($result);
	$return_res	=	$row[0];
	return $return_res;
}

/**
 * @param string $gprId
 * @param string $confirm_code
 * @param string $update
 * @param string $domainPassword
 * @param string $lead_code
 * @param boolean $verified_otp
 * 
 * @return mixed[string]
 */
function create_qsr_lead_after_activation($gprId,$confirm_code, $update='',$domainPassword='',$lead_code='',$verified_otp=false){
	$return_array=/*. (mixed[string]) .*/ array(); //QSR_VERIFICATION_MAIL_VALIDITY
	$validity_days=/*. (int) .*/get_samee_const('QSR_VERIFICATION_MAIL_VALIDITY');
	$domainPassword	=	mysqli_real_escape_string_wrapper($domainPassword);
	if($gprId=='' or $confirm_code==''){
		$return_array['error_code']='601_EMAIL_ADDR_101';
		$return_array['error_message']="Error in confiremation link";
	}
	$sql_check_activated	=	"SELECT GPR_REGISTER_ID, GPR_EMAIL_ID,  GPR_ACTIVATION_STATUS, GPR_CREATED_DATE, GPR_PCODE FROM gft_presignup_registration WHERE GPR_REGISTER_ID=$gprId ";
	$res_check_activated	=	execute_my_query($sql_check_activated);
	if($row	=mysqli_fetch_array($res_check_activated)){
		if($row['GPR_ACTIVATION_STATUS']=='A'){
			$return_array['error_code']='601_EMAIL_ADDR_101';
			$return_array['error_message']="Already activated this link";
		}else if($row['GPR_ACTIVATION_STATUS']=='I' && !$verified_otp){
			$return_array['error_code']='601_EMAIL_ADDR_101';
			$return_array['error_message']="Deactivated this comfirmation link";
		}else{			
			$created_dt				=	$row['GPR_CREATED_DATE'];
			if($update!=''){
				$sql_update_comfirm	=	execute_my_query("UPDATE gft_presignup_registration SET GPR_LEAD_CODE='$lead_code', GPR_PASSWORD='$domainPassword', GPR_ACTIVATION_STATUS='A', GPR_UPDATED_DATE=now() WHERE GPR_REGISTER_ID=$gprId ");
				$return_array['success']=	true;
				$return_array['email']	=	$row['GPR_EMAIL_ID'];
			}else{
				$created_dt = strtotime($created_dt);
				$expired_dt = strtotime("+$validity_days day", $created_dt);
				$curr_dt	=	strtotime(date('Y-m-d H:i:s'));
				if($expired_dt<$curr_dt){
					execute_my_query("UPDATE gft_presignup_registration SET GPR_ACTIVATION_STATUS='I',GPR_UPDATED_DATE=now() WHERE  GPR_REGISTER_ID=$gprId ");
					$return_array['error_code']='601_EMAIL_ADDR_101';
					$return_array['error_message']="Deactivated this comfirmation link";
				}else{
					$return_array['success']=	true;
					$return_array['email']	=	$row['GPR_EMAIL_ID'];
					$return_array['product_code']	=	$row['GPR_PCODE'];
				}
			}
			return $return_array;
		}
	}else{
		$return_array['error_code']='601_EMAIL_ADDR_101';
		$return_array['error_message']="Invalid confirmation link";
	}
	return $return_array;
}

/**
 * @param string $emp_id
 * @param string $cust_name
 * @param string $mobile_no
 * @param string $email
 *
 * @return string
 */
function create_internal_lead_code($emp_id,$cust_name='',$mobile_no='',$email=''){
	if($cust_name=='' || $mobile_no=='' || $email=='') {
		$emp_dtl = get_emp_master($emp_id);
		$cust_name  = $emp_dtl[0][1];
		$mobile_no  = $emp_dtl[0][3];
		$email		= $emp_dtl[0][4];
	}
	$lead_arr['GLH_CUST_NAME'] = $cust_name.' GFT';
	$lead_arr['GLH_CUST_STREETADDR1']='Gofrugaltech';
	$lead_arr['GLH_CUST_STREETADDR2']='Gofrugaltech';
	$lead_arr['GLH_CREATED_CATEGORY']='21';
	$lead_arr['GLH_LEAD_TYPE']='8';
	$lead_arr['GLH_STATUS']='26';
	$lead_arr['GLH_VERTICAL_CODE']='1001';

	$array_cno[1] = $mobile_no;
	$contact_type[1] = '1';  //mobile
	$array_cno[2] = $email;
	$contact_type[2] = '4';  //email
	$array_conper[1] = $array_conper[2] = $cust_name;
	$designation[1] = $designation[2] = '1';  //properitor

	$lead_dtl = array_insert_new_lead_db($lead_arr, $product_arr=null, $array_conper, $array_cno, $designation, $contact_type);
	return $lead_dtl[1];
}
/**
 * @param string $otp
 * @param string $emailid
 * @param string $product
 * 
 *  @return mixed[string]
 */
function validate_presignup_otp($otp,$emailid,$product){
	$return_array=/*. (mixed[string]) .*/ array();
	$validity_days=/*. (int) .*/get_samee_const('QSR_VERIFICATION_MAIL_VALIDITY');
	$sql_get_otp	=	" SELECT GPR_REGISTER_ID,GPR_CREATED_DATE,GPR_LEAD_CODE FROM  gft_presignup_registration WHERE (GPR_OTP_CODE='$otp' or GPR_CONFIRM_CODE='$otp') AND GPR_EMAIL_ID='$emailid' AND GPR_ACTIVATION_STATUS='N' ";
	$res_get_otp	=	execute_my_query($sql_get_otp);
	if(mysqli_num_rows($res_get_otp)==0 or mysqli_num_rows($res_get_otp)>1){
		$return_array['status']='error';
		$return_array['message']=' Invalid OTP ';
	}else{
		$row_otp	=	mysqli_fetch_array($res_get_otp);
		$created_dt	=	$row_otp['GPR_CREATED_DATE'];
		$gprId		=	$row_otp['GPR_REGISTER_ID'];
		$created_dt = strtotime($created_dt);
		$expired_dt = strtotime("+$validity_days day", $created_dt);
		$curr_dt	=	strtotime(date('Y-m-d H:i:s'));
		if($expired_dt<$curr_dt){
			execute_my_query("UPDATE gft_presignup_registration SET GPR_ACTIVATION_STATUS='I',GPR_UPDATED_DATE=now() WHERE  GPR_REGISTER_ID=$gprId ");
			$return_array['status']='error';
			$return_array['message']=' Deactivated this OTP ';
		}else{
			$return_array['status']	=	'success';
			$return_array['message']=	' Valid OTP ';
			$return_array['vlinkid']	=	$row_otp['GPR_REGISTER_ID'];
			$return_array['gwtId']	=	$row_otp['GPR_REGISTER_ID'];
			$return_array['cust_id']	=	$row_otp['GPR_LEAD_CODE'];
		}
	}
	return $return_array;
}
/** 
 *  
 * @param string $domain_name
 * @param string $product_code
 * 
 * @return mixed[string]
 */
function validate_domainname_expiry($domain_name,$product_code){
	$return_array=/*. (mixed[string]) .*/ array();
	$query_tennant="SELECT SERVER_ID, TENANT_ID,TENANT_DOMAIN_NAME, TENANT_NAME,GSM_WPOS_ADDR,TENANT_SAM_ID,GID_VALIDITY_DATE, ".
			"if((GPM_FREE_EDITION='Y' and GPM_SUBSCRIPTION_PERIOD=0), 'F',GPM_FREE_EDITION) GPM_FREE_EDITION, GPM_DISPLAY_NAME FROM gft_tenant_master " .
			" join gft_server_master gs on(GSM_SERVER_ID=SERVER_ID ) " .
			" join gft_install_dtl_new gid ON(gid.gid_lead_code=TENANT_SAM_ID AND TENANT_PRODUCT=gid.gid_product_code)".
			" join gft_product_master pm ON(pm.GPM_PRODUCT_CODE=gid.gid_product_code and pm.GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
			" WHERE GID_STATUS='A' AND TENANT_STATUS=1 AND TENANT_DOMAIN_NAME='".mysqli_real_escape_string_wrapper($domain_name)."' and TENANT_PRODUCT=$product_code" ;
	$result_tennant=execute_my_query($query_tennant);
	if(mysqli_num_rows($result_tennant)==0){
		$return_array['status']='error';
		$return_array['message']='Domain not exist';
		$return_array['error_code']='E002';
		return $return_array;
	}else if(mysqli_num_rows($result_tennant)>1){
		$return_array['status']='error';
		$return_array['message']='More then one Domain exist';
		$return_array['error_code']='E003';
		return $return_array;
	}else{
		$row_tennant	=	mysqli_fetch_array($result_tennant);
		$valitity_dt	=	$row_tennant['GID_VALIDITY_DATE'];
		$version		=	$row_tennant['GPM_FREE_EDITION'];
		$plan_name		=	$row_tennant['GPM_DISPLAY_NAME'];
		$lead_code		=	$row_tennant['TENANT_SAM_ID'];
		$domain_name	=	$row_tennant['TENANT_DOMAIN_NAME'].".".$row_tennant['GSM_WPOS_ADDR'];
		$current_dt	 	= 	date('Y-m-d');
		$plan_txt		=	"Trial";
		$return_array['plan_name']=$plan_name;
		if(strtotime($valitity_dt)<=strtotime($current_dt) and $version!='F'){
			$return_array['status']='error';
			$cust_dtls=customerContactDetail($lead_code);
			$address_dtl =	'';
			if($cust_dtls['LOCATION']!=''){
				$address_dtl	=	($address_dtl!=''?', ':'').$cust_dtls['LOCATION'];
			}
			if($cust_dtls['area_name']!=''){
				$address_dtl	.=	($address_dtl!=''?', ':'').$cust_dtls['area_name'];
			}
			if($cust_dtls['city']!=''){
				$address_dtl	.=	($address_dtl!=''?', ':'').$cust_dtls['city'];
			}
			$mail_tmp_id=187;	
			if($version=='Y'){				
				$return_array['message']='Domain expired in trial plan';
				$return_array['error_code']='E004';
				$return_array['domain_name']=$domain_name;
			}else{
				$return_array['message']='Domain subscription expired';
				$plan_txt		=	"Subscription";
				$return_array['error_code']='E005';
				$return_array['domain_name']=$domain_name;
				$mail_tmp_id=188;
			}
			$db_sms_content_config=array(
					'Customer_Id' => array($lead_code),
					'SAAS_existing_plan' => array($plan_txt),
					'domin_name' => array($domain_name),
					'Email' => array($cust_dtls['EMAIL']),
					'contact_name' => array($cust_dtls['contact_name']),
					'Mobile' => array($cust_dtls['mobile_no']),
					'address' => array($address_dtl),
					'Validity_Date'=>array($valitity_dt));
			send_formatted_mail_content($db_sms_content_config,59,$mail_tmp_id,$employee_ids=null,$customer_ids=null,$tomail_ids=null);
			return $return_array;
		}else{
			$return_array['status']='success';
			$return_array['message']='Valid domain name';
			$return_array['domain_name']=$domain_name;			 
			return $return_array;
		}
	}	
}
/**
 *
 * @param string $emailid
 * @param string $product_code
 *
 * @return mixed[string]
 */
function validate_presignup_verification($emailid,$product_code){
	$return_array1=/*. (mixed[string]) .*/ array();
	$validity_days=/*. (int) .*/get_samee_const('QSR_VERIFICATION_MAIL_VALIDITY');
	$query=" select  gcc_lead_code,TENANT_DOMAIN_NAME,GID_VALIDITY_DATE,if((GPM_FREE_EDITION='Y' and GPM_SUBSCRIPTION_PERIOD=0), 'F',GPM_FREE_EDITION) GPM_FREE_EDITION,GSM_WPOS_ADDR,GPM_DISPLAY_NAME from gft_customer_contact_dtl " .
			" join gft_install_dtl_new ins on (gid_lead_code=gcc_lead_code and gid_status='A' ) " .
			" join gft_tenant_master tm on(TENANT_SAM_ID=gid_lead_code AND TENANT_STATUS=1 AND TENANT_PRODUCT=$product_code) ".
			" join gft_server_master sm on(sm.GSM_SERVER_ID=SERVER_ID) ".
			" join gft_product_master pm ON(pm.GPM_PRODUCT_CODE=ins.gid_product_code and pm.GPM_PRODUCT_SKEW=ins.GID_LIC_PSKEW) ".
			" where gcc_contact_no='$emailid' and gid_product_code=$product_code limit 1 ";
	$result=execute_my_query($query);
	$qd=mysqli_fetch_array($result);
	if(mysqli_num_rows($result)==0){
		$sql_get_otp	=	" SELECT GPR_REGISTER_ID,GPR_CONFIRM_CODE, GPR_CREATED_DATE FROM  gft_presignup_registration WHERE  GPR_EMAIL_ID='$emailid' AND substring(GPR_PCODE,1,3)='$product_code' AND GPR_ACTIVATION_STATUS='N' ";
		$res_get_otp	=	execute_my_query($sql_get_otp);
		if(mysqli_num_rows($res_get_otp)==1){
			$rowres	=	mysqli_fetch_array($res_get_otp);
			$register_id	=	$rowres['GPR_REGISTER_ID'];
			$return_array1['status']='error';
			if(strlen($rowres['GPR_CONFIRM_CODE'])<6){
				$return_array1['message']='OTP is not yet verified';
			}else{
				$return_array1['message']='Verification mail is not yet verified';
			}
			$return_array1['error_code']='605_OTP_NOT_ACTIVETED';
			///inactive
			$created_dt	=	$rowres['GPR_CREATED_DATE'];
			$created_dt = strtotime($created_dt);
			$expired_dt = strtotime("+$validity_days day", $created_dt);
			$curr_dt	=	strtotime(date('Y-m-d H:i:s'));
			if($expired_dt<$curr_dt){
				execute_my_query("UPDATE gft_presignup_registration SET GPR_ACTIVATION_STATUS='I',GPR_UPDATED_DATE=now() WHERE  GPR_REGISTER_ID=$register_id ");
				$return_array1['message']=' Deactivated OTP/Verification link';
				$return_array1['error_code']='605_OTP_DE_ACTIVETED';
			}
			return $return_array1;
		}else if(mysqli_num_rows($res_get_otp)==0){
			$return_array1['status']='error';
			$return_array1['message']='This email id not registered';
			$return_array1['error_code']='605_NOT_REGISTERED';
			return $return_array1;
		}
	}else if(mysqli_num_rows($result)>1){
		$return_array1['status']='error';
		$return_array1['message']='More then one Domain exist';
		$return_array1['error_code']='E006';
		return $return_array1;
	}else{
		$valitity_dt	=	$qd['GID_VALIDITY_DATE'];
		$domain_name	=	$qd['TENANT_DOMAIN_NAME'].".".$qd['GSM_WPOS_ADDR'];
		$version		=	$qd['GPM_FREE_EDITION'];
		$plan_name		=	$qd['GPM_DISPLAY_NAME'];
		$return_array1['plan_name']=$plan_name;
		$current_dt		=	date('Y-m-d');
		if(strtotime($valitity_dt) <= strtotime($current_dt) and $version!='F'){
			$return_array1['status']='error';
			$return_array1['domain_name']="$domain_name";
			if($version=='Y'){
				$return_array1['message']='Domain expired in trial plan';
				$return_array1['error_code']='E004';
			}else{
				$return_array1['message']='Domain subscription expired';
				$return_array1['error_code']='E005';
			}
			return $return_array1;
		}else{
			$return_array1['status']='success';
			$return_array1['domain_name']="$domain_name";
			$return_array1['message']='Valid domain name';
			return $return_array1;
		}
	}
	return $return_array1;
}

/**
 * @param string $cust_id
 * @param string $email
 * @param string $mob_no
 * @param string $product
 * @param int $vertical_code
 * @param int $required_download
 * @param boolean $new_lead
 * @param string $GLH_CREATED_CATEGORY
 * @param string $country_code
 * @param string $state_code
 * 
 * @return string[string]
 */
function get_verification_and_otp_for_pull_lead_registration($cust_id,$email='',$mob_no='',$product='',$vertical_code=0,$required_download=0,$new_lead=false,$GLH_CREATED_CATEGORY='',$country_code='',$state_code=''){
	$return_array	=/*. (string[string]) .*/ array();
	//$product_qry	=	"";	
	if($cust_id==0 or (($email=='') && ($mob_no=='')) ) {
		$return_array['error_code']='E001';
		$return_array['error_message']='Invalid leadcode or email';
		return $return_array;
	}
	//if($product!=''){
	//	$product_qry	=	" AND GPR_PCODE='$product'";
	//}
	$email = trim($email);
	execute_my_query(" UPDATE gft_presignup_registration SET GPR_ACTIVATION_STATUS='I'  WHERE GPR_LEAD_CODE='$cust_id' ".
					" AND GPR_OTP_MOBILENO='$mob_no' and GPR_EMAIL_ID='$email' AND GPR_ACTIVATION_STATUS in('N') ");
	$confirm_code	=	md5(generatePassword());
	$otp_code		=	generate_presignup_OTP();
	$test_mode 		= (int)get_samee_const("ENVIRONMENT");
	if( (strcasecmp($email,'appdemo@gofrugal.com')==0) && ($mob_no=='9166200200') ){  //apple ios app submit demo
		$otp_code = '22144';
	}else if( (strcasecmp($email,'demo@gofrugal.com')==0) && ($mob_no=='9466200200') ){ //servquick live for ios submit
		$otp_code = '98765';
	}else if(in_array($test_mode,array(1,2))){ // qa and preprod
		$otp_code = '12345';
	}
	$lead_type	=	0;
	if($new_lead){
		$lead_type=1;
	}

	$sql_insert_tem	=	" INSERT INTO gft_presignup_registration(GPR_LEAD_CODE,GPR_PCODE,GPR_VERTICAL_CODE,GPR_CREATED_CATEGORY,".
						"GPR_EMAIL_ID, GPR_IS_NEWLEAD, GPR_CONFIRM_CODE, GPR_OTP_CODE,GPR_REQUIRED_DOWNLOAD,GPR_OTP_MOBILENO, ".
						"GPR_REMAINTER_MAIL_COUNT, GPR_ACTIVATION_STATUS, GPR_CREATED_DATE,GPR_COUNTRY_CODE,GPR_STATE_CODE)".
						" VALUES('$cust_id','$product','$vertical_code','$GLH_CREATED_CATEGORY',".
						"'$email','$lead_type','$confirm_code','$otp_code','$required_download','$mob_no',".
						"0,'N',now(),'$country_code','$state_code')";
	if(execute_my_query($sql_insert_tem)){
		$lastiinsertid	=	mysqli_insert_id_wrapper();
		$return_array['vlinkid']=$lastiinsertid;
		$return_array['confirm_code']=$confirm_code;
		$return_array['otp_code']=$otp_code;
		return $return_array;
	}else{
		$return_array['error_code']='E002';
		$return_array['error_message']='Error to generate OTP';
		return $return_array;
	}
	
}
/**
 * @param string $vertical_code
 * @param string $state_name
 *
 * @return int
 */
function get_event_mail_template_id($vertical_code,$state_name){
	$state	=	mysqli_real_escape_string_wrapper(trim($state_name));
	$res_get_stateid	=	execute_my_query("select state_id from p_map_view where state='$state' limit 1 ");
	if(mysqli_num_rows($res_get_stateid)==0){
		return 0;
	}else {
		$row_state	=	mysqli_fetch_array($res_get_stateid);
		$state_id	=	(int)$row_state['state_id'];
		$date_con 	=	date("Y-m-d");
		$sql_result = 	execute_my_query("select GWM_MAIL_TEMPLATE_ID from gft_welcome_mail_vertical_region_mapping ".
				" inner join gft_event_master ON(GWM_EVENT_ID=GEM_EVENT_ID)".
				" where GWM_VERTICAL_ID='$vertical_code' AND (GWM_STATE_ID=0 OR GWM_STATE_ID='$state_id') AND ".
				" GEM_EVENT_TO_DATE>='$date_con' ORDER BY GWM_VERTICAL_ID DESC, GWM_STATE_ID DESC limit 1");
		if(mysqli_num_rows($sql_result)==0){
			return 0;
		}
		$row_mail_id = mysqli_fetch_array($sql_result);
		return (int)$row_mail_id['GWM_MAIL_TEMPLATE_ID'];
	}
}
/**
 * @param string $product
 * @param string $cust_id
 * @param string $domain_name
 * @param string $domainPassword
 * @param string $new_lead
 * @param string $email
 * @param string $mobile
 * @param string $validity_date
 * 
 * @return void
 */
function create_saas_order($product,$cust_id,$domain_name,$domainPassword,$new_lead,$email,$mobile,$validity_date=""){
    $product_trial_array = array('601'=>'60106.0TPE','605'=>'60501.0TT30','762'=>'60106.0TOE');
    $pcodeskew	     =	isset($product_trial_array[$product])?$product_trial_array[$product]:"60501.0TT30";
    $pcode	         =	substr($pcodeskew, 0,3);
    $pskew	         =	substr($pcodeskew,3);
    $reffproduct     =  get_reff_product($pcodeskew);
    $free_edition    = 'y';
    $reffproduct_skew=($reffproduct!=''?substr($reffproduct,3):'');
    $order_no=check_and_place_order($pcode,$pskew,$free_edition,$cust_id,$reffproduct_skew);
    if($order_no!=''){
        update_call_preferance($cust_id);/* update call preference */
    }
    $return_array = array();
    $install_status=false;
    if($domain_name=='' or $domainPassword==''){
        $return_array['error_code']='E003';
        $return_array['error_message']='Required Domain name/Password';
        return $return_array;
    }
    //$return_val	=	create_qsr_lead_after_activation($vlinkid,$confirm_code,'1',$domainPassword,$cust_id,$verified_otp);//Update activation link status after created tenant
    $domain_related_dtl=update_domain_name($cust_id,$pcode,$domain_name);
    $error_code=isset($domain_related_dtl['error_code'])?$domain_related_dtl['error_code']:'0';
    if($error_code == "0"){
        $install_status=update_install_dtl_saas($cust_id,$pcode,$pskew,$order_no,$domain_related_dtl,$new_lead,$email,$mobile,$validity_date);
    }else {
        $return_array['cust_id']=$cust_id;
        $return_array['order_no']=$order_no;
        $return_array['error_code']=$domain_related_dtl['error_code'];
        $return_array['error_message']=$domain_related_dtl['error_message'];
        return $return_array;
    }
    if($install_status==true){
        $return_array['installed_status']=0;
    }else if($install_status!=true){
        $customer_name = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", $cust_id);
        $msg=" Order No : $order_no, Customer Id :$cust_id \nCustomer Name: ".$customer_name." \n Email: $email \n Domain Name: $domain_name \n\n Please Check the saas Gateway process report for the above mentioned request and re send it again ";
        $product_dtl=get_product_list_family(null,null,null,null,null,$pcode);
        send_mail_from_sam($category=37,$product_dtl[0][3],$product_dtl[0][3],'SaaS Gateway Process Failed',$msg,$attachment_file_tosend=null,
            $cc=null,false,$product_dtl[0][3],$from_page=null,$user_info_needed=false);
        $return_array['installed_status']=1;
    }
    return $return_array;
}
/** * 
 * @param string $cust_id
 * @param string $confirm_code
 * @param int $vlinkid
 * @param string $request_from
 * @param string $required_otp
 * @param boolean $verified_otp
 * @param string $product
 * 
 * @return mixed[string]
 * 
 */
function pull_lead_contact_verification($cust_id,$confirm_code,$vlinkid=0,$request_from='',$required_otp='',$verified_otp=false,$product=''){
	$return_array	=/*. (mixed[string]) .*/ array();
	$return_array['status']='false';
	$domain_name=(isset($_REQUEST['domain_name'])?(string)$_REQUEST['domain_name']:'');
	$domainPassword	=	(isset($_REQUEST['domainPassword'])?(string)$_REQUEST['domainPassword']:"");
	$validity_hrs	=/*. (int) .*/get_samee_const('CONTACT_VERIFICATION_VALIDITY');
	$where_qry		=	"";
	if($vlinkid!=0){
		$where_qry		=	" AND GPR_REGISTER_ID=$vlinkid";
	}
	$limit_query = "";
	if($verified_otp){
		$where_qry		.=	" AND GPR_ACTIVATION_STATUS='I'";
		$limit_query 	 =	" limit 1 ";
	}else{
		$where_qry		.=	" AND GPR_ACTIVATION_STATUS='N' ";
	}
	$sql_check_link	=	" SELECT GPR_ACTIVATION_STATUS,GPR_CREATED_DATE,GPR_PCODE,GPR_VERTICAL_CODE,GPR_CREATED_CATEGORY,".
						" GPR_REQUIRED_DOWNLOAD,GPR_EMAIL_ID,GPR_IS_NEWLEAD,GPR_REGISTER_ID,GPR_OTP_MOBILENO,GPR_STATE_CODE ".
						" FROM gft_presignup_registration WHERE GPR_LEAD_CODE='$cust_id' AND (GPR_CONFIRM_CODE='$confirm_code' ".
						" OR GPR_OTP_CODE='$confirm_code') $where_qry $limit_query";
	$sql_res		=	execute_my_query($sql_check_link);
	$created_dt = "";
	if(mysqli_num_rows($sql_res)==0){
		$return_array['error_code']='E001';
		$return_array['error_message']='Not a valid OTP/Verification link';
	}else if(mysqli_num_rows($sql_res)==1){
		$res_row	=	mysqli_fetch_array($sql_res);
		$link_status=	$res_row['GPR_ACTIVATION_STATUS'];
		$created_dt	=	$res_row['GPR_CREATED_DATE'];
		$vertical_code=$res_row['GPR_VERTICAL_CODE'];
		$state_code=$res_row['GPR_STATE_CODE'];
		$pcodeskew		=	$res_row['GPR_PCODE'];
		if(($product=='601' || $product=='605')&& $verified_otp){
			$pcodeskew	=	($product=='601'?"60106.0TPE":"60501.0TT30");
		}
		$email			=	$res_row['GPR_EMAIL_ID'];
		$mobile			=	$res_row['GPR_OTP_MOBILENO'];
		$required_download=	$res_row['GPR_REQUIRED_DOWNLOAD'];
		$vlinkid		=	$res_row['GPR_REGISTER_ID'];
		$created_category=	$res_row['GPR_CREATED_CATEGORY'];
		$new_lead=	($res_row['GPR_IS_NEWLEAD']==1?true:false);
		$validity_hrs	=	60*60*$validity_hrs;
		$expired_dt = strtotime($created_dt)+$validity_hrs;
		$curr_dt	=	strtotime(date('Y-m-d H:i:s'));
		$pcode	=	substr($pcodeskew, 0,3);
		$pskew	=	substr($pcodeskew,3);
		$reffproduct=get_reff_product($pcodeskew);
		$website_url=get_samee_const('Unsubscribe_Mail_URL');
		$unsubscribe_link	=	$website_url."&emailID=".md5($email);
		$free_edition='n';
		$outgoing_email_id='';
		$domain_related_dtl=array();
	//	$cust_dtl=customerContactDetail_Mail($cust_id,'');
		$cust_dtl['cust_name']=get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", $cust_id);
		$GLD_CUST_FEEDBACK="";
		if( in_array($pcode,array('601','605','604','705','762')) and $created_category!=32 ){ //TODO: to take from master
			$free_edition='y'; 
		}
		$reffproduct_skew=($reffproduct!=''?substr($reffproduct,3):'');
		$order_no=check_and_place_order($pcode,$pskew,$free_edition,$cust_id,$reffproduct_skew);
		if($order_no!=''){
			update_call_preferance($cust_id);/* update call preference */
		}
		$return_array['download_link']="";$product_video_link='';$product_help_link='';
		$send_wahtsnow_sms	=	false;
		$product_code_skew	=	$pcode."-".substr($pskew, 0,4);
		$result_whatsnow_sms	=	execute_my_query("select GWU_ID from gft_whatsnow_url_master where GWU_PRODUCT_CODE='$product_code_skew' and GWU_STATUS='A'");
		if(mysqli_num_rows($result_whatsnow_sms)==1){
			$send_wahtsnow_sms	=	true;
		}
		if($expired_dt<$curr_dt){
			execute_my_query("UPDATE gft_presignup_registration SET GPR_ACTIVATION_STATUS='I', GPR_UPDATED_DATE=now()  WHERE GPR_ACTIVATION_STATUS!='I' AND GPR_LEAD_CODE='$cust_id' AND (GPR_CONFIRM_CODE='$confirm_code' OR GPR_CONFIRM_CODE='$confirm_code') $where_qry ");
			$return_array['error_code']='E002';
			$return_array['error_message']=' Expired OTP/Verification link';
		}else{
			$return_array['status']='true';
			$return_array['cust_id']=$cust_id;
			$return_array['VERTICAL_NAME']=get_vertical_name_for($vertical_code);
			$return_array['VERTICAL_CODE']=$vertical_code;
			$return_array['product_code']=$pcode;
			$return_array['product_skew']=$pskew;
			if($request_from=='ipad' or $request_from=='android'){
				$name_in_caps	=	strtoupper($request_from);
				execute_my_query("UPDATE gft_lead_hdr SET GLH_CONTACT_VERIFIED='2', GLH_CONTACT_VERIFED_BY='$name_in_caps' WHERE GLH_LEAD_CODE=$cust_id");
				$GLD_CUST_FEEDBACK="verified contact using $request_from $email";
			}else if($required_otp=='sms'){
				execute_my_query("UPDATE gft_lead_hdr SET GLH_CONTACT_VERIFIED='2', GLH_CONTACT_VERIFED_BY='OTP' WHERE GLH_LEAD_CODE=$cust_id");
				$GLD_CUST_FEEDBACK="verified contact using OTP $mobile";
			}else{
				execute_my_query("UPDATE gft_lead_hdr SET GLH_CONTACT_VERIFIED='2', GLH_CONTACT_VERIFED_BY='MAIL' WHERE GLH_LEAD_CODE=$cust_id");
				$GLD_CUST_FEEDBACK="verified contact using email $email";
			}
			if($required_download==1){
				$return_array['README_LINK']="";
				$return_array['CRASH_RESTORE_LINK']="";
				$return_array['installation_guide_url']="";
				$return_array['download_link']="";
				$return_array['SAMPLE_DB_LINK']="";
				$return_array['SAMPLE_DB_LINK']="";				
				$product_help_link	=	"";
				$product_video_link	=	"";				
				if($pcode=='500' or $pcode=='200'){
					$version_dtl=get_version($pcode,$pskew,$select=false,$latest='y');
					$return_array['README_LINK']=(isset($version_dtl[0][3])?$version_dtl[0][3]:'');
					$return_array['CRASH_RESTORE_LINK']=(isset($version_dtl[0][15])?$version_dtl[0][15]:'');
					$return_array['installation_guide_url']=(isset($version_dtl[0][6])?$version_dtl[0][6]:'');
					$temp_download_link = isset($version_dtl[0][1])?$version_dtl[0][1]:'';
					//added for testing pilot release
					$web_installer_skews = explode(',',get_samee_const("Web_Installer_Download_Product"));
					if(in_array(substr($pskew,0,4),$web_installer_skews)){
						$sel_res = execute_my_query("select GPV_DOWNLOAD_HLINK from gft_product_version_master where GPV_PRODUCT_CODE=516 and gpv_current_version=1");
						if($row_dt = mysqli_fetch_array($sel_res)){
							$temp_download_link = $row_dt['GPV_DOWNLOAD_HLINK'];
						}
					}
					$domain_replace = explode(',',get_samee_const("POS_Downlaod_Domain"));
					$temp_download_link = str_replace($domain_replace, "", $temp_download_link);
					$spl_down_link = get_samee_const("Pos_Download_Link");
					$is_gobill_supported = (int)get_single_value_from_single_table("GTM_GOBILL_SUPPORTED", "gft_vertical_master", "GTM_VERTICAL_CODE", $vertical_code);					
					$gobill_link = "";
					if($is_gobill_supported==1){
					    $gobill_link = get_single_value_from_single_query("GPV_DOWNLOAD_HLINK", "select GPV_DOWNLOAD_HLINK from gft_product_version_master where GPV_PRODUCT_CODE=539 and gpv_current_version=1");
					    $gobill_link = str_replace($domain_replace, "", $gobill_link);
					    if($gobill_link!=""){
					        $gobill_link = "$spl_down_link?cust_id=$cust_id&filename=$gobill_link";
					    }
					}
					$download_link = "$spl_down_link?cust_id=$cust_id&filename=$temp_download_link";
					$return_array['gobill_link'] = urlencode($gobill_link);
					$return_array['download_link'] = urlencode($download_link);
					$return_array['SAMPLE_DB_LINK']=get_sample_db_link($vertical_code,$pcode,$pskew);
					$product_help_link	=	(isset($version_dtl[0][18])?$version_dtl[0][18]:'');
					$product_video_link	=	(isset($version_dtl[0][9])?$version_dtl[0][9]:'');
					/* default product master */
					if($return_array['SAMPLE_DB_LINK']==null or $return_array['SAMPLE_DB_LINK']==''){
						$return_array['SAMPLE_DB_LINK']=(isset($version_dtl[0][11])?$version_dtl[0][11]:'');
						/* product group master */
					}
				}else if($pcode=='707'){
					$return_array['download_link'] = get_single_value_from_single_table("GWU_TRIAL_URL", "gft_whatsnow_url_master", "GWU_APP_PCODE", $pcode);
				}
			}else if(($pcode=='601' or $pcode=='605') and $created_category!=32){
				$install_status=false;
				if($domain_name=='' or $domainPassword==''){
					$return_array['error_code']='E003';
					$return_array['error_message']='Required Domain name/Password';
					return $return_array;
				}
				$return_val	=	create_qsr_lead_after_activation($vlinkid,$confirm_code,$update='1',$domainPassword,$cust_id,$verified_otp);//Update activation link status after created tenant
				$domain_related_dtl=update_domain_name($cust_id,$pcode,$domain_name);
				$error_code=isset($domain_related_dtl['error_code'])?$domain_related_dtl['error_code']:'0';
				if($error_code == "0"){
					$install_status=update_install_dtl_saas($cust_id,$pcode,$pskew,$order_no,$domain_related_dtl,$new_lead,$email,$mobile);
				}else {
					$return_array['cust_id']=$cust_id;
					$return_array['order_no']=$order_no;
					$return_array['error_code']=$domain_related_dtl['error_code'];
					$return_array['error_message']=$domain_related_dtl['error_message'];
					return $return_array;
				}
				if($install_status==true){					
					$outgoing_email_id=mail_to_saas_customer($cust_id,$order_no,$domain_related_dtl['domain_name_url'],$cust_dtl['cust_name'],$pcode,$email,$user_password=$domainPassword);
					$return_array['installed_status']=0;
				}else if($install_status!=true){
					$msg=" Order No : $order_no, Customer Id :$cust_id \nCustomer Name: ".$cust_dtl['cust_name']." \n Email: $email \n Domain Name: $domain_name \n\n Please Check the saas Gateway process report for the above mentioned request and re send it again ";
					$product_dtl=get_product_list_family(null,null,null,null,null,$pcode);
					send_mail_from_sam($category=37,$product_dtl[0][3],$product_dtl[0][3],'SaaS Gateway Process Failed',$msg,$attachment_file_tosend=null,
					$cc=null,false,$product_dtl[0][3],$from_page=null,$user_info_needed=false); 
					$outgoing_email_id=regret_mail_to_webpos_customer($cust_id,$order_no,$domain_name,$cust_dtl['cust_name'],$pcode,$email);
					 $return_array['installed_status']=1;
				}
				$install_id=null;
				$install_ids=get_install_id($cust_id,substr($order_no, 0,15),$pcode);
				if(count($install_ids)==1){$install_id=implode(',', $install_ids);}
				$return_array['order_no']=$order_no;
				$return_array['installId']=$install_id;								
				$return_array['domain_name']=$domain_name;
				$return_array['server_id']=(isset($domain_related_dtl['server_id'])?(string)$domain_related_dtl['server_id']:'');
				$return_array['tenant_id']=(isset($domain_related_dtl['tenant_id'])?(string)$domain_related_dtl['tenant_id']:'');
				$return_array['tenant_error_code']=(isset($domain_related_dtl['error_code'])?(string)$domain_related_dtl['error_code']:'');
				$return_array['tenant_error_message']=(isset($domain_related_dtl['error_message'])?(string)$domain_related_dtl['error_message']:'');
				$return_array['domain_name_url']=(isset($domain_related_dtl['domain_name_url'])?(string)$domain_related_dtl['domain_name_url']:'');
				$return_array['protocol_type']=(isset($domain_related_dtl['protocol_type'])?(string)$domain_related_dtl['protocol_type']:'');
				//$return_array['installed_status']=($install_status==true?0:1);
				$return_array['outgoing_email_id']=$outgoing_email_id;
				$return_array['status']='success';
				$return_array['email']=$email;
				$return_array['domainPassword']=$domainPassword;
				$return_array['welcomeMessage']="The domain has been created  successfully";
				$db_content_config_saas_mail=array(
						'Customer_Name' => array($cust_dtl['cust_name'])
				);
				if($pcode=='605'){
					send_formatted_mail_content($db_content_config_saas_mail,37,241,null,null,$tomail_ids=$email);
				}
				if($pcode=='601'){
					send_formatted_mail_content($db_content_config_saas_mail,37,242,null,null,$tomail_ids=$email);
				}				
			} else if( in_array($pcode,array('604','705')) ){ //alert, mypulse				
				update_install_dtl_alert($cust_id,$pcode,$pskew,$order_no,$new_lead);
				alert_new_Register($cust_id,$order_no,$pcode,'',$email);
				//TODO: errors returned by above alert_new_register function need to be handled
				$return_array['order_no']=$order_no;
				$db_sms_content_config_alert=array(
						'Customer_Id' => array($cust_id),
						'Product_Video_Link'=>array($product_video_link),
						'Unsubscribe_Link'=>array($unsubscribe_link),
						'Product_Help_Link'=>array($product_help_link)
				);
				if($pcode=='604'){
					send_formatted_mail_content($db_sms_content_config_alert,2,210,$employee_ids=null,$customer_ids=null,$tomail_ids=$email);
				}
			} else if(($pcode=='601' or $pcode=='605') and $created_category==32){
				$db_sms_content_config_saas=array(
						'Customer_Id' => array($cust_id),
						'Product_Video_Link'=>array($product_video_link),
						'Unsubscribe_Link'=>array($unsubscribe_link),
						'Product_Help_Link'=>array($product_help_link)
				);
				send_formatted_mail_content($db_sms_content_config_saas,2,211,$employee_ids=null,$customer_ids=null,$tomail_ids=$email);
				$return_array['order_no']=$order_no;
				$return_array['status']='success';
				$return_array['email']=$email;
				///Mail 
			}else if($pcode=='762'){
			    $validity_date = "";
			    $free_date = get_single_value_from_single_table("GPM_FREE_TILL", "gft_product_family_master", "GPM_PRODUCT_CODE", $pcode);
			    if( ($free_date!='') && ($free_date!='0000-00-00') && (strtotime($free_date) > strtotime(date('Y-m-d'))) ){
			        $validity_date = $free_date;
			    }
			    execute_my_query("UPDATE gft_presignup_registration SET GPR_ACTIVATION_STATUS='A', GPR_PASSWORD='$domainPassword', GPR_UPDATED_DATE=now(), GPR_REMAINTER_MAIL_COUNT=1  WHERE GPR_ACTIVATION_STATUS!='I' AND GPR_LEAD_CODE='$cust_id' AND (GPR_CONFIRM_CODE='$confirm_code' OR GPR_OTP_CODE='$confirm_code') $where_qry ");
			    create_installation_entry(substr($order_no, 0,15), 1,$validity_date);
			    $saas_order_dtl = create_saas_order($pcode,$cust_id,$domain_name,$domainPassword,$new_lead,$email,$mobile,$validity_date);
			    if(!isset($saas_order_dtl['installed_status']) && (isset($saas_order_dtl['installed_status']) && $saas_order_dtl['installed_status']==1)){
			        return $saas_order_dtl;
			    }
			    send_addon_product_dtl_to_saas_server($cust_id, substr($order_no, 0,15),array('762'));
			    $resp = account_posting_to_integration_portal($cust_id,$pcode,$domainPassword,$email);
			    if(isset($resp['response_code']) && $resp['response_code']!='200'){
			        $customer_name = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", $cust_id);
			        $msg=" Order No : $order_no, Customer Id :$cust_id \nCustomer Name: ".$customer_name." \n Email: $email \n Domain Name: $domain_name \n\n Please Check the saas Gateway process report for the above mentioned request and re send it again ";
			        $product_dtl=get_product_list_family(null,null,null,null,null,$pcode);
			        send_mail_from_sam(37,$product_dtl[0][3],$product_dtl[0][3],'Integration server Process Failed',$msg,null,null,false,$product_dtl[0][3],null,false);
			        $return_array['installed_status']=1;
			    }
			    $integ_server_url = get_connectplus_config();
			    $return_array['domain_name']=$integ_server_url['integ_portal'];
			}
			if($pcode=='263'){ //sellsmart
				$content_config = array('Customer_Id' => array($cust_id));
				send_formatted_mail_content($content_config, 37, 251,null,null,array($email));
				$sms_category = 182;
				$sms_content = get_formatted_content($content_config, $sms_category);
				entry_sending_sms($mobile, $sms_content, $sms_category);
			}
			if($pcode=='703'){
				$return_array['user_auth_token'] = "AT57c982005fcca";
				$app_version = isset($_REQUEST['app_version'])?(string)$_REQUEST['app_version']:'';
				if( ($app_version!='') && (version_compare($app_version, "1.0.21",">=")===true) ){
					$return_ns_data = process_notification_registration($pcode,$mobile,$email);
					$return_array['user_auth_token'] = isset($return_ns_data['user_auth_token'])?$return_ns_data['user_auth_token']:'';
				}
				$return_array['otp'] = $confirm_code;
				$return_array['otpTimestamp'] = $created_dt;
			}
			$activity_dtl					=	array();
			$activity_dtl['GLD_LEAD_CODE']	=	$cust_id;
			$activity_dtl['GLD_EMP_ID']		=	9999;
			$activity_dtl['GLD_DATE']		=	date('Y-m-d H:i:s');
			$activity_dtl['GLD_VISIT_DATE']	=	date('Y-m-d');
			$activity_dtl['GLD_NOTE_ON_ACTIVITY']=$GLD_CUST_FEEDBACK;
			$activity_dtl['GLD_CUST_FEEDBACK']=$GLD_CUST_FEEDBACK;
			$activity_dtl['GLD_CALL_STATUS']="P";
			$activity_dtl['GLD_REPEATED_VISITS']="N";
			$activity_dtl['GLD_INTEREST_ADDON']="U";
			$activity_dtl['GLD_INTEREST_ADDON']="U";
			$activity_dtl['GLD_VISIT_NATURE']=71;
			insert_in_gft_activity_table($activity_dtl,$extra_activity_dtl=null,$new_lead=true);
			
			$offerDate = "";
			if($created_category==59){//Check offer when created category is connected banking
			    $offerDate = getOfferAvailabilityOfLead($cust_id);
			}
			$return_array['offerDate'] = "$offerDate";
			$website_url=get_samee_const('Unsubscribe_Mail_URL');
			$unsubscribe_link	=	$website_url."&emailID=".md5($email);
			$vertical_name = get_vertical_name_for($vertical_code);
			$db_sms_content_config=array(
					'Customer_Id' => array($cust_id),
					'Download_link'=>array(urldecode($return_array['download_link'])),
					'Product_Video_Link'=>array($product_video_link),
					'Unsubscribe_Link'=>array($unsubscribe_link),
					'Product_Help_Link'=>array($product_help_link),
					'WhatsNow_Playstore_link'=>array(""),
					'Vertical'=>array("$vertical_name"),
					'Customer_Name' => array($cust_dtl['cust_name']),
			        'Validity_Date' => array($offerDate)
			);			
			//Get mail template id based on customer vertical selection
			$mail_template_id = 191;
			$created_category_mail_tem = (int)get_single_value_from_single_table("GCC_MAIL_TEMPLATE_ID", "gft_lead_create_category", "GCC_ID", "$created_category");
			$vertical_mail_tmp_id = (int)get_single_value_from_single_table("GTM_WELCOME_MAIL_TEMP_ID", "gft_vertical_master", "GTM_VERTICAL_CODE", "$vertical_code");
			if($created_category_mail_tem>0){
				$mail_template_id=$created_category_mail_tem;
			}else if($vertical_mail_tmp_id>0){
				$mail_template_id=$vertical_mail_tmp_id;
			}			
			//START::Event notification in mail if any near by leads.
			$event_mail_template_id = get_event_mail_template_id($vertical_code,$state_code);
			if($event_mail_template_id>0){
				send_formatted_mail_content($db_sms_content_config,0,$event_mail_template_id,$employee_ids=null,$customer_ids=null,$tomail_ids=$email);
			}
			//END::Event notification in mail if any near by leads.
			if($send_wahtsnow_sms && ($pcode!='601' and $pcode!='605' and $pcode!='604' and $pcode!='703') && $created_category!=19 && $created_category!=41 ){
				$db_sms_content_config['WhatsNow_Playstore_link']=array(get_samee_const("WhatsNow_Playstore_link"));
				send_formatted_mail_content($db_sms_content_config,2,$mail_template_id,$employee_ids=null,$customer_ids=null,$tomail_ids=$email);
			}else if($pcode=='200'){
				send_formatted_mail_content($db_sms_content_config,2,$mail_template_id,$employee_ids=null,$customer_ids=null,$tomail_ids=$email);
			}else if($created_category==19 || $created_category==41){
				$cust_country=	get_single_value_from_single_table("glh_country", "gft_lead_hdr", "GLH_LEAD_CODE", $cust_id);
				if($cust_country=="India"){
					send_formatted_mail_content($db_sms_content_config,24,2,$employee_ids=null,$customer_ids=null,$tomail_ids=$email);
				}else{
					send_formatted_mail_content($db_sms_content_config,24,3,$employee_ids=null,$customer_ids=null,$tomail_ids=$email);
				}
			}else if($created_category==59 && $offerDate!=""){//Connecting Banking lead.
			    send_formatted_mail_content($db_sms_content_config,0,346,$employee_ids=null,$customer_ids=null,$tomail_ids=$email);
			}else if($created_category_mail_tem>0){//If mapped template id in lead created category master will send mail.
				send_formatted_mail_content($db_sms_content_config,0,$mail_template_id,$employee_ids=null,$customer_ids=null,$tomail_ids=$email);
			}
			//mail for true pos
			if($link_status=='N'){
			    execute_my_query("UPDATE gft_presignup_registration SET GPR_ACTIVATION_STATUS='A', GPR_PASSWORD='$domainPassword', GPR_UPDATED_DATE=now(), GPR_REMAINTER_MAIL_COUNT=1  WHERE GPR_ACTIVATION_STATUS!='I' AND GPR_LEAD_CODE='$cust_id' AND (GPR_CONFIRM_CODE='$confirm_code' OR GPR_OTP_CODE='$confirm_code') $where_qry ");
			}
		}
		//Auto-SMS for sending WhatsNow playstore link. As of now sending only for RPOS7 and ServQuick  registration
		$can_send_sms	=check_can_send_sms($mobile);		
		if($send_wahtsnow_sms and  $can_send_sms){
			entry_sending_sms_to_customer($mobile,get_formatted_content(array(),174),174,$cust_id,1,'9999',0,null,true);
		}
		if($pcode=='705' and  $can_send_sms){
			entry_sending_sms_to_customer($mobile,get_formatted_content(array(),189),189,$cust_id,1,'9999',0,null,true);
		}
		if($pcode=='703' and  $can_send_sms){
			entry_sending_sms_to_customer($mobile,get_formatted_content(array(),174),174,$cust_id,1,'9999',0,null,true);
		}
		if($pcode=='704' and  $can_send_sms){
			entry_sending_sms_to_customer($mobile,get_formatted_content(array(),183),183,$cust_id,1,'9999',0,null,true);
		}
		if($pcode=='511' || $pcode=='260' || $pcode=='306' ){
			$sms_template_ids['511']=186;$sms_template_ids['260']=187;$sms_template_ids['306']=188;
			entry_sending_sms_to_customer($mobile,get_formatted_content(array(),$sms_template_ids[$pcode]),$sms_template_ids[$pcode],$cust_id,1,'9999');
		}
		if($created_category==59 && $offerDate!=""){
		    entry_sending_sms_to_customer($mobile,get_formatted_content(array(),210),210,$cust_id,1,'9999');
		}
	}else if(mysqli_num_rows($sql_res)>1){
		execute_my_query("UPDATE gft_presignup_registration SET GPR_ACTIVATION_STATUS='I', GPR_UPDATED_DATE=now()  WHERE GPR_ACTIVATION_STATUS!='I' AND GPR_LEAD_CODE='$cust_id' AND (GPR_CONFIRM_CODE='$confirm_code' OR GPR_OTP_CODE='$confirm_code') $where_qry ");
		$return_array['error_code']='E001';
		$return_array['error_message']='Not a valid OTP/Verification link (more than one record) ';
	}else{
		$return_array['error_code']='E003';
		$return_array['error_message']='Invalid Customer ID/Confirm code';
	}
	return $return_array;
}

/**
 * @param string $user_auth_token
 * @param string $mobile_no
 * @param string $email
 * @param string $pcode
 * 
 * @return void
 */
function update_auth_token_to_users($user_auth_token,$mobile_no,$email,$pcode){
	$mob_condition = getContactDtlWhereCondition("GNU_MOBILE", $mobile_no);
	$sql1 = "select GNU_ID from gft_notification_users where GNU_APP_PCODE='$pcode' and ($mob_condition) ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$gnu_id = $row1['GNU_ID'];
		execute_my_query("update gft_notification_users set GNU_AUTHTOKEN='$user_auth_token',GNU_UPDATED_DATE=now() where GNU_ID='$gnu_id' ");
	}else{
		$que1 = " insert into gft_notification_users (GNU_MOBILE,GNU_AUTHTOKEN,GNU_UPDATED_DATE,GNU_APP_PCODE) ".
				" values ('$mobile_no','$user_auth_token',now(),'$pcode') ";
		execute_my_query($que1);
	}
}

/**
 * @param string $cust_id
 * @param string $contact_no
 * @param string $contacttype
 * 
 * @return void
 */
function update_customer_contact_dtl($cust_id,$contact_no,$contacttype ){
	$phone_number	=	"";
	if($contacttype==1 || $contacttype==2){
		$phone_number=substr($contact_no,-10);
	}else{
		$phone_number=$contact_no;
	}	
	$query="select GCC_CONTACT_NO from gft_customer_contact_dtl where GCC_CONTACT_NO like '%$phone_number' and GCC_LEAD_CODE='$cust_id'";
	$resultq=execute_my_query($query);
	if(mysqli_num_rows($resultq)==0){
		execute_my_query(" insert into gft_customer_contact_dtl (GCC_LEAD_CODE, GCC_CONTACT_NAME, gcc_designation, GCC_CONTACT_NO, gcc_id, gcc_contact_type)" .
				" (SELECT GCC_LEAD_CODE, GCC_CONTACT_NAME, gcc_designation, '$contact_no', '', '$contacttype' FROM gft_customer_contact_dtl where GCC_LEAD_CODE=$cust_id limit 1)");
	}
}
/**
 * 
 * @param string $cust_id
 * @param int $vlinkid
 * @param string $contact_no
 * @param string $emailID
 * 
 * @return mixed[string]
 */
function resend_otp_for_pull_lead_registration($cust_id,$vlinkid,$contact_no,$emailID){
	$return_array	=/*. (mixed[string]) .*/ array();
	$return_array['status']='false';
	$res_check_vlink	=	execute_my_query(" SELECT GPR_REGISTER_ID, GPR_OTP_MOBILENO, GPR_EMAIL_ID,GPR_COUNTRY_CODE FROM gft_presignup_registration WHERE GPR_LEAD_CODE='$cust_id' AND GPR_REGISTER_ID=$vlinkid ");
	if(mysqli_num_rows($res_check_vlink)==0){
		$return_array['error_code']='E001';
		$return_array['error_message']='Not a valid request';
	}else{
		$res_row	=	mysqli_fetch_array($res_check_vlink);
		$mob_no		=	$res_row['GPR_OTP_MOBILENO'];
		$cpy_mob_no	=	$mob_no;	
		$email		=	$res_row['GPR_EMAIL_ID'];	
		$country_code = $res_row['GPR_COUNTRY_CODE'];	
		$can_send_sms	=	false;
		$return_array['message']="Not sent SMS OTP";
		$otp_code		=	generate_presignup_OTP();

		if($contact_no!=''){
			$phone_number=substr($contact_no,-10);
			$contacttype=(strpos($phone_number,'9')===0?1:2);	
			update_customer_contact_dtl($cust_id,$contact_no,$contacttype);
			$can_send_sms	=check_can_send_sms($contact_no);			
			if($can_send_sms){
				$mob_no=$contact_no;
			}
			if(strtolower($country_code)!="in"){
				$mob_no=trim($contact_no);
				$can_send_sms=false;
			}
		}
		if($emailID!=''){		
			update_customer_contact_dtl($cust_id,$emailID,4);
			$email	=	$emailID;
		}
		if(!$can_send_sms){
			if($mob_no!=''){
				$can_send_sms=true;
			}
		}
		execute_my_query("UPDATE gft_presignup_registration SET GPR_OTP_CODE='$otp_code', GPR_OTP_MOBILENO='$mob_no', GPR_EMAIL_ID='$email' WHERE GPR_REGISTER_ID=$vlinkid");
		if($can_send_sms){
			entry_sending_sms_to_customer($mob_no,get_formatted_content(array('OTP'=>array($otp_code),'Customer_ID'=>array($cust_id)),143),143,$cust_id,1,9999,$send_to_alert=0,null,true,0,$country_code);
			$return_array['status']='true';
			$return_array['message']="Successfully sent OTP";
			if($cpy_mob_no==$mob_no){
				$return_array['message']="Successfully sent OTP to existing no";
			}
		}		
		//OTP mail 
		if(strtolower($country_code)!="in" || (!$can_send_sms)){
			$db_mail_content_config=array(
					"OTP"=>array($otp_code),
					"Customer_Id"=>array($cust_id)
			);
			send_formatted_mail_content($db_mail_content_config,85,155,$employee_ids=null,array($cust_id),array($email));
		}			 	
		$return_array['cust_id']=$cust_id;
		$return_array['vlinkid']=$vlinkid;
	}
	return $return_array;
}

/**
 * @param string $lead_code
 * @param string $reason
 * 
 * @return void
 */
function activity_for_new_lead_from_vm_im($lead_code, $reason){
	global $uid;
	$activity_dtl					= array();
	$activity_dtl['GLD_LEAD_CODE']	=	$lead_code;
	$activity_dtl['GLD_EMP_ID']		=	$uid;
	$activity_dtl['GLD_DATE']		=	date('Y-m-d H:i:s');
	$activity_dtl['GLD_VISIT_DATE']	=	date('Y-m-d');
	$activity_dtl['GLD_NOTE_ON_ACTIVITY']= $reason;
	$activity_dtl['GLD_VISIT_NATURE']	 = '25';
	array_update_tables_common($activity_dtl, 'gft_activity', null, null, $uid, null, null, $activity_dtl);
}

/**
 * @param string $quotation_no
 * @param int $approval_stat
 * @param string $reason
 * @param int $approved_by_emp
 *
 * @return boolean
 */
function quotation_approve($quotation_no,$approval_stat,$reason,$approved_by_emp){
	global $attach_path;
	$addt_upd = "";
	if($approval_stat==2){
		$res = execute_my_query("select GQH_EMP_ID,GQH_ORDER_NO,GQH_LEAD_CODE,GQH_MAIL_STATUS,GQH_QUOTATION_TO_EMAILS,GQH_CURRENCY_CODE, GQH_ORDER_AMT from gft_quotation_hdr where GQH_ORDER_NO='$quotation_no' ");
		if($row1 = mysqli_fetch_array($res)){
			if($row1['GQH_MAIL_STATUS']=='1'){
				$cust_code = $row1['GQH_LEAD_CODE'];
				$gqh_emp_id = $row1['GQH_EMP_ID'];
				$currency_type=$row1['GQH_CURRENCY_CODE'];
				$quotation_to_email_str=$row1['GQH_QUOTATION_TO_EMAILS'];
				$f_name	= "Qua_".$row1['GQH_ORDER_NO'].'.pdf';
				$cust_email_arr = customerContactDetail($cust_code);
				$total_amount = $row1['GQH_ORDER_AMT'];
				send_quotation_mail_to_customer($gqh_emp_id, $cust_code, $currency_type,$f_name, $cust_email_arr['cust_name'],
				    $quotation_no,$cust_email_arr['COUNTRY_NAME'], $total_amount, $cust_email_arr['EMAIL'], $quotation_to_email_str);
				
				$addt_upd = " ,GQH_MAIL_STATUS='2' ";
			}
		}
	}
	$update1 = " update gft_quotation_hdr set GQH_APPROVAL_STATUS='$approval_stat', GQH_APPROVEDBY_EMPID='$approved_by_emp', GQH_APPROVAL_REMARKS='$reason' $addt_upd where GQH_ORDER_NO='$quotation_no'";
	if(execute_my_query($update1)){
		return true;
	}
	return false;
}
/**
 * @param string $cust_id
 * @param string $GLH_VERTICAL_CODE
 * @param boolean $need_hq_demo
 * @param string $app_version
 * @param string $demo_url_id
 * 
 * @return string[string][string]
 */
function get_whatsnow_url_details($cust_id,$GLH_VERTICAL_CODE,$need_hq_demo=false,$app_version='',$demo_url_id=0){
	$products	=	get_cust_installed_pcode($cust_id,true);
	$hq_prod = '';
	if($need_hq_demo){
		$hq_prod = " or GWU_PRODUCT_CODE in ('300-03.0','500-07.0') ";
	}
	$whatsnow_url_arr=	array();
	$sql_whatsnow_url	=	" select GWU_PRODUCT_CODE,GWU_TRIAL_URL,GPM_IS_INTERNAL_PRODUCT from gft_whatsnow_url_master ". 
							" left join gft_product_family_master on(substring(GWU_PRODUCT_CODE,1,3)=GPM_PRODUCT_CODE) ".
							" where GWU_STATUS='A' and GWU_APP_PCODE=703 ";
	if($GLH_VERTICAL_CODE!=''){
		$sql_whatsnow_url	.=	" and (GWU_VERTICAL_CODE in ($GLH_VERTICAL_CODE) or GWU_VERTICAL_CODE=0) ";
	}else{
		$sql_whatsnow_url	.=	" and GWU_VERTICAL_CODE=0 ";
	}
	$product_condition	=	" and GWU_PRODUCT_CODE in ('300-03.0','500-07.0') ";
	if($demo_url_id>0){
	    $product_condition =" AND (GWU_ID=$demo_url_id or GWU_PRODUCT_CODE in ('300-03.0'))";
	}else if($products!=''){
		$product_condition	=	" and (GWU_PRODUCT_CODE in ($products) or GWU_PRODUCT_CODE in ('300-03.0')) ";
	}
	$order_by = " order by GWU_PRODUCT_CODE desc, GWU_VERTICAL_CODE desc ";
	
	$sql_query		=	$sql_whatsnow_url.$product_condition.$order_by;
	$res_whatsnow	=	execute_my_query($sql_query);
	if(mysqli_num_rows($res_whatsnow)==0){
		$sql_query		=	$sql_whatsnow_url." and GWU_PRODUCT_CODE=0 ".$order_by;
		$res_whatsnow	=	execute_my_query($sql_query);
	}
	$temp_url = "";
	while($row=mysqli_fetch_array($res_whatsnow)){
	    $whatsnow_url	=	array();
		$trial_url = trim($row['GWU_TRIAL_URL']);
		if($temp_url==$trial_url){
			continue;
		}
		$whatsnow_url['type']	=	"Desktop";
		$whatsnow_url['shopName'] = 'Standalone solution';
		if($row['GPM_IS_INTERNAL_PRODUCT']=='3'){
			$whatsnow_url['type']	=	"Cloud";
			$whatsnow_url['shopName'] = 'Cloud Solution';
		}
		$whatsnow_url['url']	= $trial_url;
		if( ($app_version!='') && (version_compare($app_version, "1.0.31","<")===true) ){
			$tm_demo_cust_id = "35006";
		}else{
			$tm_demo_cust_id = get_single_value_from_single_table("GWU_DEMO_CUSTOMER_ID", "gft_whatsnow_url_master", "GWU_APP_PCODE", "707");
		}
		$config_arr = get_connectplus_config();
		$cloud_url 	= $config_arr['cloud_domain'];
		$cloud_url	= str_replace("{{customerId}}", $tm_demo_cust_id, $cloud_url);
		$whatsnow_url['taskmanager_url'] 		= "$cloud_url/task_manager";
		$whatsnow_url['taskmanager_enabled'] 	= true; //backward compatibility
		$whatsnow_url['taskmanager_status'] 	= "ENABLED";
		$gwu_prod_arr = explode('-', $row['GWU_PRODUCT_CODE']);
		$pcode = isset($gwu_prod_arr[0])?$gwu_prod_arr[0]:'';
		$pgroup = isset($gwu_prod_arr[1])?$gwu_prod_arr[1]:'';
		$whatsnow_url['base_product'] = get_product_name_with_version($pcode, $pgroup);
		if($pcode=='300'){
			$outlet_dtl_arr = get_outlet_group_dtl('71183','1');
			$whatsnow_url['is_hq'] 	= true;
			$whatsnow_url['hq_outlets'] 	= $outlet_dtl_arr['outlet'];
			$whatsnow_url['hq_outlet_groups'] = $outlet_dtl_arr['outlet_group'];
			$whatsnow_url['shopName'] = 'Chain Solution';
		}
		if($pcode=='500'){
		    $whatsnow_url['features']    = array(array("code"=>"1","name"=>"Price Management","status"=>"TRIAL_NOT_TRIED","validity_date"=>"2020-01-31"));
		    if($demo_url_id>0){
		        $whatsnow_url['features'][] = array("code"=>"7","name"=>"Price Confirmation","status"=>"TRIAL_NOT_TRIED","validity_date"=>"2020-10-31");
		        $whatsnow_url['features'][] = array("code"=>"5","name"=>"Sales Discount Approval","status"=>"TRIAL_NOT_TRIED","validity_date"=>"2020-10-31");
		    }
		}
		$whatsnow_url_arr[]		=	$whatsnow_url;
		$temp_url = $trial_url;
	}
	return $whatsnow_url_arr;
}
/**
 * @param string $emp_id
 * @param string $activity_nature
 * @param int $type
 * @return mixed[]
 */
function is_having_pending_appointments_followups($emp_id,$activity_nature,$type) {
	$ret_arr = array();
	$pending_status = false;
	if($type==1 || $type==2) { // 1 - appointments and 2 - followups
		$ret_arr['success'] = "1";
		$ret_arr['query'] = "";
		$qry = "";
		if($type==1) {
			$qry = " select gld_lead_code from gft_activity ".
					" where gld_next_action='$activity_nature' and GLD_EMP_ID='$emp_id' and ".
					" GLD_NEXT_ACTION_DATE <= date(now()) and GLD_SCHEDULE_STATUS='1'";
			$lead_owner_update_qry = execute_my_query($qry);
		} else {
			$qry = " select gcf_lead_code from gft_cplead_followup_dtl ".
					" where GCF_FOLLOWUP_ACTION='$activity_nature' and GCF_ASSIGN_TO='$emp_id' and ".
					" GCF_FOLLOWUP_DATE <= date(now()) and GCF_FOLLOWUP_STATUS='1'";
			$lead_owner_update_qry = execute_my_query($qry);
		}
		if(mysqli_num_rows($lead_owner_update_qry)>0) {
			$pending_status = true;
			$ret_arr['query'] = $qry;
		}
		$ret_arr['pending_status'] = $pending_status;
	} else {
		$ret_arr['success'] = "0";
		$ret_arr['pending_status'] = true;
		$ret_arr['query'] = "";
	}
	return $ret_arr;
}
/**
 * @param string $lead_code
 * @return string
 */
function get_order_approval_waiting_qry($lead_code) {
	$status_query = " select GOD_ORDER_NO,em.GEM_EMP_NAME as employee_name,mgr.GEM_EMP_NAME as reporting_manager ".
			" from gft_order_hdr ".
			" join gft_emp_master em on (em.GEM_EMP_ID=GOD_EMP_ID) ".
			" join gft_emp_reporting rep on (rep.GER_EMP_ID=em.gem_emp_id and GER_STATUS='A') " .
			" join gft_emp_master mgr on (mgr.GEM_EMP_ID=rep.GER_REPORTING_EMPID) ".
			" where GOD_ORDER_APPROVAL_STATUS=1 and GOD_ORDER_STATUS='A' and god_order_splict=1 and GOD_LEAD_CODE='$lead_code' ";
	return $status_query;
}
/**
 * @param string $uid
 * @param string $lead_code
 *
 * @return void
 */
function send_mail_and_notification_for_opportunity_lost_changes($uid,$lead_code){
	$busi_emp_id=get_lead_mgmt_incharge(0,0,0,0,0,$lead_code,false,true);
	$visit_hostory_dtl = "<table border='1'><tr><th>S.No</th><th>Visit Date</th><th>Visited By</th><th>Activity Nature</th><th>Employee Comments</th>
						<th>Customer Feedback</th><th>Status</th><th>Prospect Status</th><th>Activity At</th></tr>";
	$rsm_dtl 	= get_rsm_of_lead($lead_code, $uid);
	$rsm_id  	= isset($rsm_dtl['emp_id'])?(int)$rsm_dtl['emp_id']:0;
	$rsm_name  	= isset($rsm_dtl['emp_name'])?(int)$rsm_dtl['emp_name']:0;	
	$reporting = get_emp_master($uid);
	$manager_mail =(isset($reporting[0][8])?$reporting[0][8]:'');
	$manager_id = (isset($reporting[0][6])?$reporting[0][6]:'');
	if($busi_emp_id>0 || $rsm_id>0){
		$query = show_visit_history_sales($lead_code,null,true,'10');
		$visit_result = execute_my_query($query);
		$s_no=1;
		while($visit_row=mysqli_fetch_array($visit_result)){
			$visit_hostory_dtl .= "<tr><td>".$s_no."</td><td>".$visit_row['GLD_VISIT_DATE']."</td><td>".$visit_row['visited_by']."</td><td>".$visit_row['activity_nature']."</td><td>".$visit_row['my_comments']."</td>
						<td>".$visit_row['cust_feedback']."</td><td>".$visit_row['gcs_name']."</td><td>".$visit_row['GPS_STATUS_NAME']."</td><td>".$visit_row['gld_date']."</td></tr>";
			$s_no++;
		}
		$visit_hostory_dtl .= "</table>";
		$cust_dtl=customerContactDetail($lead_code);
		$emp_dtl = get_emp_master($uid);
		$notify_to = ($rsm_id>0?$rsm_id:$busi_emp_id);
		$busi_emp_dtl = get_emp_master($notify_to);
		$noti_content_config					= array();
		$noti_content_config['Customer_Name']	= array($cust_dtl['cust_name']);
		$noti_content_config['Customer_Id']		= array($lead_code);
		$noti_content_config['Employee_Name']	= array($emp_dtl[0][1]);
		$noti_content_config['Lead_Incharge']	= array($busi_emp_dtl[0][1]);		
		send_formatted_notification_content($noti_content_config,0,82,1,$notify_to,$lead_code);
		if(isset($manager_id)){
		send_formatted_notification_content($noti_content_config,0,82,1,$manager_id,$lead_code);
		}
		$noti_content_config['KYC']	= array($visit_hostory_dtl);
		send_formatted_mail_content($noti_content_config,0,301,$notify_to,$customer_ids=null,$manager_mail,array($busi_emp_id));
	}
}
function send_mail_for_hq_prospect_engagement($uid,$lead_code){
	$cust_dtl=customerContactDetail($lead_code);
	$emp_dtl = get_emp_master($uid);
	$mail_content_config					= array();
	$mail_content_config['Customer_Name']	= array($cust_dtl['cust_name']);
	$mail_content_config['Customer_Id']		= array($lead_code);
	$mail_content_config['Employee_Name']	= array($emp_dtl[0][1]);
	$mail_content_config['Sales_Manager_Mobile']= array($emp_dtl[0][3]);
	$custemail = explode(",",$cust_dtl['EMAIL']); 
	send_formatted_mail_content($mail_content_config,0,308,null,$customer_ids=array($lead_code),$tomail_ids=$custemail);
}
/**
 * @param string $round
 * @param string[int] $student_ids
 * @param string $visit
 * @param string $role
 * @param string[int] $aptitude_batches
 * @return string
 */
function send_sms_for_campus_drive($round,$student_ids,$visit,$role='',$aptitude_batches=null) {
	$comma = $not_sent_msg = '';
	$sent_cnt = 0;
	$tpl_ids = array('KPS'=>'201','Aptitude'=>'200','Programming'=>'200','GD'=>'202');
	$wh = "";
	if(is_array($aptitude_batches) and count($aptitude_batches)>0) {
	    $wh = " and gad_batch in ('".implode("','",$aptitude_batches)."')";
	    if($round=='Programming') {
	        $wh = " and gcp_batch_id in ('".implode("','",$aptitude_batches)."')";
	    }
	}
	$college_id = get_single_value_from_single_table("gcv_college_id","gft_college_visits","GCV_ID",$visit);
	foreach ($student_ids as $sid) {
		$dtl_qry = execute_my_query("select gsm_student_name,gsm_mobile_no,gsm_reg_no from gft_student_master where gsm_id='$sid' ");
		$sname = $to_contact = $regno = '';
		while($row = mysqli_fetch_array($dtl_qry)) {
			$sname = $row['gsm_student_name'];
			$to_contact = $row['gsm_mobile_no'];
			$regno = $row['gsm_reg_no'];
		}
		$not_sent_msg = "";
		if($to_contact!='') {
		    $resume_link = '';
		    if($round=='KPS') {
		        $resume_upload_param = substr("00000000".$sid,-8).substr("00000".$visit,-5);
		        $long_url = "https://careers.gofrugal.com/upload_resume.html?sid=".urlencode(base64_encode($resume_upload_param));
		        $ip_addr = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
		        $json_arr = getShortenedURL($long_url, $ip_addr);
		        $resume_link = isset($json_arr['short_url'])?$json_arr['short_url']:'';
		    }
			$db_mail_content_config=/*. (string[string][int]) .*/array(
					"Student_Name"=>array($sname),
					"Round"=>array($round),
					"Register_Number"=>array($regno),
					"Result"=>array("cleared"),
			        "Upload_Link"=>array($resume_link)
			);
			$sms_content = htmlentities(get_formatted_content($db_mail_content_config,$tpl_ids[$round]));
			entry_sending_sms($to_contact, $sms_content, $tpl_ids[$round]);
			if($round=='KPS') {
			    $sms_content = htmlentities(get_formatted_content($db_mail_content_config,'213'));
			    entry_sending_sms($to_contact, $sms_content, '213');
			}
			$sent_cnt++;
		} else {
			$not_sent_msg .= $comma."$snam ($regno)";
			$comma = ",";
		}
	}
	$not_selected_cond = array('KPS'=>' ','Aptitude'=>'','GD'=>' and GSM_KPS_RESULT=1');
	$not_selected_qry = " select gkh_student_id,gsm_mobile_no,gsm_reg_no,gsm_student_name from gft_kps_hdr join gft_student_master ".
						" on (gkh_student_id=gsm_id) where gkh_student_id not in ('".implode("','",$student_ids)."') and ".
						" gsm_visit_id='$visit' and (GSM_APTITUDE_RESULT=1 or gsm_programming_result=1) ";
	if($round=='Aptitude') {
		$not_selected_qry = " select GAD_STUDENT_ID,gsm_mobile_no,gsm_reg_no,gsm_student_name from gft_aptitude_dtl join gft_student_master ".
            				" on (GAD_STUDENT_ID=gsm_id) where gsm_aptitude_result not in ('".implode("','",$student_ids)."') and gsm_visit_id='$visit' ".
            				" and GSM_APTITUDE_RESULT=2 $wh ";
	} else if($round=='GD') {
		$not_selected_qry = " select gsm_id,gsm_mobile_no,gsm_reg_no,gsm_student_name from gft_student_master ".
				            " where gsm_id not in ('".implode("','",$student_ids)."') and gsm_visit_id='$visit' and GSM_KPS_RESULT=1 ";
	} else if($round=='Programming') {
	    $not_selected_qry = " select gsm_mobile_no,gsm_reg_no,gsm_student_name from gft_campus_programming_result ".
	                        " join gft_student_master on (gcp_student_id=gsm_id) ".
	                        " where gcp_student_id not in ('".implode("','",$student_ids)."') and gsm_visit_id='$visit' ".
                	   	    " and gsm_programming_result=2 and gsm_aptitude_result not in (1,2) $wh ";
	}
	if($role!='') {
		$not_selected_qry .= " and gsm_role in ('$role') ";
	}
	$not_selected_res = execute_my_query($not_selected_qry);
	while($row=mysqli_fetch_array($not_selected_res)) {
		$sname = $row['gsm_student_name'];
		$to_contact = $row['gsm_mobile_no'];
		$regno = $row['gsm_reg_no'];
		if($to_contact!='') {
			$db_mail_content_config=/*. (string[string][int]) .*/array(
					"Student_Name"=>array($sname),
					"Round"=>array($round),
					"Register_Number"=>array($regno),
					"Result"=>array("cleared")
			);
			$not_selected_sms_content = htmlentities(get_formatted_content($db_mail_content_config,'203'));
			entry_sending_sms($to_contact, $not_selected_sms_content, '203');
			$sent_cnt++;
		} else {
			$not_sent_msg .= $comma."$snam ($regno)";
			$comma = ",";
		}
	}
	if($not_sent_msg!='') {
		$not_sent_msg = "SMS not sent to $not_sent_msg due to unavailable mobile number. ";
	}
	if($sent_cnt>0) {
		$not_sent_msg = "SMS sent to $sent_cnt students. $not_sent_msg";
	}
	return $not_sent_msg;
}
/**
 * @param string $gcc_id
 * @param string $status
 *
 * @return void
 */
function update_in_blog_subscription($gcc_id,$status='N'){
	$gcc_id_arr	=	explode(',', $gcc_id);
	$separator  = "";
	$inser_values= "";
	$inc		=	0;
	while($inc<count($gcc_id_arr)){
		$gcc_id = $gcc_id_arr[$inc];
		$inser_values .= $separator."($gcc_id, '$status', now())";
		$separator  = ",";
		$inc++;
	}
	if($inser_values!=""){
		execute_my_query("REPLACE INTO gft_gofrugal_blog_subscribe_dtl VALUES$inser_values");
	}	
}
?>
