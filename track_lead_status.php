<?php
require_once(__DIR__ ."/dbcon.php");

/**
 * @param string $approve_for_exec_id
 * @param int $month
 * @param int $year
 * @param boolean $from_freezed_data
 * 
 * @return int[string]
 */
function get_lead_sp($approve_for_exec_id,$month,$year,$from_freezed_data=false){
	global $sp_lead_hdr_check,$sp_lead_hdr_check_wfr;

	$hot_lead_date1=date('Y-m-d',mktime(0,0,0,$month,1,$year));
	$hot_lead_date2=date('Y-m-d',mktime(0,0,0,($month+1),0,$year));

	$warm_leads_n_date1=date('Y-m-d',mktime(0,0,0,($month+1),1,$year));
	$warm_leads_n_date2=date('Y-m-d',mktime(0,0,0,($month+2),0,$year));

	$warm_leads_nn_date1=date('Y-m-d',mktime(0,0,0,($month+2),1,$year));
	$warm_leads_nn_date2=date('Y-m-d',mktime(0,0,0,($month+3),0,$year));

		
	if($from_freezed_data==false){
		$query="select gtl_emp_id," .
				" sum(if(GLH_APPROX_TIMETOCLOSE between '$hot_lead_date1' and '$hot_lead_date2',1,0)) as 'hot_leads'," .
				" sum(if(GLH_APPROX_TIMETOCLOSE between '$warm_leads_n_date1' and '$warm_leads_n_date2',1,0)) as 'warm_leads_n'," .
				" sum(if(GLH_APPROX_TIMETOCLOSE between '$warm_leads_nn_date1' and '$warm_leads_nn_date2',1,0)) as 'warm_leads_nn', " .
				" sum(if(GLH_APPROX_TIMETOCLOSE between '$hot_lead_date1' and '$hot_lead_date2', glh_potential_amt,0)) as h_potential, ".
				" sum(if(GLH_APPROX_TIMETOCLOSE between '$hot_lead_date1' and '$hot_lead_date2', GLH_SERVICE_VALUE,0)) as a_collectible ".
				" from gft_track_lead_status ,gft_lead_hdr " .
				" where gtl_lead_code=glh_lead_code and  GLH_APPROX_TIMETOCLOSE !='0000-00-00' " .
				" AND GLH_APPROX_TIMETOCLOSE !=''  and GTL_TRACK_TYPE!=0  and gtl_emp_id='$approve_for_exec_id' " .
				" and gtl_month='$month' and gtl_year='$year' $sp_lead_hdr_check and GTL_TRACK_TYPE=1 " .
				" group by gtl_emp_id ";
		$result=execute_my_query($query,'',true,false);
		$query_top3="select glh_potential_amt ".
				" from  gft_track_lead_status ,gft_lead_hdr " .
				" where gtl_lead_code=glh_lead_code and  GLH_APPROX_TIMETOCLOSE !='0000-00-00' " .
				" AND GLH_APPROX_TIMETOCLOSE !=''  and GTL_TRACK_TYPE!=0  and gtl_emp_id='$approve_for_exec_id' " .
				" and gtl_month='$month' and gtl_year='$year' $sp_lead_hdr_check and GTL_TRACK_TYPE=1 ".
				" order by glh_potential_amt desc limit 3 ";

	}else{
	/* join lead hdr bcs lead status from lead hdr */
		$query="select gtl_wfreeezed_emp_id, " .
		" sum(if(GTL_WFREEZED_DOC between '$hot_lead_date1' and '$hot_lead_date2',1,0)) as 'hot_leads'," .
		" sum(if(GTL_WFREEZED_DOC between '$warm_leads_n_date1' and '$warm_leads_n_date2',1,0)) as 'warm_leads_n'," .
		" sum(if(GTL_WFREEZED_DOC between '$warm_leads_nn_date1' and '$warm_leads_nn_date2',1,0)) as 'warm_leads_nn', " .
		" sum(if(GTL_WFREEZED_DOC between '$hot_lead_date1' and '$hot_lead_date2', GTL_WFREEZED_POTENTIAL_AMT,0)) as h_potential, ".
				" sum(if(GTL_WFREEZED_DOC between '$hot_lead_date1' and '$hot_lead_date2', GTL_WFREEZED_COLLECTIBLE,0)) as a_collectible ".
						" from  gft_track_lead_status,gft_lead_hdr " .
						" where gtl_lead_code=glh_lead_code and GTL_WFREEZED_DOC !='0000-00-00'  and GTL_WFREEZED_TRACK_TYPE!=0 " .
		" AND GTL_WFREEZED_DOC !=''  and gtl_wfreeezed_emp_id='$approve_for_exec_id' " .
		" and gtl_month='$month' and gtl_year='$year' $sp_lead_hdr_check_wfr " .
						" and GTL_WFREEZED_TRACK_TYPE=1 group by gtl_wfreeezed_emp_id ";
		$result=execute_my_query($query,'',true,false);

		$query_top3="select GTL_WFREEZED_POTENTIAL_AMT ".
		" from  gft_track_lead_status,gft_lead_hdr  " .
     		" where gtl_lead_code=glh_lead_code and GTL_WFREEZED_DOC !='0000-00-00'  and GTL_WFREEZED_TRACK_TYPE!=0 " .
     		" AND GTL_WFREEZED_DOC !=''  and gtl_wfreeezed_emp_id='$approve_for_exec_id' " .
     		" and gtl_month='$month' and gtl_year='$year' $sp_lead_hdr_check_wfr " .
     		" and GTL_WFREEZED_TRACK_TYPE=1 order by GTL_WFREEZED_POTENTIAL_AMT desc limit 3 ";

	}
	$leads_count=/*. (int[string]) .*/ null;
	$leads_count['a_collectible']=0;
	$leads_count['hot_leads']=0;
	$leads_count['h_potential']=0;
	$leads_count['warm_leads_n']=0;
	$leads_count['warm_leads_nn']=0;
	if($qdata=mysqli_fetch_array($result)){
	$leads_count['a_collectible']=(int)$qdata['a_collectible'];
	$leads_count['hot_leads']=(int)$qdata['hot_leads'];
	$leads_count['h_potential']=(int)$qdata['h_potential'];
	$leads_count['warm_leads_n']=(int)$qdata['warm_leads_n'];
	$leads_count['warm_leads_nn']=(int)$qdata['warm_leads_nn'];

	}
	$result_top3=execute_my_query($query_top3,'',true,false);
	$top3_potential=0;
	while($qdata_top3=mysqli_fetch_array($result_top3)){
	$top3_potential+=(int)$qdata_top3[0];
	}
	$per_top3_potential=0;
	if($leads_count['h_potential']!=0){
	$per_top3_potential=(int)($top3_potential/$leads_count['h_potential'])*100;
	}
	$leads_count['per_top3_potential']=$per_top3_potential;
	return $leads_count;
}

/**
 * @param int $month
 * @param int $year
 * @param string $emp_id
 * 
 * @return void
 */
