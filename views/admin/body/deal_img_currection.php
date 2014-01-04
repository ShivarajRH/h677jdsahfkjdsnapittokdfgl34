<div class="page_wrap container">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Deals image currection</h2>
		</div>
		<div class="fl_right stats" >
			<select id="sel_brand" data-placeholder="Choose Brand" style="width: 250px;">
				<option value="0"></option>
				<?php foreach($this->db->query("select id,name from king_brands order by name asc")->result_array() as $b){?>
				<option value="<?=$b['id']?>"><?=$b['name']?></option>
				<?php }?>
			</select>
			
			<select id="sel_cat" data-placeholder="Choose Category" style="width: 250px;">
			</select>
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="page_topbar_left fl_left" >
			<span class="total_overview">Total Deals: <b><?php echo $ttl_data;?></b> </span>
		</div>
		<div class="page_action_buttonss fl_right" align="right">
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<table class="datagrid" width="100%" cellpadding="5" cellspacing="0">
			<thead>
				<tr>
					<th>Si</th>
					<th>Deal name</th>
					<th>Image</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					if($deals_list)
					{
						foreach($deals_list as $i=>$d)
						{
							?>
							<tr>
								<td><?php echo $i+1;?></td>
								<td><?php echo $d['deal_name'].'  '.$d['pic'];?></td>
								<td>
									<?php 
										if(empty($d['pic']))
										{
									?> 
										<img  src="<?=IMAGES_URL?>no_photo.png">
									<?php }?>
									<a href="<?php echo IMAGES_URL?>items/small/<?php echo $d['pic']?>.jpg" target="blank">
										<img alt="" height="100" src="<?php echo IMAGES_URL?>items/small/<?=$d['pic']?>.jpg">
									</a>
								</td>
								<td>
									<a class="danger_link update_img" href="javascript:void(0)" dealid="<?php echo $d["dealid"]?>" >Update image</a>
								</td>
							</tr>
							<?php
						}
					}else{
						?>
							<tr >
								<td colspan="4" align="center"><?php echo "No data found"; ?></td>
							</tr>		
						<?php 
					}
				?>
				<tr class="pagination">
					<td colspan="4" align="right"><?php echo $pagination; ?></td>
				</tr>		
			</tbody>
			
		</table>
		
	</div>
</div>


<!--image update modal -->
<div id="img_update_modal"  title="Update new image">
	<form action="<?php echo site_url("admin/image_currection_process")?>" id="img_update_form" method="post" enctype="multipart/form-data" autocomplete="off">
		<input type="hidden" name="deal_id" value=''>
		<table width="100%" cellpadding="5" cellspacing="0">
			<tbody>
				<tr>
					<td>
						<input type="file" name="pic" class="inp">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<!--image update modal end-->
<script>
$("#sel_brand").chosen();
$("#sel_cat").chosen();

$("#sel_cat").change(function(){
	var cat_id=$("#sel_cat").val();
	var brand_id=$("#sel_brand").val();

	if(cat_id.length!=0 && brand_id.length!=0)
	{
		location.href=site_url+"/admin/deal_img_currection/"+cat_id+"/"+brand_id+"/0";
	}
});

$('#sel_brand').change(function(){
	var sel_brandid=$(this).val();
	$('#sel_cat').html('').trigger("liszt:updated");
	$.getJSON(site_url+'/admin/loadcat_bybrand/'+sel_brandid,'',function(resp){
	var brand_linkedcat_html='';
	if(resp.status=='error')
	{
		alert(resp.message);
	}
	else
	{
		brand_linkedcat_html+='<option value="">Choose Category </option>';
		$.each(resp.cat_list,function(i,b){
			brand_linkedcat_html +='<option value="'+b.catid+'">'+b.category_name+'</option>';
			});
	}
	 $('#sel_cat').html(brand_linkedcat_html).trigger("liszt:updated");
	 $('#sel_cat').trigger('change');
	});
});

$(".update_img").click(function(){
	var dealid=$(this).attr("dealid");
	$("#img_update_modal").data({"dealid":dealid}).dialog("open");
})

 
$('#img_update_modal').dialog({
	autoOpen:false,
	modal:true,
	height:'150',
	width:'480',
	autoResize:true,
	open:function(){
			$("#img_update_form input[name='deal_id']").val('');
			$("#img_update_form input[name='deal_id']").val($(this).data("dealid"));
			
		},
		
	buttons:{
		'Update' : function(){
			var c=confirm("Are you sure to  submit this image");
			if(c)
				$('form',this).submit();
			else
				return false;
		},
		
		'Close':function(){
			$(this).dialog('close');
		}
	}
});

$("#img_update_form").submit(function(){
	 $in=$("input[name='pic']",this).val();
	 extension = $in.substr( ($in.lastIndexOf('.') +1) );
		
	 if(extension!='jpg' && extension!='jpeg' && extension!='png' && extension!='gif')
	 {
		 alert("Invalid file attached");
		 return false;
	}

	return true;	
});
 

</script>
