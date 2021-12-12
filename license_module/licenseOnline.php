<?php
require_once(__DIR__."/../dbcon.php");
require_once(__DIR__."/licenseClass.php");

class licenseOnline extends licenseClass{
	
		
	/**
	 * @param string $xml_tags
	 * @return void
	 */
	private function process_starter_request($xml_tags){
		$xml_header_tags=/*. (string[int]) .*/array("SHOP_NAME","CONTACT_NAME","ADDRESS","CITY","COUNTRY","STATE","PINCODE", 
	    	 "BUS_PHONE","PER_PHONE","EMAIL","FIND_US","DETAILS","PRODUCT_ID","HDD_KEY","VERTICAL","VERSION");
		$this->parsed_xml=$this->give_parsed_xml($xml_tags,$xml_header_tags);
		$mandatory_fields=/*. (string[int]) .*/array("PRODUCT_ID","VERSION");
		if(!$this->check_necessary_tags($mandatory_fields)){
		   return ;
		}
		if(!$this->check_above_minimum_version($this->PCODE,$this->parsed_xml['VERSION'],$this->PRODUCT_GROUP,'lowest')){
		   return ;	
		}
		$this->GLC_ERROR_CODE='E003';
		return ;
	}
	
	/**
	 * @param string $xml_tags
	 * @return void
	 */
	private function process_license_request($xml_tags){
		if($this->WEB_REQUEST_TYPE==1){
			$xml_header_tags=/*. (string[int]) .*/array("USER_NAME", "PASSWORD", "LICENCED_TO", "ORDER_NO", "PRODUCT_ID", "HDD_KEY",
		  		"INST_TYPE", "PRODUCT_KEY","DACE","VERSION","VERTICAL","RREASON","OFFLINE","EMP_ID");
		  	$mandatory_fields=/*. (string[int]) .*/array("LICENCED_TO","ORDER_NO","PRODUCT_ID","HDD_KEY","VERSION");
		}else{
			$xml_header_tags=/*. (string[int]) .*/array("ORDER_NO", "PRODUCT_FCODE","PRODUCT_GROUP","PRODUCT_ID","HDD_KEY","VERSION","VERTICAL","RREASON","SEND_ACTIVATION_KEY","PERIODIC_CHECK","OFFLINE","EMP_ID","HKEY","MACHINE_NAME","OTP","PASSWORD","DEALER_ID","INSTALL_TYPE");
		  	$mandatory_fields=/*. (string[int]) .*/array("ORDER_NO","PRODUCT_FCODE","PRODUCT_GROUP","HDD_KEY","VERSION");
		}
		$this->parsed_xml=$this->give_parsed_xml($xml_tags,$xml_header_tags);
			  	
		if(!$this->check_necessary_tags($mandatory_fields)){
		    return;	
		}		
		
		//custom license check
		$custom_info=explode('-',check_for_custom_and_uat_license($this->ORDER_NO,$this->FULLFILLMENT_NO));
		$this->CUSTOM_TYPE=isset($custom_info[0])?$custom_info[0]:'';
		$this->REFERENCE_ORDER=isset($custom_info[1])?$custom_info[1]:'';
		$this->REFERENCE_FULLFILLMENT=isset($custom_info[2])?$custom_info[2]:'';
		$this->UAT=isset($custom_info[3])?$custom_info[3]:'N';
/* 		if($this->CUSTOM_TYPE==8){
//			$status=get_order_approval_status($this->ORDER_NO);
				$this->GLC_ERROR_CODE='E011';
				return;
		} */
		
		//$this->check_local_user();
		if($this->LOCAL_USER === 'Y'){
			if($this->LOCAL_USER_ID!=0 and $this->GID_ORDER_NO!=''){
				$this->GID_EXPIRE_FOR=1;
				$this->GID_VALIDITY_DATE=date('Y-m-d',mktime('0','0','0',date('m'),((int)date('d')+365),date('Y')));
				$this->generate_pkey_akey();
				return;
			}else if($this->GLC_ERROR_CODE !== ''){
				return;
			}
		}
		
		$install_check=$this->get_install_id_license_ifexits();/* Its intalled ? */
		$to_check = 'lowest';
		if($install_check==false){ //new installation
			$to_check = 'highest';
		}
		$this->check_above_minimum_version($this->PCODE,$this->parsed_xml['VERSION'],$this->PRODUCT_GROUP,$to_check);

		if ($this->GLC_ERROR_CODE !== ''){
			//Return if ERROR is got in get_install_id_license_ifexits() or minimum version check
			return;
		}
		$lic_status = '';
		$is_valid_otp = false;
		if($this->WEB_REQUEST_TYPE=='27'){
			$this->OTP		= isset($this->parsed_xml['OTP'])?$this->parsed_xml['OTP']:'';
			$is_valid_otp 	= $this->validate_otp_for_hkey_change();
			if(!$is_valid_otp){
				return;
			}
 			//commented below lines as per mail from Annuity Team and Elamaran dated 12 Sep 2017 stating that changing license status in reinstallation creates manual work for annuity team to approve and they don't find the benefit in doing this
		/*	$last_reins_on	=	get_last_reinstall_date($this->GID_INSTALL_ID);
			if($last_reins_on!=''){
				$diff_days = datediff($last_reins_on, date('Y-m-d'));
				$lic_status = $this->get_license_status();
				if( ($diff_days < 45) && (in_array($lic_status, array('2','5'))) ){
					$this->update_license_status('4');
					$support_id = insert_support_entry($this->GID_LEAD_CODE, $this->GID_LIC_PCODE, $this->GID_LIC_PSKEW, $this->VERSION, '', SALES_DUMMY_ID, 
							'', 'Reinstalled with in 45 days', 139, 'T20');
				}
			} */
			//resetting backup & restore token during reinstallation
			$gen_token = uniqid(null,true);
			$con_que =  " update gft_install_dtl_new set GID_CONNECTPLUS_TOKEN='$gen_token' ".
						" where GID_LEAD_CODE='$this->GID_LEAD_CODE' and GID_LIC_PCODE=706 and GID_STATUS!='U' ";
			execute_my_query($con_que);
			post_token_to_authservice($this->GID_LEAD_CODE);
		}

		if($install_check===false and $this->GID_INSTALL_ID!=0){
			return;
		}
		if($install_check===true and $this->GID_INSTALL_ID!=0){
			if($lic_status==''){
				$lic_status = $this->get_license_status();
			}
			if($this->POS_UAT=='Y'){
				$this->GID_VALIDITY_DATE=$this->UAT_VALIDITY;
			}
		//	$asa_val=get_samee_const('ASA_Expired');
			$expire_date = $trial_till = '';
			$get_prod = "select GID_PRODUCT_CODE,GID_TRIAL_TILL_DATE from gft_install_dtl_new where GID_INSTALL_ID='$this->GID_INSTALL_ID'";
			$get_res = execute_my_query($get_prod);
			if($get_data=mysqli_fetch_array($get_res)){
				$this->GID_PRODUCT_CODE=$get_data['GID_PRODUCT_CODE'];
				$trial_till = $get_data['GID_TRIAL_TILL_DATE'];
			}
			$sel_que="select GLA_EXPIRE_DATE,GLA_NOOF_DAYS from gft_lic_approved_log where GLA_ORDER_NO='$this->ORDER_NO'".
					" and GLA_FULLFILLMENT_NO=$this->FULLFILLMENT_NO and GLA_PRODUCT_CODE='$this->GID_PRODUCT_CODE' ";
			$sel_que.="order by GLA_ID desc limit 1";
			$res_que = execute_my_query($sel_que);
			if((mysqli_num_rows($res_que)==1) && $data=mysqli_fetch_array($res_que)){
				$expire_date=$data['GLA_EXPIRE_DATE'];
			}
			/* check Harddisk ID */
			$hdd_id = $this->parsed_xml['HDD_KEY'];
			if( ($this->HKEY!='') && (!$this->IS_DFT_ORDER_NO_FOR_POS) ){  //uuid check
			    if( ($this->GPM_LICENSE_TYPE!='3') && ($this->GID_SYS_HKEY=='') && ($this->GID_SYS_HDD_ID!=$this->parsed_xml['HDD_KEY']) && !$is_valid_otp ){
					$this->GLC_ERROR_CODE = "E037";
					return;
				}
				if( ($this->GID_SYS_HKEY!='') && ($this->GID_SYS_HKEY!=$this->HKEY) ){
				    $install_type = isset($this->parsed_xml['INSTALL_TYPE'])?$this->parsed_xml['INSTALL_TYPE']:"";
				    if($install_type==""){ //only license sync request
				        $is_gosecure_customer = is_product_installed($this->GID_LEAD_CODE, 706);
				        $is_previous_hkey = is_previous_hkey($this->GID_INSTALL_ID,$this->HKEY);
				        if($is_gosecure_customer && $is_previous_hkey && is_gosecure_provisioned($this->GID_LEAD_CODE)){ //gosecure customer
				            $this->GLC_ERROR_CODE = "E038";
				            return;
				        }
				    }
				    if( ($this->GPM_LICENSE_TYPE!='3') && (!$is_valid_otp) && ($this->GAR_APPROVED_FLAG!='Y') ){
						// commented below lines as per mail from Annuity Team and Elamaran dated 12 Sep 2017 stating that blocking license in old harddisk reinstallation creates manual work for annuity team to approve and they don't find the benefit in doing this
						/* if(is_previous_hkey($this->GID_INSTALL_ID,$this->HKEY)){
							$this->GLC_ERROR_CODE = "E038";
							$support_id = insert_support_entry($this->GID_LEAD_CODE, $this->GID_LIC_PCODE, $this->GID_LIC_PSKEW, $this->VERSION, '', SALES_DUMMY_ID,
									'', 'Trying Reinstallation From Blocked Machine', 139, 'T20');
						}else{ */
							$this->GLC_ERROR_CODE = "E037";
						//}
						return;
					}
				}
			}else{	//harddisk id check for backward compatibility
				if($this->GID_SYS_HDD_ID!='' and $this->GID_SYS_HDD_ID!=$this->parsed_xml['HDD_KEY']
				and (string)$this->GAR_APPROVED_FLAG!=='Y' and empty($this->parsed_xml['OFFLINE'])){
					if($this->GPM_LICENSE_TYPE=='3'){
						execute_my_query("update gft_install_dtl_new set GID_SYS_HDD_ID='$hdd_id' where GID_INSTALL_ID='$this->GID_INSTALL_ID'");
					}else{
						$this->GLC_ERROR_CODE='E008'; /* Hard disk id mismatch */
						return;
					}
				}else if(((string)$this->GID_LIC_HARD_DISK_ID!=='' and empty($this->parsed_xml['OFFLINE']) and ($this->PCODE!=120) and  
						$this->GID_LIC_HARD_DISK_ID!=do_encryption_hdd($this->parsed_xml['HDD_KEY']) and (string)$this->GAR_APPROVED_FLAG!=='Y') ){
					if($this->GPM_LICENSE_TYPE=='3'){
						execute_my_query("update gft_install_dtl_new set GID_LIC_HARD_DISK_ID='".do_encryption_hdd($hdd_id)."' where GID_INSTALL_ID='$this->GID_INSTALL_ID'");
					}else{
						$this->GLC_ERROR_CODE='E008'; /* Hard disk id mismatch */
						return;
					}
				}
			}
			
			//checking for bulk government holidays and updating their expiry date to next configure date
			$sending_expiry_date = $this->annuity_valid;
			if($sending_expiry_date==''){
				$sending_expiry_date = $this->GID_VALIDITY_DATE;
			}
			if($sending_expiry_date>='2020-03-01' && $sending_expiry_date < date('Y-m-d') && $this->GID_LIC_PCODE=='500' && substr($this->GID_LIC_PSKEW,0,4)=='06.5' && $this->GPM_LICENSE_TYPE!=3){
			   $paid_serveeasy_expired_customer = " select GTM_BUSINESS_TYPE from gft_lead_hdr ".
			   " join gft_vertical_master on (GLH_VERTICAL_CODE=GTM_VERTICAL_CODE) ".
			   " where glh_lead_code=".$this->GID_LEAD_CODE." and glh_country='India'  ";
			  $restaurant_business = get_single_value_from_single_query("GTM_BUSINESS_TYPE", $paid_serveeasy_expired_customer);
			  if($restaurant_business==16){//businee type - Restaurant
			      $sending_expiry_date = "2020-12-31";
			      $this->annuity_valid = $sending_expiry_date;
			      $this->GID_VALIDITY_DATE = $sending_expiry_date;
			  }
			}
			$alternate_expiry_date = get_alternate_day_for_holiday($sending_expiry_date);
			if($alternate_expiry_date!=$sending_expiry_date){
				$this->annuity_valid = $alternate_expiry_date;
				$this->GID_VALIDITY_DATE = $alternate_expiry_date;
			}
			
			if($this->GID_EXPIRE_FOR==2 and $this->GID_VALIDITY_DATE < date('Y-m-d')){
				if($lic_status=='5'){ //annuity approved
					if( ($expire_date!='') && (date('Y-m-d') < $expire_date) ){
						$this->annuity_valid=$expire_date;
						$this->LICENSE_TYPE_NAME="Trial";
						$this->GPM_LICENSE_TYPE="3";
					}else{
						$this->GLC_ERROR_CODE='E009'; /* Subscription Period Expired */
						return;
					}
				}else{
					$this->GLC_ERROR_CODE='E009'; /* Subscription Period Expired */
					return;
				}
			}
			else if($this->GID_EXPIRE_FOR==1 and $this->GID_VALIDITY_DATE < date('Y-m-d')){
				if($lic_status=='5'){ //annuity approved
					if( ($expire_date!='') && (date('Y-m-d') < $expire_date) ){
						$this->annuity_valid=$expire_date;
						$this->LICENSE_TYPE_NAME="Trial";
						$this->GPM_LICENSE_TYPE="3";
					}else{
						$this->GLC_ERROR_CODE='E010'; /* ASA Period Expired */
						return;
					}
				} elseif($this->CUSTOM_TYPE==8){  //custom license
					$val_que = " select GID_VALIDITY_DATE from gft_install_dtl_new ".
							" where GID_ORDER_NO='$this->REFERENCE_ORDER' and GID_FULLFILLMENT_NO='$this->REFERENCE_FULLFILLMENT' ".
							" and GID_LIC_PCODE=$this->PCODE";
					$val_res = execute_my_query($val_que);
					if( (mysqli_num_rows($val_res)==1) && ($val_data=mysqli_fetch_array($val_res)) ){
						if($val_data['GID_VALIDITY_DATE'] < date('Y-m-d') ){
							$this->GLC_ERROR_CODE='E010'; // ASA Period Expired 
							return;
						}
					}
				}elseif ($trial_till > $this->GID_VALIDITY_DATE){
					$this->annuity_valid=$trial_till;
				}else{
					$this->GLC_ERROR_CODE='E010'; /* ASA Period Expired */
					return;
				}
			}
			else if(strcasecmp($this->GID_ORDER_NO,substr($this->ORDER_NO,0,15))!=0){
				$this->GLC_ERROR_CODE='E011'; /* Activate with Root Order Number  */
				return;
			}
			
		}/* if install id avail*/
		else{
			$check_orders=$this->check_exist_order_no($this->ORDER_NO,$this->FULLFILLMENT_NO,$this->PCODE,$this->PRODUCT_GROUP,$this->PSKEW);
			if($check_orders==false){
				if($this->GLC_ERROR_CODE!=''){
					return;
				}
				$this->find_order_no_belongs_to_which_product();
				return;
			}			
		}
		
		$this->generate_pkey_akey();		
	}
	
