<?php
/*. require_module 'libxml'; .*/
require_once(__DIR__.'/lic_util.php');
require_once(__DIR__.'/connectplus/connectplus_util.php');
require_once(__DIR__.'/lib_ods/SimpleXMLToArray.php');
require_once(__DIR__.'/lib_ods/ArrayToXML.php');
addAccessControlAllowOrigin();

class postToSAM{
	
	private /*.string.*/ $idendity="";
	private /*.string.*/ $eidendity="";
	private /*.string.*/ $gid_install_id="";
	private /*.string.*/ $trans_id="";
	private /*.string.*/ $cust_id="";
	private /*.string.*/ $pgroup="";
	private /*.string.*/ $version="";
	private /*.string.*/ $issue_datetime="";
	private /*.string.*/ $priority="";
	private /*.string.*/ $severity="";
	private /*.string.*/ $description="";
	private /*.string.*/ $full_description="";
	private /*.string.*/ $support_status="";
	private /*.string.*/ $request_type="";
	private /*.string.*/ $request_body="";
	private /*.string[string].*/ $parsed_xml=/*. (string[string]) .*/array();
	
	private /*.string.*/ $new_complaint = "";
	private /*.string.*/ $current_version = "";
	private /*.string.*/ $updated_version = "";
	private /*.string.*/ $run_datetime = "";
	private /*.string.*/ $patch_status = "";
	private /*.string.*/ $db_schema_check = "";
	private /*.string.*/ $db_schema_changes = "";
	private /*.string.*/ $remarks = "";
	private /*.int.*/ $installtype = 0;
	private /*.string.*/ $complaint_code="";
	private /*.string.*/ $complaint_id="";
	private /*.string.*/ $user_id="";
	private /*.string.*/ $ept_version="";
	private /*.string.*/ $service_user_name="";
	private /*.string.*/ $additional_info="";
	
	private /*.string.*/ $wr_install_status='';
	private /*.string.*/ $server_ip="";
	private /*.string.*/ $server_name="";
	private /*.string.*/ $user_dtl="";
	private /*.string.*/ $call_from="";
	private /*.string.*/ $header_id="";
	private /*.string.*/ $state_id="";
	private /*.string.*/ $ip_address;
	private /*.string.*/ $data_type="";
	private /*.string.*/ $invalid_user_str="";
	private /*.string.*/ $valid_user_str="";
	private /*.string.*/ $store_url="";
	private /*.string.*/ $gst_status="";
	private /*.string.*/ $event_datetime="";
	private /*.string.*/ $db_comparison_dtl="";
	private /*.string.*/ $base_product_id="";
	
	/**
	 * @param string $msg
	 * 
	 * @return void
	 */
	public function sendError($msg){
		if($this->data_type=='json'){
			$out_arr['status'] = "failure";
			$out_arr['error_message'] = $msg;
			$out_msg = json_encode($out_arr);
		}else{
			$out_msg = "<POST_POS_RES>".
					"<STATUS>failure</STATUS>".
					"<ERROR_MESSAGE>$msg</ERROR_MESSAGE>";
			if($this->request_type=='36'){
				$out_msg .= "<INVALID_USERS>$this->invalid_user_str</INVALID_USERS>".
							"<VALID_USERS>$this->valid_user_str</VALID_USERS>".
							"<STORE_URL>$this->store_url</STORE_URL>";
			}
			$out_msg .= "</POST_POS_RES>";			
		}
		log_request($this->request_body, $out_msg, $out_msg, $this->cust_id, $this->request_type, '', $msg);
		echo $out_msg;
	}
	
	/**
	 * @param string $support_id
	 *
	 * @return void
	 */
	public function sendSuccess($support_id=''){
		if($this->data_type=='json'){
			$out_arr['status'] = "success";
			$out_msg = json_encode($out_arr);
		}else{
			$out_msg  = "<POST_POS_RES>".
						"<STATUS>success</STATUS>";
			$out_msg .= ($support_id!='')?"<SUPPORT_ID>$support_id</SUPPORT_ID>":"";
			if($this->request_type=='36'){
				$out_msg .= "<INVALID_USERS>$this->invalid_user_str</INVALID_USERS>".
							"<VALID_USERS>$this->valid_user_str</VALID_USERS>".
							"<STORE_URL>$this->store_url</STORE_URL>";
			}
			$out_msg .= "</POST_POS_RES>";
		}
		log_request($this->request_body, $out_msg, $out_msg, $this->cust_id, $this->request_type);
		echo $out_msg;
	}
	
	/**
	 * @return void
	 */
	public function handle_environment_log(){
		$complaint_to_customer_id = "220286"; //Easy Print Internal Customer Id
		$complaint_name = get_single_value_from_single_table("GFT_COMPLAINT_DESC", "gft_complaint_master", "GFT_COMPLAINT_CODE", $this->complaint_code);
		$prod_arr = explode('-',$this->pgroup);
		$productcode = $prod_arr[0];
		$skew_group  = $prod_arr[1];
		$complaint_by = SALES_DUMMY_ID;
		if((int)$this->user_id!=0){
			$complaint_by = $this->user_id;
		}
		$assign_to=get_product_manager($productcode,$skew_group);
		$addtional_dtl = "Customer Id: $this->cust_id. ";
		if($this->service_user_name!=''){
			$addtional_dtl .= " Service User Name: $this->service_user_name. ";
		}
		$this->full_description = stripslashes($addtional_dtl . $this->full_description);
		$support_id = insert_support_entry($complaint_to_customer_id, $productcode, $skew_group, $this->version, '',$complaint_by,'',stripslashes($this->description),
				$this->complaint_code, $this->support_status, null, null, $assign_to, null, '',$this->full_description);
		$this->sendSuccess($support_id);
	}
	
