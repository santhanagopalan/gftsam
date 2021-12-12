<?php 
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__.'/../lic_util.php');
require_once(__DIR__.'/../chat_util.php');

/**
 * @param string $cust_id
 * @param int $mobile_no
 *
 * @return string
 */
function get_pending_feedback_conversation_id($cust_id,$mobile_no){
	global $domain_name;
	$start_date = "2017-09-09 00:00:00";
	$date_con = ($domain_name=="sam.gofrugal.com"?" AND cd.created_date>'$start_date'":"");
	if($mobile_no!='0' && $mobile_no!=""){
		$date_con .= " AND RIGHT(cd.contact_number, 10)='".substr($mobile_no, -10)."'";
	}
	$chat_id = "";
	$result_con = execute_my_query(" select cd.id new_id, cf.conversation_id feedback_id,cd.review_status,cd.chat_status from chatbot.conversation_dtl cd ".
			" LEFT JOIN chatbot.customer_feedback cf ON(cd.id=cf.chat_id) ".
			" where cust_id=$cust_id $date_con order by cd.id desc limit 1,1");
	if($row_con=mysqli_fetch_array($result_con)){//Check feedback status for previous status
		if($row_con['feedback_id']=="" && $row_con['chat_status']!='1'){
			return  $row_con['new_id'];
		}
	}
	$result_con = execute_my_query(" select cd.id new_id, cf.conversation_id feedback_id,cd.review_status,cd.chat_status from chatbot.conversation_dtl cd ".
			" LEFT JOIN chatbot.customer_feedback cf ON(cd.id=cf.chat_id) ".
			" where cust_id=$cust_id $date_con order by cd.id desc limit 1");
	if($row_con=mysqli_fetch_array($result_con)){
		if($row_con['feedback_id']==""){
			$chat_id = $row_con['new_id'];
			if($row_con['review_status']=='2'){//If wrap-up is completed, ask feedback
				return $chat_id;
			}
			if($row_con['chat_status']=='1'){//If status is initiated, don't ask feedback
				return "";
			}
			$trans_result = execute_my_query("select count(id) tot_count, max(time_stamp) last_date from chatbot.transcripts where chat_id=$chat_id");
			$trans_row = mysqli_fetch_array($trans_result);			
			if((int)$trans_row['tot_count']>0){
				$buffer_minutes=(int)get_samee_const('CHATBOT_BUFFER_MINUTES_FOR_FORCE_FEEDBACK');
				$last_date = $trans_row['last_date'];
				$date_con = date("Y-m-d H:i:s", strtotime("-$buffer_minutes min"));
				if(strtotime($last_date)>strtotime($date_con)){
					return "";
				}				
			}
		}
	}
	return $chat_id;
}
/**
 * @param int $cust_id
 *
 * @return int
 */
function get_customer($cust_id){
	$pref_lang = 1;
	$lang_que = " select GPM_PREFERRED_LANGUAGE from gft_lead_hdr ".
			" left join gft_political_map_master on (GPM_MAP_NAME=GLH_CUST_STATECODE) ".
			" where GLH_LEAD_CODE='$cust_id' ";
	$lang_res = execute_my_query($lang_que);
	if($data1 = mysqli_fetch_array($lang_res)){
		$pref_lang = ((int)$data1['GPM_PREFERRED_LANGUAGE']==0?1:(int)$data1['GPM_PREFERRED_LANGUAGE']);
	}
	return $pref_lang;
}
/**
 * @param int $support_group_id
 * @param int $cust_id
 * @param string[string] $product_version_array
 *
 * @return string[string][string]
 */
