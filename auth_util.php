<?php
require_once(__DIR__ ."/product_util.php");
if(isset($_SESSION)){
	$curr_month=(int)date('m');
	$curr_yr=(int)date('Y');		
	$date_on=date('d-m-Y');
}

$uid=(isset($_SESSION["uid"])?(string)$_SESSION["uid"]:'');
$uname=(isset($_SESSION["uname"])?(string)$_SESSION["uname"]:'');
$roleid=(isset($_SESSION["roleid"])?(int)$_SESSION["roleid"]:-1);

/* Start from inc.cp_admin.php */
//START_CP_ADMIN
$cp_user_id='';
$cp_roleid=0;
$cgi_incharge_emp_id='';
$cgi_pcs_incharge_emp_id='';
$cp_type='';

/* Moved from inc.essentials_for_popup  */
//START
$gft_partner='';
$cp_lcode='';
$cp_name='';
$cp_terrid='';
if(is_authorized_group_list($uid,$non_employee_group)){
	$sql1=" SELECT CGI_LEAD_CODE,GCA_CP_TYPE,GCA_CP_SUB_TYPE,cgi_incharge_emp_id, cgi_pcs_incharge_emp_id,GEM_ROLE_ID,GEM_EMP_NAME,glh_territory_id,CGI_PARTNER_RELATION, GCR_LEAD_CODE " .
		" FROM gft_leadcode_emp_map " .
		" join gft_emp_master em on(GLEM_EMP_ID=GEM_EMP_ID)" .
		" join gft_cp_info gcp on (GLEM_LEADCODE=CGI_LEAD_CODE) " .
		" join gft_cp_agree_dtl cg on (CGI_lead_code=gca_lead_code AND CGI_CP_AGREENO=gca_cp_agreeno) " .
		" join gft_lead_hdr lh on(CGI_LEAD_CODE=glh_lead_code)" .
		" left join gft_cp_relation cr on(gcr_reseller_lead_code=cgi_lead_code and gcr_cp_level=1) " .
		" where GEM_EMP_ID='$uid'";
	$rs1=execute_my_query($sql1,$me,true,false,4);
	if($row1=mysqli_fetch_array($rs1)){
		$cp_lcode =$row1['CGI_LEAD_CODE'];
		$cp_terrid=$row1['glh_territory_id'];
		$cgi_incharge_emp_id=$row1['cgi_incharge_emp_id'];
		$cgi_pcs_incharge_emp_id=$row1['cgi_pcs_incharge_emp_id'];
		$cp_roleid=(int)$row1['GEM_ROLE_ID'];
		$cp_name=$row1['GEM_EMP_NAME'];
		$cp_relation=(int)$row1['CGI_PARTNER_RELATION'];
		$CP_TYPE=$row1['GCA_CP_TYPE'];
		$CP_SUB_TYPE=$row1['GCA_CP_SUB_TYPE'];
		if($cp_relation==2){
			$gft_partner=$row1['GCR_LEAD_CODE'];
		}else{
			$gft_partner=$cp_lcode;	
		}
		$cp_user_id=$uid;
	}
}
//END
//START_CP_ADMIN

/**
 * @return string[int][int]
 */
function get_zone_mgr()
{
	global $roleid, $uid;
	$zone_mgr_list=/*. (string[int][int]) .*/ array();
	$i=0;
	$query_zonal_manager="select e.GEM_EMP_ID,e.GEM_EMP_name from gft_emp_master e,gft_emp_manager_relation " .
			" where gmr_zone_m=gem_emp_id and GEM_STATUS='A' " ;
	if(is_authorized_group_list($uid,array(1))){
		$query_zonal_manager.=" ";
	}else if($roleid==7 or $roleid==19 or $roleid==23) {
		$query_zonal_manager.= " and gmr_zone_m='$uid' ";
	}else if(is_authorized_group_list($uid,array(5,6))){
		$query_zonal_manager.=" and gmr_emp_id=$uid ";
	}
	$query_zonal_manager.=" group by gem_emp_id order by gem_emp_name";
	$result_zonal_manager=execute_my_query($query_zonal_manager,'auth_util.php',true,false,2);
	if(mysqli_num_rows($result_zonal_manager)>0){
		if(!is_authorized_group($uid,0,array(1,2,3,4,5,6,7,11,19)) or is_authorized_group($uid,1)){
			$zone_mgr_list[$i][0]='0';
			$zone_mgr_list[$i][1]="ANY";
			$i++;
		}
		while($qdata=mysqli_fetch_array($result_zonal_manager))
		{
			 $zone_mgr_list[$i][0]=$qdata[0];
			 $zone_mgr_list[$i][1]=$qdata[1];
			 $i++;
		}
	}
	return $zone_mgr_list;	
}	

/**
 * @param string $zone_mgr_id
 * 
 * @return string[int][int]
 */
function get_regional_mgr($zone_mgr_id)
{
	global $roleid,$uid;
	$regional_mgr_list=/*. (string[int][int]) .*/ array();
	$i=0;
	$query_reg_mgr="select e.GEM_EMP_ID,e.GEM_EMP_name from gft_emp_master e,gft_emp_manager_relation " .
			" where gmr_region_m=gem_emp_id  and GEM_STATUS='A' and gmr_region_m_ck=true " ;
	$query_reg_mgr.= (($zone_mgr_id!='0' and $zone_mgr_id!='' )? " and gmr_zone_m=$zone_mgr_id ":"");
	if(is_authorized_group_list($uid,array(1))){
		$query_reg_mgr.=" ";
	}else if($roleid==6){
		$query_reg_mgr.= " and gmr_region_m='$uid' and gmr_region_m_ck=true ";
	}else if($roleid==7 or $roleid==19 or $roleid==23){
		$query_reg_mgr.="and gmr_zone_m='$uid'";
	}else if(is_authorized_group_list($uid,array(5,6))){
		$query_reg_mgr.=" and gmr_emp_id=$uid ";
	}
	$query_reg_mgr.=" group by gem_emp_id order by gem_emp_name";
	$result_regional_manager=execute_my_query($query_reg_mgr,'auth_util.php',true,false,2);
	if(mysqli_num_rows($result_regional_manager)>0){
		if(!is_authorized_group($uid,0,array(1,2,3,4,5,11,6)) or is_authorized_group($uid,1)){
			$regional_mgr_list[$i][0]='0';
			$regional_mgr_list[$i][1]="ANY";
			$i++;		
		}
		while($qdata=mysqli_fetch_array($result_regional_manager)){
			$regional_mgr_list[$i][0]=$qdata[0];
	  		$regional_mgr_list[$i][1]=$qdata[1];
			$i++;
		}
	}
	return $regional_mgr_list;	
}

