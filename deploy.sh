echo "Deploying application ..."

# Enter maintenance mode
(php artisan down --message 'The app is being (quickly!) updated. Please try again in a minute.') || true
    # Update codebase
    git fetch origin deploy
    git reset --hard origin/deploy

    # Install dependencies based on lock file
    /local/composer install --no-interaction --prefer-dist --optimize-autoloader

    # Migrate database
    php artisan migrate

    php artisan config:cache
    php artisan route:cache

    php artisan doctrine:clear:metadata:cache
    php artisan doctrine:migrations:migrate
    php artisan doctrine:generate:proxies

    php artisan clickhouse:migrations:migrate
    php artisan clickhouse:dict:init --rewrite=true
    php artisan clickhouse:views:init --rewrite=true

    # Reset opcache without cli commands
    curl http://stream.nhgdev.xyz/api/system/op-cache-reset/95b3f901151d2810a02da40a1b6c4e6da628ed6c3f392ed82963e4ccc62f931a/

    # Note: If you're using queue workers, this is the place to restart them.
    # ...

    # Clear cache
    #php artisan optimize # TODO: Needs to be reenabled once the closure is gone

    # Reload PHP to update opcache
    # echo "" | sudo -S service php7.4-fpm reload # TODO: Old files WILL be served until we add a way to clear opcache
    # npm run production

# Exit maintenance mode
php artisan up

echo "Application deployed!"
