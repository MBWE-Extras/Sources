<?php

/**
 * @version $Id: NEW_login_fbegin.inc,v 1.1.2.3.2.1 2008/11/04 11:28:32 jason Exp $
 * @author Wiley Li <wileyli@wistron.com.tw>
 * @copyright Copyright (c) 2004 Wistron Corporation.
 */

if($is_success_for_login) {
   setcookie('Extras', $GLOBALS['_SERVER']['HTTP_REFERER'], 0, '/');
} else {
   setcookie('Extras', 'logout', time() - 3600, '/');
}

@ob_start();

$sub_menu_tab_empty = '&nbsp;<img src="/admin/image/spacer.gif">&nbsp;';
$sub_menu_tab_point = '&nbsp;<img src="/admin/image/indicator.gif">&nbsp;';
$sysinfo            = $webHooks->SystemInfo();

function gentitle($title) {
  return join(': ', $title);
}

function genhtmltitle($title) {
  global $sysinfo;

  return __PROJECT_NAME__  . ' - ' .
         $sysinfo['MNAME'] . ' - ' .
         gentitle($title);
}

if ($_GET) {
  $_get_suffix = FALSE;
  foreach($_GET as $_get_name => $_get_value){
    if ($_get_suffix === FALSE) {
      $_get_suffix = "?$_get_name=$_get_value";
    } else {
      $_get_suffix .= "&$_get_name=$_get_value";
    }
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title><?=__PROJECT_NAME__.' - '.$sysinfo['MNAME']?></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<?php if (isset($page_refresh_time) && is_integer($page_refresh_time)): ?>
<?="<meta http-equiv=\"Refresh\" content=\"$page_refresh_time\">\n"?>
<?php endif; ?>
<!-- Framework CSS -->
<link rel="stylesheet" href="/blueprint/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="/blueprint/print.css" type="text/css" media="print">
<link rel="stylesheet" href="/blueprint/src/typography.css" type="text/css" media="screen, projection">
<!--[if IE]>
    <link rel="stylesheet" href="/style/blueprint/ie.css" type="text/css" media="screen, projection">
<![endif]-->

<!-- Import fancy-type plugin -->
<link rel="stylesheet" href="/blueprint/plugins/fancy-type/screen.css" type="text/css" media="screen, projection">

<link rel="stylesheet" href="/base.css" type="text/css" media="screen, projection">

<script type="text/javascript">
<!--
function trim (val) {
  if (val == null) { return ''; }
  else             { val = val + ''; }

  // remove ' ' before inputed value
  for (var i = 0; i < val.length && val.substring (i, (i + 1)) == ' '; i++) ;

  // remove ' ' after inputed value
  for (var j = (val.length - 1); j > i && val.substring (j, (j + 1)) == ' '; j--) ;

  return val.substring (i, (j + 1));
}

function popitup(url) {
	newwindow=window.open(url,'name','top=200,left=400,location=1,menubar=1,resizable=1,scrollbars=1,status=1,toolbar=1');
	if (window.focus) {newwindow.focus()}
	return false;
}

function toggleHTMLItem(id, display) {
    htmlItem = document.getElementById(id);
    if (htmlItem) {
      if (!display) { // hide
        htmlItem.style.display = 'none';
      } else { // show
        htmlItem.style.display = '';
      }
    }
  }
// -->
</script>
<?php if (isset($head_js)) { echo $head_js; } ?>
</head>

<body onLoad="<?=$onload_jsfun?>">
    <div class="container">
        <!-- Header -->
        <div class="column span-20 last" id="header">
            <div class="column span-10 prepend-10 last">
                <div id="HeaderNav">
                    &nbsp;
                    <img src="/img/headernav_separator.gif" />
                    <!--
                    <a href="<?=htmlspecialchars($help_anchor);?>" onclick="return popitup('<?=htmlspecialchars($help_anchor);?>')"><?=__STR_HELP__?></a>
                    -->
                    <span style="color: gray;"><?=__STR_HELP__?></span>
                    <img src="/img/headernav_separator.gif" />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
            </div>
        </div>

        <!-- Tab -->
        <div class="column span-20 last" id="tabmenu">
            <table cellspacing="0">
                <tr>
                    <td class="active basic_first"><nobr><a href="#"><?=($lang['login']['title']);?></a></nobr></td>
                    <td class="basic_last">&nbsp;</td>
                </tr>
            </table>
        </div>

        <!-- Main Contents -->
        <div class="column span-20 last" id="contents" style="text-align: center;">
<?=$htmlObj->InfoBox($info_type, $message);?>