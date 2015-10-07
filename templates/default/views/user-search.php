		<div class="row">
			<div class="col-xs-12 col-md-8 col-lg-6 col-lg-offset-3 page-container">
			<h1><?= $this->escape($this->siteName); ?></h1>
			<h2><?= $this->escape($this->pageTitle); ?></h2>
			<h2>
				<a class="info" href="<?php echo $this->partUrl.$this->page?>">Пользователь на странице <?php echo $this->page; ?></a>
			</h2>
			</div>
		</div>