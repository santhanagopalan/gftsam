<?php
//session
session_start();

$cmd=isset($_REQUEST['cmd'])?$_REQUEST['cmd']:'';

$redirect_uri="https://sam.gofrugal.com/oauth/callback.php";
$zoho_auth_url="https://accounts.zoho.com/oauth/v2/auth";
$zoho_token_url="https://accounts.zoho.com/oauth/v2/token";

$state="100200301"; //TODO: Hardcoded

if ($cmd ==''){
	die("invalid command param");
}

$action=$_SERVER['PHP_SELF'];

if ($cmd == 'init'){
	doInitAction();
}else if ($cmd == 'grant_request'){
	doGrantRequestAction();
}else if ($cmd == 'get_access_token'){
        doGetAccessToken();
}else{
	die("Invalid command value");
}

function getRequestMandatoryValue($var){
	$val= isset($_REQUEST[$var])?$_REQUEST[$var]:'';

	if ($val == ''){
		die("invalid $var ");
	}

	return $val;

}

function doGrantRequestAction(){

	global $zoho_auth_url;
	global $redirect_uri;
	global $state;

	$client_id = getRequestMandatoryValue('client_id');
	$scope     = getRequestMandatoryValue('scope');
	$client_secret = getRequestMandatoryValue('client_secret');

        $_SESSION['client_id']=$client_id;
        $_SESSION['client_secret']=$client_secret;
        $_SESSION['scope']=$scope;


//access_type=offline - for generating the refresh token
echo <<<END
		<form action="$zoho_auth_url" method="GET">
		<input type="hidden" name="response_type" value="code">
		<input type="hidden" name="client_id" value="$client_id">
		<input type="hidden" name="redirect_uri" value="$redirect_uri">
		<input type="hidden" name="state" value="$state">
		<input type="hidden" name="access_type" value="offline">
		<input type="hidden" name="scope" value="$scope">

		<br><br><input type=submit name="submit" value="Grant Request">
		</form>
END;

}

function doInitAction(){
	global $action;

echo <<<END
		<form action="$action" method="GET">
		<input type=hidden name="cmd" value="grant_request">
		<br>
		<br><br>client id: <input type=text name="client_id" value="">

		<br><br>client secret: <input type=text name="client_secret" value="">

		<br><br>scope: <input type=text name="scope" value="">

		<br>
		<input type=submit value="submit" name="submit">

		</form>
END;

}

function doGetAccessToken(){
     global $action;
     global $zoho_token_url;
     global $redirect_uri;

     $code = isset($_REQUEST['code'])?$_REQUEST['code']:'';

     if ($code == ''){
	     die("Invalid code value");
     }

     $client_id = isset($_SESSION['client_id'])?$_SESSION['client_id']:'';
     if ($client_id == ''){
	     die("Cannot able to get the client_id from session");
     }

     $client_secret = isset($_SESSION['client_secret'])?$_SESSION['client_secret']:'';
     if ($client_secret == ''){
	     die("Cannot able to get the client_secret from session");
     }

     $scope= isset($_SESSION['scope'])?$_SESSION['scope']:'';
     if ($scope == ''){
	     die("Cannot able to get the scope from session");
     }

/*
code=1000.fadbca4c2be2f08b0ce82a54f4313.ba5325853af6f12a0f160
&grant_type=authorization_code
&client_id=1000.R2Z0WWOLFVMR287126QED3B4JWQ5EN
&client_secret=39c689de68c712fa5f1f06c3b1319ab98f59fa921b
&redirect_uri=https://www.zylker.com/oauthgrant
&scope=Desk.tickets.READ,Desk.basic.READ
*/


echo <<<END
    <form action="$zoho_token_url" method="POST">
     <input type="hidden" name="code" value="$code">
     <input type="hidden" name="grant_type" value="authorization_code">
     <input type="hidden" name="client_id" value="$client_id">
     <input type="text" name="client_secret" value="$client_secret">
     <input type="hidden" name="redirect_uri" value="$redirect_uri">
     <input type="hidden" name="scope" value="$scope">

<br><input type=submit name=submit value=submit>
    </form>

END;
}

?>
