<?php
require_once(__DIR__ ."/../dbcon.php");
/**
 * @param int $year 
 * 
 * @return void
 */
function holyday_list_todisplay($year){
	if ($year == 0){
		$year=(int)date("Y");
	}
	$query="SELECT GHL_DATE,dayname(GHL_DATE) day_of_week, GHL_DESC,GHL_OPTIONAL FROM gft_holiday_list" .
			" WHERE GHL_DATE between '$year-01-01' and '$year-12-31' and GHL_MONTHLY_OFF='N' order by GHL_DATE ";
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)>0){
		echo "<h1>List of National & Festival Holidays for the year $year<h1>" .
			"<table class=\"FormBorder1\" border=0 align=center cellspacing=2 cellpadding=0 width=\"100%\">".
			"<thead><tr align=center>" .
			"<td class=\"midboxseshead1\"><b>S.No.</b></td>" .
			"<td class=\"midboxseshead1\"><b>Date</b></td>" .
			"<td class=\"midboxseshead1\"><b>Day</b></td>" .
			"<td class=\"midboxseshead1\"><b>Name of Holiday</b></td>" .
			"</tr></thead><tbody>";
		$i=1;
		while($data=mysqli_fetch_array($result)){
			echo "<tr class=".($i%2==0?"odd":"even")." onMouseOver=\"this.style.backgroundColor='#ADDFFF';\" onMouseOut=\"this.style.backgroundColor='';\">" .
				"<td>$i</td><td>".$data['GHL_DATE']."</td><td>".$data['day_of_week']."</td><td>".$data['GHL_DESC']."".($data['GHL_OPTIONAL']=='Y'?"(Optional)":"")."</td></tr>";
			$i++;
		}
		echo "</tbody></table>";
		echo "Note :<b>".get_samee_const("HOLY_DAY_NOTE")."</b>";
	}else{
		echo "<font color=red>Check with HR or Mail to hr@gofrugal.com to Update Leave Details in SAM EE</font>";
	}
}
/**
 * @param int $type
 * @param string $search_text
 * @return string
 */
function get_query_for_emp_list_display($type=1,$search_text='') {
	$cond = '';
	if($search_text!='') {
		$cond .= " and (em.gem_emp_name like '$search_text%' or em.GEM_MOBILE like '$search_text%' or em.GEM_RELIANCE_NO like '$search_text%' or em.GEM_EMAIL like '$search_text%' or GEW_DESC like '$search_text%') "; 
	}
	$query = " SELECT em.GEM_EMP_ID,em.GEM_EMP_name, em.GEM_MOBILE,em.GEM_TITLE, em.GEM_RELIANCE_NO, em.GEM_PROFILE_URL, " .
			 " em.GEM_EMAIL, em.GEM_IC,  a.GEW_DESC,rm.GRM_ROLE_DESC, rm.GRM_ROLE_ABR, rm.GRM_ORDER_DISPLAY, " .
			 " em.GEM_LAPTOP_HARDDISK_ID,em.GEM_MOBILE_MAC_ID,GEM_LABTOP_MAC_ID,a.GEW_EMAIL " .
			 " FROM gft_emp_master em join gft_role_master rm on (rm.GRM_ROLE_ID=em.gem_role_id ) " .
			 " left join gft_emp_web_display a on (em.WEB_GROUP=a.GEW_ID) where em.GEM_STATUS='A' AND em.GEM_EMP_ID < 7000 $cond " .
			 " ORDER BY a.GEW_DISPLAY_ORDER, a.GEW_ID, GRM_ORDER_DISPLAY,rm.GRM_ROLE_ID,em.gem_emp_id ";
	if($type==2) {
		$query =" select GIM_INTERN_ID,GIM_INTERN_NAME,GIM_INTERN_DESC,GIM_MOBILE,GIM_MOBILE1,GIM_EMAIL,GIM_IC ".
				 " from gft_intern_master where GIM_STATUS='A' ";
	}
	return $query;
}
/**
 * @return void
 */
function emp_list_todisplay(){
	$query = get_query_for_emp_list_display();
	$result=execute_my_query($query);
if (mysqli_num_rows($result) != 0){
	$i=1;
	$result_new='';
	$show_mobile_mac_laptop_dtl=false;
	if((isset($_SESSION["uid"]) and isset($_SESSION["uname"])  and isset($_SESSION["roleid"])) and (strpos($_SERVER['SCRIPT_NAME'],'emp_ui.php')>0) ){
		$count_num_rows=mysqli_num_rows($result);
		$nav_struct=get_dtable_navigation_struct($count_num_rows,'all');
		print_dtable_navigation($count_num_rows,$nav_struct,'',"export_all_report.php",$query,$heading=null,$sp=null,1,false,null,null,null,null);
		$uid=$_SESSION["uid"];
		if (is_authorized_group_list($uid, array (65))){
			$show_mobile_mac_laptop_dtl=true;
		}
	}
//NOTE: Disabled S.No as required by kumar - 9-Nov
		//"<td class=\"midboxseshead1\"><b>S.No.</b></td>" .
	echo "<table class=\"FormBorder1\" border=0 align=center cellspacing=2 cellpadding=0 width=\"100%\">".
		"<thead><tr align=center>" .
		"<td class=\"midboxseshead1\"><b>Name</b></td>" .
		"<td class=\"midboxseshead1\"><b>Designation</b></td>" .
		"<td class=\"midboxseshead1\"><b>Mobile Number</b></td>" .
		"<td class=\"midboxseshead1\"><b>Email id</b></td>" .
		"<td class=\"midboxseshead1\"><b>Intercom</b></td>" .
		($show_mobile_mac_laptop_dtl?"<td class=\"midboxseshead1\"><b>Laptop Hard Disk ID</b></td><td class=\"midboxseshead1\"><b>Laptop MAC Addr</b></td><td class=\"midboxseshead1\"><b>Mobile MAC Addr</b></td>":"").
		"</tr></thead><tbody>";
		$data_new='';
		while ($data=mysqli_fetch_array($result)){
        	if($data['GEW_DESC']!='' and $data_new!=$data['GEW_DESC']){
				$data_new=$data['GEW_DESC'];
				$gew_email = $data['GEW_EMAIL'];
				$disp_title = $data_new;
				if($gew_email!=''){
					$disp_title .= " [$gew_email] ";
				}
				echo"<tr style=\"height:20\" class=\"highlight_blue\" onMouseOver=\"this.style.backgroundColor='#ADDFFF';\" onMouseOut=\"this.style.backgroundColor='';\">" .
						"<td colspan=10  align=\"center\" class=\"midboxseshead1\"><b> $disp_title </b></td></tr>" ;
       		}
    		//		"<td>&nbsp;$i</td>" .
	    	echo"<tr class=".($i%2==0?"odd":"even")." onMouseOver=\"this.style.backgroundColor='#ADDFFF';\" onMouseOut=\"this.style.backgroundColor='';\">".
    				"<td>&nbsp;".$data['GEM_EMP_name']."</td>" .
    				"<td>&nbsp;".$data['GEM_TITLE']."</td>" .
	    			"<td>&nbsp;".trim($data['GEM_MOBILE']). (trim($data['GEM_RELIANCE_NO'])!=''?' / '.trim($data['GEM_RELIANCE_NO']):''). "</td>" .
    				"<td>&nbsp;<a class=\"link2txt\" href=\"mailto:".$data['GEM_EMAIL']."\">".(string)str_replace('@'.get_samee_const("OFFICEAL_MAIL_DOMAIN"),'',$data['GEM_EMAIL'])."</a></td>" .
    				"<td>&nbsp;".$data['GEM_IC']."</td>".
    				($show_mobile_mac_laptop_dtl?"<td>&nbsp;".$data['GEM_LAPTOP_HARDDISK_ID']."</td><td>&nbsp;".$data['GEM_LABTOP_MAC_ID']."</td><td>&nbsp;".$data['GEM_MOBILE_MAC_ID']."</td>":"")."</tr>";
			$i++;
		}
		
	}
}
/**
 * @return void
*/

