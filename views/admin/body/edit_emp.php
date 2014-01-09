<?php 
$assigned_emp_det= $this->erpm->get_working_under_det($emp_details['employee_id']);
$assigned_terr_det=$this->erpm->get_assigned_territory_det($emp_details['employee_id']);
$assigned_twn_det=$this->erpm->get_assigned_town_det($emp_details['employee_id']);
?>
		<script type="text/javascript">
				$(function () {
				$("#txtDate").datepicker({
				changeMonth: true,
				changeYear: true,
				yearRange: '1970:1995'
				});

				$(".chzn-select").chosen({no_results_text: "No results matched"}); 
				});
		</script>
<div id="page_container">
<div id="main_column" class="clear">
<div class="cm-notification-container "> </div>
	<div class="tools-container">
		<span class="action-add">
		<a href="<?php echo site_url('admin/list_employee')?>" style="float: right;">List Employees</a></span>
		<h1 class="mainbox-title">Edit Employees</h1>
	</div>
	
	<div class="form_container">
     <form id="edit_emp" enctype="multipart/form-data"  action="<?php echo site_url('admin/process_editemployee'.'/'.$emp_details['employee_id'])?>"  method="post">
		<input type="hidden" name="emp_id" value="<?php echo $emp_details['employee_id']?>">
			<table cellspacing="5" cellpadding="2" width="100%">
			<tr>
				<td>
					<div>
						<fieldset>
						<legend><b>Personal Details</b></legend>
						<table>
							<tr>
								<td>Employee Name :<span class="red_star">*</span>
								</td>
								<td><input type="text" name="emp_name" 
									value="<?php echo set_value('emp_name',$emp_details['name'])?>"> 
								</td>
							</tr>
			
							<tr>
								<td>Father Name :
								</td>
								<td><input type="text" name="father_name" 
									value="<?php echo set_value('father_name',$emp_details['fathername'])?>"> 
								</td>
							</tr>
			
							<tr>
								<td>Mother Name :
								</td>
								<td><input type="text" name="mother_name" 
									value="<?php echo set_value('mother_name',$emp_details['mothername'])?>"> 
								</td>
							</tr>
							<?php 
								$dob = strtotime($emp_details['dob'])?$emp_details['dob']:'';
							?>
							<tr>
							<td>D.O.B :</td>
							<td><input type="text" id="txtDate" name="dob" 
								value="<?php echo set_value('dob',$dob)?>">
							</td>
						</tr>
			
							<tr>
								<td>Gender :<span class="red_star">*</span>
								</td>
									<?php $gender=array('Male'=>'Male','Female'=>'Female');?>
									<td><select name="gender" >
											<option value=Choose>Choose</option>
											<?php foreach($gender as $gen)
											{?>
												<option value="<?php echo $gen; ?>" <?php if($emp_details['gender']==$gen){?> selected="selected"<?php }?>>
												<?php echo $gen; ?></option>
									<?php echo set_select('gender',$emp_details['gender'])?>
									 <?php }?>
									 </select></td>
							</tr>
			
			
							<tr>
								<td>Qualification :
								</td>
								<td><input type="text" name="edu" 
									value="<?php echo set_value('edu',$emp_details['qualification'])?>"> <?php echo form_error('edu','<div class="error">','</div>')?>
								</td>
							</tr>
			
						
							<tr>
								<td>Address :<span class="red_star">*</span></td>
								
								<td>
								<textarea rows="1" style="height: 60px; width: 140px;"
									cols="25" name="address" align="centre" 
								    value= "<?php echo $emp_details['address'];?>"><?php echo $emp_details['address'];?></textarea> 
								    <?php echo form_error($emp_details['address'],'<div class="error">','</div>')?>
								</td>
							</tr>
			
							<tr>
								<td>City :<span class="red_star">*</span></td>
								<td><input type="text" name="city" value="<?php echo set_value('city',$emp_details['city']);?>"><?php echo form_error('city','<div class="error">','</div>')?>
								</td>
							</tr>
							
							<tr>
								<td>PostCode:</td>
								<td><input type="number" name="postcode" 
									value="<?php echo set_value('postcode',$emp_details['postcode'])?>"> <?php echo form_error('pincode','<div class="error">','</div>')?>
								</td>
							</tr>
							
							<tr>
								<td>Upload Image :</td>
								<td>
								<?php if(strlen(trim($emp_details['photo_url']))){?>
								   	<div style="padding:5px;background: #fffff0;">
								   		<input type="checkbox" value="1" id="tgl_csv_cnt"  >
								   			<?php echo $emp_details['photo_url'];?>
								   	
									   	<div id="tgl_csv_blk" style="padding:5px;display: none;">
								<input type="file" name="image" 
								   value="<?php echo set_value('image',$emp_details['photo_url'])?>"> 
									</div>
									</div>
									<?php }else{?>
										<input type="file" name="image" value="<?php echo set_value('image',$emp_details['photo_url'])?>">
										<?php
										}?>
								  <?php echo form_error('cv','<div class="errorr">','</div>')?>	
								</td>
							</tr>
							
							<tr>
							   <td>Upload CV :</td>
							   <td>
							   		<?php if(strlen(trim($emp_details['cv_url']))){?>
								   	<div style="padding:5px;background: #fffff0;">
								   		<input type="checkbox" value="1" id="tgl_csv_cnt"  >
								   			<?php echo $emp_details['cv_url'];?>
								   	
									   	<div id="tgl_csv_blk" style="padding:5px;display: none;">
									   	<input type="file" name="cv" value="<?php echo set_value('cv',$emp_details['cv_url'])?>">
									   	</div>
								   	</div>
								   	<?php }else{
								   	?>
								   	<input type="file" name="cv" value="<?php echo set_value('cv',$emp_details['cv_url'])?>">
								   	<?php 	
								   	}?>
								   		
							   <?php echo form_error('cv','<div class="errorr">','</div>')?>
							   </td>
							</tr>
							 
					</table>
				</fieldset>
			</div>
		</td>
			
			<td>
				<div>
					<fieldset>
						<legend><b>Contact Details</b></legend>
						<table>
						<tr>
							<td>Email Id :
							</td>
							<td><input type="email" name="email_id" 
								value="<?php echo set_value('email_id',$emp_details['email'])?>"> <?php echo form_error('email_id','<div class="error">','</div>')?>
							</td>
						</tr>
					
						<tr>
						    <td>Contact no :<span class="red_star">*</span>
							</td>
							<td><span class="mob1_error"></span>
								<ol id="contactList">
							<?php 
									$cno_list = $emp_details['contact_no'];
									foreach(explode(',',$cno_list) as $cno)
									{
								?>
										<li><input type="text" maxlength="10" name="contact_no[]"  class="contact_no clearContent" value="<?php echo set_value('contact_no',$cno)?>"></li>		
								<?php 		
									}
								?>
								</ol>
							</td>
						</tr>
						</table>
					</fieldset>
				</div>
				
				<div id="assignment_details">
					<fieldset>
					<legend><b>Assignment Details</b></legend>
						<table>	
							<tr>
								<td>Job Title :<span class="red_star">*</span>
								</td>
								<?php 
									$access_roles = $this->erpm->get_emp_access_roles();
								?>
								<td>
									<select name="role_id">
										<option value=Choose>Choose</option>
										<?php
											if($access_roles){
												foreach($access_roles as $role){
													$selected = set_select('role_id',$role['role_id'],($emp_details['job_title2']==$role['role_id']));
													echo '<option value="'.$role['role_id'].'" '.$selected.' >'.$role['role_name'].'</option>';					
												}
											}
										?>
								 </select>
								 
								 <?php echo form_error('job','<div class="error">','</div>')?>
								</td>
							</tr>
							<tr class="assign_under">
								<td>Assigned under :<span class="red_star">*</span></td>
								<td>
									<select name="assigned_under_id" prev_assign_under_id="<?php echo $assigned_emp_det['parent_emp_id']?>">
								    <option value="">Choose</option>
								    </select>
								
							</tr>
							
							</tr>
			
							    <tr class="inst territory">
								<td class="label">Territory</td>
								
								<?php
									$linked_terr_ids = $this->db->query("SELECT GROUP_CONCAT(territory_id*1) AS tr_ids FROM m_town_territory_link WHERE employee_id = ? AND is_active = 1;",$emp_details['employee_id'])->row()->tr_ids;
								?>
								
								<td><select class="chzn-select" style="width: 200px;" name="territory[]" multiple="multiple" prev_selected_territory_id="<?php echo $linked_terr_ids ?>">
								</select>
								
								</td>
							</tr>
							
							<tr class="inst towns">
								<td class="label">Towns</td>
								<?php
									$linked_twn_ids = $this->db->query("SELECT GROUP_CONCAT(town_id*1) AS twn_ids FROM m_town_territory_link WHERE employee_id = ? AND is_active = 1;",$emp_details['employee_id'])->row()->twn_ids;
								?>
								
								<td><select class="chzn-select" style="width: 200px;" name="town[]"  data-placeholder="select multiple towns" multiple="multiple" prev_selected_town_id="<?php echo $linked_twn_ids;?>">
								</select>
							</tr>
						</table>
					</fieldset>
				</div>
			</td>
		</tr>
		<tr>
			<td align="right"><input type="submit" value="Update">
			</td>
		</tr>
		</table>
		</form>
	</div>
