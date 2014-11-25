<div class="container">
<h2>Member Feedback Detail</h2>
<div class="listpage">
	<div >
		<form method="post" id="member_feedback_detail" action="<?php echo site_url('admin/member_add_feedback_detail');?>" data-validate="parsley">
			<table  cellpadding="7" id="plan_table">		
				<tbody>
					<tr>
						<td>Mobile Number<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<input type="text"  name="mobnumber" maxlength="10"    id="mobnumberValidate" value="<?php echo set_value('mobnumber'); ?>" ><span class="erroemsg" style="color: red;"> <?php if(validation_errors()) { echo form_error('mobnumber');}?></span>
						</td>
						<td rowspan="7" colspan="7" style="background-color:lightyellow; display:none; width:50%;" id="franchiseDetails">
						 <table  border="0" style="width:80%">
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
						<td>Member ID<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<input type="text"  name="memberid" maxlength="8"    id="memberidValidate"><span class="erroemsg1" style="color: red;"> <?php if(validation_errors()) { echo form_error('memberid');}?></span>
						</td>
						
					</tr>
					<tr>
						<td >Member Name<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<input type="text"  class="diabletext" name="mname"  id="membername"  ><span style="color: red;"> <?php if(validation_errors()) { echo form_error('mname');}?></span>
						</td>
					</tr>
					<tr>
						<td>DOB <font size="4" color="red">*</font>:</td>
						<td colspan="2">
						<input type="text"  class="diabletext" name="mdob" size=15 id="mdob" ><span style="color: red;"> <?php if(validation_errors()) { echo form_error('mdob');}?></span>(dd/mm/yyyy)
						</td>
						
					</tr>
					<tr>
						<td>Age<font size="4" color="red">*</font> :</td>
						<td colspan="2">
						<input type="text"  class="diabletext" name="mage" maxlength="3" size=15 id="mage"  ><span style="color: red;"> <?php if(validation_errors()) { echo form_error('mage');}?></span>
						</td>
						
					</tr>
					
					<tr>
						<td>Gender<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<input type="radio"  name="gender" id="maleval" value="0"><label>Male</label>
			        		<input type="radio"  name="gender" id="femaleval" value="1" ><label>Female</label><span style="color: red;"> <?php if(validation_errors()) { echo form_error('gender');}?></span>
						</td>
					</tr>
					
					<tr>
						<td>Town<font size="4" color="red">*</font>:</td>
						<td colspan="2">
						<input type="text" class="diabletext" name="town" id="townval"   ><span style="color: red;"> <?php if(validation_errors()) { echo form_error('town');	}?></span>
						</td>
					</tr>
					<tr>
						<td>Address<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<textarea  name="address" class="diabletext" id="address" ></textarea><span style="color: red;"> <?php if(validation_errors()) { echo form_error('address');	}?></span>
						</td>
					</tr>
					<tr>
						<td>Pincode<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<input type="text" name="pincode" id="pincode" maxlength="6"><span style="color: red;"> <?php if(validation_errors()) { echo form_error('pincode');	}?></span>
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
							<div class="add-button " align="right">+</div>
							 <table id="MemberDetails" border="0">
					    <tr>
					      
					        <th>Name Your Family members</th>
					        <th>Relationship </th>
					        <th>Age </th>
					    </tr>
					    <tbody id="memberstatic">
					    <tr >
					       
					        <td><input type="text" name="fmname[]" size="30" ></td>
					        <td><input type="text" name="fmrelation[]"  ></td>
					        <td><input type="text" class="fmage" name="fmage[]" size="5" maxlength="3" ></td>
					    </tr>
					    </tbody>
					</table>
						</td>
					</tr>
					
					<tr>
						<td colspan="2" style="background-color:lightyellow;">					
						 <table id="ProductdetailsDetails" border="0">
						    <tr>
						        <th colspan="2">What would you want to buy in the next 30 days for yourself?<img src="<?php echo site_url(); ?>images/together.png" style='margin-left:10px'></th>
						    </tr>
						    <tr>					       
						        <td>Product Name</td>
						        <td><input type="text" size="75" name="productname[]"  ><input type="hidden"  name="feedbackid[]" value="1"  ></td>					       
						    </tr>
						     <tr>					       
						        <td>Price Name</td>
						        <td><input type="text" size="75" name="productprice[]"  ></td>					       
						    </tr>
						     <tr>					       
						        <td>Brand Name</td>
						        <td><input type="text" size="75" name="brandname[]"  ></td>					       
						    </tr>
						     <tr>					       
						        <td>Color</td>
						        <td><input type="text" size="75" name="colorname[]"  ></td>					       
						    </tr>
				           </table>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="background-color:lightyellow;">					
						 <table id="ProductdetailsDetails" border="0">
						    <tr>
						        <th colspan="2">What would you want to buy in the next 30 days for your home?<img src="<?php echo site_url(); ?>images/home.png" style='margin-left:10px'></th>
						    </tr>
						    <tr>					       
						        <td>Product Name</td>
						        <td><input type="text" size="75" name="productname[]"  ><input type="hidden"  name="feedbackid[]" value="2"  ></td>					       
						    </tr>
						     <tr>					       
						        <td>Price Name</td>
						        <td><input type="text" size="75" name="productprice[]"  ></td>					       
						    </tr>
						     <tr>					       
						        <td>Brand Name</td>
						        <td><input type="text" size="75" name="brandname[]"  ></td>					       
						    </tr>
						     <tr>					       
						        <td>Color</td>
						        <td><input type="text" size="75" name="colorname[]"  ></td>					       
						    </tr>
				           </table>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="background-color:lightyellow;">					
						 <table id="ProductdetailsDetails" border="0">
						    <tr>
						        <th colspan="2">What would you wish to gift someone in the next 30 days?<img src="<?php echo site_url(); ?>images/gift-wrap-icon.png" style='margin-left:10px'></th>
						    </tr>
						    <tr>					       
						        <td>Product Name</td>
						        <td><input type="text" size="75" name="productname[]"  ><input type="hidden"  name="feedbackid[]" value="3"  ></td>					       
						    </tr>
						     <tr>					       
						        <td>Price Name</td>
						        <td><input type="text" size="75" name="productprice[]"  ></td>					       
						    </tr>
						     <tr>					       
						        <td>Brand Name</td>
						        <td><input type="text" size="75" name="brandname[]"  ></td>					       
						    </tr>
						     <tr>					       
						        <td>Color</td>
						        <td><input type="text" size="75" name="colorname[]"  ></td>					       
						    </tr>
				           </table>
						</td>
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
		$('#memberstatic').append(' <tr><td><input type="text" name="fmname[]" size="30"  data-required="true"></td><td><input type="text" name="fmrelation[]"   data-required="true"></td><td><input type="text" class="fmage" name="fmage[]" size="5" maxlength="3"  data-required="true"></td><td><div class="remove-button">-</div></td></tr>');
		
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
	$('#currentval').hide();
	$.post(site_url+"admin/jx_member_plan_value",{planid:$(this).val()},function(data){
		$("#plan_amt").html(data).show();
	});
});

