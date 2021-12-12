<?php

/*. forward void function customer_type(int $tab_index=, string[int] $lead_type_list=); .*/
/*. forward void function show_reference_details(string $lead_source_p,string $lead_source_p_id,string $lead_source_e,string $lead_source_e_id,string $lead_source_c,string $lead_source_c_id,boolean $show1=,boolean $show2=,boolean $show3=,boolean $check_partner_src=); .*/

require_once(__DIR__ ."/access_util.php");
require_once(__DIR__ ."/common_filter.php");
require_once(__DIR__."/competitor_util.php");

/**
 * @param int $tab_index
 * @param int $width_label
 * @param int $width_field
 * @param boolean $remove_doc
 * @param boolean $columnwise
 * 
 * @return void
 */
function customer_other_details($tab_index=0,$width_label=170,$width_field=250,$remove_doc=false,$columnwise=false){
	global $shop_info;
	if($columnwise==true){ $add_row_inbtn="<tr>";}
	if($remove_doc==false){
echo<<<END
<tr><TD class="datalabel">Shop Info</TD>
<TD><input type="hidden" id="old_shopinfo" name="old_shopinfo" value="$shop_info">
<textarea  id="shopinfo" tabindex="$tab_index" name="shopinfo"  rows="3" cols="50" class="formStyleTextarea"  onKeypress="return block_sp_char_except2sp(this,event);" >$shop_info</textarea></TD>
END;
	$tab_index++;
	}
	if($remove_doc==false){
echo<<<END
<tr><TD class="datalabel" width="170">Computerised Status</TD>
<TD><select size="1" id="computerized_status"  class="formStyleTextarea" name="computerized_status" tabindex="$tab_index" onchange="javascript:show_computerization();" onKeyUp="javascript:show_computerization();">
<option value='No'>No</option><option value='Yes'>Yes</option></select>
END;
	}
echo<<<END
</TD></TR></table>
END;
}

/**
 * @param int $tab_index
 * @param string $type_code
 * @param string $vertical
 * @param int $width_label
 * @param int $width_field
 * @param boolean $show_vertical
 * @param boolean $show_business
 * 
 * @return void
 */
function business_details(&$tab_index=0,$type_code=null,$vertical=null,$width_label=170,$width_field=250,$show_vertical=true,$show_business=true){
	$type_of_customer=get_type_list();
	
if($show_vertical){
	$vertical_type_list=get_vertical_name(null,false);
echo<<<END
<table border=0 width="100%">
	<tr>
		<TD id="not_need_for_cp1_1" class="datalabel" width="$width_label"><font color="red" size="3" >*</font>Vertical</TD>
		<TD id="not_need_for_cp1_2" width="$width_field">
			<input  type="hidden" name="old_vertical_type" id="old_vertical_type">
END;
	echo fix_combobox_with('vertical_type','vertical_type',$vertical_type_list,$vertical,$tab_index,'Select');
	$tab_index++;
echo<<<END
		</TD>
	</tr>
</table>	
END;
}
if($show_business){
echo<<<END
<table border=1 width="100%">
	<TR>
		<TD class="datalabel" width="$width_label"><font color="red" size="3">*</font>Business</TD>
		<TD><input  type="hidden" name="old_type_of_cust" id="old_type_of_cust">
END;
			echo fix_combobox_with('type_of_cust','type_of_cust',$type_of_customer,$type_code,$tab_index++,'Select',null, false,"onchange='javascript: show_cp_details();'");
			$tab_index++;
echo<<<END
		</TD>		
	</tr>
	<TR>
		<TD class="datalabel" >In Detail </td>
		<TD colspan="3">
			<input type="hidden" id="ex_spl_remarks" name="ex_spl_remarks" value="">
			<input type="text"  id="spl_remarks" tabindex="$tab_index++" name="spl_remarks" style="HEIGHT: 20px; WIDTH: 400px;" 
			class="formStyleTextarea"  onKeypress="return block_sp_char_except2sp(this,event);" value="" maxlength="50"></TD>
	</tr>
</table>
END;
}

}

//channel partner/ corporate customer detail
/**
 * @param int $tab_index
 * @param int $width_label
 * @param int $width_field
 * 
 * @return void
 */
function cp_cc_business_detail($tab_index=0,$width_label=170,$width_field=250){
	$cp_business_type=get_two_dimensinal_array_from_table('gft_channelparnter_business_type','GCP_BUSINESS_TYPE_CODE','GCP_BUSINESS_TYPE_NAME');
	$tab_index++;
	$business_type_options="";
	for($i=0;$i<count($cp_business_type);$i++){
		$business_id=$cp_business_type[$i][0];
		$business_name=$cp_business_type[$i][1];
		$business_type_options.= "<option value=$business_id> $business_name</option>";
	}
echo<<<END
<div id="cp_detail_business"  class="hide">
<fieldset>
<legend align="center"><font size=2 color="red"> Channel Partner / Corporate Business Details</font></legend>
<table cellspacing="1" cellpadding="0">
<tr><td class="datalabel" width="$width_label">Business Nature</TD>
<td width="$width_field"><input type="hidden" name="old_nature_cp_business" id="old_nature_cp_business">
<select name="nature_cp_business" id="nature_cp_business" class="formStyleTextarea" tabindex="$tab_index">
<option value="0">Select</option>$business_type_options</select>
<td class="datalabel" width="$width_label">No. of Staff(s)</TD>	
<td><input  type="hidden"  id="old_no_staff" name="old_no_staff">
<input  type="text"  id="no_staff" name="no_staff" class="formStyleTextarea" type="text" 
tabindex="$tab_index" onkeyup="javascript:extractNumber(this,0,false);" 
onkeypress="javascript:return blockNonNumbers(this, event, false, false);" ></TD>					
</tr></table></fieldset></div>
END;
}


/**
 * @param string[int][int] $employee_list
 * @param int $tab_index
 * 
 * @return void 
 */
