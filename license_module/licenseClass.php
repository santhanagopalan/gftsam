<?php
require_once(__DIR__."/../dbcon.php");
require_once(__DIR__."/licenseUpdate.php");
require_once(__DIR__."/../lic_util.php");
require_once(__DIR__."/../function.update_in_tables.php");

/*.
forward class licenseClass{
	private boolean function check_productkey();
	protected boolean function check_local_user();
	protected boolean function check_local_user_old();
	public void function check_order_split();
	public string function get_license_status();
	public string[int][int] function get_custom_license();
	public void function get_license_caption_url();
	public void function check_local_hq_license();
	public string function generate_lead();
}
.*/



class licenseClass extends licenseUpdate {
	protected /*. string .*/ $IP_ADDRESS;
    protected /*. string .*/ $USERID;
    protected /*. string .*/ $GLC_ONLINE_CONTENT;
    protected /*. string .*/ $GLC_DECRYPTED_CONTENT;
    protected /*. string .*/ $GLC_ERROR_MESSAGE;
    protected /*. string .*/ $GLC_RETURN_DATA;
    protected /*. string .*/ $GLC_RETURN_ENCRYPTED_DATA;
    protected /*. string .*/ $GLC_STATUS;
    public /*. string .*/ $Minimum_Version;
    protected /*. string .*/ $Patch_download_link;
    protected /*. string .*/ $PRODUCT_STATUS;
   
    
  
	/**
	 * @return void
	 */
	function __construct(){
		global $secret;
		$this->IP_ADDRESS=$_SERVER['REMOTE_ADDR'];
		$this->USERID=SALES_DUMMY_ID;
		//qr code scan from mydelight for offline dukaan pos
		$mode = isset($_REQUEST['mode'])?(string)$_REQUEST['mode']:'';
		if($mode=='offline'){
			$this->IS_OFFLINE=true;
		}
		if(empty($_REQUEST['s'])){
			$this->GLC_ERROR_CODE='E000';
			return;
		}
		$this->GLC_ONLINE_CONTENT=(string)$_REQUEST['s'];
		$type=isset($_REQUEST['type'])?(string)$_REQUEST['type']:'';
		if($type == 'offline'){
			$this->GLC_DECRYPTED_CONTENT=$this->GLC_ONLINE_CONTENT;
		}else{
			$this->GLC_DECRYPTED_CONTENT=lic_decrypt($this->GLC_ONLINE_CONTENT,$secret);
		}
	}
	
	/**
	 * @param string $pcode
	 * @return boolean
	 */
	function check_product_code_correct($pcode){
		$pcode_avail_query="select GPM_PRODUCT_CODE, GPM_PRODUCT_NAME, GPM_HEAD_FAMILY, GPM_STATUS from " .
				"gft_product_family_master where gpm_product_code=$pcode ";
		$result_avail_query=execute_my_query($pcode_avail_query);
		if(mysqli_num_rows($result_avail_query)==1){
			$qd=mysqli_fetch_array($result_avail_query);
			$this->PCODE=(int)$pcode;
		//	$this->PRODUCT_NAME=$qd['GPM_PRODUCT_NAME'];
			$this->HEAD_OF_FAMILY=(int)$qd['GPM_HEAD_FAMILY'];
			$this->PRODUCT_STATUS=$qd['GPM_STATUS'];
			return true;
		}else{
			$this->GLC_ERROR_CODE='PR03';
			return false;
		}
	}

/**
 * @param int $pcode
 * @param string $product_group
 * 
 * @return boolean
 */	
	function check_product_group_correct($pcode,$product_group){
		$product_group_avail="SELECT GPG_PRODUCT_FAMILY_CODE,GPG_SKEW,GPG_STATUS FROM gft_product_group_master " .
				"where GPG_PRODUCT_FAMILY_CODE=$pcode and GPG_SKEW='$product_group' ";
		$result_product_group_avail=execute_my_query($product_group_avail);
		if(mysqli_num_rows($result_product_group_avail)==1){
			$qd=mysqli_fetch_array($result_product_group_avail);
			$this->PRODUCT_GROUP=$qd['GPG_SKEW'];
			return true;
		}else{
			$this->GLC_ERROR_CODE='PR09';
			return false;
		}
	}
	

/**
 * @return boolean 
 */	
function check_order_no_avail_in_sam(){

	if(!empty($this->ORDER_NO)){
		$query_order_no_exists="select GOD_ORDER_SPLICT from  gft_order_hdr where god_order_no='$this->ORDER_NO' ";
		$result_order_no_exists=execute_my_query($query_order_no_exists);
		if(mysqli_num_rows($result_order_no_exists)==1){
			return true;
		}else{
			$this->GLC_ERROR_CODE='E030';
			return false;
		}

	}

	return false;
}

/**
 * @return void
 */
public function check_for_pos_uat(){
	$sel_que=" select GID_VALIDITY_DATE from gft_uat_admin ".
			 " join gft_install_dtl_new on (GUA_UAT_ORDER=GID_ORDER_NO and GUA_UAT_FULLFILLMENT=GID_FULLFILLMENT_NO and GUA_UAT_PCODE=GID_LIC_PCODE and GUA_UAT_PSKEW=GID_LIC_PSKEW) ".
			 " where GUA_POS_ORDER='$this->ORDER_NO' and GUA_POS_FULLFILLMENT='$this->FULLFILLMENT_NO' ".
			 " and GUA_POS_PCODE='$this->PCODE' and GID_STATUS='A' ";
	$res = execute_my_query($sel_que);
	if(mysqli_num_rows($res)==1){
		$row = mysqli_fetch_array($res);
		$this->POS_UAT = 'Y';
		$this->UAT_VALIDITY = $row['GID_VALIDITY_DATE'];
	}
}
	
	/**
	 * @return boolean
	 */
	protected function make_the_request_readable(){
		if(!empty($this->parsed_xml['PRODUCT_FCODE'])){
			if(strlen($this->parsed_xml['PRODUCT_FCODE'])!=3){
				$this->GLC_ERROR_CODE='PR02';
				return false;
			}
			else if(!$this->check_product_code_correct($this->parsed_xml['PRODUCT_FCODE'])){
				return false;
			}	
		}
		if(!empty($this->parsed_xml['PRODUCT_GROUP'])){
			if(strlen($this->parsed_xml['PRODUCT_GROUP'])!=4){
				$this->GLC_ERROR_CODE='PR08';
				return false;
			}
			else if(!$this->check_product_group_correct($this->HEAD_OF_FAMILY,$this->parsed_xml['PRODUCT_GROUP'])){
				return false;
			}
		}
		if(!empty($this->parsed_xml['PRODUCT_ID'])){
			//		if(strlen($this->parsed_xml['PRODUCT_ID'])!=8){
			//			$this->GLC_ERROR_CODE='PR01';
			//			return false;
			//		}
			$pcode=substr($this->parsed_xml['PRODUCT_ID'],0,3);
			if(!is_numeric($pcode)){
				$this->GLC_ERROR_CODE='PR02';
				return false;
			}else if(!$this->check_product_code_correct($pcode)){
				return false;
			}
				
			$skew=substr($this->parsed_xml['PRODUCT_ID'],3,5);
			$pskew=substr($skew,0,2).'.'.substr($skew,2);
			$product_group=substr($pskew,0,4);
			if(!$this->check_product_group_correct($this->HEAD_OF_FAMILY,$product_group)){
				return false;
			}
			$skew_avail_query="select  GPM_PRODUCT_TYPE,GFT_SKEW_PROPERTY,GPT_TYPE_NAME  " .
					"from gft_product_master " .
					"join gft_product_type_master ptm on (GPT_TYPE_ID=GPM_PRODUCT_TYPE) " .
					"where gpm_product_code=$pcode and gpm_product_skew='$pskew'  ";
			$result_pskew_avail_query=execute_my_query($skew_avail_query);
			if(mysqli_num_rows($result_pskew_avail_query)==1){
				$qds=mysqli_fetch_array($result_pskew_avail_query);
				if((int)$qds['GFT_SKEW_PROPERTY'] == 1){
					$this->PSKEW=$pskew;
					$this->PRODUCT_TYPE=$qds['GPM_PRODUCT_TYPE'];
					$this->EDITION_TYPE_NAME=$qds['GPT_TYPE_NAME'];
				}else{
					$this->GLC_ERROR_CODE='PR04';
					return false;
				}
			}
			else{
				$this->GLC_ERROR_CODE='PR05'; //skew not available
				return false;
			}
			$this->PRODUCT_ID=$this->parsed_xml['PRODUCT_ID'];
			
			if(strlen($this->parsed_xml['PRODUCT_ID'])==12){
				$this->CUSTOM_GROUP=substr($this->parsed_xml['PRODUCT_ID'],8,4);
			}
				
		}
		$this->HKEY				= isset($this->parsed_xml['HKEY'])?$this->parsed_xml['HKEY']:'';
		$this->GID_MACHINE_NAME = isset($this->parsed_xml['MACHINE_NAME'])?$this->parsed_xml['MACHINE_NAME']:'';
		$this->DB_PASSWORD		= isset($this->parsed_xml['PASSWORD'])?$this->parsed_xml['PASSWORD']:'';
		$this->DEALER_ID		= isset($this->parsed_xml['DEALER_ID'])?$this->parsed_xml['DEALER_ID']:'';
		$dealer_enabled = get_single_value_from_single_table("CGI_ENABLE_DEALER", "gft_cp_info", "CGI_EMP_ID", $this->DEALER_ID);
		if($dealer_enabled=='1'){
			$this->IS_DEALER = true;
		}
		if(!empty($this->parsed_xml['ORDER_NO'])){
			if ($this->WEB_REQUEST_TYPE ==1 and strlen($this->parsed_xml['ORDER_NO']) == 15){
			     //Do nothing .. backward compatible for DE`
		    }else{
		  		if(strlen($this->parsed_xml['ORDER_NO'])!=19){
				$this->GLC_ERROR_CODE='OR01';
				return false;
				}			
		    }
			$this->ORDER_NO=substr($this->parsed_xml['ORDER_NO'],0,15);
			$this->FULLFILLMENT_NO=(int)substr($this->parsed_xml['ORDER_NO'],15,4);
			if( $this->WEB_REQUEST_TYPE==1 ){ //Backward compatibility 
				$this->check_local_user_old();
			}else{
				$this->check_local_user();
			}
			if($this->GLC_ERROR_CODE!=''){
				return false;
			}
			if($this->LOCAL_USER === 'N'){
				$this->check_order_no_avail_in_sam();
				if((int)$this->DEALER_ID > 0){
					$order_chk =" select GLH_LEAD_CODE from gft_order_hdr join gft_lead_hdr on (GOD_LEAD_CODE=GLH_LEAD_CODE) ".
							" where GOD_ORDER_NO='$this->ORDER_NO' and GLH_CREATED_BY_EMPID='$this->DEALER_ID' ";
					$chk_res = execute_my_query($order_chk);
					if(mysqli_num_rows($chk_res)==0){
						$this->GLC_ERROR_CODE='D003';
						return false;
					}
					 
				}
			}
		     if ($this->WEB_REQUEST_TYPE ==1 and strlen($this->parsed_xml['ORDER_NO']) == 15){
			     $product_key=$this->parsed_xml["PRODUCT_KEY"];
			    
			     $this->FULLFILLMENT_NO=getFullFillmentNumberFromProductKey($product_key);

		     }
		 	if($this->FULLFILLMENT_NO==0){
		 		$this->GLC_ERROR_CODE='OR02';
				return false;
		 	}
		    $this->check_for_pos_uat();
		}
		if($this->WEB_REQUEST_TYPE!=1){
			$this->check_local_user();
			if($this->GLC_ERROR_CODE!=''){
				return false;
			}
		}
		$rreason_desc_neccesary='N';
		if(isset($this->parsed_xml['RREASON'])){
			$reinstall_dtl_arr=explode('-',$this->parsed_xml['RREASON']);
			$this->RREASON_CODE=((int)$reinstall_dtl_arr[0]==3?4:(int)$reinstall_dtl_arr[0]);
			$this->RREASON_DESC=isset($reinstall_dtl_arr[1])?trim($reinstall_dtl_arr[1]):'';
			if($this->RREASON_CODE!=0){
				$reinsall_list=get_reinstall_reason($this->RREASON_CODE);
				$this->RREASON_CODE_DESC=$reinsall_list[0][1];
				$rreason_desc_neccesary=($reinsall_list[0][2]==='Y'?'Y':'N');
	
			}	
			if($this->RREASON_CODE==2){
				$this->GLC_ERROR_CODE='E017';
				return false;
			}	
			if($rreason_desc_neccesary==='Y' and strlen($this->RREASON_DESC)<5 ) {
				$this->GLC_ERROR_CODE='E018';
				return false;				
			}
		}
		if($this->ORDER_NO!='' && $this->FULLFILLMENT_NO!=''){
			$query_cp = " select GOD_LEAD_CODE,glh_lead_type,GID_LIC_PCODE from gft_lead_hdr ".
						" join gft_order_hdr on (GLH_LEAD_CODE = GOD_LEAD_CODE) ".
						" left join gft_install_dtl_new on (GID_ORDER_NO=GOD_ORDER_NO) ".
						" where GOD_ORDER_NO='$this->ORDER_NO' group by GOD_ORDER_NO";
			$res_cp = execute_my_query($query_cp);
			if($data_cp=mysqli_fetch_array($res_cp)){
				$this->LEAD_TYPE=$data_cp['glh_lead_type'];
				if( ($data_cp['GID_LIC_PCODE']=='120') && ($this->PCODE!=120) ){ //DemoFriendTool
					$this->IS_DFT_ORDER_NO_FOR_POS 	= true;
					$this->PCODE 		 			= 120;
					$this->PRODUCT_GROUP 			= '01.0';
				}
			}	
		}
		return true;
	}
	/**
	 * @param string[int] $mandatory_fields
	 * 
	 * @return boolean
	 */
	protected function check_necessary_tags($mandatory_fields){
		
		foreach($mandatory_fields as $value){
			if( (!isset($this->parsed_xml[$value])) || ($this->parsed_xml[$value]=='') ){
				$this->GLC_ERROR_CODE='E002';
				$this->GLC_ADDITIONAL_ERROR_MESSAGE="[".$value."]";
				return false;
			}
		}
		if(!$this->make_the_request_readable()){
			return false;
		}		 
		return true;
	}
	
