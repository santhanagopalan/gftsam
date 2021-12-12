<?php
require_once(__DIR__ ."/dbcon.php");

/**
 * @param string $report_content
 *
 * @return void
 */
function print_dtable_footer_report_to_mailer(&$report_content){
	$report_content.=<<<END
</td></tr></table></table>
END;
}
/**
 * @param string $report_content
 * @param string[int][int] $value_arr
 * @param string[int] $value_arr_width
 * @param string[int] $value_arr_align
 * @param string[int] $value_arr_class
 * @param string[int] $row_class_in_arr
 * @param string[int] $table_row_id
 * @param string[int] $table_row_style
 * @param string[int][int] $value_arr_rowspan
 * @param string[int][int] $value_arr_colspan
 * @param string $table_row_name
 * @param int $child
 *
 * @return void
 */
function print_resultset_report_to_mailer(&$report_content,$value_arr,$value_arr_width=null,$value_arr_align=null,$value_arr_class=null,
		$row_class_in_arr=null,$table_row_id=null,$table_row_style=null,$value_arr_rowspan=null,
		$value_arr_colspan=null,$table_row_name=null,$child=0){
	global $alt_row_class;
	$s=0;
	foreach($value_arr as $i => $va){
		$trow_id='';
		$trow_style='';
		if(isset($table_row_id[$i]) and $table_row_id[$i]!=null){
			$trow_id=' id ="'.$table_row_id[$i].'"';
		}
		if(isset($table_row_style[$i]) and $table_row_style[$i]!=null){
			$trow_style=$table_row_style[$i];
		}
		if ($child!=0){
			$trow_style.="style='Display:none'";
		}
		else {
			$trow_style="";
		}
		if($table_row_name!=null && $table_row_name!=''){
			$trow_id.=' name="'.$table_row_name.'"';
		}

		if(isset($row_class_in_arr[$i]) and $row_class_in_arr[$i]!=null){
			$report_content.= "\n".'<tr class="'.$row_class_in_arr[$i].'" '.$trow_id .$trow_style.' >';
		}else if($value_arr[$i][0]!=null ){
			$report_content.= "\n".'<tr class="'.$alt_row_class[$s].'" '.$trow_id.$trow_style.'>';
		}else if($value_arr[$i][0]=='' and isset($value_arr[$i][1]) and !isset($value_arr[$i][2])){//zone
			$report_content.= "\n".'<tr class="'.$alt_row_class[2].'" '.$trow_id.$trow_style.' >';
		}else if($value_arr[$i][0]=='' and $value_arr[$i][1]=='' and $value_arr[$i][2]!=''){//region
			$report_content.= "\n".'<tr class="'.$alt_row_class[3].'" '.$trow_id.$trow_style.'>';
		}else if($value_arr[$i][0]=='' and $value_arr[$i][1]=='' and $value_arr[$i][2]=='' and $value_arr[$i][3]!=''){//area
			$report_content.= "\n".'<tr class="'.$alt_row_class[4].'" '.$trow_id.$trow_style.'>';
		}else {
			$report_content.= "\n".'<tr class="'.$alt_row_class[$s].'" '.$trow_id .$trow_style.' >';
		}
		for($j=0;$j<count($value_arr[$i]);$j++){
			$class_td='';

			if(!isset($value_arr[$i][$j])){$value_arr[$i][$j]='';}
			$value=$value_arr[$i][$j];
			if(!isset($value_arr_class[$j]) or $value_arr_class[$j]==null){
				$class_td='class="content_txt" ';
			}else if(isset($value_arr_class[$j]) and $value_arr_class[$j]!=''){
				$class_td=' class="'.$value_arr_class[$j].'" ';
			}
			$align=(($value_arr_align!=null and isset($value_arr_align[$j]))?'align="'.$value_arr_align[$j].'"':'');
			$width=(($value_arr_width!=null and isset($value_arr_width[$j]))?'width="'.$value_arr_width[$j].'px"':'');
			if($value_arr_rowspan!=null){
				if(isset($value_arr_rowspan[$i][$j]) and $value_arr_rowspan[$i][$j]>1){
					$rowspan_val=$value_arr_rowspan[$i][$j];
					$align.=" rowspan='$rowspan_val' valign='center' ";
				}
			}
			if($value_arr_colspan!=null){
				if(isset($value_arr_colspan[$i][$j]) and (int)$value_arr_colspan[$i][$j]>1){
					$colspan_val=$value_arr_colspan[$i][$j];
					$align.=" colspan='$colspan_val' ";
				}
			}
			$report_content.= "\n".'<td '.$class_td. $align .' wrap>'.$value.'</td>';
		}
		$s=($s==0)?1:0;
		$report_content.= '</tr>';
	}
}

/**
 * @param string $report_content
 * @param string $total_query
 * @param string[int] $mysort
 * @param string[int] $report_link
 * @param string[int] $show_in_popup
 * @param string[int] $value_arr_align
 * @param string[int] $sec_field_arr
 *
 * @return void
 */
