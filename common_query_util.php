<?php
/*. require_module 'standard'; .*/
/*. require_module 'mysql'; .*/


/*. forward string function check_product_family(); .*/
/*. forward string function check_skew_property(int[] $skew_property); .*/
/*. forward string function check_product_type(string $product_type); .*/
/*. forward string function check_customer_dtl(string $include_employees=,string $lead_hdr_table_alias=,string $not_in_lead_type=); .*/
/*. forward string function get_query_constrain_common(boolean $show_gft=,string $lead_hdr_table_alias=); .*/
/*. forward string function get_areamap_link(); .*/
/*. forward string function get_query_area_mapping(int $terr_id,int $area_id,int $region_id,int $zone_id=,int $district_id=,int $state_id=,int $country_id=,boolean $include_zone=,boolean $include_country=,string $from_terr_colm=,string $from_dist_colimn=,boolean $include_default_territory=,string $emp_master_alias=, string $lead_hdr_alias=, string $political_map_alias=); .*/
/*. forward string function get_query_reporting_under(string $frmtable=,string $emp_id=, boolean $include_web=, boolean $show_other_emp=, boolean $show_employee_partner=, boolean $add_additional_con=); .*/
/*. forward string function check_common_support_dtl(boolean $family_check=, string $schedule_emp_alias=, string $activity_emp_alias=); .*/

require_once(__DIR__ ."/dbcon.php");
require_once(__DIR__ ."/filter_new.php");
/*
 * Created on Jun 1, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$employee_fields=",em.GEM_MOBILE,em.GEM_EMAIL,em.GEM_IC,em.GEM_DOJ,em.GEM_REPORTING_MGR_NAME,
em.GEM_TITLE,em.GEM_LOCATION_OF_WORKING ";
$only_address_fields=",GLH_CUST_NAME, GLH_AUTHORITY_NAME, GLH_DOOR_APPARTMENT_NO, GLH_BLOCK_SOCEITY_NAME, " .
		" GLH_STREET_DOOR_NO, GLH_CUST_STREETADDR1, GLH_CUST_STREETADDR2, GLH_LANDMARK, " .
		" GLH_CUST_STATECODE, GLH_AREA_NAME, GLH_CUST_CITY, GLH_CUST_PINCODE, GLH_COUNTRY, GLH_CREATED_DATE,GLH_ADDRESS_VERIFIED ,".
		" if(GLH_ADDRESS_VERIFIED='Y',GLH_ADDRESS_VERIFIED_DATE,'') ADDRESS_VERIFIED_DATE ";

$contact_fields_list=",Authority,Contact_Person,MOBILE,BUSSNO,RESNO,EMAIL,FAX,WEBSITE,EMAIL_withid ";

$export_only_address_fields=",GLH_CUST_NAME Customer_Name, GLH_CUST_STATECODE State ," .
       				" GLH_CUST_PINCODE  Pincode, GLH_COUNTRY,GLH_CUST_STREETADDR2 Location ";

$contact_fields=", group_concat(distinct(if(gcc_designation=1,gcc_contact_name,'')) SEPARATOR ' ') 'Authority'," .
		" group_concat(distinct(gcc_contact_name)) 'Contact_Person'," .
		" group_concat(distinct if(gcc_contact_type=1,trim(GCC_CONTACT_NO),'') SEPARATOR ' ') 'MOBILE', " .
		" group_concat(distinct if(gcc_contact_type=2,trim(GCC_CONTACT_NO),'') SEPARATOR ' ') 'BUSSNO', " .
		" group_concat(distinct if(gcc_contact_type=3,trim(GCC_CONTACT_NO),'') SEPARATOR ' ') 'RESNO', " .
		" group_concat(distinct if(gcc_contact_type=4,trim(GCC_CONTACT_NO),'') SEPARATOR ' ') 'EMAIL', " .
		" group_concat(distinct if(gcc_contact_type=5,trim(GCC_CONTACT_NO),'') SEPARATOR ' ') 'FAX', ".
		" group_concat(DISTINCT if(gcc_contact_type=6,trim(GCC_CONTACT_NO),'') SEPARATOR ' ') 'WEBSITE', ".
		" group_concat(DISTINCT if(gcc_contact_type=7,trim(GCC_CONTACT_NO),'') SEPARATOR ' ') 'GTALK', ".
		" group_concat(DISTINCT if(gcc_contact_type=8,trim(GCC_CONTACT_NO),'') SEPARATOR ' ') 'SKYPE', ".
		" group_concat(distinct if(gcc_contact_type=4 && ccd.GCC_CAN_SEND='Y' && ccd.GCC_VALID='Y',concat(trim(GCC_CONTACT_NO),'#',ccd.gcc_id),'') SEPARATOR ' ') 'EMAIL_withid' " ;	
$address_fields=$only_address_fields.$contact_fields;	
$export_address_fields=$export_only_address_fields;;

			
$query_contact_dtl=" left join gft_customer_contact_dtl ccd on (GCC_LEAD_CODE=GLH_LEAD_CODE) ";

//NOTE: Refer getQueryContactDtlInnerFor($custid)
$query_contact_dtl_inner=" inner join (select gcc_lead_code  $contact_fields " .
		"from gft_customer_contact_dtl ccd " .
		"group by gcc_lead_code )" .
		"ccd on (GCC_LEAD_CODE=GLH_LEAD_CODE) ";
$query_contact_dtl_inner_direct=" inner join gft_customer_contact_dtl ccd on (GCC_LEAD_CODE=GLH_LEAD_CODE) ";
		
$group_by_address_fields=$only_address_fields ;

$only_tele_address_fields=",GTH_TELELEAD_NAME, GTH_TELELEAD_AUTHORITY_NAME, GTH_DOOR_APPARTMENT_NO, GTH_BLOCK_SOCEITY_NAME, GTH_TELELEAD_LOCATION, GTH_TELELEAD_STREETADDR1, " .
    		" GTH_TELELEAD_STREETADDR2, GLH_TELELEAD_LANDMARK, gpm_map_name, GTH_AREA_NAME, GTH_TELELEAD_CITY, GTH_TELELEAD_PINCODE ";

$group_by_tele_address_fields=$only_tele_address_fields;

$tele_address_fields=$only_tele_address_fields.",group_concat( DISTINCT  if(gtc_contact_type=1,GTC_CONTACT_NO,'') SEPARATOR ' ') 'MOBILE', " .
		" group_concat( DISTINCT  if(gtc_contact_type=2,GTC_CONTACT_NO,'') SEPARATOR ' ') 'BUSSNO'," .
		" group_concat( DISTINCT  if(gtc_contact_type=3,GTC_CONTACT_NO,'') SEPARATOR ' ') 'RESNO'," .
		" group_concat( DISTINCT  if(gtc_contact_type=4,GTC_CONTACT_NO,'') SEPARATOR ' ') 'EMAIL'," .
		" group_concat( DISTINCT  if(gtc_contact_type=5,GTC_CONTACT_NO,'') SEPARATOR ' ') 'FAX'," .
		" group_concat( DISTINCT  if(gtc_contact_type=6,GTC_CONTACT_NO,'') SEPARATOR ' ') 'WEBSITE' ";			
		

//$sp_lead_hdr_check=" and glh_status<4 and glh_lead_type in ('1','3','4','7') "; //Sales planning main check in lead hdr
$sp_lead_hdr_check=" and glh_status in(3,8,9) and glh_lead_type in (1,3) "; //Sales planning main check in lead hdr
/*we want only individual and chain order */
$sp_lead_hdr_check_wfr= " and GTL_WFREEZED_LEAD_STATUS in (3,8,9)  and glh_lead_type in (1,3) ";
/*Removed and GTL_WFREEZED_LEAD_TYPE in (1,3)  bcs lead type should be check with current data not stored*/	
	
$partner_order_subquery =  " select GCO_ORDER_NO part_ord_no,GCO_CUST_CODE cust_code, GOD_ORDER_AMT ord_amt,GOD_ORDER_DATE part_ord_date from gft_cp_order_dtl ".
    " join gft_cp_info on (CGI_LEAD_CODE=GCO_CP_LEAD_CODE) ".
    " join gft_lead_hdr on (CGI_LEAD_CODE=GLH_LEAD_CODE and GLH_LEAD_TYPE=2) ".
    " join gft_order_hdr on (GOD_ORDER_NO=GCO_ORDER_NO and GOD_ORDER_AMT > 0 and GOD_ORDER_STATUS='A') ".
    " group by GCO_ORDER_NO ";

/**
 * @param string $phone_no
 *
 * @return string
 */
function getGtcNumberWhereCondition($phone_no){
	$orig_phone_no=$phone_no;
	$phone_no=substr($phone_no,-10);
	//$cust_count_numbers.=" and gtc_number like '%$phone_no'";
	$where_condition=" and (gtc_number = '".$phone_no."' or gtc_number='0".$phone_no."' or gtc_number='91".$phone_no."' or gtc_number='00".$phone_no."' or gtc_number='".$orig_phone_no."' or gtc_number='0".$orig_phone_no."' or gtc_number='00".$orig_phone_no."' )";
	return $where_condition;

}

	
/**
 * @param int $contact_type
 * @param int $coption_type
 *
 * @return string
 */
function get_query_constrain_contact_type($contact_type,$coption_type){
	$str_query="";
	if($contact_type!=0 ){
		if($contact_type==1){
			$contact_type_str="MOBILE";
		}else if($contact_type==2){
			$contact_type_str="BUSSNO";
		}else if($contact_type==3){
			$contact_type_str="RESNO";
		}else if($contact_type==4){
			$contact_type_str="EMAIL";
		}else if($contact_type==5){
			$contact_type_str="FAX";
		}else if($contact_type==6){
			$contact_type_str="WEBSITE";
		}else{
			$contact_type_str="MOBILE"; //Default
		}

		if($coption_type==0){ //With
			$null_check="";
			$coption_type_str="!=''";
		}else{   //$coption_type=='1'
			$coption_type_str="=''";  
			$null_check=" or isnull($contact_type_str) "; 
		}//without
		$str_query=" and ($contact_type_str $coption_type_str $null_check)";
	}	
	return $str_query;
}

/**
 * @param int $contact_type
 * @param int $coption_type
 *
 * @return string
 */

function get_query_constrain_contact_type_having($contact_type,$coption_type){
	$str_query='';
	if($contact_type!=0){
		if($contact_type==1){
			$contact_type_str="MOBILE";
		}else if($contact_type==2){
			$contact_type_str="BUSSNO";
		}else if($contact_type==3){
			$contact_type_str="RESNO";
		}else if($contact_type==4){
			$contact_type_str="EMAIL";
		}else if($contact_type==5){
			$contact_type_str="FAX";
		}else if($contact_type==6){
			$contact_type_str="WEBSITE";
		}else{
			$contact_type_str="MOBILE"; //Default
		}

		if($coption_type==0){ //with
			$null_check="";
			$coption_type_str="!=''"; 
		}else { //$coption_type=='1'
			$coption_type_str="=''";  
			$null_check=" or isnull($contact_type_str) "; 
		}//without
		$str_query=" having ($contact_type_str $coption_type_str $null_check)";
	}	
	return $str_query;
}

