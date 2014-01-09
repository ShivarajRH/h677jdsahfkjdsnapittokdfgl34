<?php 
$boarder_sess=$this->session->userdata("boarder");
$user=$this->session->userdata("user");
$feed=$board=$tag=$like=$flwr=$flw=false;
if(isset($boards)) $board=true;
if(isset($tags)) $tag=true;
if(isset($feeds)) $feed=true;
if(isset($loves)){ $like=true; $tags=$loves;}
if(isset($followers)) $flwr=true;
if(isset($following)) {$followers=$following;$flw=true;}
?>
<div class="disc_bg">
<div class="container">

	<div class="d_sidepanel">
		<h1><?=$boarder['name']?></h1>
		<div style="font-size:98%;padding:5px 0px;"><b><?=$boarder['followers']?></b> followers, <b><?=$boarder['following']?></b> following</div>
		<div align="center"><img src="<?=IMAGES_URL?>people/<?=$boarder['pic']?>.jpg"></div>
		<div style="margin:5px 0px;" align="center">
<?php if($user && $boarder_sess['username']!=$boarder['username']){?>		
<?php if($user){
	if($this->db->query("select 1 from king_boarder_followers where userid=? and follower=?",array($boarder['userid'],$user['userid']))->num_rows()==0){?>
		<form action="<?=site_url("discovery/follow_boarder")?>" method="post">
		<input type="hidden" name="url" value="<?=$boarder['username']?>"> 
		<input type="image" src="<?=IMAGES_URL?>follow_user.png">
		</form>
<?php }else{?>
		<input type="image" src="<?=IMAGES_URL?>following.png">
<?php }?>
<?php }else{?>
		<input type="image" src="<?=IMAGES_URL?>follow_user.png" onclick='alert("Please login to follow this board");make_rem_redir();'>
<?php }?>
<?php }?>
		</div>
<?php if($boarder['facebook']!="" || $boarder['twitter']!="" || $boarder['linkedin']!=""){?>		
		<div class="d_user_social_links">
<?php if($boarder['facebook']!=""){?><a href="<?=$boarder['facebook']?>"><img src="<?=IMAGES_URL?>facebook.png" width="20"></a><?php }?>
<?php if($boarder['twitter']!=""){?><a href="<?=$boarder['twitter']?>"><img src="<?=IMAGES_URL?>twitter.png" width="20"></a><?php }?>
<?php if($boarder['linkedin']!=""){?><a href="<?=$boarder['linkedin']?>"><img src="<?=IMAGES_URL?>linkedin.png" width="20"></a><?php }?>
		</div>
<?php }?>		
		<div class="d_acts">
			<?=$activity?>
		</div>
	</div>
	
	<div class="d_rightpanel">
		<div class="d_user_head">
<?php if($user['userid']==$boarder['userid']){?>		
		<?=$feed?'<span>':'<a href="'.site_url("discovery/user/{$boarder['username']}/feed").'">'?><b>Feed</b><?=$feed?'</span>':'</a>'?>
<?php }?>
		<?=$board?'<span>':'<a href="'.site_url("discovery/user/{$boarder['username']}").'">'?><?=$boarder['boards']?> Boards<?=$board?'</span>':'</a>'?>
		<?=$tag?'<span>':'<a href="'.site_url("discovery/user/{$boarder['username']}/tags").'">'?><?=$boarder['tags']?> Tags<?=$tag?'</span>':'</a>'?>
		<?=$like?'<span>':'<a href="'.site_url("discovery/user/{$boarder['username']}/loves").'">'?><?=$boarder['loves']?> Loves<?=$like?'</span>':'</a>'?>
		<?=$flwr?'<span>':'<a href="'.site_url("discovery/user/{$boarder['username']}/followers").'">'?><?=$boarder['followers']?> Followers<?=$flwr?'</span>':'</a>'?>
		<?=$flw?'<span>':'<a href="'.site_url("discovery/user/{$boarder['username']}/following").'">'?><?=$boarder['following']?> Following<?=$flw?'</span>':'</a>'?>
		</div>

<ul class="disc_cont <?php if(isset($tags)){?>disc_tags_cont<?php }?>">		
<?php if(isset($boards)){
	foreach($boards as $b){
?>
		<li>
			<div class="d_board">
				<h3><?=$b['name']?></h3>
				<a href="<?=site_url("discovery/board/{$b['url']}")?>">
					<?php foreach($b['imgs'] as $pic){?>
					<span class="img" style="background:url('<?=IMAGES_URL?>tags/thumb/<?=$pic?>.jpg') no-repeat center center"></span>
					<?php }?>
				</a>
				<div class="clear"></div>
			</div>
		</li>
<?php } }?>
<?php if(isset($followers)){
	foreach($followers as $f){?>
	<li style="background:#fff;border:1px solid #AAAAAA;">
	<table width="100%">
	<tr>
	<td width="50"><a href="<?=site_url("discovery/user/".$f['username'])?>"><img src="<?=IMAGES_URL?>people/<?=$f['pic']?>_t.jpg"></a></td>
	<td align="center"><a href="<?=site_url("discovery/user/".$f['username'])?>" class="redlink"><h2 style="font-weight:300;"><?=$f['name']?></h2></a></td>
	</tr>
	</table>
	</li>
<?php } }?>
<?php if(isset($bfollowing)){
	foreach($bfollowing as $f){?>
	<li style="background:#fff;border:1px solid #AAAAAA;">
	<table width="100%">
	<tr>
	<td width="50"><a href="<?=site_url("discovery/board/".$f['url'])?>"><img src="<?=IMAGES_URL?>people/<?=$f['pic']?>_t.jpg"></a></td>
	<td align="center"><a href="<?=site_url("discovery/board/".$f['url'])?>" class="redlink"><h2 style="font-weight:300;"><?=$f['name']?></h2></a></td>
	</tr>
	</table>
	</li>
<?php } }?>
<?php if(isset($feeds)){?>
<li class="feed" style="background:#fff;margin-left:20px;padding:5px;width:96%;font-size:120%;">
<div class="d_acts" style="padding-left:20px;margin-right:5px;">
<?=empty($feeds)?"<h3 style='padding:10px;'>Start following people to get their feed!</h3>":$feeds?>
</div>
</li>
<style>
.feed .d_acts{
border:0px;
}
.feed .d_acts div{
padding:5px;
padding-top:15px;
margin:3px;
background:#fafafa;
border-bottom:1px solid #ccc;
}
.feed .d_acts div .img{
margin-top:-10px;
padding-right:7px;
}
</style>
<?php }?>
<?php if(isset($tags) || isset($loves)){
	$this->load->view("body/discovery/subs/sub_tags",array("tags"=>$tags,"cols"=>3));
?>
<style>
ul.disc_tags_cont li{
width:210px !important;
margin-left:15px !important; 
}
.d_s_tag a.img{
margin:15px 0px 0px 15px !important;
}
</style>
<?php }?>
</ul>
		
	</div>

<div class="clear"></div>
</div>
</div>
<?php
