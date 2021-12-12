<?php
/*. require_module 'standard'; .*/
/*. require_module 'mysql'; .*/

require_once(__DIR__ ."/dbcon.php");

/**
 * @param string $GCH_CUST_NAME
 * @param string $lc
 * @param int $tabindex
 * @param string $read_only
 * @param string $blockNoResult
 * @param string $emptyIfNew
 * @param boolean $stop_autocomplete
 * @param string $purpose
 * @param boolean $edit_customer_name
 * 
 * @return void
 */
function get_valid_customer_list($GCH_CUST_NAME=null,$lc=null,&$tabindex=0,$read_only=null,$blockNoResult=null,
		$emptyIfNew=null,$stop_autocomplete=false,$purpose=null,$edit_customer_name=false){
	global $cp_lcode,$cp_roleid,$select_lead_type;
	$new_lead_code	=	isset($_GET['lead_code'])?$_GET['lead_code']:'';
 	if($blockNoResult){
		$blockNoResult='blockNoResultStatus: "1",';
	}else{
		$blockNoResult='';
	}
	$autocomplet_temp_stop="";
	if($emptyIfNew)	{
	  $emptyIfNew= 'doEmptyTargetIfNew: "1",';
	}else {
	   $emptyIfNew= '';
	}
	$read_status='';
	$cust_blur_fn='';
	if($edit_customer_name==false){	
$cust_blur_fn=<<<END
class="normal_autocomplete" onblur='setStyleClassName(this,"normal_autocomplete")' 
onfocus='setStyleClassName(this,"focus_autocomplete")' onkeydown="javascript: check_empty_cust();" 
END;
	}
	if($new_lead_code!=''){
		$lc	=	$new_lead_code;
	}
	if($read_only){	
echo<<<END
<input id="cust_name" name="cust_name" type="text" size="43" tabindex="$tabindex" value="$GCH_CUST_NAME" readonly> 
<input id="custCode" name="custCode" type="hidden" value="$lc">
<input type="hidden" id="outlet_id" name="outlet_id">	
<input type="hidden" name="lic_type" id="lic_type" value="">
END;
	}else {
echo<<<END
<input id="cust_name" name="cust_name" $read_status type="text" size="43" tabindex="$tabindex" class="formStyleTextarea" 
autocomplete="off" value="$GCH_CUST_NAME"  $cust_blur_fn />
<input id="custCode" name="custCode" type="hidden" value="$lc">
<input type='hidden' id='select_lead_type' name='select_lead_type' value='$select_lead_type'>
<input type="hidden" name="stop_autocomplete" id="stop_autocomplete" value="$stop_autocomplete">
<input type="hidden" id="use_search_field" name="use_search_field" value=false>
<input type="hidden" id="outlet_id" name="outlet_id">
<input type="hidden" name="lic_type" id="lic_type" value="">
<script type="text/javascript" >	
function auto_cname_init(){
new AjaxJspTag.Autocomplete(
"list_customername.php", {
minimumCharacters: "1",
parameters: "customername={cust_name}&scontact_no={search_value}&use_search_field={use_search_field}&corporateCode={corporateCode}&installed={chb_installed}&user_id={emp_id}&cp_lcode=$cp_lcode&cp_role_id=$cp_roleid&purpose=$purpose&select_lead_type={select_lead_type}",
progressStyle: "throbbing",
target: "custCode",
className: "autocomplete",
$blockNoResult $emptyIfNew 
stopAutocomplete : document.getElementById("stop_autocomplete").value ,
emptyFunction: custEmptyFunction,
postFunction: custDetails,
source: "cust_name"
});
}//end of function
function custEmptyFunction(){ 
	if($("customer_id")){ $("customer_id").value=""; }
	makeEmptyValues_support(); 
}
</script>
END;
		if($edit_customer_name==false and $stop_autocomplete==false){
echo<<<END
<script type="text/javascript">	
auto_cname_init();
</script>
END;
		}// end of if 
	}//end of else

}

/**
 * @param int $tab_index
 * @param string $filed_name
 * @param string $filed_id
 * @param string $hidden_f_name
 * @param string $hidden_f_id
 * @param string $parameters
 * @param string $source_param
 * @param string $set_source_val
 * @param string $set_target_val
 * 
 * @return string
 */
function get_list_common_ajax($tab_index,$filed_name,$filed_id,$hidden_f_name,$hidden_f_id,$parameters,$source_param,$set_source_val='',$set_target_val=''){
$returnVar=<<<END
<input id="$filed_id" name="$filed_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$set_source_val" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="$hidden_f_id" name="$hidden_f_name" type="hidden" value="$set_target_val">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "$parameters&$source_param={{$filed_id}}",
progressStyle: "throbbing",
target: "{$hidden_f_id}",
emptyFunction: function(){ $("{$hidden_f_id}").value=''; },
className: "autocomplete",
source: "$filed_id"
});
</script>
END;
return $returnVar;
} 

/**
 * @param int $tab_index
 * @param string $field_name
 * @param string $field_id
 * @param string $field_code_name
 * @param string $field_code_id
 * @param string $comp_code_value
 * @param string $comp_name_value
 * @param int $new_comp
 * 
 * @return string
 */
function get_competitor_list($tab_index,$field_name,$field_id,
$field_code_name,$field_code_id,$comp_code_value=null,$comp_name_value=null,$new_comp=0){
$new_link='';
$return_string="";
if($new_comp==1){
	$new_link='<a href="javascript:popit_new_competitor_dtl(\'new_competitor_detail.php\',this.value)" >New</a> ';
}
$return_string.=<<<END
<input id="$field_id" name="$field_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$comp_name_value" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="$field_code_id" name="$field_code_name" type="hidden" value="$comp_code_value">
<input id="ex_$field_id" name="ex_$field_name" value="$comp_name_value" type="hidden">
<input id="ex_$field_code_id" name="ex_$field_code_name"  value="$comp_code_value" type="hidden">
$new_link
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "purpose=list_competitor&comp_name={{$field_id}}",
progressStyle: "throbbing",
target: "{$field_code_id}",
className: "autocomplete",
source: "$field_id"
});
</script>
END;
return $return_string;
}

/**
 * @param int $tab_index
 * @param string $field_name
 * @param string $field_id
 * @param string $field_code_name
 * @param string $field_code_id
 * @param string $comp_code_value
 * @param string $prod_name_value
 * 
 * @return string
 */
function get_competitor_product_list($tab_index,$field_name,$field_id,
$field_code_name,$field_code_id,$comp_code_value,$prod_name_value){
	//global $GCH_CUST_NAME;
	$return_string="";
$return_string.=<<<END
<input id="$field_id" name="$field_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$prod_name_value" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="$field_code_name" name="$field_code_name" type="hidden">
<input id="$field_code_name" name="$field_code_name" type="hidden">
<input id="ex_comp_prod_name" name="ex_comp_prod_name" type="hidden">
<input id="ex_comp_prod_code" name="ex_comp_prod_code" type="hidden">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "purpose=list_competitor_product&comp_prod_name={{$field_id}}&compCode={{$comp_code_value}}",
progressStyle: "throbbing",
target: "{$field_code_id}",
className: "autocomplete",
source: "$field_id"
});
</script>
END;
return $return_string;
}