/**
 * @param boolean $show_gft
 * @param string $lead_hdr_table_alias
 * 
 * @return string
 */
function get_query_constrain_common($show_gft=false,$lead_hdr_table_alias='') {     
	global $annuity_type;
        global $expected_close_date;
	global $asa_letter_ack_status;
        global $ass_enquiry,$skew_property,$order_no,
        		$status,$chk_sub_expiry_date,
        		$chk_proforma_date,$chk_proforma_order_no,$chk_proforma_status,$marked_reference_list,
        		$chk_ass_letter_date,$created_lead_type,$product_type,$chk_hdr_order_no,
        		$chk_hdr_install_no,$asa_letter_status,$chk_order_entry,$visit_date,
        		$next_visit_date,$chk_last_activity,$arg,$date_of_closure1,
        		$date_of_closure2,$inventory_module,
        		$or_status,$chk_ass_expiry_date,$chk_install_date,$chk_registered_date,$chk_order,$query_from_dt,$installation_status,$chk_uninstall_date,$chk_ass_enquiry_date,
       			$chk_quotation_date,$chk_quotation_order_no,$chk_quotation_status;		
       
        $query="";
        
        
        global $from_dt,$to_dt;
        $query_from_dt=db_date_format($from_dt);
		$query_to_dt=db_date_format($to_dt);
		$gft_lead='N';
		if($show_gft){
			$gft_lead='Y';
		}
        $query.=check_customer_dtl($gft_lead,$lead_hdr_table_alias);
        $query.=check_skew_property($skew_property); 
		$query.=check_product_type($product_type);
		if($marked_reference_list=='on'){
	      	$query.=" and GLH_REFERENCE_CUST='Y' ";
        } 
        
        if($chk_hdr_order_no=="on" and $order_no!="" ){
        	$query.=" and god_order_no like '%$order_no%' ";
        }else	if($order_no!="" and $chk_hdr_install_no=="on"){	
			$query.=" and gid_order_no like '%$order_no%' ";
		}else if($order_no!="" and $chk_quotation_order_no=="on"){
			$query.=" and GQH_ORDER_NO like '%$order_no%' ";
        }else if($order_no!="" and $chk_proforma_order_no=='on'){
        	$query.=" and GPH_ORDER_NO like '%$order_no%' ";
        }else if($order_no!=""){
			$query.=" and god_order_no like '%$order_no%' ";
        }
		if($chk_quotation_status=="on" and $or_status!='' and $or_status!='0'){
			$query.=" and gqh_order_status='$or_status' ";
		}
		if($chk_proforma_status=="on" and $or_status!='' and $or_status!='0'){
			$query.=" and gph_order_status='$or_status' ";
		}
		
		if($chk_proforma_status!="on" and $chk_quotation_status!="on" and !empty($or_status)){
      		$query.=" and god_order_status='$or_status' ";	
    	} 
    	if($installation_status!='' and $installation_status!='0'){
    		if($installation_status!='UG'){
    			if($installation_status=='A'){
    				$query.=" and id1.gid_status in ('A','S') ";
    			}else{
    				$query.=" and id1.gid_status='$installation_status' ";
    			}
    		}	
    	} 
    	global $expire_for;
    	if((int)$expire_for!=0){
        	$query.=" and id1.gid_expire_for=$expire_for ";
    	}
    	if($chk_ass_expiry_date=="on" && $chk_sub_expiry_date=='on' ){
    		if($query_from_dt!=''){$query.=" and id1.GID_VALIDITY_DATE >= '$query_from_dt' ";     }
	        if($query_to_dt!=''){ $query.=" and id1.GID_VALIDITY_DATE <= '$query_to_dt' ";}
    		
    	}else if($chk_ass_expiry_date=="on" ){
	        //$query.= " and  id1.gid_expire_for in (1,3) ";
	        if($query_from_dt!=''){$query.=" and id1.GID_VALIDITY_DATE >= '$query_from_dt' ";     }
	        if($query_to_dt!=''){ $query.=" and id1.GID_VALIDITY_DATE <= '$query_to_dt' ";}
        }else if($chk_sub_expiry_date=='on'){
        	$query.= " and  gid_expire_for in (2) ";
        	if($query_from_dt!=''){$query.=" and id1.GID_VALIDITY_DATE >= '$query_from_dt' ";     }
	        if($query_to_dt!=''){ $query.=" and id1.GID_VALIDITY_DATE <= '$query_to_dt' ";}
        }
        if($asa_letter_status!='0' && $asa_letter_status!=0){
        	$query.=" and id1.GID_ASA_LETTER_DISPATCHED='$asa_letter_status' ";
        }
        if($chk_ass_letter_date=="on"){
        	if($query_from_dt!=''){$query.=" and id1.GID_ASA_LETTER_DISPATCHED_DATE >= '$query_from_dt' ";     }
	        if($query_to_dt!=''){ $query.=" and id1.GID_ASA_LETTER_DISPATCHED_DATE <= '$query_to_dt' ";}
        }
       
        if($chk_install_date=="on"){
			if($query_from_dt!=''){$query.=" and id1.GID_INSTALL_DATE >= '$query_from_dt' ";	}
			if($query_to_dt!=''){$query.=" and id1.GID_INSTALL_DATE <= '$query_to_dt' ";}
 		}
 		if($chk_registered_date=="on"){
 			if($query_from_dt!=''){$query.=" and GLH_DATE >= '$query_from_dt' ";	}
 			if($query_to_dt!=''){$query.=" and GLH_DATE <= '$query_to_dt' ";}
 		}
 		global $chk_renewal_date;
 		if($chk_renewal_date=="on"){
 			if($query_from_dt!=''){$query.=" and asd. GAD_ASS_DATE >= '$query_from_dt' ";	}
			if($query_to_dt!=''){$query.=" and asd.GAD_ASS_DATE <= '$query_to_dt' ";}
 		}
 		global $inst_end_user,$inst_corporate;
 		if($inst_end_user=='on'){
 			$query.=" and glh_lead_type not in (3,13) ";
 		}
 		if($inst_corporate=='on'){
 			$query.=" and glh_lead_type in (3,13) ";
 		}
 		if($annuity_type!=0){
 			if($annuity_type==1){ $query.=" and ( isnull(gid_ass_id) or gid_ass_id='')  and GID_VALIDITY_DATE<date(now())  ";}
 			if($annuity_type==2){ $query.=" and gid_ass_id!='' ";}
 			//if($annuity_type==3){ $query.=" and datediff(now(),gid_install_date)<=365 ";}
 			if($annuity_type==3){
 				$last_year_from_dt = add_date($query_from_dt, -365);
 				$last_year_to_dt = add_date($query_to_dt, -365);
 				$query.=" and (GID_INSTALL_DATE between '$last_year_from_dt' and '$last_year_to_dt') and (GID_VALIDITY_DATE between '$query_from_dt' and '$query_to_dt') ";
 			}
 			if($annuity_type==4){ $query.=" and datediff(now(),gid_install_date) between 365 and 730 ";}
 			if($annuity_type==5){ $query.=" and datediff(now(),gid_install_date)<=365  and gid_ass_id!='' ";}
 			if($annuity_type==6){ $query.=" and datediff(now(),gid_install_date)<=365  and (isnull(gid_ass_id) or gid_ass_id='') ";}
 			//if($annuity_type==7){ $query.=" and datediff(now(),gid_install_date) between 365 and 730   and gid_ass_id!='' ";}
 			//if($annuity_type==8){ $query.=" and datediff(now(),gid_install_date) between 365 and 730   and (isnull(gid_ass_id) or gid_ass_id='') ";}
 			if( ($annuity_type==7) || ($annuity_type==8) ){
 				$last_second_year_from_dt = add_date($query_from_dt, -730);
 				$last_second_year_to_dt = add_date($query_to_dt, -730);
 				$second_yr_install_cond= " (GID_INSTALL_DATE between '$last_second_year_from_dt' and '$last_second_year_to_dt') and (GID_VALIDITY_DATE between '$query_from_dt' and '$query_to_dt') ";
 				if($annuity_type==7){ 
 					$query.=" and $second_yr_install_cond and gid_ass_id!='' ";
 				}
 				if($annuity_type==8){ 
 					$query.=" and $second_yr_install_cond and (isnull(gid_ass_id) or gid_ass_id='') ";
 				}
 			}
 			if($annuity_type==9){ $query.=" and datediff(now(),GID_VALIDITY_DATE)<=90 and gid_ass_id!='' ";}
 			if($annuity_type==10){ $query.=" and datediff(now(),GID_VALIDITY_DATE)<=90 and (gid_ass_id is null or gid_ass_id='') ";}
 			if($annuity_type==11){ $query.=" and datediff(now(),GID_VALIDITY_DATE)>90 and datediff(now(),GID_VALIDITY_DATE) <= 365 and gid_ass_id!='' ";}
 			if($annuity_type==12){ $query.=" and datediff(now(),GID_VALIDITY_DATE)>90 and datediff(now(),GID_VALIDITY_DATE) <= 365 and (gid_ass_id is null or gid_ass_id='') ";}
 			if($annuity_type==13){ $query.=" and datediff(now(),GID_VALIDITY_DATE)>365 and gid_ass_id!='' ";}
 			if($annuity_type==14){ $query.=" and datediff(now(),GID_VALIDITY_DATE)>365 and (gid_ass_id is null or gid_ass_id='') ";}
 		}
 		
        if($chk_quotation_date=="on"){  
            if($query_from_dt!=''){ $query.="  and  GQH_ORDER_DATE>='$query_from_dt' ";}
			if($query_to_dt!=''){ $query.="  and   GQH_ORDER_DATE<='$query_to_dt' "; }
        }
        if($chk_proforma_date=="on"){  
            if($query_from_dt!=''){ $query.="  and  GPH_ORDER_DATE>='$query_from_dt 00:00:00' ";}
			if($query_to_dt!=''){ $query.="  and   GPH_ORDER_DATE<='$query_to_dt 23:59:59' "; }
        }
        if($chk_uninstall_date=="on"){
			if($query_from_dt!=''){$query.=" and ud.GUD_UNINSTALL_DATE >= '$query_from_dt' ";	}
			if($query_to_dt!=''){$query.=" and ud.GUD_UNINSTALL_DATE <= '$query_to_dt' ";}
 		}
        if($chk_order=="on"){
        	if($query_from_dt!=''){$query.=" and GOD_ORDER_DATE >= '$query_from_dt' ";}
			if($query_to_dt!=''){$query.=" and GOD_ORDER_DATE<= '$query_to_dt' ";}
        }
        if($chk_order_entry=="on"){
        	if($query_from_dt!=''){$query.=" and date(GOD_CREATED_DATE) >= '$query_from_dt' ";}
			if($query_to_dt!=''){$query.=" and date(GOD_CREATED_DATE)<= '$query_to_dt' ";}
        } 
        if($visit_date=='on'){
      		   	  if($query_from_dt!=''){$query.=" and gld_visit_date >='$query_from_dt'  ";  }
           		if($query_to_dt!=''){$query.=" and  gld_visit_date <= '$query_to_dt' "; }
       	
		}
		if($next_visit_date=='on'){
      		    if($query_from_dt!=''){$query.=" and gld_next_action_date >='$query_from_dt'  ";  }
           		if($query_to_dt!=''){$query.=" and  gld_next_action_date <= '$query_to_dt' "; }
       	
        }
        if($expected_close_date=='on'){
        	if($query_from_dt!=''){$query.=" and GLD_APPORX_TIMETOCLOSE >='$query_from_dt'  ";  }
           	if($query_to_dt!=''){$query.=" and  GLD_APPORX_TIMETOCLOSE <= '$query_to_dt' "; }
        }
        if($chk_ass_enquiry_date=="on"){
     	       	if($query_from_dt!=''){$query.=" and gld_visit_date >='$query_from_dt'  ";  }
           		if($query_to_dt!=''){$query.=" and  gld_visit_date <= '$query_to_dt' "; }
     
           		$query.=" and gld_visit_nature=40 ";
        } 
		if($chk_last_activity=='on'){  $query.=" and  gld_call_status='P' ";	}
		if($arg=="visit_report"){
			if($status!='' and $status!='0'){   $query.=" and  gld_lead_status='$status' ";	}
		}	  
        if($date_of_closure1!='' and $date_of_closure2!=''){
			$query_exp_dt1=db_date_format($date_of_closure1);
		 	$query.=" and GLH_APPROX_TIMETOCLOSE >='$query_exp_dt1' ";
		}	
		if($date_of_closure2!=''){
			$query_exp_dt2=db_date_format($date_of_closure2);
			$query.=" and GLH_APPROX_TIMETOCLOSE <='$query_exp_dt2'";
		}
   		if($inventory_module!=0){
   			$query.=" and GLH_INVETORY_MODULE_STATUS ='$inventory_module' ";
   		}
   		if($created_lead_type!=0){
   			$query_clt="select ifnull(group_concat(gcc_id),$created_lead_type) from gft_lead_create_category where GCC_GROUP=$created_lead_type ";
   			$result_clt=execute_my_query($query_clt);
   			if(mysqli_num_rows($result_clt)==1 ){
				$qdclt=mysqli_fetch_array($result_clt);
				$clt=$qdclt[0];
				$query.=" and GLH_CREATED_CATEGORY in ($clt) ";
   			}
   		}
		if($ass_enquiry==1){$query.=" and GLH_ASS_ENQUIRY_CALL_MADE='Y' ";}
		else if($ass_enquiry==2){ $query.=" and GLH_ASS_ENQUIRY_CALL_MADE='N' ";}
		else if($ass_enquiry==3){ $query.=" and GLH_INTEREST_ADDON='Y' ";}
   		$query.=(($asa_letter_status!='0' and $asa_letter_status!=0 and $asa_letter_status!='')?" and GID_ASA_LETTER_DISPATCHED='$asa_letter_status' ":"");
		$query.=(($asa_letter_ack_status!='0' and $asa_letter_ack_status!='')?" and GID_ASA_LETTER_ACK='$asa_letter_ack_status' ":"");
	
    	return $query;
}//end of function	

