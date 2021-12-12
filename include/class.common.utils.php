<?php
/*. require_module 'standard'; .*/
/*. require_module 'mysql'; .*/

require_once( __DIR__ . "/../dbcon.php");

class Common{
	private /*. string .*/ $roleid;
	protected /*. string .*/ $uid='';
	private $datas = /*. (string[string]) .*/ array();

	/**
	 * @return void
	 */
	function __construct(){
		$this->roleid =	isset($_SESSION['roleid'])?(string)$_SESSION["roleid"]:"0";
		$this->uid = isset($_SESSION["uid"])?(string)$_SESSION["uid"]:"0";
	}

	/**
	 * @param string $data
	 * 
	 * @return string
	 */
	public function CCRequestDataInjection($data){
		return mysqli_real_escape_string_wrapper($data);
	}

	/**
	 * @return int
	 */
	public function AffectedRows(){
		return mysqli_affected_rows_wrapper();
	}

	/**
	 * @return int
	 */
	public function LastInsertId(){
		return mysqli_insert_id_wrapper();
	}

/**
 * @param string $cust_emp
 *
 * @return string[int][string]
 */
	public function SAMComplementaryCouponPurpose($cust_emp){
		$datas = /*. (string[int][string]) .*/ array();
		$Sql_Purpose = "SELECT gcp_id,gcp_purpose FROM gft_complementary_coupon_purpose " .
				"WHERE gcp_status = 'A' AND gcp_cust_emp = '$cust_emp'";
		$Exe_Purpose = execute_my_query($Sql_Purpose);
		if($Exe_Purpose){
			if(mysqli_num_rows($Exe_Purpose)!=0){
				while($results = mysqli_fetch_assoc($Exe_Purpose)){
					$datas[] = $results;
				}
				mysqli_free_result($Exe_Purpose);
			}
		}else{
			exit("Error in running query :". mysqli_error_wrapper());
		}
		return $datas;
	}
/**
 * @param string $empstatus
 * 
 * @return string[int][string]
 */
	function get_emp_having_coupon_book($empstatus=''){
		$datas = /*. (string[int][string]) .*/ array();
		global $uid;
		$sql_get_emp	=	" select gem_emp_id,gem_emp_name from ( ".
								 " select a.gem_emp_id, a.gem_emp_name from gft_emp_master a ".
					 			 " left join gft_role_group_master rg on (grg_role_id=gem_role_id)  ".
								 " left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id) ".
								 " left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id) ".
								 " where gem_status='A' and (ggm_group_id in (66,106) or a.GEM_ROLE_ID=67 )   group by a.gem_emp_id ".
								 " union ". 
								 " select a.gem_emp_id, a.gem_emp_name from gft_emp_master a  where gem_status='A' and gem_role_id=36   group by a.gem_emp_id".
							" )  emps where gem_emp_id!=9999 and gem_emp_id!='$uid' order by gem_emp_name";
		$emp_list		 = execute_my_query($sql_get_emp);
		if($emp_list){
			if(mysqli_num_rows($emp_list)!=0){
				while($results = mysqli_fetch_assoc($emp_list)){
					$datas[] = $results;
				}
				mysqli_free_result($emp_list);
			}
		}else{
			exit("Error in running query :". mysqli_error_wrapper());
		}
		return $datas;
	}
	/**
	 * @return mixed[]
	 */
	public function SAMApprovalNameList(){
		$datas = array();
		$Sql_Approval = "select distinct(em.GEM_EMP_ID) emp_id,em.gem_emp_name emp_name from gft_emp_master em ".
		" left join  gft_emp_group_master eg on (eg.gem_emp_id=em.gem_emp_id)".
		" left join gft_emp_reporting  h1 on (h1.ger_reporting_empid=em.gem_emp_id and h1.ger_status='A' )" .
		" left join gft_emp_reporting  h2 on (h2.ger_reporting_empid=h1.ger_emp_id and h2.ger_status='A' )" .
		" left join gft_emp_reporting  h3 on (h3.ger_reporting_empid=h2.ger_emp_id and h3.ger_status='A' )" .
		" where (h1.ger_emp_id='$this->uid' or h2.ger_emp_id='$this->uid' or h3.ger_emp_id='$this->uid' or eg.gem_group_id=18 or em.GEM_EMP_ID=$this->uid) " .
		" and gem_status='A' and gem_email!='' ";
		$Exe_Approval = execute_my_query($Sql_Approval);
		if($Exe_Approval){
			if(mysqli_num_rows($Exe_Approval)!=0){
				while($results = mysqli_fetch_assoc($Exe_Approval)){
					$datas[] = $results;
				}
			}
		}else{
			exit("Error in running query :". mysqli_error_wrapper());
		}
		mysqli_free_result($Exe_Approval);
		return $datas;
	}

