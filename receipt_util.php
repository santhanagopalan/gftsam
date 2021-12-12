<?php
require_once(__DIR__ ."/visit_submit_in_popup.php");
require_once(__DIR__ ."/function.update_in_hdr.php"); 
require_once(__DIR__ ."/common_filter.php");
require_once(__DIR__ ."/product_util.php");

/**
 * @param int $uid
 * @param int $lead_code
 * @return int
 */
function emp_for_pc_support_collection($uid,$lead_code) {
	$is_support_group = is_authorized_group_list($uid, array(8));
	$emp_id=0;
	if($is_support_group) {
		$sales_roles = get_roles_of_group('66'); //Field Sales
		$sales_roles = explode(",",$sales_roles);
		$lead_owner = get_single_value_from_single_table("glh_lfd_emp_id","gft_lead_hdr","glh_lead_code",$lead_code);
		$lead_owner_role = get_single_value_from_single_table("gem_role_id","gft_emp_master","gem_emp_id",$lead_owner);
		$lead_owner_status = get_single_value_from_single_table("gem_status","gft_emp_master","gem_emp_id",$lead_owner);
		if(in_array($lead_owner_role,$sales_roles) and $lead_owner_status=='A') {
			$emp_id = $lead_owner;
		} else { //Partner, other than sales person, inactive sales person/partner
			$terr_id = get_single_value_from_single_table("GLH_TERRITORY_ID","gft_lead_hdr","glh_lead_code",$lead_code);
			if($terr_id!='' and $terr_id!=100) {
				$emp_id = get_terr_incharge($terr_id);
			}
			else {
				$reporting_mgr_qry=execute_my_query("select ger_reporting_empid from gft_emp_reporting where ger_emp_id='$uid' and ger_status='A'");
				if($r=mysqli_fetch_array($reporting_mgr_qry)) {
					$emp_id=$r['ger_reporting_empid'];
				}
			}
		}
	}
	return $emp_id;
}
/**
 * @param string $call_from
 * 
 * @return string
 */
