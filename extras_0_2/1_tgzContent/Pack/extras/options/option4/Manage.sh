# Retrieving Form Variables... use as $GUI_varname
folder="OpenVpn"
Scriptname="Manage"

cd /proto/SxM_webui/fpkmgr/fpks/$folder

if [ -e /proto/SxM_webui/fpkmgr/fpks/$folder/$Scriptname.vars ] ;then
   . /proto/SxM_webui/fpkmgr/fpks/$folder/$Scriptname.vars
fi

if [ -e /proto/SxM_webui/fpkmgr/fpks/OpenVpn/InstallPending.flag ];then
   echo "OpenVpnInstallation is not finished,"
   echo "please Wait..."
   echo "<br><br><br><br>"
   cat  /proto/SxM_webui/fpkmgr/fpks/OpenVpn/InstallPending.flag

   # Redirect ...
   echo -n "<html><head>"
   echo -n "<meta http-equiv=\"refresh\" content=\"30;URL=/fpkmgr/index.php?SourcePage=Mybook_Mgr&ACTION=ExecScript&ScriptFolder=$folder&ScriptName=$Scriptname\">"
   echo -n "</head><body></html>"
   exit 1
fi

if ( [ "$1" = "OPENVPNCONFIGURE" ] ) ;  then
   echo " Configuring Open Vpn Server..."
   NET_VPN_IP1=$GUI_NET_VPN_IP1
   NET_VPN_IP2=$GUI_NET_VPN_IP2
   NET_VPN_IP3=$GUI_NET_VPN_IP3
   NET_VPN_IP="$NET_VPN_IP1.$NET_VPN_IP2.$NET_VPN_IP3.0"

   LAN_IP1=`/sbin/ifconfig|grep Bcast|grep "inet addr:"|cut -f3 -d ":"|cut -f1 -d "."`
   LAN_IP2=`/sbin/ifconfig|grep Bcast|grep "inet addr:"|cut -f3 -d ":"|cut -f2 -d "."`
   LAN_IP3=`/sbin/ifconfig|grep Bcast|grep "inet addr:"|cut -f3 -d ":"|cut -f3 -d "."`
   LAN_IP="$LAN_IP1.$LAN_IP2.$LAN_IP3.0"
   LAN_MASK=`/sbin/ifconfig|grep Bcast|grep "inet addr:"|cut -f4 -d ":"`

   if [ "$NET_VPN_IP1.$NET_VPN_IP2.$NET_VPN_IP3" = "$LAN_IP1.$LAN_IP2.$LAN_IP3" ];then
      echo "Lan Network Must not be equal to VPN network : operation cancelled."
   else
      if [ -e  /opt/etc/tinyproxy/tinyproxy.conf ];then
         cp /proto/SxM_webui/fpkmgr/fpks/OpenVpn/tinyproxy.cnf /opt/etc/tinyproxy/tinyproxy.conf
         cp /opt/etc/tinyproxy/tinyproxy.conf /opt/etc/tinyproxy/tinyproxy.conf.tmp
         sed -e "/^Allow /cAllow $NET_VPN_IP1.$NET_VPN_IP2.$NET_VPN_IP3.0/24" /opt/etc/tinyproxy/tinyproxy.conf.tmp >/opt/etc/tinyproxy/tinyproxy.conf
         rm /opt/etc/tinyproxy/tinyproxy.conf.tmp
      fi
      cp /opt/etc/openvpn/openvpn.conf  /opt/etc/openvpn/openvpn.conf.tmp
      sed -e "/^server /c\server $NET_VPN_IP 255.255.255.0" /opt/etc/openvpn/openvpn.conf.tmp>/opt/etc/openvpn/openvpn.conf
                                    
      cp /opt/etc/openvpn/openvpn.conf  /opt/etc/openvpn/openvpn.conf.tmp
      sed -e "/^push /c\push \"route $LAN_IP $LAN_MASK\"" /opt/etc/openvpn/openvpn.conf.tmp>/opt/etc/openvpn/openvpn.conf
    
      cp /opt/etc/openvpn/openvpn.conf  /opt/etc/openvpn/openvpn.conf.tmp
      sed -e "/^port /c\port $GUI_VPN_PORT" /opt/etc/openvpn/openvpn.conf.tmp>/opt/etc/openvpn/openvpn.conf

      # Update client configuration : 
      ExternalIP=`cat /proto/SxM_webui/fpkmgr/fpks/$folder/ExternalIp.conf|cut -f2 -d "="` 
      GUI_CERTNAME=OVClient
      cp /shares/Public/WWW/OpenVpn/ClientKeys/$GUI_CERTNAME/$GUI_CERTNAME.ovpn /shares/Public/WWW/OpenVpn/ClientKeys/$GUI_CERTNAME/$GUI_CERTNAME.ovpn.tmp
      sed -e "/^remote /c\remote $ExternalIP $GUI_VPN_PORT" /shares/Public/WWW/OpenVpn/ClientKeys/$GUI_CERTNAME/$GUI_CERTNAME.ovpn.tmp>/shares/Public/WWW/OpenVpn/ClientKeys/$GUI_CERTNAME/$GUI_CERTNAME.ovpn
   fi