/**
 * @return string 
 */
function get_incharge_type_column(){
    	global $incharge_type_column,$incharge_type;
		if($incharge_type=='Installed By'){$incharge_type_column=" id1.GID_INSTALLED_EMP ";}
	    else if($incharge_type=="Sales Incharge"){	$incharge_type_column="GLH_LFD_EMP_ID";		
		}else if($incharge_type=="Reg Incharge"){ $incharge_type_column="GLH_L1_INCHARGE";
		}else if($incharge_type=="Field Incharge"){	$incharge_type_column="GLH_FIELD_INCHARGE";
		}else if($incharge_type=="Sales Credited To"){	$incharge_type_column="id1.GID_SALESEXE_ID ";
		}else if($incharge_type_column==''){$incharge_type_column=" GLH_LFD_EMP_ID ";}	
		return 	$incharge_type_column;
}

/**
 * @param int[int] $nt_visit
 * @param string $nxt_nt_visit
 *
 * @return string
 */
function filter_for_vn_nvn($nt_visit,$nxt_nt_visit){
	$queryp="";
	$query='';

	//if($nt_visit!=''){
	//	$queryp.=" ac.gld_visit_nature='$nt_visit' ";
	//}
	if(count($nt_visit)!=0){
	 	if(is_array($nt_visit)){
    		$nature_visit =implode(",",$nt_visit);
        	foreach($nt_visit as $t){
				if($t!='' and $t!=0 ){
				 	$queryp.=" ac.gld_visit_nature='$t' or ";
				} 
			}
			$queryp=substr($queryp,0,strlen($queryp)-2);
	 	}elseif($nt_visit!=''){
	 		$queryp.=" ac.gld_visit_nature='$nt_visit' ";
	 	}
		if($queryp!=''){
		   	$query.=" and  ( $queryp ) "; 
		}
	}
/*
	if(is_array($nxt_nt_visit)){
    	$nxt_nt_visit=implode(",",$nxt_nt_visit);
	}
*/
	$temp_implode=explode(',',$nxt_nt_visit);
	if(count($temp_implode)>0){
        $k=0;
        if((int)$temp_implode[$k]>0){
			$queryp.=" ac.gld_next_action='".$temp_implode[$k]."'  ";
        }
		$k++;
		for($k=1;$k < count($temp_implode);$k++){
			if((int)$temp_implode[$k]>0){
				$queryp.=" or ac.gld_next_action='".$temp_implode[$k]."' ";
			}
		}
	}
	return $queryp;
}//end of function 			
/**
 * @param string $repoting_id
 * @param string $end_date
 * @param string $status
 *
 * @return string
 */
