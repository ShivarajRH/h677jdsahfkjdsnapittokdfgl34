<style>
.search_cont{
float:left;
margin:7px;
width:300px;
}
.search_cont h2{
font-size:110%;
margin:0px;
}
.search_cont .cont{
width:300px;

min-height:50px;
max-height:300px;
overflow:auto;
}
.search_cont .cont td{
padding:5px;
}
</style>

<div class="container">
<h2>Search Results</h2>


<div class="search_cont">
<h2>Orders (<?=count($orders)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($orders)?"grey":""?>" width="100%">
<thead><tr><th>Transid</th></tr></thead>
<tbody>
<?php foreach($orders as $o){?>
<tr>
<td><a class="link" href="<?=site_url("admin/trans/{$o['transid']}")?>"><?=$o['transid']?></a></td>
</tr>
<?php }if(empty($orders)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>

<div class="search_cont">
<h2>Invoices (<?=count($invoices)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($invoices)?"grey":""?>" width="100%">
<thead><tr><th>Invoice No</th><th>Transid</th></tr></thead>
<tbody>
<?php foreach($invoices as $o){?>
<tr><td><a class="link" href="<?=site_url("admin/invoice/{$o['invoice_no']}")?>"><?=$o['invoice_no']?></a></td>
<td><a class="link" href="<?=site_url("admin/trans/{$o['transid']}")?>"><?=$o['transid']?></a></td>
</tr>
<?php }if(empty($invoices)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>


<div class="search_cont">
<h2>Users (<?=count($users)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($users)?"grey":""?>" width="100%">
<thead><tr><th colspan="100%">User</th></tr></thead>
<tbody>
<?php foreach($users as $o){?>
<tr><td><a class="link" href="<?=site_url("admin/user/{$o['userid']}")?>"><?=$o['name']?></a></td></tr>
<?php }if(empty($users)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>


<div class="search_cont">
<h2>Deals (<?=count($deals)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($deals)?"grey":""?>" width="100%">
<thead><tr><th colspan="100%">Deal Name</th></tr></thead>
<tbody>
<?php foreach($deals as $o){?>
<tr><td><a class="link" href="<?=site_url("admin/deal/{$o['dealid']}")?>"><?=$o['name']?></a></td>
<td><a href="<?=site_url("admin/ordersfordeal/{$o['id']}")?>">orders</a>
</tr>
<?php }if(empty($deals)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>


<div class="search_cont">
<h2>Products (<?php if(!empty($products)){ echo count($products); } else if(!empty($products_bybarc)){ echo count($products_bybarc); } else { echo 0; } ?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($products)?"grey":""?>" width="100%">
<thead><tr><th colspan="100%">Product Name</th></tr></thead>
<tbody>
<?php foreach($products as $o){?>
<tr><td><a class="link" href="<?=site_url("admin/product/{$o['product_id']}")?>"><?=$o['product_name']?></a></td>
<td><?=$o['stock']?> stock</td>
<td>
<a href="<?=site_url("admin/editproduct/{$o['product_id']}")?>"><nobr>edit</nobr></a><br>
<a href="<?=site_url("admin/viewlinkeddeals/{$o['product_id']}")?>"><nobr>linked deals</nobr></a>
</td>
</tr>
<?php } ?>
<?php foreach($products_bybarc as $o){?>
<tr><td><a class="link" href="<?=site_url("admin/product/{$o['product_id']}")?>"><?=$o['product_name']?></a></td>
<td><?=$o['stock']?> stock</td>
<td>
<a href="<?=site_url("admin/editproduct/{$o['product_id']}")?>"><nobr>edit</nobr></a><br>
<a href="<?=site_url("admin/viewlinkeddeals/{$o['product_id']}")?>"><nobr>linked deals</nobr></a>
</td>
</tr>
<?php } ?>
<?php if(empty($products) && empty($products_bybarc)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>


<div class="search_cont">
<h2>Brands (<?=count($brands)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($brands)?"grey":""?>" width="100%">
<thead><tr><th colspan="100%">Brand Name</th></tr></thead>
<tbody>
<?php foreach($brands as $o){?>
<tr><td><a class="link" href="<?=site_url("admin/viewbrand/{$o['id']}")?>"><?=$o['name']?></a></td>
</tr>
<?php }if(empty($brands)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>


<div class="search_cont">
<h2>Categories (<?=count($categories)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($categories)?"grey":""?>" width="100%">
<thead><tr><th colspan="100%">Category Name</th></tr></thead>
<tbody>
<?php foreach($categories as $o){?>
<tr><td><a class="link" href="<?=site_url("admin/viewcategory/{$o['id']}")?>"><?=$o['name']?></a></td>
</tr>
<?php }if(empty($categories)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>


<div class="search_cont">
<h2>Clients (<?=count($clients)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($clients)?"grey":""?>" width="100%">
<thead><tr><th colspan="100%">Client Name</th></tr></thead>
<tbody>
<?php foreach($clients as $o){?>
<tr><td><a class="link" href="<?=site_url("admin/editclient/{$o['client_id']}")?>"><?=$o['client_name']?></a></td>
<td><a href="<?=site_url("admin/addclientorder/{$o['client_id']}")?>">new order</a></td>
</tr>
<?php }if(empty($clients)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>


<div class="search_cont">
<h2>Vendors (<?=count($vendors)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($vendors)?"grey":""?>" width="100%">
<thead><tr><th colspan="100%">Vendor Name</th></tr></thead>
<tbody>
<?php foreach($vendors as $o){?>
<tr><td><a class="link" href="<?=site_url("admin/vendor/{$o['vendor_id']}")?>"><?=$o['vendor_name']?></a></td>
<td><a href="<?=site_url("admin/editvendor/{$o['vendor_id']}")?>">edit</a></td>
</tr>
<?php }if(empty($vendors)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>


<div class="search_cont">
<h2>Tickets (<?=count($tickets)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($tickets)?"grey":""?>" width="100%">
<thead><tr><th colspan="100%">Ticket No</th></tr></thead>
<tbody>
<?php foreach($tickets as $o){?>
<tr><td><a class="link" href="<?=site_url("admin/ticket/{$o['ticket_id']}")?>">TK<?=$o['ticket_no']?></a></td>
</tr>
<?php }if(empty($tickets)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>


<div class="search_cont">
<h2>AWBs (<?=count($awbs)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($awbs)?"grey":""?>" width="100%">
<thead><tr><th>Transid</th><th>Invoice</th><th>Batch</th></tr></thead>
<tbody>
<?php foreach($awbs as $a){?>
<tr><td><a class="link" href="<?=site_url("admin/trans/{$a['transid']}")?>"><?=$a['transid']?></a></td><td><a target="_blank" href="<?=site_url("admin/invoice/{$a['invoice_no']}")?>"><?=$a['invoice_no']?></a></td><td><a href="<?=site_url("admin/batch/{$a['batch_id']}")?>">BATCH<?=$a['batch_id']?></a>
</tr>
<?php }if(empty($awbs)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>


<div class="search_cont">
<h2>PNH Deals (<?=count($pnh_deals)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($pnh_deals)?"grey":""?>" width="100%">
<thead><tr><th>PNH ID</th><th>Deal Name</th></tr></thead>
<tbody>
<?php foreach($pnh_deals as $d){?>
<tr>
<td><?=$d['pnh_id']?></td>
<td><a class="link" href="<?=site_url("admin/pnh_deal/{$d['id']}")?>"><?=$d['name']?></a></td>
</tr>
<?php }if(empty($pnh_deals)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>


<div class="search_cont">
<h2>PNH Franchises (<?=count($pnh_franchises)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($pnh_franchises)?"grey":""?>" width="100%">
<thead><tr><th>FID</th><th>Name</th></tr></thead>
<tbody>
<?php foreach($pnh_franchises as $d){?>
<tr>
<td><?=$d['pnh_franchise_id']?></td>
<td><a class="link" href="<?=site_url("admin/pnh_franchise/{$d['franchise_id']}")?>"><?=$d['franchise_name']?></a></td>
</tr>
<?php }if(empty($pnh_franchises)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>

<div class="search_cont">
<h2>PNH Members (<?=count($pnh_members)?>)</h2>
<div class="cont">
<table class="datagrid smallheader noprint <?=empty($pnh_members)?"grey":""?>" width="100%">
<thead><tr><th>MID</th><th>Name</th></tr></thead>
<tbody>
<?php foreach($pnh_members as $d){?>
<tr>
<td><?=$d['pnh_member_id']?></td>
<td><a class="link" href="<?=site_url("admin/pnh_viewmember/{$d['user_id']}")?>"><?=$d['name']?></a></td>
</tr>
<?php }if(empty($pnh_members)){?>
<tr><td colspan="100%">no matches</td>
<?php }?>
</tbody>
</table>
</div>
</div>


</div>
<?php
