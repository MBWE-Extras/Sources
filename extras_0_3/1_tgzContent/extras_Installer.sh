#!/bin/sh

ME_NAME="${0##*/}"                  						# Strip longest match of */ from start
ME_DIR=`echo $((${#0}-${#ME_NAME})) $0 | awk '{print substr($2, 1, $1)}' `  	# Substring from 0 thru pos of filename
ME_BASE="${ME_NAME%.[^.]*}"            						# Strip shortest match of . plus at least one non-dot char from end
ME_EXT=`echo ${#ME_BASE} $ME_NAME | awk '{print substr($2, $1 + 1)}' `	# Substring from len of base thru end

PACK_NAME="${ME_NAME%_[^_]*}"

CURDIR=$(pwd)
FW_STATUS_FILE=/var/upgrade_download/fwud_status

Follow() {
   if [ -n "$1" ]; then
      echo ${1}%${2} > $FW_STATUS_FILE
      sleep 3
   fi
   echo -e $(date +"%d/%m/%y %H:%M:%S") $2
}

Follow "10" "It starts..."

Follow "20" "Check type of My Book World Edition"
if [ !  -d /proto/SxM_webui ];then
   Follow "" "ERROR: This installer is only for Mybook WhiteLight device, please use the $PACK_NAME installer for your device."
   exit 1
fi

Follow "30" "Check OPTWARE installed"
if [ ! -f /opt/bin/ipkg ] ;  then
   Follow "" "ERROR: OPTWARE not installed. Please re-install EXTRAS package first."
   exit 2
fi

Follow "40" "Saving original files"
cd /proto/SxM_webui/admin
[ ! -e  inc/wixHTML.class.ext ] && cp -pf inc/wixHTML.class inc/wixHTML.class.ext
[ ! -e  inc/wixLang.class.ext ] && cp -pf inc/wixLang.class inc/wixLang.class.ext
[ ! -e  bt_guiconfig.inc.ext ] && cp -pf bt_guiconfig.inc bt_guiconfig.inc.ext
[ ! -e  guiconfig.inc.ext ] && cp -pf guiconfig.inc guiconfig.inc.ext
[ ! -e  NEW_fend.inc.ext ] && cp -pf NEW_fend.inc NEW_fend.inc.ext
[ ! -e  NEW_login_fbegin.inc.ext ] && cp -pf NEW_login_fbegin.inc NEW_login_fbegin.inc.ext
[ ! -e  redirect.inc.ext ] && cp -pf redirect.inc redirect.inc.ext
[ ! -e  system_index.php.ext ] && cp -pf system_index.php system_index.php.ext
[ ! -e  /etc/php.ini.ext ] && cp -pf /etc/php.ini /etc/php.ini.ext
cd $CURDIR

Follow "50" "Installing new system files"
cp -rpf /var/upgrade/Pack/admin/* /proto/SxM_webui/admin
cp -pf /var/upgrade/Pack/php.ini /etc/php.ini
rm -rf /var/upgrade/Pack/admin/*
rmdir  /var/upgrade/Pack/admin/

Follow "60" "Create folder \"$PACK_NAME\""
if [ ! -d /proto/SxM_webui/${PACK_NAME} ] ;  then
   mkdir /proto/SxM_webui/${PACK_NAME}
fi

Follow "70" "Copy useful \"$PACK_NAME\" files"
cp -rpf /var/upgrade/Pack/extras/* /proto/SxM_webui/${PACK_NAME}
rm -f /proto/SxM_webui/extras/js/prototype.js
rm -rf /var/upgrade/Pack/extras/*
rmdir  /var/upgrade/Pack/extras/

Follow "80" "Create folder package \"$PACK_NAME\""
if [ ! -d /proto/SxM_webui/extras/packs/${PACK_NAME} ] ;  then
   mkdir /proto/SxM_webui/extras/packs/${PACK_NAME}
fi

Follow "90" "Copy useful package \"$PACK_NAME\" files"
cp -rpf /var/upgrade/Pack/* /proto/SxM_webui/extras/packs/${PACK_NAME}

Follow "95" "Add check updates in crontab"
kill $(pidof cron)
sed -i "/extras_checkpacks.php/d" /opt/var/cron/crontabs/root
echo "00 04 * * * /proto/SxM_webui/extras/extras_checkpacks.php >> /var/log/cron.log  2>&1" >> /opt/var/cron/crontabs/root
/opt/sbin/cron

Follow "100" "\"$PACK_NAME\" Installation Complete"
