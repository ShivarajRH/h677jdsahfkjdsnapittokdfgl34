<style>
body{
background:#fff;
}
</style>

<?php 

$countrylist = array('india');

$user=$this->session->userdata("user");
$coupon=$this->session->userdata("coupon");

$cities=$this->dbm->getshipcity($this->cart->contents());
$a=false;
if(!empty($user))
	$a=$addrdet=$this->db->query("select email,name,address,state,city,pincode,landmark,telephone,country from king_users where userid=?",$user['userid'])->row_array();

$mrp_total=0;
$prods=array();
foreach($this->cart->contents() as $rid=>$item)
{
	$prod=$this->db->query("select price,is_coupon_applicable,is_giftcard,i.max_allowed_qty,i.url,i.pic,i.id,d.catid,d.brandid,i.orgprice as mrp,i.shipsto from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['id'])->row_array();
	$shipsto=$prod['shipsto'];
	$prod['qty']=$item['qty'];
	$prod['cart_price']=$item['price'];
  $prod['rowid']=$rid;
	$mrp_total+=$prod['mrp']*$item['qty'];
	$prods[]=$prod;
}

foreach($prods as $prod)
{
  if($prod['price']==$prod['cart_price'])
    continue;
  foreach($this->cart->contents() as $rid=>$item)
    if($rid==$prod['rowid'])
      break;
  $item['options']=$this->cart->product_options($rid);
  $this->cart->update(array("rowid"=>$rid,"qty"=>0));
  $this->cart->insert($item);
}

$fav_items=array();
if(!empty($user))
	$fav_items=$this->dbm->getfavsforuser($user['userid']);




$total_applicable_mrp = 0;
$savings_list = array();	
$total_cart_savings = 0;
$fav_totalsavings = 0;
$reorder_totalsavings = 0;
$instant_cashback_totalsavings = 0;
$fivefavs=false;
$reorders = false;
$instant_cashback = false;
$c_total=$cd_total=0;
$brands=$cats=array();
$total=$this->cart->total();
if($coupon)
{
	/* $c_total=$total;
	if($coupon['mode']==1)
		$c_total=$mrp_total;

	$cats = array();
	$brands = array();
	if($coupon['catid']!="")
	{
		$cats=explode(",",$coupon['catid']);
	}
	if($coupon['brandid']!="")
	{
		$brands=explode(",",$coupon['brandid']);
	}

	
	$c_total=0;
	foreach($prods as $p)
	{
		$disc_bycoup = 0;
		if($p['is_coupon_applicable'])
		{
			
			if(in_array($p['catid'], $cats))
			{
				$disc_bycoup = 1;
			}
			
			if(in_array($p['brandid'], $brands))
			{
				$disc_bycoup = 1;
			}
			
			if($coupon['catid']=="" && $coupon['brandid']=="")
			{
				$disc_bycoup = 1;
			}
		}
		if(!$disc_bycoup)
		{
			if(($p['mrp']-$p['price']) > 0)
			{
				$instant_cashback = true;
				$instant_cashback_totalsavings += ($p['mrp']-$p['price'])*$p['qty'];
				$total_applicable_mrp +=  (($coupon['mode']==1)?$p['mrp']:$p['price'])*$p['qty'];
				
			}
			
		}
			$c_total+=($coupon['mode']==1?($p['mrp']*$p['qty']):($p['price']*$p['qty']));
		 
		
	}
	if($coupon['type']==0)
	{
		$cd_total=$c_total-$coupon['value'];
		if($cd_total<0)
			$cd_total=0;
		$total1 = $instant_cashback_totalsavings;
	}
	elseif($coupon['type']==1){
		$c_total = abs($c_total-$total_applicable_mrp);
		$cd_total=$c_total-floor(($c_total)*$coupon['value']/100);
		$total1 = $instant_cashback_totalsavings;
	}
	
	if($coupon['mode']==1)
	{
		$total = $mrp_total+$cd_total-$c_total-$total1;
		if($total>$this->cart->total()){
			$instant_cashback = true;
			$instant_cashback_totalsavings = $total-$this->cart->total();
			$total = $this->cart->total();
		}
		
	}
	else{
		 
		$total=$this->cart->total()+$cd_total-$c_total;
		if(($mrp_total-$c_total) > 0){
			$instant_cashback = true;
			$instant_cashback_totalsavings = $mrp_total-$c_total;
			$total = $this->cart->total();
		}
		
	}
	
	
	// Overide $cd_total  to handle
	$cd_total = $instant_cashback_totalsavings;
	
	$total=$c_total-$cd_total; */
	
	
	//echo $c_total.'-'.$cd_total.'-'.$total_applicable_mrp;
	
	//exit; 
	
	
	
 
	
	//echo $coupon['type'];
	
	/* $cart_total = $c_total;
	$total_amount = $total;
	$total_amount_bymrp = $total_applicable_mrp;
	
	echo $total.'<br>'.$c_total;
	echo '<br>'.$cd_total.'<br>'.$instant_cashback_totalsavings;
	
	$snap_coupon = $c_total-$cd_total;
	
	exit; */
	
	
	$cats=$brands=array();
	$mrp_total=$dtotal=$c_mrp_total=$c_price=0;
	$prods=array();
	foreach($this->cart->contents() as $item)
	{
		$prod=$this->db->query("select d.is_giftcard,d.is_coupon_applicable,i.id,i.pic,i.url,d.catid,d.brandid,i.orgprice as mrp,i.shipsto from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['id'])->row_array();
		$shipsto=$prod['shipsto'];
		$prod['qty']=$item['qty'];
		$prod['price']=$item['price'];
		$mrp_total+=$prod['mrp']*$item['qty'];
		if($prod['is_coupon_applicable']==1)
		{
			$c_mrp_total+=$prod['mrp']*$prod['qty'];
			$c_price+=$prod['price']*$prod['qty'];
		}
		
		
		$prod['c_disc'] = 0;
		$prods[]=$prod;
	}
	
	$c_total=$c_price;
	if($coupon['mode']==1)
		$c_total=$c_mrp_total;
	
	if($coupon['catid']!="")
	{
		$cats=explode(",",$coupon['catid']);
		$c_total=$c_price=0;
		foreach($prods as $k=>$p)
			if($p['is_coupon_applicable']==1 && in_array($p['catid'], $cats))
			{
				$c_total+=($coupon['mode']==1?($p['mrp']*$p['qty']):($p['price']*$p['qty']));
				$c_price+=($p['price']*$p['qty']);
				$prods[$k]['is_coupon_applicable'] = 1;
				 
			}else{
				$prods[$k]['is_coupon_applicable'] = 0;
			}
	}
	
	if($coupon['brandid']!="")
	{
		$brands=explode(",",$coupon['brandid']);
		$c_total=$c_price=0;
		foreach($prods as $k=>$p)
			if($p['is_coupon_applicable']==1 && in_array($p['brandid'], $brands))
			{
				$c_total+=($coupon['mode']==1?($p['mrp']*$p['qty']):($p['price']*$p['qty']));
				$c_price+=($p['price']*$p['qty']);
				$prods[$k]['is_coupon_applicable'] = 1;
			}else{
				$prods[$k]['is_coupon_applicable'] = 0;
			}
	}
	
	
	$total_instant_cb = 0;
	foreach($prods as $k=>$p){
		if($p['is_coupon_applicable']==0){
			$total_instant_cb +=  (($p['mrp']-$p['price'])*$p['qty']);
		}
	}
	
	if($total_instant_cb){
		$instant_cashback = true;
		$instant_cashback_totalsavings = $total_instant_cb;
	}
	
	
	if($coupon['type']==0)
	{
		$cd_total=$c_total-$coupon['value'];
		if($cd_total<0)
			$cd_total=0;
	}
	elseif($coupon['type']==1)
	$cd_total=$c_total-ceil($c_total*$coupon['value']/100);
	
	
	
	
		
	
	 
	
	$total=$this->cart->total()-$c_price+$cd_total;
	
	
	 
	
	 
	
	
	
	if($total>$this->cart->total()){
		$instant_cashback_totalsavings = ($mrp_total-($c_total-$cd_total)-$this->cart->total());
		$total=$this->cart->total();
	}	
	
	if($instant_cashback_totalsavings)
		$instant_cashback = true;
		
	
	
	 
	  

}
else
{
	
	$cd_total=$c_total=0;
	$favs=$this->dbm->getallfavids();
	foreach($prods as $prod)
	{
		 
		$item_saving = 0;
		$item_det = $this->dbm->get_dealdet($prod['id']);
		if($item_det['is_giftcard'])
		{
			$savings_list[$prod['id']] =  array('gift',$item_saving);
		}
		else 
		{	
			if(in_array($prod['id'], $favs))
			{
				$fivefavs=true; 
				
				$item_saving = ($prod['mrp']*FAV_DISCOUNT/100);
				$default_saving = ($prod['mrp']-$prod['price']);
				
				if($item_saving < $default_saving){
					//$item_saving=$default_saving;
					$item_saving_diff = $default_saving-$item_saving;
					$instant_cashback_totalsavings += $item_saving_diff;
				}	
				
				$item_saving = $item_saving*$prod['qty']; 
				$fav_totalsavings += $item_saving;
				$savings_list[$prod['id']] =  array('fav',$item_saving);
				
			}
			elseif($this->dbm->is_reorder($prod['id']))
				{
					$reorders=true;
					$item_saving = ($prod['mrp']*REORDER_DISCOUNT/100);
					$default_saving = ($prod['mrp']-$prod['price']);
					
					
					if($item_saving < $default_saving){
						//$item_saving=$default_saving;
						$item_saving_diff = $default_saving-$item_saving;		
						$instant_cashback_totalsavings += $item_saving_diff; 
					}			
					
					$item_saving = $item_saving*$prod['qty'];
					$reorder_totalsavings += $item_saving;
					$savings_list[$prod['id']] =  array('reorder',$item_saving);
				}
				else
				{
					$instant_cashback_totalsavings += $item_saving = ($prod['mrp']-$prod['price'])*$prod['qty']; 
					$savings_list[$prod['id']] =  array('instant_cashback',$item_saving);
					$instant_cashback=true;
				}
		
		}
		
		$c_total+=$prod['mrp']*$prod['qty'];
		
		$cd_total+=$item_saving;
		
	}
	 
	
	// Overide $cd_total  to handle 
	$cd_total = $instant_cashback_totalsavings+$reorder_totalsavings+$fav_totalsavings;
	
	$total=$c_total-$cd_total;
	
	
	//print_r($savings_list);
	
}


 

		
	  if($total>$this->cart->total()){
			$total=$this->cart->total();
	  }	
	
    
if($total<0)
	$total=0;

$total = ceil($total);

//echo $cd_total;

//echo $c_total.' - '.$cd_total;


$my_favs=$this->dbm->getallfavids();
$cart_prod_ids = array(); 
foreach($prods as $prod)
{
	array_push($cart_prod_ids,$prod['id']);
}

$is_all_favs_loaded = 1;
foreach($my_favs as $fav_prod)
{
	if(!in_array($fav_prod,$cart_prod_ids))
	{
		$is_all_favs_loaded = 0;
		break;
	}
} 


$this->session->set_userdata('is_favsloaded',$is_all_favs_loaded);


$points_total = $this->dbm->getpointsys($this->cart->total());
$next_points_total = $this->dbm->getnextpoints($this->cart->total());



?>



<?php 
$fss_avail=false; 
//if($this->dbm->isfsavailable($total)){
	//$fss_avail=true; 
	$fsconfig=$this->dbm->getfsconfig($total);
	$fss=$this->session->userdata("fsselected");
	if(!empty($fss))
		$fss=explode(",",$this->session->userdata("fsselected"));
	if(empty($fss))
		$fss=0;
	else $fss=count($fss);

	
	//get available free samples 
	$this->db->where('available',1);
	$this->db->order_by('min','asc');
	$freesamples=$this->db->get('king_freesamples')->result_array();
	//echo $this->db->last_query();
	if(count($freesamples))
		$fss_avail=true;
		
//}

	
	$this->session->set_userdata('total_cartval',$total);

if(!$user){
	$this->session->unset_userdata('checkoutstat',0);
}


?>


<div class="container shoppingcart" style="font-size:13px;min-height:100px;background:#fff;margin:10px 0px 20px 0px;border:none;background: url(<?php echo base_url().'images/cart-bg.png';?>) no-repeat scroll -4px 0px; ">

<div style="margin:10px 0px 10px 0px;padding:15px;">




<div style="padding:10px;padding-right:0px;float: right;margin-right: 10px;margin-top: 5px;">
<a href="javascript:void(0)" onclick="scrollto_last_actv()" class="checkout_buttn"></a>
</div>
<div style="padding:10px;">
	<b style="float: right;margin-top: 5px;"><a href="<?php echo site_url('');?>" style="text-decoration: none;color: #008CD3">&lt;&lt; Continue Shopping</a> &nbsp;OR </b>
	<img src="<?=IMAGES_URL?>cart/cartpage.png" title="Your Shopping cart - Free Shipping" alt="Your Shopping cart - Free Shipping"> 
	
</div>

<div style="border-top:1px solid #FF8F22;margin: 10px 0px;">

</div>
<?php /*?>
<div style="float:left"><h1><img height=22 src="<?=IMAGES_URL?>cart.png" style="float:left;padding-right:10px;padding-left:5px;"> Your Shopping Cart</h1></div>
<div style="float:left;margin-left:80px;white-space:nowrap;">
	<img src="<?=IMAGES_URL?>truck.png" style="float:left;margin-right:5px;"><h2 class="blue">Free Shipping</h2>
	<div style="clear:left;float:right;font-size:65%;color:#aaa;">for orders above Rs <?=MIN_AMT_FREE_SHIP?>
</div>
</div>
*/?>

<style type="text/css">
	#fs_validation_text{
		background: none repeat scroll 0 0 #FFFFA0;
	    color: #CD0000;
	    display: block;
	    font-size: 12px;
	    margin: 5px 0;
	    padding: 8px;
	    text-align: center;
	}
	.star{
		margin-left: 1px !important;
	}
</style>


<div class="leftcont" style="width:700px">

<div class="cartcont">

<?php 
$items=$this->cart->contents();
if(count($items)==0)
{?>
	<div style="padding:40px;">
	<div style="font-size:160%;"><b>Your cart is empty!</b></div>
	<div>Please add products to the cart to make the checkout , <a href="<?php echo site_url('')?>">Click here</a> to continue shoppping</div>
	</div>
	<script>cartlinks();</script>
<?php }else{ ?>
<style>
#carttab th,#carttab td{
text-align: center;
padding:5px;
}
#carttab th{
padding:5px;
}
.leftcont {
    float: left;
    padding: 5px 5px 20px;
    width: 710px;
}
.shoppingcart .offers {
    float: right;
    margin-right: 10px;
    padding-top: 10px;
    width: 220px;
}
.cart_itemdata td{
	border-bottom: 1px solid #e3e3e3;
}
#total_order_value{
	padding:2px;
	font-weight: bold;	
	
	color:#cd0000;
}
</style>

