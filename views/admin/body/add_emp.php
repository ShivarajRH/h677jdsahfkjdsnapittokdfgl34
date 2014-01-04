<div class="container">
	<span  style="float: right;margin:10px;"><a	href="<?php echo site_url('admin/list_employee')?>" class="fl_right">List Employee</a></span>
	<h1 class="mainbox-title">Add & Manage Employees</h1>
	
	
	<form id="formID" enctype="multipart/form-data" action="<?php echo site_url('admin/process_addemployee')?>" method="post">
			
         <table cellspacing="5" cellpadding="2" width="100%">
         <tr>
        	 <td>
         		<div width="50%" id="personal_details">
        		 <fieldset>
        		 <legend><b>Personal Details</b></legend>
        		 <table cellspacing="5" width="100%">
				<tr>
					<td>Employee Name :<span class="red_star">*</span></td>
					<td><input type="text" name="emp_name"
						value="<?php echo set_value('emp_name')?>"><?php echo form_error('emp_name','<div class="error">','</div>')?>
					</td>
				</tr>
				<tr>
					<td>Father Name :</td>
					<td><input type="text" name="father_name" 
						value="<?php echo set_value('father_name')?>">
					</td>
				</tr>

				<tr>
					<td>Mother Name :</td>
					<td><input type="text" name="mother_name" 
						value="<?php echo set_value('mother_name')?>">
					</td>
				</tr>

				<tr>
					<td>D.O.B :</td>
					<td><input type="text" id="txtDate" name="dob" value="<?php echo set_value('dob')?>">
						
					</td>
				</tr>

				<tr>
					<td>Gender : <span class="red_star">*</span></td>
					<td><select name="gender" >
							<option value="">Choose</option>
							<option value="Male" <?php echo set_select('gender','Male')?> >Male</option>
							<option value="Female" <?php echo set_select('gender','Female')?> >Female</option>
							<?php echo set_select('gender')?>
					</select><?php echo form_error('gender','<div class="error">','</div>')?>
					</td>
				</tr>

				<tr>
					<td>Qualification :</td>
					<td><input type="text" name="edu" 
						value="<?php echo set_value('edu')?>">
					</td>
				</tr>
				
				<tr>
					<td>Address : <span class="red_star">*</span></td>
					<td><textarea rows="1" style="height: 60px; width: 140px;"
							cols="25" name="address" ><?php echo set_value('address')?></textarea><?php echo form_error('address','<div class="error">','</div>')?>
					</td>
				</tr>

				<tr>
					<td>City :<span class="red_star">*</span></td>
					<td><input type="text" name="city" value="<?php echo set_value('city')?>"><?php echo form_error('city','<div class="error">','</div>')?>
					</td>
				</tr>

				<tr>
					<td>PostCode :</td>
					<td><input type="number" name="postcode" value="<?php echo set_value('postcode')?>">
						<?php echo form_error('postcode','<div class="error">','</div>')?>
					</td>
				</tr>
				
				 <tr>
					<td>Upload Image : </td>
					<td><input type="file" name="image" id="file"
						value="<?php echo set_value('image')?>">
					</td>
				</tr>

				<tr>
					<td>Upload CV : </td>
					<td><input type="file" name="cv" id="file"
						value="<?php echo set_value('cv')?>">
					</td>
				</tr>
			</table>
		</fieldset>
		</div>
		</td>
		
		<td>				
			<div id="contact_details">
			<fieldset>
			<legend><b>Contact Details</b></legend>
			<table width="100%">
				<tr>
				<td>
					<tr>
						<td>Email Id : </td>
						<td><input type="email" name="email_id" 
							value="<?php echo set_value('email_id')?>"><?php echo form_error('email_id','<div class="error">','</div>')?>
						</td>
					</tr>
					
					<tr>
						<td>Contact no : <span class="red_star">*</span></td>
						<td>
							<ol id="contactList">
								<?php 
									$p_contact_nos = $this->input->post('contact_no');
									if(!$p_contact_nos){
								?>
									<li><input type="text" name="contact_no[]" id="contact_no" class="clearContent" value="<?php echo set_value('contact_no')?>" maxlength="10"></li>
								<?php }else{
										for($c=0;$c<count($p_contact_nos);$c++)
										{
								?>
										<li>
											<input type="text" name="contact_no[]" id="contact_no" class="clearContent" value="<?php echo set_value('contact_no['.$c.']')?>" maxlength="10">
											<?php echo form_error('contact_no['.$c.']','<span class="error">','</span>')?>
										</li>
								<?php
										} 
									} 
								?>	
								<span id="mob1_error"></span>
							</ol>
						</td>
					</tr>
				</td>
			</tr>
			</fieldset>
			</table>
			</div>
			
			<div id="assignment_details">
				<fieldset>
				<legend><b>Assignment Details</b></legend>
					<table>
						<tr>                                                                      
							<td>Job Title : <span class="red_star">*</span></td>
							<td><select name="role_id" class="inst_type">
									<option value="">Choose</option>
									<?php if($access_roles){
										foreach ($access_roles as $roles){
									?>
									<option <?php echo set_select('role_id',$roles['role_id']);?>
										value="<?php echo ($roles['role_id']);?>">
										<?php echo $roles['role_name'];?>
									</option>
									<?php }
									}?>
							</select> <?php echo form_error('role_id','<div class="error">','</div>')?>
							</td>
						</tr>
						<tr>
							<td>Has Login</td>
							<td>
								<input type="checkbox" value="1" name="has_login" <?php echo set_checkbox('has_login',1) ?>  />
								<div id="login_details_blk" style="padding:5px;background: #f7f7f7;display: none;">
									<b>Employee Login Details</b><br>
									<div>Username : <input class="inp" type="text" name="username" value="<?php echo set_value('username')?>">
										<?php echo form_error('username') ?>
									</div>
									<div>Password : <input class="inp" type="password" name="password" value="<?php echo set_value('password')?>">
										<?php echo form_error('password') ?>
									</div>
								</div>	
							</td>
						</tr>
						<tr id="assigned_under">
							<td>Assigned under :<span class="red_star">*</span></td>
							<td><select name="assigned_under_id"></select></td>
						</tr>
						<tr class="inst territory">
							<td class="label">Territory : <span class="red_star">*</span></td>
							<td>
								<select class="chzn-select" style="width: 200px;" name="territory[]" ></select>
							</td>
						</tr>
				 		
						<tr id="all_territory"><td >Territory :<span class="red_star">*</span></td>
							<td>
							<div>
							<select name="territory[]" class="chzn-select" style="width: 200px;">
							<option value=""></option>
							<?php $tr=$this->db->query("select * from pnh_m_territory_info order by territory_name asc")->result_array();?>
							<?php foreach($tr as $t){?>
							<option value="<?php echo $t['id']?>"><?php echo $t['territory_name']?></option>
							<?php }?>
							</select>
							</div>
						</td></tr>
						
						<tr class="inst towns">
								<td class="label">Towns :<span class="red_star">*</span></td>
								<td><select class="chzn-select" style="width: 200px;" name="town[]" multiple="multiple">
								     <option value="0">Choose</option>
								</select>
					              </td>
						</tr>
					</table>
				</fieldset>	
			</div>
		</td>
	</tr>

				<tr>
					<td><input  type="submit" value="Add Employee" align="right" class="myButton">
					</td>
				</tr>
			</table>
		</form>
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

