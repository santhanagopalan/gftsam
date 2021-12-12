<?php
/*. forward string[int][int] function get_product_list_family(string $launch_on=,string $status=,string $internal_pr_not_in=,string $except_pr=,boolean $show_only_head=,string $family_code=,boolean $only_base_product=); .*/

require_once(__DIR__ ."/dbcon.php");
require_once(__DIR__ ."/common_util.php");
require_once(__DIR__ ."/function.update_in_tables.php");

/**
 * @param string $product
 * 
 * @return string
 */
function get_reff_product($product){
	$result1=execute_my_query("SELECT concat(GPM_PRODUCT_CODE,GPM_REFERER_SKEW) reff_product FROM gft_product_master WHERE concat(GPM_PRODUCT_CODE,GPM_PRODUCT_SKEW)='$product' ");
	if($data1=mysqli_fetch_array($result1)){
		return $data1['reff_product'];
	}
	return '';
}

/**
 * @param string $product_code
 * @param string $product_skew
 * 
 * @return boolean
 */
function is_product_skew_avaliable($product_code,$product_skew){
	$result=execute_my_query("select * from gft_product_master where gpm_product_code=$product_code and gpm_product_skew='$product_skew' ");
	if($result!=false && mysqli_num_rows($result)==1){
		return true;
	}else{
		return false;
	}
}

/**
 * @param string $launch_on
 * @param string $status
 * @param string $internal_pr_not_in
 * @param string $except_pr
 * @param boolean $show_only_head
 * @param string $family_code
 * @param boolean $only_base_product
 * 
 * @return string[int][int]
 */
function get_product_list_family($launch_on=null,$status=null,$internal_pr_not_in=null,$except_pr=null,$show_only_head=false,$family_code=null,$only_base_product=false){
	$query="select GPM_PRODUCT_CODE, GPM_PRODUCT_NAME, GPM_PRODUCT_ABR,GPM_SUPPORT_MAILID " .
			"from gft_product_family_master where (1) " ;
	if($status=='all'){ $query.="";}else if($status!=null){ $query.=" and GPM_STATUS='$status' ";} 
	else { $query.=" and GPM_STATUS='A' ";}
	if($internal_pr_not_in!=0){$query.=" AND GPM_IS_INTERNAL_PRODUCT!='$internal_pr_not_in' ";}
	if($except_pr!=0){$query.=" and gpm_product_code !='$except_pr'";}
	if($family_code!=null){ $query.=" and gpm_product_code='$family_code' "; }
	if($launch_on!=null){	$query.=" and gpm_launched_on<'$launch_on'  ";	}
	if($show_only_head==true) {$query.=" and gpm_product_code=gpm_head_family ";}
	if($only_base_product) { $query.=" and GPM_IS_BASE_PRODUCT='Y' "; }
	$query.=" order by 2";
	return get_two_dimensinal_result_set_from_query($query);
}

/**
 * @param string $pcode
 * 
 * @return string
 */
function get_product_name($pcode){
	$query="select GPM_PRODUCT_NAME,GPM_PRODUCT_ABR from gft_product_family_master where GPM_PRODUCT_CODE=$pcode ";
	$result=execute_my_query($query,'product_util.php',true,false,2);
	if($qd=mysqli_fetch_array($result)){
		return $qd['GPM_PRODUCT_NAME'];
	}else{ 
		return null;
	}
}

/**
 * @param string $product_code
 * 
 * @return string[string][int]
 */
function get_product_from_code($product_code=null){
	$query="select GPM_PRODUCT_CODE,GPM_PRODUCT_NAME,GPM_PRODUCT_ABR,GPM_SUPPORT_MAILID from gft_product_family_master " .
			($product_code!=null?" where GPM_PRODUCT_CODE=$product_code ":'');
	$query.=" order by 1";	
	$result=execute_my_query($query,'product_util.php',true,false,2);
	$i=0;
	$prod_list = /*. (string[string][int]) .*/ array();
	while($qd=mysqli_fetch_array($result)){
		$code=$qd['GPM_PRODUCT_CODE'];
		$prod_list[$code][0]=$qd['GPM_PRODUCT_NAME'];
		$prod_list[$code][1]=$qd['GPM_PRODUCT_ABR'];
		$prod_list[$code][2]=$qd['GPM_SUPPORT_MAILID'];
		$i++;
	}
	return $prod_list;
}

/**
 * @param string $launch_on
 * 
 * @return string[int][int] 
 */
function get_product_list($launch_on=null){
	global $status,$internal_pr_not_in;
	$prod_list=get_product_list_family($launch_on,$status,$internal_pr_not_in,$except_pr='1');
	return $prod_list;
}	

/**
 * @param string $pcode
 * @param string $skew
 * 
 * @return string[int][int]
 */
function get_version_list($pcode,$skew){
	$query=" select concat(gpv_major_version,'.',gpv_minor_version,'.',gpv_patch_version,'.', gpv_exe_version) version " .
			" from gft_product_version_master  where gpv_product_code=$pcode and gpv_version_family='$skew' "; 	
	$result=execute_my_query($query);
	$i=0;
	$prod_ver=/*. (string[int][int]) .*/ array();
	while($qdata=mysqli_fetch_array($result)){
		$prod_ver[$i][0]=$qdata['version'];
		$prod_ver[$i][1]=$qdata['version'];
		$i++;
	}
	return $prod_ver;
}

/**
 * @param string $vertical
 * @param string $product_code
 * @param string $product_skew
 * 
 * @return string
 */
function get_sample_db_link($vertical,$product_code,$product_skew){
	$query="SELECT  gbr_vertical,gbr_product,GBR_SAMPLE_DB_LINK FROM gft_bvp_relation 
			where gbr_product=concat('$product_code','-',substring('$product_skew',1,4)) and gbr_vertical=$vertical ";
	$result=execute_my_query($query);
	if($result){
		if(mysqli_num_rows($result)>0){
			$qd=mysqli_fetch_array($result);
			$GBR_SAMPLE_DB_LINK=$qd['GBR_SAMPLE_DB_LINK'];
			return 	$GBR_SAMPLE_DB_LINK;
		}
	}

	return null;
}

/**
 * @param string $pcode
 * @param string $skew
 * @param boolean $select
 * @param string $latest
 * @param string $only_version
 * @param int $reltype
 * 
 * @return string[int][int]
 */
function get_version($pcode,$skew=null,$select=true,$latest=null,$only_version=null,$reltype=1){
	/* PABR is passing as param to website download thankyou page in existing website */
	if($skew==null){
		
	}
	if($skew==null){
		$query="SELECT v.gpv_product_code," .
					"concat(gpv_major_version,'.',gpv_minor_version,'.',gpv_patch_version,'.', gpv_exe_version) version," .
					" Date_format(v.gpv_release_date,'%d-%b-%Y') 'release_date',v.gpv_feature 'feature'," .
					" GPV_DOWNLOAD_HLINK 'download_link' " .
					" FROM gft_product_family_master g ,gft_product_version_master v" .
					" WHERE g.GPM_PRODUCT_CODE='$pcode' AND g.GPM_HEAD_FAMILY=GPV_PRODUCT_CODE and GPV_PRODUCT_STATUS='A' " ;
	}else{
    	$skew_sep=substr($skew,0,4);
    	$query=" select  gpv_product_code, " .
				" concat(gpv_major_version,'.',gpv_minor_version,'.',gpv_patch_version,'.', gpv_exe_version) version ," .
				" Date_format(gpv_release_date,'%d-%b-%Y') 'release_date',gpv_feature 'feature'," .
				" GPV_DOWNLOAD_HLINK 'download_link',GPG_README_LINK,GPG_HELP_LINK,GPG_STEP_BY_STEP_LINK,gpg_support_mail_id," .
    			" GPG_SYS_REQUIREMENT,GPG_INSTALLATION_GUIDE_URL,GPG_LIVE_CHAT_URL,GPV_RELEASE_NOTE_HLINK," .
    			" GPV_DOWNLOAD_SIZE,GPV_DOWNLOAD_SIZE_UNIT,GPG_CBT_LINK,GPG_SERVICE_PACK_LINK,GPG_SAMPLE_DB_LINK," .
    			" GPV_DOWNLOAD_HLINKPH,GPV_DOWNLOAD_PH_SIZE,GPV_DOWNLOAD_PH_SIZE_UNIT," .
    			" concat(GPM_PRODUCT_ABR,gpg_version) as PABR,GPG_CRASH_RESTORE_LINK  " .
				" from gft_product_version_master, gft_product_family_master ,gft_product_group_master" .
				" where GPV_PRODUCT_STATUS='A' and gpg_product_family_code=GPM_HEAD_FAMILY AND " .
				" gpv_version_family =gpg_skew  and gpv_product_code=GPM_HEAD_FAMILY  " .
				" and gpm_product_code='$pcode' and '$skew_sep' like concat(gpg_skew,'%') ";
		if($latest=='y'){ $query.=" and GPV_PUBLISH_IN_WEB='Y' ";}		
    }
    if($latest=='y'){$query.=" and GPV_DOWNLOAD_HLINK!='' ";}
   // $query.=" and GPV_RELEASE_TYPE='$reltype' ";
   $query.=" and GPV_RELEASE_TYPE='1' ";
	$query.=" order by gpv_release_date desc ";
	if($latest=='y'){$query.=" limit 1 ";}
	$result=execute_my_query($query,'product_util.php',true,false,2);
	$prod_ver=/*. (string[int][int]) .*/ array();
	$i=0;
	if($select==true){
		$prod_ver[$i][0]="0";
		$prod_ver[$i][1]="-Select-";
		$i++;
	}
	while($qdata=mysqli_fetch_array($result)){
		if($only_version!=null){
			$prod_ver[$i][0]=$qdata['version'];
			$prod_ver[$i][1]=$qdata['version'];
			$i++;
			continue;
		}
		$prod_ver[$i][0]=$qdata['version'];
		$prod_ver[$i][1]=$qdata['download_link'];
		$prod_ver[$i][2]=$qdata['release_date'];
		if($skew!=null){
			$prod_ver[$i][3]=$qdata['GPG_README_LINK'];
			$prod_ver[$i][4]=$qdata['GPG_STEP_BY_STEP_LINK'];
			$prod_ver[$i][5]=$qdata['GPG_SYS_REQUIREMENT'];
			$prod_ver[$i][6]=$qdata['GPG_INSTALLATION_GUIDE_URL'];
			$prod_ver[$i][7]=$qdata['GPV_RELEASE_NOTE_HLINK'];
			$prod_ver[$i][8]=$qdata['GPV_DOWNLOAD_SIZE'] .' '.$qdata['GPV_DOWNLOAD_SIZE_UNIT'];
			$prod_ver[$i][9]=$qdata['GPG_CBT_LINK'];
			$prod_ver[$i][10]=$qdata['GPG_SERVICE_PACK_LINK'];
			$prod_ver[$i][11]=$qdata['GPG_SAMPLE_DB_LINK'];   
			$prod_ver[$i][12]=$qdata['GPV_DOWNLOAD_HLINKPH'];    
			$prod_ver[$i][13]=$qdata['GPV_DOWNLOAD_PH_SIZE'] .' '.$qdata['GPV_DOWNLOAD_PH_SIZE_UNIT'];
			$prod_ver[$i][14]=$qdata['PABR'];
			$prod_ver[$i][15]=$qdata['GPG_CRASH_RESTORE_LINK'];
			$prod_ver[$i][16]=$qdata['gpg_support_mail_id'];
			$prod_ver[$i][17]=$qdata['GPG_LIVE_CHAT_URL'];
			$prod_ver[$i][18]=$qdata['GPG_HELP_LINK'];
		}
		$i++;
    }
	return $prod_ver;
}