function show_joint_visit_dtl($employee_list,$tab_index){
/*FYI using in visit_details.php 'Followup Activity Entry'
 * support_details.php 'Support Entry'
 */
	
echo<<<END
	<tr id="tr_visit_type">
		<td valign="top"  class="datalabel"><font color="red" size="3" >*</font>Activity Type</td>
		<td><select id="visited_type" name="visited_type" onchange="showjoint_visit_dtl();" tabindex="$tab_index" class="formStyleTextarea">
            <option value='-1'>---Select---</option>
			<option value='0'>Individual</option>
			<option value="1">Joint</option></select></td></tr>
	<tr><td></td>
		<td><div id="joint_visit_dtl" class="hide">
		<table class="solid_border">
			<tr><td width="70%">
				<span id="tab_joint_visit">
				<table id="joint_visit">
					<tbody><tr><td> 1.</td><td>
END;
echo fix_combobox_with('joint_emp0','joint_emp[0]',$employee_list,'',$tab_index,'Select',null,false,"onchange='set_values_for_agile();'",1,null,'joint_emps');
$tab_index++;
echo<<<END
						</td></tr>
					</tbody>
            <tr>
			</table>
			<table>
    			<tr><td><input name="button" onclick="addRow_joint_visit();" value="Add " type="button" class="button" tabindex="$tab_index"> 
    				<td><input value="Remove" onclick="removeRowFromTable_joint_visit();" type="button" class="button" tabindex="$tab_index">
    			</tr>
			</table>
            <table style='width:100%;display:none;' id='incentive_contrib'>
                <tr><td class="datalabel" style='width:30%;cell-padding:1px;'>Agile Contributor</td>
                <td><select name='agile_contributor' id='agile_contributor' class="formStyleTextarea">
                <option value='0'>---Select---</option>
                </select></td></tr>
            </table>
			</span>	
			</td>
		</tr>
		</table>
		
		</div>
	</tr>

<script type='text/javascript'>
jQuery.noConflict();
jQuery('document').ready(function(){
	jQuery('#visited_type').change(function(){
				var user_id=jQuery('#user_id').val();															
				if((jQuery('#visited_type').val()=='1') && (user_id>=7000)){
					var cust_Code	=	jQuery('#custCode').val();
					jQuery.ajax({
		    		type: 'GET',
		    		url: "service/process_the_request.php",
		    		data:'purpose=joint_visit_partner&custCode='+cust_Code+'',
		    		async: false,
		    		dataType: 'text',
		    		success: function(text){
					jQuery("#joint_emp0").find('option').remove().end().append(text);
					}
			});
		}
        show_hide_agile_contributor();
	});
 });
</script> 	
END;
}

/**
 * @param string $uid
 * @param string $location
 * @param int $tab_index
 * @param string $cust_name
 * @param string $door_no
 * @param string $building_name
 * @param string $street_no
 * @param string $landmark
 * @param string $street_name
 * @param string $area_name
 * @param string $city_name
 * @param string $state_name
 * @param string $state_code
 * @param string $pincode
 * @param string $shopname
 * @param boolean $show_order_dtl
 * @param boolean $show_search_opt
 * @param boolean $show_history
 * @param boolean $show_edit
 * @param boolean $stop_autocomplete
 * @param string $customer_id
 * @param boolean $cust_readonly
 * @param boolean $quotation_dtl_opt
 * @param boolean $edit_customer_name
 * @param string $country_name
 * @param string $country_code
 * @param boolean $show_support_history_link
 * @param string $chb_installedchecked
 * @param boolean $hide_corporate_name_filter
 * @param boolean $hide_install_checkbox
 * @param boolean $hide_response_group
 * @param boolean $hide_addressfileds
 * @param boolean $hide_monitor
 * @param boolean $show_dummy_search
 * @param boolean $show_gft_lead_info
 *
 * @return void
 */
