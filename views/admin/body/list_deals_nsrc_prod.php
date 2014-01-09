<div class="container">

<div class="dash_bar_right" style="padding:7px;text-align: center">
	<a href="<?=site_url('admin/exp_deals_nrc_prodslist')?>" style="float: none	">Export Data</a>   
</div>

<div class="dash_bar_right" style="padding:7px;">
	<b>Filter</b>: <span style="font-size: 13px;"><input type="checkbox" value="1" name="fil_stk"> Stock Available 
	<input type="checkbox" value="1" name="fil_publish"> Is Publish 
	<input type="checkbox" value="1" name="fil_live"> Is Live </span>   
</div>

<h2>Manage Deals - Product Not Sourceable </h2>
<div style="float: right;">
	Total : <b id="fil_total"><?php echo count($deals);?></b> Deals found
</div>
<table class="datagrid" width="100%">
<thead><tr><th>Sno</th><th>Deal ID</th><th>DealName</th><th>MRP</th><th>Price</th><th width="70">Linked Qty</th><th>Stock</th><th>Publish</th><th>Live</th></tr></thead>
<tbody>
<?php $i=1; foreach($deals as $p){?>
<tr class="deal fil_<?php echo ($p['stk']?1:0).'_'.$p['publish'].'_'.$p['live'];?>" >
<td><?=$i++?></td>
<td><?=$p['id']?></td>
<td><a href="<?=site_url("admin/deal/{$p['id']}")?>" target="_blank"><?=$p['name']?></a></td>
<td><?=$p['orgprice']?></td>
<td><?=$p['price']?></td>
<td><?=$p['qty']?></td>
<td><?=$p['stk']?></td>
<td><?=$p['publish']?'Yes':'No'?></td>
<td><span><?=$p['live']?'Yes':'No'?></span>
	<?php
		if($p['live']||$p['publish']||$p['stk'])
		{
	?>
		<a href="javascript:void(0)" stk="<?=$p['stk']?>" publish="<?=$p['publish']?>" live="<?=$p['live']?>" itemid="<?=$p['id']?>" class="danger_link jx_upd_deallivestat">Change</a>
	<?php		
		}
	?>	
	
</td>

</tr>
<?php }?>
</tbody>
</table>

</div>

<script>

$('input[name="fil_stk"],input[name="fil_publish"],input[name="fil_live"]').change(function(){
	$('.deal:visible').hide();
	var stk_stat = ($('input[name="fil_stk"]').is(':checked'))?1:0;
	var pub_stat = ($('input[name="fil_publish"]').is(':checked'))?1:0;
	var live_stat = ($('input[name="fil_live"]').is(':checked'))?1:0;
		if(stk_stat+pub_stat+live_stat)
			$('.fil_'+stk_stat+'_'+pub_stat+'_'+live_stat).show();
		else
			$('.deal').show();
			
		$('#fil_total').text($('.deal:visible').length);
		
		$('.deal:visible').each(function(i,j){
			$('td:first',this).text(i+1);
		});
				
});
$(function(){
	$('.danger_link').unbind('click');	
	$('.jx_upd_deallivestat').click(function(e){
	e.preventDefault();
	ele = $(this);
	
	if(confirm("Are you sure want to mark this deal as "+((ele.attr('live')*1)?'Not Live':'Live')+' ? '))
	{
		$.post(site_url+'/admin/jx_upd_deallivestat',{'itemid':$(this).attr('itemid')},function(resp){
			if(resp.status == 'success')
			{
				$('span',ele.parent()).text((resp.live_stat*1)?'Yes':'No');
				ele.attr('live',resp.live_stat);
			}
				
		},'json');	
	}
});
});



function showrange()
{
	location="<?=site_url("admin/product_src_changelog")?>/"+$("#ds_range").val()+"/"+$("#de_range").val();
}

$(function(){
	$("#ds_range,#de_range").datepicker();
});
</script>

<?php
