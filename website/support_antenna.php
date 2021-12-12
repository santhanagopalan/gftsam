<?php
header('Content-Type: text/xml');
$product=$product_received=(isset($_GET['product'])?($_GET['product']):0);
$support_antenna = "";
if($product=='200060'){
	$support_antenna .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><ajax-response><response>";
		$support_antenna .= "<item><product>200060</product>" .
			"<reason>Year Begin Process Video</reason>" .
			"<remarks><p>To have a very smooth year begin process in 2020-2021, please do update our latest version 6.3.3.2. It is mandatory to update the latest version to have hassle free year begin</p> <p>To do the year begin process , watch this video https://bit.ly/2ISKwuM</p> <p>Wishing you the Double Increase in sales in 2020-21</p></remarks>" .
			"<signal>5</signal>" .
			"</item>";
	$support_antenna .= " </response></ajax-response>";	
	echo $support_antenna;
exit;
}
if($product=='300030'){
        $support_antenna .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><ajax-response><response>";
                $support_antenna .= "<item><product>300030</product>" .
                        "<reason>Available</reason>" .
                        "<remarks><p>Download myGoFrugal app and raise tickets to get faster support for our Assure Care.Assuring you to give best service all the time.</p></remarks>" .
                        "<signal>5</signal>" .
                        "</item>";
        $support_antenna .= " </response></ajax-response>";
        echo $support_antenna;
exit;
}
if($product=='500070'){
	$support_antenna .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><ajax-response><response>";
		$support_antenna .= "<item><product>500070</product>" .
			"<reason>Watch this Video</reason>" .
			"<remarks>Alert!! Make Sure Your Database is Safe by using GoSecure Cloud Backup To know more about this, Please watch our self help video, https://www.youtube.com/watch?v=emu1b1LpC0M Happy Retailing</remarks>" .
			"<signal>5</signal>" .
			"</item>";
	$support_antenna .= " </response></ajax-response>";	
	echo $support_antenna;
exit;
}
if($product=='500065'){
	$support_antenna .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><ajax-response><response>";
		$support_antenna .= "<item><product>500065</product>" .
			"<reason>Watch this Video</reason>" .
			"<remarks><P><B>Alert!! Make Sure Your are ready for the Next Financial Year Begin Process(2020 to 2021) </B> </P> <P>To know more about this, Please watch our self help video. For Self Help, <a href='https://youtu.be/i2i-IDjXtPE'>Click here</a></P> <P>Happy Retailing</P></remarks>" .
			"<signal>5</signal>" .
			"</item>";
	$support_antenna .= " </response></ajax-response>";	
	echo $support_antenna;
exit;
}
$support_antenna .= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><ajax-response><response>";
		$support_antenna .= "<item><product>0</product>" .
			"<reason>Emergency mode</reason>" .
			"<remarks>Hi, Due to Heavy rainfall in Chennai. We are working for Emergency support. Kindly reach us through Live chat for only Emergency Kind of Support.</remarks>" .
			"<signal>4</signal>" .
			"</item>";
	$support_antenna .= " </response></ajax-response>";	
	echo $support_antenna;
exit;
?>
