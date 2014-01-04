<?php 
if(!isset($user) && $this->session->userdata("user")!==false)
	$user=$this->session->userdata("user");
?>

<?php if($this->uri->segment(1)!="discovery"){ /* ?>
<div style="padding-bottom:5px;">
<div style="color:#fff;float:left;">
	<div style="margin-top:5px;margin-bottom:10px;">
		<a href="<?=site_url("spotlight")?>" style="display:inline-block;float:left;text-decoration:none;"><img
			style="margin-top: 0px; margin-bottom: 0px; margin-left: 5px;"
			src="<?=IMAGES_URL?>logo.png">
		</a> 
	</div>
</div>

	<a href="<?=site_url($this->session->userdata("user")?"profile#invite":"startinviting")?>" style="float:right;color: #FF9900;text-decoration: none;font-size: 14px;padding:4px;">
		Invite Friends 
	</a>
	
	
		<div class="phone" align="center" style="margin-right: 150px;top:4px;">
			<div style="font-size: 12px;">
				Call 080 - 42124462 <span class="timing" style="color: #FFF">(10.30 am - 6.30 pm Mon-Sat)</span>
			</div>
		</div>
	
		<div class="search">
			<form id="searchbox" autocomplete="off" method="post" action="<?=site_url("search")?>"><input class="srchinp" type="text" name="snp_q" value="Search brands, categories, products,..."><img src="<?=IMAGES_URL?>loading.gif" id="sug_s_loading"><input type="image" src="<?=IMAGES_URL?>search.png" value=""></form>
		</div>


	<div class="extralinks" style="float: right">
		<a href="<?=site_url("brands")?>" style="padding:10px 0px 0px 0px;" class="brands">Brands</a>
	</div>
	
	



<div style="float:right;clear:right;margin-top:3px;display:none;">
<a href="<?=site_url("discovery")?>"><img src="<?=IMAGES_URL?>tag_lifestyle.png"></a>
</div>

<?php if($this->session->userdata("user") && 0) { ?>
	<div class="invite_cw_cont">
		<a class="green" style="font-weight:bold;font-size:10px;" href="<?=site_url("profile")?>#invite"><img src="<?=IMAGES_URL?>invite_cw_menu.png"></a>
	</div>
<?php }?>
<div class="clear"></div>
</div>
<?php */ }else{ ?>
<div style="padding:20px 0px;">
<img src="<?=IMAGES_URL?>share_discover.png">
</div>
<?php }?>


</div>
</div>
</div>
<div>
<div class="container">

<div id="menu_loader">
</div>


<div class="clear"></div>

<?php
$redlist=array("shoppingcart","checkout","discovery","claim","headtotoe","email","signin","signup");
if(!in_array($this->uri->segment(1), $redlist) && $page!="showitem"){?>

	<div class="header_searchbar">
		<div class="search">
		<div id="suggest_srch"></div>
			<form id="searchbox" autocomplete="off" method="post" action="<?=site_url("search")?>">
				<a href="javascript:void(0)" class="smenu">Everywhere</a>
			<div class="menu_list">
			<a href="javascript:void(0)" onclick='selectsrchmenu("all")'>Everywhere</a>
<?php foreach($this->db->query("select url,name from king_menu where status=1 order by name asc")->result_array() as $m){?>
				<a href="javascript:void(0)" onclick='selectsrchmenu("<?=$m['url']?>")'><?=$m['name']?></a>
<?php }?>
			</div>
				<input type="hidden" name="menu" id="srchmenu" value="all">
				<input class="srchinp" type="text" name="snp_q" value="Search brands, categories, products,..."><img src="<?=IMAGES_URL?>loading_maroon.gif" id="sug_s_loading">
				<input type="submit" class="srchbutt" value="Search">
			<div class="sugstsrch_onoff_cont">
				<label><input type="checkbox" id="sugstsrch_onoff" <?=(isset($_COOKIE['sugst_search'])&&$_COOKIE['sugst_search']=="on")?"checked":""?>> Enable Suggestions</label>
			</div>
			</form>
			<div class="clear"></div>
		</div>

		<div class="links">
			<a href="<?=site_url("favs")?>"><img src="<?=IMAGES_URL?>header/fav5s.png" alt="Favorite 5's" title="Favorite 5's"></a>
			<a href="<?=site_url("weeklysavings")?>"><img src="<?=IMAGES_URL?>header/weeklysavings.png" alt="Weekly savings offers" title="Weekly Savings Offers"></a>
			<a href="<?=site_url("brands")?>"><img src="<?=IMAGES_URL?>header/brands.png" alt="Available brands in Snapittoday" title="Available brands in Snapittoday"></a>
		</div>
		
		
		<div class="clear"></div>
	</div>
<?php }?>

<a class="headtoplinks" id="cartlink" href="<?=site_url("shoppingcart")?>"></a>
