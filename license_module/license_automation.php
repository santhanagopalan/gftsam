<?php
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__."/../lic_util.php");
require_once(__DIR__."/../product_delivery_util.php");

class licenseAutomate {
	
	private /*.string.*/ $idendity="";
	private /*.string.*/ $eidendity="";
	private /*.string.*/ $order_no="";
	private /*.string.*/ $fullfill_no="";
	private /*.string.*/ $product_code="";
	private /*.string.*/ $product_skew="";
	private /*.string.*/ $skew_group="";
	private /*.string.*/ $changes="NO";
	private /*.string.*/ $ERROR_CODE="";
	private /*.string.*/ $ERROR_MESSAGE="";
	private /*.string.*/ $RETURN_DATA="";
	private /*.string.*/ $LEAD_CODE="";
	private /*.string.*/ $purpose="";
	private /*.string.*/ $version="";
	private /*.string.*/ $request_type="";
	private /*.string.*/ $version_return="";
	private /*.int.*/ $version_status=1;  //by default status up to date
	private /*.string.*/ $cust_details="";
	private /*.string.*/ $pcode="";
	private /*.string.*/ $LOCAL_USER="N";
	private /*.string.*/ $install_id="";
	private /*.string.*/ $data_type="";
	private /*.int.*/ $clone_timestamp=0;
	private /*.string.*/ $contact='';
	private /*.string.*/ $required='';
	private /*.string.*/ $otp='';
	private /*.string.*/ $install_mode='';
	
	/**
	 * @return void
	 */
	public function check_for_license_changes() {
		$query =" select GLA_LEAD_CODE,GLA_APPROVED_ON, GLA_SENT_POS_ON from gft_lic_approved_log ".
				" join gft_install_dtl_new on (GID_ORDER_NO=GLA_ORDER_NO and GID_FULLFILLMENT_NO=GLA_FULLFILLMENT_NO ".
				" and GID_PRODUCT_CODE=GLA_PRODUCT_CODE and GID_PRODUCT_SKEW=GLA_PRODUCT_SKEW) ".
				" where GID_ORDER_NO='$this->order_no' and GID_FULLFILLMENT_NO=$this->fullfill_no ".
				" and GID_LIC_PCODE=$this->product_code and substr(GID_LIC_PSKEW,1,4)='$this->skew_group' ".
				" order by GLA_APPROVED_ON desc limit 1";
		$res_check = execute_my_query($query);
		if($row_data = mysqli_fetch_array($res_check)) {
			$sent_pos_date = $row_data['GLA_SENT_POS_ON'];
			$this->LEAD_CODE = $row_data['GLA_LEAD_CODE'];
			if($sent_pos_date==''){
				$this->changes = 'YES';
			}
		}else{
			$this->ERROR_CODE='ER03';
		}
	}
	
	/**
	 * @return void
	 */
	public function get_error_message() {
		switch($this->ERROR_CODE){
			case 'ER01':$msg="Identity length mismatch"; break;
			case 'ER02':$msg="Customer Identity verification failed"; break;
			case 'ER03':$msg="Data not available in Approval log";break;
			case 'ER04':$msg="Customer Id not available in CRM";break;
			case 'ER05':$msg="Product Not Availabe in Version Master";break;
			case 'ER06':$msg="Version Not Available in CRM";break;
			case 'ER07':$msg="Sample DB is Empty for this Version";break;
			case 'ER08':$msg="Default Master Data is Empty for this Version";break;
			case 'ER09':$msg="Record Not found in CRM. Please check the Vertical and Product Version of this customer and ensure its correctly updated";break;
			case 'ER10':$msg="Invalid Customer Id";break;
			case 'ER11':$msg="Technical Error in Query Execution";break;
			case 'ER12':$msg="This is not the latest backup. Please choose the latest backup taken for reinstallation to avoid data loss";break;
			case 'ER13':$msg="No Active backup found for this Installed Order Number";break;
			case 'ER14':$msg="ALR Period Expired. Renew your ALR to continue";break;
			case 'ER15':$latest_info = get_latest_product_version(array('516-04.0'));
						$web_instaler_url = isset($latest_info[0][4])?$latest_info[0][4]:'';
						$msg="This version of Web Installer is not supported for you. Please Get the Latest version of Web Installer from $web_instaler_url";break;
			case 'ER16':$msg="OTP / Contact is missing in request data";break;
			case 'ER17':$msg="Invalid OTP. Please enter the correct one";break;
			case 'ER18':$msg="Not a valid customer id / mobile number";break;
			default:$msg = "Undefined Error Code $this->ERROR_CODE";
		}
		$this->ERROR_MESSAGE = $msg;
	}
	
	/**
	 * @return void
	 */
	public function print_response(){
		$this->RETURN_DATA="";
		if($this->data_type!='json'){
			header("Content-type:application/xml");
			$this->RETURN_DATA .= '<?xml version="1.0" standalone="yes"?><LICENSE_RESPONSE>';
		}
		if($this->request_type=='18'){
			$this->RETURN_DATA.="<CHANGES>$this->changes</CHANGES>";
			if($this->ERROR_CODE!=""){
				$this->get_error_message();
				$this->RETURN_DATA.="<REASON_CODE>$this->ERROR_CODE</REASON_CODE>".
						"<REASON>$this->ERROR_MESSAGE</REASON>";
			}
		}elseif ($this->request_type=='23'){
			$this->RETURN_DATA.="<STATUS>$this->version_status</STATUS>".$this->version_return;
			if($this->ERROR_CODE!=""){
				$this->get_error_message();
				$this->RETURN_DATA.="<REASON_CODE>$this->ERROR_CODE</REASON_CODE>".
						"<REASON>$this->ERROR_MESSAGE</REASON>";
			}
		}elseif ( in_array($this->request_type, array('20','24','28','29','37','38','39','40','41')) ){
			$this->RETURN_DATA.=$this->cust_details;
			if($this->ERROR_CODE!=""){
				$this->get_error_message();
				if($this->data_type=='json'){
					$error_arr['ERROR_CODE'] 	= $this->ERROR_CODE;
					$error_arr['ERROR_MESSAGE'] = $this->ERROR_MESSAGE;
					$this->RETURN_DATA.= json_encode($error_arr);
				}else{
					$this->RETURN_DATA.="<ERROR_CODE>$this->ERROR_CODE</ERROR_CODE>".
							"<ERROR_MESSAGE>$this->ERROR_MESSAGE</ERROR_MESSAGE>";
				}
			}
		}
		if($this->data_type!='json'){
			$this->RETURN_DATA.='</LICENSE_RESPONSE>';
		}
		if(in_array($this->request_type,array('23','20','24','28','39','40','41'))){  //version_check - as requested by POS team, raw data sent to Support Antenna
			$RETURN_ENCRYPTED_DATA = $this->RETURN_DATA;
		}else{
			global $secret;
			$RETURN_ENCRYPTED_DATA = lic_encrypt($this->RETURN_DATA, $secret);			
		}
		echo $RETURN_ENCRYPTED_DATA;
		
		if($this->request_type!='23'){
			$ins_request=/*. (mixed[string]) .*/array();
			$ins_request['GLC_REQUEST_ID']='';
			$ins_request['GLC_FROM_ONLINE']='Y';
			$ins_request['GLC_FROM_WEB']='Y';
			$ins_request['GLC_ONLINE_CONTENT']=mysqli_real_escape_string_wrapper($_SERVER['REQUEST_URI']);
			$ins_request['GLC_REQUEST_TIME']=date('Y-m-d H:i:s');
			$ins_request['GLC_RETURN_DATA']=mysqli_real_escape_string_wrapper($this->RETURN_DATA);
			$ins_request['GLC_RETURN_ENCRYPTED_DATA']=mysqli_real_escape_string_wrapper($RETURN_ENCRYPTED_DATA);
			$ins_request['GLC_LEAD_CODE']=$this->LEAD_CODE;
			$ins_request['GLC_IP_ADDRESS']=$_SERVER['REMOTE_ADDR'];
			$ins_request['GLC_REQUEST_PURPOSE_ID']=$this->request_type;
			$ins_request['GLC_ERROR_CODE']=$this->ERROR_CODE;
			$ins_request['GLC_ERROR_MESSAGE']=$this->ERROR_MESSAGE;
			$ins_request['GLC_PROCESSING_TIME']=getDeltaTime();
			//array_update_tables_common($ins_request,'gft_lic_request',null,null,SALES_DUMMY_ID,null,null,$ins_request);
		}
	}
	
