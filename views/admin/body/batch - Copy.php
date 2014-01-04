<div class="container">
<div style="margin:3px;float: right;background: #ffffa0;padding:5px;">
<b>Remarks : </b>
<p style="margin:3px;font-size: 11px;min-width: 100px;display: inline;"><?php echo $batch['batch_remarks']?$batch['batch_remarks']:'--na--'; ?></p>
</div>
<h2>Shipment Batch Process : BATCH<?=$batch['batch_id']?></h2>
<div>
	<a target="_blank" href="<?=site_url("admin/product_proc_list_for_batch/{$batch['batch_id']}")?>">Generate product procurement list</a>
</div>

<?php
$is_pnh=false;
$inv=$invoices[0];
 if(empty($inv['transid'])) $inv['transid']=$inv['pi_transid'];
 if($this->db->query("select is_pnh from king_transactions where transid=?",$inv['transid'])->row()->is_pnh==1){ $is_pnh=true; ?>
<div style="padding:15px;">
Filter by : <select id="territory"><option value="0">All Territories</option>
<?php foreach($this->db->query("select id,territory_name as name from pnh_m_territory_info order by name asc")->result_array() as $t){?>
<option value="<?=$t['id']?>"><?=$t['name']?></option>
<?php }?>
</select>
  <select id="town"><option value="0">All Towns</option>
<?php foreach($this->db->query("select id,town_name as name from pnh_towns order by name asc")->result_array() as $t){?>
<option value="<?=$t['id']?>"><?=$t['name']?></option>
<?php }?>
</select>
  <select id="franchise"><option value="0">All Franchises</option>
<?php foreach($this->db->query("select franchise_id as id,franchise_name as name from pnh_m_franchise_info order by name asc")->result_array() as $t){?>
<option value="<?=$t['id']?>"><?=$t['name']?></option>
<?php }?>
</select>
</div>
<?php }?>



<table id="batch_orders" class="datagrid datagridsort" width="100%">
<thead>
<tr><th><input type="checkbox" class="chk_all"></th><th>Sno</th><th>Proforma Invoice</th><th>Invoice Number</th><th>Order</th><th>Ordered On</th><th>Packed</th><th>Shipped</th><th>Packed On</th><th>Shipped On</th><?php if($is_pnh){?><th>Territory</th><th>Franchise</th><?php }?><th>Action</th></tr>
</thead>
<tbody>
<?php $sno=0; foreach($invoices as $inv){ if(empty($inv['transid'])) $inv['transid']=$inv['pi_transid'];

if($is_pnh)
	$fran=$this->db->query("select f.franchise_name,f.franchise_id,t.territory_name,t.id as terry_id,tw.id as town_id from king_transactions ta join pnh_m_franchise_info f on f.franchise_id=ta.franchise_id join pnh_m_territory_info t on t.id=f.territory_id join pnh_towns tw on tw.id=f.town_id where ta.transid=?",$inv['transid'])->row_array();
	if(!$fran)
		continue;
?>
<tr <?php if($is_pnh){?>class="tbatch tw<?=$fran['town_id']?> ty<?=$fran['terry_id']?> fran<?=$fran['franchise_id']?>"<?php }?>>
<td width=1 align="center"><?php if(!$inv['packed']){?><input type="checkbox" class="chk" value="<?=$inv['p_invoice_no']?>"><?php }?></td>
<td width="10"><?=++$sno?></td>
<td><a href="<?=site_url("admin/proforma_invoice/{$inv['p_invoice_no']}")?>" <?=!$inv['p_invoice_status']?'style="text-decoration:line-through;"':''?>><?=$inv['p_invoice_no']?></a></td>
<td>
<?php if($inv['invoice_no']!=0){?>
<a href="<?=site_url("admin/invoice/{$inv['invoice_no']}")?>" <?=!$inv['invoice_status']?'style="text-decoration:line-through;"':''?>><?=$inv['invoice_no']?></a>
<?php }?>
</td>
<td><a href="<?=site_url("admin/trans/{$inv['transid']}")?>"><?=$inv['transid']?></a></td>
<td><?=format_date(date('Y-m-d H:i:s',$inv['ordered_on']))?></td>
<td><?=$inv['packed']?"YES":"NO"?></td>
<td><?=$inv['shipped']?"YES":"NO"?></td>
<td><?=$inv['packed']?$inv['packed_on']:"na"?></td>
<td><?=$inv['shipped']?$inv['shipped_on']:"na"?></td>
<?php if($is_pnh){?>
<td><?=$fran['territory_name']?></td>
<td><?=$fran['franchise_name']?></td>
<?php }?>
<td>
<?php if($inv['p_invoice_status']==0 && $inv['p_invoice_no']!=0) echo 'no action';else{ if(!$inv['packed']){?>
<a class="link" href="<?=site_url("admin/pack_invoice/{$inv['p_invoice_no']}")?>">pack this invoice</a>
<?php }elseif(!$inv['shipped']){?>
<a class="link" href="<?=site_url("admin/outscan")?>">ship products</a>
<?php }else{?>
<a class="link" href="<?=site_url("admin/invoice/{$inv['invoice_no']}")?>">view</a>
<?php }}?>
</td>
</tr>
<?php }?>
</tbody>
</table>
<div style="background:#eee;padding:3px;">With Selected : <input type="button" class="do_sw" value="Generate stock procure list">
<form id="f_sw" action="<?=site_url("admin/stock_procure_list")?>" method="post" target="_blank"><input type="hidden" class="f_tids" name="tids" ></form>
</div>

</div>

<script>





$(function(){

	
	
	
	
	$("#franchise,#town,#territory").change(function(){
		$(".tbatch").hide();
		tw=$("#town").val();
		ty=$("#territory").val();
		fran=$("#franchise").val();
		s=".tbatch";
		if(fran!=0)
			s=s+".fran"+fran+"";
		if(tw!=0)
			s=s+".tw"+tw+"";
		if(ty!=0)
			s=s+".ty"+ty+"";
		$(s).show();
	});

	$(".chk").attr("checked",false);
	$(".chk_all").change(function(){
		if($(this).attr("checked"))
			$(".chk").attr("checked",true);
		else
			$(".chk").attr("checked",false);
	}).attr("checked",false);

	$(".do_sw").click(function(){
		var tids=[];
		$(".chk:checked").each(function(){
			tids.push($(this).val());
		});
		if(tids.length==0)
		{
			alert("No items selected");
			return false;
		}
		$(".f_tids").val(tids.join(","));
		$("#f_sw").submit();
	});	


	$("#batch_orders").tablesorter({headers:{0:{sorter:false}},sortList: [[11,0]]});
	
	$('#batch_orders tr').each(function(i,itm){
		$('td:eq(1)',this).text(i);
	});

	
	
});

</script>


<?php
