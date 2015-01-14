#!/usr/bin/php
<?php

$tabidx = 2;
require_once('extras_config.inc');

$help_anchor = '';

$onload_jsfun = 'onload();'

?>

<?php include('extras_fbegin.inc'); ?>
<!-- begin /ZONE: INDEX -->
<?php
   $l = &$lang['extras']['packs'];
   $ctcs=$webHooks->GetDownloadShare();
   displayIndexPage (array( // {url,icon,desc,lock,new}
                        array('extras_manage.php'.$nLUS,
                              '../extras/images/icon_packages.gif',
                              $lang['extras']['manager'],
                              false,
                              count(glob('../extras/packs/*.new'))),
                        array('extras_system_infos.php'.$nLUS,
                              '../extras/images/icon_info.gif',
                              $lang['extras']['title'],
                              false),
                        array('/ctcs/d_index.php'.$nLUS,
                              '../extras/images/icon_ctcs.gif',
                              $lang['extras']['ctcs'],
                              empty($ctcs)),
                        array('/cpsync/index.php'.$nLUS,
                              '../extras/images/icon_cpsync.gif',
                              $lang['extras']['cpsync'],
                              false),
                        array('/fpkmgr/index.php'.$nLUS,
                              '../extras/images/icon_fpm.gif',
                              $l['featurepack']['package'],
                              !file_exists('../fpkmgr/index.php')),
                        array('../share/index.php'.$nLUS,
                              '../extras/images/icon_extplorer.gif',
                              $l['extplorer']['package'],
                              !file_exists('../share/_version')),
                     ));
?>
<!-- end   /ZONE: INDEX -->
<?php include('NEW_fend.inc'); ?>