function get_scorecard_plan_overall_freezed($month,$year,$emp_id=null){
	global $sp_lead_hdr_check_wfr;

	$hot_lead_date1=date('Y-m-d',mktime(0,0,0,$month,1,$year));
	$hot_lead_date2=date('Y-m-d',mktime(0,0,0,($month+1),0,$year));

	$warm_leads_13_date1=date('Y-m-d',mktime(0,0,0,($month+1),1,$year));
	$warm_leads_13_date2=date('Y-m-d',mktime(0,0,0,($month+4),0,$year));

	$warm_leads_46_date1=date('Y-m-d',mktime(0,0,0,($month+4),1,$year));
	$warm_leads_46_date2=date('Y-m-d',mktime(0,0,0,($month+7),0,$year));

	$warm_leads_712_date1=date('Y-m-d',mktime(0,0,0,($month+7),1,$year));
	$warm_leads_712_date2=date('Y-m-d',mktime(0,0,0,($month+13),0,$year));

	$query="select GTL_WFREEEZED_EMP_ID,GLH_LEAD_TYPE, " .
			" sum(if(GTL_WFREEZED_DOC between '$hot_lead_date1' and '$hot_lead_date2',1,0)) as 'hot_leads'," .
			" sum(if(GTL_WFREEZED_DOC between '$warm_leads_13_date1' and '$warm_leads_13_date2',1,0)) as 'warm_leads_n'," .
			" sum(if(GTL_WFREEZED_DOC between '$warm_leads_46_date1' and '$warm_leads_46_date2',1,0)) as 'warm_leads_nn', " .
			" sum(if(GTL_WFREEZED_DOC between '$warm_leads_712_date1' and '$warm_leads_712_date2',1,0)) as 'warm_leads_nnn', " .
			" sum(if(GTL_WFREEZED_DOC between '$hot_lead_date1' and '$hot_lead_date2', GTL_WFREEZED_POTENTIAL_AMT,0)) as h_potential, ".
			" sum(if(GTL_WFREEZED_DOC between '$warm_leads_13_date1' and '$warm_leads_13_date2',GTL_WFREEZED_POTENTIAL_AMT,0)) as 'warm_leads_n_potential'," .
			" sum(if(GTL_WFREEZED_DOC between '$warm_leads_46_date1' and '$warm_leads_46_date2',GTL_WFREEZED_POTENTIAL_AMT,0)) as 'warm_leads_nn_potential', " .
			" sum(if(GTL_WFREEZED_DOC between '$warm_leads_712_date1' and '$warm_leads_712_date2',GTL_WFREEZED_POTENTIAL_AMT,0)) as 'warm_leads_nnn_potential' " .
			" from  gft_track_lead_status,gft_lead_hdr " .
			" where gtl_lead_code=glh_lead_code and GTL_WFREEZED_DOC !='0000-00-00'   " .
			" AND GTL_WFREEZED_DOC !='' ".
			" and gtl_month='$month' and gtl_year='$year' $sp_lead_hdr_check_wfr " .
			" and GTL_WFREEZED_TRACK_TYPE=1 ";
	if($emp_id!=null){
		$query.=" and GTL_WFREEEZED_EMP_ID=$emp_id ";
	}
	$query.=" group by GTL_WFREEEZED_EMP_ID,glh_lead_type ";

	$result=execute_my_query($query,'',true,false);
	if($result){
		while($qdata=mysqli_fetch_array($result)){
			$wemp_id=$qdata['GTL_WFREEEZED_EMP_ID'];
			$lead_type=(int)$qdata['GLH_LEAD_TYPE'];
			$hot_leads=$qdata['hot_leads'];
			$warm_leads13=$qdata['warm_leads_n'];
			$warm_leads46=$qdata['warm_leads_nn'];
			$warm_leads712=$qdata['warm_leads_nnn'];
			$h_potential=(int)$qdata['h_potential'];
			$warm_leads13_potential=$qdata['warm_leads_n_potential'];
			$warm_leads46_potential=$qdata['warm_leads_nn_potential'];
			$warm_leads712_potential=$qdata['warm_leads_nnn_potential'];
				
			if($lead_type==1){
				$update_target_query="replace into gft_plan_scorecard_metrics(GPS_METRIC_ID,GPS_EMP_ID, " .
						"GPS_MONTH, GPS_YEAR, GPS_VALUE ) values
						('49',$wemp_id,$month,$year,'$hot_leads'),('51',$wemp_id,$month,$year,'$h_potential'),
						('37',$wemp_id,$month,$year,'$warm_leads13'),('39',$wemp_id,$month,$year,'$warm_leads13_potential'),
						('41',$wemp_id,$month,$year,'$warm_leads46'),('43',$wemp_id,$month,$year,'$warm_leads46_potential'),
						('45',$wemp_id,$month,$year,'$warm_leads712'),('47',$wemp_id,$month,$year,'$warm_leads712_potential') ";

			}else if($lead_type==3){
				$update_target_query="replace into gft_plan_scorecard_metrics(GPS_METRIC_ID,GPS_EMP_ID, " .
						"GPS_MONTH, GPS_YEAR, GPS_VALUE ) values
						('50',$wemp_id,$month,$year,'$hot_leads'),('52',$wemp_id,$month,$year,'$h_potential'),
						('38',$wemp_id,$month,$year,'$warm_leads13'),('40',$wemp_id,$month,$year,'$warm_leads13_potential'),
						('42',$wemp_id,$month,$year,'$warm_leads46'),('44',$wemp_id,$month,$year,'$warm_leads46_potential'),
						('46',$wemp_id,$month,$year,'$warm_leads712'),('48',$wemp_id,$month,$year,'$warm_leads712_potential') ";
			}else{
				$update_target_query='';
			}//end of else
			execute_my_query($update_target_query);

		}//end of while
	}//end of if


	//combination of indiv_chain
	$query="select GTL_WFREEEZED_EMP_ID, " .
			" sum(if(GTL_WFREEZED_DOC between '$hot_lead_date1' and '$hot_lead_date2',1,0)) as 'hot_leads'," .
			" sum(if(GTL_WFREEZED_DOC between '$hot_lead_date1' and '$hot_lead_date2', GTL_WFREEZED_POTENTIAL_AMT,0)) as h_potential,
			SUM(if(gld_visit_nature=2,1,0)) 'demos' ".
	" from  gft_track_lead_status,gft_lead_hdr,gft_activity " .
			" where gtl_lead_code=glh_lead_code and GTL_WFREEZED_DOC !='0000-00-00'   " .
					" AND GTL_WFREEZED_DOC !='' ".
     		" and gtl_month='$month' and gtl_year='$year' $sp_lead_hdr_check_wfr " .
     		" and GTL_WFREEZED_TRACK_TYPE=1  and GTL_WFREEZED_LAST_ACTIVITY_ID=GLD_ACTIVITY_ID ";
	if($emp_id!=null){
	$query.=" and GTL_WFREEEZED_EMP_ID=$emp_id ";
	}
	$query.=" group by GTL_WFREEEZED_EMP_ID ";
	$result=execute_my_query($query,'',true,false);
	//$total_potential=array();
	if($result){
	while($qdata=mysqli_fetch_array($result)){
	$wemp_id=$qdata['GTL_WFREEEZED_EMP_ID'];
			$hot_leads=$qdata['hot_leads'];
			$h_potential=(int)$qdata['h_potential'];
			$demos=$qdata['demos'];

			$query_top3="select GTL_WFREEZED_POTENTIAL_AMT ".
			" from  gft_track_lead_status,gft_lead_hdr  " .
     		" where gtl_lead_code=glh_lead_code and  GTL_WFREEZED_DOC !='0000-00-00'  and GTL_WFREEZED_TRACK_TYPE!=0 " .
     		" AND GTL_WFREEZED_DOC !=''  and gtl_wfreeezed_emp_id='$emp_id' " .
     		" and gtl_month='$month' and gtl_year='$year' $sp_lead_hdr_check_wfr " .
     		" and GTL_WFREEZED_TRACK_TYPE=1 AND GTL_WFREEZED_DOC between '$hot_lead_date1' and '$hot_lead_date2'
     		order by GTL_WFREEZED_POTENTIAL_AMT desc limit 3 ";
     			
     		$result_top3=execute_my_query($query_top3,'',true,false);
     		$top3_potential=0;
     		while($qdata_top3=mysqli_fetch_array($result_top3)){
     		$top3_potential+=(int)$qdata_top3[0];
     		}
     		$per_top3_potential="";
     		if($h_potential!=0){
     		$per_top3_potential=($top3_potential/$h_potential)*100;
	}


	$update_target_query="replace into gft_plan_scorecard_metrics(GPS_METRIC_ID,GPS_EMP_ID, " .
	"GPS_MONTH, GPS_YEAR, GPS_VALUE ) values ('10',$wemp_id,$month,$year," .
			"'$hot_leads'),('9',$wemp_id,$month,$year,'$h_potential'),
					('11',$wemp_id,$month,$year,'$hot_leads'),('12',$wemp_id,$month,$year,'$h_potential'),
							('14',$wemp_id,$month,$year,'$hot_leads'),('16',$wemp_id,$month,$year,'$hot_leads'),
									('28',$wemp_id,$month,$year,'$h_potential'),('27',$wemp_id,$month,$year,'$per_top3_potential'),
											('34',$wemp_id,$month,$year,'$demos') ";
											execute_my_query($update_target_query);
	}//end of while

											/* add on product sale(19) and asa/upgradation(20)  --not able to todo
											* Outstanding Amount (29)--its actual outstanding as on date refer
	*/



	//Collection plan sum of collection plan from Grderwise Outstanding and Order value of hot leads
	$gmoc_whr=$gps_whr="";
	if($emp_id!=null){
		$gmoc_whr=" and GMOC_EMP_ID='$emp_id' ";
		$gps_whr=" and GPS_EMP_ID='$emp_id'";
	}

	$query_update_on_3rd=" replace into gft_plan_scorecard_metrics
	(GPS_METRIC_ID,GPS_EMP_ID,GPS_MONTH, GPS_YEAR, GPS_VALUE )
	(select '28',GMOC_EMP_ID,'$month','$year',(GMOC_COLLECTIBLE_AMT+GPS_VALUE) FROM
	(select GMOC_EMP_ID,SUM(GMOC_COLLECTIBLE_AMT) as GMOC_COLLECTIBLE_AMT,
	SUM(GMOC_BALANCE_AMT),count(*) 'collection_fup',GPS_VALUE
	from  gft_order_collection_dtl oc
	LEFT JOIN (SELECT GPS_EMP_ID,GPS_VALUE FROM gft_plan_scorecard_metrics WHERE GPS_METRIC_ID=28
	AND GPS_MONTH=$month and GPS_YEAR=$year $gps_whr )SM ON (GPS_EMP_ID=GMOC_EMP_ID)
	WHERE gmoc_current_status=true  and
	oc.GMOC_WFREEZED_MONTH='$month' AND oc.GMOC_WFREEZED_YEAR='$year' and
	oc.GMOC_COLLECTION_DATE BETWEEN '$hot_lead_date1' and '$hot_lead_date2' $gmoc_whr
	GROUP BY GMOC_EMP_ID) T) ";
	execute_my_query($query_update_on_3rd);

	//Collection Followup
	$query_update_on_3rd=" replace into gft_plan_scorecard_metrics
	(GPS_METRIC_ID,GPS_EMP_ID,GPS_MONTH, GPS_YEAR, GPS_VALUE )
	(select '31',GMOC_EMP_ID,'$month','$year',count(*)
	from  gft_order_collection_dtl oc WHERE oc.GMOC_WFREEZED_MONTH='$month' AND oc.GMOC_WFREEZED_YEAR='$year' and
	oc.GMOC_COLLECTION_DATE BETWEEN '$hot_lead_date1' and '$hot_lead_date2' $gmoc_whr
	GROUP BY GMOC_EMP_ID) ";
	execute_my_query($query_update_on_3rd);

	//Outstanding Planned
	$query_update_on_3rd=" replace into gft_plan_scorecard_metrics
	(GPS_METRIC_ID,GPS_EMP_ID,GPS_MONTH, GPS_YEAR, GPS_VALUE )
	(select '29',GMOC_EMP_ID,'$month','$year',sum(GMOC_COLLECTIBLE_AMT)
	from  gft_order_collection_dtl oc WHERE oc.GMOC_WFREEZED_MONTH='$month' AND oc.GMOC_WFREEZED_YEAR='$year' and
	oc.GMOC_COLLECTION_DATE BETWEEN '$hot_lead_date1' and '$hot_lead_date2' $gmoc_whr
	GROUP BY GMOC_EMP_ID) ";
	execute_my_query($query_update_on_3rd);


	}//end of if


}//end of fun

/**
 * @param string $financial_yr
 * @param int $month
 * @param int $year
 * @param string $emp_id
 * @param boolean $update_sp_approve_table
 * 
 * @return void
 */
