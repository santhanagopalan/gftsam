<?php
require_once(__DIR__ ."/menu.php");
require_once(__DIR__ ."/common_filter.php");

/**
 * @param int $emply_id
 * 
 * @return boolean
 */
function show_techsupport_mobile_forward($emply_id) {
	$que = "select GMA_AGENT_ID from gft_msn_agent_master where GMA_AGENT_ID='$emply_id' and GMA_FORWARD_TO_MOBILE='Y' ";
	$res = execute_my_query($que);
	if(mysqli_num_rows($res) > 0){
		return true;
	}
	return false;
}

//Added dummy function to avoid javascript error
echo <<<END
<script type="text/javascript">
function empDetails(){}
</script>
<style>
.profile-imageg{
    width:100px;
    height:100px;
    border-radius:50%;
    overflow:hidden;
}
</style>
END;
$name='';
$id='';
$hr_id = '';
if(isset($_POST['emp_code']) and (int)$_POST['emp_code']!=0){
	$emp_code=$_POST['emp_code'];
}else{
	$id=$emp_code=$uid;
	$name=get_emp_name($uid);
}

print "<center><h2><div id='res_display'></div></h2></center>";
echo '<table align="center" ><tr><td>';
print_dtable_header("Personal Details");
echo<<<END
<form name="exec_profile" id="exec_profile" method="post" onsubmit="return false;">
<input type="hidden" name="submit_values" value="true">
<input type="hidden" name="emp_code" value="$emp_code">
<input type="hidden" name="current_emp_code" value="$emp_code">		
<table width="100%" border="0" class="Formborder1" bgcolor="#b6e5ff" >
<tr><td width="50%">
<table width="100%" border="0" class="Formborder1" bgcolor="#b6e5ff" >
<tr><td class="datalabel">NAME:</td><td id="td_name">

END;
if(is_authorized_group_list($uid,array(1,65))){
	get_employee_list($tab_index=1,$_SESSION["uname"],$emp_code,$blockNoResult='',$emptyIfNew='',
$show_others='',$sel_group_arr='',$sel_role_arr='',$all_emp="off");
}else{
echo $name;
}
echo<<<END
</td></tr>
END;
if(!is_authorized_group_list($uid,$non_employee_group)){
	$emp_arr = get_emp_master($id,'A');
	$lead_code = $emp_arr[0][14];
	$hr_id     = $emp_arr[0][24];
echo<<<END
<tr><td class="datalabel">Employee ID:</td><td id="hr_id">$hr_id</td></tr>
<tr><td class="datalabel">SAM Reference ID:</td><td id="td_id">$id</td></tr>
END;
	if($lead_code!=0) {
echo<<<END
<tr><td class="datalabel">Emp Lead Code:</td><td id="td_lead_code">$lead_code<input type="hidden" id="gem_lead_code" name="gem_lead_code" value="$lead_code"></td></tr>
END;
	}else{
echo<<<END
<tr><td class="datalabel">Emp Lead Code:</td>
<td id="td_lead_code"><input type="text" id="gem_lead_code" name="gem_lead_code" value="$lead_code" onkeyup="javascript:extractNumber(this,2,false);"></td></tr>
END;
	}

}else{
echo<<<END
<tr><td class="datalabel">SAM Reference ID:</td><td id="td_id">$id</td></tr>
END;
}
echo<<<END
<tr><td class="datalabel">Mobile No 1:</td>
<td><input type="text" name="mobileno" id="mobileno" value="" class="formStyleTextarea" onblur="javascript:extractMobileNumber(this)" onkeypress="javascript:return forMobileNumbers(this,event,true)"> * for SMS</td></tr>
<tr><td class="datalabel">Mobile No 2:</td>
<td><input type="text" name="rmobileno" id="rmobileno" value="" class="formStyleTextarea" onblur="javascript:extractMobileNumber(this)" onkeypress="javascript:return forMobileNumbers(this,event,true)"></td></tr>
<tr><td class="datalabel" nowrap>Res. Contact No:</td>
<td><input type="text" name="residence_no" id="residence_no" value="" class="formStyleTextarea"></td></tr>
END;
if(!is_authorized_group_list($uid,$non_employee_group)){
echo<<<END
<tr><td class="datalabel">Direct Reliance No: <br><font color=red>Enter like 04439200---</font></td>
<td><input type="text" name="direct_no" id="direct_no" value="" class="formStyleTextarea" onblur="javascript:extractMobileNumber(this)" onkeypress="javascript:return forMobileNumbers(this,event,true)"></td></tr>
<tr><td class="datalabel">Intercom No (Local):</td>
<td><input type="text" name="che_inter_com" id="che_inter_com" value="" class="formStyleTextarea" onblur="javascript:extractMobileNumber(this)" onkeypress="javascript:return forMobileNumbers(this,event,true)"></td></tr>
END;
}
$show = show_techsupport_mobile_forward($uid);
if($show){
	$yes_no_arr = array(array('Y','Yes'),array('N','No'));
	$mobile_fwd = fix_combobox_with('mobile_fwd', 'mobile_fwd', $yes_no_arr, 'N');
	echo '<tr><td class="datalabel">Techsupport Mobile Forward:</td><td>'.$mobile_fwd.'</td></tr>';
}
$display_mac_address="";
if($uid>7000){
	$display_mac_address="display:none;";
}
$gender_combo = fix_combobox_with("gender","gender",array(array('0','---Select---'),array('1','Male'),array('2','Female')),'0');
$blood_group_combo = get_blood_gp_combo();

