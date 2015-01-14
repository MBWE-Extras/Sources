#!/usr/bin/php
<?php

$tabidx = 2;
require_once('extras_config.inc');

$help_anchor = $htmlObj->Help('status', 'system_status');
$pgtitle = array($lang['status']['title'],
                 $lang['status']['system']['title']);
$pgtitle_omit = false;

$page_refresh_time = 60; // in seconds

$volList = $webHooks->conf['storage']['nasvolume'];

$nicObj = new wixNIC();
$currentIP = (!$nicObj->getIP('eth0')) ? 'Unknown' : $nicObj->getIP('eth0');
$model = $webHooks->model;

$head_js = <<<EOD
<script type='text/javascript' src='js/jquery-2.1.1.js'></script>
<script type="text/javascript">
<!--

function FlushRam() {
   $.ajax({
      type: 'GET',
      data: {
          ajaxtool: 'FlushRam',
      },
      url: 'extras_ajaxtools.php',
      async: true,
      cache: false,
      success: function(data) {
         $('#setup_form').submit();
      },
      error: function(error) {
      }
   });
}

-->
</script>
EOD;
$onload_jsfun = '';

if ($_POST && !$_POST['ACKNOWLEDGE']) {
  $new_submit_type = $_POST['submit_type'];
}

?>

<?php include('extras_fbegin.inc'); ?>
<?php
  displayPATH(array(
                array('link'=>'./index.php'.$nLUS,
                      'desc'=>$lang['extras']['packs']['extras']['package']),
                array('link'=>$_SERVER['PHP_SELF'].$nLUS,
                      'desc'=>$lang['extras']['packs']['extras']['infos'])
              ));
?>
<form action="<?=$_SERVER['PHP_SELF'].$nLUS?>" method="post" name="setup_form" id="setup_form">
<input name="submit_type" type="hidden" id="submit_type" value="">

<table cellpadding="0" cellspacing="0" class="tbSetup">
  <tr>
    <td colspan="2" class="listtopic">
      <?=htmlspecialchars($lang['status']['system']['systeminfo']);?>
    </td>
  </tr>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars($lang['status']['system']['name']);?>
    </td>
    <td class="listr">
      <?=$sysinfo['MNAME']?>
    </td>
  </tr>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars($lang['system']['firmwareupdate']['version']);?>
    </td>
    <td class="listr">
      <?php
      $isMionet = is_file('/etc/.mionet_installed');
      if($isMionet) {
        $mionetVer = trim(file_get_contents('/etc/.mionet_installed'));
      }

      if(is_file('/etc/sp')) {
        $spVer = trim(file_get_contents('/etc/sp'));
        if(!empty($spVer)) {
          if($isMionet) {
            echo $sysinfo['VER'].' SP'.$spVer.' '.str_replace(__MIONETVER__, $mionetVer, $lang['status']['system']['withmionet']);
          } else {
            echo $sysinfo['VER'].' SP'.$spVer;
          }
        } else {
          if($isMionet) {
            echo $sysinfo['VER'].' '.str_replace(__MIONETVER__, $mionetVer, $lang['status']['system']['withmionet']);
          } else {
            echo $sysinfo['VER'];
          }
        }
      } else {
        if($isMionet) {
          echo $sysinfo['VER'].' '.str_replace(__MIONETVER__, $mionetVer, $lang['status']['system']['withmionet']);
        } else {
          echo $sysinfo['VER'];
        }
      }
      ?>
      <br/>
      <?=str_replace('__DATETIME__', $sysinfo['BUILDTIME'], $lang['status']['system']['builton'])?>
    </td>
  </tr>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars($lang['status']['system']['date']);?>
    </td>
    <td class="listr">
      <?=date('D, d M Y H:i:s')?>
    </td>
  </tr>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars($lang['status']['system']['uptime']);?>
    </td>
    <td class="listr">
      <?=$sysinfo['UPTIME']?>
    </td>
  </tr>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars($lang['network']['lan']['ipaddress']);?>
    </td>
    <td class="listr">
      <?=$currentIP?>
    </td>
  </tr>
