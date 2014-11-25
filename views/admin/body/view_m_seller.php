<div class="container">
<h2 class="page_title">Market Place Seller Details</h2>

<div>
<fieldset style="width:50%;">
<legend><b>Seller Info</b></legend>
	<table cellspacing="10">
		<?php if($mseller_det){ ?>
		
			<?php $sellerid=$mseller_det['id'];?>
			<tr><td><b>Name</b></td><td><b>:</b></td><td><b><?php echo ucfirst($mseller_det['name']);?></b></td></tr>
			<tr><td><b>Territory | Town</b></td><td><b>:</b></td><td><b><?php echo $mseller_det['territory_name']?> | <?php echo $mseller_det['town_name']?></b></td></tr>
		<?php }?>
			<tr><td><b>Total PO Raised | Value</b></td><td><b>:</b></td><td><b><?php echo $ttl_raisedpo ;?> | <?php echo 'Rs '.$ttl_poval ;?></b></td></tr>
			<tr><td><b>Total Orders | Value</b></td><td><b>:</b></td><td><b><?php echo $ttl_ordrs ;?> | <?php echo 'Rs '.$ttl_ordrval;?></b></td></tr>
	</table>
</fieldset>
</div>
<br>

<div class="tab_view">
<ul>
	<li><a class="trg_onload" href="#po_det" onclick="load_marketseller_data(this,'po_det',0,0)">PO Details</a></li>
	<li><a href="#ordr_det" onclick="load_marketseller_data(this,'ordr_det',0,0)">Order Details</a></li>
</ul>

<div id="po_det">
<div class="tab_content"></div>
</div>

<div id="ordr_det">
<div class="tab_content"></div>
</div>

</div>

</div>

<script>
$('.tab_view').tabs();
$('.trg_onload').trigger('click');

var seller_id = '<?php echo  $mseller_det['id']?>';


function  load_marketseller_data(ele,type,pg,sellerid)
{
	$($(ele).attr('href')+' div.tab_content').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	
	$.post(site_url+'/admin/jx_get_marketseller_po_det/'+type+'/'+seller_id*1+'/'+pg*1,'',function(resp){
		$($(ele).attr('href')+' div.tab_content').html(resp.log_data+resp.pagi_links);
		
		$($(ele).attr('href')+' div.tab_content .datagridsort').tablesorter();
		
	},'json');
}

</script>