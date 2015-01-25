#!/usr/bin/php
<?php

$skip_check_initsetup = TRUE;
$tabidx = 2;
require_once('extras_config.inc');

$help_anchor = '';

$l = &$lang['extras']['packs']['extras'];

$head_js = <<<EOD
<link rel="stylesheet" href="/extras/extras.css" type="text/css" media="screen, projection">
<script type='text/javascript' src='js/jquery-2.1.1.js'></script>
<script type="text/javascript">
<!--

function ReadLog(pack) {
   $.ajax({
      type: 'GET',
      data: {
          ajaxtool: 'ReadLog',
          file: pack,
      },
      url: 'extras_ajaxtools.php',
      async: true,
      cache: false,
      success: function(data){
         $("textarea#Log" + pack).val(data);
         if ($("input#Clear" + pack).css('display') == 'none') {
            $("textarea#Desc" + pack).css('display','none');
            $("textarea#Log" + pack).css('display','inline');
            $("input#Clear" + pack).css('display','inline');
            $("input#Log" + pack).val("${l['closelog']}");
         } else {
            $("textarea#Desc" + pack).css('display','inline');
            $("textarea#Log" + pack).css('display','none');
            $("input#Clear" + pack).css('display','none');
            $("input#Log" + pack).val("${l['viewlog']}");
         }
      },
      error: function(error) {
         console.log(error);
         alert("Error ReadLog : " + error.responseText);
      }
   });
}

function ClearLog(pack) {
   $.ajax({
      type: 'GET',
      data: {
          ajaxtool: 'ClearLog',
          file: pack,
      },
      url: 'extras_ajaxtools.php',
      async: true,
      cache: false,
      success: function() {
         $("textarea#Log" + pack).val('');
         $("textarea#Desc" + pack).css('display','inline');
         $("textarea#Log" + pack).css('display','none');
         $("input#Clear" + pack).css('display','none');
         $("input#Log" + pack).val("${l['viewlog']}");
      },
      error: function(error) {
         console.log(error);
         alert("Error ClearLog : " + error.responseText);
      }
   });
}

function DelNew(pack) {
   $.ajax({
      type: 'GET',
      data: {
          ajaxtool: 'DelNew',
          file: pack,
      },
      url: 'extras_ajaxtools.php',
      async: true,
      cache: false,
      success: function() {
         $("img#New" + pack).css('display','none');
         $("img#State" + pack).css('display','inline');
      },
      error: function(error) {
         console.log(error);
         alert("Error DelNew : " + error.responseText);
      }
   });
}

function CheckPacks() {
   $.ajax({
      type: 'GET',
      url: 'extras_checkpacks.php',
      async: true,
      cache: false,
      success: function() {
         $('#setup_form').submit();
      },
      error: function(error) {
         console.log(error);
         alert("Error CheckPacks : " + error.responseText);
      }
   });
}

function Apply(pack) {
   what=$("select#Version" + pack).val();
   if (what.search("Uninstall")) {
      $('#modal1').text("${l['mess_download']}");
      $('#modal1').text($('#modal1').text().replace('#',what));
      $('#modalCheck').prop('checked', true);

      $.ajax({
         type: 'GET',
         data: {
             ajaxtool: 'Download',
             file: $("select#Version" + pack).val(),
         },
         url: 'extras_ajaxtools.php',
         async: true,
         cache: false,
         success: function(data) {
            if (data != "unfound") {
               $('#modal1').text("${l['mess_inst']}");
               $('#modal1').text($('#modal1').text().replace('#',what));
            } else {
               $('#modal1').text("${l['mess_unfound']}");
               $('#modal1').text($('#modal1').text().replace('#',what));
               $('#modal1').css('color', 'red');
               out=0;
               Boucle=setInterval(function() {
                  if (out++ > 4) {
                     clearInterval(Boucle);
                     $('#setup_form').submit();
                 }
               },500);
            }
         }
      });
      i=0;
      out=0;
      Boucle=setInterval(function() {
         $.ajax({
            type: 'GET',
            data: { ajaxtool: 'ReadStatus', },
            url: 'extras_ajaxtools.php',
            async: true,
            cache: false,
            success: function(data) {
               if (data) {
                  data=data.trim();
                  if (data.split('%',2)[1].length) {
                     $('#modal1').text(data.split('%',2)[1]);
                     $('#progressbar').prop('max',100);
                     $('#progressbar').val(parseInt(data));
                  }
                  if (!out && parseInt(data) >= 100) {
                     out++;
                     clearInterval(Boucle);
                     $('#setup_form').submit();
                  }
               }
            },
         });

         if (i++ > 300) {
            $('#modal1').text("${l['mess_timeout']}");
            $('#modal1').css('color', 'red');
            if (out++ > 4) {
               clearInterval(Boucle);
               $('#setup_form').submit();
            }
         }
      },2000);
   } else {
      $('#modal1').text("${l['mess_uninst']}");
      $('#modal1').text($('#modal1').text().replace('#',pack));
      $('#modalCheck').prop('checked', true);

      $.ajax({
         type: 'GET',
         data: { 
             ajaxtool: 'Uninstall',
             file: pack,
         },
         url: 'extras_ajaxtools.php',
         async: true,
         cache: false,
         success: function(data) {
            $('#progressbar').prop('max',100);
            $('#progressbar').val(100);
            $('#modal1').text("${l['mess_uninst_ok']}");
            $('#modal1').text($('#modal1').text().replace('#',pack));
            out=0;
            Boucle=setInterval(function() {
               if (out++ > 4) {
                  clearInterval(Boucle);
                  $('#setup_form').submit();
              }
            },500);
         }
      });
   }
}