$('#contactList').manageList();

$('input[name="has_login"]').change(function(){
	if($(this).attr('checked'))
	{
		$('#login_details_blk').show();
	}else
	{
		$('#login_details_blk').hide();
	}
}).trigger('change');
							
		
var sel_role_id =0;
	$('select[name="role_id"]').change(function(){
		$('select[name="assigned_under_id"]').html('');
		 sel_role_id = $(this).val();
			$.getJSON(site_url+'/admin/get_superior_names/'+sel_role_id,'',function(resp){
				if(resp.status == 'error'){
					alert(resp.message);
				}else{
					var emp_list_html = '<option value="">Choose</option>';
						$.each(resp.emp_list,function(i,itm){
							emp_list_html += '<option value="'+itm.employee_id+'">'+itm.name+'</option>';
						});
						$('select[name="assigned_under_id"]').html(emp_list_html);
				}
			});
	});

		$('select[name="assigned_under_id"]').change(function(){
			
			$('select[name="territory[]"]').html('');
			var sel_sup_id = $(this).val();
				 
				
				$.getJSON(site_url+'/admin/suggest_territories/'+sel_sup_id,'',function(resp){
					if(resp.status == 'error'){
						alert(resp.message);
					}else{
						var terr_list_html = '';
						 
							
							$.each(resp.terr_list,function(i,itm){
								terr_list_html += '<option value="'+itm.id+'">'+itm.territory_name+'</option>';
							});
							$('select[name="territory[]"]').html(terr_list_html);
							
							$('#'+$('select[name="territory[]"]').attr('id')+'_chzn').remove();


							if(sel_role_id > 3)
							{
								$('select[name="territory[]"]').attr({'id':'','class':'','multiple':false}).val('').chosen();
							}
							else
							{
								$('select[name="territory[]"]').attr({'id':'','class':'','multiple':true}).val('').chosen();
							}
							
							
							$('select[name="territory[]"]').trigger('change');
					}
				});
		});
			
      $('select[name="territory[]"]').change(function(){
          
          $('select[name="town[]"]').html('').trigger("liszt:updated");
          if(sel_role_id > 3){
				sel_sup_id = 0;
		var sel_territory_id = $(this).val();
			$.getJSON(site_url+'/admin/suggest_towns/'+sel_territory_id,'',function(resp){
				if(resp.status == 'error'){
					alert(resp.message);
				}else{
					var town_list_html = '';
					
						$.each(resp.town_list,function(i,itm){
							town_list_html += '<option value="'+itm.town_id+'">'+itm.town_name+'</option>';
						});
						$('select[name="town[]"]').html(town_list_html).trigger("liszt:updated");
				}
			});
          }
	});

  $(".inst_type").change(function(){
		$(".inst").hide();
		$('#assigned_under').show();
		$("#all_territory").hide();
		if($(this).val()<="5")
		{
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
		    }
		}

		if($(this).val()>="6" && $(this).val()!="8")
		{
			//$(".inst").hide();
			$('#assigned_under').hide();
			$("#all_territory").show();
			$(".towns").show();
		} 
			
		
	}).val("0").change();


									  
	$('#add_contact').click(function(e){
		e.preventDefault();
		$('#add_contact').append('<li><input type="text" name="contact_no[]" value=""> <a href="javascript:void(0)" class="remove_btn">X</a></li>');
		  $('.remove_btn').unbind('click').click(function(e){
			  e.preventDefault();
			  $(this).parent().remove();
		  });
		});

	$(function () {
		$("#txtDate").datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: '1970:1995'
		});

		$(".chzn-select").chosen({no_results_text: "No results matched"}); 

	});

	$("#contact_no").live('change', function(){
		mobok1=0;
		if(!is_mobile($(this).val()))
		{
			alert("invalid mobile number");
			return;
		}
		$.post("<?=site_url("admin/jx_checkcontact")?>",{contact_no:$(this).val()},function(data){
			if(data=="1")
			{	
				mobok1=1;
				$("#mob1_error").html("Ok").css("color","green");
				//alert('Ok');
			}
			else
				$("#mob1_error").html("This mobile number is already in the system").css("color","red");
				//alert('Already a Employee exists with given login mobile');
		});
	});
</script>

<style>
.red_star {
	color: rgb(205, 0, 0);
	font-size: 12px;
	font-weight: bold;
	margin-left: 5px;
}

#add_contact li{margin: 5px 0px;}
#add_contact .add_btn{font-size: 12px;text-decoration: none;color: #000;font-weight: bold;}
#add_contact .remove_btn{ color: rgb(205, 0, 0);font-size: 12px;  font-weight: bold;margin-left: 5px;text-decoration: none;}
#add_contact .inputbox{width: 100px;} 

#mob1_error,#mob2_error{
vertical-align:center;
color:red;
}
.error{color:red!important;}
</style>  

 

 




