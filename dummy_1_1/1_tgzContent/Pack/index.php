#!/usr/bin/php
<?php

$tabidx=2;
require_once('extras_config.inc');

$l = &$lang['extras']['packs']['dummy'];
$help_anchor = $htmlObj->Help('', '');

$head_js = <<<EOD
<link rel="stylesheet" href="/extras/extras.css" type="text/css" media="screen, projection">
<script type='text/javascript' src='/extras/js/jquery-2.1.1.js'></script>
<script type="text/javascript">
<!--

-->
</script>
EOD;

$onload_jsfun = '';

if ($_POST && !$_POST['ACKNOWLEDGE']) {
  unset($info_type);
  unset($message);

  if ($_POST['param']) {
  }

  $info_type = __INFO_ERROR__; // type of default message
}

?>

<?php include('extras_fbegin.inc'); ?>
<?php
  displayPATH(array(
                array('link'=>'/extras/index.php'.$nLUS,
                      'desc'=>$lang['homepage']),
                array('link'=>$_SERVER['PHP_SELF'].$nLUS,
                      'desc'=>$l['package'])
              ));
?>
<form action="<?=$_SERVER['PHP_SELF'].$nLUS?>" method="post" name="setup_form" id="setup_form">
<input type="hidden" value='' name='param'>
<div style="margin:200px 10px 10px 10px;">
   <p><?= $l['desc']?></p>
</div>
<?php include('NEW_fend.inc'); ?>