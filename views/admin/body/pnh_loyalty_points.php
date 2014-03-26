<div class="container">
<h2>Configure  Loyalty Points by Menu</h2>
	<div>
	<table class="datagrid" width="100%">
	 <?php foreach ($pnh_menu as $menu){?>
	 <thead><th><?=$menu['name'] ?></th><th>Actions</th></thead>
	 <tbody>
	 <tr>
	<td>
			 <?php $loyality_det=$this->db->query("SELECT l.*,a.name FROM pnh_loyalty_points l JOIN king_admin a ON a.id= l.created_by WHERE menu_id=? and is_active=1 ORDER BY amount ASC",$menu['id'])->result_array(); 
			 		if($loyality_det){?>
			 	<div class="fl_left" style="width:50%;">
					<table class="subdatagrid"  cellspacing="0" cellpadding="0">
						<thead><th>Created On</th></thead>
						<tbody><tr><td><?=format_datetime($loyality_det[0]['created_on']).'<p>By  <b>'.$loyality_det[0]['name'].'</b></p>'?></td></tr></tbody>
					</table>
				</div>
				<div class="fl_left" style="width:50%;">
					 <table class="subdatagrid" cellspacing="0" cellpadding="0">
						 <thead><th>Amount (Rs)</th><th>Points</th><th>Valid (Days)</th></thead>
						 <tbody>
							 <?php foreach($loyality_det as $points){?>
								 <tr>
								 <td><?=$points['amount']?></td>
								 <td><?=$points['points']?></td>
								 <td><?=$points['valid_days']?></td>
								 </tr>
								 <?php }?>
						 </tbody>
				 	</table>
				 </div>
			 	<?php }else{?>
			 	<?php echo '<b>No Data Found</b>'?>
			 	<?php }?>
	 	</td>
	 	<?php if($loyality_det){?>
	 		<td><a href="javascript:void(0)" class="button button-action button-small" onclick="update_menulpoint(<?php echo $menu['id']?>)">Update</a></td>
	 	<?php }else{?>
	 	 		<td><a href="javascript:void(0)" class="button button-action button-small" onclick="update_menulpoint(<?php echo $menu['id']?>)">Add</a></td>
	 	<?php }?>
	</tr>
	 <?php }?>
	 </tbody>
	</table>	
	</div>
	
	<table id="ps_clone" style="display:none" data-validate="parsley">
		<tr>
			<td><input type="text" name="amount[]" class="inp" data-required="true"></td><td><input class="inp" type="text" name="points[]" data-required="true"><td><input class="inp" type="text" name="valid_days[]" data-required="true"></td><td><input type="button" onclick="remove_newpstockdet(this)" value="X" >
		</tr>
	</table>

	<div id="update_lpoint_bloc" title="Configure Loyalty Points">
		<div class="fl_left" id="menuname">Menu : </div>
		<br><br>
		<div id="update_lpnts_div"></div>	
	</div>


</div>

<script>
function update_menulpoint(menuid)
{
	$("#update_lpoint_bloc").data('lmenuid',menuid).dialog('open');
}
$("#update_lpoint_bloc").dialog({
	modal:true,
	autoOpen:false,
	width:'500',
	height:'auto',
	open:function(){
		$('.lpnts_div').html("");
		$("#upd_pnts_tbl").html(" ");
		var dlg=$(this);
		$.post(site_url+'admin/jx_load_menu_loyalty_point',{menuid:dlg.data('lmenuid')},function(resp){
			$("#menuname").html('Menu : <b>'+resp.menuname+'</b>');
			var tbl_cont = ' ';
			if(resp.status=='success')
			{
				tbl_cont+='<div align="right" style="margin-right: 15px;margin-top: -27px;" class="lpnts_div"><input type="button"  value="ADD" onclick="clone()" ></div>';
				tbl_cont+='<form class="upd_pnts_form" data-validate="parsley"><table class="datagrid" width="50%" style="clear:both;" id="upd_pnts_tbl">';
				tbl_cont+='<input type="hidden" name="upd_menuid" class="upd_menuid" value="'+dlg.data('lmenuid')+'">';
				tbl_cont+='<thead><th>Amount (Rs)</th><th>Points</th><th>Valid (Days)</th><th>Actions</th></thead><tbody>';
				$.each(resp.l_pnts_det,function(i,a){
					tbl_cont+= '<tr>';
					tbl_cont+= ' <td><input type="text" name="amount[]" class="inp inp_qe" value="'+a.amount+'" data-required="true"></td>';
					tbl_cont+='  <td><input class="inp inp_qe" type="text" name="points[]" value="'+a.points+'" data-required="true"></td>';
					tbl_cont+='  <td><input class="inp inp_qe" type="text" name="valid_days[]" value="'+a.valid_days+'" data-required="true"></td>';
					tbl_cont+='  <td><input type="button" onclick="remove_newpstockdet(this)" value="X" ></td>';
					tbl_cont+='  </tr></tbody>';
				});
					
					tbl_cont+='  </table></form>';
					$("#update_lpnts_div ").html(tbl_cont);	
			}
			else
			{
				tbl_cont+='<div align="right" style="margin-right: 15px;margin-top: -27px;" class="lpnts_div"><input type="button"  value="ADD" onclick="clone()" ></div>';
				tbl_cont+='<form class="upd_pnts_form" data-validate="parsley"><table class="datagrid" width="50%" style="clear:both;" id="upd_pnts_tbl">';
				tbl_cont+='<input type="hidden" name="upd_menuid" class="upd_menuid" value="'+dlg.data('lmenuid')+'">';
				tbl_cont+='<thead><th>Amount (Rs)</th><th>Points</th><th>Valid (Days)</th><th>Actions</th></thead><tbody>';
				tbl_cont+='<tr><td><input type="text" name="amount[]" class="inp" data-required="true"></td><td><input class="inp" type="text" name="points[]" data-required="true"><td><input class="inp" type="text" name="valid_days[]" data-required="true"></td><td><input type="button" onclick="remove_newpstockdet(this)" value="X" ></td></tr>';
				tbl_cont+='  <tbody></table></form>';
				$("#update_lpnts_div ").append(tbl_cont);
			}
		},'json');
			},
	buttons:{
	'Update':function(){
			var update_form=$(".upd_pnts_form",this);
			if(update_form.parsley('validate'))
			{
				$.post(site_url+'admin/jx_updmenulylpnts',$('.upd_pnts_form').serialize(),function(resp){
					
						$("#update_lpoint_bloc").dialog('close');
						window.location.reload();
					
				},'json');
			}
		},
	}
});

function clone()
{
	$("#upd_pnts_tbl").append($("#ps_clone").html());
}
function remove_newpstockdet(ele)
{
	$(ele).parents('tr:first').html('');

}
</script>