function generate_overall_total_report_to_mailer(&$report_content,$total_query,$mysort,$report_link,$show_in_popup,$value_arr_align,$sec_field_arr){
	global $from_dt,$to_dt;

	$value_arr=/*. (string[int][int]) .*/ array();

	$result_total_query=execute_my_query($total_query);
	while($query_data=mysqli_fetch_array($result_total_query) ){
		//extract($query_data);
		$GLH_REFERENCE_GIVEN=$query_data['GLH_REFERENCE_GIVEN'];
		$GLC_PRODUCT_CODE=$query_data['GLC_PRODUCT_CODE'];
		$GLH_LEAD_SOURCECODE=$query_data['GLH_LEAD_SOURCECODE'];
		$GTM_VERTICAL_CODE=$query_data['GTM_VERTICAL_CODE'];
		$GPM_PINCODE=$query_data['GPM_PINCODE'];
		$GEM_EMP_ID=$query_data['GEM_EMP_ID'];
		$zone_id=$query_data['zone_id'];
		$terr_mgr_id=$query_data['terr_mgr_id'];
		$regional_mgr_id=$query_data['regional_mgr_id'];
		$zone_mgr_id=$query_data['zone_mgr_id'];

		$array_count=0;
		for($i=0;$i<count($mysort);$i++){
			$link="";
			$t_var = $mysort[$i];
			$t_v_val=$$t_var; //NOTE: $ -of- $
			if(empty($t_v_val)) continue;
			$field_value=$t_v_val;
			if($sec_field_arr[$i]=='Y'){
				$hrs_convert=secondsToTime($field_value);
				$field_value=$hrs_convert['h'].':'.$hrs_convert['m'];
			}
			if(!isset($show_in_popup[$i])) $show_in_popup[$i]='N';
			if($report_link[$i]!=''){
				$link=$report_link[$i]."&cmb_zone_mgr=$zone_mgr_id&cmb_reg_mgr=$regional_mgr_id&cmb_terr_mgr=$terr_mgr_id" .
				"&cmb_ex_name=$GEM_EMP_ID&emp_code=$GEM_EMP_ID&cmb_pincode=$GPM_PINCODE".
				"&vertical=$GTM_VERTICAL_CODE&reference_group=$GLH_LEAD_SOURCECODE&reference_list=$GLH_REFERENCE_GIVEN".
				"&productct_shown[]=$GLC_PRODUCT_CODE";
				if(isset($query_data['country_id']) and $query_data['country_id']!=''){	$link.="&cmbCountry=".$query_data['country_id']; }
				if(isset($query_data['state_id']) and $query_data['state_id']!=''){	$link.="&cmbState=".$query_data['state_id'];}
				if(isset($query_data['district_id']) and $query_data['district_id']!=''){	$link.="&cmbDist=".$query_data['district_id'];}
				if(isset($query_data['zone_id']) and $query_data['zone_id']!=''){	$link.="&cmbZone=$zone_id"; }
				if(isset($query_data['region_id']) and $query_data['region_id']!=''){	$link.="&cmbRegion=".$query_data['region_id'];}
				if(isset($query_data['area_id']) and $query_data['area_id']!=''){	$link.="&cmbArea=".$query_data['area_id'];}
				if(isset($query_data['terr_id']) and $query_data['terr_id']!=''){	$link.="&cmbterr=".$query_data['terr_id'];}
				if(isset($query_data['from_dt']) and $query_data['from_dt']!=''){	$from_date=local_date_format($from_dt);
				$link.="&from_dt=$from_date";}
				if(isset($query_data['to_dt']) and $query_data['to_dt']!=''){	$to_date=local_date_format($to_dt);$link.="&from_dt=$to_date";}
			}
			if($link!='' && $show_in_popup[$i]=='N'){
				$value_arr[$array_count][$i]="<a href=\"$link\" target=_blank>".$field_value."</a>";
			}
			else if($link!='' && $show_in_popup[$i]=='Y'){
				$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$link',4);\">".$field_value."</a>";
			}
			else{
				$value_arr[$array_count][$i]=$field_value;
			}

		}//end of for
		if(empty($value_arr[0][1])){$value_arr[0][1]='Overall Total';}
		//print_resultset_report_to_mailer($value_arr,null,$value_arr_align,null,array("highlight_green"),	null,'overarall_total',null,null);
		$dummystring='';
		//NOTE: May need to pass $report_content instead of $dummystring ?
		print_resultset_report_to_mailer($dummystring,$value_arr,null,$value_arr_align,null,array("highlight_green"),	null,null,null,null);
	}//end of while
}//end of function

/**
 * @param string $report_content
 * @param string[int] $myarr
 * @param string[int] $mysort
 * @param string $nav_struct
 * @param string $sortbycol
 * @param string $sorttype
 * @param string[int] $myarr_width
 * @param string $noheader
 * @param string[int] $myarr_extra_link
 * @param string[int] $myarr_sub
 * @param string[int] $rowspan_arr
 * @param string[int] $colspan_arr
 * @param string[int] $myarr_sub1
 * @param string[int] $rowspan_arr1
 * @param string[int] $colspan_arr1
 *
 * @return void
 */