/**
 * @param int $tab_index
 * @param string $state_name
 * @param string $stateCode
 * 
 * @return void
 */
function get_state_list_ajax($tab_index,$state_name=null,$stateCode=null){
echo<<<END
<input id="state_name" name="state_name" type="text" size="43" tabindex="$tab_index" class="normal_autocomplete" 
value="$state_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="stateCode" name="stateCode" type="hidden"  value="$stateCode">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "purpose=list_state&statename={state_name}",
progressStyle: "throbbing",
target: "stateCode",
className: "autocomplete",
emptyFunction: stateEmptyFunction,
postFunction: itemStateSelected,
source: "state_name"
});

function stateEmptyFunction(){
	document.getElementById("stateCode").value='';
}
function itemStateSelected(){
	var locale=$('stateCode').value;
	if(locale!=''){
 		var temp1=locale.split('-##-');
 		$("stateCode").value=temp1[0];
 		if($("countryCode")){
 			$("countryCode").value=temp1[1];
 			$("country_name").value=temp1[1];
 		}
	}
}
</script>
END;
}

/**
 * @param int $tab_index
 * @param string $country_name
 * @param string $countryCode
 * 
 * @return void
 */
function get_country_list_ajax($tab_index,$country_name=null,$countryCode=null){
echo<<<END
<input id="country_name" name="country_name" type="text" size="43" tabindex="$tab_index" class="normal_autocomplete" 
value="$country_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="countryCode" name="countryCode" type="hidden"  value="$countryCode">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "purpose=list_state&countryname={country_name}",
progressStyle: "throbbing",
target: "countryCode",
className: "autocomplete",
emptyFunction: countryEmptyFunction,
source: "country_name"
});

function countryEmptyFunction(){
	document.getElementById("countryCode").value='';
}
</script>
END;
}

/**
 * @param int $tab_index
 * @param string $city_name
 * 
 * @return void
 */
function get_city_list_ajax($tab_index=0,$city_name=''){
echo<<<END
<input id="city_name" name="city_name" type="text" size="43" tabindex="$tab_index" class="normal_autocomplete" 
value="$city_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="cityCode" name="cityCode" type="hidden">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "purpose=city_list&cityname={city_name}&user_id={emp_id}",
progressStyle: "throbbing",
target: "cityCode",
className: "autocomplete",
source: "city_name"
});
</script>
END;
}

/**
 * @param int $tab_index
 * 
 * @return void
 */
function get_ref_customer_list($tab_index){
echo<<<END
<input id="ref" name="ref" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="rcustCode" name="rcustCode" type="hidden">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "purpose=ref_cust&customername={ref}&type_src={lead_src}",
progressStyle: "throbbing",
target: "rcustCode",className: "autocomplete",source: "ref"
});
</script>
END;
}

/**
 * @param int $tab_index
 * @param string $add_name
 * @param string $lead_source_id
 * @param string $ref_name
 * @param boolean $return_value
 * @param boolean $is_editable
 * 
 * @return string
 */
function get_reference_list($tab_index,$add_name=null,$lead_source_id=null,$ref_name=null,$return_value=false,$is_editable=true){
	$readonly = "readonly";
	$autocmplete = "";
	if($is_editable) {
		$readonly = "";
		$autocmplete = <<<END
new AjaxJspTag.Autocomplete(
"list_reference.php", {
minimumCharacters: "1",
parameters: "name={ref_name$add_name}&type_src={lead_src$add_name}",
progressStyle: "throbbing",
target: "refCode$add_name",className: "autocomplete",source: "ref_name$add_name"
});
END;
	}
$ref_field=<<<END
<input id="ref_name$add_name" name="ref_name$add_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" $readonly 
value="$ref_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="refCode$add_name" name="refCode$add_name" type="hidden" value="$lead_source_id" $readonly>
<input id="ex_refCode$add_name" name="ex_refCode$add_name" type="hidden" value="$lead_source_id">
<input id="ex_ref_name$add_name" name="ex_ref_name$add_name" type="hidden" value="$ref_name">
<script type="text/javascript" >	
$autocmplete
</script>
END;
	if($return_value==true){
		return $ref_field;
	}else {
		echo $ref_field;
		return '';
	}
}

/**
 * @param int $tab_index
 * @param string $emp_name
 * @param string $emp_code
 * @param string $blockNoResult
 * @param string $emptyIfNew
 * @param string $show_others
 * @param string[int] $sel_group_arr
 * @param string[int] $sel_role_arr
 * @param string $all_emp
 * @param string $field_name
 * @param string $field_code
 * @param boolean $return_val
 * @param boolean $include_partners
 * 
 * @return string
 */
function get_employee_list($tab_index=0,$emp_name=null,$emp_code=null,$blockNoResult=null,$emptyIfNew=null,
$show_others=null,$sel_group_arr=null,$sel_role_arr=null,$all_emp='off',
$field_name='emp_name',$field_code='emp_code',$return_val=false,$include_partners=false){
	if($blockNoResult){
		$blockNoResult='blockNoResultStatus: "1",';
	}else{
		$blockNoResult='';
	}
	
	if($emptyIfNew)	{
	  $emptyIfNew= 'doEmptyTargetIfNew: "1",';
	}else {
	   $emptyIfNew= '';
	}
	
	$exec_only="";
	if($show_others) $exec_only.="&show_others=1";
	if($include_partners) $exec_only.="&include_partners=1";
    $select_group="";
	if($sel_group_arr!=null and is_array($sel_group_arr)){
		$select_group.="&select_group[]=".implode("&select_group[]=",$sel_group_arr);
	}else if($sel_group_arr!=null){
		$select_group.="&select_group[]=".$sel_group_arr;
	}
	$select_role='';
	if($sel_role_arr!=null and is_array($sel_role_arr)) 
		$select_role="&select_role[]=".implode('&select_role[]=',$sel_role_arr);
	elseif($sel_role_arr!=null){
		$select_role="&select_role[]=".$sel_role_arr;
	}	
	
$return_value=<<<END
<input id="$field_name" name="$field_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$emp_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")' />
<input id="$field_code" name="$field_code" type="hidden" value="$emp_code">
<script type="text/javascript">
new AjaxJspTag.Autocomplete(
"list_employee_name.php", {
minimumCharacters: "1",
parameters: "empname={{$field_name}}$exec_only&all_emp=$all_emp$select_group$select_role",
progressStyle: "throbbing",
target: "$field_code",
className: "autocomplete",
$blockNoResult $emptyIfNew
emptyFunction: empEmptyFunction,
postFunction: empDetails,
source: "$field_name" 
});

