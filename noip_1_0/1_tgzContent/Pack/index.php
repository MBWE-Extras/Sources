#!/usr/bin/php
<?php
# Purpose :
#     This script manage NoIP_Update Feature Pack to updates the no-ip 
#     alias with a cron task and produce a log file.
#     The specifications of the noip update protocol can be found at :
#     http://www.noip.com/integrate/
#
# Author     : Patrick BRIOT 
# 
# Last update: 11-10-2013
#
#

$tabidx = 3;
require_once('guiconfig.inc');
require_once('wixXML.class');

$l = &$lang['extras']['packs']['noip'];
$help_anchor = $htmlObj->Help('', '');

$head_js = <<<EOD
<link rel="stylesheet" href="/extras/extras.css" type="text/css" media="screen, projection">
<style type="text/css">
<!--
.arealog,
.arealog:focus {
    max-height:150px;
    min-height:150px;
    max-width:750px;
    min-width:750px;
    resize:none; 
    border:1px; 
    cellpadding:3px; 
    cellspacing:1px;
    display:inline;
    text-align:left;
    font-size:9pt;
    border:2px solid brown;
    box-shadow:0 0.3em 1em #000000;
}

input:not([disabled]):hover {
    background-color:#00FFFF;
}

-->
</style>

<script type="text/javascript" src="/admin/js/webtoolkit.base64.js"></script>
<script type='text/javascript' src='/extras/js/jquery-2.1.1.js'></script>
<script type="text/javascript">
<!--

function ClearLog() {
   $("textarea#noiplog").val("${l['waitlog']}");
   $.ajax({
      type: 'GET',
      data: {
          ajaxtool: 'ClearLog',
      },
      url: 'noip_ajaxtools.php',
      async: true,
      cache: false,
      success: function() {
         $("textarea#noiplog").val('');
      },
      error: function(error) {
         alert('error');
         console.log(error);
      }
   });
}

function Conf_Act(conf) {
   $.ajax({
      type: 'GET',
      data: {
          ajaxtool: conf,
          login: $("input#login").val(),
          password: Base64.encode($("input#password").val()),
          host1: $("input#host1").val(),
          host2: $("input#host2").val(),
          host3: $("input#host3").val(),
          refresh: $("input#refresh").val(),
      },
      url: 'noip_ajaxtools.php',
      async: true,
      cache: false,
      success: function(data){
         if ($("input#login").prop('disabled')==true) {
            $("input#login").prop('disabled',false);
            $("input#password").prop('disabled',false);
            $("input#host1").prop('disabled',false);
            $("input#host2").prop('disabled',false);
            $("input#host3").prop('disabled',false);
            $("input#refresh").prop('disabled',false);
            $("input#update").prop('disabled',true);
            $("input#act").css('display','inline');
            $("input#conf").css('display','none');
         } else {
            $("input#login").prop('disabled',true);
            $("input#password").prop('disabled',true);
            $("input#host1").prop('disabled',true);
            $("input#host2").prop('disabled',true);
            $("input#host3").prop('disabled',true);
            $("input#refresh").prop('disabled',true);
            $("input#update").prop('disabled',false);
            $("input#act").css('display','none');
            $("input#conf").css('display','inline');
         }
      },
      error: function(error) {
         alert('error');
         console.log(error);
      }
   });
}

function Update() {
   $("textarea#noiplog").val("${l['waitlog']}");
   $.ajax({
      type: 'GET',
      data: {
          ajaxtool: 'Update',
      },
      url: 'noip_ajaxtools.php',
      async: true,
      cache: false,
      success: function(data){
         $("textarea#noiplog").val(data);
      },
      error: function(error) {
         alert('error');
         console.log(error);
      }
   });
}

-->
</script>
EOD;

$onload_jsfun = '';

$cron="/opt/var/cron/crontabs/root";
$noipLog="/var/log/extras_noip.log";
$conf_file = "noip_conf.xml";
$rootobj = "noip";

$t = new wixXML;
$conf=array();

if(!file_exists($conf_file)) {
  $conf['login'] = "Login-no-ip@mail.com";
  $conf['password'] = base64_encode("YourPassword");
  $conf['host1'] = '';
  $conf['host2'] = '';
  $conf['host3'] = '';
  $conf['refresh'] = 7;
  $retval = ($t->WriteXML($conf_file, $t->MakeXML($conf, $rootobj)));
}
$conf = $t->ParseXML($conf_file, $rootobj);

$incron = (strpos(@file_get_contents($cron), "/noipupdate.sh") !== false);
?>