	/**
	 * @param string $xml_tags
	 * @param string[int] $xml_header_tags
	 * 
	 * @return void
	 */
	public function process_xml_request($xml_tags, $xml_header_tags=null){
		if($xml_header_tags==null){
			$xml_header_tags = array("idendity", "eidendity","trans_id","customer_id","product_id","base_product_id","version","issue_datetime","db_comparison_dtl","new_complaint",
							"priority","severity","description","support_status","current_version","updated_version","run_datetime","patch_status","additional_info",
							"db_schema_check","db_schema_changes","remarks","installtype","complaint_code","complaint_id","service_user_id","ept_version","service_user_name");
		}
		$this->parsed_xml=/*. (string[string]) .*/give_parsed_xml($xml_tags,$xml_header_tags);
		$this->idendity	=	isset($this->parsed_xml['idendity'])?$this->parsed_xml['idendity']:"";
		$this->eidendity=	isset($this->parsed_xml['eidendity'])?$this->parsed_xml['eidendity']:"";		
		$this->cust_id	=	isset($this->parsed_xml['customer_id'])?$this->parsed_xml['customer_id']:"";
		$this->pgroup	=	isset($this->parsed_xml['product_id'])?$this->parsed_xml['product_id']:"";
		if( ($this->cust_id=="") || ($this->pgroup=="") ){
			if(strcasecmp(md5($this->idendity), $this->eidendity)!=0){
				$this->sendError("Idendity Mismatch");
				exit;
			}
			$lead_dtl_arr = get_details_from_idendity($this->idendity);
			$this->cust_id = isset($lead_dtl_arr[0])?$lead_dtl_arr[0]:'';
			$this->gid_install_id= isset($lead_dtl_arr[1])?$lead_dtl_arr[1]:'';
			$pro_code = isset($lead_dtl_arr[4])?$lead_dtl_arr[4]:'';
			$pro_skew = isset($lead_dtl_arr[5])?$lead_dtl_arr[5]:'';
			$this->pgroup = $pro_code."-".substr($pro_skew, 0,4);
		}
		$temp_cust_id = $this->cust_id;
		$this->cust_id = get_single_value_from_single_table("GLH_LEAD_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", $this->cust_id);
		if($this->cust_id==''){
		    if($this->request_type=='36'){
		        $emply_id = (int)check_for_employee_mbile_and_id(substr($this->idendity,0,15));
		        if($emply_id > 0){
		            $emp_que = " select GEM_EMP_NAME,GLU_UUID from gft_emp_master join gft_local_user_activation_dtl on (GEM_EMP_ID=GLU_EMP_ID) ".
		                       " where GEM_EMP_ID='$emply_id' order by GLU_DATE_OF_ACTIVATION desc limit 1 ";
		            $emp_res = execute_my_query($emp_que);
		            if($emp_row = mysqli_fetch_array($emp_res)){
		                $api_response = register_peergroup_entry_for_webreporter(substr($this->idendity,0,19),$emply_id,$emp_row['GEM_EMP_NAME'],"GOFRUGAL",$emp_row['GLU_UUID']);
		                $json_arr = json_decode($api_response,true);
		                if( isset($json_arr['status']) && ($json_arr['status']=='success') ){
		                    $this->store_url = $json_arr['domain'];
		                    $this->sendSuccess();
		                    exit;
		                }
		            }
		        }
		    }
			$this->sendError("Customer Id($temp_cust_id) Not Found in CRM. Note - Employee License Not Supported");
			exit;
		}else if($this->pgroup==""){
			$this->sendError("Can't Process. Product Group is Empty.");
			exit;
		}
		$this->trans_id			= isset($this->parsed_xml['trans_id'])?$this->parsed_xml['trans_id']:"";
		$this->version			= isset($this->parsed_xml['version'])?$this->parsed_xml['version']:"";
		$this->issue_datetime 	= isset($this->parsed_xml['issue_datetime'])?$this->parsed_xml['issue_datetime']:"";
		$this->priority 		= isset($this->parsed_xml['priority'])?$this->parsed_xml['priority']:"";
		$this->severity 		= isset($this->parsed_xml['severity'])?$this->parsed_xml['severity']:"";
		$this->description 		= isset($this->parsed_xml['description'])?mysqli_real_escape_string_wrapper(str_replace("\\r\\n", "<br>",$this->parsed_xml['description'])):"";
		$this->support_status 	= isset($this->parsed_xml['support_status'])?$this->parsed_xml['support_status']:"";
		$this->full_description = $this->description." - Issue DateTime:$this->issue_datetime -- Version:$this->version ";
		$this->complaint_code	= isset($this->parsed_xml['complaint_code'])?$this->parsed_xml['complaint_code']:"97"; //default - product_bug
		$this->complaint_id		= isset($this->parsed_xml['complaint_id'])?$this->parsed_xml['complaint_id']:"";
		$this->user_id			= isset($this->parsed_xml['service_user_id'])?$this->parsed_xml['service_user_id']:"";
		$this->ept_version		= isset($this->parsed_xml['ept_version'])?$this->parsed_xml['ept_version']:"";
		$this->service_user_name= isset($this->parsed_xml['service_user_name'])?$this->parsed_xml['service_user_name']:"";
		$this->additional_info	= isset($this->parsed_xml['additional_info'])?$this->parsed_xml['additional_info']:"";
		
		$this->current_version 	= isset($this->parsed_xml['current_version'])?$this->parsed_xml['current_version']:"";
		$this->updated_version 	= isset($this->parsed_xml['updated_version'])?$this->parsed_xml['updated_version']:"";
		$this->run_datetime 	= isset($this->parsed_xml['run_datetime'])?$this->parsed_xml['run_datetime']:"";
		$this->patch_status 	= isset($this->parsed_xml['patch_status'])?$this->parsed_xml['patch_status']:"";
		$this->db_schema_check 	= isset($this->parsed_xml['db_schema_check'])?$this->parsed_xml['db_schema_check']:"";
		$this->db_schema_changes= isset($this->parsed_xml['db_schema_changes'])?$this->parsed_xml['db_schema_changes']:"";
		$this->remarks			= isset($this->parsed_xml['remarks'])?trim($this->parsed_xml['remarks']):"";
		$install_type_name		= isset($this->parsed_xml['installtype'])?$this->parsed_xml['installtype']:"";
		if($install_type_name=="SERVER"){
			$this->installtype = 1;
		}else if($install_type_name=="CLIENT"){
			$this->installtype = 2;
		}
		$this->db_comparison_dtl= isset($this->parsed_xml['db_comparison_dtl'])?$this->parsed_xml['db_comparison_dtl']:"";
		$this->wr_install_status= isset($this->parsed_xml['wr_install_status'])?$this->parsed_xml['wr_install_status']:"";
		$this->server_ip		= isset($this->parsed_xml['server_ip'])?$this->parsed_xml['server_ip']:"";
		$this->server_name		= isset($this->parsed_xml['server_name'])?$this->parsed_xml['server_name']:"";
		$this->user_dtl			= isset($this->parsed_xml['user_dtl'])?$this->parsed_xml['user_dtl']:"";
		$this->call_from		= isset($this->parsed_xml['from'])?$this->parsed_xml['from']:"";
		$this->state_id			= isset($this->parsed_xml['state_id'])?$this->parsed_xml['state_id']:"";
		$this->header_id		= isset($this->parsed_xml['header_id'])?$this->parsed_xml['header_id']:"";
		$this->gst_status		= isset($this->parsed_xml['gst_status'])?$this->parsed_xml['gst_status']:"";
		$this->event_datetime	= isset($this->parsed_xml['event_datetime'])?$this->parsed_xml['event_datetime']:"";
		$this->new_complaint	= isset($this->parsed_xml['new_complaint'])?$this->parsed_xml['new_complaint']:"";
		$this->base_product_id	= isset($this->parsed_xml['base_product_id'])?$this->parsed_xml['base_product_id']:"";
	}
	