fi

if ( [ "$1" = "Autodetect" ] ) ;  then
   echo "Detecting External Ip Address..."
   wget http://highlevelbits.free.fr/download-MBWEW/FPInstaller/Ipguess.php -O /proto/SxM_webui/fpkmgr/fpks/OpenVpn/ExternalIp.conf >/dev/nul 2>&1

   ExternalIP=`cat /proto/SxM_webui/fpkmgr/fpks/OpenVpn/ExternalIp.conf|cut -f2 -d "="`
   VPN_PORT=`cat /opt/etc/openvpn/openvpn.conf |grep "^port "|cut -f2 -d " "`
   GUI_CERTNAME=OVClient

   cp /shares/Public/WWW/OpenVpn/ClientKeys/$GUI_CERTNAME/$GUI_CERTNAME.ovpn /shares/Public/WWW/OpenVpn/ClientKeys/$GUI_CERTNAME/$GUI_CERTNAME.ovpn.tmp
   sed -e "/^remote /c\remote $ExternalIP $VPN_PORT" /shares/Public/WWW/OpenVpn/ClientKeys/$GUI_CERTNAME/$GUI_CERTNAME.ovpn.tmp>/shares/Public/WWW/OpenVpn/ClientKeys/$GUI_CERTNAME/$GUI_CERTNAME.ovpn
   echo " Changes will be taken into account after stop/start openvpn server..."
fi

if ( [ "$1" = "Modify" ] ) ;  then
   VPN_PORT=`cat /opt/etc/openvpn/openvpn.conf |grep "^port "|cut -f2 -d " "`
 
   GUI_CERTNAME=OVClient
   cp /shares/Public/WWW/OpenVpn/ClientKeys/$GUI_CERTNAME/$GUI_CERTNAME.ovpn /shares/Public/WWW/OpenVpn/ClientKeys/$GUI_CERTNAME/$GUI_CERTNAME.ovpn.tmp
   sed -e "/^remote /c\remote $GUI_externalIP $VPN_PORT" /shares/Public/WWW/OpenVpn/ClientKeys/$GUI_CERTNAME/$GUI_CERTNAME.ovpn.tmp>/shares/Public/WWW/OpenVpn/ClientKeys/$GUI_CERTNAME/$GUI_CERTNAME.ovpn
   echo ExternalIP=$GUI_externalIP> /proto/SxM_webui/fpkmgr/fpks/OpenVpn/ExternalIp.conf    
   echo " Changes will be taken into account after stop/start openvpn server..."
fi

if [ "$1" = "START" ];then
   echo "starting..."
   sh /proto/SxM_webui/fpkmgr/fpks/$folder/S20openvpn start
fi
  
if [ "$1" = "STOP" ];then
   echo "Stopping..."
   if [ -n "`pidof openvpn`" ]; then
      killall openvpn 2>/dev/null
   fi
   #  sh /proto/SxM_webui/fpkmgr/fpks/$folder/S20openvpn stop
fi

if [ "$1" = "CONFIGSTARTUP" ];then
   if [ "$GUI_StartupMode" = "AutoStart" ];then
      if [ ! -e  /opt/etc/init.d/S20Openvpn ];then
         cp /proto/SxM_webui/fpkmgr/fpks/$folder/S20openvpn /opt/etc/init.d/S20openvpn
         chmod +x /opt/etc/init.d/S20openvpn
         ln -s /opt/etc/init.d/S20openvpn /opt/etc/init.d/K90openvpn
      fi
   else
      if [ -e  /opt/etc/init.d/S20openvpn ];then
         rm /opt/etc/init.d/S20openvpn
         rm /opt/etc/init.d/K90openvpn
      fi
   fi
fi

echo "<b> OPENVPN </b><br>"
echo " Connect Remotely To your Mybook as if you were at home : <br>"
echo " To connect to your home while you are away, you will need to : <br>"
echo " - Open the VPN port (1394 by default) on your home router/Internet Box<br>"
echo " - Install the <a target=Mynewwindow href=http://openvpn.net/index.php/open-source/downloads.html>OpenVpn Client</a> on your remote Windows Workstation (http://... ) <br>"
echo " - Install the OpenVpn GUI <a target=Mynewwindow href=http://openvpn.se/files/binary/openvpn-gui-1.0.4.exe > ( openvpn-gui-1.0.4 ) </a> on your Windows computer"

VPN_PORT=`cat /opt/etc/openvpn/openvpn.conf |grep "^port "|cut -f2 -d " "`

echo " <br><br><b> Open Vpn Server configuration :</b><br>"

echo -n "<form action=/fpkmgr/index.php method=post>"
echo -n "<input type=hidden name=ACTION value=ExecScript>"
echo -n "<input type=hidden name=ScriptFolder value=$folder>"
echo -n "<input type=hidden name=ScriptName value=$Scriptname>"
              
