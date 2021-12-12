<?php
require_once(__DIR__ ."/product_util.php");
require_once(__DIR__ ."/common_filter.php");

/**
 * @param int $tab_index
 * @param int $new_comp
 * 
 * @return string
 */
function enter_comp_details($tab_index,$new_comp=0){
	$return_string="";
	$tab_indexes[0]=$tab_index++;
	$tab_indexes[1]=$tab_index++;
	$tab_indexes[2]=$tab_index++;
	$tab_indexes[3]=$tab_index++;
	$tab_indexes[4]=$tab_index++;
	$tab_indexes[5]=$tab_index++;
	$tab_indexes[6]=$tab_index++;
	$tab_indexes[7]=$tab_index++;
	$tab_indexes[8]=$tab_index++;
	
	$product_list=get_product_list_family('','A','1','',true);
	$product_gft_list="";
	for($i=0;$i<count($product_list);$i++)
	{
		$pcode=$product_list[$i][0];
		$pabr=$product_list[$i][2];
		$product_gft_list.="<option value=\"$pcode\">$pabr</option>";
	}
$return_string.=<<<END
<script type="text/javascript" >
function popitup_comp_master(){
		document.getElementById("comp_master").className="unhide";
}
</script>
<table><tr><td><table>
<tr><TD class="datalabel"><font color="red" size="3" >*</font>Competitor Name</TD>
<td colspan="3">
END;
if($new_comp==1){
	$return_string.=get_competitor_list($tab_indexes[0],'compet_name','compet_name',
	'competCode','competCode',null,null,$new_comp);
	$cmp_loc='cmp_loc1';$ex_cmp_loc='ex_cmp_loc1';$cmp_info='cmp_info1';$ex_cmp_info='ex_cmp_info1';
}else{
	$return_string.=get_competitor_list($tab_indexes[0],'comp_name','comp_name','compCode','compCode',null,null,$new_comp);
	$cmp_loc='cmp_loc';$ex_cmp_loc='ex_cmp_loc';$cmp_info='cmp_info';$ex_cmp_info='ex_cmp_info';
}
$return_string.=<<<END
</td><tr><td colspan="4">
<div id="comp_master"  class="hide">
<table><TR><TD class="datalabel" width="170">Office Location</TD>
<TD width="250"><INPUT type="text" id="$cmp_loc" name="$cmp_loc"  tabindex="$tab_indexes[1]" onKeypress="javascript:return block_sp_char(this,event);">
<INPUT type="hidden" id="$ex_cmp_loc" name="$ex_cmp_loc"></TD>
<td valign="top" class="datalabel" width="170">Competitor Info </td>
<td width="250"><textarea id="$cmp_info"  name="$cmp_info" rows="4" cols="26" onKeypress="javascript:return block_sp_char(this,event);" tabindex="$tab_indexes[1]" ></textarea>
<INPUT type="hidden" id="$ex_cmp_info"  name="$ex_cmp_info"></td></tr></table></div></td></tr>
<TR><TD class="datalabel" width="170"><font color="red" size="3" >*</font> Competitor Product Name</TD>
<TD width="250">
END;
if($new_comp==1){
	$return_string.=get_competitor_product_list($tab_indexes[1],'compet_prod_name','compet_prod_name',
	'compet_prod_Code','compet_prod_Code','competCode',null); 
	$doi_name="doi1";$doi_id="doi1";$ex_doi="ex_doi1";$img_id='onceDateIcon_doi1';$comp_gft_product_code='comp_gft_product_code';
	$ex_gft_product_code='comp_ex_gft_product_code';$pc='comp_pc';$av='comp_av';$ex_av='comp_ex_av';
	$comp_custbase='compet_custbase';$ex_comp_custbase='ex_compet_custbase';$cd='comp_cd';$ex_cd='comp_ex_cd';$prod_desc='comp_prod_desc';$ex_prod_desc='comp_ex_prod_desc';
}else{
	$return_string.=get_competitor_product_list($tab_indexes[1],'comp_prod_name','comp_prod_name','comp_prod_code','comp_prod_code','compCode',null);
$doi_name="doi";$doi_id="doi";$ex_doi="ex_doi";$img_id='onceDateIcon_doi';$comp_gft_product_code='gft_product_code';
$ex_gft_product_code='ex_gft_product_code';$pc='pc';$av='av';$ex_av='ex_av';
$comp_custbase='comp_custbase';$ex_comp_custbase='ex_comp_custbase';$cd='cd';$ex_cd='ex_cd';$prod_desc='prod_desc';$ex_prod_desc='ex_prod_desc';
}
$return_string.=<<<END
</TD><TD class="datalabel" width="170" ><font color="red" size="3" >*</font> Our Product </TD>
<TD><select tabindex="$tab_indexes[2]" name="$comp_gft_product_code" id="$comp_gft_product_code"  class="formStyleTextarea">
<option value='0'>Select</option>$product_gft_list</select>
<input type="hidden" name="$ex_gft_product_code" id="$ex_gft_product_code"></TD></TR>
<TR><td align="left" class="datalabel" ><font color="red" size="3" >*</font>Date of Installation</td>
<td><input type="text" name="$doi_name" id="$doi_id"  class="formStyleTextarea"  readonly>&nbsp;<img src="images/date_time.gif" Class="imagecur" id="$img_id" width="16" height="16" border="0" align="middle" alt="" tabindex="$tab_indexes[3]">
<script type="text/javascript"> init_date_func("$doi_name","%Y-%m-%d","$img_id","Bl"); </script>
<input type="hidden" name="$ex_doi" id="$ex_doi"></td> 
<TD class="datalabel"><font color="red" size="3" >*</font> Performance Rating</TD>
<TD><select id="$pc" name="$pc" tabindex="$tab_indexes[4]"  class="formStyleTextarea">
<option value='0'>select</option>
<option value="Very Good" >Very Good</option>
<option value="satisfactory">Satisfactory</option>
<option value="poor">Poor</option>
<option value="very poor">Very poor</option>
</select></TD></TR>
<TR><TD class="datalabel">Approx value of deal</TD>
<TD><INPUT  tabindex="$tab_indexes[6]" name="$av" id="$av" size="29" onkeyup="javascript:extractNumber(this,0,false);"  class="formStyleTextarea" onkeypress="javascript:return blockNonNumbers(this, event, false, false);">
<INPUT type="hidden" name="$ex_av" id="$ex_av">
<INPUT id="$comp_custbase" name="$comp_custbase" size="29" tabindex="$tab_indexes[5]" onkeyup="javascript:extractNumber(this,0,false);"   class="formStyleTextarea" onkeypress="javascript:return blockNonNumbers(this, event, false, false);"  type="hidden">
<INPUT id="$ex_comp_custbase" name="$ex_comp_custbase" type="hidden" ></TD></TR>
<tr><TD valign="top" class="datalabel">Competition Details</TD>
<TD><textarea id="$cd" name="$cd" tabindex="$tab_indexes[7]" rows="4" cols="26" class="formStyleTextarea" onKeypress="return block_sp_char(this,event);" ></textarea>
<INPUT type="hidden" name="$ex_cd" id="$ex_cd" ></TD>
<td valign="top" class="datalabel">Product Description </td>
<td><textarea id="$prod_desc" name="$prod_desc" tabindex="$tab_indexes[8]" rows="4" cols="26" class="formStyleTextarea" onKeypress="return block_sp_char(this,event);" ></textarea>
<INPUT type="hidden" name="$ex_prod_desc" id="$ex_prod_desc" ></td>
</tr></table></td></tr></table>
END;
return $return_string;
}

