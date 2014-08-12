<div class="page_container">
	<div align="left">
		<a href="<?php echo site_url('admin/asset_list')?>" class="button button-royal " style="float:right;">List
			Assets</a>
		<h3 class="page_title">Add Asset</h3>
	</div>
	
	<div class="form_container">
		<form action="<?php echo site_url('admin/process_addasset')?>" method="post" data-validate="parsley">
			<table cellpadding=5>
				<tr>
					<td><b>Asset name</b></td>

					<td><input type="text" name="name" value="<?php echo set_value('name')?>" data-required="true"> 
						
					</td>
				</tr>
				<tr>
					<td><b>Brand</b></td>

					<td><select name="brand_id" data-required="true">
							<option value="">Choose</option>
							<?php 
							$brand_list = $this->erpm->get_brands();
							if($brand_list){
									foreach ($brand_list as $brand){
							?>
							<option <?php echo set_select('id',$brand['name'])?> value="<?php echo $brand['id']?>">
								<?php echo $brand['name']?>
							</option>
							<?php 			
									}
								}
									
								?>
					</select> <?php echo form_error('brand_id','<div class="error">','</div>')?>
					</td>
				</tr>
				<tr>
					<td><b>Category</b></td>

					<td><select name="cat_id" data-required="true">
							<option value="">Choose</option>
							<?php 
							$cat_list = $this->erpm->get_cats();
							if($cat_list){
									foreach ($cat_list as $cat){
							?>
							<option <?php echo set_select('id',$cat['name'])?> value="<?php echo $cat['id']?>">
								<?php echo $cat['name']?>
							</option>
							<?php 			
									}
								}
									
								?>
					</select> <?php echo form_error('cat_id','<div class="error">','</div>')?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div  style="background: #dedede;padding:5px 10px;">
							<h3 style="margin:5px 0px;text-align: center;">Add Accessories</h3>
							<ol id="accessory_list" style="padding-left: 20px">
								
							</ol>
							<div align="right">
								<a id="add_accessory" class="add_btn" href="javascript:void(0)">Add more</a>
							</div>
						</div>
					</td>
				</tr>
                
			</table>
			
			<input style="float:right;margin-bottom: 7px;margin-right: 946px;"class="button button-action button-rounded button-small" type="submit" value="Add">
       </form>
	</div>
</div>
<style type="text/css">
	#accessory_list li{margin: 5px 0px;}	
	#accessory_list .add_btn{font-size: 12px;text-decoration: none;color: #000;font-weight: bold;}
	#accessory_list .remove_btn{font-size: 12px;text-decoration: none;color: #cd0000;font-weight: bold;margin-left: 5px;}
	#accessory_list .inputbox{width: 160px;}
</style>
<script type="text/javascript">
	  $('#add_accessory').click(function(e){
		  e.preventDefault();
		  $('#accessory_list').append('<li><input type="text" class="inputbox" name="accessory_name[]" value=""> <a href="javascript:void(0)" class="remove_btn">X</a></li>');
		  $('.remove_btn').unbind('click').click(function(e){
			  e.preventDefault();
			  $(this).parent().remove();
		  });
		  
	  }); 
		  for(var i=0;i<2;i++)
	  	$('#add_accessory').trigger('click');
	           
</script>
