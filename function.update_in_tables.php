<?php
/*. require_module 'standard'; .*/
/*. require_module 'mysql'; .*/

require_once(__DIR__ ."/dbcon.php");

/**
 * @param mixed[string] $updatearr
 * @param string $table_name
 * @param mixed[string] $table_key_arr
 * @param mixed[string] $extra_field_to_update
 * @param string $user_id
 * @param string $remarks
 * @param mixed[string] $table_column_iff_update
 * @param mixed[string] $insert_new_row
 * @param boolean $check_affected_rows
 * 
 * @return boolean
 */
function array_update_tables_common($updatearr,$table_name,$table_key_arr,$extra_field_to_update,$user_id,$remarks=null,
$table_column_iff_update=null,$insert_new_row=null,$check_affected_rows=false){
	global $me,$conn;

	$result_old=/*. (resource) .*/ NULL;

	$up_query = /*. (string[int]) .*/array();
	$audit_q = /*. (string[int]) .*/array();
	$call_function_later=0;
	$lead_code='';
	$lead_status='';
	
	if(count($updatearr)>0){
		$i=0;
		$select_name="";
		foreach($updatearr  as $key => $value){
			$select_name.=($i!=0?', ':"").$key;
			$i++;
		}
		$i=0;
		$where_name='';
		if(isset($table_key_arr) && $table_key_arr!=null){
			foreach($table_key_arr  as $key => $value){
				$where_name.=($i!=0?' and ':"").$key."='".mysqli_real_escape_string_wrapper($value)."' ";
				$i++;
			}
		}
		if($where_name=='' && $insert_new_row==null){ 
			//show_my_alert_msg("Sorry No Key field found to update data ");
			return false;
		}
		if($table_name==''){
			show_my_alert_msg("Sorry No table name mentioned to update data ");
			return false;
		}
		$num_rows_to_updated=0;
		if($where_name!==''){
		$select_q="Select $select_name from $table_name where $where_name ";
		$result_old=execute_my_query($select_q); 
		$num_rows_to_updated=mysqli_num_rows($result_old);
		}
		$j=0;
		
		if($insert_new_row!=null and $num_rows_to_updated==0){
			$column_name=$column_value='';
			$lr=0;
			foreach($insert_new_row as $key => $value){
				$column_name.=($lr!=0?",":"")."$key";
				$column_value.=($lr!=0?",":"")."'".mysqli_real_escape_string_wrapper($value)."'";
				$lr++;
			}
			$insert_query="insert ignore into $table_name ($column_name) value ($column_value)";
			execute_my_query($insert_query);
			return true;
		}
		if($where_name==''){ 
			return false; 
		}

		while($data_old=mysqli_fetch_array($result_old)){
			$i=0;
			foreach($updatearr  as $key => $value){
				if($data_old[$key]!==$value){
					$call_function_later=0;
					if(in_array(mysqli_field_type_wrapper($result_old,$i), array('249','250','251','252','253','254'))){
						$audit_q[]="('','$table_name','$key','".mysqli_real_escape_string_wrapper(trim(isset($data_old[$key])?$data_old[$key]:''))."'," .
								"'".mysqli_real_escape_string_wrapper(trim(isset($value)?(string)$value:''))."','$me',now(),'$user_id'," .
								"'".mysqli_real_escape_string_wrapper(trim($where_name))."','".mysqli_real_escape_string_wrapper(trim($remarks))."')";
						$up_query[]= " $key = '".mysqli_real_escape_string_wrapper(trim(isset($value)?(string)$value:''))."' ";
					}else{
						$audit_q[]="('','$table_name','$key','".mysqli_real_escape_string_wrapper(trim(isset($data_old[$key])?$data_old[$key]:''))."','".mysqli_real_escape_string_wrapper(trim(isset($value)?(string)$value:''))."','$me',now(),'$user_id'," .
						        "'".mysqli_real_escape_string_wrapper(trim($where_name))."','".mysqli_real_escape_string_wrapper(trim($remarks))."')";
						$up_query[]= " $key = '$value' ";
						
						if($table_name=="gft_lead_hdr" and $key=="GLH_RESPONSE_GROUP"){
							$up_query[]= " GLH_RESPONSE_EFFECT_FROM ='".date('Y-m-d H:i:s')."' ";
						}
						if($table_name=="gft_lead_hdr" and $key=="GLH_STATUS"){
							$lead_code=(string)$table_key_arr['GLH_LEAD_CODE'];
							$lead_status=$value;
							$call_function_later="1";
						}
					}
					if($table_name=="gft_lead_hdr" and $key=="GLH_CUST_NAME"){
						$lead_code=(string)$table_key_arr['GLH_LEAD_CODE'];
						$value = mysqli_real_escape_string_wrapper($value);
						$query="update $table_name set GLH_REFERREDBY='$value' where glh_reference_given = '$lead_code' ";
						execute_my_query($query);
					} 
					$j++;
				}
				$i++;
			}
			if($extra_field_to_update!=''){
				foreach($extra_field_to_update  as $key1 => $value1){
					$up_query[]= " $key1 = '$value1' ";
				}	
			}
		}
		if($j>0){
			if($table_column_iff_update!=''){
				foreach($table_column_iff_update as $key1 => $value1){
					$up_query[]= " $key1 = '$value1' ";
				}	
			}
			$query="update $table_name set ".implode(',',$up_query). " where $where_name ";
			$result_edit=execute_my_query($query);
			$affected_rows = mysqli_affected_rows_wrapper();
			if(mysqli_affected_rows_wrapper()>0){
				$query_audit_col="insert into gft_audit_log_edit_table(
								GUL_AUDIT_ID ,GUL_AUDIT_TABLE ,GUL_AUDIT_COLUMNS,GUL_AUDIT_OLD_VALUES,
								GUL_AUDIT_NEW_VALUES,GUL_PAGE ,GUL_TIME ,GUL_USER_ID,GUL_AUDIT_KEY_FIELDS," .
								"GUL_REMARKS)values ".implode(',',$audit_q);
				$resultaudit=execute_my_query($query_audit_col);
			}
			if($call_function_later=="1"){
				send_sms_regarding_lead_status_change($lead_status,$lead_code,$user_id);
			}
			if($result_edit) {
			    if($check_affected_rows) {
			        if($affected_rows>0) {
			            return true;
			        }
			    } else {
			        return true;
			    }
			}
		}
	}
	return false;
}

/**
 * @param string[string] $value_array
 * @param string[int][int] $manditory_column
 * 
 * @return boolean
 */
function check_manditory_import($value_array,$manditory_column){
	for($i=0;$i<count($manditory_column);$i++) {
		if(!isset($value_array[$manditory_column[$i][0]]) or $value_array[$manditory_column[$i][0]]==''){
			return false;
		}
	}
	return true;
}

/**
 * @param string[string] $data
 * @param string[int][int] $format_array
 * 
 * @return string[string]
 */
function update_column_heading($data,$format_array){
	$index_array=/*. (string[string]) .*/ array();
	for($j=0;$j<count($format_array);$j++){
		if(trim($data[array_search(trim($format_array[$j][1]),$data)])==$format_array[$j][1]){
			$index_array[$format_array[$j][1]]=array_search(trim($format_array[$j][1]),$data);
		}
	}
	return $index_array;
}
?>
