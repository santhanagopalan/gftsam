<?php
require_once(__DIR__."/../dbcon.php");
require_once(__DIR__."/../function.update_in_tables.php");
require_once(__DIR__."/../function.insert_stmt_for_activity.php");
require_once(__DIR__."/../function.insert_stmt_order.php");
require_once(__DIR__."/../function.update_in_hdr.php");
require_once(__DIR__."/../visit_submit_in_popup.php");

/*.
 forward class licenseUpdate{
	protected void function update_periodic_check_log(string $check_status=);
}
.*/

class licenseUpdate {
	
	public $LOCAL_USER='N';
	protected /*.int .*/$WEB_REQUEST_TYPE=0;
	protected /*.int .*/$ONLINE_REQUEST_ID=0;
	protected /*. string .*/$OTP;
	protected /*. string .*/$GLH_CUST_NAME;
	protected /*. int .*/ $LOCAL_USER_ID=0;
	protected /*. string .*/ $LOCAL_USER_NAME;
	protected /*. string[string] .*/$parsed_xml;
	protected /*. int .*/ $GID_INSTALL_ID=0;
	protected /*. int .*/ $LEAD_CODE=0 ; 
	public  /*. int .*/ $GID_LEAD_CODE=0 ;
	protected /*. string .*/ $GID_ORDER_NO ;
	protected /*. int .*/ $GID_LIC_PCODE=0 ;
	protected /*. string .*/ $GID_LIC_PSKEW ;
	protected /*. string .*/ $GID_STATUS;
	protected /*. int .*/ $GID_SALESEXE_ID=0 ;
	protected /*. int .*/ $GID_FULLFILLMENT_NO=0 ;
	protected /*. string .*/ $VERSION ;
	protected /*. int .*/ $GID_NO_CLIENTS=0 ;
	protected /*. int .*/ $GID_NO_COMPANYS=0;
	protected /*. string .*/ $GID_VALIDITY_DATE ;
	protected /*. int .*/ $GID_EXPIRE_FOR=0 ;
	protected /*. string .*/ $GID_LIC_HARD_DISK_ID ;
	protected /*. string .*/ $GPM_TRAINING_REQUIRED;
	protected /*. int .*/ $HEAD_OF_FAMILY=0 ;
	protected /*. int .*/ $GID_NCLIENTS_UPDATED_IN_CUSTPLACE=0 ;
	protected /*. int .*/ $GID_NCOMPANYS_UPDATED_IN_CUSTPLACE=0 ;
	protected /*. string .*/$ENCRYPED_HDD_KEY;
	protected /*. string .*/ $GID_SUBSCRIPTION_STATUS;
	protected /*. string .*/ $GID_UPGRADATION_DONE;
	protected /*. int .*/ $GAR_ID=0 ;
	protected /*. int .*/ $GLS_ID=0 ;
	protected /*. int .*/ $GAR_REASON=0;
	protected /*. string .*/ $GAR_APPROVED_BY;
	protected /*. string .*/ $GAR_APPROVED_FLAG;
	protected /*. string .*/ $GAR_NOTE;
	protected /*. string .*/ $GAR_APPROVAL_COMMENT;
	protected /*. int .*/ $GRD_REINSTALL_ID=0;
	
	protected /*. int .*/ $RREASON_CODE=0;
	protected /*. string .*/ $RREASON_DESC ;
	protected /*. string .*/ $RREASON_CODE_DESC;
	protected /*. string .*/ $PROFORMA_ORDER_NO ; 
	protected /*. string .*/ $PROFORMA_GENERATED_BY;
	protected /*. string .*/ $PROFORMA_DATE;
	protected /*. string .*/ $GID_CURRENT_VERSION ;
	
