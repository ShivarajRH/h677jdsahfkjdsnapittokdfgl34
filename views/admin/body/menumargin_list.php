<div class="container page_wrap">
<h2>Manage Menu Margin</h2>
<table class="datagrid datagridsort"   width="50%" >
<!--<thead><th>Sl no</th><th>Menu</th><th>Menu Margin</th><th width="20%">Loyalty Point Value(1 point value)</th><th>Minimum Balance Discount</th><th>Actions</th></thead>-->
<thead><th>Sl no</th><th>Menu</th><th>Menu Margin on OP</th><th>Menu Margin on MP</th><th>Actions</th></thead>
<?php $i=1; foreach($menu_list_res->result_array() as $menu_det ){?>
<tbody>
	<tr>
		<td><?php echo $i;?></td>
		<td><?php echo $menu_det['name']?></td>
		<td><?php echo $menu_det['default_margin'];?> %</td>
		<td><?php echo $menu_det['default_mp_margin'];?> %</td>
		<!--<td ><?php // echo 'Rs '.$menu_det['loyality_pntvalue'] ?></td>-->
		<!--<td>Amount : <b>Rs <?php // echo formatInIndianStyle($menu_det['min_balance_value'])?></b>-->
			<!--<p style="margin:3px 0px">Discount: <b><?php // echo $menu_det['bal_discount']?>%</b></p>-->
		</td>
		<td>
			<a href="javascript:void(0)" onclick="edit_margindetails(<?php echo $menu_det['id']?>)">Edit</a> &nbsp;&nbsp;<a href="javascript:void(0)" onclick="view_marginupdatelog(<?php echo $menu_det['id']?>)">View Log</a>
		</td>
	</tr>
</tbody>
<?php $i++;}?>
</table>
	<div id="edit_menudiv" title="Edit Menu Margin">
	<form id="edit_menumargin" method="post" action="<?php echo site_url("admin/to_update_menumargin")?>" data-validate="parsley">
		<input type="hidden" name="menu_id" value="">
		<table>
			<tr>
				<td><b>Menu</b></td>
				<td><b>:</b></td>
				<td><input type="text" name="menu_name" value="" size="25px" readonly='readonly' style='background-color:#E6E6E6 !important;color:grey!important;'></td>
			</tr>
			<tr></tr>
			<tr style="display:none;">
					
					<td style="display:none;"><b>Minimum Balance Value</b></td><td><b>:</b></td><td><input type="text" name="min_bal_val" value="0" size="6px"></td>
					<td style="display:none;"><b>Balance Discount:</b></td><td><b>:</b></td><td><input type="text" name="bal_disc" value="0" size="3px">%</td>
					
			</tr>
			<tr></tr>
			<tr>
				<td><b>OP Margin</b></td>
				<td><b>:</b></td>
				<td><input type="text" size="3px" name="margin" value="">%
				</td>
				<td style="display:none;"><b>Loyalty Point Value</b></td>
				<td style="display:none;"><b>:</b></td>
				<td style="display:none;">Rs.<input type="hidden" name="loyalty_pntvalue" size="3px" value="0"></td>
			</tr>
			<tr>
				<td><b>MP Margin</b></td>
				<td><b>:</b></td>
				<td><input type="text" size="3px" name="mp_margin" value="">%</td>
			</tr>
		</table>
		</form>
	</div>
	
	<div id="view_marginupdatelog" title="Margin update Log">
		<table class="datagrid" id="menumargin_updatelog" width="100%">
			<thead><th>Menu</th><th>OP Menu Margin(%)</th><th>MP Menu Margin(%)</th><th>Loyality Point(Rs)</th><th>Balance Amount(rs)</th><th>Balance Discount(%)</th><th>Last Updated By</th><th>Updated On</th></thead>
			<tbody></tbody>
		</table>
	</div>
</div>
<script>
$('.leftcont').hide();
//$('.datagridsort').tablesorter( {sortList: [[0,0]]} );

function edit_margindetails(id)
{ $('#edit_menudiv').data('menuid',id).dialog('open');}

$('#edit_menudiv').dialog({
modal:true,
autoOpen:false,
width:'300',
height:'250',
open:function(){
	dlg = $(this);
	$('#edit_menumargin input[name="menu_id"]',this).val(dlg.data('menuid'));
	$('#edit_menumargin input[name="menu_name"]').val("");
	$('#edit_menumargin input[name="min_bal_val"]').val("");
	$('#edit_menumargin input[name="bal_disc"]').val("");
	$('#edit_menumargin input[name="margin"]').val("");
	$('#edit_menumargin input[name="loyalty_pntvalue"]').val("");
	$.post(site_url+'/admin/jx_load_menumargindet',{menu_id:dlg.data('menuid')},function(result){
		if(result.status == 'error')
			alert('No Menu Found');
		else
		{
			$('#edit_menumargin input[name="menu_name"]').val(result.menu_det.name);
			$('#edit_menumargin input[name="margin"]').val(result.menu_det.default_margin);
			$('#edit_menumargin input[name="loyalty_pntvalue"]').val(result.menu_det.loyality_pntvalue);
			$('#edit_menumargin input[name="min_bal_val"]').val(result.menu_det.min_balance_value);
			$('#edit_menumargin input[name="bal_disc"]').val(result.menu_det.bal_discount);
			$('#edit_menumargin input[name="mp_margin"]').val(result.menu_det.default_mp_margin);
		}
			

	},'json');
},
buttons:{
	'Update':function()
	{
		var update_menumarginform = $('#edit_menumargin',this);
		if(confirm('Are You sure want to update?'))
			update_menumarginform.submit();
		else
			return false;
	
		
	},
	'Cancel':function()
		{$('#edit_menudiv').dialog('close');}
},
});

function view_marginupdatelog(id)
{$("#view_marginupdatelog").data('menuid',id).dialog('open');}

$("#view_marginupdatelog").dialog({
	modal:true,
	autoOpen:false,
	width:'850',
	height:'350',
	open:function(){
	dlg=$(this);
	$('#menumargin_updatelog tbody').html(" ");
	$.post(site_url+'/admin/load_menumarginupdate_log',{menuid:dlg.data('menuid')},function(res){
	if(res.status=='error')
	{
		alert(res.msg);
	
		dlg.dialog('close');
		
	}else
	{
		
		$.each(res.menumarginupdate_det,function(a,b){
			var update_row=
				"<tr>"	
				+"<td>"+b.name+"</td>"
				+"<td>"+b.default_margin+"</td>"
				+"<td>"+b.default_mp_margin+"</td>"
				+"<td>"+b.loyality_pntvalue+"</td>"
				+"<td>"+b.balance_amount+"</td>"
				+"<td>"+b.balance_discount+"</td>"
				
				+"<td>"+b.user+"</td>"
				+"<td>"+b.updated_on+"</td>"
				+"</tr>"
				$(update_row).appendTo('#menumargin_updatelog tbody');
		});
	}
		},'json');
	},
	buttons:{
		'Close':function()
		{$(this).dialog('close');}
	}
	
});


</script>