/**
 * @param string $zone_mgr_id
 * @param string $regional_mgr_id
 * 
 * @return string[int][int]
 */
function get_area_mgr($zone_mgr_id,$regional_mgr_id)
{
	global $roleid,$uid;
	$area_mgr_list=/*. (string[int][int]) .*/ array();
	$i=0;
	$query_area_mgr="select e.GEM_EMP_ID,e.GEM_EMP_name from gft_emp_master e,gft_emp_manager_relation " .
			"where gmr_area_m=gem_emp_id  and GEM_STATUS='A' and gmr_area_m_ck=true " ;
	$query_area_mgr.= (($zone_mgr_id!='0' and $zone_mgr_id!='' )? " and gmr_zone_m=$zone_mgr_id ":"");
	$query_area_mgr.= (($regional_mgr_id!='0' and $regional_mgr_id!='' )? " and gmr_region_m=$regional_mgr_id ":"");
	if(is_authorized_group_list($uid,array(1))){
		$query_area_mgr.=" ";
	}else if($roleid==11) {
		$query_area_mgr.= " and gmr_area_m='$uid' and gmr_area_m_ck=true ";
	}else if($roleid==7 or  $roleid==6  or $roleid==19 or $roleid==23){
		$query_area_mgr.= " and ( gmr_zone_m=$uid or  gmr_region_m=$uid )";
	}else if(is_authorized_group_list($uid,array(5,6))){
		$query_area_mgr.=" and gmr_emp_id=$uid ";
	}	
	$query_area_mgr.=" group by gem_emp_id order by gem_emp_name";
	$result_area_manager=execute_my_query($query_area_mgr,'auth_util.php',true,false,2);
	if(mysqli_num_rows($result_area_manager)>0){
		if(!is_authorized_group($uid,0,array(1,2,3,4,5,11))){
			$area_mgr_list[$i][0]='0';
			$area_mgr_list[$i][1]="ANY";
			$i++;
		}
		while($qdata=mysqli_fetch_array($result_area_manager)){
				$area_mgr_list[$i][0]=$qdata[0];
				$area_mgr_list[$i][1]=$qdata[1];
				$i++;
		}
	}
	return $area_mgr_list;	
}

/**
 * @param string $zone_mgr_id
 * @param string $reg_mgr_id
 * @param string $terr_mgr_id
 * @param string $add_relieved_emp
 * 
 * @return string[int][int]
 */
function get_exec($zone_mgr_id,$reg_mgr_id,$terr_mgr_id,$add_relieved_emp)
{
	$i=0;
	global $uid;
	$exec_list=/*. (string[int][int]) .*/array();
	$query_exec="select e.GEM_EMP_ID,concat(e.gem_emp_name,if(gem_status='A',_latin1'',_latin1'*')) from gft_emp_master e,gft_emp_manager_relation " .
			" where gmr_emp_id=gem_emp_id  " ;
	$query_exec.= ($add_relieved_emp!='on'?" and GEM_STATUS='A'":"");
	$query_exec.= (($zone_mgr_id!='0' and $zone_mgr_id!='' )? " and gmr_zone_m=$zone_mgr_id ":"");
	$query_exec.= (($reg_mgr_id!='0' and $reg_mgr_id!='' )? " and gmr_region_m=$reg_mgr_id ":"");
	$query_exec.= (($terr_mgr_id!='0' and $terr_mgr_id!='' )? " and gmr_area_m=$terr_mgr_id ":"");
	$team_list_count=0;
	if(!is_authorized_group($uid,1) and is_authorized_group_list($uid,array(8,5))){
			$team_list=get_team_list($uid);
			$team_list_count=count($team_list);
			if($team_list_count==1){
				$exec_id=$uid;
			}
	}
	global $default_exec_skip_for_SSE;
	/* Avoid employee default set for sales and support group */
	$add_chk=false;
	if($default_exec_skip_for_SSE==true){
		$add_chk=is_authorized_group_list($uid,array(6,7)) ;
	}
	if($team_list_count ==1 and $add_chk==false and is_authorized_group_list($uid,null,array(1,2,3))){
		$query_exec.= " and gmr_emp_id='$uid' ";
	}else if(is_authorized_group_list($uid,array(5,6)) and !is_authorized_group_list($uid,array(1,20,11,12)) and $add_chk==false ){
		/*sales and support group and not of admin ,techsupport,dev,buss dev */	
		$query_exec.= " and (gmr_emp_id='$uid' or (gmr_terri_m='$uid' and gmr_terri_m_ck=true) " .
			" or (gmr_area_m='$uid' and gmr_area_m_ck=true) " .
			" or (gmr_region_m='$uid' and gmr_region_m_ck=true) " .
			" or  (gmr_zone_m='$uid' ) )";
	}
	$query_exec.=" group by gem_emp_id order by gem_emp_name";
	$result_exec=execute_my_query($query_exec,'auth_util.php',true,false,2);
    if(mysqli_num_rows($result_exec)>0){
    	if(!is_authorized_group($uid,0,array(1,2,3,4)) or mysqli_num_rows($result_exec)>1){	
			$exec_list[$i][0]='0';
			$exec_list[$i][1]="ANY";
			$i++;	    
	    }
	    while($qdata=mysqli_fetch_array($result_exec)){
			$exec_list[$i][0]=$qdata[0];
			$exec_list[$i][1]=$qdata[1];
			$i++;
		}
    }
	return $exec_list;
}