	/**
	 * @return void
	 */
	public function check_for_version() {
		$today_date = date('Y-m-d');
		$product_family_code = get_single_value_from_single_table("GPM_HEAD_FAMILY","gft_product_family_master", "GPM_PRODUCT_CODE", $this->product_code);
		$temp_given_version = get_valid_version($this->version);
		
		$asa_quer = " select GID_VALIDITY_DATE from gft_install_dtl_new ".
				" join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW)  ".
				" where GID_ORDER_NO='$this->order_no' and GID_FULLFILLMENT_NO=$this->fullfill_no and GID_LIC_PCODE=$this->product_code ".
				" and (GID_LIC_PSKEW='$this->product_skew' or GPM_REFERER_SKEW='$this->product_skew') and GID_VALIDITY_DATE >= now() ";
		$asa_res = execute_my_query($asa_quer);
		$asa_status = false;
		if(mysqli_num_rows($asa_res) > 0){
			$asa_status = true;
		}
		$curr_version_cond = " and gpv_current_version=1 ";
		if($this->skew_group=='07.0'){
			$q1 = " select GID_INSTALL_ID from gft_install_dtl_new where GID_LEAD_CODE='$this->LEAD_CODE' ".
					" and GID_LIC_PCODE in (263,264) and GID_VALIDITY_DATE >= '$today_date' ";
			$r1 = execute_my_query($q1);
			if( (!is_product_installed($this->LEAD_CODE, 706)) && (mysqli_num_rows($r1)==0) ){
				$rpos7_ver = get_samee_const("RPOS7_VERSION");
				if($rpos7_ver!=''){
					$curr_version_cond = " and GPV_VERSION='$rpos7_ver' ";
				}
			}
		}
		
		$lat_query = " select GPV_VERSION,GPV_RELEASE_NOTE_HLINK,GPV_DOWNLOAD_HLINKPH,GPV_CDN_PATCH_LINK,GPV_MODULAR_SP_URL from gft_product_version_master ".
					 " where 1 $curr_version_cond and GPV_PRODUCT_CODE='$product_family_code' and gpv_version_family='$this->skew_group' and GPV_PRODUCT_STATUS='A' ";
		$lat_res = execute_my_query($lat_query);
		$latest_version = $temp_latest_version = $release_notes = $patch_link = $cdn_patch_link = "";
		$modular_sp_link = "";
		if($lat_data = mysqli_fetch_array($lat_res)){
			$latest_version = $lat_data['GPV_VERSION'];
			$release_notes	= $lat_data['GPV_RELEASE_NOTE_HLINK'];
			$patch_link 	= $lat_data['GPV_DOWNLOAD_HLINKPH'];
			$cdn_patch_link = $lat_data['GPV_CDN_PATCH_LINK'];
			//$modular_sp_link= $lat_data['GPV_MODULAR_SP_URL'];
			$temp_latest_version =  get_valid_version($latest_version);
			$version_res = version_compare($temp_given_version, $temp_latest_version, '>=');
			if($version_res){ //already in latest
				if($asa_status){ //checking plugin versions
					$print_profile_vers = get_single_value_from_single_table("GID_PRINT_PROFILE_VERSION", "gft_install_dtl_new", "GID_INSTALL_ID", $this->install_id);
					$latest_profile_dtl = get_latest_plugin_version(1,$this->product_code,$this->skew_group);
					if( (isset($latest_profile_dtl['version'])) && ($print_profile_vers!=$latest_profile_dtl['version']) ){
						$this->version_status = 0;
						$this->version_return ="<RELEASE_NOTES>http://www.gofrugal.com</RELEASE_NOTES>".
												"<ASA_STATUS>VALID</ASA_STATUS>".
												"<LATEST_VERSION>".$latest_profile_dtl['version']."</LATEST_VERSION>".
												"<DESCRIPTION>".$latest_profile_dtl['description']."</DESCRIPTION>".
												"<DOWNLOAD_LINK>".$latest_profile_dtl['download_path']."</DOWNLOAD_LINK>";
					}
				}					
				return; 
			}
		}
		
	    $sql3 = " select GMS_LINK,GMS_HASH_VALUE from gft_product_version_master join gft_modular_servicepack on (GMS_REF_ID=GPV_AUTO_ID) ".
	  		    " where gpv_product_code='$this->product_code' and gpv_version_family='$this->skew_group' and GPV_VERSION='$latest_version' and GMS_FROM_VERSION='$this->version' ";
	    $res3 = execute_my_query($sql3);
	    $modular_sp_hash = "";
	    if ($data3 = mysqli_fetch_array($res3)){
	        $cdn_patch_link = $data3['GMS_LINK'];
	        $modular_sp_hash = $data3['GMS_HASH_VALUE'];
	    }
		
		//If not the latest version continue below
		$this->version_status = 0;
		$query= " select GPV_VERSION, GPV_RELEASE_NOTE_HLINK, GPV_DOWNLOAD_HLINKPH, GPV_CDN_PATCH_LINK,GPV_MODULAR_SP_URL ".
				" from gft_product_version_master ".
				" where GPV_PRODUCT_CODE=$this->product_code and gpv_version_family='$this->skew_group' ".
				" and GPV_IS_MINIMUM_VERSION=1 order by GPV_RELEASE_DATE ";
		$ver_res = execute_my_query($query);
		while($ver_data = mysqli_fetch_array($ver_res)){
			$temp_minimum_version =  get_valid_version($ver_data['GPV_VERSION']);
			$minimum_version_res = version_compare($temp_given_version, $temp_minimum_version,'<');
			if($minimum_version_res){
				$latest_version = $ver_data["GPV_VERSION"];
				$release_notes 	= $ver_data["GPV_RELEASE_NOTE_HLINK"];
				$patch_link		= $ver_data["GPV_DOWNLOAD_HLINKPH"];
				$cdn_patch_link = $ver_data['GPV_CDN_PATCH_LINK'];
				$modular_sp_link = "";
				break;
			}
		}
		if($latest_version==''){
			$this->ERROR_CODE = 'ER05';
			return;
		}
		if($cdn_patch_link!=""){
			$patch_link = $cdn_patch_link;
		}

		$comments = "A new version $latest_version is available with a few important enhancements.";
		if(in_array($this->product_code, array('200','500'))){
		    $url_config = get_connectplus_config();
		    $release_notes = htmlspecialchars($url_config['release_notes']."?cust_id=".$this->LEAD_CODE."&version=$latest_version");
		}
		$ver_return="<RELEASE_NOTES>$release_notes</RELEASE_NOTES>".
					"<LATEST_VERSION>$latest_version</LATEST_VERSION>";
				
		if( $asa_status || ($this->LOCAL_USER=="Y") ){
			$comments .= "Please upgrade your POS by using the link given below ";			
			$ver_return .= "<DESCRIPTION>$comments</DESCRIPTION>".
							"<ASA_STATUS>VALID</ASA_STATUS>".
							"<DOWNLOAD_LINK>$patch_link</DOWNLOAD_LINK>";
			if($modular_sp_hash!=''){
			    $ver_return .= "<HASH_VALUE>$modular_sp_hash</HASH_VALUE>";
			}
		}else{
			$comments	.=	"Please Pay ASA by using the link given below and enjoy the services ";
			$ver_return .=	"<DESCRIPTION>$comments</DESCRIPTION>".
							"<ASA_STATUS>EXPIRED</ASA_STATUS>".
							"<STORE_LINK>store.gofrugal.com</STORE_LINK>";
		}
		$this->version_return = $ver_return;
	}
	
	/**
	 * @return boolean
	 */
	public function validate_web_installer_minimum_version(){
		if( ($this->pcode=='') || ($this->version=='') ){
				return true;
		}
		$latest_info = get_latest_product_version(array('516-04.0'));
		$product_version = isset($latest_info[0][2])?str_replace('.','',$latest_info[0][2]):'';
		if($this->version < $product_version){
			return false;
		}
		return true;
	}
	
