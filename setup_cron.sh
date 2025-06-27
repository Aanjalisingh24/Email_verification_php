#!/bin/bash

PHP_PATH=$(which php)
CRON_FILE_PATH="$(pwd)/cron.php"

CRON_JOB="0 0 * * * $PHP_PATH $CRON_FILE_PATH > /dev/null 2>&1"

(crontab -l 2>/dev/null | grep -v -F "$CRON_FILE_PATH"; echo "$CRON_JOB") | crontab -

echo "CRON job installed to run every 24 hours at midnight!"