/**
 * @param int $state_id
 * @param string $state_name
 * @param boolean $default_select
 * 
 * @return string[int][int]
 */
function get_district($state_id,$state_name=null,$default_select=true){
	global $uid;
	$i=0;	
	$district_list= /*. (string[int][int]) .*/ array();

	if($default_select==true){
		$district_list[0][0]='0';
    	$district_list[0][1]="ANY";
    	$i++;
	}
    //if(is_array($state_id)){
    //	$state_id=implode(',',$state_id);
    //}
    if(is_authorized_group_list($uid,array(5,6))  and !is_authorized_group_list($uid,array(8)) and !is_authorized_group($uid,1)){	
	 	$query_dist= " select district_id,district FROM p_map_view ".
	 			" left join b_p_map_view  on ( district_id=GPM_DISTRICT_ID) " .
	 			" join b_map_view b  on (GPM_TERRITORY_ID=b.terr_id)" .
				" join gft_emp_territory_dtl on (get_territory_id=b.terr_id) ".
		 		" where GET_STATUS='A' and ((get_work_area_type=5 and b.zone_id=get_territory_id) or " .
		 		" (get_work_area_type=4 and  b.region_id=get_territory_id) or" .
		 		" (get_work_area_type=3 and  b.area_id=get_territory_id ) or" .
		 		" (get_work_area_type=2 and  b.terr_id=get_territory_id )) and district_id is not null " .
		 		" and get_emp_id=$uid ".($state_id!=0 ? " and state_id in ($state_id) ":"") .
			 	" group by district_id "; 
    }else {
		$query_dist="SELECT district_id,district FROM p_map_view  where (1) ".($state_id!=0?" and state_id in ($state_id) ":"") .
			(($state_name!=null or $state_name!='' )? " and state ='$state_name'  ":"") ;	 
	}
    if($query_dist!=''){
		$result_dist=execute_my_query($query_dist,'auth_util.php',true,false,2);
		
		if(mysqli_num_rows($result_dist)>0){
			while($qdata=mysqli_fetch_array($result_dist)){
				$district_list[$i][0]=$qdata[0];
				$district_list[$i][1]=$qdata[1];
				$i++;
			}
		}
    }
	return $district_list;
}

/**
 * @param string $country_id
 * @param boolean $default_select
 * 
 * @return string[int][int]
 */
function get_state($country_id,$default_select=true){
	global $uid;
	
	$query_state="SELECT GPM_MAP_ID,GPM_MAP_NAME FROM `gft_political_map_master`  where GPM_MAP_TYPE='S' ".
	($country_id!=''? "and gpm_map_parent_id='$country_id'":"")." order by GPM_MAP_NAME ";
	if(is_authorized_group_list($uid,array(5,6))  and !is_authorized_group_list($uid,array(8)) and !is_authorized_group($uid,1)){
		$query_state=" select state_id as GPM_MAP_ID,state as GPM_MAP_NAME FROM p_map_view ".
	 			" left join b_p_map_view  on ( district_id=GPM_DISTRICT_ID) " .
	 			" left join b_map_view b  on (GPM_TERRITORY_ID=b.terr_id)" .
				" left join gft_emp_territory_dtl on ((get_work_area_type=5 and b.zone_id=get_territory_id) or" .
				" (get_work_area_type=4 and  b.region_id=get_territory_id) or" .
				" (get_work_area_type=3 and  b.area_id=get_territory_id ) or" .
				" (get_work_area_type=2 and  b.terr_id=get_territory_id )) ".
		 		" where GET_STATUS='A' and get_emp_id=$uid and district_id is not null " .
		 		"  group by state_id ";
	}
		
	$result_state=execute_my_query($query_state,'auth_util.php',true,false,2);
	$i=0;
	$state_list=/*. (string[int][int]) .*/ array();
	if(mysqli_num_rows($result_state)>0){
		if($default_select==true ){
			$state_list[$i][0]='0';
			$state_list[$i][1]="ANY";
			$i++;
		}
		while($qdata=mysqli_fetch_array($result_state)){
			$state_list[$i][0]=$qdata['GPM_MAP_ID'];
			$state_list[$i][1]=$qdata['GPM_MAP_NAME'];
			$i++;
		}
	}
	return $state_list;
}	

/**
 * @param boolean $show_other
 * @param boolean $all
 * @param boolean $default_select
 * 
 * @return string[int][int]
 */
