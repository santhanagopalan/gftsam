<?php
require_once(__DIR__."/dbcon.php");
require_once(__DIR__."/oauth/Client.php");
require_once(__DIR__."/oauth/GrantType/IGrantType.php");
require_once(__DIR__."/oauth/GrantType/RefreshToken.php");
require_once(__DIR__."/cache_impl.php");
require_once(__DIR__."/oauth/ZohoApiAccess.php");
class zohoMailAPI {
    const CLIENT_ID = "1000.6OO2CEHA4WT1O5U04W883VR2MPHQ1H";
    const CLIENT_SECRET = "3115c20b77dfa72120f4e8276922d38e25606d4496";
    const REDIRECT_URI = "https://sam.gofrugal.com/oauth/callback.php";
    const SCOPE = "ZohoMail.folders.READ,ZohoMail.folders.CREATE,ZohoMail.folders.UPDATE,ZohoMail.messages.READ,ZohoMail.messages.CREATE,ZohoMail.messages.UPDATE,ZohoMail.accounts.READ,ZohoMail.accounts.UPDATE,ZohoMail.attachments.CREATE,ZohoMail.attachments.READ";
    private $accountNumber = '';
    private $folderId = "";
    private $apiURL = "";
    private $refreshToken = "";
    private $apiAccess = null;
    