$material_using = "";
$mq1 = " SELECT k.GST_MTYPE_NAME,ifnull(ifnull(GSD_RECEIVING_STATUS,d.GSA_ALLOCATED_STATUS), g.GST_REQUEST_STATUS) r_status,GSM_SERIAL_NO ".
       " from gft_sys_booking_request g JOIN gft_sys_material_type_master k on(g.GSR_MTYPE_ID=k.GST_MTYPE_ID) ".
       " left join gft_sys_booking_allocation d on(g.GSR_REQUEST_ID=d.GSA_REQUEST_ID) ".
       " left join gft_sys_material_master on(GSM_M_ID=GSA_M_ID) ".
       " left join gft_sys_receipt_dtl on(GSA_ALLOCATION_ID=GSD_ALLOCATION_ID) ".
       " where g.GST_REQUEST_BY='$uid' group by GSR_REQUEST_ID having r_status='Allocated' ORDER BY g.GSR_REQUEST_DT ";
$mr1 = execute_my_query($mq1);
$msl = 1;
while($md1 = mysqli_fetch_assoc($mr1)){
    $material_using .= "<br>".$msl++.". ".$md1['GST_MTYPE_NAME']." (Serial No. ".$md1['GSM_SERIAL_NO'].") <br> ";
}
if($material_using=="") $material_using = "none";

echo<<<END
<tr><td class="datalabel">Date of Birth:</td><td>
<input type="text" name="dob"  id="dob" value="" class="formStyleTextarea" readonly>
<a href="date.php" name="dob_cal" id="dob_cal" onclick="return false;">
<img alt="" src="images/date_time.gif" align="middle" border="0" width="16"></a></td></tr>
<tr><td class="datalabel">Gender:</td>
<td>$gender_combo</td></tr>
<tr><td  class="datalabel">Permenant Address:</td><td>
<textarea name="paddress" id="paddress" cols="30" rows="5" class="formStyleTextarea"></textarea>
</td></tr><tr><td></td><td>
<input type="checkbox" name="copy_contact" id="copy_contact" onclick="javascript:CopyContactInfo(this);">
if Permenant & Current Address are same </td></tr>
<tr><td  class="datalabel">Current Address:</td><td>
<textarea name="caddress" id="caddress" cols="30" rows="5" class="formStyleTextarea"></textarea>
<input type="hidden" name="old_caddress" id="old_caddress" value="">
</td></tr><tr><td></td><td></td></tr>
</table></td>
<td valign='top'><table width="100%" border="0" class="Formborder1" bgcolor="#b6e5ff" >
<!--<tr><td class="datalabel">Emergency Contact No.</td>
<td><input type="text" name="emergency_contact" id="emergency_contact" value="" class="formStyleTextarea"></td></tr>
<tr><td class="datalabel">Personal Email ID</td>
<td><input type="text" name="personal_email" id="personal_email" value="" class="formStyleTextarea"></td></tr>
<tr><td class="datalabel">Blood Group</td>
<td>$blood_group_combo</td></tr>
<tr><td colspan=2><hr></td></tr>
</tr>-->
<tr>
	<td class="datalabel">Profile Image:</td>
	<td align="center"><img  src='' width='100px' height="100px" id='profile_image' class="profile-imageg"/>
	<br><a href='javascript:call_popup("update_profile_image.php",8);'>[Edit]</a>
	</td>
