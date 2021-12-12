<?php

require_once(__DIR__ ."/dbcon.php");

$omit_lead_type=" and glh_lead_type!=8 ";
$end_user=" and glh_lead_type not in (3,13) ";
$corp_user=" and glh_lead_type in (3,13) ";
$end_user_only=" glh_lead_type not in (3,13) ";
$corp_user_only=" glh_lead_type in (3,13) ";

/**
 * @param string $query
 * 
 * @return void
 */
function insert_mis_query($query){		
  $ins_query=" replace into gft_mis_detail_report(GMD_ID,GMD_DATE,GMD_PRODUCT_CODE,
  GMD_PRODUCT_GROUP,GMD_TOTAL_CUST,GMD_TOTAL_PAYABLE,
  GMD_TOTAL_FREE,GMD_PAYABLE_ASA,GMD_PAYABLE_PASA,
  GMD_PAYABLE_SUBSCRIPTION,GMD_FREE_ASA,
  GMD_FREE_PASA,GMD_FREE_SUBSCRIPTION,
  GMD_END_USER,GMD_CORPORATE,GMD_END_USER_PAYABLE,GMD_CORP_PAYABLE,GMD_END_USER_FREE,GMD_CORP_FREE)($query) ";
  execute_my_query($ins_query);
} 

/**
 * @param string $date
 * 
 * @return void
 */
function mis_annuity_update_address_verified($date){
	global $omit_lead_type,$end_user_only,$corp_user_only;
/* address verified */	
$query=" select 14,'$date','1' as GPG_PRODUCT_FAMILY_CODE,'0.3' as GPG_SKEW,count(distinct(glh_lead_code)),'' as 'payable',
'' as 'free','' as Payable_ASA,
       '' as Payable_PASA,
       '' as Payable_SUB,
       '' as Free_ASA, 
	   '' as Free_PASA, 
       '' as Free_SUB, 
       count(distinct(if($end_user_only,glh_lead_code,null))) as 'end_user',
       count(distinct(if($corp_user_only,glh_lead_code,null))) as 'corp_user' ,
       '' as 'payable_end_user',
       '' as 'payable_corp_user',
       '' as 'free_end_user',
       '' as 'free_corp_user' 
from gft_lead_hdr lh  
where GLH_ADDRESS_VERIFIED_DATE='$date' $omit_lead_type GROUP BY GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ";	
	insert_mis_query($query);
}

/**
 * @param string $date
 * 
 * @return void
 */
