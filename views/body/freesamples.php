<?php 
$selecteds=$this->session->userdata("fsselected");
if(!$selecteds)	$selecteds=array();
if($fsconfig['limit']<count($selecteds))
{
	$s=array();
	for($i=0;$i<$fsconfig['limit'];$i++)
		$s[]=$selecteds[$i];
	$selecteds=$s;
	$this->session->userdata("fsselected",$s);
}
?>
<div style="width:980px;padding:10px;">

<div style="float:right">
<h3 style="margin-top:5px;">Your cart value is Rs <?=$this->uri->segment(2)?>
<span style="margin-left:50px;">
You can select upto <?=$fsconfig['limit']?> free samples
</span>
</h3>
</div>

<h2>You are eligible for the following free samples</h2>

<div class="clear"></div>

<form id="fsform">
<div class="fav_cats" style="margin:10px 0px;">
<?php foreach($fss as $i=>$fs){?>
<div class="cat <?=$i<3?"top":""?> <?=($i%3==0)?"left":""?> fs<?=$fs['id']?> <?php if(in_array($fs['id'], $selecteds)){?>selected<?php }?>">
	<input type="checkbox" name="fsids[]" style="display:none;" class="fschecks" value="<?=$fs['id']?>">
	<div class="img" style="background:url(<?=IMAGES_URL?>items/small/<?=$fs['pic']?>.jpg) no-repeat;">
		&nbsp;
	</div>
	<div>
		<span class="title"><?=$fs['name']?></span>
		<span class="link"><?php if(in_array($fs['id'],$selecteds)){?>SELECTED<?php }else{?>Select this<?php }?></span>
	</div>
</div>
<?php }?>
<div class="clear"></div>
</div>
<div>
<input type="image" src="<?=IMAGES_URL?>submit.png">
</div>
</form>

</div>

<style>
#fsform .fav_cats{
max-height:400px;
overflow:auto;
}
#fsform .fav_cats .cat{
cursor:pointer;
}
#fsform .fav_cats .selected{
background:#eee;
}
</style>

<script>
maxfs=<?=$fsconfig['limit']?>;
$(".fschecks").attr("checked",false);
<?php foreach($selecteds as $s){?>
$(".fs<?=$s?> .fschecks").attr("checked",true);
<?php }?>
$("#fsform .fav_cats .cat").click(function(){
		if($(".fschecks:checked").length>=maxfs)
		{
			alert("you can select only maximum of "+maxfs+" free samples");
			return;
		}
		o=$(".fschecks",$(this));
		if(o.attr("checked"))
		{
			$(".link",$(this)).html("Select this");
			o.attr("checked",false);
			$(this).removeClass("selected");
		}
		else
		{
			$(this).addClass("selected");
			$(".link",$(this)).html("SELECTED");
			o.attr("checked",true);
		}	
});

$("#fsform").submit(function(){
	$.fancybox.showActivity();
	$.post("<?=site_url("jx_savefs")?>",$(this).serialize(),function(){
		$.fancybox.hideActivity();
		fssaved();
	});
	return false;
});

</script>


<?php
