<?php
require_once(__DIR__ ."/common_util.php");
require_once (__DIR__ ."/common_filter.php");
/**
 * @param int $audit_id
 * @param string[string] $audit_hdr
 * @param int[int] $qid
 * @param string[int] $qidans
 * @param string[int] $bq_id
 * 
 * @return void
 */
function update_audit_question_dtl($audit_id,$audit_hdr,$qid,$qidans,$bq_id=null){
	global $install_id;
	$insert_val	=	"";
	$install_q_update=/*. (string[int]) .*/ array();
	foreach($qid as $key=> $value){
		if(isset($qidans[$value]) && is_array($qidans[$value])){
			$qidans[$value]=implode(',',$qidans[$value]);
		}
		if(!isset($bq_id[$key])){
			$bq_id[$key]='';
		}
		if(isset($qidans[$value]) && (trim($qidans[$value])!='' || $bq_id[$key]!='')){
			/* $query_audit_dtl="insert into gft_audit_dtl(GAD_AUDIT_ID,GAD_AUDIT_QID,GAD_AUDIT_ANS,GAD_REMARKS)" .
			 "values ($audit_id,$value,'".mysqli_real_escape_string_wrapper($qidans[$value])."','".$bq_id[$key]."')"; */
			$insert_val	.=	($insert_val!=''?', ':'')."($audit_id,$value,'".mysqli_real_escape_string_wrapper($qidans[$value])."','".$bq_id[$key]."')";
			if($value==1){
				$install_q_update[]=" GID_READY_TO_PAY_ASS='".($qidans[$value]=='Yes'?'Y':'N')."'";
			}
			if($value==2){
				$install_q_update[]=" gid_upsell='".($qidans[$value]=='Yes'?'Y':'N')."'";
			}
			if($value==3){
				$install_q_update[]=" gid_upgrade='".($qidans[$value]=='Yes'?'Y':'N')."'";
			}
			//execute_my_query($query_audit_dtl);
		}
	}
	if($insert_val!=''){
		execute_my_query("insert into gft_audit_dtl(GAD_AUDIT_ID,GAD_AUDIT_QID,GAD_AUDIT_ANS,GAD_REMARKS) values $insert_val");
	}
	if($install_q_update!=null){
		$install_q_update_query="update gft_install_dtl_new set ".implode(',',$install_q_update) ." where gid_lead_code=".$audit_hdr['GAH_LEAD_CODE']." ".($install_id!=''? " and GID_INSTALL_ID=$install_id ":"");
		execute_my_query($install_q_update_query);
	}
}
/**
 * @param string[string] $audit_hdr
 * @param int[int] $qid
 * @param string[int] $qidans
 * @param string[int] $bq_id
 * 
 * @return int
 */
