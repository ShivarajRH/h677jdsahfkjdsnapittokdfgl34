<div class="container">
<h2>Franchisee Combo Detail</h2>
<h4 id="dateshowed" style="color: red;"></h4>
<div class="listpage">

	<div>
		<form method="post" id="franchise_plan_detail" action="<?php echo site_url('admin/add_franchise_combo_detail');?>" data-validate="parsley">
			<table class="tbl_listview" cellpadding="8">
				<tbody>
					<tr>
						<td >Name<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<input type="text"  name="mname" id="mname" size=50  ><span style="color: red;"> <?php if(validation_errors()) { echo form_error('mname');}?></span>
						</td>
					</tr>					
					<tr>
						<td>Franchisee Name<font size="4" color="red">*</font>:</td>
						<td colspan="2">
						<input type="text"  name="franchisename" size=50   id="franchisename"><span style="color: red;"> <?php if(validation_errors()) { echo form_error('franchisename');}?></span>
						</td>						
					</tr>
					<tr>
						<td>Franchisee ID/Mobile No<font size="4" color="red">*</font>:</td>
						<td colspan="2">
							<input type="text"  name="franchiseno" maxlength="10"  id="mobnumberValidate"><input type="hidden" id="existmemberval" name="existmemberval"><input type="hidden" id="invalidno" name="invalidno"><span class="erroemsg" style="color: red;"> <?php if(validation_errors()) { echo form_error('franchiseno');}?></span>
						</td>
					</tr>					
					<tr>
						<td colspan="3">	
						<table id="MemberDetails" border="0">
						<?php $i=0; foreach($plan_list as $list) { ?>
						 <tr>
					        <th><span id="errmsg"></span></th>
					        <th colspan="3"><?php echo $list['cat_plan_name']; ?></th>					       
					    </tr>
					    <?php if($i==0) {?>	
					    <tr>
					        <th>Categories</th>
					        <th>Preference 1</th>
					        <th>Preference 2 </th>
					        <th>Preference 3 </th>
					    </tr>
					    	<?php } ?>
					    				  
					     <?php foreach( $this->db->query("select b.id as cid,c.name from m_combo_categories b join king_categories c on c.id=b.catid where gender_attr=?",$list['gender_attr'])->result_array() as $i){?>
					    <tr>					   
					         <td><?php echo $i['name'];?><input type="hidden" name="category_id[]" value="<?php echo $i['cid'];?>"><input type="hidden" name="genderval[]" value="<?php echo $list['gender_attr'];?>"></td> 
					        <td><input type="text" name="preference1[]" class="preference" size="15"  data-reaaquired="true"></td>
					        <td><input type="text" name="preference2[]" class="preference" size="15"  data-reaaquired="true"></td>
					        <td><input type="text" name="preference3[]" class="preference" size="15"  data-reaaquired="true"></td>
					    </tr>
					    <?php } }?>
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

$('#mobnumberValidate').bind('keyup',function () { 
    this.value = this.value.replace(/[^0-9\.]/g,'');
    $("#franchisename" ).val('');
    $(".erroemsg" ).empty();
    $("#dateshowed").html('');
    $("#existmemberval").val('');
    $("#invalidno").val('');
    if(this.value.length =='10' || this.value.length =='8')
    {
    	 $.ajax
         ({
           'dataType': 'json',
           'type'    : 'POST',
           'url'     : site_url+"admin/jx_franchisee_mobno_validate",
           'data'    : "mobid="+this.value,
           "success" : function(response) {
        	      $(".erroemsg" ).empty();
        	      
	              if(response.dataval=="0")
	              {
	                  $(".erroemsg").append("This  number is not valid");               
	                  $("#invalidno").val('1');      
	              }else 
	              {    $("#dateshowed").html('');
	            	  if(response.dataval=="2"){
	            		  $("#dateshowed").append(response.addeddate);
	            		  $(".erroemsg").append("Already you have one plan"); 	  
	            		  $("#existmemberval").val('1');
	            	  }
	            	  var franresp = response.franidval;
	            	  $("#franchisename").val(franresp.franname);
	              }
            }
         });
    }
});
$("#franchise_plan_detail").submit(function(){
	 $("#dateshowed").empty(); 
	 var mname = $( "#mname" ).val();
	 var franchisename = $( "#franchisename" ).val();
	 var mobnumberValidate = $( "#mobnumberValidate" ).val();
	 var existmemval = $( "#existmemberval" ).val();
	 var invalidno = $( "#invalidno" ).val();
	 var preference1 = document.getElementsByName('preference1[]');
	 var preference2 = document.getElementsByName('preference2[]');
	 var preference3 = document.getElementsByName('preference3[]');
     var flag1=0;
     var flag2=0;
     var flag3=0;
     for (var i = 0; i < preference1.length; i++) 
     {         
         if (preference1[i].value!="") 
             { 
             flag1=1;
            } 
     } 
     for (var i = 0; i < preference2.length; i++) 
     {         
         if (preference2[i].value!="") 
             { 
             flag2=1;
            } 
     } 
     for (var i = 0; i < preference3.length; i++) 
     {         
         if (preference3[i].value!="") 
             { 
             flag3=1;
            } 
     } 
	 if(mname=="")
	    {
	    	alert('Please fill Name');
	    	return false;
	    }
	    else if(franchisename=="")
	    {
	    	alert('Please fill Franchise Name');
	    	return false;
	    }
	    else if(mobnumberValidate=="")
	    {
	    	alert('Please fill Franchise Id or Mob No');
	    	return false;
	    }
	    else if(flag1==0)
	    {
	    	alert('Please select atlest one Preference 1');
	    	return false;
	    }
	    else if(flag2==0)
	    {
	    	alert('Please select atlest one Preference 2');
	    	return false;
	    }
	    else if(flag3==0)
	    {
	    	alert('Please select atlest one Preference 3');
	    	return false;
	    }else if(existmemval=='1')
		{
			alert("Already you have one plan.");
			return false;
		}else if(invalidno=='1')
		{
			alert("Invalid Number.");
			return false;
		}
		else
	    {
		    return true;
	    }
	
});
$(".preference").keypress(function (e) {
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
         {
              return false;
         }
  });
</script>
<style>
/** PAGE CSS**/
.listpage{
	width:960px;
	<!--margin: 0 auto; -->
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
