<?php

/**
 * @version $Id: bt_guiconfig.inc,v 1.1.4.2 2009/09/10 09:22:08 weber Exp $
 * @author Wiley Li <wileyli@wistron.com.tw>
 * @copyright Copyright (c) 2004 Wistron Corporation.
 */

// Set flag that this is a parent file
if(!defined('_VALID_MOS')) define( '_VALID_MOS', 1 );
if(!defined('_VALID_EXT')) define( '_VALID_EXT', 1 );

if(isset($_SERVER)) {
  $GLOBALS['__GET']    = &$_GET;
  $GLOBALS['__POST']   = &$_POST;
  $GLOBALS['__SERVER'] = &$_SERVER;
  $GLOBALS['__FILES']  = &$_FILES;
} elseif(isset($HTTP_SERVER_VARS)) {
  $GLOBALS['__GET']    = &$HTTP_GET_VARS;
  $GLOBALS['__POST']   = &$HTTP_POST_VARS;
  $GLOBALS['__SERVER'] = &$HTTP_SERVER_VARS;
  $GLOBALS['__FILES']  = &$HTTP_POST_FILES;
} else {
  die("<B>ERROR: Your PHP version is too old</B><BR>".
  "You need at least PHP 4.0.0 to run System Configuration; preferably PHP 4.3.1 or higher.");
}

require_once('bt_ctcsconfig.inc');
require_once('users.php');
require_once('conf.php');
require_once('.htadmin.php');
require_once('.htusers.php');
require_once('.htshares.php');

// =========================================================
$_IPADDR   = $GLOBALS['__SERVER']['HTTP_HOST'];
$_SSL_PORT = trim(file_get_contents("/etc/port_ssl"));
$_PORT     = trim(file_get_contents("/etc/port_http"));

if($_SERVER['SERVER_PORT'] == $_SSL_PORT) {
  $_CUR_PORT = $_SSL_PORT;
} else { $_CUR_PORT = $_PORT; }

if(!file_exists('/proto/SxM_webui/extras/options/Option2')) {
  if(strpos($_IPADDR,':')!==false){
    $_tmp = explode(':',$_IPADDR);
    $_IPADDR = "{$_tmp[0]}:{$_CUR_PORT}";
  }
}

if($_SERVER['SERVER_PORT'] == $_SSL_PORT ) {
  $GLOBALS['http_host'] = "https://{$_IPADDR}";
} else {
  $GLOBALS['http_host'] = "http://{$_IPADDR}";
}
// =========================================================

// =========================================================
// ?? inquirer ??
if(!file_exists("/etc/.ready")) {
  @header("Location: {$GLOBALS['http_host']}/admin/inquirer.php");
}
// =========================================================

$session_found = TRUE;
//@session_write_close();
//@session_id( srand_id(32) );
@session_start();

if(isset($_SESSION))               $GLOBALS['__SESSION']=&$_SESSION;
elseif(isset($HTTP_SESSION_VARS))  $GLOBALS['__SESSION']=&$HTTP_SESSION_VARS;

if(file_exists('/proto/SxM_webui/extras/options/Option1')) {
  $btadmin_name = $GLOBALS['__SESSION']['s_user'] = 'admin';
}

include_once('sxmconfig.inc');

// Get Language
$GLOBALS["lang"]=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
//$GLOBALS["lang"]="fr";
if(isset($GLOBALS['__GET']["lang"])) $GLOBALS["lang"]=$GLOBALS['__GET']["lang"];
elseif(isset($GLOBALS['__POST']["lang"])) $GLOBALS["lang"]=$GLOBALS['__POST']["lang"];
if(isset($GLOBALS["lang"])) $GLOBALS["language"]=$GLOBALS["lang"];
else $GLOBALS["language"]="fr"; // set default if need

function lus($suffix = true){
  return (($suffix)?"&":"?")."lang=".$GLOBALS["language"];
}

function __logout(){
  setcookie('Extras', 'logout', time() - 3600, '/');
  @header("Location: {$GLOBALS['http_host']}/?lang=".$GLOBALS["language"]);
}

function srand_id($length=8){
  $salt      = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  $makepass  = '';
  mt_srand(10000000*(double)microtime());
  for ($i = 0; $i < $length; $i++)
    $makepass .= $salt[mt_rand(0,61)];
  return $makepass;
}

$yLUS = lus();      // &lang=
$nLUS = lus(false); // ?lang=

