<script type="text/javascript">
$(function(){window.setTimeout("redir()",1000);});
function redir()
{
	location="<?=site_url("admin")?>";
}
</script>
<div style="margin-top:70px;" align="center">
<div class="loginform" align="left">
<p style="margin:0px;margin-bottom:0px;">
<?php if(isset($signout)) echo "Signing Out"; else echo "Signing In";?>
<span style="float:right;">;)</span>
</p>
<div style="margin:0px;margin-top:0px;">please wait...</div>
</div>
</div>