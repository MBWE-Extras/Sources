#!/usr/bin/php
<?php

$tabidx = 2;
require_once('extras_config.inc');
$head_js = &$gray_js;

$help_anchor = '';

$onload_jsfun = 'onloadgray();'
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
                        array('extras_options.php'.$nLUS,
                              '../extras/images/icon_options.gif',
                              $l['extras']['options'],
                              false),
                        array('extras_system_infos.php'.$nLUS,
                              '../extras/images/icon_infos.gif',
                              $l['extras']['infos'],
                              false),
                        array('out_url.php?outurl=/extras/packs/extplorer/index.php',
                              '../extras/images/icon_extplorer.gif',
                              $l['extplorer']['package'],
                              !file_exists('packs/extplorer/_version')),
                        array('/ctcs/d_index.php'.$nLUS,
                              '../extras/images/icon_ctcs.gif',
                              $lang['extras']['ctcs'],
                              empty($ctcs) || !file_exists('options/Option1')),
                        array('/cpsync/index.php'.$nLUS,
                              '../extras/images/icon_cpsync.gif',
                              $lang['extras']['cpsync'],
                              !file_exists('options/Option1')),
                        array('out_url.php?outurl=/fpkmgr/index.php'.$nLUS,
                              '../extras/images/icon_fpm.gif',
                              $l['fpkmgr']['package'],
                              !file_exists('../fpkmgr/index.php') || !file_exists('options/Option1')),
                        array('./packs/dummy/index.php'.$nLUS,
                              '../extras/images/icon_dummy.gif',
                              $l['dummy']['package'],
                              !file_exists('./packs/dummy/_version')),
                     ));
?>
<!-- end   /ZONE: INDEX -->
<?php include('NEW_fend.inc'); ?>