function show_intern_detail(){
	$query1 = get_query_for_emp_list_display(2);
	$result=execute_my_query($query1);
	$no_of_rows=mysqli_num_rows($result);
	$i=1;
	if($no_of_rows>=1){
	while ($data=mysqli_fetch_array($result)){
		if($i==1){
		echo"<tr style=\"height:20\" class=\"highlight_blue\" onMouseOver=\"this.style.backgroundColor='#ADDFFF';\" onMouseOut=\"this.style.backgroundColor='';\">" .
				"<td colspan=10  align=\"center\" class=\"midboxseshead1\"><b>".$data['GIM_INTERN_DESC']."&nbsp;<b></td></tr>" ;
		$i++;
		}
		echo"<tr class=".($i%2==0?"odd":"even")." onMouseOver=\"this.style.backgroundColor='#ADDFFF';\" onMouseOut=\"this.style.backgroundColor='';\">".
				"<td>&nbsp;".$data['GIM_INTERN_NAME']."</td>" .
				"<td>&nbsp;".$data['GIM_INTERN_DESC']."</td>" .
				"<td>&nbsp;".substr(trim($data['GIM_MOBILE']),-10). (trim($data['GIM_MOBILE1'])!=''?' / '.substr(trim($data['GIM_MOBILE1']),-10):''). "</td>" .
				"<td>&nbsp;<a class=\"link2txt\" href=\"mailto:".$data['GIM_EMAIL']."\">".$data['GIM_EMAIL']."</a></td>" .
				"<td>&nbsp;".$data['GIM_IC']."</td>" .
				"<td>&nbsp;".''."</td></tr>";
	}
	}
	echo "</tbody></table>";
}
/**
 * @return void
 */
function cust_training_buddies(){
	echo "<h3><center><font face=\"Arial,Helvetica\"> L1 & Field Buddies Name For Customer Training</font></center></h3>";
	$incharge=execute_my_query("select GCTI_L1_INCHARGE,GCT1_SUPPORT_INCHARGE," .
			"s.GEM_EMP_NAME as l1,f.GEM_EMP_NAME as field,s.GEM_MOBILE mob,f.GEM_MOBILE mobi," .
			" s.GEM_RELIANCE_NO as rmob,f.GEM_RELIANCE_NO rmobi  " .
			" from gft_customer_training_incharge " .
			" left join gft_emp_master s on (s.gem_emp_id=GCTI_L1_INCHARGE and s.gem_status='A') " .
			" left join gft_emp_master f on (f.gem_emp_id=GCT1_SUPPORT_INCHARGE and f.gem_status='A' )") ;
	if (mysqli_num_rows($incharge) != 0){
	$i=1;
	echo "<table class=\"FormBorder1\" border=0 align=center cellspacing=2 cellpadding=0 width=\"100%\">".
		"<thead><tr align=center>" .
		"<td class=\"midboxseshead1\"><b>S.No.</b></td>" .
		"<td class=\"midboxseshead1\"><b>L1 Team Incharge</b></td>" .
		"<td class=\"midboxseshead1\"><b>L1 Team Incharge Mobile</b></td>" .
		"<td class=\"midboxseshead1\"><b>Field Support Incharge</b></td>" .
		"<td class=\"midboxseshead1\"><b>Field Support Incharge Mobile</b></td>" .
		"</tr></thead><tbody>";
		while ($result=mysqli_fetch_array($incharge)){
			  echo"<tr class=".($i%2==0?"odd":"even")." onMouseOver=\"this.style.backgroundColor='#ADDFFF';\" onMouseOut=\"this.style.backgroundColor='';\">".
    		  "<td>&nbsp;$i</td>" .
    		  "<td>&nbsp;".$result['l1']."</td>" .
    		  "<td>&nbsp;".substr(trim($result['mob']),-10). (trim($result['rmob'])!=''?' / '.substr(trim($result['rmob']),-10):''). "</td>" .
    		  "<td>&nbsp;".$result['field']."</td>" .
    		  "<td>&nbsp;".substr(trim($result['mobi']),-10). (trim($result['rmobi'])!=''?' / '.substr(trim($result['rmobi']),-10):''). "</td>" .
    		  "</tr>";
    		  $i++;
		}
		echo "</tbody></table>";
	}
}

/**
 * @param int $param This has default value 
 * 
 * @return void
 */
function show_team_page($param=0){
echo "<br>";
	if($param==1){
		echo "<h3><center><font face=\"Arial,Helvetica\"><a onclick=\"javascript:$('emp_list').className='unhide';$('holi_day').className='hide';\">GoFrugal Team</a>" .
			"&nbsp;&nbsp; / <a onclick=\"javascript:$('emp_list').className='hide';$('holi_day').className='unhide';\">Holiday List</a></font></center></h3>";
	}		
	echo "<div id=\"emp_list\" class=\"unhide\">";
	emp_list_todisplay();
	show_intern_detail();
	//cust_training_buddies();
	if($param==1){
		echo "</div><div id=\"holi_day\" class=\"hide\">";
		holyday_list_todisplay(0);
		echo "</div>";
	}
}

/**
 * @param string $emply_id
 * @param string $fromDate
 * @param string $monthlyValue
 * @param string $mrpValue
 * @param string $exist_id
 *
 * @return void
 */
function update_ctc_info($emply_id,$fromDate,$monthlyValue,$mrpValue,$exist_id){
	global $uid;
	$updarr = /*. (string[string]) .*/array();
	$updarr['GCI_EMP_ID'] 			= $emply_id;
	$updarr['GCI_EFFECTIVE_FROM'] 	= db_date_format($fromDate);
	$updarr['GCI_MONTHLY_CTC'] 		= $monthlyValue;
	$updarr['GCI_MRP'] 		        = $mrpValue;
	$updarr['GCI_UPDATED_BY'] 		= $uid;
	$updarr['GCI_UPDATED_DATE'] 	= date('Y-m-d H:i:s');
	$keyarr = /*. (string[string]) .*/array();
	if((int)$exist_id > 0){
		$keyarr['GCI_ID'] = $exist_id;
	}
	array_update_tables_common($updarr, "gft_ctc_info", $keyarr, null, $uid,null,null,$updarr);
}

