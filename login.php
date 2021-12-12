<?php
//Added by santu for https redirect
if( isset($_SERVER['HTTP_HOST']) && (in_array(strtolower($_SERVER['HTTP_HOST']), array('sam.gofrugal.com','labtest.gofrugal.com','samtestmbe.gofrugal.com'))) ){
    if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $redirect);
        exit();
    }
}


require_once(__DIR__ ."/login_check.php"); 
$ver_dtl		= get_version_dtl();
$rel_note_link	= isset($ver_dtl[0][2])?$ver_dtl[0][2]:'';
session_destroy();
header('Location: samui/#/login_new');
exit();
?>
<!DOCTYPE html>
<html>
<head> 
<title>GoFrugal Technologies</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="CSS/login_style.css">
<!-- <script src="bootstrap/js/bootstrap.min.js" ></script> -->
<script src="js/jquery-3.1.1.min.js" ></script>
<script type="text/javascript" src="js/js_util_function.js"></script>

<script type="text/javascript">
//console.log(window.innerHeight);
$(document).ready(function(){
	$('body,.mb_right_par,.row').css('height', window.innerHeight+'px');
	$('.container-fluid,.row,#bg_img').css('height', '100%');
	$('.container-fluid,.row,#bg_img').css('height', '100%');
});

function resolutioncheck(){
	var correctwidth=1024;
	var correctheight=768;
	if (screen.width!=correctwidth||screen.height!=correctheight){
		document.write("<center><span>This webpage is best viewed with screen resolution"
	                  +correctwidth+"*"+correctheight+". Your current resolution is "+screen.width+"*"+screen.height+
	                  ". <br> If possible, please change the resolution!<//span><//center>")
	}
}
var isGecko=false;
var navVerNumber="0";
var theAgent="";
function browsercheck(){
	//Detect IE5.5+
	version=0;
	if(navigator.appVersion.indexOf("MSIE")!=-1 ){
		temp=navigator.appVersion.split("MSIE");
		version=parseFloat(temp[1]);
		if (version < 5.5) {
			alert("WARNING: You are using IE version "+ version + ". We recommend you to upgrade IE to 6.0 or higher version");
			document.getElementById("browser_error").innerHTML="Please Use IE to 6.0 or higher version !"
		}
	}
	theAgent=navigator.userAgent.toLowerCase();
	isKhtml = (theAgent.indexOf("khtml") != -1);
	isOpera = (theAgent.indexOf("opera") != -1);
	isGecko = !isKhtml && !isOpera && (theAgent.indexOf("gecko/") != -1);
	if ( isGecko ){
		var i = theAgent.indexOf("rv:");
    	navVerGecko = "";
    	if ( i >= 0 ){
			var c, j;
			for (var j = i+3; j < theAgent.length; ++j ){
				c = theAgent.charAt(j);
				if ( (("0"<=c)&("9">=c)) || ("."==c) || (("a"<=c)&("z">=c)) )
					navVerGecko += c;
				else
					break;
			}
		}
		navVerGecko = (""==navVerGecko) ? "0" : navVerGecko;
		navVerNumber = parseFloat(navVerGecko);
		//navVerNumber = parseFloat(navigator.appVersion);
		if (navVerNumber < 1.6){
			alert("WARNING: You are using Mozilla (Gecko) version "+navVerNumber+ ". We recommend you to upgrade Mozilla(Gecko) to 1.7 or higher version");
			document.getElementById("browser_error").innerHTML="Please Use Mozilla(Gecko) to 1.7 or higher version !"
		}
	}
}
browsercheck();
</script>
</head><body>
<span id="browser_error"></span>
<div class="container-fluid">
<div class="row row_res">
	<div class="col-xs-12 col-sm col-md col-lg col-xl mb_left">
		<img src="images/login/BG.jpg" height="100%" width="100%" class="img-fluid" id="bg_img">
		<div class="centered wht_txt text-center pos_desk">
			<img src="images/login/SAM.png" class="img-fluid sam_png">
			<h4 class="">LET'S COLLABORATE TO WIN</h4>
			<p>
				Tell me and I forget.<br>
				Show me and I might remember.<br>
				Involve me(in joint work) and I learn.<br>
				<b style="font-style: italic;color: #32bff1;">- Benjamin Franklin</b>
			</p>	
			<div class="text-center sam_btn_dv" >
				<a href="<?php echo $rel_note_link; ?>" target="_blank">
					<button class="sam_fea_btn"><span>NEW FEATURES</span></button>
				</a>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm col-md col-lg col-xl mb_right_par">
		<div class="centered2 mb_right">
			<div class="text-xs-center"><a href="https://www.gofrugal.com/"><img src="images/Logo_New_Final.svg"  width="50%"></a></div><br>
				<h5 class="clr_dark text-xs-center"> Hey there! Welcome back</h5>
				<p class="clr_gray text-xs-center">Enter your sign in details below</p>
				<div class="err_txt" id="cmn_err"><?php echo $err_msg;?></div>
				<form action="login.php" method="post" name="loginform" id="loginform" onsubmit="javascript:submit_login();return false;">
					<div class="form-group">
						<span><img src="images/login/User.svg" height="18px" width="18px"><label class="form_lbl email_lbl"  id="">USER NAME</label></span>
						<input type="text" name="juser_name" id="juser_name" placeholder="User name" class="form-control email">
						<div class="err_txt" id="usr_err"></div>
					</div>
					<div class="form-group">
						<span><img src="images/login/password.svg"><label class="form_lbl pswd_lbl" id="">PASSWORD</label></span>
						<div class="eye_dv">
							<input type="password" name="juser_password" id="juser_password" placeholder="Password" class="form-control pswd">
							<img src="images/login/eye-1.png" width="20px" height="20px" onclick="eye(1)" class="eye_open">
							<img src="images/login/eye-2.png" width="20px" height="20px" onclick="eye(2)" class="eye_close">
							<div class="err_txt" id="pwd_err"></div>
						</div>
						
					</div>
					<div class="sam_blue font_14 frgt_p">
						<input type="hidden" name="module" value="Users">
						<input type="hidden" name="action" id="action" value="Authenticate">
						<input type="hidden" name="return_module" value="Users">
						<input type="hidden" name="return_action" value="Login">
						<input type="hidden" name="user_password" id="user_password"  value="">
						<input type="hidden" name="user_name" id="user_name" value="">
						<a class="linkblacktxt float_r" href="JavaScript:void(0);" onclick="javascript:requestpassword();">Forgot Password?</a>
					</div><br><br>
					<div>
						<input title="Login [Alt+L]" accesskey="Login [Alt+L]" class="btn sam_blue_btn" name="Login" value="SIGN IN" type="submit" >
					</div>
				</form>
			</div>
		</div>
	</div>