function sortheaders_report_to_mailer(&$report_content,$myarr,$mysort,$nav_struct,$sortbycol,$sorttype,
		$myarr_width=null,$noheader=null,$myarr_extra_link=null,$myarr_sub=null,$rowspan_arr=null,
		$colspan_arr=null,$myarr_sub1=null,$rowspan_arr1=null,$colspan_arr1=null){
	global $from_page;
	$sortimage='';
	$sortalter='';
	$mysortad=array ("" , " desc");
	$php_page_extra_args=php_extra_args('sortheaders');
	if($noheader==null){
		$report_content.="<thead>";
	}

	$report_content.=<<<END
<tr bgcolor='teal'>
END;
	$sub_arr_index=0;
	$sub_arr_index1=0;
	$sub_index1='';
	$sub_index='';
	for($i=0,$k=0;$i<count($myarr);$i++){
		$rowspan=(isset($rowspan_arr[$i])?"rowspan=\"".$rowspan_arr[$i]."\"":"");
		$colspan=(isset($colspan_arr[$i])?"colspan=\"".$colspan_arr[$i]."\"":"");
		if(isset($myarr_width[$i]) and $myarr_width[$i]!=null){
			$width="style=\"width:".$myarr_width[$i]."\"";
		}else {$width="";}
		$report_content.= '<td '.$rowspan.' '.$colspan.' '.$width.'>';
		if(isset($mysort[$k]) and $mysort[$k]!='' and (!isset($colspan_arr[$i]) or $colspan_arr[$i]==1)){
			$report_content.=$myarr[$i];
			$k++;
		}else{
			$report_content.=$myarr[$i];
			if(!isset($colspan_arr[$i]) or $colspan_arr[$i]==1 or $colspan_arr[$i]=='') {$k++; }
		}
		if(isset($myarr_extra_link[$i]) and $myarr_extra_link[$i]!=null){
			$report_content.= $myarr_extra_link[$i];
		}
		$report_content.= " </td> ";

		if(isset($colspan_arr[$i]) and isset($myarr_sub) and $colspan_arr[$i]>1 and $myarr_sub!=null){
			$rowspan1='';$colspan1='';
			for($j=1;$j<=(int)$colspan_arr[$i];$sub_arr_index++){
				if(isset($rowspan_arr1[$sub_arr_index]) and $rowspan_arr1[$sub_arr_index]!=''){
					$rowspan1="rowspan=".(isset($rowspan_arr1[$sub_arr_index])?$rowspan_arr1[$sub_arr_index]:1);
				}
				if(isset($colspan_arr1[$sub_arr_index]) and $colspan_arr1[$sub_arr_index]!=''){
					$colspan1="colspan=".(isset($colspan_arr1[$sub_arr_index])?$colspan_arr1[$sub_arr_index]:1);
				}
				if((!isset($colspan_arr1[$sub_arr_index]) or $colspan_arr1[$sub_arr_index]==1 or $colspan_arr1[$sub_arr_index]==0) and (isset($mysort[$k]) and $mysort[$k]!="") ){
					$sub_index.="<td  $rowspan1 $colspan1>".$myarr_sub[$sub_arr_index]."</td>";
					//$sub_index.="<a class=\"header_link\" href=\"$from_page?next_page="."0"."&amp;$php_page_extra_args&amp;sortbycol=$mysort[$k]&amp;sorttype=".(($sortbycol==$mysort[$k])?$sortalter:"")."\" >".(($sortbycol!='' and $sortbycol==$mysort[$k])?$sortimage:"").$myarr_sub[$sub_arr_index]."</a>";
					$k++;
					$j++;
					//echo $sub_index;
				}else{
					$sub_index.="<td $rowspan1 $colspan1>";
					$sub_index.=(isset($myarr_sub[$sub_arr_index])?$myarr_sub[$sub_arr_index]:'');
					if((!isset($colspan_arr1[$sub_arr_index]) or $colspan_arr1[$sub_arr_index]==1)){
						$k++;$j++;
					}else if($colspan_arr1[$sub_arr_index]>1){
						for($j1=1;$j1<=$colspan_arr1[$sub_arr_index];$j1++,$sub_arr_index++){
							$sub_index1.="<td >";
							$sub_index1.=(isset($mysort[$k])?"<a  class=\"header_link\" href=\"$from_page?next_page="."0"."&amp;$php_page_extra_args&amp;sortbycol=$mysort[$k]&amp;sorttype=".(($sortbycol==$mysort[$k])?$sortalter:"")."\" >".(($sortbycol!='' and $sortbycol==$mysort[$k])?$sortimage:"").$myarr_sub1[$sub_arr_index1]."</a>":$myarr_sub1[$sub_arr_index1]);
							$k++; $j++;
						}
					}
				}
			}//end of for
		}
	}//end of for
	$report_content.=<<<END
</tr>
END;

	if($sub_index!=''){
		$report_content.="<tr bgcolor='teal'>$sub_index</tr>";
	}
	if($sub_index1!=''){
		$report_content.="<tr  bgcolor='teal'>$sub_index1</tr>";
	}
	if($noheader==null) $report_content.="</thead>";

}

/**
 * @param string $report_content
 * @param string $table_title
 * @param string $tooltip
 * @param string $tooltip_width
 * @param boolean $old
 * @param boolean $mail_link
 * @param string $history_link
 *
 * @return void
 */
