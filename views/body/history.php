<div class="container histcont">

<h1 style="padding:10px 0px 5px 0px;">Your Recently Viewed Products</h1>

<div class="history">

<?php if(empty($history)){?>
No products recently viewed
<?php }else{
foreach($history as $rv){?>
<a href="<?=site_url($rv['url'])?>">
<div class="close" id="<?=$rv['id']?>"><img src="<?=IMAGES_URL?>close.png"></div>
<img src="<?=IMAGES_URL?>items/small/<?=$rv['pic']?>.jpg" title="<?=htmlspecialchars($rv['name'])?>" width=100>
</a>	
<?php } }?>


</div>

<div class="clear"></div>

</div>

<script>
$(function(){
	$(".histcont .history a").hover(function(){
		$(".close",$(this)).show();
	},function(){
		$(".close",$(this)).hide();
	});
	$(".histcont .history a .close").click(function(e){
		$.post("<?=site_url("history")?>",{id:$(this).attr("id")},function(){});
		$(this).parent().remove();
		e.stopPropagation();
		return false;
	});
});
</script>

<style>
.histcont{
min-height:300px;
}
.history .close{
margin-top:-7px;
margin-left:93px;
position:absolute;
display:none;
}
.histcont .history a{
	float:left;
	margin:5px;
	border:1px solid #ccc;
	padding:1px;
	background:#fff;
	height:90px;
	max-height:90px;
	overflow:hidden;
}
.histcont .history a:hover{
	border:1px solid #aaa;
}

</style>

<?php