function get_country($show_other=true,$all=false,$default_select=true){
	global $uid;

	$country_list=/*. (string[int][int]) .*/ array();

	if($all==true){
		$i=0;
		if($default_select==true){
			$country_list[$i][0]='0';
			$country_list[$i][1]="ANY";
			$i++;
		}
		$query_country="SELECT GPM_MAP_ID, GPM_MAP_NAME, GPM_COUNTRY_CODE, GPM_COUNTRY_CODE_2 " .
				" FROM gft_political_map_master  where GPM_MAP_STATUS='A' and GPM_MAP_TYPE='C' and GPM_MAP_ID>1 and GPM_MAP_LEVEL=3 and GPM_MAP_PARENT_ID=1 " ;
		if($show_other==false) $query_country.=" and gpm_map_id!=34 ";
		$query_country.=" order by gpm_map_name ";
		$result_country=execute_my_query($query_country,'auth_util.php',true,false,2);
		if(mysqli_num_rows($result_country)>0){
			while($qdata=mysqli_fetch_array($result_country)){
				$country_list[$i][0]=$qdata['GPM_MAP_ID'];
				$country_list[$i][1]=$qdata['GPM_MAP_NAME'];
				$country_list[$i][2]=$qdata['GPM_COUNTRY_CODE'];
				$country_list[$i][3]=$qdata['GPM_COUNTRY_CODE_2'];
				$i++;
			}
		}
	}else{
		if(is_authorized_group_list($uid,array(5,6)) and !is_authorized_privilege_group($uid, 12)){
			$i=0;
			$country_list[$i][0]='2';
			$country_list[$i][1]="INDIA";
			$i++;
		}else{
			$i=0;
			$country_list[$i][0]='0';
			$country_list[$i][1]="ANY";
			$i++;
			$country_list[$i][0]='-1';
			$country_list[$i][1]="Overseas";
			$i++;
			$query_country="SELECT * FROM gft_political_map_master  where GPM_MAP_TYPE='C' and GPM_MAP_ID>1 and GPM_MAP_PARENT_ID=1 " ;
			if($show_other==false) $query_country.=" and gpm_map_id!=34 ";
			$query_country.=" order by gpm_map_name ";
			$result_country=execute_my_query($query_country,'auth_util.php',true,false,2);
			while($qdata=mysqli_fetch_array($result_country)){
				$country_list[$i][0]=$qdata[0];
				$country_list[$i][1]=$qdata[1];
				$i++;
			}
		}
	}
	return $country_list;
}

/**
 * @param int $territory_id
 * @param string $emp_id
 * 
 * @return boolean
 */
function is_territory_accessable($territory_id,$emp_id){
	$query="select terr_id " .
			" FROM  gft_emp_territory_dtl " .
			" left join gft_business_territory_master bt1 on(get_work_area_type=2 and bt1.gbt_territory_id=get_territory_id and GET_STATUS='A') " .
			" join b_map_view b on ((" .
			" (get_work_area_type=2 and b.area_id =bt1.gbt_map_id) or " .
			" (get_work_area_type=3 and b.area_id=get_territory_id) or " .
			" (get_work_area_type=4 and b.region_id=get_territory_id) or" .
			" (get_work_area_type=5 and b.zone_id=get_territory_id) " .
			" )and GET_STATUS='A') where get_emp_id=$emp_id and terr_id=$territory_id ";
	$result_terri=execute_my_query($query);
	if(mysqli_num_rows($result_terri)>0){
		return true;
	}else{
		return false;
	}
}

/**
 * @param int $uid
 * @param int $zone_id
 * @param int $region_id
 * @param int $area_id
 * 
 * @return string[int][int]
 */
function get_territory($uid,$zone_id,$region_id,$area_id){
    if(is_authorized_group_list($uid,array(2,3,4,102)) and !is_authorized_group_list($uid,array(1,8)) ){
		$query_terr="select terr_id, terr FROM  gft_emp_territory_dtl,b_map_view b" .
			" where  GET_STATUS='A' and gbt_status='A' and ((get_work_area_type=5 and b.zone_id=get_territory_id) " .
			" or (get_work_area_type=4 and  b.region_id=get_territory_id) " .
			" or (get_work_area_type=3 and  b.area_id=get_territory_id ) " .
			" or (get_work_area_type=2 and  b.terr_id=get_territory_id ))" .
			" and get_emp_id='$uid' " .
			(($zone_id!=0 )?" and zone_id=$zone_id ":"").
			(($region_id!=0 )?" and region_id=$region_id ":""). 
			(($area_id!=0)?" and area_id=$area_id ":""). 
			" group by terr order by terr ";
	}else{
		$query_terr="SELECT b.terr_id, b.terr from b_map_view b where gbt_status='A' ".
				(($zone_id!=0 )?" and zone_id=$zone_id ":"").
				(($region_id!=0 )?" and region_id='$region_id'":""). 
				(($area_id!=0 )?" and area_id='$area_id'":"").
				" order by terr ";
	}
	$result_terr=execute_my_query($query_terr,'auth_util.php',true,false,2);
	$i=0;
	$territory_list[$i][0]='0';
	$territory_list[$i][1]="ANY";
	$i++;
	if(mysqli_num_rows($result_terr)>0){
		while($qdata=mysqli_fetch_array($result_terr)){
			$territory_list[$i][0]=$qdata[0];
	  		$territory_list[$i][1]=$qdata[1];
	  		$i++;
		}
	}
	return $territory_list;		
}

/**
 * @param int $uid
 * @param int $region_id
 * @param boolean $select_any
 * 
 * @return string[int][int]
 */
function get_area_in($uid,$region_id,$select_any=true){

	$region_list=/*. (string[int][int]) .*/ array();

	$i=0;
	if(is_authorized_group_list($uid,array(2,3,4,102)) and !is_authorized_group_list($uid,array(8)) and !is_authorized_group($uid,1)){
		$query_region="select area_id, area FROM  gft_emp_territory_dtl,b_map_view b" .
			" where GET_STATUS='A' and ((get_work_area_type=5 and b.zone_id=get_territory_id) " .
			" or (get_work_area_type=4 and  b.region_id=get_territory_id) " .
			" or (get_work_area_type=3 and  b.area_id=get_territory_id ) " .
			" or (get_work_area_type=2 and  b.terr_id=get_territory_id ))" .
			" and get_emp_id='$uid' " .
			(($region_id!=0)?" and region_id=$region_id ":"") ." group by area_id order by area ";
	}else{
		$query_region="SELECT b.area_id, b.area from b_map_view b where (1) ".
				(($region_id!=0 )?" and region_id=$region_id ":"").
				"group by area_id order by area ";
	}
	if($query_region!=''){
  
        $result_region=execute_my_query($query_region,'auth_util.php',true,false,2);
        $count_num_rows=mysqli_num_rows($result_region);
		if($count_num_rows>1 && $select_any==true){	
           	$region_list[$i][0]='0';
			$region_list[$i][1]="ANY";
			$i++;
        }
        while($qdata=mysqli_fetch_array($result_region)){
				$region_list[$i][0]=$qdata[0];
				$region_list[$i][1]=$qdata[1];
	  			$i++;
		}
        
	}
	return $region_list;
}

