<?php
/*. require_module 'standard'; .*/
/*. require_module 'mysql'; .*/

require_once __DIR__ . '/dbcon.php';
require_once __DIR__ . '/access_util.php';
$help_link = get_help_link(basename($me));


echo<<<END
<script type="text/javascript" src="{$base_relative_path}js/tooltip.js"></script>
<script type="text/javascript">tooltip_init();</script>
<script type="text/javascript">
/********** POPUP ************/
var http=getHTTPObject();
var isWorking = false;
function handleErrors(msg,me,lno){
	return true;
}
clickRowImpl();
</script>
END;

if(!isset($_GET['train_redir'])){
	$train_redir=0;
}else{
	$train_redir=(int)$_GET['train_redir'];
}


if ($train_redir != 1){
echo<<<END
<script type="text/javascript">training_redir();</script>
END;
}

$deltaTime=getDeltaTime();
track_access($deltaTime);
$GOOGLE_ANALYTICS=get_samee_const("GOOGLE_ANALYTICS");
$MAILSERVER_HOST_NAME=get_samee_const("MAILSERVER_HOST_NAME");

/**
 * @param float $deltaTime
 *
 * @return void
 */
function checkAndDisplayFooter($deltaTime){
global $dont_display_footer,$help_link;
if($dont_display_footer!=true){
$help='';
if($help_link!=''){
	$help_link = "http://help-sam.gofrugal.com/$help_link";
	$help = "<i class='fas fa-question-circle' style='color:#263238;font-weight:bold;margin-right:5px;'></i><a title='Click for Help' target='_blank' href='$help_link'>Help Link</a>";
}

echo<<<END
<div class="footer-wrap">	
	<a class='footer-element' href="http://www.gofrugal.com" target="_blank"><i class="far fa-copyright" style="margin-right:5px;"> </i>www.gofrugal.com</a>
    <span  class='footer-element'>All rights reserved</span>
	<span  class='footer-element'>Server Response Time: $deltaTime Seconds</span>      
	<!--<a title="click here to post a  News in scrolling bar" href="#" onclick="javascript:call_popup('raymedi_news_today.php',3);">Post a News</a>
	<a title="click here to send SMS to our Employees" href="#" onclick="javascript:call_popup('sendasms.php',5);">Send SMS</a>  --> 
	<script type="text/javascript">
	document.write("<span  class='footer-element'>Page Load Time :", (" " + ( (new Date()-Start)/1000 )+ " Seconds.</span>"));	
	</script>	
	$help
</div>
END;
}
}

closeDBConnection();
checkAndDisplayFooter($deltaTime);

//NOTE: $show_google_analytics defined in dbcon.php
if($_SERVER['SERVER_NAME']===$MAILSERVER_HOST_NAME and $GOOGLE_ANALYTICS!=='' and $show_google_analytics === true){
echo<<<END
$GOOGLE_ANALYTICS 
END;
}
echo<<<END
</body></html>
END;

?>
<script type="text/javascript">
window.close();
</script>