	/**
	 * @return string[string]
	 */
	public function SAMCouponList(){
		$datas = /*. (string[string]) .*/ array();
		$Sql_Coupon = "SELECT GCD_COUPON_NO " .
		"FROM gft_coupon_book_master bm join gft_coupon_distribution_dtl on (GCD_BOOK_NO=GCB_NO and GCD_DISTRIBUTE_FOR='N') " .
		"WHERE bm.GCB_TO_EMPLOYEE=$this->uid AND GCD_ORDER_NO='' ";
		$Exe_Coupon = execute_my_query($Sql_Coupon);
		if($Exe_Coupon){
			if(mysqli_num_rows($Exe_Coupon)!=0){
				while($results = mysqli_fetch_assoc($Exe_Coupon)){
					$datas[$results['GCD_COUPON_NO']] = $results['GCD_COUPON_NO'];
				}
			}
		}else{
			exit("Error in running query :". mysqli_error_wrapper());
		}
		mysqli_free_result($Exe_Coupon);
		return $datas;
	}

	/**
	 * @return string[string]
	 */
	public function SAMEmpDepartment(){
		$datas = /*. (string[string]) .*/ array();
		$Sql_Dept = "SELECT ggm_group_id,ggm_group_name FROM gft_group_master " .
		"where ggm_group_id in (3,20,56,70,83,84,85,88,89,91,95)";
		$Exe_Dept = execute_my_query($Sql_Dept);
		if($Exe_Dept){
			if(mysqli_num_rows($Exe_Dept)){
				while($results = mysqli_fetch_assoc($Exe_Dept)){
					$datas[$results['ggm_group_id']] = $results['ggm_group_name'];
				}
			}
		}else{
			exit("Error in running query :". mysqli_error_wrapper()); 
		}
		mysqli_free_result($Exe_Dept);
		return $datas;
	}
/**
 * @param int $uid
 * 
 * @return string[int][string]
 */
	function SAMRequestEmpList($uid){
		$datas = /*. (string[int][string]) .*/ array();
		$sql_get_emp	=	" select gcr_id, concat(gem_emp_name,'-',gcp_purpose) AS empnamelist from  gft_complementary_coupon_request ".
							" inner join gft_emp_master on(gcr_emp_id=gem_emp_id) ".
							" join gft_complementary_coupon_purpose on (gcp_id = gcr_purpose_id and gcp_status = 'A') " .
							" where gcr_request_to=$uid AND gcr_request_status!='I' AND gcr_coupon_purpose=1";
		$emp_list		 = execute_my_query($sql_get_emp);
		if($emp_list){
			if(mysqli_num_rows($emp_list)!=0){
				while($results = mysqli_fetch_assoc($emp_list)){
					$datas[] = $results;
				}
				mysqli_free_result($emp_list);
			}
		}else{
			exit("Error in running query :". mysqli_error_wrapper());
		}
		return $datas;
	}
	/**
	 * 
	 * @return string[int][string]
	 */
	function SAMAllEmpList(){
		$datas = /*. (string[int][string]) .*/ array();
		global $uid;
		$sql_get_emp	=	" select gem_emp_id, gem_emp_name from gft_emp_master where gem_status='A' AND gem_emp_id!=$uid and gem_emp_id<7000  order by gem_emp_name ";
				
		$emp_list		 = execute_my_query($sql_get_emp);
		if($emp_list){
			if(mysqli_num_rows($emp_list)!=0){
				while($results = mysqli_fetch_assoc($emp_list)){
					$datas[] = $results;
				}
				mysqli_free_result($emp_list);
			}
		}else{
			exit("Error in running query :". mysqli_error_wrapper());
		}
		return $datas;
	}
	
