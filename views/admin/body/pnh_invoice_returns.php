<?php
	$return_request_cond = $this->config->item('return_request_cond');
	$order_by = $this->config->item('order_by');
?>
<div class="container page_wrap">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Manage Returns</h2>
			
		</div>
		<div class="fl_right" >
			<a href="<?php echo site_url('admin/pnh_return_service_log') ?>">Product Service Log</a>&nbsp;&nbsp;
			<a href="<?php echo site_url('admin/pnh_update_returns_to_stock/'.$type) ?>">Update returns to stock</a>&nbsp;&nbsp;
			<?php if($type!='sk'){?>
			<a href="<?php echo site_url('admin/returns_reshipment_list/'.$type) ?>">Reshipments list</a>&nbsp;&nbsp;
			<?php }?>
			<a href="<?php echo site_url('admin/add_pnh_invoice_return/'.$type) ?>">Add Return</a>
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="fl_left" >
			<b>Total : </b> <?php echo $pnh_inv_returns_ttl;?>
		</div>
		<div class="page_action_buttonss fl_right" align="right">
			<form id="filter_form" method="post">
				Search : <input type="text" name="src" >&nbsp;
				Franchise : 
				<select name="franchise" style="width:100px;">
					<option value=''>choose</option>
					<?php 
						$franchise_list=$this->db->query("select franchise_id,franchise_name from pnh_m_franchise_info order by franchise_name asc")->result_array();
						foreach($franchise_list as $f)
						{
							echo '<option value="'.$f['franchise_id'].'">'.$f['franchise_name'].'</option>';	
						}
					?>
				</select>&nbsp;	
				Date range : <input type="text" class="inp fil_style" size=10 id="from_date" name="from"> 
				to <input type="text" class="inp fil_style" size=10 id="to_date" name="to" > &nbsp;
				Status:
				<select name="status_filter">
					<option value=''>All</option>
					<?php 
						if($return_request_cond)
						{
							foreach($return_request_cond as $i=> $st)
							{
								?>
								<option value="<?php echo $i+1; ?>"><?php echo $st; ?></option>
								<?php
							}
						} 
					?>
				</select>
				<?php 
					$returns_from=$this->erpm->get_assigned_mng_rtn_access();
				?>
				Returns from:
				<select name="returns_from">
					<option value="all">All</option>
					<?php foreach($returns_from as $frm){
						?>
						<option value="<?php echo $frm;?>"><?php echo $order_by[$frm]?></option>
						<?php 
					}?>
				</select>
				<input type="submit" value="submit">
			</form>
		</div>
	</div>
	<div style="clear:both">&nbsp;</div>	
	
	
	<div class="page_content">
		<table class="datagrid" width="100%">
			<thead>
				<tr>
					<th width="30">Slno</th><th width="80">By</th><th width="130">ReturnedOn</th><th width="30">ReturnID</th><th width="200">From</th><th width="100">Invoiceno</th><th width="150">Returned By</th><th width="150">Handled By</th><th width="100">Status</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					if($pnh_inv_returns)
					{
						$i=0;
						foreach ($pnh_inv_returns as $ret)
						{
				?>
							<tr>
								<td><b><?php echo $i+$pg+1; ?></b></td>
								<td>
									<?php 
										if($ret['order_from']==0)
											echo 'Storeking';
										else if($ret['order_from']==1)
											echo 'Snapittoday';
										else if($ret['order_from']==2)
											echo $ret['partnername'];
										
										$order_from=$this->erpm->get_order_from_by_id($ret['order_from']);
									?>
								</td>
								<td><?php echo format_datetime($ret['returned_on']) ?></td>
								<td><?php echo anchor('admin/view_pnh_invoice_return/'.$ret['return_id'].'/'.$order_from,$ret['return_id'],'class="link"') ?></td>
								<td>
									<?php echo anchor($ret['order_from']==0?'admin/pnh_franchise/'.$ret['franchise_id']:'admin/user/'.$ret['userid'],$ret['bill_person'],"target='_blank'"); ?>
								</td>
								<td><?php echo anchor("admin/invoice/".$ret['invoice_no'],$ret['invoice_no'],"target='_blank'"); ?></td>
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
	.pagination a{background: #B6AD9E;
color: #FFF;
display: inline-block;
min-width: 10px;
text-align: center;
border: 1px dotted #B6AD9E;padding:5px;font-weight: bold}
.pagination a:hover{background: #f1f1f1;color:#444}

</style>

<script>
var type ="<?php echo $type;?>";				
prepare_daterange('from_date','to_date');	

$("#filter_form").submit(function(){
	
	var date_from=$("input[name='from']").val();
	var date_to=$("input[name='to']").val();
	var q=$("input[name='src']").val();
	var franchise=$("select[name='franchise']").val();
	var stat=$("select[name='status_filter']").val();
	var return_from=$("select[name='returns_from']").val();
	
	if(!date_from)
		date_from='0000-00-00';
	if(!date_to)
		date_to='0000-00-00';
	if(!q)
		q=0;
	if(!franchise)
		franchise=0;
	if(!stat)
		stat=0;

	location.href = site_url+'admin/pnh_invoice_returns/'+type+'/'+date_from+'/'+date_to+'/'+q+'/'+franchise+'/'+stat+'/'+return_from;

	return false;
});

</script>