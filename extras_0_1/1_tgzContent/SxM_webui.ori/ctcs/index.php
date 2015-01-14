#!/usr/bin/php
<?php

/**
 * @version $Id: index.php,v 1.4.4.12.2.11 2009/10/12 06:32:01 jason Exp $
 * @author Wiley Li <wileyli@wistron.com.tw>
 * @copyright Copyright (c) 2004 Wistron Corporation.
 */
$tabidx = 1;
require_once('guiconfig.inc');
require_once('ctcsconfig.inc');
require_once('dn_settings.inc');
require_once('service_extra.inc');
require_once('dn_extra.inc');
require_once('xml.inc');
require_once('http_status_code.inc');

redirect_error_page(); // checking
is_btadmin();

$help_anchor = $htmlObj->Help('downloader', 'tasks');

$current_session_id = session_id();

$jsdelete = addslashes($lang['ctcs']['dn']['jsdelete']);
$jsdeleteconfirm = addslashes($lang['ctcs']['dn']['jsdeleteconfirm']);
$reorderchg = addslashes($lang['ctcs']['dn']['reorderchg']);
$reordermsubmit = addslashes($lang['ctcs']['dn']['reordermsubmit']);
$sysbusy = addslashes($lang['ctcs']['dn']['message'][11]);

$head_js = <<<EOD
<script type="text/javascript" src="/ctcs/js/ajax_dn_tasks.js"></script>
<script type="text/javascript">
<!--
var popupError = null;
var popupGenerator = null;
var is_system_running_now = 0;

// initial hash table
var pos2wgaid;
var wgaid2url;
var wgaid2loc;
var wgaid2saveas;
var wgaid2shortn;
var wgaid2size;
var wgaid2dnsize;
var wgaid2addtime;
var wgaid2status;
var wgaid2ssicon;
var wgaid2pop;
var wgaid2speed;

function showERROR(wgaid, proto){
  // 'proto' definition
  // 1: HTTP or HTTPS
  // 2: FTP
  var url = "dn_show_error.php?wgaid="+wgaid+"&proto="+proto;
  if( !popupError || popupError.closed ) {
    popupError = window.open( url, "popupError", "resizable=yes,scrollbars=yes,width=600,height=250,top=200,left=300" );
  } else {
    popupError.focus();
    popupError = window.open( url, "popupError", "resizable=yes,scrollbars=yes,width=600,height=250,top=200,left=300" );
  }

  popupError.parentWindow = window;
}

function openAdd() {
  var url = "dn_add.php{$nLUS}";
  if( !popupGenerator || popupGenerator.closed ) {
    popupGenerator = window.open( url, "popupGenerator", "resizable=yes,scrollbars=yes,width=510,height=350,top=200,left=200" );
  } else popupGenerator.focus();

  popupGenerator.parentWindow = window;
}

function processAddCallBackResult(status, message) {
  if(message != '') {
    alert(message);
  }

  if(status) { // reload this page now
    window.location.href="index.php{$nLUS}";
  }
}

function onStartStop(wgaid, ac) {
  if(is_system_running_now == 1) {
    alert('{$sysbusy}'); return;
  } else {
    is_system_running_now = 1;
  }
  
  var docRef = hidden_frame.document;
  docRef.open();
  docRef.write ('<html><body onload="document.forms[0].submit();">');
  docRef.write ('<form name="dummyName" method="post" action="/ctcs/dn_start_stop.php?id={$current_session_id}&wgaid='+wgaid+'&ac='+ac+'">');
  docRef.write ('</form>');
  docRef.write ('</body></html>');
  docRef.close();
  return;
}

function processStartStopCallBackResult(message) {
  if(message != '') {
    alert(message);
    reloadThisPageNow();
  }
}

