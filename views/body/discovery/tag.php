<?php $user=$this->session->userdata("user");
$boarder=$this->session->userdata("boarder");
?>
<div class="disc_bg">
<div class="container">

<div style="float:right">

<iframe src="//www.facebook.com/plugins/like.php?href=<?=site_url("discovery/tag/{$tag['url']}")?>&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>

<g:plusone size="medium"></g:plusone>

<a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>

</div>

<div style="float:left">
	<div class="d_sidepanel">
		Board : <a class="blacklink" href="<?=site_url("discovery/board/{$tag['boardurl']}")?>"><b><?=$tag['board']?></b></a>
	</div>
	<div class="d_sidepanel" style="clear:left;">
		<h4 style="margin:-5px;margin-bottom:5px;">We thought you might love these!</h4>
		<div class="d_relatedeals" align="center">
			<div style="padding:20px;" align="center"><img src="<?=IMAGES_URL?>loading.gif"></div>
		</div>
	</div>
</div>
	
	<div class="d_tag">
		<div class="head">
			<div style="color:#888;float:right;"><b><?=$tag['loves']?></b> Loves &nbsp; &nbsp; &nbsp;<b><?=$tag['retags']?></b> Retags</div>
		<img src="<?=IMAGES_URL?>people/<?=$tag['userpic']?>_t.jpg">
		<h1><a href="<?=site_url("discovery/user/{$tag['username']}")?>"><?=$tag['user']?></a> <span style="font-weight:normal">via</span> 
			<?php if($tag['from']==0){?>
			<?php if($tag['src_url']==""){?>upload<?php }else{?>the web<?php }?>
			<?php }else{?>
				<a href="<?=site_url("discovery/user/{$tag['fromusername']}")?>"><?=$tag['fromuser']?></a>
			<?php }?>
			</h1>
		<div>on <?=date("g:ia d/m/y",$tag['created_on'])?></div>
		<div class="clear"></div>
		</div>
		<div class="cont">
			<div align="left" style="padding:5px 0px">
			<?php if($tag['src_url']!=""){
				preg_match('@^(?:http://)?([^/]+)@i',$tag['src_url'], $matches);
				$host = $matches[1];
			?>
				<a href="<?=$tag['src_url']?>" style="float:right;font-weight:bold;">from <?=$host?></a>			
			<?php }?>
			<img src="<?=IMAGES_URL?>loveit.png" style="cursor:pointer" onclick='loveit("<?=$tag['url']?>")'> <img src="<?=IMAGES_URL?>retagit.png" style="cursor:pointer;" onclick='retag("<?=$tag['pic']?>","<?=$tag['bid']?>")'>
			</div>
			<div class="imgcont" align="center"><?php if($tag['src_url']!=""){?><a href="<?=$tag['src_url']?>"><?php }?><img src="<?=IMAGES_URL?>tags/<?=$tag['pic']?>.jpg"><?php if($tag['src_url']!=""){?></a><?php }?></div>
			<div class="tagname"><?=$tag['name']?></div>
			<?php if($tag['from']!=0){?>
			<div style="color:#888;">Retagged from <a class="blacklink" href="<?=site_url("discovery/board/{$tag['fromboardurl']}")?>"><b><?=$tag['fromboard']?></b></a> by <a class="blacklink" href="<?=site_url("discovery/board/{$tag['fromusername']}")?>"><b><?=$tag['fromuser']?></b></a></div>
			<?php }?>
			
			<?php if(!empty($tag['lovers'])){?>
			<div class="lovers">
				<h3><?=$tag['loves']?> Lovers</h3>
				<ul>
					<?php foreach($tag['lovers'] as $lover){?>
					<li><a href="<?=site_url("discovery/user/{$lover['username']}")?>"><img src="<?=IMAGES_URL?>people/<?=$lover['pic']?>_t.jpg"></a></li>
					<?php }?>
				</ul>
				<div class="clear"></div>
			</div>
			<?php }?>
			
			<?php if(!empty($tag['retagers'])){?>
			<div class="retags" align="center">
				<h3 align="left"><?=$tag['retags']?> Retags</h3>
				<table cellpadding=0 width=600>
					<?php foreach($tag['retagers'] as $i=>$r){?>
					<tr>
					<td width="50"><img src="<?=IMAGES_URL?>people/<?=$r['pic']?>_t.jpg"></td>
					<td><?=$i==0?"Originally ":"Re"?>tagged by <a href="<?=site_url("discovery/user/{$r['username']}")?>" class="redlink"><b><?=$r['user']?></b></a><br>onto <a class="redlink" href="<?=site_url("discovery/board/{$r['boardurl']}")?>"><b><?=$r['board']?></b></a> on <?=date("g:ia d/m/y",$r['created_on'])?> </td>
					</tr>
					<?php }?>
				</table>
				<div class="clear"></div>
			</div>
			<?php }?>
			
			<div class="comment">
<?php foreach($tag['comments_data'] as $c){?>
			<div style="padding:5px;">
			<table width="100%" cellpadding="3">
				<tr>
					<td width=50><a href="<?=site_url("discovery/user/{$c['username']}")?>"><img src="<?=IMAGES_URL?>people/<?=$c['pic']?>_t.jpg"></a></td>
					<td><?=$c['comment']?>				
				</tr>
			</table>
			</div>
<?php }?>
<?php if($user){?>
				<div class="write">
					<table width="100%" cellpadding=2>
						<tr>
							<td width="50" valign="top"><img src="<?=IMAGES_URL?>people/<?=$boarder['pic']?>_t.jpg"></td>
							<td>
							<form action="<?=site_url("discovery/writecomment")?>" method="post">
							<input type="hidden" name="url" value="<?=$tag['url']?>">
							<textarea name="comment" onfocus='$(".sub",$(this).parent()).show();' style="width:95%;height:50px;"></textarea>
							<input type="submit" class="sub" value="Add Comment" style="display:none;">
							</form>
							</td>
						</tr>
					</table>
				</div>
<?php }?>
			</div>
		</div>
	</div>
	
<div class="clear"></div>	
</div>
</div>

<script>
$(function(){
	$.get("<?=site_url("discovery/relatedeals/{$tag['url']}")?>",function(data){
		$(".d_relatedeals").html(data);
	});
});
</script>
<?php

