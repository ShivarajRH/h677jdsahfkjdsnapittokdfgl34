<?php $user=$this->session->userdata("user");
$status=array("Pending","Processed","Shipped","Rejected");
 ?>
 
<script type="text/javascript" src="http://www.plaxo.com/css/m/js/util.js"></script>
<script type="text/javascript" src="http://www.plaxo.com/css/m/js/basic.js"></script>
<script type="text/javascript" src="http://www.plaxo.com/css/m/js/abc_launcher.js"></script>
<script type="text/javascript"><!--
function onABCommComplete() {
  // OPTIONAL: do something here after the new data has been populated in your text area
}
//--></script>

<div style="background:#fff;padding:0px;">
<div class="container" style="min-height:530px;">
<div class="profile_loyaltypoints">
<h4>Your loyalty points : <span><?=$points?></span></h4>
<?php if($points>=POINTS_REDEEMABLE_MIN){?>
<a href="<?=site_url("claim_points")?>">Click here</a> to redeem
<?php }else{?>
Get <?=POINTS_REDEEMABLE_MIN?> points to redeem
<?php }?>
</div>
<h2 style="padding:10px 0px;margin-top:10px;">Hi <?=$user['name']?>, your Snapittoday.com account details</h2>
<div id="tabs" class="dashbrd" style="clear:both;padding:0px;">
	<ul style="padding:0px;">
		<li><a href="#orders"><img src="<?=IMAGES_URL?>icons/orders.png"> Orders</a></li>
		<li><a href="#profile"><img src="<?=IMAGES_URL?>icons/profile.png"> Profile</a></li>
		<li><a href="#cashbacks"><img src="<?=IMAGES_URL?>icons/coupons.png"> Coupons/Cashbacks</a></li>
		<li><a href="#loves"><img src="<?=IMAGES_URL?>icons/love.png"> My Loves</a></li>
		<li><a href="#favs"><img src="<?=IMAGES_URL?>icons/favs.png"> My Favs<?php /*?>(<?=count($this->dbm->getallfavids())?>) */ ?></a></li>
		<li><a href="#invite"><img src="<?=IMAGES_URL?>icons/friends.png"> Friends</a></li>
	</ul>
	<div id="orders">
	<a id="pr_invitebp_trig" href="<?=site_url("inviteforbp")?>"></a>
	<?php if(empty($orders)){?>
		<div style='background:#eee;padding:10px'>You haven't purchased anything yet</div>
	<?php }else{?>

	<table cellspacing=0 width="100%" cellpadding=7 style="background:#fafafa;">
	<tr style="background:#dedede;">
		<th>S.No</th>
		<th>Transaction ID</th>
		<th>Amount</th>
		<th>Ordered on</th>
		<th></th>
		<th>Points Earned</th>
	</tr>
	<?php foreach($orders as $i=>$o){?>
	<tr class="trans_row">
		<td><?=($i+1)?></td>
		<td><a href="<?=site_url("transaction/{$o['transid']}")?>" style="color:blue"><?=$o['transid']?></a></td>
		<td>Rs <b><?=$o['transamount']?></b></td>
		<td><?=date("g:ia d/m/y",$o['init'])?></td>
		<td>
		<?php if($o['init']>time()-(REORDER_VALID_DAYS*24*60*60)) { ?>
			<a href="<?=site_url("reorder/{$o['transid']}")?>"><img src="<?=IMAGES_URL?>reorder.png"></a>
		<?php } ?>
		</td>
		<td>
		<?php if(!empty($o['points'])){?>
		<b><?=$o['points']?></b> <?=$o['pstatus']?"":"<span style='font-size:70%'>(pending)</span>"?>
		<?php }else echo "0";?>
		</td>
	</tr>
	<?php }?>
	</table>

	
	<?php /*?>	
	<table cellspacing=0 width="100%">
	<?php foreach($orders as $i=>$o){?>
	<tr>
		<td width="50%" style="border-bottom:10px solid #fff;background:#fafafa;padding:10px;">
			<img src="<?=IMAGES_URL?>items/<?=$o['pic']?>.jpg" width=150 style="float:left;margin-right:10px;">
			<div align="right" style="margin-bottom:10px;">Ordered on <b><?=date("d D M y",$o['time'])?></b></div>
			<h4 class="green"><?=$o['item']?></h4>
			<div>Amount paid in transaction<b>Rs <?=$o['paid']?></b></div>
			<div>Quantity : <b><?=$o['quantity']?></b></div>
			<div style="font-size:85%;">Transaction ID  : <b><?=$o['transid']?></b></div>
		</td>
		<td width="50%" style="border-bottom:10px solid #fff;background:#eee;padding:10px;font-size:90%;">
			<div class="green bold" style="margin-bottom:10px;"  align="right"><?php if($o['bpuisrefund']==0){?>No Cashback - direct buy<?php }else{?>Group Buying<?php }?></div>
		<?php if($o['bpuisrefund']==1){?>
			<div style="float:right;width:220px;border-left:1px solid #000;padding-left:10px;">
				<div>Invited : <b><?=$o['totalinvites']?></b> coworkers</div>
				<div>Bought By : <b><?=$o['boughtinvites']?></b> coworkers</div>
				<div>Total Buys : <b><?=$o['quantity_done']?></b></div>
				<?php if($o['bpstatus']==0){?>
				<div><a href="javascript:void(0)" class="pr_invmore" onclick='setbpid(<?=$o['bpid']?>)' style="color:#ff9900;font-weight:bold;font-size:90%;">Invite more coworkers/friends</a></div>
				<div style="padding-top:10px;font-size:80%;">Buy process ends on <b><?=date("g:ia d M y",$o['bpexpires'])?></b></div>
				<?php }?>
			</div>
		<?php }?>
			<div>Shipment Status : <b><?=$status[$o['status']]?></b></div>
			<div>Cashback : <b><?php if($o['bpuisrefund']==0) echo 'No Cashback - direct buy'; else if($o['bpstatus']==0) echo 'Pending (Process not completed)'; elseif($o['bpstatus']==1) echo 'Done'; elseif($o['bpustatus']==2) echo "Expired";?></b></div>
			<div>Cashback Amount : <b>Rs <?php if($o['bpuisrefund']==0) echo 'na'; else if($o['bpstatus']==0) echo $o['refund']*$o['bpuqty']; else if($o['bpstatus']==1){ if($o['refund']!=$o['refund_given']) echo $o['refund_given']*$o['bpuqty'].'<span style="text-decoration:line-through">'.$o['refund']*$o['bpuqty'].'</span>'; else echo $o['refund'];} else echo "0";?></b></div>
			<div style="font-size:80%;padding-top:20px;"><?php if($o['status']==0) echo "Invoice not available until product shipped"; else {?><a href="<?=site_url("order/{$o['id']}")?>">view invoice</a><?php }?></div>
		</td>
	</tr>
	<?php }?>
	</table>
*/ ?>	
	<?php }?>
	</div>
	<div id="profile">
		<h3>Personal Information</h3>
		<div class="cont">
			
			<form action="<?=site_url("updatecrp")?>" method="post" enctype="multipart/form-data">
			<table width="100%" cellpadding="5">
			<tr>
			<td width="50%">
				<table style="margin-left:20px;margin-top:20px;" cellpadding=5 width="100%">
					<tr>
						<td>Profile Pic</td><td>:</td>
						<td>
							<div style="float:right;">	<?php if($profile['pic']!=""){?>
									<img src="<?=IMAGES_URL?>people/<?=$profile['pic']?>_t.jpg">
								<?php }elseif(($boarder=$this->session->userdata("boarder"))){?>
									<img src="<?=IMAGES_URL?>people/<?=$boarder['pic']?>_t.jpg">
								<?php }else{?>
									<img src="<?=IMAGES_URL?>default_people.jpg">
								<?php }?>
							</div>
							<input type="file" name="pic" size=10><div style="font-size:80%">Please use this uploader<br>to change your profile photo</div>
						</td>
					</tr>
					<tr>
						<td>Designation</td><td>:</td><td><input type="text" name="designation" value="<?=$p['designation']?>"></td>
					</tr>
					<tr>
						<td>Department*</td><td>:</td><td><input type="text" name="department" value="<?=$p['department']?>"></td>
					</tr>
					<tr>
						<td>Location*</td><td>:</td><td><input type="text" name="location" value="<?=$p['location']?>"></td>
					</tr>
					<tr>
						<td>Employee No</td><td>:</td><td><input type="text" name="employee_no" value="<?=$p['employee_no']?>"></td>
					</tr>
					<tr>
						<td>Desk No</td><td>:</td><td><input type="text" name="desk_no" value="<?=$p['desk_no']?>"></td>
					</tr>
					<tr><td>Facebook profile </td><td>:</td><td><input type="text" name="facebook" value="<?=$p['facebook']?>"></td></tr>
					<tr><td>LinkedIn profile</td><td>:</td><td><input type="text" name="linkedin" value="<?=$p['linkedin']?>"></td></tr>
					<tr><td>Twitter profile </td><td>:</td><td><input type="text" name="twitter" value="<?=$p['twitter']?>"></td></tr>
					</table>
			</td>
			<td width="50%">
				<table cellpadding=10 style="margin-left:20px;margin-top:30px;">
					<tr><td><b>Name :</b></td><td><b><?=$user['name']?></b></td></tr>
		<?php if($user['special']==0){?>
					<tr><td valign="top"><b>Password :</b></td><td>
					<div>******* <a href="#chnpss_cont" class="fancylink">change password</a></div>
					</td></tr>
		<?php } ?>
					<tr>
					<td><b>Personal Email :</b></td>
					<td><?=$user['email']?>
						<span style="font-size:80%"><?php if($user['verified']){?><b class="green">Verified Account</b><?php }else{?><b class="red">Unverified Account</b><?php }?></span>
					</td></tr>
					<tr><td><b>Work Email :</b></td><td><?=$workemail?></td></tr>
					<tr><td><b>Mobile :</b></td><td><?=$user['mobile']?></td></tr>
					<tr><td><b>Group :</b></td><td><b><?=$user['corp']?></b></td></tr>
					<tr><td colspan=2><input name="optin" value="yes" type="checkbox"<?php if($this->db->query("select optin from king_users where userid=?",$user['userid'])->row()->optin){?> checked="checked"<?php }?>> I would like to subscribe to newsletter and periodic offers</td></tr>
				</table>
			</td>
			</tr>	
				</table>
				<div class="update" align="right">
					<input type="image" src="<?=IMAGES_URL?>update.png">
				</div>
			</form>
		</div>
		<h3>Address Details</h3>
		<div class="cont">
			<form action="<?=site_url("changeaddr")?>" method="post">
			<table width="100%">
			<tr>
			<td width="50%">
				<table cellpadding=5 style="margin-left:40px;">
					<tr><td><b>Name :</b></td><td><input type="text" name="name" value="<?=$user['name']?>"></td></tr>
					<tr><td><b>Address :</b></td><td><textarea name="address" cols="40"><?=$addr['address']?></textarea></td></tr>
					<tr><td><b>Landmark :</b></td><td><textarea name="landmark" cols="40"><?=$addr['landmark']?></textarea></td></tr>
				</table>
			</td>
			<td valign="middle">
				<table cellpadding=5 style="margin-top:30px;margin-left:40px;">
					<tr><td><b>City :</b></td><td><input type="text" name="city" value="<?=$addr['city']?>"></td></tr>
					<tr><td><b>State :</b></td><td><input type="text" name="state" value="<?=$addr['state']?>"></td></tr>
					<tr><td><b>Pincode :</b></td><td><input type="text" name="pincode" value="<?=$addr['pincode']?>"></td></tr>
				</table>
			</td>
			</tr>
			</table>
			<div class="update" align="right">
				<input type="image" src="<?=IMAGES_URL?>update_address.png">
			</div>
			</form>
		</div>
	</div>
	<div id="cashbacks">
	
