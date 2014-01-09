
<div id="container">
	<div id="main_column" class="clear">
		<div class="cm-notification-container "></div>
		<div class="tools-container">
		  <h1 class="mainbox-title">Manage Routes</h1>
		  
		</div>
<div class="fl_right" id="create_route">Add Routes</div>
<div id="add_route" style="display: none; padding: 4px;" title="Add Route">

<form method="post" action="" id="add_route_form" data-validate="parsley" >
<table>
	<tr>
		<td>Route Name :</td>
		<td> <input type="text" name="route_name" size="40" data-required="true">
		<?php echo form_error('route_name','<div class="error">','</div>') ;?>
		</td> 
	</tr>
	<tr>
		<td>Choose Towns:</td>
	<?php 
	$all_towns=$this->db->query("select id,town_name from pnh_towns order by town_name asc")->result_array();
	
	?>
	<td>
		<select class="chzn-select" style="width: 200px;" name="towns[]" multiple="multiple" placeholder="Select Towns" data-required="true">
			            <?php foreach($all_towns as $t){?>
			            <option value="<?php echo $t['id'];?>"<?php echo set_select('towns[]',$t['id']);?>><?php echo $t['town_name']?></option>
			            <?php }?>
	      </select>
	</td>
	</tr>
</table>
</form>
</div>

<div id="view_route_dlg" style="display: none; padding: 4px;" title="Edit Route">
<form method="post" action="" id="upd_route_frm" data-validate="parsley">
<input type="hidden"  name="route_id"  id="view_route_id"  data-required="true">
<table>
<tr>
	<td>Route Name :</td>
	<td><input type="text" name="route_name" size="40" data-required="true" id="view_route_name"> </td>
</tr>
<tr>
<td>Choose Towns:</td>
<td><select class="chzn-select" style="width: 200px;" name="towns[]" multiple="multiple" data-required="true" id="view_towns_name">
	</select>
</td>
</tr>
</table>
</form>
</div>

<table class="datagrid">
<thead>
	<tr>
		<Th>Sno</Th>
		<th>Route name</th>
		<th></th>
	</tr>
</thead>
<tbody>
	<?php $i=0; foreach($routes as $t){?>
	<tr>
	<td><?=++$i?></td>
	<td><?=$t['route_name']?></td>
	<td>
		<a onclick="edit_route('<?php echo $t['id'] ?>')" href="javascript:void(0)">Edit</a>
		<a  href="<?php echo site_url('admin/view_routes/'.$t['id']);?>">View</a>
	</td>
	</tr>
	<?php 
		}
	 ?>
</tbody>
</table>
</div>
<script>
function edit_route(route_id)
{
	$('#view_route_dlg').data('route_id',route_id).dialog('open');
}
$(".chzn-select").chosen();
$(function() {
    $( "#add_route" ).dialog({
    	width:'516',
		height:'auto',
		autoResize:true,
      modal: true,
     autoOpen:false,

      buttons:{

    	  'Cancel' :function(){
				$(this).dialog('close');
			},
          'Add Route':function(){
        	var dlg = $(this);
            var form_add_route = $("#add_route_form",this);   
            if(form_add_route.parsley('validate')){
                $.post(site_url+'/admin/jx_add_route',form_add_route.serialize(),function(resp){
                	//alert("success");
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

    $( "#create_route" ).button().click(function() {
      $( "#add_route" ).dialog( "open" );
    });
  });


$( "#view_route_dlg" ).dialog({
			modal:true,
			autoOpen:false,
			width:'516',
			height:'auto',
			open:function(){
			dlg = $(this);

			
			
			$('#view_route_id').val("");
			$('#view_route_name').val("");
			$('#view_towns_name').val("");
			
			
			// ajax request fectch etask details
			   $.post(site_url+'/admin/jx_load_routedet',{route_id:$(this).data('route_id')},function(result){
			   if(result.status == 'error')
				{
					alert("Route details not found");
					dlg.dialog('close');
			    }
			    else
				{
					$.each(result.route,function(k,v){
						$('#view_route_id').val(v.route_id);
						$('#view_route_name').val(v.route_name);
						$('#view_towns_name').append('<option selected value="'+v.town_id +'">'+v.town_name +'</option>');
					});

					$('#view_towns_name').trigger("liszt:updated");
				}
					
			   },'json');
		},
			buttons:{
					'Update':function(){
	 					var dlg = $(this);
							var frm_updroute = $("#upd_route_frm",this);
							if(frm_updroute.parsley('validate')){
	 					 	$.post(site_url+'/admin/jx_upd_route',frm_updroute.serialize(),function(resp){
		 					 	if(resp.status == 'success')
		 					 	{
		 					 		dlg.dialog('close');
		 					 	
			 					}
		 					},'json');
	 					}else
	 					{
	 						alert("Error!!!")
		 				}
					}
				}
	});
/*$( "#edit_route" ).button().click(function() {
    $( "#view_route_dlg" ).dialog( "open" );
  });*/

</script>
</div>
<?php


