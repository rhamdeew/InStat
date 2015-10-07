<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title><?= $this->escape($this->metaTitle); ?></title>
	<link rel="stylesheet" href="/css/bootstrap.min.css">
	<link rel="stylesheet" href="/<?= $this->templatePath?>css/style.css">
	<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
	<meta keywords="<?= $this->escape($this->metaKeywords); ?>">
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

</body>
</html>