/**
 * @param string $ord_pcode
 * 
 * @return string[int][int]
 */
function get_skew_list($ord_pcode){
	$query="select gpm_product_skew,gpm_skew_desc from gft_product_master where gpm_product_code='$ord_pcode'";
	return get_two_dimensinal_result_set_from_query($query);
}

/**
 * @param string $productcode
 * 
 * @return string
 */
function get_head_of_family($productcode){
 	$query="select gpm_head_family from gft_product_family_master where gpm_product_code='$productcode' ";
 	$result=execute_my_query($query,'product_util.php',true,false,2);
 	$qdata=mysqli_fetch_array($result);
 	$head_of_family=$qdata['gpm_head_family'];
 	return $head_of_family;
}	

/**
 * @param string $prcode
 * 
 * @return string[int][int]
 */
function get_upgradation_skew_for_product($prcode){
	$head_of_family=get_head_of_family($prcode);
	$query1="SELECT concat(pm.GPM_PRODUCT_CODE,'-',pm.GPM_PRODUCT_SKEW) pcode, concat(pfm.GPM_PRODUCT_ABR,'-', pm.GPM_SKEW_DESC, cast(if(pm.gpm_status!='A','*','') AS CHAR CHARACTER SET latin1 ) ) pdesc " .
		" FROM gft_product_master pm ,gft_product_family_master pfm " .
		" WHERE ".($head_of_family==100?"pfm.GPM_HEAD_FAMILY in ('100','500')": " pfm.GPM_HEAD_FAMILY='$head_of_family'")." AND GFT_SKEW_PROPERTY=1 " .
		" and pm.GPM_PRODUCT_CODE=pfm.GPM_PRODUCT_CODE and pm.GPM_PRODUCT_TYPE!=8 order by 2 "; //and pm.gpm_status='A'
	return get_two_dimensinal_result_set_from_query($query1);
}

/**
 * @param boolean $select
 * @param string $default_name
 * @param string $product_type_code
 * @param string $status
 * @param boolean $option_tag
 * @param string $selected_value
 * 
 * @return mixed
 */
function skew_propery_master($select=false,$default_name='Select',$product_type_code=null,$status='A',
$option_tag=false,$selected_value=null){
    $query="SELECT GSPM_CODE, GSPM_DESC,GSP_NAME  FROM gft_skew_property_master,gft_skew_primary_master " .
    	  "where gspm_skew_type=gsp_code ";
    $query.=" order by GSP_NAME,GSPM_DESC ";
    //Change the order by to give first preference to  GSPM_DESC - for the combobox in product_view.php
    $result=execute_my_query($query,'common_util.php',true,false,2);
    $i=0;$slist="";
    if($select==true){
		if($default_name=='Select'){ $default_name="-".$default_name."-";}
		$slist="<option value='0'> $default_name</option>";
	}

    $skew_prp_code=/*. (string[int][int]) .*/ array();
    while($qdata=mysqli_fetch_array($result)){
    	$skew_code=$qdata['GSPM_CODE'];
    	$skew_pdesc=$qdata['GSPM_DESC'];
    	$skew_group=$qdata['GSP_NAME'];
    	$skew_prp_code[$i][0]=$skew_code;
    	$skew_prp_code[$i][1]=$skew_pdesc;
    	$skew_prp_code[$i][2]=$skew_group;
    	$i++;
    	if($skew_code==$selected_value) $s="selected";else $s="";				
		$slist.="<option value='$skew_code' $s>$skew_pdesc</option>";
    }
    if($option_tag==true){
    	return $slist;
    }
    return 	$skew_prp_code;
}

/**
 * @return void
 */
function product_details_demo(){
global $tab_index;
	$product_list=get_product_list_family('','A','1','',true,null,true);
	if(count($product_list)>0){
echo<<<END
<tr><td colspan="2">
<fieldset><legend ><font size="2" color="red"> Products Interested </font></legend>
<table>
END;
$script_call="";
	for($i=0;$i<count($product_list);$i++){
		$pcode=$product_list[$i][0];
		$pabr=$product_list[$i][2];
		if($i%4==0) { echo "<tr>"; }
		$script_call.='$("prod'.$pcode.'").value="";'."\n";
		$script_call.='$("old_prod'.$pcode.'").value="";'."\n";
echo<<<END
<td class="content_txt" nowrap >
<input type="hidden" name="old_prod[]" id="old_prod$pcode" value="" >
<input type="checkbox" name="prod_arr[]" id="prod$pcode" value="$pcode" tabindex="$tab_index">$pabr</td>
END;
	}
echo<<<END
</tr></table>
<script type='text/javascript'>
function clear_prod_intersted(){
$script_call
}
</script>
</fieldset></td></tr>
END;
$tab_index++;
	}
}

/**
 * @param int $pcode
 * 
 * @return boolean
 */
function is_base_product($pcode){
	$base_val = get_single_value_from_single_table("GPM_IS_BASE_PRODUCT", "gft_product_family_master", "GPM_PRODUCT_CODE", "$pcode");
	if($base_val=='Y'){
		return true;
	}
	return false;
}

/**
 * @param string $pcode
 * @param boolean $only_minimum_version
 * 
 * @return void
 */
function show_product_version($pcode,$only_minimum_version=false){
	global $me,$uid;
	$query="select gpm_desc,gpv_major_version,gpv_minor_version,gpv_patch_version," .
  		" gpv_exe_version, gpv_release_date,gpv_version,GPV_PRODUCT_STATUS," .
  		" gpv_release_note,GPV_DOWNLOAD_HLINK,GPV_BASE_VERSION,GPV_DOWNLOAD_HLINKPH," .
  		" GPV_PRODUCT_STATUS,GPV_RELEASE_NOTE_HLINK,GPM_HEAD_FAMILY,gpg_skew,GPV_PUBLISH_IN_WEB,GPV_STABLE_VERSION, " .
  		" GPG_EMP_FOR_ADD_VERSION from gft_product_version_master " .
  		" join gft_product_family_master on (gpv_product_code=gpm_product_code and gpm_product_code=GPM_HEAD_FAMILY)" .
  		" join gft_product_group_master on(gpg_product_family_code=gpm_product_code and gpv_version_family like concat(gpg_skew,'%') ) " .
  		" where concat(GPM_HEAD_FAMILY,'-',gpg_skew)='$pcode'" ;
	$title_hdr = "Product Version details";
  	if($only_minimum_version){
  		$query .= " and GPV_IS_MINIMUM_VERSION=1 ";
  		$title_hdr = "Minimum Version details";
  	}
  	$query .= " order by gpv_release_date desc,gpv_entered_on desc ";
	$result=execute_my_query($query);
echo<<<END
<form name="change_product_form" method="post" action="product_version.php">
<input type="hidden" name="version_status" id="version_status" value="version_status"/>
END;
print_dtable_header($title_hdr);
$myarr=array("S.No","Edit","Chitti <br> Message","Product","Version","Installer","Patch","Release Date","Release Note","Publish","Stable","Status");
$count_num_rows=mysqli_num_rows($result);
$nav_struct=get_dtable_navigation_struct($count_num_rows);
print_dtable_navigation($count_num_rows,$nav_struct,$me,"export_all_report.php",$query);
echo<<<END
<table cellpadding="0" cellspacing="1" width="100%" border="0" class="FormBorder1">
END;
	sortheaders($myarr,null,$nav_struct,$sortbycol=null,$sorttype=null);
	$alt_row_class=array("oddListRow","evenListRow","highlight_red");
	$synkmantis="";
	$start_of_row=(int)$nav_struct['start_of_row'];
	$MAX_NO_OF_ROWS=(int)$nav_struct['records_per_page'];
	$permission_emp_id=0;
	if($count_num_rows>0){
	$sl=0;
	mysqli_data_seek($result,$start_of_row);	
	while(($qdata=mysqli_fetch_array($result))  and $MAX_NO_OF_ROWS > $sl){
		$sl++;
	 	$product=$qdata['gpm_desc'];
	 	$major_v=$qdata['gpv_major_version'];
	 	$minor_v=$qdata['gpv_minor_version'];
	 	$patch_v=$qdata['gpv_patch_version'];
	 	$exe_v=$qdata['gpv_exe_version'];
	 	$release=$qdata['gpv_release_date'];
	 	$release_note=$qdata['gpv_release_note'];
	 	$dlink=$qdata['GPV_DOWNLOAD_HLINK'];
	  	$parentversion=$qdata['GPV_BASE_VERSION'];
	  	$ph_dlink=$qdata['GPV_DOWNLOAD_HLINKPH'];
	  	$release_note_link=$qdata['GPV_RELEASE_NOTE_HLINK'];
	  	$product_code=$qdata['GPM_HEAD_FAMILY'].'-'.$qdata['gpg_skew'];
	  	$gpv_version = $qdata['gpv_version'];
	  	$state=$qdata['GPV_PRODUCT_STATUS']; 
	  	$stat_color = ($state=='A')?'green':'red';
	 	$ver="".$major_v.".".$minor_v.".".$patch_v.".".$exe_v."";
	 	$release_note="<a href=\"$release_note_link\" target=_blank >".$release_note."</a>";
	 	$param="<a href=javascript:call_popup('enter_product_version.php?edit=edit&product=".$pcode."&major_v=".$major_v."&minor_v=".$minor_v."&patch_v=".$patch_v."&exe_v=".$exe_v."',6);><span title='Edit' class='fas fa-edit' style='font-size:14px;color:#3D5164'></span></a>";
	 	if(is_base_product($pcode)){
	 		$del="<a href=javascript:call_popup('version_inactive.php?product=$pcode&version=$gpv_version&versionStatus=$state',5);><span title='Status Change' class='fas fa-ban' style='font-size:14px;color:#3D5164'></span></a>";	 		
	 	}else{
	 		$del="<a href=javascript:call_popup('delete_version_details.php?product=".$pcode."&major_v=".$major_v."&minor_v=".$minor_v."&patch_v=".$patch_v."&exe_v=".$exe_v."',6);><i title='Delete' class='fas fa-trash-alt' style='font-size:14px;color:#3D5164'></i></a>";
	 	}
	 	$stat_str = "<i class='fas fa-check' style='font-size:14px;color:$stat_color'></i>";
		if(trim($ph_dlink)==""){
			$link1="";
		}else{
			$link1="<a href=\"$ph_dlink\">[P]</a>";
		}
		if(trim($dlink)==""){
			$link2="";
		}else{
			$link2="<a href=\"$dlink\">[I]</a>" ;
		}
		$permission_emp_id=$qdata['GPG_EMP_FOR_ADD_VERSION'];
		if($uid!=$qdata['GPG_EMP_FOR_ADD_VERSION'] && (!is_authorized_group_list($uid, array(96)))){
			$param='';$del='';
		}
		$gm_update_link = "<a href=javascript:call_popup('message_update.php?product=$pcode&version=$ver',6);>GM</a>";
		$value_arr[0]=array($sl," $param &nbsp; $del ",$gm_update_link,$product,$ver,
		$link2,$link1,$release,$release_note,$qdata['GPV_PUBLISH_IN_WEB'],$qdata['GPV_STABLE_VERSION'],$stat_str);
		print_resultset($value_arr);
	}
	}
	echo "</table><br><br><br></td></tr>";

$split_pcode_arr = explode('-', $pcode);
$product_code =	$split_pcode_arr[0];
if($product_code==500 || $product_code==200){
	$query1 = "select GPV_IS_CLOUD_MIN_VERSION,concat(gpm_desc,'-',gpg_version) product_abr,GPV_RELEASE_DATE,".
			"GPV_VERSION from gft_product_family_master".
			" join gft_product_group_master on (gpg_product_family_code=gpm_product_code and gpg_status='A')".
			"left join gft_product_version_master on (GPV_PRODUCT_CODE=gpg_product_family_code and GPV_version_family=gpg_skew  )".
			"where concat(GPM_HEAD_FAMILY,'-',gpg_skew)='$pcode' and GPV_IS_CLOUD_MIN_VERSION=1 order by GPV_RELEASE_DATE desc limit 25";
	$title = "Cloud Minimum Version details";
	$result1=execute_my_query($query1);
	print_dtable_header($title);
	$myarr=array("S.No","Product"," Cloud Version","Release Date");
echo<<<END
	<table cellpadding="0" cellspacing="1" width="100%" border="0" class="FormBorder1">
END;
	sortheaders($myarr,null,$nav_struct,$sortbycol=null,$sorttype=null);
	if($count_num_rows>0){
		$sl=0;
		while(($qdata=mysqli_fetch_array($result1))  and $MAX_NO_OF_ROWS > $sl){
			$sl++;
			$product=$qdata['product_abr'];
			$cloud_version = $qdata['GPV_VERSION'];
			$release=$qdata['GPV_RELEASE_DATE'];
			$value[0]=array($sl,$product,$cloud_version,$release);
			print_resultset($value);
			}
	}
echo<<<END
 </table>
END;

}
}
/**
 * @param string $from_status
 * @param string $to_status
 * @param string $product
 * @param string $on_date
 * @param string $in_version
 * @param string $from_group
 * @param string $to_group
 * 
 * @return void
 */
