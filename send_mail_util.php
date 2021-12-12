<?php
/*. require_module 'standard'; .*/
/*. require_module 'mysql'; .*/
/*. require_module 'json'; .*/

/*. require_module 'extra'; .*/

require_once(__DIR__ ."/dbcon.php");
include_once(__DIR__ ."/class.phpmailer.php"); //NOTE: No need to change to require_once . The definition is added in 'extra' require_module


/*. forward int function mail_send_util(string $email_from,string[] $email_to,string $subject,string $msg,string $mst,int $category,string $cc=,string $attachment=,string $reply_to=,boolean $content_type=,string $mail_template_id=,string $mail_compile_id=,string $attach_Embedded_file=,string $from_name=,string $event_id=,string $error_msg=); .*/

/**
 * @param string $email_from
 * @param string[int] $email_to
 * @param string $subject
 * @param string $msg
 * @param string $at_file
 * @param string $cc
 * @param int $category
 * @param boolean $content_type
 * @param string $reply_to
 * @param boolean $is_status_needed
 * @param boolean $crontab_call
 * @param string $fromname
 * @param string $mail_template_id
 * @param string $mail_compile_id
 * @param string $attach_Embedded_file_str
 * @param string $mail_method
 * @param int $priority
 * @return boolean
 */
function send_mail_function($email_from,$email_to,$subject,$msg,$at_file=null,$cc=null,$category=0,$content_type=false,
$reply_to=null,$is_status_needed=false,$crontab_call=false,$fromname=null,$mail_template_id=null,
$mail_compile_id=null,$attach_Embedded_file_str=null,$mail_method='',$priority=0){
	$local_testing=(get_samee_const("MAILSERVER_LOCAL_TESTING")!=''?true:false);
	/* dont add enable this constant in Server */
	if($email_to=='' or $email_to==null){
		return false;
	}

		$dir_var=dirname($_SERVER['PHP_SELF']);
		$directory=substr( $dir_var,strrpos($dir_var,'/')+1,strlen($dir_var)) ;
		/* if we configured the like store.gofrugal.com or sam.gofrugal.com directory is empty */
		$base_dir=(($directory!='sales_server' and $directory!='store_server' and $directory!='store' and $directory!='' )?'../':'');

	$mailer_object = new PHPMailer();
	$mailer_object->IsSMTP(); // telling the class to use SMTP
	if($mail_method=='sendMail') {
		$mailer_object->IsSendmail();
	}
	$mailer_object->Host = "localhost";
	$mailer_object->Hostname=get_samee_const("MAILSERVER_HOST_NAME");
	$mailer_object->From = "$email_from";
	$mailer_object->CharSet = "UTF-8";
	if($priority>0){
	    $mailer_object->Priority = $priority;
	}	
	if($fromname==null){
		$fromname=substr($email_from,0,strpos($email_from,'@'));
	}
	$mailer_object->FromName = "$fromname";
	
		if(!is_array($email_to)){
			$email_to = explode(',',/*. (string) .*/ $email_to);		
		}
		$email_to=array_unique($email_to);
		if($local_testing==false){
				foreach($email_to as $key=>$value){
					$tm=trim($value);
					if($tm=='') continue;
					$to_ename=explode('@',$tm);
			 		$to_name=$to_ename[0];	
					$mailer_object->AddAddress("$tm",$to_name);
				}			
		}
		
		if($cc!=null and $fromname!='no-reply'){
				if(!is_array($cc)){	$cc=explode(',',$cc);}
				$cc=array_unique($cc);
				if($local_testing==false){	
					foreach($cc as $key4 => $value4){	 
						$em=trim((string)$value4);
						if($em=='') continue;
						$cc_ename=explode('@',$em);
				 		$cc_name=$cc_ename[0];
						$mailer_object->AddCC("$em","$cc_name");
					}
				}	
		}else if ($cc!=null){
			$bcc=$cc;
			if(!is_array($bcc)){$bcc=explode(',',$bcc); }
				$bcc=array_unique($bcc);
			if($local_testing==false){	
				foreach($bcc as $key3 => $value3){	 
						$em=trim((string)$value3);
						if($em=='') continue;
						$bcc_ename=explode('@',$em);
				 		$bcc_name=$bcc_ename[0];
						$mailer_object->AddBCC("$em","$bcc_name");
				}
			}			
		}
		$reply_to_a=array();
		if(!is_array($reply_to) and $reply_to!='' and $reply_to!=null){
			$reply_to_a=explode(',',$reply_to);
		}else if($reply_to=='' or $reply_to==null){
			$reply_to_a=array($email_from);
		}else{
			$reply_to_a=$reply_to;
		}		 	
		$reply_to=array_unique($reply_to_a);
		if($local_testing==false){	
			foreach($reply_to as $key2 => $value2 ){
				$rp=trim((string)$value2);
				if($rp=='') continue;
				$reply_ename=explode('@',$rp);
				$reply_name=$reply_ename[0];	
				$mailer_object->AddReplyTo($rp,"$reply_name");
			}
		}
		
	if($local_testing==true){	    
		$mailer_object->Host = "10.0.0.10";
		$to_testing_ids=explode(',',get_samee_const('MAILSERVER_LOCAL_TESTING'));
		for($i=0;$i<count($to_testing_ids);$i++){
			$mailer_object->AddAddress($to_testing_ids[$i]);
		}			
		if($attach_Embedded_file_str==null){
			$msg.=" \nFrom Developer / Testing  System";
		}
	}
	$mailer_object->Subject = stripslashes($subject);
	$mailer_object->IsHTML($content_type);
	$attach_Embedded_file_json='';
	$attach_Embedded_file = /*. (string[int][string]) .*/array();
	if($attach_Embedded_file_str!=null){
		$attach_Embedded_file_json=$attach_Embedded_file_str;
		$attach_Embedded_file=json_decode($attach_Embedded_file_str,true);
	}else{
		$attach_Embedded_file= null;
	}
	$str = ' <div style=margin-top:20px> " This e-mail transmission, including any attachments, is intended only for the named recipient(s) and may contain information that is privileged, confidential and/or exempt from disclosure under applicable law. If you have received this transmission in error, or are not the named recipient(s), please return the e-mail and permanently delete this transmission, including any attachments. Our company accepts no liability for the content of this email, or for the consequences of any actions taken on the basis of the information provided, unless that information is confirmed in writing and duly signed by the authorized person(s). If you are not the intended recipient you are notified that disclosing, copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited. Any views or opinions presented in this email are solely those of the author and do not necessarily represent those of the company " </div> ';
	$disclaimer = wordwrap($str,50);
	if($attach_Embedded_file==null){
	    $msg .= $disclaimer;
		$mailer_object->Body = $msg;
	}else if(isset($attach_Embedded_file[0]['html']) and $attach_Embedded_file[0]['html']!=''){
		/* if call from sales_server/license_module ... i need this setup --Start */
		/* if call from sales_server/license_module ... i need this setup --END */		

		$fname=$base_dir.$attach_Embedded_file[0]['html'];
		if (file_exists($fname)){
			$contents=file_get_contents($base_dir.$attach_Embedded_file[0]['html']);
		}else{
			//TODO: Need to handle this type of error .... in the UI
			$stack_out = getStackTraceString();
			error_log("File: $fname not exists. ". $stack_out);
			$contents='';
		}

		if(count($attach_Embedded_file)>0){
			for($i=0;$i<count($attach_Embedded_file); $i++){
				if(isset($attach_Embedded_file[$i]['inline'])){
					$contents=preg_replace('/'.$attach_Embedded_file[$i]['file_name'].'/i','cid:'.$attach_Embedded_file[$i]['inline'] ,$contents);					
				}
			}
		}
		if($msg!=''){
			$data_t=json_decode($msg);
			if($data_t!=null){
				foreach($data_t as $key1 =>$value1){
					$contents=preg_replace('/{{'.(string)$key1.'}}/i',$value1,$contents);
				}
			}
		}
		$contents .= $disclaimer;
		$mailer_object->Body = $contents;
	}
	//$mailer_object->WordWrap = 100;
	if($at_file!=''){
		if(is_array($at_file)){
			for($i=0;$i<count($at_file);$i++){
		   		$mailer_object->AddAttachment($at_file[$i]);
			}
		}else {
			$mailer_object->AddAttachment($at_file);
		}
	}
	
	if($attach_Embedded_file!=null){
		if(is_array($attach_Embedded_file)){
			for($i=0;$i<count($attach_Embedded_file);$i++){
				if(isset($attach_Embedded_file[$i]['inline'])){
					$mailer_object->AddEmbeddedImage($base_dir."".$attach_Embedded_file[$i]['file'],$attach_Embedded_file[$i]['inline'],$attach_Embedded_file[$i]['file_name'],"base64",$attach_Embedded_file[$i]['type']);
				}
			}
		}
	}

	$returnval=false;
	$mail_error_msg = "";
	if(!$mailer_object->Send()){
		$mail_error_msg = $mailer_object->ErrorInfo;
		if($is_status_needed){
			show_my_alert_msg('Message not sent . Mailer Error:'.$mailer_object->ErrorInfo);	
		}
		$returnval=false;
	}else{
		if($is_status_needed){
			show_my_alert_msg('Mail has been sent');
		}
		$returnval=true;
	}
	$reply_to_str='';
	if(is_array($reply_to)){
		$reply_to_str=implode(',',$reply_to);
	}
	if(!$crontab_call){
		$outgoing_mail_id=mail_send_util($email_from,$email_to,$subject,$msg,$returnval,$category,$cc,$at_file,$reply_to_str,
		$content_type,$mail_template_id,$mail_compile_id,$attach_Embedded_file_json,null,null,$mail_error_msg);
		//return $outgoing_mail_id;
	}
	return $returnval;
}//END of function      


