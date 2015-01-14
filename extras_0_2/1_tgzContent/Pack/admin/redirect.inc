<?php

/**
 * @version $Id: redirect.inc,v 1.1 2007/10/12 10:16:08 wiley Exp $
 * @author Wiley Li <wileyli@wistron.com.tw>
 * @copyright Copyright (c) 2004 Wistron Corporation.
 */

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

$_IPADDR   = $GLOBALS['__SERVER']['HTTP_HOST'];
$_SSL_PORT = trim(file_get_contents("/etc/port_ssl"));
$_PORT     = trim(file_get_contents("/etc/port_http"));

if($_SERVER['SERVER_PORT'] == $_SSL_PORT) {
  $_CUR_PORT = $_SSL_PORT;
} else { $_CUR_PORT = $_PORT; }

if(!file_exists('/extras/options/Option2')) {
  if(strpos($_IPADDR,':')!==false){
    $_tmp = explode(':',$_IPADDR);
    $_IPADDR = "{$_tmp[0]}:{$_CUR_PORT}";
  }
}

if($_SERVER['SERVER_PORT'] == $_SSL_PORT ) {
  @header("Location: https://{$_IPADDR}");
} else {
  @header("Location: http://{$_IPADDR}");
}
?>