<?php if(empty($cashbacks)){?>
		<div style="background:#eee;padding:10px;">No coupons linked to your account</div>
<?php }else{?>

		<h4 class="head">My transactional cashbacks</h4>
<?php $tr=0; foreach($cashbacks as $i=>$c){
	$f=true;
	foreach($referrals as $r)
		if($r['ncoupon']==$c['code'])
		{
			$f=false;
			break;
		}
	if(!$f) continue;
	$tr=1; ?>
		<div class="eachcashbk">
			<h4>
			<a href="javascript:void(0)" title="click to activate this cashback for your cart" onclick='applycoupon("<?=$c['code']?>")'>apply cashback</a>
			<?=$c['code']?>
			</h4>
			<div>Rs <b><?=$c['value']?></b> off on min order Rs <?=$c['min']?></div>
			<div>Expires on '<b><?=date("d/m/Y",$c['expires'])?></b>'</div>
<?php
$gotfrom="";
$pc=$this->db->query("select * from king_pending_cashbacks where code=?",$c['code'])->row_array();
if(!empty($pc)){
if($pc['orderid']!=0)
	$gotfrom=$this->db->query("select i.name from king_orders o join king_dealitems i on i.id=o.itemid where o.id=?",$pc['orderid'])->row()->name;
else
	$gotfrom=$pc['transid'];
}else{
	$pc=$this->db->query("select * from king_cashbacks_track where coupon=?",$c['code'])->row_array();
if(!empty($pc))
	$gotfrom=$pc['transid'];	
}
?>
<?php if($gotfrom!=""){?>			<div style="font-size:85%">Got from buying <b>'<?=$gotfrom?>'</b></div><?php }?>
		</div>
<?php } if($tr==0){?><div style="background:#eee;padding:10px;margin:3px;">No transactional coupons linked to your account</div><?php }?>
	<div class="clear"></div>

	<h4 class="head" style="margin-top:15px;">My referral cashbacks</h4>
<?php $tr=0; foreach($cashbacks as $i=>$c){
	$f=true;
	foreach($referrals as $r)
		if($r['ncoupon']==$c['code'])
		{
			$f=false;
			break;
		}
	if($f) continue;
	$tr=1;
?>
		<div class="eachcashbk">
			<h4>
			<a href="javascript:void(0)" title="click to activate this cashback for your cart" onclick='applycoupon("<?=$c['code']?>")'>apply cashback</a>
			<?=$c['code']?>
			</h4>
			<div>Rs <b><?=$c['value']?></b> off on min order Rs <?=$c['min']?></div>
			<div>Expires on '<b><?=date("d/m/Y",$c['expires'])?></b>'</div>
		</div>
<?php } if($tr==0){?><div style="background:#eee;padding:10px;margin:3px;">No referral coupons linked to your account</div><?php }?>
	<div class="clear"></div>
<?php }?>

	
<?php 
$pcashbacks=$this->db->query("select * from king_cashbacks where status=1 and userid=?",$user['userid'])->result_array();
if(!empty($pcashbacks)){?>

<h4 class="head" style="margin-top:15px;">My unclaimed cashbacks</h4>

<table width="100%" cellpadding=5 cellspacing=0 border=0>
<tr style="background:#eee;">
<th>Cashback Amount</th><th>Transaction</th><th></th>
</tr>
<?php foreach($pcashbacks as $pc){?>
<tr class="trans_row">
<td><b>Rs <?=$pc['amount']?></b></td>
<td><?=$pc['transid']?></td>
<td><a style="color:blue;font-size:80%;" href="<?=site_url("claim/".$pc['url'])?>"><b>Claim my cashback</b></a>
</tr>
<?php }?>
</table>
<?php }?>