function empEmptyFunction(){ if($("$field_name").value==''){ $("$field_code").value="0";}}
</script>
END;

	if($return_val==true){
		return $return_value;
	}else{
		echo $return_value;
		return ''; 
	}
}

/**
 * @param int $tab_index
 * @param string $emp_name
 * @param string $emp_code
 * @param string $blockNoResult
 * @param string $emptyIfNew
 * @param string $show_others
 * @param string[int] $sel_group_arr
 * @param string[int] $sel_role_arr
 * @param string $all_emp
 * @param string $field_name
 * @param string $field_code
 * @param boolean $return_val
 * 
 * @return string
 */
function get_employee_list_pcs($tab_index=0,$emp_name=null,$emp_code=null,$blockNoResult=null,$emptyIfNew=null,
$show_others=null,$sel_group_arr=null,$sel_role_arr=null,$all_emp='off',
$field_name='emp_name',$field_code='emp_code',$return_val=false){
	if($blockNoResult){
		$blockNoResult='blockNoResultStatus: "1",';
	}else{
		$blockNoResult='';
	}
	
	if($emptyIfNew)	{
	  $emptyIfNew= 'doEmptyTargetIfNew: "1",';
	}else {
	   $emptyIfNew= '';
	}
	
	$exec_only="";
	if($show_others) $exec_only="&show_others=1";
    $select_group="";
	if($sel_group_arr!=null and is_array($sel_group_arr)){
		$select_group.="&select_group[]=".implode("&select_group[]=",$sel_group_arr);
	}else if($sel_group_arr!=null){
		$select_group.="&select_group[]=".$sel_group_arr;
	}
	$select_role='';
	if($sel_role_arr!=null and is_array($sel_role_arr)) 
		$select_role="&select_role[]=".implode('&select_role[]=',$sel_role_arr);
	elseif($sel_role_arr!=null){
		$select_role="&select_role[]=".$sel_role_arr;
	}	
	
$return_value=<<<END
<input id="$field_name" name="$field_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$emp_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")' />
<input id="$field_code" name="$field_code" type="hidden" value="$emp_code">
<script type="text/javascript">
new AjaxJspTag.Autocomplete(
"list_employee_name.php", {
minimumCharacters: "1",
parameters: "empname={{$field_name}}$exec_only&all_emp=$all_emp&select_group[]={deliver_by_id}&custCode={customer_id}",
progressStyle: "throbbing",
target: "$field_code",
className: "autocomplete",
$blockNoResult $emptyIfNew
emptyFunction: empEmptyFunction,
//postFunction: empDetails,
source: "$field_name" 
});

function empEmptyFunction(){ if($("$field_name").value==''){ $("$field_code").value="0";}}
</script>
END;

	if($return_val==true){
		return $return_value;
	}else{
		echo $return_value;
		return ''; 
	}
}


/**
 * @param int $tab_index
 * @param string $emp_name
 * @param string $emp_code
 * @param string $blockNoResult
 * @param string $emptyIfNew
 * @param string $show_others
 * @param string[int] $sel_group_arr
 * @param string[int] $sel_role_arr
 * @param string $all_emp
 * @param string $field_name
 * @param string $field_code
 * @param boolean $return_val
 * 
 * @return string
 */
function get_partner_employee_list($tab_index=0,$emp_name=null,$emp_code=null,$blockNoResult=null,$emptyIfNew=null,
$show_others=null,$sel_group_arr=null,$sel_role_arr=null,$all_emp='off',
$field_name='emp_name',$field_code='emp_code',$return_val=false){
	if($blockNoResult){
		$blockNoResult='blockNoResultStatus: "1",';
	}else{
		$blockNoResult='';
	}
	
	if($emptyIfNew)	{
	  $emptyIfNew= 'doEmptyTargetIfNew: "1",';
	}else {
	   $emptyIfNew= '';
	}
	
	$exec_only="";
	if($show_others) $exec_only="&show_others=1";
    $select_group="";
	if($sel_group_arr!=null and is_array($sel_group_arr)){
		$select_group.="&select_group[]=".implode("&select_group[]=",$sel_group_arr);
	}else if($sel_group_arr!=null){
		$select_group.="&select_group[]=".$sel_group_arr;
	}
	$select_role='';
	if($sel_role_arr!=null and is_array($sel_role_arr)) 
		$select_role="&select_role[]=".implode('&select_role[]=',$sel_role_arr);
	elseif($sel_role_arr!=null){
		$select_role="&select_role[]=".$sel_role_arr;
	}	
	
$return_value=<<<END
<input id="$field_name" name="$field_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$emp_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")' />
<input id="$field_code" name="$field_code" type="hidden" value="$emp_code">
<script type="text/javascript">
new AjaxJspTag.Autocomplete(
"list_employee_name.php", {
minimumCharacters: "1",
parameters: "func=get_partner_emp&empname={{$field_name}}$exec_only&all_emp=$all_emp$select_group$select_role&custCode={customer_id}",
progressStyle: "throbbing",
target: "$field_code",
className: "autocomplete",
$blockNoResult $emptyIfNew
emptyFunction: empEmptyFunction,
postFunction: empDetails,
source: "$field_name" 
});

function empEmptyFunction(){ if($("$field_name").value==''){ $("$field_code").value="0";}}
</script>
END;

	if($return_val==true){
		return $return_value;
	}else{
		echo $return_value;
		return ''; 
	}
}


/**
 * @param int $tab_index
 * @param string $emp_name
 * @param string $emp_code
 * @param string $blockNoResult
 * @param string $emptyIfNew
 * @param string $show_others
 * @param string[int] $sel_group_arr
 * @param string[int] $sel_role_arr
 * @param string $all_emp
 * @param string $field_name
 * @param string $field_code
 * @param boolean $return_val
 * 
 * @return string
 */
function get_partner_employee_list_for_trainig($tab_index=0,$emp_name=null,$emp_code=null,$blockNoResult=null,$emptyIfNew=null,
$show_others=null,$sel_group_arr=null,$sel_role_arr=null,$all_emp='off',
$field_name='emp_name',$field_code='emp_code',$return_val=false){
	if($blockNoResult){
		$blockNoResult='blockNoResultStatus: "1",';
	}else{
		$blockNoResult='';
	}
	
	if($emptyIfNew)	{
	  $emptyIfNew= 'doEmptyTargetIfNew: "1",';
	}else {
	   $emptyIfNew= '';
	}
	
	$exec_only="";
	if($show_others) $exec_only="&show_others=1";
    $select_group="";
	if($sel_group_arr!=null and is_array($sel_group_arr)){
		$select_group.="&select_group[]=".implode("&select_group[]=",$sel_group_arr);
	}else if($sel_group_arr!=null){
		$select_group.="&select_group[]=".$sel_group_arr;
	}
	$select_role='';
	if($sel_role_arr!=null and is_array($sel_role_arr)) 
		$select_role="&select_role[]=".implode('&select_role[]=',$sel_role_arr);
	elseif($sel_role_arr!=null){
		$select_role="&select_role[]=".$sel_role_arr;
	}	
	
$return_value=<<<END
<input id="$field_name" name="$field_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$emp_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")' />
<input id="$field_code" name="$field_code" type="hidden" value="$emp_code">
<script type="text/javascript">
new AjaxJspTag.Autocomplete(
"list_employee_name.php", {
minimumCharacters: "1",
parameters: "func=get_partner_emp&empname={{$field_name}}$exec_only&all_emp=$all_emp$select_group$select_role&custCode={customer_id1}",
progressStyle: "throbbing",
target: "$field_code",
className: "autocomplete",
$blockNoResult $emptyIfNew
emptyFunction: empEmptyFunction,
postFunction: empDetails,
source: "$field_name" 
});

