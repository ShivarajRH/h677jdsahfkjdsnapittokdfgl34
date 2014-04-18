<div class="page_wrap container">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Vendor deal import log</h2>
		</div>
	</div>
	
	<div class="page_topbar">
		<div class="page_topbar_left fl_left" >
			<span class="total_overview">Total log: <b><?php echo $ttl_log; ?> </b> </span>
		</div>
		<div class="page_action_buttonss fl_right" align="right">
			<a href="<?php echo site_url('admin/config_vendor_brand_cat_link');?>">Config vendor brand category</a>
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<table width="100%" cellpadding="5" cellspacing="0" class="datagrid">
			<thead>
				<tr>
					<th width="2%"><input type="checkbox" class="chk_all"></th>
					<th width="2%">Sl</th>
					<th width="8%">Vendor</th>
					<th>Product</th>
					<th width="41%">Error</th>
					<th width="8%">Loged on</th>
					<th width="8%">Action</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				if($log)
				{
					foreach($log as $i=> $l)
					{
						?>
						<tr>
							<td><input type="checkbox" value="<?php echo $l['id'];?>" class="p_check"></td>
							<td><?php echo ($i+1) ;?></td>
							<td><?php echo $l['vendor_name']; ?></td>
							<td>
								<?php 
									$p_html='';
									$prd_det=json_decode($l['data'],true);
									foreach($prd_det as $prd)
									{
										$p_html.='<ul>';
										foreach($prd as $p)
										{
											$p_html.='<li>'.$p['name'].'-'.$p['sku'].'</li>';
										}
										$p_html.='</ul>';
									}
									
									echo $p_html;
								?>
							</td>
							<td>
								<?php 
									$msg_det=explode(',',$l['msg']);
									$ms_html='';
									$ms_html.='<ul>';
									foreach($msg_det as $m)
									{
										$ms_html.='<li class="error"><b>'.$m.'</b></li>';
									}
									$ms_html.='</ul>';
									
									echo $ms_html;
								?>
							</td>
							<td>
								<?php echo format_datetime($l['created_on']);?>
							</td>
							<td>
								<button class="button button-tiny button-action button-rounded <?php echo 'log_id_'.$l['id']?> data_re_import" disabled="disabled" log_id="<?php echo  $l['id']; ?>">Import</button>
							</td>
						</tr>
						<?php 
					}
				}
			?>
				<tr>
					<td colspan="2" align="left" >
						<button class="button button-tiny button-action button-rounded  bulk_data_re_import" disabled="disabled">Import</button>
					</td>
					<td colspan="5" align="right" class="pagination"><?php echo $pagination; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<style>
	.error{
		color:red;
	}		
</style>

<script>
$(".chk_all").click(function(){
	if($(this).attr("checked"))
	{
		$(".p_check").attr("checked",true);
		$(".data_re_import").removeAttr("disabled");
		$(".bulk_data_re_import").removeAttr("disabled");
	}
	else
	{
		$(".p_check").attr("checked",false);
		$(".data_re_import").attr("disabled",true);
		$(".bulk_data_re_import").attr("disabled",true);
	}
});

$(".p_check").click(function(){
	if($(this).attr("checked"))
	{
		if ($(".p_check:checked").length > 1) {
			$(".bulk_data_re_import").removeAttr("disabled");
		}
		
		$(".log_id_"+$(this).val()).removeAttr("disabled");
	}
	else
	{
		if ($(".p_check:checked").length <= 1) {
				$(".bulk_data_re_import").attr("disabled",true);
		}
		$(".log_id_"+$(this).val()).attr("disabled",true);
	}
	
});

$(".data_re_import").click(function(){
	if(confirm("Are you sure to re import the deal?"))
	{
		var log_id=$(this).attr("log_id");
		var log_id_arr=new Array(log_id);
	
		$.post(site_url+'/vendor/jx_update_deal_import',{log_id:log_id_arr},function(res){
			
			if(res.status=='error')
			{
				alert(res.msg);
			}else{
				alert(res.msg);
			}
		},'json');
		
		
	}
});

$(".bulk_data_re_import").click(function(){

	if(confirm("Are you sure to re import the deal?"))
	{
		
		var log_id_arr=new Array();

		$(".p_check:checked").each(function(){
			log_id_arr.push($(this).val());
		});
		
		$.post(site_url+'/vendor/jx_update_deal_import',{log_id:log_id_arr},function(res){
			
			if(res.status=='error')
			{
				alert(res.msg);
			}else{
				alert(res.msg);
			}
		},'json');
		
		
	}
});

</script>