echo -n "<input type=hidden name=Params value=GuessExternalIP >"

ExternalIP=`cat /proto/SxM_webui/fpkmgr/fpks/$folder/ExternalIp.conf|cut -f2 -d "="`
echo -n "External Ip address / Name : <input type=text name=externalIP size=50 value=$ExternalIP>"

echo -n "<input type=submit name=Params value='Modify'>"
echo -n "<input type=submit name=Params value='Autodetect'>"
echo -n "</form>"

echo -n "<form action=/fpkmgr/index.php method=post>"

echo -n " Server port : <input type=text name=VPN_PORT size=4 maxlength=4 value=$VPN_PORT><br>"

echo "( This port number needs to be opened on your home internet Router/Box.<br> It will be used to establish the connection remotely to your Home.) "

echo -n "<input type=hidden name=ACTION value=ExecScript>"
echo -n "<input type=hidden name=ScriptFolder value=$folder>"
echo -n "<input type=hidden name=ScriptName value=$Scriptname>"

echo -n "<input type=hidden name=Params value=OPENVPNCONFIGURE>"

NET_VPN_IP1=`cat /opt/etc/openvpn/openvpn.conf |grep ^server|cut -f2 -d " "|cut -f1 -d "."`
NET_VPN_IP2=`cat /opt/etc/openvpn/openvpn.conf |grep ^server|cut -f2 -d " "|cut -f2 -d "."`
NET_VPN_IP3=`cat /opt/etc/openvpn/openvpn.conf |grep ^server|cut -f2 -d " "|cut -f3 -d "."`

echo -n "VPN Client virtual IP range : <input type=text name=NET_VPN_IP1 size=3 maxlength=3 value=$NET_VPN_IP1>. "
echo -n "<input type=text name=NET_VPN_IP2 size=3 maxlength=3 value=$NET_VPN_IP2>. "
echo -n "<input type=text name=NET_VPN_IP3 size=3 maxlength=3 value=$NET_VPN_IP3>. "
echo -n "<input type=text name=NET_VPN_IP3 size=3 maxlength=3 value=X disabled> "
echo -n "&nbsp;&nbsp;&nbsp;&nbsp;"
echo -n "<input type=submit value='Modify'></form>"

echo "<br>( Client computer will get an IP address from this range when the VPN Connection  will be established . )"
   
test=`ps -ef|grep openv|grep -c penvpn`
if [ "$test" = "0" ] ;then
   echo "<br> OpenVpn Server is currently Stopped <a href=\"/fpkmgr/index.php?ACTION=ExecScript&ScriptFolder=$folder&ScriptName=$Scriptname&Params=START \">( Start )</a>"
else
   echo "<br> OpenVpn is currently Running <a href=\"/fpkmgr/index.php?ACTION=ExecScript&ScriptFolder=$folder&ScriptName=$Scriptname&Params=STOP\">( stop )</a>"
fi

echo -n "<form action=/fpkmgr/index.php method=post>"
echo -n "Startup mode : "
echo -n "<Select name=StartupMode>"

if [ -e /opt/etc/init.d/S20openvpn ];then
   echo -n "<option  Selected value=AutoStart> Run OpenVpn at Startup </option>"
   echo -n "<option value=NoStart> Do NOT Run OpenVpn at Startup </option>"
else
   echo -n "<option value=NoStart selected> Do NOT Run OpenVpn at Startup </option>"
   echo -n "<option value=AutoStart> Run OpenVpn at Startup </option>"
fi
echo -n "</select>"

echo -n "<input type=hidden name=ACTION value=ExecScript>"
echo -n "<input type=hidden name=ScriptFolder value=$folder>"
echo -n "<input type=hidden name=ScriptName value=$Scriptname>"
echo -n "<input type=hidden name=Params value=CONFIGSTARTUP>"
echo -n "<input type=submit value='Modify'>"
echo -n "</form>"

echo    " <b>Client Configuration : </b>"

ServerIP=`/sbin/ifconfig eth0 |grep 'inet addr' | awk '{print \$2}' |./cut -c 6- `
 
GUI_CERTNAME=OVClient
echo -n " Copy the following OVClient Folder to your <i>\"c:\program files\Openvpn\config\"</i> folder of your nomad computer <br><br>"

echo -n "<a target='_blank' href='/fpkmgr/fpks/OpenVpn/OVClient.php?file=ca.crt'>Download ca.crt</a>   "
echo -n "<a target='_blank' href='/fpkmgr/fpks/OpenVpn/OVClient.php?file=OVClient.crt'>Download OVClient.crt</a>   "
echo -n "<a target='_blank' href='/fpkmgr/fpks/OpenVpn/OVClient.php?file=OVClient.key'>Download OVClient.key</a>   "
echo -n "<a target='_blank' href='/fpkmgr/fpks/OpenVpn/OVClient.php?file=OVClient.ovpn'>Download OVClient.ovpn</a>   "
