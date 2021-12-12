<?php
require_once(__DIR__ ."/../dbcon.php"); 

/*. require_module 'libxml'; .*/
/*. require_module 'simplexml'; .*/

/**
 * @param string $NDNC_CHECK  Y/N
 * 
 * @return string[string]
 */
function get_netcore_account_dtl($NDNC_CHECK){
	
	$query_netcore_account_dtl="select GNA_USERNAME,GNA_PASSWORD,GNA_FEED_ID,GNA_BRAND_NAME from gft_netcore_account_dtl " .
			"where  GNA_NDNC_CHECK='$NDNC_CHECK' ";
	$result_netcore_account_dtl=execute_my_query($query_netcore_account_dtl);
	if(mysqli_num_rows($result_netcore_account_dtl)==0){
		exit;
	}
	$qdata_netcore_acc=mysqli_fetch_array($result_netcore_account_dtl);
	$account_dtl['USERNAME']=$qdata_netcore_acc['GNA_USERNAME'];
	$account_dtl['PASSWORD']=$qdata_netcore_acc['GNA_PASSWORD'];
	$account_dtl['FEED_ID']=$qdata_netcore_acc['GNA_FEED_ID'];
	$account_dtl['BRAND_NAME']=$qdata_netcore_acc['GNA_BRAND_NAME'];
	return $account_dtl;
		
}

/*. require_module 'libxml'; .*/
/*. require_module 'simplexml'; .*/

/**
 * @param string $NDNC_CHECK  Y/N
 *
 * @return string
 */
function get_kaleyra_account_dtl($NDNC_CHECK){
    $arr['end_point'] 	= "https://api-alerts.kaleyra.com/v4/?";
    $arr['sender']		= "GOFRUG";
    $arr['api_key'] 	= "A5fc01b527d55d49f3a65a1df4a284e81";
    $arr['gateway_type'] 	= "3";
    if($NDNC_CHECK=='Y'){
        $arr['end_point'] 	= "https://api-alerts.kaleyra.com/v4/?";
        $arr['sender']		= "GOFRUG";
        $arr['api_key'] 	= "A59db5b5e1c367ab0f4d42fea0a8c2635";
        $arr['gateway_type'] 	= "4";
    }
    return $arr;
}

/**
 * @param string $NDNC_CHECK
 * @param string $sms_id
 *
 * @return void
 */