function receipt_submit_procedure($call_from){
    $collected_amt = '' ;
    $emp_id = '';
    $grd_receipt_id = '';
	global $uid;
	$submit_status="Saved";
	$discount_update = true;
	$group_id=isset($_POST['group_id'])?(int)$_POST['group_id']:0;
	if($call_from=="order"){
		$discount_update = false;
	}
	if(isset($_POST['receipt_details'])){
	    $gstin_update_date = "";
		$collection_for	=	isset($_POST['collection_for'])?$_POST['collection_for']:array();
		$quotation_collection = isset($_POST['quotation_collection'])?$_POST['quotation_collection']:"";
		$proforma_collection = isset($_POST['proforma_collection'])?$_POST['proforma_collection']:"";
		$collection_against = isset($_POST['collection_against'])?$_POST['collection_against']:"0";
		$advance1 = isset($_POST['advance1'])?$_POST['advance1']:"0";
		$customer_gstin_status = isset($_POST['customer_gstin_status'])?(int)$_POST['customer_gstin_status']:0;
		if($customer_gstin_status==3){
		    $gstin_update_date = isset($_POST['gstin_update_date'])?$_POST['gstin_update_date']:"";
		}
		$collection_against_no="";
		$check_pending_dev_ticket = false;
		$dev_proforma_check_arr = array();
		if($collection_against==1 && $advance1=='on'){
			$collection_against_no=$quotation_collection;
		}else if($collection_against==2 && $advance1=='on'){
			$collection_against_no=$proforma_collection;
		}
		$collection_type	=	"";
		if(count($collection_for)>0){
			$collection_type	=	implode(',', $collection_for);
		}
		$lead_code=(string)$_POST['lead_code'];	
		//$emp_for_pc_support_collection=emp_for_pc_support_collection($uid, $lead_code);
		//$emp_id=($emp_for_pc_support_collection!==0)?$emp_for_pc_support_collection:(string)$_POST['emp_id'];
		$emp_id=(string)$_POST['emp_id'];
		$GRD_RECEIPT_ID_given=(string)$_POST['rid'];
		$GRD_PAYMENT_FORID=isset($_POST['ptype']) ? /*. (string[int]) .*/$_POST['ptype'] : "";
		$GRD_RECEIPT_TYPE=(string)$_POST['rtype'];
		$GRD_RECEIPT_AMT=(string)$_POST['ramt'];
		$GRD_CHEQUE_NO=mysqli_real_escape_string_wrapper((string)$_POST['cno']);
		$GRD_CHEQUE_DATE=(string)$_POST['cheque_date'];
		if($GRD_RECEIPT_TYPE=="5"){  //paypal
			$GRD_CHEQUE_NO = mysqli_real_escape_string_wrapper((string)$_POST['utid']);
		}elseif ($GRD_RECEIPT_TYPE=="7"){ //Net Transfer
			$GRD_CHEQUE_NO = mysqli_real_escape_string_wrapper((string)$_POST['utrno']);
			$GRD_CHEQUE_DATE = (string)$_POST['transfer_date'];
		}
		$GRD_CHEQUE_NO = trim($GRD_CHEQUE_NO);
		$GRD_BANK_NAME=(string)$_POST['bname'];
		$GRD_VISIT_DATE=(string)$_POST['visit_date'];
		$GRD_NOTE=trim(stripslashes((string)$_POST['collection_terms']));
		$GRD_NOTE=trim(get_valid_text($GRD_NOTE));
		$GRD_NOTE=(string)str_replace("'","",$GRD_NOTE);
		$GRD_NOTE=(string)str_replace("\n","",$GRD_NOTE);
		$order_no_list=/*. (string[int]) .*/$_POST['order_no_coll'];
		$collected_amt=/*. (string[int]) .*/$_POST['c_amount'];
		$remarks=/*. (string[int]) .*/ $_POST['remarks'];
		//$remarks=(isset($_POST['remarks'])?trim(stripslashes($_POST['remarks'])):'');
		//$remarks=str_replace("'","",$remarks);
		//$remarks=str_replace("\n","",$remarks);
		$GCR_OTHERS_LISTED_PRICE=/*. (string[int]) .*/$_POST['ass_listed_amount'];
		$order_no_elist=isset($_POST['order_no_e'])?/*. (string[int]) .*/$_POST['order_no_e']:/*. (string[int]) .*/array();
		$product_elist=isset($_POST['cmbproduct_e'])?/*. (string[int]) .*/$_POST['cmbproduct_e']:/*. (string[int]) .*/array();
		$collected_eamt=/*. (string[int]) .*/$_POST['collection_eamt'];
		$eremarks=/*. (string[int]) .*/$_POST['eremarks'];
		$adv_amt = isset($_POST['adv_amt'])?(string)$_POST['adv_amt']:'';
		$adv_remarks = isset($_POST['adv_remarks'])?mysqli_real_escape_string_wrapper((string)$_POST['adv_remarks']):'';
		$asa_status_text=isset($_POST['asa_status_text'])?$_POST['asa_status_text']:'';
		if($asa_status_text!=""){
			create_support_ticket_for_asa_expiry($lead_code,$uid,"Collection");
		}
		$entry_by = $uid;
		$reason_e=/*. (string[int]) .*/ array();
		$lc=1;
		foreach($product_elist as $temp1){
			if(is_array($temp1)){
				$temp1=implode($temp1);
			}
			if($temp1!='0'){
				$tmp_re=explode('-',$temp1);
				$reason_e[$lc]=$tmp_re[2];
				$reason_e[$lc]=($reason_e[$lc]=='a'?"-1":$reason_e[$lc]);
			}
			$lc++;
		}
		//Deposited details:
		$deposited_bank="";
	    $deposited_branch="";
	    $deposited_branch_code="";
	    $deposited_date="";
	    $challan_no='';
	    $grd_receipt_id='';
        $gld_date=date('Y-m-d H:i:s');
		$GRD_STATUS=isset($_POST['status'])?(string)$_POST['status']:"";
		if($GRD_STATUS=='D' or $GRD_STATUS=='P'){
		    $deposited_bank=(string)$_POST['deposited_bank'];
		    $deposited_branch=(string)$_POST['deposited_branch'];
		    $deposited_branch_code=(string)$_POST['deposited_branch_code'];
		    $deposited_date=(string)$_POST['deposited_date'];
		    $challan_no=mysqli_real_escape_string_wrapper((string)$_POST['challan_no']);	
		}
		if($GRD_STATUS==""){
			$GRD_STATUS='D';
		}		
		$cheque_cleared_date=(string)$_POST['cheque_cleared_date'];
		$hand_over_to_terr=explode('-',(string)$_POST['emp_code2']);
		$hand_over_to=$hand_over_to_terr[0];
		$hand_over_date=$_POST['hand_over_date'];
		//if(isset($_POST['group_id'])){
			//$group_id=$_POST['group_id'];
			if($group_id==1){
				if(isset( $_POST['emp_code']) and  $_POST['emp_code']!=''){
					$emp_code_terr=explode('-',(string)$_POST['emp_code']);
					$emp_id=$emp_code_terr[0];
				}
				if(isset($_POST['receipt_id'])){
					$grd_receipt_id=(string)$_POST['receipt_id'];
				}
				if(isset($_POST['receipt_date']) and $_POST['receipt_date']!=''){
					$GRD_VISIT_DATE=(string)$_POST['receipt_date'];
				}
			}
		//}
		$lead_country = get_single_value_from_single_table("glh_country", "gft_lead_hdr", "glh_lead_code", $lead_code);
		$GRD_CONVERSION_RATE = isset($_REQUEST['conv_rate'])?(float)$_REQUEST['conv_rate']:1.0;
		$GRD_USD_AMT = '';
		if(strcasecmp($lead_country,'India')!=0) {
			if(!$_REQUEST['conv_rate'] || (float)$_REQUEST['conv_rate']<=0.0) {
				return "USD to INR Conversion Rate is required for International Customers and Partners";
			}
			$GRD_USD_AMT = $GRD_RECEIPT_AMT;
			$GRD_RECEIPT_AMT = round((float)$GRD_USD_AMT * $GRD_CONVERSION_RATE);
			$adv_amt = round((float)$adv_amt*$GRD_CONVERSION_RATE);
			for($i=1;$i<=count($collected_amt);$i++){
				if($collected_amt[$i]!=''){
					$collected_amt[$i] = round((float)$collected_amt[$i]*$GRD_CONVERSION_RATE);
				}
			}
			for($i=1;$i<=count($collected_eamt);$i++){
				if($collected_eamt[$i]!=''){
					$collected_eamt[$i] = round((float)$collected_eamt[$i]*$GRD_CONVERSION_RATE);
				}
			}
		}
		if($collection_against==2 && $advance1=='on') {
		    $dev_proforma_check_arr[$proforma_collection] = $adv_amt;
		}
		if($asa_status_text!=""){
			create_support_ticket_for_asa_expiry($lead_code,$uid,"Collection");
		}
		if($call_from!='order' and $GRD_VISIT_DATE!='' and !is_authorized_group($uid,1) and $_POST['Edit']!='edit'){
		    $detail_arr = array(
		        'GLD_LEAD_CODE'=>$lead_code,'GLD_VISIT_NATURE'=>"6",'GLD_EMP_ID'=>$emp_id,
		        'GLD_VISIT_DATE'=>$GRD_VISIT_DATE,'GLD_NOTE_ON_ACTIVITY'=>"Collection entry"
		    );
		    insert_in_gft_activity_table($detail_arr);
		}
		$reported_date='';
		if($grd_receipt_id!='' and (string)$_POST['Edit']=='edit' and $group_id==1){
			$audit_query=array();
			$user_id=$uid;
			$queryaudit="select * from gft_receipt_dtl where GRD_RECEIPT_ID='$grd_receipt_id'";
			$realized_date = '';
			$resultaudit=execute_my_query($queryaudit);
			$entered_by=$entered_on='';
			if($data_audit=mysqli_fetch_array($resultaudit)){
				$realized_date = $data_audit['GRD_REALIZED_DATE'];
				$reported_date=$data_audit['GRD_REPORTED_DATE'];
				if($data_audit['GRD_DATE']!=$GRD_VISIT_DATE){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_DATE'," .
							"'".$data_audit['GRD_DATE']."','$GRD_VISIT_DATE',now(),'$user_id','receipt.php')";
				}
				if($data_audit['GRD_EMP_ID']!=$emp_id){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_EMP_ID'," .
							"'".$data_audit['GRD_EMP_ID']."','$emp_id','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_RECEIPT_TYPE']!=$GRD_RECEIPT_TYPE){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_RECEIPT_TYPE'," .
							"'".$data_audit['GRD_RECEIPT_TYPE']."','$GRD_RECEIPT_TYPE','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_RECEIPT_AMT']!=$GRD_RECEIPT_AMT){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_RECEIPT_AMT'," .
							"'".$data_audit['GRD_RECEIPT_AMT']."','$GRD_RECEIPT_AMT','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_USD_AMT']!=$GRD_USD_AMT) {
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_USD_AMT'," .
					"'".$data_audit['GRD_USD_AMT']."','$GRD_USD_AMT',now(),'$user_id','receipt.php')";
				}
				if($data_audit['GRD_CONVERSION_RATE']!=$GRD_CONVERSION_RATE) {
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_CONVERSION_RATE'," .
					"'".$data_audit['GRD_CONVERSION_RATE']."','$GRD_CONVERSION_RATE',now(),'$user_id','receipt.php')";
				}
				if($data_audit['GRD_CHEQUE_DD_NO']!=$GRD_CHEQUE_NO){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_CHEQUE_DD_NO'," .
							"'".$data_audit['GRD_CHEQUE_DD_NO']."','$GRD_CHEQUE_NO','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_CHEQUE_DD_DATE']!=$GRD_CHEQUE_DATE){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_CHEQUE_DD_DATE'," .
							"'".$data_audit['GRD_CHEQUE_DD_DATE']."','$GRD_CHEQUE_DATE','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_BANK_NAME']!=$GRD_BANK_NAME){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_BANK_NAME'," .
							"'".$data_audit['GRD_BANK_NAME']."','".mysqli_real_escape_string_wrapper($GRD_BANK_NAME)."','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_DEPOSITED_BANK']!=$deposited_bank){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_DEPOSITED_BANK'," .
							"'".$data_audit['GRD_DEPOSITED_BANK']."','$deposited_bank','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_DEPOSITED_DATE']!=$deposited_date){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_DEPOSITED_DATE'," .
							"'".$data_audit['GRD_DEPOSITED_DATE']."','$deposited_date','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_DEPOSITED_BRANCH_NAME']!=$deposited_branch){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_DEPOSITED_BRANCH_NAME'," .
							"'".$data_audit['GRD_DEPOSITED_BRANCH_NAME']."','$deposited_branch','now()'," .
							"'$user_id','receipt.php')";
				}
				if($data_audit['GRD_DEPOSITED_BRANCH_CODE']!=$deposited_branch_code){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_DEPOSITED_BRANCH_CODE'," .
							"'".$data_audit['GRD_DEPOSITED_BRANCH_CODE']."','$deposited_branch_code','now()'," .
							"'$user_id','receipt.php')";
				}
				if($data_audit['GRD_DEPOSTIT_CHALLAN_NO']!=$challan_no){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_DEPOSTIT_CHALLAN_NO'," .
							"'".$data_audit['GRD_DEPOSTIT_CHALLAN_NO']."','$challan_no','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_CHEQUE_CLEARED_DATE']!=$cheque_cleared_date){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_CHEQUE_CLEARED_DATE'," .
							"'".$data_audit['GRD_CHEQUE_CLEARED_DATE']."','$cheque_cleared_date'," .
							"'now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_HAND_OVER_TO']!=$hand_over_to){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_HAND_OVER_TO'," .
							"'".$data_audit['GRD_HAND_OVER_TO']."','$hand_over_to','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_STATUS']!=$GRD_STATUS){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_STATUS'," .
							"'".$data_audit['GRD_STATUS']."','$GRD_STATUS','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_TERMS_REGARDING_COLLECTION']!=$GRD_NOTE){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_TERMS_REGARDING_COLLECTION'," .
							"'".$data_audit['GRD_TERMS_REGARDING_COLLECTION']."','".mysqli_real_escape_string_wrapper($GRD_NOTE)."','now()'," .
							"'$user_id','receipt.php')";
				}
				if($data_audit['GRD_HAND_OVER_DATE']!=$hand_over_date){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_HAND_OVER_DATE'," .
							"'".$data_audit['GRD_HAND_OVER_DATE']."','$hand_over_date','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_RECEIPT_ID_REF']!=$GRD_RECEIPT_ID_given){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_RECEIPT_ID_REF'," .
							"'".$data_audit['GRD_RECEIPT_ID_REF']."','$GRD_RECEIPT_ID_given','now()'," .
							"'$user_id','receipt.php')";
				}
				if($data_audit['GRD_LEAD_CODE']!=$lead_code){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_LEAD_CODE'," .
							"'".$data_audit['GRD_LEAD_CODE']."','$lead_code','now()','$user_id','receipt.php')";
				}
				if($data_audit['GRD_COLLECTION_DATE']!=$GRD_VISIT_DATE){
					$audit_query[]="('','gft_receipt_dtl','','$grd_receipt_id','GRD_COLLECTION_DATE'," .
							"'".$data_audit['GRD_COLLECTION_DATE']."','$GRD_VISIT_DATE','now()','$user_id','receipt.php')";
				}
				$entered_by = $data_audit['GRD_ENTRY_BY_EMP_ID'];
				$entered_on = $data_audit['GRD_ENTRY_DATE'];
			}
			$queryaudit="select * from gft_collection_receipt_dtl where GCR_RECEIPT_ID='$grd_receipt_id'";
			$result_audit=execute_my_query($queryaudit,'receipt_util.php',true,true);
			$order_maped=array();
			while($data_audit=mysqli_fetch_array($result_audit)){
				$find=false;
				for($i=1;$i<=count($order_no_list);$i++){
					if($collected_amt[$i]!='' and $data_audit['GCR_ORDER_NO']==$order_no_list[$i] and $data_audit['GCR_REASON']=='1'){
						/*if($data_audit['GCR_AMOUNT']!=$collected_amt[$i]){
								$audit_query[]="('','gft_collection_receipt_dtl','".$data_audit['GCR_ORDER_NO']."'," .
									"'$grd_receipt_id','GCR_AMOUNT','".$data_audit['GCR_AMOUNT']."'," .
									"'".$collected_eamt[$i]."','now()','$user_id','receipt.php')";
						}*/
						$find=true;	
					}
				}
				for($i=1;$i<=count($order_no_elist);$i++){
					if($collected_eamt[$i]!='' and $data_audit['GCR_ORDER_NO']==$order_no_elist[$i] and $data_audit['GCR_REASON']==$reason_e[$i]){
						if($data_audit['GCR_AMOUNT']!=$collected_eamt[$i]){
							$audit_query[]="('','gft_collection_receipt_dtl','".$data_audit['GCR_ORDER_NO']."'," .
									"'$grd_receipt_id','GCR_AMOUNT','".$data_audit['GCR_AMOUNT']."'," .
									"'".$collected_eamt[$i]."','now()','$user_id','receipt.php')";
						}
						if($data_audit['GCR_PAYMENT_FORID']!=$product_elist[$i]){
							$audit_query[]="('','gft_collection_receipt_dtl','{$data_audit['GCR_ORDER_NO']}'," .
									"'$grd_receipt_id','GCR_PAYMENT_FORID','{$data_audit['GCR_PAYMENT_FORID']}'," .
									"'$product_elist[$i]','now()','$user_id','receipt.php')";
						}
						$find=true;
					}
				}
				$order_maped[]=$data_audit['GCR_ORDER_NO'];
			}
			$cnt_audit_query=count($audit_query);
      		if($cnt_audit_query!=0){
	      		$aquery2=implode(',',$audit_query);
		  		$aquery="insert into gft_audit_viewer_order (" .
					" `GAV_AUDIT_ID`, `GAV_TABLE_NAME`,`GAV_ORDER_NO`,`GAV_RECEIPT_ID`,`GAV_COLUMN_NAME`, " .
					"`GAV_PREVIOUS_VALUE`, " .
					" `GAV_UPDATED_VALUE`, `GAV_UPDATED_DATETIME`, `GAV_UPDATED_BY`, `GAV_FROM_PAGE` " .
					")values ".$aquery2 ;
				execute_my_query($aquery,'receipt_util.php',true,true);	
      		}	
			$del_query=" delete from gft_collection_receipt_dtl where GCR_RECEIPT_ID='$grd_receipt_id'";
			$result= execute_my_query("$del_query",'receipt_util.php',true,true);
			if($GRD_RECEIPT_AMT!='' and $GRD_RECEIPT_AMT!=0){
				$query_ins1="replace into gft_receipt_dtl ( GRD_RECEIPT_ID , GRD_DATE ,  GRD_EMP_ID ,  GRD_RECEIPT_TYPE , " .
					    " GRD_RECEIPT_AMT ,GRD_CHEQUE_DD_NO ,  GRD_CHEQUE_DD_DATE ,  GRD_BANK_NAME , " .
					    " GRD_DEPOSITED_BANK ,GRD_DEPOSITED_DATE ,  GRD_DEPOSITED_BRANCH_NAME ," .
					    " GRD_DEPOSITED_BRANCH_CODE ,GRD_DEPOSTIT_CHALLAN_NO ,  GRD_CHEQUE_CLEARED_DATE ," .
					    " GRD_HAND_OVER_TO ,  GRD_STATUS ,GRD_TERMS_REGARDING_COLLECTION ,  GRD_CHECKED_WITH_LEDGER , " .
					    " GRD_HAND_OVER_DATE ,GRD_RECEIPT_ID_REF,GRD_LEAD_CODE, GRD_REPORTED_DATE," .
					    " GRD_COLLECTION_DATE,GRD_COLLECTION_TYPE,GRD_ENTRY_BY_EMP_ID,grd_entry_date, ".
					    " GRD_COLLECTION_AGAINST_TYPE,GRD_COLLECTION_AGAINST_QUOTE,GRD_REALIZED_DATE,GRD_CONVERSION_RATE,GRD_USD_AMT) ".
					    " values ('$grd_receipt_id','$GRD_VISIT_DATE','$emp_id','$GRD_RECEIPT_TYPE','$GRD_RECEIPT_AMT'," .
						"'$GRD_CHEQUE_NO','$GRD_CHEQUE_DATE','".mysqli_real_escape_string_wrapper($GRD_BANK_NAME)."','".mysqli_real_escape_string_wrapper($deposited_bank)."'," .
						"'$deposited_date','".mysqli_real_escape_string_wrapper($deposited_branch)."','".mysqli_real_escape_string_wrapper($deposited_branch_code)."','$challan_no'," .
						"'$cheque_cleared_date','$hand_over_to','$GRD_STATUS','".mysqli_real_escape_string_wrapper($GRD_NOTE)."','N'," .
						"'$hand_over_date','$GRD_RECEIPT_ID_given','$lead_code','$reported_date','$GRD_VISIT_DATE','$collection_type', ".
						" '$entered_by','$entered_on','$collection_against','$collection_against_no','$realized_date','$GRD_CONVERSION_RATE','$GRD_USD_AMT')";
					
				$result_ins1=execute_my_query($query_ins1,'receipt_util.php',true,true);
				if($result_ins1){
					for($i=1;$i<=count($collected_amt);$i++){
						if($collected_amt[$i]!=''){
							$order_no_collection=$order_no_list[$i];
							$query_ins2="insert into gft_collection_receipt_dtl  (GCR_ORDER_NO, GCR_RECEIPT_ID, GCR_PAYMENT_FORID ,GCR_AMOUNT ,GCR_REASON) values" .
									" ('$order_no_collection','$grd_receipt_id','".$GRD_PAYMENT_FORID[$i]."','".$collected_amt[$i]."','1') ";
						        	
							$result_ins2=execute_my_query($query_ins2,'receipt_util.php',true,true);
							update_collection_in_hdr($order_no_collection,$discount_update);
						}
					}
					for($i=1;$i<=count($collected_eamt);$i++){
						if($collected_eamt[$i]!=''){
							$order_no_collection=$order_no_elist[$i];
							$query_ins3="insert into gft_collection_receipt_dtl " .
									" (GCR_ORDER_NO, GCR_RECEIPT_ID,GCR_PAYMENT_FORID, GCR_OTHERS_LISTED_PRICE, GCR_AMOUNT ,GCR_REASON) " .
									" values ('$order_no_collection', '$grd_receipt_id', '".$product_elist[$i]."','".$GCR_OTHERS_LISTED_PRICE[$i]."', '".$collected_eamt[$i]."','".$reason_e[$i]."')";
					    	$result_ins3=execute_my_query($query_ins3,'receipt_util.php',true,true);
				    	}
					}
					if((int)$adv_amt!=0){
						$gcr_order_no = $lead_code;
						if(isPartnerLeadCode($lead_code)){
							$gcr_order_no = get_partner_balance_order($lead_code);
						}
						$que1=" insert into gft_collection_receipt_dtl " .
								" (GCR_ORDER_NO, GCR_RECEIPT_ID, GCR_PAYMENT_FORID, GCR_AMOUNT ,GCR_REASON, GCR_REMARKS) " .
								" values ('$gcr_order_no', '$grd_receipt_id', 'a', '$adv_amt','-1','$adv_remarks')";
						execute_my_query($que1);
					}
				}
				foreach($order_maped as $key=>$value){
					update_collection_in_hdr($value,$discount_update);
				}
				update_cust_bal($lead_code);
			}else{
				$query_ins1=" delete from gft_receipt_dtl where GRD_RECEIPT_ID='$grd_receipt_id'";
				$result_ins1=execute_my_query($query_ins1,'receipt_util.php',true,true);
				update_cust_bal($lead_code);
			}
			send_collection_receipt($grd_receipt_id);
		}else if($GRD_RECEIPT_AMT!='' and $GRD_RECEIPT_AMT!=0){
			if($reported_date==''){
				$reported_date=date('Y-m-d H:i:s',time());
			}
			$query_ins1="replace into gft_receipt_dtl ( GRD_RECEIPT_ID , GRD_DATE ,  GRD_EMP_ID ,  GRD_RECEIPT_TYPE , " .
				    " GRD_RECEIPT_AMT ,GRD_CHEQUE_DD_NO ,  GRD_CHEQUE_DD_DATE ,  GRD_BANK_NAME , " .
				    " GRD_DEPOSITED_BANK ,GRD_DEPOSITED_DATE ,  GRD_DEPOSITED_BRANCH_NAME ," .
				    " GRD_DEPOSITED_BRANCH_CODE ,GRD_DEPOSTIT_CHALLAN_NO ,  GRD_CHEQUE_CLEARED_DATE ," .
				    " GRD_HAND_OVER_TO ,  GRD_STATUS ,GRD_TERMS_REGARDING_COLLECTION ,  GRD_CHECKED_WITH_LEDGER , " .
				    " GRD_HAND_OVER_DATE ,GRD_RECEIPT_ID_REF,GRD_LEAD_CODE, GRD_REPORTED_DATE," .
				    " GRD_COLLECTION_DATE,GRD_COLLECTION_TYPE,GRD_ENTRY_BY_EMP_ID,GRD_ENTRY_DATE,GRD_COLLECTION_AGAINST_TYPE, ".
				    " GRD_COLLECTION_AGAINST_QUOTE,GRD_CONVERSION_RATE,GRD_USD_AMT,GRD_GSTIN_UPDATE_STATUS,GRD_EXPECTED_TO_UPDATE_GSTIN ) values ".
				    "('','$GRD_VISIT_DATE','$emp_id','$GRD_RECEIPT_TYPE','$GRD_RECEIPT_AMT'," .
					"'$GRD_CHEQUE_NO','$GRD_CHEQUE_DATE','".mysqli_real_escape_string_wrapper($GRD_BANK_NAME)."','".mysqli_real_escape_string_wrapper($deposited_bank)."'," .
					"'$deposited_date','".mysqli_real_escape_string_wrapper($deposited_branch)."','".mysqli_real_escape_string_wrapper($deposited_branch_code)."','$challan_no'," .
					"'$cheque_cleared_date','$hand_over_to','$GRD_STATUS','".mysqli_real_escape_string_wrapper($GRD_NOTE)."','N'," .
					"'$hand_over_date','$GRD_RECEIPT_ID_given','$lead_code','$reported_date','$GRD_VISIT_DATE','$collection_type', ".
					" '$entry_by',now(),'$collection_against','$collection_against_no','$GRD_CONVERSION_RATE','$GRD_USD_AMT','$customer_gstin_status','$gstin_update_date')";
				
			$result_ins1=execute_my_query($query_ins1,'receipt_util.php',true,true);
			$grd_receipt_id=mysqli_insert_id_wrapper();
			if($result_ins1){
				for($i=1;$i<=count($collected_amt);$i++){
					if($collected_amt[$i]!=''){
						$order_no_collection=$order_no_list[$i];
						$query_ins2="insert into gft_collection_receipt_dtl  (GCR_ORDER_NO, GCR_RECEIPT_ID, GCR_PAYMENT_FORID ,GCR_AMOUNT ,GCR_REASON) values" .
								" ('$order_no_collection','$grd_receipt_id','".$GRD_PAYMENT_FORID[$i]."','".$collected_amt[$i]."','1') ";
						$result_ins2=execute_my_query($query_ins2,'receipt_util.php',true,true);
						update_collection_in_hdr($order_no_collection,$discount_update);
					}
				}
				for($i=1;$i<=count($collected_eamt);$i++){
					if($collected_eamt[$i]!=''){
						$order_no_collection=$order_no_elist[$i];
						$query_ins3="insert into gft_collection_receipt_dtl " .
								" (GCR_ORDER_NO, GCR_RECEIPT_ID,GCR_PAYMENT_FORID, GCR_OTHERS_LISTED_PRICE, GCR_AMOUNT ,GCR_REASON) " .
								" values ('$order_no_collection', '$grd_receipt_id', '".$product_elist[$i]."','".$GCR_OTHERS_LISTED_PRICE[$i]."', '".$collected_eamt[$i]."','".$reason_e[$i]."')";
				    	$result_ins3=execute_my_query($query_ins3,'receipt_util.php',true,true);
			    	}
				}
				if((int)$adv_amt!=0){
					$que1=" insert into gft_collection_receipt_dtl " .
						  " (GCR_ORDER_NO, GCR_RECEIPT_ID, GCR_PAYMENT_FORID, GCR_AMOUNT ,GCR_REASON, GCR_REMARKS) " .
						  " values ('$lead_code', '$grd_receipt_id', 'a', '$adv_amt','-1','$adv_remarks')";
					$res1 = execute_my_query($que1);
					if($res1) {
						$new_date = date('Y-m-d',mktime(null,null,null,date('m'),date('d')+5,date('y')));
						$call_res = execute_my_query("select GID_LEAD_CODE from gft_install_dtl_new where GID_LEAD_CODE='$lead_code' and GID_STATUS='A' and GID_VALIDITY_DATE<='$new_date'");
						if(mysqli_num_rows($call_res) > 0){					
							//$call_upd = " update gft_lead_hdr set GLH_MAIN_PRODUCT_UVALIDITITY='$new_date', GLH_MAIN_PRODUCT_UPDATE_BY='$uid', ".
							//			" GLH_MAIN_PRODUCT_UPDATE_ON=now() where GLH_LEAD_CODE='$lead_code' ";
							//execute_my_query($call_upd);
							$validity_upd = " update gft_install_dtl_new set GID_TRIAL_TILL_DATE='$new_date' where GID_LEAD_CODE='$lead_code' and GID_VALIDITY_DATE <= '$new_date' ";
							execute_my_query($validity_upd);
						}
					}
				}
				update_cust_bal($lead_code);
				execute_my_query("update gft_collection_reminder set GCR_STATUS=2 where GCR_STATUS=1 and GCR_CHEQUE_NO='$GRD_CHEQUE_NO' and abs(GCR_REALIZED_AMT-'$GRD_RECEIPT_AMT') < 1");
			}
			send_collection_receipt($grd_receipt_id);
			$partner_dtl	=	get_asa_commission_partner_dtl($lead_code);
			if(isset($partner_dtl['partner_emp_id']) && $partner_dtl['partner_emp_id']!=""){
				$partner_id	= $partner_dtl['partner_emp_id'];
				$emp_dtl_arr = get_emp_master($emp_id,'A',null,false);
				$cust_email_arr = customerContactDetail($lead_code);
				send_notification_to_partner($partner_id,$lead_code,$cust_email_arr['cust_name'],$emp_dtl_arr[0][1],$GRD_RECEIPT_AMT,"collection_entry");
			}
		}
	}//end of if 
	/* Incetive for Outstanding */
	for($i=1;$i<=count($collected_amt);$i++){
	    if( ($collected_amt[$i]!='') && ($order_no_list[$i]!='') ){
	        outstanding_incentive($order_no_list[$i],$emp_id,$grd_receipt_id);
        }
	}//end of oustanding
	
	$hr=date('H');$clr_flag=0;$alt_msg='';$alt_beg='08.00';$i=12;$alt_duration='';
	$coll_const= get_samee_const('Payment_Realization_Timing');
	$alt_end=substr($coll_const,0,5);
	$clear_tmg_ary=explode(',',$coll_const);
	foreach($clear_tmg_ary as $value){
		$timing_const=explode('-',"$value");
		if(strcmp($hr,substr($timing_const[0],0,2))<0){
			$alt_duration=$alt_beg.' - '.$alt_end;
			$alt_msg=(string)$value;
			$clr_flag=1;
			break;
		}else{
			$alt_beg=substr($coll_const,$i-12,5);
			$alt_end=substr($coll_const,$i,5);
			$i=$i+12;
		}
	}
	if($alt_end==''){
		$alt_end=substr($coll_const,-11,5);
	}
	if($clr_flag!=0 && date('N')!='7'){
		$submit_status='Payment entry saved.Amount credited in bank between '.$alt_duration.' Hrs realisation will be done in SAM between '.$alt_msg.' Hrs';
	}else if($clr_flag!=0 && date('N')!='7'){
		$submit_status='Payment entry saved.Amount credited in bank between '.$alt_duration.' Hrs realisation will be done in SAM between '.$alt_msg.' Hrs';
	}else {
		$submit_status='Payment entry saved.Amount credited in bank after '.$alt_end.' Hrs realisation will be done in SAM between '.$clear_tmg_ary[0].' Hrs';
	}
	for($i=1;$i<=count($collected_amt);$i++) {
	    if($collected_amt[$i]!='') {
	        $order_no_collection=$order_no_list[$i];
	        $order_proforma_no = get_single_value_from_single_table("gph_order_no", "gft_proforma_hdr", "gph_converted_order_no", $order_no_collection);
	        if($order_proforma_no!='') {
	            $dev_proforma_check_arr[$order_proforma_no] = $collected_amt[$i];
	        }
	    }
	}
    foreach ($dev_proforma_check_arr as $pi=>$p_amt) {
        update_collection_pending_ticket($pi,'19',$p_amt);
    }
	return $submit_status;
}// end of function