function get_reporting_hierarchy_top_to_bottom($repoting_id,$end_date='', $status='A')
{
	$all_employee = "$repoting_id";
	$current_loop= "";
	$stat_cond = ($status=="I" || $status=="A"?" AND GEM_STATUS='$status'":" AND GEM_STATUS='A'");
	if($end_date!=''){
	    $stat_cond = " and (GEM_DOR is null or GEM_DOR>='$end_date') ";
	}
	$emp_result  = execute_my_query("select GROUP_CONCAT(GER_EMP_ID) GER_EMP_ID,GER_REPORTING_EMPID from gft_emp_reporting
			INNER JOIN gft_emp_master em ON(GER_EMP_ID=GEM_EMP_ID $stat_cond )
			where GER_REPORTING_EMPID IN($repoting_id) AND GER_STATUS='A' AND GER_EMP_ID!=1 GROUP BY GER_REPORTING_EMPID");
	if(mysqli_num_rows($emp_result)>=0){
		while($row=mysqli_fetch_array($emp_result)){
			//$all_employee = $all_employee.($all_employee!=""?",":"").$row['GER_EMP_ID'];
			$current_loop = $current_loop.($current_loop!=""?",":"").$row['GER_EMP_ID'];
		}
		if($current_loop!=""){
		    $all_employee = $all_employee.($all_employee!=""?",":"").get_reporting_hierarchy_top_to_bottom($current_loop);
		}
	}
	return $all_employee;
}
/**
 * @param string $frmtable
 * @param string $emp_id
 * @param boolean $include_web
 * @param boolean $show_other_emp
 * @param boolean $show_employee_partner
 * @param boolean $add_additional_con
 * @param string[int] $partner_emp_join_columns
 * 
 * @return string
 */
function get_query_reporting_under($frmtable="em.gem_emp_id",$emp_id=null,$include_web=false,$show_other_emp=false,$show_employee_partner=false,$add_additional_con=false,$partner_emp_join_columns=null){
    global $exec_id ,$terr_mgr_id ,$status,$regional_mgr_id,$zone_mgr_id,$uid,$non_employee_group,$cp_lcode,$non_emp_group,$admin_group,$cp_user_id,$team;

	$add_reporting_under=get_samee_const('add_reporting_under');

 	$str="";
 	$web_order= "";
 	if($include_web){
 		$web_order="or GLEM_EMP_ID=GOD_STORE_EMP";
 	}
 	if($add_reporting_under==0) return '';
 	if($cp_lcode!='' and ($non_emp_group or ($uid!=null and is_authorized_group_list($uid,$non_employee_group)) or $cp_user_id!='')){
		if(is_authorized_group_list($cp_user_id,array(13)) or is_authorized_group_list($exec_id,array(13))){
			return "join gft_cp_relation cpr2 on (cpr2.GCR_LEAD_CODE=$cp_lcode)" .
					"inner join gft_leadcode_emp_map on(GLEM_LEADCODE=cpr2.GCR_RESELLER_LEAD_CODE and (GLEM_EMP_ID in ($frmtable) $web_order) ) ";
		}else if(is_authorized_group_list($cp_user_id,array(38))){
			return " inner join gft_cp_relation on (GCR_LEAD_CODE=$cp_lcode) ".
					"inner join gft_leadcode_emp_map on(GLEM_LEADCODE=$cp_lcode and GLEM_EMP_ID=$cp_user_id and GLEM_EMP_ID in ($frmtable)) ";
		}else if(is_authorized_group_list($cp_user_id,array(39))){
			//TODO: Need to analyze her
			//"inner join gft_leadcode_emp_map on(GLEM_LEADCODE=$cp_lcode and GLEM_EMP_ID=$cp_user_id and GLEM_EMP_ID=$frmtable) ";
		}
 	}else if($emp_id!=null and $emp_id!='' and $emp_id!=0 and $team=='on' and is_authorized_group_list($emp_id,array(5,6))){
		$str.=" and ( gmr_emp_id='$emp_id' or (gmr_terri_m =$emp_id and gmr_terri_m_ck=true) " .
    			" or (gmr_area_m=$emp_id and gmr_area_m_ck=true) " .
    			" or (gmr_region_m='$emp_id' and gmr_region_m_ck=true) " .
    			" or (gmr_zone_m='$emp_id') )";
 	}else if($emp_id!=null and $emp_id!='' and $emp_id!=0 and $team!='on' and !is_authorized_group_list($emp_id,array(5,6))){
		return " inner join gft_emp_master emp_other on( emp_other.gem_emp_id in ($frmtable) and emp_other.gem_emp_id=$emp_id )";		
	}else if($emp_id!=null and $emp_id!='' and $emp_id!=0 and $team!='on'){
	    $str.="and gmr_emp_id='$emp_id' ";
	}else if($exec_id!='0' and $exec_id!='' and is_authorized_group_list($exec_id,array(5,6)) and !is_authorized_group_list($exec_id,array(27))){
		$str.=" and (gmr_emp_id=$exec_id )";
	}else if($terr_mgr_id!='' and $terr_mgr_id!='0' and $terr_mgr_id>'0' ){
		$str.=" and ( gmr_area_m=$terr_mgr_id and gmr_area_m_ck=true) ";
	}else if($regional_mgr_id!='' and $regional_mgr_id!='0'  and $regional_mgr_id>'0'){
		$str.=" and gmr_region_m='$regional_mgr_id' and gmr_region_m_ck=true ";
	}else if($zone_mgr_id!='' and $zone_mgr_id!='0' and $zone_mgr_id>'0' ){
		$str.=" and gmr_zone_m='$zone_mgr_id'";
	}
	if($admin_group and $str==""){
		if(($exec_id!='0' and $exec_id!='' and !is_authorized_group_list($exec_id,array(5,6)))){
			return " inner join gft_emp_master emp_other on( emp_other.gem_emp_id in ($frmtable) and emp_other.gem_emp_id=$exec_id ) ";
		}else{
			return "";
		}
	}else if(is_authorized_group_list($uid,array(5,6)) and !is_authorized_group_list($uid,array(1,20,11,12))){
    	/* added skip for hierarchy to admin,techsuppor,dev,business development*/
		//error_log("uid=".$uid." zone_mgr_id=".$zone_mgr_id . " regional_mgr_id=".$regional_mgr_id . " terr_mgr_id=".$terr_mgr_id . " exec_id=".$exec_id);
    	if($uid==$zone_mgr_id){
    		$str.=" and (gmr_emp_id='$uid' or gmr_zone_m='$uid')";
    	}else if($uid==$regional_mgr_id){
    		$str.=" and (gmr_emp_id='$uid' or (gmr_region_m='$uid' and gmr_region_m_ck=true))";
    	}else if($terr_mgr_id==$uid){
    		$str.=" and (gmr_emp_id='$uid' or (gmr_area_m=$uid and gmr_area_m_ck=true))";
	}else if($exec_id==$uid){
		$str.=" and (gmr_emp_id='$uid' or (gmr_terri_m =$uid and gmr_terri_m_ck=true)) " ;
	}else{
    		$str.=" and (gmr_emp_id='$uid' or (gmr_terri_m =$uid and gmr_terri_m_ck=true) " .
    				" or (gmr_area_m=$uid and gmr_area_m_ck=true) " .
    				" or (gmr_region_m='$uid' and gmr_region_m_ck=true) " .
    				" or (gmr_zone_m='$uid') )";
    	}
	}else if((!is_authorized_group_list($uid,array(5,6,25,36)) or ($exec_id!='0' and $exec_id!='' and !is_authorized_group_list($exec_id,array(5,6,25,36)))) and $str==""){
		return " inner join gft_emp_master emp_other on( emp_other.gem_emp_id in ($frmtable) " .
				(($exec_id!='0' and $exec_id!='' and !is_authorized_group_list($exec_id,array(5,6)))?" and emp_other.gem_emp_id=$exec_id ":" and emp_other.gem_emp_id=$uid ")." ) ";
	}
	//error_log("return::: inner join gft_emp_manager_relation on(gmr_emp_id=$frmtable $str ) ");
	$direct_report_hierarchy="";
	if($add_additional_con){
	    $direct_report_hierarchy=get_reporting_hierarchy_top_to_bottom($uid, '', $status);
	}
	if($include_web){
		return " inner join gft_emp_manager_relation on( (gmr_emp_id in ($frmtable) or gmr_emp_id = god_store_emp) $str ) ";
	}else{
		if($show_other_emp==true){
			return " inner join gft_emp_manager_relation on( (1) $str ) ";
		}else{
			$hierarchy_conds = "";
			$from_tbl_arr = explode(",",$frmtable);
			foreach ($from_tbl_arr as $val) {
				$hierarchy_conds .= " and $val in ($direct_report_hierarchy) ";
			}
			$include_emp_partners=" (gmr_emp_id in ($frmtable) ".($direct_report_hierarchy!=""?" $hierarchy_conds ":"").")";
			if($show_employee_partner){
			    $join_col = " OR gmr_emp_id=cgi_incharge_emp_id ";
			    if(is_array($partner_emp_join_columns) and count($partner_emp_join_columns)>0) {
			        $join_col = '';
			        foreach ($partner_emp_join_columns as $col) {
			            $join_col .= " OR gmr_emp_id=$col ";
			        }
			    }
				$include_emp_partners="(gmr_emp_id in ($frmtable) $join_col".($direct_report_hierarchy!=""?" $hierarchy_conds ":"").")";
			}
			return " inner join gft_emp_manager_relation on( $include_emp_partners $str ) ";
		}
		
	}
}	

/**
 * @param string $exec_id
 * @param string $terr_mgr_id
 * @param string $regional_mgr_id
 * @param string $zone_mgr_id
 * @param string $frmtable
 *
 * @return string
 */
	
function get_queryconstraint_reporting_under($exec_id,$terr_mgr_id,$regional_mgr_id,$zone_mgr_id,$frmtable="em.gem_emp_id")
{
	$uid=$_SESSION['uid'];
	if($exec_id!='0' and $exec_id!=''){
		$str=" and (gmr_emp_id=$exec_id  or (gmr_terri_m=$terr_mgr_id and gmr_terri_m_ck=true))";
	}else if($terr_mgr_id!='' and $terr_mgr_id!='0' and $terr_mgr_id>'0' ){
		$str=" and (( gmr_area_m=$terr_mgr_id and gmr_area_m_ck=true)) ";
	}else if($regional_mgr_id!='' and $regional_mgr_id!='0'  and $regional_mgr_id>'0'){
		$str=" and gmr_region_m='$regional_mgr_id' and gmr_region_m_ck=true ";
	}else if($zone_mgr_id!='' and $zone_mgr_id!='0' and $zone_mgr_id>'0' ){
		$str=" and gmr_zone_m='$zone_mgr_id'";
	}else{
    	$str=" and( gmr_emp_id='$uid' or (gmr_terri_m =$uid and gmr_terri_m_ck=true) " .
    			" or (gmr_area_m=$uid and gmr_area_m_ck=true) " .
    			" or (gmr_region_m='$uid' and gmr_region_m_ck=true) " .
    			" or (gmr_zone_m='$uid') )";   
    }
	//return $str;
	return "";
}

/**
 * @param string $lead_hdr_alias
 * @param int $state_id
 * @param int $country_id
 * 
 * @return string
 */
function country_state_where_condition($lead_hdr_alias='',$state_id=0,$country_id=0){
    $wh_cond = "";
    if($state_id!=0){
        $state_name = get_single_value_from_single_query("GPM_MAP_NAME", "select GPM_MAP_NAME from gft_political_map_master where GPM_MAP_TYPE='S' and GPM_MAP_ID='$state_id' ");
        $wh_cond .= " and {$lead_hdr_alias}GLH_CUST_STATECODE='$state_name' ";
    }
    if($country_id!=0){
        if($country_id==-1){
            $wh_cond .= " and {$lead_hdr_alias}glh_country!='India' ";
        }else{
            $country_name = get_single_value_from_single_query("GPM_MAP_NAME", "select GPM_MAP_NAME from gft_political_map_master where GPM_MAP_TYPE='C' and GPM_MAP_ID='$country_id' ");
            $wh_cond .= " and {$lead_hdr_alias}glh_country='$country_name' ";
        }
    }
    return $wh_cond;
}

/**
 * @param int $terr_id
 * @param int $area_id
 * @param int $region_id
 * @param int $zone_id
 * @param int $district_id
 * @param int $state_id
 * @param int $country_id
 * @param boolean $include_zone
 * @param boolean $include_country
 * @param string $from_terr_colm
 * @param string $from_dist_colimn
 * @param boolean $include_default_territory
 * @param string $emp_master_alias
 * @param string $lead_hdr_alias
 * @param string $political_map_alias
 * 
 * @return string
 */
function get_query_area_mapping($terr_id,$area_id,$region_id,$zone_id=0,$district_id=0,$state_id=0,$country_id=0,
$include_zone=false,$include_country=false,$from_terr_colm='lh.glh_territory_id',
$from_dist_colimn='lh.glh_district_id',$include_default_territory=true,$emp_master_alias='emp',$lead_hdr_alias='',$political_map_alias='')
{
	global $uid;
	global $unknown_territory,$multiple_terr,$skip_area_filter,$skip_terr_filter,$skip_region_filter,$skip_zone_filter,$cmbRegion_multi;
	$include_default_territory=($unknown_territory=='on'?true:false);
	global $corp_cust,$cmbgroup_id;
	$str="";
	$terr_cond = "$terr_id";
	if( ($multiple_terr!='') && ($multiple_terr!='0') ){
		$terr_cond .= ",$multiple_terr";
	}
	if( ($terr_id!=0 or $region_id!=0 or (is_array($cmbRegion_multi) && count($cmbRegion_multi)>0 and !in_array('0',$cmbRegion_multi)) or $zone_id!=0 or $area_id!=0) and $corp_cust!='3'){
		$str.=" inner join b_map_view bmv on ((bmv.terr_id=$from_terr_colm  " .
		        ($skip_zone_filter=='0'?(($zone_id!=0)?" and bmv.zone_id='$zone_id' ":""):'').
		        ($skip_region_filter=='0'?
                   (count($cmbRegion_multi)>0?" and bmv.region_id in (".implode(',',$cmbRegion_multi).")":
	               (($region_id!=0)?" and bmv.region_id='$region_id' ":"")):'').
		   		($skip_area_filter=='0'?(($area_id!=0)?" and bmv.area_id='$area_id' ":""):'');
		if(is_authorized_group_list($uid,array(5,6)) and !is_authorized_group_list($uid, array(36,25,8,1))){
			$territory_id_str='';		
				$result_terrcheck=execute_my_query("select get_territory_id from gft_emp_territory_dtl where get_emp_id= '".$uid."' and GET_STATUS='A' and get_work_area_type >2");
			if(mysqli_num_rows($result_terrcheck)>0){
				$query_concat_terri_id=" select group_concat(DISTINCT terr_id) area_terr_id FROM  gft_emp_territory_dtl,b_map_view b" .
					" where GET_STATUS='A' and ((get_work_area_type=5 and b.zone_id=get_territory_id) " .
					" or (get_work_area_type=4 and  b.region_id=get_territory_id) " .
					" or (get_work_area_type=3 and  b.area_id=get_territory_id ) " .
					" or (get_work_area_type=2 and  b.terr_id=get_territory_id ))" .
					" and get_emp_id='$uid' and terr_id!=100 " ;
			}else{				
				if(is_authorized_group_list($uid,array(102))){// If Territory role, show only assigned territory, before it was showing assigned and un-assigned
					$query_concat_terri_id=" select group_concat(DISTINCT terr_id) area_terr_id " .
							" FROM  gft_emp_territory_dtl join gft_business_territory_master bt1 on (GET_STATUS='A' and bt1.gbt_territory_id=get_territory_id and get_work_area_type=2) " .
							" join b_map_view b on  (b.terr_id =bt1.GBT_TERRITORY_ID )" .
							" where get_emp_id='$uid' and terr_id!=100 ";
				}else{
					$query_concat_terri_id=" select group_concat(DISTINCT terr_id) area_terr_id " .
							" FROM  gft_emp_territory_dtl join gft_business_territory_master bt1 on (GET_STATUS='A' and bt1.gbt_territory_id=get_territory_id and get_work_area_type=2) " .
							" join b_map_view b on  (b.area_id =bt1.gbt_map_id )" .
							" where get_emp_id='$uid' and terr_id!=100 ";
				}
			}
			$result_terri=execute_my_query($query_concat_terri_id);
			if($data_terri=mysqli_fetch_array($result_terri)){
				$territory_id_str=$data_terri['area_terr_id'];
			}
			if($include_default_territory==true){
				if($territory_id_str!=''){
					$territory_id_str.=",100";
				}else{
					$territory_id_str="100";
				}
			}
			if($skip_terr_filter=='0') {
			    $str.=(($terr_id!=0)?" and bmv.terr_id in ($terr_cond) ":($territory_id_str!=''?" and bmv.terr_id in ($territory_id_str) ":""));
			}
		}else {
		    if($skip_terr_filter=='0') {
		        $str.=(($terr_id!=0)?" and bmv.terr_id in ($terr_cond) ":"");
		    }
		}
		$str.=") ".($include_default_territory?(($terr_id=="0" or $terr_id=='')?" or (bmv.terr_id=$from_terr_colm and bmv.terr_id=100 )":""):' and bmv.terr_id!=100 ').")";
	}
	else if($include_zone==true){
		$str.=" inner join b_map_view bmv on (bmv.terr_id=$from_terr_colm ) " ;
	}else if($unknown_territory=='on'){
		$str.=" inner join b_map_view bmv on (bmv.terr_id=$from_terr_colm and bmv.terr_id in (1,100)) ";
	}
		
	if($state_id!=0  or $district_id!=0){
		$str.=" inner join p_map_view pmv on ( 1 ";//$from_dist_colimn=district_id 
		if($district_id!=0){
    		$str.=" and district_id =$district_id and district={$lead_hdr_alias}glh_cust_city ";
    	}
    	if($state_id!=0){
    		$str.=" and state_id =$state_id and state={$lead_hdr_alias}GLH_CUST_STATECODE ";
    	}
    	if($country_id!=0){
    		$str.=" and country_id=$country_id and {$lead_hdr_alias}glh_country=country";
    	}
	    $str.=" ) ";
	}else if($include_country==true){
		$str.=" inner join p_map_view pmv on ($from_dist_colimn=district_id )";
	}else if($country_id!=0){
		$dot = ($political_map_alias!='')?".":"";
			if($country_id!=-1){
				$str.=" inner join gft_political_map_master $political_map_alias on(".$political_map_alias.$dot."GPM_MAP_LEVEL=3 AND ".$political_map_alias.$dot."GPM_MAP_NAME={$lead_hdr_alias}glh_country and ".$political_map_alias.$dot."GPM_MAP_ID='$country_id') ";
			}elseif($country_id==-1){
				//$str.=" inner join gft_political_map_master $political_map_alias on({$lead_hdr_alias}glh_country!='India') ";
				$str .=" inner join (select GLH_LEAD_CODE as lc from gft_lead_hdr where glh_country!='India') overseas on (overseas.lc={$lead_hdr_alias}GLH_LEAD_CODE) ";
			}
	}
	if((int)$cmbgroup_id!=0){
		$group_que = " select distinct(a.gem_emp_id) uniq_eid from gft_emp_master a " .
					" left join gft_role_group_master rg on (grg_role_id=gem_role_id)" .
					" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id)" .
					" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id)" .
					" where ggm_group_id='$cmbgroup_id' ";
		$str .= " join ($group_que) gq on (uniq_eid=$emp_master_alias.GEM_EMP_ID)  ";
	}
	return $str;	   
}

/**
 * @return string
 */
function get_areamap_link(){
    global $zone_id,$region_id,$terr_id,$country_id,$state_id,$district_id;
	$areamap_link="&amp;cmbZone=$zone_id&amp;cmbRegion=$region_id&amp;cmbterr=$terr_id" .
			"&amp;cmbCountry=$country_id";
	if(is_array($state_id)){
		$state_id_a=implode(',',$state_id);
		$areamap_link.="&amp;cmbState[]=$state_id_a";
	}else{
		$areamap_link.="&amp;cmbState=$state_id";
	}	
	if(is_array($district_id)){
		$district_id_a=implode(',',$district_id);
		$areamap_link.="&amp;cmbDist[]=$district_id";
	}else{
		$areamap_link.="&amp;cmbDist=$district_id";
	}
	return $areamap_link;
}
/**
 * @param string $alias
 * @return string
 */
function get_lead_type_condition_by_filters($alias='lh') {
	global $indiv_order,$chain_order;
	$chk_lead_types = $query = "";
	if($indiv_order=='on'){ $chk_lead_types = "'1'"; }
	if($chain_order=='on'){ $chk_lead_types .= (($chk_lead_types=='')?"":",")."'3','13'"; }
	if($chk_lead_types!='') {
		$query.=" and lh.glh_lead_type in ($chk_lead_types) ";
	}
	return $query;
}

/**
 * @param string $order_type
 * 
 * @return string
 */
function check_order_type($order_type=null){
	global $product_family_type,$license_product;
    $query='';		
	if($order_type!="0" and $order_type!='-1' and $order_type!=''){
		if($order_type=='14'){$query=" and god_order_type in (1,4) ";}
		else if($order_type=='23'){$query=" and god_order_type in (2,3) ";}
		else {$query=" and god_order_type=$order_type ";}
	}
	$query .= get_lead_type_condition_by_filters();	
	if($product_family_type!=0 and $product_family_type==6){ $query.=" and GPM_CATEGORY='$product_family_type' "; }
	else if($product_family_type!=0 and $product_family_type!=6){ $query.=" and GPM_IS_INTERNAL_PRODUCT='$product_family_type' "; }
	if($license_product){$query.=" and GPM_IS_INTERNAL_PRODUCT in (0,2)";} 
    return $query;
}

/**
 * @return string
 */
function check_employee_related(){
	global $cmbgroup_id;
	if(isset($cmbgroup_id) and $cmbgroup_id!=0 and $cmbgroup_id!=''){
			$query1=" select distinct(em.gem_emp_id) as emp_id from gft_emp_master em " .
					"left join gft_role_group_master rg on (grg_role_id=gem_role_id) " .
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=em.gem_emp_id)" .
			" join gft_group_master g1 on ((g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id) and ggm_group_id in ($cmbgroup_id) )" ;
			$result1=execute_my_query($query1);
			$list_arr = array();
			while($emp_id=mysqli_fetch_array($result1)){
				$list_arr[] = $emp_id['emp_id'];
			}
			if(count($list_arr) > 0){
				$elist = implode(',',$list_arr);
				$query=" and em.gem_emp_id in ($elist) ";
				return $query;
			}
	}
	return null;
}

