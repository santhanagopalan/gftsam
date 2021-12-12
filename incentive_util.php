<?php
require_once(__DIR__ ."/dbcon.php");
/**
 * @param string $emply_id
 * @param string $bucket_val
 * @param string[string][int] $ctc_info_arr
 * @param string[string][int] $plan_info_arr
 *
 * @return string[string]
 */
function get_incentive_slab_from_mapped_plan_and_ctc($emply_id,$bucket_val,$ctc_info_arr,$plan_info_arr){
    $slab_val = "0";
    $emp_ctc = isset($ctc_info_arr[$emply_id][0])?(int)$ctc_info_arr[$emply_id][0]:0;
    $inc_plan = isset($plan_info_arr[$emply_id][0])?(int)$plan_info_arr[$emply_id][0]:0;
    $dtl_arr = /*. (string[string]) .*/array();
    $field_arr = /*. (int[string]) .*/array();
    if( ($emp_ctc > 0) && ($inc_plan > 0) ){
        $quer = " select GPD_LEVEL_ID,GPD_MIN_TIMES from gft_incentive_plan_dtl where GPD_PLAN_ID='$inc_plan' ";
        $qres = execute_my_query($quer);
        while($qrow = mysqli_fetch_array($qres)){
            $field_arr[$qrow['GPD_LEVEL_ID']] = round(($emp_ctc * (float)$qrow['GPD_MIN_TIMES']) - (float)$bucket_val);
        }
        $bucket_ratio = round($bucket_val/$emp_ctc,2);
        $sql1 = " select GPL_ID,GPL_NAME,GPD_INCENTIVE_PERCENT from gft_incentive_plan_dtl ".
            " join gft_performance_level_master on (GPL_ID=GPD_LEVEL_ID) ".
            " where GPD_PLAN_ID = '$inc_plan' and '$bucket_ratio' between GPD_MIN_TIMES and GPD_MAX_TIMES order by GPD_MIN_TIMES desc limit 1 ";
        $res1 = execute_my_query($sql1);
        if($row1 = mysqli_fetch_array($res1)){
            $dtl_arr['id'] 		= $row1['GPL_ID'];
            $dtl_arr['name'] 	= $row1['GPL_NAME'];
            $dtl_arr['percent'] = $row1['GPD_INCENTIVE_PERCENT'];
        }
        $dtl_arr['slab_dtl'] = $field_arr;
    }
    return $dtl_arr;
}

/**
 * @param string $emply_id
 * @param string $bucket_val
 * @param string[string][int] $ctc_info_arr
 * @param string $role_id
 * 
 * @return string[string]
 */
function get_incentive_slab_info_from_ctc($emply_id,$bucket_val,$ctc_info_arr,$role_id=''){
	$slab_val = "0";
	$emp_ctc = isset($ctc_info_arr[$emply_id][0])?(int)$ctc_info_arr[$emply_id][0]:0;
	$dtl_arr = /*. (string[string]) .*/array();
	$condition_column = " GIS_MIN_VALUE and GIS_MAX_VALUE ";
	$min_column_name = " GIS_MIN_VALUE";
	if($role_id=='2'){
	    $condition_column = " GIS_PC_MIN_VALUE and GIS_PC_MAX_VALUE ";
	    $min_column_name = " GIS_PC_MIN_VALUE";
	}
	$quer = " select GIS_ID, $min_column_name AS GIS_MIN_VALUE from gft_incentive_slab_master ";
	$qres = execute_my_query($quer);
	$field_arr = /*. (int[string]) .*/array();
	while($qrow = mysqli_fetch_array($qres)){
		$field_arr[$qrow['GIS_ID']] = ($emp_ctc * (float)$qrow['GIS_MIN_VALUE']) - (float)$bucket_val; 
	}
	if($emp_ctc > 0){
		$bucket_ratio = round($bucket_val/$emp_ctc,2);
		$sql1 = " select GIS_ID,GIS_SLAB_NAME,GIS_PERCENT from gft_incentive_slab_master ".
				" where $bucket_ratio between $condition_column ";
		$res1 = execute_my_query($sql1);
		if($row1 = mysqli_fetch_array($res1)){
			$dtl_arr['id'] 		= $row1['GIS_ID'];
			$dtl_arr['name'] 	= $row1['GIS_SLAB_NAME'];
			$dtl_arr['percent'] = $row1['GIS_PERCENT'];
		}
		$dtl_arr['slab_dtl'] = $field_arr;
	}
	return $dtl_arr;
}

/**
 * @param string $effective_date
 * 
 * @return string[string][int]
 */
function get_ctc_info_of_all_for_given_date($effective_date){
	$csub = " select GCI_EMP_ID emp_id,max(GCI_EFFECTIVE_FROM) eff_date from gft_ctc_info where GCI_EFFECTIVE_FROM <= '$effective_date' group by GCI_EMP_ID ";
	$ctc_que = " select GCI_EMP_ID,GCI_MONTHLY_CTC from gft_ctc_info ci ".
			" join ($csub) t1 on (t1.emp_id=GCI_EMP_ID and t1.eff_date=GCI_EFFECTIVE_FROM) ";
	$ctc_res = execute_my_query($ctc_que);
	$ctc_info_arr = /*. (string[string][int]) .*/array();
	while($ctc_row1 = mysqli_fetch_array($ctc_res)){
		$ctc_info_arr[$ctc_row1['GCI_EMP_ID']][0] = $ctc_row1['GCI_MONTHLY_CTC'];
	}
	return $ctc_info_arr;
}
/**
 * @param string $effective_date
 *
 * @return string[string][int]
 */
function get_incentive_plan_info_of_all_for_given_date($effective_date){
    $plan_check_date = $effective_date;
    $date_que_res = execute_my_query("select GEP_MONTH,GEP_YEAR from gft_expense_monthly_process_dtl where GEP_FROM_DATE='$effective_date' and GEP_TYPE=2");
    if($date_row = mysqli_fetch_array($date_que_res)){
        $plan_check_date = date($date_row['GEP_YEAR']."-".substr("0".$date_row['GEP_MONTH'], -2)."-01");
    }
    $sub1 = " select GEE_EMP_ID emp_id,max(GEE_EFFECTIVE_DATE) eff_date from gft_emp_effective_dates where GEE_TYPE='incentive_plan' and GEE_EFFECTIVE_DATE <= '$plan_check_date' group by GEE_EMP_ID ";
    $inc_que = " select GEE_EMP_ID,GEE_VALUE,GIP_COST_OF_SALES_ENABLED from gft_emp_effective_dates ci ".
               " join ($sub1) t1 on (t1.emp_id=GEE_EMP_ID and t1.eff_date=GEE_EFFECTIVE_DATE) ".
               " join gft_incentive_plan_master on (GIP_ID=GEE_VALUE) where GEE_TYPE='incentive_plan' ";
    $inc_res = execute_my_query($inc_que);
    $inc_info_arr = /*. (string[string][int]) .*/array();
    while($row1 = mysqli_fetch_array($inc_res)){
        $inc_info_arr[$row1['GEE_EMP_ID']] = array($row1['GEE_VALUE'],$row1['GIP_COST_OF_SALES_ENABLED']);
    }
    return $inc_info_arr;
}

/**
 * @param string $effective_date
 *
 * @return string[string][int]
 */
function get_dev_milestone_monthly_salary_percentage($effective_date){
    $sub1 = " select GDM_MONTH_NO mn,max(GDM_EFFECTIVE_DATE) eff_date from gft_dev_milestone_salary_percentage where GDM_EFFECTIVE_DATE <= '$effective_date' group by GDM_MONTH_NO ";
    $que = " select GDM_MONTH_NO,GDM_PERC from gft_dev_milestone_salary_percentage ci ".
           " join ($sub1) t1 on (t1.mn=GDM_MONTH_NO and t1.eff_date=GDM_EFFECTIVE_DATE) ";
    $res = execute_my_query($que);
    $inc_info_arr = /*. (string[string][int]) .*/array();
    while($row1 = mysqli_fetch_array($res)){
        $inc_info_arr[$row1['GDM_MONTH_NO']][0] = $row1['GDM_PERC'];
    }
    return $inc_info_arr;
}

