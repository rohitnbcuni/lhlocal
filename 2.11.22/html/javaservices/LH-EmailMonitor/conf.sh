#!/bin/bash

# This is the Global Config for Light House Email Comment Service Job

# set the varialbles
# For Prod
JAVA_HOME=/usr/lib/jvm/java-1.6.0-openjdk-1.6.0.0.x86_64/jre
PATH=$JAVA_HOME/bin:/$PATH
WORKDIR=/var/www/lh_email_monitor
EMAIL_LIST_T="ots-java-support@nbcuni.com"
LOCKDIR=${WORKDIR}/locks
export https_proxy=https://64.210.197.20:80

echo "Loaded configuration from config.sh"
