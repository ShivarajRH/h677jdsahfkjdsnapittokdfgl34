<?php 
foreach($clients as $c)
{
	$n_inv[$c['client_id']]=0;
	$n_ord[$c['client_id']]=0;
}	
$raw_inv=$this->db->query("select client_id,sum(total_invoice_value) as s from t_client_invoice_info group by client_id")->result_array();
foreach($raw_inv as $r)
	$n_inv[$r['client_id']]=$r['s'];
$raw_norders=$this->db->query("select client_id,count(order_id) as n from t_client_order_info group by client_id")->result_array();
foreach($raw_norders as $r)
	$n_ord[$r['client_id']]=$r['n'];
foreach($this->db->query("select * from m_client_contacts_info group by client_id limit 1")->result_array() as $c)
	$cnts[$c['client_id']]=array($c['contact_name'],$c['mobile_no_1'],$c['email_id_1']);
?>
<div class="container">
<h2 style="margin-bottom:0px;">Corporate Clients</h2>
<a href="<?=site_url("admin/addclient")?>">Add new client</a>
<div>
	<div class="dash_bar">
	<a href="<?=site_url("admin/clients")?>"></a>
	Total Clients <span><?=$this->db->query("select count(1) as l from m_client_info")->row()->l?></span>
	</div>
	<div class="clear"></div>
</div>
<table class="datagrid">
<thead>
<tr><th>Client Name</th><th>City</th><th>Contact Person</th><th>Total invoice value</th><th>Total orders</th><th></th></tr>
</thead>
<tbody>
<?php foreach($clients as $c){?>
<tr>
<td><?=$c['client_name']?></td>
<td><?=$c['city_name']?></td>
<td>
<?=$cnts[$c['client_id']][0]?><br>
<?=$cnts[$c['client_id']][1]?>, <?=$cnts[$c['client_id']][2]?>
</td>
<td>Rs <?=$n_inv[$c['client_id']]?></td>
<td><?=$n_ord[$c['client_id']]?></td>
<td>
<a href="<?=site_url("admin/editclient/{$c['client_id']}")?>">edit client</a> &nbsp;&nbsp;&nbsp;
<a href="<?=site_url("admin/addclientorder/{$c['client_id']}")?>">new order</a> &nbsp;&nbsp;&nbsp;
<a href="<?=site_url("admin/client_orders_by_client/{$c['client_id']}")?>">view orders</a>
</td>
</tr>
<?php }if(empty($clients)){?>
<tr><td colspan="100%">no clients to show</td></tr>
<?php }?>
</tbody>
</table>
</div>
<?php
