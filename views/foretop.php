<div class="attention">
	<div class="cont container">
		<div class="close">X</div>
		<div id="attentiontext"></div>
	</div>
	<div class="bar" style="display: none;">
		<div></div>
	</div>
</div>

<div class="foretop">
<div class="bgover">
<div class="container">

	<div class="logo_cont">
		<a href="<?=base_url()?>"><img src="<?=IMAGES_URL?>logo.png"></a>
	</div>
	
	<div class="foretopmenu">
	<ul>
		<li><a class="featured<?=$this->uri->segment(1)==""?" selected featuredselected":""?>" href="<?=base_url()?>">Discover</a></li>
		<li>
			<a class="shop<?=$this->uri->segment(1)=="spotlight"?" selected shopselected":""?>" href="<?=site_url("spotlight")?>">Shop</a>
		</li>
		<li><a class="discovery<?=$this->uri->segment(1)=="discovery"?" selected discoveryselected":""?>" href="<?=site_url("discovery")?>">Tags</a></li>
	</ul>	
		
	</div>
	
<div class="foretop_righttop">	
<?php 
if($this->session->userdata("user"))
$user=$this->session->userdata("user");
 if(isset($user)) { ?>
	<div class="welcomecont">
<div class="welcome">
<?php $boarder=$this->session->userdata("boarder");
$upic=IMAGES_URL."default_people.jpg";
if($boarder)
	$upic=IMAGES_URL."/people/".$boarder['pic']."_t.jpg";
else if($user)
{
	$p=$this->db->query("select pic from king_profiles where userid=?",$user['userid'])->row_array();
	if(!empty($p) && $p['pic']!="")
		$upic=IMAGES_URL."/people/".$p['pic']."_t.jpg";
}
?>
<?php /*?>
<a href="<?=$boarder?site_url("discovery/user/{$boarder['username']}/feed"):site_url("discovery")?>" style="float:right;"><img src="<?=$upic?>" height="50"></a>
*/ ?>
<div style="float:left;color: #FFF">
Hi <?php if($user['special']!=0) { ?><img
	src="<?=base_url()?>images/special<?=$user['special']?>.png"> <?php }?><b id="display_name" title="<?=$user['name']?>"><?=breakstring($user['name'],8)?></b>
	<?php if(isset($user['aid'])){?>- <?=$user['aid']?><?php }?>

<a href="<?=site_url("profile")?>" class="foretop_profile">My Profile <span class="img"><img src="<?=IMAGES_URL?>down_arrow2.png"></span></a>
		<a href="#" id="fanbhlink" style="width:0px;margin:0px;"></a><br>
</div>
</div>
<style>
.discover{
clear:right;
margin-top:0px;
margin-bottom:0px;
}
</style>
<div class="profile_popup">
	<?php if(!$user['verified']){?>
<a href="<?=site_url("getverified")?>" class="myorderlink">Get Verified</a>
	<?php }?>
	<a href="<?=site_url("profile#orders")?>">My Orders</a>
	<a href="<?=site_url("profile#profile")?>">My Account</a>
	<a href="<?=site_url("profile#coupons")?>">My Coupons</a>
	<a href="<?=site_url("profile#loves")?>">My Loves</a>
	<a href="<?=site_url("profile#favs")?>">My Favs</a>
	<a href="<?=site_url("profile#invite")?>">My Friends</a>
	<a class="signout" href="<?=site_url("signout")?>" style="padding:2px 4px;color: #FFF;background: #73CA00">Sign Out</a>
</div>

	</div>
<?php }?>

<?php if(!isset($user)){?>
	<div class="signinsignup">
		<a class="fb" href="<?=site_url("auth/fb")?>"><img src="<?=IMAGES_URL?>fblogin-small.png"></a>
		<a class="loginlink"  href="<?=site_url("signup")?>">
			Sign-in / Sign-up
		</a>
	</div>
<?php }?>
</div>	


<div class="cartheader">
	<div class="checkout"><a href="<?=site_url("shoppingcart")?>"><img src="<?=IMAGES_URL?>checkout_header.png"></a></div>
	<img src="<?=IMAGES_URL?>cart_white.png"  class="carticon">
	<div id="nocartitems"><?=$this->cart->total_items()?></div>
	<div class="totalcont">Rs <span class="total"><?=number_format($this->dbm->calc_cart_total())?></span></div>
</div>


	<div class="clear"></div>
</div>
</div>
</div>

<script>
var announce=<?=isset($_COOKIE['noannounce'])?"0":"1"?>;
$(function(){
if($('.topmenu_icons.selected').length == 0){
	$('.topmenu_icons:first').addClass('selected');
}
});
</script>
<style>
.phone{
	position: relative;
	top:10px;
}
</style>

<?php