function update_hot_leads($financial_yr,$month,$year,$emp_id,$update_sp_approve_table=false){
	/* Updated in gft_categorywise_summary using in ipr,pr  and planning status report */		
	$lead_count=get_lead_sp($emp_id,$month,$year,/*$from_freezed_data=*/true);
	$hot_leads=$lead_count['hot_leads'];
	$warm_leads_n=$lead_count['warm_leads_n'];
	$warm_leads_nn=$lead_count['warm_leads_nn'];
	//$potential=$lead_count['h_potential'];
	//$per_top3_potential=$lead_count['per_top3_potential'];
	$query_update_hotleads="replace into gft_categorywise_summary" .
				"(GCS_EMP_ID,GCS_SCATEGORY,GCS_MONTH,GCS_YEAR,GCS_VALUE,GCS_COMMENT,GCS_FINANCIAL_YEAR)" .
				"values ($emp_id,'24','$month','$year','$hot_leads','Hot leads','$financial_yr')";
	execute_my_query($query_update_hotleads,'',true,false);
    if($update_sp_approve_table==true){
    	$query_update="update gft_sales_planning_approve_incentive set GSP_HOT_LEADS='$hot_leads'," .
       	       		"GSP_WARM_LEADS_N='$warm_leads_n',GSP_WARM_LEADS_NN='$warm_leads_nn' " .
       	       		"where gsp_emp_id='$emp_id' and gsp_month='$month' and gsp_year='$year' ";
		execute_my_query($query_update,'',true,false);
		  		
	}
	$lead_count=get_lead_sp($emp_id,$month,$year,/*$from_freezed_data=*/false);
	$hot_leads=$lead_count['hot_leads'];
	$warm_leads_n=$lead_count['warm_leads_n'];
	$warm_leads_nn=$lead_count['warm_leads_nn'];
	//$potential=$lead_count['h_potential'];
	//$per_top3_potential=$lead_count['per_top3_potential'];
	$query_update_hotleads_current="replace into gft_categorywise_summary" .
     		"(GCS_EMP_ID,GCS_SCATEGORY,GCS_MONTH,GCS_YEAR,GCS_VALUE,GCS_COMMENT,GCS_FINANCIAL_YEAR)" .
     		"values ($emp_id,'15','$month','$year','$hot_leads','hot leads','$financial_yr')  " ;
	execute_my_query($query_update_hotleads_current,'',true,false);  			

	/*updating in scorecard plan detail*/
	get_scorecard_plan_overall_freezed($month,$year,$emp_id);
		
}

/**
 * @param boolean $month_wise
 * @param boolean $carried_forward
 * @param string $financial_yr
 * @param string $emp_id
 * 
 * @return string
 */
function return_query_for_lead_source($month_wise=true,$carried_forward=false,$financial_yr=null,$emp_id=null){
	global $sp_lead_hdr_check;
	$whr_emp='';
	$current_month=(int)date('m');
	$current_year=(int)date('Y');
	$date_last_financial_yr='';
	$whr_date_range='';
	$add_group_by='';
	if($emp_id!='' && $emp_id!='0'){
		$whr_emp=" and gtl_emp_id='$emp_id' and gtl_month='$current_month' and gtl_year='$current_year' ";
	}
	if($month_wise==true){
		$monthyr_for_ls="month(GLH_APPROX_TIMETOCLOSE) ls_month,year(GLH_APPROX_TIMETOCLOSE) ls_year";
		$add_group_by=",month(GLH_APPROX_TIMETOCLOSE),year(GLH_APPROX_TIMETOCLOSE) ";
	}else {
		$financial_yr_dates=get_start_end_date_of_quarter(/*$quarter=*/0,$financial_yr);
		$date_last_financial_yr=$financial_yr_dates[0];
		$monthyr_for_ls="'0' as ls_month,'$financial_yr' as ls_year ";
		$whr_date_range=" and GLH_APPROX_TIMETOCLOSE< '$date_last_financial_yr' ";
	}

	$query_update_hotleads_ls="select gtl_emp_id,source_code,ls_month,ls_year,ls_value from( (" .
			"select gtl_emp_id,GLH_LEAD_SOURCE_CODE_INTERNAL source_code,$monthyr_for_ls,".
			" count(gtl_lead_code) ls_value,'GLH_LEAD_SOURCE_CODE_INTERNAL'" .
			" from  gft_track_lead_status,gft_lead_hdr lh " .
			" where gtl_lead_code=glh_lead_code " .
			" $whr_emp $whr_date_range AND GLH_APPROX_TIMETOCLOSE!='0000-00-00' " .
			" AND GLH_APPROX_TIMETOCLOSE!='' and GTL_TRACK_TYPE!=0 ".
			" group by gtl_emp_id,GLH_LEAD_SOURCE_CODE_INTERNAL $add_group_by)" .
			" union (" .
			" select gtl_emp_id,GLH_LEAD_SOURCE_CODE_PARTNER source_code,$monthyr_for_ls,".
			" count(gtl_lead_code) ls_value,'GLH_LEAD_SOURCE_CODE_PARTNER' " .
			" from  gft_track_lead_status,gft_lead_hdr lh " .
			" where gtl_lead_code=glh_lead_code " .
			" $whr_emp $whr_date_range  AND GLH_APPROX_TIMETOCLOSE!='0000-00-00' " .
			" AND GLH_APPROX_TIMETOCLOSE!=''  and GTL_TRACK_TYPE!=0  ".
			" group by gtl_emp_id,GLH_LEAD_SOURCE_CODE_PARTNER $add_group_by)" .
			" union (" .
			" select gtl_emp_id,GLH_LEAD_SOURCECODE source_code,$monthyr_for_ls,".
			" count(gtl_lead_code) ls_value,'GLH_LEAD_SOURCECODE' " .
			" from  gft_track_lead_status,gft_lead_hdr lh " .
			" where gtl_lead_code=glh_lead_code  $sp_lead_hdr_check  " .
			" $whr_emp $whr_date_range AND GLH_APPROX_TIMETOCLOSE!='0000-00-00' " .
			" AND GLH_APPROX_TIMETOCLOSE!=''  and GTL_TRACK_TYPE!=0 ".
			" group by gtl_emp_id,GLH_LEAD_SOURCECODE $add_group_by) ) t1 where source_code!=0 ";
	//echo "<br>".$query_update_hotleads_ls;
	return $query_update_hotleads_ls;
}

/**
 * @return void
 */
function runOnceUpdateLeadHdrPlanNumber(){
	global $sp_lead_hdr_check;

	//This will make sure run once for every session
	static $count = 0;
	if ($count == 0){
		$update_plan_no=<<<END
			update gft_lead_hdr lh ,(select gtl_lead_code,count(gtl_lead_code) cnt  from gft_track_lead_status where GTL_WFREEZED_TRACK_TYPE=1 group by gtl_lead_code)t
			set glh_plan_no=cnt  where  glh_lead_code =gtl_lead_code $sp_lead_hdr_check
END;
		execute_my_query($update_plan_no);
	}
	$count++;
}

/**
 * @param string $emp_id
 * @param string $financial_yr
 * @param int $month
 * @param int $year
 * @param string $team
 * 
 * @return void
 */
function update_lead_status_cs($emp_id,$financial_yr=null,$month=0,$year=0,$team=null){
	global $sp_lead_hdr_check;
	//15 hot leads	0 month as carrried forward of previous financial yr
	//hot lead of financial yr
	//according to leadsource enter in gft_hot_lead_source_temptable
	$whr_emp='';$whr_emp_s='';$whr_emp_ls='';
	$current_month=(int)date('m');
	$current_year=(int)date('Y');
	$financial_yr=get_current_financial_yr();
	$financial_yr_dates=get_start_end_date_of_quarter(/*$quarter=*/0,$financial_yr);
	$date_last_financial_yr=$financial_yr_dates[0];
	//$end_this_financial_yr=$financial_yr_dates[1];

	runOnceUpdateLeadHdrPlanNumber();


	if($emp_id!='' && $emp_id!='0'){
		$whr_emp=" and gtl_emp_id='$emp_id' ";
		$whr_emp_s=" and GCS_EMP_ID='$emp_id' ";
		$whr_emp_ls=" and  GHL_EMP_ID='$emp_id' ";
	}

	$whr_emp.=" and gtl_month='$current_month' and gtl_year='$current_year' ";
	//$whr_emp_s=" and GCS_MONTH='$month' and GCS_YEAR='$year' ";

	$delete_query="delete from gft_categorywise_summary where  GCS_SCATEGORY in('15','17') $whr_emp_s ";
	execute_my_query($delete_query,'',true,false);
		
	$query_update_carryforward="replace into gft_categorywise_summary" .
			"(GCS_EMP_ID,GCS_SCATEGORY,GCS_MONTH,GCS_YEAR,GCS_VALUE,GCS_COMMENT,GCS_FINANCIAL_YEAR)(" .
			"select gtl_emp_id,15,0,'$financial_yr' ,count(*),'cf','$financial_yr' from  " .
			"gft_track_lead_status ,gft_lead_hdr lh " .
			"where  gtl_lead_code=glh_lead_code and GLH_APPROX_TIMETOCLOSE!='0000-00-00' " .
			"AND GLH_APPROX_TIMETOCLOSE!='' and GTL_TRACK_TYPE!=0  $sp_lead_hdr_check " .
			" and GLH_APPROX_TIMETOCLOSE < '$date_last_financial_yr' $whr_emp " .
			"group by gtl_emp_id) ";
	execute_my_query($query_update_carryforward,'',true,false);
	//  echo "<br>".$query_update_carryforward;
	$prev_month_last_date=date('Y-m-d',mktime(0,0,0,$current_month,0,$current_year));
	$query_update_carryforward_mn="replace into gft_categorywise_summary" .
			"(GCS_EMP_ID,GCS_SCATEGORY,GCS_MONTH,GCS_YEAR,GCS_VALUE,GCS_COMMENT,GCS_FINANCIAL_YEAR)(" .
			"select gtl_emp_id,'17','$current_month','$current_year',count(*),'cf','$financial_yr' from  " .
			"gft_track_lead_status ,gft_lead_hdr lh " .
			"where  gtl_lead_code=glh_lead_code and GLH_APPROX_TIMETOCLOSE!='0000-00-00' " .
			"AND GLH_APPROX_TIMETOCLOSE!=''  and GTL_TRACK_TYPE!=0  $sp_lead_hdr_check " .
			"and GLH_APPROX_TIMETOCLOSE <= '$prev_month_last_date' $whr_emp " .
			"group by gtl_emp_id) ";
	 
	execute_my_query($query_update_carryforward_mn,'',true,false);
	//$doc_check=	" and GLH_APPROX_TIMETOCLOSE >='$date_last_financial_yr'  and  " .
	//" GLH_APPROX_TIMETOCLOSE<='$end_this_financial_yr' ";
	 
	$rquery_update_hotleads="replace into gft_categorywise_summary" .
			"(GCS_EMP_ID,GCS_SCATEGORY,GCS_MONTH,GCS_YEAR,GCS_VALUE,GCS_COMMENT,GCS_FINANCIAL_YEAR) ";
	 $query_update_hotleads=" $rquery_update_hotleads (select gtl_emp_id,'15',month(GLH_APPROX_TIMETOCLOSE) mon," .
	 		" year(GLH_APPROX_TIMETOCLOSE) yr," .
								" count(*) as Hot_leads,'Hot Leads ','$financial_yr' " .
	 				" from  gft_track_lead_status,gft_lead_hdr lh " .
								" where gtl_lead_code=glh_lead_code $sp_lead_hdr_check  ".
	 				" $whr_emp AND GLH_APPROX_TIMETOCLOSE!='0000-00-00' " .
	 				" AND GLH_APPROX_TIMETOCLOSE!=''  and GTL_TRACK_TYPE!=0  " .
								" group by gtl_emp_id,month(GLH_APPROX_TIMETOCLOSE),year(GLH_APPROX_TIMETOCLOSE) ) ";
	 execute_my_query($query_update_hotleads,'',true,false);

	 // echo $query_update_hotleads;
	$nxt_month_yr=date('Y-m',mktime('0','0','0',($current_month+1),1,$current_year));
	$nxt_month_yr_arr=explode('-',$nxt_month_yr);
	$nxt_month=$nxt_month_yr_arr[1];
	$nxt_month_yr_val=$nxt_month_yr_arr[0];
	$query="$rquery_update_hotleads " .
	"(select GCS_EMP_ID,'16','$current_month','$current_year',GCS_VALUE,GCS_COMMENT," .
			"GCS_FINANCIAL_YEAR from gft_categorywise_summary where gcs_month='$nxt_month' " .
			" and gcs_year='$nxt_month_yr_val' and GCS_SCATEGORY='15' $whr_emp_s )";
			execute_my_query($query,'',true,false);
			// echo $query;
				
			$nxt_month_yr2=date('Y-m',mktime('0','0','0',($current_month+2),1,$current_year));
			$nxt_month_yr_arr2=explode('-',$nxt_month_yr2);
			$nxt_month2=$nxt_month_yr_arr2[1];
			$nxt_month_yr_val2=$nxt_month_yr_arr2[0];
			$query="$rquery_update_hotleads " .
			"(select GCS_EMP_ID,'27','$current_month','$current_year',GCS_VALUE,GCS_COMMENT," .
					"GCS_FINANCIAL_YEAR from gft_categorywise_summary where gcs_month='$nxt_month2' " .
					" and gcs_year='$nxt_month_yr_val2' and GCS_SCATEGORY='15' $whr_emp_s )";
					execute_my_query($query,'',true,false);

					// echo $query;
					// echo "<br>".$query_update_hotleads;
						
					execute_my_query("delete from gft_hot_lead_source_temptable where (1) $whr_emp_ls",'',true,false);
							//echo "delete from gft_hot_lead_source_temptable where (1) $whr_emp_ls";
							$insert_updated_hotleads="insert ignore into gft_hot_lead_source_temptable (GHL_EMP_ID,GHL_LEAD_SOURCE,GHL_MONTH," .
									"GHL_YEAR,GHL_VALUE)";
							$query_update_hotleads_ls1=return_query_for_lead_source(/*$month_wise=*/true,/*$carried_forward=*/false,$financial_yr,$emp_id);
							$query_update_hotleads_ls2=return_query_for_lead_source(/*$month_wise=*/false,/*$carried_forward=*/true,$financial_yr,$emp_id);
								

							$insert_updated_hotleads_execute1="$insert_updated_hotleads($query_update_hotleads_ls1)";
							execute_my_query($insert_updated_hotleads_execute1,'',true,false);
			//echo "<br>".$insert_updated_hotleads_execute1;

			$insert_updated_hotleads_execute2="$insert_updated_hotleads($query_update_hotleads_ls2)";
			execute_my_query($insert_updated_hotleads_execute2,'',true,false);
	//echo "<br>".$insert_updated_hotleads_execute2;
  	
}

