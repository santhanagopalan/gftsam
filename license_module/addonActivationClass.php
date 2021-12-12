<?php
require_once( __DIR__ . "/../dbcon.php");
require_once( __DIR__ . "/../function.update_in_tables.php");
require_once(__DIR__."/../lic_util.php");
require_once( __DIR__ . "/licenseUpdate.php");

/*.
 forward class addonActivation{
 private boolean function isValidCustomerId(int $customer_id);
 private void function get_primary_order_install_id();
 private boolean function return_addon_product_details();
 private string function get_query_addon_list(int $customer_id,int $pcode=,string $pskew=);
 public void function update_install_information();
 public void function check_employee_license();
 public void function get_ordered_employee();
 }
 .*/



class addonActivation extends licenseUpdate{
	public  $ADD_PRODUCT_DETAILS = /*. (mixed[string][int]) .*/array();
	private $ACTIVATED_LIST      = /*. (mixed[string][int]) .*/array();
	private $RENEWED_LIST        = /*. (mixed[string][int]) .*/array();
	private $ALREADY_ACTIVATED   = /*. (mixed[string][int]) .*/array();
	private $request_arr=/*. (mixed[string]) .*/array();
	private /*. int .*/$PRIMARY_GID_INSTALL_ID=0;
	private /*. int .*/ $CUSTOMER_ID=0;
	private /*. string .*/ $IP_ADDRESS;
	private /*. string .*/ $USERID;
	private /*. string .*/ $GID_EMP_ID;
	private /*. string .*/ $GLC_ONLINE_CONTENT;
	private /*. string .*/ $GLC_DECRYPTED_CONTENT;
	private /*. string .*/ $GLC_ERROR_MESSAGE;
	private /*. string .*/ $GLC_RETURN_DATA;
	private /*. string .*/ $GLC_RETURN_ENCRYPTED_DATA;
	private /*. string .*/ $GLC_STATUS;
	//private /*. int .*/ $FULLFILLMENT_NO=0; //Already defined in licenseUpdate
	//  protected /*. int .*/ $GID_HEAD_OF_FAMILY=0;
	//protected /*. int .*/ $GID_EXPIRE_FOR=0; //Already defined in licenseUpdate
	//protected /*. int .*/ $GID_NO_CLIENTS=0; //Already defined in licenseUpdate
	//protected /*. string .*/$GID_VALIDITY_DATE; //Already defined in licenseUpdate
	//protected /*. string .*/$GID_INSTALL_DATE; //Already defined in licenseUpdate
	//protected /*. string .*/$GID_LIC_PSKEW; //Already defined in licenseUpdate
	//  protected /*. string .*/ $ORDER_NO; //Already defined in licenseUpdate
	//protected /*. string .*/ $PRODUCT_STATUS;
	// protected /*. string .*/ $GLC_ADDITIONAL_ERROR_MESSAGE;
	// private $GID_REF_SERIAL_NO=0;
	private $IS_BACKWARD_COMPATIBLE=false;
	 
	/**
	 * @param string $order_no
	 * @param string $product_code
	 * @param string $fulfilment_no
	 * @param boolean $return_customer_id
	 *
	 * @return string
	 */
	private function get_install_id_license($order_no=null,$product_code=null,$fulfilment_no=null,$return_customer_id=false){
		$query="SELECT GID_INSTALL_ID,GID_LEAD_CODE FROM gft_install_dtl_new g " .
				" WHERE  GID_STATUS='A' ".
				" and ( (GID_LIC_ORDER_NO='$order_no' and GID_LIC_FULLFILLMENT_NO=$fulfilment_no ) " .
				" or (GID_ORDER_NO='$order_no' and GID_FULLFILLMENT_NO=$fulfilment_no) ) ".
				" and GID_LIC_PCODE='$product_code' ";
		$result=execute_my_query($query);
		if(mysqli_num_rows($result)==1){
			if($data=mysqli_fetch_array($result)){
				if($data['GID_INSTALL_ID']!=''){
					if($return_customer_id==true){
						return $data['GID_LEAD_CODE'];
					}
					return $data['GID_INSTALL_ID'];
				}else{
					return null;
				}
			}
		}

		return null;
	}

	/**
	 * @return void
	 */
	private function addon_common_check_condition(){
		if(empty($this->request_arr)){
			$this->GLC_ERROR_CODE='E012';
			return ;
		}
			
		if(empty($this->request_arr['root_orderno'])){
			$this->GLC_ERROR_CODE='E002'; /* order number not found */
			return;
		}else if (empty($this->request_arr['request_pcode'])){
			$this->GLC_ERROR_CODE='E003'; /* product code not found */
			return;
		}else if(strlen((string)$this->request_arr['root_orderno'])!=19){
			$this->GLC_ERROR_CODE='E004'; /* Invalid order number */
			return;
		}else if(empty($this->request_arr['customer_id'])){
			$this->GLC_ERROR_CODE='E005'; /* customer id Empty */
			return;
		}else if(empty($this->request_arr['hard_disk_id'])){
			$this->GLC_ERROR_CODE='E011';
			return;
		}
		else{
			if(strlen((string)$this->request_arr['request_pcode'])<3){
				$this->GLC_ERROR_CODE='E008';
				return;
			}
			$this->PCODE=(int)substr((string)$this->request_arr['request_pcode'],0,3);
			$this->request_arr['root_orderno']=str_replace('-','',$this->request_arr['root_orderno']);
			$this->ORDER_NO=substr((string)$this->request_arr['root_orderno'],0,15);
			$this->FULLFILLMENT_NO=(int)substr((string)$this->request_arr['root_orderno'],-4);
			$this->check_employee_license();
			if($this->LOCAL_USER=='Y'){
				$this->LEAD_CODE=(int)$this->request_arr['customer_id'];
				return;
			}
			if(!$this->isValidCustomerId((int)$this->request_arr['customer_id'])){
				$this->GLC_ERROR_CODE='E006';
				return;
			}
			$this->LEAD_CODE=$this->CUSTOMER_ID;
			$this->get_primary_order_install_id();
			if (!$this->IS_BACKWARD_COMPATIBLE){
				//to avoid req_act backward compatible
				if($this->PRIMARY_GID_INSTALL_ID==0){
					$this->GLC_ERROR_CODE='E009';
					return;
				}
				if( ($this->WEB_REQUEST_TYPE==2) && isset($this->request_arr['hard_disk_id']) && ($this->GID_SYS_HKEY!='') && ($this->request_arr['hard_disk_id']!=$this->GID_SYS_HKEY) ){
			        $this->GLC_ERROR_CODE='M004';
			        return;
				}
			}

			if((int)$this->request_arr['customer_id']!=$this->GID_LEAD_CODE){
				$this->GLC_ERROR_CODE='E007';
				return;
			}
			return;
		}
	}

	/**
	 * @param int $pcode
	 *
	 * @return boolean
	 */
	private function check_free_license_skew_available($pcode){
		$query="select GPM_PRODUCT_CODE,GPM_PRODUCT_SKEW  " .
				"from gft_product_master where gpm_product_code=$pcode and gpm_free_edition='Y' " .
				"and gpm_status='A' and gpm_license_type in (1,2) ";

		$result=execute_my_query($query);
		if($result){
			if(mysqli_num_rows($result)==0){
				$this->GLC_ERROR_CODE='P001';
				return false;
			}
			if(mysqli_num_rows($result)>1){
				$this->GLC_ERROR_CODE='P002';
				return false;
			}else{
				$qd=mysqli_fetch_array($result);
				$this->PSKEW=$qd['GPM_PRODUCT_SKEW'];
				$this->PCODE=(int)$qd['GPM_PRODUCT_CODE'];
				return true;
			}
		}
		return false;
	}
	/**
	 * @param int $pcode
	 * @param string $pskew
	 *
	 * @return boolean
	 **/
	private function check_trial_license_skew_available($pcode,$pskew=''){
		$query="select GPM_PRODUCT_CODE,GPM_PRODUCT_SKEW  " .
				"from gft_product_master where gpm_product_code=$pcode and gpm_free_edition='Y' " .
				"and gpm_status='A' and gpm_license_type in (3) ";
		if($pskew!=''){
		    $query .= " and GPM_PRODUCT_SKEW='$pskew' ";
		}

		$result=execute_my_query($query);
		if($result){
			if(mysqli_num_rows($result)==0){
				$this->GLC_ERROR_CODE='P001';
				return false;
			}
			if(mysqli_num_rows($result)>1){
				$this->GLC_ERROR_CODE='P002';
				return false;
			}else{
				$qd=mysqli_fetch_array($result);
				$this->PSKEW=$qd['GPM_PRODUCT_SKEW'];
				$this->PCODE=(int)$qd['GPM_PRODUCT_CODE'];
				return true;
			}
		}
		return false;
	}
	/**
	 * @param int $lead_code
	 * @param int $pcode
	 * @param string $pskew
	 *
	 * @return boolean
	 **/
	private function check_exist_pcode_avail_already($lead_code,$pcode,$pskew){
		 
		$query_ins="select GID_INSTALL_ID,GID_ORDER_NO,GID_FULLFILLMENT_NO from gft_install_dtl_new ins " .
				"join gft_order_hdr oh on (god_order_no=gid_order_no and god_order_status='A') " .
				"where gid_status='A' and gid_lead_code=$lead_code  and gid_lic_pcode=$pcode and gid_lic_pskew='$pskew' ";
		 
		$result_ins=execute_my_query($query_ins);
		if($result_ins){
			if(mysqli_num_rows($result_ins)==1){
				$qd=mysqli_fetch_array($result_ins);
				$this->GID_INSTALL_ID=(int)$qd['GID_INSTALL_ID'];
				$this->GID_ORDER_NO=$qd['GID_ORDER_NO'];
				$this->GID_FULLFILLMENT_NO=(int)$qd['GID_FULLFILLMENT_NO'];
				return true;
			}else if(mysqli_num_rows($result_ins)>1){
				$this->GLC_ERROR_CODE='TF01';
				return false;
			}
		}
		 
		$query_ord="select GOD_ORDER_NO ,if(god_order_splict=1,gco_fullfillment_no,gop_fullfillment_no) as full_no " .
				"from gft_order_hdr oh " .
				"join gft_order_product_dtl opd on (gop_order_no=god_order_no and gop_product_code=$pcode and gop_product_skew='$pskew' ) " .
				"left join gft_cp_order_dtl gco on (gco_order_no=gop_order_no and gco_product_code=gop_product_code and gco_skew=gop_product_skew )" .
				"where god_order_status='A' and (god_lead_code=$lead_code  or gco_cust_code=$lead_code) ";
		$result_ord=execute_my_query($query_ord);
		if($result_ord){
			if(mysqli_num_rows($result_ord)>1){
				$this->GLC_ERROR_CODE='TF03';
				return false;
			}
			else if(mysqli_num_rows($result_ord)==1){
				$qd=mysqli_fetch_array($result_ord);
				if($qd['full_no']=='' or $qd['full_no']=='0'){
					$this->GLC_ERROR_CODE='E015';
					return false;
				}else{
					$this->GID_ORDER_NO=$qd['GOD_ORDER_NO'];
					$this->GID_FULLFILLMENT_NO=(int)$qd['full_no'];
					return true;
				}
			}else {
				return false;
			}
		}

		$this->GLC_ERROR_CODE='TF01';
		return false;
		 
	}/* end of function */

