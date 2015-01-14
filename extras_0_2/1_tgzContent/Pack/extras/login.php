#!/usr/bin/php
<?php

$skip_check_initsetup = TRUE;
$login_page           = TRUE;
require_once('guiconfig.inc');

$sysinfo = $webHooks->SystemInfo();

$index_page_title = __PROJECT_NAME__ . ' - ' . $sysinfo['MNAME'];

$is_success_for_login = FALSE;

$head_js = <<<EOD
<script type="text/javascript" src="/admin/js/webtoolkit.base64.js"></script>
<script type="text/javascript">
<!--

function onSubmit() {
  document.login.p_pass.value = Base64.encode(document.login.p_pass.value);  
}

-->
</script>
EOD;

if ($_POST) {
  unset($info_type);
  unset($message);

  if(empty($GLOBALS['__POST']['p_user'])) {
    $err_msg = htmlspecialchars($lang['login']['errmsg'][1]);
  } else {
    $p_pass = (isset($GLOBALS['__POST']['p_pass']) ? base64_decode($GLOBALS['__POST']['p_pass']) : '');
    $is_success_for_login = activate_user($GLOBALS['__POST']['p_user'], md5(stripslashes($p_pass)));
    if($is_success_for_login) {
      @header("Location: " . $GLOBALS['http_host'] . "/admin/system_index.php{$nLUS}");
    } else {
      $err_msg = str_replace('__TARGET__', "tool", $lang['login']['errmsg'][0]);
    }
  }
}

if (!$is_success_for_login) {
  $GLOBALS['__SESSION']=array();
  @session_destroy();
  @session_write_close();
}
?>
<?php include('NEW_login_fbegin.inc'); ?>
<form action="<?=($_SERVER['PHP_SELF'])?>" method="post" onsubmit="onSubmit();" name="login" id="login">
<table cellpadding="0" cellspacing="0" class="tbLogin">
  <tr><td colspan="2" class="vncellempty">&nbsp;</td></tr>
  <tr><td colspan="2" class="vncellempty">&nbsp;</td></tr>
<?php if(isset($err_msg)): ?>
  <tr>
    <td colspan="2">
      <div id="rtnMessage" style="padding-bottom: 5px;">
        <div class="msg" style="width: 565px;">
          <ul class="err" style="padding-left: 5px; padding-top: 5px; padding-bottom: 5px;">
            <h2><?=$err_msg?></h2>
          </ul>
        </div>
      </div>
    </td>
  </tr>
<?php endif; ?>
  <tr>
    <td class="vncellreq" style="width: 250px;"><font size="4">
      <?=htmlspecialchars($lang['login']['username']);?>
    </font></td>
    <td class="vtable">
      <input class="formfld" name="p_user" id="p_user" type="text" size="25" value="" style="font-size: 14pt">
    </td>
  </font></tr>
  <tr>
    <td class="vncellempty"> </td>
  </tr>
  <tr>
    <td class="vncellreq" style="width: 250px;"><font size="4">
      <?=htmlspecialchars($lang['login']['passwd']);?>
    </font></td>
    <td class="vtable">
      <input class="formfld" name="p_pass" id="p_pass" type="password" size="25" value="" style="font-size: 14pt">
    </td>
  </tr>
  <tr>
    <td class="vncellempty"> </td>
  </tr>
  <tr>
<?php if($SXMCONF['multilingual']['enable']):?>
  <tr>
    <td class="vncellreq" style="width: 200px;"><font size="4">
      <?=htmlspecialchars($lang['login']['language']);?>
    </font></td>
    <td class="vtable">

      <select name="lang" id="lang" class="formfld" onchange="onSubmit();this.form.submit();" style="font-size: 14pt">
<?foreach($SXMCONF['multilingual']['resource'] as $name => $val):?>
        <option value="<?=$name?>" <?=($GLOBALS["language"]==$name)?"SELECTED":""?>><?=$val?></option>
<?endforeach;?>
      </select>
    </td>
  </tr>
  <tr>
    <td class="vncellempty"> </td>
  </tr>
<?php else:?>
  <input type="hidden" name="lang" id="lang" value="<?=$SXMCONF['multilingual']['default']?>">
<?php endif;?>
  <tr>
    <td class="vncellempty">&nbsp;&nbsp;&nbsp;</td>
    <td class="vtable" style="text-align:left;">
        <input type="submit" value="<?=htmlspecialchars($lang['login']['title']);?>" class="formbtn" style="font-size: 14pt">
    </td>
  </tr>
  <tr>
    <td class="vncellempty"> </td>
  </tr>
  <tr><td colspan="2" class="vncellempty">&nbsp;</td></tr>
  <tr><td colspan="2" class="vncellempty" style="width: 450px; font-weight: bold; color: #CC0000;">
    <noscript><?=htmlspecialchars($lang['noscript']);?></noscript>
  </td></tr>
</table>
<script>if(document.login) document.login.p_user.focus();</script>
<script>document.login.p_user.focus();</script>
<?php if($_GET['init']=='done'): ?>
<script>
initwindow=window.open('/help/help_init_setup.php','initialsetupinfo','top=5,left=5,width=450,height=600,location=0,menubar=0,resizable=1,scrollbars=1,status=0,toolbar=0');
if (window.focus) {initwindow.focus()}
</script>
<?php endif; ?>
</form>
<?php include('NEW_fend.inc'); ?>
<?php unset($_SESSION['LanInit']); ?>
