<?php 
$assigned_emp_det= $this->erpm->get_working_under_det($emp_details['employee_id']);
$assigned_terr_det=$this->erpm->get_assigned_territory_det($emp_details['employee_id']);
$assigned_twn_det=$this->erpm->get_assigned_town_det($emp_details['employee_id']);
$assignment_details=$this->erpm->to_get_assignmnt_details($emp_details['employee_id']);

$cfg_task_types_arr = array();
$task_type_list=$this->db->query("SELECT * FROM `pnh_m_task_types` ")->result_array();
foreach($task_type_list as $tsk_type_det)
{
	$cfg_task_types_arr[$tsk_type_det['id']] =$tsk_type_det['task_type'];
}


if($emp_details['job_title'] <= 2){
	$terr_list=$this->db->query("select * from pnh_m_territory_info")->result_array();
	$town_list=$this->db->query("select * from pnh_towns")->result_array();
}else
{
	$terr_list = $assigned_terr_det;
	$town_list = $assigned_twn_det;
}
?>

<div class="container">
<div id="suspend_emprmks" title="Suspend Employee" style="display: none;">
	<form action="<?=site_url("admin/pnh_suspend_emp/{$emp_details['employee_id']}")?>" method="post" data-validate="true" id="suspend_rmrksfrm">
		<table>
			<tr><td>Remarks</td><td>:</td><td><textarea name="suspend_empremarks" data-required="true"></textarea></td></tr>
			<tr><td><input type="hidden" name="s_empid" value=""></td></tr>
		</table>
	</form>
</div>