/**
 * @param string $table_name
 * @param string $key_col
 * @param string $id
 * @param string[string] $update_arr
 * @param string $submit_code
 * @return int
 */
function insert_audit_and_update_dtl($table_name,$key_col,$id,$update_arr,$submit_code) { // Supports only for gft_audit_viewer_order
	global $log,$uid;
	$id = mysqli_real_escape_string_wrapper($id);
	$dtls_qry = execute_my_query(" select * from $table_name where $key_col='$id' ");
	$update_str = '';
	$audit_entries = array();
	if(mysqli_num_rows($dtls_qry)>0) {
		$row = mysqli_fetch_array($dtls_qry);
		$i = 0;
		foreach ($row as $k=>$v) {
			$i++;
			if($i%2==1) {
				continue;
			}
			if(in_array($k,array_keys($update_arr))) {
				if(!isset($update_arr["$k"])) {
					continue;
				}
				$val = $update_arr["$k"];
				if($row["$k"]!=$val) {
					$audit_entries[]="('','$table_name','','$id','$k'," .
					"'".$row["$k"]."','$val','now()','$uid','$submit_code')";
					$update_str .= (($update_str!='')?",":"")." $k='".mysqli_real_escape_string_wrapper($val)."'";
				}
			}
		}
		$audit_qry = "";
		$cnt_audit_query=count($audit_entries);
		if($cnt_audit_query>0){
			$aquery2=implode(',',$audit_entries);
			$aquery=" insert into gft_audit_viewer_order (GAV_AUDIT_ID,GAV_TABLE_NAME,GAV_ORDER_NO,GAV_RECEIPT_ID,GAV_COLUMN_NAME, " .
					" GAV_PREVIOUS_VALUE,GAV_UPDATED_VALUE,GAV_UPDATED_DATETIME,GAV_UPDATED_BY,GAV_FROM_PAGE) values ".$aquery2 ;
			execute_my_query($aquery,'receipt_util.php',true,true);
			execute_my_query(" update $table_name set $update_str where $key_col='$id' ");
			$result_count = mysqli_affected_rows_wrapper();
		} else {
			$log->logInfo("From insert_audit_and_update_dtl: Nothing to update in $table_name ($key_col=$id)");
			$result_count = 0;
		}
	} else {
		$log->logInfo("From insert_audit_and_update_dtl: No rows found in $table_name for $key_col=$id.");
		$result_count = 0;
	}
	return $result_count;
}
/**
 * @param string $form_type
 * @param string $id
 * @return string
 */