    /**
     * @return void
     */
    public function __construct() {
        $this->apiAccess = new ZohoApiAccess(); 
    }
    /**
     * @param int $urlType
     * @param string $accountNumber
     * 
     * @return string
     */
    private function getAPIUrls($urlType, $accountNumber='',$folderId='',$messageId='',$attachmentId=''){        
        if($urlType == 1){//To get account number
            $this->apiURL = "https://mail.zoho.com/api/accounts";
        }else if($urlType == 2){//To pull list of mails
            $this->apiURL = "https://mail.zoho.com/api/accounts/$accountNumber/messages/search";
        }else if($urlType == 3){// To pull main content in HTML format
            $this->apiURL = "https://mail.zoho.com/api/accounts/$accountNumber/folders/$folderId/messages/$messageId/content";
        }else if($urlType == 4){// To attachment file info
            $this->apiURL = "https://mail.zoho.com/api/accounts/$accountNumber/folders/$folderId/messages/$messageId/attachmentinfo";
        }else if($urlType == 5){// To attachment file 
            $this->apiURL = "https://mail.zoho.com/api/accounts/$accountNumber/folders/$folderId/messages/$messageId/attachments/$attachmentId";
        }else if($urlType == 6){// send Reply mail
            $this->apiURL = "https://mail.zoho.com/api/accounts/$accountNumber/messages/$messageId";
        }else if($urlType == 7){// To pull mail folder list
            $this->apiURL = "https://mail.zoho.com/api/accounts/$accountNumber/folders";
        }else if($urlType == 8){// To mark mail as spam
            $this->apiURL = "https://mail.zoho.com/api/accounts/$accountNumber/updatemessage";
        }else if($urlType == 10){// To post attachment
            $this->apiURL = "https://mail.zoho.com/api/accounts/$accountNumber/messages/attachments";
        }else if($urlType == 11){// To as a new mail
            $this->apiURL = "https://mail.zoho.com/api/accounts/$accountNumber/messages";
        }
    }
    /**
     * @param mixed $resp
     *
     * @return void
     */
    protected function mailAPIResponseLog($resp){
        global $log;
        $log->logNotice(str_replace(array("\n","\t"), "",print_r($resp,true))); //json_encode not used due to unicode character issue
    }
    /**
     * 
     * @return void
     */
    private function getAccountNumber(){
        $this->getAPIUrls(1);      
        $client = new OAuth2\Client(self::CLIENT_ID, self::CLIENT_SECRET, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
        $cache_access_token = $this->apiAccess->getAccessToken(self::CLIENT_ID,self::CLIENT_SECRET,$this->refreshToken,self::SCOPE,'ZOHO_MAIL_ACCESS_TOKEN');
        $client->setAccessToken($cache_access_token);
        $client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_OAUTH);
        $response = $client->fetch($this->apiURL,null,OAuth2\Client::HTTP_METHOD_GET);
        $this->mailAPIResponseLog($response);
        if(isset($response['result']['data'][0]['accountId']) && $response['result']['data'][0]['accountId']!=""){
            $this->accountNumber = $response['result']['data'][0]['accountId'];
            execute_my_query("UPDATE gft_support_mail_master SET GSM_ACCOUNT_ID='".$this->accountNumber."' WHERE GSM_MAIL_REFRESH_TOKEN='".$this->refreshToken."'");
        }else if(isset($response['result']['status']['code']) && $response['result']['status']['code']!='200'){
            mail_error_alert("Error found Zoho mail API",json_encode($response));
        }
        
    }
    /**
     *
     * @return void
     */
    private function getInboxFolderId(){
        $this->getAPIUrls(7, $this->accountNumber);
        $client = new OAuth2\Client(self::CLIENT_ID, self::CLIENT_SECRET, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
        $cache_access_token = $this->apiAccess->getAccessToken(self::CLIENT_ID,self::CLIENT_SECRET,$this->refreshToken,self::SCOPE,'ZOHO_MAIL_ACCESS_TOKEN');
        $client->setAccessToken($cache_access_token);
        $client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_OAUTH);
        $response = $client->fetch($this->apiURL,null,OAuth2\Client::HTTP_METHOD_GET);
        $this->mailAPIResponseLog($response);
        if(isset($response['result']['data'][0]['folderId']) && $response['result']['data'][0]['folderId']!=""){
            $this->folderId = $response['result']['data'][0]['folderId'];
            execute_my_query("UPDATE gft_support_mail_master SET GSM_MAIL_FOLDER_ID='".$this->folderId."' WHERE GSM_MAIL_REFRESH_TOKEN='".$this->refreshToken."'");
        }else if(isset($response['result']['status']['code']) && $response['result']['status']['code']!='200'){
            mail_error_alert("Error found Zoho mail API",json_encode($response));
        }
    }
    /**
     * @param string $messageId
     * 
     * @return boolean
     */
    private function isExistingMessage($messageId){
        $messageRefId = (int)get_single_value_from_single_table("GCS_ID", "gft_customer_mail_hdr", "GCS_MESSAGE_ID", "$messageId");
        return $messageRefId>0 ? true : false;
    }
    /**
     * @param string $threadId
     *
     * @return string
     */
    private function getThreadMessageId($threadId){
        $messageRefId = get_single_value_from_single_table("GCS_ID", "gft_customer_mail_hdr", "GCS_MESSAGE_ID", "$threadId");
        return $messageRefId;
    }    
    /**
     * @param string $folderId
     * @param string $messageId
     * 
     * @return string[]
     */
    private function toGetAttachmentFiles($folderId, $messageId){
        global $attach_path;
        $filePathList =  array();
        $this->getAPIUrls(4, $this->accountNumber,$folderId, $messageId);
        $client = new OAuth2\Client(self::CLIENT_ID, self::CLIENT_SECRET, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
        $cache_access_token = $this->apiAccess->getAccessToken(self::CLIENT_ID,self::CLIENT_SECRET,$this->refreshToken,self::SCOPE,'ZOHO_MAIL_ACCESS_TOKEN');
        $client->setAccessToken($cache_access_token);
        $client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_OAUTH);
        $fileInfoResponse = $client->fetch($this->apiURL,array(),OAuth2\Client::HTTP_METHOD_GET);
        if(isset($fileInfoResponse['result']['data']['attachments']) && (count($fileInfoResponse['result']['data']['attachments'])>0)){
            $fileInfoList = $fileInfoResponse['result']['data']['attachments'];
            foreach ($fileInfoList as $key=>$fileInfo) {
                $attachmentId = isset($fileInfo['attachmentId'])?$fileInfo['attachmentId']:"";
                $attachmentName = isset($fileInfo['attachmentName'])?$fileInfo['attachmentName']:"";
                $this->getAPIUrls(5, $this->accountNumber,$folderId, $messageId,$attachmentId);
                $response = $client->fetch($this->apiURL,array(),OAuth2\Client::HTTP_METHOD_GET);
                if(isset($response['result'])){
                    $unix_timestamp=time();
                    $filePath = "$attach_path/Support_Upload_Files/".date('Y');
                    if(!file_exists($filePath)){
                        $oldmask = umask(0);
                        mkdir($filePath,0777);
                        umask($oldmask);
                    }
                    $attachmentName = str_replace("'","_",$attachmentName);
                    $filePath = $filePath."/".$unix_timestamp."_".$attachmentName;
                    $fileContent = is_array($response['result'])?(json_encode($response['result'])):$response['result'];
                    file_put_contents($filePath, $fileContent);
                    $filePathList[] = $filePath;
                }
            }
        }else if(isset($fileInfoResponse['result']['status']['code']) && $fileInfoResponse['result']['status']['code']!='200'){
            mail_error_alert("Error found Zoho mail API",json_encode($fileInfoResponse));
        }
        return $filePathList;
    }
    /**
     * @param string $subject
     * @param string $mailFrom
     * 
     * @return boolean
     */
    private function checkSubjectAndSkipMail($subject, $mailFrom){
        $skip_email_subjects = get_samee_const("SKIP_EMAIL_BASED_ON_SUBJECT");
        $mail_subjects = explode("***", $skip_email_subjects);
        $inc = 0;
        while(count($mail_subjects)>$inc){
            if (stripos($subject, $mail_subjects[$inc]) !== false) {
                return true;
            }
            $inc++;
        }
        $skip_from_emails = get_samee_const("SKIP_EMAIL_BASED_ON_FROM_EMAIL");
        $from_email = explode(",", $skip_from_emails);
        if(in_array($mailFrom, $from_email)){
            return true;
        }
        return false;
        
    }
    /**
     * @param string $supportRefId
     * 
     * @return void
     */
    public function pullMails($supportRefId){
        $resultMasterInfo = execute_my_query("select GSM_ID,GSM_NAME,GSM_SUPPORT_MAIL_ID,GSM_SUPPORT_GROUP, GSM_PRODUCT_GROUP,". 
                        " GSM_MAIL_REFRESH_TOKEN,GSM_MAIL_CRON_ACCESS_TIME,GSM_ACCOUNT_ID,GSM_MAIL_FOLDER_ID,GSM_SUPPORT_OWNER,". 
                        " GSM_ENABLE_SUPPORT_GROUP_CON,GSM_IS_INTERNAL_SUPPORT from gft_support_mail_master where GSM_ID='$supportRefId'");
        if(mysqli_num_rows($resultMasterInfo)==0){
            return ;
        }
        $skip_email_domains = get_samee_const("SKIP_EMAIL_DOMAINS");
        $mail_id_domain_skip_list = explode(",", $skip_email_domains);
        $rowMasterInfo = mysqli_fetch_assoc($resultMasterInfo);  
        $this->refreshToken = $rowMasterInfo['GSM_MAIL_REFRESH_TOKEN'];
        $lastMailAccessTime = $rowMasterInfo['GSM_MAIL_CRON_ACCESS_TIME'];
        $this->accountNumber = $rowMasterInfo['GSM_ACCOUNT_ID'];
        $this->folderId = $rowMasterInfo['GSM_MAIL_FOLDER_ID'];
        $supportOwner = $rowMasterInfo['GSM_SUPPORT_OWNER'];
        $isInternalSupport  = $rowMasterInfo['GSM_IS_INTERNAL_SUPPORT'];
        if($this->accountNumber==""){
            $this->getAccountNumber();
        }   
        if($this->folderId==""){
            $this->getInboxFolderId();
        }
        if($this->accountNumber == "" || $this->folderId == ""){
            return ;
        }
        if($this->accountNumber != ''){
            $this->getAPIUrls(2, $this->accountNumber);       
            $client = new OAuth2\Client(self::CLIENT_ID, self::CLIENT_SECRET, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
            $cache_access_token = $this->apiAccess->getAccessToken(self::CLIENT_ID,self::CLIENT_SECRET,$this->refreshToken,self::SCOPE,'ZOHO_MAIL_ACCESS_TOKEN');
            $client->setAccessToken($cache_access_token);
            $client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_OAUTH);
            $mailListResponse = $client->fetch($this->apiURL,array('searchKey'=>'newMails','folderId'=>$this->folderId,'receivedTime'=>"$lastMailAccessTime","limit"=>"100","includeto"=>"true"),OAuth2\Client::HTTP_METHOD_GET);
            $this->mailAPIResponseLog($mailListResponse);
            if(isset($mailListResponse['result']['data']) && 
                (count($mailListResponse['result']['data'])>0) && 
                $mailListResponse['result']['status']['code']=='200'){
                $messageList = $mailListResponse['result']['data'];
                $receivedTimeInfo = array();
                foreach ($messageList as $key => $row)
                {
                    $receivedTimeInfo[$key] = $row['receivedTime'];
                }
                array_multisort($receivedTimeInfo, SORT_ASC, $messageList);                
                foreach ($messageList as $keyIndex=>$messageInfo){
                    $createSupportTicket = true;
                    $messageRefId   = 0;
                    $attachmentPaths = "";
                    $summary        = isset($messageInfo['summary'])?$messageInfo['summary']:"";
                    $subject        = isset($messageInfo['subject'])?$messageInfo['subject']:"";
                    $messageId      = isset($messageInfo['messageId'])?$messageInfo['messageId']:"";
                    $folderId       = isset($messageInfo['folderId'])?$messageInfo['folderId']:"";
                    $hasAttachment  = isset($messageInfo['hasAttachment'])?(int)$messageInfo['hasAttachment']:0;
                    $receivedTime   = isset($messageInfo['receivedTime'])?$messageInfo['receivedTime']:"";
                    $fromAddress    = isset($messageInfo['fromAddress'])?$messageInfo['fromAddress']:"";
                    $threadId       = isset($messageInfo['threadId'])?$messageInfo['threadId']:""; 
                    $toAddress       = isset($messageInfo['toAddress'])?$messageInfo['toAddress']:""; 
                    $ccAddress       = isset($messageInfo['ccAddress'])?$messageInfo['ccAddress']:""; 
                    $mailDomain = explode("@",$fromAddress);
                    if(isset($mailDomain[1]) && in_array(strtolower($mailDomain[1]),$mail_id_domain_skip_list) && $isInternalSupport=='N') {
                        continue;
                    }
                    if($this->checkSubjectAndSkipMail($subject, $fromAddress)){
                        continue;
                    }
                    if(isset($mailDomain[1]) && $mailDomain[1]=="gofrugal.com" && $isInternalSupport=='N'){
                        $createSupportTicket = false;
                    }
                    $this->getAPIUrls(3, $this->accountNumber,$folderId,$messageId);   
                    //Pull mail content in HTML format
                    $apiUrl = $this->apiURL."?includeBlockContent=true";
                    $contentResponse = $client->fetch($apiUrl,array(),OAuth2\Client::HTTP_METHOD_GET);
                    $this->mailAPIResponseLog($contentResponse);
                    if(isset($contentResponse['result']['data']['content'])){
                        $summary = $contentResponse['result']['data']['content'];
                    }else if(isset($contentResponse['result']['data']['blockContent'])){
                        $summary = $contentResponse['result']['data']['blockContent'];
                    }
                    $summary= str_replace("\r\n", "", $summary);
                    //Pull the attachment files if exist
                    if($hasAttachment){
                        $attachmentPathsList = $this->toGetAttachmentFiles($folderId, $messageId);
                        $attachmentPaths = implode(',', $attachmentPathsList);
                    }
                    $insertMessage = array();
                    $insertMessage['GCS_SUPPORT_TEAM_ID'] = $supportRefId;
                    $insertMessage['GCS_MESSAGE_ID'] = $messageId;
                    $insertMessage['GCS_FROM_MAIL_ID'] = $fromAddress;
                    $insertMessage['GCS_SUBJECT'] = $subject;
                    $insertMessage['GCS_CONTENT'] = $summary;
                    $insertMessage['GCS_ATTACHEMENT_PATH'] = $attachmentPaths;
                    $insertMessage['GCS_OWNER_EMP'] = $supportOwner;
                    $insertMessage['GCS_MAIL_STATUS'] = 1;
                    if($threadId == "" && (!$this->isExistingMessage($messageId))){                        
                        $messageRefId = array_insert_query("gft_customer_mail_hdr", $insertMessage);                         
                    }else if($threadId != '' && $threadId != '0'){
                        $messageRefId = $this->getThreadMessageId($threadId);
                        execute_my_query("UPDATE gft_customer_mail_hdr SET GCS_MAIL_STATUS=4 WHERE GCS_ID='$messageRefId' AND GCS_MAIL_STATUS IN(3, 2)");
                        if($messageRefId == ''){// For pulling existing ongoing thread mail
                            $insertMessage['GCS_MESSAGE_ID'] = $threadId;
                            $messageRefId = array_insert_query("gft_customer_mail_hdr", $insertMessage);
                        }
                    }
                    if($messageRefId>0){
                        insert_and_send_customer_mail($messageRefId,$summary,9999,1,2,'',$toAddress,$ccAddress,$fromAddress,1,$messageId);
                        if($createSupportTicket){
                            create_ticket_for_customer_mail($messageRefId,$fromAddress,'',$summary,$attachmentPaths);
                        }                        
                    }
                    //Update last updated timestamp
                    if($receivedTime != "" && is_numeric($receivedTime)){
                        execute_my_query("UPDATE gft_support_mail_master SET GSM_MAIL_CRON_ACCESS_TIME='$receivedTime' WHERE GSM_ID='$supportRefId'");
                    }
                }
            }else if(isset($mailListResponse['result']['status']['code']) && $mailListResponse['result']['status']['code']!='200'){
                mail_error_alert("Error found Zoho mail API",json_encode($mailListResponse));
            }
        }
    }
    /**
     * @param string $filePaths
     * 
     * @return string[string]
     */
    public function uploadFileToZoho($filePaths,$messageRefId){  
        global $attach_path;
        $allFileAttachmentResponse = array();
        $hdrs = array();
        $hdrs['Content-Type'] = "application/octet-stream";
        $client = new OAuth2\Client(self::CLIENT_ID, self::CLIENT_SECRET, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
        $cache_access_token = $this->apiAccess->getAccessToken(self::CLIENT_ID,self::CLIENT_SECRET,$this->refreshToken,self::SCOPE,'ZOHO_MAIL_ACCESS_TOKEN');
        $this->getAPIUrls(10, $this->accountNumber);
        $client->setAccessToken($cache_access_token);
        $client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_OAUTH);
        $filePathArray  = explode(",", $filePaths);
        $inc = 0;
        while(count($filePathArray)>$inc){
            $fileFullPath = stripcslashes($filePathArray[$inc]);
            $filePath = "$attach_path/Support_Upload_Files/".$fileFullPath;
            if(!file_exists($filePath)){
               mail_error_alert("Not able to send attachment","The attachment file is not found in the given path $filePath, the message id is $messageRefId");
               $allFileAttachmentResponse = array();
               break; 
            }
            $file_data = (file_get_contents($filePath));
            $fileName = basename($filePath); 
            $endPoint = $this->apiURL."?fileName=$fileName";
            $attachmentResponse = $client->fetch($endPoint,$file_data,OAuth2\Client::HTTP_METHOD_POST,$hdrs,2);
            if(isset($attachmentResponse['code']) && $attachmentResponse['code']!='200'){
                mail_error_alert("Error found Zoho mail API",json_encode($attachmentResponse));
            }else if(isset($attachmentResponse['result']['data']) && (count($attachmentResponse['result']['data'])>0)){
                $allFileAttachmentResponse[] = $attachmentResponse['result']['data'];
            }            
            $inc++;
        }
        return $allFileAttachmentResponse;
    }
    /**
     * @param string $ccMailIds
     * 
     * @return string
     */
    private function getValidEmailIds($ccMailIds){
        $validCcMailArray = array();
        $ccMailArray = explode(",", $ccMailIds);
        $len=count($ccMailArray);
        for ($i=0;$i<$len;$i++){
            if(is_valid_email(trim($ccMailArray[$i]))) {
                $validCcMailArray[] =  trim($ccMailArray[$i]);
            }
        }
        return implode(',', $validCcMailArray);
    }
    /**
     * @param int $messageRefId
     *
     * @return void
     */
    public function sendReplyToSupportMail($messageRefId){
        $messageQuery = " SELECT GMD_CONTENT,GCS_FROM_MAIL_ID,GCS_MESSAGE_ID,GSM_MAIL_REFRESH_TOKEN,".
            " GCS_SUBJECT,GSM_SUPPORT_MAIL_ID,GSM_ACCOUNT_ID, GMD_ATTACHEMENT_PATH, ".
            " GMD_TO_ADDRESS,GMD_CC_ADDRESS, GMD_MAIL_SEND_TYPE,GMD_HDR_ID, GMD_ACTIVITY_SUPPORT_ID FROM gft_customer_mail_dtl".
            " INNER JOIN gft_customer_mail_hdr ON(GCS_ID=GMD_HDR_ID)".
            " INNER JOIN gft_support_mail_master ON(GSM_ID=GCS_SUPPORT_TEAM_ID) WHERE GMD_ID='$messageRefId' AND GMD_MAIL_STATUS=1";
        $messageResult = execute_my_query($messageQuery);
        if($messageRow=mysqli_fetch_assoc($messageResult)){
            $filePathArray = array();
            $messageId      = $messageRow['GCS_MESSAGE_ID'];
            $mailContent    = $messageRow['GMD_CONTENT'];
            $refreshToken   = $messageRow['GSM_MAIL_REFRESH_TOKEN'];
            $fromMailId     = $messageRow['GSM_SUPPORT_MAIL_ID'];
            $toMailId       = $messageRow['GCS_FROM_MAIL_ID'];
            $subject        = $messageRow['GCS_SUBJECT'];
            $filePaths      = $messageRow['GMD_ATTACHEMENT_PATH'];
            $ccMailIds      = $messageRow['GMD_CC_ADDRESS'];
            $toMailIds      = $messageRow['GMD_TO_ADDRESS'];
            $mailSendType   = $messageRow['GMD_MAIL_SEND_TYPE'];
            $supportId      = (int)$messageRow['GMD_ACTIVITY_SUPPORT_ID'];
            $this->accountNumber = $messageRow['GSM_ACCOUNT_ID'];
            $headerId = $messageRow['GMD_HDR_ID'];
            $this->refreshToken = $refreshToken;
            $sql_get_last_message_id = " select GMD_MESSAGE_ID from gft_customer_mail_hdr ". 
                                       " LEFT JOIN gft_customer_mail_dtl ON(GMD_HDR_ID=GCS_ID AND GMD_MESSAGE_ID!='') ".
                                       " where GCS_ID='$headerId' ORDER BY GMD_ID DESC LIMIT 1";
            $lastMessageId = get_single_value_from_single_query("GMD_MESSAGE_ID", $sql_get_last_message_id);
            $lastMessageId = $lastMessageId!=""?$lastMessageId:$messageId;
            if($filePaths!=""){
                $filePathArray = $this->uploadFileToZoho($filePaths,$messageRefId);
                if(count($filePathArray)==0){
                    execute_my_query("UPDATE gft_customer_mail_dtl SET GMD_MAIL_STATUS=3 where GMD_ID='$messageRefId'");
                    return;
                }
            }
            $client = new OAuth2\Client(self::CLIENT_ID, self::CLIENT_SECRET, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
            $cache_access_token = $this->apiAccess->getAccessToken(self::CLIENT_ID,self::CLIENT_SECRET,$this->refreshToken,self::SCOPE,'ZOHO_MAIL_ACCESS_TOKEN');
            $client->setAccessToken($cache_access_token);
            $client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_OAUTH);
            $messageDtl = array (
                //'action'=>'reply',
                'content' => $mailContent,
                'mailFormat'=>'html',
                //'additionalToAddress'=>'m.t.ashok@gmail.com',
                //'additionalCcAddress'=>'m.t.ashok@gmail.com'
            );
            if($mailSendType == '2'){// Send as a new mail
                $this->getAPIUrls(11, $this->accountNumber,'','');
                $messageDtl['fromAddress'] = $fromMailId;
                $messageDtl['toAddress'] = $this->getValidEmailIds($toMailIds);
                $messageDtl['subject'] = $subject;
                $ccMailIds = $this->getValidEmailIds($ccMailIds);
                if($ccMailIds!=""){
                    $messageDtl['ccAddress'] = $ccMailIds;
                }
            }else{ //Reply mail
                $this->getAPIUrls(6, $this->accountNumber,'',$lastMessageId);
                $messageDtl['action'] = "replyall";
                $messageDtl['subject'] = ($supportId>0?"[#$supportId] ":"").$subject;
                $ccMailIds = $this->getValidEmailIds($ccMailIds);
                if($ccMailIds!=""){
                    //$messageDtl['additionalCcAddress'] = $ccMailIds;
                    $messageDtl['ccAddress'] = $ccMailIds;                    
                }
            }
            if(count($filePathArray)>0){
                $messageDtl['attachments'] = $filePathArray;
            }
            $hdrs = array();
            $hdrs['Content-Type'] = "application/json";
            $replyResponse = $client->fetch($this->apiURL,$messageDtl,OAuth2\Client::HTTP_METHOD_POST,$hdrs,2);
            $this->mailAPIResponseLog($replyResponse);
            if(isset($replyResponse['result']['status']['code']) && $replyResponse['result']['status']['code']=='200'){
                execute_my_query("UPDATE gft_customer_mail_dtl SET GMD_MAIL_STATUS=2 where GMD_ID='$messageRefId'");
                mail_send_util($fromMailId,$toMailId,$subject,$mailContent,1,8,'','',$fromMailId,true,330);
                $messageId = isset($replyResponse['result']['data']['messageId'])?$replyResponse['result']['data']['messageId']:"";
                if($mailSendType == '2'){                    
                    execute_my_query("UPDATE gft_customer_mail_hdr SET GCS_MESSAGE_ID='$messageId' WHERE GCS_ID='$headerId'");
                }
                execute_my_query("UPDATE gft_customer_mail_dtl SET GMD_MESSAGE_ID='$messageId' WHERE GMD_ID='$messageRefId'");
            }else if(isset($replyResponse['result']['status']['code']) && $replyResponse['result']['status']['code']!='200'){
                execute_my_query("UPDATE gft_customer_mail_dtl SET GMD_MAIL_STATUS=3 where GMD_ID='$messageRefId'");
                $mail_body = "Request details: ".json_encode($messageDtl)." Response details: ".json_encode($replyResponse);
                mail_error_alert("Error found Zoho mail API",$mail_body);
            }
        }
    }
    /**
     * @param string $messageRefId
     * @param int $type
     * 
     * @return void
     */
    public function updateMailAsSpam($messageRefId, $type) {
        $spamQuery =    " select GCS_MESSAGE_ID, GSM_MAIL_REFRESH_TOKEN,GSM_ACCOUNT_ID,GSM_MAIL_FOLDER_ID from gft_customer_mail_hdr ".  
                        " INNER JOIN gft_support_mail_master ON(GCS_SUPPORT_TEAM_ID=GSM_ID) ".
                        " where GCS_ID='$messageRefId'";
        $spamResult = execute_my_query($spamQuery);
        if($spamRow=mysqli_fetch_assoc($spamResult)){
            $this->refreshToken = $spamRow['GSM_MAIL_REFRESH_TOKEN'];
            $this->accountNumber = $spamRow['GSM_ACCOUNT_ID'];
            $this->folderId = $spamRow['GSM_MAIL_FOLDER_ID'];
            $messageId = (int)$spamRow['GCS_MESSAGE_ID'];
            $this->getAPIUrls(8, $this->accountNumber);
            $client = new OAuth2\Client(self::CLIENT_ID, self::CLIENT_SECRET, OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
            $cache_access_token = $this->apiAccess->getAccessToken(self::CLIENT_ID,self::CLIENT_SECRET,$this->refreshToken,self::SCOPE,'ZOHO_MAIL_ACCESS_TOKEN');
            $client->setAccessToken($cache_access_token);
            $client->setAccessTokenType(OAuth2\Client::ACCESS_TOKEN_OAUTH);
            $hdrs = array();
            $hdrs['Content-Type'] = "application/json";
            $mode = $type==1?"moveToSpam":"markNotSpam";
            $spamResponse = $client->fetch($this->apiURL,array('messageId'=>array($messageId),'mode'=>"$mode",'isArchive'=>true),OAuth2\Client::HTTP_METHOD_PUT,$hdrs,2);
            $this->mailAPIResponseLog($spamResponse);
            if(isset($spamResponse['result']['status']['code']) && $spamResponse['result']['status']['code']=='200'){
                execute_my_query("UPDATE gft_customer_mail_hdr SET GCS_MAIL_STATUS='6' WHERE GCS_ID='$messageRefId'");
            }else if(isset($spamResponse['result']['status']['code']) && $spamResponse['result']['status']['code']!='200'){                
                mail_error_alert("Error found Zoho mail API",json_encode($spamResponse));
            }
        }
    }
    /**
     * @param string $supportRef
     * @param string $mailContent
     * 
     * @return string
     */
    public function getInlineImageContent($supportRef,$mailContent){
        $paramDtl = array();
        $baseUrl = "https://mail.zoho.com";
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($mailContent);
        $resultAccount = execute_my_query("select GSM_MAIL_REFRESH_TOKEN,GSM_ACCOUNT_ID  from gft_support_mail_master where GSM_ID='$supportRef'");
        if(mysqli_num_rows($resultAccount)==0){
            return '';
        }
        $rowAccount = mysqli_fetch_assoc($resultAccount);
        $this->refreshToken = $rowAccount['GSM_MAIL_REFRESH_TOKEN'];
        $this->accountNumber = $rowAccount['GSM_ACCOUNT_ID'];
        foreach ($doc->getElementsByTagName('img') as $item) {
            $imageUrl = str_replace("ImageDisplay", "ImageDisplayForMobile", $item->getAttribute('src'));
            $apiUrl = $baseUrl.$imageUrl;
            parse_str($apiUrl, $paramDtl);
            if(isset($paramDtl['f']) && $paramDtl['f']!=""){
                $fileNameDtl = explode('.', $paramDtl['f']);  
                $length = (count($fileNameDtl)-1);
                $cache_access_token = $this->apiAccess->getAccessToken(self::CLIENT_ID,self::CLIENT_SECRET,$this->refreshToken,self::SCOPE,'ZOHO_MAIL_ACCESS_TOKEN');
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"$apiUrl");
                $headers = array('Authorization: Zoho-oauthtoken '.$cache_access_token);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($ch, CURLOPT_SSLVERSION, 6);                
                $server_output = curl_exec ($ch);
                curl_close ($ch);                
                $imageData = base64_encode($server_output);
                $fileExtension = isset($fileNameDtl[$length])?$fileNameDtl[$length]:"";
                $src = "data: image/$fileExtension;base64,".$imageData;
                $item->setAttribute('src', $src);
            }
        }
        $html = $doc->saveHTML();
        return $html;
    }
}
$zoho = new zohoMailAPI();
print_r($zoho->pullMails());