if($_GET['action']=='logout') {
  __logout();
} else if (!$login_page) { // Checking login user and his password
  if(isset($GLOBALS['__SESSION']['s_user'])) {
  
    if(!file_exists('/proto/SxM_webui/extras/options/Option1')) {
      if ($GLOBALS['__SESSION']['s_user'] == $btadmin_name) {
        if (strstr($_SERVER['PHP_SELF'], 'ctcs')===false) {
            __logout();
          } else if (!btadmin_passwd_check($GLOBALS['__SESSION']["s_pass"])) {
            __logout();
          } else {
            $skip_check_initsetup = true;
          }
        } else if(!activate_user($GLOBALS['__SESSION']['s_user'],$GLOBALS['__SESSION']['s_pass'])) {
          __logout();
        } else if(!in_array($GLOBALS['__SESSION']['s_user'], $GLOBALS['admin_users'])) {
          __logout();
        }
      } else {
        $skip_check_initsetup = true;
        if(!activate_user($GLOBALS['__SESSION']['s_user'],$GLOBALS['__SESSION']['s_pass'])) {
          __logout();
        }
      }
  } else {
    __logout();
  }
}

// =========================================================
// ?? login renew or timed-out ?
if (!isset($_COOKIE['Extras']) && !$login_page) __logout();
if (isset($_COOKIE['Extras'])) {
   if (file_exists('/proto/SxM_webui/extras/options/Option5')) {
      $timeout=@file_get_contents('../extras/options/Option5');
      $timeout=(is_numeric($timeout)?$timeout:5)*60;
      setcookie('Extras', $GLOBALS['http_host'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'], time() + $timeout, '/');
   } else {
      setcookie('Extras', $GLOBALS['http_host'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'], 0, '/');
   }
}
// =========================================================

$GLOBALS['__SESSION']['PHP_SELF'] = $_SERVER['PHP_SELF'];

// Language for 'Share Explorer'
if ($GLOBALS['__POST']['lang'] != '') {
  $GLOBALS['__SESSION']['LANG'] = $GLOBALS['__POST']['lang'];
}

/* make sure nothing is cached */
if (!$omit_nocacheheaders) {
  @header('Expires: 0');
  @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
  @header('Cache-Control: no-store, no-cache, must-revalidate');
  @header('Cache-Control: post-check=0, pre-check=0', false);
  @header('Pragma: no-cache');
}

//include_once('sxmconfig.inc');
if (!$SXMCONF['multilingual']['enable']) {
//  $_default_lang = $SXMCONF['multilingual']['default'];
  $_default_lang=substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
  $GLOBALS["language"]          = $_default_lang;
  $GLOBALS["lang"]              = $_default_lang;
  $GLOBALS['__SESSION']['LANG'] = $_default_lang;
}

include_once('translate.inc');
include_once('utilities.inc');
include_once('webhooks.inc');

$eula_accept = $webHooks->IAgreeEULA();
if ($skip_check_initsetup !== TRUE) {
  if (!isset($initsetup) || $initsetup === FALSE) { // Go initial page if need
    if (!$eula_accept) {
      @header("Location: init.php{$nLUS}");
    }
  } else { // Go to homepage if done for initial setup
    if ($eula_accept) {
      @header("Location: /extras/login.php{$nLUS}");
#      @header("Location: index.php{$nLUS}");
    }
  }
}

if ($_POST['ACKNOWLEDGE']) {
  $webHooks->EventsAcknowledge($_POST['events']);
  if ($_GET) {
    $_get_ACKNOWLEDGE_suffix = FALSE;
    foreach($_GET as $_get_name => $_get_value){
      if ($_get_ACKNOWLEDGE_suffix === FALSE) {
        $_get_ACKNOWLEDGE_suffix = "?$_get_name=$_get_value";
      } else {
        $_get_ACKNOWLEDGE_suffix .= "&$_get_name=$_get_value";
      }
    }
  }
  @header('Location: ' . $_SERVER['PHP_SELF'] . $_get_ACKNOWLEDGE_suffix);
}

$left_colwidth  = $htmlObj->ColWidth('left');
$right_colwidth = $htmlObj->ColWidth('right');
// =========================================================
if (file_exists('/proto/SxM_webui/extras/options/Option1') && (($_SERVER['PHP_SELF'] == '/') OR ($_SERVER['PHP_SELF'] == '/index.php'))) {
   $url=parse_url($_SERVER['HTTP_REFERER']);
   switch ($url['path']) {
      case "/fpkmgr/index.php":
      case "/cpsync/index.php":
      case "/cpsync/csm_logs.php":
      case "/ctcs/index.php":
      case "/ctcs/b_index.php":
      case "/ctcs/d_index.php":
      case "/ctcs/dn_settings.php":
      case "/ctcs/bt.php":
      case "/ctcs/torrent_preferences.php":
      case "/ctcs/torrent_arrangement.php":
#         @header("Location: ".$url['scheme']."://".$url['host'].($url['port']?':'.$url['port']:'')."/extras/index.php".($url['query']?'?'.$url['query']:''));
         @header("Location: ".$url['scheme']."://".$url['host'].($url['port']?':'.$url['port']:'')."/extras/index.php{$nLUS}");
         exit;
         break;
      default:
         @header("Location: {$GLOBALS['http_host']}/extras/login.php{$nLUS}");
         exit;
         break;
   }
};
?>