<h4 class="head" style="margin-top:15px;">Used Coupons</h4>
<?php if(empty($usedcoupons)){?>
<div style="background:#eee;padding:10px;margin:3px;">You haven't used any coupons yet</div>
<?php }else{?>

<table width="100%" cellpadding=5 cellspacing=0 border=0>
<tr style="background:#eee;">
<th>Coupon</th><th>Transaction</th><th>Done on</th>
</tr>
<?php foreach($usedcoupons as $c){?>
<tr class="trans_row">
<td><?=$c['coupon']?></td>
<td><a href="<?=site_url("transaction/{$c['transid']}")?>"><?=$c['transid']?></a></td>
<td><?=date("d/m/y",$c['time'])?></td>
</tr>
<?php }?>
</table>

<?php }?>

	</div>
	<div id="loves">
<ul class="disc_cont disc_tags_cont" style="margin-top:10px">
<?php 
$cols=4;
$disp_tags=array();
for($i=0;$i<$cols;$i++)
	$disp_tags[$i]=array();
$i=0;
foreach($loves as $t)
{
	$disp_tags[$i][]=$t;
	$i++;
	if($i>=$cols)
		$i=0;
}

foreach($disp_tags as $i=>$tgs){ ?>
<li class="col<?=($i+1)?>">
<?php foreach($tgs as $t){?>
			<div class="d_s_tag">
				<a class="img" href="<?=site_url("{$t['url']}")?>"><img src="<?=IMAGES_URL?>items/small/<?=$t['pic']?>.jpg"></a>
				<div class="bottom">
						<DIV style="margin-bottom:5px;"><?=$t['name']?></DIV>
						<div style="float:left;margin:0px 5px;"><g:plusone href="<?=site_url($t['url'])?>" size="large" count=false></g:plusone></div>
						<div><iframe frameborder="0" scrolling="no" allowtransparency="true" style="border: medium none; overflow: hidden; width: 50px; height: 25px; vertical-align: top;" src="http://www.facebook.com/plugins/like.php?href=<?=urlencode(site_url($item['url']))?>&amp;send=false&amp;layout=button&amp;width=60&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=trebuchet+ms&amp;height=21"></iframe></div>
				</div>
			</div>
<?php }?>
</li>
<?php }?>
</ul>
<div class="clear"></div>	
	</div>
	<div id="favs">
	<table width="100%">
		<tr>
