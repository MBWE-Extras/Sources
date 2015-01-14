#!/usr/bin/php
<?php

/**
 * @version $Id: bt.php,v 1.1.4.4 2009/10/05 13:35:36 weber Exp $
 * @author Wiley Li <wileyli@wistron.com.tw>
 * @copyright Copyright (c) 2004 Wistron Corporation.
 */
$tabidx = 2;
require_once('bt_guiconfig.inc');
require_once('bt_ctcsconfig.inc');
require_once('service_db.inc');
require_once('service_protocol.inc');
require_once('service_preferences.inc');
require_once('service_extra.inc');
require_once('service_xml.inc');
$stopq=0;
$runn = 0;
redirect_error_page(); // checking
is_btadmin();

include($abs_dbfile);
include($abs_preferencesfile);
ctcs_write(WCTINQUIRE."\r\n"); usleep(500000);;
$help_anchor = $htmlObj->Help('downloader', 'bt_tasks');
$ajax_jsname = "ajax_index";
//$help_anchor = $htmlObj->Help('ctcs', 'all_tasks');
$pgtitle = array($lang['ctcs']['title'],
                 $lang['ctcs']['alltasks']['title']);

//$_wait_time = (queue_count()>0)?5000:1500;
$_wait_time = 5000;
$err_msg0   = $lang['ctcs']['errmsg'][0];
$head_js = <<<EOD
<script type="text/javascript">
<!--
var popupGenerator = null;

  // Checkboxes
  function ToggleOne(e) {
    if(e.checked) {
      document.setup_form.toggleAllC.checked = AllChecked();
    } else {
      document.setup_form.toggleAllC.checked = false;
    }
  }

  function ToggleAll(e) {
    if(e.checked) CheckAll();
    else ClearAll();
  }

  function CheckAll() {
    var ml = document.setup_form;
    var len = ml.elements.length;
    for(var i=0; i<len; ++i) {
      var e = ml.elements[i];
      if(e.name == "selitems[]") {
        e.checked = true;
      }
    }
    ml.toggleAllC.checked = true;
  }

  function openAdd() {
  var url = "./torrent_add.php{$nLUS}";
  if( !popupGenerator || popupGenerator.closed ) {
    popupGenerator = window.open( url, "popupGenerator", "resizable=yes,scrollbars=yes,width=510,height=350,top=200,left=200" );
  } else popupGenerator.focus();

  popupGenerator.parentWindow = window;
}
  
  
  function ClearAll() {
    var ml = document.setup_form;
    var len = ml.elements.length;
    for (var i=0; i<len; ++i) {
      var e = ml.elements[i];
      if(e.name == "selitems[]") {
        e.checked = false;
      }
    }
    ml.toggleAllC.checked = false;
  }

  function AllChecked() {
    ml = document.setup_form;
    len = ml.elements.length;
    for(var i=0; i<len; ++i) {
      if(ml.elements[i].name == "selitems[]" && !ml.elements[i].checked) return false;
    }
    return true;
  }

  function NumChecked() {
    ml = document.setup_form;
    len = ml.elements.length;
    num = 0;
    for(var i=0; i<len; ++i) {
      if(ml.elements[i].name == "selitems[]" && ml.elements[i].checked) ++num;
    }
    return num;
  }

  function processDeleteCallBackResult(message) {
  if(message != '') {
    alert(message);
    window.location.href="bt.php{$nLUS}";
  }
}

