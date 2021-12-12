<?php
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__.'/../util_expense.php');
require_once(__DIR__.'/../function.insert_stmt.php');
require_once(__DIR__.'/../support_util.php');
require_once(__DIR__.'/../human_resource/function.hr_util.php');
require_once(__DIR__.'/../ismile/ismile_util.php');
require_once(__DIR__ ."/../chat_util.php");
require_once(__DIR__."/../include/class.common.utils.php");
$purpose = isset($_REQUEST['purpose'])?(string)$_REQUEST['purpose']:'';
$employee_id = isset($_REQUEST['employee_id'])?(string)$_REQUEST['employee_id']:'';
$start_date = isset($_REQUEST['start_date'])?db_date_format((string)$_REQUEST['start_date']):'';
$end_date = isset($_REQUEST['end_date'])?$_REQUEST['end_date']:'';
$reason = isset($_REQUEST['reason'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['reason']):'';
$cust_Code= isset($_REQUEST['custCode'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['custCode']):'';
$ph_no = isset($_REQUEST['ph_no'])?(string)$_REQUEST['ph_no']:'';
$alr_status_id = isset($_REQUEST['alr_status_id'])?(int)$_REQUEST['alr_status_id']:0;
$alr_old_sub_status = isset($_REQUEST['alr_old_sub_status'])?(int)$_REQUEST['alr_old_sub_status']:0;
$pcode = isset($_REQUEST['pcode'])?(string)$_REQUEST['pcode']:'';
$pskew = isset($_REQUEST['pskew'])?(string)$_REQUEST['pskew']:'';
$prod = isset($_REQUEST['prod'])?(string)$_REQUEST['prod']:'';
$type = isset($_REQUEST['type'])?(string)$_REQUEST['type']:'';
$spent_hrs 		= isset($_REQUEST['spent_hrs'])?(int)$_REQUEST['spent_hrs']:0;
$spent_mins 	= isset($_REQUEST['spent_mins'])?(int)$_REQUEST['spent_mins']:0;

$customer_name      = isset($_REQUEST['customer_name'])?(string)$_REQUEST['customer_name']:'';
$customer_vertical  = isset($_REQUEST['customer_vertical'])?(string)$_REQUEST['customer_vertical']:'';
$state_name         = isset($_REQUEST['state_name'])?(string)$_REQUEST['state_name']:'';

$proforma_no	= isset($_REQUEST['proforma_no'])?(string)$_REQUEST['proforma_no']:'';
$quotation_no 	= isset($_REQUEST['quotation_no'])?(string)$_REQUEST['quotation_no']:'';
$approval_stat 	= isset($_REQUEST['approval_stat'])?(string)$_REQUEST['approval_stat']:'';
$offset = isset($_REQUEST['offset'])?(int)$_REQUEST['offset']:0;
$prime_id =  isset($_REQUEST['prime_id'])?(string)$_REQUEST['prime_id']:'';
$lic_no =  isset($_REQUEST['licNo'])?(string)$_REQUEST['licNo']:'';
$support_gp = isset($_REQUEST['support_gp'])?$_REQUEST['support_gp']:'';
$cmb_status = isset($_REQUEST['cmbstatus'])?(string)$_REQUEST['cmbstatus']:'';
$cmb_status_group = isset($_REQUEST['cmb_status_group'])?(string)$_REQUEST['cmb_status_group']:"";
$id 	= isset($_REQUEST['id'])?(string)$_REQUEST['id']:"";
$action_plan = isset($_REQUEST['action_plan'])?mysqli_real_escape_string_wrapper($_REQUEST['action_plan']):'';
$feedback_id = isset($_REQUEST['feedback_id'])?(int)$_REQUEST['feedback_id']:0;
$param = isset($_REQUEST['param'])?$_REQUEST['param']:'';
$assure_care_company_id = isset($_REQUEST['assure_care_company_id'])?$_REQUEST['assure_care_company_id']:'';
$cc_mail_ids = isset($_REQUEST['cc_mail_ids'])?$_REQUEST['cc_mail_ids']:'';
$to_mail_ids = isset($_REQUEST['to_mail_ids'])?$_REQUEST['to_mail_ids']:'';
$lead_stat = isset($_REQUEST['lead_status'])?$_REQUEST['lead_status']:'';
$campusVisit= isset($_REQUEST['campusVisit'])?(int)$_REQUEST['campusVisit']:0;
$scoreBased = isset($_REQUEST['scoreBased'])?(int)$_REQUEST['scoreBased']:1;
$roleFilter = isset($_REQUEST['role_filter'])?(string)$_REQUEST['role_filter']:'';
$send_sms = isset($_REQUEST['send_sms'])?$_REQUEST['send_sms']:'0';
$sid = isset($_REQUEST['sid'])?$_REQUEST['sid']:'';
$round = isset($_REQUEST['round'])?$_REQUEST['round']:'Aptitude';
$roleName = isset($_REQUEST['roleName'])?$_REQUEST['roleName']:'';
$campus_result = isset($_REQUEST['gd_res'])?$_REQUEST['gd_res']:'';
$itype = isset($_REQUEST['itype'])?$_REQUEST['itype']:'';
$resp_json = isset($_REQUEST['resp_json'])?$_REQUEST['resp_json']:''; // Skip mysql escape for this param. Need to do before insert/update
$emailid = isset($_REQUEST['email_id'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['email_id']):'';
$inv_cancel = isset($_REQUEST['inv_cancel'])?$_REQUEST['inv_cancel']:'0';
$inv_id = isset($_REQUEST['inv_id'])?$_REQUEST['inv_id']:'';
$cancel_reason = isset($_REQUEST['cancel_reason'])?mysqli_real_escape_string_wrapper(trim((string)($_REQUEST['cancel_reason']))):'';
$amount = isset($_REQUEST['amts'])?$_REQUEST['amts']:'';
$sortBy = isset($_REQUEST['sortby'])?$_REQUEST['sortby']:'';
$sortType = isset($_REQUEST['sorttype'])?$_REQUEST['sorttype']:'1';
$emp_name = isset($_REQUEST['emp_name'])?mysqli_real_escape_string_wrapper(trim((string)$_REQUEST['emp_name'])):'';
$overwrite_proforma = isset($_REQUEST['overwrite_proforma'])?$_REQUEST['overwrite_proforma']:'0';
$remarks = isset($_REQUEST['remarks'])?mysqli_real_escape_string_wrapper(trim((string)($_REQUEST['remarks']))):'Refresh';
$status = isset($_REQUEST['status'])?$_REQUEST['status']:'';
$receipt_id = isset($_REQUEST['receipt_id'])?$_REQUEST['receipt_id']:'';
if($purpose=='expense_late'){
	$date_split = explode('-', $start_date);
	/* //handled in ui itself 
	 $query = "select GAL_ID from gft_approval_log where GAL_APPROVAL_TYPE=3 and ".
				"GAL_EMP_ID='$employee_id' and GAL_EXPENSE_MONTH='$date_split[1]' and GAL_EXPENSE_YEAR='$date_split[2]' "
	$res_que = execute_my_query($query)
	$no_rows = mysqli_num_rows($res_que)
	if($no_rows > 0){
		echo "Request Sent Already"
		exit;	
	} */
	$approve_url = "show_approve=rep_mgr&emp_id=$employee_id&from_date=$start_date&to_date=$end_date";
	$emp_det = get_emp_master($employee_id);
	$db_content_config = /*. (string[string][int]) .*/array(
		"Employee_Name"=>array($emp_det[0][1]),
		"Reporting_Manager"=>array($emp_det[0][9]),
		"From_Date"=>array($start_date),
		"To_Date"=>array($end_date),
		"AMOUNT"=>array(),
		"Remarks"=>array($reason),
		"url"=>array($approve_url)
	);
	$ins_quer = " insert into gft_approval_log (GAL_APPROVAL_TYPE,GAL_DATE,GAL_APPROVAL_COLUMN,GAL_REMARKS,GAL_EMP_ID, ".
				" GAL_EXPENSE_MONTH,GAL_EXPENSE_YEAR) values (3,now(),'geh_approve_by_rep_id','','$employee_id', ".
				" '$date_split[1]','$date_split[0]') ";
	execute_my_query($ins_quer);
	send_formatted_mail_content($db_content_config, 92, 189, null, null, array($emp_det[0][8]));
	echo "Request Sent to ".$emp_det[0][9];
}else if($purpose=="rep_mgr_app"){
	$start_date_arr = explode('-',$start_date);
	$where_cond = " and GAL_APPROVAL_TYPE=3 and GAL_EMP_ID='$employee_id' and GAL_EXPENSE_MONTH='$start_date_arr[1]' ".
			   		" and GAL_EXPENSE_YEAR='$start_date_arr[0]'  ";
	$sel_res = execute_my_query("select GAL_APPROVAL_BY from gft_approval_log where 1 $where_cond");
	if(mysqli_num_rows($sel_res) > 0){
		$row1 = mysqli_fetch_array($sel_res);
		$approval_by = (int)$row1['GAL_APPROVAL_BY'];
		if($approval_by==0){
			$upd_que1 = " update gft_approval_log set GAL_APPROVAL_BY='$uid', GAL_REMARKS='$reason', GAL_DATE=now()  where 1 $where_cond";
			execute_my_query($upd_que1);
			update_claimed_amt((int)$start_date_arr[1],(int)$start_date_arr[0],(int)$employee_id);
			$upd_que2 = " update gft_exec_expense_hdr set geh_approve_by_rep_id='$uid', geh_approve_by_rep_date=now(), geh_approve_status_by_rep='yes', geh_approve_by_rep_rks='$reason' ".
					" where geh_emp_id='$employee_id' and geh_month='$start_date_arr[1]' and geh_year='$start_date_arr[0]' ";
			execute_my_query($upd_que2);
			echo "Approved";
		}else{
			echo "Already Approved";
		}
	}else{
		echo "Approval Request is Invalid";
	}
}else if($purpose=="joint_visit_partner"){
	$no_of_rows=0;$res_str="";
	if($uid>7000 && $cust_Code!=''){
	    $business_manager_que = " select gem_emp_name bsm_name,cgi_incharge_emp_id bsm_id ".
	   	    " from gft_cp_info ".
	   	    " join gft_emp_master on (cgi_incharge_emp_id=gem_emp_id) ".
	   	    " where CGI_EMP_ID='$uid'";
	    $business_manager_res = execute_my_query($business_manager_que);
	    $bsm_name = $bsm_id = '';
	    if($brow = mysqli_fetch_assoc($business_manager_res)){
	        $bsm_name = $brow["bsm_name"];
	        $bsm_id   = $brow["bsm_id"];
	    }
	    
		$query1=" select em.gem_emp_id incharge_id,em.gem_emp_name incharge_name,h1.ger_reporting_empid r1_id,em1.gem_emp_name r1_name,h2.ger_reporting_empid r2_id," .
		" em2.gem_emp_name r2_name,h3.ger_reporting_empid r3_id,em3.gem_emp_name r3_name from gft_lead_hdr " .
	    " join gft_business_territory_master  on (GLH_TERRITORY_ID=GBT_TERRITORY_ID) join gft_emp_master em on (gbt_sales_incharge=gem_emp_id) " .
	    " left join gft_emp_group_master egm on  (egm.gem_emp_id=em.gem_emp_id) left join gft_emp_reporting  h1 on (h1.GER_EMP_ID=em.gem_emp_id and h1.ger_status='A' ) " .
	    " left join gft_emp_master em1 on (h1.ger_reporting_empid=em1.gem_emp_id ) left join gft_emp_reporting  h2 on (h2.GER_EMP_ID=h1.ger_reporting_empid and h2.ger_status='A' ) " .
	    " left join gft_emp_master em2 on (h2.ger_reporting_empid=em2.gem_emp_id ) left join gft_emp_reporting  h3 on (h3.GER_EMP_ID=h2.ger_reporting_empid and h3.ger_status='A' ) " .
	    " left join gft_emp_master em3 on (h3.ger_reporting_empid=em3.gem_emp_id ) where glh_lead_code='$cust_Code' and GLH_TERRITORY_ID!=100 ";
		$res1=execute_my_query($query1);
		$no_of_rows=mysqli_num_rows($res1);
		if($no_of_rows>0){
		$data1=mysqli_fetch_array($res1);
		$res_str="<option value='0'>Select</option> " .
				" <option value='$data1[incharge_id]'>$data1[incharge_name]</option> " .
		     	" <option value='$data1[r1_id]'>$data1[r1_name]</option> " ;
		if(($data1['r1_id']==$data1['r2_id']) || ($data1['r2_id']==$data1['r3_id'])){
			$res_str.=" <option value='$data1[r2_id]'>$data1[r2_name]</option> " ;
		}else{		
			$res_str.=	" <option value='$data1[r2_id]'>$data1[r2_name]</option> " .
				        " <option value='$data1[r3_id]'>$data1[r3_name]</option> " ;	
		}
		if($bsm_name!='' && $data1['incharge_id']!=$bsm_id && $data1['r1_id']!=$bsm_id && $data1['r2_id']!=$bsm_id && $data1['r3_id']!=$bsm_id){
		    $res_str.=" <option value='$bsm_id'>$bsm_name</option> " ;
		}
		if($cgi_incharge_emp_id!=''){
			$par_con=" or em.gem_emp_id='$cgi_incharge_emp_id' ";
		}else {
			$par_con='';
		}
		}
		/* $query2=" select egm.GEM_EMP_ID ba_emp_id,em.GEM_EMP_NAME ba_emp_name from gft_emp_group_master egm join gft_emp_master em on (egm.gem_emp_id=em.gem_emp_id and gem_status='A')" .
				" join gft_group_master on (ggm_group_id=gem_group_id and ggm_status='A' and ggm_group_type='E') where 1 and (egm.GEM_GROUP_ID='97' $par_con ) "
		$res2=execute_my_query($query2);
		$num_rows=mysqli_num_rows($res2);
		while($num_rows>0){
			$data2=mysqli_fetch_array($res2);
			if($data2['ba_emp_id']!=$data1['r3_id'] and $data2['ba_emp_id']!=$data1['r2_id'] and $data2['ba_emp_id']!=$data1['r1_id'] ){
			$res_str.="<option value='$data2[ba_emp_id]'>$data2[ba_emp_name]</option> " ;
			}
			$num_rows--;
		} */
	}
	if($cust_Code=='' || ($cust_Code!='' && $no_of_rows==0)){
		$query="select a.gem_emp_id incharge_emp_id,gem_emp_name incharge_emp_name from gft_emp_master a where gem_emp_id='$cgi_incharge_emp_id'" .
		" and gem_status='A' " ;
		$res=execute_my_query($query);
		if($data=mysqli_fetch_array($res)){
		      $res_str=  " <option value='0'>Select</option> ".
		                 " <option value='$data[incharge_emp_id]'>$data[incharge_emp_name]</option> " ;
		}
	}
	echo $res_str;
}else if($purpose=="update_new_lead"){
    $created_category = "35";
    $customer_country = "India";
    $lfd_owner = get_lead_mgmt_incharge($customer_country, $state_name, $created_category, $customer_vertical);
	$lead_arr = /*. (string[string]) .*/array();
	$lead_arr['GLH_CUST_NAME'] 		= $customer_name;
	$lead_arr['GLH_COUNTRY'] 		= $customer_country;
	$lead_arr['GLH_CUST_STATECODE'] = $state_name;
	$lead_arr['GLH_LEAD_TYPE'] 		= '1';
	$lead_arr['GLH_STATUS'] 		= '26';
	$lead_arr['GLH_LFD_EMP_ID'] 	= $lfd_owner;
	$lead_arr['GLH_CREATED_BY_EMPID']=$uid;
	$lead_arr['GLH_CREATED_CATEGORY'] = $created_category;
	$lead_arr['GLH_VERTICAL_CODE']	= $customer_vertical;
	$lead_status = array_insert_new_lead_db($lead_arr, null, array($lead_arr['GLH_CUST_NAME']), array($ph_no), array('1'), array('1'));
	$ret_arr = /*. (string[string]) .*/array();
	if($lead_status[0]){
	    create_appointment($lead_status[1], $lfd_owner, $reason, '25', '49', date('Y-m-d'));
	}
	$ret_arr['cust_id'] = $lead_status[1];
	$ret_arr['cust_name'] = $lead_arr['GLH_CUST_NAME'];
	echo json_encode($ret_arr);
}else if($purpose=="get_skew_type"){
	$skew_type = '';
	$query_res = execute_my_query("select GFT_SKEW_PROPERTY from gft_product_master where GPM_PRODUCT_CODE='$pcode' and GPM_PRODUCT_SKEW='$pskew'");
	if($row1 = mysqli_fetch_array($query_res)){
		$skew_type = $row1['GFT_SKEW_PROPERTY'];
	}
	echo $skew_type;
}else if($purpose=='support_history'){
	$ret =  get_dcr_content($employee_id, false);
	echo json_encode($ret);
	
}else if($purpose=="quotaion_approve"){
	if(quotation_approve($quotation_no,$approval_stat,$reason,$uid)){
		$msg = "Status Updated Successfully";
	}else{
		$msg = "Error Occured";
	}
	echo $msg;
}else if($purpose=="proforma_approve"){
	update_proforma_approval_status($proforma_no,$approval_stat,$reason);
	echo "Status Updated Successfully";
}else if($purpose=='inactive_hkey'){
	$update1 = " update gft_hkey_log set GHL_CURRENT_STATUS='I', GHL_UPDATED_BY='$uid' where GHL_ID='$prime_id'";
	if(execute_my_query($update1)){
		echo "Deactivated Successfully";
	}else{
		echo "Error Occured";
	}
}else if($purpose=='assign_to_list'){
	$sel_role=$sel_group="";
	$only_gft = false;
	$assign_emp_list = /*. (string[string]) .*/array();
	if($cmb_status=='T51') { //partner
		$sel_role = '21';
	}else if($cmb_status=='T86') { //pcs
		$sel_role = '30';
	}else if($cmb_status=='T25'){ //pc
		$sel_group = '70';
	}else if($cmb_status=='T87'){ //sales
		$sel_group = '66';
	}else if($cmb_status=='T3'){ //support
		$sel_group = '72';
	}else if($cmb_status=='T20'){  //annuity
		$sel_group = '54';
	}else{
		$only_gft = true;
	}
	if( ($cmb_status=='T87') && ($cust_Code!='') ){
		$sel_query =" select GEM_EMP_ID, GEM_EMP_NAME from gft_lead_hdr ".
					" join gft_business_territory_master on (GBT_TERRITORY_ID=GLH_TERRITORY_ID) ".
					" join gft_emp_master on (GEM_EMP_ID=gbt_sales_incharge and GEM_STATUS='A') ".
					" where GLH_LEAD_CODE='$cust_Code' ";
		$res = execute_my_query($sel_query);
		if($row_data = mysqli_fetch_array($res)){
			$assign_emp_list[$row_data['GEM_EMP_ID']] = $row_data['GEM_EMP_NAME'];
		}
	}
/* 	if(($cmb_status_group=='4') && ($cmb_status!='T35') && ($cust_Code!='')) {
		$lead_dtls_qry = " select glh_cust_statecode,glh_country,GLH_VERTICAL_CODE,GLH_CREATED_CATEGORY ".
					     " from gft_lead_hdr where glh_lead_code = '$cust_Code' "
		$lead_res = execute_my_query($lead_dtls_qry);
		if($row=mysqli_fetch_array($lead_res)) {
			$state = $row['glh_cust_statecode'];
			$country = $row['glh_country'];
			$created_category = $row['GLH_CREATED_CATEGORY'];
			$vertical_id = $row['GLH_VERTICAL_CODE'];
			$annuity_incharge = get_annuity_incharge_based_on_region($country, $state, $created_category, $vertical_id);
			$incharge_name = get_single_value_from_single_table("gem_emp_name", "gft_emp_master", "GEM_EMP_ID", $annuity_incharge);
			if($annuity_incharge !== '' && $incharge_name !== '') {
				$assign_emp_list[$annuity_incharge] = $incharge_name;
			}
		}
	} */
	if(count($assign_emp_list)==0){	
		$supp_employee_list = get_support_exec(false,$sel_group,$sel_role,$only_gft);
		for($i=0;$i<count($supp_employee_list);$i++) {
			$assign_emp_list[$supp_employee_list[$i][0]]=$supp_employee_list[$i][1];
		}
	}
	echo json_encode($assign_emp_list);
}else if($purpose=='sql'){
	$req_data = isset($_REQUEST['req'])?(string)$_REQUEST['req']:'';
	$decrpted_data = base64_decode($req_data);
	$json_arr = json_decode($decrpted_data,true);
	$install_id = isset($json_arr['install_id'])?$json_arr['install_id']:'';
	$sel_que=" select concat(GLH_CUST_NAME,'-',GLH_CUST_STREETADDR2) as cust_name, GID_ORDER_NO, GID_FULLFILLMENT_NO, GID_DB_PASSWORD, concat(GPM_PRODUCT_ABR,' ',substr(GID_LIC_PSKEW,1,4)) as product ".
			 " from gft_install_dtl_new join gft_lead_hdr on (GID_LEAD_CODE=GLH_LEAD_CODE) ".
			 " join gft_product_family_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE) ".
			 " where GID_INSTALL_ID='$install_id' and GID_STATUS!='U' ";
	$res_que = execute_my_query($sel_que);
	$output = "<table class='Formborder1' border=1 cellpadding='1'>";
	while($row_data = mysqli_fetch_array($res_que)){
		$output .= "<tr><th>Customer Name - Location</th><td>".$row_data['cust_name']."</td></tr>".
					"<tr><th>Order No</th><td>".$row_data['GID_ORDER_NO']."</td></tr>".
					"<tr><th>Fullfillment No</th><td>".substr('0000'.$row_data['GID_FULLFILLMENT_NO'],-4)."</td></tr>".
					"<tr><th>Product</th><td>".$row_data['product']."</td></tr>".
					"<tr><th>Database Password</th><td>".$row_data['GID_DB_PASSWORD']."</td></tr>";
	}
	$output .= "</table>";
	$insert_que=" insert into gft_sql_password_log (GSP_EMP_ID,GSP_PASSWORD_FETCHED_LEAD,GSP_PURPOSE,GSP_DATE_TIME) ".
				" values ('$uid','$cust_Code','$reason',now()) ";
	execute_my_query($insert_que);
	echo $output;
}else if($purpose=='port_update'){
	$peer_group_url = get_samee_const("Peergroup_Portcheck_Url")."?purpose=port_check&customerId=$cust_Code";
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$peer_group_url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	$response_json = (string)curl_exec($ch);
	curl_close($ch);
	$response_arr = /*. (string[string]) .*/json_decode($response_json,true);
	if(isset($response_arr['status'])){
		$port_status = 0;
		if($response_arr['status']=='success'){
			$port_status = 1;
		}
		$store_url 	= isset($response_arr['domain'])?$response_arr['domain']:'';
		$port_no 	= isset($response_arr['port'])?$response_arr['port']:'';
		$internetPort = isset($response_arr['internetPort'])?(int)$response_arr['internetPort']:0;
		if($internetPort==0){
			$internetPort = $port_no;
		}
		$upd_query =" update gft_install_dtl_new set GID_PORT_STATUS='$port_status',GID_STORE_URL='$store_url',GID_PORT_NUMBER='$port_no', ".
					" GID_INTERNET_PORT='$internetPort',GID_SERVER_UDPATED_DATE=now() where GID_INSTALL_ID='$prime_id' and GID_STATUS!='U' ";
		execute_my_query($upd_query);
		echo $response_arr['message'];
	}else{
		echo "Error Occurred";
	}
	exit;
}else if($purpose=='port_changed'){
	$portNumber 	= isset($_REQUEST['portNumber'])?(string)$_REQUEST['portNumber']:'';
	$domainName 	= isset($_REQUEST['domainName'])?(string)$_REQUEST['domainName']:'';
	$internetPort 	= isset($_REQUEST['internetPortNumber'])?(int)$_REQUEST['internetPortNumber']:0;
	if($internetPort==0){
		$internetPort = $portNumber;
	}
	$order_full = str_replace('-', "", $lic_no);
	$order_no 	= substr($order_full, 0,15);
	$fullfill_no= substr($order_full, 15,4);
	$upd_query =" update gft_install_dtl_new set GID_PORT_NUMBER='$portNumber',GID_INTERNET_PORT='$internetPort',GID_STORE_URL='$domainName' ".
				" where GID_ORDER_NO='$order_no' and GID_FULLFILLMENT_NO=$fullfill_no and GID_LIC_PCODE in (200,500,501,502,550,551,120) and GID_STATUS!='U' ";
	$res = execute_my_query($upd_query);
	if($res){
		echo "success";
	}else{
		echo "failure";
	}
}else if($purpose=="get_video_comments"){
	$ret_data_arr = get_video_comments($id);
	foreach($ret_data_arr as $val_arr){
		$id1 		= $val_arr['id'];
		$ename 		= $val_arr['name'];
		$comments 	= $val_arr['comment'];
		$time_ago 	= $val_arr['time_ago'];
		$label_val	= ($val_arr['status']=='A')?"Hide":"Disabled";
echo<<<END
<div id='msg-$id1'><div><i class='fa fa-comments' style='color:cornflowerblue;font-size:large;margin-right:10px;'></i><b>$ename</b>&nbsp;
<i style='font-size:small'>$time_ago</i>
END;
if(is_authorized_group($uid,'114')){
	echo "<button class='hide_comments'  style='background-color:aliceblue;border-style:hidden;color:red;';  name='Remove' id='hide-$id1' onclick='deletecomments($id1);'>$label_val</button></div>";
}
echo<<<END
<div style='margin: 0px 0px 10px 30px;'>$comments</div></div>
END;
} 
} else if($purpose=='get_prod_dtls'){
	$payment_id = isset($_REQUEST['ref_id'])?mysqli_real_escape_string_wrapper($_REQUEST['ref_id']):'';
	$resp = '';
	if($payment_id!='') {
		$qry = execute_my_query(" select GSCD_PRODUCT_ID,GCSD_QTY,GCSD_PRICE from gft_store_cart_dtl where GSCD_ID='$payment_id'");
		if(mysqli_num_rows($qry)>0) {
			$resp .= "<table width=100% border=1 style='border-collapse: collapse;'>";
			$resp .= "<tr><th>Name</th><th>Quantity</th><th>Price</th></tr>";
			while($row = mysqli_fetch_array($qry)) {
				$prod = explode("-",$row['GSCD_PRODUCT_ID']);
				$skew_desc_arr = get_data_from_table(array('gpm_skew_desc'), "gft_product_master", array('gpm_product_code','gpm_product_skew'), array($prod[0],$prod[1]));
				$name = (isset($skew_desc_arr[0]) && isset($skew_desc_arr[0]['gpm_skew_desc']))?$skew_desc_arr[0]['gpm_skew_desc']:'';
				$qty = $row['GCSD_QTY'];
				$price = $row['GCSD_PRICE'];
				$resp .= "<tr><td width=40%>$name</td><td width=30%>$qty</td><td width=30%>$price</td></tr>";
			}
			$resp .= "</table>";
			echo $resp;
		} else {
			header('X-PHP-Response-Code: 400', true, 400);
			echo 'No products found in store cart detail';
		}
	} else {
		header('X-PHP-Response-Code: 400', true, 400);
		echo 'Transaction ID is required';
	}
} else if($purpose=='set_session') {
	$cust_id = isset($_REQUEST['cust_id'])?$_REQUEST['cust_id']:'';
	$cust_name = isset($_REQUEST['cust_name'])?$_REQUEST['cust_name']:'';
	$_SESSION['cust_id'] = $cust_id;
	$_SESSION['cust_name'] = $cust_name;
	echo "Success";
}else if($purpose=='employee_info_from_mobile'){
	$sql1 = " select GEM_EMP_ID,GEM_EMP_NAME,GLM_LOGIN_NAME from gft_emp_master ".
			" join gft_login_master on (GLM_EMP_ID=GEM_EMP_ID) ".
			" where GEM_STATUS='A' and GEM_MOBILE='".mysqli_real_escape_string_wrapper($ph_no)."' ";
	$res1 = execute_my_query($sql1);
	$out_arr = /*. (string[string]) .*/array();
	if($row1 = mysqli_fetch_array($res1)){
		$out_arr['status'] 			= "success";
		$out_arr['id'] 				= $row1['GEM_EMP_ID'];
		$out_arr['user_name'] 		= $row1['GLM_LOGIN_NAME'];
		$out_arr['employee_name'] 	= $row1['GEM_EMP_NAME'];
	}else{
		$out_arr['status'] = "failure";
		$out_arr['message'] = "Mobile Number not found in CRM";
	}
	echo json_encode($out_arr);
} else if($purpose=='get_validity_to_dt') {
	$from_dt = isset($_REQUEST['from_dt'])?db_date_format($_REQUEST['from_dt']):'';	
	$to_dt = '';
	if($from_dt!='') {
		$to_qry = " select GVD_TO_DATE from gft_validity_date_change where GVD_FROM_DATE='$from_dt' and GVD_DATETIME=(select max(GVD_DATETIME) from gft_validity_date_change where gvd_from_date='$from_dt') ";
		$to_res = execute_my_query($to_qry);
		if($row = mysqli_fetch_array($to_res)) {
			$to_dt = $row['GVD_TO_DATE'];
		}
	}
	echo json_encode(array('to_dt'=>$to_dt));
} else if($purpose=='chatbot_feedback_reason') {
	$error_msg = '';
	if($feedback_id==0) {
		$error_msg = "Invalid feedback ID";
	} else if($reason=='' || $action_plan=='') {
		$error_msg = "Both reason and next action plan are required";
	}
	if($error_msg!='') {
		show_alert_and_close($error_msg);
		exit;
	}
	execute_my_query(" update chatbot.customer_feedback set agent_reason='$reason', agent_action_plan='$action_plan' where id='$feedback_id' ");
	if(mysqli_errno_wrapper()==0) {
		show_alert_and_close("Successfully updated reason");
	} else {
		show_alert_and_close("Something went wrong while updating. Please try again.");
	}
} elseif($purpose=='support_mis_mail') {
    $mis_report_type = isset($_REQUEST['mis_report_type'])?(int)$_REQUEST['mis_report_type']:0;
    $corporateCode =  isset($_REQUEST['corporateCode'])?(int)$_REQUEST['corporateCode']:0;
	$groups = get_groups_for_param("$param");
	$gps = explode(",",$groups);
	$first_group = $gps[0];
	$hr = date('H');
	$report_from_date= $report_to_date= date('Y-m-d',strtotime("-1 day"));
	if($hr>='11') {
	    $report_from_date = $report_to_date = date('Y-m-d');
	}
	$mail_subject_date = $report_to_date;
	$corporate_lead = "";
	if($mis_report_type==2 && $corporateCode>0 && $param==4){
	    $report_from_date = $start_date;
	    $report_to_date = $end_date;
	    $mail_subject_date = $report_from_date==$report_to_date?"$report_to_date":"$report_from_date-$report_to_date";
	    $corporate_lead = $corporateCode;
	}
	$error_msg = '';
	$to_mail_id_arr = explode(",","$to_mail_ids");
	$cc_mail_id_arr = explode(",","$cc_mail_ids");
	$valid_to_mail_id_arr = $valid_cc_mail_id_arr = /*.(string[int]).*/array();
	$support_gp_id = get_support_group_for_emp($uid);
	$main_prod = get_main_prod_for_param($param);
	$all_support_mail_ids = array();
	$result_mail_ids = execute_my_query("select GSM_SUPPORT_MAIL_ID from gft_support_mail_master where GSM_STATUS=1");
	while($row_mail=mysqli_fetch_assoc($result_mail_ids)){
	    $all_support_mail_ids[] = $row_mail['GSM_SUPPORT_MAIL_ID'];
	}
	$support_team_name = get_single_value_from_single_table("gsp_group_name", "gft_support_product_group", 'gsp_group_id', $first_group);
	if((int)$assure_care_company_id==2) {
	    $main_prod .= (($main_prod!=''?",":"").'37');
	    $support_team_name = "Patanjali Support";
	} else if((int)$assure_care_company_id==3) {
	    $main_prod .= (($main_prod!=''?",":"").'40');
	    $support_team_name = "OYO Support";
	}
	$assure_care_company = $assure_care_company_id;
	if(count(array_intersect($support_gp_id,explode(",",$main_prod)))==0) {
		$error_msg = "You are not authorized to send this email";
	} else if(count($to_mail_id_arr)==1 && trim($to_mail_id_arr[0])=='') {
		$error_msg = "Please enter To Mail Ids for sending email";
	} else {
		foreach($to_mail_id_arr as $email_id) {
			if(is_valid_email(trim($email_id))) {
				$valid_to_mail_id_arr[] = trim($email_id);
			}
		}
		foreach($cc_mail_id_arr as $cc_email_id) {
			if(is_valid_email(trim($cc_email_id))) {
				$valid_cc_mail_id_arr[] = trim($cc_email_id);
			}
		}
		if(count($valid_to_mail_id_arr)==0) {
			$error_msg = "No valid email ID found in to email ID list";
		}
		$all_email_ids = array_merge($to_mail_id_arr, $cc_mail_id_arr);
		$selected_support_ids = array_intersect($all_support_mail_ids, $all_email_ids);
		if(count($selected_support_ids)>0){
		    $error_msg = "You have entered support email id, please remove email ".implode(",", $selected_support_ids)." and send report.";
		}
	}
	if($error_msg!='') {
		show_alert_and_close($error_msg,false);
		js_location_href_to("../support_mis_report.php?param=$param");
		exit;
	}
	$manual_data_topics = get_mis_report_manual_rows((int)$assure_care_company,$corporate_lead);
	$manual_data_entered = /*.(string[int][int]).*/array();
	$keys = "'to_mail_ids','cc_mail_ids'";
	foreach ($manual_data_topics as $k=>$t) {
		$cache_key = str_replace(" ","_",strtolower($t));
		$keys .= ",'$cache_key'";
		$manual_data_entered[] = array("$t",(string)$_REQUEST['ta_'."$k"]);
	}
	$mail_data = get_support_mis_ui("$param", '', $report_from_date, $report_to_date,false,$manual_data_entered,$mis_report_type,$corporateCode);
	$mail_content = "<table style='border: 1px solid black; border-collapse: collapse;' border=1 cellspacing='2' cellpadding='2'>$mail_data[0]</table>";
	$mail_content = str_replace("</table>","</table>".PHP_EOL,$mail_content);
	$mail_content = str_replace("</td>","</td>".PHP_EOL,$mail_content);
	$mail_content = str_replace("</th>","</th>".PHP_EOL,$mail_content);
	$mail_content = str_replace("</tr>","</tr>".PHP_EOL,$mail_content);
	$mail_content_config = array(
	       'from_date1'=>array($mail_subject_date),
	        'msg'=>array($support_team_name),
			'Employee_Name'=>array(get_single_value_from_single_table("gem_emp_name", "gft_emp_master", "gem_emp_id", $uid)),
			'Mail_Content'=>array($mail_content),
	);
	$mail_res = send_formatted_mail_content($mail_content_config,1,297,array((int)$uid),null,$valid_to_mail_id_arr,null,$valid_cc_mail_id_arr,get_email_addr($uid),'','','');
	if($mail_res) {
		$insert_arr = /*.(string[string]).*/array();
		$insert_arr['GMR_EMP_ID'] = $uid;
		$insert_arr['GMR_SUPPORT_GROUP_ID'] = $first_group;
		$insert_arr['GMR_REPORT_DATE'] = $report_to_date;
		$max_id_res = execute_my_query("select max(id) mail_id from gft_outgoing_emails");
		$mail_id = '';
		if($mail_row = mysqli_fetch_array($max_id_res)) {
			$mail_id = $mail_row['mail_id'];
		}
		$insert_arr['GMR_MAIL_ID'] = $mail_id;
		array_insert_query("gft_mis_report_log", $insert_arr);
		show_alert_and_close("Mail sent successfully",false);
		execute_my_query("delete from gft_cache_daily_report where GCD_EMP_ID='$uid' and GCD_KEY in ($keys) ");
	} else {
		show_alert_and_close("Error occurred while sending email",false);
	}
	js_location_href_to("../support_mis_report.php?param=$param&assure_care_company=$assure_care_company_id");
}else if($purpose=='update_pass'){
	$role_vals = explode("*^",$roleFilter);
	$updated_rows = 0;
	$batches = isset($_REQUEST['batch_id'])?/*.(string[int]).*/$_REQUEST['batch_id']:null;
	$batch_cond = '';
	if(count($batches)>0) {
	    $batch_cond = " and gad_batch in ('".implode("','",$batches)."')";
	    if($round=='Programming') {
	        $batch_cond = " and gcp_batch_id in ('".implode("','",$batches)."')";
	    }
	}
	if($round=='Aptitude' || $round=='Programming') {
		foreach ($role_vals as $role_filter) {
			$farr = explode("*@", $role_filter);
			$role_cond = "";
			$role_name = "";
			$dbcol = "GAD_SCORE";
			if($scoreBased==2){
				$dbcol = "GAD_RIGHT";
			}
			$joins = " join gft_aptitude_dtl on (gad_visit_id=gsm_visit_id and gad_student_id=gsm_id) ";
			$result_col = "GSM_APTITUDE_RESULT";
			if($round=='Programming') {
			    $dbcol = "GCP_SCORE";
			    if($scoreBased==4){
			        $dbcol = "GCP_RATING";
			    }
			    $joins = " join gft_campus_programming_result on (gcp_drive_id=gsm_visit_id and gcp_student_id=gsm_id) ";
			    $result_col = "GSM_PROGRAMMING_RESULT";
			}
			if(count($farr) >= 4){
				if($farr[1]==''){
					$role_cond .= " and GSM_PERFORMANCE_LEVEL!=1 ";
				}
				if($farr[2]==''){
					$role_cond .= " and concat(GSM_PERFORMANCE_LEVEL,'-',GSM_ACCEPT_LOW_LEVEL)!='2-1' ";
				}
				if($farr[3]==''){
					$role_cond .= " and concat(GSM_PERFORMANCE_LEVEL,'-',GSM_ACCEPT_LOW_LEVEL)!='2-0' ";
				}
				$role_name = $farr[0];
				$role_cond .= "  and GSM_ROLE='$farr[0]' and if(GSM_PERFORMANCE_LEVEL=1,$dbcol >= '$farr[1]',if(GSM_ACCEPT_LOW_LEVEL=1,$dbcol >= '$farr[2]',$dbcol >= '$farr[3]')) ";
			}
			$up1 = " update gft_student_master $joins ".
			       " set $result_col=2 where GSM_VISIT_ID='$campusVisit' and GSM_ROLE='$role_name' and GSM_PERFORMANCE_LEVEL ".
			       " in (1,2) $batch_cond ";
			$res1 = execute_my_query($up1);
			if(!$res1){
				die('error in reset');
			}
			if($role_cond!=''){
				$up2 = " update gft_student_master $joins ".
						" set $result_col=1 where GSM_VISIT_ID='$campusVisit' and GSM_ROLE='$role_name' ".
						" and GSM_PERFORMANCE_LEVEL in (1,2) $role_cond $batch_cond ";
				$res2 = execute_my_query($up2);
				if($res2){
					$affected_rows = mysqli_affected_rows_wrapper();
					$updated_rows += $affected_rows;
				}else{
					die('error in pass status update');
				}
			}else{
				die('error in forming role condition');
			}
		}
	}
	$resp_msg = $role_names = '';
	if($updated_rows>0 || $round=='kps' || $round=='GD') {
		if($send_sms=='1') {
			$student_ids = array();
			$qry_str = '';
			if($round=='kps' || $round=='GD') {
				$qry_str = "select gsm_id from gft_student_master where GSM_VISIT_ID='$campusVisit' ";
				$chk_qry_str = "";
				if($round=='GD') {
					$qry_str .= " and GSM_GD_RESULT=1 ";
					$chk_qry_str = " select gsm_id from gft_student_master where GSM_VISIT_ID='$campusVisit' and GSM_GD_RESULT ".
								   " not in (1,2) and GSM_KPS_RESULT=1 ";
				} else {
					$qry_str .= " and GSM_KPS_RESULT=1 ";
					$chk_qry_str = " select gkh_student_id from gft_kps_hdr join gft_student_master on (gkh_student_id=gsm_id) where ".
									" GSM_VISIT_ID='$campusVisit' and GSM_KPS_RESULT not in (1,2) and GSM_APTITUDE_RESULT=1 ";
				}
				if($roleName!='') {
					$role_names = implode("','",explode(",",$roleName));
					$qry_str .= " and GSM_ROLE in ('$role_names') ";
					$chk_qry_str .= " and GSM_ROLE in ('$role_names') ";
				}
				$chk_qry = execute_my_query($chk_qry_str);
				if(mysqli_num_rows($chk_qry)>0) {
					echo mysqli_num_rows($chk_qry)." students' result is pending".($role_names!=''?"in '".$role_names."' roles ":'').'. Update the status as Selected or Not selected and Send SMS.';
					exit;
				}
				$res = execute_my_query($qry_str);
				while($row = mysqli_fetch_array($res)) {
					$student_ids[] = $row['gsm_id'];
				}
			} else {
				$resp_msg .= " $updated_rows student records updated as Passed. ";
				if($roleName!='') {
					$role_names = implode("','",explode(",",$roleName));
					$qry_str .= " and GSM_ROLE in ('$role_names') ";
				}
				$chk_qry_str = " select * from gft_aptitude_dtl join gft_student_master on (GAD_STUDENT_ID=gsm_id) ".
							   " where gsm_visit_id='$campusVisit' and gsm_aptitude_result not in (1,2) $qry_str $batch_cond";
				$sms_list_qry = " select gsm_id from gft_student_master join gft_aptitude_dtl on (gad_student_id=gsm_id) ".
				                " where gsm_visit_id='$campusVisit' and gsm_aptitude_result=1 $qry_str $batch_cond ";
				if($round=='Programming') {
				    $chk_qry_str = " select * from gft_campus_programming_result join gft_student_master on (GCP_STUDENT_ID=gsm_id) ".
								   " where gsm_visit_id='$campusVisit' and gsm_programming_result not in (1,2) $qry_str $batch_cond";
				    $sms_list_qry = " select gsm_id from gft_student_master join gft_campus_programming_result on (GCP_STUDENT_ID=gsm_id) ".
								    " where gsm_visit_id='$campusVisit' and gsm_programming_result=1 $qry_str $batch_cond ";
				}
				$chk_qry = execute_my_query($chk_qry_str);
				if(mysqli_num_rows($chk_qry)>0) {
					echo mysqli_num_rows($chk_qry)." students' result is pending ".($role_names!=''?"in '".$role_names."' roles. SMS not sent":'');
				}
				$student_id_qry = execute_my_query($sms_list_qry);
				while($student_row = mysqli_fetch_array($student_id_qry)) {
					$student_ids[] = $student_row['gsm_id'];
				}
			}
			$resp_msg .= send_sms_for_campus_drive(($round=='kps'?'KPS':$round),$student_ids,$campusVisit,$role_names,$batches);
		}
		echo ($resp_msg==''?'SMS not sent':$resp_msg);
	} else  {
		echo "No update";
	}
} else if($purpose=='start_kps_assessment') {
	if((int)$employee_id==0 || (int)$sid==0) {
		echo json_encode(array("status"=>false,"message"=>"Student ID and employee id parameters are required"));
		exit;
	}
	execute_my_query("update gft_student_master set gsm_kps_emp='$employee_id', gsm_kps_result='4' where gsm_id='$sid'");
	if(mysqli_affected_rows_wrapper()>0) {
		echo json_encode(array("status"=>true,"message"=>"Assessment started"));
	} else {
		echo json_encode(array("status"=>false,"message"=>"Unable to start session"));
	}
} else if($purpose=='student_role_change') {
	execute_my_query("update gft_student_master set gsm_role = '$roleFilter' where gsm_id='$sid' and gsm_visit_id='$campusVisit'");
	if(mysqli_affected_rows_wrapper()==1) {
		show_alert_and_close("Successfully changed the role");
	} else {
		show_alert_and_close("Couldn't update role");
	}
} else if($purpose=='update_gd_result') {
	$res_file_path = $ws_file_path = array();
	if($campus_result=='1') {
		$res_file_path = upload_files_to("../$attach_path/campus_drive/$campusVisit/resume/$sid","res_file");
		$ws_file_path = upload_files_to("../$attach_path/campus_drive/$campusVisit/worksheet/$sid","ws_file");
	}
	$file_path1 = isset($res_file_path[0])?$res_file_path[0]:'';
	$file_path2 = isset($ws_file_path[0])?$ws_file_path[0]:'';
	$curr_files_qry = "select GSM_RESUME_PATH,GSM_WORKSHEET_PATH from gft_student_master where gsm_id='$sid'";
	$curr_files_res = execute_my_query($curr_files_qry);
	$curr_Resume_path = ''; $curr_ws_path = '';
	if($curr_files_row = mysqli_fetch_assoc($curr_files_res)) {
	    $curr_Resume_path = $curr_files_row['GSM_RESUME_PATH'];
	    $curr_ws_path = $curr_files_row['GSM_WORKSHEET_PATH'];
	}
	$update_qry = '';
	if($file_path1!='') {
	    $update_qry .= (($update_qry!=''?',':'')." GSM_RESUME_PATH='$file_path1' ");
	}
	if($file_path2!='') {
	    $update_qry .= (($update_qry!=''?',':'')." GSM_WORKSHEET_PATH='$file_path2' ");
	}
	if($update_qry!='') {
    	execute_my_query(" update gft_student_master set $update_qry where gsm_id='$sid' and gsm_visit_id='$campusVisit' ");
    	if(mysqli_affected_rows_wrapper()==1) {
    	    if($file_path1!='' && $curr_Resume_path!='') {
        	    unlink($curr_Resume_path);
        	}
        	if($file_path2!='' && $curr_ws_path!='') {
        	    unlink($curr_ws_path);
        	}
    	}
	}
	show_alert_and_close("Successfully uploaded.");
} else if($purpose=='interview_start') {
    if($sid=='' || $sid=='0') {
		show_alert_and_close("Student ID is required");
		exit;
	}
	if($itype=='') {
		show_alert_and_close("Please choose an interview type");
		exit;
	}
	$itype_arr = explode("-",$itype); 
	$type_str = ($itype_arr[0]=='H')?'HR':'Functional';
	$type = ($itype_arr[0]=='H')?3:2;
	$ound_id = isset($itype_arr[1])?$itype_arr[1]:'';
	$rid = create_campus_interview_round($type, $sid, $ound_id);
	$round_id_new = get_single_value_from_single_table("gci_round_no", "gft_campus_interviews", "gci_id", $rid);
	if($rid!='') {
		show_my_alert_msg("New $type_str Interview created. Round $round_id_new");
		js_location_href_to("/../edit_campus_drive_dtls.php?purpose=interview_form&GSM_ID=$sid&visit=$campusVisit&itype=$type&round_no=$round_id_new");
	} else {
		show_my_alert_msg("Couldn't create a new $type_str interview");
	}
} else if($purpose=='get_current_kps_status') {
    if($sid=='' || $sid=='0') {
		show_alert_and_close("Student ID is required");
		exit;
	}
	if($campusVisit=='' || $campusVisit=='0') {
		show_alert_and_close("Campus drive ID is required");
		exit;
	}
	$chk_qry = "select gem_emp_name from gft_student_master join gft_emp_master on (gsm_kps_emp=gem_emp_id) where gsm_id='$sid' and gsm_visit_id='$campusVisit' and gsm_kps_result='4' ";
	$chk_res = execute_my_query($chk_qry);
	if($curr_row = mysqli_fetch_array($chk_res)) {
		$empname = $curr_row['gem_emp_name'];
		$dtl = "KPS evaluation is currently in-progress by $empname. Do you want to do assessment?";
	} else {
		$dtl = "Do you want to do assessment?";
	}
	echo json_encode(array('message'=>$dtl));
} else if($purpose=='update_kps_emp') {
    if($employee_id=='' || $employee_id=='0') {
		show_alert_and_close("Employee ID is required");
		exit;
	}
	if($sid=='' || $sid=='0') {
		show_alert_and_close("Student ID is required");
		exit;
	}
	if($campusVisit=='' || $campusVisit=='0') {
		show_alert_and_close("Campus drive ID is required");
		exit;
	}
	execute_my_query("update gft_student_master set gsm_kps_emp='$employee_id', gsm_kps_result=if(gsm_kps_result not in (1,2),4,gsm_kps_result) where gsm_id='$sid' and gsm_visit_id='$campusVisit'");
	if(mysqli_affected_rows_wrapper()==1) {
		echo json_encode(array('message'=>'Assesment started.'));
	} else {
		header('X-PHP-Response-Code: 500', true, 500);
		echo json_encode(array('message'=>"Couldn't start assessment. Please try again."));
	}
} else if($purpose=='save_highlight') {
    if($resp_json=='' || $campusVisit=='' || $sid=='') {
		show_my_alert_msg("Required Fields: Student ID, Visit ID and Response");
		exit;
	}
	$all_ans = /*.(string[int]).*/json_decode($resp_json,true);
	foreach ($all_ans as $qid=>$resp) {
		$resp = mysqli_real_escape_string_wrapper(htmlentities($resp));
		execute_my_query("update gft_kps_dtl join gft_kps_hdr on (gkh_id=gkd_id) set GKD_RESPONSE='$resp' where gkh_visit_id=$campusVisit and gkh_student_id='$sid' and GKD_QUESTION_ID='$qid'");
	}
	echo json_encode(array('message'=>"Successfully Updated",'status'=>true));
} else if($purpose=='check_is_kit_quote') {
	if($quotation_no=='') {
		show_my_alert_msg("Quotation no. id required for checking kit skew");
		echo 'false';
	}
	$pcode_skews = $comma = '';
	$prods = json_decode($quotation_no);
	foreach($prods as $p) {
		$split = explode("-",$p);
		$pcode_skews .= "$comma'".$split[0]."-".$split[1]."'";
		$comma = ',';
	}
	$chk_qry = execute_my_query(" select * from gft_product_master where GPM_PRODUCT_TYPE in ('14','15','16','17','18') ".
			   " and concat(gpm_product_code,'-',gpm_product_skew) in ($pcode_skews) ");
	if(mysqli_num_rows($chk_qry)>0) {
		echo 'true';
	} else {
		echo 'false';
	}
} else if($purpose=='check_chat_feedback_access') {
	$agent_id = get_single_value_from_single_table("agent_id", "chatbot.customer_feedback", "id", $feedback_id);
	if($agent_id==$employee_id) {
		echo json_encode(array('status'=>true));
	} else {
		echo json_encode(array('status'=>false));
	}
} else if($purpose=='booking_audit') {
	$status_labels = get_booking_status_labels();
	$htm = '<table class="table table-striped table-hover">';
	if($id!='') {
		$aaudit_qry = " select GBA_UPDATED_ON,gem_emp_name,GBA_PREV_STATUS,GBA_CURRENT_STATUS,GBA_COMMENTS from gft_booking_audit_dtl ".
					  " join gft_emp_master on (gem_emp_id=GBA_UPDATED_BY) where gba_booking_id='$id' order by GBA_UPDATED_ON desc ";
		$audit_res = execute_my_query($aaudit_qry);
		if(mysqli_num_rows($audit_res)>0) {
			$sl = 0;
			$htm .= '<tr><th>S.no</th><th>Previous Status</th><th>New Status</th><th>Comments</th><th>Updated By</th><th>Updated On</th></tr>';
			while($audit_dat = mysqli_fetch_array($audit_res)) {
				$sl++;
				$htm .= "<tr><td>$sl</td>";
				$htm .= "<td>".(isset($status_labels[$audit_dat['GBA_PREV_STATUS']])?$status_labels[$audit_dat['GBA_PREV_STATUS']]:'--')."</td>";
				$htm .= "<td>".(isset($status_labels[$audit_dat['GBA_CURRENT_STATUS']])?$status_labels[$audit_dat['GBA_CURRENT_STATUS']]:'--')."</td>";
				$htm .= "<td>".$audit_dat['GBA_COMMENTS']."</td>";
				$htm .= "<td>".$audit_dat['gem_emp_name']."</td>";
				$htm .= "<td>".$audit_dat['GBA_UPDATED_ON']."</td></tr>";
			}
		}
	}
	$htm .= '</table>';
	echo $htm;
} else if($purpose=='booking_attachments') {
	$htm = '';
	if($id!='') {
		$aaudit_qry = " select GBT_PATH,gem_emp_name,GBT_UPLOADED_ON from gft_booking_attachments ".
					  " join gft_emp_master on (gem_emp_id=GBT_UPLOADED_BY) where GBT_BOOKING_ID='$id' order by GBT_UPLOADED_ON desc ";
		$audit_res = execute_my_query($aaudit_qry);
		if(mysqli_num_rows($audit_res)>0) {
			$sl = 0;
			$htm = '<table class="table table-striped table-hover">';
			$htm .= '<tr><th>S.no</th><th>File</th><th>Uploaded By</th><th>Uploaded On</th></tr>';
			while($audit_dat = mysqli_fetch_array($audit_res)) {
				$sl++;
				$path_arr = pathinfo($audit_dat['GBT_PATH']);
				$file_name = $path_arr['basename'];
				$htm .= "<tr><td>$sl</td>";
				$htm .= "<td><a target=_blank href='file_download.php?type=view&file_type=bookings&filename=$id/$file_name'>$file_name</a></td>";
				$htm .= "<td>".$audit_dat['gem_emp_name']."</td>";
				$htm .= "<td>".$audit_dat['GBT_UPLOADED_ON']."</td></tr>";
			}
			$htm .= '</table>';
		} else {
			$htm = '<h2>No Attachments found</h2>';
		}
	}
	echo $htm;
} else if($purpose=='check_exist_email_id') {
	$error_msg = check_exist_email_id(str_replace('@'.get_samee_const("OFFICEAL_MAIL_DOMAIN"),'',$emailid));
	if($error_msg=='') {
		echo json_encode(array('status'=>'success'));
	} else {
		echo json_encode(array('status'=>'failed','message'=>$error_msg));
	}
} else if( ($purpose=='find_existing_quote_proforma') || ($purpose=='validate') ){
    $prod_dtl = json_decode(json_decode($pcode));
	foreach ($prod_dtl as $code_skew){
	    $arr = explode("-", $code_skew);
	    $ord_qty = isset($arr[2]) ? (int)$arr[2] : 0;
	    $common_err_msg = common_sku_validation($arr[0], $arr[1], $ord_qty, $cust_Code);
	    if($common_err_msg!=''){
	        echo json_encode(array('status'=>'error','message'=>$common_err_msg));
            exit;
        }
	}
	if($purpose=='validate'){
	    echo json_encode(array('status'=>'success'));
	    exit;
	}
	echo check_existing_quote_proforma($employee_id, $cust_Code, $reason, $quotation_no,$prod_dtl);
}else if($purpose=='appointmentOnDate') {
	$appointment_date 	= isset($_REQUEST['appointment_date'])?$_REQUEST['appointment_date']:"";
	$show_pending_appointment 	= isset($_REQUEST['show_pending_appointment'])?$_REQUEST['show_pending_appointment']:"";
	$appointment_date = date('Y-m-d', strtotime($appointment_date));
	if($appointment_date==""){
		$error['message'] = "Select customer name/date of appointment.";
		send_failure_response($error,HttpStatusCode::BAD_REQUEST);exit;
	}
	$appointment_list = array();
	$sql_query =" select GLD_LEAD_CODE, GLH_CUST_NAME, gam.GAM_ACTIVITY_DESC, ".
				" CONCAT(ad.GLD_NEXT_ACTION_DATE, ' ' ,COALESCE(ad.GLD_NEXT_ACTION_TIME, '')) GLD_NEXT_ACTION_DATE, gcs_name from gft_activity ad ".
				" join gft_activity_master gam on (ad.GLD_NEXT_ACTION=gam.GAM_ACTIVITY_ID ) ".
				" JOIN gft_lead_hdr lh on(ad.GLD_LEAD_CODE=glh_lead_code AND GLH_LEAD_TYPE!=8) ". 
				" INNER join gft_customer_status_master on gcs_code=glh_status ".
				" WHERE  ad.GLD_NEXT_ACTION_DATE='$appointment_date'  AND ad.GLD_EMP_ID='$uid' AND GLD_SCHEDULE_STATUS in (1,3)".
				" UNION ALL".
				" select GCF_LEAD_CODE as GLD_LEAD_CODE, GLH_CUST_NAME, GAM_ACTIVITY_DESC, GCF_FOLLOWUP_DATE as GLD_NEXT_ACTION_DATE,".
				" gcs_name from gft_cplead_followup_dtl  fl ".
				" join gft_activity_master gam on (fl.GCF_FOLLOWUP_ACTION=gam.GAM_ACTIVITY_ID )  ".
				" JOIN gft_lead_hdr lh on(fl.GCF_LEAD_CODE=glh_lead_code AND GLH_LEAD_TYPE!=8)  ".
				" INNER join gft_customer_status_master on gcs_code=glh_status  ".
				" WHERE  fl.GCF_ACTUAL_FOLLOWUP_DATE='$appointment_date'  AND fl.GCF_ASSIGN_TO='$uid' AND gcf_followup_status in (1,3) ";
	$result_app = execute_my_query($sql_query);
	if($show_pending_appointment=="true"){
		$response_array['numberOfAppointment']		= mysqli_num_rows($result_app);
		echo json_encode($response_array);exit;
	}
	while($row_app=mysqli_fetch_array($result_app)){
		$lead_code	=	$row_app['GLD_LEAD_CODE'];
		$appointment['lead_code'] 		= "<a style='text-decoration:none;' href='visit_details.php?lead_code=$lead_code' target='_blank'>".$lead_code."<a>";
		$appointment['customer_name'] 	= $row_app['GLH_CUST_NAME'];
		$appointment['type'] 			= $row_app['GAM_ACTIVITY_DESC'];
		$appointment['date'] 			= $row_app['GLD_NEXT_ACTION_DATE'];
		$appointment['lead_status'] 	= $row_app['gcs_name'];
		$appointment_list[]				= $appointment;
	}
	$response_array['table_title']		= array("Lead Code","Customer Name","Next Action","Appointment On","Lead Status");
	$response_array['table_data']		= $appointment_list;
	echo json_encode($response_array);exit;
} else if($purpose=='cancel_invoice') {
	$failed_msg = '';
	if($inv_id=='' || $inv_id=='0' || !is_numeric($inv_id)) {
		$failed_msg = "Invoice ID is required";
	} else if($inv_cancel=='1' && (trim($cancel_reason)=='' || ($amount!='1' && $amount!='2'))) {
		$failed_msg = "Please provide a reason and choose option for making amounts 0";
	}
	if($failed_msg!='') {
		send_failure_response(array('message'=>$failed_msg), 400);
		exit;
	}
	$curr_time = date('Y-m-d H:i:s');
	execute_my_query("update gft_invoice_hdr set gih_inv_cancelled_by='$uid',gih_cancel_comments='$cancel_reason',gih_status='C',gih_cancelled_datetime='$curr_time' where gih_invoice_id='$inv_id' ");
	if(mysqli_errno_wrapper()==0) {
		if($amount=='1') {
			execute_my_query("update gft_invoice_hdr set gih_net_invoice_amount='0',gih_status='C' where gih_invoice_id='$inv_id'");
			if(mysqli_errno_wrapper()==0) {
				execute_my_query("update gft_invoice_product_dtl set gip_amount='0',GIP_CESS_AMT='0',GIP_IGST_AMT='0',".
								 "GIP_SGST_AMT='0',GIP_CGST_AMT='0' where gip_invoice_id='$inv_id'");
				if(mysqli_errno_wrapper()==0) {
					echo json_encode(array('message'=>'Updated invoice status'));
				} else {
					send_failure_response(array('message'=>"Couldn't invoice product amounts. Please try again."), 500);
					exit;
				}
			} else {
				send_failure_response(array('message'=>"Couldn't total invoice amount. Please try again."), 500);
				exit;
			}
		} else {
			echo json_encode(array('message'=>'Updated invoice status'));
		}
	} else {
		send_failure_response(array('message'=>"Couldn't update status. Please try again."), 500);
		exit;
	}
} else if($purpose=='fetch_alliance_partner_addons') {
    if($cust_Code=='' || $cust_Code=='0' || !is_numeric($cust_Code)) {
		send_failure_response(array("message"=>"Invalid partner customer id"), 400);
		exit;
	}
	$addon_prods = get_addon_prods_for_partner($cust_Code);
	if(count($addon_prods)>0) {
		echo json_encode($addon_prods);
	} else {
		send_failure_response(array("message"=>"No products found for partner"), 400);
		exit;
	}
} else if($purpose=='fetch_custs_for_addon' || $purpose=='get_commission_dtls_for_prod') {
    if($pcode=='' || $pskew=='' || $cust_Code=='') {
		send_failure_response(array('message'=>"Required params product,from month,to month and partner name"), 400);
		exit;
	}
	$end_date = db_date_format($end_date);
	if($end_date!='') {
	    $end_date = date('Y-m-t',strtotime($end_date));
	}
	$renewal_last_month = '';
	if($purpose=='fetch_custs_for_addon' && ($start_date=='' || $end_date=='')) {
		send_failure_response(array('message'=>"Start and End months are required"), 400);
		exit;
	}
	$partner_emp_id = get_single_value_from_single_table("cgi_emp_id", "gft_cp_info", "cgi_lead_code", $cust_Code);
	$last_prices = get_customer_price_for_addon($pcode,$pskew,$cust_Code);
	$resp_arr = array();
	$pi_dtls = get_active_proforma_for_partner_addon($cust_Code, $pcode, $pskew,$start_date,$end_date);
	$partner_remarks = '';
	$utr_no = '';
	if(count($pi_dtls)==1 || $purpose=='fetch_custs_for_addon') {
	$pi_no = $pi_dtls[0]['pi_no'];
	$approval_status = $pi_dtls[0]['approval_status'];
	$renewal_start_dt = $pi_dtls[0]['renewal_start_date'];
	if($pi_dtls[0]['renewal_end_date']!='' && $renewal_start_dt!='') {
    	$renewal_end_dt = date('Y-m-t',strtotime($pi_dtls[0]['renewal_end_date']));
    	if($purpose=='fetch_custs_for_addon' && $overwrite_proforma=='0' &&
    	    (strtotime($renewal_start_dt)!=strtotime(db_date_format($start_date)) || 
    	        strtotime($renewal_end_dt)!=strtotime(db_date_format($end_date))) &&
    	    ((strtotime($start_date)<=strtotime(db_date_format($renewal_start_dt)) &&
    	        strtotime($end_date)>=strtotime(db_date_format($renewal_start_dt)))|| 
    	        (strtotime($start_date)<=strtotime(db_date_format($renewal_end_dt)) &&
    	            strtotime($end_date)>=strtotime(db_date_format($renewal_end_dt))))) {
    	    $duration_string = date('M Y',strtotime($renewal_start_dt))." to ".date('M Y',strtotime($renewal_end_dt));
    	    if($renewal_start_dt==date('Y-m-01',strtotime($renewal_end_dt))) {
    	        $duration_string = date('M Y',strtotime($renewal_start_dt));
    	    }
//      	send_failure_response(array('message'=>"There is an active reminder for $duration_string. Do you want to overwrite that?"), 403)
    	    send_failure_response(array('message'=>"There is an active reminder for $duration_string"), 400);
    	    exit;
    	}
	}
	$partner_remarks = $pi_dtls[0]['approval_remarks'];
	$utr_no = $pi_dtls[0]['utr_no'];
	$selected_dtl = array();
	if($pi_no!='-1') {
		$selected_dtl = get_selected_outlet_dtls($pi_no);
		$qry = <<<END
select gac_start_date,gac_end_date from gft_add_on_commission_dtl join gft_lead_hdr on (glh_lead_code=gac_lead_code)
join gft_proforma_hdr on (gac_order_no=gph_order_no) where gph_order_no='$pi_no'
END;
		$res = execute_my_query($qry);
		while($pi_row = mysqli_fetch_array($res)) {
			$start_date = $pi_row['gac_start_date'];
			$renewal_last_month = $pi_row['gac_end_date'];
			$end_date = date('Y-m-t',strtotime($renewal_last_month));
		}
// 	} else if($purpose=='get_commission_dtls_for_prod') {
// 		send_failure_response(array('message'=>"No active reminders present for the selected product"), 400);
// 		exit;
	}
	$last_order_dtls = get_last_order_no($pcode,$pskew,$cust_Code);
	$ord_start_date = $last_order_dtls['order_date'];
	if($ord_start_date=='') {
		$ord_start_date = $start_date;
	}
	$curr_order_custs = get_cust_for_add_on_orders($pcode,$pskew,$partner_emp_id,$ord_start_date,$end_date);
	$custs = array_merge($last_prices,$curr_order_custs);
	$present_cust = array();
	$outlets = array();
	$all_outlets = array();
	$all_custs = array();
	foreach($custs as $cust_dtl) {
		if(!isset($outlets[(string)$cust_dtl['cust_id']])) {
			$outlets[(string)$cust_dtl['cust_id']] = array();
		}
		if(!in_array($cust_dtl['cust_id'],$present_cust)) {
			$present_cust[] = $cust_dtl['cust_id'];
			foreach($cust_dtl['outlets'] as $ol) {
				if(!in_array($ol['lead_code'],$all_outlets)) {
					$outlets[(string)$cust_dtl['cust_id']][] = $ol;
					$all_outlets[] = $ol['lead_code'];
				}
			}
			$all_custs[] = array("cust_id"=>$cust_dtl['cust_id'],"cust_name"=>$cust_dtl['cust_name'],"price"=>$cust_dtl['price']);			
		} else {
			foreach($cust_dtl['outlets'] as $ol) {
				if(!in_array($ol['lead_code'],$all_outlets)) {
					$outlets[(string)$cust_dtl['cust_id']][] = $ol;
					$all_outlets[] = $ol['lead_code'];
				}
			}
		}
	}
	foreach($all_custs as $k=>$c_row) {
		$all_custs["$k"]['outlets'] = isset($outlets[(string)$c_row['cust_id']])?$outlets[(string)$c_row['cust_id']]:array();
	}
	$resp_arr['custs'] = $all_custs;//array_merge($last_prices,$curr_order_custs)
	if($purpose=='get_commission_dtls_for_prod') {
		$month_str = '';
		if(strtotime($start_date)==strtotime($renewal_last_month)) {
			$month_str = date('M Y',strtotime($start_date));
		} else {
			$month_str = date('M Y',strtotime($start_date))." to ".date('M Y',strtotime($end_date));
		}
		$resp_arr['months'] = $month_str;
		$resp_arr['from_month'] = date('m-Y',strtotime($start_date));
		$resp_arr['to_month'] = date('m-Y',strtotime($end_date));
	}
	$resp_arr['proforma_no'] = $pi_no;
	$resp_arr['proforma_status'] = $approval_status;
	$resp_arr['selected_dtl'] = $selected_dtl;
	$resp_arr['partner_same_state'] = (is_same_state($cust_Code)?'1':'0');
// 	$partner_emails = get_email_addr_customer($cust_Code)
	$partner_emails = array();
	$query_cust = execute_my_query(" select gcc_contact_no,gcc_id from gft_customer_contact_dtl where ".
	                               " gcc_contact_type=4 and gcc_lead_code='$cust_Code' and gcc_designation in (1,2,3,4) ");
	while($em_row = mysqli_fetch_assoc($query_cust)) {
	    $partner_emails[$em_row['gcc_id']] = $em_row['gcc_contact_no'];
	}
// 	if(trim($partner_emails)!='') {
// 		$partner_email_arr = explode(",",$partner_emails);
// 	}
	$resp_arr['emails'] = $partner_emails;	    
	$resp_arr['partner_remarks'] = $partner_remarks;
	$resp_arr['utr_no'] = $utr_no;
	} else if($purpose=='get_commission_dtls_for_prod') {
	    if(count($pi_dtls)>=1) {
	        $resp_arr['pis'] = $pi_dtls;
	    } else {
	        send_failure_response(array('message'=>"No active reminders present for the selected product"), 400);
	        exit;
	    }
	}
	echo json_encode($resp_arr);
} else if($purpose=='get_zoho_mail_data') {
	$response = /*.(mixed[string]).*/array();
	if($start_date=='') {
		$start_date = date('Y-m-d');
	}
	if($end_date=='') {
		$end_date = date('Y-m-d');
	}
	$wh_con = " and GMH_RECEIVED_DATETIME>='$start_date 00:00:00' and GMH_RECEIVED_DATETIME<='$end_date 23:59:59' and GMH_IS_SPAM=0 ";
	if($support_gp!='' && $support_gp!='0') {
 		$wh_con .= " and GMH_SUPPORT_GROUP_ID='$support_gp' ";
 	}
 	if($cmb_status!='' && $cmb_status!='0') {
 		$wh_con .= " and GMH_CURRENT_STATUS='$cmb_status' ";
 	}
 	$zmials_qry = " select SQL_CALC_FOUND_ROWS ifnull(concat('#',gmh_ticket_no),gmh_mail_ticket_id) gmh_mail_ticket_id, ".
 	       " gmh_mail_ticket_id t_num,gmh_from_mail_id,gmh_lead_code,glh_cust_name,glh_cust_streetaddr2, ".
 		   " gsp_group_name,gmh_subject,gmh_received_datetime,gmh_current_status,gmh_closed_datetime, ".
 		   " datediff(date(now()),gmh_received_datetime) dys from gft_zoho_mail_hdr ".
 		   " left join gft_lead_hdr on (glh_lead_code=gmh_lead_code) ".
 		   " left join gft_support_product_group on (gmh_support_group_id=gsp_group_id) where 1 $wh_con ";
 	$offs = '0';
 	if($offset!='' && is_numeric($offset) && $offset!='0') {
 		$offs = $offset*200;
 	}
 	if($sortBy!='') {
 		$zmials_qry .= (" order by $sortBy ".($sortType=='2'?' desc ':''));
 	} else {
 		$zmials_qry .= " order by gmh_received_datetime ";
 	}
 	$zmials_qry .= " limit $offs,200 ";
 	$zmials_res = execute_my_query($zmials_qry);
 	$row_count_res = execute_my_query(" select FOUND_ROWS() row_count ");
 	$row_count = 0;
 	if($cnt_row = mysqli_fetch_array($row_count_res)) {
 		$row_count = $cnt_row['row_count'];
 	}
 	if($row_count<$offs) {
 		$offset -= 1;
 	}
 	$cnt = mysqli_num_rows($zmials_res);
 	$mails = array();
 	while($zmail_rows = mysqli_fetch_array($zmials_res)) {
 		$this_row = array();
 		$this_row[] = $zmail_rows['t_num'];
 		$this_row[] = $zmail_rows['gmh_mail_ticket_id'];
 		$this_row[] = $zmail_rows['dys']." days";
 		$this_row[] = $zmail_rows['gsp_group_name'];
 		$this_row[] = ($zmail_rows['gmh_lead_code']>0?$zmail_rows['glh_cust_name']."-".$zmail_rows['glh_cust_streetaddr2']:'Unknown');
 		$this_row[] = $zmail_rows['gmh_subject'];
 		$this_row[] = $zmail_rows['gmh_current_status'];
 		$this_row[] = $zmail_rows['gmh_from_mail_id'];
 		$this_row[] = $zmail_rows['gmh_received_datetime'];
 		$this_row[] = $zmail_rows['gmh_closed_datetime'];
 		$mails[] = $this_row;
 	}
	$response['table_data'] = $mails;
	$response['data_align'] = array("left","left","left","left","left","left","left","left","left");
	$response['numRows'] = $cnt;
	$response['offset'] = $offset;
	$response['totalRows'] = $row_count;
	$response['table_title'] = array('Ticket ID','Ticket No.',"Age","Team","Customer","Subject","Status","Email ID","Received Time","Closed Time");
	$response['table_sort'] = array('t_num','gmh_mail_ticket_id','dys','gsp_group_name','glh_cust_name','','gmh_current_status','','gmh_received_datetime','gmh_closed_datetime');
	$response['sortType'] = ($sortType=='1'?'2':'1');
	$response['sortBy'] = ($sortBy!=''?$sortBy:'gmh_received_datetime');
	echo json_encode($response);
} else if ($purpose=='get_emp_name') {
    $wh = "";
    if(is_numeric($roleFilter) && (int)$roleFilter>0) {
        $wh = " and gem_role_id='$roleFilter' ";
    } else if((int)$param>0) {
        $wh = " and gem_group_id=54 ";
    }
	$emp_name_qry = " select gem.gem_emp_name,gem.gem_emp_id from gft_emp_master gem ".
					" left join gft_emp_group_master ggm on (gem.GEM_EMP_ID=ggm.gem_emp_id) where gem_status='A' and ".
					" gem.gem_emp_id<7000 $wh and gem_emp_name like '%$emp_name%' ".
					" group by gem.gem_emp_id order by gem_emp_name limit 25 ";
	$emp_name_res = execute_my_query($emp_name_qry);
	$earr = array();
	while($emps_row = mysqli_fetch_array($emp_name_res)) {
		$earr[] = array('id'=>$emps_row['gem_emp_id'],'label'=>$emps_row['gem_emp_name']);
	}
	echo json_encode($earr);
} else if($purpose=='pcs_ordered_sku'){
	$quer = " select GPM_SKEW_DESC,concat(GPM_PRODUCT_CODE,'-',GPM_PRODUCT_SKEW) as pid, ".
			" sum(GPA_TOTAL_HRS) tot_hrs , sum(GPA_USED_HRS) used_hrs from gft_order_hdr ".
			" join gft_pcs_activity_order_hdr on (GOD_ORDER_NO=GPA_ORDER_NO) ".
			" join gft_product_master on (GPA_PRODUCT_CODE=GPM_PRODUCT_CODE and GPA_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" left join gft_cp_order_dtl on (GCO_ORDER_NO=GOD_ORDER_NO and GCO_PRODUCT_CODE=GPA_PRODUCT_CODE and GCO_SKEW=GPA_PRODUCT_SKEW) ".
			" where if(GOD_EMP_ID between 7000 and 9998,GCO_CUST_CODE='$cust_Code',GOD_LEAD_CODE='$cust_Code') and GOD_ORDER_STATUS='A' ".
			" group by pid ";
	$res1 = execute_my_query($quer);
	$list_arr = /*. (string[int][string]) .*/array();
	if(mysqli_num_rows($res1)>0){
	    while ($row1 = mysqli_fetch_array($res1)){
	        $tot_hrs	= (float)$row1['tot_hrs'];
	        $used_hrs 	= round((float)$row1['used_hrs'],2);
	        $avail_hrs	= round($tot_hrs - $used_hrs,2);
	        $list_arr[] = array(
	            'id'=>$row1['pid'],
	            'name'=>$row1['GPM_SKEW_DESC'],
	            'total_hrs'=>$tot_hrs,
	            'used_hrs'=>$used_hrs,
	            'available_hrs'=>$avail_hrs
	        );
	    }
	}else{
	    $sku_res = execute_my_query("select GPM_SKEW_DESC,concat(GPM_PRODUCT_CODE,'-',GPM_PRODUCT_SKEW) as pid from gft_product_master where GFT_SKEW_PROPERTY=25 and gpm_status='A'");
	    while ($sku_row=mysqli_fetch_assoc($sku_res)){
	        $list_arr[] = array(
	            'id'=>$sku_row['pid'],
	            'name'=>$sku_row['GPM_SKEW_DESC'],
	            'total_hrs'=> 0,
	            'used_hrs'=> 0,
	            'available_hrs'=> 0
	        );
	    }
	}
	echo json_encode($list_arr);
} else if($purpose=='update_unhappy_cust_status') {
    $error_msg = '';
    if(!is_numeric($id) || (int)$id<=0) {
        $error_msg = 'Activity ID is required';
    } else if(trim($action_plan)=='') {
        $error_msg = 'Enter description of action taken';
    } else if(!is_numeric($employee_id) || (int)$employee_id<=0) {
        $error_msg = 'Employee ID is required';
    }
    if($error_msg!='') {
        show_my_alert_msg($error_msg);
    } else {
        update_unhappy_customer($action_plan,$id,$employee_id,true);
    }
    js_location_href_to($_SERVER['HTTP_REFERER']);
} else if($purpose=='l2_transfers_detail') {
    $end_date = db_date_format($end_date);
    if($start_date=='' || $end_date=='' || (int)$employee_id<=0 || (int)$param<=0 || (int)$itype<=0) {
        send_failure_response(array("message"=>"Required query params missing"), 400);
        exit;
    }
    $qry = get_l2_transfer_dtl_qry($start_date,$end_date,$employee_id,$param,($itype=='1'?true:false),true);
    $res = execute_my_query($qry);
    $resparr = /*.(mixed[string]).*/array();
    $resparr['numberOfAppointment'] = mysqli_num_rows($res);
    $list_vals = array();
    $sl = 0;
    while($l2_row = mysqli_fetch_assoc($res)) {
        $curr_row = array();
        $sl++;
        $curr_row['sno'] = $sl;
        $chat_id = $l2_row['chat_id'];
        $curr_row['chat_id'] = "<a href='chatbot_transcript.php?chat_id=$chat_id' target=_blank>$chat_id</a>";
        $curr_row['cust_name'] = $l2_row['glh_cust_name'];
        $curr_row['trans_time'] = $l2_row['created_date'];
        $curr_row['trans_by'] = $l2_row['from_name'];
        $curr_row['trans_to'] = $l2_row['to_name'];
        $curr_row['reason'] = $l2_row['trans_reason'];
        $list_vals[] = $curr_row;        
    }
    $resparr['table_title'] = array("S.No","Chat ID","Customer Name","Transfer Time","Received From","Transferred To","Reason");
    $resparr['table_data']	= $list_vals;
    $resparr['data_align'] = array("center","center","center","center","center","center","left");
    echo json_encode($resparr);
    exit;
} else if($purpose=='get_possible_lead_status') {
    $next_stat_ids = LeadStatusStateMachine::getInstance()->getNextPossibleStateWithName($lead_stat);
    echo json_encode($next_stat_ids);
} else if($purpose=='show_inv_cancel_dtls') {
    if((int)$inv_id>0) {
        $cancel_qry = " select ifnull(gem_emp_name,'--') gem_emp_name,ifnull(gih_cancelled_datetime,'--') gih_cancelled_datetime, ".
                      " ifnull(gih_cancel_comments,'--') gih_cancel_comments from gft_invoice_hdr ".
                      " left join gft_emp_master on (gem_emp_id=gih_inv_cancelled_by) where gih_status='C' and gih_invoice_id='$inv_id' ";
        $cancel_res = execute_my_query($cancel_qry);
        $cancel_html = '';
        if($can_row = mysqli_fetch_assoc($cancel_res)) {
            $cancel_emp = $can_row['gem_emp_name'];
            $cancel_comments = $can_row['gih_cancel_comments'];
            $cancel_time = $can_row['gih_cancelled_datetime'];
            echo <<<END
<table style='width:90%;margin:auto;border-collapse:collapse;' cellpadding=2 border=1>
<tr>
<th style='text-align:right;'>Cacelled By</th>
<td>$cancel_emp</td></tr><tr>
<th style='text-align:right;'>Cacelled Date & Time</th>
<td>$cancel_time</td></tr><tr>
<th style='text-align:right;'>Comments / Reason</th>
<td>$cancel_comments</td></tr>
</table>
END;
            exit;
        } else {
            echo "<h3 style='color:red;'>Invoice cancel details not found</h3>";
            exit;
        }
    } else {
        echo "<h3 style='color:red;'>Invoice ID required</h3>";
        exit;
    }
} else if($purpose=='get_split_wrapup_status') {
    $resp_arr = array();
    if(is_numeric($id) && (int)$id>0) {
        $split_dtl_qry = " select split_query,review_status,em1.gem_emp_name wrapup_by,reviewed_on,review_comment,support_agent_id, ".
                         " em2.gem_emp_name support_agent,start_time,end_time,chat_id from chatbot.split_chat_dtl ".
                         " left join gft_emp_master em1 on (reviewed_by=em1.gem_emp_id) ".
                         " left join gft_emp_master em2 on (support_agent_id=em2.gem_emp_id) ".
                         " where split_id='$id' ";
        $split_dtl_res = execute_my_query($split_dtl_qry);
        if($split_row = mysqli_fetch_assoc($split_dtl_res)) {
            $resp_arr['status'] = 'success';
            $resp_arr['review_status'] = $split_row['review_status'];
            $resp_arr['agent_name'] = $split_row['wrapup_by'];
            $resp_arr['wrapup_time'] = $split_row['reviewed_on'];
            $resp_arr['wrapup_comments'] = $split_row['review_comment'];
            $resp_arr['split_query'] = $split_row['split_query'];
            $resp_arr['support_agent'] = $split_row['support_agent_id'];
            $resp_arr['support_agent_name'] = $split_row['support_agent'];
            $start_time = strtotime($split_row['start_time']);
            $end_time = strtotime($split_row['end_time']);
            if($end_time=='') {
                $chat_conv_id = $split_row['chat_id'];
                $last_dtl = get_last_transcript_time_id($chat_conv_id);
                $end_time = strtotime($last_dtl[1]);
            }
            $duration = $end_time-$start_time;
            $duration_str = get_duration_in_string_from_seconds($duration);
            $resp_arr['duration_str'] = $duration_str;
        } else {
            $resp_arr['status'] = 'failure';
        }
    } else {
        $resp_arr['status'] = 'failure';
    }
    echo json_encode($resp_arr);
} else if($purpose=='leave_cancel') {
    $resp_arr = array();
    if(isset($uid) && is_authorized_group_list($uid, array('19'))) {
        if(intval($id)>0) {
            $leave_dtls_qry = " select gelr_halfday,gelr_to_date,gelr_from_date,gelr_leave_type,gelr_emp_id,gels_status, ".
                              " if(gelr_session='AN','Afternoon','Forenoon') sess from gft_emp_leave_request ".
                              " join gft_emp_leave_req_status on (gelr_id=gels_lr_id) where gelr_id='$id' ";
            $leave_dtl_res = execute_my_query($leave_dtls_qry);
            $halfday = '';$requested_by = '';$leaveType = '';$leave_emp_id = '';$fromDate = '';$toDate = '';$curr_status = ''; $sess = '';
            if($leave_data = mysqli_fetch_array($leave_dtl_res)) {
                $halfday = $leave_data['gelr_halfday'];
                $requested_by = $leave_data['gelr_emp_id'];
                $leaveType = $leave_data['gelr_leave_type'];
                $fromDate = $leave_data['gelr_from_date'];
                $toDate = $leave_data['gelr_to_date'];
                $leave_emp_id = $leave_data['gelr_emp_id'];
                $curr_status = $leave_data['gels_status'];
                $sess = $leave_data['sess'];
            }
            if($curr_status!=$cmb_status) {
                $dat = array();
                $field_names = array('CL'=>'gelt_cl','SL'=>"gelt_sl","CSL"=>"gelt_csl","PL"=>"gelt_pl_taken","ML"=>"gelt_ml_taken","P"=>"gelt_permissions","PRL"=>"gelt_pl","OD"=>"gelt_od_taken",'IB'=>'gelt_ib_taken','PR'=>'gelt_pr_taken');
                $leave_type_labels = array('CL'=>'Casual Leave','SL'=>'Sick Leave','P'=>'Permission','CSL'=>'Casual/Sick Leave',"ML"=>"Maternity Leave","PL"=>"Paternity Leave",'PRL'=>'Privileged Leave','OD'=>'On-Duty','IB'=>'Internship Break','PR'=>'Project Review');
                $dat['fromDate']  = $fromDate;
                $dat['toDate']	  = $toDate;
                $dat['halfDay']	  = ($halfday=='Y')?"true":"false";
                $dat['fieldName'] = $field_names[$leaveType];
                $dat['emp_id']	  = $leave_emp_id;
                $common_obj = new Common();
                $day_counts = $common_obj->getEmpLeaveType($dat,($cmb_status=='28'?true:false));
                $getDays = $day_counts['getDays'];
                $column_name = $dat['fieldName'];
                $today_days = $day_counts['totalDays'];
                $remainingDays = $day_counts['remainingDays'];
                execute_my_query(" update gft_emp_leave_req_status set gels_status='$cmb_status',gels_cancel_comments='$cancel_reason', ".
                                 " gels_taken_days=0, gels_remaining_days='$remainingDays',gels_cancel_by_emp='$uid' where gels_lr_id='$id' ");
                execute_my_query(" UPDATE gft_emp_leave_type set $column_name = '$remainingDays' WHERE gelt_emp_id = '$leave_emp_id' ");
                $leave_string = "from $fromDate to $toDate";
                if($halfday=='Y') {
                    $leave_string = "on $fromDate ($sess)";
                } else if($fromDate==$toDate) {
                    $leave_string = "on $fromDate";
                }
                $status_string = ($cmb_status=='28'?'Revoked':'Granted');
                $leave_type_string = $leave_type_labels[$leaveType];
                $db_content_config = array(
                    'Leave_Msg'=>array("The status of your $leave_type_string $leave_string has marked as $status_string"),
                    'Leave_Reason'=>array($cancel_reason),
                    'Employee_Name'=>array(get_single_value_from_single_table("gem_emp_name", "gft_emp_master", "gem_emp_id", $uid)),
                    'Leave_Details'=>array('Leave status change')
                );
                send_formatted_mail_content($db_content_config,63,276,array($requested_by));
                $resp_arr['message'] = "Updated successfully";
            } else {
                header('X-PHP-Response-Code: 400', true, 400);
                $resp_arr['message'] = "No change to status";
            }
        } else {
            header('X-PHP-Response-Code: 400', true, 400);
            $resp_arr['message'] = 'Leave ID is required';
        }
    } else {
        header('X-PHP-Response-Code: 400', true, 400);
        $resp_arr['message'] = 'You are not authorized to perform this action';
    }
    echo json_encode($resp_arr);
} else if($purpose=='get_adj_amt_for_customer') {
    if(intval($cust_Code)<=0) {
        header('X-PHP-Response-Code: 400', true, 400);
        echo json_encode(array('message'=>'Lead code is required'));
        exit;
    }
    $amt_qry = " select ifnull(sum(ifnull(gop_sell_rate,0)*ifnull(gop_qty,0)*if(gop_coupon_hour is not null and gop_coupon_hour>0,gop_coupon_hour,1)),0) tot_order_amt, ".
               " ifnull(goa_quotation_no,'') goa_quotation_no from (select god_order_no,gop_product_code,gop_product_skew,gop_sell_rate,gop_qty,gop_coupon_hour,qh.GQH_ORDER_NO goa_quotation_no ".
               " from gft_order_hdr join gft_order_product_dtl on (god_order_splict=0 and gop_order_no=god_order_no) ".
               " join gft_product_master on (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew and ".
               " gft_skew_property in (1,2,3,11)) left join gft_customer_order_adjustment on (god_lead_code=goa_lead_code) ".
               " left join gft_quotation_hdr qh ON(qh.GQH_ORDER_NO=goa_quotation_no AND GQH_ORDER_STATUS='A')".
               " where god_order_status='A' and god_lead_code='$cust_Code' ".
               " union all ".
               " select god_order_no,gop_product_code,gop_product_skew,gop_sell_rate,gco_cust_qty gop_qty, ".
               " gco_coupon_hour gop_coupon_hour,goa_quotation_no from gft_order_hdr ".
               " join gft_order_product_dtl on (god_order_splict=1 and gop_order_no=god_order_no) ".
               " join gft_cp_order_dtl on (gco_order_no=gop_order_no and gco_product_code=gop_product_code and gco_skew=gop_product_skew) ".
               " join gft_product_master on (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew and ".
               " gft_skew_property in (1,2,3,11)) left join gft_customer_order_adjustment on (gco_cust_code=goa_lead_code) ".
               " left join gft_quotation_hdr qh ON(qh.GQH_ORDER_NO=goa_quotation_no  AND GQH_ORDER_STATUS='A')".
               " where god_order_status='A' and gco_cust_code='$cust_Code') dtls ";
    $amt_res = execute_my_query($amt_qry);
    $total_amt = 0;
    if($amt_row = mysqli_fetch_assoc($amt_res)) {
       $total_amt = $amt_row['tot_order_amt'];
       if($amt_row['goa_quotation_no']!='' && $amt_row['goa_quotation_no']!=$quotation_no) {
           header('X-PHP-Response-Code: 400', true, 400);
           $cust_name = get_single_value_from_single_table("concat(glh_cust_name,ifnull(concat(' - ',GLH_CUST_STREETADDR2),''))","gft_lead_hdr","glh_lead_code",$cust_Code);
           echo json_encode(array('message'=>"Upgradation adjustment is already completed for $cust_name. So, using this customer's orders for upgradation is not allowed."));
           exit;
       }
    }
    echo json_encode(array("message"=>$total_amt));
}else if($purpose=='get_pcs_incentive_amount'){
    $inc_amt = 0;
    $output = array();
    $time_spent 	= round( (($spent_hrs*60) + $spent_mins)/60, 4);
    if($type!="internal_billable"){
        $prod_arr		= explode("-", $prod);
        $product_code	= $prod_arr[0];
        $product_skew	= isset($prod_arr[1])?$prod_arr[1]:'';
        $order_que = " select GPA_ORDER_NO from gft_pcs_activity_order_hdr ".
            " join gft_order_hdr on (GOD_ORDER_NO=GPA_ORDER_NO) ".
            " left join gft_cp_order_dtl on (GCO_ORDER_NO=GOD_ORDER_NO and GCO_PRODUCT_CODE=GPA_PRODUCT_CODE and GCO_SKEW=GPA_PRODUCT_SKEW) ".
            " where if(GOD_EMP_ID between 7000 and 9998,GCO_CUST_CODE='$cust_Code',GOD_LEAD_CODE='$cust_Code') and GOD_ORDER_STATUS='A' and GPA_PRODUCT_CODE='$product_code' and GPA_PRODUCT_SKEW='$product_skew' ".
            " and GPA_TOTAL_HRS > GPA_USED_HRS group by GPA_ORDER_NO order by GOD_CREATED_DATE ";
        $result = execute_my_query($order_que);
        if($rw=mysqli_fetch_assoc($result)){
            $order_no = $rw["GPA_ORDER_NO"];
            $que = " select sum(GOP_QTY*GOP_SELL_RATE) as sell_rate,GPA_TOTAL_HRS,GPA_USED_HRS from gft_order_product_dtl ".
                " join gft_pcs_activity_order_hdr on (GPA_ORDER_NO=GOP_ORDER_NO and GPA_PRODUCT_CODE=GOP_PRODUCT_CODE and GPA_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
                " where GOP_ORDER_NO='$order_no' and GOP_PRODUCT_CODE='$product_code' and GOP_PRODUCT_SKEW='$product_skew' ";
            $res1 = execute_my_query($que);
            $processing_percent = 0.60; //60%
            if($row1 = mysqli_fetch_array($res1)){
                $tot_sell_rate 	= $row1['sell_rate'] * $processing_percent;
                $tot_hrs		= $row1['GPA_TOTAL_HRS'];
                $gpa_used	    = (float)$row1['GPA_USED_HRS'];
                $avail_hrs 	    = round($tot_hrs-$gpa_used,4);
                $time_consumed =  ($time_spent < $avail_hrs) ? $time_spent : $avail_hrs;
                
                $per_hr_cost	= round($tot_sell_rate/$tot_hrs,2);
                $inc_amt		= round($per_hr_cost * $time_consumed, 2);
            }
        }
    }else{
        $service_amount = get_single_value_from_single_table('GMM_SERVICE_TYPE','gft_pcs_milestone_master','GMM_ID', 10);
        $inc_amt = round(($service_amount/8)*$time_spent,2);
    }
    $output['inc_amt'] = (float)$inc_amt;
    echo json_encode($output);
}else if($purpose=='addon_edit_details'){
    $edit_by_emp = isset($_REQUEST['edit_by_emp'])?(string)$_REQUEST['edit_by_emp']:'';
    $no_of_device = isset($_REQUEST['no_of_device'])?(string)$_REQUEST['no_of_device']:'';
    $exp_dt = isset($_REQUEST['exp_dt'])?(string)$_REQUEST['exp_dt']:'';
    $edit_reason = isset($_REQUEST['edit_reason'])?(string)$_REQUEST['edit_reason']:'';
    $row_id = isset($_REQUEST['row_id'])?(string)$_REQUEST['row_id']:0;
    $return_resp = array();
    if($edit_by_emp=='' || $edit_by_emp==0 || $exp_dt=='' || $edit_reason=='' || $row_id==0){
        $return_resp["message"] = "invalid_data";
    }else{
        $exp_dt = date('Y-m-d',strtotime($exp_dt));
        $sel_que = execute_my_query("select GAI_LEAD_CODE,date(GAI_VALIDITY_DATETIME) old_exp_dt,GAI_NO_OF_DEVICE as old_clients from gft_addon_feature_install_dtl where  GAI_ID=$row_id ");
        if($sel_rw = mysqli_fetch_array($sel_que)){
            $old_exp_dt  = $sel_rw["old_exp_dt"];
            $old_clients = $sel_rw["old_clients"];
            $table_name  = "gft_addon_feature_install_dtl";
            if($old_exp_dt==$exp_dt && $old_clients==$no_of_device){
                $return_resp["message"] = "already_exists";
            }else{
                execute_my_query("update gft_addon_feature_install_dtl set GAI_NO_OF_DEVICE=$no_of_device,GAI_VALIDITY_DATETIME='$exp_dt' where GAI_ID=$row_id ");
                post_instock_custom_license($sel_rw["GAI_LEAD_CODE"]);
                $return_resp["message"] = "success";
                if($old_exp_dt!=$exp_dt){
                    $insert_arr = array(
                        "GIA_INSTALL_ID" => $row_id,
                        "GIA_COLUMN_NAME"=> "GAI_VALIDITY_DATETIME",
                        "GIA_OLD_VALUE"  => $old_exp_dt,
                        "GIA_NEW_VALUE"  => $exp_dt,
                        "GIA_REASON"     => "$edit_reason",
                        "GIA_EDITED_BY"  => $edit_by_emp,
                        "GIA_EDITED_DATE"=> date('Y-m-d H:i:s'),
                        'GIA_TABLE_NAME' => $table_name
                    );
                    array_insert_query("gft_install_audit_log",$insert_arr);
                }
                if($old_clients!=$no_of_device){
                    $insert_arr = array(
                        "GIA_INSTALL_ID"  => $row_id,
                        "GIA_COLUMN_NAME" => "GAI_NO_OF_DEVICE",
                        "GIA_OLD_VALUE"   => $old_clients,
                        "GIA_NEW_VALUE"   => $no_of_device,
                        "GIA_REASON"      => "$edit_reason",
                        "GIA_EDITED_BY"   => $edit_by_emp,
                        "GIA_EDITED_DATE" => date('Y-m-d H:i:s'),
                        'GIA_TABLE_NAME'  => $table_name
                    );
                    array_insert_query("gft_install_audit_log",$insert_arr);
                }
                
            }
        }
    }
    echo json_encode($return_resp);
    exit;
}else if($purpose=="check_reporting"){
    if($employee_id!="" && $employee_id!=0){
        $rque = " select GER_REPORTING_EMPID from gft_emp_reporting  join gft_emp_master on (GER_EMP_ID=gem_emp_id and gem_status='A') where GER_REPORTING_EMPID=$employee_id and GER_STATUS='A' ";
        $reporting_id = get_single_value_from_single_query("GER_REPORTING_EMPID",$rque);
        $output['message'] = false;
        if($reporting_id!=""){
            $output['message'] = true;
        }
        echo json_encode($output);
        exit;
    }
}else if($purpose == "validate_skew_allow_discount"){
	$pcode_skews = $no_disc_skews = $pcodes = $skews = array();
	$pcode_skews = $_REQUEST['pcode_skews'];
	$pcodeskews = "";
	if(count($pcode_skews)>0){
		foreach($pcode_skews as $k=>$v){
			list($pcode,$skew) = explode('-',$v);
			$pcodeskews .= ($pcodeskews!=""?",":"")."'$pcode-$skew'";
		}
		$no_disc_skews = validate_to_allow_skew_discount($pcodeskews);
	}
	echo json_encode($no_disc_skews);
}else if($purpose=="emp_history"){
   $edit_que = " select GEA_ACTIVITY_ID,GEA_COLUMN,GEA_PREV_VAL,GEA_NEW_VAL,em.gem_emp_name as updated_by,GEA_REASON,ap.gem_emp_name as approved_by,GEA_DATETIME ".
    " from gft_emp_dtl_audit ". 
    " join gft_emp_master em on (GEA_UPDATED_BY=em.gem_emp_id) ".
    " left join gft_emp_master ap on (GEA_APPROVED_BY=ap.gem_emp_id) ".
    " where GEA_EMP_ID=$employee_id order by GEA_DATETIME desc ";
   $edit_res = execute_my_query($edit_que);
   $output = array();
   $content = "<table class='grid_table'><thead><tr class='thead'><th>Updated date</th><th>Updated by</th><th>Reason</th><th>Approved by</th><th>Column Name</th><th>Previous value</th><th>New Value</th></tr></thead><tbody>";
   $result = false;
   $value_arr = array();
   $same_val = array();
   while($edit_row=mysqli_fetch_assoc($edit_res)){
       $result = true;
       $date = date('M d,Y H:i:s',strtotime($edit_row["GEA_DATETIME"])).'****'.$edit_row["updated_by"].'****'.$edit_row["GEA_REASON"]."****".$edit_row["approved_by"];
       $value_arr[$date][] = array($edit_row["GEA_COLUMN"],$edit_row["GEA_PREV_VAL"],$edit_row["GEA_NEW_VAL"]);
   }
   $output = "";
   foreach ($value_arr as $key => $arr){
       $rowspan = count($arr);
       $dta = explode("****", $key);
       $output.= " <tr> <td rowspan=$rowspan>$dta[0]</td><td rowspan=$rowspan>$dta[1]</td><td rowspan=$rowspan>$dta[2]</td><td rowspan=$rowspan>$dta[3]</td>";
       foreach ($arr as $nm => $val){
           $output .= "<td>$val[0]</td><td>$val[1]</td><td>$val[2]</td>";
           $output .="</tr>";
       }
   }
   $content .= "$output</tbody></table>";
   if(!$result){
       $content = "<h4 class='no_dtl'>No history...</h4>";
   }
   echo $content;
}else if($purpose=="alr_sub_status" && $alr_status_id!=0){
    $res = execute_my_query(" select GAS_ID,GAS_NAME from gft_alr_sub_status where GAS_STATUS_ID=$alr_status_id ");
    $option = "<option value=0>Select</option>";
    while ($row=mysqli_fetch_assoc($res)) {
        $selected = ($alr_old_sub_status==$row["GAS_ID"]) ? "selected='selected'" : "";
        $option .= "<option value=".$row["GAS_ID"]." $selected>".$row["GAS_NAME"]."</option>";
    }
    
    echo $option;
}else if($purpose=="update_gstin_edc_date" && $receipt_id!='' && $end_date!=''){
    $res = execute_my_query(" update gft_receipt_dtl set GRD_EXPECTED_TO_UPDATE_GSTIN='$end_date' where GRD_GSTIN_UPDATE_STATUS=3 and GRD_RECEIPT_ID='$receipt_id' ");
    echo json_encode(array("status"=>"1","message"=>"Updated Successfully"));
}else if($purpose=='post_advance_status'){
    if($status==4){
        $receipt_ids = get_samee_const("Skip_Receipt_Id_For_DR_Validation");
        $receipt_ids = ($receipt_ids=="") ? $sid : $receipt_ids.','.$sid;
        execute_my_query(" update gft_samee_const set GSC_VALUE='$receipt_ids' where GSC_NAME='Skip_Receipt_Id_For_DR_Validation'  ");
    }else{
        $cur_date = date('Y-m-d H:i:s');
        execute_my_query(" update gft_collection_receipt_dtl set GCR_ORDER_NO=$status,GCR_REASON=1,GCR_REALIZED_BY=$uid,GCR_REALIZED_ON='$cur_date',GCR_REMARKS='$remarks' where GCR_RECEIPT_ID=$sid and GCR_REASON='-1' ");  
    }
    $output['message'] = "Updated Successfully";
    echo json_encode($output); 
}else {
	echo "Invalid Purpose";
}
?>
