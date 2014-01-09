<?php

$user=$this->session->userdata("user");

if(isset($itemdetails['enddate']))
$ed=$itemdetails['enddate'];
$item=$itemdetails;
$bps=false;
if($this->session->userdata("bps"))
{
	$bpdata=$this->session->userdata("bps");
	if($item['id']==$bpdata['itemid'])
		$bps=true;
}

?>


<div style="display:none">
	<div id="writereview" style="background:#fff;width:500px;" align="center">
		<h3 align="left">Write a Review</h3>
		<div class="cont">
		<form id="writereviewfrm">
			<input type="hidden" name="itemid" value="<?=$item['id']?>">
			<input type="hidden" name="rating" value="5">
			<table cellpadding=5 style="font-size:120%;padding:10px;margin-left:30px;">
				<tr>
					<td>Rating</td>
					<td class="star_rating"><?php for($i=0;$i<5;$i++){?><img src="<?=IMAGES_URL?>star.png"><?php }?></td>
				</tr>
				<tr><td>Review</td><td><textarea style="width:300px;height:100px;" name="review"></textarea></td></tr>				
				<tr <?php if($this->session->userdata("user")){?>style="display:none;"<?php }?>><td>Your Name</td><td><input type="text" name="uname" size=30 <?php if($this->session->userdata("user")){$user=$this->session->userdata("user");?>value="<?=$user['name']?>"<?php }?>></td></tr>
			</table>
			<div class="update" align="right">
				<input type="image" src="<?=IMAGES_URL?>submit_review.png">
			</div>
		</form>
		</div>
	</div>
</div>

<div id="buyprocesscont" style="display:none;">
	<div id="confirmbp">
		<div class="bp_startf">
			<div class="head">
				Please confirm your buying
			</div>
			<div class="cont">
				<table cellpadding=10 cellspacing=0>
					<tr id="bp_gbinvcont">
						<td>
							<h3>You have invited <span class="bp_gbinvited"></span> coworkers</h3>
							<h4>This deal expires in <span class="blue" style="font-size:120%"><?=$item['bp_expires']/24/60/60?></span> days</h4>
						</td>
						<td>
							<div id="bp_gbinvref" style="font-weight:normal;">When all your coworkers bought this deal,<br>you will get a cashback of <b class="blue" style="font-size:120%">Rs <span class="bp_gbrefund"></span></b> for each quantity you bought</div>
						</td>
					</tr>
					<tr class="wbrdr">
						<td width="65%">
							<h3>You are buying "<?=$item['name']?>"</h3>
						</td>
						<td width="35%">
							<h3>Rs <?=number_format($item['price'])?></h3>
						</td>
					</tr>
					<tr class="wbrdr" id="bp_gbeligi" style="background:#D7EBB9;">
						<td><h3>For yourself, you are getting <span id="bp_gbqty"></span> quantities</h3></td>
						<td><h3>@ Rs <span class="bp_gbeligiprice"></span> / qty</h3></td>
					</tr>
					<tr class="wbrdr">
						<td align="center" style="vertical-align:top;">
						</td>
						<td align="left" style="vertical-align:top;">
							<a href="javascript:void(0)" onclick='startbuyp()'><img src="<?=IMAGES_URL?>continue.png"></a>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	