	/**
	 * @param string $cust_id
	 * 
	 * @return void
	 */
	public function return_cutomer_info($cust_id){
		
		if(!$this->validate_web_installer_minimum_version()){
			$this->ERROR_CODE = 'ER15';
			return;
		}
		$otp_verified = false;
		$generated_order = "";
		if($this->required=='order'){ //order creation for patanjali case
			$pcode_to_create = '500';
			$pskew_to_create = '07.0SR';
			if( ($this->otp=='') || ($this->contact=='') ){
				$this->ERROR_CODE = 'ER16';
				return;
			}
			if(!validate_otp($this->otp, $this->contact)){
				$this->ERROR_CODE = 'ER17';
				return;
			}
			$otp_verified = true;
			if(!is_order_available($cust_id,$pcode_to_create,$pskew_to_create)){
				$territory_id = get_single_value_from_single_table("GLH_TERRITORY_ID", "gft_lead_hdr", "GLH_LEAD_CODE", $cust_id);
				$new_order_no = get_order_no("D", date('y'), $territory_id, PATANJALI_ID);
				$order_hdr_arr = array(
					'GOD_LEAD_CODE'=>$cust_id,	'GOD_ORDER_STATUS'=>'A',
					'GOD_EMP_ID'=>PATANJALI_ID,	'GOD_INCHARGE_EMP_ID'=>PATANJALI_ID,
					'GOD_ORDER_AMT'=>'0',		'GOD_COLLECTED_AMT'=>'0',
					'GOD_BALANCE_AMT'=>'0',		'GOD_ORDER_SPLICT'=>'0',
					'GOD_ORDER_NO'=>$new_order_no,'GOD_IMPL_REQUIRED'=>'Yes',
					'GOD_PD_EXPENSE_TYPE'=>'2'
				);
				$generated_order = array_insert_new_order($order_hdr_arr, array($pcode_to_create), array($pskew_to_create), array('1'), array('0'));
				//Created PD
				if($generated_order!=""){
					generate_product_delivery($generated_order, 1, 2, $cust_id,$pcode_to_create,$pskew_to_create);
				}
			}
		}
		
		$vers_cond = " and GPV_PUBLISH_IN_WEB='Y' and GPV_PRODUCT_STATUS='A' ";
		if(is_product_installed($cust_id, 706)){
			$vers_cond = " and GPV_VERSION in (select ifnull(GID_CONNECTPLUS_VERSION,GID_CURRENT_VERSION) from gft_install_dtl_new where GID_LEAD_CODE='$cust_id' ".
								" and GID_STATUS!='U' and GID_LIC_PCODE in (500,200) ) ";
		}else if( is_product_installed($cust_id, 300) && is_kit_based_customer($cust_id) ){
			$outlet_ver_que = " select GID_CURRENT_VERSION from gft_outlet_lead_code_mapping ".
							  " join gft_install_dtl_new on (GOL_CUST_ID=GID_LEAD_CODE and GOL_ORDER_NO=GID_ORDER_NO and GOL_FULLFILLMENT_NO=GID_FULLFILLMENT_NO and GID_LIC_PCODE=substr(GOL_EDITION,1,3)) ". 
 							  " where GOL_INSTALL_ID in ( select GID_INSTALL_ID from gft_install_dtl_new where GID_LEAD_CODE='$cust_id' and GID_LIC_PCODE=300) ";
			$outlet_ver_res = execute_my_query($outlet_ver_que);
			$outlet_version_arr = /*. (string[int]) .*/array();
			while($ver_row = mysqli_fetch_array($outlet_ver_res)){
				$outlet_version_arr[] = $ver_row['GID_CURRENT_VERSION'];
			}
			$outlet_version_arr = array_unique($outlet_version_arr);
			if(count($outlet_version_arr) > 0){
				$outlet_ver_str = "'".implode("','", $outlet_version_arr)."'";
				$vers_cond = " and GPV_VERSION in ($outlet_ver_str) ";
			}
		}
		$query_cust=" select GLH_LEAD_CODE, GLH_CUST_NAME, GTM_IS_MACRO, GLH_VERTICAL_CODE, GTM_MICRO_OF, glh_country, GLH_CUST_STATECODE,GPG_PRODUCT_ALIAS,st.GPM_GST_STATE_CODE, ".  
					" group_concat(distinct if(GTM_IS_MACRO='N',GTM_VERTICAL_CODE,null)) as vertical_ids,glh_reference_given,glh_lead_sourcecode,glh_lead_type,GLE_GST_NO, ".
					" group_concat(distinct if(GTM_IS_MACRO='N',GTM_CHAIN_NAME,null)) as chain_names, ".
					" group_concat(distinct if(GTM_IS_MACRO='N',GTM_ERP_NAME,null)) as erp_names, ".
					" group_concat(distinct if(gcc_contact_type=1, GCC_CONTACT_NO, null)) as mobile_no, ".
					" group_concat(distinct if(gcc_contact_type=2, GCC_CONTACT_NO, null)) as buss_ph, ".
					" group_concat(distinct if(gcc_contact_type=4, GCC_CONTACT_NO, null)) as email_id , ". 
					" concat(GPPM_PRODUCT_ABR,gpg_version) as pname,GPV_VERSION, gpg_product_family_code, gpg_skew, if(LENGTH(GPV_CDN_SETUP_LINK)>5,GPV_CDN_SETUP_LINK,GPV_DOWNLOAD_HLINK) GPV_DOWNLOAD_HLINK, ".
					" GPV_SAMPLE_DB_PATH,GPV_GST_SAMPLE_DB_PATH,GPV_CHAIN_MANAGER_PATH,GPV_WEB_REPORTER_PATH,GCS_NAME,cu.GPM_NUMERIC_COUNTRY_CODE, ". 
					" concat(GPM_PRODUCT_NAME,' ',gpg_version) as prod_name,GLH_CUST_STREETADDR1,GLH_CUST_CITY,gpg_product_name ".
					" from gft_lead_hdr join gft_customer_contact_dtl on (GLH_LEAD_CODE=GCC_LEAD_CODE) ".
					" join gft_lead_hdr_ext on (GLE_LEAD_CODE=GLH_LEAD_CODE) ".
					" join gft_vertical_master on (GTM_STATUS='A' and ((GTM_VERTICAL_CODE=GLH_VERTICAL_CODE) or (GTM_MICRO_OF=GLH_VERTICAL_CODE)) ) ".
					" join gft_bvp_relation on (gbr_vertical=GTM_MICRO_OF) ".
					" join gft_customer_status_master cs ON(GCS_CODE=GLH_STATUS)".
					" join gft_product_group_master on (gpg_product_family_code=substring_index(gbr_product,'-',1) and gpg_skew=substring_index(gbr_product,'-',-1)) ".
					" join gft_product_family_master fm on (gpg_product_family_code=fm.gpm_product_code) ".
					" join gft_product_primary_master on (GPPM_PRODUCT_CODE=gpg_product_family_code) ".
					" join (select GPV_PRODUCT_CODE,gpv_version_family,GPV_VERSION,GPV_CDN_SETUP_LINK,GPV_DOWNLOAD_HLINK,GPV_SAMPLE_DB_PATH,GPV_GST_SAMPLE_DB_PATH,GPV_CHAIN_MANAGER_PATH,GPV_WEB_REPORTER_PATH from gft_product_version_master where 1 $vers_cond order by GPV_RELEASE_DATE desc) gpv ".
						"  on (GPV_PRODUCT_CODE=gpg_product_family_code and gpv_version_family=gpg_skew) ".
					" left join gft_political_map_master st on (st.GPM_MAP_NAME=GLH_CUST_STATECODE and st.GPM_MAP_TYPE='S') ".
					" left join gft_political_map_master cu on (cu.GPM_MAP_NAME=glh_country and cu.GPM_MAP_TYPE='C') ".
					" where GLH_LEAD_CODE='$cust_id' group by GLH_LEAD_CODE ";
		$res_cust = execute_my_query($query_cust);
		$arr = /*. (string[string]) .*/array();
		if($row_data = mysqli_fetch_array($res_cust)){
			$mobile_no_str = $row_data['mobile_no'];
			if($mobile_no_str==""){
				$mobile_no_str = $row_data['buss_ph'];
			}else{
				$mobile_no_str .= ",".$row_data['buss_ph'];
			}
			$vertical_id = $row_data['vertical_ids'];
			$vertical_names = $row_data['chain_names'];
			$customer_name = (string)str_replace(array("'",'"','~'),"",$row_data['GLH_CUST_NAME']); //single and double quotes, tilde symbols removed as it creates problem in POS - http://sam.gofrugal.com/issuemanager/RayMedi_Tools/view.php?id=1640
			$product_fcode = $row_data['gpg_product_family_code'];
			$product_group = $row_data['gpg_skew'];
			$glh_country   = $row_data['glh_country'];
			$country_code  = (int)$row_data['GPM_NUMERIC_COUNTRY_CODE'];
			$vat_sample_db = $row_data['GPV_SAMPLE_DB_PATH'];
			$gst_sample_db = $row_data['GPV_GST_SAMPLE_DB_PATH'];
			$corp_cust_id = (int)$row_data['glh_reference_given'];
			$glh_lead_type = (int)$row_data['glh_lead_type'];
			$sample_db = $vat_sample_db;
			$state_name = $row_data['GLH_CUST_STATECODE'];
			$state_code = $row_data['GPM_GST_STATE_CODE'];
			$gpg_product_name = $row_data['gpg_product_name'];
			$lead_status= "";
			$enable_lead_status=get_samee_const('ENABLE_LEAD_STATUS_IN_LICENCE_XML');
			if($enable_lead_status==1){
				$lead_status = $row_data['GCS_NAME'];
			}			
			if(strcasecmp($glh_country, "India")==0){
				if($gst_sample_db!=''){
					$sample_db = $gst_sample_db;
				}
			}else{
				$state_name = 'none';
				$state_code = '0';
			}
			$stable_vers_arr	= get_latest_product_version(array($product_fcode."-".$product_group),'stable');
			$stable_version 	= isset($stable_vers_arr[0][2])?$stable_vers_arr[0][2]:'';
			$stable_setup 		= isset($stable_vers_arr[0][4])?$stable_vers_arr[0][4]:'';
			$stable_sample_db 	= isset($stable_vers_arr[0][5])?$stable_vers_arr[0][5]:'';
			$stable_gst_sample_db= isset($stable_vers_arr[0][9])?$stable_vers_arr[0][9]:'';
			if( ($stable_gst_sample_db!='') && (strcasecmp($glh_country, "India")==0) ){
				$stable_sample_db = $stable_gst_sample_db;
			}
			$stable_gcmsetup	= isset($stable_vers_arr[0][7])?$stable_vers_arr[0][7]:'';
			$stable_webreport	= isset($stable_vers_arr[0][8])?$stable_vers_arr[0][8]:'';
			$stable_smi         = isset($stable_vers_arr[0][10])?$stable_vers_arr[0][10]:'';
			$stable_attr = " STABLE_VERSION='$stable_version' STABLE_SETUP='$stable_setup' STABLE_SAMPLE_DB='$stable_sample_db' STABLE_RCM_SETUP='$stable_gcmsetup' STABLE_WEB_REPORTER='$stable_webreport' STABLE_SMI_WR_PATH='$stable_smi' ";
			$need_otp = 'false';
			if(check_partner_lead_source($cust_id,'',PATANJALI_ID) && !$otp_verified){
				$need_otp = 'true';
			}
			$this->cust_details="<BUSINESS_NAME><![CDATA[".htmlspecialchars($customer_name)."]]></BUSINESS_NAME>".
								"<NEED_OTP>$need_otp</NEED_OTP>".
								"<MOBILE_NO>".$mobile_no_str."</MOBILE_NO>".
								"<EMAIL>".$row_data['email_id']."</EMAIL>".
								"<VERTICAL_ID>$vertical_id</VERTICAL_ID>".
								"<PRODUCT_NAME>".$row_data['pname']."</PRODUCT_NAME>".
								"<PRODUCT_VERSION>".$row_data['GPV_VERSION']."</PRODUCT_VERSION>".
								"<PRODUCT_FCODE>".$product_fcode."</PRODUCT_FCODE>".
								"<PRODUCT_GROUP>".$product_group."</PRODUCT_GROUP>".
								"<SETUP_URL>".$row_data['GPV_DOWNLOAD_HLINK']."</SETUP_URL>".
								"<SAMPLE_DB_PATH>".$sample_db."</SAMPLE_DB_PATH>".
								"<PRODUCT_DETAILS>".
									"<PRODUCT NAME='".$row_data['pname']."' FCODE='$product_fcode' GROUP='$product_group' ALIAS='".$row_data['GPG_PRODUCT_ALIAS']."' ".
										" VERSION='".$row_data['GPV_VERSION']."' SETUP='".$row_data['GPV_DOWNLOAD_HLINK']."' SAMPLE_DB='$sample_db' RCM_SETUP='".$row_data['GPV_CHAIN_MANAGER_PATH']."' WEB_REPORTER='".$row_data['GPV_WEB_REPORTER_PATH']."' ".
										$stable_attr.
										" />".
								"</PRODUCT_DETAILS>".
								"<ADDRESS1>".$row_data['GLH_CUST_STREETADDR1']."</ADDRESS1>".
								"<ADDRESS2>".$row_data['GLH_CUST_CITY']."</ADDRESS2>".
								"<COUNTRY>$glh_country</COUNTRY>".
								"<country_code>$country_code</country_code>".
								"<STATE>$state_name</STATE>".
								"<state_code>$state_code</state_code>".
								"<GSTIN>".$row_data['GLE_GST_NO']."</GSTIN>".
								"<LEAD_STATUS>$lead_status</LEAD_STATUS>".
								"<TYPE>Customer</TYPE>";
			
			$prod_dtl['NAME'] 		= $row_data['pname'];
			$prod_dtl['FCODE'] 		= $row_data['gpg_product_family_code'];
			$prod_dtl['GROUP'] 		= $row_data['gpg_skew'];
			$prod_dtl['VERSION'] 	= $row_data['GPV_VERSION'];
			$prod_dtl['SETUP'] 		= $row_data['GPV_DOWNLOAD_HLINK'];
			$prod_dtl['SAMPLE_DB'] 	= $row_data['GPV_SAMPLE_DB_PATH'];
			$prod_dtl['ALIAS'] 		= $row_data['GPG_PRODUCT_ALIAS'];
			$prod_dtl['RCM_SETUP'] 	= $row_data['GPV_CHAIN_MANAGER_PATH'];
			
			$kit_based_cust = "N";
			$kit_type = "";
			$outlet_details = "";
			$re_prod_id = $re_edition = "";
			$check_kit_id = $cust_id;
			$hq_customer = "N";
			if( ($glh_lead_type==13) && ((int)$row_data['glh_lead_sourcecode']==7) && ($corp_cust_id > 0) ){
				$check_kit_id = $corp_cust_id;
				$hq_installed_id = (int)get_hq_installed_cust_id($corp_cust_id);
			}else{
				$hq_installed_id = (int)get_hq_installed_cust_id($cust_id);
			}
			$is_kit_based = is_kit_based_customer($check_kit_id);
			if($hq_installed_id > 0){
				$hq_customer = "Y";
				if($is_kit_based){  // for kit based HQ delight customers
				    $kit_based_cust = "Y";
				    $kit_type = "RCM";
				}else{ //for old HQ customers
				    $vertical_names = $row_data['prod_name'];
				    $id_len = count(explode(",",$vertical_id));
				    while ($id_len>1){
				        $vertical_names .= ",".$row_data['prod_name'];
				        $id_len--;
				    }
				}
				if($this->install_mode=='REINSTALLATION'){
				    $kit_based_cust = 'Y'; //for both old hq and new kit based hq
				}
				$que1 = " select GID_STORE_URL from gft_install_dtl_new where GID_LEAD_CODE='$hq_installed_id' and GID_LIC_PCODE=300 and GID_STATUS in ('A','S') and GID_STORE_URL!='' ";
				$hq_store_url = get_single_value_from_single_query("GID_STORE_URL", $que1);
				$sql_que =" select GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GPM_PRODUCT_TYPE from gft_order_product_dtl ".
				          " join gft_order_hdr on (GOD_ORDER_NO=GOP_ORDER_NO and GOD_ORDER_STATUS='A') ".
				          " join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
				          " where GOD_LEAD_CODE='$hq_installed_id' and GPM_LICENSE_TYPE!=3 and GFT_SKEW_PROPERTY=1 and GOP_PRODUCT_CODE in (200,500) ";
				$sql_res = execute_my_query($sql_que);
				if($sql_row = mysqli_fetch_array($sql_res)){
				    $re_prod_id = $sql_row['GOP_PRODUCT_CODE']."-".substr($sql_row['GOP_PRODUCT_SKEW'],0,4);
				    $re_edition = $sql_row['GPM_PRODUCT_TYPE'];
				}
				$outlet_details .= "<OUTLET_DETAILS HQ_URL='$hq_store_url'>";
				$outlet_info_arr = get_outlet_info_in_array($hq_installed_id,$cust_id);
				if( ($kit_based_cust=='Y') && (count($outlet_info_arr) > 0) ){
					foreach ($outlet_info_arr[0] as $key => $va){
						if($outlet_info_arr[0][$key]=='1'){ //HQ outlet not required for web installer
							continue;
						}
						$outlet_details .= "<OUTLET OUTLET_ID='".$outlet_info_arr[0][$key]."' ".
										    " NAME='".htmlentities(htmlentities($outlet_info_arr[1][$key]))."' ".
										    " LOCATION='".htmlentities(htmlentities($outlet_info_arr[2][$key]))."' ".
											" EDITION='".$outlet_info_arr[3][$key]."' ".
											" ADDRESS='".htmlentities(htmlentities($outlet_info_arr[4][$key]))."' ".
											" MOBILE='".$outlet_info_arr[5][$key]."' ".
											" EMAIL='".$outlet_info_arr[6][$key]."' ".
											" VATTIN='".$outlet_info_arr[8][$key]."' ".
											" CONNECT_PLUS_TOKEN='".get_connectplus_token($outlet_info_arr[9][$key], '706')."' ".
											" ORDER_NO='".$outlet_info_arr[7][$key]."' />";	
					}
				}
				$outlet_details .= "</OUTLET_DETAILS>";
			}
			$order_no = $skew_code = '';
			$user_type = 'P';
			$install_type = "NEW";
			$lic_que=" select GID_ORDER_NO,GID_FULLFILLMENT_NO,GID_CONNECTPLUS_TOKEN,GPM_LICENSE_TYPE,GID_LIC_PCODE,GID_LIC_PSKEW,GPM_PRODUCT_TYPE from gft_install_dtl_new ".
					 " join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
					 " where GID_LEAD_CODE='$cust_id' and GID_LIC_PCODE='$product_fcode' and SUBSTR(GID_LIC_PSKEW,1,4)='$product_group' ".
					 " and GID_STATUS in ('A','S') and GPM_LICENSE_TYPE in (1,2) ";
			$lic_res = execute_my_query($lic_que);
			$num_rows = mysqli_num_rows($lic_res);
			if($row2 = mysqli_fetch_array($lic_res)){ 
				$user_type = 'L';
				$install_type = "REINSTALL";
				if($num_rows==1){ //only one root orders
					$order_no = $row2['GID_ORDER_NO'].substr("0000".$row2['GID_FULLFILLMENT_NO'], -4);
					$skew_code = $row2['GID_LIC_PCODE'].str_replace(".", "", substr($row2['GID_LIC_PSKEW'], 0, 6));
					$re_prod_id = $row2['GID_LIC_PCODE']."-".substr($row2['GID_LIC_PSKEW'],0,4);
					$re_edition = $row2['GPM_PRODUCT_TYPE'];
				}
			}else{ //no license installed....
				
				//checking for order present
				$ord_que = " select GOP_ORDER_NO,GOP_FULLFILLMENT_NO,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GPM_PRODUCT_TYPE from gft_order_product_dtl ".
						   " join gft_order_hdr on (GOD_ORDER_NO=GOP_ORDER_NO and GOD_ORDER_STATUS='A') ".
						   " join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
						   " where GOD_LEAD_CODE='$cust_id' and GOD_ORDER_SPLICT=0 and GPM_PRODUCT_CODE='$product_fcode' ".
						   " and SUBSTR(GPM_PRODUCT_SKEW,1,4)='$product_group' and GFT_SKEW_PROPERTY=1 and GPM_LICENSE_TYPE=1 ";
				$ord_res = execute_my_query($ord_que);
				if( (mysqli_num_rows($ord_res)==1) && ($ord_row = mysqli_fetch_array($ord_res)) ){
					$order_no = $ord_row['GOP_ORDER_NO'].substr("0000".$ord_row['GOP_FULLFILLMENT_NO'], -4);
					$re_prod_id = $ord_row['GOP_PRODUCT_CODE']."-".substr($ord_row['GOP_PRODUCT_SKEW'],0,4);
					$re_edition = $ord_row['GPM_PRODUCT_TYPE'];
				}
				
				//checking for trial license
				$trial_que=" select GID_ORDER_NO,GID_FULLFILLMENT_NO,GID_CONNECTPLUS_TOKEN,GPM_LICENSE_TYPE,GID_LIC_PCODE,GID_LIC_PSKEW,GPM_PRODUCT_TYPE from gft_install_dtl_new ".
						" join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
						" where GID_LEAD_CODE='$cust_id' and GID_LIC_PCODE='$product_fcode' and SUBSTR(GID_LIC_PSKEW,1,4)='$product_group' ".
						" and GID_STATUS in ('A','S') and GPM_LICENSE_TYPE in (3) ";
				$trial_res = execute_my_query($trial_que);
				$num_rows = mysqli_num_rows($trial_res);
				if($trial_data = mysqli_fetch_array($trial_res)){
					$user_type = 'T';
					$install_type = "REINSTALL";
					if($num_rows==1){
						$order_no = $trial_data['GID_ORDER_NO'].substr("0000".$trial_data['GID_FULLFILLMENT_NO'], -4);
						$skew_code = $trial_data['GID_LIC_PCODE'].str_replace(".", "", substr($trial_data['GID_LIC_PSKEW'], 0, 6));
						$re_prod_id = $trial_data['GID_LIC_PCODE']."-".substr($trial_data['GID_LIC_PSKEW'],0,4);
						$re_edition = $trial_data['GPM_PRODUCT_TYPE'];
					}
				}
			}
			$sql1 = " select if(god_order_splict=1,GCO_ORDER_NO,GOP_ORDER_NO) as order_no,if(god_order_splict=1,GCO_FULLFILLMENT_NO,GOP_FULLFILLMENT_NO) as fullfill_no, ".
			 		" GPM_PRODUCT_TYPE,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW ".
					" from gft_order_hdr join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
					" left join gft_cp_order_dtl on (GCO_ORDER_NO=GOP_ORDER_NO and GCO_PRODUCT_CODE=GOP_PRODUCT_CODE and GCO_SKEW=GOP_PRODUCT_SKEW) ".
					" join gft_product_master pm on (GOP_PRODUCT_CODE=pm.GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
					" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
					" where GOD_ORDER_STATUS='A' and if(god_order_splict=1,GCO_CUST_CODE,GOD_LEAD_CODE)='$cust_id' and GPM_PRODUCT_TYPE=18 and GFT_SKEW_PROPERTY=1 and fm.GPM_IS_INTERNAL_PRODUCT!=4 ";
			$sql_res1 = execute_my_query($sql1);
			if( (mysqli_num_rows($sql_res1)==1) && ($sdata = mysqli_fetch_array($sql_res1)) ){
				$kit_based_cust = 'Y';
				$kit_type 		= 'ERP';
				$user_type 		= "L";
				$order_no 		= $sdata['order_no'].substr("0000".$sdata['fullfill_no'], -4);
				$vertical_names = $row_data['erp_names'];
				$re_prod_id     = $sdata['GOP_PRODUCT_CODE']."-".substr($sdata['GOP_PRODUCT_SKEW'],0,4);
				$re_edition     = $sdata['GPM_PRODUCT_TYPE'];
			}
			$connectplus_token = get_connectplus_token($cust_id, '706');
			$is_nt = (int)get_single_value_from_single_table("GCD_IS_NT", "gft_cust_env_data", "GCD_LEAD_CODE", $cust_id);
			$nt_customer = "false";
			$nt_res = execute_my_query("select GCD_IS_NT from gft_cust_env_data where GCD_LEAD_CODE='$cust_id' and GCD_IS_NT=1");
			if($nt_row = mysqli_fetch_array($nt_res)){
			    $nt_customer = "true";
			}
			$rebranded_dtl = get_rebranded_product_name($re_prod_id, $re_edition, $vertical_id);
			$rebranded_name= isset($rebranded_dtl[0]) ? $rebranded_dtl[0] : '';
			$solution_name = isset($rebranded_dtl[1]) ? $rebranded_dtl[1] : '';
			//For taking corporate customer id
			$corporate_xml_dtl = "";
			if($kit_based_cust=='N'){
				$corp_query   = " select GID_LEAD_CODE from gft_outlet_lead_code_mapping ".
								" INNER JOIN gft_install_dtl_new ON(GID_INSTALL_ID=GOL_INSTALL_ID) ".
								" where GOL_CUST_ID=$cust_id";
				$corporate_lead_code = get_single_value_from_single_query('GID_LEAD_CODE', $corp_query);
				$corporate_xml_dtl = $corporate_lead_code>0?"<HQ_CUSTOMER_ID>$corporate_lead_code</HQ_CUSTOMER_ID>":"";
			}
			$this->cust_details .=  "<USER_TYPE>$user_type</USER_TYPE>".
									"<ORDER_NO>$order_no</ORDER_NO>".
									"<SKEW_CODE>$skew_code</SKEW_CODE>".
									"<INSTALL_TYPE>$install_type</INSTALL_TYPE>".
									"<CONNECT_PLUS_TOKEN>$connectplus_token</CONNECT_PLUS_TOKEN>".
									"<NT_CUSTOMER>$nt_customer</NT_CUSTOMER>".
									"<KIT_BASED>$kit_based_cust</KIT_BASED>".
									$corporate_xml_dtl.
									"<KIT_TYPE>$kit_type</KIT_TYPE>".
									"<HQ_CUSTOMER>$hq_customer</HQ_CUSTOMER>".
									"<VERTICAL_NAME>$vertical_names</VERTICAL_NAME>".
									"<REBRANDED_NAME>$rebranded_name</REBRANDED_NAME>".
									"<SOLUTION_NAME>$solution_name</SOLUTION_NAME>".
									"$outlet_details";
			
			$arr['BUSINESS_NAME']	= htmlspecialchars($customer_name);
			$arr['MOBILE_NO']		= $mobile_no_str;
			$arr['EMAIL']			= $row_data['email_id'];
			$arr['VERTICAL_ID']		= $vertical_id;
			$arr['VERTICAL_NAME']	= $vertical_names;
			$arr['PRODUCT_NAME']	= $row_data['pname'];
			$arr['PRODUCT_VERSION']	= $row_data['GPV_VERSION'];
			$arr['PRODUCT_FCODE']	= $product_fcode;
			$arr['PRODUCT_GROUP']	= $product_group;
			$arr['SETUP_URL']		= $row_data['GPV_DOWNLOAD_HLINK'];
			$arr['SAMPLE_DB_PATH']	= $row_data['GPV_SAMPLE_DB_PATH'];
			$arr['PRODUCT_DETAILS'] = $prod_dtl;
			$arr['COUNTRY']			= $row_data['glh_country'];
			$arr['STATE']			= $row_data['GLH_CUST_STATECODE'];
			$arr['TYPE']			= "Customer";
			$arr['USER_TYPE']		= $user_type;
			$arr['ORDER_NO']		= $order_no;
			$arr['CONNECT_PLUS_TOKEN']= $connectplus_token;
			$arr['KIT_BASED']		= $kit_based_cust;
			$contact_query_res = execute_my_query(" select GCC_CONTACT_NAME,GCC_CONTACT_NO from gft_customer_contact_dtl where GCC_LEAD_CODE='$cust_id' and GCC_CONTACT_TYPE in (1,2) ");
			$contact_arr = /*. (string[int][string]) .*/array(); 
			while ($row_contact = mysqli_fetch_array($contact_query_res)){
				$temp_arr['name'] 	= $row_contact['GCC_CONTACT_NAME'];
				$temp_arr['mobile'] = $row_contact['GCC_CONTACT_NO'];
				$contact_arr[] = $temp_arr;				
			}
			$arr['CONTACTS']		= $contact_arr;
			$this->LEAD_CODE = $cust_id;
		}else{
			$this->ERROR_CODE = 'ER09';
		}
		if($this->data_type=='json'){
			$this->cust_details = json_encode($arr);
		}
		if($this->ERROR_CODE!=''){
			$this->cust_details = "";
		} 
	}
	
