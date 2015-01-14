#!/bin/sh
echo $(date +"%d/%m/%y %H:%M:%S") "Uninstalling package noip 1.0 ..."
sleep 3

test=` cat /opt/var/cron/crontabs/root |grep -c noipupdate.sh`
if [ ! "$test" = "0" ];then
   echo $(date +"%d/%m/%y %H:%M:%S") "Remove the noip cron..."
   kill `pidof cron`
   sed -i "/noip\/noipupdate.sh/d" /opt/var/cron/crontabs/root
   /opt/sbin/cron
   sleep 3
fi

echo $(date +"%d/%m/%y %H:%M:%S") "Remove the noip log file..."
if [ -e "/var/log/extras_noip.log" ];then
   rm "/var/log/extras_noip.log"
fi
sleep 3

echo $(date +"%d/%m/%y %H:%M:%S") "Remove the extras noip Folder..."
rm -R "/proto/SxM_webui/extras/packs/noip"
sleep 3

echo $(date +"%d/%m/%y %H:%M:%S") "Uninstall package noip 1.0 complete"
