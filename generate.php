<?php

if(isset($_SERVER['REQUEST_METHOD'])) {
	die('No way!');
}

require 'vendor/autoload.php';
require 'classes/MyInsta.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

use MetzWeb\Instagram\Instagram;
use MetzWeb\Instagram\MyInsta;

$instagram = new MyInsta(getenv('INSTAGRAM_API_KEY'));
ORM::configure('mysql:host=localhost;dbname='.getenv('DB_NAME'));
ORM::configure('username', getenv('DB_USER'));
ORM::configure('password', getenv('DB_PASSWORD'));

$shortopts = "";
$longopts = array(
	"best::",
	"best100::",
	"topday::",
	"topweek::",
	"users::",
	"number::",
	);

$options = getopt($shortopts,$longopts);

//Получаем все фотографии по хэштегу
if(isset($options['best'])) {
	$mode = 'best';
	$tag = $options['best'];
}
//Обновление лайков у 100 лучших фотографий
if(isset($options['best100'])) {
	$mode = 'best100';
	$tag = $options['best100'];
}
//Получаем все фотографии по хэштегу за сутки
if(isset($options['topday'])) {
	$mode = 'topday';
	$tag = $options['topday'];
}
//Получаем все фотографии по хэштегу за неделю
if(isset($options['topweek'])) {
	$mode = 'topweek';
	$tag = $options['topweek'];
}
//Обновляем значения у пользователей
if(isset($options['users'])) {
	$mode = "users";
}

$number = '';
if(isset($options['number'])) {
	$number = $options['number'];
}

if(!isset($tag) || empty($tag)) {
	$tag = getenv('HASHTAG');
}

$date = date('y-m-d');
$time = time();
$params = [];

if($mode=='best' || $mode=='topday' || $mode=='topweek') {

	$tagResult = $instagram->getTag(urlencode($tag));
	$tagCount = $tagResult->data->media_count;

	//Если указываем number, то лимитируем по нему
	if(empty($number)) {
		$number = $tagCount;
	}

	$i = 0;
	while($i<$tagCount && $i<$number) {
		//Не выходим за рамки лимитов
		if($mode=="best") {
			//Получаем последнюю метку пагинации (если существует)
			$result = ORM::for_table('photos')->where('updated',$date)->where('tag',$tag)->where_gt('next_max_id',0)->order_by_asc('created_time')->find_one();

			$params = [];
			if(is_object($result)) {
				$params = ['max_tag_id'=>$result->next_max_id];
			}
			sleep(1);
		}

		$photos = $instagram->getTagMedia(urlencode($tag),33,$params);
		if(is_array($photos->data)) {
			$endPhoto = end($photos->data);

			foreach ($photos->data as $key => $photo) {

				//Если режим с ограничением по дате публикации то останавливаем цикл по условию
				if($mode=="topday" || $mode=="topweek") {
					$timeDiff = ($mode=="topday") ? 86400 : 86400*7;

					if(($time-$photo->created_time)>=$timeDiff) {
						break 2;
					}
				}


				$user = ORM::for_table('user')->where('user_id',$photo->user->id)->find_one();
				if(is_object($user)) {
					//Если пользователь обновил имя то меняем его в БД
					if($user->user_name!=$photo->user->username) {
						$user->user_name = $photo->user->username;
						$user->save();
						echo "Update user: ".$user->user_id."\n";
					}
				}
				else {
					//Если пользователя не существует в БД, то создаем его и делаем первую запись в журнале
					$user = ORM::for_table('user')->create();
					$user->user_id = $photo->user->id;

					$getUser = $instagram->getUser($photo->user->id);
					if($getUser) {
						$user->followers = $getUser->data->counts->followed_by;

						$dbUserLog = ORM::for_table('user_log')->create();
						$dbUserLog->user_id = $photo->user->id;
						$dbUserLog->date = $date;
						$dbUserLog->posts = $getUser->data->counts->media;
						$dbUserLog->followers = $getUser->data->counts->followed_by;
						$dbUserLog->follows = $getUser->data->counts->follows;
						$dbUserLog->save();
						echo "New user log: ".$photo->user->id."\n";
					}

					$user->user_name = $photo->user->username;


					$doubleCheck = ORM::for_table('user')->where('user_name',$photo->user->username)->find_one();
					if(is_object($doubleCheck)) {
						$duser = ORM::for_table('user')->where_equal('user_name', $photo->user->username)->delete_many();
					}

					$user->save();
					echo "New user: ".$photo->user->username."\n";

				}


				// $dbPhoto = ORM::for_table('photos')->where('photo_id',$photo->id)->where('tag',$tag)->find_one();
				$dbPhoto = ORM::for_table('photos')->where('photo_id',$photo->id)->find_one();
				if(is_object($dbPhoto)) {
					//Если фото существует обновляем количество комментариев
					if($dbPhoto->comments!=$photo->comments->count) {
						$dbPhoto->comments = $photo->comments->count;
					}
					//Если фото существует обновляем лайки
					if($dbPhoto->likes!=$photo->likes->count) {
						$dbPhoto->likes = $photo->likes->count;
					}
					if($dbPhoto->tag!=$tag) {
						$dbPhoto->tag = $tag;
					}
					if($user->banned==1) {
						$dbPhoto->banned = 1;
					}
					$dbPhoto->updated = $date;
					$dbPhoto->save();
				}
				else {
					//Если не существует, то создаем
					$dbPhoto = ORM::for_table('photos')->create();
					$dbPhoto->created_time = $photo->created_time;
					$dbPhoto->link = $photo->link;
					$dbPhoto->user_id = $user->user_id;
					$dbPhoto->tag = $tag;
					$dbPhoto->updated = $date;
					$dbPhoto->photo_id = $photo->id;
					$dbPhoto->likes = $photo->likes->count;
					$dbPhoto->comments = $photo->comments->count;
					if($user->banned==1) {
						$dbPhoto->banned = 1;
					}
					$dbPhoto->save();
					echo "New photo: ".$photo->id."\n";
				}

				//Ставим метку пагинации
				if($dbPhoto->photo_id==$endPhoto->id) {
					if(isset($photos->pagination->next_max_tag_id)) {
						if($mode=="best") {
							$dbPhoto->next_max_id = $photos->pagination->next_max_tag_id;
							$dbPhoto->save();
						}
						else {
							$params = ['max_tag_id'=>$photos->pagination->next_max_tag_id];
						}
					}
					else {
						exit("Done!\n");
					}
				}
			}
		}
		else {
			echo "No data from instagram"."\n";
		}

		$updatedCount = ORM::for_table('photos')->where('updated',$date)->where('tag',$tag)->count();
		$i = $updatedCount;
	}
}

