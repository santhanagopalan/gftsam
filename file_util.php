<?php
/*. require_module 'fileinfo'; .*/

/*. forward string[int] function upload_files_to(string $uploadDir,string $param_name=); .*/

require_once(__DIR__ ."/function.send_sms.php");
require_once(__DIR__ ."/function.insert_stmt_for_activity.php");
require_once(__DIR__ ."/function.insert_stmt.php");
require_once(__DIR__ ."/function.update_in_tables.php");
require_once(__DIR__ ."/mis_detail_update_util.php");
require_once(__DIR__ ."/tabular_form_new_lead_entry.php");
require_once(__DIR__ . "/lib_ods/smart_resize_image.function.php");

/**
 * @param string $lead_code
 * @param string $remarks
 *
 * @return void
 */
function update_address_invalid($lead_code,$remarks){
	global $uid;
	if($remarks=='') { $remarks=='Letter Returned';}
	$update_column=/*. (mixed[string]) .*/ array();
	$table_key_arr=/*. (string[string]) .*/ array();
	$table_name='gft_lead_hdr';
	$update_column['GLH_ADDRESS_VERIFIED']='I';
	$table_key_arr['GLH_LEAD_CODE']=$lead_code;
	$table_column_iff_update['GLH_ADDRESS_VERIFIED_DATE']=date('Y-m-d');
	$table_column_iff_update['GLH_ADDRESS_VERIFIED_BY']=$uid;
	array_update_tables_common($update_column,$table_name,$table_key_arr,null,$uid,$remarks,$table_column_iff_update,$insert_new_row=null);
}

/** 
 * @param int $re
 * 
 * @return string
 */
function memmorystatus($re){
	$alert=0;
	$df = disk_total_space("/");
	$fs = disk_free_space("/");
	$report= "<table  align= center border=1><tr><td>Total Memory </td><td align=right> ".round($df/1024/1024)." MB.</td></tr>";
	$report.="<tr><td>Free Space </td><td align=right> ".round($fs/1024/1024)." MB.</td></tr>" ; 
	$report.="<tr><td>Free Space % </td><td align=right> ".round($fs/$df * 100,2)."%.</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
	$alert += (round($fs/$df * 100,2)< 15 ? 1:0);
	$df = disk_total_space("/home/");
	$fs = disk_free_space("/home/");
	$report.= "<tr><td>Total Memory - home</td><td align=right> ".round($df/1024/1024)." MB.</td></tr>";
	$report.="<tr><td>Free Space -home</td><td align=right> ".round($fs/1024/1024)." MB.</td></tr>" ; 
	$report.= "<tr><td>Free Space - home % </td><td align=right> ".round($fs/$df * 100,2)."%.</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
	$alert += (round($fs/$df * 100,2)< 15 ? 1:0);
	$df = disk_total_space("/var/");
	$fs = disk_free_space("/var/");
	$report.= "<tr><td>Total Memory -var </td><td align=right>".round($df/1024/1024)." MB.</td></tr>";
	$report.= "<tr><td>Free Space - var </td><td align=right>".round($fs/1024/1024)." MB.</td></tr>" ; 
	$report.= "<tr><td>Free Space % - var</td><td align=right> ".round($fs/$df * 100,2)."%.</td></tr></table></html>";
	$alert += (round($fs/$df * 100,2)< 15 ? 1:0);
	if($alert!=0){
		$report = "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"><html><center><h1>Memory Use - Danger Position</h1></center><br>".$report;
		return $report;
	}else
	$report ="<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"><html><center class=\"formHeader\"><u><h1>Memory Use - Monthly Report</h1></u></center><br>".$report;
	if($re==1)
		return $report;
	else
		return "";
}
 
/**
 * @param int $filesizeInt
 *
 * @return string[string]
 */
function file_size_info($filesizeInt) { 

	$filesize=(float)$filesizeInt;

	$bytes = array('KB', 'KB', 'MB', 'GB', 'TB'); # values are always displayed  
	if($filesize < (float)1024) $filesize = 1; # in at least kilobytes. 
	for ($i = 0; $filesize > (float)1024; $i++){
		$filesize /= 1024; 
	}

	$file_size_info=/*. (string[string]) .*/ array();

	$file_size_info['size'] = round($filesize,2); 
	$file_size_info['type'] = $bytes[$i]; 
	return $file_size_info; 
}

/**
 * @return string[string]
 */
function calculate_db_size(){
	$sql = "SHOW TABLE STATUS";$total=0;
	$result = execute_my_query($sql); // This is the result of executing the query
	$max_length=0;
	$max_length_table="";	
	$max_length_table_row=0;
	while($row = mysqli_fetch_array($result)){// Here we are to add the columns 'Index_length' and 'Data_length' of each row
		$dsize=(int)$row['Data_length']+(int)$row['Index_length'];
		if($max_length<$dsize){
			$max_length=$dsize;
			$max_length_table=$row['Name'];	
			$max_length_table_row=$row['Rows'];
		}
		$total+= $dsize;
	}

	$dbsize= /*. (string[string]) .*/ array();
    $dbsize['max_length']="".$max_length;
	$dbsize['max_length_table']=$max_length_table;
	$dbsize['max_length_table_row']=$max_length_table_row;
	$dbsize['total']="".$total;
	return $dbsize;
} 

/**
 * @param string $f_location
 * @param string $filename
 *
 * @return void
 */
function _Download_new($f_location,$filename){
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Length: ' . filesize($f_location));
	header('Content-Disposition: attachment; filename='.$filename);
	readfile($f_location);
}

/**
 * @return string
 */
function upload_template(){
$string_upload_temp=<<<END
<table cellpadding="0" cellspacing="0" border="0" id="table_upload" width="100%">
<tbody><tr><td><input class="upfile_file" type="file" name="upfile1[0]" id="upfile10" value="" size="35"></td></tr></tbody>
<tfoot><tr><td><input type="button" name="addUpload" id="addUpload" class="button" value="ADD" onClick="javascript:addUploads();">
<span id="remove_upload_btn" style="Display:none"><input type="button" name="remUpload" id="remUpload" class="button" value="REMOVE" onClick="javascript:removeUploads();"></span>
</td></tr></tfoot></table>
END;
 	return $string_upload_temp;			
}

/**
 * @param string $uploadDir
 * @param string $param_name
 * 
 * @return string[int]
 */
function  upload_files_to($uploadDir,$param_name=''){
	ini_set('upload_max_filesize', '20M');  
	ini_set('post_max_size', '20M');  
	ini_set('max_input_time', 300);  
	ini_set('max_execution_time', 300);

    	$attachment_file_tosend=/*. (string[int]) .*/ array();
    	$upload_req_param = ($param_name!='')?$param_name:'upfile1';
	if( isset($_FILES["$upload_req_param"]['name']) && count($_FILES["$upload_req_param"]['name'])>0 ){
  		if(!file_exists($uploadDir)){			
	    	mkdir("$uploadDir", 0777,true);
		}
		$file_name=  /*. (string[int]) .*/ $_FILES["$upload_req_param"]['name'];
		$f_temp_name=/*. (string[int]) .*/ $_FILES["$upload_req_param"]['tmp_name'];
		$size=       /*. (int[int])    .*/ $_FILES["$upload_req_param"]['size'];
       	$count_files=count($_FILES["$upload_req_param"]['name']);
    	$userfile=array();
    	$f_name=/*. (string[int]) .*/ array();
		for($k=0;$k<$count_files;$k++){
			if($file_name[$k]!='' && $size[$k]!=0){
				$f_name[$k]=str_replace(" ","_",basename($file_name[$k]));
				if(move_uploaded_file($f_temp_name[$k],"$uploadDir/{$f_name[$k]}")) {
			   		$attachment_file_tosend[$k]="$uploadDir/{$f_name[$k]}";
			   		chmod("$uploadDir/{$f_name[$k]}",0777);
			 	}else {
			 		echo "Not uploaded $uploadDir/{$f_name[$k]}";
			 		mail_error_alert($subject='File Not Uploaded',$msg='Not uploaded');	
			 	}
			}
		}
	}	
	return $attachment_file_tosend;
}

/**
 * @return string
 */
function upload_Embedded_template(){
$string_upload_temp=<<<END
<table cellpadding="0" cellspacing="0" border="0" id="table_Embedded_upload" width="100%">
<tbody><tr><td><input type="file" name="upembfile1[0]" id="upembfile10" value="" size="35"></td>
<!-- <td><input type="text" name="upembfileid1[0]" id="upembfileid10" value="" size="35"></td> -->
</tr></tbody>
<tfoot>
<!--
<tr><td><input type="button" name="addUpload" id="addUpload" class="button" value="ADD" onClick="javascript:add_Embedded_Uploads();">
<span id="remove_Embedded_upload_btn" style="Display:none"><input type="button" name="remUpload" id="remUpload" class="button" value="REMOVE" onClick="javascript:remove_Embedded_Uploads();"></span>
</td></tr>
--></tfoot></table>
END;
 	return $string_upload_temp;			
}

/**
 * @param string $zipfile
 * @param string $hedef
 *
 * @return void
 */
function ezip($zipfile, $hedef = ''){
	global $uploadDir;
	try{
		chmod($uploadDir,0777);
	}catch(Exception $e){
		error_log("WARNING: uploadDir change permission");
	}
	$zip = new ZipArchive;
	if ($zip->open($zipfile) === TRUE) {
		$fname_with_ext=basename($zipfile);
		$fname_without_ext=substr($fname_with_ext,0,-4);
		if ($zip->locateName($fname_without_ext."/") === FALSE){
			show_my_alert_msg("The zip file $fname_without_ext does not contain the directory with name : $fname_without_ext");
			die();
		}
		$zip->extractTo($hedef);
		$zip->close();
	} else {
		show_my_alert_msg("Not able to unzip");
	}
	//exec("unzip $zipfile -d $hedef");
}