	/**
	 * @return void
	 */
	public function post_the_issue_to_sam(){
		$prod_arr = explode('-',$this->pgroup);
		$productcode = $prod_arr[0];
		if(!isset($prod_arr[1])){
		    $this->sendError("Unable to identify the product group. Please send product code with product group");
		    exit;
		}
		$complaint_code_que = execute_my_query(" select GFT_COMPLAINT_CODE from gft_complaint_master where GFT_COMPLAINT_CODE='$this->complaint_code' and GFT_STATUS='A' ");
		if(mysqli_num_rows($complaint_code_que)<1){
		    $this->sendError("Complaint code is not available in SAM.Please post valid complaint code");
		    exit;
		}
		$skew_group  = $prod_arr[1];
		$assign_to	 = get_product_manager($productcode,$skew_group);
		if( isset($_FILES['file']['name']) ){
			$_FILES['upfile1']['name'][] 	= $_FILES['file']['name'];
			$_FILES['upfile1']['tmp_name'][]= $_FILES['file']['tmp_name'];
			$_FILES['upfile1']['size'][]	= $_FILES['file']['size'];
		}
		$complaint_by = $this->user_id;
		if($complaint_by==''){
			$complaint_by = SALES_DUMMY_ID;
		}
		$support_status = $this->support_status;
		if( (($this->complaint_code=='157') && ($this->support_status=='T70')) || ($productcode=='715') ){
			$support_status = 'T71';
		}
		//$new_complaint=($this->new_complaint=='true')?true:false; //changing it in order to create same complaint as activity to the existing same complaint
		$new_complaint = false;
		if(in_array($productcode,array('715','806'))){
			$sql1 = " select GCH_COMPLAINT_ID from gft_customer_support_hdr join gft_customer_support_dtl on (GCH_COMPLAINT_ID=GCD_COMPLAINT_ID) ".
					" where GCH_LEAD_CODE='$this->cust_id' and GCH_CURRENT_STATUS='$support_status' ".
					" and GCH_PRODUCT_CODE='$productcode' and GCH_COMPLAINT_CODE='$this->complaint_code' ";
			$res1 = execute_my_query($sql1);
			if(mysqli_num_rows($res1) > 0){
				$this->sendSuccess('');
				exit;
			}
		}
		$cust_ids_to_skip = explode(",", get_samee_const("SKIP_CUSTOMER_IDS_FROM_IOT_POSTING"));
		if(count($cust_ids_to_skip) > 0){
		    array_map("trim", $cust_ids_to_skip);
		    if(in_array($this->cust_id, $cust_ids_to_skip)){
		        $this->sendSuccess('customer id skipped in SAM');
		        exit;
		    }
		}
		$support_id = insert_support_entry($this->cust_id, $productcode, $skew_group, $this->version, '',$complaint_by,'',stripslashes($this->description),
						$this->complaint_code, $support_status, null, null, $assign_to, '22', $this->severity,stripslashes($this->full_description),
						true,$this->trans_id,null,$this->complaint_id,$this->priority,$new_complaint,'','','','','','',0,'','','',$this->base_product_id);
		if($this->additional_info!=""){
			$xml_log = "<additional_info>".$this->additional_info."</additional_info>";
			
			//Deadlock graph from Xevents doesn't produce good XML. So alternative approach to make the valid XML
			libxml_use_internal_errors(true);
			$xml_valid = simplexml_load_string($xml_log);
			if (!$xml_valid) {
				$xml_log = (string)str_replace("<victim-list>", "<deadlock><victim-list>", $xml_log);
				$xml_log = (string)str_replace("<process-list>", "</victim-list><process-list>", $xml_log);
				$xml_log = (string)str_replace(array("<deadlock-list>","</deadlock-list>"), "", $xml_log);
				
			}
			libxml_clear_errors();
			
			$xml_to_array_object = new simpleXml2Array($xml_log);
			$array_log = $xml_to_array_object->arr; 
			for($i=0; $i<count($array_log);$i++){
				$category		= 	isset($array_log['log'][$i]['category'])?$array_log['log'][$i]['category']:"";
				$subcategory	=	isset($array_log['log'][$i]['subcategory'])?$array_log['log'][$i]['subcategory']:"";
				$content_type	=	isset($array_log['log'][$i]['content_type'])?$array_log['log'][$i]['content_type']:"";
				$content		=	isset($array_log['log'][$i]['content'])?$array_log['log'][$i]['content']:"";
				$issue_data		=	$content;
				if($content_type=='xml'){
					$event_xml	= 	new Array2XML();
					$issue_data	= 	$event_xml->createXML("root",$content)->saveXML();
				}
				if( ($category!="") && ($issue_data!="") ){
					$table_name = "gft_pos_issue_dtl";
					$insert_array['GPI_COMPLAINT_ID'] 		= $support_id;
					$insert_array['GPI_CONTENT_TYPE'] 		= $content_type;
					$insert_array['GPI_CATEGORY_NAME'] 		= $category;
					$insert_array['GPI_SUBCATEGORY_NAME'] 	= $subcategory;
					$insert_array['GPI_ISSUE_DATA'] 		= $issue_data;
					array_update_tables_common($insert_array, $table_name, null, null, SALES_DUMMY_ID,null,null,$insert_array);
				}
			}
		}
		$this->sendSuccess($support_id);
		exit;
	}
	/**
	 * @param string $cust_id
	 * @param string $updated_version
	 * 
	 * @return void
	 */
	function check_exiting_patch_failure_support($cust_id,$updated_version,$remarks){
		$check_status 	= "T17";
		if($remarks=='EPT Patch'){
				$check_status 	= "T71";
		}
		execute_my_query("UPDATE gft_customer_support_hdr hdr  
			INNER JOIN gft_customer_support_dtl dtl ON(GCH_LAST_ACTIVITY_ID=gcd_activity_id)
			SET gch_current_status='T1', gcd_status='T1', GCD_REMARKS='Resoved automatically, the last updated version is $updated_version .'
			where (1)  and GCH_COMPLAINT_CODE='57' AND gch_current_status='$check_status' and hdr.GCH_COMPLAINT_DATE>='2017-06-27 00:00:00'".
				" and hdr.GCH_LEAD_CODE='$cust_id' ");
	}
	/**
	 * @param string $cust_id
	 * @param string $pcode
	 * @param string $pskew
	 * @param string $version
	 * @return void
	 */
	private function check_and_resolve_pending_patch_update_tickets($cust_id,$pcode,$pskew,$version) {
	    $valid_version = get_valid_version($version);
	    $wh_condition = " and (gpv_version='$version' or gpv_version='$valid_version') ";
	    $release_time_qry = execute_my_query(" select gpv_entered_on from gft_product_version_master where GPV_PRODUCT_CODE='$pcode' $wh_condition ");
	    $release_time = '';
	    if($row = mysqli_fetch_assoc($release_time_qry)) {
	        $release_time = $row['gpv_entered_on'];
	    }
	    $pskew_group = substr($pskew, 0, 4);
	    if($release_time!='') {
	        $complaint_id_qry = " select gch_complaint_id,gch_version,gch_product_type,gch_complaint_code,gpv_entered_on, ".
                	   	        " gch_fixed_in_version from gft_customer_support_hdr join gft_product_version_master on ".
                	   	        " (gpv_version=gch_fixed_in_version and gch_product_code=gpv_product_code) ".
                	   	        " where gch_current_status='T36' and gch_lead_code='$cust_id' and gch_product_code='$pcode' ".
                	   	        " and gch_product_skew='$pskew_group' ";
	        $complaint_id_res = execute_my_query($complaint_id_qry);
	        while($s_row = mysqli_fetch_array($complaint_id_res)) {
	            $ver_date = $s_row['gpv_entered_on'];
	            if(strtotime($release_time)>strtotime($ver_date)) {
    	            $ver = $s_row['gch_version'];
    	            $fix_version = $s_row['gch_fixed_in_version'];
    	            $product_type = $s_row['gch_product_type'];
    	            $complaint_code = $s_row['gch_complaint_code'];
    	            $complaint_id = $s_row['gch_complaint_id'];
    	            insert_support_entry($cust_id,$pcode,$pskew_group,$ver,$product_type,'9999','',
    	                "Ticket solved in version $fix_version and patch applied for version $version. Ticket status changed from pending patch update to Solved.",$complaint_code,'T1',null,null,
    	                null,'25','4',null,false,'',null,$complaint_id);
    	        }
	        }
	    }
	}
	/**
	 * @return void
	 */
	public function handle_patch_log(){
		$dtl_arr = get_details_from_idendity($this->idendity);
		$install_id = $dtl_arr[1];
		$order_no = $dtl_arr[2];
		$fullfillment_no = $dtl_arr[3];
		$pcode = $dtl_arr[4];
		$pskew = $dtl_arr[5];
		$skew_group = substr($pskew,0,4);
		$local_ip_arr = explode(',', get_samee_const("LOCAL_IP_ADDRESS_SETUPWIZARD"));
		$access_type = "1"; //external access - from customer
		if(in_array($this->ip_address, $local_ip_arr)){
			$access_type = "2"; //internal access - inside office
		}
		$act_message = $db_file_path = $support_id = "";
		$addons_patch = false;
		if(in_array($this->remarks, array('Web Reporter Patch','EPT Patch'))){
			$addons_patch = true;
		}
		if($this->patch_status=="S"){ //success
			$act_message = "Patch Version ".$this->updated_version." Applied Successfully";
			if( ($access_type=="1") && (!$addons_patch) ){  //external customer and not addons patch
				$upd_que = " update gft_install_dtl_new set GID_CURRENT_VERSION = '$this->updated_version' where GID_INSTALL_ID='$install_id' ";
				execute_my_query($upd_que);
			}
		}else if($this->patch_status=="F"){
			$act_message = "Patch Apply Failed for ".$this->current_version." to ".$this->updated_version;
			$loaded_file=isset($_FILES['file']['tmp_name'])?(string)$_FILES['file']['tmp_name']:null;
			$upload_path="../sales_server_support/Patch_Log";
			if(!file_exists($upload_path)){
				mkdir($upload_path);
				chmod($upload_path, 0777);
			}
			$dt_year = date('Y');	
			$upload_path= $upload_path."/".$dt_year;
			if(!file_exists($upload_path)){
				mkdir($upload_path);
				chmod($upload_path, 0777);
			}
			$up_file_name = date('Ymd').'_'.date('His').'_'.$this->cust_id;
			$upload_path = $upload_path."/".$up_file_name.".zip";
			if(!file_exists($upload_path)){
				if(move_uploaded_file($loaded_file, $upload_path)){
					$db_file_path = $dt_year."/".$up_file_name.".zip";
					chmod($upload_path, 0777);
				}
			}
			$support_file_path = "../Patch_Log/".$db_file_path;
			if($access_type=="1"){
			    if(!$addons_patch){  //customer
    				$complaint_status 	= "T17";
    				$complaint_code		= 57; //Unable to apply patch
    				$assign_to			= get_product_manager($pcode,$skew_group);
    				$support_id 		= insert_support_entry($this->cust_id, $pcode, $skew_group, $this->current_version,'',
    					SALES_DUMMY_ID, '', $act_message, $complaint_code, $complaint_status, null, null, $assign_to, null, '',$act_message,
    					true,'',null,null,'',true,$support_file_path);
			    }else if ($this->remarks=='Web Reporter Patch'){
			        $cust_name = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", $this->cust_id);
			        $prod_mail_id = get_single_value_from_single_table("GPM_SUPPORT_MAILID", "gft_product_family_master", "GPM_PRODUCT_CODE", '803');
			        $mail_config = array(
			            'comments'=>array("Web Reporter"),
			            'msg'=>array("$cust_name (Id : $this->cust_id) "),
			            'Attachment'=>array($upload_path)
			        );
			        send_formatted_mail_content($mail_config, 14, 340,null,null,array($prod_mail_id));
			    }
			}
		}
		
		$insert_arr['GPD_LEAD_CODE'] 		= $this->cust_id;
		$insert_arr['GPD_ORDER_NO'] 		= $order_no;
		$insert_arr['GPD_FULLFILLMENT_NO'] 	= $fullfillment_no;
		$insert_arr['GPD_PRODUCT_CODE'] 	= $pcode;
		$insert_arr['GPD_PRODUCT_SKEW'] 	= $pskew;
		$insert_arr['GPD_INSTALL_ID_REFF'] 	= $install_id;
		$insert_arr['GPD_PATCHRUN_DATETIME']= $this->run_datetime;
		$insert_arr['GPD_CURRENT_VERSION'] 	= $this->current_version;
		$insert_arr['GPD_UPDATED_VERSION'] 	= $this->updated_version;
		$insert_arr['GPD_PATCH_STATUS'] 	= $this->patch_status;
		$insert_arr['GPD_SCHEMA_STATUS'] 	= $this->db_schema_check;
		$insert_arr['GPD_SCHEMA_CHANGE_DTL']= $this->db_schema_changes;
		$insert_arr['GPD_REMARKS'] 			= $this->remarks;
		$insert_arr['GPD_LOG_FILE_PATH'] 	= $db_file_path;
		$insert_arr['GPD_RECEIVED_DATETIME']= date('Y-m-d H:i:s');
		$insert_arr['GPD_REMOTE_IPADDR'] 	= $this->ip_address;
		$insert_arr['GPD_ACCESS_TYPE'] 		= $access_type;
		$insert_arr['GPD_INSTALL_TYPE'] 	= $this->installtype;
		$insert_arr['GPD_DB_COMPARE_DTL'] 	= $this->db_comparison_dtl;
		array_update_tables_common($insert_arr, "gft_patch_dtl", null, null, SALES_DUMMY_ID,null,null,$insert_arr);
		if( ($this->patch_status=='S') && (!$addons_patch) ){// To resolve the existing "Unable to apply patch" Complaint
			$this->check_exiting_patch_failure_support($this->cust_id,$this->updated_version,$this->remarks);
// 			$this->check_and_resolve_pending_patch_update_tickets($this->cust_id,$pcode,$pskew,$this->updated_version);
		}
		$this->sendSuccess($support_id);
		exit;
	}
	