function customer_details($uid,$location='',&$tab_index=1,
$cust_name=null,$door_no=null,$building_name=null,$street_no=null,
$landmark=null,$street_name=null,$area_name=null,$city_name=null,
$state_name=null,$state_code=null,$pincode=null,$shopname="Lead Name(Shop Name)",
$show_order_dtl=false,$show_search_opt=false,$show_history=true,$show_edit=true,
$stop_autocomplete=false,$customer_id=null,$cust_readonly=false,$quotation_dtl_opt=false,
$edit_customer_name=false,$country_name=null,$country_code=null,$show_support_history_link=false,
$chb_installedchecked=null,$hide_corporate_name_filter=false,$hide_install_checkbox=false,
$hide_response_group=false,$hide_addressfileds=false,$hide_monitor=false,$show_dummy_search=false,$show_gft_lead_info=true){
    
    $corp_code='';
	$response_group_selection ='';
	global $edit_page,$order_split_page, $auth_gr_edit_cust_name,$global_web_domain;
	$authorized=is_authorized_group_list($uid,$auth_gr_edit_cust_name);
	if(!$authorized and basename($_SERVER['PHP_SELF'])=="edit_cust_details.php"){
		$readonly_fld="readonly";
	}
	$support_entry=false;
	if(basename($_SERVER['PHP_SELF'])=="tele_support_activity.php"  or 
	  basename($_SERVER['PHP_SELF'])=="support_details.php" ){
		$support_entry=true;$hide_response_group=true;
		$skip_column_hidden_arr=skip_mandatory($id=array('1','3'));
	}else {
		$skip_column_hidden_arr=skip_mandatory($id=array('1'));
	}
	$skip_column_hidden=$skip_column_hidden_arr[0];
	$mark_fields=$skip_column_hidden_arr[1];
	//if(is_array($mark_fields)){
	//	extract($mark_fields);
	//}
$mark_md_Street_Name=$mark_fields['mark_md_Street_Name'];
$mark_md_Landmark=$mark_fields['mark_md_Landmark'];
$mark_md_Area_Name=$mark_fields['mark_md_Area_Name'];
$mark_md_Location=$mark_fields['mark_md_Location'];
$mark_md_Pincode=$mark_fields['mark_md_Pincode'];
$mark_md_City=$mark_fields['mark_md_City'];
$mark_md_State=$mark_fields['mark_md_State'];
$mark_md_Country=$mark_fields['mark_md_Country'];


	$presales_selected='';
	$field_sales_selected='';
	$width_spec="100px"; //40%
	if((is_authorized_group_list($uid,array(1,27)) and $hide_response_group==false and $order_split_page!='true')){
		if(is_authorized_group($uid,27)){	
			$presales_selected="selected"; 
		}
$response_group_selection=<<<END
<tr id="response_row" ><td class="datalabel">Response Group</td>
<td nowrap><select id="response_group" name="response_group" class="formStyleTextarea">
<option value="23" $field_sales_selected > Field Sales </option>
<option value="27" $presales_selected>Presales/Web </option>
<option value="40">Auto Followup From SAM </option>
</select>
</td></tr>
END;
	}
	if($show_dummy_search==true){
$response_group_selection=<<<END
	<input type='hidden' id='search_value' name='search_value' value=''>
END;
	}
echo<<<END
<table width="100%" border="0" id='shop_dtl_tbody'>
<input type="hidden" id="edit_customer_name" name="edit_customer_name" value="$edit_customer_name">
END;
	if(!is_authorized_group_list($uid,array(13,14,21)) and $edit_page!='true' and $order_split_page!='true'){
		$tab_index++;
		if($hide_corporate_name_filter==false){ $id_corp_name_style=''; }
		else{ $id_corp_name_style="style='display:none'"; }
echo<<<END
<tr id='corp_name_filter' $id_corp_name_style >
<TD class="datalabel"  width="$width_spec" nowrap>Corporate Name</TD>
<TD nowarp><input autocomplete="off" id="corporate_name" name="corporate_name" tabindex="$tab_index" 
class="normal_autocomplete" value="" onblur='setStyleClassName(this,"normal_autocomplete")' 
 onfocus='setStyleClassName(this,"focus_autocomplete")' type="text" size="43"/>
<input id="corporateCode" name="corporateCode" type="hidden" class="formStyleTextarea" value="$corp_code"></TD></tr>
END;
	}else{
echo<<<END
<input id="corporate_name" name="corporate_name" type="hidden">
<input id="corporateCode" name="corporateCode" type="hidden"  value="$corp_code">
END;
	}	
echo<<<END
<script type="text/javascript" src="js/GofrugalLayer.js"></script>
<script type="text/javascript">
function chat_message_followup(){
	var cust_code=$("custCode").value;
	if(cust_code==""){
		$("custCode").focus();
		alert("Please select the valid lead Name");
		
	}else{
		call_popup('chat_message.php?customer_id='+cust_code,6);
	}
}
</script>
END;
echo<<<END
$response_group_selection
<TR><TD class="datalabel" width="$width_spec"><font color="red" size="3" ></font>Customer ID</TD>
<td nowrap>
<input type="text" id="customer_id" name="customer_id"  value="{$customer_id}"  size="10" class="formStyleTextarea" readonly>
<input type="hidden" id="created_by_empid" name="created_by_empid">
END;
$formattextcntent=<<<END
onkeypress="javascript:return removeInvalidChar(this,event);" onblur="javascript:makeStdWords(this);"
END;
	if($show_history==true){
		$tab_index++;
echo<<<END
<a href="javascript:openPopup_custdetails_fromActivity('supporthistory.php');"  class="subtle">
<img alt="" src="images/history.jpeg" border="0" align="absmiddle" height="20" width="20" title="History" tabindex="$tab_index" ></a>
END;
	}
	if($show_edit==true){
$tab_index++;
echo<<<END
<a href="javascript:openPopup_custdetails_fromActivity('edit_cust_details.php');" class="subtle">
<img alt="" src="images/edit.gif" border="0" align="absmiddle" title="Customer Details"  tabindex="$tab_index"></a>
END;
	}
	if($show_support_history_link==true and !is_authorized_group_list($uid,array(13,14,21))){
		$tab_index++;
echo<<<END
<a href="javascript:Popup_history('supporthistory.php?call_from=popup&from_dt=01-11-2004');" class="subtle" title="Support History">
<img alt="SH" src="images/history.jpeg" class="imagecur" border="0" align="absmiddle" height="20" width="20" tabindex="$tab_index"></a>
END;
	}
	$tab_index++;
	$hide_install_check_style=(($hide_install_checkbox==true  or  $order_split_page=='true')?" style='display:none' ":'');
	if($order_split_page!='true'){
echo<<<END
<a id='save_link' onclick="javascript:save_contact_dtl();" class="subtle" title="Save Customer Details">
<img alt="Save Customer Details" src="images/opportunities.gif" class="imagecur" border="0" align="absmiddle" height="20" width="20"></a>
<a href="javascript:chat_message_followup();">
<img alt="" src="images/chat_icon.jpeg" border="0" align="absmiddle" height="25" width="25" title="Chat details" ></a>
<script type="text/javascript">
function feedback_call(){
	var cust_Code=$("custCode").value;
	if($("lead_type") && $("lead_type").value==2){
		var url="website/web_cp_info_collect.php?cust_id="+cust_Code+"&audited_by="+$uid;
		call_popup(url,4);
	}else {	
		var body="<iframe src=\"customer_checklist.php?custCode="+cust_Code+"\" frameborder=\"0\" height=500 width=\"100%\"></iframe>";
		drawLayer('Customer Check List',body,620,'',document.getElementById("theLayer"));
	}
}
function show_app_users(){
	var cust_Code=$("custCode").value;
	if(cust_Code==''){
		alert("Please Select a Customer");
	}else{
		call_popup('show_app_users.php?custCode='+cust_Code,7);
	}
}
function show_sat_details(){
    var cust_Code=$("custCode").value;
	if(cust_Code==''){
		alert("Please Select a Customer");
	}else{
		window.open('$global_web_domain'+'/sat-integration.html?cust_id='+cust_Code);
	}
}
function open_setup_install(){
	var cust_Code=$("custCode").value;
	if(cust_Code==''){
		alert("Please Select a Customer");
	}else{
		call_popup('setup_install.php?lead_code='+cust_Code,7);
	}
}
function server_hardening(){
    jQuery("#server_hardening_tr").toggle();
    jQuery("#server_hardening_arrow").toggleClass("up_arrow","down_arrow");
}
function open_server_hardening_template(arg){
	var cust_Code=$("custCode").value;
	var cust_name=$("cust_name").value;
	if(cust_Code=='' && cust_name==''){
		alert("Please Select a Customer");
	}else{
		call_popup('server_hardening_template.php?sbd_ver='+arg+'&lead_code='+cust_Code+'&cust_name='+cust_name,8);
	}
}
</script>
<div id="theLayer" id="theLayer" style="position:absolute;width:500px;left:100;top:100;visibility:visible"></div>
<a href="javascript:feedback_call();" class="subtle" title="Check List">
<img alt="SH" src="images/check_list.jpg" class="imagecur" border="0" align="absmiddle" height="20" width="20" tabindex="$tab_index"></a>
<a href="javascript:show_app_users();" title="App Users">App</a>
<a href="javascript:show_sat_details();" title="App Users"><img src="images/sat_tool.png" class="imagecur" border="0" align="absmiddle" height="20" width="20" title="SAT Details"></a>
END;
	}
echo<<<END
<span id="install_check" $hide_install_check_style >
<input type="checkbox" name="chb_installed" $chb_installedchecked id="chb_installed" value="1" tabindex="$tab_index">
<label class="datalabel" for="chb_installed" id="lbl_installed">Installed</label></span>
<label class="datalabel" id="chb_asa_type">&nbsp;</label><input type="hidden" id="old_response_group" name="old_response_group">
	</tr>
	<TR>
		<TD class="datalabel" width="$width_spec"><font color="red" size="3" >*</font>$shopname</TD>
		<td nowrap>
END;
    get_valid_customer_list($cust_name,$customer_id,$tab_index,$cust_readonly,true,true,
    $stop_autocomplete,"followup",$edit_customer_name);
echo<<<END
$skip_column_hidden<a href="javascript:show_addressfileds();" id="show_hide_ank">
<img id="show_hide_img" src="images/down.gif" border="0"></a>  <span id='myg'></span> 
<a id="customer_pd_status" class="subtle"></a>
<span id="gosecure_icon" style="display:none;">| <img src="images/datalocker.png" class="imagecur" border="0" align="absmiddle" height="20" width="20" title="Gosecure customer"></span>
</TD></tr>
	<TR id="row_doorapp">
		<TD class="datalabel">Door No & Society</TD>
		<TD><INPUT  type="text" id="door_no" name="door_no" tabindex="$tab_index"  size="10" class="normal_autocomplete" 
autocomplete="off"  value="$door_no" maxlength="20">
END;
	$tab_index++;
echo<<<END
<span id="row_blocksociety">
<INPUT  type="text" id="building_name" name="building_name" tabindex="$tab_index"  SIZE="31" class="normal_autocomplete" 
onkeypress="javascript:return removeInvalidChar(this,event);" onblur="javascript:setStyleClassName(this,'normal_autocomplete');makeStdWords(this);"  
onfocus="setStyleClassName(this,'focus_autocomplete');" value="$building_name" autocomplete="off"></span>
		</TD></tr>
END;
	$tab_index++;
echo<<<END
<TR id="row_avanue_street"><TD class="datalabel">Street No and Name<font color="red" size="3">$mark_md_Street_Name</font></TD><TD>
<INPUT  type="text" id="street_no" name="street_no" tabindex="$tab_index"  SIZE="10" value="$street_no"  
 class="normal_autocomplete"  onkeypress="javascript:return removeInvalidChar(this,event);" 
  onblur="javascript:setStyleClassName(this,'normal_autocomplete');makeStdWords(this); "  
  onfocus="setStyleClassName(this,'focus_autocomplete');" autocomplete="off">
		<span id="row_street_name">
			<INPUT type="text" id="street_name" name="street_name" tabindex="$tab_index" SIZE="31	" value="$street_name"  class="normal_autocomplete" value="" 
			onkeypress="javascript:return removeInvalidChar(this,event);" 
			onblur="javascript:setStyleClassName(this,'normal_autocomplete');makeStdWords(this);"
			onfocus="setStyleClassName(this,'focus_autocomplete');" autocomplete="off">
		</span></td></TR>
<TR id="row_landmark"><TD class="datalabel"><font color="red" size="3">$mark_md_Landmark</font>Landmark</TD>
<TD><INPUT  type="text" id="landmark" name="landmark" tabindex="$tab_index"  SIZE="43" value="$landmark"  
  class="normal_autocomplete" onkeypress="javascript:return removeInvalidChar(this,event);" 
  onblur="javascript:setStyleClassName(this,'normal_autocomplete');makeStdWords(this);" 
    onfocus='setStyleClassName(this,"focus_autocomplete")' autocomplete="off">
</TD></TR> 
<tr id="row_location"><TD class="datalabel"><font color="red" size="3">$mark_md_Location</font>Location</TD>
<td>
END;
	$tab_index++;
echo<<<END
<input type="text" id="loc_name" name="loc_name" type="text" size="43" tabindex="$tab_index" class="normal_autocomplete" 
 onkeypress="javascript:return removeInvalidChar(this,event);" onblur='setStyleClassName(this,"normal_autocomplete");makeStdWords(this);'  
 onfocus='setStyleClassName(this,"focus_autocomplete")' autocomplete="off"  value="$location" />
<input id="locCode" name="locCode" type="hidden">
END;
	$tab_index++;
echo<<<END
</td></tr>
<tr id="row_area_name"><TD class="datalabel"><font color="red" size="3">$mark_md_Area_Name</font> Area Name</TD>
<TD><INPUT type="text" id="area_name" name="area_name"  size="25" tabindex="$tab_index" 
 onkeypress="javascript:return removeInvalidChar(this,event);" value="$area_name" 
 class="normal_autocomplete" onblur="setStyleClassName(this,'normal_autocomplete');makeStdWords(this);"
 onfocus="setStyleClassName(this,'focus_autocomplete');" autocomplete="off"/>
	<span id="row_pincode"><font color="red" size="3">$mark_md_Pincode</font>Pin&nbsp;&nbsp;&nbsp;
		<INPUT type="text" name="pcode" id="pcode"  size="10" tabindex="$tab_index"  class="normal_autocomplete" 
		onkeypress="javascript:return removeInvalidChar(this,event);" 
		onblur="onReplaceDigit(this);setStyleClassName(this,'normal_autocomplete');makeStdWords(this);"
		onfocus="setStyleClassName(this,'focus_autocomplete');" value="$pincode" autocomplete="off">
		<INPUT  type="hidden" name="common_target" id="common_target" value="">
	</span>
	</td></tr>
	<tr id="row_city_name">
		<TD class="datalabel"><font color="red" size="3" >$mark_md_City</font>City/District</TD>
		<TD>
END;
	get_city_list_ajax($tab_index,$city_name);
	$tab_index++;
echo<<<END
		</td></tr>
	<tr id="row_state_name">
		<TD class="datalabel"><font color="red" size="3" >$mark_md_State</font>State</TD>
		<td nowrap>
END;
	$tab_index++;
	get_state_list_ajax($tab_index,$state_name,$state_code);
echo<<<END
		</td></tr>
	<tr id="row_country_name">
		<td class="datalabel"><font color="red" size="3" >$mark_md_Country</font>Country</TD>
		<td nowrap>
END;
	$tab_index++;
	get_country_list_ajax($tab_index,$country_name,$country_code);
echo<<<END
	</tr>	  	  
	<tr id="row_Address_body" style='display:none'>
		<TD class="datalabel">Address</TD>
		<td ><span id="column_Address_body"> </span> </td>
	</tr>
END;
	if($show_order_dtl==true){
echo<<<END
	<tr>
		<td class="datalabel">Order Detail</TD>
		<td><a href="OrderDetails" title="Order Details" onclick="javscript:var ccode=$('custCode').value;if(ccode!=''){tt=ccode.split('-');window.open('order_releated_details.php?lcode='+tt[0],'_blank');}return false;">[O]</a></td>
	</tr>
END;
	}
echo<<<END
	<tr><td colspan='2'><a href="Excel_File/Migration_Template.xls" target="_blank">Migration Template File</a> 
		&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:open_setup_install();">GCM Setup Install</a>
		&nbsp;&nbsp;&nbsp;&nbsp;<a style="text-decoration:underline; cursor:pointer" onclick="var ccode=$('custCode').value;console.log(ccode);if(ccode!=''){window.open('customer_alr_details.php?cust_id='+ccode);}else{alert('Please select a customer.');}" target="_blank">About Customer</a>
		&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:server_hardening();">Server Hardening Template <img id="server_hardening_arrow" class="down_arrow" src="images/down.gif" border="0"></a></td></tr>
    <tr id="server_hardening_tr" style="display:none">
        <td colspan='2'><a href="javascript:open_server_hardening_template(1);">HQ server</a> &nbsp;&nbsp;&nbsp;&nbsp;
        <a href="javascript:open_server_hardening_template(3);">ERP server</a>&nbsp;&nbsp;&nbsp;&nbsp;
        <a href="javascript:open_server_hardening_template(2);">TruePOS server</a>&nbsp;&nbsp;&nbsp;&nbsp;
         <a href="javascript:open_server_hardening_template(4);">HQ Database server</a></td>
    </tr>
END;
	if($quotation_dtl_opt==true){
echo<<<END
	<tr><td colspan=2>
			<table border=1 width="100%">
				<tr id='quote_proforma_label'><td width="33%" class='quote_proforma' align="center">Quotations</td>
					<td width="33%" class='quote_proforma' align="center">Proforma Invoice</td>
					<td width="34%" class='quote_proforma' align="center">Pilot Orders</td>
				</tr>
				<tr id='quote_proforma_buttons'><td id="quot" name="quot">&nbsp;</td><td id="proforma" name="proforma">&nbsp;</td>
					<td id="service_o" name="service_o">&nbsp;</td></tr>
			</table>
		</td>
	</tr>
END;
	}else{
		echo<<<END
		
	<tr><td colspan=2>
			<table width="100%">
				<tr id='quote_proforma_buttons'><td id="service_o" name="service_o">&nbsp;</td></tr>
			</table>
	</td></tr>
END;
	}
if($show_gft_lead_info==true){
echo<<<END
	<!--to display : sales executive incharge and created by whom? -->
	<tr id='show_gft_lead_info'><td colspan='2' id="gft_lead_info" bgcolor='teal' style="display:none;color:white;font-size:12px;"></td></tr>

END;
}
	
	
echo<<<END
</table>
<script type="text/javascript">
pinc_init();
new AjaxJspTag.Autocomplete(
	 "list_customername.php", {
	minimumCharacters: "1",
	parameters: "customername={corporate_name}&lead_type=3",
	progressStyle: "throbbing",
	target: "corporateCode", 
	className: "autocomplete",
	emptyFunction: cor_emptyFunction,
	postFunction: cor_postFunction,
	source: "corporate_name"
});
</script>
END;
if($hide_addressfileds==true){
echo<<<END
<script type="text/javascript">
	hide_addressfileds();
</script>
END;
}
	
}//end of function

