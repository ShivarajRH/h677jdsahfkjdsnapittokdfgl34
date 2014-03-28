<?php  

if(empty($deals) && is_from_google())
{
	$sdata=array();
	$sdata=$this->dbm->searchdeals($pagetitle);
	if(empty($sdata) && $this->dbm->getsearchcount($pagetitle)==0)
		$redirect_true=true;
	else
		$sdata=$this->dbm->searchdeals($pagetitle,false);
	$deals=$sdata;
}

$colors=array("green","violet","lightblue","red","black","darkgreen",'orange');

$ps=array();
foreach($deals as $d)
	$ps[]=$d['price'];

if(!empty($ps))
{
$max=max($ps);
$min=min($ps);

$sprices=array(ceil($min+($min/3)),ceil($max/2),ceil($max-($min/3)));
foreach($sprices as $i=>$p)
	$sprices[$i]=($p-($p%50))!=0?($p-($p%50)):$p;

}

//$sprices=array(700,2500,5000);

$sbrands=array();
$ubrands=array();
$scats=array();
$prices=array();
foreach($deals as $d)
{
	$scats[$d['catid']]=$d['category'];
	$ucats[$d['catid']]=$d['caturl'];
	$sbrands[$d['brandid']]=$d['brandname'];
	$ubrands[$d['brandid']]=$d['brandurl'];
	if(!isset($brands[$d['brandid']]))
		$brands[$d['brandid']]=array();
	if(!isset($cats[$d['catid']]))
		$cats[$d['catid']]=array();
	$brands[$d['brandid']][]=$d['itemid'];
	$cats[$d['catid']][]=$d['itemid'];
	$f=false;
	foreach($sprices as $pid=>$p)
	if($d['price']<$p)
	{
		$f=true;
		break;
	}
	if(!$f)
		$pid=3;
	if(!isset($prices[$pid]))
		$prices[$pid]=array();
	$prices[$pid][]=$d['itemid'];
}
arsort($sbrands);

if(isset($sprices))
{
$t_prices=$prices;
$prices=array();
for($i=count($sprices);$i>=0;$i--)
	if(isset($t_prices[$i]))
		$prices[$i]=$t_prices[$i];
}
?>
<style>
.dealdlist .gbenabled{
margin-top:-10px;
margin-bottom:-16px;
}
</style>
<div>
	<div class="container">
<?php if(isset($brandlogo)){?>
		<div class="brands_avail" align="right">
			<img src="<?=IMAGES_URL?>brands/<?=$brandlogo?>.jpg">
		</div>
<style>
#refine_brand_cont{
display:none;
}
</style>		
<?php }?>

<?php if(isset($search)){
	if($this->input->post("all")!="no" && count($deals)<($dc=$this->dbm->getsearchcount($this->input->post("snp_q")))){?>
<div style="padding:10px 0px;font-size:14px;">
<div>Your search found <b><?=count($deals)?></b> product<?=count($deals)>1?"s":""?></div>
<div style="font-size:80%">You can also try searching with at least one of the words in '<b><a href="javascript:void(0)" onclick='$("#allsearch_form").submit();'><?=$this->input->post("snp_q")?></a></b>' to find  <b><a href="javascript:void(0)" onclick='$("#allsearch_form").submit();'><?=$dc?></a></b> products
</div>
</div>
<form action="<?=site_url("search")?>" method="post" id="allsearch_form">
<input type="hidden" name="snp_q" value="<?=htmlspecialchars($this->input->post("snp_q"))?>">
<input type="hidden" name="all" value="no">
</form>
<?php }}?>

<div class="homecont refine" style="margin-bottom:10px;padding-bottom:0px;">
	<div class="top">
		<div class="head">search results</div>
	<div class="bottom">
	<div class="imghead">
			<h2></h2>
	</div>
	<div style="clear:both;padding:7px 0px 0px;">
		<h3>Categories <span style="font-weight:normal;font-size:80%;">(<?=count($scats)?>)</span></h3>
		<div class="search_fltr_box">
		<div class="scont">
		<?php asort($scats); $c=0; foreach($scats as $bid=>$brand){?>
			<div class="refine_but refine_cat"><div class="xp"></div>
<?php if(stripos($_SERVER['HTTP_USER_AGENT'],"Googlebot")!==false){?>			
				<a href="<?=(rand(0,5)==1?site_url("viewbycat/".$ucats[$bid]):site_url($this->uri->segment(1)."/".$ucats[$bid]))?>">
<?php }else{?>
				<a href="<?=site_url($this->uri->segment(1)."/".$ucats[$bid])?>">
<?php }?>
					<?=$brand?>
				</a>
			<input type="checkbox" class="ref_brand" checked="checked" value="<?=$bid?>"></div>
		<?php $c++; }?>
		</div>
		</div>
	</div>
	<div style="clear:both;padding:7px 0px 0px;" id="refine_brand_cont">
		<h3>Brands <span style="font-weight:normal;font-size:80%;">(<?=count($sbrands)?>)</span></h3>
		<div class="search_fltr_box">
		<div class="scont">
		<?php asort($sbrands); $brandids=array(); foreach($sbrands as $bid=>$brand){ $brandids[]=$bid; ?>
			<div class="refine_but refine_brand"><div class="xp"></div>
<?php if(stripos($_SERVER['HTTP_USER_AGENT'],"Googlebot")!==false){?>			
				<a href="<?=(rand(0,5)==1?site_url("viewbybrand/".$ubrands[$bid]):site_url($this->uri->segment(1)."/".$ubrands[$bid]))?>">
<?php }else{?>
				<a href="<?=site_url($this->uri->segment(1)."/{$ubrands[$bid]}")?>">
<?php }?>
				<?=$brand?></a>
			<input type="checkbox" checked="checked" class="ref_brand" value="<?=$bid?>"></div>
		<?php }?>
		</div>
		</div>
		<div class="clear"></div>
	</div>
	<div style="clear:both;padding:7px 0px 0px;">
		<h3>Price Range</h3>
		<div class="search_fltr_box">
		<div class="scont">
		<?php foreach($prices as $pid=>$null){  
			$p_disp=($pid==count($sprices))?" &gt; {$sprices[$pid-1]}":($pid==0?"0":$sprices[$pid-1])." - ".$sprices[$pid];
		?>
			<div class="refine_but refine_price"><div class="xp"></div>
			<?php
			$ff=$this->uri->segment(2)?($this->uri->segment(1)."/".$this->uri->segment(2)):("cb/".$this->uri->segment(1)); 
			?>
				<a href="<?=site_url("$ff/price-".trim(str_replace("&lt;","lt",str_replace("&gt;","gt",$p_disp))))?>"><?=$p_disp?></a>
				<input type="checkbox" class="ref_price" checked="checked" value="<?=$pid?>">
			</div>
		<?php }?>
		</div>
		</div>
		<div class="clear"></div>
	</div>
	</div>
	</div>

	<div class="clear" style="padding-top:10px;"></div>
