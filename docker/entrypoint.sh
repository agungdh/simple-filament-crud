#!/bin/bash
set -e

PROFILE="${1:-web}"
APP_DIR="/var/www/html"

build_assets() {
    echo "Building frontend assets..."
    su -s /bin/bash www-data -c "cd $APP_DIR && npm run build"
}

migrate_database() {
    echo "Running database migrations..."
    su -s /bin/bash www-data -c "cd $APP_DIR && php artisan migrate --force"
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

        exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf -n
        ;;
    deploy)
        if [ ! -f "$APP_DIR/.env" ]; then
            echo "Missing $APP_DIR/.env. Bind mount your .env file before running the deploy step."
            exit 1
        fi

        build_assets
        migrate_database
        optimize_app
        ;;
    queue)
        # Run as www-data so SQLite WAL/SHM files are not created as root.
        exec su -s /bin/bash www-data -c "cd $APP_DIR && exec php artisan queue:work --sleep=3 --tries=3 --timeout=90 --max-time=3600"
        ;;
    scheduler)
        exec su -s /bin/bash www-data -c "cd $APP_DIR && exec php artisan schedule:work"
        ;;
    *)
        echo "Unknown profile: $PROFILE"
        echo "Usage: entrypoint.sh {web|deploy|queue|scheduler}"
        exit 1
        ;;
esac