//function customer_type
/**
 * @param int $tab_index
 * @param string[int] $lead_type_list
 * 
 * @return void
 */
function customer_type($tab_index=0,$lead_type_list=null){
	global $uid;
	$lead_subtype_list=get_lead_subtype();
	$lead_type_list=get_lead_type($lead_type_list);
	$allow_lead_type_change = false;
	if(is_authorized_group_list($uid, array(34,106,54,96))){
		$allow_lead_type_change = true;
	}
	$allow_lead_type_change = json_encode($allow_lead_type_change);
echo<<<END
<div id="show_lead">
<table width=100% border="0">
	<tr ><TD class="datalabel" ><font color="red" size="3" >*</font>Lead Type</TD><TD>
		<input type="hidden" name="old_lead_type" id="old_lead_type">
		<select size="1" name="lead_type" id="lead_type" onchange="javascript: show_lead_subtype($allow_lead_type_change);" 
		onkeyup="javascript: show_lead_subtype($allow_lead_type_change);" tabindex="$tab_index"  class="formStyleTextarea">
END;
	for($i=0;$i<count($lead_type_list);$i++){
		$t_id=$lead_type_list[$i][0];
		$t_name=$lead_type_list[$i][1];
		echo "<option value=\"$t_id\">$t_name</option>";
	}
echo<<<END
		</select>
		</TD>
		<td id="show_lead_subtype" class="datalabel" nowrap ><font color="red" size="3" >*</font>Lead Sub Type</TD>
		<td id="show_lead_subtype2"><input type="hidden" name="old_lead_sub_type" id="old_lead_sub_type">
			<select size="1" name="lead_sub_type" id="lead_sub_type" tabindex="$tab_index"  class="formStyleTextarea">
			<option value="0">Select</option>
END;
	for($i=0;$i<count($lead_subtype_list);$i++){
		$sub_type_code=$lead_subtype_list[$i][0];
		$sub_type_name=$lead_subtype_list[$i][1];
		echo "<option value=\"$sub_type_code\">$sub_type_name</option>";
	}
echo<<<END
			</select></td>
		<td class="corp_delivery datalabel hide" nowrap ><font color="red" size="3" >*</font>Delivery Type</TD>
		<td class="corp_delivery hide">
			<select size="1" name="corp_delivery_type" id="corp_delivery_type" class="formStyleTextarea">
				<option value=0>-select-</option>
				<option value=1>Solution Delivery</option>
				<option value=2>Product Delivery</option>
			</select>
		</td>
	</tr>
	<tr><td id="partner_suggession" colspan=4></td></tr></table>
</div>
END;

echo<<<END
<script type="text/javascript">
document.getElementById("show_lead_subtype").style.display = 'none';
document.getElementById("show_lead_subtype2").style.display = 'none';
</script>
END;
}

