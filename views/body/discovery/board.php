<?php $user=$this->session->userdata("user"); ?>
<div class="disc_bg">

<div class="container" style="padding:10px 0px;">

<div align="center" style="padding:20px 100px;">
	<h1><?=ucfirst($board['name'])?></h1>
	<div align="center" style="padding:10px;margin:10px;border:2px solid #aaa;border-width:2px 0px;" align="left">
		<div style="float:left">
			<img src="<?=IMAGES_URL?>people/<?=$board['userpic']?>_t.jpg" style="width:30px;float:left;margin-right:15px;margin-top:-7px;"> <a href="<?=site_url("discovery/user/{$board['username']}")?>" class="redlink" style="font-weight:bold"><?=$board['user']?></a>
		</div>
		<div style="float:right">
<?php if($board['public']){?>
			<a href="javascript:void(0)" onclick='addtagtoboard("<?=$board['bid']?>","<?=$board['name']?>")' class="redlink">Add a tag</a>,
<?php }?>
			<a href="<?=site_url("discovery/board/{$board['url']}/followers")?>" class="redlink"><b><?=$board['followers']?></b> followers</a>, <b><?=$board['tags']?></b> tags
		</div>
		<div style="margin:-5px 0px;">
<?php if($user){
	if($this->db->query("select 1 from king_board_followers where bid=? and follower=?",array($board['bid'],$user['userid']))->num_rows()==0){?>
		<form action="<?=site_url("discovery/follow_board")?>" method="post">
		<input type="hidden" name="url" value="<?=$board['url']?>"> 
		<input type="image" src="<?=IMAGES_URL?>follow_board.png">
		</form>
<?php }else{?>
		<input type="image" src="<?=IMAGES_URL?>following.png">
<?php }?>
<?php }else{?>
		<input type="image" src="<?=IMAGES_URL?>follow_board.png" onclick='alert("Please login to follow this board");make_rem_redir();'>
<?php }?>
		</div>
	</div>
</div>	

<?php if(isset($followers)){?>
	<div style="margin-top:50px;margin:20px 100px;background:#fff;">
		<?php foreach($followers as $f){?>
			<div class="d_follower">
				<table width="100%" cellpadding=5>
					<tr>
						<td width="50"><a href="<?=site_url("discovery/user/{$f['username']}")?>"><img src="<?=IMAGES_URL?>people/<?=$f['pic']?>_t.jpg"></a></td>
						<td><a href="<?=site_url("discovery/user/{$f['username']}")?>" class="redlink"><h3><?=$f['name']?></h3></a>
					</tr>
				</table>
			</div>
		<?php }?>
	</div>

<?php }else{?>
<ul class="disc_cont disc_tags_cont">
<?php 
	$this->load->view("body/discovery/subs/sub_tags",array("cols"=>5,"tags"=>$board['tag_data']));
?>
</ul>
<?php }?>

<div class="clear"></div>

</div>

</div>
<?php