function onDelete(wgaid, name) {
  if(is_system_running_now == 1) {
    alert('{$sysbusy}'); return;
  }

  var msg_del_task = '{$jsdelete}: '+name+'\\n{$jsdeleteconfirm}';

  if(confirm(msg_del_task)) {
    is_system_running_now = 1;
  
    var docRef = hidden_frame.document;
    docRef.open();
    docRef.write ('<html><body onload="document.forms[0].submit();">');
    docRef.write ('<form name="dummyName" method="post" action="/ctcs/dn_delete.php?id={$current_session_id}&wgaid='+wgaid+'">');
    docRef.write ('</form>');
    docRef.write ('</body></html>');
    docRef.close();
    return;
  }
}

function processDeleteCallBackResult(message) {
  if(message != '') {
    alert(message);
    window.location.href="index.php{$nLUS}";
  }
}

function onMoveUP(wgaid, cur_pos) {
  if((cur_pos-1)<1) return; // in top already

  // retrieve
  var val1 = pos2wgaid.get((cur_pos-1));
  var val2 = pos2wgaid.get(cur_pos);

  pos2wgaid.set((cur_pos-1), val2);
  pos2wgaid.set(cur_pos, val1);

  displayTasksTable('tbl_download_tasks'); // render

  document.setup_form.arrangement.value = 1;
  
  if(document.setup_form.arrangement.value==1) {
    toggleHTMLItem('arrangement_submit',true);
  }
}

function onMoveDown(wgaid, cur_pos) {
  if((cur_pos+1)>pos2wgaid.size()) return; // in bottom already

  // retrieve
  var val1 = pos2wgaid.get((cur_pos+1));
  var val2 = pos2wgaid.get(cur_pos);

  pos2wgaid.set((cur_pos+1), val2);
  pos2wgaid.set(cur_pos, val1);

  displayTasksTable('tbl_download_tasks'); // render

  document.setup_form.arrangement.value = 1;
  
  if(document.setup_form.arrangement.value==1) {
    toggleHTMLItem('arrangement_submit',true);
  }
}

function reloadThisPageNow() {
  if(document.setup_form.arrangement.value==1) {
    if(confirm('{$reorderchg}')){
      window.location.href="index.php{$nLUS}";
    } else {
      alert('{$reordermsubmit}');
    }
  } else {
    window.location.href="index.php{$nLUS}";
  }
}
-->
</script>
EOD;
$onload_jsfun = '';

if($_POST) {
  unset($info_type);
  unset($message);



  if($_POST['newrow_str'][strlen($_POST['newrow_str'])-1]==','){
    $_POST['newrow_str'] = substr($_POST['newrow_str'],0,-1);
  }
  // --------------------------------------------------------------------
  $post_newrow  = explode(',',$_POST['newrow_str']);
  $post_origrow = $_POST['origrow'];
  // --------------------------------------------------------------------
  // array_diff($post_newrow,$post_origrow)
  $diff = array_diff_assoc($post_newrow,$post_origrow);
  if(!empty($diff)){
    if(file_exists($wga_abs_dn_db)) {
      $contents = file_get_contents($wga_abs_dn_db);
      $xmlAry = XML_unserialize($contents);
    }

    if($xmlAry){
      $sequence_no = time();

      $i = 1;
      foreach($post_newrow as $wga_id){
        if(isset($xmlAry[$xml_rootobj][$wga_id])) { // if exist
          $xmlAry[$xml_rootobj][$wga_id]['arrangement'] = $sequence_no+($i++*10);
        }
      }

      if(!is_schedule_controller_busy()){
        write_db_xml($xmlAry);
        @header("Location: index.php{$nLUS}");
      }
    }
  }
}
?>

<?php include('dn_fbegin.inc'); ?>

<?php
  displayPATH(array(
                array('link'=>'./d_index.php'.$nLUS,
                      'desc'=>$lang['homepage']),
                array('link'=>$_SERVER['PHP_SELF'].$nLUS,
                      'desc'=>$lang['ctcs']['dn']['tasks'])
              ));
?>

<script type="text/javascript" src="/admin/js/overlibmws/overlibmws.js"></script>
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000"></div>
<table cellpadding="0" cellspacing="0" class="tbPath">
	<tr><td colspan="2" class="vncellempty">&nbsp;</td></tr>
