		<div class="row">
			<div class="col-xs-12 col-md-8 col-lg-6 col-lg-offset-3 page-container">
			<h1>Самые популярные пользователи #<?= $this->hashTag; ?></h1>

			<?php if(isset($this->page)): ?>
				<h2>Страница: <?php echo $this->page; ?></h2>
			<?php endif; ?>
			<?php if($this->yesterDayFlag): ?>
				<h2>Результаты за вчерашний день. Обновление в 8:00</h2>
			<?php endif; ?>

			<form action="" id="user-search">
				<input type="text" id="user-search-username">
				<button id="user-search-button">Найти себя</button>
			</form>
			<br/>
			<script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script><div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="small" data-yashareQuickServices="vkontakte,facebook,twitter,odnoklassniki,gplus" data-yashareTheme="counter"></div>
			<table class="table">
			<tr class="warning">
				<td>username</td>
				<td>фотографии</td>
				<td>подписчики</td>
				<td>подписки</td>
			</tr>
			<?php foreach($this->items as $item):?>
			<?php
			$postDiffHtml = '';
			$followsDiffHtml = '';
			$followersDiffHtml = '';

			if(isset($item['yposts']) && isset($item['posts'])) {
				if($item['yposts']>0) {
					if($item['yposts']>$item['posts']) {
						$postDiffHtml = '<span class="text-success">+'.($item['yposts']-$item['posts']).'</span>';
					}
					elseif($item['yposts']<$item['posts']) {
						$postDiffHtml = '<span class="text-danger">'.($item['yposts']-$item['posts']).'</span>';
					}
					else {
						$postDiffHtml = '<span class="text-muted">--</span>';
					}
				}
			}
			if(isset($item['yfollows']) && isset($item['follows'])) {
				if($item['yfollows']>0) {
					if($item['yfollows']>$item['follows']) {
						$followsDiffHtml = '<span class="text-success">+'.($item['yfollows']-$item['follows']).'</span>';
					}
					elseif($item['yfollows']<$item['follows']) {
						$followsDiffHtml = '<span class="text-danger">'.($item['yfollows']-$item['follows']).'</span>';
					}
					else {
						$followsDiffHtml = '<span class="text-muted">--</span>';
					}
				}

			}
			if(isset($item['yfollowers']) && isset($item['followers'])) {
				if($item['yfollowers']>0) {
					if($item['yfollowers']>$item['followers']) {
						$followersDiffHtml = '<span class="text-success">+'.($item['yfollowers']-$item['followers']).'</span>';
					}
					elseif($item['yfollowers']<$item['followers']) {
						$followersDiffHtml = '<span class="text-danger">'.($item['yfollowers']-$item['followers']).'</span>';
					}
					else {
						$followersDiffHtml = '<span class="text-muted">--</span>';
					}
				}

			}
			?>
			<tr>
				<td class="username"><a href="/user-detail/<?php echo $item['user_name']?>"><?php echo $item['user_name']; ?></a></td>
				<td><?php if(isset($item['posts'])) echo ($item['posts']>0) ? $item['posts'] : ''; echo '   '.$postDiffHtml;?></td>
				<td><?php if(isset($item['followers'])) echo ($item['followers']>0) ? $item['followers'] : ''; echo '   '.$followersDiffHtml;?></td>
				<td><?php if(isset($item['follows'])) echo ($item['follows']>0) ? $item['follows'] : ''; echo '   '.$followsDiffHtml;?></td>
			</tr>
			<?php endforeach; ?>
			</table>
			<?php if(isset($this->pagination) && isset($this->partUrl)): ?>
			<nav>
			  <ul class="pagination">
				<?php foreach($this->pagination as $page_val): ?>
					<?php if($page_val=='active'): ?>
					<li class="active">
						<?php if(isset($this->page)): ?>
						<a href="<?php echo $this->partUrl.$this->page;?>"><?php echo $this->page; ?></a>
						<?php else: ?>
						<a href="<?php echo $this->partUrl.'1';?>">1</a>
						<?php endif; ?>
					</li>
					<?php else: ?>
					<li>
						<a href="<?php echo $this->partUrl.$page_val;?>"><?php echo $page_val; ?></a>
					</li>
					<?php endif; ?>
				<?php endforeach;?>
			  </ul>
			</nav>
			<?php endif; ?>
			</div>
		</div>
	<script>
	$(document).ready(function() {
		$('#user-search-button').on('click',function() {
			window.location = '/user-search/'+$('#user-search-username').val();
			return false;
		})
	})
	</script>