/**
 * @param string $emply_id
 * @param string $type
 * @param string $effec_date
 * @param string $type_value
 * 
 * @return void
 */
function update_employee_effective_dates($emply_id,$type,$effec_date,$type_value){
    $q1 = " select GEE_EMP_ID from gft_emp_effective_dates where GEE_EMP_ID='$emply_id' and GEE_TYPE='$type' ".
        " and GEE_EFFECTIVE_DATE='$effec_date' and GEE_VALUE='$type_value' ";
    $r1 = execute_my_query($q1);
    if(mysqli_num_rows($r1) > 0){
        return;
    }
    $insert_arr = array(
        'GEE_EMP_ID'=>$emply_id,            'GEE_TYPE'=>$type,
        'GEE_EFFECTIVE_DATE'=>$effec_date,  'GEE_VALUE'=>$type_value
    );
    array_insert_query("gft_emp_effective_dates", $insert_arr);
}

/**
 * @param string $reg_no
 * @param string $mobile
 * @param string $email
 * @param string $visit_id
 * @return string
 */
function get_student_id($reg_no,$mobile,$email,$visit_id) {
	$cond = " and gsm_visit_id = '$visit_id' ";
	if($reg_no!='') {
    	$q1 = "select gsm_id from gft_student_master where GSM_REG_NO='".mysqli_real_escape_string_wrapper($reg_no)."' $cond";
    	$res1 = execute_my_query($q1);	
    	if($row = mysqli_fetch_array($res1)) {
    		return $row['gsm_id'];
    	}
	}
	$q2 = "select gsm_id from gft_student_master where GSM_EMAIL_ID='".mysqli_real_escape_string_wrapper($email)."' $cond";
	$res2 = execute_my_query($q2);
	if($row = mysqli_fetch_array($res2)) {
		return $row['gsm_id'];
	}
	$q3 = "select gsm_id from gft_student_master where GSM_MOBILE_NO='".mysqli_real_escape_string_wrapper($mobile)."' $cond";
	$res3 = execute_my_query($q3);
	if($row = mysqli_fetch_array($res3)) {
		return $row['gsm_id'];
	}
	return '0';
}
/**
 * @param string $drive_id
 * @param string $param
 * @return string[int]
 */
function get_no_matches_for_drive($drive_id,$param) {
	$qry = '';
	if($param=='apti_upload') {
		$qry = "select gad_id key_id from gft_aptitude_dtl where gad_student_id='0' and gad_visit_id='$drive_id'";
	} else if($param=='kps_upload') {
		$qry = "select gkh_id key_id from gft_kps_hdr where gkh_student_id='0' and gkh_visit_id='$drive_id'";
	} else if($param=='guvi_upload') {
	    $qry = "select gcp_id key_id from gft_campus_programming_result where gcp_student_id='0' and gcp_drive_id='$drive_id'";
	}
	$res = execute_my_query($qry);
	$arr = /*.(string[int]).*/array();
	while($row = mysqli_fetch_array($res)) {
		$arr[] = $row['key_id'];
	}
	return $arr;
}
/**
 * @param string[int] $no_matches
 * @param string $drive_id
 * @param string $param
 * @param boolean $from_popup
 * @return string
 */
function student_non_matches_ui($no_matches,$drive_id,$param,$from_popup=false) {
	$ui = '';
	if($no_matches==null || count($no_matches)==0) {
		$no_matches = get_no_matches_for_drive($drive_id,$param);
	}
	if(count($no_matches)>0) {
		$url_prefix = '';
		$popup = '0';
		if($from_popup) {
			$url_prefix = '/human_resource/';
			$popup = '1';
		}
		$ids = implode("','",$no_matches);
		$not_in_qry = $qry = '';
		if($param=='apti_upload') {
			$qry = " select gad_id,GAD_STUDENT_NAME,GAD_STUDENT_REG_NO,GAD_STUDENT_MOBILE,GAD_STUDENT_EMAIL from gft_aptitude_dtl ".
				   " where gad_id in ('$ids') and (GAD_STUDENT_NAME!='' or GAD_STUDENT_REG_NO !='' or GAD_STUDENT_MOBILE!='' or ".
				   " GAD_STUDENT_EMAIL!='') ";
			$not_in_qry = "select gad_student_id from gft_aptitude_dtl where gad_student_id!='0' and GAD_VISIT_ID='$drive_id'";
		} else if($param=='kps_upload') {
			$qry = " select gkh_id gad_id,GKH_STUDENT_NAME GAD_STUDENT_NAME,GKH_STUDENT_REG_NO GAD_STUDENT_REG_NO, ".
				   " GKH_STUDENT_MOBILE GAD_STUDENT_MOBILE,GKH_STUDENT_EMAIL GAD_STUDENT_EMAIL from gft_kps_hdr ".
				   " where gkh_id in ('$ids') and (GKH_STUDENT_NAME!='' or GKH_STUDENT_REG_NO!='' or GKH_STUDENT_MOBILE!='' or ".
				   " GKH_STUDENT_EMAIL!='')";
			$not_in_qry = "select gkh_student_id from gft_kps_hdr where gkh_student_id!='0' and GKH_VISIT_ID='$drive_id'";
		} else if($param=='guvi_upload') {
		    $qry = " select GCP_ID gad_id,GCP_STUDENT_NAME GAD_STUDENT_NAME,'' GAD_STUDENT_REG_NO, ".
		  		   " GCP_MOBILE_NO GAD_STUDENT_MOBILE,GCP_EMAIL_ID GAD_STUDENT_EMAIL from gft_campus_programming_result ".
		  		   " where gcp_id in ('$ids') and (GCP_STUDENT_NAME!='' or GCP_MOBILE_NO!='' or GCP_EMAIL_ID!='') ";
		    $not_in_qry = "select gcp_student_id from gft_campus_programming_result where gcp_student_id!='0' and gcp_drive_id='$drive_id'";
		}
		$master_qry = " select gsm_id,concat(GSM_REG_NO,' - ',GSM_STUDENT_NAME,' - ',GSM_STREAM) dtl from gft_student_master ".
		   			  " where gsm_id not in ($not_in_qry) and GSM_VISIT_ID='$drive_id' ";
		$arr = get_two_dimensinal_array_from_query($master_qry, "gsm_id", "dtl");
		$res = execute_my_query($qry);
		$rcount = mysqli_num_rows($res);
		if($rcount>0) {
			$ui .= "<script type='text/javascript' src='/../js/jquery.js'></script>";
			$ui .= "<script type='text/javascript' src='/../js/js_campus_drive.js'></script>";
			$ui .= "<link href='/../libs/select2.css' rel='stylesheet' /><script type='text/javascript' src='/../libs/select2.js'></script>";
			$ui .= "<h2 style='text-align: center;'>Student Identification not found for following students</h2>";
			$ui .= "<table border=1 style='border-collapse: collapse;' align='center' width='60%'>"; 
			$ui .= "<form name='matching_form' method='POST' action='".$url_prefix."campus_drive_submit.php'>";
			$ui .= "<input type='hidden' name='purpose' value='matches'>";
			$ui .= "<input type='hidden' name='param' value='$param'>";
			$ui .= "<input type='hidden' name='pop_up' value='$popup'>";
			$ui .= "<tr><th colspan=6>Total Count: $rcount</th></td>";
			$ui .= "<tr><th></th><th>Student Name</th>";
			if($param!='guvi_upload') {
			    $ui .= "<th>Reg No</th>";
			}
			$ui .= "<th>Email</th><th>Mobile</th><th>Master Data [RegNo - Name - Stream]</th></tr>";
			$counter = 0;
			while($row = mysqli_fetch_array($res)) {
				$counter += 1;
				$college_combo = fix_combobox_with("s_id$counter", "s_id[$counter]", $arr, '',"","select",null,false,"",1,null,"");
				$apt_id = $row['gad_id'];
				$ui .= "<tr><td>$counter</td>";
				$ui .= "<td>".$row['GAD_STUDENT_NAME']."<input type='hidden' name='apt_id[$counter]' value='$apt_id'></td>";
				if($param!='guvi_upload') {
				    $ui .= "<td>".$row['GAD_STUDENT_REG_NO']."</td>";
				}
				$ui .= "<td>".$row['GAD_STUDENT_EMAIL']."</td>";
				$ui .= "<td>".$row['GAD_STUDENT_MOBILE']."</td>";
				$ui .= "<td>".$college_combo."<script type='text/javascript'>jQuery('#s_id$counter').select2();</script></td>";
				$ui .= "</tr>";
			}
			$ui .=<<<END
			<tr><td colspan='6' style='text-align:center;'>
			<INPUT  class="button" id="submit1" name="submit1" type="button" value="Submit" onclick="matches_evaluate();">
			<INPUT  class="button" id="reset1" name="reset1" type="reset" value="Reset"></td></tr></form>
END;
			$ui .= "</table>";
		}
	} else {
		$ui = "<h3>No mismatches found</h3>";
	}
	return $ui;
}
/**
 * @return string[int]
 */
