<div class="container">
<h2>Member Subscription Plan</h2>
<h4 id="dateshowed" style="color: red;"></h4>
<div class="listpage">
	<div >
	<form method="post" id="member_plan_detail" action="<?php echo site_url('admin/add_memberplan_detail');?>"  data-validate="parsley">
			<table  cellpadding="7">		
				<tbody>
					<tr>
						<td>Mobile Number<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<input type="text"  name="mobnumber" maxlength="10"    id="mobnumberValidate" value="<?php echo set_value('mobnumber'); ?>" ><span class="erroemsg" style="color: red;"> <?php if(validation_errors()) { echo form_error('mobnumber');}?></span>
						</td>
							<td>Franchise name:</td>
						<td colspan="2">
							<select name="sfranchiseid" id="sfranchiseid">
							<option value="">Franchise Name</option>
							<?php foreach($franchise_list as $flist) { ?>
							<option value="<?php echo $flist['franchise_id']?>"><?php echo $flist['franchise_name']?> - <?php echo $flist['city']?></option>
							<?php } ?>
							</select>
						</td>
						<td>Franchise ID/Mob No:</td>
						<td colspan="2">
							<input type="text"  maxlength="10" name="fmobnumber"  id="fidmobnumber" ><span class="mobnoerror" style="color: red;"></span>
						</td>
						</tr>
						<tr>
						<td>Member ID<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<input type="text"   name="memberid" maxlength="8"    id="memberidValidate"><input type="hidden" id="existmemberval" name="existmemberval"><span class="erroemsg1" style="color: red;"><?php if(validation_errors()) { echo form_error('memberid');}?></span>
						</td>
						<td rowspan="7" colspan="6" style="background-color:lightyellow; display:none; width:63%;" id="franchiseDetails">
						 <table  border="0">
						  <tr>					      
					        <td colspan="2" align="center"><h4>Franchises Detail</h4></td>						        
					    </tr>
					    <tr>					  
					        <td>Franchise Name:</td>
					        <td id="franchisename"></td>					       
					    </tr>
					     <tr>					    
					        <td>Address:</td>
					        <td id="franchiseaddress"></td>
					    </tr>
					    <tr>					    
					        <td>Locality:</td>
					        <td id="franchiselocality"></td>
					    </tr>
					    <tr>					    
					        <td>City:</td>
					        <td id="franchisecity"></td>
					    </tr>
					    <tr>					    
					        <td>Postcode:</td>
					        <td id="franchisepostcode"></td>
					    </tr>
					    <tr>					    
					        <td>State:</td>
					        <td id="franchisestate"></td>
					    </tr>
					</table>
						</td>
					</tr>
					<tr>
						<td >Member Name<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<input type="text"  class="disabletext" name="mname"  id="membername"  ><span style="color: red;"> <?php if(validation_errors()) { echo form_error('mname');}?></span>
						</td>
					</tr>
					<tr>
						<td>Email :</td>
						<td colspan="2">
						<input type="text"  class="disabletext" name="memail" size=15 id="memail" >
						</td>
						
					</tr>
					<tr>
						<td>DOB <font size="4" color="red">*</font>:</td>
						<td colspan="2">
						<input type="text"  class="disabletext" name="mdob" size=15 id="mdob" ><span style="color: red;"> <?php if(validation_errors()) { echo form_error('mdob');}?></span>(dd/mm/yyyy)
						</td>
						
					</tr>
					<tr>
						<td>Age <font size="4" color="red">*</font>:</td>
						<td colspan="2">
						<input type="text"  class="disabletext" name="mage" maxlength="3" size=15 id="mage"  ><span style="color: red;"> <?php if(validation_errors()) { echo form_error('mage');}?></span>
						</td>
						
					</tr>
					
					<tr>
						<td>Gender<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<input type="radio"   name="gender" id="maleval" value="0"><label>Male</label>
			        		<input type="radio"   name="gender" id="femaleval" value="1" ><label>Female</label>
						</td>
					</tr>
					
					<tr>
						<td>Town<font size="4" color="red">*</font>:</td>
						<td colspan="2">
						<input type="text" class="disabletext" name="town" id="townval" ><span style="color: red;"> <?php if(validation_errors()) { echo form_error('town');	}?></span>
						</td>
					</tr>
					<tr>
						<td>Address<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<textarea  class="disabletext" name="address" id="address" ></textarea><span style="color: red;"> <?php if(validation_errors()) { echo form_error('address');	}?></span>
						</td>
					</tr>
					<tr>
						<td>Pincode<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<input type="text" class="disabletext" name="pincode" maxlength="6" id="pincode" ><span style="color: red;"> <?php if(validation_errors()) { echo form_error('pincode');	}?></span>
						</td>
					</tr>
					<tr>
						<td>How many members are <br>there in your family:</td>
						<td>
							<input type="text" name="memcount" id="memcount"><span style="color: red;"> <?php if(validation_errors()) { echo form_error('memcount');	}?></span>
						</td>
						<td id="refere_field">
						</td>
					</tr>
					<tr>
						<td colspan="3">
							 <table id="paymentDetails" border="0">
					    <tr>
					      
					        <th>Paymentvia</th>
					        <th>Payment Details </th>
					        <th>Remarks </th>
					    </tr>
					  <!--   <tr>
					       
					        <td><select name="payment_via"><option value="cash">Cash</option><option value="neft">Neft</option><option value="cheque">Cheque</option></select></td>
					        <td><input type="text" name="paymentdetail"   ></td>
					        <td><input type="text" name="premarks"   ></td>
					    </tr> -->
					</table>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<div class="add-button " align="right">+</div>
							 <table id="MemberDetails" border="0">
					    <tr>
					        <!-- <th>No.</th>-->
					        <th>Name Your Family members</th>
					        <th>Relationship </th>
					        <th>Age </th>
					    </tr>
					    <tbody id="memberstatic">
					    <tr>
					        <!-- <td><input type="text" id="memcount" size="5"  data-required="true"></td> -->
					        <td><input type="text" name="fmname[]" size="30" ></td>
					        <td><input type="text" name="fmrelation[]"  ></td>
					        <td><input type="text" maxlength="3" name="fmage[]" size="5" ></td>
					    </tr>
					    </tbody>
					</table>
						</td>
					</tr>
					<tr style="background-color:lightgreen;">
						<td>Select your Subsciption Plan  for Family  Combo s<font size="4" color="red">*</font>:</td>
						<td>
							<?php foreach($plan_list as $i=>$list) { ?>
							<label style="padding:5px;margin:0px 3px;"><input type="radio"  name="planval" class="planval" value="<?php echo $list['id']; ?>"  <?=$list?($i==0?"checked":""):""?>> <?php echo $list['plan_name']; ?></label>
							<?php  } ?>
						<span style="color: red;"> <?php if(validation_errors()) { echo form_error('planval');	}?></span></td>
					</tr>
					<tr style="background-color:lightgreen;">
						<td>Select your Storeking Family  Combos with </br>Subsciption Fee, valid for a period of 5 months<font size="4" color="red">*</font>:</td>
						<td id="currentval"><?php foreach($plan_val as $i=>$p)
									{ ?>
							<label style="padding:5px;margin:0px 3px;"><input type="radio"  name="subscribeval" class="subscribeval" value="<?php echo $p['id'];?>" <?=$p?($i==0?"checked":""):""?>><?php echo $p['plan_amount'];?></label>
	    				<?php } ?></td>
						<td id="plan_amt" style="display:none;"><span style="color: red;"> <?php if(validation_errors()) { echo form_error('subscribeval');	}?></span></td>
					</tr>	
					<tr>
						<td colspan="3">
							<div align="right"><input type="submit" name="submit" value="Submit" class="dark-button"></div>
						</td>
					</tr>
					
				</tbody>
			</table>
		</form>
	</div>
