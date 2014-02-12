<?php $rec_views=$this->dbm->gethistory(14); ?>

<?php $notrending=array("discovery","signup","checkout","shoppingcart","headtotoe"); 
if(!in_array($this->uri->segment(1), $notrending) && stripos($_SERVER['HTTP_USER_AGENT'],"ipad")===false && $page!="showitem"){?>

<div class="search_right">
<div class="link"><img src="<?=IMAGES_URL?>subscribe_right.png"></div>

<div class="cont">
<div class="head"><a class="close" href="javascript:void(0)" onclick='$(".search_right .link").click()'>x</a>Subscribe & get a SnapCoupon!</div>

<div class="subscribe">
		<form id="ts_subscribe" style="white-space:nowrap;">
			<input type="text" id="ts_subscribe_input" value="Enter your Email..." style="width:230px;">
			<a href="javascript:void(0)" onclick='$("#ts_subscribe").submit()'><img src="<?=IMAGES_URL?>subscribe.png" style="margin-bottom:-7px;"></a>
		</form>
	<div class="clear"></div>
</div>
<div class="clear"></div>
<div class="trending">
	<div class="head">Trending</div>
	<div class="tcont">
				<?php $trends=$this->dbm->gettrends();?>
				<?php foreach($trends as $trend){?>
					<a href="<?=site_url("trend/$trend")?>">#<?=$trend?></a>
				<?php }?>
	</div>
</div>

<div class="share">
<a href="<?=site_url($this->session->userdata("user")?"profile#invite":"startinviting")?>"><img src="<?=IMAGES_URL?>invite_friends_text.png"></a>
</div>

</div>

</div>

<?php 
/*
$redlist=array("featured","index","discovery");
if(!in_array($this->uri->segment(1), $redlist) && !in_array($this->uri->rsegment(2),$redlist)){?>
<div id="trending_subscribe">
	<div class="trends_cont">
	<div class="trends">
	<div class="cont">
			</div>
		</div>
	</div>
</div>
<?php }
*/?>

<?php }?>



<?php if($page!="indexsignin" && $page!="indexsignup" && $this->uri->segment(1)!="discovery"){?>
<div id="header_menu_cont">
<div class="menu">
	<ul>
<?php $home_selected=(!$this->uri->segment(1)||$this->uri->segment(1)=="spotlight")?true:false;?>	
		<li class="main" >
			<a class="homelink" href="<?=site_url("spotlight")?>">
			<img src="<?=IMAGES_URL?>home.png" style="display:none">
			<img src="<?=IMAGES_URL?>home2.png">
			</a>
		</li>
<?php 
$url=$this->uri->segment(1);
$menus=$this->dbm->getmenucats();
$menu=$menus[0];
$ci=0;
foreach($menu as $m){
	if($m['name']=="Exclusive") continue;
?>
		<li class="main <?php if($m['name']=="Exclusive"){?> exclusive<?php }?> <?=$url==$m['url']?"selected":""?>">
		
		<a href="<?php if($m['name']=="Exclusive") echo site_url("exclusive"); else {?><?=site_url($m['url'])?><?php }?>">
			<?=ucfirst($m['name'])?>
		</a>
			<?php if(!empty($menus[1][$m['id']]) && $m['name']!="Exclusive"){ $menu_det=$menus[1][$m['id']];?>
			<div class="menu_cont" style="<?=$ci==2340?"display:block;":""?>margin-left:-<?=($ci*65)+10?>px;">
				<div class="menuimg">
					<a href="<?=site_url($menu_det['top']['url'])?>">
						<img src="<?=IMAGES_URL?>items/small/<?=$menu_det['top']['pic']?>.jpg">
					</a>
					<div align="left">
					<h3><?=$menu_det['top']['name']?></h3>
					<a class="link" href="<?=site_url($menu_det['top']['url'])?>">View Product</a>
					<h4>Rs <?=$menu_det['top']['price']?></h4>
					</div>
				</div>
				<div class="menu_links">
				<h3>Shop by category <b>(<?=$menu_det['cats_count']?>)</b></h3>
					<ul>
						<?php foreach($menus[1][$m['id']]['cats'] as $cat){?>
							<li><a href="<?=site_url($m['url']."/".$cat['url'])?>"><?=$cat['name']?></a></li>
						<?php }?>
					</ul>
				</div>
				<div class="mcats">
					<h3>Popular categories</h3>
					<ul>
						<?php foreach($menus[1][$m['id']]['mcats'] as $cat){?>
							<li><a href="<?=site_url($m['url']."/".$cat['url'])?>"><?=$cat['name']?></a></li>
						<?php }?>
					</ul>
					<h3 style="margin-top:7px;color:#FF8B12 !important;">Top brands <b>(<?=$menu_det['brands_count']?>)</b></h3>
					<ul>
						<?php foreach($menus[1][$m['id']]['mbrands'] as $cat){?>
							<li><a href="<?=site_url($m['url']."/".$cat['url'])?>"><?=$cat['name']?></a></li>
						<?php }?>
					</ul>
				</div>

				<div class="viewall">
					<a href="<?=site_url($m['url'])?>">See all <?=$menu_det['cats_count']?> categories in <?=$m['name']?> &gt;</a>
				</div>
				
				<div class="clear"></div>
	
				
			</div>
			<?php }?>
		</li>
<?php $ci++;}?>
	</ul>
	<div class="clear"></div>
</div>
</div>
<?php }?>