</tr>
<tr>
	<td class="datalabel">Courier Address:</td>
	<td><textarea name="courier_addr" id="courier_addr" cols="30" rows="5" class="formStyleTextarea"></textarea>
	<input type="hidden" name="old_courier_addr" id="old_courier_addr" value=""></td>
</tr>
<tr style="$display_mac_address">
	<td class="datalabel">Mobile MAC address:</td>
	<td><input type='text' name="mobile_mac_address" id="mobile_mac_address" value="" />
	<input type="hidden" name="old_mobile_mac_address" id="old_mobile_mac_address" value=""></td>
</tr>
<tr style="$display_mac_address">
	<td class="datalabel">Laptop Hard Disk ID:</td>
	<td><input type='text' name="laptop_harddisk_id" id="laptop_harddisk_id" value="" />
	<input type="hidden" name="old_laptop_harddisk_id" id="old_laptop_harddisk_id" value=""></td>
</tr>
<tr style="$display_mac_address">
	<td class="datalabel">Laptop MAC ID:</td>
	<td><input type='text' name="laptop_mac_id" id="laptop_mac_id" value="" />
	<input type="hidden" name="old_laptop_mac_id" id="old_laptop_mac_id" value=""></td>
</tr>
<tr style="$display_mac_address">
	<td class="datalabel">Help link to get MAC ID:</td>
	<td><a href="https://help-sam.gofrugal.com/Android-device-mac-address.html" target="_blank">Android</a>&nbsp;&nbsp;
		<a href="https://help-sam.gofrugal.com/iOS-device-mac-address.html" target="_blank">iOS</a>&nbsp;&nbsp;
		<a href="https://help-sam.gofrugal.com/Laptop-mac-address.html" target="_blank">Laptop</a>&nbsp;&nbsp;</td>
</tr>
<tr style="$display_mac_address">
	<td class="datalabel">Help link to get HardDisk ID:</td>
	<td><a href="https://help-sam.gofrugal.com/License-activation-for-Internal-testing.html" target="_blank">HardDisk ID</a>&nbsp;&nbsp;</td>
</tr>		
<tr style="$display_mac_address">
	<td class="datalabel">IT Material Request:</td>
	<td><a href="sys_admin_booking_request.php" target="_blank">Click here</a>&nbsp;&nbsp;</td>
</tr>
<tr style="$display_mac_address">
	<td class="datalabel" style="vertical-align:middle;">GFT Material Using</td>
	<td><div style="height:70px; overflow:auto;">$material_using</div></td>
</tr>
</table>
<br><br>
<div class='password-change' onclick="window.open('personalize.php','_blank')">Click <span class='here-class'>here</span> to Change Password</div>
</td></tr>
 <tr><td colspan="2" align="center">
<INPUT type="button" class="button" name="Save" id="save" value="Save" onclick="">
<INPUT type="reset" class="button" name="reset1"></td>
</tr></table></form>
</table></table></table>
<script type="text/javascript">
init_date_func('dob',"%Y-%m-%d","dob_cal","Bl");
function saveDetails(){
	$('exec_profile').onsubmit="";
	$('exec_profile').submit();
}
function CopyContactInfo(obj){
	if(obj.checked==true){
		$("caddress").value=$("paddress").value;
	}else{
		$("caddress").value=$("old_caddress").value;
	}
}
</script>
END;
require_once(__DIR__ ."/footer.php");

?>