function processAddCallBackResult(status, message) {
  if(message != '') {
    alert(message);
  }

  if(status) { // reload this page now
    window.location.href="bt.php{$nLUS}";
  }
}


  function ReloadPage() {
    document.setup_form.do_action.value = "reloadpage";
    document.setup_form.lang.value      = "{$GLOBALS["language"]}";
    document.setup_form.submit();
  }

  function UpdateTracker() {
    if(NumChecked()==0) {
      alert("{$err_msg0}");
      return;
    }
    document.setup_form.do_action.value = "updatetracker";
    document.setup_form.submit();
  }

  function RestartTracker() {
    if(NumChecked()==0) {
      alert("{$err_msg0}");
      return;
    }
    document.setup_form.do_action.value = "restarttracker";
    document.setup_form.submit();
  }

  function onStop(id) {
    var docRef = hidden_frame.document;
    docRef.open();
    docRef.write ('<html><body onload="document.forms[0].submit();">');
    docRef.write ('<form name="dummyName" method="post" action="/ctcs/ctcs_inquire.php?query=WCTQUIT&id=' + id + '">');
    docRef.write ('</form>');
    docRef.write ('</body></html>');
    docRef.close();
    setTimeout("window.open('bt.php{$nLUS}', '_self')", {$_wait_time});
    return;
  }

  function onStart(id) {
    var docRef = hidden_frame.document;
    docRef.open();
    docRef.write ('<html><body onload="document.forms[0].submit();">');
    docRef.write ('<form name="dummyName" method="post" action="/ctcs/ctcs_inquire.php?query=WCTSTART&id=' + id + '">');
    docRef.write ('</form>');
    docRef.write ('</body></html>');
    docRef.close();
    setTimeout("window.open('bt.php{$nLUS}', '_self')", {$_wait_time});
    return;
  }

  function onStopQueue(id) {
    var docRef = hidden_frame.document;
    docRef.open();

    docRef.write ('<html><body onload="document.forms[0].submit();">');
    docRef.write ('<form name="dummyName" method="post" action="/ctcs/ctcs_inquire.php?query=WCTQUITQUEUE&id=' + id + '">');
    docRef.write ('</form>');
    docRef.write ('</body></html>');
    docRef.close();
    setTimeout("window.open('bt.php{$nLUS}', '_self')", {$_wait_time});
    return;
  }

  var img_connect_t  = new Image();
  var img_connect_p  = new Image();
  var img_seed       = new Image();
  var img_peer       = new Image();
  var img_seed_stop  = new Image();
  var img_peer_stop  = new Image();

  img_connect_t.src  = "/{$ctcs_imgdir}/image/ct_s_connect_t.gif";
  img_connect_p.src  = "/{$ctcs_imgdir}/image/ct_s_connect_p.gif";
  img_seed.src       = "/{$ctcs_imgdir}/image/ct_s_seed.gif";
  img_peer.src       = "/{$ctcs_imgdir}/image/ct_s_peer.gif";
  img_seed_stop.src  = "/{$ctcs_imgdir}/image/ct_s_seed_stop.gif";
  img_peer_stop.src  = "/{$ctcs_imgdir}/image/ct_s_peer_stop.gif";
//-->
</script>
EOD;

$aq          = $preferences["autostopbt"];
$aqbool      = $preferences["autostopbt_bool"];
$qst         = $preferences["autostopbt_st"];
$row_size    = 0;
$ctcs_xml    = read_ctcs_xml();
$db_by_order = sorted_db_by_order($db);

$inquire_name2i = array();
if (is_array($ctcs_xml)) {
  foreach(array_keys($ctcs_xml) as $ct){
    if ($ct[0]=="C"&&$ct[1]=="T") {
      $inquire_name2i[basename($ctcs_xml[$ct]["CTORRENT"]["filename"])] = $ct;
    }
  }
}

function estimated_seconds($dl_rate, $left_size){
  if ($dl_rate <= 0)
    return estimated_time(0);
  else
    return estimated_time($left_size/$dl_rate);
}

function abbreviation_name($name){
  $len = strlen($name) - 8; // subtract the length of ".torrent"

  $max_length_for_display = 25;
  $_tmp = substr($name,0,$len);
  if ($len>$max_length_for_display) {
    return substr($_tmp,0,$max_length_for_display)." ...";
  }
  return $_tmp;
}

