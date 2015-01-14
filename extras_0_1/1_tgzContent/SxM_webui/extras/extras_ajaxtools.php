#!/usr/bin/php
<?php

$path="/proto/SxM_webui/extras/packs/";

switch ($_GET['ajaxtool']) {
   case 'FlushRam':
        @exec('sync && echo 3 > /proc/sys/vm/drop_caches');
#        @exec('/etc/init.d/S55mini_httpd restart');
        break;
   case 'ReadLog':
        $path.=$_GET['file'].".log";
        if (file_exists($path)) {
           $file=fopen($path,"r");
           $read=fread($file,filesize($path));
           fclose($file);
           echo $read;
           flush();
        } else {
           exit (1);
        }
        break;
   case 'ClearLog':
        unlink($path.$_GET['file'].".log");
        break;
   case 'DelNew':
        unlink($path.$_GET['file'].".new");
        break;
   case 'Download':
        $pack = array('fw_output_name'=>'wdhxnc-01.02.14.img', 'fw_destination'=>'/var/upgrade_download');
        @mkdir ($pack['fw_destination']);
        @copy ("https://github.com/MBWE-Extras/MBWE_Extras/raw/master/Download/".$_GET['file']."/".$_GET['file'].".img", $pack['fw_destination']."/".$pack['fw_output_name']);

        require_once('webhooks.inc');
        $retval = $webHooks->Upgrade($pack,true);
        break;
   case 'ReadStatus':
        $path='/var/upgrade_download/fwud_status';
        if (file_exists($path)) {
           $file=fopen($path,"r");
           $read=fread($file,filesize($path));
           fclose($file);
           echo $read;
        }
        break;
   case 'Uninstall':
        @system("./packs/${_GET['file']}/_uninstall.sh >> ./packs/${_GET['file']}.log 2>&1", $retval);
        echo $retval;
        break;
}

?>
