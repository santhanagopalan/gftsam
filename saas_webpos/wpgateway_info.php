<?php
require_once( __DIR__ . "/../product_util.php");
$secret1=bin2hex("myownkey");
/* Using in TruePOS Recharge 
 * 1. incase of recharge new order no to pass in the below function */

//$test_lic=true; /* for testing purpose it wont sent request to true pos test server */

/**
 * @param int $tenant_id
 * @param int $server_id
 * @param int $product_code
 * @param string $action
 * @param string $order_no
 * 
 * @return void
 */
function send_status_update_mail($tenant_id,$server_id,$product_code,$action,$order_no=null){ 	
	global $mail_to_team;

			$query="select TENANT_STATUS, GTS_NAME,TENANT_DOMAIN_NAME,TENANT_SAM_ID,TENANT_CUSTOM_DOMIN,TENANT_PRODUCT," .
						"GSM_WPOS_ADDR,GLH_CUST_NAME,group_concat(gcc_contact_no) as email_id from gft_tenant_master " .
						"join gft_server_master on (GSM_SERVER_ID=SERVER_ID) " .
						"join gft_lead_hdr lh on (glh_lead_code=TENANT_SAM_ID ) " .
						"join gft_tenant_status_master stm on (TENANT_STATUS=GTS_CODE ) " .
						"JOIN gft_customer_contact_dtl ccd on (gcc_lead_code=glh_lead_code and gcc_contact_type='4')" .
						"where TENANT_ID=$tenant_id and SERVER_ID=$server_id  group by TENANT_SAM_ID ";
			$result=execute_my_query($query);
			$data=mysqli_fetch_array($result);
			$tenant_status=(int)$data['TENANT_STATUS'];
			$lead_code=(int)$data['TENANT_SAM_ID'];
			$domain_name=$data['TENANT_DOMAIN_NAME'].".".$data['GSM_WPOS_ADDR'];
			$cust_name=$data['GLH_CUST_NAME'];
			$to_emailids=explode(',',$data['email_id']);
			$read_only_period=0;
			$date_of_renewal='';
			$template_id=0;
			if($action=='status_change'){
				
				
					if($tenant_status==4){ // read only
						$read_only_period=(int)get_samee_const('TRUEPOS_READ_ONLY_PERIOD');
						$date_of_renewal=date('d-m-Y',mktime('0','0','0',(int)date('m'),(int)date('d')+$read_only_period,(int)date('Y')));
						switch($product_code){
						case 601:	$template_id=93;	break;
						case 602:	$template_id=95;	break;
						case 603:	$template_id=97;	break;
						default:	
							break;
					 	}
					 	
					}
					if($tenant_status==5){  // Dropped or deacativated 
						switch($product_code){
						case 601:	$template_id=94;	break;
						case 602:	$template_id=96;	break;
						case 603:	$template_id=98;	break;
						default:	
							break;
						} 
					}					
					
					if($tenant_status==2){  // uninstall
						switch($product_code){
						case 601:	$template_id=109;	break;
						case 605:	$template_id=180;	break;
						//case 602:	$template_id=96;	break;
						//case 603:	$template_id=98;	break;
						default:	
							break;
						} 
					}					
					
					
					if(($tenant_status==3 || $tenant_status==2) && $mail_to_team==true){
						/* uninstall from website/webpos_cancel.php */
						/* mail to team : server changed ,uninstalled */	
						$db_content_config=/*. (string[string][int]) .*/array(
						'SAAS_existing_plan'=>array(),
						'domin_name'=>array($domain_name),
						'date_of_renewal'=>array($date_of_renewal),
						'Customer_Name'=>array($cust_name),
						'readonly_period'=>array($read_only_period),
						'dateon'=>array(date('d-m-Y')),
						'reason'=>array('')
						);
						$tenant_status_string=$data['GTS_NAME'];
						$msg=" Status Change ".$tenant_status_string ."<br> Tenant Id ".$tenant_id ."<br> Server id ".$server_id ."<br>Domain Name".$domain_name ;
							$product_info_array=get_product_from_code((string)$product_code);
							$team_mail_id=$product_info_array[(string)$product_code][2];
						send_mail_from_sam($category=59,$team_mail_id,$team_mail_id,'Info: Status Change ',$msg,$attachment_file_tosend=null,
						$cc=null,true,$team_mail_id,$from_page=null,$user_info_needed=false);				
					}
					if($template_id!=0){
						$db_content_config=/*. (string[string][int]) .*/ array(
						'SAAS_existing_plan'=>array(),
						'domin_name'=>array($domain_name),
						'date_of_renewal'=>array($date_of_renewal),
						'Customer_Name'=>array($cust_name),
						'readonly_period'=>array($read_only_period),
						'dateon'=>array(date('d-m-Y'))
						);
						send_formatted_mail_content($db_content_config,57,$template_id,null,array($lead_code));
					}
					
			}else if($action=='Renew' or $action=='Recharge'){
				$purpose='';
				if($order_no!=''){
					$purpose=get_purpose_of_the_order($order_no,$product_code);
				}
				if($purpose=='') {
					$purpose='Renewal ';
				}
				$db_content_config=array(
					'SAAS_existing_plan'=>array(),
					'domin_name'=>array($domain_name),
					'date_of_renewal'=>array(),
					'Customer_Name'=>array($cust_name),
					'purpose'=>array($purpose)
				);
				if($product_code==601){
					send_formatted_mail_content($db_content_config,37,108,null,array($lead_code));
				}else{
					send_formatted_mail_content($db_content_config,37,179,null,array($lead_code));
				}
			}
}

/**
 * @param string $text
 *
 * @return string
 */
function get_valid_text1($text){
	$text=preg_replace('/[\s\s]+ | [\n\t\r]/', ' ', trim($text));
	return preg_replace('/[^[:print:]]/','',$text);
}

/**
 * @param int $lead_code
 * @param int $product_code
 *
 * @return string[string]
 */
function get_root_order_no_install_id($lead_code,$product_code){
	$result_root_ono=execute_my_query("SELECT GID_ORDER_NO,GID_INSTALL_ID FROM gft_install_dtl_new WHERE GID_LEAD_CODE=$lead_code AND " .
			"GID_LIC_PCODE=$product_code and gid_status='A' limit 1 ");
	if($data_root_order_no=mysqli_fetch_array($result_root_ono)){

		$return_arr['GID_ORDER_NO']=$data_root_order_no['GID_ORDER_NO'];
		$return_arr['GID_INSTALL_ID']=$data_root_order_no['GID_INSTALL_ID'];
		return $return_arr;
	}
	return null;
}

/**
 * @param string $xmlRaw
 * @param string[int] $xmlFieldNames
 *
 * @return string[string]
 */
function give_saasparsedxml($xmlRaw,$xmlFieldNames){
	$parsedXML=/*. (string[string]) .*/ array();
	foreach ($xmlFieldNames as $xmlField) {
		if(strpos($xmlRaw,$xmlField)!==false){
			$parsedXML[$xmlField]=substr($xmlRaw,
					strpos($xmlRaw,"<$xmlField>")+strlen("<$xmlField>"),
					strpos($xmlRaw,"</$xmlField>")-strlen("<$xmlField>")-strpos($xmlRaw,"<$xmlField>"));
			$parsedXML[$xmlField]=get_valid_text1($parsedXML[$xmlField]);
			//$parsedXML[$xmlField]=get_valid_text1(lic_decrypt($parsedXML[$xmlField],$secret1));
		}
	}
	return $parsedXML;
}

/**
 * @param int $request_id
 *
 * @return boolean
 */