</table>
<form action="<?=$_SERVER['PHP_SELF'].$nLUS?>" method="post" name="setup_form" id="setup_form">
<input type="hidden" name="arrangement" value="0">
<?displayRtnMessage($info_type, $message);?>

<div id="tbl_download_tasks"></div>

<?php

  $debug_str = ''; $debug = false;
  $update = false;
  $xmlAry = null;
  if(file_exists($wga_abs_dn_db)) {
    $contents = file_get_contents($wga_abs_dn_db);
    $xmlAry = XML_unserialize($contents);
  }

  /*
    <wgaUID>
      <url></url>
      <username_password_required></username_password_required>
      <username></username>
      <password></password>
      <location></location>
      <save_as></save_as>
      <size></size>
      <downloaded_size></downloaded_size>
      <timestamp></timestamp>
      <arrangement></arrangement>
      <in_queue></in_schedule>
    </wgaUID>
  */
  if($xmlAry){
    // be arranged from lowest to highest by timestamp
    $indexBytimestamp = array();
    if(is_array($xmlAry[$xml_rootobj])) {
      foreach($xmlAry[$xml_rootobj] as $wga_id => $item){
        $indexBytimestamp[$item["arrangement"]] = $wga_id;
      }
      ksort($indexBytimestamp);
    }

    $js_wga_elementID = '';
    // ---------------------------------------------------------
    // example:
    //   var myHash = new Hash({1:'OiD1207103228',2:'OiD1207103249'});
    //
    // position vs. wgaID,            called 'pos2wgaid'
    // wgaID    vs. url,              called 'wgaid2url'
    // wgaID    vs. location,         called 'wgaid2loc'
    // wgaID    vs. name for save as, called 'wgaid2saveas'
    // wgaID    vs. name for save as, called 'wgaid2shortn' // for short name
    // wgaID    vs. size,             called 'wgaid2size'
    // wgaID    vs. downloaded size,  called 'wgaid2dnsize'
    // wgaID    vs. created time,     called 'wgaid2addtime'
    // wgaID    vs. status icon,      called 'wgaid2status'
    // wgaID    vs. start/stop icon,  called 'wgaid2ssicon' // 1:start, 2:stop
    // wgaID    vs. pop-up info.,     called 'wgaid2pop'
    // wgaID    vs. download speed,   called 'wgaid2speed'
    // ---------------------------------------------------------
    $seq = 1;
    $started_hash_str = 'var %s = new Hash({'; $ended_hash_str = "});\n";
    // ---------------------------------------------------------
    $pos2wgaid     = sprintf($started_hash_str,'pos2wgaid');
    $wgaid2url     = sprintf($started_hash_str,'wgaid2url');
    $wgaid2loc     = sprintf($started_hash_str,'wgaid2loc');
    $wgaid2saveas  = sprintf($started_hash_str,'wgaid2saveas');
    $wgaid2shortn  = sprintf($started_hash_str,'wgaid2shortn');
    $wgaid2size    = sprintf($started_hash_str,'wgaid2size');
    $wgaid2dnsize  = sprintf($started_hash_str,'wgaid2dnsize');
    $wgaid2addtime = sprintf($started_hash_str,'wgaid2addtime');
    $wgaid2status  = sprintf($started_hash_str,'wgaid2status');
    $wgaid2ssicon  = sprintf($started_hash_str,'wgaid2ssicon');
    $wgaid2pop     = sprintf($started_hash_str,'wgaid2pop');
    $wgaid2speed   = sprintf($started_hash_str,'wgaid2speed');
    // ---------------------------------------------------------
    $newrow_str = "";
    // ---------------------------------------------------------
    foreach($indexBytimestamp as $wga_id){
      $item = $xmlAry[$xml_rootobj][$wga_id];
      // **********************************************************
      echo "<input type=\"hidden\" name=\"origrow[]\" value=\"".$wga_id."\">\n";
      $newrow_str .= $wga_id.',';
      // **********************************************************
      // 1 =>    complete: dn_complete.gif
      // 2 =>  incomplete: dn_incomplete.gif (ps. this item is not running)
      // 3 => downloading: dn_download.gif
      // 4 =>    schedule: dn_schedule.gif (ps. also can be queue status)
      // 5 =>       error: dn_error.gif
      $icons_table = array(1=>'complete',2=>'incomplete',3=>'download',4=>'schedule');
      $status_icon = 2; // incomplete by default
      $abs_fpid = $item['location'].'/'.$wga_log_prefix.substr($wga_id,3).'.wgpid';
      $abs_fwgspider = $item['location'].'/'.$wga_log_prefix.substr($wga_id,3).'.wgspider';
      $abs_fwglog = $item['location'].'/'.$wga_log_prefix.substr($wga_id,3).'.wglog';
      
      if(wgpid_running($abs_fpid)){ $status_icon = 3; } // 'download'
      // **********************************************************
      // short file name if need
      $short_save_as_filename = $item['save_as'];
      if(strlen($short_save_as_filename)>20) {
        $short_save_as_filename = substr($item['save_as'],0,20).' ...';
      }
      // **********************************************************
      // total size
      if(is_numeric($item['size'])&&$item['size']>=0){
        $size = $item['size'];
      } else { // wgspider
        $size = wgspider_size($abs_fwgspider);
      }
      // **********************************************************
      // downloaded size
      if($status_icon != 3){
        $abs_save_as = $item['location'].'/'.$item['save_as'];
        $downloaded_size = downloaded_filesize($abs_save_as);

        if($downloaded_size!=$item['downloaded_size']){
          $xmlAry[$xml_rootobj][$wga_id]['downloaded_size'] = $downloaded_size;
          if(is_htmlized_index_for_ftp($abs_fwglog)){ // finished
            $xmlAry[$xml_rootobj][$wga_id]['size'] = $downloaded_size;
            $xmlAry[$xml_rootobj][$wga_id]['downloaded_size'] = $downloaded_size;
            $size = $downloaded_size;
          }
          $update = true;

          // debug
          if($debug)
            $debug_str .= '['.date('Y/m/d H:i:s').'] '.$item['save_as'].':downloaded: '.$downloaded_size."\n";
        }
      }
      // **********************************************************
      if($downloaded_size==$size&&is_numeric($downloaded_size)&&is_numeric($size)) {
        $status_icon = 1;
      }
      if($item['in_queue']==strYES&&$status_icon!=3&&$status_icon!=1) {
        $status_icon = 4;
      }
      if($status_icon==2){ // incomplete status
        if(strpos($item['url'],'http://')!==false
            || strpos($item['url'],'https://')!==false){
          // did this item happen on HTTP 4xx/5xx issue?
          $spider_contents = file_exists($abs_fwgspider)?file_get_contents($abs_fwgspider):'';
          if(is_client_error_4xx($spider_contents) || is_server_error_5xx($spider_contents)){
            $status_icon = 5;
            $openERR = ' [<a href="#" onClick="showERROR(\\\''.$wga_id.'\\\',1);"><strong style="color:#FFCC00;">?</strong></a>]';
          }
        } else if(strpos($item['url'],'ftp://')!==false){
          $wglog_contents = file_exists($abs_fwglog)?file_get_contents($abs_fwglog):'';
          if(is_error_in_ftp($wglog_contents) || is_client_error_4xx($wglog_contents) || is_server_error_5xx($wglog_contents)){
            // we need to check whether the error happened caused by HTTP(S) requested
            // becasue we may use Proxy for download
            $status_icon = 5;
            $openERR = ' [<a href="#" onClick="showERROR(\\\''.$wga_id.'\\\',2);"><strong style="color:#FFCC00;">?</strong></a>]';
          }
        }
      }
      // **********************************************************
      unset($IPADDR_ONLY);
      $IPADDR_ONLY = explode(":",$_IPADDR);
      $information = '<strong>'.$lang['ctcs']['dn']['pfname'].':</strong> '.$item['save_as'].'<br>'.
                     '<strong>'.$lang['ctcs']['dn']['purl'].':</strong> '.$item['url'].'<br>'.
                     '<strong>'.$lang['ctcs']['dn']['ploc'].':</strong> \\\\\\\\\\\\\\\\'.$IPADDR_ONLY[0].'\\\\\\\\'.basename($item['location']);
      // **********************************************************
      $pos2wgaid     .= $seq++.":'{$wga_id}',";
      // **********************************************************
      $wgaid2url     .= "'{$wga_id}':'".htmlspecialchars($item['url'])."',";
      $wgaid2loc     .= "'{$wga_id}':'".htmlspecialchars($item['location'])."',";
      $wgaid2saveas  .= "'{$wga_id}':'".htmlspecialchars($item['save_as'])."',";
      $wgaid2shortn  .= "'{$wga_id}':'".htmlspecialchars($short_save_as_filename).$openERR."',"; unset($openERR);
      $wgaid2size    .= "'{$wga_id}':'".file_size($size)."',";
      $wgaid2dnsize  .= "'{$wga_id}':'".displayDownloadedSize($downloaded_size, $size)."',";
      $wgaid2addtime .= "'{$wga_id}':'".date('Y/m/d H:i:s',$item['timestamp'])."',";
      $wgaid2status  .= "'{$wga_id}':'".$status_icon."',";
      $wgaid2ssicon  .= "'{$wga_id}':'".(($status_icon==3||$status_icon==4)?2:1)."',";
      $wgaid2pop     .= "'{$wga_id}':'".$information."',";
      $wgaid2speed   .= "'{$wga_id}':'-',";
      // **********************************************************
      // make array structure for ajax (javascript)
      if($status_icon==3) {
        $js_wga_elementID .= '"'.$wga_id.'",';
      }
      // **********************************************************
      // reset
      unset($size);
      unset($downloaded_size);
    }
    // ---------------------------------------------------------
    if ($pos2wgaid[strlen($pos2wgaid)-1]==',')         { $pos2wgaid     = substr($pos2wgaid,     0, -1); }
    if ($wgaid2url[strlen($wgaid2url)-1]==',')         { $wgaid2url     = substr($wgaid2url,     0, -1); }
    if ($wgaid2loc[strlen($wgaid2loc)-1]==',')         { $wgaid2loc     = substr($wgaid2loc,     0, -1); }
    if ($wgaid2saveas[strlen($wgaid2saveas)-1]==',')   { $wgaid2saveas  = substr($wgaid2saveas,  0, -1); }
    if ($wgaid2shortn[strlen($wgaid2shortn)-1]==',')   { $wgaid2shortn  = substr($wgaid2shortn,  0, -1); }
    if ($wgaid2size[strlen($wgaid2size)-1]==',')       { $wgaid2size    = substr($wgaid2size,    0, -1); }
    if ($wgaid2dnsize[strlen($wgaid2dnsize)-1]==',')   { $wgaid2dnsize  = substr($wgaid2dnsize,  0, -1); }
    if ($wgaid2addtime[strlen($wgaid2addtime)-1]==',') { $wgaid2addtime = substr($wgaid2addtime, 0, -1); }
    if ($wgaid2status[strlen($wgaid2status)-1]==',')   { $wgaid2status  = substr($wgaid2status,  0, -1); }
    if ($wgaid2ssicon[strlen($wgaid2ssicon)-1]==',')   { $wgaid2ssicon  = substr($wgaid2ssicon,  0, -1); }
    if ($wgaid2pop[strlen($wgaid2pop)-1]==',')         { $wgaid2pop     = substr($wgaid2pop,     0, -1); }
    if ($wgaid2speed[strlen($wgaid2speed)-1]==',')     { $wgaid2speed   = substr($wgaid2speed,   0, -1); }
    // ---------------------------------------------------------
    // separator by comma (,)
    echo "<input type=\"hidden\" name=\"newrow_str\" value=\"".$newrow_str."\">\n";
    // ---------------------------------------------------------
    $pos2wgaid     .= $ended_hash_str;
    $wgaid2url     .= $ended_hash_str;
    $wgaid2loc     .= $ended_hash_str;
    $wgaid2saveas  .= $ended_hash_str;
    $wgaid2shortn  .= $ended_hash_str;
    $wgaid2size    .= $ended_hash_str;
    $wgaid2dnsize  .= $ended_hash_str;
    $wgaid2addtime .= $ended_hash_str;
    $wgaid2status  .= $ended_hash_str;
    $wgaid2ssicon  .= $ended_hash_str;
    $wgaid2pop     .= $ended_hash_str;
    $wgaid2speed   .= $ended_hash_str;
    // ---------------------------------------------------------
    if ($js_wga_elementID[strlen($js_wga_elementID)-1] == ','){
      $js_wga_elementID = substr($js_wga_elementID, 0, -1);
    }
    // ---------------------------------------------------------
    // UPDATE if need
    if($update && !is_schedule_controller_busy()){
      write_db_xml($xmlAry);
    }
    // ---------------------------------------------------------
  }