	/**
	 * @return void
	 */
	public function update_installer_status(){
		$hdr_check = get_single_value_from_single_table("GIH_ID", "gft_installer_info_hdr", "GIH_ID", $this->header_id);
		if($hdr_check==''){
			$this->sendError("Unable to identify the Header Id ($this->header_id) ");
			exit;
		}
		$dtl_query =" insert into gft_installer_info_dtl (GID_HDR_ID,GID_STATE,GID_REMARKS,GID_UPDATE_DATE, GID_IP_ADDRESS) ".
				" values ('$this->header_id','$this->state_id','".mysqli_real_escape_string_wrapper($this->remarks)."',now(),'$this->ip_address') ";
		$dtl_res = execute_my_query($dtl_query);
		$dtl_id = mysqli_insert_id_wrapper();
		$updhdr =" update gft_installer_info_hdr set GIH_LAST_DTL_ID=$dtl_id, GIH_CURRENT_STATE='$this->state_id', ".
				" GIH_UPDATE_DATE=now() where GIH_ID='$this->header_id' ";
		if(execute_my_query($updhdr)){
			$this->sendSuccess();
		}else{
			$this->sendError("Technical Error in Query Execution");
		}
	}
	
	/**
	 * @return void
	 */
	public function save_web_reporter_info(){
		$web_reporter_install_status = 0;
		if($this->wr_install_status=='Y'){
			$web_reporter_install_status = 1;
		}
		$prod_arr = explode("-", $this->pgroup);
		$updatearr['GID_WEB_REPORTER_INSTALL_STATUS'] = "$web_reporter_install_status";
		if(strlen($this->server_ip) > 1){
		    $updatearr['GID_SERVER_IP'] 	= $this->server_ip;
		}
		$updatearr['GID_SERVER_NAME'] 	= $this->server_name;
		
		//hq and saas products(truepos, servquick) will have the url in server_name param so no need to get from peergroup
		if( (strpos($this->server_name, "http://")!==false) || (strpos($this->server_name, "https://")!==false) ){
			$updatearr['GID_STORE_URL'] = $this->server_name;
			$updatearr['GID_PORT_STATUS'] = '1';
			$updatearr['GID_PORT_NUMBER'] = '';
		}else if((int)$prod_arr[0]!=300){ //not for hq
			//get from peergroup and update
			$split_order_no = substr($this->idendity,0,5)."-".substr($this->idendity,5,5)."-".substr($this->idendity,10,5)."-".substr($this->idendity,15,4)."-WR";
			$post_to_peergroup = true;
			if(is_trial_installtion($this->gid_install_id)){ //coming is trial installation and customer already has paid installation then no need to post to peergroup as already paid order number will be their
				$sql1 = " select GID_ORDER_NO from gft_install_dtl_new ".
						" join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_IS_BASE_PRODUCT='Y') ".
						" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
						" where GID_LEAD_CODE='$this->cust_id' and GID_STATUS!='U' and GPM_LICENSE_TYPE in (1,2) and GFT_SKEW_PROPERTY in (1,11) ";
				$res1 = execute_my_query($sql1);
				if(mysqli_num_rows($res1) > 0){
					$post_to_peergroup = false;
				}
			}
			if($post_to_peergroup){
			    $cust_res = execute_my_query("select GLH_CUST_NAME, GLH_CUST_STREETADDR2,GID_SYS_HKEY  from gft_lead_hdr join gft_install_dtl_new on(GLH_LEAD_CODE=GID_LEAD_CODE)".
			                     " where GLH_LEAD_CODE='$this->cust_id' and GID_INSTALL_ID='$this->gid_install_id'");
			    if($data1 = mysqli_fetch_array($cust_res)){
			        $cust_name = str_replace("'", "", $data1['GLH_CUST_NAME']);
			        $cust_location = $data1['GLH_CUST_STREETADDR2'];
			        $uuid = $data1['GID_SYS_HKEY'];
			        $post_str = "purpose=get_store_url&orderNo=$split_order_no&customerName=$cust_name&customerLocation=$cust_location&customerId=$this->cust_id&uuid=$uuid";
			        $ch = curl_init();
			        curl_setopt($ch,CURLOPT_URL,get_samee_const("Peergroup_Portcheck_Url"));
			        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_str);
			        $response_json = (string)curl_exec($ch);
			        curl_close($ch);
			        $response_arr = json_decode($response_json,true);
			        if( isset($response_arr['status']) && ($response_arr['status']=='success') ){
			            $updatearr['GID_STORE_URL']		= $response_arr['domain'];
			        }
			    }
			}
		}
		
