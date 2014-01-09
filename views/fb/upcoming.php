<style>
 .headingtext{
 background:url(<?=base_url()?>images/fade.png) repeat-x;
 height:46px;
 color:#222;
 padding:8px 0px 0px 15px;
 font-weight:normal;
 }

</style>
<div class="headingtext" style="clear:both;margin:0px 0px;margin-top:15px;">Upcoming sales</div>
<div style="margin-left:20px;">
<?php $i=0;foreach($deals as $heading => $cd){?>
<div style="color:#888;font-family:arial;font-size:13px;float:left;margin:7px;border:1px solid #E0D8CA;">
<div style="background:#F7F3F0;color:#444;font-size:14px;font-weight:bold;padding:5px;"><?=$heading?></div>
<div align="left" style="padding:10px;">
<?php foreach($cd as $deal){?>
<div style="padding:7px 10px;">
<div><a class="link1" href="<?=site_url("deal/{$deal['url']}")?>" style="font-weight:bold;color:#333;"><?=$deal['name']?></a></div>
<div style="padding-left:20px;padding-top:5px;">starts <?=date("ga",$deal['startdate'])?></div>
<div style="padding-left:20px;padding-top:3px;">ends <?=date("ga d/m",$deal['enddate'])?></div>
</div>
<?php }?>
</div>
</div>
<?php }?>
<?php if(empty($deals)){?>
<div style="font-family:arial;padding:20px;">No upcoming deals available now. Please check back later.</div>
<?php }?>
</div>