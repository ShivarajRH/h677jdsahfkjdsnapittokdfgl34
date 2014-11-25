<link rel="stylesheet"	href="<?php echo site_url(); ?>css/datatable/manage.franchises.css" type="text/css" />
<script type="text/javascript" charset="utf-8">  
var orders_val ;
$(document).ready(function() {
	    	 orders_val = $('#comboplans_table').DataTable({
											    	"processing": true,
													"serverSide": true,
													"iDisplayLength" : 20,//pagination count
													"bAutoWidth": false,
													"bDeferRender": true,
													
													"dom": '<"clear">iprftp',
													"sAjaxSource": site_url+"admin/jx_fetch_comboplan_categories",		
													"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Category Search: "}, //for loding image
													"fnServerData": function(sSource, aoData, fnCallback,oSettings )
										            {
														oSettings.jqXHR = $.ajax
											              ({
											               'dataType': 'json',
											                'type'    : 'POST',
											                'url'     : sSource,
											                'data'    : aoData,
											                "success" : function(response) {
												                 fnCallback(response); 
												                 if ((response.recordsFiltered/20)>1) {
											                	      $('#comboplans_table_paginate')[0].style.display = "none";
											                	      $('.dataTables_paginate').show();      	      
										                	     } else {
										                	    	 $('.dataTables_paginate').hide();
										                	    	 $('#comboplans_table_paginate')[0].style.display = "none";		                	   
										                	     }
												                 
													                 }
											              });
											                	
										            },
							            "columns": [  							
								        				{ "data": "rowid" },
								        				{ "data": "genderval" },
								        				{ "data": "catname" },
								        				{ "data": "action" }
								        			        											        
							        			], 
							        	"order": [[0, 'asc']],
							        	"columnDefs": [
														{
														    "targets": [3],
														    "visible": true,
														    "searchable": false,
														    "orderable":false
														}]
			    	});
	    	   $("#submit_id").click(function() {
		    	 var genderval =  $("#genderval").val();
		    	 var categoryval = $("#categoryval").val();
		    	 if(genderval=="")
		    	 {
			    	 alert('Please select Gender');
			    	 return false;
		    	 }
		    	 else if(categoryval=="")
		    	 {
			    	 alert('Please select Category');
			    	 return false;
		    	 }else
		    	 {
		    		 $.ajax
		             ({
		               'dataType': 'json',
		               'type'    : 'POST',
		               'url'     : site_url+"admin/jx_add_newcombo_category",
		               'data'    : "gender="+genderval+"&category="+categoryval,
		               "success" : function(response) {  
							if(response.errorid=="1")
							{
								$("#genderval").val('');
								$("#categoryval").val('');
			                }
								$("#errorval").empty();
								$("#errorval").append(response.errormsg_val).fadeIn('slow').delay(1500).fadeOut('slow');
			                }
		             });
		    	 } 
			    	 
	    	 });
});
</script>
<h2 class="page_title">Combo Plan Categories</h2>
<div class="container page_wrap">
	<div class="page_top">
	       <!-- <a href="<?php echo site_url('admin/pnh_addfranchise')?>" target="_blank"
			class="button fl_right button-rounded button-action button-tiny">Add Categories</a> -->
		 
			
					
	
	</div>
</div>

		<table id="comboplans_table" class="display " cellspacing="0">
			<thead>
				<tr>
				
					<th>ID</th>
					<th>Gender Attribute</th>
					<th>Category Name</th>
				   <th>Action</th>
				</tr>
			</thead>
		</table>
	
<div id="addform">
	<form method="post" id="comboadd_category">
	 <table  cellpadding="15">		
			<tbody>
				  <tr>
			        <th>Add New Combo Category</th>
			      </tr>
				  <tr>
					<td>Gender Attribute:</td>
					<td>
							<select name="genderval" id="genderval" style="width: 133px;">
							<option value="">Select Gender</option>
							<option value="MEN">Men</option>
							<option value="WOMEN">Women</option>
							<option value="BABY">Baby</option>
							</select>
					</td>
				 </tr>
				  <tr>
					<td>Category Name:</td>
					<td>
							<select name="categoryval" id="categoryval" style="width: 133px;">
							<option value="">Select Category</option>
							<?php foreach($category_list as $list){?>
							<option value="<?php echo $list['id'];?>"><?php echo $list['name'];?></option>
						<?php } ?>
							</select>
					</td>
				 </tr>
				 <tr>
					<td colspan="3"><div id="errorval" style="color: red;"></div>
						<div align="right"><input type="button" name="submit_id" id="submit_id" value="Submit" /></div>
					</td>
				</tr>
			</tbody>
	 </table>
    </form>
	</div>
	

<style>
.dataTables_wrapper {
   float:left;
    width:50%;
}
div#addform {
    float: right;
    margin-top: 66px;
    vertical-align: top;
    width: 49%;
}
.dataTables_wrapper .dataTables_processing
{
    overflow: hidden;
    display: none;
    top: 100px;
    left: 0;
    height: 100%;
    width: 200%;
    background: rgba(255, 255, 255, .8) 50% 50% no-repeat;
}

</style>