function suspense_amount_entry($form_type='new',$id='') {
	global $uid;
	$GRD_RECEIPT_TYPE = isset($_REQUEST['rtype'])?$_REQUEST['rtype']:'';
	$GRD_STATUS = isset($_REQUEST['status'])?$_REQUEST['status']:'';
	$get_deposit_status = false;
	if($GRD_STATUS=='D' or $GRD_STATUS=='P') {
		$get_deposit_status = true;
	}
	if($GRD_STATUS==""){
		$GRD_STATUS='D';
	}
	$insert_arr = array();
	$insert_arr['GSE_EMP_ID'] 	    		 = $uid;	
	if($form_type=='new') {
		$insert_arr['GSE_ENTRY_DATE']   		 = isset($_REQUEST['visit_date'])?$_REQUEST['visit_date']:date('Y-m-d');
		$insert_arr['GSE_REPORTED_DATE'] 		 = date('Y-m-d H:i:s',time());
	}
	$insert_arr['GSE_RECEIPT_AMT']  		 = isset($_REQUEST['ramt'])?$_REQUEST['ramt']:'';
	$insert_arr['GSE_RECEIPT_TYPE'] 		 = $GRD_RECEIPT_TYPE;
	$insert_arr['GSE_STATUS']	    		 = $GRD_STATUS;
	if($get_deposit_status) {
		$insert_arr['GSE_DEPOSITED_BANK']        = isset($_REQUEST['deposited_bank'])?$_REQUEST['deposited_bank']:'';
		$insert_arr['GSE_DEPOSITED_BRANCH_NAME'] = isset($_REQUEST['deposited_branch'])?$_REQUEST['deposited_branch']:'';
		$insert_arr['GSE_DEPOSITED_DATE']        = isset($_REQUEST['deposited_date'])?$_REQUEST['deposited_date']:'';
		$insert_arr['GSE_DEPOSITED_BRANCH_CODE'] = isset($_REQUEST['deposited_branch_code'])?$_REQUEST['deposited_branch_code']:'';
		$insert_arr['GSE_DEPOSTIT_CHALLAN_NO']   = isset($_REQUEST['challan_no'])?$_REQUEST['challan_no']:'';
	}
	$insert_arr['GSE_HAND_OVER_TO'] 		 = isset($_REQUEST['emp_code2'])?$_REQUEST['emp_code2']:'';
	$insert_arr['GSE_HAND_OVER_DATE']		 = isset($_REQUEST['hand_over_date'])?$_REQUEST['hand_over_date']:'';
	$insert_arr['GSE_BANK_NAME']			 = isset($_REQUEST['bname'])?$_REQUEST['bname']:'';
	$insert_arr['GSE_CHEQUE_DD_DATE'] 		 = isset($_REQUEST['cheque_date'])?$_REQUEST['cheque_date']:'';
	$insert_arr['GSE_CHEQUE_DD_NO']   		 = isset($_REQUEST['cno'])?trim($_REQUEST['cno']):''; 
	if($GRD_RECEIPT_TYPE=="5"){  //paypal
		$insert_arr['GSE_CHEQUE_DD_NO']   = isset($_REQUEST['utid'])?trim($_REQUEST['utid']):'';
	}elseif ($GRD_RECEIPT_TYPE=="7"){ //Net Transfer
		$insert_arr['GSE_CHEQUE_DD_NO']   = isset($_REQUEST['utrno'])?trim((string)$_REQUEST['utrno']):'';
		$insert_arr['GSE_CHEQUE_DD_DATE'] = isset($_REQUEST['transfer_date'])?$_REQUEST['transfer_date']:'';;
	}	
	$insert_arr['GSE_TERMS_REGARDING_COLLECTION'] = isset($_REQUEST['collection_terms'])?$_REQUEST['collection_terms']:'';
	if($form_type=='new') {
		$insert_id = array_insert_query("gft_suspense_amount_entry", $insert_arr);
		if($insert_id>0) {
			return "Successfully submitted suspense entry with ID: ".$insert_id;
		} else {
			return "Something went wrong while inserting suspense entry. Please try again.";
		}
	} else if($form_type=='edit') {
		if($id=='' or !is_numeric($id)) {
			show_alert_and_close("A valid suspense entry ID required for edit mode.");
			return '';
		}
		$update_arr = array();
		foreach ($insert_arr as $k=>$v) {
			$update_arr["$k"] = $v;
		}
		if(count($update_arr)>0) {
			$update_res = insert_audit_and_update_dtl("gft_suspense_amount_entry", "gse_id", $id, $update_arr, 'receipt.php');
		} else {
			return "No details changed for suspense entry.";
		}
		return "Updated $update_res entry with ID $id";
	} else {
		return 'Invalid purpose';
	}
}
/**
 * @return void
 */
function declare_status_list(){
//	global $uid;
	$query_cheque=" select GCS_STATUS,GCS_STATUS_ABR,GCS_FOR_CASH,GCS_FOR_CHEQUE,GCS_FOR_DD,GCS_FOR_NETTRANSFER ".
				  " from gft_cheque_status_master ";
//	$query_cheque.=(!is_authorized_group($uid,1)? " where GCS_STATUS_ABR in ('H','O','D','W')":"" ) ;
	$result_cheque=execute_my_query($query_cheque);
	echo "<script>var cheque_stat_arr = new Array();\n";
	$i=0;
	while($row_data = mysqli_fetch_array($result_cheque)){
		if($row_data['GCS_FOR_CASH']=='Y'){
			$stat_abr = '1'.'-'.$row_data['GCS_STATUS_ABR'].'-'.$row_data['GCS_STATUS'];
			echo "cheque_stat_arr[$i] = '$stat_abr';\n";
			$i++;
		}
		if ($row_data['GCS_FOR_CHEQUE']=='Y'){
			$stat_abr = '2'.'-'.$row_data['GCS_STATUS_ABR'].'-'.$row_data['GCS_STATUS'];
			echo "cheque_stat_arr[$i] = '$stat_abr';\n";
			$i++;
		}
		if ($row_data['GCS_FOR_DD']=='Y'){
			$stat_abr = '3'.'-'.$row_data['GCS_STATUS_ABR'].'-'.$row_data['GCS_STATUS'];
			echo "cheque_stat_arr[$i] = '$stat_abr';\n";
			$i++;
		}
		if ($row_data['GCS_FOR_NETTRANSFER']=='Y'){
			$stat_abr = '7'.'-'.$row_data['GCS_STATUS_ABR'].'-'.$row_data['GCS_STATUS'];
			echo "cheque_stat_arr[$i] = '$stat_abr';\n";
			$i++;
		}
	}
	echo "</script>";
}

/**
 * @param string $lead_code
 * @param boolean $suspense
 * @param string $form_type
 * @param string $receipt_id
 * @return void
 */
