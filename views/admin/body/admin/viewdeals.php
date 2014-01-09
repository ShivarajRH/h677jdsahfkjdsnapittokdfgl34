<?php 
$user=$this->session->userdata("admin_user");
?>
<script>
$(function(){
	$("select").val(0);
	$(".viewmore").click(function(){
		$("select",$(this).parent()).show();
	});
	$("#brandsel").change(function(){
		obj=$(this);
		if(obj.val()==0)
			return;
		location.href="<?=site_url("admin/dealsforbrand")?>/"+obj.val();
	});
	$("#catsel").change(function(){
		obj=$(this);
		if(obj.val()==0)
			return;
		location.href="<?=site_url("admin/dealsforcategory")?>/"+obj.val();
	});
});
</script>
<style>
div.nextprev{
color:#00f;
margin-right: 75px;
margin-bottom:0px;
padding: 5px;
font-family: arial;
font-weight:bold;
font-size: 11px;
}
div.nextprev img{
margin-bottom:-4px;
}
#page {
#background:#FFFFFF none repeat scroll 0 0;
#border:1px solid #F0E9D6;
margin:0 auto;
font-size: 12px;
}
.deal{
margin-bottom: 10px;
background: url(<?=base_url()?>images/title-bg-top-small.gif) repeat-x scroll left top;
border:#CCCCCC 1px solid;
-moz-border-radius:10px;
padding: 10px;
float: left;
width: 695px;
font-family:arial;
}
.admhotellinks span{
padding:0px 4px;
font-size:11px;
}
</style>
<div class="heading" style="margin-bottom:0px;">
<div class="headingtext container">
<?php if(isset($pagetitle)) echo $pagetitle; else echo "Deals";?>
<div><a style="font-size:14px;" href="<?=site_url("admin/adddeal")?>">Add deal</a></div>
</div>
</div>
<div class="container" style="font-family:arial;">
<?php if(isset($p)){?>
<div align="right" style="margin-top:5px;margin-bottom:-13px;font-size:12px;">
<?php 
$st=(($p-1)*5+1);
$et=$st+4;
if($et>$len)
	$et=$len;
?>
<?php if($len>0){?>
<?php if($p>1 && isset($prevurl)){?>
<a style="padding:5px;" href="<?=$prevurl?>">previous</a>
<?php }?>
showing <?=$st?><?php if($st!=$et){?>-<?=$et?><?php }?> of <?=$len?>
<?php if($et<$len && isset($nexturl)){?>
<a style="padding:5px;" href="<?=$nexturl?>">next</a>
<?php }}?>
</div>
<?php }?>
<div style="float:left">

<div class="sidepane">
<div style="font-size:15px;">View deals by Menu</div>
<form id="bymenu">
<div>Select Menu : <select id="menuid">
<?php foreach($menu as $m){?><option value="<?=$m['id']?>"><?=$m['name']?></option><?php }?>
</select></div>
<div>StartDate: <input type="text" id="pstartdate"></div>
<input type="submit" value="Ok">
</form>
</div>

