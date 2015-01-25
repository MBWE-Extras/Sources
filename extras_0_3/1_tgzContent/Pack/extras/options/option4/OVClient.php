#!/usr/bin/php
<?php
   if (isset($_GET['file'])) {
      $tableau=split('¤',$_GET['file']);
      if (count($tableau) == 1) {
         $tableau[0]="/shares/Public/WWW/OpenVpn/ClientKeys/OVClient/".$tableau[0];
         header("Content-type: application/octet-stream");
         header("Content-Disposition: attachment; filename=\"" . basename($tableau[0]) . '"' );
         header("Content-Length: ". filesize($tableau[0]));
         readfile($tableau[0]);
      }
   }
?>
