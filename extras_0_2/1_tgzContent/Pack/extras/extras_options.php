#!/usr/bin/php
<?php

$skip_check_initsetup = TRUE;
$tabidx = 2;
require_once('extras_config.inc');

$help_anchor = '';

$l = &$lang['extras']['packs']['extras'];

$STR_ENABLE=__STR_ENABLE__;
$STR_DISABLE=__STR_DISABLE__;

$head_js = <<<EOD
<link rel="stylesheet" href="/extras/extras.css" type="text/css" media="screen, projection">
<script type='text/javascript' src='js/jquery-2.1.1.js'></script>
<script type="text/javascript">
<!--

function Option1() {
   $.ajax({
      type: 'GET',
      url: 'extras_ajaxtools.php',
      data: {
         ajaxtool: 'Option1',
      },
      async: true,
      cache: false,
      success: function(data) {
         if(data=='Option1_checked') {
           $('#chkopt1').prop("checked", true); 
           $('#btnopt1').val("${STR_DISABLE}");
         };
         if(data=='Option1_unchecked') {
           $('#chkopt1').prop("checked", false); 
           $('#btnopt1').val("${STR_ENABLE}");
         };
      },
      error: function(error) {
         alert('error');
         console.log(error);
      }
   });
}

function Option2() {
   $.ajax({
      type: 'GET',
      url: 'extras_ajaxtools.php',
      data: {
         ajaxtool: 'Option2',
      },
      async: true,
      cache: false,
      success: function(data) {
         if(data=='Option2_checked') {
           $('#chkopt2').prop("checked", true); 
           $('#btnopt2').val("${STR_DISABLE}");
         };
         if(data=='Option2_unchecked') {
           $('#chkopt2').prop("checked", false); 
           $('#btnopt2').val("${STR_ENABLE}");
         };
      },
      error: function(error) {
         alert('error');
         console.log(error);
      }
   });
}

function Option3() {
   $.ajax({
      type: 'GET',
      url: 'extras_ajaxtools.php',
      data: {
         ajaxtool: 'Option3',
      },
      async: true,
      cache: false,
      success: function(data) {
         if(data=='Option3_checked') {
           $('#chkopt3').prop("checked", true); 
           $('#btnopt3').val("${STR_DISABLE}");
         };
         if(data=='Option3_unchecked') {
           $('#chkopt3').prop("checked", false); 
           $('#btnopt3').val("${STR_ENABLE}");
         };
      },
      error: function(error) {
         alert('error');
         console.log(error);
      }
   });
}

function Option4() {
   $.ajax({
      type: 'GET',
      url: 'extras_ajaxtools.php',
      data: {
         ajaxtool: 'Option4',
      },
      async: true,
      cache: false,
      success: function(data) {
         if(data=='Option4_checked') {
           $('#chkopt4').prop("checked", true); 
           $('#btnopt4').val("${STR_DISABLE}");
         };
         if(data=='Option4_unchecked') {
           $('#chkopt4').prop("checked", false); 
           $('#btnopt4').val("${STR_ENABLE}");
         };
      },
      error: function(error) {
         alert('error');
         console.log(error);
      }
   });
}