function webposgateway_post($request_id){
	$result_r=execute_my_query("SELECT GSM_SERVER_NAME, GSM_DOMAIN_NAME, GSM_WPOS_ADDR, GSR_REQUEST_ID,  GSR_LEAD_CODE, GSR_ORDER_NO,GRD_REQUEST,GSM_ERROR_MAIL_ALERT " .
			" FROM gft_webpos_gateway_request join gft_server_master on (GSM_SERVER_ID=GSR_SERVER_ID) where GSR_REQUEST_ID=$request_id ");
	if($data=mysqli_fetch_array($result_r)){
		$data_sent=$data['GRD_REQUEST'];

$cc_emailid=$data['GSM_ERROR_MAIL_ALERT'];
		$url_encoded_pgs=urlencode($data_sent);
		$webpossam_url=$data['GSM_DOMAIN_NAME'];
		$url1 = "$webpossam_url/SAMSAASServlet";
		$curl_proxyid='';
		/* map this in db constants for testing curl_proxyid='10.0.0.4:3128'; */
		$curl_proxyid=get_samee_const('CURLOPT_PROXY');
		$update_ch = curl_init();
		if($update_ch === false){
			$msg='ERROR: Curl error during init';
			error_log($msg);
			mail_error_alert('SAAS CURL ERROR: init problem',$msg,7,$cc_emailid);
		}else{
			curl_setopt($update_ch, CURLOPT_URL,$url1);
			if($curl_proxyid!="")  curl_setopt($update_ch, CURLOPT_PROXY, $curl_proxyid);
			curl_setopt($update_ch, CURLOPT_POST,1);
			curl_setopt($update_ch,CURLOPT_POSTFIELDS,"xmldata=$url_encoded_pgs");
			curl_setopt($update_ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($update_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$result= curl_exec($update_ch);
			$error_mes='';
			if($result===false) {
				$error_mes= "xml mesasge \n:". $data_sent.
					"<br> \n ". $url1."?xmldata=".$url_encoded_pgs.
					"<br> \n Curl Error:".curl_error($update_ch) .curl_errno($update_ch) .
					"<br> \n result got: ".$result;
				mail_error_alert('SAAS CURL ERROR',$error_mes,7,$cc_emailid);
			}else{
				$parsedXML=give_saasparsedxml($result,array('status','message'));
				if(!isset($parsedXML['status'])){
					$parsed_error_message=isset($parsedXML['message'])?$parsedXML['message']:'(message tag not found. Please check the xml)';
					$error_message="URL::  ".$url1."?xmldata=$url_encoded_pgs".
						"<br> \n Request Id: ".$request_id .
						"<br> \n Error Message :".$parsed_error_message.
						"<br> \n Error :".print_r($parsedXML,true).
						"<br> \n result got :".$result.
						"<br> \n";

					mail_error_alert('SAAS CURL ERROR: status problem',$error_message,7,$cc_emailid);
				}else if($parsedXML['status']!='success'){
					$error_message="URL:: ".$url1."?xmldata=$url_encoded_pgs".
						"<br> \n Request Id: ".$request_id .
						"<br> \n Error Message :".$parsedXML['message'] .
						"<br> \n result got: ".$result;
					mail_error_alert('SAAS CURL ERROR',$error_message,7,$cc_emailid);
				}
			}
			curl_close($update_ch);
			execute_my_query("update gft_webpos_gateway_request set GRD_RESPONSE='".mysqli_real_escape_string_wrapper($result)."' where GSR_REQUEST_ID=$request_id ");
			if($error_mes==''){
				return true;
			}
			else{
				return false;
			}
				
		}
	}
	return false;
}

/**
 * @param int $tenant_id
 * @param int $server_id
 * @param int $install_id
 * @param string $action
 *
 * @return boolean
 */
function send_saas_server_tenant_status($tenant_id,$server_id,$install_id,$action){

	if(empty($install_id)) { exit;}

	$send_status=false;
	/* get the Request id to be sent */
	$query_request_id="insert into gft_webpos_gateway_request (GSR_SERVER_ID,GSR_ORDER_NO,GSR_LEAD_CODE,GSR_DATE)(" .
			"select SERVER_ID,GID_ORDER_NO,GID_LEAD_CODE,date(now()) FROM gft_install_dtl_new  " .
			"join gft_tenant_master tm on (TENANT_SAM_ID=gid_lead_code and GID_PRODUCT_CODE=TENANT_PRODUCT ) " .
			"where gid_install_id=$install_id ) ";
	//print $query_request_id;
	$result_request_id=execute_my_query($query_request_id);
	if($result_request_id){
		$request_id=mysqli_insert_id_wrapper();
			
		$query="select TENANT_STATUS,GTS_NAME,TENANT_DOMAIN_NAME,TENANT_SAM_ID,TENANT_CUSTOM_DOMIN,TENANT_PRODUCT,GSM_WPOS_ADDR," .
				" IF(GID_EXPIRE_FOR=2,GID_VALIDITY_DATE,'0000-00-00') GID_VALIDITY_DATE,GID_ORDER_NO,GID_PRODUCT_SKEW, " .
				" GID_NO_CLIENTS,GID_NO_COMPANYS   " .
				" from gft_tenant_master " .
				" join gft_server_master on (GSM_SERVER_ID=SERVER_ID) " .
				" join gft_install_dtl_new ins on (gid_lead_code=TENANT_SAM_ID and gid_lic_pcode=TENANT_PRODUCT and gid_install_id=$install_id) " .
				" join gft_tenant_status_master stm on (GTS_CODE=TENANT_STATUS )" .
				" where TENANT_ID=$tenant_id and SERVER_ID=$server_id ";
		$result=execute_my_query($query);
		if($data=mysqli_fetch_array($result)){
			$domin_name=$data['TENANT_DOMAIN_NAME'].($data['TENANT_CUSTOM_DOMIN']!='1'?'.'.$data['GSM_WPOS_ADDR']:'');
			$lead_code=$data['TENANT_SAM_ID'];
			$tenant_status=$data['TENANT_STATUS'];
			$is_custome_domain=$data['TENANT_CUSTOM_DOMIN'];
			$product_code=$data['TENANT_PRODUCT'];
			$product_code_skew=$data['TENANT_PRODUCT'].'-'.$data['GID_PRODUCT_SKEW'];
			$tenant_status_string=$data['GTS_NAME'];
			$order_no=$data['GID_ORDER_NO'];
			$action='status_change';
			$data_sent1="<REGISTERINFO>" .
					"<request_id>".$request_id."</request_id>" .
					"<action>".$action."</action>".
					"<sam_id>".$lead_code."</sam_id>" .
					"<server_id>".$server_id."</server_id>".
					"<domin_name>".$domin_name."</domin_name>" .
					"<tenant_id>".$tenant_id."</tenant_id>".
					"<tenant_status>".$tenant_status ."</tenant_status>".
					"<product_purchased>".$product_code_skew."</product_purchased>" .
					"<is_custome_domain>".$is_custome_domain ."</is_custome_domain>" .
					"<no_of_tills>".$data['GID_NO_CLIENTS']."</no_of_tills>" .
					"<no_of_users>".$data['GID_NO_COMPANYS']."</no_of_users>".
					"<subscription_validity>".$data['GID_VALIDITY_DATE']." 00:00:00</subscription_validity>".
					"</REGISTERINFO>";

				
			$result_r=execute_my_query("replace into gft_webpos_gateway_request (GSR_REQUEST_ID,GSR_SERVER_ID,GSR_DATE, GSR_ORDER_NO, GSR_LEAD_CODE,GRD_REQUEST,GRD_PURPOSE) " .
					" values('$request_id',$server_id,now(),'$order_no','$lead_code', '".mysqli_real_escape_string_wrapper($data_sent1)."','$action')");
				
				
			global $update_status_to_server;
			$update_status_to_server=(isset($update_status_to_server)?$update_status_to_server:true);
			if($update_status_to_server==false){
				send_status_update_mail($tenant_id,$server_id,$product_code,$action);
				return false;
			}
			$send_status=webposgateway_post($request_id);
			if($send_status==true){
				send_status_update_mail($tenant_id,$server_id,$product_code,$action);
			}
			else if($send_status==false){
				$msg=" Order No : $order_no, Customer Id :$lead_code \n " .
				"\n Domain Name: $domin_name \n Status Change : $tenant_status_string " .
				"Please Check the saas Gateway process report for the above mentioned request and re send it again ";
				send_mail_from_sam($category=37,'true-pos@gofrugal.com','true-pos@gofrugal.com','SaaS Gateway Process Failed',
				$msg,$attachment_file_tosend=null,
				$cc=null,false,'true-pos@gofrugal.com',$from_page=null,$user_info_needed=false);
					
			}
			 
		}/* end of if(data)*/

		return $send_status;
	}/* end of request id */

	return false;
}

/**
 * @param int $install_id
 *
 * @return string
 */
function get_expiry_date_install_id($install_id){
	$result=execute_my_query("SELECT GID_VALIDITY_DATE FROM gft_install_dtl_new WHERE GID_INSTALL_ID=$install_id");
	if($data=mysqli_fetch_array($result)){
		return $data['GID_VALIDITY_DATE'];
	}
	return date('Y-m-d');
}
/**
 * @param string $text_value
 * 
 * @return string
 */
function replaceSpecialCharecter($text_value){
	$text_value=str_replace("&", "&amp;", $text_value);
	$text_value=str_replace("<", "&lt;", $text_value);
	$text_value=str_replace(">", "&gt;", $text_value);
	return $text_value;
}
/**
 * @param string $lead_code
 * 
 * @return boolean
 */
function is_active_saas_customer($lead_code){
    $sql_saas_product = "select GID_LEAD_CODE, GID_PRODUCT_CODE,GID_PRODUCT_SKEW,GID_SERVER_ID,gid_tenant_id,GID_INSTALL_ID, ".
        " GLH_CUST_NAME,TENANT_DOMAIN_NAME,GSM_WPOS_ADDR from gft_install_dtl_new ".
        " inner join gft_product_master on(GID_LIC_PCODE=GPM_PRODUCT_CODE and GID_LIC_PSKEW=GPM_PRODUCT_SKEW) ".
        " join gft_tenant_master on (TENANT_SAM_ID=gid_lead_code and TENANT_STATUS in (1,4) and GID_PRODUCT_CODE=TENANT_PRODUCT and GID_TENANT_ID=TENANT_ID and GID_SERVER_ID=SERVER_ID)" .
        " join gft_server_master on (GSM_SERVER_ID=SERVER_ID)" .
        " INNER JOIN gft_lead_hdr ON(GLH_LEAD_CODE=GID_LEAD_CODE)".
        " where GID_PRODUCT_CODE in('601','605') AND  GID_STATUS='A' AND GID_LEAD_CODE='$lead_code'".
        " AND GID_VALIDITY_DATE>NOW() ORDER BY GPM_FREE_EDITION DESC,  GID_VALIDITY_DATE DESC LIMIT 1";
    $result_saas_product = execute_my_query($sql_saas_product);
    if(mysqli_num_rows($result_saas_product)>0){
        return true;
    }
    return false;
}
/**
 * @param string $lead_code
 * @param string $order_no
 * @param string[string] $product_code
 * 
 * @return void
 */
function send_addon_product_dtl_to_saas_server($lead_code, $order_no,$product_code){
    $sql_saas_product = "select GID_LEAD_CODE, GID_PRODUCT_CODE,GID_PRODUCT_SKEW,GID_SERVER_ID,gid_tenant_id,GID_INSTALL_ID, ". 
        " GLH_CUST_NAME,TENANT_DOMAIN_NAME,GSM_WPOS_ADDR from gft_install_dtl_new ". 
        " inner join gft_product_master on(GID_LIC_PCODE=GPM_PRODUCT_CODE and GID_LIC_PSKEW=GPM_PRODUCT_SKEW) ".
        " join gft_tenant_master on (TENANT_SAM_ID=gid_lead_code and TENANT_STATUS in (1,4) and GID_PRODUCT_CODE=TENANT_PRODUCT and GID_TENANT_ID=TENANT_ID and GID_SERVER_ID=SERVER_ID)" .
        " join gft_server_master on (GSM_SERVER_ID=SERVER_ID)" .
        " INNER JOIN gft_lead_hdr ON(GLH_LEAD_CODE=GID_LEAD_CODE)".
        " where GID_PRODUCT_CODE in('601','605') AND  GID_STATUS='A' AND GID_LEAD_CODE='$lead_code'". 
        " AND GID_VALIDITY_DATE>NOW() ORDER BY GPM_FREE_EDITION DESC,  GID_VALIDITY_DATE DESC LIMIT 1";
    $result_saas_product = execute_my_query($sql_saas_product);
    if((mysqli_num_rows($result_saas_product)>0) && ($row_saas_product=mysqli_fetch_assoc($result_saas_product))){
        $install_id = $row_saas_product['GID_INSTALL_ID'];
        $customer_name=htmlentities(trim($row_saas_product['GLH_CUST_NAME']));
        $customer_name	=	replaceSpecialCharecter(str_replace("'", "", $customer_name));
        $product_code_con = implode(',', $product_code);
        $base_product = $row_saas_product['GID_PRODUCT_CODE']."-".$row_saas_product['GID_PRODUCT_SKEW'];
        $server_id = $row_saas_product['GID_SERVER_ID'];
        $tenant_id = $row_saas_product['gid_tenant_id'];
        $domin_name=$row_saas_product['TENANT_DOMAIN_NAME'].'.'.$row_saas_product['GSM_WPOS_ADDR'];
        $sql_addon_product = "select GID_ORDER_NO,GID_PRODUCT_CODE, GID_NO_CLIENTS,GID_NO_COMPANYS,". 
        " GID_VALIDITY_DATE,GID_STATUS,GID_FULLFILLMENT_NO  from gft_install_dtl_new where ". 
        " GID_PRODUCT_CODE IN($product_code_con) AND GID_LEAD_CODE='$lead_code'";
       $result_addon_product = execute_my_query($sql_addon_product);
       $order_date = get_single_value_from_single_table("GOD_CREATED_DATE", "gft_order_hdr", "GOD_ORDER_NO", "$order_no");
       $addon_xml_dlt = "";
       while($row_addon_product=mysqli_fetch_assoc($result_addon_product)){
           $install_status = $row_addon_product['GID_STATUS']=='A'?'A':'I';
           $root_order_no  = $row_addon_product['GID_ORDER_NO'].str_pad($row_addon_product['GID_FULLFILLMENT_NO'], 4, '0', STR_PAD_LEFT);;
           $addon_xml_dlt .= "<addon>";
           $addon_xml_dlt .= "<addon_code>".$row_addon_product['GID_PRODUCT_CODE']."</addon_code>";
           $addon_xml_dlt .= "<addon_qty>".$row_addon_product['GID_NO_CLIENTS']."</addon_qty>";
           $addon_xml_dlt .= "<addon_order_date>$order_date</addon_order_date>";
           $addon_xml_dlt .= "<addon_exp_dt>".$row_addon_product['GID_VALIDITY_DATE']."</addon_exp_dt>";
           $addon_xml_dlt .= "<addon_order_no>$order_no</addon_order_no>";
           $addon_xml_dlt .= "<addon_status>$install_status</addon_status>";
           $addon_xml_dlt .= "<addon_root_order_no>$root_order_no</addon_root_order_no>";
           $addon_xml_dlt .= "</addon>";
       }
       if($addon_xml_dlt!=""){
           execute_my_query( "update gft_order_product_dtl set GOP_USEDQTY=gop_qty, GOP_CP_USEDQTY=gop_qty " .
                            " WHERE GOP_ORDER_NO='$order_no' AND GOP_PRODUCT_CODE IN($product_code_con) ");
           $query_request_id="insert into gft_webpos_gateway_request (GSR_SERVER_ID,GSR_ORDER_NO,GSR_LEAD_CODE,GSR_DATE)(" .
               "select SERVER_ID,GID_ORDER_NO,GID_LEAD_CODE,now() FROM gft_install_dtl_new  " .
               "join gft_tenant_master tm on (TENANT_SAM_ID=gid_lead_code and TENANT_STATUS in (1,4) and GID_PRODUCT_CODE=TENANT_PRODUCT and GID_TENANT_ID=TENANT_ID and GID_SERVER_ID=SERVER_ID) " .
               "where gid_install_id=$install_id ) ";           
           execute_my_query($query_request_id);
           $request_id=mysqli_insert_id_wrapper();
           $post_addon_xml = "<REGISTERINFO>";
           $post_addon_xml .= "<request_id>$request_id</request_id>";
           $post_addon_xml .= "<action>addonreq</action>";
           $post_addon_xml .= "<sam_id>$lead_code</sam_id>";
           $post_addon_xml .= "<domin_name>$domin_name</domin_name>";
           $post_addon_xml .= "<tenant_id>$tenant_id</tenant_id>";
           $post_addon_xml .= "<customer_name><![CDATA[".$customer_name."]]></customer_name>";
           $post_addon_xml .= "<product_purchased>$base_product</product_purchased>";
           $post_addon_xml .= "<addons>$addon_xml_dlt</addons>";
           $post_addon_xml .= "</REGISTERINFO>";
           execute_my_query("replace into gft_webpos_gateway_request (GSR_REQUEST_ID,GSR_SERVER_ID,GSR_DATE, GSR_ORDER_NO, GSR_LEAD_CODE,GRD_REQUEST,GRD_PURPOSE) " .
               " values('$request_id',$server_id,now(),'$order_no','$lead_code', '".mysqli_real_escape_string_wrapper($post_addon_xml)."','addon_sync')");
           webposgateway_post($request_id);
       }
        
    }
}
/**
 * @param int $install_id
 * @param int $lead_code
 * @param string $root_order_no
 * @param string $purpose
 * @param string $action
 * @param string $new_email
 * 
 * @return int
 */
function send_to_sass_server($install_id,$lead_code,$root_order_no,$purpose,$action,$new_email=''){
	
	
	$request_id=0;
	$qsrpassword='';
	//$action='';
	
	/* get the Request id to be sent */
	$query_request_id="insert into gft_webpos_gateway_request (GSR_SERVER_ID,GSR_ORDER_NO,GSR_LEAD_CODE,GSR_DATE,GSR_NEW_ORDER_NO)(" .
			"select SERVER_ID,GID_ORDER_NO,GID_LEAD_CODE,now(),'$root_order_no' FROM gft_install_dtl_new  " .
			"join gft_tenant_master tm on (TENANT_SAM_ID=gid_lead_code and TENANT_STATUS in (1,4) and GID_PRODUCT_CODE=TENANT_PRODUCT and GID_TENANT_ID=TENANT_ID and GID_SERVER_ID=SERVER_ID) " .
			"where gid_install_id=$install_id ) ";
	
	$result_request_id=execute_my_query($query_request_id);
	if($result_request_id){
		$request_id=mysqli_insert_id_wrapper();
	}		
	
	$authority_name='';
	$door_no='';
	$block='';
	$street_no='';
	$street_name='';
	$location='';
	$area_name='';
	$landmark='';
	$city='';
	$pincode='';
	$state_name='';
	$country='';
	$extrainfo='';
	$created_date='';
	$buss_phno='';
	$mobile_no='';
	$resno='';
	$email='';
	$website='';
	$fax='';
	$vertical_id='';
	$vertical_name='';
	
	/* retrive the details sent while registration */	
	$customer_name='';
	$result_registor_dtl=execute_my_query("select GOD_REMARKS from gft_order_hdr where god_order_no='$root_order_no'");
	if($data_registor_dtl=mysqli_fetch_array($result_registor_dtl)){
			$registor_dtl_json=$data_registor_dtl['GOD_REMARKS'];
			$registor_dtl=json_decode($registor_dtl_json,true);
			if($registor_dtl){
				$vertical_name=(isset($registor_dtl['vertical'])?htmlentities(trim(get_vertical_name_for($registor_dtl['vertical']))):'');
				$customer_name=(isset($registor_dtl['shop_name'])?htmlentities(trim($registor_dtl['shop_name'])):'');
				$authority_name=(isset($registor_dtl['contact_name'])?htmlentities(trim($registor_dtl['contact_name'])):'');
				$city=(isset($registor_dtl['city_name'])?htmlentities(trim($registor_dtl['city_name'])):'');
				$pincode=(isset($registor_dtl['pincode'])?htmlentities(trim($registor_dtl['pincode'])):'');
				$mobile_no=(isset($registor_dtl['mob_no'])?htmlentities(trim($registor_dtl['mob_no'])):'');
				$country=(isset($registor_dtl['country'])?htmlentities(trim($registor_dtl['country'])):'');
				$vertical_id=(isset($registor_dtl['vertical'])?htmlentities(trim($registor_dtl['vertical'])):'');
				$state_name=(isset($registor_dtl['STATECODE'])?htmlentities(trim($registor_dtl['STATECODE'])):'');
				$email=(isset($registor_dtl['email'])?htmlentities(trim($registor_dtl['email'])):'');
				$extrainfo=(isset($registor_dtl['extrainfo'])?htmlentities(trim($registor_dtl['extrainfo'])):'');
			}
	}

	if($customer_name==''){
			$query="select GLH_LEAD_CODE,GLH_CUST_NAME,GLH_AUTHORITY_NAME,GLH_DOOR_APPARTMENT_NO,GLH_BLOCK_SOCEITY_NAME," .
					" GLH_STREET_DOOR_NO,GLH_CUST_STREETADDR1,GLH_CUST_STREETADDR2,GLH_LANDMARK,GLH_CUST_STATECODE,GLH_AREA_NAME,GLH_CUST_CITY,GLH_CUST_PINCODE," .
					" GLH_VERTICAL_CODE,GTM_VERTICAL_NAME,".
					" GLH_COUNTRY,ifnull(GLH_CREATED_DATE,GLH_DATE) as GLH_CREATED_DATE," .
					" group_concat(distinct if(gcc_contact_type=1,GCC_CONTACT_NO,'') SEPARATOR ' ') 'MOBILE'," .
					" group_concat(distinct if(gcc_contact_type=2,GCC_CONTACT_NO,'') SEPARATOR ' ') 'BUSSNO'," .
					" group_concat(distinct if(gcc_contact_type=3,GCC_CONTACT_NO,'') SEPARATOR ' ') 'RESNO'," .
					" group_concat(distinct if(gcc_contact_type=4,GCC_CONTACT_NO,'') SEPARATOR ' ') 'EMAIL'," .
					" group_concat(distinct if(gcc_contact_type=5,GCC_CONTACT_NO,'') SEPARATOR ' ') 'FAX'," .
					" group_concat(DISTINCT if(gcc_contact_type=6,GCC_CONTACT_NO,'') SEPARATOR ' ') 'WEBSITE' " .
					" from gft_lead_hdr a left join gft_customer_contact_dtl ccd on (GCC_LEAD_CODE=GLH_LEAD_CODE)" .
					" left join gft_vertical_master on (GTM_VERTICAL_CODE=GLH_VERTICAL_CODE)" .
					" where GLH_LEAD_CODE='$lead_code' group by glh_lead_code ORDER BY GLH_DATE DESC " ;
					
			$result_lead=execute_my_query($query);
			if($data_lead=mysqli_fetch_array($result_lead)){
				$customer_name=htmlentities(trim($data_lead['GLH_CUST_NAME']));
				$authority_name=htmlentities(trim($data_lead['GLH_AUTHORITY_NAME']));
				$door_no=htmlentities(trim($data_lead['GLH_DOOR_APPARTMENT_NO']));
				$block=htmlentities(trim($data_lead['GLH_BLOCK_SOCEITY_NAME']));
				$street_no=htmlentities(trim($data_lead['GLH_STREET_DOOR_NO']));
				$street_name=htmlentities(trim($data_lead['GLH_CUST_STREETADDR1']));
				$location=htmlentities(trim($data_lead['GLH_CUST_STREETADDR2']));
				$area_name=htmlentities(trim($data_lead['GLH_AREA_NAME']));	
				$landmark=htmlentities(trim($data_lead['GLH_LANDMARK']));
				$extrainfo='';
				$city=htmlentities(trim($data_lead['GLH_CUST_CITY'])); 
				$pincode=trim($data_lead['GLH_CUST_PINCODE']); 
				$state_name=htmlentities(trim($data_lead['GLH_CUST_STATECODE']));
				$country=htmlentities(trim($data_lead['GLH_COUNTRY']));
				$vertical_id=trim($data_lead['GLH_VERTICAL_CODE']);
				$vertical_name=trim($data_lead['GTM_VERTICAL_NAME']);
				$created_date=$data_lead['GLH_CREATED_DATE'];
				$mobile_no=trim($data_lead['MOBILE']);
				$buss_phno=trim($data_lead['BUSSNO']);
				$resno=trim($data_lead['RESNO']);
				$email=trim($data_lead['EMAIL']);
				$fax=trim($data_lead['FAX']);
				$website=trim($data_lead['WEBSITE']);
				$mobile_no = preg_replace("/[[:blank:]]+/"," ",$mobile_no);
				$email = preg_replace("/[[:blank:]]+/", " ", $email);
				$mobile_no=(string)str_replace(' ',',',$mobile_no);
				$buss_phno=(string)str_replace(' ',',',$buss_phno);
				$resno=(string)str_replace(' ',',',$resno);
				$email=(string)str_replace(' ',',',$email);
				$fax=(string)str_replace(' ',',',$fax);
				$website=(string)str_replace(' ',',',$website);
				$mobile_no_arr=explode(',',$mobile_no);
				$mobile_no=$mobile_no_arr[0];
				$email_arr=explode(',',$email);
				$email=$email_arr[0];
				$customer_name	=	str_replace("'", "", $customer_name);
			}
		}
		
		
	$query="select GID_INSTALL_ID ,concat(GID_PRODUCT_CODE,'-',GID_PRODUCT_SKEW) ROOT_PRODUCT," .
			" SERVER_ID,TENANT_ID,TENANT_DOMAIN_NAME,TENANT_STATUS,TENANT_CUSTOM_DOMIN,GSM_WPOS_ADDR,if((GPM_FREE_EDITION='Y' and GPM_SUBSCRIPTION_PERIOD=0), 'F',GPM_FREE_EDITION) GPM_FREE_EDITION," .
			" GID_LIC_PSKEW,GPM_SKEW_DESC,GID_ORDER_NO,GID_PRODUCT_CODE, GID_NO_CLIENTS ,GID_NO_BILLS,GID_NO_COMPANYS ,GID_NO_SMS," .
			" GSG_START_DATE, if((GPM_FREE_EDITION='Y' and GPM_SUBSCRIPTION_PERIOD=0), '0000-00-00',GID_VALIDITY_DATE) END_DATE, pm.GFT_SKEW_PROPERTY,  " .
			" if(apin.GAP_PRODUCT_SKEW like '%PSL','Service','') service_module," .
			" if(apin.GAP_PRODUCT_SKEW like '%ASL','Taly Import','') accounts_module," .
			" TENANT_SMS_PASSCODE,GSG_PASS_CODE,GSG_SMS_USERID, if(GID_EXPIRE_FOR=2,datediff(GID_VALIDITY_DATE,now()),0) no_of_days ," .
			" GOD_ORDER_DATE,pm.GPM_PRODUCT_TYPE " .
			" from gft_install_dtl_new " .
			" join gft_order_hdr oh on (god_order_no=gid_order_no and god_order_status='A')".
			" join gft_product_master pm on (gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew ) " .
			" join gft_tenant_master on (TENANT_SAM_ID=gid_lead_code and TENANT_STATUS in (1,4) and GID_PRODUCT_CODE=TENANT_PRODUCT and GID_TENANT_ID=TENANT_ID and GID_SERVER_ID=SERVER_ID)" .
			" join gft_server_master on (GSM_SERVER_ID=SERVER_ID)" .
			" join gft_sms_gateway_info on (GSG_LEAD_CODE=GID_LEAD_CODE and GID_STATUS='A' and GSG_ORDER_NO=GID_ORDER_NO ) " .
			" left join gft_addon_plugin_dtl apin on (GAP_INSTALL_ID=GID_INSTALL_ID)" .
			" where gid_status='A' and gid_install_id='$install_id' group by gid_install_id ";
			
	$result=execute_my_query($query);
	$data_p=mysqli_fetch_array($result);
	$domin_name=$data_p['TENANT_DOMAIN_NAME'].($data_p['TENANT_CUSTOM_DOMIN']!='1'?'.'.$data_p['GSM_WPOS_ADDR']:'')	;
	$license_type='SaaS';
	$res_get_pass	=	execute_my_query(" SELECT GPR_PASSWORD,GTM_VERTICAL_CODE,GTM_VERTICAL_NAME FROM gft_presignup_registration".
										 " left join gft_vertical_master on(GPR_VERTICAL_CODE=GTM_VERTICAL_CODE)".
										 " WHERE GPR_LEAD_CODE='$lead_code' AND GPR_PASSWORD!='' order by GPR_CREATED_DATE desc LIMIT 1");
	if($row_data=mysqli_fetch_array($res_get_pass)){
		$qsrpassword=$row_data['GPR_PASSWORD'];
		if($row_data['GTM_VERTICAL_CODE']!='' and $row_data['GTM_VERTICAL_NAME']!=''){
			$vertical_id=trim($row_data['GTM_VERTICAL_CODE']);
			$vertical_name=trim($row_data['GTM_VERTICAL_NAME']);
		}
	}
	if($data_p['END_DATE']=='0000-00-00'){
		$data_p['END_DATE']	=	date('Y-m-d');
	}
	$data_sent1='';
	/* $data_sent1="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";*/
	
	$qsrpassword	=	replaceSpecialCharecter($qsrpassword);
	$customer_name	=	replaceSpecialCharecter($customer_name);
	$authority_name	=	replaceSpecialCharecter($authority_name);
	$door_no		=	replaceSpecialCharecter($door_no);
	$block			=	replaceSpecialCharecter($block);
	$street_no		=	replaceSpecialCharecter($street_no);
	$street_name	=	replaceSpecialCharecter($street_name);
	$location		=	replaceSpecialCharecter($location);
	$area_name		=	replaceSpecialCharecter($area_name);
	$landmark		=	replaceSpecialCharecter($landmark);
	$city			=	replaceSpecialCharecter($city);
	$country		=	replaceSpecialCharecter($country);
	$pincode		=	replaceSpecialCharecter($pincode);
	$vertical_name	=	replaceSpecialCharecter($vertical_name);
	$email			=	replaceSpecialCharecter($email);
	$edition        =   ((int)$data_p['GPM_PRODUCT_TYPE']<=20?1:(int)$data_p['GPM_PRODUCT_TYPE']);
	if($new_email!=''){$email			=	replaceSpecialCharecter($new_email);}
	$extrainfo		=	replaceSpecialCharecter($extrainfo);
	$data_p['GPM_SKEW_DESC']=	replaceSpecialCharecter($data_p['GPM_SKEW_DESC']);
	$send_vertical_dtl	="";
	if($action=='new'){
		$send_vertical_dtl=	"<vertical_id>".$vertical_id."</vertical_id>".
				"<vertical_name><![CDATA[".$vertical_name."]]></vertical_name>";
	}
	
	$data_sent1="<REGISTERINFO>";
	$data_sent1.="<request_id>".$request_id."</request_id>" .
					"<action>".$action."</action>".
					"<sam_id>".$lead_code."</sam_id>" .
					"<domin_name>".$domin_name."</domin_name>" .
					"<tenant_id>".$data_p['TENANT_ID']."</tenant_id>" .
					"<tenant_status>".$data_p['TENANT_STATUS'] ."</tenant_status>".
					"<tenant_password>".$qsrpassword."</tenant_password>".
					"<is_custome_domain>".$data_p['TENANT_CUSTOM_DOMIN'] ."</is_custome_domain>" .
					"<customer_name><![CDATA[".$customer_name."]]></customer_name>".
					"<authority_name><![CDATA[".$authority_name."]]></authority_name>".
					"<door_appartment_no><![CDATA[".$door_no."]]></door_appartment_no>".
					"<block_soceity_name><![CDATA[".$block."]]></block_soceity_name>".
					"<street_name><![CDATA[".$street_no.$street_name."]]></street_name>".
					"<location><![CDATA[".$location."]]></location>".
					"<area_name><![CDATA[".$area_name."]]></area_name>".
					"<landmark><![CDATA[".$landmark ."]]></landmark>".
					"<city><![CDATA[".$city ."]]></city>".
					"<pincode>".$pincode."</pincode>".
					"<state_name><![CDATA[".$state_name."]]></state_name>".
					"<country><![CDATA[".$country."]]></country>".
					"<extrainfo><![CDATA[".$extrainfo."]]></extrainfo>".
					"<created_date>".$created_date."</created_date>".		  
					"<buss_phone>".$buss_phno."</buss_phone>".
					"<mobile>".$mobile_no."</mobile>".	
					"<res_phone>".$resno."</res_phone>".
					"<email>".$email."</email>"	.
					"<website>".$website."</website>".
					"<fax>".$fax."</fax>".
					"<purpose>".$purpose."</purpose>".
					"<is_free>".$data_p['GPM_FREE_EDITION']."</is_free>".
					"<plan_skew><![CDATA[".$data_p['GPM_SKEW_DESC']."]]></plan_skew>".
					"<product_edition>".$edition."</product_edition>".
					"<server_id>".$data_p['SERVER_ID']."</server_id>".
					"<sms_userid>".$lead_code. "</sms_userid>".
					"<sms_passcode><![CDATA[".$data_p['GSG_PASS_CODE']. "]]></sms_passcode>".
					$send_vertical_dtl.
					"<root_orderno>".$data_p['GID_ORDER_NO']."</root_orderno>" .
					"<product_purchased>".$data_p['ROOT_PRODUCT']."</product_purchased>" .
					"<license_type>".$license_type."</license_type>" .
					"<service_module>".$data_p['service_module']."</service_module>".
					"<tally_export_module>".$data_p['accounts_module']."</tally_export_module>".
					"<subscription_validity>".$data_p['END_DATE']." 00:00:00</subscription_validity>" .
					"<order_created_date>".$data_p['GOD_ORDER_DATE']." 00:00:00</order_created_date>".
					"<no_of_tills>".$data_p['GID_NO_CLIENTS']."</no_of_tills>" .
					"<no_of_bills>".$data_p['GID_NO_BILLS']."</no_of_bills>".
					"<no_of_sms>".$data_p['GID_NO_SMS']."</no_of_sms>".
					"<no_of_users>".$data_p['GID_NO_COMPANYS']."</no_of_users>";		
					
	 $data_sent1.="</REGISTERINFO>";			
	/* Removed  
	 * 
	 * "<previous_expiry_date>".$pre_subscription_expiry_date."</previous_expiry_date>".
	 * 	($top_up_order_no!=''?"<topup_order_no>".$top_up_order_no."</topup_order_no>":"").
		($saasplan_order_no!=''?"<subscription_orderno>".$order_no."</subscription_orderno>":"")
	 */				
	 
	 global $test_lic;
	 if($test_lic==true){
	 	print $data_sent1; 
	 }
	 $update_request_table="update gft_webpos_gateway_request set GRD_REQUEST='".mysqli_real_escape_string_wrapper($data_sent1)."' where GSR_REQUEST_ID=".$request_id ; 
	 execute_my_query( $update_request_table);	
	 
	 return $request_id;			 
				
	
}

/**
 * @param string $order_no
 * @param int $install_id
 * @param int $product_code
 * @param int $lead_code
 * @param string $root_order_no
 *
 * @return void
 */
function update_tills($order_no,$install_id,$product_code,$lead_code,$root_order_no){
	$curr_ord_qty=0;$curr_user_qty=0;
	$order_splict='';
	if($root_order_no==''){
		$install_details=get_root_order_no_install_id($lead_code,$product_code);
		$root_order_no=$install_details['GID_ORDER_NO'];
	}
	if($install_id==''){
		return;
	}
	$query_get_register	=	" select GOP_PRODUCT_SKEW,opd.GOP_QTY,opd.GOP_USEDQTY,opd.gop_order_no,GFT_SKEW_PROPERTY,oh.god_order_splict from gft_order_hdr oh ".
							" join gft_order_product_dtl opd on (oh.god_order_no=opd.gop_order_no ) ".
							" join gft_product_master pm on (opd.gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew) ".
							" where oh.god_order_status='A' and oh.god_order_no='$order_no' and opd.gop_product_code='$product_code'".
							" and GFT_SKEW_PROPERTY in (20,21)";
	$result_get_register	=	execute_my_query($query_get_register);
	if(mysqli_num_rows($result_get_register)>0){
		while($row=mysqli_fetch_array($result_get_register)){
			$pskew		=	$row['GOP_PRODUCT_SKEW'];
			$order_qty	=	$row['GOP_QTY'];
			$used_qty	=	$row['GOP_USEDQTY'];
			$gop_order_no=	$row['gop_order_no'];
			$skew_type	=	$row['GFT_SKEW_PROPERTY'];
			$order_splict=	$row['god_order_splict'];
			if($order_qty!=$used_qty){
				execute_my_query("update gft_order_hdr oh " .
						" join gft_order_product_dtl opd on (oh.god_order_no=opd.gop_order_no and ((gop_start_date<=date(now()) and gop_ass_end_date>=date(now())) or (gop_start_date<='0000-00-00' and gop_ass_end_date>='0000-00-00') ) )".
						" set opd.GOP_USEDQTY=opd.GOP_QTY,gop_ass_end_date='".get_expiry_date_install_id($install_id) ."',GOP_REFERENCE_ORDER_NO='".$root_order_no."' ".
						" where god_order_no='$gop_order_no' and god_order_splict=false and oh.god_order_status='A' and god_lead_code=$lead_code and opd.gop_product_code=$product_code and gop_product_skew='$pskew' ");
			}
			if($order_no==$gop_order_no){
				if($skew_type=='20'){
					$curr_ord_qty=$curr_ord_qty+$order_qty;
				}
				if($skew_type=='21'){
					$curr_user_qty=$curr_user_qty+$order_qty;
				}
			}
		}
		$sql_update_qry_client	=	"";
		if($order_no!='' and ($product_code=='601' or $product_code=='605') and $curr_ord_qty>0){
			$up_comp_query=" update gft_install_dtl_new ins " .
					" join gft_product_master pm on (gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew) " .
					" left join  gft_product_master_attributes pa on (GPMA_PRODUCT_CODE=gid_lic_pcode and GPMA_PRODUCT_SKEW=gid_lic_pskew AND GPMA_ATTRIBUTE=2)" .
					" set GID_NO_CLIENTS=GID_NO_CLIENTS+($curr_ord_qty) where GID_INSTALL_ID=$install_id and " .
					" gid_lead_code=$lead_code and gid_product_code=$product_code ";
			execute_my_query($up_comp_query);
		}
		if($order_no!='' and ($product_code=='601' or $product_code=='605') and $curr_user_qty>0){
			$up_comp_query=" update gft_install_dtl_new ins " .
					" join gft_product_master pm on (gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew) " .
					" left join  gft_product_master_attributes pa on (GPMA_PRODUCT_CODE=gid_lic_pcode and GPMA_PRODUCT_SKEW=gid_lic_pskew AND GPMA_ATTRIBUTE=2)" .
					" set GID_NO_COMPANYS=GID_NO_COMPANYS+($curr_user_qty) where GID_INSTALL_ID=$install_id and " .
					" gid_lead_code=$lead_code and gid_product_code=$product_code ";
			execute_my_query($up_comp_query);
		}
	}else{
		//If trial, update trial skew no of user and register
		$sql_check_installed_type	=	" select GID_ORDER_NO from gft_install_dtl_new ".
										" inner join gft_product_master on(GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW)  ".
										" where GID_INSTALL_ID=$install_id and gid_lead_code=$lead_code and gid_product_code=$product_code and GPM_FREE_EDITION='Y'";
		$result_check_installed_type	=	execute_my_query($sql_check_installed_type);
		if(mysqli_num_rows($result_check_installed_type)>0){
			$up_comp_query=" update gft_install_dtl_new ins " .
					" join gft_product_master pm on (gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew) " .
					" left join  gft_product_master_attributes pa on (GPMA_PRODUCT_CODE=gid_lic_pcode and GPMA_PRODUCT_SKEW=gid_lic_pskew AND GPMA_ATTRIBUTE=2)" .
					" set GID_NO_CLIENTS=GPM_CLIENTS,GID_NO_BILLS=(GPMA_VALUE*GPM_CLIENTS),GID_NO_COMPANYS=GPM_COMPANYS where GID_INSTALL_ID=$install_id and " .
					" gid_lead_code=$lead_code and gid_product_code=$product_code ";
			execute_my_query($up_comp_query);
		}
	}
	$cp_order_dtl_chk_with_reforder=" and (GCO_REFERENCE_ORDER_NO='".$root_order_no."' or isnull(GCO_REFERENCE_ORDER_NO) ) ";
	$gop_order_dtl_chk_with_reforder=" and (GOP_REFERENCE_ORDER_NO='".$root_order_no."' or isnull(GOP_REFERENCE_ORDER_NO) ) ";
	
	/* addon plugin */
	$query_od="select opd.GOP_REFERENCE_ORDER_NO, opd.GOP_PRODUCT_SKEW, opd.gop_order_no, sum(opd.GOP_QTY) order_qty, sum(opd.GOP_USEDQTY) used_qty  " .
			" from gft_order_hdr oh " .
			" join gft_order_product_dtl opd on (oh.god_order_no=opd.gop_order_no ) " .
			" left join gft_cp_order_dtl cpd on (opd.gop_order_no=cpd.gco_order_no and " .
			" opd.gop_product_code=cpd.gco_product_code and gop_product_skew=cpd.gco_skew and gco_cust_code=$lead_code $cp_order_dtl_chk_with_reforder) " .
			" join gft_product_master pm on (opd.gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew) " .
			" where oh.god_order_status='A' and ((god_order_splict and gco_cust_code=$lead_code) or (!god_order_splict and god_lead_code=$lead_code)) and " .
			" opd.gop_product_code=$product_code and GFT_SKEW_PROPERTY in (22) $gop_order_dtl_chk_with_reforder " .
			" group by opd.gop_product_code,opd.GOP_PRODUCT_SKEW " ;

	$ord_qty=0;$usd_qty=0;
	$result_od=execute_my_query($query_od);
	if($install_id!=''){
		execute_my_query("delete from gft_addon_plugin_dtl where GAP_INSTALL_ID=$install_id ");
	}
	if($result_od and mysqli_num_rows($result_od)>0){
		while($data_od=mysqli_fetch_array($result_od)){
			$skew=$data_od['GOP_PRODUCT_SKEW'];
			$ord_qty=$data_od['order_qty'];
			$usd_qty=$data_od['used_qty'];
			$gop_order_no=$data_od['gop_order_no'];
			if($data_od['order_qty']!=$data_od['used_qty']){
				if($order_splict){
					execute_my_query("update " .
							" gft_order_product_dtl opd join gft_cp_order_dtl cpd on (opd.gop_order_no=cpd.gco_order_no and opd.gop_product_code=cpd.gco_product_code and gop_product_skew=cpd.gco_skew and gco_cust_code=$lead_code)".
							" set GCO_USEDQTY=GCO_CUST_QTY  ,GCO_REFERENCE_ORDER_NO='".$root_order_no."' ".
							" where gop_order_no='$gop_order_no' and opd.gop_product_code=$product_code and gop_product_skew='$skew' ");
					$subQuery = " select sum(GCO_USEDQTY) from gft_cp_order_dtl where gco_order_no='$gop_order_no' ".
								" and gco_product_code=$product_code and GCO_SKEW='$skew'  group by gco_product_code,gco_skew";
					execute_my_query("update gft_order_product_dtl opd set opd.GOP_USEDQTY=($subQuery) ".
							" where gop_order_no='$gop_order_no' and opd.gop_product_code=$product_code and gop_product_skew='$skew'");
				}else{
					execute_my_query("update gft_order_product_dtl opd set opd.GOP_USEDQTY=opd.GOP_QTY ,GOP_REFERENCE_ORDER_NO='".$root_order_no."' ".
							" where gop_order_no='$gop_order_no' and  opd.gop_product_code=$product_code and gop_product_skew='$skew' ");
				}
			}
			$ins_addplugin="insert into gft_addon_plugin_dtl (GAP_INSTALL_ID,GAP_PRODUCT_CODE,GAP_PRODUCT_SKEW)" .
					"(SELECT GID_INSTALL_ID, GID_LIC_PCODE,'$skew' FROM gft_install_dtl_new " .
					" WHERE GID_INSTALL_ID=$install_id ) ";
			execute_my_query($ins_addplugin);

		}
	}
}

/**
 * @param string $order_no
 * @param int $lead_code
 * @param int $server_id
 * @param int $tenant_id
 * @param string $action
 * @param int $product_code
 * @param string $purpose
 * @param string $new_email
 * 
 * @return boolean 
 */
function webposgatewaycall($order_no,$lead_code,$server_id,$tenant_id,$action,$product_code,$purpose="Manual Update",$new_email=''){
	$customer_name=''; $authority_name='';$door_no='';$block='';$saasplan_skew='';$no_of_days=0;$exist_is_free='';
	$street_no=''; $street_name=''; $location='';	$area_name='';	$landmark='';
	$city=''; $pincode=''; $state_name=''; $country=''; $created_date=''; 
	$buss_phno=''; $mobile_no='';$resno='';$email='';$fax='';$website='';$passcode='';$sms_userid='';
	$request_id=1;$license_type='';$top_up_validity='';$subscription_expiry_date='';$top_up_order_no='';$top_up_order_no='';$saasplan_order_no='';
	$pre_subscription_expiry_date='';$domin_name='';$tenant_status='';$order_date='';$service_module='off';$accounts_module='off';
	$extrainfo='';$plan_skew='';$plan_name='';
	
	$install_id='';
	$root_order_no='';

	$result_root_ono=execute_my_query("SELECT GID_ORDER_NO,GID_INSTALL_ID FROM gft_install_dtl_new WHERE GID_LEAD_CODE=$lead_code AND " .
			"GID_LIC_PCODE=$product_code and gid_status='A'");
	if($data_root_order_no=mysqli_fetch_array($result_root_ono)){
		$root_order_no=$data_root_order_no['GID_ORDER_NO'];
		$install_id=$data_root_order_no['GID_INSTALL_ID'];
	}
	$no_of_bills=0;
	$no_of_sms=0;
	/* to update users and nodes  call below function update_tills*/
	update_tills($order_no,$install_id,$product_code,$lead_code,$root_order_no);
	global $test_lic;
  
    /* This below query returns rows iff Tenant status is under used i.e 1 */
	$query_p=" SELECT GID_ORDER_NO, GID_PRODUCT_SKEW, GID_FULLFILLMENT_NO, TENANT_DOMAIN_NAME,TENANT_ID,SERVER_ID," .
			" GSM_WPOS_ADDR, GID_NO_CLIENTS, GID_NO_COMPANYS, TENANT_CUSTOM_DOMIN,GID_NO_BILLS, " .
			" GSG_START_DATE, if((sskew.GPM_FREE_EDITION='Y' and sskew.GPM_SUBSCRIPTION_PERIOD=0), '0000-00-00',GSG_END_DATE) END_DATE, pm.GFT_SKEW_PROPERTY, TENANT_STATUS, " .
			" if(apin.GAP_PRODUCT_SKEW like '%PSL','Service','') service_module,GID_LIC_PSKEW, sskew.GPM_SKEW_DESC," .
			" if(apin.GAP_PRODUCT_SKEW like '%ASL','Taly Import','') accounts_module,TENANT_SMS_PASSCODE,GSG_PASS_CODE,GSG_SMS_USERID,sskew.GPM_FREE_EDITION, datediff(GID_VALIDITY_DATE,now()) no_of_days " .
			" FROM gft_install_dtl_new " .
			" join gft_product_master pm on (GID_PRODUCT_CODE=pm.gpm_product_code and GID_PRODUCT_SKEW=pm.gpm_product_skew) " .
			" join gft_product_master sskew on (GID_PRODUCT_CODE=sskew.gpm_product_code and GID_LIC_PSKEW=sskew.gpm_product_skew) " .
			" join gft_tenant_master on (TENANT_SAM_ID=gid_lead_code and TENANT_STATUS in (1) and GID_PRODUCT_CODE=TENANT_PRODUCT and GID_TENANT_ID=TENANT_ID and GID_SERVER_ID=SERVER_ID)" .
			" join gft_server_master on (GSM_SERVER_ID=SERVER_ID)" .
			" join gft_sms_gateway_info on (GSG_LEAD_CODE=GID_LEAD_CODE and GID_STATUS='A' and GSG_ORDER_NO=GID_ORDER_NO) " .
			" left join gft_addon_plugin_dtl apin on (GAP_INSTALL_ID=GID_INSTALL_ID)" .
			" WHERE GID_PRODUCT_CODE=$product_code AND GID_LEAD_CODE=$lead_code group by GAP_INSTALL_ID ";
	$result_p=execute_my_query($query_p);

	if($data_p=mysqli_fetch_array($result_p)){
		$root_order_no=$data_p['GID_ORDER_NO'];
		$root_product=$product_code.'-'.$data_p['GID_PRODUCT_SKEW'];
		$root_product_pos=$root_order_no.'0001'.$product_code.$data_p['GID_PRODUCT_SKEW'];
		$no_of_tills=$data_p['GID_NO_CLIENTS'];
		$no_of_users=$data_p['GID_NO_COMPANYS'];
		$license_type='SaaS';
		$subscription_start_date=$data_p['GSG_START_DATE'];
		$subscription_expiry_date=$data_p['END_DATE'];
		$fullfil_no=$data_p['GID_FULLFILLMENT_NO'];
		$domin_name=$data_p['TENANT_DOMAIN_NAME'].($data_p['TENANT_CUSTOM_DOMIN']!='1'?'.'.$data_p['GSM_WPOS_ADDR']:'');
		$tenant_status=$data_p['TENANT_STATUS'];
		$is_custome_domain=$data_p['TENANT_CUSTOM_DOMIN'];
		$service_module=($data_p['service_module']!=''?'on':'off');
		$accounts_module=($data_p['accounts_module']!=''?'on':'off');
		$passcode=trim($data_p['GSG_PASS_CODE']);
		$sms_userid=trim($data_p['GSG_SMS_USERID']);
		$plan_skew=$data_p['GID_LIC_PSKEW'];
		$plan_name=$data_p['GPM_SKEW_DESC'];
		$exist_is_free=$data_p['GPM_FREE_EDITION'];
		$no_of_days=$data_p['no_of_days'];
		$no_of_bills=$data_p['GID_NO_BILLS'];
				
	}
	
	if($order_no!='' and $lead_code!='' and ($action=='Recharge' or $action=='new') ){
		if($action=='Recharge' or $action=='new'){
			//check the bellow query for all conditions
			$query_p="select oh.god_order_date,oh.GOD_CREATED_DATE, opd.gop_product_code, " .
					" if(GFT_SKEW_PROPERTY=17,if(god_order_splict,gco_cust_qty,opd.GOP_QTY),'') topup, " .
					" if(GFT_SKEW_PROPERTY=17,opd.GOP_PRODUCT_SKEW,'') topup_skew, ".
					" if(GFT_SKEW_PROPERTY=18,opd.GOP_PRODUCT_SKEW,'') saasplan_skew, ".
					" if(GFT_SKEW_PROPERTY=18,if(god_order_splict,gco_cust_qty,opd.GOP_QTY),'')saas_plan, " .
					" if(GFT_SKEW_PROPERTY=18,GPM_SUBSCRIPTION_PERIOD,0) subscription_preiod, " .
					" sum(GOP_SELL_AMT) sell_amt, god_order_splict,GPM_FREE_EDITION " .
				" from gft_order_hdr oh " .
				" join gft_order_product_dtl opd on (oh.god_order_no=opd.gop_order_no)" .
				" left join gft_cp_order_dtl on (god_order_splict=1 and gco_order_no=gop_order_no and gco_product_code=gop_product_code and gco_skew = gop_product_skew and gco_cust_code=$lead_code)".
				" join gft_product_master pm on (opd.gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew) " .
				" where oh.god_order_status='A' and god_order_no='$order_no' and ((god_order_splict=0 and god_lead_code=$lead_code) or (god_order_splict=1 and gco_cust_code=$lead_code)) and opd.gop_product_code=$product_code " .
				" and GFT_SKEW_PROPERTY in (17,18) and GOP_USEDQTY in (0)  " . 
				" group by god_lead_code,god_order_no,opd.GOP_PRODUCT_CODE,gop_product_skew ";
				
				/* in the above query should be GOP_USEDQTY=0 */
			$result_p=execute_my_query($query_p);
			if($data_p=mysqli_fetch_array($result_p)){
				$order_date=trim($data_p['GOD_CREATED_DATE']);
				$receive_amount=trim($data_p['sell_amt']);
				$product_code=trim($data_p['gop_product_code']);
				$topup_skew=trim($data_p['topup_skew']);
				$saasplan_skew=trim($data_p['saasplan_skew']);
				$topup_qty=$data_p['topup'];
				$saas_plan=$data_p['saas_plan'];
				$subscription_validity=trim($data_p['subscription_preiod']);
				$order_splict=$data_p['god_order_splict'];
				$is_free=$data_p['GPM_FREE_EDITION'];
				
				if($saasplan_skew!=''){
					$saasplan_order_no=$order_no;
					/* if($exist_is_free=='N' and $no_of_days>0 and $plan_skew!=$saasplan_skew){
						$pre_subscription_expiry_date=$subscription_expiry_date;
					}else { */
						//echo " in else part";
						$pre_subscription_expiry_date=$subscription_expiry_date;
						//print " Prev expiry date ".$pre_subscription_expiry_date;
						/* renewed plan - validity is $subscription_validity */
						$subscription_expiry_date=date('Y-m-d',mktime('0','0','0',date('m'),date('d')+($saas_plan*$subscription_validity),date('Y')));
						//strcmp(date('Y-m-d'),$pre_subscription_expiry_date)> 0
						if($pre_subscription_expiry_date!='' and $pre_subscription_expiry_date!='0000-00-00' and $pre_subscription_expiry_date!=null and $pre_subscription_expiry_date> date('Y-m-d') ){
							$datea=explode('-',$pre_subscription_expiry_date);
							$subscription_expiry_date=date('Y-m-d',mktime('0','0','0',$datea[1],$datea[2]+($saas_plan*$subscription_validity),$datea[0]));
							//echo "Calc new expiry date";
						}
					//}
					
					if($action!='new'){
					$query_gateway_info="update gft_sms_gateway_info set GSG_END_DATE='$subscription_expiry_date', GSG_SAAS_PLAN='$saasplan_skew' where GSG_LEAD_CODE=$lead_code and GSG_PRODUCT_CODE=$product_code "; 
					$result_lic=execute_my_query($query_gateway_info);
					if($tenant_status!=1 ){
						$table_name='gft_tenant_master';
						$table_key_arr=array();
						$table_key_arr['TENANT_ID']=$tenant_id;
						$table_key_arr['SERVER_ID']=$server_id;
						$update_column=array();
						$update_column['TENANT_STATUS']=1;
						$table_column_iff_update['GTM_UPDATED_DATE']=date('Y-m-d');
						array_update_tables_common($update_column,$table_name,$table_key_arr,null,9999,$remarks=null,
						$table_column_iff_update);
						
					}
					//To send mail if customer renewal form trial to subscription(Trupos and Servquick)
					if($product_code!='601' and $product_code!='605'){
						if($exist_is_free=='Y' and $is_free=='N'){
							$cust_dtl=customerContactDetail($lead_code);
							$mail_content_config_cust=array(
									'Customer_Id' => array($lead_code),
									'Customer_Name'=>array($cust_dtl['cust_name']));
							send_formatted_mail_content($mail_content_config_cust,37,193,null,array($lead_code)); // Mail Send to Customer
						}						
					}
					$update_install_dtl="update gft_install_dtl_new set GID_EXPIRE_FOR=2,GID_VALIDITY_DATE='$subscription_expiry_date', GID_LIC_PSKEW='$saasplan_skew' where gid_lead_code=$lead_code and gid_product_code=$product_code and gid_status='A' ";
					$result_lic=execute_my_query($update_install_dtl);
					/* call update tills again to update the no of bills */
					if($product_code!='601' and $product_code!='605'){
						update_tills($order_no,$install_id,$product_code,$lead_code,$root_order_no);
					}
					if($saasplan_skew!=$plan_skew and ($product_code=='601' or $product_code=='605')){												
						$res_skew_dtl	=	execute_my_query("	select (t2.GPM_CLIENTS-t1.GPM_CLIENTS) GPM_CLIENTS,(t2.GPM_COMPANYS-t2.GPM_COMPANYS) GPM_COMPANYS from gft_product_master t1 ".
															 "	inner join gft_product_master t2 on(1) ".
															 "	where t1.GPM_PRODUCT_CODE=$product_code and t2.GPM_PRODUCT_CODE=$product_code and (t1.GPM_PRODUCT_SKEW='$plan_skew' and t2.GPM_PRODUCT_SKEW='$saasplan_skew')");
						if(mysqli_num_rows($res_skew_dtl)==1 and $row_ls=mysqli_fetch_array($res_skew_dtl)){
								$qry_con	=	"";
								$qry_con	.=	" GID_NO_CLIENTS=GID_NO_CLIENTS+(".$row_ls['GPM_CLIENTS'].") ";
								$qry_con	.=	",GID_NO_COMPANYS=GID_NO_COMPANYS+(".$row_ls['GPM_COMPANYS'].")";
									execute_my_query("update gft_install_dtl_new set  $qry_con where gid_lead_code=$lead_code and gid_product_code=$product_code and gid_status='A' ");
						}
					}
					}				
				
				}  
				if($topup_skew!=''){
					$query_sms_count=" select GPMA_VALUE from gft_product_master_attributes where GPMA_PRODUCT_CODE=$product_code and GPMA_PRODUCT_SKEW='$topup_skew'  and GPMA_ATTRIBUTE=1 "; 
					$result_sms_count=execute_my_query($query_sms_count);
					if($data_sms_count=mysqli_fetch_array($result_sms_count)){
						$no_of_sms+=$data_sms_count['GPMA_VALUE']*$saas_plan;
					}
					$top_up_order_no=$order_no;
				}
				if($order_splict==1){
					execute_my_query("update " .
						" gft_order_product_dtl opd join gft_cp_order_dtl cpd on (opd.gop_order_no=cpd.gco_order_no and opd.gop_product_code=cpd.gco_product_code and gop_product_skew=cpd.gco_skew and gco_cust_code=$lead_code)". 
						" join gft_product_master pm on (opd.gop_product_code=pm.gpm_product_code and gop_product_skew=pm.gpm_product_skew) " .
						" set gco_start_date='$pre_subscription_expiry_date', gco_end_date='$subscription_expiry_date' " .
						" where gop_order_no='$order_no' and opd.gop_product_code=$product_code ");
					
					execute_my_query(" update gft_order_product_dtl opd set opd.GOP_USEDQTY=(select sum(GCO_USEDQTY) from gft_cp_order_dtl where gco_order_no=gop_order_no and opd.gop_product_code=gco_product_code and gco_skew=gop_product_skew group by gco_product_code,gco_skew ) " .
						" where gop_order_no='$order_no' and opd.gop_product_code=$product_code ");
				}else{
					execute_my_query( "update gft_order_product_dtl set GOP_USEDQTY=gop_qty, GOP_CP_USEDQTY=gop_qty, gop_start_date='$pre_subscription_expiry_date', gop_ass_end_date='$subscription_expiry_date'" .
						" WHERE GOP_ORDER_NO='$order_no' AND GOP_PRODUCT_CODE=$product_code ");
				}
			}
			
			
			if($passcode=='' and $sms_userid=='' and $action=='new'){
			$passcode=generatePassword(8);
			$sms_userid=$lead_code; //trim($email);
			$gateway_update=" update gft_sms_gateway_info set GSG_PASS_CODE='$passcode',GSG_SMS_USERID='$sms_userid'  where GSG_LEAD_CODE=$lead_code and GSG_PRODUCT_CODE=$product_code ";
			}
			
			if($product_code==601 or $product_code==605){
				if($test_lic){ return true; }
				$request_id=send_to_sass_server($install_id,$lead_code,$order_no,$purpose,$action,$new_email);
				$send_status=webposgateway_post($request_id);				
				if($action!='new' && $send_status==true){
					send_status_update_mail($tenant_id,$server_id,$product_code,$action,$order_no);
				}else if($send_status==false){
					
					$msg=" Order No : $order_no, Customer Id :$lead_code \nCustomer Name: ".$customer_name." \n Email: $email \n Domain Name: $domin_name \n\n " .
							"Please Check the saas Gateway process report for the above mentioned request and re send it again ";  
					
					send_mail_from_sam($category=37,'true-pos@gofrugal.com','true-pos@gofrugal.com','SaaS Gateway Process Failed',$msg,$attachment_file_tosend=null,
					$cc=null,false,'true-pos@gofrugal.com',$from_page=null,$user_info_needed=false);
					
				}
				return $send_status;
			}
			
		}
	    return true;
	}/* if orderno  exists */
	else if($action=='status_change' || $action=='ReadOnly' || $action=='Dropped' || $action=='Uninstall'){
		  
		    $query_p=" SELECT GID_INSTALL_ID,GID_ORDER_NO, GID_PRODUCT_CODE, GID_PRODUCT_SKEW, GID_STATUS, GID_LEAD_CODE," .
		    		" GID_FULLFILLMENT_NO,TENANT_ID,TENANT_DOMAIN_NAME,TENANT_CUSTOM_DOMIN,GID_HEAD_OF_FAMILY,GSM_WPOS_ADDR ," .
		    		" TENANT_STATUS " .
					" FROM gft_install_dtl_new " .
					" join gft_tenant_master on (TENANT_SAM_ID=gid_lead_code and GID_PRODUCT_CODE=TENANT_PRODUCT and  " .
					"TENANT_ID=$tenant_id and SERVER_ID=$server_id  and GID_TENANT_ID=TENANT_ID and GID_SERVER_ID=SERVER_ID) " .
					" join gft_server_master on (GSM_SERVER_ID=SERVER_ID ) " .
					" WHERE GID_PRODUCT_CODE=$product_code AND GID_LEAD_CODE=$lead_code and gid_status='A' ";
					
			//print $query_p;		
			$result_p=execute_my_query($query_p);
			while($data_p=mysqli_fetch_array($result_p)){
				$install_id=$data_p['GID_INSTALL_ID'];
				$root_order_no=$data_p['GID_ORDER_NO'];
				$install_status=$data_p['GID_STATUS'];
				$full_fill_no=$data_p['GID_FULLFILLMENT_NO'];
				$root_product=$data_p['GID_PRODUCT_CODE'].'-'.$data_p['GID_PRODUCT_SKEW'];
				$root_product_skew=$data_p['GID_PRODUCT_SKEW'];
				$tenant_id=$data_p['TENANT_ID'];
				$is_custome_domain=$data_p['TENANT_CUSTOM_DOMIN'];
				$head_of_family=$data_p['GID_HEAD_OF_FAMILY'];
				$tenant_status=$data_p['TENANT_STATUS'];
				$GID_LEAD_CODE=$data_p['GID_LEAD_CODE'];
				$reason=(isset($_POST['reason'])?mysqli_real_escape_string_wrapper($_POST['reason']):'');
				if($tenant_status==5  || $action=='Dropped' ){ /*dropped */
					$tenant_status=5;
					$domin_name=($data_p['TENANT_CUSTOM_DOMIN']!='1'?$lead_code.'_'.$tenant_id.'_'.$data_p['TENANT_DOMAIN_NAME'].'.'.$data_p['GSM_WPOS_ADDR']:$data_p['TENANT_DOMAIN_NAME']);
					$query="update gft_tenant_master,gft_install_dtl_new set TENANT_STATUS='$tenant_status', TENANT_DOMAIN_NAME='$domin_name',GID_STATUS='U' ,GTM_UPDATED_DATE=date(now())  " .
						" where TENANT_SAM_ID=gid_lead_code and GID_PRODUCT_CODE=TENANT_PRODUCT and TENANT_ID=$tenant_id and GID_INSTALL_ID=$install_id " .
						" and GID_TENANT_ID=TENANT_ID and GID_SERVER_ID=SERVER_ID ";
					execute_my_query($query);	
					//echo "Dropped Query updated  ".$query;	
				
				}else if($tenant_status==4 || $action=='ReadOnly' ){ /*readonly */
					$tenant_status=4;
					//$read_only_period=get_samee_const('TRUEPOS_READ_ONLY_PERIOD');
					
					$query ="update gft_tenant_master set TENANT_STATUS='$tenant_status',GTM_UPDATED_DATE=date(now())  " .
					" where TENANT_SAM_ID=$lead_code and TENANT_ID=$tenant_id ";
					execute_my_query($query);	
					//echo "Dropped Query updated  ".$query;
		
				}else if($tenant_status==2 || $action=='Uninstall'){
					$tenant_status=2;
					if($install_id==''){ 
						return false; 
					}
					execute_my_query( "update gft_install_dtl_new, gft_order_hdr set GID_STATUS='U', GOD_ORDER_STATUS='C'  " .
						" where gid_order_no=god_order_no and gid_install_id=$install_id  ");
						
			    	execute_my_query( "update gft_tenant_master,gft_install_dtl_new, gft_order_hdr set TENANT_STATUS='2' ,GID_STATUS='U', GOD_ORDER_STATUS='C' ,TENANT_DOMAIN_NAME=concat('Wepbos_',$tenant_id),GTM_UPDATED_DATE=date(now())  " .
						" where TENANT_SAM_ID=gid_lead_code and GID_PRODUCT_CODE=TENANT_PRODUCT and gid_order_no=god_order_no and TENANT_ID=$tenant_id and " .
						" GID_INSTALL_ID=$install_id and GID_TENANT_ID=TENANT_ID and GID_SERVER_ID=SERVER_ID ");
			
						
					$query1="replace into gft_uninstall_dtl (GUD_INSTALL_REFF, GUD_UNINSTALL_DATE,GUD_REASON_CODE,GUD_NOTE,GUD_ACTIVE_UNINSTALL,gud_executive_id,gud_approved_by,GUD_REPORTED_ON) " .
					" values ('$install_id',date(now()),'7','$reason', 'U',".SALES_DUMMY_ID.",'1',date(now()))";
					execute_my_query($query1);
					update_call_preferance($GID_LEAD_CODE);	
				}
				$send_status=send_saas_server_tenant_status($tenant_id,$server_id,$install_id,$action);
		    	return $send_status;
				
			}
			
	}/* end of if action as status change */
	return false; 
}/* end of function */
?>