function show_receipt_dtl($lead_code='',$suspense=false,$form_type='new',$receipt_id='')
{
	global $uid;
	global $group_id;
	$amount = $receipt_type = $cheque_status = $terms = $cheque_dd_bank_name = $deposited_bank = $deposit_dt = $utr = $utid = $transfer_dt = '';
	$deposited_branch = $handed_over_to = $handed_over_dt = $cheque_dd_no = $chalan_no = $cheque_dd_dt = $deposit_branch_code = '';
	if($form_type=='edit' and $receipt_id!='' and $suspense) {
		$dtls_qry = execute_my_query(" select * from gft_suspense_amount_entry where gse_id='$receipt_id' ");
		if(mysqli_num_rows($dtls_qry)>0) {
			$row = mysqli_fetch_array($dtls_qry);
			$amount = $row['GSE_RECEIPT_AMT'];
			$receipt_type = $row['GSE_RECEIPT_TYPE'];
			$cheque_status = $row['GSE_STATUS'];
			$terms = $row['GSE_TERMS_REGARDING_COLLECTION'];
			$cheque_dd_no = $row['GSE_CHEQUE_DD_NO'];
			$cheque_dd_dt = $row['GSE_CHEQUE_DD_DATE'];
			if($receipt_type=='7') {
				$utr = $cheque_dd_no;
				$transfer_dt = $cheque_dd_dt;
				$cheque_dd_no = $cheque_dd_dt = '';
			} else if($receipt_type=='5') {
				$utid = $cheque_dd_no;
				$cheque_dd_no = $cheque_dd_dt = '';
			}
			$cheque_dd_bank_name = $row['GSE_BANK_NAME'];
			$deposited_bank = $row['GSE_DEPOSITED_BANK'];
			$deposit_dt = $row['GSE_DEPOSITED_DATE'];
			$deposited_branch = $row['GSE_DEPOSITED_BRANCH_NAME'];
			$handed_over_to = $row['GSE_HAND_OVER_TO'];
			$handed_over_dt = $row['GSE_HAND_OVER_DATE'];
			$chalan_no = $row['GSE_DEPOSTIT_CHALLAN_NO'];
			$deposit_branch_code = $row['GSE_DEPOSITED_BRANCH_CODE'];
			
		}
	}
	if($deposited_bank=='') {
		$deposited_bank = "HDFC Bank";
	}
	$advance_selected='';
	$cq_status_list=cheque_status_list($group_id);
	declare_status_list();
	global $order_no,$order_list,$no_orders,$indirorderno;
	if(basename($_SERVER['SCRIPT_NAME'])=='order_details.php'){
	    $no_orders=1;
		$order_list[count($order_list)]=$order_no;	
		$indirorderno[count($indirorderno)]=$order_no;
		$advance_selected="selected";
	}
//	$threshold_advance_amount=get_samee_const('Threshold_Advance_Amount');
	$red_star = "<span style='color:red;'>*</span>";
	$country = get_single_value_from_single_table("glh_country", "gft_lead_hdr", "glh_lead_code", $lead_code);
	$currency = "INR";
	if(strcasecmp($country,'India')!=0) {
		$currency = "USD";
	}
	$gstin_input_design = "";
	$is_required_gstin_status = check_gstin_update_needed($lead_code);
	if($form_type!='edit' && ($is_required_gstin_status)){
	    $gstin_input_design=<<<GSTIN
        <tr>
        <td colspan="2">
            <div id="gstin_status_div"  class="">
                <TABLE border="0" cellPadding="0" cellSpacing="2" width="100%" >
                    <TR><TD class="datalabel" width="175"><span style="color:red;">*</span>Customer GSTIN Status</TD>
                    <TD><select name='customer_gstin_status' id='customer_gstin_status' class='formStyleTextarea '>
                        <option value='0'>Select</option>
                        <option value='1'>Customer has GSTIN B2B(Registered)</option>
                        <option value='2'>Customer dont have GSTIN B2C(Unregistered)</option>
                        <option value='3'>Customer applied for GSTIN</option>
                    </select></TD></TR>
                    <TR id="gstin_update_date_div" class="hide"><TD class="datalabel" width="175"><span style="color:red;">*</span>Expected date of updating GSTIN</TD>
                    <TD><input name="gstin_update_date" type="text" class="formStyleTextarea" id="gstin_update_date" value="" readonly>
                        <img src="images/date_time.gif" class="imagecur" id="onceDateIcon5h" alt="" width=16 height=16 border=0 >
                    </TD></TR>
                    </table>
            </div>
        </td>
        </tr>
        <script>init_date_func("gstin_update_date","%Y-%m-%d","onceDateIcon5h","Bl");</script>
GSTIN;
	}
echo<<<END
<tr><td colspan="2">
<table border="1" id="table_advance_collection" class="hide" ><tbody>
<TR><TD align="center" width="150">Customer Id</TD>
	<TD align="center" width="150">Payment Type</TD>
	<TD align="center" width="50">Receipt Amount [$currency]</TD>
	<TD align="center" width="150">Remarks</TD><tr>
<tr><td>$lead_code</td><td>Advance</td>
<td><input name="adv_amt" id="adv_amt"  type="text"  size="10" onchange="calculate_total(this);"  onkeyup="javascript:extractNumber(this,2,false);"  onkeypress="javascript:return blockNonNumbers(this, event, true, false);"> </td>
<td><input type="text"  name="adv_remarks" id="adv_remarks" size="30"></td>
</tr></table>
END;

echo<<<END
<tr><td colspan="2">
<table border="1" id="table_order_collection" class="hide"><tbody>
	<TR>
	<TD align="center" width="10">S.No</TD>
	<TD align="center" width="150">Order No</TD>
	<TD align="center" width="100">Order Amount</TD>
	<TD align="center" width="150">Balance Amount</TD>
	<TD align="center" width="150">Payment Type</TD>
	<TD align="center" width="50">Receipt Amount [$currency]</TD>
	<TD align="center" width="150">Remarks</TD><tr>
	<td>1</td>
	<td><select size="1" name="order_no_coll[1]"  id='order_no_coll1' class="formStyleTextarea" onchange="updateCityState(this);" >
END;
	if(basename($_SERVER['SCRIPT_NAME'])!='order_details.php'){
		echo "<option value=\"0\">select</option>";
	}
	for($i=0;$i<count($order_list);$i++){
		$order_no=$order_list[$i];
		echo "<option value=\"$order_no\">$order_no</option>";
	}	
echo<<<END
	</select></td>
    <TD><input type="text" name="order_amt[1]" id="order_amt1" Readonly='true' class="formStyleTextarea" ></TD>
	<TD align="center"><input type="text" name="balance[1]" id="balance1"  Readonly='true' class="formStyleTextarea"  size="10"></TD>
	<TD><select size="1" name="ptype[1]" id="ptype1" class="formStyleTextarea">
  		<option value='d'>On Delivery </option>
		<option value='a' $advance_selected>Advance</option>
    </select></td>
    <td><input name="c_amount[1]" id="collection_amt1"  type="text"  size="10" onchange="calculate_total(this);"  onkeyup="javascript:extractNumber(this,2,false);"  onkeypress="javascript:return blockNonNumbers(this, event, true, false);"> </td>           		
	<td><input type="text"  name="remarks[1]" id="remarks1" size="20"></td>
	</tr></tbody><tfoot><tr><td colspan="7" align="center">
		<input id="receipt_addrow" name="button" onclick="addRow_collection();" value="Add " type="button" class="button"> 
		<input id="receipt_removerow" value="Remove" onclick="removeRowFromTable_collection();" type="button" class="button">
	</td></tr></tfoot></table></td></tr>
	<tr><td colspan="2">
<div class="hide" id="div_extra_collection">
	<table border="1"  id="table_extra_collection">
	<tbody><tr class="modulelisttitle"><td colspan="7" align="center">Extra Charges</td></tr>
	<tr><TD align="center"  width="10" >S.No</TD>
	<td align="center"  width="150" >Order No </td>
	<td align="center"  width="150">Purpose of Collection </td>
	<td align="center"  width="50">Listed Amount </td><td align="center"  width="50">Receipt Amount [$currency]</td>
	<td align="center"  width="150">Remarks </td></tr>
	<tr><td>1</td>
	<td><select size="1" name="order_no_e[1]"  id='order_no_e1'   class="formStyleTextarea" onchange="changeOrderNo(this);">
	<option value="0">select</option>
END;
	$receiptnoedit=($group_id!=1?"readonly":"");
	$tds_option=(is_authorized_group_list($uid,array(1,17))?"<option value='4'>TDS</option><option value='5'>Paypal</option><option value='6'>EBS</option>":"");
	$receipt_num = $coll_for = $amt_readonly = '';
	$hide_receipt_field = ($form_type=='new'?" hide":"");
	if(!$suspense) {
		$receipt_num =<<<END
		<TR class='$hide_receipt_field'><TD class="datalabel" width="175">Receipt Number</TD>
		<TD><INPUT id="rid" name="rid" $receiptnoedit class="formStyleTextarea" ></TD></TR>
END;
		$collection_type = get_two_dimensinal_array_from_table("gft_collection_type_master","GCT_ID","GCT_NAME","GCT_STATUS",'1',"GCT_NAME");
		$combo = fix_combobox_with('collection_for','collection_for',$collection_type,'',null,'',null,false,"",3);
		$coll_for =<<<END
		<TR><TD class="datalabel" width="175"><span style="color:red;">*</span>Collection For&nbsp;</TD>
		<TD>$combo</TD></TR>
END;
		$amt_readonly = "readonly";
	}
$blocknonnumbers =<<<SCRIPT
onkeypress="javascript:return blockNonNumbers(this, event, false, false);"
SCRIPT;
if($suspense) {
	$blocknonnumbers = '';
}
echo<<<END
</select></td><td>
<SELECT name="cmbproduct_e[1]" id="cmbproduct_e1" onchange="getextraCollectionAmt(this);"><option value=0>Select</option></SELECT></TD>
<td><input type="text" name="ass_listed_amount[1]" id="ass_listed_amount1"  Readonly='true' class="formStyleTextarea"  size="10"></TD>
<td><input type="text" id="collection_eamt1" name="collection_eamt[1]" size="10" onchange="calculate_total(this);"  onkeyup="javascript:extractNumber(this,2,false);" onkeypress="javascript:return blockNonNumbers(this, event, true, false);" ></td>
<td><input type="text" id="eremarks1" name="eremarks[1]" size="20"></td></tr></tbody>
<tfoot><tr><td colspan="6" align="center">
<input type="button" onclick="addRow_extra();" value="Add "  class="button"> 
<input type="button" value="Remove " onclick="removeRowFromTable_extra();" class="button"></td></tr></tfoot></table></div></td></tr>
<tr><td colspan="2">
<TABLE  border="0" cellPadding="0" cellSpacing="2" width="100%" id="receipt_entry_dtl" class="hide">  
$receipt_num
<TR><TD class="datalabel" width="175"><span style="color:red;">*</span>Receipt Amount [$currency]&nbsp;</TD>
<TD><INPUT id="ramt" name="ramt" value='$amount' class="formStyleTextarea" onkeyup="javascript:extractNumber(this,2,false);" $blocknonnumbers onBlur="javascript:receipt_amt();" $amt_readonly></TD></TR>
$coll_for
<tr><td valign="top" class="datalabel"><span style="color:red;">*</span>Receipt Type</td><TD valign="top">
END;
$receipt_type_query = "select GRT_TYPE_CODE,GRT_TYPE_NAME from gft_receipt_type_master ";
if(!is_authorized_group($uid, 17)){
	$receipt_type_query.= " where GRT_FOR_SALES='Y' ";
}
//$receipt_type_list=get_two_dimensinal_array_from_table('gft_receipt_type_master','GRT_TYPE_CODE', 'GRT_TYPE_NAME');
$receipt_type_list=get_two_dimensinal_array_from_query($receipt_type_query, 'GRT_TYPE_CODE', 'GRT_TYPE_NAME');
echo fix_combobox_with('rtype','rtype',$receipt_type_list,$receipt_type,null,'Select',null,false,"onchange='javascript:show_terr();'");
echo<<<END
</td>
<td><div id="c_terms" class="hide">
<TABLE border="0" cellPadding="0" cellSpacing="2" width="100%" >
<tr><td valign="top" class="datalabel">Terms regarding Collection</td>
<TD><textarea  class="formStyleTextarea"  name="collection_terms" id="collection_terms" rows="2" cols="40">$terms</textarea></td></tr>
</TABLE></div></td>
</tr>
<TR id='c_stat'><td id='td_cc_stat' valign="top" class="datalabel" width="20%"><span style="color:red;">*</span>Cash / Cheque / DD status</td>
<TD><select size="1" name="status" id="status" onclick="javascript:show_dtl_regarding_status(this);" class="formStyleTextarea"><option value="">Select</option>
END;
		for($i=2;$i<count($cq_status_list);$i++){
			$selected = '';
			if($cheque_status==$cq_status_list[$i][0]) {
				$selected = 'selected="selected"';
			}
			echo "<option value=\"".$cq_status_list[$i][0]."\" $selected>".$cq_status_list[$i][1]."</option>";
		}
echo<<<END
</select></td></TR>
<tr><td colspan="2"><div id="dtl" class="hide">
<TABLE border="0" cellPadding="0" cellSpacing="2" width="100%" >
<TR><TD class="datalabel" width="170">$red_star Cheque / DD No</TD>
<TD><INPUT id="cno" name="cno"  class="formStyleTextarea" value='$cheque_dd_no' size="20"></TD></TR>
<tr><td align="right"  class="datalabel" nowrap>$red_star Cheque /DD Date</td>
<td><input name="cheque_date" type="text" class="formStyleTextarea" id="cheque_date" value="$cheque_dd_dt" readonly>&nbsp;
<img src="images/date_time.gif" class="imagecur" id="onceDateIcon2" alt="" width=16 height=16 border=0 ></td></tr><TR><TD class="datalabel" >$red_star Bank Name</TD>
<TD><INPUT id="bname" name="bname" size="40" class="formStyleTextarea" value='$cheque_dd_bank_name'></TD></TR></table></div>
<div id="net_transfer" class="hide">
<TABLE border="0" cellPadding="0" cellSpacing="2" width="100%" >
<TR><TD class="datalabel" width="175"><span style="color:red;">*</span>UTR No</TD>
<TD><INPUT id="utrno" name="utrno" value='$utr' class="formStyleTextarea" size="20"></TD></TR>
<tr><td align="right"  class="datalabel" nowrap><span style="color:red;">*</span>Transferred Date</td>
<td><input name="transfer_date" type="text" value='$transfer_dt' class="formStyleTextarea" id="transfer_date" readonly>&nbsp;
<img src="images/date_time.gif" class="imagecur" id="onceDateIcon3" alt="" width=16 height=16 border=0 ></td></tr>
<tr><td align="right"  class="datalabel" nowrap>Transferer Name / Concern </td>
<td><input name="transfer_name" type="text" class="formStyleTextarea" id="transfer_name" value=""></td>
</table></div>
<div id="paypal" class="hide">
<TABLE border="0" cellPadding="0" cellSpacing="2" width="100%" >
<TR><TD class="datalabel" width="175"><span style="color:red;">*</span>Unique Transaction ID</TD>
<TD><INPUT id="utid" name="utid" value='$utid' class="formStyleTextarea" size="20"></TD></TR>
</table></div>
</td></tr>
<tr><td colspan="2">
<div id="deposited_details" class="hide">
<table border="0" cellPadding="0" cellSpacing="2" width="100%" >        
<tr><td class="datalabel" width="170"><span style="color:red;">*</span>Deposited Bank</td>
<td><input type="text" id="deposited_bank" name="deposited_bank" value="$deposited_bank" class="formStyleTextarea" size="50">
<tr><td class="datalabel"><span style="color:red;">*</span>Deposited Branch</td>
<td><input type="text" id="deposited_branch" name="deposited_branch" value='$deposited_branch' class="formStyleTextarea" size="50">
<tr><td align="right"  class="datalabel" nowrap><span style="color:red;">*</span>Deposited Date</td>
<td><input name="deposited_date" type="text" class="formStyleTextarea" id="deposited_date" value="$deposit_dt" readonly>&nbsp;
<img src="images/date_time.gif" class="imagecur" id="onceDateIcon3d" alt="" width=16 height=16 border=0 ></td></tr>
<tr><td class="datalabel">Deposited Branch Code</td>
<td><input type="text" id="deposited_branch_code" name="deposited_branch_code" class="formStyleTextarea" value="$deposit_branch_code" size="20"></tr> 
<tr><td class='datalabel'>Challen No</tD>
<td><input name="challan_no" type="text" class="formStyleTextarea" id="challan_no" value="$chalan_no" size="20"></td></tr></table></div></td></tr>
<tr><td colspan="2"><div id="cleared_date" class="hide">
<table><tr><td class="datalabel" width="175"><span style="color:red;">*</span>A/C Cleared Date</td>
<td><input name="cheque_cleared_date" type="text" class="formStyleTextarea" id="cheque_cleared_date" value="" readonly>&nbsp;
<img src="images/date_time.gif" class="imagecur" id="onceDateIcon4c" alt="" width=16 height=16 border=0></td>
</tr></table></div></td></tr>  
<tr><td colspan="2"><div id="hand_over_to_dtl"  class="hide">
<table><tr><td class="datalabel" width="175">$red_star Hand over to</td><td>
END;
$handover_emp_list=get_emp_list_by_group_filter(array(5,6,17,68),false,((isset($_GET['Edit']) and (string)$_GET['Edit'])=='edit'?'A':'A'));
echo fix_combobox_with('emp_code2','emp_code2',$handover_emp_list,$handed_over_to,'','Select');
echo<<<END
</td></tr>
<tr><td class="datalabel" width="175">$red_star Hand over Date</td><td><input name="hand_over_date" type="text" class="formStyleTextarea" id="hand_over_date" value="$handed_over_dt" readonly>&nbsp;
<img src="images/date_time.gif" class="imagecur" id="onceDateIcon4h" alt="" width=16 height=16 border=0 ></td>
</tr></table></div></td></tr>
$gstin_input_design;
</TABLE>
<script type="text/javascript">
var jq = jQuery.noConflict();
jq('document').ready(function() {
	receipt_entry_ui();
	if(parseInt($suspense)==1) {
		var recipt_type = $("rtype").value;
		if(recipt_type=="2" || recipt_type=="3"){
	    	$("dtl").style.display="block";
		}else{
			$("dtl").style.display="none";
		}
		if(recipt_type=="5"){
			$("paypal").style.display="block";
		}else{
			$("paypal").style.display="none";
		}
		if(recipt_type=="7"){
			$("net_transfer").style.display="block";
			document.getElementById("td_cc_stat").innerHTML="Cash / Cheque / DD status";
		}else{
			$("net_transfer").style.display="none";
			document.getElementById("td_cc_stat").innerHTML="<span style='color:red;'>*</span>Cash / Cheque / DD status";
		}
		show_dtl_regarding_status('');
		var receipt_type = jq('#rtype').val();
		if(receipt_type!='1' && receipt_type!='2' && receipt_type!='3') {
			jq('#status option[value=""]').attr('selected', 'selected');
		}
		if(jq('#status option').size() > 1){
			jq("#c_stat").removeClass("hide").addClass("show");
		}else{
			jq("#c_stat").removeClass("show").addClass("hide");
		}        
	}
    jq("#customer_gstin_status").change(function(){
        if(jq('#customer_gstin_status option').size() > 1 && jq("#customer_gstin_status").val()==3){
			jq("#gstin_update_date_div").removeClass("hide").addClass("show");
		}else{
			jq("#gstin_update_date_div").removeClass("show").addClass("hide");
		}
    });
});
function show_dtl_regarding_status(obj){
	var stat_val = jq("#status").val(); 
	var receipt_type = jq('#rtype').val();
	if((stat_val=="D" || stat_val=="P") && (receipt_type=='1' || receipt_type=='2' || receipt_type=='3')){
		$("deposited_details").style.display="block";
	}else{
    	$("deposited_details").style.display="none";
    }
    if(stat_val=="P"){
		$("cleared_date").style.display="block";
    }else{
    	$("cleared_date").style.display="none";
    }
   	if(stat_val=="W"){
		$("hand_over_to_dtl").style.display="block";
    }else{
    	$("hand_over_to_dtl").style.display="none";
    }
}
function show_terr(){
	var recipt_type = $("rtype").value;
	if(recipt_type=="2" || recipt_type=="3"){
    	$("dtl").style.display="block";
	}else{
		$("dtl").style.display="none";
	}
	if(recipt_type=="5"){
		$("paypal").style.display="block";
	}else{
		$("paypal").style.display="none";
	}
	if(recipt_type=="7"){
		$("net_transfer").style.display="block";
		document.getElementById("td_cc_stat").innerHTML="Cash / Cheque / DD status";
	}else{
		$("net_transfer").style.display="none";
		document.getElementById("td_cc_stat").innerHTML="<span style='color:red;'>*</span>Cash / Cheque / DD status";
	}
	update_status_combo(recipt_type);
	show_dtl_regarding_status("");
	$("c_terms").style.display="block";
	if(jq('#status option').size() > 1){
		jq("#c_stat").removeClass("hide").addClass("show");
	}else{
		jq("#c_stat").removeClass("show").addClass("hide");
	}
}	
function empEmptyFunction(){}
function empDetails(){}
init_date_func("deposited_date","%Y-%m-%d","onceDateIcon3d","Bl");
init_date_func("cheque_cleared_date","%Y-%m-%d","onceDateIcon4c","Bl");
init_date_func("cheque_date","%Y-%m-%d","onceDateIcon2","Bl");
init_date_func("transfer_date","%Y-%m-%d","onceDateIcon3","Bl");
init_date_func("hand_over_date","%Y-%m-%d","onceDateIcon4h","Bl");
function update_status_combo(r_type) {
	selectHTML="<option value=''>Select</option>";
	for(i=0; i<cheque_stat_arr.length; i++){
		var temp = new Array();
		temp = cheque_stat_arr[i].split('-');
		if(r_type==temp[0]) {
			selectHTML+= "<option value='"+temp[1]+"'>"+temp[2]+"</option>";				
		}
	}
	jq('#status').find('option').remove().end().append(selectHTML);
}

function advance_clicked(){
	if(jq("#advance1").is(":checked")){
		if(jq("#allow_adv").val()=='N'){
			jq("#advance1").prop("checked","");
			var bal_amt = jq("#tot_balance_amt").val()
			alert("New Order Advance cannot be placed due to outstanding amount of Rs."+bal_amt+". So place Outstanding Collection Entry First");
			return false;
		}
		jq("#table_advance_collection").removeClass("hide").addClass("show");
	}else{
		jq("#table_advance_collection").removeClass("show").addClass("hide");
	}
	receipt_entry_ui();
}
		
function order_clicked(){
	if(jq("#order1").is(":checked")){
		jq("#table_order_collection").removeClass("hide").addClass("show");
		jq("#table_order_inv").removeClass("hide").addClass("show");
	}else{
		jq("#table_order_collection").removeClass("show").addClass("hide");
		jq("#table_order_inv").removeClass("show").addClass("hide");
	}
	receipt_entry_ui();
}

function receipt_entry_ui(){
	if( jq("#advance1").is(":checked") || jq("#order1").is(":checked") || (jq("#suspense_entry").length==1 && jq("#suspense_entry").val()=='yes')){
		jq("#receipt_entry_dtl").removeClass("hide").addClass("show");
	}else{
		jq("#receipt_entry_dtl").removeClass("show").addClass("hide");
	}		
}
		
</script>
END;
}

