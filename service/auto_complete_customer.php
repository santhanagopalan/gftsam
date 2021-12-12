<?php
require_once(__DIR__.'/../dbcon.php');

$search_term 	= isset($_GET['term'])?mysqli_real_escape_string_wrapper(trim((string)$_GET['term'])):'';
$limit			= isset($_GET['limit'])?(int)$_GET['limit']:0;
$purpose        = isset($_GET['purpose'])?$_GET['purpose']:'';
$quotation_no   = isset($_GET['quotation_no'])?$_GET['quotation_no']:'';
$tax_mode       = isset($_GET['tax_mode'])?$_GET['tax_mode']:'';
$corp_cust      = isset($_GET['corp_cust'])?(string)$_GET['corp_cust']:'false';

$id_cond = "";
if(is_numeric($search_term)){
	$id_cond = " or GLH_LEAD_CODE='$search_term' ";
}

$whr_cond = "";
if($corp_cust=='true'){
    $whr_cond = " and GLH_LEAD_TYPE=3 ";
}
$joins = "";
$cols = "";
$group_by = "";
if($purpose=='order_adj_cust') {
    $joins = " left join gft_customer_order_adjustment on (glh_lead_code=goa_lead_code) ";
    $group_by = " and glh_lead_type in (1,3,13) ";//group by glh_lead_code ";
}
$sql1 = " select GLH_LEAD_CODE,GLH_CUST_NAME,GLH_CUST_STREETADDR2$cols from gft_lead_hdr $joins ".
		" where 1 $whr_cond and (GLH_CUST_NAME like '$search_term%' $id_cond)  $group_by ";
if($limit > 0){
	$sql1 .= " limit $limit ";
}
$res1 = execute_my_query($sql1);
$arr = /*. (string[int][string]) .*/array();
while ($row1 = mysqli_fetch_array($res1)){
    $name = $row1['GLH_CUST_NAME']."-".$row1['GLH_CUST_STREETADDR2'];
	$glh_lead_code	= $row1['GLH_LEAD_CODE'].($purpose=='order_adj_cust'?"-$name":'');
	$name_addr		= $row1['GLH_CUST_NAME']." - ".$row1['GLH_CUST_STREETADDR2'];
	$arr[] = array('id'=>"$glh_lead_code",'label'=>"$name_addr");
}
echo json_encode($arr);

?>
<script type="text/javascript">
window.close();
</script>