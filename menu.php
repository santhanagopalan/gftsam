<?php
require_once(__DIR__ ."/inc.essentials_for_popup.php");
require_once(__DIR__ ."/menu_util.php");
$rep_issue="{$base_relative_path}report_an_issue.php?id=$uid";
$send_mail="send_mail_to_other.php?id=$uid";
$ver_dtl		= get_version_dtl();
$disp_ver_build	= isset($ver_dtl[0][0])?$ver_dtl[0][0]:'';
$disp_ver_date	= isset($ver_dtl[0][1])?$ver_dtl[0][1]:'';
$rel_note_link	= isset($ver_dtl[0][2])?$ver_dtl[0][2]:'';
if($disp_ver_date!=''){
	$disp_ver_date = date('M d, Y',strtotime($disp_ver_date));
}

$disp_ver_link = "<span class='version-link' onclick=\"window.open('$rel_note_link','_blank')\">$disp_ver_build</span>";
$temp_lastlogin	= isset($_SESSION["lastlogin"])?(string)$_SESSION["lastlogin"]:'';
$temp_uname		= (string)$_SESSION["uname"];
$emobile		= (string)$_SESSION['mobile_no'];
$temp_lastlogin	= substr($temp_lastlogin,0,16);
$last_login_str = date('M d, Y H:i',strtotime($temp_lastlogin));
$tourl			= $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
$empidx			= $_SESSION['uid'];

$bque = "select GB_NAME,GB_URL from gft_bookmark where GB_EMP_ID='$empidx' ";
$bres = execute_my_query($bque);
$barr = /*. (string[int][string]) .*/array();
while ($brow = mysqli_fetch_array($bres)){
	$barr[] = array('id'=>$brow['GB_URL'],'label'=>$brow['GB_NAME']); 
}
$bsource = json_encode($barr);

$emp_dtl_que = "select GEM_EMP_ID,GEM_EMP_NAME,GEM_PROFILE_URL from gft_emp_master where GEM_EMP_ID='$empidx' ";
$emp_dtl_res = execute_my_query($emp_dtl_que);
$gem_prof_url = "";
if($emp_dtl_row = mysqli_fetch_array($emp_dtl_res)){
	$gem_prof_url = $emp_dtl_row['GEM_PROFILE_URL'];
	if($gem_prof_url==""){
		$gem_prof_url = "images/Profile.png";
	}
}

$shortcut_menus = get_shortcut_menus($empidx,$roleid);
$shortcut_menus_html = "<div class='shortcuts'>";
foreach ($shortcut_menus as $s_menu){
    $h_ref = $s_menu['link'];
    $target_blank = ' target="_blank" ';
    if(isset($s_menu['popup']) && $s_menu['popup']){
        $h_ref = "javascript:call_popup('$h_ref',5);";
        if(isset($s_menu['custom_tag']) && $s_menu['custom_tag'] == 'print')
            $h_ref = "javascript:window.print();";
    }else if(isset($s_menu['same_tab']) && $s_menu['same_tab']){
        $target_blank='';
    }
    if(isset($s_menu['popup']) && $s_menu['popup']){
        $shortcut_menus_html .= '<a class="shortcut-link shortcut-link-onclick"  onclick="'.$h_ref.'" '.$target_blank.'" >'.
            "<span class='".$s_menu['icon'] ." shortcut-icons'></span>".$s_menu['label']."</a>";
    }else{
        $shortcut_menus_html .= '<a class="shortcut-link" href="'.$h_ref.'" '.$target_blank.'" >'.
            "<span class='".$s_menu['icon'] ." shortcut-icons'></span>".$s_menu['label']."</a>";
    }
    
}
$shortcut_menus_html .= "</div>";
echo<<<END
<link rel='stylesheet' href='{$base_relative_path}CSS/fontawesome-all.css'>
<link rel='stylesheet' href='{$base_relative_path}CSS/menu.css'>
<div class='top-menu'>
	<div class='gofrugal-logo'>
		<img src="images/Logo_New_Final.svg" alt="logo" style="margin-left:10px">
	</div>
	<div class='sam-dtl'>
		<div class="left-line">
			<div class='sam-name'>SAM Enterprise Edition</div>
			<div class='sam-version'>V$disp_ver_link (Released on $disp_ver_date)</div>
		</div>
	</div>	
	<div class='menu-right'>
        $shortcut_menus_html
		<div class='search-bar'>
			<div class='menu-cell' style='padding-right: 25px;'>
				<input id="menuall" name="menuall" type="hidden">
				<input id="base_path" name="base_path" type="hidden" value="$base_relative_path">
				<input id="goto" name="goto" type="text" size="20" placeholder="Go To" class="menu-complete ui-autocomplete-input" autocomplete="off" spellcheck="false">
				<input id="url" name="url" type="hidden">
				<input id="bookmarkname" name="bookmarkname" type="text" size="15" placeholder="Filter" class="menu-complete ui-autocomplete-input" style="margin-right:2px" autocomplete="off" spellcheck="false">
				<a title="Manage your Bookmarks" href="manage_bookmarks.php"><span class="fas fa-edit" style="font-size:14px;color:gray"></span></a>
			</div>
			<div class='menu-cell'>
				<img src="$gem_prof_url" width="30px" height="30px" style="border-radius:50%">
			</div>
			<div class="menu-cell">
			    <div class='name-info'>Welcome!, $temp_uname</div>
			    <div class='last-login'>Last login $last_login_str</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript" src="{$base_relative_path}js/jquery-ui.js"></script>