	/**
	 * @return boolean
	 */	
	protected function validate_otp_for_hkey_change(){
		$wh_que = " where GOM_OTP='$this->OTP' and GOM_ORDER_NO='$this->ORDER_NO' and GOM_FULLFILLMENT_NO=$this->FULLFILLMENT_NO ".
					" and GOM_HKEY='$this->HKEY' and GOM_OTP_STATUS='A' ";
		$sql_que = " select GOM_LEAD_CODE from gft_otp_master $wh_que ";
		$sql_res = execute_my_query($sql_que);
		if($sql_data = mysqli_fetch_array($sql_res)){
			$sel_que=" select GAR_ID from gft_approved_reinstallation_dtl where GAR_ORDER_NO='$this->ORDER_NO' and GAR_FULLFILLMENT_NO=$this->FULLFILLMENT_NO ".
					" and GAR_PRODUCT_CODE='$this->GID_LIC_PCODE' and GAR_PRODUCT_SKEW='$this->GID_LIC_PSKEW' and GAR_STATUS='P' ";
			$num_rows = mysqli_num_rows(execute_my_query($sel_que));
			if($num_rows == 0){
				$sql_reinstall_update	=	" INSERT INTO gft_approved_reinstallation (GAR_REQUEST_BY, GAR_LEAD_CODE, GAR_REASON, GAR_DATE_OF_REQUEST, GAR_APPROVED_BY, GAR_APPROVED_DATE, GAR_APPROVED_FLAG, GAR_APPROVAL_COMMENT) ".
						" VALUES(9999, '$this->LEAD_CODE', 5, NOW(), 9999, NOW(), 'Y', 'OTP Success for Reinstallation')";
				execute_my_query($sql_reinstall_update);
				$this->GAR_ID	=	mysqli_insert_id_wrapper();
				$sql_reinstall_dtl		=	" INSERT INTO gft_approved_reinstallation_dtl (GAR_ID, GAR_ORDER_NO, GAR_PRODUCT_CODE, GAR_PRODUCT_SKEW, GAR_FULLFILLMENT_NO, GAR_STATUS, GAR_INSTALL_ID_REFF) ".
						" VALUES('$this->GAR_ID', '$this->ORDER_NO', '$this->GID_LIC_PCODE' , '$this->GID_LIC_PSKEW', $this->FULLFILLMENT_NO ,'C', $this->GID_INSTALL_ID) ";
				execute_my_query($sql_reinstall_dtl);
			}
			$sql_up = " update gft_otp_master set GOM_OTP_STATUS='I' $wh_que ";
			$this->GID_STATUS='S';
			if(execute_my_query($sql_up)){
				return true;
			}	
		}else{
			$this->GLC_ERROR_CODE='E013';
			$wrong_attempt = (int)get_single_value_from_single_table("GID_OTP_ATTEMPTS", "gft_install_dtl_new", "GID_INSTALL_ID", $this->GID_INSTALL_ID);
			$wrong_attempt = $wrong_attempt + 1;
			if($wrong_attempt==5){
				$db_content_config = array(
					'Order_No'=>array($this->ORDER_NO.substr('0000'.$this->FULLFILLMENT_NO, -4)),
					'Customer_Name'=>array($this->GLH_CUST_NAME),
					'Customer_Id'=>array($this->LEAD_CODE)
				);
				send_formatted_mail_content($db_content_config, 86, 224);
			}
			execute_my_query("update gft_install_dtl_new set GID_OTP_ATTEMPTS=$wrong_attempt where GID_INSTALL_ID='$this->GID_INSTALL_ID'");
		}
		return false;
	}
	
/**
 * @return boolean
 */		
	protected function is_reregister_in_exists_harddisk_id_trial(){
		
		$query=" select GID_INSTALL_ID,GID_INSTALL_DATE,GLH_CUST_NAME,GID_STATUS,GID_LEAD_CODE,GID_ORDER_NO,GID_EXPIRE_FOR,GID_VALIDITY_DATE," .
				"if(GID_VALIDITY_DATE>=date(now()),'Y','N') as validity_status,GID_FULLFILLMENT_NO," .
				" if(pm.GPM_REFERER_SKEW='',pm.GPM_PRODUCT_SKEW,pm.GPM_REFERER_SKEW) AS GPM_REFERER_SKEW ". 	
			    " from gft_install_dtl_new " .
			    " left join gft_lead_hdr lh on (glh_lead_code=gid_lead_code) ".
			    " join gft_product_master pm on (gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew ) " .
			    " where gid_product_code='$this->PCODE' and gid_product_skew='$this->TRIAL_SKEW' and gid_status in ('A','S') ";
		if(isset($this->parsed_xml['CUSTOMER_ID'])){
			$query .= " and GID_LEAD_CODE='".$this->parsed_xml['CUSTOMER_ID']."' ";
			$ord_dtl = get_order_dtl_of_lead($this->parsed_xml['CUSTOMER_ID'],null,false,'','',true,'',null,null,'N','Y');
			if(count($ord_dtl) > 0){
			    $this->GLC_ERROR_CODE="LA06";
			    return false;
			}
		}else if($this->LEAD_CODE!=0){
			$query .= " and GID_LEAD_CODE='$this->LEAD_CODE' ";
		}
		if($this->IS_DEALER){
			if(isset($this->parsed_xml['HDD_KEY'])){
				$query .= " and GID_SYS_HDD_ID='".$this->parsed_xml['HDD_KEY']."' ";
			}
			$query .= " and GLH_LEAD_TYPE=7 and GID_EMP_ID='$this->DEALER_ID' ";
		}else{
			$query .= " and GLH_LEAD_TYPE!=7 ";
		}
		$query .= " order by GID_VALIDITY_DATE desc limit 1 ";
		$result=execute_my_query($query);
		if($qd_ins=mysqli_fetch_array($result)){
		 	 $this->PRODUCT_ID=$this->PCODE.(string)str_replace('.','',$qd_ins['GPM_REFERER_SKEW']);	
			 $this->GID_LEAD_CODE=$this->LEAD_CODE=(int)$qd_ins['GID_LEAD_CODE'];
			 $this->GID_LIC_PSKEW=$this->PSKEW=$this->TRIAL_SKEW;
			 $this->GLH_CUST_NAME=$qd_ins['GLH_CUST_NAME'];
			 $this->GID_ORDER_NO=$this->ORDER_NO=$qd_ins['GID_ORDER_NO'];
			 $this->GID_INSTALL_ID=(int)$qd_ins['GID_INSTALL_ID'];
			 $this->GID_FULLFILLMENT_NO=$this->FULLFILLMENT_NO=(int)$qd_ins['GID_FULLFILLMENT_NO'];
			 $this->GID_EXPIRE_FOR=(int)$qd_ins['GID_EXPIRE_FOR'];
			 $this->GID_INSTALL_DATE=$qd_ins['GID_INSTALL_DATE'];
			 $this->GID_VALIDITY_DATE=$qd_ins['GID_VALIDITY_DATE'];
			 $this->GID_STATUS=$qd_ins['GID_STATUS'];
			 if(strtotime($this->GID_VALIDITY_DATE) < strtotime(date('Y-m-d'))){
			 	$this->GLC_ERROR_CODE="E016";
			 	return false;
			 }
			 /* if(!$this->IS_DEALER){
			 	$this->GLC_ERROR_CODE="M001";
			 	return false;
			 } */
		}
		return true;
	}
	
	
	/**
	 * @param string $err_code
	 * 
	 * @return void
	 */
	protected function give_error_message($err_code){
		$err_msg='';
		$order_fullfill_no = isset($this->parsed_xml['ORDER_NO'])?$this->parsed_xml['ORDER_NO']:'';
		switch ($err_code){
			case 'E000' : $err_msg='Empty Param'; break;
			case 'E001' : $err_msg='Request Details Empty'; break;
			case 'E002' : $err_msg='Mandatory Details Missing'.$this->GLC_ADDITIONAL_ERROR_MESSAGE ; break;			
			case 'E003' : $err_msg='Free Starter Registration Stopped. Please Activate 30 days trial license '; break;
			case 'E004' : $err_msg='For activation,the minimum version required is "'.$this->Minimum_Version.'" Please update the latest version and activate  ' ; break;
			case 'E005' : $err_msg='Not able to identify Version';break;
			case 'E007' : $err_msg='Installation status is Not Active. Please contact GOFRUGAL CST team';break;
			case 'E008' : 	if($this->WEB_REQUEST_TYPE==1) {
								$err_msg='Hard disk id mismatch. Please get Approval ';break;
							}else{
								$err_msg='Hard disk id mismatch. Please surrender your license and proceed with New Hard disk ';break;
							}
			case 'E009' : $err_msg='Subscription Period Expired ';break;
			case 'E010' : $err_msg='ALR Period Expired.Please contact 04461716171 for renewing the service. ';break;
			case 'E011' : $err_msg='Activate with Root Order Number ';break;
			case 'E006' : $err_msg='Cant Activate  30 days Subscription one more time in this System.';break;
			case 'E012' : $err_msg='OTP fails. Generate New OTP ';break;
			case 'E013' : $err_msg='OTP entered is incorrect';break;
			case 'E014' : $err_msg='Order Number is Wrong it is not licenseable ';break;
			case 'E015' : $err_msg='Your License is Restricted .Order in cancel status';break;
			case 'E016' : $err_msg="Dear Customer, Thanks for your interest. You have already used the product with the order number $this->ORDER_NO ".
									" and your trial license is  expired. ";break;
			case 'E017' : $err_msg='Please Use RayMedi Doctor for Registry Corrupt'; break; //RReason as 2
			case 'E018' : $err_msg='Please enter the Reinstall Reason in detail'; break;
			case 'E019' : $err_msg='Please Check Order Number and Product '; break;
			case 'E019.1' : $err_msg='Please Check Order Number and Product. Multipe record exists '; break;
			case 'E020' : $err_msg='Order Not Splitted'; break;
			case 'E021' : $err_msg='Product Mismatch'; break;
			case 'E022' : $err_msg='Skew Mismatch'; break;
			case 'E023' : $err_msg='Order No not available while generate Activation key'; break;
			case 'E024' : $err_msg='Not able to indentify skew / product group'; break;
			case 'E026' : $err_msg='Customer Id Wrong '; break;
			case 'E027' : $err_msg='Order Number is Wrong';break;
			case 'E028' : $err_msg='Product Selected Wrongly. '.$this->GLC_ADDITIONAL_ERROR_MESSAGE ; break;	
			case 'E029' : $err_msg='Fullfillment Number Greater than purchased Quantity '; break;
			case 'E030' : $err_msg='Order No Not available in our CRM '; break;
			case 'E031' : $err_msg='Fullfillment Number is Wrong '; break;
			case 'E032' : $err_msg='More than 1 installation details exists. Please contact GoFrugal support.'; break;
			case 'E033' : $err_msg='The mobile number entered for "Installed by" is not registered in GoFrugal Server(Refer GFT Team page).'.
									' Please enter the registered mobile number to continue. If you are not a GoFrugal Employee/Partner, leave that "Installed by" as empty.';break;
			case 'E034' : $err_msg='You are not a valid lead';break;
			case 'E036'	: $err_msg='Employee License of HQ is not allowed in this IP Address';break;
			case 'E037'	: $err_msg="The order number entered is currently in use on another machine. By using the same order number here, you will not be able to use the application hereafter on the old machine. ".
									"If it is found later that the same order number has been misused on two or more machines, the license of all the machines will be terminated";break;
			case 'E038' : $err_msg="You are a Gosecure customer and your data is synced to cloud from another machine. So usage in this machine is not allowed. If you really want to use in this machine, please do reinstallation so that you can restore your cloud data and continue";break;
			case 'E039' : $err_msg="Proprietor Contact Info Not Available in CRM. Contact GoFrugal to Update Your Contact";break;
						
			case 'TF01' : $err_msg='Technical Issue in Lead Generation ';break;
			case 'TF02' : $err_msg='Technical Issue in Order Generation';break;
			case 'TF03' : $err_msg='Technical Issue in OTP Generation';break;
			case 'TF04' : $err_msg='Technical Issue in checking OTP'; break;
			case 'TF05' : $err_msg='Technical Issue in Finding Trial Skew'; break;
			case 'TF06' : $err_msg='Technical Issue in identifing Active Trial Order'; break;
			case 'TF07' : $err_msg='Technical Issue Product key length Mismatch'; break;
			
			case 'OR01' : $err_msg='Order Number length Mismatch';break;
			case 'OR02' : $err_msg='Fullfillment Number is invalid';break;
			case 'PR01' : $err_msg='Product id length Mismatch';break;
			case 'PR02' : $err_msg='Product Code is Wrong';break;
			case 'PR03' : $err_msg='Product Code Not Available in SAM';break;
			case 'PR04' : $err_msg='Skew Not Applicable for license  Actiavation';break;
			case 'PR05' : $err_msg='Skew Not Available in SAM';break;
			case 'PR06' : $err_msg='Trial Skew Not Available For this Edition'; break;
			case 'PR07' : $err_msg='More than one Trial license Skew Available For this Edition'; break;
			case 'PR08' : $err_msg='Product Group is Wrong'; break;
			case 'PR09' : $err_msg='Product Group Not Available in SAM'; break;
			
			
			case 'PK01' : $err_msg='Order No mismatch in Product key';break;
			case 'PK02' : $err_msg='Hard disk id mismatch in Product key';break;
			case 'PK03' : $err_msg='Product code mismatch in Product Key';break;
			case 'PK04' : $err_msg='Product key length mismatch';break;
			
			case 'D001' : $err_msg="Invalid Dealer Id($this->DEALER_ID)";break;
			case 'D002' : $err_msg="You are not authorized to Gofrugal Dealership program.".
									" Please contact Gofrugal Partner Management team";break;
			case 'D003' : $err_msg="Access denied! Data mismatch in dealer information. Please contact your software vendor";break;
			
			case 'L001' : $err_msg='Local User Hard disk id not mapped in SAM';break;
			case 'L002' : $err_msg='Local User Email id / Mobile No. not mapped in SAM';break;
			case 'L003' : $err_msg='Local User License not configured in SAM Master. Check the Fullfillment Number';break;

			case 'LA01' : $err_msg='License Approval Error';break;
			case 'LA02' : $err_msg='Subscription License Approval Limit Exceeds';break;
			case 'LA03' : $err_msg='Subscription License Approval Expired. Get Approval to Activate';break;
			case 'LA04' : $err_msg='Technical issue in finding License Status';break;
			case 'LA05' : $err_msg="License Approval Period Expired";break;
			case 'LA06' : $err_msg="Dear Customer, You have already purchased a base product in this customer id. ".
			                       "So you are not allowed for Trial installation using this customer id. Kindly activate with the purchased order number";break;
			
			case 'DB01' : $err_msg='Data Insertion Error';break;
			
			case 'M001' : $err_msg='Dear Customer, Thanks for your interest. You have already used the product with the order number <<'.$this->ORDER_NO
									.'>>. So, in the installation wizard kindly select "I have order no...." option and then try';break;
			case 'M002' : $err_msg='You have given employee order number (Mobile no + Employee id) which is not valid. Please contact license team for more details.';break;

			default     : $err_msg='Cant Process the request. ['.$err_code.']';
				      break;
		}
		$get_incharge_emp = /*. (string[int]) .*/array();
		$get_incharge_emp[0] = $get_incharge_emp[1] = $contact_msg = '';
		if(in_array($err_code,array('E007','E009','E016','E035','M001','LA06'))){
			if($this->LEAD_CODE!=0){
				$get_incharge_emp = get_incharge_name_no("$this->LEAD_CODE",'');
			}else if( isset($this->parsed_xml['CUSTOMER_ID']) && ($this->parsed_xml['CUSTOMER_ID']!='') ){
			    $get_incharge_emp = get_incharge_name_no($this->parsed_xml['CUSTOMER_ID'],'');
			}
		}elseif (in_array($err_code, array('LA05'))){
			if(((int)$this->LICENSE_STATUS) <= 6) {
				$get_incharge_emp = get_incharge_name_no('',$this->ORDER_NO);
			}else{
				$get_incharge_emp = get_incharge_name_no("$this->LEAD_CODE",'');
			}
		}
		if($this->IS_DEALER){
			$contact_msg = "Contact your vendor to buy the License";
		}else if( ($get_incharge_emp[0]!='') && ($get_incharge_emp[1]!='') ){
			$contact_msg = ' Contact '.$get_incharge_emp[0].' in '.$get_incharge_emp[1];
		}
		$this->GLC_ERROR_MESSAGE=$err_msg.'. '.$contact_msg;
	}
	
	/**
	 * @param string $msg_code
	 * 
	 * @return string
	 */
	public function get_license_note_msg($msg_code){
		switch($msg_code){
			case 'MSG_01' : $msg_note='Dear customer, Our Lead Management team will contact you and extend your evaluation license period';break;
			case 'MSG_02' : $msg_note='Dear Customer, If you want to order,'.
									' Please contact Mr/Ms.'.$this->ASSIGNED_EMP.' in this number '.$this->ASSIGNED_EMP_NO.
									' . If you have already ordered and still you are getting Trial expiry message,'.
									' Please click the below link.';break;
			case 'MSG_03' : $msg_note='Dear customer, If you have already ordered and still you are getting Trial expiry message,'.
								' Please click the below link.';break;
			default : $msg_note='Unknown messgae';break;
		}
		return $msg_note;
	}
	
	/**
	 * @param string $xmlRaw
	 * @param string[int] $xmlFieldNames
	 * 
	 * @return string[string] 
	 */
	protected function give_parsed_xml($xmlRaw,$xmlFieldNames){
		$parsedXML=/*. (string[string]) .*/ array();
		foreach ($xmlFieldNames as $xmlField) {
			if(strpos($xmlRaw,"<$xmlField>")!==false){
				$parsedXML[$xmlField]=substr($xmlRaw,
					  strpos($xmlRaw,"<$xmlField>")+strlen("<$xmlField>"),
		       		  strpos($xmlRaw,"</$xmlField>")-strlen("<$xmlField>")
		       		  -strpos($xmlRaw,"<$xmlField>"));
			}
			foreach($parsedXML as $key=>$value){
				$parsedXML[$key]=trim($value);
			}
		}
		return $parsedXML;
    }
    
    /**
     * @param string $err_code
     * 
     * @return void
     */
    protected function generate_error_response($err_code){
		$this->give_error_message($err_code);
		$this->GLC_STATUS='F';
		$min_verion_specified_err='';
		if(!empty($this->Minimum_Version)){
			$min_verion_specified_err='<MINIMUM_VERSION>'.$this->Minimum_Version.'</MINIMUM_VERSION>';
		}
		if(!empty($this->Patch_download_link)){
			$min_verion_specified_err.='<PATCH_LINK>'.$this->Patch_download_link.'</PATCH_LINK>';
		}
		
		$min_verion_specified_err.='<ADD_ERROR_INFO>'.$this->GLC_ADDITIONAL_ERROR_MESSAGE.'</ADD_ERROR_INFO>';
		if($this->WEB_REQUEST_TYPE == 11){
			$this->GLC_RETURN_DATA='<?xml version="1.0" standalone="yes"?>' .
							'<TRIAL_REGISTRATION_RESPONSE>' .
							'<ERROR_CODE> '.$this->GLC_ERROR_CODE.'</ERROR_CODE>'.
							'<ERROR_MESSAGE>'.$this->GLC_ERROR_MESSAGE.'</ERROR_MESSAGE>'
							.$min_verion_specified_err	
							.'</TRIAL_REGISTRATION_RESPONSE>';
		}
		else if($this->WEB_REQUEST_TYPE == 14){
			$this->GLC_RETURN_DATA='<?xml version="1.0" standalone="yes"?>' .
							'<TRIAL_REGISTRATION_RESPONSE_WITH_OTP>' .
							'<ERROR_CODE> '.$this->GLC_ERROR_CODE.'</ERROR_CODE>'.
							'<ERROR_MESSAGE>'.$this->GLC_ERROR_MESSAGE.'</ERROR_MESSAGE>'
							.$min_verion_specified_err	
							.'</TRIAL_REGISTRATION_RESPONSE_WITH_OTP>';
		}
		else if( ($this->WEB_REQUEST_TYPE==12) || ($this->WEB_REQUEST_TYPE==27) ){
			$this->GLC_RETURN_DATA .= '<?xml version="1.0" standalone="yes"?><REQ_ACTIVATION_KEY_RESPONSE>';
			if($this->GLC_ERROR_CODE=='E037'){ //hardware mismatch
				$contact_info = contact_info_for_authentication($this->GID_LEAD_CODE);
				if($contact_info!=''){
					$this->GLC_RETURN_DATA .= "<NEEDOTP>YES</NEEDOTP>".
												"<CONTACTS>$contact_info</CONTACTS>".
												"<MESSAGE>$this->GLC_ERROR_MESSAGE</MESSAGE>";
				}else{
					$this->GLC_ERROR_CODE = 'E039';
					$this->give_error_message($this->GLC_ERROR_CODE);
					$this->GLC_RETURN_DATA .='<ERROR_CODE>'.$this->GLC_ERROR_CODE.'</ERROR_CODE>' .
											'<ERROR_MESSAGE>'.$this->GLC_ERROR_MESSAGE.'</ERROR_MESSAGE>';
				}
			}else{
				$this->GLC_RETURN_DATA .= '<ERROR_CODE>'.$this->GLC_ERROR_CODE.'</ERROR_CODE>' .
											'<ERROR_MESSAGE>'.$this->GLC_ERROR_MESSAGE.'</ERROR_MESSAGE>'.
											$min_verion_specified_err;
			}
			$this->GLC_RETURN_DATA .= '</REQ_ACTIVATION_KEY_RESPONSE>';
		}else if($this->WEB_REQUEST_TYPE==1){
			$this->GLC_RETURN_DATA='<?xml version="1.0" standalone="yes"?>' .
					'<ACTIVATION_KEY>' .
					'<ERROR_CODE>'.$this->GLC_ERROR_CODE.'</ERROR_CODE>' .
					'<ER>'.$this->GLC_ERROR_MESSAGE .(!empty($this->Patch_download_link)?$this->Patch_download_link:'').'</ER>'.
					$min_verion_specified_err
					.'</ACTIVATION_KEY>';
		}else if($this->WEB_REQUEST_TYPE==13){
			/* Commented new message format
			$this->GLC_RETURN_DATA='<?xml version="1.0" standalone="yes"?>' .
						'<ACTIVATION_KEY>' .
						'<ERROR_CODE>'.$this->GLC_ERROR_CODE.'</ERROR_CODE>' .
						'<ER>'.$this->GLC_ERROR_MESSAGE .(!empty($this->Patch_download_link)?$this->Patch_download_link:'').'</ER>'.
						$min_verion_specified_err 
						.'</ACTIVATION_KEY>';
			 */
			//OLD STARTER_REGISTRATION error message.
			$this->GLC_RETURN_DATA='<?xml version="1.0" standalone="yes"?><STARTER_REGISTRATION><ORDER_NO>2</ORDER_NO><CUSTOMER_ID>2</CUSTOMER_ID><PRODUCT_ID>500070ST</PRODUCT_ID><PRODUCT_KEY>XM0H012234H0H1177755E8CG</PRODUCT_KEY><A_KEY>'.$this->GLC_ERROR_MESSAGE.'</A_KEY></STARTER_REGISTRATION>';

		}else {
			throw new ErrorException("Undefined Web Request Type");
		}
		
		if( isset($this->parsed_xml['PERIODIC_CHECK']) && $this->parsed_xml['PERIODIC_CHECK']=='YES' ){
			$this->update_periodic_check_log('Invalid');
		}
	}
	