function fetch_all_drive_questions() {
    $qry = "select GDQ_ID,GDQ_QUESTION from gft_drive_questions ";
	$res = execute_my_query($qry);
	$res_arr = /*.(string[int]).*/array();
	while($row = mysqli_fetch_array($res)) {
		$res_arr[(int)$row['GDQ_ID']] = $row['GDQ_QUESTION'];
	}
	return $res_arr;
}
/**
 * @param string $campus_visit
 * @return void
 */
function show_eoi_summary($campus_visit) {
	$qry = " select gsm_role,count(distinct GSM_ID) student_counts,gcm_name,GCV_DRIVE_NAME,gcv_id, ".
	       " sum(if(gsm_aptitude_result=1 or gsm_programming_result=1,1,0)) apti, ".
		   " sum(if(gsm_kps_result=1,1,0)) kps,sum(if(gsm_gd_result=1,1,0)) gd,grm_name,gsm_role_id from gft_student_master ". 
		   " left join gft_college_visits on (gsm_visit_id=gcv_id) left join gft_college_master on (gcv_college_id=gcm_id) ".
		   " left join gft_campus_roles_master on (grm_id=GSM_ROLE_ID) ".
		   " where gsm_visit_id='$campus_visit' group by gsm_role,gsm_role_id ";
	$res = execute_my_query($qry);
	$row_count = mysqli_num_rows($res);
	$colspan_arr = array();
	$rowspan_arr = array();
	$myarr_sub = array();
	echo '<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="95%">';
	$myarr = array("S.No","Role Name","No. of Students","Aptitude/Programming Passed Count","KPS Passed Count","GD Passed Count");
	$mysort = array("","gsm_role","student_counts","apti","kps","gd");
	sortheaders($myarr, $mysort, null, '', '', null, null, null, $myarr_sub, $rowspan_arr, $colspan_arr);
	$sl = $total_cnt = 0;
	$tot_apti_passed = 0; $tot_kps_passed = 0; $tot_gd_passed = 0;
	if(mysqli_num_rows($res)>0) {
	    $drive_id = '';
    	while($row = mysqli_fetch_array($res)) {
    		$sl++;
    		$role = ($row['gsm_role']!=''?$row['gsm_role']:$row['grm_name']);
    		$role_id = ($row['gsm_role']!=''?$row['gsm_role']:$row['gsm_role_id']);
    		$counts = $row['student_counts'];
    		$total_cnt += (int)$counts;
    		$apti = $row['apti'];
    		$kps = $row['kps'];
    		$gd = $row['gd'];
    		$drive_id = $row['gcv_id'];
    		$tot_apti_passed += (int)$apti;
    		$tot_kps_passed += (int)$kps;
    		$tot_gd_passed += (int)$gd;
    		$url_params = "page_limit=all&roleName=$role_id&purpose=eoi&new_tab=1&campusVisit=".urlencode($drive_id);
    		$url1 = "score_detail.php?".$url_params;
    		$cnt_link = "<a href='$url1' target=_blank>$counts</a>";
    		print_resultset(array(array("$sl",$role,$cnt_link,$apti,$kps,$gd)),null,array('left','left','right','right','right','right'));
    	}
    	$total_array = array();
    	$total_array[] = array("","Total","<a href='score_detail.php?page_limit=all&purpose=eoi&new_tab=1&campusVisit=".urlencode($drive_id)."' target=_blank>$total_cnt</a>",$tot_apti_passed,$tot_kps_passed,$tot_gd_passed);
    	print_resultset($total_array,null,array('left','left','right','right','right','right'));
	}
	echo "</table>";
}
/**
 * @param string $campus_visit
 * @return void
 */