/**
 * @param int $uid
 * @param int $zone_id
 * @param boolean $select_any
 * 
 * @return string[int][int]
 */
function get_region($uid,$zone_id,$select_any=true){
	$region_list=/*. (string[int][int]) .*/ array();
	$i=0;
	if(is_authorized_group_list($uid,array(2,3,4,102)) and !is_authorized_group_list($uid,array(8)) and !is_authorized_group($uid,1)){
		$query_region="select region_id, region FROM  gft_emp_territory_dtl,b_map_view b" .
			" where GET_STATUS='A' and ((get_work_area_type=5 and b.zone_id=get_territory_id) " .
			" or (get_work_area_type=4 and  b.region_id=get_territory_id) " .
			" or (get_work_area_type=3 and  b.area_id=get_territory_id ) " .
			" or (get_work_area_type=2 and  b.terr_id=get_territory_id ))" .
			" and get_emp_id='$uid' " .
			(($zone_id!=0 )?" and zone_id=$zone_id ":"") ." group by region_id order by region ";
	}else{
		$query_region="SELECT b.region_id, b.region from b_map_view b where (1) ".
				(($zone_id!=0 )?" and zone_id=$zone_id ":"").
				"group by region_id order by region ";
	}
	if($query_region!=''){
        $result_region=execute_my_query($query_region,'auth_util.php',true,false,2);
        $count_num_rows=mysqli_num_rows($result_region);
		if($count_num_rows>1 && $select_any==true){
           	$region_list[$i][0]='0';
			$region_list[$i][1]="ANY";
			$i++;
        }
        while($qdata=mysqli_fetch_array($result_region)){
				$region_list[$i][0]=$qdata[0];
				$region_list[$i][1]=$qdata[1];
	  			$i++;
		}
        
	}
	return $region_list;
}

/**
 * @param int $uid
 * 
 * @return string[int][int]
 */
function get_zone($uid){
	$zone_list=/*. (string[int][int]) .*/ array();
	$i=0;
	if(is_authorized_group_list($uid,array(2,3,4,102)) and !is_authorized_group_list($uid,array(8)) and !is_authorized_group($uid,1) ){
		$query_zone=" select zone_id, zone FROM gft_emp_territory_dtl,b_map_view b" .
			" where GET_STATUS='A' and ((get_work_area_type=5 and b.zone_id=get_territory_id) " .
			" or (get_work_area_type=4 and  b.region_id=get_territory_id) " .
			" or (get_work_area_type=3 and  b.area_id=get_territory_id ) " .
			" or (get_work_area_type=2 and  b.terr_id=get_territory_id ))" .
			" and get_emp_id='$uid' group by zone_id ";
	}else{
		$query_zone="SELECT b.zone_id, b.zone from b_map_view b group by zone_id order by 2 ";
	}

	$result_zone=execute_my_query($query_zone,'auth_util.php',true,false,2);
	$count_num_rows=mysqli_num_rows($result_zone);
	if($count_num_rows>1){	
		$zone_list[$i][0]='0';
		$zone_list[$i][1]="ANY";
		$i++;
    }
	while($qdata=mysqli_fetch_array($result_zone)){
	  		$zone_list[$i][0]=$qdata[0];
	  		$zone_list[$i][1]=$qdata[1];
			$i++;
	}
	
	return $zone_list;
}//End of zone fn

/**
 * @param boolean $default_select
 * 
 * @return string[int][int]
 */
function get_partner_available_in_country($default_select=true){
	
$query=<<<END
		select GPM_MAP_ID, GPM_MAP_NAME, GPM_COUNTRY_CODE, GPM_COUNTRY_CODE_2  
		from gft_emp_master em1 
		join gft_cp_info cp on (CGI_EMP_ID=em1.gem_emp_id and (CGI_STATUS='10' OR (CGI_STATUS=14 AND CGI_STATUS_TILL_DATE<=date(now()) ) ) )
		join gft_lead_hdr lh on (lh.GLH_LEAD_CODE=CGI_LEAD_CODE ) 
		join gft_political_map_master  pm on (GPM_MAP_TYPE='C' and GPM_MAP_NAME=GLH_COUNTRY)
		where em1.gem_role_id ='21' and CGI_EMP_ID!=7004 
		and em1.gem_status='A' group by GPM_MAP_ID order by GPM_MAP_NAME 
END;

	$result_country=execute_my_query($query,'auth_util.php',true,false,2);
	$i=0;$country_list=/*. (string[int][int]) .*/ array();
	if($default_select==true){
		$country_list[$i][0]='0';
		$country_list[$i][1]="ANY";
		$i++;
	}
		if(mysqli_num_rows($result_country)>0){
			while($qdata=mysqli_fetch_array($result_country)){
				$country_list[$i][0]=$qdata['GPM_MAP_ID'];
				$country_list[$i][1]=$qdata['GPM_MAP_NAME'];
				$country_list[$i][2]=$qdata['GPM_COUNTRY_CODE'];
				$country_list[$i][3]=$qdata['GPM_COUNTRY_CODE_2'];
				$i++;
			}
		}
	return $country_list;
}//end of function 

