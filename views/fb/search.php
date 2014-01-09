<?php if(empty($deals) && empty($brands) && empty($category)){?>
<h1>Sorry, no results found</h1>
<?php }?>
<?php if(!empty($deals)){?>
<div style="padding-top:10px;">
<div style="background:#eee;padding:5px 10px;font-size:17px;font-family:arial;font-weight:bold;" align="left">
<div style="float:right;color:blue;font-size:12px;"><?=count($deals)?> deals found</div>
Deals</div>
<?php foreach($deals as $deal){?>
<div style="border:1px solid #aaa;padding:5px;float:left;margin:10px;font-family:arial;font-size:13px;" align="left">
<div style="margin-right:10px;float:left;max-height:200px;max-width:300px;overflow:hidden">
<a href="<?=site_url("deal/".$deal['url'])?>"><img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="max-height:200px;"></a>
</div>
<div style="clear:both;padding-top:10px;color:brown;font-size:15px;font-weight:bold;font-family:trebuchet ms;" align="left">
<a href="<?=site_url("deal/".$deal['url'])?>" style="color:brown;text-decoration:none;"><?=$deal['itemname']?></a>
</div>
<div style="padding-top:5px;">from <?=$deal['brandname']?></div>
<div style="padding-top:10px;">Rs <b><?=$deal['price']?></b> <span style="text-decoration:line-through"><?=$deal['orgprice']?></span></div>
<div style="padding-top:10px;"><?=$deal['category']?></div>
<?php if($deal['dealtype']==1){?>
<div style="padding-top:5px;">Group Sale</div>
<?php }?>
</div>
<?php }?>
</div>
<?php }?>
<?php if(!empty($brands)){?>
<div style="padding-top:10px;clear:both;font-family:arial;">
<div style="background:#eee;padding:5px 10px;font-size:17px;font-family:arial;font-weight:bold;" align="left">
<div style="float:right;color:blue;font-size:12px;"><?=count($brands)?> brands found</div>
Brands</div>
<?php foreach($brands as $brand){?>
<div style="float:left;margin:10px;"><a href="<?=site_url("brand/".$brand['name'])?>" style="font-size:20px;">
<?php if($brand['logoid']!=NULL){?>
<img src="<?=base_url()?>images/brands/<?=$brand['logoid']?>.jpg">
<?php }else{?>
<?=$brand['name']?>
<?php }?>
</a></div>
<?php }?>
</div>
<?php }?>

<?php if(!empty($category)){?>
<div style="padding-top:10px;clear:both;font-family:arial;font-size:13px;">
<div style="background:#eee;padding:5px 10px;font-size:17px;font-family:arial;font-weight:bold;" align="left">
<div style="float:right;color:blue;font-size:12px;"><?=count($deals)?> categories found</div>
Categories</div>
<?php foreach($category as $brand){?>
<div style="float:left;margin:10px;"><a href="<?=site_url("category/".$brand['name'])?>" style="color:blue;font-size:13px;">
<?=$brand['name']?>
</a></div>
<?php }?>
</div>
<?php }?>
<?php
