<style>
<!--
.viewdeal{
padding:3px;
background:#f90;
font-weight:bold;
color:#fff;
font-size:12px;
font-family:trebuchet ms;
}
-->
</style>
<!--<div class="headingtext" style="font-size:24px;padding-top:5px;padding-left:5px;padding-bottom:0px;">Deals</div>-->
<div style="padding-top:0px;" align="left">
<table cellspacing="20" style="font-family:arial;font-size:12px;">
<tr>
<?php $i=1;foreach($deals as $deal){?>
<td align="left" valign="bottom" style="width:200px;padding-right:5px;padding-bottom:10px;">
<div style="vertical-align:top">
<a href="<?=site_url("saleitem/".$deal['id'])?>">
<img src="<?=base_url()?>images/items/<?=$deal['pic']?>.jpg" style="max-width:200px;max-height: 200px;">
</a></div>
<div style="padding-top:5px;font-size:12px;font-weight:bold;"><?=$deal['name']?></div>
<div style="color:blue;">Price : Rs <b><?=$deal['price']?></b></div>
<div style=""><?=$deal['category']?></div>
<div align="left" style="padding-top:5px;"><a class="viewdeal" href="<?=site_url("saleitem/".$deal['id'])?>">view deal</a></div>
</td>
<?php $i++;if($i>4){ echo "</tr><tr>"; $i=1;}}?>
</tr>
</table>
</div>
