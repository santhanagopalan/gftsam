<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: ". gmdate("D, d M Y H:i:s", time() + 3600) . " GMT");
echo <<<END
<script>location.href="login.php";</script>
END;
?>