</div>

</div>



<script>
	$(function (){
	$('.add-button').click(function(){
		$('#MemberDetails').append(' <tr><td><input type="text" name="fmname[]" size="30"  data-required="true"></td><td><input type="text" name="fmrelation[]"   data-required="true"></td><td><input type="text" name="fmage[]" size="5" maxlength="3"  data-required="true"></td><td><div class="remove-button">-</div></td></tr>');
		
	});
	$( "#mobnumberValidate" ).keyup();
	});

//remove the parameter
$(function(){
	$('.remove-button').live("click",function(){
		$(this).closest("tr").remove();
	});
});
$('input[name="planval"]').change(function() {
	$('#currentval').html('');
     var checked="";
	$.ajax ({
        'dataType': 'json',
        'type'    : 'POST',
        'url'     : site_url+"admin/jx_member_plan_value",
        'data'    : "planid="+$(this).val(),
        "success" : function(resp) {
		 var htmlval='';
		  $.each(resp.val, function (index, data){	
	             if(index==0)
		          checked = "checked='checked'";   
			  htmlval +='<input type="radio"  name="subscribeval" class="subscribeval" value="'+data.id+'">' +data.amt+' </label>';
             });
				$("#currentval").html(htmlval).show();
        }
	});
});

$('#sfranchiseid').change(function() {
	if(this.value !=''){
    $.ajax
    ({
      'dataType': 'json',
      'type'    : 'POST',
      'url'     : site_url+"admin/jx_franchisee_mobidno_detail",
      'data'    : "franchiseid="+this.value,
      "success" : function(resp) {
	             if(resp.dataval=="2")
	             {
		             var fval= resp.franidval;
	            	 $('#fidmobnumber').val(fval.fmobno);
	             }
     		 }
    });
	}
});