function support_status_change($from_status,$to_status,$product,$on_date=null,$in_version=null,
$from_group=null,$to_group=null){
global $address_fields,$query_contact_dtl;
	$query="select GCH_COMPLAINT_ID,GCH_COMPLAINT_DATE,GLH_LEAD_CODE $address_fields ,
	dtl.GCD_PROBLEM_SUMMARY ps,dtl.GCD_PROBLEM_DESC pd,st.GTM_NAME,
	if(ldtl.GCD_STATUS='T1',se.GEM_EMP_NAME,pe.GEM_EMP_NAME) as ASSIGNED_CLOSED 
	from  gft_status_history 
	inner join gft_customer_support_dtl dtl on (dtl.gcd_complaint_id=gcd_complaint_id and gcd_activity_id=gsh_activity_id)
	inner join gft_customer_support_hdr hdr on (hdr.gch_complaint_id=gcd_complaint_id)
	inner join gft_customer_support_dtl ldtl on (ldtl.gcd_activity_id=GCH_LAST_ACTIVITY_ID)
	inner join gft_lead_hdr lh on (glh_lead_code=gch_lead_code)
	inner join gft_status_master st on (st.gtm_code=gch_current_status)
	join gft_product_family_master pfm1 on (pfm1.GPM_PRODUCT_CODE=GCH_PRODUCT_CODE)
	join gft_product_group_master pg on (gpg_product_family_code=pfm1.GPM_head_family AND hdr.GCH_PRODUCT_SKEW =pg.gpg_skew) 
	$query_contact_dtl
	inner join gft_status_master fg on (fg.gtm_code=gsh_old_status)
	inner join gft_status_master tg on (tg.gtm_code=gsh_new_status)
	left join gft_emp_master pe on (pe.gem_emp_id=ldtl.GCD_PROCESS_EMP)
	left join gft_emp_master se on (se.gem_emp_id=ldtl.GCD_EMPLOYEE_ID)
	where 1 ";
	if($from_status!=null and $to_status!=null){
	$query.=" and gsh_old_status='$from_status' and gsh_new_status='$to_status' "; }
	if($from_group!=null and $to_group!=null){
	$query.=" and fg.gtm_group_id='$from_group' and tg.gtm_group_id='$to_group'	 "; }
	if($on_date!=null){ $query_from_date=db_date_format($on_date);
	 $query.=" and date(dtl.gcd_activity_date)='$query_from_date' ";}
	if($in_version!=null){ $query.=" and GCH_FIXED_IN_VERSION='$in_version' "; }
	if ($product != null and $product != "0" and $product != '') {
		$sid = explode('-', $product);
		$product1 = $sid[0];
		$familyver = $sid[1];
		$query .= " AND GPM_HEAD_FAMILY = $product1 AND pg.gpg_skew ='$familyver' ";
	}
	$query.=" group by glh_lead_code,gch_complaint_id";
	
	$myarr=array("S.No","Complaint Id","Complaint Date","Customer Id","Customer",
	"Problem Summary","Problem Desc","Current Status","Assigned To / Closed by ");
	$mysort=array("","GCH_COMPLAINT_ID","GCH_COMPLAINT_DATE","GLH_LEAD_CODE","GLH_CUST_NAME",
	"ps","pd","GTM_NAME","ASSIGNED_CLOSED");
	$from_status_str='';
	$to_status_str='';

	if($from_status!=null and $to_status!=null){
	$from_status_arr=get_support_status_master($from_status);
	$to_status_arr=get_support_status_master($to_status);
	$from_status_str=$from_status_arr[0][1];
	$to_status_str=$to_status_arr[0][1];
	}else if($from_group!=null and $to_group!=null){
		$from_status_arr=get_support_status_master(null,$from_group);
		$to_status_arr=get_support_status_master(null,$to_group);
		for($i=0;$i<count($from_status_arr);$i++){
		$from_status_str.=$from_status_arr[$i][1].",";
		}
		for($i=0;$i<count($to_status_arr);$i++){
		$to_status_str.=$to_status_arr[$i][1].",";
		}
		$from_status_str=substr($from_status_str,0,-1);
		$to_status_str=substr($to_status_str,0,-1);
		
	}
	$report_link[1]="complaint_details.php?";
	$show_in_popup[1]="Y";
	$table_header="Status Changed from ".$from_status_str." to ".$to_status_str." ";
	generate_reports($table_header,$query,$myarr,$mysort,$sms_category=null,$email_category=null,
$previous_months_link=null,$value_arr_align=null,$myarr_width=null,$total_query=null,$myarr_extra_link=null,
$myarr_sub=null,$rowspan_arr=null,$colspan_arr=null,$myarr_sub1=null,$rowspan_arr1=null,
$colspan_arr1=null,$report_link,$show_in_popup=null,$scorallable_tbody=true,$navigation=false,$order_by=null,true,
$value_arr_total=null);
}

/**
 * @param string $pcode
 * @param string $skew
 * @param string $free_edition
 * @param string $au
 * @param string $vertical
 * 
 * @return void
 */
