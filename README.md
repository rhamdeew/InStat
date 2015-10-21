### Обновления
- Добавлена поддержка кириллических тегов
- Добавлена поддержка бана пользователей и фотографий
- Роутер сделан подключаемым (чтобы делать свои роутинги)

<pre>
	UPDATE user SET banned=1 WHERE user_name="video.prikol";

	SELECT user_id FROM user WHERE user_name="video.prikol";
+------------+
| user_id    |
+------------+
| 1590109359 |
+------------+
1 row in set (0.00 sec)

	UPDATE photos SET banned=1 WHERE user_id=1590109359;
</pre>

### Установка

1. Клонируем репозиторий
2. composer update
3. Копируем .env_sample в .env и редактируем там необходимые поля
4. Импортируем схему БД - 
<pre>
	mysql -uuser -ppassword db < schemes/schema.sql
	mysql -uuser -ppassword db < schemes/schema08102015.sql
	mysql -uuser -ppassword db < schemes/schema21102015.sql
</pre>
5. В cron записываем правила для получения необходимых данных.
Пример:
<pre>
35 8-23 * * * php /var/www/site/generate.php --topday
30 2 * * * php /var/www/site/generate.php --topweek
0 2 * * 0-5 php /var/www/site/generate.php --best100
0 2 * * 6 php /var/www/site/generate.php --best
0 6 * * * php /var/www/site/generate.php --users
</pre>
6. Опционально копируем шаблон в templates и прописываем название шаблона в .env