function show_kps_summary($campus_visit) {
	$qry = " select gsm_role,count(distinct GSM_ID) student_counts,gcv_id,sum(if(gsm_kps_result=1,1,0)) passed, ".
		   " sum(if(gsm_kps_result=2,1,0)) not_passed,sum(if(gsm_kps_result=3,1,0)) review,sum(if(gsm_kps_result=0,1,0)) pending, ".
		   " sum(if(gsm_kps_result=1,1,0)) kps_passed_cnt,sum(if(gsm_kps_result=4,1,0)) kps_in_progress from gft_kps_hdr ".
		   " join gft_student_master on (gsm_id=gkh_student_id) ".
		   " join gft_college_visits on (gsm_visit_id=gcv_id) join gft_college_master on (gcv_college_id=gcm_id) ".
		   " where gkh_visit_id='$campus_visit' and (gsm_aptitude_result=1 or gsm_programming_result=1) group by gsm_role";
	$res = execute_my_query($qry);
	$row_count = mysqli_num_rows($res);
	$colspan_arr = array();
	$rowspan_arr = array();
	$myarr_sub = array();
	echo '<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="95%">';
	$myarr = array("S.No","Role Name","No. of Students","Selected","Not Selected","In-Progress","Need Help for Review","Result Pending");
	$mysort = array("","gsm_role","student_counts","passed","not_passed","kps_in_progress","review","pending");
	sortheaders($myarr, $mysort, null, '', '', null, null, null, $myarr_sub, $rowspan_arr, $colspan_arr);
	$sl = $t1 =$t2 = $t3 = $t4 = $t5 = $t6 = 0;
	$drive_id = '';
	while($row = mysqli_fetch_array($res)) {
		$sl++;
		$role = $row['gsm_role'];
		$counts = $row['student_counts'];
		$t1 += (int)$counts;
		$passed_cnt = $row['passed'];
		$t2 += (int)$passed_cnt;
		$not_passed = $row['not_passed'];
		$t3 += (int)$not_passed;
		$review = $row['review'];
		$t4 += (int)$review;
		$pending = $row['pending'];
		$t5 += (int)$pending;
		$in_progress = $row['kps_in_progress'];
		$t6 += (int)$in_progress;
		$drive_id = $row['gcv_id'];
		$url_params = "page_limit=all&roleName=$role&purpose=kps_dtl&new_tab=1&campusVisit=".urlencode($drive_id);
		$url1 = "score_detail.php?".$url_params."&aps_result=passed";
		$count_link =<<<END
		<a href='$url1' target=_blank>$counts</a>
END;
		$url2 = "score_detail.php?".$url_params."&kps_result=passed";
		$url3 = "score_detail.php?".$url_params."&kps_result=not_passed";
		$url4 = "score_detail.php?".$url_params."&kps_result=review";
		$url5 = "score_detail.php?".$url_params."&kps_result=pending&aps_result=passed";
		$url6 = "score_detail.php?".$url_params."&kps_result=in_progress";
		$passed_cnt = "<a href='$url2' target=_blank>".$row['kps_passed_cnt']."</a>";
		$not_url = "<a href='$url3' target=_blank>$not_passed</a>";
		$in_progress_url = "<a href='$url6' target=_blank>$in_progress</a>";
		$review_url = "<a href='$url4' target=_blank>$review</a>";
		$pending_url = "<a href='$url5' target=_blank>$pending</a>";
		print_resultset(array(array("$sl",$role,$count_link,"$passed_cnt",$not_url,$in_progress_url,$review_url,$pending_url)));
	}
	$url_params = "score_detail.php?page_limit=all&purpose=kps_dtl&new_tab=1&campusVisit=".urlencode($drive_id)."&aps_result=passed";
	$count_link = "<a href='$url_params' target=_blank>$t1</a>";
	$passed_cnt = "<a href='$url_params&kps_result=passed&show_send_sms=1' target=_blank>$t2</a>";
	$not_url = "<a href='$url_params&kps_result=not_passed' target=_blank>$t3</a>";
	$in_link = "<a href='$url_params&kps_result=in_progress' target=_blank>$t6</a>";
	$review_url = "<a href='$url_params&kps_result=review' target=_blank>$t4</a>";
	$pending_url = "<a href='$url_params&kps_result=pending' target=_blank>$t5</a>";
	print_resultset(array(array("","Total",$count_link,"$passed_cnt",$not_url,$in_link,$review_url,$pending_url)));
	echo "</table>";
}
/**
 * @param string $campus_visit
 * @return void
 */
function show_gd_summary($campus_visit) {
	$qry = " select gsm_role,count(distinct GSM_ID) student_counts,gcm_name,GCV_DRIVE_NAME,gcv_id,sum(if(gsm_gd_result=2,1,0)) gd_no, ".
			" sum(if(gsm_gd_result=1,1,0)) gd,sum(if(gsm_gd_result=0,1,0)) gd_pending from gft_student_master ".
			" join gft_college_visits on (gsm_visit_id=gcv_id) join gft_college_master on (gcv_college_id=gcm_id) ".
			" where gsm_visit_id='$campus_visit' and gsm_kps_result=1 group by gsm_role";
	$res = execute_my_query($qry);
	$row_count = mysqli_num_rows($res);
	$colspan_arr = array();
	$rowspan_arr = array();
	$myarr_sub = array();
	echo '<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="95%">';
	$myarr = array("S.No","Role Name","No. of Students","GD Selcted","GD Not Selected","Result Pending");
	$mysort = array("","gsm_role","student_counts","gd","gd_no","gd_pending");
	sortheaders($myarr, $mysort, null, '', '', null, null, null, $myarr_sub, $rowspan_arr, $colspan_arr);
	$sl = $total_cnt = 0;
	$t1 = $t2 = $t3 = $t4 = 0;
	$drive_id = '';
	while($row = mysqli_fetch_array($res)) {
		$sl++;
		$role = $row['gsm_role'];
		$counts = $row['student_counts'];
		$t1 += (int)$counts;
		$gd = $row['gd'];
		$t2 += (int)$gd;
		$gd_reject = $row['gd_no'];
		$t3 += (int)$gd_reject;
		$gd_pending = $row['gd_pending'];
		$t4 += (int)$gd_pending;
		$drive_id = $row['gcv_id'];
		$url_params = "page_limit=all&roleName=$role&purpose=gd_dtl&new_tab=1&kps_result=passed&campusVisit=".urlencode($drive_id);
		$url1 = "score_detail.php?".$url_params;
		$cnt_link = "<a href='$url1' target=_blank>$counts</a>";
		print_resultset(array(array("$sl",$role,$cnt_link,"<a href='$url1&gd_res=passed' target=_blank>$gd</a>",
				"<a href='$url1&gd_res=not_passed' target=_blank>$gd_reject</a>",
				"<a href='$url1&gd_res=pending' target=_blank>$gd_pending</a>")));
	}
	$url_params = "page_limit=all&&purpose=gd_dtl&new_tab=1&kps_result=passed&campusVisit=".urlencode($drive_id);
	$url1 = "score_detail.php?".$url_params;
	$cnt_link = "<a href='$url1' target=_blank>$t1</a>";
	print_resultset(array(array("","Total",$cnt_link,"<a href='$url1&gd_res=passed&show_send_sms=1' target=_blank>$t2</a>",
				"<a href='$url1&gd_res=not_passed' target=_blank>$t3</a>",
				"<a href='$url1&gd_res=pending' target=_blank>$t4</a>")));
	echo "</table>";
}
/**
 * @return string[string][int]
 */
function get_campus_roles() {
	$qry = execute_my_query("select grm_id,grm_name from gft_campus_roles_master");
	$roles = /*.(string[string][int]).*/array();
	while($row = mysqli_fetch_array($qry)) {
		$roles[$row['grm_id']] = $row['grm_name'];
	}
	return $roles;
}
/**
 * @return void
 */