/**
 * @param string $lead_source_p
 * @param string $lead_source_p_id
 * @param string $lead_source_e
 * @param string $lead_source_e_id
 * @param string $lead_source_c
 * @param string $lead_source_c_id
 * @param boolean $show1
 * @param boolean $show2
 * @param boolean $show3
 * @param boolean $check_partner_src
 * 
 * @return void
 */
function show_reference_details($lead_source_p,$lead_source_p_id, $lead_source_e,$lead_source_e_id,
			$lead_source_c,$lead_source_c_id,$show1=true,$show2=true,$show3=true,$check_partner_src=false){
	global $uid;	
	$referance_e='';
	$referance_c='';
	$referance_p='';
	$lead_src_details_e='';
	$lead_src_details_c='';
	$lead_src_details_p='';

	$add_event_link='<td><a href="event_master_details.php" target="_blank">EventDetails</a></td>';		
	if(is_authorized_group_list($uid,array(13,31,39))){
		$show1=false;$show3=false;
		$add_event_link='';
	}	
	
	global $tab_index;
	if($show1){
		$lead_source_list_p=get_lead_source('2');
		$is_editable = true;
		$src_type_readonly = '';
		if($check_partner_src and !is_authorized_group_list($uid,array(34))) { // Allowed only for partner management team
			//$is_editable = false;
			//$src_type_readonly = 'disabled';
		}
		$lead_src_details_p="<input type=\"hidden\" name=\"ex_lead_src_P\" id=\"ex_lead_src_P\">";
		$lead_src_details_p.="<select size=\"1\" name=\"lead_src_P\" id=\"lead_src_P\"  tabindex=\"$tab_index\"" .
				" class=\"formStyleTextarea\" onchange=\"javascript:onchange_refname_p();\" $src_type_readonly>";
		for($i=0;$i<count($lead_source_list_p);$i++){
			$src_id=$lead_source_list_p[$i][0];
			$src_name=$lead_source_list_p[$i][1];
			$s="";
			if($lead_source_p==$src_id){ $s="selected"; }
			$lead_src_details_p.="<option value=$src_id $s > $src_name</option>";

		}
		$lead_src_details_p.="</select>";
		$partner_name=get_name_of_customer($lead_source_p_id);
		$referance_p = get_reference_list($tab_index,$add_name="_P",$lead_source_p_id,$partner_name,$return_value=true,$is_editable);
    }
	if($show3){
		$lead_source_list_e=get_lead_source('1');
		$lead_src_details_e="<input type=\"hidden\" name=\"ex_lead_src_E\" id=\"ex_lead_src_E\">";
		$lead_src_details_e.="<select size=\"1\" name=\"lead_src_E\" id=\"lead_src_E\"  tabindex=\"$tab_index\"" .
					" class=\"formStyleTextarea\" onchange=\"javascript:onchange_refname_e();\"();\">";
	           
	 	$lead_source_e_name=get_emp_name($lead_source_e_id);
	    for($i=0;$i<count($lead_source_list_e);$i++){
			$src_id=$lead_source_list_e[$i][0];
			$src_name=$lead_source_list_e[$i][1];
			$s="";
			if($lead_source_e==$src_id){ $s="selected"; }
			$lead_src_details_e.="<option value=$src_id $s > $src_name</option>";

		}   
		$lead_src_details_e.="</select>";
		$referance_e=get_reference_list($tab_index,$add_name="_E",$lead_source_e_id,$lead_source_e_name,$return_value=true);
	}
	if($show2){		
     	$lead_source_list_c=get_lead_source('3');		
		$lead_src_details_c="<input type=\"hidden\" name=\"ex_lead_src_C\" id=\"ex_lead_src_C\">";
		$lead_src_details_c.="<select size=\"1\" name=\"lead_src_C\" id=\"lead_src_C\"  tabindex=\"$tab_index\"
	    		  class=\"formStyleTextarea\" onchange=\"javascript:onchange_refname_c();\">";
		if($lead_source_c_id=='' or $lead_source_c_id=='0'){
			$lead_source_c_name='';
		}else {
			$lead_source_c_name=get_name_of_customer($lead_source_c_id);
		}
	    for($i=0;$i<count($lead_source_list_c);$i++){
			$src_id=$lead_source_list_c[$i][0];
			$src_name=$lead_source_list_c[$i][1];
			$s="";
			if($lead_source_c==$src_id){ $s="selected"; }
			$lead_src_details_c.="<option value=$src_id $s > $src_name</option>";
		}
    	$lead_src_details_c.="</select>";
		$referance_c=get_reference_list($tab_index,$add_name="_C",$lead_source_c_id,$lead_source_c_name,$return_value=true);
	}
	$query_mandatory_fields="select GLS_SOURCE_CODE,GLS_MANDATORY_CODE,GLS_MANDATORY_TEXT from gft_lead_source_master";
	$result_mandatory_fields=execute_my_query($query_mandatory_fields);
	$mandatory_fields="";
	while($qd=mysqli_fetch_array($result_mandatory_fields)){
		$ls=$qd["GLS_SOURCE_CODE"];
		$mandatory_text=$qd["GLS_MANDATORY_TEXT"];
		$mandatory_code=$qd["GLS_SOURCE_CODE"];
$mandatory_fields.=<<<END
	<input type="hidden" id="ls_mandatory_text[$ls]" value="$mandatory_text" >
	<input type="hidden" id="ls_mandatory_code[$ls]" value="$mandatory_code" >    
END;

	}
echo<<<END
<script>function onchange_of_internal_ref(){}</script>
<script type="text/javascript">
function onchange_refname_c(){ $("ref_name_C").value='';}
function onchange_refname_e(){ $("ref_name_E").value='';}
function onchange_refname_p(){ $("ref_name_P").value='';}

</script>
<table>
END;
	if($show1){
echo<<<END
<tr><td class="datalabel" nowrap><font color="red" size="3" ></font>Partner/Company $mandatory_fields</TD>
<TD>$lead_src_details_p</td>
<td>$referance_p</td>
END;
	}
	if($show2){
echo<<<END
<tr><td class="datalabel" nowrap><font color="red" size="3" ></font> Cust/Other </TD>
<TD>$lead_src_details_c</td>
<td>$referance_c </td> $add_event_link 
END;
	}
	if($show3){
echo<<<END
<tr><td class="datalabel" nowrap><font color="red" size="3" ></font>Internal </TD>
<TD>$lead_src_details_e</td>
<td>$referance_e</td>
END;
	}
echo<<<END
</table>           
END;
}