	/**
	 * @return void
	 */
	public function get_ref_serial_no() {
		$quer = " select GOP_REF_SERIAL_NO from gft_order_product_dtl ".
				" where GOP_ORDER_NO='$this->ORDER_NO' and GOP_FULLFILLMENT_NO='$this->FULLFILLMENT_NO' ";
		$res = execute_my_query($quer);
		if($row = mysqli_fetch_array($res)){
			$this->GID_REF_SERIAL_NO=$row['GOP_REF_SERIAL_NO'];
		}
	}		
	
	/**
	 * @return void
	 */
	public function print_license_response(){
		/* web request 1 is old license process */
		if(!empty($this->ACTIVATION_KEY) and ($this->WEB_REQUEST_TYPE==1 or 
		(isset($this->parsed_xml['SEND_ACTIVATION_KEY']) and $this->parsed_xml['SEND_ACTIVATION_KEY']=='Y' ) ) ){
			$this->get_ref_serial_no();
			$this->update_license_dtl();
			$this->update_lic_approved_log_details();			
		}
		else if(empty($this->GLC_RETURN_DATA)){	
			$this->generate_error_response($this->GLC_ERROR_CODE);
		}
		global $secret;
		$this->GLC_RETURN_ENCRYPTED_DATA='<AK>'.lic_encrypt($this->GLC_RETURN_DATA,$secret).'</AK>';
				
		$insert_lic_request=/*. (mixed[string]) .*/array();	
		$insert_lic_request['GLC_REQUEST_ID']='';	
		$insert_lic_request['GLC_FROM_ONLINE']='Y';
		$insert_lic_request['GLC_FROM_WEB']='Y';		
		$insert_lic_request['GLC_ONLINE_CONTENT']=(!empty($this->GLC_ONLINE_CONTENT)?$this->GLC_ONLINE_CONTENT:'');				
		$insert_lic_request['GLC_REQUEST_TIME']=date('Y-m-d H:i:s');
		$insert_lic_request['GLC_RETURN_DATA']=mysqli_real_escape_string_wrapper($this->GLC_RETURN_DATA);
		$insert_lic_request['GLC_RETURN_ENCRYPTED_DATA']=$this->GLC_RETURN_ENCRYPTED_DATA;			
		$insert_lic_request['GLC_LEAD_CODE']=(!empty($this->LEAD_CODE)?$this->LEAD_CODE:0);
		if($this->LOCAL_USER=='Y'){
			$insert_lic_request['GLC_LEAD_CODE']=$this->LOCAL_USER_ID;
		}		
		$insert_lic_request['GLC_IP_ADDRESS	']=$this->IP_ADDRESS;
		$insert_lic_request['GLC_HDD_KEY']=(!empty($this->parsed_xml['HDD_KEY'])?$this->parsed_xml['HDD_KEY']:'');
		$insert_lic_request['GLC_EXPIRY_DATE']=($this->annuity_valid!='')?$this->annuity_valid:$this->GID_VALIDITY_DATE;
		if(isset($this->parsed_xml['OFFLINE']) and $this->parsed_xml['OFFLINE']=="YES") {
			$insert_lic_request['GLC_EMP_ID']=(!empty($this->parsed_xml['EMP_ID'])?$this->parsed_xml['EMP_ID']:'');
		}else {
			$insert_lic_request['GLC_EMP_ID']=(!empty($this->USERID)?$this->USERID:'');
		}	
		
		$insert_lic_request['GLC_REQUEST_PURPOSE_ID']=(!empty($this->parsed_xml['OFFLINE'])?'17':$this->WEB_REQUEST_TYPE);	
		$insert_lic_request['GLC_PROCESSING_TIME']=getDeltaTime();
		$insert_lic_request['GLC_DECRYPTED_CONTENT']=mysqli_real_escape_string_wrapper($this->GLC_DECRYPTED_CONTENT);			
		$insert_lic_request['GLC_STATUS']=$this->GLC_STATUS;
		$insert_lic_request['GLC_ERROR_CODE']=(!empty($this->GLC_ERROR_CODE)?$this->GLC_ERROR_CODE:'');
		$insert_lic_request['GLC_ERROR_MESSAGE']=(!empty($this->GLC_ERROR_MESSAGE)?$this->GLC_ERROR_MESSAGE:'');	
		array_update_tables_common($insert_lic_request,'gft_lic_request',null,null,SALES_DUMMY_ID,null,null,$insert_lic_request);
		$this->ONLINE_REQUEST_ID=mysqli_insert_id_wrapper();
		
		if(!empty($this->GLC_ONLINE_CONTENT)){
			$insert_arr=/*. (mixed[string]) .*/ array();
			$insert_arr['LIC_LOG_BUSINESSNAME']=mysqli_real_escape_string_wrapper(isset($this->parsed_xml['LICENCED_TO'])?$this->parsed_xml['LICENCED_TO']:'');
			$insert_arr['LIC_LOG_ORDERNO']=(!empty($this->parsed_xml['ORDER_NO'])?$this->parsed_xml['ORDER_NO']:'');
			$insert_arr['LIC_LOG_IP_ADDRESS']=$this->IP_ADDRESS;
			$insert_arr['LIC_LOG_HDD_KEY']=(!empty($this->parsed_xml['HDD_KEY'])?$this->parsed_xml['HDD_KEY']:'');
			$insert_arr['LIC_LOG_HDD']=(!empty($this->ENCRYPED_HDD_KEY)?$this->ENCRYPED_HDD_KEY:'');
			$insert_arr['LIC_HKEY']=(!empty($this->parsed_xml['HKEY'])?$this->parsed_xml['HKEY']:'');
			$insert_arr['LIC_LOG_PRODUCTID']=(!empty($this->parsed_xml['PRODUCT_ID'])?$this->parsed_xml['PRODUCT_ID']:'');
			$insert_arr['LIC_LOG_PRODUCTKEY']=mysqli_real_escape_string_wrapper((!empty($this->PRODUCT_KEY)?$this->PRODUCT_KEY:''));
			$insert_arr['LIC_LOG_ACT_KEY']=(!empty($this->ACTIVATION_KEY)?$this->ACTIVATION_KEY:'');
			$insert_arr['LIC_LOG_NCLIENTS_ACTIVATED']=(!empty($this->GID_NO_CLIENTS)?$this->GID_NO_CLIENTS:0);
			$insert_arr['LIC_LOG_NCOMPANYS_ACTIVATED']=(isset($this->GID_NO_COMPANYS)?$this->GID_NO_COMPANYS:0);
			$insert_arr['LIC_LOG_INSTALL_ID']=(isset($this->GID_INSTALL_ID)?$this->GID_INSTALL_ID:0);
			$insert_arr['LIC_VALIDITY_DATE']=(isset($this->GID_VALIDITY_DATE)?$this->GID_VALIDITY_DATE:'');
			$insert_arr['LIC_EXPIRE_FOR']=(isset($this->GID_EXPIRE_FOR)?$this->GID_EXPIRE_FOR:'');
			$insert_arr['LIC_LOG_DATETIME']=date('Y-m-d H:i:s');
			$insert_arr['LIC_LOCAL_USER']=$this->LOCAL_USER;
			$insert_arr['LIC_LOCAL_USER_ID']=(isset($this->LOCAL_USER_ID)?$this->LOCAL_USER_ID:0);
			$insert_arr['LIC_LOG_STATUS']=$this->GLC_STATUS;
			$insert_arr['LIC_LEAD_CODE']=(isset($this->LEAD_CODE)?$this->LEAD_CODE:0);
			$insert_arr['LIC_CONNECTION_TYPE']=1;
			$insert_arr['LIC_FULLFILLMENT_NO']=(isset($this->FULLFILLMENT_NO)?$this->FULLFILLMENT_NO:0);
			$insert_arr['LIC_LOG_USERID']=$this->USERID;
			$insert_arr['LIC_ERROR_CODE']=(isset($this->GLC_ERROR_CODE)?$this->GLC_ERROR_CODE:'');
			$insert_arr['LIC_LOG_COMMENT']=(isset($this->GLC_ERROR_MESSAGE)?$this->GLC_ERROR_MESSAGE:'');
			//$insert_arr['LIC_ONLINE_REQUEST_ID']=$this->ONLINE_REQUEST_ID;
			//array_update_tables_common($insert_arr,'gft_lic_log',null,null,SALES_DUMMY_ID,null,null,$insert_arr);
		}
		
		if($this->WEB_REQUEST_TYPE==11 and $this->OTP!=''){
			$this->send_mail_sms_trial_otp();
		}
		header('Content-Type: text/xml');
		//print $this->GLC_RETURN_DATA;
		if(isset($this->parsed_xml['OFFLINE']) && $this->parsed_xml['OFFLINE']=="YES") {
			print $this->GLC_RETURN_DATA;
		}else if($this->IS_OFFLINE){
			if($this->GLC_ERROR_CODE!=''){
				$resp_arr['status'] = "error";
				$resp_arr['message'] = $this->GLC_ERROR_MESSAGE;
				if($this->GLC_ERROR_CODE=='E037'){ //hardware mismatch
					$contact_info = contact_info_for_authentication($this->GID_LEAD_CODE);
					if($contact_info!=''){
						$resp_arr['status'] = "harddisk_mismatch";
						$resp_arr['message'] = $this->GLC_ERROR_MESSAGE." Do you want to Reinstall?";
						$resp_arr['contacts'] = explode(',', $contact_info);
						$resp_arr['order_no'] = $this->ORDER_NO.substr("0000".$this->FULLFILLMENT_NO, -4);
						$resp_arr['hkey']	  = $this->HKEY;
						$resp_arr['product_code'] = $this->GID_LIC_PCODE;
					}else{
						$this->GLC_ERROR_CODE = 'E039';
						$this->give_error_message($this->GLC_ERROR_CODE);
						$resp_arr['status'] = "error";
						$resp_arr['message'] = $this->GLC_ERROR_MESSAGE;
					}
				}
			}else{
				$validity_date = ($this->annuity_valid!='')?$this->annuity_valid:$this->GID_VALIDITY_DATE;
				$offline_data['dukaan_order_no']= substr((string)str_replace("-", "", $this->ORDER_NO_SPLITED), -12);
				$offline_data['customer_id'] 	= substr("00000000".$this->GID_LEAD_CODE, -8);
				$offline_data['expiry_date'] 	= date('ymd',strtotime($validity_date));
				$offline_data['noc']	 		= substr("00".$this->GID_NO_CLIENTS, -2);
				$offline_data['lic_type'] 		= $this->GPM_LICENSE_TYPE;
				$offline_data['mode'] 			= rand(1,3);
				$offline_data['activation_key'] = $this->ACTIVATION_KEY;
				$resp_data['label'] = "License Key";
				$resp_data['value'] = get_offline_key($offline_data);
				$resp_arr['status'] = "success";
				$resp_arr['message'][] = $resp_data;
			}
			header('Content-Type: application/json');
			print json_encode($resp_arr);
		}else {
			print $this->GLC_RETURN_ENCRYPTED_DATA;
		}
	}

	/**
	 * @param int $pcode	 
	 * @param string $version
	 * @param string $product_group
	 * @param string $type
	 * 
	 * @return boolean
	 */
	public function check_above_minimum_version($pcode,$version,$product_group,$type){
		/* Note:
		 *  1. Version with dots between major,minor,patch,exec
		 *  2. Version wtihout dots
		 *  3. in last segment with 'dot' and with 'underscore'
		 *  4. in last segment prefix with RC ...suffix with RC
		 */
		if($pcode==0 or $version==='' or $product_group === ''){
			return false;
		}
		$this->VERSION = $version;
		$chk_query	=" select GPV_VERSION from gft_product_version_master where gpv_product_code=$pcode and gpv_version_family='$product_group' ".
					 " and (GPV_VERSION='$version' or concat(GPV_MAJOR_VERSION,GPV_MINOR_VERSION,GPV_PATCH_VERSION,GPV_EXE_VERSION)='$version') ";
		$chk_res = execute_my_query($chk_query);
		if($data1 = mysqli_fetch_array($chk_res)){
			 $this->VERSION=($data1['GPV_VERSION']!=''?$data1['GPV_VERSION']:$version);
		}
		if( ($this->LEAD_TYPE=='3') || ($this->LEAD_TYPE=='13') || ($this->LEAD_TYPE=='8' && $this->LOCAL_USER=='Y') ){  //skip minimum version for Corporate customers and gft(local user)
			return true;
		}
		$query = " SELECT GPV_VERSION,GPV_ALIAS_VERSION,GPV_DOWNLOAD_HLINKPH FROM ".
				" gft_product_family_master pfm join gft_product_group_master pg on (gpg_product_family_code=GPM_HEAD_FAMILY) ".
				" left join gft_product_version_master vm1 on (gpg_product_family_code=vm1.gpv_product_code and gpg_skew=vm1.gpv_version_family) ". 
				" where gpm_product_code=$pcode and gpg_skew='$product_group' and GPV_IS_MINIMUM_VERSION=1 ";
		if($type=='lowest'){
			$query .= " order by GPV_RELEASE_DATE limit 1 ";
		}else if($type=='highest'){
			$query .= " order by GPV_RELEASE_DATE desc limit 1 ";
		}elseif ($type=='nearest'){
			$query .= " order by GPV_RELEASE_DATE ";
		}
		$result=execute_my_query($query);
		$num_rows = mysqli_num_rows($result);
		if($num_rows==1){ //if only one
				$qd=mysqli_fetch_array($result);
				$min_version = $qd['GPV_VERSION'];
				$alias_version = $qd['GPV_ALIAS_VERSION'];
				if($qd['GPV_DOWNLOAD_HLINKPH']!=''){
					$lic_vcheck=license_version_check_minimum($min_version,$this->VERSION);
					if(!$lic_vcheck){
						$this->Minimum_Version=($alias_version!='')?$alias_version:$min_version;
						$this->Patch_download_link=$qd['GPV_DOWNLOAD_HLINKPH'];
						$this->GLC_ERROR_CODE='E004';
						return false;
					}							
					return true;						
				}
		}else if($num_rows > 1){ //if more than one, need to find the nearest
			while($row1 = mysqli_fetch_array($result)){
				$min_version = $row1['GPV_VERSION'];
				$alias_version = $qd['GPV_ALIAS_VERSION'];
				$lic_vcheck = license_version_check_minimum($min_version, $this->VERSION);
				if(!$lic_vcheck){
					$this->Minimum_Version=($alias_version!='')?$alias_version:$min_version;
					$this->Patch_download_link=$row1['GPV_DOWNLOAD_HLINKPH'];
					$this->GLC_ERROR_CODE='E004';
					return false;
				}
			}
			return true;
		}
		//$this->GLC_ERROR_CODE='E005';
		return true;
	}
	