/**
 * @param string $dirpath
 *
 * @return boolean
 */
function unlink_files($dirpath){
	if(file_exists($dirpath)){
		if(is_dir($dirpath)){
			$dir_handle  = opendir($dirpath);
			while (($file = readdir($dir_handle)) !== false) {

				$child_file=$dirpath."/".$file;

				if($file!="." and $file!=".." and !is_dir($child_file)){
					if (file_exists($child_file)){
						try{
							chmod($child_file,0777);
						}catch(Exception $e){
							error_log("WARNING: in unlink_files  ".$child_file . "  Exception: " . $e->__toString());
						}

						unlink($child_file);
					}
				}else if($file!="." and $file!=".." and $file!='' and is_dir($child_file)){
					unlink_files($child_file);
				}
			}
			closedir($dir_handle);
			rmdir($dirpath);
		}else{
			unlink_files($dirpath);
		}
	}
	return true;
}

/**
 * @param string $uploadDir
 *
 * @return string[int][string]
 */
function upload_Embedded_files_to($uploadDir){
    	$attachment_file_tosend=/*. (string[int][string]) .*/ array();

	if(count($_FILES['upembfile1']['name'])>0){
  		if(!file_exists($uploadDir)){			
	    	mkdir("$uploadDir", 0777,true);
	    	chmod("$uploadDir",0777);
		}
	    $file_name=/*. (string[int]) .*/ $_FILES['upembfile1']['name'];
		$f_temp_name=/*. (string[int]) .*/ $_FILES['upembfile1']['tmp_name'];
		$size=/*. (int[int]) .*/ $_FILES['upembfile1']['size'];
       	$count_files=count($_FILES['upembfile1']['name']);
       	$type=/*. (string[int]) .*/ $_FILES['upembfile1']['type'];
    	$userfile=array();
    	$f_name=/*. (string[int]) .*/ array();
//echo " ... type 0 val=".$type[0];
//echo " <br>... file_name 0 val=". $file_name[0];
//echo " <br>... file_name att  val=". substr($file_name[0],-4);
//print_r($file_name);

    
    	if($count_files==1 and ($type[0]=='application/zip' or $type[0]=='application/x-zip' 
    		or ( $type[0]=='application/download' and substr($file_name[0],-4) == ".zip")  ) 
    		or ( $type[0]=='application/octet-stream' and substr($file_name[0],-4) == ".zip") ){
    		$f_name[0]=basename($file_name[0]);
    		if(file_exists("$uploadDir/{$f_name[0]}")){
    			unlink("$uploadDir/{$f_name[0]}");
    		}
    		unlink_files("$uploadDir/".substr($f_name[0],0,-4));/*folder */    		
			if(move_uploaded_file($f_temp_name[0],"$uploadDir/{$f_name[0]}")){
	    		if($file_name[0]!='' && $size[0]!=0){
					$f_name[0]=basename($file_name[0]);
					chmod("$uploadDir/".$f_name[0], 0777);
					$filename = str_replace(" ","\ ",$f_name[0]);
					if(file_exists("$uploadDir/".substr($f_name[0],0,-4))){
						/* remove the existing files some while zip is upload after removed some files it not removed */
						if($attachment_path=opendir("$uploadDir/".substr($f_name[0],0,-4))){
						
							while (($file = readdir($attachment_path)) !== false) {
								unlink("$uploadDir/".substr($f_name[0],0,-4)."/$file");
							}
						}
					}
					ezip("$uploadDir/$filename","$uploadDir/");
					$tempdir=$uploadDir."/".substr($f_name[0],0,-4)."/";
					chmod("$tempdir",0777);
					if($attachment_path=opendir("$uploadDir/".substr($f_name[0],0,-4))){
						$k=0;
					 	while (($file = readdir($attachment_path)) !== false) {
							$upload_file="$uploadDir/".substr($f_name[0],0,-4)."/$file";
							$upload_file=realpath($upload_file);
					 		if( mime_content_type($upload_file)=='text/plain' 
					 		 or mime_content_type($upload_file)=='text/html' or substr($file,-5)=='.html' 
					 		 or substr($file,-4)=='.htm' ){
					 			$attachment_file_tosend[0]['html']="$uploadDir/".substr($f_name[0],0,-4)."/$file";
					 		}else if($file!='.' and $file!='..' and $file!='Thumbs.db'){
						 		$attachment_file_tosend[$k]['file']="$uploadDir/".substr($f_name[0],0,-4)."/$file";
				   				$attachment_file_tosend[$k]['inline']="img".substr("000".$k,-3);
				   				$attachment_file_tosend[$k]['file_name']=$file;
				   				if($type[0]=='application/octet-stream'){
				   					 $file_type=substr($file,strrpos($file,'.')+1,strlen($file));
				   					 $attachment_file_tosend[$k]['type']="image/$file_type" ;
				   				}else{
									$afile="$uploadDir/".substr($f_name[0],0,-4)."/$file";
									$afile=realpath($afile);
				   					$attachment_file_tosend[$k]['type']= mime_content_type($afile);
				   				}
				   				$k++;
							}

							$tdir="$uploadDir/".substr($f_name[0],0,-4)."/$file";
							try{
								chmod($tdir,0777);
							}catch(Exception $e){
								error_log("WARNING: in upload_Embedded_files_to ".$tdir);
							}
						}
					}
				}
	    	}else{
				echo "Not uploaded $uploadDir/{$f_name[0]}";
			 	mail_error_alert($subject='File Not Uploaded',$msg='Not uploaded');	
			}
    	}else if(isset($_REQUEST['upembfileid1'])){
			$upembfileid1=/*. (string[int]) .*/ $_REQUEST['upembfileid1'];
			for($k=0;$k<$count_files;$k++){
				if($file_name[$k]!='' && $size[$k]!=0){
					$f_name[$k]=basename($file_name[$k]);
					if(file_exists("$uploadDir/{$f_name[$k]}")){
						 unlink("$uploadDir/{$f_name[$k]}");
					}
					$toUploadFile="$uploadDir/".$f_name[$k];
					if(move_uploaded_file($f_temp_name[$k],$toUploadFile)) {
				   		$attachment_file_tosend[$k]['file']=$toUploadFile;
				   		$attachment_file_tosend[$k]['inline']=$upembfile1[$k];
				   		$attachment_file_tosend[$k]['file_name']=$f_name[$k];
				   		$attachment_file_tosend[$k]['type']=$type[$k];
				 	}else {
				 		echo "Not uploaded $toUploadFile";
				 		mail_error_alert($subject='File Not Uploaded',$msg='Not uploaded');	
				 	}
				}
			}
    	}
	}
	return $attachment_file_tosend;
}

/**
 * @param string $dir
 * 
 * @return void 
 */
