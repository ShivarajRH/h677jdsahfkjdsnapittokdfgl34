<?php
	$return_request_cond = $this->config->item('return_request_cond');

?>
<div class="container page_wrap">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">PNH Returns</h2>
		</div>
		<div class="fl_right" >
			<a href="<?php echo site_url('admin/pnh_return_service_log') ?>">Product Service Log</a>&nbsp;&nbsp;
			<a href="<?php echo site_url('admin/add_pnh_invoice_return') ?>">Add Return</a>
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="fl_left" >
			<b>Total : </b> <?php echo $pnh_inv_returns_ttl;?>
		</div>
		<div class="page_action_buttonss fl_right" align="right">
			<form id="filter_form" method="post">
				Status : 
				<select name="status">
					<option value="all" <?php echo (($status=='all')?'selected':'') ?> >All</option>
					<option value="0" <?php echo (($status=='0')?'selected':'') ?> >Pending</option>
					<option value="2" <?php echo (($status=='2')?'selected':'') ?> >Closed</option>
				</select>
				Search : <input type="text" name="src" >&nbsp;
				Franchise : 
				<select name="franchise" style="width:250px;">
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
				<input type="submit" value="submit">
			</form>
		</div>
	</div>
	<div style="clear:both">&nbsp;</div>	
	
	
	<div class="page_content">
		<table class="datagrid" width="100%">
			<thead>
				<tr>
					<th width="30">Slno</th><th width="130">ReturnedOn</th><th width="30">ReturnID</th><th width="200">Franchise Name</th><th width="100">Invoiceno</th><th width="150">Returned By</th><th width="150">Handled By</th><th width="100">Status</th>
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
								<td><?php echo format_datetime($ret['returned_on']) ?></td>
								<td><?php echo anchor('admin/view_pnh_invoice_return/'.$ret['return_id'],$ret['return_id'],'class="link"') ?></td>
								<td><?php echo $ret['franchise_name'] ?></td>
								<td><?php echo $ret['invoice_no'] ?></td>
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
prepare_daterange('from_date','to_date');	

$("#filter_form").submit(function(){
	
	var date_from=$("input[name='from']").val();
	var date_to=$("input[name='to']").val();
	var q=$("input[name='src']").val();
	var franchise=$("select[name='franchise']").val();
	var status=$("select[name='status']").val();
	
	if(!date_from)
		date_from='0000-00-00';
	if(!date_to)
		date_to='0000-00-00';
	if(!q)
		q=0;
	if(!franchise)
		franchise=0;
	 

	location.href = site_url+'admin/pnh_invoice_returns/'+date_from+'/'+date_to+'/'+q+'/'+franchise+'/'+status;

	return false;
});

</script>