function mis_annuity_call_update($date){
	global $omit_lead_type,$end_user_only,$corp_user_only;
$query=" select if(gch_current_status='T24',8,if(gch_current_status='T22',10,if(gch_current_status='T23',6,if(gch_current_status='T10',9,if(gch_current_status='T20',11,if(gch_current_status='T5',7,0)))))) mid,'$date',GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ,         
	count(*),'' as 'payable',
	'' as  'free',
	'' as Payable_ASA,
    '' as Payable_PASA,
    '' as Payable_SUB,
    '' as Free_ASA, 
	'' as Free_PASA, 
    '' as Free_SUB, 
       count(if($end_user_only,glh_lead_code,null))'end_user',
       count(if($corp_user_only,glh_lead_code,null)) 'corp_user' ,
   	   '' as 'payable_end_user',
       '' as 'payable_corp_user',
       '' as 'free_end_user',
       '' as 'free_corp_user' 
       from  gft_customer_support_hdr csh 
join gft_customer_support_dtl csd on (gcd_complaint_id=gch_complaint_id )       		
join gft_lead_hdr lh on (glh_lead_code=gch_lead_code $omit_lead_type )
join gft_product_family_master pfm on (pfm.gpm_product_code=GCH_PRODUCT_CODE ) 
join gft_product_group_master pg on (GPG_PRODUCT_FAMILY_CODE=pfm.GPM_HEAD_FAMILY and GPG_SKEW=SUBSTR(GCH_PRODUCT_SKEW,1,4))
where GCD_ACTIVITY_DATE>='$date'  and GCD_ACTIVITY_DATE<='$date 23:59:59' and gch_current_status in ('T24','T22','T23','T10','T20','T5') GROUP BY mid,GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ";
	insert_mis_query($query);
	
	$query=" select '5','$date',GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ,         
	count(distinct(glh_lead_code))," .
	"'' as  'payable',
	'' as  'free',
	'' as  Payable_ASA,
    '' as  Payable_PASA,
    '' as  Payable_SUB,
    '' as  Free_ASA, 
	'' as  Free_PASA, 
    '' as  Free_SUB, 
    count(if($end_user_only,glh_lead_code,null))'end_user',
    count(if($corp_user_only,glh_lead_code,null)) 'corp_user' ,
     '' as 'payable_end_user',
     '' as  'payable_corp_user',
     '' as 'free_end_user',
     '' as  'free_corp_user' 	
       from  gft_customer_support_hdr csh 
join gft_customer_support_dtl csd on (gcd_complaint_id=gch_complaint_id )       		
join gft_lead_hdr lh on (glh_lead_code=gch_lead_code $omit_lead_type )
join gft_product_family_master pfm on (pfm.gpm_product_code=GCH_PRODUCT_CODE ) 
join gft_product_group_master pg on (GPG_PRODUCT_FAMILY_CODE=pfm.GPM_HEAD_FAMILY and GPG_SKEW=SUBSTR(GCH_PRODUCT_SKEW,1,4))
where GCD_ACTIVITY_DATE>='$date' and GCD_ACTIVITY_DATE<='$date 23:59:59' and gch_current_status not in ('T24','T22','T23','T10','T20','T5') " .
" and GCH_ASS_CUST ='E' GROUP BY GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ";
	insert_mis_query($query);
	
}

/**
 * @param string $date
 * 
 * @return void
 */
function mis_annuity_asa_renewal($date){
	global $omit_lead_type,$end_user,$corp_user,$end_user_only,$corp_user_only;
$query=" select 4,'$date',GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ,count(*),sum(if(GPM_FREE_EDITION='N',1,0)) 'payable',
sum(if(GPM_FREE_EDITION='Y',1,0)) 'free',
sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=1,1,0)) Payable_ASA,
       sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=3,1,0)) Payable_PASA,
       sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=2,1,0)) Payable_SUB,
       sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=1,1,0)) Free_ASA, 
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=3,1,0)) Free_PASA, 
       sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=2,1,0)) Free_SUB, 
       count(if($end_user_only,glh_lead_code,null))'end_user',
       count(if($corp_user_only,glh_lead_code,null)) 'corp_user' ,
       count(if(GPM_FREE_EDITION='N' $end_user,glh_lead_code,null))'payable_end_user',
       count(if(GPM_FREE_EDITION='N' $corp_user,glh_lead_code,null)) 'payable_corp_user',
       count(if(GPM_FREE_EDITION='Y' $end_user,glh_lead_code,null))'free_end_user',
       count(if(GPM_FREE_EDITION='Y' $corp_user,glh_lead_code,null)) 'free_corp_user' 
       from gft_install_dtl_new ins 
join gft_ass_dtl asd on (GAD_INS_REFF=GID_INSTALL_ID)        		
join gft_lead_hdr lh on (glh_lead_code=gid_lead_code $omit_lead_type )
join gft_product_master pm on (pm.gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew) 
join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code) 
join gft_product_group_master pg on (GPG_PRODUCT_FAMILY_CODE=pfm.GPM_HEAD_FAMILY and GPG_SKEW=SUBSTR(GID_LIC_PSKEW,1,4))
where gid_status='A' and GAD_ASS_DATE='$date' GROUP BY GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ";

	insert_mis_query($query);
}

/**
 * @param string $ddate
 * 
 * @return void
 */