/**
 * @param int[] $skew_property
 * 
 * @return string
 */
function check_skew_property($skew_property){
	$query='';
	if(is_array($skew_property)){
    	if(count($skew_property)==1 and $skew_property[0]==0){
		return NULL;
	}
    	$query.=" and pm.gft_skew_property in (".implode(',',$skew_property).") ";
    }else{
	    if($skew_property!='' and $skew_property!=0){
			$query.=" and pm.gft_skew_property in ($skew_property)";	
	    }
    }
    return $query;   		
}

/**
 * @param string $product_type
 *
 * @return string
 */
function check_product_type($product_type){		
		$query='';
		if($product_type=='Y'  or  $product_type=='N'){
 				$query.=" and pm.gpm_free_edition='$product_type' ";
 		}else if($product_type!=0){
 				$query.=" and pm.GPM_PRODUCT_TYPE ='$product_type' ";
 		}
 		
 		global $lic_type;
 		if($lic_type=='P'){ $query.=" and pm.GFT_SKEW_PROPERTY=1 ";}
 		else if($lic_type=='S'){ $query.=" and pm.GFT_SKEW_PROPERTY=11 ";}
 		
 		return $query;
} 	

/**
 * @param string $include_employees
 * @param string $lead_hdr_table_alias
 * @param string $not_in_lead_type
 *
 * @return string
 */
