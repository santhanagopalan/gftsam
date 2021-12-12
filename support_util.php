<?php
/*. forward string function get_team_condition_for_param(string $team,string $gem_alias=,string $chatbot_check_col=,string $chatbot_column=); .*/
require_once(__DIR__ ."/common_util.php");
require_once(__DIR__ ."/common_query_util.php");
require_once(__DIR__."/zohoMailAPI.php");
//global $received_in_ho;
$testing_purpose='true';

/**
 * @param string $access_lead_str
 * @param string $id_condition
 * @param string $wh_condition
 * @return string $que
 */

function get_support_tickets_query($access_lead_str,$id_condition,$wh_condition) {
    $cur_date = date('Y-m-d H:i:s');
	$que =" select GLH_LEAD_CODE,GCH_COMPLAINT_ID, las.GCD_PROBLEM_SUMMARY, las.GCD_PROBLEM_DESC, GLH_CUST_NAME, GLH_CUST_STREETADDR2, GFT_COMPLAINT_DESC, GBI_NAME,em1.GEM_EMP_NAME as GEM_EMP_NAME,em2.gem_emp_name created_by, ".
			" GCH_CURRENT_STATUS, GTM_NAME, GCH_COMPLAINT_DATE, las.GCD_ESCALATION,GTM_GROUP_ID, if(GTM_GROUP_ID=3,'Y','N') as terminal, las.GCD_PROMISE_DATE, GSR_RATING_VALUE, ".
			" case GCH_ESCALATION_NTIMES when 0 then GBI_RESTORE_PERIOD when 1 then GBI_RESTORE_PERIOD+GBI_L1_PERIOD ".
			" when 2 then GBI_RESTORE_PERIOD+GBI_L1_PERIOD+GBI_L2_PERIOD when 3 then GBI_RESTORE_PERIOD+GBI_L1_PERIOD+GBI_L2_PERIOD+GBI_L3_PERIOD ".
			" when 4 then GBI_RESTORE_PERIOD+GBI_L1_PERIOD+GBI_L2_PERIOD+GBI_L3_PERIOD+GBI_L4_PERIOD ELSE 0 END as escalate_time, ".
			" gch_fixed_in_version,gid_current_version,pfm.GPM_PRODUCT_ABR as product_name, GSS_DESC as SUB_STATUS,if(las.gcd_status='T2',GSS_DESC,'') as dev_sub_status, las.GCD_SUB_STATUS AS SUB_STATUS_ID,".
			" GDS_NAME AS SERVICE_TYPE,DATE_FORMAT(las.GCD_PROMISE_DATE , '%D, %b %Y') as edc,GSM_NAME as severity,".
			" las.GCD_SERVICE_TYPE as SERVICE_TYPE_ID,DATEDIFF('$cur_date',GCH_COMPLAINT_DATE) as age,if('$cur_date'>las.GCD_PROMISE_DATE,DATEDIFF('$cur_date',las.GCD_PROMISE_DATE),'') as edc_skip,las.gcd_status as curr_status,if(GCH_OWNERSHIP=1,'Customer','GFT') as owner from gft_customer_support_hdr ".
			" join gft_customer_support_dtl las on (las.gcd_activity_id=GCH_LAST_ACTIVITY_ID) ".
			" join gft_customer_support_dtl fir on (fir.gcd_activity_id=GCH_FIRST_ACTIVITY_ID) ".
			" left join gft_install_dtl_new on (gid_lic_pcode=gch_product_code and substr(gid_lic_pskew,1,4)=gch_product_skew ".
			" and gid_status!='U' and gid_lead_code=gch_lead_code) ".
			" left join gft_product_master pm on (pm.gpm_product_code=gid_lic_pcode and pm.gpm_product_skew=gid_lic_pskew and ".
			" gpm_product_type!=8 and gft_skew_property in (1,11) and gpm_license_type!=3) ".
			" left join gft_product_family_master pfm on (pfm.gpm_product_code=gch_product_code and ".
			" pfm.gpm_is_internal_product in (0,2,3)) join gft_lead_hdr on (GLH_LEAD_CODE=GCH_LEAD_CODE) ".
			" join gft_status_master on (GCH_CURRENT_STATUS=GTM_CODE) ".
			" join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE and GFT_INTERNAL_COMPLAINT=0) ".
			" left join gft_emp_master em1 on (em1.GEM_EMP_ID=las.GCD_PROCESS_EMP) ".
			" left join gft_emp_master em2 on (em2.GEM_EMP_ID=fir.GCD_EMPLOYEE_ID) ".
			" left join gft_support_rating on (GCH_COMPLAINT_ID=GSR_COMPLAINT_ID AND GSR_RATED_TYPE IN(1,2)) ".
			" left join gft_business_impact_master on (GBI_ID=GCH_BUSINESS_IMPACT) ".
			" left join gft_cust_imp_ms_current_status_dtl ms on(GIMC_COMPLAINT_ID=GCH_COMPLAINT_ID)".
			" left join gft_sub_status_master ON(GSS_ID=las.GCD_SUB_STATUS)".
			" left join gft_dev_service_type_master ON (las.GCD_SERVICE_TYPE=GDS_ID)".
			" left join gft_severity_master on (las.GCD_SEVERITY=GSM_CODE) ".
			" where GLH_LEAD_CODE in ($access_lead_str) $id_condition $wh_condition ".
			" AND ms.GIMC_COMPLAINT_ID IS NULL ";
	return $que;
}
/**
 * @param int $id
 * 
 * @return void
 */
function show_support_hdr($id){
	global $alt_row_class;
	
	$query="SELECT g.GCH_COMPLAINT_ID, g.GCH_LAST_ACTIVITY_ID, g.GCH_COMPLAINT_DATE, lh.GLH_TERRITORY_ID, " .
			"lh.GLH_LEAD_CODE, lh.GLH_CUST_NAME , concat(lh.GLH_DOOR_APPARTMENT_NO,',', lh.GLH_BLOCK_SOCEITY_NAME,',',lh.GLH_STREET_DOOR_NO, lh.GLH_CUST_STREETADDR1), lh.GLH_CUST_STREETADDR2, " .
			"lh.GLH_CUST_PINCODE, c.GFT_COMPLAINT_DESC, p.GPM_PRODUCT_NAME, g.GCH_VERSION, " .
			"g.GCH_PRODUCT_SKEW, s.GTM_NAME, dtl.GCD_PROBLEM_SUMMARY,  " .
			" g.GCH_FIXED_IN_VERSION,GCH_RESTORE_TIME,GCH_READY_TO_SUPPORT, GCH_ASSIGN_TIME, GCH_SOLVED_TIME  " .
			" FROM gft_customer_support_hdr g " .
			" join gft_customer_support_dtl dtl on (gch_complaint_id= gcd_complaint_id and gch_last_activity_id=gcd_activity_id ) " .
			" JOIN gft_status_master s ON (s.GTM_CODE = g.GCH_CURRENT_STATUS ) ".
			" join gft_lead_hdr lh on(g.GCH_LEAD_CODE=lh.GLH_LEAD_CODE) " .
			" LEFT JOIN gft_complaint_master c ON (g.GCH_COMPLAINT_CODE = c.GFT_COMPLAINT_CODE) " .
			" LEFT JOIN gft_product_family_master p ON (g.GCH_PRODUCT_CODE=p.GPM_PRODUCT_CODE) ".
			" where g.GCH_COMPLAINT_ID = $id ";
	$myarr=array("Support Id", "Last Activity Id","Complaint date","Territory Id",
			 "Lead Code", "Customer name","Address","Location", 
			 "Pin code","Complaint" ,"Product","Version","Product Skew", "Current Status","Summary", 
			 "Fixed in version","Restore Time","Ready to Support","Assign Time","Solved time");

	$result=execute_my_query($query,'Support_util.php');
			 
	
echo<<<END
<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="400">
          <tr height="30" class="formHeader" >
				<td align="center">Title</td>
                <td align="center">value</td>
          </tr>
END;
	$s=0;
	if($query_data=mysqli_fetch_array($result)){
		for($i = 0; $i<count($myarr);$i++){
echo<<<END
<tr height=20 class=$alt_row_class[$s] onMouseOver=this.style.backgroundColor='#C8DC9B'; onMouseOut=this.style.backgroundColor='';>
<td class="content_txt" width =100 align=center>$myarr[$i]</td>
<td class="content_txt" width =300 align=left>$query_data[$i]</td>
</tr>
END;
			$s= ($s==1?0:1);
		}
	}
echo "</table>";
}

/**
 * @param string $custCode
 * 
 * @return void
 */
function cust_support_status_summary($custCode){
	$q2='';
	$qo_total='';
	$qo_total_select='';
	$ex_field_name='';
	$field_name='';
	$sa='';$q1="";

	$stcode=/*. (string[int]) .*/ array();

	$statusq="SELECT GTM_CODE,GTM_NAME,gtm_group_id FROM gft_status_master where GTM_STATUS='A' order by prob desc ";
	$result=execute_my_query($statusq);
	$stdes= array("S.No","Product","Total");
	$mysort=array("","GPM_PRODUCT_NAME,GPG_VERSION","USI");
	for($i=0;$data=mysqli_fetch_array($result);$i++){
		$stcode[$i]=$data['GTM_CODE'];
		array_push($stdes,$data['GTM_NAME']);
		$staprob[$i]=$data['gtm_group_id'];
		$q2.="sum(if(h.GCH_CURRENT_STATUS ='$stcode[$i]',1,0)) as 'ST_{$stcode[$i]}',";
		$qo_total.=",@t{$stcode[$i]} :=0  ";
		$qo_total_select.=",@t{$stcode[$i]} ";
		$field_name.=",ST_{$stcode[$i]}";
		$ex_field_name.=",ST_{$stcode[$i]} as '{$data[1]}' ";
		array_push($mysort,"ST_{$stcode[$i]}");
		if($staprob[$i]==3 or $staprob[$i]==4 ){
			$sa.="'$stcode[$i]',";
		}
	}
	$sa=substr($sa,0,-1);
	$q2=substr($q2,0,-1);
	$query= " select GPM_DESC, gpg_version, GPM_HEAD_FAMILY,pg.gpg_skew, sum(if(h.GCH_CURRENT_STATUS NOT IN ($sa),1,0)) USI,{$q1}{$q2} ";
	$query.=" FROM (gft_customer_support_hdr h,gft_product_family_master g,gft_product_group_master pg )  " .
	        " join gft_lead_hdr lh on (glh_lead_code=gch_lead_code) " .
			" join  gft_customer_support_dtl d " .
			" on(h.GCH_COMPLAINT_ID=d.GCD_COMPLAINT_ID AND h.GCH_LAST_ACTIVITY_ID = d.GCD_ACTIVITY_ID) ";
	$query.=" where gpm_product_code = GCH_PRODUCT_CODE AND GCH_product_skew =pg.gpg_skew  " .
			" and gpg_product_family_code=GPM_HEAD_FAMILY and gch_lead_code=$custCode";
	$query.=" GROUP BY concat(GPM_HEAD_FAMILY,'-',gpg_skew)" ;
$result=execute_my_query($query);
print_dtable_header("Support Status Summary");
echo<<<END
<table cellpadding="0" cellspacing="2"  border="0"  class="FormBorder1" >
END;
	sortheaders($stdes,null,null,null,null);	
echo "<tbody>";
$sl=1;
while($datar=mysqli_fetch_array($result)){
	echo "<tr style=\"height:20\"  onMouseOver=\"this.style.backgroundColor='#C8DC9B';\" onMouseOut=\"this.style.backgroundColor='';\">";
	echo "<td valign=TOP align=CENTER class=\"content_txt\">$sl</td>\n";
	echo "<td valign=TOP align=CENTER class=\"content_txt\">{$datar['GPM_DESC']}-{$datar['gpg_version']}</td>\n";
	echo "<td valign=TOP align=CENTER class=\"content_txt\">{$datar['USI']}</td>\n";
	for($i=0;$i<count($stcode);$i++){
		echo "<td valign=TOP align=CENTER class=\"content_txt\">".$datar["ST_{$stcode[$i]}"]."</td>\n";
	}
	echo"</tr>";
	$sl++;
}
echo "</table>";
}

/**
 * @param string $topic
 * @param string $from_dt
 * @param string $to_dt
 * @param string $product
 * @param string $level
 * @param string $priority
 * @param string $status
 * @param string $severity
 * @param string $cust_name
 * @param string $uid
 * @param string $exec_id
 * @param string $reason
 * @param string $visit_date
 * @param string $skew
 * @param string $version
 * @param string $cust_emotion
 * @param string $complaint_code
 * 
 * @return void
 */
function showsupport_group_summary_support($topic,$from_dt,$to_dt,$product,$level,
				$priority,$status,$severity,$cust_name,$uid,
				$exec_id,$reason,$visit_date,
				$skew,$version,$cust_emotion,$complaint_code){
	$q1="";
	//global $alt_row_class;
	$qo_total="";
	$qo_total_select="";
	$qo_field="";
	$field_name="";
	$ex_field_name="";
	$sa="";
	$familyver='';
	$sid=explode('-',$product);
	$product1=$sid[0];
	if(isset($sid[1])){
		$familyver=$sid[1];
	}
	$date_on=date('Y-m-d',time());
	
	$myarr=array("S.No","Group Name");
	$mysort=array("","GSP_GROUP_NAME");
	$rowspan_arr=array("2","2");
	$colspan_arr=array("1","1");
	$value_arr_total=array("N","N");
	$value_arr_align=array("Right","Left");
	$report_link=array("","");
	$stcode=/*. (string[int]) .*/ array();
	
	$statusq="SELECT GTM_CODE,GTM_NAME,gtm_group_id,GTM_ASSURE_STATUS FROM gft_status_master where GTM_STATUS='A' 
	and GTM_ASSURE_STATUS='Y' order by gtm_group_id ";
	$result=execute_my_query($statusq);
	$assure_status_num=mysqli_num_rows($result);
	$select_query_list="";
	$outer_select_query_list="";
	if($assure_status_num>0){
		array_push($myarr,"Assure Pending");
		array_push($colspan_arr,($assure_status_num+1));
		array_push($rowspan_arr,"1");$myarr_sub=array();
		$total_put_comma="";$put_comma="";
		$total_list_of_status="";
		$i=0;
		while($data=mysqli_fetch_array($result)){
			$stcode[$i]=$data['GTM_CODE'];
			array_push($myarr_sub,$data['GTM_NAME']);
			array_push($mysort,"ST_{$stcode[$i]}");
			$select_query_list.=$put_comma."sum(if(hdr.GCH_CURRENT_STATUS ='$stcode[$i]',1,0)) as 'ST_{$stcode[$i]}' ";
			$outer_select_query_list.=$put_comma."ST_{$stcode[$i]}";	
			//$link="support_report.php?support_status=".$stcode[$i]."&from_dt=&to_dt=";
			//array_push($report_link,$link);
			array_push($value_arr_total,'Y');
			array_push($value_arr_align,"Right");
			$total_list_of_status.=$put_comma."'$stcode[$i]'";
			$put_comma=",";$i++;
		}
		$select_query_list.=$put_comma."sum(if(hdr.GCH_CURRENT_STATUS IN ($total_list_of_status), 1,0)) total_list_of_status ";
		$outer_select_query_list.=$put_comma."total_list_of_status";
		array_push($myarr_sub,"Total");
		array_push($mysort,"total_list_of_status");
		array_push($report_link,"");
		array_push($value_arr_total,'Y');
		array_push($value_arr_align,"Right");
		
	}	
	
	$put_comma='';

	/*other than Assure*/
	$statusq="SELECT GTM_CODE,GTM_NAME,gtm_group_id,GTM_ASSURE_STATUS FROM gft_status_master 
	where GTM_STATUS='A' and GTM_ASSURE_STATUS='N' and GTM_CODE!='T1' order by gtm_group_id ";
	$result=execute_my_query($statusq);
	$nassure_status_num=mysqli_num_rows($result);
	if($nassure_status_num>0){
		if($select_query_list!=''){	$put_comma=","; }else{ $put_comma=""; }
		for($i=0;$data=mysqli_fetch_array($result);$i++){
			$stcode[$i]=$data['GTM_CODE'];
			array_push($myarr,$data['GTM_NAME']);
			array_push($mysort,"ST_{$stcode[$i]}");
			array_push($colspan_arr,"1");
			array_push($rowspan_arr,"2");
			//$link="support_report.php?support_status=".$stcode[$i]."&from_dt=&to_dt=";
			//array_push($report_link,$link);
			array_push($value_arr_total,'Y');
			array_push($value_arr_align,"Right");
			$staprob[$i]=$data['gtm_group_id'];
			$select_query_list.=$put_comma."sum(if(hdr.GCH_CURRENT_STATUS ='$stcode[$i]', 1,0)) as 'ST_{$stcode[$i]}' ";
			$outer_select_query_list.=$put_comma."ST_{$stcode[$i]}";
			$put_comma=",";
		}
				
	}	
	global $region_id,$zone_id,$terr_id,$district_id,$state_id,$country_id,$area_id;
	$query= " SELECT spg.GSP_GROUP_ID,GSP_GROUP_NAME,$outer_select_query_list    ";//,cust
	$query.=" from gft_support_product_group spg  ";
	$query.=" join ( select spg.GSP_GROUP_ID, $select_query_list ";
	$query.=" FROM gft_customer_support_hdr hdr " .
			" join gft_lead_hdr lh on (glh_lead_code=gch_lead_code)" .
			" join gft_product_family_master g on (GPM_PRODUCT_CODE = GCH_PRODUCT_CODE)" .
			" join gft_product_group_master pg on (GCH_PRODUCT_SKEW =pg.GPG_SKEW and GPG_PRODUCT_FAMILY_CODE=GPM_HEAD_FAMILY)" .
			" join gft_support_product_group spg on (spg.GSP_GROUP_ID=GPG_SUPPORT_GROUP_ID and  spg.GSP_STATUS='A')" .
			" JOIN gft_complaint_master cm ON (hdr.GCH_COMPLAINT_CODE= cm.GFT_COMPLAINT_CODE)" ; 
								
	if($terr_id!=0 or $region_id!=0 or $zone_id!=0 or $district_id!=0 or $state_id!=0 or $country_id!=0){ 	
		$query.=get_query_area_mapping($terr_id,$area_id,$region_id,$zone_id,$district_id,$state_id,$country_id);
		$areamap_link=get_areamap_link();
	}
	$query.=" where (1)  ";
	$query.=check_common_support_dtl();
	$query .= " and GSP_STATUS='A' ";
	if($product!='' && $product!=0){
		$query.=" and pg.gpg_product_family_code = $product1 AND pg.GPG_SKEW ='$familyver' ";
	}
	$query.=" GROUP BY spg.GSP_GROUP_ID ) sp ON (spg.GSP_GROUP_ID=sp.GSP_GROUP_ID) ";
    $query.= " GROUP BY spg.GSP_GROUP_ID " ;
	$order_by=" GSP_GROUP_NAME ";
    generate_reports("Support Summary",$query,$myarr,$mysort,$sms_category=null,$email_category=null,
		$previous_months_link=null,$value_arr_align=null,$myarr_width=null,$total_query=null,$sec_field_arr=null,
		/*$myarr_sub*/ null,$rowspan_arr,$colspan_arr,$myarr_sub1=null,$rowspan_arr1=null,$colspan_arr1=null,
		$report_link,$show_in_popup=null,$scorallable_tbody=false,$navigation=false,$order_by,$heading=true,
		$value_arr_total,$sorting_required=true,$child=0,$trow_name=null,$emyarr=null,$emyarr_sub=null,
		$pass_only_this_arg=null,$export_index=0);
}

/**
 * @param string $topic
 * @param string $from_dt
 * @param string $to_dt
 * @param string $product
 * @param string $level
 * @param string $priority
 * @param string $status
 * @param string $severity
 * @param string $cust_name
 * @param string $uid
 * @param string $exec_id
 * @param string $reason
 * @param string $visit_date
 * @param string $skew
 * @param string $version
 * @param string $cust_emotion
 * @param string $complaint_code
 * 
 * @return void
 */
function show_all_summary_support($topic,$from_dt,$to_dt,$product,$level,
				$priority,$status,$severity,$cust_name,$uid,
				$exec_id,$reason,$visit_date,
				$skew,$version,$cust_emotion,$complaint_code){
	$q1="";
	//global $alt_row_class;
	$qo_total="";
	$qo_total_select="";
	$qo_field="";
	$field_name="";
	$ex_field_name="";
	$sa="";
	$familyver='';
	$sid=explode('-',$product);
	$product1=$sid[0];
	if(isset($sid[1])){
		$familyver=$sid[1];
	}
	$date_on=date('Y-m-d',time());
	/*$myarr=array("S.No","Product","Customers");
	$mysort=array("","prod_abr","cust");
	$rowspan_arr=array("2","2","2");
	$colspan_arr=array("1","1","1");
	$value_arr_total=array("N","N","Y");
	$value_arr_align=array("Right","Left","right");
	$report_link=array("","");
	$report_link[2]="comp_license.php?from_dt=&to_dt";
	*/
	$myarr=array("S.No","Product");
	$mysort=array("","prod_abr");
	$rowspan_arr=array("2","2");
	$colspan_arr=array("1","1");
	$value_arr_total=array("N","N");
	$value_arr_align=array("Right","Left");
	$report_link=array("","");
	
	$statusq="SELECT GTM_CODE,GTM_NAME,gtm_group_id,GTM_ASSURE_STATUS FROM gft_status_master where GTM_STATUS='A' 
	and GTM_ASSURE_STATUS='Y' order by gtm_group_id ";
	$result=execute_my_query($statusq);
	$assure_status_num=mysqli_num_rows($result);
	$select_query_list="";
	$outer_select_query_list="";

	if($assure_status_num>0){
		array_push($myarr,"Assure Pending");
		array_push($colspan_arr,($assure_status_num+1));
		array_push($rowspan_arr,"1");$myarr_sub=array();
		$total_put_comma="";$put_comma="";
		$total_list_of_status="";
		$i=0;
		while($data=mysqli_fetch_array($result)){
			$stcode[$i]=$data['GTM_CODE'];
			array_push($myarr_sub,$data['GTM_NAME']);
			array_push($mysort,"ST_{$stcode[$i]}");
			$select_query_list.=$put_comma."sum(if(hdr.GCH_CURRENT_STATUS ='$stcode[$i]' and DATE(hdr.GCH_COMPLAINT_DATE)<=date('$date_on') ,1,0)) as 'ST_{$stcode[$i]}' ";
			$outer_select_query_list.=$put_comma."ST_{$stcode[$i]}";	
			$link="support_report.php?support_status[]=".$stcode[$i]."&from_dt=&to_dt=";
			array_push($report_link,$link);
			array_push($value_arr_total,'Y');
			array_push($value_arr_align,"Right");
			$total_list_of_status.=$put_comma."'$stcode[$i]'";
			$put_comma=",";$i++;
		}
		$select_query_list.=$put_comma."sum(if(hdr.GCH_CURRENT_STATUS IN ($total_list_of_status) 
		and DATE(hdr.GCH_COMPLAINT_DATE)<=date('$date_on') ,1,0)) total_list_of_status ";
		$outer_select_query_list.=$put_comma."total_list_of_status";
		array_push($myarr_sub,"Total");
		array_push($mysort,"total_list_of_status");
		array_push($report_link,"");
		array_push($value_arr_total,'Y');
		array_push($value_arr_align,"Right");
		
	}	

	$put_comma='';

	/*other than Assure*/
	$statusq="SELECT GTM_CODE,GTM_NAME,gtm_group_id,GTM_ASSURE_STATUS FROM gft_status_master 
	where GTM_STATUS='A' and GTM_ASSURE_STATUS='N' and GTM_CODE!='T1' order by gtm_group_id ";
	$result=execute_my_query($statusq);
	$nassure_status_num=mysqli_num_rows($result);
	if($nassure_status_num>0){
		if($select_query_list!=''){	$put_comma=","; }else{ $put_comma=""; }
		for($i=0;$data=mysqli_fetch_array($result);$i++){
			$stcode[$i]=$data['GTM_CODE'];
			array_push($myarr,$data['GTM_NAME']);
			array_push($mysort,"ST_{$stcode[$i]}");
			array_push($colspan_arr,"1");
			array_push($rowspan_arr,"2");
			$link="support_report.php?support_status[]=".$stcode[$i]."&from_dt=&to_dt=";
			array_push($report_link,$link);
			array_push($value_arr_total,'Y');
			array_push($value_arr_align,"Right");
			$staprob[$i]=$data['gtm_group_id'];
			$select_query_list.=$put_comma."sum(if(hdr.GCH_CURRENT_STATUS ='$stcode[$i]' and DATE(hdr.GCH_COMPLAINT_DATE)<=date('$date_on') ,1,0)) as 'ST_{$stcode[$i]}' ";
			$outer_select_query_list.=$put_comma."ST_{$stcode[$i]}";
			$put_comma=",";
		}
	}	
	global $region_id,$zone_id,$terr_id,$district_id,$state_id,$country_id,$area_id;
	$query= " SELECT g.GPM_PRODUCT_ABR, g.GPM_head_family,pg.gpg_version,pg.gpg_skew,
	concat(g.GPM_PRODUCT_CODE,'-',pg.gpg_skew) as prod,
	concat(g.GPM_PRODUCT_ABR,'-',pg.gpg_version) as prod_abr,$outer_select_query_list    ";//,cust
	$query.=" from gft_product_family_master g  " .
			" join gft_product_group_master pg on (GPG_PRODUCT_FAMILY_CODE=g.GPM_HEAD_FAMILY) ";
	$query.=" left join ( select GPM_HEAD_FAMILY,pg.gpg_skew , $select_query_list ";
	$query.=" FROM gft_customer_support_hdr hdr " .
			" join gft_product_family_master g on(GPM_PRODUCT_CODE = GCH_PRODUCT_CODE and GPM_STATUS='A')" .
			" join gft_product_group_master pg on (GCH_PRODUCT_SKEW =pg.GPG_SKEW and GPG_PRODUCT_FAMILY_CODE=GPM_HEAD_FAMILY)  " .
			" join gft_customer_support_dtl dtl on (hdr.GCH_COMPLAINT_ID=dtl.GCD_COMPLAINT_ID AND hdr.GCH_LAST_ACTIVITY_ID = dtl.GCD_ACTIVITY_ID) " .
			" JOIN gft_complaint_master cm ON hdr.GCH_COMPLAINT_CODE= cm.GFT_COMPLAINT_CODE " ; 
								
	if(($terr_id!='' or $region_id!='' or $zone_id!='' or $district_id!='' or $state_id!='' or $country_id!='') and 
	($terr_id!=0 or $region_id!=0 or $zone_id!=0 or $district_id!=0 or $state_id!=0 or $country_id!=0)){ 	
		$query.=" join gft_lead_hdr lh on (glh_lead_code=hdr.gch_lead_code) ";
		$query.=get_query_area_mapping($terr_id,$area_id,$region_id,$zone_id,$district_id,$state_id,$country_id);
		$areamap_link=get_areamap_link();
	}
	$query.=" where (1)  ";
	$query.=check_common_support_dtl();
	$query.=" GROUP BY concat(GPM_HEAD_FAMILY,'-',GPG_SKEW) ) sp ON (sp.GPM_HEAD_FAMILY=g.GPM_HEAD_FAMILY and sp.gpg_skew=pg.gpg_skew ) ";
	/*$query.=" left join (select GPM_head_family,gpg_skew, count(*) cust " .
			" from gft_product_family_master pfm " .
			" join gft_install_dtl_new gid_mn on (GID_LIC_PCODE=pfm.GPM_PRODUCT_CODE and GID_STATUS='A')".
			" join gft_order_hdr oh on (god_order_no=gid_order_no and god_order_status='A')" .
			" join gft_product_group_master pgm on (GPG_PRODUCT_FAMILY_CODE=GPM_HEAD_FAMILY and GID_LIC_PSKEW like concat(gpg_skew,'%')) ";
	if($terr_id!='' or $region_id!='' or $zone_id!='' or $district_id!=0 or $state_id!=0 or $terr_id!=0 or $region_id!=0 or $zone_id!=0 ){ 	
		$query.=" join gft_lead_hdr ilh on (gid_lead_code=ilh.glh_lead_code) ";
		$query.=get_query_area_mapping($terr_id,$area_id,$region_id,$zone_id,$district_id,$state_id,$country_id,'','','ilh.glh_territory_id','ilh.glh_district_id'); 
	}
	$query.=" where (1) ";
	$query.=" GROUP BY concat(GPM_HEAD_FAMILY,'-',GPG_SKEW) )  cc " .
			" on (g.GPM_HEAD_FAMILY =cc.GPM_HEAD_FAMILY and cc.GPG_SKEW like pg.gpg_skew)  ";*/
	//$query .= " where GPM_STATUS='A' ";
	if($product!='' && $product!=0){
		$query.=" and pg.gpg_product_family_code = $product1 AND pg.GPG_SKEW ='$familyver' ";
	}
    $query.= " GROUP BY concat(g.GPM_HEAD_FAMILY,'-',pg.GPG_SKEW) " ;
    		//"HAVING total_list_of_status!=0 and total_list_of_status!='' ";

	$order_by=" gpm_display_order ";
    generate_reports("Support Summary",$query,$myarr,$mysort,$sms_category=null,$email_category=null,
		$previous_months_link=null,$value_arr_align=null,$myarr_width=null,$total_query=null,$sec_field_arr=null,
		/*$myarr_sub*/null,$rowspan_arr,$colspan_arr,$myarr_sub1=null,$rowspan_arr1=null,$colspan_arr1=null,
		$report_link,$show_in_popup=null,$scorallable_tbody=false,$navigation=false,$order_by,$heading=true,
		$value_arr_total,$sorting_required=true,$child=0,$trow_name=null,$emyarr=null,$emyarr_sub=null,
		$pass_only_this_arg=null,$export_index=1);
}//end of function

/**
 * @param string $custCode
 * 
 * @return void
 */
function cust_support_summary($custCode){
	$put_comma='';
	$sum_of_reason='';
	$nature_list=get_two_dimensinal_array_from_table('gft_complaint_nature_master','GCM_NATURE_ID','GCM_NATURE','GCM_NATURE_STATUS','A');
	$sum_of_nature="";
	$myarr= /*. (string[int]) .*/array();
	$alt_row_class=array();
	for($i=0;$i<count($nature_list);$i++){
		
		$nature_id=$nature_list[$i][0];
		$nature_name=$nature_list[$i][1];
		$sum_of_nature.=" $put_comma sum(case when GCD_NATURE='$nature_id' then 1 else 0 end) `$nature_name` ";
		$put_comma=",";
		$myarr[]=$nature_name;
	}
	$sum_of_nature.=",count(GCD_NATURE) `Total` ";
		$myarr[]="Total ";
	$query=" select $sum_of_nature " .
		" from gft_customer_support_dtl a,gft_customer_support_hdr hdr " .
 		" where  gch_lead_code =$custCode and gch_complaint_id=gcd_complaint_id ";
 	print_dtable_header("Support Summary vs Nature of Activity ");			
echo<<<END
<table cellpadding="0" cellspacing="2"  border="0"  class="FormBorder1" width="100%" >
<tbody><tr style="height:20" class="moduleListTitle">
END;
	for($i=0;$i<count($myarr);$i++){
		echo '<th align="center" class="header_without_link">'.$myarr[$i].'</th>';
	}//end of for
	$param="custCode=$custCode";
	if($query_data=mysqli_fetch_array(execute_my_query($query,'Support_util.php'))){
		if(!isset($alt_row_class[1]))$alt_row_class[1]='';
		echo "<tr style=\"height:20\" class=\"$alt_row_class[1]\" onMouseOver=\"this.style.backgroundColor='#C8DC9B';\" onMouseOut=\"this.style.backgroundColor='';\">";
		for($i=0;$i<count($nature_list);$i++){
			$nature_id=$nature_list[$i][0];
			$link=$query_data[$i]!=0 ?"<a href=\"supporthistory.php?$param&amp;support_activity=$nature_id\">$query_data[$i]</a>" :$query_data[$i];
			echo "<td align=\"center\">".$link."</td>";
		}
			echo "<td align=\"center\"><a href=\"supporthistory.php?$param\">".$query_data['Total']."</a></td>";
	}
	echo "</tr></tbody></table>\n";
	print_dtable_footer();
	$reason_list =get_activity_list($other_users=null,$next_visit_nature=false,$visit_nature=false,
		$sales_planning=false,$act_id=null,$activity_nature="0",$support_activity=true);
	$put_comma="";
	for($i=0;$i<count($reason_list);$i++){
	
		$reason_id=$reason_list[$i][0];
		$reason_name=$reason_list[$i][1];
		$sum_of_reason.=" $put_comma sum(case when a.GCD_VISIT_REASON='$reason_id' then 1 else 0 end) `$reason_name` ";
		$put_comma=",";
		
	}	
	$sum_of_reason.=",sum(case when a.GCD_NATURE=1 then 1 else 0 end) `Total` " .
			",sum(gld_distance_travelled) `Distance Travelled` ";
		
	$query=" select $sum_of_reason " .
	 		" from gft_customer_support_hdr hdr " .
	 		" join gft_customer_support_dtl a on (gch_complaint_id=gcd_complaint_id and GCD_NATURE=1 )" .
	 		" join gft_activity ga on(a.gcd_activity_id=ga.gld_reffer_id)" .
	 		" where gch_lead_code =$custCode ";
	$result=execute_my_query($query,'Support_util.php');
	print_dtable_header("Support Summary vs Visit Reason");			
echo<<<END
<table cellpadding="0" cellspacing="2"  border="0"  class="FormBorder1" width="100%" >
<tbody>
	<tr style="height:20" class="moduleListTitle">
END;
	$value_arr=/*. (string[int][int]) .*/ array();
	$value_arr_align=/*. (string[int]) .*/ array();

	$query_data=mysqli_fetch_array($result);
	for($i=0;$i<mysqli_num_fields($result);$i++){
		echo  '<th align="center" class="header_without_link" > '.mysqli_field_name_wrapper($result,$i).'</th>';
		$value_arr[0][]=$query_data[$i];
		$value_arr_align[]="center";
	}
	print_resultset($value_arr,'',$value_arr_align);
	echo"</table>";
	print_dtable_footer();

}
/**
 * @param string $param
 * @param string $return_type
 * @return string
 */
function get_groups_for_param($param, $return_type='') {
    $gps = '';
    $filter_string = "";
    $emp_team_group = '';
    $partner_support_groups = "24";
    $employee_support_groups = "13";
    switch ($param) {
        case '1':
            $gps = '4,21';
            $filter_string = "support_product_group_multi[]=4&support_product_group_multi[]=21";
            $emp_team_group = "86";
            $partner_support_groups = "24,42";
            $employee_support_groups = "13,48";
            break;
        case '2':
            $gps = '5,20';
            $filter_string = "support_product_group_multi[]=5&support_product_group_multi[]=20";
            $emp_team_group = "81";
            $partner_support_groups = "24,41";
            $employee_support_groups = "13,47";
            break;
        case '3':
            $gps = '3,22,23';
            $filter_string = "support_product_group_multi[]=3&support_product_group_multi[]=22&support_product_group_multi[]=23";
            $emp_team_group = "4";
            $partner_support_groups = "24,43";
            $employee_support_groups = "13,49";
            break;
        case '4':
            $gps = '6';
            $filter_string = "support_product_group_multi[]=6";
            $emp_team_group = "5";
            $partner_support_groups = "24,44";
            $employee_support_groups = "13,50";
            break;
        case '5':
            $gps = '7';
            $filter_string = "support_product_group_multi[]=7";
            $emp_team_group = "14";
            $partner_support_groups = "24,45";
            $employee_support_groups = "13,51";
            break;
        case '6':
            $gps = '1';
            $filter_string = "support_product_group_multi[]=1";
            $emp_team_group = "74";
            break;
        case '7':
            $gps = '17'; // presales and presales intl
            $filter_string = "support_product_group_multi[]=17";
            $emp_team_group = "69";
            break;
        case '8':
            $gps = '12'; // TRAC
            $filter_string = "support_product_group_multi[]=12";
            $emp_team_group = "15";
            break;
        case '9':
            $gps = '36';
            $filter_string = "support_product_group_multi[]=36";
            $emp_team_group = "102";
            $partner_support_groups = "24,46";
            $employee_support_groups = "13,52";
            break;
        default:
            $gps = '4';
            $filter_string = "support_product_group_multi[]=4";
            $emp_team_group = "86";
            break;
    }
    if($return_type=='filter'){
        return $filter_string;
    } else if($return_type=='web_group') {
        return $emp_team_group;
    }else if($return_type=='partner') {
        return $partner_support_groups;
    }else if($return_type=='employee') {
        return $employee_support_groups;
    }
    return $gps;
}
/**
 * @param string $team
 * @param string $gem_alias
 * @param string $chatbot_check_col
 * @param string $chatbot_column
 * @param string $assure_company
 * @return string
 */
function get_team_condition_for_param($team,$gem_alias='',$chatbot_check_col='',$chatbot_column='',$assure_company='-1') {
    global $assure_care_company;
    $assure_company = (intval($assure_company)>0?$assure_company:$assure_care_company);
    $condition = '';
    if((int)$team>0) {
        if($gem_alias!='') {
            $gem_alias .= ".";
        }
        $web_gps = get_groups_for_param($team,'web_group');
        if($web_gps!='' and ($assure_company=='1' or intval($assure_company)==0)) {
            if($team=='9999') {
                $condition = " and ".$gem_alias."gem_emp_id='9999' ";
            } else if($team!='7') {
                if($chatbot_column!='' and $chatbot_check_col!='') {
                    $gps = get_groups_for_param($team);
                    $support_gp_wh = " $chatbot_column in ($gps) ";
//                     if((int)$assure_care_company==2) {
//                         $support_gp_wh = " $chatbot_column in (37,38,39) ";
//                     } else if(intval($assure_care_company)==3) {
//                         $support_gp_wh = " $chatbot_column in (40) ";
//                     }
                    $condition = " and if($chatbot_check_col='3',".$gem_alias."web_group in ($web_gps) and ".
                        $gem_alias."gem_role_id not in (31),$support_gp_wh) ";
                } else {
                    $condition = " and ".$gem_alias."web_group in ($web_gps) and ".$gem_alias."gem_role_id not in (31) ";
                }
            } else {
                $condition = " and ".$gem_alias."gem_role_id in (31) ";
            }
        }
    }
    return $condition;
}
/**
 * @return string
 */
function assure_care_company_filter_condition(){
    global $assure_care_company;
    $cond = "";
    if($assure_care_company!=0){
        $cond .= " and GSP_COMPANY_ID='$assure_care_company' ";
    }
    return $cond;
}
/**
 * @return void
 */
function show_my_support() {
    global $emp_code,$zone_id,$region_id,$prev_status,
			$terr_id,$country_id,$state_id,$district_id,$from_dt,$to_dt,$chk_last_support_activity,
			$support_status,$reason,$chk_ordernotexist,$support_product_group,$unique_support,$chk_skip_iot,
			$chk_assigned,$support_activity,$sortbycol,$sorttype,$attach_path,$show_type,$uid,$area_id,
			$received_in_ho,$non_employee_group,$only_address_fields,$query_contact_dtl,$export_address_fields;
	$queryct='';
	$inner_whr='';
	if(is_authorized_group_list($uid,$non_employee_group)){
		$queryct= " join gft_leadcode_emp_map on (GLEM_EMP_ID=$uid) " .  
				" join gft_cp_info cp on(GLEM_LEADCODE=cp.CGI_LEAD_CODE and ((GLH_LEAD_SOURCECODE=5 and GLH_REFERENCE_OF_PARTNER=cp.CGI_LEAD_CODE ) or (GLH_LEAD_SOURCECODE=7 and glh_reference_given=cp.CGI_LEAD_CODE) or glh_lead_code=cp.CGI_LEAD_CODE) or (glh_lfd_emp_id=GLEM_EMP_ID) or (GLH_CREATED_BY_EMPID	=GLEM_EMP_ID)) ";
	}
	$query_from_date=db_date_format($from_dt);
	$query_to_date=db_date_format($to_dt);
	$sub_query = " select GCD_COMPLAINT_ID comp_id,max(GCD_ACTIVITY_ID) as act_id from gft_customer_support_dtl where gcd_activity_date between '$query_from_date 00:00:00' and '$query_to_date 23:59:59' group by GCD_COMPLAINT_ID ";
	$latest_act_join = " join ($sub_query) tt on (comp_id=hdr.GCH_COMPLAINT_ID and act_id=dtl.GCD_ACTIVITY_ID) ";
	$status_join = 	" JOIN gft_status_master F on (gch_current_status = F.GTM_CODE) ".
					" join gft_status_group_master on (GMG_GROUP_ID=GTM_GROUP_ID)";
	if($unique_support!='true'){
		$latest_act_join = "";
		$status_join = " JOIN gft_status_master F on (dtl.GCD_STATUS = F.GTM_CODE) ";
	}
	$dtl_join = "";
	$status_history_join = '';
	$prev_cond = '';
	if($chk_last_support_activity=='on'){
		$dtl_join = " and dtl.GCD_ACTIVITY_ID=hdr.GCH_LAST_ACTIVITY_ID ";
	} else if($prev_status!='') {
	    $status_history_join = " left join gft_status_history gsh on (dtl.gcd_activity_id=gsh.gsh_activity_id and gch_complaint_id=gsh.gsh_complaint_id) ";
	    $prev_cond .= " and gsh.gsh_old_status ='$prev_status' ";
	}
	$mysort =array ("","dtl.GCD_ACTIVITY_ID","","","dtl.GCD_ACTIVITY_DATE","em.GEM_EMP_NAME","hdr.GCH_COMPLAINT_ID", 
			"hdr.GCH_COMPLAINT_DATE","GLH_CUST_NAME","B.GPM_PRODUCT_ABR", "hdr.GCH_VERSION",
			"cm.GFT_COMPLAINT_DESC","G.GCM_NATURE","F.GTM_NAME","dtl.GCD_PROBLEM_SUMMARY","dtl.GCD_PROMISE_DATE",
			"PE.GEM_EMP_NAME", "dtl.GCD_SCHEDULE_DATE", "time_spent", "K1.GAM_ACTIVITY_DESC", 
			"dtl.GCD_PROBLEM_DESC","dtl.GCD_REMARKS","dtl.GCD_INTERNAL_EMOTION","dtl.GCD_PROBLEM_DESC" ); 			
	$querysel="SELECT dtl.GCD_ACTIVITY_ID,dtl.GCD_ACTIVITY_DATE,dtl.GCD_VISIT_TIMEOUT,em.GEM_EMP_NAME, hdr.GCH_COMPLAINT_ID, " .
			"hdr.GCH_COMPLAINT_DATE,GLH_CUST_BUSSPHNO,GLH_TERRITORY_ID, GLH_LEAD_CODE $only_address_fields, B.GPM_PRODUCT_ABR, hdr.GCH_VERSION, " .
			"cm.GFT_COMPLAINT_DESC,G.GCM_NATURE,F.GTM_NAME, dtl.GCD_PROBLEM_SUMMARY,dtl.GCD_PROMISE_DATE, " .
			"PE.GEM_EMP_NAME AS PEMP,  dtl.GCD_SCHEDULE_DATE, dtl.GCD_EXTRA_CHARGES, K1.GAM_ACTIVITY_DESC, " .
			"dtl.GCD_PROBLEM_DESC,  dtl.GCD_REMARKS, dtl.GCD_INTERNAL_EMOTION,dtl.GCD_PROBLEM_DESC, dtl.GCD_UPLOAD_FILE,em.GEM_MOBILE,ac.gld_distance_travelled,dtl.gcd_visit_no,".
			"GCD_RECEIVED_IN_HO, GCD_VN_TRANSID,GZC_CHAT_ID,GCG_ID,if(GCD_VISIT_TIMEOUT='0000-00-00 00:00:00' or GCD_VISIT_TIMEOUT is null,'',TIMEDIFF(GCD_VISIT_TIMEOUT,GCD_ACTIVITY_DATE)) as time_spent,  ".
			" chat_id ".
			" FROM gft_customer_support_hdr hdr " .
			" join gft_customer_support_dtl dtl on(hdr.GCH_COMPLAINT_ID =dtl.GCD_COMPLAINT_ID $dtl_join) " .
			$latest_act_join.$status_history_join.
			" join gft_lead_hdr lh on (lh.glh_lead_code=hdr.GCH_LEAD_CODE) ";
	$queryexport="SELECT dtl.GCD_ACTIVITY_ID 'Activity Id',dtl.GCD_ACTIVITY_DATE 'Time In',dtl.GCD_VISIT_TIMEOUT 'Time Out', " .
			" GLH_LEAD_CODE $export_address_fields ,em.GEM_EMP_NAME 'Executive'," .
			"hdr.GCH_COMPLAINT_ID 'S Id', " .
			"hdr.GCH_COMPLAINT_DATE 'Complaint Date',B.GPM_PRODUCT_ABR 'Product', hdr.GCH_VERSION 'Version', " .
			"cm.GFT_COMPLAINT_DESC 'Complaint Desc',G.GCM_NATURE 'Nature',F.GTM_NAME 'Status'," .
			"dtl.GCD_PROBLEM_SUMMARY ,dtl.GCD_PROMISE_DATE 'Promise Date', " .
			"PE.GEM_EMP_NAME AS 'Scheduled To',dtl.GCD_SCHEDULE_DATE 'Schedule Date'," .
			"dtl.GCD_EXTRA_CHARGES 'Extra Charges',K1.GAM_ACTIVITY_DESC 'Activity Desc', " .
			"dtl.GCD_PROBLEM_DESC 'Problem Desc',dtl.GCD_REMARKS 'Remarks',dtl.GCD_INTERNAL_EMOTION 'Internal Emotion', ".
			"if(GCD_VISIT_TIMEOUT='0000-00-00 00:00:00' or GCD_VISIT_TIMEOUT is null,'',TIMEDIFF(GCD_VISIT_TIMEOUT,GCD_ACTIVITY_DATE)) as time_spent ".
			" FROM gft_customer_support_hdr hdr " .
			" join gft_customer_support_dtl dtl on(hdr.GCH_COMPLAINT_ID =dtl.GCD_COMPLAINT_ID $dtl_join) " .
			$latest_act_join.
			" join gft_lead_hdr lh on (lh.glh_lead_code=hdr.GCH_LEAD_CODE) ";
	$emp_cond = '';
	if($emp_code!=0 and $emp_code!='') {
	    $emp_cond = get_query_reporting_under(($chk_assigned=='on'?'dtl.gcd_process_emp':'dtl.GCD_EMPLOYEE_ID'),$emp_code);
	}
	$query = " join gft_support_product_group on (gsp_group_id=glh_main_product) ".
	   	     " join gft_product_family_master B on (hdr.GCH_PRODUCT_CODE=B.GPM_PRODUCT_CODE) " .
			 " join gft_product_group_master pg on (pg.gpg_product_family_code=B.GPM_head_family AND hdr.GCH_PRODUCT_SKEW =pg.gpg_skew )" .
			 " $status_join $queryct " .
			 get_query_area_mapping($terr_id,$area_id,$region_id,$zone_id,$district_id,$state_id,$country_id).$emp_cond;
	global $support_calls;
	if($support_calls==3){
		$inner_whr=" and  date(gcd_activity_date)<'$query_from_date' "; 
	}else if($show_type==4){
		$inner_whr=" and  date(gcd_activity_date)<='$query_to_date' ";
	}
	if($support_calls==3 or $support_calls==4){
	$query.="join (select GCH_PRODUCT_CODE,GCH_PRODUCT_SKEW,GCD_COMPLAINT_ID as MGCD_COMPLAINT_ID,max(gcd_activity_id) mact 
from gft_customer_support_hdr,gft_customer_support_dtl sp where gch_complaint_id=gcd_complaint_id  
$inner_whr group by gch_product_code,gch_product_skew,GCD_COMPLAINT_ID ) m1 on 
(mact=dtl.gcd_activity_id AND MGCD_COMPLAINT_ID=dtl.GCD_COMPLAINT_ID ) "; 		
	}
	$query.=" left JOIN gft_complaint_master cm ON (hdr.GCH_COMPLAINT_CODE= cm.GFT_COMPLAINT_CODE) ".
			" left JOIN gft_severity_master C on (dtl.GCD_SEVERITY=C.GSM_CODE) " .
			" left JOIN gft_priority_master D on (dtl.GCD_PRIORITY=D.GPM_CODE) " .
			" left JOIN gft_complaint_nature_master G on (dtl.GCD_NATURE = G.GCM_NATURE_ID) " .
			" LEFT JOIN gft_activity_master K ON (dtl.GCD_TO_DO=K.GAM_ACTIVITY_ID) ". 
			" LEFT JOIN gft_emp_master em ON (dtl.GCD_EMPLOYEE_ID=em.GEM_EMP_ID) " .
			" LEFT JOIN gft_customer_emotion_master CE ON (CE.GCM_EMOTION_ID = dtl.GCD_CUSTOMER_EMOTION) " .
			" LEFT JOIN gft_emp_master PE ON (dtl.GCD_PROCESS_EMP=PE.GEM_EMP_ID) ".
			" LEFT JOIN gft_activity_master K1 ON (dtl.GCD_VISIT_REASON=K1.GAM_ACTIVITY_ID) ".
			" LEFT JOIN gft_activity ac ON (em.gem_emp_id=ac.gld_emp_id and ac.gld_reffer_id=dtl.gcd_activity_id and ac.gld_lead_code=gch_lead_code)" .
			" left join gft_zoho_chat_hdr gc on(gc.GZC_SUPPORT_ID=dtl.gcd_activity_id)".
			" left join gft_chat_group_master cg on(cg.GCG_ID=gc.GZC_AGENT_GROUP_ID and GCG_ACTIVITY_TYPE=1)".
			" LEFT JOIN gft_chat_wrapup_dtl cw ON(GCW_ACTIVITY_ID=dtl.gcd_activity_id)";
	if($chk_ordernotexist=='on'){
		$query.= " left join gft_install_dtl_new idn on (gch_lead_code=gid_lead_code) ";
	}
	$query.="WHERE (1) ";
	global $group_by;
	$query.=get_query_constrain_common($show_gft=true);
	if($chk_ordernotexist=='on'){
			$query.= " and idn.gid_order_no is null ";
			$group_by=" gch_lead_code ";
	}
	$dat="";
	if($reason!='' and $reason!='0') {
		if($chk_assigned=='on'){
			$query.="and dtl.GCD_TO_DO='$reason' ";
		}else{
			$query.=" and dtl.GCD_VISIT_REASON='$reason' ";
		}
	}
	if($support_activity!='' and $support_activity!='0' and $support_activity!='-1' and $chk_assigned!='on'){
		$query.=" and dtl.GCD_NATURE='$support_activity' ";
	}else if($support_activity=='-1'){
		$query.=" and dtl.GCD_NATURE!=1 ";
	}
	if($received_in_ho=='1'){
		$query.=" and GCD_VISIT_NO!='' ";
	}
	if($received_in_ho=='2'){
		$query.=" and GCD_RECEIVED_IN_HO = 'Y' ";
	}
	if($received_in_ho=='3'){
		$query.=" and GCD_VISIT_NO = '' and GCD_NATURE = '1' ";
	}
	if($received_in_ho=='4'){
		$query.=" and GCD_RECEIVED_IN_HO = 'N' and GCD_NATURE = '1' ";
	}
	if( ($support_product_group!='') && ($support_product_group!='0') ){
		$query .= " and lh.GLH_MAIN_PRODUCT='$support_product_group' and GCH_OWNERSHIP!=1 ";
	}
	$query.=check_common_support_dtl(true,'PE','em');
	$query .= $prev_cond;
	if($support_status!=null && $support_status!='Any' and is_array($support_status)){
		if(is_array($support_status)){
			$i=0;
			$ss = 0;
			$query1="";
			foreach ($support_status as $t)	
			{
				if($t=='0' or $t=='Any' or $t==''){$ss=1; continue;}
				if ($i==0) {	$query1.=" and (GCD_STATUS='$t'";	} 
				else {$query1.=" or GCD_STATUS='$t'";}
				$i++;
			}
			$query1.=")";
			if($ss!=1){
				$query.=$query1;
			}
		}	
	}else{
		$st =$support_status;
		if($support_status!='0' && $support_status!='Any' and $support_status!=''){
		$query.=" and GCD_STATUS='$st' ";
		}
	}
	if($unique_support=='true'){
		$query .= "AND GMG_GROUP_ID!=7";
	}
	if($chk_skip_iot=='on'){
		$query .= " and GCD_COMPLAINT_ID not in (select GCH_COMPLAINT_ID from gft_customer_support_hdr join gft_customer_support_dtl on (GCH_FIRST_ACTIVITY_ID=GCD_ACTIVITY_ID) where GCD_NATURE=22 and GCD_ACTIVITY_DATE between '$query_from_date 00:00:00' and '$query_to_date 23:59:59' ) ";
	}
	$query .= assure_care_company_filter_condition();
	if( in_array($unique_support,array('grouping','grouping_rem')) ){
		if($unique_support=='grouping_rem'){
			$query .= " and hdr.gch_current_status='T2' ";
		}
		$query .= " group by GCD_COMPLAINT_ID ";
	}else{
		$query.= " group by GCD_COMPLAINT_ID,gcd_activity_id ".($group_by!=''? ',':''). $group_by;
	}
	if($sortbycol==''){
		$query.= " ORDER BY GCD_ACTIVITY_DATE desc ";
		$sortbycol = "GCD_ACTIVITY_DATE";
		$sorttype = '2';
	}else{
		$query.= " ORDER BY $sortbycol ".($sorttype=='2'?"DESC ":" ");
	}
	$queryexport = $queryexport.$query;
	$query =$querysel.$query;
	$result=execute_my_query($query,'Support_util.php');
	$count_num_rows=mysqli_num_rows($result);
	$r_query=$queryexport;
	$nav_struct=get_dtable_navigation_struct($count_num_rows);      
	$myarr=array("A-Id","Activity Date","Time Out","Complaint By","S-Id","Complaint Date","Customer","Product","Version","Complient","Nature","Status","Summary",	
		"Promise Date","Scheduled To","Schedule Date","Extra Charges","Visit Reason","Activity Desc","Remarks");
		
	$myarr=array("S No","A-Id","Distance Traveled","Received in HO","Activity Date","Complaint By","S-Id","Complaint Date","Customer","Customer Id","Product","Version","Compliant","Nature","Status","Summary",	
		"Scheduled To","Schedule Date","Time Spent","Visit Reason","Activity Desc","Solution Given","Internal Emotion","File Attach");
	$sp='<input type="button" class="button" value="submit" title="Submit[Alt+S]" accessKey="S" ' .
		'onclick="javascript:submit_support_activity_form();">';
	print_dtable_navigation($count_num_rows,$nav_struct,substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],"/")+1),"export_all_report.php",$r_query,$heading=null,$sp);
	  
	//$alt_row_class=array("oddListRow","evenListRow");
	$warr = array("30","30","30","30","30","30","30","30","30","30","30","30","30","30","30","30","30","30","100","60","30","30");
    
echo<<<END
<tr><td>
<form name='submit_support_extra' id='submit_support_extra' method='POST' action='submit_support_extra.php'>
<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1">
<input type='hidden' name='submit_support_extra' value='true'>
<input type='hidden' name='submit_support_extra1' value='true'>
END;
	sortheaders($myarr,$mysort,$nav_struct,$sortbycol,$sorttype);     
	$s=0;$s_no=0;
	$start_of_row=(int)$nav_struct['start_of_row'];
	$MAX_NO_OF_ROWS=(int)$nav_struct['records_per_page'];
	$sl=0;$id="";
	if($count_num_rows>0){
		mysqli_data_seek($result,$start_of_row);	
		while(($query_data=mysqli_fetch_array($result)) and $MAX_NO_OF_ROWS > $sl){            
			$sl++;
			$Upload_File=$query_data['GCD_UPLOAD_FILE'];
			$GCD_VN_TRANSID=$query_data['GCD_VN_TRANSID'];
			$chat_id	=	$query_data['GZC_CHAT_ID'];
			$chat_group_id	=$query_data['GCG_ID'];
			$godesk_chat_id	=	$query_data['chat_id'];
			$tooltip=get_necessary_data_from_query_for_tooltip($query_data);
			$attach='';	
			if($Upload_File!=""){
				$attachment = explode(",", $Upload_File);
				for($j=0;$j<count($attachment);$j++){
				$ds=$attachment[$j];
				$attc= substr($ds,strpos($ds,'/')+1);
				$attach=$attach."<br><a href=\"$attach_path/Support_Upload_Files/$ds\" >$attc</a>";
				}//for attach
			}
            $sl_link=$sl.($query_data['GCD_VN_TRANSID']!='0'?"<a href=\"javascript:call_popup('techsupport_call_refference.php?transId={$query_data['GCD_VN_TRANSID']}',7);\"><img src=\"images/speaker.jpg\" height=\"20px\" width=\"20px\"></a>":'');
            if($chat_group_id!='' and $chat_id!=''){
            	$sl_link=$sl."<a href=\"javascript:call_popup('chat_history_details.php?chatId=$chat_id',4);\"><img src=\"images/chat_new.png\" height=\"20px\" width=\"20px\"></a>";
            }
            if($godesk_chat_id!=""){
		    $sl_link = $sl."<a href=\"chatbot_transcript.php?chat_id=$godesk_chat_id\" target='_blank'><img src=\"images/chat_new.png\" height=\"20px\" width=\"20px\"></a>";
            }
            $chk_rho_active=(($query_data['GCM_NATURE']=="Visit" and $query_data['gcd_visit_no']!='' and $query_data['gcd_visit_no']!=0)?'':"disabled");
			$check_rho=($query_data['GCD_RECEIVED_IN_HO']=='Y'?"checked":'');  
			$value_array[0]=array($sl_link,
					$query_data['GCD_ACTIVITY_ID'],
					$query_data['gld_distance_travelled'],
					"<input type=\"hidden\" id=\"prev_rho[$sl]\" name=\"prev_rho[]\" value=\"{$query_data['GCD_RECEIVED_IN_HO']}\">" .
					"<input type='checkbox' name='rho[]' id='rho[$sl]' value='{$query_data['GCD_ACTIVITY_ID']}' $check_rho $chk_rho_active>" .
					"<input type=\"hidden\" id=\"activity_id[$sl]\" name=\"activity_id[]\" value=\"{$query_data['GCD_ACTIVITY_ID']}\">",
					$query_data['GCD_ACTIVITY_DATE'],
					"<a onMouseover=\"ddrivetip('{Contact No: {$query_data['GEM_MOBILE']} }','#EFEFEF', 100);\" onMouseout=\"hideddrivetip();\">{$query_data['GEM_EMP_NAME']}</a>",
					"<a title=\"click here to view Complaint History\" href=\"javascript:call_popup('complaint_details.php?id={$query_data['GCH_COMPLAINT_ID']}',7);\">{$query_data['GCH_COMPLAINT_ID']}</a>",
					$query_data['GCH_COMPLAINT_DATE'],
					"<a href=\"javascript:call_popup('supporthistory.php?call_from=popup&custCode={$query_data['GLH_LEAD_CODE']}',7);\" onMouseover=\"ddrivetip('{$tooltip}','#EFEFEF', 200);\" onMouseout=\"hideddrivetip();\">{$query_data['GLH_CUST_NAME']}</a>". 
					"&nbsp;<a href=\"javascript:call_popup('order_releated_details.php?lcode={$query_data['GLH_LEAD_CODE']}',7);\" title=\"Order Releated Details\">[0]</a>",
					$query_data['GLH_LEAD_CODE'],
					$query_data['GPM_PRODUCT_ABR'],
					$query_data['GCH_VERSION'],
					$query_data['GFT_COMPLAINT_DESC'],
					$query_data['GCM_NATURE'],
					$query_data['GTM_NAME'],
					$query_data['GCD_PROBLEM_SUMMARY'],
					$query_data['PEMP'],
					($query_data['GCD_SCHEDULE_DATE']!='0000-00-00 00:00:00'?$query_data['GCD_SCHEDULE_DATE']:''),
					$query_data['time_spent'],$query_data['GAM_ACTIVITY_DESC'],$query_data['GCD_PROBLEM_DESC'],
					$query_data['GCD_REMARKS'],$query_data['GCD_INTERNAL_EMOTION'],$attach);
			print_resultset($value_array);
     	}//End of while
     }//End of if
echo "</table></form></td></tr>";
print_dtable_navigation($count_num_rows,$nav_struct,substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],"/")+1),"export_all_report.php",$r_query,$heading,$sp);
echo "</table>";
echo<<<END
<script type="text/javascript">
function submit_support_activity_form(){
	 //obj=document.support_report_form;
	var activity_form_id="";
	var activity_form_id_revert="";
	var poststr="";
	var shown_sep="";
	var shown_sep2="";
	if(document.getElementsByName("rho[]")){
		var activity_list=document.getElementsByName("rho[]");
		var prev_activity_list=document.getElementsByName("prev_rho[]");
		var activity_id_list=document.getElementsByName("activity_id[]");
		var products_shown="";
		for (i=0;i<activity_list.length;++ i){
			var prev_rho_value=prev_activity_list[i].value;
			var activity_id=activity_id_list[i].value; 
			if (activity_list[i].checked &&  prev_rho_value=='N'){
				activity_form_id= activity_form_id + shown_sep + activity_list[i].value ;
				shown_sep=",";
			}else if (!(activity_list[i].checked) && prev_rho_value=='Y'){
				activity_form_id_revert= activity_form_id_revert + shown_sep2 + activity_id ;
				shown_sep2=",";
			}
		}
		poststr="activity_id="+activity_form_id+"&reverted_activity_id="+activity_form_id_revert;       	
		ai = new AJAXInteraction('submit_support_extra.php',handleHttpResponse_submit);
		ai.doPost(poststr);
	}
}
</script>
END;
}

/**
 * @param string $support_status
 * 
 * @return void
 */
function show_selected_values($support_status){
	$st='';
	if(is_array($support_status)){
		$st_comma="('".implode("','",$support_status)."')";
		$st=" SELECT group_concat(gtm_name) FROM gft_status_master where gtm_code in $st_comma ";
	}else if($support_status!=''){
		$st=" SELECT group_concat(gtm_name) FROM gft_status_master where gtm_code ='$support_status' ";	
	}
	if($st!=''){
		$rst=execute_my_query($st);
		$qd=mysqli_fetch_array($rst);
		$status_selected=$qd[0];
		if($status_selected!=''){
			echo '<br><font color="red"><center><b>Status Selected: </b></font>'.$status_selected."<br>";
		}
	}
}

/**
 * @param string $GCH_PRODUCT_CODE
 * @param string $order_number
 * @param string $GCH_PRODUCT_VERSION
 * @param string $lead_code
 * 
 * @return void
 */
function checking_version_update_product($GCH_PRODUCT_CODE,$order_number,$GCH_PRODUCT_VERSION,$lead_code){
	$verck = "SELECT g.GPV_PRODUCT_CODE, CONCAT(g.GPV_MAJOR_VERSION,'.', g.GPV_MINOR_VERSION,'.', g.GPV_PATCH_VERSION,'.', g.`GPV_EXE_VERSION`) " .
			" FROM gft_product_version_master g,gft_product_family_master f " .
			" where CONCAT(g.GPV_MAJOR_VERSION,'.', g.GPV_MINOR_VERSION,'.', g.GPV_PATCH_VERSION,'.', g.`GPV_EXE_VERSION`)= '$GCH_PRODUCT_VERSION' " .
			" AND GPV_PRODUCT_CODE=GPM_HEAD_FAMILY  AND GPM_PRODUCT_CODE='$GCH_PRODUCT_CODE' ";
	$resultvck = execute_my_query($verck);
	if ($datavc = mysqli_fetch_array($resultvck)) {
		$upvesion = "update gft_install_dtl_new set GID_UPDATED_TIME=now(),GID_CURRENT_VERSION ='$GCH_PRODUCT_VERSION' " .
					" where GID_LIC_PCODE='$GCH_PRODUCT_CODE' and gid_lead_code='$lead_code' " .
					" and gid_status='A' and GID_CURRENT_VERSION!='$GCH_PRODUCT_VERSION' ";
		$res = execute_my_query($upvesion);
		if(!$res) {
			die("Data is not inserted customer_support_dtl<br>$upvesion");
		}
	}
}

/**
 * @param string $complaint_id
 * @param string $lead_code
 * 
 * @return void
 */
function show_escalation_history($complaint_id=null,$lead_code=null){
	global $sortbycol,$sorttype,$me;
	$mysort=/*. (string[int]) .*/ array();
	$myarr=/*. (string[int]) .*/ array();
	$query=" select GCD_COMPLAINT_ID,gem_emp_name,GCD_ACTIVITY_DATE,GCD_REMARKS " .
			" from gft_customer_support_dtl dtl " .
			" join gft_emp_master em on (em.gem_emp_id=dtl.gcd_employee_id) " .
			" join gft_customer_support_hdr hdr on (dtl.gcd_complaint_id=hdr.gch_complaint_id)" .
			" where dtl.GCD_ESCALATION='Y'  "; 
	if($complaint_id!=null){
		$query.=" AND GCD_COMPLAINT_ID='$complaint_id' ";  
		$myarr=array('S.No','Escalation By','Date','Remarks');
		$mysort=array('','gem_emp_name','GCD_ACTIVITY_DATE','GCD_REMARKS');
	}else if($lead_code!=null){
		$query.=" and GCH_LEAD_CODE='$lead_code' ";
		$myarr=array('S.No','Complaint Id','Escalation By','Date','Remarks');
		$mysort=array('','GCD_COMPLAINT_ID','gem_emp_name','GCD_ACTIVITY_DATE','GCD_REMARKS');
	}else{
		$sortbycol=$_REQUEST['sortbycol'];
		$sorttype=$_REQUEST['sorttype'];
		$query.=add_order_by_query($default_order_by='GCD_COMPLAINT_ID',$default_order_by_type=1);
	}
	print_dtable_header("Escalation History");
	$result=execute_my_query($query);
	$cnt=mysqli_num_rows($result);
	$nav_struct=get_dtable_navigation_struct($cnt);
	print_dtable_navigation($cnt,$nav_struct,$me,'export_all_report.php',
	$query,$heading=$myarr,$sp=null,$htmltype=1,$show_nav=true,$post_array=null,$heading2=null,
	$sms_category=null,$email_category=null,$to_whom=null,$take_sort_array_for_export=$mysort);
echo<<<END
	<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width='100%'>
END;
	sortheaders($myarr,$mysort,$nav_struct,$sortbycol,$sorttype);
	$start_of_row=(int)$nav_struct['start_of_row'];
	$MAX_NO_OF_ROWS=(int)$nav_struct['records_per_page']; 
	$sl=0;
	$value_arr=array();
	while( ($qdata=mysqli_fetch_array($result))  and $MAX_NO_OF_ROWS > $sl){
		$sl++;	
		for($i=0;$i<count($mysort);$i++){
			if($mysort[$i]==''){
				$start_of_row++;
				$value_arr[$sl][$i]=$start_of_row;
			}else {
				$value_arr[$sl][$i]=$qdata[$mysort[$i]];
			}
					
		}
	}
	print_resultset($value_arr);
echo<<<END
</table>
END;
	print_dtable_footer();
}

/**
 * @param string $custCode
 * 
 * @return void
 */
function show_audit_history($custCode){
	global $from_dt,$to_dt,$me;
	$query_from_date=db_date_format($from_dt);
	$query_to_date=db_date_format($to_dt);
	print_dtable_header("Latest Audit Details"); 
	$myarr=array("S.No.","Audit Date","Audit Type","Audit Question & Answer ","Customer Comments","My comments","Audit By","Regional Coordinator","Field Incharge");
	$query="SELECT GLH_LEAD_CODE, GAH_AUDIT_ID,  GAT_AUDIT_DESC, GAH_DATE_TIME, " .
 		" a.GEM_EMP_NAME as audit,b.GEM_EMP_NAME as incharge,c.GEM_EMP_NAME as field,GAH_CUSTOMER_COMMENTS,". 
 		" GAH_MY_COMMENTS,GAH_AUDIT_TYPE,GAH_REFFERNCE_ORDER_NO " .
 		" FROM gft_lead_hdr" .
		" JOIN gft_audit_hdr ON (GLH_LEAD_CODE=GAH_LEAD_CODE)" .
		" JOIN gft_audit_type_master ON(GAH_AUDIT_TYPE=GAT_AUDIT_ID) " .
		" left JOIN gft_emp_master c on (c.GEM_EMP_ID=GAH_FIELD_INCHARGE)" .
		" left JOIN gft_emp_master b on (b.GEM_EMP_ID=GAH_L1_INCHARGE)" .
		" left JOIN gft_emp_master a on (a.GEM_EMP_ID=GAH_AUDIT_BY) " .
		" where GLH_LEAD_CODE=$custCode " .
		(($from_dt!='' and $to_dt!='')?" and GAH_DATE_TIME between '$query_from_date 00:00:00' and '$query_to_date 23:59:59' ":''). 
		"GROUP BY GAH_AUDIT_ID order by GAH_DATE_TIME desc";
		$result_req=execute_my_query($query);
	$count_num_rows=mysqli_num_rows($result_req);
	$nav_struct=get_dtable_navigation_struct($count_num_rows);
	print_dtable_navigation($count_num_rows,$nav_struct,$me,"export_all_report.php",$query,$heading=$myarr,
		$sp=null,$htmltype=1,$show_nav=true,$post_array=null,$heading2=null,$sms_category=null,
		$email_category=null,$to_whom=null,$take_sort_array_for_export=null);
echo<<<END
<div id="theLayer" id="theLayer" style="position:absolute;width:500px;left:100;top:100;visibility:visible"></div>
<table cellpadding="0" cellspacing="1" width="100%" border="0" class="FormBorder1">
END;
	sortheaders($myarr,$mysort=null,$nav_struct,$sortbycol=null,$sorttype=null,$myarr_width=null,$noheader=null,$myarr_extra_link=null,$myarr_sub=null,$rowspan_arr=null,$colspan_arr=null,
			$myarr_sub1=null,$rowspan_arr1=null,$colspan_arr1=null);
	if($count_num_rows){
	    $start_of_row=(int)$nav_struct['start_of_row'];
		$MAX_NO_OF_ROWS=(int)$nav_struct['records_per_page'];
		mysqli_data_seek($result_req,$start_of_row);
		$s=0;
		$value_arr_align=array("left","center","center","center","center","center","center","center","center","center","center");
		while(($data_req=mysqli_fetch_array($result_req))  and $MAX_NO_OF_ROWS > $s){
		    $s++;
		    $log_name	=	 ($data_req['GAH_AUDIT_TYPE']!=39?'Q & A':'LOG');
		    $pd_id        = $data_req['GAH_REFFERNCE_ORDER_NO'];
		    $log_name_text = "<input type=button value=\"$log_name\" class=\"button\" onclick=\"javascript:auditid_dtl({$data_req['GAH_AUDIT_ID']});\">";
		    if($data_req['GAH_AUDIT_TYPE']==25){
		        $uat_signoff = execute_my_query("select GPA_ID from gft_pcs_audit_dtl where GPA_SIGNEDOFF_BY>0 AND GPA_PD_ID='".$pd_id."' AND GPA_SIGNOFF_STATUS=4 LIMIT 1");
		        if(mysqli_num_rows($uat_signoff)>0){
		            $log_name_text = "<input type=button value=\"LOG\" class=\"button\" onclick=\"javascript:call_popup('pd_uat_training_activity.php?product_delivery_id=$pd_id&lead_code=$custCode&show_history=1',6);\">";
		        }
		    }
		    $value_arr[0]=array($s,$data_req['GAH_DATE_TIME'],$data_req['GAT_AUDIT_DESC'],$log_name_text,$data_req['GAH_CUSTOMER_COMMENTS'],($data_req['GAH_AUDIT_TYPE']!=39?$data_req['GAH_MY_COMMENTS']:''),$data_req['audit'],$data_req['incharge'],$data_req['field']);		
			print_resultset($value_arr,'',$value_arr_align);
		}
	}
echo<<<END
</table>
<script type="text/javascript" src="js/GofrugalLayer.js"></script>
<script type="text/javascript">
function auditid_dtl(audit_id){
	var body="<iframe src=\"auditid_dtl.php?audit_id="+audit_id+"\" frameborder=\"0\" height=500 width=\"100%\"></iframe>";
		drawLayer('Audit Details',body,550,'',document.getElementById("theLayer"));
}
</script>
</td></tr>
</table>
END;
}

/**
 * @param string $custCode
 * 
 * @return void
 */
function order_collection_conversation($custCode){

	$order_no='TODO: VALUE NOT FOUND';

	$query= " select con.GMOC_ORDER_NO, emp.GEM_EMP_NAME, con.GMOC_AGREED,con.GMOC_OUTSTANDING_REASON,GOR_REASON pending_reason, " .
			" con.GMOC_REMARKS, con.GMOC_DATE, con.gmoc_current_status, rep.GEM_EMP_NAME manager, " .
			" con.GMOC_REP_AGREED, con.GMOC_REP_DATE, con.GMOC_collection_date,Freezed from " .
			"( (SELECT emp_st.GMOC_ORDER_NO, emp_st.GMOC_EMP_ID, emp_st.GMOC_AGREED,emp_st.GMOC_OUTSTANDING_REASON," .
			" emp_st.GMOC_REMARKS, emp_st.GMOC_DATE, emp_st.gmoc_current_status, " .
			" emp_st.GMOC_REP_ID, emp_st.GMOC_REP_AGREED, emp_st.GMOC_REP_DATE, " .
			" emp_st.GMOC_collection_date, concat(GMOC_WFREEZED_YEAR,'-',GMOC_WFREEZED_MONTH) Freezed " .
			" FROM gft_order_collection_dtl emp_st join gft_order_hdr on (emp_st.GMOC_ORDER_NO=GOD_ORDER_NO)" .
			" where GOD_LEAD_CODE='$custCode'" .
			" ) union all (" .
			" SELECT ac_st.GMOC_ORDER_NO, ac_st.GMOC_EMP_ID, ac_st.GMOC_AGREED,'', " .
			" ac_st.GMOC_REMARKS, ac_st.GMOC_DATE, ac_st.gmoc_current_status," .
			" '','AC','','', '0-0' Freezed FROM gft_order_collection_dtl_by_accounts ac_st join gft_order_hdr on (ac_st.GMOC_ORDER_NO=GOD_ORDER_NO) " .
			" where GOD_LEAD_CODE='$custCode') ) con " .
			" left join gft_outstanding_reason on(GOR_ID=GMOC_OUTSTANDING_REASON) " .
			" join gft_emp_master emp on (GMOC_EMP_ID=gem_emp_id) " .
			" left join gft_emp_master rep on (GMOC_REP_ID=rep.gem_emp_id) order by GMOC_DATE desc ";
	$result=execute_my_query($query);
	$myarr=array("S.No.","Order No","Employee name","Expected Collection Date","Outstanding Reason","Status of Agree","Remarks","Date","Manager", "Status of Manager Agree","Date (Manager)");
	print_dtable_header("Collection Coversation of $order_no");
echo<<<END
<table cellpadding="0" cellspacing="0" width="100%" border="0" class="FormBorder1">
END;
	sortheaders($myarr,$mysort=null,$nav_struct=null,$sortbycol=null,$sorttype=null);
	$sl=1;
	while($data=mysqli_fetch_array($result)){
		$GMOC_AGREED=$GMOC_REP_AGREED='';
		if($data['GMOC_AGREED']=='Y'){$GMOC_AGREED="<img src=\"images/yes_g.gif\">";}else if($data['GMOC_AGREED']=='N'){$GMOC_AGREED="<img src=\"images/no.gif\">";}
		if($data['GMOC_REP_AGREED']=='AC'){$GMOC_REP_AGREED='';}else if($data['GMOC_REP_AGREED']=='Y'){$GMOC_REP_AGREED="<img src=\"images/yes_g.gif\">";}else if($data['GMOC_REP_AGREED']=='N'){$GMOC_REP_AGREED="<img src=\"images/no.gif\">";} 
		print_resultset(array(0=>array($sl,$data['GMOC_ORDER_NO'],$data['GEM_EMP_NAME'],($data['Freezed']!='0-0'?'<font color="red">**<font>':'').$data['GMOC_collection_date'],$data['pending_reason'],$GMOC_AGREED,$data['GMOC_REMARKS'],$data['GMOC_DATE'] ,$data['manager'],$GMOC_REP_AGREED,$data['GMOC_REP_DATE'])));	
		$sl++;
	}
echo<<<END
</table><br>Note<br><font color="red">**<font> - Sales plan Completed for the corresponding month.
END;
}

/**
* @param string $custCode
*
* @return void
*/

function milestone_wise_commercial_classification_wise_effort_summary($custCode){
	$query1=" select GLD_ACTIVITY_ID,GLD_LEAD_CODE,GMM_NAME,GMM_ID,GMM_SERVICE_TYPE,GCCM_NAME,sum(if(GLD_TIME_DURATION,GLD_TIME_DURATION,0)) as Effort,GCCM_id, " .
			" sum(if(GCCM_id=1,GLD_TIME_DURATION,0)) as Billable,  " .
			" sum(if(GCCM_id=2,GLD_TIME_DURATION,0)) as PCS_Complimentary,  " .
			" sum(if(GCCM_id=3,GLD_TIME_DURATION,0)) as Sales_Complimentary,  " .
			" sum(if(GCCM_id=4,GLD_TIME_DURATION,0)) as Customer_Complimentary, " .
			" sum(if(GCCM_id=5,GLD_TIME_DURATION,0)) as Product_Issues, " .
			" sum(if(GCCM_id=6,GLD_TIME_DURATION,0)) as Product_Enhancement, " .
			" sum(if(GCCM_id=7,GLD_TIME_DURATION,0)) as Pre_sales, " .
			" sum(if(GCCM_id=8,GLD_TIME_DURATION,0)) as Internal_Complimentary, " .
			" sum(if(GCCM_id=9,GLD_TIME_DURATION,0)) as Internal_Billable " .
			" from  gft_activity  join gft_pcs_milestone_master  on (GMM_ID=GLD_MILESTONE) join  gft_commercial_classification_master on " . 
			" (GCCM_ID=GLD_COMMERCIAL_CLASSIFICATION) where gld_lead_code = $custCode group by GMM_ID ";
	$result1=execute_my_query($query1);
	$num_rows = mysqli_num_rows($result1);
	$nav = get_dtable_navigation_struct($num_rows);
	print_dtable_header("Milestone wise Commercial classification wise Effort Summary");
	print_dtable_navigation($num_rows, $nav, '');
	$myarr=array("S.NO","Milestone","Service Type","Detailed Effort(Days)","Total Effort in Man Days");
	$myarr_sub=array("Billable","PCS Complimentary","Sales Complimentary","Customer Complimentary","Product Issues","Product Enhancement","Pre-sales","Internal Complimentary","Internal Billable");
	$rowspan_arr=array('2','2','2','1','2');
	$colspan_arr=array('1','1','1','9','1');
	$mysort=array("","GMM_NAME","GMM_SERVICE_TYPE","Billable","PCS_Complimentary","Sales_Complimentary","Customer_Complimentary","Product_Issues","Product_Enhancement","Pre_sales" ,"Internal_Complimentary","Internal_Billable","Effort");
	$value_arr_align = array();
	echo<<<END
<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="100%">
END;
	$Billable_sum = $PCS_Complimentary_sum = $Sales_Complimentary_sum = $Customer_Complimentary_sum = $Effort_sum = 0.0;
	$Product_Issues_sum = $Product_Enhancement_sum = $Pre_sales_sum = $Internal_Complimentary_sum = $Internal_Billable_sum = 0.0;
	sortheaders($myarr, $mysort, $nav, null, null, null, null, null, $myarr_sub, $rowspan_arr, $colspan_arr );
	$sl=0;
	$start_of_row=(int)$nav['start_of_row'];
	$MAX_NO_OF_ROWS=(int)$nav['records_per_page'];
	$tooltip="";
	if($num_rows>0){
		mysqli_data_seek($result1,$start_of_row);
		while(($row_data = mysqli_fetch_array($result1)) and ($MAX_NO_OF_ROWS > $sl)){
			$sl++;
			$GMM_NAME=$row_data['GMM_NAME'];
			$GMM_ID=$row_data['GMM_ID'];
			$GMM_SERVICE_TYPE=$row_data['GMM_SERVICE_TYPE'];
			
			$tooltip="Click to see the detailed actitvity";
			$Billable="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
			"onMouseout=\"hideddrivetip();\"" .
			" href=\"javascript:call_popup('pcs_detailed_activity_report.php?milestone=$GMM_ID&com_class=1&cust_code=$custCode',7);\">" .
				(round(($row_data['Billable']/8),2))."</a>";
			
			$PCS_Complimentary="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
			"onMouseout=\"hideddrivetip();\"" .
			" href=\"javascript:call_popup('pcs_detailed_activity_report.php?milestone=$GMM_ID&com_class=2&cust_code=$custCode',7);\">" .
			    (round(($row_data['PCS_Complimentary']/8),2))."</a>";
			$Sales_Complimentary="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
			"onMouseout=\"hideddrivetip();\"" .
			" href=\"javascript:call_popup('pcs_detailed_activity_report.php?milestone=$GMM_ID&com_class=3&cust_code=$custCode',7);\">" .
			  (round(($row_data['Sales_Complimentary']/8),2))."</a>";
			$Customer_Complimentary="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
			"onMouseout=\"hideddrivetip();\"" .
			" href=\"javascript:call_popup('pcs_detailed_activity_report.php?milestone=$GMM_ID&com_class=4&cust_code=$custCode',7);\">" .
			   (round(($row_data['Customer_Complimentary']/8),2))."</a>";
			$Product_Issues="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
			"onMouseout=\"hideddrivetip();\"" .
			" href=\"javascript:call_popup('pcs_detailed_activity_report.php?milestone=$GMM_ID&com_class=5&cust_code=$custCode',7);\">" .
			    (round(($row_data['Product_Issues']/8),2))."</a>";
			$Product_Enhancement="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
			"onMouseout=\"hideddrivetip();\"" .
			" href=\"javascript:call_popup('pcs_detailed_activity_report.php?milestone=$GMM_ID&com_class=6&cust_code=$custCode',7);\">" .
			(round(($row_data['Product_Enhancement']/8),2))."</a>";
			$Pre_sales="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
			"onMouseout=\"hideddrivetip();\"" .
			" href=\"javascript:call_popup('pcs_detailed_activity_report.php?milestone=$GMM_ID&com_class=7&cust_code=$custCode',7);\">" .
			(round(($row_data['Pre_sales']/8),2))."</a>";
			$Internal_Complimentary="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
			"onMouseout=\"hideddrivetip();\"" .
			" href=\"javascript:call_popup('pcs_detailed_activity_report.php?milestone=$GMM_ID&com_class=8&cust_code=$custCode',7);\">" .
			(round(($row_data['Internal_Complimentary']/8),2))."</a>";
			$Internal_Billable="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
			"onMouseout=\"hideddrivetip();\"" .
			" href=\"javascript:call_popup('pcs_detailed_activity_report.php?milestone=$GMM_ID&com_class=9&cust_code=$custCode',7);\">" .
			  (round(($row_data['Internal_Billable']/8),2))."</a>";
			$Effort="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
			"onMouseout=\"hideddrivetip();\"" .
			" href=\"javascript:call_popup('pcs_detailed_activity_report.php?milestone=$GMM_ID&cust_code=$custCode',7);\">" .
			(round(($row_data['Effort']/8),2))."</a>";
			$Billable_sum 				+= round(($row_data['Billable']/8),2);
			$PCS_Complimentary_sum		+= round(($row_data['PCS_Complimentary']/8),2);
			$Sales_Complimentary_sum	+= round(($row_data['Sales_Complimentary']/8),2);
			$Customer_Complimentary_sum	+= round(($row_data['Customer_Complimentary']/8),2);
			$Product_Issues_sum			+= round(($row_data['Product_Issues']/8),2);
			$Product_Enhancement_sum	+= round(($row_data['Product_Enhancement']/8),2);
			$Pre_sales_sum				+= round(($row_data['Pre_sales']/8),2);
			$Internal_Complimentary_sum += round(($row_data['Internal_Complimentary']/8),2);
			$Internal_Billable_sum		+= round(($row_data['Internal_Billable']/8),2);
			$Effort_sum					+= round(($row_data['Effort']/8),2);
			$value_array[0] = array($sl,$GMM_NAME,$GMM_SERVICE_TYPE,$Billable,$PCS_Complimentary,$Sales_Complimentary,$Customer_Complimentary,$Product_Issues,$Product_Enhancement,$Pre_sales,$Internal_Complimentary,$Internal_Billable,$Effort);
			print_resultset($value_array,null,null);
		}
		$billable_sum_link  = "<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
							  "onMouseout=\"hideddrivetip();\"" .
							  " href=\"javascript:call_popup('pcs_detailed_activity_report.php?com_class=1&cust_code=$custCode',7);\">" .
							   $Billable_sum."</a>";
		$pcs_comp_sum_link  = "<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
							  "onMouseout=\"hideddrivetip();\"" .
							  " href=\"javascript:call_popup('pcs_detailed_activity_report.php?com_class=2&cust_code=$custCode',7);\">" .
							   $PCS_Complimentary_sum."</a>";
		$sales_comp_sum_link ="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
							  "onMouseout=\"hideddrivetip();\"" .
							  " href=\"javascript:call_popup('pcs_detailed_activity_report.php?com_class=3&cust_code=$custCode',7);\">" .
							  $Sales_Complimentary_sum."</a>";
		$cust_comp_sum_link  = "<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
							  "onMouseout=\"hideddrivetip();\"" .
							  " href=\"javascript:call_popup('pcs_detailed_activity_report.php?com_class=4&cust_code=$custCode',7);\">" .
							   $Customer_Complimentary_sum."</a>";
		$prod_issues_sum_link ="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
							  "onMouseout=\"hideddrivetip();\"" .
							  " href=\"javascript:call_popup('pcs_detailed_activity_report.php?com_class=5&cust_code=$custCode',7);\">" .
							   $Product_Issues_sum."</a>";
		$prod_enhan_sum_link ="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
							  "onMouseout=\"hideddrivetip();\"" .
							  " href=\"javascript:call_popup('pcs_detailed_activity_report.php?com_class=6&cust_code=$custCode',7);\">" .
							   $Product_Enhancement_sum."</a>";
		$pre_sales_sum_link ="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
							  "onMouseout=\"hideddrivetip();\"" .
							  " href=\"javascript:call_popup('pcs_detailed_activity_report.php?com_class=7&cust_code=$custCode',7);\">" .
							   $Pre_sales_sum."</a>";
		$Internal_Complimentary_sum_link = "<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
							  "onMouseout=\"hideddrivetip();\"" .
							  " href=\"javascript:call_popup('pcs_detailed_activity_report.php?com_class=8&cust_code=$custCode',7);\">" .
							   $Internal_Complimentary_sum."</a>";
		$Internal_Billable_sum_link = "<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
							  "onMouseout=\"hideddrivetip();\"" .
							  " href=\"javascript:call_popup('pcs_detailed_activity_report.php?com_class=9&cust_code=$custCode',7);\">" .
							   $Internal_Billable_sum."</a>";
		$Effort_sum_link    ="<a onMouseover=\"ddrivetip('$tooltip','#EFEFEF', 200);\" " .
							  "onMouseout=\"hideddrivetip();\"" .
							  " href=\"javascript:call_popup('pcs_detailed_activity_report.php?cust_code=$custCode',7);\">" .
							   $Effort_sum."</a>";
		$value_array[0] = array('','Sum Total','',$billable_sum_link,$pcs_comp_sum_link,$sales_comp_sum_link,$cust_comp_sum_link,$prod_issues_sum_link,$prod_enhan_sum_link,$pre_sales_sum_link,$Internal_Complimentary_sum_link,$Internal_Billable_sum_link,$Effort_sum_link);
		print_resultset($value_array,null,null);
	}
	echo "</table>";	
}

/**
 * @param string $custCode
 * @param string $from_dt
 * @param string $to_dt
 * 
 * @return void
 */
function show_training_imp_history($custCode,$from_dt,$to_dt){
	$query=" SELECT GCD_COUPON_NO, GCD_HANDLED_BY, h.GEM_EMP_NAME issued_by, GCD_GIVEN_DATE, GCD_SIGNED_OFF, GCD_RECEIVED_DATE, GCD_RECEIVED_BY, r.GEM_EMP_NAME received_by " .
			" FROM gft_coupon_distribution_dtl join gft_emp_master h on (GCD_HANDLED_BY=h.GEM_EMP_ID) " .
			" left join gft_emp_master r on (GCD_RECEIVED_BY=r.GEM_EMP_ID) " .
			" WHERE GCD_DISTRIBUTE_FOR='C' AND GCD_TO_ID='$custCode'";  
	$result=execute_my_query($query,'Support_util.php'); 
	$myarr=array("Coupon No","Coupon Issued by","Issued Date","Status","Signed Off date","Received by");					
	if(mysqli_num_rows($result)>0){
	   	print_dtable_header("Training Coupon Status");
echo<<<END
<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="100%">
END;
		sortheaders($myarr,null,null,null,null);
		while($query_data=mysqli_fetch_array($result)){
			$print_array[0]=array( $query_data['GCD_COUPON_NO'],$query_data['issued_by'],$query_data['GCD_GIVEN_DATE'], $query_data['GCD_SIGNED_OFF'],$query_data['GCD_RECEIVED_DATE'], $query_data['received_by']);
			print_resultset($print_array);
		}
		echo "</table><br>";
		print_dtable_footer();	
	}
	$rating_arr=array(0=>"Not yet given",1=>"Poor",2=>"Average",3=>"Good",4=>"Very Good",5=>"Excellent",);	
	$query=	" select GPT_TRAINING_ID,GIM_MS_NAME, GCC_CONTACT_NAME, GCC_CONTACT_NO, if(GPT_IS_SPOC=1,'SPOC','Trainee') spoc,".
			" if(GPT_ACK_STATUS=1,'Yes','No') ack,GPT_ACTIVITY_TIME_SPENT,GPT_CUST_RATING,GPT_ACTIVITY_ON,GPT_LEAD_CODE  from  gft_pd_training_feedback_dtl ".
			" inner join gft_customer_contact_dtl on(GPT_CONTACT_ID=gcc_id) ".
			" INNER JOIN gft_cust_imp_ms_current_status_dtl ON(GPT_TRAINING_ID=GIMC_COMPLAINT_ID)".
			" INNER JOIN gft_impl_mailstone_master ON(GIM_MS_ID=GIMC_MS_ID)".
			" where GPT_LEAD_CODE='$custCode'";
	$result=execute_my_query($query,'Support_util.php');
	$myarr=array("Training ID","Activity Type","Activity On","Trainee / SPOC Name","Contact","Contact Type","Time Spent","Acknowledge / Feedback","Rating");
	if(mysqli_num_rows($result)>=0){
		print_dtable_header("Training Feeback Details");
		echo<<<END
	<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="100%">
END;
		sortheaders($myarr,null,null,null,null);
		while($query_data=mysqli_fetch_array($result)){
			$time_spent_in_mins=get_duration_string($query_data['GPT_ACTIVITY_TIME_SPENT']);
			$rating_no = (int)$query_data['GPT_CUST_RATING'];
			$rating_str = $rating_arr[$rating_no];
			$edit_icon = "";
			if($query_data['spoc']=="SPOC" && $query_data['ack']=='No'){
			    $edit_icon = "&nbsp;<span class='imagecur edit-icon fas fa-pencil-alt' onclick=\"javascript:call_popup('pd_spoc_change.php?lead_code=".$query_data['GPT_LEAD_CODE']."&training_id=".$query_data['GPT_TRAINING_ID']."',3);\"></span>";
			}
			$print_array[0]=array($query_data['GPT_TRAINING_ID'].$edit_icon,$query_data['GIM_MS_NAME'],$query_data['GPT_ACTIVITY_ON'],$query_data['GCC_CONTACT_NAME'],$query_data['GCC_CONTACT_NO'], $query_data['spoc'],$time_spent_in_mins,$query_data['ack'],$rating_str);
			print_resultset($print_array);

		}
		echo "</table><br>";
		print_dtable_footer();
	}
	
	$query=" select GPD_ORDER_NO,GPD_OLD_EXPIRY_DT,GPD_NEW_EXPIRY_DT,GPD_EXTENDED_ON,GPD_EMP_COMMENTS,GEM_EMP_NAME from  gft_product_delivery_log ".
			" inner join gft_emp_master on(gem_emp_id=GPD_EXTENDED_BY) WHERE GPD_LEAD_CODE=$custCode";
	$result=execute_my_query($query,'Support_util.php');
	$myarr=array("Order No","Old Expiry Date","New Expiry Date","Updated On","Comments","Updated by");
	if(mysqli_num_rows($result)>=0){
	print_dtable_header("Coupon Expiry Log");
echo<<<END
	<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="100%">
END;
		sortheaders($myarr,null,null,null,null);
			while($query_data=mysqli_fetch_array($result)){
			$print_array[0]=array( $query_data['GPD_ORDER_NO'],$query_data['GPD_OLD_EXPIRY_DT'],$query_data['GPD_NEW_EXPIRY_DT'], $query_data['GPD_EXTENDED_ON'],$query_data['GPD_EMP_COMMENTS'], $query_data['GEM_EMP_NAME']);
			print_resultset($print_array);
			}
			echo "</table><br>";
			print_dtable_footer();
	}
	$query=" select GIM_MS_NAME, hdr.GCH_COMPLAINT_ID,GCH_COMPLAINT_DATE, GIMD_ACTUAL_DURATION_MINS,GTS_STATUS_NAME, td.GIMC_STATUS,  GIMC_WORKED_DURATION,GIMC_APPROVAL,GIMC_APPROVAL_ON,GEM_EMP_NAME approval_by ".
			" from  gft_customer_support_hdr hdr " .
			" JOIN gft_status_master F on (hdr.GCH_CURRENT_STATUS = F.GTM_CODE) " .
			" join gft_cust_imp_ms_current_status_dtl td on(hdr.GCH_COMPLAINT_ID = GIMC_COMPLAINT_ID) " .
			" join gft_impl_mailstone_master on(GIM_MS_ID=GIMC_MS_ID)" .
			" join gft_ms_task_status s1 on (s1.GTS_STATUS_CODE= td.GIMC_STATUS) " .
			" left join gft_emp_master on (GIMC_APPROVAL_BY=gem_emp_id)" .
			" WHERE (hdr.GCH_CURRENT_STATUS='T1'  or hdr.GCH_CURRENT_STATUS='T6') and GCH_LEAD_CODE=$custCode " .
			" order by GIM_MS_ID ";
	$result=execute_my_query($query,'Support_util.php'); 
	$myarr=array("Training Id","Milestone Name","Approval Date","Standard Duration (min)","Worked Duration (min)","Status","Approval Status","Approval On","Approval By");					
	if(mysqli_num_rows($result)>0){
		print_dtable_header("Training Current Status");
echo<<<END
<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="100%">
END;
		sortheaders($myarr,null,null,null,null);
		while($query_data=mysqli_fetch_array($result)){
			$print_array[0]=array( $query_data['GCH_COMPLAINT_ID'],$query_data['GIM_MS_NAME'],$query_data['GCH_COMPLAINT_DATE'], $query_data['GIMD_ACTUAL_DURATION_MINS'],$query_data['GIMC_WORKED_DURATION'], $query_data['GTS_STATUS_NAME'],$query_data['GIMC_APPROVAL'],$query_data['GIMC_APPROVAL_ON'],$query_data['approval_by']);
			print_resultset($print_array);
		}
		echo "</table>";
		print_dtable_footer();
	}
	$query=" select hdr.GCH_COMPLAINT_ID, GCH_COMPLAINT_DATE, GIM_MS_NAME, GIT_TASK_NAME, GITC_DATE,GITC_ACTUAL_DURATION_MINS, GITC_WORKED_DURATION, GTS_STATUS_NAME, td.GITC_STATUS ".
			" from  gft_customer_support_hdr hdr " .
			" JOIN gft_status_master F on (hdr.GCH_CURRENT_STATUS = F.GTM_CODE) " .
			" join gft_cust_imp_task_current_status_dtl td on(hdr.GCH_COMPLAINT_ID = GITC_COMPLAINT_ID) " .
			" join gft_impl_mailstone_master on(GIM_MS_ID=GITC_MS_ID)" .
			" join gft_impl_task_master on (GIT_TASK_ID=GITC_TASK_ID)" .
			" join gft_ms_task_status s1 on (s1.GTS_STATUS_CODE= td.GITC_STATUS) " .
			" WHERE GCH_LEAD_CODE=$custCode " . // and (hdr.GCH_CURRENT_STATUS='T1'  or hdr.GCH_CURRENT_STATUS='T6') 
			" order by GIM_MS_ID,GIT_TASK_ID ";
	$result=execute_my_query($query,'Support_util.php'); 
	$myarr=array("Training Id","Malestone Name","Task Name","Plan /Activity Date","Actual Duration (min)","Worked Duration (min)","Status");					
	if(mysqli_num_rows($result)>0){
   	print_dtable_header("Training Current Status (Task)");
echo<<<END
<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="100%">
END;
	sortheaders($myarr,null,null,null,null);
	while($query_data=mysqli_fetch_array($result)){
		$print_array[0]=array($query_data['GCH_COMPLAINT_ID'],$query_data['GIM_MS_NAME'],$query_data['GIT_TASK_NAME'], $query_data['GITC_DATE'], $query_data['GITC_ACTUAL_DURATION_MINS'],$query_data['GITC_WORKED_DURATION'], $query_data['GTS_STATUS_NAME']);
		print_resultset($print_array);
	}
	echo "</table>";
	print_dtable_footer();
	}
	$query=" select hdr.GCH_COMPLAINT_ID,GCM_ACTIVITY_ID,sc.GEM_EMP_NAME schedule_to, GCM_DATE, GIM_MS_NAME, GCD_ACTIVITY_DATE, GTS_STATUS_NAME, GCM_DURATION" .
			" From  gft_customer_support_hdr hdr " .
			" join gft_customer_support_dtl dtl on (hdr.GCH_COMPLAINT_ID=dtl.GCD_COMPLAINT_ID) " .
			" join gft_cust_imp_training_ms_log td on(dtl.GCD_ACTIVITY_ID = GCM_ACTIVITY_ID)" .
			" join gft_impl_mailstone_master on(GIM_MS_ID=GCM_MS_ID) " .
			" join gft_ms_task_status s1 on (s1.GTS_STATUS_CODE= td.GCM_STATUS) " .
			" left join gft_emp_master sc on (sc.gem_emp_id=GCD_PROCESS_EMP) " .
			" WHERE GCH_LEAD_CODE=$custCode " .
			" order by GCM_ACTIVITY_ID ";
	$result=execute_my_query($query,'Support_util.php'); 
	$myarr=array("Training Id","Activity id","Milestone Name","Activity Date","Schedule to","Scheduled on","Worked Duration (min)","Status");					
	if(mysqli_num_rows($result)>0){
   	print_dtable_header("Training log (Milestone)");
echo<<<END
<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="100%">
END;
	sortheaders($myarr,null,null,null,null);
	while($query_data=mysqli_fetch_array($result)){
		$print_array[0]=array($query_data['GCH_COMPLAINT_ID'],$query_data['GCM_ACTIVITY_ID'],$query_data['GIM_MS_NAME'],$query_data['GCD_ACTIVITY_DATE'],$query_data['schedule_to'],$query_data['GCM_DATE'], $query_data['GCM_DURATION'], $query_data['GTS_STATUS_NAME']);
		print_resultset($print_array);
	}
	echo "</table>";
	print_dtable_footer();
	}
	$query=" select hdr.GCH_COMPLAINT_ID,GCT_ACTIVITY_ID, GIM_MS_NAME, GIT_TASK_NAME, GCT_DATE, GCD_ACTIVITY_DATE, GTS_STATUS_NAME, GCT_DURATION,GCT_REMARKS" .
			" From  gft_customer_support_hdr hdr " .
			" join gft_customer_support_dtl dtl on (hdr.GCH_COMPLAINT_ID=dtl.GCD_COMPLAINT_ID) " .
			" join gft_cust_imp_training_task_log td on(dtl.GCD_ACTIVITY_ID = GCT_ACTIVITY_ID)" .
			" join gft_impl_mailstone_master on(GIM_MS_ID=GCT_MS_ID) " .
			" join gft_impl_task_master on (GIT_TASK_ID=GCT_TASK_ID)" .
			" join gft_ms_task_status s1 on (s1.GTS_STATUS_CODE= td.GCT_STATUS) " .
			" WHERE GCH_LEAD_CODE=$custCode " .
			" order by GCT_ACTIVITY_ID ";
	$result=execute_my_query($query,'Support_util.php'); 
	$myarr=array("Training Id","Activity id","Milestone Name","Task Name","schedul / Ativity Date","Activity Date","Worked Duration (min)","Status","Remarks");					
	if(mysqli_num_rows($result)>0){
   	print_dtable_header("Training log (Task)");
echo<<<END
<table cellpadding="0" cellspacing="2"  border="0" class="FormBorder1" width="100%">
END;
	sortheaders($myarr,null,null,null,null);
	while($query_data=mysqli_fetch_array($result)){
		$print_array[0]=array($query_data['GCH_COMPLAINT_ID'],$query_data['GCT_ACTIVITY_ID'],$query_data['GIM_MS_NAME'],$query_data['GIT_TASK_NAME'],
			$query_data['GCT_DATE'], $query_data['GCD_ACTIVITY_DATE'], $query_data['GCT_DURATION'], $query_data['GTS_STATUS_NAME'], $query_data['GCT_REMARKS']);
		print_resultset($print_array);
	}
	echo "</table>";
	print_dtable_footer();
	}		
}

/**
 * @param string $trans_id
 * 
 * @return string
 */
function get_call_status($trans_id) {
	$gtc_call_status = '';
	if($trans_id!='' || $trans_id!='0') {
		$query = "select GTC_CALL_STATUS from gft_techsupport_incomming_call_temp where GTC_TRANS_ID='$trans_id' ";
		$res = execute_my_query($query);
		if($row = mysqli_fetch_array($res)){
			$gtc_call_status = $row['GTC_CALL_STATUS'];
		}
	}
	return $gtc_call_status;
}

/**
 * @param string $complaint_id
 * @param string $purpose
 * @param string $activity_id
 * @param string $called_phone_no
 * 
 * @return void
 */
function send_no_response_sms_to_customer($complaint_id,$purpose,$activity_id,$called_phone_no='') {
	global $uid;
	$sms_template_id=$mail_template_id=0;$contact_type=0;$cust_id='';
	$db_sms_content_config=/*. (string[string][int]) .*/array();
	$remaining_attempt = (int)get_samee_const('No_Response_Attempt') - get_no_response_count($complaint_id);
	$query = " select GLH_LEAD_CODE, GLH_CUST_NAME,DATE(GCD_SCHEDULE_DATE) as sch_date, TIME(GCD_SCHEDULE_DATE) as sch_time ".
			 " from gft_customer_support_dtl sd ".
			 " join gft_customer_support_hdr sh on (sh.GCH_COMPLAINT_ID = sd.GCD_COMPLAINT_ID) ".
			 " join gft_lead_hdr lh on (lh.GLH_LEAD_CODE = sh.GCH_LEAD_CODE) ".
			 " where sd.GCD_ACTIVITY_ID='$activity_id' ";
	$res = execute_my_query($query);
	if($row = mysqli_fetch_array($res)) {
		$cust_id = $row['GLH_LEAD_CODE'];
		$cust_name = $row['GLH_CUST_NAME'];
		$schedule_date = $row['sch_date'];
		$schedule_time = $row['sch_time'];
		$db_sms_content_config = array(
			"complaint_id"=>array($complaint_id),
			"comp_id"=>array($complaint_id),
			"Customer_Name"=>array($cust_name),
			"to_date"=>array($schedule_date),
			"to_time"=>array($schedule_time),
			"no_of_times"=>array($remaining_attempt)
		);
	}
	if($called_phone_no!='') {
		$contact_type = get_contact_type_of_phone_no($cust_id,$called_phone_no);
	}
	$mobile_no_arr = explode(',',get_mobil_no_customer($cust_id));
	$mobile_no = $mobile_no_arr[0];
	if($purpose=='support_closed'){
		$sms_template_id = 155;
		$mail_template_id = 176;
	}else if($purpose=='support_attempt' && ($remaining_attempt > 0) ){
		$sms_template_id = 154;
		if($contact_type==1){
			$mobile_no = $called_phone_no;
		}
	}
	if($sms_template_id!=0) {
		$sms_content=htmlentities(get_formatted_content($db_sms_content_config,$sms_template_id));
		entry_sending_sms_to_customer($mobile_no,$sms_content,$sms_template_id,(int)$cust_id,0,$uid,0,null,true);
	}
	if($mail_template_id!=0){
		send_formatted_mail_content($db_sms_content_config, 6, $mail_template_id, null, array($cust_id));
	}
}

/**
 * @param string $lead_code
 * @param string $complaint_id
 *
 * @return void
 */
function send_no_internet_sms_mail($lead_code,$complaint_id) {
	global $uid;
	$sms_template_id	=	169;
	$mail_template_id	=	221;
	
	$db_sms_content_config = array(
				"comp_id"=>array($complaint_id),
		);
	$sms_content=htmlentities(get_formatted_content($db_sms_content_config,$sms_template_id));
	entry_sending_sms_to_customer(null,$sms_content,$sms_template_id,(int)$lead_code,0,$uid);
	
	$contact_arr = customerContactDetail($lead_code);
	$email_str = isset($contact_arr['EMAIL'])?$contact_arr['EMAIL']:'';
	$email_arr = explode(',', $email_str);
	send_formatted_mail_content($db_sms_content_config, 6, $mail_template_id, null, null,$email_arr);
}
/**
 * @param string[int] $employee_ids
 * @param string $open_time
 * @param string $close_time
 * @param string $assure_care_company
 * @param string $lead_condition
 * @return string
 */
function get_support_history_table($employee_ids,$open_time,$close_time,$assure_care_company='1',$lead_condition='') {
    $query_cond = " and GCD_ACTIVITY_DATE >= '$open_time' and GCD_ACTIVITY_DATE <= '$close_time' ";
    if(in_array($assure_care_company,array('2','3'))) {
        $query_cond .= " and gsp_company_id='$assure_care_company' ";
    } else if(is_array($employee_ids) && count($employee_ids)>0){
        $emp_id_list = implode(",",$employee_ids);
        if($emp_id_list!='') {
            $query_cond .= " and GCD_EMPLOYEE_ID in ($emp_id_list) ";
        }
    }
    $rep_query = " select lh.GLH_LEAD_CODE, h.GCH_COMPLAINT_ID, concat(lh.GLH_CUST_NAME,'-',ifnull(lh.GLH_CUST_STREETADDR2,'')) as lead_name, ".
                 " em.gem_emp_name, st.gtm_name, if(GCD_VISIT_TIMEOUT > '0000-00-00 00:00:00', ".
                 " timediff(GCD_VISIT_TIMEOUT, GCD_ACTIVITY_DATE), '') as duration, ".
                 " nt.GCM_NATURE, chat_id, K.GAM_ACTIVITY_DESC, d.GCD_PROBLEM_DESC, d.GCD_REMARKS, d.GCD_INTERNAL_EMOTION, ".
                 " sv.gsm_name, pv.gpm_name, emt.GCM_EMOTION_NAME, ".
                 " if(GCH_ASS_CUST='Y','ALR Customer',if(GCH_ASS_CUST='N','Non ALR Customer','')) as IS_ASA,GCD_VN_TRANSID, ".
                 " GPM_PRODUCT_ABR,GCH_VERSION from gft_customer_support_hdr h ".
                 " join gft_customer_support_dtl d on (h.GCH_COMPLAINT_ID =d.GCD_COMPLAINT_ID) ".
                 " join gft_lead_hdr lh on (lh.GLH_LEAD_CODE=h.GCH_LEAD_CODE) ".
                 " join gft_support_product_group on (glh_main_product=gsp_group_id) ".
                 " left join gft_emp_master em on (em.gem_emp_id=d.GCD_EMPLOYEE_ID) ".
                 " left join gft_customer_emotion_master emt on (d.GCD_CUSTOMER_EMOTION=emt.GCM_EMOTION_ID) ".
                 " left join gft_status_master st on (d.GCD_STATUS=st.gtm_code) ".
                 " left join gft_priority_master pv on  (d.GCD_PRIORITY=pv.gpm_code) ".
                 " left join gft_severity_master sv on  (d.GCD_SEVERITY=sv.gsm_code) ".
                 " left join gft_complaint_nature_master nt on (GCD_NATURE=nt.GCM_NATURE_ID) ".
                 " LEFT JOIN gft_activity_master K ON (d.GCD_VISIT_REASON=K.GAM_ACTIVITY_ID) ".
                 " left join gft_product_family_master on (GPM_PRODUCT_CODE=GCH_PRODUCT_CODE) ".
                 " left join gft_chat_wrapup_dtl cw ON(GCW_ACTIVITY_ID=gcd_activity_id AND GCW_COMPLAINT_TYPE=1)".
                 " where 1 $query_cond $lead_condition order by lead_name ";
    $rep_res = execute_my_query($rep_query);
    $sl_no = 0;
    $emp_name_hdr = "";
    $dr_cols = "<th>Customer Emotion</th><th>ALR Status</th>";
    if(in_array($assure_care_company,array('2','3'))) { // Call from support MIS
        $emp_name_hdr = "<th>Activity By</th>";
        $dr_cols = "";
    }
    $content = "<tr><th width='40px'>Sl.No</th><th>Support Id</th><th>Shop Name - Location</th>$emp_name_hdr<th>Problem Description</th>".
               "<th>Solution Given</th><th>Internal Emotion</th><th>Status</th><th>Duration</th><th>Nature</th>".
               "<th>Severity</th><th>Priority </th>$dr_cols</tr>";
    if(mysqli_num_rows($rep_res) == 0) {
        $content .= "<tr><td colspan='14' align='center'>No Activity Found</td></tr>";
    }else {
        $prev_lead_code = "";
        $unique_lead_sno = 0;
        $lead_code_count_arr=/*. (int[string]) .*/array();
        while ($row3 = mysqli_fetch_array($rep_res)){
            $this_lead_code = $row3['GLH_LEAD_CODE'];
            if(!isset($lead_code_count_arr[$this_lead_code])){
                $lead_code_count_arr[$this_lead_code]=0;
            }
            $lead_code_count_arr[$this_lead_code]++;
        }
        mysqli_data_seek($rep_res,0);
        while ($row3 = mysqli_fetch_array($rep_res)){
            $sl_no++;
            $audio_link = "";
            $chat_link  = "";
            $trans_id  = $row3['GCD_VN_TRANSID'];
            $chat_id 	= $row3['chat_id'];
            if( ($trans_id!='') && ($trans_id!='0') ){
                $temp_link = CURRENT_SERVER_URL."/techsupport_call_refference.php?transId=$trans_id";
                $audio_link = "<a href='$temp_link' target='_blank'>[A]</a>";
            }
            if($chat_id!=""){
                $temp_link = CURRENT_SERVER_URL."/chatbot_transcript.php?chat_id=$chat_id";
                $chat_link  = "<a href='$temp_link' target='_blank'>[H]</a>";
            }
            $this_lead_code = $row3['GLH_LEAD_CODE'];
            if($prev_lead_code!=$this_lead_code){
                $unique_lead_sno++;
                $prev_lead_code = $this_lead_code;
            }
            $rowcount=$lead_code_count_arr[$this_lead_code];
            $lead_code_count_arr[$this_lead_code]=-1; //Reset the previous value to skip next loop.
            $content .= "<tr>";
            if ($rowcount == 1){
                $content .="<td rowspan=$rowcount>$unique_lead_sno </td>";
            }else if ($rowcount != -1){
                $content .="<td rowspan=$rowcount>$unique_lead_sno  <br><br>(Activities: $rowcount)</td>";
            }
            $prod_name_version = $row3['GPM_PRODUCT_ABR']."&nbsp;".$row3['GCH_VERSION'];
            $esc_problem_desc=htmlspecialchars($row3['GCD_PROBLEM_DESC'])."<br> [$prod_name_version] ";
            $content .= "<td>".$row3['GCH_COMPLAINT_ID']." ". $audio_link."</td>".
                        "<td>".$row3['lead_name']."</td>".
                        (in_array($assure_care_company,array('2','3'))?"<td>".$row3['gem_emp_name']."</td>":"").
                        "<td>".$esc_problem_desc."</td>".
                        "<td>".$row3['GCD_REMARKS']."</td>"."<td>".$row3['GCD_INTERNAL_EMOTION']."</td>".
                        "<td>".$row3['gtm_name']."</td><td>".$row3['duration']."</td>".
                        "<td>".$row3['GCM_NATURE']."&nbsp;".$chat_link."</td>".
                        "<td>".$row3['gsm_name']."</td><td>".$row3['gpm_name']."</td>".
                        ($assure_care_company=='1'?"<td>".$row3['GCM_EMOTION_NAME']."</td><td>".$row3['IS_ASA']."</td></tr>":"");
        }
        $content = "<tr><td colspan='14'>Total Unique Customers: $unique_lead_sno , Total Activities : $sl_no</td></tr>".$content;
    }
    return $content;
}
/**
 * @param int $employee_id
 *
 * @return string
 */
function get_pending_support_mail_count($employee_id){
    $total_count_with_ticket_ids = "" ;
    $result_support_peding = execute_my_query("select  COUNT(GCS_ID) TOTAL_PENDING, GCS_OWNER_EMP,group_concat(GCH_COMPLAINT_ID) as ticket_ids   from gft_customer_mail_hdr ".
        " LEFT JOIN gft_mail_support ON(GCS_ID=GMS_MAIL_HDR_ID)".
        " LEFT JOIN gft_customer_support_hdr on(GCH_COMPLAINT_ID=GMS_SUPPORT_ID)".
        " LEFT JOIN gft_customer_support_dtl on(gcd_activity_id=GCH_LAST_ACTIVITY_ID)".
        " where  GCS_MAIL_STATUS in (1,4)  AND  IF(GCD_PROCESS_EMP IS NULL || (GCD_STATUS='T2'),GCS_OWNER_EMP,GCD_PROCESS_EMP)=$employee_id AND GCS_FROM_MAIL_ID NOT LIKE '%@gofrugal.com'  group by GCS_OWNER_EMP");
    if((mysqli_num_rows($result_support_peding)>0) && $row_support_peding=mysqli_fetch_assoc($result_support_peding)){
        return $row_support_peding['TOTAL_PENDING'].'***'.$row_support_peding["ticket_ids"];
    }
    return $total_count_with_ticket_ids;
}
/**
 * @param int $employee_code
 * @param boolean $for_mail
 *  
 * @return string[string]
 */
function get_dcr_content($employee_code, $for_mail=false){
	$time_diff_hrs = 0;
	$domain_name	= CURRENT_SERVER_URL."/";
	$buffer_hrs = (int)get_samee_const("Daily_Report_Buffer");
	$close_time = date('Y-m-d H:i:s');
	$que_res = execute_my_query("select GDR_DATETIME, TIMESTAMPDIFF(HOUR, GDR_DATETIME, now()) as diff from gft_daily_report where GDR_EMP_ID='$employee_code' order by GDR_ID desc limit 1");
	if($row = mysqli_fetch_array($que_res)){
		$last_submitted = $row['GDR_DATETIME'];
		$time_diff_hrs	= (int)$row['diff'];
	}else{
		$last_submitted = date('Y-m-d 00:00:00');
	}
	$open_time = $last_submitted;
	if($time_diff_hrs > $buffer_hrs){
		$open_time = date('Y-m-d H:i:s', (strtotime($close_time) - ($buffer_hrs*60*60)) );
	}
	$ret_arr = /*. (string[string]) .*/array();
	$yester_date = date('Y-m-d',mktime(null,null,null,date('m'),date('d')-1,date('Y')));
	$not_in_status = "3,7";
	$pend_link = $call_entry = $unique_text =$pending_wrapup_link= '';
	$support_status_filter = Cache::getString("dcr_support_status_filter");
	if ($support_status_filter === null){
		$query_res = execute_my_query("select GTM_CODE from gft_status_master where GTM_GROUP_ID not in ($not_in_status)");
		$support_status_filter = '';
		while($row1 = mysqli_fetch_array($query_res)){
			$val = $row1['GTM_CODE'];
			if($support_status_filter==""){
				$support_status_filter .= "support_status[]=$val";
			}else{
				$support_status_filter .= "&support_status[]=$val";
			}
		}
		Cache::putString("dcr_support_status_filter",$support_status_filter,86400); //one day
	}
	$que1=" select count(GCD_PROCESS_EMP) as total_pend, GCD_PROCESS_EMP ".
			" from gft_customer_support_hdr ".
			" join gft_customer_support_dtl on (GCD_ACTIVITY_ID= GCH_LAST_ACTIVITY_ID) ".
			" join gft_status_master on (GTM_CODE=gch_current_status) ".
			" where GCD_PROCESS_EMP=$employee_code and GTM_STATUS='A' and GTM_GROUP_ID not in ($not_in_status) ".
			" group by GCD_PROCESS_EMP ";
	$res1 = execute_my_query($que1);
	if($row1 = mysqli_fetch_array($res1)){
		$total_pend = $row1['total_pend'];
		$url_link = $domain_name."telesupport.php?emp_code=$employee_code&chk_assigned=on&from_dt=&to_dt=&$support_status_filter&assure_care_company=0";
		$pend_link = "<a href='$url_link' target='_blank'>$total_pend</a>";
	}
	$gtc_date_cond = " and GTC_DATE >= '$open_time' and GTC_DATE <= '$close_time' ";
	$gcd_date_cond = " and GCD_ACTIVITY_DATE >= '$open_time' and GCD_ACTIVITY_DATE <= '$close_time' ";
	$gld_date_cond = " and GLD_DATE>='$open_time' and GLD_DATE<='$close_time' ";
	$que2=" select t1.total_calls, t1.unique_calls, (t2.total_support+t3.total_visit) as total_entries, (t2.unique_support+t3.unique_visit) as unique_entries  from (  ".
			" (select count(GTC_AGENT_ID) as total_calls, count(distinct GTC_LEAD_CODE) as unique_calls from gft_techsupport_incomming_call ".  
			" join gft_voicenap_group on (GVG_GROUP_ID=GTC_MAIN_GROUP) where GVG_SUPPORT_GROUP not in ('13') and GTC_AGENT_ID=$employee_code and GTC_MAIN_DIV!='13'  and GTC_CALL_STATUS in (1,4,5) $gtc_date_cond) t1, ".
			" (select count(GCD_EMPLOYEE_ID) as total_support, count(distinct GCH_LEAD_CODE) as unique_support from gft_customer_support_dtl join gft_customer_support_hdr on (GCH_COMPLAINT_ID=GCD_COMPLAINT_ID) where GCD_EMPLOYEE_ID=$employee_code $gcd_date_cond) t2, ".
			" (select count(gld_activity_by) as total_visit, count(distinct GLD_LEAD_CODE) as unique_visit from gft_activity where gld_activity_by=$employee_code  and GLD_VISIT_NATURE not in (10,99) $gld_date_cond) t3 ".
			" )";
	$res2 = execute_my_query($que2);
	$ret_arr['pending_entries_cnt'] = '0';
	if($row2 = mysqli_fetch_array($res2)){
		$temp_entry_link = $domain_name."telesupport.php?emp_code=$employee_code&from_dt_time=$open_time&to_dt_time=$close_time";
		$temp_call_link = $domain_name."tech_incomming_call_details.php?vs_call_agent=$employee_code&from_dt_time=$open_time&to_dt_time=$close_time";
		//	$entry_link = "<a href='$temp_entry_link' target='_blank'>".$row2['total_entries']."</a>";
		$entry_link = $row2['total_entries'];
		$call_link = "<a href='$temp_call_link' target='_blank'>".$row2['total_calls']."</a>";
		$call_entry = $call_link." / ".$entry_link;
		$unique_calls 	= (int)$row2['unique_calls'];
		$unique_entries = (int)$row2['unique_entries'];
		$unique_text 	= $unique_calls." / ".$unique_entries;
		$sel_que=" select GLH_LEAD_CODE,GLH_LEAD_TYPE,concat(GLH_CUST_NAME,'-',ifnull(GLH_CUST_STREETADDR2,'')) as cust_name,GTC_TRANS_ID from gft_lead_hdr ".
				 " join gft_techsupport_incomming_call on (GTC_LEAD_CODE=GLH_LEAD_CODE $gtc_date_cond and GTC_AGENT_ID='$employee_code' and GTC_CALL_STATUS in (1,4,5) and GTC_MAIN_DIV!='13') ".
				 " left join gft_voicenap_group on (GVG_GROUP_ID=GTC_MAIN_GROUP) ";
		if(is_authorized_group_list("$employee_code", array(6))) {
			$sel_que = " select t.* from (select GLH_LEAD_TYPE,GLH_LEAD_CODE,concat(GLH_CUST_NAME,'-',ifnull(GLH_CUST_STREETADDR2,'')) as cust_name, ".
					   " GTC_TRANS_ID from gft_techsupport_incomming_call join gft_lead_hdr on (gtc_lead_code=glh_lead_code) ".
					   " left join gft_voicenap_group on (GVG_GROUP_ID=GTC_MAIN_GROUP) ".
					   " where (gtc_activity_id is null) and gtc_date>='$open_time' and gtc_agent_id='$employee_code' and GTC_MAIN_DIV!='13' ".
					   " and GVG_SUPPORT_GROUP not in ('13') and gtc_transfer_type not in ('CF','T') and gtc_call_status!='3' order by gtc_lead_code desc) t group by t.GLH_LEAD_CODE ";
		} else {
			$sel_que .= " where GLH_LEAD_CODE not in (select GCH_LEAD_CODE from gft_customer_support_dtl join gft_customer_support_hdr on (GCH_COMPLAINT_ID=GCD_COMPLAINT_ID) where GCD_EMPLOYEE_ID=$employee_code $gcd_date_cond )  ".
						" and GLH_LEAD_CODE not in (select GLD_LEAD_CODE from gft_activity where gld_activity_by=$employee_code  and GLD_VISIT_NATURE not in (10,99) $gld_date_cond) ".
						" and GVG_SUPPORT_GROUP not in ('13') group by GLH_LEAD_CODE ";
		}
		$list_res = execute_my_query($sel_que);
		if(mysqli_num_rows($list_res) > 0){
			$validation_cnt = 0;
			$unique_alert_txt = "";
			$unique_text .= "<div style='line-height:2.0;'><b>Missing Activity Entries for the Following Customer Calls</b>";
			$cnt = 1;
			$activity_link = '';
			while($row_data = mysqli_fetch_array($list_res)){
				$activity_url = "tele_support_activity.php?lead_code=$row_data[GLH_LEAD_CODE]&TransId=$row_data[GTC_TRANS_ID]";
				if(is_authorized_group_list($employee_code, array(27))) {
					$activity_url = "visit_details.php?lead_code=$row_data[GLH_LEAD_CODE]";
				}
				if(!in_array($row_data['GLH_LEAD_TYPE'],array('3','13','8'))) {
					$validation_cnt++;
				}
				$unique_text .= <<<END
<br><a onMouseover="ddrivetip('Activity Entry','#EFEFEF', 200)"; onMouseout="hideddrivetip()"; style="text-decoration:none;"
href='http://$_SERVER[SERVER_NAME]/$activity_url' target=_blank>
END;
				$unique_alert_txt .= ($unique_alert_txt=="")?$row_data['GLH_LEAD_CODE']:",".$row_data['GLH_LEAD_CODE'];
				$unique_text .= "$cnt. Customer Id:".$row_data['GLH_LEAD_CODE']." - ".$row_data['cust_name'];
				$unique_text .= "</a>";
				$cnt++;
			}
			$unique_text .= "</div>";
			$ret_arr['unique_alert_txt'] = $unique_alert_txt;
			$ret_arr['pending_entries_cnt'] = "$validation_cnt";
		}
	}
	$content = get_support_history_table(array($employee_code),$open_time,$close_time);
		
	
	$activity_summary = get_activity_history_for_daily_report($employee_code, null, $open_time, $close_time, null);
	$activity_txt = $activity_summary['activity_txt'];
	$sql_query = 	" select cc.id from chatbot.conversation_dtl  cc ".
	   	            " left join chatbot.split_chat_dtl scd on (scd.chat_id=cc.id and scd.split_status!='3') ".
					" where (1) AND cc.created_date>='$open_time' AND cc.created_date<='$close_time' ".
					" AND chat_status=3 and cc.review_status='1' and ".
					" ((scd.split_id is null and cc.agent_user_id='$employee_code') or ".
	                " (scd.split_id is not null and scd.support_agent_id='$employee_code' and scd.review_status=1)) ";	
	$result1=execute_my_query($sql_query);
	$pending_wrapup_count = (int)mysqli_num_rows($result1);
	$pending_wrapup_count_str = "$pending_wrapup_count";
	if($pending_wrapup_count>0){
		$temp_link = "chatbot_details_report.php?from_dt_time=$open_time&to_dt_time=$close_time&emp_code=$employee_code&chatbot_review_status=1&include_split=1";
		$pending_wrapup_count_str = "&nbsp;<a href='$temp_link' target='_blank'>$pending_wrapup_count</a>";
		
	}
	//Check total number of chat engaged
	$chat_summary = "";
	$sql_chat_engaged = " select  count(distinct(cust_id)) total_unique_chat from chatbot.conversation_dtl cc ".
						" where (1) AND cc.agent_user_id='$employee_code' AND cc.created_date>='$open_time' AND ".
						" cc.created_date<='$close_time'  group by  cc.agent_user_id";
	$res_chat_engaged = execute_my_query($sql_chat_engaged);
	if($row_chat_engaged=mysqli_fetch_array($res_chat_engaged)){
		$total_chat_engaged = (int)$row_chat_engaged['total_unique_chat'];
		$sql_chat_feedback = 	" select  rating, count(rating) total_feedback, sum(rating) total_sum_rating from(".
								" select cf.chat_id, rating, group_concat(GRR_REASON_CODE) reason_code from chatbot.conversation_dtl cc ".
								" INNER JOIN chatbot.customer_feedback cf ON(cf.chat_id=cc.id)".
								" LEFT JOIN gft_rating_reason_code_dtl ON(GRR_REF_ID=cf.id AND GRR_SOURCE_TYPE=2) ".
								" where (1) AND cf.agent_id='$employee_code' AND cc.created_date>='$open_time' ".
								" AND cc.created_date<='$close_time' group by cf.id) cf where (((!FIND_IN_SET('7',reason_code)) AND (!FIND_IN_SET('13',reason_code))) OR isnull(reason_code))";
		$res_chat_feedback_uni = execute_my_query($sql_chat_feedback." group by cf.chat_id");
		$total_feedback_unique_cust = mysqli_num_rows($res_chat_feedback_uni);
		$res_chat_feedback = execute_my_query($sql_chat_feedback." group by rating order by rating");
		$rating_dtl = "";
		$total_feedback = 0;
		$sum_of_tatal_rating = 0;
		while($row_chat_feedkack=mysqli_fetch_array($res_chat_feedback)){
			$rating_no 		= (int)$row_chat_feedkack['rating'];
			$total_rating 	= (int)$row_chat_feedkack['total_feedback'];
			$sum_of_rating 	= (int)$row_chat_feedkack['total_sum_rating'];
			$total_feedback = ($total_feedback+$total_rating);
			$sum_of_tatal_rating = ($sum_of_tatal_rating+$sum_of_rating);
			$rating_dtl .= "<tr bgcolor='#FFC300'><td><b>$rating_no STAR</b></td><td><b>$total_rating</b></td></tr>";
		}
		if($total_chat_engaged>0){			
			$chat_summary  = "<tr bgcolor='#2792cf'><td><b>Total Unique Customer</b></td><td><b>$total_chat_engaged &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td></tr>";
			$chat_summary .= "<tr bgcolor='#2792cf'><td><b>No.of Unique Customer Feedback</b></td><td><b>$total_feedback_unique_cust</b></td></tr>";
			$chat_summary .= "<tr bgcolor='#2792cf'><td><b>Total Feedback</b></td><td><b>$total_feedback</b></td></tr>";
			if($sum_of_tatal_rating>0 && $total_feedback>0){
				$avg_feedback = $sum_of_tatal_rating/$total_feedback;
				$avg_feedback = round($avg_feedback,2);
				$chat_summary .= "<tr bgcolor='#2792cf'><td><b>Avg of total ratings & feedbacks</b></td><td><b>$avg_feedback</b></td></tr>";
			}			
			$chat_summary .= "<tr><td colspan='2'>&nbsp;&nbsp;</td></tr>";
			$chat_summary .= "<tr><td colspan='2'><table border='1' width='100%'><tr><th>Rating</th><th>Total</th></tr>$rating_dtl</table></td></tr>";
			$chat_summary = "<table border='1'>$chat_summary</table>";
		}
	}		
	$open_date 	= date("Y-m-d 00:00:00", strtotime($open_time)); 
	$close_date	= date("Y-m-d 23:59:59", strtotime($close_time)); 
	$pending_call_dtl = get_pending_callback_details($employee_code, $open_date, $close_date);
	$total_count = (int)$pending_call_dtl[0];
	$pending_count = $pending_call_dtl[1];
	$callback_url	= 	$domain_name."tech_incomming_call_details.php?from_dt=$open_time&".
						"to_dt=$close_time&vs_call_agent=$employee_code&call_received_medium=3";
	$cloud_callback = 	"<a href='$callback_url&vs_callback_status=N'  target='_blank'>".
						"$pending_count</a> / ".
						"<a href='$callback_url' target='_blank'>$total_count</a>";
	$pending_support_mail = get_pending_support_mail_count($employee_code);
	$pending_support_mail_count = 0;
	if($pending_support_mail!=""){
    	$pending_support_mail_arr = explode('***', $pending_support_mail);
    	$pending_support_mail_count = $pending_support_mail_arr[0];
    	if($pending_support_mail_count>0){
    	    $pending_support_mail_count = "<a href='".$domain_name."customer_mail_report.php?emp_code=$employee_code&". 
    	   	           "from_dt=&to_dt=&customer_mail_status[]=1&customer_mail_status[]=4&unknow_support_mail_type=1&list_pending_action=1&page_limit=all' ". 
    	               "target='_blank'>$pending_support_mail_count</a>";
    	}
	}
	
	$ret_arr['call_entry'] 		= $call_entry;
	$ret_arr['cloud_callback'] 	= $cloud_callback;
	$ret_arr['unique_entry'] 	= $unique_text;
	$ret_arr['pend_compl'] 		= $pend_link;
	$ret_arr['sh_content'] 		= $content;
	$ret_arr['visit_history'] 	= $activity_txt;
	$ret_arr['pending_wrapup'] 	= $pending_wrapup_count_str;
	$ret_arr['pending_wrapup_count'] 	= $pending_wrapup_count;
	$ret_arr['chat_summary'] 	= $chat_summary;
	$ret_arr['activity_summary'] = $activity_summary;
	$ret_arr['support_mail_pending'] = $pending_support_mail_count;
	return $ret_arr;
}

/**
 * @param string $prod_id
 * @param string $emplyoee_id
 * @param string $received_date
 * 
 * @return void
 */
function update_coupon_in_daily_achieved($prod_id,$emplyoee_id,$received_date=''){
	$sel_que=" select m1.GFT_SKEW_PROPERTY, m2.GPM_LICENSE_TYPE from gft_product_master m1 ".
			" left join gft_product_master m2 on (m2.GPM_PRODUCT_CODE=m1.GFT_LOWER_PCODE and m2.GPM_PRODUCT_SKEW=m1.GFT_LOWER_SKEW ) ".
			" where concat(m1.GPM_PRODUCT_CODE,m1.GPM_PRODUCT_SKEW)='$prod_id' ";
	$sel_res = execute_my_query($sel_que);
	$metric_id = "";
	if($rowd = mysqli_fetch_array($sel_res)){
		$skew_prop = $rowd['GFT_SKEW_PROPERTY'];
		if(in_array($skew_prop,array('1','11'))){
			$metric_id = 91;
		}else if($skew_prop=='2'){
			$metric_id = 93;
			if($rowd['GPM_LICENSE_TYPE']=='3'){  //trial to upgradation
				$metric_id = 91;
			}
		}else if(in_array($skew_prop, array('5','6','9','10','7','8','12'))){
			$metric_id = 92;
		}
	}
	if($metric_id!=''){
		update_daily_achieved($emplyoee_id, $metric_id, 1, $received_date);
	}
}

/**
 * @param string $emp_id
 * @param string $lead_code
 * @param string $act_id
 * @return void
 */
function update_tech_support_incoming_for_act_id($emp_id,$lead_code,$act_id) { 
    $que_res = execute_my_query("select GDR_DATETIME from gft_daily_report where GDR_EMP_ID='$emp_id' order by GDR_ID desc limit 1");
    $last_submit = '';
    if($row = mysqli_fetch_array($que_res)) {
        $last_submit = $row['GDR_DATETIME'];
    }
    if($act_id!='' and $lead_code!='' and $last_submit!=''){
        $calls_qry = " select t.gtc_id from (select gtc_id from gft_techsupport_incomming_call where gtc_lead_code='$lead_code' ".
                     " and gtc_date>='$last_submit' and GTC_AGENT_ID=$emp_id and gtc_transfer_type not in ('CF','T') ".
                     " and (gtc_activity_id is null or gtc_activity_id='')) t ";
        $calls_res = execute_my_query($calls_qry);
        $ids = "";
        while($row = mysqli_fetch_array($calls_res)) {
            $ids .= ($ids!="")?",":"";
            $ids .= $row['gtc_id'];
        }
        if($ids!="") {
            execute_my_query("update gft_techsupport_incomming_call set gtc_activity_id='$act_id' where gtc_id in ($ids) ");
        }
    }
}

/**
 * @param string $cust_id
 * 
 * @return string
 */
function get_support_owner_for_a_customer($cust_id){
	$que1 = " select GSP_MYGOFRUGAL_EMP from gft_lead_hdr ".
			" join gft_support_product_group on (GSP_GROUP_ID=GLH_MAIN_PRODUCT) ".
			" join gft_emp_master on (GEM_EMP_ID=GSP_MYGOFRUGAL_EMP) ".
			" where GLH_LEAD_CODE='$cust_id' and GEM_STATUS='A' ";
	$res1 = execute_my_query($que1);
	$process_emp = "";
	if($row1 = mysqli_fetch_array($res1)){
		$process_emp = $row1['GSP_MYGOFRUGAL_EMP'];
	}
	return $process_emp;
}

/**
 * @param string[int] $support_id_arr
 * @param string $solution_given
 * @param string $support_status
 * @param string $old_status
 * @param string $emp_id
 *
 * @return void
 */
function update_support_id_to_solved($support_id_arr,$solution_given,$support_status,$old_status="",$emp_id=""){
	$i=0;
	$remarks_val 	= "'$solution_given'";
	$employee_id 	= "9999";
	if($support_status=='T2'){
		$remarks_val = "GCD_REMARKS";
		$employee_id = "GCD_EMPLOYEE_ID";
	}
	if($old_status=="T36" && $emp_id!=""){ 
	    $remarks_val = "'$solution_given'";
	    $employee_id = "$emp_id";
	}
	foreach ($support_id_arr as $key_id) {
		$dtl_que=" insert into gft_customer_support_dtl (GCD_COMPLAINT_ID, gcd_activity_id, GCD_ACTIVITY_DATE, GCD_EMPLOYEE_ID, GCD_REPORTED_DATE, ".
				" GCD_NATURE, gcd_status, GCD_CONTACT_TYPE, GCD_CONTACT_PERSION, GCD_CUSTOMER_EMOTION, GCD_CUST_CALL_TYPE, gcd_contact_no, ".
				" GCD_CONTACT_MAILID, GCD_PROCESS_EMP, GCD_TO_DO, GCD_SCHEDULE_DATE, GCD_ESTIMATED_TIME, GCD_SEVERITY, GCD_PRIORITY, ".
				" GCD_LEVEL, GCD_PROMISE_MADE, GCD_PROMISE_DATE, GCD_FEEDBACK, GCD_COMPLAINT_CODE,GCD_PRODUCT_MODULE, GCD_PRODUCT_CODE, GCD_PROBLEM_SUMMARY, ".
				" GCD_PROBLEM_DESC, GCD_UPLOAD_FILE, GCD_REMARKS, GCD_VISIT_REASON, GCD_VISIT_TIMEOUT, GCD_EXTRA_CHARGES, GCD_VISIT_NO, ".
				" GCD_RECEIVED_IN_HO, GCD_ESCALATION, GCD_ESCALATION_RESP, GCD_LAST_ACTIVITY_OF_DAY, GCD_VN_TRANSID, GCD_NO_RESPONSE_REASON,GCD_SERVICE_TYPE,GCD_SUB_STATUS,GCD_EFFORT_IN_DAYS) ".
				" (select GCD_COMPLAINT_ID, '', now(), $employee_id, GCD_REPORTED_DATE, ".
				" GCD_NATURE, '$support_status', GCD_CONTACT_TYPE, GCD_CONTACT_PERSION, GCD_CUSTOMER_EMOTION, GCD_CUST_CALL_TYPE, gcd_contact_no, ".
				" GCD_CONTACT_MAILID, GCD_PROCESS_EMP, GCD_TO_DO, now(), GCD_ESTIMATED_TIME, GCD_SEVERITY, GCD_PRIORITY, ".
				" GCD_LEVEL, GCD_PROMISE_MADE, GCD_PROMISE_DATE, GCD_FEEDBACK, GCD_COMPLAINT_CODE,GCD_PRODUCT_MODULE, GCD_PRODUCT_CODE, GCD_PROBLEM_SUMMARY, ".
				" GCD_PROBLEM_DESC, GCD_UPLOAD_FILE, $remarks_val, GCD_VISIT_REASON, GCD_VISIT_TIMEOUT, GCD_EXTRA_CHARGES, GCD_VISIT_NO, ".
				" GCD_RECEIVED_IN_HO, GCD_ESCALATION, GCD_ESCALATION_RESP, 'Y', '', GCD_NO_RESPONSE_REASON,GCD_SERVICE_TYPE,GCD_SUB_STATUS,GCD_EFFORT_IN_DAYS ".
				" from gft_customer_support_hdr join gft_customer_support_dtl on (GCH_LAST_ACTIVITY_ID=gcd_activity_id) where GCH_COMPLAINT_ID='$key_id' and gch_current_status!='T1') ";
		$dtl_res = execute_my_query($dtl_que);
		if($dtl_res){
			$i++;
			$last_act_id = mysqli_insert_id_wrapper();
			updated_hdr_with_last_actid($key_id, $last_act_id, $support_status);
			if($old_status!="T36" && $emp_id==""){
			    echo "$i Support Id - $key_id updated as $support_status <br>";
			}
		}
	}
}
/**
 * @param string $param
 * @return string
 */
function get_main_prod_for_param($param) {
	$gps = '';
	switch ($param) {
		case '1':
			$gps = '4';
			break;
		case '2':
			$gps = '5';
			break;
		case '3':
			$gps = '3';
			break;
		case '4':
			$gps = '6';
			break;
		case '5':
			$gps = '7';
			break;
		case '6':
			$gps = '1';
			break;
		case '7':
			$gps = '17'; // presales and presales intl
			break;
		case '8':
		    $gps = '12'; // TRAC
		    break;
		case '9':
		    $gps = '36'; // ServQuick
		    break;
		default:
			$gps = '4';
			break;
	}
	return $gps;
}
/**
 * @param mixed[] $search_array
 * @param mixed $delete_value
 * @return mixed[]
 */
function remove_value_from_array($search_array,$delete_value) {
    if(($key = array_search($delete_value, $search_array))!==false) {
        unset($search_array[$key]);
    }
    return $search_array;
}
/**
 * @param string $param
 * @param string $emp_code
 * @param string $from_dt
 * @param string $to_dt
 * @param string[int] $all_chat_ids
 * @param string[int] $all_split_ids
 * @return mixed[string]
 */
function get_chat_summary_report($param='1',$emp_code='',$from_dt='',$to_dt='',$all_chat_ids=null,$all_split_ids=null) {
    global $assure_care_company;
	$return_arr = /*.(mixed[string]).*/array();
	$engaged_cust_ids = '';
	$gps = get_groups_for_param($param);
	$emp_gps = get_groups_for_param($param,'web_group');
	$from_dt = db_date_format($from_dt);
	$to_dt = db_date_format($to_dt);
	$role_wh = get_team_condition_for_param($param,'','cd.chat_status','cd.support_group_id');
    $role_wh .= assure_care_company_filter_condition();
	$qry =<<<END
select count(distinct(cd.id)) total_chats, count(distinct(cd.cust_id)) cust_count,
count(distinct(case cd.chat_status when 4 then cd.id end)) not_routed,
count(distinct(case cd.chat_status when 1 then cd.id end)) initiated_count,sum(if(cd.chat_status=1 and cd1.id is null,1,0)) 
abandoned_chats,count(distinct(case cd.chat_status when 2 then cd.id end)) self_serviced
from chatbot.conversation_dtl cd 
left join gft_emp_master on (gem_emp_id=cd.agent_user_id)
left join chatbot.conversation_dtl cd1 on (cd.cust_id=cd1.cust_id and cd1.created_date>='$from_dt 00:00:00' and 
cd1.created_date<='$to_dt 23:59:59' and cd1.chat_status in (2,3)) 
join gft_lead_hdr on (glh_lead_code=cd.cust_id) join gft_support_product_group on (gsp_group_id=glh_main_product)
where cd.created_date>='$from_dt 00:00:00' and cd.created_date<='$to_dt 23:59:59' $role_wh
END;
	$res = execute_my_query($qry);
	$row_count = mysqli_num_rows($res);
	$return_arr['headers'] = array("Chat","Count");
	$emp_data = array();
	if($row_count>0) {
		$domain = CURRENT_SERVER_URL;
		$url = $domain."/chatbot_conversations.php?from_dt=$from_dt&to_dt=$to_dt&team_name_select=$param&assure_care_company=$assure_care_company";
		$gp_arr = explode(",",$gps);
		if($assure_care_company=='2') {
		    $url .= "&support_product_group_multi[]=37"; // Patanjali team
		} else {
    		foreach($gp_arr as $gi) {
    		    $url .= "&support_product_group_multi[]=$gi";
    		}
		}
		$total_chats_count = count(remove_value_from_array(array_unique($all_chat_ids),''));
		while($row = mysqli_fetch_assoc($res)) {
			$emp_row = /*.(string[string]).*/array();
			$emp_data[] = array('Total no. of Chats',"<a style='color:black;' target=_blank href='$url'>$total_chats_count</a>");
			$emp_data[] = array('Total no. of Unique customers',$row['cust_count']);
			$emp_data[] = array('Initiated Chats',"<a style='color:black;' target=_blank href='$url&chatbot_status=1'>".$row['initiated_count']."</a>");
			$emp_data[] = array('Total no. of chats abondened without engagement',"<a href='$url&abandoned_chats=1' target=_blank style='color:black;'>".$row['abandoned_chats']."</a>");
			$emp_data[] = array('Total no. of self service chats',"<a href='$url&chatbot_status=2' target=_blank style='color:black;'>".$row['self_serviced']."</a>");
			$emp_data[] = array('Total no. of not routed chats',"<a href='$url&chatbot_status=4' target=_blank style='color:black;'>".$row['not_routed']."</a>");
			$emp_data[] = array('Total no. of chats repeated',(string)((int)$row['total_chats']-(int)$row['cust_count']));
			$query_count = count(remove_value_from_array(array_unique($all_split_ids),''));
			$emp_data[] = array('Total no. of queries',"$query_count");
		}
	}
	$return_arr['values'] = $emp_data;
	$return_arr['team_totals'] = array();
	$return_arr['alignment'] = array('left','right');
	$return_arr['width'] = array('50%','50%');
	return $return_arr;
}
/**
 * @param string $param
 * @param string $emp_code
 * @param string $from_date
 * @param string $to_date
 * @return mixed[string]
 */
function get_chat_feedback_report($param='1',$emp_code='',$from_date='',$to_date='',$all_cust_ids=null,$feedback_taken_chats=null) {
    global $assure_care_company;
	$return_arr = /*.(mixed[string]).*/array();
	$gps = get_groups_for_param($param);
	$emp_gps = get_groups_for_param($param,'web_group');
	$from_dt = date('Y-m-d 00:00:00');
	if($from_date!='') {
	    $from_dt = "$from_date 00:00:00";
	}
	$to_dt = date('Y-m-d 23:59:59');
	if($to_date!='') {
	    $to_dt = "$to_date 23:59:59";
	}
	$role_wh = get_team_condition_for_param($param,'','cd.chat_status','cd.support_group_id');
    $role_wh .= assure_care_company_filter_condition();
    $split_counts = array();
    $split_count_qry = " select support_agent_id,count(distinct split_id) qry_cnt from chatbot.conversation_dtl cd ".
                       " join chatbot.split_chat_dtl on (id=chat_id) ".
                       " join gft_emp_master on (gem_emp_id=support_agent_id) ".
                       " join gft_lead_hdr on (glh_lead_code=cust_id) ".
                       " join gft_support_product_group on (gsp_group_id=glh_main_product) ".
                       " where created_date>='$from_dt' and created_date<='$to_dt' and split_status!='3' and ".
                       " chat_status in ('3') $role_wh group by gem_emp_id ";
    $split_count_res = execute_my_query($split_count_qry);
    while($s_row = mysqli_fetch_assoc($split_count_res)) {
        $em_id = $s_row['support_agent_id'];
        $split_counts[$em_id] = $s_row['qry_cnt'];
    }
    $partner_support_group = get_groups_for_param($param,'partner');
    $employee_support_group = get_groups_for_param($param,'employee');
    $total_chats_qry = execute_my_query(" select ifnull(gem_emp_id,9999) gem_emp_id,ifnull(gem_emp_name,'Uber') gem_emp_name, ".
                                   " count(distinct(cd.id)) total_chats, ".
                                   " count(distinct(case when fq.chat_id is not null then fq.chat_id end)) req_chat_count, ".
                                   " count(distinct(case when cd.support_group_id IN($employee_support_group) then glh_lead_code end)) emp_chats, ".
                                   " count(distinct(case when cd.support_group_id IN($partner_support_group) then glh_lead_code end)) partner_chats, ".
                                   " count(distinct(cd.cust_id)) unique_cust_count ".
                                   " from chatbot.conversation_dtl cd left join gft_emp_master on (gem_emp_id=cd.agent_user_id) ".
                                   " left join chatbot.customer_feedback cf on (cf.chat_id=cd.id and date(cf.date_time)=date(cd.created_date)) ".
                                   " join gft_lead_hdr on (glh_lead_code=cd.cust_id) ".
                                   " join gft_support_product_group on (gsp_group_id=glh_main_product) ".
                                   " left join chatbot.feedback_req_analytics fq ON (fq.chat_id=cd.id) ".
                                   " where cd.created_date>='$from_dt' and cd.created_date<='$to_dt' $role_wh ".
                                   " and cd.chat_status in ('2','3') group by gem_emp_id ");
    $emp_chat_counts = array();
    $team_total_chats = 0;
    $team_feedbacks_taken = 0;
    $team_no_feedbacks_taken = 0;
    $team_partner_chats = 0; $team_emp_chats = 0;
    $emp_chats = array(); $partner_chats = array();
    $feedback_requests = array();
    $unique_cust_count = array();
    $emp_names = array();
    while($c_row = mysqli_fetch_assoc($total_chats_qry)) {
        $eid = $c_row['gem_emp_id'];
        $emp_names[$eid] = $c_row['gem_emp_name'];
        $emp_chat_counts[$eid] = $c_row['total_chats'];
        $emp_chats[$eid] = $c_row['emp_chats'];
        $partner_chats[$eid] = $c_row['partner_chats'];
        $feedback_requests[$eid] = $c_row['req_chat_count'];
        $unique_cust_count[$eid] = $c_row['unique_cust_count'];
        $team_emp_chats += (int)$c_row['emp_chats'];
        $team_partner_chats += (int)$c_row['partner_chats'];
        $team_total_chats += (int)$c_row['total_chats'];
    }
    $team_feedbacks_taken = count(remove_value_from_array(array_unique($feedback_taken_chats),''));
    $team_no_feedbacks_taken = $team_total_chats - $team_feedbacks_taken;
    $all_cust_ids_count = count(remove_value_from_array(array_unique($all_cust_ids),''));
    $role_wh = get_team_condition_for_param($param,'','chat_status','support_group_id');
    $role_wh .= assure_care_company_filter_condition();
	$dtl_qry = " select gem_emp_id emp_id,gem_emp_name employee_name,web_group,cf.chat_id,cd.cust_id,cf.feedback, ".
	   	       " group_concat(grr_reason_code) reason_code, cf.chat_status,gem_role_id,gsp_company_id, ".
        	   " support_group_id,ifnull(cf.rating,-1) rating,gem_status from chatbot.customer_feedback cf ".
        	   " join chatbot.conversation_dtl cd on (cd.id=cf.chat_id and date(cf.date_time)=date(cd.created_date)) ".
        	   " left join gft_emp_master on (cf.agent_id=gem_emp_id) ".        	   
        	   " LEFT JOIN gft_rating_reason_code_dtl on (GRR_REF_ID=cf.id AND GRR_SOURCE_TYPE=2) ".
        	   " join gft_lead_hdr on (glh_lead_code=cd.cust_id) ".
        	   " join gft_support_product_group on (gsp_group_id=glh_main_product) ".
        	   " where cf.date_time>='$from_dt' and cf.date_time<='$to_dt' and cd.created_date>='$from_dt' ".
        	   " and cd.created_date<='$to_dt' and cf.chat_status in (2,3) ".
        	   " group by cd.id, cf.id order by cf.chat_id ";
	$qry = " select if(chat_status=2,9999,emp_id) emp_id,employee_name,count(distinct(chat_id)) total_chats_with_feedback, ".
           " sum(if(rating=5,1,0)) five_str,sum(if(rating=4,1,0)) four_str,sum(if(rating=3,1,0)) three_str, ".
           " sum(if(rating=1,1,0)) one_str,sum(if(rating!=-1,1,0)) all_str, ".
           " sum(if(rating=2,1,0)) two_str from ($dtl_qry) feedbacks_tbl where ".
           " ((!FIND_IN_SET('7',reason_code) and !FIND_IN_SET('13',reason_code)) OR reason_code is null) ".
           " $role_wh group by emp_id order by employee_name ";
	$domain_name = CURRENT_SERVER_URL;
	$res = execute_my_query($qry);
	$row_count = mysqli_num_rows($res);
	$return_arr['headers'] = array('S.no','Employee','Total Chats','Partner Chats','Employee Chats','Query Count','Total Feedback Req','Total Chats without Feedback','Total Chats with Feedback','Unique Customers','Total Feedback','5 Star','4 Star','3 Star','2 Star','1 Star','Avg','Feedback chats v/s All Chats %');
	$return_arr['alignment'] = array('left','left','right','right','right','right','right','right','right','right','right','right','right','right','right','right','right','center');
	$return_arr['width'] = array('3%','8%','4%','4%','4%','4%','4%','7%','7%','6%','6%','5%','5%','5%','5%','5%','5%','13%');
	$dtls = array();
	$total_unique = $total_feedback = $total_5 = $total_4 = $total_3 = $total_2 = $total_1 = 0;
	$sl = $team_total = $no_feedbk = $total_chats_with_feedback = $total_feedback_req= 0;
	$team_avg = 0.0; $total_split_count = 0;
	$feedback_emps = array();
	$last_rows = array();
	$attribs = "target=_blank style='color:black;'";
	if($row_count>0) {
		$n = $row_count;
		while($row = mysqli_fetch_assoc($res)) {
			$sl++;
			$emp_id = $row['emp_id'];
			$total_5 += (int)$row['five_str'];
			$total_4 += (int)$row['four_str'];
			$total_3 += (int)$row['three_str'];
			$total_2 += (int)$row['two_str'];
			$total_1 += (int)$row['one_str'];
			$total_feedback_req += isset($feedback_requests[$emp_id])?(int)$feedback_requests[$emp_id]:0;	
			$total_feedback += (int)$row['all_str'];
			$all_chat_counts = isset($emp_chat_counts[$emp_id])?$emp_chat_counts[$emp_id]:'0';
			$curr_total = ((int)$row['five_str']*5)+((int)$row['four_str']*4)+((int)$row['three_str']*3)+((int)$row['two_str']*2)+(int)$row['one_str'];
			$team_total += $curr_total;
			$no_feedback = (int)$all_chat_counts-(int)$row['total_chats_with_feedback'];
			$no_feedbk += (int)$no_feedback;
			$avg = 0.0;
			if((int)$row['all_str']>0) {
			    $avg = $curr_total/(int)$row['all_str'];
			}
			$feedback_emps[] = $emp_id;
			if($emp_id=='9999') {
			    $uber_feedback_ratio = '0';
			    if((int)$all_chat_counts>0) {
			        $uber_feedback_ratio = number_format(((int)$row['total_chats_with_feedback']/(int)$all_chat_counts)*100,2);
			    }
			    $last_rows[] =array($sl,'Uber',$all_chat_counts,isset($partner_chats[$emp_id])?$partner_chats[$emp_id]:'0',
			                        isset($emp_chats[$emp_id])?$emp_chats[$emp_id]:'0','',$no_feedback,$row['total_chats_with_feedback'],
			                        isset($unique_cust_count[$emp_id])?$unique_cust_count[$emp_id]:'0',
			                        $row['all_str'],$row['five_str'],$row['four_str'],$row['three_str'],$row['two_str'],$row['one_str'],
			                        number_format($avg,2),$uber_feedback_ratio);
			    continue;
			}
			$no_feedback_link = "0";
			if($no_feedback!="" && $no_feedback!="0"){
				$no_feedback_link = "<a href='$domain_name/chatbot_details_report.php?from_dt=$from_date&to_dt=$to_date&".
				"emp_code=$emp_id&feedback_day=3&assure_care_company=$assure_care_company' target='_blank'>$no_feedback</a>";
			}
			$emp_row = array();
			$emp_row[] = $sl;
			$emp_row[] = $row['employee_name'];
			$emp_row[] = $all_chat_counts;
			$emp_row[] = isset($partner_chats[$emp_id])?$partner_chats[$emp_id]:'0';
			$emp_row[] = isset($emp_chats[$emp_id])?$emp_chats[$emp_id]:'0';
			$emp_row[] = isset($split_counts[$emp_id])?$split_counts[$emp_id]:$all_chat_counts;
			$total_split_count += isset($split_counts[$emp_id])?(int)$split_counts[$emp_id]:(int)$all_chat_counts;
			$emp_row[] = isset($feedback_requests[$emp_id])?"<a $attribs href='$domain_name/chatbot_conversations.php?emp_code=$emp_id&req_feedback=1&from_dt=$from_date&to_dt=$to_date&assure_care_company=$assure_care_company'>".(string)$feedback_requests[$emp_id]."</a>":'0';
			$emp_row[] = $no_feedback_link;
			$emp_row[] = $row['total_chats_with_feedback'];
			$emp_row[] = isset($unique_cust_count[$emp_id])?$unique_cust_count[$emp_id]:'0';
			$fb_url = "$domain_name/chatbot_customer_feedback_report.php?assure_care_company=$assure_care_company&emp_code=$emp_id&feedback_day=1&from_dt=$from_date&to_dt=$to_date&feedback_day=1";
			$emp_row[] = "<a $attribs href='$fb_url'>".$row['all_str']."</a>";
			$emp_row[] = "<a $attribs href='$fb_url&rate_val_arr[]=5'>".$row['five_str']."</a>";
			$emp_row[] = "<a $attribs href='$fb_url&rate_val_arr[]=4'>".$row['four_str']."</a>";
			$emp_row[] = "<a $attribs href='$fb_url&rate_val_arr[]=3'>".$row['three_str']."</a>";
			$emp_row[] = "<a $attribs href='$fb_url&rate_val_arr[]=2'>".$row['two_str']."</a>";
			$emp_row[] = "<a $attribs href='$fb_url&rate_val_arr[]=1'>".$row['one_str']."</a>";
			$emp_row[] = number_format($avg,2);
			$emp_row[] = ((int)$all_chat_counts>0?number_format(((int)$row['total_chats_with_feedback']/(int)$all_chat_counts)*100,2):'0');
			$dtls[] = $emp_row;
		}
	}
	foreach($emp_chat_counts as $e_id=>$cnt) {
	    if(!in_array($e_id, $feedback_emps)) {
	        $sl++;
	        $all_chat_counts = isset($emp_chat_counts["$e_id"])?$emp_chat_counts["$e_id"]:'0';
	        $emp_row = array();
	        $emp_row[] = $sl;
	        $emp_row[] = $emp_names["$e_id"];
	        $emp_row[] = $all_chat_counts;
	        $emp_row[] = isset($partner_chats["$e_id"])?$partner_chats["$e_id"]:'0';
	        $emp_row[] = isset($emp_chats["$e_id"])?$emp_chats["$e_id"]:'0';
	        $emp_row[] = isset($split_counts["$e_id"])?$split_counts["$e_id"]:$all_chat_counts;
	        $total_split_count += isset($split_counts["$e_id"])?(int)$split_counts["$e_id"]:(int)$all_chat_counts;
	        $emp_row[] = isset($feedback_requests["$e_id"])?$feedback_requests["$e_id"]:'0';
	        $total_feedback_req += isset($feedback_requests["$e_id"])?(int)$feedback_requests["$e_id"]:0;
	        $emp_row[] = "<a href='$domain_name/chatbot_details_report.php?from_dt=$from_date&to_dt=$to_date&".
	   	        "emp_code=$e_id&chat_feedback_status=1&assure_care_company=$assure_care_company' target='_blank'>$all_chat_counts</a>";
	        $emp_row[] = '0';
	        $emp_row[] = $unique_cust_count["$e_id"];
	        $emp_row = array_merge($emp_row,array('0','0','0','0','0','0','0','0'));
	        if($e_id=='9999') {
	            $last_rows[] = $emp_row;
	        } else {
	            $dtls[] = $emp_row;
	        }
	    }
	}
	$dtls = array_merge($dtls,$last_rows);
	if($total_feedback>0) {
		$team_avg = $team_total/$total_feedback;
	}
	$return_arr['values'] = $dtls;
	$return_arr['team_totals'] = array('','Total',"$team_total_chats",$team_partner_chats,$team_emp_chats,"$total_split_count",
                                       "$total_feedback_req","$team_no_feedbacks_taken",
                                       "$team_feedbacks_taken","$all_cust_ids_count","$total_feedback","$total_5",
    	                               "$total_4","$total_3","$total_2","$total_1",number_format($team_avg,2),
	    ((int)$team_total_chats>0?number_format(((int)$team_feedbacks_taken/(int)$team_total_chats)*100,2):'0'));
	return $return_arr;
}
/**
 * @param string $team
 * @param string $emp_code
 * @param string $from_date
 * @param string $to_date
 * @param string $custCode
 * @param string $feedback_day
 * @return string
 */
function get_feedback_reason_query($team,$emp_code='',$from_date='',$to_date='',$custCode='',$feedback_day='1') {
    if($from_date=='') {
        $from_date = date('Y-m-d');
    }
    if($to_date=='') {
        $to_date = date('Y-m-d');
    }
    $from_date = (db_date_format($from_date)." 00:00:00");
    $wh_cond = " and cd.created_date>='$from_date' ";
    $to_date = (db_date_format($to_date)." 23:59:59");
    $wh_cond .= " and cd.created_date<='$to_date' ";
    if($feedback_day=='1') {
        $wh_cond .= " and date(cf.date_time)=date(cd.created_date) ";
    } else if($feedback_day=='2') {
        $wh_cond .= " and date(cf.date_time)>date(cd.created_date) ";
    }
    if((int)$emp_code>0) {
        $wh_cond .= " and cf.agent_id='$emp_code' and (agent_reason='' or agent_reason is null) ";
    }
    if((int)$custCode>0) {
        $wh_cond .= " and cf.customer_id='$custCode' ";
    }
    $role_wh = '';
    if((int)$team>0) {
        $role_wh = get_team_condition_for_param($team,'');
    }
    $role_wh .= assure_care_company_filter_condition();
    $qry =<<<END
select customer_name,customer_id,agent_name,agent_reason,agent_action_plan,cid,chat_time,feedback_tims,reason_txt,feedback,cfid from
(select concat(glh_cust_name,if(GLH_CUST_STREETADDR2 is not null,concat('-',GLH_CUST_STREETADDR2),'')) customer_name,cf.customer_id,cd.id cid,cd.created_date chat_time,feedback,cf.id cfid,
cf.date_time feedback_tims,gem_emp_name agent_name, ifnull(agent_reason,'') agent_reason,ifnull(agent_action_plan,'')
agent_action_plan,group_concat(GRR_REASON_CODE) status_code,group_concat(GFR_NAME) reason_txt from chatbot.conversation_dtl cd
join chatbot.customer_feedback cf on (cf.chat_id=cd.id) join gft_emp_master on (gem_emp_id=cf.agent_id) join gft_lead_hdr
on (glh_lead_code=cf.customer_id) LEFT JOIN gft_rating_reason_code_dtl ON(GRR_REF_ID=cf.id AND GRR_SOURCE_TYPE=2)
left join gft_feedback_rating_master on (GFR_ID=GRR_REASON_CODE)
join gft_support_product_group on (gsp_group_id=glh_main_product)
where 1 $wh_cond $role_wh and cf.rating in (0,1) group by cf.id,gem_emp_id) dtls
where ((!FIND_IN_SET('7',status_code) and !FIND_IN_SET('13',status_code)) OR status_code is null) 
END;
    return $qry;
}
/**
 * @param string $param
 * @param string $emp_code
 * @param string $from_date
 * @param string $to_date
 * @return mixed[string]
 */
function get_feedback_reason_report($param,$emp_code='',$from_date='',$to_date='') {
    global $assure_care_company;
	$return_arr = /*.(mixed[string]).*/array();
	$qry = get_feedback_reason_query($param,$emp_code,$from_date,$to_date);
	$res = execute_my_query($qry);
	$return_arr['headers'] = array('Chat ID','Customer','Agent','Reason','Action Plan');
	$rows = array();
	$domain_name = CURRENT_SERVER_URL;
	if(mysqli_num_rows($res)>0) {
		while($row = mysqli_fetch_array($res)) {
			$dtl = array();
			if($emp_code!='0' and $emp_code!='') {
				$dtl[] = $row['customer_id'];
			} else {
				$dtl[] = "<a style='color:black;' href='$domain_name/chatbot_transcript.php?chat_id=".$row['cid']."&assure_care_company=$assure_care_company' target='_blank'>".$row['cid']."</a>";
				$dtl[] = $row['customer_name'];
			}
			$dtl[] = $row['agent_name'];
			$dtl[] = $row['agent_reason'];
			$dtl[] = $row['agent_action_plan'];
			$rows[] = $dtl;
		}
	}
	$return_arr['values'] = $rows;
	return $return_arr;
}
/**
 * @param string $param
 * @param string $emp_code
 * @param string $from_date
 * @param string $to_date
 * @param string $lead_condition
 * @param string $corporate_query_params
 * @return mixed[string]
 */
function get_support_mail_summary($param='1',$emp_code='',$from_date='',$to_date='',$type='',$lead_condition='',$corporate_query_params=''){
    global $assure_care_company;
    $return_arr = /*.(mixed[string]).*/array();
    $gps = get_groups_for_param($param);
    if($assure_care_company=='2') {
        $gps = "37,38,39";
    } else if($assure_care_company=='3') {
        $gps = '40';
    }
    $wh_cond = "";
    $date_con = "";
    $previous_date = date("Y-m-d", strtotime("$from_date -1 days"));
    if($from_date!=""){
        $date_con = " and GCS_RECEIVED_TIME>='$from_date 00:00:00' ";
        $date_con .= " and GCS_RECEIVED_TIME<='$to_date 23:59:59' ";
    } 
    $from_date = ($from_date==""?"2019-04-01":"$from_date");
    if($gps!='') {
        $wh_cond .= " and GSM_SUPPORT_GROUP in ($gps) ";
    }
    $rows = array();
    //$wh_cond .= assure_care_company_filter_condition();
    $domain = CURRENT_SERVER_URL;
    $mail_report_url = "$domain/customer_mail_report.php?support_product_group=$gps".$corporate_query_params;//from_dt=$from_date&to_dt=$to_date&
    $common_query =<<<QUERY
        SELECT PROCESS_EMP, EMP_NAME,SOLVED,PENDING_CUSTOMER,NOT_SOLVED, MAIL_COUNT FROM(
        select   IF(GCD_PROCESS_EMP IS NULL AND GCS_MAIL_STATUS IN (1,4) AND SUBSTRING(GCS_FROM_MAIL_ID, -13)!='@gofrugal.com','9999', 
                    IF(GCD_PROCESS_EMP IS NULL AND GCS_MAIL_STATUS IN (1,4) AND  SUBSTRING(GCS_FROM_MAIL_ID, -13)='@gofrugal.com','10000', GCD_PROCESS_EMP)) PROCESS_EMP, 
        IF(GCD_PROCESS_EMP IS NULL AND GCS_MAIL_STATUS IN (1,4) AND  SUBSTRING(GCS_FROM_MAIL_ID, -13)!='@gofrugal.com','Not Assigned Customer Mail', 
                IF(GCD_PROCESS_EMP IS NULL AND GCS_MAIL_STATUS IN (1,4) AND  SUBSTRING(GCS_FROM_MAIL_ID, -13)='@gofrugal.com','Not Assigned GFT Mail', GEM_EMP_NAME))  EMP_NAME, 
        SUM(IF(GTM_GROUP_ID IN(3,7),1,0)) SOLVED,
        SUM(IF(GTM_GROUP_ID IN(2),1,0)) PENDING_CUSTOMER, 
        SUM(IF(GTM_GROUP_ID NOT IN(2,3,7),1,0)) NOT_SOLVED,
        COUNT(GCS_ID) MAIL_COUNT
        from gft_customer_mail_hdr 
        INNER JOIN gft_support_mail_master on(GCS_SUPPORT_TEAM_ID=GSM_ID)
        LEFT JOIN gft_mail_support ON(GMS_MAIL_HDR_ID=GCS_ID)
        LEFT JOIN gft_customer_support_hdr on(GCH_COMPLAINT_ID=GMS_SUPPORT_ID )
        LEFT JOIN gft_customer_support_dtl on(gcd_activity_id=GCH_LAST_ACTIVITY_ID)
        LEFT JOIN gft_emp_master em ON(GEM_EMP_ID=GCD_PROCESS_EMP)
        LEFT JOIN gft_status_master ON(gch_current_status=GTM_CODE)
        where 1 AND (gch_current_status!='T2' OR gch_current_status IS NULL) $wh_cond  $lead_condition
        
QUERY;
    $rows = array();
    $styl = "style='color:black;'";
    $tot_ic = 0;$tot_mi = 0;$tot_vm = 0;$tot_oc = 0;
    if($type != "overall"){
        $date_str = get_date_in_indian_format_without_time($from_date);
        $rows = array(array("Before $date_str","0","0","0"),array("On $date_str","0","0","0"));        
        $querys[] = "SELECT if(PROCESS_EMP!=10000 AND PROCESS_EMP!=9999,'1',PROCESS_EMP) PROCESS_EMP_NEW, ".
                    " if(PROCESS_EMP!=10000 AND PROCESS_EMP!=9999,'Pending Support',EMP_NAME) EMP_NAME_NEW, ".
                    " SUM(NOT_SOLVED) PENDING_SUPPORT, SUM(MAIL_COUNT) NOT_ASSIGNED FROM(".$common_query.
                    " and GCS_RECEIVED_TIME<'$from_date 00:00:00' GROUP BY PROCESS_EMP ORDER BY GCD_PROCESS_EMP ASC) tbl WHERE 1 AND (NOT_SOLVED>0  OR PROCESS_EMP  IN(10000,9999))) ".
                    " tbl WHERE 1 GROUP BY PROCESS_EMP_NEW";
        $querys[] = "SELECT if(PROCESS_EMP!=10000 AND PROCESS_EMP!=9999,'1',PROCESS_EMP) PROCESS_EMP_NEW, ".
                    " if(PROCESS_EMP!=10000 AND PROCESS_EMP!=9999,'Pending Support',EMP_NAME) EMP_NAME_NEW, ".
                    " SUM(NOT_SOLVED) PENDING_SUPPORT, SUM(MAIL_COUNT) NOT_ASSIGNED FROM(".$common_query.$date_con.
                    " GROUP BY PROCESS_EMP ORDER BY GCD_PROCESS_EMP ASC) tbl WHERE 1 AND (NOT_SOLVED>0  OR PROCESS_EMP  IN(10000,9999)) ) ".
                    " tbl WHERE 1 GROUP BY PROCESS_EMP_NEW";
        $inc = 0;
        $tot_pending_mail=0; $tot_pending_gft=0;$tot_pending_support=0; 
        while($inc<count($querys)){
            $res = execute_my_query($querys[$inc]);
            while($row = mysqli_fetch_assoc($res)) {
                $emp_id = $row['PROCESS_EMP_NEW'];
                if($emp_id==10000){
                    $rows[$inc][1] = $row['NOT_ASSIGNED'];
                }
                if($emp_id==9999){
                    $rows[$inc][2] = $row['NOT_ASSIGNED'];
                } 
                if($emp_id==1){
                    $rows[$inc][3] = $row['PENDING_SUPPORT'];
                }                               
            }
            $tot_pending_gft=$tot_pending_gft+$rows[$inc][1];
            $tot_pending_mail=$tot_pending_mail+$rows[$inc][2]; 
            $tot_pending_support=$tot_pending_support+$rows[$inc][3];
            $date_filter = ($inc==1?"&from_dt=$from_date&to_dt=$to_date":"&to_dt=$previous_date");
            $rows[$inc][1] = "<a $styl href='$mail_report_url$date_filter&unknow_support_mail_type=2' target=_blank>".$rows[$inc][1]."</a>";
            $rows[$inc][2] = "<a $styl href='$mail_report_url$date_filter&unknow_support_mail_type=1' target=_blank>".$rows[$inc][2]."</a>";
            $rows[$inc][3] = "<a $styl href='$mail_report_url$date_filter&mail_support_status=1' target=_blank>".$rows[$inc][3]."</a>";
            $inc++;
            if($inc==2){
                $rows[$inc][0] = "Overall";
                $rows[$inc][1] = "<a $styl href='$mail_report_url&from_dt=2019-04-01&to_dt=$to_date&unknow_support_mail_type=2' target=_blank>".$tot_pending_gft."</a>";
                $rows[$inc][2] = "<a $styl href='$mail_report_url&from_dt=2019-04-01&to_dt=$to_date&unknow_support_mail_type=1' target=_blank>".$tot_pending_mail."</a>";
                $rows[$inc][3] = "<a $styl href='$mail_report_url&from_dt=2019-04-01&to_dt=$to_date&mail_support_status=1' target=_blank>".$tot_pending_support."</a>";
                
            }
        }
        $return_arr['headers'] = array('Mail Summary','Not Assigned GFT Mail','Not Assigned Customer Mail','Pending Support');
        $return_arr['alignment'] = array('left','right','right','right');
        $return_arr['values'] = $rows;
        return $return_arr;
    }
    $today_date = date("Y-m-d");
    $res = execute_my_query("SELECT PROCESS_EMP, EMP_NAME,SOLVED,PENDING_CUSTOMER,NOT_SOLVED, MAIL_COUNT FROM(".$common_query.
                            " GROUP BY PROCESS_EMP ORDER BY GCD_PROCESS_EMP ASC) tbl WHERE 1 AND (NOT_SOLVED>0  OR PROCESS_EMP  IN(10000,9999))". 
                            " ) tbl WHERE 1 AND (NOT_SOLVED>0  OR PROCESS_EMP  IN(10000,9999))");
    while($row = mysqli_fetch_assoc($res)) {
        $agent_id = $row['PROCESS_EMP'];
        $agent_name = $row['EMP_NAME'];
        $emp_id = $row['PROCESS_EMP']=='9999' || $row['PROCESS_EMP']=='10000'?"":$row['PROCESS_EMP'];
        $total = $row['MAIL_COUNT'];
        $solved = $row['SOLVED'];
        $pending_customer = $row['PENDING_CUSTOMER'];
        $pending_support = $row['NOT_SOLVED'];
        if($emp_id!=''){
            $pending_support = "<a $styl href='$mail_report_url&emp_code=$emp_id&mail_support_status=1&from_dt=2019-04-01&to_dt=$today_date' target=_blank>$pending_support</a>";
        }else{
            $filter_id = $row['PROCESS_EMP']=='9999'?"1":"2";
            $total = "<a $styl href='$mail_report_url&unknow_support_mail_type=$filter_id&from_dt=2019-04-01&to_dt=$today_date' target=_blank>$total</a>";
        }
        $curr_row = array($agent_name, $total,$solved,$pending_customer,$pending_support); 
        $rows[] = $curr_row;
    }
    $return_arr['headers'] = array('Support Executive','Total','Solved','Pending Customer','Pending Support');
    $return_arr['alignment'] = array('left','right','right','right','right');
    $return_arr['values'] = $rows;
    return $return_arr;
}
/**
 * @param string $param
 * @param string $emp_code
 * @param string $from_date
 * @param string $to_date
 * @param string $lead_condition
 * @param string $corporate_query_params
 * @return mixed[string]
 */
function get_support_calls_count($param='1',$emp_code='',$from_date='',$to_date='',$lead_condition='',$corporate_query_params='') {
    global $assure_care_company;
	$return_arr = /*.(mixed[string]).*/array();
	$gps = get_groups_for_param($param);
	if($assure_care_company=='2') {
	    $gps = "37,38,39";
	} else if($assure_care_company=='3') {
	    $gps = '40';
	}
	$wh_cond = " and gtc_date>='$from_date 00:00:00' ";
	$wh_cond .= " and gtc_date<='$to_date 23:59:59' ";
	if($gps!='') {
		$wh_cond .= " and GVG_SUPPORT_GROUP in ($gps) ";
	}
	$wh_cond .= assure_care_company_filter_condition();
	$gp_arr = explode(",",$gps);
	$domain = CURRENT_SERVER_URL;
	$url = "$domain/tech_incomming_call_details.php?from_dt=$from_date&to_dt=$to_date&assure_care_company=$assure_care_company".$corporate_query_params;
	foreach ($gp_arr as $gp) {
		$url .= "&support_product_group_multi[]=$gp";
	}
	$all_calls_qry =<<<END
select GTC_CALL_STATUS call_status,GTC_RECALL_STATUS recall_status,GLE_SUPPORT_MODE,GTC_RING_TIME,if(gtc_agent_id is not null 
and gtc_agent_id>0 and gtc_agent_id!=9999,gtc_agent_id,-1) gtc_agent_id,ifnull(gem_emp_name,'Unknown') gem_emp_name 
from gft_techsupport_incomming_call left join gft_voicenap_group on (GVG_GROUP_ID=GTC_MAIN_GROUP)
left join gft_support_product_group on (gsp_group_id=gvg_support_group)
left join gft_lead_hdr_ext on (GTC_LEAD_CODE=gle_lead_code) 
left join gft_lead_hdr ON(GTC_LEAD_CODE=GLH_LEAD_CODE)
left join gft_emp_master on (gem_emp_id=gtc_agent_id)
where 1 and (gtc_specific_reason!='vsmile' or gtc_specific_reason is null) $wh_cond $lead_condition
group by gtc_trans_id,gtc_date,gtc_number,gtc_lead_code,gtc_main_group
END;
	$calls_count_qry =<<<END
select sum(if(call_status=1,1,0)) ic_count,sum(if(call_status=3 and GLE_SUPPORT_MODE=2 and TIME_TO_SEC(GTC_RING_TIME)>=10,1,0)) 
missed_count,gtc_agent_id,gem_emp_name,sum(if(call_status=2 and GLE_SUPPORT_MODE=2,1,0)) vm_count,sum(if(call_status=4,1,0)) oc_count 
from ($all_calls_qry) all_calls group by GTC_AGENT_ID having ic_count+missed_count+vm_count+oc_count>0 order by gem_emp_name
END;
	$res = execute_my_query($calls_count_qry);
	$rows = array();
	$styl = "style='color:black;'";
	$tot_ic = 0;$tot_mi = 0;$tot_vm = 0;$tot_oc = 0;
	while($row = mysqli_fetch_assoc($res)) {
	    $curr_row = array();
	    $emp_id = $row['gtc_agent_id'];
	    $ic_count = $row['ic_count'];
	    $mi_count = $row['missed_count'];
	    $vm_count = $row['vm_count'];
	    $oc_count = $row['oc_count'];
	    $tot_ic += (int)$ic_count;
	    $tot_mi += (int)$mi_count;
	    $tot_vm += (int)$vm_count;
	    $tot_oc += (int)$oc_count;
        $curr_row[] = $row['gem_emp_name'];
        $curr_row[] = "<a $styl href='$url&vs_call_sytatus=1&vs_call_agent=$emp_id' target=_blank>$ic_count</a>";
        $curr_row[] = "<a $styl href='$url&vs_call_sytatus=3&cust_support_type=1&avg_based_on=9&vs_call_agent=$emp_id' target=_blank>$mi_count</a>";
        $curr_row[] = "<a $styl href='$url&vs_call_sytatus=2&cust_support_type=1&vs_call_agent=$emp_id' target=_blank>$vm_count</a>";
        $curr_row[] = "<a $styl href='$url&vs_call_sytatus=4&vs_call_agent=$emp_id' target=_blank>$oc_count</a>";
        $rows[] = $curr_row;
	}
	$tot_ic_link = "<a $styl href='$url&vs_call_sytatus=1' target=_blank>$tot_ic</a>";
	$tot_mi_link = "<a $styl href='$url&vs_call_sytatus=3&cust_support_type=1&avg_based_on=9' target=_blank>$tot_mi</a>";
	$tot_vm_link = "<a $styl href='$url&vs_call_sytatus=2&cust_support_type=1' target=_blank>$tot_vm</a>";
	$tot_oc_link = "<a $styl href='$url&vs_call_sytatus=4' target=_blank>$tot_oc</a>";
	$return_arr['headers'] = array('Agent','Incoming Calls','Missed Calls','Voice Mail','Outgoing Calls');
	$return_arr['alignment'] = array('left','right','right','right','right');
	$return_arr['values'] = $rows;
	$return_arr['team_totals'] = array("Total",$tot_ic_link,$tot_mi_link,$tot_vm_link,$tot_oc_link);
	return $return_arr;
}
/**
 * @param string $param
 * @param string $from_date
 * @param string $to_date
 * @param string $lead_condition
 * @param string $corporate_query_params
 * 
 * @return mixed[string]
 */
function get_support_ticket_summary($param='1',$from_date='',$to_date='',$lead_condition='',$corporate_query_params='') {
    global $assure_care_company;
    $return_arr = /*.(mixed[string]).*/array();
    $gp_condition = '';
    if($assure_care_company=='1') {
        $gp_condition = get_team_condition_for_param($param);
    }
    $one_yr_before = date("Y-m-d H:i:s",strtotime("$from_date 00:00:00 -1 year"));
    $status_codes = "'T3','T15'";
    $company_wh = assure_care_company_filter_condition();
    $opening_qry = <<<END
	select sum(if(complaint_dt<="$from_date 00:00:00" and opening_status in ('T3','T15'), 1, 0)) opening_cnt,
	sum(if(complaint_dt>="$from_date 00:00:00" and complaint_dt<="$to_date 23:59:59", 1, 0)) incoming,
	sum(if(solved_dt>="$from_date 00:00:00" and solved_dt<="$to_date 23:59:59" and curr_status_gp in ('3') and
	((complaint_dt>="$from_date 00:00:00" and complaint_dt<="$to_date 23:59:59") or (gcd_complaint_id=dtl2.open_complaint_id
	and dtl2.opening_status in ('T3','T15'))),1,0)) solved,count(distinct if(curr_day_status not in ('T3','T15') and
	curr_day_status is not null and curr_status_gp not in ('3') and curr_status_gp is not null and
	((complaint_dt>="$from_date 00:00:00" and complaint_dt<="$to_date 23:59:59") or (gcd_complaint_id=dtl2.open_complaint_id
	and dtl2.opening_status in ('T3','T15'))), gcd_complaint_id, null)) responded,sum(if(complaint_dt<="$to_date 23:59:59"
	and closing_status in ('T3','T15'), 1, 0)) closing from (select dtl1.gcd_status closing_status,
	gch_solved_time solved_dt,dtl3.gcd_status curr_day_status, status3.gtm_group_id curr_status_gp,
	gch_complaint_date complaint_dt,dtl3.gcd_complaint_id gcd_complaint_id,gch_complaint_id,gsp_company_id from gft_customer_support_hdr
	join gft_customer_support_dtl fir on (gch_first_activity_id=fir.gcd_activity_id and gch_complaint_id=fir.gcd_complaint_id
	and fir.gcd_nature in (18,20,23)) join gft_customer_support_dtl las on (las.gcd_complaint_id=gch_complaint_id and
    las.gcd_activity_id=gch_last_activity_id) join gft_emp_master on (las.gcd_process_emp=gem_emp_id $gp_condition)
    join gft_lead_hdr on (glh_lead_code=gch_lead_code) join gft_support_product_group on (glh_main_product=gsp_group_id)
    left join gft_customer_support_dtl dtl1 on
	(gch_complaint_id=dtl1.gcd_complaint_id and dtl1.gcd_activity_id in (select max(gcd_activity_id)
	from gft_customer_support_dtl where gcd_activity_date>="$one_yr_before" and gcd_activity_date<="$to_date 23:59:59" $lead_condition group by
	gcd_complaint_id)) left join gft_customer_support_dtl dtl3 on (gch_complaint_id=dtl3.gcd_complaint_id and dtl3.gcd_activity_id
	in (select max(gcd_activity_id) from gft_customer_support_dtl where gcd_activity_date>="$from_date 00:00:00" and
	gcd_activity_date<="$to_date 23:59:59" group by gcd_complaint_id) and dtl3.gcd_activity_id!=gch_first_activity_id) left
	join gft_status_master status3 on (gtm_code=dtl3.gcd_status) where gch_complaint_date>="$one_yr_before" and
	gch_complaint_date<="$to_date 23:59:59" $lead_condition group by gch_complaint_id) dtls left join (select dtl2.gcd_status opening_status,
	dtl2.gcd_complaint_id as open_complaint_id from gft_customer_support_dtl dtl2 where dtl2.gcd_activity_id in
	(select max(gcd_activity_id) from gft_customer_support_dtl where gcd_activity_date>="$one_yr_before" and
	gcd_activity_date<="$from_date 00:00:00" group by gcd_complaint_id)) dtl2 on (gch_complaint_id=dtl2.open_complaint_id)
    where 1 $company_wh 
END;
    $responded = $open = $solved = $incom = $closing = '0';
    $return_arr['headers'] = array('Status','Count');
    $opening_res = execute_my_query($opening_qry);
    while($opening_row = mysqli_fetch_array($opening_res)) {
        $open = $opening_row['opening_cnt'];
        $incom = $opening_row['incoming'];
        $solved = $opening_row['solved'];
        $responded = $opening_row['responded'];
        $closing = $opening_row['closing'];
    }
    if(datediff($to_date,date('Y-m-d'))==0 and (int)$closing>0){
        $sam_domain = CURRENT_SERVER_URL;
        $one_yr_dt = date("Y-m-d",strtotime("$from_date 00:00:00 -1 year"));
        $closing = "<a href='$sam_domain/telesupport.php?support_activity_multi[]=18&support_activity_multi[]=20".
            "&support_activity_multi[]=23&support_status[]=T3&support_status[]=T15&chk_assigned=on&team_name_select=$param".
            "$corporate_query_params&from_dt=$one_yr_dt&to_dt=$to_date&assure_care_company=$assure_care_company' target=_blank>$closing</a>";
    }
    $return_arr['values'][] = array('Open Pending',$open);
    $return_arr['values'][] = array('Incoming',$incom);
    $return_arr['values'][] = array('Solved',$solved);
    $return_arr['values'][] = array('Responded',$responded);
    $return_arr['values'][] = array('Overall Pending',$closing);
    $return_arr['alignment'] = array('left','right');
    return $return_arr;
}
/**
 * @param string $param
 * @param string $from_dt
 * @param string $to_dt
 * @param string $lead_condition
 * @param string $corporate_query_params
 * 
 * @return mixed[string]
 */
function get_support_ticket_counts($param,$from_dt,$to_dt,$lead_condition='',$corporate_query_params='') {
    global $assure_care_company;
	$return_arr = /*.(mixed[string]).*/array();
	$gps = get_groups_for_param($param);
	$status_codes = "'T3','T15'";
	$status_url = "&support_status[]=T3&support_status[]=T15";
	if($param!='4') {
		$status_codes = "'T3'";
		$status_url = "&support_status[]=T3";
	}
	$gp_condition = '';
	if($assure_care_company=='1') {
	   $gp_condition = get_team_condition_for_param($param);
	}
	$gp_condition .= assure_care_company_filter_condition();
	$url_prev_dt = date('Y-m-d',strtotime("$from_dt -1 day"));
	$url_from_dt = $from_dt;
	$url_to_dt = $to_dt;
	$url_last_yr = date("Y-m-d",strtotime("$from_dt -1 year"));
	$from_dt .= " 00:00:00";
	$to_dt .= " 23:59:59";
	$one_yr_before = date("Y-m-d H:i:s",strtotime("$from_dt -1 year"));
	$solved_statuses = '';
	$solved_arr = get_support_status_master(null,3);
	$solved_url = '';
	foreach($solved_arr as $i=>$rowi) {
		$solved_statuses .= ",'".$rowi[0]."'";
		$solved_url .= "&support_status[]=".$rowi[0];
	}
	$qry =<<<END
select sum(if(gch_current_status in ($status_codes) and position=1,1,0)) pending_support_exp,
sum(if(gch_current_status in ($status_codes) and position=5,1,0)) pending_support_safe,
sum(if(gch_current_status in ($status_codes),1,0)) pending_support,
sum(if(gch_current_status in ('T17') and position=1,1,0)) pending_dev_support_exp,
sum(if(gch_current_status in ('T17') and position=5,1,0)) pending_dev_support_safe,
sum(if(gch_current_status in ('T17'),1,0)) pending_dev_support,
sum(if(gch_current_status in ('T2') and (gdc_mantis_status is null or gdc_mantis_status not in (80,90)) and position=1,1,0)) pending_dev_exp,
sum(if(gch_current_status in ('T2') and (gdc_mantis_status is null or gdc_mantis_status not in (80,90)) and position=5,1,0)) pending_dev_safe,
sum(if(gch_current_status in ('T2') and (gdc_mantis_status is null or gdc_mantis_status not in (80,90)) ,1,0)) pending_dev,
sum(if((gtm_group_id in ('3') or gdc_mantis_status in (80,90)) and prob=1,1,0)) solved,assigned_to,emp_id from
(select gch_current_status,gch_solved_time,gch_complaint_date,gtm_group_id,prob,gdc_mantis_status,
case when(now() >= GCH_RESTORE_TIME) then '1' when(now() < GCH_RESTORE_TIME) then '5' else '6' end as position,
if(las.gcd_process_emp is not null and las.gcd_process_emp>0 and las.gcd_process_emp!=9999,las.gcd_process_emp,9999) emp_id,
ifnull(gem_emp_name,'Unassigned') assigned_to from gft_customer_support_hdr 
join gft_customer_support_dtl fir on (fir.gcd_activity_id=gch_first_activity_id 
and fir.gcd_complaint_id=gch_complaint_id and fir.gcd_nature in (18,20,23)) join gft_customer_support_dtl las 
on (las.gcd_activity_id=gch_last_activity_id and las.gcd_complaint_id=gch_complaint_id)
left join gft_dev_complaints on (GDC_COMPLAINT_ID=GCH_COMPLAINT_ID)
left join gft_emp_master on (las.gcd_process_emp=gem_emp_id) 
join gft_lead_hdr on (glh_lead_code=gch_lead_code) join gft_support_product_group on (gsp_group_id=glh_main_product)
join gft_complaint_nature_master on (fir.gcd_nature=GCM_NATURE_ID)
join gft_status_master on (gtm_code=gch_current_status)  
where gch_complaint_date>='$one_yr_before' and gch_complaint_date<='$to_dt' $gp_condition $lead_condition) dtls
group by assigned_to having pending_support+pending_dev_support>0 order by emp_id
END;
	$res = execute_my_query($qry);
	$split_up = array('Resolution Time Expired','Resolution Time Not Expired','Total');
	$return_arr['headers'] = array('0'=>'Employee','Pending Support'=>$split_up,'Pending Dev Support'=>$split_up);
	$return_arr['alignment'] = array('left','right','right','right','right','right','right');
	$vals = array();
	$domain_name = CURRENT_SERVER_URL;
	$solved_statuses = get_status(null,false,null,array('3'));
	$solved_url = '';
	foreach ($solved_statuses as $s_arr) {
		$solved_url .= "&support_status[]=".(string)$s_arr[0];
	}
	$styl = "style='color:black;'";
	while($row = mysqli_fetch_assoc($res)) {
		$emp_id = $row['emp_id'];
		$url = "$domain_name/telesupport.php?from_dt=$url_last_yr&to_dt=$url_to_dt&team_name_select=$param&chk_assigned=on$corporate_query_params&support_activity_multi[]=18&support_activity_multi[]=20&support_activity_multi[]=23&emp_code=$emp_id&chk_assigned=on&assure_care_company=$assure_care_company";
		$this_row = array();
		$this_row[] = $row['assigned_to'];
		$this_row[] = "<a $styl href='$url&skip_auto_refresh=1$status_url&res_time_status=1' target=_blank>".$row['pending_support_exp']."</a>";
		$this_row[] = "<a $styl href='$url&skip_auto_refresh=1$status_url&res_time_status=2' target=_blank>".$row['pending_support_safe']."</a>";
		$this_row[] = "<a $styl href='$url&skip_auto_refresh=1$status_url' target=_blank>".$row['pending_support']."</a>";
		$this_row[] = "<a $styl href='$url&support_status[]=T17&skip_auto_refresh=1&res_time_status=1' target=_blank>".$row['pending_dev_support_exp']."</a>";
		$this_row[] = "<a $styl href='$url&support_status[]=T17&skip_auto_refresh=1&res_time_status=2' target=_blank>".$row['pending_dev_support_safe']."</a>";
		$this_row[] = "<a $styl href='$url&support_status[]=T17&skip_auto_refresh=1' target=_blank>".$row['pending_dev_support']."</a>";
		$vals[] = $this_row;
	}
	$return_arr['values'] = $vals;
	return $return_arr;
}
/**
 * @param string $param
 * @param string $from_dt
 * @param string $to_dt
 * @param string $lead_condition
 * @param string $corporate_query_params
 * @return mixed[string]
 */
function get_iot_summary($param,$from_dt,$to_dt,$lead_condition='',$corporate_query_params='') {
    global $assure_care_company;
	$return_arr = /*.(mixed[string]).*/array();
	$url_from_dt = $from_dt;
	$url_prev_dt = date('Y-m-d',strtotime("$from_dt -1 day"));
	$url_to_dt = $to_dt;
	$url_one_yr_dt = date('Y-m-d',strtotime("$from_dt -1 year"));
	$from_dt = " $from_dt 00:00:00 ";
	$to_dt = " $to_dt 23:59:59 ";
	$one_yr_dt = date('Y-m-d H:i:s',strtotime("$from_dt -1 year"));
	$wh_con = '';
	if($assure_care_company=='1') {
	   $wh_con = get_team_condition_for_param($param);
	}
	$wh_con .= assure_care_company_filter_condition();
	$qry =<<<END
select sum(if(gch_complaint_date>='$one_yr_dt' and gch_complaint_date<'$from_dt' and gch_current_status in ('T3','T15'),1,0)) old_support,
sum(if(gch_complaint_date>='$from_dt' and gch_complaint_date<='$to_dt' and gch_current_status in ('T3','T15'),1,0)) new_support, 
sum(if(gch_complaint_date>='$one_yr_dt' and gch_complaint_date<='$to_dt' and gch_current_status in ('T3','T15'),1,0)) all_support,
sum(if(gch_complaint_date>='$one_yr_dt' and gch_complaint_date<'$from_dt' and gch_current_status in ('T17'),1,0)) old_devs,
sum(if(gch_complaint_date>='$from_dt' and gch_complaint_date<='$to_dt' and gch_current_status in ('T17'),1,0)) new_devs,
sum(if(gch_complaint_date>='$one_yr_dt' and gch_complaint_date<='$to_dt' and gch_current_status in ('T17'),1,0)) all_devs,
sum(if(gch_complaint_date>='$one_yr_dt' and gch_complaint_date<'$from_dt' and gch_current_status in ('T2')
and if(gdc_mantis_status is null,1,gdc_mantis_status not in (80,90)),1,0)) old_dev,sum(if(gch_complaint_date>='$from_dt' 
and gch_complaint_date<='$to_dt' and gch_current_status in ('T2') and if(gdc_mantis_status is null,1,
gdc_mantis_status not in (80,90)),1,0)) new_dev, 
sum(if(gch_complaint_date>='$one_yr_dt' and gch_complaint_date<='$to_dt' and gch_current_status in ('T2') 
and if(gdc_mantis_status is null,1, gdc_mantis_status not in (80,90)),1,0)) all_dev,
sum(if(gch_complaint_date>='$one_yr_dt' and gch_complaint_date<'$from_dt' and (gtm_status_group in (3) 
or gdc_mantis_status in (80,90)),1,0)) old_solved, sum(if(gch_complaint_date>='$from_dt' and 
gch_complaint_date<='$to_dt' and (gtm_status_group in (3) or gdc_mantis_status in (80,90)),1,0)) new_solved,
sum(if(gch_complaint_date>='$one_yr_dt' and gch_complaint_date<='$to_dt' and (gtm_status_group in (3) 
or gdc_mantis_status in (80,90)),1,0)) all_solved from (select gch_complaint_date,gch_current_status,prob,
gtm_group_id gtm_status_group,gdc_mantis_status from gft_customer_support_hdr 
join gft_customer_support_dtl f on (f.gcd_activity_id=gch_first_activity_id and f.gcd_complaint_id=gch_complaint_id)
join gft_customer_support_dtl l on (l.gcd_activity_id=gch_last_activity_id and l.gcd_complaint_id=gch_complaint_id) 
join gft_status_master on (gtm_code=gch_current_status) join gft_emp_master on (l.gcd_process_emp=gem_emp_id)
join gft_lead_hdr on (glh_lead_Code=gch_lead_code) join gft_support_product_group on (gsp_group_id=glh_main_product)
left join gft_dev_complaints on (gdc_complaint_id=gch_complaint_id) where f.gcd_nature='22' and gch_complaint_date>='$one_yr_dt' 
and gch_complaint_date<='$to_dt' $wh_con $lead_condition) dtls
END;
	$solved_statuses = get_status(null,false,null,array('3'));
	$solved_url = '';
	foreach ($solved_statuses as $s_arr) {
		$solved_url .= "&support_status[]=".$s_arr[0];
	}
	$res = execute_my_query($qry);
	$return_arr['headers'] = array('','Pending Support and Pending SQA','Pending Dev Support','Pending Developer','Solved');
	$return_arr['alignment'] = array('left','right','right','right','right');
	$vals = array();
	$domain_name = CURRENT_SERVER_URL."/telesupport.php?team_name_select=$param&chk_assigned=on$corporate_query_params&assure_care_company=$assure_care_company";
	while($row = mysqli_fetch_array($res)) {
		$this_row = array();
		$this_row[] = 'Before '.date('M d,Y',strtotime($from_dt));
		$this_row[] = "<a href='$domain_name&from_dt=$url_one_yr_dt&to_dt=$url_prev_dt&support_activity=22&support_status[]=T3&support_status[]=T15' target=_blank style='color:black;'>".$row['old_support']."</a>";
		$this_row[] = "<a href='$domain_name&from_dt=$url_one_yr_dt&to_dt=$url_prev_dt&support_activity=22&support_status[]=T17' target=_blank style='color:black;'>".$row['old_devs']."</a>";
		$this_row[] = "<a href='$domain_name&from_dt=$url_one_yr_dt&to_dt=$url_prev_dt&support_activity=22&support_status[]=T2&include_mantis_sync=on' target=_blank style='color:black;'>".$row['old_dev']."</a>";
		$this_row[] = "<a href='$domain_name&from_dt=$url_one_yr_dt&to_dt=$url_prev_dt&support_activity=22$solved_url&include_mantis_sync=on' target=_blank style='color:black;'>".$row['old_solved']."</a>";
		$vals[] = $this_row;
		$this_row = array();
		$this_row[] = ($url_from_dt==$url_to_dt?"On ".date("M d,Y",strtotime($from_dt)):"Between ".date("M d,Y",strtotime($from_dt))." and ".date("M d,Y",strtotime($to_dt)));
		$this_row[] = "<a href='$domain_name&from_dt=$url_from_dt&to_dt=$url_to_dt&support_activity=22&support_status[]=T3&support_status[]=T15' target=_blank style='color:black;'>".$row['new_support']."</a>";
		$this_row[] = "<a href='$domain_name&from_dt=$url_from_dt&to_dt=$url_to_dt&support_activity=22&support_status[]=T17' target=_blank style='color:black;'>".$row['new_devs']."</a>";
		$this_row[] = "<a href='$domain_name&from_dt=$url_from_dt&to_dt=$url_to_dt&support_activity=22&support_status[]=T2&include_mantis_sync=on' target=_blank style='color:black;'>".$row['new_dev']."</a>";
		$this_row[] = "<a href='$domain_name&from_dt=$url_from_dt&to_dt=$url_to_dt&support_activity=22$solved_url&include_mantis_sync=on' target=_blank style='color:black;'>".$row['new_solved']."</a>";
		$vals[] = $this_row;
		$this_row = array();
		$this_row[] = 'Overall';
		$this_row[] = "<a href='$domain_name&from_dt=$url_one_yr_dt&to_dt=$url_to_dt&support_activity=22&support_status[]=T3&support_status[]=T15' target=_blank style='color:black;'>".$row['all_support']."</a>";
		$this_row[] = "<a href='$domain_name&from_dt=$url_one_yr_dt&to_dt=$url_to_dt&support_activity=22&support_status[]=T17' target=_blank style='color:black;'>".$row['all_devs']."</a>";
		$this_row[] = "<a href='$domain_name&from_dt=$url_one_yr_dt&to_dt=$url_to_dt&support_activity=22&support_status[]=T2&include_mantis_sync=on' target=_blank style='color:black;'>".$row['all_dev']."</a>";
		$this_row[] = "<a href='$domain_name&from_dt=$url_one_yr_dt&to_dt=$url_to_dt&support_activity=22$solved_url&include_mantis_sync=on' target=_blank style='color:black;'>".$row['all_solved']."</a>";
		$vals[] = $this_row;
	}
	$return_arr['values'] = $vals;
	return $return_arr;
}
/**
 * @param string $param
 * @param string $from_dt
 * @param string $to_dt
 * @param string $lead_condition
 * @param string $corporate_query_params
 * @return mixed[string]
 */
function get_iot_summary_ownerwise($param,$from_dt,$to_dt,$lead_condition='',$corporate_query_params='') {
    global $assure_care_company;
	$one_yr_dt = date('Y-m-d',strtotime("$from_dt -1 year"));
	$wh_con = '';
	if($assure_care_company=='1') {
	   $wh_con = get_team_condition_for_param($param);
	}
	$wh_con .= assure_care_company_filter_condition();
	$qry = <<<END
select ifnull(gem_emp_name,'Unassigned') emp_name,ifnull(gem_emp_id,'') emp_id,sum(if(gch_current_status in ('T3','T15'),1,0))
pending_support,sum(if(gch_current_status in ('T17'),1,0)) pending_dev_support,sum(if(gch_current_status in ('T2') 
and if(gdc_mantis_status is null,1, gdc_mantis_status not in (80,90)),1,0)) pending_dev 
from (select gch_current_status, gtm_group_id gtm_status_group, gdc_mantis_status, 
las.gcd_process_emp emp from gft_customer_support_hdr  
join gft_customer_support_dtl fir on (fir.gcd_activity_id=gch_first_activity_id and fir.gcd_complaint_id=gch_complaint_id)
join gft_customer_support_dtl las on (las.gcd_activity_id=gch_last_activity_id and las.gcd_complaint_id=gch_complaint_id)
join gft_lead_hdr on (glh_lead_Code=gch_lead_code) join gft_support_product_group on (gsp_group_id=glh_main_product)
join gft_status_master on (gtm_code=gch_current_status) join gft_emp_master on (gem_emp_id=las.gcd_process_emp)
left join gft_dev_complaints on (gdc_complaint_id=gch_complaint_id) where fir.gcd_nature='22' and gch_complaint_date<='$to_dt 23:59:59'   
and gch_complaint_date>='$one_yr_dt 00:00:00' $wh_con $lead_condition)  dtls join gft_emp_master on (emp=gem_emp_id) group by emp
having pending_support+pending_dev_support+pending_dev>0
END;
	$res = execute_my_query($qry);
	$return_array = /*.(mixed[string]).*/array();
	$return_array['headers'] = array('Agent','Pending Support and Pending SQA','Pending Dev Support','Pending Developer');
	$return_array['alignment'] = array('left','right','right','right');
	$counts = array();
	$domain_name = CURRENT_SERVER_URL."/telesupport.php?team_name_select=$param&chk_assigned=on$corporate_query_params&assure_care_company=$assure_care_company";
	while($row = mysqli_fetch_array($res)) {
		$agent_row = array();
		$agent_row[] = $row['emp_name'];
		$emp_id = $row['emp_id'];
		$agent_row[] = "<a href='$domain_name&emp_code=$emp_id&chk_assigned=on&support_status[]=T3&support_status[]=T15&from_dt=$one_yr_dt&to_dt=$to_dt&support_activity=22' target=_blank style='color:black;'>".$row['pending_support']."</a>";
		$agent_row[] = "<a href='$domain_name&emp_code=$emp_id&chk_assigned=on&support_status[]=T17&from_dt=$one_yr_dt&to_dt=$to_dt&support_activity=22' target=_blank style='color:black;'>".$row['pending_dev_support']."</a>";
		$agent_row[] = "<a href='$domain_name&emp_code=$emp_id&chk_assigned=on&support_status[]=T2&include_mantis_sync=on&from_dt=$one_yr_dt&to_dt=$to_dt&support_activity=22' target=_blank style='color:black;'>".$row['pending_dev']."</a>";
		$counts[] = $agent_row;
	}
	$return_array['values'] = $counts;
	return $return_array;
}
/**
 * @param string $param
 * @param string $emp_code
 * @param string $from_date
 * @param string $to_date
 * @param string $lead_condition
 * 
 * @return mixed[string]
 */
function get_product_issues_enhancements_summary($param='1',$emp_code='',$from_date='',$to_date='',$lead_condition='') {
    global $assure_care_company;
    $return_arr = /*.(mixed[string]).*/array();
    $gps = get_groups_for_param($param);
    $support_status = "'T2'";
    if($param=='4') {
        $support_status = "'T15','T3'";
    }
    $team_wh = '';
    if($assure_care_company=='1') {
        $team_wh = get_team_condition_for_param($param);
    }
    $wh_cond = " and gcd_status in ($support_status) ".assure_care_company_filter_condition();
    $qry =<<<END
select sum(if(GCD_CUST_CALL_TYPE in ('3','5') ,1,0)) product_issues, sum(if(GCD_CUST_CALL_TYPE in ('6'),1,0)) enhancements,
sum(if(GCD_CUST_CALL_TYPE in ('4') ,1,0)) comm,sum(if(GCD_CUST_CALL_TYPE in ('7') ,1,0)) doubts,
sum(if(GCD_CUST_CALL_TYPE in ('10') ,1,0)) feedback,sum(if(GCD_CUST_CALL_TYPE in ('8') ,1,0)) it,
sum(if(GCD_CUST_CALL_TYPE in ('9') ,1,0)) train,sum(if(GCD_CUST_CALL_TYPE is null or GCD_CUST_CALL_TYPE='' 
or GCD_CUST_CALL_TYPE='0',1,0)) none, prod_gp,prod_name from (select GCD_CUST_CALL_TYPE,gcd_status,
concat(gch_product_code,'-',gch_product_skew) prod_gp,concat(GPM_PRODUCT_ABR,' ',gpg_version) prod_name 
from gft_customer_support_hdr join (select gcd_complaint_id,GCD_CUST_CALL_TYPE,gcd_status from
gft_customer_support_dtl where gcd_activity_id in (select max(gcd_activity_id) from gft_customer_support_dtl 
join gft_emp_master on (gcd_employee_id=gem_emp_id) where gcd_activity_date>='$from_date 00:00:00' and 
gcd_activity_date<='$to_date 23:59:59' and gcd_nature!='24' $team_wh group by gcd_complaint_id) and 
gcd_activity_date>='$from_date 00:00:00' and gcd_activity_date<='$to_date 23:59:59' and 
gcd_nature!='24') dtl on (gch_complaint_id=gcd_complaint_id)
join gft_lead_hdr on (glh_lead_code=gch_lead_code) join gft_support_product_group on (gsp_group_id=glh_main_product)
join gft_product_group_master on (gpg_product_family_code=gch_product_code and gpg_skew=gch_product_skew)
join gft_product_family_master on (gpg_product_family_code=gpm_product_code) where GCH_OWNERSHIP!=1 $wh_cond $lead_condition) dtls 
group by prod_gp
END;
	$res = execute_my_query($qry);
	$return_arr['headers'] = array('Product','Product Issues','Enhancements','Commercial','Doubts/Clarifications','Feedback','IT Infrastructure','Training','None');
	$counts = array();
	while($row = mysqli_fetch_array($res)) {
		$this_row = array();
		$this_row[] = $row['prod_name'];
		$this_row[] = $row['product_issues'];
		$this_row[] = $row['enhancements'];
		$this_row[] = $row['comm'];
		$this_row[] = $row['doubts'];
		$this_row[] = $row['feedback'];
		$this_row[] = $row['it'];
		$this_row[] = $row['train'];
		$this_row[] = $row['none'];
		$counts[] = $this_row;
	}
	$return_arr['values'] = $counts;
	$return_arr['alignment'] = array('left','right','right','right','right','right','right','right','right');
	return $return_arr;
}
/**
 * @param string $from_dt
 * @param string $to_dt
 * @param string $type
 * @param string $lead_condition
 * @param string $corporate_query_params
 * @return mixed[]
 */
function get_hq_overall_pending_summary($from_dt='',$to_dt='',$type='1',$lead_condition='',$corporate_query_params='') {
	if($from_dt=='') {
		$from_dt = date('Y-m-d');
	}
	if($to_dt=='') {
		$to_dt = date('Y-m-d');
	}
	$one_year_date = add_date($from_dt,-365);
	$query1 = get_hq_support_pending_query($from_dt,$to_dt,$type,$lead_condition);
	$split_up = array('Resolution Time Expired','Resolution Time Not Expired','Total');
	if($type=='1') {
		$hdrs = array("0"=>'Product','Pending Support'=>$split_up,'Pending Dev Support'=>$split_up,'Pending Developer'=>$split_up,'1'=>'Pending Patch Update','2'=>'Pending Customer');
	} else {
		$hdrs = array("0"=>'Employee','Pending Support'=>$split_up,'Pending Dev Support'=>$split_up,'1'=>'Pending Customer');
	}
	$res = execute_my_query($query1);
	$values = array();
	$totals = array('Total');
	$tot1 = $tot2 = $tot3 = $tot4 = $tot5 = $tot6 = $tot7 = $tot8 = $tot9 = $tot10 = $tot11 = 0;
	$domain_name = CURRENT_SERVER_URL;
	$return_arr = array();
	$return_arr['styles'] = array();
	$link_font_color = "style='color:black;'";
	while($row = mysqli_fetch_array($res)) {
		$product_code = $row['pro_code']."-".$row['GCH_PRODUCT_SKEW'];
		$emp_id = $row['GCD_PROCESS_EMP'];
		$url_param = "&prod=$product_code";
		if($type=='2') {
			$url_param = "&emp_code=$emp_id&chk_assigned=on";
		}
		$common_url_params = '';
		if($type=='2' and $row['GEM_EMP_NAME']=='') {
		    $common_url_params = "&unassigned=1";
		}
		$curr_row = array();
		$curr_row[] = ($type=='1'?$row['product_name']."-".$row['version']:($row['GEM_EMP_NAME']!=''?$row['GEM_EMP_NAME']:'Not Assigned'));
		
		$curr_row[] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T15&support_status[]=T3$url_param&support=hq$corporate_query_params&skip_auto_refresh=1&res_time_status=1&page_limit=all$common_url_params' target='_blank'>".$row['pending_support_resolution']."</a>";
		$tot6 += (int)$row['pending_support_resolution'];
		$curr_row[] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T15&support_status[]=T3$url_param&support=hq$corporate_query_params&skip_auto_refresh=1&res_time_status=2&page_limit=all$common_url_params' target='_blank'>".$row['pending_support_safe']."</a>";
		$tot7 += (int)$row['pending_support_safe'];
		$curr_row[] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T15&support_status[]=T3$url_param&support=hq$corporate_query_params&skip_auto_refresh=1&page_limit=all$common_url_params' target='_blank'>".$row['tot_pending_suppoort']."</a>";
		$tot8 += (int)$row['tot_pending_suppoort'];
		
		$curr_row[] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T17$url_param&support=hq$corporate_query_params&skip_auto_refresh=1&res_time_status=1&page_limit=all$common_url_params' target='_blank'>".$row['pending_dev_support_resolution']."</a>";
		$tot9 += (int)$row['pending_dev_support_resolution'];
		$curr_row[] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T17$url_param&support=hq$corporate_query_params&skip_auto_refresh=1&res_time_status=2&page_limit=all$common_url_params' target='_blank'>".$row['pending_dev_support_safe']."</a>";
		$tot10 += (int)$row['pending_dev_support_safe'];
		$curr_row[] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T17$url_param&support=hq$corporate_query_params&skip_auto_refresh=1&page_limit=all$common_url_params' target='_blank'>".$row['tot_pending_dev_suppoort']."</a>";
		$tot11 += (int)$row['tot_pending_dev_suppoort'];
		if($type=='1') {
			$curr_row[] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T2$corporate_query_params&include_mantis_sync=on$url_param&support=hq&skip_auto_refresh=1&res_time_status=1&page_limit=all' target='_blank'>".$row['pending_dev_resolution']."</a>";
			$tot3 += (int)$row['pending_dev_resolution'];
			$curr_row[] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T2$corporate_query_params&include_mantis_sync=on$url_param&support=hq&skip_auto_refresh=1&res_time_status=2&page_limit=all' target='_blank'>".$row['pending_dev_safe']."</a>";
			$tot4 += (int)$row['pending_dev_safe'];
			$curr_row[] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T2$corporate_query_params&include_mantis_sync=on$url_param&support=hq&skip_auto_refresh=1&page_limit=all' target='_blank'>".$row['tot_pending_dev']."</a>";
			$tot5 += (int)$row['tot_pending_dev'];
			
			$curr_row[] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T36$url_param&support=hq$corporate_query_params&skip_auto_refresh=1&page_limit=all' target='_blank'>".$row['patch_update']."</a>";
			$tot1 += (int)$row['patch_update'];
			$return_arr['styles'][] = array("","background-color:lightcoral;","background-color: lightsteelblue;","background-color: darkkhaki;","background-color:lightcoral;","background-color: lightsteelblue;","background-color: darkkhaki;","background-color:lightcoral;","background-color: lightsteelblue;","background-color: darkkhaki;","","");
		} else {
			$return_arr['styles'][] = array("","background-color:lightcoral;","background-color: lightsteelblue;","background-color: darkkhaki;","background-color:lightcoral;","background-color: lightsteelblue;","background-color: darkkhaki;","","","","","");
		}
		$curr_row[] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T16$url_param&support=hq$corporate_query_params&skip_auto_refresh=1&page_limit=all$common_url_params' target='_blank'>".$row['pending_customer']."</a>";
		$tot2 += (int)$row['pending_customer'];
		$values[] = $curr_row;
	} 
	$totals[1] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T15&support=hq&skip_auto_refresh=1$corporate_query_params&res_time_status=1&page_limit=all' target='_blank'>".$tot6."</a>";
	$totals[2] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T15&support_status[]=T3&support=hq&skip_auto_refresh=1$corporate_query_params&res_time_status=2&page_limit=all' target='_blank'>".$tot7."</a>";
	$totals[3] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T15&support_status[]=T3&support=hq&skip_auto_refresh=1$corporate_query_params&page_limit=all' target='_blank'>".$tot8."</a>";
	$totals[4] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T17&support=hq&skip_auto_refresh=1$corporate_query_params&res_time_status=1&page_limit=all' target='_blank'>".$tot9."</a>";
	$totals[5] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T17&support=hq&skip_auto_refresh=1$corporate_query_params&res_time_status=2&page_limit=all' target='_blank'>".$tot10."</a>";
	$totals[6] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T17&support=hq&skip_auto_refresh=1$corporate_query_params&page_limit=all' target='_blank'>".$tot11."</a>";
	if($type=='1') {
		$totals[7] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T2&include_mantis_sync=on&support=hq$corporate_query_params&skip_auto_refresh=1&res_time_status=1&page_limit=all' target='_blank'>".$tot3."</a>";
		$totals[8] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T2&include_mantis_sync=on&support=hq$corporate_query_params&skip_auto_refresh=1&res_time_status=2&page_limit=all' target='_blank'>".$tot4."</a>";
		$totals[9] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T2&include_mantis_sync=on&support=hq$corporate_query_params&skip_auto_refresh=1&page_limit=all' target='_blank'>".$tot5."</a>";
		$totals[10] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T36&support=hq$corporate_query_params&skip_auto_refresh=1&page_limit=all' target='_blank'>".$tot1."</a>";
		$totals[11] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T16&support=hq$corporate_query_params&skip_auto_refresh=1&page_limit=all' target='_blank'>$tot2</a>";
	} else {
		$totals[7] = "<a $link_font_color href='$domain_name/telesupport.php?from_dt=$one_year_date&to_dt=$to_dt&support_status[]=T16&support=hq$corporate_query_params&skip_auto_refresh=1&page_limit=all' target='_blank'>$tot2</a>";
	}
	$return_arr['tot_style'] = array("background-color: darkkhaki;","background-color: darkkhaki;","background-color: darkkhaki;","background-color: darkkhaki;","background-color: darkkhaki;","background-color: darkkhaki;","background-color: darkkhaki;","background-color: darkkhaki;","background-color: darkkhaki;","background-color: darkkhaki;","background-color: darkkhaki;","background-color: darkkhaki;");
	$return_arr['team_totals'] = $totals;
	$return_arr['values'] = $values;
	$return_arr['alignment'] = array('left','right','right','right','right','right','right'.'right');
	if($type=='1') {
		$return_arr['alignment'] = array('left','right','right','right','right','right','right','right','right','right','right','right');
	} else {
		$return_arr['alignment'] = array('left','right','right','right','right','right','right','right');
	}
	$return_arr['headers'] = $hdrs;
	return $return_arr;
}
/**
 * @param string $param
 * @param string $emp_code
 * @param string $from_date
 * @param string $to_date
 * @param string $lead_condition
 * @param string $corporate_query_params
 * 
 * @return mixed[string]
 */
function get_overall_pending_summary($param='',$emp_code='',$from_date='',$to_date='',$lead_condition='',$corporate_query_params='') {
    global $assure_care_company;
// 	$wh_cond = " and gch_complaint_date>='".add_date(date('Y-m-d'),-365)." 00:00:00' ";
    $wh_cond = '';
    if($assure_care_company=='1') {
	   $wh_cond = get_team_condition_for_param($param);
    }
	$wh_cond .= assure_care_company_filter_condition();
	// Hardcoded 2012-01-01 in below query since no tickets are present before this date
	// TODO: inform teams to close old tickets and dynamically set date range as 1 year back
	$all_qry =<<<END
	select GCH_RESTORE_TIME,GCD_SEVERITY,gch_current_status,gtm_name from gft_customer_support_hdr join gft_customer_support_dtl on 
	(gcd_complaint_id=gch_complaint_id and gcd_activity_id=gch_last_activity_id) join gft_emp_master on (gem_emp_id=gcd_process_emp)
    join gft_status_master on (gch_current_status=gtm_code) join gft_lead_hdr on (glh_lead_Code=gch_lead_Code)
    join gft_support_product_group on (gsp_group_id=glh_main_product)
    where 1 $wh_cond $lead_condition and gch_current_status in ('T0','T3','T4','T17','T70','T84') and gch_complaint_date>='2012-01-01 00:00:00'
END;
	$qry =<<<END
	select count(*) tot,sum(if(date(GCH_RESTORE_TIME)>=date(now()),1,0)) assure_ok,sum(if(gcd_severity='1',1,0)) crash_count,
	sum(if(date(GCH_RESTORE_TIME)<date(now()),1,0)) assure_expire,sum(if(gcd_severity='2',1,0)) block_count,gtm_name,gch_current_status,
	sum(if(gcd_severity='3',1,0)) major_count,sum(if(gcd_severity='4',1,0)) minor_count,sum(if(gcd_severity='5',1,0)) feature_count,
	sum(if(GCD_SEVERITY='' or GCD_SEVERITY is null,1,0)) unknown_severity from ($all_qry) all_dtl 
	group by gch_current_status having tot>0 order by gch_current_status
END;
	$res = execute_my_query($qry);
	$return_array = /*.(mixed[string]).*/array();
	$return_array['headers'] = array('Status','Total','Age < Assure Time','Age > Assure Time','Crash','Block','Major','Minor','Feature','Unknown');
	$dtls = array();
	$url = CURRENT_SERVER_URL."/telesupport.php?assure_care_company=$assure_care_company&team_name_select=$param&chk_assigned=on$corporate_query_params&skip_date_filter=1&from_dt=&to_dt=";
	$totals = $assure1_tot = $assure2_tot = $crash_tot = $block_tot = $major_tot = $minor_tot = $feature_tot = $unknown_tot = 0;
	$styl = "style='color:black;'";
	while($row = mysqli_fetch_array($res)) {
		$rows = array();
		$stat = $row['gch_current_status'];
		$rows[] = $row['gtm_name'];
		$row_url = $url."&support_status[]=$stat";
		$rows[] = "<a href='$row_url' target=_blank $styl>".$row['tot']."</a>";
		$rows[] = "<a href='$row_url&res_time_status=2' target=_blank $styl>".$row['assure_ok']."</a>";
		$rows[] = "<a href='$row_url&res_time_status=1' target=_blank $styl>".$row['assure_expire']."</a>";
		$rows[] = "<a href='$row_url&severity=1' target=_blank $styl>".$row['crash_count']."</a>";
		$rows[] = "<a href='$row_url&severity=2' target=_blank $styl>".$row['block_count']."</a>";
		$rows[] = "<a href='$row_url&severity=3' target=_blank $styl>".$row['major_count']."</a>";
		$rows[] = "<a href='$row_url&severity=4' target=_blank $styl>".$row['minor_count']."</a>";
		$rows[] = "<a href='$row_url&severity=5' target=_blank $styl>".$row['feature_count']."</a>";
		$rows[] = "<a href='$row_url&severity=-1' target=_blank $styl>".$row['unknown_severity']."</a>";
		$dtls[] = $rows;
		$totals += (int)$row['tot'];
		$assure1_tot += (int)$row['assure_ok'];
		$assure2_tot += (int)$row['assure_expire'];
		$crash_tot += (int)$row['crash_count'];
		$block_tot += (int)$row['block_count'];
		$major_tot += (int)$row['major_count'];
		$minor_tot += (int)$row['minor_count'];
		$feature_tot += (int)$row['feature_count'];
		$unknown_tot += (int)$row['unknown_severity'];
	}
	$return_array['values'] = $dtls;
	$return_array['alignment'] = array('left','right','right','right','right','right','right','right','right','right');
	$total_url = "$url&support_status[]=T0&support_status[]=T3&support_status[]=T4&support_status[]=T17&support_status[]=T70&support_status[]=T84";
	$tot_link = "<a href='$total_url' target=_blank $styl>$totals</a>";
	$assure1_link = "<a href='$total_url&res_time_status=2' target=_blank $styl>$assure1_tot</a>";
	$assure2_link = "<a href='$total_url&res_time_status=1' target=_blank $styl>$assure2_tot</a>";
	$crash_link = "<a href='$total_url&severity=1' target=_blank $styl>$crash_tot</a>";
	$block_link = "<a href='$total_url&severity=2' target=_blank $styl>$block_tot</a>";
	$major_link = "<a href='$total_url&severity=3' target=_blank $styl>$major_tot</a>";
	$minor_link = "<a href='$total_url&severity=4' target=_blank $styl>$minor_tot</a>";
	$feature_link = "<a href='$total_url&severity=5' target=_blank $styl>$feature_tot</a>";
	$unknown_link = "<a href='$total_url&severity=-1' target=_blank $styl>$unknown_tot</a>";
	$return_array['team_totals'] = array('Total',"$tot_link","$assure1_link","$assure2_link","$crash_link","$block_link","$major_link","$minor_link","$feature_link","$unknown_link");
	return $return_array;
}
/**
 * @param string $param
 * @param string $from_date
 * @param string $to_date
 * @return mixed[string]
 */
function get_mail_summary_data($param,$from_date,$to_date) {
	$main_prod = get_main_prod_for_param($param);
	if($from_date=='') {
		$from_date = date('Y-m-d');
	}
	if($to_date=='') {
		$to_date = date('Y-m-d');
	}
	$prev_date = date('Y-m-d',strtotime("$from_date -1 day"));
	$one_year_date = add_date($from_date,-365);
	$qry =<<<END
select sum(if(GMH_RECEIVED_DATETIME<'$from_date 00:00:00' and GMH_CURRENT_STATUS='Open',1,0)) old_open,
sum(if(GMH_RECEIVED_DATETIME<'$from_date 00:00:00' and GMH_CURRENT_STATUS='Closed',1,0)) old_closed,
sum(if(GMH_RECEIVED_DATETIME>='$from_date 00:00:00' and GMH_RECEIVED_DATETIME<='$to_date 23:59:59' and 
GMH_CURRENT_STATUS='Open',1,0)) new_open,sum(if(GMH_RECEIVED_DATETIME>='$from_date 00:00:00' 
and GMH_RECEIVED_DATETIME<='$to_date 23:59:59' and GMH_CURRENT_STATUS='Closed',1,0)) new_closed,
sum(if(GMH_RECEIVED_DATETIME>='$from_date 00:00:00' and GMH_RECEIVED_DATETIME<='$to_date 23:59:59',1,0)) new_received,
sum(if(GMH_RECEIVED_DATETIME>='$one_year_date 00:00:00' and GMH_RECEIVED_DATETIME<='$to_date 23:59:59' and 
GMH_CURRENT_STATUS='Open',1,0)) all_open,sum(if(GMH_RECEIVED_DATETIME>='$one_year_date 00:00:00' 
and GMH_RECEIVED_DATETIME<='$to_date 23:59:59' and GMH_CURRENT_STATUS='Closed',1,0)) all_closed from
(select GMH_ID,GMH_MAIL_TICKET_ID,GMH_CURRENT_STATUS, GMH_RECEIVED_DATETIME,GMH_CLOSED_DATETIME,GMH_CREATED_DATETIME 
from gft_zoho_mail_hdr where GMH_RECEIVED_DATETIME>='$one_year_date 00:00:00' and GMH_SUPPORT_GROUP_ID='$main_prod' and GMH_IS_SPAM=0) dtls
END;
	$res = execute_my_query($qry);
	$total = 0; $new_open = 0; $new_close = 0; $overall_open = 0;
	$curr_received = 0; $curr_open = 0; $curr_closed = 0;
	$old_open = 0; $old_closed = 0;
	$all_open = 0; $all_closed = 0;
	if($row = mysqli_fetch_array($res)) {
		$curr_received = $row['new_received'];
		$curr_open = $row['new_open'];
		$curr_closed = $row['new_closed'];
		$old_open = $row['old_open'];
		$old_closed = $row['old_closed'];
		$all_open = $row['all_open'];
		$all_closed = $row['all_closed'];
	}
	$domain_name = CURRENT_SERVER_URL;
	$curr_label = ($from_date==$to_date?"On ".date('M d,Y',strtotime($from_date)):"Between ".date('M d,Y',strtotime($from_date))." and ".date('M d,Y',strtotime($to_date)));
	$return_array = /*.(mixed[string]).*/array();
	$return_array['headers'] = array('',"Till ".date('M d,Y',strtotime($from_date)),$curr_label,'Total');
	$return_array['alignment'] = array('left','right','right','right');
	$return_array['values'] = array();
	$return_array['values'][] = array('Mails Received',"--",
			"<a href='$domain_name/zmail_report.php?start_date=$from_date&end_date=$to_date&support_group=$main_prod' style='color:black;' target=_blank>$curr_received</a>","--");
	$return_array['values'][] = array('Pending',
			"<a href='$domain_name/zmail_report.php?start_date=$one_year_date&end_date=$prev_date&support_group=$main_prod&cmbstatus=Open' style='color:black;' target=_blank>$old_open</a>",
			"<a href='$domain_name/zmail_report.php?start_date=$from_date&end_date=$to_date&support_group=$main_prod&cmbstatus=Open' style='color:black;' target=_blank>$curr_open</a>",
			"<a href='$domain_name/zmail_report.php?start_date=$one_year_date&end_date=$to_date&support_group=$main_prod&cmbstatus=Open' style='color:black;' target=_blank>$all_open</a>");
// 	$return_array['values'][] = array('Closed Count',
// 			"<a href='$domain_name/zmail_report.php?start_date=$one_year_date&end_date=$prev_date&support_group=$main_prod&cmbstatus=Closed' style='color:black;' target=_blank>$old_closed</a>",
// 			"<a href='$domain_name/zmail_report.php?start_date=$from_date&end_date=$to_date&support_group=$main_prod&cmbstatus=Closed' style='color:black;' target=_blank>$curr_closed</a>",
// 			"<a href='$domain_name/zmail_report.php?start_date=$one_year_date&end_date=$to_date&support_group=$main_prod&cmbstatus=Closed' style='color:black;' target=_blank>$all_closed</a>");
	return $return_array;
}
/**
 * @param string $param
 * @param number $count
 * @return mixed[string]
 */
function get_desk_subject_data($param,$count=10) {
	$gp = get_main_prod_for_param($param);
	$count = (($count<0 or !is_numeric($count))?10:$count);
	$to_date = date('Y-m-d');
	$one_year_date = date('Y-m-d',strtotime("$to_date -1 year"));
	$qry =<<<END
select GMH_FROM_MAIL_ID,ifnull(glh_cust_name,'Unknown') cust_name,GMH_SUBJECT,GMH_RECEIVED_DATETIME,
ifnull(concat("#",GMH_TICKET_NO),GMH_MAIL_TICKET_ID) tkt_no from gft_zoho_mail_hdr
left join gft_lead_hdr on (glh_lead_code=gmh_lead_code) where GMH_SUPPORT_GROUP_ID in ($gp) 
and GMH_CURRENT_STATUS='Open' and GMH_RECEIVED_DATETIME<='$to_date 23:59:59' and GMH_IS_SPAM=0
order by GMH_RECEIVED_DATETIME limit $count
END;
	$res = execute_my_query($qry);
	$return_array = /*.(mixed[string]).*/array();
	$return_array['headers'] = array('Age','Customer','Ticket ID','Subject','E-mail ID','Received Time');
	$return_array['alignment'] = array('center','left','left','left','left','left');
	$vals = array();
	while($row = mysqli_fetch_array($res)) {
		$this_row = array();
		$this_row[] = get_age_in_days_hour_minutes((strtotime(date('Y-m-d'))-strtotime($row['GMH_RECEIVED_DATETIME']))/60,false,true);
		$this_row[] = $row['cust_name'];
		$this_row[] = $row['tkt_no'];
		$this_row[] = $row['GMH_SUBJECT'];
		$this_row[] = $row['GMH_FROM_MAIL_ID'];
		$this_row[] = $row['GMH_RECEIVED_DATETIME'];
		$vals[] = $this_row;
	}
	$return_array['values'] = $vals;
	$return_array['width'] = array('10%','20%','10%','40%',"10%","10%");
	$domain_name = CURRENT_SERVER_URL;
	$return_array['additional_content'] = "<a href='$domain_name/zmail_report.php?start_date=$one_year_date&end_date=$to_date&support_group=$gp&cmbstatus=Open' style='color:black;' target=_blank><strong>View All Pending Mails</strong></a>";
	return $return_array;
}
/**
 * @param string $param
 * @param string $from_date
 * @param string $to_date
 * @return mixed[]
 */
function get_chat_duration_summary_data($param,$from_date,$to_date) {
    $from_dt = date('Y-m-d');
    if($from_date!='') {
        $from_dt = $from_date;
    }
    $to_dt = date('Y-m-d');
    if($to_date!='') {
        $to_dt = $to_date;
    }
    $gps = get_groups_for_param($param,"web_group");
    $wh_condition = get_team_condition_for_param($param,'');
    $qry = " select sum(if(in_mts<10 or isnull(in_mts),1,0)) 10_min,sum(if(in_mts>=10 and in_mts<20,1,0)) 1020_mins, ".
           " sum(if(in_mts>=20 and in_mts<30,1,0)) 2030_mins,sum(if(in_mts>=30,1,0)) 30_mins,gem_emp_name,gem_emp_id ".
           " from chatbot.conversation_dtl cd left join chatbot.ml_chat_summary ml on (ml.chat_id=cd.id) ".
           " join gft_emp_master on (cd.agent_user_id=gem_emp_id) where cd.created_date>='$from_dt 00:00:00' and ".
           " cd.created_date<='$to_dt 23:59:59' $wh_condition group by cd.agent_user_id order by gem_emp_name ";
    $res = execute_my_query($qry);
    $vals = array();
    $domain_name = CURRENT_SERVER_URL;
    $total_10 = 0;
    $total_1020 = 0;
    $total_2030 = 0;
    $total_30 = 0;
    $url_common = "$domain_name/chatbot_conversations.php?from_dt=$from_dt&to_dt=$to_dt&team_name_select=$param";
    $attribs = "target=_blank style='color:black'";
    while($row = mysqli_fetch_assoc($res)) {
        $this_row = array();
        $emp_id = $row['gem_emp_id'];
        $this_row[] = $row['gem_emp_name'];
        $this_row[] = "<a href='$url_common&emp_code=$emp_id&chat_duration=1' $attribs>".$row['10_min']."</a>";
        $this_row[] = "<a href='$url_common&emp_code=$emp_id&chat_duration=2' $attribs>".$row['1020_mins']."</a>";
        $this_row[] = "<a href='$url_common&emp_code=$emp_id&chat_duration=3' $attribs>".$row['2030_mins']."</a>";
        $this_row[] = "<a href='$url_common&emp_code=$emp_id&chat_duration=4' $attribs>".$row['30_mins']."</a>";
        $total_10 += (int)$row['10_min'];
        $total_1020 += (int)$row['1020_mins'];
        $total_2030 += (int)$row['2030_mins'];
        $total_30 += (int)$row['30_mins'];
        $vals[] = $this_row;
    }
    $total_10_url = "<a href='$url_common&chat_duration=1' $attribs>$total_10</a>";
    $total_1020_url = "<a href='$url_common&chat_duration=2' $attribs>$total_1020</a>";
    $total_2030_url = "<a href='$url_common&chat_duration=3' $attribs>$total_2030</a>";
    $total_30_url = "<a href='$url_common&chat_duration=4' $attribs>$total_30</a>";
    $return_array = array();
    $return_array['values'] = $vals;
    $return_array['width'] = array('30%','14%','14%','14%','14%','14%');
    $return_array['alignment'] = array('left','right','right','right','right');
    $return_array['headers'] = array('Agent','<10 Mins','Between 10 and 20 Mins','Between 20 and 30 mins','>30 Mins');
    $return_array['team_totals'] = array('Total',$total_10_url,$total_1020_url,$total_2030_url,$total_30_url);
    return $return_array;
}
/**
 * @param string $param
 * @param string $from_date
 * @param string $to_date
 * @return mixed[]
 */
function get_team_learning_summary($param,$from_date,$to_date) {
    global $mydelight_tm_cust_id;
    $from_dt = date('Y-m-d');
    if($from_date!='') {
        $from_dt = db_date_format($from_date);
    }
    $to_dt = date('Y-m-d');
    if($to_date!='') {
        $to_dt = db_date_format($to_date);
    }
    $tot_asses = 0;$tot_learnt = 0;$tot_ip = 0;$tot_learn_pending = 0;$tot_reopen = 0;
    $vals = array();
    $wh_condition = get_team_condition_for_param($param,'','','','1');
    $emp_list_qry = execute_my_query(" select gem_emp_name,gem_emp_id,glm_login_name,glm_password from gft_emp_master ".
                                     " join gft_login_master on (glm_emp_id=gem_emp_id) ".
                                     " where gem_status='A' $wh_condition ");
    $learning_manager_error_res = "";
    while($row = mysqli_fetch_assoc($emp_list_qry)) {
        $dat = array();
        $emp_id = $row['gem_emp_id'];
        $today_summary = array();
        if($learning_manager_error_res==''){
            $today_summary 	= get_task_manager_activity_summary($mydelight_tm_cust_id,$from_dt,$to_dt,$emp_id,20);
        }        
        if(isset($today_summary['response']['error']) && $today_summary['response']['error']!=''){
            $learning_manager_error_res  = $today_summary['response']['error'];
        }
        $asses_done = isset($today_summary['pending_by_status']['assessment_done'])?$today_summary['pending_by_status']['assessment_done']:'0';
        $learnt = isset($today_summary['pending_by_status']['completed'])?$today_summary['pending_by_status']['completed']:'0';
        $in_p = isset($today_summary['pending_by_status']['in_progress'])?$today_summary['pending_by_status']['in_progress']:'0';
        $learn_pending = isset($today_summary['pending_by_status']['pending_learning'])?$today_summary['pending_by_status']['pending_learning']:'0';
        $reopen = isset($today_summary['pending_by_status']['reopened'])?$today_summary['pending_by_status']['reopened']:'0';
        if((int)$asses_done>0 or (int)$learnt>0 or (int)$in_p>0 or (int)$learn_pending>0 or (int)$reopen>0) {
            $dat[] = $row['gem_emp_name'];
            $dat[] = $asses_done;
            $dat[] = $learnt;
            $dat[] = $in_p;
            $dat[] = $learn_pending;
            $dat[] = $reopen;
            $vals[] = $dat;
            $tot_asses += (int)$asses_done;
            $tot_learnt += (int)$learnt;
            $tot_ip += (int)$in_p;
            $tot_learn_pending += (int)$learn_pending;
            $tot_reopen += (int)$reopen;
        }
    }
    $return_array = array();
    $return_array['values'] = $vals;
    $return_array['width'] = array('30%','14%','14%','14%','14%','14%');
    $return_array['alignment'] = array('left','right','right','right','right','right');
    $return_array['headers'] = array('Agent','Assessment Completed','Learning Completed','Learning In-Progress','Pending Learning','Re-opened');
    $return_array['team_totals'] = array('Total',$tot_asses,$tot_learnt,$tot_ip,$tot_learn_pending,$tot_reopen);
    $return_array['error_message'] = "$learning_manager_error_res";
    return $return_array;

}
/**
 * @param string $team
 * @param string $from_date
 * @param string $to_date
 * @param string $lead_condition
 * @param string $corporate_query_params
 * @return mixed[]
 */
function get_complaint_status_change_data($team,$from_date,$to_date,$lead_condition='',$corporate_query_params='') {
    global $assure_care_company;
    $from_dt = date('Y-m-d');
    if($from_date!='') {
        $from_dt = db_date_format($from_date);
    }
    $to_dt = date('Y-m-d');
    if($to_date!='') {
        $to_dt = db_date_format($to_date);
    }
    $lead_hdr_join = " join gft_lead_hdr on (glh_lead_code=gch_lead_code) join gft_support_product_group on (gsp_group_id=glh_main_product) ";
    $company_joins = " join gft_customer_support_hdr on (gch_complaint_id=gcd_complaint_id) $lead_hdr_join ";
    $return_arr = array();
    $gp_condition = '';
    if($assure_care_company=='1') {
        $gp_condition = get_team_condition_for_param($team,'');
    }
    $gp_condition .= assure_care_company_filter_condition();
    $dtl_qry = " select gem_emp_id,gem_emp_name,gsh_new_status,'' gsh_old_status,'' gcd_activity_id,new_status_group ".
               " from gft_emp_master left join (".
                        " select ifnull(gcd_employee_id,9999) gcd_employee_id,gsh_new_status,gsp_company_id, ".
                        " gtm_group_id new_status_group from gft_customer_support_dtl join gft_status_history on ".
                        " (gcd_activity_id=gsh_activity_id) join gft_status_master on (gsh_new_status=gtm_code) $company_joins where ".
                        " gcd_activity_date>='$from_dt 00:00:00' and gcd_activity_date<='$to_dt 23:59:59' and gsh_old_status='T17' $lead_condition) ".
                        " to_devs on (gcd_employee_id=gem_emp_id) where 1 $gp_condition ".
                        " union all ".
                        " select gem_emp_id,gem_emp_name,'' gsh_new_status,gsh_old_status,gcd_activity_id,'' new_status_group ".
                        " from gft_emp_master left join (".
                            " select ifnull(gcd_process_emp,9999) gcd_process_emp,gcd_activity_id,gsp_company_id, ".
                            " gsh_old_status from gft_customer_support_dtl join gft_status_history on (gcd_activity_id=gsh_activity_id) ".
                            " $company_joins where gcd_activity_date>='$from_dt 00:00:00' and gcd_activity_date<='$to_dt 23:59:59' ".
                            " and gsh_new_status='T17' $lead_condition) from_devs on (gcd_process_emp=gem_emp_id)".
                        " where 1 $gp_condition ".
                        " union all ".
                        " select gem_emp_id,gem_emp_name,'T17' as gsh_new_status,'' gsh_old_status,gcd_activity_id,'' new_status_group ".
                        " from gft_emp_master left join (select gcd_process_emp,gcd_activity_id from gft_customer_support_hdr ".
                        " join gft_customer_support_dtl on (gcd_activity_id=gch_first_activity_id and gch_complaint_id=gcd_complaint_id) ".
                        " $lead_hdr_join join gft_emp_master on (gcd_process_emp=gem_emp_id) where gcd_status='T17' and gch_complaint_date>='$from_dt 00:00:00' ".
                        " and gch_complaint_date<='$to_dt 23:59:59' $gp_condition $lead_condition) fir ".
               " on (gem_emp_id=gcd_process_emp) ";
    $summary_qry = " select count(distinct(case when gcd_activity_id!='' then gcd_activity_id end)) dev_support_assigned, ".
                   " sum(if(gsh_new_status='T2',1,0)) pending_dev,gem_emp_id,sum(if(new_status_group=2,1,0)) pending_cust, ".
                   " sum(if(new_status_group=3,1,0)) solved,gem_emp_name,sum(if(gsh_new_status in ('T3','T15'),1,0)) revert_support ".
                   " from ($dtl_qry) dtls group by gem_emp_id having dev_support_assigned+pending_dev+pending_cust+solved>0 ";
    $res = execute_my_query($summary_qry);
    $tot_dev_sup = 0; $tot_dev = 0; $tot_cust = 0; $tot_solved = 0; $tot_rev = 0;
    $return_arr['headers'] = array('Employee','Pending Dev Support Assigned','Moved to Pending Developer','Moved to Pending Customer','Reverted to Pending Support','Solved');
    $return_arr['alignment'] = array('left','right','right','right','right','right');
    $return_arr['width'] = array('30%','14%','14%','14%','14%','14%');
    $vals = array();
    $domain = CURRENT_SERVER_URL;
    $report_url = "$domain/support_activity_details.php?from_dt=$from_date&to_dt=$to_date&assure_care_company=$assure_care_company".$corporate_query_params;
    $link_style = "style='color:black'";
    $solved_statuse = get_status_master_from_group(array('3'));
    $solved_url = "";
    foreach ($solved_statuse as $sid) {
        $solved_url .= "&support_status[]=$sid";
    }
    $cust_statuses = get_status_master_from_group(array('2'));
    $cust_url = '';
    foreach ($cust_statuses as $sid) {
        $cust_url .= "&support_status[]=$sid";
    }
    while($row = mysqli_fetch_assoc($res)) {
        $row_arr = array();
        $dev_support = $row['dev_support_assigned'];
        $dev = $row['pending_dev'];
        $cust = $row['pending_cust'];
        $solv = $row['solved'];
        $rev = $row['revert_support'];
        $tot_cust += (int)$cust;
        $tot_dev += (int)$dev;
        $tot_dev_sup += (int)$dev_support;
        $tot_solved += (int)$solv;
        $tot_rev += (int)$rev;
        $emp_id = $row['gem_emp_id'];
        $row_arr[] = $row['gem_emp_name'];
        $row_url = $report_url."&emp_code=$emp_id";
        $row_arr[] = "<a href='$row_url&support_status[]=T17&chk_assigned=on' target='_blank' $link_style>".$dev_support."</a>";
        $row_arr[] = "<a href='$row_url&support_status[]=T2&prev_status=T17' target='_blank' $link_style>".$dev."</a>";
        $row_arr[] = "<a href='$row_url&prev_status=T17$cust_url' target='_blank' $link_style>".$cust."</a>";
        $row_arr[] = "<a href='$row_url&support_status[]=T3&support_status[]=T15&prev_status=T17' target='_blank' $link_style>".$rev."</a>";
        $row_arr[] = "<a href='$row_url&prev_status=T17$solved_url' target='_blank' $link_style>".$solv."</a>";
        $vals[] = $row_arr;
    }
    $report_url .= "&team_name_select=$team";
    $tot_dev_sup_link = "<a href='$report_url&support_status[]=T17&chk_assigned=on' target='_blank' $link_style>$tot_dev_sup</a>";
    $tot_dev_link = "<a href='$report_url&support_status[]=T2&prev_status=T17' target='_blank' $link_style>$tot_dev</a>";
    $tot_cust_link = "<a href='$report_url&prev_status=T17$cust_url' target='_blank' $link_style>$tot_cust</a>";
    $tot_solved_link = "<a href='$report_url&prev_status=T17$solved_url' target='_blank' $link_style>$tot_solved</a>";
    $tot_rev_link = "<a href='$report_url&support_status[]=T3&support_status[]=T15&prev_status=T17' target='_blank' $link_style>$tot_rev</a>";
    $return_arr['team_totals'] = array('Total',$tot_dev_sup_link,$tot_dev_link,$tot_cust_link,$tot_rev_link,$tot_solved_link);
    $return_arr['values'] = $vals;
    return $return_arr;
}
/**
 * @param string $team
 * @param string $from_date
 * @param string $to_date
 * @param string $lead_condition
 * @param string $corporate_query_params
 * 
 * @return mixed[]
 */
function get_ftr_data($team,$from_date,$to_date,$lead_condition='',$corporate_query_params='') {
    global $assure_care_company;
    $from_dt = date('Y-m-d');
    if($from_date!='') {
        $from_dt = db_date_format($from_date);
    }
    $to_dt = date('Y-m-d');
    if($to_date!='') {
        $to_dt = db_date_format($to_date);
    }
    $return_arr = array();
    $gp_condition = '';
    if($assure_care_company=='1') {
        $gp_condition = get_team_condition_for_param($team,'');
    }
    $company_wh = assure_care_company_filter_condition();
    $ftr_qry = " select gem_emp_id, gem_emp_name, count(distinct gcd_complaint_id) ftr from  ".
               
               " (select gcd_complaint_id, gcd_employee_id, gsp_company_id from gft_customer_support_dtl  ".
               " join gft_customer_support_hdr on ( gch_complaint_id=gcd_complaint_id )  ".
               " join gft_lead_hdr on (glh_lead_code=gch_lead_code) ".
               " join gft_support_product_group on (glh_main_product=gsp_group_id) ".
               " where gcd_activity_date>='$from_dt 00:00:00' and gcd_activity_date<='$to_dt 23:59:59'  ".
               " and gch_current_status in ( 'T1','T48' ) and gcd_status in ('T1','T48') $lead_condition and  ".
               " gcd_employee_id is not null and gcd_employee_id < 7000 and gcd_activity_id in  ".
               
               " (select min(dtl2.gcd_activity_id) from gft_customer_support_hdr  ".
               " join gft_customer_support_dtl dtl1 on ( dtl1.gcd_activity_date>='$from_dt 00:00:00' and  ".
               " dtl1.gcd_activity_date<='$to_dt 23:59:59' and gch_complaint_id=dtl1.gcd_complaint_id) ".
               " join gft_customer_support_dtl dtl2 on ( dtl2.gcd_complaint_id=dtl1.gcd_complaint_id) ".
               " where dtl2.gcd_employee_id is not null and dtl2.gcd_employee_id < 7000 and GCH_ESCALATION_NTIMES=0 ".
               " group by gch_complaint_id ) ) dtls  ".
               " join gft_emp_master on ( gem_emp_id=dtls.gcd_employee_id )  ".
               " join gft_role_group_master on (gem_role_id=grg_role_id) ".
               " where 1 $gp_condition $company_wh group by gem_emp_id ";
    $ftr_res = execute_my_query($ftr_qry);
    $all_act_qry = " select gem_emp_id,gem_emp_name,count(distinct(gcd_complaint_id)) act_cnt from gft_customer_support_dtl ".
                   " join gft_customer_support_hdr on (gch_complaint_id=gcd_complaint_id) ".
                   " join gft_emp_master on (gem_emp_id=gcd_employee_id) join gft_role_group_master on (gem_role_id=grg_role_id) ".
                   " join gft_lead_hdr on (glh_lead_code=gch_lead_code) join gft_support_product_group on (gsp_group_id=glh_main_product) ".
                   " where gcd_activity_date>='$from_dt 00:00:00' and gcd_activity_date<='$to_dt 23:59:59' ".
                   " and gch_current_status not in ('T41','T37','T14') $gp_condition $company_wh $lead_condition group by gem_emp_id ";
    $all_cnt_res = execute_my_query($all_act_qry);
    $cnts = array();
    while($all_row = mysqli_fetch_assoc($all_cnt_res)) {
        $cnts[(string)$all_row['gem_emp_id']] = array('emp_name'=>(string)$all_row['gem_emp_name'],'all_act_cnt'=>(string)$all_row['act_cnt']);
    }
    while($ftr_row = mysqli_fetch_assoc($ftr_res)) {
        if(!isset($cnts[(string)$ftr_row['gem_emp_id']])) {
            $cnts[(string)$ftr_row['gem_emp_id']] = array('emp_name'=>(string)$ftr_row['gem_emp_name']);
        }
        $cnts[(string)$ftr_row['gem_emp_id']]['ftr'] = $ftr_row['ftr'];
    }
    $return_arr['headers'] = array('Employee Name','Total Complaints','FTR Complaints','FTR %');
    $return_arr['width'] = array('40%','20%','20%','20%');
    $return_arr['alignment'] = array('left','right','right','center');
    $vals_arr = array();
    $domain = CURRENT_SERVER_URL;
    $report_url = "$domain/support_activity_details.php?from_dt=$from_date&to_dt=$to_date&assure_care_company=$assure_care_company".$corporate_query_params;
    $team_all = 0; $team_ftr = 0;
    foreach ($cnts as $eid=>$dtls) {
        $all_cnt = isset($dtls['all_act_cnt'])?(int)$dtls['all_act_cnt']:0;
        $ftr_cnt = isset($dtls['ftr'])?(int)$dtls['ftr']:0;
        $team_all += $all_cnt;
        $team_ftr += $ftr_cnt;
        $vals = array();
        $vals[] = $dtls['emp_name'];
        $vals[] = "<a target=_blank href='$report_url&emp_code=$eid' style='color: black;'>$all_cnt</a>";
        $vals[] = "$ftr_cnt";
        $vals[] = ($all_cnt==0?0:number_format((($ftr_cnt/$all_cnt)*100),2));
        $vals_arr[] = $vals;
    }
    $return_arr['values'] = $vals_arr;
    $return_arr['team_totals'] = array("Team","<a target=_blank href='$report_url&team_name_select=$team' style='color: black;'>$team_all</a>",
        "$team_ftr",($team_all==0?0:number_format((($team_ftr/$team_all)*100),2)));
    return $return_arr;
}
/**
 * @param string $param
 * @return string
 */
function get_support_group_manager_for_param($param) {
	$mgr_id = '';
	$gps = get_groups_for_param($param);
	$mgr_id_qry = execute_my_query(" select GSP_GROUP_MANAGER from gft_support_product_group where GSP_GROUP_ID in ($gps) ");
	if($row = mysqli_fetch_array($mgr_id_qry)) {
		$mgr_id = $row['GSP_GROUP_MANAGER'];
	}
	return $mgr_id;
}
/**
 * @param string $emp_id
 * @return string[int]
 */
function get_support_group_for_emp($emp_id) {
	$qry = <<<END
select gsp_group_id from gft_support_product_group join gft_voicenap_group on (GVG_SUPPORT_GROUP=gsp_group_id) 
join gft_voicenap_group_emp_dtl on (GVGED_GROUP_ID=GVG_GROUP_ID) where GVGED_EMPLOYEE='$emp_id'
END;
	$res = execute_my_query($qry);
	$g_id = /*.(string[int]).*/array();
	while($row = mysqli_fetch_array($res)) {
		$g_id[] = $row['gsp_group_id'];
	}
	return $g_id;
}
/**
 * @param string $title
 * @param mixed[string] $dtl_arr
 * @param string $table_width
 * @param string $table_align
 * @return string
 */
function show_data_in_table($title,$dtl_arr,$table_width='90',$table_align='left') {
	$headers = isset($dtl_arr['headers'])?/*.(string[int]).*/$dtl_arr['headers']:null;
	$col_count = 0;//count($headers);
	foreach ($headers as $h) {
		if(is_array($h)) {
			$col_count += count($h);
		} else {
			$col_count += 1;
		}
	}
	$table = '';
	if($col_count>0) {
		$alignment_arr = isset($dtl_arr['alignment'])?/*.(string[int]).*/$dtl_arr['alignment']:array_fill(0,$col_count,'left');
		$width = isset($dtl_arr['width'])?/*.(string[int]).*/$dtl_arr['width']:array_fill(0,$col_count,(string)(100/$col_count)."%");
		$values = isset($dtl_arr['values'])?/*.(string[int][int]).*/$dtl_arr['values']:array();
		$totals = isset($dtl_arr['team_totals'])?/*.(string[int]).*/$dtl_arr['team_totals']:array();
		$error_message = isset($dtl_arr['error_message'])?/*.(string).*/$dtl_arr['error_message']:"";
		$table = "<table style='border-collapse: collapse;' border=1 cellspacing=3 cellpadding=3 width='$table_width%' align='$table_align'>";
		if($title!='') {
			$table .= "<tr><td colspan='$col_count' style='background-color: bisque;'><h3 align='center'>$title</h3></td></tr>";
		}
		if($error_message!=""){
		    $table .= "<tr><td colspan='$col_count' style='background-color: red;'><h3 align='center'>$error_message</h3></td></tr>";
		}
		$table .= "<tr>";
		$cnt = 0;
		foreach($headers as $k=>$h) {
			$rowspan = 2;
			$colspan = 1;
			$hdr_txt = $h;
			if(is_array($h)) {
				$rowspan = 1;
				$colspan = count($h);
				$hdr_txt = $k;
			}
			$table .=<<<END
			<th width='$width[$cnt]' rowspan='$rowspan' colspan='$colspan' style='background-color:burlywood;'>$hdr_txt</th>
END;
			$cnt++;
		}
		$table .= "</tr>";
		$cnt = 0;
		$table .= "<tr>";
		foreach($headers as $h) {
			if(is_array($h)) {
				foreach($h as $h_text) {
					$table .= "<th width='$width[$cnt]' rowspan='1' style='background-color:burlywood;'>$h_text</th>";
					$cnt++;
				}
			} else {
				$cnt++;
			}
		}
		$table .= "</tr>";
		foreach($values as $i=>$vals) {
			$table .= "<tr>";
			foreach($vals as $k=>$v) {
				$style = '';
				if(isset($dtl_arr['styles']) and isset($dtl_arr['styles'][$i]) and isset($dtl_arr['styles'][$i][$k])) {
					$style = "style='".(string)$dtl_arr['styles'][$i][$k]."'";
				}
				$table .= "<td align='$alignment_arr[$k]' width='$width[$k]' $style>$v</td>";
			}
			$table .= "</tr>";
		}
		$table .= "<tr>";
		foreach ($totals as $k=>$t) {
			$style = '';
			if(isset($dtl_arr['tot_style']) and isset($dtl_arr['tot_style'][$k])) {
				$style = "style='".(string)$dtl_arr['tot_style'][$k]."'";
			}
			$table .= "<td align='$alignment_arr[$k]' width='$width[$k]' $style>$t</td>";
		}
		$table .= "</tr>";
		$table .= "</table>";
	}
	$additional_content = isset($dtl_arr['additional_content'])?$dtl_arr['additional_content']:'';
	$table .= "<br/><p style='text-align:center;'>$additional_content</p>";
	return $table;
}
/**
 * @param int $assure_care_company
 * @param string $corporate_lead
 * @return string[int]
 */
function get_mis_report_manual_rows($assure_care_company, $corporate_lead='') {
    $manual_entry_fields = array('Channel wise rating','Learning Plan signoff by team members', 
                                 'Need Help from other Team','Unhappy Customers','Engagements we missed collaboration');
    if(in_array($assure_care_company,array('2','3')) || $corporate_lead!='') {
        $manual_entry_fields = array('Other Comments');
    } else if($assure_care_company==0) { // to get Gofrugal,Patanjali and OYO MIS manual fields
        $manual_entry_fields[] = 'Other Comments';
    }
	return $manual_entry_fields;
}
/**
 * @param string $param
 * @param string $emp_code
 * @param string $from_date
 * @param string $to_date
 * @param boolean $allow_send_mail
 * @param string[int][int] $manual_data
 * @param int $mis_report_type
 * @param int $corporateCode
 * 
 * @return string[int]
 */
function get_support_mis_ui($param,$emp_code,$from_date,$to_date,$allow_send_mail=false,$manual_data=null,$mis_report_type=0,$corporateCode=0) {
	global $uid,$assure_care_company;
	if($from_date=='') {
		$from_date = date('Y-m-d');
	}
	if($to_date=='') {
		$to_date = date('Y-m-d');
	}
	$lead_condition = "";
	$complaint_lead_condition = "";
	$show_corporate_specific = false;
	$corporate_query_params = "&list_corp_cust=&custCode=";
	if($mis_report_type==2 && $corporateCode>0 && $param==4){
	    $corporate_ids = get_corporate_chain_customer_ids($corporateCode);
	    $corporate_id_list = implode(",", $corporate_ids);
	    $lead_condition = " AND GLH_LEAD_CODE in($corporate_id_list)";
	    $complaint_lead_condition = " AND GCH_LEAD_CODE in($corporate_id_list)";	    
	    $show_corporate_specific = true;	    
	    $corporate_query_params = "&list_corp_cust=3&custCode=$corporateCode";
	}
	$from_date = db_date_format($from_date);
	$to_date = db_date_format($to_date);
	$right_align_style = "style='text-align: right;'";
	$send_mail_form = $return_str = '';
	if(!in_array($assure_care_company,array('2','3')) && !$show_corporate_specific) {
	    $role_wh = get_team_condition_for_param($param);
	    $total_counts_qry = " select cd.id all_chat_id,ifnull(cf.chat_id,'') feedback_taken, ".
	                        " if(cd.chat_status in ('2','3'),cd.cust_id,'') cid,if(cd.chat_status='3',split_id,'') split_id ".
	                        " from chatbot.conversation_dtl cd ".
	                        " left join gft_emp_master on (gem_emp_id=cd.agent_user_id) ".
	                        " left join chatbot.customer_feedback cf on (cf.chat_id=cd.id and ".
	                        " date(cf.date_time)=date(cd.created_date) and cd.chat_status in ('2','3')) ".
	                        " join gft_lead_hdr on (glh_lead_code=cd.cust_id) ".
	                        " join gft_support_product_group on (gsp_group_id=glh_main_product) ".
	                        " left join chatbot.feedback_req_analytics fq ON (fq.chat_id=cd.id) ".
	                        " left join chatbot.split_chat_dtl scd on (cd.id=scd.chat_id and cd.chat_status=3) ".
	                        " where cd.created_date>='$from_date 00:00:00' and cd.created_date<='$to_date 23:59:59' $role_wh ";
        $total_counts_res = execute_my_query($total_counts_qry);
        $all_chats = array(); $all_answered_custs = array(); $all_feedback_requests = array(); $all_split_ids = array();
        while($c_row = mysqli_fetch_assoc($total_counts_res)) {
            $all_chats[] = $c_row['all_chat_id'];
            $all_feedback_requests[] = $c_row['feedback_taken'];
            $all_answered_custs[] = $c_row['cid'];
            $all_split_ids[] = $c_row['split_id'];
        }
        $chat_summary_data = get_chat_summary_report($param,$emp_code,$from_date,$to_date,$all_chats,$all_split_ids);
    	$return_str .= "<tr><th $right_align_style>Chat Summary</th><td>".show_data_in_table("",$chat_summary_data,'40')."</td></tr>";
    	$emp_vs_chats = get_chat_feedback_report($param,$emp_code,$from_date,$to_date,$all_answered_custs,$all_feedback_requests);
    	$return_str .= "<tr><th $right_align_style>Employee v/s Chats</th><td>".show_data_in_table("", $emp_vs_chats,'100')."</td></tr>";
    	$poor_feedbacks = get_feedback_reason_report($param,$emp_code,$from_date,$to_date);
    	$return_str .= "<tr><th $right_align_style>Poor Feedback Analysis</th><td>".show_data_in_table("", $poor_feedbacks)."</td></tr>";
	}
// 	$chat_duration_summary = get_chat_duration_summary_data($param,$from_date,$to_date);
// 	$return_str .= "<tr><th $right_align_style>Chat Duration Summary</th><td>".show_data_in_table("", $chat_duration_summary,'60')."</td></tr>";
	if(!in_array($param,array('6','7'))) {
	    $tickets_counts = get_support_ticket_summary($param,$from_date,$to_date,$lead_condition,$corporate_query_params);
		$return_str .= "<tr><th $right_align_style>myGoFrugal and Uber Tickets Counts</th><td>".show_data_in_table("", $tickets_counts,"50")."</td></tr>";
		$tickets_summary = get_support_ticket_counts($param,$from_date,$to_date,$lead_condition,$corporate_query_params);
		$return_str .= "<tr><th $right_align_style>myGoFrugal and Uber Tickets Summary</th><td>".show_data_in_table("", $tickets_summary)."</td></tr>";
		$overall_pending = /*.(mixed[string]).*/array();
		$overall_pending_userwise = /*.(mixed[string]).*/array();
// 		if((int)$assure_care_company!=2) {
//     		$mail_summary = get_mail_summary_data($param,$from_date,$to_date);
//     		$return_str .= "<tr><th $right_align_style>Support Mail Summary</th><td>".show_data_in_table("", $mail_summary,'40')."</td></tr>";
//     		$mail_subjects = get_desk_subject_data($param);
//     		$return_str .= "<tr><th $right_align_style>Open Support Mails</th><td>".show_data_in_table("", $mail_subjects)."</td></tr>";
// 		}
		if($param=='4') {
		    $overall_pending = get_hq_overall_pending_summary($from_date,$to_date,'1',$lead_condition,$corporate_query_params);
		    $overall_pending_userwise = get_hq_overall_pending_summary($from_date,$to_date,'2',$lead_condition,$corporate_query_params);
		} else {
		    $overall_pending = get_overall_pending_summary($param,$emp_code,$from_date,$to_date,$lead_condition,$corporate_query_params);
		}
		$overall = show_data_in_table("", $overall_pending);
		$return_str .= "<tr><th $right_align_style>Overall Pending Summary</th><td>".$overall."</td></tr>";
		if(count($overall_pending_userwise)>0) {
			$hq_user_wise = show_data_in_table("", $overall_pending_userwise);
			$return_str .= "<tr><th $right_align_style>Overall Pending Summary - Employee wise</th><td>".$hq_user_wise."</td></tr>";
		}
		$iot = get_iot_summary($param, $from_date, $to_date,$lead_condition,$corporate_query_params);
		$return_str .= "<tr><th $right_align_style>IoT Summary</th><td>".show_data_in_table("", $iot,'60')."</td></tr>";
		$iot_agents = get_iot_summary_ownerwise($param, $from_date, $to_date,$lead_condition,$corporate_query_params);
		$return_str .= "<tr><th $right_align_style>IoT Summary - Agent Wise</th><td>".show_data_in_table("", $iot_agents)."</td></tr>";
		$dev_support_activities = get_complaint_status_change_data($param, $from_date, $to_date,$lead_condition,$corporate_query_params);
		$return_str .= "<tr><th $right_align_style>Pending Dev Support Activities</th><td>".show_data_in_table("", $dev_support_activities)."</td></tr>";
		$ftr_data = get_ftr_data($param,$from_date,$to_date,$complaint_lead_condition,$corporate_query_params);
		$return_str .= "<tr><th $right_align_style>Emp wise FTR Complaints</th><td>".show_data_in_table("", $ftr_data,'60')."</td></tr>";
	}
	$call_summary = get_support_calls_count($param,$emp_code,$from_date,$to_date,$lead_condition,$corporate_query_params);
	$return_str .= "<tr><th $right_align_style>Call Summary</th><td>".show_data_in_table("", $call_summary,'40')."</td></tr>";
	$mail_summary = get_support_mail_summary($param,$emp_code,$from_date,$to_date,'',$complaint_lead_condition,$corporate_query_params);
	$mail_summary_all = get_support_mail_summary($param,$emp_code,"","","overall",$complaint_lead_condition,$corporate_query_params);
	$return_str .= "<tr><th $right_align_style>Support Mail Summary</th><td>".show_data_in_table("", $mail_summary,'40')."</td></tr>";
	$return_str .= "<tr><th $right_align_style>Overall Support Mail Summary - Employee wise</th><td>".show_data_in_table("", $mail_summary_all,'40')."</td></tr>";
	if(!in_array($param,array('6','7'))) {
	    $issues_enhancements = get_product_issues_enhancements_summary($param,$emp_code,$from_date,$to_date,$lead_condition);
		$return_str .= "<tr><th $right_align_style>Product Issues and Enhancements</th><td>".show_data_in_table("", $issues_enhancements,'70')."</td></tr>";
	}
	if(!$show_corporate_specific){
	    $learning_summary  = get_team_learning_summary($param,$from_date,$to_date);
	    $return_str .= "<tr><th $right_align_style>Team Learning Summary</th><td>".show_data_in_table("", $learning_summary,'50')."</td></tr>";
	}
	if(in_array($assure_care_company,array('2','3')) || ($show_corporate_specific)) {
	    $return_str .= "<tr><th $right_align_style>Support History</th><td><table border=1 width='100%'>".get_support_history_table(null,$from_date." 00:00:00",$to_date." 23:59:59",$assure_care_company,$lead_condition)."</table></td></tr>";
	}
	$ids = '';
	if(isset($manual_data) && count($manual_data)>0) {
		foreach ($manual_data as $k=>$arr) {
			$return_str .= "<tr><th $right_align_style>".$arr[0]."</th><td>".$arr[1]."</td></tr>";
		}
	}
	if($allow_send_mail) {
	    $topics = get_mis_report_manual_rows((int)$assure_care_company, $lead_condition);
		$keys = "'to_mail_ids','cc_mail_ids'";
		foreach ($topics as $k=>$t) {
			$cache_key = str_replace(" ","_",strtolower($t));
			$keys .= ",'$cache_key'";
		}
		$contents = array();
		$data_qry =<<<END
		select GCD_KEY,GCD_VALUE from gft_cache_daily_report where gcd_emp_id='$uid' and GCD_KEY in ($keys)
END;
		$data_res = execute_my_query($data_qry);
		$to_mail_ids = $cc_mail_ids = '';
		while($data_row = mysqli_fetch_array($data_res)) {
			$contents[$data_row['GCD_KEY']] = $data_row['GCD_VALUE'];
			if($data_row['GCD_KEY']=='to_mail_ids') {
				$to_mail_ids = $data_row['GCD_VALUE'];
			} else if($data_row['GCD_KEY']=='cc_mail_ids') {
				$cc_mail_ids = $data_row['GCD_VALUE'];
			}
		}
		$k = 0;
		foreach ($topics as $k=>$t) {
			$ids .= (($ids!=''?",":"")."textarea#ta$k");
			$cache_key = str_replace(" ","_",strtolower($t));
			$val = isset($contents["$cache_key"])?$contents["$cache_key"]:'';
			$return_str .= <<<END
		<tr><th $right_align_style>$t</th><td><textarea id='ta$k'>$val</textarea>
			<br/><input type='button' value='Save Data' onclick='javascript:save_report_content("$uid","$cache_key","ta$k")'>
				 <input type='button' value='Reset' onclick='javascript:reset_report_content("ta$k")'>
		</td></tr>
END;
		}
		$total_ta = $k;
		$send_mail_form .=<<<END
<table width='40%' border=1 style='border: 1px solid black; border-collapse: collapse;' align='center'>
<tr><th class='filter_label'>To Mail Ids</th><td><input type='text' name='to_mail_ids' id='to_mail_ids' size=50 value='$to_mail_ids'></td></tr>
<tr><th class='filter_label'>CC Mail Ids</th><td><input type='text' name='cc_mail_ids' id='cc_mail_ids' size=50 value='$cc_mail_ids'></td></tr>
<tr><td colspan=2 align='center'>
<INPUT type="button" value="Save Data" onclick='javascript:save_email_ids("$uid")'>&nbsp;&nbsp;
<INPUT class='button' id="send_mail_submit" name="submit1" type="button" value="Send Email" total_ta='$total_ta'>
</td></tr>
</table>
END;
	}
	return array($return_str,$ids,$send_mail_form);
}
/**
 * @param string $support_id
 * 
 * @return string[string]
 */
function get_ticket_dtl($support_id){
	$sql1 = " select cr.GEM_EMP_ID created_emp_id,cr.GEM_EMP_NAME created_emp_name,GCH_FIRST_ACTIVITY_ID from gft_customer_support_hdr ".
			" join gft_customer_support_dtl fst on (GCH_COMPLAINT_ID=fst.GCD_COMPLAINT_ID and fst.gcd_activity_id=GCH_FIRST_ACTIVITY_ID) ".
			" join gft_customer_support_dtl lst on (GCH_COMPLAINT_ID=lst.GCD_COMPLAINT_ID and lst.gcd_activity_id=GCH_LAST_ACTIVITY_ID) ".
			" join gft_emp_master cr on (cr.GEM_EMP_ID=fst.GCD_EMPLOYEE_ID) ".
			" where GCH_COMPLAINT_ID='$support_id' ";
	$res1 = execute_my_query($sql1);
	$data_arr = /*. (string[string]) .*/array();
	if($row1 = mysqli_fetch_array($res1)){
		$data_arr['created_emp_id'] 	= $row1['created_emp_id'];
		$data_arr['created_emp_name'] 	= $row1['created_emp_name'];
		$data_arr['first_act_id']		= $row1['GCH_FIRST_ACTIVITY_ID'];
	}
	return $data_arr;
}

/**
 * @param string[int][string] $dtl_arr
 *
 * @return void
 */
function update_support_ids_from_mantis($dtl_arr){
    $support_ids = array();
	foreach ($dtl_arr as $key => $val_arr){
		$complaint_id 	= $val_arr['complaint_id'];
		$resolved_date 	= $val_arr['resolved_date'];
		$resolved_by 	= isset($val_arr['resolved_by'])?$val_arr['resolved_by']:'';
		$add_note	 	= $val_arr['add_note'];
		$fixed_version 	= $val_arr['fixed_version'];
		$support_status	= $val_arr['status_to_update'];
		$resolved_by_emp = isset($val_arr['resolved_by_emp'])?$val_arr['resolved_by_emp']:'';
		
		$chk_que = " select GCH_COMPLAINT_ID,gch_lead_code,gch_product_code,gch_product_skew ".
		           " from gft_customer_support_hdr where GCH_COMPLAINT_ID='$complaint_id' and gch_current_status='T2' ";
		$chk_res = execute_my_query($chk_que);
		if(mysqli_num_rows($chk_res)==0){ //check to ensure currently complaint is in Pending Developer status
			continue;
		} else {
		    while($row = mysqli_fetch_assoc($chk_res)) {
		        $lead_code = $row['gch_lead_code'];
		        if(!isset($support_ids[$lead_code])) {
		            $support_ids[$lead_code] = array();
		        }
		        $pcode = $row['gch_product_code'];
		        $pskew = $row['gch_product_skew'];
		        if(!isset($support_ids[$lead_code][$pcode."-".$pskew])) {
		            $support_ids[$lead_code][$pcode."-".$pskew] = array();
		        }
		        if(!isset($support_ids[$lead_code][$pcode."-".$pskew][$fixed_version])) {
		            $support_ids[$lead_code][$pcode."-".$pskew][$fixed_version] = array();
		        }
		        $support_ids[$lead_code][$pcode."-".$pskew][$fixed_version][] = $row['GCH_COMPLAINT_ID'];
		    }
		}
		$emp_col_qry = "(select gem_emp_id from gft_emp_master where gem_email='$resolved_by')";
		if($resolved_by=='' and intval($resolved_by_emp)>0) {
		    $emp_col_qry = "'$resolved_by_emp'";
		}
		$update1="INSERT into gft_customer_support_dtl (GCD_COMPLAINT_ID, gcd_activity_id,GCD_ACTIVITY_DATE," .
				" GCD_EMPLOYEE_ID, GCD_REPORTED_DATE, GCD_NATURE, GCD_STATUS, GCD_CONTACT_TYPE, " .
				" GCD_CONTACT_PERSION, GCD_CUSTOMER_EMOTION, gcd_contact_no, GCD_CONTACT_MAILID, " .
				" GCD_PROCESS_EMP, GCD_TO_DO, GCD_SCHEDULE_DATE, GCD_ESTIMATED_TIME, " .
				" GCD_SEVERITY, GCD_PRIORITY, GCD_LEVEL, GCD_PROMISE_MADE, " .
				" GCD_PROMISE_DATE, GCD_FEEDBACK, GCD_PROBLEM_SUMMARY, GCD_PROBLEM_DESC, " .
				" GCD_UPLOAD_FILE, GCD_REMARKS, GCD_EXTRA_CHARGES,gcd_complaint_code,gcd_service_type,gcd_sub_status,gcd_effort_in_days,GCD_PRODUCT_MODULE )( ".
				" SELECT g.GCD_COMPLAINT_ID, 0,'$resolved_date',$emp_col_qry, ".
				" now(), '8', '$support_status', g.GCD_CONTACT_TYPE," .
				" g.GCD_CONTACT_PERSION, g.GCD_CUSTOMER_EMOTION, g.GCD_CONTACT_NO, g.GCD_CONTACT_MAILID," .
				" g.GCD_PROCESS_EMP, g.GCD_TO_DO, g.GCD_SCHEDULE_DATE, g.GCD_ESTIMATED_TIME," .
				" g.GCD_SEVERITY, g.GCD_PRIORITY, g.GCD_LEVEL, g.GCD_PROMISE_MADE," .
				" g.GCD_PROMISE_DATE, g.GCD_FEEDBACK, g.GCD_PROBLEM_SUMMARY,g.GCD_PROBLEM_DESC," .
				" '', '$add_note', g.GCD_EXTRA_CHARGES,gcd_complaint_code,gcd_service_type,gcd_sub_status,gcd_effort_in_days,g.GCD_PRODUCT_MODULE " .
				" FROM gft_customer_support_dtl g,gft_customer_support_hdr h where GCD_COMPLAINT_ID='$complaint_id' AND " .
				" h.GCH_COMPLAINT_ID = GCD_COMPLAINT_ID AND h.GCH_LAST_ACTIVITY_ID = GCD_ACTIVITY_ID ) ";
		$update2="UPDATE gft_customer_support_hdr SET GCH_CURRENT_STATUS='$support_status', " .
		" GCH_FIXED_IN_VERSION='$fixed_version',GCH_READY_TO_SUPPORT=now(), " .
		" GCH_LAST_ACTIVITY_ID=(SELECT MAX(GCD_ACTIVITY_ID) " .
		" FROM gft_customer_support_dtl where GCD_COMPLAINT_ID='$complaint_id')  " .
		" where GCH_COMPLAINT_ID='$complaint_id'";
		$update3="insert into gft_status_history (gsh_complaint_id,gsh_activity_id,gsh_last_activity_id,gsh_new_status,gsh_old_status)" .
				"(select a.gcd_complaint_id,a.gcd_activity_id, max(b.gcd_activity_id) t_last_act,a.gcd_status new_status, b.gcd_status old_status" .
				" from gft_customer_support_hdr h,gft_customer_support_dtl a, gft_customer_support_dtl b" .
				" where h.gch_complaint_id=a.gcd_complaint_id and h.gch_last_activity_id=a.gcd_activity_id" .
				" and  a.gcd_complaint_id=b.gcd_complaint_id and a.gcd_activity_id > b.gcd_activity_id" .
				" and h.gch_complaint_id='$complaint_id' group by a.gcd_complaint_id,b.gcd_activity_id order by b.gcd_activity_id desc limit 1); ";
		execute_my_query($update1);
		execute_my_query($update2);
		execute_my_query($update3);
		update_dev_table($complaint_id);
	}
	foreach ($support_ids as $cust_id=>$complaint_dtl) {
	    $cust_name = get_single_value_from_single_table("glh_cust_name", "gft_lead_hdr", "glh_lead_code", "$cust_id");
	    foreach ($complaint_dtl as $prod=>$support_ticket_dtl) {
	        $code_skew = explode("-",$prod);
	        $pcode = $code_skew[0];
	        $pgroup = $code_skew[1];
	        $type_of_prod = (int)get_single_value_from_single_table("GPM_IS_INTERNAL_PRODUCT", "gft_product_family_master", "GPM_PRODUCT_CODE", $pcode);
	        if($type_of_prod!=3){ // not required for saas product
	            foreach ($support_ticket_dtl as $fixed_in_version=>$tkt_ids) {
	                if(count($tkt_ids)>0) {
	                    $sids = implode(",",$tkt_ids);
	                    $db_content_config = /*.(string[string][int]).*/array();
	                    $db_content_config['Customer_Name'] = array($cust_name);
	                    $db_content_config['comp_id'] = array($sids);
	                    $db_content_config['version'] = array($fixed_in_version);
	                    $db_content_config['ProductName'] = array(get_product_name_with_version($pcode, $pgroup));
	                    send_formatted_mail_content($db_content_config, 8, 332, null, array((int)$cust_id));
	                }
	            }
	        }else if($type_of_prod==3){
	            foreach ($support_ticket_dtl as $fixed_in_version=>$tkt_ids) {
	                if(count($tkt_ids)>0) {
	                    $sids = implode(",",$tkt_ids);
	                    $db_content_config = /*.(string[string][int]).*/array();
	                    $db_content_config['Customer_Name'] = array($cust_name);
	                    $db_content_config['comp_id'] = array($sids);
	                    $db_content_config['version'] = array($fixed_in_version);
	                    $db_content_config['ProductName'] = array(get_product_name_with_version($pcode, $pgroup));
	                    send_formatted_mail_content($db_content_config, 8, 371, null, array((int)$cust_id));
	                }
	            }
	        }
	    }
	}
}

/**
 * @param string $support_id
 * @param string[string] $dtl_val_arr
 * @param string $gcd_status
 *
 * @return string
 */
function get_support_dtl_query_from_previous($support_id,$dtl_val_arr,$gcd_status=''){
	$cols_arr = array('GCD_COMPLAINT_ID','GCD_ACTIVITY_DATE','GCD_EMPLOYEE_ID','GCD_REPORTED_DATE','GCD_NATURE','gcd_status','GCD_CONTACT_TYPE','GCD_CONTACT_PERSION',
						'GCD_CUSTOMER_EMOTION','GCD_CUST_CALL_TYPE','gcd_contact_no','GCD_CONTACT_MAILID','GCD_PROCESS_EMP','GCD_TO_DO','GCD_SCHEDULE_DATE','GCD_ESTIMATED_TIME',
						'GCD_SEVERITY','GCD_PRIORITY','GCD_LEVEL','GCD_PROMISE_MADE','GCD_PROMISE_DATE','GCD_FEEDBACK','GCD_COMPLAINT_CODE','GCD_PRODUCT_CODE',
						'GCD_PROBLEM_SUMMARY','GCD_PROBLEM_DESC','GCD_UPLOAD_FILE','GCD_REMARKS','GCD_VISIT_REASON','GCD_VISIT_TIMEOUT','GCD_EXTRA_CHARGES','GCD_VISIT_NO',
						'GCD_RECEIVED_IN_HO','GCD_ESCALATION','GCD_ESCALATION_RESP','GCD_CUST_USERID','GCD_LAST_ACTIVITY_OF_DAY','GCD_VN_TRANSID','GCD_NO_RESPONSE_REASON',
	                   	'GCD_INTERNAL_EMOTION', 'GCD_SERVICE_TYPE', 'GCD_SUB_STATUS','GCD_EFFORT_IN_DAYS', 'GCD_PRODUCT_MODULE');
	$insert_str = "";
	$select_str = "";
	$put_comma = "";
	$dtl_val_arr['GCD_COMPLAINT_ID']	= $support_id;
	$dtl_val_arr['GCD_ACTIVITY_DATE'] 	= date('Y-m-d H:i:s');
	if($gcd_status!=""){
	    $dtl_val_arr['gcd_status'] 	= $gcd_status;
	}
	if(!isset($dtl_val_arr['GCD_EMPLOYEE_ID']))$dtl_val_arr['GCD_EMPLOYEE_ID']=SALES_DUMMY_ID;
	if(!isset($dtl_val_arr['GCD_ESCALATION']))$dtl_val_arr['GCD_ESCALATION'] = 'N';
		
	foreach ($cols_arr as $cname){
		$insert_str .= $put_comma . $cname;
		$select_str .= $put_comma. (isset($dtl_val_arr[$cname])?"'".mysqli_real_escape_string_wrapper($dtl_val_arr[$cname])."'":$cname);
		$put_comma = ",";
	}
	$sql1 = " insert into gft_customer_support_dtl ($insert_str) ".
				" (select $select_str from gft_customer_support_hdr ".
				" join gft_customer_support_dtl on (gcd_activity_id=GCH_LAST_ACTIVITY_ID) where GCH_COMPLAINT_ID='$support_id') ";
	return $sql1;
}
/**
 * @param string $lead_code
 *
 * @return int
 */
function get_assure_care_company_id($lead_code){
	$company_id = 	1;
	$query 		= 	"select GAC_ID as company_id from gft_lead_hdr ".
			" INNER JOIN gft_assure_care_company ON(GAC_REF_ID=GLH_REFERENCE_OF_PARTNER AND GLH_LEAD_SOURCE_CODE_PARTNER=37) ". //37 - corporate/company lead source
			" where GLH_LEAD_CODE=$lead_code ";
	$result = execute_my_query($query);
	if($row=mysqli_fetch_array($result)){
		$company_id = (int)$row['company_id'];
	}
	return $company_id;
}
/**
 * @param string $lead_code
 * @param string $product_code
 * @param string $product_group
 *
 * @return int
 */
function get_support_group_of_lead($lead_code, $product_code, $product_group){
	$support_group_id = 17;
	$company_id = get_assure_care_company_id($lead_code);
	$support_group_rows = execute_my_query("select GPC_SUPPORT_GROUP from gft_product_company_mapping where GPC_PRODUCT_ID='$product_code-$product_group' AND GPC_COMPANY_ID='$company_id'");
	if((mysqli_num_rows($support_group_rows)>0) && ($support_group_row=mysqli_fetch_array($support_group_rows))){
		$support_group_id = (int)$support_group_row['GPC_SUPPORT_GROUP'];
	}
	return $support_group_id;
}
/**
 * @param string $lead_code
 *
 * @return string
 */
function get_executive_for_mygofrugal_complaint($lead_code){
	$support_group 	= "";
	$assign_to		= '';
	$support_dtl_rows = execute_my_query("select GLH_MAIN_PRODUCT, GSP_MYGOFRUGAL_EMP from gft_lead_hdr".
			" INNER JOIN gft_support_product_group ON(GLH_MAIN_PRODUCT=GSP_GROUP_ID)".
			" INNER JOIN gft_emp_master em ON(GSP_MYGOFRUGAL_EMP=GEM_EMP_ID AND GEM_STATUS='A')".
			" where glh_lead_code='$lead_code'");
	if((mysqli_num_fields($support_dtl_rows)>0) && $support_dtl_row=mysqli_fetch_array($support_dtl_rows)){
		$assign_to 		= $support_dtl_row['GSP_MYGOFRUGAL_EMP'];
		$support_group	= $support_dtl_row['GLH_MAIN_PRODUCT'];
	}
	if($support_group=='17' || $support_group=='34'){// If support group id presales, get employee based on the LMT mapping
		$assign_to = get_lead_mgmt_incharge(0,0,0,0,0,$lead_code,false);
	}
	return $assign_to;
}
/**
 * @return string
 */
function get_unhappy_cust_ui() {
    return <<<END
<fieldset>
	<legend style='font-weight: bold; font-size: 14px; color: red; text-align: center;'>Unhappy Customer</legend>
	<table style='dth:90%;margin: auto;'>
    	<tr><td colspan=2 style='text-align: center;'>
    	   <input type='checkbox' name='is_unhappy' id='is_unhappy' class='form-input'>&nbsp;
           <label for='is_unhappy' class='datalabel'>Customer is Unhappy</label>
    	</td></tr>
    	<tr id='unhappy_reason_row'>
    		<td class='datalabel'><label for='unhappy_reason'><span style='color:red;'>*</span>&nbspReason</label></td>
    		<td><textarea name='unhappy_reason' id='unhappy_reason' rows="5" cols="40" maxlength="300" class='form-input'></textarea></td>
    	</tr>
	</table>
</fieldset>
END;
}
/**
 * @param string $emp_id
 * @param string $comments
 * @param string $activity_id
 * @param boolean $status_update
 * @return void
 */
function update_unhappy_customer($comments,$activity_id,$emp_id='',$status_update=false) {
    if($activity_id!='') {
        if($status_update and $emp_id!='') {
            $update_arr = array();
            $update_arr['GUD_STATUS_CHANGED_BY'] = $emp_id;
            $update_arr['GUD_STATUS_CHANGE_COMMENTS'] = $comments;
            $update_arr['GUD_STATUS_CHANGED_ON'] = date('Y-m-d H:i:s');
            array_update_tables_common($update_arr, "gft_unhappy_cust_dtl", array('GUD_ACTIVITY_ID'=>$activity_id), null, $emp_id);
            show_my_alert_msg("Status updated successfully");
        } else {
            $insert_arr = array();
            $insert_arr['GUD_ACTIVITY_ID'] = $activity_id;
            $insert_arr['GUD_COMMENTS'] = $comments;
            array_insert_query("gft_unhappy_cust_dtl", $insert_arr);
        }
    }
}
/**
 * @param string $from_dt
 * @param string $to_dt
 * @param string $emp_code
 * @param string $team_name_select
 * @param boolean $is_incoming
 * @param boolean $is_drill_down
 * @return string
 */
function get_l2_transfer_dtl_qry($from_dt,$to_dt,$emp_code,$team_name_select,$is_incoming,$is_drill_down) {
    $dtl_qry = ''; $wh = '';
    if($from_dt!='') {
        $wh .= " and cta.created_date>='".db_date_format($from_dt)." 00:00:00' ";
    }
    if($to_dt!='') {
        $wh .= " and cta.created_date<='".db_date_format($to_dt)." 23:59:59' ";
    }
    $em_alias = "em2";
    $select_cols = "'' trans_from_agent_id,trans_to_agent_id";
    if($is_drill_down) {
        $select_cols = "trans_from_agent_id,trans_to_agent_id,em1.gem_emp_name from_name,em2.gem_emp_name to_name";
    }else if($is_incoming) {
        $select_cols = "trans_from_agent_id,'' trans_to_agent_id";
     }
    if($is_incoming) {
        $em_alias = "em1";
    }
    if((int)$emp_code>0) {
        $wh .= " and $em_alias.gem_emp_id='$emp_code' ";
    } else {
        $wh .= get_team_condition_for_param($team_name_select,$em_alias);
    }
    $dtl_qry = " select $em_alias.gem_emp_id,$em_alias.gem_emp_name,cta.chat_id,cta.created_date,$select_cols,gew_desc, ".
               " $em_alias.web_group,cust_id,glh_cust_name,trans_reason from chatbot.chat_transfer_audit cta ".
               " join gft_emp_master em1 on (cta.trans_from_agent_id=em1.gem_emp_id) ".
               " join gft_emp_master em2 on (cta.trans_to_agent_id=em2.gem_emp_id) ".
               " join chatbot.conversation_dtl cd on (cd.id=cta.chat_id) ".
               " join gft_lead_hdr on (cd.cust_id=glh_lead_code) ".
               " join gft_emp_web_display on ($em_alias.web_group=gew_id) where action_type=1 and trans_reason COLLATE UTF8_GENERAL_CI LIKE 'L2 Transfer Required%' $wh ";
    return $dtl_qry;
}

/**
 * @param string $lead_code
 * @param string $complaint_id
 * 
 * @return string
 */
function get_pending_issues_query_for_web($lead_code,$complaint_id=null){
    $que1 = " SELECT GCH_COMPLAINT_ID,dtl.GCD_PROBLEM_SUMMARY,dtl.GCD_PROBLEM_DESC,GCH_CURRENT_STATUS,GCH_COMPLAINT_DATE, GAM_ACTIVITY_DESC, " .
            " gem_emp_name,gem_emp_id, GTM_NAME, GTM_GROUP_ID, GPM_PRODUCT_ABR, concat(GCH_PRODUCT_CODE,'-',GCH_PRODUCT_SKEW) product,GCH_COMPLAINT_CODE, " .
            " GIT_COUPON_REQUIRED,GTS_STATUS_NAME,GCH_PRODUCT_TYPE,GCH_VERSION,GIMC_OPCODE,dtl.GCD_REMARKS,GDQ_BATON_WOBBLING,dtl.GCD_INTERNAL_EMOTION, " .
            " dtl.gcd_process_emp assigned_emp FROM gft_customer_support_hdr " .
            " join gft_customer_support_dtl dtl on (gch_complaint_id=dtl.gcd_complaint_id and gch_last_activity_id=dtl.gcd_activity_id)" .
            " join gft_customer_support_dtl fir on (gch_complaint_id=fir.gcd_complaint_id and gch_first_activity_id=fir.gcd_activity_id)" .
            " join gft_product_family_master pfm on (pfm.gpm_product_code=GCH_PRODUCT_CODE) " .
            " join gft_status_master st on (gtm_code=GCH_CURRENT_STATUS and GTM_GROUP_ID not in (7)) " .
            " join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE) ".
            " left join gft_cust_imp_ms_current_status_dtl on (GIMC_COMPLAINT_ID=GCH_COMPLAINT_ID)" .
            " left join gft_impl_mailstone_master on (GIMC_MS_ID=GIM_MS_ID) " .
            " left join gft_impl_mailstone_type_master on (GIM_MS_TYPE=GIT_TYPE) " .
            " left join gft_ms_task_status on (GTS_STATUS_CODE=GIMC_STATUS)".
            " left join gft_activity_master on (GAM_ACTIVITY_ID=dtl.GCD_TO_DO) " .
            " left join gft_emp_master on (dtl.GCD_PROCESS_EMP=gem_emp_id)" .
            " left join gft_data_quality on (GDQ_REF_ID=dtl.gcd_activity_id and GDQ_REMINDER_TYPE=2) ".
            " WHERE GCH_LEAD_CODE='$lead_code' and ( (GTM_GROUP_ID!=3 and (GCH_CURRENT_STATUS!='T71' or fir.gcd_nature!='22')) ".
            (isset($complaint_id)? " or gch_complaint_id=$complaint_id ":"" ).
            " ) order by GCH_COMPLAINT_ID desc ";
    return $que1;
}
/**
 * @param string $complaint_id
 * @param int $note_id
 * 
 * @return string[int][string]
 */
function get_complaint_notes($complaint_id,$note_id=0){
    $note_list = /*.(string[int][string]).*/array();
    $wh = '';
    if($note_id>0){
        $wh = " and gcn_id='$note_id' ";
    }
    $query =" select GCN_ID,GCN_NOTE,GEM_EMP_NAME,GCN_UPDATED_ON,GCN_UPDATED_BY from gft_complaint_notes ". 
            " INNER JOIN gft_emp_master em ON(GCN_UPDATED_BY=em.GEM_EMP_ID) ".
            " where GCN_COMPLAINT_ID='$complaint_id' $wh order by 1 ";
    $result = execute_my_query($query);
    while($row=mysqli_fetch_assoc($result)){
        $notes = /*.(string[string]).*/array();
        $notes['id'] = $row['GCN_ID'];
        $notes['note'] = $row['GCN_NOTE'];
        $notes['updatedBy'] = $row['GEM_EMP_NAME'];
        $notes['timestamp'] = date('M d,Y h:i A',strtotime($row['GCN_UPDATED_ON']));
        $notes['employeeImageUrl'] = CURRENT_SERVER_URL."/".get_profile_pic_url($row['GCN_UPDATED_BY']);
        $note_list[] = $notes;
    }
    return $note_list;
}
/**
 * @param string $emp_id
 * @return string[int]
 */
function get_group_list_for_emp($emp_id) {
   $groups = /*.(string[int]).*/array();
   $emp_group_qry = " select gem_group_id from gft_emp_group_master where gem_emp_id='$emp_id' ";
   $role_group_qry = " select grg_group_id from gft_role_group_master join gft_emp_master on (gem_role_id=grg_role_id) ".
                     " where gem_emp_id='$emp_id' ";
   $group_qry = " select * from (($emp_group_qry) union ($role_group_qry)) t ";
   $group_res = execute_my_query($group_qry);
   while($row = mysqli_fetch_assoc($group_res)) {
       $groups[] = $row['gem_group_id'];
   }
   return $groups;
}
/**
 * @param int $team_support_id
 * @param string $from_mail_id
 * @param string $subject
 * @param string $content
 * @param int $send_by
 * 
 * @return int
 */
function insert_in_support_mail_hdr($team_support_id,$from_mail_id, $subject, $content, $send_by){
    $insertMessage = array(
        "GCS_SUPPORT_TEAM_ID"=>"$team_support_id",
        "GCS_MESSAGE_ID"=>"",
        "GCS_FROM_MAIL_ID"=>"$from_mail_id",
        "GCS_SUBJECT"=>"$subject",
        "GCS_CONTENT"=>"$content",
        "GCS_MAIL_START_FROM"=>"2",
        "GCS_OWNER_EMP"=>"$send_by",
        "GCS_MAIL_STATUS"=>"2"        
    );
    $messageRefId = array_insert_query("gft_customer_mail_hdr", $insertMessage);
    return $messageRefId;
}
/**
 * @param int $mail_hdr_id
 * @param string $mail_content
 * @param int $act_by
 * @param int $mailed_by
 * @param int $mail_stat
 * @param string $attachment_files
 * @param string $to_address
 * @param string $cc_address
 * @param string $from_address
 * @param int $send_type
 * @param int $support_id
 *
 * @return void
 */
function insert_and_send_customer_mail($mail_hdr_id,$mail_content,$act_by=9999,$mailed_by=2,$mail_stat=1,$attachment_files='',
    $to_address='',$cc_address='',$from_address='', $send_type=1, $message_id='',$support_id=0){
    $to_address = trim($to_address,',');
    $dtl_ins_arr = array(
        "GMD_HDR_ID"=>"$mail_hdr_id", 
        "GMD_CONTENT"=>$mail_content,
        "GMD_ACTIVITY_BY"=>"$act_by", 
        "GMD_MAILED_BY"=>"$mailed_by", 
        "GMD_MAIL_STATUS"=>"$mail_stat",
        "GMD_ATTACHEMENT_PATH"=>"$attachment_files", 
        "GMD_FROM_ADDRESS"=>"$from_address", 
        "GMD_TO_ADDRESS"=>"$to_address", 
        "GMD_CC_ADDRESS"=>"$cc_address",
        "GMD_MAIL_SEND_TYPE"=>"$send_type",
        "GMD_MESSAGE_ID"=>"$message_id",
        "GMD_ACTIVITY_SUPPORT_ID"=>"$support_id"
    );
    $insert_id = (int)array_insert_query("gft_customer_mail_dtl", $dtl_ins_arr);
    if($insert_id > 0 && trim($attachment_files)==''){// If any attachment in activity, send that mail through cron because of reducing delay in activity page submit.
        $zm_obj = new zohoMailAPI();
        $zm_obj->sendReplyToSupportMail($insert_id);
    }
}
/**
 * @param string $email_id
 * @param string $lead_code
 * 
 * @return string[string]
 */
function get_customer_using_email($email_id, $lead_code=''){
    $return_array = array();
    $condition = ($lead_code!=''?" AND GLH_LEAD_CODE='$lead_code'":" AND gcc_contact_no='$email_id' ");
    $result_find_lead = execute_my_query("select GLH_LEAD_CODE, GLH_MAIN_PRODUCT, gcl_lead_code,
                    	  GSM_PRODUCT_GROUP, GSM_SUPPORT_OWNER,GEM_EMP_NAME from gft_customer_contact_dtl
                    	  left join gft_lead_hdr lh on (glh_lead_code=gcc_lead_code)
                    	  left join gft_customer_product_info cc on (gcl_lead_code=glh_lead_code)
                          left join gft_support_mail_master ON(GLH_MAIN_PRODUCT=GSM_SUPPORT_GROUP)
                          left join gft_emp_master em ON(GSM_SUPPORT_OWNER=GEM_EMP_ID)
                    	  where glh_lead_type!=8 $condition group by GLH_LEAD_CODE ORDER BY gcl_lead_code DESC limit 1");
    if(mysqli_num_rows($result_find_lead) == 0){
        return;
    }
    $row_find_lead = mysqli_fetch_assoc($result_find_lead);
    $return_array['lead_code'] = $row_find_lead['GLH_LEAD_CODE'];
    $return_array['support_group'] = $row_find_lead['GLH_MAIN_PRODUCT'];
    if($row_find_lead['GSM_PRODUCT_GROUP']!=""){
        $return_array['product_group'] = explode("-", $row_find_lead['GSM_PRODUCT_GROUP']);;
    }
    if($row_find_lead['GSM_SUPPORT_OWNER']!=""){
        $return_array['owner'] = $row_find_lead['GSM_SUPPORT_OWNER'];
        $return_array['owner_name'] = $row_find_lead['GEM_EMP_NAME'];
    }
    return $return_array;
}
/**
 * @param string $channel_id
 * @param string $from_mail
 * @param string $subject
 * @param string $desc
 * @param string $mail_hdr_id
 * @param int $support_id
 * @param boolean $is_new_complaint
 * @param string $lead_code
 * @param int $sup_group
 * 
 * @return void
 */
function send_support_mail_to_support_chat_channel($channel_id, $from_mail, $subject, $desc, $mail_hdr_id, $support_id=0, $is_new_complaint=true,$lead_code='',$sup_group=0){
    if($channel_id==""){
        return;
    }
    $from_date = date("d-m-Y",strtotime("-30 days"));
    $date_now = date("d-m-Y");
    $customer_link = "https://sam.gofrugal.com/customer_mail_report.php?support_product_group=$sup_group";
    $customer_name = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", "$lead_code");
    $chat_message = "---------------------------------\n";
    $chat_message .= "*From:* $from_mail \n";
    $chat_message .= "*Subject:* $subject \n";
    $chat_message .= ((int)$lead_code>0?"*Customer Id:* [$lead_code]($customer_link&from_dt=$from_date&to_dt=&custCode=$lead_code) \n":"");
    $chat_message .= ($customer_name!=""?"*Customer Name:* $customer_name \n":"");
    $chat_message .= "*Mail Link:* [Click](https://sam.gofrugal.com/mail_view.php?mailRefId=$mail_hdr_id&support_id=$support_id)\n";
    $chat_message .= ($support_id>0?"*Support Id:* [$support_id](https://sam.gofrugal.com/tele_support_activity.php?fcomp_id=$support_id) ":"");
    $chat_message .= " [More]($customer_link&from_dt=$date_now&to_dt=$date_now)";
    
    
    do_curl_for_zoho_chat_push($channel_id,$chat_message);
    return '';
}
/**
 * @param string $channel_id
 * @param string $lead_code
 * @param string $complaint_id
 * @param string $summary
 * @param string $assign_to
 * 
 * @return string
 */
function send_support_ticket_notification_to_chat($channel_id,$lead_code,$complaint_id,$summary,$assign_to){
    $customer_name = get_single_value_from_single_table("GLH_CUST_NAME", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
    $complaint_owner = get_single_value_from_single_table("GEM_EMP_NAME", "gft_emp_master", "GEM_EMP_ID", $assign_to);
    $domain = CURRENT_SERVER_URL;
    $chat_message = "---------------------------------\n";
    $chat_message .= "*Complaint Id:* $complaint_id \n";
    $chat_message .= "*Customer Id:* $lead_code \n";
    $chat_message .= "*Customer Name:* $customer_name \n";
    $chat_message .= "*Summary:* $summary \n";
    $chat_message .= "*Assign To:* $complaint_owner \n";
    $chat_message .= "*Complaint Link:* [Click]($domain/complaint_details.php?id=$complaint_id)\n";
    do_curl_for_zoho_chat_push($channel_id,$chat_message);
    return '';
}
/**
 * @param int $mail_hdr_id
 * @param string $from_address
 * @param string $lead_code
 * @param string $thread_desc
 * @param string $thread_attachment
 * 
 * @return void
 */
function create_ticket_for_customer_mail($mail_hdr_id, $from_address, $lead_code='', $thread_desc='', $thread_attachment=''){
    global $secret;
    $datetime = date('Y-m-d H:i:s');
    $que1 = " select mh.GCS_FROM_MAIL_ID,mh.GCS_SUBJECT,mh.GCS_CONTENT,mh.GCS_ATTACHEMENT_PATH,mh.GCS_OWNER_EMP, ".
            " GMS_SUPPORT_ID,sm.GSM_PRODUCT_GROUP,mh.GCS_SUPPORT_TEAM_ID,em.GEM_EMP_NAME,sm.GSM_SUPPORT_GROUP, ".
            " GSM_ENABLE_SUPPORT_GROUP_CON, GSM_SUPPORT_MAIL_CHANNEL,GSM_TICKET_OWNER_TYPE,". 
            " GSM_IS_INTERNAL_SUPPORT from gft_customer_mail_hdr mh ".
            " join gft_support_mail_master sm ON(GSM_ID=mh.GCS_SUPPORT_TEAM_ID)".
            //" join gft_product_company_mapping cm on (cm.GPC_SUPPORT_GROUP=sm.GSM_SUPPORT_GROUP) ".
            " join gft_emp_master em on (GEM_EMP_ID=GCS_OWNER_EMP) ".
            " left join gft_mail_support on (GMS_MAIL_HDR_ID=mh.GCS_ID) ".
            " where mh.GCS_ID='$mail_hdr_id' group by GCS_ID ";
    $res1 = execute_my_query($que1);
    if($row1 = mysqli_fetch_array($res1)){
        $from_mail = $from_address;//$row1['GCS_FROM_MAIL_ID'];
        $mail_subj = $row1['GCS_SUBJECT'];
        $mail_body = strip_tags(str_replace(array("<br>","<br />","<br/>"), "\n", $row1['GCS_CONTENT']));
        $owner_emp = $row1['GCS_OWNER_EMP'];
        $owner_name= $row1['GEM_EMP_NAME'];
        $file_path = $row1['GCS_ATTACHEMENT_PATH'];
        $sup_group = (int)$row1['GSM_SUPPORT_GROUP'];
        $prod_arr = explode("-", $row1['GSM_PRODUCT_GROUP']);
        $chat_channel = $row1['GSM_SUPPORT_MAIL_CHANNEL'];
        $existing_support_id = '';
        $support_id = 0;
        $remarks = '';
        $new_complaint = true;
        $currect_status = "";;
        if((int)$row1['GMS_SUPPORT_ID'] > 0){
            $existing_support_id = $row1['GMS_SUPPORT_ID'];
            $remarks = strip_tags(str_replace(array("<br>","<br />","<br/>"), "\n", $thread_desc));
            $file_path = $thread_attachment;
            $currect_status = get_single_value_from_single_table("gch_current_status", "gft_customer_support_hdr", "GCH_COMPLAINT_ID", "$existing_support_id");
            $new_complaint = false;
            $current_status_group = get_single_value_from_single_query("GTM_GROUP_ID", "select GTM_GROUP_ID from gft_customer_support_hdr inner join gft_status_master ON(gch_current_status=GTM_CODE) where  GCH_COMPLAINT_ID='$existing_support_id'");
            if($current_status_group==3){//Solved
                return;
            }
        }
        if(count($prod_arr) <= 1){
            return;
        }
        if($row1['GSM_ENABLE_SUPPORT_GROUP_CON'] == 'Y'){
            $cust_dtl   = get_customer_using_email($from_mail, $lead_code);
            $lead_code  = isset($cust_dtl['lead_code'])?$cust_dtl['lead_code']:$lead_code;
            $sup_group  = isset($cust_dtl['support_group'])?$cust_dtl['support_group']:$sup_group;
            $prod_arr   = isset($cust_dtl['product_group'])?$cust_dtl['product_group']:$prod_arr;
            $owner_emp = isset($cust_dtl['owner'])?$cust_dtl['owner']:$owner_emp;
            $owner_name = isset($cust_dtl['owner_name'])?$cust_dtl['owner_name']:$owner_name;
        }
        $complaint_code = '164';
        $comp_status 	= ($currect_status!=""?"$currect_status":(($sup_group==6) ? 'T15' : 'T3')); //pending sqa or support
        if($lead_code==""){
            $que2 = " select GCC_LEAD_CODE from gft_customer_contact_dtl ".
                " left join gft_pos_users on (GPU_CONTACT_ID=GCC_ID) ".
                " left join gft_install_dtl_new on (GID_LEAD_CODE=GCC_LEAD_CODE and GID_LIC_PCODE='$prod_arr[0]' and GID_STATUS!='U') ".
                " left join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
                " where GCC_CONTACT_NO='$from_mail' and if(GPU_CONTACT_STATUS is null,1,GPU_CONTACT_STATUS='A') ".
                " group by GCC_LEAD_CODE order by ifnull(GID_LEAD_CODE,'z'),GPM_LICENSE_TYPE,GID_VALIDITY_DATE desc ";
            $res2 = execute_my_query($que2);
            if($row2=mysqli_fetch_assoc($res2)){
                $lead_code = $row2['GCC_LEAD_CODE'];
            }
        }  
        //For internal ticket take lead code from emp master. if not found null, don't create ticket.
        if($row1['GSM_IS_INTERNAL_SUPPORT']=='Y'){
            $query_string=" select GEM_LEAD_CODE from gft_emp_master where GEM_EMAIL='$from_mail' AND GEM_OFFICE_EMPID>0 AND GEM_STATUS='A'";
            $emp_lead_code = (int)get_single_value_from_single_query("GEM_LEAD_CODE", $query_string);
            $lead_code = $emp_lead_code>0?"$emp_lead_code":"";
        }
        //Asign ticket based on region
        if($row1['GSM_TICKET_OWNER_TYPE']==1){
            $result_lead_dtl = execute_my_query("select GLH_MAIN_PRODUCT,GPM_MAP_ID,GLH_CUST_STATECODE from gft_lead_hdr
left join gft_political_map_master on (gpm_map_name=glh_cust_statecode and gpm_map_type='S')
where GLH_LEAD_CODE='$lead_code'");
            if((mysqli_num_rows($result_lead_dtl)>0) && ($row_lead_dtl=mysqli_fetch_assoc($result_lead_dtl))){
                $product_gp = $row_lead_dtl['GLH_MAIN_PRODUCT'];
                $owner_emp = get_cst_agent_for_customer($row_lead_dtl['GPM_MAP_ID'], $product_gp);
                $owner_name = get_single_value_from_single_table("GEM_EMP_NAME", "gft_emp_master", "GEM_EMP_ID", "$owner_emp");
                $product_dtl = get_single_value_from_single_table("GVG_PRODUCT", "gft_voicenap_group", "GVG_SUPPORT_GROUP", "$product_gp"," AND GVG_STATUS='A' order by GVG_PREFER_ORDER asc");
                if($product_dtl!=""  && $product_dtl!="0"){
                    $prod_arr = explode("-",$product_dtl);
                }
                $comp_status = "T20";
            }
		}
		if((int)$existing_support_id > 0){
			$old_owner_emp = (int)get_single_value_from_single_table("GCD_PROCESS_EMP","gft_customer_support_dtl","GCD_COMPLAINT_ID","$existing_support_id","ORDER BY gcd_activity_id DESC");
			$owner_emp = $old_owner_emp>0 && $old_owner_emp !=9999 ? $old_owner_emp : $owner_emp;
		}
        if($lead_code!=""){            
            $support_id = insert_support_entry($lead_code, $prod_arr[0], $prod_arr[1], '', '', SALES_DUMMY_ID, '',$mail_subj,
                $complaint_code, $comp_status,$datetime,null,$owner_emp,'5','4',$mail_body,true,'',$remarks,$existing_support_id,3,$new_complaint,$file_path);
            if((int)$support_id == 0){
                return;
			}
			$mail_dtl_id = get_single_value_from_single_table("GMD_ID","gft_customer_mail_dtl","GMD_HDR_ID","$mail_hdr_id","ORDER BY GMD_ID DESC");
			execute_my_query("UPDATE gft_customer_mail_dtl SET GMD_SUPPORT_ACTIVITY_ID=(select GCH_LAST_ACTIVITY_ID from gft_customer_support_hdr where GCH_COMPLAINT_ID='$support_id') ". 
			"WHERE GMD_ID='$mail_dtl_id'");
            if($new_complaint){
                $ins_arr = array("GMS_MAIL_HDR_ID"=>$mail_hdr_id,"GMS_SUPPORT_ID"=>$support_id);
                array_insert_query("gft_mail_support", $ins_arr);
                execute_my_query("update gft_customer_mail_hdr set GCS_MAIL_STATUS=2 where GCS_ID='$mail_hdr_id'");
                $chat_domain = get_single_value_from_single_table("GGM_URL", "gft_gst_menu_card_master", "GGM_ID", "6");
                $chat_url = $chat_domain."?product_data=".urlencode(lic_encrypt("custid=$lead_code", $secret));
                $content_config = array(
                    "complaint_id"=>array($support_id), "Employee_Name"=>array($owner_name), "Live_Chat_Link"=>array($chat_url)
                );
                $content_arr = get_formatted_mail_content($content_config, 6, 365);
                $ack_content = $content_arr['formated_content'];
                insert_and_send_customer_mail($mail_hdr_id,$ack_content,9999,2,1,'','','','', 1, '',$support_id);
            }
        }
        send_support_mail_to_support_chat_channel($chat_channel, $from_mail, $mail_subj, $mail_body, $mail_hdr_id, $support_id, $chat_channel,$lead_code,$sup_group);
    }
}
/**
 * @param string $uid
 * 
 * @return string
 */
function get_employee_signature_content($uid){
    $emp_singature = file_get_contents('employee_singnature.html', true);
    $emp_master_dtl = get_emp_master($uid);
    $emp_singature = str_replace("{{Employee_Name}}", (isset($emp_master_dtl[0][1])?$emp_master_dtl[0][1]:''), $emp_singature);
    $emp_singature = str_replace("{{Employee_Email_Id}}", (isset($emp_master_dtl[0][4])?$emp_master_dtl[0][4]:''), $emp_singature);
    $emp_singature = str_replace("{{Employee_Designation}}", (isset($emp_master_dtl[0][7])?$emp_master_dtl[0][7]:''), $emp_singature);    
    return $emp_singature;
}
/**
 * @param string $support_id
 * @param string $mail_content
 * @param string $emp_id
 * @param string $attachment_files
 * @param string $to_emails
 * @param string $cc_mails
 * 
 * @return void
 */
function check_and_send_customer_mail($support_id,$mail_content,$emp_id,$attachment_files='',$to_emails='',$cc_mails=''){
    if(trim(str_replace("&nbsp;","",strip_tags($mail_content)))==""){
        return;
    }
    $que1 = " select GMS_MAIL_HDR_ID from gft_mail_support where GMS_SUPPORT_ID='$support_id' ";
    $res1 = execute_my_query($que1);
    if($row1 = mysqli_fetch_array($res1)){
        $hdr_id = $row1['GMS_MAIL_HDR_ID'];
        insert_and_send_customer_mail($hdr_id,$mail_content,$emp_id,2,1,$attachment_files,$to_emails,$cc_mails,'', 1, '',$support_id);
    }else{
        $result_support_mail = execute_my_query("select GSM_ID,GSM_SUPPORT_MAIL_ID,GCH_FIRST_ACTIVITY_ID from gft_customer_support_hdr ". 
                               " INNER JOIN gft_support_mail_master mm ON(concat(GCH_PRODUCT_CODE,'-',GCH_PRODUCT_SKEW)=GSM_PRODUCT_GROUP) ".
                               " where GCH_COMPLAINT_ID=$support_id");
        if($row_support_mail=mysqli_fetch_assoc($result_support_mail)){
            $team_support_id = $row_support_mail['GSM_ID'];
            $from_mail_id = $row_support_mail['GSM_SUPPORT_MAIL_ID'];
            $last_activity = $row_support_mail['GCH_FIRST_ACTIVITY_ID'];
            $subject = "GOFRUGAL Support Ticket: $support_id. ".get_single_value_from_single_table("GCD_PROBLEM_SUMMARY", "gft_customer_support_dtl", "gcd_activity_id", $last_activity);
            $hdr_id = insert_in_support_mail_hdr($team_support_id,$from_mail_id, $subject, $mail_content, $emp_id);
            insert_and_send_customer_mail($hdr_id,$mail_content,$emp_id,2,1,$attachment_files,$to_emails,$cc_mails,$from_mail_id,2,'',$support_id);
            $ins_arr = array("GMS_MAIL_HDR_ID"=>$hdr_id,"GMS_SUPPORT_ID"=>$support_id);
            array_insert_query("gft_mail_support", $ins_arr);
        }
    }
}

/**
 * @param string $ticket_id
 * 
 * @return string[string]
 */
function get_prev_details_of_support($ticket_id) {
    $prev_qry = " select gcd_sub_status,gcd_service_type,gcd_effort_in_days,gcd_product_module from gft_customer_support_dtl ".
                " join gft_customer_support_hdr on (gcd_complaint_id=gch_complaint_id and gcd_activity_id=gch_last_activity_id) ".
                " where gch_complaint_id='$ticket_id' ";
    $query_res = execute_my_query($prev_qry);
    $sub_status = ''; $serv_type = ''; $effort = ''; $prod_module = '';
    if($row = mysqli_fetch_assoc($query_res)) {
        $sub_status = $row['gcd_sub_status'];
        $serv_type = $row['gcd_service_type'];
        $effort = $row['gcd_effort_in_days'];
        $prod_module = $row['gcd_product_module'];
    }
    $result_arr = array('sub_status'=>$sub_status,'service_type'=>$serv_type,'effort'=>$effort,'product_module'=>$prod_module);
    return $result_arr;
}
/**
 * @param string $support_id
 * 
 * @return mixed[]
 */
function get_commercial_proforma_dtl($support_id){
    $return_array = array();
    $query =    " select GDC_PROFORMA_NUMBER, GPH_ORDER_AMT,GPH_ORDER_STATUS from gft_dev_complaints ".
                " INNER JOIN gft_proforma_hdr ON(GDC_PROFORMA_NUMBER=GPH_ORDER_NO) ".
                " where GDC_COMPLAINT_ID='$support_id'";
    $result = execute_my_query($query);
    if($row=mysqli_fetch_assoc($result)){
        $return_array['amount'] = $row['GPH_ORDER_AMT'];
        $return_array['proforma_no'] = $row['GDC_PROFORMA_NUMBER'];
        $return_array['status'] = $row['GPH_ORDER_STATUS'];
    }
    return $return_array;
    
}
/**
 * @param string[int] $lead_arr
 * @param int $mygofrugal_user_id
 *
 * @return mixed[string]
 */
function self_complaint_summary($lead_arr,$mygofrugal_user_id=0){
    $lead_str = implode(',', $lead_arr);
    if($lead_str==''){
        $lead_str = '0'; //throw error
    }
    $return_array = /*. (mixed[string]) .*/array();
    $total_query =  "count(GCH_COMPLAINT_ID) as total_tick, count(if(GTM_GROUP_ID='3',1,null)) as solved_tick ,". 
                    " count(if(GTM_GROUP_ID!='3',1,null)) as pending_tick";
    $que_supp = " select  $total_query".
        " from gft_customer_support_hdr ".
        " join gft_customer_support_dtl las on (las.gcd_activity_id=GCH_LAST_ACTIVITY_ID) ".
        " join gft_customer_support_dtl fir on (fir.gcd_activity_id=GCH_FIRST_ACTIVITY_ID) ".
        " join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE) ".
        " join gft_status_master on (GTM_CODE=gch_current_status) ".
        " join gft_status_group_master on (GTM_GROUP_ID=GMG_GROUP_ID) ".
        " where GCH_OWNERSHIP=1 AND GTM_GROUP_ID!=7 and GCH_LEAD_CODE in ($lead_str) ";
    if($mygofrugal_user_id!=0){
        $que_supp .= " and fir.GCD_CUST_USERID='$mygofrugal_user_id' ";
    }
    $res_supp = execute_my_query($que_supp);
    $summary_list = /*. (string[int][string]) .*/array();
    if($row1 = mysqli_fetch_array($res_supp)){
        if($row1['total_tick']>0){
            $item_arr['label']	=	"Total";
            $item_arr['value']	=	$row1['total_tick'];
            $item_arr['status_code'] = "self--1";
            $summary_list[]		=	$item_arr;
            $item_arr['label']	=	"Solved";
            $item_arr['value']	=	$row1['solved_tick'];
            $item_arr['status_code'] = "self--2";
            $summary_list[]		=	$item_arr;
            $item_arr['label']	=	"Pending";
            $item_arr['value']	=	$row1['pending_tick'];
            $item_arr['status_code'] = "self--3";
            $summary_list[]		=	$item_arr;
        }        
    }
    $return_array['type']	=	"list";
    $return_array['label']	=	"Internal Support Status";
    $return_array['items']	=	$summary_list;
    return $return_array;
}
/**
 * @param string[int] $lead_arr
 * @param int $mygofrugal_user_id
 * @param int $date_con_days
 *
 * @return mixed[string]
 */
function complaint_summary($lead_arr,$mygofrugal_user_id=0, $date_con_days=0){
    $lead_str = implode(',', $lead_arr);
    if($lead_str==''){
        $lead_str = '0'; //throw error
    }
    $return_array = /*. (mixed[string]) .*/array();
    $all_group_ids = "1,3,9,10";
    $total_query = "count(GCH_COMPLAINT_ID) as total_tick, count(if(GTM_GROUP_ID='3',1,null)) as solved_tick ,";    
    if($date_con_days>0){
        $last_dt	=	(date('Y-m-d H:i:s',strtotime("-$date_con_days days",strtotime(date('Y-m-d H:i:s')))));
        $total_query = "count(if(GCH_COMPLAINT_DATE>='$last_dt',1,null)) as total_tick, 
                        count(if(GCH_COMPLAINT_DATE>='$last_dt' AND GTM_GROUP_ID='3',1,null)) as solved_tick, ";
    }
    $que_supp = " select  $total_query".
        " count(if(GTM_GROUP_ID in (1,9),1,null)) as pend_support, count(if(GTM_GROUP_ID='10',1,null)) as pending_pcs ".
        " from gft_customer_support_hdr ".
        " join gft_customer_support_dtl las on (las.gcd_activity_id=GCH_LAST_ACTIVITY_ID) ".
        " join gft_customer_support_dtl fir on (fir.gcd_activity_id=GCH_FIRST_ACTIVITY_ID) ".
        " join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE and GFT_INTERNAL_COMPLAINT=0) ".
        " join gft_status_master on (GTM_CODE=gch_current_status) ".
        " join gft_status_group_master on (GTM_GROUP_ID=GMG_GROUP_ID) ".
        " where GCH_OWNERSHIP!=1 AND GCH_LEAD_CODE in ($lead_str) and GTM_GROUP_ID in ($all_group_ids)";
    if($mygofrugal_user_id!=0){
        $que_supp .= " and fir.GCD_CUST_USERID='$mygofrugal_user_id' ";
    }
    $res_supp = execute_my_query($que_supp);
    $summary_list = /*. (string[int][string]) .*/array();
    if($row1 = mysqli_fetch_array($res_supp)){
        $item_arr['label']	=	"Total".($date_con_days>0?" <br>Last $date_con_days days":"");
        $item_arr['value']	=	$row1['total_tick'];
        $item_arr['status_code'] = $all_group_ids;
        $summary_list[]		=	$item_arr;
        $item_arr['label']	=	"Solved".($date_con_days>0?"<br> Last $date_con_days days":"");
        $item_arr['value']	=	$row1['solved_tick'];
        $item_arr['status_code'] = "3";
        $summary_list[]		=	$item_arr;
        $item_arr['label']	=	"Pending Assure Care";
        $item_arr['value']	=	$row1['pend_support'];
        $item_arr['status_code'] = "1,9";
        $summary_list[]		=	$item_arr;
        if((int)$row1['pending_pcs']>0){
            $item_arr['label']	=	"Pending Solution Delivery";
            $item_arr['value']	=	$row1['pending_pcs'];
            $item_arr['status_code'] = "10";
            $summary_list[]		=	$item_arr;
        }
    }
    $return_array['type']	=	"list";
    $return_array['label']	=	"Support Status";
    $return_array['items']	=	$summary_list;
    return $return_array;
}

/**
 * @param string[int] $lead_arr
 * @param int $mygofrugal_user_id
 *
 * @return mixed[string]
 */
function pending_developer_summary($lead_arr,$mygofrugal_user_id=0){
    $lead_str = implode(',', $lead_arr);
    $summary_list = array();
    if($lead_str==''){
        $lead_str = '0'; //throw error
    }
    $return_array = /*. (mixed[string]) .*/array();
    $que_supp = " select GSM_CODE,GSM_NAME,count(GCH_COMPLAINT_ID) as cnt, if(GSM_CODE=5,GSM_CODE,1) severity_group, ".
        " if(GSM_CODE=5,GSM_NAME,'Issues')severity_group_name from gft_customer_support_hdr ".
        " join gft_customer_support_dtl las on (las.gcd_activity_id=GCH_LAST_ACTIVITY_ID) ".
        " join gft_customer_support_dtl fir on (fir.gcd_activity_id=GCH_FIRST_ACTIVITY_ID) ".
        " join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE and GFT_INTERNAL_COMPLAINT=0) ".
        " join gft_severity_master on (GSM_CODE=las.GCD_SEVERITY) ".
        " left join gft_sub_status_master ON(GSS_ID=las.GCD_SUB_STATUS)".
        " where GCH_OWNERSHIP!=1 AND GCH_LEAD_CODE in ($lead_str) and (GCH_CURRENT_STATUS='T2' AND (GSS_IS_APPLICABLE_TO IS NULL OR GSS_IS_APPLICABLE_TO IN(0,1))) ";
    if($mygofrugal_user_id!=0){
        $que_supp .= " and fir.GCD_CUST_USERID='$mygofrugal_user_id' ";
    }
    $que_supp .= " group by severity_group ";
    $res_supp = execute_my_query($que_supp);
    $summary_list = array(array('label'=>"Issue","value"=>"0",'status_code'=>"dev--1"),
        array('label'=>"Feature","value"=>"0",'status_code'=>"dev--5"));
    while($row1 = mysqli_fetch_array($res_supp)){
        if($row1['severity_group']==1){
            $summary_list[0]['value'] = $row1['cnt'];
        }else{
            $summary_list[1]['value'] = $row1['cnt'];
        }
    }
    $return_array['type']	=	"list";
    $return_array['label']	=	"Pending Developer";
    $return_array['items']	=	$summary_list;
    return $return_array;
}
/**
 * @param string[int] $lead_arr
 * @param int $mygofrugal_user_id
 *
 * @return mixed[string]
 */
function pending_developer_feature_summary($lead_arr,$mygofrugal_user_id=0){
    $lead_str = implode(',', $lead_arr);
    $summary_list = array();
    if($lead_str==''){
        $lead_str = '0'; //throw error
    }
    $return_array = /*. (mixed[string]) .*/array();
    $que_supp = " select if(GDS_ID IS NULL ,'0',GDS_ID) GDS_ID,if(GDS_NAME IS NULL,'Yet to classify',GDS_NAME) GDS_NAME ,count(GCH_COMPLAINT_ID) as cnt".
        " from gft_customer_support_hdr ".
        " join gft_customer_support_dtl las on (las.gcd_activity_id=GCH_LAST_ACTIVITY_ID) ".
        " join gft_customer_support_dtl fir on (fir.gcd_activity_id=GCH_FIRST_ACTIVITY_ID) ".
        " join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE and GFT_INTERNAL_COMPLAINT=0) ".
        " join gft_severity_master on (GSM_CODE=las.GCD_SEVERITY) ".
        " left join gft_dev_service_type_master ON (las.GCD_SERVICE_TYPE=GDS_ID)".
        " where GCH_OWNERSHIP!=1 AND GCH_LEAD_CODE in ($lead_str) AND GCH_CURRENT_STATUS='T2' AND las.GCD_SEVERITY=5 ";
    if($mygofrugal_user_id!=0){
        $que_supp .= " and fir.GCD_CUST_USERID='$mygofrugal_user_id' ";
    }
    $que_supp .= " group by GDS_ID ";
    $res_supp = execute_my_query($que_supp);
    while($row1 = mysqli_fetch_array($res_supp)){
        $summary_list[] = array(
            'label'=>$row1['GDS_NAME'],
            'value'=>$row1['cnt'],
            'status_code'=>"devfeature--".$row1['GDS_ID']
        );
    }
    $return_array['type']	=	"list";
    $return_array['label']	=	"Features";
    $return_array['items']	=	$summary_list;
    return $return_array;
}
/**
 * @param string[int] $lead_arr
 * @param int $mygofrugal_user_id
 *
 * @return mixed[string]
 */
function pending_customer_tickets_summary($lead_arr,$mygofrugal_user_id=0){
    $lead_str = implode(',', $lead_arr);
    if($lead_str==''){
        $lead_str = '0'; //throw error
    }
    $return_array = /*. (mixed[string]) .*/array();
    $que_supp = " select count(if(GTM_GROUP_ID=2,1,null)) as cust_tick, count(if(GTM_GROUP_ID=13,1,null)) as patch_tick, ".
        " count(if((GTM_GROUP_ID=5 && (las.GCD_SUB_STATUS=2 || las.GCD_SUB_STATUS=3 || las.GCD_SUB_STATUS=16)),1,null)) as commercial_response,".
        " count(if((GTM_GROUP_ID=5 && (las.GCD_SUB_STATUS=11)),1,null)) as uat_sign_off from gft_customer_support_hdr ".
        " join gft_customer_support_dtl las on (las.gcd_activity_id=GCH_LAST_ACTIVITY_ID) ".
        " join gft_customer_support_dtl fir on (fir.gcd_activity_id=GCH_FIRST_ACTIVITY_ID) ".
        " join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE and GFT_INTERNAL_COMPLAINT=0) ".
        " join gft_status_master on (GTM_CODE=gch_current_status) ".
        " join gft_status_group_master on (GTM_GROUP_ID=GMG_GROUP_ID) ".
        " left join gft_sub_status_master ON(GSS_ID=las.GCD_SUB_STATUS)".
        " where GCH_OWNERSHIP!=1 AND GCH_LEAD_CODE in ($lead_str) and (GTM_GROUP_ID in (2,13) OR (GTM_GROUP_ID=5 AND GSS_IS_APPLICABLE_TO IN(2))) ";
    if($mygofrugal_user_id!=0){
        $que_supp .= " and fir.GCD_CUST_USERID='$mygofrugal_user_id' ";
    }
    $res_supp = execute_my_query($que_supp);
    $summary_list = /*. (string[int][string]) .*/array();
    if($row1 = mysqli_fetch_array($res_supp)){
        if((int)$row1['commercial_response']>0){
            $summary_list[]		=	array('label'=>'Commercial Pending','value'=>$row1['commercial_response'],'status_code'=>'pend--5');
        }
        $summary_list[]		=	array('label'=>'Pending Status Update','value'=>$row1['cust_tick'],'status_code'=>'pend--2');
        $summary_list[]		=	array('label'=>'Pending Patch Update','value'=>$row1['patch_tick'],'status_code'=>'pend--13');
        if((int)$row1['uat_sign_off']>0){
            $summary_list[]		=	array('label'=>'UAT Sign off pending','value'=>$row1['uat_sign_off'],'status_code'=>'pend--6');
        }
    }
    $return_array['type']	=	"list";
    $return_array['label']	=	"My Pendings";
    $return_array['items']	=	$summary_list;
    return $return_array;
}

?>