	/**
	 * @return boolean
	 */
	protected function get_install_id_license_ifexits(){
		if(empty($this->ORDER_NO) or empty($this->PCODE) or empty($this->FULLFILLMENT_NO)){
			return false;
		}
		
		$unin_query=" select GID_STATUS from gft_install_dtl_new join gft_product_family_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE) ".
					" WHERE GID_ORDER_NO='".$this->ORDER_NO."' and GID_FULLFILLMENT_NO=$this->FULLFILLMENT_NO ".
					" and (GID_LIC_PCODE=$this->PCODE or GID_PRODUCT_CODE=$this->PCODE or GPM_HEAD_FAMILY=$this->PCODE) ";
		$unin_res=execute_my_query($unin_query);
		if(mysqli_num_rows($unin_res) > 0){
			while($unin_data=mysqli_fetch_array($unin_res)){
				$status[]=$unin_data['GID_STATUS'];
			}
			$status=array_unique($status);
			if( (count($status)==1) and ($status[0]=='U') ){
				$this->GLC_ERROR_CODE='E007';
				return false;
			}
		}
		$query="SELECT GOD_ORDER_NO,GLH_CUST_NAME,GOD_ORDER_STATUS,GOD_ORDER_DATE,ohem.GEM_EMP_ID as ORDERED_EMP_ID,ohem.GEM_EMP_NAME as ORDERED_BY,GID_INSTALL_ID," .
				" GID_ORDER_NO,GID_FULLFILLMENT_NO,GID_LEAD_CODE,GID_INSTALL_DATE,GID_NO_CLIENTS,GID_NO_COMPANYS,GID_SYS_HKEY, GID_SYS_HDD_ID," .
				" GID_LIC_HARD_DISK_ID,GID_NO_CLIENTS,GID_NO_COMPANYS,GID_EXPIRE_FOR,pm1.GPM_LICENSE_TYPE,GPL_LICENSE_TYPE_NAME," .
				" GID_VALIDITY_DATE,GID_HEAD_OF_FAMILY,ptm1.GPT_TYPE_NAME as EDITION,ptm1.GPT_TYPE_NAME as EEDITION," .
				" GID_STATUS,GID_LIC_PCODE,GID_LIC_PSKEW,pm1.GPM_SKEW_DESC,GID_NCLIENTS_UPDATED_IN_CUSTPLACE,GID_NCOMPANYS_UPDATED_IN_CUSTPLACE," .
				" GID_PRODUCT_CODE,GID_PRODUCT_SKEW,pm2.GPM_SKEW_DESC AS 'Earlier_Skew_Desc'," .
				" if(pm1.GPM_REFERER_SKEW='',pm1.GPM_PRODUCT_SKEW,pm1.GPM_REFERER_SKEW) AS GPM_REFERER_SKEW ," .
				" if(pm2.GPM_REFERER_SKEW='',pm2.GPM_PRODUCT_SKEW,pm2.GPM_REFERER_SKEW) AS EGPM_REFERER_SKEW," .
				" GID_CURRENT_VERSION,GID_SUBSCRIPTION_STATUS,GID_UPGRADATION_DONE , " .
				" dtl.GAR_ID ,GLS_ID, em.GEM_EMP_NAME AS 'APPROVED_BY',GAR_REASON ,GAR_APPROVED_FLAG ,GAR_NOTE,GAR_APPROVAL_COMMENT " .
				" FROM gft_install_dtl_new g " .
				" left join gft_lead_hdr lh on (glh_lead_code=gid_lead_code) ".
				" left join gft_product_master pm1 on (pm1.GPM_PRODUCT_CODE=GID_LIC_PCODE  and pm1.GPM_PRODUCT_SKEW=GID_LIC_PSKEW )" .
				" left join gft_product_type_master ptm1 on (ptm1.GPT_TYPE_ID=pm1.GPM_PRODUCT_TYPE) " .
				" left join gft_product_family_master pfm1 on (pfm1.GPM_PRODUCT_CODE=pm1.GPM_PRODUCT_CODE)".
				" left join gft_product_group_master pg1 on (pg1.gpg_product_family_code=pfm1.gpm_head_family and pg1.gpg_skew=substr(pm1.GPM_PRODUCT_SKEW,1,4)) ".
				" left join gft_product_license_type plt on (pm1.GPM_LICENSE_TYPE=GPL_LICENSE_TYPE_ID )".
				" left join gft_product_master pm2 on (pm2.GPM_PRODUCT_CODE=GID_PRODUCT_CODE and pm2.GPM_PRODUCT_SKEW=GID_PRODUCT_SKEW )".
				" left join gft_product_type_master ptm2 on (ptm2.GPT_TYPE_ID=pm2.GPM_PRODUCT_TYPE) " .
				" left join gft_product_family_master pfm2 on (pfm2.GPM_PRODUCT_CODE=pm2.GPM_PRODUCT_CODE)".
				" left join gft_product_group_master pg2 on (pg2.gpg_product_family_code=pfm2.gpm_head_family and pg2.gpg_skew=substr(pm2.GPM_PRODUCT_SKEW,1,4)) ".
				" left join gft_order_hdr oh on (god_order_no=gid_order_no) " .
				" left join gft_emp_master ohem on (ohem.gem_emp_id=GOD_EMP_ID) ".
				" left join gft_approved_reinstallation_dtl dtl on (GAR_INSTALL_ID_REFF=GID_INSTALL_ID AND GAR_STATUS='P') " .
				" left join gft_approved_reinstallation hdr on (hdr.GAR_ID=dtl.GAR_ID and gar_lead_code=gid_lead_code) " .
				" left join gft_emp_master em on (em.gem_emp_id=GAR_APPROVED_BY) ".
				" left join gft_lic_surrender on (GLS_ORDER_WITH_FILL_NO='".$this->ORDER_NO.substr('0000'.$this->FULLFILLMENT_NO, -4)."' and GLS_CODE_STATUS='A' ".
				" and substr(GID_LIC_PSKEW,1,6)=GLS_PRODUCT_SKEW and GID_LIC_PCODE=GLS_PRODUCT_CODE) ".				
				" WHERE GID_ORDER_NO='".$this->ORDER_NO."' and GID_FULLFILLMENT_NO=$this->FULLFILLMENT_NO ".
				" and (GID_LIC_PCODE=$this->PCODE or GID_PRODUCT_CODE=$this->PCODE or pfm1.GPM_HEAD_FAMILY=$this->PCODE) and (GID_STATUS='A' or GID_STATUS='S')"; //?????
		//in new process only head of family check is possible . 
		
		if(!empty($this->PSKEW)){		
			$query.=" and (pm1.GPM_PRODUCT_SKEW='$this->PSKEW' or pm1.GPM_REFERER_SKEW='$this->PSKEW' " .
				" or pm2.GPM_PRODUCT_SKEW='$this->PSKEW' or pm2.GPM_REFERER_SKEW='$this->PSKEW' ) ";
		}else if($this->PRODUCT_GROUP!==''){
			$query.=" and (pg1.gpg_skew='$this->PRODUCT_GROUP' or pg2.gpg_skew='$this->PRODUCT_GROUP') ";
		}		
		
		if($this->CUSTOM_GROUP!=''){
			$query.=" and pm1.GPM_PRODUCT_SKEW like '%".$this->CUSTOM_GROUP."' ";
		}

		$this->GID_INSTALL_DATE=date('Y-m-d'); //Default value.

		$result=execute_my_query($query); 

		$num_rows=mysqli_num_rows($result);


	  	if($num_rows==0){
			return false;
	  	}else if($num_rows==1){
			$data=mysqli_fetch_array($result);
			$this->GLH_CUST_NAME=$data['GLH_CUST_NAME'];
			$this->GID_INSTALL_ID=(int)$data['GID_INSTALL_ID'];
			$this->GID_ORDER_NO=$data['GID_ORDER_NO'];
			$this->GID_FULLFILLMENT_NO=(int)$data['GID_FULLFILLMENT_NO'];
			$this->LEAD_CODE=$this->GID_LEAD_CODE=(int)$data['GID_LEAD_CODE'];
			$this->GID_INSTALL_DATE=($data['GID_INSTALL_DATE']!=''?$data['GID_INSTALL_DATE']:date('Y-m-d'));
			$this->GID_VALIDITY_DATE=$data['GID_VALIDITY_DATE'];
			$this->GID_STATUS=$data['GID_STATUS'];
			$this->GID_CURRENT_VERSION=$data['GID_CURRENT_VERSION'];
			$this->GID_SUBSCRIPTION_STATUS=$data['GID_SUBSCRIPTION_STATUS'];
			$this->GID_UPGRADATION_DONE=$data['GID_UPGRADATION_DONE'];
			$this->GAR_ID=(int)$data['GAR_ID'];
			$this->GLS_ID=(int)$data['GLS_ID'];
			$this->GAR_APPROVED_BY=$data['APPROVED_BY'];
			$this->GAR_REASON=(int)$data['GAR_REASON'];
			$this->GAR_APPROVED_FLAG=$data['GAR_APPROVED_FLAG'];
			$this->GAR_NOTE=$data['GAR_NOTE'];
			$this->GAR_APPROVAL_COMMENT=$data['GAR_APPROVAL_COMMENT'];
			$this->GOD_ORDER_DATE=$data['GOD_ORDER_DATE'];		   
		    $this->GOD_ORDERED_BY=$data['ORDERED_BY'];
		    $this->ORDERED_EMP_ID=(int)$data['ORDERED_EMP_ID'];
		    $this->EDITION_TYPE_NAME=$data['EDITION'];
		   	if($data['GID_STATUS']=='U'){
				$this->GLC_ERROR_CODE='E007';
				return false;
			/* commenting below lines as this creates problem whie upgradation RPOS to RPOS VSS
			    }else if($this->PCODE === (int)$data['GID_PRODUCT_CODE'] and (int)$data['GID_PRODUCT_CODE']!==(int)$data['GID_LIC_PCODE']){
				$this->GID_LIC_PCODE=(int)$data['GID_PRODUCT_CODE'];
				$this->GID_LIC_PSKEW=$data['GID_PRODUCT_SKEW'];
				$this->SKEW_DESC=$data['Earlier_Skew_Desc'];
				$this->PRODUCT_ID=$data['GID_PRODUCT_CODE'].(string)str_replace('.','',$data['EGPM_REFERER_SKEW']);
				$this->GID_NO_CLIENTS=0;
				$this->GID_NO_COMPANYS=0;			
				$this->GID_EXPIRE_FOR=1;
				$this->GPM_LICENSE_TYPE=1;
				$this->LICENSE_TYPE_NAME='Perpetual';
				$this->GID_VALIDITY_DATE=$data['GID_VALIDITY_DATE'];
				$this->EDITION_TYPE_NAME=$data['EDITION'];
				
				return true;				
			 */}else{
				//commented because pcode contains family code.
				//$this->PRODUCT_ID=$this->PCODE.(string)str_replace('.','',$data['GPM_REFERER_SKEW']); 
				$this->PRODUCT_ID=$data['GID_LIC_PCODE'].(string)str_replace('.','',$data['GPM_REFERER_SKEW']);
				$this->GID_NO_CLIENTS=(int)$data['GID_NO_CLIENTS'];  // other products .. Users
				$this->GID_NO_COMPANYS=(int)$data['GID_NO_COMPANYS'];//DE usage
				$this->GID_NCLIENTS_UPDATED_IN_CUSTPLACE=(int)$data['GID_NCLIENTS_UPDATED_IN_CUSTPLACE'];
				$this->GID_NCOMPANYS_UPDATED_IN_CUSTPLACE=(int)$data['GID_NCOMPANYS_UPDATED_IN_CUSTPLACE'];
				$this->GID_LIC_PCODE=(int)$data['GID_LIC_PCODE'];
				$this->GID_LIC_PSKEW=$data['GID_LIC_PSKEW'];
				$this->SKEW_DESC=$data['GPM_SKEW_DESC'];
				$this->GID_SYS_HDD_ID=$data['GID_SYS_HDD_ID'];
				$this->GID_SYS_HKEY=$data['GID_SYS_HKEY'];
				$this->GID_LIC_HARD_DISK_ID=substr($data['GID_LIC_HARD_DISK_ID'],0,3);
				$this->GID_EXPIRE_FOR=(int)$data['GID_EXPIRE_FOR'];
				$this->GPM_LICENSE_TYPE=(int)$data['GPM_LICENSE_TYPE'];
				$this->LICENSE_TYPE_NAME=$data['GPL_LICENSE_TYPE_NAME'];
				$validity_date_timestamp = strtotime($data['GID_VALIDITY_DATE']);
				$today_date_timestamp = strtotime(date('Y-m-d'));
				$this->GID_VALIDITY_DATE=$data['GID_VALIDITY_DATE'];
				if($this->IS_DFT_ORDER_NO_FOR_POS && ($validity_date_timestamp > $today_date_timestamp) ){
					$this->GID_VALIDITY_DATE = add_date(date('Y-m-d'), 5);
				}
				$reinstall_prepared = get_reinstall_prepared_time($this->GID_INSTALL_ID);
				if($reinstall_prepared!=''){
					$reinstall_date = date('Y-m-d',strtotime($reinstall_prepared));
					$extended_date = add_date($reinstall_date, 5);
					$ext_timestamp = strtotime($extended_date);
					if($ext_timestamp < strtotime(date('Y-m-d'))){
						//update to inactive as it is expired 
						$upd_que=" update gft_reinstall_prepare_dtl set GRP_PREPARE_STATUS=0,GRP_UPDATED_DATETIME=now() ".
								 " where GRP_INSTALL_ID='$this->GID_INSTALL_ID' and GRP_PREPARE_STATUS=1 ";
						$upd_res = execute_my_query($upd_que);
					}else if(strtotime($this->GID_VALIDITY_DATE) >= $ext_timestamp){
						$this->GID_VALIDITY_DATE = $extended_date;
						$this->GPM_LICENSE_TYPE	 = 2;
						$this->LICENSE_TYPE_NAME = 'SUBSCRIPTION';
					}
				}
				
				
				return true;
			}							
		}else{
			//More than 1 row retuns.
			
			$this->GLC_ERROR_CODE='E032';
			return false;
		}
	}
/**
 * @return boolean
 */
protected function check_local_user(){
	$allowed_ip = explode(',',get_samee_const('LOCAL_IP_ADDRESS_SETUPWIZARD'));
	$parsed_hdd_key = isset($this->parsed_xml['HDD_KEY'])?$this->parsed_xml['HDD_KEY']:'';
	if($this->IS_DEALER){
		$hdd_que="select CGI_EMP_ID from gft_cp_info where CGI_EMP_ID='$this->DEALER_ID' and CGI_DEALER_UUID='$this->HKEY' ";
	}else{
		$hdd_que="select LIC_HDD_LU from gft_lic_localuser where LIC_STATUS_LU='A' and LIC_HDD_LU='$parsed_hdd_key'";
	}
	$hdd_rows=mysqli_num_rows(execute_my_query($hdd_que));
	if($hdd_rows==0){
	    $hdd_rows = mysqli_num_rows(execute_my_query("select GEM_EMP_ID from gft_emp_master where GEM_STATUS='A' and GEM_LAPTOP_HARDDISK_ID='$parsed_hdd_key'"));
	}
	if(!empty($this->ORDER_NO)){
		$mobile_que="select GEM_EMP_ID ,GEM_EMP_NAME from gft_emp_master where GEM_STATUS='A' and concat(substr(gem_mobile,-10),substr(concat('00000',gem_emp_id),-5))='".$this->ORDER_NO."' ";
		$mobile_res=execute_my_query($mobile_que);
		$mobile_rows=mysqli_num_rows($mobile_res);
		if( ($mobile_rows>0) && (in_array($this->IP_ADDRESS, $allowed_ip) || ($hdd_rows>0)) ){
			$qdata = mysqli_fetch_array($mobile_res);
			$this->LOCAL_USER = 'Y';
			$this->GID_ORDER_NO=$this->ORDER_NO;
			$this->GID_FULLFILLMENT_NO=$this->FULLFILLMENT_NO;
			$prod_id_arr=get_product_id($this->PCODE,$this->PRODUCT_GROUP,$this->FULLFILLMENT_NO);
			$prod_id = isset($prod_id_arr[0])?$prod_id_arr[0]:'';
			$no_of_clients = isset($prod_id_arr[1])?$prod_id_arr[1]:'0';
			if(strlen($prod_id)==8) {
				$this->GID_LIC_PCODE=(int)substr($prod_id,0,3);
				$this->GID_LIC_PSKEW=$this->PSKEW=substr($prod_id,3,2).'.'.substr($prod_id,5,3);
				$this->PRODUCT_ID=$prod_id;
				$this->GID_NO_CLIENTS=(int)$no_of_clients;
			}else{
				$this->GLC_ERROR_CODE='L003';
				return false;
			}
			$this->GID_NO_COMPANYS=0;
			$this->GID_EXPIRE_FOR=2;
			$this->GPM_LICENSE_TYPE=2;
			$this->LICENSE_TYPE_NAME='SUBSCRIPTION';
			$this->GLH_CUST_NAME=$qdata['GEM_EMP_NAME'];
			$this->LOCAL_USER_ID=(int)$qdata['GEM_EMP_ID'];
			$this->LOCAL_USER_NAME=$qdata['GEM_EMP_NAME'];
			return true;
		}
	}else if($this->IS_DEALER){
		$chk_que=" select GEM_EMP_ID,GEM_EMP_NAME,GEM_MOBILE from gft_cp_info join gft_emp_master on (GEM_EMP_ID=CGI_EMP_ID) ".
				 " where CGI_EMP_ID='$this->DEALER_ID' and CGI_DEALER_UUID='$this->HKEY' ";
		$chk_res = execute_my_query($chk_que);
		if($row1 = mysqli_fetch_array($chk_res)){
			$this->LOCAL_USER = 'Y';
			$this->GID_ORDER_NO = $this->ORDER_NO = substr($row1['GEM_MOBILE'],-10).substr("00000".$row1['GEM_EMP_ID'], -5);
			$this->GID_FULLFILLMENT_NO = $this->FULLFILLMENT_NO = '1';
			$this->GLH_CUST_NAME = $this->LOCAL_USER_NAME = $row1['GEM_EMP_NAME'];
			$prod_id_arr	= get_product_id($this->PCODE,$this->PRODUCT_GROUP,$this->FULLFILLMENT_NO);
			$prod_id 		= isset($prod_id_arr[0])?$prod_id_arr[0]:'';
			$no_of_clients 	= isset($prod_id_arr[1])?$prod_id_arr[1]:'0';
			if(strlen($prod_id)==8){
				$this->GID_LIC_PCODE	= (int)substr($prod_id,0,3);
				$this->GID_LIC_PSKEW	= $this->PSKEW = substr($prod_id,3,2).'.'.substr($prod_id,5,3);
				$this->PRODUCT_ID		= $prod_id;
				$this->GID_NO_CLIENTS	= (int)$no_of_clients;
			}else{
				$this->GLC_ERROR_CODE='L003';
				return false;
			}
			$this->GID_NO_COMPANYS=0;
			$this->GID_EXPIRE_FOR=2;
			$this->GPM_LICENSE_TYPE=2;
			$this->LICENSE_TYPE_NAME='SUBSCRIPTION';
			$this->LOCAL_USER_ID=(int)$row1['GEM_EMP_ID'];
			return true;
		}
	}
	return false;
}


	/**
	 * @param int $gid_install_id
	 * 
	 * @return boolean
	 */