function get_chatbot_welcome_screen_content($support_group_id,$cust_id,$product_version_array){
	$pref_lang = get_customer($cust_id);
	$response_arr = array();
	$customer_version = "";
	$product_group = "";
	if($support_group_id==6 && (isset($product_version_array['300-03.0']) && $product_version_array['300-03.0']=="")){
		$corporate_id = get_corporate_customer_for_client($cust_id);
		$res_version = execute_my_query("select GPV_VERSION as GID_CURRENT_VERSION from gft_install_dtl_new ".
				"left join gft_product_version_master on (GID_STATUS='A' AND GPV_PRODUCT_CODE=GID_LIC_PCODE and  (GID_CURRENT_VERSION=GPV_VERSION or GID_CURRENT_VERSION=REPLACE(concat(GPV_MAJOR_VERSION,GPV_MINOR_VERSION,GPV_PATCH_VERSION,GPV_EXE_VERSION),'_','.') or GPV_VERSION=concat('3.0.0.RC',GID_CURRENT_VERSION))  )".
				"where gid_lead_code=$corporate_id AND GID_PRODUCT_CODE=300 AND GID_STATUS='A' AND GID_VALIDITY_DATE>NOW()");
		if($row_version=mysqli_fetch_array($res_version)){
			$product_version_array['300-03.0'] = $row_version['GID_CURRENT_VERSION'];
		}
		if($corporate_id!=$cust_id){
			if((isset($product_version_array['500-07.0']) && $product_version_array['500-07.0']!="")){
				$support_group_id = 5;
			}else if((isset($product_version_array['500-06.5']) && $product_version_array['500-06.5']!="")){
				$support_group_id = 4;
			}else if((isset($product_version_array['200-06.0']) && $product_version_array['200-06.0']!="")){
				$support_group_id = 3;
			}
		}
	}
	if(in_array($support_group_id, array(3,4,5,6,20,21,22,23))){//Only RPOS 7 and 6, DE and HQ
		$product_group = get_single_value_from_single_query("GVG_PRODUCT", "select GVG_PRODUCT from gft_voicenap_group where GVG_SUPPORT_GROUP=$support_group_id order by GVG_PREFER_ORDER ASC limit 1");
		if($product_group!=""){
			$customer_version = isset($product_version_array[$product_group])?$product_version_array[$product_group]:"";
		}
	}
	$sql_content = 	" select GCE_WELCOME_CONTENT,GCW_VERSION_SPECIFIC_CONTENT,GCW_CONTENT_LANGUAGE,".
			" GCW_PRODUCT_VERSION,GLM_LANGUAGE,GCW_IS_CONFIRMATION_NEEDED, GCW_REGION_IDS,".
			" GCW_VERTICAL_IDS, GCW_PRODUCT_EDITION  from gft_chatbot_welcome_content_master ".
			" join gft_language_master ON(GCW_CONTENT_LANGUAGE=GLM_ID) ".
			" where  GCW_SUPPORT_GROUP_ID=$support_group_id AND GCW_STATUS=1 ";
	$res_content = execute_my_query($sql_content);
	while($row=mysqli_fetch_array($res_content)){
		$single_row = array();
		$content_product_lang 		= (int)$row['GCW_CONTENT_LANGUAGE'];
		$content_product_version 	= $row['GCW_PRODUCT_VERSION'];
		$single_row['tabName'] 		= $row['GLM_LANGUAGE'];
		$single_row['tabContent'] 	= $row['GCE_WELCOME_CONTENT'];
		$single_row['isDefaultTab'] = ($content_product_lang==$pref_lang?true:false);
		$single_row['isConfirmationNeeded']=false;
		if($content_product_version!="" && $customer_version!="" && $row['GCW_VERSION_SPECIFIC_CONTENT']!=""){
			if(!(version_compare($customer_version, $content_product_version) >= 0)){
				$single_row['tabContent'] 	= $row['GCW_VERSION_SPECIFIC_CONTENT'];
				if($row['GCW_IS_CONFIRMATION_NEEDED']=='Y'){
					$single_row['isConfirmationNeeded']=true;
				}				
			}
		}else if(($row['GCW_REGION_IDS']!="" || $row['GCW_VERTICAL_IDS']!="" || $row['GCW_PRODUCT_EDITION']!="") && $product_group!=""){
			$single_row['tabContent'] 	= "";
			$region_ids = explode(',', $row['GCW_REGION_IDS']);
			$vertical_ids = explode(',', $row['GCW_VERTICAL_IDS']);
			$edition_ids = explode(',', $row['GCW_PRODUCT_EDITION']);
			$result_cust_dtl = execute_my_query("select GLH_VERTICAL_CODE,region_id, GPM_PRODUCT_TYPE from gft_lead_hdr 
				LEFT JOIN b_map_view ON(GLH_TERRITORY_ID=terr_id)
				LEFT JOIN gft_install_dtl_new ON( GID_STATUS!='U' AND GID_LEAD_CODE=GLH_LEAD_CODE AND concat(GID_LIC_PCODE,'-',substring(GID_LIC_PSKEW,1,4))='$product_group')
				LEFT JOIN gft_product_master ON(GPM_PRODUCT_CODE=GID_LIC_PCODE AND GPM_PRODUCT_SKEW=GID_LIC_PSKEW)
				where glh_lead_code='$cust_id' AND GPM_FREE_EDITION='N' AND GLH_TERRITORY_ID!=100 limit 1");
			if($row_cust=mysqli_fetch_array($result_cust_dtl)){
				$cust_vertical 	= (int)$row_cust['GLH_VERTICAL_CODE'];
				$cust_region 	= (int)$row_cust['region_id'];
				$cust_edition 	= (int)$row_cust['GPM_PRODUCT_TYPE'];
				if(($row['GCW_REGION_IDS']=="" || in_array($cust_region, $region_ids)) &&
						($row['GCW_VERTICAL_IDS']=="" || in_array($cust_vertical, $vertical_ids)) &&
						($row['GCW_PRODUCT_EDITION']=="" || in_array($cust_edition, $edition_ids))){
					$single_row['tabContent'] 	= $row['GCE_WELCOME_CONTENT'];
					
				}
			}
		}else if($row['GCW_VERSION_SPECIFIC_CONTENT']=="" && $row['GCE_WELCOME_CONTENT']!=""){
			$single_row['tabContent'] 	= $row['GCE_WELCOME_CONTENT'];
		}
		if($single_row['tabContent']!="" ){
			$response_arr[] = $single_row;
		}
	}
	return $response_arr;
}
/**
 * @param string[string] $dec_arr
 *
 * @return string[string]
 */
function get_chat_customer_details($dec_arr){
	$return_arr = /*. (string[string]) .*/array();
	foreach ($dec_arr as $key=>$value){
		$cust_arr = explode("=", $value);
		if(isset($cust_arr[0]) && $cust_arr[0]=="custid")
			$return_arr['prod_cust_id'] = isset($cust_arr[1])?(int)$cust_arr[1]:0;
		if(isset($cust_arr[0]) && $cust_arr[0]=="mygofrugal_user_id")
			$return_arr['mygofrugal_user_id'] = isset($cust_arr[1])?(int)$cust_arr[1]:0;
		if(isset($cust_arr[0]) && $cust_arr[0]=="mydelight_user_id")
			$return_arr['mydelight_user_id'] = isset($cust_arr[1])?(int)$cust_arr[1]:0;
		if(isset($cust_arr[0]) && $cust_arr[0]=="pos_userid")
			$return_arr['pos_userid'] = isset($cust_arr[1])?(int)$cust_arr[1]:0;
		if(isset($cust_arr[0]) && $cust_arr[0]=="support_group_id")
		    $return_arr['support_group_id'] = isset($cust_arr[1])?(int)$cust_arr[1]:0;
		
	}
	return $return_arr;
}
/**
 * @param string $cust_id
 * @param string $pos_id
 * @param string $type
 *
 * @return string
 */
function get_pos_contact($cust_id,$pos_id,$type){
	$return_val = "";
	$sql_query = 	" select  GCC_CONTACT_NO,GCC_CONTACT_NAME, gcc_contact_type from gft_customer_contact_dtl ".
			" inner join gft_pos_users on(gcc_id=GPU_CONTACT_ID) ".
			" where  GPU_USER_ID='$pos_id' and GCC_LEAD_CODE='$cust_id' AND gcc_contact_type=$type limit 1";
	$result_con	=	execute_my_query($sql_query);
	if((mysqli_num_rows($result_con)>0) && ($row_con=mysqli_fetch_array($result_con))){
		return (string)$row_con['GCC_CONTACT_NO'];
	}
	return $return_val;		
}
/**
 * @param string $message
 * 
 * @return void
 */
function return_routing_error_response($message){
    $error = array();
    $error_code = 200;
    header('X-PHP-Response-Code: '.$error_code, true, $error_code);
    $error['status']="error";
    $error['message']="$message";
    echo json_encode($error);exit;
}
/**
 * @param boolean $is_employee_partner_mobile_no
 * @param string $installed_product
 * @param boolean $asa_status
 * @param boolean $non_asa_status
 *
 * @return string[string]
 */
function get_uber_product_list($is_employee_partner_mobile_no, $installed_product,$asa_status, $non_asa_status,$vertical_id){
    $uber_product_data_list = array();
    $sql_uber_product = execute_my_query("select topic_id,if(topic_name='nonasa','Non-ASA',product_name)  product_name, ". 
        " product_code, brand_id, product_name_alias, GBP_BRAND_ID from gft_chatbot_group_mapping_master ".
        " LEFT JOIN gft_chatbot_topic_master on(GCM_PRODUCT=product_code) ".
        " left join gft_brand_product_mapping ON(GCM_PRODUCT=GBP_PRODUCT AND GBP_EDITION=0 AND GBP_VERTICAL='$vertical_id' AND brand_id=GBP_BRAND_ID) ".
        " WHERE GCM_STATUS=1 AND topic_id!=1 ".
        (($is_employee_partner_mobile_no)?"":" AND GCM_PRODUCT in('0'".($installed_product!=''?",$installed_product":"").")").
        (($is_employee_partner_mobile_no)?"":" AND GCM_ASA_STATUS in(0".(($asa_status)?",1":(($non_asa_status)?",2":"")).")").
        " GROUP BY topic_id ORDER BY GBP_BRAND_ID DESC");       
        if((mysqli_num_rows($sql_uber_product)==0)){
            $uber_product_data['product_name'] = "New Visitor";
            $uber_product_data['product_id'] = "9";
            $uber_product_data['product_code'] = "0";
            $uber_product_data['brand_id'] = "0";
            $uber_product_data_list[] = $uber_product_data;
        }
        $unique_product = array();
        while($row_product=mysqli_fetch_array($sql_uber_product)){
            if((!in_array($row_product['product_code'], $unique_product)) || $row_product['product_code']==0){
                $uber_product_data['product_name'] = $row_product['product_name'];
                $uber_product_data['product_id'] = $row_product['topic_id'];
                $uber_product_data['product_code'] = $row_product['product_code'];
                $uber_product_data['brand_id'] = $row_product['brand_id'];
                $uber_product_data_list[] = $uber_product_data;
                if($row_product['product_code']!=0)
                    $unique_product[] = $row_product['product_code'];
            }            
        }
        return $uber_product_data_list;
}
header('Content-Type: application/json');
addAccessControlAllowOrigin();
$data = $_REQUEST;
$domain_name=$_SERVER['HTTP_HOST'];
$mobile_no  	=	isset($data['mobile_no'])?/*. (string) .*/mysqli_real_escape_string_wrapper(trim($data['mobile_no'])):'';
$product_data	=	isset($data['product_data'])?(string)$data['product_data']:'';
$generate_otp	=	isset($data['generate_otp'])?(int)$data['generate_otp']:0;
$country_code 	= 	(isset($data['country_code'])?mysqli_real_escape_string_wrapper($data['country_code']):'');
$email          =   isset($data['email'])?mysqli_real_escape_string_wrapper($data['email']):'';
$source_from    =   isset($data['source_from'])?(string)($data['source_from']):'';
$sub_lead_code      =   isset($data['lead_code'])?(string)($data['lead_code']):'';
$is_sub_routing =   isset($data['sub_routing'])?(boolean)($data['sub_routing']):false;
$brand_id       =  isset($data['brand_id'])?(int)($data['brand_id']):0;
$product_id     =  isset($data['product_id'])?(string)($data['product_id']):'';

$prod_cust_id = 0;
$pos_user_mobile = '';
$pos_user_name = '';
$pos_user_id = 0;
$mygofrugal_user_id = 0;
$mydelight_user_id = 0;
$sub_routing_group_id =0;
$cname = '';
$decrypted_data = lic_decrypt($product_data, $secret);
$data['decrypted_product_data'] = $decrypted_data; //log purpose
$dec_arr = explode("&", $decrypted_data);
//1. Chat initation from product/mydelight/gofrugal app
if($product_data!=""){	
	if(isset($dec_arr[0])){		
		$cust_arr 			= get_chat_customer_details($dec_arr);		
		$prod_cust_id 		= isset($cust_arr['prod_cust_id'])?(int)$cust_arr['prod_cust_id']:0;
		$mygofrugal_user_id = isset($cust_arr['mygofrugal_user_id'])?(int)$cust_arr['mygofrugal_user_id']:0;
		$mydelight_user_id 	= isset($cust_arr['mydelight_user_id'])?(int)$cust_arr['mydelight_user_id']:0;
		$pos_user_id 		= isset($cust_arr['pos_userid'])?(int)$cust_arr['pos_userid']:0;
		$sub_routing_group_id = isset($cust_arr['support_group_id'])?(int)$cust_arr['support_group_id']:0;
	}
	if($prod_cust_id==0 && $decrypted_data!=""){
		$cust_dtl	=	/*. (string[string]) .*/json_decode($decrypted_data,true);
		$pos_user_id        = isset($cust_dtl['pos_userid'])?(int)$cust_dtl['pos_userid']:0;
		$prod_cust_id		= isset($cust_dtl['cust_id'])?(int)$cust_dtl['cust_id']:0;		
	}
	if(isset($dec_arr[3])) {
	    $key_val = explode("=",$dec_arr[3]);
	    $cname = isset($key_val[1])?$key_val[1]:'';
	}
	if($pos_user_id!=0 && $pos_user_id!='' && $prod_cust_id>0){		
		$pos_user_query=execute_my_query("select  GCC_CONTACT_NO,GCC_CONTACT_NAME, gcc_contact_type from gft_customer_contact_dtl ".
				" inner join gft_pos_users on(gcc_id=GPU_CONTACT_ID)".
				" where  GPU_USER_ID=$pos_user_id and GCC_LEAD_CODE=$prod_cust_id ");
		while($pos_user_result = mysqli_fetch_array($pos_user_query)){
			if($pos_user_result['gcc_contact_type']==1){
				$pos_user_mobile = $pos_user_result['GCC_CONTACT_NO'];
				$pos_user_name   = $pos_user_result['GCC_CONTACT_NAME'];
			}else if($pos_user_result['gcc_contact_type']==4){
				$email = $pos_user_result['GCC_CONTACT_NO'];
			}			
		}
	}
	$emp_info_query = "";
	if($prod_cust_id>0 && $mydelight_user_id>0){//myDelight user id
		$emp_info_query = " SELECT GEM_MOBILE mobile_no, GEM_EMAIL email_id FROM gft_emp_master WHERE GEM_EMP_ID='$mydelight_user_id' ";
	}
	if($prod_cust_id>0 && $mygofrugal_user_id>0){//myGofrugal user id
		$emp_info_query = " SELECT GCL_USERNAME mobile_no, GCL_EMAIL_ID email_id FROM gft_customer_login_master WHERE GCL_USER_ID='$mygofrugal_user_id' ";
	}
	if($emp_info_query!=""){
		$emp_info_result = execute_my_query($emp_info_query);
		if((mysqli_num_rows($emp_info_result)>0) && $row_emp_info=mysqli_fetch_array($emp_info_result)){
			$pos_user_mobile= $row_emp_info['mobile_no'];
			$email 			= $row_emp_info['email_id'];
		}
	}
	if($prod_cust_id==0){
	    return_routing_error_response("Please enter valid Product Data.");
	}
}else {
	$mobile_no = substr($mobile_no, -15);
	if(($mobile_no=="" || (strlen($mobile_no)<10) || (!is_numeric($mobile_no))) && $email==''){		
		return_routing_error_response("Please enter valid mobile number.");
	}else if((strpos($email, '@') == FALSE)&& $mobile_no ==""){
		return_routing_error_response("Please enter valid email id.");
	}
}
$result_group			=	/*. (mixed[string]) .*/ array();
$temp_CustomerCallerId 	= 	substr($mobile_no,-10);
$asa_status 			= 	false;
$non_asa_status 		= 	false;
$response_array			=	array();
$outlet_list			=	array();
$product_asa_dtl		=	array();
$unique_installed_product=array();
$temp_array = array();
$welcome_arr = /*. (string[int][string]) .*/array();
$cust_id				=	"";
$cust_name				=	"";
$contact_name			=	"";
$support_group_id		=	"";
$product_version		=	"";
$product_version_array	=	array();
$asa_status_text 		=	"";
$seperator=$ins_seperator=$all_leads=$installed_product="";
$is_employee_partner_mobile_no=false;
$is_employee = false;
if($prod_cust_id!=0){
	$all_leads .= $seperator.$prod_cust_id;
	$seperator = ",";
	$query1=" select GLH_LEAD_CODE,GLH_CUST_NAME,GLH_MAIN_PRODUCT from gft_lead_hdr where GLH_LEAD_CODE='$prod_cust_id' ";
	$result1=execute_my_query($query1);
	if($row1=mysqli_fetch_array($result1)){
		$cust_id = $row1['GLH_LEAD_CODE'];
		$support_group_id = $row1['GLH_MAIN_PRODUCT'];
		$emp_id = (int)get_single_value_from_single_query("GEM_EMP_ID", "select GEM_EMP_ID from gft_emp_master where gem_lead_code='$cust_id' AND GEM_STATUS='A'");
		$cp_dtl=its_active_partner_lead_code($cust_id);
		if($cp_dtl['is_cp']==='true' && isset($cp_dtl['cp_lead_code']) && $cp_dtl['cp_lead_code']!=""){// Partner			
			$is_employee_partner_mobile_no=true;
			$support_group_id		=	'24';
			if($cp_dtl['is_dealer']=='true'){//For dealer
				$support_group_id		=	'35';
			}
			$contact_name = $cname;
			$all_leads = "";
		}else if(($emp_id>0 && $emp_id<7000)){
			$is_employee_partner_mobile_no=true;
			$is_employee=true;
			$support_group_id		=	'13';
			$all_leads="";
		}
	}else{
		return_routing_error_response("invalid customer id ($prod_cust_id).");
	}
}else{
	$emp_result = get_employee_info_from_contact($mobile_no,$email);
	$emply_id = isset($emp_result['GEM_EMP_ID'])?(int)$emp_result['GEM_EMP_ID']:0;
	if($emply_id>0){/* Employees Group */
		$cust_id				=	$emp_result['GEM_LEAD_CODE'];
		$cust_name 				= 	$emp_result['GEM_EMP_NAME'];
		$contact_name			=	$emp_result['GEM_EMP_NAME'];
		$email					=   $emp_result['GEM_EMAIL'];
		$is_employee_partner_mobile_no=true;
		$is_employee=true;
		$support_group_id		=	'13';
	}else{
		$cp_result=its_active_partner_no($temp_CustomerCallerId,$email);
		if($cp_result['is_cp']==='true'){	/* partner Group */
			$cust_id				=	$cp_result['cp_lead_code'];
			$cust_name 				= 	$cp_result['cp_name'];
			$contact_name			=	$cp_result['cp_contact_name'];
			$email					=   $cp_result['cp_email'];
			$is_employee_partner_mobile_no=true;
			$support_group_id		=	'24';
			if($cp_result['is_dealer']=='true'){//For dealer
				$support_group_id		=	'35';
			}
		}else if(check_its_exists_in_SAM(($mobile_no!='')?$mobile_no:$email)) {
			$gcc_contact_no_where_condtion = '';
			$email_condition = '';
			if($mobile_no!=''){
				$gcc_contact_no_where_condtion=" and ".getContactDtlWhereCondition('gcc_contact_no',$mobile_no);
			}else if($email!=''){
				$email_condition = "and GCC_CONTACT_NO='$email'";
			}
			// 1. Get all leads based on given mobile number
			$query=<<<END
				  	select GLH_LEAD_CODE,GLH_CUST_NAME,GLH_MAIN_PRODUCT,GCC_CONTACT_NAME,GLH_CUST_STREETADDR2,GPU_USER_ID from gft_customer_contact_dtl
					left join gft_lead_hdr lh on (glh_lead_code=gcc_lead_code)
					left join gft_pos_users ON (gcc_id=GPU_CONTACT_ID)
					where glh_lead_type!=8 $gcc_contact_no_where_condtion $email_condition group by GLH_LEAD_CODE
END;
			$result=execute_my_query($query);		
			$total_row = mysqli_num_rows($result);
			while($row=mysqli_fetch_array($result)){
				$all_leads .= "$seperator".$row['GLH_LEAD_CODE'];
				$seperator = ",";
				if($contact_name==""){
					$contact_name=$row['GCC_CONTACT_NAME'];
				}
				$cust_id = ($cust_id==""?$row['GLH_LEAD_CODE']:$cust_id);
				$support_group_id = ($support_group_id=="")?$row['GLH_MAIN_PRODUCT']:$support_group_id;
				$outlet_dtl['cust_id']=$row['GLH_LEAD_CODE'];
				$outlet_dtl['cust_name']=$row['GLH_CUST_NAME'];
				$outlet_dtl['cust_location']=$row['GLH_CUST_STREETADDR2'];
				$prod_str = "custid=".$row['GLH_LEAD_CODE']."&pos_userid=".(int)$row['GPU_USER_ID'];
				$outlet_dtl['product_encrypt'] = lic_encrypt($prod_str, $secret);
				$outlet_list[] = $outlet_dtl;
				$pos_id = ((int)$row['GPU_USER_ID']);				
				if($total_row==1 && $pos_id>0 && $mobile_no==""){
					$mobile_no = get_pos_contact($cust_id,$pos_id,1);
				}
				if($total_row==1 && $pos_id>0 && $email==""){
					$email = get_pos_contact($cust_id,$pos_id,4);
				}
			}
		}else{
			//If entered mobile not available in SAM
		}
	}
}

	//2. Get all installed product details.
if($all_leads!="" || $sub_lead_code!=""){
    $installation_leads = ($sub_lead_code!=""?$sub_lead_code:$all_leads);
    $install_result 	=	execute_my_query(query_to_get_customer_installation_dtl($installation_leads));
	$num_rows = mysqli_num_rows($install_result);
	$gmp_product_code_not_in=get_samee_const('NON_ASA_PRODUT_SKIP_CC_SOLUTION');
	$asa_not_in_product_code = explode(",", $gmp_product_code_not_in);
	$all_installed_product_code = array();
	$install_lead_codes = /*. (string[int]) .*/array();
	$all_license_types = /*. (string[int]) .*/array();
	$support_cust_id = 0;
	while($row_install_dtl=mysqli_fetch_array($install_result)){
		$base_product = $row_install_dtl['GPM_IS_BASE_PRODUCT'];
		$validity_date = $row_install_dtl['GID_VALIDITY_DATE'];
		$gpm_license_type = $row_install_dtl['GPM_LICENSE_TYPE'];
		$non_asa_status = true;
		$install_lead_codes[] = $row_install_dtl['GLH_LEAD_CODE'];
		if($base_product=='Y'){
			$all_license_types[] = $gpm_license_type;
			if(strtotime($validity_date)>=strtotime(date('Y-m-d'))){
				$asa_status=true;
				$support_group_id = $row_install_dtl['GLH_MAIN_PRODUCT'];
				$support_cust_id  = $row_install_dtl['GLH_LEAD_CODE'];
				$asa_status_text = "Valid";
				$installed_product.= $ins_seperator."'".$row_install_dtl['PCODE_SKEW']."'";
				$ins_seperator=",";
			}else if($gpm_license_type!='3'){
				$asa_status_text = "<br>Your ASA Expired on ".date('d-M-Y',strtotime($validity_date));
			}
		}
		$ins_lead_code 	 = $row_install_dtl['GID_LEAD_CODE'];
		$welcome_content = (string)$row_install_dtl['GPV_WELCOME_CONTENT'];
		$install_status  = $row_install_dtl['GID_STATUS'];
		$lic_pcode		 = $row_install_dtl['GID_LIC_PCODE'];
		$all_installed_product_code[]=$lic_pcode;
		$product_dtl['installed_cust_id'] = $ins_lead_code;
		$product_dtl['product_name'] = $row_install_dtl['GPM_PRODUCT_ABR'];
		$product_dtl['asa_status'] =$asa_status_text;
		$product_dtl['version'] =$row_install_dtl['GID_CURRENT_VERSION'];
		$product_asa_dtl[$ins_lead_code][]=$product_dtl;
		if(!in_array($product_dtl['product_name'], $temp_array)){
			$unique_installed_product[]=$product_dtl;
			$product_version .= ($product_version!=""?", ":"").$product_dtl['product_name']." ".$product_dtl['version'];
			$temp_array[]=$product_dtl['product_name'];
			if($row_install_dtl['GPM_FREE_EDITION']=='N'){
				$version_key = substr($row_install_dtl['PCODE_SKEW'], 0,8);
				$product_version_array[$version_key] = $row_install_dtl['GPV_VERSION'];
				if(strtotime($validity_date)<strtotime(date('Y-m-d')) && (substr($version_key, 0,3)=='300')){
					$temp_array[] = array_pop($temp_array);
				}
			}
		}
		if( ($install_status!='U') && ($welcome_content!='') ){
			if( ($support_group_id!='6') || ($lic_pcode=='300') ){ 
				$arr = /*. (string[string]) .*/array();
				$arr['title'] 	= $row_install_dtl['GPM_PRODUCT_ABR'];
				$arr['mesg'] 	= $welcome_content;
				//$arr['buttons']	= array();
				$welcome_arr[] = $arr;
			}
		}
	}
	$all_license_types = array_unique($all_license_types);
	$all_license_type_str = implode(",", $all_license_types);	
	$support_group_dtl = get_preferred_support_group($installation_leads);
	if($num_rows==0){		
		$support_group_id = isset($support_group_dtl['support_group_id'])?$support_group_dtl['support_group_id']:'17';
		$support_cust_id = isset($support_group_dtl['cust_id'])?$support_group_dtl['cust_id']:'';
	}else if(!$asa_status){		
		if(($all_license_type_str=='3') || count(array_intersect($all_installed_product_code, $asa_not_in_product_code))==count($all_installed_product_code)){
			$support_group_id = isset($support_group_dtl['support_group_id'])?$support_group_dtl['support_group_id']:'17';
			$support_cust_id = isset($support_group_dtl['cust_id'])?$support_group_dtl['cust_id']:'';
		}else if($support_group_id!='17'){
			$support_group_id = "1";
			$product_version .= "  ".$asa_status_text;
		}
		if(isset($install_lead_codes[0])){
		    $support_cust_id = $install_lead_codes[0];
		}
	}
	$cust_id = ($sub_lead_code!=""?$cust_id:($support_cust_id>0?"$support_cust_id":"$cust_id"));
}else if($support_group_id==""){
	$support_group_id = '17';
}
if($support_group_id=='6'){ //only HQ
	$installed_product_temp = $installed_product;
	$installed_product_temp .=($installed_product_temp!=""?",":"")."'300-03.0'";
	$installed_product = "'300-03.0'";
	if(!isset($product_version_array['300-03.0'])){
		$product_version_array['300-03.0'] = "";
	}	
}
if($support_group_id=='17'){//For presales support group no need to show non-asa group.
	$non_asa_status=false;
}
//If chat is getting initiated from web installer, product group should be new visitor to block uberization content in delight chat
if($source_from=='installer'){
	$installed_product="";
}
if ($is_employee_partner_mobile_no === false){
$sql_chatbot_query = 	" select GCG_ID, GCG_NAME,GCM_ID, GCM_TITLE,GCM_ROUTE_LINK,GCG_ROUTE_LINK  from gft_chatbot_group_mapping_master ".
		" inner join gft_chatbot_group_master cm ON(GCM_GROUP_ID=GCG_ID) ".
		" where GCM_STATUS=1 ".
		(($is_employee_partner_mobile_no)?"":" AND GCM_PRODUCT in('0'".($installed_product!=''?",$installed_product":"").")").
		(($is_employee_partner_mobile_no)?"":" AND GCM_ASA_STATUS in(0".(($asa_status)?",1":(($non_asa_status)?",2":"")).")").
		" order by GCG_ORDER_BY, GCM_ORDER_BY limit 1";
$result_chatbot		=	execute_my_query($sql_chatbot_query);
while($row_chatbot=mysqli_fetch_array($result_chatbot)){
	$group_id 					=	$row_chatbot['GCG_ID'];
	$chat_group['group_id']	 	= 	$row_chatbot['GCG_ID'];
	$chat_group['group_name'] 	=	$row_chatbot['GCG_NAME'];
	$chat_group['name']	 		= 	$row_chatbot['GCM_TITLE'];
	$chat_group['route'] 		=	$row_chatbot['GCM_ROUTE_LINK'];
	$subgroup['name']			=	$row_chatbot['GCM_TITLE'];
	$subgroup['route']			=	$row_chatbot['GCM_ROUTE_LINK'];
	$subgroup1[$group_id][]		=	$subgroup;
	if(count($subgroup1[$group_id])>1){
		$chat_group['name']	 		= 	$row_chatbot['GCG_NAME'];
		$chat_group['route'] 		=	$row_chatbot['GCG_ROUTE_LINK'];
		$chat_group['subtopics']	=	$subgroup1[$group_id];
	}
	$response_array[$group_id] = $chat_group;
	$chat_group['subtopics']	= array();
}

} //end of customer (!$is_employee_partner_mobile_no)

$chatbot_groups = array();
foreach ($response_array as $key=>$values){
	$chatbot_groups[]=$values;
}
$outlet_group_list=array();
foreach ($outlet_list as $key_outlet=>$values_outlet){
	//$values_outlet['products']=isset($product_asa_dtl[$values_outlet['cust_id']])?$product_asa_dtl[$values_outlet['cust_id']]:array();
	if(isset($product_asa_dtl[$values_outlet['cust_id']])){
		$values_outlet['product_version']="";
	}
	$outlet_group_list[]=$values_outlet;	
}

if ($is_employee_partner_mobile_no === false){
if( (count($chatbot_groups)==0) || $is_employee ){
	$about_gst['name']="New Visitor";
	$about_gst['route']="newvisitor:/";
	$chatbot_groups[]=$about_gst;
}
}//end of customer / new visittor (!is_employee_partner_mobile_no)

if ($is_employee_partner_mobile_no){
	$chat_support_agent['group_id']="15";
	$chat_support_agent['group_name']="Support Agent";
	$chat_support_agent['name']="about_gst";
	$chat_support_agent['route']="about_gst:/";
	$chatbot_groups[]=$chat_support_agent;
}

if($support_group_id=='6'){ //only HQ
	$installed_product = $installed_product_temp;
}
$uber_product_data_list = array();
$vertical_id = get_single_value_from_single_table("GLH_VERTICAL_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", $cust_id);
if($is_sub_routing){//When parter/employee selection branding and product variant
    $installed_product = $product_id!=""?"'$product_id'":$installed_product;
    $uber_product_data_list = get_uber_product_list(false, $installed_product,false, false,$vertical_id);
}else{
    $uber_product_data_list = get_uber_product_list($is_employee_partner_mobile_no, $installed_product,$asa_status, $non_asa_status,$vertical_id);
}
//Send in vertical and business informatoin if presales
$vertical_name=$business_name="";
$agent_list = array();
$agent_name_list = array();
if($cust_id!="") {
    if($support_group_id=="17"){
        $cust_dtl=customerContactDetail($cust_id);
    	$vertical_name = $cust_dtl['VERTICAL_NAME'];
    	$business_name = $cust_dtl['BUSINESS_NAME'];
    	$welcome_arr[] = array('title'=>"New Visitor",'mesg'=>"Vertical Name: $vertical_name <br>Business Name: $business_name");
    	$sub_group=get_lead_mgmt_incharge(0,0,0,0,0,$cust_id,true);
    	if($sub_group!=""){
    		$rows_lmt_owner = 	execute_my_query(" select GLI_EMP_ID,GEM_EMP_NAME from gft_lmt_incharge_master ".
    							" INNER JOIN gft_emp_master em ON(GLI_EMP_ID=GEM_EMP_ID) ".
    							" where ".($sub_group=='706'?"":" GLI_PRESALES_SUPPORT_GROUP='$sub_group' AND ")." GLI_ACTIVE_STATE='A' GROUP BY GLI_EMP_ID");
    		while ($row_lmt_owner=mysqli_fetch_array($rows_lmt_owner)){
    			$agent_list[] = $row_lmt_owner['GLI_EMP_ID'];
    			$agent_name_list[] = $row_lmt_owner['GEM_EMP_NAME'];
    		}
    	}
    }
}
$customer_country = get_single_value_from_single_table("glh_country", "gft_lead_hdr", "GLH_LEAD_CODE", "$cust_id");
if($support_group_id=="17" && $cust_id!="" && strtolower($customer_country)!="india" && $customer_country!=""){
	$support_group_id="34";
}else if(strtolower($customer_country)!="india" && $customer_country!=""){
	$welcome_arr = null;
}

//Send OTP entered mobile no
$new_otp = "";
if($cust_id!="" && $cust_id!="0" && $generate_otp==1){
	if($mobile_no!=''){
	$new_otp 		= 	generate_sms_otp($cust_id,$email_id="",$mobile_no);
	entry_sending_sms_to_customer($mobile_no,get_formatted_content(array('OTP'=>array($new_otp),'Customer_ID'=>array($cust_id)),143),143,$cust_id,1,'9999',0,null,true,0,$country_code);
	}else if($email!=''){
		$new_otp 		= 	generate_sms_otp($cust_id,$email,$mobile_no='');
		$db_sms_content_config = /*. (string[string][int]) .*/array(
				'OTP' => array($new_otp),
				'Email'=>array($email),
				'Customer_Id'=>array($cust_id),
				'Customer_ID'=>array($cust_id));
		send_formatted_mail_content($db_sms_content_config,85,155,null,null,$email);
	}
}
if($cust_name==""){
	$cust_name = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", $cust_id);
}
$preferred_agent_ids = "";
$preferred_agent_names = "";
$put_comma = "";
if($all_leads!=''){
	$sql1 = " select GCP_AGENT_ID,GLM_LOGIN_NAME from gft_customer_preferred_agent join gft_login_master on (GLM_EMP_ID=GCP_AGENT_ID) ".
			" where GCP_LEAD_CODE in ($all_leads) and GCP_STATUS=1 AND GCP_SUPPORT_TYPE='2' group by GCP_AGENT_ID ";
	$res1 = execute_my_query($sql1);
	while ($data1 = mysqli_fetch_array($res1)){
		$preferred_agent_ids 	.= $put_comma.$data1['GCP_AGENT_ID'];
		$preferred_agent_names 	.= $put_comma.$data1['GLM_LOGIN_NAME'];
		$put_comma = ",";
	}
}
$mobile_no = $pos_user_mobile!='' && $pos_user_mobile!=0?"$pos_user_mobile":"$mobile_no";
$feedback_conversation_id="";
$welcome_screen_content = array();
if($cust_id!='' && $cust_id!='0'){
	$feedback_conversation_id = get_pending_feedback_conversation_id($cust_id,$mobile_no);
	if(strtolower($customer_country)=="india"){
		$welcome_screen_content	=	get_chatbot_welcome_screen_content($support_group_id,$cust_id,$product_version_array);
	}	
}
$support_group_name = "";
$chat_auto_connect  = false;
$sub_routing = false;
$partner_sub_group = 0;
$employee_sub_group = 0;
if($is_sub_routing){
    $query_parter_group = ("select sg.GSP_PARTNER_SUPPORT_GROUP, sg.GSP_EMPLOYEE_SUPPORT_GROUP from gft_voicenap_group ".
        " INNER JOIN gft_support_product_group sg ON(GVG_SUPPORT_GROUP=GSP_PARTNER_SUPPORT_GROUP) ".
        " where GVG_PRODUCT='$product_id' limit 1");
    if($sub_lead_code!=""){
        $query_parter_group = ("select sg.GSP_PARTNER_SUPPORT_GROUP, sg.GSP_EMPLOYEE_SUPPORT_GROUP from gft_support_product_group sg ".
            " where GSP_GROUP_ID=$support_group_id AND GSP_PARTNER_SUPPORT_GROUP>0");
    }
    $result_parter_group = execute_my_query($query_parter_group);
    if((mysqli_num_rows($result_parter_group)>0) && $support_info_row=mysqli_fetch_assoc($result_parter_group)){
        $partner_sub_group = (int)$support_info_row['GSP_PARTNER_SUPPORT_GROUP'];
        $employee_sub_group = (int)$support_info_row['GSP_EMPLOYEE_SUPPORT_GROUP'];
        if($sub_routing_group_id ==24 || $sub_routing_group_id==13){//For getting partner and employee sub group.
            $partner_emp_support_group_id = ($sub_routing_group_id==24)?$partner_sub_group:$employee_sub_group;
            if($partner_emp_support_group_id>0){
                $support_group_id =$partner_emp_support_group_id;
                $support_group_name = get_single_value_from_single_table("GSP_GROUP_NAME", "gft_support_product_group", "GSP_GROUP_ID", $support_group_id);
            }
        }
    }else{
        $support_group_name = get_single_value_from_single_table("GSP_GROUP_NAME", "gft_support_product_group", "GSP_GROUP_ID", $support_group_id);
    }
    $sub_routing = true;
}else{
    $support_info_res = execute_my_query("select GSP_GROUP_ID,GSP_GROUP_NAME,GSP_CHAT_AUTO_INITIATE,GSP_BRAND_OPTION_IN_CHAT,GSP_PARTNER_SUPPORT_GROUP, GSP_EMPLOYEE_SUPPORT_GROUP from gft_support_product_group where GSP_GROUP_ID='$support_group_id'");
    if((mysqli_num_rows($support_info_res)>0) && $support_info_row=mysqli_fetch_assoc($support_info_res)){
        $support_group_name = $support_info_row['GSP_GROUP_NAME'];
        $chat_auto_connect = ($support_info_row['GSP_CHAT_AUTO_INITIATE']=='Y'?true:false);
        $sub_routing =  ($support_info_row['GSP_BRAND_OPTION_IN_CHAT']=='Y'?true:false);
    }
}
$customer_support_type = (int)get_single_value_from_single_table("GLE_SUPPORT_MODE", "gft_lead_hdr_ext", "GLE_LEAD_CODE", $cust_id);
if($customer_support_type==2){// For phone support customer show the new cloud support number.
    $message = "We would like to inform you that we have temporarily changed our Call service number ".
            "Old Number : 044 66200200 New Number : 044 61716171 ".
            "This change will be effective from 18/03/2020 , 12am IST Please update the same in ". 
            "your contacts so that you can reach our executives easily.";
    $welcome_arr[] = array('title'=>"Phone Support Customer",'mesg'=>"Dear customer, $message");
}
$send_otp_to_both = get_samee_const("SEND_OTP_TO_BOTH_CONTACT");
$min_product_version_for_year_begin = array(3,4,5,6);
$business_hrs = isBusinessHourRouting();
$all_response =array();
$all_response['status']		="success";
$all_response['contact_no']	=$mobile_no;
$all_response['email_id']	=$email;
$all_response['contact_name']=$pos_user_name!='' && $pos_user_name!=null?"$pos_user_name":"$contact_name";
$all_response['pos_user_id']	="$pos_user_id";
$all_response['cust_id']	="$cust_id";
$all_response['support_cust_id']	="$sub_lead_code";
$all_response['cust_name']	="$cust_name";
$all_response['support_group_id']	= $support_group_id;
$all_response['support_group_name']	= $support_group_name;
$all_response['preferred_agent_ids']	= $preferred_agent_ids;
$all_response['preferred_agent_names']	= $preferred_agent_names;
//$all_response['installed_products']	=$unique_installed_product;
$all_response['product_version']= $product_version;
$all_response['topics']			= $chatbot_groups;
$all_response['agent_list']		= $agent_list;
$all_response['agent_name_list']= $agent_name_list;
$all_response['product_list']	=$uber_product_data_list;
$all_response['outlet_list']	= $outlet_group_list;
$all_response['welcome_mesg'] 	= $welcome_arr;
$all_response['vertical_name']	= $vertical_name;
$all_response['business_name'] 	= $business_name;
$all_response['pending_feedback'] 	= $feedback_conversation_id;
$all_response['shift']			= ($business_hrs)?"day":"night";
$all_response['promotion_data'] = $welcome_screen_content;
$all_response['initiate_chat'] = $chat_auto_connect;
$all_response['sub_routing'] = $sub_routing;
$all_response['complaintType'] = ($support_group_id==17 || $support_group_id==34)?"appointment":"ticket";
$all_response['auto_verify']   = ($send_otp_to_both==1?"$new_otp":"");
$migr_link = null;
if((int)$cust_id>0){
	$all_response['product_encrypt'] = lic_encrypt("custid=$cust_id&pos_userid=&support_group_id=$support_group_id&contact_name=$contact_name", $secret);
	if((int)get_voice_minutes_for_ordered_value($cust_id) > 1 && (!check_support_migration_status($cust_id))){
		//$migr_link = "$global_web_domain/confirm-digital-assure-care.html?data=".urlencode(lic_encrypt(json_encode(array('cid'=>$cust_id)), $secret));
	}
}
$all_response['support_migration_url'] = $migr_link;
$json_res = json_encode($all_response);
echo $json_res;
enter_call_center_request(json_encode($data), $json_res, "Chatbot Routing");
exit;
?>