function show_product_download_website($pcode,$skew,$free_edition,$au,$vertical){
	
	$version_dtl=get_version($pcode,$skew,$select=false,$latest='y');
	$version=$version_dtl[0][0];	
	$download_link=$version_dtl[0][1];
	$release_date=$version_dtl[0][2];
	$readme_link=$version_dtl[0][3];
	$step_by_step_instruction_file=$version_dtl[0][4];
	$sys_req=$version_dtl[0][5];
	$installation_guide=$version_dtl[0][6];
	$release_note_link=$version_dtl[0][7];
	$download_size=$version_dtl[0][8];
	$cbt_link=$version_dtl[0][9];
	$service_pack_link=$version_dtl[0][10];
	$sample_db_link=$version_dtl[0][11];
	$link_skew=substr($skew,1,1);
	$pabr=$version_dtl[0][14];
	$sam_url="http://gofrugal.com$au/thankyou.html?param=$pabr&free_edition=$free_edition&vertical=$vertical";
echo<<<END
	<table>
		<tr><td valign="top" colspan="3" class="bodytxt"><strong><br/>Download details</strong></td></tr>
		<tr><td valign="top" colspan="3"><table width="100%">
		<tr><td width="52%" valign="top" class="bodytxt">Version:</td>
			<td width="48%" valign="top" class="bodytxt">$version</td></tr>
		<tr><td class="bodytxt">Date Published:</td><td class="bodytxt">$release_date</td></tr>
		<tr><td class="bodytxt">Language:</td>
			<td class="bodytxt">English</td></tr>
		<tr><td class="bodytxt">Download Size:</td><td class="bodytxt">$download_size</td></tr>
		<tr><td colspan=2>
			<a href="$sam_url" target=_blank>
			<img src="../images/download.gif" alt="Download" name="POS" width="167" height="31" border="0" align="left"/></a></td></tr>
			
END;
if($free_edition=='Y'){
	$order_cd_url='';
	$result_ocd=execute_my_query("SELECT GSPL_LINK FROM gft_store_product_link_master WHERE GSPL_PRODUCT_CODE='$pcode' and GSPL_LINK_DESC='Order Free Evaluation CD' ");
	if($datacd=mysqli_fetch_array($result_ocd)){
		$order_cd_url=$datacd['GSPL_LINK'];
	}
	if($order_cd_url!=''){
echo<<<END
		<tr><td colspan=2 align="center"><a href="$order_cd_url" target=_blank><img src="images/orderCD.jpg" alt="RayMedi"  border="0" align="center"/></a></td></tr>
END;
	}
}
echo<<<END
		<tr><td class="bodytxt" colspan="3"><strong>Prerequisite for Installation</strong></td></tr>
			</td></tr>
		<tr><td class="bodytxt" colspan="3">
<pre>
<a class="link1txt">Make sure you are online for license activation </a>		
</pre></td></tr>
<tr><td class="bodytxt" colspan="3"><strong><br />Related Resources</strong></td></tr>
END;
if($readme_link!=''){
echo<<<END
<tr><td class="bodytxt" colspan="3">
<a id="readme" onmouseout="javascript:this.style.textDecoration='none'" 
onmouseover="javascript:this.style.textDecoration='underline'" class="link1txt" 
target="_blank" href="$readme_link">Readme</a></td></tr>
END;
}
echo<<<END
<tr><td class="bodytxt" colspan="3"><a id="Release Notes" 
onmouseout="javascript:this.style.textDecoration='none'" 
onmouseover="javascript:this.style.textDecoration='underline'" 
class="link1txt" target="_blank" href="$release_note_link">Release Notes</a></td></tr>
END;
if($step_by_step_instruction_file!=''){
echo<<<END
<tr><td class="bodytxt" colspan="3">
<a id="step by step instruction" onmouseout="javascript:this.style.textDecoration='none'" 
onmouseover="javascript:this.style.textDecoration='underline'" 
class="link1txt" target="_blank" href="$step_by_step_instruction_file">Step by Step Instructions</a></td></tr>
END;
}
if($cbt_link!=''){
echo<<<END
 <tr><td class="bodytxt" colspan="3"><a id="Audio visual Training" 
onmouseout="javascript:this.style.textDecoration='none'" 
onmouseover="javascript:this.style.textDecoration='underline'" class="link1txt" target="_blank"
href="$cbt_link">Installation Training</a></td></tr>
END;
}
if($sys_req!=''){
echo<<<END
<tr><td class="bodytxt" colspan="3"><a id="System Requirements" 
href="$sys_req" class="link1txt" target="_blank" 
onmouseover="javascript:this.style.textDecoration='underline'"
onmouseout="javascript:this.style.textDecoration='none'">System Requirements </a></td></tr>
END;
}
if($service_pack_link!='' && isset($vertical) && $vertical!=''){
	$service_pack_link.="?vertical=$vertical";
echo<<<END
<tr><td class="bodytxt" colspan="3"><a id="Service Pack" 
href="$service_pack_link" class="link1txt" target="_blank" 
onmouseover="javascript:this.style.textDecoration='underline'"
onmouseout="javascript:this.style.textDecoration='none'">Service Pack </a></td></tr>
END;
}

if($sample_db_link!=''){
echo<<<END
<tr><td class="bodytxt" colspan="3"><a id="Sample DB " 
href="$sample_db_link" class="link1txt" target="_blank" 
onmouseover="javascript:this.style.textDecoration='underline'"
onmouseout="javascript:this.style.textDecoration='none'">Sample DB </a></td></tr>
END;
}
echo<<<END
</table></td></tr></table>
END;
}
/*
function call_domain_name($cust_id,$gft_order_no,$product){
	$product_code=substr($product,0,3);
	$product_skew=substr($product,3,(strlen($product)-3));
	$order_no=substr($gft_order_no,0,15);
	$fullfillno=substr($gft_order_no,15,4);
	$result1=execute_my_query("SELECT concat(GPM_PRODUCT_CODE,GPM_REFERER_SKEW) reff_product FROM gft_product_master WHERE concat(GPM_PRODUCT_CODE,GPM_PRODUCT_SKEW)='$product' ");
	if($data1=mysqli_fetch_array($result1)){
		$reffproduct=$data1['reff_product'];
	}
	$reffproduct_skew=substr($reffproduct,3);
	$order_validation= "SELECT god_lead_code, gop_product_code, gop_product_skew, GOP_QTY,gid_lead_code, gid_lic_order_no, gid_lic_pcode,gid_lic_pskew, gid_expire_for, gid_validity_date,gid_no_clients, gid_no_companys,GPM_SUBSCRIPTION_PERIOD " .
		" FROM gft_order_hdr " .
		" JOIN gft_order_product_dtl on(GOP_ORDER_NO=GOD_ORDER_NO and god_order_status='A')" .
		" join gft_product_master on(gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew) " .
		" left join gft_install_dtl_new on(GOD_ORDER_NO=GID_ORDER_NO and gop_product_code=gid_product_code and gid_product_skew=gop_product_skew and gid_status='A') " .
		" where god_order_splict=false and god_lead_code='$cust_id' and gop_product_code=$product_code and gop_product_skew='$reffproduct_skew' ";
	$result_varifacation=execute_my_query($order_validation);
	if($data_varify=mysqli_fetch_array($result_varifacation)){
		if($data_varify['gid_lead_code']!='' and $data_varify['gid_lead_code']!=null){
			if($data_varify['gop_product_code']==$data_varify['gid_lic_pcode'] and $data_varify['gop_product_skew']==$data_varify['gid_lic_pskew']){
				$da_order_no=$data_varify['gid_lic_order_no'];
				$da_expired_for=$data_varify['gid_expire_for'];
				$da_exiry_date=$data_varify['gid_validity_date'];
				$da_no_tills=$data_varify['gid_no_clients'];
				$da_no_complany=$data_varify['gid_no_companys'];
				if(isset($_REQUEST['domain_name_confirmation'])){
					$return_statement['ERROR_CODE']=1003;
					$return_statement['ERROR_MESSAGE']=get_samee_const($product_code.'_other_email_id');
					return $return_statement;
				}
				echo "Code: 1003 <br>";
				echo get_samee_const($product_code.'_other_email_id');
				echo "</body></html>";
				exit;
			}
		}
	}
	$query_tennant= "SELECT SERVER_ID, TENANT_ID,TENANT_DOMAIN_NAME, TENANT_NAME,GSM_WPOS_ADDR FROM gft_tenant_master" .
			" join gft_server_master gs on(GSM_SERVER_ID=SERVER_ID) " . 
			" WHERE TENANT_STATUS=0 and TENANT_PRODUCT=$product_code ORDER BY SERVER_ID, TENANT_ID limit 1";
	$result_tennant=execute_my_query($query_tennant);
	if(mysqli_num_rows($result_tennant)!=0){
		if($data_tennant=mysqli_fetch_array($result_tennant)){
			$server_id=$data_tennant['SERVER_ID'];
			$tenant_id=$data_tennant['TENANT_ID'];
			$default_domain_name=$data_tennant['TENANT_DOMAIN_NAME'];
			$tenant_name=$data_tennant['TENANT_NAME'];
			$base_domain_name=$data_tennant['GSM_WPOS_ADDR'];
		}
		if(isset($_REQUEST['domain_name_confirmation'])){
			$return_statement['default_domain_name']=$default_domain_name;
			return $return_statement;
		}
		domain_name_form($cust_id,$gft_order_no,$product,$default_domain_name,$default_domain_name,$base_domain_name);
		echo "</body></html>";
		exit;
	}else{
		$product_dtl=get_product_list_family(null,null,null,null,null,$product_code);	
		$msg="Customer Id :$cust_id , Registration order No: $order_no , Product :$product Tenant Space Not available. Please contact the customer soon";
		send_mail_from_sam($category=2,get_samee_const("ADMIN_TEAM_MAIL_ID"),$product_dtl[0][3],'Tenant Space Not available',$msg);
		if(isset($_REQUEST['domain_name_confirmation'])){
			$return_statement['ERROR_MESSAGE']=get_samee_const($product_code.'_tenant_space_not_available');
			return $return_statement;
		}
		echo get_samee_const($product_code.'_tenant_space_not_available');
		exit;
	}
}
true pos not completed
*/

/**
 * @param string[int] $prod_arr
 * @param string $version_type
 * 
 * @return string[int][int]
 */
function get_latest_product_version($prod_arr,$version_type=''){
	$version_dtl = /*. (string[int][int]) .*/array();
	$i=0;
	foreach ($prod_arr as $val){
		$val_arr = explode('-', $val);
		$query = " select GPV_PRODUCT_CODE, gpv_version_family, GPV_VERSION, if(LENGTH(GPV_CDN_SETUP_LINK)>5,GPV_CDN_SETUP_LINK,GPV_DOWNLOAD_HLINK) GPV_DOWNLOAD_HLINK, GPV_SAMPLE_DB_PATH, ".
				" concat(GPM_PRODUCT_ABR,gpg_version) as pname,GPG_PRODUCT_ALIAS,GPV_CHAIN_MANAGER_PATH,GPV_WEB_REPORTER_PATH,GPV_GST_SAMPLE_DB_PATH,GPV_SMI_WR_LINK ".
				" from gft_product_version_master ".
				" join gft_product_family_master on (GPM_PRODUCT_CODE=GPV_PRODUCT_CODE) ".
				" join gft_product_group_master on (gpg_product_family_code=GPM_HEAD_FAMILY and GPG_SKEW=gpv_version_family) ".
				" where GPV_PRODUCT_CODE='$val_arr[0]' and gpv_version_family='$val_arr[1]' and GPV_PRODUCT_STATUS='A' ";
		if($version_type=='stable'){
			$query .= " and GPV_STABLE_VERSION='Y' ";			
		}
		$query .= " order by GPV_RELEASE_DATE desc, GPV_ENTERED_ON desc limit 1 ";
		$res = execute_my_query($query);
		if($row1 = mysqli_fetch_array($res)){
			$version_dtl[$i][0] = $row1['GPV_PRODUCT_CODE'];
			$version_dtl[$i][1] = $row1['gpv_version_family'];
			$version_dtl[$i][2] = $row1['GPV_VERSION'];
			$version_dtl[$i][3] = $row1['pname'];
			$version_dtl[$i][4] = $row1['GPV_DOWNLOAD_HLINK'];
			$version_dtl[$i][5] = $row1['GPV_SAMPLE_DB_PATH'];
			$version_dtl[$i][6] = $row1['GPG_PRODUCT_ALIAS'];
			$version_dtl[$i][7] = $row1['GPV_CHAIN_MANAGER_PATH'];
			$version_dtl[$i][8] = $row1['GPV_WEB_REPORTER_PATH'];
			$version_dtl[$i][9] = $row1['GPV_GST_SAMPLE_DB_PATH'];
			$version_dtl[$i][10] = $row1['GPV_SMI_WR_LINK'];
			$i++;
		}
	}
	return $version_dtl;
}
/**
 * @param string $type
 * @param boolean $with_tax_amount
 * 
 * @return mixed[][]
 */
