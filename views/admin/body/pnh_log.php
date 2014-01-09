<div class="container">
	<div class="tabs">
		<ul>
			<li><a href="#unorderd_fran_log">Unorderd Franchise Log</a></li>
			<li><a href="#executive_account_log">Executive Account Log</a></li>
			<li><a href="#offer_log">Offer Log</a></li>
			<li><a href="#call_log">Call Log</a></li>
		</ul>
		
		<div id="unorderd_fran_log"  style="display: block;">
			<div >
			<?php
			if($unorderd_fran){?>
			<h2 class="page_title">Unordered Franchise Log</h2>
			<table class="datagrid datagridsort" width="100%">
			<thead>
			<th>Sl no</th><th>Franchise Id</th><th>Name</th><th>Last Ordered</th><th>Enquiry</th>
			</thead>
			<tbody>
			<?php 
			$i=1;
			foreach($unorderd_fran  as $unorderd_fran_det){
			$fr_unorder_log=$this->db->query("SELECT a.*,b.name,DATE_FORMAT(a.created_on,'%d/%m/%Y %h:%i %p') as created_by,DATE_FORMAT(a.last_orderd,'%d/%m/%Y %h:%i %p') as last_orderd FROM pnh_franchise_unorderd_log a
																			JOIN king_admin b ON b.id=a.created_by
																			WHERE franchise_id=? order by id desc ",$unorderd_fran_det['franchise_id']);
			?>
			<tr><td><?php echo $i;?></td><td><input type="hidden" name="franchise_id" value="<?php echo $unorderd_fran_det['franchise_id']?>"><?php echo $unorderd_fran_det['pnh_franchise_id'];?></td><td><?php echo $unorderd_fran_det['franchise_name'] .'</br>'.$unorderd_fran_det['login_mobile1'].','.$unorderd_fran_det['login_mobile2'];?></td><td><input type="hidden" name="last_orderd" value="<?php echo $unorderd_fran_det['last_orderd'];?>"><?php echo $unorderd_fran_det['last_orderd'];?></td>
			<td width="400">
			<div style="margin-bottom: 10px;">
			<a href="javascript:void(0)" style="font-size:85%;" onclick='$("form",$(this).parent()).toggle()'>add msg</a>
				<form method="post" style="display:none;"  action="<?php echo site_url("admin/pnh_update_unorderd_log/{$unorderd_fran_det['franchise_id']}")?>">
					<input type="hidden" name="last_orderon" value="<?php echo $unorderd_fran_det['actiontime']?>" >
					<textarea style="width: 98% " name="msg"></textarea>
					<br />
					<input type="checkbox" name="admin_notify" value="1">admin notify <input style="float: right" type="submit" value="add">
				</form>
			</div>
			<div>
					<?php if($fr_unorder_log->num_rows()){?>
							<ul class="item_list">
					<?php 		
					foreach ($fr_unorder_log->result_array() as $log){?>
								<li>
									<p><?php echo $log['msg']?></p>
									<div align="right" style="font-size: 11px;padding:5px;">
										<span style="float: left">Is Admin Notified : <?php echo ($log['is_notify']==1)?'Yes':'No' ?></span>
										<b><?php echo "By  ".ucwords($log['name']);?></b> - <?php echo $log['created_by']?> 
									</div>
								</li>
					<?php }	?>				
							</ul>
					<?php }	?>	
			</div>
			</td>
			</tr>
			<?php 
				$i++; 
			}
			?>
			</tbody>	
			</table>
			<?php } ?>
			</div>
		</div>



		<div id="executive_account_log">
			<div align="right">
				<span style="float: left;font-weight: bold;font-size: 13px;">
					Total Listed : <?php echo $pnh_exec_account_details?count($pnh_exec_account_details):0; ?>
				</span>
				Filter by date <input type="text" id="inp_date" size="10" name="inp_date" value="<?php echo $fil_date?>" >
				<input type="button" value="submit" onclick="show_executivelog()">
			</div>
		<?php  if($pnh_exec_account_details){?>
		<table class="datagrid datagridsort" width="100%">
		<thead>
		<th>Sl no</th><th>From</th><th>Employee Name</th><th>Message</th><th>Reciept Status</th><th>Updated by</th><th>Updated on</th><th>Logged On</th>
		</thead>
		<?php 
		$i=1;
		foreach($pnh_exec_account_details as $account_det){
		?>
		<tbody>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $account_det['contact_no'];?></td>
			<td><?php echo $account_det['employee_name'];?></td>
			<td width="400"><?php echo $account_det['msg'];?></td>
			<td>
				<?php 
					if($account_det['reciept_status'])
					{
						echo '<p>'.$account_det['remarks'].'</p>';
					} 
				?>
			</td>
			<td><?php echo $account_det['updatedby_name'];?></td>
			<td><?php echo $account_det['updated_on'];?></td>
			<td><?php echo $account_det['logged_on'];?></td>
		</tr>
		</tbody>
		<?php $i++;}?>
		</table>
		
		<?php }else
		{
			echo '<h3 align="left">No data found for - '.format_date($fil_date).'</h3>';
		}?>
		
		</div>


		<div id="call_log">
		<table class="datagrid datagridsort">
		<thead>
		<th>Sl no</th><th>Sender Contact no</th><th>Franchise Name</th><th>Msg</th><th>Created On</th>
		</thead>
		<tbody>
		<?php 
		$i=1;
		if($log_details){
		foreach($log_details as $log_det){
		?>
		<tr>
		<td><?php echo $i;?></td>
		<td><?php echo $log_det['sender']; ?></td>
		<td><?php echo $log_det['franchise_name'];?></td>
		<td><?php echo $log_det['msg'];?></td>
		<td><?php echo $log_det['created_on'];?></td>
		</tr>
		<?php
		$i++; 
		}
		}
		?>
		</tbody>
		</table>
		</div>

</div>
</div>

<style>
.item_list{padding:5px;background: #FFF;}
.item_list li{border-bottom:1px solid #cdcdcd;list-style: none;background: #FFF;}
.item_list li p{margin:5px 6px;width: 300px;text-align: justify;}
</style>

<script>
$('#inp_date').datepicker();

function show_executivelog()
{
	location.href = site_url+'/admin/log_details/'+$('#inp_date').val()+'#executive_account_log';
}

$('.datagridsort').tablesorter( {sortList: [[0,0]]} ); 
</script>