/**
 * @param string $email_from
 * @param string[] $email_to
 * @param string $subject
 * @param string $msg
 * @param string $mst
 * @param int $category
 * @param string $cc
 * @param string $attachment
 * @param string $reply_to
 * @param boolean $content_type
 * @param string $mail_template_id
 * @param string $mail_compile_id
 * @param string $attach_Embedded_file
 * @param string $from_name
 * @param string $event_id
 * @param string $error_msg
 *
 * @return int
 */
function mail_send_util($email_from,$email_to,$subject,$msg,$mst,$category,$cc='',$attachment='',$reply_to=null,
$content_type=false,$mail_template_id=null,$mail_compile_id=null,$attach_Embedded_file=null,$from_name=null,$event_id=null,$error_msg=null){
	global $conn;
	$attachment_str='';
	if(is_array($reply_to)){
		$reply_to_str=implode(',',/*. (string[int]) .*/ $reply_to);
	}else{
		$reply_to_str=$reply_to;
	}
	$msg=mysqli_real_escape_string_wrapper($msg);
	$subject=mysqli_real_escape_string_wrapper($subject);
	//$category=mysqli_real_escape_string_wrapper($category);
	if(is_array($attachment)){
		for($at=0;$at<count($attachment);$at++){
			if($at>0){ $attachment_str.=",";}	
			if(substr_count($attachment[$at],'../')==2){
				$attachment_str.=(string)str_replace('../../','../',$attachment[$at]);
			}else{
			$attachment_str.=$attachment[$at];
			}
		}
	}
	if(is_array($cc)){
		$cc=implode(',',/*. (string[int]) .*/ $cc);
	}
	$time=date('Y-m-d H:i:s');
	if($content_type==true){ $content_type='Y';}
	else $content_type='N';
	$embed_attachment='';
	if($attach_Embedded_file!=null and $attach_Embedded_file!=''){
		if(is_array($attach_Embedded_file)){
			$embed_attachment=json_encode($attach_Embedded_file);
		}
		else{
			$embed_attachment=$attach_Embedded_file;
		}
		//$embed_attachment=mysqli_real_escape_string_wrapper($embed_attachment)
	}
	if(is_array($email_to)) $email_to=implode(',',$email_to);
	$query_ins="insert into gft_outgoing_emails  (id, send_time, email_id,attachment, category," .
				"message, cc_to,email_from,FROM_NAME,subject,mail_sent_status,reply_to,content_html," .
				"mail_template_id,mail_compile_id,mail_emb_attachment,GOM_EVENT_ID,mailer_error_msg) " .
				"values('','$time','$email_to','$attachment_str','$category','$msg','$cc'," .
				"'$email_from','$from_name','$subject','$mst','$reply_to_str','$content_type','$mail_template_id'," .
				"'$mail_compile_id','$embed_attachment','$event_id','$error_msg')";
  	$result=execute_my_query($query_ins,'',true,false,5);
  	if($result){
  		$mail_id=mysqli_insert_id_wrapper();
  		return $mail_id;
  	}else {
  		return -1;
  	}	
}

