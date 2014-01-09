<div class="container snaplive">

<div class="headtxt">
<h1>Snapittoday Live!</h1>
<h3>See what people are buying at snapittoday.com</h3>
</div>

<div id="buffer" style="display:none">
</div>

<div class="itemcont" style="display:none">

<?php foreach($feed as $f)
	$this->load->view("body/livefeed_item",array("f"=>$f));
?>

</div>

<div class="clear"></div>

</div>
<style>
.container{
width:98%;
padding:0px 10px;
}
</style>
<script>
var feed_hash="<?=$feed_hash?>";
var feed_url="<?=site_url("live")?>";
</script>

<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
  {parsetags: 'explicit'}
</script>
<script src="<?=base_url()?>min/?g=livefeed&<?=str_replace(".","",APL_VER)?>"></script>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<div id="fb-root"></div>

<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<?php
