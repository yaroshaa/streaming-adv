# Database

## MySQL
### Main idea -> work with doctrine
* Config config/doctrine.php
* Mappings config/mappings

### Workflow to change db
Change or add app/Entities + mapping + app/Repositories
```shell
php artisan doctrine:migrations:diff
php artisan doctrine:migrations:migrate
```

## Clickhouse
# Install
https://clickhouse.tech/#quick-start

May be needed to set a password on /etc/clickhouse-server/users.xml
Search and fill <password></password> section

***

# Migration tools
You need to change columns in clickhouse table?

1. Change config/clickhouse.php mapping to new state
2. php artisan clickhouse:migrations:diff
3. php artisan clickhouse:migrations:migrate
4. You're the best


To update dictionaries
php artisan clickhouse:dict:init --rewrite=true