/**
 * @param string $effective_date
 *
 * @return string[string][int]
 */
function get_dev_milestone_mrp_formula($effective_date){
    $sub1 = " select GDM_SLAB_ID,GDM_MIN_PERC,GDM_MAX_PERC,GDM_WEIGHTAGE,max(GDM_EFFECTIVE_DATE) eff_date from gft_dev_milestone_mrp_formula where GDM_EFFECTIVE_DATE <= '$effective_date' group by GDM_SLAB_ID ";
    $que =  " select ci.GDM_SLAB_ID,ci.GDM_MIN_PERC,ci.GDM_MAX_PERC,ci.GDM_WEIGHTAGE from gft_dev_milestone_mrp_formula ci ".
            " join ($sub1) t1 on (t1.GDM_SLAB_ID=ci.GDM_SLAB_ID and t1.eff_date=ci.GDM_EFFECTIVE_DATE) order by ci.GDM_SLAB_ID ";
    $res = execute_my_query($que);
    $inc_info_arr = /*. (string[string][int]) .*/array();
    while($row1 = mysqli_fetch_array($res)){
        $inc_info_arr[$row1['GDM_SLAB_ID']] = array($row1['GDM_MIN_PERC'],$row1['GDM_MAX_PERC'],$row1['GDM_WEIGHTAGE']);
    }
    return $inc_info_arr;
}

/**
 * @param string $month_start_date
 * @param string $month_end_date
 *
 * @return string[string][int]
 */
function get_ctc_info_of_all_for_given_date_prorate($month_start_date,$month_end_date){
    $csub = " select GCI_EMP_ID emp_id,max(GCI_EFFECTIVE_FROM) eff_date from gft_ctc_info where GCI_EFFECTIVE_FROM <= '$month_end_date' group by GCI_EMP_ID ";
    $ctc_que = " select GCI_EMP_ID,GCI_MONTHLY_CTC,GCI_MRP,GCI_EFFECTIVE_FROM from gft_ctc_info ci ".
        " join ($csub) t1 on (t1.emp_id=GCI_EMP_ID and t1.eff_date=GCI_EFFECTIVE_FROM) ";
    $ctc_res = execute_my_query($ctc_que);
    $ctc_info_arr = /*. (string[string][int]) .*/array();
    $month_start_date_time = $month_start_date." 00:00:00";
    $month_end_date_time = $month_end_date." 23:59:59";
    $numberofdays_in_months = round((strtotime($month_end_date_time)-strtotime($month_start_date_time)) / (60 * 60 * 24));
    while($ctc_row1 = mysqli_fetch_array($ctc_res)){        
        $employee_id = $ctc_row1['GCI_EMP_ID'];
        $eff_date = date('Y-m-d', strtotime($ctc_row1['GCI_EFFECTIVE_FROM']));
        $eff_date_time = date('Y-m-d 00:00:00', strtotime($ctc_row1['GCI_EFFECTIVE_FROM']));
        $ctc_amt = $mrp_amt = 0;
        if(strtotime($eff_date_time)<= strtotime($month_start_date_time)){
            $ctc_amt = $ctc_row1['GCI_MONTHLY_CTC'];
            $mrp_amt = $ctc_row1['GCI_MRP'];
        }else if(strtotime($eff_date_time)>strtotime($month_start_date_time)){
            $number_of_days = round((strtotime($month_end_date_time)-strtotime($eff_date_time)) / (60 * 60 * 24));
            $ctc_amt = (($number_of_days/$numberofdays_in_months)*$ctc_row1['GCI_MONTHLY_CTC']);
            $query =" select GCI_MONTHLY_CTC from gft_ctc_info".
                    " where GCI_EFFECTIVE_FROM < '$eff_date' AND GCI_EMP_ID='$employee_id' ORDER BY GCI_EFFECTIVE_FROM DESC LIMIT 1";
            $ctc_amt_remaining = get_single_value_from_single_query("GCI_MONTHLY_CTC", $query);
            if($ctc_amt_remaining!=""){
                $ctc_amt1 = ((($numberofdays_in_months-$number_of_days)/$numberofdays_in_months)*$ctc_amt_remaining);
                $ctc_amt = $ctc_amt+$ctc_amt1;
            }
        }
        $ctc_info_arr[$ctc_row1['GCI_EMP_ID']] = array($ctc_amt,$mrp_amt);
    }
    return $ctc_info_arr;
}
/**
 * @param string $month_start_date
 * @param string $month_end_date
 *
 * @return string[string][int]
 */
function get_mrp_info_of_all_for_given_date_prorate($month_start_date,$month_end_date){
    $csub = " select GMI_EMP_ID emp_id,max(GMI_EFFECTIVE_FROM) eff_date from gft_mrp_info where GMI_EFFECTIVE_FROM <= '$month_end_date' group by GMI_EMP_ID ";
    $mrp_que = " select GMI_EMP_ID,GMI_MONTHLY_MRP,GMI_EFFECTIVE_FROM from gft_mrp_info ci ".
        " join ($csub) t1 on (t1.emp_id=GMI_EMP_ID and t1.eff_date=GMI_EFFECTIVE_FROM) ";
    $mrp_res = execute_my_query($mrp_que);
    $mrp_info_arr = /*. (string[string][int]) .*/array();
    $month_start_date_time = $month_start_date." 00:00:00";
    $month_end_date_time = $month_end_date." 23:59:59";
    $numberofdays_in_months = round((strtotime($month_end_date_time)-strtotime($month_start_date_time)) / (60 * 60 * 24));
    while($mrp_row1 = mysqli_fetch_array($mrp_res)){
        $employee_id = $mrp_row1['GMI_EMP_ID'];
        $eff_date = date('Y-m-d', strtotime($mrp_row1['GMI_EFFECTIVE_FROM']));
        $eff_date_time = date('Y-m-d 00:00:00', strtotime($mrp_row1['GMI_EFFECTIVE_FROM']));
        $mrp_amt = 0;
        if(strtotime($eff_date_time)<= strtotime($month_start_date_time)){
            $mrp_amt = $mrp_row1['GMI_MONTHLY_MRP'];
        }else if(strtotime($eff_date_time)>strtotime($month_start_date_time)){
            $number_of_days = round((strtotime($month_end_date_time)-strtotime($eff_date_time)) / (60 * 60 * 24));
            $mrp_amt = (($number_of_days/$numberofdays_in_months)*$mrp_row1['GMI_MONTHLY_MRP']);
            $query =" select GMI_MONTHLY_MRP from gft_mrp_info".
                " where GMI_EFFECTIVE_FROM < '$eff_date' AND GMI_EMP_ID='$employee_id' ORDER BY GMI_EFFECTIVE_FROM DESC LIMIT 1";
            $mrp_amt_remaining = get_single_value_from_single_query("GMI_MONTHLY_MRP", $query);
            if($mrp_amt_remaining!=""){
                $mrp_amt1 = ((($numberofdays_in_months-$number_of_days)/$numberofdays_in_months)*$mrp_amt_remaining);
                $mrp_amt = $mrp_amt+$mrp_amt1;
            }
        }
        $mrp_info_arr[$mrp_row1['GMI_EMP_ID']][0] = $mrp_amt;
    }
    return $mrp_info_arr;
}
/**
 * @param string $start_date
 * @param string $end_date
 * @param int $emp_id
 * 
 * @return string
 */
function get_query_for_booking_expense($start_date, $end_date, $emp_id=0){
    $booking_expense_query =" select SUM(GBH_BOOKING_EXPENSE) BOOKING_EXPENSE, GBH_INCENTIVE_BUCKET_EMP from gft_booking_hdr ".
                    " where GBH_STATUS=2 AND GBH_PAYABLE_BY=1 AND date(GBH_FROM_DT) BETWEEN '$start_date' AND '$end_date'". 
                    ($emp_id>0?" AND GBH_INCENTIVE_BUCKET_EMP='$emp_id'":"")." GROUP BY GBH_INCENTIVE_BUCKET_EMP";
    
    return $booking_expense_query;
}
/**
 * @param string $start_date
 * @param string $end_date
 * @param string $emplyId
 * @param string $skip_roles
 *
 * @return string[int]
 */