function get_asa_print_list($type,$with_tax_amount=true){
	$usd_conv=get_samee_const('UNIV_PRICE_CONVERSION');
	$skew_property_id=($type=="pd_price_list"?"7":"8");
	$return_list["$type"]=array();
	$query_pd_services =" select GSPM_DESC,GAP_PRICE_NAME,GAP_PRICE_DESC,if(GAP_PRICE!='' and GAP_PRICE!=0,if(GAP_INCL_OF_TAX='Y',GAP_PRICE,ceil(GAP_PRICE*((100+GAP_TAX_PERC+GAP_SERVICE_TAX_PERC)/100))),0) as GAP_PRICE, ".
						" GAP_TAX_PERC,GAP_SERVICE_TAX_PERC from gft_assure_price_list_master ".
						" join gft_skew_property_master spm on (GSPM_CODE=GAP_SKEW_PROPERTY_ID) ".
						" where GAP_SKEW_PROPERTY_ID in ($skew_property_id) and GAP_STATUS='A' order by GAP_ORDER_BY ";
	
	$result_services=execute_my_query($query_pd_services);
	if($result_services){
		$i=0;
		while($qdata=mysqli_fetch_array($result_services)){
			if((int)$qdata['GAP_PRICE']!=0){
				$inr=$qdata['GAP_PRICE'];
				$usd=ceil($qdata['GAP_PRICE']/$usd_conv);
				if(!$with_tax_amount){ //exclusive of tax
					$total_tax_perc = (float)$qdata['GAP_TAX_PERC'] + (float)$qdata['GAP_SERVICE_TAX_PERC'];
					$inr = round(($inr * 100)/(100+$total_tax_perc));
				}
				$return_list["$type"][]=array("Title"=>$qdata['GAP_PRICE_NAME'],"Desc"=>$qdata['GAP_PRICE_DESC'],"INR"=>$inr,"Dollar"=>$usd);
				$i++;
			}
		}
	}
	return $return_list;
}
/**
 * @param string $cust_country
 * 
 * @return string[string][string]
 */
function get_phone_support_pricing($cust_country=''){
	$phone_support_plan =array();
	$result = execute_my_query("select GPM_SKEW_DESC,GPM_LIST_PRICE,GPM_NET_RATE, GPM_USD_RATE,GPM_PRODUCT_CODE,".
								"GPM_PRODUCT_SKEW,GPM_SUPPORT_HRS from gft_product_master ".
								"where GFT_SKEW_PROPERTY=7 AND GPM_SUPPORT_HRS>0 AND GPM_STORE_LIST='Y' AND GPM_STATUS='A' order by GPM_LIST_PRICE asc");
	while($row=mysqli_fetch_array($result)){
		$plan_dtl['Title'] = "Phone and Remote support";
		$plan_dtl['Desc'] = $row['GPM_SKEW_DESC'];
		$plan_dtl['INR'] = round($row['GPM_NET_RATE']);
		$plan_dtl['Dollar'] = round($row['GPM_USD_RATE']);
		$plan_dtl['ListPrice'] = round($row['GPM_LIST_PRICE']);
		$plan_dtl['id'] 		= $row['GPM_PRODUCT_CODE']."-".$row['GPM_PRODUCT_SKEW'];
		$plan_dtl['price'] 		= (int)$row['GPM_LIST_PRICE'];
		$plan_dtl['hours'] 		= $row['GPM_SUPPORT_HRS']." Hrs";
		$plan_dtl['currency'] 	= "inr";
		$plan_dtl['description']= array("view all terms & conditions","18% GST extra");
		if($cust_country!='' && $cust_country!='india'){			
			$plan_dtl['price'] 		= (int)$row['GPM_LIST_PRICE'];
			$plan_dtl['currency'] 	= "usd";
			$plan_dtl['description']= array("view all terms & conditions");
		}
		$phone_support_plan[]=$plan_dtl;
	}
	return $phone_support_plan;
}
/**
 * @param int $lead_code
 * @param int $product_code
 * 
 * @return string[int]
 */
function get_installed_product_dtl($lead_code,$product_code){
	$arr = /*. (string[int]) .*/array();
	$sql1 = " select GID_VALIDITY_DATE from gft_install_dtl_new where GID_LEAD_CODE='$lead_code' ".
			" and GID_LIC_PCODE='$product_code' and GID_STATUS in ('A','S') ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){ 
		$arr['GID_VALIDITY_DATE'] = $row1['GID_VALIDITY_DATE'];
	}
	return $arr;
}

/**
 * @param string $product_code
 * @param string $product_pg
 * @param int $vertical
 * @param int $business_id
 * @param int[int] $not_in_pcode
 * @param boolean $with_tax
 *
 * @return string[int][string]
 */
function return_addon_price($product_code,$product_pg,$vertical,$business_id,$not_in_pcode=null,$with_tax=true){
	$add_service="";
	global $server_skew_property;
	if($vertical!=1){ $add_service=" or pm.GPM_PRODUCT_TYPE in (6) ";}
	$sql_get_allmicro	=	" SELECT GROUP_CONCAT(GTM_VERTICAL_CODE) AS all_micro FROM gft_vertical_master WHERE GTM_MICRO_OF=$vertical";
	$micro_res=execute_my_query($sql_get_allmicro);
	$micro_row=mysqli_fetch_array($micro_res);
	$micro_list	=	'';
	$str = "";
	if($micro_row['all_micro']!=''){
		$micro_list	= ','.$micro_row['all_micro'];
	}
	$sql_get_addon =" SELECT DISTINCT gpm.GPM_PRODUCT_CODE, gpm.GPM_PRODUCT_SKEW,gpf.GPM_PRODUCT_ABR, gpm.GPM_SKEW_DESC, gpm.GPM_DISPLAY_NAME, gap.GAP_EDITION_ID, gpm.GFT_SKEW_PROPERTY, ".
					" ROUND(gpm.GPM_NET_RATE) GPM_NET_RATE, ROUND(gpm.GPM_LIST_PRICE) GPM_LIST_PRICE, round(gpm.GPM_USD_RATE) GPM_USD_RATE, gpm.GPM_UNIV_PRICE ".
					" FROM gft_addon_product_map AS gap LEFT JOIN gft_product_master AS gpm ON(gap.GAP_ADDON_PRODUCT_CODE=gpm.GPM_PRODUCT_CODE) ".
					" LEFT JOIN gft_product_family_master AS gpf ON(gap.GAP_ADDON_PRODUCT_CODE=gpf.GPM_PRODUCT_CODE) ".
					" WHERE gap.GAP_PRODUCT_CODE='$product_code-$product_pg' AND (gap.GAP_VARTICAL_ID IN($vertical$micro_list) OR gap.GAP_VARTICAL_ID=0) ".
					" AND gpm.GFT_SKEW_PROPERTY in ($server_skew_property) AND gpm.GPM_STATUS='A' and GPM_STORE_LIST='Y' ";
	if( is_array($not_in_pcode) && (count($not_in_pcode)>0) ){
		$str = "'".implode("','", $not_in_pcode)."'";
		$sql_get_addon .= " and gap.GAP_ADDON_PRODUCT_CODE not in ($str) ";
	}

	$addon_rows=execute_my_query($sql_get_addon);
	$price_list=/*. (string[int][string]) .*/ array();
	$i	=0;
	while($qdata=mysqli_fetch_array($addon_rows)){
		$price_list[$i]['Pcode']=$qdata['GPM_PRODUCT_CODE'];
		$price_list[$i]['Pskew']=$qdata['GPM_PRODUCT_SKEW'];
		$price_list[$i]['ProductAbr']=$qdata['GPM_PRODUCT_ABR'];
		$price_list[$i]['SkewName']=$qdata['GPM_SKEW_DESC'];
		$price_list[$i]['SkewDisplayName']=$qdata['GPM_DISPLAY_NAME'];
		$price_list[$i]['EditionType']=$qdata['GAP_EDITION_ID'];
		$price_list[$i]['SkewProperty']=$qdata['GFT_SKEW_PROPERTY'];
		$inr_amount = $qdata['GPM_LIST_PRICE'];
		if($with_tax){
			$inr_amount = $qdata['GPM_NET_RATE'];
		}
		$price_list[$i]['INR']=$inr_amount;
		$price_list[$i]['USD']=$qdata['GPM_USD_RATE'];
		if($qdata['GPM_PRODUCT_CODE']=='703'){
		    $price_list[$i]['INR']='2000';
		    $price_list[$i]['CloudINR']='200';
		    $price_list[$i]['CloudUSD']='6';
		    
		}
		$i++;
	}

	$query= " SELECT gpm.GPM_PRODUCT_CODE, gpf.GPM_PRODUCT_ABR,gpm.GFT_SKEW_PROPERTY, ".
			" GPM_PRODUCT_SKEW, GPM_SKEW_DESC, GPM_DISPLAY_NAME, GAP_EDITION_ID, GPM_LIST_PRICE,GPM_NET_RATE, GPM_USD_RATE  ".
			" FROM gft_addon_product_map AS gap ".
			" LEFT JOIN gft_product_master AS gpm ON (gap.GAP_ADDON_PRODUCT_CODE=gpm.GPM_PRODUCT_CODE) ".
			" LEFT JOIN gft_product_family_master AS gpf ON(gap.GAP_ADDON_PRODUCT_CODE=gpf.GPM_PRODUCT_CODE) ".
			" WHERE gap.GAP_PRODUCT_CODE='$product_code-$product_pg' AND (gap.GAP_VARTICAL_ID IN($vertical) OR gap.GAP_VARTICAL_ID=0) ".
			" AND gpm.GFT_SKEW_PROPERTY in (18) AND gpm.GPM_STATUS='A' AND gpm.GPM_PRODUCT_CODE!='604' AND GPM_STORE_LIST='Y'  group by gpm.GPM_PRODUCT_CODE";
	$result=execute_my_query($query);
	while($qdata=mysqli_fetch_array($result)){
		$price_list[$i]['Pcode']=$qdata['GPM_PRODUCT_CODE'];
		$price_list[$i]['ProductAbr']=$qdata['GPM_PRODUCT_ABR'];
		$price_list[$i]['SkewProperty']=$qdata['GFT_SKEW_PROPERTY'];
		$price_list[$i]['Pskew']=$qdata['GPM_PRODUCT_SKEW'];
		$price_list[$i]['SkewName']=$qdata['GPM_SKEW_DESC'];
		$price_list[$i]['SkewDisplayName']=$qdata['GPM_DISPLAY_NAME'];
		$price_list[$i]['EditionType']=$qdata['GAP_EDITION_ID'];
		$inr_amount = $qdata['GPM_LIST_PRICE'];
		if($with_tax){
			$inr_amount = $qdata['GPM_NET_RATE'];
		}
		$price_list[$i]['INR']=$inr_amount;
		$price_list[$i]['USD']=$qdata['GPM_USD_RATE'];
		$i++;
	}
	//Special case for SaaS product - Eg. GoFrugal Alert
	$query= " SELECT distinct gpm.GPM_PRODUCT_CODE, gpf.GPM_PRODUCT_ABR,gpm.GFT_SKEW_PROPERTY ".
			" FROM gft_addon_product_map AS gap ".
			" LEFT JOIN gft_product_master AS gpm ON (gap.GAP_ADDON_PRODUCT_CODE=gpm.GPM_PRODUCT_CODE) ".
			" LEFT JOIN gft_product_family_master AS gpf ON(gap.GAP_ADDON_PRODUCT_CODE=gpf.GPM_PRODUCT_CODE) ".
			" WHERE gap.GAP_PRODUCT_CODE='$product_code-$product_pg' AND (gap.GAP_VARTICAL_ID IN($vertical) OR gap.GAP_VARTICAL_ID=0) ".
			" AND gpm.GFT_SKEW_PROPERTY in (18) AND gpm.GPM_STATUS='A' AND gpm.GPM_PRODUCT_CODE='604'";
	$result=execute_my_query($query);
	while($qdata=mysqli_fetch_array($result)){
		$price_list[$i]['Pcode']=$qdata['GPM_PRODUCT_CODE'];
		$price_list[$i]['ProductAbr']=$qdata['GPM_PRODUCT_ABR'];
		$price_list[$i]['SkewProperty']=$qdata['GFT_SKEW_PROPERTY'];
		$i++;
	}

	return $price_list;
}