function rrmdir($dir) {
	if(is_dir($dir)){
		$objects = scandir($dir);
		foreach ($objects as $objectVar) {
			if ($objectVar != "." && $objectVar != "..") {
				if (filetype($dir."/".$objectVar) == "dir") rrmdir($dir."/".$objectVar); else unlink($dir."/".$objectVar);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}

/**
 *@param string $path
 *
 * @return boolean
 */
function chmode_files($path){
	if(file_exists($path)){
		if(!is_dir($path)){
			chmod($path,0777);
		}else{
			$dir_handle  = opendir($path);
			while (($file = readdir($dir_handle)) !== false) {
				$child_file=$path."/".$file;
				if($file!="." and $file!=".." and !is_dir($child_file)){
					chmod($child_file,0777);
				}else if($file!="." and $file!=".." and $file!='' and is_dir($child_file)){
					chmode_files($child_file);
				}
			}
			closedir($dir_handle);
		}
	}
	return true;
}

/**
 * @param string $attachment
 *
 * @return void
 */
function techsupport_preformanceupload($attachment){
	global $uid;
	$file_name=basename($attachment);
	$handle_out = fopen("$attachment","r");
	$i=0;
	$s=0;
	$num=0;$putcomma='';$count_nums_split=0;$contact_nos='';$num_rows_affected=0;
	$i=0;
	$date=substr($file_name,0,4).'-'.substr($file_name,4,2).'-'.substr($file_name,6,2);

	$format_array=/*. (string[int][int]) .*/ array(); //For the declaration
	$format_array=array(array('GSP_EMP_ID','Agent Name'),
							array('GSP_IC','IC'),
							array('GSP_IM','IM'),
							array('GSP_OC','OC'),
							array('GSP_OM','OM'),
							array('GSP_IM_REJECT','IM_Reject'),
							array('GSP_IM_IGNORE','IM_Ignore'),
							array('GSP_IC_TRANSFER','IC_Xfer'),
							array('GSP_IC_TRANSFER_RCVD','IC_XferRcvd')
							);
	$index_array=/*. (string[string]) .*/ array();
	
	while (($data = fgetcsv($handle_out, 1000, ",")) !== FALSE) {
		if($i==0){
			$data=array_trim($data);
			$index_array=update_column_heading($data,$format_array);
			if(isset($index_array['Agent Name']) and isset($index_array['IC']) and isset($index_array['IM']) and isset($index_array['OC']) and isset($index_array['OM']) and isset($index_array['IM_Reject'])  and isset($index_array['IM_Ignore']) 
			and isset($index_array['IC_XferRcvd']) and isset($index_array['IC_Xfer'])){ 
				$i++; continue;
			}else {echo 'Heading shows Different<br>prefered columns in the cvs file are <br><pre>Agent Name,IC,IM,OC,OM,IM_Ignore, IM_Reject,IC Transfer, IC Transfer Rcvd,IM_Customer </pre>'; 
				exit;
			}
		}
		$value_array=/*. (string[int][int]) .*/ array();
		for($j=0;$j<count($format_array);$j++){
			$value_array[$format_array[$j][0]]=$data[$index_array[$format_array[$j][1]]];
		}
		if(check_manditory_import($value_array,$format_array)){
			$value_array['GSP_EMP_ID']=get_user_id($value_array['GSP_EMP_ID']);
			$value_array['GSP_DATE']=$date;
			$key_value_array=array('GSP_EMP_ID'=>$value_array['GSP_EMP_ID'],'GSP_DATE'=>$value_array['GSP_DATE']);
			if($value_array['GSP_EMP_ID']!=0){
				$venter=array_update_tables_common($value_array,'gft_ts_performance',$key_value_array,null, $uid,$remarks=null,$table_column_iff_update=null,$insert_new_row=$value_array);
			}else{
				//print_r($data);
			}
		}
		$i++;
	}/* end of while */
	/*MAIL SEND*/
}//end of fn

/* NOT USED
 * @param string $attachment
 * @param string $reference_no
 * @param string $approved_date
 * 
 *  @return void
 *
 
function process_ndnc_approved_list($attachment,$reference_no,$approved_date){
	exit;
	global $conn;
	$query_exist_ref_no="select GNA_ID from  gft_ndnc_approval where GNA_REFERENCE_NO='$reference_no' ";
	$result_exist_ref_no=execute_my_query($query_exist_ref_no);
	$num_rows=mysqli_num_rows($result_exist_ref_no);
	if($num_rows==1){
		$qdata=mysqli_fetch_array($result_exist_ref_no);
		$ref_id=$qdata['GNA_ID'];
	}else{
		$insert_ref_no="insert into gft_ndnc_approval (GNA_ID, GNA_REFERENCE_NO, GNA_APPROVE_DATE) values" .
				"('','$reference_no','$approved_date') ";
		$insert_ref_result=	execute_my_query($insert_ref_no);
		$ref_id=mysqli_insert_id_wrapper();	
	}
	$handle_out = fopen("$attachment","r");  
	echo "<br>";
	$i=0;
	$s=0;
	$num=0;$putcomma='';$count_nums_split=0;$contact_nos='';$num_rows_affected=0;
	while (($data = fgetcsv($handle_out, 1000, ",")) !== FALSE) {
		if($i>0){
			$count_nums_split++;		
			$contact_nos.="{$putcomma}'".$data[0]."'";
			$putcomma=',';
			if($count_nums_split==50){
				$update_query="update gft_customer_contact_dtl set GCC_CAN_SEND='Y',GCC_NDNC_ID='$ref_id' " .
						" where  gcc_contact_no in ($contact_nos)";
				$rs=execute_my_query($update_query)	;
				$num_rows_affected+=mysqli_affected_rows_wrapper();	
				$putcomma='';$contact_nos='';
				$count_nums_split=0;
			}
		}
		$i++;
	}
	if($contact_nos!=''){
		$update_query="update gft_customer_contact_dtl set GCC_CAN_SEND='Y',GCC_NDNC_ID='$ref_id' " .
						" where  gcc_contact_no in ($contact_nos)";
		$rs=execute_my_query($update_query)	;	
		$num_rows_affected+=mysqli_affected_rows_wrapper();				
	}
	echo "<center><b>Num rows affected=".$num_rows_affected."</b></center>";
	send_sms_to_new_lead($GLH_LEAD_CODE=null,$NDNC_ID=$ref_id);
}//end of file
*/


/**
 * @param string $PCS_Activity
 * 
 * @return void 
 */
function mail_send_to_customer_and_pcs($PCS_Activity){
	$db_mail_content_config['PCS_Activity'][0]=(string)$PCS_Activity;
	$pcs_array = /*. (string[int]).*/ array();
	$pcs_array[0]="pcs-team@gofrugal.com";
	send_formatted_mail_content($db_mail_content_config, 1,184,null,null,$pcs_array);
	echo $PCS_Activity;
}

/**
 * @param string[string] $a
 * @param string[string] $b
 * 
 * @return int
 */
function sortByOrder($a, $b) {
	return $a['2'] - $b['2'];
}


/**
 * @param string $attachment
 * 
 * @return void
 */
function implementation_act_import($attachment){
	$handle_out = fopen("$attachment","r");  
	$i=$j=0;
	$s=0;
	$num=0;$putcomma='';$count_nums_split=0;$contact_nos='';$num_rows_affected=0;
	$PCS_Activity='';
	$filedata = /*. (string[int][int]) .*/array();
	$err_msg='';
	$date_order=0;
	$emp_id_order=0;
	$cust_id_order=0;
	$milestone_id_order=0;
	$activity_id_order=0;
	$hours_spend_order=0;
	$commercial_order=0;
	$procuct_order=0;
	$data = /*.(string[int]) .*/array();
	$milestone_arr = get_one_dimensional_array_from_single_table('GMM_NAME','gft_pcs_milestone_master');
	$commm_clas_arr = get_one_dimensional_array_from_single_table('GCCM_NAME','gft_commercial_classification_master');
	while(($data = fgetcsv($handle_out, 1000,",")) !== FALSE){
		if($j==0){ 
			$k=0;
			while($k<8){
				if(!isset($data[$k])){
					show_my_alert_msg("Name of the headers are not in specified format -".$k);
					exit();
				}
				$data[$k]=preg_replace('/\s+/', ' ', $data[$k]);
				switch(strtolower(trim($data[$k]))){
					case "date format yyyy-mm-dd":
						$date_order=$k;
						break;
					case "sam employee id":
						$emp_id_order=$k;
						break;
					case "customer sam id":
						$cust_id_order=$k;
						break;
					case "milestone":
						$milestone_id_order=$k;
						break;
					case "activity in details":
						$activity_id_order=$k;
						break;
					case "hours spent in man hours":
						$hours_spend_order=$k;
						break;
					case "commercial classification":
						$commercial_order=$k;
						break;
					case "products":
						$procuct_order=$k;
						break;
					default:
						show_my_alert_msg("Name of the headers are not in specified format at column - ".($k+1)) ;
						exit();
				}
				$k++;
				
			}
			$j++; continue;
		}
		$filedata[$i][0] = isset($data[$date_order])?trim($data[$date_order]):'';
		$filedata[$i][1] = isset($data[$emp_id_order])?trim($data[$emp_id_order]):'';
		$filedata[$i][2] = isset($data[$cust_id_order])?trim($data[$cust_id_order]):'';
		$filedata[$i][3] = isset($data[$activity_id_order])?trim($data[$activity_id_order]):'';
		$filedata[$i][4] = isset($data[$milestone_id_order])?trim($data[$milestone_id_order]):'';
		$filedata[$i][5] = isset($data[$hours_spend_order])?trim($data[$hours_spend_order]):'';
		$filedata[$i][6] = isset($data[$commercial_order])?trim($data[$commercial_order]):'';
		$filedata[$i][7] = isset($data[$procuct_order])?trim($data[$procuct_order]):'';
		
		if((get_single_value_from_single_table('GEM_EMP_NAME','gft_emp_master',' gem_status="A" and GEM_EMP_ID',$filedata[$i][1])=='')){
			$err_msg="SAM Employee ID -".$data[$emp_id_order]." at row ".($i+1);
			break;
		}
		$dat_con=(string)str_replace(',','',$filedata[$i][0]);
		$filedata[$i][0]=date('Y-m-d',strtotime($dat_con));
		if (($filedata[$i][0]=="1970-01-01")){
			$err_msg="Date Format ".$data[$date_order]." at row ".($i+1);
			break;
		}
		if((preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$filedata[$i][0]))==0){
			$err_msg="Date Format ".$data[$date_order]." at row ".($i+1);
			break;
		}
		if(datediff(date('y-m-d'),$filedata[$i][0])>0){
			$err_msg="Activity date is greater than current date at row ".($i+1);
			break;
		}
		if(datediff($filedata[$i][0],date('Y-m-d'))>get_samee_const('PCS_ACTIVITY_ALLOWED_DAYS')){
			$err_msg="Activity date less than ".get_samee_const('PCS_ACTIVITY_ALLOWED_DAYS')." days are not allowed at row ".($i+1);
			break;
		}
		if(get_single_value_from_single_table('GLH_CUST_NAME','gft_lead_hdr','GLH_LEAD_CODE',$filedata[$i][2])==''){
			$err_msg="Lead Code ".$filedata[$i][2]." at row ".($i+1);
			break;
		}
		if(!in_array(strtolower($filedata[$i][4]),array_map('strtolower',$milestone_arr))){
			$err_msg="Milestone ".$filedata[$i][4]." at row ".($i+1);
			break;
		}
		if(!is_numeric($filedata[$i][5])){
			$err_msg="Time Duration ".$filedata[$i][5]." at row ".($i+1);
			break;
		}
		if(!in_array(strtolower($filedata[$i][6]),array_map('strtolower',$commm_clas_arr))){
			$err_msg="Commercial Classification  ".$filedata[$i][6]." at row ".($i+1) ;
			break;
		}
		$GLD_MILESTONE=get_single_value_from_single_table('GMM_ID','gft_pcs_milestone_master','GMM_NAME',$filedata[$i][4]);
		$GLD_COMMERCIAL_CLASSIFICATION=get_single_value_from_single_table('GCCM_ID','gft_commercial_classification_master','GCCM_NAME',$filedata[$i][6]);
		$que1 =  "select GLD_LEAD_CODE from gft_activity where GLD_LEAD_CODE= ".$filedata[$i][2].
				 " and GLD_EMP_ID= ".$filedata[$i][1]." and GLD_MILESTONE= ".$GLD_MILESTONE.
				 " and GLD_TIME_DURATION= " .$filedata[$i][5]." and GLD_COMMERCIAL_CLASSIFICATION= ".$GLD_COMMERCIAL_CLASSIFICATION." and GLD_VISIT_DATE = '".$filedata[$i][0]."'"  ;
		$res=execute_my_query($que1);
		$num_rows=mysqli_num_rows($res);
		if($num_rows>0){
			$err_msg=" Activity Details at row - ".($i+1)." already uploaded." ;
			break;
		}
		
		$i++;
	}
	usort($filedata, 'sortByOrder');
	if(count($filedata)==0){
		$err_msg=" Date value at row 1 ";
	} 
	$PCS_Activity.='<center><table border=1 ><tr><th>S.No</th><th>Date</th><th>Employee Name</th><th>Customer Name</th><th>Milestone</th><th>Time Duration (In Hours)</th><th>Actitvity Details</th><th>Commercial Classification</th></tr>';
	$j=$k=0;
	$prev_lead_code='';
	$output_table='';
	if($err_msg==''){
		while($k<$i){
			if($j==0){ $j++; continue;}
			$visit_date=$filedata[$k][0];
			$employee_id='';
			$employee_id=$filedata[$k][1];
			$LEAD_CODE=$filedata[$k][2];
			if($filedata[$k][7]!=''){
				$GLD_NOTE_ON_ACTIVITY=mysqli_real_escape_string_wrapper($filedata[$k][3]).'-'.$filedata[$k][7];
			}
			else{
				$GLD_NOTE_ON_ACTIVITY=mysqli_real_escape_string_wrapper($filedata[$k][3]);
			}
			$GLD_MILESTONE=get_single_value_from_single_table('GMM_ID','gft_pcs_milestone_master','GMM_NAME',$filedata[$k][4]);
			$GLD_TIME_DURATION=$filedata[$k][5];
			$GLD_COMMERCIAL_CLASSIFICATION=get_single_value_from_single_table('GCCM_ID','gft_commercial_classification_master','GCCM_NAME',$filedata[$k][6]);
			$GLD_MY_COMMENTS_CODE='6';
			$GLD_CUST_FEEDBACK_CODE='7';
			if($LEAD_CODE!='' && $employee_id!='' && $GLD_MILESTONE!='' && $GLD_TIME_DURATION!='' && $GLD_COMMERCIAL_CLASSIFICATION!=''  ){
				$lead_status_q="select glh_status,glh_cust_name from gft_lead_hdr where glh_lead_code='$LEAD_CODE' ";
				$lead_status_r=execute_my_query($lead_status_q);
				$qd=mysqli_fetch_array($lead_status_r);
				$lead_status=$qd['glh_status'];
				$lead_name=$qd['glh_cust_name'];
				$detail_arr = array(
				    'GLD_LEAD_CODE'=>$LEAD_CODE,'GLD_VISIT_NATURE'=>"36",'GLD_EMP_ID'=>$employee_id,
				    'GLD_VISIT_DATE'=>$visit_date,'GLD_NOTE_ON_ACTIVITY'=>$GLD_NOTE_ON_ACTIVITY,
				    'GLD_CUST_FEEDBACK'=>$GLD_CUST_FEEDBACK_CODE,'GLD_MY_COMMENTS_CODE'=>$GLD_MY_COMMENTS_CODE,
				    'GLD_MILESTONE'=>$GLD_MILESTONE,'GLD_TIME_DURATION'=>$GLD_TIME_DURATION,'GLD_COMMERCIAL_CLASSIFICATION'=>$GLD_COMMERCIAL_CLASSIFICATION
				);
				insert_in_gft_activity_table($detail_arr);
				//code to send for subject when file import by PCS Team
				
				if($GLD_COMMERCIAL_CLASSIFICATION=='1'){
						if($prev_lead_code==''){
						$prev_lead_code=$LEAD_CODE;
					}
						
					if($LEAD_CODE!=$prev_lead_code ){
						$query= " select GCC_CONTACT_NO from gft_customer_contact_dtl where gcc_lead_code = '$prev_lead_code ' and  gcc_contact_type=4 ";
						$to_mail_ids='';
						$res=execute_my_query($query);
						if($data1=mysqli_fetch_array($res)){
							$to_mail_ids=$data1[0];
						}
												
						$cc_mail_ids=get_single_value_from_single_table('GCCM_MAIL_TO','gft_commercial_classification_master','GCCM_ID','1');
						$output_table.= '</table></center><br>';
						$db_content_config = array(
								'PCS_Activity'=>array($output_table)
						);
						send_formatted_mail_content($db_content_config, 1, 184, null, null,array($to_mail_ids),null,array($cc_mail_ids));
						$output_table='';
							
					}
					if($output_table==''){
						$output_table= '<center><table border=1 ><tr><th>S.No</th><th>Date</th><th>Employee Name</th><th>Customer Name</th><th>Milestone</th><th>Time Duration (In Hours)</th><th>Actitvity Details</th><th>Commercial Classification</th></tr>';
						$sno=0;
					}
					$sno=$sno+1;
					$output_table.= '<tr><td>'.($sno).'</td><td>'.$visit_date.'</td>';
					$output_table.= '<td>'.get_single_value_from_single_table('gem_emp_name','gft_emp_master','gem_emp_id',$employee_id).'</td>';
					$output_table.= '<td>'.$lead_name.'</td>';
					$output_table.= '<td>'.$filedata[$k][4].'</td>';
					$output_table.= '<td>'.$GLD_TIME_DURATION.'</td>';
					$output_table.= '<td>'.$GLD_NOTE_ON_ACTIVITY.'</td>';
					$output_table.= '<td>'.$filedata[$k][6].'</td></tr>';
					$prev_lead_code=$LEAD_CODE;
				}							
				$PCS_Activity.='<tr><td>'.($k+1).'</td><td>'.$visit_date.'</td>';
				$PCS_Activity.='<td>'.get_single_value_from_single_table('gem_emp_name','gft_emp_master','gem_emp_id',$employee_id).'</td>';
				$PCS_Activity.='<td>'.$lead_name.'</td>';
				$PCS_Activity.='<td>'.$filedata[$k][4].'</td>';
				$PCS_Activity.='<td>'.$GLD_TIME_DURATION.'</td>';
				$PCS_Activity.='<td>'.$GLD_NOTE_ON_ACTIVITY.'</td>';
				$PCS_Activity.='<td>'.$filedata[$k][6].'</td></tr>';
				$k++;
		}//end of if
	}//end of while
	if($output_table!=''){
		$query= " select GCC_CONTACT_NO from gft_customer_contact_dtl where gcc_lead_code = '$prev_lead_code' and  gcc_contact_type=4 ";
		$to_mail_ids=$cc_mail_ids='';
	
		$res=execute_my_query($query);
		if($data2=mysqli_fetch_array($res)){
			$to_mail_ids=$data2[0];
		}
		$output_table.= '</table></center><br>';
		$cc_mail_ids=get_single_value_from_single_table('GCCM_MAIL_TO','gft_commercial_classification_master','GCCM_ID','1');
		$db_content_config = array(
				'PCS_Activity'=>array($output_table)
		);
		send_formatted_mail_content($db_content_config, 1, 184, null, null,array($to_mail_ids),null,array($cc_mail_ids));
	}
	$PCS_Activity.="</table></center><br>";
		mail_send_to_customer_and_pcs($PCS_Activity);
		show_my_alert_msg("Mail Sent Successfully to customer");
	}
	else{
		show_my_alert_msg("Values Not Inserted - Check $err_msg ");
	}
	
	
}//end of file

/**
 * @param string $dispatch_status
 *
 * @return void
 */
function mail_send_of_receipt_dispatched($dispatch_status){
	$group_by_query="select GEM_EMP_ID,GEM_EMP_NAME,GEM_EMAIL,count(*) n_receipts from gft_receipt_letter_dispatch " .
			"inner join gft_receipt_dtl rd on (GRD_RECEIPT_ID_REF=GRL_RECEIPT_ID and grd_lead_code=grl_lead_code) " .
			"inner join gft_emp_master em on (GEM_EMP_ID=grd_emp_id and GEM_STATUS='A' and GEM_EMP_ID<7000) " .
			"where grl_mail_send='N' and GRL_DISPATCHED='$dispatch_status' group by grd_emp_id ";
	$rgroup_by_query=execute_my_query($group_by_query);
	while($qdata_rg=mysqli_fetch_array($rgroup_by_query)){
		$rg_emp_id=$qdata_rg['GEM_EMP_ID'];
		$emp_emailid=$qdata_rg['GEM_EMAIL'];
		$query_m="select grd_emp_id,GLH_LEAD_CODE,GLH_CUST_NAME,GRD_RECEIPT_ID_REF,GRD_RECEIPT_AMT from gft_receipt_letter_dispatch " .
				"inner join gft_receipt_dtl rd on (GRD_RECEIPT_ID_REF=GRL_RECEIPT_ID and grd_lead_code=grl_lead_code) " .
				"inner join gft_lead_hdr lh on (glh_lead_code=grl_lead_Code) " .
				"where grl_mail_send='N' and grd_emp_id='$rg_emp_id' ";
		$query_m.=" and GRL_DISPATCHED='$dispatch_status' ";
		$result_m=execute_my_query($query_m);
		$mail_content="Dear ".$qdata_rg['GEM_EMP_NAME'].",<br>";
		if($dispatch_status=='Y'){
			$mail_content.="  Receipts are sent to the following customers. ";
		}else{
			$mail_content.="  Receipts sent through courier are returned from the following customers. ";
		}
		$sl=1;
		$mail_content.='<center><table border=1 ><tr><th>S.No</th><th>Customer id</th><th>Customer Name</th><th>Receipt id</th><th>Amount in Rs.</th></tr>';
		while($qd=mysqli_fetch_array($result_m)){
			$mail_content.='<tr><td>'.$sl.'</td><td>'.$qd['GLH_LEAD_CODE'].'</td>';
			$mail_content.='<td>'.$qd['GLH_CUST_NAME'].'</td>';
			$mail_content.='<td>'.$qd['GRD_RECEIPT_ID_REF'].'</td>';
			$mail_content.='<td align="right">'.$qd['GRD_RECEIPT_AMT'].'</td></tr>';
			$sl++;
		}
		$mail_content.="</table></center><br>";
		$mail_content.="<hr>";
		$mail_content.="This is a automated message from SAM. ";
		$email_from=get_samee_const('ADMIN_TEAM_MAIL_ID');
		$email_to=$emp_emailid;
		$cc=/*. (string[int]) .*/ array();
		$cc[0]=get_samee_const('AC_MAIL_ID');
		$cc_arr=get_email_addr_reportingmaster($rg_emp_id,false);
		$cc=array_merge($cc,$cc_arr);
		if($dispatch_status=='Y'){
			$subject='Receipts Dispatched';
		}else {
			$subject='Receipts Dispatched letter Returned ';
		}
		$email_from=$reply_to=get_samee_const('ADMIN_TEAM_MAIL_ID');
		send_mail_from_sam($category='41',$email_from,$email_to,$subject,$mail_content,$attachment_file_tosend=null,
		$cc,$content_type=true,$reply_to,$from_page=null,$user_info_needed=false,
		$reply_to_incoming_mailid=null,$fromname=null,$mail_template_id=null,$mail_compile_id=null);
		echo "<br>Mail sent to ".$qdata_rg['GEM_EMP_NAME'];
	}//end of while
	execute_my_query("update gft_receipt_letter_dispatch set grl_mail_send='Y' where grl_mail_send='N'
	and GRL_DISPATCHED='$dispatch_status' ");
}


/**
 * @param string $attachment
 *
 * @return void 
 */
function read_csv_file($attachment){
	$handle_out = fopen("$attachment","r");  
	$content='';
	$i=0;
	$s=0;
	$num=0;$putcomma='';$count_nums_split=0;$contact_nos='';$num_rows_affected=0;
	$i=0;$dispatch_status='';
	while (($data = fgetcsv($handle_out, 1000, ",")) !== FALSE) {
		if($i==0){	$i++; continue; }
		$emp_id=$data[0];
		$designation=$data[2];
		$department=$data[3];
		$functionVar=$data[4];
		$division=$data[5];
		$ins_query="update gft_emp_master set GEM_TITLE='$designation',GEM_DEPARTMENT='$department',GEM_FUNCTION='$functionVar',GEM_DIVISION='$division' where gem_emp_id='$emp_id' ;";
		$content.=$ins_query."\n";
		print $ins_query;
	}
	
	write_to_file($folder_name='Emp_Details',$content,$filename='emp_update.sql',$mode='w',$move_onemore_folder=false);
	
}

/**
 * @param string $attachment
 *
 * @return void 
 */
function receipt_letter_dispacth_import($attachment){
	$handle_out = fopen("$attachment","r");  
	$i=0;
	$s=0;
	$num=0;$putcomma='';$count_nums_split=0;$contact_nos='';$num_rows_affected=0;
	$i=0;$dispatch_status='';
	$dispatch_date='';
	while (($data = fgetcsv($handle_out, 1000, ",")) !== FALSE) {
		if($i==0){ 
			if(trim($data[0])=='Receipt Barcode'){$i++; continue;}
			else {echo 'Heading shows Different .Please check Received column header 1 as '.trim($data[0]);  return; }
		}
		$barcode_read=$data[0];
		$barcode_read_arr=explode('-',$barcode_read);
		$lead_code=$barcode_read_arr[0];
		$receipt_id=$barcode_read_arr[1];
		if($data[1]!=''){	$dispatch_date=$data[1]; }
		if($data[2]!=''){	$dispatch_status=$data[2]; }
		if($lead_code!='' and $receipt_id!='' and $dispatch_date!='' and $dispatch_status!=''){
			
			if($dispatch_status=='Y'){
				execute_my_query("update gft_receipt_letter_dispatch set GRL_LATEST='N' WHERE GRL_LEAD_CODE='$lead_code' " .
							"and GRL_RECEIPT_ID='$receipt_id' ");
			
				$query_insert="replace into gft_receipt_letter_dispatch(GRL_LEAD_CODE ,
				GRL_RECEIPT_ID ,GRL_DISPATCH_DATE ,GRL_DISPATCHED,GRL_MAIL_SEND,GRL_LATEST)
				values('$lead_code','$receipt_id','$dispatch_date','$dispatch_status','N','Y')";
				execute_my_query($query_insert);
			
				$update_receipt_dtl="update gft_receipt_dtl set GRD_RECEIPT_DISPATCHED='Y',
				GRD_RECEIPT_DISPATCH_DATE='$dispatch_date' 
				where GRD_RECEIPT_ID_REF='$receipt_id' and GRD_LEAD_CODE='$lead_code' ";
				execute_my_query($update_receipt_dtl);
			}else if($dispatch_status=='R'){
				$reason=mysqli_real_escape_string_wrapper($data[3]);
				$query_update="update gft_receipt_letter_dispatch set GRL_DISPATCHED='R',
				GRL_REASON='$reason',GRL_MAIL_SEND='N' where GRL_LEAD_CODE='$lead_code' 
				and GRL_RECEIPT_ID='$receipt_id' and GRL_DISPATCHED='Y' AND GRL_LATEST='Y'  "; 
				execute_my_query($query_update);
				$update_receipt_dtl="update gft_receipt_dtl set GRD_RECEIPT_DISPATCHED='R'  
				where GRD_RECEIPT_ID_REF='$receipt_id' and GRD_LEAD_CODE='$lead_code' 
				and GRD_RECEIPT_DISPATCHED='Y' ";
				execute_my_query($update_receipt_dtl);
				update_address_invalid($lead_code,$reason);
			}
		}	
		$i++;
	}/* end of while */
	
	/*MAIL SEND*/
	mail_send_of_receipt_dispatched($dispatch_status);
		
	echo "<br><b>Thanks for Updating Receipt Dispatch / Return .</b><br>";
}//end of file

/**
 * @param string $dispatch_status
 *
 * @return void
 */
function mail_send_of_asa_dispatched($dispatch_status){
	$group_by_query="select GEM_EMP_ID,GEM_EMP_NAME,GEM_EMAIL,count(*) n_asa_letters from gft_asa_letter_dispatch " .
			"inner join gft_install_dtl_new  ins on (GID_LEAD_CODE=GSL_LEAD_CODE AND GID_INSTALL_ID=GSL_INSTALL_ID) " .
			" inner join gft_lead_hdr lh on (GLH_LEAD_CODE=GID_LEAD_CODE)" .
			" inner join gft_emp_master em on (GEM_EMP_ID=GLH_FIELD_INCHARGE) " .
			"where GSL_MAIL_SEND='N' and GSL_DISPATCHED='$dispatch_status' group by gem_emp_id ";
	$rgroup_by_query=execute_my_query($group_by_query);
	$num_rows_to_update=mysqli_num_rows($rgroup_by_query);
	while($qdata_rg=mysqli_fetch_array($rgroup_by_query)){
		$rg_emp_id=$qdata_rg['GEM_EMP_ID'];
		$emp_emailid=$qdata_rg['GEM_EMAIL'];
		$query_m="select gem_emp_id,GLH_LEAD_CODE,GLH_CUST_NAME,GID_ORDER_NO,GID_HEAD_OF_FAMILY,
		GID_FULLFILLMENT_NO,GID_REF_SERIAL_NO,GID_VALIDITY_DATE,GPM_PRODUCT_ABR AS PROD from gft_asa_letter_dispatch " .
		"inner join gft_install_dtl_new  ins on (GID_LEAD_CODE=GSL_LEAD_CODE AND GID_INSTALL_ID=GSL_INSTALL_ID) " .
		" inner join gft_lead_hdr lh on (GLH_LEAD_CODE=GID_LEAD_CODE)" .
		" inner join gft_product_family_master pfm on (GPM_PRODUCT_CODE=GID_HEAD_OF_FAMILY) ".
		" inner join gft_emp_master em on (GEM_EMP_ID=GLH_FIELD_INCHARGE) " .
		"where GSL_MAIL_SEND='N' and gem_emp_id='$rg_emp_id' ";
		$query_m.=" and GSL_DISPATCHED='$dispatch_status' ";
		$result_m=execute_my_query($query_m);
		$mail_content="Dear ".$qdata_rg['GEM_EMP_NAME'].",<br>";
		if($dispatch_status=='Y'){
			$mail_content.="  ASA letter are sent to the following customers ."; }
			else {
				$mail_content.="  ASA letter are returned from the following customers .";
			}
			$sl=1;
			$mail_content.='<center><table border="1"><tr><th>S.No</th><th>Customer id</th><th>Customer Name</th><th>Product</th>
		<th>Order No</th><th>Fullfillment No</th><th>ASA Expiry Date</th></tr>';
			while($qd=mysqli_fetch_array($result_m)){
				$mail_content.='<tr><td>'.$sl.'</td><td>'.$qd['GLH_LEAD_CODE'].'</td>';
				$mail_content.='<td>'.$qd['GLH_CUST_NAME'].'</td>';
				$mail_content.='<td>'.$qd['PROD'].'</td>';
				$mail_content.='<td align="right">'.$qd['GID_ORDER_NO'].'</td>';
				$mail_content.='<td>'.$qd['GID_FULLFILLMENT_NO'].'</td>';
				$mail_content.='<td>'.$qd['GID_VALIDITY_DATE'].'</td>';
				$mail_content.='</tr>';
					
				$sl++;
			}
			$mail_content.="</table></center><br>";
			$mail_content.="<hr>";
			$mail_content.="This is a automated message from SAM. ";
			$email_from=get_samee_const('ADMIN_TEAM_MAIL_ID');
			$email_to=$emp_emailid;
			$cc=/*. (string[int]) .*/ array();
			$asa_incharge_list=get_employee_in_array($zone_id=null,$region_id=null,$area_id=null,$terr_id=null,
					$country_id=null,$state_id=null,$district_id=null,$group_arr=array(54));

			for($i=0;$i<count($asa_incharge_list);$i++){
				$cc[$i]=$asa_incharge_list[$i][2];
			}
			/*ACCOUNTS RECEIVALBLE PERSONS */
			$cc_arr=get_email_addr_reportingmaster($rg_emp_id,false);
			$cc=array_merge($cc,$cc_arr);
			//array_push($cc,get_samee_const('GFT_CUST_CARE_EMAILID'));
			if($dispatch_status=='Y'){
				$subject='ASA Letter Dispatched';
			}else {
				$subject='ASA Letter Returned ';
			}
			$reply_to=get_samee_const('ADMIN_TEAM_MAIL_ID');
			send_mail_from_sam($category=43,$email_from,$email_to,$subject,$mail_content,$attachment_file_tosend=null,
			$cc,$content_type=true,$reply_to,$from_page=null,$user_info_needed=false,
			$reply_to_incoming_mailid=null,$fromname=null,$mail_template_id=null,$mail_compile_id=null);
			echo "<br>Mail sent to ".$qdata_rg['GEM_EMP_NAME'];
	}//end of while
	if($num_rows_to_update>0){
		execute_my_query("update gft_asa_letter_dispatch set gsl_mail_send='Y' where gsl_mail_send='N'
		and GSL_DISPATCHED='$dispatch_status' ");
	}
}


/**
 * @param string $attachment
 *
 * @return void 
 */
function asa_letter_dispacth_import($attachment){
	$handle_out = fopen("$attachment","r");
	/* <install_id>-<lead_code>*/  
	$i=0;
	$s=0;
	$num=0;$putcomma='';$count_nums_split=0;$contact_nos='';$num_rows_affected=0;
	$i=0;$dispatch_date='';
	$dispatch_status='';

	while (($data = fgetcsv($handle_out, 1000, ",")) !== FALSE) {
		if($i==0){ 
			if(trim($data[0])=='ASA Barcode'){$i++; continue;}
			else {echo 'Heading shows Different'; exit; }
		}		
		$barcode_read=$data[0];
		$barcode_read_arr=explode('-',$barcode_read);
		$cnt_br=count($barcode_read_arr);
		$install_id=$barcode_read_arr[0];	
		$lead_code=(isset($barcode_read_arr[1])?$barcode_read_arr[1]:'');
		if($data[1]!=''){	$dispatch_date=$data[1];}
		if($data[2]!=''){	$dispatch_status=$data[2];	}
		if($lead_code=='' && $install_id!=''){
			$query_get_lead_code="select GID_LEAD_CODE from gft_install_dtl_new where gid_install_id='$install_id' ";
			$result_get_lead_code=execute_my_query($query_get_lead_code);
			$qdl=mysqli_fetch_array($result_get_lead_code);
			$lead_code=$qdl['GID_LEAD_CODE'];
			if($lead_code==''){
				continue;
			}
		}else {
			$query_get_lead_code="select GID_LEAD_CODE from gft_install_dtl_new where gid_install_id='$install_id' and gid_lead_code='$lead_code' ";
			$result_get_lead_code=execute_my_query($query_get_lead_code);
			if(mysqli_num_rows($result_get_lead_code)!=1){
			   continue;
			}
		}
		if($lead_code!='' and $install_id!='' and $dispatch_date!='' and $dispatch_status!=''){
			if($dispatch_status=='Y'){
					execute_my_query("update gft_asa_letter_dispatch set GSL_LATEST='N' WHERE GSL_LEAD_CODE='$lead_code' " .
							"and GSL_INSTALL_ID='$install_id' ");
					$query_insert="replace into gft_asa_letter_dispatch (
					GSL_LEAD_CODE ,GSL_DISPATCH_DATE,GSL_DISPATCHED,GSL_REASON,GSL_INSTALL_ID,GSL_LATEST) 
					values('$lead_code','$dispatch_date','$dispatch_status','','$install_id','Y')";
					execute_my_query($query_insert);
					$update_receipt_dtl="update gft_install_dtl_new set GID_ASA_LETTER_DISPATCHED='Y',
					GID_ASA_LETTER_DISPATCHED_DATE='$dispatch_date'  
					where GID_LEAD_CODE='$lead_code' and GID_INSTALL_ID='$install_id' ";
					execute_my_query($update_receipt_dtl);
			}else if($dispatch_status=='R'){
					$reason=mysqli_real_escape_string_wrapper($data[3]);
					$query_update="update gft_asa_letter_dispatch set GSL_DISPATCHED='R',GSL_MAIL_SEND='N',
					GSL_REASON='$reason' where GSL_LEAD_CODE='$lead_code' AND GSL_INSTALL_ID='$install_id'    
					and	GSL_LATEST='Y' and GSL_DISPATCHED='Y' "; 
					execute_my_query($query_update);
					$update_receipt_dtl="update gft_install_dtl_new set GID_ASA_LETTER_DISPATCHED='R' 					
					where GID_LEAD_CODE='$lead_code' AND GID_INSTALL_ID='$install_id' and  GID_ASA_LETTER_DISPATCHED='Y'  ";
					execute_my_query($update_receipt_dtl);
					
					update_address_invalid($lead_code,$reason);
			}
		}
		$i++;
	}/* end of while */
	if($dispatch_date!=''){
		/* update in MIS */
		update_asa_letter_sent_undelivered($dispatch_date);
	}
	/*MAIL SEND*/
	mail_send_of_asa_dispatched($dispatch_status);
	echo "<br><br>Thanks for updating";
}//end of fn


/**
 * @param string $dispatch_status
 *
 * @return void
 */
function mail_send_of_upgrade_dispatched($dispatch_status){
	$group_by_query="select GEM_EMP_ID,GEM_EMP_NAME,GEM_EMAIL,count(*) n_asa_letters from gft_upgrade_letter_dispatch " .
			"inner join gft_install_dtl_new  ins on (GID_LEAD_CODE=GUL_LEAD_CODE AND GID_INSTALL_ID=GUL_INSTALL_ID) " .
			"inner join gft_lead_hdr lh on (GLH_LEAD_CODE=GID_LEAD_CODE)" .
			"inner join gft_emp_master em on (GEM_EMP_ID=GLH_FIELD_INCHARGE AND GEM_STATUS='A') " .
			"where GUL_MAIL_SEND='N' and GUL_DISPATCHED='$dispatch_status' group by gem_emp_id ";
	$rgroup_by_query=execute_my_query($group_by_query);
	$num_rows_to_update=mysqli_num_rows($rgroup_by_query);
	while($qdata_rg=mysqli_fetch_array($rgroup_by_query)){
		$rg_emp_id=$qdata_rg['GEM_EMP_ID'];
		$emp_emailid=$qdata_rg['GEM_EMAIL'];
		$query_m="select gem_emp_id,GLH_LEAD_CODE,GLH_CUST_NAME,GID_ORDER_NO,GID_HEAD_OF_FAMILY,
		GID_FULLFILLMENT_NO,GID_REF_SERIAL_NO,GID_VALIDITY_DATE,GPM_PRODUCT_ABR AS PROD from gft_upgrade_letter_dispatch " .
		"inner join gft_install_dtl_new  ins on (GID_LEAD_CODE=GUL_LEAD_CODE AND GID_INSTALL_ID=GUL_INSTALL_ID) " .
		" inner join gft_lead_hdr lh on (GLH_LEAD_CODE=GID_LEAD_CODE)" .
		" inner join gft_product_family_master pfm on (GPM_PRODUCT_CODE=GID_HEAD_OF_FAMILY) ".
		" inner join gft_emp_master em on (GEM_EMP_ID=GLH_FIELD_INCHARGE) " .
		"where GUL_MAIL_SEND='N' and gem_emp_id='$rg_emp_id' ";
		$query_m.=" and GUL_DISPATCHED='$dispatch_status' ";
		$result_m=execute_my_query($query_m);
		$mail_content="Dear ".$qdata_rg['GEM_EMP_NAME'].",<br>";
		if($dispatch_status=='Y'){
			$mail_content.="  Upgradation letter are sent to the following customers .";
		}else {
			$mail_content.="  Upgradation letter are returned from the following customers .";
		}
		$sl=1;
		$mail_content.='<center><table border=1><tr><th>S.No</th><th>Customer id</th><th>Customer Name</th><th>Product</th>
		<th>Order No</th><th>Fullfillment No</th><th>ASA Expiry Date</th></tr>';
		while($qd=mysqli_fetch_array($result_m)){
			$mail_content.='<tr><td>'.$sl.'</td><td>'.$qd['GLH_LEAD_CODE'].'</td>';
			$mail_content.='<td>'.$qd['GLH_CUST_NAME'].'</td>';
			$mail_content.='<td>'.$qd['PROD'].'</td>';
			$mail_content.='<td align="right">'.$qd['GID_ORDER_NO'].'</td>';
			$mail_content.='<td>'.$qd['GID_FULLFILLMENT_NO'].'</td>';
			$mail_content.='<td>'.$qd['GID_VALIDITY_DATE'].'</td>';
			$mail_content.='</tr>';
				
			$sl++;
		}
		$mail_content.="</table></center><br>";
		$mail_content.="<hr>";
		$mail_content.="This is a automated message from SAM. ";
		$email_from=get_samee_const('ADMIN_TEAM_MAIL_ID');
		$email_to=$emp_emailid;
		$cc=/*. (string[int]) .*/ array();
		//$cc[0]=get_email_addr($uid);
		$asa_incharge_list=get_employee_in_array($zone_id=null,$region_id=null,$area_id=null,$terr_id=null,
				$country_id=null,$state_id=null,$district_id=null,$group_arr=array(54));

		for($i=0;$i<count($asa_incharge_list);$i++){
			$cc[$i]=$asa_incharge_list[$i][2];
		}
		/*ACCOUNTS RECEIVALBLE PERSONS */
		$cc_arr=get_email_addr_reportingmaster($rg_emp_id,false);
		$cc=array_merge($cc,$cc_arr);
		array_push($cc,get_samee_const('FORNTOFFICE_MAIL_ID'));
		if($dispatch_status=='Y'){
			$subject='Upgrade Letter Dispatched';
		}else {
			$subject='Upgrade Letter Returned ';
		}
		$reply_to=get_samee_const('ADMIN_TEAM_MAIL_ID');
		send_mail_from_sam($category=43,$email_from,$email_to,$subject,$mail_content,$attachment_file_tosend=null,
		$cc,$content_type=true,$reply_to,$from_page=null,$user_info_needed=false,
		$reply_to_incoming_mailid=null,$fromname=null,$mail_template_id=null,$mail_compile_id=null);
		echo "<br>Mail sent to ".$qdata_rg['GEM_EMP_NAME'];
	}//end of while
	if($num_rows_to_update>0){
		execute_my_query("update gft_upgrade_letter_dispatch set gul_mail_send='Y' where gul_mail_send='N'
		and GUL_DISPATCHED='$dispatch_status' ");
	}
}

/** 
 * @param string $attachment
 *
 * @return void
 */
function upgrade_letter_dispacth_import($attachment){
	$handle_out = fopen("$attachment","r");
	/* <install_id>-<lead_code>*/  
	$i=0;
	$s=0;
	$num=0;$putcomma='';$count_nums_split=0;$contact_nos='';$num_rows_affected=0;
	$i=0;
	$dispatch_date='';
	$dispatch_status='';

	while (($data = fgetcsv($handle_out, 1000, ",")) !== FALSE) {
		if($i==0){ 
			if(trim($data[0])=='Upgrade Barcode'){$i++; continue;}
			else {echo 'Heading shows Different'; exit; }
		}		
		$barcode_read=$data[0];
		$barcode_read_arr=explode('-',$barcode_read);
		$cnt_br=count($barcode_read_arr);
		$install_id=$barcode_read_arr[0];	
		$lead_code=(isset($barcode_read_arr[1])?$barcode_read_arr[1]:'');
		if($data[1]!=''){			$dispatch_date=$data[1];		}
		if($data[2]!=''){		$dispatch_status=$data[2];		}
        if($lead_code=='' && $install_id!=''){
			$query_get_lead_code="select GID_LEAD_CODE from gft_install_dtl_new where gid_install_id='$install_id' ";
			$result_get_lead_code=execute_my_query($query_get_lead_code);
			$qdl=mysqli_fetch_array($result_get_lead_code);
			$lead_code=$qdl['GID_LEAD_CODE'];
			if($lead_code==''){
				continue;
			}
		}else {
			$query_get_lead_code="select GID_LEAD_CODE from gft_install_dtl_new where gid_install_id='$install_id' and gid_lead_code='$lead_code' ";
			$result_get_lead_code=execute_my_query($query_get_lead_code);
			if(mysqli_num_rows($result_get_lead_code)!=1){
			   continue;
			}
		}
		
		if($lead_code!='' and $install_id!='' and $dispatch_date!='' and $dispatch_status!=''){
			
			if($dispatch_status=='Y' ){
					execute_my_query("update gft_upgrade_letter_dispatch set GUL_LATEST='N' WHERE GUL_LEAD_CODE='$lead_code' " .
							"and GUL_INSTALL_ID='$install_id' ");
					$query_insert="replace into gft_upgrade_letter_dispatch (
					GUL_LEAD_CODE ,GUL_DISPATCH_DATE,GUL_DISPATCHED,GUL_REASON,GUL_INSTALL_ID,GUL_LATEST) 
					values('$lead_code','$dispatch_date','$dispatch_status','','$install_id','Y')";
					execute_my_query($query_insert);
					$update_receipt_dtl="update gft_install_dtl_new set GID_UPGRADE_LETTER_DISPATCHED='Y',
					GID_UPGRADE_LETTER_DISPATCHED_DATE='$dispatch_date'  
					where GID_LEAD_CODE='$lead_code' and GID_INSTALL_ID='$install_id' ";
					execute_my_query($update_receipt_dtl);
			}else if($dispatch_status=='R'){
					$reason=mysqli_real_escape_string_wrapper($data[3]);
					$query_update="update gft_upgrade_letter_dispatch set GUL_DISPATCHED='R',GUL_MAIL_SEND='N',
					GUL_REASON='$reason' where GUL_LEAD_CODE='$lead_code' AND GUL_INSTALL_ID='$install_id'    
					and	GUL_LATEST='Y' AND  GUL_DISPATCHED='Y'"; 
					execute_my_query($query_update);
					$update_receipt_dtl="update gft_install_dtl_new set GID_UPGRADE_LETTER_DISPATCHED='R',
					GID_UPGRADE_LETTER_DISPATCHED_DATE='$dispatch_date'  
					where GID_LEAD_CODE='$lead_code' AND GID_INSTALL_ID='$install_id' and GID_UPGRADE_LETTER_DISPATCHED='Y' ";
					execute_my_query($update_receipt_dtl);
					update_address_invalid($lead_code,$reason);
			}
		}	
				
		$i++;
	}/* end of while */
	if($dispatch_date!=''){
		update_upgrade_letter_sent_undelivered($dispatch_date);
	}
	/*MAIL SEND*/
	mail_send_of_upgrade_dispatched($dispatch_status);
	echo "<br><br>Thanks for updating";
}//end of fn

/**
 * @param string $dispatch_status
 *
 * @return void
 */
function mail_send_of_feedback_letter_dispatched($dispatch_status){
	$group_by_query="select GEM_EMP_ID,GEM_EMP_NAME,GEM_EMAIL,count(*) n_asa_letters from gft_feedback_letter_dispatch " .
			"inner join gft_lead_hdr lh on (GLH_LEAD_CODE=GFL_LEAD_CODE)" .
			"inner join gft_emp_master em on (GEM_EMP_ID=GLH_FIELD_INCHARGE AND GEM_STATUS='A') " .
			"where GFL_MAIL_SEND='N' and GFL_DISPATCHED='$dispatch_status' and  GFL_LATEST='Y' group by gem_emp_id ";
	$rgroup_by_query=execute_my_query($group_by_query);
	$num_rows_to_update=mysqli_num_rows($rgroup_by_query);
	while($qdata_rg=mysqli_fetch_array($rgroup_by_query)){
		$rg_emp_id=$qdata_rg['GEM_EMP_ID'];
		$emp_emailid=$qdata_rg['GEM_EMAIL'];
		$query_m="select gem_emp_id,GLH_LEAD_CODE,GLH_CUST_NAME,GID_ORDER_NO,group_concat(distinct(GID_VALIDITY_DATE)) AS GID_VALIDITY_DATE," .
				" group_concat(distinct(GPM_PRODUCT_ABR)) AS PROD from gft_feedback_letter_dispatch " .
				" inner join gft_install_dtl_new  ins on (GID_LEAD_CODE=GFL_LEAD_CODE ) " .
				" inner join gft_lead_hdr lh on (GLH_LEAD_CODE=GID_LEAD_CODE)" .
				" inner join gft_product_family_master pfm on (GPM_PRODUCT_CODE=GID_HEAD_OF_FAMILY) ".
				" inner join gft_emp_master em on (GEM_EMP_ID=GLH_FIELD_INCHARGE) " .
				"where GFL_MAIL_SEND='N' and gem_emp_id='$rg_emp_id' ";
		$query_m.=" and GFL_DISPATCHED='$dispatch_status' ";
		$query_m.=" group by GLH_LEAD_CODE ";
		$result_m=execute_my_query($query_m);
		$mail_content="Dear ".$qdata_rg['GEM_EMP_NAME'].",<br>";

		if($dispatch_status=='Y'){
			$subject='Feedback Letter Dispatched';
			$mail_content.="  Feedback Form  are sent to the following customers through Courier .";
		}else {
			$subject='Feedback Letter Returned ';
			$mail_content.="  Feedback Form  are returned from the following customers through Courier .";
		}
		$sl=1;
		$mail_content.='<center><table border=1><tr><th>S.No</th><th>Customer id</th><th>Customer Name</th><th>Product(s)</th>
		<th>ASA/SUB Expiry Date</th></tr>';
		while($qd=mysqli_fetch_array($result_m)){
			$mail_content.='<tr><td>'.$sl.'</td><td>'.$qd['GLH_LEAD_CODE'].'</td>';
			$mail_content.='<td>'.$qd['GLH_CUST_NAME'].'</td>';
			$mail_content.='<td>'.$qd['PROD'].'</td>';
			$mail_content.='<td>'.$qd['GID_VALIDITY_DATE'].'</td>';
			$mail_content.='</tr>';
				
			$sl++;
		}
		$mail_content.="</table></center><br>";
		$mail_content.="<hr>";
		$mail_content.="This is a automated message from SAM. ";
		$email_from=get_samee_const('ADMIN_TEAM_MAIL_ID');
		$email_to=$emp_emailid;
		$cc=/*. (string[int]) .*/ array();
		//$cc[0]=get_email_addr($uid);
		$asa_incharge_list=get_employee_in_array($zone_id=null,$region_id=null,$area_id=null,$terr_id=null,
				$country_id=null,$state_id=null,$district_id=null,$group_arr=array(54));

		for($i=0;$i<count($asa_incharge_list);$i++){
			$cc[$i]=$asa_incharge_list[$i][2];
		}
		/*ACCOUNTS RECEIVALBLE PERSONS */
		$cc_arr=get_email_addr_reportingmaster($rg_emp_id,false);
		$cc=array_merge($cc,$cc_arr);
		array_push($cc,get_samee_const('FORNTOFFICE_MAIL_ID'));

		$reply_to=get_samee_const('ADMIN_TEAM_MAIL_ID');
		send_mail_from_sam($category=43,$email_from,$email_to,$subject,$mail_content,$attachment_file_tosend=null,
		$cc,$content_type=true,$reply_to,$from_page=null,$user_info_needed=false,
		$reply_to_incoming_mailid=null,$fromname=null,$mail_template_id=null,$mail_compile_id=null);
		echo "<br>Mail sent to ".$qdata_rg['GEM_EMP_NAME'];
	}//end of while
	if($num_rows_to_update>0){
		execute_my_query("update gft_feedback_letter_dispatch set GFL_MAIL_SEND='Y' where GFL_MAIL_SEND='N'
		and GFL_DISPATCHED='$dispatch_status' AND GFL_LATEST='Y' ");
	}
}

/** 
 * @param string $attachment
 *
 * @return void
 */
function feedback_letter_dispacth_import($attachment){
	$handle_out = fopen("$attachment","r");
	$i=0;	$s=0;	$num=0;$putcomma='';$count_nums_split=0;$contact_nos='';$num_rows_affected=0;
	$dispatch_date='';
	$dispatch_status='';

	while (($data = fgetcsv($handle_out, 1000, ",")) !== FALSE) {
		if($i==0){ 
			if(trim($data[0])=='Customer Id Barcode'){$i++; continue;}
			else {echo 'Heading shows Different'; exit; }
		}		
		$lead_code=$data[0];
		if($data[1]!=''){			$dispatch_date=$data[1];		}
		if($data[2]!=''){		$dispatch_status=$data[2];		}
        if($lead_code==''){
				continue;
		}
				
		if($lead_code!='' and $dispatch_date!='' and $dispatch_status!=''){
			
			if($dispatch_status=='Y' ){
					execute_my_query("update gft_feedback_letter_dispatch set GFL_LATEST='N' WHERE GFL_LEAD_CODE='$lead_code' " .
							"and GFL_DISPATCH_DATE!='$dispatch_date' ");
					$query_insert="insert ignore into gft_feedback_letter_dispatch(
					GFL_LEAD_CODE,GFL_DISPATCH_DATE,GFL_DISPATCHED,GFL_REASON) 
					values('$lead_code','$dispatch_date','$dispatch_status','')";
					execute_my_query($query_insert);
					
			}else if($dispatch_status=='R'){
					$reason=mysqli_real_escape_string_wrapper($data[3]);
					$query_update="update gft_feedback_letter_dispatch set GFL_DISPATCHED='R',GFL_MAIL_SEND='N',
					GFL_REASON='$reason' where GFL_LEAD_CODE='$lead_code' and  GFL_LATEST='Y' and GFL_DISPATCHED='Y' "; 
					execute_my_query($query_update);
					update_address_invalid($lead_code,$reason);
					
			}
		}	
				
		$i++;
	}/* end of while */
	/*MAIL SEND*/
	mail_send_of_feedback_letter_dispatched($dispatch_status);
	echo "<br><br>Thanks for updating";
}//end of fn

/**
 * @param string $attached_file
 * 
 * @return void
 */
function temporay_pincode_list_import($attached_file){
	$data_arr = data_from_attachemnet_file($attached_file,'csv');
	$insert_val = "";
	$put_comma = "";
	foreach ($data_arr as $key => $val_arr){
		$val_arr = array_trim($val_arr);
		if($key==0){
			if($val_arr[0]!='officename'){
				die('officename is not present in first column');
			}elseif ($val_arr[1]!='pincode'){
				die('pincode is not present in second column');
			}elseif ($val_arr[2]!='divisionname'){
				die('divisionname is not present in third column');
			}elseif ($val_arr[3]!='regionname'){
				die('regionname is not present in fourth column');
			}elseif ($val_arr[4]!='taluk'){
				die('taluk is not present in fifth column');
			}elseif ($val_arr[5]!='districtname'){
				die('districtname is not present in sixth column');
			}elseif ($val_arr[6]!='statename'){
				die('statename is not present in seventh column');
			}
		}else{
			foreach ($val_arr as $k => $v){
				$val_arr[$k] = str_replace("+ACY-", "and", mysqli_real_escape_string_wrapper($v));
			}
			$insert_val .= $put_comma."('$val_arr[0]','$val_arr[1]','$val_arr[2]','$val_arr[3]','$val_arr[4]','$val_arr[5]','$val_arr[6]')";
			$put_comma = ",";
		}
	}
	if($insert_val!=''){
		$insert_query = " insert into temp_pincode_list (TPL_LOCATION_NAME,TPL_PINCODE,TPL_DIVISION_NAME,TPL_REGION_NAME,TPL_TALUK_NAME, ".
				" TPL_DISTRICT_NAME,TPL_STATE_NAME) values $insert_val ";
		execute_my_query($insert_query);
	}
	echo "imported";
}

/**
 * @return void
 */
function mail_send_of_address_invalid(){
	$query=" select count(*) from gft_audit_log_edit_table where date(gul_time)=date(now()) ";
	$result=execute_my_query($query);
	$qd=mysqli_fetch_array($result);
	echo "Num of Records / Customer Id updated is ".$qd[0];

	$query="select GEM_EMP_ID,GEM_EMP_NAME,GEM_EMAIL,count(*) cnt from gft_lead_hdr " .
			"inner join gft_emp_master em on (gem_emp_id=glh_lfd_emp_id and gem_status='A') " .
			"where GLH_ADDRESS_VERIFIED='I' and GLH_ADDRESS_VERIFIED_DATE=date(now()) " .
			"group by GEM_EMP_ID ";

	$result=execute_my_query($query);
	while($qdata=mysqli_fetch_array($result)){
		$rg_emp_id=$qdata['GEM_EMP_ID'];
		$emp_emailid=$qdata['GEM_EMAIL'];
		$cnt=$qdata['cnt'];
		$mail_content="Dear ".$qdata['GEM_EMP_NAME'].',<br>';
		$mail_content.="We identified ".$cnt." prospects/customers address has invalid.";
		$mail_content.="<br>In Customer Report you can get the details  or please " .
				" <a href=\"http://sam.gofrugal.com/cust_Reports.php?from_dt=&to_dt=&address_verified=I&emp_code=$rg_emp_id\">Click Here </a>";
		$mail_content.="<br><hr>";
		$mail_content.="This is a automated message from SAM. ";
		$email_from=get_samee_const('ADMIN_TEAM_MAIL_ID');
		$email_to=$emp_emailid;
		global $uid;
		$cc=/*. (string[int]) .*/ array();
		$cc[0]=get_email_addr($uid);
		$cc_arr=get_email_addr_reportingmaster($rg_emp_id,false);
		$cc=array_merge($cc,$cc_arr);
		$subject='Address Identified as Invalid';
		$reply_to=get_samee_const('ADMIN_TEAM_MAIL_ID');
		send_mail_from_sam($category='Address Invalid',$email_from,$email_to,$subject,$mail_content,$attachment_file_tosend=null,
		$cc,$content_type=true,$reply_to,$from_page=null,$user_info_needed=false,
		$reply_to_incoming_mailid=null,$fromname=null,$mail_template_id=null,$mail_compile_id=null);
		echo "<br>Mail sent to ".$qdata['GEM_EMP_NAME'];

	}
}


/** 
 * @param string $attachment
 *
 * @return void
 */
function address_invalid_mark($attachment){
	$handle_out = fopen("$attachment","r");  
	$i=0;
	$s=0;
	$num=0;$putcomma='';$count_nums_split=0;$contact_nos='';$num_rows_affected=0;
	$i=0;$dispatch_status='';
	while (($data = fgetcsv($handle_out, 1000, ",")) !== FALSE) {
		if($i==0){ 
			if(trim($data[0])=='Address Invalid'){$i++; continue;}
			else {echo 'Heading shows Different'; exit; }
		}
		$lead_code=$data[0];
		if($lead_code=='') continue;
		update_address_invalid($lead_code,'');
		$i++;
	}/* end of while */
	
	/*MAIL SEND*/
	mail_send_of_address_invalid();
		
	echo "<br><b>Thanks for Updating.</b><br>";
}//end of file

/**
 * @param string $upload_directory
 * @param string $file_param
 *
 * @return string
 */
function upload_single_file($upload_directory,$file_param){
    $fpath = "";
    $fsize = isset($_FILES[$file_param]['size'])?(int)$_FILES[$file_param]['size']:0;
    if($fsize > 0){
        if(!file_exists($upload_directory)){
            mkdir($upload_directory,0777,true);
        }
        $temp_path = $upload_directory."/".uniqid()."_".str_replace(" ","_",basename($_FILES[$file_param]['name']));
        if(move_uploaded_file($_FILES[$file_param]['tmp_name'],$temp_path)) {
            $fpath = $temp_path;
        }else{
            error_log("file upload error in 'upload_single_file' function");
        }
    }
    return $fpath;
}

/**
 * @param string $original_image_path
 * @param int $width
 * @param int $height
 * @param string $save_new_file_path
 *
 * @return string
 */
function get_resized_image($original_image_path,$width,$height,$save_new_file_path){
    $temp_img = smart_resize_image($original_image_path, null, $width,$height,false,'return',false,false,100);
    if(imagejpeg($temp_img, $save_new_file_path)){
        return $save_new_file_path;
    }
    return '';
}

/**
 * @param  string $source
 * @param  string $destination
 * @param  string
 * 
 * @return boolean
 */
function zipFile($source, $destination, $flag = ''){
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }
    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }
    $source = str_replace('\\', '/', realpath($source));
    if($flag){
        $flag = basename($source) . '/';
    }
    if (is_dir($source) === true) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($files as $file){
            $file = str_replace('\\', '/', realpath($file));
            if (is_dir($file) === true){
                $zip->addEmptyDir(str_replace($source . '/', '', $flag.$file . '/'));
            }else if (is_file($file) === true){
                $zip->addFromString(str_replace($source . '/', '', $flag.$file), file_get_contents($file));
            }
        }
    }else if (is_file($source) === true){
        $zip->addFromString($flag.basename($source), file_get_contents($source));
    }
    return $zip->close();
}

?>