<?php foreach($favs as $i=>$love){?>
			<td width="33%">
			<table>
			<tr>
				<td><a style="float:left;" href="<?=site_url($love['url'])?>"><img style="margin-right:5px;" src="<?=IMAGES_URL?>items/small/<?=$love['pic']?>.jpg"></a></td>
				<td>
				<div>
					<h4><a href="<?=site_url($love['url'])?>" style="text-decoration:none"><?=$love['name']?></a></h4>
					<div style="font-size:11px;margin-top:10px;">Locked until <b><?=date("d/m/Y",$love['expires_on'])?></b></div>
				</div>
				</td>
			</tr>
			</table>
			</td>
<?php if(($i+1)%3==0) echo "</tr><tr>";}?>		
		</tr>
	</table>
	
	<div id="favs_faq">
	
<h3 style="margin-top:10px;">FAQs</h3>	
<ol class="faqs">	
	
<li>
	<div class="q">What is the Favourite 5's Program?</div>
	<div class="a">Favourite 5's is a great way to save every time you shop on Snapittoday.com<br>
 	Just pick your 5 favorite items, lock them, and start saving 10% on your Favorite 5's every time you order with us.
 		</div>
</li>

<li>
	<div class="q">Why did you create the program?</div>
	<div class="a">This program is our way of giving loyal Snapittoday.com customers a big discount,  we want you to enjoy shopping with us, over and over again.Then watch for your instant savings for as long as we have this program.</div>
