#!/bin/sh
echo $(date +"%d/%m/%y %H:%M:%S") "Uninstalling package dummy 1.0 ..."
sleep 3
rm -rf /proto/SxM_webui/extras/packs/dummy
echo $(date +"%d/%m/%y %H:%M:%S") "Uninstall package dummy 1.0 complete"
exit 0
