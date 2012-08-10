#!/bin/bash
# Loads a global config file
SCRIPT_PATH=`dirname $0`
. $SCRIPT_PATH/conf.sh

# -----------------------------------------------
# check if script is running and create lock file
# -----------------------------------------------
START_TIMESTAMP=$(date +%s)
READABLE_TIME=$(date)

if [ -f $LOCKDIR/.lh_email_cs_running ]; then
  LOG_TIMESTAMP=$(cat $LOCKDIR/.lh_email_cs_running)
  let LAST_RUN=$START_TIMESTAMP-$LOG_TIMESTAMP
  READABLE_TIME2=$(/bin/gawk "BEGIN {print strftime(\"%c\",$LOG_TIMESTAMP)}")
  echo "Lock file found in $LOCKDIR/.lh_email_cs_running from a job, another process must be running started at $READABLE_TIME2"
  echo "light house comment service job blocked by lock file: $LOCKDIR/.lh_email_cs_running. Started:$READABLE_TIME2 Running for: $LAST_RUN seconds"|/bin/mail -s "Light House Comment Service job blocked by lock file " $EMAIL_LIST
  #$READABLE_TIME2 Running for: $LAST_RUN seconds"|/bin/mail -s
  #"light house comment service - job blocked by lock file" $EMAIL_LIST
  exit 1
fi

# create lock file
echo "no lock file found, continuing"
echo $START_TIMESTAMP > $LOCKDIR/.lh_email_cs_running

if [ $? != "0" ]; then
  echo "Could not create lock file"
  echo "failure to create lock file in: $LOCKDIR"|/bin/mail -s "Light House Email Comment Services - unable to create lock file" $EMAIL_LIST
  exit 2
fi

# -----------------------------------------------

echo "$0: Started - `date`"


#java -Dfile.encoding=iso-8859-1 -Xbootclasspath/a:/home/videonbc/cron/lighthouseEmailMonitor/mail-1.4.jar:/home/videonbc/cron/lighthouseEmailMonitor/lhemailcommentprocessor.jar:/home/videonbc/cron/lighthouseEmailMonitor/activation-1.1.jar:/home/videonbc/cron/lighthouseEmailMonitor/commons-codec-1.3.jar:/home/videonbc/cron/lighthouseEmailMonitor/commons-io-2.2.jar:/home/videonbc/cron/lighthouseEmailMonitor/commons-lang3-3.1.jar -jar /home/videonbc/cron/lighthouseEmailMonitor/lhemailcommentprocessor.jar RKFMLVEM04.e2k.ad.ge.com lighthousedev.comments@nbcuni.com GE210User

java -Dfile.encoding=iso-8859-1 -Xbootclasspath/a:/home/videonbc/cron/lighthouseEmailMonitor/mail-1.4.jar:/home/videonbc/cron/lighthouseEmailMonitor/lhemailcommentprocessor.jar:/home/videonbc/cron/lighthouseEmailMonitor/activation-1.1.jar:/home/videonbc/cron/lighthouseEmailMonitor/commons-codec-1.3.jar:/home/videonbc/cron/lighthouseEmailMonitor/commons-io-2.2.jar:/home/videonbc/cron/lighthouseEmailMonitor/commons-lang3-3.1.jar -jar /home/videonbc/cron/lighthouseEmailMonitor/lhemailcommentprocessor.jar /home/videonbc/cron/lighthouseEmailMonitor/conf/lh-comment-mail.properties
echo "$0: Finished - `date`"

# -----------------------------------------
# Remove lock file
# -----------------------------------------

# removing lock file
rm -f $LOCKDIR/.lh_email_cs_running

# -----------------------------------------------
