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
if [ ! -d /proto/SxM_webui ];then
   Follow "" "ERROR: This installer is only for Mybook WhiteLight device, please use the $PACK_NAME installer for your device."
   exit 1
fi

Follow "30" "Installing OPTWARE..."
if [ ! -f /opt/bin/ipkg ] ;  then
   Follow "34" "Installing the OPTWARE feed ..."
   feed=http://ipkg.nslu2-linux.org/feeds/optware/cs05q1armel/cross/unstable
   ipk_name=$(wget -qO- $feed/Packages | awk '/^Filename: ipkg-opt/ {print $2}')
   wget $feed/$ipk_name
   tar -xOvzf $ipk_name ./data.tar.gz | tar -C / -xzvf -
   mkdir -p /opt/etc/ipkg
   echo "src armel http://ipkg.nslu2-linux.org/feeds/optware/cs05q1armel/cross/unstable" > /opt/etc/ipkg/armel-feed.conf
   /opt/bin/ipkg update

   export PATH=$PATH:/opt/bin

   Follow "38" "configuring correct OPTWARE Environment ..."
   [ ! -e  /root/.bashrc ] && Follow "WARNING : /root/.bashrc does not exist"
   [ "`grep -c "/opt/bin" /root/.bashrc`" -eq "0" ] && echo -en "\n export PATH=\$PATH:/opt/bin:/opt/sbin" >>/root/.bashrc
   [ ! -e  /root/.bash_profile ] && Follow "WARNING : /root/.bash_profile does not exist"
   [ "`grep -c "/opt/bin" /root/.bash_profile`" -eq "0" ] && echo -en "\n export PATH=\$PATH:/opt/bin:/opt/sbin" >>/root/.bash_profile

   if [ ! -e  /etc/profile ];then
      touch /etc/profile
   fi
   if [ "`cat /etc/profile|grep -c /opt/bin `" -eq "0" ];then
      echo -en "\n PATH=\$PATH:/opt/bin:/opt/sbin" >>/etc/profile
      echo -en "\n export PATH " >>/etc/profile
   fi
fi

Follow "40" "configuring OPTWARE startup scripts ..."
if  [ ! -f /etc/init.d/S90optware ] ;  then
    echo "if [ -d /opt/etc/init.d ]; then" >/etc/init.d/S99toptware
    echo "   for f in /opt/etc/init.d/S* ; do" >>/etc/init.d/S99toptware
    echo "   [ -x \$f ] && \$f start" >>/etc/init.d/S99toptware
    echo "   done" >>/etc/init.d/S99toptware
    echo "fi" >>/etc/init.d/S99toptware
    chmod +x /etc/init.d/S99toptware
fi

Follow "45" "Installing sort tools ..."
if [ ! -e /usr/bin/sort ] ;then
   mv -f Pack/fpkmgr/sort /usr/bin/sort
   chmod +x /usr/bin/sort
fi

Follow "50" "Installing dirname tools ..."
if [ ! -e /usr/bin/dirname ] ;then
   mv -f Pack/fpkmgr/dirname /usr/bin/dirname
   chmod +x /usr/bin/dirname
fi

Follow "55" "Installing perl..."
if [ ! -e /opt/bin/perl ] ;then
   /opt/bin/ipkg update
   /opt/bin/ipkg install perl
fi

Follow "60" "Installing FeaturePack Manager..."

Follow "70" "Creating folders..."
if [ ! -d /proto/SxM_webui/fpkmgr ] ;then
   mkdir /proto/SxM_webui/fpkmgr
fi
if [ ! -d /proto/SxM_webui/fpkmgr/fpks ] ;then
   mkdir /proto/SxM_webui/fpkmgr/fpks
fi
if [ ! -d /proto/SxM_webui/fpkmgr/temp ] ;then
   mkdir /proto/SxM_webui/fpkmgr/temp
fi

Follow "75" "Installing index.php file..."
mv -f Pack/fpkmgr/index.php /proto/SxM_webui/fpkmgr/index.php
chmod +x /proto/SxM_webui/fpkmgr/index.php

Follow "80" "Deflating HTML package files..."
mv -f Pack/fpkmgr/HTML.tar /proto/SxM_webui/fpkmgr/HTML.tar
cd /proto/SxM_webui/fpkmgr
tar -xf /proto/SxM_webui/fpkmgr/HTML.tar  
rm "/proto/SxM_webui/fpkmgr/HTML.tar"
cd $CURDIR

Follow "85" "Deflating package files..."
mv -f Pack/fpkmgr/System_Configuration.tar /proto/SxM_webui/fpkmgr/fpks/System_Configuration.tar
cd /proto/SxM_webui/fpkmgr/fpks
tar -xf /proto/SxM_webui/fpkmgr/fpks/System_Configuration.tar
rm "/proto/SxM_webui/fpkmgr/fpks/System_Configuration.tar"
cd $CURDIR

Follow "90" "Installing final configuration..."
sh /proto/SxM_webui/fpkmgr/fpks/System_Configuration/_install

Follow "92" "Updating the original WD index.php file..."
if [ ! -e /proto/SxM_webui/index.php.ori ] ;then
   /opt/bin/perl Pack/fpkmgr/updatessm.pl
   rm -f Pack/fpkmgr/updatessm.pl
fi

Follow "94" "Create folder package \"$PACK_NAME\""
if [ ! -d /proto/SxM_webui/extras/packs/${PACK_NAME} ] ;  then
   mkdir /proto/SxM_webui/extras/packs/${PACK_NAME}
fi

Follow "96" "Copy useful package \"$PACK_NAME\" files"
rm -rf /var/upgrade/Pack/fpkmgr/*
rmdir  /var/upgrade/Pack/fpkmgr/
mv -f /var/upgrade/Pack/* /proto/SxM_webui/extras/packs/${PACK_NAME}

rm -f "./FeaturePackInstaller.sh"
Follow "100" "\"$PACK_NAME\" Installation Complete"