function get_query_for_earnings_and_deductions($start_date,$end_date, $emplyId='',$skip_roles=''){
    $month_val 	= date('m',strtotime($end_date));
    $year_val 	= date('Y',strtotime($end_date));
    $sub1 		=" select geh_emp_id,(geh_total_travelexp_claimed+geh_official_mobile_bill) as expenses ".
        " from gft_exec_expense_hdr where geh_month='$month_val' and geh_year='$year_val' ";
    $booking_expense_query = get_query_for_booking_expense($start_date, $end_date,(int)$emplyId);
    $kpi_query = "select GAC_KPI KPI, GAC_EMP_ID from gft_agile_cprr where GAC_YEAR='$year_val'".
                 " AND GAC_MONTH='$month_val' AND GAC_APPROVAL_STATUS=2 AND GAC_METRIC_TYPE=1 GROUP BY GAC_EMP_ID";
    $arp_query = "select GAC_KPI ARP, GAC_EMP_ID from gft_agile_cprr where GAC_YEAR='$year_val'".
        " AND GAC_MONTH='$month_val' AND GAC_APPROVAL_STATUS=2 AND GAC_METRIC_TYPE=2 GROUP BY GAC_EMP_ID";
    $common_wh_cond = "";
    if((int)$emplyId!=0){
        $common_wh_cond .= " and GEM_EMP_ID='$emplyId' ";
    }
    if($skip_roles!=''){
        $common_wh_cond .= " and GEM_ROLE_ID not in ($skip_roles) ";
    }
    
    $sql1 = " select GEM_EMP_ID,GEM_EMP_NAME,GEM_PROFILE_URL,GEM_ROLE_ID,GRM_ROLE_DESC,sum(GIE_INCENTIVE_AMT) bucket_amt, ".
        " expenses,GIA_ID,GIA_ATTRIBUTE_NAME,sum(GCD_COMMISSION_AMT) as reff_comm, BOOKING_EXPENSE, KPI, ARP ".
        " from gft_orderwise_incentive_owner ".
        " join gft_incentive_earning on (GOI_ID=GIE_OWNER_REF_ID) ".
        " join gft_incentive_attribute_master on (GOI_ATTRIBUTE_ID=GIA_ID) ".
        " join gft_emp_master em on (em.GEM_EMP_ID=GOI_OWNER_EMP) ".
        " left join gft_order_hdr on (GOD_ORDER_NO=GOI_ORDER_NO) ".
        " join gft_role_master on (GRM_ROLE_ID=GEM_ROLE_ID) ".
        " left join gft_receipt_dtl on (GRD_RECEIPT_ID=GIE_RECEIPT_ID and GRD_CHECKED_WITH_LEDGER='Y') ".
        " left join gft_reff_commission_dtl on (GCD_ORDER_NO=GOI_ORDER_NO and GOI_ATTRIBUTE_ID=4) ".
        " left join ($sub1) t1 on (GEM_EMP_ID=t1.geh_emp_id) ".
        " left join ($booking_expense_query)expense_tbl on(GEM_EMP_ID=expense_tbl.GBH_INCENTIVE_BUCKET_EMP)".
        " left join ($kpi_query)kp ON(kp.GAC_EMP_ID=GEM_EMP_ID)".
        " left join ($arp_query)ar ON(ar.GAC_EMP_ID=GEM_EMP_ID)".
        " where if(GOI_INCENTIVE_TYPE=4,date(GOI_CREATED_DATE),if((GOD_EMP_ID>7000 && GOD_EMP_ID != 9999),date(GIE_CREATED_DATE),if(GOI_ATTRIBUTE_ID!=13,GRD_CHEQUE_CLEARED_DATE,date(GIE_CREATED_DATE)))) between '$start_date' and '$end_date' ".
        " $common_wh_cond group by GEM_EMP_ID,GOI_ATTRIBUTE_ID order by GEM_EMP_NAME ";
    
    $partner_effective_date = get_samee_const("PARTNER_AGILE_EFFECTIVE_DATE");
    $partner_effective_condition = "";
    if($partner_effective_date!=''){//partner collection displayed before partner_effective_date
        $partner_effective_condition = " and GRD_CHEQUE_CLEARED_DATE<'$partner_effective_date' ";
    }
    $sub_que = " select GRD_RECEIPT_ID rid ,substring_index(group_concat(GBI_INCHARGE_EMP order by GBI_EFFECTIVE_FROM desc),',',1) as incharge ".
               " from gft_receipt_dtl join gft_cp_info on (GRD_LEAD_CODE=CGI_LEAD_CODE) ".
               " left join gft_business_incharge on (GRD_LEAD_CODE=GBI_LEAD_CODE and GBI_EFFECTIVE_FROM<=GRD_CHEQUE_CLEARED_DATE) ".
               " where 1 $partner_effective_condition and GRD_CHEQUE_CLEARED_DATE between '$start_date' and '$end_date' ".
               " group by GRD_RECEIPT_ID";
    
    $part_que = " select gem_emp_id,GEM_EMP_NAME,GEM_ROLE_ID,GEM_PROFILE_URL,GRM_ROLE_DESC,expenses,".
        " round(sum(if(GLH_COUNTRY='India',(GRD_RECEIPT_AMT*100/118),GRD_RECEIPT_AMT)),2) as pcoll ".
        " from gft_lead_hdr join gft_receipt_dtl on (GRD_LEAD_CODE=GLH_LEAD_CODE) ".
        " left join ($sub_que) pe on (GRD_RECEIPT_ID=pe.rid) ".
        " join gft_cp_info on (GRD_LEAD_CODE=CGI_LEAD_CODE or GRD_EMP_ID=CGI_EMP_ID) ".
        " left join gft_emp_master on (GEM_EMP_ID=if(GLH_COUNTRY='India',if(GRD_ACCOUNT=3,CGI_DEALER_INCHARGE,if(pe.incharge is null,cgi_incharge_emp_id,pe.incharge)),if(GRD_ACCOUNT=3,CGI_DEALER_INCHARGE,GRD_EMP_ID))) ".
        " join gft_role_master on (GRM_ROLE_ID=GEM_ROLE_ID) ".
        " left join ($sub1) t1 on (GEM_EMP_ID=t1.geh_emp_id) ".
        " where 1 $partner_effective_condition and GLH_LEAD_TYPE=2 and CGI_LEAD_CODE is not null and GRD_CHEQUE_CLEARED_DATE between '$start_date' and '$end_date' and GRD_CHECKED_WITH_LEDGER='Y' and GRD_RECEIPT_TYPE not in (8,11) ".
        " and gem_emp_id < 7000 and gem_role_id!=59 $common_wh_cond group by gem_emp_id ";
    $return_query[0] = $sql1;
    $return_query[1] = $part_que;
    return $return_query;
}
/**
 * @param string $emply_id
 * @param string $year_val
 * @param string $month_val
 *
 * @return string[int]
 */
function get_expense_amount($emply_id,$year_val,$month_val){
    $start_date = date("$year_val-$month_val-01");
    $end_date = date("$year_val-$month_val-t");
    $booking_expense_query = get_query_for_booking_expense($start_date, $end_date,$emply_id);
    $que1 = " select geh_emp_id,(geh_total_travelexp_claimed+geh_official_mobile_bill) as expenses, BOOKING_EXPENSE ".
        " from gft_exec_expense_hdr ".
        " left join ($booking_expense_query) booking_tbl  on(geh_emp_id=booking_tbl.GBH_INCENTIVE_BUCKET_EMP)".
        "where geh_month='$month_val' and geh_year='$year_val' and geh_emp_id='$emply_id' ";
    $res1 = execute_my_query($que1);
    $return_arr = array(0=>0,1=>0);
    if($row1 = mysqli_fetch_array($res1)){
        $return_arr[0] = (float)$row1['expenses'];
        $return_arr[1] = (float)$row1['BOOKING_EXPENSE'];
    }
    return $return_arr;
}

/**
 * @param string $date_val
 * 
 * @return string[string][int]
 */
