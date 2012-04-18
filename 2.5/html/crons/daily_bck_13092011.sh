#!/bin/bash
stime=$(date +%c)
echo $stime "Daily Cron started ..." >> /var/www/lighthouse-uxd/lighthouse/html/crons/daily.log
echo "########################" >> /var/www/lighthouse-uxd/lighthouse/html/crons/daily.log
/usr/bin/php /var/www/lighthouse-uxd/lighthouse/html/crons/cron.updatewo.php
if [ $? != 0 ]; then
/bin/mail -s "Daily Cron Failed - cron.updatewo.php" dps-tools-support@nbcuni.com
else
echo "cron.updatewo.php exit status good" >> /var/www/lighthouse-uxd/lighthouse/html/crons/daily.log
fi
etime=$(date +%c)
echo $etime "Daily Cron Completed ..." >> /var/www/lighthouse-uxd/lighthouse/html/crons/daily.log
echo "###########################" >> /var/www/lighthouse-uxd/lighthouse/html/crons/daily.log
echo "" >> /var/www/lighthouse-uxd/lighthouse/html/crons/daily.log