	protected function check_hard_disk_id_change_approved($gid_install_id){
	
	if(empty($gid_install_id)){
		return false;
	}	
$query=<<<END
SELECT GAR_STATUS ,GAR_REASON,GAR_APPROVED_BY,GEM_EMP_NAME,GAR_APPROVED_DATE,dtl.GAR_ID,GAR_NOTE,GAR_APPROVED_FLAG,GAR_APPROVAL_COMMENT,
GAR_REASON  
FROM gft_approved_reinstallation_dtl dtl
join  gft_approved_reinstallation hdr on (hdr.GAR_ID=dtl.GAR_ID)
left join gft_emp_master em on (gem_emp_id=GAR_APPROVED_BY)
WHERE  dtl.GAR_INSTALL_ID_REFF=$gid_install_id and GAR_STATUS='P' ;
END;

// Reason check missing 

		$result=execute_my_query($query);
		if(mysqli_num_rows($result)==1){
			$qd=mysqli_fetch_array($result);
			if($qd['GAR_APPROVED_FLAG']=='N'){
				return false;
			}else{
				$this->GAR_APPROVED_FLAG='Y';
				$this->GAR_APPROVED_BY=$qd['GEM_EMP_NAME'];
				$this->GAR_NOTE=$qd['GAR_NOTE'];
				$this->GAR_APPROVAL_COMMENT=$qd['GAR_APPROVAL_COMMENT'];
				$this->GAR_ID=(int)$qd['GAR_ID'];
				return true;
			}
		}
		return false;
}


/**
 * @param string $order_no
 * @param int $fullfillment_no
 * @param int $pcode
 * @param string $product_group
 * @param string $skew
 * 
 * @return boolean
 */
public function check_exist_order_no($order_no,$fullfillment_no,$pcode,$product_group,$skew){
	$today=date('Y-m-d');
	$this->check_order_split();
	
$query=" select GLH_LEAD_CODE,GLH_CUST_NAME,god_order_status,GOD_ORDER_NO,GOP_FULLFILLMENT_NO,god_order_splict,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,gft_skew_property, ".
	" if(GPM_ORDER_TYPE=1,(GOP_QTY*GPM_CLIENTS),(GOP_ADD_CLIENTS+GPM_CLIENTS)) AS nclients, ".
	" if(GPM_ORDER_TYPE=1,(GOP_QTY*GPM_COMPANYS),(GOP_ADD_COMPANYS+GPM_COMPANYS)) 'ncompanys', ".
	" if(god_order_type=4,1,GPL_EXPIRTY_TYPE) as  GID_EXPIRE_FOR, GPM_LICENSE_TYPE,GPL_LICENSE_TYPE_NAME, ".
	" if(god_order_type=4 or GPL_EXPIRTY_TYPE=1,DATE_ADD('$today',INTERVAL GPM_DEFAULT_ASS_PERIOD DAY), ".
	" DATE_ADD('$today',INTERVAL GPM_SUBSCRIPTION_PERIOD DAY)) as  GID_VALIDITY_DATE ,god_emp_id as credited_to, ".
	" ord.GEM_EMP_NAME as 'ORDERED_BY',ord.GEM_EMP_ID as 'ORDERED_EMP_ID',GOD_ORDER_DATE ,GPM_SKEW_DESC,if(pm.GPM_REFERER_SKEW='',GPM_PRODUCT_SKEW,GPM_REFERER_SKEW) AS GPM_REFERER_SKEW, ".
	" GPM_TRAINING_REQUIRED,GOD_IMPL_REQUIRED,opd.GOP_REFERENCE_ORDER_NO as ref_order,opd.GOP_REFERENCE_FULLFILLMENT_NO as ref_fullfill ".
	" from gft_order_hdr ".
	" left join gft_lead_hdr lh on (glh_lead_code=god_lead_code) ".	
	" left join gft_order_product_dtl opd on (gop_order_no=god_order_no) ".
	" left join gft_product_master pm on (pm.gpm_product_code=gop_product_code and gpm_product_skew=gop_product_skew ) ".
	" left join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code and GPM_HEAD_FAMILY='$this->HEAD_OF_FAMILY') ".
	" left join gft_product_group_master pg on (pg.gpg_product_family_code=pfm.gpm_head_family and pg.gpg_skew=substr(pm.GPM_PRODUCT_SKEW,1,4)) ".
	" left join gft_product_license_type plt on  (GPM_LICENSE_TYPE=GPL_LICENSE_TYPE_ID ) ".
	" left join gft_emp_master ord on (ord.gem_emp_id=god_emp_id) ".
	" left join gft_emp_master lfd on (lfd.gem_emp_id=glh_lfd_emp_id) ".
	" where god_order_no='$order_no' and GOP_FULLFILLMENT_NO='$fullfillment_no' and GFT_SKEW_PROPERTY in (1,11) ";

$query_cp="	select GLH_LEAD_CODE,GLH_CUST_NAME,god_order_status,god_order_splict,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GOP_QTY,gft_skew_property,GCO_ORDER_NO,GCO_FULLFILLMENT_NO, ".
	" if(GPM_ORDER_TYPE=1,(GCO_CUST_QTY*GPM_CLIENTS),GPM_CLIENTS)+GCO_ADD_CLIENTS AS nclients, ".
	" if(GPM_ORDER_TYPE=1,(GCO_CUST_QTY*GPM_COMPANYS),(GCO_ADD_COMPANYS+GPM_COMPANYS)) 'ncompanys', ".
	" if(god_order_type=4,1,GPL_EXPIRTY_TYPE) as  GID_EXPIRE_FOR,GPM_LICENSE_TYPE,GPL_LICENSE_TYPE_NAME, ".	
	" if(god_order_type=4 or GPL_EXPIRTY_TYPE=1,DATE_ADD('$today',INTERVAL GPM_DEFAULT_ASS_PERIOD DAY) , ".
	" DATE_ADD('$today',INTERVAL GPM_SUBSCRIPTION_PERIOD DAY)) as  GID_VALIDITY_DATE, god_emp_id as credited_to, ".
	" ord.GEM_EMP_NAME as 'ORDERED_BY',ord.GEM_EMP_ID as 'ORDERED_EMP_ID',GOD_ORDER_DATE ,GPM_SKEW_DESC,if(pm.GPM_REFERER_SKEW='',GPM_PRODUCT_SKEW,GPM_REFERER_SKEW) AS GPM_REFERER_SKEW, ".
	" GPM_TRAINING_REQUIRED,GOD_IMPL_REQUIRED,cp.GCO_REFERENCE_ORDER_NO as ref_order,cp.GCO_REFERENCE_FULLFILLMENT_NO as ref_fullfill ".
	" from gft_order_hdr ".
	" left join gft_order_product_dtl opd on (gop_order_no=god_order_no ) ".
	" left join gft_product_master pm on (pm.gpm_product_code=gop_product_code and gpm_product_skew=gop_product_skew) ". 
	" left join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code and GPM_HEAD_FAMILY='$this->HEAD_OF_FAMILY') ".
	" left join gft_product_group_master pg on (pg.gpg_product_family_code=pfm.gpm_head_family and pg.gpg_skew=substr(pm.GPM_PRODUCT_SKEW,1,4)) ".
	" left join gft_product_license_type plt on  (GPM_LICENSE_TYPE=GPL_LICENSE_TYPE_ID ) ".
	" left join gft_cp_order_dtl cp on (gco_order_no=gop_order_no and gco_product_code=gop_product_code and gco_skew=gop_product_skew ".
	" and GOP_REF_SERIAL_NO=GCO_REF_SERIAL_NO and GCO_FULLFILLMENT_NO=$fullfillment_no) ".
	" join gft_lead_hdr lh on (GLH_LEAD_CODE=gco_cust_code) ".
	" left join gft_emp_master ord on (ord.gem_emp_id=god_emp_id) ".
	" left join gft_emp_master lfd on (lfd.gem_emp_id=glh_lfd_emp_id) ".
	" where god_order_no='$order_no'	AND god_order_splict=1 and GFT_SKEW_PROPERTY in (1,11) ";

if($this->WEB_REQUEST_TYPE==1){  //Backward compatibility
	if($pcode!=''){
		$query_cp.="and GOP_PRODUCT_CODE='$pcode'";
		$query.="and GOP_PRODUCT_CODE='$pcode'";
	}
}
if($skew!=''){
	$query.=" and (gpm_product_skew='$skew' or GPM_REFERER_SKEW='$skew')  ";
	$query_cp.=" and (gpm_product_skew='$skew' or GPM_REFERER_SKEW='$skew')  ";
}else if($product_group!=''){
	$query.=" and pg.gpg_skew='$product_group' ";
	$query_cp.=" and pg.gpg_skew='$product_group' ";
}else{
	$this->GLC_ERROR_CODE='E024';
	return false;
}

if($this->CUSTOM_GROUP!=''){
	$query.=" and gop_product_skew like '%".$this->CUSTOM_GROUP."' ";
	$query_cp.=" and GCO_SKEW like '%".$this->CUSTOM_GROUP."' ";
}

if($this->ORDER_SPLIT==1) {
	$result=execute_my_query($query_cp);
}else {
	$result=execute_my_query($query);
}

	$num_rows=mysqli_num_rows($result);

	if($num_rows==1){
		$qd=mysqli_fetch_array($result);
		if($qd['god_order_status']!=='A'){
			$this->GLC_ERROR_CODE='E015';/* Order is not active */			
			return false;
		}else if($qd['GOP_PRODUCT_CODE']===''){
			$this->GLC_ERROR_CODE='E021'; /* Product Mismatch */
			return false; 
		}else if($skew!='' and $qd['GOP_PRODUCT_SKEW']===''){
			$this->GLC_ERROR_CODE='E022'; /* SKEW Mismatch */
			return false;
		}else if( (int)$qd['gft_skew_property']!=1 and (int)$qd['gft_skew_property']!=11 ){
			$this->GLC_ERROR_CODE='E014'; /* Order Number is Wrong */
			return false;
		}else if ($qd['ref_order']!='' && $qd['ref_fullfill']!='' && $this->WEB_REQUEST_TYPE!=1) {
			$this->GLC_ERROR_CODE='E011'; /* Order Number Contains References. Its not the root order. For User Based Order validation */
			return false;
		}else if((int)$qd['god_order_splict']==1){   /* spitable order */
			$result_cp=execute_my_query($query_cp);
			$qd_cp=mysqli_fetch_array($result_cp);
			if(empty($qd_cp['GCO_ORDER_NO']) and $this->FULLFILLMENT_NO >(int)$qd_cp['GOP_QTY']){
					$this->GLC_ERROR_CODE='E029';					
					return false;
			}
			if(empty($qd_cp['GCO_ORDER_NO'])){
					$this->GLC_ERROR_CODE='E020'; /* order not splited */
					return false;
			}
						
			$this->GLH_CUST_NAME=$qd_cp['GLH_CUST_NAME'];
			$this->PRODUCT_ID=$qd_cp['GOP_PRODUCT_CODE'].(string)str_replace('.','',$qd_cp['GPM_REFERER_SKEW']);
			$this->LEAD_CODE=$this->GID_LEAD_CODE=(int)$qd_cp['GLH_LEAD_CODE'];
			$this->GID_ORDER_NO=$qd_cp['GCO_ORDER_NO'];
			$this->GID_FULLFILLMENT_NO=(int)$qd_cp['GCO_FULLFILLMENT_NO'];
			$this->GID_LIC_PCODE=(int)$qd_cp['GOP_PRODUCT_CODE'];
			$this->GID_LIC_PSKEW=$qd_cp['GOP_PRODUCT_SKEW'];
			$this->SKEW_DESC=$qd_cp['GPM_SKEW_DESC'];
			$this->GID_NO_CLIENTS=(int)$qd_cp['nclients'];
			$this->GID_NO_COMPANYS=(int)$qd_cp['ncompanys'];			
			$this->GID_EXPIRE_FOR=(int)$qd_cp['GID_EXPIRE_FOR'];
			$this->GPM_LICENSE_TYPE=(int)$qd_cp['GPM_LICENSE_TYPE'];
			$this->LICENSE_TYPE_NAME=$qd_cp['GPL_LICENSE_TYPE_NAME'];
			$this->GID_VALIDITY_DATE=$qd_cp['GID_VALIDITY_DATE'];
			$this->GID_SALESEXE_ID=(int)$qd_cp['credited_to'];
			$this->GOD_IMPL_REQUIRED=$qd_cp['GOD_IMPL_REQUIRED'];
			$this->GPM_TRAINING_REQUIRED=$qd_cp['GPM_TRAINING_REQUIRED'];
			$this->GOD_ORDER_DATE=$qd_cp['GOD_ORDER_DATE'];
			$this->GOD_ORDERED_BY=$qd_cp['ORDERED_BY'];
			$this->ORDERED_EMP_ID=(int)$qd_cp['ORDERED_EMP_ID'];
			$kit_validity = get_kit_based_validity($this->GID_ORDER_NO,$this->GID_LIC_PCODE,$this->PRODUCT_GROUP);
			if($kit_validity!=''){
				$this->GID_VALIDITY_DATE = $kit_validity;
			}
			return true;
		}
		$this->GLH_CUST_NAME=$qd['GLH_CUST_NAME'];
		$this->PRODUCT_ID=$qd['GOP_PRODUCT_CODE'].(string)str_replace('.','',$qd['GPM_REFERER_SKEW']);
		$this->LEAD_CODE=$this->GID_LEAD_CODE=(int)$qd['GLH_LEAD_CODE'];
		$this->GID_ORDER_NO=$qd['GOD_ORDER_NO'];
		$this->GID_FULLFILLMENT_NO=(int)$qd['GOP_FULLFILLMENT_NO'];
		$this->GID_LIC_PCODE=(int)$qd['GOP_PRODUCT_CODE'];
		$this->GID_LIC_PSKEW=$qd['GOP_PRODUCT_SKEW'];
		$this->SKEW_DESC=$qd['GPM_SKEW_DESC'];
		$this->GID_NO_CLIENTS=(int)$qd['nclients'];
		$this->GID_NO_COMPANYS=(int)$qd['ncompanys'];
		$this->GID_EXPIRE_FOR=(int)$qd['GID_EXPIRE_FOR'];
		$this->GPM_LICENSE_TYPE=(int)$qd['GPM_LICENSE_TYPE'];
		$this->LICENSE_TYPE_NAME=$qd['GPL_LICENSE_TYPE_NAME'];
		$this->GID_VALIDITY_DATE=$qd['GID_VALIDITY_DATE'];
		if($this->IS_DEALER){
			$trial_validity = (int)get_samee_const("Dealer_Trial_Days");
			$this->GID_VALIDITY_DATE = date('Y-m-d', strtotime("+$trial_validity days"));
		}
		$this->GID_SALESEXE_ID=(int)$qd['credited_to'];
		$this->GOD_IMPL_REQUIRED=$qd['GOD_IMPL_REQUIRED'];
		$this->GPM_TRAINING_REQUIRED=$qd['GPM_TRAINING_REQUIRED'];
		$this->GOD_ORDER_DATE=$qd['GOD_ORDER_DATE'];
		$this->GOD_ORDERED_BY=$qd['ORDERED_BY'];
		$this->ORDERED_EMP_ID=(int)$qd['ORDERED_EMP_ID'];
		return true;
	}else if ($num_rows > 0){
		$this->GLC_ERROR_CODE='E019.1';
		return false;
	}else{
		$this->GLC_ERROR_CODE='E019';
		return false;
	}
}

	/**
	 * @return void
	 */
	public function check_order_split(){
		if( !empty($this->ORDER_NO) ){
			$result_order=execute_my_query("select GOD_ORDER_SPLICT from  gft_order_hdr where god_order_no='$this->ORDER_NO' ");
			if(mysqli_num_rows($result_order)==0){
				$this->GLC_ERROR_CODE='E027';
				$this->GLC_ADDITIONAL_ERROR_MESSAGE='Order Number not available in SAM ';
			}else{
				$qd=mysqli_fetch_array($result_order);
				$this->ORDER_SPLIT=(int)$qd['GOD_ORDER_SPLICT'];
			}
		}
	}
	
	/**
	 * @return string
	 */
	public function get_license_status(){
		$lic_stat='0';
		$this->check_order_split();
		if($this->ORDER_SPLIT==0) {
		$qu_st = "select GOP_LICENSE_STATUS as LICENSE_STATUS from gft_order_product_dtl ".
					" join gft_product_master pm on (GFT_SKEW_PROPERTY in (1,11) and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW and pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE) ".
					" join gft_product_family_master fm  on (fm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE) ".
					" where GOP_ORDER_NO='$this->ORDER_NO' and GOP_FULLFILLMENT_NO='$this->FULLFILLMENT_NO' ".
					" and (GOP_PRODUCT_CODE='$this->PCODE' or GPM_HEAD_FAMILY='$this->PCODE')";
		}else {
			$qu_st = "select GCO_LICENSE_STATUS as LICENSE_STATUS from gft_cp_order_dtl ".
					" join gft_product_master pm on (GFT_SKEW_PROPERTY in (1,11) and GCO_SKEW=GPM_PRODUCT_SKEW and pm.GPM_PRODUCT_CODE=GCO_PRODUCT_CODE) ".
					" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=GCO_PRODUCT_CODE) ".
					" where	GCO_ORDER_NO='$this->ORDER_NO' and GCO_FULLFILLMENT_NO='$this->FULLFILLMENT_NO' ".
					" and (GCO_PRODUCT_CODE='$this->PCODE' or GPM_HEAD_FAMILY='$this->PCODE')";
		}
		$result_st = execute_my_query($qu_st);
		if( $dat=mysqli_fetch_array($result_st) ) {
			$lic_stat = $dat['LICENSE_STATUS'];
		}else{
			$get_prod = " select if(GOD_ORDER_SPLICT=0,GOP_LICENSE_STATUS,GCO_LICENSE_STATUS) as LICENSE_STATUS from gft_install_dtl_new ".
						" join gft_order_hdr on (GOD_ORDER_NO = GID_ORDER_NO) ".
						" left join gft_order_product_dtl on (GID_ORDER_NO=GOP_ORDER_NO and GID_PRODUCT_CODE=GOP_PRODUCT_CODE) ".
						" left join gft_cp_order_dtl on (GID_ORDER_NO=GCO_ORDER_NO and GID_FULLFILLMENT_NO=GCO_FULLFILLMENT_NO and GID_PRODUCT_CODE=GCO_PRODUCT_CODE) ".
						" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=GID_LIC_PCODE) ".
						" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and pm.GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
						" where GID_ORDER_NO='$this->ORDER_NO' and GID_FULLFILLMENT_NO=$this->FULLFILLMENT_NO ".
						" and (GID_LIC_PCODE='$this->PCODE' or GPM_HEAD_FAMILY='$this->PCODE') and GFT_SKEW_PROPERTY in (1,11) ";
			$get_res = execute_my_query($get_prod);
			if($get_data=mysqli_fetch_array($get_res)){
				$lic_stat=$get_data['LICENSE_STATUS'];
			}
		}
		return $lic_stat;
	}
	
