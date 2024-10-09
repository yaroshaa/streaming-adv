# Mock

## Web 
http://127.0.0.1:8000/#/generate-mock
## CLI
```shell
php arisan generate:mock // maybe not actual
php arisan feedbacks:generate {--count=100} {--delay=0} // Generate feedbacks with timeout 


```

Generate single order
```bash
php artisan generator --start-date=2021-04-01 --end-date=2021-04-02 --count-orders=1
```

Generate single order for each hour for one day

```bash
php artisan generator --start-date=2021-04-01 --end-date=2021-04-01 --count-orders=1 --foreach-hour
```
this command generate one order for each hour like 00:00, 01:00, 02:00... etc. for 2021-04-01

If need generate order or many orders with specific currency, you can use option --currency, for example:

```bash
php artisan generator --start-date=2021-04-01 --end-date=2021-04-02 --count-orders=1 --currency=EUR
```
if you not use --currency option, then currency will be select as random


Generate infinity orders, use --infinity option, this need for test socket

```bash
php artisan generator --start-date=2021-04-01 --end-date=2021-04-02 --count-orders=1 --infinity --sleep=2
```
--sleep is requirement option for use with --infinity, this is timeout between iterations in seconds.

If you need to generate orders in relation to one product to many orders, use option --use-products, for example:

```bash
php artisan generator --start-date=2021-04-01 --end-date=2021-04-02 --count-orders=1 --use-product
```
Products must be added beforehand. ( php artisan products:sync ) 
 

Generate marketing overview data:
1. generate active users
```bash
php artisan generator:marketing -U --count=2
```

2. generate cart actions
```bash
php artisan generator:marketing -C --count=2
```

3. generate warehouse data
```bash
php artisan generator:marketing -W --count=2
```

4. generate marketing expense data
```bash
php artisan generator:marketing -E --count=2
```

also you can use --infinity or -I for generate infinity data for test web socket

```bash
php artisan generator:marketing -E --infinity
```

you can set --start-date and --end-date

```bash
php artisan generator:marketing -E --count=2 --start-date=2021-04-16 --end-date=2021-04-20
```

this command will add 2 marketing expense items for each days from --start-date to --end-date

### Updated faker
Before use php artisan fake:{command} -> need to run [seeders](setup-project.md)
```shell
php artisan fake:active-user {--count=100} {--date=now} {--market=} {--I|infinity} 
php artisan fake:cart-action {--count=100} {--date=now} {--market=} {--I|infinity}
php artisan fake:marketing-expense {--count=100} {--date=now} {--channel=} {--market=} {--currency=} {--I|infinity}
php artisan fake:warehouse-statistic {--count=100} {--date=now} {--warehouse=} {--market=} {--I|infinity}

php artisan fake:order {--count=100} {--date=now} {--market=} {--status=} {--warehouse-name=} {--currency-code=}
 {--product-ids=} {--product-count-min=1} {--product-count-max=3} {--I|infinity}
// Example, for generate product-statistic by product with multi dates
php artisan fake:order --count=1 --product-count-min=3 --product-ids=1,2,3 --date=2021-06-06 --market=1
php artisan fake:order --count=1 --product-count-min=3 --product-ids=1,2,3 --date=2021-06-07 --market=1

php artisan fake:live-mode {--market=} {--date=now} {--count-min=1} {--count-max=10} // Generate data from all fakers by date
```