/**
 * @param int $tab_index
 * 
 * @return void
 */
function enter_competitor_details($tab_index){
echo<<<END
<table>
END;
	echo "<tr><td>1.</td><td>";
	echo get_competitor_list($tab_index,$field_name='competitor_name[1]',$field_id='competitor_name_1',
$field_code_name='competitorCode[1]',$field_code_id='competitorCode_1');

	echo "</td><td>2.</td><td>";
	echo get_competitor_list($tab_index,$field_name='competitor_name[2]',$field_id='competitor_name_2',
	$field_code_name='competitorCode[2]',$field_code_id='competitorCode_2');

echo "</td><tr><td>3.</td><td>";
	echo get_competitor_list($tab_index,$field_name='competitor_name[3]',$field_id='competitor_name_3',
	$field_code_name='competitorCode[3]',$field_code_id='competitorCode_3');

echo "</td><td>4.</td><td>";
	echo get_competitor_list($tab_index,$field_name='competitor_name[4]',$field_id='competitor_name_4',
	$field_code_name='competitorCode[4]',$field_code_id='competitorCode_4');

echo "</td><tr><td>5.</td><td>";
	echo get_competitor_list($tab_index,$field_name='competitor_name[5]',$field_id='competitor_name_5',
	$field_code_name='competitorCode[5]',$field_code_id='competitorCode_5');

echo "</td><td>6.</td><td>";
	echo get_competitor_list($tab_index,$field_name='competitor_name[6]',$field_id='competitor_name_6',
	$field_code_name='competitorCode[6]',$field_code_id='competitorCode_6');	
	
echo<<<END
</table>
END;
}

/**
 * @param string $competitor_name
 * @param string $competitor_location
 * @param string $competitor_info
 * 
 * @return string
 */
function insert_competitor_name($competitor_name,$competitor_location=null,$competitor_info=null){
	$query="insert into  gft_competitor_master (GCM_COMPETE_COMPANY_NAME,GCM_COMPETE_OFFICE_LOCATION,GCM_COMPETE_INFO)
	values('$competitor_name','$competitor_location','$competitor_info')";
	execute_my_query($query);
	$query_lst="select last_insert_id()";
	$result_lst=execute_my_query($query_lst);
	$qd=mysqli_fetch_array($result_lst);
	$competitor_code=$qd[0];
	return $competitor_code;
}