<?php if(!empty($volList)) { ?>
<?php for($i = 0; $i < count($volList); $i++) {?>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars(str_replace('__TARGET__', $volList[$i]['name'], $lang['status']['system']['volusage']));?>
    </td>
    <td class="listr">
    <?php
        $volumeinfo = $webHooks->VolumesInfo($i);
        if($volumeinfo != ''){
          $vol_info = explode(',', $volumeinfo);
          $vol_status = explode('|', $vol_info[7]);

          if ($vol_status[0] == 0 || $vol_status[0] == 4){ // good/degraded
            if($vol_info[5] != '??') {
              $htmlObj->DisplayUsage(((int)$vol_info[5]));
              $devicename = $vol_info[2];
              $htmlObj->DisplayFree($devicename, $vol_info[0]);
            } else {
              echo __STR_UNKNOWN__;
            }
          } else {
            echo $vol_status[1];
          }
        }
    ?>
    </td>
  </tr>
  <?php if($model != 'WWLXN'): ?>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars(str_replace('__TARGET__', $volList[$i]['name'], $lang['status']['system']['volraidtype']));?>
    </td>
    <td class="listr">
      <?=htmlspecialchars($utilityObj->RtnReadableRaidLevel($vol_info[3]));?>
    </td>
  </tr>
  <?php endif; ?>
<?php } ?>
<?php } ?>
  <tr><td colspan="2" class="vncellempty">&nbsp;</td></tr>
  <tr>
    <td colspan="2" class="listtopic">
      <?=htmlspecialchars($lang['extras']['statusmore']['sysmore']);?>
    </td>
  </tr>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars($lang['extras']['statusmore']['sysinfo']);?>
    </td>
    <td class="listr">
      <?=htmlspecialchars(__PROJECT_NAME__.' ('.file_get_contents('/etc/model').' - '.file_get_contents('/etc/modelNumber').')');?>
    </td>
  </tr>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars($lang['extras']['statusmore']['hddtemp']);?>
    </td>
    <td class="listr">
       <?php
         ob_start();
         @system("/usr/sbin/smartctl -a -d ata /dev/sda |awk '/Temperature/ { print $10 }' ");
         $Temp1 = trim(ob_get_contents());
         ob_clean();
         @system("/usr/sbin/smartctl -a -d ata /dev/sdb |awk '/Temperature/ { print $10 }' ");
         $Temp2 = trim(ob_get_contents());
         ob_clean();
	 if ("$Temp1"!="") echo 'HDD 1 : '.str_replace("#1", $Temp1, str_replace("#2", $Temp1*9/5+32, htmlspecialchars($lang['extras']['statusmore']['thdd'])));
	 if (("$Temp1"!="") && ("$Temp2"!="")) echo " - ";
	 if ("$Temp2"!="") echo 'HDD 2 : '.str_replace("#1", $Temp2, str_replace("#2", $Temp2*9/5+32, htmlspecialchars($lang['extras']['statusmore']['thdd'])));
       ?>
    </td>
  </tr>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars($lang['extras']['statusmore']['ram']);?>
    </td>
    <td class="listr" title="<?=$mem=file_get_contents('/proc/meminfo');?>">
       <?php
         $mem = explode(PHP_EOL, $mem);
         $TotalMem = array_map('trim', explode(":", $mem[0]));
         $FreeMem  = array_map('trim', explode(":", $mem[1]));
         $htmlObj->DisplayUsage((int)($FreeMem[1]/$TotalMem[1]*100));
         $htmlObj->DisplayFree('', $TotalMem[1]);
         echo ' : '.$FreeMem[1].' / '.$TotalMem[1];
       ?>
       &nbsp;&nbsp;&nbsp;
       <input type="button" class="formbtn" id="flush" value="<?=htmlspecialchars($lang['extras']['statusmore']['flush']);?>" onClick="FlushRam();">
    </td>
  </tr>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars($lang['extras']['statusmore']['cpu']);?>
    </td>
    <td class="listr" title="<?=$cpu=file_get_contents('/proc/cpuinfo');?>">
       <?php
         $cpu = explode(PHP_EOL, $cpu);
         $cpu = array_map('trim', explode(":", $cpu[0]));
         echo $cpu[1];
       ?>
    </td>
  </tr>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars($lang['extras']['statusmore']['wdserial']);?>
    </td>
    <td class="listr" title="<? ob_start();@system(".|top");$t=ob_get_contents();ob_clean();echo substr($t,strpos($t,"[7m")+4);?>">
      <?=file_get_contents('/etc/serialNumber');?>
    </td>
  </tr>
  <tr>
    <td class="vncellt">
      <?=htmlspecialchars($lang['extras']['statusmore']['linux']);?>
    </td>
    <td class="listr">
      <?=file_get_contents('/proc/version');?>
    </td>
  </tr>
</table>
<?php include('NEW_fend.inc'); ?>