</li>

<li>
	<div class="q">How does the Favourite 5's program work?</div>
	<div class="a">Choose 5 of your favorite items from 4000++ products – one item per shopping Category (i.e. one from Hair Care, one from Skin care, one from Makeup etc..) for up to 5 Categories in total. As soon as you lock in your Favorite 5's, you can start saving 10% on them every time you place a order @ Snapittoday.com</div>
</li>

<li>
	<div class="q">Who is eligible to sign-up for the Favourite 5's Program?</div>
	<div class="a">Anyone can sign up for the Snapittoday.com.</div>
</li> 

<li>
	<div class="q">Can I add coupons and sales on top of my faves discounts for even bigger savings?</div>
	<div class="a">Yes , you can and get discount as subject to coupon code Value.</div>
</li>
<li>
	<div class="q">How long are my Favourites locked in for?</div>
	<div class="a">Your favorite 5's are locked in for a period of 90 days,</div> 
</li>
<li>
	<div class="q">Can I swap out my Favourites for new ones?</div>
	<div class="a">NO, Until your 90 day period is over, so be extra sure of locking-in favorite 5's</div>
</li>
<li>
	<div class="q">What happens if one of my Favorites is out of stock or discontinued by the manufacturer?</div>
	<div class="a">You will have a option to choose a new Favourite product into your profile</div>