<div id="unsuspend_emprmks" title="Unsuspend Employee" style="display: none;">
	<form action="<?=site_url("admin/pnh_unsuspend_emp/{$emp_details['employee_id']}")?>" method="post" data-validate="true" id="unsuspend_rmrksfrm">
		<table>
			<tr><td>Remarks</td><td>:</td><td><textarea name="unsuspend_empremarks" data-required="true"></textarea></td></tr>
			`<tr><td><input type="hidden" name="un_sempid" value=""></td></tr>
		</table>
	</form>
</div>



	<div id="main_column" class="clear">
		<?php if($emp_details['is_suspended']==0){?> 
			<a href="javascript:void(0)" class="suspend_link" onclick="suspend_emp(<?php echo $emp_details['employee_id']?>)" style="float:right;">Suspend Account</a>
		<?php }else{?>
					<a href="javascript:void(0)" class="suspend_link" onclick="unsuspend_emp(<?php echo $emp_details['employee_id']?>)" style="float:right;">Unsuspend Account</a>
		<?php }?>	 
		<div>
			<table width="100%" cellpadding="0">
				<tr>
					<td valign="top">
						<div class="emp_bio">
						<fieldset>
						<legend><b>Personal Details</b></legend>
						<table >	
						<tr>
							<td >
								<h4 style="margin:0px;font-size: 24px;"><b><?php echo ucwords($emp_details['name'])?></b>
									<a style="margin-left: 10px;font-size: 12px;"	href="<?php echo site_url('admin/edit_employee'.'/'.$emp_details['employee_id'])?>">edit</a>
								</h4>
							</td>
							<td width="50%">	
								<div class="avatar" style="float: right;" >
								<?php 
									if(trim($emp_details['photo_url'])){
								?> 
								<img alt="" height="100" src="<?php echo base_url().'resources/employee_assets/image/'.$emp_details['photo_url']?>" >
								<?php }else{ 
									echo '<div style="height: 50px;width: 50px;background: #F1F1F1;text-align: center;padding: 20px;border: 2px solid #FFF;">No image updated</div>';
								}?>
								</div>
							</td>
						</tr>
						<tr>
							<td><b>Address</b>:
								
								<?php echo $emp_details['address']?>,
								<?php echo $emp_details['city']?>-<?php echo $emp_details['postcode']?>
							</td>
						</tr>
						<tr>	
							<td>
								<b>Bio</b>:
								<?php echo $emp_details['gender']?> 
								<?php echo $emp_details['qualification']?>
							</td>
						</tr>
						<tr>	
							<?php if(strlen(trim($emp_details['cv_url']))<=5){?>
								<td><b>CV</b>: <a href="<?php echo base_url().'resources/employee_assets/cv/'.$emp_details['cv_url']?>">View/Download</a></td> 
							<?php }?>
							</td>
						</tr>
								<?php if($emp_details['job_title']>1){?>
							</table>
							</fieldset>
						</div>
						<br>
						
						<b>Task History</b>
						<?php if($show_send_log)
						{ 
						?>
							<div  width="100%">
								<!-- filter block -->
								<div style="overflow: hidden;">
									<div style="float:right;overflow: hidden;padding:2px 5px;font-size: 12px;width:700px;">
										<div style="width:350px;float:right;background: #ffffd0;">
											<form method="post" action="<?php echo site_url('admin/view_employee/'.$emp_details['employee_id']);?>" id="by_date_range_frm"> 
												Date range : <input type="text" class="inp fil_style" size=10 id="from_date" name="from"> 
												to <input type="text" class="inp fil_style" size=10 id="to_date" name="to" > 
												<input type="submit" class="fil_style" style="padding:3px 6px;" value="submit">
											</form>
										</div>
										<div class="clear"></div>
									</div>
								</div>
								<!-- filter block end-->
								<table width="100%" cellpadding="5" cellspacing="0" class="datagrid">
									<thead>
										<tr>
											<th width="3%">#</th>
											<th width="8%">Processed on</th>
											<th>Invoices</th>
											<th width="5%">Total Invoices</th>
											<th width="10%">Territory</th>
											<th width="10%">Town</th>
											<th width="10%">Amount</th>
											<th width="10%">Start Km</th>
											<th>Remark</th>
										</tr>
									</thead>
									<?php if($send_invoice_det)
									{
										foreach($send_invoice_det as $i=> $sent_invoice)
										{?>
											<tr>
												<td><?php echo $i+1;?></td>
												<td><?php echo format_date($sent_invoice['sent_on']); ?></td>
												<td><?php echo str_ireplace(',',' ',$sent_invoice['invoices']);?></td>
												<td><?php echo count(explode(',',$sent_invoice['invoices']));?></td>
												<td><?php echo $sent_invoice['territory_name']; ?></td>
												<td><?php echo $sent_invoice['town_name']; ?></td>
												<td><?php echo $sent_invoice['start_meter_rate']; ?></td>
												<td><?php echo $sent_invoice['amount']; ?></td>
												<td><?php echo $sent_invoice['remark'];?></td>
											</tr>
											
									<?php
										}
	
										if($manifesto_sent_log_pagi){
									?>
											<tr>
												<td colspan="7" class="pagination"><?php echo $manifesto_sent_log_pagi;?></td>
											</tr>
								<?php 
										}
									}
								?>
								</table>
							</div>
							
					<?php }if($emp_details['job_title2']<=5){?>
						<br>
						<div class="tab_view" width="100%">
						<ul>
							<li><a href="#fu_tasks" id="tbl_upcomming_tasks" onclick="load_smslog_data(this,'fu_tasks',0,0)">Upcoming Tasks</a></li>
							<li><a href="#completed_tasks" onclick="load_smslog_data(this,'completed_tasks',0,0)">Completed Tasks</a></li>
							<li><a href="#closed_tasks" onclick="load_smslog_data(this,'closed_tasks',0,0)">Closed Tasks</a><li>
						</ul>
						
							<div id="fu_tasks">
							<div class="tab_content"></div>
							</div>
							
							<div id="completed_tasks">
							<div class="tab_content"></div>	
							</div>
							
							<div id="closed_tasks">
							<div class="tab_content"></div>	
							
							</div>
						</div>
						<br>
						<b>SMS Log</b>
						<div class="tab_view" width="100%">
  							<ul>
									<li><a href="#sys2_emp">System to Employee</a></li>
									<li><a href="#emp2_sys">Employee to System </a></li>
							</ul>
								<div id="sys2_emp">
								
								<div class="tab_view tab_view_inner">
								  <ul>
								    	<li><a href="#payment_collection" id="tbl_paid_collection"  onclick="load_smslog_data(this,'payment_collection',0,0)">Start Day SMS</a></li>
								    	<li><a href="#offer_dytoemp"  onclick="load_smslog_data(this,'offer_dytoemp',0,0)">Offer Of The Day SMS</a></li>
								    	<li><a href="#daysales_summary"  onclick="load_smslog_data(this,'daysales_summary',0,0)">End Day SMS</a></li>
								     	<li><a href="#task_remainder"  onclick="load_smslog_data(this,'task_remainder',0,0)">Task Reminder</a></li>
								  		<li><a href="#emp_bouncesms"  onclick="load_smslog_data(this,'emp_bouncesms',0,0)">Bounce</a></li>
								  		<li><a href="#shipmnet_ntfy" onclick="load_smslog_data(this,'shipmnet_ntfy',0)">Shipments notifications</a></li>
								  </ul>
								  <div id="payment_collection">
									<h4>Payment Collection</h4>
										<div class="tab_content"></div>
								</div>
								<div id="shipmnet_ntfy">
									<h4>Pnh shipments notifications</h4>
									<div class="tab_content"></div>
								</div>
								<div id="offer_dytoemp">
									<h4>Offer Of The Day SMS</h4>
										<div class="tab_content"></div>
								</div>
								
								  <div id="daysales_summary">
									<h4>Day Sales</h4>
									<div class="tab_content"></div>
								</div>
								
								 <div id="task_remainder">
									<h4>Task Remainder</h4>
								<div class="tab_content"></div>
								</div>
								
								<div id="emp_bouncesms">
									<h4>Employee Bounce SMS</h4>
								<div class="tab_content"></div>
								</div>
								
								
							  </div>
							</div>				
								<div id="emp2_sys" style="padding:0px !important;">
								
								<div class="tab_view tab_view_inner">
								  
								  <ul>
								    	<li><a href="#paid" onclick="load_smslog_data(this,'paid',0,0)">Paid</a></li>
								     	<li><a href="#new" onclick="load_smslog_data(this,'new',0,0)">New</a></li>
								     	<li><a href="#existing" onclick="load_smslog_data(this,'existing',0,0)">Existing</a></li>
								    	<li><a href="#task" onclick="load_smslog_data(this,'task',0,0)">Task</a></li>
								    	<li><a href="#ship" onclick="load_smslog_data(this,'ship',0,0)" >Ship</a></li>
								    	<li><a href="#delivered_invoicesms" onclick="load_smslog_data(this,'ship_delivered',0,0)">Delivered</a></li>
										<li><a href="#returned_invoicesms" onclick="load_smslog_data(this,'returned_invoicesms',0,0)">Returned</a></li>
										<li><a href="#inv_pickup" onclick="load_smslog_data(this,'inv_pickup',0)" >Pickup</a></li>
										<li><a href="#inv_handover" onclick="load_smslog_data(this,'inv_handover',0)" >Handover</a></li>
												
								  </ul>
								<div id="paid">
								<h4>Paid Log</h4>
								<div class="tab_content"></div>
								</div>
								
								<div id="new">
								<h4>New Franchisee Identified Log</h4>
									<div class="tab_content"></div>
								</div>
								
								<div id="existing">
								<h4>Existing Franchisee Issue  Log</h4>
									<div class="tab_content"></div>
								</div>
								
								<div id="task">
								<h4>Task Log</h4>
								<div class="tab_content"></div>
								</div>
								
								<div id="ship">
								<h4>Invoice Ship Log</h4>
								<div class="tab_content"></div>
								</div>
								
								<div id="delivered_invoicesms">
								<h4>Delivered Log</h4>
								<div class="tab_content"></div>
								</div>

								<div id="returned_invoicesms">
								<h4>Returned Log</h4>
								<div class="tab_content"></div>
								</div>
								<div id="inv_pickup">
								<h4>Manifesto Pickup Log</h4>
								<div class="tab_content"></div>
								</div>
											
								<div id="inv_handover">
								<h4>Invoices handover log</h4>
								<div class="tab_content"></div>
								</div>
							</div>
						</div>
						
						<?php }if($emp_details['job_title2']>5){?>
							<div class="tab_view">
								<ul>
									<li><a href="#sys2_emp">Employee to System</a></li>
								</ul>
								<div id="emp2_sys" style="padding: 0px !important;">

									<div id="emp2_sys" style="padding: 0px !important;">

										<div class="tab_view tab_view_inner">

											<ul>
												<li><a href="#delivered_invoicesms"
													onclick="load_smslog_data(this,'ship_delivered',0,0)">Delivered</a>
												</li>
												<li><a href="#returned_invoicesms"
													onclick="load_smslog_data(this,'returned_invoicesms',0,0)">Returned</a>
												</li>
												<li>
													<a href="#lr_number_updates"
													onclick="load_smslog_data(this,'lr_number_updates',0,0)">LR number updates</a>
												</li>
												<li><a href="#inv_pickup" onclick="load_smslog_data(this,'inv_pickup',0)" >Pickup</a></li>
												<li><a href="#inv_handover" onclick="load_smslog_data(this,'inv_handover',0)" >Handover</a></li>
												
											</ul>

											<div id="delivered_invoicesms">
												<h4>Delivered Log</h4>
												<div class="tab_content"></div>
											</div>

											<div id="returned_invoicesms">
												<h4>Returned Log</h4>
												<div class="tab_content"></div>
											</div>
											<div id="lr_number_updates">
												<h4>LR number updates</h4>
												<div class="tab_content"></div>
											</div>
											<div id="inv_pickup">
											<h4>Manifesto Pickup Log</h4>
											<div class="tab_content"></div>
											</div>
											
											<div id="inv_handover">
											<h4>Invoices handover log</h4>
											<div class="tab_content"></div>
											</div>
											
										</div>

									</div>
								</div>

							</div>
							<?php }?>
					</td>
					
					<td>
					
					<div>
					<fieldset>
					<legend><b>Contact Details</b></legend>
						<table cellspacing="5">
							<tr>
								<td><b>Email:</b></td>
								 <td><?php echo $emp_details['email'] ?></td>
							</tr>
							<tr>
								<td><b>Mobile:</b></td><td><?php echo str_replace(',',', ',$emp_details['contact_no']) ?></td>
							</tr>
							<tr>
								<td><b>Do not send sms:</b></td><td><input type="checkbox" name="do_not_send_sms" value="0" id="do_not_send_sms" emp_id="<?php echo $emp_details['employee_id']; ?>" <?php echo $emp_details['send_sms']?'':'checked'; ?>></td>
							</tr>
						</table>
					</fieldset>
					</div>
					
					<div>
					<fieldset>
					<legend><b>Assignment Details</b></legend>
					<table width="100%" cellspacing="5">
					<td width="100">
						
						<p>
						<b>Designation</b><br/>
						<?php echo $this->db->query("select role_name from m_employee_roles a join m_employee_info b on a.role_id = b.job_title2 where b.employee_id = ? ",$emp_details['employee_id'])->row()->role_name;?>
						</p>
						
						<p>
								<b>Working/Assigned Under</b><br />
								<?php echo ($assigned_emp_det)?$assigned_emp_det['role_name'].'----'.$assigned_emp_det['name']:'' ;?>
								
						</p>
						<div class="module" style="width: 400px;">
							<b>Assigned Territory</b>
							<ul class="list-inline">
								<?php 
								foreach ($terr_list as $assigned_terr)
									echo '<li>'.$assigned_terr['territory_name'].','.'</li>';
								?>
							</ul>	
						</div>
						<?php  if($emp_details['job_title2']>=5){?>
						<br />
						<div class="module" style="width: 400px;">
							<b>Assigned Towns</b>
							<ul class="list-inline">
								<?php 
								foreach ($town_list as $assigned_twn) 
									echo '<li>'.$assigned_twn['town_name'].','.'</li>';
								?>
							</ul>	
						</div>
						<?php }else{?>
						<br />
						<div class="module" style="width: 400px;">
							<b>Assigned Towns</b>
							<ul class="list-inline">
								<?php 
								echo 'All Towns of ';
								foreach ($terr_list as $assigned_terr)
									echo '<li>'.$assigned_terr['territory_name'].','.'</li>';
								?>
							</ul>	
						</div>
						<?php }}?>
						<br />
					</td>
					</table>
					</fieldset>
					</div>
					</br>
	
					</td>
				</tr>
			</table>
		
        </div>
        
	
</div>
</div>
 
 
<div id="view_activity_log" style="display: none; padding: 4px;" title="Activity Log History">
		<table width="100%" class="datagrid" id=task_log>
		<thead>
			<th>Task Start Date</th>
			<th>Task End Date</th>
			<th>Task Status</th>
			<th>Remarks</th>
			<th>Logged On</th>
			<th>Logged By</th>
		</thead>
		<tbody>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
		</tbody>
		</table>
</div> 

<script>
var employee_id = '<?php echo $emp_details['employee_id']*1;?>';
prepare_daterange('from_date','to_date');

$("#by_date_range_frm").submit(function(){
	var from=$("#from_date",this).val();
	var to=$("#to_date",this).val();

	if(from=='' || to=='')
	{
		alert("Please check your date range");
		return false;
	}	
});
	


	function view_log(task_id)
	{
		$('#view_activity_log').data('task_id',task_id).dialog('open');
	}
	
	$( "#view_activity_log" ).dialog({
		modal:true,
		autoOpen:false,
		width:'516',
		height:'auto',
		open:function(){
		dlg = $(this);
	
		// ajax request fetch activity log details
		$('#task_log tbody').html("");
		   $.post(site_url+'/admin/jx_view_activitylog',{task_id:$(this).data('task_id')},function(result){
		   if(result.status == 'error')
			{
				alert("Activity Log details not found");
				dlg.dialog('close');
		    }
		    else
			{
				$.each(result.activity_log,function(k,v){
					 var tblRow =
						 "<tr>"
						  +"<td>"+v.start_date+"</td>"
						  +"<td>"+v.end_date+"</td>"
						  +"<td>"+result.task_status_list[v.task_status]+"</td>"
						  +"<td>"+v.msg+"</td>"
						  +"<td>"+v.logged_on+"</td>"
						  +"<td>"+v.name+"</td>"
						  +"</tr>"
						  $(tblRow).appendTo("#task_log tbody");
				});
	
			}
				
		   },'json');
	}
	});

	$('.tab_view').tabs();

	function  load_smslog_data(ele,type,pg,emp_id)
	{
		$($(ele).attr('href')+' div.tab_content').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
		terr_id = 0;
		$.post(site_url+'/admin/jx_getpnh_exsms_log/'+type+'/'+terr_id*1+'/'+employee_id+'/'+pg*1,'',function(resp){
			$($(ele).attr('href')+' div.tab_content').html(resp.log_data+resp.pagi_links);
			
			$($(ele).attr('href')+' div.tab_content .datagridsort').tablesorter();
			
		},'json');
	}

	$('.log_pagination a').live('click',function(e){
		e.preventDefault();
		$.post($(this).attr('href'),'',function(resp){
			$('#'+resp.type+' div.tab_content').html(resp.log_data+resp.pagi_links);
			$('#'+resp.type+' div.tab_content .datagridsort').tablesorter();
		},'json');
	});
	$('#tbl_paid_collection').trigger('click');
	$('#tbl_upcomming_tasks').trigger('click');

	function suspend_emp(s_empid)
	{
		$("#suspend_emprmks").data('employee_id',s_empid).dialog('open');
	}

	function unsuspend_emp(un_sempid)
	{
		$("#unsuspend_emprmks").data('employee_id',un_sempid).dialog('open');
	}

	$("#suspend_emprmks").dialog({
		modal:true,
		autoOpen:false,
		width:'280',
		height:'200',
		'open':function(){
			dlg=$(this);
			$("#suspend_emprmks input name['s_empid'])",this).val(dlg.data('employee_id'));
		},

		buttons:{
			'Submit':function(){
				var suspend_rmksfrm = $("#suspend_rmrksfrm",this);
				 if(suspend_rmksfrm.parsley('validate'))
					 {
		
						 $('#suspend_rmrksfrm').submit();
						$(this).dialog('close');
					}
			       else
			       {
			       	alert('Remarks Need to be addedd!!!');
			       }
				
			},
			'Cancel':function()
			{
				$(this).dialog('close');
			},
		}
		
	});

	$("#unsuspend_emprmks").dialog({
		modal:true,
		autoOpen:false,
		width:'280',
		height:'200',
		'open':function(){
			dlg=$(this);
			$("#unsuspend_emprmks input name['un_sempid'])",this).val(dlg.data('employee_id'));
		},

		buttons:{
			'Submit':function(){
				var suspend_rmksfrm = $("#unsuspend_rmrksfrm",this);
				 if(suspend_rmksfrm.parsley('validate'))
					 {
		
						 $('#unsuspend_rmrksfrm').submit();
						$(this).dialog('close');
					}
			       else
			       {
			       	alert('Remarks Need to be addedd!!!');
			       }
				
			},
			'Cancel':function()
			{
				$(this).dialog('close');
			},
		}
		
	});

	$("#do_not_send_sms").click(function(){
		var emp_id=$(this).attr('emp_id');
		var status='';
		if($(this).is(":checked"))
			status=$(this).attr('value');
		else
			status=1;
		
			if(confirm("Are you sure want to change"))
			{
				$.post(site_url+'/admin/jx_update_emp_sms_status',{emp_id:emp_id,status:status},function(res){
						alert(res.message);
				},'json');
			}
		
	});	
</script>
<style>
.tab_view_inner {
	padding: 0px !important;
}

.pagination a {
	background: none repeat scroll 0 0 #E3E3E3;
	border: 1px dotted #CDCDCD;
	color: #676767;
	font-size: 13px;
	padding: 3px 6px;
	text-decoration: none;
}

.pagination a:hover {
	background: none repeat scroll 0 0 #FFFFFF;
	padding: 4px 6px;
}

.list-inline li {
	display: inline-block;
	text-transform: capitalize;
}

.emp_bio p {
	margin-bottom: 30px;
}

.tabs {
	padding: 0px;
}

.tabcont {
	padding: 5px;
}

.suspend_link {
	border-radius: 5px;
	background: #f77;
	display: inline-block;
	padding: 3px 7px;
	color: #fff;
}

.suspend_link:hover {
	border-radius: 0px;
	background: #f00;
	text-decoration: none;
}

fieldset {
	background: #f9f9f9;
	border: 1px solid #F1F1F1;
	padding: 5px 20px;
}
fieldset legend{
	padding: 5px;
	background: #FFF;
	margin-left: -10px;
}
</style>