$('#fidmobnumber').bind('keyup',function () { 
    this.value = this.value.replace(/[^0-9\.]/g,'');
    $(".mobnoerror" ).empty();
    if(this.value.length =='10' || this.value.length =='8')
    {
    	 $.ajax
         ({
           'dataType': 'json',
           'type'    : 'POST',
           'url'     : site_url+"admin/jx_franchisee_mobno_validate",
           'data'    : "mobid="+this.value,
           "success" : function(response) {
        	      $(".mobnoerror" ).empty();
	              if(response.dataval=="0")
	              {
	                  $(".mobnoerror").append("This  number is not valid");               
	              }else if(response.dataval=="2")
	              {
	            	  var franresp = response.franidval;
	            	  $("#sfranchiseid").val(franresp.fid).attr('selected', true);
	            	 
	              }
            }
         });
    }
});

$('#mobnumberValidate').bind('keyup',function () { 
    this.value = this.value.replace(/[^0-9\.]/g,'');
    $(".erroemsg" ).empty();
    $(".erroemsg1" ).empty();
    $("#dateshowed").empty(); 
    $('.disabletext').removeAttr('disabled');
    $(".disabletext").val('');
    $("#memberidValidate").val('');
    $("#sfranchiseid").val('');
    $("#fidmobnumber").val('');
    $("#franchiseDetails").hide(); 
    $("#existmemberval").val('');
    if(this.value.length==10)
    {
    	 $.ajax
         ({
           'dataType': 'json',
           'type'    : 'POST',
           'url'     : site_url+"admin/jx_member_mobno_validate",
           'data'    : "mobno="+this.value,
           "success" : function(response) {
	              if(response.dataval=="0")
	              {
	            	  $(".erroemsg" ).empty();
	                  $(".erroemsg").append("This mobile number is not valid");
	                  $(".disabletext").attr('disabled', '');
	                 
	              }else 
	              {
	            	  $('.disabletext').removeAttr('disabled');
	            	  $(".erroemsg" ).empty();
	            	  $("#dateshowed").html('');
	            	  if(response.dataval=="2")
	            	  {        
	            		   $(".erroemsg" ).show();
	            		   $("#dateshowed").append(response.addeddate);
	            	  $(".erroemsg").append("Already you have one plan");	   
	            	  $(".disabletext").attr('disabled', '');
		            	   $("#existmemberval").val('1');
	            	  }    
	            	  
		              $("#franchiseDetails").show();
	            	  var memresp = response.memberdetail;
	            	  $("#mobnumberValidate").val(memresp.mmobile);
	            	  $("#memberidValidate").val(memresp.memid);
	            	  $("#membername").val(memresp.mname);
	            	  $("#memail").val(memresp.memail);
	            	  $("#mdob").val(memresp.mdob);
	            	  $("#mage").val(memresp.mage);
	            	  if(memresp.mgender=="0")
	            		  $('input:radio[name=gender][value=0]').attr('checked', true);
	            	  else
	            		  $('input:radio[name=gender][value=1]').attr('checked', true);
	            	  $("#townval").val(memresp.mcity);
	            	  $("#address").val(memresp.maddress);
	            	  $("#pincode").val(memresp.mpincode);
	            	  $("#franchisename").text(memresp.fname);
	            	  $("#franchiseaddress").text(memresp.faddress);
	            	  $("#franchiselocality").text(memresp.flocality);
	            	  $("#franchisecity").text(memresp.fcity);
	            	  $("#franchisepostcode").text(memresp.fpostcode);
	            	  $("#franchisestate").text(memresp.fstate);
	            	  var sfranchiseid  = document.getElementById("sfranchiseid").value;  
	            	  if(sfranchiseid=="")
	            	  {
	            		  $("#sfranchiseid").val(memresp.fid).attr('selected', true);
	            		  $('#fidmobnumber').val(memresp.fmobno);
	            	  }
	            	  var memfamresp = response.m_familydetail;
	            	  if(memfamresp.length >'0'){
	            	  $('#memberstatic').empty();
    					 $.each(memfamresp, function (index, data) {					           	
    						 $('#memberstatic').append(' <tr><td><input type="text" name="fmname[]" size="30" value="'+data.fmname+'"  data-required="true"></td><td><input type="text" name="fmrelation[]"   data-required="true" value="'+data.fmrelation+'"></td><td><input type="text" name="fmage[]" size="5" value="'+data.fmage+'" maxlength="3" data-required="true"></td><td><input type="hidden" name="fmid[]" value="'+data.fmid+'" ></td></tr>');
						});
	            	  }
	            	  
	              }
	              
            }
         });
    }
});