function update_asa_letter_sent_undelivered($ddate){
	global $omit_lead_type,$end_user,$corp_user,$end_user_only,$corp_user_only;
     /* this will be called whenever the file import */	
	$query=" select 12,GSL_DISPATCH_DATE,GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ,count(*),sum(if(GPM_FREE_EDITION='N',1,0)) 'payable',
sum(if(GPM_FREE_EDITION='Y',1,0)) 'free',
sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=1,1,0)) Payable_ASA,
       sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=3,1,0)) Payable_PASA,
       sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=2,1,0)) Payable_SUB,
       sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=1,1,0)) Free_ASA, 
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=3,1,0)) Free_PASA, 
       sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=2,1,0)) Free_SUB, 
       count(if($end_user_only,glh_lead_code,null))'end_user',
       count(if($corp_user_only,glh_lead_code,null)) 'corp_user' ,
       count(if(GPM_FREE_EDITION='N' $end_user,glh_lead_code,null))'payable_end_user',
       count(if(GPM_FREE_EDITION='N' $corp_user,glh_lead_code,null)) 'payable_corp_user',
       count(if(GPM_FREE_EDITION='Y' $end_user,glh_lead_code,null))'free_end_user',
       count(if(GPM_FREE_EDITION='Y' $corp_user,glh_lead_code,null)) 'free_corp_user' 
       from gft_install_dtl_new ins 
join gft_asa_letter_dispatch asl on (GSL_LEAD_CODE=GID_LEAD_CODE AND GSL_INSTALL_ID=GID_INSTALL_ID)      		
join gft_lead_hdr lh on (glh_lead_code=gid_lead_code $omit_lead_type )
join gft_product_master pm on (pm.gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew) 
join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code) 
join gft_product_group_master pg on (GPG_PRODUCT_FAMILY_CODE=pfm.GPM_HEAD_FAMILY and GPG_SKEW=SUBSTR(GID_LIC_PSKEW,1,4))
where gid_status='A' and GSL_DISPATCH_DATE='$ddate' GROUP BY GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ";	
	insert_mis_query($query);
	
	$query=" select 13,GSL_DISPATCH_DATE,GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ,count(*),sum(if(GPM_FREE_EDITION='N',1,0)) 'payable',
sum(if(GPM_FREE_EDITION='Y',1,0)) 'free',
sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=1,1,0)) Payable_ASA,
       sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=3,1,0)) Payable_PASA,
       sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=2,1,0)) Payable_SUB,
       sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=1,1,0)) Free_ASA, 
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=3,1,0)) Free_PASA, 
       sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=2,1,0)) Free_SUB, 
       count(if($end_user_only,glh_lead_code,null))'end_user',
       count(if($corp_user_only,glh_lead_code,null)) 'corp_user' ,
       count(if(GPM_FREE_EDITION='N' $end_user,glh_lead_code,null))'payable_end_user',
       count(if(GPM_FREE_EDITION='N' $corp_user,glh_lead_code,null)) 'payable_corp_user',
       count(if(GPM_FREE_EDITION='Y' $end_user,glh_lead_code,null))'free_end_user',
       count(if(GPM_FREE_EDITION='Y' $corp_user,glh_lead_code,null)) 'free_corp_user' 
       from gft_install_dtl_new ins 
join gft_asa_letter_dispatch asl on (GSL_LEAD_CODE=GID_LEAD_CODE AND GSL_INSTALL_ID=GID_INSTALL_ID)      		
join gft_lead_hdr lh on (glh_lead_code=gid_lead_code $omit_lead_type )
join gft_product_master pm on (pm.gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew) 
join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code) 
join gft_product_group_master pg on (GPG_PRODUCT_FAMILY_CODE=pfm.GPM_HEAD_FAMILY and GPG_SKEW=SUBSTR(GID_LIC_PSKEW,1,4))
where gid_status='A' and GSL_DISPATCH_DATE='$ddate' AND GSL_DISPATCHED='R' GROUP BY GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ";	
	insert_mis_query($query);
	
}

/**
 * @param string $ddate
 * 
 * @return void
 */