<div style="padding:5px;">

	<div >
		<h3 style="background: none repeat scroll 0 0 #D8E3F0 !important;
    font-size: 13px;
    font-weight: normal;
    padding: 5px 10px;
    text-align: left;">
		Total <b style="font-size: 16px;"><?php echo count($this->cart->contents());?></b>  Items In Cart  
	</h3>
	</div>
	<table width="100%" style="border:0px solid #eee;color:#767272;border:1px solid #E3E3E3" id="carttab" cellpadding=0 cellspacing=0>
	
<?php 

$qty_strict=array("372786323396");









$has_onlygiftcard = 0;
$has_giftcard_with_products = 0;
$total_gifts = 0;
$total_incart = count($this->cart->contents());

$total_giftcard_amount = 0;



$reorder_savings = 0;

$carti=0;
foreach($this->cart->contents() as $k=>$item)
{
	$prod=$prods[$carti];
	$additional_msg = '';
	
	
	
		// check if giftcard 
		$gc_item_det = $this->dbm->get_dealdet($item['id']); 
		if($gc_item_det != false)
		{
			if($gc_item_det['is_giftcard'])
			{
				$total_giftcard_amount += $prod['mrp']*$item['qty'];
				$total_gifts += 1;
			}
			
			if($coupon)
			{
				if(!$prod['is_coupon_applicable'])
				{
					if($prod['is_giftcard'])
					{
						$additional_msg = '<div class="green" align="left" style="margin-top:10px;font-size:11px;color:#cd0000">Coupon Code is not applicable for this Gift Voucher</div>';
					}
					else 
					{
						$additional_msg = '<div class="green" align="left" style="margin-top:10px;font-size:11px;color:#cd0000">Coupon Code is not applicable for this Item</div>';
					}
				}
			}
			else
			{
				$saving_det = $savings_list[$prod['id']];
				if($saving_det[0] == 'instant_cashback')
				{
					if($saving_det[1])
						$additional_msg = '<img src="'.IMAGES_URL.'instantcashback_txt.png" /> (Rs. '.$saving_det[1].')';
					else 
						$additional_msg = '';
				}
				else if($saving_det[0] == 'reorder')
				{
					$additional_msg = 'Reordering Item';
				}else if($saving_det[0] == 'fav')
				{
					$additional_msg = '<b>Your Favourite 5 item</b> (Rs. '.$saving_det[1].')'; 
					 
				}else if($saving_det[0] == 'gift')
				{
					$additional_msg = 'Gift Voucher <span style="color:#444">( Email Delivery )</span>'; 
					 
				}
				
				if(!$prod['is_coupon_applicable'] && $this->session->flashdata("coupon_errorcode"))
				{
					if($prod['is_giftcard'])
					{
						$additional_msg .= '<div class="green" align="left" style="margin-top:10px;font-size:11px;color:#cd0000">Coupon Code is not applicable for this Gift Voucher</div>';
					}
					else
					{
						$additional_msg .= '<div class="green" align="left" style="margin-top:10px;font-size:11px;color:#cd0000">Coupon Code is not applicable for this Item</div>';
					}
				}
				
			}	
			
			
		}
	 
	
	
	
	$pic=$prod['pic']; 
	?>
		<tr class="cart_itemdata">
			<td align="center" width=200>
				<img src="<?=IMAGES_URL?>items/small/<?=$pic?>.jpg" style="max-width:200px;max-height:90px;">
			</td>
			<td style="text-align: left;" width="300">
				<div style="padding:10px 0px">
				<a href="<?=site_url($prod['url'])?>" class="itemurl"><?=$item['name']?></a>
				<?php 
					if($additional_msg)
							echo '<div class="green" align="left" style="margin-top:10px;font-size:11px;"><b>'.$additional_msg.'</b></div>'; 
				?>
				<?php 
					if($this->cart->has_options($item['rowid'])){
						$opts=$this->cart->product_options($item['rowid']);
						if($this->db->query("select 1 from king_buyprocess where bpid=?",$opts['bpid'])->num_rows()>1)
							echo '<div style="color:#ff9900;">Group buying enabled</div>';
						if(isset($opts['sizing']))
							echo '<div class="green" align="center">Selected Size : <b>'.$opts['sizing'].'</b></div>';
					}	
					$shipsto=$prod['shipsto'];
					if($shipsto!="")
						echo '<div style="color:red">Shipping only to '.$shipsto.'</div>'	
				?>
				</div>
			</td>
			<td width="70">
				<form id="cartupform">
					<input name="id" type="hidden" value="<?=$item['rowid']?>">
					<select name="qty">
						<?php
							$mq=MAX_QTY;
							if($prod['max_allowed_qty']!=0)
								$mq=$prod['max_allowed_qty'];
							if($mq<$item['qty'])
								$mq=$item['qty'];
							if(in_array($item['id'], $qty_strict)) $mq=1; 
						?>
						<?php for($i=1;$i<=$mq;$i++){?>
						<option value="<?=$i?>" <?=$i==$item['qty']?"selected":""?>><?=$i?></option>
						<?php } ?>
					</select>
				</form>
				<a href="javascript:void(0)" onclick='removeProd(this)' title="Remove" class="removelink">Remove</a>
			</td>
			<td>&nbsp;</td>
			<td width="80" style='font-size:120%;'>
				Rs. <?=(number_format($prod['mrp']*$item['qty']))?>
			</td>
		</tr>
		 
		
<?php
$carti++;
}