$('#memberidValidate').bind('keyup',function () { 
    this.value = this.value.replace(/[^0-9\.]/g,'');
    $(".erroemsg1" ).empty();
    $(".erroemsg" ).empty();
    $("#dateshowed").empty();
    $('.disabletext').removeAttr('disabled');
    $(".disabletext").val('');
    $("#mobnumberValidate").val('');
    $("#sfranchiseid").val('');
    $("#fidmobnumber").val('');
    $("#franchiseDetails").hide(); 
    $("#existmemberval").val('');
    if(this.value.length==8)
    {
    	 $.ajax
         ({
           'dataType': 'json',
           'type'    : 'POST',
           'url'     : site_url+"admin/jx_member_mobno_validate",
           'data'    : "memerid="+this.value,
           "success" : function(response) {             
	              if(response.dataval=="0")
	              {
	            	  $(".erroemsg1" ).empty();
	                  $(".erroemsg1").append("This member id is not valid");
	                  $(".disabletext").attr('disabled', '');
	                  
	              }else 
	              {
	            	  $('.disabletext').removeAttr('disabled');
	            	  $(".erroemsg1" ).empty();
	            	  $("#dateshowed").empty();
	            	  if(response.dataval=="2")
		              {
	            		   $(".erroemsg1" ).show();
	            		   $("#dateshowed").append(response.addeddate);
		            	   $(".erroemsg1").append("Already you have one plan"); 
		            	   $(".disabletext").attr('disabled', '');
		            	   $("#existmemberval").val('1');
		              }
	            	  $("#franchiseDetails").show();
	            	  var memresp = response.memberdetail;
	            	  $("#mobnumberValidate").val(memresp.mmobile);
	            	  $("#memberidValidate").val(memresp.memid);
	            	  $("#membername").val(memresp.mname);
	            	  $("#memail").val(memresp.memail);
	            	  $("#mdob").val(memresp.mdob);
	            	  $("#mage").val(memresp.mage);
	            	  if(memresp.mgender=="0")
	            		  $('input:radio[name=gender][value=0]').attr('checked', true);
	            	  else
	            		  $('input:radio[name=gender][value=1]').attr('checked', true);
	            	  $("#townval").val(memresp.mcity);
	            	  $("#address").val(memresp.maddress);
	            	  $("#pincode").val(memresp.mpincode);
	            	  $("#franchisename").text(memresp.fname);
	            	  $("#franchiseaddress").text(memresp.faddress);
	            	  $("#franchiselocality").text(memresp.flocality);
	            	  $("#franchisecity").text(memresp.fcity);
	            	  $("#franchisepostcode").text(memresp.fpostcode);
	            	  $("#franchisestate").text(memresp.fstate);

	            	  var sfranchiseid  = document.getElementById("sfranchiseid").value;  
	            	  if(sfranchiseid=="")
	            	  {
	            		  $("#sfranchiseid").val(memresp.fid).attr('selected', true);
	            		  $('#fidmobnumber').val(memresp.fmobno);
	            	  }
	            	  
	            	  var memfamresp = response.m_familydetail;
	            	  if(memfamresp.length >'0'){
	            	  $('#memberstatic').empty();
    					 $.each(memfamresp, function (index, data) {					           	
    						 $('#memberstatic').append(' <tr><td><input type="text" name="fmname[]" size="30" value="'+data.fmname+'"  data-required="true"></td><td><input type="text" name="fmrelation[]"   data-required="true" value="'+data.fmrelation+'"></td><td><input type="text" name="fmage[]" size="5" value="'+data.fmage+'"  data-required="true"></td><td><input type="hidden" name="fmid[]" value="'+data.fmid+'" ></td></tr>');
						});
	            	  }
	            	  
	              }
	              
            }
         });
    }
});
$('#memcount').bind('keyup',function () { 
	 this.value = this.value.replace(/[^0-9\.]/g,'');
});
$("#member_plan_detail").submit(function(){
	 $("#dateshowed").empty(); 
	 var fmname_cnt = [];
	 var planval =  $("input[name=planval]:checked").val();
	 var subscribeval =  $("input[name=subscribeval]:checked").val();
	 var mobnumberValidate = $( "#mobnumberValidate" ).val();
	 var memberidValidate = $( "#memberidValidate" ).val();
	 var membername = $( "#membername" ).val();
	 var mage = $( "#mage" ).val();
	 var datevalue = $( "#mdob" ).val();
	 var mgender =  $("input[name=gender]:checked").val();
	 var townval = $( "#townval" ).val();
	 var address = $( "#address" ).val();
	 var pincode = $( "#pincode" ).val();
	 var existmemval = $( "#existmemberval" ).val();
	 var familycount = $( "#memcount" ).val();
	 var validformat=/^\d{2}\/\d{2}\/\d{4}$/ ;//Basic check for format validity
	 if(familycount=="")
		 familycount = 0;
	 $('input[name="fmname[]"]').each(function() {
			 if($(this).val()!="")
			 fmname_cnt.push($(this).val());
	    });
	    if(mobnumberValidate=="")
	    {
	    	$('#dateshowed').append('Please fill Mobil Number');
	    	return false;
	    }
	    else if(memberidValidate=="")
	    {
	    	$('#dateshowed').append('Please fill Member Id');
	    	return false;
	    }
	    else if(membername=="")
	    {
	    	$('#dateshowed').append('Please fill member Name');
	    	return false;
	    }
	    else if(mgender=="")
	    {
	    	$('#dateshowed').append('Please select Gender');
	    	return false;
	    }
	    else if(mage=="")
	    {
	    	$('#dateshowed').append('Please fill Age');
	    	return false;
	    }
	    else if(townval=="")
	    {
	    	$('#dateshowed').append('Please fill Town');
	    	return false;
	    }
	    else if(address=="")
	    {
	    	$('#dateshowed').append('Please fill Address');
	    	return false;
	    }
	    else if(pincode=="")
	    {
	    	$('#dateshowed').append('Please fill Pincode');
	    	return false;
	    }
	    else if(familycount != fmname_cnt.length)
		{
	    	$('#dateshowed').append("Family Member count value and family member details not equal.");
		 return false; 
	 	}
	 	else if (!validformat.test(datevalue))
		{
	 		$('#dateshowed').append("Invalid Date Format. Please correct and submit again.");
			return false;
		}
		else if(planval =='')
		{
			alert("Please select one plan.");
			return false;
		}
		else if(subscribeval == undefined)
		{
			alert("Please select one Subscription value.");
			return false;
		}else if(existmemval=='1')
		{
			alert("Already you have one plan.");
			return false;
		}
		else
		{
			
			 //Detailed check for valid date ranges
			var monthfield=datevalue.split("/")[1];
			var dayfield=datevalue.split("/")[0];
			var yearfield=datevalue.split("/")[2];
			var dayobj = new Date(yearfield, monthfield-1, dayfield);
			if ((dayobj.getMonth()+1!=monthfield)||(dayobj.getDate()!=dayfield)||(dayobj.getFullYear()!=yearfield))
				{
				$('#dateshowed').append("please fill the correct date.");
					return false;
		  		 }
				else
				{
			 $('.disabletext').removeAttr('disabled');
			return true;
		}
			
		}

});
</script>
<style>
/** PAGE CSS**/
.listpage{
	<!--width:1000px;
	margin: 0 auto; -->
	position:relative;
}
.listpage-title{
	font-size: 18px;
	margin-top:10px;
	}
