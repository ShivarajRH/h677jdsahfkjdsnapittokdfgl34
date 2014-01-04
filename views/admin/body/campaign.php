<div class="container" align="left">
<h2>Campaign List 
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a href="<?=site_url("admin/campaign/create")?>" style="text-decoration: underline;font-size: 12px;">Create</a>
	- 
	<a href="<?=site_url("admin/campaign")?>" style="text-decoration: underline;font-size: 12px;">List all</a>
</h2>
<?php 
	$camp_notify = $this->session->flashdata('campaign_notify');
	if($camp_notify){
		echo '<div align="left" style="padding:3px;background: #ffffd0;font-weight:bold">'.$camp_notify.'</div>';
	}
?>
<div style="margin:10px">

<?php 
	if($action=='show'){
		$campaign_list = $this->db->query ( "select c.*,t.template_filename from king_campaigns c  
													join king_campaign_templates t on t.id = c.template_id		
												order by c.id desc" )->result_array (); 
		if(!$campaign_list){
			echo "No Campaigns added yet!";
		}else{
?>
<table class="table_grid_view" style="background:#fff;width: 70%" border=0 cellpadding=5 cellspacing=0>
	<thead>
		<th>#</th>
		<th>Campain no</th>
		<th>Campain Type</th>
		<th>Campain Title</th>
		<th>Campaign Template</th>
		<th>Campain Status</th>
		<th>CreatedOn</th>
		<th>Action</th>
	</thead>
	<tbody>
		<?php 
			$i=1;
			foreach($campaign_list as $camp){
			 
			?>
			<tr class="<?php echo (($i%2)?'even_row':'odd_row')?>">
				<td><?=$i++?>)</td>
				<td><?=anchor('admin/campaign/edit/'.$camp['campaign_no'],$camp['campaign_no'])?></td>
				<td><?=$camp['campaign_type']?></td>
				<td><?=$camp['title']?></td>
				<td><?=$camp['template_filename']?></td>
				<td><?=$camp['is_active']==1?"Active":"InActive"?></td>
				<td><?=date('d/M/Y g:ia',strtotime($camp['created_on']))?></td>
				<td>
					<a href="<?=site_url("admin/campaign/edit/{$camp['campaign_no']}")?>">Edit</a> |
					<a href="<?=site_url("campaigns/{$camp['campaign_no']}")?>" target="_blank">View Campaign</a>
				</td>
			</tr>
		<?php }?>
	</tbody>
</table>
<?php } ?>
<?php }elseif($action=='create' || $action=='edit'){
	
?>
	<h3 style="margin: 0px;">Create Campaign</h3>
	<form action="<?php echo site_url('admin/campaign/'.$action)?>" method="post" enctype="multipart/form-data">
		<table cellpadding="5" cellspacing="5" style="background: #e3e3e3">
			<tr>
				<td>Campaign no <span class="req">*</span></td>
				<td>
					<?php echo site_url('campaign').'/';?>
					
				<input type="text" class="inputbox" name="campaign_no" <?php echo (($action=='edit')?'readonly':'') ?> value="<?php echo (isset($campaign_det['campaign_no'])?$campaign_det['campaign_no']:'') ?>"></td>
			</tr>
			<tr>
				<td>Type <span class="req">*</span></td>
				<td><input type="text" size="60" class="inputbox" name="campaign_type" value="<?php echo (isset($campaign_det['campaign_type'])?$campaign_det['campaign_type']:'deal') ?>"></td>
			</tr>
			<tr>
				<td>Title <span class="req">*</span></td>
				<td><input type="text" size="60" class="inputbox" name="campaign_title" value="<?php echo (isset($campaign_det['title'])?$campaign_det['title']:'') ?>"></td>
			</tr>
			<tr>
				<td>Banner <span class="req">*</span></td>
				<td>
					<span style="font-size: 11px;">
					
						<?php 
							$use_default = 1;
							if(isset($campaign_det['banner_image'])){
								if($campaign_det['banner_image'] == 'default_banner.png'){
									$use_default = 1;
								}else{
									$use_default = 0;
								}
							}
						?>
					
						<input type="checkbox" name="default_banner" value="1" <?php echo ($use_default?'checked':'')?> > Use default
					</span>
					<b>OR</b>
					upload banner  (allowed only: jpg,png) 
					<input type="file" class="inputbox" name="campaign_other_img" value="">
					<br />
					<?php 
						if(isset($campaign_det['banner_image'])){
					?>
					<div style="padding:2px;">
						<img src="<?php echo base_url().'/images/newsletter/banners/'.$campaign_det['banner_image'];?>" height="30" />
					</div>
					<?php }?>
				</td>
			</tr>
			<tr>
				<td>Banner Link <span class="req">*</span></td>
				<td><input type="text" size="60" class="inputbox" name="campaign_banner_link" value="<?php echo (isset($campaign_det['banner_link'])?$campaign_det['banner_link']:base_url()) ?>"></td>
			</tr>
			<tr>
				<td>TemplateID <span class="req">*</span></td>
				<td>
					 
					<select name="template_id">
						<?php 
							
							$sql = "select * from king_campaign_templates where is_active = 1 ";
							$templ_res = $this->db->query($sql);
							if($templ_res->num_rows()){
								foreach($templ_res->result_array() as $tmpl){
									$selected_templ = '';
							//		if(isset($campaign_det['banner_img'])){
										if($tmpl['id'] == $campaign_det['template_id']){
											$selected_templ = 'selected';
										}
								//	}
						?>
							<option value="<?php echo $tmpl['id']?>" <?php echo $selected_templ;?>><?php echo $tmpl['template_name']."  ( filename:{$tmpl['template_filename'] } )"?></option>
						<?php 			
								}								
							}
						?>
					
					</select> Or <a href="javascript:void(0);cr_campaign_tmpl()">Create Template</a> 
				</td>
			</tr>
			<tr style="display: none;">
				<td>Campaign Duration</td>
				<td>
					From <input type="text" class="inputbox" name="campaign_start" value="<?php echo date('Y-m-d H:i:00')?>">
					To <input type="text" class="inputbox" name="campaign_end" value="<?php echo date('Y-m-d H:i:00',time()+(3600*24) )?>">
				</td>
			</tr>
			<tr style="display: none;">
				<td>Campaign Cycle</td>
				<td>
					<select name="campaign_cycle">
						<option value="6">6hrs</option>
						<option value="12">12 hrs</option>
						<option value="24">1 day </option>
						<option value="48">2 day</option>
						<option value="168">7 week</option>
					</select>
					 
				</td>
			</tr>
			<tr>
				<td>Make Active</td>
				
				<?php 
					$campaign_status = '';
					if(isset($campaign_det['is_active'])){
						if(1 == $campaign_det['is_active']){
							$campaign_status = 'checked';
						}
										 
					}
				?>
				<td><input type="checkbox" class="inputbox" name="is_active" value="1" <?php echo $campaign_status; ?> ></td>
			</tr>
		</table>
		<br />
		<h3 style="margin:0px">Deals for Campaign <span class="req">*</span></h3>
		<table class="table_grid_view" style="width: auto" cellpadding="0" cellspacing="0">
			<thead>
				<th>#</th>
				<th>Relative Link</th>
				<th>DealID</th>
				<th>Order</th>
				<th>Active</th>
			</thead>
			<tbody>
				<?php 
				 
					for($i=0;$i<20;$i++){
						$dealid = $status = $order = $relative_link = '';
						
						$order = $i+1;
						 
						if(isset($campaign_deal_list)){
							
							if(isset($campaign_deal_list[$i])){
								$dealid = $campaign_deal_list[$i]['deal_id'];
								$status = $campaign_deal_list[$i]['is_active'];
								$order = $campaign_deal_list[$i]['order'];
								$relative_link = $campaign_deal_list[$i]['relative_link'];
							}	
						}
						
				?>
				<tr class="<?php echo (($i%2)?'even_row':'odd_row');?>" >
					<td align="right" width="20">
						 <?php echo $i+1?>) 
					</td>
					<td width="130">
						<input type="text" class="relative_deallink" style="width: 350px" value="<?php echo $relative_link;?>" name="camp_deal_list[relative_link][]"> 	 
					</td>
					<td width="50">
						<input type="text" readonly="readonly" class="deallink_id" size="10" value="<?php echo $dealid;?>" name="camp_deal_list[id][]"> 	 
					</td>
					<td align="center"  width="20">
						<input type="text" size="4" name="camp_deal_list[order][]" value="<?php echo $order;?>"> 	 
					</td>
					<td align="center"  width="20">
						<select name="camp_deal_list[status][]">
							<option value="0" <?php echo ($status?'':'selected')?>>No</option>
							<option value="1" <?php echo ($status?'selected':'')?>>Yes</option>
						</select> 	 
					</td>
				</tr>
				<?php }?>
			</tbody>
		</table>
		<table width="600">
			<tr>
				<td colspan="2" align="right"><input type="submit" value="<?php echo (($action=='create')?'Create':'Update')?> Campaign"></td>
			</tr>
		</table>
	</form>