	/**
	 * @return int
	 */
	public function get_license_approval_days(){
		$que =  " select if(GOD_ORDER_SPLICT=0,GOP_APPROVAL_DAYS,GCO_APPROVAL_DAYS) as GOP_APPROVAL_DAYS ".
				" from gft_order_hdr ".
				" join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO and GOP_ORDER_NO='$this->ORDER_NO' ".
				" and GOP_FULLFILLMENT_NO=$this->FULLFILLMENT_NO) ".
				" left join gft_cp_order_dtl on (GOD_ORDER_NO=GCO_ORDER_NO and GCO_ORDER_NO='$this->ORDER_NO' and ".
				" GCO_FULLFILLMENT_NO=$this->FULLFILLMENT_NO) ";
		$res = execute_my_query($que);
		if($row = mysqli_fetch_array($res)){
			return (int)$row['GOP_APPROVAL_DAYS'];
		}else{
			return 30;  //default value, if days not given
		}	
	}
	
	/**
	 * @return void
	 */
	protected function generate_pkey_akey(){
		$last_sent_date = "";
		$gid_db_password = "";
		global $secret;
		//LICENSE APPROVAL PROCESS
		if($this->LOCAL_USER=="N") {
			$this->LICENSE_STATUS = $this->get_license_status();
			if($this->LICENSE_STATUS=='0'){
				$this->GLC_ERROR_CODE='LA04';
				return;
			}
			$quer = " select GID_CURRENT_LICENSE,GID_TRIAL_APPLIED_COUNT,GID_TRIAL_TILL_DATE,GID_SENT_EXPIRY_DATE,GID_DB_PASSWORD ".
					" from gft_install_dtl_new where GID_INSTALL_ID='$this->GID_INSTALL_ID'";
			$quer_res = execute_my_query($quer);
			if( (mysqli_num_rows($quer_res)==1) && $quer_data=mysqli_fetch_array($quer_res) ) {
				$this->LIC_COUNT			=	(int)$quer_data['GID_TRIAL_APPLIED_COUNT'];
				$this->GID_TRIAL_TILL_DATE	=	$quer_data['GID_TRIAL_TILL_DATE'];
				$last_sent_date				= 	$quer_data['GID_SENT_EXPIRY_DATE'];
				$raw_password				= $quer_data['GID_DB_PASSWORD'];
				if($raw_password!=''){
					$gid_db_password = $raw_password;
				}
			}else{
			    $send_act_key = isset($this->parsed_xml['SEND_ACTIVATION_KEY']) ? $this->parsed_xml['SEND_ACTIVATION_KEY'] : '';
			    if( ($send_act_key=='Y') && in_array($this->GID_LIC_PCODE, array('200','500')) ){
			        $skip_data_security = get_samee_const("SKIP_DATA_SECURITY");
			        if($skip_data_security!='YES'){
			            $corp_cust = get_single_value_from_single_table("glh_reference_given", "gft_lead_hdr", "glh_lead_code", $this->LEAD_CODE);
			            if($corp_cust!='626563'){ //temproray for swiggy stores installtion due to windows password complexity rules. Once POS and DFW team releases permanenet fix, we can remove it
			                $this->DATA_SECURITY_PASSWORD = $gid_db_password = generate_private_key_password();
			            }
			        }
			    }
			}
			if($this->LICENSE_STATUS=='1' || $this->LICENSE_STATUS=='4'  || $this->LICENSE_STATUS=='3') { // 1- Waiting for CM approval  3- Higher Authority Approved  4-Waiting Annuity Approval
				$this->PENDING_APPROVAL=1;
				if($this->LICENSE_STATUS=='1') {
					if(!empty($this->GID_INSTALL_ID)){
						if($this->LIC_COUNT==0){ $this->LIC_COUNT++; }
						$que_res = execute_my_query("select GID_INSTALL_DATE from gft_install_dtl_new where GID_INSTALL_ID=$this->GID_INSTALL_ID and GID_ORDER_NO=GID_LIC_ORDER_NO");
						if( (mysqli_num_rows($que_res)==1) && ($que_da = mysqli_fetch_array($que_res)) ){
							$this->GID_INSTALL_DATE=$que_da['GID_INSTALL_DATE'];
							//$this->GID_VALIDITY_DATE=add_date($this->GID_INSTALL_DATE,30);
							$this->annuity_valid=add_date($this->GID_INSTALL_DATE,$this->get_license_approval_days());
						}else{
							$up_que = " select GOD_ORDER_DATE from gft_order_hdr ".
									  " join gft_install_dtl_new on (GID_LIC_ORDER_NO = GOD_ORDER_NO) ".
									  " where GID_INSTALL_ID=$this->GID_INSTALL_ID";
							$up_res = execute_my_query($up_que);
							if( (mysqli_num_rows($up_res)==1) && ($up_data = mysqli_fetch_array($up_res)) ){
								$this->annuity_valid=add_date($up_data['GOD_ORDER_DATE'],30);
							}
						}
						$que_inser = "update gft_install_dtl_new set GID_TRIAL_TILL_DATE='$this->annuity_valid', GID_TRIAL_APPLIED_COUNT=$this->LIC_COUNT ".
								" where GID_INSTALL_ID='$this->GID_INSTALL_ID'";
						if(!execute_my_query($que_inser)) {
							$this->GLC_ERROR_CODE='DB01';
						}
					}else{
						$this->annuity_valid=date('Y-m-d',mktime(0, 0, 0, (int)date("m") , (int)date("d")+$this->get_license_approval_days(), (int)date("Y")));
					}
				}else if($this->LICENSE_STATUS=='4'){
					if($this->GID_STATUS=='S'){
						$this->annuity_valid=date('Y-m-d',mktime(0, 0, 0, (int)date("m") , (int)date("d")+30, (int)date("Y")));
						if($this->annuity_valid > $this->GID_VALIDITY_DATE){
							$this->annuity_valid=$this->GID_VALIDITY_DATE;
						}
						$que_up = "update gft_install_dtl_new set GID_TRIAL_TILL_DATE='$this->annuity_valid' ".
							" where GID_INSTALL_ID='$this->GID_INSTALL_ID'";
						execute_my_query($que_up);
					}else{
						$this->annuity_valid=$this->GID_TRIAL_TILL_DATE;
					}
				}elseif ($this->LICENSE_STATUS=='3'){
					$this->annuity_valid=$this->GID_TRIAL_TILL_DATE;
				}
				$this->GPM_LICENSE_TYPE=3;
				$this->LICENSE_TYPE_NAME="Trial";
			}else if($this->LICENSE_STATUS=='2' || $this->LICENSE_STATUS=='8') {
				if(!empty($this->GID_INSTALL_ID)) {
					$que_ins = "update gft_install_dtl_new set GID_CURRENT_LICENSE='ACTUAL' ".
							   " where GID_INSTALL_ID='$this->GID_INSTALL_ID'";
					$ins_res = execute_my_query($que_ins);
					if(!$ins_res) {
						$this->GLC_ERROR_CODE='DB01';
					}
				}
			}else if($this->LICENSE_STATUS=='7'){
				$this->PENDING_APPROVAL=1;
				$this->annuity_valid=add_date($this->GID_INSTALL_DATE,(int)get_samee_const('Evaluation_Limit'));
				$this->LIC_NOTE=$this->get_license_note_msg('MSG_01');
			}else if($this->LICENSE_STATUS=='9'){
				$this->GLC_ERROR_CODE='E034';
				return;
			}
			
			if($this->LICENSE_STATUS=='1' || $this->LICENSE_STATUS=='3'){   //waiting for cm || higher authority extend
				$this->LIC_NOTE=$this->get_license_note_msg('MSG_03');
				$this->complaint_link=get_samee_const('Subscription_Msg_Link');
			}else if($this->LICENSE_STATUS=='8' || $this->LICENSE_STATUS=='12'){  // contact approved by LM || Evaluation extended by BD
				$assign_que=" select GEM_EMP_NAME,glh_lfd_emp_id,GEM_MOBILE,GEM_RELIANCE_NO from ".
						" gft_lead_hdr join gft_emp_master on (glh_lfd_emp_id=GEM_EMP_ID)".
						" where GLH_LEAD_CODE='$this->LEAD_CODE'";
				$assign_res=execute_my_query($assign_que);
				if( (mysqli_num_rows($assign_res)==1) && ($data=mysqli_fetch_array($assign_res)) ){
					$this->ASSIGNED_EMP=$data['GEM_EMP_NAME'];
					//if mobile_no_1 not available ,then we will display the mobile_no_2.
					if($data['GEM_MOBILE']!=''){
						$this->ASSIGNED_EMP_NO=$data['GEM_MOBILE'];
					}else if($data['GEM_RELIANCE_NO']!=''){
						$this->ASSIGNED_EMP_NO=$data['GEM_RELIANCE_NO'];
					}
				
				}
				$this->LIC_NOTE=$this->get_license_note_msg('MSG_02');
				$this->complaint_link=get_samee_const('Subscription_Msg_Link');
			}
		}elseif ($this->LOCAL_USER=='Y'){
			$add_days = 30;
			if($this->GID_LIC_PCODE=='550'){
				$this->GPM_LICENSE_TYPE=3;
				$this->LICENSE_TYPE_NAME='TRIAL';
				$add_days = 7;
			}
			$this->GID_VALIDITY_DATE=add_date(date('Y-m-d'), $add_days);
		}
		
		$current_expiry_date = $this->GID_VALIDITY_DATE;
		if($this->annuity_valid!=''){
			if($this->annuity_valid < date('Y-m-d')){
				$this->GLC_ERROR_CODE = "LA05";
				return;
			}
			$current_expiry_date = $this->annuity_valid;
		}
		
		/* 	* No need to check the nearest/highest minimum version check here 
		 	* as license update for (additional client/addon/asa) can be done for versions above delight 1
		if($current_expiry_date==$last_sent_date){
			if(!$this->check_above_minimum_version($this->PCODE, $this->parsed_xml['VERSION'], $this->PRODUCT_GROUP, 'nearest')){
				return ;	
			}
		}else{
			//no need of minimum version here as Expiry date need to be synced between POS and SAM
		}*/
		
		if(empty($this->GID_ORDER_NO)){
			$this->GLC_ERROR_CODE='E023';
			return;			
		}else if(!empty($this->parsed_xml['PRODUCT_KEY'])){
			if(!$this->check_productkey()){
				return ;
			}
		}else if(empty($this->parsed_xml['PRODUCT_KEY'])){	
			$hkey = ($this->HKEY!='')?$this->HKEY:$this->parsed_xml['HDD_KEY'];
			$cust_name = (string)str_replace(array("'",'"','~'), "", $this->GLH_CUST_NAME);
			if( ($this->IS_OFFLINE) && isset($this->parsed_xml['SHOP_NAME'])){
				$cust_name = $this->parsed_xml['SHOP_NAME'];
			}
			$this->PRODUCT_KEY=generate_product_key(strtoupper(trim($cust_name)),$this->ORDER_NO,$hkey,$this->PRODUCT_ID,$this->GID_FULLFILLMENT_NO);
			$this->ENCRYPED_HDD_KEY=do_encryption_hdd($hkey);
			if(strlen($this->PRODUCT_KEY)<24){
				$this->GLC_ERROR_CODE='TF07';
				return;
			}	
		}		
					
		if(!empty($this->PRODUCT_KEY)){	
	    	$spk=split_pkey_from_fullno($this->PRODUCT_KEY);
			$prodkeyfull=$spk[0];			$fullfill_no=$spk[1]; 
			$eswaptonormal=do_encryption_productkey_sn($prodkeyfull);
			$product_key=/*. (string[string]) .*/array();
			$product_key["customer_name"]=substr($eswaptonormal,0,4);
			$product_key["order_no"]=substr($eswaptonormal,4,4);
			$product_key["hkey"]=$this->GID_LIC_HARD_DISK_ID=substr($eswaptonormal,8,4);
			$product_key["productcode"]=substr($eswaptonormal,12,4);
			$product_key["product_skew"]=substr($eswaptonormal,16,4);
			$product_key["inst_type"]='1';//substr($this->GID_LIC_HARD_DISK_ID,3,1);
			$product_key["noclients"]=substr($eswaptonormal,20,2);
			$product_key['fullfillment_no']=$fullfill_no;
			
	   		
       		$product_key["fullfillment_no"]=substr("000".$product_key["fullfillment_no"],-4);
	
			$activationkey=do_activation_customername($product_key["customer_name"]);
			$activationkey.=do_activation_orderno($product_key["order_no"]);
			$activationkey.=do_activation_hdd($product_key["hkey"]);
			$activationkey.=do_activation_prodid0($product_key["productcode"],$this->PCODE);
			$activationkey.=do_activation_prodid1($product_key["product_skew"]);
			$activationkey.=do_ak_orderfullfillmentno($product_key["fullfillment_no"]);
			//if number of clients greater than 99 (for ex.209), then it will be made as four digits (0209)
			//first two digits (02) will be appended at the end of activation key
			//last two digits (09) will be in same place as like before
			$end_noc='';
			if( strlen($this->GID_NO_CLIENTS) > 2 ) {
				$noc_str = substr("0000".$this->GID_NO_CLIENTS,-4);
				$end_noc = do_ak_noc_server(substr($noc_str,0,2));
			}
			$activationkey.=do_ak_noc_server(substr("00".$this->GID_NO_CLIENTS,-2));
			//specially for DE-5 Backward compatability
			if($this->WEB_REQUEST_TYPE==1) {
				if(($this->GID_EXPIRE_FOR==2 or $this->GID_EXPIRE_FOR==4) or ($this->GPM_LICENSE_TYPE==3)){
					if($this->annuity_valid!=''){
						$subscription_prd_split=explode('-',$this->annuity_valid);
					}else{
						$subscription_prd_split=explode('-',$this->GID_VALIDITY_DATE);
					}
					$ltmp_1=substr($subscription_prd_split[0],-2);//yyyy to yy
					$ltmp=$ltmp_1.$subscription_prd_split[1].$subscription_prd_split[2];//yymmdd
					$asubscribtion_prd=do_ak_graceperiod($ltmp);
					$validity_prd = $asubscribtion_prd;
				}else {
					if($this->annuity_valid!=''){
						$ass_expiry_date_split=explode('-',$this->annuity_valid);
					}else{
						$ass_expiry_date_split=explode('-',$this->GID_VALIDITY_DATE);
					}
					$ltmp_1=substr($ass_expiry_date_split[0],-2);
					$ltmp_ass=$ltmp_1.$ass_expiry_date_split[1];//yymm
					$aass_prd=do_ak_graceperiod($ltmp_ass);
					$validity_prd = $aass_prd;
				}  
			}else{
				//before key length is 30 for perpetual. due to this a limitation in pos occured as 25th as default.
				//in Project Delight we eliminate it by sending the the 32 digits.
				//need to check with the older version POS			
				if($this->annuity_valid!=''){
					$subscription_prd_split=explode('-',$this->annuity_valid);
				}else{
					$subscription_prd_split=explode('-',$this->GID_VALIDITY_DATE);
				}
				$ltmp_1=substr($subscription_prd_split[0],-2);//yyyy to yy
				$ltmp=$ltmp_1.$subscription_prd_split[1].$subscription_prd_split[2];//yymmdd
				$asubscribtion_prd=do_ak_graceperiod($ltmp);
				$validity_prd = $asubscribtion_prd;
			}
			$activationkey .= $validity_prd;
			
			if(!empty($this->GID_NO_COMPANYS)){
				$no_companys=substr("0000".$this->GID_NO_COMPANYS,-4); 
				$activationkey.=do_ak_noc_server(substr($no_companys,0,2)).do_ak_noc_server(substr($no_companys,2,2));					
			}
			if($end_noc!=''){
				$activationkey.=$end_noc;
			}
		    
			$activationkey_ns=do_activationkey_ns($activationkey);
			$this->ACTIVATION_KEY=$activationkey_ns;
			
			$this->ORDER_NO_SPLITED=substr($this->GID_ORDER_NO,0,5);
			$this->ORDER_NO_SPLITED.="-".substr($this->GID_ORDER_NO,5,5);
			$this->ORDER_NO_SPLITED.="-".substr($this->GID_ORDER_NO,10,5);
			$this->ORDER_NO_SPLITED.="-".substr("0000".$this->GID_FULLFILLMENT_NO,-4);
			
			$this->PRODUCT_KEY_SPLITED=substr($this->PRODUCT_KEY,0,4);
			$this->PRODUCT_KEY_SPLITED.="-".substr($this->PRODUCT_KEY,4,4);
			$this->PRODUCT_KEY_SPLITED.="-".substr($this->PRODUCT_KEY,8,4);
			$this->PRODUCT_KEY_SPLITED.="-".substr($this->PRODUCT_KEY,12,4);
			$this->PRODUCT_KEY_SPLITED.="-".substr($this->PRODUCT_KEY,16,4);
			$this->PRODUCT_KEY_SPLITED.="-".substr($this->PRODUCT_KEY,20,4);
			
			$pos_prod_dtl = get_pos_details_for_kit_based_customer($this->GID_LEAD_CODE,'xml');
			$pos_noc = 0;
			$addon_list = "";
			if($this->GID_LIC_PCODE=='300'){
			    $addon_cnt 	= get_addons_bought_for_kit($this->GID_LEAD_CODE);
			    foreach ($addon_cnt as $arr_addon){
			        $addon_list .= "<ADDONS NAME='$arr_addon[0]' SKEW='$arr_addon[1]' COUNT='$arr_addon[2]' ORDER='$arr_addon[3]'  ADDON_TYPE='$arr_addon[4]' />";
			    }
			    $pos_noc 	= get_number_of_clients_from_order($this->LEAD_CODE);
			}
			if($pos_prod_dtl!=''){
				$this->KIT_BASED = "Y";
			}
			$custom_order_nos=$this->get_custom_license();
			$this->CUSTOM_LIC=isset($custom_order_nos[0])?$custom_order_nos[0]:array();  
			$this->CUSTOM_SKEWS=isset($custom_order_nos[1])?$custom_order_nos[1]:array(); 
			$this->CUSTOM_STATUS=isset($custom_order_nos[2])?$custom_order_nos[2]:array(); 
			$cl_qty_arr	= isset($custom_order_nos[3])?$custom_order_nos[3]:array();
			$cl_type_arr = isset($custom_order_nos[4])?$custom_order_nos[4]:array();
			$this->GLC_STATUS='S';
			$product_id = $this->PRODUCT_ID;
			if($this->IS_DFT_ORDER_NO_FOR_POS){
				$product_id = $this->parsed_xml['PRODUCT_FCODE'].(string)str_replace(".", "", $this->parsed_xml['PRODUCT_GROUP'])."SR";
			}		
			$this->GLC_RETURN_DATA='<?xml version="1.0" standalone="yes"?>' .
						'<ACTIVATION_KEY>'. 
    					'<AK>'.        			 
        				'<ORDER_NO>'.$this->ORDER_NO_SPLITED.'</ORDER_NO>'.
        				'<PRODUCT_ID>'.$product_id.'</PRODUCT_ID>' .
        				'<HDD_KEY>'.$this->parsed_xml['HDD_KEY'].'</HDD_KEY>' .
        				'<HKEY>'.$this->HKEY.'</HKEY>' .
        				'<INST_TYPE>1</INST_TYPE>' .
        				'<LICENSE_TYPE_NAME>'.$this->LICENSE_TYPE_NAME.'</LICENSE_TYPE_NAME>'.
        				'<LICENSE_TYPE>'.$this->GPM_LICENSE_TYPE.'</LICENSE_TYPE>';
			$uat_val = $demo_val = 'N';
			if($this->POS_UAT=='Y' || $this->UAT=='Y'){
				$uat_val = 'Y';
			}
			if($this->GID_LIC_PCODE=='300'){
				$chk_que=" select GPMA_VALUE from gft_product_master_attributes where GPMA_PRODUCT_CODE='$this->GID_LIC_PCODE' ".
					 	" and GPMA_PRODUCT_SKEW='$this->GID_LIC_PSKEW' and GPMA_VALUE=1 and GPMA_ATTRIBUTE=8 ";
				$chk_res = execute_my_query($chk_que);
				if(mysqli_num_rows($chk_res) > 0){
					$demo_val = 'Y';					
				}
			}
			$this->GLC_RETURN_DATA.="<UAT>$uat_val</UAT>".
									"<DEMO>$demo_val</DEMO>";
        	if($this->WEB_REQUEST_TYPE==1 or (isset($this->parsed_xml['SEND_ACTIVATION_KEY']) and $this->parsed_xml['SEND_ACTIVATION_KEY']=='Y') ){			
        	$this->GLC_RETURN_DATA.='<PRODUCT_KEY>'.$this->PRODUCT_KEY.'</PRODUCT_KEY>' . 
        				'<A_KEY>'.$this->ACTIVATION_KEY.'</A_KEY>';  
        	}
        	
       		if($this->LOCAL_USER_ID!=0){
       			$this->GLC_RETURN_DATA.='<CUSTOMER_ID>'.$this->LOCAL_USER_ID.'</CUSTOMER_ID>';
       		}else{
       			$this->GLC_RETURN_DATA.='<CUSTOMER_ID>'.$this->LEAD_CODE.'</CUSTOMER_ID>';
       		}
        	if( ($this->WEB_REQUEST_TYPE!=1) && ($this->LIC_NOTE=='') && ($this->complaint_link=='') ){
				$this->get_license_caption_url();
        	}
        	$customer_name = (string)str_replace(array("'",'"','~'), "", $this->GLH_CUST_NAME);
	        $this->GLC_RETURN_DATA.='<CUSTOMER_NAME>'.htmlspecialchars($customer_name).'</CUSTOMER_NAME>'.
        				'<LICENSE_NOTE>'.htmlspecialchars($this->LIC_NOTE).'</LICENSE_NOTE>'.
        				'<URL>'.htmlspecialchars($this->complaint_link).'</URL>';
        	
        	$this->GLC_RETURN_DATA.='<CUSTOM_LICENSE_LIST>';
			for($i=0;$i<count($this->CUSTOM_SKEWS);$i++){
				$cl_qty_val = isset($cl_qty_arr[$i])?$cl_qty_arr[$i]:"0";
				$cl_type_val = isset($cl_type_arr[$i])?$cl_type_arr[$i]:"";
	        	$this->GLC_RETURN_DATA.=' <CUSTOM_LICENSE SKEW="'.$this->CUSTOM_SKEWS[$i].'" ORDER="'.$this->CUSTOM_LIC[$i].'" STATUS="'.$this->CUSTOM_STATUS[$i].'"'.
	        							' COUNT="'.$cl_qty_val.'" CL_TYPE="'.$cl_type_val.'" />';
	        }
        	$this->GLC_RETURN_DATA.='</CUSTOM_LICENSE_LIST>';
        	$this->GLC_RETURN_DATA .= "<KIT_BASED>$this->KIT_BASED</KIT_BASED>".
          							  "<POS_NOC>$pos_noc</POS_NOC>".
        							  "<POS_DETAILS>$pos_prod_dtl</POS_DETAILS>".
        							  "<ADDON_LIST>$addon_list</ADDON_LIST>".
        							  "<DB_PASSWORD>$gid_db_password</DB_PASSWORD>";
        	
        	if($this->WEB_REQUEST_TYPE=='12'){ //system aassessment tool changes only for new online license
	        	if(system_assessment_available($this->LEAD_CODE)){
	        		$this->sys_assessment = 'Y';
	        	}else {
	        		//Sivaprakash asked to stop creating support tiket for system assessment tool
	        		//$this->create_system_assessment_support_id();
	        	}
	        	$this->GLC_RETURN_DATA.="<SA_TOOL>$this->sys_assessment</SA_TOOL>";
        	}
        	//connectplus info
        	$connectplus_enable = "NO";
        	$connectplus_token = get_connectplus_token($this->GID_LEAD_CODE,'706');
        	if($connectplus_token!=''){
        		$connectplus_enable = "YES";
        	}
        	$prod_group_dtl = get_product_group_master($this->GID_LIC_PCODE,substr($this->GID_LIC_PSKEW, 0,4));
        	$prod_alias = isset($prod_group_dtl['alias'])?$prod_group_dtl['alias']:'';
        	$pm_dtl        = get_product_master_dtl($this->GID_LIC_PCODE, $this->GID_LIC_PSKEW);
        	$re_edition    = isset($pm_dtl[5]) ? $pm_dtl[5] : '';
        	$re_vertical   = isset($this->parsed_xml['VERTICAL'])?(int)$this->parsed_xml['VERTICAL']:0;
        	if($re_vertical==0){
        	    $re_vertical = get_single_value_from_single_table("GLH_VERTICAL_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", $this->GID_LEAD_CODE);
        	}
        	$re_prod_id    = $this->GID_LIC_PCODE."-".substr($this->GID_LIC_PSKEW, 0,4);
        	$rebranded_dtl = get_rebranded_product_name($re_prod_id, $re_edition, $re_vertical);
        	$rebranded_name= isset($rebranded_dtl[0]) ? $rebranded_dtl[0] : '';
        	$solution_name = isset($rebranded_dtl[1]) ? $rebranded_dtl[1] : '';
        	$this->GLC_RETURN_DATA .= "<CONNECTPLUS>$connectplus_enable</CONNECTPLUS>".
          							  "<CONNECTPLUS_TOKEN>$connectplus_token</CONNECTPLUS_TOKEN>".
        							  "<IP_ADDRESS>".$this->IP_ADDRESS."</IP_ADDRESS>".
        							  "<PRD_ALIAS>$prod_alias</PRD_ALIAS>".
        							  "<REBRANDED_NAME>$rebranded_name</REBRANDED_NAME>".
        	                          "<SOLUTION_NAME>$solution_name</SOLUTION_NAME>";
        	$alr_not_accepted = check_for_alr_exclusion($this->ORDER_NO, (string)$this->GID_LEAD_CODE,$this->GID_INSTALL_ID);
        	$validate_alr = ($alr_not_accepted)?"FALSE":"TRUE";
        	$this->GLC_RETURN_DATA .= "<VALIDATE_ALR>$validate_alr</VALIDATE_ALR>";
        	$this->GLC_RETURN_DATA.='</AK>'. 
									'</ACTIVATION_KEY>';
			return ;
		}
	}	
	
