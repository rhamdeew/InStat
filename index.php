<?php

require 'vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

error_reporting(E_ALL);

ORM::configure('mysql:host=localhost;dbname='.getenv('DB_NAME'));
ORM::configure('username', getenv('DB_USER'));
ORM::configure('password', getenv('DB_PASSWORD'));

$hashTag = getenv('HASHTAG');

$klein = new \Klein\Klein();

$klein->respond(function ($request, $response, $service) {
	$service->hashTag = getenv('HASHTAG');
	$service->siteName = getenv('SITENAME');
	$service->templatePath = 'templates/'.getenv('TEMPLATE').'/';

	$service->layout($service->templatePath.'layouts/default.php');
});

//Index page
$klein->respond('GET', '/', function ($request, $response, $service) {
	$time = time();
	$items = ORM::for_table('photos')->where_gt('created_time',$time-86400)->order_by_desc('likes')->limit(10)->find_many();

	$service->metaKeywords = 'Лучшие инстаграмы Ульяновска за сутки';
	$service->metaTitle = 'Топ 10 фотографий за сутки #'.$service->hashTag;
	$service->pageTitle = 'Топ 10 фотографий за сутки #'.$service->hashTag;

	$service->items = $items;
	$service->render($service->templatePath.'views/best.php');
});

//Topweek page
$klein->respond('GET', '/topweek', function ($request, $response, $service) {
	$time = time();
	$items = ORM::for_table('photos')->where_gt('created_time',$time-(86400*7))->order_by_desc('likes')->limit(10)->find_many();

	$service->metaKeywords = 'Лучшие инстаграмы Ульяновска за неделю';
	$service->metaTitle = 'Топ 10 фотографий за неделю #'.$service->hashTag;
	$service->pageTitle = 'Топ 10 фотографий за неделю #'.$service->hashTag;

	$service->items = $items;
	$service->render($service->templatePath.'views/best.php');
});

//Best-ever page
$klein->respond('GET', '/best', function ($request, $response, $service) use ($klein) {
	$items = ORM::for_table('photos')->order_by_desc('likes')->limit(10)->find_many();
	$pagination = [
		1 => 'active',
		2,3,4,5,6,7,8,9,10
	];
	if(!empty($items)) {
		$service->metaKeywords = 'Лучшие инстаграмы Ульяновска за все время';
		$service->metaTitle = 'Лучшие фотографии за все время #'.$service->hashTag;
		$service->pageTitle = 'Лучшие фотографии за все время #'.$service->hashTag;

		$service->items = $items;
		$service->pagination = $pagination;
		$service->partUrl = '/best/';
		$service->render($service->templatePath.'views/best.php');
	}
	else {
		$response->code('404');
		$service->metaTitle = "404";
		$service->render($service->templatePath.'views/error.php');
	}
});

//Best-ever page with pagination
$klein->respond('GET', '/best/[*:page]', function ($request, $response, $service) use ($klein) {
	$flag = false;
	if(filter_var($request->page,FILTER_VALIDATE_INT,array("min_range"=>1))) {
		$offset = $request->page-1;
		if($offset==0) {
			header('Location: /best');
			exit;
		}
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
				$service->metaKeywords = 'Лучшие инстаграмы Ульяновска за все время страница '.$request->page;
				$service->metaTitle = 'Лучшие фотографии за все время #'.$service->hashTag.' страница '.$request->page;
				$service->pageTitle = 'Лучшие фотографии за все время #'.$service->hashTag.' страница '.$request->page;

				$service->items = $items;
				$service->pagination = $pagination;
				$service->partUrl = '/best/';
				$service->page = $request->page;
				$service->render($service->templatePath.'views/best.php');
				$flag = true;
			}
		}
	}
	if(!$flag) {
		$response->code('404');
		$service->metaTitle = "404";
		$service->render($service->templatePath.'views/error.php');
	}

});