/**
 * @param string $receit_id
 *
 * @return void
 */
function receipt_edit($receit_id){

	$lcode='';
	if($receit_id==''){
		return;
	}
			$receipt_query=" SELECT GRD_RECEIPT_ID, GRD_DATE, GRD_EMP_ID, em1.gem_emp_name, GRD_RECEIPT_TYPE," .
				" GRD_RECEIPT_AMT, GRD_CHEQUE_DD_NO, GRD_CHEQUE_DD_DATE, GRD_BANK_NAME," .
				" GRD_DEPOSITED_BANK, GRD_DEPOSITED_DATE, GRD_DEPOSITED_BRANCH_NAME, GRD_DEPOSITED_BRANCH_CODE," .
				" GRD_DEPOSTIT_CHALLAN_NO, GRD_CHEQUE_CLEARED_DATE, GRD_HAND_OVER_TO,em1.gem_emp_name handover," .
				" GRD_HAND_OVER_DATE,GRD_USD_AMT,GRD_CONVERSION_RATE,glh_country, ".
				" GRD_STATUS, GRD_TERMS_REGARDING_COLLECTION, GRD_CHECKED_WITH_LEDGER,GRD_COLLECTION_TYPE," .
				" GRD_RECEIPT_ID_REF, GRD_LEAD_CODE, glh_cust_name, GRD_REPORTED_DATE, GRD_COLLECTION_DATE, GRD_REFUND_AMT, " .
				" GRD_COLLECTION_AGAINST_TYPE, GRD_COLLECTION_AGAINST_QUOTE FROM gft_receipt_dtl " .
				" join gft_emp_master em1 on (em1.gem_emp_id=GRD_EMP_ID) " .
				" join gft_lead_hdr on (glh_lead_code=GRD_LEAD_CODE)" .
				" left join gft_emp_master em2 on (GRD_HAND_OVER_TO=em2.gem_emp_id) " .
				" where GRD_RECEIPT_ID='$receit_id' ";
			
		$receipt_result=execute_my_query($receipt_query);
		$data=mysqli_fetch_array($receipt_result);
		if(!$data){
			//Invalid receit_id 
			return;
		}
			$visit_date = $date_on = $data['GRD_DATE'];
			$emp_id=$data['GRD_EMP_ID'];
			$emp_name=$data['gem_emp_name'];
			$receipt_type=$data['GRD_RECEIPT_TYPE'];
			$receipt_amt=$data['GRD_RECEIPT_AMT'];
			$usd_amt = $data['GRD_USD_AMT'];
			$conv_rate = $data['GRD_CONVERSION_RATE'];
			$country = $data['glh_country'];
			$cheque_no=$data['GRD_CHEQUE_DD_NO'];
			$cheque_date=$data['GRD_CHEQUE_DD_DATE'];
			$bank_name=$data['GRD_BANK_NAME'];
			$deposit_bank=$data['GRD_DEPOSITED_BANK'];
			$deposit_date=$data['GRD_DEPOSITED_DATE'];
			$deposit_branch_name=$data['GRD_DEPOSITED_BRANCH_NAME'];
			$deposit_branch_code=$data['GRD_DEPOSITED_BRANCH_CODE'];
			$deposit_challan_no=$data['GRD_DEPOSTIT_CHALLAN_NO'];
			$cheque_clear_date=$data['GRD_CHEQUE_CLEARED_DATE']; 
			$handover_to=$data['GRD_HAND_OVER_TO'];
			$handover_to_name=$data['handover'];
			$handover_date=$data['GRD_HAND_OVER_DATE'];
			$receipt_status=$data['GRD_STATUS']; 
			$comments=trim(stripslashes($data['GRD_TERMS_REGARDING_COLLECTION']));
			$comments=(string)str_replace("\n","",$comments);
			$checked_with_ledger=$data['GRD_CHECKED_WITH_LEDGER'];
			$receipt_id_reff=$data['GRD_RECEIPT_ID_REF']; 
			$lcode=$data['GRD_LEAD_CODE'];
			$reported_date=$data['GRD_REPORTED_DATE'];
			$collection_date=$data['GRD_COLLECTION_DATE'];
			$cust_name=$data['glh_cust_name'];
			$refund_amt=$data['GRD_REFUND_AMT'];
			$collection_for=$data['GRD_COLLECTION_TYPE'];
			$collection_against = $data['GRD_COLLECTION_AGAINST_TYPE'];
			$collection_against_quote=$data['GRD_COLLECTION_AGAINST_QUOTE'];
		$receipt_order_query= " SELECT GCR_ORDER_NO, GCR_RECEIPT_ID, GCR_PAYMENT_FORID," .
				" GCR_AMOUNT, GCR_REASON, GCR_OTHERS_LISTED_PRICE " .
				" FROM gft_collection_receipt_dtl where GCR_RECEIPT_ID='$receit_id' order by GCR_REASON ";
		//echo $receipt_order_query; 
		$receipt_order_res=execute_my_query($receipt_order_query);
		$i=0;
		$advance_order_no='';
		$adv_amt = 0;
		$coll_order_no=/*. (string[int]) .*/ array();
		$collection_reason=/*. (string[int]) .*/ array();
		$payment_for=/*. (string[int]) .*/ array();
		$collection_amt=/*. (string[int]) .*/ array();
		$other_list_prize=/*. (string[int]) .*/ array();
		while($data_order=mysqli_fetch_array($receipt_order_res)){
			$advance_reason=$data_order['GCR_REASON'];
			if($advance_reason=='-1'){
				$adv_amt = (int)$data_order['GCR_AMOUNT'];
				if(strcasecmp($country,'India')!=0) {
					$adv_amt /= $conv_rate;
				}
				echo "<script>jq('#advance1').attr('checked','checked'); advance_clicked();</script>";
			}else{
				echo "<script>jq('#order1').attr('checked','checked'); order_clicked();</script>";
				$coll_order_no[$i]=$data_order['GCR_ORDER_NO'];
				$collection_reason[$i]=($data_order['GCR_REASON']!='-1'?$data_order['GCR_REASON']:"a");
				$payment_for[$i]=($data_order['GCR_REASON']!='-1'?$data_order['GCR_PAYMENT_FORID']:"a-a-a"); 
				$collection_amt[$i]=$data_order['GCR_AMOUNT'];
				if(strcasecmp($country,'India')!=0) {
					$collection_amt[$i] /= $conv_rate;
				}
				$other_list_prize[$i]=$data_order['GCR_OTHERS_LISTED_PRICE'];
				$i++;
			}
		}
		$query_order="SELECT GOD_ORDER_NO FROM gft_order_hdr where god_lead_code='$lcode' and (god_order_status='A' or GOD_ORDER_AMT>0) ";
		$result_order=execute_my_query($query_order);
		$no_orders=mysqli_num_rows($result_order);
	    $i=0;
	    $order_list=/*. (string[int]) .*/ array();
		while($qdata=mysqli_fetch_array($result_order)){
			$order_list[$i]=$qdata[0];
			$i++;
		}
		if(count($order_list)==0){
			$result=execute_my_query("SELECT GCO_ORDER_NO FROM gft_cp_order_dtl WHERE GCO_CUST_CODE='$lcode' ");
			$i=0;
			$indirorderno=/*. (string[int]) .*/ array();
			while($data=mysqli_fetch_array($result)){
				$indirorderno[$i]=$data[0];
				$i++; 	
			}
			$order_list=$indirorderno;
		}else{
			$indirorderno=$order_list;
		}
		$order_no_str=implode(',',$order_list);
		$coll_order_no_str=implode(',',$coll_order_no);
		$payment_for_str=implode(',',$payment_for);
		$collection_amt_str=implode(',',$collection_amt);
		$collection_reason_str=implode(',',$collection_reason);
		$other_list_prize_str=implode(',',$other_list_prize);
		if( ($receipt_type=='7') || ($receipt_status=='D') ){
			$receipt_status="";
		}
		$display_amt = $receipt_amt;
		if(strcasecmp($country,'India')!=0) {
			$display_amt = "$usd_amt";
		}
echo<<<END
<script type="text/javascript">
jQuery.noConflict();
if($refund_amt!=0) alert("Refund Given for the receipt ! Do not Edit the receipt ! ");
var arr_order_list= "$order_no_str".split(",");
$('visit_date').value= "$visit_date";
$('emp_name').value="$emp_name";
$('emp_code').value="$emp_id";
$('customer_name').value="$cust_name";
$('lead_code').value="$lcode";
$('rtype').value="$receipt_type";
var collection_for="$collection_for";
var collection_against_quote_no="$collection_against_quote";
if(collection_for!=''){
	jQuery.each(collection_for.split(","), function(i,e){
    	jQuery("#collection_for option[value='" + e + "']").prop("selected", true);
	});
}
show_terr();
var collection_against="$collection_against";
if(collection_against=="1"){
jQuery("#collection_against1").prop("checked", true);
jQuery("#collection_against_quotation_no").show();
jQuery("#collection_against_quotation_no option[value='" + collection_against_quote_no + "']").prop("selected", true);
}
if(collection_against=="2"){
jQuery("#collection_against2").prop("checked", true);
jQuery("#collection_against_proforma_no").show();
jQuery("#collection_against_proforma_no option[value='" + collection_against_quote_no + "']").prop("selected", true);
}
if($('conv_rate')) $('conv_rate').value='$conv_rate';
$('cno').value="$cheque_no";
$('utrno').value="$cheque_no";
$('utid').value="$cheque_no";
$('cheque_date').value="$cheque_date";
$('transfer_date').value="$cheque_date";
$('bname').value="$bank_name";
$('rid').value="$receipt_id_reff";
$('ramt').value="$display_amt";
$('collection_terms').value="$comments";
$('status').value="$receipt_status";
show_dtl_regarding_status($('status'));
$('emp_code2').value="$handover_to";
$('hand_over_date').value="$handover_date";
$('deposited_bank').value="$deposit_bank";
$('deposited_date').value="$deposit_date";
$('deposited_branch').value="$deposit_branch_name";
$('deposited_branch_code').value="$deposit_branch_code";
$('challan_no').value="$deposit_challan_no";
$('cheque_cleared_date').value="$cheque_clear_date"; 
$('adv_amt').value="$adv_amt";
var cmb=$("order_no_coll1");
cmb.options.length=1;
for(k=0;k<arr_order_list.length;k++){
	if(arr_order_list[k]!=''){
	option=document.createElement("OPTION");
	cmb.options.add(option);
	txt=document.createTextNode(arr_order_list[k]);
	option.appendChild(txt);
	option.value=arr_order_list[k];
	}
}
END;
$balance_list	=	'';
if(isset($_REQUEST['empid'])){
	$balance_order_no="000".substr("0000".(string)$_REQUEST['empid'],-4).substr("00000000".(string)$_REQUEST['lead_code'],-8);
	$balance_list	=<<<END
	option=document.createElement("OPTION");
	cmb1.options.add(option);
	txt=document.createTextNode("$balance_order_no");
	option.appendChild(txt);
	option.value="$balance_order_no";
	option.selected="selected";
END;
}

if($advance_order_no!=''){
echo<<<END
	var cmb1=$("order_no_e1");
	$("div_extra_collection").className="unhide";
	option=document.createElement("OPTION");
	cmb1.options.add(option);
	txt=document.createTextNode("$advance_order_no");
	option.appendChild(txt);
	option.value="$advance_order_no";
	option.selected="selected";
	
	$balance_list
	
	var cmb2=$("cmbproduct_e1");	
	option=document.createElement("OPTION");
	cmb2.options.add(option);
	txt=document.createTextNode("CP Advance");
	option.appendChild(txt);
	option.value="a-a-a";
	option.selected="selected";
END;
}
echo<<<END
var coll_order_no= "$coll_order_no_str".split(",");
var payment_for="$payment_for_str".split(",");
var collection_amt="$collection_amt_str".split(",");
var collection_reason="$collection_reason_str".split(",");
var other_list_prize="$other_list_prize_str".split(",");
  
for(k=0,l=0;k<coll_order_no.length;k++){
	if(collection_reason[k]=='1'){
		j=l+1;
		if(l!=0){
			addRow_collection();
		}
		$("order_no_coll"+j).value=coll_order_no[k];
		$("ptype"+j).value=payment_for[k];
		$("collection_amt"+j).value=collection_amt[k]
		updateCityState($("order_no_coll"+j));
		l++;
	}
}
for(k=0,l=0;k<coll_order_no.length;k++){
	if(collection_reason[k]!='1'){
		j=l+1;
		if(l!=0){
			addRow_extra();
		}
		$("order_no_e"+j).value=coll_order_no[k];
		$("cmbproduct_e"+j).value=payment_for[k];
		$("ass_listed_amount"+j).value=other_list_prize[k];
		$("collection_eamt"+j).value=collection_amt[k];
		l++;
	}
}
</script>
END;
}

