<div class="container">
	<div id="main_column" class="clear">
		<div class="cm-notification-container "></div>
		<div class="tools-container">
		 	 <span class="action-add"> 
			  <a href="<?php echo site_url('admin/add_employee')?>" style="float:right;">Add Employee &nbsp;</a>
			  <a href="<?php echo site_url('admin/roletree_view')?>" style="float:right;">Role Tree &nbsp;</a>
			</span>
		 <h1 class="mainbox-title">Employees List</h1>	
		
		</div>
		</br> 
		<!--  <div class="tab_emplist">
			<ul>
				<li><a href="#active_emplist">Active Employees</a></li>
				<li><a href="#inactive_emplist">Suspended Employees</a></li>
			</ul>
			<div id="active_emplist">
				<h4>Active Employees</h4>
			</div>
			<div id="inactive_emplist">
				<h4>Inactive Employees</h4>
			</div>
			
		</div>-->
		
		
		<div style="float: right">
		<b>View by Roles:</b><select id="empdet_disp_roles">
			<option value="0">All</option>
			<?php foreach($this->db->query("select role_id,role_name  from m_employee_roles order by role_id")->result_array() as $r){?>
			<option value="<?=$r['role_id'];?>"
			<?=$r['role_id']==$this->uri->segment(3)?"selected":""?>>
				<?=$r['role_name']?>
			</option>
			<?php }?>
		</select>&nbsp;&nbsp;&nbsp; 
	
			<b>Or</b> &nbsp;
			
			<b>View by Territory:</b> 
			<select	id="empdet_disp_terry">
				<option value="0">All</option>
				<?php foreach ($this->db->query("select id,territory_name from pnh_m_territory_info order by territory_name asc")->result_array() as $terr_det){?>
            	<option value="<?php echo $terr_det['id'];?>"
            	<?php echo $terr_det['id']==$this->uri->segment(4)?"selected":""?>>
            	<?php echo $terr_det['territory_name']?>
            	</option>
				<?php }?>
       	</select>&nbsp;&nbsp;
		</div>
		<br> <br>
		<div>

			<?php if($emp_list){?>
			<table class="datagrid datagridsort" width="100%" cellspacing="5">
				<thead>
					<th width="30%">Employee Name</th>
					<th width="45%">Territory / Department</th>
					<th width="20%">Mobile</th>
					<th width="15%">Emp Type</th>
				</thead>
				<tbody>
					<?php 
						$i=$pg+1;
				foreach($emp_list as $emp_det){
	          ?>
					<tr>
						<td>
							<?php echo '<b>'.ucwords($emp_det['name']).'</b><br>';?>
							<?php if($emp_det['job_title']<=5 && $emp_det['job_title2']==0){?>
							<?php echo ucwords($emp_det['role_name'].'<br>');?>
								<?php } else{?>
							<?php echo ucwords($emp_det['fc']).'<br>';?>
							<?php }?>
							<?php $assigned_emp_det= $this->erpm->get_working_under_det($emp_det['employee_id']); 
							if($assigned_emp_det)
							echo 'Reports to:'.$assigned_emp_det['name'].'<br>';
							else 
							echo $assigned_emp_det='';
						?>
						</td>
						<td><?php 
						if($emp_det['emp_type'] == "1")
						{
							//echo '<b>Department : </b>';
							$dept_det = $this->employee->get_dept_det_byemp($emp_det['employee_id']);
							
							if($dept_det)
								echo $dept_det['dept_name'];
							else echo '--';
						}
						elseif($emp_det['emp_type'] == 2)
						{
								if($emp_det['job_title']<= 2){
									$terr_list=$this->db->query("select * from pnh_m_territory_info")->result_array();
									$town_list=$this->db->query("select * from pnh_towns")->result_array();
								}
								else
								{
									$terr_list = $this->erpm->get_assigned_territory_det($emp_det['employee_id']);
									$town_list = $this->erpm->get_assigned_town_det($emp_det['employee_id']);;
								}
						
								foreach ($terr_list as $assigned_terr)
									echo $assigned_terr['territory_name'].', ';

								?>
									<?php echo '<br>';?>
									<?php 
								if($emp_det['job_title']==5){
								foreach ($town_list as $assigned_twn) 
									echo $assigned_twn['town_name'].', ';
								}
								else if($emp_det['job_title']==4) 
									echo "All Towns Of ".$assigned_terr['territory_name'].', ';
								else 
									echo "All Towns";
						}
						?>
						</td>
					
						<td><?php echo $emp_det['contact_no']?>
							<?php echo '<br>';?>
							<a href="<?php echo site_url('admin/edit_employee'.'/'.$emp_det['employee_id'])?>" class="button button-tiny button-flat-action">Edit</a> 
							<a href="<?php echo site_url('admin/view_employee'.'/'.$emp_det['employee_id'])?>" class="button button-tiny button-flat-primary">View</a>
						</td>
						<td><?php 
								$emp_type_arr = array(1=>"Office Emp",2=>"Field Emp");
								echo $emp_type_arr[$emp_det['emp_type']];
							?>
						</td>
					</tr>
					<?php 
				$i++;
               }
               ?>

				</tbody>
			</table>
			<div id="pagination">
				<?php echo $pagination?>
			</div>
			<?php 		
			}else{
				echo "No Data Found";		
			}
		?>
		</div>
	</div>
</div>		
<script>
	$(function(){
		$("#empdet_disp_roles").change(function(){
			$("#empdet_disp_terry").val(0)
				location='<?=site_url("admin/list_employee")?>/'+$(this).val()*1+'/'+$("#empdet_disp_terry").val()*1;
		});

		$("#empdet_disp_terry").change(function(){
			
				location='<?=site_url("admin/list_employee")?>/'+$("#empdet_disp_roles").val()*1+'/'+$(this).val()*1;
		});
	});
	$('.datagridsort').tablesorter( {sortList: [[0,0]]} ); 

	
	$(".tab_emplist").tabs();
</script>


<style>

#pagination a {
	background: none repeat scroll 0 0 rgb(255, 255, 255);
	color: rgb(0, 0, 0);
	font-size: 13px;
	padding: 3px 6px;
}

.ui-tabs .ui-tabs-panel{padding:0px;}
.ui-widget-header{background: none;border:none;}
.ui-widget{border:none;}

</style>

