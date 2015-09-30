<?php

require 'vendor/autoload.php';
require 'classes/MyInsta.php';
require 'classes/Template.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

use MetzWeb\Instagram\Instagram;
use MetzWeb\Instagram\MyInsta;

// error_reporting(E_ALL);
$instagram = new MyInsta(getenv('INSTAGRAM_API_KEY'));
ORM::configure('mysql:host=localhost;dbname='.getenv('DB_NAME'));
ORM::configure('username', getenv('DB_USER'));
ORM::configure('password', getenv('DB_PASSWORD'));

$klein = new \Klein\Klein();

$klein->respond('GET', '/', function () {
	$time = time();	
	$items = ORM::for_table('photos')->where_gt('created_time',$time-86400)->order_by_desc('likes')->limit(10)->find_many();
    return Template::render('index',['header'=>'Топ 10 фотографий за сутки #ulsk','items'=>$items]);
});

$klein->respond('GET', '/topweek', function () {
	$time = time();	
	$items = ORM::for_table('photos')->where_gt('created_time',$time-(86400*7))->order_by_desc('likes')->limit(10)->find_many();
    return Template::render('index',['header'=>'Топ 10 фотографий за неделю #ulsk','items'=>$items]);
});

$klein->respond('GET', '/oldest', function ($response) {
	$items = ORM::for_table('photos')->order_by_asc('created_time')->limit(10)->find_many();
	$pagination = [
		1 => 'active',
		2,3,4,5,6,7,8,9,10
	];
	if(!empty($items)) {
 	return Template::render('index',[
 		'header'=>'10 самых старых фотографий #ulsk',
 		'items'=>$items,
 		'pagination'=>$pagination,
 		'partUrl'=>'/oldest/',
 	]);
    }
    else {
 	return Template::render('error',['header'=>'Такой страницы нет'],404);
	}
});

$klein->respond('GET', '/oldest/[i:page]', function ($request,$response) {
	$offset = $request->page-1;
	if($offset>0) {
		$count = ORM::for_table('photos')->order_by_asc('created_time')->offset(10*$offset)->limit(90)->select('id')->find_many();
		$pagination = [
			$offset,
			'active',
		];
		$nextPages = floor(count($count)/10);
		for($i=1;$i<=$nextPages;$i++) {
			$pagination[] = $request->page+$i; 
		}

		$items = ORM::for_table('photos')->order_by_asc('created_time')->offset(10*$offset)->limit(10)->find_many();
		if(!empty($items)) {
	 	return Template::render('index',[
	 		'header'=>'Самые старые фотографии #ulsk',
	 		'items'=>$items,
	 		'page'=>$request->page,
	 		'pagination'=>$pagination,
	 		'partUrl'=>'/oldest/',
	 	]);
		}
	}
	
	return Template::render('error',['header'=>'Такой страницы нет'],404);
});

$klein->respond('GET', '/best', function ($request,$response) {
	$items = ORM::for_table('photos')->order_by_desc('likes')->limit(10)->find_many();
	$pagination = [
		1 => 'active',
		2,3,4,5,6,7,8,9,10
	];
	if(!empty($items)) {
 	return Template::render('index',[
 		'header'=>'10 самых лучших фотографий #ulsk',
 		'items'=>$items,
 		'pagination'=>$pagination,
 		'partUrl'=>'/best/',
 	]);
	}
	else {
 	return Template::render('error',['header'=>'Такой страницы нет'],404);
	}
});

$klein->respond('GET', '/best/[i:page]', function ($request,$response) {
	$offset = $request->page-1;
	if($offset>0) {
		$count = ORM::for_table('photos')->order_by_desc('likes')->offset(10*$offset)->limit(90)->select('id')->find_many();
		$pagination = [
			$offset,
			'active',
		];
		$nextPages = floor(count($count)/10);
		for($i=1;$i<=$nextPages;$i++) {
			$pagination[] = $request->page+$i; 
		}

		$items = ORM::for_table('photos')->order_by_desc('likes')->offset(10*$offset)->limit(10)->find_many();
		if(!empty($items)) {
	 	return Template::render('index',[
	 		'header'=>'Самые лучшие фотографии #ulsk',
	 		'items'=>$items,
	 		'page'=>$request->page,
	 		'pagination'=>$pagination,
 			'partUrl'=>'/best/',
	 	]);
		}		
	}
	
	return Template::render('error',['header'=>'Такой страницы нет'],404);
});