		$table_key_arr['GID_INSTALL_ID']= $this->gid_install_id;
		$column_iff_update['GID_SERVER_UDPATED_DATE'] = date('Y-m-d H:i:s');
		array_update_tables_common($updatearr, "gft_install_dtl_new", $table_key_arr, null, SALES_DUMMY_ID,null,$column_iff_update);
		if($this->call_from=='web_reporter'){
			$this->sendSuccess();
			exit;
		}
		execute_my_query("update gft_app_users set GAU_USER_STATUS=0 where GAU_INSTALL_ID='$this->gid_install_id'");
		execute_my_query("update gft_customer_device_mapping set GCD_DEVICE_STATUS=0 where GCD_INSTALL_ID='$this->gid_install_id'");
		$invalid_user_arr = $valid_user_arr = /*. (string[int]) .*/array();
		$cust_lead_type = (int)get_single_value_from_single_table("GLH_LEAD_TYPE", "gft_lead_hdr", "GLH_LEAD_CODE", $this->cust_id);
		$lead_sub_type 	= (int)get_single_value_from_single_table("GLH_LEAD_SUBTYPE", "gft_lead_hdr", "GLH_LEAD_CODE", $this->cust_id);
		if( ($this->user_dtl!='') && (($cust_lead_type!=2) || ($lead_sub_type==10)) ){
			$user_dtl_xml = "<user_dtl>".$this->user_dtl."</user_dtl>";
			$xml_to_array_object = new simpleXml2Array($user_dtl_xml);
			$user_dtl_arr = $xml_to_array_object->arr;
			for($i=0; $i<count($user_dtl_arr['users']);$i++){
				$ref_emp_id = isset($user_dtl_arr['users'][$i]['id'])?$user_dtl_arr['users'][$i]['id']:'';
				$username	= isset($user_dtl_arr['users'][$i]['username'])?$user_dtl_arr['users'][$i]['username']:'';
				$app_pcode	= isset($user_dtl_arr['users'][$i]['addon_pcode'])?$user_dtl_arr['users'][$i]['addon_pcode']:'';
				$device_id	= isset($user_dtl_arr['users'][$i]['device_name'])?$user_dtl_arr['users'][$i]['device_name']:'';
				if($device_id!=''){ //device based
					update_device_id($this->gid_install_id,$device_id,$app_pcode,'1');
				}else{ //user based
					$mobile		= isset($user_dtl_arr['users'][$i]['mobile'])?$user_dtl_arr['users'][$i]['mobile']:'';
					$email		= isset($user_dtl_arr['users'][$i]['email'])?strtolower($user_dtl_arr['users'][$i]['email']):'';
					if( ($mobile!='') && !is_valid_mobile($mobile) ){
						$invalid_user_arr[] = $ref_emp_id;
						continue;
					}
					if( ($email!='') && (!is_valid_email($email)) ){
						$invalid_user_arr[] = $ref_emp_id;
						continue;
					}
					$valid_user_arr[] = $ref_emp_id;
					//save_pos_user_contacts($this->cust_id, $this->gid_install_id, $ref_emp_id, $mobile, $email, $username,'A','5','',true);
					//if( ($mobile!='') || ($email!='') ){
					//	update_app_user($this->gid_install_id, $ref_emp_id, $app_pcode, '1');
					//}
					$mobile_gcc_id = save_and_get_contact_id($this->cust_id, $mobile, '', '', '');
					if($mobile_gcc_id > 0){
						update_app_to_mobile($mobile_gcc_id,$this->gid_install_id,$app_pcode,'1');
					}
				}
			}
		}
		$this->invalid_user_str = implode(',', $invalid_user_arr);
		$this->valid_user_str	= implode(',', $valid_user_arr);
		post_to_connectplus_authservice($this->cust_id);
		$this->store_url		= isset($updatearr['GID_STORE_URL'])?$updatearr['GID_STORE_URL']:'';
		if($this->invalid_user_str!=''){
			$this->sendError("Invalid Contacts found in the users list");
		}else{
			$this->sendSuccess();
		}
		exit;
	}
	
	/**
	 * @return void
	 */
	function insert_product_migration_data() {
		$log_file = isset($_FILES['file']['tmp_name'])?(string)$_FILES['file']['tmp_name']:'';
		$log_file_path = '';
		if($log_file!=='') {
			$log_file_path = "../sales_server_support/Migration_dtl";
			if(!file_exists($log_file_path)) {
				mkdir($log_file_path);
				chmod($log_file_path,0777);
			}
			$file_name = $this->cust_id."-".strtotime("now");
			$log_file_path .= "/$file_name.zip";
			if(!file_exists($log_file_path)) {
				if(move_uploaded_file($log_file,$log_file_path)) {
					chmod($log_file_path,0777);
				} else {
					$this->sendError("Error occurred while uploading log file. Please try again.");
				}
			}
		}
		$install_dtl = array();
		$install_dtl['GMD_INSTALL_ID'] 		 = $this->gid_install_id;
		$install_dtl['GMD_VERSION_NO'] 		 = $this->current_version;
		$install_dtl['GMD_MIGRATION_STATUS'] = $this->patch_status;
		$install_dtl['GMD_LOG_FILE_PATH']	 = $log_file_path;
		$install_dtl['GMD_REMARKS']  		 = $this->remarks;
		$install_dtl['GMD_REQUEST_DATETIME'] = date('Y-m-d H:i:s');
		$install_dtl['GMD_RUN_DATETIME'] 	 = $this->run_datetime;
		$insert_res = array_update_tables_common($install_dtl, "gft_product_migration_dtl", null, null, SALES_DUMMY_ID,null,null,$install_dtl);
		if($insert_res) {
			$this->sendSuccess();
		} else {
			$this->sendError("Error occurred while updation migration detail. Please try again.");
		}
	}
	/**
	 * @return void
	 */
	function insert_pos_status_tracker() {
         $pos_status = array();
         $pos_status['GPT_INSTALL_ID']      = $this->gid_install_id;
         $pos_status['GPT_STATUS']          = $this->gst_status;
         $pos_status['GPT_REMARKS']         = $this->remarks;
         if($this->event_datetime!=""){
         	$pos_status['GPT_EVENT_DATETIME']  = $this->event_datetime;
         }
         $pos_status['GPT_REQUEST_DATETIME']= date('Y-m-d H:i:s');
         $pos_status_res = array_insert_query("gft_pos_status_tracker", $pos_status,"boolean");
	     if($pos_status_res){
	         if($this->gst_status=='15'){
	             $lead_owner = get_valid_lead_owner($this->cust_id);
	             $act_comment = "Lead is interested in the Food aggregator solution demo and ".$this->remarks;
	             create_appointment($this->cust_id, $lead_owner, $act_comment, '91', '49', date('Y-m-d'));
	         }
	     	$this->sendSuccess();
	     } else {
	     	$this->sendError("Error Occurred while inserting the POS Status Details.");
	     }
	}
	
	/**
	 * @return void
	 */
	public function __construct(){
		$this->ip_address = $_SERVER['REMOTE_ADDR'];
		try{
			$this->request_body = file_get_contents('php://input');
			$headers = apache_request_headers();
			foreach ($headers as $header => $value) {
				if($header=='meta_data'){
					$this->request_body = html_entity_decode((string)$value);
				}
			}
			$this->data_type = isset($_GET['datatype'])?(string)$_GET['datatype']:'';
			if($this->data_type=='json'){
				$head_tag 	= isset($_GET['purpose'])?(string)$_GET['purpose']:'root';
				$array_data = json_decode($this->request_body, true);
				$arr_to_xml	= 	new Array2XML();
				$xml_str	= 	$arr_to_xml->createXML($head_tag,$array_data)->saveXML();
				$this->request_body = (string)str_replace(array("\n","  "),"", $xml_str);
			}
		}catch(Exception $e){
			die("Exception in: ".$e);
		}
		$xml_header_tags=/*. (string[int]) .*/array("POST_POS_ISSUE","POST_PATCH_LOG","POST_ENVIRONMENT_LOG","APP_USER_PROVISIONING","POST_INSTALLER_STATUS",
				"POST_DETRACMIG_LOG","POST_STATUS","PRINT_PROFILE_IMPORT");
		$parsed_xml1=give_parsed_xml($this->request_body,$xml_header_tags);
		if(isset($parsed_xml1['POST_POS_ISSUE']) && ($parsed_xml1['POST_POS_ISSUE']!=='')){
			$this->request_type = '22';
			$this->process_xml_request($parsed_xml1['POST_POS_ISSUE']);
			$this->post_the_issue_to_sam();
		}elseif (isset($parsed_xml1['POST_PATCH_LOG']) && ($parsed_xml1['POST_PATCH_LOG']!=='')){
			$this->request_type = '25';
			$this->process_xml_request($parsed_xml1['POST_PATCH_LOG']);
			$this->handle_patch_log();
		}elseif (isset($parsed_xml1['POST_ENVIRONMENT_LOG']) && ($parsed_xml1['POST_ENVIRONMENT_LOG']!=='')){
			$this->request_type = '31';
			$this->process_xml_request($parsed_xml1['POST_ENVIRONMENT_LOG']);
			$this->handle_environment_log();
		}elseif (isset($parsed_xml1['APP_USER_PROVISIONING']) && ($parsed_xml1['APP_USER_PROVISIONING']!=='')){
			$this->request_type = '36';
			$xml_sub_tags = array("idendity","eidendity","wr_install_status","server_ip","server_name","user_dtl","from");
			$this->process_xml_request($parsed_xml1['APP_USER_PROVISIONING'],$xml_sub_tags);
			$this->save_web_reporter_info();
		}elseif (isset($parsed_xml1['POST_INSTALLER_STATUS']) && ($parsed_xml1['POST_INSTALLER_STATUS']!=='')){
			$this->request_type = '40';
			$xml_sub_tags = array("idendity","eidendity","customer_id","state_id","remarks","header_id");
			$this->process_xml_request($parsed_xml1['POST_INSTALLER_STATUS'],$xml_sub_tags);
			$this->update_installer_status();
		}else if(isset($parsed_xml1['POST_DETRACMIG_LOG']) && ($parsed_xml1['POST_DETRACMIG_LOG']!=='')) {
			$this->request_type = '51';
			$this->process_xml_request($parsed_xml1['POST_DETRACMIG_LOG']);
			$this->insert_product_migration_data();
		}else if(isset($parsed_xml1['POST_STATUS']) && ($parsed_xml1['POST_STATUS']!=='')) {
				$this->request_type = '52';
				$this->process_xml_request($parsed_xml1['POST_STATUS'],array("idendity", "eidendity","gst_status","remarks","event_datetime"));
				$this->insert_pos_status_tracker();		
		}else if(isset($parsed_xml1['PRINT_PROFILE_IMPORT']) && ($parsed_xml1['PRINT_PROFILE_IMPORT']!='') ){
			$this->request_type = '52';
			$this->process_xml_request($parsed_xml1['PRINT_PROFILE_IMPORT'],array("idendity", "eidendity","version"));
			update_plugin_version("1",$this->gid_install_id,$this->version);
			$this->sendSuccess();
		}else{
			$this->sendError("Unable to idendity the XML header tag");
			exit;
		}
	}
}
$obj = new postToSAM();
?>