<?php /*?>	
	<div id="buyprocess">
		<div class="bp_startf">
			<div class="head">
				Shop More - Pay Less
			</div>
			<div class="cont">
				<table cellpadding=10 cellspacing=0>
					<tr>
						<td width="65%">
							<h3>You are buying "<?=$item['name']?>"</h3>
						</td>
						<td width="35%">
							<h3>Rs <?=number_format($item['price'])?></h3>
						</td>
					</tr>
					<tr class="wbrdr" style="background:#D7EBB9">
						<td><h3>Price/Item for selected slot (<span id="bp_gbopt"></span>)</h3></td>
						<td><h3><span id="bp_gbprice"></span></h3></td>
					</tr>
					<tr class="wbrdr">
						<td><h3>Select the number of quantities for yourself</h3></td>
						<td>
						<select id="qty">
					<?php for($i=1;$i<=20;$i++){?>
							<option value="<?=$i?>"><?=$i?></option>
					<?php }?>
						</select>
						</td>
					</tr>
					<tr class="wbrdr">
						<td align="center" style="vertical-align:top;padding-bottom:5px;">
							<div style="padding:5px;font-size:120%;">Invite your coworkers to buy this item<br> and get a cash back of <b style="text-decoration:underline"><span class="bp_gbcashbk"></span>/item</b> you bought</div>
						</td>
						<td align="left" style="vertical-align:top;padding-bottom:5px;">
							<div style="padding:5px;font-size:120%;"><h2 style="margin-left:-100px;margin-bottom:-30px;">Or</h2>Pay now & avail instant discount of <b style="text-decoration:underline"><span class="bp_gbcashbk"></span>/item</b>, if you buy more than <span class="bp_gbmin"></span> quantities</div>
						</td>
					</tr>
					<tr style="background:#eee">
						<td align="center" style="vertical-align:top;">
							<a href="<?=site_url("inviteforbp")?>" id="bp_continue"><img src="<?=IMAGES_URL?>buy_invite.png"></a>
						</td>
						<td align="left" style="vertical-align:top;">
							<a href="javascript:void(0)" onclick='confirmbpthrudirect()'><img src="<?=IMAGES_URL?>buy_checkout.png"></a>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
<?php */?>

	<div id="buyprocess">
		<div class="bp_start">
			<div class="head">You have opted for "<span class="blue"><?=$item['name']?></span>" and chosen to buy quantity of <span id="bp_gbopt"></span> pieces</div>
			<div class="cont">
				<table width="100%" cellspacing=10>
					<tr>
						<td width="50%" class="top_cont">
							<div align="center"><span class="c_head">Option 1</span></div>
							<div class="text">
							<?php if(!$user){?>
							Invite <span class="blue">friends, family and coworkers</span> to shop with you and save with <span class="green">group buying</span>
							<?php }else{?>
							<?php if($user['corpid']==0){?>
							You have signed in as <span class="blue"><?=$user['name']?></span>
							<div style="font-size:80%;padding-top:10px;">Invite <span class="blue">friends, family and coworkers</span> to shop with you and save with <span class="green">group buying</span></div>
							<?php }else{?>
							You have signed in as an employee of <span class="blue"><?=$user['corp']?></span> group<br>You have <span class="blue"><?=$this->dbm->getcoworkerslen($user['userid'],$user['corpid'])?></span> colleagues
							<div style="font-size:80%;margin-top:10px;">Invite your colleagues to shop with you</div>
							<?php } }?>
							</div>
						</td>
						<td width="50%" class="top_cont">
							<div align="center"><span class="c_head">Option 2</span></div>
							<div class="text">Buy the selected quantity yourself and later share it with friends</div>
						</td>
					</tr>
					<tr>
						<td width="50%">
						<?php if(!$this->session->userdata("user")){?>
							<div align="right"><a onclick='gb_redirect_start()' href="javascript:void(0)"><img src="<?=IMAGES_URL?>gb_login_signup.png"></a></div>
						<?php }else{?>
							<div align="right"><a href="<?=$user['corpid']==0?site_url("inviteforbp_nonc"):site_url("inviteforbp")?>" id="bp_continue"><img src="<?=IMAGES_URL?>gb_start.png"></a></div>
						<?php }?>
						</td>
						<td width="50%">
							<div align="right">
								<div style="float:left;font-size:140%;padding-left:10px;">Select Qty : <select id="qty">
					<?php for($i=1;$i<=20;$i++){?>
							<option value="<?=$i?>"><?=$i?></option>
					<?php }?>
						</select>
								</div>
								<a href="javascript:void(0)" onclick='confirmbpthrudirect()'><img src="<?=IMAGES_URL?>gb_checkout.png"></a>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>

</div>

<script>
<?php if($item['enddate']<time()+(3*24*60*60)){?>
var ed=new Date(<?=date("Y,",$ed)?><?=(date("n",$ed)-1)?>,<?=date("j,G,",$ed)?><?=(date("i",$ed)+1-1)?>,<?=(date("s",$ed)+1-1)?>);
<?php }?>
</script>

<div id="video" style="display:none;"></div>

<div class="itemmaincont" align="center">
	
	<div class="topitem" align="center">
	
		<div class='container' style="margin-left:0px;">
				<div class="breadcrumb" align="left">


					<div class="item_disc_links" align="right">
					
