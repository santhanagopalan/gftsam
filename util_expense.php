<?php
require_once(__DIR__ ."/dbcon.php");

/**
 * @param int $month
 * @param int $year
 * @param int $emp_id
 * 
 * @return void
 */
function update_claimed_amt($month,$year,$emp_id){
	$updated_time=date('Y-m-d H:i:s');	
	$sum_query="SELECT h.GBD_EMP_ID, month(GBD_ENTRY_DATE) month_b,year(GBD_ENTRY_DATE) as year_b," .
		" sum(GBD_AMOUNT) as local_exp," .
		" sum(GBD_TRAVEL_FARE_OUT) travel_fare," .
		" sum( h.GBD_TRAVEL_BOARDING_OUT) as boarding," .
		" sum(h.GBD_TRAVEL_LODGING_OUT) as lodging, " .
		" sum(h.GBD_MOBILE) as mobile,sum(h.GBD_CYBER) as cyber," .
		" sum(h.GBD_MISC_AMT) as misc_amt,sum(ifnull(GBD_AMOUNT,0) + ifnull(GBD_TRAVEL_FARE_OUT,0)+ ifnull(GBD_TRAVEL_BOARDING_OUT,0)+ ifnull(GBD_TRAVEL_LODGING_OUT,0)+ifnull(GBD_MOBILE,0)+ ifnull(GBD_CYBER,0)+ifnull(GBD_MISC_AMT,0)) as tot, now()" .
		" FROM gft_bussexp_hdr h " .
		" where h.gbd_emp_id='$emp_id' " . 
		" and month(gbd_entry_date)='$month' and year(gbd_entry_date)='$year' " .
		" group by h.gbd_emp_id,month(gbd_entry_date),year(gbd_entry_date) ";
	
	$query="select * from gft_exec_expense_hdr g WHERE " .
			"g.geh_emp_id='$emp_id' AND g.geh_month='$month' AND g.geh_year='$year'";
	$result=execute_my_query($query);
	if(mysqli_num_rows($result)!=0){
		$query="update  gft_exec_expense_hdr ,($sum_query) t2
	        set geh_local_exp_claimed=local_exp,geh_out_travel_fare_claimed=travel_fare," .
			" geh_boarding_claimed=boarding, geh_lodging_claimed=lodging, geh_mobile_claimed=mobile," .
			" geh_cyber_claimed=cyber,geh_misc_claimed=misc_amt,geh_total_travelexp_claimed=tot ," .
			"  geh_claimed_amt_update_date='$updated_time' ". 
			"where geh_emp_id=t2.gbd_emp_id and geh_month=t2.month_b and geh_year=t2.year_b ";
		execute_my_query($query);	
	}else{
		$query_ins="insert into  gft_exec_expense_hdr (geh_emp_id, geh_month,geh_year, geh_local_exp_claimed, " .
	 		"geh_out_travel_fare_claimed, geh_boarding_claimed, geh_lodging_claimed," .
	 		"geh_mobile_claimed,geh_cyber_claimed, geh_misc_claimed, geh_total_travelexp_claimed, geh_claimed_amt_update_date)" .
	 		" ($sum_query)";
		execute_my_query($query_ins);		
	}
}
/**
 * @param string $uid
 * @param int $exp_emp_id
 * @param int $exp_month
 * @param int $exp_year
 * @param mixed[] $old_row
 * @param mixed[] $expnese_amt
 * @param string $expense_cmd
 * @param int $current_status
 *
 * @return void
 */
function update_error_log($uid,$exp_emp_id,$exp_month,$exp_year,$old_row,$expnese_amt,$expense_cmd,$current_status){
    $old_serv_stat	=	mysqli_real_escape_string_wrapper(json_encode($old_row));
    $new_serv_stat	=	(json_encode($expnese_amt));
    $table_name='gft_approval_log';
    $insert_arr=/*. (mixed[string]) .*/ array();
    $insert_arr['GAL_APPROVAL_TYPE']=$current_status;
    $insert_arr['GAL_DATE']=date("Y-m-d H:i:s");
    $insert_arr['GAL_APPROVAL_BY']=$uid;
    $insert_arr['GAL_REMARKS']=$expense_cmd;
    $insert_arr['GAL_OLD_VALUE']=$old_serv_stat;
    $insert_arr['GAL_NEW_VALUE']=$new_serv_stat;
    $insert_arr['GAL_EMP_ID']=$exp_emp_id;
    $insert_arr['GAL_EXPENSE_MONTH']=$exp_month;
    $insert_arr['GAL_EXPENSE_YEAR']=$exp_year;
    array_update_tables_common($insert_arr,$table_name,null,null,SALES_DUMMY_ID,null,null,$insert_arr);
}
/**
 * @param string $from_dt
 * @param string $to_dt
 * @param string $month
 * @param string $year
 *
 * @return void
 */