function send_sms_kaleyra_by_smsid($NDNC_CHECK='N',$sms_id=''){
    $block_sms_category='';   
    $test_mode = get_samee_const('STORE_PAYMENT_TEST_MODE');
    $domain = "labtest.gofrugal.com";
    if($test_mode=='0'){
        $domain = 'sam.gofrugal.com';
    }
        $sms_id_filter='';
        if ($sms_id != ''){
            $sms_id_filter=" and gos_id=".$sms_id;
        }
        
        $max_char=(int)get_samee_const('CONST_MAX_NO_CHARACTER_FOR_A_SMS');
        $sql=" select gos_id,gos_sms_content,gos_msg_sent_time,gos_receiver_mobileno,gos_sent_to_alert,gsc_priority," .
            " GSC_VALIDITY_PERIOD_HRS,GSC_DESC,GSS_NAME,GOS_ACTIVITY_ID" .
            " FROM  gft_sending_sms " .
            " join gft_sms_config on(gos_category=GSC_ID and GSC_STATUS='A') " .
            " join gft_sms_sender_name_list snd on (GSS_ID= GSC_SENDER_AS)" .
            " where gos_sent_to_alert=0 and gos_sms_content!='' and gos_receiver_mobileno > 1000000000 and gos_sms_status=0 " .
            " and gos_msg_sent_time > date(now())" .
            " $block_sms_category and GSC_CHECK_NDNC='$NDNC_CHECK' " .
            " $sms_id_filter " .
            " limit 2000 ";
        $resultsms=execute_my_query($sql);
        $count_num_rows=mysqli_num_rows($resultsms);
        if($count_num_rows==0){
            exit;
        }
        $account_dtl = get_kaleyra_account_dtl($NDNC_CHECK);
        $gatewayUrl = $account_dtl['end_point'];
        $api_key   = $account_dtl['api_key'];
        $gate_type = $account_dtl['gateway_type'];
        $today=date('Y-m-d H:i:s');
        $lc=0;
        $lc1=0;
        global $conn;
        $gid=/*. (string[int]) .*/ array();
        $gid_act	=	/*. (string[int]) .*/ array();
        $full_act_list=	/*. (string[int]) .*/ array();
        $sms_json_list = array();
        while($row=mysqli_fetch_array($resultsms)){
            if(strlen(trim($row['gos_sms_content']))<= $max_char){
                $content_esc=mysqli_real_escape_string_wrapper(trim($row['gos_sms_content']));
                $gos_receiver_mobileno=$row['gos_receiver_mobileno'];
                $today_date_val=date('Y-m-d');
                
                $check_query="SELECT gos_id from gft_sending_sms_temp where gos_sms_content='$content_esc' and gos_msg_sent_date='".$today_date_val."' and gos_receiver_mobileno='$gos_receiver_mobileno'";
                $check_result=execute_my_query($check_query);
                if($check_data=mysqli_fetch_array($check_result)){
                    $update_dublicate="update gft_sending_sms set gos_sms_status='4' where gos_id='".$row['gos_id']."' ";
                    execute_my_query($update_dublicate);
                    if($row['GOS_ACTIVITY_ID']!=0 and $row['GOS_ACTIVITY_ID']!=''){
                        update_sms_status_inactivity($row['GOS_ACTIVITY_ID'], 'Duplicate');
                    }
                    continue;
                }
                
                //indifiy dublicates by entering into primary key enable table
                $insert_temp="insert into gft_sending_sms_temp " .
                    "(gos_id ,gos_sms_content ,gos_msg_sent_date ,gos_receiver_mobileno ,gos_sent_to_alert) values " .
                    " (".$row['gos_id'].",'$content_esc','$today_date_val','".$row['gos_receiver_mobileno']."',0)";
                $result_temp=execute_my_query($insert_temp,'netcore_util.php',false);
                $affected_rows=mysqli_affected_rows_wrapper();
                if($affected_rows==-1){
                    //NOTE: The following case should not happen...... as the duplicate is handled above....
                    /* status 4 is dublicate */
                    $update_dublicate="update gft_sending_sms set gos_sms_status='4' where gos_id='".$row['gos_id']."' ";
                    execute_my_query($update_dublicate);
                    continue;
                }
                
                $mobileno=trim($row['gos_receiver_mobileno']);
                $msg=(trim($row['gos_sms_content']));
                $gid[$lc]=$row['gos_id'];
                $full_act_list[$lc]	=	$row['GOS_ACTIVITY_ID'];
                $msg_unique_id=$row['gos_id'];
                $tag_name=$row['GSC_DESC'];
                $sender=$row['GSS_NAME'];
                $tag_name=substr($tag_name,0,25);
                $sms_count = ceil((strlen($msg)/160));
                $sms_count_dtl = array('GSD_SMS_ID'=>$row['gos_id'],'GSD_COUNT'=>"$sms_count",'GSD_GATEWAY_TYPE'=>"$gate_type");
                array_insert_query("gft_sent_sms_dtl", $sms_count_dtl);
                if($row['GOS_ACTIVITY_ID']>0){
                    $gid_act[$lc1]	=	$row['GOS_ACTIVITY_ID'];// get activity based sms send list
                    $lc1++;
                }
                $sms_json = array();
                $sms_json['to'] = "$mobileno";
                $sms_json['msgid'] = "$msg_unique_id";
                $sms_json['message'] = "$msg";
                $sms_json['sender'] = "$sender";
                /* $sms_json['custom1'] = "";
                $sms_json['custom2'] = ""; */
                $sms_json_list[] = $sms_json;
                $lc++;
            }else{
                /* status 5 is Exceed Content limit */
                execute_my_query("update gft_sending_sms set gos_sms_status='5' where gos_id='".$row['gos_id']."'");
            }
        }/* end of while */
        if($lc==0){
            exit;
        }
        if($lc>0){
            $req_gen="insert into  gft_netcore_request (GNR_ID,GNR_TIME,GNR_RECORDS,GSC_CHECK_NDNC) values ('',now(),$lc,'$NDNC_CHECK')";
            $result_req_gen=execute_my_query($req_gen);
            $request_id_sam=mysqli_insert_id_wrapper();
            $str_gid=implode(',',$gid);
            $sql_update="update gft_sending_sms set GOS_ACCOUNT_ID=$gate_type,gos_status_updated_time='$today',gos_sent_to_alert=1 ".
                ",GOS_SAM_REQUEST_ID='$request_id_sam',gos_sms_status=7 where gos_id in ($str_gid) ";
            execute_my_query($sql_update);
            if(!empty($gid_act)){
                $str_gid_act=implode(',',$gid_act);
                if($str_gid_act!=''){
                    execute_my_query("UPDATE gft_activity SET GLD_SMS_DELIVERY_STATUS='Waiting for Status Update' WHERE GLD_ACTIVITY_ID in($str_gid_act)");
                }
            }
            $sql_update_temp="update gft_sending_sms_temp set gos_sent_to_alert=1 ".
                " where gos_id in ($str_gid)";
            execute_my_query($sql_update_temp);
            
            execute_my_query("delete FROM gft_sending_sms_temp WHERE gos_msg_sent_date< date(now()) ");
            $sms_json_to_post = array();
            $sms_json_to_post['message'] = "GOFRUGAL SMS";
            $sms_json_to_post['sms'] = $sms_json_list;
            $sms_json_to_post['flash'] = 0;
            $sms_json_to_post['unicode'] = 0;
            $sms_json_to_post['dlrurl'] = "https://$domain/sms_delivery_update.php?delivered={delivered}";    
            $post_json_data = json_encode($sms_json_to_post);
            $post_data = "api_key=$api_key&method=sms.json&json=".urlencode(json_encode($sms_json_to_post));
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => "$gatewayUrl",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $post_data,
            ));            
            $api_response = (string)curl_exec($ch);
            curl_close($ch);
            if($api_response!=""){
                $filename="sms_send_".$request_id_sam.".txt";
                $filename1="sms_request_send_".$request_id_sam.".txt";
                write_to_file('netcore_response',$api_response,$filename,'w',false);
                write_to_file('netcore_response',$post_json_data,$filename1,'w',false);
                $json_arr = json_decode($api_response,true);
                $resp_status = isset($json_arr['status'])?$json_arr['status']:"";
                $message = isset($json_arr['message'])?$json_arr['message']:"";
                $data    = isset($json_arr['data'])?$json_arr['data']:null;
                if($resp_status=="OK"){
                    $lc = 0; 
                    foreach ($data as $sms_key=>$sms_obj) {
                        $sms_sent_status = isset($sms_obj['status'])?$sms_obj['status']:"";
                        $sms_sent_id = isset($sms_obj['customid'])?(int)$sms_obj['customid']:0;
                        $tid = isset($sms_obj['id'])?$sms_obj['id']:"";
                        if($sms_sent_id>0){
                            $status_array = array('AWAITED-DLR'=>7,'INV-NUMBER'=>12,'FAILED'=>2,'DNDNUMB'=>10);
                            $status_to_update = isset($status_array["$sms_sent_status"])?$status_array["$sms_sent_status"]:2;
                            $update_query = " update gft_sending_sms set gos_sent_to_alert=1,gos_sms_status='$status_to_update',". 
                                " gos_status_updated_time=now(), GOS_SAM_REQUEST_ID=$request_id_sam where gos_id='$sms_sent_id' ";
                            execute_my_query($update_query);
                            execute_my_query("UPDATE gft_sent_sms_dtl SET GSD_GATEWAY_STATUS='$sms_sent_status',GSD_TID='$tid'  WHERE GSD_SMS_ID='$sms_sent_id'");
                            if($full_act_list[$lc]>0){
                                $act_sms_status	=	'Waiting for Status Update';
                                if($status_to_update==2){$act_sms_status	=	'Failed';}
                                else if($status_to_update==10){$act_sms_status	=	'Registered for NDNC';}
                                else if($status_to_update==11){$act_sms_status	=	'NDNC Status is currently unavailable';}
                                else if($status_to_update==12){$act_sms_status	=	'Invalid Number';}
                                update_sms_status_inactivity($full_act_list[$lc], $act_sms_status);
                            }
                        } 
                        $lc++;
                    }  
                    $update_request_id="update gft_netcore_request set GNR_RECORDS=(select count(*) from gft_sending_sms where GOS_SAM_REQUEST_ID=$request_id_sam)  where GNR_ID='$request_id_sam' ";
                    execute_my_query($update_request_id);
                    
                    /*incase of NDNC update in customer_contact_dtl */
                    $update_ndnc=" update gft_customer_contact_dtl,gft_sending_sms set GCC_NDNC_ON='Y' " .
                        " where gos_receiver_mobileno=gcc_contact and GOS_SAM_REQUEST_ID=$request_id_sam and gos_sms_status=10 ";
                    execute_my_query($update_ndnc);
                    
                    /*incase of invalid number in customer_contact_dtl */
                    $update_ndnc=" update gft_customer_contact_dtl,gft_sending_sms set GCC_CAN_SEND='N' " .
                        " where gos_receiver_mobileno=gcc_contact and GOS_SAM_REQUEST_ID=$request_id_sam and gos_sms_status=12 ";
                    execute_my_query($update_ndnc);
                }else{
                    execute_my_query("update gft_sending_sms set gos_sms_status=8 where GOS_SAM_REQUEST_ID=$request_id_sam");
                    execute_my_query("update gft_netcore_request set GNR_RECORDS=(select count(*) from gft_sending_sms where GOS_SAM_REQUEST_ID=$request_id_sam)  where GNR_ID='$request_id_sam' ");
                    mail_error_alert('Kaleyra SMS Response',$message.$api_response,7,null);
                }
            }
            
        }
}

