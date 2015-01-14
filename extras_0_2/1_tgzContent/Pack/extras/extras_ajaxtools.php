#!/usr/bin/php
<?php
$cookie="ajax";
require_once('extras_config.inc');
unset($cookie);
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
        @copy ("https://github.com/MBWE-Extras/Downloads/raw/master/Packages/".$_GET['file']."/".$_GET['file'].".img", $pack['fw_destination']."/".$pack['fw_output_name']);
        if (file_exists($pack['fw_destination']."/".$pack['fw_output_name'])) {
           require_once('webhooks.inc');
           $retval = $webHooks->Upgrade($pack,true);
        } else {
           echo "unfound";
        }
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
        if ($_GET['file'] == "extras") {
           @system("./packs/${_GET['file']}/_uninstall.sh >> /var/log/${_GET['file']}.log 2>&1 &", $retval);
           @header("Location: /");
           exit;
        } else {
           @system("./packs/${_GET['file']}/_uninstall.sh >> ./packs/${_GET['file']}.log 2>&1", $retval);
        }
        echo $retval;
        break;
   case 'Option1':
        if (!file_exists('options/Option1')) {
           touch('options/Option1');
           echo "Option1_checked";
        } else {
           unlink('options/Option1');
           echo "Option1_unchecked";
        }
        break;
   case 'Option2':
        if (!file_exists('options/Option2')) {
           touch('options/Option2');
           echo "Option2_checked";
        } else {
           unlink('options/Option2');
           echo "Option2_unchecked";
        }
        break;
   case 'Option3':
        $blacklist = array('');
        $folders = rglobfolders($_SERVER['DOCUMENT_ROOT'], $blacklist);
        $fsecure = "#!/usr/bin/php\n<?php\n#extras_option3\ninclude('redirect.inc');\n?>\n";
        if (!file_exists('/proto/SxM_webui/extras/options/index.php')) {
           $dt = filemtime(__FILE__);
           foreach ($folders as $folder) {
              if (!file_exists($folder."/index.php") && !file_exists($folder."/index.html")) {
                 $fp = fopen($folder."/index.php", 'w');
                 fwrite($fp, $fsecure);
                 fclose($fp);
                 touch($folder."/index.php", $dt);
                 chmod($folder."/index.php", 0700);
              }
           }
           echo "Option3_checked";
        } else {
           $crc1 = strtoupper(dechex(crc32($fsecure)));
           foreach ($folders as $folder) {
              if ($crc1 == strtoupper(dechex(crc32(@file_get_contents($folder."/index.php"))))) {
                 unlink($folder."/index.php");
              }
           }
           echo "Option3_unchecked";
        }
        break;
   case 'Option4':
        if (!file_exists('/proto/SxM_webui/fpkmgr/fpks/OpenVpn/Manage.sh.ext')) {
           rename ('/proto/SxM_webui/fpkmgr/fpks/OpenVpn/Manage.sh', '/proto/SxM_webui/fpkmgr/fpks/OpenVpn/Manage.sh.ext');
           copydtmod ('/proto/SxM_webui/extras/options/option4/Manage.sh', '/proto/SxM_webui/fpkmgr/fpks/OpenVpn/Manage.sh');
           copydtmod ('/proto/SxM_webui/extras/options/option4/OVClient.php', '/proto/SxM_webui/fpkmgr/fpks/OpenVpn/OVClient.php');
           echo "Option4_checked";
        } else {
           unlink ('/proto/SxM_webui/fpkmgr/fpks/OpenVpn/OVClient.php');
           unlink ('/proto/SxM_webui/fpkmgr/fpks/OpenVpn/Manage.sh');
           rename ('/proto/SxM_webui/fpkmgr/fpks/OpenVpn/Manage.sh.ext', '/proto/SxM_webui/fpkmgr/fpks/OpenVpn/Manage.sh');
           echo "Option4_unchecked";
        }
        break;
   case 'Option5':
        if (!file_exists('options/Option5')) {
           $fp = fopen('options/Option5', 'w');
           fwrite($fp, $_GET['timeout']);
           fclose($fp);
           echo "Option5_checked";
        } else {
           unlink('options/Option5');
           echo "Option5_unchecked";
        }
        break;
}
?>
