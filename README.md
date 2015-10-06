1. Клонируем репозиторий
2. composer update
3. Копируем .env_sample в .env и редактируем там необходимые поля
4. Импортируем схему БД - mysql -uuser -ppassword db < schema.sql
5. В cron записываем правила для получения необходимых данных.
Пример:
<pre>
35 8-23 * * * php /var/www/site/generate.php --topday
30 2 * * * php /var/www/site/generate.php --topweek
0 2 * * 0-5 php /var/www/site/generate.php --best100
0 2 * * 6 php /var/www/site/generate.php --best
0 6 * * * php /var/www/site/generate.php --users
</pre>