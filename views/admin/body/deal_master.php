<link rel="stylesheet" href="<?php echo site_url(); ?>css/datatable/manage.deal.css" type="text/css" />	
<script src="<?php echo site_url(); ?>js/datatable/manage.deal.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">  
$(document).ready(function() {
	js_manage_deal();
});
</script>
<h2 class="page_title">Deal Master</h2>
<div class="container page_wrap">
	<div class="page_top">
		<a href="<?php echo site_url('admin/pnh_adddeal')?>" target="_blank" class="button fl_right button-rounded button-action button-tiny">Add Deal</a>
		<h1 class="page_title">Manage Deal</h1>
	</div>
	<section>
		<table id="deal_table" class="display" cellspacing="0" width="100%">
			<thead>
			 <tr>
			        <th></th>
			        <th>Item ID</th>
			        <th class="dealfor">Deal For</th>
			        <th>PNH ID</th>
					<th>Deal Name</th>					
				    <th class="brandnameval">Brand</th>
					<th class="categoryname">Category</th>
					<th>Mrp</th>
					<th>Offer Price</th>
					<th>Member Price</th>
					<th>Current Stock</th>
					<th class="statusval">Status</th>
					<th>Action</th>					
			</tr>
			</thead>
		</table>
	</section>
</div>	