<?php if($item['cashback']!=0){?>
						<div title="Get cashback when you order this product" class="cashback">Cashback <b>Rs <?=$item['cashback']?></b></div>
<?php }?>
						<?php if($item['favs']){?><a href="javascript:void(0)" onclick='location="<?=site_url("selectfav/{$item['id']}")?>";'><img src="<?=IMAGES_URL?>prod_add_fav.png"></a><?php }?>
						<img id="item_disc_love" src="<?=IMAGES_URL?>loveit.png">
						<img id="item_disc_tag" src="<?=IMAGES_URL?>snapit.png">
					</div>


					<a href="<?=site_url($item['murl'])?>" class="blue"><?=$item['menu']?></a> &raquo; <a href="<?=site_url($item['murl']."/".$item['caturl'])?>" class="blue"><?=ucwords($item['category'])?></a> &raquo; <a href="<?=site_url($item['burl'])?>" class="blue"><?=ucwords($item['brandname'])?></a>
				</div>
				
				<div class="itempic">
					<div style="width:100%;height:400px;position:relative;overflow:hidden;">
						<a id="zoom1" rel="position: 'cz-cnt'" href="<?=IMAGES_URL?>items/big/<?=$itemdetails['pic']?>.jpg" class="cloud-zoom itemphotos">
							<img title="<?=htmlspecialchars($itemdetails['name'])?>" alt="<?=htmlspecialchars($itemdetails['name'])?>" src="<?=IMAGES_URL?>items/<?=$itemdetails['pic']?>.jpg" style="width:100%;">
						</a>
						<?php 
						$itemresources[0][]=array("id"=>$itemdetails['pic']);
						?>
					</div>
					<div class="rollover">/ Rollover to magnify image</div>
					<div class="viewlarge"><a class="fancylink" href="<?=IMAGES_URL?>items/big/<?=$item['pic']?>.jpg"><img src="<?=IMAGES_URL?>find.png" style="margin-bottom:-5px;"> View larger</a></div>
					<div id="photosvideos" style="clear:left;float:left;">
						<div style="clear:left;float:left;">
							<div style="padding-top:3px; width: 100%; text-align: left;">
								<?php 
								if(count($itemresources[0])>0)
								foreach($itemresources[0] as $pic){?>
								<a class="cloud-zoom-gallery" rel="useZoom: 'zoom1', smallImage: '<?=IMAGES_URL?>items/<?=$pic['id']?>.jpg'" style="padding:3px;" href="<?=IMAGES_URL?>items/big/<?=$pic['id']?>.jpg"><img src="<?=IMAGES_URL?>items/thumbs/<?=$pic['id']?>.jpg" height="50" style="background:#fff;border:1px solid #eee;"></a>
								<?php 
								}
								?>
								<?php 
								if(count($itemresources[1])>0)
									echo '<div style="padding-top:10px;padding-bottom:2px;font-size:18px;border-bottom:2px solid #ccc;">Videos</div>';
								foreach($itemresources[1] as $pic){?>
								<a title="<?=$item['name']?>" href="http://www.youtube.com/watch?v=<?=$pic['id']?>&feature=player_embedded" class="vlink"><img src="http://i3.ytimg.com/vi/<?=$pic['id']?>/default.jpg" width="100" style="border:1px solid #eee;"></a>
								<?php 
								}
								?>
							</div>
						</div>
						<div style="clear:left;font-size:12px;padding:0px 5px;">Note: Images shown are for illustration purpose only actual product packing might change as per supply from manufacturers</div>
					</div>
				
			<div style="float:left;border-right:2px solid #eee;font-size:13px;width:400px;clear:left;padding-right:10px;" align="left">
				<div style="padding-top:10px;font-weight:bold;padding-bottom:5px;font-size:15px;border-bottom:2px solid #ccc;">
					<div align="left" style="margin-top:-7px;float:right;padding-bottom:0px;">
						<div style="float:left;"><iframe src="http://www.facebook.com/plugins/like.php?href=<?=urlencode(site_url($item['url']))?>&amp;send=false&amp;layout=standard&amp;width=90&amp;show_faces=false&amp;action=recommend&amp;colorscheme=light&amp;font&amp;height=25" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:25px;" allowTransparency="true"></iframe></div>
						<div style="float:left;margin:0px 5px;"><g:plusone size="large" count=false></g:plusone></div>
						<div style="float:left;"><iframe frameborder="0" scrolling="no" allowtransparency="true" style="border: medium none; overflow: hidden; width: 50px; height: 25px; vertical-align: top;" src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Fsnapittoday&amp;send=false&amp;layout=button&amp;width=60&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=trebuchet+ms&amp;height=21"></iframe></div>
					</div>
					Description
				</div>
				<div class="resetoverrid" style="text-align: justify;font-size: 110%;padding:10px;">
					<?=$itemdetails['description1']?>
					<div style="padding-left:30px;padding:10px;padding-top:15px;">
						<?=$itemdetails['description2']?>
					</div>
				</div>
			</div>
				
				
				</div>
				
<?php /*?>				

						<div class="itemheading" style="float: right;width: 560px;">
						<div style="margin-top:5px;">
							<?php if($item['menuid']==4 || $item['menuid2']==4){?>
								<img src="<?=IMAGES_URL?>couch_logo.png" style="float:right;margin-left:10px;">
							<?php }?>
							
							<?php if($item['groupbuy']==1){?>
								<img src="<?=IMAGES_URL?>groupenabled.png" style="float:right">
							<?php }?>
							</div>
						</div>
*/ ?>
							<div class="timercont">
