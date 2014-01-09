<div class="container page_wrap">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Book Templates</h2>
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="page_action_buttons fl_left" >
		<br>
			<b>Total : </b><span><?php echo $template_details['total_template'] ?></span>
		</div>
		<div class="page_action_buttons fl_right">
			<a href="<?php echo site_url('admin/pnh_create_book_template') ?>" target="_blank" class="button button-rounded button-flat-secondary button-small" >Create book Template</a>&nbsp;
		</div>
	</div>
	<div style="clear:both">&nbsp;</div>
	<div class="page_content">
		<table cellpadding="5" cellspacing="0" width="100%" class="datagrid">
			<thead>
				<tr>
					<th width="3%">#</th>
					<th width="20%">Template Name</th>
					<th width="10%">Menu</th>
					<th width="10%">Value(Rs)</th>
					<th width="15%">Denomination</th>
					<th width="10%">Created by</th>
					<th width="10%">Created on</th>
				</tr>
			</thead>
			<tbody>
				<?php if($template_details['template_list'])
				{
					foreach($template_details['template_list'] as $i=>$template)
					{
						?>
						<tr>
							<td><?php echo $i+1; ?></td>
							<td><?php echo $template['book_type_name']; ?></td>
							<td>	
							<?php 
								$menu_list=$this->db->query("select id,name from pnh_menu where id in (".$template['menu_ids'].")")->result_array();
								foreach($menu_list as $m)
								{
								 echo $m['name']."<br>";
								}
								?>
							</td>
							<td><?php echo $template['value']; ?></td>
							<td>
								<?php 
									$denomination_det=$this->db->query("select a.*,b.* from pnh_m_book_template_voucher_link a join pnh_m_voucher b on b.voucher_id=a.voucher_id where book_template_id=? order by b.denomination asc",$template['book_template_id'])->result_array();
									if($denomination_det)
									{?>
										<table cellpadding="0" style="border-collapse: collapse;" >
											<thead>
												<tr>
													<th>#</th>
													<th>Coupon value(Rs)</th>
													<th>Qty</th>
												</tr>
											</thead>
											<tbody>
									<?php 
										foreach($denomination_det as $i=>$denomination)
										{?>
											<tr>
												<td><?php echo $i+1; ?></td>
												<td><?php echo $denomination['denomination']; ?></td>
												<td><?php echo $denomination['no_of_voucher']; ?></td>
											</tr>
								  <?php }
									?>
											</tbody>
										</table>
								<?php 
									}
								?>
							</td>
							<td><?php echo $template['username']; ?></td>
							<td><?php echo format_datetime($template['created_on']); ?></td>
						</tr>
						
				<?php 	
					}?>
						<tr>
							<td align="right" colspan="9" class="pagination"><?php echo $template_details['pagination']; ?></td>
						</tr>
				<?php 	
				}else{
					echo '<tr><td align="center" colspan="9">No coupons are found</td></tr>';
				}
					?>
			</tbody>
		</table>
	</div>
</div>

