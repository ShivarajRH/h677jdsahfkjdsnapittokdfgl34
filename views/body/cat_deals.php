<?php  $colors=array("green","violet","lightblue","red","black","darkgreen",'orange');
$sbrands=array();
foreach($dbrands as $bid=>$brand)
	$sbrands[$bid]=$brand['name'];
arsort($sbrands);
?>
<div>
	<div class="container">
<?php /*?>		<div style="padding:0px;margin-bottom:5px;border:0px;background:#fff;" class="homecont">
			<div class="homehead">
				<div class="hctitle">
					<div class="cnt" style="min-width:250px;"><?=ucfirst($pagetitle)?></div>
				</div>
			</div>
		</div>*/ ?>

<?php if(!empty($brands)){?><div class="brands_avail">
		<img src="<?=IMAGES_URL?>brands_available.png" style="margin:10px 0px;">
<?php if(count($brands)>6){?>		
		<marquee style="width:835px;float:right;" behavior="alternate">
<?php }?>
			<?php foreach($brands as $b){?>
				<a href="<?=site_url($b['url'])?>" title="<?=$b['name']?>"><img src="<?=IMAGES_URL?>brands/<?=$b['logoid']?>.jpg" width="120" style="width:120px;max-height:50px;"></a>
			<?php }?>
<?php if(count($brands)>6){?>		
		</marquee>
<?php }?>
	</div><?php }?>
<div class="clear"></div>	

<div class="homecont refine" style="padding-bottom:0px;">
	<div style="display:inline-block">
		<div class="bender bc-black">
			<div class="matter">Refine results</div>
			<span></span>
		</div>
	</div>
		<h3>Brands</h3>
		<div class="search_fltr_box">
		<div class="scont">
	<?php $brandids=array(); foreach($sbrands as $bid=>$brand){ $brandids[]=$bid; ?>
		<div class="refine_but"><div class="xp">+</div><?=$brand?><input type="checkbox" checked="checked" class="ref_brand" value="<?=$bid?>"></div>
	<?php }?>
		</div>
		</div>
	<div class="clear"></div>
</div>
	
	<?php foreach($deals as $bid=>$cdeals){?>
		<div class="homecont brandcont" id="brandcont<?=$bid?>">
			<div>
				<a class="blue" href="<?=site_url($dbrands[$bid]['url'])?>" style="margin-top:15px;margin-right:15px;float:right;font-size:100%;font-weight:bold;"><?=$dbrands[$bid]['num']?> product<?=$dbrands[$bid]['num']>1?"s":""?></a>
				<div class="bender bc-<?=$colors[rand(0,count($colors)-1)]?>">
					<div class="matter"><?=ucfirst($dbrands[$bid]['name'])?></div>
					<span></span>
				</div>
			</div>
			<div class="spot_deal_cont">
			<table cellspacing=10>
				<tr>
					<?php
					foreach($cdeals as $i=>$deal){?>
						<td class="dealdlist" <?php if($i%5==0 || $i==0) echo 'style="border-left:1px solid #ccc"';?>>
							<?php if(!$deal['live']){?><div class="ofs"><img src="<?=IMAGES_URL?>ofs_small.png"></div><?php }?>
							<?php if($deal['groupbuy']){?><div class="gbenabled"><img src="<?=IMAGES_URL?>groupenabled.png"></div><?php }?>
							<div class="imgcont">
								<a href="<?=site_url("{$deal['url']}")?>" class="scrollman sm_prodt">
									<img <?php if(B_BROW){?>class="scrm_load" src="<?=IMAGES_URL?>scroll_load.gif"<?php }else{?> title="<?=htmlspecialchars($deal['name'])?>" alt="<?=htmlspecialchars($deal['name'])?>" src="<?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg"<?php }?>>
									<?php if(B_BROW){?><span class="scrm_data"><?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg</span><?php }?>
								</a>
							<?php /*?>							<a href="<?=site_url("{$deal['url']}")?>"><img src="<?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg" width=160></a>   */ ?>
							</div>
							<div class="namecont">
								<div><a class="at" href="<?=site_url($deal['caturl'])?>" style="text-decoration:none"><?=$deal['category']?></a></div>
								<div style="font-size:95%;padding:5px 0px;"><?=$deal['name']?></div>
							</div>
							<div class="price"><b>Rs <?=number_format($deal['price'])?></b></div>
							<?php if($deal['price']<$deal['orgprice']){?>
							<div class="save">save <b><?=ceil(($deal['orgprice']-$deal['price'])/$deal['orgprice']*100)?>%</b></div>
							<div class="mrp"><span style="text-decoration:line-through">Rs <?=$deal['orgprice']?></span> list price</div>
							<?php }?>
						</td>
					<?php if(($i+1)%5==0) echo '</tr><tr><td colspan="5" style="border:0px;"></td></tr><tr>';}?>
				</tr>
			</table>
			</div>
			<?php if(empty($deals)){?>
					<div align="center" style="padding:50px;"><h2>Sorry! No deals available right now<br>Please check back later</h2></div>
			<?php }?>
		</div>
	<?php }?>

			<div id="noresults" style="display:none;margin:40px 20px;" align="center">
				<h3>There are no products to display after refinement. Please reset your filters</h3>
			</div>
	
	
	</div>
</div>
<script>
var brands=[<?=implode(",",$brandids)?>];
$(function(){
	$(".refine_but").click(function(){
		if($("input:not(:checked)",$(this).parent()).length==0 && $(".xp:contains(x)",$(this).parent()).length==0 && $(".refine_but",$(this).parent()).length!=1)
		{
			$(".refine_but",$(this).parent()).addClass("refine_but_unselected");
			$("input",$(this).parent()).attr("checked",false);
		}
		if($("input",$(this)).attr("checked"))
		{
			$(".xp",$(this)).html("+");
			$(this).addClass("refine_but_unselected");
			$("input",$(this)).attr("checked",false);
		}else{
			$(".xp",$(this)).html("x");
			$(this).removeClass("refine_but_unselected");
			$("input",$(this)).attr("checked",true);
		}
		$("input",$(this)).change();
	});
	$(".refine_but input").change(function(){
		$("#noresults").hide();
		$(".brandcont").hide();
		sels=[];
		$(".refine_but input:checked").each(function(i){
			sels.push($(this).val());
			$("#brandcont"+$(this).val()).show();			
		});
		if(sels.length==0)
			$("#noresults").show();
		scrollman();
	});
});
</script>

<?php if(empty($deals)){?>
<div style="margin:40px;" align="center">
<h2>There are no products currently available now!</h2>
<script>
window.setTimeout(function(){location=base_url;},3000);
</script>
</div>
<?php }?>
<div class="clear"></div>
<?php