$(function(){
   $('#mpacks li').click(function() {
      $('#mpacks li').removeClass('selected');
      $(this).addClass('selected');
      $('.infopack .mpack').css('display','none');
      $('.infopack .mpack[id="m' + $(this).children("img")[0].id + '"]').css('display','inline-block');
   });
});

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

<input type="checkbox" id="modalCheck" />
<div class="modalLayer">
   <div class="modalPopup">
      <p id="modal1"></p>
      <progress id="progressbar"></progress> 
      <p id="modal2"><?="${l['modal2']}";?></p>
   </div>
</div>

<?php
  displayPATH(array(
                array('link'=>'/extras/index.php'.$nLUS,
                      'desc'=>$l['package']),
                array('link'=>$_SERVER['PHP_SELF'].$nLUS,
                      'desc'=>$lang['extras']['manager']),
              ));
?>
<div id="manage">
   <input type='button' class='formbtn' value="<?="${l['check']}"?>" onClick='CheckPacks();'><br>
   <div id='mpacks'>
<?php
   foreach ($lang['extras']['packs'] as $key=>$packs) {
      $wnew='none';
      $wstate='inline';
      if (file_exists("packs/$key.new")) {
         $instnew="images/icon_new.gif";
         $tipnew=str_replace(".|",".\n","${l['tipnew']}");
         $wnew='inline';
         $wstate='none';
      }
      if (file_exists("${packs['lock']}")) {
         $inst="images/icon_nok.gif";
         $tip=str_replace(".|",".\n","${packs['tiplock']}");
         $lock='disabled';
      } else {
         if (file_exists("packs/$key/_version")) {
            $inst="images/icon_ok.gif";
            $tip=str_replace(".|",".\n","${l['tipok']}");
         } else {
            $inst="images/icon_none.gif";
            $tip=str_replace(".|",".\n","${l['tipnone']}");
         }
      }
      echo "   <li id='$key'  style=\"cursor: pointer;\" onClick=DelNew('$key');><!--
               --><img id='$key' src=\"images/${packs['icon']}\" />${packs['package']}<!--
               --><img title=\"$tip\" id=\"State$key\" src=\"$inst\" class='imgstate' style='display:${wstate};'/><!--
               --><img title=\"$tipnew\" id=\"New$key\" src=\"$instnew\" class='imgstate' style='display:${wnew};'/></li>\n";
   }
?>
   </div>
</div>
<div class='infopack'>
<?php
   foreach ($lang['extras']['packs'] as $key=>$packs) {
      $lock=(file_exists($packs['lock'])?'disabled':'');
      $vers=@file_get_contents("packs/$key/_version");
      $icon='images/'.(file_exists('images/'.$packs['view'])?$packs['view']:'icon_construction.gif');
      echo "      <div id='m$key' class='mpack'>\n";
      echo "         <div>\n";
      echo "           <div class='dpack'>\n";
      echo "              <p style='font-size:20px; font-family:tahoma;'>".$packs['package']."<br></p>\n";
      echo "              <p>".($vers?$vers:"--")."<br></p>\n";
      echo "              <select id='Version$key' title='Available versions'>\n";
      foreach (explode(',', $packs['packs']) as $pack) {
         echo "                 <option value=\"${key}_".str_replace('.','_',$pack)."\">".str_replace('#',$pack,"${l['inst']}")."</option>\n";
      }
      echo "                 <option value=\"Uninstall_$key\">${l['uninst']}</option>\n";
      echo "              </select><br>\n";
      echo "              <br>\n";
      echo "              <input type='button' class='formbtn' id='Apply$key' value='${l['apply']}' onClick='Apply(\"$key\");' $lock><br>\n";
      echo "              <input type='button' class='formbtn' id='Log$key' value='${l['viewlog']}' onClick='ReadLog(\"$key\");'><br>\n";
      echo "              <input type='button' class='formbtn' id='Clear$key' value='${l['clearlog']}' onClick='ClearLog(\"$key\");' style='display:none' ><br>\n";
      echo "           </div>\n";
      echo "           <div class='imgpack'><img src='$icon' /></div>\n";
      echo "         </div>\n";
      echo "         <textarea id='Desc$key' class='areapack' readonly>\n";
      echo ($packs['desc']?$packs['desc']:$lang['extras']['nodesc'])."\n";
      echo "         </textarea>\n";
      echo "         <textarea id='Log$key' class='areapack' readonly style='display:none'>\n";
      echo "         </textarea>\n";
      echo "      </div>\n";
   }
?>
</div>
</form>
<?php include('NEW_fend.inc'); ?>