function check_customer_dtl($include_employees='N',$lead_hdr_table_alias='',$not_in_lead_type=''){
	global $customer_id,$cust_name,$custCode,$customer_only,$lead_type,$vertical,$oncall_only;
	global $cmb_area,$loc_id, $pincode,$lead_status,$city_name,$marked_reference_list;
	global $response_group,$broadband,$corp_cust,$brand_id_filter;
	global $operation_pntimes,$no_times_planned, $operation_dntimes,$no_times_demo,$reference_group,$reference_list_code,$reference_group_with;
	global $address_verified,$preferred_comm,$customer_ids,$prospect_status;
	
	$query='';
	if($customer_only=='on'){ $query.=" and glh_is_customer='Y' ";	}
	$arr_key = (int)array_search('0',$lead_status);
	if(is_array($lead_status) and $lead_status[$arr_key]!='0'){
		$query.=" and GLH_STATUS in (".implode(',',$lead_status).") ";
	}
	if($prospect_status!=0){
		$query.=" and GLH_PROSPECTS_STATUS='$prospect_status' ";
	}
	if($customer_ids!=''){
		$query.=" and {$lead_hdr_table_alias}glh_lead_code in ($customer_ids) ";
	}
    if($customer_id!='' ){
    	$query.=" and {$lead_hdr_table_alias}glh_lead_code='$customer_id' ";
    }else if($corp_cust=='3' and $custCode!='' and $cust_name!=''){
    	$query.=" and (glh_lead_code='$custCode' or  (GLH_LEAD_SOURCECODE=7 and glh_reference_given=$custCode) ) "; 
    }else if($corp_cust=='3' and $custCode=='' and $cust_name==''){
    	$query.=" and (glh_lead_type='3' or (glh_lead_type=13 and GLH_LEAD_SOURCECODE=7 and glh_reference_given!='') ) "; 
    }else if($custCode!=''){
    	$query.=" and {$lead_hdr_table_alias}glh_lead_code='$custCode' ";
    }else if($cust_name!='' and $custCode==''){
		$query.= " and concat({$lead_hdr_table_alias}GLH_CUST_NAME,'-',{$lead_hdr_table_alias}glh_cust_streetaddr2) like '$cust_name%'";
	}
	if($preferred_comm!=0){ $query.=" and GLH_PREFERRED_COMMUNICATION='$preferred_comm' "; }
	//if($support_product_group!='' and $support_product_group!=0){$query.= " and GLH_MAIN_PRODUCT=$support_product_group";}
    if($cmb_area!="" and $cmb_area!=0){$query.=" and GLH_AREA_NAME like '$cmb_area%' ";}
	if($loc_id!="" and  $loc_id!="0"){$query.=" and GLH_CUST_STREETADDR2 like '$loc_id%' ";}
	if($city_name!='' and $city_name!="0"){	$query.=" and glh_cust_city like '$city_name%' ";}
	if($pincode!=0)	{$query.=" and {$lead_hdr_table_alias}GLH_CUST_PINCODE = '$pincode'";}
	if($lead_type!='' and $lead_type!='0'){$query.=" and GLH_LEAD_TYPE  in ($lead_type) ";}
	
	/* lead type!=8 check in other reports to make the executive details hide 
	 * but in techsupport executives may call and this in where condition 
	 */
	if($include_employees=='N'){$query.=($lead_type!=8?" and glh_lead_type!='8' ":""); }
	$query .= ($not_in_lead_type!='') ? " and glh_lead_type not in ($not_in_lead_type) ":"";
	global $registered_date,$include_micro;
	if($registered_date=='on' && $vertical!="" && $vertical!="0"){ 
		$query.=" and  GDP_VERTICAL_CODE='$vertical'"; 
	}else if($vertical!="0" and $vertical!=""){
		if($include_micro==1){
			$query.=" and ({$lead_hdr_table_alias}GLH_VERTICAL_CODE='$vertical' or {$lead_hdr_table_alias}GLH_VERTICAL_CODE in (select GTM_VERTICAL_CODE from gft_vertical_master where GTM_MICRO_OF='$vertical') ) ";
		}else{
			$query.=" and {$lead_hdr_table_alias}GLH_VERTICAL_CODE='$vertical' ";
		}
	}		
	if($brand_id_filter!=0){
	    $query .= " and {$lead_hdr_table_alias}GLH_VERTICAL_CODE in (select GBP_VERTICAL from gft_brand_product_mapping where GBP_BRAND_ID='$brand_id_filter' and GBP_STATUS=1) ";
	}
	if($marked_reference_list=='on'){$query.=" and GLH_REFERENCE_CUST='Y' ";} 
	if($oncall_only=='on'){$query.=" and isnull(GLH_REF_OUTLET_ID) ";}  
	global $cc_only;
	if($cc_only=='on'){$query.=" and GCD_VN_TRANSID!=0 ";}
	if($response_group!=0){$query.=" and GLH_RESPONSE_GROUP ='$response_group' ";}
	if($broadband=='on'){$query.=" and GLH_BROADBAND='Y' ";}
	if($operation_pntimes!="0" && $no_times_planned!=''){
    	if($operation_pntimes=="Less than"){ $query.= " and glh_plan_no < $no_times_planned";}
	    elseif($operation_pntimes=="Greater than"){ $query.= " and glh_plan_no > $no_times_planned";}
		elseif($operation_pntimes=="Equal to"){$query.= " and glh_plan_no = $no_times_planned";}	
    } 
	if($operation_dntimes!="0" && $no_times_demo!=''){
    	if($operation_dntimes=="Less than"){ $query.= " and glh_demo_count < $no_times_demo";}
	    elseif($operation_dntimes=="Greater than"){ $query.= " and glh_demo_count > $no_times_demo";}
		elseif($operation_dntimes=="Equal to"){$query.= " and glh_demo_count= $no_times_demo";}	
    }
    if($address_verified!='' && $address_verified!='0'){
    	$query.=" and GLH_ADDRESS_VERIFIED='$address_verified' "; 
    }
	if($reference_group!=0){
		$query_to_get_category="select GLS_SOURCE_CATEGORY from gft_lead_source_master where gls_source_code='$reference_group' ";
		$result_to_get_category=execute_my_query($query_to_get_category);
		$qds=mysqli_fetch_array($result_to_get_category);
		$reference_group_category=(int)$qds['GLS_SOURCE_CATEGORY'];
		if($reference_group_category==1){//internal
			$query.=" and GLH_LEAD_SOURCE_CODE_INTERNAL = $reference_group ";
			if($reference_list_code!=''){ $query.="and GLH_REFERENCE_INTERNAL='$reference_list_code' ";}
			if($reference_group_with=='-1'){ $query.=" and GLH_LEAD_SOURCECODE=0 and  GLH_LEAD_SOURCE_CODE_PARTNER=0 "; }
		}else if($reference_group_category==2){//partner
			$query.=" and GLH_LEAD_SOURCE_CODE_PARTNER = $reference_group ";
			if($reference_list_code!=''){ $query.="and GLH_REFERENCE_OF_PARTNER='$reference_list_code' ";}
			if($reference_group_with=='-1'){ $query.=" and GLH_LEAD_SOURCECODE=0 and  GLH_LEAD_SOURCE_CODE_INTERNAL=0 "; }
		}else if($reference_group_category==3){//cust_other
			$query.=" and glh_lead_sourcecode = $reference_group ";
			if($reference_list_code!=''){ $query.="and GLH_REFERENCE_GIVEN='$reference_list_code' ";}
			if($reference_group_with=='-1'){ $query.=" and GLH_LEAD_SOURCE_CODE_PARTNER=0 and  GLH_LEAD_SOURCE_CODE_INTERNAL=0 "; }
		}else{
			$query.=" and glh_lead_sourcecode = $reference_group ";
		}	
		if($reference_group_with=='9999'){ $query.=" and glh_created_by_empid='9999' "; }
		else if($reference_group_with=='-1'){ $query.=" and glh_created_by_empid!='9999' "; }
		else if($reference_group_with!=0){$query.=" and GLH_LEAD_SOURCECODE=$reference_group_with ";}
		//$query.=" and glh_lead_type!=13 ";
	}
	
	return $query;
}

/**
 * @param boolean $family_check
 * @param string $schedule_emp_alias
 * @param string $activity_emp_alias
 * 
 * @return string
 */