</div>
</div>
</div>
<style>
	.manageListOptions{
	font-weight:bold;
	font-size:14px;
	cursor:pointer;
	background:#555;
	color:#FFF;
	padding:1px 4px;
	margin:1px;
	border-radius:5px;
	margin-left: 5px;
}
							
</style>


<script type="text/javascript">

$('#tgl_csv_cnt').change(function(){
	if($(this).attr('checked'))
	{
		$('#tgl_csv_blk').hide();
	}else
	{
		$('#tgl_csv_blk').show();
	}
}).trigger('click');
	
$('#contactList').manageList();
var employee_id = "<?php echo $emp_details['employee_id'];?>";		
var sel_role_id ='';
	$('select[name="role_id"]').change(function(){

		prev_assign_under_id = $('select[name="assigned_under_id"]').attr('prev_assign_under_id');
		sel_role_id = $(this).val();
		
		if(sel_role_id == 8)
		{
			
			$('tr.assign_under').hide();
			$('tr.territory').hide();
			$('tr.towns').hide();
			
			
		}else{
			$('tr.assign_under').show();
			if(sel_role_id == 5)
			{
				$('tr.towns').show();
			}else
			{
				$('tr.towns').hide();
			}

			if(sel_role_id == 6)
			{
				$('tr.assign_under').hide();
				$('tr.towns').show();
			}
			
				$.getJSON(site_url+'/admin/get_superior_names/'+sel_role_id,'',function(resp){
					if(resp.status == 'error'){
						alert(resp.message);
					}else{
						var emp_list_html = '<option value="">Choose</option>';
							$.each(resp.emp_list,function(i,itm){
								var selected = (prev_assign_under_id == itm.employee_id)?'selected':'';
								emp_list_html += '<option value="'+itm.employee_id+'" '+selected+' >'+itm.name+'</option>';
							});
	
							$('select[name="assigned_under_id"]').html(emp_list_html).trigger('change');
	
							
					}
				});
		}
		
		
	}).trigger('change');
	

	$('select[name="assigned_under_id"]').change(function(){
		$('select[name="territory[]"]').html('').trigger("liszt:updated");
		$('select[name="town[]"]').html('').trigger("liszt:updated");
		prev_selected_territory_id=	$('select[name="territory[]"]').attr('prev_selected_territory_id');
		var sel_sup_id = $(this).val()*1;

		var sel_role_id = $('select[name="role_id"]').val();
			
			$.getJSON(site_url+'/admin/suggest_territories/'+sel_sup_id+'/'+employee_id+'/'+sel_role_id,'',function(resp){
				if(resp.status == 'error'){
					alert(resp.message);
				}else{
					var terr_list_html = '';
					var prev_selected_territory_ids_arr = prev_selected_territory_id.split(',')
						$.each(resp.terr_list,function(i,itm){
							var selected = ($.inArray(itm.id,prev_selected_territory_ids_arr) != -1)?'selected':'';
							terr_list_html += '<option value="'+itm.id+'" '+selected+' >'+itm.territory_name+'</option>';
						});
						$('select[name="territory[]"]').html(terr_list_html);
						
						$('#'+$('select[name="territory[]"]').attr('id')+'_chzn').remove();


						if(sel_role_id > 3)
						{
							$('select[name="territory[]"]').attr({'id':'','class':'','multiple':false}).chosen();
						}
						else
						{
							$('select[name="territory[]"]').attr({'id':'','class':'','multiple':true}).chosen();
						}
						
						
						$('select[name="territory[]"]').trigger('change');
						 
			
				}
			});
	});
	


