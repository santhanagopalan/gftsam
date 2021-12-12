<?php
require_once(__DIR__ ."/inc.essentials_for_popup.php");
require_once(__DIR__ ."/file_util.php");
$uid=$_SESSION['uid'];
if(isset($_POST['report_an_issue'])){
    $email_from=$cc=get_email_addr($uid);
    $email_to=get_samee_const("SAM_SUPPORT_MAIL_ID");
    $summary = isset($_POST['summary'])?$_POST['summary']:'';
    $description = isset($_POST['description'])?$_POST['description']:'';
    $severity           = isset($_POST['cmbseverity'])?$_POST['cmbseverity']:'';
    $priority           = isset($_POST['cmbpriority'])?$_POST['cmbpriority']:'';
    $customer_call_type = isset($_POST['cmbcallmark'])?$_POST['cmbcallmark']:'';
    $assign_to          = isset($_POST['assign_to'])?$_POST['assign_to']:'';
    $complaint_type     = isset($_POST['complaint_type'])?$_POST['complaint_type']:'167';
    $complaint_status   = isset($_POST['complaint_status'])?$_POST['complaint_status']:'T17';
    $summary            = mysqli_real_escape_string_wrapper($summary);
    $description        = mysqli_real_escape_string_wrapper($description);    
    $uploadDir = "$attach_path/Support_Upload_Files/".date("Y");
	$attachment_file_tosend=upload_files_to($uploadDir);	
	$emp_lead_code=0;
	if($uid>7000){
	    $partner_dtl = get_partner_emp_id($uid);
	    $emp_lead_code = isset($partner_dtl['partner_lead_code'])?(int)$partner_dtl['partner_lead_code']:0;
	}else{
	    $emp_lead_code = (int)get_single_value_from_single_table("GEM_LEAD_CODE", "gft_emp_master", "GEM_EMP_ID", $uid);
	}	
	$emp_lead_code = ($emp_lead_code==0?"232794":"$emp_lead_code");	
	$uploaded_file_path = implode(",", $attachment_file_tosend);
	$product_dtl = get_latest_product_version(array("1-04.0"));
	$version = isset($product_dtl[0][2])?$product_dtl[0][2]:"4.0.2.33";
	$ticket_id = insert_support_entry($emp_lead_code, '1', '04.0', "$version", '', $uid, '', $summary, "$complaint_type","$complaint_status    ",null,null,
	    $assign_to,'25',"$severity",$description,false,'',null,'',"$priority",true,$uploaded_file_path,"$customer_call_type");
	send_support_ticket_notification_to_chat('samteam',$emp_lead_code,$ticket_id,$summary,$assign_to);
	show_my_alert_msg("Complaint registered Successfully.\"+'\\n'+\"Customer Id : $emp_lead_code\"+'\\n'+\"Support Id :$ticket_id ");
 	close_the_popup();
}//End of if
$red_asterik = '<font color="red" size="3" >*</font>';
echo<<<END
<table width="100%">
<tr><td>
END;
$complaint_status = "";
$team_id = get_single_value_from_single_table("WEB_GROUP", "gft_emp_master", "GEM_EMP_ID", $uid);
if($team_id=='12'){
    $complaint_status =<<<END
    <tr><td class="head_blue" width="100">$red_asterik Complaint Status </td><td>
        <select name='complaint_status' id='complaint_status' class='formStyleTextarea' tabindex="4">
        <option value='0'>Select Status</option>
        <option value='T17'>Pending dev support</option>
        <option value='T2'>Pending Developer</option>    
    </select>
    </td></tr>
END;
}
$string_upload_temp=upload_template();
print_dtable_header("Report an issue ");
echo<<<END
<script type="text/javascript" src="js/js_upload.js"></script>
<form  name='report_an_issue' action="report_an_issue.php" method="post" enctype="multipart/form-data" onreset="return check();" >
<input type="hidden" name="report_an_issue" value="true"> 
<table class="FormBorder1" width="100%">
<tr><td class="head_blue" width="100">$red_asterik Complaint Type </td><td>
END;
$complaint_type_list = get_two_dimensinal_array_from_query("select GFT_COMPLAINT_CODE,GFT_COMPLAINT_DESC  from gft_complaint_master where GFT_COMPLAINT_GROUP=22 AND GFT_STATUS='A'", "GFT_COMPLAINT_CODE", "GFT_COMPLAINT_DESC");
echo fix_combobox_with('complaint_type','complaint_type',$complaint_type_list,'0',1,'-Select-',"Style='width:120px'",false);
echo<<<END
<tr><td class="head_blue" width="25%">$red_asterik Summary</td>
<td><input width="75%" type=text name="summary" class="formStyleTextarea" size=62 tabindex="2"></td></tr>
<tr><td class="head_blue" width="100" valign="top">$red_asterik Description</td>
<td><textarea  cols=60 rows=10 name=description class="formStyleTextarea" tabindex="3"></textarea></td></tr>
$complaint_status
<tr><td class="head_blue" width="100">$red_asterik Severity </td><td>
END;
$severity_list = get_two_dimensinal_array_from_table('gft_severity_master','GSM_CODE','GSM_NAME','GSM_STATUS','A');
echo fix_combobox_with('cmbseverity','cmbseverity',$severity_list,0,4,'-Select-',"Style='width:120px'",false);
echo<<<END
</td></tr>
<tr><td class="head_blue" width="100">$red_asterik Priority </td><td>
END;
$priority_list = get_two_dimensinal_array_from_table('gft_priority_master','GPM_CODE','GPM_NAME','GPM_STATUS','A');
echo fix_combobox_with('cmbpriority','cmbpriority',$priority_list,0,5,'-Select-',"Style='width:120px'",false);
echo<<<END
</td></tr>
<tr><td class="head_blue" width="100">$red_asterik Customer Call Type </td><td>
END;
$call_mark_list = get_two_dimensinal_array_from_table('gft_cust_call_master','GCC_ID','GCC_DESC','GCC_STATUS','A');
echo fix_combobox_with('cmbcallmark','cmbcallmark',$call_mark_list,'0',6,'-Select-',"Style='width:120px'",false);
echo<<<END
</td></tr>