function empEmptyFunction(){ if($("$field_name").value==''){ $("$field_code").value="0";}}
</script>
END;

	if($return_val==true){
		return $return_value;
	}else{
		echo $return_value;
		return ''; 
	}
}

/**
 * @param int $tab_index
 * @param string $emp_name
 * @param string $emp_code
 * @param string $blockNoResult
 * @param string $emptyIfNew
 * @param string $show_others
 * 
 * @return void
 */
function get_ref_employee_list($tab_index=0,$emp_name=null,$emp_code=null,$blockNoResult=null,$emptyIfNew=null,$show_others=null){
	if($blockNoResult){
		$blockNoResult='blockNoResultStatus: "1",';
	}else{
		$blockNoResult='';
	}
	if($emptyIfNew)	{
	  $emptyIfNew= 'doEmptyTargetIfNew: "1",';
	}else {
	   $emptyIfNew= '';
	}
	$exec_only="";
	if($show_others) $exec_only="&show_others=1";

echo<<<END
<input id="remp_name" name="remp_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$emp_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")' />
<input id="remp_code" name="remp_code" type="hidden" value="$emp_code">
<script type="text/javascript">
new AjaxJspTag.Autocomplete(
"list_employee_name.php", {
minimumCharacters: "1",
parameters: "empname={remp_name}$exec_only&all_emp=on",
progressStyle: "throbbing",
target: "remp_code",
className: "autocomplete",
$blockNoResult $emptyIfNew
emptyFunction: rempEmptyFunction,
postFunction: rempDetails,
source: "remp_name" 
});
function rempEmptyFunction(){}
function rempDetails(){}
</script>
END;
}

/**
 * @param string $name
 * @param string $id
 * @param int $tab_index
 * @param string $emp_name
 * @param string $emp_code
 * @param string $sel_group_arr
 * 
 * @return string
 */
function get_employee_name_list($name,$id,$tab_index=0,$emp_name=null,$emp_code=null,$sel_group_arr=null)
{
	$select_group="";
	if($sel_group_arr!=null and is_array($sel_group_arr)){
		for($i=0;$i<count($sel_group_arr);$i++){
			$select_group.="&select_group[]=".$sel_group_arr[$i];
		}
	}else if($sel_group_arr!=null){
		$select_group.="&select_group[]=".$sel_group_arr;
	}
return <<<END
<input id="$name" name="$name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$emp_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")' onkeyUp="javascript: check_empty();" />
<input id="$id" name="$id" type="hidden" value=$emp_code>
<script type="text/javascript" >	
function check_empty(){
   	if(document.getElementById("$name").value==""){
   		document.getElementById("$id").value="";
   	}
}
new AjaxJspTag.Autocomplete(
"list_employee_name.php", {
minimumCharacters: "1",
parameters: "empname={{$name}}$select_group",
progressStyle: "throbbing",
target: "$id",
className: "autocomplete",
emptyFunction: empEmpty$name,
postFunction: post$name,
source: "$name" 
});
function empEmpty$name(){}
function post$name(){}
</script>
END;
}

/**
 * @param int $tab_index
 * @param string $emp_name
 * @param string $emp_code
 * @param string $sel_group_arr
 * @param string $uid
 * @param boolean $show_others
 * 
 * @return string
 */
function get_employee_list_reporting($tab_index=0,$emp_name=null,$emp_code=null,$sel_group_arr=null,$uid=null,$show_others=false){
	global $all_emp_value;
	$roleid=$_SESSION['roleid'];
	$exec_only="";
	if($show_others and is_authorized_group($uid,1)) {
		$exec_only="&show_others=1";
	}
	$select_group="";
	if($sel_group_arr!=null and is_array($sel_group_arr)){
		for($i=0;$i<count($sel_group_arr);$i++){
			$select_group.="&select_group[]=".$sel_group_arr[$i];
		}
	}else if($sel_group_arr!=null){
		$select_group.="&select_group[]=".$sel_group_arr;
	}
	
$return_value=<<<END
<input id="emp_name" name="emp_name" type="text" style="width:200px" tabindex="$tab_index" class="normal_autocomplete" 
value="$emp_name" style="height:20" onblur='setStyleClassName(this,"normal_autocomplete");check_empty();' 
onfocus='setStyleClassName(this,"focus_autocomplete");call_ajax_empdetail();' onkeydown="javascript:check_empty();" />
<input id="emp_code" name="emp_code" type="hidden" value="$emp_code">
<input id="roleid" name="roleid" type="hidden" value="$roleid">
<input id="user_id" name="user_id" type="hidden" value="$uid">
<input id="show_all" name="show_all" type="hidden" value="false">
<!--COMBINATION ON EMPLOYEE ID AND TERRITORY_ID  -->
<script type="text/javascript">
function check_empty(){
   	if(document.getElementById("emp_name").value==""){
   		document.getElementById("emp_code").value="";
   	}
}
/*
var all_emp_status='$all_emp_value';
function onCheckAllSelected(ob)
{
	if(ob.checked){
		all_emp_status='on';
	}else{
		all_emp_status='';
	}
}
*/
call_ajax_empdetail();

function call_ajax_empdetail(){
new AjaxJspTag.Autocomplete(
"list_employee_name.php", {
minimumCharacters: "1",
parameters: "empname={emp_name}$exec_only"+"&emp_code={emp_code}&user_id={user_id}&all_emp="+all_emp_status+"&roleid={roleid}&select_group=$select_group",
progressStyle: "throbbing",
target: "emp_code",
className: "autocomplete",
emptyFunction: empEmptyFunction,		
postFunction: empDetails,
source: "emp_name"
});
function empEmptyFunction(){}
	if($("emp_name").value=="") $("emp_code").value="0";
}
</script>
END;
return $return_value;
}


/**
 * @param int $tab_index
 * @param string $app_emp_name
 * @param string $app_emp_code
 * @param string $app_sel_group_arr
 * @param string $uid
 * @param boolean $pcs_show_others
 *
 * @return string
 */
