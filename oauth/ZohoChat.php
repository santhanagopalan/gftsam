<?php 
require_once(__DIR__ ."/../dbcon.php");
require_once("Client.php");
require_once("GrantType/IGrantType.php");
require_once("GrantType/RefreshToken.php");
require_once(__DIR__."/../cache_impl.php");
require_once(__DIR__."/ZohoApiAccess.php");
class ZohoChat{
	/**
	 * Auth details for Live
	 */
	const LIVE_CLIENT_ID		=	"1000.7Z7HN24HO5W951496FA8J7X242RGS3";
	const LIVE_SECRET_KEY		=	"53c810b1d5be3dc206c0d440cfd8ada9bf5de7bc83";
	const LIVE_REFRESH_TOKEN	=	"1000.374767026313884856e32fc0f1153d98.25ad2f6e8177e4eb7ba9b3003f88219b";
	const LIVE_REDIRECT_URL		=	"http://sam.gofrugal.com/oauth/callback.php";
	const LIVE_SCREEN_NAME		=	"gofrugal";
	
	/**
	 * Auth details for Testing
	 */
	const TEST_CLIENT_ID		=	"1000.WAONMX49N86K065869TGR082O1QNIJ";
	const TEST_SECRET_KEY		=	"f7f492cc730346428f313c7d715831c2279fd95251";
	const TEST_REFRESH_TOKEN	=	"1000.47388878d7b7282e04037965c9a158bb.e10c201bbbe65238424f67362b40d1c8";
	const TEST_REDIRECT_URL		=	"https://labtest.gofrugal.com/oauth/callback.php";
	const TEST_SCREEN_NAME		=	"stagging";
	/**
	 * API end point
	 */
	const END_POINT				=	"https://salesiq.zoho.com/api/v1/";
	/**
	 * Client ID
	 *
	 * @var string
	 */
	protected $client_id = null;
	
	/**
	 * Client Secret
	 *
	 * @var string
	 */
	protected $client_secret = null;
	
