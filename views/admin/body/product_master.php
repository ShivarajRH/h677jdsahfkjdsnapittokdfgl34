<link rel="stylesheet"	href="<?php echo site_url(); ?>css/datatable/manage.product.css" type="text/css" />
<script	src="<?php echo site_url(); ?>js/datatable/manage.product.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">  
$(document).ready(function() {
	js_manage_product();
});
</script>
<h2 class="page_title">Product Master</h2>
<div class="container page_wrap">
	<div class="page_top">
	       <a href="<?php echo site_url('admin/addproduct')?>" target="_blank"
			class="button fl_right button-rounded button-action button-tiny">Add Product</a>
		   <a href="<?php echo site_url('admin/po_on_top_sold')?>" target="_blank"
			class="button fl_right button-rounded button-action button-tiny">Top Sold Products</a>
			<a href="<?php echo site_url('admin/prod_mrp_update')?>" target="_blank"
			class="button fl_right button-rounded button-action button-tiny">Update Mrp</a>&nbsp
			<a href="<?php echo site_url('admin/get_barcode_info')?>" target="_blank"
			class="button fl_right button-rounded button-action button-tiny">Add Barcode</a>&nbsp	
					
		<h1 class="page_title">Manage Products</h1>
	</div>
	<section>
		<table id="product_table" class="display " cellspacing="0" width="100%">
			<thead>
				<tr>
					<th></th>
					<th>Product ID</th>
					<th>Product Name</th>
					<th>Product Mrp</th>
					<th class="brandnameval">Brand </th>
					<th class="categoryname">Category</th>
					<th>Total Linked Deal</th>
					<th>Current Stock</th>
					<th class="sourceableval">Sourceable</th>
					<th>Is Active</th>
					<th>Action</th>
				</tr>
			</thead>
		</table>
	</section>
</div>