if($total_incart  == $total_gifts){
	$has_onlygiftcard = 1;
}else if($total_incart  > $total_gifts && $total_gifts > 0){
	$has_giftcard_with_products = 1;
}




?>
	</table>
<script>
$("#cartupform select").change(function(){
	$.fancybox.showActivity();
//	inp=$(this).parent().serialize();
	inp="id="+$("input[name=id]",$(this).parent()).val()+"&qty="+$(this).val();
	$.post("<?=site_url("deals/editqty")?>",inp,function(data){
		cart_updated=true;
		$("#cartlink").click();
	});
});

function checkoutstat(status){
	if(status)
		 
		//$('.couponcont').css('opacity','0.5');
		//$('#couponcode').attr('readonly',true);
		//$('#applycoupon').unbind('click');
		//$('.couponcont .buttonstyle1').hide();
		$.post("<?=site_url("jx_checkoutstat")?>",'status='+status,function(){
			
		});
}

var bypass = 0;
var onload = 1;
function load_contbox(cntboxid,ele,status){
	//if(!onload && status == 2){
		if(!validate_recipient_det()){
			return false;
		}
	//}
	
	var stat = 1;
	if(!bypass){
		if($('.fs_sels').length){
			if(!$('.fs_sels:checked').length){
				//alert("\n Alas, how can you miss a free sample of branded products,\nIncrease your Total-cart value and choose a brand products sample FREE.");
				stat = 1;
			}
		}
	}
	if(stat){
		$('#'+cntboxid).show();
		$(ele).parent().hide();
		checkoutstat(status);
	}
	
}

</script>
<table width="100%" style="margin-top:5px;">
<tr>
<td width="60%" valign="top">
<div class="couponcont" style="background: #F7F7F7;border:0px solid #e3e3e3;padding:10px;">
<div class="head" style="font-size: 16px;background: #D8E3F0;padding:10px;-moz-border-radius:5px 5px 0px 0px">
	<span title="snapcoupon codes or Your cashbacks can be used." style="float: right;font-size: 11px;color: #FFF;font-weight: bold;padding:5px;-moz-border-radius:10px;background: #aaa;cursor: pointer;">?</span>
	<div align="center">
		<img src="<?php echo IMAGES_URL.'/use-snapcoupon.png'?>" style="vertical-align:middle;" >
	</div>
</div>

<?php if($this->session->flashdata("couponmsg")){?>
			<div style="color:color: #cd0000;"><b><?=$this->session->flashdata("couponmsg")?></b></div>
<?php } ?>
<?php if(!$coupon){?>
			<div style="text-align:left;color:#008CD3;font-weight:bold;padding:10px 0px">
				<a title="Search cashbacks in your profile" href="<?=site_url("profile#cashbacks")?>">
					<img src="<?=IMAGES_URL?>find.png">
				</a>
				<input type="text" id="couponcode" class="inputbox" style="width: 200px;margin-left: 10px;"> 
				<a style="position: relative;top:5px;" href="javascript:void(0)" id="applycoupon">
					<img src="<?=IMAGES_URL?>apply.gif"> 
				</a>
				<p style="font-size: 11px;font-weight: normal;margin: 0px;margin-top: 10px;">
					<span style="color: #cd0000">Please note:</span> <br />When you apply coupon,instant cashback will be removed.
				</p>
			</div>	
<?php }else{?>
			<div style="text-align:left;padding:10px;font-size: 13px;">
				Applied Coupon : <b><?=$coupon['code']?></b>
				<a href="javascript:void(0)" class="buttonstyle1" onclick='clearcoupon();cart_updated=true;' style="font-size:10px;padding:3px;">Remove</a>
			</div>
<?php } ?>
</div>
			<?php  /*if($mrp_total!=$this->cart->total()){?>
				<div  style="font-weight:bold;margin-top:10px;font-size: 18px;">
					Total saved : <span class="blue">Rs <?=number_format($mrp_total-$total)?></span>
				</div>
			<?php }  */?>
			<br />
		<div style="padding:10px;border:1px dotted #ccc;width: 250px;font-size: 14px;background: #E9F9B8">
		 	<span style="background:url(<?php echo base_url().'/images/gift-wrap-icon.png'?>) no-repeat scroll 0px 0px;padding:10px 18px;;"></span>
			<input type="checkbox" id="is_giftwrap"  value="1" style="float: right;" />
			Gift wrap my order.  <br >
			<p style="margin: 0px;padding:0px;font-size: 10px;margin-left: 30px;">( Rs<?php echo GIFTWRAP_CHARGES;?> Extra would be added to your cart )</p>
		</div>	