$klein->respond('GET', '/not-popular', function ($request,$response) {
	$items = ORM::for_table('photos')->order_by_asc('likes')->limit(10)->find_many();
	$pagination = [
		1 => 'active',
		2,3,4,5,6,7,8,9,10
	];
	if(!empty($items)) {
 	return Template::render('index',[
 		'header'=>'10 самых непопулярных фотографий #ulsk',
 		'items'=>$items,
	 	'pagination'=>$pagination,
 		'partUrl'=>'/not-popular/',
 	]);
	}
	else {
 	return Template::render('error',['header'=>'Такой страницы нет'],404);
	}
});

$klein->respond('GET', '/not-popular/[i:page]', function ($request,$response) {
	$offset = $request->page-1;
	if($offset>0) {
		$count = ORM::for_table('photos')->order_by_asc('likes')->offset(10*$offset)->limit(90)->select('id')->find_many();
		$pagination = [
			$offset,
			'active',
		];
		$nextPages = floor(count($count)/10);
		for($i=1;$i<=$nextPages;$i++) {
			$pagination[] = $request->page+$i; 
		}

		$items = ORM::for_table('photos')->order_by_asc('likes')->offset(10*$offset)->limit(10)->find_many();
		if(!empty($items)) {
	 	return Template::render('index',[
	 		'header'=>'Самые непопулярные фотографии #ulsk',
	 		'items'=>$items,
	 		'page'=>$request->page,
	 		'pagination'=>$pagination,
 			'partUrl'=>'/not-popular/',
	 	]);
		}		
	}
	
	return Template::render('error',['header'=>'Такой страницы нет'],404);
});

$klein->respond('GET', '/users', function ($request,$response) {
	$hour = date('H');
	$yesterDayFlag = false;
	if($hour>=0 && $hour<8) {
		$date = date('y-m-d',time()-86400);
		$yesterdayDate = date('y-m-d',time()-(86400*2));
		$yesterDayFlag = true;
	}
	else {
		$date = date('y-m-d');
		$yesterdayDate = date('y-m-d',time()-86400);
	}

	$yesterdayUsers = [];

	$todayItems = ORM::for_table('user_log')
	->table_alias('ul')
	->where('ul.date',$date)
	->order_by_desc('ul.followers')
	->join('user',['ul.user_id','=','u.user_id'],'u')
	->limit(100)
	->select('ul.*')
	->select('u.user_name')
	->find_many();
	
	$yesterdayItems = ORM::for_table('user_log')->where('date',$yesterdayDate)->order_by_desc('followers')->find_many();
	foreach($yesterdayItems as $yesterdayItem) {
		$yesterdayUsers[$yesterdayItem->user_id] = $yesterdayItem;
	}

	$pagination = [
		1 => 'active',
		2,3,4,5,6,7,8,9,10
	];

	if(!empty($todayItems)) {
 	return Template::render('users',[
 		'header'=>'Самые популярные пользователи',
 		'todayItems'=>$todayItems,
 		'yesterdayUsers'=>$yesterdayUsers, 
 		'yesterDayFlag'=>$yesterDayFlag,
 		'pagination'=>$pagination,
 		'partUrl'=>'/users/',
 	]);
	}
	else {
 	return Template::render('error',['header'=>'Такой страницы нет'],404);
	}
});