</div>

	<div class="inner_deals_cont">
		<div class="homecont">
			<div>
				<div style="float:right"><b id="refine_numb"><?=count($deals)?></b> products found</div>
				<div class="bender bc-<?=$colors[rand(0,count($colors)-1)]?>">
					<div class="matter"><h1 style="font-size:inherit;"><?=ucfirst($pagetitle)?></h1></div>
					<span></span>
				</div>
			</div>
			<div class="spot_deal_cont">
					<?php
					foreach($deals as $i=>$deal){?>
						<div class="dealdlist" id="deal<?=$deal['itemid']?>" <?php //if($i%5==0 || $i==0) echo 'style="border-left:1px solid #ccc"';?>>
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
								<div style=""><a class="at" href="<?=site_url($deal['caturl'])?>" style="text-decoration:none"><?=$deal['category']?></a></div>
								<div style="font-size:95%;padding:5px 0px;"><?=$deal['name']?></div>
							</div>
							<div>
							</div>
							<div class="price">
							<b>Rs <?=number_format($deal['orgprice'])?></b>
							<?php if($deal['price']!=$deal['orgprice']){?>
							<div class="instantcashback">Rs <?=$deal['price']?> <img src="<?=IMAGES_URL?>instantcashback.png"></div>
							<?php }?>
							</div>
<?php /*?>							
							<div class="freeshipping">
							<b class="blue">Free Shipping</b>
							<?php if($deal['price']<MIN_AMT_FREE_SHIP){?>
							 <span>on Rs <?=MIN_AMT_FREE_SHIP?> &amp; more</span>
							<?php }?>
							</div>
*/ ?>
<?php if($deal['live']){?>
							<div class="addcart_quick_cont"><label><input type="checkbox" value="<?=$deal['url']?>" style="display:none;" class="addtocart_quick"> <span>Add to cart</span></label></div>
<?php } ?>
						</div>
					<?php if(($i+1)%4==0){?>
					<?php }?>
					<?php }?>
				<div class="clear"></div>
			<div id="noresults" style="clear:both;display:none;margin:40px 20px;" align="center">
				<h3>There are no products to display after refinement. Please reset your filters</h3>
			</div>
			<?php if(empty($deals)){?>
			<?php if(!isset($redirect_true)){?>
					<div align="center" style="clear:both;padding:50px;"><h2>Sorry! No products available right now<br>Please check back later</h2></div>
			<?php }else{?>
					<div align="center" style="clear:both;padding:50px;"><h2>Your searched product is not available right now.<br>Please wait.. we are redirecting you to a relevant page..</h2><script>location='<?=base_url()?>';</script></div>
			<?php }?>
			<?php }?>
			</div>
		</div>
		
	</div>	
		<div class="clear"></div>
	</div>
</div>

<script>

var brands=[];

$(function(){
	$(".refine_but").click(function(){
		$.fancybox.showActivity();
		if($("input:not(:checked)",$(this).parent()).length==0 && $(".refine_but_selected",$(this).parent()).length==0 && $(".refine_but",$(this).parent()).length>1)
		{
			$(".refine_but",$(this).parent()).addClass("refine_but_unselected");
			$("input",$(this).parent()).attr("checked",false);
		}
		if($("input",$(this)).attr("checked"))
		{
			$(this).addClass("refine_but_unselected");
			$(this).removeClass("refine_but_selected");
			$("input",$(this)).attr("checked",false);
		}else{
			$(this).removeClass("refine_but_unselected");
			$(this).addClass("refine_but_selected");
			$("input",$(this)).attr("checked",true);
		}
		refine();
//		$("input",$(this)).change();
		$.fancybox.hideActivity();
	});
//	$(".refine_but input").change(function(){
//		refine();
//	});
});

var prices=[],cats=[];
<?php foreach($prices as $p=>$price){?>
prices[<?=$p?>]=[<?=implode(",",$price)?>];
<?php }?>

<?php if(isset($brands))
		foreach($cats as $c=>$cat){?>
cats[<?=$c?>]=[<?=implode(",",$cat)?>];
<?php }?>

<?php if(isset($brands))
		foreach($brands as $p=>$brand){
?>
	brands[<?=$p?>]=[<?=implode(",",$brand)?>];
<?php }?>
			

</script>

<?php
