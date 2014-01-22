<?php
	$manifesto_ttlbystatus = array(1=>0,2=>0,3=>0,4=>0);
	if($status_summary)
	{
		foreach($status_summary as $stat_det)
		{
			$manifesto_ttlbystatus[$stat_det['status']] = $stat_det['ttl'];
		}
	}	
?>


<style>
	.sel_invoice_list td{padding:5px;border:1px dotted #555;font-size:12px;font-family: arial}
	.show_invoice{min-width: 150px;}
	.frm_block td{font-size: 11px;}
	#man_selinv_summary{padding:6px;font-weight: bold;}
	
	.town_det{width: 100%;border-bottom: 1px solid #cdcdcd;font-size: 12px;}
	.town_det h4{padding:7px;background: #dfdfdf;font-size: 12px;margin:0px 0px;}
	.town_det h3{padding:3px 7px;background: #ffffc0;font-size: 11px;margin:0px 0px;}
	.tgl_blck{float: right;font-size: 18px;font-weight: bold;cursor: pointer;}
	.fr_invoice_det{display: inline-block;min-width: 120px;}
	.sel_invoice_list{max-height: 400px;overflow: auto;background: #FFF !important;padding:3px 4px}
	.ttl_inv_count{font-size: 12px;font-weight: bold;float:right;padding:3px;}
	.hide{display:none;}
	.highleting{border-radius: 3px 3px 3px 3px;float: right;
	    font-size: 15px;
	    font-weight: bold;
	    min-width: 60px;
	    padding: 2px 4px;
	    text-align: center;
    }
    .leftcont{display: none}
    .page_wrap .page_topbar .page_topbar_left{width: 19%;}
    .page_wrap .page_topbar .page_topbar_right{width: 79%;}
    .stats{padding:3px;}
    .stats a{padding:5px 10px;border:1px dotted #ffffa0;background: #ffffd0;color: #454545;margin-right: 2px;display: inline-block;}
    .stats a.warn{background: #cd0000;color:#FFF}
</style>
<div class="page_wrap container">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Modify/Print Manifesto</h2>
		</div>
		<div class="fl_right stats" >
		
			<!-- add handle by alternate number  -->
			<?php
				if($this->erpm->auth(true,true))
				{?>
					<button id="add_alternative_nukber">Add alternative number</button>											
			<?php } 
				?>
			<!-- add handle by alternate number  -->
		
			<a href="<?php echo site_url('/admin/view_manifesto_sent_log/0/0000-00-00/0000-00-00/1/0'); ?>"  class="<?php echo  ($manifesto_ttlbystatus[1])?'warn':''?>">Pending : <b><?php echo $manifesto_ttlbystatus[1]; ?></b></a>
			
			<a href="<?php echo site_url('/admin/view_manifesto_sent_log/0/0000-00-00/0000-00-00/2/0'); ?>" class="<?php echo  ($manifesto_ttlbystatus[2])?'warn':''?>">Outscanned : <b><?php echo $manifesto_ttlbystatus[2]; ?></b></a>
			
			<a href="<?php echo site_url('/admin/view_manifesto_sent_log/0/0000-00-00/0000-00-00/3/0'); ?>">Shipped : <b><?php echo $manifesto_ttlbystatus[3]; ?></b></a>
			
			<a href="<?php echo site_url('/admin/view_manifesto_sent_log/0/0000-00-00/0000-00-00/4/0'); ?>">Cancelled : <b><?php echo $manifesto_ttlbystatus[4]; ?></b></a>
			
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="page_topbar_left fl_left" >
			<span class="total_overview">Total Manifesto: <b><?php echo $manifesto_send_smry_ttl; ?> </b> </span>
		</div>
		<div class="page_action_buttonss fl_right" align="right">
			<form action="" method="post" id="manifesto_filter_form">
				Search by invoice / Manifesto id: <input type="text" name="src_invoice">&nbsp;
				
				Date range : <input type="text" class="inp fil_style" size=10 id="from_date" name="from"> 
				to <input type="text" class="inp fil_style" size=10 id="to_date" name="to" > &nbsp;
				
				<?php $status=array('Pending','Scanned','Shipped','Cancelled')?>
					Status : <select name="status">
						<option value="0">Choose</option>
						<?php foreach($status as $i=>$s){?>
						<option value="<?php echo $i+1 ?>"><?php echo $s;?></option>
						<?php }?>	
					</select>&nbsp;
					
				<?php $hub_names=$this->db->query("select id,hub_name from pnh_deliveryhub order by hub_name")->result_array();?>
				Hubs : 	<select name="hubs">
							<option value="0">Choose</option>
							<?php foreach($hub_names as $hub){ ?>
							<option value="<?php echo $hub['id']; ?>"><?php echo $hub['hub_name'];?></option>
							<?php } ?>
						</select>
						
						<select name="view_option">
								<option value="1">View</option>
								<option value="2">Download</option>
							</select>	
				<input type="submit" value="submit" >	
			</form>
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<table width="100%" cellpadding="5" cellspacing="0" class="datagrid">
			<thead>
				<tr>
					<th width="3%">Manifesto refno</th>
					<th width="3%">Invoices Value</th>
					<th width="6%">Processed on</th>
					<th width="8%">Processed by</th>
					<th width="18%">Transporter</th>
					<th width="18%">To be collected by @ destination</th>
					<th width="8%">Total invoices</th>
					<th>Remark</th>
					<th>Status</th>
					<th width="15%" id="actions">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					if($mainfesto_sent_det)
					{
						$show_lr_update=0;
						foreach($mainfesto_sent_det as $i=>$sent_det)
						{
							//$driver_name=($sent_det['hndlby_roleid'])?$sent_det['driver_name']:$sent_det['hndleby_name'];
							//$mobile=($sent_det['hndlby_roleid']==0)?$sent_det['hndleby_contactno']:$sent_det['contact_no'];
							if($sent_det['hndlby_roleid'])
							{
								$transporter_name=$sent_det['driver_name'];
							}else if($sent_det['hndlby_type']==4){
								$transporter_name="Courier - ".$sent_det['courier_name'];
							}else{ 
								
								$transporter_name='Bus Transport - <a href="javascript:void(0)" bus_id="'.$sent_det['bus_id'].'" dest_id="'.$sent_det['bus_destination'].'" manifesto_id="'.$sent_det['id'].'" class="show_destination_address_det">Show details</a><br><a href="javascript:void(0)" manifesot="'.$sent_det['id'].'" class="show_office_pickup_deatils">Office pickup details</a>';
							
							?>
							<script>
								$("#actions").html('');
								$("#actions").html('Actions <input type="checkbox" name="select_all">');
							</script>
							<?php }
							$mobile=($sent_det['hndlby_roleid']==0)?0:'('.$sent_det['contact_no'].')';
							
							$emp_id=$sent_det['hndleby_empid'];
							
							$tranporter_link='';
							if($emp_id)
							{
								$tranporter_link='<a href="'.site_url("/admin/view_employee/$emp_id").'" target="_blank">'.$transporter_name.'</a>';
							}else{
								$tranporter_link=$transporter_name;
							}
							
							$manifesto_total_amt = @$this->db->query("select round(sum((c.i_orgprice)*c.quantity)) as amt 
																				from shipment_batch_process_invoice_link a
																				join king_invoice b on a.invoice_no = b.invoice_no 
																				join king_orders c on c.id = b.order_id 
																				where a.inv_manifesto_id  = ?   
																			group by a.inv_manifesto_id ",$sent_det['manifesto_id'])->row()->amt*1;
																			
																			
							$man_hub_names = $this->db->query("select group_concat(distinct d.hub_name) as hub_names 
																	from pnh_m_manifesto_sent_log a 
																	join pnh_t_tray_invoice_link b on find_in_set(b.invoice_no,a.sent_invoices)
																	left join pnh_t_tray_territory_link c on c.tray_terr_id = b.tray_terr_id    
																	join pnh_deliveryhub d on d.id = c.territory_id 
																	where a.id = ? ",$sent_det['id'])->row()->hub_names;																		
							
				?>
							<tr>
								
								<td><span class="highleting"><b><?php echo $sent_det['id']?><b></b></span></td>
								<td width="80"><b><?php echo ($manifesto_total_amt?'Rs '.formatInIndianStyle($manifesto_total_amt):'');?></b></td>
								<td width="80"><?php echo format_date($sent_det['sent_on']);?></td>
								<td><?php echo $sent_det['sent_by'];?></td>
								<td width="200">
									<?php echo $tranporter_link;?><br>
									<span><?php echo ($mobile)?$mobile.'<br>':'';?></span>
									<?php echo $sent_det['hndlby_roleid']?$sent_det['role_type'].'<br>':'';?>
									<?php echo $sent_det['hndlby_roleid']?'<a href="javascript:void(0)" class="show_vehicle_det" m_id="'.$sent_det['id'].'">Vehicle details</a><br>':'';?>
								</td>
								<td width="400" ><?php echo ($sent_det['pick_up_by'])?'<a href="'.site_url('admin/view_employee/'.$sent_det['pickup_empid']).'" target="_blank">'. $sent_det['pick_up_by'].'('.$sent_det['pick_up_by_contact'].')</a>' : '';?>
									<?php 
										if($sent_det['pick_up_by'])
										{
											echo ' <div><b>Destination : </b> '.$sent_det['dest_shortname'].'</div>';
										}
										
										echo '<div><b>Delivery Hubs : </b>'.$man_hub_names.'</div>';
										
										if($sent_det['hndlby_type']==3)
										{
											echo '<div><a href="javascript:void(0)" onclick="print_del_label('.$sent_det['id'].')" >Print Delivery Label </a></div>';	
										}
										
										echo '<div><a href="javascript:void(0)" onclick="print_franchise_label('.$sent_det['id'].')" >Print Franchise Labels </a></div>';
										
										
									?>	
								</td>
								<td>
									<?php echo count(explode(',',$sent_det['sent_invoices']));?><br>
									<a href="javascript:void(0)" onclick="show_invoices_dlg(this)" invoices="<?php echo $sent_det['sent_invoices']; ?>" manifesto_id="<?php echo $sent_det['id']; ?>">View Invoices</a>
								</td>
								<td><?php echo $sent_det['remark'];?></td>
								<td>
									<?php if($sent_det['status']==1)
												echo '<span style="color:red;"><b>Pending</b></span>';
											else
												if($sent_det['status']==2)
													echo '<span style="color:orange;"><b>Scanned</b></span>';
											else if($sent_det['status']==3)
													echo '<span style="color:green;"><b>Shipped</b></span>';
											else if($sent_det['status']==4)
													echo '<span style="color:blue;"><b>Cancelled</b></span>';
									?>
								</td>
								<td>
									<?php if($sent_det['status']!=4){?>
										
										<!-- manifesto modification button -->
										<?php if($sent_det['status']==1){?>
										<input type="button" value="Modify" onclick="sent_manifesto_edit(<?php echo $sent_det['manifesto_id'].','.$sent_det['id']?>)" id="<?php echo $sent_det['manifesto_id']; ?>"><br>
										<a href="<?php echo site_url('/admin/cancel_manifesto/'.$sent_det['id'])?>" id="cancel_manifesto">Cancel</a><br>
										<?php } ?>
										<!-- manifesto modification button end-->
										
										<!-- manifesto print button -->
										<?php if($sent_det['status']!=1){?>

<?php if($this->db->query("select count(*) as t 
	from shipment_batch_process_invoice_link e 
	join pnh_m_manifesto_sent_log f on f.manifesto_id = e.inv_manifesto_id
	join proforma_invoices i on i.p_invoice_no = e.p_invoice_no 
	join king_orders c on c.id = i.order_id 
	join king_dealitems d on d.id = c.itemid 
	join king_deals a on a.dealid = d.dealid 
	where f.id = ? and ((dispatch_id > 0 and menuid != 112 ) or dispatch_id = 0 )  ",$sent_det['id'])->row()->t) { ?>

												<input type="button" value="<?php echo ($sent_det['is_printed'])?'Reprint':'Print'; ?>"  onclick="print_sent_manifesto(<?php echo $sent_det['manifesto_id'].','.$sent_det['id']?>)"><br>

<?php }	?>

<?php if($this->db->query("select count(*) as t 
	from shipment_batch_process_invoice_link e 
	join pnh_m_manifesto_sent_log f on f.manifesto_id = e.inv_manifesto_id
	join proforma_invoices i on i.p_invoice_no = e.p_invoice_no 
	join king_orders c on c.id = i.order_id 
	join king_dealitems d on d.id = c.itemid 
	join king_deals a on a.dealid = d.dealid and menuid = 112 
	where f.id = ? and dispatch_id > 0 ",$sent_det['id'])->row()->t) { ?>
												<input type="button" value="<?php echo ($sent_det['is_printed'])?'Reprint':'Print'; ?> Dispatch Manifesto"  onclick="print_sent_newmanifesto(<?php echo $sent_det['manifesto_id'].','.$sent_det['id']?>)"><br>

										<?php }	?>




										<?php }	?>
										<!-- manifesto print button end-->
										
										<!-- update vehicle details button and lr number update button-->
										<?php if($sent_det['status']!=1 && $sent_det['bus_id']==0 && $sent_det['hndlby_roleid']){ ?>
												<input type="button" value="Update vehicle details" onclick="show_driver_details(<?php echo $sent_det['id'].','.$mobile.",'".strip_tags($tranporter_link)."'".','.$sent_det['job_title2']; ?>)" <?php echo ($sent_det['status']==3)?'class="hide"' :'""'; ?>>
										<?php }else if($sent_det['status']==2 && ($sent_det['bus_id']!=0 || $sent_det['hndlby_type']==4 ) && ($sent_det['office_pickup_empid'] || $sent_det['hndleby_courier_id'] )  ){
												echo '<br><b>LR number needed</b>';?>
												<br>
												<a href="<?php echo site_url('admin/update_bulk_lrdetails');?>">Update LR number</a>
												<?php /*
												<input type="button" value="Update LR number" onclick="show_lr_update_form(<?php echo $sent_det['id'].','.$sent_det['hndleby_courier_id']; ?>)"><br>
												 */?>
												 
										<!-- update vehicle details button and lr number update button-->		
										<?php }
										
										//office pick up det update button
										if($sent_det['bus_id']!=0 && !$sent_det['office_pickup_empid'] && $sent_det['status']!=1){	
											$show_lr_update=1;
										?>		<input type="button" value="Update office pick up details" onclick="show_office_pickup_det(<?php echo $sent_det['id']; ?>)">
												<input type="checkbox" value="<?php echo $sent_det['id']; ?>" name="update_lr" class="update_lr">
										<?php }?>
										
										<!-- pick list outscan link -->
										<?php if($sent_det['status']==1){?>
												<a style="text-decoration: underline;" href="<?php echo site_url('/admin/pick_list_for_pending_shipments/'.$sent_det['id'])?>" target="_blank">Pick list</a><br>
												<a style="text-decoration: underline;" href="<?php echo site_url('/admin/pnh_outscan_pending_shipents/'.$sent_det['id'])?>" target="_blank">Outscan shipments</a>
										<?php }	?>
										<!-- pick list outscan link end-->
										
										<!-- manualy mark delivered option link -->
										<?php
											 if($sent_det['status']==3 && $this->erpm->auth(PNH_SHIPMENT_MANAGER,true))
											{?>
												<a style="text-decoration: underline;" href="javascript:void(0)"  onclick="show_mark_delivered_dlg(this)" send_log_id="<?php echo $sent_det['id']; ?>" invoices="<?php echo $sent_det['sent_invoices'];?>" >Mark Delivered</a>
										<?php }?>
										<!-- manualy mark delivered option link end-->
										
										
									<?php }else{
										echo 'Cancelled On :'.format_datetime($sent_det['modified_on']).'<br>';
										echo 'Cancelled by :'.$this->db->query("select username from king_admin where id=?",$sent_det['modified_by'])->row()->username.'<br>';
									}	?>
								</td>
							</tr>
							
				<?php	}
				if($show_lr_update){
				?>
				<tr>
					<td colspan="10" align="right">
						<input type="button" value="Update office pick up details" id="m_update_office_pick_up_details">
					</td>
				</tr>
				<?php }?>
			</tbody>
		</table>
	</div>
	<div align="left" class="pagination">
			<?php echo $manifesto_send_smry_pagi?>
	</div>
		<?php }else{
			echo "No manifesto send log found";
		}?>
</div>

<form id="sent_mainifestoprint" target="hndl_ganmanifestoprint" action="<?php echo site_url('admin/gen_manifestoprint')?>" method="post">
	<input type="hidden" name="id" value="0">
	<input type="hidden" name="sent_id" value="0">
</form>


<!-- Manifesto update dialogbox -->
	<div id="update_manifesto_dlg" title="Update Shipment transport details">
		<form action="<?php echo site_url('admin/update_manifesto_detail')?>" method="post" id="manifesto_update_form">
			<input type="hidden" name="manifest_log_id" value="0">
			<input type="hidden" name="manifest_log_sent_id" value="0">
			<div id="mani_invoice_nos"></div>
			<div id="mani_invoice_nos_by_town"></div>
		</form>
	</div>
<!-- Manifesto update dialogbox end -->
	
<!-- Modal for Invoices list -->
<div id="invoices_list_dlg" title="Selected Invoices for shipment">
</div>
<!-- Modal for Invoices list -->	
	
<!-- Modal Driver details update -->
<div id="update_driver_detils" title=" Update Transporter detail">
	<form action="<?php echo site_url('admin/update_driver_details_in_sent_manifesto')?>" method="post" id="update_driver_det_frm">
		<input type="hidden" name="manifest_log_sent_id" value="0">
		<div id="driver_details">
		</div>
	</form>
</div>
<!-- Modal Driver details update end -->	

<!-- Modal for update lt number -->
<div id="update_lr_number_details" title=" update lr number">
	<form action="<?php echo site_url("admin/update_lr_number_in_sent_manifesto") ?>" method="post" id="update_lr_number_form">
		<input type="hidden" name="manifest_log_sent_id" value="0">
		<div id="lr_number_update">
		
		</div>
	</form>
</div>
<!-- Modal for update lt number end-->

<!-- bus transport_details -->
<div id="bus_tranaport_details" title=" Bus Transport details">
</div>
<!-- bus transport_details end-->

<!-- vehicle details modal -->
<div id="vehicle_details" title="Vehicle Details"></div>
<!-- vehicle details modal ens-->

<!-- Update office pick up details modal -->
<div id="update_office_pickup" title="Office pick up details">
	<form action="<?php echo site_url('admin/update_office_pick_up_details')?>" id="update_office_pickup_form" method="post">
	</form>
</div>
<!-- Update office pick up details modal end-->

<!-- office pickup list modal -->
<div id="office_pick_up_det" title="Office pick up list">
	<div id="office_pick_up_det_inner"></div>
</div>
<!-- office pickup list modal end-->

<!-- mark delivered -->
<div id="mark_delivered_courier_transport" title="Update delivered status">
	<form id="mark_delivered_courier_transport_form" action="<?php echo site_url('admin/jx_mark_delivered_status_for_courier_traspost')?>" method="post">
	</form>
</div>
<!-- mark delivered end--> 


<!-- add alternative number -->
<div id="add_alternative_number" title="Add alternative number">
	<form action="<?php echo site_url('admin/pnh_add_manifesto_alternative_number') ?>" method="post" id="add_alternative_number_form">
		<table cellpadding="5" cellspacing="0" class="datagrid" width="100%">
			<tbody>
				<tr>
					<td>Manifesto id : </td>
					<td><input type="text" name="ch_manifesto_id">&nbsp;&nbsp;<a href="javascript:void(0)" id="ch_manifesto_id">check</a></td>
				</tr>
			</tbody>
		</table>
		<div id="det_box">
		</div>
	</form>
</div>

<style>.error_inp{border:2px solid #cd0000 !important;}</style>
<!-- add alternative number end -->

<script>
prepare_daterange('from_date','to_date');

$("#by_date_range_frm").submit(function(){
	var from=$("#from_date",this).val();
	var to=$("#to_date",this).val();

	if(from=='' || to=='')
	{
		alert("Please check your date range");
		return false;
	}	
});

$("#srch_by_invoice_num").submit(function(){
	var kwd=$("input[name=src_invoice]",this).val();

	if(kwd.length==0)
	{
		alert("Please enter invoice number");
		return false;
	}

	if(isNaN(kwd))
	{
		alert("Please check given invoice number");
		return false;
	}
});

$("#filter_by_status_form").submit(function(){
	var status=$("select[name='status']",this).val();
	
	if(status==0)
	{
		alert('please choose status');
		return false;
	}
	return true;
});

function print_sent_manifesto(id,sent_id){

	var p=confirm("Are you sure want to print this modifesto?");
	if(p)
	{	var modify_btn='#'+id;
		$(modify_btn).hide();
		$('#sent_mainifestoprint input[name="id"]').val(id);
		$('#sent_mainifestoprint input[name="sent_id"]').val(sent_id);
		$('#sent_mainifestoprint').submit();
	}else{
			return false;
		}
}


function print_sent_newmanifesto(id,sent_id){

	var p=confirm("Are you sure want to print this modifesto?");
	if(p)
	{	
		window.open(site_url+"/admin/gen_manifestoprintbydispatch/"+sent_id)
	}else
	{
		return false;
	}
}

function sent_manifesto_edit(manifesto_id,manifesto_sent_id)
{
	$('#update_manifesto_dlg').data({'manifesto_id':manifesto_id,'manifesto_sent_id':manifesto_sent_id}).dialog('open');
}

/*
//get the manifesto invoices
$('#update_manifesto_dlg').dialog({

	autoOpen:false,
	modal:true,
	height:'auto',
	autoResize:true,
	width:900,
	open:function(){

		$('#mani_invoice_nos',this).html('');
		var dlgEle = $(this); 
		
		$.post(site_url+'/admin/jx_get_terrtownby_invlist','manifesto_id='+$(this).data('manifesto_id'),function(resp){
			var sel_invlist_html = '<div id="man_selinv_summary">Total Shipment Selected : <b></b></div>';	
			sel_invlist_html += '<div style="max-height:200px;overflow:auto"><table class="sel_invoice_list">';
			 
				$.each(resp.invlist,function(k,invdet){
					if(k==0)
						sel_invlist_html += '<tr>';
					else	
					if(k%5==0 && k > 0)
						sel_invlist_html += '</tr><tr>';
						
					sel_invlist_html += '<td class="show_invoice" valing="top"><div><input type="checkbox" name="invoice_nos[]" value="'+invdet.invoice_no+'" checked ><b>'+invdet.invoice_no+'</b><br /><span style="font-size:10px;color:#555;">'+invdet.franchise_name+'</span></div></td>';
					 
				});
				
			sel_invlist_html += '</tr></table></div>';
			sel_invlist_html += resp.frm_transporter_det;
  			
			$('#mani_invoice_nos',dlgEle).html(sel_invlist_html).show();
			$('#man_selinv_summary b',dlgEle).html(resp.invlist.length);
			select_transport(document.getElementById('Transport_opts'));	
					  
		},'json');
		
		$('div[aria-describedby="update_manifesto_dlg"]').css({'position':'fixed','top':'30px'});
		
	},
	buttons:{
		'Modify' : function(){
			$('#update_manifesto_dlg input[name="manifest_log_id"]').val($('#update_manifesto_dlg').data('manifesto_id'));
			$('#update_manifesto_dlg input[name="manifest_log_sent_id"]').val($('#update_manifesto_dlg').data('manifesto_sent_id'));
			
			var c=confirm("Are you sure to modify this manifesto");
			if(c)
				$('form',this).submit();
			else
				return false;
		},
		'Cancel':function(){
			$(this).dialog('close');
		}
	}
});
//update manifesto dlg end
*/

//get the manifesto invoices
$('#update_manifesto_dlg').dialog({

	autoOpen:false,
	modal:true,
	height:'auto',
	autoResize:true,
	width:1000,
	open:function(){

		$('#mani_invoice_nos',this).html('');
		var dlgEle = $(this); 
		
		$.post(site_url+'/admin/jx_get_terrtownby_invlist','manifesto_id='+$(this).data('manifesto_id'),function(resp){
			var sel_summary_html='<div id="man_selinv_summary">Total Shipment Selected : <b></b></div><div>';	
			$.each(resp.sorted_towns,function(town_name,town_id){
				var twn_det = resp.inv_town_link[town_id];
					sel_summary_html+='<div class="town_det">';
					sel_summary_html+='	<h4>'+twn_det.name+'<span class="tgl_blck">&plus;</span><span style="font-size: 9px;font-weight: bold;float:right;padding:3px;margin-top:2px;">Edit-</span><span class="ttl_inv_count">'+' ( '+twn_det.ttl_inv+' ) </span></h4>';
					sel_summary_html+='	<div class="town_fran_list" style="display:none">';

					$.each(twn_det.franchises,function(fr_id,fr_det){
						sel_summary_html+="<h3>"+fr_det.name+"</h3>";	
						sel_summary_html+='<div class="sel_invoice_list">';
						$.each(fr_det.invoices,function(l,invoice){
							sel_summary_html += '<span class="fr_invoice_det"><input type="checkbox" name="invoice_nos[]" value="'+invoice+'" checked ><b>'+invoice+'</b></span> ';
						});
						sel_summary_html += '</div>';
					});
					sel_summary_html+="	</div>";
					sel_summary_html+='</div>';
			});
				
			sel_summary_html+='</div>';
			sel_summary_html += resp.frm_transporter_det;
			$('#mani_invoice_nos_by_town',dlgEle).html(sel_summary_html).show();
			$('#man_selinv_summary b',dlgEle).html(resp.invlist.length);
			select_transport(document.getElementById('Transport_opts'));

			if(!$('#shiping_date').hasClass('hasDatepicker'))
			{
				$('#shiping_date').datepicker();
			}	
					  
		},'json');
		
		$('div[aria-describedby="update_manifesto_dlg"]').css({'position':'fixed','top':'30px'});
		
	},
	buttons:{
		'Modify' : function(){
			$('#update_manifesto_dlg input[name="manifest_log_id"]').val($('#update_manifesto_dlg').data('manifesto_id'));
			$('#update_manifesto_dlg input[name="manifest_log_sent_id"]').val($('#update_manifesto_dlg').data('manifesto_sent_id'));
			
			var c=confirm("Are you sure to modify this manifesto");
			if(c)
				$('form',this).submit();
			else
				return false;
		},
		'Cancel':function(){
			$(this).dialog('close');
		}
	}
});
//update manifesto dlg end


//manifesto update form validation
$("#manifesto_update_form").submit(function(){
	var check_boxes=0;
	var trans_opt=$("select[name=transport_opts]",this).val();
	var drive_name='';
	var mobile_num='';
	var vehicle_number=$("input[name=vehicle_num]",this).val();
	//var drive_name=$("#other_driver").val().length;
	//var mobile_num=$("input[name=other_driver_ph]").val();
	var shipdate=$("input[name=shiping_date]").val();
	
	$("input[name=\"invoice_nos[]\"]",this).each(function(){
			if($(this).attr("checked"))
			{
				check_boxes=1;
			}
		});

	if(check_boxes==0)
	{
		alert("Please select atleast one invoice");
		return false;
	}

	
	if(trans_opt=='Choose')
	{
		alert('Please select transport');
		return false;
	}

	if($("select[name=drivers_list]",this).val()=='choose' && $("select[name=drivers_list]",this).is(":visible"))
	{
		alert("Please select driver name");
		return false;
	}

	if($("select[name=field_cordinators_list]",this).val()=='choose' && $("select[name=field_cordinators_list]",this).is(":visible"))
	{
		alert("Please select field cordinator");
		return false;
	}

	if($("select[name=buses_list]",this).val()=='choose' && $("select[name=buses_list]",this).is(":visible"))
	{
		alert("Please select bus transport");
		return false;
	}

	if($("select[name=bus_det_add]",this).val()=='choose' && $("select[name=bus_det_add]",this).is(":visible"))
	{
		alert("Please select bus transport destination");
		return false;
	}
	
	
	if($("select[name=tr_tranport_type]",this).val()=='choose' && $("select[name=tr_tranport_type]",this).is(":visible"))
	{
		alert("Please select  transportation type");
		return false;
	}

	if( ($("select[name=fr_list]",this).val()=='' && $("select[name=fr_list]",this).is(":visible") && ( $("select[name=excutives_list]",this).is(':hidden') && $("select[name=territory_manager]",this).is(':hidden') ) ) || ($("select[name=excutives_list]",this).val()=='' && $("select[name=excutives_list]",this).is(":visible")) || ($("select[name=territory_manager]",this).val()=='' && $("select[name=territory_manager]",this).is(":visible")) )
	{
		alert("Please select to be collected by @ destination");
		return false;
	}

	if(vehicle_number.length==0  && $("input[name=vehicle_num]",this).is(":visible"))
	{
		alert("Please enter vehicle number");
		return false;
	}

	if(drive_name==0 && $("#other_driver").is(":visible"))
	{
		alert("Please enter other transport");
		return false;
	}

	if($("input[name=other_driver_ph]").is(":visible"))
	{
		if(mobile_num.length==0)
		{
			alert("Please enter phone number");
			return false;
		}else if(isNaN(mobile_num))
		{		alert('Invalid phone number');
				return false;
		}else if(mobile_num.length <=9)
		{
			alert('Invalid phone number');
			return false;
		}
	}

	if($("select[name=courier_list]",this).val()==0 && $("select[name=courier_list]",this).is(":visible"))
	{
		alert("Please select Courier list");
		return false;
	}
	
	if(shipdate.length==0)
	{
		alert("Please choose shipdate");
		return false;
	}

});


//transport options
function select_transport(ele)
{
	var value=$(ele).attr("value");
	$("#pick-up-by-blk").hide();
	$("#vehicle_no").hide();
	$("select[name=buses_list]").remove();
	$("select[name=bus_det_add]").remove();
	//$("#pick-up-by").html('');

	$(".trans_opt_blk").hide();
	if(value=='Choose')
	{
		$(".trans_opt_blk").hide();
	}

	if(value==0)
	{
		$("#pick-up-by-blk").show();

		if($("select[name='fr_list']").val()=='')
		{
			$(".pickup_options").trigger('click');
		}
	}

	if(value==7)
	{
		$("#drivers_list_blk").show();
		$("#vehicle_no").show();
	}else if(value==6)
		$("#field_cordinators_list_blk").show();
	else if(value==0)
	{
		$("#other_trans").show();

		var tt_id = $('select[name="tr_tranport_type"]').attr('tt_id');
		$('select[name="tr_tranport_type"]').attr('tt_id',0);
		$('select[name="tr_tranport_type"]').val(tt_id).trigger('change');
		
	}else if(value==4)
	{
		$("#courier_opt_blk").show();
	}
}

$('.tgl_blck').live('click',function(e){
	e.preventDefault();
	if($(this).parent().parent().find('.town_fran_list').is(':visible'))
	{
		$(this).parent().parent().find('.town_fran_list').hide();
		$(this).html("&plus;");
	}else
	{
		$(this).parent().parent().find('.town_fran_list').show();
		$(this).html("&minus;");
	}
});

//choose tranport type
$("select[name=tr_tranport_type]").live('change',function(){
	var transport_type=$(this).val();
	var html_cnt='';
	$("select[name=buses_list]").remove();
	$("select[name=bus_det_add]").remove();
	if(transport_type!='choose')
	{
		$.post(site_url+'/admin/jx_get_buses_list',{tranport_type:transport_type},function(res){
			html_cnt+='<select name="buses_list" id="busues_list" style="margin:2px;"><option value="choose">Choose Bus</option>';
			$.each(res.buses_list,function(a,b){
				html_cnt+='<option value="'+b.id+'">'+b.name+'('+b.contact_no+')</option>';	
			});
			html_cnt+='</select>';
			$(html_cnt).appendTo("#other_trans");

			$("select[name=buses_list]").val($('select[name="tr_tranport_type"]').attr('bus_id')).trigger('change');
			$('select[name="tr_tranport_type"]').attr('bus_id',0);
			
		},'json');
	}
});

//show bus transport
$("select[name=buses_list]").live('change',(function(){
	var trs_id=$(this).val();
	var transport_type_id=$("select[name=tr_tranport_type]").val();
	var html_cnt='';
	$("select[name=bus_det_add]").remove();
	$.post(site_url+'/admin/jx_get_bustrs_des_address',{bus_id:trs_id,transport_type:transport_type_id},function(res){
		html_cnt+='<select name="bus_det_add" style="margin:2px;"><option value="choose">choose Destination</option>';
		$.each(res.dest_address_list,function(a,b){
			var contact_det=b.contact_no.split(',');
			html_cnt+='<option value="'+b.id+'">'+b.short_name+'('+contact_det[0]+')</option>';
		});	
		html_cnt+='</select>';
		$(html_cnt).appendTo("#other_trans");

		$("select[name=bus_det_add]").val($('select[name="tr_tranport_type"]').attr('bus_dest_id'));
		$('select[name="tr_tranport_type"]').attr('bus_dest_id',0);
	},'json');
}));

$('select[name="excutives_list"]').live('change',function(){
	$('select[name="fr_list"]').val('');
});

$('select[name="fr_list"]').live('change',function(){
	$('select[name="excutives_list"]').val('');
	$('select[name="territory_manager"]').val('');
	$('select[name="pickup_options"]').val(0);
	$(".excutives_list").hide();
	$(".territory_manager").hide();
	
});

$(".pickup_options").live('click',function(){
	$(".excutives_list").hide();
	$(".territory_manager").hide();
	$('select[name="fr_list"]').val('');

	var emp_list=$(this).attr('emp_list');
	if(emp_list=='Executives')
		$(".excutives_list").show();
	else if(emp_list=='Territory Manager')
		$(".territory_manager").show();
});

//invoice scan option
function show_invoices_dlg(event)
{
	var invoices=$(event).attr('invoices');
	var manifesto_id=$(event).attr('manifesto_id');
	
	$('#invoices_list_dlg').data({'invoices':invoices,'manifesto_id':manifesto_id}).dialog('open');
}

//invoices list modal
$('#invoices_list_dlg').dialog({
	autoOpen:false,
	modal:true,
	height:'480',
	width:'700',
	autoResize:true,
	open:function(){
		$('#invoices_list_dlg').html('');
		var table_html='';
		var invoices=$(this).data('invoices').split(',');
		var manifesto_id=$(this).data('manifesto_id');

	$.post(site_url+'/admin/jx_get_territory_by_invoices',{invoices:$(this).data('invoices'),manifesto_id:manifesto_id},function(resp){
			table_html+='<h3>Selected Invoices for Shipment</h3><table class="datagrid" cellpadding="5" cellspacing="0">';
			table_html+="<thead>";
				table_html+="<tr>";
				table_html+="<th>#</th>";
				table_html+="<th>Territory</th>";
				table_html+="<th>Town</th>";
				table_html+="<th>Invoice</th>";
				table_html+="<th>AWb</th>";
				table_html+="</tr>";
			table_html+="</thead>";
			table_html+="<body>";

			$.each(resp.territory_by_inv,function(a,b){
						
					table_html+='<tr>';
					table_html+=   '<td>'+(a+1)+'</td>';
					table_html+=   '<td><span>'+b.territory_name+'</span></td>';
					table_html+=   '<td>'+b.town_name+'</span></td>';
					table_html+=   '<td>';
					temp =new Array();
					$.each(b.invoice_no.split(','),function(c,d){
						temp.push('<a href="'+site_url+'/admin/invoice/'+d+'" target="_blank">'+d+'</a>');
						});
					table_html+='<span>'+temp.join(',')+'</span>';
					table_html+=   '</td>';	
					var awb=resp.lrno?resp.lrno:'';			
					table_html+=   '<td>'+awb+'</td>';
					table_html+='</tr>';
			});
					
			table_html+="</body>";
			table_html+="</table>";
			$('#invoices_list_dlg').append(table_html);

			},'json');
		
			
		
		},
		
	buttons:{
		'Close':function(){
			$(this).dialog('close');
		}
	}
	});

//invoices list modal

//driver update modal 
$('#update_driver_detils').dialog({
	autoOpen:false,
	modal:true,
	height:'320',
	width:'480',
	autoResize:true,
	open:function(){
		$('#driver_details').html('');
		var table_html='';
		var driver_name=$(this).data('driver_name');
		var mobile=$(this).data('mobile');
		var sent_id=$(this).data('sent_id');
		var job_title=$(this).data('job_title');
		
		table_html+='<table class="datagrid" width="100%">';
		table_html+='	<thead><tr><th colspan="2">Vehicle Details</th></tr></thead>';
		table_html+='	<tbody>';
		table_html+='       <tr>';
		table_html+='       	<td>Name:</td>';
		table_html+='       	<td>'+driver_name+'</td>';
		table_html+='       </tr>';
		if(mobile)
		{
			table_html+='       <tr>';
			table_html+='       	<td>Contact:</td>';
			table_html+='       	<td>'+mobile+'</td>';
			table_html+='       </tr>';
			if(job_title!=6)
			{
				table_html+='       <tr>';
				table_html+='       	<td>Vechicle Start KM:</td>';
				table_html+='       	<td><input type="text" size:"10" name="start_km"></td>';
				table_html+='       </tr>';
			}
		}
		table_html+='       <tr>';
		table_html+='       	<td>Amount:</td>';
		table_html+='       	<td><input type="text" size:"10" name="amount"><input type="hidden" value="'+sent_id+'" name="manifesto_sent_id"></td>';
		table_html+='       </tr>';
		table_html+='       <tr>';
		table_html+='       	<td>Send sms:</td>';
		table_html+='       	<td>';
		table_html+='       		<ol>';
		table_html+='       			<li>Territory Manager : <input type="checkbox" name="tm" value=4 checked="checked"></li>';
		table_html+='       			<li>Bussiness Executive : <input type="checkbox" name="BE" value=5 checked="checked"></li>';
		table_html+='       		</ol>';
		table_html+='       	</td>';
		table_html+='       </tr>';
		table_html+='	</tbody>';
		table_html+='</table>';

		$('#driver_details').append(table_html);
		
		},
		
	buttons:{
		'Submit' : function(){
			var c=confirm("Are you sure to  submit this details");
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

//driver update modal end


function show_driver_details(sent_id,mobile,driver_link,job_title)
{
	$("#update_driver_detils").data({'sent_id':sent_id,'driver_name':driver_link,'mobile':mobile,'job_title':job_title}).dialog('open');
}

$(".show_destination_address_det").click(function(){
	var destination_id=$(this).attr("dest_id");
	var bus_id=$(this).attr("bus_id");
	var manifesto_id=$(this).attr('manifesto_id');
	$("#bus_tranaport_details").data({'bus_id':bus_id,'destination_id':destination_id,'manifesto_id':manifesto_id}).dialog('open');
});

$("#update_driver_det_frm").submit(function(){
	var amount=$("input[name=amount]",this).val();
	var km='';
	if($("input[name=start_km]",this).is(":visible"))
	{
		km=$("input[name=start_km]",this).val();
	}
	
	if(amount.length==0)
	{
		alert("Please Enter amount");
		return false;
	}

	if(km.length==0 && $("input[name=start_km]",this).is(":visible"))
	{
		alert("Please Enter Km");
		return false;
	}
	
	return true;
});


$('#bus_tranaport_details').dialog({
	autoOpen:false,
	modal:true,
	height:'350',
	width:'480',
	autoResize:true,
	open:function(){
		$('#bus_tranaport_details').html('');
		var table_html='';
		var bus_id=$(this).data('bus_id');
		var destination_id=$(this).data('destination_id');
		var manifesto_id=$(this).data('manifesto_id');
		
		$.post(site_url+'/admin/jx_bus_transport_details',{bus_id:bus_id,dest_id:destination_id,manifesto_id:manifesto_id},function(res){
			$.each(res.bus_details,function(a,b){
				var type='';
				if(b.type==1)
					type='Bus';
				else if(b.type==2)
					type='Cargo';
				else if(b.type==3)
					type='General Packege';

				table_html+="<div>";
				table_html+="	<fieldset>";
				table_html+="	 <legend>Source Address</legend>";
				table_html+="	 <table>";
				table_html+="	 	<tr>";
				table_html+="	 		<td> Bus Name:</td><td>"+b.name+"</td>";
				table_html+="	 	</tr>";
				table_html+="	 	<tr>";
				table_html+="	 		<td> Address:</td><td>"+b.address+"</td>";
				table_html+="	 	</tr>";
				table_html+="	 	<tr>";
				table_html+="	 		<td> Contact:</td><td>"+b.contact_no+"</td>";
				table_html+="	 	</tr>";
				table_html+="	 </table>";
				table_html+="	</fieldset>";
				table_html+="</div>";
				table_html+="<div>";
				table_html+="	<fieldset>";
				table_html+="	 <legend>Destination Address</legend>";
				table_html+="	 <table>";
				table_html+="	 	<tr>";
				table_html+="	 		<td> Destination Name:</td><td>"+b.short_name+"</td>";
				table_html+="	 	</tr>";
				table_html+="	 	<tr>";
				table_html+="	 		<td> Address         :</td><td>"+b.d_address+"</td>";
				table_html+="	 	</tr>";
				table_html+="	 	<tr>";
				table_html+="	 		<td> Contact         :</td><td>"+b.d_contact_no+"</td>";
				table_html+="	 	</tr>";
				table_html+="	 	<tr>";
				table_html+="	 		<td> Type         :</td><td>"+type+"</td>";
				table_html+="	 	</tr>";
				table_html+="	 </table>";
				table_html+="	</fieldset>";
				table_html+="</div>";		
			});

			$('#bus_tranaport_details').html(table_html);
		},'json');
				
		},
		
	buttons:{
	
		'Close':function(){
			$(this).dialog('close');
		}
	}
	});

	$(".show_vehicle_det").click(function(){
		var m_id=$(this).attr("m_id");
		$("#vehicle_details").data({'manifesto_id':m_id}).dialog('open');
	});

	$("#vehicle_details").dialog({
		autoOpen:false,
		modal:true,
		height:'270',
		width:'250',
		autoResize:true,
		open:function(){
			$("#vehicle_details").html('');
			var m_id=$(this).data('manifesto_id');
			var html_cnt='';
			$.post(site_url+"/admin/jx_vehicle_details",{manifesto_id:m_id},function(res){
				html_cnt+="<h3>Vehicle details</h3>";
				html_cnt+='<table cellpadding="5" cellspacing="0">';
				$.each(res.vehicle_details,function(a,b){
					html_cnt+="<tr>";
						html_cnt+="<td>Vehicle number : </td>";
						html_cnt+="<td>"+b.hndleby_vehicle_num+" </td>";
					html_cnt+="</tr>";
					html_cnt+="<tr>";
						html_cnt+="<td>Start Km : </td>";
						html_cnt+="<td>";
						if(b.start_meter_rate)
							html_cnt+=b.start_meter_rate;
						else
							html_cnt+='Not updated';
						html_cnt+="</td>";
					html_cnt+="</tr>";
					html_cnt+="<tr>";
					html_cnt+="<td>Amount : </td>";
					html_cnt+="<td>";
					if(b.amount)
						html_cnt+=b.amount;
					else
						html_cnt+='Not updated';
					html_cnt+="</td>";
				html_cnt+="</tr>";
				});
				html_cnt+="</table>";
				
				$("#vehicle_details").append(html_cnt);

				},'json');
				
				
		},

		buttons:{
			
			'Close':function(){
				$(this).dialog('close');
			}
		}
		});

//show the details of office pick up
function show_office_pickup_det(manifesto_id)
{
	$("#update_office_pickup").data({'manifesto_id':manifesto_id}).dialog('open');
}

$("#update_office_pickup").dialog({
	autoOpen:false,
	modal:true,
	height:'370',
	width:'400',
	autoResize:true,
	open:function(){
		$("#update_office_pickup #update_office_pickup_form").html('');

		var manifesto_id='';
		var m_id=0;
		
			if($.isArray($(this).data('manifesto_id')))
			{
				manifesto_id=$(this).data('manifesto_id');
			}else{
				m_id=$(this).data('manifesto_id');
			}

		
		var html_cnt='';
		$.post(site_url+'/admin/jx_get_office_pick_up_details',{manifesto_id:m_id},function(res){
			html_cnt+='<table cellpadding="5" cellspacing="0" class="datagrid"><tbody>';
			if(m_id)
			{
			html_cnt+="    <tr>";
			html_cnt+="    		<td><b>Manifesto Id : <b></td>";
			html_cnt+="    		<td>"+res.manifesto_det.id +"</td>";
			html_cnt+="    </tr>";
			html_cnt+="    <tr>";
			html_cnt+="    		<td><b>Transporter : </b></td>";
			html_cnt+="    		<td>"+res.manifesto_det.name +"</td>";
			html_cnt+="    </tr>";
			html_cnt+="    <tr>";
			html_cnt+="    		<td><b>Transport Type : </b></td>";
			html_cnt+="    		<td>";
			if(res.manifesto_det.type==1)
				html_cnt+="Bus";
			else if(res.manifesto_det.type==2)
				html_cnt+="Cargo";
			else if(res.manifesto_det.type==3)
				html_cnt+="General Packege";
			html_cnt+="    		</td>";
			html_cnt+="    </tr>";
			html_cnt+="    <tr>";
			html_cnt+="    		<td><b>Destination : </b></td>";
			html_cnt+="    		<td>"+res.manifesto_det.short_name +"</td>";
			html_cnt+="    </tr>";
			manifesto_id=res.manifesto_det.id
			}
			
			
			html_cnt+="    <tr>";
			html_cnt+="    <td><b>Send via</b></td>"
			html_cnt+="    <td><input type='hidden' value='"+manifesto_id+"' name='manifesot_id'>";
			html_cnt+="    		<select name='office_pick_up[]' class='chzn-select' multiple='multiple' id='office_pick_list'>";
			html_cnt+="    			<option value='choose'>choose</option>";
										if(res.office_pickup_list)
										{
											$.each(res.office_pickup_list,function(a,b){
												var contact_det=b.contact_no.split(',');
												html_cnt+="<option value='"+b.employee_id+"'>"+b.name+"("+contact_det[0]+")</option>"
											});
										}
			html_cnt+="    		</select>";
			html_cnt+="    </td>";
			html_cnt+="    </tr>";								

			

			html_cnt+='</tbody></table>';
			
			$("#update_office_pickup_form").append(html_cnt);
			$(".chzn-select").chosen({no_results_text: "No results matched"}); 
		},'json')
	},
	
	buttons:{
		'Submit' : function(){
			var c=confirm("Are you sure to  submit this details");
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

	$("#update_office_pickup_form").submit(function(){
		var office_pick_up=$("#office_pick_list",this).val();
		
		if(!office_pick_up)
		{
			alert("Please select sent via");
			return false
		}
		return true
	});

	$(".show_office_pickup_deatils").click(function(){
		var manifesto=$(this).attr("manifesot");
		$("#office_pick_up_det").data({'manifesto':manifesto}).dialog('open');
		
	});	

	$("#office_pick_up_det").dialog({
		autoOpen:false,
		modal:true,
		height:'300',
		width:'400',
		autoResize:true,
		open:function(){
			var manifesto=$(this).data('manifesto');
			
			$("#office_pick_up_det_inner").html('');
			$.post(site_url+"/admin/jx_get_office_pickup_by_manifesto",{manifesto:manifesto},function(res){
				var html_cnt='';
					
					if(res.error)
						html_cnt=res.error;
					else
					{
						html_cnt ="<table cellpadding='6' cellspacing='0' class='datagrid'><thead>";
						html_cnt+='<tr>';
						html_cnt+='		<th>#</th>';
						html_cnt+='		<th>Name</th>';
						html_cnt+='		<th>Contact</th>';
						html_cnt+='</tr></thead><tbody>';
	
						$.each(res.employe_list,function(a,b){
							html_cnt+="<tr>";
							html_cnt+="	  <td>"+(a+1)+"</td>";
							html_cnt+="	  <td>"+b.name+"</td>";
							html_cnt+="	  <td>"+b.contact_no+"</td>";
							html_cnt+="</tr>";	
						});
						html_cnt+="</tbody></table>";
						
					}
					$("#office_pick_up_det_inner").html(html_cnt);	
				
			},'json');
			
			},
		buttons:{
			'Close':function(){
				$(this).dialog('close');
			}
		}
	});


function show_lr_update_form(manifesto_id,corier_id)
{
	$("#update_lr_number_details").data({'manifesto_id':manifesto_id,'corier_id':corier_id}).dialog('open');
}

//lr number  update modal 
$('#update_lr_number_details').dialog({
	autoOpen:false,
	modal:true,
	height:'320',
	width:'480',
	autoResize:true,
	open:function(){
		$('#lr_number_update').html('');
		var table_html='';
		var sent_id=$(this).data('manifesto_id');
		var courier_id=$(this).data('corier_id');
		
		table_html+='<input type="hidden" value="'+sent_id+'" name="manifesto_sent_id"><input type="hidden" value="'+courier_id+'" name="courier_id"><table class="datagrid" width="100%">';
		table_html+='	<thead><tr><th colspan="2">Update Lr number</th></tr></thead>';
		table_html+='	<tbody>';
		table_html+='       <tr>';
		table_html+='       	<td>Lr number:</td>';
		table_html+='       	<td><input tye="text" name="lr_number"></td>';
		table_html+='       </tr>';
		table_html+='       <tr>';
		table_html+='       	<td>Amount:</td>';
		table_html+='       	<td><input type="text" size:"10" name="amount"></td>';
		table_html+='       </tr>';
		table_html+='       <tr>';
		table_html+='       	<td>Send sms:</td>';
		table_html+='       	<td>';
		table_html+='				<ol>';
		table_html+='					<li>Territory Manager : <input type="checkbox" name="Tm" value="4" checked="checked"></li>';
		table_html+='					<li>Business Executive : <input type="checkbox" name="BE" value="5" checked="checked"></li>';
		table_html+='				</ol>';
		table_html+='       	</td>';
		table_html+='       </tr>';
		table_html+='	</tbody>';
		table_html+='</table>';

		$('#lr_number_update').append(table_html);
		
		},
		
	buttons:{
		'Submit' : function(){
			var c=confirm("Are you sure to  submit this details");
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
//lr number  update modal 


$("#update_lr_number_form").submit(function(){
	var amount=$("input[name=amount]",this).val();
	var lr_number='';
	if($("input[name=lr_number]",this).is(":visible"))
	{
		lr_number=$("input[name=lr_number]",this).val();
	}
	
	if(amount.length==0)
	{
		alert("Please Enter amount");
		return false;
	}

	if(lr_number.length==0 && $("input[name=lr_number]",this).is(":visible"))
	{
		alert("Please Enter lr number");
		return false;
	}
	
	return true;
});

$("input[name=\"select_all\"]").live('change',function(){
	if($(this).attr("checked"))
		$("input[name=update_lr]").attr("checked",true);
	else
		$("input[name=update_lr]").attr("checked",false);

	//$("input[name=select]").trigger('change');	
});

$("#m_update_office_pick_up_details").click(function(){

	var manifesto_id=new Array();
	$("input[name='update_lr']:checked").each(function(){
		manifesto_id.push($(this).val());
	});

	if(manifesto_id.length==0)
	{
		alert("Please select atleast one manifesto");
	}else{
		$("#update_office_pickup").data({'manifesto_id':manifesto_id}).dialog('open');
	}
});


function print_del_label(man_id)
{
	$('#print_del_label_frm').html('<iframe src="'+site_url+'/admin/print_deliverylabel/'+man_id+'"></iframe>');	
}

function print_franchise_label(man_id)
{
	$('#print_del_label_frm').html('<iframe src="'+site_url+'/admin/print_fran_deliverylabelby_manid/'+man_id+'"></iframe>');	
}


$("#cancel_manifesto").click(function(e){
	e.preventDefault();
	if(confirm("Are you sure want to cancel this manifesto"))
	{
		window.location.href = $(this).attr('href');
	}
});

$('.show-by').change(function(){
	var type = $('select[name="type"]').val();
	var store = $('select[name="store"]').val();
	var kwd = (($('#srch-inp').val()=='Search...')?'any':$('#srch-inp').val());
	
		location.href = site_url+'/parser/setting/product_list/'+type+'/'+store+'/'+kwd;
});

$("#manifesto_filter_form").submit(function(){
	
	var search_query=$("input[name='src_invoice']").val();
	var date_from=$("input[name='from']").val();
	var date_to=$("input[name='to']").val();
	var status=$("select[name='status']").val();
	var hubs_name=$("select[name='hubs']").val();
	var view_option=$("select[name='view_option']").val();

	if(view_option==2 && (!date_from || !date_to))
	{
		alert("Please choose date for download");
		return false;
	}
	
	if(!search_query || isNaN(search_query))
		search_query=0;
	if(!date_from)
		date_from='0000-00-00';
	if(!date_to)
		date_to='0000-00-00';
	
	//$("#filter_form").attr('action',site_url+'/admin/view_manifesto_sent_log/'+search_query+'/'+date_from+'/'+date_to+'/'+status;);
	location.href = site_url+'admin/view_manifesto_sent_log/'+search_query+'/'+date_from+'/'+date_to+'/'+status+'/'+hubs_name+'/'+view_option;

	return false;
});

function show_mark_delivered_dlg(e)
{
	var invoices=$(e).attr('invoices');
	var send_log_id=$(e).attr('send_log_id');
	$("#mark_delivered_courier_transport").data({'invoices':invoices,'send_log_id':send_log_id}).dialog('open');

}

$("#mark_delivered_courier_transport").dialog({
	autoOpen:false,
	modal:true,
	height:'320',
	width:'720',
	autoResize:true,
	open:function(){
		$("#mark_delivered_courier_transport_form").html('');
		var invoices=$(this).data('invoices');
		var send_log_id=$(this).data('send_log_id');
		var html_contant='';
		var ds='';
		var show_submit=0;

		html_contant+="<input type='hidden' value='"+send_log_id+"' name='send_log_id'><table class='datagrid' cellpadding='5' cellspacing='0'><thead><tr><th>#</th><th>Invoice</th><th>Received by</th><th>Received on</th><th>Contact No</th><th>Delivered</th></tr></thead><tbody>";
		$.post(site_url+'/admin/jx_check_invoice_dlvrstatus',{invoices:invoices},function(res){
			
			$.each(res.invoice_det,function(a,b){
				html_contant+="<tr>";
				html_contant+="	<td>"+(a+1)+"</td>";
				html_contant+="	<td>"+b.inv+"</td>";
				html_contant+="	<td><input type='text' name='received_by[]' value=''></td>";
				html_contant+="	<td><input type='text' name='received_on[]' value='' class='received_on'></td>";
				html_contant+="	<td><input type='text' name='contact_no[]' value='' maxlength='10' ></td>";
				ds='';

				if(b.st)
				{
					ds="disabled='disabled' checked='checked'";
				}else{
					show_submit=1;
				}
				
				html_contant+="	<td><input type='checkbox' name='delivered[]' value='"+b.inv+"' "+ds+" ></td>";
				html_contant+="<tr>";	
			});

			html_contant+="</tbody></table>";
			$("#mark_delivered_courier_transport_form").html(html_contant);
			$('#mark_delivered_courier_transport_form .received_on').datepicker();

			if(show_submit)
			{
				var buttons={
					
					'Submit' : function(){
						var c=confirm("Are you sure to  submit this details");
						if(c)
							$('form',this).submit();
						else
							return false;
					},
					
					'Close':function(){
						$(this).dialog('close');
					}
				};
				
			}else
			{
				var buttons={
					
				'Close':function(){
						$(this).dialog('close');
					}
				};
			}
				
			$('#mark_delivered_courier_transport').dialog('option', 'buttons', buttons);

		},'json');
		
		
	}
});

$("#mark_delivered_courier_transport_form").submit(function(){
	var mobile=$("input[name='contact_no[]']",this).val();
	var delivered_status=0;
	var ship_date=$("input[name='received_on[]']",this);
	var error=0;

	$('.error_inp',this).removeClass('error_inp');
	
	$("input[name='delivered[]']:checked",this).each(function(a,b){
		if(!$(this).attr('disabled'))
		{
			var trEle = $(this).parents('tr:first');
			
			var rcvd_by = $("input[name='received_by[]']",trEle).val();
			var rcvd_on = $("input[name='received_on[]']",trEle).val();
			var contact_no = $("input[name='contact_no[]']",trEle).val();
				if(contact_no.length!=0)
				{
					if(contact_no.length<10 || isNaN(contact_no*1))
					{
						$("input[name='contact_no[]']",trEle).addClass('error_inp');
					}
				}

				if(rcvd_on == "")
					$("input[name='received_on[]']",trEle).addClass('error_inp');
				delivered_status = 1;
			
		}
	});

	if(delivered_status==0)
	{
		alert('Select atleast one invoice');
		return false;
	}

	if($('.error_inp',this).length)
	{
		alert("Please enter valid data to submit");
		return false;
	}
	
});

$("#add_alternative_nukber").click(function(){
	$("#add_alternative_number").data({}).dialog('open');
});

$("#add_alternative_number").dialog({
	autoOpen:false,
	modal:true,
	height:'400',
	width:'400',
	autoResize:true,
	open:function(){
		$("#det_box").html('');
		$("input[name='ch_manifesto_id']").val('');
		}
});

$("#ch_manifesto_id").click(function(){
	var manifsto_id=$("input[name='ch_manifesto_id']").val();

	if(manifsto_id.length==0)
	{
		alert('enter manifesto id');
		return false;
	}
	$("#det_box").html('');
	var html_cnt='';
	
	$.post(site_url+'/admin/jx_check_manifesto',{manifesto:manifsto_id},function(res){
		if(res.status=='error')
		{
			$('#add_alternative_number').dialog('option', 'buttons',{}); 
			html_cnt+='<div  style="color:red;" align="center">'+res.message+'</div>';
		}else{
			html_cnt+="<table class='datagrid' width='100%' cellpadding='5' cellspacing='0'><tr><td>Mobile Number : </td><td><input type='text' name='alternative_number' maxlength='10'></td></tr></table>"

				var buttons={
					
					'Submit' : function(){
						var c=confirm("Are you sure to  submit this details");
						if(c)
							$('form',this).submit();
						else
							return false;
					},
					
					'Close':function(){
						$(this).dialog('close');
					}
				};
			$('#add_alternative_number').dialog('option', 'buttons', buttons);
		}
			
		$("#det_box").html(html_cnt);

		
		
	},'json');
});

$("#add_alternative_number_form").submit(function(){
	var mobile_num=$("input[name='alternative_number']",this).val();
	var m_id=$("input[name='ch_manifesto_id']",this).val();

	if(m_id.lenght==0)
	{
		alert('please enter manifesto id');
		return false;
	}

	if(mobile_num.length==0)
	{
		alert('Please enter mobile number');
		return false;
	}

	if(mobile_num.length > 10)
	{
		alert('Invalid Mobile Number');
		return false;
	}

	if(isNaN(mobile_num))
	{
		alert('Invalid Mobile Number');
	}
	return true;
});
</script>

<div id="print_del_label_frm" style="width: 1px;height: 1px;visibility: hidden"></div>
