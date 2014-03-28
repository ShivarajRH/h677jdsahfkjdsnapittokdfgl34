<div class="container">



<h2><?=$pagetitle?></h2>

<div class="dash_bar " style="padding:5px;">
Date range : <input type="text" class="inp" size=10 id="s_from"> to <input type="text" class="inp" size=10 id="s_to"> <input type="button" value="Go" onclick='go_date()'>  
</div>

<div class="dash_bar fl_right">
Show for vendor : <select id="vendor"><option value="0">select</option>
<?php foreach($this->db->query("select vendor_id,vendor_name from m_vendor_info order by vendor_name asc")->result_array() as $v){?>
<option <?php if($this->uri->segment(3)==$v['vendor_id']){?>selected<?php }?> value="<?=$v['vendor_id']?>"><?=$v['vendor_name']?></option>
<?php }?>
</select>
</div>


<div class="clear"></div>

<table class="datagrid" width="100%">
<thead><tr><th>Sno</th><th>GRN</th><th>Vendor</th><th>Items</th><th>Received Qtys</th><th>Invoiced Qtys</th><th>Invoice Value</th><th>Taken On</th></tr></thead>
<tbody>
<?php  $i=1; foreach($grns as $g){?>
<Tr>
<td><?=$i++?></td>
<td><a href="<?=site_url("admin/viewgrn/{$g['grn_id']}")?>" class="link"><?=$g['grn_id']?></a></td>
<td><a href="<?=site_url("admin/vendor/{$g['vendor_id']}")?>" class="link"><?=$g['vendor_name']?></a></td>
<td><?=$g['items']?></td>
<td><?=$g['received']?></td>
<td><?=$g['invoiced']?></td>
<td>Rs <?=number_format($g['invoice_value'],2)?></td>
<td><?=format_datetime($g['created_on'])?></td>
</Tr>
<?php }?>
</tbody>
</table>

</div>


<script>

function go_date()
{
	from=$("#s_from").val();
	to=$("#s_to").val();
	if(from.length==0 || to.length==0)
	{
		alert("Check date");return;
	}
	location="<?=site_url("admin/stock_intake_list")?>/<?=$this->uri->segment(3)?$this->uri->segment(3):0?>/"+from+"/"+to;
}

$(function(){

	$("#vendor").change(function(){
		location="<?=site_url("admin/stock_intake_list")?>/"+$(this).val()+"/<?=$this->uri->segment(4)?$this->uri->segment(4):0?>/<?=$this->uri->segment(5)?$this->uri->segment(5):0?>/";
	});

	$("#s_from,#s_to").datepicker();
});
</script>


<?php
