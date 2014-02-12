<div class="container">
<?php
		$terr_id = $this->uri->segment(3);
		$role_id = $this->uri->segment(4);
?>
	<h2>Employee Expense and Incentive Tracking</h2>
	<div id="tabs" style="clear: both;">
		
		<div style="float:right;margin:5px;position: relative;z-index:1;font-size: 12px">
			<b>Territory</b> : <select name="territory" class="sel_terr">
				<option value="0">All</option>
				<?php $ter=$this->db->query("select id,territory_name from pnh_m_territory_info order by territory_name asc"); 
					foreach($ter->result_array() as $t) {
				?>
					<option value="<?=$t['id']?>" <?php echo (($terr_id==$t['id'])?'selected':'');?> ><?=$t['territory_name']?></option>
				<?php
					}
				?>
			</select>
			
			
			<b>Role</b> : <select name="role" class="sel_desi">
				<option value="0">All</option>
				<?php $role=$this->db->query("select role_id,role_name from m_employee_roles where role_id in (2,3,4,6) order by role_name asc"); 
					foreach($role->result_array() as $r) {
				?>
					<option value="<?=$r['role_id']?>" <?php echo (($role_id==$r['role_id'])?'selected':'');?> ><?=$r['role_name']?></option>
				<?php
					}
				?>
			</select>
		</div>
		<ul>
			<li><a href="#expensive">Expensive</a></li>
			<li><a href="#incentive">Incentive</a></li>
		</ul>
		
		<div id="expensive">
			
			<table class="datagrid" width="100%">
				<thead>
					<tr>
						<th>Sl.No</th>
						<th>Employee Name</th>
						<th>Expense Claimed</th>
						<th>Expense Paid</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Remarks</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
						
						<?php $i=1; foreach($exp_list as $e) {?>
							<form action="<?=site_url("admin/add_emp_expense_det")?>"  method="post" name="expense_emp_details">
								<tr>
									<input type="hidden" name="empid" value="<?=$e['employee_id']?>">
									<input type="hidden" name="name" value="<?=$e['name']?>">
									<td><?=$i++?></td>
									<td><?=$e['name']?></td>
									<td><input type="text" name="exp_claim" class="inp_width"></td>
									<td><input type="text" name="exp_paid" class="inp_width"></td>
									<td><input type="text" name="s_date" class="inp_width" id="date_from"></td>
									<td><input type="text" name="e_date" class="inp_width" id="date_to"></td>
									<td><textarea name="remarks"   style="width:200px;height:17px;"></textarea></td>
									<td><input type="submit" value="submit"></td>
								</tr>
							</form>
						<?php } ?>
					
				</tbody>
			</table>
		</div>
		<div id="incentive">
			<table class="datagrid" width="100%">
				<thead>
					<tr>
						<th>Sl.No</th>
						<th>Employee Name</th>
						<th>Incentive Amount</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Remarks</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
						
						<?php $i=1; foreach($exp_list as $e) {?>
							<form action="<?=site_url("admin/add_emp_incentive_det")?>"  method="post" name="incentive_emp_details">
								<tr>
									<input type="hidden" name="empid" value="<?=$e['employee_id']?>">
									<input type="hidden" name="name" value="<?=$e['name']?>">
									<td><?=$i++?></td>
									<td><?=$e['name']?></td>
									<td><input type="text" name="amount" class="inp_width"></input></td>
									<td><input type="text" name="st_date" class="inp_width" id="from"></input></td>
									<td><input type="text" name="et_date" class="inp_width" id="to"></input></td>
									<td><textarea name="inc_remarks"  style="width:200px;height:17px;"></textarea></td>
									<td><input type="submit" value="submit"></td>
								</tr>
							</form>
						<?php } ?>
					
				</tbody>
			</table>
		</div>
	</div>
</div>

<style>
.required_inp{border:1px solid #cd0000 !important;}
.inp_width{width:65px;}
#pop_info
{
	z-index:1;
}
</style>
<script type="text/javascript">
$(function()
{
	$("#date_from,#date_to").datepicker();
	$("#from,#to").datepicker();
	$('#tabs').tabs();
	
	$(".sel_terr").change(function(){
		location="<?=site_url("admin/emp_det")?>/"+$(this).val()+'/0';
	});
	
	$(".sel_desi").change(function(){
		location="<?=site_url("admin/emp_det")?>/0/"+$(this).val();
	});
	
	
});


$('form[name="expense_emp_details"]').submit(function(){
	
	if(!$('input[name="exp_claim"]').val())
	{
		alert('Expense Claim field Required');
		return false;
	}
	else if(!$('input[name="exp_paid"]').val())
	{
		alert('Expense Paid field Required');
		return false;
	}
	if(!$('input[name="s_date"]').val())
	{
		alert('Start Date field Required');
		return false;
	}
	if(!$('input[name="e_date"]').val())
	{
		alert('End Date field Required');
		return false;
	}
	if($('textarea[name="remarks"]').val() == '' )
	{
		alert('Remarks field Required');
		return false;
	}
	
});

$('form[name="incentive_emp_details"]').submit(function(){
	
	if(!$('input[name="amount"]').val() )
	{
		alert('Incentive Amount field Required');
		return false;
	}
	if(!$('input[name="st_date"]').val())
	{
		alert('Start Date field Required');
		return false;
	}
	if(!$('input[name="et_date"]').val())
	{
		alert('End Date field Required');
		return false;
	}
	if(!$('textarea[name="inc_remarks"]').val())
	{
		alert('Remarks field Required');
		return false;
	}
});

</script>