/**
 * @param boolean $default_select
 * 
 * @return string[int][int]
 */
function get_ref_customer_available_in_country($default_select=true){
call_mysql_READ_UNCOMMITTED();
$query_slow=<<<END
		select GPM_MAP_ID, GPM_MAP_NAME, GPM_COUNTRY_CODE, GPM_COUNTRY_CODE_2  
		from gft_lead_hdr lh 
		join gft_install_dtl_new on(lh.glh_lead_code=gid_lead_code and GID_STATUS='A' )
		join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW and GPM_FREE_EDITION='N' and gpm_status='A')  
		join gft_political_map_master  pm on (GPM_MAP_TYPE='C' and GPM_MAP_NAME=GLH_COUNTRY)
		where lh.GLH_REFERENCE_CUST='Y' and glh_lead_type!=8  
		group by GPM_MAP_ID order by GPM_MAP_NAME
END;
$query=<<<END
	select GPM_MAP_ID,GPM_MAP_NAME,GPM_COUNTRY_CODE,GPM_COUNTRY_CODE_2 from ref_cust_view5
	join gft_political_map_master  pm on (GPM_MAP_TYPE='C' and GPM_MAP_NAME=glh_country)
	group by GPM_MAP_ID
END;
	
	$result_country=execute_my_query($query,'auth_util.php',true,false,2);
	$i=0;
	$country_list=/*. (string[int][int]) .*/ array();
	if($default_select==true){
	$country_list[$i][0]='0';
	$country_list[$i][1]="ANY";
	$i++;
	}
		if(mysqli_num_rows($result_country)>0){
			while($qdata=mysqli_fetch_array($result_country)){
				$country_list[$i][0]=$qdata['GPM_MAP_ID'];
				$country_list[$i][1]=$qdata['GPM_MAP_NAME'];
				$country_list[$i][2]=$qdata['GPM_COUNTRY_CODE'];
				$country_list[$i][3]=$qdata['GPM_COUNTRY_CODE_2'];
				$i++;
			}
		}
	return $country_list;
}//end of function 

/**
 * @param int $country_code
 * @param boolean $default_select
 * 
 * @return string[int][int]
 */
function get_partner_available_in_states($country_code,$default_select=true){
	call_mysql_READ_UNCOMMITTED();
$query=<<<END
		select state_id,state  
		from gft_emp_master em1 
		join gft_cp_info cp on (CGI_EMP_ID=em1.gem_emp_id and (CGI_STATUS='10' OR (CGI_STATUS=14 AND CGI_STATUS_TILL_DATE<=date(now()) ) ) )
		join gft_lead_hdr lh on (lh.GLH_LEAD_CODE=CGI_LEAD_CODE ) 
		join gft_pincode_master pim on (GPM_PINCODE=GLH_CUST_PINCODE)
		join p_map_view pv on (district_id=gpm_district_id and country_id=$country_code)
		where em1.gem_role_id ='21' and CGI_EMP_ID!=7004 
		and em1.gem_status='A' 
		group by state_id 
		order by state  
END;

	$result=execute_my_query($query,'auth_util.php',true,false,1);
	$state_list=/*. (string[int][int]) .*/ array();
	$i=0;
	if($default_select==true){
		$state_list[0][0]='0';
		$state_list[0][1]='Any';
		$i++;
	}
	if(mysqli_num_rows($result)>0){
		while($qdata=mysqli_fetch_array($result)){
			$state_list[$i][0]=$qdata['state_id'];
			$state_list[$i][1]=$qdata['state'];
			$i++;
		}
	}
	return $state_list;
}//end of function

/**
 * @param int $country_code
 * @param boolean $default_select
 * 
 * @return string[int][int]
 */
function get_ref_customer_available_in_states($country_code,$default_select=true){
	call_mysql_READ_UNCOMMITTED();
$query_slow=<<<END
		select state_id,state  
		from gft_lead_hdr lh  
		join gft_install_dtl_new on(lh.glh_lead_code=gid_lead_code and GID_STATUS='A')
		join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW and GPM_FREE_EDITION='N' and gpm_status='A') 
		join gft_pincode_master pim on (GPM_PINCODE=GLH_CUST_PINCODE)
		join p_map_view pv on (district_id=gpm_district_id and country_id=$country_code)
		where lh.GLH_REFERENCE_CUST='Y' and glh_lead_type!=8 		
		group by state_id 
		order by state  
END;
$query=<<<END
	select state_id,state from ref_cust_view5 where not isnull(state_id) and country_id=$country_code 
	group by state_id order by state 
END;

	$result=execute_my_query($query,'auth_util.php',true,false,1);
	$state_list=/*. (string[int][int]) .*/ array();
	$i=0;
	if($default_select==true){
		$state_list[0][0]='0';
		$state_list[0][1]='Any';
		$i++;
	}
	
	if(mysqli_num_rows($result)>0){
		while($qdata=mysqli_fetch_array($result)){
			$state_list[$i][0]=$qdata['state_id'];
			$state_list[$i][1]=$qdata['state'];
			$i++;
		}
	}
	return $state_list;
}//end of function

/**
 * @param int $country_code
 * @param int $state_code
 * @param boolean $default_select
 * 
 * @return string[int][int]
 */