	/**
	 * @param string $emp_id
	 *
	 * @return void
	 */
	public function get_employee_license_info($emp_id){
		
		if(!$this->validate_web_installer_minimum_version()){
			$this->ERROR_CODE = 'ER15';
			return;
		}
		
		$emp_arr = get_emp_master($emp_id, 'A', null, false);
		$type = "Employee";
		if((int)$emp_arr[0][16]==0){
			$type = "Partner";
		}
		$version_dtl = get_latest_product_version(array('500-07.0','500-06.5','200-06.0'));  //TODO: schema for primary products passed
		$vers_xml = "";
		for($i=0; $i < count($version_dtl); $i++){
			$fcode 	= $version_dtl[$i][0];
			$pgroup = $version_dtl[$i][1]; 
			$stable_vers_arr = get_latest_product_version(array($fcode."-".$pgroup),'stable');
			$stable_version 	= isset($stable_vers_arr[0][2])?$stable_vers_arr[0][2]:'';
			$stable_setup 		= isset($stable_vers_arr[0][4])?$stable_vers_arr[0][4]:'';
			$stable_sample_db 	= isset($stable_vers_arr[0][5])?$stable_vers_arr[0][5]:'';
			$stable_gcmsetup	= isset($stable_vers_arr[0][7])?$stable_vers_arr[0][7]:'';
			$stable_webreport	= isset($stable_vers_arr[0][8])?$stable_vers_arr[0][8]:'';
			$stable_smi	        = isset($stable_vers_arr[0][10])?$stable_vers_arr[0][10]:'';
			$vers_xml .= "<PRODUCT NAME='".$version_dtl[$i][3]."' FCODE='$fcode' GROUP='$pgroup' ALIAS='".$version_dtl[$i][6]."'".
						" VERSION='".$version_dtl[$i][2]."' SETUP='".$version_dtl[$i][4]."' SAMPLE_DB='".$version_dtl[$i][5]."' RCM_SETUP='".$version_dtl[$i][7]."' WEB_REPORTER='".$version_dtl[$i][8]."' ".
						" STABLE_VERSION='$stable_version' STABLE_SETUP='$stable_setup' STABLE_SAMPLE_DB='$stable_sample_db' STABLE_RCM_SETUP='$stable_gcmsetup' STABLE_WEB_REPORTER='$stable_webreport' STABLE_SMI_WR_PATH='$stable_smi' />";
		}
		$order_no = substr($emp_arr[0][3], -10).substr("00000".$emp_id, -5)."0003";
		$this->cust_details="<BUSINESS_NAME>".$emp_arr[0][1]."</BUSINESS_NAME>".
				"<NEED_OTP>false</NEED_OTP>".
				"<MOBILE_NO>".$emp_arr[0][3]."</MOBILE_NO>".
				"<EMAIL>".$emp_arr[0][4]."</EMAIL>".
				"<VERTICAL_ID></VERTICAL_ID>".
				"<VERTICAL_NAME></VERTICAL_NAME>".
				"<EDITION_NAME></EDITION_NAME>".
				"<PRODUCT_DETAILS>".
					$vers_xml.
				"</PRODUCT_DETAILS>".
				"<COUNTRY>India</COUNTRY>".
				"<country_code>91</country_code>".
				"<STATE></STATE>".
				"<state_code></state_code>".
				"<TYPE>$type</TYPE>".
				"<USER_TYPE>L</USER_TYPE>".
				"<ORDER_NO>$order_no</ORDER_NO>".
				"<SKEW_CODE></SKEW_CODE>".
				"<INSTALL_TYPE>NEW</INSTALL_TYPE>".
				"<REBRANDED_NAME></REBRANDED_NAME>".
				"<CONNECT_PLUS_TOKEN></CONNECT_PLUS_TOKEN>";
		$this->LEAD_CODE = $emp_id;
	}
	
