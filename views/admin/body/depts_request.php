<?php
/**
 * View departments and assined request list and updation page
 * @author Shivaraj <Shivaraj@storeking.in>_Jul_12_2014
 * @last_modify Shivaraj <Shivaraj@storeking.in>_Nov_08_2014
 */
//print_r($req_list);
//print_r($dept_list);
?>
<div class="container">
<h2>Departments Requests</h2>
	<table class="datagrid" width="100%">
		<thead>
			<tr>
				<th width="2%">Slno</th>
				<th width="5%">Dept ID</th>
				<th width="20%">Department</th>
				<th width="50%">Request List</th>
				<th width="30%" align="center">Actions</th>
			</tr>
		</thead>
		<tbody>
<?php
		foreach($dept_list as $i=>$dept)
		{
?>
		
			<form method="post" onsubmit="javascript:return confirm('Are you sure want to update?')">
			<tr>
				
				<td>
					<?=(++$i);?>
				</td>
				<td>
					<?=$dept['dept_id'];?>
				</td>
				<td>
					<input type="hidden" name="sel_dept_id" size="3" value="<?=$dept['dept_id'];?>"/>
					<input type="hidden" name="sel_dept_name" size="3" value="<?=$dept['name'];?>"/>
						<b><?=$dept['name'];?></b>
						<div align="right" style="padding:5px; margin-right: 30px">
							<a href="javascript:void(0)" onclick="return view_assigned_emp(this);">View Employees</a>
						</div>
				</td>
				<td>
<?php
					foreach($req_list as $req)
					{
						$chk_sts = " ";
						//echo "<br> {$req['id']}, {$dept['type_ids']}"."";
						
						if(match_in_list($req['id'], "{$dept['type_ids']}"))
								$chk_sts = " checked ";
						
?>
					
					<span class="fl_left"><input type="checkbox" name="sel_type_ids[]" value="<?=$req['id'];?>" size="50" class="fl_left inpadding" <?=$chk_sts;?> /><?=$req['name'];?></span>
<?php
					}
?>
				</td>
				<td align="left">
					<input type="submit" value="Update" class="button button-tiny button-caution button-rounded">
				</form>
				</td>
			</tr>
	
<?php
		}
?>

		</tbody>
	</table>
</div>
<div style="display:none;">
	<div id="dlg_dept_employee_list">
		<h3>Employee List</h3>
		<table class="datagrid" width="100%">
			<thead>
				<tr>
					<th>#</th>
					<th>Name</th>
					<th>Email</th>
					<th>Contact No</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				
			</tbody>
		</table>
	</div>
</div>
<?php /*<input type="text" name="ifsc_code" value="<?php echo set_value('ifsc_code');?>" size="20"><?php echo form_error('ifsc_code','<div class="error">','</div>')?>*/ ?>

<style>
	.inpadding { padding: 5px;}
</style>
<script>
	//=========== Dept Employee ===========
	$("#dlg_dept_employee_list").dialog({
		modal:true,
		autoOpen:false,
		width:700,
		height:600,
		autoResize:true
		,open: function() {
			var dlg=$(this);
			var dept_id=dlg.data("dept_id");
			$.post(site_url+"/admin/dept_employee_list/"+dept_id,{},function(resp) {
				if(resp.status=='success')
				{
					var rdata='';
					$.each(resp.emp_det,function(i,emp) {
						var email=emp.email==null?'-Not Given-':emp.email;
						var contact_no=emp.contact_no==null?'-Not Given-':emp.contact_no;
						rdata+="<tr>\n\
									<td>"+(++i)+"</td>\n\
									<td>"+emp.name+"</td>\n\
									<td>"+email+"</td>\n\
									<td>"+contact_no+"</td>\n\
									<td><a href='"+site_url+"/admin/edit_employee/"+emp.employee_id+"' class='button button-tiny button-rounded button-primary' target='_blank'>Edit</a></td>\n\
								</tr>";
					});
					
					$("tbody",dlg).html(rdata);
				}
				else
				{
					alert("Error: "+resp.message);
					$(dlg).dialog("close");
				}
			},'json');
		}
		,buttons:{
			Close:function() {
				$(this).dialog("close");
			}
		}
	});
	function view_assigned_emp(elt)
	{
		var trElt=$(elt).closest("tr");
		var dept_id=$("input[name='sel_dept_id']",trElt).val();
		var dept_name=$("input[name='sel_dept_name']",trElt).val();
		$("#dlg_dept_employee_list").data({dept_id:dept_id}).dialog("option","title",$.ucfirst(dept_name)+" - department employees").dialog("open");
		
	}
	//=========== Dept Employee ===========
</script>