	/**
	 * Refresh token
	 *
	 * @var string
	 */
	protected $refresh_token = null;
	/**
	 * Access token
	 *
	 * @var string
	 */
	protected $access_token = null;
	/**
	 * Screen Name
	 *
	 * @var string
	 */
	protected $screen_name = null;
	private $apiacess = null;
	/**
	 * Construct
	 *
	 * @param int $mode Mode Testing / Live
	 * 
	 * @return void
	 */
	public function __construct($mode)
	{
		if($mode==0){
			$this->client_id     = self::LIVE_CLIENT_ID;
			$this->client_secret = self::LIVE_SECRET_KEY;
			$this->refresh_token = self::LIVE_REFRESH_TOKEN;
			$this->screen_name	=self::LIVE_SCREEN_NAME;	
		}else{
			$this->client_id     = self::TEST_CLIENT_ID;
			$this->client_secret = self::TEST_SECRET_KEY;
			$this->refresh_token   = self::TEST_REFRESH_TOKEN;
			$this->screen_name	=self::TEST_SCREEN_NAME;
		}	
		$this->apiacess = new ZohoApiAccess();
	}
	/**
	 * @param string $chat_id
	 * @param string[string][string] $response_arr
	 * @param string $fromtime
	 * 
	 * @return void
	 */
	public function updateResponseDetails($chat_id, $response_arr,$fromtime=""){
		$actual_response=array();
		if(isset($response_arr['data'])){
			$actual_response=$response_arr;
			$chat_tanscript	=	"";
			try{				
				if(is_array($response_arr['data'])){
					$chat_tanscript	=	mysqli_real_escape_string_wrapper(json_encode($response_arr));
				}else{
					$chat_tanscript=mb_convert_encoding($response_arr,'UTF-8','UTF-8');
				}
			}catch(Exception $e){
				$chat_tanscript	=	'{"error":{"message":"'.mysqli_real_escape_string_wrapper($e->getMessage()).'","code":2032}}	';
			}		
			if($fromtime!=""){
				$result_row=mysqli_fetch_array(execute_my_query("SELECT GZC_TRANSCRIPT FROM gft_zoho_chat_hdr WHERE GZC_CHAT_ID=$chat_id"));
				$transcript_value=json_decode($result_row['GZC_TRANSCRIPT'],true);
				if(is_array($response_arr['data'])){
					array_shift($response_arr['data']);
				}else{
					$json_response=stripcslashes(mb_convert_encoding($response_arr,'UTF-8','UTF-8'));
					$response_arr=json_decode($json_response,true);
					array_shift($response_arr['data']);
				}				
				$new_array['data']=array_merge($transcript_value['data'],$response_arr['data']);				
				$chat_tanscript	=	mysqli_real_escape_string_wrapper(json_encode($new_array));
			}
			$sql_update_tanscript	=	"UPDATE gft_zoho_chat_hdr SET GZC_TRANSCRIPT='$chat_tanscript', GZC_TRANSRIPT_SYNC=1 WHERE GZC_CHAT_ID=$chat_id";
			execute_my_query($sql_update_tanscript);
			if(count($actual_response['data'])>=49 && isset($actual_response['data'][49]['time'])){
				$fromtime=$actual_response['data'][49]['time'];
				$this->getSingleChatTranscript($chat_id,$fromtime);
			}
		}else if(isset($response_arr['error'])){
			$chat_tanscript	=	mysqli_real_escape_string_wrapper(json_encode($response_arr));
			$sql_update_tanscript	=	"UPDATE gft_zoho_chat_hdr SET GZC_TRANSCRIPT='$chat_tanscript', GZC_TRANSRIPT_SYNC=1 WHERE GZC_CHAT_ID=$chat_id";
			execute_my_query($sql_update_tanscript);
		}else{
			$chat_tanscript	=	mysqli_real_escape_string_wrapper(json_encode($response_arr));
			$sql_update_tanscript	=	"UPDATE gft_zoho_chat_hdr SET GZC_TRANSCRIPT='$chat_tanscript', GZC_TRANSRIPT_SYNC=1 WHERE GZC_CHAT_ID=$chat_id";
			execute_my_query($sql_update_tanscript);
		}
	}
	/**
	 * @param string $chat_id
	 * @param string $fromtime
	 * 
	 * @return void
	 */
	public function getSingleChatTranscript($chat_id,$fromtime=""){
		$additional_params="";
		if($fromtime!=""){
			$additional_params="?fromtime=$fromtime";
		}else{
			$additional_params="?limit=49";
		}
		$response_arr	=	/*. (string[string][string]) .*/array();
		$client = new OAuth2\Client($this->client_id, $this->client_secret, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
		$cache_access_token = $this->apiacess->getAccessToken($this->client_id,$this->client_secret,$this->refresh_token,'SalesIQ.chatdetails.READ,SalesIQ.chattranscript.READ','ZOHO_ACCESS_TOKEN');	
		$this->access_token = $cache_access_token;
		$client->setAccessToken($cache_access_token);
		$client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_OAUTH);
		$response = $client->fetch(self::END_POINT.$this->screen_name."/chats/$chat_id/transcript$additional_params");		
		if(isset($response["result"])){
			$response_arr = $response["result"];
			$this->updateResponseDetails($chat_id, $response_arr,$fromtime);
		}		
	}
	/**
	 * Get chat transcript
	 *
	 * @return void
	 */
	public function getChatTranscript()
	{	
		$response_arr	=	/*. (string[string][string]) .*/array();
		$client = new OAuth2\Client($this->client_id, $this->client_secret, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
		$cache_access_token = $this->apiacess->getAccessToken($this->client_id,$this->client_secret,$this->refresh_token,'SalesIQ.chatdetails.READ,SalesIQ.chattranscript.READ','ZOHO_ACCESS_TOKEN');
		$this->access_token = $cache_access_token;
		$client->setAccessToken($cache_access_token);
		$client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_OAUTH);
		$sql_pending_list	=	" select GZC_CHAT_ID from gft_zoho_chat_hdr where (GZC_STATUS!=4 AND GZC_TRANSRIPT_SYNC=0) OR ".
								" (GZC_STATUS=4 AND GZC_LAST_UPDATED_DATE_TIME> DATE_ADD(NOW(), INTERVAL -6 HOUR))";
		$result_pending_list=	execute_my_query($sql_pending_list);
		while($row=mysqli_fetch_array($result_pending_list)){
			$chat_id	=	$row['GZC_CHAT_ID'];
			$response = $client->fetch(self::END_POINT.$this->screen_name."/chats/$chat_id/transcript?limit=49");
			if(isset($response["result"])){
				$response_arr = $response["result"];
				$this->updateResponseDetails($chat_id, $response_arr);
			}						
		}
	}
	/**
	 * @param string $chat_id
	 * @param string $image_url
	 * @param string $return_format
	 * 
	 * @return string
	 */
	public function getAttachementLink($chat_id,$image_url,$return_format=''){
		$response_arr	=	/*. (string[string][string]) .*/array();
		$client = new OAuth2\Client($this->client_id, $this->client_secret, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
		$cache_access_token = $this->apiacess->getAccessToken($this->client_id,$this->client_secret,$this->refresh_token,'SalesIQ.chatdetails.READ,SalesIQ.chattranscript.READ','ZOHO_ACCESS_TOKEN');
		$this->access_token = $cache_access_token;
		$client->setAccessToken($cache_access_token);
		$client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_OAUTH);
		$response = $client->fetch(self::END_POINT.$this->screen_name."/chats/$chat_id$image_url");
		if(isset($response["result"])){
			$response_arr = $response["result"];
			if(isset($response_arr['data']['download_url']) && $response_arr['data']['download_url']!='' && $return_format!='all'){
				return $response_arr['data']['download_url'];
			}else if($return_format=='all'){
				return json_encode($response_arr);
			}else{
				return '';
			}
		}					
	}
	/**
	 * @param string $chat_id
	 *
	 * @return string
	 */
	public function getSingleChatDetails($chat_id){
		$response_arr	=	/*. (string[string][string]) .*/array();
		$chat_department="";
		$client = new OAuth2\Client($this->client_id, $this->client_secret, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
		$cache_access_token = $this->apiacess->getAccessToken($this->client_id,$this->client_secret,$this->refresh_token,'SalesIQ.chatdetails.READ,SalesIQ.chattranscript.READ','ZOHO_ACCESS_TOKEN');
		$this->access_token = $cache_access_token;
		$client->setAccessToken($cache_access_token);
		$client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_OAUTH);
		$response = $client->fetch(self::END_POINT.$this->screen_name."/chats/$chat_id");
		if(isset($response["result"])){
			$response_arr = $response["result"];
			if(isset($response_arr['data'])){
				$chat_department	=	isset($response_arr['data']['department_name'])?$response_arr['data']['department_name']:"";
			}	
		}		
		return $chat_department;
	}
}
?>