/**
 * @param string $lead_code
 * 
 * @return int
 */
function get_customer_advance($lead_code) {
	$ret_arr = 0;
	$sel_quer = " select sum(GCR_AMOUNT) as adv_amt from gft_collection_receipt_dtl ".
			" join gft_receipt_dtl on (GCR_RECEIPT_ID=GRd_RECEIPT_ID) ".
			" where GRD_LEAD_CODE='$lead_code' and GCR_REASON='-1' and GRD_CHECKED_WITH_LEDGER='Y' ";
	$res1 = execute_my_query($sel_quer);
	if($row1 = mysqli_fetch_array($res1)){
		$ret_arr = (float)$row1['adv_amt'];
	}
	return $ret_arr;
}

/**
 * @param string $lead_code
 *
 * @return void
 */
function show_advance_selection($lead_code){
	$adv_amount = get_customer_advance($lead_code);
	$threshold_advance_amount=get_samee_const('Threshold_Advance_Amount');
	$red_star = "<span style='color:red;'>*</span>";
	echo<<<END
<table style="width:100%;">
	<tr> <td class="datalabel"> Available Advance : </td> <td> Rs. <input type="text" id="adv" value="0" readonly>
	<input type=hidden id="threshold_advance_amount" name="threshold_advance_amount" value="$threshold_advance_amount">
	<INPUT type=hidden id="ramt_per" name="ramt_per" class="formStyleTextarea" type="text" size="5">
	</td> </tr>
	<tr> <td class="datalabel">$red_star Amount to Utilize : </td> <td> Rs. <input type="text" id="ramt" name="ramt" value="" onkeypress="javascript:return blockNonNumbers(this, event, true, false);" onkeyup="javascript:extractNumber(this,2,false);"> </td> </tr>
</table>
<script>
var advance_val="$adv_amount";
</script>
END;
}

/**
 * @param string $lead_code
 * @param string $order_no
 * @param float $deduct_amount
 *
 * @return void
 */
function deduct_from_advance($lead_code, $order_no, $deduct_amount){
	$order_by = get_single_value_from_single_table("GOD_EMP_ID", "gft_order_hdr", "GOD_ORDER_NO", $order_no);
	$sel_quer = " select GCR_AMOUNT,GRD_RECEIPT_ID from gft_collection_receipt_dtl ".
			" join gft_receipt_dtl on (GCR_RECEIPT_ID=GRD_RECEIPT_ID) ".
			" where GRD_LEAD_CODE='$lead_code' and GCR_REASON='-1' and GRD_STATUS in ('C','P') and GRD_ACCOUNT!=3 ".
			" order by if(GRD_EMP_ID='$order_by',0,GRD_RECEIPT_ID) ";
	
	//if partner balance order number is 15 digit format where as if customer the balance order number is leadcode.
	$balance_order_no = get_partner_balance_order($lead_code);
	if($balance_order_no==''){
		$balance_order_no = $lead_code;
	}
	
	$gcr_order_condition = " and GCR_ORDER_NO='$balance_order_no' ";
	$res1 = execute_my_query($sel_quer);
	while( ($row1 = mysqli_fetch_array($res1)) && ($deduct_amount > 0) ){
		$gcr_amt		 = (float)$row1['GCR_AMOUNT'];
		$grd_receipt_id  = $row1['GRD_RECEIPT_ID'];
		if($gcr_amt >= $deduct_amount){
			$utilize = $deduct_amount;
			$remaining_amt = $gcr_amt-$deduct_amount;
		}else{
			$utilize = $gcr_amt;
			$remaining_amt = 0;
		}
		$deduct_amount = $deduct_amount - $utilize;
		$ins_quer = " insert into gft_collection_receipt_dtl " .
				" (GCR_ORDER_NO, GCR_RECEIPT_ID, GCR_PAYMENT_FORID, GCR_AMOUNT ,GCR_REASON, GCR_REMARKS) " .
				" values ('$order_no', '$grd_receipt_id', 'a', '$utilize','1','From Advance')";
		execute_my_query($ins_quer);
		$upd_quer = " update gft_collection_receipt_dtl set GCR_AMOUNT='$remaining_amt' where GCR_RECEIPT_ID='$grd_receipt_id' $gcr_order_condition";
		execute_my_query($upd_quer);
		update_incentive_earnings($grd_receipt_id);
	}
	//To delete the fully uitlized receipt amounts from collection detail
	execute_my_query("delete from gft_collection_receipt_dtl where GCR_AMOUNT=0  and GCR_REASON='-1' $gcr_order_condition ");
}
/**
 * @param int $uid
 * @param string $lead_code
 * @param string[] $collection_dtl
 * 
 * @return int 
 */
function update_receipt_details_from_app($uid, $lead_code, $collection_dtl){
	//$emp_for_pc_support_collection=emp_for_pc_support_collection($uid, $lead_code);
	//$emp_id     =   ($emp_for_pc_support_collection!==0)?$emp_for_pc_support_collection:$uid;
	$emp_id = $uid;
	$receipt_entry_for	=isset($collection_dtl['receipt_entry_for'])?(int)$collection_dtl['receipt_entry_for']:'';
	$receipt_amount		=isset($collection_dtl['receipt_amount'])?$collection_dtl['receipt_amount']:'';
	$receipt_remarks	=isset($collection_dtl['receipt_remarks'])?mysqli_real_escape_string_wrapper($collection_dtl['receipt_remarks']):'';
	$receipt_type		=isset($collection_dtl['receipt_type'])?$collection_dtl['receipt_type']:'';
	$cash_cheque_dd_status=isset($collection_dtl['cash_cheque_dd_status'])?$collection_dtl['cash_cheque_dd_status']:'';
	$deposited_bank		=isset($collection_dtl['deposited_bank'])?$collection_dtl['deposited_bank']:'';
	$deposited_branch	=isset($collection_dtl['deposited_branch'])?$collection_dtl['deposited_branch']:'';
	$deposited_date		=isset($collection_dtl['deposited_date'])?$collection_dtl['deposited_date']:'';
	$cheque_dd_no		=isset($collection_dtl['cheque_dd_no'])?$collection_dtl['cheque_dd_no']:'';
	$cheque_dd_date		=isset($collection_dtl['cheque_dd_date'])?$collection_dtl['cheque_dd_date']:'';
	$bank_name			=isset($collection_dtl['bank_name'])?$collection_dtl['bank_name']:'';
	$hand_over_to_emp	=isset($collection_dtl['hand_over_to_emp'])?$collection_dtl['hand_over_to_emp']:'';
	$hand_over_date		=isset($collection_dtl['hand_over_date'])?$collection_dtl['hand_over_date']:'';
	$utr_no				=isset($collection_dtl['utr_no'])?$collection_dtl['utr_no']:'';
	$transfer_date		=isset($collection_dtl['transfer_date'])?$collection_dtl['transfer_date']:'';
	$transferer_name	=isset($collection_dtl['transferer_name'])?$collection_dtl['transferer_name']:'';
	$transaction_no		=isset($collection_dtl['transaction_no'])?$collection_dtl['transaction_no']:'';
	$visit_date 		=isset($collection_dtl['visit_date'])?$collection_dtl['visit_date']:'';
	$outstanding_order_no=isset($collection_dtl['outstanding_order_no'])?$collection_dtl['outstanding_order_no']:'';
	$collection_for		=isset($collection_dtl['collection_for'])?$collection_dtl['collection_for']:'';
	$collection_against=isset($collection_dtl['collection_against'])?$collection_dtl['collection_against']:'0';
	$quotation_collection=isset($collection_dtl['quotation_collection'])?$collection_dtl['quotation_collection']:'';
	$proforma_collection=isset($collection_dtl['proforma_collection'])?$collection_dtl['proforma_collection']:'';
	$customer_gstin_status=isset($collection_dtl['customer_gstin_status'])?(int)$collection_dtl['customer_gstin_status']:0;
	$gstin_update_date=isset($collection_dtl['gstin_update_date'])?$collection_dtl['gstin_update_date']:'';
	if($collection_against==2){
		$quotation_collection=$proforma_collection;
	}
	$reported_date=date('Y-m-d H:i:s',time());
	if($cash_cheque_dd_status==""){
		$cash_cheque_dd_status='D';
	}
	if($utr_no!=''){
		$cheque_dd_no	=	$utr_no;		
	}
	if($transaction_no!=''){
		$cheque_dd_no	=	$transaction_no;
	}
	if($receipt_type==7){ //Net Transfer
		$cheque_dd_date=$transfer_date;
	}
	$insert_arr = /*. (string[string]) .*/array();
	$insert_arr['GRD_DATE'] = $visit_date;
	$insert_arr['GRD_EMP_ID'] = $emp_id;
	$insert_arr['GRD_RECEIPT_TYPE'] = $receipt_type;
	$insert_arr['GRD_RECEIPT_AMT'] = $receipt_amount;
	$insert_arr['GRD_CHEQUE_DD_NO'] = $cheque_dd_no;
	$insert_arr['GRD_CHEQUE_DD_DATE'] = $cheque_dd_date;
	$insert_arr['GRD_BANK_NAME'] = $bank_name;
	$insert_arr['GRD_DEPOSITED_BANK'] = $deposited_bank;
	$insert_arr['GRD_DEPOSITED_DATE'] = $deposited_date;
	$insert_arr['GRD_DEPOSITED_BRANCH_NAME'] = $deposited_branch;
	$insert_arr['GRD_HAND_OVER_TO'] = $hand_over_to_emp;
	$insert_arr['GRD_STATUS'] = $cash_cheque_dd_status;
	$insert_arr['GRD_TERMS_REGARDING_COLLECTION'] = $receipt_remarks;
	$insert_arr['GRD_CHECKED_WITH_LEDGER'] = 'N';
	$insert_arr['GRD_HAND_OVER_DATE'] = $hand_over_date;
	$insert_arr['GRD_LEAD_CODE'] = $lead_code;
	$insert_arr['GRD_REPORTED_DATE'] = $reported_date;
	$insert_arr['GRD_COLLECTION_DATE'] = $visit_date;
	$insert_arr['GRD_COLLECTION_TYPE'] = $collection_for;
	$insert_arr['GRD_ENTRY_BY_EMP_ID'] = $emp_id;
	$insert_arr['GRD_ENTRY_DATE'] = date('Y-m-d H:i:s');
	$insert_arr['GRD_COLLECTION_AGAINST_TYPE'] = $collection_against;
	$insert_arr['GRD_COLLECTION_AGAINST_QUOTE'] = $quotation_collection;
	$insert_arr['GRD_GSTIN_UPDATE_STATUS'] = $customer_gstin_status;
	$insert_arr['GRD_EXPECTED_TO_UPDATE_GSTIN'] = $gstin_update_date;
	$grd_receipt_id = array_insert_query("gft_receipt_dtl", $insert_arr);
	$dev_proforma_check_arr = array();
	if($collection_against=='2') {
	    $dev_proforma_check_arr[$quotation_collection] = $receipt_amount;
	}
	if($grd_receipt_id!=0){
		if($receipt_entry_for==2){
			$sql_bal_amt	=	" SELECT o.GOD_ORDER_NO,o.GOD_EMP_ID, o.GOD_INCHARGE_EMP_ID, o.GOD_ORDER_DATE, o.GOD_ORDER_AMT, ".
					" o.GOD_ORDER_STATUS, GOD_COLLECTION_REALIZED, GOD_TAX_MODE, GOD_BALANCE_AMT, god_order_type, ".
					" GOD_DISCOUNT_ADJ_AMT, god_invoice_status FROM gft_order_hdr o ".
					" join gft_emp_master em on (em.gem_emp_id=o.god_emp_id) ".
					" join gft_emp_master iem on (iem.gem_emp_id=o.GOD_INCHARGE_EMP_ID) ".
					" where GOD_ORDER_STATUS='A' and GOD_ORDER_NO='$outstanding_order_no' AND GOD_BALANCE_AMT>0 ".
					" order by o.GOD_ORDER_DATE";
			$res_bal_amt	=	execute_my_query($sql_bal_amt);
			if(mysqli_num_rows($res_bal_amt)>0){
				while($row_bal=mysqli_fetch_array($res_bal_amt)){
					$order_no_collection	=	$row_bal['GOD_ORDER_NO'];
					$bal_amount				=	$row_bal['GOD_BALANCE_AMT'];
					if($bal_amount>0 and $receipt_amount>0){
					    $dev_pending_amt = $receipt_amount;
						if($receipt_amount<$bal_amount){
							$query_ins2="insert into gft_collection_receipt_dtl  (GCR_ORDER_NO, GCR_RECEIPT_ID, GCR_PAYMENT_FORID ,GCR_AMOUNT ,GCR_REASON) values" .
									" ('$order_no_collection','$grd_receipt_id','a','".$receipt_amount."','1') ";
							 
							$result_ins2=execute_my_query($query_ins2,'receipt_util.php',true,true);
							update_collection_in_hdr($order_no_collection,true);
							$receipt_amount	=	0;
						}else{
							$query_ins2="insert into gft_collection_receipt_dtl  (GCR_ORDER_NO, GCR_RECEIPT_ID, GCR_PAYMENT_FORID ,GCR_AMOUNT ,GCR_REASON) values" .
									" ('$order_no_collection','$grd_receipt_id','a','".$bal_amount."','1') ";
							 
							$result_ins2=execute_my_query($query_ins2,'receipt_util.php',true,true);
							update_collection_in_hdr($order_no_collection,true);
							$receipt_amount	=	($receipt_amount-$bal_amount);
						}
						$order_proforma_no = get_single_value_from_single_table("gph_order_no", "gft_proforma_hdr", "gph_converted_order_no", $order_no_collection);
						if($order_proforma_no!='') {
						    $dev_proforma_check_arr[$order_proforma_no] = $dev_pending_amt;
						}
					}
				}
			}
		}
	
	}
	if($receipt_amount!='' and $receipt_amount>0){
		$que1=" insert into gft_collection_receipt_dtl " .
				" (GCR_ORDER_NO, GCR_RECEIPT_ID, GCR_PAYMENT_FORID, GCR_AMOUNT ,GCR_REASON, GCR_REMARKS) " .
				" values ('$lead_code', '$grd_receipt_id', 'a', '$receipt_amount','-1','$receipt_remarks')";
		$res1 = execute_my_query($que1);
		if($res1) {
			$new_date = date('Y-m-d',mktime(null,null,null,date('m'),date('d')+5,date('y')));
			$call_res = execute_my_query("select GID_LEAD_CODE from gft_install_dtl_new where GID_LEAD_CODE='$lead_code' and GID_STATUS='A' and GID_VALIDITY_DATE<='$new_date'");
			if(mysqli_num_rows($call_res) > 0){
				//$call_upd = " update gft_lead_hdr set GLH_MAIN_PRODUCT_UVALIDITITY='$new_date', GLH_MAIN_PRODUCT_UPDATE_BY='$emp_id', ".
				//			" GLH_MAIN_PRODUCT_UPDATE_ON=now() where GLH_LEAD_CODE='$lead_code' ";
				//execute_my_query($call_upd);
				$validity_upd = " update gft_install_dtl_new set GID_TRIAL_TILL_DATE='$new_date' where GID_LEAD_CODE='$lead_code' and GID_VALIDITY_DATE <= '$new_date' ";
				execute_my_query($validity_upd);
			}
		}
	}
	/* Incetive for Outstanding */
	if($insert_arr['GRD_RECEIPT_AMT']!='' && $outstanding_order_no != ''){
	    outstanding_incentive($outstanding_order_no,$emp_id,$grd_receipt_id);
	}//end of oustanding
	update_cust_bal($lead_code);
	send_collection_receipt($grd_receipt_id,$uid);
    foreach ($dev_proforma_check_arr as $pi=>$amt) {
        update_collection_pending_ticket($pi,'19',$amt);
    }
	return (int)$grd_receipt_id;
}
/**
 * 
 * @return string
 */