</td>
<td>

<div class="ordersummary" style="background: #e7e7e7;border:0px solid #e3e3e3;">
<div class="head" style="font-size: 16px;background: #D8E3F0;padding:10px;-moz-border-radius:5px 5px 0px 0px">Order Summary</div>
	<table width="100%" style="clear:both;color: #666666" cellpadding=12 cellspacing=0 >
		
<?php if($this->cart->total()){ ?>
		<tr style="background: #f7f7f7;font-weight: bold;">
			<td style="text-align:left;width: 120px;font-size: 12px;border-bottom: 1px dotted #ccc">
				Subtotal
			</td>
			<td style="text-align:right;font-size:12px;padding-left:0px;padding-right:20px;border-bottom: 1px dotted #ccc;" align="right">Rs <?=number_format($mrp_total)?></td>
		</tr>
<?php }?>
<?php 
	if($fivefavs){
?>
		<tr style="background: #f7f7f7;font-weight: bold;">
			<td style="text-align:left;font-size: 12px;width: 120px;border-bottom: 1px dotted #ccc">5FAVs Savings</td>
			<td style="text-align:right;font-size:12px;padding-right:20px;padding-left:0px;border-bottom: 1px dotted #ccc">&#8212; Rs <?=number_format(floor($fav_totalsavings))?></td>
		</tr>
<?php }?>
<?php 
	if($reorders){
?>
		<tr style="background: #f7f7f7;font-weight: bold;">
			<td style="text-align:left;font-size: 12px;width: 120px;border-bottom: 1px dotted #ccc">Reorder Savings</td>
			<td style="text-align:right;font-size:12px;padding-right:20px;padding-left:0px;border-bottom: 1px dotted #ccc">&#8212; Rs <?=number_format(floor($reorder_totalsavings))?></td>
		</tr>
<?php }?>

<?php if($coupon){?>
		<tr style="background: #f7f7f7;font-weight: bold;">
			<td style="text-align:left;width: 120px;font-size: 12px;border-bottom: 1px dotted #ccc">
				<img src="<?php echo IMAGES_URL.'snapcoupon_txt.png'?>" >
			</td>
			<td style="text-align:right;font-size:12px;padding-right:20px;padding-left:0px;border-bottom: 1px dotted #ccc">
				&#8212; Rs <?=number_format($c_total-$cd_total)?>
			</td>
		</tr>
<?php } ?>		





<?php if($instant_cashback && $instant_cashback_totalsavings>0){

	
	?>
		<tr style="background: #f7f7f7;font-weight: bold;">
			<td style="text-align:left;font-size: 12px;border-bottom: 1px dotted #ccc;">
				<img src="<?php echo IMAGES_URL.'instantcashback_txt.png'?>" >
			</td>
			<td style="text-align:right;font-size:110%;padding-left:0px;padding-right:20px;border-bottom: 1px dotted #ccc;" align="right">&#8212; Rs <?=number_format($instant_cashback_totalsavings)?></td>
		</tr>
<?php }?>
<tr id="gc_charges" ct=<?php  echo ($has_onlygiftcard?$total:(($total<MIN_AMT_FREE_SHIP&&$total)?$total+SHIPPING_CHARGES:$total)); ?> style="background: #f7f7f7;font-weight: bold;display: none;">
	<td style="text-align:left;width: 120px;font-size: 12px;border-bottom: 1px dotted #ccc">GiftWrap Charges</td>
	<td style="text-align:right;font-size:12px;padding-right:20px;padding-left:0px;border-bottom: 1px dotted #ccc">
		Rs <?php echo GIFTWRAP_CHARGES;?>
	</td>
</tr>
<?php 
 	
	$cart_shipping_charge = 0;
if(($total-$total_giftcard_amount<MIN_AMT_FREE_SHIP && $total-$total_giftcard_amount > 0 ) ){
	
	if($coupon['value'] < MIN_COUPON_VAL_FOR_NOSHIPCHARGE){
		$cart_shipping_charge = SHIPPING_CHARGES;
?>
		<tr style="font-weight:bold;background: #f7f7f7;">
			<td align="left" style="border-bottom: 1px dotted #ccc;width: 120px;">Shipping Charges<div style="font-size:90%;font-weight:normal;">as the billing amount is less than Rs <?=MIN_AMT_FREE_SHIP?></div></td>
			<td align="right" style="font-size:12px;padding-right:20px;padding-left:0px;border-bottom: 1px dotted #ccc">Rs <?=SHIPPING_CHARGES?></td>
		</tr>
<?php }
}
?>

		<tr style="background:#D8E3F0;font-size:130%;color: #008CE2">
			<td colspan="2" style="text-align:right;font-size:120%;font-weight:bold;padding-right:15px;width: 100px" >
				<span style="float: left">Cart Total</span> 
				<b>Rs <span id="ct_amount">
						<?php
							if($has_onlygiftcard){
								echo number_format($total);
							}else{
								
								$ttl_computed_amount = 0;
								 if($total!=$total_giftcard_amount && ($total-$total_giftcard_amount < MIN_AMT_FREE_SHIP) && $coupon['value'] < MIN_COUPON_VAL_FOR_NOSHIPCHARGE){
								 	$ttl_computed_amount = $total+SHIPPING_CHARGES;
								 }else{
								 	$ttl_computed_amount = $total;
								 }
								
								 echo number_format($ttl_computed_amount);
								
							}
						?>
					 </span>
				</b>
			</td>
		</tr>
		<?php 
			if(count($points_total)){
		?>
			<tr style="background: #f7f7f7;font-weight: bold;">
				<td style="text-align:left;width: 130px;font-size: 11px;border-bottom: 1px dotted #ccc;font-weight: normal;background: #ffffe0;" colspan=2>
					<?php 
						if(count($points_total))
							echo ' You get <b style="font-size:12px;color:#7AC143">'.number_format($points_total['points']).' Loyalty Points  </b> on Checkout 
									<a href="#loyalty_points_grid" style="color:#000;font-size:130%;text-decoration:none;margin-left:10px;" class="q_tip"> (?) </a>
									';
						
							$point_sys = $this->db->get('king_points_sys')->result_array();	
							if(isset($point_sys[0])){
								echo '<div id="loyalty_points_grid" title="Loyalty Points" style="position:absolute;margin-top:5px;display:none;width:255px;margin-left:-10px;;background:#fafafa;padding:5px;border:1px solid #e3e3e3">
										<p style="font-size:10px">
											Get more points by increasing your cart value.
										</p>
										<table border=1 cellpadding=5 cellspacing=0 style="font-size:11px;" width=100%>
											<thead>
												<th>Min Amount (Rs) </th>
												<th>Loyalty Points</th> 
											</thead>
											<tbody>
										';
								$lamt = 0;
								$ttl_points_listed = count($point_sys); 
								foreach ($point_sys as $p_i => $ps){
									
									if(!$ps['points']){
										$lamt = $ps['amount'];
										continue ; 
									}	
									
									/*if($p_i+1 == $ttl_points_listed)
										$p_str = 	' &gt; '.$lamt;
									else
										$p_str = 	$lamt.' - '.$ps['amount'];*/
									
									echo '<tr>
											<td>'.$ps['amount'].'</td>
											<td>'.$ps['points'].'</td>
										</tr>';
									$lamt = $ps['amount'];
									
								}
									echo '</tbody>
											</table>
										';
								echo '</div>';
							}
						
					?>
				</td>
			</tr>	
		<?php } ?>
		<?php 
			if(count($next_points_total) && 0){
		?>	
			<tr>
				<td colspan="2" style="padding:2px">		
					<?php 
						if(count($next_points_total)){
							
					?> 
						<p style="margin: 5px 0px;font-weight: normal;padding:5px;"><span >Increase your cart value to <b style="font-size:13px;">Rs <?php echo $next_points_total['amount']?></b> to avail <b style="font-size:13px;"><?php echo $next_points_total['points']?>  Loyalty Points</b></span></p>
					<?php } ?>
				</td>
			</tr>
		<?php 		
			}
		?>
	</table>