/**
 * @param string $product_code
 * @param string $product_pg
 * @param int $vertical
 * @param int $business_id
 * @param boolean $with_tax
 *
 * @return string[int][string]
 */
function return_additional_node_price($product_code,$product_pg,$vertical,$business_id,$with_tax=true){
	$sql_get_additional_node	=	"SELECT gpm.GPM_PRODUCT_CODE,gpm.GPM_PRODUCT_SKEW,gpf.GPM_PRODUCT_ABR,gpm.GPM_SKEW_DESC,gpm.GPM_DISPLAY_NAME,gpm.GFT_SKEW_PROPERTY" .
			" ,ROUND(gpm.GPM_NET_RATE) GPM_NET_RATE,round(gpm.GPM_USD_RATE) GPM_USD_RATE,round(gpm.GPM_LIST_PRICE) GPM_LIST_PRICE,gpm.GPM_UNIV_PRICE " .
			" FROM gft_product_master gpm INNER JOIN gft_product_family_master AS gpf ON(gpm.GPM_PRODUCT_CODE=gpf.GPM_PRODUCT_CODE)" .
			" WHERE gpm.GFT_SKEW_PROPERTY in (3,16) AND gpm.GPM_PRODUCT_CODE=$product_code AND gpm.GPM_PRODUCT_SKEW LIKE '%$product_pg%'  AND gpm.GPM_STATUS='A' AND GPM_STORE_LIST='Y'";
	$additional_node_rows=execute_my_query($sql_get_additional_node);
	$price_list=/*. (string[int][string]) .*/ array();
	//global $usd_conv;
	$i	=0;
	while($qdata=mysqli_fetch_array($additional_node_rows)){
		$price_list[$i]['Pcode']=$qdata['GPM_PRODUCT_CODE'];
		$price_list[$i]['Pskew']=$qdata['GPM_PRODUCT_SKEW'];
		$price_list[$i]['ProductAbr']=$qdata['GPM_PRODUCT_ABR'];
		$price_list[$i]['SkewName']=$qdata['GPM_SKEW_DESC'];
		$price_list[$i]['SkewDisplayName']=$qdata['GPM_DISPLAY_NAME'];
		$price_list[$i]['SkewProperty']=$qdata['GFT_SKEW_PROPERTY'];
		$inr_amount = $qdata['GPM_LIST_PRICE'];
		if($with_tax){
			$inr_amount = $qdata['GPM_NET_RATE'];
		}
		$price_list[$i]['INR']=$inr_amount;
		$price_list[$i]['USD']=$qdata['GPM_USD_RATE'];
		$i++;
	}
	return $price_list;
}

/**
 * @param string $pcode
 * @param string $pgroup
 * 
 * @return string[string]
 */
function get_product_group_master($pcode,$pgroup){
	$sql1 = " select GPG_PRODUCT_ALIAS from gft_product_group_master ".
			" join gft_product_family_master on (GPM_HEAD_FAMILY=gpg_product_family_code) ".
			" where GPM_PRODUCT_CODE='$pcode' and gpg_skew='$pgroup' ";
	$res1 = execute_my_query($sql1);
	$return_arr = /*. (string[string]) .*/array();
	if($row1 = mysqli_fetch_array($res1)){
		$return_arr['alias'] = $row1['GPG_PRODUCT_ALIAS'];
	}
	return $return_arr;
}

/**
 * @param string $prev_prod_code
 * 
 * @return void
 */
function update_minimum_version_in_app_auth_table($prev_prod_code){
	if(in_array($prev_prod_code, array('702'))){
		$que1 = " SELECT GPV_VERSION from gft_product_version_master where gpv_product_code='$prev_prod_code' ".
				" and GPV_IS_MINIMUM_VERSION=1 order by GPV_RELEASE_DATE desc,GPV_ENTERED_ON desc limit 1 ";
		$res1 = execute_my_query($que1);
		if($qdata1 = mysqli_fetch_array($res1)){
			$app_min_version = $qdata1['GPV_VERSION'];
			if($prev_prod_code=='702'){
				execute_my_query("update gft_emp_auth_key set GEK_MIN_VERSION='$app_min_version'");
			}
		}	
	}
}

/**
 * @param string $customer_id
 * @param string $pcode
 * @param string $pskew
 * 
 * @return boolean
 */
function is_order_available($customer_id,$pcode,$pskew){
	$sql1 = " select GOD_ORDER_NO from gft_order_hdr join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
			" where GOD_ORDER_STATUS='A' and GOD_LEAD_CODE='$customer_id' ".
			" and GOP_PRODUCT_CODE='$pcode' and GOP_PRODUCT_SKEW='$pskew' ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		return true;		
	}
	return false;
}

/**
 * @param string $lead_code
 *
 * @return boolean
 */
function is_valid_lead_code($lead_code){
    if((int)$lead_code==0){
        return false;
    }
	$chk_lead = get_single_value_from_single_table("GLH_LEAD_CODE", "gft_lead_hdr", "GLH_LEAD_CODE", $lead_code);
	if($chk_lead==$lead_code){
		return true;
	}
	return false;
}

/**
 * @param int $pcode
 * 
 * @return boolean
 */
function is_valid_product_code($pcode){
    if($pcode==0){
        return false;
    }
    $chk_val = get_single_value_from_single_table("GPM_PRODUCT_CODE", "gft_product_family_master", "GPM_PRODUCT_CODE", $pcode);
    if($chk_val==$pcode){
        return true;
    }
    return false;
}

/**
 * @param string $status_id
 * 
 * @return string
 */
function get_system_compatability_status_string($status_id){
	$str = "";
	switch ($status_id){
		case '1':$str='Passed';break;
		case '2':$str='Failed';break;
		case '3':$str='Warning';break;
		default:$str = $status_id;
	}
	return $str;
}

/**
 * @param string $cust_id
 * @param string $status
 * @param string[int][string] $dtl_arr
 * @param string $username
 * @param string $mobile
 * @param string $product_code
 *
 * @return string
 */
function update_system_compatability_details($cust_id,$status,$dtl_arr,$username='',$mobile='',$product_code=''){
	if(count($dtl_arr)==0){
		return 'detail array is empty';
	}
	$status_str = get_system_compatability_status_string($status);
	$ins_arr = array(
					'HW_CUST_ID'=>$cust_id,'HW_STATUS'=>$status_str,'HW_UPDATE_DATE'=>date('Y-m-d H:i:s'),
					'HW_MOBILE'=>$mobile, 'HW_PRODUCT_CODE'=>$product_code, 'HW_USERNAME'=>$username
				);
	$hid = (int)array_insert_query("gft_hwassessment_info", $ins_arr);
	if($hid==0){
		return 'error in Header Update';
	}
	$ins_val = $put_comma = "";
	foreach ($dtl_arr as $val_arr){
		$an = mysqli_real_escape_string_wrapper($val_arr['name']);
		$av = mysqli_real_escape_string_wrapper($val_arr['value']);
		$astat = get_system_compatability_status_string($val_arr['status']);
		$ar = mysqli_real_escape_string_wrapper($val_arr['recommended']);
		$ins_val	.= " $put_comma ('$hid','$an','$av','$astat','$ar') ";
		$put_comma = ",";
	}
	$ins_que = " insert into gft_hardware_assessment (GHA_HDR_ID,GHA_KEY_NAME,GHA_KEY_VALUE,GHA_KEY_STATUS,GHA_KEY_RECOMMENDED) values $ins_val ";
	$res = execute_my_query($ins_que);
	if($res){
		return 'success';
	}else{
		return 'error in detail insert';
	}
}

/**
 * @param string $god_order_no
 * @param string $sch_date
 * @param string $sch_time
 *
 * @return string
 */
function update_training_schedule_date($god_order_no,$sch_date,$sch_time){
	$sch_date =  db_date_format($sch_date);
	if($sch_date==''){
		return 'invalid schedule date';
	}
	if(strlen($god_order_no)!=15){
		return 'invalid order number';
	}
	$upd1 = " update gft_cust_imp_ms_current_status_dtl set GIMC_SESSION_1_CDATE='$sch_date',GIMC_SESSION_1_CTIME='$sch_time' where GIMC_OPCODE like '$god_order_no%' ";
	$res1 = execute_my_query($upd1);
	if($res1){
		return 'success';
	}else{
		return 'error in updating schedule';
	}
}

/**
 * @param string $god_order_no
 * @param int $pd_expense_type
 *
 * @return string
 */
function update_delivery_type_for_order($god_order_no,$pd_expense_type){
	$upd1 = " update gft_order_hdr set GOD_PD_EXPENSE_TYPE='$pd_expense_type' where GOD_ORDER_NO='$god_order_no' ";
	$res1 = execute_my_query($upd1);
	if($res1){
		return 'success';
	}else{
		return 'error in updating pd expense type';
	}
}

/**
 * @param string $cust_id
 * @param int $status
 * 
 * @return void
 */
function update_implementation_current_status($cust_id,$status){
	if(!check_partner_lead_source($cust_id,'',PATANJALI_ID)){
		return;
	}
	$sql1 = " select GCD_CURRENT_STATUS,l1.GIL_LEVEL exist_level,l2.GIL_LEVEL curr_level ".
			" from gft_customer_dashboard ".
			" left join gft_implementation_level l1 on (l1.GIL_ID=GCD_CURRENT_STATUS) ".
			" left join gft_implementation_level l2 on (l2.GIL_ID='$status') ".
			" where GCD_LEAD_CODE='$cust_id' ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)) {
		$exis_stat	= $row1['GCD_CURRENT_STATUS'];
		$exis_level	= (float)$row1['exist_level'];
		$curr_level	= (float)$row1['curr_level'];
		if($curr_level > $exis_level){
			execute_my_query("update gft_customer_dashboard set GCD_CURRENT_STATUS='$status' where GCD_LEAD_CODE='$cust_id'");
		}
	}else{
		$insarr = array('GCD_LEAD_CODE'=>$cust_id,'GCD_CURRENT_STATUS'=>$status);
		array_insert_query("gft_customer_dashboard", $insarr);
	}
}