<tr><td class="head_blue" width="100">$red_asterik Assign To </td><td>
END;
$assign_to_list = get_two_dimensinal_array_from_query("select GEM_EMP_ID, GEM_EMP_NAME  from gft_emp_master where WEB_GROUP=12 AND GEM_STATUS='A'", "GEM_EMP_ID", "GEM_EMP_NAME");
echo fix_combobox_with('assign_to','assign_to',$assign_to_list,'0',7,'-Select-',"Style='width:120px'",false);
echo<<<END
</td></tr>
<tr><td class="head_blue" valign="top">Attachment</td>
<td>$string_upload_temp</td></tr>
<tr><td colspan="2" align="center">
<input type=button class="button" value="Save" onclick="javascript:my_evaluate();" tabindex="8">&nbsp;&nbsp;
<input type=reset class="button" value="Reset" tabindex="9"></td></tr></table>
</form>
</td></tr></table></td></tr>
<tr><td><font color="red">Please send screenshots to understand the issues better.</font></td></tr></table>
<script type="text/javascript">
function check(){
	var yourstate=window.confirm("Are you sure you want to reset the values?");
	if (yourstate)	{ 
		return true;
	}else{
		return false;
	}
}
function my_evaluate(){
    if(document.report_an_issue.complaint_type.value=="0"){
		alert("Please select Complaint Type ");
		return false;
	}
	if(document.report_an_issue.summary.value==""){
    	alert("Please enter Summary ");
     	return false;
    }
    if(document.report_an_issue.description.value==""){
    	alert("Please enter Description ");
     	return false;
    }
    if($('complaint_status') && $('complaint_status').value==0){
        alert("Please select complaint status ");
     	return false;
    }
	if(document.report_an_issue.cmbseverity.value=="0"){
		alert("Please select Severity ");
		return false;
	}
    if(document.report_an_issue.cmbpriority.value=="0"){
		alert("Please select the Priority ");
		return false;
	}
    if(document.report_an_issue.cmbcallmark.value=="0"){
		alert("Please select the Customer Call Type ");
		return false;
	}
    if(document.report_an_issue.assign_to.value=="0"){
		alert("Please select the assign to ");
		return false;
	}
	obj=document.forms[0];
    obj.submit();
}
</script>
END;
?>
<script type="text/javascript">
window.close();
</script>