function get_app_employee_list_reporting($tab_index=0,$app_emp_name=null,$app_emp_code=null,$app_sel_group_arr=null,$uid=null,$pcs_show_others=false){
	global $all_emp_value;
	$roleid=$_SESSION['roleid'];
	$exec_only="";
	if($pcs_show_others and is_authorized_group($uid,1)) {
		$exec_only="&show_others=1";
	}
	$select_group="";
	if($app_sel_group_arr!=null and is_array($app_sel_group_arr)){
		for($i=0;$i<count($app_sel_group_arr);$i++){
			$select_group.="&select_group[]=".$app_sel_group_arr[$i];
		}
	}else if($app_sel_group_arr!=null){
		$select_group.="&select_group[]=".$app_sel_group_arr;
	}

	$return_value=<<<END
<input id="app_emp_name" name="app_emp_name" type="text" style="width:200px" tabindex="$tab_index" class="normal_autocomplete"
value="$app_emp_name" style="height:20" onblur='setStyleClassName(this,"normal_autocomplete");check_empty();'
onfocus='setStyleClassName(this,"focus_autocomplete");call_ajax_empdetail();' onkeydown="javascript:check_empty();" />
<input id="app_emp_code" name="app_emp_code" type="hidden" value="$app_emp_code">
<input id="roleid" name="roleid" type="hidden" value="$roleid">
<input id="user_id" name="user_id" type="hidden" value="$uid">
<input id="show_all" name="show_all" type="hidden" value="false">
<!--COMBINATION ON EMPLOYEE ID AND TERRITORY_ID  -->
<script type="text/javascript">
function check_empty(){
   	if(document.getElementById("app_emp_name").value==""){
   		document.getElementById("app_emp_code").value="";
   	}
}
/*
var all_emp_status='$all_emp_value';
function onCheckAllSelected(ob)
{
	if(ob.checked){
		all_emp_status='on';
	}else{
		all_emp_status='';
	}
}
*/
call_ajax_empdetail();

function call_ajax_empdetail(){
new AjaxJspTag.Autocomplete(
"list_employee_name.php", {
minimumCharacters: "1",
parameters: "empname={app_emp_name}$exec_only"+"&emp_code={app_emp_code}&user_id={user_id}&all_emp="+all_emp_status+"&roleid={roleid}&select_group=$select_group",
progressStyle: "throbbing",
target: "app_emp_code",
className: "autocomplete",
emptyFunction: empEmptyFunction,
postFunction: empDetails,
source: "app_emp_name"
});
function empEmptyFunction(){}
	if($("app_emp_name").value=="") $("app_emp_code").value="0";
}
</script>
END;
	return $return_value;
}

/**
 * @param int $tab_index
 * @param string $emp_name
 * @param string $emp_code
 * @param string $group_id
 * @param string $uid
 * @param string $post_function
 * @param string $empty_function
 * 
 * @return void
 */
function get_employee_list_by_group($tab_index=0,$emp_name=null,$emp_code=null,$group_id=null,$uid=null,
$post_function=null,$empty_function=null){
	global $all_emp_value;
	$post_function_str='';
	if($post_function!=null){
		$post_function_str='postFunction: '.$post_function .',';
	}
	if($empty_function!=null){
		$post_function_str.="emptyFunction: $empty_function ,";
	}
	if(is_array($group_id)){
		$group_str="&group_id[]=".implode("&group_id[]=",$group_id);
	}else{
		$group_str="&group_id=$group_id";
	}
echo<<<END
<input id="emp_name" name="emp_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$emp_name" style="height:20" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete");' />
<input id="emp_code" name="emp_code" type="hidden" value="$emp_code">
<input id="user_id" name="user_id" type="hidden" value="$uid">
<input id="show_all" name="show_all" type="hidden" value="false">
<script type="text/javascript">
var all_emp_status='$all_emp_value';
function onCheckAllSelected(ob){
	if(ob.checked){
		all_emp_status='on';
	}else{
		all_emp_status='off';
	}
}
call_ajax_empdetail();
function call_ajax_empdetail(){
new AjaxJspTag.Autocomplete(
"list_employee_name_by_group.php", {
minimumCharacters: "1",
parameters: "empname={emp_name}"+"&emp_code="+document.getElementById('emp_code').value+"$group_str"+
"&user_id="+document.getElementById('user_id').value+"&all_emp="+all_emp_status,
progressStyle: "throbbing",
target: "emp_code",
className: "autocomplete",
$post_function_str
source: "emp_name" 
});
}
</script>
END;
}

/**
 * @param int $tab_index
 * @param string $emp_name
 * @param string $emp_code
 * @param string $group_id
 * @param string $uid
 * 
 * @return void
 */
function get_employee_list_by_group1($tab_index=0,$emp_name=null,$emp_code=null,$group_id=null,$uid=null){
	global $all_emp_value;
echo<<<END
<input id="emp_name" name="emp_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$emp_name" style="height:20" onblur='setStyleClassName(this,"normal_autocomplete")' 
onfocus='setStyleClassName(this,"focus_autocomplete");call_ajax_empdetail();' />
<input id="emp_code" name="emp_code" type="hidden" value="$emp_code">
<input id="user_id" name="user_id" type="hidden" value="$uid">
<input id="show_all" name="show_all" type="hidden" value="false">
<script type="text/javascript">
var all_emp_status='$all_emp_value';
function onCheckAllSelected(ob){
	if(ob.checked){
		all_emp_status='on';
	}else{
		all_emp_status='';
	}
}
call_ajax_empdetail();

function call_ajax_empdetail(){
new AjaxJspTag.Autocomplete(
"list_employee_name_by_group.php", {
minimumCharacters: "1",
parameters: "empname={emp_name}"+"&group_id="+document.getElementById('group_id').value+"&emp_code="+document.getElementById('emp_code').value+
"&user_id="+document.getElementById('user_id').value+"&all_emp="+all_emp_status,
progressStyle: "throbbing",
target: "emp_code",
className: "autocomplete",
postFunction: "empDetails",
source: "emp_name" 
});
}
</script>
END;
}

/* NOT_USED
 * @param string $tab_index
 * 
 * @return void
 *
function get_employee_list1($tab_index=null){
echo<<<END
<input id="jemp_name" name="jemp_name[1]" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$emp_name" style="height:20" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="jemp_code" name="jemp_code[1]" type="hidden">
<!--COMBINATION ON EMPLOYEE ID AND TERRITORY_ID  -->
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_employee_name.php", {
minimumCharacters: "1",
parameters: "empname={jemp_name}",
progressStyle: "throbbing",
target: "jemp_code",
className: "autocomplete",
source: "jemp_name" 
});
</script>
END;
}
*/

/**
 * @param int $tab_index
 * @param string $emp_email
 * 
 * @return void
 */
function get_employee_email($tab_index=0,$emp_email=null){
echo<<<END
<input id="email_to" name="email_to" type="text" size="50" tabindex="$tab_index" class="normal_autocomplete" 
value="$emp_email" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="emp_code" name="emp_code" type="hidden">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "purpose=list_email&emp_email={email_to}",
progressStyle: "throbbing",
target: "emp_code",
className: "autocomplete",
postFunction: update_mgr_details,
source: "email_to"
});
</script>
END;
}

