time=$(date +%c)
echo $stime "Hourly Cron started ..." >> /var/www/lighthouse-uxd/lighthouse/html/crons/hour.log
echo "########################" >> /var/www/lighthouse-uxd/lighthouse/html/crons/hour.log
php /var/www/lighthouse-uxd/lighthouse/html/crons/cron_wo_feedback_alerts.php
if [ $? != 0 ]; then
/bin/mail -s "Hourly Cron Failed - cron_wo_feedback_alerts.php" ots-tools-support@nbcuni.com
else
echo "cron_wo_feedback_alerts.php exit status good" >> /var/www/lighthouse-uxd/lighthouse/html/crons/hour.log
fi
etime=$(date +%c)
echo $etime "Hour Cron Completed ..." >> /var/www/lighthouse-uxd/lighthouse/html/crons/hour.log
echo "###########################" >> /var/www/lighthouse-uxd/lighthouse/html/crons/hour.log
echo "" >> /var/www/lighthouse-uxd/lighthouse/html/crons/hour.log

