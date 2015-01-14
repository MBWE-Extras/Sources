#!/bin/sh

sleep 5
CURDIR=$(pwd)
PACK_NAME="extras 0.2"

Follow() {
   if [ -n "$1" ]; then
      echo ${1}%${2} > $FW_STATUS_FILE
   fi
   echo -e $(date +"%d/%m/%y %H:%M:%S") $2
   sleep 3
}

Follow "" "Uninstalling package \"$PACK_NAME\""

Follow "" "Removing check updates in crontab"
kill $(pidof cron)
sed -i "/extras_checkpacks.php/d" /opt/var/cron/crontabs/root
/opt/sbin/cron

Follow "" "Restoring original system files"
cd /proto/SxM_webui/admin
[ -e  inc/wixHTML.class.ext ] && mv -f inc/wixHTML.class.ext inc/wixHTML.class
[ -e  inc/wixLang.class.ext ] && mv -f inc/wixLang.class.ext inc/wixLang.class
[ -e  bt_guiconfig.inc.ext ] && mv -f bt_guiconfig.inc.ext bt_guiconfig.inc
[ -e  guiconfig.inc.ext ] && mv -f guiconfig.inc.ext guiconfig.inc
[ -e  NEW_fend.inc.ext ] && mv -f NEW_fend.inc.ext NEW_fend.inc
[ -e  NEW_login_fbegin.inc.ext ] && mv -f NEW_login_fbegin.inc.ext NEW_login_fbegin.inc
[ -e  redirect.inc.ext ] && mv -f redirect.inc.ext redirect.inc
[ -e  system_index.php.ext ] && mv -f system_index.php.ext system_index.php
[ -e  /etc/php.ini.ext ] && mv -f /etc/php.ini.ext /etc/php.ini
cd $CURDIR

Follow "" "Deleting package"
rm -rf /proto/SxM_webui/extras/

Follow "" "Uninstall package \"$PACK_NAME\" complete"
rm -f $0
exit 0
