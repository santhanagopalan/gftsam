<?php

require_once(__DIR__ ."/dbcon.php");

/* Moved th $cp_type, $cp_user_id init to auth_util.php */

$nextpage=(isset($_GET['next_page'])?(string)$_GET['next_page']:'');
if(!is_authorized_group_list((string)$_SESSION['uid'],$non_employee_group)){
	$me=$_SERVER['PHP_SELF'];
	$cp_names=(isset($_REQUEST['cp_name1'])?(string)$_REQUEST['cp_name1']:'');
	$cp_lcode1=trim((isset($_REQUEST['lcode1'])?(string)$_REQUEST['lcode1']:''));
	$from_dt=(isset($_REQUEST['from_dt'])?(string)$_REQUEST['from_dt']:'');
	$to_dt=(isset($_REQUEST['to_dt'])?(string)$_REQUEST['to_dt']:'');
	$lcodear=explode('-',$cp_lcode1);
	$cp_user_id=$lcodear[0];
	$pc_name	=	"";
	$pc_id		=	"";
	if(isset($_REQUEST['lcode1']) and $lcodear[1]!=''){
                $query_incharge="SELECT cgi_incharge_emp_id,cgi_pcs_incharge_emp_id, gem_emp_name FROM gft_cp_info ".
                				" inner join gft_emp_master on(gem_emp_id=cgi_pcs_incharge_emp_id) WHERE CGI_LEAD_CODE=".$lcodear[1];
		$result=execute_my_query($query_incharge);

		if($datar=mysqli_fetch_array($result)){
			$cgi_incharge_emp_id=$datar['cgi_incharge_emp_id'];	
			$cgi_pcs_incharge_emp_id=$datar['cgi_pcs_incharge_emp_id'];
			$pc_name	=	$datar['gem_emp_name'];
			$pc_id		=	$datar['cgi_pcs_incharge_emp_id'];
		}
	}
	$cps_status=(isset($_REQUEST['cps_status'])?(string)$_REQUEST['cps_status']:'');
	$usrid=$emp_id=(string)$_SESSION['uid'];
	//print "--------------- cp_lcode1: $cp_lcode1 -------------";
	$tab_index=1;
echo<<<END
<form name="cp_admin_form" method="post" action="$me" onsubmit="return false;">
<table align="center" width="100%" class="solid_border">
<tr><td colspan="2">&nbsp;</td>
</tr><tr><td class="head_blue">Channel Partner /<br> Corporate Customer</td>
<td><input id="cp_name1" name="cp_name1" type="text" size="30" tabindex="$tab_index" class="normal_autocomplete" value="$cp_names" onblur='setStyleClassName(this,"normal_autocomplete")' onfocus='setStyleClassName(this,"focus_autocomplete")'/>
<input type="hidden" id="lcode1" name="lcode1" value="$cp_lcode1">
<input type="hidden" id="gft_partner" name="gft_partner" value="$gft_partner"></td>
END;
	$role_selection="";
	if(basename($me)=='cp_bussiness_summary.php'){
echo<<<END
<TD class="head_blue">From&nbsp;<td><input name="from_dt" type="text" class="formStyleTextarea" id="from_dt" value="$from_dt" size="14"  readonly>&nbsp;
<a href="javascript:function d(){}" id="onceDateIcon2" tabindex="2"><img alt="" src="images/date_time.gif" class="imagecur"  width="16" height="16" border="0" align="middle"></a></TD>
<TD class="head_blue" >To&nbsp;<td><input name="to_dt" type="text"  class="formStyleTextarea" id="to_dt" value="$to_dt" size="14" readonly>&nbsp;
<a href="javascript:function d(){}" id="onceDateIcon3" tabindex="3"><img alt="" src="images/date_time.gif" class="imagecur"  width="16" height="16" border="0" align="middle"></a>
<script type="text/javascript">
init_date_func("from_dt","%Y-%m-%d","onceDateIcon2","Bl");
init_date_func("to_dt","%Y-%m-%d","onceDateIcon3","Bl");
</script>
</TD><td>Status<td><select name="cps_status" id="cps_status" class="formStyleTextarea">
END;
		$cps_status_array=array('All',"Active","InActive");
		for($k=0;$k<count($cps_status_array);$k++){
			$selected=($cps_status==$k ? "selected" : "");
			print "<option value=$k $selected >{$cps_status_array[$k]} </option>";
		}
echo<<<END
</td><td><input type="button" name="showCP" tabindex="4" class="button" value="SHOW" onclick="javascript:checkCPField()"></td>
END;
	}
	if(basename($me)=="pcs_schedule.php"){
		$role_selection="&role=22";
	}
echo<<<END
</tr></TABLE>
<script type="text/javascript">
new AjaxJspTag.Autocomplete(
"list_cp_name.php", {
minimumCharacters: "1",
parameters: "cp_name={cp_name1}$role_selection&emp_id=$emp_id",
progressStyle: "throbbing",
target: "lcode1",
className: "autocomplete",
emptyFunction: emptyValues,
postFunction: checkCPField ,
source: "cp_name1"
});

function emptyValues(){
	document.cp_admin_form.lcode1.value='';
   	document.cp_admin_form.cp_name1.value='';
}
function checkCPField(){
	if(document.cp_admin_form.lcode1.value=='' || document.cp_admin_form.lcode1.value=='.' || document.cp_admin_form.cp_name1.value==''){
   		document.cp_admin_form.lcode1.value='';
   		document.cp_admin_form.cp_name1.value='';
   		return false;
 	}else{
 		document.cp_admin_form.onsubmit="";
 		document.cp_admin_form.submit();
 	}
}
</script></FORM>
END;
$cp_lcode = '';
	if(isset($_REQUEST['lcode1'])){
		$temp=explode('-',(string)$_REQUEST['lcode1']);
		$cp_user_id=$temp[0];
		$cp_lcode =$temp[1];
		$cp_roleid=$temp[2];
		$roleid1=$temp[2];
		
	}else if(basename($me)!='cp_bussiness_summary.php'){
		require_once(__DIR__ ."/footer.php");
		exit;
	}
}
if($cp_lcode!='') {
	if(basename($me)=='cp_order_details.php') {
		$close_window = false;
		if((int)$uid>7000) {
			$close_window = true;
		}
		if($global_gst_mode==1 and !validate_state_code_gstin($cp_lcode,$close_window,false,'',true)) {
			echo<<<END
			<script type="text/javascript">
			document.cp_admin_form.lcode1.value='';
			document.cp_admin_form.cp_name1.value='';
			</script>
END;
			exit;
		}
	}
}
if($cp_user_id!=''){
    $show_gstin_validation = false;
	$cp_type_query=" SELECT gca_cp_agreeno,GCA_CP_TYPE,CGI_PARTNER_OF_PARTNER,gle_gst_eligible,gle_gst_no,glh_country " .
			" FROM gft_leadcode_emp_map join gft_cp_info on (GLEM_LEADCODE=CGI_lead_code) " .
			" join gft_cp_agree_dtl on (CGI_lead_code=gca_lead_code AND CGI_CP_AGREENO=gca_cp_agreeno)" .
			" join gft_cp_relation on GCR_LEAD_CODE=CGI_lead_code and GCR_CP_LEVEL=1 ".
			" join gft_lead_hdr_ext on (cgi_lead_code=gle_lead_code) ".
			" join gft_lead_hdr on (gle_lead_code=glh_lead_code) ".
			" where GLEM_EMP_ID=$cp_user_id  ";
	$cp_type_result=execute_my_query($cp_type_query);
	if($data=mysqli_fetch_array($cp_type_result)) {
		$cp_type=$data['GCA_CP_TYPE'];	
		$gst_status = $data['gle_gst_eligible'];
		$gstin = $data['gle_gst_no'];
		if(strcasecmp($data['glh_country'], 'India')==0) {
    		if(!in_array($gst_status,array('1','2'))) {
    		    $show_gstin_validation = true;
    		} else if($gst_status=='1' and $gstin=='') {
    		    $show_gstin_validation = true;
    		}
		}
	}
	if($cp_type=='' or $cp_type==null) {
	    $cp_type='12';
	}
	if(basename($me)=='cp_order_details.php') {
    	if($show_gstin_validation) {
    	    $gst_validation_msg = "Please update the GST details of the partner from Customer GSTIN Report to proceed to Order Creation";
    	    if(is_authorized_group_list((string)$_SESSION['uid'],$non_employee_group)) {
    	        $gst_validation_msg = "Please update your GST details using \"GST Updates\" menu in myDelight app to proceed to Order Creation";
    	    }
    	    echo <<<END
<div style="width: 90%;margin: auto;text-align: center;font-size: 16px;font-weight: bold;color: orangered;">
$gst_validation_msg
</div>
END;
    	    exit;
    	}
	}
}
?>