</div>

</td>
</tr>
</table>

<script type="text/javascript">
	$('.q_tip').hover(function(){
		$($(this).attr('href')).fadeIn();
	},function(){ 
		$($(this).attr('href')).fadeOut();
	}).click(function(e){
		e.preventDefault();
		//$('#loyalty_points_grid').dialog('open');
	});
	//$('#loyalty_points_grid').dialog({autoOpen:false,modal:true,width:350,buttons:{'Close':function(){$(this).dialog('close')}}});
</script>

<?php 
if($has_onlygiftcard ||$has_giftcard_with_products){
	
	$gc_recp_det = $this->session->userdata('gc_recp_details');
 
 
	$gc_recp_name = (isset($gc_recp_det['name'])?$gc_recp_det['name']:'');
	$gc_recp_email = (isset($gc_recp_det['email'])?$gc_recp_det['email']:'');
	$gc_recp_mob = (isset($gc_recp_det['mob'])?$gc_recp_det['mob']:'');
	$gc_recp_msg = (isset($gc_recp_det['msg'])?$gc_recp_det['msg']:'');
	 
?>
<div style="padding:5px;background: #FFFFD2;border:1px solid #e3e3e3;margin-top: 10px;">
	<h3 style="padding:5px;">Please provide Recipient Details for Sending Gift-Voucher </h3>
	<form id="recipient_dets" onsubmit="return false;">
		<table cellpadding=5 cellspacing=5>
			<tr>
				<td><b>Recipient Name<span class="star">*</span></b></td>
				<td><input type="text" class="inputbox" style="width: 200px;" name="recp_name" value="<?php echo $gc_recp_name;?>">
			</tr>
			<tr>
				<td><b>Recipient Email-ID<span class="star">*</span></b></td>
				<td><input type="text" class="inputbox" style="width: 200px;" name="recp_email" value="<?php echo $gc_recp_email;?>">
			</tr>
			<tr>
				<td><b>Recipient Mobileno<span class="star">*</span></b></td>
				<td><input type="text" class="inputbox" maxlength="10" style="width: 200px;" name="recp_mobile" value="<?php echo $gc_recp_mob;?>">
			</tr>
			<tr>
				<td valign="top">
					<b>Message to Recipient<span class="star">*</span></b>
					<p style="font-size: 10px;margin: 0px;padding:0px;">Keep message short and sweet </p>
				
				</td>
				<td><textarea rows="3" class="inputbox" style="background-color:#FFF;height: 60px;width: 500px;" cols="45" name="recp_msg"><?php echo $gc_recp_msg;?></textarea>
			</tr>
		</table>
	</form>
</div>
<?php } ?>
	
<?php if($fss_avail){
	?>
	<div class="cart_select_freesamples" style="padding:10px;">
	<div>
		<b>Choose your </b>	
		<?php 
			if(isset($fsconfig['limit'])){
		?>
			<span style="font-size: 18px;color: #EF8320;font-weight: bold;margin-left: 3px;"><?=$fsconfig['limit']?></span>
			<b style="float:right"><span id="fsselected_disp"><?=$fss?></span> of <?=$fsconfig['limit']?> selected</b>
		<?php } ?>	
			<img style="margin-bottom:-5px;" src="<?=IMAGES_URL?>cart/freesamples.png">
			<span id="fs_validation_text" style="margin-left: 10px;font-size: 12px;color: #cd0000"></span>
	</div>
	<br />
	<div>
	 
	<table class="freesamples_sel" cellspacing=5 cellpadding=3>
	<tr>
	<?php foreach($freesamples as $i=>$fs){?>
	<td width="175" align="center">
		<div align="left">
			<input type="checkbox" class="fs_sels" value="<?=$fs['id']?>">
		</div>	
		<img src="<?=IMAGES_URL?>items/thumbs/<?=$fs['pic']?>.jpg" style="margin: 20px" >
		<br />
		<div><?=$fs['name']?></div>
	</td>
	<?php if(($i+1)%4==0) echo "</tr><tr>"; }?>
	</tr>
	</table>
	<p style="color: #cd0000;margin:3px;font-weight: bold;font-size: 11px;">
		* All free samples are subject to availability at the time of shipping. 
	</p>
	</div>
	</div>
<?php }?>
	
	
	<div style="margin-top:10px;background:#F0F0F1;padding:10px 5px;padding-top:10px;">
		<a style="float:right;margin-top: -5px" 
			<?php 
				if($user){
			?>
					href="#checkoutaddress" 
					onclick="load_contbox('checkoutaddress',this,2)"  
			<?php }else{
			?>
					href="#userauth_cont" 
					onclick="load_contbox('userauth_cont',this,1)"
			<?php 	
			} ?>
			class="continue_buttn scrollPage"></a>
		<a style="color:#fff;text-decoration:none;visibility: hidden;"  href="<?=base_url()?>"><img src="<?=IMAGES_URL?>continueshopping.png"></a>
	</div>
	<div class="clear"></div>
</div>	

<script>
cartlinks();
$(".cartfancylink").fancybox();
$("#applycoupon").click(function(){
	if(!is_required($("#couponcode").val()))
	{
		alert("Enter coupon code");return false;
	}
	$.post("<?=site_url("jxcoupon")?>","coupon="+$("#couponcode").val(),function(){
		cart_updated=true;
		$("#cartlink").click();
	});
});


function fssaved()
{
	$("#cartlink").click();
}
function removeProd(o)
{
	$("select[name=qty]",$(o).parent().parent()).html('<option value="0">0</option>').val(0).change();
}


var total_fs_avial = parseInt($('.fs_sels').length);
var limit_fs = parseInt(<?php echo (isset($fsconfig['limit'])?$fsconfig['limit']:0); ?>);

$('.fs_sels').click(function(){
		
		$('#fs_validation_text').html("Updating Please wait...").show();
		$.fancybox.showActivity();
		upd_chkout_fss($(this));
});





function upd_chkout_fss(ele)
{ 
	s = ele.val();
	$.fancybox.showActivity();
	$.post(site_url+"jx_savefs",{fsids:s},function(resp){

		if(resp.status!=1){
			ele.attr('checked',false);
		}
		
		$('#fs_validation_text').html(resp.message).show();
		$.fancybox.hideActivity();
		$("#fsselected_disp").html(resp.total_selected);
	},'json');
}


$('#fs_validation_text').html("").hide();

</script>
<?php }?>

</div>

<a id="checkout" name="checkout"></a>

<form action="<?=site_url("checkout/step3")?>" method="post" id="c2form">

<input type="checkbox" name="is_giftwrap"  value="1" style="display: none;" /> 
<div id="userauth_cont" style="display:none">
<?php if(!$user){?>
<div style="font-size: 16px;background: #D8E3F0;padding:15px;-moz-border-radius:10px 10px 0px 0px" class="head">
	<b>Please enter your email address to get status of orders. </b>
	<span class="c_help" " title="Please provide below details to get status of your orders.">?</span>
</div>
 
<div class="useraccount">
	<div id="get_regis_text">
		It seems that you dont have <b>snapittoday.com account</b>, Please create new password. 
	</div>
	<table width=100%>
	<tr>
	<td><b>Your Email-Id</b><span class="star">*</span></td>
	<td>
		<input type="text" name="email" class="mand chkot_email inputbox" value="<?php if(!empty($user)) echo $user['email'];?>">
		<input style="display: none;" type="button" value="Submit" id="chkot_email_check">
	</td>
</tr>
	<tr class="chkot_pass"><td>Password<span class="star">*</span></td><td><input type="password" class="inputbox" style="width:203px"  name="password"></td></tr>
	<tr class="chkot_pass"><td>Confirm Password<span class="star">*</span></td><td><input type="password" class="inputbox" style="width:203px" name="cpassword"></td></tr>
	</table>
</div>

<style>
.addrcont,.chkot_pass{display:none;}
</style>
<?php }else{?>
	<input type="hidden" name="email" value="<?=$user['email']?>">
<?php }?>