if ($_POST["do_action"]=="reloadpage") {
  @header("Location: bt.php{$nLUS}");
} else if ($_POST["do_action"]&&$_POST["selitems"]) {
  unset($info_type);
  unset($message);

  if (!empty($_POST["selitems"])) {
    // Update Tracker
    if ($_POST["do_action"]=="updatetracker") {
      foreach($_POST["selitems"] as $encoded_peer_id){
        $decoded_peer_id = decode_peer_id($encoded_peer_id);
        if (verify_peer_id($decoded_peer_id) !== false) {
          ctcs_write(WCTUPDATE." {$decoded_peer_id}\r\n");
          usleep(500000); // wait 0.5 second
        }
      }
      $info_type = __INFO_SUCCESS__;
      $message = $lang['ctcs']['message'][0];
    } else if ($_POST["do_action"]=="restarttracker") {
      foreach($_POST["selitems"] as $encoded_peer_id){
        $decoded_peer_id = decode_peer_id($encoded_peer_id);
        if (verify_peer_id($decoded_peer_id) !== false) {
          ctcs_write(WCTRESTART." {$decoded_peer_id}\r\n");
          usleep(500000); // wait 0.5 second
        }
      }
      $info_type = __INFO_SUCCESS__;
      $message = $lang['ctcs']['message'][1];
    }
  }
} else if ($_GET['act']=='del'&&$_GET['id']) {
	unset($info_type);
  unset($message);

  @unlink("{$torrent_dir}/{$peer_id}.bak");
  $info_type = __INFO_ERROR__;
  $peer_id   = decode_peer_id($_GET["id"]);
  $message   = str_replace('__TARGET__', $peer_id, $lang['ctcs']['errmsg'][15]);
  if (verify_peer_id($peer_id) === false) {
    if (isset($db[$peer_id])) {
      @copy("{$torrent_dir}/{$peer_id}","{$torrent_dir}/{$peer_id}.bak");
      @unlink("{$torrent_dir}/{$peer_id}");
      @system("/bin/rm -f {$torrent_dir}/{$peer_id}.*");

      if (remove_db($peer_id)) {
        $info_type = __INFO_SUCCESS__;
        $message   = str_replace('__TARGET__', $peer_id, $lang['ctcs']['message'][6]);
      } else {
        // rollback
        @copy("{$torrent_dir}/{$peer_id}.bak","{$torrent_dir}/{$peer_id}");
      }
    }
  }
  @unlink("{$torrent_dir}/{$peer_id}.bak");
  $htmlObj->SendMsg2Parent($info_type, $message);
  sleep(1);
  @header("Location: bt.php{$nLUS}");
}

// status icons
$icon_style   = "vertical-align: middle;";
$icon_furinfo = $htmlObj->Image(__IMG_CT_FURINFO__, __STR_DETAILS__,"",$icon_style."margin-right: 1px;",0,$ctcs_imgdir);
$icon_start   = $htmlObj->Image(__IMG_CT_START__,   __STR_START__,  "",$icon_style."margin-right: 1px;",0,$ctcs_imgdir);
$icon_stop    = $htmlObj->Image(__IMG_CT_STOP__,    __STR_STOP__,   "",$icon_style."margin-right: 1px;",0,$ctcs_imgdir);
$icon_del     = $htmlObj->Image(__IMG_DEL__,        __STR_DELETE__, "",$icon_style."margin-right: 1px;",0);

$htmlObj->GetChildPageMsg(&$info_type, &$message);
?>

<?php //include('torrent_fbegin.inc'); ?>
<?php include('dn_fbegin.inc'); ?>

<?php
  displayPATH(array(
                array('link'=>'./b_index.php'.$nLUS,
                      'desc'=>$lang['homepage']),
                array('link'=>$_SERVER['PHP_SELF'].$nLUS,
                      'desc'=>$lang['ctcs']['dn']['tasks'])
              ));
?>

