<?php

$state=isset($_REQUEST['state'])?$_REQUEST['state']:'';

if ($state == '100200301'){
   //Special request from zohoOauthTool.php 
 
  $code=$_REQUEST['code'];

  echo "<br>Code: $code ";

echo <<<END
   <form action="zohoOauthTool.php" method="GET">
     <input type="hidden" name="code" value="$code">
     <input type="hidden" name="cmd" value="get_access_token">
     <br>
     
     <br><input type="submit" value="submit" name="submit">
   </form>
END;
} 


?>