<div align="right" style="background:#F0F0F1;padding:20px;">
	<a href="#checkoutaddress" style="padding:14px 74px" onclick='validate_emailinp(this)'  class="continue_buttn scrollPage"></a>
</div>

<div class="clear"></div>
</div>

<script type="text/javascript">
	function validate_emailinp(ele){
		$('#chkot_email_check').trigger('click');
	}
</script>

<div id="checkoutaddress" style="display: none;">





<div style="font-size: 16px;background: #D8E3F0;padding:15px;-moz-border-radius:10px 10px 0px 0px" class="head">
	<b>Please enter 
		<?php 
			if($has_onlygiftcard){
				echo 'billing address';
			}else{
				echo 'shipping address';
			}
		?>
	</b>
	<span class="c_help" title="Please enter billing/shipping details.">?</span>
</div>

	<?php 
			if($has_onlygiftcard){
				
			}
	?>		

<div class="addrcont">
 
<table id="shippingaddr" class="addrtable" cellpadding=4>
<input type="hidden" name="telephone">
<input type="hidden" name="bill_telephone">

<input type="hidden" name="gc_recp_name" value="">
<input type="hidden" name="gc_recp_email"value="">
<input type="hidden" name="gc_recp_mobile"value="">
<input type="hidden" name="gc_recp_msg" value="">



<tr>
	<td>Name<span class="star">*</span></td><td><input type="text" name="person" class="mand inputbox" value="<?=$user?htmlspecialchars($a['name']):""?>"></td>
</tr>
<tr>
<td>Address<span class="star">*</span></td><td><input type="text" name="address" class="mand inputbox" value="<?=$a?htmlspecialchars($a['address']):""?>" style="width:350px;"></td>
</tr>
<tr>
<td>Landmark<span class="star">*</span></td><td><input type="text" name="landmark" class="mand inputbox" value="<?=$a?htmlspecialchars($a['landmark']):""?>" style="width:350px;"></td>
</tr>

<tr>
<td>City<span class="star">*</span></td><td>
<?php if(!$cities){?>
<input type="text"  name="city" class="mand inputbox" value="<?=$a?htmlspecialchars($a['city']):""?>">
<?php }else{?>
<select name="city" class="inputbox" style="padding:3px 0px;height: 27px">
<?php foreach($cities as $c){?><option value="<?=$c?>" <?=($a && $a['city']==$c)?"checked":""?>><?=$c?></option>
<?php }?>
</select>&nbsp;&nbsp;<span title="One or more items in your cart can be shipped to this city only!" style="font-size:140%;cursor:pointer;cursor:help;color:red;font-weight:bold;">!</span>
<?php }?>
</td>
</tr>
<tr>
<td>State<span class="star">*</span></td><td><input class="mand inputbox" type="text" name="state" value="<?=$a?htmlspecialchars($a['state']):""?>"></td>
</tr>
<tr>
<td>Country<span class="star">*</span></td><td>
	<select class="mand inputbox" name="country" style="height: 30px;width: 210px;">
		<?php 
			$u_country = $a?htmlspecialchars($a['country']):'';
			foreach($countrylist as $country){
				$is_selected = (($u_country== $country)?'selected':'');
				echo '<option value="india" '.$is_selected.'>'.ucwords($country).'</option>';
			}
		?>
		
	</select>
</td>
</tr>
<tr>
<td valign="top">Pincode<span class="star">*</span></td><td><input class="mand inputbox" type="text" name="pincode" maxlength="6" value="<?=$a?htmlspecialchars($a['pincode']):""?>">
<?php if($this->dbm->is_cod_available()){?>
<a class="buttonstyle1" href="javascript:void(0)" onclick='checkcod()' style="font-size:80%;width:auto;color:auto;padding:3px 5px;">Click here to Check COD Availability</a>
<br><div style="font-weight:normal;color:red;font-size:80%">COD is available in selected cities</div>
<script>
function checkcod()
{
	$.fancybox.showActivity();
	$.post("<?=site_url("jx/checkcodpin")?>","pin="+$("input[name=pincode]").val(),function(data){
		$.fancybox.hideActivity();
		alert(data);
	});
}
</script>
<?php }?>
</td>
</tr>
<tr>
<td>Mobile<span class="star">*</span></td>
<td><input type="text" class="mand inputbox" name="mobile" value="<?=$user?htmlspecialchars($user['mobile']):""?>"></td>
</tr>
<tr>
<td>Telephone </td>
<td><input type="text" class="inputbox" style="padding:5px;border:1px solid #AAAAAA;width: 200px;" name="telephone" value="<?=$a?htmlspecialchars($a['telephone']):""?>"></td>
</tr>

</table>

</div>
<?php 
			if(!$has_onlygiftcard){
?>
	<div style="padding:5px;background: #e3e3e3">
		<input type="checkbox" id="shipbillcheck" name="shipbillcheck">Billing address is same as shipping address
	</div>
	
<?php 		}
?>

<div id="billingaddr" style="display:none;">
<table class="addrtable" cellpadding=4>
<tr>
	<td>Name<span class="star">*</span></td><td><input type="text" name="bill_person" class="mand inputbox" value="<?=$user?htmlspecialchars($a['name']):""?>"></td>
</tr>
<tr>
<td>Address<span class="star">*</span></td><td><input type="text" name="bill_address" class="mand inputbox" value="<?=$a?htmlspecialchars($a['address']):""?>" style="width:350px;"></td>
</tr>
<tr>
<td>Landmark<span class="star">*</span></td><td><input type="text" name="bill_landmark" class="mand inputbox" value="<?=$a?htmlspecialchars($a['landmark']):""?>" style="width:350px;"></td>
</tr>

<tr>
<td>City<span class="star">*</span></td><td>
<input type="text"  name="bill_city" class="mand inputbox" value="<?=$a?htmlspecialchars($a['city']):""?>">
</td>
</tr>
<tr>
<td>State<span class="star">*</span></td><td><input class="mand inputbox" type="text" name="bill_state" value="<?=$a?htmlspecialchars($a['state']):""?>"></td>
</tr>
<tr>
<td valign="top">Pincode<span class="star">*</span></td><td><input class="mand inputbox" type="text" name="bill_pincode" maxlength="6" value="<?=$a?htmlspecialchars($a['pincode']):""?>">
</td>
</tr>
<tr>
<td valign="top">Country<span class="star">*</span></td><td><input class="mand inputbox" type="text" name="bill_country" maxlength="6" value="<?=$a?htmlspecialchars($a['country']):""?>">
</tr>
<tr>
<td>Email<span class="star">*</span></td>
<td><input type="text" class="mand inputbox" name="bill_email" value="<?=$user?htmlspecialchars($user['email']):""?>"></td>
</tr>
<tr>
<td>Mobile<span class="star">*</span></td>
<td><input type="text" class="mand inputbox" name="bill_mobile" value="<?=$user?htmlspecialchars($user['mobile']):""?>"></td>
</tr>
<tr>
<td>Telephone </td>
<td><input type="text" class="inputbox"  style="padding:5px;border:1px solid #AAAAAA;width: 200px;" name="bill_telephone" value="<?=$a?htmlspecialchars($a['telephone']):""?>"></td>
</tr>
</table>
</div>

<div style="background:#F0F0F1;padding:15px 10px;height: 30px;" align="right">
	<a href="javascript:void(0)" onclick='payment_trig(this)' class="continue_buttn scrollPage" style="padding-bottom: 20px" id="paymethod_trig"></a>
</div>

</div>

<a name="payment" id="payment"></a>

<div id="payment_mode" style="display:none;margin-top:20px;background: #f9f9f9">
 

<div class="head" style="font-size: 16px;background: #D8E3F0;padding:15px;-moz-border-radius:10px 10px 0px 0px">
	<b id="head_pm" >Please select Payment method</b>
	<span style="float: right"><b>Total Cart Value : </b> <span id="total_order_value">0</span> </span>
</div>


<div id="payment_method_cont" >
<div align="center"><img src="<?=IMAGES_URL?>loader2.gif"></div>
</div>

<div style="margin-top:20px;clear:both;">
	<fieldset style="margin: 0px;padding:0px;border:none;">
		<legend style="margin: 0px;padding:0px;padding:10px 0px 0px 0px;">Do you have anything to say to us :</legend>
		<div style="padding:0px;background: #D8E3F0;">
			<textarea name="user_note" rows="3" cols="50" style="width: 100%"></textarea>
		</div>
	</fieldset>