	/**
	 * @param int  $group_code
	 * @param string $emp_status
	 *
	 * @return string[string]
	 */
	public function SAMEmpListGroupFilter($group_code,$emp_status='A'){
		$datas = /*. (string[string]) .*/ array();
		//if(is_array($group_id_arr)){ $group_code=implode(',',$group_id_arr);}
		//else $group_code=$group_id_arr;

		$without_dummy=" a.gem_emp_id!= ".$this->uid;
		$Sql_Group="select distinct(a.gem_emp_id) eid,gem_emp_name,gem_mobile from gft_emp_master a" .
				" left join gft_role_group_master rg on (grg_role_id=gem_role_id)" .
				" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id)" .
				" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id)" .
				" where $without_dummy  ".($emp_status!=''?" and a.gem_status='A'":"");
		if($group_code!=0){ $Sql_Group.=" and ggm_group_id in ($group_code) "; }
		$Exe_Group = execute_my_query($Sql_Group);
		if($Exe_Group){
			if(mysqli_num_rows($Exe_Group)!=0){
				while($results = mysqli_fetch_assoc($Exe_Group)){
					$datas[$results['eid']] = $results['gem_emp_name'];
				}
			}
		}else{
			exit("Error in running query :". mysqli_error_wrapper());
		}
		mysqli_free_result($Exe_Group);
		return $datas;
	}
	/**
	 * @return mixed
	 */
	public function getEmpReportMgrInfo(){
		$datas = array();
		$Sql_EmpDetails = "select DISTINCT em.GEM_EMP_ID ,em.GEM_DEPARTMENT,em.GEM_TITLE,em.GEM_EMP_NAME," .
		"e.GEM_EMP_NAME 'rep_name',e.GEM_EMP_Id as 'rep_id',em.GEM_DOJ,em.GEM_DOR," .
		"datediff(if(em.GEM_STATUS='A',date(now()),em.GEM_DOR ),em.GEM_DOJ) servicein,lt.gelt_cl,lt.gelt_sl,lt.gelt_el " .
		"from gft_emp_master em " .
		"left join gft_emp_group_master gm on (gm.gem_emp_id=em.gem_emp_id) " .
		"left join gft_emp_reporting d on d.GER_EMP_ID=em.GEM_EMP_ID and d.GER_STATUS='A' " .
		"left join gft_emp_master e on e.GEM_EMP_ID=d.GER_REPORTING_EMPID " .
		"left join gft_emp_leave_type lt on em.gem_emp_id = lt.gelt_emp_id " .
		"where em.GEM_OFFICE_EMPID!=0 and em.gem_status='A' and em.gem_emp_id = '".$this->uid."'";
		$Exe_EmpDetails = execute_my_query($Sql_EmpDetails);
		try{
			if($Exe_EmpDetails){
				if(mysqli_num_rows($Exe_EmpDetails)>0){
					$datas = mysqli_fetch_assoc($Exe_EmpDetails);
				}
			}
		}catch(Exception $e){
			exit($e->getMessage());
		}
		mysqli_free_result($Exe_EmpDetails);
		return $datas;
	}

	/**
	 * @param string $from
	 * @param string $to
	 *
	 * @return int
	 */
	public function getDateDiffDays($from,$to){
		$from=date('d-m-Y',strtotime($from));
		$to=date('d-m-Y',strtotime($to));
		$nodays=(int)  ((strtotime($to) - strtotime($from))/ (60 * 60 * 24)); //it will count no. of days
		$nodays=$nodays+1;
		return $nodays;
	}

	/**
	 * @param string $from
	 * @param string $to
	 *
	 * @return int
	 */
	public function getCountSunday($from,$to){
		$from=date('d-m-Y',strtotime($from));
		$to=date('d-m-Y',strtotime($to));
		$cnt=0;
		$nodays=(int) ((strtotime($to) - strtotime($from))/ (60 * 60 * 24)); //it will count no. of days
		$nodays=$nodays+1; 
	    for($i=0;$i<$nodays;$i++){       
	     $p=0;
	     $tarr = explode("-",$from);
	     $fd=(int)$tarr[0];
	     $fm=(int)$tarr[1];
	     $fy=(int)$tarr[2];

	     $datetime = strtotime("$fd-$fm-$fy");            
	     if($i==0){
	     	$nextday = date('d-m-Y',strtotime("0 day", $datetime));  //this will add one day in from date (from date + 1)
	        //$fd = (string)$fd;
	        $p=(int)date('w',mktime(0, 0, 0, $fm  ,$fd , $fy));
	     }else{
	    	$nextday = date('d-m-Y',strtotime("+1 day", $datetime));  //this will add one day in from date (from date + 1)
		$tarr = explode("-",$nextday);
		//$nd = (string)$nd;
		$nd=(int)$tarr[0];
		$nm=(int)$tarr[1];
		$ny=(int)$tarr[2];

	        $p=(int)date('w',mktime(0, 0, 0, $nm  ,$nd , $ny));
	     }
	     if($p==0)            // check whethere value is 0 then its sunday
	         $cnt++;          //count variable of sunday                        
	      $from=$nextday;          
	      $p++;            
	     }             
	   return $cnt;
	}
	