function get_all_employee_expenses($start_date,$end_date){
    $from_month_val 	= date('m',strtotime($start_date));
    $from_year_val 	= date('Y',strtotime($start_date));
    $to_month_val 	= date('m',strtotime($end_date));
    $to_year_val 	= date('Y',strtotime($end_date));
    $booking_expense_query = get_query_for_booking_expense($start_date, $end_date);
    $que1 = " select geh_emp_id,(geh_total_travelexp_claimed+geh_official_mobile_bill) as expenses, BOOKING_EXPENSE ".
        " from gft_exec_expense_hdr ".
        " left join ($booking_expense_query) booking_tbl  on(geh_emp_id=booking_tbl.GBH_INCENTIVE_BUCKET_EMP)".
        " where geh_month='$to_month_val' and geh_year='$to_year_val' ";
    $res1 = execute_my_query($que1);
    $ret_arr = /*. (string[string][int]) .*/array(); 
    while($row1 = mysqli_fetch_array($res1)){
        $ret_arr[$row1['geh_emp_id']][0] = $row1['expenses'];
        $ret_arr[$row1['geh_emp_id']][1] = $row1['BOOKING_EXPENSE'];
    }
    return $ret_arr;
}

/**
 * @param string $start_date
 * @param string $end_date
 * @param string $emplyId
 * @param string $skip_roles
 * @param string $skip_metrics
 * @param string $metrics_req_ids
 *
 * @return mixed[int]
 */
function get_earnings_and_deductions_arr($start_date,$end_date, $emplyId='',$skip_roles='',$skip_metrics='',$metrics_req_ids=''){
    $arr 		= /*. (string[string][string]) .*/array();
    $d_arr		= /*. (string[string][string]) .*/array();
    $e_arr		= /*. (string[string][int][string]) .*/array();
    $incentive_query  =get_query_for_earnings_and_deductions($start_date,$end_date, $emplyId,$skip_roles='');
    //$sql1       =   $incentive_query[0];    
    $month_val 	= date('m',strtotime($end_date));
    $year_val 	= date('Y',strtotime($end_date));
    $sub1 		=" select geh_emp_id,(geh_total_travelexp_claimed+geh_official_mobile_bill) as expenses ".
        " from gft_exec_expense_hdr where geh_month='$month_val' and geh_year='$year_val' ";
    $booking_expense_query = get_query_for_booking_expense($start_date, $end_date);
    $common_wh_cond = "";
    if((int)$emplyId!=0){
        $common_wh_cond .= " and GEM_EMP_ID='$emplyId' ";
    }
    if($skip_roles!=''){
        $common_wh_cond .= " and GEM_ROLE_ID not in ($skip_roles) ";
    }
    if($skip_metrics!=''){
        if($metrics_req_ids!=''){
            $common_wh_cond .= " and if(GEM_EMP_ID in ($metrics_req_ids),1,GOI_ATTRIBUTE_ID not in ($skip_metrics)) ";
        }else{
            $common_wh_cond .= " and GOI_ATTRIBUTE_ID not in ($skip_metrics) ";
        }
    }
    
    $sql1 = " select GEM_EMP_ID,GEM_EMP_NAME,GEM_PROFILE_URL,GEM_ROLE_ID,GRM_ROLE_DESC,sum(GIE_INCENTIVE_AMT) bucket_amt, ".
        " expenses,BOOKING_EXPENSE booking_expense,GIA_ID,GIA_ATTRIBUTE_NAME ".
        " from gft_orderwise_incentive_owner ".
        " join gft_incentive_earning on (GOI_ID=GIE_OWNER_REF_ID) ".
        " join gft_incentive_attribute_master on (GOI_ATTRIBUTE_ID=GIA_ID) ".
        " left join gft_order_hdr on (GOD_ORDER_NO=GOI_ORDER_NO) ".
        " join gft_emp_master em on (em.GEM_EMP_ID=GOI_OWNER_EMP) ".
        " join gft_role_master on (GRM_ROLE_ID=GEM_ROLE_ID) ".
        " left join gft_receipt_dtl on (GRD_RECEIPT_ID=GIE_RECEIPT_ID and GRD_CHECKED_WITH_LEDGER='Y') ".
        " left join ($sub1) t1 on (GEM_EMP_ID=t1.geh_emp_id) ".
        " left join ($booking_expense_query) booking_tbl  on(GEM_EMP_ID=booking_tbl.GBH_INCENTIVE_BUCKET_EMP)".
        " where if(GOD_ORDER_NO is null,1,GOD_ORDER_STATUS='A') and if(GOI_INCENTIVE_TYPE=4,date(GOI_CREATED_DATE),if((GOD_EMP_ID>7000 && GOD_EMP_ID != 9999),date(GIE_CREATED_DATE),if(GOI_ATTRIBUTE_ID!=13,GRD_CHEQUE_CLEARED_DATE,date(GIE_CREATED_DATE)))) between '$start_date' and '$end_date' ".
        " $common_wh_cond group by GEM_EMP_ID,GOI_ATTRIBUTE_ID order by GEM_EMP_NAME ";
    $res1 = execute_my_query($sql1);
    $temp_emply_id = 0;
    while($row1 = mysqli_fetch_array($res1)){
        $emply_id 	= $row1['GEM_EMP_ID'];
        $profile_url = $row1['GEM_PROFILE_URL'];
        if($profile_url==""){
            $profile_url = "images/Profile.png";
        }
        $expenses	= (float)$row1['expenses'];
        $booking_expense = (float)$row1['booking_expense'];
        $arr[$emply_id]		= array(
            'emp_name'		=> $row1['GEM_EMP_NAME'],
            'profile_url'	=> $profile_url,
            'role_id'		=> $row1['GEM_ROLE_ID'],
            'role'			=> $row1['GRM_ROLE_DESC'],
            'expense'		=> $expenses,
            'booking_expense'=> $booking_expense,
            'reff_comm'		=> "0"
        );
        $d_arr[$emply_id]	= array(
            array('id'=>'expense','label'=>'Expenses claimed','amount'=>$expenses,'clickable'=>true,"navigate_to"=>"expenses"),
            array('id'=>'booking_expense','label'=>'Booking Expenses','amount'=>$booking_expense,'clickable'=>true,"navigate_to"=>"expenses")
        );
        $e_arr[$emply_id][]	= array(
            'id'		=> $row1['GIA_ID'],
            'label'		=> $row1['GIA_ATTRIBUTE_NAME'],
            'amount'	=> (float)$row1['bucket_amt'],
            'clickable'	=> true
        );
    }
    $part_que =   $incentive_query[1];
    $part_res = execute_my_query($part_que);
    while ($part_row = mysqli_fetch_array($part_res)){
        $pemp = $part_row['gem_emp_id'];
        $e_arr[$pemp][]	= array(
            'id'		=> '-1',
            'label'		=> 'Partner Collection',
            'amount'	=> (float)$part_row['pcoll'],
            'clickable'	=> false
        );
        if(!isset($arr[$pemp])){
            $profile_url = ($part_row['GEM_PROFILE_URL']=="")?"images/Profile.png":$part_row['GEM_PROFILE_URL'];
            $expenses_dtl	= get_expense_amount($pemp,$year_val,$month_val);
            $expenses = $expenses_dtl[0];
            $booking_expense = $expenses_dtl[1];
            $arr[$pemp]		= array(
                'emp_name'		=> $part_row['GEM_EMP_NAME'],
                'profile_url'	=> $profile_url,
                'role_id'		=> $part_row['GEM_ROLE_ID'],
                'role'			=> $part_row['GRM_ROLE_DESC'],
                'expense'		=> $expenses,
                'booking_expense'=> $booking_expense,
                'reff_comm'		=> 0
            );
            $d_arr[$pemp]	= array(
                array('id'=>'expense','label'=>'Expenses claimed','amount'=>$expenses,'clickable'=>true,"navigate_to"=>"expenses"),
                array('id'=>'booking_expense','label'=>'Booking Expenses','amount'=>$booking_expense,'clickable'=>true,"navigate_to"=>"expenses")
            );
        }
    }
    
    $ret_arr[0] = $arr;
    $ret_arr[1] = $e_arr;
    $ret_arr[2] = $d_arr;
    return $ret_arr;
}
/**
 * @param string $emply_id
 * @param float $revenue_amt
 *
 * @return string[int]
 */
