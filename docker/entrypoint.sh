#!/bin/bash
set -e

PROFILE="${1:-web}"
APP_DIR="/var/www/html"

build_assets() {
    echo "Building frontend assets..."
    su -s /bin/bash www-data -c "cd $APP_DIR && npm run build"
}

optimize_app() {
    echo "Optimizing application..."
    su -s /bin/bash www-data -c "cd $APP_DIR && php artisan optimize"
}

case "$PROFILE" in
    web)
        if [ ! -f "$APP_DIR/.env" ]; then
            echo "Missing $APP_DIR/.env. Bind mount your .env file before starting the container."
            exit 1
        fi

        build_assets
        optimize_app

        exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf -n
        ;;
    queue)
        exec php "$APP_DIR/artisan" queue:work --sleep=3 --tries=3
        ;;
    scheduler)
        exec php "$APP_DIR/artisan" schedule:work
        ;;
    *)
        echo "Unknown profile: $PROFILE"
        echo "Usage: entrypoint.sh {web|queue|scheduler}"
        exit 1
        ;;
esac