	/**
	 * @return string[string]
	 */
	function getDateList(){
		$Sql_EmpRequest = "SELECT gelr_emp_id,date_format(gelr_from_date, '%d-%m-%Y') as gelr_from_date," .
		"gelr_to_date,datediff(gelr_to_date,gelr_from_date) as days " .
		"FROM gft_emp_leave_request " .
		"join  gft_emp_leave_req_status on (gelr_id = gels_lr_id and gels_last_audit = 'Y') " .
		"where gelr_emp_id = '".$this->uid."' and gels_status not in (31,32)";
		$Exe_EmpRequest = execute_my_query($Sql_EmpRequest);

$fromdate='';

		while($results = mysqli_fetch_assoc($Exe_EmpRequest)){
			if($results['gelr_from_date']!=''){
				$nextday = '';
				for($i=0;$i<=(int)$results['days'];$i++){
					if($nextday!=''){
						$fromdate = $nextday;
					}
					if($i == 0){
						$fromdate = $results['gelr_from_date'];
						$datetime = strtotime($fromdate);
						$nextday = $fromdate;
					}else{
						$datetime = strtotime($fromdate);
						$nextday = date('d-m-Y',strtotime("+1 day", $datetime));
					}
					$fromdate = $nextday;
					$this->datas[(string)str_replace('-','',$nextday)] = $nextday;
				}
			}
		}
		return $this->datas;
	}
	/**
	 * @return string[string]
	 */
	public function getHoliday(){
		$year = date("Y");
		$Sql_Holidays = "SELECT date_format(ghl_date,'%d-%m-%Y') as dates FROM gft_holiday_list g where ghl_date like '$year%'";
		$Exe_Holidays = execute_my_query($Sql_Holidays);
		while($results = mysqli_fetch_assoc($Exe_Holidays)){
				$this->datas[(string)str_replace('-','',$results['dates'])] = $results['dates'];
		}
		$this->getDateList();
		return $this->datas;
	}
	
	/**
	 * @param string[] $ReqField
	 * @param boolean $is_cancel
	 * 
	 * @return int[string]
	 */
	public function getEmpLeaveType($ReqField,$is_cancel=false){
		//extract($ReqField);
		$fromDate=$ReqField['fromDate'];
		$toDate=$ReqField['toDate'];
		$halfDay=$ReqField['halfDay'];
		$fieldName=$ReqField['fieldName'];
		$emp_id = isset($ReqField['emp_id'])?$ReqField['emp_id']:$this->uid;
		$getDays=0;

		$numberDays = $this->getDateDiffDays($fromDate,$toDate); // getting no. of days
		$sundayCount = $this->getCountSunday($fromDate,$toDate); // getting sunday counts
		$Sql_Holidays = "SELECT count(*) as 'NoOfHolidays' FROM gft_holiday_list g where ghl_date between '$fromDate' and '$toDate'";
		$Exe_Holidays = execute_my_query($Sql_Holidays);
		$holidays = mysqli_result($Exe_Holidays,0,"NoOfHolidays");
		if($fieldName!='gelt_permissions') {
			if($halfDay == 'false'){
				if(in_array($fieldName,array('gelt_el','gelt_cl','gelt_csl',"gelt_ml_taken","gelt_pl_taken","gelt_od_taken","gelt_ib_taken","gelt_pr_taken"))){
					$getDays = ($numberDays-$sundayCount-$holidays);
				}else if(in_array($fieldName,array('gelt_sl','gelt_pl'))) {
					$getDays = $numberDays;
				}
			}else{
				if(in_array($fieldName,array('gelt_sl'))) {
					$getDays = 1;
				}else{
					$getDays = 0.5;
				}
			}
		} else {
			$getDays = 1;
		}
		$datas['getDays'] = $getDays;
		$Sql_LeaveType = "SELECT $fieldName FROM gft_emp_leave_type WHERE gelt_emp_id = '$emp_id'";
		$Exe_LeaveType = execute_my_query($Sql_LeaveType);
		try{
			if(mysqli_num_rows($Exe_LeaveType)==0) {
				$datas['totalDays'] = 0;
				$datas['remainingDays'] = 0;
			} else if($Exe_LeaveType){
				$datas['totalDays'] = (float)mysqli_result($Exe_LeaveType,0,$fieldName);
				$datas['remainingDays'] = (float)mysqli_result($Exe_LeaveType,0,$fieldName)-$getDays; // In case of permission cancel it is subtracted (taken count)
				if(($is_cancel and in_array($fieldName,array('gelt_cl','gelt_sl','gelt_csl','gelt_pl'))) or (!$is_cancel and in_array($fieldName,array("gelt_ml_taken","gelt_pl_taken","gelt_od_taken","gelt_ib_taken","gelt_pr_taken")))) { 
					$datas['remainingDays'] = (float)mysqli_result($Exe_LeaveType,0,$fieldName)+$getDays;
				}
			}
		}catch(Exception $e){
			exit($e->getMessage());
		}
		mysqli_free_result($Exe_Holidays);
		mysqli_free_result($Exe_LeaveType);
		return $datas;
	}