	/**
	 * @param string $key_id
	 *
	 * @return void
	 */
	public function process_the_key_id($key_id){
		if(!is_numeric($key_id)){
			$this->ERROR_CODE='ER10';
			return;
		}
		$emp_check = mysqli_num_rows(execute_my_query("select GEM_EMP_ID from gft_emp_master where GEM_EMP_ID='$key_id' and GEM_STATUS='A'"));
		if($emp_check == 1){
			$this->get_employee_license_info($key_id);
		}else{
			$this->return_cutomer_info($key_id);
		}
	}
	
	/**
	 * @param string $version
	 * @param string $purpose
	 *
	 * @return void
	 */
	public function get_master_link_for_version($version,$purpose){
		if($this->LEAD_CODE==''){
			$this->ERROR_CODE='ER04';
		}else {
			$product_family_code = get_single_value_from_single_table("GPM_HEAD_FAMILY","gft_product_family_master", "GPM_PRODUCT_CODE", $this->product_code);
			$sel_que=" select GPV_VERSION,GPV_SAMPLE_DB_PATH,GPV_DEFAULT_MASTER_PATH,GPV_GST_DEFAULT_MASTER_PATH,GPV_WEB_REPORTER_PATH,GPV_WEB_REPORTER_HASH,GPV_EASY_PRINT_PATH,GPV_RTA_PATH,GPV_PRA_PATH,GPV_GOBILL_PATH,GPM_WEBSITE_LINK,GPM_STORE_PDESC ".
				" from gft_product_version_master join gft_product_family_master on (GPM_PRODUCT_CODE=GPV_PRODUCT_CODE) where GPV_VERSION='$version' and GPV_PRODUCT_CODE='$product_family_code' ";
			if( ($this->pcode!='') && is_numeric($this->pcode)){
				$sel_que=" select GPV_VERSION,GPV_SAMPLE_DB_PATH,GPV_DEFAULT_MASTER_PATH,GPV_GST_DEFAULT_MASTER_PATH,GPV_WEB_REPORTER_PATH,GPV_WEB_REPORTER_HASH,GPV_EASY_PRINT_PATH,GPV_RTA_PATH,GPV_PRA_PATH,GPV_GOBILL_PATH,GPM_WEBSITE_LINK,GPM_STORE_PDESC ".
						" from gft_product_version_master,gft_product_family_master where GPV_VERSION='$version' and GPV_PRODUCT_CODE='$product_family_code' and GPM_PRODUCT_CODE='$this->pcode' ";
			}
			$sel_res = execute_my_query($sel_que);
			if($row1 = mysqli_fetch_array($sel_res)){
				if($purpose=='download_link'){
					$website_link 	= $row1['GPM_WEBSITE_LINK'];
					$msg_desc 		= $row1['GPM_STORE_PDESC'];
					$download_path 	= $row1['GPV_WEB_REPORTER_PATH'];
					$download_hash  = $row1['GPV_WEB_REPORTER_HASH'];
					if($this->pcode=='60'){
						$download_path = $row1['GPV_EASY_PRINT_PATH'];
						$download_hash = "";
					}else if($this->pcode=='901'){
                        $download_path = $row1['GPV_RTA_PATH'];
                        $download_hash = "";
                    }else if($this->pcode=='518'){
                        $download_path = $row1['GPV_PRA_PATH'];
                        $download_hash = "";
                    }else if($this->pcode=='539'){
                        $download_path = $row1['GPV_GOBILL_PATH'];
                        $download_hash = "";
                    }else if($this->pcode=='20'){
                        $download_path = "https://cdn-download.gofrugal.com/Delight/Product_Delivery/GOFRUGAL_PD.exe";
			$download_hash = "";
			$msg_desc = "GOFRUGAL Product Delivery Tool";
                    }
					$this->cust_details = "<DOWNLOAD_LINK>".$download_path."</DOWNLOAD_LINK>".
									    "<HASH_VALUE>$download_hash</HASH_VALUE>".
										"<DESCRIPTION>$msg_desc</DESCRIPTION>".
										"<RELEASE_NOTES>".$website_link."</RELEASE_NOTES>";
				}else if($purpose=='master_link'){
					$vat_path = $row1['GPV_DEFAULT_MASTER_PATH'];
					$gst_path = $row1['GPV_GST_DEFAULT_MASTER_PATH'];
					$default_master = $vat_path;
					if($gst_path!=''){
						$glh_country = get_single_value_from_single_table("GLH_COUNTRY", "gft_lead_hdr", "GLH_LEAD_CODE", $this->LEAD_CODE);
						if( ($this->LOCAL_USER=='Y') || (strcasecmp($glh_country, "India")==0) ){
							$default_master = $gst_path;
						}
					}
					if($default_master!=''){
						$this->cust_details = "<DEFAULT_MASTER_PATH>".$default_master."</DEFAULT_MASTER_PATH>";
					}else{				
						$this->ERROR_CODE='ER08';
					}
				}
			}else{
				$this->ERROR_CODE = 'ER06';
			}
		}
	}
	