function update_audit_details($audit_hdr,$qid,$qidans,$bq_id=null){
	//global $install_id;
	$audit_hdr['GAH_LAST_AUDIT']='Y';
	$imp_condition='';
	$non_pd_audit_type = array(16,7,9,6,8,41,14);//Non PD related audit list
	$pd_audit_type = array(15,17,19,21,18,20,22,23,24,25,26,38,43,63);//PD related audit list
	if(isset($audit_hdr['GAH_OPCODE']) and ( in_array($audit_hdr['GAH_AUDIT_TYPE'], $pd_audit_type))){ 
		$imp_condition=" and (GAH_OPCODE='".$audit_hdr['GAH_OPCODE']."') ";
	}
	if(isset($_REQUEST['order_catagory'][0]) && $_REQUEST['order_catagory'][0] == 12){ //checking complementary coupon
		if(isset($audit_hdr['GAH_REFFERNCE_ORDER_NO'])){
			$imp_condition=" and (GAH_OPCODE='".$audit_hdr['GAH_REFFERNCE_ORDER_NO']."') ";
		}
	}
	if(in_array($audit_hdr['GAH_AUDIT_TYPE'], $non_pd_audit_type)){
		$column_name2='';
		$values2	="";
		$inc	=	0;
		foreach($audit_hdr as $key2 => $value2){
			$column_name2	.=	($inc!=0?",":"")."$key2";
			$values2.=($inc!=0?",":"")."'$value2'";
			$inc++;
		}
		$query_audit_hdr="insert into gft_audit_hdr($column_name2) values ($values2)";
		execute_my_query($query_audit_hdr);
		$audit_id=mysqli_insert_id_wrapper();
		update_audit_question_dtl($audit_id,$audit_hdr,$qid,$qidans,$bq_id);
		return  $audit_id;
	}
	$result_select=execute_my_query("select * from gft_audit_hdr where GAH_LEAD_CODE=".$audit_hdr['GAH_LEAD_CODE']." and GAH_LAST_AUDIT='Y' $imp_condition");
	$columns=feach_data_header($result_select);
	if($data_hdr=mysqli_fetch_array($result_select)){
		for($i=0;$i<count($columns);$i++){
			if(!isset($audit_hdr[$columns[$i]]) and $columns[$i]!='GAH_AUDIT_ID'){
				$audit_hdr[$columns[$i]]=$data_hdr[$columns[$i]];
			}
		}
	}
	if($audit_hdr['GAH_AUDIT_TYPE']!=20){
	execute_my_query("update gft_audit_hdr set GAH_LAST_AUDIT='N' where GAH_LEAD_CODE=".$audit_hdr['GAH_LEAD_CODE']." $imp_condition ");
	}
	/* if(!empty($audit_hdr['GAH_REFFERNCE_ORDER_NO']) && $audit_hdr['GAH_TRAINING_STATUS'] == 0){
		$result_select=execute_my_query("select gah_audit_id,GAH_MILESTONE_STATUS,GAH_TRAINING_STATUS from gft_audit_hdr where GAH_LEAD_CODE=".$audit_hdr['GAH_LEAD_CODE']." and GAH_LAST_AUDIT='Y' and GAH_OPCODE='".$audit_hdr['GAH_REFFERNCE_ORDER_NO']."'");
		if(mysqli_num_rows($result_select)>0){
			$update_sql = "UPDATE gft_audit_hdr set GAH_TRAINING_STATUS=".$audit_hdr['GAH_TRAINING_STATUS']." " .
			",GAH_MILESTONE_STATUS='' , GAH_RC_APPROVAL_STATUS='',GAH_CM_APPROVAL_STATUS='' " .
			"WHERE gah_audit_id = '".mysqli_result($result_select,0,'gah_audit_id')."' and GAH_REFFERNCE_ORDER_NO='".$audit_hdr['GAH_REFFERNCE_ORDER_NO']."' AND GAH_LEAD_CODE=".$audit_hdr['GAH_LEAD_CODE']."";
			//execute_my_query($update_sql);
		}
	} */
	$i=0;$column_name='';$values='';
	foreach($audit_hdr as $key1 => $value1){
		$column_name.=($i!=0?",":"")."$key1";
		$values.=($i!=0?",":"")."'$value1'";
		$i++;		
		
	}
	$query_audit_hdr="insert into gft_audit_hdr($column_name) values ($values)"; 
	execute_my_query($query_audit_hdr);
	$audit_id=mysqli_insert_id_wrapper();	
	if(is_null($qid)) return $audit_id;
	update_audit_question_dtl($audit_id,$audit_hdr,$qid,$qidans,$bq_id);
	return $audit_id;
}
/**
 * @param string $audittypeid
 * @param string $product_code
 * @param string $vertical_code
 * @param string $version_code
 * @param string $group_id
 * @param int $delivery_type
 * @param string $skip_bq_audit
 *
 * @return string
 */