/* NOT_USED
 * @param string $tab_index
 * @param string $name_suffix
 * @param string $post_function
 * 
 * @return string
 *
function get_employee_email_ajax($tab_index=null,$name_suffix=null,$post_function=null){
	if($post_function!=null){
		$post_function_call="postFunction:$post_function,";
	}
$filter_doc=<<<END
<input id="email_addr$name_suffix" name="email_addr$name_suffix" type="text" size="50" tabindex="$tab_index" class="normal_autocomplete" 
value="$emp_email" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="email_addr_target" name="email_addr_target" type="hidden">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "purpose=list_email&emp_email={email_addr$name_suffix}&accept_comma=true",
progressStyle: "throbbing",
target: "email_addr_target",
className: "autocomplete",
$post_function_call
source: "email_addr$name_suffix"
});
</script>
END;
	return $filter_doc;
}
*/

/**
 * @param string $terr_code
 * @param int $tab_index
 * @param string $terr_name
 * @param string $aid
 * 
 * @return void
 */
function get_territory_list_ajax($terr_code,$tab_index,$terr_name,$aid=null){
	$id_add='';
	$name_add='';
	if($aid!=''){ $id_add="$aid"; $name_add="[$aid]";}
echo<<<END
<input id="terr_name$id_add" name="terr_name$name_add" type="text" size="25" tabindex="$tab_index" class="normal_autocomplete" 
value="$terr_name" onblur='setStyleClassName(this,"normal_autocomplete")' 
onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="terrCode$id_add" name="terrCode$name_add" type="hidden">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "purpose=list_terrtory&terr_name={terr_name$id_add}",
progressStyle: "throbbing",
target: "terrCode$id_add",
className: "autocomplete",
postFunction: terr_details,
source: "terr_name$id_add"
});
</script>
END;
}

/**
 * @param string $area_code
 * @param int $tab_index
 * @param string $area_name
 * @param string $aid
 * 
 * @return void
 */
function get_area_list_ajax($area_code,$tab_index,$area_name,$aid=null){
	$id_add='';
	$name_add='';
	if($aid!=''){ $id_add="$aid"; $name_add="[$aid]";}
echo<<<END
<input id="area_name$id_add" name="area_name$name_add" type="text" size="25" tabindex="$tab_index" class="normal_autocomplete" 
value="$area_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="areaCode$id_add" name="areaCode$name_add" type="hidden">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "purpose=business_area&area_name={area_name$id_add}",
progressStyle: "throbbing",
target: "areaCode$id_add",
className: "autocomplete",
postFunction: area_details,
source: "area_name$id_add"
});
</script>
END;
}

/**
 * @param string $region_code
 * @param int $tab_index
 * @param string $region_name
 * @param string $aid
 * 
 * @return void
 */
function get_region_list_ajax($region_code,$tab_index,$region_name,$aid=null){
	$id_add='';
	$name_add='';
	if($aid!=''){ $id_add="$aid"; $name_add="[$aid]";}
echo<<<END
<input id="region_name$id_add" name="region_name$name_add" type="text" size="25" tabindex="$tab_index" class="normal_autocomplete" 
value="$region_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="regionCode$id_add" name="regionCode$name_add" type="hidden">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "purpose=list_region&region_name={region_name$id_add}",
progressStyle: "throbbing",
target: "regionCode$id_add",
className: "autocomplete",
postFunction: region_details,
source: "region_name$id_add"
});
</script>
END;
}

/**
 * @param string $zone_code
 * @param int $tab_index
 * @param string $zone_name
 * @param string $aid
 * 
 * @return void
 */
function get_zone_list_ajax($zone_code,$tab_index,$zone_name,$aid=null){
	$id_add='';
	$name_add='';
	if($aid!=''){ $id_add="$aid"; $name_add="[$aid]";}
echo<<<END
<input id="zone_name$id_add" name="zone_name$name_add" type="text" size="25" tabindex="$tab_index" class="normal_autocomplete" 
value="$zone_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="zoneCode$id_add" name="zoneCode$name_add" type="hidden">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "purpose=list_zone&zone_name={zone_name$id_add}",
progressStyle: "throbbing",
target: "zoneCode$id_add",
className: "autocomplete",
postFunction: zone_details,
source: "zone_name$id_add"
});
</script>
END;
}

/**
 * @param string $aid
 * @param string $post_function
 * 
 * @return void
 */
function get_group_list_ajax($aid=null,$post_function=''){
	$id_add='';
	$name_add='';
	$post_fuc='';
	if($aid!=''){ $id_add="$aid"; $name_add="[$aid]";}
	if($post_function!=''){$post_fuc="postFunction: $post_function,";}
echo<<<END
<input id="group_nm$id_add" name="group_nm$name_add" type="text" size="25" class="normal_autocomplete" 
value="" onblur='setStyleClassName(this,"normal_autocomplete")' 
onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="groupCode$id_add" name="groupCode$name_add" type="hidden">
<script type="text/javascript" >	
new AjaxJspTag.Autocomplete(
"list_groups.php", {
minimumCharacters: "1",
parameters: "group_nm={group_nm$id_add}",
progressStyle: "throbbing",
target: "groupCode$id_add",
className: "autocomplete",$post_fuc
source: "group_nm$id_add"
});
</script>
END;
}

/**
 * @param string $aid for siffix in field
 * @param int $group_code value to be set in code
 * @param string $group_name value to be set in select text field
 * 
 * @return string
 */
function get_cust_group_list_ajax($aid,$group_code,$group_name){
	$id_add='';
	$name_add='';
	if($aid!=''){ $id_add="$aid"; $name_add="[$aid]";}
$return_val=<<<END
	<input id="cust_group_name$id_add" name="cust_group_name$name_add" type="text" size="30" class="normal_autocomplete" 
	value="$group_name" onblur='setStyleClassName(this,"normal_autocomplete")' 
	onfocus='setStyleClassName(this,"focus_autocomplete")'/>
	<input id="cust_group_code$id_add" name="cust_group_code$name_add" type="hidden" value="$group_code">
	<script type="text/javascript" >	
	new AjaxJspTag.Autocomplete(
	"list_groups.php", {
	minimumCharacters: "1",
	parameters: "cust_group_name={cust_group_name$id_add}",
	progressStyle: "throbbing",
	target: "cust_group_code$id_add",
	className: "autocomplete",
	source: "cust_group_name$id_add"
	});
</script>
END;

	return $return_val;
}

/**
 * @param string $emp_id
 * @param string $emp_name
 * @param string $emp_code
 * 
 * @return void
 */