<script type="text/javascript">
jQuery.noConflict();
var old_val = new Array();
var ln = 0;
jQuery(document).ready(function(){
    jQuery("#goto").autocomplete({
      source: "list_menuname.php?required=json",
	  autoFocus: true,
	  delay:150,
	  max:10,
      scroll:true,
      select: function( event, ui ) {
		var base_path = jQuery("#base_path").val();
		var mn=ui.item.id.trim();
		if(mn=='-1'){
			return false;
		}else if(mn!=''){
			window.open(base_path+"dropdown_menu.php?menuall="+mn,"_blank");
		}
		jQuery(this).val("");
		return false;
      },
	  focus: function (event, ui) {
	      	event.preventDefault();
	  }
    });
    jQuery("#bookmarkname").autocomplete({
      source: $bsource,
	  autoFocus: true,
	  delay:150,
      showNoSuggestionNotice:true,
      noSuggestionNotice:"No Matches",
      select: function( event, ui ) {
		var base_path = jQuery("#base_path").val();
		var mn=ui.item.id.trim();
      	var path1 	= location.href;
		var pos 	= path1.indexOf('?');
      	var url1	= path1;
		if(pos > 0){
			url1	= path1.substring(0,pos);
	 	}
      	location.href=url1+'?'+mn;
		return false;
      },
      response: function(event, ui) {
        if (!ui.content.length) {
            var noResult = { value:"",label:"No Result"};
            ui.content.push(noResult);
        }
      },
	  focus: function (event, ui) {
	      	event.preventDefault();
	  }
    });
});		
</script>
END;

$mypath=substr($me,strpos($me,"/")+1);
$setteb=authendicated($mypath);
if($setteb=='NULL'){
	show_my_alert_msg("Access is Denied");
	show_firstpage_login();
	exit;
}
$menu_arr = get_applicable_menu_array($uid,$roleid);
if(count($menu_arr)==0){
	echo "<br/><font color='red' nowrap> Menu Configuration setting is not done please contact Administrator </font><br/>";
	exit;
}
$menu_html = "<div class='m-container'>";
foreach ($menu_arr as $t_arr){
	$inner_menus = "";
	$active_class="";
	$line_style = "";
	$tab_href = "";
	foreach ($t_arr['menus'] as $a_arr){
	    $path = $a_arr["path"];
	    if(strpos($path, "{{username}}")!==false){
	        $path = udpate_user_credential_menu($path);
	    }
	    $inner_menus .= "<a href='{$base_relative_path}$path'>".$a_arr["name"]."</a>";
		if($a_arr["path"]==$mypath){
			$active_class = 'menu-active';
			$line_style = "border-right: 1px solid #EAF0F6;";
		}
		if($a_arr["order"]=='1'){
			$tab_href = $base_relative_path.$a_arr["path"];
		}
	}
	$menu_html.="<div class='m-dropdown $active_class'>".
					"<a href='$tab_href'><button class='m-dropbtn $active_class'>".$t_arr["name"]."</button></a>".
					"<div class='m-holder'>".
						"<span class='fas fa-caret-down m-arrow $active_class'></span>".
						"<div class='m-dropdown-content'>$inner_menus</div>".
					"</div>".
					"<div class='line-separator' style='$line_style'></div> ".
				"</div>";
}
$menu_html .= "</div>";
echo $menu_html;
//tabarea($setteb);
//require_once(__DIR__ ."/raymedi_today.php");
//menuarea($setteb);
//Go to top icon
echo<<<END
<div id="go-to-top-box" style='display:none;'>
		<i class="fas fa-angle-double-up" id="go-to-top"></i>
</div>
<link rel="Stylesheet" type="text/css" href="libs/jquery.datetimepicker/jquery.datetimepicker.css" />
<script type="text/javascript" src="libs/jquery.datetimepicker/jquery.datetimepicker.full.min.js" ></script>
<script>
jQuery(document).ready(function(){
	jQuery('#donv,#demo_date,#visit_date,#timeout').datetimepicker({
		format:'Y-m-d H:i',
		step: 15,
        scrollInput : false 
	});
    jQuery('#bsns_eff_date,#inc_from_dt,#inc_to_dt,#m_time,#process_from_dt,#process_to_dt').datetimepicker({
		format:'Y-m-d',
        timepicker:false,
        scrollInput : false
    });
    jQuery('#gstin_edc_date').datetimepicker({
		format:'Y-m-d',
        timepicker:false,
        scrollInput : false,
        minDate: 0
    });
});
</script>
END;
?>
