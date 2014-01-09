<div class="container">

<div style="padding-top:10px">
<?php if(!isset($byclient)){?>
	<div class="dash_bar">
		<span><?=$this->db->query("select count(1) as l from t_client_order_info")->row()->l?></span>
		Total Orders
	</div>
<?php }?>
	<div class="dash_bar">
		view by client : <select id="sel_client">
		<option value="0">select</option>
		<?php foreach($this->db->query("select client_id,client_name from m_client_info order by client_name")->result_array() as $cn){?>
		<option value="<?=$cn['client_id']?>"><?=$cn['client_name']?></option>
		<?php }?>
		</select>
	</div>
</div>

<div class="clear"></div>
<h2>Corporate Client Orders <?=isset($pagetitle)?$pagetitle:" this month"?></h2>
<table class="datagrid">
<thead>
<tr><th>Order ID</th><th>Client</th><th>Reference No</th><th>Status</th><th>Created On</th><th>Created By</th></tr>
</thead>
<tbody>
<?php foreach($orders as $o){?>
<tr>
<td><a class="link" href="<?=site_url("admin/client_order/{$o['order_id']}")?>">O<?=$o['order_id']?></a></td>
<td><a href="<?=site_url("admin/editclient/{$o['client_id']}")?>"><?=$o['client']?></a></td>
<td><?=$o['order_reference_no']?></td>
<td><?php switch($o['order_status']){
	case 0:
		echo "Pending";break;
	case 1:
		echo "Partial";break;
	case 2:
		echo "Complete";break;
	case 3:
		echo "closed";break;
}?></td>
<td><?=$o['created_on']?></td>
<td><?=$o['created_by']?></td>
</tr>
<?php }if(empty($orders)){?>
<tr><td colspan="100%">no orders to show</td></tr>
<?php }?>
</tbody>
</table>


</div>

<script>
$(function(){
	$("#sel_client").change(function(){
		if($(this).val()==0)
			return;
		location='<?=site_url('admin/client_orders_by_client')?>/'+$(this).val();
	});
});
</script>

<?php