?>

<script type="text/javascript">
<!--
  <?=$pos2wgaid?>
  <?=$wgaid2url?>
  <?=$wgaid2loc?>
  <?=$wgaid2saveas?>
  <?=$wgaid2shortn?>
  <?=$wgaid2size?>
  <?=$wgaid2dnsize?>
  <?=$wgaid2addtime?>
  <?=$wgaid2status?>
  <?=$wgaid2ssicon?>
  <?=$wgaid2pop?>
  <?=$wgaid2speed?>

  function displayTasksTable(elementID){
    var tbl = "";

    // ------------------------------------------------------------------
    tbl += '<table cellpadding="0" cellspacing="0" class="tbSetup">';
    // ------------------------------------------------------------------
    tbl += '<tr>';
    tbl += '<td colspan="5" class="listrgrid_action" style="padding-right: 5px; text-align: right;" nowrap>';
    tbl += '<strong><?=$lang['ctcs']['dn']['pschedulestatus']?>: <span style="color:#F4DD0B;"><?=($_dn_enable_sc)?__STR_ENABLE__:__STR_DISABLE__?></span></strong>';
    tbl += '</td>';
    tbl += '<td>&nbsp;</td>';
    tbl += '</tr>';
    // ------------------------------------------------------------------
    tbl += '<tr>';
    tbl += '<td class="listhdrr" style="width: 250px;" ><?=$lang['ctcs']['dn']['pfname']?></td>';
    tbl += '<td class="listhdrr" style="width:  80px;" nowrap><?=$lang['ctcs']['dn']['psize']?></td>';
    tbl += '<td class="listhdrr" style="width: 120px;" nowrap><?=$lang['ctcs']['dn']['pdned']?></td>';
    tbl += '<td class="listhdrr" style="width:  80px;" nowrap><?=$lang['ctcs']['dn']['pspeed']?></td>';
    tbl += '<td class="listhdrr" nowrap><?=$lang['ctcs']['dn']['padded']?></td>';
    tbl += '<td class="listrgrid_action" nowrap>&nbsp;</td>';
    tbl += '</tr>';


<?php if($xmlAry): ?>
    // ------------------------------------------------------------------
    var cur_pos = 1;
    var newrow_str = ''; // reset
    var icon_map = new Array('complete','incomplete','download','schedule','error');
    pos2wgaid.each(function(pair) {
      var pos = pair.key;
      var wgaid = pair.value;

      var url     = wgaid2url.get(wgaid);
      var loc     = wgaid2loc.get(wgaid);
      var saveas  = wgaid2saveas.get(wgaid);
      var shortn  = wgaid2shortn.get(wgaid);
      var size    = wgaid2size.get(wgaid);
      var dnsize  = wgaid2dnsize.get(wgaid);
      var addtime = wgaid2addtime.get(wgaid);
      var status  = wgaid2status.get(wgaid);
      var ssicon  = wgaid2ssicon.get(wgaid);
      var pop     = wgaid2pop.get(wgaid);
      var speed   = wgaid2speed.get(wgaid);

      tbl += '<tr id="tr_'+wgaid+'">';

      // ----- File name -----
      tbl += '<td class="listrgrid_first listrgrid_dn_first">';
      tbl += '<img src="/ctcs/image/dn_'+icon_map[status-1]+'.gif" style="vertical-align: middle;">&nbsp;';
      tbl += '<a href="javascript:void(0);"';
      tbl += 'onclick=\'return overlib("'+pop+'",';
      tbl += 'STICKY,';
      tbl += 'CAPTION, "<?=$lang['ctcs']['dn']['sinfo']?>",';
      tbl += 'FGCLASS, "olFGCLASS",';
      tbl += 'CGCLASS, "olCGCLASS",';
      tbl += 'BGCLASS, "olBGCLASS_URL",';
      tbl += 'TEXTFONTCLASS, "olTEXTFONTCLASS",';
      tbl += 'CAPTIONFONTCLASS, "olCAPTIONFONTCLASS",';
      tbl += 'CLOSEFONTCLASS, "olCLOSEFONTCLASS",';
      tbl += 'CLOSECLICK, CLOSETEXT, "&times;");\'';
      tbl += 'onmouseout="return nd();"><img src="/ctcs/image/dn_info.gif" style="vertical-align: middle;"></a>&nbsp;';
      tbl += '<a href="#" onClick="onMoveUP(\''+wgaid+'\','+cur_pos+')";><img src="/ctcs/image/dn_move_up.gif" style="vertical-align: middle;"/></a>';
      tbl += '<a href="#" onClick="onMoveDown(\''+wgaid+'\','+cur_pos+')";><img src="/ctcs/image/dn_move_down.gif" style="vertical-align: middle;"/></a>';
      tbl += '&nbsp;'+shortn+'&nbsp;&nbsp;';
      tbl += '</td>';
      // ----- Total Size -----
      tbl += '<td class="listrgrid"><span id="size_'+wgaid+'">'+size+'</span></td>';
      // ----- Downloaded Size -----
      tbl += '<td class="listrgrid"><span id="downloaded_'+wgaid+'">'+dnsize+'</span></td>';
      // ----- Speed -----
      tbl += '<td class="listrgrid"><span id="speed_'+wgaid+'">'+speed+'</span></td>';
      // ----- Created Date and Time -----
      tbl += '<td class="listrgrid">'+addtime+'</td>';
      // ----- Start/Stop or Delete Buttons -----
  		tbl += '<td class="listrgrid_action" nowrap>';
   		tbl += '<a href="#" onClick="onStartStop(\''+wgaid+'\','+ssicon+')"><img src="/ctcs/image/dn_'+((ssicon=='1')?'start':'stop')+'.gif" style="vertical-align: middle;"/></a>';
      tbl += '&nbsp;';
      if(status != 3)
        tbl += '<a href="#" onClick="onDelete(\''+wgaid+'\',\''+saveas+'\');"><img src="/ctcs/image/dn_del.gif" style="vertical-align: middle;"/></a>';
      
      tbl += '</td>';

      tbl += '</tr>';

      cur_pos++;

      // render the value of the 'newrow_str'
      newrow_str += wgaid+',';
    });
<?php endif; ?>
    // ------------------------------------------------------------------
    tbl += '<tr>';
    tbl += '<td colspan="5">&nbsp;</td>';
    tbl += '<td class="listrgrid_action" style="padding-top: 5px;" nowrap>';
    tbl += '<a href="javascript:openAdd();"><img src="/ctcs/image/dn_add.gif" style="vertical-align: middle;"></a>';
    tbl += '</td>';
    tbl += '</tr>';
    // ------------------------------------------------------------------
<?php if(is_array($xmlAry[$xml_rootobj])&&count($xmlAry[$xml_rootobj])>=2): ?>
    tbl += '<tr id="arrangement_submit">';
    tbl += '<td colspan="5" style="text-align: right;">';
    tbl += '<input name="submit" type="submit" id="submit" class="formbtn" value="<?=__STR_SUBMIT__?>">';
    tbl += '</td>';
    tbl += '<td>&nbsp;</td>';
    tbl += '</tr>';
<?php endif; ?>
    // ------------------------------------------------------------------
    tbl += '<tr><td colspan="6" calss="vncellempty">&nbsp;</td></tr>';
    // ------------------------------------------------------------------
    tbl += '<tr>';
    tbl += '<td colspan="5" class="icon_desc" style="border: 1px #034A89 solid; padding: 2px 2px 2px 2px; border-bottom: 0;" nowrap>';
    tbl += '<ul style="padding:0;margin:0;padding-left:5px;">';
    tbl += '<li class="icon_list"><img src="/ctcs/image/dn_info.gif" style="vertical-align: middle;"> <?=$lang['ctcs']['dn']['sinfo']?></li>';
    tbl += '<li class="icon_list"><img src="/ctcs/image/dn_move_up.gif" style="vertical-align: middle;"> <?=$lang['ctcs']['dn']['moveup']?></li>';
    tbl += '<li class="icon_list"><img src="/ctcs/image/dn_move_down.gif" style="vertical-align: middle;"> <?=$lang['ctcs']['dn']['movedown']?></li>';
    tbl += '</ul>';
    tbl += '</td>';
    tbl += '<td>&nbsp;</td>';
    tbl += '</tr>';
    tbl += '<tr>';
    tbl += '<td colspan="5" class="icon_desc" style="border: 1px #034A89 solid; padding: 2px 2px 2px 2px; border-top: 0;" nowrap>';
    tbl += '<ul style="padding:0;margin:0;padding-left:5px;">';
    tbl += '<li class="icon_list"><img src="/ctcs/image/dn_complete.gif" style="vertical-align: middle;"> <?=$lang['ctcs']['dn']['sok']?></li>';
    tbl += '<li class="icon_list"><img src="/ctcs/image/dn_incomplete.gif" style="vertical-align: middle;"> <?=$lang['ctcs']['dn']['sstop']?></li>';
    tbl += '<li class="icon_list"><img src="/ctcs/image/dn_error.gif" style="vertical-align: middle;"> <?=$lang['ctcs']['dn']['serror']?></li>';
    tbl += '<li class="icon_list"><img src="/ctcs/image/dn_download.gif" style="vertical-align: middle;"> <?=$lang['ctcs']['dn']['sdning']?></li>';
    tbl += '<li class="icon_list"><img src="/ctcs/image/dn_schedule.gif" style="vertical-align: middle;"> <?=$lang['ctcs']['dn']['squeue']?></li>';
    tbl += '</ul>';
    tbl += '</td>';
    tbl += '<td>&nbsp;</td>';
    tbl += '</tr>';
    // ------------------------------------------------------------------
    tbl += '</table>';
    // ------------------------------------------------------------------

    if(document.getElementById(elementID)) {
      document.getElementById(elementID).innerHTML = tbl;
<?php if($xmlAry): ?>
      document.setup_form.newrow_str.value = newrow_str;
<?php endif; ?>
    }
  }

  displayTasksTable('tbl_download_tasks');
