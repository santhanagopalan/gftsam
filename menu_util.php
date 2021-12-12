<?php

require_once(__DIR__ ."/dbcon.php");

/**
 * @param string $cp_lcode
 * 
 * @return string
 */
function raymedi_releases($cp_lcode){
$today_date=date('Y-m-d');
global $uid;
//$uid=$_SESSION['uid'];

if(is_authorized_group_list($uid,array(13,14))){
	$innerquery= " (SELECT GPV_PRODUCT_CODE,GPM_desc,GPV_MAJOR_VERSION,gpv_version_family " .
 		" FROM gft_product_family_master,gft_product_group_master pg,gft_product_version_master, " .
 		"(SELECT DISTINCT GOP_PRODUCT_CODE FROM gft_order_hdr,gft_order_product_dtl " .
 		" WHERE GOD_ORDER_NO=GOP_ORDER_NO AND GOD_LEAD_CODE='$cp_lcode' ) as al " .
 		" where GPM_HEAD_FAMILY=GPV_PRODUCT_CODE and GPV_PRODUCT_CODE=al.GOP_PRODUCT_CODE and GPM_PRODUCT_CODE!=1 " .
 		" and pg.gpg_product_family_code=GPM_HEAD_FAMILY and pg.gpg_skew=gpv_version_family " .
 		" group by GPV_PRODUCT_CODE, pg.gpg_skew) as T1 ";
}
else{
	$innerquery=" (SELECT GPV_PRODUCT_CODE,GPM_DESC,GPM_PRODUCT_ABR,GPV_MAJOR_VERSION,gpv_version_family " .
			" FROM gft_product_family_master,gft_product_version_master,gft_product_group_master pg " .
			" where GPM_HEAD_FAMILY=GPV_PRODUCT_CODE and GPM_PRODUCT_CODE!=1 AND GPM_HEAD_FAMILY=GPM_PRODUCT_CODE " .
			" and pg.gpg_product_family_code=GPM_HEAD_FAMILY and pg.gpg_skew=gpv_version_family " .
			" group by GPV_PRODUCT_CODE, pg.gpg_skew ) as T1 ";
}

$sql1="select GPM_DESC,T2.GPV_BASE_VERSION,versionA,GPV_DOWNLOAD_HLINK,GPV_DOWNLOAD_HLINKPH, " .
		" GPV_PRODUCT_STATUS,GPV_SPECIAL_TOOL, GPV_IS_SPECIAL, GPV_CSP_PATCH, " .
		" date_format(GPV_RELEASE_DATE,'%d-%m-%Y'),GPV_RELEASE_DATE,T1.GPV_PRODUCT_CODE from " .
		" $innerquery " .
		" inner join (SELECT GPV_PRODUCT_CODE,GPV_MAJOR_VERSION, " .
		" CONCAT(GPV_MAJOR_VERSION,'.',GPV_MINOR_VERSION,'.',GPV_PATCH_VERSION,'.',GPV_EXE_VERSION) as versionA, " .
 		" GPV_BASE_VERSION,GPV_DOWNLOAD_HLINK,GPV_DOWNLOAD_HLINKPH,GPV_RELEASE_DATE,GPV_PRODUCT_STATUS, " .
 		" GPV_SPECIAL_TOOL, GPV_IS_SPECIAL, GPV_CSP_PATCH,gpv_version_family " .
 		" FROM gft_product_version_master as Out1 " .
 		" where GPV_PRODUCT_STATUS='A' AND " .
 		" exists (select * from (SELECT GPV_PRODUCT_CODE,GPV_MAJOR_VERSION,MAX(GPV_RELEASE_DATE) AS GPV_RELEASE_DATE " .
		" FROM gft_product_version_master group by GPV_PRODUCT_CODE,GPV_MAJOR_VERSION) as Iner " .
		" where Out1.GPV_PRODUCT_CODE= Iner.GPV_PRODUCT_CODE and Out1.GPV_MAJOR_VERSION= Iner.GPV_MAJOR_VERSION " . 
		" and Out1.GPV_RELEASE_DATE= Iner.GPV_RELEASE_DATE) order by GPV_PRODUCT_CODE,GPV_MAJOR_VERSION,GPV_RELEASE_DATE " .
		") as T2 " .
		" on (T1.GPV_PRODUCT_CODE=T2.GPV_PRODUCT_CODE and T1.gpv_version_family=T2.gpv_version_family)  " .
		" group by GPM_DESC,T2.GPV_BASE_VERSION,versionA,GPV_DOWNLOAD_HLINK,GPV_DOWNLOAD_HLINKPH," .
		" GPV_PRODUCT_STATUS,GPV_SPECIAL_TOOL,GPV_IS_SPECIAL, GPV_CSP_PATCH, GPV_RELEASE_DATE,T1.GPV_PRODUCT_CODE " .
		" having datediff('$today_date',GPV_RELEASE_DATE) < 8 ORDER BY GPV_RELEASE_DATE DESC LIMIT 8";
 return $sql1;
}