	public /*. int .*/ $FULLFILLMENT_NO=0;
	public /*. string .*/ $ORDER_NO;
	public /*. string .*/ $GOD_EMP_ID=SALES_DUMMY_ID;
	protected /*. string .*/ $ORDER_NO_SPLITED ;
	protected /*. string .*/ $GOD_ORDERED_BY ;
	protected /*. int  .*/ $ORDERED_EMP_ID=0;
	protected /*. string .*/ $GOD_ORDER_DATE ;
	protected /*. string .*/ $GOD_IMPL_REQUIRED;
	protected /*. string .*/ $PRODUCT_NAME ;
	protected /*. string .*/ $PRODUCT_TYPE;
	protected /*. string .*/ $EDITION_TYPE_NAME;
	protected /*. string .*/ $SKEW_DESC ;
	protected /*. string .*/ $GID_INSTALL_DATE ;
	protected /*. string .*/ $PRODUCT_KEY;
	protected /*. string .*/ $PRODUCT_KEY_SPLITED;
	protected /*. string .*/ $ACTIVATION_KEY;
	protected /*. int .*/ $Vertical=0;
	protected /*. string .*/ $Vertical_Name;
	protected /*. string .*/ $Customer_Name;
	protected /*. int .*/ $GPM_CATEGORY=0;
	protected /*. int .*/ $InstallationType=0;
	protected /*. string .*/ $InstallationType_Desc ;
	public  /*. int .*/$PCODE=0;
    public /*. string .*/$PSKEW;
    public  /*. string .*/$SKEW_ADDON;
    protected /*. string .*/ $PRODUCT_GROUP;
    protected /*. string .*/ $TRIAL_SKEW;
    protected /*. string .*/ $GID_SYS_HDD_ID;
	protected /*. string .*/ $PRODUCT_ID;
	protected /*. int .*/ $GPM_LICENSE_TYPE=0;
	protected /*. string .*/ $LICENSE_TYPE_NAME;
	protected /*. int .*/ $PENDING_APPROVAL=0;
	protected /*. string .*/ $LICENSE_STATUS='0';
	protected /*. int .*/ $ORDER_SPLIT=0;
	protected /*. string .*/$annuity_valid='';
	protected /*. string .*/$GID_TRIAL_TILL_DATE='';
	protected /*. int .*/ $LIC_COUNT=0;
	protected /*. string[int] .*/$USED_PCODE;  //for addon
	protected /*. string[int] .*/$USED_PSKEW;  //for addon
	protected /*. string .*/$LEAD_TYPE;
	protected /*. string[int] .*/$CUSTOM_LIC;
	protected /*. int .*/$ORDER_APPROVAL_STATUS=0;
	protected /*. string .*/$INSTALLED_NO='';
	protected /*. string .*/$INSTALLED_BY='';
	protected /*. string .*/$INSTALLED_NAME='';
	protected /*. string .*/$LIC_NOTE='';
	protected /*. string .*/$ASSIGNED_EMP='';
	protected /*. string .*/$ASSIGNED_EMP_NO='';
	protected /*. string .*/$complaint_link='';
	protected /*. string .*/$CUSTOM_TYPE='';
	protected /*. string .*/$REFERENCE_ORDER='';
	protected /*. string .*/$REFERENCE_FULLFILLMENT='';
	protected /*. string .*/$CUSTOM_GROUP='';
	protected /*. string .*/$GID_PRODUCT_CODE='';
	protected /*. string[int] .*/$CUSTOM_SKEWS;
	protected /*. string[int] .*/$CUSTOM_STATUS;
	protected /*. string .*/$UAT='';
	protected /*. string .*/$POS_UAT='';
	protected /*. string .*/$UAT_VALIDITY='';
	protected /*. int .*/$GID_REF_SERIAL_NO=1;
	protected /*. int .*/$send_mail = 1;   // 1 - will send mail   0 - wont send mail
	protected /* string */$GLH_COUNTRY='';
	protected /* string */$GLH_STATECODE='';
	protected /* string */$GLH_CREATED_CATEGORY='';
	protected /*. string .*/ $GLC_ERROR_CODE='';
	protected /*. string .*/ $GLC_ADDITIONAL_ERROR_MESSAGE;
	protected /*. string .*/ $sys_assessment='N';
	protected /*. string .*/ $DB_PASSWORD = "";
	protected /*. string .*/ $HKEY = "";
	protected /*. string .*/ $GID_MACHINE_NAME = "";
	protected /*. string .*/ $GID_SYS_HKEY = "";
	protected /*. boolean .*/ $IS_DFT_ORDER_NO_FOR_POS = false;
	protected /*. boolean .*/ $IS_DEALER = false;
	protected /*. string .*/ $REG_TYPE = "";
	protected /*. string .*/ $DEALER_ID = "";
	protected /*. boolean .*/$IS_OFFLINE = false;
	protected /*. string .*/ $KIT_BASED = "N";
	protected /*. string .*/ $CP_TOKEN = "";
	protected /*. string .*/ $NS_TOKEN = "";
	protected /*. string .*/ $DATA_SECURITY_PASSWORD = "";
	
	
/**
 * @param string $orderno
 * @param int $productcode
 * @param int $lead_code
 * 
 * @return void
 */
protected function update_client_used_qty_order_product_dtl($orderno,$productcode,$lead_code){ 
	$query_ins_client="SELECT GID_ORDER_NO, GID_PRODUCT_CODE, GID_PRODUCT_SKEW, GID_FULLFILLMENT_NO, GID_NO_CLIENTS, GID_NO_COMPANYS, " .
			" GID_LIC_ORDER_NO, GID_LIC_PCODE, GID_LIC_PSKEW, GPM_CLIENTS, GPM_COMPANYS,GFT_SKEW_PROPERTY,GID_PREV_EXPIRY_DATE, GID_VALIDITY_DATE " .
			" FROM gft_install_dtl_new join gft_product_master on(GID_PRODUCT_CODE=GPM_PRODUCT_CODE and GID_PRODUCT_SKEW=GPM_PRODUCT_SKEW)" .
			" WHERE GID_ORDER_NO='$orderno' AND GID_PRODUCT_CODE=$productcode AND GID_LEAD_CODE=$lead_code ";
			
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
	 		$installed_current_skew_property=(int)$data_ins_client['GFT_SKEW_PROPERTY'];
	 		$prev_expiry_date=$data_ins_client['GID_PREV_EXPIRY_DATE'];
			$cur_expiry_date=$data_ins_client['GID_VALIDITY_DATE'];
			if($installed_current_skew_property==1){
				$add_client_add_company_skew='3,14';
			}else{
				$add_client_add_company_skew='16,13';
			}
	 		$client_skew_property=($installed_current_skew_property==1?3:($installed_current_skew_property==11?16:''));
	 		$company_skew_property=($installed_current_skew_property==1?14:($installed_current_skew_property==11?13:''));
	 		$no_client_query=" SELECT GOP_ORDER_NO,GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GOP_QTY, GOP_USEDQTY, god_order_splict,GFT_SKEW_PROPERTY " .
	 				"from ( (SELECT GOP_ORDER_NO,GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GOP_QTY, GOP_USEDQTY, gh.god_order_splict,GFT_SKEW_PROPERTY " .
	 				" FROM gft_order_product_dtl g " .
	 				" join gft_order_hdr gh on (g.GOP_ORDER_NO=gh.GOD_ORDER_NO AND gh.GOD_ORDER_STATUS='A' and god_order_splict=0 )" .
	 				" join gft_product_master on(GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW)" .
	 				" where GOP_PRODUCT_CODE =$productcode and GOD_LEAD_CODE=$lead_code  " .
	 				" and GFT_SKEW_PROPERTY in ($add_client_add_company_skew) " .
	 				" )union all (" .
	 				" select GCO_ORDER_NO, GCO_PRODUCT_CODE,GCO_SKEW,GCO_CUST_QTY, GCO_USEDQTY,gh.god_order_splict,GFT_SKEW_PROPERTY " .
	 				" from gft_order_product_dtl g " .
	 				" join gft_order_hdr gh on (g.GOP_ORDER_NO=gh.GOD_ORDER_NO AND gh.GOD_ORDER_STATUS='A' and god_order_splict=1 )" .
	 				" join gft_product_master on(GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW)" .
	 				" join gft_cp_order_dtl on (GOP_ORDER_NO=GCO_ORDER_NO and GOP_PRODUCT_CODE=GCO_PRODUCT_CODE and GOP_PRODUCT_SKEW=GCO_SKEW ) " .
	 				" where GOP_PRODUCT_CODE =$productcode and GCO_CUST_CODE=$lead_code " .
	 				" and GFT_SKEW_PROPERTY in ($add_client_add_company_skew) " .
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
	 			if($order_split==='1'){
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
 * @param string $orderno
 * @param int $productcode
 * @param string $productskew
 * @param int $lead_code
 * @param string $subscription_skew
 * 
 * @return void
 */
private function update_gft_order_product_dtl($orderno,$productcode,$productskew,$lead_code,$subscription_skew=null){ 
	
	$query="update gft_order_product_dtl join gft_product_master pm on (pm.gpm_product_code=gop_product_code and pm.gpm_product_skew=gop_product_skew) ".
		   " set gop_usedqty=if(pm.GPM_ORDER_TYPE=1,$this->GID_NO_CLIENTS,gop_usedqty+1) where gop_order_no='$orderno '" .
		   " and  gop_product_code='$productcode' and gop_usedqty < gop_qty " .
		   " and (gop_product_skew='$productskew' ".($subscription_skew!==''?" or gop_product_skew='$subscription_skew'":"").")";
	$result=execute_my_query($query,'',true,false);
	$query2=" update gft_cp_order_dtl t1,gft_order_hdr lh,gft_product_master pm ".
			" set gco_usedqty=if(pm.GPM_ORDER_TYPE=1,$this->GID_NO_CLIENTS,gco_usedqty+1) " .
      		" where GCO_ORDER_NO ='$orderno' and pm.GPM_PRODUCT_CODE=GCO_PRODUCT_CODE and pm.GPM_PRODUCT_SKEW=GCO_SKEW and GCO_PRODUCT_CODE='$productcode' " .
      		" and (GCO_SKEW='$productskew'".($subscription_skew!==null?" or GCO_SKEW='$subscription_skew'":"").") " .
      		" and GCO_CUST_CODE=$lead_code and gco_cust_qty>gco_usedqty ". 
      		" and gco_order_no=god_order_no and god_order_splict=1  ";
	execute_my_query($query2,'',true,false);
	$this->update_client_used_qty_order_product_dtl($orderno,$productcode,$lead_code);
}


/**
 * @param int $lead_code
 * @param string $orderno
 * @param int $productcode
 * @param int $head_of_family
 * @param int $fullfillment_no
 * @param string $productskew
 * @param int $install_id
 * 
 * @return void
 */
protected function update_advance_asa_order($lead_code,$orderno,$productcode,$head_of_family,$fullfillment_no,$productskew,$install_id){
	$query= "SELECT GOD_ORDER_DATE,GOD_EMP_ID,op.GOP_ORDER_NO, op.GOP_PRODUCT_CODE, op.GOP_PRODUCT_SKEW, pm.GPM_DEFAULT_ASS_PERIOD,GFT_SKEW_PROPERTY,GOP_SELL_AMT " .
			" FROM gft_order_product_dtl op " .
			" join gft_product_master pm on (pm.gpm_product_code=op.GOP_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW and GFT_SKEW_PROPERTY in (4,15))" .
			" join gft_order_hdr oh on (god_order_no=op.GOP_ORDER_NO)" .
			" WHERE op.GOP_ORDER_NO='$orderno' AND op.GOP_PRODUCT_CODE=$productcode and GPM_REFERER_SKEW='$productskew' ";
	$result=execute_my_query($query);
	if($data=mysqli_fetch_array($result)){
		$GOD_ORDER_NO=$data['GOP_ORDER_NO'];
		$ass_product_skew=$data['GOP_PRODUCT_SKEW'];
		$GOP_SELL_AMT=$data['GOP_SELL_AMT'];
		$ASS_START_DATE=date('Y-m-d');
		$ASS_END_DATE=date('Y-m-d',mktime("0","0","0",date('m'),((int)date('d')+(int)$data['GPM_DEFAULT_ASS_PERIOD']),date('Y')));
		$asa_type=((int)$data['GFT_SKEW_PROPERTY']==4?1:3);
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
		$this->update_gft_order_product_dtl($orderno,$productcode,$ass_product_skew,$lead_code);
	}
	
}


/**
  * @return void
  */

protected function  entry_gft_client_install_dtl(){
	
	$table_name='gft_clientinstall_dtl';
	$install_dtl=/*. (mixed[string]) .*/array();
	$install_dtl['GCD_LEAD_CODE']=$this->GID_LEAD_CODE;
	$install_dtl['GCD_ORDER_NO']=$this->GID_ORDER_NO;
	$install_dtl['GCD_PRODUCT_CODE']=$this->GID_LIC_PCODE;
	$install_dtl['GCD_EMP_ID']=SALES_DUMMY_ID;
	$install_dtl['GCD_PRODUCT_SKEW']=$this->GID_LIC_PSKEW;
	$install_dtl['GCD_PRODUCT_VERSION']=$this->VERSION;
	$install_dtl['GCD_LIC_GEN_TYPE']=1;
	$install_dtl['GCD_CINSTALL_DATE']=date('Y-m-d');
	$install_dtl['GCD_STATUS']='T1';	
	$install_dtl['GCD_INSTALL_REFF']=$this->GID_INSTALL_ID;	
	$install_dtl['GCD_QTY']=($this->GID_NO_CLIENTS -$this->GID_NCLIENTS_UPDATED_IN_CUSTPLACE );	     	
	
		
	array_update_tables_common($install_dtl,$table_name,null,null,$install_dtl['GCD_EMP_ID'],$remarks=null,$table_column_iff_update=null,$install_dtl);
	
}
	

	/**
	 * @return void
	 */
	protected function update_hkey_log() {
		if( ($this->GID_INSTALL_ID!=0) && ($this->HKEY!='') ){
			$insert_dtl['GHL_INSTALL_ID']		=$this->GID_INSTALL_ID;
			$insert_dtl['GHL_HKEY']			 	=$this->HKEY;
			$insert_dtl['GHL_MACHINE_NAME']	 	=$this->GID_MACHINE_NAME;
			$insert_dtl['GHL_CURRENT_STATUS']	='A';
			$insert_dtl['GHL_CREATED_TIME']	 	= date('Y-m-d H:i:s');
			array_update_tables_common($insert_dtl, "gft_hkey_log", null, null, SALES_DUMMY_ID,null,null,$insert_dtl);
		}
	}
	
	/**
	 * @return void
	 */
	protected function entry_gft_upgradation_dtl(){
	
		$table_name='gft_upgradation_dtl';
	   	$install_dtl=/*. (mixed[string]) .*/ array();
	   	$install_dtl['GUD_LEAD_CODE']=$this->GID_LEAD_CODE;
		$install_dtl['GUD_ORDER_NO']=$this->GID_ORDER_NO;
		$install_dtl['GUD_PRODUCT_CODE']=$this->GID_LIC_PCODE;
		$install_dtl['GUD_LIC_FULLFILLMENT_NO']=$this->GID_FULLFILLMENT_NO;
		$install_dtl['GUD_EMP_ID']=SALES_DUMMY_ID;
		$install_dtl['GUD_CURRENT_VERSION']=$this->GID_CURRENT_VERSION;
		$install_dtl['GUD_PRODUCT_SKEW']=$this->GID_LIC_PSKEW;
		$install_dtl['GUD_UPDATED_VERSION']=$this->VERSION;
		$install_dtl['GUD_LIC_GEN_TYPE']=1;
		$install_dtl['GUD_UPGRADATION_DATE']=date('Y-m-d');
		$install_dtl['GUD_STATUS']='T1';
		$install_dtl['GUD_INSTALL_REF']=$this->GID_INSTALL_ID;
		
		array_update_tables_common($install_dtl,$table_name,null,null,SALES_DUMMY_ID,$remarks=null,$table_column_iff_update=null,$install_dtl);	
		
		support_activity_mv_status($current_status=array('T8'),$this->GID_LEAD_CODE,SALES_DUMMY_ID,date('Y-m-d'),$this->GID_LIC_PCODE,$change_staus='T1');
		
	}
	
	/**
	 * @return void
	 */
	protected function entry_reinstall_dtl(){
	
	$lead_code=$this->GID_LEAD_CODE;
	$product_code=$this->GID_LIC_PCODE;
	$table_name='gft_reinstall_dtl';
	$install_dtl=/*. (mixed[string]) .*/array();
	$install_dtl['GRD_ORDER_NO']=$this->GID_ORDER_NO;
	$install_dtl['GRD_PRODUCT_CODE']=$this->GID_LIC_PCODE;
	$install_dtl['GRD_FULLFILLMENT_NO']=$this->GID_FULLFILLMENT_NO;
	$install_dtl['GRD_EMP_ID']=SALES_DUMMY_ID;
	$install_dtl['GRD_PRODUCT_SKEW']=$this->GID_LIC_PSKEW;
	$install_dtl['GRD_PRODUCT_VERSION']=$this->VERSION;
	$install_dtl['GRD_LIC_GEN_TYPE']=1;
	$install_dtl['GRD_REINSTALL_DATE']=date('Y-m-d');
	$install_dtl['GRD_REINSTALL_REASONCODE']=$this->RREASON_CODE;
	$install_dtl['GRD_REASON_NOTE']=$this->RREASON_DESC;
	$install_dtl['GRD_STATUS']='T1';
	$install_dtl['GRD_PROFORMA_ORDER_NO']=(!empty($this->PROFORMA_ORDER_NO)?$this->PROFORMA_ORDER_NO:'');
	$install_dtl['GRD_INSTALL_REFF']=$this->GID_INSTALL_ID;
	
	array_update_tables_common($install_dtl,$table_name,null,null,SALES_DUMMY_ID,null,null,$install_dtl);	
	$this->GRD_REINSTALL_ID=mysqli_insert_id_wrapper(); 
	
		
    //removed auto generation of reinstallation order on (Mar 22, 2016) as Reinstallation is Automated after delight
					
	/* update dublicate of requested earlier
	$query_update="update gft_reinstall_dtl,gft_install_dtl_new set GRD_DUBLICATE_OF='".$this->GRD_REINSTALL_ID."' " .
				" WHERE GID_INSTALL_ID=GRD_INSTALL_REFF and grd_reinstall_id!='".$this->GRD_REINSTALL_ID."' " .
				" and grd_reinstall_date=date(now()) ";
	execute_my_query($query_update);*/
	
	if($this->WEB_REQUEST_TYPE=='27'){
		$insert_dtl=/*. (string[string]) .*/array();
		execute_my_query("update gft_hkey_log set GHL_CURRENT_STATUS='I' where GHL_INSTALL_ID='$this->GID_INSTALL_ID'");
		$this->update_hkey_log();
		$name_que="select GPM_PRODUCT_NAME from gft_product_family_master where GPM_PRODUCT_CODE=SUBSTR('$this->PRODUCT_ID',1,3)";
		$name_res=execute_my_query($name_que);
		if($data=mysqli_fetch_array($name_res)){
			$this->PRODUCT_NAME=$data['GPM_PRODUCT_NAME'];
		}
		$vertical = isset($this->parsed_xml['VERTICAL'])?(int)$this->parsed_xml['VERTICAL']:0;
		$pgroup   = $this->GID_LIC_PCODE."-".substr($this->GID_LIC_PSKEW, 0,4);
		$brand_dtl= get_product_brand_name($pgroup,$vertical);
		$brand_name=isset($brand_dtl[1])?$brand_dtl[1]:"";
		$sms_mail_content =array(
				'Order_No'=>array($this->ORDER_NO.substr('0000'.$this->FULLFILLMENT_NO, -4)),
				'Customer_Id'=>array($this->LEAD_CODE),
				'Customer_Name'=>array($this->GLH_CUST_NAME),
		        'Product_Name'=>array(($brand_name!="") ? $brand_name : $this->PRODUCT_NAME),
				'Date'=>array(date('Y-m-d')),
				'Time'=>array(date('H:i:s'))
		);
		$cust_cont = customerContactDetail($this->LEAD_CODE);
		$cust_email = explode(',', $cust_cont['EMAIL']);
		send_formatted_mail_content($sms_mail_content, 86, 158, null,null,$cust_email);
		$to_mobile_arr = get_contact_dtl_for_designation($this->LEAD_CODE, 1, 1);
		$to_mobile = isset($to_mobile_arr[0])?$to_mobile_arr[0]:'';
		if($to_mobile!=''){
			$sms_content = htmlentities(get_formatted_content($sms_mail_content, 171));
			entry_sending_sms_to_customer($to_mobile,$sms_content,171,$this->LEAD_CODE,0,null,0,null,true);
		}
		
		$res1 = execute_my_query("select count(GHL_HKEY) as cnt from gft_hkey_log where GHL_INSTALL_ID='$this->GID_INSTALL_ID'");
		if($row1 = mysqli_fetch_array($res1)){
			if($row1['cnt']=='5'){
				send_formatted_mail_content($sms_mail_content, 86, 223);
			}
		}
		notify_pos_product($this->GID_LEAD_CODE, '', 'license_sync', '');
	}
	}	
	
	 
					    
	
	/**
	 * @return void
	 */
	protected function entry_renewal_dtl_new(){
		if(isset($license_dtl['LIC_CONNECTION_TYPE'])){
			$lic_connection_type=$license_dtl['LIC_CONNECTION_TYPE'];
		}else{
			$lic_connection_type=0;
		}
		$query_rw="insert ignore into gft_ass_dtl (GAD_ASS_DATE, GAD_ASS_START_DATE, GAD_ASS_END_DATE, GAD_EMP_ID, GAD_PRODUCT_CODE, GAD_ASS_ORDER_NO, GAD_INS_REFF, GAD_PRODUCT_SKEW, GAD_UPDATED_ON, GAD_LIC_GEN_TYPE)  " .
	        		"(SELECT GID_PREV_EXPIRY_DATE,GID_PREV_EXPIRY_DATE, GID_VALIDITY_DATE,".SALES_DUMMY_ID.", GID_LIC_PCODE,GID_ASS_ID,GID_INSTALL_ID,GID_LIC_PSKEW,now(),$lic_connection_type " .
					" from gft_install_dtl_new " .
					" where GID_INSTALL_ID=".$this->GID_INSTALL_ID." )";
		$result_rw=execute_my_query($query_rw,'',true,false);
	
	}
	
	/**
	 * @return void
	 */
	protected function entry_asa_update_dtl(){
		   	
		$query_ass_insert=" insert ignore into gft_ass_update_request_dtl (GAU_ORDER_NO, GAU_PRODUCT_CODE," .
				" GAU_PRODUCT_SKEW,GAU_ASS_UPDATE_ON, GAU_ASS_EXPIRED_DATE,GAU_LIC_GEN_TYPE,GAU_INSTALL_REFF) ".
				" (SELECT GID_ASS_ID,GID_LIC_PCODE,GID_LIC_PSKEW,now(),GID_VALIDITY_DATE," .
				" '1',GID_INSTALL_ID from  gft_install_dtl_new 
				where gid_install_id=".$this->GID_INSTALL_ID." ) ";
     	$result=execute_my_query($query_ass_insert,'',true,false);
	}
	/**
	 * @param int $expiry_type_id
	 * 
	 * @return string
	 */
	public function get_license_expiry_type($expiry_type_id){
		$query=" SELECT GET_TYPE_NAME FROM gft_expire_type_master where GET_TYPE_ID=$expiry_type_id ";
		$result=execute_my_query($query);
		if($qd=mysqli_fetch_array($result)){
			$GET_TYPE_NAME=$qd['GET_TYPE_NAME'];
			return $GET_TYPE_NAME;
		}
		return '';
	}
	
	/**
	 * @return void
	 */
	public function create_system_assessment_support_id(){
		//only for new installation support id will be created  //lead code for local user
		if( ($this->GID_INSTALL_ID!=0) || ($this->GID_LEAD_CODE==0) ){
			return;
		}
		$query_res = execute_my_query("select GPM_PRODUCT_CODE from gft_product_family_master where  GPM_PRODUCT_CODE='$this->GID_LIC_PCODE' and GPM_CATEGORY in (1,2,3) and GPM_IS_INTERNAL_PRODUCT=0");
		$num_rows = mysqli_num_rows($query_res);
		if($num_rows==0){  //only base product condition
			return;
		}
		$GCD_STATUS='T25';  //pending pc
		if(in_array($this->LEAD_TYPE,array('3','13'))){
			$GCD_STATUS='T86';  //pending pcs
		}
		$process_emp = '';
		$que_res = execute_my_query("select GLH_L1_INCHARGE, GLH_FIELD_INCHARGE from gft_lead_hdr where GLH_LEAD_CODE='$this->GID_LEAD_CODE'");
		if($row1 = mysqli_fetch_array($que_res)){
			$process_emp = $row1['GLH_FIELD_INCHARGE'];
			if( ($process_emp=='') || ($process_emp=='9999') ){
				$process_emp = $row1['GLH_L1_INCHARGE'];
			}
			if(is_authorized_group($process_emp, '', 30)){ //pcs team
				$GCD_STATUS='T86';  //pending pcs
			}
			if(!is_gft_employee($process_emp)){
				$GCD_STATUS='T51'; //pending partner
				$query_cp = " select CGI_EMP_ID from gft_leadcode_emp_map ".
						" join gft_cp_info on (GLEM_LEADCODE = CGI_LEAD_CODE) where GLEM_EMP_ID='$process_emp'";
				$res_cp = execute_my_query($query_cp);
				if($data_cp = mysqli_fetch_array($res_cp)) {
					$process_emp = $data_cp['CGI_EMP_ID'];
				}
			}
		}
		if( ($process_emp!='') && ($process_emp!='9999') ){
			$summary = "System Assessment Tool Run Pending";
			insert_support_entry($this->GID_LEAD_CODE, $this->GID_LIC_PCODE, $this->GID_LIC_PSKEW, $this->parsed_xml['VERSION'],
						$this->PRODUCT_TYPE,SALES_DUMMY_ID, $reason_visit='15', $summary, $GCH_COMPLAINT_CODE='152',
						$GCD_STATUS,null,null,$process_emp,null,null,$summary,true);
		}
	}

	/**
	 * @return void
	 */
	private function send_activation_mail_from_SAM(){
		$cc_mails = /*. (string[int]) .*/array();
	global $address_fields,$query_contact_dtl;
	$lead_query=" select GLH_LEAD_CODE,GLH_LEAD_TYPE,GLH_VERTICAL_CODE,GTM_VERTICAL_NAME $address_fields from gft_lead_hdr $query_contact_dtl " .
			" left join gft_vertical_master vm on (GTM_VERTICAL_CODE=GLH_VERTICAL_CODE)" .
			" where glh_lead_code=$this->GID_LEAD_CODE GROUP BY GLH_LEAD_CODE ";
	$result_lead_query=execute_my_query($lead_query);
	$qd_lead_query=mysqli_fetch_array($result_lead_query);
	$Vertical_Name=$qd_lead_query['GTM_VERTICAL_NAME'];
	$vertical = $qd_lead_query['GLH_VERTICAL_CODE'];
	if($qd_lead_query['GLH_LEAD_TYPE']=='7'){
		return;
	}
	$customer_info=get_necessary_data_from_query_for_tooltip($qd_lead_query,$license_mail=true);
		
		$name_que="select GPM_PRODUCT_NAME from gft_product_family_master where GPM_PRODUCT_CODE=SUBSTR('$this->PRODUCT_ID',1,3)";
		$name_res=execute_my_query($name_que);
		if($data=mysqli_fetch_array($name_res)){
			$this->PRODUCT_NAME=$data['GPM_PRODUCT_NAME'];
		}		
		$type_in_mail="Order Closed By";
		$by_in_mail=$this->GOD_ORDERED_BY;
		$executive=0;		
		if($this->WEB_REQUEST_TYPE==14){
			$type_in_mail="Installed by";
			$by_in_mail=($this->INSTALLED_NAME!='')?$this->INSTALLED_NAME:'Self';

			if($this->INSTALLED_BY!='' && $this->INSTALLED_BY!='self'){
				$executive = $this->INSTALLED_BY;
			}
			$key_name='PRESALES_TEAM_MAIL';
		}else{ 
			$key_name='ANNUITY_TEAM_MAIL_ID';
			$executive = $this->ORDERED_EMP_ID;
		}
		if($executive!=0) {
			$emp_details = get_emp_master($executive,'',null,false);
			array_push($cc_mails,$emp_details[0][4]);
			if( ((int)$emp_details[0][10]) || ((int)$emp_details[0][13]) || ((int)$emp_details[0][11])){  //channel partner || referal partner || cp employee 
				$ptr_emp = $emp_details[0][12];
				if($ptr_emp!='0') {
					$businee_mgr_mail = get_partner_business_mgr_mail_id($ptr_emp,true);
				}else{
					$businee_mgr_mail = get_partner_business_mgr_mail_id($executive);
				}
				array_push($cc_mails, $businee_mgr_mail);
				array_push($cc_mails, get_samee_const('PARTNER_MGMT_TEAM_MAIL'));
			}else{
			    //Below mail commented : To stop sending new installation mails to Kumar, we removed the entire hierarchy as it is not required
				//add_reporting_managers_in_cc($cc_mails,$executive);
			}
		}
		array_push($cc_mails, get_samee_const($key_name));
		$product_dtl = substr(($this->GID_LIC_PCODE."-".$this->GID_LIC_PSKEW), 0, 8);
		$brand_dtl = get_product_brand_name($product_dtl, $vertical);
		$brand_name = isset($brand_dtl[1])?$brand_dtl[1]:"";
		$dbcontent_config=/*. (string[string][int]) .*/array(
			"Installation_Type"=>array($this->InstallationType_Desc),
			"Order_No"=>array($this->ORDER_NO_SPLITED),
			"Installed_Type"=>array($type_in_mail),
			"Installed_By"=>array($by_in_mail),
			"Customer_Id"=>array((string)$this->GID_LEAD_CODE),
			"Order_Date"=>array($this->GOD_ORDER_DATE),
		    "ProductName"=>array($brand_name!=""?$brand_name:$this->PRODUCT_NAME),
			"EditionType"=>array($this->EDITION_TYPE_NAME),
			"Skew_Desc"=>array($this->SKEW_DESC),		
			"Install_Date"=>array($this->GID_INSTALL_DATE),
			"Version"=>array($this->VERSION),
			"Vertical"=>array($Vertical_Name),
			"No_Clients"=>array((string)$this->GID_NO_CLIENTS),
			"No_Companys"=>array((string)$this->GID_NO_COMPANYS),
			"Expiry_Type"=>array($this->get_license_expiry_type($this->GID_EXPIRE_FOR)),
			"Expiry_Date"=>array($this->GID_VALIDITY_DATE),
			"Current_Expiry_Date"=>array(($this->annuity_valid!='')?"Current ASA Expiry Date :".$this->annuity_valid:''),
			"LICENSE_TYPE_NAME"=>array($this->LICENSE_TYPE_NAME),
			"Product_Key"=>array($this->PRODUCT_KEY),
			"Activation_Key"=>array($this->ACTIVATION_KEY),
			"Reinstall_Reason"=>array((!empty($this->RREASON_CODE_DESC)?"Reinstall Reason :".$this->RREASON_CODE_DESC:'')),
			"Approved_By"=>array((!empty($this->GAR_APPROVED_BY)?"Approved By :".$this->GAR_APPROVED_BY:"")),
			"Generated_Time"=>array(date('Y-m-d H:i:s')),
			"Customer_Name"=>array($this->GLH_CUST_NAME),
			"Customer_Info"=>array($customer_info)
		);
		$mail_template_id='';
		if(!empty($this->ACTIVATION_KEY)){
			$mail_template_id=156;
		}else if(empty($this->ACTIVATION_KEY)){
			$mail_template_id=159;
		}
		send_formatted_mail_content($dbcontent_config,$category=9,$mail_template_id,$employee_ids=null,$customer_ids=array($this->GID_LEAD_CODE),null,null,$cc_mails);
	}
	/**
	 * @return void
	 * 
	 */
	
	protected function insert_in_gft_install_dtl_and_other(){
	
		$table_name='gft_install_dtl_new';
		$install_dtl=/*. (mixed[string]) .*/array();
		$install_dtl['GID_SALESEXE_ID']=$this->GID_SALESEXE_ID;
		$install_dtl['GID_ORDER_NO']=$install_dtl['GID_LIC_ORDER_NO']=$this->GID_ORDER_NO;
		$install_dtl['GID_PRODUCT_CODE']=$install_dtl['GID_LIC_PCODE']=$this->GID_LIC_PCODE;
		$install_dtl['GID_FULLFILLMENT_NO']=$install_dtl['GID_LIC_FULLFILLMENT_NO']=$this->GID_FULLFILLMENT_NO;
		$install_dtl['GID_LEAD_CODE']=$this->GID_LEAD_CODE;
		if( ($this->INSTALLED_BY!='') && ($this->INSTALLED_BY!='self') ){
			$install_dtl['GID_EMP_ID']=$this->INSTALLED_BY;
		}else{
			$install_dtl['GID_EMP_ID']=($this->IS_DEALER)?$this->DEALER_ID:SALES_DUMMY_ID;
		}
		$install_dtl['GID_PRODUCT_SKEW']=$install_dtl['GID_LIC_PSKEW']=$this->GID_LIC_PSKEW;
		$install_dtl['GID_CURRENT_VERSION']=$install_dtl['GID_PRODUCT_VERSION']=$this->VERSION;
		$install_dtl['GID_NO_CLIENTS']=$install_dtl['GID_NCLIENTS_UPDATED_IN_CUSTPLACE']=$this->GID_NO_CLIENTS;
		$install_dtl['GID_VALIDITY_DATE']=$this->GID_VALIDITY_DATE;
		$install_dtl['GID_EXPIRE_FOR']=$this->GID_EXPIRE_FOR;
		$install_dtl['GID_LIC_HARD_DISK_ID']=$this->GID_LIC_HARD_DISK_ID;
		$install_dtl['GID_SYS_HDD_ID']=isset($this->parsed_xml['HDD_KEY'])?$this->parsed_xml['HDD_KEY']:'';
		$install_dtl['GID_SYS_HKEY']=$this->HKEY;
		$install_dtl['GID_MACHINE_NAME']=isset($this->parsed_xml['MACHINE_NAME'])?$this->parsed_xml['MACHINE_NAME']:'';
		$install_dtl['GID_LIC_GEN_TYPE']=1;
		$install_dtl['GID_NO_COMPANYS']=$install_dtl['GID_NCOMPANYS_UPDATED_IN_CUSTPLACE']=$this->GID_NO_COMPANYS;
		$install_dtl['GID_HEAD_OF_FAMILY']=$this->HEAD_OF_FAMILY;
		$install_dtl['GID_INSTALL_DATE']=date('Y-m-d');
		$install_dtl['GID_STATUS']='A';
		$install_dtl['GID_SUBSCRIPTION_STATUS']='Y';
		$install_dtl['GID_UPGRADATION_DONE']='Y';
		$install_dtl['GID_CREATED_TIME']=date('Y-m-d H:i:s');
		$install_dtl['GID_REF_SERIAL_NO']=$this->GID_REF_SERIAL_NO;
		if($this->PENDING_APPROVAL==1) {
			$install_dtl['GID_CURRENT_LICENSE']='TRIAL';
			$install_dtl['GID_TRIAL_APPLIED_COUNT']=1;
		}else {
			$install_dtl['GID_CURRENT_LICENSE']='ACTUAL';
			$install_dtl['GID_TRIAL_APPLIED_COUNT']=0;
		}
		if($this->annuity_valid!=''){
			$install_dtl['GID_TRIAL_TILL_DATE']=$this->annuity_valid;
			$install_dtl['GID_SENT_EXPIRY_DATE']=$this->annuity_valid;
		}else{
			$install_dtl['GID_TRIAL_TILL_DATE']=$this->GID_VALIDITY_DATE;
			$install_dtl['GID_SENT_EXPIRY_DATE']=$this->GID_VALIDITY_DATE;
		}
		$install_dtl['GID_INSTALLED_MOBILE_NO']=$this->INSTALLED_NO;
		$install_dtl['GID_INSTALLED_EMP']=$this->INSTALLED_BY;
		$install_dtl['GID_CONNECTPLUS_TOKEN'] = $this->CP_TOKEN;
		$install_dtl['GID_NS_TOKEN'] = $this->NS_TOKEN;
		$install_dtl['GID_DB_PASSWORD'] = $this->DATA_SECURITY_PASSWORD;
		if($this->DATA_SECURITY_PASSWORD!=''){
		    $install_dtl['GID_PASSWORD_GENERATED_DATE'] = date('Y-m-d H:i:s');
		}
		array_update_tables_common($install_dtl,$table_name,null,null,SALES_DUMMY_ID,$remarks=null,$table_column_iff_update=null,$install_dtl);
		$install_id=mysqli_insert_id_wrapper();
		if($this->CP_TOKEN!=''){
			post_token_to_authservice($this->GID_LEAD_CODE,$install_id);
			post_gosecure_license_type($this->GID_LEAD_CODE);
		}
		generate_accounts_invoice($this->GID_ORDER_NO, 'customer', 'lic_activation');
		account_posting_to_integration_portal($this->GID_LEAD_CODE,$this->GID_LIC_PCODE);
		if( ($this->WEB_REQUEST_TYPE=='14') && ($this->INSTALLED_BY!='') && ($this->INSTALLED_BY!='self') ){
			update_daily_achieved($this->INSTALLED_BY, 14, 1);  //14 - trial installation
			$lfd_update_query = " update gft_lead_hdr join gft_customer_status_master on (GLH_STATUS=GCS_CODE) ".
								" set GLH_LFD_EMP_ID='$this->INSTALLED_BY' where GLH_LEAD_CODE='$this->GID_LEAD_CODE' ".
								" and GCS_DUPLICATE_CASE='From New' and GLH_CREATED_BY_EMPID=9999 ";
			execute_my_query($lfd_update_query);
		}
		//commented below lines as there is no need to update the training status during installation. PD process will take care
	/*	if($this->GOD_IMPL_REQUIRED === 'Yes'){
			$opcode="{$this->GID_ORDER_NO}{$this->GID_LIC_PCODE}{$this->GID_LIC_PSKEW}{$this->GID_FULLFILLMENT_NO}";
			update_implementation_status($this->GID_LEAD_CODE,$install_id,$opcode);
		}  */
		
		$this->update_gft_order_product_dtl($this->GID_ORDER_NO,$this->GID_LIC_PCODE,$this->PSKEW,$this->GID_LEAD_CODE,$this->GID_LIC_PSKEW);
		$this->update_advance_asa_order($this->GID_LEAD_CODE, $this->GID_ORDER_NO,$this->GID_LIC_PCODE,$this->HEAD_OF_FAMILY,
		$this->GID_FULLFILLMENT_NO,$this->GID_LIC_PSKEW,$install_id);
		
		update_install_table_outstanding($this->GID_ORDER_NO);
		if($this->GPM_TRAINING_REQUIRED === 'Y'){   	
			//Asked by Sivaprakash that No need to create pending Training complaint during the product installation 
			/* insert_support_entry($this->GID_LEAD_CODE,$this->GID_LIC_PCODE,$this->GID_LIC_PSKEW,$this->parsed_xml['VERSION'],
			$this->PRODUCT_TYPE,SALES_DUMMY_ID,'16','Training Pending','18','T6'); */
		}
		support_activity_mv_status($current_status=array('T9','T8'),$this->GID_LEAD_CODE,SALES_DUMMY_ID,(string)$install_dtl['GID_INSTALL_DATE'],
		$this->GID_LIC_PCODE,$change_staus='T1');
		if($this->HKEY!=''){
			$insert_dtl['GHL_INSTALL_ID']		= $install_id;
			$insert_dtl['GHL_HKEY']			 	= $this->HKEY;
			$insert_dtl['GHL_MACHINE_NAME']	 	= $this->GID_MACHINE_NAME;
			$insert_dtl['GHL_CURRENT_STATUS']	= 'A';
			$insert_dtl['GHL_CREATED_TIME']	 	= date('Y-m-d H:i:s');
			array_update_tables_common($insert_dtl, "gft_hkey_log", null, null, SALES_DUMMY_ID,null,null,$insert_dtl);
		}
		if(is_auto_split_order($this->ORDER_NO,$this->FULLFILLMENT_NO)){
		    $hq_installed_id = get_hq_installed_cust_id($this->LEAD_CODE);
		    notify_pos_product($hq_installed_id, "", "outlet_order_update", "",$this->GID_LIC_PCODE,'',false,'','','','',$this->ORDER_NO.substr("0000".$this->FULLFILLMENT_NO,-4));
		    notify_pos_product($hq_installed_id, "", "outlet_uuid_update", "",$this->GID_LIC_PCODE,"",false,"","","","",$this->ORDER_NO.substr("0000".$this->FULLFILLMENT_NO,-4));
		}
	}
	
	/**
	 * @return void
	 */
	protected function update_install_dtl_table(){
		
		if($this->GID_INSTALL_ID != 0){
			$install_update_arr=/*. (mixed[string]) .*/array();
			if($this->GID_NO_CLIENTS > $this->GID_NCLIENTS_UPDATED_IN_CUSTPLACE){
				$install_update_arr['GID_NCLIENTS_UPDATED_IN_CUSTPLACE']=$this->GID_NO_CLIENTS;
				$this->entry_gft_client_install_dtl();
			}  
			if($this->GID_NO_COMPANYS > $this->GID_NCOMPANYS_UPDATED_IN_CUSTPLACE){
				$install_update_arr['GID_NCOMPANYS_UPDATED_IN_CUSTPLACE']=$this->GID_NO_COMPANYS;
			}
			if($this->GID_CURRENT_VERSION !== $this->VERSION ){
				$install_update_arr['GID_CURRENT_VERSION']=$this->VERSION;
				//log ....insert ....? 	
			}
			if($this->GID_UPGRADATION_DONE==='N' ){
				$install_update_arr['GID_UPGRADATION_DONE']='Y';
				$this->entry_gft_upgradation_dtl();
			}
			if($this->GID_SUBSCRIPTION_STATUS==='N' and $this->GID_EXPIRE_FOR ==2){
				$install_update_arr['GID_SUBSCRIPTION_STATUS']='Y';
				$this->entry_renewal_dtl_new();
			}
			if($this->GID_SUBSCRIPTION_STATUS==='N' and $this->GID_EXPIRE_FOR ==1){
				$install_update_arr['GID_SUBSCRIPTION_STATUS']='Y';
				 $this->entry_asa_update_dtl();
			} 
			$install_update_arr['GID_LIC_HARD_DISK_ID']=$this->GID_LIC_HARD_DISK_ID;
			/* update any reinstallation approval....pending*/
			if(isset($this->parsed_xml['HDD_KEY']) && (string)$this->parsed_xml['HDD_KEY']!==''){
				$install_update_arr['GID_SYS_HDD_ID']=$this->parsed_xml['HDD_KEY'];
			}
			if($this->HKEY!=''){
				$install_update_arr['GID_SYS_HKEY']=$this->HKEY;
				$install_update_arr['GID_MACHINE_NAME']=$this->GID_MACHINE_NAME;
				$install_update_arr['GID_OTP_ATTEMPTS']=0; //emptying after success
				$check_rows = mysqli_num_rows(execute_my_query("select GHL_INSTALL_ID from gft_hkey_log where GHL_INSTALL_ID='$this->GID_INSTALL_ID' and GHL_CURRENT_STATUS='A'"));
				if($check_rows==0){
					execute_my_query("update gft_hkey_log set GHL_CURRENT_STATUS='A' where GHL_INSTALL_ID='$this->GID_INSTALL_ID' and GHL_HKEY='$this->HKEY' order by GHL_ID desc limit 1");
				}
			}			
			$install_update_arr['GID_SENT_EXPIRY_DATE'] = ($this->annuity_valid!='')?$this->annuity_valid:$this->GID_VALIDITY_DATE;
			if( isset($this->parsed_xml['VERSION']) && ($this->parsed_xml['VERSION']!='') ){
				$install_update_arr['GID_CURRENT_VERSION'] 	= $this->parsed_xml['VERSION'];
			}
			
			if($this->GAR_ID!=0){
				$table_name='gft_approved_reinstallation_dtl';
				$update_arr=/*. (mixed[string]) .*/array();
				$key_field_arr=/*. (mixed[string]) .*/array();
				$update_arr['GAR_REINSTALL_DATE']=date('Y-m-d');
				$update_arr['GAR_REINSTALL_ID']=(isset($this->GRD_REINSTALL_ID)?$this->GRD_REINSTALL_ID:0);
				$update_arr['GAR_STATUS']='C';
				$key_field_arr['GAR_ID']=$this->GAR_ID;
				$key_field_arr['GAR_INSTALL_ID_REFF']=$this->GID_INSTALL_ID;
				$key_field_arr['GAR_PRODUCT_CODE']=$this->GID_LIC_PCODE;
				$key_field_arr['GAR_PRODUCT_SKEW']=$this->GID_LIC_PSKEW;
				array_update_tables_common($update_arr,$table_name,$key_field_arr,null,SALES_DUMMY_ID,'Reinstallation done',null,null);
				$audit_query= "insert into gft_audit_viewer_order (GAV_TABLE_NAME, GAV_ORDER_NO, GAV_PRODUCT_CODE,GAV_PRODUCT_SKEW, GAV_COLUMN_NAME, GAV_PREVIOUS_VALUE, GAV_UPDATED_VALUE, GAV_UPDATED_DATETIME, GAV_UPDATED_BY, GAV_FROM_PAGE)" .
							" values ('gft_install_dtl_new','$this->ORDER_NO', '$this->GID_LIC_PCODE','$this->GID_LIC_PSKEW','GID_STATUS','S','A',now(),'9999','licenseUpdate.php') ";
				execute_my_query($audit_query);
				notify_pos_product(get_hq_installed_cust_id($this->LEAD_CODE), '', "outlet_uuid_update", "",$this->GID_LIC_PCODE,"",false,"","","","",$this->ORDER_NO.substr("0000".$this->FULLFILLMENT_NO,-4));
			}
			
			if($this->GLS_ID!=0){
				$table_name='gft_lic_surrender';
				$update_arr=/*. (mixed[string]) .*/array();
				$key_field_arr=/*. (mixed[string]) .*/array();
				$update_arr['GLS_UPDATE_DATE']=date("Y-m-d H:i:s");
				$update_arr['GLS_CODE_STATUS']='I';
				$update_arr['GLS_SUR_STATUS']='Y';
				$key_field_arr['GLS_ID']=$this->GLS_ID;
				array_update_tables_common($update_arr,$table_name,$key_field_arr,null,SALES_DUMMY_ID,'Surrender license Activated before mail confirmation',null,null);
			}
			
			$install_key_arr['GID_INSTALL_ID']=$this->GID_INSTALL_ID;
			$table_column_iff_update['GID_UPDATED_TIME']=date('Y-m-d H:i:s');
		
			array_update_tables_common($install_update_arr,$table_name='gft_install_dtl_new',$install_key_arr,$extra_field_to_update=null,SALES_DUMMY_ID,
			$remarks=null,$table_column_iff_update=null,$insert_new_row=null);
			execute_my_query("update gft_install_dtl_new set GID_STATUS='A' where GID_INSTALL_ID='$this->GID_INSTALL_ID' and GID_STATUS='S'");
		}		
		
	}
	
	/**
	 * @return void
	 */
	protected function update_Trail_otp_status_to_inactive(){
			$table_name='gft_otp_master';
			$update_arr=/*. (mixed[string]) .*/array();
			$table_key_arr=/*. (mixed[string]) .*/array();
			$update_arr['GOM_OTP_STATUS']='I';
			$table_key_arr['GOM_LEAD_CODE']=$this->LEAD_CODE ;
			$table_key_arr['GOM_WEB_REQUEST_TYPE']=11;
			$table_key_arr['GOM_PCODE']=$this->PCODE;
			$table_key_arr['GOM_PSKEW']=$this->PSKEW;
			$table_key_arr['GOM_HDD_ID']=$this->parsed_xml['HDD_KEY'];
			$table_key_arr['GOM_OTP_MAIL_TO']=$this->parsed_xml['EMAIL'];
			array_update_tables_common($update_arr,$table_name,$table_key_arr,null,SALES_DUMMY_ID,$remarks='OTP MOVE TO INACTIVE',null);
	}
	
	/**
	 * @return void
	 */
	public function update_activity(){
		$lic_days = get_samee_const('Evaluation_Limit');
		$ins_que2 = " INSERT INTO GFT_LIC_APPROVED_LOG(GLA_LEAD_CODE, GLA_ORDER_NO, GLA_FULLFILLMENT_NO, GLA_PRODUCT_CODE, ".
				" GLA_PRODUCT_SKEW, GLA_STATUS_CHANGEDAS,GLA_STATUS_ID, GLA_APPROVED_BY, GLA_APPROVED_ON, GLA_SENT_POS_ON, GLA_EMP_COMMENTS, GLA_NOOF_DAYS) ".
				" VALUES ('$this->LEAD_CODE', '$this->ORDER_NO', $this->FULLFILLMENT_NO,'$this->PCODE','$this->TRIAL_SKEW', ".
				" 'Waiting Contact Approval', '7','9999',now(),now(),'$lic_days days evaluation license given','2')";
		$res_que2 = execute_my_query($ins_que2);
		
		$activity_dtl					=	array();
		$activity_dtl['GLD_LEAD_CODE']	=	$this->LEAD_CODE;
		$activity_dtl['GLD_EMP_ID']		=	SALES_DUMMY_ID;//get_lead_mgmt_incharge($this->GLH_COUNTRY, $this->GLH_STATECODE, $this->GLH_CREATED_CATEGORY, $this->parsed_xml['VERTICAL']);
		$activity_dtl['GLD_DATE']		=	date('Y-m-d H:i:s');
		$activity_dtl['GLD_VISIT_DATE']	=	date('Y-m-d');
		$activity_dtl['GLD_NOTE_ON_ACTIVITY']	= "$lic_days days evaluation license given";
		$activity_dtl['GLD_VISIT_NATURE']		= '1';
		//$activity_dtl['GLD_LEAD_STATUS']=26;
		//$activity_dtl['GLD_CALL_STATUS']="P";
		//$activity_dtl['GLD_REPEATED_VISITS']="N";
		//$activity_dtl['GLD_INTEREST_ADDON']="U";
		//$activity_dtl['GLD_SCHEDULE_STATUS']=1;
		//$activity_dtl['GLD_NEXT_ACTION_DATE']=date('Y-m-d');
		//$activity_dtl['GLD_NEXT_ACTION']=52;
		insert_in_gft_activity_table($activity_dtl,$extra_activity_dtl=null,$new_lead=true);
		
	}
	
	/**
	 * @return void
	 **/
	protected function update_license_dtl(){
		
		if($this->LOCAL_USER === 'N'){
			$vertical = isset($this->parsed_xml['VERTICAL'])?(int)$this->parsed_xml['VERTICAL']:0;
			if(in_array($this->GID_LIC_PCODE,array('200','300','303','500','501','502','550','551'))){ //only base pcodes
			    if( ($vertical!=0) && ($vertical!=19) ){ //wine shop vertical update stopped temporarily till issue fix in wns sync by elamaran
			        update_vertical_for_customer($this->GID_LEAD_CODE,$vertical);
			    }
			}
			if($this->GID_INSTALL_ID==0){
				$this->insert_in_gft_install_dtl_and_other();
				$addon_category = get_single_value_from_single_table("GPM_ADDON_CATEGORY", "gft_product_family_master", "GPM_PRODUCT_CODE", $this->GID_LIC_PCODE);
			 	if(in_array($this->GID_LIC_PCODE,array('519','535')) || ($addon_category=='4') ){ //capillary
					zepogatewaycall($this->GID_ORDER_NO, $this->GID_LEAD_CODE, $this->GID_LIC_PCODE,$this->GID_FULLFILLMENT_NO);
				}
				if($this->WEB_REQUEST_TYPE==12) {  
				    update_lead_status($this->GID_LEAD_CODE, 9);
				}	
				$this->InstallationType=1;
				$this->InstallationType_Desc='New Installation';
				if($this->WEB_REQUEST_TYPE==14 and $this->OTP!=''){					
					$this->update_Trail_otp_status_to_inactive();			
					//updating as OTP Used by downladed lead.
					$update_que = " update gft_presignup_registration set GPR_INSTALL_OTP_STATUS=2 where GPR_LEAD_CODE='$this->LEAD_CODE' and GPR_INSTALL_OTP_STATUS!=2 ";
					execute_my_query($update_que);										
				}
				$this->send_mail = isset($_REQUEST['send_mail'])?(int)$_REQUEST['send_mail']:1;
				if($this->send_mail){
					if($this->IS_DEALER){
						send_notification_to_dealer_for_trial_license($this->DEALER_ID);
					}else{
						$this->send_activation_mail_from_SAM();//mailer function
					}
				}
				if($this->WEB_REQUEST_TYPE==14){
					$this->update_activity();
				}
				if(in_array($this->WEB_REQUEST_TYPE,array(12,27,11,14))){
					update_call_preferance($this->GID_LEAD_CODE);
				}
			}
			else if($this->GAR_ID!=0){
				$this->entry_reinstall_dtl();
				$this->update_install_dtl_table();
				$this->InstallationType=2;
				$this->InstallationType_Desc='Reinstallation';
			}else if(!empty($this->GID_UPGRADATION_DONE) and $this->GID_UPGRADATION_DONE === 'N'){
				$this->update_install_dtl_table();
				$this->InstallationType=6;
				$this->InstallationType_Desc='Upgradation';
			}else if(!empty($this->GID_SUBSCRIPTION_STATUS) and $this->GID_SUBSCRIPTION_STATUS ==='N' and $this->GID_EXPIRE_FOR ==2){
				$this->update_install_dtl_table();			
				$this->InstallationType=5;
				$this->InstallationType_Desc='Subscription Renewal';			
			}else if(!empty($this->GID_SUBSCRIPTION_STATUS) and $this->GID_SUBSCRIPTION_STATUS ==='N' and $this->GID_EXPIRE_FOR ==1){
				$this->update_install_dtl_table();			
				$this->InstallationType=4;
				$this->InstallationType_Desc='ASA Update';
				$this->entry_asa_update_dtl();			
			}else if($this->GID_NO_CLIENTS > $this->GID_NCLIENTS_UPDATED_IN_CUSTPLACE){
				$this->update_install_dtl_table();			
				$this->InstallationType=3;
				$this->InstallationType_Desc='Client Update';
			}else{
				$this->update_install_dtl_table();
				$this->InstallationType=7;
				$this->InstallationType_Desc='License Update';	
			}
			
			//update_cust_bal($this->GID_LEAD_CODE); //commenting this function as it creates memory issue due to recursive call in corporate customer and client when their mapping is incorrect 
			if($this->GID_SYS_HKEY==''){
				$this->update_hkey_log();
			}
			if( isset($this->parsed_xml['PERIODIC_CHECK']) && ($this->parsed_xml['PERIODIC_CHECK']=='YES') && $this->GPM_LICENSE_TYPE!=3 ){
				$this->update_periodic_check_log('Valid');
			}
			
		}else{
			$insert_arr=/*. (mixed[string]) .*/array();
			$table_name='gft_local_user_activation_dtl';
			$insert_arr['GLU_EMP_ID']=$this->LOCAL_USER_ID;
			$insert_arr['GLU_HARD_DISK_ID']=$this->parsed_xml["HDD_KEY"];
			$insert_arr['GLU_DATE_OF_ACTIVATION']=date('Y-m-d H:i:s');
			$insert_arr['GLU_PRODUCT_CODE']=$this->PCODE;
			$insert_arr['GLU_PRODUCT_SKEW']=$this->GID_LIC_PSKEW;
			$insert_arr['GLU_DB_PASSWORD']=$this->DB_PASSWORD;
			$insert_arr['GLU_UUID']=$this->HKEY;
			
			array_update_tables_common($insert_arr,$table_name,null,null,SALES_DUMMY_ID,$remarks=null,$table_column_iff_update=null,$insert_arr);	
			$name_que="select GPM_PRODUCT_NAME from gft_product_family_master where GPM_PRODUCT_CODE=SUBSTR('$this->PRODUCT_ID',1,3)";
			$name_res=execute_my_query($name_que);
			if($data=mysqli_fetch_array($name_res)){
				$this->PRODUCT_NAME=$data['GPM_PRODUCT_NAME'];
			}
			$db_content_config=/*. (string[string][int]) .*/array(
				'Employee_Name' => array($this->LOCAL_USER_NAME),
				'Order_No' => array($this->ORDER_NO_SPLITED),
				'Product_Name' => array($this->PRODUCT_NAME),
				'Activation_Key' => array($this->ACTIVATION_KEY),
				"Expiry_Date"=>array($this->GID_VALIDITY_DATE),
				'Hardisk_id'=>array($this->parsed_xml["HDD_KEY"]),
				'Activation Date' =>array(date('Y-m-d H:i:s')),
				'Version'=>array($this->parsed_xml["VERSION"])						
			);
			if(isset($this->parsed_xml['PERIODIC_CHECK']) && ($this->parsed_xml['PERIODIC_CHECK']=='YES') && $this->GPM_LICENSE_TYPE!=3){
				$this->update_periodic_check_log('Valid');
			}else{
				send_formatted_mail_content($db_content_config,9,153,array($this->LOCAL_USER_ID),null);
			}
		}
		

	}
	
	/**
	 * @param string $check_status
	 * 
	 * @return void
	 */
	protected function update_periodic_check_log($check_status=''){
		if($check_status!='') {
			$insert_array=/*. (mixed[string]) .*/array();
			$table='gft_periodic_license_check';
			$insert_array['GPL_LEAD_CODE']=$this->LEAD_CODE;
			$insert_array['GPL_ORDER_NO']=$this->ORDER_NO;
			$insert_array['GPL_FULLFILLMENT_NO']=$this->FULLFILLMENT_NO;
			$insert_array['GPL_PRODUCT_ID']=$this->PRODUCT_ID;
			$insert_array['GPL_CHECK_DATE']=date('Y-m-d');
			$insert_array['GPL_CHECK_TIME']=date('H:i:s');
			$insert_array['GPL_CHECK_STATUS']=$check_status;
			$insert_array['GPL_ERROR_MSG']=$this->GLC_ERROR_MESSAGE;
			array_update_tables_common($insert_array,$table,null,null,SALES_DUMMY_ID,$remarks=null,$table_column_iff_update=null,$insert_array);
		}
	}
	
	/**
	 * @return void
	 */
	protected function send_mail_sms_trial_otp(){
		
		$db_mail_content_config=/*. (string[string][int]) .*/array(
			"OTP"=>array($this->OTP),
			"Customer_Id"=>array($this->LEAD_CODE)
		);
		send_formatted_mail_content($db_mail_content_config,85,155,$employee_ids=null,array($this->LEAD_CODE),array($this->parsed_xml['EMAIL']));
		if(!empty($this->parsed_xml['MOBILE_NO'])){
		    $db_sms_content_config=/*. (string[string][int]) .*/array(
				"OTP"=>array($this->OTP),
				"Customer_ID"=>array($this->LEAD_CODE)
			);
			$temp_content=get_formatted_content($db_sms_content_config,143);
			$temp_content=$sms_content=htmlentities($temp_content);
			entry_sending_sms_to_customer($this->parsed_xml['MOBILE_NO'],$sms_content,143,$this->LEAD_CODE,0,$sender=null,
											$send_to_alert=0,$tele_cust_code=null,true);
			//sms for installed_by
			if($this->INSTALLED_NO!=''){
				$sms_arr_content = array(
					"Customer_ID"=>array($this->LEAD_CODE),
					"Sales_Person"=>array($this->INSTALLED_NAME),
					"Customer_Name"=>array(isset($this->parsed_xml['SHOP_NAME'])?$this->parsed_xml['SHOP_NAME']:'')
				);
				$sms_content=htmlentities(get_formatted_content($sms_arr_content, 145));
				entry_sending_sms_to_customer($this->INSTALLED_NO,$sms_content,145,$this->LEAD_CODE,0,$sender=null,$send_to_alert=0,$tele_cust_code=null,true);
			}
		}
		$this->update_Trail_otp_status_to_inactive();
		
		$table_name='gft_otp_master';
		$table_key_arr=/*. (mixed[string]) .*/array();
		$insert_arr=/*. (mixed[string]) .*/ array();
		$insert_arr['GOM_LEAD_CODE']=$this->LEAD_CODE ;
		$insert_arr['GOM_HDD_ID']=$this->parsed_xml['HDD_KEY'];
		$insert_arr['GOM_WEB_REQUEST_TYPE']=$this->WEB_REQUEST_TYPE;
		$insert_arr['GOM_WEB_REQUEST_ID']=$this->ONLINE_REQUEST_ID;
		$insert_arr['GOM_PCODE']=$this->PCODE;
		$insert_arr['GOM_PSKEW']=$this->PSKEW;
		$insert_arr['GOM_OTP_MAIL_TO']=$this->parsed_xml['EMAIL'];
		$insert_arr['GOM_OTP_SMS_TO']=(!empty($this->parsed_xml['MOBILE_NO'])?$this->parsed_xml['MOBILE_NO']:'');
		$insert_arr['GOM_GEN_DATE_TIME']=date('Y-m-d H:i:s');
		$insert_arr['GOM_OTP']=$this->OTP;
		$insert_arr['GOM_OTP_STATUS']='A';

		array_update_tables_common($insert_arr,$table_name,$table_key_arr,null,SALES_DUMMY_ID,$remarks='OTP Generation',null,$insert_arr);
		
		//updating as OTP sent.
		$update_que = " update gft_presignup_registration set GPR_INSTALL_OTP_STATUS=1 where GPR_LEAD_CODE='$this->LEAD_CODE' and GPR_INSTALL_OTP_STATUS!=2 ";
		execute_my_query($update_que);
	}
	
	
/**
 * @param string $skew
 * 
 * @return boolean
 */
	protected function generate_order_no_on_request($skew){
		
		if($this->PCODE!='' and $this->LEAD_CODE!=''){
$query_exist_unused_order_check=<<<END
 			select GOD_ORDER_NO, GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW from gft_order_product_dtl 
		 	join gft_order_hdr on (gop_order_no=god_order_no )
		 	JOIN gft_product_master pm on (gpm_product_code=gop_product_code and gpm_product_skew=gop_product_skew )
			where god_lead_code=$this->LEAD_CODE and gop_product_code=$this->PCODE and GOP_PRODUCT_SKEW='$skew'
			and god_order_status='A'  and gop_usedqty=0
END;
			
			$result=execute_my_query($query_exist_unused_order_check);
			if(mysqli_num_rows($result)>0){
				$data=mysqli_fetch_array($result);				
				$this->ORDER_NO=$data['GOD_ORDER_NO'];								
				return true;
				
			}else{
				$query=" select glh_territory_id,glh_vertical_code ".
						" from gft_lead_hdr where glh_lead_code=$this->LEAD_CODE ";
				$result=execute_my_query($query,'web_custdateils.php',true,false);
				$data=mysqli_fetch_array($result);
				$territory_id=$data['glh_territory_id'];
				$vertical=$data['glh_vertical_code'];
				$order_date=date('Y-m-d H:i:s');
				$year=date('y');
				$order_no=get_order_no('D',$year,$territory_id,SALES_DUMMY_ID);
				$fullfill_no = 1;
				if($order_no!='' and strlen($order_no)==15){
					$table_name='gft_order_hdr';
					$insert_arr=$table_key_arr=/*. (mixed[string]) .*/array();
					$insert_arr['GOD_ORDER_NO']=$order_no;
					$insert_arr['GOD_LEAD_CODE']=$this->LEAD_CODE;
					$insert_arr['GOD_EMP_ID']=$insert_arr['GOD_INCHARGE_EMP_ID']=$this->GOD_EMP_ID;
					$insert_arr['GOD_ORDER_DATE']=date('Y-m-d');
					$insert_arr['GOD_ORDER_STATUS']='A';
					$insert_arr['GOD_ORDER_AMT']=0;
					$insert_arr['GOD_CREATED_DATE']=date('Y-m-d H:i:s');
					$insert_arr['GOD_ORDER_TYPE']=1;
					$insert_arr['GOD_ORDER_APPROVAL_STATUS']=2; //oRDER aPPROVED
					
					$table_key_arr['GOD_ORDER_NO']=$order_no;
					array_update_tables_common($insert_arr,$table_name,$table_key_arr,null,SALES_DUMMY_ID,$remarks=null,$table_column_iff_update=null,$insert_arr);
					
					$table_name='gft_order_product_dtl';
					$insert_gop_table=$table_key_arr=/*. (mixed[string]) .*/array();
					$insert_gop_table['GOP_ORDER_NO']=$order_no;
					$insert_gop_table['GOP_FULLFILLMENT_NO']=$fullfill_no;
					$insert_gop_table['GOP_PRODUCT_CODE']=$this->PCODE;
					$insert_gop_table['GOP_PRODUCT_SKEW']=$skew;
					$insert_gop_table['GOP_QTY']=1;
					$insert_gop_table['GOP_LICENSE_STATUS']=2;  //Approved by CM
					$insert_gop_table['GOP_REFERENCE_ORDER_NO']		= $this->REFERENCE_ORDER;
					$insert_gop_table['GOP_REFERENCE_FULLFILLMENT_NO']	= $this->REFERENCE_FULLFILLMENT;
					
					array_update_tables_common($insert_gop_table,$table_name,null,null,SALES_DUMMY_ID,$remarks=null,$table_column_iff_update=null,$insert_gop_table);
					$this->ORDER_NO=$order_no;	
					zepogatewaycall($order_no, $this->LEAD_CODE, $this->PCODE, $fullfill_no);
					provision_connectplus($order_no);
					generate_pos_notification($order_no);
					return true;
					
		    	}else{
		    		$this->GLC_ERROR_CODE='TF02';
		    		return false;
		    	}
			}
		
		}
		return false; 					 
	}
	
	/**
	 * @return void
	 */
	public function update_lic_approved_log_details(){
		$upd_query=" update gft_lic_approved_log join gft_install_dtl_new on (GID_ORDER_NO=GLA_ORDER_NO and GID_FULLFILLMENT_NO=GLA_FULLFILLMENT_NO ".
				" and GID_PRODUCT_CODE=GLA_PRODUCT_CODE and GID_PRODUCT_SKEW=GLA_PRODUCT_SKEW) ".
				" set GLA_SENT_POS_ON =now() ".
				" where GID_ORDER_NO='$this->ORDER_NO' and GID_FULLFILLMENT_NO=$this->FULLFILLMENT_NO ".
				" and GID_LIC_PCODE=$this->GID_LIC_PCODE and GID_LIC_PSKEW='$this->GID_LIC_PSKEW' and GLA_SENT_POS_ON is null ";
		execute_my_query($upd_query);
	}
}


?>
