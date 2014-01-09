<div class="item">
<div class="brdrcont">
	<div class="pic">
		<a href="<?=site_url($f['url'])?>"><img src="<?=IMAGES_URL?>items/small/<?=$f['pic']?>.jpg"></a>
	</div>
	<div class="name"><h4><?=$f['name']?></h4></div>
	<div class="scbutts">
		<div class="cont">
			<div class="fb">
				<iframe src="//www.facebook.com/plugins/like.php?href=<?=site_url("discovery/tag/{$f['url']}")?>&amp;send=false&amp;layout=button_count&amp;width=50&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:50px; height:21px;" allowTransparency="true"></iframe>
			</div>
			<g:plusone annotation="none" size="medium" href="<?=site_url($f['url'])?>" width="60"></g:plusone>
			<iframe allowtransparency="true" frameborder="0" scrolling="no"
            src="//platform.twitter.com/widgets/tweet_button.html?url=<?=urlencode(site_url($f['url']))?>&count=none"
            style="width:60px; height:20px;"></iframe>
		</div>
	</div>
	
	<div class="timecont">
		<img src="<?=IMAGES_URL?>chrono.png">
		<div class="time" title="<?=date("c",$f['time'])?>">a minute ago</div>
	</div>
	
</div>
</div>
<?php
