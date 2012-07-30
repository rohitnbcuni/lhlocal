#!/bin/bash

# This is the Global Config for Light House Email Comment Service Job

# set the varialbles
# For Prod
JAVA_HOME=/usr/java/jdk1.6.0_05
PATH=$JAVA_HOME/bin:/$PATH
WORKDIR=/home/videonbc/cron/lighthouseEmailMonitor
EMAIL_LIST="abhilash.kornalliose@nbcuni.com"
LOCKDIR=${WORKDIR}/locks
export https_proxy=https://64.210.197.20:80

echo "Loaded configuration from config.sh"