	/**
	 * @param string $deptid
	 * 
	 * @return string[string]
	 */
	public function SAMDeptWiseEmp($deptid){
                $datas = /*. (string[string]) .*/ array();
                $deptid = (is_array($deptid)?$deptid['deptid']:$deptid);
                $Sql_Dept_Emp = "select em.gem_emp_id,em.gem_emp_name,ggm_group_name from gft_emp_master em " .
                "left join gft_role_group_master rg on (grg_role_id=gem_role_id) " .
                "left join gft_emp_group_master rg1 on (rg1.gem_emp_id=em.gem_emp_id)" .
                "left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id)   " .
                "left join gft_emp_manager_relation on(gmr_emp_id=em.gem_emp_id) AND em.GEM_EMP_ID < 7000   " .
                "where em.gem_status='A' and ggm_group_id in ($deptid)  group by gem_emp_id order by em.gem_emp_name";
                $Exe_Dept_Emp = execute_my_query($Sql_Dept_Emp);
                if($Exe_Dept_Emp){
                        if(mysqli_num_rows($Exe_Dept_Emp)){
                                while($results = mysqli_fetch_assoc($Exe_Dept_Emp)){
                                        $datas[$results['gem_emp_id']] = $results['gem_emp_name'];
                                }
                        }
                }else{
                        exit("Error in running query :". mysqli_error_wrapper());
                }
                mysqli_free_result($Exe_Dept_Emp);
                return $datas;
        }

}
/**
 * @param string $employee_id
 * @param string $empstatus
 * @param string $format
 *
 * @return string[int][string]
 */
function get_emp_having_coupon_book($employee_id,$empstatus='', $format=''){
	$datas = /*. (string[int][string]) .*/ array();
	$json_datas = /*. (string[string][string]) .*/ array();
	$sql_get_emp	=	" select gem_emp_id,gem_emp_name from ( ".
			" select a.gem_emp_id, a.gem_emp_name from gft_emp_master a ".
			" left join gft_role_group_master rg on (grg_role_id=gem_role_id)  ".
			" left join gft_emp_group_master rg1 on (rg1.gem_emp_id=a.gem_emp_id) ".
			" left join gft_group_master g1 on (g1.ggm_group_id=rg.grg_group_id or g1.ggm_group_id=rg1.gem_group_id) ".
			" where gem_status='A' and ggm_group_id in (66,106,36,54,155)   group by a.gem_emp_id ".
			" union ".
			" select a.gem_emp_id, a.gem_emp_name from gft_emp_master a  where gem_status='A' and gem_role_id in(36,31,89,34)   group by a.gem_emp_id".
			" )  emps where gem_emp_id!=9999 and gem_emp_id!='$employee_id' order by gem_emp_name";
	$emp_list		 = execute_my_query($sql_get_emp);
	if($emp_list){
		if(mysqli_num_rows($emp_list)!=0){
			while($results = mysqli_fetch_assoc($emp_list)){
				$datas[] = $results;
				$json_datas[] = array("id"=>$results['gem_emp_id'],"name"=>$results['gem_emp_name']);
			}
			mysqli_free_result($emp_list);
		}
	}else{
		exit("Error in running query :". mysqli_error_wrapper());
	}

	if($format == 'json') {
		return $json_datas;
	}
	return $datas;
}
//main
if(isset($_REQUEST['callback'])){
	//$common = "Common";
	$common = new Common();
	$callback = (string)$_REQUEST['callback'];
	try {
		echo json_encode(call_user_func_array(array($common, $callback), array($_REQUEST))); // getting employee departmentwise employee list
	} catch(Exception $e) {
		die($e);
	}
}
?>
