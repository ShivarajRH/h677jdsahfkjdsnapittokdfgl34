<div class="container">
	<h2>PNH Deals bulk update</h2>
	
	<!-- Upload csv file form block-->
	<form method="post" target="_blank" enctype="multipart/form-data" id="deal_bulk_update_form">
		Upload file : <input type="file" name="deals"><input type="submit" value="Upload">
		<div style="color:#888;margin:10px 20px;">
			<ul>
				<li>Only CSV format supported</li>
				<li>First row as head neglected always</li>
			</ul>
		</div>
	</form>
	<!-- Upload csv file form block end-->
	
	<!-- Template Block -->
	<h4 style="margin:0px;">Template</h4>
	<div style="max-width:900px;overflow:auto">
		<table class="datagrid noprint">
		<?php $template=array("Item id","Deal Name","Category (ID)","Brand (ID)","Menu (ID)","Description"); ?>
			<thead>
				<tr>
					<?php foreach($template as $t){?><th><?=$t?></th><?php }?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<?php foreach($template as $i=>$t){?><td>&nbsp;</td><?php }?>
				</tr>
			</tbody>
		</table>
	</div>
	<!-- Template Block end-->
	
	<br><br>
	<!-- log block -->
	<h3 style="margin-bottom:0px;">Deal bulk updates</h3>
	<table class="datagrid">
		<thead>
			<tr>
				<th>Deals Updated</th>
				<th>Created on</th>
				<th>Created By</th>
			</Tr>
		</thead>
		<tbody>
			<?php foreach($this->db->query("select b.id,b.items,b.created_on,u.name as admin  from m_deals_bulk_update b join king_admin u on u.id=b.created_by order by b.id desc")->result_array() as $u){?>
			<tr>
				<td><?=$u['items']?></td>
				<td><?=date("g:ia d/m/y",$u['created_on'])?></td>
				<td><?=$u['admin']?></td>
			</tr>
			<?php }?>
		</tbody>
	</table>
	<!-- log block end-->
</div>
<script>
	$(function(){
		$("#deal_bulk_update_form").submit(function(){
			var file=$("input[type=file]").val();
				if(file=='')
				{
					alert('Please Select File');
					return false;
					}
			});
		});
</script>