	/**
	 * @return void
	 */
	public function get_license_caption_url(){
		$query1=" select GPG_LICENSE_NOTE_CAPTION, GPG_LICENSE_NOTE_URL from gft_product_group_master ".
				" where gpg_product_family_code=$this->HEAD_OF_FAMILY and gpg_skew='$this->PRODUCT_GROUP'";
		$result1 = execute_my_query($query1);
		if( (mysqli_num_rows($result1)==1) && $row=mysqli_fetch_array($result1) ){
			$this->LIC_NOTE = $row['GPG_LICENSE_NOTE_CAPTION'];
			$this->complaint_link = $row['GPG_LICENSE_NOTE_URL'];
		}
	}
	
	/**
	 * @return string[int][int]
	 */
	function get_custom_license(){
		$custom=/*. (string[int][int]) .*/array();
		$que_custom="select GOP_ORDER_NO as order_no,GOP_FULLFILLMENT_NO as fulfil_no,GOP_PRODUCT_CODE as pcode,GOP_PRODUCT_SKEW as pskew,GOD_ORDER_STATUS ".
					" from gft_order_product_dtl".
					" join gft_order_hdr on (GOD_ORDER_NO=GOP_ORDER_NO) ".
					" join gft_product_master on (GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW and GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_TYPE=8) ".
					" where GOD_ORDER_SPLICT=0 and GOP_REFERENCE_ORDER_NO='$this->ORDER_NO' and GOP_REFERENCE_FULLFILLMENT_NO=$this->FULLFILLMENT_NO and GOP_PRODUCT_CODE='$this->PCODE' ".
					" union all ".
					" select GCO_ORDER_NO as order_no,GCO_FULLFILLMENT_NO as fulfil_no,GCO_PRODUCT_CODE as pcode, GCO_SKEW as pskew, GOD_ORDER_STATUS ".
					" from gft_cp_order_dtl".
					" join gft_order_hdr on (GOD_ORDER_NO=GCO_ORDER_NO) ".
					" join gft_product_master on (GPM_PRODUCT_SKEW=GCO_SKEW and GPM_PRODUCT_CODE=GCO_PRODUCT_CODE and GPM_PRODUCT_TYPE=8) ".
					" where GCO_REFERENCE_ORDER_NO='$this->ORDER_NO' and GCO_REFERENCE_FULLFILLMENT_NO=$this->FULLFILLMENT_NO and GCO_PRODUCT_CODE='$this->PCODE' ";
		if($this->LOCAL_USER=='Y'){
			$que_custom=" select GLLD_ORDER_NO order_no, GLLD_FULLFILLMENT_NO fulfil_no,GLLD_PRODUCT_CODE pcode,GLLD_PRODUCT_SKEW pskew, 'A' as GOD_ORDER_STATUS ".
						" from gft_local_license_dtl where GLLD_ORDER_NO='$this->ORDER_NO' and GLLD_FULLFILLMENT_NO=$this->FULLFILLMENT_NO and GLLD_PRODUCT_CODE='$this->PCODE' and substr(GLLD_PRODUCT_SKEW,1,4)='$this->PRODUCT_GROUP' ";
		}else if ($this->KIT_BASED=='Y'){
			$que_custom=" select GOP_ORDER_NO as order_no,GOP_FULLFILLMENT_NO as fulfil_no,GOP_PRODUCT_CODE as pcode,GOP_PRODUCT_SKEW as pskew,GOD_ORDER_STATUS, ".
						" sum(GOP_QTY) as pqty from gft_order_product_dtl ".
						" join gft_order_hdr on (GOD_ORDER_NO=GOP_ORDER_NO) ".
						" join gft_product_master on (GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW and GPM_PRODUCT_CODE=GOP_PRODUCT_CODE) ".
						" where GPM_PRODUCT_TYPE=8 and GOD_LEAD_CODE='$this->GID_LEAD_CODE' and concat(GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW) not in ('50007.0CL0113','50006.5CL0088','20006.0CL0023') ".
						" and GPM_ORDER_TYPE not in (4) and GOD_ORDER_STATUS='A' group by pcode,pskew ";
		}
		$res_custom=execute_my_query($que_custom);
		while( (mysqli_num_rows($res_custom)>0) && ($data_custom=mysqli_fetch_array($res_custom)) ){
			$fullfill=substr("0000".$data_custom['fulfil_no'],-4);
			$custom[0][]=$data_custom['order_no'].$fullfill;
			$custom[1][]=$data_custom['pcode'].(string)str_replace('.', '', $data_custom['pskew']);
			$custom[2][]=$data_custom['GOD_ORDER_STATUS'];
			if($this->KIT_BASED=='Y'){
				$custom[3][]=$data_custom['pqty'];
				$cl_type = 'SPECIFIC';
				if($data_custom['pcode']=='300'){
					$cl_type = 'HQ';
				}
				$custom[4][]= $cl_type;
			}
		}
		return $custom;
	}
	
	
	/**
	 * @return boolean
	 */
	private function check_productkey(){
		    $this->PRODUCT_KEY=(string)str_replace('-','',$this->parsed_xml['PRODUCT_KEY']);
		    if(strlen($this->PRODUCT_KEY)<24){
				$this->GLC_ERROR_CODE='PK04';
				return false;
			}
			$spk=split_pkey_from_fullno($this->PRODUCT_KEY);
			$prodkeyfull=$spk[0];			$fullfill_no=$spk[1]; 
			$eswaptonormal=do_encryption_productkey_sn($prodkeyfull);
			$product_key=/*. (string[string]) .*/ array();			
			$product_key["customer_name"]=substr($eswaptonormal,0,4);
			$product_key["order_no"]=substr($eswaptonormal,4,4);
			$product_key["hard_disk"]=$ehdd_sn=substr($eswaptonormal,8,4);
			$product_key["productcode"]=substr($eswaptonormal,12,4);
			$product_key["product_skew"]=substr($eswaptonormal,16,4);
			$eprodkey_sn=$product_key["productcode"].$product_key["product_skew"];
			$product_key["inst_type"]=substr($ehdd_sn,3,1);
			$product_key["noclients"]=substr($eswaptonormal,20,2);
			$product_key['fullfillment_no']=$fullfill_no;
		
		    if($product_key["order_no"] != do_encryption_order($this->ORDER_NO)){
		    	$this->GLC_ERROR_CODE='PK01';
		    	$this->GLC_ADDITIONAL_ERROR_MESSAGE=$product_key["order_no"] ." not match with ".do_encryption_order($this->ORDER_NO);
				return false;
		    }else if(substr($product_key["hard_disk"],0,3)!=do_encryption_hdd($this->parsed_xml['HDD_KEY']) and (isset($this->parsed_xml['OFFLINE']) and $this->parsed_xml['OFFLINE']!="YES")){
		    	$this->GLC_ERROR_CODE='PK02';
		    	$this->GLC_ADDITIONAL_ERROR_MESSAGE=$product_key["hard_disk"]." not match with".do_encryption_hdd($this->parsed_xml['HDD_KEY']);
				return false;
//		    }else if($this->GID_LIC_HARD_DISK_ID!='' and (substr($product_key["hard_disk"],0,3)!=substr($this->GID_LIC_HARD_DISK_ID,0,3)) and (isset($this->parsed_xml['OFFLINE']) and $this->parsed_xml['OFFLINE']=="YES")){
//		    	$this->GLC_ERROR_CODE='PK02';
//		    	$this->GLC_ADDITIONAL_ERROR_MESSAGE=$product_key["hard_disk"]." not match with".substr($this->GID_LIC_HARD_DISK_ID,0,3);
// 	return false;
		    }else if($eprodkey_sn!=do_encryption_prodid($this->parsed_xml['PRODUCT_ID'])){
		    	$this->GLC_ERROR_CODE='PK03';
		    	$this->GLC_ADDITIONAL_ERROR_MESSAGE=$eprodkey_sn." not match with ".do_encryption_prodid($this->parsed_xml['PRODUCT_ID']);
				return false;
		    }
		
			return true;	
	}


/**
 * @return boolean
 */
	function find_order_no_belongs_to_which_product(){
		$splitable_order=0;
		if( !empty($this->ORDER_NO) ){
			$query_order_no_exists="select GOD_ORDER_SPLICT from  gft_order_hdr where god_order_no='$this->ORDER_NO' ";
			$result_order_no_exists=execute_my_query($query_order_no_exists);
			if(mysqli_num_rows($result_order_no_exists)==0){
				$this->GLC_ERROR_CODE='E027';
		    	$this->GLC_ADDITIONAL_ERROR_MESSAGE='Order Number not available in SAM ';
		    	return false;
			}else{
				$qd=mysqli_fetch_array($result_order_no_exists);
				$splitable_order=(int)$qd['GOD_ORDER_SPLICT'];
			}
			
		}
		
		if(!empty($this->PCODE) and !empty($this->ORDER_NO) and !empty($this->FULLFILLMENT_NO) ){
			
			$query_check_install="select GPM_PRODUCT_SKEW,GPM_PRODUCT_NAME,GPM_SKEW_DESC from gft_install_dtl_new ins " .
					"join gft_product_master pm on (GPM_PRODUCT_CODE=GID_LIC_PCODE AND GPM_PRODUCT_SKEW=GID_LIC_PSKEW) " .
					"join gft_product_family_master pfm on (pfm.gpm_product_code=pm.GPM_PRODUCT_CODE) " .
					"where gid_order_no='$this->ORDER_NO' and GID_HEAD_OF_FAMILY='$this->HEAD_OF_FAMILY'  " .
					"and gid_lic_fullfillment_no=$this->FULLFILLMENT_NO ";
			$result=execute_my_query($query_check_install);
			if(mysqli_num_rows($result)==1){
				$this->GLC_ERROR_CODE='E028';
				$qd=mysqli_fetch_array($result);
				$this->GLC_ADDITIONAL_ERROR_MESSAGE='Purchased '.$qd['GPM_PRODUCT_NAME'].'-'.$qd['GPM_SKEW_DESC'];
				return false;
			}
		}
		
		if(!empty($this->HEAD_OF_FAMILY) and !empty($this->ORDER_NO) and !empty($this->FULLFILLMENT_NO) ){
			
			$query_check_gop="select GPM_PRODUCT_SKEW,GPM_PRODUCT_NAME,GPM_SKEW_DESC from gft_order_product_dtl  " .
					"join gft_product_master pm on (pm.GPM_PRODUCT_CODE=gop_product_code AND GPM_PRODUCT_SKEW=gop_product_skew) " .
					"join gft_product_family_master pfm on (pfm.gpm_product_code=pm.GPM_PRODUCT_CODE) " .
					"where gop_order_no='$this->ORDER_NO' and GPM_HEAD_FAMILY='$this->HEAD_OF_FAMILY'  " .
					"and GOP_FULLFILLMENT_NO=$this->FULLFILLMENT_NO and GFT_SKEW_PROPERTY in (1,11)";
					
			if($splitable_order==1){
				$query_check_gop="select GPM_PRODUCT_SKEW,GPM_PRODUCT_NAME,GPM_SKEW_DESC from gft_cp_order_dtl  " .
				"join gft_product_master pm on (pm.GPM_PRODUCT_CODE=gco_product_code AND GPM_PRODUCT_SKEW=gco_skew) " .
				"join gft_product_family_master pfm on (pfm.gpm_product_code=pm.GPM_PRODUCT_CODE) " .
				"where gco_order_no='$this->ORDER_NO' and GPM_HEAD_FAMILY='$this->HEAD_OF_FAMILY'  " .
				"and GCO_FULLFILLMENT_NO=$this->FULLFILLMENT_NO and GFT_SKEW_PROPERTY in (1,11)";
				
			}
			$result=execute_my_query($query_check_gop);
			if(mysqli_num_rows($result)==1){
				$this->GLC_ERROR_CODE='E028';
				$qd=mysqli_fetch_array($result);
				$this->GLC_ADDITIONAL_ERROR_MESSAGE='Purchased '.$qd['GPM_PRODUCT_NAME'].'-'.$qd['GPM_SKEW_DESC'];
				return false;
			}
		}
		
		if(!empty($this->ORDER_NO) and !empty($this->FULLFILLMENT_NO) ){
			
				$query_check_gop="select gop_order_no,group_concat(concat(GPM_PRODUCT_NAME,'-',GPM_SKEW_DESC)) prod from gft_order_product_dtl  " .
					"join gft_product_master pm on (pm.GPM_PRODUCT_CODE=gop_product_code AND GPM_PRODUCT_SKEW=gop_product_skew) " .
					"join gft_product_family_master pfm on (pfm.gpm_product_code=pm.GPM_PRODUCT_CODE) " .
					"where gop_order_no='$this->ORDER_NO'  " .
					"and GOP_FULLFILLMENT_NO=$this->FULLFILLMENT_NO and GFT_SKEW_PROPERTY in (1,11) GROUP BY gop_order_no,GOP_FULLFILLMENT_NO ";
					
			if($splitable_order==1){
				$query_check_gop="select gco_order_no,group_concat(concat(GPM_PRODUCT_NAME,'-',GPM_SKEW_DESC)) prod from gft_cp_order_dtl  " .
				"join gft_product_master pm on (pm.GPM_PRODUCT_CODE=gco_product_code AND GPM_PRODUCT_SKEW=gco_skew) " .
				"join gft_product_family_master pfm on (pfm.gpm_product_code=pm.GPM_PRODUCT_CODE) " .
				"where gco_order_no='$this->ORDER_NO'  " .
				"and GCO_FULLFILLMENT_NO=$this->FULLFILLMENT_NO and  GFT_SKEW_PROPERTY in (1,11) GROUP BY gco_order_no,GCO_FULLFILLMENT_NO ";
				
			}
			$result=execute_my_query($query_check_gop);
			if(mysqli_num_rows($result)>0){
				$this->GLC_ERROR_CODE='E028';
				$qd=mysqli_fetch_array($result);
				$this->GLC_ADDITIONAL_ERROR_MESSAGE='Purchased '.$qd['prod'];
				return false;
			}
			
		}
		if($splitable_order==1 ){
			/* check it not splitted */
			$query_check_gop="select gco_order_no,group_concat(concat(GPM_PRODUCT_NAME,'-',GPM_SKEW_DESC)) prod from gft_cp_order_dtl  " .
				"join gft_product_master pm on (pm.GPM_PRODUCT_CODE=gco_product_code AND GPM_PRODUCT_SKEW=gco_skew) " .
				"join gft_product_family_master pfm on (pfm.gpm_product_code=pm.GPM_PRODUCT_CODE) " .
				"where gco_order_no='$this->ORDER_NO'  " .
				"GROUP BY gco_order_no ";
			$result=execute_my_query($query_check_gop);
			if(mysqli_num_rows($result)==0){
				$this->GLC_ERROR_CODE='E020';
				$qd=mysqli_fetch_array($result);
				$this->GLC_ADDITIONAL_ERROR_MESSAGE='';
				return false;
			}	
				
		}
		
		
		return false;	
	}
	
