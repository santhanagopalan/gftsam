<?php
require_once(__DIR__ ."/dbcon.php");

/**
 * @param string $date_c
 * 
 * @return string
 */
function employee_selection_query($date_c){
	$sql="SELECT em.GEM_EMP_ID,em.GEM_EMP_NAME,em.GEM_ROLE_ID,em.GEM_EMAIL " .
		" FROM gft_emp_master em where em.GEM_EMP_ID!=".SALES_DUMMY_ID." and em.gem_status !='I' and gem_office_empid!='0'" .
		" and (em.gem_role_id<=7 or em.gem_role_id=11) and GEM_DOJ < DATE_SUB('$date_c',INTERVAL 7 DAY) ";
	return $sql;
}

/**
 * @param string $emp_id
 * @param boolean $reporting_masters
 * 
 * @return string
 */
function get_reminder_cc($emp_id,$reporting_masters=true){
	$attention_to_rep=get_email_addr_reportingmaster($emp_id,$reporting_masters,explode(",", EMP_IDS_TO_SKIP_REPORTING_MAIL));
	$cc="";
	if(count($attention_to_rep)>0){
		$ca_to=implode(',',$attention_to_rep); 
		$cc=$ca_to;
	}
	return $cc;
}

/**
 * @return void
 */
function outstanding_reminder(){
	global $attach_path;
	$uploadDir="$attach_path/outstanding";
	if(!file_exists($uploadDir)){
		try{
			mkdir("$uploadDir", 0777);
		}catch(ErrorException $e){
			error_log("Exception ". $e->__toString());
		}
	}
	$date_c = date('Y-m-d');
	// after 7 days from DOJ
	$sql3=employee_selection_query($date_c);
	$rs3=execute_my_query($sql3);
	$email_from=get_samee_const("ADMIN_TEAM_MAIL_ID");
	$reply_to=array(get_samee_const('AC_MAIL_ID'));
	while($row3=mysqli_fetch_array($rs3)){
	$emp_id=$row3[0];
	$emp_name=$row3[1];
	$email_to=$row3[3];
	if(trim($email_to)=="") {
		continue;
	}
		$cc=get_reminder_cc($emp_id);
		$purpose_of_mail=$subject="Oustanding Reminder";
		$msg=" Please find the attachment ";
		$filename="$uploadDir/outstand_".$emp_id."_".$date_c.".csv";
		$sql_report="select GEM_EMP_NAME,GOD_ORDER_NO,GOD_ORDER_DATE,GLH_CUST_NAME,GLH_AUTHORITY_NAME," .
				" CONCAT(GLH_CUST_STREETADDR1,'##',GLH_CUST_STREETADDR2,'##',GLH_CUST_CITY,'##',GLH_CUST_PINCODE) address," .
				" round(GOD_ORDER_AMT,0) GOD_ORDER_AMT,round(collected_amt,0) collected_amt," .
				" (ifnull(round(GOD_ORDER_AMT,0),0)-ifnull(round(collected_amt,0),0)) outst ,lh.GLH_LEAD_CODE,lh.GLH_TERRITORY_ID," .
				" lh.GLH_CUST_BUSSPHNO,em.gem_emp_id,em.gem_mobile" .
				" from (gft_emp_master em,gft_order_hdr oh,gft_lead_hdr lh )" .
				" left join gft_emp_reporting rp on ( rp.GER_EMP_ID=god_emp_id)" .
				" left join (select sum(GCR_AMOUNT) as collected_amt,gcr_order_no from " .
				" gft_collection_receipt_dtl,gft_receipt_dtl where gcr_receipt_id=grd_receipt_id" .
				" and GRD_STATUS not in ('WE','CB','N','R') and gcr_reason='1' and GRD_REFUND_AMT=0 " .
				" group by gcr_order_no ) c on (c.gcr_order_no=god_order_no)" .
				" left join gft_order_collection_dtl oci_dtl on (oci_dtl.gmoc_order_no=god_order_no)" .
				" left join gft_order_collection_dtl_by_accounts oci_ac on(oci_ac.gmoc_order_no=god_order_no)" .
				" left join gft_emp_reporting h1 on (h1.ger_emp_id=em.gem_emp_id and h1.ger_status='A' )" .
				" left join gft_emp_reporting h2 on (h1.ger_reporting_empid=h2.ger_emp_id and h2.ger_status='A')" .
				" left join gft_emp_reporting h3 on (h2.ger_reporting_empid=h3.ger_emp_id and h3.ger_status='A')" .
				" where em.gem_emp_id=god_emp_id and god_order_status='A' and god_order_no not like '%A%' and" .
				" (h3.ger_reporting_empid='$emp_id' or h2.ger_reporting_empid='$emp_id' or h1.ger_reporting_empid='$emp_id' or em.gem_emp_id='$emp_id')" .
				" and god_order_date >='2004-01-01' and god_order_date<='$date_c'" .
				" and oh.god_lead_code=GLH_LEAD_CODE" .
				" group by god_order_no having  outst>0 order by em.gem_emp_name,god_order_date ";
		$file_content=" Employee , Order No , Ordered Date , Customer Name , Authority , Address , Order Amount , Collection Amount, Outstanding Amount \r\n";
		//print " $sql_report <br> ";
		$rs_sql_report=execute_my_query($sql_report);
		if(mysqli_num_rows($rs_sql_report)>0){
			while($row5=mysqli_fetch_array($rs_sql_report)){
				$Employee=$row5[0];
				$OrderNo=$row5[1];
				$OrderedDate=$row5[2];
				$Customer_Name=$row5[3];
				$Authority=$row5[4];
				$Address=$row5[5];
				$Order_Amount=$row5[6];
				$Collection_Amount=$row5[7];
				$Outstanding_Amount=$row5[8];
				$leadcode=$row5[9];
				$terr_id=$row5[10];
				$cust_phone=$row5[11];
				$cust_id="$leadcode-$terr_id";
				$emp_id=$row5[12];
				$emp_mobile=$row5[13];
$file_content.=<<<END
$Employee, '$OrderNo' , $OrderedDate , $Customer_Name , $Authority , $Address , $Order_Amount , $Collection_Amount , $Outstanding_Amount \r\n
END;
			}
			$fp=fopen("$filename","w");
			if($fp){
				fwrite($fp,$file_content,strlen($file_content));
				fclose($fp);
			}else{
				print " file couldn't create $filename";
			}
			$at_file[0]="$filename";
			$rs=send_mail_function($email_from,$email_to,$subject,$msg,$at_file,$cc,$subject,true,$reply_to);
		}
	}
}