$klein->respond('GET', '/users/[i:page]', function ($request,$response) {
	$offset = $request->page-1;
	if($offset>0) {
		$hour = date('H');
		$yesterDayFlag = false;
		if($hour>=0 && $hour<8) {
			$date = date('y-m-d',time()-86400);
			$yesterdayDate = date('y-m-d',time()-(86400*2));
			$yesterDayFlag = true;
		}
		else {
			$date = date('y-m-d');
			$yesterdayDate = date('y-m-d',time()-86400);
		}

		$yesterdayUsers = [];

		$count = ORM::for_table('user_log')
			->table_alias('ul')
			->where('ul.date',$date)
			->order_by_desc('ul.followers')
			->offset(100*$offset)
			->limit(900)
			->select('ul.id')
			->find_many();

		$pagination = [
			$offset,
			'active',
		];
		$nextPages = floor(count($count)/100);
		for($i=1;$i<=$nextPages;$i++) {
			$pagination[] = $request->page+$i; 
		}		

		$todayItems = ORM::for_table('user_log')
		->table_alias('ul')
		->where('ul.date',$date)
		->order_by_desc('ul.followers')
		->join('user',['ul.user_id','=','u.user_id'],'u')
		->offset(100*$offset)
		->limit(100)
		->select('ul.*')
		->select('u.user_name')
		->find_many();
		
		$yesterdayItems = ORM::for_table('user_log')->where('date',$yesterdayDate)->order_by_desc('followers')->find_many();
		foreach($yesterdayItems as $yesterdayItem) {
			$yesterdayUsers[$yesterdayItem->user_id] = $yesterdayItem;
		}

		if(!empty($todayItems)) {
	 	return Template::render('users',[
	 		'header'=>'Самые популярные пользователи',
	 		'todayItems'=>$todayItems,
	 		'yesterdayUsers'=>$yesterdayUsers,
	 		'page'=>$request->page,
	 		'yesterDayFlag'=>$yesterDayFlag,
	 		'pagination'=>$pagination,
	 		'partUrl'=>'/users/',
	 	]);
		}		
	}
	
	return Template::render('error',['header'=>'Такой страницы нет'],404);
});

$klein->respond('GET', '/user-search/[*:username]', function ($request,$response) {
	
	if(date('H')<8) {
		$date = date('y-m-d',time()-86400);
	}
	else {
		$date = date('y-m-d');
	}

	$partUrl = '/users/';
	if(preg_match("/^[A-z0-9+-_]+$/", $request->username) == 1) {
		$username = strtolower($request->username);
		
		$user = ORM::for_table('user')->where('user_name',$username)->find_one();

		if(is_object($user)) {
			$followers = ORM::for_table('user_log')->where('date',$date)->where('user_id',$user->user_id)->select('followers')->find_one();
			$result = ORM::for_table('user_log')
			->table_alias('ul')
			->where_gte('ul.followers',$followers->followers)
			->where('ul.date',$date)
			->join('user',['ul.user_id','=','u.user_id'],'u')
			->order_by_desc('ul.followers')
			->select('ul.*')
			->select('u.user_name')
			->find_many();

			// $result = ORM::for_table('user_log')->raw_query('SELECT user_id FROM user_log WHERE followers>=(SELECT followers FROM user_log WHERE date="'.$date.'" AND user_id="'.$user->user_id.'") AND date="'.$date.'" ORDER BY followers DESC')->find_many();
			foreach ($result as $key => $value) {
				if($value->user_id == $user->user_id) {
					$page = ceil(($key+1)/100);
					return Template::render('user-search',[
						'header'=>'Поиск по нику в списке пользователей',
						'username'=>$username,
						'page'=>$page,
						'partUrl'=>$partUrl,
					]);
				}
			}	
		}
	}

	return Template::render('error',['header'=>'Такой страницы нет'],404);
});

$klein->respond('GET', '/user-detail/[*:username]', function ($request,$response) {
	
	$partUrl = '/users/';
	if(preg_match("/^[A-z0-9+-_]+$/", $request->username) == 1) {
		$username = strtolower($request->username);
		
		$user = ORM::for_table('user')->where('user_name',$username)->find_one();

		if(is_object($user)) {
			$result = ORM::for_table('user_log')->where('user_id',$user->user_id)->order_by_desc('date')->limit(30)->find_many();

			if(!empty($result)) {
				return Template::render('user-detail',[
					'header'=>'Детальная информация по пользователю',
					'username'=>$username,
					'result'=>$result,
					'partUrl'=>$partUrl,
				]);
			}
		}
	}

	return Template::render('error',['header'=>'Такой страницы нет'],404);
});

