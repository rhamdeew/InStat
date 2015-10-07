<?php
	$me = [
	"https://instagram.com/p/4v1K3Rp5sa/",
	"https://instagram.com/p/4sNL4mJ5mI/",
	"https://instagram.com/p/4KGtXCp5pO/",
	"https://instagram.com/p/37P5pmp5s-/",
	"https://instagram.com/p/3JPe-SJ5k4/",
	"https://instagram.com/p/2_YFVCJ5vP/",
	"https://instagram.com/p/2qQoqgp5r0/",
	"https://instagram.com/p/2ls8G6p5rB/",
	];
	$key = rand(0,count($me)-1);
?>
		<div class="row">
			<div class="col-xs-12 col-md-8 col-lg-6 col-lg-offset-3 page-container">

				<h1>Какая досада!</h1>
				<p>К сожалению такой страницы нет :(</p>
				<p>Здесь есть только <a href="/">ссылка на главную</a> и я:</p>
	
				<div id="instagram-wrapper"></div>
			</div>
		</div>
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
	<script src="http://api.instagram.com/oembed?url=<?php echo $me[$key]?>&callback=callMe"></script>