function get_collection_incharge($emp_id=null,$emp_name=null,$emp_code=null){
	$id_add='';
	$name_add='';
	$get_collection_incharge_mandatory=true;
	$sel_group_arr=/*. (string[int]) .*/ array();
	if($emp_id!=''){
 		if(is_authorized_group($emp_id,5,'') or is_authorized_group($emp_id,0,2) or is_authorized_group($emp_id,0,7) ){
	 		$get_collection_incharge_mandatory=false;
	 		$sel_group_arr=array('5','6');
	 	}
	}else {
 		$get_collection_incharge_mandatory=false;
 		$sel_group_arr=array('5','6');
 	}
echo<<<END
<script type="text/javascript">function empDetails(){}</script>
END;
	echo "<tr><td class=\"datalabel\" >Collection Incharge</TD>" .
 		"<td><input type=\"hidden\"  name=\"collection_incharge_mandatory\"  id=\"collection_incharge_mandatory\" value=\"$get_collection_incharge_mandatory\" >";
	get_employee_list($tab_index=null,$emp_name,$emp_code,/*$blockNoResult=*/null,/*$emptyIfNew=*/null,/*$show_others=*/null,$sel_group_arr,null,'off');
	echo "</td></tr>";
}

/**
 * @param int $tab_index
 * @param string $femp_name
 * @param string $femp_code
 * @param string $blockNoResult
 * @param string $emptyIfNew
 * @param string $show_others
 * @param string $sel_group_arr
 * @param string $sel_role_arr
 * @param string $all_emp
 * @param bool $get_role
 * @param string $emp_dtl
 * 
 * @return void
 */
function get_followup_employee_list($tab_index=0,$femp_name=null,$femp_code=null,$blockNoResult=null,$emptyIfNew=null,
$show_others=null,$sel_group_arr=null,$sel_role_arr=null,$all_emp='on',$get_role=false,$emp_dtl=null){
	if($blockNoResult){
		$blockNoResult='blockNoResultStatus: "1",';
	}else{
		$blockNoResult='';
	}
	
	if($emptyIfNew)	{
		$emptyIfNew= 'doEmptyTargetIfNew: "1",';
	}else {
	   $emptyIfNew= '';
	}
	$target = "target: 'femp_code',";
	$role_data = "";
	if($get_role) {
		$target = "target: 'emp_dtl',";
		$role_data = "&get_role=1";
	}
	$exec_only="";
	if($show_others) $exec_only="&show_others=1";
	$select_group="";
	if($sel_group_arr!=null and is_array($sel_group_arr)){
		for($i=0;$i<count($sel_group_arr);$i++){
			$select_group.="&select_group[]=".$sel_group_arr[$i];
		}
	}else if($sel_group_arr!=null){
		$select_group.="&select_group[]=".$sel_group_arr;
	}
	$select_role='';
	if($sel_role_arr!=null and is_array($sel_role_arr)) $select_role="&select_role=".implode(',',$sel_role_arr);
	$skip_pc_validation_ids = get_samee_const('SKIP_PC_VALIDATION');
echo<<<END
<input id="femp_name" name="femp_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$femp_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="femp_code" name="femp_code" type="hidden" value="$femp_code">
<input id="lead_owner_id" name="lead_owner_id" type="hidden" value="$femp_code">
<input id="lead_owner_role" name="lead_owner_role" type="hidden" value="$femp_code">
<input id="skip_pc_validation_ids" name="skip_pc_validation_ids" type="hidden" value="$skip_pc_validation_ids">
END;
$clear_on_data_load = "if(jq('#emp_dtl')) {jq('#emp_dtl').attr('value','');}";
if($get_role) {
	echo "<input type='hidden' id='emp_dtl' name='emp_dtl' value='$emp_dtl'>";
	$clear_on_data_load = "jq('#femp_code').attr('value','');";
}
echo<<<END
<!--COMBINATION ON EMPLOYEE ID AND TERRITORY_ID  -->
<script type="text/javascript">
new AjaxJspTag.Autocomplete(
"list_employee_name.php", {
minimumCharacters: "1",
parameters: "empname={femp_name}&user_id={user_id}$exec_only&all_emp=$all_emp$select_group$select_role$role_data",
progressStyle: "throbbing",
$target
className: "autocomplete",
$blockNoResult $emptyIfNew
emptyFunction: empEmptyFunction,
postFunction: empDetailsUpdate,
source: "femp_name" 
});
function empEmptyFunction(){}
function empDetailsUpdate(){
	var jq = jQuery.noConflict();
	$clear_on_data_load
}
</script>
END;
}

/**
 * @param int $tab_index
 * @param string $lfd_emp_name
 * @param string $lfd_emp_code
 * @param string $blockNoResult
 * @param string $emptyIfNew
 * @param string $show_others
 * @param string $sel_group_arr
 * @param string $sel_role_arr
 * @param string $all_emp
 * 
 * @return void
 */
function get_followup_employee_list_edit($tab_index=0,$lfd_emp_name=null,$lfd_emp_code=null,$blockNoResult=null,$emptyIfNew=null,
$show_others=null,$sel_group_arr=null,$sel_role_arr=null,$all_emp='on'){
	if($blockNoResult){
		$blockNoResult='blockNoResultStatus: "1",';
	}else{
		$blockNoResult='';
	}
	
	if($emptyIfNew)	{
		$emptyIfNew= 'doEmptyTargetIfNew: "1",';
	}else {
	   $emptyIfNew= '';
	}
	$exec_only="";
	if($show_others) $exec_only="&show_others=1";
	$select_group="";
	if($sel_group_arr!=null and is_array($sel_group_arr)){
		for($i=0;$i<count($sel_group_arr);$i++){
			$select_group.="&select_group[]=".$sel_group_arr[$i];
		}
	}else if($sel_group_arr!=null){
		$select_group.="&select_group[]=".$sel_group_arr;
	}
	$select_role='';
	if($sel_role_arr!=null and is_array($sel_role_arr)) $select_role="&select_role=".implode(',',$sel_role_arr);
echo<<<END
<input id="lfd_emp_name" name="lfd_emp_name" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" 
value="$lfd_emp_name" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")' />
<input id="lfd_emp_code" name="lfd_emp_code" type="hidden" value="$lfd_emp_code">
<!--COMBINATION ON EMPLOYEE ID AND TERRITORY_ID  -->
<script type="text/javascript">
new AjaxJspTag.Autocomplete(
"list_employee_name.php", {
minimumCharacters: "1",
parameters: "empname={lfd_emp_name}&user_id={user_id}$exec_only&all_emp=$all_emp$select_group$select_role",
progressStyle: "throbbing",
target: "lfd_emp_code",
className: "autocomplete",
$blockNoResult $emptyIfNew
emptyFunction: empEmptyFunction,
postFunction: empDetails,
source: "lfd_emp_name" 
});
function empEmptyFunction(){}
</script>
END;
}

/**
 * @param string $femp_name
 * @param string $femp_code
 * 
 * @return void
 */
