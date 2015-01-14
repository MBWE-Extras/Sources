<?php
require_once('wixBase.class');
require_once('xml.inc');

define("PEER_ID_PREFIX", "-CD0302-"); // Peer ID prefix

define("strYES", "YES");
define("strNO",  "NO");

$cBase             = new wixBase();
$xml_rootobj       = $cBase->sys_var["xml_rootobj"];
$config_file       = $cBase->sys_var["config_dir"].'/'.$cBase->sys_var["config_file"];
$ctcs_dir          = $cBase->sys_var["ctcs_dir"];
$varrun_path       = $cBase->sys_var["varrun_path"];
$btadmin_name      = $cBase->sys_var["btwebui_admin"];
$torrent_dir       = "/DataVolume/_torrent_";
$wga_log_prefix    = ".wga";
$wga_abs_dn_db     = $ctcs_dir."/dn.xml";
$wga_abs_sc_db     = $ctcs_dir."/dn_settings.inc";
$ctcs_imgdir       = "ctcs";
$max_running_tasks = 3;

if (!is_dir($torrent_dir)) {
  @mkdir($torrent_dir, 0755);
  @chmod($torrent_dir, 0755);
}

function _false($value){
  return ($value===false)?"false":$value;
}

function destination_share(){
  global $xml_rootobj, $config_file;  

  $contents = file_get_contents($config_file);
  $configAry = XML_unserialize($contents);

  $dest_shr = $configAry[$xml_rootobj]['downloadshare'];
  
  return strlen($dest_shr)?$dest_shr:false;
}

function is_destination_share_good(){
  if (($share = destination_share())!==false) {
    @system("/bin/df \"{$share}\" >/dev/null 2>&1", $retval);
    if (is_numeric($retval) && $retval == 0) {
      return true;
    }
  }
  return false;
}

function redirect_error_page(){
  if (!is_destination_share_good()) {
    @header("Location: torrent_error.php");
  }
}

function ctcs_del_backup($absfile){
  @unlink("{$absfile}.bak");
}

function ctcs_backup($absfile){
  if (@file_exists($absfile)) {
    //print "##### {$absfile} #####\n";
    ctcs_del_backup($absfile);
    return @copy($absfile, "{$absfile}.bak");
  }

  return false;
}

function ctcs_rollback($absfile){
  if (@file_exists("{$absfile}.bak")) {
    //print "!!!!! {$absfile} !!!!!\n";
    if (@copy("{$absfile}.bak", $absfile)) {
      ctcs_del_backup($absfile);
    }
  }
}

function ctcs_lock($abslock){
  $n = 0;
  while ($n < 20) {
    /* open the lock file in append mode to avoid race condition */
    if (($fd = @fopen($abslock, "x")) !== FALSE) {
      /* succeeded */
      fclose($fd);
      return;
    } else {
      /* file locked, wait and try again */
      usleep(500000); // wait 0.5 second
      $n++;
    }
  }
}

function ctcs_unlock($abslock){
  if (file_exists($abslock)) {
    @unlink($abslock);
  }
}

function is_ctcs_locked($abslock){
  $n = 0;
  while ($n < 20) {
    if (is_file($abslock)) {
      usleep(500000); // wait 0.5 second
      $n++;
    } else {
      return false;
    }
  }

  return true;
}

function checkpieces($torrent_file){
  @touch("/tmp/checkpieces.{$torrent_file}");
}

function checkpieces_done($torrent_file){
  print ">> checkpieces_done('{$torrent_file}')\n";
  @unlink("/tmp/checkpieces.{$torrent_file}");
}

function checkpieces_count(){
  $count = 0;
  @exec("/bin/ls /tmp/checkpieces.* 2>&1", $output, $retval);
  if (is_numeric($retval) && $retval == 0) {
    foreach($output as $out){
      if (strstr($out, "/tmp/checkpieces.")!==false) {
        $count++;
      }
    }
  }
  return $count;
}

function is_checkpieces($torrent_file){
  return @file_exists("/tmp/checkpieces.{$torrent_file}");
}

function is_unable($torrent_file){
  return @file_exists("/tmp/failedcheckpieces.{$torrent_file}");
}

function encode_peer_id($peer_id){
  return base64_encode(urlencode($peer_id));
}

function decode_peer_id($encode_peer_id) {
  return urldecode(base64_decode($encode_peer_id));
}

function verify_peer_id($peer_id){
  if ($peer_id == "") { return false; }

  if (substr($peer_id, 0, strlen(PEER_ID_PREFIX)) != PEER_ID_PREFIX) {
    return false;
  }

  return $peer_id;
}

function remove_failedcheckpieces($torrent_file){
  if (@file_exists("/tmp/failedcheckpieces.{$torrent_file}")) {
    @unlink("/tmp/failedcheckpieces.{$torrent_file}");
  }
}

function touch_failedcheckpieces($torrent_file){
  if (!@file_exists("/tmp/failedcheckpieces.{$torrent_file}")) {
    @system("/bin/touch \"/tmp/failedcheckpieces.{$torrent_file}\"");
  }
}

function run_ctorrrent_script($torrent, $dir){
  global $torrent_dir;

  remove_failedcheckpieces($torrent);
  $dinfo = explode("/",$dir);
  $vol   = $dinfo[1];
  $shr   = $dinfo[2];
  if ($vol!=""&&$shr!="") {
    @system("/bin/df \"/{$vol}/{$shr}\" >/dev/null 2>&1", $retval);
    if (!is_numeric($retval) || $retval != 0) {
      touch_failedcheckpieces($torrent); return false;
    }
  } else { touch_failedcheckpieces($torrent); return false; }

  if ($dir!=null||$dir!="") {
    if (!is_dir($dir)) {
      @mkdir($dir, 0755);
      @chown($dir, "admin");
      @chgrp($dir, "jewab");
    }
    @chdir($dir);
    @exec("/bin/nice -n 19 /usr/bin/ctorrent -d -e \"8760\" -S \"0.0.0.0:7777\" \"{$torrent_dir}/{$torrent}\"");
    return true;
  }

  return false;
}

function is_btadmin(){
  global $btadmin_name;
  if ($btadmin_name != $GLOBALS['__SESSION']['s_user']) {
    $GLOBALS['__SESSION']=array();
    @session_destroy();
    @header("Location: {$GLOBALS['http_host']}");
  }
}

function btadmin_passwd_check($check_passwd){
  include('.ctcspasswd');
  return ($btadmin_passwd==$check_passwd)?true:false;
}
?>