function print_dtable_header_report_to_mailer(&$report_content,$table_title,$tooltip=null,$tooltip_width="300",$old=true,$mail_link=true,$history_link=null){
	$access_page1=$_SERVER['SCRIPT_NAME'];
	global $group_id,$me;
	$report_id='';$menu_name='';

	if($group_id==1){
		$get_bname=basename($access_page1);
		$query=" select menu_name,mid from gft_menu_master where menu_path='$get_bname' ";
		$result=execute_my_query($query,$me,true,false,3);
		if($data=mysqli_fetch_array($result)){
			$menu_name=$data['menu_name'];
			$report_id=$data['mid'];
		}
		$today_date=date('Y-m-d');
		$table_title="<a href=\"accesspage_report.php?menu_path=$report_id&amp;for_date=$today_date&amp;to_date=$today_date&amp;emp_name=&amp;emp_code=&amp;location=1&amp;menu_name=".urlencode($menu_name)."\" target=\"new\" $tooltip >$table_title</a>";
	}else{
		$table_title="<a href=\"\" $tooltip >$table_title</a>";
	}
	$uid=$_SESSION['uid'];
	if($mail_link){
		$send_mail="send_mail_to_other.php?id=$uid";
		$send_mail_link="<a href=\"javascript:call_popup('$send_mail',4);\"><img  src=\"images/emails.gif\" alt=\"send mail\" hspace=\"3\" align=\"middle\" border=\"0\"></a>&nbsp";
		$table_title.="&nbsp;".$send_mail_link;
	}
	if($old==true){
		$report_content.=<<<END
<table  cellpadding="0" cellspacing="0" border="1" width="99%" align="center" ><tbody>
<tr><td>
<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr>
<td vAlign="top" align="left" width="5px" style="background-image:url(images/header_start.gif); height:18px;">
<img alt="" src="images/header_start.gif" border="0"></td>
<td class="formHeader" style="background-image:url(images/header_tile.gif); height:18px;" vAlign="middle" align="left" noWrap width="20%">
$table_title</td>
<td vAlign="top" align="left"  width="5px"  style="background-image:url(images/header_end.gif); height:18px;"><img alt="" src="images/header_end.gif" border="0"></td>
<td NOWRAP>&nbsp;&nbsp;<B> $history_link </B></td>
<!--<td width='100%'><img alt="" height='1' src='images/blank.gif'></td> -->
</tr></table></td></tr><tr><td>
END;
	}else{
	$report_content.=<<<END
<table  cellpadding="0" cellspacing="0" border="1" align="center" width="100%" ><tbody>
<tr><td width='75%'><a href="">$table_title</a>
<span align='right'>$history_link</span></td></tr>
<tr><td>
END;
		}
}

/**
 * @param string $table_header
 * @param string $query
 * @param string[int] $myarr
 * @param string[int] $mysort
 * @param string $sms_category
 * @param string $email_category
 * @param string $previous_months_link
 * @param string[int] $value_arr_align
 * @param string $myarr_width
 * @param string $total_query
 * @param string[int] $sec_field_arr
 * @param string $myarr_sub
 * @param string $rowspan_arr
 * @param string $colspan_arr
 * @param string $myarr_sub1
 * @param string $rowspan_arr1
 * @param string $colspan_arr1
 * @param string[int] $report_link
 * @param string[int] $show_in_popup
 * @param boolean $scorallable_tbody
 * @param boolean $navigation
 * @param string $order_by
 * @param boolean $heading
 * @param string $value_arr_total
 * @param boolean $sorting_required
 * @param int $child
 * @param string $trow_name
 * @param string $emyarr
 * @param string $emyarr_sub
 * @param string[int] $pass_only_this_arg
 * @param int $export_index
 * 
 * @return string
 */