/**
 * @return void
 */
function sales_planning_reminder(){
	global $attach_path;
	$uploadDir="$attach_path/salesplanning";
	if(!file_exists($uploadDir)){
		mkdir("$uploadDir", 0777);
	}
	$date_c = date('Y-m-d');
	// after 7 days from DOJ
	$sql3=employee_selection_query($date_c);
	$rs3=execute_my_query($sql3);
	$email_from=get_samee_const("ADMIN_TEAM_MAIL_ID");
	$reply_to=array(get_samee_const("ADMIN_TEAM_MAIL_ID"));
	while($row3=mysqli_fetch_array($rs3)){
		$emp_id=$row3[0];
		$emp_name=$row3[1];
		$email_to=$row3[3];
		if(trim($email_to)=="") continue;
		$cc=get_reminder_cc($emp_id);
		$purpose_of_mail=$subject="Sales planning  Reminder";
		$msg=" Please find the attachment ";
		$filename="$uploadDir/salesplanning_".$emp_id."_".$date_c.".csv";
$sql_report=<<<END
select em.GEM_EMP_NAME,b.GLH_CUST_NAME,DATE_FORMAT(b.GLH_DATE,'%d-%m-%Y'),
 DATE_FORMAT(ac.GLD_VISIT_DATE,'%d-%m-%Y'),b.glh_visit_count , 
 group_CONCAT(DISTINCT(fm.GPM_PRODUCT_ABR) separator ' '),
 v.GTM_VERTICAL_NAME,e.GEM_EMP_name,DATE_FORMAT(ac.GLD_NEXT_ACTION_DATE,'%d-%m-%Y'), T2.GAM_ACTIVITY_ID,if(em.gem_status='I','Relieved','Active'),
 b.glh_authority_name, concat(b.glh_cust_streetaddr1,'##',b.glh_cust_streetaddr2,'##',b.glh_cust_city,'##',b.glh_cust_statecode,'##',b.glh_cust_pincode) address, 
 DATE_FORMAT(ac.GLD_APPORX_TIMETOCLOSE,'%d-%m-%Y'),T2.GAM_ACTIVITY_DESC,GLD_NOTE_ON_ACTIVITY, 
 gcsm.GCS_NAME 'Status',b.GLH_POTENTIAL_AMT,ifnull( max(ifnull(forcollection.outstanding_amt,0)),0 ) outstanding_amt  
from gft_lead_hdr b left join gft_activity ac on (b.GLH_LEAD_CODE=ac.gld_lead_code ) 
inner join gft_emp_master em on ( ac.gld_emp_id=em.gem_emp_id ) 
inner join gft_emp_master r 
left join ( 
select god_lead_code,sum(ifnull(collected_amt,0)) collected_amt,
sum(ifnull(GOD_ORDER_AMT,0)) ordered_amt, 
( ifnull(sum(ifnull(GOD_ORDER_AMT,0)),0) - ifnull(sum(ifnull(collected_amt,0)),0) ) outstanding_amt,
god_emp_id from gft_order_hdr oh 
left join ( select ifnull(sum(ifnull(GCR_AMOUNT,0)),0) collected_amt,
gcr_order_no from gft_collection_receipt_dtl, gft_receipt_dtl 
where gcr_receipt_id=grd_receipt_id and GRD_STATUS not in ('WE','CB','N','R')
 and gcr_reason='1' and GRD_REFUND_AMT=0 group by gcr_order_no 
 ) coll on (god_order_no=gcr_order_no) 
 where god_order_status='A' and god_order_no not like '%A%' group by god_lead_code,god_emp_id ) forcollection 
 on (forcollection.god_lead_code=b.GLH_LEAD_CODE and forcollection.god_emp_id=em.gem_emp_id) 
 left join gft_customer_status_master gcsm on (gcsm.GCS_CODE=b.GLH_STATUS ) 
 left join (gft_vertical_master v  on (v.GTM_VERTICAL_CODE = GLH_VERTICAL_CODE) 
 left join gft_emp_master e on (b.GLH_REFERENCE_INTERNAL=e.GEM_EMP_ID) 
 left join gft_activity_master T2 on (T2.GAM_ACTIVITY_ID=ac.GLD_NEXT_ACTION) 
 left join (gft_lead_product_dtl lp join gft_product_family_master fm on(fm.GPM_PRODUCT_CODE= lp.GLC_PRODUCT_CODE)) 
 on(lp.GLC_LEAD_CODE=ac.GLD_LEAD_CODE ) 
 left join gft_emp_reporting h1 on (h1.ger_emp_id=em.gem_emp_id and h1.ger_status='A' ) 
 left join gft_emp_reporting h2 on (h1.ger_reporting_empid=h2.ger_emp_id and h2.ger_status='A') 
 left join gft_emp_reporting h3 on (h2.ger_reporting_empid=h3.ger_emp_id and h3.ger_status='A') 
 where h1.GER_REPORTING_EMPID=r.GEM_EMP_ID and ( gld_call_status='P' ) AND 
 ac.GLD_APPORX_TIMETOCLOSE between '2004-04-01' and  '$date_c' 
 and (h3.ger_reporting_empid=$emp_id or h2.ger_reporting_empid=$emp_id or h1.ger_reporting_empid=$emp_id or em.gem_emp_id=$emp_id) 
 group by ac.GLD_LEAD_CODE,em.gem_emp_id ORDER BY em.GEM_EMP_NAME,ac.GLD_NEXT_ACTION_DATE desc
END;
$file_content=<<<END
Employee Name , Shop Name , First Visit , Last Visit , Total no. of visits ,  Products , Vertical , Refference , Next Action Date , Activity , Employee Status , Authority , Address , Time to close Order , Activity Desc , Activity Note , Status , Potential Amount , Outstanding \r\n
END;
//print " $sql_report <br> ";
		$rs_sql_report=execute_my_query($sql_report);
		if(mysqli_num_rows($rs_sql_report)>0){
			while($row5=mysqli_fetch_array($rs_sql_report)){
				$file_content.=<<<END
$row5[0] , $row5[1] , $row5[2] , $row5[3] , $row5[4] , $row5[5] , $row5[6] , $row5[7] , $row5[8] , $row5[9] , $row5[10]  , $row5[11] , $row5[12] , $row5[13] , $row5[14] , $row5[15] , $row5[16] , $row5[17] , $row5[18]  \r\n
END;
			}
			$fp=fopen("$filename","w");
			if($fp){
				fwrite($fp,$file_content,strlen($file_content));
				fclose($fp);
			}else{
				print " file couldn't create $filename";
			}
			$at_file[0]="$filename";
			$rs=send_mail_function($email_from,$email_to,$subject,$msg,$at_file,$cc,$subject,true,$reply_to);
		}
	}
}

/**
 * @param int $max_notreported
 * @param int $max_leave
 * 
 * @return void
 */
function activity_reminder($max_notreported,$max_leave){
	//date range for holiday
	if($max_leave==0){
		$max_leave=20;
	}

	$holidays_list="";
	$date_c = date('Y-m-d');
	$sql1="SELECT ifnull(group_concat(CONCAT(\"'\",GHL_DATE,\"'\")),'0') FROM gft_holiday_list where GHL_OPTIONAL!='Y' AND GHL_DATE between DATE_SUB('$date_c',INTERVAL $max_leave DAY)  and  '$date_c'  ";
	$rs1=execute_my_query($sql1);
	if($row1=mysqli_fetch_array($rs1)){
		$holidays_list=trim($row1[0]);
		if($holidays_list==""){
			$holidays_no=0;
		}
	}
	$dc=1;
	$str=array();
	$cdate=0;
	$str_date="";
	$date_list=array();
	while($cdate<$max_notreported and $dc<$max_leave){
		$sql2="select if( (DAYOFWEEK(DATE_SUB('$date_c',INTERVAL $dc DAY))=1) or ( DATE_SUB('$date_c',INTERVAL $dc DAY) in ($holidays_list)),0,DATE_SUB('$date_c',INTERVAL $dc DAY))";
		$rs2=execute_my_query($sql2);
		if($row2=mysqli_fetch_array($rs2)){
			$t1=$row2[0];
			if($t1){
				$str[$cdate]=" GLD_VISIT_DATE='$t1' ";
				$date_list[$cdate]=" $t1 ";
				$str_date=(string)$cdate;
				$cdate++;
			}
		}
		$dc++;
	}
	if(count($str)>0){
		$sortbycol=(isset($_GET['sortbycol'])?(string)$_GET['sortbycol']:"");
		$sorttype=(isset($_GET['sorttype'])?(string)$_GET['sorttype']:"");
		$str_date=implode(" or ",$str);
		$exclude_employee_ids = SALES_DUMMY_ID.",7044";
		$sql3="SELECT em.GEM_EMP_ID,em.GEM_EMP_NAME,em.GEM_ROLE_ID,em.GEM_EMAIL " .
				" FROM gft_emp_master  as em left join  (SELECT  distinct GLD_EMP_ID FROM  gft_activity where  ($str_date) ) as B" .
				" on (B.GLD_EMP_ID=em.GEM_EMP_ID ) where B.GLD_EMP_ID is null and em.gem_status !='I' and gem_office_empid!='0'" .
				" and (em.gem_role_id<=7 or em.gem_role_id=11) and GEM_DOJ < DATE_SUB('$date_c',INTERVAL $max_notreported DAY) " .
				" and em.GEM_EMP_ID not in ($exclude_employee_ids) ";
		$rs3=execute_my_query($sql3);
		//print "$sql3 <br>";
		$lc=1;
		$email_from=get_samee_const("ADMIN_TEAM_MAIL_ID");
		$reply_to=array(get_samee_const("ADMIN_TEAM_MAIL_ID"));
		while($row3=mysqli_fetch_array($rs3)){
			$emp_id=$row3[0];
			$emp_name=$row3[1];
			$email_to=$row3[3];
			if(trim($email_to)=="") {
				continue;
			}
			$cc=get_reminder_cc($emp_id);
			$subject="Activity Details Not Reported";
			$purpose_of_mail="Activity Details Not Reported";
			/* plz dont change the subject 
			 if you want change "subject",you should change "subject" in 
			  missed_visit_report.php and send_mail_to_other.php	
			*/
			if($max_notreported==7){
			   $db_sms_content_config=array(
			   'Employee_Name'=>array($emp_name));
    	       $message=get_formatted_mail_content($db_sms_content_config,$category=1,$mail_template_id=44);
	           $body_message=$message['content'];
	           $content_type=$message['content_type'];
	           $subject=$message['Subject'];
	           $at_file=$message['Attachment'];
	           $rs= send_mail_function($email_from,$email_to,$subject,$body_message,$at_file,null,1,$content_type,$reply_to,false,false,$fromname=null);
			}else{
			   $abandoned_date=implode(" and ",$date_list);
			   $db_sms_content_config=array(
			   'abandoned_date'=>array($abandoned_date),'Employee_Name'=>array($emp_name));
    	        $message=get_formatted_mail_content($db_sms_content_config,$category=1,$mail_template_id=45);
	            $body_message=$message['content'];
	            $content_type=$message['content_type'];
	            $subject=$message['Subject'];
	            $at_file=$message['Attachment'];
	            $rs= send_mail_function($email_from,$email_to,$subject,$body_message,$at_file,$cc,1,$content_type,$reply_to,false,false,$fromname=null);
			 }
			$lc++;
		}
	}
}

/**
 * @return void
 */
function pending_installation_reminder(){
	global $attach_path;
	$uploadDir="$attach_path/pending_installation";
	if(!file_exists($uploadDir)){
		mkdir("$uploadDir", 0777);
	}
	$date_c = date('Y-m-d');
	// after 7 days from DOJ
	$sql3=employee_selection_query($date_c);
	$rs3=execute_my_query($sql3);
	$email_from=get_samee_const("ADMIN_TEAM_MAIL_ID");
	$reply_to=array(get_samee_const("ADMIN_TEAM_MAIL_ID"));
	while($row3=mysqli_fetch_array($rs3)){
		$emp_id=$row3[0];
		$emp_name=$row3[1];
		$email_to=$row3[3];
		if(trim($email_to)=="") continue;
		$cc=get_reminder_cc($emp_id);
		$purpose_of_mail=$subject="Pending Installation  Reminder";
		$msg=" Please find the attachment ";
		$filename="$uploadDir/pending_installation_".$emp_id."_".$date_c.".csv";
		$sql_report=<<<END
select GLH_CUST_NAME,concat(GLH_CUST_STREETADDR1,'##',GLH_CUST_STREETADDR2,'##',GLH_CUST_CITY,'##',GLH_CUST_PINCODE),
 GOD_ORDER_NO,GOD_ORDER_DATE,gpm_product_name,(gop_qty-gop_usedqty),round((gop_sell_rate*(gop_qty-gop_usedqty)),0),
 concat(pfm.gpm_product_code,'-',opd.gop_product_skew),em.gem_emp_name, pm.GPM_CLIENTS
  from  
 (gft_order_hdr,gft_lead_hdr,gft_order_product_dtl opd , gft_product_family_master pfm,
 gft_emp_master em,gft_product_master pm) 
 left join gft_emp_reporting h1 on (h1.ger_emp_id=em.gem_emp_id and h1.ger_status='A' ) 
 left join gft_emp_reporting h2 on (h1.ger_reporting_empid=h2.ger_emp_id and h2.ger_status='A') 
 left join gft_emp_reporting h3 on (h2.ger_reporting_empid=h3.ger_emp_id and h3.ger_status='A') 
 where god_order_no=gop_order_no and gop_product_code=pfm.gpm_product_code and  
pm.gpm_product_code=pfm.gpm_product_code and pm.gpm_product_skew=opd.gop_product_skew  
and GOD_LEAD_code=GLH_LEAD_CODE and GOP_QTY > GOP_USEDQTY 
and god_order_status='A' and em.gem_emp_id=god_emp_id and (GFT_SKEW_PROPERTY='1' or GFT_SKEW_PROPERTY='3') 
and (h3.ger_reporting_empid='$emp_id' or h2.ger_reporting_empid='$emp_id' or h1.ger_reporting_empid='$emp_id' or em.gem_emp_id='$emp_id') 
and GOD_ORDER_DATE between '2004-04-01' and  '$date_c' 
group by god_order_no,gop_product_code,gop_product_skew ORDER BY em.gem_emp_name,GOD_ORDER_DATE desc 
END;
		$file_content=<<<END
 Shop Name , Address , Order no ,  Order Date , Product Name , Ordered Quantity , Amount , Product id , Ordered By , No of Clients   \r\n
END;
		$rs_sql_report=execute_my_query($sql_report);
		if(mysqli_num_rows($rs_sql_report)>0){
			while($row5=mysqli_fetch_array($rs_sql_report)){
				$file_content.=<<<END
$row5[0], $row5[1] , '$row5[2]' , $row5[3] , $row5[4] , $row5[5] , $row5[6] , $row5[7] , $row5[8] , $row5[9]  \r\n
END;
			}
			$fp=fopen("$filename","w");
			if($fp){
				fwrite($fp,$file_content,strlen($file_content));
				fclose($fp);
			}else{
				print " file couldn't create $filename";
			}
			$at_file[0]="$filename";
			$rs=send_mail_function($email_from,$email_to,$subject,$msg,$at_file,$cc,$subject,true,$reply_to);
		}
	}
}

/**
 * @return string
 */
function query_ass_expiry(){
$sql=<<<END
 select GPM_PRODUCT_NAME,GPM_PRODUCT_ABR,GOD_ORDER_NO,GID_INSTALL_DATE,
 GLH_CUST_NAME,GID_VALIDITY_DATE,GLH_LEAD_CODE,GLH_TERRITORY_ID,GLH_CUST_BUSSPHNO,gem_emp_name,gem_mobile,gem_emp_id,
 round((asapm.GPM_LIST_PRICE+(if((GID_NO_CLIENTS-pm.GPM_CLIENTS)<0,0,(GID_NO_CLIENTS-pm.GPM_CLIENTS))*price_cl*(20/100))*(1+(asapm.GPM_SERVISE_TAX_PERC/100))),2) as 'ass_value',
 E.GEM_MOBILE 
 from gft_order_hdr inner 
 join  gft_install_dtl_new A on (GID_ORDER_NO=GOD_ORDER_NO AND GOD_ORDER_STATUS='A' and GID_VALIDITY_DATE!='0000-00-00' and GID_EXPIRE_FOR in (1,3))
 inner join gft_product_family_master pfm ON (pfm.GPM_PRODUCT_CODE=A.GID_PRODUCT_CODE)
 join gft_product_master pm on(pm.gpm_product_code=pfm.gpm_product_code and gid_lic_pskew=pm.gpm_product_skew)
 join gft_product_master asapm on(asapm.gpm_product_code=pfm.gpm_product_code and gid_lic_pskew=asapm.GPM_REFERER_SKEW and asapm.gft_skew_property=4) 
 inner join gft_lead_hdr D on (A.GID_LEAD_CODE=D.GLH_LEAD_CODE)
 inner join gft_emp_master E on (glh_lfd_emp_id=GEM_EMP_ID and gem_status='A') 
 left join (SELECT gpm_product_code as cl_pr_code,substring(gpm_product_skew,1,4) skew_sb, 
 GPM_CLIENTS as 'add_cl',(GPM_LIST_PRICE/GPM_CLIENTS) 'price_cl'
 FROM gft_product_master g WHERE  gft_skew_property=3) cl_pr 
on (cl_pr_code=gid_lic_pcode and skew_sb=substring(gid_lic_pskew,1,4)) 
END;
	return $sql;
}

/**
 * @return string
 */
function query_subcription_expiry(){
$sql=<<<END
 select GPM_PRODUCT_NAME,GPM_PRODUCT_ABR,GOD_ORDER_NO,GID_INSTALL_DATE,
 GLH_CUST_NAME,GID_VALIDITY_DATE,GLH_LEAD_CODE,GLH_TERRITORY_ID,GLH_CUST_BUSSPHNO,gem_emp_name,gem_mobile,gem_emp_id,
 round((pm.GPM_LIST_PRICE+(if((GID_NO_CLIENTS-pm.GPM_CLIENTS)<0,0,(GID_NO_CLIENTS-pm.GPM_CLIENTS))*GPM_LIST_PRICE)*(100+pm.GPM_TAX_PERC+pm.GPM_SERVISE_TAX_PERC/100)),2) as 'renuval_value', E.GEM_MOBILE 
 from gft_order_hdr inner 
 join  gft_install_dtl_new A on (GID_ORDER_NO=GOD_ORDER_NO AND GOD_ORDER_STATUS='A' and GID_VALIDITY_DATE!='0000-00-00' and GID_EXPIRE_FOR in (2))
 inner join gft_product_family_master pfm ON (pfm.GPM_PRODUCT_CODE=A.GID_PRODUCT_CODE)
 join gft_product_master pm on(pm.gpm_product_code=pfm.gpm_product_code and gid_lic_pskew=pm.gpm_product_skew)
 inner join gft_lead_hdr D on (A.GID_LEAD_CODE=D.GLH_LEAD_CODE)
 inner join gft_emp_master E on (glh_lfd_emp_id=GEM_EMP_ID and gem_status='A') 
 left join (SELECT gpm_product_code as cl_pr_code,substring(gpm_product_skew,1,4) skew_sb, 
 GPM_CLIENTS as 'add_cl',GPM_LIST_PRICE 'price_cl'
 FROM gft_product_master g WHERE  gft_skew_property=13) cl_pr 
on (cl_pr_code=gid_lic_pcode and skew_sb=substring(gid_lic_pskew,1,4)) 
END;
	return $sql;
}
?>