</li>
<li>
	<div class="q">Where can i check the My favorite list.</div>
	<div class="a">Your Favourite 5 products are allays displayed in your profile page (under favourite 5 tab)</div>
</li>
</ol>
	
	</div>
	</div>
	<div id="invite">
		<div style="padding:0px;">
		<form action="<?=site_url("invite")?>" id="invitefrm" method="post">
			<div style="margin:20px;width:935px;margin-left:0px;height:910px;background:url(<?=IMAGES_URL?>invite_fb_bg.png) no-repeat;">
			<div style="padding-top:375px;padding-left:37px;">
				<div style="float:right;margin-right:30px;width:450px;">
					<h4 style="color:#A8518A">Start Inviting</h4>
					<div style="font-size:80%">Please enter email address of your friends separated by comma</div>
					<textarea style="width:100%;height:120px;" id="recipient_list" name="emails"></textarea>
					<div style="float:left"><a href="#" onclick="showPlaxoABChooser('recipient_list', '/pl_cb.php'); return false"><img src="http://www.plaxo.com/images/abc/buttons/add_button.gif" alt="Add from my address book" /></a></div>
					<div align="right">
						<input type="image" src="<?=IMAGES_URL?>submit_invite.png">
					</div>
				</div>
				
				<h4 style="color:#A8518A;margin-top:20px;">Invite your Facebook friends</h4>
				<a href="javascript:void(0)" onclick='cws_fblogin()' id="invitefb" style="color:blue"><img src="<?=IMAGES_URL?>fblogin.gif"></a>
				
				<h4 style="color:#A8518A;margin-top:50px;">Invite from</h4>
				<a href="javscript:void(0)" onclick="showPlaxoABChooser('recipient_list', '/pl_cb.php')"><img src="<?=IMAGES_URL?>invite_g.png"></a>
				<a href="javscript:void(0)" onclick="showPlaxoABChooser('recipient_list', '/pl_cb.php')"><img src="<?=IMAGES_URL?>invite_y.png"></a>
				
				
				<a id="cws_fb_getdata" class="cws_fancylink" href="<?=site_url("fbinviteforbp")?>"></a>
			</div>
			</div>
		</form>
		</div>
		<div>
			<h4 class="head">My Referrals</h4>
			<table width="100%" cellpadding=7 cellspacing=0 style="background:#fafafa;">
				<tr style="background:#ddd">
					<th>Name</th><th>Reward Coupon</th><th>Coupon given on</th>
				</tr>
				<?php foreach($referrals as $r){?>
				<tr class="trans_row">
					<td><?=$r['name']?></td>
					<td><?=$r['ncoupon']?></td>
					<td><?=date("d/m/y",$r['time'])?></td>
				</tr>
				<?php }?>
			</table>
		</div>
	</div>
	
	
</div>	

</div>
</div>