function check_common_support_dtl($family_check=true,$schedule_emp_alias='',$activity_emp_alias=''){
	global $cust_emotion,$severity,$severity_multi_str,$priority,$level,$complaint_code,$issue_type,$version,$C_ID,$fixedversion, $searchfor,$from_dt,$to_dt,$chk_visit_date,$chk_reported_date,$chk_complaint,$chk_promissed,
	$chk_scheduled,$chk_lst_activity,$escalation,$complaint_group,$support_calls,$chk_assigned,$emp_code,$incharge_type,$unassigned,
	$solved_within,$scall_category,$team_name_select;
	$query='';
	if($C_ID!='' and $C_ID!='0'){
		$query.= " AND hdr.GCH_COMPLAINT_ID = '$C_ID' ";
	}
	if(is_array($complaint_code) && count($complaint_code) > 0){
	    $complaint_code_arr = implode(',', $complaint_code);
	    if($complaint_code_arr!=0) $query.=" and GCH_COMPLAINT_CODE in ($complaint_code_arr) ";
	}
	if($issue_type!='' and $issue_type!='0' and $issue_type!='null'){$query.=" and dtl.GCD_CUST_CALL_TYPE='$issue_type' ";	}
	if($issue_type=='null'){$query.=" and (dtl.GCD_CUST_CALL_TYPE='' or dtl.GCD_CUST_CALL_TYPE is null) ";	}
	if($version!=0 and $version!=''){$query.=" and hdr.GCH_VERSION	='$version' ";	}
	if($fixedversion!=0 and $fixedversion!='')	{$query.=" and hdr.GCH_FIXED_IN_VERSION='$fixedversion' ";	}
	if($complaint_group!='' and $complaint_group!='0' ){$query.=" and GFT_COMPLAINT_GROUP='$complaint_group' ";}
	if($escalation=='on'){ $query.=" and GCH_ESCALATION='Y' "; }
	if($family_check){
		$query.=check_product_family();
	}
	if($scall_category!='' and $scall_category!=0){	$query.=" and GCH_CALL_TYPE='$scall_category' ";}
	if($incharge_type!='' and $incharge_type!='0'){
		$incharge_type_column='';
		if($incharge_type=="Sales Incharge"){
			$incharge_type_column="glh_lfd_emp_id";
		}else if($incharge_type=="Reg Incharge"){
			 $incharge_type_column="glh_l1_incharge";
		}else if($incharge_type=="Field Incharge"){
			$incharge_type_column="glh_field_incharge";
		}
		if($emp_code!='' and $emp_code!=0 and $incharge_type_column!=''){
			$query.=" and $incharge_type_column=$emp_code ";
		}
	}else{
		if($emp_code=='9999'){
			$query.=" and (dtl.GCD_PROCESS_EMP is null or dtl.GCD_PROCESS_EMP='' or dtl.GCD_PROCESS_EMP='0'  or dtl.GCD_PROCESS_EMP='9999') ";
		}else{
			if($chk_assigned=='on'){
				if($unassigned=='1') { // only unassigned tickets
				    $query .= " and (dtl.GCD_PROCESS_EMP is null or dtl.GCD_PROCESS_EMP='0' or dtl.GCD_PROCESS_EMP='') ";
				} else {
				    if((int)$emp_code>0) {
				        $query .= " and (dtl.GCD_PROCESS_EMP='$emp_code' ";
				    } else {
				        $query .= " and (dtl.GCD_PROCESS_EMP!=''";
				    }
				    if($unassigned=='2') { // include unassigned with assigned
				        $query .= " or dtl.GCD_PROCESS_EMP is null or dtl.GCD_PROCESS_EMP='0' or dtl.GCD_PROCESS_EMP='' ";
				    }
				    $query .= ")";
				}
			}else if($emp_code!='' and ($incharge_type==0 or $incharge_type=='')){
				$query.=" and dtl.GCD_EMPLOYEE_ID='$emp_code' ";
			}
		}
	}
	if($team_name_select!='0') {
	    $emp_master_alias = (($chk_assigned=='on')?$schedule_emp_alias:$activity_emp_alias);
	    $query .= get_team_condition_for_param("$team_name_select",$emp_master_alias);
	}
	global $extended_support;
	if($extended_support=="on"){
 			$query.=" and GCH_ASS_CUST='E' ";
 	}
	if($cust_emotion!='' and $cust_emotion!='0')$query.=" and dtl.GCD_CUSTOMER_EMOTION='$cust_emotion' ";
	if((int)$severity!=0) {
	    if($severity=='-1') {
	        $query .=" and dtl.GCD_SEVERITY is null ";
	    } else {
	        $query .=" and dtl.GCD_SEVERITY='$severity' ";
	    }
	}
	if( ($severity_multi_str!='') && ($severity_multi_str!='0')) $query.=" and dtl.GCD_SEVERITY in ($severity_multi_str) ";
	if($priority!='' and $priority!='0')$query.=" and dtl.GCD_PRIORITY='$priority' ";
	if($level!='' and $level!='0') $query.=" and dtl.GCD_LEVEL='$level' ";
	if($searchfor !=''){
		$query.=" and (dtl.GCD_PROBLEM_SUMMARY like '%$searchfor%' or dtl.GCD_PROBLEM_DESC like '%$searchfor%' or dtl.GCD_REMARKS like '%$searchfor%') ";
	}
	if($from_dt!='' or $to_dt!=''){
	  	$query_from_dt=db_date_format($from_dt);
      	$query_to_dt=db_date_format($to_dt);
		if($query_from_dt!=''){$query_from_dt.=' 00:00:00';}
		if($query_to_dt!='') {$query_to_dt.=' 23:59:59';}	
		if($chk_visit_date=="on"  or  $chk_reported_date=="on" or $chk_complaint=="on"  or  
				$chk_promissed=="on" or  $chk_scheduled=="on"  or $chk_lst_activity=="on"){
			if($chk_complaint=="on"){
			   if($query_from_dt!=''){ $query.=" and hdr.GCH_COMPLAINT_DATE>='$query_from_dt' ";}
			   if($query_to_dt!=''){  $query.=" and hdr.GCH_COMPLAINT_DATE<='$query_to_dt' ";}
			}		
			if($support_calls==3){//opening
				$query.=" and dtl.gcd_activity_date <'$query_from_dt' ";
			}else if($support_calls==4){//closing
				$query.=" and dtl.gcd_activity_date <='$query_to_dt' ";
			}else if($chk_visit_date=="on"){
			   if($query_from_dt!=''){ $query.=" AND dtl.GCD_ACTIVITY_DATE >= '$query_from_dt' "; }
			   if($query_to_dt!=''){ $query.=" AND dtl.GCD_ACTIVITY_DATE <= '$query_to_dt'"; }
			}
			if($chk_reported_date=="on"){
	 			if($query_from_dt!=''){ $query.=" and dtl.GCD_REPORTED_DATE>='$query_from_dt' "; }
			    if($query_to_dt!=''){$query.=" and dtl.GCD_REPORTED_DATE<='$query_to_dt' "; }
			}
			if($chk_promissed=="on"){
			   if($query_from_dt!=''){  $query.=" and GCD_PROMISE_DATE>='$query_from_dt' "; }
			   if($query_to_dt!=''){  $query.=" and GCD_PROMISE_DATE<='$query_to_dt' "; }
			}
			if($chk_scheduled=="on"){
			   if($query_from_dt!=''){ $query.=" and GCD_SCHEDULE_DATE>='$query_from_dt'"; }
			   if($query_to_dt!=''){$query.=" and GCD_SCHEDULE_DATE<='$query_to_dt' ";}
			}
			if($chk_lst_activity=="on"){
			   $query.=" and gcd_activity_id=GCH_LAST_ACTIVITY_ID";
			   if($query_from_dt!=''){ $query.=" AND dtl.GCD_ACTIVITY_DATE >= '$query_from_dt' "; }
			   if($query_to_dt!=''){ $query.=" AND dtl.GCD_ACTIVITY_DATE <= '$query_to_dt'"; }
			}
		}else {
			if($query_from_dt!=''){ $query.=" and hdr.GCH_COMPLAINT_DATE>='$query_from_dt' ";}
			if($query_to_dt!=''){  $query.=" and hdr.GCH_COMPLAINT_DATE<='$query_to_dt' ";}
		}
	}
	if($support_calls==1){
		$query.=" and date(GCH_COMPLAINT_DATE)=date(GCD_ACTIVITY_DATE) and GCD_LAST_ACTIVITY_OF_DAY='Y' ";
	}else if($support_calls==2){
		$query.=" and date(GCH_COMPLAINT_DATE)<date(GCD_ACTIVITY_DATE) and GCD_LAST_ACTIVITY_OF_DAY='Y'  ";
	}
	if($solved_within!=0){
		if($solved_within=='1'){
			$query.=" and gch_complaint_date=gcd_reported_date and gtm_group_id='3' ";
		}elseif($solved_within=='2'){ 
			$query.=" and timediff(gcd_reported_date,gch_complaint_date)<='00:30:00' and gch_complaint_date!=gcd_reported_date and gtm_group_id='3'";
		}elseif($solved_within=='3'){ $query.=" and timediff(gcd_reported_date,gch_complaint_date)<='04:00:00' and gch_complaint_date!=gcd_reported_date " .
				" and timediff(gcd_reported_date,gch_complaint_date) >'00:30:00' and gtm_group_id='3' ";
		}elseif($solved_within=='4'){
			$query.=" and timediff(gcd_reported_date,gch_complaint_date)>'04:00:00' and timestampdiff(HOUR,gch_complaint_date,gcd_reported_date)<'25' and gtm_group_id='3'";
		}elseif($solved_within=='5'){
			$query.=" and timestampdiff(HOUR,gch_complaint_date,gcd_reported_date)<='48' and timestampdiff(HOUR,gch_complaint_date,gcd_reported_date)>='25'  and gtm_group_id='3'";
		}elseif($solved_within=='6'){
			$query.=" and datediff(gcd_reported_date,gch_complaint_date)<=7  and timestampdiff(HOUR,gch_complaint_date,gcd_reported_date)>48 and gtm_group_id='3'";
		}elseif($solved_within=='7'){
			$query.=" and datediff(gcd_reported_date,gch_complaint_date)>7 and gtm_group_id='3' ";
		}
	}
	return $query;
}

/**
 * @return string 
 */
function check_product_family(){
	$query='';	
	global $product;
	$sid=explode('-',$product);
	$product1=$sid[0];
	if(isset($sid[1])){
		$familyver=$sid[1];			
		if($product1!='' and $product1!=0){
			$query=" AND pg.gpg_product_family_code = $product1 AND pg.gpg_skew ='$familyver' ";
		}
	}
	return $query;
}

/**
 * @return string
 */
function check_cp_order(){
	$query='';
	global $cp_lead_code;
	if($cp_lead_code!=0){
    	$query=" and god_lead_code='$cp_lead_code' ";
    }
    return $query;
}	

/**
 * @return string
 */
function get_last_insert_id(){
	 $query1="select last_insert_id()";
	 $result1=execute_my_query($query1);
	 $qd=mysqli_fetch_array($result1);
	 $last_insert_id=$qd[0];
	 return $last_insert_id;
}

/**
 * @param string $table_alias
 * @param string $from_date
 * @param string $to_date
 *
 * @return string
 */
function get_employee_according_to_working($table_alias,$from_date,$to_date){
		$query=" and em.gem_doj<='$to_date' and ((em.gem_status='A' ) or 
		 (em.gem_status='I' and (em.gem_dor >='$from_date' or em.gem_dor='0000-00-00'))) ";
		return $query; 
	}

/**
 * @param string $exten_no
 * @param int $avg_based_on
 * @param string $from_dt
 * @param string $to_dt
 * @param string $phone_no
 * @param string $call_type
 * @param string $emp_code
 * @param string $upload_fileid
 * 
 * @return string
 */
function tech_extno_filter($exten_no=null,$avg_based_on=0,$from_dt=null,$to_dt=null,$phone_no=null,$call_type=null,$emp_code=null,$upload_fileid=''){
	$cust_count_numbers="";
	global $office_id,$vs_call_sytatus,$vs_call_agent,$vs_callback_status,$support_product_group,$not_in_support_product_group,$trans_id_filter,$call_stat_multi;
	if($upload_fileid!=''){
		$cust_count_numbers.=" and GTC_UPLOAD_FILE_ID = ".$upload_fileid;
	}
	if($office_id!='')$cust_count_numbers.=" and GTC_OFFICE_ID=$office_id ";
	if($vs_call_sytatus!='' and $vs_call_sytatus!='0') $cust_count_numbers.=" and GTC_CALL_STATUS=$vs_call_sytatus " ;
	if( is_array($call_stat_multi) && (count($call_stat_multi) > 0) ){
		$cs = implode(",",$call_stat_multi);
		if($cs!='0'){
			$cust_count_numbers .= " and GTC_CALL_STATUS in ($cs) ";
		}
	}
	if($vs_callback_status!='' and $vs_callback_status!='0') $cust_count_numbers.=" and GTC_RECALL_STATUS='$vs_callback_status' " ;
	if($vs_call_agent!='' and $vs_call_agent!=0) {
	    if($vs_call_agent==-1) {
	        $cust_count_numbers  .= " and (GTC_AGENT_ID=0 or GTC_AGENT_ID=9999 or GTC_AGENT_ID is NULL) ";
	    } else {
	       $cust_count_numbers .= " and GTC_AGENT_ID=$vs_call_agent " ;
	    }
	}
	if($exten_no!='all' and $exten_no!='techsupport' and $exten_no!='others' and $exten_no!=''){
	   	$cust_count_numbers.=" and gtc_exten = '$exten_no'";	
	}else if($exten_no=='techsupport'  or  $exten_no=='others'){
		$get_extnesion_no=get_extension_no_list($office_id,$group=1,$default_caption='',$status='A');
    	if(count($get_extnesion_no)>0){
	    	$extno=/*. (string[int]) .*/ array();
	    	for($j=0;$j<count($get_extnesion_no);$j++){
	    		$extno[$j]=$get_extnesion_no[$j][0];
	    	}
	    	$extno_inc=implode(',',$extno);
	    	if($exten_no=='techsupport'){
	    		$cust_count_numbers.=" and gtc_exten in ($extno_inc) ";
	    	}else {
		   		$cust_count_numbers.=" and gtc_exten  not in ($extno_inc) ";
		   	}
		}
	}
	if($avg_based_on==1){$cust_count_numbers.=" and GTC_CALL_STATUS in (1,2,3) ";}
	else if($avg_based_on==2){$cust_count_numbers.=" and GTC_CALL_STATUS in (4,5) ";}
	else if($avg_based_on==3){$cust_count_numbers.=" and GTC_CALL_STATUS in (3) ";}
	else if($avg_based_on==4){$cust_count_numbers.=" and time_to_sec(GTC_DURATION)!=0 ";}
	else if($avg_based_on==5 or $avg_based_on==6){$cust_count_numbers.=" and GTC_CALL_STATUS in (1,2,3) ";}
	else if($avg_based_on==7 or $avg_based_on==8){;}
	else if($avg_based_on==9) {
	    $cust_count_numbers .= " and GTC_CALL_STATUS=3 and TIME_TO_SEC(GTC_RING_TIME)>=10 ";
	}
	$query_from_dt=db_date_format($from_dt);
	$query_to_dt=db_date_format($to_dt);
	if($query_from_dt!=''){$query_from_dt.=' 00:00:00';}
	if($query_to_dt!='') {$query_to_dt.=' 23:59:59';}
	global $from_date_time, $to_date_time,$call_received_medium;
	if( ($from_date_time!='') && ($to_date_time!='') ){
		$query_from_dt = $from_date_time;
		$query_to_dt = $to_date_time;
	}
   	if($from_dt!=''){
		$cust_count_numbers.=" and gtc_date >= '$query_from_dt' "; 
	} 
	if( $to_dt!=''){
		$cust_count_numbers.=" and gtc_date <= '$query_to_dt'"; 
	} 
	if($phone_no!=''){
		//$orig_phone_no=$phone_no;
		//$phone_no=substr($phone_no,-10);
		//$cust_count_numbers.=" and gtc_number like '%$phone_no'";
		//$cust_count_numbers.=" and (gtc_number = '".$phone_no."' or gtc_number='0".$phone_no."' or gtc_number='91".$phone_no."' or gtc_number='00".$phone_no."' or gtc_number='".$orig_phone_no."' )";
		$cust_count_numbers.=getGtcNumberWhereCondition($phone_no);
	}
	if($support_product_group!=0 and $support_product_group!=''){
		$cust_count_numbers.=" and GSP_GROUP_ID=$support_product_group " ;
	}
	if($not_in_support_product_group!=''){
		$cust_count_numbers.=" and GSP_GROUP_ID not in ($not_in_support_product_group) " ;
	}
	if($trans_id_filter!=''){
		$cust_count_numbers.=" and GTC_TRANS_ID = '$trans_id_filter' " ;
	}
	if($call_type!='' and $call_type!='0'){
		if($call_type=='1'){
 		    $cust_count_numbers.=" and gtc_emp_id is not null and gtc_emp_id!=0";
		}else if($call_type=='2'){
			$cust_count_numbers.=" and gtc_lead_code is not null and gtc_lead_code!=0 and (gtc_emp_id is null or gtc_emp_id=0)";			 		    	
		}else if($call_type=='3'){
			$cust_count_numbers.=" and (gtc_emp_id is null or gtc_emp_id=0) and (gtc_lead_code is null or gtc_lead_code=0)";
			if($call_received_medium=='vsmile'){
				$cust_count_numbers .= " and GTC_RECALL_STATUS!='NR' ";
			}
	    }else if($call_type=='4'){
			$cust_count_numbers.=" and (GTC_MAIN_GROUP=901 and GTC_RECALL_STATUS='NR') ";
	    }
	}
	if($emp_code != ''){
		$cust_count_numbers.=" and gtc_emp_id = '$emp_code'";
	}
    return $cust_count_numbers;
}

