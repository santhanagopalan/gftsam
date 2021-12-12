<?php
/*. require_module 'mcrypt'; .*/

/*. forward string function do_activation_hdd(string $ehdd_sn); .*/
/*. forward string function do_activation_orderno(string $eorderno_sn); .*/
/*. forward string function do_activation_customername(string $ecustomername_sn); .*/
/*. forward string function do_ak_noc_server(string $noclients); .*/
/*. forward string function do_encryption_hdd(string $ohdd); .*/
/*. forward string function do_encryption_customername(string $customername); .*/
/*. forward string function do_activationkey_ns(string $activationkey); .*/
/*. forward string function do_encryption_order(string $orderno); .*/
/*. forward string function lic_encrypt(string $value,string $secret); .*/
/*. forward string function do_encryption_prodid(string $ProdID_pc); .*/

require_once(__DIR__ ."/product_util.php");
require_once(__DIR__ ."/visit_submit_in_popup.php");
require_once(__DIR__ ."/function.send_sms.php");
require_once(__DIR__ ."/function.update_in_hdr.php"); 
require_once(__DIR__.'/log.php');
$secret ="1c363e82e2db9a14a556e4258e9ebe62";

/**
 * @param string $data
 * 
 * @return string
 */
function fun_hex2bin_str($data) {
    $len = strlen($data);
    return pack("H" . $len, $data);
}


/**
 * @param string $data
 *
 * @return string
 */
function pad($data) {
	$padlen = 8-(strlen($data) % 8);
	if($padlen == 8){
		return $data;
	}

  	for ($i=0; $i<$padlen; $i++){
		//$data .= chr($padlen);
		$data .= chr(0);
	}
  	return $data;
}

/**
 * @param string $key
 *
 * @return string
 */
function make_openssl_blowfish_key($key)
{
    if("$key" === '')
        return $key;

    $len = (16+2) * 4;
    while(strlen($key) < $len) {
        $key .= $key;
    }
    $key = substr($key, 0, $len);
    return $key;
}
/**
 * @param string $value
 * @param string $secret
 * 
 * @return string
 */
function lic_decrypt($value,$secret){
	$secret=fun_hex2bin_str($secret);
	$value=pad($value);  
	$value=base64_decode($value);
	$iv = random_bytes(8);
	$secret = make_openssl_blowfish_key($secret);
    $v = openssl_decrypt(($value), 'BF-ECB', $secret, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
	$v = rtrim($v, "\0");
	/*
    //$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
	$iv = mcrypt_create_iv(8,MCRYPT_DEV_URANDOM);
    $v=mcrypt_decrypt(MCRYPT_BLOWFISH, $secret, $value, MCRYPT_MODE_ECB, $iv);
	$v=rtrim($v, "\0"); //Remove the leading '\0' character
	*/
    $v = preg_replace('/[\x01-\x08]/u', '', $v); //to remove data which are padded in blowfish encryption
	return $v;
}

/**
 * @param string $value
 * @param string $secret
 *
 * @return string
 */
function lic_encrypt($value,$secret){	
	$secretVar=$secret;
	$valueVar=$value;
	$secret=fun_hex2bin_str($secret);
    $value=pad($value); 
    $iv = random_bytes(8);
    $blockSize = 8;
    $len = strlen($value);
    $paddingLen = intval(($len + $blockSize - 1) / $blockSize) * $blockSize - $len;
    $padding = str_repeat("\0", $paddingLen);
    $data = $value . $padding;
    $secret = make_openssl_blowfish_key($secret);
    $v = openssl_encrypt($data, 'BF-ECB', $secret, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);
    $encdata =  base64_encode($v);
    /*$secret=fun_hex2bin_str($secret);
	$value=pad($value);        
    //$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv(8,MCRYPT_DEV_URANDOM);
	$v=mcrypt_encrypt(MCRYPT_BLOWFISH,$secret, $value, MCRYPT_MODE_ECB,$iv);
	$encdata=base64_encode($v);
	*/
	
	//For Validation
	$v2_val=base64_decode($encdata);
	$value2_val = lic_decrypt($encdata,$secretVar);
	if ($v != $v2_val){
		throw new ErrorException("Problem in  base64_decode -- lic_util.php::lic_encrypt()");
	}
	
 	if ($value2_val != $valueVar){
 		error_log("value=::".$value ."::");
 		$arr = unpack("H*",$value);
 		$arr_str=print_r($arr,true);
 		//$arr_str='';
 		//for($k=1; $k <=count($arr); $k++){
 		//   $arr_str .="-0x".$arr[$k];
 		//}
 		error_log("value HH=::".strval($arr_str)."::");
 		error_log("value2_val=::".$value2_val ."::");
 		error_log("valueVar=::".$valueVar."::");
 		throw new ErrorException("Problem in  lic_decrypt  --- lic_util.php::lic_encrypt() ");
 	}	
	return $encdata;
}


/**
 * @param string $customername
 * 
 * @return string
 */

function do_encryption_customername($customername){
	$ltmp1 = 0;
	for( $i = 0;$i<strlen($customername);$i++){
    	$ltmp1 = $ltmp1 + ord(substr($customername,$i, 1));
	}

	if ((strlen($customername) <= 5) or ($ltmp1 <= 60)){$ltmp1 = $ltmp1 + 86;} 
	elseif (strlen($customername) <= 10){$ltmp1 = $ltmp1 + 73;}
	elseif (strlen($customername) <= 15){$ltmp1 = $ltmp1 + 61;}
	elseif (strlen($customername) <= 20){$ltmp1 = $ltmp1 + 55;}
	elseif (strlen($customername) <= 25){$ltmp1 = $ltmp1 + 47;}
	elseif (strlen($customername) <= 30){$ltmp1 = $ltmp1 + 39;} 
	elseif (strlen($customername) <= 35){$ltmp1 = $ltmp1 + 23;}
	elseif (strlen($customername) <= 40){$ltmp1 = $ltmp1 + 18;}
	elseif (strlen($customername) <= 45){$ltmp1 = $ltmp1 + 41;} 
	elseif (strlen($customername) <= 50){ $ltmp1 = $ltmp1 + 33;}
	else{}
	if ($ltmp1 <= 999 ){    $ltmp1 = $ltmp1 + 4011;}
	elseif ($ltmp1 > 9999){    $ltmp1 =substr($ltmp1,-4) - 789;}	
	$ecustomername = $ltmp1;
	return "$ecustomername";
}
 
/**
 * @param string $orderno
 *
 * @return string 
 */

function do_encryption_order($orderno){
	$ltmp=0;
	for( $i = 0;$i<strlen($orderno);$i++){	
    	$ltmp = $ltmp + ord(substr($orderno,$i, 1));
 	}
	if ($ltmp <= 999){$ltmp = $ltmp + 1947;}
	elseif($ltmp > 9999){$ltmp =substr($ltmp,-4) - 321;}
	$eorderno = $ltmp;
	return "$eorderno";
}

/**
 * @param string $ProdID_pc
 * 
 * @return string
 */
function do_encryption_prodid($ProdID_pc){
	$sProdID='';
	
	if (strlen($ProdID_pc)<6){
		//Note: For the proper value - $ProdID_pc can be 8
		throw new ErrorException("Invalid product Id value :".$ProdID_pc.": of length=".strlen($ProdID_pc));
	}
	
	if (strlen($ProdID_pc) < 7){$pp="00";$ProdID_pc =$ProdID_pc.$pp;}
	elseif (strlen($ProdID_pc) < 8){$pp="0"; $ProdID_pc = $ProdID_pc.$pp;}
	$pk0=(int)substr($ProdID_pc,0,1);
    $pcode=substr($ProdID_pc,0,3);

    if (strpos($ProdID_pc,"EMPLOYEE")!== false){
	    return $ProdID_pc;
    }

	if ($pk0==0){$sProdID =$sProdID.chr(ord(substr($ProdID_pc,0,1)) +20);} 
	elseif ($pk0==1){$sProdID =$sProdID.chr(ord(substr($ProdID_pc,0,1)) +22);}   
	elseif ($pk0==2){$sProdID =$sProdID.chr(ord(substr($ProdID_pc,0,1)) +24);}   
	elseif ($pk0==3){$sProdID =$sProdID.chr(ord(substr($ProdID_pc,0,1)) +26);}   
	elseif ($pk0==4){$sProdID =$sProdID.chr(ord(substr($ProdID_pc,0,1)) +28);}   
	elseif ($pk0==5){$sProdID =$sProdID.chr(ord(substr($ProdID_pc,0,1)) +19);}   
	elseif ($pk0==6){$sProdID =$sProdID.chr(ord(substr($ProdID_pc,0,1)) +21);}   
	elseif ($pk0==7){$sProdID =$sProdID.chr(ord(substr($ProdID_pc,0,1)) +23);}   
	elseif ($pk0==8){$sProdID =$sProdID.chr(ord(substr($ProdID_pc,0,1)) +25);}   
	elseif ($pk0==9){$sProdID =$sProdID.chr(ord(substr($ProdID_pc,0,1)) +27);}   
	$fProdID0=$sProdID;

	$pk1=(int)substr($ProdID_pc,1,1);
	if ($pk1==0){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,1,1)) + 23);} 
	elseif ($pk1==1){$sProdID = $sProdID.chr(ord(substr($ProdID_pc, 1, 1)) + 25);}   
	elseif ($pk1==2){$sProdID = $sProdID.chr(ord(substr($ProdID_pc, 1, 1)) + 27);}   
	elseif ($pk1==3){$sProdID = $sProdID.chr(ord(substr($ProdID_pc, 1, 1)) + 29);}   
	elseif ($pk1==4){$sProdID = $sProdID.chr(ord(substr($ProdID_pc, 1, 1)) + 31);}   
	elseif ($pk1==5){$sProdID = $sProdID.chr(ord(substr($ProdID_pc, 1, 1)) + 24);}   
	elseif ($pk1==6){$sProdID = $sProdID.chr(ord(substr($ProdID_pc, 1, 1)) + 26);}   
	elseif ($pk1==7){$sProdID = $sProdID.chr(ord(substr($ProdID_pc, 1, 1)) + 28);}   
	elseif ($pk1==8){$sProdID = $sProdID.chr(ord(substr($ProdID_pc, 1, 1)) + 30);}   
	elseif ($pk1==9){$sProdID = $sProdID.chr(ord(substr($ProdID_pc, 1, 1)) + 32);}   
	$fProdID1=$sProdID;

	$pk2=(int)substr($ProdID_pc,2,1);
	if ($pk2==0){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,2,1)) + 21);} 
	elseif ($pk2==1){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,2,1)) + 23);}   
	elseif ($pk2==2){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,2,1)) + 25);}   
	elseif ($pk2==3){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,2,1)) + 27);}   
	elseif ($pk2==4){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,2,1)) + 29);}   
	elseif ($pk2==5){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,2,1)) + 22);}   
	elseif ($pk2==6){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,2,1)) + 24);}   
	elseif ($pk2==7){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,2,1)) + 26);}   
	elseif ($pk2==8){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,2,1)) + 28);}   
	elseif ($pk2==9){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,2,1)) + 30);}   
	$fProdID2=$sProdID;

	$pk3=(int)substr($ProdID_pc,3,1);
	if ($pk3==0){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,3,1)) + 24);} 
	elseif ($pk3==1){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,3,1)) + 26);}   
	elseif ($pk3==2){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,3,1)) + 28);}   
	elseif ($pk3==3){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,3,1)) + 30);}   
	elseif ($pk3==4){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,3,1)) + 32);}   
	elseif ($pk3==5){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,3,1)) + 25);}   
	elseif ($pk3==6){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,3,1)) + 27);}   
	elseif ($pk3==7){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,3,1)) + 29);}   
	elseif ($pk3==8){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,3,1)) + 31);}   
	elseif ($pk3==9){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,3,1)) + 33);}   
	$fProdID3=$sProdID;

	$pk4=(int)substr($ProdID_pc,4,1);
	if ($pk4==0){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,4,1)) + 17);} 
	elseif ($pk4==1){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,4,1)) + 19);}   
	elseif ($pk4==2){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,4,1)) + 21);}   
	elseif ($pk4==3){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,4,1)) + 23);}   
	elseif ($pk4==4){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,4,1)) + 25);}   
	elseif ($pk4==5){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,4,1)) + 18);}   
	elseif ($pk4==6){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,4,1)) + 20);}   
	elseif ($pk4==7){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,4,1)) + 22);}   
	elseif ($pk4==8){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,4,1)) + 24);}   
	elseif ($pk4==9){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,4,1)) + 26);}   
	$fProdID4=$sProdID;

	$pk5=(int)substr($ProdID_pc,5,1);
	if ($pk5==0){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,5,1)) + 19);} 
	elseif ($pk5==1){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,5,1)) + 21);}   
	elseif ($pk5==2){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,5,1)) + 23);}   
	elseif ($pk5==3){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,5,1)) + 25);}   
	elseif ($pk5==4){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,5,1)) + 27);}   
	elseif ($pk5==5){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,5,1)) + 20);}   
	elseif ($pk5==6){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,5,1)) + 22);}   
	elseif ($pk5==7){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,5,1)) + 24);}   
	elseif ($pk5==8){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,5,1)) + 26);}   
	elseif ($pk5==9){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,5,1)) + 28);}   
	$fProdID5=$sProdID;
	
	$pk6=substr($ProdID_pc,6,1);
	if ($pk6=="0"){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,6,1)) + 22);} 
	elseif ($pk6=="A"){$sProdID = $sProdID.chr(85);}
	elseif ($pk6=="B"){$sProdID = $sProdID.chr(71);}
	elseif ($pk6=="C"){$sProdID = $sProdID.chr(73);}
	elseif ($pk6=="D"){$sProdID = $sProdID.chr(77);}
	elseif ($pk6=="E"){$sProdID = $sProdID.chr(74);}
	elseif ($pk6=="F"){$sProdID = $sProdID.chr(80);}
	elseif ($pk6=="G"){$sProdID = $sProdID.chr(86);}
	elseif ($pk6=="H"){$sProdID = $sProdID.chr(82);}
	elseif ($pk6=="I"){$sProdID = $sProdID.chr(69);}
	elseif ($pk6=="J"){$sProdID = $sProdID.chr(81);}
	elseif ($pk6=="K"){$sProdID = $sProdID.chr(67);}
	elseif ($pk6=="L"){$sProdID = $sProdID.chr(89);}
	elseif ($pk6=="M"){$sProdID = $sProdID.chr(66);}
	elseif ($pk6=="N"){$sProdID = $sProdID.chr(65);}
	elseif ($pk6=="P"){$sProdID = $sProdID.chr(87);}
	elseif ($pk6=="R"){$sProdID = $sProdID.chr(75);}
	elseif ($pk6=="S"){$sProdID = $sProdID.chr(88);}   
	//elseif ($pk6=="T"){$sProdID = $sProdID.chr(72);}
	elseif ($pk6=="U"){$sProdID = $sProdID.chr(90);}
	
	$eprodcode=$sProdID;
	$pk7=substr($ProdID_pc,7,1);
	if ($pk7=="0"){$sProdID = $sProdID.chr(ord(substr($ProdID_pc,7,1)) + 18);} 
	elseif ($pk7=="A"){$sProdID = $sProdID.chr(81);}
	elseif ($pk7=="B"){$sProdID = $sProdID.chr(86);}
	elseif ($pk7=="C"){$sProdID = $sProdID.chr(73);}
	elseif ($pk7=="D"){$sProdID = $sProdID.chr(71);}
	elseif ($pk7=="E"){$sProdID = $sProdID.chr(74);}
	elseif ($pk7=="F"){$sProdID = $sProdID.chr(65);}
	elseif ($pk7=="G"){$sProdID = $sProdID.chr(78);}
	elseif ($pk7=="H"){$sProdID = $sProdID.chr(82);}
	elseif ($pk7=="I"){$sProdID = $sProdID.chr(87);}
	elseif ($pk7=="J"){$sProdID = $sProdID.chr(84);}
	elseif ($pk7=="K"){$sProdID = $sProdID.chr(68);}
	elseif ($pk7=="L"){$sProdID = $sProdID.chr(89);}
	elseif ($pk7=="M"){$sProdID = $sProdID.chr(66);}
	elseif ($pk7=="N"){$sProdID = $sProdID.chr(69);}
	elseif ($pk7=="P"){$sProdID = $sProdID.chr(88);}
	elseif ($pk7=="R"){$sProdID = $sProdID.chr(75);}
	elseif ($pk7=="S"){$sProdID = $sProdID.chr(80);}
	elseif ($pk7=="T"){$sProdID = $sProdID.chr(72);}
	elseif ($pk7=="U"){$sProdID = $sProdID.chr(90);}
	
	$eprodcode=$sProdID;
	return "$eprodcode";
}

