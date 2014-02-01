<div class="container">

<div style="float:right;margin-right:200px;">
Date range : <input type="text" class="inp" size=10 id="s_from"> to <input type="text" class="inp" size=10 id="s_to"> <input type="button" value="Go" onclick='go_date()'>  
</div>


<h2><?=$pagetitle?></h2>

<table class="datagrid datagridsort">
<theaD><Tr><th>Franchise</th><th>Margin</th><th>Brand</th><th>Category</th><th>Deals</th><th>Start</th><th>End</th><th>Reason</th><th>Created On</th><Th>Created By</Th></Tr></theaD>
<tbody>
<?php foreach($discs as $d){?>
<tr>
<td><a class="link" href="<?=site_url("admin/pnh_franchise/{$d['franchise_id']}")?>"><?=$d['franchise_name']?></a></td>
<td><?=$d['sch_discount']?>%</td>
<td><?=$d['brandid']==0?"All Brand":$this->db->query("select name from king_brands where id=?",$d['brandid'])->row()->name?></td>
<td><?=$d['catid']==0?"All Categories":$this->db->query("select name from king_categories where id=?",$d['catid'])->row()->name?></td>
<td><?=empty($d['deal'])?"All Deals":$d['deal']?>
<td><?=date("d/m/y",$d['sch_discount_start'])?></td>
<td><?=date("d/m/y",$d['sch_discount_end'])?></td>
<td><?=$d['reason']?></td>
<td><?=date("d/m/y g:ia ",$d['created_on'])?></td>
<td><?=$d['created_by']?></td>
</tr>
<?php }?>
</tbody>

</table>

</div>


<script>
$('.leftcont').hide();
function go_date()
{
	from=$("#s_from").val();
	to=$("#s_to").val();
	if(from.length==0 || to.length==0)
	{
		alert("Check date");return;
	}
	location="<?=site_url("admin/pnh_sch_discounts")?>/<?=$this->uri->segment(3)?$this->uri->segment(3):0?>/"+from+"/"+to;
}

$(function(){
	$("#s_from,#s_to").datepicker();
});
$('.datagridsort').tablesorter( {sortList: [[0,0]]} ); 
</script>


<?php
