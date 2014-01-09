<?php $user=$this->session->userdata("callc_user");?>
<div id="header" style="padding:10px 0px;">
<div style="background:#000;">
<img src="<?=base_url()?>images/logo.png">
</div>
<?php if($this->uri->segment(2)!="login"){ ?>

<div style="padding:10px;padding-top:20px;clear:both;">
<form id="tranform">
Enter Transaction id : <input type="text">
</form>
</div>

<script>
$(function(){
	$("#tranform").submit(function(){
		location="<?=site_url("callcenter/trans")?>/"+$("input",$(this)).val();
		return false;
	});
	$(".datagrid tr").hover(function(){
		$("td",$(this)).css("background","#eee");
	},function(){
		$("td",$(this)).css("background","transparent");
	});
});
</script>

<?php } ?>
</div>