/**
 * @param string $eproductkey
 * 
 * @return string
 */
function do_encryption_productkey_ns($eproductkey){ 
	
	if (strlen($eproductkey)<20){
		//Note: For the proper value - $eproductkey can be 24
		throw new ErrorException("Invalid eproductkey value :".$eproductkey.": of length=".strlen($eproductkey));
	}
	
 	$ns=$eproductkey;
	$ns=strrev($ns);
	$n1=substr($ns,1,1);
	$n2=substr($ns,3,1); 
	$n3=substr($ns,5,1);
	$n4=substr($ns,7,1);
	$n5=substr($ns,9,1);
	$n6=substr($ns,11,1);
	$n7=substr($ns,13,1);
	$n8=substr($ns,15,1);
	$n9=substr($ns,17,1);
	$n10=substr($ns,19,1);
	//$n11=substr($ns,21,1);
	//$n12=substr($ns,23,1);
	
	$nn1=substr($ns,0,1);
	$nn2=substr($ns,2,1); 
	$nn3=substr($ns,4,1);
	$nn4=substr($ns,6,1);
	$nn5=substr($ns,8,1);
	$nn6=substr($ns,10,1);
	$nn7=substr($ns,12,1);
	$nn8=substr($ns,14,1);
	$nn9=substr($ns,16,1);
	$nn10=substr($ns,18,1);
	//$nn11=substr($ns,20,1);
	//$nn12=substr($ns,22,1);
	//$nf=$n1.$n2.$n3.$n4.$n5.$n6.$n7.$n8.$n9.$n10.$n11.$n12; 
	//$nse=$nn1.$nn2.$nn3.$nn4.$nn5.$nn6.$nn7.$nn8.$nn9.$nn10.$nn11.$nn12;
	
	$nf=$n1.$n2.$n3.$n4.$n5.$n6.$n7.$n8.$n9.$n10; 
	$nse=$nn1.$nn2.$nn3.$nn4.$nn5.$nn6.$nn7.$nn8.$nn9.$nn10;
	$normaltoswap=$nf.$nse;
	return $normaltoswap;
}

/**
 * @param string $product_key_normal
 * @param string $fullfillment_no
 * 
 * @return string
 */

function add_productkey_with_fullfillment_no($product_key_normal,$fullfillment_no='0001'){
	$swap_to_last=$product_key_normal[2];$product_key_normal[2]=$fullfillment_no[0];
	$swap_to_last.=$product_key_normal[4];$product_key_normal[4]=$fullfillment_no[1];
	$swap_to_last.=$product_key_normal[11];$product_key_normal[11]=$fullfillment_no[2];
	$swap_to_last.=$product_key_normal[13];$product_key_normal[13]=$fullfillment_no[3];
	$product_key_normal=$product_key_normal.$swap_to_last;
	
	if (strlen($product_key_normal)<24){
		//Note: For the proper value - $eproductkey can be 24
		throw new ErrorException("Invalid eproductkey after fullfillment no added :".$product_key_normal.": of length=".strlen($product_key_normal));
	}
	return $product_key_normal;
}

/**
 * @param string $shop_name
 * @param string $order_no
 * @param string $hdd_key
 * @param string $product_id
 * @param int $fullfillment_no
 * 
 * @return string
 */
function generate_product_key($shop_name,$order_no,$hdd_key,$product_id,$fullfillment_no){
	$product_key_normal=do_encryption_customername($shop_name);
	$product_key_normal.=do_encryption_order($order_no);
	$product_key_normal.=do_encryption_hdd($hdd_key);
	$product_key_normal.='1';
	$product_key_normal.=do_encryption_prodid($product_id);		   	
   	$product_key=do_encryption_productkey_ns($product_key_normal);
   	$fullfillment_no=substr("0000".$fullfillment_no,-4);
	$product_key=add_productkey_with_fullfillment_no($product_key,$fullfillment_no);
	/* should be of length 24 */
	return 	$product_key;
}
/**
 * @param string $eproductkey_user
 *
 * @return string
 */
function do_encryption_productkey_sn($eproductkey_user){ 
	$swaptonormal='';
	$l1=strlen($eproductkey_user);
	if($l1==20){
		$sn=$eproductkey_user;
		$p1=substr($sn,0,10);
		$p2=substr($sn,10,10);
		$sn1=substr($p2,0,1).substr($p1,0,1);
		$sn2=substr($p2,1,1).substr($p1,1,1);
		$sn3=substr($p2,2,1).substr($p1,2,1);
		$sn4=substr($p2,3,1).substr($p1,3,1);
		$sn5=substr($p2,4,1).substr($p1,4,1);
	 	$sn6=substr($p2,5,1).substr($p1,5,1);
	 	$sn7=substr($p2,6,1).substr($p1,6,1);
	 	$sn8=substr($p2,7,1).substr($p1,7,1);
	 	$sn9=substr($p2,8,1).substr($p1,8,1);
	 	$sn10=substr($p2,9,1).substr($p1,9,1);
	 	$swaptonormal=$sn1.$sn2.$sn3.$sn4.$sn5.$sn6.$sn7.$sn8.$sn9.$sn10;
 	}
 	$swaptonormal=strrev($swaptonormal);
 	return $swaptonormal;
}


/**
 * @param string $enoclients
 * 
 * @return string
 */
function do_clientno_normal($enoclients){
	$sProdID='';

	$sProdID =$sProdID.chr(ord(substr($enoclients,0,1))-20);
	$sProdID =$sProdID.chr(ord(substr($enoclients,1,1))-25);
    return $sProdID;
}


/**
 * @param string $ohdd
 * 
 * @return string
 */
function do_encryption_hdd($ohdd){
	$ltmp2 = 0;
	for( $i = 0;$i<strlen($ohdd);$i++){
	    $ltmp2 = ($ltmp2 + ord(substr($ohdd,$i, 1))+13);
	}
	if($ltmp2 <= 99){$ltmp2 = $ltmp2 + 555;}
	elseif ($ltmp2 > 99){$ltmp2 =substr($ltmp2,-3);}
	$oehdd = $ltmp2;
	return "$oehdd";
}
 

/**
 * @param string $ecustomername_sn
 * 
 * @return string
 */
function do_activation_customername($ecustomername_sn){
	$varstr1 = $ecustomername_sn;
	$Tmp_Str1 = chr(ord(substr($varstr1, 0, 1)) + 29) . chr(ord(substr($varstr1, 1, 1)) + 33) . chr(ord(substr($varstr1, 2, 1)) + 23) . chr(ord(substr($varstr1, 3, 1)) + 17);
	$acustomername = $Tmp_Str1;
	return $acustomername;   
}

/** 
 * @param string $eorderno_sn
 * 
 * @return string
 */

function do_activation_orderno($eorderno_sn){
	$varstr = $eorderno_sn;
	$Tmp_Str = chr(ord(substr($varstr, 0, 1)) + 28) . chr(ord(substr($varstr, 1, 1)) + 32) . chr(ord(substr($varstr, 2, 1)) + 22) . chr(ord(substr($varstr, 3, 1)) + 18);
	$aorderno = $Tmp_Str;
	return $aorderno;   
}


/**
 * @param string $ehdd_sn
 * 
 * @return string
 */
function do_activation_hdd($ehdd_sn){
	$varstr2 = $ehdd_sn;
	$Tmp_Str2 = chr(ord(substr($varstr2, 0, 1)) + 27) . chr(ord(substr($varstr2, 1, 1)) + 31) . chr(ord(substr($varstr2, 2, 1)) + 21) . chr(ord(substr($varstr2, 3, 1)) + 19);
 	$ahdd = $Tmp_Str2;
 	return $ahdd;   
}
 
 

/**
 * Activation code generation algorithm for 4 & 5th part
 *
 * @param string $eprodcode0_sn
 * @param string $pcode
 * 
 * @return string
 */
function do_activation_prodid0($eprodcode0_sn,$pcode=null){ 
	$a0=substr($eprodcode0_sn,0,1);/*char 1*/
	if($a0=='D'){$a0=0;} 
	elseif($a0=='G'){$a0=1;} 
	elseif($a0=='F'){$a0=1;} 
	elseif($a0=='H'){$a0=2;} 
	elseif($a0=='J'){$a0=3;} 
	elseif($a0=='P'){$a0=4;} 
	elseif($a0=='C'){$a0=5;} 
	elseif($a0=='E'){$a0=6;} 
	elseif($a0=='N'){$a0=7;}	//done on sep 8th //Remove pcode 100 on May 23 2007
	elseif($a0=='T'){$a0=7;}
	elseif($a0=='Q'){$a0=8;} 	// changes done on 06-11-06//check in on 24-11-06 (rpos super)
	elseif($a0=='K'){$a0=9;} 
	$akp0=$a0; 
	
 	$a1=substr($eprodcode0_sn,1,1);/* char 2 */
	if($a1=='G'){$a1=0;} 
	elseif($a1=='I'){$a1=1;}   
	elseif($a1=='K'){$a1=2;} 
	elseif($a1=='M'){$a1=3;} 
	elseif($a1=='O'){$a1=4;} 
	elseif($a1=='H'){$a1=5;} 
	elseif($a1=='J'){$a1=6;} 
	elseif($a1=='L'){$a1=7;} 
	elseif($a1=='N'){$a1=8;} 
	elseif($a1=='P'){$a1=9;} 
	$akp1=$a1; 
	$a2=substr($eprodcode0_sn,2,1);/* char 3*/
	if($a2=='E'){$a2=0;} 
	elseif($a2=='G'){$a2=1;}   
	elseif($a2=='I'){$a2=2;} 
	elseif($a2=='K'){$a2=3;} 
	elseif($a2=='M'){$a2=4;} 
	elseif($a2=='F'){$a2=5;} 
	elseif($a2=='H'){$a2=6;} 
	elseif($a2=='J'){$a2=7;} 
	elseif($a2=='L'){$a2=8;} 
	elseif($a2=='N'){$a2=9;}
	elseif($a2=='Q'){$a2=9;}  
	$akp2=$a2; 
	$a3=substr($eprodcode0_sn,3,1); /* char 4 */
	if($a3=='H'){$a3=0;} 
	elseif($a3=='J'){$a3=1;}
	elseif($a3=='L'){$a3=2;} 
	elseif($a3=='N'){$a3=3;} 
	elseif($a3=='P'){$a3=4;} 
	elseif($a3=='I'){$a3=5;} 
	elseif($a3=='K'){$a3=6;} 
	elseif($a3=='M'){$a3=7;} 
	elseif($a3=='O'){$a3=8;} 
	elseif($a3=='Q'){$a3=9;} 
	$akp3=$a3; 
	$prodcode4=$akp0.$akp1.$akp2.$akp3;
	return $prodcode4;   
}

/**
 * @param string $eprodcode1_sn
 *
 * @return string
 */
function do_activation_prodid1($eprodcode1_sn){
	$a0=substr($eprodcode1_sn,0,1);/*char1*/
	if($a0=='A'){$a0=0;} 
	elseif($a0=='C'){$a0=1;}   
	elseif($a0=='E'){$a0=2;} 
	elseif($a0=='G'){$a0=3;} 
	elseif($a0=='I'){$a0=4;} 
	elseif($a0=='B'){$a0=5;} 
	elseif($a0=='D'){$a0=6;} 
	elseif($a0=='F'){$a0=7;} 
	elseif($a0=='H'){$a0=8;} 
	elseif($a0=='J'){$a0=9;} 
	$akp4=$a0; 
 
  	$a1=substr($eprodcode1_sn,1,1);/*char 2*/
	if($a1=='C'){$a1=0;} 
	elseif($a1=='E'){$a1=1;}   
	elseif($a1=='G'){$a1=2;} 
	elseif($a1=='I'){$a1=3;} 
	elseif($a1=='K'){$a1=4;} 
	elseif($a1=='D'){$a1=5;} 
	elseif($a1=='F'){$a1=6;} 
	elseif($a1=='H'){$a1=7;} 
	elseif($a1=='J'){$a1=8;} 
	elseif($a1=='L'){$a1=9;} 
	$akp5=$a1;
	$a2=substr($eprodcode1_sn,2,1);/*char 3*/
	if($a2=='F'){$a2=0;} 
	elseif($a2=='H'){$a2=1;}   
	elseif($a2=='J'){$a2=2;}   
	elseif($a2=='L'){$a2=3;}   
	elseif($a2=='N'){$a2=4;}   
	elseif($a2=='G'){$a2=5;}   
	elseif($a2=='I'){$a2=6;}   
	elseif($a2=='K'){$a2=7;}   
	elseif($a2=='M'){$a2=8;}   
	elseif($a2=='O'){$a2=9;}
	elseif($a2=='Y'){$a2='L';}   
	elseif($a2=='Z'){$a2='U';}   
   	$akp6=$a2;
 
	$a3=substr($eprodcode1_sn,3,1);/*char 4*/
	if($a3=='B'){$a3=0;} 
	elseif($a3=='D'){$a3=1;}   
	elseif($a3=='F'){$a3=2;}   
	elseif($a3=='H'){$a3=3;}   
	elseif($a3=='J'){$a3=4;}   
	elseif($a3=='C'){$a3=5;}   
	elseif($a3=='E'){$a3=6;}   
	elseif($a3=='G'){$a3=7;}   
	elseif($a3=='I'){$a3=8;}   
	elseif($a3=='K'){$a3=9;}
	elseif($a3=='Y'){$a3='L';}   
	elseif($a3=='X'){$a3='S';}
	$akp7=$a3;
	$prodcode5=$akp4.$akp5.$akp6.$akp7; 
    return $prodcode5; 
}


