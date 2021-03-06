<?php
	$sel_brandid = $this->uri->segment(3);
?>
<div class="page_wrap" >
	<div class="page_top" >
		<div class="page_topbar_left fl_left">
			<h2 class="page_title"> Manage Vendors <?=isset($brand)?" for $brand products":""?></h2>
		</div>
		<div class="page_topbar_right fl_right">
			<a href="<?=site_url("admin/addvendor")?>" class="button button-rounded button-action button-small">Add Vendor</a>
		</div>
	</div>

	<div class="clearboth">
		Filter by Brand : 
		<select id="vend_disp_brands">
			<option value="0">Choose</option>
			<?php foreach($this->db->query("select distinct(b.id),b.name from m_vendor_info v join m_vendor_brand_link vb on vb.vendor_id=v.vendor_id join king_brands b on b.id=vb.brand_id order by b.name asc")->result_array() as $b){?>
				<option value="<?=$b['id']?>" <?php echo ($sel_brandid==$b['id'])?'selected':'';?> ><?=$b['name']?></option>
			<?php }?>
		</select>
	</div>
	<div class="page_content clearboth">
		<div class="clear"></div>
		<table class="datagrid datagridsort" width="100%">
			<thead>
				<tr>
					<th width="10">Slno</th>	
					<th width="300">Name</th>
					<th>Contact Details</th>
					<th>Brands Supported</th>
					<th>Total POs raised</th>
					<th>Total PO value</th>
					<th>Active</th>
					<th width="100px">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($vendors as $i=>$v){?>
					<tr>
						<td><?php echo $i+1; ?></td>
						<td style="text-transform: capitalize"><span><?=$v['vendor_name']?></span><br><span style="font-size: 11px;"><?=$v['city_name']?></span></td>
						<td><?=$v['contact_name']?><br><?=$v['mobile_no_1']?><br><?=$v['email_id_1']?></td>
						<td><?php foreach($this->erpm->getbrandsforvendor($v['vendor_id']) as $i=>$b){?><?=$i>0?", ":""?><a href="<?=site_url("admin/viewbrand/{$b['id']}")?>"><?=$b['name']?></a><?php }?></td>
						<td><?=$v['pos']?></td>
						<td><?=format_price($v['total_value'],0)?></td>
						<td><?=$v['is_active']?"YES":"NO"?></td>
						<td>
							<a class="dbllink" href="<?=site_url("admin/vendor/{$v['vendor_id']}")?>">view</a>&nbsp;
							<a class="dbllink" href="<?=site_url("admin/editvendor/{$v['vendor_id']}")?>">edit</a>&nbsp;
							<a href="<?php echo site_url("admin/purchaseorder/{$v['vendor_id']}")?>" target="_blank" >Create PO</a>
						</td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</div>

</div>
<script>
$(".datagrid").tablesorter({sortList: [[0,0]]});
$(function(){
	$("#vend_disp_brands").change(function(){
		v=$(this).val();
		if(v!="0")
			location='<?=site_url("admin/vendorsbybrand")?>/'+v;
		else
			location='<?=site_url("admin/vendors")?>';
	});
});
</script>
<style>
.leftcont{display:none;}
</style>
<?php
