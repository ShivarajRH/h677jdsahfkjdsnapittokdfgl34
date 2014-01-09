<?php $user=$this->session->userdata("user"); ?>
<div style="background:#fff;padding:0px;">
<div class="container">

<div id="tabs" class="dashbrd">
	<div class="head">
		<h2 style="float:right">Dashboard</h2>
		<div  style="float:right;padding-right:20px;">
			<a class="green" style="font-weight:bold;font-size:12px;" href="<?=site_url("profile")?>">My Account</a>
		</div>
		<h1>
			<div style="float:left"><?=$user['corp']?></div>
			<?php if(!$user['verified']){?>
				<a href="<?=site_url("getverified")?>" style="font-size:12px;color:red;">Unverified Account! <span style="font-size:70%">(Please click here)</span></a>
			<?php }else{?>
				<div class="green verifiedcont">Verfied Account</div>
			<?php }?>
		</h1>
	</div>
	<div class="clear" style="padding-top:5px;"></div>
	<div id="dashboard" style="padding:0px;">
		<table width="100%" style="border-top:2px solid #aaa;" cellpadding="0" cellspacing="0">
		<tr>
			<td width="220" style="border-right:2px solid #bbb;background:#f0f0f0;padding:5px;padding-top:10px;">
				<div class="me">
					<table cellpadding=3>
						<tr>
							<td>
								<?php if($profile['pic']!=""){?>
									<img width="90" src="<?=IMAGES_URL?>people/<?=$profile['pic']?>.jpg">
								<?php }else{?>
									<img width="90" src="<?=IMAGES_URL?>default_people.png">
								<?php }?>
							</td>
							<td>
								<h3><?=$user['name']?></h3>
								<div style="font-size:12px;padding-top:7px;"><?=$profile['designation']?><br><?=$user['corp']?></div>
							</td>
						</tr>
					</table>
				</div>
				<h3 style="margin-top:25px;">
				<a href="<?=site_url("jx/viewcoworkers")?>" style="display:none;float:right;font-size:80%;" class="fancylink">view all</a>
				Coworkers (<?=$coworkerslen?>)
				</h3>
				<div>
					<table width="100%" cellpadding=5 cellspacing=0>
							<?php foreach($coworkers as $i=>$c){?>
							<tr onclick='showCoworker(<?=$i?>)' class="cw_infoshw">
								<td width=60>
												<?php if($c['pic']!=""){?>
													<img width="60" src="<?=IMAGES_URL?>people/<?=$c['pic']?>.jpg">
												<?php }else{?>
													<img width="60" src="<?=IMAGES_URL?>default_people.png">
												<?php }?>
								</td>
								<td style="vertical-align:middle"><b><?=$c['name']?></b></td>
							</tr>
							<?php }if(empty($coworkers)){?>
							<tr>
							<td align="center" style="padding:20px;"><a href="<?=site_url("profile")?>#invite" style="color:blue;"><img src="<?=IMAGES_URL?>invite_cw_menu.png"></a></td>
							</tr>
							<?php }?>
					</table>
				</div>
			</td>
			<td>
				<table width="100%" style="margin:10px 10px 10px 10px;padding:10px;background:#E4E4E4;border-radius:15px;">
					<tr>
						<td>
							<div style="margin:20px 10px;">
								<h2>Activity in your network</h2>
								<h3 style="font-weight:normal;font-size:160%;margin-top:10px;">in last 30 days</h3>
							</div>
							<img src="<?=IMAGES_URL?>dashbrd_arrow.png" style="margin-bottom:-30px;">
						</td>
						<td width="10%" align="center" style="padding:0px 20px;">
							<div class="tabscount" style="background:#6D98AB;">
								<?=$coworkerslen?>
								<div>Coworkers</div>
							</div>
						</td>
						<td width="10%" align="center" style="padding:0px 20px;">
							<div class="tabscount" style="background:#00275E;">
								<?=$totalbuy?>
								<div>Products Bought</div>
							</div>
						</td>
						<td width="10%" align="center" style="padding:0px 20px;">
							<div class="tabscount" style="background:#F9E6A4;color:#000;">
								<?=$totalreview?>
								<div>Reviews written</div>
							</div>
						</td>
					</tr>
				</table>
				<div>
					<table width="100%" cellpadding="0" cellspacing="0" class="proftable">
						<tr>
							<td width="50%" class="cw_more_info">
								<?php foreach($coworkers as $i=>$c){?>
									<div class="profbots" id="prof<?=$i?>">
										<div class="head"><span class="cw_more_close">x</span>Coworker Details</div>
										<div class="cont">
											<div style="float:right">
												<?php if($c['pic']!=""){?>
													<img src="<?=IMAGES_URL?>people/<?=$c['pic']?>.jpg">
												<?php }else{?>
													<img src="<?=IMAGES_URL?>default_people.png">
												<?php }?>
											</div>
											<h2><?=$c['name']?></h2>
											<div style="padding-top:5px;margin:10px;">
												<table>
													<tr>
														<td>Department :</td><td><?=$c['department']==""?'<I>not specified</I>':$c['department']?></td>
													</tr>
													<tr>
														<td>Location :</td><td><?=$c['location']==""?'<i>not specified</i>':$c['location']?></td>
													</tr>
												</table>
											</div>
											<div style="margin:10px;">
												<table width="180">
													<tr>
														<td><div class="tabscount" style="height:50px;background:#00275E;font-size:100%;"><div style="font-size:120%"><?=$c['products']?></div>products bought</div></td>
														<td><div class="tabscount" style="height:50px;background:#F9E6A4;color:#000;font-size:100%;"><div style="font-size:120%"><?=$c['reviews']?></div>reviews</div></td>
													</tr>
												</table>
											</div>
											<div>
												<?php if(!$c['item']){?>
													<div style="border-radius:5px;background:#ccc;border:1px solid #fff;padding:5px;"><?=$c['name']?> has no purchases yet</div>
												<?php }else{?>
													<div style="border-radius:5px;background:#ccc;border:1px solid #fff;padding:5px;"><?=$c['name']?> receently purchased <b><?=$c['item']?></b> at <b style="color:green">Rs <?=$c['price']?></b> on <?=date("D d M y",$c['lastbuy_on'])?></div>
												<?php }?>
											</div>
										</div>
									</div>
								<?php }?>
							</td>
							<td width="50%" class="purchases">
								<div class="profbots" style="padding:0px;background:#fff;">
									<div class="head">Recently purchased by your coworkers</div>
									<div class="cont" style="padding:0px;background:#fff;">
										<?php foreach($purchases as $i=>$p){?>
											<div class="recpurch">
												<div class="trig">
												<b style="color:#00275E"><?=$p['name']?></b> <span class="arrow">&raquo;</span>
												<div> <span style="float:right;color:#ff9900;">Rs <?=$p['price']?></span><span>on <?=date("D d M",$p['lastbuy_on'])?></span> <b><?=$p['coworker']?></b></div>
												</div>
												<div class="dealdet" style="margin-top:-<?=($i*45)+10+($i*2)?>px;">
													<a href="javascript:void(0)" style="color:blue;float:right;text-decoration:none;" class="xdealdet">x</a>
													<img src="<?=IMAGES_URL?>items/<?=$p['pic']?>.jpg" width="150" style="margin-right:10px;float:left">
													<h2><?=$p['name']?></h2>
													<div style="color:#ff9900;font-weight:bold;font-size:120%;margin-top:10px;"><b>Rs <?=$p['price']?></b></div>
													<a href="<?=site_url("{$p['url']}")?>" style="display:block;width:79px;background:url(<?=IMAGES_URL?>viewbutton.png) no-repeat;color:blue;text-decoration:none;padding:2px 5px;margin-top:10px;height:20px;float:left;">view offer</a>
													<div style="float:left">Co-worker <b style="color:#0093DD;font-size:130%"><?=$p['coworker']?></b> recently purchased this<br> @ <b style="color:#00923F;">Rs <?=$p['price']?></b> on <?=date("D d M",$p['lastbuy_on'])?></div>
												</div>
											</div>
										<?php }if(empty($purchases)){?>
											<h3 style="padding:10px;">Hmm.. Its time to make your first purchase and fill this space</h3>
										<?php }?>
									</div>
								</div>
							</td>
							<td width="50%">
								<div class="profbots" style="background:#ffF;">
									<div class="head" style="background:#55C114;color:#fff;">Looking to buy something else?</div>
									<div class="cont lk_cont">
										<div style="font-size:10px;margin:10px 5px;">
											<table cellpadding=3>
												<tr>
													<td style="vertical-align:middle;padding-left:0px;"><img src="<?=IMAGES_URL?>lk_require.png"></td>
													<td>Create<br>Requirement</td>
													<td style="vertical-align:middle;padding-left:10px;"><img src="<?=IMAGES_URL?>lk_invite.png"></td>
													<td>Invite<br>Coworkers</td>
													<td style="vertical-align:middle;padding-left:10px;"><img src="<?=IMAGES_URL?>lk_deal.png"></td>
													<td>Get your<br>Deal</td>
												</tr>
											</table>
										</div>
										<form id="lk_form">
										<div>
										<span style="float:right;font-size:70%;">(Ex : Nokia N72, iPod 4GB,etc)</span>
											What are you looking to buy?
											<textarea name="product" style="width:100%;height:60px;"></textarea>
										</div>
										<div>
											When do you plan to?
											<table>
												<tr>
													<td><label><input type="radio" name="when" value="thisweek"> this week</label></td>
													<td><label><input type="radio" name="when" value="nextweek" checked="checked"> next week</label></td>
													<td><label><input type="radio" name="when" value="thismonth"> this month</label></td>
												</tr>
											</table>
										</div>
										<div>
											<span class="lk_invite" style="float:right"><a id="invite_cw" href="<?=site_url("inviteforbp")?>"><img src="<?=IMAGES_URL?>invite_coworkers.png"></a></span>
											Are Coworkers interested?
										</div>
										<div style="padding:10px 2px;clear:both;" align="right">
											<input type="hidden" name="uids" id="lk_uids" value="">
											<input type="hidden" name="emails" id="lk_emails">
											<input type="image" src="<?=IMAGES_URL?>submit_req.png">
										</div>
										</form>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		</table>
	</div>