/**
 * @param string $alow_change_status
 * @param string $privilage_to_edit_lead_status
 * @param boolean $for_new_lead
 * 
 * @return string
 */
function customer_lead_status($alow_change_status='false',$privilage_to_edit_lead_status="N",$for_new_lead=false){
	global $tab_index;
	$customer_status_list=get_customer_status_list(null,null,$for_new_lead);
	$prospects_status_list=get_two_dimensinal_result_set_from_query("select GPS_STATUS_ID ,GPS_STATUS_NAME from gft_prospects_status_master where GPS_STATUS='A' and gps_lead_type=1 order by GPS_ORDER_BY ");
	$prospects_status_list_partner=get_two_dimensinal_result_set_from_query("select GPS_STATUS_ID ,GPS_STATUS_NAME from gft_prospects_status_master where GPS_STATUS='A' and gps_lead_type=2 order by GPS_ORDER_BY ");
	$prospects_cmb=fix_combobox_with('prospects_status','prospects_status',$prospects_status_list,$selected_value=0,$tab_index,$default_value='-Select',
	$style="style='width:150px' ",$add_opt_group=false,"onchange=\"javascript:reason_for_RPS_change('$alow_change_status');\"");
	$status_cmb=fix_combobox_with($id='lead_status',$name='lead_status',$customer_status_list,$selected_value,$tab_index,$default_value='-Select',
	$style="style='width:150px' ",$add_opt_group=false,$onchange_function="onchange=\"javascript:changeLeadStatus('$alow_change_status');\"");
$return_string=<<<END
<tr id="tr_status">
	<td class="datalabel"><font color="red" size="3" >*</font> Lead Status</TD>
	<td><input type="hidden" id="ex_lead_status" name="ex_lead_status" value="0">
	    <input type="hidden" name="privilage_to_edit_lead_status" id="privilage_to_edit_lead_status" value="$privilage_to_edit_lead_status">
		<input type="hidden" id="reason_mandatory" name="reason_mandatory" value="N">
		<input type="hidden" id="ls_can_change_manually" name="ls_can_change_manually" value="Y">
		<input type="hidden" id="ls_SE_can_change_manually" name="ls_SE_can_change_manually" value="Y">
		<input type="hidden" id="ls_edc_mandatory" name="ls_edc_mandatory" value="Y">
		<input type="hidden" id="ls_next_action_mandatory" name="ls_next_action_mandatory" value="Y">
		<input type="hidden" id="show_prospects_status" name="show_prospects_status" value="N">
		$status_cmb</TD></tr>
<tr id="reason_for_change_in_lead_status" style="display:none">
	<td class="datalabel">Reason for Lead Status Change</td>
	<td><select  id="reason_change_in_status" name="reason_change_in_status" class="formStyleTextarea" 
		onchange="onchange_reason_change_in_status(this.value);" style='width:150px'>
		<option value='0'>-Select-</select></select>
	</td>
</tr>
<tr id="reason_for_change_in_lead_status_dtl" style="display:none" >
	<td class="datalabel">Reason for Lead Status Change Detail </td>
	<td><textarea id="reason_change_in_status_others" name="reason_change_in_status_others" maxlength='75'  
		class="formStyleTextarea" rows="3" cols="30"></textarea></td></tr>

		
<tr id="tr_prospect_status"  style="display:none">
	<td class="datalabel"><font  size="3" class="mandatory_marker_red" >*</font> Prospect Status</td>
	<td>
	<input type="hidden" id="ex_prospects_status" name="ex_prospects_status" value="0">
	<input type="hidden" id="ps_edc_mandatory" name="ps_edc_mandatory" value="N">
	<input type="hidden" id="ps_edc_mandatory_ndays_check" name="ps_edc_mandatory_ndays_check" value="0">
	<input type="hidden" id="ps_next_action_mandatory" name="ps_next_action_mandatory" value="N">
	<input type="hidden" id="ps_cross_check" name="ps_cross_check" value="N">
	<input type="hidden" id="prospect_reason_mandatory" name="prospect_reason_mandatory" value="N">$prospects_cmb </td></tr>
<tr id="reason_for_change_in_prospects_status" style="display:none">
	<td class="datalabel"><font  size="3" class="mandatory_marker_red" >*</font> Reason for Prospect status Change</td>
	<td><select  id="reason_change_in_pstatus" name="reason_change_in_pstatus" class="formStyleTextarea" 
		onchange="onchange_reason_change_in_pstatus(this.value);" style='width:150px'>
		<option value='0'>-Select-</option></select></td></tr>
<TR id="competitor_dtl" style="display:none"><TD colspan="2"> 
<div id="show_competition"  class="hide">
<fieldset><legend align="center"><font size="2" color="red"> Competitor's Details </font></legend>
END;
$tab_index=90;
$new_comp=1;
$return_string.=enter_comp_details($tab_index,$new_comp);//32
$return_string.=<<<END
</fieldset></div></td></tr>		
<tr id="reason_for_change_in_pstatus_dtl" style="display:none">
	<td class="datalabel">Reason for Prospect status Change Detail </td>
	<td><textarea id="reason_change_in_pstatus_others" name="reason_change_in_pstatus_others" maxlength='75'  
		class="formStyleTextarea" rows="3" cols="30"></textarea></td></tr>
<tr id="tr_doc" style='display:none'>
	<td class="datalabel"><font  size="3" class="mandatory_marker_red" >*</font> <label title="Exepected Date Of Closing"> EDC </label></TD>
	<td nowrap>
		<input type="hidden" id="ex_doc" name="ex_doc" value="">
		<input name="doc" type="text" id="doc"  class="formStyleTextarea" Readonly='true' >
		&nbsp;<a href="javascript:makeClick(this);" id="onceDateIcon1_doc" tabindex="$tab_index" >
		<img alt="" src="images/date_time.gif" width="16" border="0" align="middle" ></a></td>
</tr>
<tr id='tr_license_value' style='display:none'>
		<td class="datalabel"><font  size="3" class="mandatory_marker_red" >*</font> Potential Value</td>
		<td><input type="hidden" id="ex_license_value" name="ex_license_value">
			<input type="text" id="license_value" name="license_value" class="formStyleTextarea" tabindex="$tab_index"  
			onkeyup="javascript:extractNumber(this,2,false);" onkeypress="javascript:return blockNonNumbers(this, event, true, false);" ></td>
</tr>
<tr id='tr_advance_collectiable' style='display:none'>		
		<td class="datalabel"> Advance Collectible </td>	
		<td><input type="hidden" id="ex_service_value" name="ex_service_value">
			<input type="text" id="service_value" name="service_value" class="formStyleTextarea" tabindex="$tab_index" 
			onkeyup="javascript:extractNumber(this,2,false);" onkeypress="javascript:return blockNonNumbers(this, event, true, false);" ></td>
</tr>
				
<script type="text/javascript">
function makeClick(ob){}init_date_func("doc","%Y-%m-%d","onceDateIcon1_doc","Bl");</script>   		
END;
return $return_string;
}