-->
</script>

<table cellpadding="0" cellspacing="0" class="tbSetup">
<tr>
  <td colspan="7" class="vncellempty">
    <div id="note">
      <strong><?=htmlspecialchars(__STR_NOTE__);?>:</strong>
      <ol>
        <li><?=$lang['ctcs']['dn']['inote1']?></li>
      </ol>
    </div>
  </td>
</tr>
</table>
</form>

<script type="text/javascript">
//<![CDATA[
<?php if($js_wga_elementID): ?>
    var elementIDArray = new Array(<?=$js_wga_elementID?>);

    var progress = new AjaxWGAQuery("<?=session_id()?>", elementIDArray, {
        frequency: 10,
        reload_page: "index.php<?=$nLUS?>"
    });
    progress.start();
<?php else: ?>
    setTimeout("window.location='index.php<?=$nLUS?>'", 60000*5);
<?php endif; ?>
//]]>
</script>
<?php if(is_array($xmlAry[$xml_rootobj])&&count($xmlAry[$xml_rootobj])>=2): ?>
<script>toggleHTMLItem('arrangement_submit',false);</script>
<?php endif; ?>
<iframe name="hidden_frame" src="/blank.html" width="0" height="0" frameborder="0" framespacing="0" border="0"></iframe>
<?php include('NEW_fend.inc'); ?>
