<?php
require_once(__DIR__ ."/common_util.php");
require_once(__DIR__ ."/inc.new_lead_activity_entry.php");
require_once(__DIR__ ."/product_util.php");
require_once(__DIR__."/lib_ods/ods.php");

function getSheetRows($obj, $sheet){
	return $obj->sheets[$sheet]['rows'];
}

function getCellValue($obj, $sheet,$row,$cell){
	return isset($obj->sheets[$sheet]['rows'][$row][$cell]['value'])?$obj->sheets[$sheet]['rows'][$row][$cell]['value']:'';
}

/**
 * @param string $pincode
 *
 * @return string
 */
function get_state_name_on_pincode($pincode){
	$query= "SELECT p.state FROM gft_pincode_master g, p_map_view p " .
			" WHERE p.district_id=g.GPM_DISTRICT_ID and g.GPM_PINCODE='$pincode' group by p.state";
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)>0){
		$data=mysqli_fetch_array($result);
		return $data['state'];
	}else{
		return null;
	}
}
/**
 * @param string $pincode
 *
 * @return mixed[]
 */
function get_address_details_using_pincode($pincode){
	$return_data	=	/* .(int[string]). */array();
	$query= "SELECT p.district, p.state, p.country FROM gft_pincode_master g, p_map_view p " .
			" WHERE p.district_id=g.GPM_DISTRICT_ID and g.GPM_PINCODE='$pincode' group by p.state";
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)>0){
		$data=mysqli_fetch_array($result);
		$return_data['district']	=	$data['district'];
		$return_data['state']		=	$data['state'];
		$return_data['country']		=	$data['country'];
		return $return_data;
	}else{
		return $return_data;
	}
}
/**
 * @param string $ods_file
 * 
 * @return string[int][int]
 */
function parse_ods_file($ods_file){
	$ret_data = /*. (string[int][int]) .*/array();
	$data_arr = /*. (string[int][int][string]) .*/array();
	$object1 = parseOds($ods_file);
	$data_arr = getSheetRows($object1,0);
	$row_len = count($data_arr);
	$col_len = count($data_arr[0]); 
	for($i=0;$i<$row_len;$i++){
		for($j=0;$j < $col_len;$j++){
			$ret_data[$i][$j] = getCellValue($object1, 0, $i, $j); 	
		}
	}
	return $ret_data;
	
}

/**
 * @param string $attachment
 * @param string $type
 * 
 * @return string[int][int]
 */
function data_from_attachemnet_file($attachment,$type='csv'){
	ini_set("memory_limit", "1024M");
	$dtl_arr = /*. (string[int][int]) .*/array();
	$i=0;
	if($type=='csv'){
		$handle_out = fopen("$attachment","r");
		fseek($handle_out,0);
		while (($data = fgetcsv($handle_out, 10000, ",")) !== FALSE) {
			foreach ($data as $key=>$val){
				$dtl_arr[$i][$key] = trim($val);
			}
			$i++;
		}
		fclose($handle_out);
	}else if($type=='ods'){
		$dtl_arr = parse_ods_file($attachment);
	}
	return $dtl_arr;
}

/**
 * @param string $attachment
 * @param string $type
 * 
 * @return void
 */