/**
 * @param string $fullfill
 *
 * @return string
 */
function do_ak_orderfullfillmentno($fullfill){
	if(strlen($fullfill)<4){
		$femt="0000";
		$fullfill=$femt.$fullfill;
		$fullfill=substr($fullfill,-4);
	}
	$p1=substr($fullfill,0,1);
	if($p1==0){$p1=ord($p1)+19; $p1=chr($p1);}
	elseif($p1==1){$p1=ord($p1)+21; $p1=chr($p1);}
	elseif($p1==2){$p1=ord($p1)+23; $p1=chr($p1);}
	elseif($p1==3){$p1=ord($p1)+25;	$p1=chr($p1);}
	elseif($p1==4){$p1=ord($p1)+27;	$p1=chr($p1);}
	elseif($p1==5){$p1=ord($p1)+30;	$p1=chr($p1);}
	elseif($p1==6){$p1=ord($p1)+27;	$p1=chr($p1);}
	elseif($p1==7){$p1=ord($p1)+25;	$p1=chr($p1);}
	elseif($p1==8){$p1=ord($p1)+21;	$p1=chr($p1);}
	elseif($p1==9){$p1=ord($p1)+18;	$p1=chr($p1);}
	$pfirst=$p1;
	$p2=substr($fullfill,1,1);/*second digit */
	if($p2==0){$p2=ord($p2)+34;  $p2=chr($p2);}
	elseif($p2==1){$p2=ord($p2)+32;  $p2=chr($p2);}
	elseif($p2==2){$p2=ord($p2)+30;  $p2=chr($p2);}
	elseif($p2==3){$p2=ord($p2)+28;  $p2=chr($p2);}
	elseif($p2==4){$p2=ord($p2)+26;  $p2=chr($p2);}
	elseif($p2==5){$p2=ord($p2)+24;  $p2=chr($p2);}
	elseif($p2==6){$p2=ord($p2)+22;  $p2=chr($p2);}
	elseif($p2==7){$p2=ord($p2)+20;  $p2=chr($p2);}
	elseif($p2==8){$p2=ord($p2)+18;  $p2=chr($p2);}
	elseif($p2==9){$p2=ord($p2)+16;  $p2=chr($p2);}
	$psecond=$p2;
	$p3=substr($fullfill,2,1);/*3rd digit*/
	if($p3==0){$p3=ord($p3)+17; $p3=chr($p3);}
	elseif($p3==1){$p3=ord($p3)+18;  $p3=chr($p3);}
	elseif($p3==2){$p3=ord($p3)+20;  $p3=chr($p3);}
	elseif($p3==3){$p3=ord($p3)+22;  $p3=chr($p3);}
	elseif($p3==4){$p3=ord($p3)+24;  $p3=chr($p3);}
	elseif($p3==5){$p3=ord($p3)+26;  $p3=chr($p3);}
	elseif($p3==6){$p3=ord($p3)+28;  $p3=chr($p3);}
	elseif($p3==7){$p3=ord($p3)+30;  $p3=chr($p3);}
	elseif($p3==8){$p3=ord($p3)+32;  $p3=chr($p3);}
	elseif($p3==9){$p3=ord($p3)+33;  $p3=chr($p3);}
	$pthird=$p3;
	$p4=substr($fullfill,3,1);/*4th digit*/
	if($p4==0){$p4=ord($p4)+18;  $p4=chr($p4);}
	elseif($p4==1){$p4=ord($p4)+20;  $p4=chr($p4);}
	elseif($p4==2){$p4=ord($p4)+25;  $p4=chr($p4);}
	elseif($p4==3){$p4=ord($p4)+27;  $p4=chr($p4);}
	elseif($p4==4){$p4=ord($p4)+30;  $p4=chr($p4);}
	elseif($p4==5){$p4=ord($p4)+27;  $p4=chr($p4);}
	elseif($p4==6){$p4=ord($p4)+25;  $p4=chr($p4);}
	elseif($p4==7){$p4=ord($p4)+28;  $p4=chr($p4);}
	elseif($p4==8){$p4=ord($p4)+21;  $p4=chr($p4);}
	elseif($p4==9){$p4=ord($p4)+19;  $p4=chr($p4);}
	$pfourth=$p4;
	$sixactivekey6 = $p1.$p2.$p3.$p4;
	return $sixactivekey6;
}

/**
 * @param string $noclients
 *
 * @return string
 */
function do_ak_noc_server($noclients){
	if((int)$noclients>99){		
		$albhabet_sno=substr($noclients,0,2);
		$albhabet_sno_later=substr($noclients,2,1);
	}
	$p1=substr($noclients,0,1);/*first digit*/
	if($p1==0){$p1=ord($p1)+19;  $p1=chr($p1);}
	elseif($p1==1){$p1=ord($p1)+21;  $p1=chr($p1);}
	elseif($p1==2){$p1=ord($p1)+23;  $p1=chr($p1);}
	elseif($p1==3){$p1=ord($p1)+25;  $p1=chr($p1);}
	elseif($p1==4){$p1=ord($p1)+27;  $p1=chr($p1);}
	elseif($p1==5){$p1=ord($p1)+30;  $p1=chr($p1);}
	elseif($p1==6){$p1=ord($p1)+27;  $p1=chr($p1);}
	elseif($p1==7){$p1=ord($p1)+25;  $p1=chr($p1);}
	elseif($p1==8){$p1=ord($p1)+21;  $p1=chr($p1);}
	elseif($p1==9){$p1=ord($p1)+18;  $p1=chr($p1);}
	$p1=$p1;
	$p2=substr($noclients,1,1);/*Second  digit */
	if($p2==0){$p2=ord($p2)+34;  $p2=chr($p2);}
	elseif($p2==1){$p2=ord($p2)+32;  $p2=chr($p2);}
	elseif($p2==2){$p2=ord($p2)+30;  $p2=chr($p2);}
	elseif($p2==3){$p2=ord($p2)+28;  $p2=chr($p2);}
	elseif($p2==4){$p2=ord($p2)+26;  $p2=chr($p2);}
	elseif($p2==5){$p2=ord($p2)+24;  $p2=chr($p2);}
	elseif($p2==6){$p2=ord($p2)+22;  $p2=chr($p2);}
	elseif($p2==7){$p2=ord($p2)+20;  $p2=chr($p2);}
	elseif($p2==8){$p2=ord($p2)+18;  $p2=chr($p2);}
	elseif($p2==9){$p2=ord($p2)+16;  $p2=chr($p2);}
	$p2=$p2;
	$noclients = $p1.$p2;
	return $noclients;
}


/**
 *  normal activation key to swap 
 *
 * @param string $activationkey
 *
 * @return string
 */
function do_activationkey_ns($activationkey){ 
	$ns=$activationkey;
 	$ns=strrev($ns);
	$n1=substr($ns,1,1);
	$n2=substr($ns,3,1); 
	$n3=substr($ns,5,1);
	$n4=substr($ns,7,1);
	$n5=substr($ns,9,1);
	$n6=substr($ns,11,1);
	$n7=substr($ns,13,1);
	$n8=substr($ns,15,1);
	$n9=substr($ns,17,1);
	$n10=substr($ns,19,1);
	$n11=substr($ns,21,1);
	$n12=substr($ns,23,1);
	$n13=substr($ns,25,1);
	$n14=substr($ns,27,1);
	$n15=substr($ns,29,1);
	$n16=substr($ns,31,1);
	$n17=substr($ns,33,1);
	$n18=substr($ns,35,1);
	$nn1=substr($ns,0,1);
	$nn2=substr($ns,2,1); 
	$nn3=substr($ns,4,1);
	$nn4=substr($ns,6,1);
	$nn5=substr($ns,8,1);
	$nn6=substr($ns,10,1);
	$nn7=substr($ns,12,1);
	$nn8=substr($ns,14,1);
	$nn9=substr($ns,16,1);
	$nn10=substr($ns,18,1);
	$nn11=substr($ns,20,1);
	$nn12=substr($ns,22,1);
	$nn13=substr($ns,24,1);
	$nn14=substr($ns,26,1);
	$nn15=substr($ns,28,1);
	$nn16=substr($ns,30,1);
	$nn17=substr($ns,32,1);
	$nn18=substr($ns,34,1);
	$nf=$n1.$n2.$n3.$n4.$n5.$n6.$n7.$n8.$n9.$n10.$n11.$n12.$n13.$n14.$n15.$n16.$n17.$n18; 
 	$nse=$nn1.$nn2.$nn3.$nn4.$nn5.$nn6.$nn7.$nn8.$nn9.$nn10.$nn11.$nn12.$nn13.$nn14.$nn15.$nn16.$nn17.$nn18;
 	$normaltoswap_a=$nf.$nse;
	return $normaltoswap_a;
}

/**
 * swap to normal
 * this swap is for server 26 digit activation key
 *
 * @param string $normaltoswap_a
 * 
 * @return string
 */
function do_activationkey_sn($normaltoswap_a){ 
	$sn=$normaltoswap_a;
	$p1=substr($sn,0,13);
	$p2=substr($sn,13,13);
	$sn1=substr($p2,0,1).substr($p1,0,1);
	$sn2=substr($p2,1,1).substr($p1,1,1);
	$sn3=substr($p2,2,1).substr($p1,2,1);
	$sn4=substr($p2,3,1).substr($p1,3,1);
	$sn5=substr($p2,4,1).substr($p1,4,1);
	$sn6=substr($p2,5,1).substr($p1,5,1);
	$sn7=substr($p2,6,1).substr($p1,6,1);
	$sn8=substr($p2,7,1).substr($p1,7,1);
	$sn9=substr($p2,8,1).substr($p1,8,1);
	$sn10=substr($p2,9,1).substr($p1,9,1);
	$sn11=substr($p2,10,1).substr($p1,10,1);
	$sn12=substr($p2,11,1).substr($p1,11,1);
	$sn13=substr($p2,12,1).substr($p1,12,1);
	$swaptonormal_a=$sn1.$sn2.$sn3.$sn4.$sn5.$sn6.$sn7.$sn8.$sn9.$sn10.$sn11.$sn12.$sn13;
	$swaptonormal_a=strrev($swaptonormal_a);
	return $swaptonormal_a;
}

/**
 * @param string $ltmp0
 *
 * @return string
 */