function update_upgrade_letter_sent_undelivered($ddate){
	global $omit_lead_type,$end_user,$corp_user,$end_user_only,$corp_user_only;
     /* this will be called whenever the file import */	
	$query=" select 19,GUL_DISPATCH_DATE,GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ,count(*),sum(if(GPM_FREE_EDITION='N',1,0)) 'payable',
sum(if(GPM_FREE_EDITION='Y',1,0)) 'free',
sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=1,1,0)) Payable_ASA,
       sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=3,1,0)) Payable_PASA,
       sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=2,1,0)) Payable_SUB,
       sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=1,1,0)) Free_ASA, 
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=3,1,0)) Free_PASA, 
       sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=2,1,0)) Free_SUB, 
       count(if($end_user_only,glh_lead_code,null))'end_user',
       count(if($corp_user_only,glh_lead_code,null)) 'corp_user' ,
       count(if(GPM_FREE_EDITION='N' $end_user,glh_lead_code,null))'payable_end_user',
       count(if(GPM_FREE_EDITION='N' $corp_user,glh_lead_code,null)) 'payable_corp_user',
       count(if(GPM_FREE_EDITION='Y' $end_user,glh_lead_code,null))'free_end_user',
       count(if(GPM_FREE_EDITION='Y' $corp_user,glh_lead_code,null)) 'free_corp_user' 
       from gft_install_dtl_new ins 
join gft_upgrade_letter_dispatch asl on (GUL_LEAD_CODE=GID_LEAD_CODE AND GUL_INSTALL_ID=GID_INSTALL_ID)      		
join gft_lead_hdr lh on (glh_lead_code=gid_lead_code $omit_lead_type )
join gft_product_master pm on (pm.gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew) 
join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code) 
join gft_product_group_master pg on (GPG_PRODUCT_FAMILY_CODE=pfm.GPM_HEAD_FAMILY and GPG_SKEW=SUBSTR(GID_LIC_PSKEW,1,4))
where gid_status='A' and GUL_DISPATCH_DATE='$ddate' GROUP BY GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ";	
	insert_mis_query($query);
	
	$query=" select 20,GUL_DISPATCH_DATE,GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ,count(*),sum(if(GPM_FREE_EDITION='N',1,0)) 'payable',
sum(if(GPM_FREE_EDITION='Y',1,0)) 'free',
sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=1,1,0)) Payable_ASA,
       sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=3,1,0)) Payable_PASA,
       sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=2,1,0)) Payable_SUB,
       sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=1,1,0)) Free_ASA, 
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=3,1,0)) Free_PASA, 
       sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=2,1,0)) Free_SUB, 
       count(if($end_user_only,glh_lead_code,null))'end_user',
       count(if($corp_user_only,glh_lead_code,null)) 'corp_user' ,
       count(if(GPM_FREE_EDITION='N' $end_user,glh_lead_code,null))'payable_end_user',
       count(if(GPM_FREE_EDITION='N' $corp_user,glh_lead_code,null)) 'payable_corp_user',
       count(if(GPM_FREE_EDITION='Y' $end_user,glh_lead_code,null))'free_end_user',
       count(if(GPM_FREE_EDITION='Y' $corp_user,glh_lead_code,null)) 'free_corp_user' 
       from gft_install_dtl_new ins 
join gft_upgrade_letter_dispatch asl on (GUL_LEAD_CODE=GID_LEAD_CODE AND GUL_INSTALL_ID=GID_INSTALL_ID)      		
join gft_lead_hdr lh on (glh_lead_code=gid_lead_code $omit_lead_type )
join gft_product_master pm on (pm.gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew) 
join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code) 
join gft_product_group_master pg on (GPG_PRODUCT_FAMILY_CODE=pfm.GPM_HEAD_FAMILY and GPG_SKEW=SUBSTR(GID_LIC_PSKEW,1,4))
where gid_status='A' and GUL_DISPATCH_DATE='$ddate' AND GUL_DISPATCHED='R' GROUP BY GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ";	
	insert_mis_query($query);
	
}

/**
 * @param string $ddate
 * 
 * @return void
 */
