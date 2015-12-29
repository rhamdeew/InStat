### Обновления
- Во время получения данных случае если пользователь не существует отправляется письмо администратору
- Добавлен скрипт follows.php позволяющий пользователю авторизоваться через Instagram и посмотреть статистику по своим фолловерам
- Добавлена поддержка кириллических тегов
- Добавлена поддержка бана пользователей и фотографий
- Роутер сделан подключаемым (чтобы делать свои роутинги)
- Добавлена консольная утилита для бана, запуск:
<pre>
php ban.php
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
5. Идем на страницу https://www.instagram.com/developer/clients/manage/ и регистрируем новое приложение.
[Скриншот](http://i.imgur.com/oZZ4bKI.png)
Там получаем CLIENT ID и CLIENT SECRET, соответственно прописываем их в .env
Прописываем у приложения REDIRECT URI
[Скриншот](http://i.imgur.com/CqehjtK.png)
6. В cron записываем правила для получения необходимых данных.
Пример:
<pre>
35 8-23 * * * php /var/www/site/generate.php --topday
30 2 * * * php /var/www/site/generate.php --topweek
0 2 * * 0-5 php /var/www/site/generate.php --best100
0 2 * * 6 php /var/www/site/generate.php --best
0 6 * * * php /var/www/site/generate.php --users
</pre>
7. Опционально копируем шаблон в templates и прописываем название шаблона в .env

### Демо

[InstaUlsk](http://instaulsk.ru)