<form action="<?=($_SERVER['PHP_SELF'])?>" method="post" name="setup_form" id="setup_form">
<input type="hidden" name="do_action" value="">
<input type="hidden" name="lang" value="">
<center>
<table border="0" cellspacing="0" cellpadding="0" style="" >
  <tr>
    <td colspan="10">
    </td>
  </tr>
  <tr>
    <td colspan="10">&nbsp;</td>
  </tr>
  <tr>

    <td class="listhdrr"><?=$lang['ctcs']['alltasks']['torrent']?></td>
    <td class="listhdrr"><?=$lang['ctcs']['alltasks']['size']?></td>
    <td class="listhdrr"><?=$lang['ctcs']['alltasks']['percentage']?></td>
    <td class="listhdrr"><?=$lang['ctcs']['alltasks']['dn']?></td>
    <td class="listhdrr"><?=$lang['ctcs']['alltasks']['up']?></td>
    <td class="listhdrr"><?=$lang['ctcs']['alltasks']['shareratio']?></td>
    <td class="listhdrr"><?=$lang['ctcs']['alltasks']['timeleft']?></td>
    <td class="listhdrr"><?=$lang['ctcs']['alltasks']['health']?></td>
    <td class="list">&nbsp;</td>
  </tr>
  <?php
  $selitems        = "<input type=\"checkbox\" name=\"selitems[]\" value=\"%s\" onclick=\"javascript:ToggleOne(this);\">";
  $peer_icon       = $htmlObj->Image(__IMG_CT_S_PEER__,     "","",$icon_style,0,$ctcs_imgdir);
  $seed_icon       = $htmlObj->Image(__IMG_CT_S_SEED__,     "","",$icon_style,0,$ctcs_imgdir);
  $seed_stop_icon  = $htmlObj->Image(__IMG_CT_S_SEED_STOP__,"","",$icon_style,0,$ctcs_imgdir);
  $peer_stop_icon  = $htmlObj->Image(__IMG_CT_S_PEER_STOP__,"","",$icon_style,0,$ctcs_imgdir);
  $peer_check_icon = $htmlObj->Image(__IMG_CT_S_CHECK__,    "","",$icon_style,0,$ctcs_imgdir);
  $queue_icon      = $htmlObj->Image(__IMG_CT_S_QUEUED__,   "","",$icon_style,0,$ctcs_imgdir);
  $unable_icon     = $htmlObj->Image(__IMG_CT_S_UNABLED__,  "","",$icon_style,0,$ctcs_imgdir);

$html_row =<<<EOD
<tr>

  <td class="listrgrid_first" nowrap>%s %s</td>
  <td class="listrgrid">%s</td>
  <td class="listrgrid"><div id="percentage%s">%s</div></td>
  <td class="listrgrid"><div id="dl%s">%s</div></td>
  <td class="listrgrid"><div id="ul%s">%s</div></td>
  <td class="listrgrid"><div id="udr%s">%s</div></td>
  <td class="listrgrid"><div id="estimated%s">%s</div></td>
  <td class="listrgrid"><div id="health%s">%s</div></td>
  <td valign="middle" class="list" nowrap>
    <a href="torrent_summary.php?id=%s&status=%s{$yLUS}">{$icon_furinfo}</a>
    %s
    %s
  </td>