function do_ak_graceperiod($ltmp0){
	$p1=substr($ltmp0,0,1);
	$tp1=substr($ltmp0,0,1);
	if($p1==0){$p1=ord($p1)+19;	  $p1=chr($p1);	}
	elseif($p1==1){$p1=ord($p1)+21;	  $p1=chr($p1);	}
	elseif($p1==2){$p1=ord($p1)+23;	  $p1=chr($p1);	}
	elseif($p1==3){$p1=ord($p1)+25;	  $p1=chr($p1);	}
	elseif($p1==4){$p1=ord($p1)+27;	  $p1=chr($p1);	}
	elseif($p1==5){$p1=ord($p1)+30;	  $p1=chr($p1);	}
	elseif($p1==6){$p1=ord($p1)+27;	  $p1=chr($p1);	}
	elseif($p1==7){$p1=ord($p1)+25;	  $p1=chr($p1);	}
	elseif($p1==8){$p1=ord($p1)+21;	  $p1=chr($p1);	}
	elseif($p1==9){$p1=ord($p1)+18;	  $p1=chr($p1);	}
	$pfirst=$p1;
	$p2=substr($ltmp0,1,1);
	$tp2=substr($ltmp0,1,1);
	if($p2==0){	  $p2=ord($p2)+18;	  $p2=chr($p2);	}
	elseif($p2==1){	  $p2=ord($p2)+20;	  $p2=chr($p2);	}
	elseif($p2==2){	  $p2=ord($p2)+25;	  $p2=chr($p2);	}
	elseif($p2==3){	  $p2=ord($p2)+27;	  $p2=chr($p2);	}
	elseif($p2==4){	  $p2=ord($p2)+30;	  $p2=chr($p2);	}
	elseif($p2==5){	  $p2=ord($p2)+27;	  $p2=chr($p2);	}
	elseif($p2==6){	  $p2=ord($p2)+25;	  $p2=chr($p2);	}
	elseif($p2==7){	  $p2=ord($p2)+22;	  $p2=chr($p2);	}
	elseif($p2==8){	  $p2=ord($p2)+18;	  $p2=chr($p2);	}
	elseif($p2==9){	  $p2=ord($p2)+19;	  $p2=chr($p2);	}
	$psecond=$p2;
	$p3=substr($ltmp0,2,1);
	$tp3=substr($ltmp0,2,1);
	if($p3==0){	  $p3=ord($p3)+17;	  $p3=chr($p3);	}
	elseif($p3==1){	  $p3=ord($p3)+18;	  $p3=chr($p3);	}
	elseif($p3==2){	  $p3=ord($p3)+20;	  $p3=chr($p3);	}
	elseif($p3==3){	  $p3=ord($p3)+22;	  $p3=chr($p3);	}
	elseif($p3==4){	  $p3=ord($p3)+24;	  $p3=chr($p3);	}
	elseif($p3==5){	  $p3=ord($p3)+26;	  $p3=chr($p3);	}
	elseif($p3==6){	  $p3=ord($p3)+28;	  $p3=chr($p3);	}
	elseif($p3==7){	  $p3=ord($p3)+30;	  $p3=chr($p3);	}
	elseif($p3==8){	  $p3=ord($p3)+32;	  $p3=chr($p3);	}
	elseif($p3==9){	  $p3=ord($p3)+33;	  $p3=chr($p3);	}
	$pthird=$p3;
	$p4=substr($ltmp0,3,1);
	$tp4=substr($ltmp0,3,1);
	if($p4==0){	  $p4=ord($p4)+34;	  $p4=chr($p4);	}
	elseif($p4==1){	  $p4=ord($p4)+32;	  $p4=chr($p4);	}
	elseif($p4==2){	  $p4=ord($p4)+30;	  $p4=chr($p4);	}
	elseif($p4==3){	  $p4=ord($p4)+28;	  $p4=chr($p4);	}
	elseif($p4==4){	  $p4=ord($p4)+26;	  $p4=chr($p4);	}
	elseif($p4==5){	  $p4=ord($p4)+24;	  $p4=chr($p4);	}
	elseif($p4==6){	  $p4=ord($p4)+22;	  $p4=chr($p4);	}
	elseif($p4==7){	  $p4=ord($p4)+20;	  $p4=chr($p4);	}
	elseif($p4==8){	  $p4=ord($p4)+18;	  $p4=chr($p4);	}
	elseif($p4==9){	  $p4=ord($p4)+16;	  $p4=chr($p4);	}
	$pfourth=$p4;
	$p5=substr($ltmp0,4,1);
	$tp5=substr($ltmp0,4,1);
	if($p5!=''){
		if($p5==0){  $p5=ord($p5)+19;  $p5=chr($p5);}
		elseif($p5==1){  $p5=ord($p5)+21;  $p5=chr($p5);}
		elseif($p5==2){  $p5=ord($p5)+23;  $p5=chr($p5);}
		elseif($p5==3){  $p5=ord($p5)+25;  $p5=chr($p5);}
		elseif($p5==4){  $p5=ord($p5)+27;  $p5=chr($p5);}
		elseif($p5==5){  $p5=ord($p5)+30;  $p5=chr($p5);}
		elseif($p5==6){  $p5=ord($p5)+27;  $p5=chr($p5);}
		elseif($p5==7){  $p5=ord($p5)+25;  $p5=chr($p5);}
		elseif($p5==8){  $p5=ord($p5)+21;  $p5=chr($p5);}
		elseif($p5==9){  $p5=ord($p5)+18;  $p5=chr($p5);}
		$pfifth=$p5;
	}
	$p6=substr($ltmp0,5,1);
	$tp6=substr($ltmp0,5,1);
	if($p6!=''){
		if($p6==0){  $p6=ord($p6)+18;  $p6=chr($p6);}
		elseif($p6==1){  $p6=ord($p6)+20;  $p6=chr($p6);}
		elseif($p6==2){  $p6=ord($p6)+25;  $p6=chr($p6);}
		elseif($p6==3){  $p6=ord($p6)+27;  $p6=chr($p6);}
		elseif($p6==4){  $p6=ord($p6)+30;  $p6=chr($p6);}
		elseif($p6==5){  $p6=ord($p6)+27;  $p6=chr($p6);}
		elseif($p6==6){  $p6=ord($p6)+25;  $p6=chr($p6);}
		elseif($p6==7){  $p6=ord($p6)+22;  $p6=chr($p6);}
		elseif($p6==8){  $p6=ord($p6)+18;  $p6=chr($p6);}
		elseif($p6==9){  $p6=ord($p6)+19;  $p6=chr($p6);}
		$psix=$p6;
	}
	$sixactivekey7 = $p1.$p2.$p3.$p4.$p5.$p6;
	return $sixactivekey7;
}

	/**
	 * @param string $minimum_version
	 * @param string $request_version
	 * 
	 * @return  boolean
	 * */
	
	function license_version_check_minimum($minimum_version,$request_version){
		$request_version=(string)str_replace('-','.',$request_version);
		$minimum_version=(string)str_replace('-','.',$minimum_version);

		$request_version=(string)str_replace("RC","",$request_version);
		$minimum_version=(string)str_replace("RC","",$minimum_version);
		$request_version=(string)str_replace("SP",".",$request_version);
		$minimum_version=(string)str_replace("SP",".",$minimum_version);
				
		$r = version_compare($request_version,$minimum_version);
		if ($r <0){
			return false;
		}
		return true;
	}
	
	/**
	 * @param string $lead_code
	 * @param string $order_no
	 *
	 * @return string[int]
	 */
	function get_incharge_name_no($lead_code='',$order_no=''){
		$ret_name = /*. (string[int]) .*/array();
		$ret_name[0] = $ret_name[1] = $que1 = '';
		$sel_que = " select GEM_EMP_NAME, GEM_MOBILE from gft_emp_master ";
		if($lead_code!=''){
		    $lead_own = get_valid_lead_owner($lead_code);
		    if($lead_own==0){
		        $lead_own = get_lead_mgmt_incharge('', '', 0, 0,100,$lead_code);
		    }
			$que1 = " where GEM_EMP_ID='$lead_own' ";
		}else if ($order_no!=''){
			$que1 = " join gft_order_hdr on (GEM_EMP_ID = GOD_INCHARGE_EMP_ID ) ".
					" where GOD_ORDER_NO='$order_no' and GEM_STATUS='A' ";
		}
		if($que1!='') {
			$res1 = execute_my_query($sel_que.$que1);
			if($row1 = mysqli_fetch_array($res1)){
				$ret_name[0] = $row1['GEM_EMP_NAME'];
				$ret_name[1] = $row1['GEM_MOBILE'];
			}
		}
		return $ret_name;
	}
	
	/**
	 * @param string $lead_code
	 * @param int $vertical_id
	 * 
	 * @return void
	 */
	function update_vertical_for_customer($lead_code,$vertical_id){
		$upquery = "update gft_lead_hdr set GLH_VERTICAL_CODE='$vertical_id' where GLH_LEAD_CODE='$lead_code'";
		if(!execute_my_query($upquery)){
			//error	
		}
	}


/**
 * Used for old license
 *
 * @param string $product_key
 * 
 * @return string
 */
function getFullFillmentNumberFromProductKey($product_key){
	$pk=explode('-',$product_key);
	$prodkeyfull='';
	for($i=0;$i<count($pk);$i++){
		$prodkeyfull.=$pk[$i];
	} 

	$fullfill_no='';
	if(strlen($prodkeyfull)==24){
		//get_fullfillment_no	
		$fullfill[0]=substr($prodkeyfull,2,1);
		$fullfill[1]=substr($prodkeyfull,4,1);
		$fullfill[2]=substr($prodkeyfull,11,1);
		$fullfill[3]=substr($prodkeyfull,13,1);
		//swaped pk     
		$miss_p[0]=substr($prodkeyfull,20,1);   
		$miss_p[1]=substr($prodkeyfull,21,1);
		$miss_p[2]=substr($prodkeyfull,22,1);
		$miss_p[3]=substr($prodkeyfull,23,1);   
		$pr_arr=str_split($prodkeyfull,1);  
		$pr_arr[2]=$miss_p[0];
		$pr_arr[4]=$miss_p[1];
		$pr_arr[11]=$miss_p[2];
		$pr_arr[13]=$miss_p[3];
		$prodkeyfull=implode("",$pr_arr);
		$prodkeyfull=substr(implode("",$pr_arr),0,20);
		$fullfill_no=implode("",$fullfill);
	}
	return $fullfill_no;
}

/**
 * @param int $code
 * @param string $group
 * @param string $fullfill_no
 *
 * @return string[int]
 */
function get_product_id($code,$group,$fullfill_no) {
	$return_array = /*. (string[int]) .*/array();
	$query= " select GLL_PRODUCT_ID,GLL_CLIENTS from gft_local_license_master where GLL_PRODUCT_FCODE='$code' and ".
			" GLL_PRODUCT_GROUP='$group' and GLL_FULLFILLMENT_NO=$fullfill_no";
	$res = execute_my_query($query);
	if($row = mysqli_fetch_array($res)){
		$return_array[0] = $row['GLL_PRODUCT_ID'];
		$return_array[1] = $row['GLL_CLIENTS'];
	}
	return $return_array;
}

/**
 * @param string $order_no
 * @param string $fullfillment_no
 * @param string $pcode
 * @param string $pskew
 * 
 * @return string
 */
function get_lead_code_from_idendity($order_no, $fullfillment_no, $pcode, $pskew) {
	$int_val_of_fullfill_no = (int)$fullfillment_no;
	$query =" select GID_LEAD_CODE from gft_install_dtl_new ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW)  ".
			" where GID_ORDER_NO='$order_no' and GID_FULLFILLMENT_NO=$int_val_of_fullfill_no and GID_LIC_PCODE='$pcode' ".
			" and (GID_LIC_PSKEW='$pskew' or GPM_REFERER_SKEW='$pskew') ";
	$res = execute_my_query($query);
	if($row_data = mysqli_fetch_array($res)){
		return $row_data['GID_LEAD_CODE'];
	}
	return '';
}

/**
 * @param string $order_no
 * @param string $fullfillment_no
 * 
 * @return boolean
 */
function is_dft_order($order_no,$fullfillment_no){
	$fullfill_no = (int)$fullfillment_no;
	$sql1 = " select GOP_ORDER_NO from gft_order_product_dtl join gft_order_hdr on (GOD_ORDER_NO=GOP_ORDER_NO) ".
			" where GOP_ORDER_NO='$order_no' and GOP_FULLFILLMENT_NO=$fullfill_no and GOP_PRODUCT_CODE=120 and GOD_ORDER_STATUS='A' ";
	$res1 = execute_my_query($sql1);
	if(mysqli_num_rows($res1) > 0){
		return true;
	}
	return false;
}

/**
 * @param string  $identity
 * 
 * @return boolean
 */
function is_local_user_identity($identity){
    $mob_no = substr($identity, 0,10);
    $emp_id = (int)substr($identity, 10,5);
    $qr1 = execute_my_query(" select GEM_EMP_ID from gft_emp_master where GEM_MOBILE='$mob_no' and GEM_EMP_ID='$emp_id' and GEM_STATUS='A' ");
    if(mysqli_num_rows($qr1) > 0){
        return true;
    }
    return false;
}

/**
 * @param string $idendity
 * 
 * @return string[int]
 */
function get_details_from_idendity($idendity) {
	$arr_dtl = /*. (string[int]) .*/array();
	if(strlen($idendity)!=27){
		return $arr_dtl;
	}
	$order_no = substr($idendity, 0, 15);
	$fullfillment_no = substr($idendity, 15, 4);
	$pcode = substr($idendity, 19, 3);
	$pskew = substr($idendity, 22, 2).".".substr($idendity, 24, 3);
	if( is_dft_order($order_no,$fullfillment_no) && ($pcode!='120') ){ //dft order no with Base product license
		$pcode = '120';
		$pskew = "01.0SL";
	}
	$query =" select GID_LEAD_CODE,GID_LIC_PSKEW,GID_INSTALL_ID,GID_NO_CLIENTS,GID_LIC_ORDER_NO,GPM_PRODUCT_TYPE,GID_INSTALL_DATE ".
			" from gft_install_dtl_new ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW)  ".
			" where GID_ORDER_NO='$order_no' and GID_FULLFILLMENT_NO=$fullfillment_no and GID_LIC_PCODE=$pcode ".
			" and (GID_LIC_PSKEW='$pskew' or GPM_REFERER_SKEW='$pskew') and GID_STATUS in ('A','S') ";
	$res = execute_my_query($query);
	if($row_data = mysqli_fetch_array($res)){
		$arr_dtl[0] = $row_data['GID_LEAD_CODE'];
		$arr_dtl[1] = $row_data['GID_INSTALL_ID'];
		$arr_dtl[2] = $order_no;
		$arr_dtl[3] = $fullfillment_no;
		$arr_dtl[4] = $pcode;
		$arr_dtl[5] = $row_data['GID_LIC_PSKEW'];
		$arr_dtl[6] = $row_data['GID_NO_CLIENTS'];
		$arr_dtl[7] = $row_data['GID_LIC_ORDER_NO'];
		$arr_dtl[8] = $row_data['GPM_PRODUCT_TYPE'];
		$arr_dtl[9] = $row_data['GID_INSTALL_DATE'];
	}
	return $arr_dtl;
}

/**
 * @param string $lead_code
 * @param string $check_status
 * 
 * @return boolean
 */
