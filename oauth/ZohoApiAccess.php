<?php
require_once("Client.php");
require_once("GrantType/IGrantType.php");
require_once("GrantType/RefreshToken.php");
require_once(__DIR__."/../cache_impl.php");
class ZohoApiAccess {
	const AUTHORIZE_URL 		= 	'https://accounts.zoho.com/oauth/v2/auth';
	const ACCESS_TOKEN_URL	 	= 	'https://accounts.zoho.com/oauth/v2/token';
	/**
	 * Get the access token
	 * @param string $client_id
	 * @param string $client_secret
	 * @param string $refresh_token
	 * @param string $scope
	 * @param string $cache_var_name
	 * @param string $redirect_uri
	 *
	 * @return string
	 */
	public function getNewAccessToken($client_id,$client_secret,$refresh_token,$scope,$cache_var_name,$redirect_uri="https://sam.gofrugal.com/oauth/callback.php"){
		$client = new OAuth2\Client($client_id, $client_secret, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
// 		$params = array("refresh_token" => $refresh_token, "scope"=>"$scope","response_type"=>"code","access_type"=>"offline");
		$params = array("refresh_token"=>$refresh_token,"client_id"=>"$client_id","client_secret"=>"$client_secret","scope"=>"$scope","grant_type"=>"refresh_token","redirect_uri"=>"$redirect_uri");
		$response = $client->getZohoAccessToken(self::ACCESS_TOKEN_URL, "refresh_token", $params,array('Content-Type'=>'application/x-www-form-urlencoded'));
		$response_arr = $response["result"];
		$access_token = '';
		if(isset($response_arr['access_token']) and $response_arr['access_token']!='' and isset($response['code']) and $response['code']=='200'){
			$access_token	=	$response_arr['access_token'];
			//Cache::putString("$cache_var_name",$access_token,3000);
		}else{
		    mail_error_alert("Error found when generate zoho access token ",json_encode($response_arr));
		}
		return $access_token;
	}
	/**
	 * @param string $client_id
	 * @param string $client_secret
	 * @param string $refresh_token
	 * @param string $scope
	 * @param string $cache_var_name
	 * @return string
	 */
	public function getAccessToken($client_id,$client_secret,$refresh_token,$scope,$cache_var_name){
	    $date_now = date("Y-m-d H:i:s");
	    $cache_access_token = "";
	    if($cache_var_name == "ZOHO_MAIL_ACCESS_TOKEN"){
	        $result = execute_my_query("SELECT GSM_MAIL_ACCESS_TOKEN FROM gft_support_mail_master WHERE GSM_MAIL_REFRESH_TOKEN='$refresh_token'");
	        if((mysqli_num_rows($result)>0) && $tokenrow=mysqli_fetch_assoc($result)){
	            $token_dtl  = json_decode($tokenrow['GSM_MAIL_ACCESS_TOKEN'], true);
	            $cache_access_token = isset($token_dtl['access_token'])?$token_dtl['access_token']:"";
	            $timestamp = isset($token_dtl['timestamp'])?$token_dtl['timestamp']:"";
	            if($timestamp!="" && (round(abs(strtotime($date_now) - strtotime($timestamp)) / 60,2)>50)){
	                $cache_access_token = "";	                
	            }
	        }
	    }else{
	        $cache_access_token = Cache::getString("$cache_var_name");
	    }
		
	    if ($cache_access_token=== null || $cache_access_token==''){
			$cache_access_token=	$this->getNewAccessToken($client_id,$client_secret,$refresh_token,$scope,$cache_var_name);
			if($cache_var_name == "ZOHO_MAIL_ACCESS_TOKEN"){
			    $token_dtl = array();
			    $token_dtl['access_token'] = $cache_access_token;
			    $token_dtl['timestamp'] = $date_now;
			    execute_my_query("UPDATE gft_support_mail_master SET GSM_MAIL_ACCESS_TOKEN='".json_encode($token_dtl)."' WHERE GSM_MAIL_REFRESH_TOKEN='".$refresh_token."'");
			}
			
		}
		return $cache_access_token;
	}
}
?>