/**
 * @param int $month
 * @param int $year
 * @param string $emp_id
 * 
 * @return void
 */
function update_into_gft_track_lead_status($month,$year,$emp_id=null){
	global $sp_lead_hdr_check;
	/*this is to be call on first day of the month*/
	$i=$month;
	$y=$year;
	$query_lfield='';$query_etrack='';
	/*deleting all details from track for the month*/	
	if($emp_id!=''){ $query_etrack=" and gtl_emp_id='$emp_id' "; $query_lfield=" and glh_lfd_emp_id='$emp_id' ";}
	$already_update=date('Y-m').'-03';
	if($already_update>date('Y-m-d')){
		$query_del="delete from gft_track_lead_status where gtl_month='$i' and gtl_year='$y' $query_etrack";
		execute_my_query($query_del);
	}
	$year_month_no=year_month_no("$month","$year");
	// ***UPDATING IN TRACK LEAD_STATUS************
    $query="insert ignore into gft_track_lead_status(GTL_LEAD_CODE, GTL_EMP_ID,
    GTL_MONTH, GTL_YEAR, GTL_DOC, GTL_LEAD_STATUS, GTL_REMARKS, GTL_UPDATED_ON,GTL_TRACK_TYPE,GTL_YR_MONTH) ";
    $query.="(select glh_lead_code,glh_lfd_emp_id,'$i','$y',glh_approx_timetoclose,glh_status,'',now()," .
    		" case when glh_status=3 then 1 when GLH_BALANCE_AMOUNT >0 then 2 " .
    		" when ((GLH_INTEREST_ADDON='Y' or GID_UPSELL='Y' or GID_UPGRADE='Y' or (GID_READY_TO_PAY_ASS='Y' and GID_VALIDITY_DATE<now() and GID_EXPIRE_FOR in (1,3))) and GID_STATUS='A') then 3  else 0 end as track_type,'$year_month_no' " .
			" from gft_lead_hdr left join gft_install_dtl_new on (glh_lead_code=gid_lead_code and (GID_UPSELL='Y' or GID_UPGRADE='Y' or (GID_STATUS='A' and GID_READY_TO_PAY_ASS='Y' and GID_VALIDITY_DATE<now() and GID_EXPIRE_FOR in (1,3)))) " .
			" where glh_approx_timetoclose!='0000-00-00' $sp_lead_hdr_check $query_lfield " .
			" group by glh_lead_code having track_type!=0 ) ";
	//echo $query;
	execute_my_query($query,'',true,false);
    update_lead_status_cs($emp_id,/*$financial_yr_arr=*/null,$month=$i,$year=$y);
}

/**
 * @param string $emp_id
 * 
 * @return void
 */
function update_track_lead_status($emp_id){
	global $sp_lead_hdr_check;
	$today_date=date('Y-m-d');
	$year_month_no=year_month_no(null,null,$today_date);
	$query="replace into gft_track_lead_status(GTL_LEAD_CODE, GTL_EMP_ID," .
		" GTL_MONTH, GTL_YEAR, GTL_DOC, GTL_LEAD_STATUS, GTL_REMARKS, GTL_UPDATED_ON,GTL_TRACK_TYPE,GTL_YR_MONTH) ";
    $query.="(select glh_lead_code,glh_lfd_emp_id,month(now()),year(now()),glh_approx_timetoclose,glh_status,'',now(), " .
    		"case when glh_status=3 then 1 when GLH_BALANCE_AMOUNT >0 then 2 " .
    		" when ((GLH_INTEREST_ADDON='Y' or GID_UPSELL='Y' or GID_UPGRADE='Y' or (GID_READY_TO_PAY_ASS='Y' and GID_VALIDITY_DATE<now() and GID_EXPIRE_FOR in (1,3))) and GID_STATUS='A') then 3  else 0 end as track_type,'$year_month_no' " .
    		" from (gft_lead_hdr,gft_lead_fexec_dtl lf) " .
    		" left join gft_install_dtl_new on (glh_lead_code=gid_lead_code and (GID_UPSELL='Y' or GID_UPGRADE='Y' or (GID_STATUS='A' and GID_READY_TO_PAY_ASS='Y' and GID_VALIDITY_DATE<now() and GID_EXPIRE_FOR in (1,3)))) " .
    		" where glf_emp_id=glh_lfd_emp_id and glf_status='A' and glh_lead_code=glf_lead_code " .
    		" and glf_from_date='$today_date' and " .
    		" glh_lfd_emp_id='$emp_id'  " .
    		" $sp_lead_hdr_check  and " .
			" glh_approx_timetoclose!='0000-00-00' having track_type!=0) ";
	execute_my_query($query,'',true,false);
	update_lead_status_cs($emp_id);
	
}

/**
 * @param string $approve_for_exec_id
 * @param string $redo_done_date
 * @param string $month
 * @param string $year
 * @param string $finished_planning_date
 * @param string $redo_done_by
 * @param string $hot_leads
 * @param string $warm_leads_n
 * @param string $warm_leads_nn
 * @param string $prev_approved_by
 * @param string $prev_approved_date
 * @param string $zmgr_id
 * @param string $zmgr_approved_time
 * @param string $rmgr_id
 * @param string $rmgr_approved_time
 * @param string $amgr_id
 * @param string $amgr_approved_time
 * 
 * @return void
 */
function update_redo_entry($approve_for_exec_id,$redo_done_date,$month,$year,$finished_planning_date,
	$redo_done_by,$hot_leads,$warm_leads_n,$warm_leads_nn,$prev_approved_by,$prev_approved_date,
	$zmgr_id,$zmgr_approved_time,$rmgr_id,$rmgr_approved_time,$amgr_id,$amgr_approved_time){
	$query=" insert into gft_sales_planning_redo_cycle (GSP_REDO_ID, GSP_EMP_ID, GSP_MONTH," .
			"GSP_YEAR, GSP_PLANNED_FINISHED_DATE, GSP_REDO_DONE_BY, GSP_REDO_DONE_DATE, GSP_HOT_LEADS," .
			"GSP_WARM_LEADS_N, GSP_WARM_LEADS_NN,GSP_APPROVED_BY,GSP_APPROVED_DATE,GSP_PLANNED_APPROVED_AM," .
			"GSP_PLANNED_APPROVED_AM_DATE, GSP_PLANNED_APPROVED_RM, GSP_PLANNED_APPROVED_RM_DATE," .
			"GSP_PLANNED_APPROVED_ZM, GSP_PLANNED_APPROVED_ZM_DATE)value ('','$approve_for_exec_id','$month'," .
			"'$year','$finished_planning_date','$redo_done_by','$redo_done_date','$hot_leads'," .
			"'$warm_leads_n','$warm_leads_nn','$prev_approved_by','$prev_approved_date','$amgr_id'," .
			"'$amgr_approved_time','$rmgr_id','$rmgr_approved_time','$zmgr_id','$zmgr_approved_time')";
	$result=execute_my_query($query,'',true,false);
	if($result){
		$redo_id=mysqli_insert_id_wrapper();
		$query_track="insert into gft_sales_planning_redo_track (GSP_REDO_ID,GSP_LEAD_CODE,GSP_DOC,GSP_LEAD_STATUS)" .
				" (select $redo_id,gtl_lead_code,gtl_doc,gtl_lead_status from gft_track_lead_status " .
				" where gtl_emp_id='$approve_for_exec_id' and gtl_month='$month' and gtl_year='$year'  and GTL_TRACK_TYPE!=0 " .
				" and gtl_doc!='' and gtl_doc!='0000-00-00' )";
	    $result=execute_my_query($query_track,'',true,false);
	}			
}


