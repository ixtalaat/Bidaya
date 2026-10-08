#!/bin/bash
# Railway worker service start command.
# Make executable: git update-index --chmod=+x railway/run-worker.sh
set -e

php artisan queue:work --tries=3 --backoff=30