<?php if($item['live']){ if($item['enddate']<time()+(3*24*60*60)){?>
								<div class="disch" style="font-weight:normal;margin-top:-5px;font-size:90%;">Deal ends in 
								<div style="display:inline" id="countdown"></div></div>
<?php }?>
<?php }else{?>
<?php }?>
							</div>

				<div class="itempricedet">
					
					<div id="cz-cnt"></div>
					
					
					<div class="pricebar">
						<h1><?=$itemdetails['name']?></h1>
					<table width=100%>
					    <tr>
					        <td width=40%>
					             	<?php if($item['price'] != $item['orgprice']){ ?>
					             <div align="center" class="instantcashback">
					             		<b class="maroon" onmouseout='$("#cashbacktip").hide()' onmouseover='$("#cashbacktip").show()'>Rs <?=$item['price']?><br><img src="<?=IMAGES_URL?>instantcashback.png"> <span style="color:#000;font-size:55%;">(?)</span></b>
										<div id="cashbacktip">
												<div><span>Rs <?=$item['orgprice']?></span>Product price</div>
												<div><span>- Rs <?=$item['orgprice']-$item['price']?></span>Instant Cashback</div>
												<div class="final"><span>Rs <?=$item['price']?></span>Final price on your cart</div>
										</div>
					            </div>
					                <?php } ?>
					            <div align="center" class="pricedet">
					             	<?php if($item['price'] != $item['orgprice']){ ?>
					            		MRP <span style="font-size: 80%">Rs</span> <?=number_format($item['orgprice']).'/-'?>
					            	<?php }else{ ?>
					             		<div align="center" class="instantcashback">
					             			<b class="maroon">Rs <?=$item['price']?></b>
					             			<div style="font-size:50%;margin-bottom:10px;">Inclusive of all Taxes</div>
					             		</div>
					            	<?php }?>
					            </div>
					            <div align="center" style="margin:10px;font-size: 11px;">
					            	<?php if($item['sizing']==0){?>
						               Select Qty : 
										<select id="inst_qty">
										<?php
											$mq=MAX_QTY;
											if($item['max_allowed_qty']!=0)
												$mq=$item['max_allowed_qty'];
											$qty_strict=array("372786323396");
											if(in_array($item['id'], $qty_strict)) $mq=1; 
										?>
										<?php for($i=1;$i<=$mq;$i++){?>
										<option value="<?=$i?>"><?=$i?></option>
										<?php }?>
										</select>
									<?php }else{?>	
										Select Size : <br><select id="sizing" style="display:none;">
										<?php $disp_f_sizes=$disp_sizes=array(); $sizing_rw=explode(":",$item['sizing']);$type=array_shift($sizing_rw);$sizing=explode(",",array_pop($sizing_rw)); if($type=="1"){
											foreach($sizing as $size){ $disp_sizes[]=$size; $disp_f_sizes[]=$size;?>
											<option value="<?=$size?>"><?=$size?></option>
										<?php }}else{
											$sizes=array("Small (S)","Medium (M)","Large (L)","Extra Large (XL)","XXL");
											$s_sizes=array("S","M","L","XL","XXL");
											foreach($sizing as $size){ $disp_sizes[]=$s_sizes[$size]; $disp_f_sizes[]=$sizes[$size]; ?>
											<option value="<?=$sizes[$size]?>"><?=$sizes[$size]?></option>
										<?php }} ?>
										</select>
										<input type="hidden" value="1" id="inst_qty">
										<?php foreach($disp_sizes as $i=>$s){?>
										<input type="button" class="size-but<?=$i==0?" size-but-selected":""?>" onclick="$('#sizing').val('<?=$disp_f_sizes[$i]?>').change();$('.size-but').removeClass('size-but-selected');$(this).addClass('size-but-selected');" value="<?=$s?>">
										<?php }?>
								<?php }?>
					            </div>
					        </td>
					         <td width=60% valign="top" style="border-left:1px solid #ddd">
					            <div class="pricedet_right">
					                <img src="<?php echo IMAGES_URL ?>/truck.png" height=20 />
					                <span style="margin-left:10px;color: #555;position: relative;top:-4px;"> 
						               	 *Free Shipping 
										<?php if($item['price']<=MIN_AMT_FREE_SHIP){?>
												<span style="font-size: 10px;"> | Orders above <b>Rs <?=MIN_AMT_FREE_SHIP?></b></span> 
										<?php }else{
											echo ' Available';
										}?>
									</span>
									<div> Dispatches in <span class="maroon"><?=$item['shipsin']?></span></div>
									
									<div class="instantcocont" align="center">
<?php if(!$item['live']){?>
										<img src="<?=IMAGES_URL?>/ofs.png">
<?php }else if($itemdetails['quantity']<=$itemdetails['available']){?>
										<span style="color:red;font-size:150%;font-weight:bold;">SOLD OUT</span>
<?php }else{?>
										<a class="instant_trig" onclick="instantco()"><div class="icon"></div>Add to cart</a>
<?php }?>
									</div>
									
						
					            </div>
					        </td>
					</tr>
					</table>		
<?php if(!$item['live'] || $itemdetails['quantity']<=$itemdetails['available']){?>
						<div class="notifyme">
							<form id="remindmefrm">
							<input type="hidden" name="id" value="<?=$item['id']?>">
								<div style="font-size:13px;">Notify me when this product is available</div>
								<div>Email : <input size="25" type="text" name="email" <?=$user?'value="'.$user['email'].'"':""?>> <input type="submit" value="Notify Me" class="grad_button"></div>
							</form>
						</div>
<?php }?>
					</div>
					

