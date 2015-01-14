#!/usr/bin/php
<?php

require_once('guiconfig.inc');
require_once('wixXML.class');

$path="/proto/SxM_webui/extras/packs/noip/";
$cron="/opt/var/cron/crontabs/root";
$noipLog="/var/log/extras_noip.log";
$conf_file = "noip_conf.xml";
$rootobj = "noip";

switch ($_GET['ajaxtool']) {
   case 'Configure':
	 @system('kill `pidof cron`');
        @system('sed -i "/noip\/noipupdate.sh/d" '.$cron);
	 @system('/opt/sbin/cron');
        break;
   case 'Activate':
        $t = new wixXML;
        $conf=array();
        $conf['login'] = $_GET['login'];
        $conf['password'] = $_GET['password'];
        $conf['host1'] = $_GET['host1'];
        $conf['host2'] = $_GET['host2'];
        $conf['host3'] = $_GET['host3'];
        $conf['refresh'] = $_GET['refresh'];
        $retval = ($t->WriteXML($conf_file, $t->MakeXML($conf, $rootobj)));
        if ($retval) {
           @system('kill $(pidof cron)');
           @system('sed -i "/noip\/noipupdate.sh/d" '.$cron);
           @system('echo "0 1 */'.$_GET['refresh'].' * * sh '.$path.'noipupdate.sh" >> '.$cron);
           @system('/opt/sbin/cron');
        }
        break;
   case 'Update':
        @system('sh '.$path.'noipupdate.sh');
        echo @file_get_contents($noipLog);
        break;
   case 'ClearLog':
        unlink($noipLog);
        break;
}

?>
