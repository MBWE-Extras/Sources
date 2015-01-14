<?php

/**
 * @version $Id: NEW_fend.inc,v 1.1.2.3 2008/05/09 02:34:02 wiley Exp $
 * @author Wiley Li <wileyli@wistron.com.tw>
 * @copyright Copyright (c) 2004 Wistron Corporation.
 */

?>

        </div>

        <!-- Footer -->
        <div class="column span-18 prepend-1 last" id="footer">
        <?=$lang['copyright']?>
<?php
if(file_exists('/etc/.gui_is_in_debug_mode')) {
$gETimer = explode(' ', microtime());
$gETimer = $gETimer[1] + $gETimer[0];

echo '<p class="benchmark">';
printf("Created this page in <dfn>%f</dfn> seconds.", ($gETimer - $gSTimer));
echo "</p>\n";
}
?>
        </div>
    </div>
<?php include('extras_cookie.inc'); ?>
</body>
</html>
<?php @ob_end_flush(); ?>