<?php if(isset($relateds) && !empty($relateds)){?>					
					<div style="margin-top:25px;">
<?php /*?>						<b style="float:right;font-size:80%;">Get upto <?=COMBINED_BUY_DISCOUNT?>% off on buying these relevant products</b>*/ ?>
						<img src="<?=IMAGES_URL?>relevant.png">
					</div>
					<div class="related">
		<?php foreach($relateds as $r){?>			
						<div class="item">
							<div class="img"><img src="<?=IMAGES_URL?>items/thumbs/<?=$r['pic']?>.jpg"></div>
							<div class="select">
								Qty :
								<select class="relbuys" name="<?=$r['itemid']?>">
										<option value="0">None</option>
									<?php for($i=1;$i<=$r['max_allowed_qty'];$i++){?>
										<option value="<?=$i?>"><?=$i?></option>
									<?php }?>
								</select>
							</div>
							<div class="name">
							<a href="<?=site_url($r['url'])?>" target="_blank"><?=$r['name']?></a>
							</div>
							<div class="price maroon">Rs <?=$r['price']?></div>
							<div class="clear"></div>
						</div>
		<?php }?>
						<div class="instantcocont"  style="float:right;">
							<a class="instant_trig instant_trig_rev" onclick="instantco()">Add to cart</a>
						</div>
						<div class="clear"></div>
					</div>
<?php }?>

<?php if($item['groupbuy']==1){?>					
					<div class="groupbuy">
						<div class="head"><span style="float:right">Price/piece</span>Buy Qty</div>
						<div class="cnt">
<!--							<div class="slothead">-->
<!--								<div style="float:right">Price/Piece</div>-->
<!--								<div>Buy Qty</div>-->
<!--							</div>-->
							<div class="slotcont">
								<table width="100%" cellspacing=0>
							    <?php
							     	$slots=unserialize($item['slots']);
							     	$nslots=array();
							     	$nslotprice=array();
							     	if(is_array($slots))
							     	foreach($slots as $sno=>$srs)
							     	{
							     		$nslots[]=$sno;
							     		$nslotprice[]=$srs;
							     	}
							     	if(empty($nslots))
							     		echo "This product is not available for Group Buying";
							     	else{
							    ?>
							    	<?php foreach($nslots as $i=>$n){?>
										<tr class="slot slot<?=($i==0?"1":($nslots[$i-1]+1))?>">
											<td width="5"><input type="checkbox" class="slotsel" value="<?=($i==0?"1":($nslots[$i-1]+1))?>"><input type="hidden" class="uprlimit" value="<?=$nslots[$i]?>"></td>
											<td align="left" class="brac"><?=($i==0?"1":($nslots[$i-1]+1))?>-<?=$nslots[$i]?></td>
											<td align="right" class="bracprice"><b>Rs <span class="val"><?=($i==0?$item['price']:$nslotprice[$i])?></span></b></td>
										</tr>
							    	<?php }?>
							    <?php }?>
								</table>
							</div>
						</div>
					</div>
					
					<div class="addtocart">
						<div align="center"><img src="<?=IMAGES_URL?>buymore.png"></div>
						<div class="text">The more you buy, the lesser you pay. Buy it yourself or invite your community to help you out. You win either way!</div>
						<div style="text-align:center"><img src="<?=base_url()?>images/group_buy.png" class="buyprocessbut"></div>
						<div align="center" class="howitworks"><a href="<?=IMAGES_URL?>sit_how_it_works.png" class="fancylink blue">how it works?</a></div>
						<?php if($bps){?>
						<div align="center" style="padding-top:10px;color:red;font-weight:bold;">Group buying invitation<br>accepted!</div>
						<?php }?>
						<a href="#buyprocess" id="buyprocessa"></a>
						<a href="#confirmbp" id="confirmbpa"></a>
					</div>
<?php }?>
					<div class="spotlight">
						<div class="head">
							<div style="float:right;margin-top:1px;cursor:pointer;" onclick='snapit("<?=$item['id']?>",function(d){$(".snapitnum").html(d);})'><img src="<?=IMAGES_URL?>thumbs.png" style="float:left;margin-right:-5px;margin-top:-2px;"><span class="snapitbutt">snap it</span></div>
							Spotlight <span style="margin-left:20px;color:#444;font-size:80%;font-weight:normal;"><b><?=$item['loves']?></b> Loves, <b class="snapitnum"><?=$item['snapits']?></b> says <i>'snap it!'</i></span>
						</div>
						<div class="cnt" align="center">
