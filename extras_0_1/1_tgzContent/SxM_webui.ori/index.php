#!/usr/bin/php
<?php

/**
 * @version $Id: index.php,v 1.23.4.7.2.4 2009/09/10 09:20:36 weber Exp $
 * @author Wiley Li <wileyli@wistron.com.tw>
 * @copyright Copyright (c) 2004 Wistron Corporation.
 */

$skip_check_initsetup = TRUE;
$login_page           = TRUE;
require_once('guiconfig.inc');

$sysinfo = $webHooks->SystemInfo();

$index_page_title = __PROJECT_NAME__ . ' - ' . $sysinfo['MNAME'];

$is_success_for_login = FALSE;

$head_js = <<<EOD
<script type="text/javascript" src="admin/js/webtoolkit.base64.js"></script>
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
	  if(isset($GLOBALS['__POST']['p_pass'])) $p_pass=base64_decode($GLOBALS['__POST']['p_pass']);
	  else $p_pass='';
	  if(isset($GLOBALS['__POST']['p_user'])) {
	    if ($GLOBALS['__POST']['p_user'] == $btadmin_name &&
	          $GLOBALS['__POST']['login_mode'] == 'downloader') {
	      if (btadmin_passwd_check(md5(stripslashes($p_pass)))) {
	        $GLOBALS['__SESSION']["s_user"] = $GLOBALS['__POST']['p_user'];
	        $GLOBALS['__SESSION']["s_pass"] = md5(stripslashes($p_pass));
	        $is_success_for_login = TRUE;
	      }
	    } else if(!activate_user($GLOBALS['__POST']['p_user'], md5(stripslashes($p_pass)))) {
	      $is_success_for_login = FALSE;
	    } else {
	      if ($GLOBALS['__POST']['login_mode'] == 'configuration') {
	        if (in_array($GLOBALS['__POST']['p_user'], $GLOBALS['admin_users'])) {
	          $is_success_for_login = TRUE;
	        }
	      } else if ($GLOBALS['__POST']['login_mode'] == 'explorer') {
	        $is_success_for_login = TRUE;
	      } else if ($GLOBALS['__POST']['login_mode'] == 'copymanager') {
	        $is_success_for_login = TRUE;
	      }
	    }
	  }

	  if ($is_success_for_login) {
	    if ($GLOBALS['__POST']['login_mode'] == 'configuration') {
	      if ($SXMCONF["epressmode"]["enable"]) {
	        @header("Location: " . $GLOBALS['http_host'] . "/admin/basic_index.php{$nLUS}");
	      } else {
	        @header("Location: " . $GLOBALS['http_host'] . "/admin/system_index.php{$nLUS}");
	      }
	    } else if ($GLOBALS['__POST']['login_mode'] == 'explorer') {
	      @header("Location: " . $GLOBALS['http_host'] . "/share/index.php?lang=" . $GLOBALS['__POST']['lang']);
	    } else if ($GLOBALS['__POST']['login_mode'] == 'downloader') {
	      @header("Location: " . $GLOBALS['http_host'] . "/ctcs/d_index.php{$nLUS}");
	    } else if ($GLOBALS['__POST']['login_mode'] == 'copymanager') {
	      @header("Location: " . $GLOBALS['http_host'] . "/cpsync/index.php{$nLUS}");
	    }
	  } else {
	    switch($GLOBALS['__POST']['login_mode']){
	      case 'configuration':
	        $tool = $lang['login']['configuration'];
	        break;
	      case 'explorer':
	        $tool = $lang['login']['explorer'];
	        break;
				case 'copymanager':
					 $tool = $lang['login']['copymanager'];
	        break;
	      default:
	        $tool = $lang['login']['downloader'];
	    }
	    $err_msg = str_replace('__TARGET__', $tool, $lang['login']['errmsg'][0]);
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
<?php if(isset($_SERVER["HTTP_REFERER"])&&strpos($_SERVER["HTTP_REFERER"],"/share/index.php")!==false): ?>
<script>window.open('/index.php<?=$nLUS?>','_self');</script>
<?php else: ?>
<form action="<?=($_SERVER['PHP_SELF'])?>" method="post" onSubmit="onSubmit();" name="login" id="login">
<table cellpadding="0" cellspacing="0" class="tbLogin">
  <tr><td colspan="2" class="vncellempty">&nbsp;</td></tr>
  <tr><td colspan="2" class="vncellempty">&nbsp;</td></tr>
<?php if(isset($err_msg)): ?>
  <tr>
    <td colspan="2">
      <div id="rtnMessage" style="padding-bottom: 5px;">
        <div class="msg" style="width: 370px;">
          <ul class="err" style="padding-left: 5px; padding-top: 5px; padding-bottom: 5px;">
            <h2><?=$err_msg?></h2>
          </ul>
        </div>
      </div>
    </td>
  </tr>
<?php endif; ?>
  <tr>
    <td class="vncellreq">
      <?=htmlspecialchars($lang['login']['username']);?>
    </td>
    <td class="vtable">
      <input class="formfld" name="p_user" id="p_user" type="text" size="25" value="">
    </td>
  </tr>
  <tr>
    <td class="vncellreq">
      <?=htmlspecialchars($lang['login']['passwd']);?>
    </td>
    <td class="vtable">
      <input class="formfld" name="p_pass" id="p_pass" type="password" size="25" value="">
    </td>
  </tr>
  <tr>
    <td class="vncellreq">
      <?=htmlspecialchars($lang['login']['tool']);?>
    </td>
    <td class="vtable">
      <select name="login_mode" class="formfld">
        <option value="configuration" SELECTED><?=htmlspecialchars($lang['login']['configuration']);?></option>
<?php if($SXMCONF['downloader']['enable']):?>
        <option value="downloader"><?=htmlspecialchars($lang['login']['downloader']);?></option>
<?php endif;?>
				<option value="copymanager"><?=htmlspecialchars($lang['login']['copymanager']);?></option>
      </select>
    </td>
  </tr>
<?php if($SXMCONF['multilingual']['enable']):?>
  <tr>
    <td class="vncellreq">
      <?=htmlspecialchars($lang['login']['language']);?>
    </td>
    <td class="vtable">
      <select name="lang" id="lang" class="formfld">
<?foreach($SXMCONF['multilingual']['resource'] as $name => $val):?>
        <option value="<?=$name?>" <?=($GLOBALS["language"]==$name)?"SELECTED":""?>><?=$val?></option>
<?endforeach;?>
      </select>
    </td>
  </tr>
<?php else:?>
  <input type="hidden" name="lang" id="lang" value="<?=$SXMCONF['multilingual']['default']?>">
<?php endif;?>
  <tr>
    <td class="vncellempty">&nbsp;</td>
    <td class="vtable" style="text-align:left;">
        <input type="submit" value="<?=htmlspecialchars($lang['login']['title']);?>" class="formbtn">
    </td>
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
<?php endif; ?>
<?php include('NEW_fend.inc'); ?>
<?php unset($_SESSION['LanInit']); ?>