/**
 * @param string $subject
 * @param string $msg
 * @param int $mail_category
 * @param string $cc
 * @param int $type
 *
 * @return void
 */
function mail_error_alert($subject,$msg,$mail_category=7,$cc=null,$type=0){
	$email_from=get_samee_const('NO-REPLY_MAIL_ID');
	if($type==1){
		$email_to=get_samee_const('PROCESSING_DELAY_MAIL_TO');
	}else {
		$email_to=get_samee_const('MAIL_ERROR_ALERT_TO');
	}
	$requestarray="";
	if(isset($_REQUEST)){
		$requestarray=(isset($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:'');
	}
	$msg='<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">' .
		'<html><head><meta content="text/html;charset=UTF-8" http-equiv="Content-Type"></head><body> '.
		$msg.
    	(isset($_SERVER['HTTP_REFERER'])?"<br> Refferer: ".$_SERVER['HTTP_REFERER']:'').
		"<br>Request from: " . $_SERVER['PHP_SELF'] ."<br>".$requestarray."<br>". get_user_info().
		"</body></html>";
	$rs=send_mail_function($email_from,$email_to,$subject,$msg,null,$cc,$mail_category,true);
}

/**
 * @param int $category
 * @param string $email_from
 * @param string $email_to
 * @param string $subject
 * @param string $msg
 * @param string $attachment_file_tosend
 * @param string $cc
 * @param boolean $content_type
 * @param string $reply_to
 * @param string $from_page
 * @param boolean $user_info_needed
 * @param string $reply_to_incoming_mailid
 * @param string $fromname
 * @param string $mail_template_id
 * @param string $mail_compile_id
 * 
 * @return void
 */
function send_mail_from_sam($category,$email_from,$email_to,$subject,$msg,$attachment_file_tosend=null,
	$cc=null,$content_type=false,$reply_to=null,$from_page=null,$user_info_needed=true,$reply_to_incoming_mailid=null,
	$fromname=null,$mail_template_id=null,$mail_compile_id=null){
	if($user_info_needed==true and $content_type==true){	
		$msg.="<br>".get_user_info($content_type);
	    $msg="<pre>".$msg."</pre><br>";
	    
	    $content_type=true;
	}else if($user_info_needed==true) {
		$msg.="\n".get_user_info($content_type).'\n';
	}
	if(isset($_REQUEST['ggmap']) && $user_info_needed==true){
	    	$msg.="Google Map:".(string)$_REQUEST['ggmap'];
	}
    $time_now=date('Y-m-d H:i:s');
	$rs=send_mail_function($email_from,$email_to,$subject,$msg,$attachment_file_tosend,$cc,$category,$content_type,
	$reply_to,$is_status_needed=false,$crontab_call=false,$fromname,$mail_template_id,$mail_compile_id);
}//end of function 

/**
 * @param string $GOD_ORDER_NO
 *
 * @return void
 */
function send_data_migration_mail($GOD_ORDER_NO){
	$email_from=get_samee_const('MAIL_ERROR_ALERT_FROM');
	$content="Migration Order Details ";
	$query="SELECT GOD_EMP_ID,GLH_LEAD_CODE, GLH_CUST_NAME, GLH_CUST_STREETADDR1, GLH_CUST_STREETADDR2, GLH_AREA_NAME, GLH_CUST_CITY, GLH_CUST_STATECODE " .
			" FROM gft_lead_hdr, gft_order_hdr WHERE GLH_LEAD_CODE =god_lead_code and god_order_no='$GOD_ORDER_NO' ";
	$result=execute_my_query($query);
	if($query_data=mysqli_fetch_array($result)){		
		$content.="<br>Customer Id :".$query_data['GLH_LEAD_CODE'].
		"<br>Customer Name : <b>".$query_data['GLH_CUST_NAME']."</b><br>";
		/*"<br>Address :<br>".$query_data['GLH_CUST_STREETADDR1'].
		"<br>".$query_data['GLH_CUST_STREETADDR2'].
		"<br>".$query_data['GLH_AREA_NAME'].
		"<br>".$query_data['GLH_CUST_CITY'].
		"<br>".$query_data['GLH_CUST_STATECODE']."<BR>";*/
		$emp_dtl=get_emp_master($query_data['GOD_EMP_ID']);
	}
	$content.="<br>Migration Order Details : <br>"; 
	$query="SELECT GPM_PRODUCT_NAME, GPM_SKEW_DESC,GOP_SELL_AMT FROM gft_product_master p,gft_product_family_master pf, gft_order_product_dtl " .
			" WHERE p.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and pf.GPM_PRODUCT_CODE=p.GPM_PRODUCT_CODE " .
			" AND GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW " .
			" AND gop_order_no='$GOD_ORDER_NO' and gop_product_skew like '%0D'";
	$result=execute_my_query($query);
	$i=0;
	while($data=mysqli_fetch_array($result)){
		$content.= (++$i).".".$data['GPM_PRODUCT_NAME'].$data['GPM_SKEW_DESC']. "<br>Sell Amount For Migration :".$data['GOP_SELL_AMT']."<br>";
	}
	$rs=send_mail_function($email_from,get_samee_const("DATAMIGRATION"),'Data Migration Order',$content,null,$emp_dtl[0][4],'Data Migration Order',true);
}

/**
 * @return void
 */
function email_id_not_available(){
	echo "<table cellpadding=\"0\" cellspacing=\"2\" width=\"100%\" border=\"0\" class=\"FormBorder1\"><tbody>";
	echo<<<END
		<tr height="20" ><td colspan="11" align ="center" class="head_black_10"> Email Id Not Available for this customer</tr>
END;
	echo "</table>";
}
/**
 * @param string $from_dt
 * @param string $to_dt
 * @param string $msender_email
 * @param string $mreceiver_email
 * @param string $mail_status
 * @param string $mcategory_code
 * @param string $custCode
 *
 * @return void
 */
function show_mail_sent($from_dt,$to_dt,$msender_email,$mreceiver_email,$mail_status,$mcategory_code,$custCode=''){
	global $me,$sortbycol,$sorttype;
	print_dtable_header("Mail Sent ");
	$condition_qry	=	"";
	if($custCode!=''){
		$cust_dtl=customerContactDetail($custCode);
		$cust_all_emails	=	$cust_dtl['EMAIL'];
		$cust_mails	=	explode(',', $cust_all_emails);
		if(count($cust_mails)==0){
			email_id_not_available();
			return;
		}else if(count($cust_mails)==1){
			if($cust_mails[0]==''){
				email_id_not_available();
				return;
			}	
			$condition_qry="email_id like '$cust_mails[0]%' ";
		}else{
			if($cust_mails[0]==''){
				email_id_not_available();
				return;
			}
			for($in=0;$in<count($cust_mails);$in++){
				if($cust_mails[$in]!=''){
					if($condition_qry==''){ $condition_qry=" email_id like '$cust_mails[$in]%' "; }else{ $condition_qry .=" or email_id like '$cust_mails[$in]%' "; }
				}	
			}
		}
		$customer_credate_arr	=	mysqli_fetch_array(execute_my_query("SELECT GLH_DATE FROM gft_lead_hdr WHERE GLH_LEAD_CODE=$custCode"));
		$query_from_dt=db_date_format($from_dt);
		if($from_dt=='01-11-2004'){
			if($customer_credate_arr['GLH_DATE']!=''){
				$from_dt=date('d-m-Y',strtotime($customer_credate_arr['GLH_DATE']));
			}
		}
	}
	$query_e1="select id, send_time, email_id, attachment,ifnull(gmc_name,category) category," .
			" cc_to, reply_to, " .
			" subject, mail_sent_status, email_from, resent_time, reply_to_mail_id " .
			" from gft_outgoing_emails " .
			" left join gft_mail_category_master msm on (category=gmc_id) where 1 ";
	$query_from_dt=db_date_format($from_dt);
	$query_to_dt=db_date_format($to_dt);
	$temp	=	false;
	if($query_from_dt==''){
		$temp	=	true;
	}else if($query_from_dt!='' and $query_to_dt==''){
		$now_dt_com	=	strtotime(date('Y-m-d'));
		$from_dt1_com=	strtotime($query_from_dt);
		$expired_dt = strtotime("+30 day", $from_dt1_com);
		if($expired_dt<$now_dt_com){
			$temp	=	true;
		}
	}else if($query_from_dt!='' and $query_to_dt!=''){
		$now_dt_com	=	strtotime($query_to_dt);
		$from_dt1_com=	strtotime($query_from_dt);
		$expired_dt = strtotime("+30 day", $from_dt1_com);
		if($expired_dt<$now_dt_com){
			$temp	=	true;
		}
	}
	if($temp){
		echo ("<br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b style='color:red; font-size:12px;'>Info:</b>
			Not supported due to performance issue. So please use date filter range between 30 days. Contact sam-team@ if required ");
		//require_once(__DIR__ ."/footer.php");
		return;
	}
	if($query_from_dt!=''){ $query_e1.="  and send_time>='$query_from_dt 00:00:00' "; }
	if($query_to_dt!=''){ $query_e1.="  and send_time<='$query_to_dt 23:59:59' "; }
	if($mail_status!='-1'){ $query_e1.=" and mail_sent_status='$mail_status' ";}
	if($mcategory_code!=''){ $query_e1.=" and gmc_id='$mcategory_code' "; }
	if($custCode!=''){
		$query_e1.=" and ($condition_qry)";
	}else if($mreceiver_email!=''){ $query_e1.=" and email_id like '$mreceiver_email%' "; }
	if($msender_email!=''){ $query_e1.=" and email_from like '$msender_email%' "; }
	if($sortbycol==''){
		$query_e1.= " ORDER BY  send_time desc ";
		$sortbycol = "send_time";
		$sorttype = '2';
	}else{
		$query_e1.= " ORDER BY $sortbycol ".($sorttype=='2'?"DESC ":" ");
	}
	$result_e1=execute_my_query($query_e1);
	$r_query=$query_e1;
	$count_num_rows=mysqli_num_rows($result_e1);
	$myarr=array("S.No","Date & Time","From E-mail Id","To Email Ids","To CC Email Ids","Subject",
			"Attachment File","Category","Status","Reply To","Reply Of Mail");
	$mysort=array("","send_time", "email_from","email_id","cc_to","subject","attachment",
			"category","mail_sent_status","reply_to","reply_to_mail_id");

	$nav_struct=get_dtable_navigation_struct($count_num_rows);
	print_dtable_navigation($count_num_rows,$nav_struct, $me,"export_all_report.php",$r_query,
	$myarr,$sp=null,$htmltype=1,$show_nav=true,$post_array=null,$heading2=null,
	$sms_category=null,$email_category=null,$to_whom=null,$take_sort_array_for_export=$mysort);
	echo "<table cellpadding=\"0\" cellspacing=\"2\" width=\"100%\" border=\"0\" class=\"FormBorder1\"><tbody>";
	sortheaders($myarr,$mysort,$nav_struct,$sortbycol,$sorttype);
	$s=0;

	if($count_num_rows==0){
		echo<<<END
		<tr height="20" ><td colspan="11" align ="center" class="head_black_10"> No Mails </tr>
END;
	}else if($count_num_rows>0){

		$start_of_row=(int)$nav_struct['start_of_row'];
		$MAX_NO_OF_ROWS=(int)$nav_struct['records_per_page'];
		mysqli_data_seek($result_e1,$start_of_row);
		$i=0;$sl=0;
		while(($query_data=mysqli_fetch_array($result_e1)) and $MAX_NO_OF_ROWS > $sl){
			$sl++;
			$id=$query_data['id'];
			$time="<a href=\"javascript:call_popup('mail_sent_report.php?mail_id=$id',8);\" title=\"Detail Mail Content\">".$query_data['send_time']."<a>";
			$to_id=str_replace(',',', ',$query_data['email_id']);
			$attach=$query_data['attachment'];
			$category=$query_data['category'];
			$subject=$query_data['subject'];

			$t_int_mail_sent_status=(int)$query_data['mail_sent_status'];
			switch($t_int_mail_sent_status){
				case 0:
					$status="Pending";
					break;
				case 1:
					$status="sent";
					break;
				case 2:
					$status="Error in Mail";
					break;
				case 3:
					$status="Failed";
					break;
				default:
					$status="";
					break;
			}
			$email_from=$query_data['email_from'];
			$reply_to_mail_id=($query_data['reply_to_mail_id']!=''?"<a href=\"\">":"");
			$cc_to=str_replace(',',', ',$query_data['cc_to']);
			$reply_to=str_replace(',',', ',$query_data['reply_to']);
			if($category=="Collection Report"){
				$value1="file_download.php?file_type=mail_sent&filename=Collection/";
				$folder="Collection";
				$attach1=explode(',',$attach);
				$fl=/*. (string[int]) .*/ array();
				foreach ( $attach1 as $key => $value ) {
					$fl[]="<a href=\"$value1".basename($value)."\" target=_blank>".basename($value)."</a>";
				}
			}else{
				$attach1=explode(',',$attach);
				$fl=/*. (string[int]) .*/ array();
				foreach ( $attach1 as $key => $value ) {
					$fl[]="<a href=\"$value\" target=_blank>".basename($value)."</a>";
				}
			}
			$attach_link=implode(', ',$fl);
			$value_arr[0]=array($sl,$time,$email_from,$to_id,$cc_to,$subject,$attach_link,$category,$status,$reply_to,$reply_to_mail_id);
			$value_arr_align=array("left","left","left","left","left","left","left","left","left","left","left");
			print_resultset($value_arr,$value_arr_width=null,$value_arr_align);
		}
	}
	echo "</table>";
	print_dtable_navigation($count_num_rows,$nav_struct,$me,"export_all_report.php",$r_query,$heading=null);
}

/**
 * @param int $GCD_COMPLAINT_ID
 * 
 * @return void
 */
function send_mail_sms_for_complaint_type($GCD_COMPLAINT_ID){
	$query_sel = " select GFT_MAIL_ID, GFT_SMS_ID, GFT_COMPLAINT_DESC, GLH_LEAD_CODE, GLH_CUST_NAME, GEM_EMP_NAME,gpg_support_mail_id, ".
				 " GPG_YEAR_END_URL from gft_customer_support_hdr ".
				 " join gft_customer_support_dtl on (gcd_activity_id=gch_last_activity_id) ".
				 " join gft_lead_hdr on (GLH_LEAD_CODE=GCH_LEAD_CODE) ".
				 " join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE) ".
				 " join gft_emp_master on (GCD_EMPLOYEE_ID=GEM_EMP_ID) ".
				 " join gft_product_group_master on (gpg_product_family_code=GCH_PRODUCT_CODE and gpg_skew=GCH_PRODUCT_SKEW) ".
				 " where GCH_COMPLAINT_ID='$GCD_COMPLAINT_ID' ";	
	$que_res = execute_my_query($query_sel);
	if($row1 = mysqli_fetch_array($que_res)){
		$mail_template_id = (int)$row1['GFT_MAIL_ID'];
		$sms_category_id = (int)$row1['GFT_SMS_ID'];
		$cust_id = (int)$row1['GLH_LEAD_CODE'];
		$reply_to = $from_mail_id = $row1['gpg_support_mail_id'];
		$content_arr = array(
			"comp_id"=>array($GCD_COMPLAINT_ID),
			"Customer_Name"=>array($row1['GLH_CUST_NAME']),
			"message"=>array($row1['GFT_COMPLAINT_DESC']),
			"Employee_Name"=>array($row1['GEM_EMP_NAME']),
			"Year_End_Video_Link"=>array($row1['GPG_YEAR_END_URL'])
		);
		if($mail_template_id!=0){
			send_formatted_mail_content($content_arr, 6, $mail_template_id,null, array($cust_id),null,null,null, $reply_to, '',$from_mail_id);
		}
		if($sms_category_id!=0){
			$sms_content = get_formatted_content($content_arr, $sms_category_id);
			entry_sending_sms_to_customer('', $sms_content, $sms_category_id, $cust_id);
		}
	}
}

?>
