#!/bin/sh
echo $(date +"%d/%m/%y %H:%M:%S") "Uninstalling package extplorer 1.0 ..."
sleep 3
rm -rf /proto/SxM_webui/extras/packs/extplorer
echo $(date +"%d/%m/%y %H:%M:%S") "Uninstall package extplorer 1.0 complete"
exit 0