	/**
	 * @return void
	 */
	public function generate_mailer_for_db_password(){
		$sel_que=" select GLH_LEAD_CODE,GLH_CUST_NAME, group_concat(distinct GCC_CONTACT_NO) as email_contact from gft_lead_hdr ".
				 " left join gft_customer_contact_dtl on (GLH_LEAD_CODE=GCC_LEAD_CODE and gcc_designation=1 and gcc_contact_type=4) ". //properitor email
				 " where GLH_LEAD_CODE='$this->LEAD_CODE' ";
		$que_res = execute_my_query($sel_que);
		if($row_data = mysqli_fetch_array($que_res)){
			$to_mail = $row_data['email_contact'];
			$cust_name	= $row_data['GLH_CUST_NAME'];
			if($to_mail==''){
				$this->cust_details = "<MESSAGE>Proprietor Email Id Not Available in CRM. Please Register Your Proprietor Email Id to Proceed</MESSAGE>";
				return;
			}
			$email_id_arr = explode(',', $to_mail);
			$order_fullfill_no = $this->order_no.substr("0000".$this->fullfill_no, -4);
			$remote_address = $_SERVER['REMOTE_ADDR'];
			$confirm_code	=	md5(generatePassword());			
			$sql_update	= " UPDATE gft_lic_surrender set GLS_CODE_STATUS='I' where GLS_PURPOSE=2 and GLS_ORDER_WITH_FILL_NO='$order_fullfill_no' AND GLS_PRODUCT_CODE=$this->product_code ";
			execute_my_query($sql_update);
			$sql_insert	=	" INSERT INTO gft_lic_surrender	(GLS_PURPOSE,GLS_ORDER_WITH_FILL_NO, GLS_PRODUCT_CODE, GLS_PRODUCT_SKEW, GLS_LEAD_CODE, GLS_CONFIRM_CODE, GLS_CREATE_DATE, GLS_SENT_MAIL, GLS_IP_ADDRESS) ".
					" VALUES('2','$order_fullfill_no', $this->product_code, '$this->product_skew', $this->LEAD_CODE, '$confirm_code', now(),'$to_mail','$remote_address') ";
			if(execute_my_query($sql_insert)){
				$insertid			=	mysqli_insert_id_wrapper();
				$idendity_arr 		= get_details_from_idendity($this->idendity);
				$param_arr['glsId']	= "$insertid";
				$param_arr['confirm_code'] 	= $confirm_code;
				$param_arr['install_id']	= isset($idendity_arr[1])?$idendity_arr[1]:'';
				
				$confirm_link	=	"http://".$_SERVER['SERVER_NAME']."/service/process_the_request.php?purpose=sql&req=".base64_encode(json_encode($param_arr));
				//confirmation link in mail
				$db_sms_content_config=array(
						'Customer_Surrender_Link'=>array($confirm_link),
						'Order_No'=>array($order_fullfill_no),
						'Customer_Id'=>array($this->LEAD_CODE),
						'Customer_Name'=>array($cust_name)
						);
				send_formatted_mail_content($db_sms_content_config,86,231,null,null,$email_id_arr);
				$this->cust_details = "<MESSAGE>A confirmation mail has been sent to your email. Please check your mail to proceed further</MESSAGE>";
			}
		}
	}
	