function system_assessment_available($lead_code,$check_status=""){
	$where_con	=	"";
	if($check_status!=""){
		$where_con	=	" AND HW_STATUS='$check_status'";
	}
	$query_res = execute_my_query("select HW_CUST_ID from gft_hwassessment_info where HW_CUST_ID='$lead_code' $where_con");
	if(mysqli_num_rows($query_res) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $xmlRaw
 * @param string[int] $xmlFieldNames
 * 
 * @return string[string]
 */
function give_parsed_xml($xmlRaw,$xmlFieldNames){
	$parsedXML=/*. (string[string]) .*/ array();
	foreach ($xmlFieldNames as $xmlField) {
		if(strpos($xmlRaw,"<$xmlField>")!==false){
			$parsedXML[$xmlField]=substr($xmlRaw,
					strpos($xmlRaw,"<$xmlField>")+strlen("<$xmlField>"),
					strpos($xmlRaw,"</$xmlField>")-strlen("<$xmlField>")
					-strpos($xmlRaw,"<$xmlField>"));
		}
		foreach($parsedXML as $key=>$value){
			if(strpos($value,"<![CDATA[")!==false){
				$value = str_replace(array("<![CDATA[","]]>"), "", $value);
			}
			$parsedXML[$key]=trim($value);
		}
	}
	return $parsedXML;
}

/**
 * @param string $productcode
 * @param string $skew_group
 * 
 * @return string
 */
function get_product_manager($productcode, $skew_group){
	$sel_que = "select GPG_PRODUCT_MANAGER from gft_product_group_master where gpg_product_family_code='$productcode' and gpg_skew='$skew_group'";
	$sel_res = execute_my_query($sel_que);
	if($row1 = mysqli_fetch_array($sel_res)){
		return $row1['GPG_PRODUCT_MANAGER'];	
	}
	return '';
}

/**
 * @param string $order_no
 * @param string $fullfill_no
 * @param string $lic_pcode
 * @param string $lic_pskew
 * @param string $cust_id
 * 
 * @return string 
 */
function get_db_password($order_no, $fullfill_no, $lic_pcode, $lic_pskew, $cust_id){
	$quer = " select GID_DB_PASSWORD from gft_install_dtl_new where GID_ORDER_NO='$order_no' and GID_FULLFILLMENT_NO=$fullfill_no ".
			" and GID_LIC_PCODE='$lic_pcode' and GID_LIC_PSKEW='$lic_pskew' ";
	$resul = execute_my_query($quer);
	if($row1 = mysqli_fetch_array($resul)){
		if($row1['GID_DB_PASSWORD']!=''){
			return $row1['GID_DB_PASSWORD'];
		}
	}
	$swap_cust_id = substr(str_shuffle($cust_id),-4);
	$c1 = chr(65+(((int)substr($swap_cust_id, 0,1))*2));
	$c2 = chr(65+(((int)substr($swap_cust_id, 1,1))*2));
	$c3 = chr(65+(((int)substr($swap_cust_id, 2,1))*2));
	$c4 = chr(65+(((int)substr($swap_cust_id, 3,1))*2));
	$password = str_shuffle(date('Ym').$c1.$c4.$c2.$c3);
	return $password;
}

/**
 * @param string $input_data
 * @param string $ret_data
 * @param string $ret_encrypt_data
 * @param string $lead_code
 * @param string $req_type
 * @param string $err_code
 * @param string $err_msg
 * @param string $decrpted_input_data
 * @param string $expiry_date
 *
 * @return void
 */
function log_request($input_data, $ret_data, $ret_encrypt_data, $lead_code, $req_type, $err_code='', $err_msg='',$decrpted_input_data='',$expiry_date=''){
	global $log;
	$ins_request=/*. (mixed[string]) .*/array();
//	$ins_request['GLC_REQUEST_ID']='';
	$ins_request['GLC_ONLINE_CONTENT']=$input_data;
	$ins_request['GLC_REQUEST_TIME']=date('Y-m-d H:i:s');
	$ins_request['GLC_RETURN_DATA']=$ret_data;
//	$ins_request['GLC_RETURN_ENCRYPTED_DATA']=$ret_encrypt_data;
	$ins_request['GLC_LEAD_CODE']=$lead_code;
	$ins_request['GLC_IP_ADDRESS']=isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:gethostbyname(gethostname());
	$ins_request['GLC_REQUEST_PURPOSE_ID']=$req_type;
	$ins_request['GLC_ERROR_CODE']=$err_code;
	$ins_request['GLC_ERROR_MESSAGE']=$err_msg;
	$ins_request['GLC_DECRYPTED_CONTENT']=$decrpted_input_data;
	$ins_request['GLC_PROCESSING_TIME']=getDeltaTime();
	if($expiry_date!=''){
		$ins_request['GLC_EXPIRY_DATE'] = $expiry_date;
	}
	//array_update_tables_common($ins_request,'gft_lic_request',null,null,SALES_DUMMY_ID,null,null,$ins_request);
	$log->logInfo($ins_request,null,true);
}

/**
 * @param string $lead_code
 * 
 * @return string
 */
function contact_info_for_authentication($lead_code){
	$sel_query = " select group_concat(distinct GCC_CONTACT_NO order by GCC_CONTACT_TYPE desc) as contact_info ".
				 " from gft_lead_hdr join gft_customer_contact_dtl on (GCC_LEAD_CODE=GLH_LEAD_CODE) ".
				 " left join gft_contact_dtl_group_map on (GCC_ID=GCG_CONTACT_ID) ".
				 " where GLH_LEAD_CODE='$lead_code' and (gcc_designation=1 or GCG_GROUP_ID=3) ".
				 " and if(GLH_COUNTRY='India',GCC_CONTACT_TYPE in (1,4), GCC_CONTACT_TYPE in (4)) ".
				 " group by GLH_LEAD_CODE ";
	$res_query	= execute_my_query($sel_query);
	if($row_data = mysqli_fetch_array($res_query)){
		return $row_data['contact_info'];
	}
	return '';
}

/**
 * @param int $install_id
 * 
 * @return string
 */
function get_last_reinstall_date($install_id){
	$query = " select max(GRD_REINSTALL_DATE) as reins_date from gft_reinstall_dtl where GRD_INSTALL_REFF='$install_id' having reins_date is not null ";
	$res = execute_my_query($query);
	if(mysqli_num_rows($res)==0){
		$query = " select GID_INSTALL_DATE as reins_date from gft_install_dtl_new where GID_INSTALL_ID='$install_id'";
		$res = execute_my_query($query);
	}
	if($row1 = mysqli_fetch_array($res)){
		return $row1['reins_date'];
	}
	return '';
}

/**
 * @param int $install_id
 * @param string $hkey
 * 
 * @return boolean
 */
function is_previous_hkey($install_id,$hkey){
	$check_res = execute_my_query("select GHL_INSTALL_ID from gft_hkey_log where GHL_INSTALL_ID='$install_id' and GHL_CURRENT_STATUS='A'");
	if(mysqli_num_rows($check_res)==0){
		return false;
	}
	$query_res = execute_my_query(" select GHL_INSTALL_ID from gft_hkey_log where GHL_INSTALL_ID='$install_id' and GHL_HKEY='$hkey' and GHL_CURRENT_STATUS='I'");
	if(mysqli_num_rows($query_res) > 0){
		return true;
	}
	return false;
}

/**
 * @param string $prodkeyfull
 *
 * @return string[int]
 */
function split_pkey_from_fullno($prodkeyfull){
	if($prodkeyfull!='' and strlen($prodkeyfull)==24){
		//get_fullfillment_no
		$fullfill[0]=substr($prodkeyfull,2,1);
		$fullfill[1]=substr($prodkeyfull,4,1);
		$fullfill[2]=substr($prodkeyfull,11,1);
		$fullfill[3]=substr($prodkeyfull,13,1);
		//swaped pk
		$miss_p[0]=substr($prodkeyfull,20,1);
		$miss_p[1]=substr($prodkeyfull,21,1);
		$miss_p[2]=substr($prodkeyfull,22,1);
		$miss_p[3]=substr($prodkeyfull,23,1);
		$pr_arr=str_split($prodkeyfull,1);
		$pr_arr[2]=$miss_p[0];
		$pr_arr[4]=$miss_p[1];
		$pr_arr[11]=$miss_p[2];
		$pr_arr[13]=$miss_p[3];
		$prodkeyfull=implode("",$pr_arr);
		$prodkeyfull=substr(implode("",$pr_arr),0,20);
		$fullfill_no=implode("",$fullfill);
		$spk=array($prodkeyfull,$fullfill_no);
		return $spk;
	}
	$spk=array('','0');
	return $spk;
}

/**
 * @param string $install_id
 * 
 * @return string
 */
function get_reinstall_prepared_time($install_id){
	$select_query = " select max(GRP_PREPARED_DATETIME) as prepared_time from gft_reinstall_prepare_dtl where GRP_INSTALL_ID='$install_id' and GRP_PREPARE_STATUS=1 ";
	$result1 = execute_my_query($select_query);
	if($row1 = mysqli_fetch_array($result1)){
		return $row1['prepared_time'];
	}
	return '';
}

/**
 * @param string $install_id
 * 
 * @return boolean
 */
function is_valid_asa($install_id){
	$validity_date = get_single_value_from_single_table("GID_VALIDITY_DATE", "gft_install_dtl_new", "GID_INSTALL_ID", $install_id);
	if(strtotime($validity_date) >= strtotime(date('Y-m-d'))){
		return true;
	}
	return false;
}

/**
 * @param string[string] $data_arr
 * 
 * @return string
 */
function get_offline_key($data_arr){
	$ak = $data_arr['activation_key'];
	$ak_arr = str_split($ak,1);
	$checksum = 0;
	foreach ($ak_arr as $key => $val){
		$ascii_val = (ord($val)*($key+1));
		$checksum += $ascii_val;
	}
	$checksum = substr("000000"."$checksum", -6);
	$s = str_split($checksum,1);
	$o = str_split($data_arr['dukaan_order_no'],1);
	$c = str_split($data_arr['customer_id'],1);
	$e = str_split($data_arr['expiry_date'],1);
	$n = str_split($data_arr['noc'],1);
	$i = str_split($data_arr['lic_type'],1);
	
	$mode = $data_arr['mode'];
	if($mode=='1'){
		$act_key = $o[4].$o[1].$e[5].$s[5].$c[0].$o[8].$o[11].$c[2].$o[2].$s[2].$o[0].
					$c[4].$e[3].$i[0].$o[5].$n[1].$c[1].$mode.$n[0].$s[0].$c[6].$c[5].$e[1].$o[6].$s[1].
					$c[3].$e[4].$e[2].$o[7].$o[3].$e[0].$c[7].$o[9].$s[4].$o[10].$s[3];
	}else if($mode=='2'){
		$act_key = $c[7].$e[5].$o[8].$o[4].$c[2].$o[9].$c[6].$c[5].$e[3].$o[5].$o[6].$s[3].$s[0].$s[1].
					$o[1].$c[3].$i[0].$mode.$e[0].$o[3].$e[2].$o[2].$o[11].$c[0].$e[1].$n[1].
					$c[1].$c[4].$s[2].$s[4].$o[0].$o[7].$o[10].$e[4].$s[5].$n[0];
	}else if($mode=='3'){
		$act_key = $c[1].$n[0].$s[3].$e[0].$c[5].$o[7].$s[1].$s[2].$c[0].$o[10].$s[4].$o[0].$e[2].$c[7].
					$o[9].$n[1].$c[6].$mode.$o[1].$e[5].$s[0].$c[2].$e[4].$e[1].$c[3].$i[0].$o[3].$o[6].$c[4].
					$e[3].$s[5].$o[4].$o[2].$o[8].$o[11].$o[5];
	}
	$split_act_key = "";
	for($m=0;$m<strlen($act_key);$m++){
		$split_act_key .= substr($act_key, $m, 1);
		if( ($m!=35) && ($m%6 == 5) ){
			$split_act_key .= "-";
		}
	}
	return $split_act_key;
}

/**
 * @param string $order_no
 * @param string $pcode
 * @param string $pgroup
 * 
 * @return string
 */
function get_kit_based_validity($order_no,$pcode,$pgroup){
	$sql1 = " select GSK_PRODUCT_QTY from gft_order_product_dtl ".
			" join gft_product_master on (GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" join gft_skew_kit_master on (GOP_PRODUCT_CODE=GSK_KIT_PCODE and GOP_PRODUCT_SKEW=GSK_KIT_PSKEW and GSK_PRODUCT_CODE=300) ". //to get hq qty
			" where GOP_ORDER_NO='$order_no' and GPM_ORDER_TYPE=2 ";
	$res1 = execute_my_query($sql1);
	$hq_qty = 0;
	if($row1 = mysqli_fetch_array($res1)){
		$hq_qty = (int)$row1['GSK_PRODUCT_QTY'];
	}
	if($hq_qty==0){
		return '';
	}
	$sql2 = " select GID_INSTALL_ID from gft_install_dtl_new where GID_ORDER_NO='$order_no' ".
			" and GID_LIC_PCODE='$pcode' and substr(GID_LIC_PSKEW,1,4)='$pgroup' ";
	$res2 = execute_my_query($sql2);
	$num_rows = mysqli_num_rows($res2);
	if($num_rows <= $hq_qty){
		$sql3 = " select GID_VALIDITY_DATE from gft_install_dtl_new where GID_ORDER_NO='$order_no' order by GID_INSTALL_DATE limit 1 ";
		$res3 = execute_my_query($sql3);
		if($row2 = mysqli_fetch_array($res3)){
			return $row2['GID_VALIDITY_DATE'];
		}
	}
	return '';
}

/**
 * @param string $lead_code
 * @param string $return_format
 * 
 * @return int
 */
function get_pos_details_for_kit_based_customer($lead_code,$return_format){
	$sql1 = " select GOD_ORDER_NO from gft_order_hdr join gft_order_product_dtl on (GOD_ORDER_NO=GOP_ORDER_NO) ".
			" join gft_product_master on (GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" where GOD_LEAD_CODE='$lead_code' and GOD_ORDER_STATUS='A' and GPM_ORDER_TYPE in (2,3) group by GOD_ORDER_NO ";
	
	$que1 = " select GOP_PRODUCT_CODE,if(GPM_REFERER_SKEW='',GOP_PRODUCT_SKEW,GPM_REFERER_SKEW) as REFERER_SKEW,sum(GOP_QTY) as tot_qty,sum(GOP_CP_USEDQTY) as used_qty,GPT_TYPE_NAME ".
			" from gft_order_product_dtl ".
			" join gft_product_master on (GOP_PRODUCT_CODE=GPM_PRODUCT_CODE and GOP_PRODUCT_SKEW=GPM_PRODUCT_SKEW) ".
			" join gft_product_type_master on (GPT_TYPE_ID=GPM_PRODUCT_TYPE) ".
			" where GFT_SKEW_PROPERTY in (1,11) and GPM_ORDER_TYPE not in (2,3,4) and GOP_PRODUCT_CODE in (200,500) and GOP_ORDER_NO in ($sql1) and GPM_PRODUCT_TYPE!=8 ".
			" group by GOP_PRODUCT_CODE,REFERER_SKEW ";
	$res1 = execute_my_query($que1);
	$total_cnt = $used_cnt = /*. (int[string]) .*/array();
	$edition_name = /*. (string[string]) .*/array();
	while($row1 = mysqli_fetch_array($res1)){
		$pcode = $row1['GOP_PRODUCT_CODE'];
		$pskew = $row1['REFERER_SKEW'];
		$prod_id = $pcode.(string)str_replace(".", "", $pskew);
		$total_cnt[$prod_id]= (int)$row1['tot_qty'];
		$used_cnt[$prod_id] = (int)$row1['used_qty'];
		$edition_name[$prod_id] = $row1['GPT_TYPE_NAME'];
	}
	$out_txt = "";
	if($return_format=='xml'){
		foreach($total_cnt as $product_id => $val){
			$out_txt .= "<POS CODE='$product_id' EDITION='$edition_name[$product_id]' TOTAL_CNT='$val' USED_CNT='$used_cnt[$product_id]' />";
		}
	}else if($return_format=='only_count'){
		$out_txt .= (string)array_sum($total_cnt); 
	}
	return $out_txt;
}

/**
 * @param string $cust_id
 * @param int $outlet_cust_id
 * 
 * @return string[int][int]
 */
function get_outlet_info_in_array($cust_id,$outlet_cust_id=0){
	$sql1 = " select GLH_LEAD_CODE, GLH_CUST_NAME,GLH_CUST_STREETADDR2,GOL_OUTLET_ID,GOL_VAT_TIN, ".
			" GLH_CUST_PINCODE,GLH_CUST_CITY,GLH_CUST_STATECODE,outlet.GID_ORDER_NO,outlet.GID_FULLFILLMENT_NO, ".
			" group_concat(if(GCC_CONTACT_TYPE in (1,2),GCC_CONTACT_NO,null)) as mobile, ".
			" group_concat(if(GCC_CONTACT_TYPE=4,GCC_CONTACT_NO,null)) as email ".
			" from gft_install_dtl_new base ".
			" join gft_outlet_lead_code_mapping on (base.GID_INSTALL_ID=GOL_INSTALL_ID) ".
			" join gft_lead_hdr on (GLH_LEAD_CODE=GOL_CUST_ID) ".
			" join gft_customer_contact_dtl on (GCC_LEAD_CODE=GLH_LEAD_CODE) ".
			" left join gft_install_dtl_new outlet on (outlet.GID_LEAD_CODE=GLH_LEAD_CODE and outlet.GID_STATUS!='U' and outlet.GID_LIC_PCODE in (500,200) ) ".
			" where base.GID_LEAD_CODE='$cust_id' and base.GID_LIC_PCODE=300 and base.GID_STATUS!='U' and GOL_OUTLET_STATUS='A' ";
	if( ($outlet_cust_id!=0) && ($outlet_cust_id!=$cust_id) ){
		$sql1 .= " and GOL_CUST_ID='$outlet_cust_id' ";
	}
	$sql1 .= " group by GOL_OUTLET_ID ";
	$res1 = execute_my_query($sql1);
	$arr = /*. (string[int][int]) .*/array();
	while($row1 = mysqli_fetch_array($res1)){
		$gid_order_no = $row1['GID_ORDER_NO'];
		$mobile_arr = explode(",", $row1['mobile']);
		$email_arr  = explode(",", $row1['email']);
		$address 	= $row1['GLH_CUST_CITY']." - ".$row1['GLH_CUST_PINCODE']." , ".$row1['GLH_CUST_STATECODE'];
		$arr[0][] 	= $row1['GOL_OUTLET_ID'];
		$arr[1][]	= $row1['GLH_CUST_NAME'];
		$arr[2][] 	= $row1['GLH_CUST_STREETADDR2'];
		$arr[3][] 	= "SELL";
		$arr[4][] 	= $address;
		$arr[5][] 	= $mobile_arr[0];
		$arr[6][] 	= $email_arr[0];
		$arr[7][] 	= ($gid_order_no=="")?"":$gid_order_no.substr("0000".$row1['GID_FULLFILLMENT_NO'], -4);
		$arr[8][] 	= $row1['GOL_VAT_TIN'];
		$arr[9][] 	= $row1['GLH_LEAD_CODE'];
	}
	return $arr;
}

/**
 * @param int $lead_code
 * 
 * @return int
 */
function get_number_of_clients_from_order($lead_code){
	$sql1 = " select sum(GOP_QTY) as qty_val from gft_order_hdr ".
			" join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
			" join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			" where GOD_LEAD_CODE='$lead_code' and GOD_ORDER_STATUS='A' and GFT_SKEW_PROPERTY=3 ";
	$res1 = execute_my_query($sql1);
	if ($row1 = mysqli_fetch_array($res1)){
		return (int)$row1['qty_val'];
	}
	return 0;
}

/**
 * @param string $version
 * 
 * @return string
 */
function get_valid_version($version){
	$version = (string)str_replace('-','.',$version);
	$version = (string)str_replace("RC","",$version);
	$version = (string)str_replace("SP",".",$version);
	return $version;
}

/**
 * @return string[string]
 */
function get_api_key_info(){
	$ret_arr = /*. (string[string]) .*/array();
	$headers = apache_request_headers();
	$api_key = isset($headers['X-Api-Key'])?(string)$headers['X-Api-Key']:'';
	$ip_addr = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
	$ret_arr['partner_id'] = "0";
	$ret_arr['api_key'] = $api_key;
	$ret_arr['ip_addr'] = $ip_addr;
	if($api_key!=''){
		$sql1 = " select GPK_EMP_ID,GPK_IP_CHECK,GPK_IP_ADDRESS from gft_api_key ".
				" where GPK_STATUS=1 and GPK_API_KEY='".mysqli_real_escape_string_wrapper($api_key)."' ";
		$res1 = execute_my_query($sql1);
		if($row1 = mysqli_fetch_array($res1)){
			$partner_id 		= $row1['GPK_EMP_ID'];
			$is_ip_check 		= (int)$row1['GPK_IP_CHECK'];
			$list_of_ip_address = $row1['GPK_IP_ADDRESS'];
			if( ($is_ip_check==1) && ($list_of_ip_address!='') ){
				if($ip_addr!=''){ //remote addr should be present. not supported when called via cron
					if(strpos($list_of_ip_address, $ip_addr)!==false){
						$ret_arr['partner_id'] = $partner_id;
					}
				}
			}else{
				$ret_arr['partner_id'] = $partner_id;
			}
		}
	}
	return $ret_arr;
}

/**
 * @param string $message
 * @param string $error_code
 * @param string $request_data
 * @param string $req_type
 * @param string $lead_code
 *
 * @return void
 */
function send_error_code_with_log($message,$error_code,$request_data,$req_type,$lead_code){
	header('X-PHP-Response-Code: '.$error_code, true, $error_code);
	$error['message']=$message;
	$resp = json_encode($error);
	echo $resp;
	log_request($request_data, $resp, '', $lead_code, $req_type,'',$message);
}

/**
 * @param int $plugin_type
 * @param string $pcode
 * @param string $pgroup
 * 
 * @return string[string]
 */
function get_latest_plugin_version($plugin_type,$pcode,$pgroup){
	$ret_arr = /*. (string[string]) .*/array();
	$sql1 = " select GAV_VERSION,GAV_DOWNLOAD_PATH,GAV_DESCRIPTION from gft_plugins_version where GAV_BASE_PCODE='$pcode' ".
			" and GAV_BASE_PSKEW='$pgroup' and GAV_PLUGIN_TYPE='$plugin_type' order by GAV_ID desc limit 1 ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$ret_arr['version'] 		= $row1['GAV_VERSION'];
		$ret_arr['download_path'] 	= $row1['GAV_DOWNLOAD_PATH'];
		$ret_arr['description'] 	= $row1['GAV_DESCRIPTION'];
	}
	return $ret_arr;
	
}

/**
 * @param string $plugin_type
 * @param string $install_id
 * @param string $version
 * 
 * @return void
 */
function update_plugin_version($plugin_type, $install_id, $version){
	if($plugin_type=='1'){
		$up1 = "update gft_install_dtl_new set GID_PRINT_PROFILE_VERSION='$version' where GID_INSTALL_ID='$install_id' ";
		execute_my_query($up1);
	}
	$insert_arr['GPI_INSTALL_ID'] 	= $install_id;
	$insert_arr['GPI_PLUGIN_TYPE'] = $plugin_type;
	$insert_arr['GPI_VERSION'] 	= $version;
	$insert_arr['GPI_UPDATE_DATE'] = date('Y-m-d H:i:s');
	array_insert_query("gft_plugins_installed", $insert_arr);
}

/**
 * @param string $emply_id
 * 
 * @return string[string][int]
 */
function get_employee_mapped_custom_license($emply_id){
	$ret_arr['rpos6'] = /*. (string[int]) .*/array();
	$ret_arr['rpos7'] = /*. (string[int]) .*/array();
	
	$mobile_no	 = get_single_value_from_single_table("GEM_MOBILE", "gft_emp_master", "GEM_EMP_ID", $emply_id);
	$local_order = substr($mobile_no, -10).substr("00000".$emply_id, -5);
	$que_custom = " select GLLD_PRODUCT_CODE,GLLD_PRODUCT_SKEW from gft_local_license_dtl where GLLD_ORDER_NO='$local_order' ";
	$res_custom = execute_my_query($que_custom);
	while ($row1 = mysqli_fetch_array($res_custom)){
		$pcode = $row1['GLLD_PRODUCT_CODE'];
		$pskew = (string)str_replace(".", "", $row1['GLLD_PRODUCT_SKEW']);
		if(substr($pskew, 0,3)=='065'){
			$ret_arr['rpos6'][] = $pcode.$pskew;
		}else if(substr($pskew, 0,3)=='070'){
			$ret_arr['rpos7'][] = $pcode.$pskew;
		}
	}
	return $ret_arr;
}

/**
 * @param string $lead_code
 * 
 * @return string[int][string]
 */
function get_addons_bought_for_kit($lead_code){
    $que_res = execute_my_query("select GLH_LEAD_CODE from gft_lead_hdr where glh_reference_given='$lead_code' and GLH_LEAD_SOURCECODE=7");
    $all_leads = array($lead_code);
    while($row_data = mysqli_fetch_array($que_res)){
        $all_leads[] = $row_data['GLH_LEAD_CODE'];
    }
    $all_lead_str = implode(",", $all_leads);
    $feature_sub = " select GAF_NAME,GAM_PRODUCT_CODE,GAM_PRODUCT_SKEW from gft_addon_feature_master join gft_addon_feature_skew_mapping on (GAM_FEATURE_ID=GAF_ID) group by GAM_PRODUCT_CODE,GAM_PRODUCT_SKEW ";
	$qu_add=" select GOP_ORDER_NO,GOP_FULLFILLMENT_NO,GOP_PRODUCT_CODE,concat(GOP_PRODUCT_CODE,'-',GOP_PRODUCT_SKEW) as prod_id, ".
			" ifnull(GAF_NAME,GPM_PRODUCT_NAME) GPM_PRODUCT_NAME, sum(GOP_QTY) as pqty from gft_order_product_dtl ".
			" join gft_order_hdr on (GOD_ORDER_NO=GOP_ORDER_NO and GOD_ORDER_STATUS='A') ".
			" join gft_product_master pm on (pm.GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW and pm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE) ".
			" join gft_product_family_master pfm on (pfm.GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and pfm.GPM_STATUS='A') ".
			" left join ($feature_sub) fs on (fs.GAM_PRODUCT_CODE=GOP_PRODUCT_CODE and fs.GAM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
			" where GOD_LEAD_CODE in ($all_lead_str) and GPM_LICENSE_TYPE!=3 and (pfm.GPM_IS_INTERNAL_PRODUCT=2 or pfm.GPM_CATEGORY=6) ".
			" and GFT_SKEW_PROPERTY in (1,11,18) and GOP_PRODUCT_CODE not in (306) group by prod_id ";
	$re_add = execute_my_query($qu_add);
	$ret_arr = /*. (string[int][string]) .*/array();
	while ($add_row = mysqli_fetch_array($re_add)){
		$arr[0] = $add_row['GPM_PRODUCT_NAME'];
		$arr[1] = $add_row['prod_id'];
		$arr[2] = $add_row['pqty'];
		$arr[3] = $add_row['GOP_ORDER_NO'].substr("0000".$add_row['GOP_FULLFILLMENT_NO'], -4);
		$arr[4] = (in_array($add_row['GOP_PRODUCT_CODE'], array('514','529','727','728')))?"integration":"mobile";
		$ret_arr[] = $arr;
	}
	return $ret_arr;
}

/**
 * @param string $order_no
 * @param string $fullfill_no
 * @param string $prod_code
 * @param string $prod_skew
 * 
 * @return int
 */
function get_extended_count($order_no,$fullfill_no,$prod_code,$prod_skew){
	$sql1 = " select count(GLA_LEAD_CODE) as ext_cnt from gft_lic_approved_log where GLA_STATUS_ID=3 and GLA_ORDER_NO='$order_no' ".
			" and GLA_FULLFILLMENT_NO='".(int)$fullfill_no."' and GLA_PRODUCT_CODE='$prod_code' and GLA_PRODUCT_SKEW='$prod_skew' ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$extended_cnt = (int)$row1['ext_cnt'];
		return $extended_cnt;
	}
	return 0;
}

