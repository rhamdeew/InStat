		<div class="row">
			<div class="col-xs-12 col-md-8 col-lg-6 col-lg-offset-3 page-container">
			<h1><?php echo $this->escape($this->siteName); ?></h1>
			<h2><?php echo $this->escape($this->pageTitle); ?></h2>

			<?php if(isset($this->page)): ?>
				<h2>Страница: <?php echo $this->page; ?></h2>
			<?php endif; ?>
			<div id="instagram-wrapper"></div>
			<script>
			function callMe(resp) {
				if(resp.html.length>0) {
					var newDiv = document.createElement('div')

					newDiv.innerHTML = resp.html;
					// var wrapper = document.getElementById('instagram-wrapper');
					document.getElementById('instagram-wrapper').appendChild(newDiv);
				}
			}
			</script>
			<?php foreach($this->items as $item): ?>
				<script src="http://api.instagram.com/oembed?url=<?php echo $item->link?>&callback=callMe"></script>
			<?php endforeach; ?>
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