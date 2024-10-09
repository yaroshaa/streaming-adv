#!/usr/bin/env bash

echo "Deploy application..."

PATH_TO_PROJECT=/var/www/streaming-adv
PATH_TO_TMP_DIR=/var/www/tmp

if true; then

    if [[ -f "dependencies.tar.gz" ]]; then
        echo "Unpack dependencies"
        tar -xzf dependencies.tar.gz && rm dependencies.tar.gz
        rsync -av --progress "$PATH_TO_TMP_DIR/node_modules/" "$PATH_TO_PROJECT/node_modules/"
        rsync -av --progress "$PATH_TO_TMP_DIR/vendor/" "$PATH_TO_PROJECT/vendor/"
    fi

    if [[ -f "changed_files.tar.gz" ]]; then
        echo "Unpack dependencies"
        tar -xzf changed_files.tar.gz && rm changed_files.tar.gz
        rsync -av --progress "$PATH_TO_TMP_DIR/" "$PATH_TO_PROJECT/" --exclude node_modules --exclude vendor
    fi

    NUMBER_OF_PHP_FILES=$(find . -type d \( -path ./node_modules -o -path ./vendor \) -prune -false -o -name '*.php' | wc -l)
    echo "Changed $NUMBER_OF_PHP_FILES php files"

    if [[ "$NUMBER_OF_PHP_FILES" -ne 0 ]]; then
        cd "$PATH_TO_PROJECT" && php artisan down
        cd "$PATH_TO_PROJECT" && php artisan migrate

        cd "$PATH_TO_PROJECT" && php artisan config:cache
        cd "$PATH_TO_PROJECT" && php artisan route:cache

        cd "$PATH_TO_PROJECT" && php artisan doctrine:clear:metadata:cache
        cd "$PATH_TO_PROJECT" && php artisan doctrine:migrations:migrate
        cd "$PATH_TO_PROJECT" && php artisan doctrine:generate:proxies

        cd "$PATH_TO_PROJECT" && php artisan clickhouse:migrations:migrate
        cd "$PATH_TO_PROJECT" && php artisan clickhouse:dict:init --rewrite=true
        cd "$PATH_TO_PROJECT" && php artisan clickhouse:views:init --rewrite=true

        # Seeders
        cd "$PATH_TO_PROJECT" && php artisan db:seed --class=CurrencySeeder
        cd "$PATH_TO_PROJECT" && php artisan db:seed --class=SourceSeeder
        cd "$PATH_TO_PROJECT" && php artisan db:seed --class=MarketSeeder
        cd "$PATH_TO_PROJECT" && php artisan db:seed --class=HolidayEventsSeeder
        cd "$PATH_TO_PROJECT" && php artisan db:seed --class=MarketingChannelsSeeder
        cd "$PATH_TO_PROJECT" && php artisan db:seed --class=WarehousesSeeder

        cd "$PATH_TO_PROJECT" && chmod ugo+rwx storage
        cd "$PATH_TO_PROJECT" && php artisan up

        cd "$PATH_TO_PROJECT" && php artisan test
        if [ $? -eq 1 ]; then
            echo 'Tests failed'
            exit 1
        fi
    fi

    NUMBER_OF_JS_FILES=$(find . -type d \( -path ./node_modules -o -path ./vendor \) -prune -false -o -name '*.vue' -o -name '*.js'| wc -l)

    echo "Changed $NUMBER_OF_JS_FILES js files"

    if [[ "$NUMBER_OF_JS_FILES" -ne 0 ]]; then
        cd "$PATH_TO_PROJECT" && npm run prod
    fi

    if [[ ! -f "$PATH_TO_PROJECT/.env" ]]; then
        echo "\e[31m.env not found"
        exit 1
    fi



    if [[ -d "$PATH_TO_TMP_DIR" ]]; then
        echo "Remove $PATH_TO_TMP_DIR dir"
        rm -r "$PATH_TO_TMP_DIR"
    fi
fi