/**
 * @param string $NDNC_CHECK
 * @param string $sms_id
 * 
 * @return void
 */

function send_sms_netcore_by_smsid($NDNC_CHECK='N',$sms_id=''){
	$block_sms_category='';

	//This check is not required. Need to review.
	//if((int)date('H')<8 or (int)date('H')>20){
	//	$block_sms_category=" and gsc_priority='H' ";
	//}

	$sms_id_filter='';
	if ($sms_id != ''){
		$sms_id_filter=" and gos_id=".$sms_id;
	}

	$max_char=(int)get_samee_const('CONST_MAX_NO_CHARACTER_FOR_A_SMS');
	$sql=" select gos_id,gos_sms_content,gos_msg_sent_time,gos_receiver_mobileno,gos_sent_to_alert,gsc_priority," .
			" GSC_VALIDITY_PERIOD_HRS,GSC_DESC,GSS_NAME,GOS_ACTIVITY_ID" .
			" FROM  gft_sending_sms " .
			" join gft_sms_config on(gos_category=GSC_ID and GSC_STATUS='A') " .
			" join gft_sms_sender_name_list snd on (GSS_ID= GSC_SENDER_AS)" .
			" where gos_sent_to_alert=0 and gos_sms_content!='' and gos_receiver_mobileno > 1000000000 and gos_sms_status=0 " .
			" and gos_msg_sent_time > date(now())" .
			" $block_sms_category and GSC_CHECK_NDNC='$NDNC_CHECK' " .
			" $sms_id_filter " .
			" limit 2000 "; 
//error_log("aaaa sql=".$sql);
	$resultsms=execute_my_query($sql);
	$count_num_rows=mysqli_num_rows($resultsms);
	if($count_num_rows==0){
		exit;
	}
	/*ACCOUNT DTL START */
	$account_dtl=get_netcore_account_dtl($NDNC_CHECK);
	$username=$account_dtl['USERNAME'];
	$password=$account_dtl['PASSWORD'];
	$feedid=$account_dtl['FEED_ID'];
$xml_header=<<<END
<!DOCTYPE REQ SYSTEM 'http://bulkpush.mytoday.com/BulkSms/BulkSmsV1.00.dtd'>
<REQ><VER>1.0</VER><USER>
<USERNAME>$username</USERNAME>
<PASSWORD>$password</PASSWORD>
</USER><ACCOUNT>
<ID>$feedid</ID>
</ACCOUNT>
END;
	/* ACCOUNT DTL END */	
	$today=date('Y-m-d H:i:s');
	$lc=0;
	$lc1=0;
	global $conn;
	$gid=/*. (string[int]) .*/ array();
	$gid_act	=	/*. (string[int]) .*/ array();
	$full_act_list=	/*. (string[int]) .*/ array();
	$return_xml='';
	while($row=mysqli_fetch_array($resultsms)){
	    if(strlen(trim($row['gos_sms_content']))<= $max_char){
	    	$content_esc=mysqli_real_escape_string_wrapper(trim($row['gos_sms_content']));
		$gos_receiver_mobileno=$row['gos_receiver_mobileno'];
		$today_date_val=date('Y-m-d');

		$check_query="SELECT gos_id from gft_sending_sms_temp where gos_sms_content='$content_esc' and gos_msg_sent_date='".$today_date_val."' and gos_receiver_mobileno='$gos_receiver_mobileno'";
		$check_result=execute_my_query($check_query);
		if($check_data=mysqli_fetch_array($check_result)){
			$update_dublicate="update gft_sending_sms set gos_sms_status='4' where gos_id='".$row['gos_id']."' ";
			execute_my_query($update_dublicate);
			if($row['GOS_ACTIVITY_ID']!=0 and $row['GOS_ACTIVITY_ID']!=''){
				update_sms_status_inactivity($row['GOS_ACTIVITY_ID'], 'Duplicate');
			}
			continue;
		}

			//indifiy dublicates by entering into primary key enable table
			$insert_temp="insert into gft_sending_sms_temp " .
				"(gos_id ,gos_sms_content ,gos_msg_sent_date ,gos_receiver_mobileno ,gos_sent_to_alert) values " .
				" (".$row['gos_id'].",'$content_esc','$today_date_val','".$row['gos_receiver_mobileno']."',0)";
		 	$result_temp=execute_my_query($insert_temp,'netcore_util.php',false);
		 
		 	$affected_rows=mysqli_affected_rows_wrapper();
			if($affected_rows==-1){
				//NOTE: The following case should not happen...... as the duplicate is handled above....
				/* status 4 is dublicate */
		 		$update_dublicate="update gft_sending_sms set gos_sms_status='4' where gos_id='".$row['gos_id']."' ";
		 		execute_my_query($update_dublicate);
		 		continue;
		 	}
		
			$mobileno=trim($row['gos_receiver_mobileno']);
			$msg=htmlentities(trim($row['gos_sms_content']));
			$gid[$lc]=$row['gos_id'];
			$full_act_list[$lc]	=	$row['GOS_ACTIVITY_ID'];	
			$msg_unique_id=$row['gos_id'];
			$priority=$row['gsc_priority'];
			$validity_period=(int)$row['GSC_VALIDITY_PERIOD_HRS'];
			//if($validity_period==''){$validity_period=0;}
			//else { $validity_period=$validity_period*60; }
			$validity_period=$validity_period*60; 
			$msg_unique_id_per_request=$msg_unique_id;
			$type=0; /*0 -text message,1-flash message */
			$tag_name=$row['GSC_DESC'];
			$sender=$row['GSS_NAME'];
			$tag_name=substr($tag_name,0,25);
			if($row['GOS_ACTIVITY_ID']>0){
				$gid_act[$lc1]	=	$row['GOS_ACTIVITY_ID'];// get activity based sms send list
				$lc1++;
			}		
$return_xml.=<<<END
<MESSAGE>
	<TAG>$tag_name</TAG>
	<TEXT>$msg</TEXT>
	<DLR>1</DLR>
	<TYPE>$type</TYPE>
	<MID>$msg_unique_id_per_request</MID>
	<VALIDITY>$validity_period</VALIDITY>            	
	<SMS FROM="$sender" TO="$mobileno" INDEX ="$msg_unique_id"></SMS>
</MESSAGE>
END;
		$lc++;
	}else{
		/* status 5 is Exceed Content limit */
   		execute_my_query("update gft_sending_sms set gos_sms_status='5' where gos_id='".$row['gos_id']."'");
   	}
}/* end of while */
$xml_footer=<<<END
</REQ>
END;
	if($return_xml=='' or $lc==0){
		exit;
	}
	if($lc>0){
		$req_gen="insert into  gft_netcore_request (GNR_ID,GNR_TIME,GNR_RECORDS,GSC_CHECK_NDNC) values ('',now(),$lc,'$NDNC_CHECK')";
		$result_req_gen=execute_my_query($req_gen);
		$request_id_sam=mysqli_insert_id_wrapper();
		$str_gid=implode(',',$gid);
		$sql_update="update gft_sending_sms set GOS_ACCOUNT_ID=$feedid,gos_status_updated_time='$today',gos_sent_to_alert=1 ".
		      ",GOS_SAM_REQUEST_ID='$request_id_sam',gos_sms_status=7 where gos_id in ($str_gid) ";
		execute_my_query($sql_update);
		if(!empty($gid_act)){
			$str_gid_act=implode(',',$gid_act);
			if($str_gid_act!=''){
				execute_my_query("UPDATE gft_activity SET GLD_SMS_DELIVERY_STATUS='Waiting for Status Update' WHERE GLD_ACTIVITY_ID in($str_gid_act)");
			}
		}
		$sql_update_temp="update gft_sending_sms_temp set gos_sent_to_alert=1 ".
		      " where gos_id in ($str_gid)";
		execute_my_query($sql_update_temp);
	
		execute_my_query("delete FROM gft_sending_sms_temp WHERE gos_msg_sent_date< date(now()) ");
		
		$return_xml=$xml_header.$return_xml.$xml_footer;
		$url_encoded_xml=urlencode($return_xml);
    	$host = 'bulkpush.mytoday.com';
	    $port = 80;
		$path = "/BulkSms/SendSms";
		$use_get_method=false;
		if($use_get_method==true){
	  		$path .= "?UserRequest=".$url_encoded_xml;
	    	$out = "GET $path HTTP/1.0\r\nHost: $host\r\n\r\n Content-Type: text/xml \r\n\r\n  ";
		}else{
			$url_encoded_xml="UserRequest=".$url_encoded_xml;
		    $out = "POST $path HTTP/1.1\r\n";
		    $out .= "Host: $host\r\n";
		    $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		    $out .= "Content-Length: " . strlen($url_encoded_xml) . "\r\n";
		    $out .= "\r\n";
		    $out .= $url_encoded_xml;
		}
	    $fp = fsockopen($host, $port, $errno, $errstr, 30);
	 	if (!$fp){
	        print "Could not able to connect to $host";
	        exit;
	    }

//error_log("aaaa out=$out");
	    if(fwrite($fp, $out)=== FALSE){
	    	print "fwrite error $host";
	        exit;
	    }
	    $request_return='';
	    while (!feof($fp)) {
	        $request_return.=fgets($fp, 128);
	    }
//error_log("aaaa request_return=$request_return");
	    if(count($request_return) >0 and $request_return!=''){
	    	$filename="sms_send_".$request_id_sam.".txt";
        	write_to_file($folder_name='netcore_response',$content=$request_return,$filename,$mode='w',false);
			$request_response=http_parse_message($request_return);
			$request_return=$request_response->body;
			if(count($request_return)){
				$xml= /*. (SimpleXMLElement) .*/ null;
				libxml_use_internal_errors(true);
				try{
					$xml=simplexml_load_string($request_return);
				}catch(Exception $e){
					mail_error_alert('Netcore Response',$e->getMessage().$request_return,$mail_category=7,$cc=null);
				}
				if($xml!=null and $xml->getName()=='RESULT'){
					if(!isset($xml['REQID'])){
						/* 8- Netcore Error*/
						execute_my_query("update gft_sending_sms set gos_sms_status=8 where GOS_SAM_REQUEST_ID=$request_id_sam");
						execute_my_query("update gft_netcore_request set GNR_RECORDS=(select count(*) from gft_sending_sms where GOS_SAM_REQUEST_ID=$request_id_sam)  where GNR_ID='$request_id_sam' ");
						mail_error_alert("Netcore Error Response",$request_return,7,array(get_samee_const("ADMIN_TEAM_MAIL_ID"),get_samee_const("PRESALES_MAIL_ID")));
					}else{
						$request_id=$xml['REQID'];
						$lc=0;
						foreach($xml->children() as $child){
						    if($child->getName()!='REQUEST-ERROR'){
								$status=7;
								foreach($child->children() as $granchild){
									if($granchild->getName()=='ERROR'){
										$status=2;
										foreach($granchild->children() as $granchild1){
											if($granchild1->getName()=='ERROR'){
												foreach($granchild1->children() as $grangranchild){
													if($grangranchild->getName()=='CODE'){
														switch($grangranchild){
															case '131':
																$status=11; /*NDNC status not available*/
																break;
															case '132':
																$status=10; /*NDNC on*/
																break;
															case '108':
																$status=12;  /*Invalid Number*/
																break;
															default:
																//Do nothing.
																//NOTE: Already $status value set to 2
																break;
																			
														}
													}
												}
											}
										}
									}
								}
								$sql_update="update gft_sending_sms set gos_status_updated_time='".$child['SUBMITDATE']."',gos_sms_status=$status, GOS_SAM_REQUEST_ID=$request_id_sam, GOS_TID='".$child['TID']."' where gos_id =".$gid[$lc]." ";
								execute_my_query($sql_update);						
								if($full_act_list[$lc]>0){
									$act_sms_status	=	'Waiting for Status Update';
									if($status==2){$act_sms_status	=	'Failed';}
									else if($status==10){$act_sms_status	=	'Registered for NDNC';}
									else if($status==11){$act_sms_status	=	'NDNC Status is currently unavailable';}
									else if($status==12){$act_sms_status	=	'Invalid Number';}
									update_sms_status_inactivity($full_act_list[$lc], $act_sms_status);
								}
							}
							$lc++;
						}
						$update_request_id="update gft_netcore_request set GOS_NETCORE_REQUEST_ID='$request_id',GNR_RECORDS=(select count(*) from gft_sending_sms where GOS_SAM_REQUEST_ID=$request_id_sam)  where GNR_ID='$request_id_sam' ";
						execute_my_query($update_request_id);
						
						/*incase of NDNC update in customer_contact_dtl */
						$update_ndnc=" update gft_customer_contact_dtl,gft_sending_sms set GCC_NDNC_ON='Y' " .
								" where gos_receiver_mobileno=gcc_contact and GOS_SAM_REQUEST_ID=$request_id_sam and gos_sms_status=10 ";
						execute_my_query($update_ndnc);
						
						/*incase of invalid number in customer_contact_dtl */
						$update_ndnc=" update gft_customer_contact_dtl,gft_sending_sms set GCC_CAN_SEND='N' " .
								" where gos_receiver_mobileno=gcc_contact and GOS_SAM_REQUEST_ID=$request_id_sam and gos_sms_status=12 ";
						execute_my_query($update_ndnc);
								
					}
				}
				libxml_clear_errors();
			}
	    }
		fclose($fp);
	}
}

/**
 * @param string $NDNC_CHECK
 * 
 * @return void
 */
function send_sms_netcore($NDNC_CHECK='N'){
	send_sms_netcore_by_smsid($NDNC_CHECK,'');
}


?>
