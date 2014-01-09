<style>
#content{
background:transparent;
}
</style>
<div class="container">

<div align="<?=!isset($thankyou)?"center":"left"?>" style="min-height:200px;padding:20px 0px;padding-top:50px;">
<?php if(isset($thankyou)){?>
<div style="float:right"><iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fsnapittoday&amp;width=292&amp;height=590&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=true&amp;header=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:292px; height:590px;" allowTransparency="true"></iframe></div>
<img src="<?=IMAGES_URL?>thankyou.png">
<?php }?>
<div class="info" style="font-size:13px;" align="left">
<div style="font-size:20px;padding-bottom:5px;"><?=$info[0]?></div>
<?=$info[1]?>
</div>
</div>

<div class="clear"></div>


<?php if (isset($ga_data)){ $g=$ga_data['trans'];?>
<script>
_gaq.push(['_addTrans',
           '<?=$g['transid']?>',           // order ID - required
           'Snapittoday',  // affiliation or store name
           '<?=$g['amount']?>',          // total - required
           '',           // tax
           '<?=$g['ship']+$g['cod']?>',              // shipping
           '<?=$g['city']?>',       // city
           '<?=$g['state']?>',     // state or province
           '<?=$g['country']?>'             // country
         ]);

<?php foreach($ga_data['orders'] as $o){?>          
         _gaq.push(['_addItem',
           '<?=$g['transid']?>',           // order ID - required
           '<?=$o['id']?>',           // SKU/code - required
           '<?=htmlspecialchars($o['name'])?>',        // product name
           '',   // category or variation
           '<?=$o['price']?>',          // unit price - required
           '<?=$o['quantity']?>'               // quantity - required
         ]);
<?php }?>
         _gaq.push(['_trackTrans']); //submits transaction to the Analytics servers

</script>
<?php }?>
</div>