/**
 * @param string $validity_date
 * 
 * @return void
 */
function get_alternate_day_for_holiday($validity_date){
	$validity_date = db_date_format($validity_date);
	$ret_date = $validity_date;
	$date_query = " select GVD_TO_DATE from gft_validity_date_change where GVD_FROM_DATE='$validity_date' and GVD_DATETIME=(select max(GVD_DATETIME) from gft_validity_date_change where gvd_from_date='$validity_date') ";
	$date_res = execute_my_query($date_query);
	if($row = mysqli_fetch_array($date_res)) {
		$ret_date = $row['GVD_TO_DATE'];
	}
// 	if(in_array($validity_date, array('2017-10-17','2017-10-18','2017-10-19'))){
// 		$ret_date = '2017-10-20';
// 	}
	return $ret_date;
}

/**
 * @param string $lead_code
 * 
 * @return boolean
 */
function is_gosecure_provisioned($lead_code){
    $qr = execute_my_query("select GCD_LEAD_CODE from gft_cust_env_data where GCD_LEAD_CODE='$lead_code' and gcd_provision_status='success'");
    if(mysqli_num_rows($qr) > 0){
        return true;
    }
    return false;
}

/**
 * @param string $gid_lead_code
 * @param string $gid_lic_pcode
 * 
 * @return string
 */
function get_connectplus_token($gid_lead_code,$gid_lic_pcode){
	$cp_token = "";
	if( ($gid_lic_pcode=='706') && !is_gosecure_provisioned($gid_lead_code) ){
	    return $cp_token;
	}
	$sql1 = " select GID_CONNECTPLUS_TOKEN from gft_install_dtl_new ".
			" where GID_LEAD_CODE='$gid_lead_code' and GID_LIC_PCODE='$gid_lic_pcode' and GID_STATUS!='U' ";
	$res1 = execute_my_query($sql1);
	if($row1 = mysqli_fetch_array($res1)){
		$cp_token = $row1['GID_CONNECTPLUS_TOKEN'];
	}
	return $cp_token;
}

/**
 * @param string $req_url
 * @param string $req_data
 * @param string $req_header
 * @param string $req_method
 * @param string $resp_code
 * @param string $resp_body
 * 
 * @return void
 */
function insert_into_sync_queue($req_url,$req_data,$req_header,$req_method,$resp_code='',$resp_body=''){
	$today_time = date('Y-m-d H:i:s');
	$ins_arr = array(
			'GSQ_REQUEST_URL'	=> $req_url, 		
			'GSQ_REQUEST_BODY'	=> $req_data,
			'GSQ_REQUEST_HEADER'=> $req_header,
			'GSQ_REQUEST_METHOD'=> $req_method,
			'GSQ_STATUS'		=> 'P',
			'GSQ_RESPONSE_CODE'	=> $resp_code,
			'GSQ_RESPONSE_BODY'	=> $resp_body,
			'GSQ_CREATED_DATE'	=> $today_time,
			'GSQ_UPDATED_DATE'	=> $today_time
	);
	array_insert_query("gft_sync_queue", $ins_arr);
}

/**
 * @param string $cust_id
 * @param string $install_id
 * 
 * @return void
 */
function post_token_to_authservice($cust_id,$install_id=''){
	$sql1 = " select GID_VALIDITY_DATE,GID_CONNECTPLUS_TOKEN,GPG_PRODUCT_ALIAS from gft_install_dtl_new ".
			" join gft_product_group_master on (gpg_product_family_code=GID_LIC_PCODE and gpg_skew=substr(GID_LIC_PSKEW,1,4)) ".
			" where GID_LEAD_CODE='$cust_id' and GID_CONNECTPLUS_TOKEN!='' and GID_CONNECTPLUS_TOKEN is not null ".
			" and GID_STATUS!='U' ";
	if($install_id!=''){
		$sql1 .= " and GID_INSTALL_ID='$install_id' ";
	}
	$res1 = execute_my_query($sql1);
	$data_arr = /*. (string[int][string]) .*/array();
	while ($row1 = mysqli_fetch_array($res1)){
		$data_arr[] = array(
				'addonApiToken'=>$row1['GID_CONNECTPLUS_TOKEN'],
				'addonExpiryDate'=>$row1['GID_VALIDITY_DATE'],
				'addonProductName'=>$row1['GPG_PRODUCT_ALIAS']
		);
	}
	if(count($data_arr) > 0){
		$cp_config 	= get_connectplus_config();
		$cp_api_key = $cp_config['provision_api_key'];
		$post_url 	= str_replace("{{customerId}}", $cust_id, $cp_config['post_token_url']);
		$post_data 	= json_encode($data_arr);
		$header_arr = array("Content-Type: application/json","x-api-key: $cp_api_key");
		do_curl_to_connectplus($cust_id, $post_url, $post_data, $header_arr);
	}
}

/**
 * @param string $cust_id
 * @param string $pcode
 * @param string $pgroup
 * @param string $pversion
 *
 * @return string[int][string]
 */