<?php /*?>						
							<table width="450" cellpadding=0 cellspacing=0 style="margin-left:40px;font-size:80%">
								<tr>
									<td width=5 class="num"><?=$item['loves']?></td>
									<td>loves</td>
									<td width=5  class="num"><?=$item['buys']?></td>
									<td>buys</td>
									<td width=5 class="num snapitnum"><?=$item['snapits']?></td>
									<td>says<br>snap it!</td>
								</tr>
							</table>
							
*/ ?>							
							<table width="100%">
								<tr>
									<td width="50%">
										<div class="ratingstar">
											<table width="100%">
												<tr>
													<td width="10"><img style="width:30px;padding-right:10px;" src="<?=IMAGES_URL?>tick.png"></td>
													<td>
													<?php $star=$item['ratings']/10; for($i=1;$i<=floor($star);$i++){?>
														<img src="<?=IMAGES_URL?>star.png">
													<?php }?>
													<?php for($i2=0;$i<=5;$i++,$i2++){?>
														<img src="<?=IMAGES_URL?>unstar<?php if($i2==0 && ($star*10)-(floor($star)*10)==5) echo ".5";?>.png">
													<?php }?>
													</td>
												</tr>
											</table>
										</div>
									</td>
									<td width="50%">
										<div class="reviewcount">
											<table width="100%">
												<tr>
								<?php if($item['reviews']>0){?>				
													<td width="10" class="num"><?=$item['reviews']?></td>
													<td>reviews found</td>
								<?php }else{?>
													<td class="num">&nbsp;</td>
													<td>Be the first to write review!</td>
								<?php }?>
												</tr>
											</table>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
<?php if(!$item['groupbuy']){
	$lovers=$this->dbm->getitemloves($item['id']);
	if(!empty($lovers)){?>
					<div class="itemlovers">
						<h4>People with same love</h4>
						<?php foreach($lovers as $l){?>
							<a href="<?=site_url("discovery/user/{$l['username']}")?>"><img src="<?=IMAGES_URL?>people/<?=$l['bpic']!=""?$l['bpic']:$l['ppic']?>.jpg" style="height:70px;max_width:120px;"></a>
						<?php }?>
					</div>
<?php }}?>
					
				</div>
				<div style="clear:both;"></div>
		</div>	
	</div>	

	<div align="center" style="clear:both">
	<div class="container">	
		<div class="bottomitem">
			<div style="padding:0px 7px;border-left:2px solid #eee;margin-left:-2px;">
				<div style="padding-top:10px;margin-bottom:5px;font-weight:bold;padding-bottom:5px;font-size:15px;border-bottom:2px solid #ccc;">Reviews</div>
				<div align="right"><a class="fancylink" href="#writereview" style="color:blue;font-size:90%;">write a review</a></div>
				<div id="reviewscont"></div>
			</div>

<?php /*?>			
			<div style="float:left;font-size:13px;margin-left:10px;width:383px;padding-right:10px;" align="left">
			
				<div style="clear:left;float:left;padding-right: 20px; padding-left: 0px;">
					<div style="padding-top:10px;padding-bottom:2px;font-size:15px;font-weight:bold;border-bottom:2px solid #ccc;">Leave Comments</div>
					<div id="fb-root"></div>
					<script>  window.fbAsyncInit = function() {    FB.init({status: true, cookie: true,             xfbml: true});  };  (function() {    var e = document.createElement('script'); e.async = true;    e.src = document.location.protocol +      '//connect.facebook.net/en_US/all.js';    document.getElementById('fb-root').appendChild(e);  }());</script>
					<fb:comments href="<?=site_url("deal/".$itemdetails['url'])?>" num_posts="2" width="383"></fb:comments>
				</div>
			
			</div>
*/ ?>			
			<div style="clear:both"></div>
		
		</div>
		
		<div class="homecont" style="margin-top:20px;padding-top:10px;">
				<div>
					<div class="bender bc-green">
						<div class="matter">Frequently bought together</div>
						<span></span>
					</div>
				</div>
			<div class="spot_deal_cont">
<?php foreach($extradeals as $i=>$deal){?>	
						<div class="dealdlist" id="faprod<?=$i?>">
							<div class="imgcont">
								<a href="<?=site_url("{$deal['url']}")?>" class="scrollman sm_prodt">
									<img class="scrm_load" src="<?=IMAGES_URL?>scroll_load.gif">
									<span class="scrm_data"><?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg</span>
								</a>
<?php /*?>							<a href="<?=site_url("{$deal['url']}")?>"><img src="<?=IMAGES_URL?>items/small/<?=$deal['pic']?>.jpg" width=160></a>   */ ?>
							</div>
							<div class="namecont">
								<div><a class="at" href="<?=site_url($deal['caturl'])?>" style="text-decoration:none"><?=$deal['category']?></a></div>
								<div style="font-size:95%;padding:5px 0px;"><?=$deal['name']?></div>
							</div>
							<div class="price">
							<b class="maroon">Rs <?=number_format($deal['orgprice'])?></b>
<?php if($deal['orgprice']!=$deal['price']){?>
							<div class="instantcashback"> Rs <?=$deal['price']?> <img src="<?=IMAGES_URL?>instantcashback.png"></div>
<?php }?>
							</div>
							<div class="addcart_quick_cont"><label><input type="checkbox" value="<?=$deal['url']?>" style="display:none;" class="addtocart_quick"> <span>Add to cart</span></label></div>
						</div>
<?php }?>
			<div class="clear"></div>
			</div>
		</div>
		
	</div>
	</div>
