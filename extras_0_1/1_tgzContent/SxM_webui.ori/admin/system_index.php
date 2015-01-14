#!/usr/bin/php
<?php

/**
 * @version $Id: system_index.php,v 1.1.2.10.2.2 2008/08/07 06:40:49 jason Exp $
 * @author Wiley Li <wileyli@wistron.com.tw>
 * @copyright Copyright (c) 2004 Wistron Corporation.
 */
$tabidx = 1;
require_once('guiconfig.inc');

$help_anchor = $htmlObj->Help('system', 'system_summary');
?>

<?php include('NEW_fbegin.inc'); ?>
<!-- begin /ZONE: INDEX -->
<?php
    $l = $lang['system'];
    displayIndexPage(array( // {url,icon,desc}
                       array('./system_general.php'.$nLUS,
                             'icon_general_setup.gif',
                             $l['generalsetup']['title']),
                       array('./system_alerts.php'.$nLUS,
                             'icon_alerts.gif',
                             $l['alert']['title']),
                       array('./system_change_admin_passwd.php'.$nLUS,
                             'icon_admin_password.gif',
                             $l['chgadminpasswd']['title']),
                       array('./system_firmware_automated.php'.$nLUS,
                             'icon_update.gif',
                             $l['firmwareupdate']['title']),
                       array('./system_config_manage.php'.$nLUS,
                             'icon_configuration.gif',
                             $l['configurationmanagement']['title']),
                       array('./system_advanced.php'.$nLUS,
                             'icon_advanced.gif',
                             $l['advancedsetup']['title']),
                       //array('./system_ups_manage.php'.$nLUS,
                       //      'icon_ups.gif',
                       //      $l['ups']['title']),
                       array('./shutdown_reboot.php'.$nLUS,
                             'icon_restart.gif',
                             $lang['express']['shutdownreboot'])
                     ));
?>
<!-- end   /ZONE: INDEX -->
<?php include('NEW_fend.inc'); ?>