if($mode=="best100") {
	$photos = ORM::for_table('photos')->order_by_desc('likes')->limit(100)->find_many();
	foreach($photos as $photo) {
		$result = $instagram->getMedia($photo->photo_id);
		if($result) {
			if($photo->likes!=$result->data->likes->count || $photo->comments!=$result->data->comments->count) {
				$photo->likes = $result->data->likes->count;
				$photo->comments = $result->data->comments->count;
				$photo->updated = $date;
				$photo->save();
				echo "Update photo: ".$photo->id."\n";
			}
		}
	}
}

if($mode=="users") {
	if(empty($number)) {
		$users = ORM::for_table('user')->order_by_desc('followers')->find_many();
	}
	else {
		$users = ORM::for_table('user')->order_by_desc('followers')->limit($number)->find_many();
	}

	foreach($users as $key => $user) {
		if(empty($number)) {
			sleep(1);
		}
		$result = $instagram->getUser($user->user_id);

		if($result) {
			$dbUserLog = ORM::for_table('user_log')->where('date',$date)->where('user_id',$user->user_id)->find_one();
			if(is_object($dbUserLog)) {
				if($result->data->counts->media!=$dbUserLog->posts || $result->data->counts->followed_by!=$dbUserLog->followers || $result->data->counts->follows!=$dbUserLog->follows) {
					if(isset($result->data->counts->media))
						$dbUserLog->posts = $result->data->counts->media;
					if(isset($result->data->counts->followed_by))
						$dbUserLog->followers = $result->data->counts->followed_by;
					if(isset($result->data->counts->follows))
						$dbUserLog->follows = $result->data->counts->follows;
					$dbUserLog->save();
					echo "Update user log: ".$user->user_id."   ".($key+1)."\n";
				}
			}
			else {
				$dbUserLog = ORM::for_table('user_log')->create();
				$dbUserLog->user_id = $user->user_id;
				$dbUserLog->date = $date;
				if(isset($result->data->counts->media)) {
					$dbUserLog->posts = $result->data->counts->media;
				}
				if(isset($result->data->counts->followed_by)) {
					$dbUserLog->followers = $result->data->counts->followed_by;
				}
				if(isset($result->data->counts->follows)) {
					$dbUserLog->follows = $result->data->counts->follows;
				}
				$dbUserLog->save();
				echo "New user log: ".$user->user_id."   ".($key+1)."\n";
			}

			//Упрощаем себе жизнь простыми выборками
			if(isset($result->data->counts->followed_by)) {
				if($user->followers != $result->data->counts->followed_by) {
					$user->followers = $result->data->counts->followed_by;
					$user->save();
				}
			}
		}
	}
}

echo "Done!\n";
