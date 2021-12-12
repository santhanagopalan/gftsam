<?php
require_once(__DIR__ ."/dbcon.php");
$server_id=(isset($_GET['sid'])?(int)$_GET['sid']:1);
$message='';
$result_check=execute_my_query("SELECT GSM_DOMAIN_NAME FROM gft_server_master WHERE GSM_SERVER_ID=$server_id");
if($data_check=mysqli_fetch_array($result_check)){
	$server_url=$data_check['GSM_DOMAIN_NAME'];
	$server_url=str_replace('http://','',$server_url);
	$server_url=str_replace('/service','',$server_url);
	$admin_acces_url=$server_url;
	$curl_proxyid='';
	$update_ch = curl_init();
	curl_setopt($update_ch, CURLOPT_URL,$admin_acces_url);
	if($curl_proxyid!="")  curl_setopt($update_ch, CURLOPT_PROXY, $curl_proxyid);
	curl_setopt($update_ch, CURLOPT_POST,1);
	curl_setopt($update_ch,CURLOPT_POSTFIELDS,"xmldata=");
	curl_setopt($update_ch, CURLOPT_RETURNTRANSFER, TRUE);
	$result= curl_exec($update_ch);
	if($result==true) {
		$message ="success : \n ";
	}else if(curl_errno($update_ch)){
		$message= "Error : Curl -".curl_error($update_ch) .curl_errno($update_ch) ;
	}else{
		$message= "Error : Curl unexpected:";
	}
	curl_close($update_ch);
}else{
	$message= "Error : Server id not found error";
}
echo $message;
?>
<script type="text/javascript">
window.close();
</script>