function get_audit_questions_query($audittypeid,$product_code='',$vertical_code='',$version_code='',$group_id='',$delivery_type=0,$skip_bq_audit=""){
	if($delivery_type==0){$delivery_type=1;}
	$query="select GAQ_GROUP_NAME,GAQ_QUESTION_ID,GAQ_QUESTION_TYPE,GAQ_AVAL_ANSWER,GAQ_INPUT_TYPE, ".
			" GAQ_ANS_MANDITORY,qm.GAQ_GROUP_ID,GAQ_SAT_RASULT_TAG,GAQ_TAB_NAME,GAQ_SHOW_LABEL,GAQ_SHOW_TAG " .
			" from gft_audit_question_master qm " .
			" left join gft_audit_question_group_master gm on (gm.GAQ_GROUP_ID=qm.GAQ_GROUP_ID)" .
			" left join gft_audit_question_group_map_master gmm on (gmm.GAQ_AUDIT_ID=GAQ_AUDIT_TYPE and GAQ_QGROUP_ID=gm.GAQ_GROUP_ID) " .
			" where qm.GAQ_STATUS='A' " .
			" and GAQ_PRODUCT_CODE in ('0' ".($product_code!=0 ? ",'$product_code'":'') .") " .
			" and GAQ_VERTICAL_CODE in (0".($vertical_code!=0 ? ",$vertical_code":'')." ) " .
			" and GAQ_PRODUCT_VERSION in (0".($version_code!=0 ? ",$version_code":'')." ) " .
			" and GAQ_DELIVERY_TYPE in (0".($delivery_type!=0 ? ",$delivery_type":'')." ) " .
			($group_id!=''?" AND qm.GAQ_GROUP_ID in ($group_id) ":"").
			($skip_bq_audit!=''?" AND qm.GAQ_QUESTION_ID NOT IN ($skip_bq_audit) ":"").
			($audittypeid!='' ?" and GAQ_AUDIT_TYPE in ($audittypeid) ":"")." order by GAQ_QORDER_BY,GAQ_ORDER_BY ";
	return $query;
}
/**
 * @param string $audittypeid
 * @param string $product_code
 * @param string $vertical_code
 * @param string $type_of_call
 * @param string $version_code
 * @param string $group_id
 * @param int $delivery_type
 * @param boolean $for_hr
 * @param string $dtl_qry
 * 
 * @return void
 */