<a href="#invitefb_lastcont" id="invitefb_last"></a>
<div style="display:none">
<div id="invitefb_lastcont" style="width:500px;">
<h2>Please enter your message</h2>
<div style="padding:10px 5px;">
<textarea id="invitefb_msg" style="width:450px;height:120px;">I invite you to join Snapittoday.com, a one stop destination for all your fashion, cosmetics & beauty products.</textarea>
</div>
<div align="right">
<input type="button" value="Invite my Facebook friends" onclick='final_invitefb_proc()' style="font-size:120%;">
</div>
</div>
</div>

<style>
.trans_row td{
border-bottom:1px dashed #aaa;
}
.p_head{
	padding:5px;
	background:#efefef;
	border-bottom:1px solid #aaa;
}
.profbots{
background:#E8EFF1;
color:#000;
height:300px;
border:1px solid #bbb;
}
.profbots .head{
border-bottom:1px solid #bbb;
background:#E2F6CB;
font-weight:bold;
font-size:120%;
color:#559F2E;
padding:20px;
}
.profbots .cont{
padding:10px 20px;
}
.tabscount{
width:170px;
border-radius:10px;
color:#fff;
font-weight:bold;
font-size:300%;
text-align:center;
padding:30px 0px;
}
.tabscount div{
font-size:12px;
}
.proftable td{
vertical-align:top;
}
.eachcashbk{
margin:5px;padding:7px;
background:#f9f9f9;
border:1px solid #aaa;
float:left;
width:285px;
height:87px;
}
.eachcashbk h4 a{
 float:right;color:#44a;font-size:11px;
 font-weight:normal;
 }
.eachcashbk h4{
MARGIN:-5px;
margin-bottom:5px;
background:#ddd;
padding:5px;
}
.eachcashbk div{
font-size:12px;
padding-bottom:5pxx;
padding-left:10px;
}
#cashbacks h4.head{
background:#606060;
color:#fff;
padding:5px;
margin-top:10px;
}
.ui-tabs .ui-tabs-nav li.ui-tabs-selected
{
padding:0px;
}
.dashbrd{
margin-bottom:10px;
}
.dashbrd td{
vertical-align:top;
}
#profile h3{
border-bottom:2px solid #aaa;
border-radius:15px 15px 0px 0px;
font-size:100%;
padding:10px 20px;
background:rgb(245, 245, 245);
}
#profile .cont{
background:#F1F6F8;
margin-bottom:20px;
}
#profile .update{
padding:10px 20px;
background:#D6E9F1;
}
.ui-widget-header
{
border:0px solid #aaa !important;
}
.ui-tabs .ui-tabs-nav{
background:transparent !important;
}
.ui-tabs .ui-tabs-nav li a
{
color:#fff;
}
.ui-tabs-selected
{
background:#777 !important;
font-weight:bold;
}

.ui-tabs .ui-tabs-nav li.ui-tabs-selected a{
font-weight:bold;
}
.ui-tabs-selected a{
color:#fff !important;
}
.ui-tabs .ui-tabs-nav li a
{
padding-left:23px;
padding-right:26px;
}
.ui-tabs .ui-tabs-nav li a div
{
font-size:75%;
}
.ui-state-default, .ui-widget-content .ui-state-default{
background:#eee;
}
.ui-tabs .ui-tabs-nav li{
margin:0px;
border-radius:0px;
border:0px;
border-right: 1px solid #aaa;
border-bottom:1px solid #aaa !important;
background:#000;
min-width:145px;
}
.ui-state-active, .ui-widget-content .ui-state-active {
border:0px;
}
.ui-widget-content{
border-radius:0px;
border:1px solid #aaa;
}
#favs tr td{
border:1px dashed #ccc;
}
#favs tr td:first{
}
#favs tr td table td{
border:0px;
}
.dashbrd ul li{
background:#000;
}
.dashbrd ul li a{
padding:8px 30px !important;
}
#tabs ul li img{
float:left;
margin-bottom:-3px;
margin-right:3px;
}
</style>
<script>
var bpid=0,cws_min=0,cws_loaded_once=0,fbs=[];

var fb_loggedin=0;

