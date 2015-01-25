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

Follow "30" "Check EXTRAS installed"
if [ ! -d /proto/SxM_webui/extras/packs ] ;  then
   Follow "" "ERROR: EXTRAS not installed. Please install EXTRAS package first before its sub-packages."
   exit 3
fi

Follow "40" "Stopping old Twonky"
if [ -e /etc/init.d/S97twonkyserver ] ;  then
   /etc/init.d/S97twonkyserver stop
   if [ ! -e /etc/init.d/S97twonkyserver.5 ] ;  then
      mv /etc/init.d/S97twonkyserver /etc/init.d/S97twonkyserver.5
      chmod a-x /etc/init.d/S97twonkyserver.5
   fi
fi
if [ ! -d /twonky5 ] ;  then
   mv /twonky /twonky5
fi

Follow "50" "Installing new Twonky"
if [ ! -d /twonky ] ;  then
   mkdir /twonky
fi
cp -rpf /var/upgrade/Pack/twonky/* /twonky
rm -rf  /var/upgrade/Pack/twonky/*
rmdir   /var/upgrade/Pack/twonky/

Follow "60" "Creating database Twonky"
if [ ! -d /CacheVolume/TwonkyServer ] ;  then
   mkdir /CacheVolume/TwonkyServer
fi
cp -pf /var/upgrade/Pack/twonkyserver.ini /CacheVolume/TwonkyServer/twonkyserver.ini
rm -f  /var/upgrade/Pack/twonkyserver.ini

Follow "70" "Create folder package \"$PACK_NAME\""
if [ ! -d /proto/SxM_webui/extras/packs/${PACK_NAME} ] ;  then
   mkdir /proto/SxM_webui/extras/packs/${PACK_NAME}
fi

Follow "80" "Copy useful package \"$PACK_NAME\" files"
cp -pf /var/upgrade/Pack/* /proto/SxM_webui/extras/packs/${PACK_NAME}

Follow "90" "Starting new Twonky"
if [ -e /twonky/twonky.sh ] ;  then
   cp -f /twonky/twonky.sh /etc/init.d/S97twonkyserver
   chmod +x /etc/init.d/S97twonkyserver
   /etc/init.d/S97twonkyserver start
else
   Follow "" "ERROR: Impossible to start new twonky."
   exit 90
fi

Follow "100" "\"$PACK_NAME\" Installation Complete"