	/**
	 * @return  void
	 */
	public function prepare_for_reinstallation(){
		$prepared_time = date('Y-m-d H:i:s');
		if($this->LOCAL_USER=='Y'){
			$this->cust_details="<STATUS>Success</STATUS>".
					"<CLONE_TIMESTAMP>".strtotime($prepared_time)."</CLONE_TIMESTAMP>";
			return;
		}
		if(!is_valid_asa($this->install_id)){
			$this->ERROR_CODE='ER14';
			return;
		}		
		$update_query = " update gft_reinstall_prepare_dtl set GRP_PREPARE_STATUS=0,GRP_UPDATED_DATETIME=now() ".
				" where GRP_INSTALL_ID='$this->install_id' and GRP_PREPARE_STATUS=1 ";
		execute_my_query($update_query);
		$insert_query = " insert into gft_reinstall_prepare_dtl (GRP_INSTALL_ID,GRP_PREPARE_STATUS,GRP_PREPARED_DATETIME,GRP_UPDATED_DATETIME) ".
				" values ('$this->install_id','1','$prepared_time','$prepared_time') ";
		$ins_res = execute_my_query($insert_query);
		if($ins_res){
			$this->cust_details="<STATUS>Success</STATUS>".
					"<CLONE_TIMESTAMP>".strtotime($prepared_time)."</CLONE_TIMESTAMP>";
		}else{
			$this->ERROR_CODE='ER11';
		}
	}
	