	/**
	 * @param string $xmlRaw
	 * @param string[int] $xmlFieldNames
	 *
	 * @return string[string]
	 */

	private function give_parsed_xml($xmlRaw,$xmlFieldNames){
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
	 *  @param string $identity
	 *  
	 *  @return void
	 */
	private function update_addon_request_dtl_from_identity($identity){
		$product_dtl = get_details_from_idendity($identity);
		$order_no = isset($product_dtl[2])?$product_dtl[2]:"";
		$fullfillment_no = isset($product_dtl[3])?$product_dtl[3]:"";
		$root_orderno = $order_no.$fullfillment_no;
		$this->request_arr['root_orderno']=$root_orderno;
		$this->request_arr['request_pcode']= isset($product_dtl[4])?$product_dtl[4]:"";
		$this->request_arr['request_pskew']= isset($product_dtl[5])?$product_dtl[5]:"";
		$this->request_arr['customer_id']=isset($product_dtl[0])?$product_dtl[0]:"";
		$this->request_arr['hard_disk_id']='1';
	}
	/**
	 * @param string $request_string
	 *
	 * @return void
	 */
	private function decrypt_addon_request($request_string){
		global $secret;

		//Value of $request_string can be $_REQUEST['req_list']
		$temp_url_decode=urldecode($request_string);
		if (strpos($temp_url_decode,"orderno")>0 || strpos($temp_url_decode,"request_pcode")>0){
			$this->IS_BACKWARD_COMPATIBLE=true;
			//TODO: is there any other better solution?
		}


		if ($this->IS_BACKWARD_COMPATIBLE){
	  if (isset($_REQUEST['req_list'])){
		  $this->request_arr=json_decode(urldecode((string)$_REQUEST['req_list']),true);

		  if (empty($this->request_arr['request_pcode'])){
			  $this->GLC_ERROR_CODE='E002';
			  $this->GLC_ADDITIONAL_ERROR_MESSAGE='Product code is missing';
			  return;
		  }

		  if (strlen($this->request_arr['request_pcode']) <3){
			  $this->GLC_ERROR_CODE='PR02';
			  return ;
		  }

		  $product_code=substr($this->request_arr['request_pcode'],0,3);
		  $root_orderno=(string)str_replace('-','',$this->request_arr['orderno']);
		  $only_order_no=substr($root_orderno,0,15);
		  $only_fullno=substr($root_orderno,-4);
		  $customer_id=$this->get_install_id_license($only_order_no,$product_code,$only_fullno,true);

		  $this->request_arr['root_orderno']=$root_orderno;
		  $this->request_arr['customer_id']=$customer_id;
		  $this->request_arr['hard_disk_id']='old';
	  }else if (isset($_REQUEST['req_act'])){
		  $this->request_arr=json_decode(urldecode((string)$_REQUEST['req_act']),true);


		  if (!isset($this->request_arr['activated'][0]['orderno'])){
			  //This case means 'activated' not found in JSON
		  	$this->GLC_ERROR_CODE='E013';
		  	return;
		  }

		  //TODO: Need to have better solution
		  $root_orderno=$this->request_arr['activated'][0]['orderno'];
		  $this->request_arr['root_orderno']=$root_orderno;
		  $this->request_arr['hard_disk_id']='old';
		  $this->GID_LEAD_CODE=$this->request_arr['customer_id'];
	  }else{
		  $this->GLC_ERROR_CODE="Invalid request";
	  }


		}else{
			$this->GLC_DECRYPTED_CONTENT=lic_decrypt($request_string,$secret);
			//$this->GLC_DECRYPTED_CONTENT=urldecode($_REQUEST['req_list']);
			$parsed_xml=$this->give_parsed_xml($this->GLC_DECRYPTED_CONTENT,array('REQ'));
			$this->request_arr=json_decode($parsed_xml['REQ'],true);
		}
		if(isset($_REQUEST['request_trial_addon'])){
			$headers 	= apache_request_headers();
			$identity	= isset($headers['X-identity'])?$headers['X-identity']:'';
			$eidentity	= isset($headers['X-eidentity'])?$headers['X-eidentity']:'';
			if( $identity!="" && $eidentity!="" && (strcasecmp(md5($identity),$eidentity)==0) ){
				$this->update_addon_request_dtl_from_identity($identity);
			}
			if(isset($this->request_arr['installed_by']) && $this->request_arr['installed_by']!=""){
				$arr_dtl 			= get_installed_employee_from_contact($this->request_arr['installed_by']);
				$this->GOD_EMP_ID	= isset($arr_dtl['GEM_EMP_ID'])?(int)$arr_dtl['GEM_EMP_ID']:'9999';
			}else if(isset($this->request_arr['create_trial_pcode']) && $this->request_arr['create_trial_pcode']!="" && 
			    in_array($this->request_arr['create_trial_pcode'], array('742'))){// assign lead owner as order created by employee 
			        $query_to_take_lead_owner = " SELECT glh_lfd_emp_id FROM gft_lead_hdr ".
			 			        " INNER JOIN gft_emp_master ON(GEM_EMP_ID=GLH_LFD_EMP_ID) ".
			 			        " WHERE GLH_LEAD_CODE='".$this->request_arr['customer_id']."' AND GEM_STATUS='A' AND GEM_EMP_ID NOT IN('9999','9998')";
			        $currect_lead_owner = (int)get_single_value_from_single_query("glh_lfd_emp_id", $query_to_take_lead_owner);
			        $this->GOD_EMP_ID	= $currect_lead_owner>0?$currect_lead_owner:'9999';
			}
		}
		if(isset($_REQUEST['req_list'])){
	  //skew check to give available addons.
	  if(!$this->IS_BACKWARD_COMPATIBLE){
	  	$this->SKEW_ADDON=isset($this->request_arr['request_pskew'])?(string)$this->request_arr['request_pskew']:'';
	  	if($this->SKEW_ADDON==''){
	  		$this->GLC_ERROR_CODE='E019';
	  		return;
	  	}
	  }
		}

		$this->addon_common_check_condition();
		return;
	}
	/**
	 * @return void
	 */
	private function process_trial_addon_request(){

		$this->WEB_REQUEST_TYPE=15;

		if(empty($_REQUEST['request_trial_addon'])) {
			$this->GLC_ERROR_CODE='E001';
			return;
		}


		$this->decrypt_addon_request((string)$_REQUEST['request_trial_addon']);
		if($this->GLC_ERROR_CODE!=''){
			return;
		}
		if($this->PRIMARY_GID_INSTALL_ID!=0){
			if(empty($this->request_arr['create_trial_pcode'])){
				$this->GLC_ERROR_CODE='E016';
				return;
			}
		}
		$create_pskew = "";
		$parent_code  = isset($this->request_arr['parent_code']) ? (int)$this->request_arr['parent_code'] : 0;
		if($parent_code > 0){
		    $this->PCODE = $parent_code;
		    $tque = " select GPM_PRODUCT_SKEW from gft_addon_feature_master ".
		            " join gft_addon_feature_skew_mapping on (GAM_FEATURE_ID=GAF_ID) ".
		            " join gft_product_master on (GPM_PRODUCT_CODE=GAM_PRODUCT_CODE and GPM_PRODUCT_SKEW=GAM_PRODUCT_SKEW) ".
		            " where GPM_LICENSE_TYPE=3 and GAF_CODE='".$this->request_arr['create_trial_pcode']."'";
		    $tres = execute_my_query($tque);
		    if($trow = mysqli_fetch_array($tres)){
		        $create_pskew = $trow['GPM_PRODUCT_SKEW'];
		    }
		}else {
		    $this->PCODE  = (int)$this->request_arr['create_trial_pcode'];
		}
		if($this->LOCAL_USER=='Y'){
			$query= " select pm.GPM_PRODUCT_CODE, pm.GPM_PRODUCT_SKEW, GPM_VALIDATIAN_TYPE, GPM_VALIDATIAN_METHOD, GPM_WEBSITE_LINK , gpm_product_abr, GPT_TYPE_NAME as Edition, ".
					" GPM_ADDON_CATEGORY from gft_product_master pm ".
					" join gft_product_family_master fm on (pm.GPM_PRODUCT_CODE=fm.GPM_PRODUCT_CODE) ".
					" join gft_product_type_master ptm on (GPT_TYPE_ID=GPM_PRODUCT_TYPE) ".
					" where pm.gpm_product_code=$this->PCODE and gpm_free_edition='Y'  and pm.gpm_status='A' and gpm_license_type in (3) ";
			$result=execute_my_query($query);
			while($qdata=mysqli_fetch_array($result)){
			    $empl_lic = get_addon_formatted_response('YES',$qdata['GPM_PRODUCT_CODE'],$qdata['gpm_product_abr'],'5',add_date(date('Y-m-d'),30),'','2','Trial',
            			        $qdata['Edition'],$qdata['GPM_VALIDATIAN_TYPE'],$qdata['GPM_VALIDATIAN_METHOD'],'Buy',"",$qdata['GPM_WEBSITE_LINK'],$qdata['GPM_ADDON_CATEGORY']);
				array_push($this->ADD_PRODUCT_DETAILS, $empl_lic);
			}
			return;
		}
		if(!$this->check_trial_license_skew_available($this->PCODE,$create_pskew)){
			return;
		}

		$this->check_exist_pcode_avail_already($this->CUSTOMER_ID,$this->PCODE,$this->PSKEW);
		if(!empty($this->GLC_ERROR_CODE)){
			return;
		}else  if (empty($this->GID_ORDER_NO)){
			$this->generate_order_no_on_request($this->PSKEW);
		}
			$this->return_addon_product_details();
			return;
	}

	/**
	 * @return void
	 */
	private function process_free_addon_request(){

		$this->WEB_REQUEST_TYPE=16;

		if(empty($_REQUEST['request_free_addon'])) {
			$this->GLC_ERROR_CODE='E001';
			return;
		}


		$this->decrypt_addon_request((string)$_REQUEST['request_free_addon']);
		if($this->GLC_ERROR_CODE!=''){
			return;
		}
		if($this->PRIMARY_GID_INSTALL_ID!=0){
				
			if(empty($this->request_arr['create_free_pcode'])){
				$this->GLC_ERROR_CODE='E014';
				return;
			}
			$this->PCODE=(int)$this->request_arr['create_free_pcode'];
			if(!$this->check_free_license_skew_available($this->PCODE)){
				return;
			}
				

			$this->check_exist_pcode_avail_already($this->CUSTOMER_ID,$this->PCODE,$this->PSKEW);
			if(!empty($this->GLC_ERROR_CODE)){
				return;
			}else if(empty($this->GID_ORDER_NO)){
				 
				$this->generate_order_no_on_request($this->PSKEW);
			}
			$this->return_addon_product_details();
			return;

		}
	}

	/**
	 * @param int $pcode
	 * @param int $customer_id
	 *
	 * @return void
	 */
	private function update_used_qty_mobil_app($pcode,$customer_id){
		$update_opd="update gft_order_hdr join gft_order_product_dtl opd on (GOP_ORDER_NO=GOD_ORDER_NO) set GOP_USEDQTY=gop_qty " .
				" where god_order_splict=0 and  god_lead_code=$customer_id and gop_product_code=$pcode ";
		execute_my_query($update_opd);

		$update_cod="update gft_order_hdr join gft_cp_order_dtl opd on (GCO_ORDER_NO=GOD_ORDER_NO) set GCO_USEDQTY=GCO_CUST_QTY " .
				" where god_order_splict=1 and  gco_cust_code=$customer_id and gco_product_code=$pcode ";
		execute_my_query($update_cod);
	}
	/**
	 * @param int $customer_id
	 *
	 * @return boolean
	 */
	private function isValidCustomerId($customer_id){
		$query_cust_exists="select glh_lead_code from gft_lead_hdr where glh_lead_code='$customer_id' ";
		$result_cust_exists=execute_my_query($query_cust_exists);
		if(mysqli_num_rows($result_cust_exists) > 0){
			$this->CUSTOMER_ID=$customer_id;
			return true;
		}

		return false;
	}

	/**
	 * @return void
	 */
	public function get_addon_products(){
	    global $global_dealer_pcodes_arr;
		$used='';
		if(is_array($this->USED_PCODE) && count($this->USED_PCODE) > 0){
			$used ="'".implode("','", $this->USED_PCODE)."'";
		}
		$que_res = execute_my_query("select GID_LIC_PCODE from gft_install_dtl_new where GID_LEAD_CODE='$this->CUSTOMER_ID' and GID_STATUS='U'");
		$uninstalled_pcodes = /*. (string[int]) .*/array();
		while ($que_row = mysqli_fetch_array($que_res)){
		    $uninstalled_pcodes[] = $que_row['GID_LIC_PCODE'];
		}
        $local_addon_arr = array();
		if(isset($this->request_arr['request_pcode']) && isset($this->request_arr['request_pskew'])){
		    $pid = $this->request_arr['request_pcode'].$this->request_arr['request_pskew'];
		    $local_addon_arr = explode(",", get_single_value_from_single_table("GLL_ADDON_PCODE", "gft_local_license_master", "GLL_PRODUCT_ID", $pid));
		}
		$query_addon =  "select GAP_ADDON_PRODUCT_CODE,pm.GPM_PRODUCT_SKEW,GPM_PRODUCT_ABR,pm.GPM_SKEW_DESC,pm.GPM_FREE_EDITION,pm.GPM_LICENSE_TYPE,GPT_TYPE_NAME as 'Edition',GAP_ADDON_PRODUCT_CODE as used, ".
				" GPM_WEBSITE_LINK, pm.GPM_VALIDATIAN_METHOD, pm.GPM_VALIDATIAN_TYPE, GAP_USER_COUNT,if(pm.GFT_SKEW_PROPERTY=11,2,1) as EXPIRE_FOR,GPL_LICENSE_TYPE_NAME,GPM_ADDON_CATEGORY, ".
				" GPM_LICENSE_GRACE_PERIOD_DAYS from gft_addon_product_map ".
				" left join gft_lead_hdr on (GLH_LEAD_CODE='$this->CUSTOMER_ID' and if(GAP_VARTICAL_ID=0,1,GLH_VERTICAL_CODE=GAP_VARTICAL_ID)) ".     //lead vertical
				" join gft_product_master pmb on (pmb.GPM_PRODUCT_CODE = GAP_PRODUCT_CODE and  REPLACE(pmb.GPM_PRODUCT_SKEW,'.','')='$this->SKEW_ADDON' and if(GAP_EDITION_ID=0,1,pmb.GPM_PRODUCT_TYPE=GAP_EDITION_ID) ) ".  //base skew match
				" join gft_product_family_master pfm on (pfm.GPM_PRODUCT_CODE=GAP_ADDON_PRODUCT_CODE and pfm.GPM_STATUS='A' and (pfm.GPM_IS_INTERNAL_PRODUCT=2 or pfm.GPM_CATEGORY=6)) ".   //addon code match
				" join gft_product_master pm on (pfm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE and pm.gpm_status='A' and pm.GFT_SKEW_PROPERTY in (1,11,18) ) ".   //addon skews match
				" left join gft_product_type_master ptm on (GPT_TYPE_ID=pm.GPM_PRODUCT_TYPE) ".
				" left join gft_product_license_type on (GPL_LICENSE_TYPE_ID=pm.GPM_LICENSE_TYPE) ".
				" where GAP_STATUS='A' and substr(GAP_PRODUCT_CODE,1,3)='$this->PCODE' and REPLACE(substr(GAP_PRODUCT_CODE,5,8),'.','')=SUBSTR('$this->SKEW_ADDON',1,3)";
		if($used!=''){
			$query_addon.=" having used not in ($used)";
		}
		$query_addon.=" order by GAP_USER_COUNT desc,GAP_ADDON_PRODUCT_CODE,GPM_LICENSE_TYPE desc ";
		$pcode_test	=	'';
		$result=execute_my_query($query_addon);
		$installed_date = get_single_value_from_single_table("GID_INSTALL_DATE", "gft_install_dtl_new", "GID_INSTALL_ID", $this->PRIMARY_GID_INSTALL_ID);
		while($qdata=mysqli_fetch_array($result)){
			if($pcode_test!=$qdata['GAP_ADDON_PRODUCT_CODE']){
				$user_cnt = (int)$qdata['GAP_USER_COUNT'];
				if( ($qdata['GAP_ADDON_PRODUCT_CODE']=='703') && (datediff($installed_date,date('Y-m-d'))<=365) && !in_array($this->PCODE, $global_dealer_pcodes_arr) ){ //base product within one year
				    $user_cnt = 1;
				}
				$activation = "NO";
				$user_qty = $validity_period = $lic_type = '';
				if($user_cnt > 0){
					$activation = "YES";
					$user_qty 	= "$user_cnt";
					$lic_type 	= $qdata['GPL_LICENSE_TYPE_NAME'];
					$validity_period = get_single_value_from_single_table("GID_VALIDITY_DATE", "gft_install_dtl_new", "GID_INSTALL_ID", $this->PRIMARY_GID_INSTALL_ID);
				}
				if($this->LOCAL_USER=='Y'){
				    if(in_array($qdata['GAP_ADDON_PRODUCT_CODE'],$local_addon_arr)){
				        $activation = "YES";
				        $validity_period = add_date(date('Y-m-d'),30);
				        $user_qty = '3';
				    }
				}
				$is_free_tag = "NA";
				if($qdata['GPM_FREE_EDITION']=='Y' and $qdata['GPM_LICENSE_TYPE']!='3'){
				    $is_free_tag = 'Free';
				}else{
				    $pcode_test	=	$qdata['GAP_ADDON_PRODUCT_CODE'];
				    if( ($qdata['GPM_LICENSE_TYPE']=='3') && ($activation=='NO') ){
				        $is_free_tag = 'Try & Buy';
				    }else{
				        $is_free_tag = 'Buy';
				    }
				}
				if(in_array($this->PCODE, $global_dealer_pcodes_arr)){
				    $is_free_tag = 'NA';
				}else if(in_array($qdata['GAP_ADDON_PRODUCT_CODE'], $uninstalled_pcodes)){
				    $is_free_tag = 'Buy';
				}
				$base_prod_id = $this->PCODE."-".substr($this->SKEW_ADDON,0,2).".".substr($this->SKEW_ADDON,2,1);
				$root_order = $this->ORDER_NO.substr("0000".$this->FULLFILLMENT_NO,-4);
				$custom_lic_arr = get_addon_features_license_details($qdata['GAP_ADDON_PRODUCT_CODE'],$this->GID_LEAD_CODE,'pos',$base_prod_id);
				$addon_arr = get_addon_formatted_response($activation, $qdata['GAP_ADDON_PRODUCT_CODE'], $qdata['GPM_PRODUCT_ABR'], $user_qty,$validity_period,
				                $root_order,$qdata['EXPIRE_FOR'],$lic_type,$qdata['Edition'],$qdata['GPM_VALIDATIAN_TYPE'],$qdata['GPM_VALIDATIAN_METHOD'],
            				    $is_free_tag,"",$qdata['GPM_WEBSITE_LINK'],$qdata['GPM_ADDON_CATEGORY'],'A',$qdata['GPM_PRODUCT_SKEW'],'','','N','','N','NA','',$custom_lic_arr);
				array_push($this->ADD_PRODUCT_DETAILS, $addon_arr);
			}
				
		}
	}

	/**
	 * @return boolean
	 */
	private function return_addon_product_details(){
	    global $global_dealer_pcodes_arr;
		if($this->PCODE!=0 and $this->PSKEW!=''){
			$query=$this->get_query_addon_list($this->GID_LEAD_CODE,$this->PCODE,$this->PSKEW);
		}else{
			$query=$this->get_query_addon_list($this->GID_LEAD_CODE);
		}
		 
		$result=execute_my_query($query);
		if(!empty($result) && mysqli_num_rows($result)>0){
			while($qdata=mysqli_fetch_array($result)){
				$response_array=array();
				if($qdata['GID_STATUS']==null or $qdata['GID_STATUS']!='U' ){
					$qty_val=$qdata['GID_NO_CLIENTS'];
					if($qdata['GID_INSTALL_ID']==''){
						//	$t_qty=isset($response_array['QTY'])?(int)$response_array['QTY']:0;
						$qty_val=$qdata['GID_NO_CLIENTS_ADD'];
					}
					$orderno_val = $qdata['GOD_ORDER_NO'].substr(('000'.$qdata['GOP_FULLFILLMENT_NO']),-4);
					$pcode = $qdata['pcode'];
					$this->USED_PCODE[]=$qdata['pcode'];
					$this->USED_PSKEW[]=$qdata['GPM_PRODUCT_SKEW'];
					if ($this->IS_BACKWARD_COMPATIBLE){
						if($qdata['pcode']==805){
							continue;
						}
						$response_array['pcode']=$qdata['pcode'];
						$response_array['qty']=$qty_val;
						$response_array['orderno']=$orderno_val;
						$response_array['validity_period']=$qdata['GID_VALIDITY_DATE'];
						$response_array['expiry_type']=$qdata['GID_EXPIRE_FOR'];
						$response_array['status']='A';
					}else{
						if(isset($this->request_arr['request_pskew'])){
							$pcode_skew_group = $this->PCODE."-".substr($this->request_arr['request_pskew'], 0,2).".".substr($this->request_arr['request_pskew'], 2,1);
							$sql_quer = " select GAP_ADDON_PRODUCT_CODE,GAP_USER_COUNT from gft_addon_product_map ".
									" where GAP_ADDON_PRODUCT_CODE='$pcode' and GAP_PRODUCT_CODE='$pcode_skew_group' and GAP_USER_COUNT > 0 ";
							if($row1 = mysqli_fetch_array(execute_my_query($sql_quer))){
							    $qty_val += (int)$row1['GAP_USER_COUNT'];
							}
						}
						$addon_category   = $qdata['GPM_ADDON_CATEGORY'];
						$ns_token         = $qdata['GID_NS_TOKEN'];
						$connect_token    = $qdata['GID_CONNECTPLUS_TOKEN'];
						$validity_date    = $qdata['GID_VALIDITY_DATE'];
						$pilot_license    = $qdata['GPM_PILOT_LICENSE'];
						$grace_period     = (int)$qdata['GPM_LICENSE_GRACE_PERIOD_DAYS'];
						$is_free_flag     = "Buy";
						$license_agreement_stat = "NA";
						if( ($pcode=='706') && ($connect_token=='') ){
							$connect_token = $this->CP_TOKEN = uniqid(null,true);
						}
						if( ($pcode=='538') && ($ns_token=="") ){
						    create_customer_account_in_ns($this->GID_LEAD_CODE, array($this->GID_LEAD_CODE));
						    $ns_token = $this->NS_TOKEN = create_and_update_ns_token($this->GID_LEAD_CODE,$pcode,"create");
						}
						if($pcode=='805'){
						    $qty_val = (int)get_single_value_from_single_query("GAO_ORDER_COUNT", "select GAO_ORDER_COUNT from gft_addon_order_count where GAO_PRODUCT_CODE=$pcode and GAO_INSTALL_ID='$this->PRIMARY_GID_INSTALL_ID'");
						    //$validity_date = "";
						}
						if($pcode=='706'){
						    if(strtotime($qdata['GID_VALIDITY_DATE'])>=strtotime(date('Y-m-d'))){
						        $license_agreement_stat = is_license_agreement_agreed($this->GID_LEAD_CODE,'706') ? "AGREED" : "PENDING";
						    }
						    if( ($qdata['GPM_FREE_EDITION']=='Y') && (strtotime($qdata['GID_INSTALL_DATE'])<=strtotime($qdata['GPM_LAUNCHED_ON'])) ){
						        $pilot_license = "Y";
						    }
						}
						if(in_array($this->PCODE, $global_dealer_pcodes_arr)){
						    $is_free_flag='NA';
						}else if($qdata['GPM_FREE_EDITION']=='Y' and $qdata['GPM_LICENSE_TYPE']!='3'){
						    $is_free_flag='Free';
						}
						$grace_validity = ($grace_period>0) ? date('Y-m-d', strtotime("+$grace_period days", strtotime($qdata['GID_VALIDITY_DATE']))) : "";
						$base_prod_id = $this->PCODE."-".substr($this->SKEW_ADDON,0,2).".".substr($this->SKEW_ADDON,2,1);
						$custom_lic_arr = get_addon_features_license_details($pcode,$this->GID_LEAD_CODE,'pos',$base_prod_id);
						$config_url = $iden = "";
						if(in_array($pcode, array('533','737'))){
						    $cp_config = get_connectplus_config();
						    if( isset($this->request_arr['root_orderno']) && isset($this->request_arr['request_pcode']) && isset($this->request_arr['request_pskew'])){
						        $iden = $this->request_arr['root_orderno'].$this->request_arr['request_pcode'].$this->request_arr['request_pskew'];
						    }
						    $user_que = " select GPU_USER_ID,GPU_USER_NAME,GPU_PASSWORD from gft_pos_users where GPU_CONTACT_STATUS='A' ".
										    " and GPU_INSTALL_ID='$this->PRIMARY_GID_INSTALL_ID' and GPU_USER_NAME='admin' and GPU_CONTACT_TYPE=1 ";
						    $user_res = execute_my_query($user_que);
						    $config_params = "&custId=$this->GID_LEAD_CODE&skewCode=$pcode&identity=$iden";
						    if($user_row = mysqli_fetch_array($user_res)){
						        $config_params .= "&user_id=".$user_row['GPU_USER_ID']."&user_name=".$user_row['GPU_USER_NAME']."&password=".$user_row['GPU_PASSWORD'];
						    }
						    $config_url = $cp_config['integ_portal']."/login?purpose=configure".$config_params;
						}
						$response_array = get_addon_formatted_response('YES', $pcode, $qdata['prod_name'], $qty_val, $validity_date, $orderno_val,
                						    $qdata['GID_EXPIRE_FOR'],$qdata['GPL_LICENSE_TYPE_NAME'],$qdata['Edition'],$qdata['GPM_VALIDATIAN_TYPE'],$qdata['GPM_VALIDATIAN_METHOD'],
                						    $is_free_flag,(string)$qdata['GPG_PRODUCT_ALIAS'],$qdata['GPM_WEBSITE_LINK'],$addon_category,'A',$qdata['GPM_PRODUCT_SKEW'],
						                    $connect_token,$ns_token,'N',$grace_validity,$pilot_license,$license_agreement_stat,"",$custom_lic_arr,$config_url);
					}
					array_push($this->ADD_PRODUCT_DETAILS,$response_array);
				}
			}//end of while

			$this->get_ordered_employee();
			return true;
		}else{
			$this->GLC_ERROR_CODE='E010'; //Not Purchased
			return false;
		}

	}

	/**
	 * @return void
	 */
	public function get_ordered_employee() {
		$query =" select GOD_EMP_ID, GID_INSTALLED_EMP from gft_order_hdr ".
				" join gft_install_dtl_new  on (GID_ORDER_NO=GOD_ORDER_NO) ".
				" where GID_INSTALL_ID='$this->PRIMARY_GID_INSTALL_ID' ";
		$res_mail = execute_my_query($query);
		if($data_mail=mysqli_fetch_array($res_mail)){
			$this->ORDERED_EMP_ID = ( ((int)$data_mail['GID_INSTALLED_EMP']) > 0) ? $data_mail['GID_INSTALLED_EMP'] : $data_mail['GOD_EMP_ID'] ;
		}
	}

	/**
	 * @return void
	 */
	private function get_primary_order_install_id(){
		if(empty($this->ORDER_NO) or empty($this->PCODE) or empty($this->FULLFILLMENT_NO)){
			return ;
		}
		$query= " SELECT GID_INSTALL_ID,GID_LEAD_CODE,GID_SYS_HKEY FROM gft_install_dtl_new WHERE  GID_STATUS='A' ".
		        " and GID_ORDER_NO='$this->ORDER_NO' and GID_FULLFILLMENT_NO=$this->FULLFILLMENT_NO ".
		        " and GID_LIC_PCODE=$this->PCODE ";
		$result=execute_my_query($query);
		if(mysqli_num_rows($result)==1){
			$data=mysqli_fetch_array($result);
			$this->GID_LEAD_CODE=$this->LEAD_CODE=(int)$data['GID_LEAD_CODE'];
			$this->PRIMARY_GID_INSTALL_ID=(int)$data['GID_INSTALL_ID'];
			$this->GID_SYS_HKEY = $data['GID_SYS_HKEY'];
			return ;
		}else{
			$this->PRIMARY_GID_INSTALL_ID=0;
			return ;
		}
	}
	/**
	 * @param int $customer_id
	 * @param int $pcode
	 * @param string $pskew
	 *
	 * @return string
	 */

	private function get_query_addon_list($customer_id,$pcode=0,$pskew=''){
		$today=date('Y-m-d');

		if(empty($customer_id)) return '';

		$query_gop_ref_update=" select gop_product_code,count(*),sum(if((GOP_REFERENCE_ORDER_NO='' or isnull(gop_reference_order_no)),1,0)) empty_gop_refernce " .
				"from gft_order_hdr " .
				"join gft_order_product_dtl opd on (gop_order_no=god_order_no)
				join gft_product_family_master pfm on (gpm_product_code=gop_product_code and (GPM_IS_INTERNAL_PRODUCT=2 or gpm_category=6 ) and gpm_status='A')
				where gop_order_no=god_order_no and god_order_status='A' and god_lead_code = '$customer_id'
				group by gop_product_code
				having count(*)>1 and empty_gop_refernce>1 ";
		$result_gop_ref_update=execute_my_query($query_gop_ref_update);
		//print $query_gop_ref_update;
		if(mysqli_num_rows($result_gop_ref_update)>0){
			while($qd_list_pcode=mysqli_fetch_array($result_gop_ref_update)){
				$gop_product_code=$qd_list_pcode['gop_product_code'];
				$query_gop_ref_update2=" select god_order_date,gop_order_no,gop_product_code,gop_product_skew ".
						"from gft_order_hdr " .
						"join gft_order_product_dtl opd on (gop_order_no=god_order_no)
						join gft_product_family_master pfm on (gpm_product_code=gop_product_code and gpm_category=6)
						where gop_order_no=god_order_no and god_order_status='A' and god_lead_code= $customer_id
						and gop_product_code=$gop_product_code
						group by gop_order_no,gop_product_code,gop_product_skew
						order by god_order_date asc  ";
				$result_gop_ref_update2=execute_my_query($query_gop_ref_update2);
				//print $query_gop_ref_update2;

				$i=1;$gop_ref_order_no='';
				while($qdp=mysqli_fetch_array($result_gop_ref_update2)){
					if($i==1) { $gop_ref_order_no=$qdp['gop_order_no']; $i++; continue;}
					$update_q="update gft_order_product_dtl set gop_reference_order_no='$gop_ref_order_no' where gop_order_no='".$qdp['gop_order_no']."' and gop_product_code=".$qdp['gop_product_code']." ";
					execute_my_query($update_q);
					$i++;
				}
			}
		}

		$app_val_dt = " if(GPM_SUBSCRIPTION_PERIOD!=0,DATE_ADD('$today',INTERVAL GPM_SUBSCRIPTION_PERIOD DAY),DATE_ADD('$today',INTERVAL GPM_DEFAULT_ASS_PERIOD DAY)) ";
		$query= "select GLH_CUST_NAME,GLH_LEAD_CODE 'lead_code',GPM_LICENSE_TYPE," .
				" GOD_ORDER_NO ,GOD_ORDER_DATE 'order_date',god_order_splict,gpm_product_abr as 'prod_name',GPM_CATEGORY," .
				" gop_qty as 'qty',pfm.gpm_product_code 'pcode',pfm.GPM_LICENSE_GRACE_PERIOD_DAYS,pm.GPM_PRODUCT_SKEW ,gop_usedqty," .
				" em.gem_emp_name as 'order_closed_by',god_emp_id as 'ordered_eid',pfm.GPM_ADDON_CATEGORY,pm.GPM_PILOT_LICENSE, ".
				" GOP_FULLFILLMENT_NO,GOP_REF_SERIAL_NO,GID_NO_CLIENTS,sum(GOP_QTY * GPM_CLIENTS) 'GID_NO_CLIENTS_ADD'," .
				" (GOP_QTY * GPM_COMPANYS) 'no_companys',pfm.GPM_LAUNCHED_ON, " .
				" glh_lead_type as 'lead_type',gft_skew_property,GPG_PRODUCT_ALIAS," .
				" GPM_PRODUCT_SKEW,GPM_SUBSCRIPTION_PERIOD AS 'SUBSCRIPTION_PERIOD',GPM_GRACE_PERIOD 'GRACE_PERIOD',GPM_REFERER_SKEW,pm.gpm_skew_desc," .
				" GPM_DEFAULT_ASS_PERIOD AS ASS_PERIOD ,GPL_LICENSE_TYPE_NAME,GPT_TYPE_NAME as 'Edition',GPM_VALIDATIAN_METHOD,GPM_VALIDATIAN_TYPE, GPM_WEBSITE_LINK,GPM_NS_REGISTRATION_KEY, " .
				" ifnull(GID_EXPIRE_FOR,if(gft_skew_property=11,2,1)) as  GID_EXPIRE_FOR, ifnull(GID_CONNECTPLUS_TOKEN,'') GID_CONNECTPLUS_TOKEN, ifnull(GID_NS_TOKEN,'') GID_NS_TOKEN, ".
				" ifnull(GID_VALIDITY_DATE,if(GPM_FREE_TILL is not null and GPM_FREE_TILL>$app_val_dt,GPM_FREE_TILL,$app_val_dt)) as  GID_VALIDITY_DATE ,".
				" GLH_VERTICAL_CODE,god_order_type,pm.GPM_FREE_EDITION ,GID_INSTALL_ID,GID_NCLIENTS_UPDATED_IN_CUSTPLACE,GPM_HEAD_FAMILY,GID_STATUS,GID_SUBSCRIPTION_STATUS,GID_INSTALL_DATE " .
				" from gft_lead_hdr lh " .
				" join ( SELECT GOD_ORDER_NO, GOP_ORDER_NO, GOD_EMP_ID, GOD_ORDER_DATE, GOD_ORDER_AMT, god_order_splict, god_order_type, GOD_ORDER_STATUS, GOD_LEAD_CODE, ".
				"GOP_PRODUCT_CODE, GOP_PRODUCT_SKEW, GOP_QTY, gop_usedqty, GOP_FULLFILLMENT_NO, GOP_REF_SERIAL_NO, GOP_ADD_CLIENTS, GOP_ADD_COMPANYS ".
				" FROM gft_order_hdr ohdr ".
				" join gft_order_product_dtl opd on ( opd.GOP_ORDER_NO=ohdr.GOD_ORDER_NO and god_order_splict=0 ) ".
				" where GOD_LEAD_CODE='$customer_id' and god_order_status='A' ".
				" union all ".
				" SELECT GOD_ORDER_NO, GOP_ORDER_NO, GOD_EMP_ID, GOD_ORDER_DATE, GOD_ORDER_AMT, god_order_splict, god_order_type, GOD_ORDER_STATUS, cpod.GCO_CUST_CODE as GOD_LEAD_CODE, cpod.GCO_PRODUCT_CODE as GOP_PRODUCT_CODE, cpod.GCO_SKEW as GOP_PRODUCT_SKEW, cpod.GCO_CUST_QTY as GOP_QTY, ".
				" cpod.GCO_USEDQTY as gop_usedqty, cpod.GCO_FULLFILLMENT_NO as GOP_FULLFILLMENT_NO, cpod.GCO_REF_SERIAL_NO as GOP_REF_SERIAL_NO, cpod.GCO_ADD_CLIENTS as GOP_ADD_CLIENTS, cpod.GCO_ADD_COMPANYS as GOP_ADD_COMPANYS ".
				" FROM gft_order_hdr ohdr join gft_order_product_dtl opd on ( opd.GOP_ORDER_NO=ohdr.GOD_ORDER_NO ) ".
				" join gft_cp_order_dtl cpod on ( cpod.GCO_ORDER_NO=opd.GOP_ORDER_NO AND cpod.GCO_PRODUCT_CODE=opd.GOP_PRODUCT_CODE AND cpod.GCO_SKEW=opd.GOP_PRODUCT_SKEW ) ".
				" where GCO_CUST_CODE='$customer_id' and god_order_status='A' ) opd " .
				" on (GOD_LEAD_CODE=GLH_LEAD_CODE) " .
				" left join gft_install_dtl_new ins on (gid_lead_code=glh_lead_code and gid_lic_pcode=GOP_PRODUCT_CODE and GID_STATUS='A' ) ".
				" join gft_product_family_master pfm on (opd.gop_product_code=pfm.gpm_product_code and (GPM_IS_INTERNAL_PRODUCT=2 or gpm_category=6 ) and gpm_status='A') " .
				" join gft_product_master pm on (pm.gpm_product_code=opd.gop_product_code and pm.gpm_product_skew=if(GID_STATUS in ('A','S'),GID_LIC_PSKEW,opd.gop_product_skew)) " .
				" join gft_product_license_type plt on  (GPM_LICENSE_TYPE=GPL_LICENSE_TYPE_ID )".
				" join gft_emp_master em on (god_emp_id=gem_emp_id)" .
				" left join gft_product_type_master ptm on (GPT_TYPE_ID=GPM_PRODUCT_TYPE) ".
				" left join gft_product_group_master pg on (pg.gpg_product_family_code=pfm.GPM_HEAD_FAMILY and gpg_skew=substr(GID_LIC_PSKEW,1,4)) ";

		$query.=" where  gft_skew_property in (1,11,18) and glh_lead_code='$customer_id' ";
		$query.=" group by glh_lead_code,opd.gop_product_code having if(GID_STATUS is null,gop_usedqty=0,1) ";
		return $query;
			
	}

	/**
	 * @return void
	 */
	private function process_addon_request_list(){

		$this->WEB_REQUEST_TYPE=2;

		if(empty($_REQUEST['req_list'])) {
			$this->GLC_ERROR_CODE='E001';
			return;
		}


		$this->decrypt_addon_request((string)$_REQUEST['req_list']);
		if($this->GLC_ERROR_CODE!=''){
			return;
		}
		if($this->PRIMARY_GID_INSTALL_ID!=0 || $this->LOCAL_USER=='Y'){
			$this->return_addon_product_details();
			return;
		}
	}

	/**
	 * @return void
	 */
	private function process_request_activated_list(){

		$this->WEB_REQUEST_TYPE=3;

		if(empty($_REQUEST['req_act'])) {
			$this->GLC_ERROR_CODE='E001';
			return;
		}


		$this->decrypt_addon_request((string)$_REQUEST['req_act']);
		if($this->GLC_ERROR_CODE!=''){
			return;
		}

		if(empty($this->request_arr['activated'])){
			$this->GLC_ERROR_CODE='E013';
			return;
		}
		else{

			$product_dtl = /*. (string[string][string]) .*/ array();
			$iproduct_code='';
			$length_of_request=count($this->request_arr['activated']);
			$query=$this->get_query_addon_list($this->GID_LEAD_CODE);
			$result=execute_my_query($query);
			while($qdata=mysqli_fetch_array($result)){
				$iproduct_code=$qdata['pcode'];
				$product_dtl[$iproduct_code]['GID_EXPIRE_FOR']=$qdata['GID_EXPIRE_FOR'];
				$product_dtl[$iproduct_code]['GID_NO_CLIENTS']=strval((int)$qdata['GID_NO_CLIENTS']+(int)$qdata['GID_NO_CLIENTS_ADD']);
				$product_dtl[$iproduct_code]['GOD_ORDER_NO']=$qdata['GOD_ORDER_NO'];
				$product_dtl[$iproduct_code]['GOP_FULLFILLMENT_NO']=$qdata['GOP_FULLFILLMENT_NO'];
				$product_dtl[$iproduct_code]['validity_period']=$qdata['GID_VALIDITY_DATE'];
				$product_dtl[$iproduct_code]['expiry_type']=$qdata['GID_EXPIRE_FOR'];
				$product_dtl[$iproduct_code]['GPM_PRODUCT_SKEW']=$qdata['GPM_PRODUCT_SKEW'];
				$product_dtl[$iproduct_code]['GPM_REFERER_SKEW']=$qdata['GPM_REFERER_SKEW'];
				$product_dtl[$iproduct_code]['GPM_HEAD_FAMILY']=$qdata['GPM_HEAD_FAMILY'];
				$product_dtl[$iproduct_code]['GID_INSTALL_ID']=$qdata['GID_INSTALL_ID'];
				$product_dtl[$iproduct_code]['GID_SUBSCRIPTION_STATUS']=$qdata['GID_SUBSCRIPTION_STATUS'];
				$product_dtl[$iproduct_code]['GID_INSTALL_DATE']=$qdata['GID_INSTALL_DATE'];
				$product_dtl[$iproduct_code]['GOD_ORDER_DATE']=$qdata['order_date'];
				$product_dtl[$iproduct_code]['GLH_CUST_NAME']=$qdata['GLH_CUST_NAME'];
				$product_dtl[$iproduct_code]['prod_name']=$qdata['prod_name'];
				$product_dtl[$iproduct_code]['order_closed_by']=$qdata['order_closed_by'];
				$product_dtl[$iproduct_code]['gpm_skew_desc']=$qdata['gpm_skew_desc'];

			}

			//For validation
			//what type of validation ???, after knowing the purpose only need to enable it
			/*for($i=0;$i<$length_of_request;$i++){
			$pcode_str=(string)$this->request_arr['activated'][$i]['pcode'];
			if(!isset($product_dtl[$pcode_str]['GID_NO_CLIENTS'])){
			$this->GLC_ERROR_CODE='E018';
			$this->GLC_ADDITIONAL_ERROR_MESSAGE='Product code '.$pcode_str . ' is having problem';
			return;
			}
			}*/


			//print_r($product_dtl);
			for($i=0;$i<$length_of_request;$i++){
				$this->GID_ORDER_NO=substr((string)$this->request_arr['activated'][$i]['orderno'],0,15);
				$this->GID_FULLFILLMENT_NO=(int)substr((string)$this->request_arr['activated'][$i]['orderno'],-4);
				$this->GID_LIC_PCODE=$pcode=(string)$this->request_arr['activated'][$i]['pcode'];
				$this->GID_INSTALL_ID=(int)$product_dtl[(string)$this->GID_LIC_PCODE]['GID_INSTALL_ID'];
				$this->GID_SALESEXE_ID=$this->GID_EMP_ID=SALES_DUMMY_ID;
				$this->GID_REF_SERIAL_NO=1;
				$status=$this->request_arr['activated'][$i]['status'];
				$this->HEAD_OF_FAMILY=(int)$product_dtl[$pcode]['GPM_HEAD_FAMILY'];
				$this->GID_EXPIRE_FOR=(int)$product_dtl[$iproduct_code]['GID_EXPIRE_FOR'];
				$this->GID_VALIDITY_DATE=$product_dtl[$pcode]['validity_period'];
				$this->GID_NO_CLIENTS=(int)$product_dtl[$pcode]['GID_NO_CLIENTS'];
				$this->GID_SUBSCRIPTION_STATUS=$product_dtl[$pcode]['GID_SUBSCRIPTION_STATUS'];
				$this->GID_INSTALL_DATE=($this->GID_INSTALL_ID!=0?$product_dtl[(string)$this->GID_LIC_PCODE]['GID_INSTALL_DATE']:date('Y-m-d'));
				$this->GID_LIC_PSKEW=$product_dtl[$pcode]['GPM_PRODUCT_SKEW'];
				$this->GOD_ORDER_DATE=$product_dtl[$pcode]['GOD_ORDER_DATE'];
				$this->GLH_CUST_NAME=$product_dtl[$pcode]['GLH_CUST_NAME'];
				$this->PRODUCT_NAME=$product_dtl[$pcode]['prod_name'];
				$this->GOD_ORDERED_BY=$product_dtl[$pcode]['order_closed_by'];
				$this->SKEW_DESC=$product_dtl[$pcode]['gpm_skew_desc'];

				$this->ORDER_NO_SPLITED=substr($this->GID_ORDER_NO,0,5);
				$this->ORDER_NO_SPLITED.="-".substr($this->GID_ORDER_NO,5,5);
				$this->ORDER_NO_SPLITED.="-".substr($this->GID_ORDER_NO,10,5);
				$this->ORDER_NO_SPLITED.="-".substr("0000".$this->GID_FULLFILLMENT_NO,-4);

				if($product_dtl[$pcode]['GID_INSTALL_ID']==''){
					array_push($this->ACTIVATED_LIST,array("pcode"=>$this->GID_LIC_PCODE));

				}else if($product_dtl[$pcode]['GID_INSTALL_ID']!=''){
					$this->GID_INSTALL_DATE=$product_dtl[(string)$this->GID_LIC_PCODE]['GID_INSTALL_DATE'];
					if($this->GID_SUBSCRIPTION_STATUS === 'N'){
						array_push($this->RENEWED_LIST,array("pcode"=>$this->GID_LIC_PCODE));
					}
					else {
						array_push($this->ALREADY_ACTIVATED,array("pcode"=>$this->GID_LIC_PCODE));
					}
				}
				$this->update_license_dtl();
				$this->update_used_qty_mobil_app((int)$this->GID_LIC_PCODE,$this->GID_LEAD_CODE);
			}/* end of for loop*/
		}

	}


	/**
	 * @param string $err_code
	 *
	 * @return void
	 */
	private function add_on_license_err_message($err_code){
		switch($err_code){
			case 'E000': $this->GLC_ERROR_MESSAGE='Empty Request'; break;
			case 'E001': $this->GLC_ERROR_MESSAGE='Empty Param'; break;
			case 'E002': $this->GLC_ERROR_MESSAGE='Root Order Number Empty'; break;
			case 'E003': $this->GLC_ERROR_MESSAGE='Request Product code Empty'; break;
			case 'E004': $this->GLC_ERROR_MESSAGE='Order Number length Mismatch'; break;
			case 'E005': $this->GLC_ERROR_MESSAGE='Customer Id Empty'; break;
			case 'E006': $this->GLC_ERROR_MESSAGE='Customer Id Not Available in SAM'; break;
			case 'E007': $this->GLC_ERROR_MESSAGE='Order Number Not belongs to this Customer Id'; break;
			case 'E008': $this->GLC_ERROR_MESSAGE='Product Code Wrong '; break;
			case 'E009': $this->GLC_ERROR_MESSAGE='Base Product installation details missing'; break;
			case 'E010': $this->GLC_ERROR_MESSAGE='Not purchased Addon '; break;
			case 'E011': $this->GLC_ERROR_MESSAGE='Harddisk id is empty '; break;
			case 'E012': $this->GLC_ERROR_MESSAGE='Empty Request After Decode '; break;
			case 'E013': $this->GLC_ERROR_MESSAGE='Activated list is Empty ';break;
			case 'E014': $this->GLC_ERROR_MESSAGE='Free Order Creation Product Code is Empty ';break;
			case 'E015': $this->GLC_ERROR_MESSAGE='Free Order Not Splitted ';break;
			case 'E016': $this->GLC_ERROR_MESSAGE='Trial Order Creation Product Code is Empty ';break;
			case 'E017': $this->GLC_ERROR_MESSAGE='Trial Order Not Splitted ';break;
			case 'E018': $this->GLC_ERROR_MESSAGE='Addon Product code not found in order details ';break;
			case 'E019': $this->GLC_ERROR_MESSAGE='Product skew not found in request details';break;
				
			case 'P001': $this->GLC_ERROR_MESSAGE='Free Order Product Skew not available in SAM ';break;
			case 'P002': $this->GLC_ERROR_MESSAGE='More than one Free Order Product available in SAM ';break;

			case 'TF01': $this->GLC_ERROR_MESSAGE='More than one license activated for this product '; break;
			case 'TF02': $this->GLC_ERROR_MESSAGE='Issue in generating order number'; break;
			case 'TF03': $this->GLC_ERROR_MESSAGE='More than one order of same product in active'; break;
				
			case 'M001': $this->GLC_ERROR_MESSAGE='Order Number Length Mismatch';break;
			case 'M002': $this->GLC_ERROR_MESSAGE='Incorrect OTP';break;
			case 'M003': $this->GLC_ERROR_MESSAGE='Order No Not Available in CRM';break;
			case 'M004': $this->GLC_ERROR_MESSAGE="Harddisk id mismatch. Kindly do License update to proceed further";break;
				
			default:
				$this->GLC_ERROR_MESSAGE='Unexpected error ['.$err_code.']';
				break;
					
		}

	}

	/**
	 * @param string $request_body
	 *
	 * @return string[string]
	 */
	public function process_plugin_addon($request_body){
		$ret_arr = /*. (string[string]) .*/array();
		$ret_arr['status'] = "failure";
		$this->request_arr = json_decode($request_body,true);
		$req_purpose 	= isset($this->request_arr['purpose'])?$this->request_arr['purpose']:'';
		$order_full_no 	= isset($this->request_arr['order_no'])?$this->request_arr['order_no']:'';
		$pcode			= isset($this->request_arr['product_code'])?$this->request_arr['product_code']:'';
		$to_contact		= isset($this->request_arr['to_contact'])?$this->request_arr['to_contact']:'';
		$hkey			= isset($this->request_arr['hkey'])?$this->request_arr['hkey']:'';
		$order_full_no	= str_replace("-", "", $order_full_no);
		if(strlen($order_full_no)!=19){
			$ret_arr['error_code'] = 'M001';
			$ret_arr['message'] = 'Order Number Length Mismatch';
			return $ret_arr;
		}
		$order_no = substr($order_full_no, 0,15);
		$fullfill_no = substr($order_full_no, 15, 4);
		if($req_purpose=='send_otp_to_contact'){
			$query1=" select GID_LEAD_CODE,GID_LIC_PCODE,GID_LIC_PSKEW from gft_install_dtl_new ".
					" join gft_product_family_master on (GID_LIC_PCODE=GPM_PRODUCT_CODE) ".
					" where GID_ORDER_NO='$order_no' and GID_FULLFILLMENT_NO=$fullfill_no and GPM_HEAD_FAMILY='$pcode' ";
			$res1 = execute_my_query($query1);
			$lead_code = $lic_pcode = $lic_pskew = '';
			if($qdata = mysqli_fetch_array($res1)){
				$this->LEAD_CODE = $lead_code	= $qdata['GID_LEAD_CODE'];
				$lic_pcode = $qdata['GID_LIC_PCODE'];
				$lic_pskew = $qdata['GID_LIC_PSKEW'];
			}
			if($lead_code==''){
				$ret_arr['status'] 		= 'failure';
				$ret_arr['message'] 	= 'Customer Id Not Available in CRM';
				return $ret_arr;
			}
			$new_otp = generate_OTP(5);
			$db_mail_content_config=/*. (string[string][int]) .*/array(
					"OTP"=>array($new_otp),
					"Customer_Id"=>array($lead_code),
					"Customer_ID"=>array($lead_code)
			);
			$update_query = " update gft_otp_master set GOM_OTP_STATUS='I' where GOM_ORDER_NO='$order_no' ".
					" and GOM_FULLFILLMENT_NO=$fullfill_no and GOM_HKEY='$hkey' ";
			execute_my_query($update_query);
			$insert_arr['GOM_LEAD_CODE'] 		= $lead_code;
			$insert_arr['GOM_ORDER_NO'] 		= $order_no;
			$insert_arr['GOM_FULLFILLMENT_NO'] 	= $fullfill_no;
			$insert_arr['GOM_OTP'] 				= $new_otp;
			$insert_arr['GOM_OTP_STATUS'] 		= 'A';
			$insert_arr['GOM_HKEY'] 			= $hkey;
			$insert_arr['GOM_PCODE']			= $lic_pcode;
			$insert_arr['GOM_PSKEW']			= $lic_pskew;
			$insert_arr['GOM_GEN_DATE_TIME']	= date('Y-m-d H:i:s');
			if(!strpos($to_contact,"@")){
				$insert_arr['GOM_OTP_SMS_TO']	=	$to_contact;
				$sms_content = htmlentities(get_formatted_content($db_mail_content_config,143));
				entry_sending_sms($to_contact, $sms_content, 143);
			}else{
				$insert_arr['GOM_OTP_MAIL_TO']	=	$to_contact;
				send_formatted_mail_content($db_mail_content_config,85,155,null,null,array($to_contact));
			}
			array_update_tables_common($insert_arr, "gft_otp_master", null,null, SALES_DUMMY_ID,null,null,$insert_arr);
			$ret_arr['status'] = 'success';
			$ret_arr['message'] = 'OTP has been generated to the selected contact. Kindly enter the same.';
			return $ret_arr;
		}else if($req_purpose=='validate_otp'){
			$otp = $this->request_arr['otp'];
			$check_quey=" select GOM_LEAD_CODE,GOM_PCODE,GOM_PSKEW from gft_otp_master where GOM_ORDER_NO='$order_no' and GOM_FULLFILLMENT_NO=$fullfill_no ".
					" and GOM_OTP='$otp' and GOM_OTP_STATUS='A'  ";
			$check_res = execute_my_query($check_quey);
			if(mysqli_num_rows($check_res)==1){
				$row_data = mysqli_fetch_array($check_res);
				$update_otp = execute_my_query("update gft_otp_master set GOM_OTP_STATUS='I' where GOM_ORDER_NO='$order_no' and GOM_FULLFILLMENT_NO=$fullfill_no and GOM_OTP='$otp' ");
				$ret_arr['status'] = 'success';
				$surrender = isset($this->request_arr['surrender'])?$this->request_arr['surrender']:'';
				if($surrender=='true'){
					$gid_lic_pcode = $row_data['GOM_PCODE'];
					$gid_lic_pskew = $row_data['GOM_PSKEW'];
					$gid_lead_code = $row_data['GOM_LEAD_CODE'];
					$sel_que=" select GAR_ID from gft_approved_reinstallation_dtl where GAR_ORDER_NO='$order_no' and GAR_FULLFILLMENT_NO=$fullfill_no ".
							" and GAR_PRODUCT_CODE='$gid_lic_pcode' and GAR_PRODUCT_SKEW='$gid_lic_pskew' and GAR_STATUS='P' ";
					$num_rows = mysqli_num_rows(execute_my_query($sel_que));
					if($num_rows == 0){
						$install_id_arr = get_install_id($gid_lead_code,'',$gid_lic_pcode,$gid_lic_pskew,'');
						$install_id = isset($install_id_arr[0])?$install_id_arr[0]:0;
						if($install_id > 0){
							$sql_reinstall_update	=	" INSERT INTO gft_approved_reinstallation(GAR_REQUEST_BY, GAR_LEAD_CODE, GAR_REASON, GAR_DATE_OF_REQUEST, GAR_APPROVED_BY, GAR_APPROVED_DATE, GAR_APPROVED_FLAG, GAR_APPROVAL_COMMENT) ".
									" VALUES(9999, $gid_lead_code, 5, NOW(),9999, NOW(), 'Y', 'Surrender License')";
							execute_my_query($sql_reinstall_update);
							$lastiinsertid	=	mysqli_insert_id_wrapper();
							$sql_reinstall_dtl		=	" INSERT INTO  gft_approved_reinstallation_dtl(GAR_ID, GAR_ORDER_NO, GAR_PRODUCT_CODE, GAR_PRODUCT_SKEW, GAR_FULLFILLMENT_NO, GAR_STATUS, GAR_INSTALL_ID_REFF) ".
									" VALUES($lastiinsertid, '$order_no', $gid_lic_pcode , '$gid_lic_pskew', $fullfill_no ,'P', $install_id) ";
							execute_my_query($sql_reinstall_dtl);
						}
					}
				}
			}else{
			    if($otp=='453452'){ //hardcoding this value for Ordereasy in integration portal till the otp issue challenge is addressed as per the request from senthil
			        $ret_arr['status'] = 'success';
			    }else{
			        $ret_arr['error_code'] = 'M002';
			        $ret_arr['message'] = 'Incorrect OTP';
			    }
			}
			return $ret_arr;
		}elseif ($req_purpose=='regenerate_otp'){
			$sel_que =  " select ifnull(GCO_CUST_CODE,GOD_LEAD_CODE) lead_code,GOP_ORDER_NO ".
			 			" from gft_order_product_dtl ".
			 			" left join gft_cp_order_dtl on (GCO_ORDER_NO=GOP_ORDER_NO and GCO_PRODUCT_CODE=GOP_PRODUCT_CODE and GCO_SKEW=GOP_PRODUCT_SKEW and GCO_FULLFILLMENT_NO=$fullfill_no) ".
					    " join gft_order_hdr on (GOP_ORDER_NO=GOD_ORDER_NO) where GOD_ORDER_NO='$order_no' ";
			$sel_res = execute_my_query($sel_que);
			if(mysqli_num_rows($sel_res)==0){
				$ret_arr['error_code'] = 'M003';
				$ret_arr['message'] = 'Order No not available in CRM';
				return $ret_arr;
			}
			$row1 = mysqli_fetch_array($sel_res);
			$this->LEAD_CODE = $lead_code = $row1['lead_code'];
			$new_otp = generate_OTP(5);
			$update_otp = execute_my_query("update gft_otp_master set GOM_OTP_STATUS='I' where GOM_ORDER_NO='$order_no' and GOM_FULLFILLMENT_NO='$fullfill_no'");
			$insert_que =" insert into gft_otp_master (GOM_ORDER_NO,GOM_FULLFILLMENT_NO,GOM_OTP,GOM_OTP_STATUS) ".
					" values ('$order_no','$fullfill_no','$new_otp','A') ";
			execute_my_query($insert_que);
			$db_mail_content_config=/*. (string[string][int]) .*/array(
					"OTP"=>array($new_otp),
					"Customer_Id"=>array($this->LEAD_CODE)
			);
			send_formatted_mail_content($db_mail_content_config,85,155,$employee_ids=null,array($lead_code));
			$ret_arr['status'] = 'success';
			$ret_arr['message'] = 'OTP Regenerated and Sent to Registered Mail';
			return $ret_arr;
		}
		return $ret_arr;
	}

	/**
	 * @return string[int]
	 */
	public function print_addon_response(){
		$response_msg=array();
		$this->GLC_STATUS='S';
		if($this->GLC_ERROR_CODE!=''){
			if($this->GLC_ERROR_CODE=='E010' and !$this->IS_BACKWARD_COMPATIBLE){
				//For new process.
				$this->get_addon_products();
				$response_msg['product_details']=$this->ADD_PRODUCT_DETAILS;
			}else{
				$this->add_on_license_err_message($this->GLC_ERROR_CODE);
				$response_msg['error_code']=$this->GLC_ERROR_CODE;
				$response_msg['error_message']=$this->GLC_ERROR_MESSAGE;
				$this->GLC_STATUS='F';
			}
		}else if(!empty($this->ADD_PRODUCT_DETAILS)){
			if(!$this->IS_BACKWARD_COMPATIBLE){
				$this->get_addon_products();
				$this->update_install_information();  //as the service req_act wont be send, we are updating in this sservice itself.
			}
			$response_msg['product_details']=$this->ADD_PRODUCT_DETAILS;
		}else if($this->WEB_REQUEST_TYPE==3){
			if(!empty($this->ACTIVATED_LIST)){
				$response_msg['activated']=$this->ACTIVATED_LIST;
			}
			if(!empty($this->RENEWED_LIST)){
				$response_msg['Subscription Renewed']=$this->RENEWED_LIST;
			}
			if(!empty($this->ALREADY_ACTIVATED)){
				$response_msg['Already Activated']=$this->ALREADY_ACTIVATED;
			}
		}else if(!$this->IS_BACKWARD_COMPATIBLE){
			$this->get_addon_products();
			$response_msg['product_details']=$this->ADD_PRODUCT_DETAILS;
		}
		$this->IP_ADDRESS=$_SERVER['REMOTE_ADDR'];
		$this->GLC_RETURN_DATA=json_encode($response_msg);
		//print $this->GLC_RETURN_DATA;


		global $secret;
		$response_json = "";
		if ($this->IS_BACKWARD_COMPATIBLE){
			$response_msg['customer_id']="".$this->LEAD_CODE;
			$response_msg_json=json_encode($response_msg);
			//print $response_msg_json;
			$response_json = $response_msg_json;
		}else{
			//print $this->GLC_RETURN_ENCRYPTED_DATA=lic_encrypt($this->GLC_RETURN_DATA,$secret);
			$this->GLC_RETURN_ENCRYPTED_DATA=lic_encrypt($this->GLC_RETURN_DATA,$secret);
			$response_json = $this->GLC_RETURN_ENCRYPTED_DATA;
		}
		/* $insert_lic_request=array();
		 $insert_lic_request['GLC_REQUEST_ID']='';
		 $insert_lic_request['GLC_FROM_ONLINE']='Y';
		 $insert_lic_request['GLC_FROM_WEB']='Y';
		 $insert_lic_request['GLC_ONLINE_CONTENT']=(!empty($this->GLC_ONLINE_CONTENT)?$this->GLC_ONLINE_CONTENT:'');
		 $insert_lic_request['GLC_REQUEST_TIME']=date('Y-m-d H:i:s');
		 $insert_lic_request['GLC_RETURN_DATA']=$this->GLC_RETURN_DATA;
		 $insert_lic_request['GLC_RETURN_ENCRYPTED_DATA']=$this->GLC_RETURN_ENCRYPTED_DATA;
		 $insert_lic_request['GLC_LEAD_CODE']=(!empty($this->LEAD_CODE)?$this->LEAD_CODE:0);
		 $insert_lic_request['GLC_IP_ADDRESS	']=$this->IP_ADDRESS;
		 $insert_lic_request['GLC_HDD_KEY']=(!empty($this->request_arr['hard_disk_id'])?(string)$this->request_arr['hard_disk_id']:'');
		 $insert_lic_request['GLC_EMP_ID']=(!empty($this->USERID)?$this->USERID:'');
		 $insert_lic_request['GLC_REQUEST_PURPOSE_ID']=$this->WEB_REQUEST_TYPE;
		 $insert_lic_request['GLC_PROCESSING_TIME']=getDeltaTime();
		 $insert_lic_request['GLC_DECRYPTED_CONTENT']=$this->GLC_DECRYPTED_CONTENT;
		 $insert_lic_request['GLC_STATUS']=$this->GLC_STATUS;
		 $insert_lic_request['GLC_ERROR_CODE']=(!empty($this->GLC_ERROR_CODE)?$this->GLC_ERROR_CODE:'');
		 $insert_lic_request['GLC_ERROR_MESSAGE']=(!empty($this->GLC_ERROR_MESSAGE)?$this->GLC_ERROR_MESSAGE:'');
		 //array_update_tables_common($insert_lic_request,'gft_lic_request',null,null,SALES_DUMMY_ID,null,null,$insert_lic_request);
		 //$this->ONLINE_REQUEST_ID=mysqli_insert_id_wrapper();
		 */
		log_request($this->GLC_ONLINE_CONTENT, $this->GLC_RETURN_DATA, $this->GLC_RETURN_ENCRYPTED_DATA, $this->LEAD_CODE, $this->WEB_REQUEST_TYPE,
				$this->GLC_ERROR_CODE, $this->GLC_ERROR_MESSAGE,$this->GLC_DECRYPTED_CONTENT);
		$response_details[0]=$response_json;
		$response_details[1]=$this->GLC_STATUS;
		return $response_details;

	}

	/**
	 * @param string $request_body
	 *
	 * @return string[string]
	 */
	public function activate_addon($request_body){
		$ret_arr = /*. (string[string]) .*/array();
		$ret_arr['status'] = "failure";
		$this->request_arr = json_decode($request_body,true);
		$idendity 	= isset($this->request_arr['idendity'])?$this->request_arr['idendity']:'';
		$eidendity 	= isset($this->request_arr['eidendity'])?$this->request_arr['eidendity']:'';
		$pcode		= isset($this->request_arr['pcode'])?$this->request_arr['pcode']:'';
		if(strlen($idendity)!=27){
			$ret_arr['message'] = "Idendity Length Mismatch";
			return $ret_arr;
		}elseif (strcasecmp($eidendity, md5($idendity))!=0){
			$ret_arr['message'] = "Idendity and Eidendity Mismatch";
			return $ret_arr;
		}
		$dtl_arr = get_details_from_idendity($idendity);
		$this->LEAD_CODE = isset($dtl_arr[0])?$dtl_arr[0]:'';
		if($this->LEAD_CODE==''){
			$ret_arr['message'] = "Not able to identify the Customer. Invalid Idendity";
			return $ret_arr;
		}
		if(!in_array($pcode, array('703'))){
			$ret_arr['status'] = "success";
			$ret_arr['message'] = "Not supported for this product code ($pcode) ";
			return $ret_arr;
		}
		$check_exists = execute_my_query("select GID_INSTALL_ID from gft_install_dtl_new where GID_LEAD_CODE='$this->LEAD_CODE' and GID_PRODUCT_CODE='$pcode' and GID_STATUS='A'");
		if(mysqli_num_rows($check_exists) > 0){
			$ret_arr['status'] = "success";
			return $ret_arr;
		}
		$sql_que = " select GPM_PRODUCT_SKEW from gft_product_master ".
				" where GPM_PRODUCT_CODE='$pcode' and gft_skew_property in (1,11) and GPM_STATUS='A' limit 1 ";
		$sql_res = execute_my_query($sql_que);
		if(mysqli_num_rows($sql_res)==0){
			$ret_arr['message'] = "Skew not available for the Product Code($pcode) ";
			return $ret_arr;
		}
		$row1 = mysqli_fetch_array($sql_res);
		$skew = $row1['GPM_PRODUCT_SKEW'];
		$this->PCODE=$pcode;
		$this->REFERENCE_ORDER = $dtl_arr[2];
		$this->REFERENCE_FULLFILLMENT = $dtl_arr[3];
		$this->GID_LEAD_CODE	= $this->LEAD_CODE;
		$this->generate_order_no_on_request($skew);
		$this->update_install_information($pcode);
		$ret_arr['status'] = "success";
		return $ret_arr;
	}

	/**
	 * @param string $req_type
	 *
	 * @return void
	 */
	public function process_request_addon($req_type){
		try{
			$request_body = file_get_contents('php://input');
		}catch(Exception $e){
			die("Exception in: ".$e);
		}
		$this->WEB_REQUEST_TYPE = 26;
		if($req_type=='plugin'){
			$resp = $this->process_plugin_addon($request_body); //magento
		}else if($req_type=='activate_addon'){
			$this->WEB_REQUEST_TYPE = 32;
			$resp = $this->activate_addon($request_body);
		}else{
			$resp['status'] = "failure";
			$resp['message'] = "Invalid Request";
		}
		$json_resp = json_encode($resp);
		log_request($request_body, $json_resp, '', $this->LEAD_CODE, $this->WEB_REQUEST_TYPE);
		echo $json_resp;
		exit;
	}
	/**
	 * @return void
	 */
	function __construct(){
		$this->GLC_ONLINE_CONTENT=json_encode($_REQUEST);
		if(isset($_REQUEST['req_list'])){
			$this->process_addon_request_list();
		}else if(isset($_REQUEST['req_act'])){
			$this->process_request_activated_list();
		}else if(isset($_REQUEST['request_trial_addon'])){
			$this->process_trial_addon_request();
		}else if(isset($_REQUEST['request_free_addon'])){
			$this->process_free_addon_request();
		}else if(isset($_REQUEST['req'])){
			$req = $_REQUEST['req'];
			$this->process_request_addon($req);
		}else{
			$this->GLC_ERROR_CODE='E000';
		}
	}

	/**
	 * @param string $pcode
	 * 
	 * @return void
	 */
	public function update_install_information($pcode=''){
		if($this->LOCAL_USER=='Y'){
			return;
		}
		$product_dtl = /*. (string[string][string]) .*/ array();
		$query=$this->get_query_addon_list($this->GID_LEAD_CODE);
		$result=execute_my_query($query);
		while($qdata=mysqli_fetch_array($result)){
			$iproduct_code=$qdata['pcode'];
			$user_cnt = 0;
			$validity_date = $qdata['GID_VALIDITY_DATE'];
			if($this->WEB_REQUEST_TYPE==32){
			    if( ($pcode!='') && ($iproduct_code!=$pcode) ){
			        continue; //installation entry only for the activated product
			    }
				$root_dtl = get_details_from_idendity($this->request_arr['idendity']);
				$root_install_id = $root_dtl[1];
				$root_pcode = $root_dtl[4];
				$root_pskew = $root_dtl[5];
				$pcode_skew_group = $root_pcode."-".substr($root_pskew, 0,4);
				$sql_quer = " select GAP_ADDON_PRODUCT_CODE,GAP_USER_COUNT from gft_addon_product_map ".
						" where GAP_ADDON_PRODUCT_CODE='$iproduct_code' and GAP_PRODUCT_CODE='$pcode_skew_group' and GAP_USER_COUNT > 0 ";
				if($row1 = mysqli_fetch_array(execute_my_query($sql_quer))){
					$user_cnt = (int)$row1['GAP_USER_COUNT'];
					$validity_date = get_single_value_from_single_table("GID_VALIDITY_DATE", "gft_install_dtl_new","GID_INSTALL_ID", $root_install_id);
				}
			}
			//	if($iproduct_code==$this->PCODE){
			$product_dtl[$iproduct_code]['GID_EXPIRE_FOR']=$qdata['GID_EXPIRE_FOR'];
			$product_dtl[$iproduct_code]['GID_NO_CLIENTS']=strval((int)$qdata['GID_NO_CLIENTS']+(int)$qdata['GID_NO_CLIENTS_ADD']-$user_cnt);
			$product_dtl[$iproduct_code]['GOD_ORDER_NO']=$qdata['GOD_ORDER_NO'];
			$product_dtl[$iproduct_code]['GOP_FULLFILLMENT_NO']=$qdata['GOP_FULLFILLMENT_NO'];
			$product_dtl[$iproduct_code]['validity_period']=$validity_date;
			$product_dtl[$iproduct_code]['expiry_type']=$qdata['GID_EXPIRE_FOR'];
			$product_dtl[$iproduct_code]['GPM_PRODUCT_SKEW']=$qdata['GPM_PRODUCT_SKEW'];
			$product_dtl[$iproduct_code]['GPM_REFERER_SKEW']=$qdata['GPM_REFERER_SKEW'];
			$product_dtl[$iproduct_code]['GPM_HEAD_FAMILY']=$qdata['GPM_HEAD_FAMILY'];
			$product_dtl[$iproduct_code]['GID_INSTALL_ID']=$qdata['GID_INSTALL_ID'];
			$product_dtl[$iproduct_code]['GID_NCLIENTS_UPDATED_IN_CUSTPLACE']=$qdata['GID_NCLIENTS_UPDATED_IN_CUSTPLACE'];
			$product_dtl[$iproduct_code]['GID_SUBSCRIPTION_STATUS']=$qdata['GID_SUBSCRIPTION_STATUS'];
			$product_dtl[$iproduct_code]['GID_INSTALL_DATE']=$qdata['GID_INSTALL_DATE'];
			$product_dtl[$iproduct_code]['GOD_ORDER_DATE']=$qdata['order_date'];
			$product_dtl[$iproduct_code]['GLH_CUST_NAME']=$qdata['GLH_CUST_NAME'];
			$product_dtl[$iproduct_code]['prod_name']=$qdata['prod_name'];
			$product_dtl[$iproduct_code]['order_closed_by']=$qdata['order_closed_by'];
			$product_dtl[$iproduct_code]['gpm_skew_desc']=$qdata['gpm_skew_desc'];
			$product_dtl[$iproduct_code]['GOP_REF_SERIAL_NO']=$qdata['GOP_REF_SERIAL_NO'];

			$this->GID_ORDER_NO=$product_dtl[$iproduct_code]['GOD_ORDER_NO'];
			$this->GID_FULLFILLMENT_NO=$product_dtl[$iproduct_code]['GOP_FULLFILLMENT_NO'];
			$this->GID_LIC_PCODE=$pcode=$qdata['pcode'];
			$this->GID_INSTALL_ID=(int)$product_dtl[(string)$this->GID_LIC_PCODE]['GID_INSTALL_ID'];
			$this->GID_SALESEXE_ID=$this->GID_EMP_ID=SALES_DUMMY_ID;
			$this->GID_REF_SERIAL_NO=(int)$product_dtl[$pcode]['GOP_REF_SERIAL_NO'];
			//		$status=$this->request_arr['activated'][$i]['status'];
			$this->HEAD_OF_FAMILY=(int)$product_dtl[$pcode]['GPM_HEAD_FAMILY'];
			$this->GID_EXPIRE_FOR=(int)$product_dtl[$iproduct_code]['GID_EXPIRE_FOR'];
			$this->GID_VALIDITY_DATE=$product_dtl[$pcode]['validity_period'];
			$this->GID_NO_CLIENTS=(int)$product_dtl[$pcode]['GID_NO_CLIENTS'];
			$this->GID_NCLIENTS_UPDATED_IN_CUSTPLACE = (int)$product_dtl[$pcode]['GID_NCLIENTS_UPDATED_IN_CUSTPLACE'];
			$this->GID_SUBSCRIPTION_STATUS=$product_dtl[$pcode]['GID_SUBSCRIPTION_STATUS'];
			$this->GID_INSTALL_DATE=($this->GID_INSTALL_ID!=0?$product_dtl[(string)$this->GID_LIC_PCODE]['GID_INSTALL_DATE']:date('Y-m-d'));
			$this->GID_LIC_PSKEW=$product_dtl[$pcode]['GPM_PRODUCT_SKEW'];
			$this->GOD_ORDER_DATE=$product_dtl[$pcode]['GOD_ORDER_DATE'];
			$this->GLH_CUST_NAME=$product_dtl[$pcode]['GLH_CUST_NAME'];
			$this->PRODUCT_NAME=$product_dtl[$pcode]['prod_name'];
			$this->GOD_ORDERED_BY=$product_dtl[$pcode]['order_closed_by'];
			$this->SKEW_DESC=$product_dtl[$pcode]['gpm_skew_desc'];

			$this->ORDER_NO_SPLITED=substr($this->GID_ORDER_NO,0,5);
			$this->ORDER_NO_SPLITED.="-".substr($this->GID_ORDER_NO,5,5);
			$this->ORDER_NO_SPLITED.="-".substr($this->GID_ORDER_NO,10,5);
			$this->ORDER_NO_SPLITED.="-".substr("0000".$this->GID_FULLFILLMENT_NO,-4);

			if($product_dtl[$pcode]['GID_INSTALL_ID']==''){
				array_push($this->ACTIVATED_LIST,array("pcode"=>$this->GID_LIC_PCODE));

			}else if($product_dtl[$pcode]['GID_INSTALL_ID']!=''){
				$this->GID_INSTALL_DATE=$product_dtl[(string)$this->GID_LIC_PCODE]['GID_INSTALL_DATE'];
				if($this->GID_SUBSCRIPTION_STATUS === 'N'){
					array_push($this->RENEWED_LIST,array("pcode"=>$this->GID_LIC_PCODE));
				}
				else {
					array_push($this->ALREADY_ACTIVATED,array("pcode"=>$this->GID_LIC_PCODE));
				}
			}
			$this->update_license_dtl();
			$this->update_used_qty_mobil_app((int)$this->GID_LIC_PCODE,$this->GID_LEAD_CODE);
		}
		//}
	}

	/**
	 * @return void
	 */
	public function check_employee_license(){

		$query= " select GEM_EMP_ID,GEM_EMP_NAME,GEM_EMAIL from gft_emp_master  where gem_status='A' ".
				" and concat(substr(gem_mobile,-10),substr(concat('00000',gem_emp_id),-5))='".$this->ORDER_NO."' ";

		$result=execute_my_query($query);
		if($qd=mysqli_fetch_array($result)){
			$this->LOCAL_USER = 'Y';
		}
		if(is_dft_order($this->ORDER_NO, $this->FULLFILLMENT_NO)){
			$this->LOCAL_USER = "Y";
		}

	}

}// end of class

?>
