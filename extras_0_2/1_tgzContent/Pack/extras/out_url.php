#!/usr/bin/php
<!doctype html>
<html>
<head>
<script>

function getCookie(cookieName) {
   var re = new RegExp('[; ]'+cookieName+'=([^\\s;]*)');
   var sMatch = (' '+document.cookie).match(re);
   if (cookieName && sMatch) return unescape(sMatch[1]);
   return false;
}

function setCookie(name, value, expire) {
   var date = new Date();
   date.setTime(date.getTime() + expire);
   var expires = "; expires=" + date.toGMTString();
   document.cookie = name+'='+value+(expire?expires:'')+'; path =/';
}

window.onload=function() {
   var Extras = getCookie('Extras');
   var test = "<?=$_GET['outurl']?>";
   if (Extras &&
      ((test.indexOf('/extras/packs/extplorer/index.php')!=-1) ||
       (test.indexOf('/fpkmgr/index.php')!=-1))) {
         setCookie("Extras", Extras, 900000);
         document.location.href=test;
   } else {
      alert("!!!WARNING !!!\n\n\nSomething wrong in your request !\n\nTransmit this information to the developper.\n\n\n");
      document.location.href="<?=$_SERVER['HTTP_REFERER']?>";
   }
};

</script>
</head>
<body>
</body>
</html>