</div>	

</div>
</div>
<style>
</style>
<script>
$(function(){
	$(".cw_infoshw").hover(function(){
		if(!$(this).hasClass("cw_infosel"))
			$(this).addClass("cw_infohvr");
	},function(){
			$(this).removeClass("cw_infohvr");
	}).click(function(){
		$(".cw_infoshw").removeClass("cw_infosel");
		$(this).addClass("cw_infosel");
	});
	$("#lk_form").submit(function(){
		if(!is_required($("[name=product]",$(this)).val()))
		{
			alert("Please enter product details you are looking to buy");
			return false;
		}
		pst=$(this).serialize();
		$.post("<?=site_url("jx/lookingto")?>",pst,function(){
			alert("Your requirement added. We will try our best to get this deal. Thanks!");
			return false;
		});
		return false;
	});
	$("#invite_cw").fancybox({
		'onComplete':function(){cws_min=0;initcs();return false;}
	});
	$(".recpurch").hover(function(){
		if(!$(this).hasClass("recselected"))
			$(this).addClass("rechover");
	},function(){
			$(this).removeClass("rechover");
	});
	$(".recpurch .trig").click(function(){
		if($(this).parent().hasClass("recselected"))
				return false;
		$(".recpurch").removeClass("recselected");
		$(".dealdet").hide();
		$(".dealdet",$(this).parent()).show("fast");
		$(this).parent().addClass("recselected");
	});
	$(".xdealdet").click(function(e){
		e.preventDefault();
		$(this).parent().hide();
		return false;
	});
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
	$(".cw_more_close").click(function(){
		$(".cw_more_info").hide();
		$(".purchases").show();
	});
});

function showCoworker(r)
{
	$(".purchases").hide();
	$(".cw_more_info").show();
	$(".cw_more_info .profbots").hide();
	$(".cw_more_info #prof"+r).show();
}
function cws_done(selcwrks,emails)
{
	$.fancybox.close();
	uids=[];
	for(i=0;i<selcwrks.length;i++)
		uids.push(selcwrks[i][0]);
	uidstr=uids.join(",");
	emails=emails.join(",");
	$("#lk_emails").val(emails);
	$("#lk_uids").val(uidstr);
}	
</script>
<style>
.cw_more_close{
cursor:pointer;
float:right;
font-weight:bold;
font-size:80%;
color:blue;
}
.dashbrd{
padding:10px 0px;
}
.dashbrd .me{
    background: none repeat scroll 0 0 #FFFFFF;
    margin-top: 9px;
    padding: 5px;
 }
.dashbrd .head{
padding-bottom:10px;
}
</style>
<?php /*?>
<link rel="stylesheet" href="<?=base_url()?>css/grumble.css?v=1">
<script src="<?=base_url()?>js/Bubble.js"></script>
<script src="<?=base_url()?>js/jquery.grumble.js"></script>
*/?>