/**
 * @param string $approve_for_exec_id
 * @param int $month
 * @param int $year
 * @param boolean $from_freezed_data
 * 
 * @return int[string]
 */
function get_revenue_sp($approve_for_exec_id,$month,$year,$from_freezed_data=false){
	global $sp_lead_hdr_check;
	global $sp_lead_hdr_check_wfr;

	$hot_lead_date1=date('Y-m-d',mktime(0,0,0,$month,1,$year));
	$hot_lead_date2=date('Y-m-d',mktime(0,0,0,($month+1),0,$year));
	if($from_freezed_data==false){
		$query="select gtl_emp_id," .
			" sum(if(GLH_APPROX_TIMETOCLOSE between '$hot_lead_date1' and '$hot_lead_date2',1,0)) as 'hot_leads'," .
			" sum(if(GLH_APPROX_TIMETOCLOSE between '$hot_lead_date1' and '$hot_lead_date2', glh_potential_amt,0)) as h_potential ".
			" from gft_track_lead_status ,gft_lead_hdr " .
     		" where gtl_lead_code=glh_lead_code and  GLH_APPROX_TIMETOCLOSE !='0000-00-00' " .
     		" AND GLH_APPROX_TIMETOCLOSE !=''  and GTL_TRACK_TYPE!=0  and gtl_emp_id='$approve_for_exec_id' " .
     		" and gtl_month='$month' and gtl_year='$year' $sp_lead_hdr_check and GTL_TRACK_TYPE=3 " .
     		" group by gtl_emp_id ";
		$result=execute_my_query($query,'',true,false);
	}else{
		$query="select gtl_wfreeezed_emp_id, " .
			" sum(if(GTL_WFREEZED_DOC between '$hot_lead_date1' and '$hot_lead_date2',1,0)) as 'hot_leads'," .
			" sum(if(GTL_WFREEZED_DOC between '$hot_lead_date1' and '$hot_lead_date2', GTL_WFREEZED_POTENTIAL_AMT,0)) as h_potential ".
			" from  gft_track_lead_status,gft_lead_hdr " .
     		" where gtl_lead_code=glh_lead_code and GTL_WFREEZED_DOC !='0000-00-00'  and GTL_WFREEZED_TRACK_TYPE!=0 " .
     		" AND GTL_WFREEZED_DOC !=''  and gtl_wfreeezed_emp_id='$approve_for_exec_id' " .
     		" and gtl_month='$month' and gtl_year='$year' $sp_lead_hdr_check_wfr " .
     		" and GTL_WFREEZED_TRACK_TYPE=3 group by gtl_wfreeezed_emp_id ";
		$result=execute_my_query($query,'',true,false);
	}
	$leads_count=/*. (int[string]) .*/ null;
	$leads_count['hot_leads']=0;
	$leads_count['h_potential']=0;
	if($qdata=mysqli_fetch_array($result)){
		$leads_count['hot_leads']=(int)$qdata['hot_leads'];
		$leads_count['h_potential']=(int)$qdata['h_potential'];
	}
	return $leads_count;
}

/**
 * @param string $approve_for_exec_id
 * @param int $month
 * @param int $year
 * @param boolean $from_freezed_data
 * @param boolean $installed_only
 * 
 * @return int[string]
 */
function get_order_sp($approve_for_exec_id,$month,$year,$from_freezed_data=false,$installed_only=true){
	$from_date=date('Y-m-d',mktime(0,0,0,$month,1,$year));
	$to_date=date('Y-m-d',mktime(0,0,0,($month+1),0,$year));
	if($from_freezed_data==false){
		$query=" select oh.GOD_INCHARGE_EMP_ID, count(*) as hot_order, sum(GOD_BALANCE_AMT) outstanding, sum(ifnull(oc.GMOC_COLLECTIBLE_AMT,0)) order_collectible " .
				" FROM gft_order_hdr oh " .
				" join gft_order_collection_dtl oc on(oc.GMOC_ORDER_NO=oh.GOD_ORDER_NO and oc.GMOC_AGREED='Y' and oc.gmoc_current_status=1)" .
				" WHERE oh.GOD_INCHARGE_EMP_ID=$approve_for_exec_id and oh.GOD_ORDER_STATUS='A' ".($installed_only==true? "and god_installed_value > god_collection_realized":""). " and oh.GOD_BALANCE_AMT>0 " .
				" and oc.GMOC_collection_date between '$from_date' and '$to_date' group by oh.GOD_INCHARGE_EMP_ID ";
	}else{
		$query=" select oh.GOD_INCHARGE_EMP_ID, count(*) as hot_order, sum(GOD_BALANCE_AMT) outstanding, sum(ifnull(oc.GMOC_COLLECTIBLE_AMT,0)) order_collectible " .
			" FROM gft_order_hdr oh " .
			" join gft_order_collection_dtl oc on(oc.GMOC_ORDER_NO=oh.GOD_ORDER_NO and oc.GMOC_AGREED='Y' and oc.gmoc_current_status=1)" .
			" WHERE oh.GOD_ORDER_STATUS='A' and god_installed_value > god_collection_realized and oh.GOD_BALANCE_AMT>0 " .
			" and GMOC_WFREEZED_MONTH=$month and GMOC_WFREEZED_YEAR=$year  " .
			" group by oh.GOD_INCHARGE_EMP_ID ";
	}
	//echo $query;
	$order_count=/*. (int[string]) .*/ null;
	$result=execute_my_query($query,'',true,false);
	$order_count['hot_order']=0;
	$order_count['order_collectible']=0;
	$order_count['outstanding']=0;
	if($qdata=mysqli_fetch_array($result)){
		$order_count['hot_order']=(int)$qdata['hot_order'];
		$order_count['order_collectible']=(int)$qdata['order_collectible'];
		$order_count['outstanding']=(int)$qdata['outstanding'];
	}	
	return $order_count;
}

/**
 * @param int $month
 * @param int $year
 * 
 * @return void
 */
function update_opening_closing_balance($month,$year){
/*1 st of month and after previous month clearence checkedby accounts*/
	$start_of_month=date('Y-m-d',mktime('0','0','0',$month,1,$year));
	$end_of_month=date('Y-m-d',mktime('0','0','0',$month+1,0,$year));
	execute_my_query("delete from gft_outstanding_ledger_bmap_dtl where GOL_MONTH='$month' and GOL_YEAR='$year' ");
	
	if((int)date('m')==$month and (int)date('Y')==$year && 1==2){
	$query="insert into gft_outstanding_ledger_bmap_dtl(
	GOL_ZONE_ID,GOL_REGION_ID,GOL_AREA_ID,GOL_TERR_ID,GOL_EMP_ID,GOL_MONTH,GOL_YEAR,GOL_OPENING_BAL,
	GOL_FROM_DATE,GOL_END_DATE)(	
	select zone_id,region_id,area_id,terr_id,god_incharge_emp_id,'$month','$year',
	sum(GOD_BALANCE_AMT) 'outstanding','$start_of_month','$end_of_month'  
	from (gft_order_hdr,gft_lead_hdr)
	inner join b_map_view bm on (terr_id=glh_territory_id)
	left join gft_order_collection_dtl OS_WF on (gmoc_order_no=god_order_no and GMOC_WFREEZED_MONTH=$month and  GMOC_WFREEZED_YEAR=$year ) 
	where god_lead_code=glh_lead_code and god_order_status='A' and    
	GOD_BALANCE_AMT>0 and god_order_date<'$start_of_month'  and glh_lead_type!='8' 
	group by zone_id,region_id,terr_id,god_incharge_emp_id) ";
	}
	else {
		/*Current Outstanding + Os collection for calc of previous month*/
	$query=" insert into gft_outstanding_ledger_bmap_dtl(
	GOL_ZONE_ID,GOL_REGION_ID,GOL_AREA_ID,GOL_TERR_ID,GOL_EMP_ID,GOL_MONTH,GOL_YEAR,GOL_OPENING_BAL,
	GOL_FROM_DATE,GOL_END_DATE)(select bm.zone_id,bm.region_id,bm.area_id,bm.terr_id,oh.god_incharge_emp_id,'$month','$year',
	sum(if(GOD_BALANCE_AMT>0,round(GOD_BALANCE_AMT),0))+ifnull(COLLECTION1,0) as 'outstanding',
	'$start_of_month','$end_of_month' from gft_order_hdr oh 
	inner join gft_lead_hdr lh on (glh_lead_code=god_lead_code and glh_lead_type!=8)
	inner join b_map_view bm on (terr_id=glh_territory_id) 
	left join(
	select zone_id,region_id,area_id,terr_id,god_incharge_emp_id,sum(gcr_amount) 'COLLECTION1' 
	from  gft_collection_receipt_dtl cr
	inner join gft_order_hdr oh on (god_order_no=gcr_order_no and god_order_status='A' and 
	god_order_date < '$start_of_month' and GOD_LAST_STATUS_CHANGED_ON < '$start_of_month'
	and GOD_COLLECTION_INCHARGE_EFF_FROM<'$start_of_month' )   
	inner join gft_lead_hdr lh on (glh_lead_code=god_lead_code and glh_lead_type!=8)
	inner join b_map_view bm on (terr_id=glh_territory_id)
	inner join gft_receipt_dtl rd on ( rd.grd_receipt_id = cr.gcr_receipt_id and  
	GRD_CHECKED_WITH_LEDGER='Y' and GRD_CHEQUE_CLEARED_DATE >='$start_of_month' 
	 and grd_status in ('H','O','C','P','D','W') )
	 group by zone_id,region_id,area_id,terr_id,god_incharge_emp_id  
	) cl on (cl.god_incharge_emp_id=oh.god_incharge_emp_id and 
	cl.zone_id=bm.zone_id and cl.region_id=bm.region_id and cl.area_id=bm.area_id and cl.terr_id=bm.terr_id )
	where god_order_date < '$start_of_month' and god_order_status='A' 
	and GOD_LAST_STATUS_CHANGED_ON <'$start_of_month'  and GOD_COLLECTION_INCHARGE_EFF_FROM<'$start_of_month' 
	group by bm.zone_id,bm.region_id,bm.area_id,bm.terr_id,oh.god_incharge_emp_id ) ";
	
	}
	execute_my_query($query);
	//print $query;	
	$last_month=date('m',mktime('0','0','0',$month-1,1,$year));
	$last_month_yr=date('Y',mktime('0','0','0',$month-1,1,$year));
	
    /*updating os planned amount */
	$query_update="update gft_outstanding_ledger_bmap_dtl m1,(
	select bm.zone_id,bm.region_id,bm.area_id,bm.terr_id,oh.god_incharge_emp_id,
	sum(round(GMOC_COLLECTIBLE_AMT)) os_coll from gft_order_hdr oh 
	inner join gft_lead_hdr lh on (glh_lead_code=god_lead_code and glh_lead_type!=8)
	inner join b_map_view bm on (terr_id=glh_territory_id) 
	join gft_order_collection_dtl OS_WF on (gmoc_order_no=god_order_no and     
	GMOC_WFREEZED_MONTH=$month and  GMOC_WFREEZED_YEAR=$year  
	and GMOC_collection_date between '$start_of_month' and '$end_of_month' ) 
	group by bm.zone_id,bm.region_id,bm.area_id,bm.terr_id,oh.god_incharge_emp_id)m2
	set GOL_OS_PLANNED=os_coll  where GOL_MONTH='$month' and GOL_YEAR='$year' and 
	 m1.GOL_ZONE_ID=m2.zone_id AND m1.GOL_REGION_ID=m2.region_id and 
	 m1.GOL_AREA_ID=m2.area_id AND m1.GOL_TERR_ID=m2.terr_id AND 
	 m1.GOL_EMP_ID=m2.god_incharge_emp_id ";
	 execute_my_query($query_update);
	
	 $query_update="update gft_outstanding_ledger_bmap_dtl m1,
	(select GOL_ZONE_ID,GOL_REGION_ID,GOL_AREA_ID,GOL_TERR_ID,GOL_EMP_ID,GOL_OPENING_BAL FROM 
	 gft_outstanding_ledger_bmap_dtl WHERE GOL_MONTH='$month' and GOL_YEAR='$year' ) m2 
	 set m1.GOL_CLOSING_BAL= m2.GOL_OPENING_BAL where GOL_MONTH='$last_month' and GOL_YEAR='$last_month_yr' and 
	 m1.GOL_ZONE_ID=m2.GOL_ZONE_ID AND m1.GOL_REGION_ID=m2.GOL_REGION_ID and 
	 m1.GOL_AREA_ID=m2.GOL_AREA_ID AND m1.GOL_TERR_ID=m2.GOL_TERR_ID AND 
	 m1.GOL_EMP_ID=m2.GOL_EMP_ID ";
	
	execute_my_query($query_update);
	 	 		
}

