#!/bin/sh
echo $(date +"%d/%m/%y %H:%M:%S") "Uninstalling package twonky7 1.0 ..."
sleep 3
echo $(date +"%d/%m/%y %H:%M:%S") "Stopping and removing Twonky7 service ..."
/etc/init.d/S97twonkyserver stop
kill `pidof twonkystarter`
rm -f /etc/init.d/S97twonkyserver

echo $(date +"%d/%m/%y %H:%M:%S") "Removing Twonky7 and restoring twonky5 ..."
rm -rf /twonky
mv /twonky5 /twonky

echo $(date +"%d/%m/%y %H:%M:%S") "Removing Twonky7 database and configuration ..."
rm -f  /CacheVolume/twonkyserver.ini
rm -rf /CacheVolume/music
rm -rf /CacheVolume/pictures
rm -rf /CacheVolume/videos
rm -rf /CacheVolume/TwonkyServer
rm -rf /proto/SxM_webui/extras/packs/twonky

echo $(date +"%d/%m/%y %H:%M:%S") "Starting Twonky 5 ..."
mv /etc/init.d/S97twonkyserver.5 /etc/init.d/S97twonkyserver
chmod a+x /etc/init.d/S97twonkyserver
/etc/init.d/S97twonkyserver start

echo $(date +"%d/%m/%y %H:%M:%S") "Remove the extras twonky7 Folder..."
rm -rf /proto/SxM_webui/extras/packs/twonky7

echo $(date +"%d/%m/%y %H:%M:%S") "Uninstall package twonky7 1.0 complete"
rm -f $0
exit 0