function show_campus_drives_list() {
	global $college_id,$campus_visit,$global_web_domain;
	$wh_cond = '';
	if($college_id!='0' and $college_id!='') {
		$wh_cond .= " and gcm_id='$college_id' ";
	}
	if($campus_visit!='' and $campus_visit!='0') {
		$wh_cond .= " and gcv_id='$campus_visit' ";
	}
	$qry = " select GCM_NAME,GCM_LOCATION,GCV_ID,GCV_COLLEGE_ID,GCV_DRIVE_NAME,GCD_VISIT_DATE,GCV_EOI_START_DATETIME,GCV_EOI_END_DATETIME, ".
	 	   " GCV_ROLES,GCM_NAME,GCM_LOCATION,GCV_DRIVE_NAME,GCD_VISIT_DATE,GCV_EOI_START_DATETIME,GCV_EOI_END_DATETIME,ifnull(GCV_ID,UUID()) d_id, ".
	 	   " group_concat(GRM_NAME) GCV_ROLES from gft_college_master join gft_college_visits on (gcm_id=gcv_college_id) ".
	 	   " left join gft_drive_role_mapping on (gdm_visit_id=gcv_id) ".
	 	   " left join gft_campus_roles_master on (GRM_ID=gdm_role_id) where 1 $wh_cond ".
	 	   " group by d_id order by GCD_VISIT_DATE desc ";
	$res = execute_my_query($qry);
	echo '<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="95%">';
	$myarr = array("S.No","Content-control","Drive Name","Drive Date","Campus Drive URL","EOI Start Time","EOI End Time","Roles");
	$mysort = array();
	sortheaders($myarr, $mysort, null, '', '', null, null);
	$clg_list = array();
	$roles = get_campus_roles();
	while($row = mysqli_fetch_array($res)) {
		$clg_name = $row['GCM_NAME']." - ".$row['GCM_LOCATION'];
		$cid   =  $row ['GCV_ID'];
		$drive_name = $row['GCV_DRIVE_NAME'];
		$drive_dt = $row['GCD_VISIT_DATE'];
		$drive_url = "$global_web_domain/careers/#/".urlencode($drive_name)."/jobs";
		$eoi_start = $row['GCV_EOI_START_DATETIME'];
		$eoi_end = $row['GCV_EOI_END_DATETIME'];
		$role_str = $row['GCV_ROLES']; // explode(",",
		$clg_list[$clg_name][] = array($drive_name,$drive_dt,$drive_url,$eoi_start,$eoi_end,$role_str,$cid);
	}
	$value_arr = array();
	$sno = 1;
	ksort($clg_list);
	foreach ($clg_list as $clg=>$clg_dtls) {
		$dtl_cnt = count($clg_dtls);
		print_executive_name($clg);
		foreach ($clg_dtls as $k=>$dtls) {
			$value_arr = array();
			$curr_row = array();
			$curr_row[] = $sno;
			$curr_row[] = '<a onclick="javascript:call_popup(\'create_campus_drive.php?drive_id='.(string)$dtls[6].'\',7);"><span class="imagecur edit-icon fas fa-pencil-alt"><span></a>';
			$curr_row[] = $dtls[0];
			$curr_row[] = $dtls[1];
			$curr_row[] = $dtls[2];
			$curr_row[] = $dtls[3];
			$curr_row[] = $dtls[4];
			$curr_row[] = $dtls[5];
			$value_arr[] = $curr_row;
			print_resultset($value_arr);
			$sno++;
		}
	}
// 	print_resultset($value_arr,null,null,null,null,null,null,$rowspan_arr);
	echo "</table>";
}
/**
 * @param string $drive_id
 * @return string[int]
 */
function get_all_qns($drive_id) {
	$all_qn_ids = /*.(string[int]).*/array();
	$all_qn_qry = " select gkd_question_id,ifnull(gdq_display_text,gdq_question) gdq_question ".
	        " from gft_kps_dtl join gft_drive_questions on (gdq_id=gkd_question_id) ".
			" join gft_kps_hdr on (gkd_id=gkh_id) where gkh_visit_id='$drive_id' and GDQ_STATUS=1 group by gkd_question_id ".
			" order by gkd_question_id ";
	$all_qn_res = execute_my_query($all_qn_qry);
	while($row = mysqli_fetch_array($all_qn_res)) {
		$all_qn_ids[(int)$row['gkd_question_id']] = $row['gdq_question'];
	}
	return $all_qn_ids;
}
/**
 * @param string $content
 * @return string
 */
function get_string_content_for_export($content) {
// 	$content = (string)str_replace(",",";",$content);
// 	$content = (string)str_replace("\n",";;",$content);
	$content = (string)str_replace("\\r","",$content);
	$content = (string)str_replace("\"","\'",$content);
// 	$content = "\"".$content."\"";
	return $content;
}
/**
 * @param string $drive_id
 * @param string $student_id
 * @param string $export_type
 * @return mixed[]
 */
function get_student_dtls_for_export($drive_id,$student_id='',$export_type='csv') {
	$wh_cond = '';
	if($student_id!='') {
		$wh_cond .= " and gsm_id='$student_id' ";
	}
	$dtl_qry =<<<END
select gsm_id,gsm_student_name,GSM_REG_NO,GSM_EMAIL_ID,GSM_MOBILE_NO,GSM_ROLE,GSM_ROLE_DESC,GSM_PREF_LOC,
if(GSM_PERFORMANCE_LEVEL=1,'AP',if(GSM_ACCEPT_LOW_LEVEL=1,'IP - Yes','IP - No')) as performer_type,
gad_score,gad_right,gad_wrong,gad_no_response,GSM_LANGUAGE,GSM_DEGREE,ifnull(gcp_score,'***') gcp_score,ifnull(gcp_rating,'***') gcp_rating from gft_student_master 
left join gft_aptitude_dtl on (gsm_id=gad_student_id and gad_visit_id=gsm_visit_id)
left join gft_kps_hdr on (gkh_student_id=gad_student_id and gad_visit_id=gkh_visit_id)
left join gft_campus_programming_result on (gcp_student_id=gsm_id and gcp_drive_id=gsm_visit_id) 
where gsm_visit_id='$drive_id' $wh_cond order by gsm_id
END;
	$dtl_res = execute_my_query($dtl_qry);
	$csv_arr = array();
	while($dtl_row = mysqli_fetch_array($dtl_res)) {
		$dtl = array();
		$dtl[] = $dtl_row['GSM_REG_NO'];
		$dtl[] = $dtl_row['gsm_student_name'];
		$dtl[] = $dtl_row['GSM_DEGREE'];
		$dtl[] = $dtl_row['GSM_EMAIL_ID'];
		$dtl[] = $dtl_row['GSM_MOBILE_NO'];
		$dtl[] = $dtl_row['GSM_ROLE'];
		$resp = $dtl_row['GSM_ROLE_DESC'];
		$pref_regions = $dtl_row['GSM_PREF_LOC'];
		if($export_type=='csv') {
			$resp = str_replace("\\n"," ",$resp);
			$pref_regions = str_replace("\\n"," ",$pref_regions);
		} else if($export_type=='pdf') {
			$resp = str_replace("\\n","<br/>",$resp);
			$pref_regions = str_replace("\\n","<br/>",$pref_regions);
		}
		$resp = get_string_content_for_export($resp);
		$pref_regions = get_string_content_for_export($pref_regions);
		$dtl[] = ($export_type=='csv'?'"'.$resp.'"':$resp);
		$dtl[] = $dtl_row['performer_type'];
		$dtl[] = $dtl_row['GSM_LANGUAGE'];
		$dtl[] = ($export_type=='csv'?'"'.$pref_regions.'"':$pref_regions);
		$dtl[] = $dtl_row['gad_score'];
		$dtl[] = $dtl_row['gad_right'];
		$dtl[] = $dtl_row['gad_wrong'];
		$dtl[] = $dtl_row['gad_no_response'];
		$dtl[] = $dtl_row['gcp_score'];
		$dtl[] = $dtl_row['gcp_rating'];
		$csv_arr[$dtl_row['gsm_id']] = $dtl;
	}
	return $csv_arr;
}