function update_proforma_invoice($ddate){
	global $omit_lead_type,$end_user_only,$corp_user_only;
	$query=" select 16,GPH_ORDER_DATE,'1' AS GPG_PRODUCT_FAMILY_CODE,'0.3' AS GPG_SKEW ,count(*),'' as 'payable',
'' as 'free',
'' as Payable_ASA,
'' as Payable_PASA,
      '' as Payable_SUB,
      '' asFree_ASA, 
	'' as Free_PASA, 
     '' as Free_SUB, 
       count(if($end_user_only,glh_lead_code,null)) 'end_user',
       count(if($corp_user_only,glh_lead_code,null)) 'corp_user' ,
       '' as 'payable_end_user',
       '' as 'payable_corp_user',
       '' as 'free_end_user',
       '' as 'free_corp_user' 
       from gft_proforma_hdr ins 		
join gft_lead_hdr lh on (glh_lead_code=GPH_LEAD_CODE $omit_lead_type ) 
join gft_emp_master em on (em.gem_emp_id=GPH_EMP_ID )
join gft_emp_group_master egm on (egm.GEM_EMP_ID=em.gem_emp_id and GEM_GROUP_ID=54 )		
where GPH_ORDER_DATE='$ddate' AND GPH_ORDER_STATUS='A' GROUP BY GPH_ORDER_DATE  ";	
	insert_mis_query($query);
	
   $query=" select 17,GPH_ORDER_DATE,'1' AS GPG_PRODUCT_FAMILY_CODE,'0.3' AS GPG_SKEW ,sum(GPH_ORDER_AMT),'' as 'payable',
'' as 'free',
'' as Payable_ASA,
'' as Payable_PASA,
      '' as Payable_SUB,
      '' asFree_ASA, 
	'' as Free_PASA, 
     '' as Free_SUB, 
       sum(if($end_user_only,GPH_ORDER_AMT,null)) 'end_user',
       sum(if($corp_user_only,GPH_ORDER_AMT,null)) 'corp_user' ,
       '' as 'payable_end_user',
       '' as 'payable_corp_user',
       '' as 'free_end_user',
       '' as 'free_corp_user' 
       from gft_proforma_hdr ins 		
join gft_lead_hdr lh on (glh_lead_code=GPH_LEAD_CODE $omit_lead_type ) 
join gft_emp_master em on (em.gem_emp_id=GPH_EMP_ID )
join gft_emp_group_master egm on (egm.GEM_EMP_ID=em.gem_emp_id and GEM_GROUP_ID=54 )		
where GPH_ORDER_DATE='$ddate' AND GPH_ORDER_STATUS='A' GROUP BY GPH_ORDER_DATE  ";	
	insert_mis_query($query);	
}

/**
 * @param string $ddate
 * 
 * @return void
 */
function update_realized_amount($ddate){
	global $omit_lead_type,$end_user_only,$corp_user_only;
		$query=" select 18,GRD_CHEQUE_CLEARED_DATE,'1' AS GPG_PRODUCT_FAMILY_CODE,'0.3' AS GPG_SKEW ,sum(GRD_RECEIPT_AMT),'' as 'payable',
'' as 'free',
'' as Payable_ASA,
'' as Payable_PASA,
      '' as Payable_SUB,
      '' asFree_ASA, 
	'' as Free_PASA, 
     '' as Free_SUB, 
       sum(if($end_user_only,GRD_RECEIPT_AMT,null)) 'end_user',
       sum(if($corp_user_only,GRD_RECEIPT_AMT,null)) 'corp_user' ,
       '' as 'payable_end_user',
       '' as 'payable_corp_user',
       '' as 'free_end_user',
       '' as 'free_corp_user' 
       from gft_receipt_dtl ins 		
join gft_lead_hdr lh on (glh_lead_code=GRD_LEAD_CODE $omit_lead_type ) 
join gft_emp_master em on (em.gem_emp_id=GRD_EMP_ID )
join gft_emp_group_master egm on (egm.GEM_EMP_ID=em.gem_emp_id and GEM_GROUP_ID=54 ) 
where GRD_CHEQUE_CLEARED_DATE='$ddate' and GRD_CHECKED_WITH_LEDGER='Y' and GRD_STATUS='P' GROUP BY GRD_CHEQUE_CLEARED_DATE  ";	
	insert_mis_query($query);
}

