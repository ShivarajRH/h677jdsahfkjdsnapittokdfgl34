<div class="container" style="clear:both;padding-top:10px;">
    <div align="center" class="scrollup">Scroll to top</div>
<?php if(!isset($smallheader)){?>
<div style="clear:both;margin-top:0px;">
<div class="">
<?php }else{?>
<div style="clear:both;margin-top:0px;"><br></div>
<?php }?>
<div align="left" class="footerlinks<?php if(isset($smallheader)) echo "2";?>">
<div style="float:right">
ADMIN PANEL</div>
<div>&copy; 2013 Snapittoday.com</div>
</div>
<?php 
if(!isset($smalheader)){?>
</div>
</div>
<?php }	?>
</div>
<style>
.form, .deal{
background-color:#fff;
}
</style>
<script type="text/javascript">
    $(window).scroll(function(){if ($(this).scrollTop() > 100) {$('.scrollup').fadeIn();} else { $('.scrollup').fadeOut(); }});
     $('.scrollup').click(function(){ $("html, body").animate({ scrollTop: 0 }, 1000); return false; });
</script>