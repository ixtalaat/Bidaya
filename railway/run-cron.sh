#!/bin/bash
# Railway cron service start command: runs the Laravel scheduler every minute.
# Make executable: git update-index --chmod=+x railway/run-cron.sh
set -e

while true; do
    echo "Running the scheduler..."
    php artisan schedule:run --verbose --no-interaction &
    sleep 60
done
