#!/bin/bash
stime=$(date +%c)

echo $stime "15 Min Cron started ..." >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/fifteen_minutes.log
echo "########################" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/fifteen_minutes.log

php /var/www/lighthouse-uxd/lighthouse/current/html/crons/cron.activate_wo.php
if [ $? != 0 ]; then
/bin/mail -s "15 Min Cron Failed - cron.activate_wo.php" ots-tools-support@nbcuni.com
else
echo "cron.activate_wo.php exit status good" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/fifteen_minutes.log
fi

php /var/www/lighthouse-uxd/lighthouse/current/html/crons/cron.companies.php
if [ $? != 0 ]; then
/bin/mail -s "15 Min Cron Failed - cron.companies.php" ots-tools-support@nbcuni.com
else
echo "cron.companies.php exit status good" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/fifteen_minutes.log
fi

php /var/www/lighthouse-uxd/lighthouse/lighthouse/html/crons/cron.users.php
if [ $? != 0 ]; then
/bin/mail -s "15 Min Cron Failed - cron.users.php" ots-tools-support@nbcuni.com
else
echo "cron.users.php exit status good" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/fifteen_minutes.log
fi

php /var/www/lighthouse-uxd/lighthouse/current/html/crons/cron.projects.php
if [ $? != 0 ]; then
/bin/mail -s "15 Min Cron Failed - cron.projects.php" ots-tools-support@nbcuni.com
else
echo "cron.projects.php exit status good" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/fifteen_minutes.log
fi

php /var/www/lighthouse-uxd/lighthouse/current/html/crons/cron.perms.php
if [ $? != 0 ]; then
/bin/mail -s "15 Min Cron Failed - cron.perms.php" ots-tools-support@nbcuni.com
else
echo "cron.perms.php exit status good" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/fifteen_minutes.log
fi


php /var/www/lighthouse-uxd/lighthouse/current/html/crons/cron_wo_alerts.php
if [ $? != 0 ]; then
/bin/mail -s "15 Min Cron Failed - cron_wo_alerts.php" ots-tools-support@nbcuni.com
else
echo "cron_wo_alerts.php exit status good" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/fifteen_minutes.log
fi

php /var/www/lighthouse-uxd/lighthouse/current/html/crons/cron.users.delete.php
if [ $? != 0 ]; then
/bin/mail -s "15 Min Cron Failed - cron.users.delete.php" ots-tools-support@nbcuni.com
else
echo "cron.users.delete.php exit status good" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/fifteen_minutes.log
fi

etime=$(date +%c)
echo $etime "15 Min Cron Completed ..." >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/fifteen_minutes.log
echo "###########################" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/fifteen_minutes.log
echo "" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/fifteen_minutes.log


