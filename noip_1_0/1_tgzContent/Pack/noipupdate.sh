#!/bin/sh
#
# Purpose :
#     This script updates the no-ip alias.
#     The specifications of the noip update protocol can be found at :
#     http://www.noip.com/integrate/request
#     With SFRBox renewing his public IP more than 30 days it is 
#     mandatory to previously update DNS hostname with a fake IP. 
#
# Author     : Patrick BRIOT 
# 
# Last update: 11-10-2013
#

NoIP_Conf=$(dirname $0)/noip_conf.xml            # NoIP file configuration
NoIP_Log=/var/log/extras_noip.log                # The log file

NoIP_Login=$(awk -F'[<>]' '/<login>/{print $3}' $NoIP_Conf)
NoIP_Password=$(awk -F'[<>]' '/<password>/{print $3}' $NoIP_Conf | openssl enc -base64 -d)
NoIP_Hosts=$(awk -F'[<>]' '/<host[0-9]>/{printf $3" "}' $NoIP_Conf)
#NoIP_MyIP=$(/opt/bin/curl -s ifconfig.me 2>&1)

for NoIP_Host in $NoIP_Hosts
do
	if [ ! -z "$NoIP_Host" ]; then
		# Call NoIP Update system faking to force updating
		result=$(/opt/bin/curl -s -u $NoIP_Login:$NoIP_Password "https://dynupdate.no-ip.com/nic/update?hostname=$NoIP_Host&myip=1.2.3.4" 2>&1) 
		# Call NoIP Update system
		result=$(/opt/bin/curl -s -u $NoIP_Login:$NoIP_Password "http://dynupdate.no-ip.com/nic/update?hostname=$NoIP_Host" 2>&1)

		case $result in
		     "good"*)       verbose="$result -> DNS hostname update successful" ;;
		     "nochg"*)      verbose="$result -> IP address is current, no update performed" ;;
		     "nohost")      verbose="$result -> Hostname does not exist under specified account" ;;
		     "badauth")     verbose="$result -> Invalid username password combination" ;;
		     "badagent")    verbose="$result -> Client disabled" ;;
		     "!donator")    verbose="$result -> Update request was sent including a feature that is not available" ;;
		     "abuse")       verbose="$result -> Username is blocked due to abuse" ;;
		     "911")         verbose="$result -> Retry the update no sooner 30 minutes" ;;
		     *)             verbose="$result -> Unlisted error" ;;
		esac

		# Log the result
		echo "$(date +"%d/%m/%y %H:%M:%S") DNS $NoIP_Host : $verbose" >> $NoIP_Log
	fi
done
tail -n 100 $NoIP_Log > $NoIpLog.tmp
mv -f $NoIPLog.tmp $NoIP_Log