function Option5() {
   $.ajax({
      type: 'GET',
      url: 'extras_ajaxtools.php',
      data: {
         ajaxtool: 'Option5',
         timeout: $('#btntout').val(),
      },
      async: true,
      cache: false,
      success: function(data) {
         if(data=='Option5_checked') {
           $('#chkopt5').prop("checked", true); 
           $('#btntout').prop('disabled',true);
           $('#btnopt5').val("${STR_DISABLE}");
         };
         if(data=='Option5_unchecked') {
           $('#chkopt5').prop("checked", false); 
           $('#btntout').prop('disabled',false);
           $('#btnopt5').val("${STR_ENABLE}");
         };
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

if ($_POST && !$_POST['ACKNOWLEDGE']) {
  unset($info_type);
  unset($message);

  $info_type = __INFO_ERROR__; // type of default message
}

?>
<?php include('extras_fbegin.inc'); ?>
<form action="<?=$_SERVER['PHP_SELF'].$nLUS?>" method="post" name="setup_form" id="setup_form">
<?php
  displayPATH(array(
                array('link'=>'/extras/index.php'.$nLUS,
                      'desc'=>$l['package']),
                array('link'=>$_SERVER['PHP_SELF'].$nLUS,
                      'desc'=>$l['options']),
              ));
?>
<table cellpadding="0" cellspacing="0" class="tbSetup">
  <tr title="<?=htmlspecialchars($l['option1tip']);?>">
    <td colspan="2" class="listtopic">
      <?=htmlspecialchars($l['option1title']);?>
    </td>
  </tr>
  <tr title="<?=htmlspecialchars($l['option1tip']);?>">
    <td class="vncellt" style="border: 0;">
      <?=htmlspecialchars($l['option1']);?>
    </td>
    <td class="listrnobg" style="border-bottom: 0;">
      <table><tr>
        <td width="100px">
          <input name="chkopt1" type="checkbox" id="chkopt1" <?=(file_exists('options/Option1')?'CHECKED':'')?> onclick="return false">
          <?=htmlspecialchars(__STR_ENABLED__);?>
        </td>
        <td width="120px">
          <input name="btnopt1" type="button" class="formbtn" id="btnopt1" value="<?=htmlspecialchars((file_exists('options/Option1')?__STR_DISABLE__:__STR_ENABLE__));?>" onClick="Option1();">
        </td>
      </tr></table>
    </td>
  </tr>
  <tr title="<?=htmlspecialchars($l['option2tip']);?>">
    <td colspan="2" class="listtopic">
      <?=htmlspecialchars($l['option2title']);?>
    </td>
  </tr>
  <tr title="<?=htmlspecialchars($l['option2tip']);?>">
    <td class="vncellt" style="border: 0;">
      <?=htmlspecialchars($l['option2']);?>
    </td>
    <td class="listrnobg" style="border-bottom: 0;">
      <table><tr>
        <td width="100px">
          <input name="chkopt2" type="checkbox" id="chkopt2" <?=(file_exists('options/Option2')?'CHECKED':'')?> onclick="return false">
          <?=htmlspecialchars(__STR_ENABLED__);?>
        </td>
        <td width="120px">
          <input name="btnopt2" type="button" class="formbtn" id="btnopt2" value="<?=htmlspecialchars((file_exists('options/Option2')?__STR_DISABLE__:__STR_ENABLE__));?>" onClick="Option2();">
        </td>
      </tr></table>
    </td>
  </tr>
  <tr title="<?=htmlspecialchars($l['option3tip']);?>">
    <td colspan="2" class="listtopic">
      <?=htmlspecialchars($l['option3title']);?>
    </td>
  </tr>
  <tr title="<?=htmlspecialchars($l['option3tip']);?>">
    <td class="vncellt" style="border: 0;">
      <?=htmlspecialchars($l['option3']);?>
    </td>
    <td class="listrnobg" style="border-bottom: 0;">
      <table><tr>
        <td width="100px">
          <input name="chkopt3" type="checkbox" id="chkopt3" <?=(file_exists('options/index.php')?'CHECKED':'')?> onclick="return false">
          <?=htmlspecialchars(__STR_ENABLED__);?>
        </td>
        <td width="120px">
          <input name="btnopt3" type="button" class="formbtn" id="btnopt3" value="<?=htmlspecialchars((file_exists('images/index.php')?__STR_DISABLE__:__STR_ENABLE__));?>" onClick="Option3();">
        </td>
      </tr></table>
    </td>
  </tr>
  <tr title="<?=htmlspecialchars($l['option4tip']);?>">
    <td colspan="2" class="listtopic">
      <?=htmlspecialchars($l['option4title']);?>
    </td>
  </tr>
  <tr title="<?=htmlspecialchars($l['option4tip']);?>">
    <td class="vncellt" style="border: 0;">
      <?=htmlspecialchars($l['option4']);?>
    </td>
    <td class="listrnobg" style="border-bottom: 0;">
      <table><tr>
        <td width="100px">
          <input name="chkopt4" type="checkbox" id="chkopt4" <?=(file_exists('../fpkmgr/fpks/OpenVpn/Manage.sh.ext')?'CHECKED':'')?> onclick="return false">
          <?=htmlspecialchars(__STR_ENABLED__);?>
        </td>
        <td width="120px">
          <input name="btnopt4" type="button" class="formbtn" id="btnopt4" value="<?=htmlspecialchars((file_exists('../fpkmgr/fpks/OpenVpn/Manage.sh.ext')?__STR_DISABLE__:__STR_ENABLE__));?>" onClick="Option4();">
        </td>
      </tr></table>
    </td>
  </tr>
  <tr title="<?=htmlspecialchars($l['option5tip']);?>">
    <td colspan="2" class="listtopic">
      <?=htmlspecialchars($l['option5title']);?>
    </td>
  </tr>
  <tr title="<?=htmlspecialchars($l['option5tip']);?>">
    <td class="vncellt" style="border: 0;">
      <?=htmlspecialchars($l['option5']);?>
    </td>
    <td class="listrnobg" style="border-bottom: 0;">
      <table><tr>
        <td width="100px">
          <input name="chkopt5" type="checkbox" id="chkopt5" <?=(file_exists('options/Option5')?'CHECKED':'')?> onclick="return false">
          <?=htmlspecialchars(__STR_ENABLED__);?>
        </td>
        <td width="120px">
          <input name="btnopt5" type="button" class="formbtn" id="btnopt5" value="<?=htmlspecialchars((file_exists('options/Option5')?__STR_DISABLE__:__STR_ENABLE__));?>" onClick="Option5();">
        </td>
        <td width="150px">
          <input name="btntout" type="number" class="formbtn" id="btntout" step="1" min="1" max="60"
                 value="<?=(is_numeric(@file_get_contents('options/Option5'))?@file_get_contents('options/Option5'):5)?>" style="text-align:center; width:50px" 
                 <?=(file_exists('options/Option5')?'disabled':'')?>> <?=$l['minutes']?>
        </td>
      </tr></table>
    </td>
  </tr>
</table>
</form>
<?php include('NEW_fend.inc'); ?>