</div>
<div align="center">
<h2>Buy now in India online - <?=$itemdetails['name']?> at Rs <?=$itemdetails['price']?></h2>
</div>

<script>var itemid="<?=$itemdetails['id']?>";var itemname="<?=$itemdetails['name']?>";var itempic="<?=$itemdetails['pic']?>";;</script>
<script>
$(function(){
	getreviews(itemid,function(revs){
		$("#reviewscont").html(revs);
	});
	$("#writereviewfrm").submit(function(){
		if(!is_required($("textarea",$(this)).val()))
		{
			alert("Please write your review regarding this product");
			return false;
		}
		if(!is_required($("input[name=uname]",$(this)).val()))
		{
			alert("Please mention your name");
			return false;
		}
		$.fancybox.showActivity();
		pst=$(this).serialize();
		$.post("<?=site_url("jx/writereview")?>",pst,function(){
			$.fancybox.hideActivity();
			$.fancybox.close();
			alert("Thanks for writing review. Your review was submitted.");
			getreviews(itemid,function(revs){
				$("#reviewscont").html(revs);
			});
		});
		return false;
	});
	$("#writereviewfrm input[name=rating]").val("5");
	$(".star_rating img").hover(function(e){
		$(this).nextAll().attr("src","<?=base_url()?>images/unstar.png");
		$(this).prevAll().attr("src","<?=base_url()?>images/star.png");
		$(this).attr("src","<?=base_url()?>images/star.png");
		e.stopPropagation();
	},function(e){
		i=0;
		$(".star_rating img").attr("src","<?=base_url()?>images/unstar.png").each(function(){
			i++;
			$(this).attr("src","<?=base_url()?>images/star.png");
			if(i==$("#writereviewfrm input[name=rating]").val())
				return false;
		});
		e.stopPropagation();
	}).click(function(){
		$(this).nextAll().attr("src","<?=base_url()?>images/unstar.png");
		$(this).prevAll().attr("src","<?=base_url()?>images/star.png");
		$(this).attr("src","<?=base_url()?>images/star.png");
		$("#writereviewfrm input[name=rating]").val($(this).prevAll().length+1);
	});
	
	$(".slotcont .slot").hover(function(){
		$(this).addClass("slothover");
	},function(){
		$(this).removeClass("slothover");
	});
	$(".slotcont .slot").click(function(){
		$(".slot").removeClass("slotselected");
		$(".slotsel").attr("checked",false);
		$(this).addClass("slotselected");
		$(".slotsel",$(this)).attr("checked",true);
	});
	$(".slotsel").attr("checked",false);
	$(".slot:nth-child(2)").click();
	$("#buyprocessa,#confirmbpa").fancybox({margin:"0",padding:"0"});
	$("#bp_continue").fancybox({
		'onComplete':function(){cws_min-=1;initcs();return false;}
	});
	$(".buyprocessbut").click(function(){
<?php if(!$user || $user['verified']==1){?>
<?php if($item['live']){?>
<?php if(!$bps){?>
		if($(".slotsel:checked").length==0)
		{
			alert("Please select a group buying option");
			return;
		}
		uids=[];
		emailar=[];
		fbs=[];
		fbstr=uidstr=emails="";
		minbuyers=parseInt($(".slotsel:checked").val());
		maxbuyers=parseInt($(".uprlimit",$(".slotsel:checked").parent()).val());
		$("#qty").html("");
		for(i=minbuyers;i<=maxbuyers;i++)
			$("#qty").append('<option value="'+i+'">'+i+'</option>');
		$("#bp_gbopt").html($(".slot"+$(".slotsel:checked").val()+" .brac").html());
		$(".bp_gbmin").html($(".slotsel:checked").val());
		slotprice=parseInt($(".slot"+$(".slotsel:checked").val()+" .bracprice .val").html());
		refund=<?=$item['price']?>-slotprice;
		$(".bp_gbcashbk").html("Rs "+refund);
		cws_min=parseInt($(".slotsel:checked").val());
		$("#bp_gbprice").html("Rs "+$(".slot"+$(".slotsel:checked").val()+" .bracprice .val").html());

		$("#buyprocessa").click();
<?php }else{?>
		location="<?=site_url("checkbps/".$item['id'])?>";
<?php }?>
<?php }else{?>
	alert("Stocks not available. Please check back later");
<?php }?>
<?php }else{
			$this->session->set_userdata("vredir",site_url($item['url'])); ?>
	alert("Please get verified your account");
	location="<?=site_url("getverified")?>";
<?php }?>
	});
});

function cws_fb_done(selfbs)
{
	fbs=[];
	for(i=0;i<selfbs.length;i++)
		fbs.push(selfbs[i][0]);
	fbstr=fbs.join(",");
	$("#bp_continue").click();
}

