<script type="text/javascript">

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

var previousOnunload = window.onunload;

window.onunload=function() {
   if (previousOnunload) { previousOnunload(); }
   var Extras = getCookie('Extras');
   if (Extras) {
      setCookie("Extras", Extras, 10000);
   }
};

var previousOnload = window.onload;

window.onload=function(){
   if (previousOnload) { previousOnload(); }
   var Extras = getCookie('Extras');
   if (Extras) {
      if (0<?=@file_exists('/proto/SxM_webui/extras/options/Option5')?>) {
         var Timeout=parseInt("0<?=@file_get_contents('/proto/SxM_webui/extras/options/Option5')?>");
         Timeout=((Timeout)?Timeout:5)*60000;
         setCookie("Extras", Extras, Timeout);
         setTimeout("location.reload(true);", Timeout);
      } else {
         setCookie("Extras", Extras, 0);
      }
   }
};
 
</script>