<div class="footer">

<a href="<?=site_url("history")?>" style="float:right;margin-top:5px;color:#059CD1;">manage</a>
<div class="history">
<h3>your recent views</h3>
<?php if(empty($rec_views)){?>
No products recently viewed
<?php }else{
foreach($rec_views as $rv){?>
<a href="<?=site_url($rv['url'])?>">
<img src="<?=IMAGES_URL?>items/thumbs/<?=$rv['pic']?>.jpg" title="<?=htmlspecialchars($rv['name'])?>">
</a>	
<?php } }?>
<div class="clear"></div>
</div>


<div style="padding-top:20px;padding-bottom:20px;font-size:80%;" align="left">



<div style="float:right;">
<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fsnapittoday&amp;width=360&amp;height=220&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=false&amp;header=false&amp;" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:360px; height:220px;" allowTransparency="true"></iframe>
</div>


<table width="620">
<tr>
<Td class="ftlinks">
<h3>our products</h3>
<?php foreach($this->db->query("select name,url from king_menu where status=1 order by name asc")->result_array() as $m){?>
<div><a href="<?=site_url($m['url'])?>"><?=$m['name']?></a></div>
<?php }?>
</Td>
<td class="ftlinks" align="left" valign="top">
<h3>legal terms</h3>
<div><a href="<?=site_url("terms")?>">Terms of service</a></div>
<div><a href="<?=site_url("privacy_policy")?>">Privacy</a></div>
<div><a href="<?=site_url("shipping_policy")?>">Shipping Policy</a></div>
<div><a href="<?=site_url("cancellation_policy")?>">Returns</a></div>
<div><a href="<?=site_url("disclaimer")?>">Disclaimer</a></div>
</td>
<td class="ftlinks" align="left" valign="top">
<h3>support</h3>
<div><a href="<?=site_url("faqs")?>">FAQs</a></div>
<div><a href="mailto:hello@snapittoday.com">Email Us</a></div>
<div><a href="<?=site_url("contact_us")?>">Contact Us</a></div>
<div><a href="<?=site_url("joinhands")?>">Supplier Contact</a></div>
<div><a href="<?=site_url("about_us")?>">About Us</a></div>
</td>
<td class="ftlinks" align="left" valign="top" style="color:#fff;">
<div class="assist">
<div>For Assistance:</div>
<table cellspacing=0 cellpadding=1>
	<tr><td>Email : </td><td><b><?=CS_EMAIL?></b></td></tr>
</table>
</div>
</td>
</tr>
</table>


<div style="padding-top:20px;">

<div style="float:left">

<div style="display:inline-block" align="left">
<h3>PAYMENT OPTIONS</h3>
<img src="<?=IMAGES_URL?>visa.png">
<img src="<?=IMAGES_URL?>mastercard.png">
</div>

</div>

</div>

<div style="float:left;margin-left:60px;">
<h2 style="padding-top:10px;text-transform:uppercase;">
<div style="float:right;margin-top:-5px;margin-left:10px;"> <a href="http://www.facebook.com/snapittoday"><img src="<?=IMAGES_URL?>big_facebook.png"></a> <a href="http://twitter.com/snapittoday"><img src="<?=IMAGES_URL?>big_twitter.png"></a></div>
Stay Connected :
</h2>
</div>

<div style="clear:both;padding-top:20px;font-size:110%;">
Snapittoday.com is a one stop destination for your Personal care, beauty,wellness and Healthcare products. It offers products from top Indian and international brands. Some of the most reputed brands available on Snapittoday.com include Garnier,L'Oreal,Gillette,Lakme, Sally Hansen, Lotus Herbals, Maybelline, Revlon, Olay, Lux, Dove, Park Avenue, Organic Surge, Palmer's. Snapittoday.com offers the widest range or products and customers get to choose from over 4000+ products. 
We will soon be listed in health and beauty care category on Junglee.com
</div>

<div align="right" style="font-weight:bold;margin-top:10px;font-size:100%;">
&copy;2011-<?=date("Y")?> www.Snapittoday.com, Local Cube Commerce Pvt Ltd., All rights reserved
</div>
<div align="right" style="color:#606060;font-size:90%;">v <?=APL_VER?></div>

</div>

</div>

</div>

<?php /*?>
url : <?php echo $this->benchmark->elapsed_time("url","url1");?>
db : <?php echo $this->benchmark->elapsed_time("itemdb","itemdb1");?>
item : <?php echo $this->benchmark->elapsed_time("item","item1");?>
total : <?php echo $this->benchmark->elapsed_time();?>
*/?>