function audit_questions_ui($audittypeid,$product_code='',$vertical_code='',$type_of_call='',$version_code='',$group_id='',$delivery_type=1,$for_hr=false,$dtl_qry=''){
	//global $uid,$rc_incharge;
	$comment_line1="";
	$comment_line2="";
	$pc_validation=1;
	if(!empty($vertical_code)){
	$sql = "SELECT GAQ_VERTICAL_CODE FROM gft_audit_question_master WHERE GAQ_STATUS='A' AND GAQ_AUDIT_TYPE in ($audittypeid)
			 AND GAQ_VERTICAL_CODE = $vertical_code";
			 $exe = execute_my_query($sql);
			 $counts = mysqli_num_rows($exe);
			 if($counts == 0){
			 	$vertical_code = "20";
			 }
	}else{
		$vertical_code = "20";
	}	
	$query	=	get_audit_questions_query($audittypeid,$product_code,$vertical_code,$version_code,$group_id,$delivery_type);
	$result=execute_my_query($query);
	$sl=1;$group_name='';
if(isset($_REQUEST['order_catagory'])){
	//if GFT Order
}else{
global	$cp_lcode,$pc_id,$pc_name;
if($cp_lcode!=''){
$cust_dtl=customerContactDetail($cp_lcode);
if(count($cust_dtl)!=0){
$cust_name	=	$cust_dtl['cust_name'];
$sql_partner_type=	"SELECT GLH_LEAD_SUBTYPE FROM gft_lead_hdr WHERE GLH_LEAD_CODE=$cp_lcode";
$row_partner_type	=	mysqli_fetch_array(execute_my_query($sql_partner_type));
if(!isset($_REQUEST['audittypeid'])){
if(!isset($_REQUEST['call_from']))
{
	$expense_query=	"SELECT GET_ID, GET_NAME FROM gft_expense_type_master WHERE GET_STATUS='A' AND GET_FOR_PD='Y'";
	$expense_type_list=get_two_dimensinal_array_from_query($expense_query,'GET_ID','GET_NAME','GET_ID');
	$pc_validation=0;
	echo<<<END
	<tr><td class="head_blue" nowrap><span style="color:red;">*</span>Product Delivery by</td><td>
	<input class='pd_by' type='hidden' name='deliver_by_id' id='deliver_by_id' value='58' >
END;
	if($row_partner_type['GLH_LEAD_SUBTYPE']==7){
		echo<<<END
		<input class='pd_by' id='pd_by3' type='radio' name='deliver_by' value='3'>$cust_name
END;
	}
	echo<<<END
	<input class='pd_by' id='pd_by1' type='radio' name='deliver_by' value='1' >GoFrugal
	<input class='pd_by' id='pd_by2' type='radio' name='deliver_by' value='2'>GoFrugal Partner
	</td></tr>
	<tr>
	<td class="head_blue" nowrap><span class='hide' id='expense_div'><span style="color:red;">*</span>Customer Location for PD</span></td>
	<td>
END;
	echo fix_combobox_with("pd_expense_type","pd_expense_type",$expense_type_list,0,null,"Select",null,false,'', 1,null,'hide');
	echo "</td></tr>";
	echo "<tr><td class='head_blue' nowrap><div id='ajax_pc_list' style='display: none;'><span style='color:red;'>*</span>Delivery Owner</div></td><td>";
	echo "<div id='ajax_partner_list' style='display: none;'>";
	get_partner_employee_list(null,null,null,null,null,null,array(36),null,'off','partner_emp_name','partner_emp_code');
	echo "</div><div id='ajax_gft_list' style='display: none;'>";
	get_employee_list_pcs(null,"$pc_name","$pc_id",null,null,null,array(70),null,'off','product_consultant_name','product_consultant');
	echo "</div><td></tr>";
}
}
}
	}
}
$ans = array();
if($dtl_qry!='' and $for_hr) {
	$dtl_res = execute_my_query($dtl_qry);
	while($row = mysqli_fetch_array($dtl_res)) {
		$ans[$row['GCD_QUESTION_ID']] = $row['GCD_RESPONSE'];
	}
}
	while($data=mysqli_fetch_array($result)){
		if($data['GAQ_GROUP_NAME']!=$group_name){		
			$group_name=$data['GAQ_GROUP_NAME'];
if($type_of_call==''){			
echo<<<END
<tr class='modulelisttitle'><td colspan='2' class="sub_head_white">$group_name</td>
END;
}
if($type_of_call=='iframe' and $group_name=='Hardware Assessment'){
	break;
}
if($type_of_call=='' and $audittypeid==15){
	$comment_line1="/*";
	$comment_line2="*/";
}
}
		$manditory_mark=($data['GAQ_ANS_MANDITORY']=='Y'?'<font color=red size=4>*</font>':'');
		$required_attrib = (($data['GAQ_ANS_MANDITORY']=='Y' and $for_hr)?'required':'');
		$is_reqd = ($data['GAQ_ANS_MANDITORY']=='Y' and $for_hr);
		$selected_val = '';
		if($for_hr) {
			$selected_val = isset($ans[$data['GAQ_QUESTION_ID']])?$ans[$data['GAQ_QUESTION_ID']]:'';
		}
echo<<<END
<tr><td class="head_blue" nowrap>$manditory_mark<label id="q$sl">{$data['GAQ_QUESTION_TYPE']}</label></td><td>
<input type="hidden" name="qid[]" id="qid[$sl]" value="{$data['GAQ_QUESTION_ID']}">
<input type="hidden" name="qanstype[]" id="qanstype[$sl]" value="{$data['GAQ_INPUT_TYPE']}">
<input type="hidden" name="ansmandtory[]" id="ansmandtory[$sl]" value="{$data['GAQ_ANS_MANDITORY']}">
END;
if($audittypeid==15){
	echo<<<END
<input type="hidden" name="selected_bq_id[]" id="selected_bq_id[{$data['GAQ_QUESTION_ID']}]" value="">
<input type="hidden" name="bq_group_id[]" id="bq_group_id[$sl]" value="{$data['GAQ_GROUP_ID']}">
END;
}
		$data_answer=$data['GAQ_AVAL_ANSWER'];
		$ansswer_vals_array=explode(',',$data_answer);
		if($data_answer=='EMP_LIST') {
			$qry = "select gem_emp_id,gem_emp_name from gft_emp_master where gem_status='A' and gem_emp_id<7000";
			$ansswer_vals_array = get_two_dimensinal_array_from_query($qry, "gem_emp_id", "gem_emp_name");
		}
		$t_inputtype = (int)$data['GAQ_INPUT_TYPE'];
		switch ($t_inputtype){
			case 1:
				echo fix_checkbox_with("qidans[".$data['GAQ_QUESTION_ID']."][]",$ansswer_vals_array,array($selected_val),'',null,null,true,$is_reqd);
				break;
			case 2:
				echo fix_radio_with("qidans[".$data['GAQ_QUESTION_ID']."][]",$ansswer_vals_array,array($selected_val),'',null,null,true,$is_reqd);
				break;
			case 3:
				echo fix_combobox_with("qidans[".$data['GAQ_QUESTION_ID']."]","qidans[".$data['GAQ_QUESTION_ID']."]",$ansswer_vals_array,$selected_val,null,'select',null,false,'',1,null,'',$is_reqd);
				break;
			case 4:
				echo "<input type=\"text\" size=\"52\" id=\"qidans[".$data['GAQ_QUESTION_ID']."]\" name=\"qidans[".$data['GAQ_QUESTION_ID']."]\" class=\"formStyleTextarea\" $required_attrib value='$selected_val'>";
				break;
			case 5:
				echo "<textarea cols=\"50\" rows=\"2\" id=\"qidans[".$data['GAQ_QUESTION_ID']."]\" name=\"qidans[".$data['GAQ_QUESTION_ID']."]\" class=\"formStyleTextarea\" $required_attrib>$selected_val</textarea>";
				break;
			case 6:
				echo "<input type=\"text\" id=\"qidans[".$data['GAQ_QUESTION_ID']."]\" name=\"qidans[".$data['GAQ_QUESTION_ID']."]\" class=\"formStyleTextarea\" size=\"10\" value='$selected_val' readonly/>" .
				    "&nbsp; <img src='images/date_time.gif' class='imagecur' id='dateqid_".$data['GAQ_QUESTION_ID']."' width='16' height='16' align='absmiddle'>". 
				    "<script type=\"text/javascript\" >init_date_func(\"qidans[".$data['GAQ_QUESTION_ID']."]\",\"%Y-%m-%d\",'dateqid_".$data['GAQ_QUESTION_ID']."','Bl');</script>";
				break;	
			case 12:
				echo "<input type=\"file\" id=\"qidans[".$data['GAQ_QUESTION_ID']."]\" name=\"qidans[".$data['GAQ_QUESTION_ID']."]\" $required_attrib>";
				break;
			default:
				//Do nothing
				break;
		}
echo<<<END
</td></tr>
END;
if($data['GAQ_QUESTION_ID']=='456') {
    echo <<<END
<tr><td class="head_blue" nowrap><label id="campus_offer_letter">Offer Letter</label></td>
<td><input type='file' name='campus_offer_letter[]' id='campus_offer_letter'></td></tr>
END;
}
	$sl++;
	}
if($audittypeid==20){
echo<<<END
<script type="text/javascript" >
function validate_audit_questions(){
var no_of_questions=document.getElementsByName("qid[]").length;
	if(document.getElementById('product_consultant') && $pc_validation){
		if(document.getElementById('product_consultant').value == 0){
			alert("Please select a Product Consultant in 'Delivery Owner'");
			return false;
		}
	}
	if($("date_from1")){
	var myStyle = document.getElementById("date_from1");	
	if(myStyle.style.display == "none"){ 	     
	}else{
		 for(var i=1;i<=no_of_questions;i++){
		if(document.getElementById("ansmandtory["+i+"]").value=='Y'){
			switch(document.getElementById("qanstype["+i+"]").value){
				case '1':
					if(!find_anyone_checked("qidans["+document.getElementById("qid["+i+"]").value+"][]")){
						alert("Please Select correct Answer for question '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}
					break;
				case '2':
					if(!find_anyone_checked("qidans["+document.getElementById("qid["+i+"]").value+"][]")){
						alert("Please Select correct Answer for question '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}				
					break;
				case '3':
					if(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value==0){
						alert("Please Select correct Answer for question  '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}
					break;
				case '4':
					document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value=trim(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value);
					if(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value==''){
						alert("Please Select correct Answer for question '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}
					break;
				case '5':
					if(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value==''){
						alert("Please Select correct Answer for question  '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}
					break;		
				case '6':
					if(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value==''){
						alert("Please Select correct Answer for question  '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}
					if(document.getElementById("qid["+i+"]").value==115){//This validation added for Expected Completion Date of Product Delivery
						var sel_date	=(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value).split('-');
						var selectedDate = new Date(sel_date[0],sel_date[1],sel_date[2]);	
						var currentDate = new Date()
						var current_dt	=new Date(currentDate.getFullYear(),(currentDate.getMonth() + 1),currentDate.getDate());
						if(Date.parse(selectedDate)<Date.parse(current_dt)){
							alert("'"+document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"' should not be past date");
							return false;
						}
					}
					break;		
			}
		}
	}
	}
	}
	return true;
}
</script>
END;
}else{
	
echo<<<END
<script type="text/javascript" >
$comment_line1 function validate_audit_questions(){
var no_of_questions=document.getElementsByName("qid[]").length;	
		 for(var i=1;i<=no_of_questions;i++){
		if(document.getElementById("ansmandtory["+i+"]").value=='Y'){
			switch(document.getElementById("qanstype["+i+"]").value){
				case '1':
					if(!find_anyone_checked("qidans["+document.getElementById("qid["+i+"]").value+"][]")){
						alert("Please Select correct Answer for question '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}
					break;
				case '2':
					if(!find_anyone_checked("qidans["+document.getElementById("qid["+i+"]").value+"][]")){
						alert("Please Select correct Answer for question '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}				
					break;
				case '3':
					if(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value==0){
						alert("Please Select correct Answer for question  '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}
					break;
				case '4':
					document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value=trim(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value);
					if(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value==''){
						alert("Please Select correct Answer for question '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}
					break;
				case '5':
					if(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value==''){
						alert("Please Select correct Answer for question  '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}
					break;		
				case '6':
					if(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value==''){
						alert("Please Select correct Answer for question  '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}
					if(document.getElementById("qid["+i+"]").value==115){//This validation added for Expected Completion Date of Product Delivery
						var sel_date	=(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value).split('-');
						var selectedDate = new Date(sel_date[0],sel_date[1],sel_date[2]);	
						var currentDate = new Date()
						var current_dt	=new Date(currentDate.getFullYear(),(currentDate.getMonth() + 1),currentDate.getDate());
						if(Date.parse(selectedDate)<Date.parse(current_dt)){
							alert("'"+document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"' should not be past date");
							return false;
						}
					}
					break;		
				case '12':
					if(document.getElementById("qidans["+document.getElementById("qid["+i+"]").value+"]").value==''){
						alert("Please Select a file for  '" +document.getElementById("q"+i).innerHTML.replace( /\\&amp;/g, '&' )+"'");
						return false;
					}
					break;
			}
		}
	}
	
	return true;
}$comment_line2
</script>
END;
	
}
}
/**
 * @param string $quotation_no
 * @return boolean
 */
function check_proposal_quotation($quotation_no) {
    $proposal_quote = false;
    $proposal_check_qry = " select GQH_HQ_PROPOSAL_DOC_ID from gft_quotation_hdr where gqh_order_no='$quotation_no' ";
    $proposal_check_res = execute_my_query($proposal_check_qry);
    if($proposal_row = mysqli_fetch_assoc($proposal_check_res)) {
        if((int)$proposal_row['GQH_HQ_PROPOSAL_DOC_ID']>0) {
            $proposal_quote = true;
        }
    }
    return $proposal_quote;
}
?>
