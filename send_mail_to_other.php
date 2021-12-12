<?php
require_once(__DIR__ ."/inc.essentials_for_popup.php");
require_once(__DIR__ ."/common_filter.php");
$uid=$_SESSION['uid'];
$email_from=trim(get_email_addr($uid));
//echo "Email from".$email_from;
echo<<<END
<script type="text/javascript" src="js/js_send_mail_to_other.js"></script>
END;
if(isset($_POST['report_an_issue'])){
	$email_from=$_POST['email_from'];
	$email_to=$_POST['email_to'];
	$subject=$_POST['subject']?(string)$_POST['subject']:'Mail';
	$msg=$_POST['message'];
	$get_have_to_close=$_POST['have_to_close'];
	$name=$email_from;
	$mail_status=0;
	$cc=$_POST['cc'];
	$category=$_POST['category'];
	if($category==''){$category="Send Mail";}
    $rs=send_mail_function($email_from,$email_to,$subject,$msg,'',$cc,$category);
	if($get_have_to_close){
		print "<script>self.close();</script>";
	}
}//End of if
$have_to_close='';
$cc_readonly='';
$Subject=isset($_GET['subj'])?(string)$_GET['subj']:"";
if($Subject){
	$Subject=" value=\"$Subject\" readonly";
	$have_to_close=1;
}
$cc="";
$category=isset($_GET['category'])?(string)$_GET['category']:"";
$emp_id=isset($_GET['empid'])?(string)$_GET['empid']:"";
$email_to_id=isset($_GET['mailto1'])?(string)$_GET['mailto1']:"";
$email_to=get_email_addr($email_to_id);
$attention_to_rep=get_email_addr_reportingmaster($emp_id,$reporting_masters=true);
if(count($attention_to_rep)>0){
	$ca_to=implode(',',$attention_to_rep); 
	$cc=$ca_to;
	$cc_readonly=" readonly ";
}
echo<<<END
<script>
function check(){
	var yourstate=window.confirm("Are you sure you want to reset the values?");
	if (yourstate){
		//Boolean variable. Sets to true if user pressed "OK" versus "Cancel."
		return true;
	}else{
		return false;
	}
}
function my_evaluate(){
	if(document.report_an_issue.email_from.value==""){
		alert("Please enter the From email address ");
		return false;
    }else{
		id=isEmail(document.report_an_issue.email_from.value);
		if(!id){
			document.report_an_issue.email_from.focus();
			return false;	
		}		
    }
    if(document.report_an_issue.subject.value==""){
    	 alert("Please enter the subject ");
     	 return false;
    }
    if(document.report_an_issue.message.value==""){
    	 alert("Please enter the message ");
     	 return false;
    }
	obj=document.forms[0];
	obj.submit();
	return true;
}
</script>
<table><tr><td>
END;
print_dtable_header("Send Mail");
echo<<<END
<form  name='report_an_issue' action="send_mail_to_other.php" method="post" onreset="return check();" >
<input type="hidden" name="report_an_issue" value="true"> 
<input type="hidden" name="category" value="$category">
<input type="hidden" name="have_to_close" value="$have_to_close">
<table class="FormBorder1" width="400px">
<tr><td class="head_blue" width="100px">From</td><td><input type="text" name="email_from" value="$email_from" size="75"  class="formStyleTextarea" readonly></td></tr>    
<tr><td class="head_blue" width="100px">To</td><td>
END;
get_employee_email($tab_index=2,$email_to);
echo<<<END
<tr><td class="head_blue" width="100px">CC</td><td><input type=text id="cc" name="cc" value="$cc" size="75"  class="formStyleTextarea" $cc_readonly></tr> 
<tr><td class="head_blue" width="100px">Subject
<td> <input type=text name="subject" class="formStyleTextarea" size=75  $Subject tabindex="2"> </tr>
<tr><td class="head_blue" width="100px" valign="top">Message</td>
<td><textarea  cols=70 rows=10 name=message class="formStyleTextarea" tabindex="3"></textarea>
</td></tr>
<tr height=5></tr>
<tr><td colspan=2 align='center'>
<input type="button" class="button" value="Send" onclick="javascript:my_evaluate();" tabindex="4">&nbsp;&nbsp;
<input type="reset" class="button" value="Reset" tabindex="5">
</table></td></tr></table>
END;
?>
<script type="text/javascript">
window.close();
</script>