/**
 * @param string $pcode
 * @param string $pgroup
 * 
 * @return void
 */
function update_current_version_for_product_version($pcode,$pgroup){
	$upd1 =	" update gft_product_version_master set gpv_current_version=0 " .
			" where GPV_PRODUCT_CODE=$pcode and gpv_version_family='$pgroup' ";
	execute_my_query($upd1);
	
	$upd2 = " update gft_product_version_master set GPV_CURRENT_VERSION=1 ".
		 	" where gpv_product_code='$pcode' and gpv_version_family='$pgroup' and GPV_PRODUCT_STATUS='A' ".
			" order by GPV_RELEASE_DATE desc, GPV_ENTERED_ON desc limit 1 ";
	execute_my_query($upd2);
}

/**
 * @param string $pcode_group
 * 
 * @return string
 */
function get_product_column_name_from_code($pcode_group){
    $col = "";
    switch ($pcode_group){
        case '500-07.0' : $col = "GPV_RPOS7_VERSION";break;
        case '500-06.5' : $col = "GPV_RPOS6_VERSION";break;
        case '200-06.0' : $col = "GPV_DE6_VERSION";break;
        case '300-03.0' : $col = "GPV_HQ3_VERSION";break;
        case '601-06.0' : $col = "GPV_TRUEPOS_VERSION";break;
        default:$col="";break;
    }
    return $col;
}

/**
 * @param string $cust_id
 * 
 * @return void
 */
function show_corporate_support_summary_against_call_type($cust_id){
    $sql1 = " select ifnull(GCC_DESC,'To be Updated') GCC_DESC,count(*) as tot_cnt,count(if(GTM_GROUP_ID=3,1,null)) as solv_cnt, ".
        " count(if(GTM_GROUP_ID=9,1,null)) as sqa_cnt, count(if(GTM_GROUP_ID in (2,13),1,null)) as cust_cnt, ".
        " count(if(GTM_GROUP_ID=5,1,null)) as dev_cnt, count(if(GTM_GROUP_ID=10,1,null)) as pcs_cnt, ".
        " count(if(GTM_GROUP_ID=1,1,null)) as other_cnt ".
        " from gft_lead_hdr ".
        " join gft_customer_support_hdr on (GCH_LEAD_CODE=GLH_LEAD_CODE) ".
        " join gft_customer_support_dtl las on (las.gcd_activity_id=GCH_LAST_ACTIVITY_ID and las.GCD_COMPLAINT_ID=GCH_COMPLAINT_ID) ".
        " join gft_customer_support_dtl fir on (fir.gcd_activity_id=GCH_FIRST_ACTIVITY_ID and fir.GCD_COMPLAINT_ID=GCH_COMPLAINT_ID) ".
        " join gft_status_master on (GCH_CURRENT_STATUS=GTM_CODE) ".
        " join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE and GFT_INTERNAL_COMPLAINT=0) ".
        " left join gft_cust_call_master on (las.GCD_CUST_CALL_TYPE=GCC_ID) ".
        " where GTM_GROUP_ID in (1,2,3,5,9,13,10) and (GLH_LEAD_CODE='$cust_id' or (glh_reference_given='$cust_id' and glh_lead_type=13)) ".
        " group by GCC_DESC order by GCC_DESC ";
    
    $tot_que =  " select 'Total' GCC_DESC,count(*) as tot_cnt,count(if(GTM_GROUP_ID=3,1,null)) as solv_cnt, ".
                " count(if(GTM_GROUP_ID=9,1,null)) as sqa_cnt, count(if(GTM_GROUP_ID in (2,13),1,null)) as cust_cnt, ".
                " count(if(GTM_GROUP_ID=5,1,null)) as dev_cnt, count(if(GTM_GROUP_ID=10,1,null)) as pcs_cnt, ".
                " count(if(GTM_GROUP_ID=1,1,null)) as other_cnt ".
                " from gft_lead_hdr ".
                " join gft_customer_support_hdr on (GCH_LEAD_CODE=GLH_LEAD_CODE) ".
                " join gft_customer_support_dtl las on (las.gcd_activity_id=GCH_LAST_ACTIVITY_ID and las.GCD_COMPLAINT_ID=GCH_COMPLAINT_ID) ".
                " join gft_customer_support_dtl fir on (fir.gcd_activity_id=GCH_FIRST_ACTIVITY_ID and fir.GCD_COMPLAINT_ID=GCH_COMPLAINT_ID) ".
                " join gft_status_master on (GCH_CURRENT_STATUS=GTM_CODE) ".
                " join gft_complaint_master on (GFT_COMPLAINT_CODE=GCH_COMPLAINT_CODE and GFT_INTERNAL_COMPLAINT=0) ".
                " left join gft_cust_call_master on (las.GCD_CUST_CALL_TYPE=GCC_ID) ".
                " where GTM_GROUP_ID in (1,2,3,5,9,13,10) and (GLH_LEAD_CODE='$cust_id' or (glh_reference_given='$cust_id' and glh_lead_type=13)) ";
    
    $myarr  = array("Call type","Total","Solved","Pending Support","Pending customer","Pending developer","Pending PCS","Others");
    $mysort = array("GCC_DESC","tot_cnt","solv_cnt","sqa_cnt","cust_cnt","dev_cnt","pcs_cnt","other_cnt");
    generate_reports("Call type vs Support Summary", $sql1, $myarr, $mysort,null,null,null,null,null,$tot_que);
    
}

/**
 * @param string $orderNo
 * 
 * @return void
 */
function update_for_prepaid_license($orderNo){
    $que1 = " select if(GOD_ORDER_SPLICT=1,GCO_CUST_CODE,GOD_LEAD_CODE) as lead,GPMA_ATTRIBUTE,GPMA_VALUE, ".
            " GOD_ORDER_NO, if(GOD_ORDER_SPLICT=1,GCO_FULLFILLMENT_NO,GOP_FULLFILLMENT_NO) fullfill,GOP_PRODUCT_CODE, ".
            " if(GOD_ORDER_SPLICT=1,GCO_CUST_QTY,GOP_QTY) qty ".
            " from gft_order_hdr join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) ".
            " left join gft_cp_order_dtl on (GCO_ORDER_NO=GOP_ORDER_NO and GCO_PRODUCT_CODE=GOP_PRODUCT_CODE and GCO_SKEW=GOP_PRODUCT_SKEW) ".
            " join gft_product_master_attributes on (GPMA_PRODUCT_CODE=GOP_PRODUCT_CODE and GPMA_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
            " where GOD_ORDER_NO='$orderNo' and if(GOD_ORDER_SPLICT=1,GCO_ORDER_NO is not null,1) ";
    $res1 = execute_my_query($que1);
    while ($row1 = mysqli_fetch_array($res1)){
        $ord_no = $row1['GOD_ORDER_NO'];
        $ful_no = $row1['fullfill'];
        $lead   = $row1['lead'];
        if($row1['GPMA_ATTRIBUTE']=='10'){
            $chk_res = execute_my_query("select GPL_ORDER_NO from gft_prepaid_license where GPL_ORDER_NO='$ord_no' and GPL_FULLFILLMENT_NO='$ful_no' ");
            if(mysqli_num_rows($chk_res) > 0){ //already present
                continue;
            }
            $pcode  = $row1['GOP_PRODUCT_CODE'];
            $ord_cnt= $row1['GPMA_VALUE']*$row1['qty'];
            $insert_arr = array(
                "GPL_LEAD_CODE"=>$lead,"GPL_PRODUCT_CODE"=>$pcode,
                "GPL_ORDER_NO"=>$ord_no,"GPL_FULLFILLMENT_NO"=>$ful_no,
                "GPL_COUNT"=>$ord_cnt
            );
            array_insert_query("gft_prepaid_license", $insert_arr);
            $que2 = " select GID_INSTALL_ID from gft_install_dtl_new ".
                    " join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
                    " where GID_LEAD_CODE='$lead' and GID_LIC_PCODE in (200,500) and GID_STATUS!='U' ".
                    " order by GPM_LICENSE_TYPE limit 1 ";
            $res2 = execute_my_query($que2);
            if($row2 = mysqli_fetch_array($res2)){
                $base_install_id = $row2['GID_INSTALL_ID'];
                $res3 = execute_my_query(" select GAO_ID from gft_addon_order_count where GAO_INSTALL_ID='$base_install_id' ");
                if($row3 = mysqli_fetch_array($res3)){
                    $pid = $row3['GAO_ID'];
                    execute_my_query(" update gft_addon_order_count set GAO_ORDER_COUNT=GAO_ORDER_COUNT+$ord_cnt where GAO_ID='$pid' ");
                }else{
                    $ins_arr = array('GAO_INSTALL_ID'=>$base_install_id, 'GAO_PRODUCT_CODE'=>$pcode, 'GAO_ORDER_COUNT'=>$ord_cnt);
                    array_insert_query("gft_addon_order_count", $ins_arr);
                }
            }
        }
    }
}

/**
 * @param string $product_id
 * @param string $edition
 * @param string $vertical
 * 
 * @return string[int]
 */
function get_rebranded_product_name($product_id, $edition, $vertical){
    $q1 = " select GRE_PRODUCT_NAME,GRE_SOLUTION_NAME from gft_rebranding where GRE_PRODUCT_ID='$product_id' ".
          " and (GRE_EDITION='$edition' or GRE_EDITION=0) and (GRE_VERTICAL='$vertical' or GRE_VERTICAL=0) ".
          " order by GRE_EDITION desc, GRE_VERTICAL desc limit 1 ";
    $r1 = execute_my_query($q1);
    $dtl_arr = /*. (string[int]) .*/array();
    if($d1 = mysqli_fetch_array($r1)){
        $dtl_arr[0] = $d1['GRE_PRODUCT_NAME'];
        $dtl_arr[1] = $d1['GRE_SOLUTION_NAME'];
    }
    return $dtl_arr;
}
/**
 * @param string $pcode
 * @param string $pskew
 * @param string[int] $addon_feature
 *
 * @return void
 */
function update_addon_feature_mapping($pcode, $pskew, $addon_feature){
    execute_my_query("delete from gft_addon_feature_skew_mapping where GAM_PRODUCT_CODE='$pcode' AND GAM_PRODUCT_SKEW='$pskew'");
    if ($addon_feature!=""){
        execute_my_query("INSERT INTO gft_addon_feature_skew_mapping(GAM_PRODUCT_CODE, GAM_PRODUCT_SKEW, GAM_FEATURE_ID)".
            " VALUES('$pcode','$pskew','$addon_feature')");
    }
}

/**
 * @param string $lead_code_str
 * 
 * @return void
 */
