<?php
require_once(__DIR__.'/../dbcon.php');
require_once(__DIR__."/../ismile/ismile_util.php");
/**
 * @param string $msg
 * @param string $data_type
 *
 * @return void
 */
function send_failure_msg($msg,$data_type){
	if($data_type=='xml'){
		$resp = "<?xml version='1.0' standalone='yes'?>".
				"<SERVER_RESPONSE><STATUS>failure</STATUS><MESSAGE>$msg</MESSAGE></SERVER_RESPONSE>";
	}else {
		$ret_arr['status']  = 'failure';
		$ret_arr['message'] = $msg;
		$resp = json_encode($ret_arr);
	}
	echo $resp;
}

$product_code	= isset($_GET['product_code'])?(int)$_GET['product_code']:0; 
$customer_id	= isset($_GET['customer_id'])?(int)$_GET['customer_id']:0;
$rec_version	= isset($_GET['version'])?(string)$_GET['version']:'';
$type			= isset($_GET['type'])?(string)$_GET['type']:'';
$data_type		= isset($_GET['data_type'])?(string)$_GET['data_type']:'';
$base_pcode     = isset($_GET['base_product_code'])?mysqli_real_escape_string_wrapper($_GET['base_product_code']):'';
$base_pvers     = isset($_GET['base_version'])?(string)$_GET['base_version']:'';

if($data_type=='xml'){
	header('Content-Type: application/xml');
}else{
	header('Content-Type: application/json');
}

$check_cust = get_single_value_from_single_table("GLH_LEAD_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", "$customer_id");
if($check_cust==''){
	$check_emp = get_single_value_from_single_table("GEM_EMP_ID", "gft_emp_master", "GEM_EMP_ID", "$customer_id");
	if($check_emp==''){
		send_failure_msg("Invalid Customer id ($customer_id)",$data_type);
		exit;
	}
}
//For supporting GOBill back compatibility
if($product_code=='539' && $type=='' && $base_pcode==''){
    $installed_dtl = get_cust_installed_base_product($customer_id);
    if(isset($installed_dtl['product_version']) && $installed_dtl['product_version']!=""){
        $base_pvers = $installed_dtl['product_version'];
        $base_pcode = $installed_dtl['product_code']."-".$installed_dtl['product_skew'];
        $type = "compatible";
    }
}
$resp_arr = /*. (string[string]) .*/array();
$sql1 = " select GPV_VERSION,GPV_DOWNLOAD_HLINK,GPV_CDN_SETUP_LINK,GPM_STORE_PDESC,GPV_RPOS7_VERSION,GPV_RPOS6_VERSION,GPV_DE6_VERSION, ".
		" GPV_HQ3_VERSION,GPV_WEB_REPORTER_PATH,GPV_EASY_PRINT_PATH,GPV_CHAIN_MANAGER_PATH,GPV_HASH_VALUE,GPV_SMI_WR_LINK,GPV_GOBILL_PATH,GPV_RELEASE_DATE from gft_product_version_master ".
		" join gft_product_family_master on (GPM_PRODUCT_CODE=GPV_PRODUCT_CODE) ".
		" where GPV_PRODUCT_CODE='$product_code' ";
if(!isset($_GET['version'])){ // if version not requested in query param, need to send only active version
    $sql1 .=" and GPV_PRODUCT_STATUS='A' ";
}