function get_revenue_slab_dtl($emply_id,$revenue_amt){
	$ret_arr = /*. (string[string]) .*/array();
	$sql2 = " select GID_SLAB_ID,GID_FLOOR_AMOUNT from gft_incentive_dtl ".
			" join gft_emp_master on (GEM_INCENTIVE_ID=GID_PLAN_ID) ".
			" where GEM_EMP_ID='$emply_id' ";
	$res2 = execute_my_query($sql2);
	$field_arr = /*. (int[string]) .*/array();
	while($qrow = mysqli_fetch_array($res2)){
		$field_arr[$qrow['GID_SLAB_ID']] = (float)$qrow['GID_FLOOR_AMOUNT'] - (float)$revenue_amt;
	}
	$ret_arr['slab_dtl']= $field_arr;	
	$sql1 = " select GID_SLAB_ID,GIS_SLAB_NAME,GID_INCENTIVE_PERC from gft_incentive_dtl ".
			" join gft_emp_master on (GEM_INCENTIVE_ID=GID_PLAN_ID) ".
			" join gft_incentive_slab_master on (GID_SLAB_ID=GIS_ID) ".
			" where GEM_EMP_ID='$emply_id' and '$revenue_amt' >= GID_FLOOR_AMOUNT order by GID_SLAB_ID desc limit 1 ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$ret_arr['id'] 		= $row1['GID_SLAB_ID'];
		$ret_arr['name']	= $row1['GIS_SLAB_NAME'];
		$ret_arr['percent']	= $row1['GID_INCENTIVE_PERC'];
	}
	return $ret_arr;
}

/**
 * @return string[int]
 */
function get_incentive_slab_master(){
	$mq = execute_my_query("select GIS_ID,GIS_SLAB_NAME from gft_incentive_slab_master");
	$slab_master = /*. (string[int]) .*/array();
	while ($mr = mysqli_fetch_array($mq)){
		$slab_master[(int)$mr['GIS_ID']] = $mr['GIS_SLAB_NAME'];
	}
	return $slab_master;
}


/**
 * @param float $ratio_val
 * 
 * @return int
 */
function get_ratio_slab_dtl($ratio_val){
	$ratio_slab_id 	= 0;
	$cs_ratio = floor($ratio_val);
	if ($cs_ratio >= 10){
		$ratio_slab_id = 3;
	}elseif ($cs_ratio >= 7){
		$ratio_slab_id = 2;
	}elseif ($cs_ratio >= 4){
		$ratio_slab_id = 1;
	}
	return $ratio_slab_id;	
}

/**
 * @param string $start_date
 * @param string $end_date
 * @param int $month
 * @param int $year
 * @param string $employee_id
 * @param int $data_patch
 *
 * @return void
 */
