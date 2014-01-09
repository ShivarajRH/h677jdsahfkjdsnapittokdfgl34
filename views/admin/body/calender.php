<?php $user=$this->session->userdata("admin_user");
	$task_typelist=$this->db->query('select id,task_type,task_for from pnh_m_task_types order by id asc')->result_array();
?>
<link rel='stylesheet' type='text/css' href="<?php echo base_url().'css/fullcalendar.css'?>">
<script type='text/javascript' src="<?php echo base_url().'js/fullcalendar.min.js'?>"></script>
<script type='text/javascript' src="<?php echo base_url().'js/jquery-ui-timepicker-addon.js'?>"></script>
<script>
	$(function(){
	    $( ".tsk_stdate" ).datepicker({
	      changeMonth: true,
	     dateFormat: "dd/mm/yy",
	    	minDate: new Date(),
	      numberOfMonths:1,
	      onClose: function( selectedDate ) {
	        $( ".tsk_endate" ).datepicker( "option", "minDate", selectedDate );
	    
	      }
	    });
	    $( ".tsk_endate" ).datepicker({
	     	 changeMonth: true,
	   	 	dateFormat: "dd/mm/yy",
	    	minDate: new Date(),
	      	numberOfMonths: 1,
	      	onClose: function( selectedDate ) {
	        $( ".tsk_stdate" ).datepicker( "option", "maxDate", selectedDate );
	      
	      }
	    });
	   
	});

	function add_holiday_for_emp(){
		$('#add_holiday').dialog({
			modal:true,
			autoOpen:true,
			width:'500',
			height:'400',
			open:function(){
			dlg = $(this);
			},
			buttons:{
					'Cancel' :function(){
					 $(this).dialog('close');
					},
					'Submit':function(){
						var dlg = $(this);
						var frm_addholiday = $("#add_holiday_form",this);
						 if(frm_addholiday.parsley('validate')){
							 $.post(site_url+'/admin/jx_addholiday',frm_addholiday.serialize(),function(resp){
						          if(resp.status == 'success')
			                         {
			                         
			                    	 dlg.dialog('close');
			                         }
			                },'json');
			            }else
			            {
			             alert('Error!!!');
			            }
			          
			    },
			}
				
		});
	}	

	function view_holiday_details()
	{
		$('#view_holidy_details').dialog({
			modal:true,
			autoOpen:true,
			width:'800',
			height:'500',
			open:function(){
			dlg = $(this);
			$('#view_holidays_list tbody').html("");
			$.post(site_url+'/admin/jx_loadholiday',function(result){
				if(result.status == 'error')
				{
					alert("Holiday Log details not found");
					dlg.dialog('close');
			    }
				else
				{
					$.each(result.holiday_list,function(k,v){
						 var tblRow =
							 "<tr>"
							  +"<td>"+v.name+"</td>"
							  +"<td>"+v.holidy_stdt+"</td>"
							  +"<td>"+v.holidy_endt+"</td>"
							  +"<td>"+v.ttl_days+"</td>"
							  +"<td>"+v.remarks+"</td>"
							  +"<td>"+v.createdbyname+"</td>"
							  +"<td>"+v.created_on+"</td>"
							  +"</tr>"
							  $(tblRow).appendTo("#view_holidays_list tbody");
					});
		
				}
			},'json');
		}
	});
}

	
	$('.leftcont').hide();

	
	var typenames = [];
		<?php foreach($task_typelist as $typedet){?>
			typenames[<?php echo $typedet['id']?>] = "<?php echo $typedet['task_type']?>"; 
		<?php }?>
</script>
<div id="container-fluid"></div>
	<a onclick="print_report()" href="javascript:void(0)" class="btn fl_right" style="margin:10px !important;">Print</a>
	<span class="fl_right task_color_legends">
		Task Legends : <b class="pending">&nbsp;</b> Pending <b class="completed">&nbsp;</b> Completed <b class="closed">&nbsp;</b> Closed   
	</span>
	