function delete_pos_users_in_identity_service($lead_code_str){
    $cp_config = get_connectplus_config();
    $cp_api_key = $cp_config['provision_api_key'];
    $header_arr = array("Content-Type: application/json","x-api-key: $cp_api_key");
    $qr = execute_my_query(" select GLH_LEAD_CODE from gft_lead_hdr where GLH_LEAD_CODE in ($lead_code_str) ");
    while ($rd = mysqli_fetch_array($qr)){
        $cust_id = $rd['GLH_LEAD_CODE'];
        $post_url	= (string)str_replace("{{customerId}}", $cust_id, $cp_config['pos_user_url']);
        $ret_arr = do_curl_to_connectplus($cust_id, $post_url, null, $header_arr,"DELETE");
        echo json_encode($ret_arr)."<br><br>";
    }
}

/**
 * @param string $cust_id
 * @param string $pcode
 * @param string $pskew
 * 
 * @return void
 */
function check_and_create_free_product($cust_id,$pcode,$pskew){
    $q1 = " select GLH_TERRITORY_ID,GFP_FREE_PRODUCT from gft_lead_hdr ".
          " join gft_free_product_master on (GFP_VERTICAL=GLH_VERTICAL_CODE) ".
          " where GLH_LEAD_CODE='$cust_id' and GFP_BUY_PRODUCT='$pcode-$pskew' ";
    $r1 = execute_my_query($q1);
    while($d1 = mysqli_fetch_array($r1)){
        $free_prod      = explode("-",$d1['GFP_FREE_PRODUCT']);
        $territory_id   = $d1['GLH_TERRITORY_ID']; 
        if(count($free_prod)==2){
            $free_pcode = $free_prod[0];
            $free_pskew = $free_prod[1];
            $order_hdr_arr = array(
                'GOD_LEAD_CODE'=>$cust_id,
                'GOD_EMP_ID'=>SALES_DUMMY_ID,
                'GOD_INCHARGE_EMP_ID'=>SALES_DUMMY_ID,
                'GOD_ORDER_STATUS'=>'A',
                'GOD_ORDER_AMT'=>0,
                'GOD_REMARKS'=>'Created from default free product based on bought product',
                'GOD_ORDER_NO'=>get_order_no("D", date('y'), $territory_id, SALES_DUMMY_ID)
            );
            array_insert_new_order($order_hdr_arr, array($free_pcode), array($free_pskew), array(1), array("0.00"));
        }
    }
}

/**
 * @param int $convert
 * 
 * @return string
 */
function getYMDSplit($convert){
    $years = ($convert / 365) ;
    $years = floor($years); 
    $month = ($convert % 365) / 30.5; 
    $month = floor($month); 
    $days = ($convert % 365) % 30.5; 
    $str =  $years.' years - '.$month.' month - '.$days.' days';
    return $str;
}

/**
 * @param string $lead_code
 *
 * @return string[int]
 */
function get_additional_alr_info_for_kit_customer($lead_code){
    $purchased_cl =	$cl_skew = $alr_skew = $separator = "";
    $noc_qty = $all_cl_list_price = 0;
    if(is_kit_based_customer($lead_code)){
        $cl_percent = (int)get_samee_const("SAM_ASA_AMT_FOR_CUSTLIC");
        $sql1 = " select GPM_PRODUCT_TYPE,GFT_SKEW_PROPERTY,GOP_QTY,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,if(GLH_COUNTRY='India',GPM_LIST_PRICE,GPM_USD_RATE) price ".
            " from gft_order_product_dtl join gft_order_hdr on (GOD_ORDER_NO=GOP_ORDER_NO) ".
            " join gft_product_master pm on (GOP_PRODUCT_CODE=pm.GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
            " join gft_product_family_master fm on (fm.GPM_PRODUCT_CODE=pm.GPM_PRODUCT_CODE) ".
            " join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ".
            " where GOD_ORDER_STATUS='A' and (GFT_SKEW_PROPERTY=3 or GPM_PRODUCT_TYPE=8) and GOD_LEAD_CODE='$lead_code' and GPM_IS_INTERNAL_PRODUCT=4 ";
        $res1 = execute_my_query($sql1);
        while ($row1 = mysqli_fetch_array($res1)){
            $gop_pcode = $row1['GOP_PRODUCT_CODE'];
            $gop_pskew = $row1['GOP_PRODUCT_SKEW'];
            $gop_qty   = $row1['GOP_QTY'];
            if($row1['GPM_PRODUCT_TYPE']=='8'){
                $cl_asa_list_price = round($gop_qty*(float)$row1['price']*$cl_percent/100,2);
                $purchased_cl .= $separator.$gop_pcode.'-'.$gop_pskew.'-'.$gop_qty.'-'.$cl_asa_list_price;
                $separator = "**";
                $all_cl_list_price += $cl_asa_list_price;
                $cl_skew = $gop_pcode."-".substr($gop_pskew, 0,4)."CLASA";
            }else if($row1['GFT_SKEW_PROPERTY']=='3'){
                $noc_qty += (int)$row1['GOP_QTY'];
                $alr_skew = $gop_pcode."-".substr($gop_pskew, 0,4)."ULASA";
            }
        }
    }
    return array($alr_skew,$noc_qty,$cl_skew,$purchased_cl,$all_cl_list_price);
}

/**
 * @param string $outlet_id
 * @param string $addon_pcode
 * @param string $addon_pskew
 * @param string $qty_count
 * @param string $prod_type
 * 
 * @return void
 */
function entry_in_hq_outlet_product_mapping($outlet_id,$addon_pcode,$addon_pskew,$qty_count,$prod_type){
    $updatearr['GHO_OUTLET_REF_ID']	= $outlet_id;
    $updatearr['GHO_PRODUCT_CODE'] 	= $addon_pcode;
    $updatearr['GHO_PRODUCT_SKEW'] 	= $addon_pskew;
    $updatearr['GHO_QTY'] 		 	= $qty_count;
    $updatearr['GHO_PRODUCT_TYPE'] 	= $prod_type;
    $updatearr['GHO_UPDATED_DATE'] 	= date('Y-m-d H:i:s');
    
    $table_key_arr['GHO_OUTLET_REF_ID']	= $outlet_id;
    $table_key_arr['GHO_PRODUCT_CODE'] 	= $addon_pcode;
    $table_key_arr['GHO_PRODUCT_SKEW'] 	= $addon_pskew;
    $table_key_arr['GHO_PRODUCT_TYPE'] 	= $prod_type;
    
    array_update_tables_common($updatearr, "gft_hq_outlet_product_mapping", $table_key_arr, null,'9999','',null,$updatearr);
}

/**
 * @param string $code
 * @param string $skew
 * @param string $qty
 * @param string $cust_id
 * 
 * @return string
 */
function common_sku_validation($code,$skew,$qty,$cust_id){
    $que1 = " select GPM_SKEW_DESC,GPM_MIN_QTY_REQ from gft_product_master ".
            " where GPM_PRODUCT_CODE='$code' and GPM_PRODUCT_SKEW='$skew' ";
    $res1 = execute_my_query($que1);
    if($row1 = mysqli_fetch_array($res1)){
        $min_qty = (int)$row1['GPM_MIN_QTY_REQ'];
        if( ($min_qty > 1) && ($qty < $min_qty) ){
            return "For ".$row1['GPM_SKEW_DESC'].", minimum qty required is $min_qty"; 
        }
    }
    if($code=='706'){
        $err_msg = validate_gosecure_suggested_bucket($cust_id,$skew,$qty);
        if($err_msg!=''){
            return $err_msg;
        }
    }
    return "";
}

/**
 * @param string[int] $lead_arr
 * @param int $product_code
 *
 * @return string[int]
 */
function get_specific_product_installed_customer_ids_from_list($lead_arr,$product_code){
    $gid_lead_arr = array();
    $lead_str = implode(",", $lead_arr);
    if($lead_str!=''){
        $que1 = " select GLH_LEAD_CODE,GLH_CUST_NAME from gft_install_dtl_new join gft_lead_hdr on (GLH_LEAD_CODE=GID_LEAD_CODE) ".
            " where GID_LEAD_CODE in ($lead_str) and GID_LIC_PCODE='$product_code' and GID_STATUS!='U' group by GID_LEAD_CODE ";
        $res1 = execute_my_query($que1);
        while($row1 = mysqli_fetch_array($res1)){
            $gid_lead_arr[] = array("id"=>$row1['GLH_LEAD_CODE'],"name"=>$row1['GLH_CUST_NAME']);
        }
    }
    return $gid_lead_arr;
}

/**
 * @param string $category
 * @param int $marketplace_id
 * 
 * @return int
 */
function get_vertical_code_for_marketplace_category($category,$marketplace_id){
    $category = mysqli_real_escape_string_wrapper(trim($category));
    $que1 = " select GMC_VERTICAL from gft_marketplace_category_master where GMC_CATEGORY_NAME='$category' and GMC_MARKETPLACE_ID='$marketplace_id' ";
    $res1 = execute_my_query($que1);
    $vert_code = 0;
    if($row1 = mysqli_fetch_assoc($res1)){
        $vert_code = (int)$row1['GMC_VERTICAL'];
    }
    return $vert_code;
}

/**
 * @param string $lead_code
 * @return string[string]
 */
function get_surrender_clients_dtl($lead_code){
    $base_prod =  get_base_installation_dtl($lead_code);
    $output=array();
    if(count($base_prod)>0){
        $order_no = substr("$base_prod[0]",0,-4);
        $que = execute_my_query(" select GID_NO_CLIENTS,GID_SURRENDERED_CLIENTS from gft_install_dtl_new where gid_lead_Code=$lead_code and GID_LIC_PCODE=$base_prod[1] and GID_LIC_PSKEW='$base_prod[2]' and GID_ORDER_NO='$order_no' ");
        if($res=mysqli_fetch_assoc($que)){
            $output["client"] = $res["GID_NO_CLIENTS"];
            $output["surrender_client"] = $res["GID_SURRENDERED_CLIENTS"];
        }
    }
    return $output;
}

/**
 * @param String $lead_code
 * 
 * @return boolean
 */
function is_hq_outlet($lead_code){
    $res1 = execute_my_query(" select GCD_IS_HQ from gft_cust_env_data where GCD_LEAD_CODE='$lead_code' ");
    if(mysqli_num_rows($res1) > 0){
        while ($row1 = mysqli_fetch_array($res1)){
            if((int)$row1['GCD_IS_HQ']==1){
                return true;
            }
        }
    }else{ //if no entry in gft_cust_env_data check from hq outlet lead code mapping
        $res2 = execute_my_query("select GOL_CUST_ID from gft_outlet_lead_code_mapping where GOL_CUST_ID='$lead_code'");
        if(mysqli_num_rows($res2) > 0){
            return true;
        }
    }
    return false;
}

?>