</div>	
<script type="text/javascript">
function submit_login(){
	var objForm = document.loginform;
	var strUsr=document.getElementById("juser_name").value;
	var strPwd=document.getElementById("juser_password").value;
	var strErr="";
	if(document.getElementById("browser_error").innerHTML!=''){
		strErr +=document.getElementById("browser_error").innerHTML+"\n";
	}
	if (strUsr == '' || trim(strUsr)==null){
		$('#usr_err').text('Please enter the User name.');
		$('#pwd_err').text('');
		return false;
	}else if (<?php echo ($skip_password==true?"false":"true") ?> && (strPwd.length <= 0 || trim(strPwd)==null)){
			$('#usr_err').text('');
			$('#pwd_err').text('Please enter the Password.');
			return false;
	}else if(strErr.length>0){
		$('#cmn_err').text(strErr);
		return false;
	}
	document.getElementById("user_password").value=strPwd;
	document.getElementById("user_name").value=strUsr;
	document.getElementById("juser_name").value="";
	document.getElementById("juser_password").value="";
	objForm.submit();
}
function requestpassword(){
	if(document.getElementById("juser_name").value==""){
		alert("Please Enter the User name to reset the password! ")
		return false;	
	}
	var passwordreset = confirm('Do you want to reset your password?');
	if(passwordreset==true){
	document.getElementById("action").value="Passwordchange";
	document.getElementById("user_name").value=document.getElementById("juser_name").value;
	document.loginform.submit();
	return true;
	}
	else{
		return false;
	}
}
document.getElementById("juser_name").focus();
</script>
<script type="text/javascript">
		$('.email').focusin(function(){
			$('.email_lbl').css('color','#3aa3f9');
		});
		$('.email').focusout(function(){
			var email = $(this).val();
			if(email == ''){
				$('.email_lbl').css('color','#6a6a6a');
			}else{
				$('.email_lbl').css('color','#3aa3f9');
			}
		});
		$('.pswd').focusin(function(){
			$('.pswd_lbl').css('color','#3aa3f9');
		});
		$('.pswd').focusout(function(){
			var pswd = $(this).val();
			if(pswd == ''){
				$('.pswd_lbl').css('color','#6a6a6a');
			}else{
				$('.pswd_lbl').css('color','#3aa3f9');
			}
		});
		function eye(a){
			if(a == 1){
				$('.eye_open').hide();
				$('.eye_close').show();
				$(".pswd").prop('type', 'text');
			}else{
				$('.eye_close').hide();
				$('.eye_open').show();	
				$(".pswd").prop('type', 'password');
			}
		}
	</script>
</body></html>
