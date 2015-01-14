#!/usr/bin/php
<?php

$tabidx = 3;
require_once('extras_config.inc');
$head_js = $gray_js;

$help_anchor = '';

$onload_jsfun = 'onloadgray();'
?>

<?php include('extras_fbegin.inc'); ?>
<?php
    $l = &$lang['extras']['packs'];
    displayIndexPage (array( // {url,icon,desc,lock,new}
                         array('/extras/packs/noip/index.php'.$nLUS,
                               '../extras/images/icon_noip.gif',
                               $l['noip']['package'],
                               !file_exists('packs/noip/_version')),
                         array('/extras/packs/sfrbox/index.php'.$nLUS,
                               '../extras/images/icon_sfrbox.gif',
                               $l['sfrbox']['package'],
                               !file_exists('packs/sfrbox/_version')),
                         array('/extras/packs/rsync/index.php'.$nLUS,
                               '../extras/images/icon_rsync.gif',
                               $l['rsync']['package'],
                               !file_exists('packs/rsync/_version')),
                         array('/extras/packs/openvpn/index.php'.$nLUS,
                               '../extras/images/icon_openvpn.gif',
                               $l['openvpn']['package'],
                               !file_exists('packs/openvpn/_version')),
                         array('/extras/packs/transmission/index.php'.$nLUS,
                               '../extras/images/icon_transmission.gif',
                              $l['transmission']['package'],
                              !file_exists('packs/transmission/_version')),
                     ));
?>
<!-- end   /ZONE: INDEX -->
<?php include('NEW_fend.inc'); ?>