if($type=='compatible'){
    $column   = get_product_column_name_from_code($base_pcode);
    if($column!=''){
        $q2 = " select GPV_VERSION,$column from gft_product_version_master where GPV_PRODUCT_CODE='$product_code' ".
            " order by GPV_RELEASE_DATE desc,GPV_ENTERED_ON desc ";
        $r2 = execute_my_query($q2);
        $dtl_arr = array();
        $ver_matched = false;
        while($d2 = mysqli_fetch_array($r2)){
            $dtl_arr[] = array($d2['GPV_VERSION'],$d2[$column]);
            if($d2[$column]==$base_pvers){
                $rec_version = $d2['GPV_VERSION'];
                $ver_matched = true;
                break;
            }
        }
        if(!$ver_matched){
            foreach ($dtl_arr as $key=> $val){
                if( ($val[1]!='') && ($val[1]!='0') && (version_compare($val[1], $base_pvers,"<")) ){
                    $rec_version = $val[0];
                    break;
                }
            }
        }
    }
}
$rec_version = ($rec_version=='' && $product_code=='539'?"1.0.0.8":$rec_version);
if($rec_version!=""){
	$sql1 .= " and GPV_VERSION='$rec_version' ";
}
if($type=="minimum"){
	$sql1 .= " and GPV_IS_MINIMUM_VERSION=1 ";
}
$sql1 .= " order by gpv_current_version desc,GPV_RELEASE_DATE desc,GPV_ENTERED_ON desc limit 1 ";
$res1 = execute_my_query($sql1);
if($row_data = mysqli_fetch_array($res1)){
	$cdn_setup_link = $row_data['GPV_CDN_SETUP_LINK'];
	$version  		= $row_data['GPV_VERSION'];
	$download_link 	= $row_data['GPV_DOWNLOAD_HLINK'];
	if(strlen($cdn_setup_link) > 5){
		$download_link = $cdn_setup_link;
	}
	$description 	= $row_data['GPM_STORE_PDESC'];
	$gcm_path		= $row_data['GPV_CHAIN_MANAGER_PATH'];
	if($data_type=='xml'){
		$resp = "<?xml version='1.0' standalone='yes'?>".
				"<SERVER_RESPONSE><STATUS>success</STATUS>".
				"<VERSION>$version</VERSION>".
				"<URL>$download_link</URL>".
				"<DESCRIPTION>$description</DESCRIPTION>".
				"</SERVER_RESPONSE>";
	}else{
		$resp_arr['status']				= "success";
		$resp_arr['version'] 			= $version;
		$resp_arr['release_date']       = $row_data['GPV_RELEASE_DATE'];
		$resp_arr['url'] 				= $download_link;
		$resp_arr['file_hash_value']	= $row_data['GPV_HASH_VALUE'];
		$resp_arr['description']		= $description;
		$resp_arr['compatible_rpos7'] 	= $row_data['GPV_RPOS7_VERSION'];
		$resp_arr['compatible_rpos6'] 	= $row_data['GPV_RPOS6_VERSION'];
		$resp_arr['compatible_de6'] 	= $row_data['GPV_DE6_VERSION'];
		$resp_arr['compatible_hq3'] 	= $row_data['GPV_HQ3_VERSION'];
		$resp_arr['web_reporter_path'] 	= $row_data['GPV_WEB_REPORTER_PATH'];
		$resp_arr['easy_print_path'] 	= $row_data['GPV_EASY_PRINT_PATH'];
		$resp_arr['gobill_path']        = $row_data['GPV_GOBILL_PATH'];
		$resp_arr['gcm_path']           = $gcm_path;
		$resp_arr['smi_wr_path']        = $row_data['GPV_SMI_WR_LINK'];
		$vers_family = "";
		if(in_array($product_code,array('715','806','540','748','514','758','759','539','763'))){
			$prod_arr = /*. (string[int][string]) .*/array();
			$que1 = " select GPV_VERSION,GPV_DOWNLOAD_HLINK,GPV_HASH_VALUE,concat(GMV_BASE_PCODE,'-',GMV_BASE_PGROUP) as pg,GMV_FORCE_UPGRADE ".
					" from gft_product_version_master join gft_minimum_version_product_wise on (GMV_ADDON_PCODE=GPV_PRODUCT_CODE and GMV_MINIMUM_VERSION=GPV_VERSION) ".
					" where GMV_ADDON_PCODE='$product_code' and GMV_ADDON_VERSION='$version' ";
			if($base_pcode!='') $que1 .= " and concat(GMV_BASE_PCODE,'-',GMV_BASE_PGROUP)='$base_pcode' ";
			$que_res = execute_my_query($que1);
			while ($row1 = mysqli_fetch_array($que_res)){
				$pg = $row1['pg'];
				$pg_name = "";
				if($pg=='500-07.0'){
					$pg_name = "rpos7";
				}else if($pg=='500-06.5'){
					$pg_name = "rpos6";
				}else if($pg=='200-06.0'){
					$pg_name = "de6";
				}else if($pg=='300-03.0'){
					$pg_name = "hq3";
				}
				$compatible_version = $row_data[get_product_column_name_from_code($pg)];
				$prod_arr[] = array(
									'product'=>"$pg_name",
									'pcode'=>$pg,
									'compatible_version'=>$compatible_version,
									'minimum_version'=>$row1['GPV_VERSION'],
								    'force_upgrade'=>($row1['GMV_FORCE_UPGRADE']=='1'),
									'download_path'=>$row1['GPV_DOWNLOAD_HLINK'],
									'file_hash'=>$row1['GPV_HASH_VALUE']
							 );
			}
			$resp_arr['productDetails'] = $prod_arr;
		}
		$resp = json_encode($resp_arr);
	}
}else{
	send_failure_msg("Product code ($product_code) or Version($rec_version) not available in Version Master",$data_type);
	exit;	
}

echo stripslashes($resp);
closeDBConnection();
exit;
?>