function generate_reports_to_mailer($table_header,$query,$myarr,$mysort,$sms_category=null,$email_category=null,
		$previous_months_link=null,$value_arr_align=null,$myarr_width=null,$total_query=null,$sec_field_arr=null,
		$myarr_sub=null,$rowspan_arr=null,$colspan_arr=null,$myarr_sub1=null,$rowspan_arr1=null,$colspan_arr1=null,
		$report_link=null,$show_in_popup=null,$scorallable_tbody=false,$navigation=true,$order_by=null,$heading=true,
		$value_arr_total=null,$sorting_required=true,$child=0,$trow_name=null,$emyarr=null,$emyarr_sub=null,
		$pass_only_this_arg=null,$export_index=0){
     /*    $sec_field_arr=SECONDS fILED NOTIFICATION Y/N */
	$report_content='';		
	$row_class_in_arr=/*. (string[int]) .*/ null;
	global $uid;
	global $sorttype;
	global $reference_group,$reference_list,$reference_list_code,$noheader;

		global $sortbycol,$emp_id,$ename,$support_calls,
				$GEM_EMP_ID,$GLH_LEAD_SOURCECODE,$GLH_REFERENCE_GIVEN,
				$indiv_order,$chain_order,$value_arr_width,$vertical;
	$query.=add_order_by_query($order_by,$sorttype,$sorting_required,$mysort); 
	$result=execute_my_query($query);
	$count_num_rows=mysqli_num_rows($result);
	if($count_num_rows==0) { 
		$stacktrace=getStackTraceString();
		error_log("query returns zero - ".$query . " at " . $stacktrace);
		return ''; 
	}

	$tooltip=/*. (string) .*/ null;
	if($heading==true){	
		if($table_header!=null){
			print_dtable_header_report_to_mailer($report_content,$table_header,$tooltip=null,$tooltip_width="300",$old=true,$mail_link=false,$previous_months_link);
		}
		
		if($emyarr==null){  $emyarr=$myarr;}
		if($emyarr_sub==null){  $emyarr_sub=$myarr_sub;}
$report_content.=<<<END
<table cellpadding="0" cellspacing="2" width="100%" border="1" class="FormBorder1">
END;
	sortheaders_report_to_mailer($report_content,$myarr,$mysort,null,$sortbycol,$sorttype,$myarr_width,$noheader,$myarr_extra_link=null,
	$myarr_sub,$rowspan_arr,$colspan_arr,$myarr_sub1,$rowspan_arr1,$colspan_arr1);
	
	}	
	$s=0;
	$id=$prev_date="";$tbody_class="";
	$start_of_row=0;
	
	if($count_num_rows>0){
		$sl=0;$array_count=0;
		if($scorallable_tbody==true){
			$tbody_class="class='scrollable'";
		}
		if($heading==true){
			$report_content.="<tbody $tbody_class>";
		}
		$serails=$start_of_row;
		$financial_yr='';
		
		$value_arr=/*. (string[int][int]) .*/ array();
		$value_arr_total=/*. (string[int][int]) .*/ array();
		$value_arr_total_value=/*. (string[int][int]) .*/ array();

		while($query_data=mysqli_fetch_array($result) ){
			$sl++;$serails++;
			if($child==1){$row_class_in_arr[$array_count]="highlight_orange";}
			//extract($query_data);
			$GLH_LEAD_CODE=isset($query_data['GLH_LEAD_CODE']) ? $query_data['GLH_LEAD_CODE'] : '';
			$GLH_CUST_NAME=isset($query_data['GLH_CUST_NAME']) ? $query_data['GLH_CUST_NAME'] : '';
			$pcode=isset($query_data['pcode']) ? $query_data['pcode'] :'';
			$pskew=isset($query_data['pskew']) ? $query_data['pskew'] :'';

			for($i=0;$i<count($mysort);$i++){
				$link="";
				if($mysort[$i]=='' && ($myarr[$i]=='S.No' || $myarr[$i]=='S.No.')){
					$serial_no_dis=$serails;
					if($count_num_rows<=26 && ($child==1 or $child==2)){
						if($child==1){$serial_no_dis=chr($sl+64);}//capital letter
						else if($child==2){$serial_no_dis=chr($sl+96);}//small letter
					}else{
						if($child==1 or $child==2){
							$serial_no_dis=numberToRoman($sl);
						} 
					}
					$value_arr[$array_count][$i]=$serial_no_dis;
					continue;
				}else if($mysort[$i]=='GEM_EMP_NAME'){
					 	$link=get_ename_link($emp_id,$ename,$link_category=1,$tooltip=null,$query_data);
					 	$value_arr[$array_count][$i]=$link;
					 	continue;
				}else if($mysort[$i]=='GLH_CUST_NAME'){
					     if($query_data['GLH_LEAD_CODE']!=''){
					 		$tooltip=get_necessary_data_from_query_for_tooltip($query_data);
    						$link="<a  onMouseover=\"ddrivetip('".$tooltip."','#EFEFEF', 200)\";" .
			   			   " onMouseout=\"hideddrivetip()\"; " .
             		       " href=\"javascript:call_popup('edit_cust_details.php?lcode=$GLH_LEAD_CODE&call_from=popup&uid=$uid',7);\" class=\"subtle\">$GLH_CUST_NAME</a>" .
             		       " <a href=\"javascript:call_popup('supporthistory.php?call_from=popup&history_type=3&from_dt=01-11-2004&custCode=$GLH_LEAD_CODE',7);\" class=\"subtle\"><img alt=\"\" src=\"images/history.jpeg\" border=0 width=20 height=20></a>"; 
    
					 		$value_arr[$array_count][$i]=$link;
					     }else {
					     	$value_arr[$array_count][$i]='';
					     }
					 	continue;
				}else if($mysort[$i]=='GOD_ORDER_NO'){
						
						 if(is_authorized_group_list($uid,array(1))){
							$url_link_or="order_details.php?order_no=".$query_data['GOD_ORDER_NO']."&formtype=edit";
							$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$url_link_or',7);\" title='Order Details' >".$query_data['GOD_ORDER_NO']."</a>";
						 }else {
						 	$value_arr[$array_count][$i]=$query_data['GOD_ORDER_NO'];
						 	$value_arr[$array_count][$i].="<a href=\"javascript:call_popup('order_releated_details.php?order_no=".$query_data['GOD_ORDER_NO']."',5);\">[O]</a>";
						 }
						continue;
				}
				else if($mysort[$i]=='GER_NO_CALL_BACK'){
					$event_id=(isset($query_data['event_id'])?$query_data['event_id']:'');
					$link=" <a href=\"javascript:call_popup('supporthistory.php?call_from=popup&history_type=7&from_dt=01-11-2004&custCode=$GLH_LEAD_CODE&event_id=$event_id',7);\" class=\"subtle\">".$query_data['GER_NO_CALL_BACK']."</a>";
					$value_arr[$array_count][$i]=$link;
					continue; 
				}
				else {
					$t_var=$mysort[$i];
					$field_value=$query_data[$t_var]; //NOTE: $ -of- $
					if(isset($sec_field_arr[$i]) and $sec_field_arr[$i]=='Y'){
						$hrs_convert=secondsToTime($field_value);
						$field_value=$hrs_convert['h'].':'.$hrs_convert['m'].':'.$hrs_convert['s'];								
					}
					if(!isset($show_in_popup[$i])){
						$show_in_popup[$i]='N';
					}
					
					if(isset($report_link[$i]) && $report_link[$i]!='' && $pass_only_this_arg[$i]==null){
						$link=$report_link[$i];
						if(isset($query_data['country_id']) and $query_data['country_id']!=''){	$link.="&cmbCountry=".$query_data['country_id']; }
			   			if(isset($query_data['state_id']) and $query_data['state_id']!=''){	$link.="&cmbState=".$query_data['state_id'];}
			   			if(isset($query_data['district_id']) and $query_data['district_id']!=''){	$link.="&cmbDist=".$query_data['district_id'];}			   		
						if(isset($query_data['zone_mgr_id']) && $query_data['zone_mgr_id']!=''){$link.="&cmb_zone_mgr={$query_data['zone_mgr_id']}";}
						if(isset($query_data['regional_mgr_id']) && $query_data['regional_mgr_id']!=''){$link.="&cmb_reg_mgr={$query_data['regional_mgr_id']}";}
						if(isset($query_data['terr_mgr_id']) && $query_data['terr_mgr_id']!=''){$link.="&cmb_terr_mgr={$query_data['terr_mgr_id']}";}
						if(isset($GEM_EMP_ID) && $GEM_EMP_ID!=''){$link.="&cmb_ex_name=$GEM_EMP_ID";}
						if(isset($query_data['GTM_VERTICAL_CODE']) && $query_data['GTM_VERTICAL_CODE']!=''){
							$link.="&vertical={$query_data['GTM_VERTICAL_CODE']}";
						}else if(isset($vertical) && isset($query_data['vertical']) and $query_data['vertical']!=0){
							$link.="&vertical=$vertical";
						} 
						if(isset($query_data['GPM_PINCODE']) && $query_data['GPM_PINCODE']!='')
						{$link.="&cmb_pincode={$query_data['GPM_PINCODE']}"; }
						if(isset($query_data['GLC_PRODUCT_CODE']) && $query_data['GLC_PRODUCT_CODE']!='')
						{$link.="&productct_shown[]={$query_data['GLC_PRODUCT_CODE']}"; }
						if(isset($query_data['reference_group'])){$link.="&reference_group={$query_data['reference_group']}";}
						else if(isset($reference_group) and $reference_group){$link.="&reference_group=$reference_group";}
						if(isset($query_data['reference_list'])){	$link.="&reference_list={$query_data['reference_list']}";}
						else if(isset($reference_list) and $reference_list){$link.="&reference_list=$reference_list";}
						if(isset($query_data['reference_list_code'])){	$link.="&reference_list_code={$query_data['reference_list_code']}";}
						else if(isset($reference_list_code) and $reference_list_code){$link.="&reference_list_code=$reference_list_code";}
						if(isset($query_data['GLH_LEAD_SOURCECODE'])){
							$link.="&reference_group=$GLH_LEAD_SOURCECODE&reference_list_code=$GLH_REFERENCE_GIVEN";
						}
						if(isset($query_data['financial_year']) and $query_data['financial_year']!=''){
							$link.="&financial_yr=".$query_data['financial_year'];
						}else if($financial_yr!=''){ $link.="&financial_yr=$financial_yr";} 
						if(isset($query_data['zone_id']) and $query_data['zone_id']!=''){$link.="&cmbZone={$query_data['zone_id']}"; }
				   		if(isset($query_data['region_id']) and $query_data['region_id']!=''){$link.="&cmbRegion={$query_data['region_id']}";}
				   		if(isset($query_data['area_id']) and $query_data['area_id']!=''){$link.="&cmbArea={$query_data['area_id']}";}
				   		if(isset($query_data['terr_id']) and $query_data['terr_id']!=''){$link.="&cmbterr={$query_data['terr_id']}";}
						if(isset($query_data['from_dt']) and $query_data['from_dt']!=''){$from_date=local_date_format($query_data['from_dt']);
							$link.="&from_dt=$from_date";}
						if(isset($query_data['to_dt']) and $query_data['to_dt']!=''){	$to_date=local_date_format($query_data['to_dt']);
						$link.="&to_dt=$to_date";}	
						if(isset($query_data['audit_sp']) and $query_data['audit_sp']!=''){ $link.="&audit_sp={$query_data['audit_sp']}"; }
						if(isset($query_data['pcode']) and $query_data['pcode']!='' && $query_data['pskew']!=''){
							$link.="&prod=$pcode-$pskew";}
						if(isset($query_data['prod']) and $query_data['prod']!='' && $query_data['prod']!=''){
							$link.="&prod=".$query_data['prod']; }	
						if(isset($query_data['support_calls']) and $query_data['support_calls']!=''){
							$link.="&support_calls=$support_calls";
						}
						if(isset($query_data['GLH_CREATED_CATEGORY'])){
							$link.="&created_lead_type={$query_data['GLH_CREATED_CATEGORY']}";
						}
						if(isset($query_data['gdp_create_category'])){
							$link.="&registered_category={$query_data['gdp_create_category']}&registered_date=on";
						}
						global $emp_code,$team;	
						if(isset($query_data['GEM_EMP_ID'])){
							$link.="&emp_code={$query_data['GEM_EMP_ID']}";
						}else if($emp_code!=''){
							$link.="&emp_code=$emp_code&team=$team";
						}
						if(isset($query_data['indiv_order']) && $indiv_order=='on'){
							$link.="&indiv_order=".$indiv_order;
						}
						if(isset($query_data['chain_order']) && $chain_order=='on'){
							$link.="&chain_order=".$chain_order;
						}
						if(isset($query_data['GSM_ID'])){
							$link.="&metric_id=".$query_data['GSM_ID'];
						}
						$title="";						

						$t_var = $mysort[$i];
						$t_v_val=$$t_var; //NOTE: $ -of- $

						if($t_v_val =="[P]"){
							$title=" title='Productwise' ";
						}else if($t_v_val =="[V]"){
							$title=" title='Verticalwise' ";
						}else if($t_v_val =="[E]"){
							$title=" title='Executivewise' ";
						}
						if(isset($query_data['GCH_COMPLAINT_ID'])){
							$link.="&id=".$query_data['GCH_COMPLAINT_ID'];					
						}
						if(isset($query_data['GLH_LEAD_CODE'])){
							$link.="&lcode=".$query_data['GLH_LEAD_CODE'];
						}
						if(isset($query_data['lcode1'])){
							$link.="&lcode1=".$query_data['lcode1'];
						}
						if(isset($query_data['GCA_PARTNER_RELATIONSHIP'])){
							$link.="&cp_relationship=".$query_data['GCA_PARTNER_RELATIONSHIP'];
						}
						if(isset($query_data['CGI_INCHARGE_EMP_ID'])){
							$link.="&incharge_emp_code=".$query_data['CGI_INCHARGE_EMP_ID'];
						}
						if(isset($query_data['CGI_RELATIONSHIP_MANAGER'])){
							$link.="&relational_emp_code=".$query_data['CGI_RELATIONSHIP_MANAGER'];
						}
						if(isset($query_data['GLS_SUBTYPE_CODE'])){
							$link.="&lead_sub_type=".$query_data['GLS_SUBTYPE_CODE'];
						}
						if(isset($query_data['GPL_TAG_NAME']) && isset($query_data['GPL_TAG_ID']) && isset($query_data['GPL_PRICE_LIST_ID'])){
							$link.="&tag_id=".$query_data['GPL_TAG_ID']."&pl_code=".$query_data['GPL_PRICE_LIST_ID'];
						}
						if(isset($query_data['add_pincode'])  && $query_data['add_pincode']==1 ){
							$link.="&add_pincode=".$query_data['GLH_CUST_PINCODE'];
						}
						if((isset($query_data['event_id']) &&  $query_data['event_id']!='' ) or (isset($_REQUEST['event_id']) &&  $_REQUEST['event_id']!='' && $_REQUEST['event_id']!=0)){
							$event_id=(isset($query_data['event_id'])?$query_data['event_id']:(string)$_REQUEST['event_id']);
							$link.="&event_id=".$event_id;
						}
						if($link!=''){
								$link=$_SERVER['SERVER_NAME']."/".$link;
						}
						if($link!='' && ($show_in_popup[$i]=='N' || $show_in_popup[$i]=='')){	
							$value_arr[$array_count][$i]="<a href=\"$link\" target=_blank>".$field_value."</a>";
						}else if($link!='' && $show_in_popup[$i]=='Y'){	
						 	$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$link',4);\" $title >".$field_value."</a>";
						}
							 	
					}
					else if(isset($report_link[$i]) && $report_link[$i]!='' && $pass_only_this_arg[$i]!=null){
						if(array_key_exists('json',$pass_only_this_arg[$i]) ){
							$link=$report_link[$i];
							$title=(isset($title)?$title:'');
							if((is_array($pass_only_this_arg[$i]))){
								for($j=0;$j<count($pass_only_this_arg[$i]);$j++){
									$pass_key = array_keys($pass_only_this_arg[$i]);
									$var_to_pass=$pass_only_this_arg[$i][$pass_key[$j]];
									$json = encode_str($query_data[$var_to_pass]); 
									$link.="?".$pass_key[$j]."=".(isset($query_data[$var_to_pass])?$json:(isset($_REQUEST[$var_to_pass])?(string)$_REQUEST[$var_to_pass]:$var_to_pass));
								}
								if($link!='' && $show_in_popup[$i]=='N'){	
									$value_arr[$array_count][$i]="<a href=\"$link\" target=_blank>".$field_value."</a>";
								}else if($link!='' && $show_in_popup[$i]=='Y'){	
								 	$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$link',4);\" $title >".$field_value."</a>";
								}
							}else if($pass_only_this_arg[$i]=='Y'){/* value become a link */
								if($link!='' && ($show_in_popup[$i]=='N' || $show_in_popup[$i]=='')){	
									$value_arr[$array_count][$i]="<a href=\"$field_value\" target=_blank>".$field_value."</a>";
								}else if($link!='' && $show_in_popup[$i]=='Y'){	
								 	$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$field_value',4);\" $title >".$field_value."</a>";
								}
							}
						}else{
							$link=$report_link[$i];
							$title=(isset($title)?$title:'');
							if((is_array($pass_only_this_arg[$i]))){
								for($j=0;$j<count($pass_only_this_arg[$i]);$j++){
									$var_to_pass=$pass_only_this_arg[$i][$j];
									$link.="&".$var_to_pass."=".(isset($query_data[$var_to_pass])?$query_data[$var_to_pass]:(isset($_REQUEST[$var_to_pass])?(string)$_REQUEST[$var_to_pass]:$var_to_pass));
								}
								if($link!='' && $show_in_popup[$i]=='N'){	
									$value_arr[$array_count][$i]="<a href=\"$link\" target=_blank>".$field_value."</a>";
								}else if($link!='' && $show_in_popup[$i]=='Y'){	
								 	$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$link',4);\" $title >".$field_value."</a>";
								}
							}else if($pass_only_this_arg[$i]=='Y'){/* value become a link */
								if($link!='' && ($show_in_popup[$i]=='N' || $show_in_popup[$i]=='')){	
									$value_arr[$array_count][$i]="<a href=\"$field_value\" target=_blank>".$field_value."</a>";
								}else if($link!='' && $show_in_popup[$i]=='Y'){	
								 	$value_arr[$array_count][$i]="<a href=\"javascript:call_popup('$field_value',4);\" $title >".$field_value."</a>";
								}
							}
						}
					}
					else{
						$value_arr[$array_count][$i]=$field_value;
					}
					if(isset($value_arr_total[$i]) and $value_arr_total[$i]=='Y' and isset($mysort[$i])){
						$t_var=$mysort[$i];
						$t_v_val=$$t_var; //NOTE: $ -of- $
						$value_arr_total_value[0][$i]=(isset($value_arr_total_value[0][$i])?$value_arr_total_value[0][$i]:0)+$t_v_val;
					}else {
						$value_arr_total_value[0][$i]='';
					}
				}//end of else
				
			}//end of for
			$array_count++;
		}//end of while
		
		print_resultset_report_to_mailer($report_content,$value_arr,$value_arr_width,$value_arr_align,$value_arr_class=null,
				$row_class_in_arr,$table_row_id=null,$table_row_style=null,$value_arr_rowspan=null,
				$value_arr_colspan=null,$trow_name,$child);
	
		
		if(isset($value_arr_total) && count($value_arr_total)>0 &&  $count_num_rows>1){
		/*	for($pt=0;$pt<count($value_arr_total);$pt++){
				if($value_arr_total[$pt]=='Y'){
					$value_arr_total_value[0][0] ='Page Total'; $colspan_arr[0][0]=$pt;
				break;
				}
		}*/	
		if($sec_field_arr!=null){
			for($i=0;$i<count($mysort);$i++){
				if(isset($sec_field_arr[$i]) and $sec_field_arr[$i]=='Y'){
					$hrs_convert=secondsToTime((int)$value_arr_total_value[0][$i]);
					$tfield_value=$hrs_convert['h'].':'.$hrs_convert['m'].':'.$hrs_convert['s'];		
					$value_arr_total_value[0][$i]=$tfield_value;						 		
				}
			}
		}

if ($value_arr_total_value[0][1] != ''){
		if((!isset($value_arr_total_value[0][1]) or $value_arr_total_value[0][1]=='') && $navigation==true){
			$value_arr_total_value[0][1]='Page Total';
		}else if((!isset($value_arr_total_value[0][1]) or $value_arr_total_value[0][1]=='') && $navigation==false){
			$value_arr_total_value[0][1]='Total';
		}
}

		print_resultset_report_to_mailer($report_content,$value_arr_total_value,$value_arr_width,$value_arr_align,null,array("highlight_blue"),'page_total',null,$colspan_arr,null,$trow_name,$child);
		}
		if($heading==true){
			$report_content.="</tbody>";
		}
	}//end of if
	
	if($total_query!=null && $count_num_rows>1){
		$report_content.='<tfoot>';
		generate_overall_total_report_to_mailer($report_content,$total_query,$mysort,$report_link,$show_in_popup,$value_arr_align,$sec_field_arr);
		$report_content.='</tfoot>';	
	}
	
	if($heading==true){
		print_dtable_footer_report_to_mailer($report_content);	
	}
	return $report_content;
	
}//end of function
?>