/**
 * @return string
 */
function show_current_version_dtl(){
	$ver_dtl=get_version_dtl();
	if($ver_dtl==null){
		$ver=get_samee_const('SAM_VERSION');$ver_date=get_samee_const('SAM_RELEASE_DATE');
		$disp_ver_build="<b>Version[".$ver."] Date[".$ver_date."]</b>";
	}else{
		$ver=$ver_dtl[0][0];
		$ver_date=$ver_dtl[0][1];
		$hlink=trim($ver_dtl[0][2]);
		if($hlink){
			$hlink="<a title=\"Release Note\" href=\"$hlink\">$ver</a>";
		}else{
			$hlink=$ver;
		}
		$disp_ver_build=" <b>-</b> [$hlink] <b>On</b> [<a title=\"Issues fixed\" href=\"support_report.php?prod=1-03.0&amp;updated_version=$ver&amp;support_status[]=T1\">".$ver_date."</a>]";
	}
	return $disp_ver_build;
}

/**
 * @return string
 */
function raymedi_news(){
$today_date=date('Y-m-d');
$sql_rt="select GNT_ID,GEM_EMP_NAME,GNT_NEWS,GNT_DATE,GNT_EMP_ID,gnt_id " .
		"from gft_news_today,gft_emp_master " .
		"WHERE GNT_EMP_ID=GEM_EMP_ID and  GNT_PUBLISH_STATUS='1' and " .
 		"DATEDIFF(DATE_ADD(GNT_DATE,INTERVAL GNT_DAYS_TOSHOW DAY),'$today_date') >= 0 " .
 		"order by GNT_DATE,GNT_ID desc";
return $sql_rt;
}


/**
 * @param string $setteb
 * 
 * @return void
 */
function menuarea($setteb){
	global $uid;
	global $base_relative_path;
	$roleid=$_SESSION["roleid"];
	//$uid=(string)$_SESSION["uid"];
	$setteb=$setteb==null?'1':$setteb;
	if(!is_authorized_group($uid,1) || ($roleid=='84') ){
		$menuq= "SELECT distinct(m.mid),m.menu_name, m.menu_path,m.menu_param,m.menu_popup FROM gft_menu_master m, gft_menu_role_access ra  
		WHERE m.mid= ra.GMR_MID AND (ra.GMR_ROLE_ID=$roleid OR ra.GMR_EMP_ID=$uid)  " .
				"AND m.fk_tab_id=$setteb AND menu_daccess='1'  order by GMR_AVAILABILITY ";
	}else{
		$menuq= "SELECT distinct(m.mid),m.menu_name, m.menu_path,m.menu_param,m.mid,m.menu_popup FROM gft_menu_master m where m.fk_tab_id=$setteb " .
				"AND menu_daccess='1'  order by menu_order ";
	}
	$resultmenu=execute_my_query($menuq,'menu_util.php',true,true,4);
	
	$menu1='';

	if(!($setteb=='11')){
echo<<<END

END;
	$c=0;
	$mrow=0;

	$menu = /*. (string[int]) .*/ array();
	while($data=mysqli_fetch_array($resultmenu)){
		if($c+strlen($data['menu_name'])>150){
			$menu[$mrow].="&nbsp;&nbsp;</td></tr>";
			$mrow++;
			$c=0;
		}		
		if($c==0){
			$menu[$mrow]="<tr id=\"$mrow\"><td class=\"moduleMenuBg\" align=\"left\">&nbsp;&nbsp;" .
					"<a href=\"javascript:menuhide('firstrow');\"><span class=\"imagecur sprite_up\"></span></a>&nbsp;&nbsp;|";
			if($mrow==0){
				$menu1="<tr id=\"$mrow\"><td class=\"moduleMenuBg\" align=\"left\">&nbsp;&nbsp;" .
						"<a href=\"javascript:menuhide('allrow');\">" .
						"<span class=\"imagecur sprite_down\"></span></a>&nbsp;&nbsp;|";
			}
		}
		$menu[$mrow].="&nbsp;&nbsp;<a href=".$base_relative_path."".$data['menu_path'].($data['menu_param']!=''?"?".$data['menu_param']:"").($data['menu_popup']=='1'?" target='_balnk' ":"").">" .
				"<font color='#000000'>".$data['menu_name']."</font></a>&nbsp;&nbsp;|";
		if($mrow==0){
			$menu1.="&nbsp;&nbsp;<a href=".$base_relative_path."".$data['menu_path'].($data['menu_param']!=''?"?".$data['menu_param']:"").($data['menu_popup']=='1'?" target='_balnk' ":"").">" .
					"<font color='#000000'>".$data['menu_name']."</font></a>&nbsp;&nbsp;|";
		}
		$c+=(strlen($data['menu_name'])+5);
	}
	$menu[$mrow].="&nbsp;&nbsp;</td></tr>";
	$menu1.="&nbsp;&nbsp;</td></tr>";
	echo "<div class=\"unhide\" id=\"firstrow\">" .
			"<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">".$menu1." <tbody> </tbody>" .
			"</table></div>";
	echo "<div class=\"hide\" id=\"allrow\">" .
		"<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">".implode($menu)." <tbody></tbody>" .
		"</table></div>";
	}
}

