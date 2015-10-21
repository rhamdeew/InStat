		<div class="row">
			<div class="col-xs-12 col-md-8 col-lg-6 col-lg-offset-3 page-container">
			<h1>Статистика пользователя @<?= $this->username; ?></h1>

			<div id="instagram-wrapper"></div>
			<script>
			function callMe(resp) {
				if(resp.html.length>0) {
					var newDiv = document.createElement('div')
					newDiv.innerHTML = resp.html;
					document.getElementById('instagram-wrapper').appendChild(newDiv);
				}
			}
			</script>
			<script src="http://api.instagram.com/oembed?url=<?php echo $this->photo->link?>&callback=callMe"></script>

			<script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script><div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="small" data-yashareQuickServices="vkontakte,facebook,twitter,odnoklassniki,gplus" data-yashareTheme="counter"></div>
			<table class="table">
			<tr class="warning">
				<td>дата</td>
				<td>фотографии</td>
				<td>подписчики</td>
				<td>подписки</td>
			</tr>
			<?php $this->result = array_reverse($this->result);?>
			<?php if(count($this->result>1)):?>
			<?php foreach($this->result as $key => $item):?>
			<?php
			$postDiffHtml = '--';
			$followersDiffHtml = '--';
			$followsDiffHtml = '--';

			if($key>0) {
				if($item->posts>$posts) {
					$postDiffHtml = '<span class="text-success">+'.($item->posts-$posts).'</span>';
				}
				elseif($item->posts<$posts) {
					$postDiffHtml = '<span class="text-danger">-'.($posts-$item->posts).'</span>';
				}

				if($item->followers>$followers) {
					$followersDiffHtml = '<span class="text-success">+'.($item->followers-$followers).'</span>';
				}
				elseif($item->followers<$followers) {
					$followersDiffHtml = '<span class="text-danger">-'.($followers-$item->followers).'</span>';
				}
				if($item->follows>$follows) {
					$followsDiffHtml = '<span class="text-success">+'.($item->follows-$follows).'</span>';
				}
				elseif($item->follows<$follows) {
					$followsDiffHtml = '<span class="text-danger">-'.($follows-$item->follows).'</span>';
				}
			}
			$posts = $item->posts;
			$followers = $item->followers;
			$follows = $item->follows;
			?>
			<tr>
				<td><?php echo $item->date; ?></td>
				<td><?php echo $item->posts.'   '.$postDiffHtml;?></td>
				<td><?php echo $item->followers.'   '.$followersDiffHtml;?></td>
				<td><?php echo $item->follows.'   '.$followsDiffHtml;?></td>
			</tr>

			<?php endforeach; ?>
			<?php else:?>
			<?php endif;?>
			</table>
			</div>
		</div>