// $klein->respond('GET', '/photo-search/[*:username]', function ($request,$response) {
// 	$partUrl = '/best/';
// 	if(preg_match("/^[A-z0-9+-_]+$/", $request->username) == 1) {
// 		$username = strtolower($request->username);
		
// 		$user = ORM::for_table('user')->where('user_name',$username)->select('user_id')->find_one();
// 		$result = ORM::for_table('photos')->where('user_id',$user->user_id)->select('likes')->select('photo_id')->order_by_asc('likes')->find_many();
		
// 		$r = ORM::for_table('photos')->select('photo_id')->order_by_desc('likes')->where_gte('likes',$result[0]->likes)->find_array();

// 		$start = $result[0]->photo_id;
// 		$endkeys = array_keys($result);
// 		$endkey = end($endkeys);
// 		$end = $result[$endkey]->photo_id;

// 		$sliceKey = 0;
// 		foreach ($r as $key => $value) {
// 			if($value['photo_id']==$start) {
// 				echo $start;
// 				$sliceKey = $key;
// 				break;
// 			}
// 		}
// 		if($sliceKey>0) {
// 			echo $sliceKey;
// 			$r = array_slice($r, 0, $sliceKey, true);
// 		}

// 		$sliceKey = 0;
// 		foreach ($r as $key => $value) {
// 			if($value['photo_id']==$end) {
// 				$sliceKey = $key;
// 				break;
// 			}
// 		}
// 		if($sliceKey>0) {
// 			$r = array_slice($r, $sliceKey, NULL, true);
// 		}

// 		$photos = [];
// 		$sliceKey = 0;
// 		foreach ($result as $item) {
// 			foreach ($r as $key => $value) {
// 				if($value['photo_id'] == $item->photo_id) {
// 					$page = ceil(($key+1)/10);
// 					$photos[$page] = '';
// 				}
// 			}
// 		}
// 		return Template::render('photo-search',[
// 			'header'=>'Поиск по нику в списке фото',
// 			'username'=>$username,
// 			'photos'=>$photos,
// 			'partUrl'=>$partUrl,
// 		]);
// 	}

// 	return Template::render('error',['header'=>'Такой страницы нет']);	
// });

$klein->respond('GET', '/about', function ($request,$response) {
	$content = "
	Полная статистика по популярному ульяновскому хэштегу #ulsk<br/>
	Данные по фотографиям обновляются раз в час, по пользователям раз в сутки.<br/><br/>
	<p>
	Проект еще в стадии начальной разработки и если у вас есть какие-то пожелания то пожалуйста свяжитесь со мной одним из удобных способов:<br/>
		<ul>
		<li>r@hmdw.me</li>
		<li>twitter.com/rhamdeew</li>
		<li>vk.com/hamdeew</li>
		</ul>
	</p>
	";
	return Template::render('page',['header'=>'О проекте','content'=>$content]);
});

// $klein->respond('GET', '/statistic', function ($request,$response) {
// 	$result = ORM::for_table('photos')->raw_query('SELECT MONTH(FROM_UNIXTIME(created_time)) as m, YEAR(FROM_UNIXTIME(created_time)) as y, COUNT(*) as c FROM photos GROUP BY MONTH(FROM_UNIXTIME(created_time)), YEAR(FROM_UNIXTIME(created_time))')->find_many();
// 	$sortedByMonthYear = [];
// 	foreach ($result as $item) {
// 		$sortedByMonthYear[$item->y][$item->m] = $item->c;
// 	}
// 	ksort($sortedByMonthYear);

// 	return Template::render('statistic',['header'=>'Статистика','sorted'=>$sortedByMonthYear]);
// });

// $klein->respond('404', function ($request) {
	// return Template::render('error',['header'=>'Такой страницы нет']);
// });

$klein->onHttpError(function ($code, $router) {
	return Template::render('error',['header'=>'Такой страницы нет'], 404);
});

$klein->dispatch();
?>