<h1 class="mainbox-title" align="left">Event Calender For PNH Employees</h1>
<table width="100%">
	<tr>
		<td width="70%">
			<div id="calender_cont">
					<select name="view_byterry" id="chose_terry" class="chzn-select" style="min-width: 180px;" data-placeholder="Choose Territory">
					<option value="">Choose Territory</option>
					<?php if ($territory_list){
					      foreach($territory_list as $terr){?>
					      <option value="<?php echo $terr['id']; ?>" <?php echo set_select('territory',$terr['id']); ?> ><?php echo $terr['territory_name']?></option>
					<?php
						 }
					}
					?>
					</select>
					<select id="view_bytwn" class="chzn-select" style="min-width: 180px;" name="towns[]"  data-placeholder="Choose Towns" data-required="true"></select>
					<select id="sub_emp_list" class="chzn-select" style="min-width: 180px;" name="sub_emp"  data-placeholder="Choose employee" data-required="true"></select>
				 	
				 	<a href=javascript:void(0) onclick=add_holiday_for_emp()>Add Holiday</a>&nbsp;&nbsp;&nbsp;
			
				 	<a href=javascript:void(0) onclick=view_holiday_details()>View Holiday Details</a>
			
					<div id="add_holiday" title="Add Holidays for Employees" style="display: none;">
					<form action="<?php echo site_url('admin/jx_addholiday')?>" method="post" id="add_holiday_form" data-validate="parsley">
						<table width="100%" cellspacing="5" cellpadding="3">
						<tr>
							<td>Employee Name:</td>
							<?php 
							$all_emp_list=$this->db->query("SELECT b.employee_id,b.name AS employee_name,b.job_title,c.short_frm AS role_name,d.short_frm
															FROM `m_town_territory_link` a 
															JOIN m_employee_info b ON a.employee_id = b.employee_id 
															JOIN  m_employee_roles c ON c.role_id=b.job_title
															LEFT JOIN m_employee_roles d ON d.role_id=b.job_title2
															WHERE b.job_title > 2 AND a.is_active = 1 
															GROUP BY b.employee_id   
															ORDER BY employee_name ASC")->result_array();
							?>
							<td>
								<select name="emp_for_holiday" id="emp_list_for_holiday" style="width:200px;" class="chzn-select" data-required="true">
								<option value="">Select Employee</option>
								<?php if($all_emp_list){
									foreach($all_emp_list as $hol_emp){
								?>
								<option value="<?php echo $hol_emp['employee_id']?>"><?php echo $hol_emp['employee_name']?></option>
								<?php } }?>
								</select>
							</td>
						</tr>
						<tr>
							<td>Date:</td>
							<td>
							<input type="text" name="hol_stdate" class="tsk_stdate" style="width:90px;height:25px;" value="<?php echo set_value('hol_stdate');?>"  data-required="true"/>
							
							<input type="text" name="hol_endate" class="tsk_endate" value="<?php echo set_value('hol_endate');?>" style="width:90px;height:25px;" data-required="true"/></td>
						</tr>
						<tr>
						<td>Remarks:</td>
						<td><textarea name="holidy_remarks" data-required="true"></textarea></td>
						</tr>
						</table>
						</form>
					</div>
					<div id="view_holidy_details" Title="Employee Holiday List" style="display: none;">
						<table id="view_holidays_list" width="100%" class="datagrid">
						<thead>
							
							<th>Employee Name</th>
							<th>From</th>
							<th>End</th>
							<th>Total Days</th>
							<th>Remarks</th>
							<th>Created By</th>
							<th>Created On</th>
							
						</thead>
						<tbody>
						</tbody>
						</table>
					</div>
					
				 	<div id='calendar'>
				 	</div>
				
			</div>
		</td>
		<td width="30%" >
			<div id="task_listsumm_block" >
				<h3> Agenda of Tasks <span class="pagi_total">
				
				</span></h3>
				<div id="task_listsumm"></div>
				 <div id='agenda_datepicker'></div> 
				
			</div>
		</td>
	</tr>
</table>


	<div id="add_task_frm_dlg" title="Add Task">
		<form action="<?php echo site_url('admin/p_addtask')?>" method="post" id="add_task_frm" >
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top" width="450">
						<table width="100%" cellpadding="10" cellspacing="0">
							<tr class="assgn twn">
								<td>Choose Town:</td>
								<td><select name="assigned_town" class="chzn-select require_inp" style="width: 200px;" data-required="true" >
								
								<?php 
								if($get_locationbyempid){
										$last_grp= '';
								     foreach($get_locationbyempid as $get_location){
						
										$get_location['territory_name'] = ucwords($get_location['territory_name']);
						
										if($last_grp == '')
										{
											echo '<optgroup label="'.$get_location['territory_name'].'">';
											$last_grp = $get_location['territory_name'];
										}	
											
										if($last_grp != $get_location['territory_name'])
										{
											echo '</optgroup><optgroup label="'.$get_location['territory_name'].'">';
											$last_grp = $get_location['territory_name'];
										}
								?>
								<option value="<?php echo $get_location['town_id']; ?>" <?php echo set_select('assigned_town',$get_location['town_id']);?> ><?php echo $get_location['town_name'];?></option>
									<?php }
										echo '</optgroup>';
									}?>
								</select>
							</td>
							</tr>
							<tr class="assgn type">
								<td>Assigned To:</td>
								<td><select name="assigned_to" class="chzn-select require_inp"
									style="width: 200px;">
								</select>
								</td>
							</tr>
							
							<tr>
								<td>Franchisee:</td>
								<td>
								<input type="radio" class="fr_type" name="fr_type" value="1" ><?php echo 'Existing '?>
								<input type="radio" class="fr_type" name="fr_type" value="2" ><?php echo 'New'?>
								</td>
							</tr>
							<tr class="choose_task_type">
								<td width="100" valign="top" style="vertical-align: top !important;">Choose Task Type:</td>
								<td>
									<ul id="task_typelist">
										<?php foreach($task_typelist as $typedet){?>
										<li class="task_typelist_<?php echo $typedet['task_for'];?>"><input type="checkbox" name="choose_task_type[]"  value="<?php echo $typedet['id']?>"><?php echo $typedet['task_type'] ?>
										</li>
										<?php }?>
									</ul>
								</td>
							</tr>
							<tr>
							<td>Date:</td>
							<td>
							<input type="text" name="tsk_stdate" class="tsk_stdate" style="width:90px;height:25px;" value="<?php echo set_value('tsk_stdate');?>"  data-required="true"/>
							
							<input type="text" name="due_date" class="tsk_endate" value="<?php echo set_value('due_date');?>" style="width:90px;height:25px;" data-required="true"/></td>
								
							</tr>
						</table>
					</td>
					<td valign="top">
					
						<div id="task_typelistdetblck">
							<div id="sales_target_frlist"></div>
							<div id="tsk_success"><textarea name="tsk_success" ></textarea></div>
						</div>						
					</td>
				</tr>
			</table>
			
	</form>
</div>

<div id="view_task_dlg" title="Task Details">
<h2 style="float: right;margin-top: -7px;font-size: 12px;color: red;vertical-align: top">Task ID:<b><input size="4px"type="text" name="task_id" id="view_task_refno"  readonly='readonly' style='background-color:#E6E6E6 !important;color:red!important;'></b></h2>


<form   method="post" id="upd_task_frm" data-validate="parsley">
	<input type="hidden" name="task_id" id="view_task_id" class="text ui-widget-content ui-corner-all" data-required="true"/>
	<table width="100%">
	<tr>
	<td valign="top" width="450">
	<table width="100%" cellpadding="10" cellspacing="0">
	<tr>
		<td>Assigned Town:</td>
		<td><input readonly='readonly' style='background-color:#E6E6E6 !important;color:grey !important;' type="text" name="assigned_town" id="view_task_assigned_town" value="" class="text ui-widget-content ui-corner-all" data-required="true"/></td>
	</tr>
	
	<tr>
		<td>Assigned To:</td>
		<td><input readonly='readonly' style='background-color:#E6E6E6 !important;color:grey !important;' type="text" name="assigned_to" id="view_task_assigned_to" value="" class="text ui-widget-content ui-corner-all tooltip" data-required="true">
		</td>
	</tr>
	<tr>
	<td>contact Number:</td>
		<td><a href="javascript:void(0)" id="view_emp_permalink" style="cursor: pointer;"><input style='cursor: pointer;color:#133ED5 !important;' type="text" name="assigned_to" id="view_task_assigned_phno" value="" class="text ui-widget-content ui-corner-all tooltip" data-required="true">
		</a></td>
	</tr>
	
	<tr>
		<td>Franchisee:</td>
		<td>
		<input type="radio" class="view_fr_type_e" value="1" ><?php echo 'Existing '?>
		<input type="radio" class="view_fr_type_n"  value="2" ><?php echo 'New'?>
		</td>
	</tr>
	
	<tr>
		<td>Tasks:</td>
		<td>
			<ul id="view_task_type_list">
				<li></li>
			</ul>
		</td>
	</tr>
	
	<tr >
		<td>Franchises:</td>
		<td>
			<ul id="view_task_assigned_franchise">
					<li></li>
			</ul>
		</td>
	</tr>
	<tr>
		<td>Date:</td>
		<td><input type="text" name="tsk_stdate" id="view_task_stdate" class= "tsk_stdate"  style="width:90px;height:25px;" value="" class="text ui-widget-content ui-corner-all" data-required="true"/>
		<input type="text" name="due_date" id="view_task_due_date" class= "tsk_endate"  style="width:90px;height:25px;" value="" class="text ui-widget-content ui-corner-all" data-required="true"/></td>
	</tr>
	<tr>
		<td>Assigned By:</td>
		<td><input readonly='readonly' style='background-color:#E6E6E6 !important;color:grey !important;' type="text" name="assigned_by" id="view_task_assigned_by" value="" class="text ui-widget-content ui-corner-all" data-required="true"/></td>
	</tr>	
	
	<tr>
		<td>Status :</td>
		<td>
		<select name="status" id="view_task_status" class="status_type" id="test">					
		<option value="1" <?php echo set_select('status',1);?>>Pending</option>
		<option value="2" <?php echo set_select('status',2);?>>Complete</option>
		<option value="3" <?php echo set_select('status',3);?>>Closed</option>
		</select>
		</td>
	</tr>
	
	
	
	</table>
	</td>
	<td valign="top">
		<div id="view_task_typelistdetblck">
		</div>
		
		<div id="view_remarks_posted_via_sms">
		</div>
		<div class="status st_msg">
		<div class="label"><b>Remarks :</b>
		<br/>
		<textarea name="msg" id="view_task_reason" style="height:60px; width:98%;"col="25"></textarea>
		</div>
	</div>
	</td>
	</tr>
	
	</table>
	</form>
	</div>

<script type='text/javascript'>
function load_taskdlg(datestr,task_id)
{
	force_eventview_id = task_id;
	$('#calendar').fullCalendar("refetchEvents");
	$('#calendar').fullCalendar('gotoDate', new Date(datestr));
}
var force_eventview_id = 0;
var url_hash = location.hash;
var task_id = location.hash.replace('#taskview-','');    
	if(url_hash.length != task_id.length)    
    	force_eventview_id = task_id;


function fil_emplist(){
	$('#sub_emp_list li').hide();
	$('#sub_emp_list li input[name="employee"]:checked').attr('checked',false);
	
	var sel_terrid = $('#chose_terry').val()*1;
	var sel_twnid = $('#view_bytwn').val()*1; 
		console.log('.tt_'+sel_terrid+'_'+sel_twnid);

		$('.tt_0_0').show();
		$('.tt_'+sel_terrid+'_0').show();
		$('.tt_'+sel_terrid+'_'+sel_twnid).show();

		
		$('.no_tt_link').show();

		if(!sel_terrid && !sel_twnid)
		$('#sub_emp_list li').show();
		
}

function fmt_date(dObj){
	var d = (dObj.getDate())>9?(dObj.getDate()):'0'+(dObj.getDate());
	var m = (dObj.getMonth()+1)>9?(dObj.getMonth()+1):'0'+(dObj.getMonth()+1);
	
	//return dObj.getFullYear()+'-'+m+'-'+d;
	return d+'-'+m+'-'+ dObj.getFullYear();
}

function fmt_date_slash(dObj){
	var d = (dObj.getDate())>9?(dObj.getDate()):'0'+(dObj.getDate());
	var m = (dObj.getMonth()+1)>9?(dObj.getMonth()+1):'0'+(dObj.getMonth()+1);
	
	//return dObj.getFullYear()+'-'+m+'-'+d;
	return d+'/'+m+'/'+ dObj.getFullYear();
	
}

function fmt_datetime(dObj){
	var d = (dObj.getDate())>9?(dObj.getDate()):'0'+(dObj.getDate());
	
	var m = (dObj.getMonth()+1)>9?(dObj.getMonth()+1):'0'+(dObj.getMonth()+1);
	
	var h = dObj.getHours();
	var mm = (dObj.getMinutes())>9?(dObj.getMinutes()):'0'+(dObj.getMinutes());
	var ampm='am';
		if(h>12)
		{
			h=h-12;
			ampm='pm';
		}
		if(h==0)
		h=12;

	h=((h>9)?h:'0'+h);
	
	
	return dObj.getFullYear()+'-'+m+'-'+d+' '+h+':'+mm+' '+ampm;
	
}
var agenda_events = new Array();

function getMonth(){
	  var date = $("#calendar").fullCalendar('getDate');
	  var month_int = date.getMonth();
	  //you now have the visible month as an integer from 0-11
}	

function switchView(view) {
    $('#calendar').fullCalendar('changeView', view);
    loadEvents();
}

	 $cal = $('#calender');
	 $( "#add_task_frm_dlg" ).dialog({
		 							modal:true,
		 							autoOpen:false,
		 							width:'900',
		 							height:'auto',
		 							autoResize:true,
		 							open:function()
		 							{
		 								 
										 // prepare add new task form by clearing content in form 
		 								
		 								var sel_twn_id = $('#view_bytwn').val()?$('#view_bytwn').val():"";
		 								auto_select_emp_id = $('#sub_emp_list').val()?$('#sub_emp_list').val():0;
		 								
		 								$('select[name="assigned_town"]',this).val(sel_twn_id).trigger("liszt:updated");
		 								
		 								if(sel_twn_id)
		 									$('select[name="assigned_town"]',this).val(sel_twn_id).trigger('change');
		 								
		 								$('select[name="assigned_to"]',this).html("").val("").trigger("liszt:updated");
		 								$('input[name="fr_type"]',this).attr("checked",false);
		 								$('input[name="fr_type"]:first',this).attr("checked",true).trigger('change');
		 								
		 								$('input[name="choose_task_type[]"]',this).attr("checked",false);
		 								$('#task_typelistdetblck').html("");

		 								$('#task_assigned_town').val("");
		 								$('#choose_franchise').hide();
		 								
			 						var st_date_str = fmt_date_slash($(this).data('st_date'));
			 						var end_date_str = fmt_date_slash($(this).data('en_date'));
			 							
			 							$('input[name="tsk_stdate"]',this).val(st_date_str);
			 							$('input[name="due_date"]',this).val(end_date_str);
			 						},
			 						buttons:{
				 						'Cancel' :function(){
					 					 $(this).dialog('close');
					 				},
					 					'Submit':function(){
					 						var dlg = $(this);
				 							var frm_addtask = $("#add_task_frm",this);
				 							
					 					       	
												$('.require_inp',this).each(function(){

													var ival =  $(this).val();
														if($(this).hasClass("chzn-done") && ival == 0 )
															ival = "";
													
													if(ival == "")
													{
														$(this).parent().append('<span class="error_msg_text">required</span>');	
													}
													else
													{
														$(this).parent().find('.error_msg_text').remove();
													}	

												
												});
					 						
					 						if(!$('.error_msg_text',this).length)
			 								{
							 					$.post(site_url+'/admin/jx_add_emptask',frm_addtask.serialize(),function(resp)
						 						{
							 			    		if(resp.status == 'success')
							 						{
							 				   			dlg.dialog('close');
		                                           		$('#calendar').fullCalendar('refetchEvents');	
					 					    		}
							 					},'json'); 
						 					}else
						 					{
						 						alert("Please fill required fields!!!")
							 				}

											if ($(this).find('input[name="choose_task_type[]"]:checked').length == 0) {
											      alert('check at least one checkbox');
											      return false;
										}
											
					 					}
			 						}
	                          });

	 $("#view_task_dlg" ).dialog({
			modal:true,
			autoOpen:false,
			width:'900',
			height:'auto',
			autoResize:true,
			open:function(){
			dlg = $(this);

			$('#view_task_refno').val("");
			$('#view_task_id').val("");
			$('#view_task_title').val("");
			$('#view_task_task').val("");
			$('#view_task_stdate').val("");
			$('#view_task_due_date').val("");
			$('#view_task_assigned_by').val("");
			$('#view_task_assigned_to').val("");
			$('#view_task_assigned_phno').val("");
			$('#view_task_status').val(1);
			$('#view_task_assigned_town').val("");
			$('#view_task_assigned_franchise').val("");
			$('#view_task_assigned_type').val("");
			
		
			$('#view_sales_target_frlist').hide();
		
			// ajax request fetch task details
			   $.post(site_url+'/admin/jx_load_taskdet',{id:$(this).data('task_id')},function(result){
			   if(result.status == 'error')
				{
					alert("Task details not found");
					dlg.dialog('close');
			    }
			    else
				{
			    	$('#view_task_refno').val(result.task.ref_no);
			    	
					$('#view_task_id').val(result.task.id);
					$('#view_task_title').val(result.task.title);
					$('#view_task_task').val(result.task.task);
					$('#view_task_stdate').val(result.task.start);
					$('#view_task_due_date').val(result.task.end);
					$('#view_task_assigned_by').val(result.task.assigned_byrole_name);
					$('#view_task_assigned_to').val(result.task.assignedto_byrole_name);
					$('#view_task_assigned_phno').val(result.task.contact_no);
					$('#view_task_status').val(result.task.status);
					$('#view_task_reason').val(result.task.reason);
					$('#view_task_assigned_town').val(result.task.town_name);
					$('#view_task_assigned_type').val(result.task.task_name);
					$('#view_task_type_list').html("");
					$('#view_task_assigned_franchise').html("");
					var task_type_arr = result.task.task_type.split(',');
					$('#view_task_typelistdetblck').html("");
					$('#view_remarks_posted_via_sms').html("");

					$('#view_emp_permalink').attr('href',site_url+'/admin/view_employee/'+result.task.assigned_to);
					

						if(result.task_sms_remarks != undefined)
						{
							var remarks_via_sms_table='';
								remarks_via_sms_table='<div><h4>Remarks Via SMS</h4><table class="datagrid"><thead><th>Task</th><th>SMS</th><th>Posted By</th><th>Posted On</th></thead><tbody>';
								$.each(result.task_remarks_sms,function(t,r){
									remarks_via_sms_table+= '<tr><td>'+r.ref_id+'</td><td width="80%">'+r.remarks+'</td><td>'+r.posted_by+'</td><td width="100%">'+r.posted_on+'</td></tr>';
								});
							remarks_via_sms_table+= '</tbody></table></div>';
						 	$('#view_remarks_posted_via_sms').append(remarks_via_sms_table).show();
						}
						var fr_list = new Array();
						
							 $.each(task_type_arr,function(t,task_type)	{
								$('#view_task_type_list').append('<li>'+result.task_type_names[task_type]+'</li>');
								var tsk_tblhtml='';
								if(task_type==1)
								{
									
											tsk_tblhtml = '<div><h4>Sales Target</h4><table class="datagrid"><thead><th>Franchise Name</th><th>Avg Weekly Sales</th><th>Target Sales</th><th>Actual Target</th></thead><tbody>';
											$.each(result.sales_target,function(i,frdet){
												tsk_tblhtml += '<tr><td><b>'+frdet.franchise_name+'</b></td><td>'+frdet.avg_amount+'</td><td><input type="text" name="tg_sales['+frdet.f_id+']" value="'+frdet.target_amount+'" class="st_weekly_sales_target"></td><td>'+frdet.actual_target+'</td></tr>';
												fr_list[frdet.f_id]=frdet.franchise_name;
											});
											tsk_tblhtml += '</tbody></table></div>';
											
											
								}

								else
								{
									$('#tsk_success').show();
									$.each(result.task_type_list[task_type],function(i,typedet){
										tsk_tblhtml = '<div><h4>'+typedet.task_type;
										if(task_type==2)
											tsk_tblhtml += '&nbsp&nbsp&nbspCurrent Balance: <span>'+typedet.custom_field_1+'</span>';	
										tsk_tblhtml += '</h4><table width="100%">'+
										'	<tbody><tr><td><textarea  name = "view_reqst_msg['+task_type+']" style="width: 437px; height: 35px;">'+typedet.request_msg+'</textarea></td> <td></td></tr></tbody>'+
										'	</table></div>';
									
									});
								}


								
								 $('#view_task_typelistdetblck').append(tsk_tblhtml).show();

								

								 $('.view_fr_type_e').attr('checked',false).attr('disabled',true);
								 $('.view_fr_type_n').attr('checked',false).attr('disabled',true);
								 
								if( result.tasks_type_for[task_type]==1)
									$('.view_fr_type_e').attr('checked',true);
								else
									$('.view_fr_type_n').attr('checked',true);	
								
						});

						$.each(fr_list,function(f_id,franchise_name){
							if(franchise_name!=undefined)
								$('#view_task_assigned_franchise').append('<li>'+franchise_name+'</li>');
						});
								
				
					
					$('.status_type').trigger('change');
					if(result.task.status == 2)
					{
						$("#view_task_status option[value=" + 1 + "]").hide();
						$('#view_task_stdate').attr('disabled',true);
						$('#view_task_due_date').attr('disabled',true);
						$('.st_weekly_sales_target').attr('disabled',true);
						$('#view_task_typelistdetblck textarea').attr('disabled', true).Screen.showCursor = false;
					
						//$('#view_task_reason').	attr('readonly',false);
					}
					
					if(result.task.status == 3)
					{
						$('#view_task_stdate').attr('disabled',true);
					 	$('#view_task_due_date').attr('disabled',true);
						$('#view_task_status').	attr('disabled',true);
						$('.st_weekly_sales_target').attr('disabled',true);
						$('#view_task_typelistdetblck textarea').attr('disabled', true).Screen.showCursor = false;
					
						$(".ui-dialog-buttonpane button:contains('Update')").button().hide();
						
					}
					else
					{
						$('#view_task_reason').attr('disabled', false);
						$('#view_task_status').	attr('disabled',false);
						$('#view_task_stdate').attr('disabled',false);
					 	$('#view_task_due_date').attr('disabled',false);
						$(".ui-dialog-buttonpane button:contains('Update')").button().show();
					
				    }
					}
					
			   },'json');
		},
			buttons:{
					'Update':function(){
	 					var dlg = $(this);
							var frm_updtask = $("#upd_task_frm",this);
							if(frm_updtask.parsley('validate')){
	 					 	$.post(site_url+'/admin/jx_upd_emptask',frm_updtask.serialize(),function(resp){
		 					 	if(resp.status == 'success')
		 					 	{
		 					 		dlg.dialog('close');
		 					 		$('#calendar').fullCalendar('refetchEvents');	
			 					}
		 					},'json');
	 					}else
	 					{
	 						alert("Error!!!")
		 				}
					},
			/* 'Delete': function(){
					var dlg = $(this);
					var frm_deletetask = $("#upd_task_frm",this);
					if(confirm("Are you sure want to delete this task ?"))
					{
						$.post(site_url+'/admin/jx_update_deltedtask',frm_deletetask.serialize(),function(resp)
						{
							dlg.dialog('close');
					 		$('#calendar').fullCalendar('refetchEvents');	

						},'json');
				 }
		
				}*/
			}
	});

	 var twnlnk_franchise_html='';

   $(document).ready(function(){
		  var date = new Date();
		  var d = date.getDate();
		  var m = date.getMonth();
		  var y = date.getFullYear();

		  $('#calendar').fullCalendar({
			  	editable: true,
			  	droppable: true,
		   		draggable: true,
		   		
			   	header: {
		 		left: 'prev,next today',
		 		center: 'title',
		 		right: 'month,basicWeek,agendaDay'
		 		},
		   	
		   	selectable: true,
			selectHelper: true,
			select:function( startDate, endDate, allDay, jsEvent, view )
             {
	           
				var d1 = (startDate.getDate())>9?(startDate.getDate()):'0'+(startDate.getDate());
				var m1 = (startDate.getMonth()+1)>9?(startDate.getMonth()+1):'0'+(startDate.getMonth()+1);
					
					//return dObj.getFullYear()+'-'+m+'-'+d;
					var dstr = startDate.getFullYear()+'/'+m1+'/'+d1;

				//2012-01-01
		        if((new Date(dstr).getTime()) >= (new Date(y,m,d)).getTime())
					$( "#add_task_frm_dlg" ).data({'st_date':startDate,'en_date':endDate}).dialog('open');
		        
			        
             },
		   
				loading: function(bool) {
			    if (bool) $('#loading').show();
			    else $('#loading').hide();
			 },
			 editable: true,
			
			 events: function(start, end, callback){
				 	sel_emp_id = '';
				 	sel_town_id = '';
				 	sel_usr_id = '';
				 
				 	$('#calendar').fullCalendar('removeEvents')
				 
				 	if($("#sub_emp_list").val())
						sel_emp_id=$("#sub_emp_list").val();

				 	 sel_town_id=$("#view_bytwn").val();
	                 sel_terry_id=$("#chose_terry").val();

	                 if(!sel_emp_id && !sel_town_id && !sel_terry_id)
	                	 $('input[name="my_task"]').attr('checked',true);
	                 else
	                	 $('input[name="my_task"]').attr('checked',false);

				 	
				 	if($('input[name="my_task"]').is(':checked'))
						sel_usr_id = $("input[name='my_task']:checked").val();
                      var pdata = {start: start.getTime(),end: end.getTime(),territory_id:sel_terry_id,town_id:sel_town_id,emp_id:sel_emp_id,user_id:sel_usr_id};
                       $.post(site_url+'/admin/jx_load_tasklist',pdata,function(result)
                    		   {
								 callback(result.tasklist);

								 if(force_eventview_id)
								 {
									 $( "#view_task_dlg" ).data('task_id',force_eventview_id).dialog('open');
									 force_eventview_id = 0;
								 }
								 
                		   },'json');
                  
                    
			   },
	
			 eventClick: function(calEvent, jsEvent, view) 
			 {
				
			     $( "#view_task_dlg" ).data('task_id',calEvent.id).dialog('open');
			     
			 },

			 eventRender: function(event, element)
			  	{         
				
					 var town = event.town_name;
	
					 var name = event.employee_name;

					 var ref_no = event.ref_no;
					 
					 element.find('.fc-event-title').html(ref_no+'-' +town + ' - ' + name );
				},
				
				 eventResize: function(event,dayDelta,minuteDelta,revertFunc) {

				        alert(
				            "The end date of " + event.title + "has been moved " +
				            dayDelta + " days and " +
				            minuteDelta + " minutes."
				        );

				        if (!confirm("is this okay?")) {
				            revertFunc();
				        }
				    },
	      });
	      
		  $('#agenda_datepicker').datepicker({
		        inline: true,
		        beforeShowDay : function(date) {

		        	var d1 = (date.getDate())>9?(date.getDate()):'0'+(date.getDate());
					var m1 = (date.getMonth()+1)>9?(date.getMonth()+1):'0'+(date.getMonth()+1);
						//return dObj.getFullYear()+'-'+m+'-'+d;
					var dstr = date.getFullYear()+'-'+m1+'-'+d1;
					
		        	if ($.inArray((new Date(dstr)).getTime(),agenda_events) != -1) {
		                return [ true, 'mybold', '' ];
		            } else {
		                return [ true, '', '' ];
		            }
		        },
		        onChangeMonthYear:function(year,month,obj){
		        	month = ((month < 10) ? '0'+month:month);
		        	$('#calendar').fullCalendar('gotoDate', new Date(year+'-'+month+'-01'));
			    },
		        onSelect: function(dateText, inst) {
		        var d = new Date(dateText);
		          $('#calendar').fullCalendar('gotoDate', d);
		        }
		    }); 

		  
	});

 	$("input[name='employee']").change(function (){
		if($(this).attr('checked'))
		{
			$("input[name='employee']").attr('checked',false);
			$(this).attr('checked',true);	 
		}
		 $("#my_task").attr('checked',false);
		 $('#calendar').fullCalendar('refetchEvents');
 	});

	 $("#my_task").change(function(){
		 $("input[name='employee']").attr('checked',false);
		 $("#chose_terry").val('').trigger('liszt:updated');
		 $("#view_bytwn").val('').trigger('liszt:updated');
		 $('#calendar').fullCalendar('refetchEvents');    
 	 });


	 $("#my_task").attr('checked',true);
	 $("input[name='employee']").attr('checked',false);

	 

 	$(".status_type").change(function(){
	$(".status").hide();
	$('.status select').html('').val('').trigger("liszt:updated");
    if($(this).val()=="2")
	{
    	
		$(".st_msg").show();
	}
    else if($(this).val()=="3")
	{
		 $(".st_msg").show();
	}
    if($(this).val()=="1")
	{
    	
		$(".st_msg").show();
	}
    
	}).val("1").change();


 	$(".chzn-select").chosen();

var auto_select_emp_id = 0;

$('select[name="assigned_town"]').change(function(){
	
	$('select[name="assigned_to"]').html('').trigger("liszt:updated");
    var	sel_town_id=$(this).val();
	$.getJSON(site_url+'/admin/get_assignedempid/'+sel_town_id,'',function(resp){
	if(resp.status=='error')
			{
				alert(resp.message);
		
			}
		else
		{
		var twn_lnkdemp_html='';
			twn_lnkdemp_html+='<option value="">Choose</option>';
			$.each(resp.assigned_empid,function(i,itm){
					
				twn_lnkdemp_html+='<option '+((auto_select_emp_id==itm.employee_id)?'selected':'')+' value="'+ itm.employee_id +'">'+itm.name+'</option>';	
			});
			$('select[name="assigned_to"]').html(twn_lnkdemp_html).trigger("liszt:updated");	
			auto_select_emp_id = 0;
	}
});
});

	$('select[name="assigned_town"]').change(function(){
		
           $('select[name="choose_franchise[]"]').html('').trigger("liszt:updated");
           var sel_twn_id = $(this).val();
          $.getJSON(site_url+'/admin/get_franchisebytwn_id/'+sel_twn_id,'',function(resp){
           if(resp.status=='errorr')
           {
				alert(resp.message);
           }
           else
           {
			 twnlnk_franchise_html='';
			   twnlnk_franchise_html+='<option value="">choose</option>';
			 $.each(resp.franchise_list,function(i,itm){
				 twnlnk_franchise_html+='<option value="'+ itm.franchise_id+'">'+itm.franchise_name+'</option>';
				 });
			  $('select[name="choose_franchise[]"]').html(twnlnk_franchise_html).trigger("liszt:updated");
           }
         
          }); 
		});
		
	$('#view_bytwn').change(function(){
		load_employees();
		$('#calendar').fullCalendar('refetchEvents');
	});

	$("#chose_terry").change(function(){
		$('#calendar').fullCalendar('refetchEvents');
	});
	var sel_terrid=0;
	$('#chose_terry').change(function(){

		load_employees();
		list_alltaskbysel(0);
		
	   	$('#view_bytwn').html('').trigger("liszt:updated");
	   	sel_terrid = $(this).val();
		$.getJSON(site_url+'/admin/showtwn_lnkterr/'+sel_terrid,'',function(resp){

			 var lnkdtown_list_html='';
		if(resp.status == 'Errorr')
		{
		   alert(resp.message);
		}
		else
		{
		  
		   lnkdtown_list_html += '<option value="">Choose Town</option>';
		   $.each(resp.town_linkedtoterry,function(i,itm){
			   lnkdtown_list_html += '<option value="'+itm.id+'">'+itm.town_name+'</option>';
		
		   });
	  		
	  		// $('#view_bytwn').trigger('change');
		}
		 $('#view_bytwn').html(lnkdtown_list_html).trigger("liszt:updated");

		 $('#view_bytwn').trigger('change');
	  	});
	});

	var sel_town_id=0;
	$('#view_bytwn').on("change",function(){

		fil_emplist();
		list_alltaskbysel(0);
		
 	var sel_town_id = $(this).val();
    $.getJSON(site_url+'/admin/taskdet_bytwnid/'+sel_town_id,'',function(resp){
    if(resp.status == 'error')
    {
	    alert(resp.message);
	}
	else
	{
	  	$('#total_twn_tasks').text(resp.task.total);
	}
	});
	});

	$('#view_bytwn').on("change",function(){
	 var sel_town_id = $(this).val();
     $.getJSON(site_url+'/admin/pendingtaskdet_bytwnid/'+sel_town_id,'',function(resp){
     if(resp.status == 'error')
     {
	    alert(resp.message);
     }
     else
     {
	    $('#pending_tasks').text(resp.task.pending);
	 }
	 });
     });

	$("#chose_terry").val('').trigger("liszt:updated");
	 $("#view_bytwn").val('').trigger("liszt:updated");

$('.task_select').change(function(){
	$('.task').hide();
	if ($(this).val() =="5")
	{
		$('.task_type').hide();
	}
	else if(!$(this.val()))
	{
		$('.task_type').show();
    }
});	

	function print_report()
	{
	/*	var terr_id = $('#chose_terry').val();
		var terr_name=$('#chose_terry option:selected').text();
	
		if(!terr_id)
			{
				alert("Choose territory");
				return false;
			}*/
		var emp_id = $('#sub_emp_list').val();
		var employee_names = $('#sub_emp_list option:selected').text();
			if(!emp_id)
			{
				alert("Choose Employee");
				return false;
			}
	
		var cviewObj = $('#calendar').fullCalendar('getView');
			if(cviewObj.name != 'basicWeek')
			{
				alert("Please choose week from option");
				return false;
			}

		if(!$('#calendar').fullCalendar('clientEvents').length)
		{
			alert("No tasks found for the selected week");
			return false;
		}	
		 
		var fmt_seldate=fmt_date(cviewObj.visStart);
		
		var fmt_endate=fmt_date(new Date(cviewObj.visStart.getTime()+6*24*60*60*1000));
		
			if(confirm("Do you want to take print out for "+employee_names+" From:"+fmt_seldate +" To:"+fmt_endate+" "))
			{
				window.open(site_url+'/admin/task_print/'+emp_id+'/'+fmt_seldate);
			}
	}

	function load_employees()
	{
		var terr_id = $('#chose_terry').val();
		var twn_id = $('#view_bytwn').val();
		$('#sub_emp_list').html('').trigger("liszt:updated");
		$.post(site_url+'/admin/jx_getpnhemployees','terr_id='+terr_id+'&twn_id='+twn_id,function(resp){
			if(resp.status != 'error')
			{
				var optHtml = '<option value="">Choose Employee</option>';
				$.each(resp.emp_list,function(a,b){
					if(b.short_frm==null)
						b.short_frm=b.role_name;
					else
						b.role_name=b.short_frm;
					optHtml += '<option value="'+b.employee_id+'" >'+b.employee_name+' ('+b.role_name+')'+'</option>';
				});	
				$('#sub_emp_list').html(optHtml).trigger("liszt:updated");
				
			}
		},'json');
	}

	$('#sub_emp_list').live('change',function(){
		
		$('#calendar').fullCalendar('refetchEvents');	
		$('.fc-button fc-button-prev fc-state-default fc-corner-left').click();
		list_alltaskbysel(0);
	});


load_employees();


$('.fr_type').change(function() {
	$('#task_typelist li').hide();
	$('#task_typelist li.task_typelist_'+$(this).val()).show();
	$('#task_typelistdetblck').html('');

	$('#task_typelist input[name="choose_task_type[]"]:checked').attr('checked',false);

});
$('.fr_type:first').attr('checked',true).trigger('change');	

 

$('#task_typelist input[name="choose_task_type[]"]').change(function(){
	var typeid=$(this).val();
	var twn_id = $('select[name="assigned_town"] option:selected').val();
	var typedethtml = '';

		typedethtml = '<div id="task_typelistdet_'+typeid+'">';
		if(typeid == 1)
		{
			
		//	load_salestargetdata();
				typedethtml += '<div><h4>Sales Target</h4><table id="sl_target" class="datagrid" width="100%">'+
								'	<thead><th>Franchise</th><th>Monthly Avg</th><th>Last Week Sales</th><th>Target</th><th></th></thead>'+
								'	<tbody><tr><td><select name="tsk_frid[]" class="small_input require_inp" style="width:150px;">'+twnlnk_franchise_html+'</select></td><td class="st_monthly_sales">0</td><td class="st_weekly_sales">0</td><td><input type="hidden" name="avg_sales[]" value="submit"> <input type="text" name="tg_sales[]" class="st_weekly_sales_target require_inp"></td><td><input type="button" class="add_tblrow" value="+"></td></tr></tbody>'+
								'	</table></div>';
		
		
		
		}
		else
		{
			$('#choose_franchise').hide();
			
			var tmpHTML = '';
			if(typeid == 2)
			{
				tmpHTML = '<input type="hidden" name="pc_current_bal" value="0" >';
			}
			typedethtml += '<div><h4>'+typenames[typeid]+'</h4><table width="100%">'+
							'	<tbody><tr><td><textarea name = "reqst_msg['+typeid+'][]"  class="require_inp" style="width: 437px; height: 35px;"></textarea>'+tmpHTML+'</td></tr></tbody>'+
							'</table></div>';
			
						
		

			
		}

		typedethtml += '</div>';
		
		if($(this).attr('checked'))
		{
			if(!$('#task_typelistdet_'+typeid).length)
			{
				$('#task_typelistdetblck').append(typedethtml);

				if(typeid == 2)
				{
					if(!$('#task_typelistdet_2 h4 span').length)
						$('#task_typelistdet_2 h4').append('&nbsp&nbsp&nbspCurrent Balance: <span></span>');
					$('#task_typelistdet_2 h4 span').html('');
					$.getJSON(site_url+'/admin/get_twn_currentbalance/'+twn_id,'',function(resp){
						if(resp.status=='errorr')
						{
							alert(resp.message);
						}
						else
						{
							$('#task_typelistdet_2 h4 span').html(resp.balance.total_balance);
							$('input[name="pc_current_bal"]').val(resp.balance.total_balance);
							
						}
					});
				}
			}
			
		}else
		{
			$('#task_typelistdet_'+typeid).remove();
		}
});

$('select[name="tsk_frid[]"]').live('change',function(){
	var trele = $(this).parent().parent();
	$.post(site_url+'/admin/get_fran_dailysales',{fids:$(this).val()},function(resp){
			$('.st_weekly_sales',trele).text(0);
			$('.st_monthly_sales',trele).text(0);
			if(resp.status != 'error')
			{
				$.each(resp.data,function(i,frdet){
					$('.st_weekly_sales',trele).text(frdet.avg_sales);
					$('.st_monthly_sales',trele).text(frdet.avg_mon_sales);
					$('input[name="avg_sales[]"]',trele).val(frdet.avg_sales);
				});
			}
	},'json');
});


$(".add_tblrow").live('click',function(){
    var tmpl = $(this).parent().parent().html();
   	 	$(this).parent().parent().parent().append('<tr>'+tmpl+'</tr>');
   	 $(this).addClass('remove_tblrow').removeClass('add_tblrow').val('-');
  });

$(".remove_tblrow").live('click',function(){
	$(this).parent().parent().remove();
  });
 


function list_alltaskbysel(st)
{
	var ttl_disp = 5;
	var trid = $('#chose_terry').val();
	var twid = $('#view_bytwn').val();
	var emp_id = $('#sub_emp_list').val();
	$('#task_listsumm_block').show();	
	$('#task_listsumm').html('');

	$('.pagi_total').html('');
	
	$.post(site_url+'/admin/jx_loadall_tasklist/'+st,{trid:trid,twid:twid,emp_id:emp_id},function(resp){
		
		if(resp.status == 'error')
		{
		}else
		{
			$('#task_listsumm').html('');
			$.each(resp.emp_task_list,function(a,b){
				$('#task_listsumm').append('<div class="task_listdet" style="overflow:hidden;" onclick=load_taskdlg("'+b.on_date_str+'",'+b.id+') ><p>'+b.town_name+' - '+b.name+' '+'</p><p><span class="agenda_task_status_'+b.task_status+'">'+resp.task_status[b.task_status]+'</span><span style="float:right;margin-left:10px;"><b>on:</b>  '+b.on_date+'</span><span style="float:right;"><b>by:</b>  '+b.assignedby_name+'</span></p></div>');
			});

			var ttl_pages=Math.ceil(resp.total_rows/ttl_disp);
			var pagiHtml = '<div id="agenda_tasks_pagi" class="pagi" >';
				
				if(st > 0)
					pagiHtml+= '<a href="javascript:void(0)" onclick="list_alltaskbysel('+(st-ttl_disp)+')" >Prev</a> ';
			
				if(resp.total_rows > st+resp.emp_task_list.length)
					pagiHtml+= ' <a href="javascript:void(0)" onclick="list_alltaskbysel('+(st+ttl_disp)+')" >Next</a>';
				
				 
				 
				pagiHtml += '</div>';
				$('#task_listsumm').append(pagiHtml);

				$('.pagi_total').html('<span >'+(st+1)+'-'+(st+resp.emp_task_list.length)+'/'+(resp.total_rows)+' Tasks</span>');
				agenda_events = new Array();
				if(resp.date_summ.length)
				{
					$.each(resp.date_summ,function(a,b){
						agenda_events.push((new Date(b.assigned_date)).getTime());
					});
				}


				$( "#agenda_datepicker" ).datepicker( "refresh" );
				
		}
	},'json');
}

$('#task_listsumm_block').show();
list_alltaskbysel(0);

$('.task_listdet').live('mouseover',function(){
	$(this).addClass('task_listdet_over');
});
$('.task_listdet').live('mouseleave',function(){
	$(this).removeClass('task_listdet_over');
});


</script>

<style>
.task_listdet{cursor: pointer;}
.task_listdet_over{background: #f1f1f1 !important;}
.task_listdet a{color:#333;}
.task_listdet span{font-size: 11px;
padding: 2px 4px;
border-radius: 3px;}
.mybold>a.ui-state-default {
    font-weight:bold;
    background-color: #FFF ; 
    background-image: none ;
    color: #000;
}									
									
#calendar{margin-top: 10px;}
#task_typelistdetblck h4{
margin:2px;
}
.ui-dialog-content td{
vertical-align: top !important;
}

.ui-dialog-content th{
font-size: 11px;
}
#task_typelistdetblck h4,#view_task_typelistdetblck h4{
margin:2px;
}
.st_weekly_sales_target{
width:50px;
}
.add_tblrow,.remove_tblrow{
padding:3px 6px !important ;
}

.small_input{
font-size: 11px !important;
padding:3px 6px !important ;
}
.task_listdet{padding:3px;border-bottom: 1px dotted #cdcdcd;background: #FFF}
.task_listdet p{margin:2px;padding:3px;}
textarea.small_input{width: 98%;}
.pagi {padding:5px 0px;text-align: right;margin:3px 0px;}
.pagi span{float: left}
.pagi a{padding:5px 10px;background: #fcfcfc;border:1px solid #ededed;color: #333}
.pagi a.pagi_selected{background: #555;color: #FFF}
.pagi_total{color: #cd0000;float: right}

#task_listsumm_block{background: #f7f7f1;
    margin: 3px;
    padding: 5px 8px;}
#task_listsumm_block h3{padding: 2px 12px;}
#calender_cont{margin:5px;}
 .mainbox-title {
    color: rgb(10, 156, 204);
    font-size: 24px;
    font-weight: bold;
    margin: 13px 0 0 9px;
    padding: 4px 0 0;
}

#nocursor
  {cursor: default;}
.error_inp{border:1px solid #cd0000 !important;}
.error_msg_text{color: #cd0000;font-size: 11px;display: block;}
 
#task_typelistdetblck div{margin-bottom: 5px;}
#view_task_stdate,#view_task_due_date,.tsk_stdate,.tsk_endate{width: 67px !important;
height: 20px !important;
padding: 2px 1px;}
.text{padding:5px !important;}
li{list-style: none;}
textarea{width: 95% !important;}
form{font-size: 12px;}
.pagi a{background: #555;color: #FFF;}
.pagi a:hover{background: #f1f1f1;color: #555;}
.btn {
    -moz-border-bottom-colors: none;
    -moz-border-left-colors: none;
    -moz-border-right-colors: none;
    -moz-border-top-colors: none;
    background-color: rgb(245, 245, 245);
    background-image: linear-gradient(to bottom, rgb(255, 255, 255), rgb(230, 230, 230));
    background-repeat: repeat-x;
    border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgb(179, 179, 179);
    border-image: none;
    border-radius: 4px 4px 4px 4px;
    border-style: solid;
    border-width: 1px;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
    color: rgb(51, 51, 51);
    cursor: pointer;
    display: inline-block;
    font-size: 14px;
    line-height: 20px;
    margin-bottom: 0;
    padding: 4px 12px;
    text-align: center;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
    vertical-align: middle;
}
.task_color_legends{padding:3px;}
.task_color_legends b{padding: 3px;
display: inline-block!important;
width: 5px;
height: 5px;
margin-left: 10px;}
.task_color_legends b.pending{background: blue}
.task_color_legends b.completed{background: orange}
.task_color_legends b.closed{background: red}
</style>