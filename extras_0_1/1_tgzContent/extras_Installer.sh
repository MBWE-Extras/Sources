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
   fi
   echo -e $(date +"%d/%m/%y %H:%M:%S") $2
   sleep 3
}

Follow "10" "It starts..."

Follow "20" "Check type of My Book World Edition"
if [ !  -d /proto/SxM_webui ];then
   Follow "" "ERROR: This installer is only for Mybook WhiteLight device, please use the $PACK_NAME installer for your device."
   exit 1
fi

Follow "30" "Check OPTWARE installed"
if [ ! -f /opt/bin/ipkg ] ;  then
   Follow "35" "Installing OPTWARE..."

   Follow "40" "Installing the OPTWARE feed ..."
   feed=http://ipkg.nslu2-linux.org/feeds/optware/cs05q1armel/cross/unstable
   ipk_name=$(wget -qO- $feed/Packages | awk '/^Filename: ipkg-opt/ {print $2}')
   wget $feed/$ipk_name
   tar -xOvzf $ipk_name ./data.tar.gz | tar -C / -xzvf -
   mkdir -p /opt/etc/ipkg
   echo "src armel http://ipkg.nslu2-linux.org/feeds/optware/cs05q1armel/cross/unstable" > /opt/etc/ipkg/armel-feed.conf
   /opt/bin/ipkg update

   export PATH=$PATH:/opt/bin

   Follow "45" "Configuring correct OPTWARE Environment ..."
   [ ! -e  /root/.bashrc ] && Follow "" "WARNING : /root/.bashrc does not exist"
   [ "`grep -c "/opt/bin" /root/.bashrc`" -eq "0" ] && echo -en "\n export PATH=\$PATH:/opt/bin:/opt/sbin" >>/root/.bashrc

   [ ! -e  /root/.bash_profile ] && Follow "" "WARNING : /root/.bash_profile does not exist"
   [ "`grep -c "/opt/bin" /root/.bash_profile`" -eq "0" ] && echo -en "\n export PATH=\$PATH:/opt/bin:/opt/sbin" >>/root/.bash_profile

   if [ ! -e  /etc/profile ];then
      touch /etc/profile
   fi
   if [ "`cat /etc/profile|grep -c /opt/bin `" -eq "0" ];then
      echo -en "\n PATH=\$PATH:/opt/bin:/opt/sbin" >>/etc/profile
      echo -en "\n export PATH " >>/etc/profile
   fi
fi

Follow "50" "Create folder package \"$PACK_NAME\""
if [ ! -d /proto/SxM_webui/extras/packs/${PACK_NAME} ] ;  then
   mkdir /proto/SxM_webui/extras/packs/${PACK_NAME}
fi

Follow "60" "Copy useful package \"$PACK_NAME\" files"
cp -p /var/upgrade/Pack/* /proto/SxM_webui/extras/packs/${PACK_NAME}

Follow "70" "Replacing system files and saving originals"

Follow "80" "Modifying original files"

cd $CURDIR

Follow "100" "\"$PACK_NAME\" Installation Complete"
