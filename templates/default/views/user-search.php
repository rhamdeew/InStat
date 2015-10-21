		<div class="row">
			<div class="col-xs-12 col-md-8 col-lg-6 col-lg-offset-3 page-container">
				<h1>Поиск пользователя</h1>
				<h2>Пользователь <?php echo $this->escape($this->username); ?> найден!</h2>
				<div class="links">
					<a class="info" href="<?php echo $this->partUrl.$this->page?>">Пользователь на странице <?php echo $this->page; ?></a>
				</div>
				<div class="links">
					<a class="info" href="<?php echo $this->usernameLink?>">Перейти на страницу <?php echo $this->username; ?></a>
				</div>
			</div>
		</div>