</tr>
EOD;

    $tdlrate = $tulrate = 0;
    $row_size = count($inquire_name2i);
    $ajax_ElementArray = array();
    foreach(array_keys($db_by_order) as $order){
      $filename = $db_by_order[$order];
	  //var_dump($db[$filename]);
	  //var_dump($db[$filename][11]);
	  //var_dump($inquire_name2i[$filename]);
      if (isset($inquire_name2i[$filename])) { // running
	  //echo "dddddddd";
        $n            = (int)trim(substr($inquire_name2i[$filename], 2));
        $i            = $inquire_name2i[$filename];
        $icon         = $htmlObj->Image(__IMG_CT_S_CONNECT_T__,"","simg{$n}",$icon_style,0,$ctcs_imgdir);
        $peer_id      = encode_peer_id($ctcs_xml[$i]["CTORRENT"]["peer_id"]);
        $torrent_size = $ctcs_xml[$i]["CTDETAIL"]["torrent_size"];
        $n_have       = $ctcs_xml[$i]["CTSTATUS"]["n_have"];
        $n_total      = $ctcs_xml[$i]["CTSTATUS"]["n_total"];
        $n_avail      = $ctcs_xml[$i]["CTSTATUS"]["n_avail"];
        $dl_rate      = $ctcs_xml[$i]["CTSTATUS"]["dl_rate"];
        $ul_rate      = $ctcs_xml[$i]["CTSTATUS"]["ul_rate"];
        $dl_total     = $ctcs_xml[$i]["CTSTATUS"]["dl_total"];
        $ul_total     = $ctcs_xml[$i]["CTSTATUS"]["ul_total"];
        $left_size    = ($n_total<=0)?0:$torrent_size*(1-$n_have/$n_total);
        $tdlrate     += $dl_rate;
        $tulrate     += $ul_rate;
        $torrent_size = ($torrent_size<=0)?$db[$filename][6]:$torrent_size;

        if ($ul_rate>0 && $n_have>=$n_total) {
          $icon = $htmlObj->Image(__IMG_CT_S_SEED__,"","simg{$n}",$icon_style,0,$ctcs_imgdir);
		  $iconstatus = "Complete_and_Uploading_(seed)";
        } else if ($dl_rate>0 && $n_have<$n_total) {
          $icon = $htmlObj->Image(__IMG_CT_S_PEER__,"","simg{$n}",$icon_style,0,$ctcs_imgdir);
		  $iconstatus = "Incomplete_and_Downloading/Uploading_(peer)";		  
        }

        $estimated_sec = estimated_seconds($dl_rate, $left_size);
        if ($n_have>=$n_total&&$n_have>0) {
          // enable auto-stop-bt-task and use OR condition
          if ($aq==1&&$aqbool==0) {
            $start_timestamp = $ctcs_xml[$i]["CTORRENT"]["start_timestamp"];
            $estimated_sec   = estimated_time($qst*60-($ctcs_xml["LastModified"]-$start_timestamp));
          }
        }

        $action_icon = "<a href=\"javascript:onStop('{$peer_id}')\">{$icon_stop}</a>";
        if (is_checkpieces($filename)) {
          $action_icon = "";
          $icon = $peer_check_icon;
		  $iconstatus = "Hash-Checking_data";
          $row_size = ($row_size>0)?($row_size-1):0;
        } else {
          $ajax_ElementArray[] = $n;
        }

        echo sprintf($html_row, 
                                $icon, abbreviation_name($filename),
                                file_size($torrent_size),
                                $n, percentage($n_have, $n_total),
                                $n, ($action_icon)?speed_rate($dl_rate):"-",
                                $n, ($action_icon)?speed_rate($ul_rate):"-",
                                $n, ($action_icon)?ud_ratio($ul_total, $dl_total):"-",
                                $n, $estimated_sec,
                                $n, ($action_icon)?percentage($n_avail, $n_total):"-",
                                $peer_id,$iconstatus,
                                $action_icon,
                                "");
      } else { // in standby or checking pieces
        $peer_id       = encode_peer_id($filename);
        $action_icon   = "<a href=\"javascript:onStart('{$peer_id}')\">{$icon_start}</a>";
        $show_del_icon = false;
        $action_del    = "";

        $status_icon = $seed_stop_icon;
		$iconstatus_2 = "Stopped_and_Complete";
        if (($db[$filename][4]!=$db[$filename][5])||$db[$filename][4]<=0) {
          $status_icon = $peer_stop_icon;
		  $iconstatus_2 = "Stopped_and_Incomplete";
        }

        if (is_checkpieces($filename)) {
          $action_icon = "";
          $status_icon = $peer_check_icon;
		  $iconstatus_2 = "Connecting_to_peers";
		  
        } else if ($db[$filename][11]==1) {
          $action_icon = "<a href=\"javascript:onStopQueue('{$peer_id}')\">{$icon_stop}</a>";
		 
          $status_icon = $queue_icon;
		  $iconstatus_2 = "Queued";
        } else if ($db[$filename][11]==2) {
          $action_icon = "<a href=\"javascript:onStopQueue('{$peer_id}')\">{$icon_stop}</a>";
          $status_icon = $htmlObj->Image(__IMG_CT_S_CONNECT_T__,"","",$icon_style,0,$ctcs_imgdir);
		  $iconstatus_2 = "Connecting_to_tracker_to_start";
		  
          $to_run = true;
        } else { $show_del_icon = true;}

        if ($show_del_icon) {
          $msg0 = str_replace('__TARGET__', $filename, $lang['ctcs']['message'][5]);
          $msg0 = str_replace("'", "\\'", $msg0);
          $action_del = "<a href=\"./bt.php?act=del&id={$peer_id}{$yLUS}\" onClick=\"return confirm('{$msg0}')\">{$icon_del}</a>";
        }

        if (is_unable($filename)) {
          $status_icon = $unable_icon;
		  $iconstatus_2 = "Unable_to_Write_to_Disk/Unable_to_Hash-Check_data";
        }

        echo sprintf($html_row, 
                                $status_icon, abbreviation_name($filename),
                                file_size($db[$filename][6]),
                                "", ($action_icon)?percentage($db[$filename][4], $db[$filename][5]):percentage(0,0),
                                "", ($to_run)?speed_rate(0):"-",
                                "", ($to_run)?speed_rate(0):"-",
                                "", ($to_run)?ud_ratio(0,0):"-",
                                "", ($to_run)?estimated_time(0):"-",
                                "", ($to_run)?percentage(0,0):"-",
                                $peer_id,$iconstatus_2,
                                $action_icon,
                                $action_del);
      }
    }
  ?>
  <tr>
    <td colspan="8"></td>
    <td class="listrgrid_action" style="padding-top: 4px;" nowrap>
      <a href="javascript:openAdd();"><img src="/ctcs/image/dn_add.gif" style="vertical-align: middle;"></a>
    </td>
  </tr>
  <tr>
    <td colspan="6" style="padding-top: 10px; text-align: right;">
      <?=$img_hdd?>
      <div style="width: 450px; font-weight: bold; color: #CC0000;">
      <noscript><br/><?=htmlspecialchars($lang['noscript']);?></noscript>
      </div>
    </td>
  </tr>