</div>

<div style="padding-top:20px;clear:both;">
<input type="checkbox" checked="checked" id="check18yrs"> I am atleast 18 years old and I agree to <a href="<?=site_url("terms")?>">Terms and Conditions</a>





<div style="background:#eee;padding:15px 5px;" align="right">
	<input id="checkout_trig" type="image" style="display: none;" src="<?=IMAGES_URL?>buy_checkout.png">
	<a class="checkout_buttn"  href="javascript:void(0)" onclick="validate_checkout()"></a>
</div>
</div>


</div>

</form>

</div>

<script type="text/javascript">
<?php 
		if($has_onlygiftcard){
?>
		$('#shippingaddr').hide();
		$('#billingaddr').show();
		$('.couponcont').hide();
		$('.cart_select_freesamples').hide();
		bypass = 1;
<?php
		}
?>
</script>


<div class="offers">
<?php 
$items=$this->cart->contents();
if(count($items)!=0){
?>
<?php
$next=$this->db->query("select min,`limit` from king_freesamples_config where min>$total order by min asc")->row_array();
 if(!empty($next)){?>
 <div style="margin:0px 0px 10px 0px;background:#ffffa0;border:1px dotted #cd0000">
 <p style="font-size: 12px;padding:0px 5px;">
 	Increase your order value to <b style="white-space:nowrap">Rs <?=$next['min']?></b> , 
 	and you can get upto <b><?=$next['limit']?></b> samples FREE along with the order
 </p>
 </div>
<?php }?>

<?php if(!empty($fav_items) && !$this->session->userdata('is_favsloaded')){?>
 
 
<div class="offer freesamples">

<a class="blue bold" href="<?=site_url("loadfavs")?>" style="text-decoration:none;">
		<img src="<?php echo base_url().'/images/load-fav5.png'?>" />
</a>
	
<div class="offercont">
<table width="100%" cellspacing=0 cellpadding=3>
<?php foreach($fav_items as $fav){?>
<tr>
	<td>
		<div align="center" style="margin-top: 10px;">
			<a href="<?=$fav['url']?>"><img src="<?=IMAGES_URL?>items/small/<?=$fav['pic']?>.jpg"></a>
		</div>	
		<br />
		<div><a href="<?=$fav['url']?>"><?=$fav['name']?></a></div>
	</td>
</tr>
<?php }?>
</table>
</div>
</div>
<?php }?>

<?php 

if($this->session->userdata('is_favsloaded') || empty($fav_items)){
	$recent=$this->dbm->getrecentsold();
?>
<div class="offer recentsold">
<h3>Recently sold</h3>
<div class="offercont" style="max-height:none">
<table width="100%" cellspacing=0 cellpadding=5>
<?php foreach($recent as $i=>$r){?>
<tr>
	<td>
		<div align="center" style="margin-top: 10px;">
			<a href="<?=$r['url']?>"><img src="<?=IMAGES_URL?>items/small/<?=$r['pic']?>.jpg" ></a>
		</div>
		 
		<div class="o_itemcont">
			<div class="o_itemcont_name" ><?=$r['name']?></div>
			<div class="o_itemcont_pricedet">
				Rs <?=$r['orgprice']>$r['price']?"<b>{$r['price']}</b> <span class='strike'>{$r['orgprice']}</span>":"<b>{$r['price']}</b>"?>
				<span style="float: right">
					<a href="<?=$r['url']?>" style="text-decoration: none;color: #FFF;background: #AE4B84;padding:3px;font-size: 10px;-moz-border-radius:5px;">Buynow</a>
				</span>
			</div>
		</div>
	</td>
</tr>
<?php if($i==3) break; }?>
</table>

<div class="offeritem" style="display: none;">
<?php foreach($recent as $i=>$r){ if($i<=3) continue;?>
<ul>
<li><a href="<?=$r['url']?>"><?=$r['name']?></a>
</ul>
<?php if($i>6) break;}?>
</div>




</div>
</div>
<?php } ?>
<?php } ?>
<div style="padding:5px 0px">
	<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fsnapittoday&amp;width=200&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=false&amp;header=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:258px;" allowTransparency="true"></iframe>
</div>


</div>

<div class="clear"></div>
<a class="chk_login" href="#chk_login_form"></a>



<div style="display:none;">

<div id="chk_login_form" style="background:#e9e9e9;width: 350px;">
<div style="padding:10px;">
<table width="100%" cellpadding=5 cellspacing=0>
	<tr>
		<td valign="top" >
			<h3 align="center">Sign into <span style="font-size: 18px;color: #999">Snapittoday.com</span></h3>
			<form action="<?=site_url("emailsignin")?>" method="post" id="indsigninfrm">
					<p style="padding:5px;margin: 0px;font-size: 12px;margin:10px 0 0;color: #FFF;background: #000;text-align: center;">
						We see that you already have account  with us, <br /> Please Sign-in with your email and password
					</p>
					<div style="padding:5px;font-size: 14px;line-height: 20px;">
						<div style="margin:10px 0px;">
							<b>Email Address:</b><br />
							<input type="text" class="inputbox" style="width: 300px;padding:5px;" name="email">
						</div>
						<div  >
							<div style="margin:10px 0px;">
								<b>Password:</b><br />
								<input type="password" class="inputbox" style="width: 300px;padding:5px;" name="password">
							</div>
							<div style="padding:5px;margin-top: 10px;">
							
								<a class="fancylink" href="#forgotpass" style="color: #555">Forgot password ?</a>
								<input type="image" src="<?=base_url()?>images/signin-new.png" style="border:0px;padding-top:0px;float: right;">
							</div>
						</div>
						 
					</div>
			</form>
			 
		</td>
		 
	</tr>
	
</table>
</div>
 
 


</div>



</div>




</div>
<div style="display:none;">
<div id="forgotpass" style="width:450px;padding:10px;">
		<h2>Forgot Password?</h2>
		<form id="forgotpassfrm">
			<table style="margin:20px;" cellpadding=5>
				<tr>
					<td><h3>Your Email : </h3></td>
					<td><input class="inputbox" type="text" name="email" style="width:220px"></td>

				</tr>
				<tr>
					<td colspan=2>Please enter your work or personal email id provided while creating your account</td>
				</tr>
				<tr>
					
					<td align="left">
						Already have an account, <a href="javascript:void(0)" onclick='$(".chk_login").click();'>Click here</a>
					</td>
					<td align="right"><input type="image" src="<?php echo IMAGES_URL?>/submit.png"></td>
				</tr>

			</table>
		</form>
</div>
</div>


<script type="text/javascript">



var giftwrap_charge = '<?php echo GIFTWRAP_CHARGES?>';
$('#is_giftwrap').change(function(){
	var ct_amt = $('#gc_charges').attr('ct')*1;
	var stat = 0;
		giftwrap_charge = parseFloat(giftwrap_charge);
	if($(this).attr('checked')){
		$('input[name="is_giftwrap"]').attr('checked',true);
		$('#gc_charges').show();
		$('#ct_amount').text(ct_amt+giftwrap_charge);
		stat = 1;
	}else{
		$('input[name="is_giftwrap"]').attr('checked',false);
		$('#gc_charges').hide();
		var ct_amt = $('#gc_charges').attr('ct')*1;
		$('#ct_amount').text(ct_amt);
		stat = 0;
	}	
	checkoutsess('gc_stat','set','set_stat='+stat); 


	get_checkoutstat(function(status){
		if(status > 2){
			$('#paymethod_trig').trigger('click');	
		}
	})
	
	
	//if($('.paymetho').length)
		//$('.paymetho:checked').trigger('click');
	
});



checkoutsess('gc_stat','get','',function(resp){
	if(resp.stat==1){
		$('#is_giftwrap').attr('checked',true).trigger('change'); 
	}else{
		$('#is_giftwrap').attr('checked',false); 
	}
});

 

