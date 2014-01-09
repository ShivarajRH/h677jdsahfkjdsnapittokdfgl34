<?php
	$return_request_cond = $this->config->item('return_request_cond');
	
?>
<div class="container page_wrap">
	
	<div class="page_topbar" >
		<h2 class="page_title fl_left">Return Product Service log</h2>
		
		<div class="page_action_buttonss fl_right" align="right">
			<a href="<?php echo site_url('admin/pnh_invoice_returns') ?>">List Returns</a>
			&nbsp;&nbsp;
			<a href="<?php echo site_url('admin/add_pnh_invoice_return') ?>">Add Return</a>
		</div>
		<br />
		<div class="page_topbar_left fl_left" style="clear: both">
			<b>Total : </b> <?php echo $prod_ttl;?>
		</div>
		
	</div>
	<div style="clear:both">&nbsp;</div>
	<div class="page_content">
		<table class="datagrid" width="100%">
			<thead>
				<tr>
					<th width="30">Slno</th><th width="130">ReturnedOn</th><th width="30">ReturnID</th><th width="200">Franchise Name</th><th width="100">Invoiceno</th><th width="100">Product Name</th><th width="10">Qty</th><th width="150">Returned By</th><th width="150">Handled By</th><th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					if($prod_list)
					{
						$i=0;
						foreach ($prod_list as $ret)
						{
				?>
							<tr>
								<td><b><?php echo $i+1; ?></b></td>
								<td><?php echo format_datetime($ret['returned_on']) ?></td>
								<td><?php echo anchor('admin/view_pnh_invoice_return/'.$ret['return_id'],$ret['return_id'],'class="link"') ?></td>
								<td><?php echo $ret['franchise_name'] ?></td>
								<td><?php echo $ret['invoice_no'] ?></td>
								<td><?php echo $ret['product_name'] ?></td>
								<td><?php echo $ret['qty'] ?></td>
								<td><?php echo $ret['return_by']?$ret['return_by']:' ' ?></td>
								<td><?php echo $ret['handled_by_name'] ?></td>
								<td><?php echo $return_request_cond[$ret['status']]; ?></td>
							</tr>
				<?php 
							$i++;
						}
					} 
				?>
			</tbody>
		</table>
		<div align="right" class="pagination">
			<?php echo $pagination; ?>
		</div>
	</div>
	
</div>
<style>
	.leftcont{display: none;}
</style>