	/**
	 * @return void
	 */
	function __construct(){
		$this->WEB_REQUEST_TYPE=1;
			parent :: __construct();
			if(empty($this->GLC_ERROR_CODE)){
				$xml_header_tags=/*. (string[int]) .*/array("STARTER_REGISTRATION","ACTIVATION_KEY","REQ_ACTIVATION_KEY","REQ_ACTIVATION_KEY_WITH_OTP");
				$parsed_xml1=$this->give_parsed_xml($this->GLC_DECRYPTED_CONTENT,$xml_header_tags);
				if(isset($parsed_xml1['ACTIVATION_KEY']) and $parsed_xml1['ACTIVATION_KEY']!==''){
					$this->process_license_request($parsed_xml1['ACTIVATION_KEY']);	 //old Process				
				}else if(isset($parsed_xml1['STARTER_REGISTRATION']) and $parsed_xml1['STARTER_REGISTRATION']!==''){
					$this->WEB_REQUEST_TYPE=13;                                      //We stopped now 
					$this->process_starter_request($parsed_xml1['STARTER_REGISTRATION']);				
				}else if(isset($parsed_xml1['REQ_ACTIVATION_KEY']) and $parsed_xml1['REQ_ACTIVATION_KEY']!==''){
					$this->WEB_REQUEST_TYPE=12; //new process 
					$this->process_license_request($parsed_xml1['REQ_ACTIVATION_KEY']);				
				}else if(isset($parsed_xml1['REQ_ACTIVATION_KEY_WITH_OTP']) && ($parsed_xml1['REQ_ACTIVATION_KEY_WITH_OTP']!=='') ){
					$this->WEB_REQUEST_TYPE=27; //reinstallation with otp
					$this->process_license_request($parsed_xml1['REQ_ACTIVATION_KEY_WITH_OTP']);
				}else{
					$this->GLC_ERROR_CODE='E001';				
				}
			}		
			
	}
	
}


$lic_online_obj=new licenseOnline();
$lic_online_obj->print_license_response();
?>