<?php 
}?>
</div>

<style type="text/css">
.req{
	color:#cd0000;
}
</style>

<div id="cr_campaign_template">
	<h3 style="margin: 5px 0px">Create Campaign Template</h3>
	<form id="cr_campaign_template_frm" action="<?php echo site_url('admin/cr_campaign_tmpl')?>" method="post"> 
		<table cellpadding="3" cellspacing="2">
			<tr>
				<td><b>Template Name</b></td>
				<td> <input type="text" name="tmpl_name" value=""></td>
			</tr>
			<tr>
				<td><b>Template Filename</b></td>
				<td> <input type="text" name="tmpl_filename" value=""></td>
			</tr>
			<tr>
				<td><b>Make Active</b></td>
				<td> <input type="checkbox" name="tmpl_is_active" value="1"></td>
			</tr>
			<tr>
				<td colspan="2" align="right"> <input class="sbutton" type="submit" value="Create"></td>
			</tr>
		</table>
	</form>
</div>
<div style="display:none">
	<div id="loading_dlg" align="center" >
		<p id="loading_dlg_action" style="font-size: 16px;"></p>
		<img id="loading_dlg_icon" src="<?php echo IMAGES_URL.'/scroll_load.gif'?>" height="30" />
	</div>
</div>
<style type="text/css">

