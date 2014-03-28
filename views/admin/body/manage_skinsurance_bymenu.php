<div class="container">
<h2>Manage Insurance By Menu</h2>
<div>
	<table class="datagrid" width="100%">
	 <?php foreach ($pnh_menu as $menu){?>
	 <thead><th><?=$menu['name'] ?></th><th>Default Insurance Margin(%)</th><th>Actions</th></thead>
	 <tbody>
	 <tr>
	 
	 	<td>
			 <?php $insurance_det=$this->db->query("SELECT i.*,a.name FROM pnh_member_insurance_menu i JOIN king_admin a ON a.id= i.created_by WHERE menu_id=? and is_active=1 ORDER BY less_than ASC",$menu['id'])->result_array(); 
			 		if($insurance_det){?>
			 	<div class="fl_left" style="width:50%;">
					<table class="subdatagrid"  cellspacing="0" cellpadding="0">
						<thead><th>Created On</th></thead>
						<tbody><tr><td><?=format_datetime($insurance_det[0]['created_on']).'<p>By  <b>'.$insurance_det[0]['name'].'</b></p>'?></td></tr></tbody>
					</table>
				</div>
				<div class="fl_left" style="width:50%;">
					 <table class="subdatagrid" cellspacing="0" cellpadding="0">
						 <thead><th>Order Amount range (Rs)</th><th>Insurance Value (Rs)</th></thead>
						 <tbody>
							 <?php foreach($insurance_det as $i){?>
								 <tr>
								 <td>&gt;<?=$i['greater_than']." &lt; ".$i['less_than']?></td>
								 <td><?=$i['insurance_value']?></td>
								 </tr>
								 <?php }?>
						 </tbody>
				 	</table>
				 </div>
			 	<?php }else{?>
			 	<?php echo '<b>No Data Found</b>'?>
			 	<?php }?>
	 	</td>
	 	
	 	<?php if($insurance_det){?>
	 		<td><?php echo $insurance_det[0]['insurance_margin']?></td>
	 		<td><a href="javascript:void(0)" class="button button-action button-small" onclick="update_insurancemenu(<?php echo $menu['id']?>)">Update</a></td>
	 		<?php }else{?>
	 			<td></td>
	 	 		<td><a href="javascript:void(0)" class="button button-action button-small" onclick="update_insurancemenu(<?php echo $menu['id']?>)">Add</a></td>
	 	<?php }?>
	</tr>
	 <?php }?>
	 </tbody>
	</table>	
	</div>
	
	<table id="ps_clone" style="display:none" data-validate="parsley">
		<tr>
			<td><input type="text" name="greater_than[]" class="inp" data-required="true"></td><td><input class="inp" type="text" name="less_than[]" data-required="true"><td><input class="inp" type="text" name="insurance_value[]" data-required="true"></td><td><input type="button" onclick="remove_newpstockdet(this)" value="X" >
		</tr>
	</table>
	
	<div id="update_imenu_bloc" title="Configure Order Range For Insurance">
		<div class="fl_left" id="menuname">Menu : </div>
		<br><br>
		<div id="update_insurance_div"></div>	
	</div>
</div>
<script>

function update_insurancemenu(menuid)
{
	$("#update_imenu_bloc").data('imenuid',menuid).dialog('open');
}

function clone()
{
	$("#upd_imenu_tbl").append($("#ps_clone").html());
}
function remove_newpstockdet(ele)
{
	$(ele).parents('tr:first').html('');

}

$("#update_imenu_bloc").dialog({
	modal:true,
	autoOpen:false,
	width:'500',
	height:'auto',
	open:function(){
		$('.imenu_div').html("");
		//$("#upd_imenu_tbl").html(" ");
		var dlg=$(this);
		$.post(site_url+'admin/jx_load_menu_insurance_info',{menuid:dlg.data('imenuid')},function(resp){
			$("#menuname").html('Menu : <b>'+resp.menuname+'</b>');
			var tbl_cont = ' ';
			if(resp.status=='success')
			{
				tbl_cont+='<div align="right" style="margin-right: 15px;margin-top: -27px;" class="imenu_div"><input type="button"  value="ADD" onclick="clone()" ></div>';
				tbl_cont+='<form class="upd_imenu_form" data-validate="parsley"><table class="datagrid" width="100%" style="clear:both;" id="upd_imenu_tbl">';
				tbl_cont+='<input type="hidden" name="upd_menuid" class="upd_menuid" value="'+dlg.data('imenuid')+'">';
				tbl_cont+='<thead><th>Greater than</th><th>Less than (Rs)</th><th>Insurance Value (Rs)</th><th>Actions</th></thead><tbody>';
				$.each(resp.i_menu_det,function(i,a){
					tbl_cont+= '<tr>';
					tbl_cont+= ' <td><input type="text" name="greater_than[]" class="inp inp_qe" value="'+a.greater_than+'" data-required="true"></td>';
					tbl_cont+= ' <td><input type="text" name="less_than[]" class="inp inp_qe" value="'+a.less_than+'" data-required="true"></td>';
					tbl_cont+='  <td><input class="inp inp_qe" type="text" name="insurance_value[]" value="'+a.insurance_value+'" data-required="true"></td>';
					tbl_cont+='  <td><input type="button" onclick="remove_newpstockdet(this)" value="X" ></td>';
					tbl_cont+='  </tr></tbody>';
				});
					
					tbl_cont+='  </table><br>';
					tbl_cont+='<div> <b>Default Margin : </b><input type="text" name="insurance_margn" value="'+resp.insurance_margin+'"></div></form>'
					$("#update_insurance_div ").html(tbl_cont);	
			}
			else
			{
				tbl_cont+='<div align="right" style="margin-right: 15px;margin-top: -27px;" class="imenu_div"><input type="button"  value="ADD" onclick="clone()" ></div>';
				tbl_cont+='<form class="upd_imenu_form" data-validate="parsley"><table class="datagrid" width="50%" style="clear:both;" id="upd_imenu_tbl">';
				tbl_cont+='<input type="hidden" name="upd_menuid" class="upd_menuid" value="'+dlg.data('imenuid')+'">';
				tbl_cont+='<thead><th>Greater than  (Rs)</th><th>Less than (Rs)</th><th>Insurance Value (Rs)</th><th>Actions</th></thead><tbody>';
				tbl_cont+='<tr><td><input class="inp" type="text" name="greater_than[]" data-required="true"><td><td><input type="text" name="less_than[]" class="inp" data-required="true"></td><td><input class="inp" type="text" name="insurance_value[]" data-required="true"></td><td><input type="button" onclick="remove_newpstockdet(this)" value="X" ></td></tr>';
				tbl_cont+='  <tbody></table>';
				tbl_cont+='<div> Default Margin : <input type="text" name="insurance_margn" value="'+resp.insurance_margin+'"></div></form>';
				
					
				$("#update_insurance_div ").append(tbl_cont);
			}
		},'json');
			},
	buttons:{
	'Update':function(){
			var update_form=$(".upd_imenu_form",this);
			if(update_form.parsley('validate'))
			{
				$.post(site_url+'admin/jx_upd_insurancemenu',$('.upd_imenu_form').serialize(),function(resp){
					
						$("#update_imenu_bloc").dialog('close');
						window.location.reload();
					
				},'json');
			}
		},
	}
});
</script>