/**
 * @param string $comp_name
 * @param string $comp_code
 * @param string $GCM_OFF_LOC
 * @param string $GCM_COMPETE_INFO
 * @param string $GLC_PRODUCT_NAME
 * @param string $PROD_CODE
 * @param string $GCM_PRODUCT_CUSTBASE
 * @param string $GLC_APPROX_VALUE
 * @param string $comp_details
 * @param string $GCM_PROUCT_DESC
 * @param string $GLC_PERF_CODE
 * @param string $GLC_INSTALL_DATE
 * @param string $lead_code
 * @param string $territory_id
 * @param string $GLC_EMP_ID
 * 
 * @return string
 */
function submit_comp_details($comp_name,$comp_code,$GCM_OFF_LOC,$GCM_COMPETE_INFO,$GLC_PRODUCT_NAME,$PROD_CODE,
					$GCM_PRODUCT_CUSTBASE,$GLC_APPROX_VALUE,$comp_details,$GCM_PROUCT_DESC,$GLC_PERF_CODE,
					$GLC_INSTALL_DATE,$lead_code,$territory_id,$GLC_EMP_ID){
	/* if($comp_code=='' && $comp_name!=''){
		$comp_code=insert_competitor_name($comp_name,$GCM_OFF_LOC,$GCM_COMPETE_INFO);
		
	} */
	$GLC_PRODUCT_NAME=ucwords($GLC_PRODUCT_NAME); 	
	$query1="replace into gft_lead_compete_dtl (GLC_LEAD_CODE,GLC_COMPETE_CODE,GLC_COMP_CODE,GLC_COMPETITOR_NAME," .
	"GLC_COMPETE_PRODUCT_NAME,GLC_COMPETE_FLAG,GLC_INSTALL_DATE,GLC_APPROX_VALUE,GLC_PERF_CODE," .
	"GLC_PRODUCT_DESC,GLC_PRODUCT_CODE)" .
	" values ('$lead_code','$comp_code','$comp_code','$comp_name','$GLC_PRODUCT_NAME'," .
	"'$comp_details','$GLC_INSTALL_DATE','$GLC_APPROX_VALUE','$GLC_PERF_CODE'," .
	"'$GCM_PROUCT_DESC','$PROD_CODE')";
	$query2="replace into gft_compete_dtl (GCM_COMPETE_CODE,GCM_PRODUCT_NAME)" .
		"values('$comp_code','$GLC_PRODUCT_NAME')";
	$result1=execute_my_query($query1); 
	$result2=execute_my_query($query2); 
	if($result1 and $result2)	{
		$status_r="saved";		
	}else{
		$status_r="Competitor detail is not inserted";
	}
	return  $status_r;
}

/**
 * @param string $lead_code
 * @param string[int] $ex_competitorCode
 * @param string[int] $competitorCode
 * @param string[int] $competitor_name
 * @param string $emp_id
 * 
 * @return void
 */
function competitor_approaching_lead($lead_code,$ex_competitorCode,$competitorCode,$competitor_name,$emp_id){
		$ins_query="insert into gft_competitor_approach_lead(GCA_LEAD_CODE ,GCA_COMPETITOR_CODE ,GCA_COMPETITOR_NAME,
		GCA_UPDATED_BY ,GCA_UPDATED_DATE)values";
		$execute_cdtl=false; $put_comma="";
		if(is_array($competitorCode)){
			for($i=0;$i<count($competitorCode);$i++){
				if($competitorCode[$i]==$ex_competitorCode[$i]){
					continue;
				}
				else if($competitorCode[$i]!=$ex_competitorCode[$i] && $ex_competitorCode[$i]!=0){
					$competitor_code=$competitorCode[$i];
						if($competitor_code==0 && $competitor_name[$i]!=''){
							$competitor_code=insert_competitor_name($competitor_name[$i]);
						}
						if($competitor_code!=0){
							$lead_comp_update_arr['GCA_COMPETITOR_CODE']=$competitor_code;
							$lead_comp_update_key_arr['GCA_LEAD_CODE']=$lead_code;
							$lead_comp_update_key_arr['GCA_COMPETITOR_CODE']=$excompetitorCode[$i];
							$table_column_iff_update['GCA_UPDATED_BY']=$emp_id;
							$table_column_iff_update['GCA_UPDATED_DATE']=date('Y-m-d H:i:s');
							$table_name='gft_competitor_approach_lead';
							array_update_tables_common($lead_comp_update_arr,$table_name,$table_key_arr=null,
							$extra_field_to_update=null,$emp_id,$remarks=null,$table_column_iff_update);
						}
				}
				else if($competitorCode[$i]!=0 && $competitor_name[$i]!=''){	
					$ins_query.="$put_comma($lead_code,$competitorCode[$i],'$competitor_name[$i]',$emp_id,now())";
					$put_comma=",";$execute_cdtl=true;
				}else if($competitorCode[$i]==null && $competitor_name[$i]!=''){
					$competitor_code=insert_competitor_name($competitor_name[$i]);
					$ins_query.="$put_comma($lead_code,$competitor_code,'$competitor_name[$i]',$emp_id,now())";
					$put_comma=",";$execute_cdtl=true;
				}	
			}
		}		
		if($execute_cdtl==true){
			execute_my_query($ins_query);
		}	
	}	

?>