/**
 *
 * @return string
 */
function get_call_received_from_condition(){
    global $call_received_medium;
    $query = "";
    if($call_received_medium=='intellicon'){
        $query.= " and (GTC_SPECIFIC_REASON is null OR GTC_SPECIFIC_REASON='') ";
    }else if($call_received_medium!='' && $call_received_medium!='0'){
        $query.= " and GTC_SPECIFIC_REASON='$call_received_medium' ";
    }
    return $query;
}
/**
 * @param boolean $defaultVar
 * 
 * @return string
 */
function join_cp_work_range($defaultVar=false){	
	global $terr_id,$region_id,$zone_id;	
	if($terr_id!=0 or $region_id!=0  or $zone_id!=0 or $defaultVar==true){		
		 $query=" left join gft_emp_territory_dtl c on (c.GET_EMP_ID=em.GEM_EMP_ID and c.GET_STATUS='A' ) ".
		"left join gft_work_area_master wam on (wam.gwm_code=c.get_work_area_type) ". 
						" left join b_map_view	on ( (wam.gwm_code in (1,2) and terr_id=c.GET_TERRITORY_ID) " .
					"  or (wam.gwm_code=3 and area_id=c.GET_TERRITORY_ID) " .
					"  or (wam.gwm_code=4 and region_id=c.GET_TERRITORY_ID) " .
					"  or (wam.gwm_code=5 and zone_id=c.GET_TERRITORY_ID) )" ;
		 return $query;
 			
	}
	return null;
}//end of function	

/**
 * @param string $created_date
 * @param string $bmap_alias
 *
 * @return string
 */
function common_cp_filters($created_date='on',$bmap_alias=''){
	global $status,$cp_code,$from_dt,$to_dt,$incharge_emp_code,$relational_emp_code,$prospect_status,
	$lead_sub_type;
	global $terr_id,$region_id,$zone_id,$cp_relationship;
	$query=" and gcp.CGI_EMP_ID!=7004 and em.gem_role_id in ( 21,27,73,83 ) "; 
	$query.=(($status=='A' or $status=='I')? " and em.gem_status='$status' ":"");
	if($cp_code!=''){
		$query.=" and em.gem_emp_id='$cp_code' ";
	}	
	if(($from_dt!=''  or  $to_dt!='') and $created_date=='on'){
		if($from_dt!=''){	$query.=" and gcp.CGI_CREATED_DATE>='".db_date_format($from_dt)."'";}
		if($to_dt!=''){	$query.=" and gcp.CGI_CREATED_DATE<='".db_date_format($to_dt)."'";}
	}
	if($bmap_alias!=''){
		$bmap_alias	=	"$bmap_alias.";
	}
	if($incharge_emp_code!=''){ $query.=" and gcp.cgi_incharge_emp_id='$incharge_emp_code' ";}
	if($relational_emp_code!=''){ $query.=" and gcp.CGI_RELATIONSHIP_MANAGER='$relational_emp_code' ";}
	if($prospect_status!=0){ $query.=" and gcp.cgi_status=$prospect_status ";}
	if($lead_sub_type!=0){ $query.=" and cg.GCA_CP_SUB_TYPE=$lead_sub_type "; } 
	if($cp_relationship!=0){ $query.=" and cg.GCA_PARTNER_RELATIONSHIP=$cp_relationship  ";}	
	if($terr_id!=0){ $query.=" and ".$bmap_alias."terr_id=$terr_id";}
	else if($region_id!=0){	$query.=" and ".$bmap_alias."region_id=$region_id";	}
	else if($zone_id!=0){	$query.=" and ".$bmap_alias."zone_id=$zone_id";		}
	return $query;
}//end of function

/**
 * @param string $lead_code
 * @param int $type
 * @param boolean $include_expense
 *
 * @return string[int][int]
 */
function get_server_products($lead_code,$type=1,$include_expense=false){
	$product = /*. (string[int][int]) .*/array();
	$skew_arr = /*. (string[int]) .*/array();
	$skew_prop = '';
	if($type==1){
		$skew_prop = "7,8,19,23";
	}elseif ($type==2){
		$skew_prop = "12";		
	}elseif ($type==3){
		$skew_prop = "7,8,12,19,23";
	}
	
	$ser_query= " select GOD_LEAD_CODE, GOD_ORDER_NO, GOP_FULLFILLMENT_NO, pm.GPM_PRODUCT_CODE, pm.GPM_PRODUCT_SKEW, ".
			" GPM_PRODUCT_NAME, GPM_PRODUCT_ABR, GPM_SKEW_DESC, sum(GOP_QTY) as tot_qty, sum(GOP_USEDQTY) as tot_usedqty, ".
			" (sum(GOP_QTY) - sum(GOP_USEDQTY)) as pending_qty, substring(pm.GPM_PRODUCT_SKEW,1,4) pgroup, GFT_SKEW_PROPERTY,GOP_SERVICE_TAX_RATE ".
			" from gft_order_hdr oh ".
			" join gft_order_product_dtl on (GOP_ORDER_NO = GOD_ORDER_NO) ".
			" join gft_product_master pm on (pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and pm.GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			" join gft_product_family_master pfm on (pm.GPM_PRODUCT_CODE=pfm.GPM_PRODUCT_CODE) ".
			" where GFT_SKEW_PROPERTY in ($skew_prop) ";
	$ser_query.=" and oh.GOD_LEAD_CODE='$lead_code' ";
	$ser_query.=" group by pm.GPM_PRODUCT_CODE,pm.GPM_PRODUCT_SKEW ".
				" order by pfm.gpm_product_code,pgroup,GFT_SKEW_PROPERTY ";
	
	$ser_res = execute_my_query($ser_query);
	$i=0;
	while($qdata=mysqli_fetch_array($ser_res)){
		$product[$i][0] = $qdata['GPM_PRODUCT_CODE']."-".$qdata['GPM_PRODUCT_SKEW'];
		$product[$i][1] = $qdata['GPM_PRODUCT_NAME']."-".$qdata['GPM_SKEW_DESC'];
		$product[$i][2] = $qdata['tot_qty'];
		$product[$i][3] = $qdata['tot_usedqty'];
		$product[$i][4] = $qdata['pending_qty'];
		$product[$i][5] = $qdata['GPM_PRODUCT_NAME'];
		$product[$i][6] = $qdata['GPM_PRODUCT_ABR'].'-'.$qdata['GPM_SKEW_DESC'];
		$product[$i][7] = $qdata['GOP_SERVICE_TAX_RATE'];
		$skew_arr[]		= $qdata['GFT_SKEW_PROPERTY'];
		$i++;
	}
	if($include_expense && (!in_array('12', $skew_arr)) ){ //TODO: need to have better solution to append the expense skew 
		$ser_query= " select pm.GPM_PRODUCT_CODE, pm.GPM_PRODUCT_SKEW, GPM_PRODUCT_NAME, GPM_PRODUCT_ABR, GPM_SKEW_DESC,GPM_SERVISE_TAX_PERC ".
				" from gft_product_master pm ".
				" join gft_product_family_master pfm on (pm.GPM_PRODUCT_CODE=pfm.GPM_PRODUCT_CODE) ".
				" where GFT_SKEW_PROPERTY = 12 and pm.GPM_STATUS='A' and pm.GPM_PRODUCT_CODE=391 ";  //expense skew property
		$ser_query.=" group by pm.GPM_PRODUCT_CODE,pm.GPM_PRODUCT_SKEW ".
				" order by pfm.gpm_product_code ";
		$ser_res = execute_my_query($ser_query);
		if($qdata=mysqli_fetch_array($ser_res)){
			$product[$i][0] = $qdata['GPM_PRODUCT_CODE']."-".$qdata['GPM_PRODUCT_SKEW'];
			$product[$i][1] = $qdata['GPM_PRODUCT_NAME']."-".$qdata['GPM_SKEW_DESC'];
			$product[$i][2] = 0;
			$product[$i][3] = 0;
			$product[$i][4] = 0;
			$product[$i][5] = $qdata['GPM_PRODUCT_NAME'];
			$product[$i][6] = $qdata['GPM_PRODUCT_ABR'].'-'.$qdata['GPM_SKEW_DESC'];
			$product[$i][7] = $qdata['GPM_SERVISE_TAX_PERC'];
			$i++;
		}
	}
	return $product;
}
/**
 * @return string[int][int]
 */
function get_team_names_for_mis() {
    return array(array('1','RPOS 6.5'),array('2','RPOS 7'),array('3','DE'),array('8','TRAC'),array('4','HQ'),array('5','TruePOS'),array('9','ServQuick'),array('6','Annuity Team'),array('7','Presales'));
}
?>