$('select[name="territory[]"]').change(function(){
	$('select[name="town[]"]').html('').trigger("liszt:updated");
	prev_selected_town_id =$('select[name="town[]"]').attr('prev_selected_town_id');
	
		var sel_terr_id = $(this).val();
			$.getJSON(site_url+'/admin/suggest_towns/'+sel_terr_id+'/'+employee_id,'',function(resp){
				if(resp.status == 'error'){
					alert(resp.message);
				}else{
					var town_list_html = '';
					var prev_selected_town_ids_arr = prev_selected_town_id.split(',')
						$.each(resp.town_list,function(i,itm){
							
							var selected = ($.inArray(itm.town_id,prev_selected_town_ids_arr) != -1)?'selected':'';
								town_list_html += '<option value="'+itm.town_id+'" '+selected+' > '+itm.town_name+'</option>';
						});
						$('select[name="town[]"]').html(town_list_html).trigger("liszt:updated");
				}
			});
	});


$(".inst_type").change(function(){
	$(".inst").hide();

	$('.inst select').html('').val('').trigger("liszt:updated");
			if($(this).val()=="3")
		{
	     $(".territory").show();
	    
	    }
		else if($(this).val()=="4")
		{		
			 $(".territory").show();
		}
	
		else if($(this).val()=="5")
		{
			$(".territory").show();
			$(".towns").show();
	    }else if($(this).val()=="6")
		{
			$(".territory").show();
			$(".towns").show();
	    }
	}).val("0").change();


$(".contact_no").live('change', function(){
	mobok1=0;
	if(!is_mobile($(this).val()))
	{
		alert("invalid mobile number");
		return;
	}
	ele = $(this);
	$.post("<?=site_url("admin/jx_checkcontact/{$emp_details['employee_id']}")?>",{contact_no:$(this).val()},function(data){
		if(data=="1")
		{	
			mobok1=1;
			ele.removeClass('mob_error');
		}
		else
		{
			alert("This mobile number is already in the system");
			ele.addClass('mob_error');
		}
			
			//alert('Already a Employee exists with given login mobile');
	});
});

$('#edit_emp').submit(function(){
	if($('.mob_error',this).length)
	{
		alert("Invalid mobile nos found");
		return false;
	}
});


<?php if(!$emp_details){?>
var mobok1=0;
var mobok2=0;
<?php }else{?>
var mobok1=1;
var mobok2=1;
<?php }?>
</script>

<style>
.red_star {
	color: rgb(205, 0, 0);
	font-size: 12px;
	font-weight: bold;
	margin-left: 5px;
}

.mob1_error,.mob2_error{
vertical-align:center;
color:red;
}
.error{color:red!important;}
</style>