<script type="text/javascript">
var jq=jQuery.noConflict();
jq(document).ready(function(){ 
				var jsonres = jq.ajax({
						url:"service/personaldetails.php",
						type:"get",
						datatype:"json",
						async:false,
						error:function(){alert("ajax error");}
						}).responseText;
		var obj = jq.parseJSON(jsonres);
		jq("#dob").val(obj.dob);
		jq("#mobileno").val(obj.mobile_no);
		jq("#rmobileno").val(obj.secondmobile_no);
		jq("#residence_no").val(obj.residence_no);
		jq("#che_inter_com").val(obj.inter_com);
		jq("#direct_no").val(obj.direct_no);
		jq("#paddress").val(obj.paddress);
		jq("#caddress").val(obj.caddress);
		jq("#courier_addr").val(obj.courier_address);
		jq("#mobile_mac_address").val(obj.mobile_mac_address);
		jq("#old_mobile_mac_address").val(obj.mobile_mac_address);
		jq("#laptop_harddisk_id").val(obj.laptop_harddisk_id);
		jq("#old_laptop_harddisk_id").val(obj.laptop_harddisk_id);
		jq("#laptop_mac_id").val(obj.laptop_mac_id);
		jq("#old_laptop_mac_id").val(obj.laptop_mac_id);
		jq("#mobile_fwd").val(obj.mobile_fwd);
		jq("#emp_name").attr("readonly",true);	
		jq("#profile_image").attr("src", obj.profile_url);
		jq('#gender').val(obj.gender);
// 		jq('#emergency_contact').val(obj.emergency_contact);
// 		jq('#personal_email').val(obj.personal_email);
// 		jq('#blood_gp').val(obj.blood_gp);
});
jq("#save").click(function(){
	if($("mobile_mac_address").value.length!="" && ($("mobile_mac_address").value.length<12 || $("mobile_mac_address").value.length>17)){
		alert("Your Mobile Mac Address should be 12 or 17 characters");
		return false;
	}
	if($("laptop_mac_id").value.length!="" && ($("laptop_mac_id").value.length<12 || $("laptop_mac_id").value.length>17)){
		alert("Your Laptop MAC ID should be 12 or 17 characters");
			return false;
	}
	var mobmac = jQuery("#mobile_mac_address").val();
	if ( (mobmac!="") && !mobmac.match(/^([a-fA-F0-9]{2}[:-]?){5}[a-fA-F0-9]{2}$/)) {
		   alert("Please enter the correct Mobile Mac Address");
		   return false;
	}
	var lapmac = jQuery("#laptop_mac_id").val();
	if ( (lapmac!="") && !lapmac.match(/^([a-fA-F0-9]{2}[:-]?){5}[a-fA-F0-9]{2}$/)) {
		alert("Please enter the correct Laptop Mac Address"); 
	       return false;
	}
	if($("mobileno").value==''){
		alert("Mobile Number 1 should not be empty.please enter the Mobile Number .");
		return false;
	}
	if($('gender').value=='0') {
		alert("Please select your gender.");
		return false;
	}
// 	if($('personal_email').value!='') {
// 		if(isEmail($('personal_email').value)==false) {
// 			alert('Check Entered Personal Email id is valid or invalid');
// 			return false;
// 		}
// 	}
// 	if($('emergency_contact').value!='') {
// 		if($('emergency_contact').value.length<10) {
// 			alert('Invalid emergency contact number. Emergency contact must have 10 digits.');
// 			return false;
// 		}
// 	}
	if($("laptop_harddisk_id").value!="" && $("old_laptop_harddisk_id").value==""){
		if(!confirm("Are you sure entered hard disk is correct?")){
			return false;
		}
	}
	if((($("mobileno").value.charAt(0)=='9' || $("mobileno").value.charAt(0)=='8'  || $("mobileno").value.charAt(0)=='7' || $("mobileno").value.charAt(0)=='6') && $("mobileno").value.length > 9) 
	|| ($("mobileno").value.charAt(0)=='0' && ($("mobileno").value.charAt(1)=='9' || $("mobileno").value.charAt(1)=='8' || $("mobileno").value.charAt(1)=='7' || $("mobileno").value.charAt(1)=='6') && $("mobileno").value.length > 9)){
		var formdata = jq("#exec_profile").serialize();
		jq.ajax({
			url:"service/savepersonaldetails.php",
			type:"post",
			data:formdata,
			datatype:"json",
			async:false,
			success:function(res){
				//var object = jq.parseJSON(res);
				alert(res.message);
				jq("#gem_lead_code").attr("readonly","readonly");
				location.reload();
			},
			error:function(){alert("ajax error");}
		});
	}else{
		alert("Enter a valid Mobile No");
		return false;
	}
});	
</script>
