<?php
	$order_by = $this->config->item('order_by');
?>
<div class="page_wrap container">
	
	<div class="page_topbar" >
		<h2 class="page_title fl_left">Update pending returns stock</h2>	
		
		<div class="page_action_buttons fl_right" align="right">
			<a href="<?php echo site_url('admin/add_pnh_invoice_return/'.$type) ?>">Add Return</a>
			&nbsp;
			&nbsp;
			<a href="<?php echo site_url('admin/pnh_invoice_returns/'.$type) ?>">List All Returns</a>
		</div>
		
		<br />
	</div>
		
	<div style="clear:both">&nbsp;</div>
	<div class="page_content form_block">
		<?php if($prd_det)
		{
			?>
			<table class="datagrid" cellpadding="5" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th width="3%">#</th>
						<th width="8%">ReturnedOn</th>
						<th width="7%">ReturnId</th>
						<th width="8%">By</th>
						<th width="10%">From</th>
						<th width="8%">InvoiceNo</th>
						<th width="30%">Product Name</th>
						<th width="5%">Qty</th>
						<th>Handle by</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						foreach($prd_det as $i=>$d)
						{
							?>
							<tr>
								<td>
									<?php echo $i+1;?>
									<input type="hidden" name="return_prd_id" value="<?php echo $d['return_product_id']; ?>">
								</td>
								<td><?php echo format_datetime($d['returned_on']);?></td>
								<td><a href="<?php echo site_url('admin/view_pnh_invoice_return/'.$d['return_id'].'/'.$type); ?>" target="_blank"><?php echo $d['return_id'];?></a></td>
								<td>
									<?php
										if($d['order_from']<2)
											echo  $order_by[$d['order_from']];
										else 
											echo $this->db->query("select b.name from king_transactions a join  partner_info b on b.id=a.partner_id where a.transid=?",$d['transid'])->row()->name;
									?>
								</td>
								<td>
									<?php 
										if($d['order_from']==0)
											echo anchor("admin/pnh_franchise/".$d['franchise_id'],$this->db->query("select bill_person from king_orders where id=? group by id",$d['order_id'])->row()->bill_person);
										else
											echo anchor("admin/user/".$d['userid'],$d['bill_person'],"target='_blank'");
									?>
								</td>
								<td><?php echo anchor("admin/invoice/".$d['invoice_no'],$d['invoice_no']);?></td>
								<td><?php echo anchor("admin/product/".$d['product_id'],$d['product_name'],"target='_blank'");?></td>
								<td><?php echo $d['qty'];?></td>
								<td><?php echo $d['handled_by_name'];?></td>
								<td class="action_<?php echo $d['return_product_id'];?>">
									<button class="move_to_stock button button-flat-royal button-small button-rounded" return_id="<?php echo $d['return_product_id']; ?>">Move stock</button>
								</td>
							</tr>
							<?php
						}
					?>
				</tbody>
			</table>
			<?php 
		}else{
			echo '<div align="center">No data found</div>';
		}?>
	</div>
</div>

<script> 
		$(".move_to_stock").click(function(){
			var return_id=$(this).attr("return_id");

			if(parseInt(return_id))
			{
				if(confirm('Do you want to move the stock?'))
				{
					$(".action_"+return_id).html('<img src="'+base_url+'/images/loading.gif'+'">');

					$.post(site_url+"/admin/jx_update_pending_return_stock",{return_prod_id:return_id},function(rep){
						if(rep.status=='success')
						{
							$(".action_"+return_id).html('<b>Stock updated</b>');
						}else if(rep.status=='error'){
							$(".action_"+return_id).html('<button class="move_to_stock button button-flat" return_id="'+return_id+'">Move stock</button>');
							alert("Error!!Stock not updated");
						}
						
					},'json');
				}	
			}
		});
</script>