<?php include('extras_fbegin.inc'); ?>
<?php
  displayPATH(array(
                array('link'=>'/extras/extras_packs1.php'.$nLUS,
                      'desc'=>$lang['homepage']),
                array('link'=>$_SERVER['PHP_SELF'].$nLUS,
                      'desc'=>$l['package'])
              ));
?>
<form action="<?=$_SERVER['PHP_SELF'].$nLUS?>" method="post" name="setup_form" id="setup_form">
<?displayRtnMessage($info_type, $message);?>
<div  cellpadding="0" cellspacing="0" style="display:inline-block; vertical-align:top;">
<table cellpadding="0" cellspacing="0" style="margin-left:auto; margin-right:auto; width:400px;">
  <tr>
    <td colspan="2" class="listtopic"><?=htmlspecialchars($l['menu1']);?></td>
  </tr>
  <tr>
    <td class="vncellreq" style="width:40%"><?=htmlspecialchars($l['login']);?></td>
    <td class="vtable" style="width:60%">
      <input name="login" type="text" class="formfld" id="login" size="22" value="<?=($conf['login'])?>" <?=($incron?'disabled':'')?>>
    </td>
  </tr>
  <tr>
    <td class="vncellreq" style="width:40%"><?=htmlspecialchars($l['pwd']);?></td>
    <td class="vtable" style="width:60%">
      <input name="password" type="password" class="formfld" id="password" size="22" value="<?=base64_decode($conf['password'])?>" <?=($incron?'disabled':'')?>>
    </td>
  </tr>
  <tr>
    <td class="vncellreq" style="width:40%"><?=htmlspecialchars($l['refresh']);?></td>
    <td class="vtable" style="width:60%">
      <input name="refresh" type="number" id="refresh" step="1" value="<?=($conf['refresh'])?>" min="1" max="14" style="text-align:center;" <?=($incron?'disabled':'')?>> <?=($l['days'])?>
    </td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: right; padding:0 10px;">
      <input name="conf" type="button" class="formbtn" id="conf" value="<?=($l['conf'])?>" style="display:<?=($incron?'inline':'none')?>" title="<?=$l['tipconf']?>" onClick="Conf_Act('Configure');">
      <input name="act" type="button" class="formbtn" id="act" value="<?=($l['act'])?>" style="display:<?=($incron?'none':'inline')?>" title="<?=$l['tipact']?>" onClick="Conf_Act('Activate');">
    </td>
  </tr>
</table>
</div>
<div  cellpadding="0" cellspacing="0" style="display:inline-block; vertical-align:top;">
<table cellpadding="0" cellspacing="0" style="margin-left:auto; margin-right:auto; width:350px;">
  <tr>
    <td colspan="2" class="listtopic"><?=htmlspecialchars($l['menu2']);?>&#160;</td>
  </tr>
  <tr>
    <td class="vncellreq"><?=htmlspecialchars($l['host1']);?></td>
    <td class="vtable">
      <input name="host1" type="text" class="formfld" id="host1" size="28" value="<?=($conf['host1'])?>" <?=($incron?'disabled':'')?>>
    </td>
  </tr>
  <tr>
    <td class="vncellreq"><?=htmlspecialchars($l['host2']);?></td>
    <td class="vtable">
      <input name="host2" type="text" class="formfld" id="host2" size="28" value="<?=($conf['host2'])?>" <?=($incron?'disabled':'')?>>
    </td>
  </tr>
  <tr>
    <td class="vncellreq"><?=htmlspecialchars($l['host3']);?></td>
    <td class="vtable">
      <input name="host3" type="text" class="formfld" id="host3" size="28" value="<?=($conf['host3'])?>" <?=($incron?'disabled':'')?>>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <input name="update" type="button" class="formbtn" id="update" value="<?=$l['update']?>" title="<?=$l['tipupdate'];?>" onClick="Update();" <?=($incron?'':'disabled')?>>
    </td>
  </tr>
</table>
</div>
<br>
<br>
<table cellpadding="0" cellspacing="0" style="margin-left: auto; margin-right: auto; width:750px;">
  <tr>
    <td colspan="2" class="listtopic">
      <?=htmlspecialchars($l['menu3']);?>
      <input name="clear" type="button" value=" <?=$l['clearlog'];?> " class="formfld" id="clear" size="40" style="float: right; margin:0 10px;" title="<?=$l['tipclearlog'];?>" onClick="ClearLog();">
    </td>
  </tr>
  <tr>
    <td class="vtable" colspan="2">
      <textarea id="noiplog" class="arealog" readonly><?php echo @file_get_contents($noipLog); ?></textarea>
    </td>
  </tr>
</table>
<?php include('NEW_fend.inc'); ?>