checkoutsess('gc_recp_det','get','',function(resp){
	var gc_recp_det = resp.gc_recp_det;
		if(gc_recp_det != false){
			$('input[name="recp_name"]').val(gc_recp_det.name);$('input[name="gc_recp_name"]').val(gc_recp_det.name);
			$('input[name="recp_email"]').val(gc_recp_det.email);$('input[name="gc_recp_email"]').val(gc_recp_det.email);
			$('input[name="recp_mobile"]').val(gc_recp_det.mobile);$('input[name="gc_recp_mobile"]').val(gc_recp_det.mobile);
			$('textarea[name="recp_msg"]').val(gc_recp_det.msg);$('input[name="gc_recp_msg"]').val(gc_recp_det.msg);
		}
});


function validate_checkout(){
	var un = $('textarea[name="user_note"]').val();
		if(is_nohtml(un)){
			$('#checkout_trig').trigger('click');	
		}else{
			alert("Please note: remove special tags from note."); 
		}
}

$("#forgotpassfrm").submit(function(){
	if(!is_email($("input[name=email]",$(this)).val()))
	{
		alert("Please enter a valid email id");
		return false;
	}
	$.fancybox.showActivity();
	$.post("<?php echo site_url('jxforpass'); ?>",$(this).serialize(),function(resp){
		alert(resp);
		$.fancybox.hideActivity();		
	});
	return false;
});


function show_lgnfrm(){
	$('#fwdfrm_options').hide();
	$('#loginfrm_options').show();
}

function show_frgtfrm(){
	$('#loginfrm_options').hide();
	$('#fwdfrm_options').show();
}

var cart_total = <?php echo ($this->session->userdata('total_cartval')?$this->session->userdata('total_cartval'):0);?>;


function payment_trig(ele)
{

	var stat = 0;
	/*
	$('#billingaddr .mand').each(function(){
		$(this).val($.trim($(this).val()));
		if(!$(this).val()){
			stat += 1;
		}
	});
	
	if(!$('#shipbillcheck').attr('checked')){
		$('#shippingaddr .mand').each(function(){
			$(this).val($.trim($(this).val()));
			if(!$(this).val()){
				stat += 1;
			}
		});
	}
	*/

	$(".mand",$('#c2form')).filter(":not(:hidden)").each(function(i){
		$(this).val($.trim($(this).val()));
		if(!$(this).val()){
			stat += 1;
		}
	});
	
	
	if(stat){
		$(ele).data('allow_scroll',0);
		alert("All Fields are mandatory");
	}else{
		$(ele).data('allow_scroll',1);
		$(ele).parent().hide();
		checkoutstat(3);
		load_payment_block();
	}
}


function get_checkoutstat(cb){
	$.post("<?=site_url("jx_checkoutstat")?>",'action=check',function(resp){
		
		return cb(resp);
	});	
}

function load_payment_block(){


	if(1){	
	
		var shipping_charges = parseFloat(<?php echo $cart_shipping_charge*1?>);
		var cod_charges = parseFloat(<?php echo COD_CHARGES*1?>);
			cart_total = parseFloat(cart_total);
	
	
			
		
		$("#payment_mode").show();
		$("#payment_method_cont").html('<div align="center"><img src="<?=IMAGES_URL?>loader2.gif"></div>');
	
		var tmp_cartval = parseFloat(cart_total)+parseFloat(shipping_charges);
		if($('#is_giftwrap').attr('checked'))
			tmp_cartval += parseFloat(giftwrap_charge); 
		 
		
		if(!tmp_cartval){
			$('#head_pm').css('visibility','hidden');
			$("#payment_method_cont").hide();
			$('#total_order_value').css('background','#ffffd0').hide().html('Rs '+tmp_cartval).fadeIn(1500);
			return false;
		}else{
			$("#payment_method_cont").show();
			$('#head_pm').css('visibility','visible');
		}
	
		
		$.post("<?=site_url("jx/checkcodpin")?>","pin="+$("input[name=pincode]").val()+"&echo=yes",function(data){
			$("#payment_method_cont").html(data);
	
			$('#total_order_value').css('background','#ffffd0').hide().html('Rs '+(cart_total+shipping_charges)).fadeIn(1500);
	
	
			$('.paymetho').bind('click',function(){
	
				if($('#is_giftwrap').attr('checked'))
					cart_total_new = cart_total+giftwrap_charge; 
				else
					cart_total_new = cart_total; 
				
				if($('.codmetho').attr('checked')){
					
					<?php 
						if($cart_shipping_charge){
					?>		
						$('#total_order_value').css('background','#ffffd0').hide().html('Rs '+(cart_total_new+shipping_charges)).fadeIn(1500);
					<?php 		
						}else{
					?>
						$('#total_order_value').css('background','#ffffd0').hide().html('Rs '+((cart_total_new+cod_charges*1))+'<span style="font-size:11px;color:#cd0000;display:block;text-align:center"> COD Charges <b>Rs 40</b> added</span>').fadeIn(1500);
					<?php 		
						}	
					?>
					
					
				}else{
					$('#total_order_value').css('background','#ffffd0').hide().html('Rs '+(cart_total_new+shipping_charges)).fadeIn(1500);
				}
			});
	
			if($('.paymetho').length){
				$('.paymetho:checked').trigger('click');
			}
			
		});
	}
}


var fssels=[<?=$this->session->userdata("fsselected")?>];
$(function(){
	$(".fs_sels").attr("checked",false);
	$(".fs_sels").each(function(){
		if($.inArray(parseInt($(this).val()),fssels)>-1)
			$(this).attr("checked",true);
	});
	
	$("#chkot_email_check").click(function(){
		em=$(".chkot_email").val();
		if(!is_email(em))
		{
			alert("Please enter a valid email");
			return;
		}
		$.fancybox.showActivity();
		$.post("<?=site_url("jx/useraccheck")?>","em="+em,function(data){
			$.fancybox.hideActivity();
			if(data=="yes")
			{
				$("#chk_login_form input[name=email]").val(em);
				$(".chk_login").click();
			}else{
				$("#chkot_email_check").hide();
				$(".addrcont,.chkot_pass").show();
				$(".chkot_email").attr("readonly",true);

				$("#checkoutaddress").show();
				$('#get_regis_text').fadeIn();
				$('#userauth_cont .continue_buttn').parent().hide();
				checkoutstat(2);
				
			}
		});
	});
	
	$("#c2form input").keypress(function(e){
		if(e.which==13)
			return false;
	});
	$("#fakeform").submit(function(){
		return false;
	});
	$(".chk_login").fancybox({
								'onClosed':function(){
												$('body').css('opacity','0.5');
												location.href = location.href;
											}
							});


	$('#carttab tr').hover(function(){
		$(this).css('background','#f7f7f7');
	},function(){
		$(this).css('background','#FFF');
	});
	
});


$('.scrollPage').click(function(e) {
	if($(this).data('allow_scroll')){
	   var elementClicked = $(this).attr("href");
	   var destination = $(elementClicked).offset().top;
	   $("html:not(:animated),body:not(:animated)").animate({ scrollTop: destination-20}, 500 );
	}
	
	return false;
	
}).data('allow_scroll',1);

function scrollto_last_actv(){
	var rid = 'RAND_'+Math.round(Math.random()*1000);
	$('.continue_buttn:visible').attr('id',rid);
	
	var destination = $('#'+rid).offset().top;
	   $("html:not(:animated),body:not(:animated)").animate({ scrollTop: destination-20}, 500 );
}


</script>
<script type="text/javascript">
	

	function reset_formactv(){
		var checkoutstat_flg = <?php echo ($this->session->userdata('checkoutstat')?$this->session->userdata('checkoutstat'):0);?>;
		if(checkoutstat_flg){
			bypass = 1;	
			 
			//$('.couponcont').css('opacity','0.5');
			//$('.couponcont .buttonstyle1').hide();
			//$('#couponcode').attr('readonly',true);
			//$('#applycoupon').unbind('click');
			for(i=0;i<=checkoutstat_flg-1;i++){
				$('.continue_buttn:eq('+i+')').trigger('click');
			}	
		}
	}
	 
	$(function(){
		window.setTimeout('reset_formactv()',500);
	});

	$('.codmetho').live('click',function(){
		<?php 
			if($has_onlygiftcard || $has_giftcard_with_products){
				echo '$(".paymetho:first").attr("checked",true).trigger("click");';
				echo 'alert("Cash on delivery is not applicable for gifts \n Please remove gifts from your cart .");';
			}
		?>
	});
</script>