$klein->respond('GET', '/oldest', function ($request, $response, $service) use ($klein) {
	$items = ORM::for_table('photos')->order_by_asc('created_time')->limit(10)->find_many();
	$pagination = [
		1 => 'active',
		2,3,4,5,6,7,8,9,10
	];

	if(!empty($items)) {
		$service->metaKeywords = 'Самые старые инстаграмы Ульяновска';
		$service->metaTitle = 'Самые старые инстаграмы Ульяновска #'.$service->hashTag;
		$service->pageTitle = 'Самые старые инстаграмы Ульяновска #'.$service->hashTag;

		$service->items = $items;
		$service->pagination = $pagination;
		$service->partUrl = '/oldest/';
		$service->render($service->templatePath.'views/best.php');
	}
	else {
		$response->code('404');
		$service->metaTitle = "404";
		$service->render($service->templatePath.'views/error.php');
	}
});

$klein->respond('GET', '/oldest/[*:page]', function ($request, $response, $service) use ($klein) {
	$flag = false;
	if(filter_var($request->page,FILTER_VALIDATE_INT,array("min_range"=>1))) {
		$offset = $request->page-1;
			if($offset==0) {
				header('Location: /oldest');
				exit;
			}
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
				$service->metaKeywords = 'Самые старые инстаграмы Ульяновска страница '.$request->page;
				$service->metaTitle = 'Самые старые инстаграмы Ульяновска #'.$service->hashTag.' страница '.$request->page;
				$service->pageTitle = 'Самые старые инстаграмы Ульяновска #'.$service->hashTag.' страница '.$request->page;

				$service->items = $items;
				$service->pagination = $pagination;
				$service->partUrl = '/oldest/';
				$service->page = $request->page;
				$service->render($service->templatePath.'views/best.php');
				$flag = true;
			}
		}
	}
	if(!$flag) {
		$response->code('404');
		$service->metaTitle = "404";
		$service->render($service->templatePath.'views/error.php');
	}
});

$klein->respond('GET', '/not-popular', function ($request, $response, $service) use ($klein) {
	$items = ORM::for_table('photos')->order_by_asc('likes')->limit(10)->find_many();
	$pagination = [
		1 => 'active',
		2,3,4,5,6,7,8,9,10
	];
	if(!empty($items)) {
		$service->metaKeywords = 'Самые непопулярные инстаграмы Ульяновска';
		$service->metaTitle = 'Самые непопулярные инстаграмы Ульяновска #'.$service->hashTag;
		$service->pageTitle = 'Самые непопулярные инстаграмы Ульяновска #'.$service->hashTag;

		$service->items = $items;
		$service->pagination = $pagination;
		$service->partUrl = '/not-popular/';
		$service->render($service->templatePath.'views/best.php');
	}
	else {
		$response->code('404');
		$service->metaTitle = "404";
		$service->render($service->templatePath.'views/error.php');
	}
});

$klein->respond('GET', '/not-popular/[*:page]', function ($request, $response, $service) use ($klein) {
	$flag = false;
	if(filter_var($request->page,FILTER_VALIDATE_INT,array("min_range"=>1))) {
		$offset = $request->page-1;
			if($offset==0) {
			header('Location: /not-popular');
			exit;
		}

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
				$service->metaKeywords = 'Самые непопулярные инстаграмы Ульяновска страница '.$request->page;
				$service->metaTitle = 'Самые непопулярные инстаграмы Ульяновска #'.$service->hashTag.' страница '.$request->page;
				$service->pageTitle = 'Самые непопулярные инстаграмы Ульяновска #'.$service->hashTag.' страница '.$request->page;

				$service->items = $items;
				$service->pagination = $pagination;
				$service->partUrl = '/not-popular/';
				$service->page = $request->page;
				$service->render($service->templatePath.'views/best.php');
				$flag = true;
			}
		}
	}
	if(!$flag) {
		$response->code('404');
		$service->metaTitle = "404";
		$service->render($service->templatePath.'views/error.php');
	}
});

