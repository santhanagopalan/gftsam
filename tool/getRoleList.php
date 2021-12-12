<?php
require_once( __DIR__ . "/../dbcon.php");
require_once( __DIR__ . "/service_check.php");

 header("Access-Control-Allow-Origin: *");

$query=<<<END
select grm_role_id,grm_role_desc from gft_role_master where grm_status='A' and grm_role_id not in (85,50,40,20,79,80,81,21,22,28,26,78,77) order by grm_role_Desc
END;

$result=execute_my_query($query);

$row_list=array();
while($qdata=mysqli_fetch_array($result)){
	$row_list[] = array(
			"id"=>$qdata['grm_role_id'],
			"value"=>$qdata['grm_role_desc']
			);
}
echo json_encode($row_list);


?>