function get_addon_feature_for_base_product($cust_id,$pcode,$pgroup,$pversion){
    $feature_arr = /*. (string[int][string]) .*/array();
	$pversion = get_valid_version($pversion);
	if(($pcode>0) && ($pgroup>0)){
		$pcode_group = $pcode."-".$pgroup;
		$sql = " select gam.GAM_MIN_SUPPORTED_BASE_PRODUCT_VERSION as MIN_VERSION, gpm.GPM_NAME, gpm.GPM_ID ". 
			   " from gft_pos_approval_product_mapping gam inner join gft_pos_approval_master gpm on (gam.GAM_APPROVAL_MASTER_ID = gpm.GPM_ID) ".
			   " where GAM_BASE_PRODUCT = '$pcode_group' order by gpm.GPM_ID ";
		$res = execute_my_query($sql);
		while($row = mysqli_fetch_assoc($res)){
			$version_validator = version_compare($pversion, get_valid_version($row['MIN_VERSION']),">=");			
			if($version_validator){
				$feature_arr[] = array("code"=> $row['GPM_ID'], "name"=> $row['GPM_NAME']);
			}
		}
	}
    return $feature_arr;
}


/**
 * @param string $customerId
 * 
 * @return int
 */
function get_valid_lead_owner($customerId){
    $q1 = " select GLH_LFD_EMP_ID from gft_lead_hdr join gft_emp_master on (GEM_EMP_ID=GLH_LFD_EMP_ID and GEM_STATUS='A') ".
        " where GLH_LEAD_CODE='$customerId' and GLH_LFD_EMP_ID not in (9999,9998) ";
    $r1 = execute_my_query($q1);
    $lfd_emp = 0;
    if($d1 = mysqli_fetch_assoc($r1)){
        $lfd_emp = (int)$d1['GLH_LFD_EMP_ID'];
    }
    return $lfd_emp;
}

/**
 * @param string $customerId
 *
 * @return int
 */
function get_valid_last_activity_by($customerId){
    $q1 = " select GLD_ACTIVITY_BY,GLD_VISIT_DATE from gft_activity join gft_emp_master on (GEM_EMP_ID=GLD_ACTIVITY_BY and GEM_STATUS='A') ".
        " join gft_activity_master on (GAM_ACTIVITY_ID=GLD_VISIT_NATURE) ".
        " where GLD_LEAD_CODE='$customerId' and GLD_ACTIVITY_BY!=9999 and GAM_AUTOMATIC_ACTIVITY='N' ".
        " order by GLD_VISIT_DATE desc,GLD_DATE desc,GLD_ACTIVITY_ID desc limit 1 ";
    $r1 = execute_my_query($q1);
    $act_arr = /*. (string[int]) .*/array();
    if($d1 = mysqli_fetch_assoc($r1)){
        $act_arr[0] = $d1['GLD_ACTIVITY_BY'];
        $act_arr[1] = $d1['GLD_VISIT_DATE'];
    }
    return $act_arr;
}

/**
 * @param string $cust_id
 * @param string $product_code
 * @param string $mob_no
 * @param string $mobile_os
 * @param string $created_by_id
 * @param string $created_by_no
 * @param string $gai_user_mode
 *
 * @return void
 */
function update_in_app_installed_dtl($cust_id,$product_code,$mob_no,$mobile_os,$created_by_id,$created_by_no,$gai_user_mode){
    $mobile_os_id = 0;
    if(strcasecmp($mobile_os,"android")==0){
        $mobile_os_id=1;
    }else if(strcasecmp($mobile_os, "ios")==0){
        $mobile_os_id=2;
    }
    $qres1 = execute_my_query(" select GAI_ID from gft_app_installed_dtl where GAI_MOBILE='$mob_no' and GAI_APP_PCODE='$product_code' ");
    if($d1 = mysqli_fetch_array($qres1)){
        $prime_id = $d1['GAI_ID'];
        $up1 =  " update gft_app_installed_dtl set GAI_LEAD_CODE='$cust_id',GAI_DEVICE_OS='$mobile_os_id',GAI_USER_MODE='$gai_user_mode',GAI_UPDATED_DATETIME=now() where GAI_ID='$prime_id' ";
        execute_my_query($up1);
    }else{
        $install_by_remarks = 'GFT Employee / Partner';
        if((int)$created_by_id==0){
            $q1 = " select GAI_INSTALLED_EMP,date(GAI_CREATED_DATETIME) as created_dt from gft_app_installed_dtl join gft_emp_master on (GEM_EMP_ID=GAI_INSTALLED_EMP and GEM_STATUS='A') ".
                " where GAI_LEAD_CODE='$cust_id' order by GAI_CREATED_DATETIME desc limit 1";
            $r1 = execute_my_query($q1);
            if($rd1 = mysqli_fetch_assoc($r1)){
                $created_by_id = $rd1['GAI_INSTALLED_EMP'];
                $install_by_remarks = 'Based on previous installation on '.$rd1['created_dt'];
            }else{
                $created_by_id = get_valid_lead_owner($cust_id);
                $install_by_remarks = 'Based on lead owner';
                if((int)$created_by_id==0){
                    $last_act_dtl = get_valid_last_activity_by($cust_id);
                    if(count($last_act_dtl) > 1){
                        $created_by_id = $last_act_dtl[0];
                        $install_by_remarks = 'Based on last activity on '.$last_act_dtl[1];
                    }
                }
                if((int)$created_by_id==0){
                    $created_by_id = get_lead_mgmt_incharge('', '', 0, 0,100,$cust_id);
                    $install_by_remarks = 'Based on agile mapping';
                }
            }
            if((int)$created_by_id!=0){
                $created_by_no = get_single_value_from_single_table("GEM_MOBILE", "gft_emp_master", "GEM_EMP_ID", $created_by_id);
            }
        }
        $insarr = array(
            'GAI_LEAD_CODE'=>$cust_id,
            'GAI_MOBILE'=>$mob_no,
            'GAI_APP_PCODE'=>$product_code,
            'GAI_DEVICE_OS'=>$mobile_os_id,
            'GAI_INSTALLED_EMP'=>$created_by_id,
            'GAI_INSTALLED_CONTACT'=>$created_by_no,
            'GAI_INSTALLED_REMARKS'=>$install_by_remarks,
            'GAI_USER_MODE'=>$gai_user_mode,
            'GAI_CREATED_DATETIME'=>date('Y-m-d H:i:s'),
            'GAI_UPDATED_DATETIME'=>date('Y-m-d H:i:s'),
        );
        array_insert_query("gft_app_installed_dtl", $insarr);
    }
}

/**
 * @param string $customer_id
 * @param string $pcode
 * 
 * @return boolean
 */
function is_license_agreement_agreed($customer_id, $pcode){
    $q1 = " select GCL_LEAD_CODE from gft_customer_license_agreement_dtl where GCL_LEAD_CODE='$customer_id' and GCL_PRODUCT_CODE='$pcode' ";
    $r1 = execute_my_query($q1);
    if(mysqli_num_rows($r1) > 0){
        return true;
    }
    return false;
}

/**
 * @param string $cust_id
 * @param string $mobile_no
 * 
 * @return string
 */
function get_peekaboo_auto_login_url($cust_id,$mobile_no){
    $login_url = "";
    if(is_product_installed($cust_id, 734)){
        $mob_cond = getContactDtlWhereCondition("GCC_CONTACT_NO", $mobile_no);
        $que1 = " select GPU_USER_NAME,GPU_PASSWORD from gft_pos_users ".
                " join gft_install_dtl_new on (GID_INSTALL_ID=GPU_INSTALL_ID) ".
                " join gft_customer_contact_dtl on (GCC_ID=GPU_CONTACT_ID) ".
                " where GID_LEAD_CODE='$cust_id' and GPU_CONTACT_STATUS='A' and GID_STATUS!='U' and $mob_cond ".
                " and GPU_PASSWORD!='' and GPU_PASSWORD is not null ";
        $res1 = execute_my_query($que1);
        if($row1 = mysqli_fetch_array($res1)){
            $arr = array('username'=>$row1['GPU_USER_NAME'],'password'=>$row1['GPU_PASSWORD']);
            $config = get_connectplus_config();
            $cloud_domain = str_replace("{{customerId}}", $cust_id, $config['cloud_domain']);
            $login_url= $cloud_domain."/peekaboo/#/signed/".base64_encode(json_encode($arr));
        }
    }
    return $login_url;
}

/**
 * @param string $cust_id
 * 
 * @return string[string]
 */
function get_base_product_info_for_customer_id($cust_id){
    $que = " select GID_INSTALL_ID,GID_LIC_PCODE,GID_LIC_PSKEW,GPG_PRODUCT_ALIAS from gft_install_dtl_new ".
        " join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
        " join gft_product_group_master on (gpg_product_family_code=GID_LIC_PCODE and gpg_skew=substr(GID_LIC_PSKEW,1,4)) ".
        " where GID_LEAD_CODE='$cust_id' and GID_LIC_PCODE in (200,300,500) and GID_STATUS!='U' ".
        " order by GPM_LICENSE_TYPE limit 1";
    $res = execute_my_query($que);
    $ret_arr = array();
    if($row = mysqli_fetch_array($res)){
        $ret_arr = array(
            'install_id'=>$row['GID_INSTALL_ID'],
            'pcode'=>$row['GID_LIC_PCODE'],
            'pskew'=>$row['GID_LIC_PSKEW'],
            'alias'=>$row['GPG_PRODUCT_ALIAS']
        );
    }
    return $ret_arr;
}

/**
 * @param string $cust_id
 * 
 * @return void
 */
function post_gosecure_license_type($cust_id){
    $gosecure_ref_id = isset($_SESSION['gosecure_ref_id'])?(int)$_SESSION['gosecure_ref_id']:null;
    $que = " select GID_STATUS,GPM_LICENSE_TYPE,GID_VALIDITY_DATE from gft_install_dtl_new ".
        " join gft_product_master on (GPM_PRODUCT_CODE=GID_LIC_PCODE and GPM_PRODUCT_SKEW=GID_LIC_PSKEW) ".
        " where GID_LEAD_CODE='$cust_id' and GID_LIC_PCODE=706 ";
    $res = execute_my_query($que);
    if($row = mysqli_fetch_array($res)){
        $license_type = "SUBSCRIPTION";
        $expiry_date  = date('Y-m-d\T23:59:59.000P',strtotime($row['GID_VALIDITY_DATE']." 23:59:59"));
        if($row['GID_STATUS']=='U'){
            $license_type = "NA";
        }else if($row['GPM_LICENSE_TYPE']=='3'){
            $license_type = "TRIAL";
        }
        $base_prod_info = get_base_product_info_for_customer_id($cust_id);
        $base_prod_alias = isset($base_prod_info['alias']) ? $base_prod_info['alias'] : '';
        if($base_prod_alias!=''){
            $ord_arr = get_order_dtl_of_lead($cust_id,null,false,'706');
            $len     = count($ord_arr);
            $lastOrderDate = ($len > 0) ? date('Y-m-d\T23:59:59.000P',strtotime($ord_arr[$len-1][1])) : null;            
            $post_arr   = array(
                            array('customerId'=>$cust_id,'licenseType'=>$license_type,'product'=>$base_prod_alias,
                                  'licenseExpiry'=>$expiry_date,'lastOrderDate'=>$lastOrderDate,
                                  'referenceId'=>$gosecure_ref_id)
                          );
            $cp_config  = get_connectplus_config();
            $post_url   = str_replace("{{customerId}}", $cust_id, $cp_config['cloud_domain']).$cp_config['gosecure_lic_post'];
            $cp_api_key = $cp_config['gosecure_lic_key'];
            $header_arr = array("Content-Type: application/json","x-api-key: $cp_api_key");
            do_curl_to_connectplus($cust_id, $post_url, json_encode($post_arr), $header_arr,"PUT");
        }
    }
}

/**
 * @param string $orderNo
 * @param string $fullfillmentNo
 * @param string $validity_date
 * 
 * @return int
 */
function create_installation_entry($orderNo,$fullfillmentNo,$validity_date=''){
    $full_no= (int)$fullfillmentNo;
    $que = " select GOD_LEAD_CODE,GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW,GPM_LICENSE_TYPE, ".
           " if(GPM_LICENSE_TYPE=1,GPM_DEFAULT_ASS_PERIOD,GPM_SUBSCRIPTION_PERIOD) valid_days ".
           " from gft_order_hdr ".
           " join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
           " join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
           " where GOP_ORDER_NO='$orderNo' and GOP_FULLFILLMENT_NO=$full_no and god_order_splict=0 ";
    $res = execute_my_query($que);
    $install_id = 0;
    if($row = mysqli_fetch_array($res)){
        $pcode = $row['GOP_PRODUCT_CODE'];
        $pskew = $row['GOP_PRODUCT_SKEW'];
        $lcode = $row['GOD_LEAD_CODE'];
        $valid_days = $row['valid_days'];
        $today = date('Y-m-d');
        $ins_arr = array(
            'GID_EMP_ID'            => SALES_DUMMY_ID,
            'GID_SALESEXE_ID'       => SALES_DUMMY_ID,
            'GID_ORDER_NO'          => $orderNo,
            'GID_FULLFILLMENT_NO'   => $fullfillmentNo,
            'GID_LIC_ORDER_NO'      => $orderNo,
            'GID_LIC_FULLFILLMENT_NO'=>$fullfillmentNo,
            'GID_PRODUCT_CODE'      => $pcode,
            'GID_LIC_PCODE'         => $pcode,
            'GID_HEAD_OF_FAMILY'    => $pcode,
            'GID_PRODUCT_SKEW'      => $pskew,
            'GID_LIC_PSKEW'         => $pskew,
            'GID_CREATED_TIME'      => date('Y-m-d H:i:s'),
            'GID_INSTALL_DATE'      => $today,
            'GID_STATUS'            => 'A',
            'GID_LEAD_CODE'         => $lcode,
            'GID_VALIDITY_DATE'     => $validity_date!=''?$validity_date:add_date($today, $valid_days),
            'GID_EXPIRE_FOR'        => ($row['GPM_LICENSE_TYPE']=='1') ? '1' : '2'
        );
        $install_id = array_insert_query("gft_install_dtl_new", $ins_arr);
    }
    return $install_id;
}

/**
 * @param string $activation
 * @param string $addon_code
 * @param string $addon_name
 * @param string $addon_qty
 * @param string $valid_period
 * @param string $order_no
 * @param string $expiry_type
 * @param string $lic_type
 * @param string $edition
 * @param string $vtype
 * @param string $vmethod
 * @param string $is_free
 * @param string $prod_alias
 * @param string $send_url
 * @param string $addon_category
 * @param string $stat
 * @param string $pskew
 * @param string $cp_token
 * @param string $nf_token
 * @param string $free_license
 * @param string $grace_period
 * @param string $is_pilot
 * @param string $lic_agreement
 * @param string $download_url
 * @param string[int] $custom_lic_arr
 * @param string $configure_url
 * 
 * @return string[string]
 */
