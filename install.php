<?php
require_once(__DIR__ ."/dbcon.php");
$name=(isset($_REQUEST['uid'])?(string)$_REQUEST['uid']:'');
$crypt_name=(isset($_REQUEST['euid'])?(string)$_REQUEST['euid']:'');
$iden=(isset($_REQUEST['order_no'])?(string)$_REQUEST['order_no']:"");
$full_no=$product_id=$order_no='';
if($iden!=''){
	$order_no=mysqli_real_escape_string_wrapper(substr($iden,0,15));
	$full_no=mysqli_real_escape_string_wrapper(substr($iden,15,4));
	$product_id=mysqli_real_escape_string_wrapper(substr($iden,19,8));
}
$process=(isset($_REQUEST['process'])?(string)$_REQUEST['process']:"");
$crypwd_md5=strtolower(md5($name));
$crypt_name=strtolower($crypt_name);
$to_mail='';$product_link='';
$pcode='';
if($crypt_name==$crypwd_md5){
	$result=execute_my_query("SELECT GIH_ID,GIH_UNIQUE_REFERENCE,GIH_ORDER_NO FROM gft_installation_handling_hdr where GIH_UNIQUE_REFERENCE='$name'");
	$rows=mysqli_num_rows($result);
	$msg='';
	$time=substr($name,0,19);
	$host_name=substr($name,19);
	if($process!=''){
		$subject=($process=='I'?"Setup Installed":($process=='U'?"Uninstalled":($process=='A'?"Aborted":($process=='C'?"Canceled":($process=='S'?"Setup Started":($process=='R'?"Registered":""))))));
		$mail=($process=='I'?"install":($process=='U'?"uninstall":($process=='A'?"abort":($process=='C'?"cancel":($process=='S'?"start":($process=='R'?"Register":""))))));
		$body_message=$subject;
		$subject="{$_REQUEST['product']}-{$_REQUEST['productversion']} - $subject ";
	}
	if($rows==0){
		$last_id=0;
		if(strpos(get_samee_const("LOCAL_IP_ADDRESS_SETUPWIZARD"),$_SERVER['REMOTE_ADDR'])===false){
			execute_my_query("insert into gft_installation_handling_hdr (GIH_ID,GIH_UNIQUE_REFERENCE,GIH_DATE,GIH_ORDER_NO,GIH_PROCESS,GIH_REMOTE_ADDRESS,GIH_PRODUCT,GIH_FULLFILLMENT_NO,GIH_PRODUCT_ID) ".
							" values('','$name',now(),'$order_no','$process','{$_SERVER['REMOTE_ADDR']}','{$_REQUEST['product']}','$full_no','$product_id')");
			$last_id=mysqli_insert_id_wrapper();
		}
		/*$msg="<br>Dear Team,<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" .
				"$body_message successfully for the product {$_REQUEST['product']} - {$_REQUEST['productversion']}. <br>" .
				(isset($_REQUEST['reason']) and $_REQUEST['reason']!=''?"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Reason - {$_REQUEST['reason']}":'').
				"<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".
		        "<table border=0><tr><td>IP Address : </td><td>&nbsp;&nbsp;{$_SERVER['REMOTE_ADDR']}</td></tr><tr>" .
		        (($_SERVER['REMOTE_ADDR']=='203.196.165.82' or $_SERVER['REMOTE_ADDR']=='220.225.219.229')?"<tr><td>Local test : </td><td>&nbsp;&nbsp;GFT Testing</td></tr>" :'').
				"<tr><td>Date : </td><td>&nbsp;&nbsp;$time</td></tr>" .
				"<tr><td>Host Name : </td><td>&nbsp;&nbsp;$host_name</td></tr><tr>" .
				($order_no!=''?"<tr><td>Order No : </td><td>&nbsp;&nbsp;$order_no</td></tr>":'');*/
		foreach($_REQUEST as $key => $value){
			if($key!='uid' and $key!='euid' and $key!='order_no' and $key!='process' and $key!='product'){
				if(strpos(get_samee_const("LOCAL_IP_ADDRESS_SETUPWIZARD"),$_SERVER['REMOTE_ADDR'])===false){
					execute_my_query("insert into gft_installation_handling_dtl (GID_ID,GID_PARAM_NAME,GID_PARAM_VALUE)values($last_id,'$key','".mysqli_real_escape_string_wrapper($value)."')");
				}
			}
		}
		$msg.="</table><br><br>This is automated message from SAM.<br>";	
		if(isset($_REQUEST['product_code']))
			$pcode=str_replace('.','',$_REQUEST['product_code']);
			$product_link='http://www.gofrugal.com';
		if(isset($pcode) and substr($pcode,3,2)!=''){
			$query_code="SELECT gpg_support_mail_id, GSPL_LINK FROM gft_product_group_master ".
						" left join gft_store_product_link_master on( GSPL_PRODUCT_CODE=gpg_product_family_code AND GSPL_LINK_DESC='Product Overview') " .
						" where gpg_product_family_code=".substr($pcode,0,3)." and gpg_skew='".substr($pcode,3,2).'.'.substr($pcode,5,1)."'";
			$result_mail=execute_my_query($query_code);
			if($data_code=mysqli_fetch_array($result_mail)){
				$to_mail=$data_code['gpg_support_mail_id'];
				$product_link=($data_code['GSPL_LINK']!=''?$data_code['GSPL_LINK']:$product_link);
			}
		}
		/*if($to_mail==''){
			$to_mail=get_samee_const('INSTALLATION_UNINSTALLATION');
		}
		send_mail_function("$mail@".get_samee_const("OFFICEAL_MAIL_DOMAIN"),$to_mail,$subject,$msg,null,null,'product setup process',true);
		*/
	}
}
if($process!='S' and $product_link!=''){ header("Location: $product_link");}else{header("Location: "."http://www.gofrugal.com/product-videos.html?product=".substr($pcode,0,3).substr($pcode,3,2).substr($pcode,5,1));}