function show_in_tabular_form($attachment,$type='csv'){
	global $alt_row_class, $uid; 
	$script_call='';

	$exp_date='';
	$next_action_date='';
	$visit_date='';
	$store_type='';
	$gstin_no='';
	$hardware_status ='';
	$ongo_status = '';
	$state='';
	$street_no='';
	$street_name='';
	$door_no='';
	$landmark='';
	$area_name='';
	$location='';
	$phone_no='';
	$address='';
	$fax_no='';
	$block='';
	$my_comments='';
	$pc_id = 0;
	$duplicate_lead_validation = "";
	if(is_authorized_group_list($uid, array(96,34,24))){
		$duplicate_lead_validation = "<tr><td class='datalabel'>Do you want to create new lead even duplicate contact?</td><td><input type='checkbox' name='allow_duplicate_lead' id='allow_duplicate_lead' value='1'></td></tr>";
	}
	$available_lead_stat = /*. (string[string]) .*/array();
	$stat_query = "select GCS_CODE, GCS_NAME from gft_customer_status_master where GCS_STATUS='A' and GCS_CAN_CHANGE_MANUALLY='Y' and GCS_PRIVILAGE_GROUP_ONLY_EDIT_AFTER='Y' ";
	$res_stat = execute_my_query($stat_query);
	while($row_stat = mysqli_fetch_array($res_stat)){
		$available_lead_stat[$row_stat['GCS_CODE']] = $row_stat['GCS_NAME'];
	}
	$lead_status_arr = get_two_dimensinal_array_from_query($stat_query, "GCS_CODE", "GCS_NAME", "GCS_ORDER_BY","-select-","0");
	
	echo "<br>";
	$i=0;
	$s=0;
	$num=0;
	$import_simple=false;$headings_display="";$title="New Lead Entry";

	$i=0;
	print_dtable_header($title);
	echo '<table width="100%" border=1 class="Formborder1" id="new_lead_entry_table">' . 
			'<thead><form name="new_lead_entry" id="new_lead_entry" method="post" action="import_lead.php"">' .
	 	  	'<input type="hidden" name="new_lead_entry_submit" id="new_lead_entry_submit" value="true">' .
			'<input type="hidden" name="employee_code" id="employee_code" value="">' .
			'<input type="hidden" name="employee_name" id="employee_name" value=""></thead>';
	$data = data_from_attachemnet_file($attachment,$type);
	$num = count($data);
	 while($i < $num) {
		$title_2='';       
		if($i==0){
			if($data[$i][0]=="* Business / Shop Name" && $data[$i][1]=="Contact Name" && $data[$i][2]=="* Mobile No"
			&& $data[$i][3]=="Email Id" && $data[$i][4]=="* Area / City" && $data[$i][5]=="Pincode" 
			&& $data[$i][6]=="Address" && $data[$i][7]=="State" && $data[$i][8]=="Store Type" && $data[$i][9]=="GSTIN"
			&& $data[$i][10]=="PC Name"){
				$headings=$data[$i];
				$import_simple=true;
				$import_simple_value="1";
 		echo '<input type="hidden" name="import_simple" id="import_simple" value='.$import_simple_value.'>';
		echo '<tr class="modulelisttitle" style="height: 20"  >' .
				'<th>S.No <input type="checkbox" name="accept_a" id="accept_a" onchange="javascript:accepta();"/></th><th>Demo Shown</th>';
for($col=0;$col<count($headings);$col++){
echo<<<END
<th>$headings[$col]</th>
END;
}//end of for loop
			echo '</tr>';
			
			}else{	
				$red_mark = "<font color=red size=3>*</font>";
				$headings=array("$red_mark Customer Name","Authority Name","Contact No","$red_mark Mobile No","Fax","Email-Id", "$red_mark Lead Status", 
						"My Comments","Door/ Apartment No","Block / Society / Complex / Plaza",
						"Avenue / Street / Cross No","Landmark","Street Name",
						"Location","Area Name","City / Dist / Taluk","State","Pincode",
						"Visit Date","Next Action Date","Next Action","Date of Closure ","Vertical");
				$title="New Lead Entry";
				for($col=0;$col<count($headings);$col++){
					$title_2.="<span id=\"show($col+2)\" style=\"display:none\">" .
							"<img title=\"$headings[$col]\" src=\"images/right_arrow.jpeg\" " .
							"width=\"10\" hieght=\"10\" border=\"0\" " .
							"onclick=\"javascript:show_this_col('new_lead_entry_table',($col+2),($col+2),'show($col+2)','','',1);\" > </span> "; 
				}
				
			echo '<table width="100%" class="Formborder1" id="new_lead_entry_table">' .
				'<tr class="modulelisttitle" style="height: 20">' .
				'<th colspan="'.(count($headings)+2).'" align="left">'.$title_2.'</th></tr>' .
				'<tr class="modulelisttitle" style="height: 20"  >' .
				'<th>S.No <input type="checkbox" name="accept_a" id="accept_a" onchange="javascript:accepta();"/></th><th>Demo Shown</th>';
			for($col=0;$col<count($headings);$col++){
echo<<<END
<th>$headings[$col]<span show_status="show">
<img src="images/left_arrow.jpeg" width="10" hieght="10" border="0" 
onclick="javascript:hide_this_col('new_lead_entry_table',($col+2),($col+2),'show($col+2)','','',1);" ></th>
END;
			}
			echo '</tr>';	
			
			}
			$i++;
			continue;	
  					
		}//end of first row read
		else{
			//Data
			if($import_simple==true){
				$cust_name=makeValidData($data[$i][0]);
				$auth_name=makeValidData($data[$i][1]);
				$mobile_no=trim($data[$i][2]);
				$email_id=trim($data[$i][3]);
				$city=makeValidData($data[$i][4]);
				$pincode=makeValidData(trim($data[$i][5]));
				$address=makeValidData(trim($data[$i][6]));
				$state	=	makeValidData(trim($data[$i][7]));
				$store_type	=	trim($data[$i][8]);
				$gstin_no = trim($data[$i][9]);
				$pc_name = trim($data[$i][10]);	
				$lead_reference_id = isset($data[$i][11])?trim($data[$i][11]):"";
				$hardware_status = isset($data[$i][12])?trim($data[$i][12]):"";
				$ongo_status = isset($data[$i][13])?trim($data[$i][13]):"";
				$additional_note = isset($data[$i][14])?trim($data[$i][14]):'';
				if($cust_name=='' && $auth_name=='' && $mobile_no==''){
					continue;
				}
				if($pincode==''){
					if($address!=''){
						$result_get_pincode=execute_my_query("select gpm_pincode from gft_pincode_master where ".	
						"GPM_LOCATION_NAME like '$address%' ");
						if(mysqli_num_rows($result_get_pincode)>0){
								$qdp=mysqli_fetch_array($result_get_pincode);
								$pincode=$qdp['gpm_pincode'];
						}		
					}
					
					if($city!='' && $pincode==''){
						$result_get_pincode=execute_my_query("select gpm_pincode from gft_pincode_master where ".
						"GPM_LOCATION_NAME like '$city%' ");
						if(mysqli_num_rows($result_get_pincode)==0){
								$qdp=mysqli_fetch_array($result_get_pincode);
								$pincode=$qdp['gpm_pincode'];									
						}	
					}
					
				}
			}else{
				$cust_name=makeValidData($data[$i][0]);
				$auth_name=makeValidData($data[$i][1]);
				$phone_no=trim($data[$i][2]);
				$mobile_no=trim($data[$i][3]);
				$fax_no=trim($data[$i][4]);
				$email_id=trim($data[$i][5]);
				$lead_status_name = trim($data[$i][6]);
				$lead_status = array_search($lead_status_name, $available_lead_stat);
				$door_no=makeValidData($data[$i][7]);
				$block=makeValidData($data[$i][8]);
				$street_no=makeValidData($data[$i][9]);
				$landmark=makeValidData($data[$i][10]);
				$street_name=makeValidData($data[$i][11]);
				$location=makeValidData($data[$i][12]);
				$area_name=makeValidData($data[$i][13]);
				$city=makeValidData($data[$i][14]);
				$state=makeValidData($data[$i][15]);
				$pincode=trim($data[$i][16]);
				
				if(substr($data[$i][17],-3,1)=='/')$data[$i][17]=substr($data[$i][17],1,5).'/20'.substr($data[$i][17],-2,2);
				if(substr($data[$i][18],-3,1)=='/')$data[$i][18]=substr($data[$i][18],1,5).'/20'.substr($data[$i][18],-2,2);
				if(substr($data[$i][19],-3,1)=='/')$data[$i][19]=substr($data[$i][19],1,5).'/20'.substr($data[$i][19],-2,2);
				$visit_date=db_date_format(str_replace("/","-",$data[$i][17]));
				$next_action_date=db_date_format(str_replace("/","-",$data[$i][18]));
				if($data[$i][19]!=''){
					$exp_date=db_date_format(str_replace("/","-",$data[$i][19]));
				}else{
					$exp_date=date('d-m-Y',mktime('0','0','0',(int)date('m')+1,(int)date('d'),(int)date('Y')));
				}
			    //$next_action=(isset($data[$i][20])?$data[$i][20]:$exp_date);
				$next_action=3;
				$my_comments=(isset($data[$i][20])?makeValidData($data[$i][20]):"");
				//$vertical=(isset($data[$i][21])?$data[$i][21]:20);
			}
			if($state=='' and $pincode!=''){
					$state=get_state_name_on_pincode($pincode);
			}
			$pincode_check="true";
		  /*   $rs=check_lead_entry($cust_name,$auth_name,$door_no,$street_no,$street_name,$landmark,
			    				$area_name,$location,$city,$pincode,$state,$phone_no);
		    if($rs!='') {
		    	show_my_alert_msg("Lead data in Row $i already exists in Customer Id:$rs");
		    	$i++;
		    	continue;
		    } */
			$rcheck_pincode_exists=execute_my_query("select * from gft_pincode_master where gpm_pincode='$pincode'");
			$check_pincode_exists=(mysqli_num_rows($rcheck_pincode_exists)<0?'':$pincode);
			
			echo '<tr style="height: 20" class='.$alt_row_class[$s].
				' onMouseOver="this.style.backgroundColor=\'#C8DC9B\';" ' .
				'onMouseOut="this.style.backgroundColor=\'\';">' .
				'<td nowrap>'.
				'<input type="checkbox" id="accept['.$i.']" name="accept['.$i.']" value="checked" checked> '.$i.'</td>';
			echo '<td>Demo <input type="checkbox" id="demo_act['.$i.']" name="demo_act['.$i.']">'	;   
			if($import_simple==true){
			echo '<td><input type="text" name="customer_name['.$i.']" id="cust_name['.$i.']"  ' .
				'onkeypress="javascript:return removeInvalidChar(this,event);" ' .
				'onblur="javascript:makeStdWords(this);" value="'.$cust_name.'"
				 class="formStyleTextarea" size="30"></td>' .
				'<td><input type="text" name="auth_name['.$i.']" id="auth_name['.$i.']" ' .
				'class="formStyleTextarea" value="'.$auth_name.'"></td>' .
				'<td><input type="text" name="mobile_no['.$i.']" id="mobile_no['.$i.']" size="15" ' .
				'class="formStyleTextarea" value="'.$mobile_no.'"></td>' .
				'<td><input type="text" name="email_id['.$i.']" id="email_id['.$i.']" size="15" ' .
				'class="formStyleTextarea" value="'.$email_id.'"></td>' .
				'<td><input type="text" name="city['.$i.']" id="city['.$i.']" size="15" ' .
				' class="formStyleTextarea"  value="'.$city.'"></td>' .
				'<td><input type="text" name="pincode['.$i.']" id="pincode_'.$i.'" ' .
				' size="10" class="formStyleTextarea"  value="'.$pincode.'"
				onkeypress="javascript:return removeInvalidChar(this,event);"  
				onblur="onReplaceDigit(this);setStyleClassName(this,\'normal_autocomplete\');
				makeStdWords(this);" onfocus="setStyleClassName(this,\'focus_autocomplete\');" 
				autocomplete="off" >
				<input type="hidden" id="pcode_'.$i.'" name="pcode['.$i.']" value="'.$check_pincode_exists.'">
				</td>' .
				'<td><input type="text" name="address['.$i.']" id="address['.$i.']" size="15" ' .
				' class="formStyleTextarea"  value="'.$address.'"></td>' .
				'<script type="text/javascript">  ' .
				"new AjaxJspTag.Autocomplete(
	'list_location_from_pincode.php', {
	minimumCharacters: '1',
	parameters: 'keyword=GLH_CUST_PINCODE&fvalue={pincode_$i}',
	progressStyle: 'throbbing',
	blockNoResultStatus: '1',
	target: 'pincode_$i',
	className: 'autocomplete',
	source: 'pincode_$i'});".'</script>'.
			'<td><input type="text" name="state['.$i.']" id="state['.$i.']" size="15" ' .
				'class="formStyleTextarea" value="'.$state.'"></td>' .
			'<td><input type="text" name="store_type['.$i.']" id="store_type['.$i.']" size="15" ' .
				'class="formStyleTextarea" value="'.$store_type.'"></td>' .
			'<td><input type="text" name="gstin_no['.$i.']" id="gstin_no['.$i.']" size="15" ' .
				'class="formStyleTextarea" value="'.$gstin_no.'"></td>' .
			'<td><input type="text" name="pc_name['.$i.']" id="pc_name['.$i.']" size="15" ' .
				'class="formStyleTextarea" value="'.$pc_name.'"></td>'.
			'<td><input type="text" name="lead_reference_id['.$i.']" id="lead_reference_id['.$i.']" size="15" ' .
				'class="formStyleTextarea" value="'.$lead_reference_id.'"></td>'.
			'<td><input type="text" name="hardware_status['.$i.']" id="hardware_status['.$i.']" size="15" ' .
				'class="formStyleTextarea" value="'.$hardware_status.'"></td>'.
			'<td><input type="text" name="ongo_status['.$i.']" id="ongo_status['.$i.']" size="15" ' .
				'class="formStyleTextarea" value="'.$ongo_status.'"></td>'.
			'<td><input type="text" name="add_notes['.$i.']" id="add_notes['.$i.']" size="15" ' .
				'class="formStyleTextarea" value="'.$additional_note.'"></td>';			
			}else { 	 
					echo '<td><input type="text" name="customer_name['.$i.']" id="cust_name'.$i.'"  ' .
						'onkeypress="javascript:return removeInvalidChar(this,event);" ' .
						'onblur="javascript:makeStdWords(this);" value="'.$cust_name.'"
						 class="formStyleTextarea" size="30"></td>' .
						'<td><input type="text" name="auth_name['.$i.']" id="auth_name['.$i.']" ' .
						'class="formStyleTextarea" value="'.$auth_name.'"></td>' .
						'<td><input type="text" name="ph_no['.$i.']" id="ph_no['.$i.']" size="15" ' .
						'class="formStyleTextarea" value="'.$phone_no.'"></td>' .
						'<td><input type="text" name="mobile_no['.$i.']" id="mobile_no'.$i.'" size="15" ' .
						'class="formStyleTextarea" value="'.$mobile_no.'"></td>' .
						'<td><input type="text" name="fax_no['.$i.']" id="fax_no['.$i.']" size="15" ' .
						'class="formStyleTextarea" value="'.$fax_no.'"></td>' .
						'<td><input type="text" name="email_id['.$i.']" id="email_id['.$i.']" size="15" ' .
						'class="formStyleTextarea" value="'.$email_id.'"></td>' .
						'<td>'.fix_combobox_with("lead_status_arr$i", "lead_status_arr[$i]", $lead_status_arr, $lead_status).'</td>'.
						'<td><textarea name="my_comments['.$i.']" id="my_comments['.$i.']" ' .
						' class="formStyleTextarea" onkeypress="javascript:return removeInvalidChar(this,event);"
						 onblur="javascript:makeStdWords(this);">'.$my_comments.'</textarea></td>' .
						'<td><input type="text" name="door_no['.$i.']" id="door_no['.$i.']" ' .
						' value="'.$door_no.'" class="formStyleTextarea" 
						 onkeypress="javascript:return removeInvalidChar(this,event);" 
						 onblur="javascript:makeStdWords(this);"> </td>' .
						'<td><input type="text" name="block['.$i.']" id="block['.$i.']" ' .
						' value="'.$block.'" class="formStyleTextarea" onkeypress="javascript:return removeInvalidChar(this,event);" 
						 onblur="javascript:makeStdWords(this);"> </td>' .
						 '<td><input type="text" name="street_no['.$i.']" id="street_no['.$i.']" ' .
						' value="'.$street_no.'" class="formStyleTextarea" onkeypress="javascript:return removeInvalidChar(this,event);" 
						 onblur="javascript:makeStdWords(this);"> </td>' .
						 '<td><input type="text" name="landmark['.$i.']" id="landmark['.$i.']" ' .
						' value="'.$landmark.'" class="formStyleTextarea" onkeypress="javascript:return removeInvalidChar(this,event);" 
						 onblur="javascript:makeStdWords(this);" > </td>' .
						 '<td><input type="text" name="street_name['.$i.']" id="street_name['.$i.']" ' .
						' value="'.$street_name.'" class="formStyleTextarea" onkeypress="javascript:return removeInvalidChar(this,event);" 
						 onblur="javascript:makeStdWords(this);"
						 onfocus="setStyleClassName(this,\'focus_autocomplete\');"> </td>' .
						 '<td><input type="text" name="location['.$i.']" id="location['.$i.']" ' .
						' value="'.$location.'" class="formStyleTextarea" onkeypress="javascript:return removeInvalidChar(this,event);" 
						 onblur="javascript:makeStdWords(this);" > </td>' .        
						'<td><input type="text" name="area_name['.$i.']" id="area_name['.$i.']" ' .
						'onkeypress="javascript:return removeInvalidChar(this,event);" 
						 onblur="javascript:makeStdWords(this);" class="formStyleTextarea" value="'.$area_name.'" $BLOCK_SPECIAL_CHARACTERS></td>' .
						'<td><input type="text" name="city['.$i.']" id="city['.$i.']" size="15" ' .
						' class="formStyleTextarea"  value="'.$city.'"></td>' .
						'<td><input type="text" name="state['.$i.']" id="state['.$i.']" size="15" ' .
						'class="formStyleTextarea"  value="'.$state.'"></td>' .
						'<td><input type="text" name="pincode['.$i.']" id="pincode['.$i.']" ' .
						' size="10" class="formStyleTextarea"  value="'.$pincode.' " onkeyup="javascript:extractNumber(this,0,false);"  maxlength="6" 
						onkeypress="javascript:return blockNonNumbers(this, event, false, false);" 
						onblur="javascript:  makeStdWords(this);" ></td>' . 
						'<td nowrap><input type="text" size="12" name="visit_date_arr['.$i.']" id="visit_date_arr['.$i.']" ' .
						'class="formStyleTextarea" value="'.$visit_date.'"> ' .
						'<script type="text/javascript">init_date_func_without_button("visit_date_arr['.$i.']","%Y-%m-%d","","Bl");</script>'.
						'</td>' .
						'<td nowrap><input type="text" name="next_action_date['.$i.']" id="next_action_date['.$i.']"  size="12" ' .
						'class="formStyleTextarea" value="'.$next_action_date.'">' .
						'<script type="text/javascript">init_date_func_without_button("next_action_date['.$i.']","%Y-%m-%d","","Bl");</script>'.
						'</td>' .
						'<td><select name="next_action['.$i.']" id="next_action['.$i.']" class="formStyleTextarea">';
					$activtiy_list=get_activity_list();
					echo "<option value=\"0\" >Select</option>";	
					for($a=0;$a<count($activtiy_list);$a++){        
						$sopt="";
						$act_id=(int)$activtiy_list[$a][0];
						$act_id_desc=$activtiy_list[$a][1];
						if($act_id==1 or  $act_id>5){
							continue;
						}
						echo "<option value=\"$act_id\" $sopt>$act_id_desc</option>";
					}
					echo '</select></td>';
					echo '<td nowrap><input type="text" name="exp_date['.$i.']" id="exp_date['.$i.']" size="12" ' .
						' class="formStyleTextarea" value="'.$exp_date.'" >' .
						'<script type="text/javascript">init_date_func_without_button("exp_date['.$i.']","%Y-%m-%d","","Bl");</script></td>';
					echo '<td><select name="vertical_arr['.$i.']" id="vertical_arr['.$i.']" class="formStyleTextarea">' ;
					$vertical_type_list=get_vertical_name();
					for($v=0;$v<count($vertical_type_list);$v++){
						echo "<option value=\"".$vertical_type_list[$v][0]."\">".$vertical_type_list[$v][1]."</option>";
					}
					echo '</select></td></tr>' ;
					}
					$i++;
				$s=($s==1?0:1);
			} 
		}
		$mandatory_mark = '<font color="red" size="3">*</font>';
		$type_of_customer=get_type_list();
		$que1 = "select GTM_VERTICAL_CODE,GTM_VERTICAL_NAME from gft_vertical_master where GTM_STATUS='A'";
		$vertical_list = get_two_dimensinal_array_from_query($que1, "GTM_VERTICAL_CODE", "GTM_VERTICAL_NAME",' GTM_IS_MACRO desc,GTM_VERTICAL_NAME');
		$vertical_combo = fix_combobox_with("vertical_id", "vertical_id", $vertical_list, '','','-select-');
		
		$que2 = "select GEM_EMP_ID,GEM_EMP_NAME from gft_emp_master where GEM_STATUS='A' ";
		$emply_list = get_two_dimensinal_array_from_query($que2, "GEM_EMP_ID", "GEM_EMP_NAME",'');
		$created_by_combo = fix_combobox_with("created_by", "created_by", $emply_list, '','','-select-');
		$assign_to_combo  = fix_combobox_with("assign_to", "assign_to", $emply_list, '','','-select-');
		
		echo '</table><br>';
		echo '<table border=1 width="500">' ;
		echo "<tr><td class='datalabel' width='150'>$mandatory_mark Created By</TD><td>$created_by_combo</td></tr>";
		echo "<tr><td class='datalabel' width='150'>$mandatory_mark Assign To</TD><td>$assign_to_combo</td></tr>";
		echo "<tr><TD class='datalabel' width='150'>$mandatory_mark Business Type</TD>" .
			'<TD>'.fix_combobox_with('type_of_cust','type_of_cust',$type_of_customer,'','','Select',null,false,"onchange='javascript: show_cp_details();'").'</td></tr>';
		echo "<tr><td class='datalabel' width='150'>Vertical</TD><td>$vertical_combo</td></tr>";
		echo '<tr><td colspan=2>';
		customer_type($tab_index=0);
		echo '</td>';
		echo '<tr><td colspan=2>';
		show_reference_details('','','','','','');
		echo '</td>';
	$tab_index++;
	product_details_demo();
	$next_activtiy_list=get_activity_list_with_group(null,true);
	$na_combo = fix_combobox_with('na','na',$next_activtiy_list,0,0,'Select',null,true);
	echo "<tr><td class='datalabel'>$mandatory_mark Activity Content</td><td><textarea id='activity_note' name='activity_note' cols='50' rows='4' ></textarea></td></tr>".
	   	"<tr><td class='datalabel'>Next Action</td><td>$na_combo</td></tr>".
	   	"<tr><td class='datalabel'>Next Action Date</td><td><input name='donv' type='text' class='formStyleTextarea' id='donv' ondblclick=\"javascript:this.value='';\"  onchange=\"javascript:date_check_should_be_greater('donv');\"></td></tr>".
		$duplicate_lead_validation.'<tr><td colspan=2 align="center" ><input type="button" class="button" id="save_dtl" name="save_dtl" tot_row="'.($i-1).'"' .
		'value="Save" title="Save[Alt+S]" accessKey="S">' ;					
	echo '</table></form>';
echo<<<END
<script type="text/javascript">
$script_call
var jq = jQuery.noConflict();
jq(document).ready(function(){
	jq("#created_by").select2();
	jq("#assign_to").select2();
	jq("#save_dtl").click(function(){
		var len = jq("#save_dtl").attr("tot_row");
		for(i=1;i<=len;i++){
			var cust_name = jq("#cust_name"+i).val();
			var mob_no = jq("#mobile_no"+i).val();
			var lead_stat = jq("#lead_status_arr"+i).val();
			if(cust_name==''){
				alert("Please Enter a Customer Name in Row "+i);
				return false;	
			}
			if(mob_no==''){
				alert("Mobile Number is empty in Row "+i);
				return false;			
			}
			if(lead_stat=='0'){
				alert("Please Choose a Lead Status in Row "+i);
				return false;
			}
		}
		gather_info();
	});
});
</script>
END;
}
?>
