#!/usr/bin/php
<?php

$tabidx = 5;
require_once('extras_config.inc');
$head_js = &$gray_js;

$help_anchor = '';

$onload_jsfun = 'onloadgray();'
?>

<?php include('extras_fbegin.inc'); ?>
<!-- begin /ZONE: INDEX -->
<?php
    $l = &$lang['extras']['packs'];
    displayIndexPage (array( // {url,icon,desc}
                        array(''.$nLUS,
                              '../extras/images/icon_construction.gif',
                              $lang['extras']['ongoing'],
                              false),
                     ));
?>
<!-- end   /ZONE: INDEX -->
<?php include('NEW_fend.inc'); ?>