function get_followup_incharge($femp_name=null,$femp_code=null){
	$get_collection_incharge_mandatory=true;
	$lfd_emp_code='';

	/*
	 if($emp_id!=''){

	if(is_authorized_group($emp_id,5,'') or is_authorized_group($emp_id,'',2) or is_authorized_group($emp_id,'',7) ){
	$get_collection_incharge_mandatory=false;
	$sel_group_arr=array(5,7);
	$sel_role_arr=array(2);
	}

	}
	else {
	$get_collection_incharge_mandatory=false;
	$sel_group_arr=array(5,7);
	$sel_role_arr=array(2);
	}
	*/
	$sel_group_arr=array(5);
	echo<<<END
<script type="text/javascript"> function empDetails(){}</script>
END;

	echo "<tr><td class=\"datalabel\" > Followup By </TD>" .
	"<td><input type=\"hidden\"  name=\"collection_incharge_mandatory\"   id=\"collection_incharge_mandatory\" value=\"$get_collection_incharge_mandatory\" >" .
	"<input type=\"hidden\" name=\"prev_lfd_emp_code\" id=\"prev_lfd_emp_code\" value=\"$lfd_emp_code\" >	";

	get_followup_employee_list_edit($tab_index=0,$femp_name,$femp_code,$blockNoResult=null,$emptyIfNew=null,
	$show_others=null,$sel_group_arr,$sel_role_arr=null,'off');
	echo "</td></tr>";
}

/**
 * @param string $POST_FUNCTION
 * @param string $mcategory_code
 * @param string $mcategory_name
 * 
 * @return string
 */
function mail_category_ajax($POST_FUNCTION=null,$mcategory_code=null,$mcategory_name=null){
	$make_it_readony='';
	$call_post_function='';
	$POST_FUNCTION_DTL='';
	if($POST_FUNCTION!=null){ 	
		$POST_FUNCTION_DTL="postFunction: ".$POST_FUNCTION.",";
	}
	if($mcategory_code!=null and $POST_FUNCTION!=null){
		$make_it_readony=" readonly=true ";
		$call_post_function="$POST_FUNCTION();";
	}	
$array_of_filter_elements=<<<END
<input id="mcategory_name" name="mcategory_name" type="text" style='width:150'  class="normal_autocomplete" 
value="$mcategory_name" style="height:20" onblur='setStyleClassName(this,"normal_autocomplete");' 
onfocus='setStyleClassName(this,"focus_autocomplete");' $make_it_readony /> 
<input id="mcategory_code" name="mcategory_code" type="hidden" value="$mcategory_code">
<script type="text/javascript">
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "mcategory_name={mcategory_name}&user_id={emp_id}&purpose=list_mail_category" ,
progressStyle: "throbbing",
target: "mcategory_code",
className: "autocomplete",
$POST_FUNCTION_DTL
source: "mcategory_name"
});
$call_post_function
</script>
END;
	return $array_of_filter_elements;	
}

/**
 * @param string $POST_FUNCTION
 * @param string $mcategory_code
 * @param string $mcategory_name
 * 
 * @return string
 */
function document_category_ajax($POST_FUNCTION=null,$mcategory_code=null,$mcategory_name=null){
	$make_it_readony='';
	$call_post_function='';
	$POST_FUNCTION_DTL='';
	if($POST_FUNCTION!=null){ 	
		$POST_FUNCTION_DTL="postFunction: ".$POST_FUNCTION.",";
	}
	if($mcategory_code!=null and $POST_FUNCTION!=null){
		$make_it_readony=" readonly=true ";
		$call_post_function="$POST_FUNCTION();";
	}	
$array_of_filter_elements=<<<END
<input id="mcategory_name" name="mcategory_name" type="text" style='width:150'  class="normal_autocomplete" 
value="$mcategory_name" style="height:20" onblur='setStyleClassName(this,"normal_autocomplete");' 
onfocus='setStyleClassName(this,"focus_autocomplete");' $make_it_readony /> 
<input id="mcategory_code" name="mcategory_code" type="hidden" value="$mcategory_code">
<script type="text/javascript">
new AjaxJspTag.Autocomplete(
"list_common.php", {
minimumCharacters: "1",
parameters: "mcategory_name={mcategory_name}&user_id={emp_id}&purpose=list_document_category" ,
progressStyle: "throbbing",
target: "mcategory_code",
className: "autocomplete",
$POST_FUNCTION_DTL
source: "mcategory_name"
});
$call_post_function
</script>
END;
	return $array_of_filter_elements;	
}

/**
 * @param string $cname
 * 
 * @return void
 */
function getcustnameajx($cname){
	global $uid;

	$uid1=$uid;
echo<<<END
<input id="cname" name="cname" type="text" size="30" class="normal_autocomplete" value="$cname"
onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input id="custCode" name="custCode" type="hidden">
<script type="text/javascript">
new AjaxJspTag.Autocomplete(
"list_customername.php", {
minimumCharacters: "1",
parameters: "customername={cname}&user_id=$uid1",
progressStyle: "throbbing",
target: "custCode",
className: "autocomplete",
source: "cname"
});
</script>
END;
}

/**
 * @param string $field_name
 * @param string $field_code_name
 * @param string $field_value
 * @param string $field_code_value
 * 
 * @return void
 */
function list_city_from_pincode($field_name="loc_name",$field_code_name="loc_code",$field_value=null,$field_code_value=null){
echo<<<END
		   <input type="text" name="$field_name" id="$field_name" class="normal_autocomplete" 
				onblur='setStyleClassName(this,"normal_autocomplete")' value="$field_value" 
				onfocus='setStyleClassName(this,"focus_autocomplete")'/>

		   <input type="hidden" name="$field_code_name" id="$field_code_name" value="$field_code_value">
<script type="text/javascript">		    
  new AjaxJspTag.Autocomplete(
"list_loc.php", {
minimumCharacters: "1",
parameters: "loc_name={$field_name}",
progressStyle: "throbbing",
target: "$field_code_name",
className: "autocomplete",
source: "$field_name" 
});
</script>     
END;
}

/**
 * @param string[] $act_group_arr
 * 
 * @return string[int][int]
 */
function list_activity_of_group($act_group_arr){
	if(is_array($act_group_arr)){
		$act_group_arr=implode(',',$act_group_arr);
	}
	$query="select distinct(GAM_ACTIVITY_ID),GAM_ACTIVITY_DESC from gft_activity_master,gft_activity_group_map where 
	GAG_ACTIVITY_ID=GAM_ACTIVITY_ID and GAM_ACTIVITY_STATUS='A' and GAG_GROUP_ID IN($act_group_arr) ";
	$result=execute_my_query($query);
	$i=0;	$act_list=/*. (string[int][int]) .*/ array();
	while($qd=mysqli_fetch_array($result)){
		$act_list[$i][0]=$qd['GAM_ACTIVITY_ID'];
		$act_list[$i++][1]=$qd['GAM_ACTIVITY_DESC'];
	}
	return $act_list;
}
?>