	/**
	 * @return boolean
	 */
	protected function check_local_user_old(){  //for backward compatibility
		$query=" select GEM_EMP_ID,GEM_EMP_NAME,LIC_HDD_LU,LIC_SNO_LU,GEM_EMAIL,concat(substr(gem_mobile,-10),substr(concat('00000',gem_emp_id),-5)) order_no " .
				" from gft_emp_master " .
				" left join gft_lic_localuser lic on (LIC_EMP_ID=gem_emp_id and LIC_STATUS_LU='A' and LIC_HDD_LU='".$this->parsed_xml['HDD_KEY']."' )" .
				" where gem_status='A' ";
		if(!empty($this->ORDER_NO) and !empty($this->ORDER_NO)){
			$query.=" and concat(substr(gem_mobile,-10),substr(concat('00000',gem_emp_id),-5))='".$this->ORDER_NO."' ";
		}
		else if(isset($this->parsed_xml['EMAIL']) and $this->parsed_xml['EMAIL']!='' and $this->ORDER_NO==''){
			$query.=" and gem_email='".$this->parsed_xml['EMAIL']."'";
		}else{
			return false;
		}
		$result=execute_my_query($query);
		if(mysqli_num_rows($result)>0){
			$qd=mysqli_fetch_array($result);
			$this->LOCAL_USER = 'Y';
			if($qd['LIC_HDD_LU']!=''){
				$this->GID_ORDER_NO=$qd['order_no'];
				$this->GID_FULLFILLMENT_NO=($this->FULLFILLMENT_NO > 0?$this->FULLFILLMENT_NO:1);
				$this->GID_LIC_PCODE=$this->PCODE;
				$this->GID_LIC_PSKEW=$this->PSKEW=isset($this->PSKEW)?$this->PSKEW:$this->parsed_xml['PRODUCT_GROUP'].'SL';
				$this->GID_NO_CLIENTS=0;
				$this->GID_NO_COMPANYS=0;
				$this->GID_EXPIRE_FOR=1;
				$this->GPM_LICENSE_TYPE=1;
				$this->LICENSE_TYPE_NAME='PERPETUAL';
	
				if($this->WEB_REQUEST_TYPE==1) {
					//old local user //Backward compatible
					$prod_id=$this->PCODE.$this->GID_LIC_PSKEW;
				}else{
					$prod_id=$this->parsed_xml['PRODUCT_FCODE'].$this->GID_LIC_PSKEW;
				}
				$this->PRODUCT_ID=(string)str_replace('.','',$prod_id);
				$this->GLH_CUST_NAME=$qd['GEM_EMP_NAME'];
				$this->LOCAL_USER_ID=(int)$qd['GEM_EMP_ID'];
				$this->LOCAL_USER_NAME=$qd['GEM_EMP_NAME'];
				return true;
			}
			else{
				$this->GLC_ERROR_CODE='L001'; /* Not mapped in local user */
				return false;
			}
		}else{
			return false;
		}
	
	}
	
	/**
	 * @return void
	 */
	public function check_local_hq_license(){
		$allowed_ip = explode(',',get_samee_const('LOCAL_IP_ADDRESS_SETUPWIZARD'));
		$query=" select GEM_EMP_ID,GEM_EMP_NAME,GEM_EMAIL,concat(substr(gem_mobile,-10),substr(concat('00000',gem_emp_id),-5)) order_no " .
				" from gft_emp_master  where gem_status='A' ";
		if(!empty($this->ORDER_NO)){
			$this->FULLFILLMENT_NO=$fullfill=substr($this->parsed_xml['ORDER_NO'],15,4);
			$query.=" and concat(substr(gem_mobile,-10),substr(concat('00000',gem_emp_id),-5))='".$this->ORDER_NO."' and '$fullfill'='0001' ";
		}
				
		$result=execute_my_query($query);
		if(mysqli_num_rows($result)>0){
			if(in_array($_SERVER['REMOTE_ADDR'], $allowed_ip)){
				$qd=mysqli_fetch_array($result);
				$this->LOCAL_USER = 'Y';
				$this->GID_ORDER_NO=$qd['order_no'];
				$this->GID_FULLFILLMENT_NO=($this->FULLFILLMENT_NO > 0?$this->FULLFILLMENT_NO:1);
				$this->GID_LIC_PCODE=$this->PCODE;
				$this->GID_LIC_PSKEW=$this->PSKEW=$this->parsed_xml['PRODUCT_GROUP'].'PL';
				$this->GID_NO_CLIENTS=15;
				$this->GID_NO_COMPANYS=0;
				$this->GID_EXPIRE_FOR=2;
				$this->GPM_LICENSE_TYPE=2;
				$this->LICENSE_TYPE_NAME='SUBSCRIPTION';
				$prod_id=$this->parsed_xml['PRODUCT_FCODE'].$this->GID_LIC_PSKEW;
				$this->PRODUCT_ID=(string)str_replace('.','',$prod_id);
				$this->GLH_CUST_NAME=$qd['GEM_EMP_NAME'];
				$this->LOCAL_USER_ID=(int)$qd['GEM_EMP_ID'];
				$this->LOCAL_USER_NAME=$qd['GEM_EMP_NAME'];
			}else{
				$this->GLC_ERROR_CODE='E036';
				return;				
			}
		}
	}
	
	/**
	 * @return string
	 */
	public function generate_lead(){
		$k=1;
		$array_cno=/*. (string[int]) .*/array();
		$array_conper=/*. (string[int]) .*/array();
		$contact_type=/*. (string[int]) .*/array();
		$designation=/*. (string[int]) .*/array();
		if(isset($this->parsed_xml['BUS_PHONE']) and $this->parsed_xml['BUS_PHONE']!=''){
			$array_conper[$k]=(isset($this->parsed_xml['CONTACT_NAME'])?$this->parsed_xml['CONTACT_NAME']:'');
			$array_cno[$k]=trim($this->parsed_xml['BUS_PHONE']);
			$contact_type[$k]='2';
			$designation[$k]='1';
			$k++;
		}
		if(isset($this->parsed_xml['MOBILE_NO']) and $this->parsed_xml['MOBILE_NO']!=''){
			$array_conper[$k]=(isset($this->parsed_xml['CONTACT_NAME'])?$this->parsed_xml['CONTACT_NAME']:'');
			$array_cno[$k]=trim($this->parsed_xml['MOBILE_NO']);
			$contact_type[$k]='1';
			$designation[$k]='1';
			$k++;
		}
		if(isset($this->parsed_xml['EMAIL']) and $this->parsed_xml['EMAIL']!=''){
			$array_conper[$k]=(isset($this->parsed_xml['CONTACT_NAME'])?$this->parsed_xml['CONTACT_NAME']:'');
			$array_cno[$k]=trim($this->parsed_xml['EMAIL']);
			$contact_type[$k]='4';
			$designation[$k]='1';
		}
	
	
		$product_arr[0]=$this->PCODE;
		$product_skew=substr($this->PSKEW,3,5);
		$product_skew=substr($product_skew,0,2).'.'.substr($product_skew,2,3);
		$dtls_array['VERTICAL']=$this->parsed_xml['VERTICAL'];
	
		$lead_arr['GLH_CUST_NAME']=isset($this->parsed_xml['SHOP_NAME'])?$this->parsed_xml['SHOP_NAME']:'';
		$lead_arr['GLH_CUST_STREETADDR1']=isset($this->parsed_xml['ADDRESS'])?$this->parsed_xml['ADDRESS']:'';
		//		$lead_arr['GLH_CUST_STREETADDR2']=$dtls_array['address_2'];
		//		$lead_arr['GLH_AREA_NAME']=$dtls_array['area_name'];
		//		$lead_arr['GLH_LANDMARK']=$dtls_array['GLH_LANDMARK'];
		$lead_arr['GLH_CUST_CITY']=isset($this->parsed_xml['CITY'])?$this->parsed_xml['CITY']:'';
		$lead_arr['GLH_CUST_STATECODE']=isset($this->parsed_xml['STATE'])?$this->parsed_xml['STATE']:'';
		$lead_arr['GLH_CUST_PINCODE']=isset($this->parsed_xml['PINCODE'])?$this->parsed_xml['PINCODE']:'';
		$lead_arr['GLH_STATUS']='26'; //Lead status 26 - New
		$lead_arr['GLH_COUNTRY']=isset($this->parsed_xml['COUNTRY'])?$this->parsed_xml['COUNTRY']:'';
		$lead_arr['GLH_VERTICAL_CODE']=$dtls_array['VERTICAL'];
		$lead_arr['GLH_LEAD_SOURCE_CODE_INTERNAL']='';
		$lead_arr['GLH_CREATED_CATEGORY']='30';
		$lead_arr['GLH_LEAD_TYPE']='1';
		if($this->INSTALLED_BY!='self' && $this->INSTALLED_BY!=''){
			$installed_by_dtl = get_emp_master($this->INSTALLED_BY,'',null,false);
			if($installed_by_dtl[0][11]=='1'){  //if cp employee
				$lead_arr['GLH_LFD_EMP_ID']=$installed_by_dtl[0][12];  //Partner of the CP employee
				$lead_arr['GLH_CREATED_BY_EMPID']=$installed_by_dtl[0][12];
			}else{
				$lead_arr['GLH_LFD_EMP_ID']=$this->INSTALLED_BY;
				$lead_arr['GLH_CREATED_BY_EMPID']=$this->INSTALLED_BY;
			}
		}
		if($this->IS_DEALER){
			$lead_arr['GLH_LEAD_TYPE']			=	'7';
			$lead_arr['GLH_LFD_EMP_ID']			=	$this->DEALER_ID;
			$lead_arr['GLH_CREATED_BY_EMPID']	=	$this->DEALER_ID;
		}else if($this->LOCAL_USER=='Y'){
			$lead_arr['GLH_LEAD_TYPE']='8';
		}
		$lead_create_status=array_insert_new_lead_db($lead_arr,$product_arr,$array_conper,$array_cno,$designation,$contact_type,null,'off',null,null,null,'website');
		$cust_id=$lead_create_status[1];
		if( $this->IS_DEALER && $lead_create_status[0]){
			execute_my_query("update gft_lead_hdr set GLH_CREATED_CATEGORY='39',GLH_LEAD_TYPE=7 where GLH_LEAD_CODE='$cust_id'");
		}
		$lmt_incharge=$lead_create_status[5];
		if( ((int)$lmt_incharge)==0 ){
			$lmt_incharge = get_lead_mgmt_incharge($lead_arr['GLH_COUNTRY'],$lead_arr['GLH_CUST_STATECODE'],(int)$lead_arr['GLH_CREATED_CATEGORY'],(int)$lead_arr['GLH_VERTICAL_CODE']);
		}
		if( ($this->LOCAL_USER!='Y') && ($lead_arr['GLH_LEAD_TYPE']!='7') ){
			if($lead_create_status[0]){
				mail_to_webcustomer_product_info($cust_id, (string)$this->PCODE, $this->PSKEW, 'y', $this->ORDER_NO, $this->parsed_xml['CONTACT_NAME'], $this->parsed_xml['EMAIL'],$this->parsed_xml['VERTICAL'],true);
				mail_to_know_web_registered_dtl($cust_id,(string)$this->PCODE, $this->PSKEW);
			}
			$activity_dtl	=/*. (string[string]) .*/	array();
			$activity_dtl['GLD_LEAD_CODE']	=	$cust_id;
			$activity_dtl['GLD_EMP_ID']		=	$lmt_incharge;
			$activity_dtl['GLD_DATE']		=	date('Y-m-d H:i:s');
			$activity_dtl['GLD_VISIT_DATE']	=	date('Y-m-d');
			$activity_dtl['GLD_NOTE_ON_ACTIVITY']="Trial Registered";
			$activity_dtl['GLD_CUST_FEEDBACK']="Trial Registered";
			$activity_dtl['GLD_LEAD_STATUS']='26';
			$activity_dtl['GLD_CALL_STATUS']="P";
			$activity_dtl['GLD_REPEATED_VISITS']="N";
			$activity_dtl['GLD_INTEREST_ADDON']="U";
			$activity_dtl['GLD_SCHEDULE_STATUS']='1';
			$activity_dtl['GLD_INTEREST_ADDON']="U";
			$activity_dtl['GLD_VISIT_NATURE']='1';
			$activity_dtl['GLD_NEXT_ACTION_DATE']=date('Y-m-d');
			$activity_dtl['GLD_NEXT_ACTION']='62';
			insert_in_gft_activity_table($activity_dtl,null,true);
		}
		return $cust_id;
	}
	
	/**
	 * @param string $stat
	 *
	 * @return void
	 */
	public function update_license_status($stat){
		$this->check_order_split();
		if($this->ORDER_SPLIT==1){
			$up_query = "UPDATE gft_cp_order_dtl set GCO_LICENSE_STATUS='$stat' where ".
					"GCO_ORDER_NO='$this->ORDER_NO' and GCO_FULLFILLMENT_NO='$this->FULLFILLMENT_NO' ".
					"and GCO_PRODUCT_CODE='$this->PCODE'";
		}else{
			$up_query = "UPDATE gft_order_product_dtl set GOP_LICENSE_STATUS='$stat' where ".
					"GOP_ORDER_NO='$this->ORDER_NO' and GOP_FULLFILLMENT_NO='$this->FULLFILLMENT_NO' ".
					"and GOP_PRODUCT_CODE='$this->PCODE'";
		}
		$up_res=execute_my_query($up_query);
		if(!$up_res){
			$this->GLC_ERROR_CODE='DB01';
			return;
		}
	}
	
}
?>