$klein->respond('GET', '/users', function ($request, $response, $service) use ($klein) {
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

	$users = ORM::for_table('user')->order_by_desc('followers')->where('banned',0)->limit(100)->select('user_id')->select('user_name')->select('followers')->find_array();

	if(!empty($users)) {
		$inusers = array();
		foreach($users as $key => $user) {
			$inusers[] = $user['user_id'];
		}
		$inusers = implode(',', $inusers);

		// $time = time();
		$result = ORM::for_table('user_log')->raw_query('
			SELECT ul.user_id,ul.posts,ul.followers,ul.follows,ul2.posts AS yposts,ul2.followers AS yfollowers,ul2.follows AS yfollows 
			FROM user_log AS ul 
			LEFT JOIN user_log AS ul2 ON ul.user_id=ul2.user_id 
				WHERE ul.user_id IN('.$inusers.') AND ul.date = "'.$date.'" AND ul2.date = "'.$yesterdayDate.'" 
			LIMIT 100
				')->find_array();

		foreach($result as $r) {
			foreach($users as $k => $u) {
				if($r['user_id']==$u['user_id']) {
					$users[$k] = $u + $r;
				}
			}
		}

		$pagination = [
			1 => 'active',
			2,3,4,5,6,7,8,9,10
		];

		$service->metaKeywords = 'Самые популярные пользователи инстаграм Ульяновска';
		$service->metaTitle = 'Самые популярные пользователи инстаграм Ульяновска #'.$service->hashTag;
		$service->pageTitle = 'Самые популярные пользователи инстаграм Ульяновска #'.$service->hashTag;
		$service->siteName = 'Самые популярные пользователи';

		$service->items = $users;
		$service->pagination = $pagination;
		$service->yesterDayFlag = $yesterDayFlag;
		$service->partUrl = '/users/';
		$service->render($service->templatePath.'views/users.php');
	}
	else {
		$response->code('404');
		$service->metaTitle = "404";
		$service->render($service->templatePath.'views/error.php');
	}
});

$klein->respond('GET', '/users/[*:page]', function ($request, $response, $service) use ($klein) {
	$flag = false;
	if(filter_var($request->page,FILTER_VALIDATE_INT,array("min_range"=>1))) {
		$offset = $request->page-1;
		if($offset==0) {
			header('Location: /users');
			exit;
		}
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
		// $count = ORM::for_table('user')->order_by_desc('followers')->where('banned',0)->offset(100*$offset)->limit(900)->find_many();
		$count = ORM::for_table('user')->raw_query('SELECT COUNT(*) AS count FROM user WHERE banned = 0')->find_array();
		$count = ceil(intval($count[0]['count'])/100);
		if($offset>0) {
			$pagination = [$offset,'active'];
		}
		else {
			$pagination = ['active'];
		}
		$k=0;
		for($i=$offset;$i<=$count;$i++) {
			if($k>9) 
				break;
			$pagination[] = $i+2;
			$k++;
		}
		$users = ORM::for_table('user')->order_by_desc('followers')->where('banned',0)->offset(100*$offset)->limit(100)->select('user_id')->select('user_name')->select('followers')->find_array();

		if(!empty($users)) {
			$inusers = array();
			foreach($users as $key => $user) {
				$inusers[] = $user['user_id'];
			}
			$inusers = implode(',', $inusers);
			$result = ORM::for_table('user_log')->raw_query('
				SELECT ul.user_id,ul.posts,ul.followers,ul.follows,ul2.posts AS yposts,ul2.followers AS yfollowers,ul2.follows AS yfollows 
				FROM user_log AS ul 
				LEFT JOIN user_log AS ul2 ON ul.user_id=ul2.user_id 
					WHERE ul.user_id IN('.$inusers.') AND ul.date = "'.$date.'" AND ul2.date = "'.$yesterdayDate.'" 
				LIMIT 100
					')->find_array();

			foreach($result as $r) {
				foreach($users as $k => $u) {
					if($r['user_id']==$u['user_id']) {
						// $users[$k] = array_merge($u,$r);
						$users[$k] = $u + $r;
					}
				}
			}

			$service->metaKeywords = 'Самые популярные пользователи инстаграм Ульяновска страница '.$request->page;
			$service->metaTitle = 'Самые популярные пользователи инстаграм Ульяновска #'.$service->hashTag.' страница '.$request->page;
			$service->pageTitle = 'Самые популярные пользователи инстаграм Ульяновска #'.$service->hashTag.' страница '.$request->page;
			$service->siteName = 'Самые популярные пользователи';
			$service->items = $users;
			$service->pagination = $pagination;
			$service->yesterDayFlag = $yesterDayFlag;
			$service->partUrl = '/users/';
			$service->page = $request->page;
			$service->render($service->templatePath.'views/users.php');
			$flag = true;
		}
	}
	if(!$flag) {
		$response->code('404');
		$service->metaTitle = "404";
		$service->render($service->templatePath.'views/error.php');
	}
});

$klein->respond('GET', '/user-search/[*:username]', function ($request, $response, $service) use ($klein) {
	$flag = false;

	$service->partUrl = '/users/';
	if(preg_match("/^[A-z0-9+-_]+$/", $request->username) == 1) {
		$username = strtolower($request->username);
		
		$user = ORM::for_table('user')->where('user_name',$username)->find_one();

		if(is_object($user)) {
			$followers = $user->followers;
			$result = ORM::for_table('user')->where('banned',0)->where_gte('followers',$followers)->order_by_desc('followers')->select('user_id')->find_array();

			$number = 0;
			foreach($result as $k => $r) {
				if($r['user_id']==$user->user_id) {
					$number = $k+1;
					break;
				}
			}
			$page = ceil($number/100);

			if($page>0) {
				$service->metaKeywords = 'Пользователь '.$username.' найден!';
				$service->metaTitle = 'Пользователь '.$username.' найден!';
				$service->pageTitle = 'Пользователь '.$username.' найден!';
				$service->siteName = 'Поиск пользователя';
				$service->page = $page;
				$service->render($service->templatePath.'views/user-search.php');
				$flag = true;
			}
		}
	}

	if(!$flag) {
		$response->code('404');
		$service->metaTitle = "404";
		$service->render($service->templatePath.'views/error.php');
	}
});

$klein->respond('GET', '/user-detail/[*:username]', function ($request, $response, $service) use ($klein) {
	$flag = false;
	$service->partUrl = '/users/';
	if(preg_match("/^[A-z0-9+-_]+$/", $request->username) == 1) {

		$username = strtolower($request->username);
		
		$user = ORM::for_table('user')->where('user_name',$username)->find_one();

		if(is_object($user)) {
			$result = ORM::for_table('user_log')->where('user_id',$user->user_id)->order_by_desc('date')->limit(30)->find_many();

			if(!empty($result)) {
				$service->metaKeywords = 'Статистика пользователя '.$username;
				$service->metaTitle = 'Статистика пользователя '.$username;
				$service->pageTitle = 'Статистика пользователя '.$username;
				$service->siteName = 'Статистика пользователя '.$username;
				$service->username = $username;
				$service->result = $result;
				$service->render($service->templatePath.'views/user-detail.php');
				$flag = true;
			}
		}
	}

	if(!$flag) {
		$response->code('404');
		$service->metaTitle = "404";
		$service->render($service->templatePath.'views/error.php');
	}
});

$klein->respond('GET', '/about', function ($request, $response, $service) use ($klein) {
	$service->pageTitle = 'О проекте';
	$service->render($service->templatePath.'views/page.php');
});

$klein->dispatch();
?>