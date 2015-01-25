<?php
require_once ('guiconfig.inc');

function copydtmod($file1, $file2){
   if (file_exists($file1)) {
      @copy($file1, $file2);
      $dt = filemtime($file1);
      touch($file2, $dt);
      chmod($file2, fileperms($file1));
   }
}

function compfiles($file1, $file2){
   if (!file_exists($file1)) {
      return false;
   } else {
      $crc1 = strtoupper(dechex(crc32(@file_get_contents($file1))));
      $crc2 = strtoupper(dechex(crc32(@file_get_contents($file2))));
      return ($crc1==$crc2);
   }
}

function rfolders($path, $blacklist){
   $dirHandle = opendir($path);
   while($item = readdir($dirHandle)) {
      if (is_dir($newPath = $path."/".$item) && !in_array($item, $blacklist)) {
         $folders = array_merge($folders, (array)$newPath, rfolders($newPath, &$blacklist));
      }
   }
   return $folders;
} # 0,247s

function rglobfolders($path='', $blacklist) {
   $paths=glob($path.'/*', GLOB_ONLYDIR);
   foreach ($paths as $path) {
      if (!in_array($path, $blacklist)) {
         $folders = array_merge($folders, (array)$path, rglobfolders($path, &$blacklist));
      }
   }
   return $folders;
} # 0,066s


$gray_js = <<<EOD
<script type='text/javascript' src='/extras/js/jquery-2.1.1.js'></script>
<script type="text/javascript">
<!--

function grayscale(src) {
   var supportsCanvas = !!document.createElement('canvas').getContext;
   if (supportsCanvas) {
      var canvas = document.createElement('canvas'), 
      context = canvas.getContext('2d'), 
      imageData, px, length, i = 0, gray, 
      img = new Image();

      img.src = src;
      canvas.width = img.width;
      canvas.height = img.height;
      context.drawImage(img, 0, 0);

      imageData = context.getImageData(0, 0, canvas.width, canvas.height);
      px = imageData.data;
      length = px.length;

      for (; i < length; i += 4) {
          gray = px[i] * .3 + px[i + 1] * .59 + px[i + 2] * .11;
          px[i] = px[i + 1] = px[i + 2] = gray;
      }
				
      context.putImageData(imageData, 0, 0);
      return canvas.toDataURL();
   } else {
      return src;
   }
}

function onloadgray() {
   $('.gray').each(function() {
	this.src = grayscale(this.src);
   });
}

-->
</script>
EOD;

?>
