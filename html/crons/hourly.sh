time=$(date +%c)
echo $stime "Hourly Cron started ..." >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/hour.log
echo "########################" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/hour.log
php /var/www/lighthouse-uxd/lighthouse/current/html/crons/cron_wo_feedback_alerts.php
if [ $? != 0 ]; then
/bin/mail -s "Hourly Cron Failed - cron_wo_feedback_alerts.php" ots-tools-support@nbcuni.com
else
echo "cron_wo_feedback_alerts.php exit status good" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/hour.log
fi

php /var/www/lighthouse-uxd/lighthouse/current/html/crons/rally_project.php
if [ $? != 0 ]; then
/bin/mail -s "15 Min Cron Failed - rally_project.php" ots-tools-support@nbcuni.com
else
echo "rally_project.php exit status good" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/rally_project.log
fi

etime=$(date +%c)
echo $etime "Hour Cron Completed ..." >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/hour.log
echo "###########################" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/hour.log
echo "" >> /var/www/lighthouse-uxd/lighthouse/current/html/crons/hour.log