.listpage-totalitems{font-size: 13px;margin-right: 10px;}

/** Button CSS **/
.button{font-size: 11px;font-weight: bold;background: #e3e3e3;color: #676767;padding:4px 10px;text-decoration: none;text-transform: uppercase;}
.dark-button{background: #676767;color: #f3f3f3}
.dark-button:hover{background: #676767;color:#FFF}

.light-button{background: #f3f3f3;color: #676767}
.light-button:hover{background: #f3f3f3;color:#FFF}

input[type="text"],input[type="password"],select,textarea,file{
	font-size: 13px;
	padding:5px 2px;
	min-width: 200px;
	border:1px solid #cdcdcd;
	background: #fefefe;
}

input[type="password"],input[type="file"]{
	font-size: 13px;
	padding:5px 2px;
	min-width: 200px;
	border:1px solid #cdcdcd;
	background: #fefefe;
}
/** block CSS **/
.block_300{width: 300px;}
.block_100{width: 100px;}

.min_block{margin-top:40;width:"500";height:"500";}

.left{
	float:left;
}
.right{
	float:right
}

.error_inp {
	color: #cd0000;
}

.add-button{
	text-align:center;
	width:20px;
	background: #676767;
	color: #f3f3f3;
	cursor: pointer;
	font-weight:bolder;
	font-style:bold;
}

.remove-button{
	text-align:center;
	width:20px;
	background: #676767;
	color: #f3f3f3;
	cursor: pointer;
	font-weight:bolder;
	font-style:bold;
}

.add-button:hover{
	background: #676767;
	color:#FFF;
}
.remove-button:hover{
	background: #676767;
	color:#FFF;
}
/** Button CSS **/
.button{font-size: 11px;font-weight: bold;background: #e3e3e3;color: #676767;padding:4px 10px;text-decoration: none;text-transform: uppercase;}
.dark-button{background: #676767;color: #f3f3f3}
.dark-button:hover{background: #676767;color:#FFF}

.light-button{background: #f3f3f3;color: #676767}
.light-button:hover{background: #f3f3f3;color:#FFF}

input[type="text"],input[type="password"],select,textarea,file{
	font-size: 13px;
	padding:5px 2px;
	min-width: 200px;
	border:1px solid #cdcdcd;
	background: #fefefe;
}

</style>
<?php