function update_cost_of_sales_details($start_date,$end_date,$month,$year, $employee_id='',$data_patch=0){
    $ctc_info_arr 	= get_ctc_info_of_all_for_given_date_prorate($start_date, $end_date);
    $mrp_info_arr 	= get_mrp_info_of_all_for_given_date_prorate($start_date, $end_date);
    $incentive_query  =get_query_for_earnings_and_deductions($start_date,$end_date, $employee_id,'');
    //For employee earning and deductions
    $sql1   =   $incentive_query[0];
    $employee_details = array();
    $sql1 = "SELECT GEM_EMP_ID, GEM_EMP_NAME, SUM(bucket_amt) bucket_amt, expenses, SUM(reff_comm) reff_comm,".
        " GEM_ROLE_ID, BOOKING_EXPENSE, KPI, ARP FROM($sql1)tbl group by GEM_EMP_ID";
    $res1 = execute_my_query($sql1);
    while ($earningrow=mysqli_fetch_assoc($res1)){
        $empoyeeId = $earningrow['GEM_EMP_ID'];
        $emp_ctc = isset($ctc_info_arr[$empoyeeId][0])?$ctc_info_arr[$empoyeeId][0]:0;
        $emp_mrp = isset($mrp_info_arr[$empoyeeId][0])?$mrp_info_arr[$empoyeeId][0]:0;
        $employee_details[$empoyeeId] = array('employee_name'=>$earningrow['GEM_EMP_NAME'],
            'earning_amt'=>$earningrow['bucket_amt'],
            'expense'=>((float)$earningrow['expenses']+(float)$earningrow['BOOKING_EXPENSE']),
            'reference_commission'=>$earningrow['reff_comm'],
            'employee_salary'=>$emp_ctc,
            'emp_mrp'=>$emp_mrp,
            'kpi'=>$earningrow['KPI'],
            'arp'=>$earningrow['ARP'],
            'role_id'=>$earningrow['GEM_ROLE_ID'] );
    }
    //For getting partner collection
    $part_que   =   $incentive_query[1];
    $part_res = execute_my_query($part_que);
    while ($part_row = mysqli_fetch_array($part_res)){
        $empoyeeId = $part_row['gem_emp_id'];
        if(isset($employee_details[$empoyeeId])){
            $employee_details[$empoyeeId]['partner_collection']=$part_row['pcoll'];
        }else{
            $emp_ctc = isset($ctc_info_arr[$empoyeeId][0])?$ctc_info_arr[$empoyeeId][0]:0;
            $emp_mrp = isset($mrp_info_arr[$empoyeeId][0])?$mrp_info_arr[$empoyeeId][0]:0;
            $employee_details[$empoyeeId] = array('employee_name'=>$part_row['GEM_EMP_NAME'],
                'earning_amt'=>0,
                'expense'=>$part_row['expenses'],
                'reference_commission'=>0,
                'partner_collection'=>$part_row['pcoll'],
                'employee_salary'=>$emp_ctc,
                'emp_mrp'=>$emp_mrp,
                'kpi'=>'',
                'arp'=>'',
                'role_id'=>$part_row['GEM_ROLE_ID']);
        }
    }
    $mysql1 = " select em.GEM_EMP_ID, GEM_EMP_NAME, em.GEM_ROLE_ID   from gft_emp_master em ".
            " left join gft_role_master b on em.GEM_ROLE_ID=b.GRM_ROLE_ID ".
            " left join gft_emp_group_master gm on (gm.gem_emp_id=em.gem_emp_id) ". 
            " left join gft_role_group_master rg on (rg.GRG_ROLE_ID=b.GRM_ROLE_ID) ".
            " where em.gem_status='A' and (gm.GEM_GROUP_ID IN(23,27,36,54) or rg.GRG_GROUP_ID IN(23,27,36,54) ) ";
    if((int)$employee_id!=0){
    	$mysql1 .= " and em.GEM_EMP_ID='$employee_id' ";
    }
    $mysql1 .= " group by em.GEM_EMP_ID ";
    $filed_team_emps = execute_my_query($mysql1);
    while($row_field=mysqli_fetch_assoc($filed_team_emps)){
        $emplo_id = $row_field['GEM_EMP_ID'];
        if(!isset($employee_details[$emplo_id])){
            $expense_query = "select sum(expenses) expenses from (select SUM(GBH_BOOKING_EXPENSE) expenses, GBH_INCENTIVE_BUCKET_EMP emp_id  from gft_booking_hdr ".
                " where GBH_STATUS=2 AND GBH_PAYABLE_BY=1 AND date(GBH_FROM_DT) BETWEEN '$start_date' AND '$end_date' ".
                " AND GBH_INCENTIVE_BUCKET_EMP='$emplo_id' GROUP BY GBH_INCENTIVE_BUCKET_EMP UNION ".
                " select (geh_total_travelexp_claimed+geh_official_mobile_bill) as expenses,
                            geh_emp_id emp_id from gft_exec_expense_hdr where geh_month='$month' and geh_year='$year' AND geh_emp_id='$emplo_id')tbl group by emp_id";
            $expense_amt = get_single_value_from_single_query("expenses", $expense_query);
            $kpi_query  =  "select GAC_KPI KPI  from gft_agile_cprr where GAC_EMP_ID='$emplo_id' AND  GAC_YEAR='$year' AND GAC_MONTH='$month' AND GAC_APPROVAL_STATUS=2 AND GAC_METRIC_TYPE=1 GROUP BY GAC_EMP_ID";
            $kpi_amt = get_single_value_from_single_query("KPI", $kpi_query);
            $arp_amt = get_single_value_from_single_query("KPI", "select GAC_KPI KPI  from gft_agile_cprr where GAC_EMP_ID='$emplo_id' AND  GAC_YEAR='$year' AND GAC_MONTH='$month' AND GAC_APPROVAL_STATUS=2 AND GAC_METRIC_TYPE=2 GROUP BY GAC_EMP_ID");
            $emp_ctc = isset($ctc_info_arr[$emplo_id][0])?$ctc_info_arr[$emplo_id][0]:0;
            $emp_mrp = isset($mrp_info_arr[$emplo_id][0])?$mrp_info_arr[$emplo_id][0]:0;
            $employee_details[$emplo_id] = array('employee_name'=>$row_field['GEM_EMP_NAME'],
                'earning_amt'=>0,
                'expense'=>$expense_amt,
                'reference_commission'=>0,
                'partner_collection'=>0,
                'employee_salary'=>$emp_ctc,
                'emp_mrp'=>$emp_mrp,
                'kpi'=>"$kpi_amt",
                'arp'=>"$arp_amt",
                'role_id'=>$row_field['GEM_ROLE_ID']);
            
        }
    }
    //One time purpose, to be removed once run in live
    if($data_patch==1){
        $emp_condition= "";
        if($employee_id>0){
            $emp_condition = "  inner join gft_emp_manager_relation on( (gmr_emp_id in (em.gem_emp_id) OR gmr_emp_id=cgi_incharge_emp_id) and gmr_emp_id='$employee_id' ) ";
        }
        $employee_details = array();
        $query =<<<QUERY
    select partner_order_amt,partner_coll,indep_orders,indep_order_amt,chain_orders,chain_order_amt, service_inv,total_order_amt,inst_cnt,coll_amt,end_usr_coll,hq_usr_coll, em.GEM_EMP_ID,em.GEM_EMP_NAME,em.GEM_STATUS as stat,GRG_ROLE_ID,GRG_GROUP_ID,cgi_incharge_emp_id,GOD_LEAD_CODE ,em.GEM_MOBILE,em.GEM_EMAIL,em.GEM_IC,em.GEM_DOJ,em.GEM_REPORTING_MGR_NAME, em.GEM_TITLE,em.GEM_LOCATION_OF_WORKING ,ind_prospect,hq_prospect,tot_prospects, new_count1,new_count2,e_amt,p_amt from gft_emp_master em left join gft_cp_info on (CGI_EMP_ID=em.GEM_EMP_ID ) inner join gft_role_group_master on ( GRG_ROLE_ID=em.GEM_ROLE_ID and (GRG_GROUP_ID in (66,23,13,54,82,70,11) or em.GEM_ROLE_ID=59)) left join gft_emp_group_master egm on (egm.GEM_EMP_ID = em.GEM_EMP_ID and egm.GEM_GROUP_ID in (54,27,106)) $emp_condition left join (select if(em1.gem_emp_id=9999,emp_other.gem_emp_id,em1.gem_emp_id) gem_emp_id, if(em1.gem_emp_id=9999,emp_other.GEM_STATUS,em1.GEM_STATUS) GEM_STATUS,GOD_YR_MONTH,GOD_LEAD_CODE, sum(if(lh.glh_lead_type in (1,2) and gpm_free_edition='N' ,gop_qty,0)) indep_orders, sum(if(lh.glh_lead_type in (1,2),round(gop_sell_amt,0),0)) indep_order_amt , sum(if(lh.glh_lead_type in ('3','13') and gpm_free_edition='N' ,gop_qty,0)) chain_orders, sum(if(lh.glh_lead_type in ('3','13'), round(gop_sell_amt),0)) chain_order_amt , sum(if(god_emp_id<7000 and glh_lead_type=2,round(gop_sell_amt),0)) partner_order_amt, sum(if(god_order_type=2,round(gop_sell_amt,0),0)) service_inv , sum(if(gpm_free_edition='N',round(gop_sell_amt),0)) 'total_order_amt' from gft_order_hdr oh join gft_order_product_dtl on (god_order_no=gop_order_no) join gft_product_master on (gop_product_code=gpm_product_code and gop_product_skew=gpm_product_skew) join gft_lead_hdr lh on(lh.GLH_LEAD_CODE=oh.GOD_LEAD_CODE and lh.glh_lead_type in (1,2,3,13)) inner join gft_emp_master em1 on (em1.gem_emp_id=god_emp_id) left join gft_collection_receipt_dtl on ( GCR_ORDER_NO=GOD_ORDER_NO and GOD_EMP_ID=9999) left join gft_receipt_dtl on (GRD_RECEIPT_ID=GCR_RECEIPT_ID) join gft_emp_master em on ( em.gem_emp_id=ifnull(GRD_EMP_ID, god_emp_id)) inner join gft_emp_master emp_other on( emp_other.gem_emp_id=em.gem_emp_id) where god_order_status='A' and GOD_ORDER_DATE between '$start_date' and '$end_date' group by gem_emp_id,GEM_STATUS) oh on (oh.gem_emp_id=em.gem_emp_id) left join ( select order_emp.gem_emp_id odr_emp,count(distinct god_lead_code) new_count1,sum(round(gop_sell_amt)) e_amt from gft_order_hdr join gft_order_product_dtl on (god_order_no=gop_order_no) join gft_lead_hdr lh on (glh_lead_code=god_lead_code and glh_order_close_date between '$start_date' and '$end_date' and glh_lead_type!=8) join gft_product_master on (gpm_product_code=gop_product_code and gpm_product_skew=gop_product_skew) left join gft_collection_receipt_dtl on (GCR_ORDER_NO=GOD_ORDER_NO and GOD_EMP_ID=9999) left join gft_receipt_dtl on (gcr_receipt_id=grd_receipt_id) join gft_emp_master order_emp on (if(god_emp_id=9999,grd_emp_id,god_emp_id)=order_emp.gem_emp_id and order_emp.gem_emp_id<7000) where god_order_date between '$start_date' and '$end_date' and god_order_status='A' and gpm_free_edition='N' group by order_emp.gem_emp_id ) emp_new on (em.gem_emp_id=emp_new.odr_emp) left join ( select order_emp.gem_emp_id odr_emp,sum(round(gop_sell_amt)) p_amt,count(distinct lh.glh_lead_code) new_count2 from gft_order_hdr left join gft_collection_receipt_dtl on (GCR_ORDER_NO=GOD_ORDER_NO and GOD_EMP_ID=9999) left join gft_receipt_dtl on (gcr_receipt_id=grd_receipt_id) join gft_order_product_dtl on (god_order_no=gop_order_no) join gft_cp_order_dtl cpo on (god_order_no=gco_order_no and gop_product_code=gco_product_code and gop_product_skew = gco_skew and god_emp_id>=7000 ) join gft_lead_hdr lh on (gco_cust_code=glh_lead_code and glh_order_close_date between '$start_date' and '$end_date' and glh_lead_type!='8') join gft_product_master on (gpm_product_code=gop_product_code and gpm_product_skew=gop_product_skew) join gft_emp_master order_emp on (if(god_emp_id=9999,grd_emp_id,god_emp_id)=order_emp.gem_emp_id and order_emp.gem_emp_id>7000 and order_emp.gem_emp_id!=9999) where god_order_date between '$start_date' and '$end_date' and god_order_status='A' and gpm_free_edition='N' group by order_emp.gem_emp_id ) partner_new on (em.gem_emp_id=partner_new.odr_emp) left join (select gem_emp_id,GEM_STATUS,count(*) inst_cnt,GID_YR_MONTH from gft_install_dtl_new inner join gft_emp_master em1 on (em1.gem_emp_id=gid_salesexe_id) where gid_status='A' and GID_INSTALL_DATE between '$start_date' and '$end_date' group by gem_emp_id )ins on (ins.gem_emp_id=em.gem_emp_id) left join ( select gem_emp_id,GEM_STATUS,round(sum(if(glh_country='India',(grd_receipt_amt*100/118),grd_receipt_amt))) coll_amt,sum(if(GLH_LEAD_TYPE=1,round(grd_receipt_amt),0)) end_usr_coll,sum(if(GLH_LEAD_TYPE in (3,13),round(grd_receipt_amt),0)) hq_usr_coll,GRD_CC_YR_MONTH, sum(if(GLH_LEAD_TYPE=2,round(grd_receipt_amt),0)) partner_coll from gft_receipt_dtl inner join gft_emp_master em1 on (em1.gem_emp_id=grd_emp_id) inner join gft_lead_hdr lh on (GLH_LEAD_CODE=grd_lead_code) where GRD_CHECKED_WITH_LEDGER='Y' and GRD_REFUND_AMT=0 and GRD_CHEQUE_CLEARED_DATE between '$start_date' and '$end_date' group by gem_emp_id )rcd on (rcd.gem_emp_id=em.gem_emp_id ) left join(select GLH_LFD_EMP_ID, sum(if(GLH_LEAD_TYPE=1,1,0)) ind_prospect,sum(if(GLH_LEAD_TYPE=3,1,0)) hq_prospect, sum(1) tot_prospects from gft_lead_hdr lh WHERE GLH_LEAD_TYPE IN(1,3) and GLH_PROSPECT_ON between '$start_date' and '$end_date' group by GLH_LFD_EMP_ID)inpros on (inpros.GLH_LFD_EMP_ID=em.gem_emp_id) where 1 and (GEM_GROUP_ID in (54,27) or GRG_GROUP_ID IN (66,104,13,70,11)) group by GEM_EMP_ID having (coll_amt!=0 or (GRG_GROUP_ID=70 and stat='A')) ORDER BY cgi_incharge_emp_id ,GEM_EMP_id
QUERY;
        $result = execute_my_query($query);
        while($row_patch=mysqli_fetch_assoc($result)){
            $empoyeeId = $row_patch['GEM_EMP_ID'];
            $inchargeId = $row_patch['cgi_incharge_emp_id'];
            if($empoyeeId<7000){
                $emp_ctc = isset($ctc_info_arr[$empoyeeId][0])?$ctc_info_arr[$empoyeeId][0]:0;
                $emp_mrp = isset($mrp_info_arr[$empoyeeId][0])?$mrp_info_arr[$empoyeeId][0]:0;
                $employee_details[$empoyeeId] = array('employee_name'=>$row_patch['GEM_EMP_NAME'],
                    'earning_amt'=>$row_patch['coll_amt'],
                    'expense'=>0,
                    'reference_commission'=>0,
                    'employee_salary'=>$emp_ctc,
                    'emp_mrp'=>$emp_mrp,
                    'partner_collection'=>0,
                    'role_id'=>$row_patch['GRG_ROLE_ID'] );
            }else if($inchargeId>0){
            $emp_dtl = get_emp_master($inchargeId);
                if(isset($employee_details[$inchargeId])){
                    $employee_details[$inchargeId]['partner_collection']=$employee_details[$inchargeId]['partner_collection']+$row_patch['coll_amt'];
                }else{
                    $emp_ctc = isset($ctc_info_arr[$inchargeId][0])?$ctc_info_arr[$inchargeId][0]:0;
                    $emp_mrp = isset($mrp_info_arr[$inchargeId][0])?$mrp_info_arr[$inchargeId][0]:0;
                    $employee_details[$inchargeId] = array('employee_name'=>$emp_dtl[0][1],
                        'earning_amt'=>0,
                        'expense'=>0,
                        'reference_commission'=>0,
                        'partner_collection'=>$row_patch['coll_amt'],
                        'employee_salary'=>$emp_ctc,
                        'emp_mrp'=>$emp_mrp,
                        'role_id'=>$emp_dtl[0][2]);
                }
            }
        }
        foreach ($employee_details as $employee_id=>$employee_dtl) {
            $expense_query = "select sum(expenses) expenses from (select SUM(GBH_BOOKING_EXPENSE) expenses, GBH_INCENTIVE_BUCKET_EMP emp_id  from gft_booking_hdr ". 
                            " where GBH_STATUS=2 AND GBH_PAYABLE_BY=1 AND date(GBH_FROM_DT) BETWEEN '$start_date' AND '$end_date' ".
                            " AND GBH_INCENTIVE_BUCKET_EMP='$employee_id' GROUP BY GBH_INCENTIVE_BUCKET_EMP UNION ".
                             " select (geh_total_travelexp_claimed+geh_official_mobile_bill) as expenses, 
                            geh_emp_id emp_id from gft_exec_expense_hdr where geh_month='$month' and geh_year='$year' AND geh_emp_id='$employee_id')tbl group by emp_id";
            $expense_amt = get_single_value_from_single_query("expenses", $expense_query);
            $kpi_query  =  "select GAC_KPI KPI  from gft_agile_cprr where GAC_EMP_ID='$employee_id' AND  GAC_YEAR='$year' AND GAC_MONTH='$month' AND GAC_APPROVAL_STATUS=2 AND GAC_METRIC_TYPE=1 GROUP BY GAC_EMP_ID";
            $kpi_amt = get_single_value_from_single_query("KPI", $kpi_query);
            $arp_amt = get_single_value_from_single_query("KPI", "select GAC_KPI KPI  from gft_agile_cprr where GAC_EMP_ID='$employee_id' AND  GAC_YEAR='$year' AND GAC_MONTH='$month' AND GAC_APPROVAL_STATUS=2 AND GAC_METRIC_TYPE=2 GROUP BY GAC_EMP_ID");
            $employee_details[$employee_id]['expense']= "$expense_amt";
            $employee_details[$employee_id]['kpi']= "$kpi_amt";
            $employee_details[$employee_id]['arp']= "$arp_amt";
        }
    }
    foreach ($employee_details as $employee_id=>$employee_dtl) {
        $employee_salary = isset($employee_dtl['employee_salary'])?(int)$employee_dtl['employee_salary']:0;
        $employee_emp_mrp = isset($employee_dtl['emp_mrp'])?(int)$employee_dtl['emp_mrp']:0;
        $role_id       =   isset($employee_dtl['role_id'])?(int)$employee_dtl['role_id']:0;
        $tot_earned    =   isset($employee_dtl['earning_amt'])?(float)$employee_dtl['earning_amt']:0;
        $tot_partner_collection   =   isset($employee_dtl['partner_collection'])?(float)$employee_dtl['partner_collection']:0;
        $kpi       =   isset($employee_dtl['kpi'])?$employee_dtl['kpi']:'';
        $arp       =   isset($employee_dtl['arp'])?$employee_dtl['arp']:'';
        $tot_expense   =   isset($employee_dtl['expense'])?(float)$employee_dtl['expense']:0;
        $tot_reference_commission   =   isset($employee_dtl['reference_commission'])?(float)$employee_dtl['reference_commission']:0;
        $additional_collection = 0;
        $additional_referral_commission =0 ;
        $additional_incentive =0 ;
        $existing_record_id = 0;
        $check_emp_dtl     = execute_my_query("SELECT GEC_ID, GEC_ADDITIONAL_COLLECTION, GEC_ADDITIONAL_REF_COMMISSION,GEC_ADDITIONAL_INCENTIVE FROM gft_emp_cost_of_sales_dtl  WHERE GEC_EMP_ID='$employee_id' AND GEC_YEAR='$year' AND GEC_MONTH='$month' ");
        if((mysqli_num_rows($check_emp_dtl)>0) && ($row=mysqli_fetch_assoc($check_emp_dtl))){
            $existing_record_id =(int)$row['GEC_ID'];
            $additional_collection = $row['GEC_ADDITIONAL_COLLECTION'];
            $additional_referral_commission =$row['GEC_ADDITIONAL_REF_COMMISSION'] ;
            $additional_incentive = (int)$row['GEC_ADDITIONAL_INCENTIVE'] ;
        }        
        $bucket_val = ($tot_earned+$tot_partner_collection+$additional_collection) - ($tot_expense+$tot_reference_commission+$additional_referral_commission);
        //For PC the incentive will be based on CTC after Sep-2018
        if(($role_id=='59' || $role_id=='30') || ($role_id=='2' && (strtotime($start_date)<strtotime(date("2018-10-01"))))){//pc, pcs and cst
            $slab_dtl_arr 	= get_revenue_slab_dtl($employee_id,$bucket_val);
        }else{
            $slab_dtl_arr 	= get_incentive_slab_info_from_ctc($employee_id,$bucket_val,$ctc_info_arr,$role_id);
        }
        $slab_perc 		= isset($slab_dtl_arr['percent'])?(float)$slab_dtl_arr['percent']:0.0;
        $incentive_amt	= round($bucket_val*$slab_perc/100,2);
        if($incentive_amt < 0 || $additional_incentive > 0) $incentive_amt = 0;
        $cost_per_rupee = '0.00';        
        $emp_mrp_percentage = '0.00';
        if($employee_emp_mrp>0){
            $emp_mrp_percentage = ($incentive_amt/$employee_emp_mrp)*100;
        }
        $check_emp_dtl     = execute_my_query("SELECT GEC_ID FROM gft_emp_cost_of_sales_dtl  WHERE GEC_EMP_ID='$employee_id' AND GEC_YEAR='$year' AND GEC_MONTH='$month' ");
        $emp_group = (int)get_single_value_from_single_table("WEB_GROUP", "gft_emp_master", "GEM_EMP_ID", $employee_id);
        $insert_arr = array();
        $insert_arr['GEC_EMP_ID'] = "$employee_id";
        $insert_arr['GEC_YEAR'] = "$year";
        $insert_arr['GEC_MONTH'] = "$month";
        $insert_arr['GEC_COLLECTION'] = "$tot_earned";
        $insert_arr['GEC_PARTNER_COLLECTION'] = "$tot_partner_collection";
        $insert_arr['GEC_REF_COMMISSION'] = "$tot_reference_commission";
        $insert_arr['GEC_INCENTIVE'] = "$incentive_amt";
        $insert_arr['GEC_EXPENSE'] = "$tot_expense";
        $insert_arr['GEC_CTC'] = "$employee_salary";
        $insert_arr['GEC_MRP_AMT'] = "$employee_emp_mrp";
        $insert_arr['GEC_COST_PER_RUPEE'] = "$cost_per_rupee";
        $insert_arr['GEC_CURRENT_REGION'] = "$emp_group";
        $insert_arr['GEC_KPI'] = "$kpi";
        $insert_arr['GEC_ARP'] = "$arp";
        $insert_arr['GEC_MRP']="$emp_mrp_percentage";
        $insert_arr['GEC_UPDATE_ON'] = date("Y-m-d H:i:s");
        if($existing_record_id>0){
            $table_key_arr['GEC_ID'] = $existing_record_id;
            array_update_tables_common($insert_arr, "gft_emp_cost_of_sales_dtl", $table_key_arr, null, '9999');
        }else{
            array_insert_query("gft_emp_cost_of_sales_dtl", $insert_arr);
        }
    }
}

