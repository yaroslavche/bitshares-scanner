## Required
- PHP 7.2
- MariaDB 10.2 (for Doctrine2 json_array) || MySQL version with json_array support

## install
```bash
composer install
bin/console d:d:c #doctrine:database:create
bin/console d:s:c #doctrine:schema:create
bin/console d:f:l #doctrine:fixtures:load
```
setup cron
add in jmos command scheduler task for command b:u:a (btss:update:assets)
```bash
crontab -e
#add line in crontab
0 * * * * php /var/www/vhosts/bts-scanner/bin/console scheduler:execute -vvv > /var/www/vhosts/bts-scanner/var/log/cron.log
```

 - `However, please be aware that by default the nodes' smallest buckets are 1 minute, if you want 3-second buckets you can run your own node with that set in config.ini, may need a lot of RAM though.` [link][1]

[1]: https://github.com/bitshares/bitshares-core/issues/552
