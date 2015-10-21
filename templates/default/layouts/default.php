<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<?php
	switch($this->code) {
		case 'userdetail':
			$metaTitle = 'Статистика пользователя @'.$this->username;
			$description = 'Сервис анализа локальных сегментов Instagram. Хэштег #'.$this->hashTag.'. '.$metaTitle;
			$keywords = 'Instagram, статистика, город Ульяновск, @'.$this->username;
			break;
		case 'usersearch':
			$metaTitle = 'Поиск пользователя @'.$this->username;
			$description = 'Сервис анализа локальных сегментов Instagram. Хэштег #'.$this->hashTag.'. '.$metaTitle;
			$keywords = 'Instagram, статистика, город Ульяновск, @'.$this->username;
			break;
		case 'users':
			$suffix = isset($this->page) ? ' страница '.$this->page : '.';
			$metaTitle = 'Самые популярные пользователи #'.$this->hashTag.$suffix;
			$description = 'Сервис анализа локальных сегментов Instagram. Хэштег #'.$this->hashTag.'. '.$metaTitle;
			$keywords = 'Instagram, статистика, город Ульяновск, самые популярные пользователи, #'.$this->hashTag;
			break;
		case 'about':
			$metaTitle = 'О проекте';
			$description = $metaTitle.'. Сервис анализа локальных сегментов Instagram. Хэштег #'.$this->hashTag.'. ';
			$keywords = 'Instagram, статистика, город Ульяновск, самые популярные пользователи, #'.$this->hashTag;
			break;
		case 'notpopular':
			$suffix = isset($this->page) ? ' страница '.$this->page : '.';
			$metaTitle = 'Самые непопулярные инстаграмы Ульяновска #'.$this->hashTag.$suffix;
			$description = $metaTitle;
			$keywords = 'Instagram, статистика, город Ульяновск, самые непопулярные фотографии, #'.$this->hashTag;
			break;
		case 'oldest':
			$suffix = isset($this->page) ? ' страница '.$this->page : '.';
			$metaTitle = 'Самые старые инстаграмы Ульяновска #'.$this->hashTag.$suffix;
			$description = $metaTitle;
			$keywords = 'Instagram, статистика, город Ульяновск, самые старые фотографии, #'.$this->hashTag;
			break;
		case 'bestever':
			$suffix = isset($this->page) ? ' страница '.$this->page : '.';
			$metaTitle = 'Лучшие инстаграмы Ульяновска за все время #'.$this->hashTag.$suffix;
			$description = $metaTitle;
			$keywords = 'Instagram, статистика, город Ульяновск, лучшие инстаграмы, #'.$this->hashTag;
			break;
		case 'topweek':
			$suffix = isset($this->page) ? ' страница '.$this->page : '.';
			$metaTitle = 'Лучшие инстаграмы Ульяновска за неделю #'.$this->hashTag.$suffix;
			$description = $metaTitle;
			$keywords = 'Instagram, статистика, город Ульяновск, лучшие инстаграмы недели, #'.$this->hashTag;
			break;
		case 'index':
			$suffix = isset($this->page) ? ' страница '.$this->page : '.';
			$metaTitle = 'Топ-10 фотографий за сутки #'.$this->hashTag.$suffix;
			$description = $metaTitle;
			$keywords = 'Instagram, статистика, город Ульяновск, лучшие инстаграмы за сутки, #'.$this->hashTag;
			break;
		default:
			$metaTitle = $this->hashTag;
			$description = $metaTitle;
			$keywords = 'Instagram, статистика, город Ульяновск, лучшие инстаграмы, #'.$this->hashTag;
			break;
	}
	?>

	<title><?= $metaTitle; ?></title>
	<link rel="stylesheet" href="/css/bootstrap.min.css">
	<link rel="stylesheet" href="/<?= $this->templatePath?>css/style.css">
	<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	<meta name="description" content="<?= $description; ?>">
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?= $metaTitle; ?>">
	<meta property="og:url" content="<?= $this->siteURL; ?>">
	<meta property="og:site_name" content="Insta#ULSK">
	<meta property="og:image" content="<?= $description; ?>/logo.png" />
	<meta property="og:description" content="<?= $description; ?>">
	<meta name="twitter:card" content="summary">
	<meta name="twitter:title" content="<?= $this->siteName; ?>">
	<meta name="twitter:description" content="<?= $description; ?>">
	<meta name="twitter:creator" content="@rhamdeew">
	<meta keywords="<?= $keywords; ?>">
</head>
<body>
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
			<span class="sr-only">Свернуть навигацию</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			</button>
		</div>

		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
			<li><a href="/">Top-10 за сутки</a></li>
			<li><a href="/topweek">Top-10 за неделю</a></li>
			<li><a href="/best">Лучшее за все времена</a></li>
			<li><a href="/oldest">Самые старые</a></li>
			<li><a href="/not-popular">Самые непопулярные</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
			<li><a href="/users">Пользователи</a></li>
			<!-- <li><a href="/statistic">Статистика</a></li> -->
			<li><a href="/about">О проекте</a></li>
			</ul>
		</div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>
	<div class="container-fluid">
		<?= $this->yieldView(); ?>
	</div>
	<script async="" defer="" src="//platform.instagram.com/en_US/embeds.js"></script>
	<script async="" defer="" src="/js/bootstrap.min.js"></script>

<!--Счетчик тут-->
<?php if(defined('PRODUCTION')): ?>
<!-- Yandex.Metrika counter -->
<!-- /Yandex.Metrika counter -->
<?php endif;?>
</body>
</html>