<div class="sidepane">
<div style="font-size:15px;">View deals by status</div>
<a style="color:#00f;font-weight:bold;margin:0px 5px;font-size:13px;" href="<?=site_url("admin/dealsbystatus/active")?>"><nobr>Active</nobr></a>
<a style="color:#aaa;font-weight:bold;margin:0px 5px;font-size:13px;" href="<?=site_url("admin/dealsbystatus/inactive")?>"><nobr>Inactive</nobr></a>
<a style="color:#f00;font-weight:bold;margin:0px 5px;font-size:13px;" href="<?=site_url("admin/dealsbystatus/expired")?>"><nobr>Expired</nobr></a>
<a style="color:grey;font-weight:bold;margin:0px 5px;font-size:13px;" href="<?=site_url("admin/dealsbystatus/unpublished")?>"><nobr>Unpublished</nobr></a>
<a style="color:green;font-weight:bold;margin:0px 5px;font-size:13px;" href="<?=site_url("admin/dealsbystatus/published")?>"><nobr>Published</nobr></a>
</div>
<div class="sidepane">
<div style="font-size:15px;">View deals by category</div>
<?php $ic=0;
foreach($categories as $category){
?>
<a style="margin:0px 5px;font-size:13px;" href="<?=site_url("admin/dealsforcategory/{$category->id}")?>"><?=$category->name?></a>
<?php $ic++;if($ic==10) break;}?>
<?php if(count($categories)>10){?>
<div align="center"> 
<a href="javascript:void(0)" class="viewmore" style="font-size:13px;float:right;font-weight:bold;">more</a>
<select id="catsel" style="display:none">
<?php foreach($categories as $cat){?>
<option value="<?=$cat->id?>"><?=$cat->name?></option>
<?php }?>
</select>
</div>
<?php }?>
</div>
<?php 
if($user['usertype']==1){
?>
<div class="sidepane">
<div style="font-size:15px;">View deals by brand</div>
<?php $ic=0;
foreach($brands as $brand){
?>
<a style="margin:0px 5px;font-size:13px;" href="<?=site_url("admin/dealsforbrand/{$brand->id}")?>"><nobr><?=$brand->name?></nobr></a>
<?php $ic++;if($ic==10) break;}?>
<?php if(count($brands)>10){?>
<div align="center"> 
<a href="javascript:void(0)" class="viewmore" style="font-size:13px;float:right;font-weight:bold;">more</a>
<select id="brandsel" style="display:none">
<option value="0">--select--</option>
<?php foreach($brands as $brand){?>
<option value="<?=$brand->id?>"><?=$brand->name?></option>
<?php }?>
</select>
</div>
<?php }?>
</div>
<?php }?>
</div>
<div style="font-family:arial;padding-top:15px;padding-left:230px;">
<?php 
$usertype=$this->session->userdata("usertype");
//echo $usertype;
if(isset($deals)&& isset($dealitems) && $deals!=FALSE)
{
foreach($deals as $deal)
{
	$catid=$deal->catid;
	//print_r($catid);exit;
	//echo $catid;
	$item=$dealitems[$deal->dealid][0];
//	if($catid!=4)
	{
?>

<div class="deal">
<div style="font-family: arial;font-size: 16px;font-weight: bold;margin-bottom:10px;">
<div style="float: right;" class="admhotellinks">
<span><a style="font-size: 11px;" href="<?=site_url('admin/edit/'.$deal->dealid)?>">Edit</a></span>
<span><a style="font-size: 11px;" href="<?=site_url('admin/getpicsandvideos/'.$deal->dealid.'/'.$deal->catid)?>">view Photos & Videos</a></span>
<span><a style="font-size: 11px;" href="<?=site_url('admin/addpicsandvideos/'.$deal->dealid.'/'.$deal->catid)?>">Add Photos & Videos</a></span>
<?php $user=$this->session->userdata("admin_user");if($user["usertype"]==1) { if($deal->publish==0){?>
<span><a style="font-size: 11px;" onclick='$(".pubform<?=$deal->dealid?>").show()' href="javascript:void(0)">Publish</a></span>
<?php }else {?>
<span><a style="font-size: 11px;" href="<?=site_url('admin/publishdeal/'.$deal->dealid.'/'.$deal->catid.'/'.$item->id.'/0')?>">Unpublish</a></span>
<?php }}?> 
</div>
<div style="float:left;padding:5px;"><img style="float:left; width: 100px;height: 100px;" src="<?=base_url().'images/items/'.$deal->pic.'.jpg'?>"></div>
</div>
	<div>
	<div style="color:#872;float:left;font-size:20px;margin-left:15px;margin-top:15px;">
	<?=$deal->tagline;?>
	<div style="color:#444;font-size:14px;"><?php if(isset($deal->brandname)) echo $deal->brandname;?></div>
	</div>
	<?php if($user['usertype']==1 && $deal->publish==0){?>
	<div class="pubform<?=$deal->dealid?>" style="background:#eee;padding:5px;display:none;font-size:12px;float:right;clear:right"> 
	<form action="<?=site_url('admin/publishdeal/'.$deal->dealid.'/'.$deal->catid.'/'.$item->id.'/1')?>" method="post">
	<div>Agent Commission : Rs <input size="4" type="text" name="agentcom" value="<?=$item->agentcom?>"></div>
	<div><label><input type="checkbox" name="live"> Live Deal</label></div>
	<div align="right"><input type="submit" value="Publish"></div>
	</form>
	</div>
	<?php }?>
	<div style="clear:right;font-size:12px;float: right;margin-top:10px;">
	<?php if($deal->startdate>time()){?>
		<div style="color:#aaa;font-size:16px;font-weight:bold;">Inactive</div>
	<?php }elseif($deal->enddate>time()){?>
		<div style="color:#00f;font-size:16px;font-weight:bold;">Active</div>
	<?php }else{?>
		<div style="color:#f00;font-size:16px;font-weight:bold;">Expired</div>
		<?php }?>
		<div><label style=" font-family:arial;font-weight: bold;">Deal Start Date : </label><label style="font-size: 13px;font-family: arial;padding: 0px;"><?=date("ga d/m/Y",$deal->startdate)?></label></div>
		<div><label style="font-family:arial;font-weight: bold;">Deal End Date : </label><label style="font-size: 13px;font-family: arial;padding: 0px;"><?=date("ga d/m/Y",$deal->enddate)?></label></div>
		<div><label style="font-family:arial;font-weight: bold;">Deal Type : </label><label style="font-size: 13px;font-family: arial;padding: 0px;"><?php if($deal->dealtype==0) echo "Brand Sale"; else echo "Group Sale";?></label></div>
		<input type="hidden" name="dealid" value="<?=$deal->dealid?>">
		<div style="font-size:13px;padding-top:10px;">Category : <span style="color: #426C33;"><?=$deal->name?></span></div>
		<div style="padding-top:5px;font-size:13px;">
<?php if($deal->publish==1){?>
		<b>Published</b>
		<?php if($item->live==0){?>
		but <b>not live</b>
		<?php if($user['usertype']==1){?>
		<br><a href="<?=site_url("admin/livedeal/{$deal->dealid}/".$item->id."/1")?>">make it live</a>
		<?php }?>
		<?php }else {?>
		and <b>live</b>
		<?php if($user['usertype']==1){?>
		<br><a href="<?=site_url("admin/livedeal/{$deal->dealid}/".$item->id."/0")?>">make it not live</a>
		<?php }?>
		<?php }?>
<?php }else{?><b>Not published</b>
<?php }?>
		</div>
		<div style="padding-top:5px;">
		<?php $f=$this->db->query("select favs from king_dealitems where dealid=?",$deal->dealid)->row()->favs; $fav=1; 
		if($f) $fav=0;?>
		<a href="<?=site_url("admin/favdeal/{$deal->dealid}/$fav")?>"><?=!$fav?"remove from":"add to"?> favorite</a>
		</div>
		<div style="padding-top:5px;">
			<a href="<?=site_url("admin/define_cashback/{$deal->dealid}")?>">define cashback</a>
		</div>
	</div>
	</div>	
<div style="float: left;clear:left;">

<?php 
$dealitem=$item;
$deal->menu=$this->db->query("select m.name from king_deals d join king_menu m on m.id=d.menuid where dealid=?",$deal->dealid)->row()->name;
	?>
	<div style="margin-top: 5px;margin-bottom: 0px;float: left;margin-left:10px;border:0px solid;padding: 8px;">
    <div class="admhotellinks">
    <?php if($dealitem->available==$dealitem->quantity){?>
		<span style="font-size:13px;color:#f00;">SOLD OUT</span>
    <?php }?>
	</div>	
	<div style="font-size:13px;float:left;margin-left: 20px;margin-top: 0px;padding: 4px;">
		<div><label style=" font-family:arial;">Menu : </label><label style="font-weight: bold;font-size: 13px;font-family: arial;padding: 0px;"><?=$deal->menu?></label></div>
		<div style="margin-left: 0px;"><span style="font-family: arial;font-size: 12px;">MRP : </span><span style="font-weight: bold;font-family: arial;padding: 0px;">Rs. <?=$dealitem->orgprice?></span></div>
		<div style="margin-left: 0px;"><span style="font-family: arial;font-size: 12px;">Offer Price : </span><span style="font-weight: bold;font-family: arial;padding: 0px;">Rs. <?=$dealitem->price?></span></div>
		<div style="margin-left: 0px;"><span style="font-family: arial;font-size: 12px;">Agent Commission : </span><span style="font-weight: bold;font-family: arial;padding: 0px;">Rs. <?=$dealitem->agentcom?></span> <a href="javascript:void(0)" onclick='$("#agentcomch<?=$deal->dealid?>").show()'>change</a></div>
		<div id="agentcomch<?=$deal->dealid?>" style="font-size:12px;display:none">
		<form action="<?=site_url("admin/changecom")?>" method="post">
		<input type="hidden" name="dealid" value="<?=$deal->dealid?>">
		<input type="hidden" name="itemid" value="<?=$item->id?>">
		Agent Commission <input type="text" size="5" name="agentcom">
		<input type="submit" value="change">
		</form>	
		</div>
		<div style="margin-left: 0px;"><span style="font-family: arial;font-size: 12px;">Quantity : </span><span style="font-weight: bold;font-family: arial;padding: 0px;"><?php if($dealitem->quantity==4294967295) echo "No limit"; else echo $dealitem->quantity; ?></span></div>
		<div style="margin-left: 0px;"><span style="font-family: arial;font-size: 12px;">Sold : </span><span style="font-weight: bold;font-family: arial;padding: 0px;"><?=$dealitem->available?></span></div>
		<div style="margin-left: 0px;"><span style="font-family: arial;font-size: 12px;">Stock : </span><span style="font-weight: bold;font-family: arial;padding: 0px;"><?php $stock=0; $s=$this->db->query("select available from king_stock where itemid=?",$item->id)->row_array(); if(!empty($s)) $stock=$s['available'];?><?=$stock?></span></div>
		<div style="margin-left: 0px;"><span style="font-family: arial;font-size: 12px;">Cashback : Rs </span><span style="font-weight: bold;font-family: arial;padding: 0px;"><?=$this->db->query("select cashback from king_dealitems where id=?",$item->id)->row()->cashback?></span></div>
    </div>
    <div style="float:left;margin-left:10px;">
    <h3 style="margin:0px">Slots</h3>
    <?php
     	$slots=unserialize($deal->slots);
     	$nslots=array();
     	$nslotprice=array();
     	if(is_array($slots))
     	foreach($slots as $sno=>$srs)
     	{
     		$nslots[]=$sno;
     		$nslotprice[]=$srs;
     	}
     	if(empty($nslots))
     		echo "<h3>Alert! No slots. Add one</h3>";
     	else{
    ?>
    	<table width=200 border=1 cellspacing=0>
    	<?php foreach($nslots as $i=>$n){?>
    		<tr>
    			<td><?=($i==0?"1":($nslots[$i-1]+1))?>-<?=$nslots[$i]?></td><td>Rs <?=($i==0?$dealitem->price:$nslotprice[$i])?></td>
    		</tr>
    	<?php }?>
    	</table>
    <?php }?>
    </div>
	</div>
	</div>
	
	
	<table class="datagrid smallheader noprint" style="float:left;clear:left">
	<thead>
	<tr><th style="background:#fff;color:#000;" colspan="100%">Linked products</th></tr>
	<tR><th>Product</th><th>Qty</th><th>MRP</th></tR>
	</thead>
	<tbody>
	<?php {
		$itemid=$this->db->query("select id from king_dealitems where dealid=?",$deal->dealid)->row()->id;
		$prods=$this->db->query("select p.product_name,p.mrp,l.* from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$itemid)->result_array();
		foreach($prods as $p){
	?>
	<tr>
	<td><input type="hidden" name="prods_id[]" value='<?=$p['product_id']?>'><?=$p['product_name']?></td>
	<td><?=$p['qty']?></td>
	<td><?=$p['mrp']?></td>
	</tr>
	<?php } }?>
	</tbody>
	</table>
	
	
	<a href="<?=site_url("admin/update_partner_deal_prices/{$item->id}")?>" style="float:right;">View/Update partner prices</a>
	
	<?php if($user['usertype']==1){?><a href="<?=site_url("admin/activityfordeal/{$deal->dealid}")?>" style="float:right;clear:both;font-size:13px;">View Changelog</a><?php }?>
</div>
<?php 
	}
}
}

else {
?>
<div align="center" style="padding-top:50px;font-weight: bold;font-size: 29px;color: #888;">No Deals available!</div>
<?php }?>
</div>
</div>
<script>
$(function(){
	$("#pstartdate").datepicker({dateFormat:"mm:dd:yy"});
	$("#bymenu").submit(function(){
		location="<?=site_url("admin/dealsbymenu")?>/"+$("#menuid").val()+"/"+$("#pstartdate").val();
		return false;
	});
});
</script>