function cws_done(selcwrks,emailsa)
{
	$("#qty").html("<option value='1'>1</option>");
	$("#qty").val("1");
	$.fancybox.showActivity();
	uids=[];
	for(i=0;i<selcwrks.length;i++)
		uids.push(selcwrks[i][0]);
	uidstr=uids.join(",");
	emailar=emailsa;
	emails=emailsa.join(",");
	confirmbp();
}
var instantcheckout=0;
function instantco()
{
<?php if(0){?>
	alert("Please login to buy this product");
<?php }else{?>
<?php if($item['live']){?>
<?php if($bps){?>
location="<?=site_url("checkbps/".$item['id'])?>";
return;
<?php }else{?>
	q=$("#inst_qty").val();
	$("#qty").html("<option value='"+q+"'>"+q+"</option>");
	$("#qty").val(q);
	uids=[];emailar=[];uidstr="";emails="";
	instantcheckout=1;
	$(".relbuys").each(function(i){
		if($(this).val()!=0)
			rbuys.push($(this).attr("name")+"-"+$(this).val());
	});
	startbuyp();
<?php } }else{?>
alert("Stock not available. Please check back later");
<?php }?>
<?php }?>
}
function confirmbp()
{
	startbuyp();
<?php /*?>	
	$("#bp_gbinvcont").show();
	if($("#qty").val()>=minbuyers)
		$(".bp_gbeligiprice").html(slotprice);
	else
		$(".bp_gbeligiprice").html("<?=$item['price']?>");
	if(uids.length==0 && emailar.length==0)
		$("#bp_gbinvcont").hide();
	$("#bp_gbqty").html($("#qty").val());
	if(uids.length==0 && emailar.length==0)
		$(".bp_gbinvited").html("0");
	else
		$(".bp_gbinvited").html(uids.length+emailar.length);
	$(".bp_gbrefund").html(refund);
	$("#confirmbpa").click();
*/?>
}
	function startbuyp(){
		$.fancybox.showActivity();
		rb=rbuys.join(",");
		pst="qty="+$("#qty").val()+"&uids="+uidstr+"&item="+itemid+"&emails="+emails+"&fbs="+fbstr+"&fbemail="+fbemail+"&rbuys="+rb;
		$.post("<?=site_url("jx/startbuyprocess")?>",pst,function(resp){
			bpid=resp.bpid;
			addtocart();
		},"json");
	}
function confirmbpthrudirect()
{
	if(parseInt($("#qty").val())>=minbuyers)
		confirmbp();
	else
		alert("In order to qualify for the selected group buying, you need to buy atleast "+minbuyers+" quantities or invite coworkers");
}

var selectedcoworkers=[],cws_emails=[];

var fb_loggedin=0;

var fbs=[],uids=[],emailar=[],rbuys=[];
var cws_min=0,bpid=0;
var uidstr="",emails="",fbstr="",fbemail="";
var minbuyers=0,maxbuyers=0;
var refund=0,slotprice=0;
</script>
<script>
$(".vlink").click(function() {
	$.fancybox({
			'padding'		: 0,
			'autoScale'		: false,
			'title'			: this.title,
			'width'		: 680,
			'height'		: 495,
			'href'			: this.href.replace(new RegExp("watch\\?v=", "i"), 'v/'),
			'type'			: 'swf',
			'swf'			: {
			   	 'wmode'		: 'transparent',
				'allowfullscreen'	: 'true'
			}
		});

	return false;
});
$(function(){
	$(".relatedbut").click(function(){
		$(".related").toggle();
	});
	$(".relbuys").val("0");
	$(".itempic").hover(function(){
		$("#cz-cnt").show();
	},function(){
		$("#cz-cnt").hide();
		window.setTimeout(function(){$("#cz-cnt").hide();},3000);
	});
	$("#cz-cnt").hover(function(){$(this).hide();},function(){$(this).hide();});
	<?php if(isset($init_gb)){?>
	$("#buyprocessa").click();
	<?php }?>
});


function gb_redirect_start()
{
	$.fancybox.showActivity();
	$.post("<?=site_url("jx/gb_redirect")?>","url=<?=$item['url']?>",function(){
		location="<?=site_url("signup")?>";
	});
}
var cws_loaded_once=0;

</script>
<style>
#writereview h3{
border-bottom:2px solid #aaa;
border-radius:15px 15px 0px 0px;
font-size:100%;
padding:10px 20px;
background:rgb(245, 245, 245);
}
#writereview .cont{
background:#F1F6F8;
}
#writereview .update{
margin-top:20px;
padding:10px 20px;
background:#D6E9F1;
}
.bp_gbcashbk{
white-space:nowrap;
}
.homecont table td {
padding:20px 0px;
}
#fancybox-outer
{
}
.spot_deal_cont .dealdlist{
width:222px;
}
body{
background:#fff;
}
</style>