/**
 * @param string $orderNumber
 * 
 * @return void
 */
function update_license_and_service_cost_for_order($orderNumber){
    $col_cond  = "GOP_SELL_RATE*GOP_QTY*if(GOP_COUPON_HOUR>0,GOP_COUPON_HOUR,1)"; 
    $q1 = " select sum(if(GPM_CP_PRODUCT=1, $col_cond, 0)) license, ".
          " sum(if(GPM_CP_PRODUCT=2, $col_cond, 0)) service_with_del, ".
          " sum(if(GPM_CP_PRODUCT=3, $col_cond, 0)) service_without_del ".
          " from gft_order_product_dtl ".
          " join gft_order_hdr on (GOD_ORDER_NO=GOP_ORDER_NO) ".
          " join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ".
          " join gft_lead_hdr_ext on (GLE_LEAD_CODE=GLH_LEAD_CODE) ".
          " join gft_product_master on (GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
          " where GOP_ORDER_NO='$orderNumber' group by GOP_ORDER_NO ";
    $r1 = execute_my_query($q1);
    if($d1 = mysqli_fetch_assoc($r1)){
        $lic_cost       = round((float)$d1['license'],2);
        if($lic_cost > 0){
            $gop_dtl = get_gop_dtl($orderNumber);
            $alr_rate = isset($gop_dtl['alr_rate'])?$gop_dtl['alr_rate']:0;
            $lic_cost -= $alr_rate;
        }
        $ser_with_del   = round((float)$d1['service_with_del'],2);
        $ser_without_del= round((float)$d1['service_without_del'],2);
        $up1 = "update gft_order_hdr set GOD_LICENSE_COST='$lic_cost',GOD_SERVICE_WITH_DELIVERY_COST='$ser_with_del',GOD_SERVICE_WITHOUT_DELIVERY_COST='$ser_without_del' where GOD_ORDER_NO='$orderNumber'";
        execute_my_query($up1);
    }
}

