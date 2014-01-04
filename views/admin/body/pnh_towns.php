<div class="container">

<h2>PNH Towns <?=isset($terry)?"for Territory:$terry":""?></h2>

<a href="<?=site_url("admin/pnh_addtown")?>">Add new town</a>
<?php 
	$terr_id = $this->uri->segment(3);
?>
<div>
View by territory :<select id="sel_terry">
<option value="0">All</option>
<?php foreach($this->db->query("select id,territory_name as name from pnh_m_territory_info order by territory_name asc")->result_array() as $t){?>
<option value="<?=$t['id']?>" <?php echo (($terr_id==$t['id'])?'selected':'');?> ><?=$t['name']?></option>
<?php }?>
</select>
</div><br>

<table class="datagrid" width="400">
<thead><tr><th>Sno</th><th>Town</th><th>Territory</th><th>&nbsp;</th></thead>
<tbody>
<?php $i=0; 

foreach($towns as $t){?>
<tr><td width=10><?=++$i?></td><td><?=$t['town_name']?></td><td><?=$t['territory_name']?></td>
	<td><a href="javascript:void(0)" onclick="show_edittown_frm(this)" terr_id="<?php echo $t['territory_id'] ?>" town_id="<?php echo $t['town_id'] ?>" town_name="<?php echo $t['town_name'] ?>" >Edit</a></td>
</tr>
<?php }?>
</tbody>
</table>

</div>

<div id="edit_town_dlg" title="Edit Town">	
	<form action="<?php echo site_url('admin/upd_pnhtown') ?>" method="post">
		<input type="hidden" name="town_id" value="0"> 
		<table>
			<tr><td><b>Town</b></td> <td> <input type="text" name="town_name" value=""> </td></tr>
			<tr>
				<td><b>Territory</b></td>
				<td>
					<select name="terr_id">
						<?php foreach($this->db->query("select id,territory_name as name from pnh_m_territory_info order by territory_name asc")->result_array() as $t){?>
						<option value="<?=$t['id']?>"><?=$t['name']?></option>
						<?php }?>
					</select>
				</td>
			</tr>
		</table>
	</form>
</div>

<script>
$(function(){
	$("#sel_terry").change(function(){
		v=$(this).val();
		if(v==0)
			v = '';
		location="<?=site_url("admin/pnh_towns")?>/"+v;
	});
});

$('#edit_town_dlg').dialog({
							autoOpen:false,
							modal:true,
							open:function(){
								terr_id = $(this).data('terr_id');
								town_id = $(this).data('town_id');
								twn_name = $(this).data('town_name');
								
								$('form input[name="town_id"]',this).val(town_id);
								$('form input[name="town_name"]',this).val(twn_name);
								$('form select[name="terr_id"]',this).val(terr_id);
							},
							buttons:{
								'Update' : function(){
									
									$('form',this).submit();
								},
								'Cancel':function(){
									$(this).dialog('close');
								}
							}
						});


function show_edittown_frm(ele)
{
	$('#edit_town_dlg').data({terr_id:$(ele).attr('terr_id'),town_id:$(ele).attr('town_id'),town_name:$(ele).attr('town_name')}).dialog('open');
} 


</script>
<?php