function get_collection_realisation_message(){
	$submit_status	=	"";
	$alt_duration	=	"";
	$hr=date('H');$clr_flag=0;$alt_msg='';$alt_beg='08.00';$i=12;
	$coll_const= get_samee_const('Payment_Realization_Timing');
	$alt_end=substr($coll_const,0,5);
	$clear_tmg_ary=explode(',',$coll_const);
	foreach($clear_tmg_ary as $value){
		$timing_const=explode('-',$value);
		if(strcmp($hr,substr($timing_const[0],0,2))<0){
			$alt_duration=$alt_beg.' - '.$alt_end;
			$alt_msg=$value;
			$clr_flag=1;
			break;
		}else{
			$alt_beg=substr($coll_const,$i-12,5);
			$alt_end=substr($coll_const,$i,5);
			$i=$i+12;
		}
	}
	if($alt_end==''){
		$alt_end=substr($coll_const,-11,5);
	}
	if($clr_flag!=0 && date('N')!='7'){
		$submit_status=' Amount credited in bank between '.$alt_duration.' Hrs realisation will be done in SAM between '.$alt_msg.' Hrs';
	}else {
		$submit_status=' Amount credited in bank after '.$alt_end.' Hrs realisation will be done in SAM between '.$clear_tmg_ary[0].' Hrs';
	}
	return $submit_status;
}

/**
 * @return string
 */
function get_next_credit_note_no(){
	$financial_year_start = ((int)date('m') < 4) ? date('Y-04-01',strtotime('-1 year')) : date('Y-04-01');
	$yr = date('y', strtotime($financial_year_start));
	$cr_ref = "CR".(string)substr("00".$yr,-2).substr("00".($yr+1),-2);
	$sel_query =" select max(GRD_CHEQUE_DD_NO) as max_id from gft_receipt_dtl  where GRD_RECEIPT_TYPE=8 and ".
			" GRD_DATE >= '$financial_year_start' and GRD_CHEQUE_DD_NO like '$cr_ref%' having max_id is not null ";
	$res_query = execute_my_query($sel_query);
	if(mysqli_num_rows($res_query)==0){
		$next_no = $cr_ref."0001";
	}else {
		$row1 = mysqli_fetch_array($res_query);
		$max_id = (int)substr($row1['max_id'],-4);
		$max_id = $max_id + 1;
		$next_no = $cr_ref.(string)substr("0000$max_id", -4);
	}
	return $next_no;
}
/**
 *
 * @param int $cp_emp_id
 * @param string $receipt_amount
 * @param string $rtype
 * @param string $cash_cheque_dd_status
 * @param string $hand_over_to_emp
 * @param string $cheque_dd_no
 * @param string $cheque_dd_date
 * @param string $deposited_date
 * @param string $receipt_remarks
 * @param string $account
 *
 * @return void
 */

function send_collection_entry_mail($cp_emp_id,$receipt_amount,$rtype,$cash_cheque_dd_status,$hand_over_to_emp,$cheque_dd_no,$cheque_dd_date,$deposited_date,$receipt_remarks,$account='2'){
	$db_sms_content_config	=	array();
	$receipt_type			=	get_single_value_from_single_table("GRT_TYPE_NAME", "gft_receipt_type_master", "GRT_TYPE_CODE", $rtype);
	$cheque_status			=	get_single_value_from_single_table("GCS_STATUS", "gft_cheque_status_master", "GCS_STATUS_ABR", $cash_cheque_dd_status);
	$mail_subject = "Partner Payment Details - $receipt_type";
	if($account=='3'){
		$mail_subject = "Dealer Payment Details - $receipt_type";
	}
	$cp_details=get_emp_master((string)$cp_emp_id,'A',null,false);
	$cp_name=$cp_details[0][1];
	$db_sms_content_config['dateon']=array(date('Y-m-d'));
	$db_sms_content_config['CP_NAME']=array($cp_name);
	$db_sms_content_config['AMOUNT']=array($receipt_amount);
	$db_sms_content_config['RECEIPT_TYPE']=array($receipt_type);
	$db_sms_content_config['message']=array($mail_subject);
	$db_sms_content_config['COMMENTS']=array($receipt_remarks);
	$handoverto_emp_name='';
	if((int)$hand_over_to_emp!=0){
		$handoverto_emp_dtl=get_emp_master($hand_over_to_emp,'A',null,true);
		$handoverto_emp_name=$handoverto_emp_dtl[0][1];
	}
	if($cheque_dd_no!=''){
		$db_sms_content_config['EXTRAS1']=array('Cheque / DD Number : '.$cheque_dd_no .'<br>Cheque / DD  Date '.$cheque_dd_date) ;
	}else {
		$db_sms_content_config['EXTRAS1']=' ';
	}
	if(isset($cheque_status) and $cheque_status!='' and (int)$rtype!=7){
		$deposited_date_str='';
		if($deposited_date!=''){
			$deposited_date_str="<br>Deposited Date : ".$deposited_date;
		}
		$db_sms_content_config['EXTRAS2']=array('Status :'.$cheque_status .' '.$handoverto_emp_name.' '.$deposited_date_str);
	}else {
		$db_sms_content_config['EXTRAS2']=' ';
	}
	send_formatted_mail_content($db_sms_content_config,18,41,null,null);
}

/**
 * @param string $lcode
 * @param string $order_str
 *
 * @return string
 */
function partner_accounts_update($lcode,  $order_str){
	global $uid;
	if($order_str==""){
		return 'Order Number is Mandatory';
	}
	$cp_id = get_cp_emp_id_for_leadcode($lcode);
	$balance_order_no="000".substr("0000".$cp_id,-4).substr("00000000".$lcode,-8);
	$sel_quer = " select GOD_ORDER_NO, GOD_ORDER_AMT, GOD_BALANCE_AMT   ".
			" from gft_order_hdr where GOD_LEAD_CODE='$lcode' and GOD_BALANCE_AMT!=0 and GOD_ORDER_NO in ($order_str) ";
	$sel_quer .= " group by GOD_ORDER_NO ";
	$res = execute_my_query($sel_quer);
	while($row1 = mysqli_fetch_array($res)){
		$GOD_ORDER_NO = $row1['GOD_ORDER_NO'];
		$order_amt = (float)$row1['GOD_ORDER_AMT'];
		$balance_amt = (float)$row1['GOD_BALANCE_AMT'];
		if($balance_amt==0){
			continue;
		}else if($balance_amt < 0){
			//update_parnter_order_collection_details($GOD_ORDER_NO,$order_amt,$balance_order_no);
		}else{
			$balquery	=	" SELECT GCR_RECEIPT_ID, GRD_RECEIPT_TYPE, GCR_PAYMENT_FORID, GCR_AMOUNT, " .
					" GCR_REASON, GCR_OTHERS_LISTED_PRICE " .
					" FROM gft_collection_receipt_dtl join gft_receipt_dtl on (GCR_RECEIPT_ID=GRD_RECEIPT_ID)" .
					" where GCR_ORDER_NO='$balance_order_no' " .
					" and GRD_REFUND_AMT=0 and GRD_STATUS in ('P','C') and GRD_CHECKED_WITH_LEDGER='Y' order by GCR_RECEIPT_ID ";
			$result		=	execute_my_query($balquery,'',true,false);
			$rid		=	0;
			$totdepositamt = 0;
			$amount 	= 	/*. (int[int]) .*/ array();
			$receipt_id = $paymentfor = /*. (string[int]) .*/array();
			if(mysqli_num_rows($result)>0){
				while($data=mysqli_fetch_array($result)){
					$receipt_id[$rid]	=	$data['GCR_RECEIPT_ID'];
					$paymentfor[$rid]	=	$data['GCR_PAYMENT_FORID'];
					$amount[$rid]		=	(int)$data['GCR_AMOUNT'];
					$totdepositamt		=	$totdepositamt+$amount[$rid];
					$rid++;
				}
			}
			if($totdepositamt < round($balance_amt)){
				return "No balance to deduct"; //to avoid going negative balance
			}
			for($rid=0; ($rid<count($receipt_id) and $balance_amt>0);$rid++){
				$colrecamt=0;
				if($balance_amt>=$amount[$rid] and $amount[$rid]>0){
					$colrecamt=$amount[$rid];
					$balance_amt=$balance_amt-$amount[$rid];
					$amount[$rid]=0;
					$balquery=" delete from gft_collection_receipt_dtl where GCR_ORDER_NO='$balance_order_no' AND GCR_RECEIPT_ID='$receipt_id[$rid]' ";
				}else if($balance_amt < $amount[$rid]){
					$colrecamt=$balance_amt;
					$amount[$rid]=$amount[$rid]-$balance_amt;
					$balance_amt=0;
					$balquery=" update gft_collection_receipt_dtl set GCR_AMOUNT=$amount[$rid] where " .
					"GCR_ORDER_NO='$balance_order_no' AND  GCR_RECEIPT_ID='$receipt_id[$rid]' ";
				}else{
					$balquery='';
				}
				if($colrecamt>0){
					$receiptqury=" insert into gft_collection_receipt_dtl ( GCR_ORDER_NO, GCR_RECEIPT_ID, " .
							" GCR_PAYMENT_FORID, GCR_AMOUNT, GCR_REASON ,GCR_REALIZED_BY, GCR_REALIZED_ON, GCR_REMARKS)" .
							" values ('$GOD_ORDER_NO','$receipt_id[$rid]', '$paymentfor[$rid]'," .
							" '$colrecamt', '1','$uid',now(),'Partner Management Team realized collection manually using external script') ";
					execute_my_query($receiptqury);
					execute_my_query($balquery);
				}
			}
		}
		update_collection_in_hdr($GOD_ORDER_NO);
	}
	return "Partner Accounts Updated Successfully for these Order Number - $order_str";
}
?>