/**
 * @param string $setteb
 * 
 * @return void
 */
function tabarea($setteb){
	global $me;
	global $uid;
	global $base_relative_path;
	//$uid=(string)$_SESSION["uid"];
	$roleid=$_SESSION["roleid"];
	$tabselectid=array(0,1,2,3,4,5,6,7,8,9,10);
    $mypath=substr($me,strrpos($me,"/")+1);
   $setteb=$setteb==null?'1':$setteb;
	if(!is_authorized_group($uid,1) || ($roleid=='84') ){
		$emp_cond = " OR g.GMR_EMP_ID=$uid ";
		if($roleid=='84'){
			$emp_cond = "";			
		}
		$avltabquery=" SELECT distinct(g.GMR_TAB_ID) tab_id,t.tab_name,m.menu_path, t.tab_select_id,m.menu_popup  " .
				"FROM gft_menu_role_access g " .
				" left join gft_tab_master t on(t.tab_id=g.GMR_TAB_ID )".
				" left join gft_menu_master m on(m.mid=g.GMR_MID )" .
				" where (g.GMR_ROLE_ID=$roleid $emp_cond) AND GMR_AVAILABILITY=1 " .
				"order by t.tab_select_id";
	}else{
		$avltabquery="SELECT DISTINCT(m.fk_tab_id) tab_id,t.tab_name, m.menu_path,t.tab_select_id, m.menu_popup FROM " .
				"gft_menu_master m
				left join gft_tab_master t on(t.tab_id=m.fk_tab_id ) where  m.menu_order=1 " .
				"AND !isnull(m.fk_tab_id) order by t.tab_select_id";
	}
	$avltabresult=execute_my_query($avltabquery,'menu_util.php',true,false,4);
	
	if(mysqli_num_rows($avltabresult)==0){
		echo("&nbsp;<br/><font color=\"red\" nowrap>&nbsp;Menu Configuration setting is not done " .
				"please contact Administrator &nbsp;</font><br/>&nbsp;");
		exit;
	}
	$tabnamelist="";
	$tabidlist="";
	$menupathlist='';
	$menu_popuplist='';
	while($data= mysqli_fetch_array($avltabresult)){
		$tabidlist.=$data['tab_id'].',';
		$tabnamelist.=$data['tab_name'].',';
		$menupathlist.=$data['menu_path'].',';
		$menu_popuplist.=$data['menu_popup'].',';
	}
	$tabnamelist=substr($tabnamelist,0,(strlen($tabnamelist)-1));
	
	$tabidlist=substr($tabidlist,0,(strlen($tabidlist)-1));
	$menupathlist=substr($menupathlist,0,(strlen($menupathlist)-1));
	$tab_name=explode(',',$tabnamelist);
	$tab_id=explode(',',$tabidlist);
	$tab_href =explode(',',$menupathlist);
	$menu_popup =explode(',',$menu_popuplist);

	$tab_border_class= array("tabOffBorder","tabOnBorder");
	$tab_select_class=array("otherTab","currentTab");
	$menu_start_gif=array("sprite_menuoff_start","sprite_menuon_start");
	$menu_end_gif=array("sprite_menuoff_end","sprite_menuon_end");
	$menu_tile_gif=array("$base_relative_path/images/menu_off_tile.gif","$base_relative_path/images/menu_on_tile.gif");
	$import="<img src=\"images/import.gif\" width=\"20px\" height=\"16px\" border=0></img>&nbsp;Import";
	for($i=0;$i<count($tab_name);$i++)
	{
		$s=0;
		
		if ($tab_id[$i] ==$setteb)
		{
  			$s=1;
	  	}
		$s_tab_border_class=$tab_border_class[$s];
		$s_menu_start_gif=$menu_start_gif[$s];
		$s_menu_end_gif=$menu_end_gif[$s];
		$s_menu_tile_gif=$menu_tile_gif[$s];
		$s_tab_select_class=$tab_select_class[$s];
		$i_tab_name=$tab_name[$i];
		$i_tab_href=$base_relative_path."".$tab_href[$i]."?tab_to_select=$i";
		if($i==10){
			$i_tab_href=$tab_href[$i]."?tab_to_select=$i";
		}
		$blank=($menu_popup[$i]=='1'?'target="_blank"':'');
echo<<<END
<td width="5" class="$s_tab_border_class" height="12px"><span class="$s_menu_start_gif"></span></td>
<td class="$s_tab_border_class" style="background-image:url($s_menu_tile_gif)" height="16px" nowrap valign=center>
<A class='$s_tab_select_class' href='$i_tab_href' $blank>$i_tab_name</A></td>
<td width="5" class="$s_tab_border_class" height="12px"><span class="$s_menu_end_gif"></td>
<td class="tabOffBorder" valign="bottom" height="16px"><img src='$base_relative_path/images/blank.gif' alt="" width='5' height='16px'></td>
END;
}
echo<<<END
</tr></tbody></table>
END;
}
/**
 * @return string
 */
