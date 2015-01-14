#!/usr/bin/php
<?php

$tabidx = 4;
require_once('extras_config.inc');
require_once('wixUtility.class');

$help_anchor = '';

$onload_jsfun = 'onload();'

?>

<?php include('extras_fbegin.inc'); ?>
<!-- begin /ZONE: INDEX -->
<?php
    $l = &$lang['extras']['packs'];
    displayIndexPage (array( // {url,icon,desc}
                       (true?
                        array(''.$nLUS,
                              '../extras/images/icon_construction.gif',
                              $lang['extras']['ongoing']):
                        array()),
                     ));
?>
<!-- end   /ZONE: INDEX -->
<?php include('NEW_fend.inc'); ?>