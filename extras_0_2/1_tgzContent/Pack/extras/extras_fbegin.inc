<?php

@ob_start();
$ajax_frequency     = 30; // unit: seconds
$sub_menu_tab_empty = '&nbsp;<img src="/admin/image/spacer.gif">&nbsp;';
$sub_menu_tab_point = '&nbsp;<img src="/admin/image/indicator.gif">&nbsp;';
$sysinfo            = $webHooks->SystemInfo();

function gentitle($title) {
  return join(': ', $title);
}

function genhtmltitle($title) {
	return __PROJECT_NAME__  . ' - ' .
         gentitle($title);
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=9">
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
<link rel="stylesheet" href="/blueprint/screen.css" type="text/css" media="screen, projection">

<script type="text/javascript" src="./js/prototype.js"></script>
<? if(isset($ajax_jsname)): ?>
<script type="text/javascript" src="./js/<?=$ajax_jsname?>.js"></script>
<? endif; ?>

<!-- Import fancy-type plugin -->
<link rel="stylesheet" href="/blueprint/plugins/fancy-type/screen.css" type="text/css" media="screen, projection">

<link rel="stylesheet" href="/base.css" type="text/css" media="screen, projection">
<? if(!isset($arrange)): ?>
<? endif; ?>
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
                    <a href="../admin/basic_index.php<?=$nLUS?>"><?=($lang['expressmod']);?></a>
                    <img src="/img/headernav_separator.gif" />
<?php if($help_anchor==false): ?>
                    <span style="color: gray;"><?=__STR_HELP__?></span>
<?php else: ?>
                    <a href="<?=htmlspecialchars($help_anchor);?>" onclick="return popitup('<?=htmlspecialchars($help_anchor);?>')"><?=__STR_HELP__?></a>
<?php endif; ?>
                    <img src="/img/headernav_separator.gif" />
                    <a href="<?=$_SERVER['PHP_SELF'].'?action=logout'.$yLUS?>"><?=__STR_LOGOUT__?></a>
                </div>
            </div>
        </div>

        <!-- Tab -->
        <div class="column span-20 last" id="tabmenu">
            <table cellspacing="0">
                <tr>
                    <td class="<?=($tabidx==1)?'active':''?> dnitem first"><a href="/admin/system_index.php<?=$nLUS?>"><?=($lang['system']['title']);?></a></td>
                    <td class="<?=($tabidx==2)?'active':''?> dnitem"><a href="/extras/index.php<?=$nLUS?>">Extras</a></td>
                    <td class="<?=($tabidx==3)?'active':''?> dnitem"><a href="/extras/extras_packs1.php<?=$nLUS?>">Packs 1</a></td>
                    <td class="<?=($tabidx==4)?'active':''?> dnitem"><a href="/extras/extras_packs2.php<?=$nLUS?>">Packs 2</a></td>
                    <td class="<?=($tabidx==5)?'active':''?> dnitem"><a href="/extras/extras_packs3.php<?=$nLUS?>">Packs 3</a></td>
                    <td class="<?=($tabidx==6)?'active':''?> dnitem last"><a href="/extras/extras_packs4.php<?=$nLUS?>">Packs 4</a></td>
                </tr>
            </table>
        </div>

        <!-- Main Contents -->
        <div class="column span-20 last" id="contents" style="text-align: center;">
