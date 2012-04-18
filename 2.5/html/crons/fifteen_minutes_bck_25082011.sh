#!/bin/sh
if [ -e test.file ]
then
	exit 0
else
	touch test.file
	php /var/www/lighthouse-uxd/lh/lighthouse/html/crons/cron.activate_wo.php	
	php /var/www/lighthouse-uxd/lh/lighthouse/html/crons/cron.companies.php
	php /var/www/lighthouse-uxd/lh/lighthouse/html/crons/cron.users.php
	php /var/www/lighthouse-uxd/lh/lighthouse/html/crons/cron.projects.php
	php /var/www/lighthouse-uxd/lh/lighthouse/html/crons/cron.perms.php
	rm test.file
fi