$('#mobnumberValidate').bind('keyup',function () { 
    this.value = this.value.replace(/[^0-9\.]/g,'');
    $(".erroemsg" ).empty();
    $(".erroemsg1" ).empty();
    $('.diabletext').removeAttr('disabled');
    $(".diabletext").val('');
    $("#memberidValidate").val('');
    $("#franchiseDetails").hide(); 
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
	                  $(".erroemsg").append("This mobile nubmer is not valid");
	                  $(".diabletext").attr('disabled', '');
	              }else
	              { 
	            	  $('.diabletext').removeAttr('disabled');
	            	  $(".erroemsg" ).empty();
	            	//  if(response.dataval=="2")
	            	//  {        
	            	//  $(".erroemsg").append("Already you have one feedback");	   
	            	//  $(".diabletext").attr('disabled', '');
	            	//  }    
	            	
		              $("#franchiseDetails").show();
	            	  var memresp = response.memberdetail;
	            	  $("#mobnumberValidate").val(memresp.mmobile);
	            	  $("#memberidValidate").val(memresp.memid);
	            	  $("#membername").val(memresp.mname);
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
	            	  var memfamresp = response.m_familydetail;
	            	  if(memfamresp.length >'0'){
	            	  $('#memberstatic').empty();
    					 $.each(memfamresp, function (index, data) {					           	
    						 $('#memberstatic').append(' <tr><td><input type="text" name="fmname[]" size="30" value="'+data.fmname+'"  data-required="true"></td><td><input type="text" name="fmrelation[]"   data-required="true" value="'+data.fmrelation+'"></td><td><input type="text" class="fmage" name="fmage[]" size="5" value="'+data.fmage+'" maxlength="3"  data-required="true"></td><td><input type="hidden" name="fmid[]" value="'+data.fmid+'" ></td></tr>');
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
   $(".erroemsg1" ).empty();
    $('.diabletext').removeAttr('disabled');
    $(".diabletext").val('');
    $("#mobnumberValidate").val('');
    $("#franchiseDetails").hide(); 
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
	                 
	              }else	              	
	              {
	            	  $('.diabletext').removeAttr('disabled');
	            	  $(".erroemsg1" ).empty();
	            	//  if(response.dataval=="2")
		             // {
	            	//	   $("#dateshowed").append(response.addeddate);
		            //	   $(".erroemsg1").append("Already you have one feedback"); 
		            //	   $(".diabletext").attr('disabled', '');
		            //  }	            	 
	            	  $("#franchiseDetails").show();
	            	  var memresp = response.memberdetail;
	            	  $("#mobnumberValidate").val(memresp.mmobile);
	            	  $("#memberidValidate").val(memresp.memid);
	            	  $("#membername").val(memresp.mname);
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

	            	  var memfamresp = response.m_familydetail;
	            	  if(memfamresp.length >'0'){
	            	  $('#memberstatic').empty();
    					 $.each(memfamresp, function (index, data) {					           	
    						 $('#memberstatic').append(' <tr><td><input type="text" name="fmname[]" size="30" value="'+data.fmname+'"  data-required="true"></td><td><input type="text" name="fmrelation[]"   data-required="true" value="'+data.fmrelation+'"></td><td><input type="text" class="fmage" name="fmage[]" size="5" value="'+data.fmage+'" maxlength="3"  data-required="true"></td><td><input type="hidden" name="fmid[]" value="'+data.fmid+'" ></td></tr>');
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
$('.fmage').bind('keyup',function () { 
	 this.value = this.value.replace(/[^0-9\.]/g,'');
});
$("#member_feedback_detail").submit(function(){
	 var fmname_cnt = [];
	 var mobnumberValidate = $( "#mobnumberValidate" ).val();
	 var memberidValidate = $( "#memberidValidate" ).val();
	 var membername = $( "#membername" ).val();
	 var mage = $( "#mage" ).val();
	 var datevalue = $( "#mdob" ).val();
	 var mgender =  $("input[name=gender]:checked").val();
	 var townval = $( "#townval" ).val();
	 var address = $( "#address" ).val();
	 var pincode = $( "#pincode" ).val();
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
	    	alert('Please fill Mobil Number');
	    	return false;
	    }
	    else if(memberidValidate=="")
	    {
	    	alert('Please fill Member Id');
	    	return false;
	    }
	    else if(membername=="")
	    {
	    	alert('Please fill member Name');
	    	return false;
	    }
	    else if(mgender=="")
	    {
	    	alert('Please select Gender');
	    	return false;
	    }
	    else if(mage=="")
	    {
	    	alert('Please fill Age');
	    	return false;
	    }
	    else if(townval=="")
	    {
	    	alert('Please fill Town');
	    	return false;
	    }
	    else if(address=="")
	    {
	    	alert('Please fill Address');
	    	return false;
	    }
	    else if(pincode=="")
	    {
	    	alert('Please fill Pincode');
	    	return false;
	    }
	    else if(familycount != fmname_cnt.length)
		{
	    	 alert("Family Member count value and family member details not equal.");
		 return false; 
	 	}
	 else if (!validformat.test(datevalue)){
		 alert("Invalid Date Format. Please correct and submit again.");
		return false;
	}
	else{ //Detailed check for valid date ranges
	var monthfield=datevalue.split("/")[1];
	var dayfield=datevalue.split("/")[0];
	var yearfield=datevalue.split("/")[2];
	var dayobj = new Date(yearfield, monthfield-1, dayfield);
	if ((dayobj.getMonth()+1!=monthfield)||(dayobj.getDate()!=dayfield)||(dayobj.getFullYear()!=yearfield)){
	alert("please fill the correct date.");
	return false;
	}
	else{
		$('.diabletext').removeAttr('disabled');
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

input[type="password"],input[type="file"]{
	font-size: 13px;
	padding:5px 2px;
	min-width: 200px;
	border:1px solid #cdcdcd;
	background: #fefefe;
}
</style>
<?php
