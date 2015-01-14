#!/bin/sh
echo $(date +"%d/%m/%y %H:%M:%S") "Uninstalling package fpkmgr 1.0 ..."
sleep 3
mv  -f /proto/SxM_webui/index.php.ori /proto/SxM_webui/index.php
rm -rf /proto/SxM_webui/fpkmgr
rm -rf /proto/SxM_webui/extras/packs/fpkmgr
echo $(date +"%d/%m/%y %H:%M:%S") "Uninstall package fpkmgr 1.0 complete"
exit 0