/**
 * @param string $title
 * @param string $assigned_to
 * @param int $tab_index
 * @param int $cols
 * @param boolean $na
 * @param boolean $fa_mandaotory
 * @param boolean $fa_date
 * @param string $colwidth
 * @param bool $get_role
 * 
 * @return void
 */
function show_field_plan_dtl($title,$assigned_to,$tab_index=0,$cols=0,$na=true,$fa_mandaotory=false,
$fa_date=false,$colwidth="",$get_role=false){
	global $get_activity_others_list,$man_assign_to;
	$next_activtiy_list=get_activity_list_with_group($get_activity_others_list,true);
	$tab_indexes=/*. (int[int]) .*/ array();

	if($tab_index==''){ 
		$tab_indexes[0]=29;
	}else {
		$tab_indexes[0]=$tab_index++;
	}
	$show_fa_date_mandotory=($fa_date==true?"<font color='red'>*</font>":'');
	
	$tab_indexes[1]=$tab_index++;
	$tab_indexes[2]=$tab_index++;
	$tab_indexes[3]=$tab_index++;
	$tab_indexes[4]=$tab_index++;
	$tab_indexes[5]=$tab_index++;
	$table_width="width='100%'";
	if($colwidth==""){
		$colwidth="141";
		$table_width="";
	}
echo<<<END
<script type='text/javascript'>
function empDetails(){}
</script>
<fieldset>
<legend align="center" ><font size="2" color="red"> $title</font></legend>
<table id="table2" border="0" cellpadding="2" cellspacing="1" bgcolor="#b6e5ff" $table_width>
<tbody><TR><TD class="datalabel"  width="$colwidth"> $show_fa_date_mandotory Date &amp; Time</TD>
<TD ><INPUT id="demo_date" name="demo_date"  value=''  size="20" class="formStyleTextarea" 
ondblclick="javascript:this.value=''" onchange="javascript:date_check_should_be_greater('demo_date');" tabindex=$tab_indexes[0] readonly>&nbsp;
<script type="text/javascript">
// init_date_func_without_button("demo_date","%Y-%m-%d %H:%M","onceDateIcon2","Bl");
function dumpfunc(){}
</script>
</td>
END;
$show_fa_mandotory="";
global $uid;
	if(is_authorized_group($uid,1)){
		$man_assign_to='A';
	}
	if($cols==2) echo '<tr>';
		$sel_group_arr=array(5,6,62,20,27,34,35,12,23);
	if(is_authorized_group_list($uid,$sel_group_arr) or is_authorized_group_list($uid,array(1))){
		$sel_group_arr=array(5,6,62,20,27,34,35,12,23,13);
	}
	$show_fa_mandotory=($fa_mandaotory==true?"<font color='red'>*</font>":'');
		
echo<<<END
		<TD class="datalabel"  width="150">Assign  To</TD>
		<TD width="200"><input type="hidden" id="man_assign_to" name="man_assign_to" value="$man_assign_to">
END;
get_followup_employee_list($tab_index=null,$femp_name=null,$femp_code=null,$blockNoResult=null,$emptyIfNew=null,
$show_others=1,$sel_group_arr,$sel_role_arr=null,$all_emp='off',$get_role);
$baton_label = LIVE_BATON_LABEL;
echo<<<END
		</TD>
	</tr>
	<tr><td align='right'>$show_fa_mandotory Assign as Lead Incharge</td>
	<td>
		<input type="radio" name="assign_lead" id="assign_lead0" value='off' title="Followup will be assigned">
		<label class="datalabel" for="assign_lead0">No</label>
		<input type="radio" name="assign_lead" id="assign_lead1" value='on' title="He will be lead incharge">
		<label class="datalabel" for="assign_lead1">Yes</label>
	</td></tr><tr id="msg_row" hidden><td></td><td colspan=2><div id="assign_lead_msg" style='color:red'></div></td></tr>
<tr id='tr_baton'><TD align='right'><input type='checkbox' name='baton_wobbling' id='baton_wobbling'></TD>
<TD class="datalabel" style='text-align:left;' width="130" nowrap>$baton_label</TD></tr>
	<tr id="followup_action" ><td  class="datalabel"  width="150"> $show_fa_mandotory Followup Action </td>
		<TD>
END;
			$tab_index++;
			echo fix_combobox_with('fa','fa',$next_activtiy_list,0,$tab_index,'Select',null,true);
echo<<<END
		</td></tr>
	<tr id="tr_follow_up_detail" style='display:none'>
		<td  width="150"   class="datalabel"><font color="red">*</font>Followup Detail</td>
		<td><textarea id="follow_up_detail" name="follow_up_detail" rows="3"  cols="30" maxlength="500" 
			class="formStyleTextarea" tabindex="$tab_indexes[3]"  onblur="copy_content_to(this.id,'send_sms_content');"></textarea></td>
	</tr>
</table>
</fieldset>
<script type='text/javascript'>
function copy_content_to(id1,id2){
	if( $(id2) && $(id2) ){
		$(id2).value=$(id1).value;
	}
}
var jq = jQuery.noConflict();
function show_msg() {
	var assign_lead = jq("input:radio[name=assign_lead]:checked").val();
	var femp_name = jq("#femp_name").val();
	jq("#assign_lead_msg").text("");
	jq("#msg_row").show();
	if(assign_lead=="on" && femp_name!="") {
		jq("#assign_lead_msg").text("Assigning as lead incharge and also assigning a followup activity.");
	} else if(assign_lead=="off" && femp_name!=""){
		jq("#assign_lead_msg").text("Assigning only a followup activity.");
	}
}
jq("input:radio[name=assign_lead]").change(function() {
	show_msg();
});
jq("#femp_name").change(function() {
	show_msg();
});
</script>	
END;

}
?>
