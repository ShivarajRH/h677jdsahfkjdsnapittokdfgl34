<div class="container">
<h2>Stock Checker</h2>


<div style="padding:10px 10px;">
Enter Item Name : <input type="text" class="inp sc_name" size="60" value="<?=isset($deal)?$deal['name']:""?>">
<div class="srch_result_pop closeonclick" id="po_prod_list"></div>
</div>

<div style="padding:10px 10px;">
<form method="post">
Or Enter ITEMID : <input type="text" class="inp sc_iid" name="id" size="30" value="<?=isset($deal)?$deal['id']:""?>"><input type="submit" value="Go">
</form>
</div>

<?php if(isset($deal)){ $s=$this->db->query("select live from king_dealitems where id=?",$deal['id'])->row()->live;?>
<div>
<h3>Stock status for '<?=$deal['name']?>'</h3>
<table cellpadding=7 style="font-size:120%" cellspacing=0>
<tr style="background:#eee;"><th>Current Site Status</th><th>Actual Status</th></tr>
<tr>
<td valign="middle" style="text-align:center;vertical-align:middle;padding:10px;height:50px;width:180px;background:<?=$s?"green":"#f33"?>;color:#fff;font-weight:bold;font-size:140%;"><?=$s?"AVAILABLE":"OUT OF STOCK"?></td>
<td valign="middle" style="text-align:center;vertical-align:middle;padding:10px;height:50px;width:180px;background:<?=$status?"green":"#f33"?>;color:#fff;font-weight:bold;font-size:140%;"><?=$status?"AVAILABLE":"OUT OF STOCK"?></td>
</tr>
</table>
</div>
<?php }?>


</div>

<script>

function adddealitem(id)
{
	$(".sc_iid").val(id);
	$(".sc_iid").parent().submit();
}

$(function(){
	$(".sc_name").keydown(function(){
		q=$(this).val();
		$.post("<?=site_url("admin/jx_search_deals")?>",{q:q},function(data){
			$("#po_prod_list").html(data).show();
		});
	});
});
</script>
<?php
