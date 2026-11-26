#!/bin/bash
set -e

PROFILE="${1:-web}"

case "$PROFILE" in
    web)
        /usr/bin/supervisord -c /etc/supervisor/supervisord.conf -n
        ;;
    queue)
        php /var/www/html/artisan queue:work --sleep=3 --tries=3
        ;;
    scheduler)
        php /var/www/html/artisan schedule:work
        ;;
    *)
        echo "Unknown profile: $PROFILE"
        echo "Usage: entrypoint.sh {web|queue|scheduler}"
        exit 1
        ;;
esac
