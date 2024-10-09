##### Path to sources: resources/js/analytics
Seeding test data
```bash
php artisan db:seed --class=AnalyticsSiteSeeder
```

- app.js this file contain analytics library and after execute build command compile to public/js/stream-analytics.js  

- injectSource.js this file contain injection script for use on client side

- test.html this file contain example with injection script and two actions "pageview" and "click"