</table>
</center>
</form>

<script type="text/javascript">
  //document.getElementById("tdlrate").innerHTML   = "<?=speed_rate($tdlrate)?>";
  //document.getElementById("tulrate").innerHTML   = "<?=speed_rate($tulrate)?>";
  //document.getElementById("toggleAllC").disabled = <?=($row_size<=0)?"true":"false"?>;
</script>
<?php
  $_val = "";
  sort($ajax_ElementArray);
  foreach ($ajax_ElementArray as $i){
    $_val .= "\"{$i}\",";
  }
  $_val = substr($_val, 0, strlen($_val)-1);
?>


<script type="text/javascript">

  var ElementArray = new Array(<?=$_val?>);
  function init(){
    var progress = new AjaxCTCSQuery(<?=$row_size?>, ElementArray,{
      frequency: <?=$ajax_frequency?>,
      reload_page: "./bt.php<?=$nLUS?>"
    });
    progress.start();
  }

init();
</script>
<br>
<table border="0" cellspacing="2" cellpadding="2" class="tbSetup">
  <tr>
    <td><?=$htmlObj->Image(__IMG_CT_S_CONNECT_T__,"","",$icon_style,0,$ctcs_imgdir)?> <?=$lang['ctcs']['alltasks']['iconconnectt']?></td>
    <td rowspan="4" width="15">&nbsp;</td>
    <td><?=$htmlObj->Image(__IMG_CT_S_CONNECT_P__,"","",$icon_style,0,$ctcs_imgdir)?> <?=$lang['ctcs']['alltasks']['iconconnectp']?></td>
  </tr>
  <tr>
    <td><?=$peer_icon?> <?=$lang['ctcs']['alltasks']['iconpeer']?></td>
    <td><?=$seed_icon?> <?=$lang['ctcs']['alltasks']['iconseed']?></td>
  </tr>
  <tr>
    <td><?=$seed_stop_icon?> <?=$lang['ctcs']['alltasks']['iconseedstop']?></td>
    <td><?=$peer_stop_icon?> <?=$lang['ctcs']['alltasks']['iconpeerstop']?></td>
  </tr>
  <tr>
    <td><?=$peer_check_icon?> <?=$lang['ctcs']['alltasks']['iconcheck']?></td>
    <td><?=$queue_icon?> <?=$lang['ctcs']['alltasks']['iconqueue']?></td>
  </tr>
  <tr>
    <td><?=$unable_icon?> <?=$lang['ctcs']['alltasks']['iconunabled']?></td>
  </tr>
</table>
<iframe name="hidden_frame" src="/blank.html" width="0" height="0" frameborder="0" framespacing="0" border="0"></iframe>
<?php //include('fend.inc'); ?>
<?php include('NEW_fend.inc'); ?>