function get_partner_available_in_district($country_code,$state_code,$default_select=true){
$query=<<<END
 select district_id,district   
 from gft_emp_master em1 
 join gft_cp_info cp on ( CGI_EMP_ID=em1.gem_emp_id and (CGI_STATUS=10 OR (CGI_STATUS=14 AND CGI_STATUS_TILL_DATE<=date(now()) ) )  )
 join gft_lead_hdr lh on (lh.GLH_LEAD_CODE=CGI_LEAD_CODE ) 
 join gft_pincode_master pim on (GPM_PINCODE=GLH_CUST_PINCODE)
 join p_map_view pv on (district_id=gpm_district_id and country_id=$country_code and state_id=$state_code)
 where em1.gem_role_id ='21' 
 and CGI_EMP_ID!=7004 and em1.gem_status='A' 
 group by district_id  order by district 
END;

	$city_result=execute_my_query($query);
	$district_list=/*. (string[int][int]) .*/ array();
	$i=0;
	if($default_select==true){
		$district_list[0][0]='0';
    	$district_list[0][1]="ANY";
    	$i++;
	}
	if(mysqli_num_rows($city_result)>0){
		while($qdata=mysqli_fetch_array($city_result)){
			$district_list[$i][0]=$qdata[0];
			$district_list[$i][1]=$qdata[1];
			$i++;
		}
	}
	return $district_list;	
} 

/**
 * @param int $country_code
 * @param int $state_code
 * @param boolean $default_select
 * 
 * @return string[int][int]
 */
function get_ref_customer_available_in_district($country_code,$state_code,$default_select=true){
	call_mysql_READ_UNCOMMITTED();
$query_slow=<<<END
 select district_id,district   
 from gft_lead_hdr lh 
 join gft_install_dtl_new on(lh.glh_lead_code=gid_lead_code and GID_STATUS='A')
 join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW and GPM_FREE_EDITION='N' and gpm_status='A') 
 join gft_pincode_master pim on (GPM_PINCODE=GLH_CUST_PINCODE)
 join p_map_view pv on (district_id=gpm_district_id and country_id=$country_code and state_id=$state_code)
 where lh.GLH_REFERENCE_CUST='Y' and glh_lead_type!=8 
 group by district_id  order by district 
END;

$query=<<<END
	select district_id,district from ref_cust_view5 where country_id=$country_code and state_id=$state_code 
	group by district_id  order by district 
END;
 

	$city_result=execute_my_query($query);
	$district_list=/*. (string[int][int]) .*/ array();
	$i=0;
	if($default_select==true){
		$district_list[0][0]='0';
	    $district_list[0][1]="ANY";
		$i++;
	}
	if(mysqli_num_rows($city_result)>0){
		while($qdata=mysqli_fetch_array($city_result)){
			$district_list[$i][0]=$qdata[0];
			$district_list[$i][1]=$qdata[1];
			$i++;
		}
	}
	return $district_list;	
} 
/**
 * @param string $uid
 * @param string $lead_code
 * @param string $opcode
 * @param string $extend_date
 * @param string $expiry_date
 * @param string $comments
 * @param int $notify_type Type of Notification to be sent (1 for email and 2 for MyDelight Notification)
 * @param string $request_id If processing a coupon extension request from MyDelight 
 * @return boolean
 */
