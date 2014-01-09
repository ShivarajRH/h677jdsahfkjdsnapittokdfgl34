<style>
.bigad{
border:6px solid #ccf;
padding:5px;
margin:7px;
font-size:13px;
width:650px;
margin-left:30px;
background:#fafafa;
}
.bigad div.img, .bigad div.desp{
padding:3px;
float:left;
clear:right;
}
.bigad div.desp a.title{
color:blue;
text-decoration:none;
font-weight:bold;
}
a.title b{
color:black !important;
}
.bigad div.desp a.title:hover{
text-decoration:underline;
}
img{
border:0px;
}
</style>
<div class="c-fb-cont">
<?php if(isset($deal) && !empty($deal)){$ad=$deal;?>
<div class="bigad">
<div class="img"><a href="http://test2.via.com/index.php/hotel/view/<?=$ad['hotel_id']?>" target="_blank"><img src="http://test2.via.com/resources/images/hotel_deal_images/thumbs/<?=$ad['image_name']?>.jpg"></a></div>
<div class="desp">
<div class="title"><a class="title" href="http://test2.via.com/index.php/hotel/view/<?=$ad['hotel_id']?>" target="_blank"><b><?=$ad['hotel_name']?></b><br/><?=$ad['hotel_deal_title']?></a></div>
<div style="padding-top:10px;font-size:12px;"><?=$ad['hotel_deal_desc']?></div>
</div>
<div style="float:right;">
<a href="http://test2.via.com/index.php/hotel/viewDeal/<?=$ad['hotel_deal_id']?>" target="_blank"><img src="<?=base_url()?>images/book.png"></a>
</div>
<div style="clear:both;font-size:1px;">&nbsp;</div>
</div>
<script>
$(function(){
	$(".c-fb-cont .c-fb-button, .c-fb-cont .c-fb-button-selected").click(function(){
		h=$(this).attr("href");
		$("#share_form input[name=id]").val("<?=$ad['hotel_deal_id']?>");
		$("#share_form").attr("action",h).submit();
		return false;
	});
});
</script>
<form id="share_form" method="post">
<input type="hidden" name="id" value="">
</form>
<?php }?>
		<div align="left" style="padding:5px;">
<?php if(!isset($deal)){?>
		<div style="float:right"><fb:bookmark /></div>
			<a class="c-fb-button<?php if($this->uri->segment(2)=="invite") echo "-selected";?>" href="<?=site_url("fb/invite")?>">Send requests to friends</a>
<?php }?>
			<a class="c-fb-button<?php if($this->uri->segment(2)=="invite_fr") echo "-selected";?>" href="<?=site_url("fb/invite_fr")?>">share with your friends</a>
			<a class="c-fb-button<?php if($this->uri->segment(2)=="invite_wall") echo "-selected";?>" href="<?=site_url("fb/invite_wall")?>">Share in your wall</a>
		</div>
<div class="my_xfbml" style="width:760px;">
<?=$print?>
</div>
<div style="margin:10px;">
<h4 style="font-size:16px;padding-bottom:3px;">Benefits of sharing</h4>
<div style="padding-left:7px;">
You get via points for sharing, which can be redeemed to buy any products/tour package from VIA
<br/>Points are allotted in following ways
</div>
<div align="left" style="margin-top:10px;">
<style>
.ptable{
font-size:13px;
}
.ptable tr td{
padding:3px;
border-bottom:1px solid #fff;
}
.ptable tr td:first-child{
padding-left:10px;
}
.ptable .qty{
vertical-align:center;
font-weight:bold;
font-size:14px;
}
.ptable .example{
font-size:12px;
width:250px;
}
.ptable tr.last td{
border:0px !important;
}
</style>
<table class="ptable" cellpadding="5" style="height:250px;width:700px;background:url(<?=base_url()?>/images/back_blue.png)">
<tr>
<td colspan="4" style="font-weight:bold;font-size:15px;padding:7px;" align="center">you get one via point for the following actions</td>
</tr>
<tr>
<td>For every </td>
<td class="qty"><?=$ptsys['referrals']?></td>
<td>referrals (new user) brought to ViaBazaar</td>
<td class="example">So, if <?=($ptsys['referrals']*4)?> new users signed up with your invitation link, you get 4 points</td>
</tr>
<tr>
<td>For every </td>
<td class="qty"><?=$ptsys['invites']?></td>
<td>invitation requests sent to your friends</td>
<td class="example">So, if you sent <?=$ptsys['invites']*5?> invitation requests, you get 5 points</td>
</tr>
<tr>
<td>For sharing through your wall of</td>
<td class="qty"><?=$ptsys['shares']?></td>
<td>friends</td>
<td class="example">So, if you have <?=$ptsys['shares']*5?> friends, sharing in your own wall will give 5 points</td>
</tr>
<tr>
<td style="width:195px">For writing in your friends' wall for</td>
<td class="qty"><?=$ptsys['friend_shares']?></td>
<td>times</td>
<td class="example">So, if you write in <?=$ptsys['friend_shares']*2?> friends' wall, you get 2 points</td>
</tr>
<tr class="last">
<td>For every</td>
<td class="qty"><?=$ptsys['rupees']?></td>
<td>rupees you are spending in ViaBazaar</td>
<td class="example">So, if you bought a product/travel package for Rs <?=$ptsys['rupees']*16?>, you get 16 points</td>
</tr>
</table>
</div>
</div>
</div>
<?php