/**
 * @param string $student_id
 * @param string $drive_id
 * @param string $export_type
 * 
 * @return mixed[]
 */
function get_student_kps_dtl($student_id,$drive_id,$export_type) {
    $wh = '';
    if(intval($student_id)>0) {
        $wh .= " and gkh_student_id='$student_id' ";
    } 
    if(intval($drive_id)>0) {
        $wh .= " and gkh_visit_id='$drive_id' ";
    }
    $answers =<<<END
select gkd_response,gkd_question_id,ifnull(gdq_display_text,gdq_question) gdq_display_text,gkh_student_id
from gft_kps_dtl join gft_kps_hdr on (gkh_id=gkd_id)
join gft_drive_questions on (gdq_id=gkd_question_id)
where gdq_status='1' $wh order by gkh_student_id,gkd_question_id
END;
    $ans_res = execute_my_query($answers);
    $content = '';
    $csv_arr = array(); $answers_arr = array(); $qns_arr = array();
    while($ans_rows = mysqli_fetch_array($ans_res)) {
        $sid = $ans_rows['gkh_student_id'];
        if(!isset($answers_arr[$sid])) {
            $answers_arr[$sid] = array();
        }
        $resp = $ans_rows['gkd_response'];
        $q_id = $ans_rows['gkd_question_id'];
        $q_txt = $ans_rows['gdq_display_text'];
        if($export_type=='csv') {
            $resp = str_replace("\\n"," ",$resp);
            $q_txt = str_replace("\\n"," ",$q_txt);
        } else if($export_type=='pdf') {
            $resp = str_replace("\\n","<br/>",$resp);
            $q_txt = str_replace("\\n","<br/>",$q_txt);
        }
        $resp = get_string_content_for_export($resp);
        $answers_arr[$sid][$q_id] = $resp;
        $qns_arr[$q_id] = get_string_content_for_export($q_txt); 
    }
    $csv_arr['qns'] = $qns_arr;
    $csv_arr['ans'] = $answers_arr;
    return $csv_arr;
}
/**
 * @param string $type
 * @param string $s_id
 * @param string $round_id
 * @return string
 */
function create_campus_interview_round($type,$s_id,$round_id='') {
	global $uid;
	$round = 1;
	if($round_id!='' and is_numeric($round_id)) {
		$round = $round_id;
	} else if($type!='1') {
		$max_round = '';
		$qry = execute_my_query("select max(GCI_ROUND_NO) last_r from gft_campus_interviews where GCI_STUDENT_ID='$s_id' and GCI_TYPE='$type'");
		if($row = mysqli_fetch_array($qry)) {
			$max_round = $row['last_r'];
			$round = (int)$max_round+1;
		}
	}
	$ins_arr = array();
	$ins_arr['GCI_TYPE'] = $type;
	$ins_arr['GCI_ROUND_NO'] = $round;
	$ins_arr['GCI_STUDENT_ID'] = $s_id;
	$ins_arr['GCI_EMP_ID'] = $uid;
	$ins_arr['GCI_CREATED_DATETIME'] = date('Y-m-d H:i:s');
	$ins_arr['GCI_RESULT'] = '0';
	array_update_tables_common($ins_arr, "gft_campus_interviews", array('GCI_TYPE'=>$type,'GCI_ROUND_NO'=>$round,'GCI_STUDENT_ID'=>$s_id), null, $uid, null, null, $ins_arr);
	$rid = '';
	$rid_qry = execute_my_query("select gci_id from gft_campus_interviews where gci_type='$type' and gci_round_no='$round' and gci_student_id='$s_id'");
	if($row = mysqli_fetch_array($rid_qry)) {
		$rid = $row['gci_id'];
	}
	return $rid;
}
/**
 * @param string $campus_visit
 * @return void
 */
