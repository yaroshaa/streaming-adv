# Setup project
- install php7.4-fpm, composer, node v12.19, npm v6.14, ClickhouseDB v20.9, redis v5.0, laravel-echo-server v1.6.2,
  mysql 8.0
- run `composer install` and `npm install`
- copy `.env.example` to `.env` set `BROADCAST_DRIVER=redis` and fill `CLICKHOUSE_`[doc](Docs/Developers/database.md), `FB_` [doc](Docs/Integrations/Facebook/index.md), `GOOGLE_` and `DB_` sections
- to make project works run:

        php artisan serve
        npm run dev
        laravel-echo-server start

- create the database in the Clickhouse by the commands:
```bash
        php artisan clickhouse:migrations:migrate
        php artisan clickhouse:dict:init
        php artisan clickhouse:views:init
```

- to migrate mysql database run:
```bash
        php artisan migrate
        php artisan doctrine:migration:migrate
        php artisan doctrine:generate:proxies
``` 

- fill the database by placing this config into the `crontab -e`
    
        * * * * * php /path/to/your/project/artisan schedule:run >> /dev/null 2>&1
        * * * * * php /path/to/your/project/artisan fb:sync
        0 0 1 * * php /path/to/your/project/artisan exchange:rates:sync

- Available seeding
```bash
  php artisan db:seed --class=CurrencySeeder
  php artisan db:seed --class=SourceSeeder
  php artisan db:seed --class=MarketSeeder
  php artisan db:seed --class=HolidayEventsSeeder
  php artisan db:seed --class=MarketingChannelsSeeder
  php artisan db:seed --class=WarehousesSeeder

```

- run `php artisan jwt:secret` to generate jsw secret key

- Make google auth token [Google integration](../Integrations/GoogleAuth/index.md)