function get_addon_formatted_response($activation,$addon_code,$addon_name,$addon_qty,$valid_period,$order_no,
            $expiry_type='',$lic_type='',$edition='',$vtype='',$vmethod='',$is_free='',$prod_alias='',$send_url='',
            $addon_category='',$stat='A',$pskew='',$cp_token='',$nf_token='',$free_license='N',$grace_period='',
    		$is_pilot='N',$lic_agreement='NA',$download_url='',$custom_lic_arr=array(),$configure_url=''){
    $custom_lic_count = count($custom_lic_arr);
    $arr = array(
        'ACTIVATION'        => $activation,
        'PCODE'             => $addon_code,
        'PRODUCT_NAME'      => $addon_name,
        'QTY'               => $addon_qty,
        'VALIDITY_PERIOD'   => $valid_period,
        'ORDERNO'           => $order_no,
        'EXPIRY_TYPE'       => $expiry_type,
        'LIC_TYPE'          => $lic_type,
        'EDITION'           => $edition,
        'VALIDATIAN_TYPE'   => ($configure_url!='') ? "Configure" : $vtype,
        'VALIDATIAN_METHOD' => $vmethod,
        'IS_FREE'           => ($custom_lic_count > 0) ? "" : $is_free,
        'PRODUCT_ALIAS'     => $prod_alias,
        'URL'               => $send_url,
        'ADDON_CATEGORY'    => $addon_category,
        'STATUS'            => $stat,
        'PSKEW'             => $pskew,
        'CONNECTPLUS_TOKEN' => $cp_token,
        'NOTIFICATION_TOKEN'=> $nf_token,
        'FREE_LICENSE'      => $free_license,
        'GRACE_PERIOD'      => $grace_period,
        'IS_PILOT'          => $is_pilot,
        'LICENSE_AGREEMENT' => $lic_agreement,
        'DOWNLOAD_URL'      => $download_url,
        'CUSTOM_LICENSE_COUNT'=>$custom_lic_count,
        'CUSTOM_LICENSE'    => $custom_lic_arr,
        'CONFIGURE_URL'     => $configure_url 
    );
    return $arr;
}

/**
 * @param string $pcode
 * @param string $lead_code
 * @param string $response_for
 * @param string $base_prod_id
 * 
 * @return string[int][string]
 */
function get_addon_features_license_details($pcode,$lead_code,$response_for='pos',$base_prod_id=''){
    $ret_arr = /*. (string[int][string]) .*/array();
    if($pcode=='526'){
        $q1 = " select GAF_NAME,GAF_CODE,GAM_PRODUCT_SKEW,GAI_NO_OF_DEVICE,GAI_VALIDITY_DATETIME,GAI_INSTALLED_DATETIME, ".
              " GPM_VALIDATIAN_METHOD,GPM_VALIDATIAN_TYPE,'YES' activation, 0 is_trial,GPM_LICENSE_TYPE ".
              " from gft_addon_feature_master join gft_addon_feature_skew_mapping on (GAM_FEATURE_ID=GAF_ID) ".
              " join gft_product_master on (GPM_PRODUCT_CODE=GAM_PRODUCT_CODE and GPM_PRODUCT_SKEW=GAM_PRODUCT_SKEW) ".
              " join gft_addon_feature_install_dtl on (GAI_LEAD_CODE='$lead_code' and GAI_PRODUCT_CODE=GAM_PRODUCT_CODE and GAI_PRODUCT_SKEW=GAM_PRODUCT_SKEW) ".
              " where GAM_PRODUCT_CODE='$pcode' and GAI_STATUS='A' group by GAF_CODE ";
        
        $q2 = " select GAF_NAME,GAF_CODE,GAM_PRODUCT_SKEW,GAI_NO_OF_DEVICE,GAI_VALIDITY_DATETIME,GAI_INSTALLED_DATETIME, ".
              " GPM_VALIDATIAN_METHOD,GPM_VALIDATIAN_TYPE,'NO' activation,sum(if(GPM_LICENSE_TYPE=3,1,0)) is_trial,GPM_LICENSE_TYPE ".
              " from gft_addon_feature_master join gft_addon_feature_skew_mapping on (GAM_FEATURE_ID=GAF_ID) ".
              " join gft_product_master on (GPM_PRODUCT_CODE=GAM_PRODUCT_CODE and GPM_PRODUCT_SKEW=GAM_PRODUCT_SKEW) ".
              " join gft_addon_product_map on (GAP_ADDON_PRODUCT_CODE=GAM_PRODUCT_CODE and GAP_PRODUCT_CODE='$base_prod_id' and GAP_FEATURE_CODE=GAF_CODE) ".
              " left join gft_addon_feature_install_dtl on (GAI_LEAD_CODE='$lead_code' and GAI_PRODUCT_CODE=GAM_PRODUCT_CODE and GAI_PRODUCT_SKEW=GAM_PRODUCT_SKEW) ".
              " where GAM_PRODUCT_CODE='$pcode' and GAI_LEAD_CODE is null group by GAF_CODE ";
        
        $main_que = "select * from ($q1 union $q2) t1 group by GAF_CODE ";
        $r1 = execute_my_query($main_que);
        while ($d1 = mysqli_fetch_array($r1)){
            $action_flag = ((int)$d1['is_trial'] > 0) ? "Try & Buy" : "Buy";
            $activation = $d1['activation'];
            $addon_name = $d1['GAF_NAME'];
            $addon_code = $d1['GAF_CODE'];
            $valid_date = (string)$d1['GAI_VALIDITY_DATETIME'];
            $lic_count  = (string)$d1['GAI_NO_OF_DEVICE'];
            if($response_for=='app'){
                if($activation=='YES'){
                    $lic_type   = ((int)$d1['GPM_LICENSE_TYPE']=='3') ? "trial" : "live";
                    $ret_arr[] = array(
                        "name"=>$addon_name,
                        "code"=>$addon_code,
                        "expiryDate"=>$valid_date,
                        "installedDate"=>$d1['GAI_INSTALLED_DATETIME'],
                        "licenseCount"=>$lic_count,
                        "userType"=>$lic_type
                    );
                }
            }else if($response_for=='pos'){
                $ret_arr[] = get_addon_formatted_response($activation, $addon_code, $addon_name, $lic_count,
                    $valid_date, "","2","","Starter",$d1['GPM_VALIDATIAN_TYPE'],$d1['GPM_VALIDATIAN_METHOD'],$action_flag,"","","","A",$d1['GAM_PRODUCT_SKEW']);
            }
        }
    }
    return $ret_arr;
}

/**
 * @param string $lead_code
 * @param string $pcode
 * @param string $pskew
 * @param string $client_qty
 * 
 * @return void
 */
function update_feature_install_dtl($lead_code,$pcode,$pskew,$client_qty){
    $que1 = " select GAM_FEATURE_ID,GPM_SUBSCRIPTION_PERIOD,GFT_SKEW_PROPERTY,GPM_REFERER_SKEW ".
            " from gft_addon_feature_skew_mapping join gft_addon_feature_master on (GAF_ID=GAM_FEATURE_ID) ".
            " join gft_product_master on (GPM_PRODUCT_CODE=GAM_PRODUCT_CODE and GPM_PRODUCT_SKEW=GAM_PRODUCT_SKEW) ".
            " where GAM_PRODUCT_CODE='$pcode' and GAM_PRODUCT_SKEW='$pskew' ";
    $res1 = execute_my_query($que1);
    $today_time = date('Y-m-d H:i:s');
    while($row1 = mysqli_fetch_array($res1)){
        $feature_id = $row1['GAM_FEATURE_ID'];
        $subsc_days = $row1['GPM_SUBSCRIPTION_PERIOD'];
        $sku_prop   = $row1['GFT_SKEW_PROPERTY'];
        $ref_sku    = $row1['GPM_REFERER_SKEW'];
        if($sku_prop=='26'){
            $rup = " update gft_addon_feature_install_dtl set GAI_NO_OF_DEVICE='$client_qty',GAI_VALIDITY_DATETIME=(if(GAI_VALIDITY_DATETIME > now(),GAI_VALIDITY_DATETIME,now()) + INTERVAL $subsc_days DAY) ".
                   " where GAI_LEAD_CODE='$lead_code' and GAI_PRODUCT_CODE='$pcode' and GAI_PRODUCT_SKEW='$ref_sku' ";
            execute_my_query($rup);
        }else{
            $que2 = " select GAI_ID from gft_addon_feature_install_dtl ".
                " where GAI_LEAD_CODE='$lead_code' and GAI_PRODUCT_CODE='$pcode' and GAI_PRODUCT_SKEW='$pskew' ";
            $res2 = execute_my_query($que2);
            if($row2 = mysqli_fetch_array($res2)){
                $prime_id = $row2['GAI_ID'];
                $up1 = " update gft_addon_feature_install_dtl set GAI_NO_OF_DEVICE=GAI_NO_OF_DEVICE+$client_qty where GAI_ID='$prime_id' ";
                execute_my_query($up1);
            }else{
                //mark existing trial row to 'U'
                $up2 =" update gft_addon_feature_install_dtl join gft_product_master on (GPM_PRODUCT_CODE=GAI_PRODUCT_CODE and GPM_PRODUCT_SKEW=GAI_PRODUCT_SKEW) ".
                    " set GAI_STATUS='U' where GAI_LEAD_CODE='$lead_code' and GAI_FEATURE_ID='$feature_id' and GPM_LICENSE_TYPE=3 and GAI_STATUS='A' ";
                execute_my_query($up2);
                $install_datetime   = $today_time;
                $validity_datetime  = date('Y-m-d H:i:s',strtotime("+$subsc_days days"));
                $ins_arr = array(
                    'GAI_LEAD_CODE'         => $lead_code,
                    'GAI_PRODUCT_CODE'      => $pcode,
                    'GAI_PRODUCT_SKEW'      => $pskew,
                    'GAI_FEATURE_ID'        => $feature_id,
                    'GAI_NO_OF_DEVICE'      => $client_qty,
                    'GAI_INSTALLED_DATETIME'=> $install_datetime,
                    'GAI_VALIDITY_DATETIME' => $validity_datetime,
                    'GAI_STATUS'            => 'A',
                );
                array_insert_query("gft_addon_feature_install_dtl", $ins_arr);
            }
        }
    }
    if(mysqli_num_rows($res1) > 0){
        post_instock_custom_license($lead_code);
    }
}

/**
 * @param string $lead_code
 */
function warehouse_provisioning($lead_code){
    $corp_id = get_corporate_customer_for_client($lead_code);
    $que1 = " select GOP_PRODUCT_CODE,GOP_PRODUCT_SKEW from gft_order_hdr ".
            " join gft_order_product_dtl on (GOP_ORDER_NO=GOD_ORDER_NO) ".
            " join gft_product_master on (GPM_PRODUCT_CODE=GOP_PRODUCT_CODE and GPM_PRODUCT_SKEW=GOP_PRODUCT_SKEW) ".
            " where GPM_LICENSE_TYPE!=3 and GOP_PRODUCT_CODE in (200,500) and GPM_PRODUCT_TYPE!=8 and GOD_LEAD_CODE='$corp_id' and GOD_ORDER_STATUS='A' ";
    $res1 = execute_my_query($que1);
    if($row1 = mysqli_fetch_array($res1)){
        $base_pcode = $row1['GOP_PRODUCT_CODE'];
        $prod_col_name = get_product_column_name_from_code($base_pcode."-".substr($row1['GOP_PRODUCT_SKEW'], 0,4));
        if($prod_col_name==""){
            return;
        }
        $base_que = " select ba.GPV_VERSION,ba.GPV_WH_BASE_SCHEMA_URL from gft_install_dtl_new ".
                    " join gft_product_version_master hq on (hq.GPV_PRODUCT_CODE=GID_LIC_PCODE and hq.GPV_VERSION=GID_CURRENT_VERSION) ".
                    " join gft_product_version_master ba on (ba.GPV_PRODUCT_CODE=$base_pcode and ba.GPV_VERSION=hq.$prod_col_name) ".
                    " where GID_LEAD_CODE='$lead_code' and GID_STATUS!='U' and GID_LIC_PCODE=300 order by GID_VALIDITY_DATE ";
        $base_res = execute_my_query($base_que);
        if($base_row = mysqli_fetch_array($base_res)){
            $post_data = array(
                'user'=>$lead_code,
                'product'=>'warehouse',
                'base_schema'=>$base_row['GPV_WH_BASE_SCHEMA_URL'],
                'product_version'=>$base_row['GPV_VERSION']
            );
            $cp_config = get_connectplus_config();
            $header_arr = array('Content-Type:application/json','X-Api-Key:'.$cp_config['warehouse_api_key']);
            do_curl_to_connectplus($lead_code, $cp_config['warehouse_url'], json_encode($post_data), $header_arr);
        }
    }
}

/**
 * @param string $lic_no
 * @param string $cust_id
 * 
 * @return string[string]
 */
function register_peergroup_entry_for_nettrade($lic_no,$cust_id,$ph_no){
    $parsed_data = /*. (string[string]) .*/array();
    $split_order_no = substr($lic_no,0,5)."-".substr($lic_no,5,5)."-".substr($lic_no,10,5)."-".substr($lic_no,15,4);
    $cust_res = execute_my_query("select GLH_CUST_NAME, GLH_CUST_STREETADDR2 from gft_lead_hdr where GLH_LEAD_CODE='$cust_id'");
    if($data1 = mysqli_fetch_array($cust_res)){
        $cust_name 		= $data1['GLH_CUST_NAME'];
        $cust_location 	= $data1['GLH_CUST_STREETADDR2'];
        $post_str 		= "purpose=get_store_url&orderNo=$split_order_no&customerName=$cust_name&customerLocation=$cust_location&customerId=$ph_no&csEnabled=1&entryFor=HQNT";
        $ch = curl_init(get_samee_const("Peergroup_Portcheck_Url"));
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_str);
        $response_json = (string)curl_exec($ch);
        curl_close($ch);
        $parsed_data = /*. (string[string]) .*/json_decode($response_json,true);
    }
    return $parsed_data;
}

/**
 * @param string $lic_no
 * @param string $id
 * @param string $name
 * @param string $location
 * @param string $uuid
 * 
 * @return string
 */
function register_peergroup_entry_for_webreporter($lic_no,$id,$name,$location,$uuid){
    $split_order_no = substr($lic_no,0,5)."-".substr($lic_no,5,5)."-".substr($lic_no,10,5)."-".substr($lic_no,15,4)."-WR";
    $post_str = "purpose=get_store_url&orderNo=$split_order_no&customerName=$name&customerLocation=$location&customerId=$id&uuid=$uuid";
    $ch = curl_init(get_samee_const("Peergroup_Portcheck_Url"));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_str);
    $response_json = (string)curl_exec($ch);
    curl_close($ch);
    return $response_json;
}

?>
