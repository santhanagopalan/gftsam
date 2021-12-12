<?php
require_once( __DIR__ . "/../dbcon.php");
require_once( __DIR__ . "/service_check.php");

 header("Access-Control-Allow-Origin: *");

$role_id=isset($_GET['role_id'])?$_GET['role_id']:'';
if ($role_id == ''){
  die("role id not found");
}

$query=<<<END
select GEM_EMP_ID as id ,GEM_EMP_NAME as value from gft_emp_master where GEM_ROLE_ID=$role_id and gem_status='A' order by GEM_EMP_NAME
END;

$result=execute_my_query($query);

$row_list=array();
while($qdata=mysqli_fetch_array($result)){
	$row_list[] = array(
			"id"=>$qdata['id'],
			"value"=>$qdata['value']
			);
}
echo json_encode($row_list);


?>