function show_interviews_report($campus_visit) {
	$qry =<<<END
	select gsm_role,gsm_id,gsm_visit_id,gsm_student_name,GSM_REG_NO,if(GSM_PERFORMANCE_LEVEL=1,'AP',if(GSM_ACCEPT_LOW_LEVEL=1,'IP - Yes',
	'IP - No')) as performer_type,if(GSM_GD_RESULT='1','Selected',if(GSM_GD_RESULT='2','Not Selected','Pending')) gd_res,gsm_resume_path,
	gsm_worksheet_path,group_concat(if(gci_type='2','Functional',if(gci_type='3','HR','')) separator '^^^') itype,
	group_concat(gci_round_no separator '^^^') rnd, group_concat(gem_emp_name separator '^^^') emp,
	group_concat(ifnull(GCI_RESULT_DATETIME,GCI_CREATED_DATETIME) separator '^^^') dt,group_concat(if(GCI_RESULT='1','Selected',
	if(GCI_RESULT='2','Not Selected','Pending')) separator '^^^') res,if(GSM_FINAL_RESULT='' or GSM_FINAL_RESULT is null,'Pending',GSM_FINAL_RESULT) 
	final_res,gsm_offer_letter_path from gft_student_master left join gft_campus_interviews on (gsm_id=gci_student_id and gci_type <> 1) 
	left join gft_emp_master on (gci_emp_id=gem_emp_id) where gsm_visit_id='$campus_visit' and gsm_gd_result='1' 
	group by gsm_id order by gsm_role,gsm_student_name,gci_type
END;
	// Need to add campus interview type condition => <>1
	$res = execute_my_query($qry);
	echo '<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="95%">';
	$myarr = array("S.No","Role Name","Student Name","Register No.","Peformer Type","GD Result","Resume","Worksheet","Interview Type","Employee","Result DateTime","Result","Feedback Link","Offer Finalization");
	$mysort = array("","gsm_role","gsm_student_name","gsm_reg_no","performer_type","gd_res","","","","","","","","final_res");
	sortheaders($myarr, $mysort, null, '', '');
	$val_rowspan = array();
	$sl = 0;
	while($row = mysqli_fetch_array($res)) {
		$sl++;
		$sid = $row['gsm_id'];
		$visit = $row['gsm_visit_id'];
		$sname = $row['gsm_student_name'];
		$sname_link = "<a href='student_performance.php?drive=$visit&GSM_ID=$sid' target=_blank>$sname</a>";
		$role = $row['gsm_role'];
		$reg_no = $row['GSM_REG_NO'];
		$perform = $row['performer_type'];
		$gd_res = $row['gd_res'];
		$pth = $row['gsm_resume_path'];
		$final_res = $row['final_res'];
		$dirs = explode("/",$pth);
		array_shift($dirs);
		$pth = urlencode(implode("/",$dirs));
		$wpth = $row['gsm_worksheet_path'];
		$dirs = explode("/",$wpth);
		array_shift($dirs);
		$wpth = urlencode(implode("/",$dirs));
		$resume_path = (($pth!='')?"<a href='file_download.php?type=view&filename=$pth&file_type=campus_drive' target=_blank><img src='/images/file_view.ico' alt='view' height='24' width='24' title='View/Download File'/></a>":'File not uploaded');
		$worksheet_path = (($wpth!='')?"<a href='file_download.php?type=view&filename=$wpth&file_type=campus_drive' target=_blank><img src='/images/file_view.ico' alt='view' height='24' width='24' title='View/Download File'/></a>":'File not uploaded');
		$itype_arr = explode('^^^',$row['itype']);
		$rnd_arr = explode('^^^',$row['rnd']);
		$emp_arr = explode('^^^',$row['emp']);
		$dt_arr = explode('^^^',$row['dt']);
		$io_res = explode('^^^',$row['res']);
		$offer_letter_path = $row['gsm_offer_letter_path'];
		$link_arr = '';
		$s_rows = count($itype_arr);
		$i = 0;
		$class_name = "";
		$value_arr_class = array();
		$class_names = array('Selected'=>'highlight-green','Not Selected'=>'highlight-orange','Pending'=>'highlight-lightsalmon',
				'Independent Performer'=>'highlight-green','Assisted Performer'=>'highlight-green',''=>'highlight-lightsalmon',
				'Intern'=>'highlight-darkseagreen');
		while($i<$s_rows) {
			$value_arr[0]=array();
			$value_arr_rowspan = array();
			if($i==0) {
				print_executive_name($sname);
				$value_arr[0][] = $sl;
				$value_arr[0][] = $role;
				$value_arr[0][] = $sname_link."<br/><a href=\"javascript:call_popup('edit_campus_drive_dtls.php?purpose=interview&GSM_ID=$sid&visit=$visit',5);\">[Interview]</a>";
				$value_arr[0][] = $reg_no;
				$value_arr[0][] = $perform;
				$value_arr[0][] = $gd_res;
				$class_name = "content_txt ".$class_names[$gd_res];
				$value_arr_class[5] = $class_name;
				$value_arr[0][] = $resume_path;
				$value_arr[0][] = $worksheet_path;
				$value_arr_rowspan = array($s_rows,$s_rows,$s_rows,$s_rows,$s_rows,$s_rows,$s_rows,$s_rows,1,1,1,1,1,$s_rows);
			}
			$value_arr[0][] = (($itype_arr[$i]!='' and $rnd_arr[$i]!='')?$itype_arr[$i]." - ".$rnd_arr[$i]:'');
			$value_arr[0][] = $emp_arr[$i];
			$value_arr[0][] = $dt_arr[$i];
			$value_arr[0][] = $io_res[$i];
			$class_name = "content_txt ".$class_names[$io_res[$i]]; 
			$value_arr_class[11] = $class_name;
			$itype = (($itype_arr[$i]=='Functional')?'2':'3');
			$rnd = $rnd_arr[$i];
			$value_arr[0][] = "<a href=\"javascript:call_popup('edit_campus_drive_dtls.php?purpose=interview_form&GSM_ID=$sid&visit=$visit&itype=$itype&round_no=$rnd',5);\">[View]</a>";
			if($i==0) {
				$link = "<a href=\"javascript:call_popup('edit_campus_drive_dtls.php?purpose=interview_form&GSM_ID=$sid&visit=$visit&itype=4&round_no=1',5);\">[$final_res]</a>";
				if($offer_letter_path!='') {
				    $link .= "<br/><a href='file_download.php?type=view&filename=$offer_letter_path&file_type=campus_drive' target=_blank><img src='/images/file_view.ico' alt='view' height='24' width='24' title='View/Download File'/></a>";
				}
				$value_arr[0][] = "$link";
				$class_name = "content_txt ".$class_names[$final_res];
				$value_arr_class[13] = $class_name;
			}
			print_resultset($value_arr,null,null,$value_arr_class,null,null,null,($i==0)?array($value_arr_rowspan):null);
			$i++;
		}
	}
	echo "</table>";
}
/**
 * @param string $int_id
 * @param string $itype
 * @param string $drive_id
 * @param string $s_id
 * @param string[int] $qid
 * @param string[int] $qanstype
 * @param string[int] $qidans
 * @return void
 */
function update_interview_responses($int_id,$itype,$drive_id,$s_id,$qid,$qanstype,$qidans) {
	global $uid;
	$show_resume_upload = false;
	for($i=0;$i<count($qid);$i++) {
		$q_id = $qid[$i];
		$type = isset($qanstype[$i])?$qanstype[$i]:'';
		$val = ($type=='2')?$qidans[(int)$q_id][0]:$qidans[(int)$q_id];
		$resp = isset($val)?mysqli_real_escape_string_wrapper($val):'';
		$ins_arr = array();
		if($q_id=='442' or $q_id=='445' or $q_id=='454') {
			if($resp=='Selected' or $resp=='Yes' ) {
				$result = '1';
				if($q_id=='442') {
					$show_resume_upload = true;
				}
			} else {
				$result = '2';
			}
			execute_my_query(" update gft_campus_interviews set GCI_RESULT='$result',gci_result_datetime=now() where GCI_ID='$int_id' ");
			if($itype=='1') {
				execute_my_query(" update gft_student_master set GSM_GD_RESULT='$result', GSM_GD_EMP='$uid', GSM_GD_RESULT_DATETIME=now() ".
						" where gsm_id='$s_id' and gsm_visit_id='$drive_id' ");
			}
			continue;
		}
		$ins_arr['GCD_ID'] = $int_id;
		$ins_arr['GCD_QUESTION_ID'] = $qid[$i];
		$ins_arr['GCD_RESPONSE'] = trim($resp);
		array_update_tables_common($ins_arr,"gft_campus_interview_dtl",array('GCD_ID'=>$int_id,"GCD_QUESTION_ID"=>$q_id),null,$uid, null,null,$ins_arr);
	}
	if($show_resume_upload) {
		show_my_alert_msg("Sucessfully updated result");
		js_location_href_to("/../edit_campus_drive_dtls.php?purpose=upload_resume&GSM_ID=$s_id&visit=$drive_id");
	} else {
		show_my_alert_msg("Sucessfully updated result");
		echo<<<END
		<script type="text/javascript">top.window.close();</script>
END;
	}
}
?>
