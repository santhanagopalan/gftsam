<?php
require_once( __DIR__ . "/licenseUpdate.php");
require_once( __DIR__ . "/addonActivationClass.php");

$addonActivation_obj=new addonActivation();
$response = $addonActivation_obj->print_addon_response();
echo $response[0];
exit;
?>