/**
 * @return void
 */
function update_active_customer(){
	global $omit_lead_type,$end_user,$corp_user,$end_user_only,$corp_user_only;
	$yeseteday=date('Y-m-d',mktime('0','0','0',date('m'),date('d')-1,date('Y')));
	/* Active Customer of today get at 00:00:01 */
	$query=" select 1,date(now()),GPG_PRODUCT_FAMILY_CODE,GPG_SKEW,count(*),sum(if(GPM_FREE_EDITION='N',1,0)) 'payable',
	sum(if(GPM_FREE_EDITION='Y',1,0)) 'free',
	sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=1,1,0)) Payable_ASA,
	sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=3,1,0)) Payable_PASA,
	sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=2,1,0)) Payable_SUB,
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=1,1,0)) Free_ASA,
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=3,1,0)) Free_PASA,
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=2,1,0)) Free_SUB,
	count(if($end_user_only,glh_lead_code,null))'end_user',
	count(if($corp_user_only,glh_lead_code,null)) 'corp_user',
	count(if(GPM_FREE_EDITION='N' $end_user,glh_lead_code,null))'payable_end_user',
	count(if(GPM_FREE_EDITION='N' $corp_user,glh_lead_code,null)) 'payable_corp_user',
	count(if(GPM_FREE_EDITION='Y' $end_user,glh_lead_code,null))'free_end_user',
	count(if(GPM_FREE_EDITION='Y' $corp_user,glh_lead_code,null)) 'free_corp_user'
	from gft_install_dtl_new ins
	join gft_lead_hdr lh on (glh_lead_code=gid_lead_code $omit_lead_type )
	join gft_product_master pm on (pm.gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew)
	join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code)
	join gft_product_group_master pg on (GPG_PRODUCT_FAMILY_CODE=pfm.GPM_HEAD_FAMILY and GPG_SKEW=SUBSTR(GID_LIC_PSKEW,1,4))
	where gid_status='A' GROUP BY GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ";


	insert_mis_query($query);


	/* ASA/Subscribtion Active */


	$query=" select 2,date(now()),GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ,count(*),sum(if(GPM_FREE_EDITION='N',1,0)) 'payable',
	sum(if(GPM_FREE_EDITION='Y',1,0)) 'free',
	sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=1,1,0)) Payable_ASA,
	sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=3,1,0)) Payable_PASA,
	sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=2,1,0)) Payable_SUB,
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=1,1,0)) Free_ASA,
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=3,1,0)) Free_PASA,
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=2,1,0)) Free_SUB,
	count(if($end_user_only,glh_lead_code,null))'end_user',
	count(if($corp_user_only,glh_lead_code,null)) 'corp_user',
	count(if(GPM_FREE_EDITION='N' $end_user,glh_lead_code,null))'payable_end_user',
	count(if(GPM_FREE_EDITION='N' $corp_user,glh_lead_code,null)) 'payable_corp_user',
			count(if(GPM_FREE_EDITION='Y' $end_user,glh_lead_code,null))'free_end_user',
					count(if(GPM_FREE_EDITION='Y' $corp_user,glh_lead_code,null)) 'free_corp_user'
							from gft_install_dtl_new ins
							join gft_lead_hdr lh on (glh_lead_code=gid_lead_code $omit_lead_type )
							join gft_product_master pm on (pm.gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew)
							join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code)
							join gft_product_group_master pg on (GPG_PRODUCT_FAMILY_CODE=pfm.GPM_HEAD_FAMILY and GPG_SKEW=SUBSTR(GID_LIC_PSKEW,1,4))
							where gid_status='A' and gid_validity_date>date(now()) GROUP BY GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ";


							insert_mis_query($query);

	/* ASA /SUB EXPIRED */


	$query=" select 3,date(now()),GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ,count(*),sum(if(GPM_FREE_EDITION='N',1,0)) 'payable',
	sum(if(GPM_FREE_EDITION='Y',1,0)) 'free',
	sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=1,1,0)) Payable_ASA,
	sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=3,1,0)) Payable_PASA,
	sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=2,1,0)) Payable_SUB,
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=1,1,0)) Free_ASA,
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=3,1,0)) Free_PASA,
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=2,1,0)) Free_SUB,
	count(if($end_user_only,glh_lead_code,null))'end_user',
	count(if($corp_user_only,glh_lead_code,null)) 'corp_user',
	count(if(GPM_FREE_EDITION='N' $end_user,glh_lead_code,null))'payable_end_user',
	count(if(GPM_FREE_EDITION='N' $corp_user,glh_lead_code,null)) 'payable_corp_user',
	count(if(GPM_FREE_EDITION='Y' $end_user,glh_lead_code,null))'free_end_user',
	count(if(GPM_FREE_EDITION='Y' $corp_user,glh_lead_code,null)) 'free_corp_user'
	from gft_install_dtl_new ins
	join gft_lead_hdr lh on (glh_lead_code=gid_lead_code $omit_lead_type )
	join gft_product_master pm on (gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew)
	join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code)
	join gft_product_group_master pg on (GPG_PRODUCT_FAMILY_CODE=pfm.GPM_HEAD_FAMILY and GPG_SKEW=SUBSTR(GID_LIC_PSKEW,1,4))
	where gid_status='A' and gid_validity_date<date(now()) GROUP BY GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ";

	insert_mis_query($query);
	/* expired on today*/
	$query=" select 15,date(now()),GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ,count(*),sum(if(GPM_FREE_EDITION='N',1,0)) 'payable',
	sum(if(GPM_FREE_EDITION='Y',1,0)) 'free',
	sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=1,1,0)) Payable_ASA,
	sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=3,1,0)) Payable_PASA,
	sum(if(GPM_FREE_EDITION='N' and GID_EXPIRE_FOR=2,1,0)) Payable_SUB,
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=1,1,0)) Free_ASA,
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=3,1,0)) Free_PASA,
	sum(if(GPM_FREE_EDITION='Y' and GID_EXPIRE_FOR=2,1,0)) Free_SUB,
	count(if($end_user_only,glh_lead_code,null))'end_user',
	count(if($corp_user_only,glh_lead_code,null)) 'corp_user',
	count(if(GPM_FREE_EDITION='N' $end_user,glh_lead_code,null))'payable_end_user',
	count(if(GPM_FREE_EDITION='N' $corp_user,glh_lead_code,null)) 'payable_corp_user',
			count(if(GPM_FREE_EDITION='Y' $end_user,glh_lead_code,null))'free_end_user',
			count(if(GPM_FREE_EDITION='Y' $corp_user,glh_lead_code,null)) 'free_corp_user'
			from gft_install_dtl_new ins
			join gft_lead_hdr lh on (glh_lead_code=gid_lead_code $omit_lead_type )
			join gft_product_master pm on (gpm_product_code=gid_lic_pcode and gpm_product_skew=gid_lic_pskew)
			join gft_product_family_master pfm on (pfm.gpm_product_code=pm.gpm_product_code)
			join gft_product_group_master pg on (GPG_PRODUCT_FAMILY_CODE=pfm.GPM_HEAD_FAMILY and GPG_SKEW=SUBSTR(GID_LIC_PSKEW,1,4))
			where gid_status='A' and gid_validity_date=date(now()) GROUP BY GPG_PRODUCT_FAMILY_CODE,GPG_SKEW ";

			insert_mis_query($query);
			/*Renewed */
			mis_annuity_asa_renewal($yeseteday);
			/*annuity status and extended*/
			mis_annuity_call_update($yeseteday);
			mis_annuity_update_address_verified($yeseteday);
}
?>