/**
 * @param int $month
 * @param int $year
 * 
 * @return void
 */
function update_opening_closing_balance_followup_bmap($month,$year){
//call every mid night
	$start_of_month=date('Y-m-d',mktime('0','0','0',$month,1,$year));
	$end_of_month=date('Y-m-d',mktime('0','0','0',$month+1,0,$year));

	/*Query Desc : New order and its collection */
	if($month==(int)date('m') and $year==(int)date('Y')){
	$query="select zone_id,region_id,area_id,terr_id,god_incharge_emp_id,sum(round(GOD_ORDER_AMT)) ORDER_AMT,
	sum(GOD_COLLECTION_REALIZED) 'COLLECTION' 
	from (gft_order_hdr,gft_lead_hdr)
	inner join b_map_view bm on (terr_id=glh_territory_id)
	where god_lead_code=glh_lead_code and god_order_status='A'   
	and glh_lead_type!='8' AND god_order_date between '$start_of_month'	and '$end_of_month'  
	group by zone_id,region_id,area_id,terr_id,god_incharge_emp_id ";
	}else {
	$query=" select bm.zone_id,bm.region_id,bm.area_id,bm.terr_id,oh.god_incharge_emp_id,'$month','$year',
	sum(GOD_ORDER_AMT) ORDER_AMT,COLLECTION1 as 'COLLECTION',
	'$start_of_month','$end_of_month' from gft_order_hdr oh 
	inner join gft_lead_hdr lh on (glh_lead_code=god_lead_code)
	inner join b_map_view bm on (terr_id=glh_territory_id and glh_lead_type!=8) 
	left join(
	select zone_id,region_id,area_id,terr_id,god_incharge_emp_id,sum(gcr_amount) 'COLLECTION1' 
	from  gft_collection_receipt_dtl cr
	inner join gft_order_hdr oh on (god_order_no=gcr_order_no and god_order_status='A' and  god_order_date 
	between '$start_of_month'	and '$end_of_month')   
	inner join gft_lead_hdr lh on (glh_lead_code=god_lead_code and glh_lead_type!=8)
	inner join b_map_view bm on (terr_id=glh_territory_id)
	inner join gft_receipt_dtl rd on ( rd.grd_receipt_id = cr.gcr_receipt_id and  
	GRD_CHECKED_WITH_LEDGER='Y' and GRD_CHEQUE_CLEARED_DATE between '$start_of_month' and '$end_of_month' 
	and grd_status in ('H','O','C','P','D','W') )
	 group by zone_id,region_id,area_id,terr_id,god_incharge_emp_id  
	) cl on (cl.god_incharge_emp_id=oh.god_incharge_emp_id and 
	cl.zone_id=bm.zone_id and cl.region_id=bm.region_id and cl.area_id=bm.area_id and cl.terr_id=bm.terr_id )
	where god_order_date between '$start_of_month' and '$end_of_month' and god_order_status='A' 
	group by bm.zone_id,bm.region_id,bm.area_id,bm.terr_id,oh.god_incharge_emp_id  ";
	
	 
		}
	
	
	$result=execute_my_query($query);
	while($qdata=mysqli_fetch_array($result)){
		$incharge_emp_id=$qdata['god_incharge_emp_id'];
		$new_order_amt=$qdata['ORDER_AMT'];
		$collection=$qdata['COLLECTION']; 
		$zone_id=$qdata['zone_id'];
		$region_id=$qdata['region_id'];
		$area_id=$qdata['area_id'];
		$terr_id=$qdata['terr_id'];
		$exist_rows="select * from gft_outstanding_ledger_bmap_dtl where GOL_EMP_ID='$incharge_emp_id' and  
		GOL_MONTH='$month' and GOL_YEAR='$year' and GOL_ZONE_ID='$zone_id' and GOL_REGION_ID='$region_id'  
		and GOL_AREA_ID='$area_id' and GOL_TERR_ID='$terr_id' ";
		$rexist_rows=execute_my_query($exist_rows);
		if(mysqli_num_rows($rexist_rows)==1){
			$query_update1="update gft_outstanding_ledger_bmap_dtl set GOL_NEW_ORDER_AMT='$new_order_amt' ,
			GOL_NEW_ORDER_COLLECTION='$collection' where GOL_EMP_ID='$incharge_emp_id' and  
			GOL_MONTH='$month' and GOL_YEAR='$year' and GOL_ZONE_ID='$zone_id' and GOL_REGION_ID='$region_id'  
			and GOL_AREA_ID='$area_id' and GOL_TERR_ID='$terr_id' ";
			execute_my_query($query_update1);
		}
		else{
			/* incase he has no outstanding then here new row can added */
			$query_update2="insert into gft_outstanding_ledger_bmap_dtl
			(GOL_ZONE_ID,GOL_REGION_ID,GOL_AREA_ID,GOL_TERR_ID,GOL_EMP_ID,GOL_MONTH,GOL_YEAR,GOL_OPENING_BAL,GOL_NEW_ORDER_AMT,GOL_NEW_ORDER_COLLECTION,
			GOL_FROM_DATE,GOL_END_DATE) values  
			('$zone_id','$region_id','$area_id','$terr_id','$incharge_emp_id','$month','$year','0','$new_order_amt','$collection',
			'$start_of_month','$end_of_month') ";
			execute_my_query($query_update2);
		}
	}

	/*outstanding collection */
	$query=" select zone_id,region_id,area_id,terr_id,god_incharge_emp_id,sum(gcr_amount) 'collected' from gft_order_hdr
	inner join gft_lead_hdr lh on (glh_lead_code=god_lead_code and glh_lead_type!=8)
	inner join b_map_view bm on (terr_id=glh_territory_id) 
	inner join gft_collection_receipt_dtl cr on (god_order_no=gcr_order_no)
	inner join gft_receipt_dtl rd on ( rd.grd_receipt_id = cr.gcr_receipt_id and  
	GRD_CHECKED_WITH_LEDGER='Y' and GRD_CHEQUE_CLEARED_DATE between '$start_of_month' 
	and '$end_of_month' and grd_status in ('H','O','C','P','D','W') )   
	where god_order_date<'$start_of_month' and god_order_status='A'  group by zone_id,region_id,area_id,terr_id,god_incharge_emp_id ";
	$result=execute_my_query($query); 
	while($qdata=mysqli_fetch_array($result)){
		$incharge_emp_id=$qdata['god_incharge_emp_id'];
		$collected=$qdata['collected'];
		$zone_id=$qdata['zone_id'];
		$region_id=$qdata['region_id'];
		$area_id=$qdata['area_id'];
		$terr_id=$qdata['terr_id'];
		
		$query_update1="update gft_outstanding_ledger_bmap_dtl SET GOL_OUTSTANIDING_COLLECTION='$collected' where 
		GOL_EMP_ID='$incharge_emp_id' and GOL_MONTH='$month' and GOL_YEAR='$year' 
		and GOL_ZONE_ID='$zone_id' and GOL_REGION_ID='$region_id' 
		and GOL_AREA_ID='$area_id' and GOL_TERR_ID='$terr_id'";
		execute_my_query($query_update1);
	}
	
	/*Transferred_to executives from other executive */
	$query="select zone_id,region_id,area_id,terr_id,god_incharge_emp_id,sum(round(GOD_BALANCE_AMT)) 'outstanding' 
	from (gft_order_hdr,gft_lead_hdr)
	inner join b_map_view bm on (terr_id=glh_territory_id) 
	where god_lead_code=glh_lead_code and god_order_status='A' and 
	GOD_BALANCE_AMT!='0' and GOD_COLLECTION_INCHARGE_EFF_FROM between '$start_of_month' 
	and '$end_of_month' and god_order_date not between '$start_of_month' 
	and '$end_of_month' and glh_lead_type!='8' group by zone_id,region_id,area_id,terr_id,god_incharge_emp_id ";
	$result=execute_my_query($query);
	while($qdata=mysqli_fetch_array($result)){
		$incharge_emp_id=$qdata['god_incharge_emp_id'];
		$outstanding=$qdata['outstanding'];
		$zone_id=$qdata['zone_id'];
		$region_id=$qdata['region_id'];
		$area_id=$qdata['area_id'];
		$terr_id=$qdata['terr_id'];
		$exist_rows="select * from gft_outstanding_ledger_bmap_dtl where GOL_EMP_ID='$incharge_emp_id' and  
		GOL_MONTH='$month' and GOL_YEAR='$year' and GOL_ZONE_ID='$zone_id' and GOL_REGION_ID='$region_id'  
		and GOL_AREA_ID='$area_id' and GOL_TERR_ID='$terr_id' ";
		$rexist_rows=execute_my_query($exist_rows);
		if(mysqli_num_rows($rexist_rows)==1){
			$query_update1="update gft_outstanding_ledger_bmap_dtl SET GOL_OS_TRANSFERED_TO_ME='$outstanding' where 
			GOL_EMP_ID='$incharge_emp_id' and GOL_MONTH='$month' and GOL_YEAR='$year' 
			and GOL_ZONE_ID='$zone_id' and GOL_REGION_ID='$region_id' 
			and GOL_AREA_ID='$area_id' and GOL_TERR_ID='$terr_id'";
			execute_my_query($query_update1);
		}else{
			$query_update2="insert into gft_outstanding_ledger_bmap_dtl
			(GOL_ZONE_ID,GOL_REGION_ID,GOL_AREA_ID,GOL_TERR_ID,GOL_EMP_ID,GOL_MONTH,GOL_YEAR,
			GOL_OS_TRANSFERED_TO_ME,GOL_FROM_DATE,GOL_END_DATE) values  
			('$zone_id','$region_id','$area_id','$terr_id','$incharge_emp_id','$month','$year','$outstanding',
			'$start_of_month','$end_of_month') ";
			execute_my_query($query_update2);
		}
	}
	/*Transferred_to other exectives from him*/
	$query="select zone_id,region_id,area_id,terr_id,GOD_PREV_COLLECTION_INCHARGE,sum(round(GOD_BALANCE_AMT)) 'outstanding' 
	from (gft_order_hdr,gft_lead_hdr)
	inner join b_map_view bm on (terr_id=glh_territory_id) 
	where god_lead_code=glh_lead_code and god_order_status='A' and 
	GOD_BALANCE_AMT!='0' and GOD_COLLECTION_INCHARGE_EFF_FROM between '$start_of_month' 
	and '$end_of_month' and god_order_date not between '$start_of_month' 
	and '$end_of_month' and glh_lead_type!='8' group by zone_id,region_id,area_id,terr_id,GOD_PREV_COLLECTION_INCHARGE ";
	$result=execute_my_query($query);
	while($qdata=mysqli_fetch_array($result)){
		$incharge_emp_id=$qdata['GOD_PREV_COLLECTION_INCHARGE'];
		$outstanding=$qdata['outstanding'];
		$zone_id=$qdata['zone_id'];
		$region_id=$qdata['region_id'];
		$area_id=$qdata['area_id'];
		$terr_id=$qdata['terr_id'];
		$query_update1="update gft_outstanding_ledger_bmap_dtl SET GOL_OS_TRANSFERED_FROM_ME='$outstanding' where 
		GOL_EMP_ID='$incharge_emp_id' and GOL_MONTH='$month' and GOL_YEAR='$year' 
		and GOL_ZONE_ID='$zone_id' and GOL_REGION_ID='$region_id' 
		and GOL_AREA_ID='$area_id' and GOL_TERR_ID='$terr_id' ";
		execute_my_query($query_update1);
	}
	
	
	
	/*Activate and deactivate order  */
	
	$query="select bm.zone_id,bm.region_id,bm.area_id,bm.terr_id,oh.god_incharge_emp_id,'$month','$year',
	sum(if(GOD_BALANCE_AMT>0,round(GOD_BALANCE_AMT),0))+ifnull(COLLECTION1,0) as 'outstanding',
	'$start_of_month','$end_of_month' 
	from gft_order_hdr oh  
	inner join gft_lead_hdr lh on (glh_lead_code=god_lead_code and glh_lead_type!=8)
	inner join b_map_view bm on (terr_id=glh_territory_id) 
	left join(
	select zone_id,region_id,area_id,terr_id,god_incharge_emp_id,sum(gcr_amount) 'COLLECTION1' 
	from  gft_collection_receipt_dtl cr
	inner join gft_order_hdr oh on (god_order_no=gcr_order_no and god_order_status='A' 
	and GOD_LAST_STATUS_CHANGED_ON between '$start_of_month' and '$end_of_month' 
	and god_order_date <'$start_of_month' )   
	inner join gft_lead_hdr lh on (glh_lead_code=god_lead_code and glh_lead_type!=8)
	inner join b_map_view bm on (terr_id=glh_territory_id)
	inner join gft_receipt_dtl rd on ( rd.grd_receipt_id = cr.gcr_receipt_id and  
	GRD_CHECKED_WITH_LEDGER='Y' and GRD_CHEQUE_CLEARED_DATE >='$start_of_month' 
	 and grd_status in ('H','O','C','P','D','W') )
	 group by zone_id,region_id,area_id,terr_id,god_incharge_emp_id  
	) cl on (cl.god_incharge_emp_id=oh.god_incharge_emp_id and 
	cl.zone_id=bm.zone_id and cl.region_id=bm.region_id and cl.area_id=bm.area_id and cl.terr_id=bm.terr_id )
	where god_order_date < '$start_of_month' and god_order_status='A'  
	and GOD_LAST_STATUS_CHANGED_ON between '$start_of_month' and '$end_of_month'     
	group by bm.zone_id,bm.region_id,bm.area_id,bm.terr_id,oh.god_incharge_emp_id ";

	$result=execute_my_query($query);
	while($qdata=mysqli_fetch_array($result)){
		$incharge_emp_id=$qdata['god_incharge_emp_id'];
		$outstanding=$qdata['outstanding'];
		$zone_id=$qdata['zone_id'];
		$region_id=$qdata['region_id'];
		$area_id=$qdata['area_id'];
		$terr_id=$qdata['terr_id'];
		$exist_rows="select * from gft_outstanding_ledger_bmap_dtl where GOL_EMP_ID='$incharge_emp_id' and  
		GOL_MONTH='$month' and GOL_YEAR='$year' and GOL_ZONE_ID='$zone_id' and GOL_REGION_ID='$region_id'  
		and GOL_AREA_ID='$area_id' and GOL_TERR_ID='$terr_id' ";
		$rexist_rows=execute_my_query($exist_rows);
		if(mysqli_num_rows($rexist_rows)==1){
		$query_update1="update gft_outstanding_ledger_bmap_dtl SET GOL_ORDER_TO_ACTIVE_STATUS='$outstanding' 
		where 
		GOL_EMP_ID='$incharge_emp_id' and GOL_MONTH='$month' and GOL_YEAR='$year' 
		and GOL_ZONE_ID='$zone_id' and GOL_REGION_ID='$region_id' 
		and GOL_AREA_ID='$area_id' and GOL_TERR_ID='$terr_id' ";
		execute_my_query($query_update1);
		}
		else {
$query_update2="insert into gft_outstanding_ledger_bmap_dtl
			(GOL_ZONE_ID,GOL_REGION_ID,GOL_AREA_ID,GOL_TERR_ID,GOL_EMP_ID,GOL_MONTH,GOL_YEAR,
			GOL_ORDER_TO_ACTIVE_STATUS,GOL_FROM_DATE,GOL_END_DATE) values  
			('$zone_id','$region_id','$area_id','$terr_id','$incharge_emp_id','$month','$year',
			'$outstanding','$start_of_month','$end_of_month') ";
			execute_my_query($query_update2);
		}	
	}//end of while
	
	
	
	$query="select zone_id,region_id,area_id,terr_id,god_incharge_emp_id,
	SUM(round(GOD_BALANCE_AMT)) 'c_outstanding' 
	from gft_order_hdr
	inner join gft_lead_hdr lh on (god_lead_code=glh_lead_code  and glh_lead_type!=8)
	inner join b_map_view bm on (terr_id=glh_territory_id)
	where GOD_BALANCE_AMT>0 and GOD_LAST_STATUS_CHANGED_ON between '$start_of_month'  
	and '$end_of_month' and god_order_date <'$start_of_month' and god_order_status!='A'    
	group by zone_id,region_id,area_id,terr_id,god_incharge_emp_id ";
	$result=execute_my_query($query);
	while($qdata=mysqli_fetch_array($result)){
		$incharge_emp_id=$qdata['god_incharge_emp_id'];
		$zone_id=$qdata['zone_id'];
		$region_id=$qdata['region_id'];
		$area_id=$qdata['area_id'];
		$terr_id=$qdata['terr_id'];
		$c_outstanding=$qdata['c_outstanding'];
		$exist_rows="select * from gft_outstanding_ledger_bmap_dtl where GOL_EMP_ID='$incharge_emp_id' and  
		GOL_MONTH='$month' and GOL_YEAR='$year' and GOL_ZONE_ID='$zone_id' and GOL_REGION_ID='$region_id'  
		and GOL_AREA_ID='$area_id' and GOL_TERR_ID='$terr_id' ";
		$rexist_rows=execute_my_query($exist_rows);
		if(mysqli_num_rows($rexist_rows)==1){
		$query_update1="update gft_outstanding_ledger_bmap_dtl SET 
		GOL_ORDER_TO_INACTIVE_STATUS='$c_outstanding' where 
		GOL_EMP_ID='$incharge_emp_id' and GOL_MONTH='$month' and GOL_YEAR='$year' 
		and GOL_ZONE_ID='$zone_id' and GOL_REGION_ID='$region_id' 
		and GOL_AREA_ID='$area_id' and GOL_TERR_ID='$terr_id' ";
		execute_my_query($query_update1);
		}
		else {
$query_update2="insert into gft_outstanding_ledger_bmap_dtl
			(GOL_ZONE_ID,GOL_REGION_ID,GOL_AREA_ID,GOL_TERR_ID,GOL_EMP_ID,GOL_MONTH,GOL_YEAR,
			GOL_ORDER_TO_INACTIVE_STATUS,GOL_FROM_DATE,GOL_END_DATE) values  
			('$zone_id','$region_id','$area_id','$terr_id','$incharge_emp_id','$month','$year',
			'$c_outstanding','$start_of_month','$end_of_month') ";
			execute_my_query($query_update2);
		}	
	}

	if($month==(int)date('m') and $year==(int)date('Y')){
		/* toupdate the closing balance of the current month
		$query_update_clb="update gft_outstanding_ledger_bmap_dtl ,( 
		select zone_id,region_id,area_id,terr_id,god_incharge_emp_id,sum(round(god_balance_amt)) as outs
		from gft_order_hdr,gft_lead_hdr 
		inner join b_map_view bm on (terr_id=glh_territory_id)
		where god_lead_code=glh_lead_code and god_order_status='A' 
		and GOD_BALANCE_AMT>0 and glh_lead_type!='8' 
		group by zone_id,region_id,area_id,terr_id,god_incharge_emp_id)t
		set GOL_CLOSING_BAL=outs where 
		GOL_EMP_ID=god_incharge_emp_id and GOL_MONTH='$month' and GOL_YEAR='$year' 
		and GOL_ZONE_ID=zone_id and GOL_REGION_ID=region_id 
		and GOL_AREA_ID=area_id and GOL_TERR_ID=terr_id ";
		$result_update_clb=execute_my_query($query_update_clb);
		*/
		
		
		$query_select="select zone_id,region_id,area_id,terr_id,god_incharge_emp_id,
		sum(round(god_balance_amt)) as outs
		from gft_order_hdr,gft_lead_hdr 
		inner join b_map_view bm on (terr_id=glh_territory_id)
		where god_lead_code=glh_lead_code and god_order_status='A' 
		and GOD_BALANCE_AMT>0 and glh_lead_type!='8' 
		group by zone_id,region_id,area_id,terr_id,god_incharge_emp_id ";
		
		$result_cs_query=execute_my_query($query_select);
		while($qd=mysqli_fetch_array($result_cs_query)){
			$incharge_emp_id=$qd['god_incharge_emp_id'];
			$zone_id=$qd['zone_id'];
			$region_id=$qd['region_id'];
			$area_id=$qd['area_id'];
			$terr_id=$qd['terr_id'];
			$c_outstanding=$qd['outs'];
			
			$exist_rows="select * from gft_outstanding_ledger_bmap_dtl where GOL_EMP_ID='$incharge_emp_id' and  
			GOL_MONTH='$month' and GOL_YEAR='$year' and GOL_ZONE_ID='$zone_id' and GOL_REGION_ID='$region_id'  
			and GOL_AREA_ID='$area_id' and GOL_TERR_ID='$terr_id' ";
			$rexist_rows=execute_my_query($exist_rows);
			if(mysqli_num_rows($rexist_rows)==1){
				$query_update1="update gft_outstanding_ledger_bmap_dtl SET 
				GOL_CLOSING_BAL='$c_outstanding' where 
				GOL_EMP_ID='$incharge_emp_id' and GOL_MONTH='$month' and GOL_YEAR='$year' 
				and GOL_ZONE_ID='$zone_id' and GOL_REGION_ID='$region_id' 
				and GOL_AREA_ID='$area_id' and GOL_TERR_ID='$terr_id' ";
				execute_my_query($query_update1);
			}
			else {
				$query_update2="insert into gft_outstanding_ledger_bmap_dtl
				(GOL_ZONE_ID,GOL_REGION_ID,GOL_AREA_ID,GOL_TERR_ID,GOL_EMP_ID,GOL_MONTH,GOL_YEAR,
				GOL_CLOSING_BAL,GOL_FROM_DATE,GOL_END_DATE) values  
				('$zone_id','$region_id','$area_id','$terr_id','$incharge_emp_id','$month','$year',
				'$c_outstanding','$start_of_month','$end_of_month') ";
				execute_my_query($query_update2);
			}	
		}
	/*	execute_my_query("update gft_outstanding_ledger_bmap_dtl,gft_emp_master set GOL_CLOSING_BAL='' where 
		GOL_EMP_ID=gem_emp_id and GOL_MONTH='$month' and GOL_YEAR='$year' and gem_status='I' ");*/
		
		
	}
	
	/*Query check
	 * 
	 * 
	 * select gol_emp_id,sum(gol_closing_bal) cb ,sum(gol_opening_bal),sum(gol_new_order_amt), sum(GOL_ORDER_TO_ACTIVE_STATUS), sum(ifnull(gol_opening_bal,0)+ifnull(gol_new_order_amt,0)+ifnull(GOL_ORDER_TO_ACTIVE_STATUS,0)) a1,
sum(ifnull(gol_new_order_collection,0)+ifnull(GOL_OUTSTANIDING_COLLECTION,0)+ifnull(gol_order_to_inactive_status,0)) a2,
sum(ifnull(gol_opening_bal,0)+ifnull(gol_new_order_amt,0)+ifnull(GOL_ORDER_TO_ACTIVE_STATUS,0) 
-ifnull(gol_new_order_collection,0)-ifnull(GOL_OUTSTANIDING_COLLECTION,0)-ifnull(gol_order_to_inactive_status,0)) ccb ,
sum(GOL_OUTSTANIDING_COLLECTION) 
 from  gft_outstanding_ledger_bmap_dtl  where gol_month=5 and gol_year='2011' group by gol_emp_id having cb!=ccb
	 */
	
	
}

