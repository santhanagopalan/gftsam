<?php
/*. require_module 'standard'; .*/

$skip_password=false;
$skip_authorization=false;
$compulsory_show_error=false;
$compulsory_show_explain=false;
//$db_constr="localhost:/usr/local/mysql/data/mysqld-samtest.sock";
$db_constr="172.17.1.5:6036";
$dbuser="root";
$dbname="sales_server" ;
$dbpass="samR@@t";

if(isset($_SERVER['SERVER_NAME']) and $_SERVER['SERVER_NAME'] === "sam.gofrugal.com" and $_SERVER['SERVER_ADDR'] === "172.31.17.27" and $_SERVER['SERVER_ADDR'] === "15.207.93.199"){
    $show_fullfillment_with_orderno=true;
    $my_product_skip="1";
}else if(file_exists('../wamp') or file_exists('../../wamp') or file_exists('../../../wamp')){
        //$dbpass="SAM_RayMedi";
        $show_fullfillment_with_orderno=false;
    $my_product_skip="1";
}else {
    $show_fullfillment_with_orderno=true;
    $my_product_skip="1";
}

if(isset($_SERVER['SERVER_ADDR']) and $_SERVER['SERVER_ADDR']==='127.0.0.1'){
        $skip_password=true;
        $skip_authorization=false;
        $compulsory_show_error=true;
        $compulsory_show_explain=false;
}
?>