function get_leave_types_help() {
	return <<<END
		<br><br><b>Leave Types:</b><br><b>CSL: </b>Casual/Sick Leave (and both half days in same day)<br><b>H: </b>Half day Casual/Sick Leave
		<br><b>PRL: </b>Privelege Leave<br><b>ML: </b>Maternity Leave<br><b>PL: </b>Paternity Leave
END;
}

/**
 * @param string $emply_id
 * @param string $set_role_id
 *
 * @return string[int][int]
 */
function get_applicable_menu_array($emply_id,$set_role_id){
	$m_arr = /*. (string[int][int]) .*/array();
	$menu_que = " SELECT menu_name,menu_path,menu_order,tab_id,tab_name from gft_menu_master ".
			" join gft_tab_master t on (tab_id=fk_tab_id) ".
			" left join gft_menu_role_access on (mid=GMR_MID) " .
			" where menu_daccess=1 ";
	if(!is_authorized_group($emply_id,1) || ($set_role_id=='84') ){
		$role_cond = " and (GMR_ROLE_ID=$set_role_id or GMR_EMP_ID=$emply_id) ";
		if($set_role_id=='84'){
			$role_cond = " and GMR_ROLE_ID=$set_role_id ";
		}
		$menu_que .= $role_cond;
	}
	$menu_que .= " group by mid order by t.tab_select_id,menu_name ";
	$menu_res = execute_my_query($menu_que);
	while($menu_row = mysqli_fetch_array($menu_res)){
		$m_name		= $menu_row['menu_name'];
		$m_path		= $menu_row['menu_path'];
		$m_order	= $menu_row['menu_order'];
		$m_tabid	= (int)$menu_row['tab_id'];
		$m_tabname 	= $menu_row['tab_name'];

		$m_arr[$m_tabid]['name'] 	= $m_tabname;
		$m_arr[$m_tabid]['menus'][]	= array('name'=>$m_name,'path'=>$m_path,'order'=>$m_order);
	}
	$out_arr = array();
	foreach ($m_arr as $mid => $darr){
	    $out_arr[] = array("name"=>$darr['name'],"menus"=>$darr['menus']);
	}
	return $out_arr;
}

?>