function update_coupon_status($uid,$lead_code,$opcode,$extend_date,$expiry_date,$comments,$notify_type=1,$request_id='') { 
	$update_success = false;
	$comments =	mysqli_real_escape_string_wrapper($comments);
	$order_no =	substr($opcode,0,15);
	$date_now =	date("Y-m-d H:i:s");
	$sql_extend_date	=	" INSERT INTO gft_product_delivery_log(GPD_LEAD_CODE,GPD_ORDER_NO,GPD_OLD_EXPIRY_DT,GPD_NEW_EXPIRY_DT,GPD_EXTENDED_ON,GPD_EXTENDED_BY,GPD_EMP_COMMENTS) ".
			" VALUES('$lead_code','$order_no','$expiry_date','$extend_date','$date_now','$uid','$comments')";
	$update_coupon_date	=	"update gft_coupon_distribution_dtl SET GCD_EXPIRY_DATE='$extend_date' where GCD_TO_ID=$lead_code and GCD_ORDER_NO='$opcode'";
	if(exists_in_lead_hdr_ext($lead_code)){
		execute_my_query("update gft_lead_hdr_ext set GLE_PD_AGE='$extend_date' where GLE_LEAD_CODE='$lead_code'");
	}else{
		execute_my_query("insert into gft_lead_hdr_ext (GLE_LEAD_CODE,GLE_PD_AGE) values ('$lead_code','$extend_date')");
	}
	if(execute_my_query($update_coupon_date)){
		execute_my_query($sql_extend_date);
		$log_id = mysqli_insert_id_wrapper();
		$sql_coupon_expiry_dtl = " select count(GCD_COUPON_NO) as no_of_coupon, group_concat(GCD_COUPON_NO) as coupon_list, GCD_GIVEN_DATE,GCD_EXPIRY_DATE, ".
				" GEM_EMP_NAME,GCD_TO_ID,GCD_ORDER_NO from gft_coupon_distribution_dtl inner join gft_emp_master em on(em.gem_emp_id=GCD_HANDLED_BY) ".
				" where  GCD_TO_ID=$lead_code and GCD_ORDER_NO='$opcode' and GCD_SIGNED_OFF='N' group by GCD_ORDER_NO, GCD_TO_ID";
		$res_coupon_expiry_dtl	=	execute_my_query($sql_coupon_expiry_dtl);
		if(mysqli_num_rows($res_coupon_expiry_dtl)!=0){
			while($row=mysqli_fetch_array($res_coupon_expiry_dtl)){
				$order_no =	substr($row['GCD_ORDER_NO'], 0,15);
				$cust_dtl = customerContactDetail_Mail($lead_code,$order_no);
				$no_of_coupon =	$row['no_of_coupon'];
				$coupon_list  =	$row['coupon_list'];
				$given_date	  =	$row['GCD_GIVEN_DATE'];
				$expity_date  =	$row['GCD_EXPIRY_DATE'];
				$given_by	  =	$row['GEM_EMP_NAME'];
				$sales_emp_name	= 9999;
				if(is_authorized_group_list($cust_dtl['Order_By_Whom'], array (54))){$sales_emp_name=$cust_dtl['Order_By_Whom']; }else{$sales_emp_name= $cust_dtl['LFD_EMP_ID']; }
				$other_cc =	array($cust_dtl['Reg_incharge'],$sales_emp_name,$cust_dtl['Field_incharge'],$uid);
				if(isPartnerEmployee((int)$cust_dtl['Field_incharge'])){
					$other_cc =	array_merge(/*. (string[int]) .*/$other_cc,/*. (string[int]) .*/getPartnerIdBusinessManagerId($order_no));
				}
				if(isPartnerEmployee((int)$sales_emp_name)){
					$other_cc =	array_merge(/*. (string[int]) .*/$other_cc,/*. (string[int]) .*/getPartnerIdBusinessManagerId($order_no));
				}
				$help_us_help_you =	get_samee_const("Help_Us_Help_You");
				$pc_name_arr = get_emp_master($cust_dtl['Field_incharge'],$status='',$roleid=null,$only_gft_emp=false,$select_any=false);
				$incharge_email = 'training@gofrugal.com';
				$other_cc = array_merge($other_cc, get_email_addr_reportingmaster($cust_dtl['Field_incharge'],true,explode(",", EMP_IDS_TO_SKIP_REPORTING_MAIL)));
				$pc_name = $pc_name_arr[0][1];
				$pc_mobile = $pc_name_arr[0][3];
				$pc_mail = $pc_name_arr[0][4];
				$emp_name_arr = get_emp_master($uid,$status='',$roleid=null,$only_gft_emp=false,$select_any=false);
				$emp_name = $emp_name_arr[0][1];
				$emp_mobile = $emp_name_arr[0][3];
				$emp_role =	$emp_name_arr[0][7];
				$db_cust_mail_content_config=array(
						'Customer_Id'=>array($lead_code),
						'Customer_Name'=>array($cust_dtl['cust_name']),
						'Coupon_Details'=>array($coupon_list),
						'Current_Expiry_Date'=>array($expity_date),
						'PC_Name'=>array($pc_name),
						'PC_Mobile'=>array($pc_mobile),
						'PC_Email'=>array($pc_mail),
						'Help_Us_Help_You'=>array($help_us_help_you),
						'Employee_Name'=>array($emp_name),
						'Employee_Role'=>array($emp_role),
						'Mobile'=>array($emp_mobile)
				);
				if($notify_type==1) {
					send_formatted_mail_content($db_cust_mail_content_config,55,199,null,array($lead_code),null,$other_cc,explode(',',$incharge_email));
				}
				$to_id = get_single_value_from_single_table("GPE_EMP_ID", "gft_pd_extension_request", "GPE_ORDER_REF", $opcode);
				if($to_id=='') {
					$to_id = $cust_dtl['Field_incharge'];
				}
				if($log_id!='') {
					if($request_id!='') {
						execute_my_query("update gft_pd_extension_request set gpe_status='0', gpe_log_id='$log_id' where gpe_id='$request_id'");
					} else {
						execute_my_query("update gft_pd_extension_request set gpe_status='0', gpe_log_id='$log_id' where GPE_ORDER_REF='$opcode' and gpe_status='1'");
					}
				}
				$db_noti_content_config = array(
						'Employee_Name'=>array($emp_name),
						'PC_Name'=>array($pc_name),
						'Customer_Id'=>array($lead_code),
						'Customer_Name'=>array($cust_dtl['cust_name']),
				);
				send_formatted_notification_content($db_noti_content_config,55,80,1,$to_id);
			}
			$update_success = true;
		}
	}
	return $update_success;
}

/**
 * @param int $emply_id
 * @param string $pcode
 * @param string $pgroup
 * 
 * @return void
 */
function is_service_user_login_allowed($emply_id,$pcode,$pgroup){
	$pid = $pcode.'-'.$pgroup;
	$sql1 = " select GEM_EMP_ID,GSU_EMPLOYEE_ID from gft_service_user_restriction ".
			" left join gft_emp_master on (WEB_GROUP=GSU_TEAM_ID and GSU_TEAM_ID!=0 and GEM_STATUS='A') ".
			" where GSU_PRODUCT_ID='$pid' ";
	$sql_res = execute_my_query($sql1);
	$goact_skip_auth_validation = get_samee_const("SKIP_GOACT_PRODUCT_AUTH");
	if($pcode=='803' && $goact_skip_auth_validation){
	    return true; //no restriction for this product code when $goact_skip_auth_validation has value 1
	}
	if(mysqli_num_rows($sql_res)==0){
		return true; //no restriction for this product code and group
	}
	$allowed_emp_id_arr = /*. (int[int]) .*/ array();
	while ($sql_row = mysqli_fetch_array($sql_res)){
		$allowed_emp_id_arr[] = (int)$sql_row['GEM_EMP_ID'];
		$allowed_emp_id_arr[] = (int)$sql_row['GSU_EMPLOYEE_ID'];
	}
	if(in_array($emply_id, $allowed_emp_id_arr)){
		return true;
	}
	return false;
}

/**
 * @param int $emply_id
 * @param string $pcode
 * @param string $pgroup
 * @param int $authorize_for
 *
 * @return boolean
 */
function is_user_authorized($emply_id,$pcode,$pgroup,$authorize_for){
    $pid = $pcode.'-'.$pgroup;
    $que1 = " select GUA_EMP_ID from gft_user_authorization where GUA_STATUS=1 ".
            " and GUA_EMP_ID='$emply_id' and GUA_PRODUCT_ID='$pid' and GUA_AUTH_FOR='$authorize_for' ";
    $res1 = execute_my_query($que1);
    if(mysqli_num_rows($res1) > 0){
        return true;
    }
    return false;
}

?>
