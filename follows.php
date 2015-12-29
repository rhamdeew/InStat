<?php
session_start();
require 'vendor/autoload.php';
require 'classes/MyInsta.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

use MetzWeb\Instagram\Instagram;
use MetzWeb\Instagram\MyInsta;

$instagram = new MyInsta(getenv('INSTAGRAM_API_KEY'));
$instagram = new MyInsta(array(
    'apiKey'=>getenv('INSTAGRAM_API_KEY'),
    'apiSecret'=>getenv('INSTAGRAM_API_SECRET'),
    'apiCallback'=>getenv('SITE_URL').'/follows.php',
));

ORM::configure('mysql:host=localhost;dbname='.getenv('DB_NAME'));
ORM::configure('username', getenv('DB_USER'));
ORM::configure('password', getenv('DB_PASSWORD'));

if(!isset($_SESSION['access_token']) && !isset($_GET['code'])) {
	echo "<a href='{$instagram->getLoginUrl()}'>Войти через инстаграм</a>";
}

if(isset($_GET['code'])) {
	$data = $instagram->getOAuthToken($_GET['code']);
	$_SESSION['access_token'] = $data->access_token;
	header('Location: '.getenv('SITE_URL').'/follows.php');
	exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<style>
		body {
			text-align: center;
			color: #333;
			padding-top: 40px;
		}
		.panel {
			position: fixed;
			top:0px;
			height:40px;
			width:100%;
			line-height: 40px;
			border-bottom: 1px solid #CCC;
			background-color: white;
		}
		.panel a {
			margin: 0px 5px;
		}
		table {
			text-align: left;
			margin:auto;
		}
		td {
			border: 1px solid #CCC;
			padding: 10px;
			background-color: #ccff9a;
		}
	</style>
</head>
<body>
<?php
$userid = 'self';
if(isset($_GET['username'])) {
	$result = $instagram->searchUser($_GET['username'],1);
	if(is_object($result)) {
		if($result->meta->code==200) {
			$userid = $result->data[0]->id;
		}
	}
}

if(isset($_SESSION['access_token'])) {
	$instagram->setAccessToken($_SESSION['access_token']);
	?>

	<div class='panel'>
		<a href='#follows'>Мои подписки</a>
		<a href='#followers'>Мои подписчики</a>
		<a href='#not-follower'>Кто не подписаны на меня</a>
		<a href='#not-follow'>На кого я не подписан</a>
	</div>

	<?php
	echo '<h1 id="follows">Мои подписки</h1>';

	$next = '';
	$follows = $instagram->getUserFollows($userid,100);
	$follow = array();
	if(is_object($follows)) {
		if($follows->meta->code==200) {
			foreach($follows->data as $item) {
				$follow[$item->id] = $item->username;
			}
			if(isset($follows->pagination->next_cursor))
				$next = $follows->pagination->next_cursor;
		}
	}
	$i=0;
	while(!empty($next)) {
		$follows = $instagram->getUserFollows($userid,100,$next);
		if($follows->meta->code==200) {
			foreach($follows->data as $item) {
				$follow[$item->id] = $item->username;
			}
			if(isset($follows->pagination->next_cursor))
				$next = $follows->pagination->next_cursor;
			else $next = '';
		}
	}

	echo '<table>';
	echo '<thead><td>ID пользователя</td><td>Имя пользователя</td></thead>';

	foreach($follow as $id => $item) {
		echo '<tr>';
		echo '<td>'.$id.'</td><td><a href="http://instagram.com/'.$item.'">'.$item.'</a></td>';
		echo '</tr>';
	}
	echo '</table>';

	echo '<h1 id="followers">Подписаны на меня</h1>';

	$next = '';
	$followers = $instagram->getUserFollower($userid,100);
	$follower = array();
	if(is_object($followers)) {
		if($followers->meta->code==200) {
			foreach($followers->data as $item) {
				$follower[$item->id] = $item->username;
			}
			if(isset($followers->pagination->next_cursor))
				$next = $followers->pagination->next_cursor;
		}
	}
	$i=0;
	while(!empty($next)) {
		$followers = $instagram->getUserFollower($userid,100,$next);
		if($followers->meta->code==200) {
			foreach($followers->data as $item) {
				$follower[$item->id] = $item->username;
			}
			if(isset($followers->pagination->next_cursor))
				$next = $followers->pagination->next_cursor;
			else $next = '';
		}
	}

	echo '<table>';
	echo '<thead><td>ID пользователя</td><td>Имя пользователя</td></thead>';

	foreach($follower as $id => $item) {
		echo '<tr>';
		echo '<td>'.$id.'</td><td><a href="http://instagram.com/'.$item.'">'.$item.'</a></td>';
		echo '</tr>';
	}
	echo '</table>';

	//На кого я подписан и они не подписаны на меня
	foreach($follow as $id => $item) {
		if(!isset($follower[$id])) {
			$not_follower[$id] = $item;
		}
	}
	echo '<h1 id="not-follower">На кого я подписан и они не подписаны на меня</h1>';
	echo '<table>';
	echo '<thead><td>ID пользователя</td><td>Имя пользователя</td></thead>';

	foreach($not_follower as $id => $item) {
		echo '<tr>';
		echo '<td>'.$id.'</td><td><a href="http://instagram.com/'.$item.'">'.$item.'</a></td>';
		echo '</tr>';
	}
	echo '</table>';

	echo '<br/>';

	//Кто на меня подписан и я не подписан на них
	foreach($follower as $id => $item) {
		if(!isset($follow[$id])) {
			$not_follow[$id] = $item;
		}
	}

	echo '<h1 id="not-follow">Кто на меня подписан и я не подписан на них</h1>';
	echo '<table>';
	echo '<thead><td>ID пользователя</td><td>Имя пользователя</td></thead>';

	foreach($not_follow as $id => $item) {
		echo '<tr>';
		echo '<td>'.$id.'</td><td><a href="http://instagram.com/'.$item.'">'.$item.'</a></td>';
		echo '</tr>';
	}
	echo '</table>';
}
?>
</body>
</html>