function cws_done(selectedcoworkers,cws_emails)
{
	if(selectedcoworkers.length==0 && cws_emails==0)
	{
		alert("You haven't selected any coworkers");
		return;
	}
	$.fancybox.showActivity();
	pst="bpid="+bpid+"&cws="+selectedcoworkers.join(",")+"&emails="+cws_emails.join(",");
	$.post("<?=site_url("jx/extendbp")?>",pst,function(){
		location.reload(true);
	});
}
function setbpid(a_bpid)
{
	bpid=a_bpid;
}

function final_invitefb_proc()
{
	$.fancybox.close();
	$.fancybox.showActivity();
	$.post('<?=site_url("jx/invitefbfriends")?>','fbs='+fbstr+"&msg="+$("#fancybox-content #invitefb_msg").val(),function(){
		$.fancybox.hideActivity();
		alert("Your friends are invited. Thanks for inviting!");
	});
}

function cws_fb_done(selfbs)
{
	fbs=[];
	for(i=0;i<selfbs.length;i++)
		fbs.push(selfbs[i][0]);
	fbstr=fbs.join(",");
	if(fbs.length==0)
		alert("You haven't selected any friends");
	else
	$("#invitefb_last").click();
}
window.fbAsyncInit = function() {
	   FB.init({
		    appId  : '<?=FB_APPID?>',
		    status : true, // check login status
		    cookie : true, // enable cookies to allow the server to access the session
		    oauth  : true // enable OAuth 2.0
		  });
	  FB.getLoginStatus(function(response) {
		  if (response.authResponse) {
			  fb_loggedin=1;
		  } else {
			  fb_loggedin=0;
		  }
		});
};


function cws_fblogin()
{
	if(fb_loggedin==1)
	{
		$.fancybox.showActivity();
		$("#cws_fb_getdata").click();
		return;
	}
	 FB.login(function(response) {
		   if (response.authResponse) {
			   $.fancybox.showActivity();
			   $("#cws_fb_getdata").click();
		   } else {
			  alert("Please login to your Facebook Account to invite your friends");
		   }
		 }, {scope: 'email,publish_stream'});
}


var direct_fb_loading=true;
var cws_min=0;

$(function(){
	d=document;
    var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
    js = d.createElement('script'); js.id = id; js.async = true;
    js.src = "//connect.facebook.net/en_US/all.js";
    d.getElementsByTagName('head')[0].appendChild(js);

	cws_loaded_once=1;

	window.scrollTo(0,0);

	$("#cws_fb_getdata, #invitefb_last").fancybox();

	$("#pr_invitebp_trig").fancybox({
		'onComplete':function(){initcs();return false;}
	});

	$(".pr_invmore").click(function(){
		$("#pr_invitebp_trig").click();
	});
	$("#tabs").tabs();
	$("#pro_cpass").submit(function(){
		if($("input[name=password]",$(this)).val()=="")
		{
			alert("Enter new password");
			return false;
		}
		if($("input[name=password]",$(this)).val()!=$("input[name=cpassword]",$(this)).val())
		{
			alert("Passwords are not same");
			return false;
		}
		return true;
	});

	if(location.hash!="")
		$("#tabs ul li a[href="+location.hash+"]").click();

	$(".profile_popup a").click(function(){
		setTimeout(function(){
			$("#tabs ul li a[href="+location.hash+"]").click();
		},500);
	});

	$(".faqs li .q:first").click();
});
</script>
<div  style="display:none;">
<div id="chnpss_cont" style="padding:10px">
<h3>Change Password</h3>
					<form action="<?=site_url("changepwd")?>" method="post" id="pro_cpass">
<table width=400 cellpadding=5>
<tr>
						<td>New Password :</td><td><input type="password" name="password"></td>
</tr>
<tr>
						<td>Confirm Password :</td><td><input type="password" name="cpassword"></td>
</tr>
<tr>
						<td></td><td><input type="submit" value="Update"></td>
						</tr>
</table>					
					</form>
</div></div>
<div id="fb-root"></div>