function expense_auto_claim($from_dt,$to_dt,$month,$year){
    
    $sql_sub_query = " select GAE_EMP_ID,SUM(GAE_REQUESTED_AMT) as adv_exp ".
        " FROM gft_advance_expenses ".
        " where GAE_YEAR='$year' and  GAE_MONTH='$month' and gae_status not in (8,9,11,16) ".
        " group by GAE_EMP_ID ";
    
    $sql	=	" select  GED_EMP_ID, GEM_EMP_NAME,SUM(if(GED_TYPE_EXPENSE=1,GED_TOTAL_AMOUNT,0)) as local_exp, ".
        " SUM(if(GED_TYPE_EXPENSE!=1,GED_TOTAL_AMOUNT,0)) as travel_exp ,SUM(GED_LODGING_AMT) as lodging_exp,".
        " SUM(GED_FOOD_AMT) as food_exp,SUM(GED_BOADING_AMT) as boarding_exp,SUM(GED_OTHERS_AMT)as misc_exp,".
        " ae.adv_exp, ".
        " GEM_BYOD_POLICY,GEM_OFFICE_DATA_CARD, GEM_FOOD_EXPENSE	from gft_expense_hdr ed".
        " inner join gft_emp_master em on(em.gem_emp_id=ed.GED_EMP_ID)".
        " left join ($sql_sub_query) ae ON (ed.GED_EMP_ID = ae.GAE_EMP_ID) ".
        " where GED_TO_DATE>='$from_dt' and GED_TO_DATE<='$to_dt' ".
        " group by GED_EMP_ID";
    $res	=	execute_my_query($sql);
    $byod_exp_amt	= /*. (int) .*/get_samee_const('BYOD_EXPENSE_AMOUNT');
    $data_card_amt	= /*. (int) .*/get_samee_const('DATA_CARD_EXPENSE_AMOUNT');
    $food_exp_per_month	= /*. (int) .*/get_samee_const('FOOD_EXPENSE_AMOUNT');
    $update_emp_ids = array();
    while($row=mysqli_fetch_array($res)){
        $emp_id			=	$row['GED_EMP_ID'];
        $local_exp		=	$row['local_exp'];
        $travel_exp		=	$row['travel_exp'];
        $lodging_exp	=	$row['lodging_exp'];
        $boarding_exp	=	$row['boarding_exp'];
        $food_exp		=	$row['food_exp'];
        $adv_exp        =   $row['adv_exp'];
        $misc_exp		=	$row['misc_exp'];
        $byod_exp		=	0;
        $data_card		=	0;
        $fixed_food_card=	0;
        $update_emp_ids[] = $emp_id;
        if($row['GEM_BYOD_POLICY']=='Yes'){
            $byod_exp		=	$byod_exp_amt;
        }
        if($row['GEM_OFFICE_DATA_CARD']=='Yes'){
            $data_card		=	$data_card_amt;
        }
        if($row['GEM_FOOD_EXPENSE']=='Yes'){
            $food_exp =	($food_exp+$food_exp_per_month);
        }
        $geh_total_travelexp_claimed=($local_exp+$travel_exp+$lodging_exp+$boarding_exp+$misc_exp+$byod_exp+$data_card+$food_exp);
        $sql_check		=	execute_my_query("select geh_emp_id from gft_exec_expense_hdr where geh_year='$year' and geh_month='$month' and geh_emp_id='$emp_id'");
        $expnese_amt	=	array();
        $expnese_amt['exp1']	=	(int)$local_exp;
        $expnese_amt['exp2']	=	(int)$travel_exp;
        $expnese_amt['exp3']	=	(int)$lodging_exp;
        $expnese_amt['exp4']	=	(int)$boarding_exp;
        $expnese_amt['exp5']	=	(int)$byod_exp;
        $expnese_amt['exp6']	=	(int)$data_card;
        $expnese_amt['exp7']	=	(int)$misc_exp;
        $expnese_amt['exp8']	=	(int)$geh_total_travelexp_claimed;
        $expnese_amt['exp10']	=	(int)$food_exp;
        $expnese_amt['advance']	=	(int)$adv_exp;
        $expnese_amt['advance_rks']	="";
        if(mysqli_num_rows($sql_check)==1){
            execute_my_query(" update gft_exec_expense_hdr set geh_local_exp_claimed='$local_exp', geh_out_travel_fare_claimed='$travel_exp', ".
                "  geh_boarding_claimed='$boarding_exp', geh_lodging_claimed='$lodging_exp',geh_misc_claimed='$misc_exp',".
                " geh_total_travelexp_claimed='$geh_total_travelexp_claimed',geh_mobile_claimed='$byod_exp',".
                " geh_cyber_claimed='$data_card', geh_food_claimed='$food_exp', geh_advance_amount='$adv_exp' where ".
                "  geh_year='$year' and geh_month='$month' and geh_emp_id='$emp_id' ");
            
            
            update_error_log("9999",(int)$emp_id,(int)$month,(int)$year,null,$expnese_amt,"Submitted for claim",1);
        }else{
            execute_my_query("insert into gft_exec_expense_hdr(geh_emp_id,geh_month,geh_year,geh_local_exp_claimed,geh_out_travel_fare_claimed,".
                " geh_boarding_claimed,geh_lodging_claimed,geh_misc_claimed,geh_total_travelexp_claimed,geh_mobile_claimed,geh_cyber_claimed,geh_food_claimed,geh_advance_amount ) ".
                " values('$emp_id','$month','$year','$local_exp','$travel_exp',".
                " '$boarding_exp','$lodging_exp','$misc_exp','$geh_total_travelexp_claimed','$byod_exp','$data_card','$food_exp', '$adv_exp');");
            update_error_log("9999",(int)$emp_id,(int)$month,(int)$year,null,$expnese_amt,"Submitted for claim",1);
        }
        execute_my_query("update gft_expense_hdr SET GED_CLAIM_STATUS=2 where GED_EMP_ID='$emp_id' AND GED_TO_DATE>='$from_dt' and GED_TO_DATE<='$to_dt'");
    }
    
    //Update BYOD Amount alone
    $sql_byod	=	execute_my_query("select GEM_EMP_ID,GEM_BYOD_POLICY,GEM_OFFICE_DATA_CARD,GEM_FOOD_EXPENSE,SUM(GAE_REQUESTED_AMT) as adv_exp  from gft_emp_master ".
        " left join gft_advance_expenses on( GAE_EMP_ID = GEM_EMP_ID  and  GAE_YEAR='$year' and  GAE_MONTH='$month' and gae_status not in (8,9,11,16) )".
        " where (GEM_BYOD_POLICY='Yes' OR GEM_OFFICE_DATA_CARD='Yes' OR GEM_FOOD_EXPENSE='Yes') AND ".
        " (GEM_STATUS='A' OR (GEM_STATUS='I' AND GEM_DOR>='$from_dt')) group by GEM_EMP_ID");
    while($row_byod=mysqli_fetch_array($sql_byod)){
        $emp_id1			=	$row_byod['GEM_EMP_ID'];
        $byod_exp		=	0;
        $data_card		=	0;
        $food_exp		=	0;
        $adv_exp        =   0;
        if($row_byod['GEM_BYOD_POLICY']=='Yes'){
            $byod_exp		=	$byod_exp_amt;
        }
        if($row_byod['GEM_OFFICE_DATA_CARD']=='Yes'){
            $data_card		=	$data_card_amt;
        }
        if($row_byod['GEM_FOOD_EXPENSE']=='Yes'){
            $food_exp		=	$food_exp_per_month;
        }
        $adv_exp        =   $row_byod['adv_exp'];
        $expnese_amt	=	array();
        $expnese_amt['exp1']	=	0;
        $expnese_amt['exp2']	=	0;
        $expnese_amt['exp3']	=	0;
        $expnese_amt['exp4']	=	0;
        $expnese_amt['exp5']	=	(int)$byod_exp;
        $expnese_amt['exp6']	=	(int)$data_card;
        $expnese_amt['exp7']	=	0;
        $expnese_amt['exp8']	=	(int)($byod_exp+$data_card+$food_exp);
        $expnese_amt['exp10']	=	(int)$food_exp;
        $expnese_amt['advance']	=	$adv_exp;
        $expnese_amt['advance_rks']	="";
        $geh_total_travelexp_claimed=(int)($byod_exp+$data_card+$food_exp);
        $sql_check		=	execute_my_query("select geh_emp_id from gft_exec_expense_hdr where geh_year='$year' and geh_month='$month' and geh_emp_id='$emp_id1'");
        if(mysqli_num_rows($sql_check)==0){
            execute_my_query("insert into gft_exec_expense_hdr(geh_emp_id,geh_month,geh_year, geh_mobile_claimed,geh_cyber_claimed,geh_total_travelexp_claimed,geh_food_claimed,geh_advance_amount) ".
                " values('$emp_id1','$month','$year','$byod_exp','$data_card','$geh_total_travelexp_claimed','$food_exp','$adv_exp');");
            update_error_log("9999",(int)$emp_id1,(int)$month,(int)$year,null,$expnese_amt,"Submitted for claim",1);
        }else if(!in_array($emp_id1, $update_emp_ids)){
            execute_my_query(" update gft_exec_expense_hdr set  geh_total_travelexp_claimed='$geh_total_travelexp_claimed',geh_mobile_claimed='$byod_exp',".
                " geh_cyber_claimed='$data_card', geh_food_claimed='$food_exp', geh_advance_amount='$adv_exp' where ".
                "  geh_year='$year' and geh_month='$month' and geh_emp_id='$emp_id1' ");
        }
    }
    /* Update/Insert Advance amount  */
    $sql_advance =     " select GEM_EMP_ID,SUM(GAE_REQUESTED_AMT) as adv_exp ".
        " from gft_advance_expenses ".
        " join gft_emp_master on(GAE_EMP_ID = GEM_EMP_ID  ) ".
        " where (GEM_STATUS='A' OR (GEM_STATUS='I' AND GEM_DOR>='$from_dt')) and  GAE_YEAR='$year' and  GAE_MONTH='$month' and gae_status not in (8,9,11,16)  ".
        " group by GEM_EMP_ID ";
    
    $advance_exe = execute_my_query($sql_advance);
    while($adv_res = mysqli_fetch_assoc($advance_exe)){
        $adv_emp_id    =   (int)$adv_res['GEM_EMP_ID'];
        $adv_exp_amt   =   (int)$adv_res['adv_exp'];
        $expense_amt = array();
        $expense_amt['advance'] = $adv_exp_amt;
        $sql_check		=	execute_my_query("select geh_emp_id from gft_exec_expense_hdr where geh_year='$year' and geh_month='$month' and geh_emp_id='$adv_emp_id'");
        if(mysqli_num_rows($sql_check)>0){
            execute_my_query(   " update gft_exec_expense_hdr set geh_advance_amount='$adv_exp_amt'  ".
                " where geh_year='$year' and geh_month='$month' and geh_emp_id='$adv_emp_id' ");
        }else{
            execute_my_query(   " insert into gft_exec_expense_hdr(geh_emp_id,geh_month,geh_year,geh_local_exp_claimed,geh_out_travel_fare_claimed,".
                " geh_boarding_claimed,geh_lodging_claimed,geh_misc_claimed,geh_total_travelexp_claimed,geh_mobile_claimed,geh_cyber_claimed,geh_food_claimed,geh_advance_amount ) ".
                " values('$adv_emp_id','$month','$year','0','0',".
                " '0','0','0','0','0','0','0', '$adv_exp_amt');");
        }
        update_error_log("9999",(int)$adv_emp_id,(int)$month,(int)$year,null,$expense_amt,"Submitted for claim",1);
    }
    
}
?>
