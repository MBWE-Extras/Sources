#!/usr/bin/php -q
<?php

require_once('wixXML.class');

$XmlObj = new wixXML();
$XmlRoot=$XmlObj->sys_var['xml_rootobj'];
$extrasPath="/proto/SxM_webui/extras/lang/";
$extrasNew="/proto/SxM_webui/extras/packs/";
$URLpacks="https://raw.githubusercontent.com/MBWE-Extras/Downloads/master/Check/";
$langs=array('en.xml','de.xml','es.xml','fr.xml','gb.xml','it.xml','jp.xml','ko.xml','zh.xml');

$packsOld=array();
if (file_exists($extrasPath.$langs[0])) {
   $XmlObj->ParseXML($extrasPath.$langs[0], $XmlRoot, "UTF-8");
   foreach ($XmlObj->xmlary[$XmlRoot]['extras']['packs'] as $key=>$value) {
      $packsOld[$key]=max(split(",",$value['packs']));
   }
}

foreach ($langs as $l) {
   @copy($URLpacks.$l, $extrasPath.$l);
}

if (file_exists($extrasPath.$langs[0])) {
   $XmlObj->ParseXML($extrasPath.$langs[0], $XmlRoot, "UTF-8");
   foreach ($XmlObj->xmlary[$XmlRoot]['extras']['packs'] as $key=>$value) {
      if (!array_key_exists($key, $packsOld) || (max(split(",",$value['packs'])) > $packsOld[$key])) {
         touch ($extrasNew.$key.".new");
      }
   }
}

?>