.ui-dialog .ui-dialog-titlebar{
	display:none; 
}
#ui-datepicker-div{
	z-index:99999 !important;
	font-size:12px;
}
.ui-dialog{
	font-size:12px;
}
.sbutton{
	background: none repeat scroll 0pt 0pt rgb(255, 237, 0); color: rgb(0, 0, 0); font-weight: bold; padding: 3px;
}
</style>
<script type="text/javascript"> 
var site_url = '<?php echo site_url()?>';

function show_loading(msg){
	$('#loading_dlg_action').html(msg);
	$('#loading_dlg').dialog('open');
	$('#loading_dlg_icon').show();
}

function hide_loading(){
	$('#loading_dlg_action').html('');
	$('#loading_dlg').dialog('close');
	$('#loading_dlg_icon').hide(); 
}

function update_loading(resp){
	$('#loading_dlg_action').html(resp+'<br /><a href="javascript:void(0);hide_loading()" style="color:#cd0000;font-size:12px;margin-top:10px;">Close</a>');
	$('#loading_dlg_icon').hide();
}
$('#cr_campaign_template').dialog({autoOpen:false,modal:true});

	function cr_campaign_tmpl(){
		$('#cr_campaign_template').dialog('open');
	}

	$('#cr_campaign_template_frm').submit(function(){ 
		 
		var actn = $(this).attr('action');
		var pdata = $(this).serialize();
			show_loading("Creating Template Please wait...");
			$.post(actn,pdata,function(resp){
				var optns = '';
					$.each(resp.tmpl_list,function(i,item){
						optns+='<option value="'+item.id+'">'+item.template_name+'(filename:'+item.template_filename+')</option>';
					});
					$('select[name="template_id"]').html(optns);

					$('select[name="template_id"] option:last').attr('selected',true);
					hide_loading();
					$('#cr_campaign_template').dialog('close');
			},'json');
		return false;	
	});

	$('.relative_deallink').change(function(){
		var ele = $(this);
		var deallinkid_ele = $('.deallink_id',$(this).parent().parent());
		var status_ele = $('select[name="camp_deal_list[status][]"]',$(this).parent().parent());
			deallinkid_ele.val("Updating...");
		
		$.post(site_url+'/admin/getdealdetbyurl','url='+ele.val(),function(resp){
			if(resp.deal_det){
				deallinkid_ele.val(resp.deal_det.dealid);
				status_ele.val(1);
			}else{
				deallinkid_ele.val("");
				status_ele.val(0);
			}	
		},'json');
	});
	$('.relative_deallink').bind('keyup',function(){
		$(this).trigger('change');
	});
</script>

</div>
<?php