/**
 * @param string $quotation_no
 * 
 * @return int
 */
function get_license_cost_in_quotation($quotation_no){
    $lic_cost = 0;
    if(strtotime(date('Y-m-d')) >= strtotime(get_samee_const("New_Incentive_Order_Date"))){
        $que1 = " select sum(if(GFT_SKEW_PROPERTY not in (7,8,12,23), GQP_SELL_RATE*GQP_QTY*if(GQP_COUPON_HOUR>0,GQP_COUPON_HOUR,1), 0)) license ".
            " from gft_quotation_product_dtl ".
            " join gft_product_master on (GQP_PRODUCT_CODE=GPM_PRODUCT_CODE and GQP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
            " where GQP_ORDER_NO='$quotation_no' group by GQP_ORDER_NO ";
        $res1 = execute_my_query($que1);
        if($row1 = mysqli_fetch_array($res1)){
            $lic_cost = (int)$row1['license'];
        }
    }else{
        $lic_cost = 1; //to support backward compatability
    }
    return $lic_cost;
}

/**
 * @param string $customerId
 * 
 * @return int[int]
 */
function get_lead_creation_and_prospecting_owner($customerId){
    $created_by_emp = $prospect_by_emp = 0;
    $sql1 = " select cr.GEM_EMP_ID created_by,pr.GEM_EMP_ID prospect_by from gft_lead_hdr ".
        " left join gft_emp_master cr on (cr.GEM_EMP_ID=GLH_CREATED_BY_EMPID and cr.GEM_STATUS='A' and cr.GEM_OFFICE_EMPID > 0) ".
        " left join gft_emp_master pr on (pr.GEM_EMP_ID=GLH_PROSPECT_BY and pr.GEM_STATUS='A' and pr.GEM_OFFICE_EMPID > 0) ".
        " where GLH_LEAD_CODE='$customerId' ";
    $res1 = execute_my_query($sql1);
    if($row1 = mysqli_fetch_array($res1)){
        $created_by_emp 	= (int)$row1['created_by'];
        $prospect_by_emp 	= (int)$row1['prospect_by'];
        $creat_incen_eligible = (is_authorized_group_list($created_by_emp, array('23','54')) || is_authorized_group($created_by_emp, null,'31'));
        $prosp_incen_eligible = (is_authorized_group_list($prospect_by_emp, array('23','54')) || is_authorized_group($prospect_by_emp, null,'31'));
        if( ($created_by_emp==9999) || !$creat_incen_eligible ){
            $created_by_emp = 0;
        }
        if( ($prospect_by_emp==9999) || !$prosp_incen_eligible ){
            $prospect_by_emp = 0;
        }
    }
    return array($created_by_emp,$prospect_by_emp);
}

/**
 * @return float
 */
function get_pcs_entry_skew_sell_rate($order_no){
    $pcs_cond = pcs_entry_condition();
    $que1 = " select sum(if(1 $pcs_cond,GOP_SELL_RATE*GOP_QTY*if(GOP_COUPON_HOUR>0,GOP_COUPON_HOUR,1),0)) pcs_entry_cost ".
            " from gft_order_hdr join gft_lead_hdr on (GLH_LEAD_CODE=GOD_LEAD_CODE) ".
            " join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
            " join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
            " where GOP_ORDER_NO='$order_no' ";
    $res1 = execute_my_query($que1);
    $pcs_entry_cost = 0;
    if($row1 = mysqli_fetch_array($res1)){
        $pcs_entry_cost = (float)$row1['pcs_entry_cost'];
    }
    return $pcs_entry_cost;
}

?>