	/**
	 * @return void
	 */
	public function inactive_the_reinstallation_backup(){
		$update_query = " update gft_reinstall_prepare_dtl set GRP_PREPARE_STATUS=0,GRP_UPDATED_DATETIME=now() ".
				" where GRP_INSTALL_ID='$this->install_id' and GRP_PREPARE_STATUS=1 ";
		$upd_res = execute_my_query($update_query);
		if($upd_res){
			$this->cust_details="<STATUS>Success</STATUS>";
		}else{
			$this->ERROR_CODE='ER11';
		}
	}
	
	/**
	 * @return void
	 */
	public function verify_the_reinstallation_backup(){
		if($this->LOCAL_USER=='Y'){
			$this->cust_details="<STATUS>Success</STATUS>";
			return;
		}
		$sel_query= " select max(GRP_PREPARED_DATETIME) as clone_time from gft_reinstall_prepare_dtl ".
				" where GRP_INSTALL_ID='$this->install_id' and GRP_PREPARE_STATUS=1 having clone_time is not null ";
		$que_res = execute_my_query($sel_query);
		if($row1 = mysqli_fetch_array($que_res)){
			if(strtotime($row1['clone_time'])==$this->clone_timestamp){
				$this->cust_details="<STATUS>Success</STATUS>";
			}else{
				$this->ERROR_CODE='ER12';
			}
		}else{
			$this->ERROR_CODE='ER13';
		}
	}
	
	/**
	 * @param string $customerId
	 * 
	 * @return void
	 */
	public function process_send_otp($customerId){
		if(send_otp_to_mobile($customerId,$this->contact)){
			$this->cust_details = "<STATUS>success</STATUS>";
		}else{
			$this->ERROR_CODE = 'ER18';
			return;
		}
	}
	
	/**
	 * @return void
	 */
	public function check_version_for_apply(){
		$stat = "active";
		$sql1 = " select GPV_PRODUCT_STATUS,GPV_CURRENT_VERSION,GPV_IS_MINIMUM_VERSION from gft_product_version_master ".
				" where GPV_PRODUCT_CODE='$this->pcode' and GPV_VERSION_FAMILY='$this->pgroup' and GPV_VERSION='$this->version' ";
		$res1 = execute_my_query($sql1);
		if($row1 = mysqli_fetch_array($res1)){
			$vers_stat	= $row1['GPV_PRODUCT_STATUS'];
			if($vers_stat=='I'){
				$stat = "inactive";
			}else {
				$curr_vers 	= (int)$row1['GPV_CURRENT_VERSION'];
				$min_vers	= (int)$row1['GPV_IS_MINIMUM_VERSION'];
				if ( ($min_vers==0) && ($curr_vers!=1) ){
					$stat = "latest_available";
				}
			}
		}else{
			$stat = "not_available";
		}
		$this->cust_details = "<STATUS>$stat</STATUS>";
	}
	
	/**
	 * @return void
	 */
	public function __construct() {
		$this->idendity	=	isset($_REQUEST['idendity'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['idendity']):"";
		$this->eidendity=	isset($_REQUEST['eidendity'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['eidendity']):"";
		$this->purpose	=	isset($_REQUEST['purpose'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['purpose']):"";
		$this->version	=	isset($_REQUEST['version'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['version']):"";
		$this->pcode	=	isset($_REQUEST['pcode'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['pcode']):"";
		$this->pgroup	=	isset($_REQUEST['pgroup'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['pgroup']):"";
		$this->data_type=	isset($_REQUEST['data_type'])?(string)$_REQUEST['data_type']:'';
		$cust_id		=	isset($_REQUEST['cust_id'])?mysqli_real_escape_string_wrapper((string)$_REQUEST['cust_id']):"";
		$compile_type  	= 	isset($_REQUEST['compile_type'])?strtoupper((string)$_REQUEST['compile_type']):'';
		$this->contact	=	isset($_REQUEST['contact'])?(string)$_REQUEST['contact']:'';
		$this->required	=	isset($_REQUEST['required'])?(string)$_REQUEST['required']:''; 
		$this->otp		=	isset($_REQUEST['otp'])?(string)$_REQUEST['otp']:''; 
		$this->install_mode    =    isset($_REQUEST['mode'])?(string)$_REQUEST['mode']:'';
		$this->clone_timestamp =	isset($_REQUEST['clone_timestamp'])?(int)$_REQUEST['clone_timestamp']:0;
		
		switch ($this->purpose) {
			case 'version_check' : $this->request_type = '23';break;
			case 'cust_info'	 : $this->request_type = '20';break;
			case 'master_link'	 : $this->request_type = '24';break;
			case 'download_link' : $this->request_type = '28';break;
			case 'db_password' 	 : $this->request_type = '29';break;
			case 'reinstall_preparation': $this->request_type = '37';break;
			case 'inactive_last_backup'	: $this->request_type = '38';break;
			case 'verify_backup'	 	: $this->request_type = '39';break;
			case 'send_otp' 	 : $this->request_type = '40';break;
			case 'resend_otp' 	 : $this->request_type = '40';break;
			case 'version_check_before_apply' : $this->request_type = '41';break;
			default: $this->request_type = '18';  //auto sync 
		}
		
		if(in_array($this->request_type,array('18','23','24','28','29','37','38','39'))){
			if(strlen($this->idendity)!=27){
				$this->ERROR_CODE='ER01';
				return;
			}
			if(strtoupper(md5($this->idendity))!=$this->eidendity){
				$this->ERROR_CODE='ER02';
				return;
			}
			$this->order_no = substr($this->idendity,0,15);
			$this->fullfill_no = substr($this->idendity,15,4);
			$this->product_code = substr($this->idendity, 19,3);
			$this->product_skew = substr($this->idendity, 22,2).'.'.substr($this->idendity, 24,3);
			$this->skew_group = substr($this->product_skew, 0,4);
			$dtl_arr = get_details_from_idendity($this->idendity);
			if($compile_type=='MP'){
				$reference_number= substr($this->idendity,0,19);
				$this->LEAD_CODE = get_single_value_from_single_table("GML_PARTNER_ID", "gft_mp_license_dtl", "GML_REFERENCE_NO", $reference_number);
			}else{
				$this->LEAD_CODE = isset($dtl_arr[0])?$dtl_arr[0]:'';
			}
			$this->install_id = isset($dtl_arr[1])?$dtl_arr[1]:'';
			if($this->LEAD_CODE==''){
				if(in_array($this->request_type,array('23','24','28','37','38','39'))){ //to allow employee license
					$this->LEAD_CODE = check_for_employee_mbile_and_id($this->order_no);
					$this->LOCAL_USER = 'Y';
				}
				if($this->LEAD_CODE==""){
					$this->ERROR_CODE='ER04';
					return;
				}
			}
		}
		switch ($this->request_type) {
			case '18' : $this->check_for_license_changes();break;
			case '20' : $this->process_the_key_id($cust_id);break; //cust_info api
			case '23' : $this->check_for_version(); break;
			case '24' : $this->get_master_link_for_version($this->version,$this->purpose);break;
			case '28' : $this->get_master_link_for_version($this->version,$this->purpose);break;
			case '29' : $this->generate_mailer_for_db_password();break;
			case '37' : $this->prepare_for_reinstallation();break;
			case '38' : $this->inactive_the_reinstallation_backup();break;
			case '39' : $this->verify_the_reinstallation_backup();break;
			case '40' : $this->process_send_otp($cust_id);break;
			case '41' : $this->check_version_for_apply();break;
			default	  : break;
		}
	}
}

$automate = new licenseAutomate();
$automate->print_response();

?>