/**
 * @param string $month
 * @param string $year
 * 
 * @return void
 */
function query_checker_outstanding($month,$year){
	$query_cnt=" select gmoc_order_no,count(*) cntfrom gft_order_collection_dtl where 
	GMOC_WFREEZED_MONTH='$month' and GMOC_WFREEZED_YEAR='$year' 
	group by gmoc_order_no having cnt>1";
	$result_cnt=execute_my_query($query_cnt);
	$num_rows=mysqli_num_rows($result_cnt);
	echo "<table border='1'>";
	if($num_rows>1){
		echo '<th><td colspan="2">Planning (Dublicate entry)</td></th>';
		while($qdata=mysqli_fetch_array($result_cnt)){
			echo '<th><td colspan="2">'.$qdata['gmoc_order_no'].'</td><td>'.$qdata['cnt'].'</td></th>';
		}
	}
	echo "</table>";
}

/**
 * @param int $month
 * @param int $year
 * @param string $emp_id
 * 
 * @return void
 */
function updated_lead_status_on3rd($month,$year,$emp_id=null){
	global $sp_lead_hdr_check;
	$month_end=find_lastday_in_month($month,$year);
	//skip earlier installed  to update
	$query_update_b4_3rd="update gft_track_lead_status,gft_lead_hdr  set GTL_WFREEEZED_EMP_ID=0 ," .
			" GTL_FREEZED_DATE='0000-00-00' where " .
			" gtl_lead_code=glh_lead_code and GTL_TRACK_TYPE!=0 and " .
			" gtl_month='$month' and gtl_year='$year' and GTL_WFREEEZED_EMP_ID='$emp_id' " ;
	execute_my_query($query_update_b4_3rd,'',true,false);
	//glh_status not in (8,9,10) => GTL_TRACK_TYPE!=0
	$query_update_on_3rd=" update gft_track_lead_status,gft_lead_hdr,gft_activity set GTL_FREEZED_DATE=now()," .
			"GTL_WFREEZED_LEAD_STATUS=GLH_STATUS,GTL_LEAD_STATUS=GLH_STATUS,GTL_WFREEEZED_EMP_ID=gtl_emp_id," .
			"GTL_WFREEZED_DOC=glh_approx_timetoclose, GTL_DOC=glh_approx_timetoclose, GTL_WFREEZED_POTENTIAL_AMT=glh_potential_amt, " .
			"GTL_WFREEZED_LEAD_TYPE=GLH_LEAD_TYPE, GTL_WFREEZED_TRACK_TYPE=GTL_TRACK_TYPE,GTL_WFREEZED_COLLECTIBLE=GLH_SERVICE_VALUE,  " .
			"GTL_WFREEZED_LAST_ACTIVITY_ID=GLD_ACTIVITY_ID  ".
			"where gtl_lead_code=glh_lead_code and gtl_month='$month' and gtl_year='$year' and gtl_emp_id='$emp_id' and gld_lead_code=glh_lead_code and GLD_CALL_STATUS='P' ";
	execute_my_query($query_update_on_3rd,'',true,false);
	$query_update_on_3rd=" update gft_order_hdr oh, gft_order_collection_dtl oc " .
			" set oc.GMOC_WFREEZED_MONTH='$month', oc.GMOC_WFREEZED_YEAR='$year' " .
			" WHERE oh.GOD_ORDER_STATUS='A' and oh.god_installed_value > oh.god_collection_realized " .
			" and oh.GOD_BALANCE_AMT>0 and oc.GMOC_COLLECTIBLE_AMT>0 " .
			" and oc.GMOC_ORDER_NO=oh.GOD_ORDER_NO " .
			" and oc.GMOC_AGREED='Y' and gmoc_current_status=1 ";
	execute_my_query($query_update_on_3rd,'',true,false);
	$financial_yr=get_current_financial_yr($month,$year);
	update_hot_leads($financial_yr,$month,$year,$emp_id,false);
	execute_my_query("update gft_lead_hdr lh " .
			" set glh_plan_no=(select count(gtl_lead_code) " .
			" from gft_track_lead_status where gtl_lead_code=glh_lead_code and GTL_WFREEZED_TRACK_TYPE=1 " .
			" group by glh_lead_code) where GLH_APPROX_TIMETOCLOSE between '$year-$month-01' and '$year-$month-$month_end' $sp_lead_hdr_check ");
	get_scorecard_plan_overall_freezed($month,$year,$emp_id);
}
?>
