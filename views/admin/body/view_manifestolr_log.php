<style>
	.leftcont{display:none}
</style>

<div class="page_wrap container">
	<div class="clearboth">
		<div class="fl_left" >
			<h2 class="page_title">Manifesto Lrno Update log</h2>
		</div>
		<div class="fl_right stats" >
			<form action="<?php echo site_url('admin/manifestolr_log')?>" method="post">
				<div class="dash_bar" style="float:right;max-width: 100%">
					<b>Filter By </b>Start Date :<input type="text" name="st_dt" id="st_date" style="width:100px"> End Date :<input type="text" name="en_dt" id="en_date" style="width:100px" > 
					<!--  </div>
					<div class="dash_bar" style="max-width:500px;float:right">-->
					search By manifesto Id:<input type="text" name="srch_manifesto"> 
					<input type="submit" value="Submit">
				</div>
			</form>
		</div>
	</div>
	
	<div class="page_topbar" >
		<div class="page_topbar_left fl_left" >
			<?php if(isset($pagetitle))?>
						<h3><?php echo $pagetitle ;?></h3> 
		</div><br>
		<div class="page_action_buttonss fl_right" align="right">
			<a href="javascript:void(0)" id="logistick_manifesto_list">Print logistic manifesto list</a>
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	
	<div class="page_content">
		<table class="datagrid" width="100%" cellpading="5">
		<?php if($manifesto_lr_res){?>
		<thead><th>Slno</th><th>Destination</th><th>Manifesto Id</th><th>Lr Number</th><th>No of boxes</th><th>Weight (Kg)</th><th>Shipment Charges (Rs)</th><th>Updated on</th><th>Updated By</th></thead>
		<tbody>
		<?php $i=1; foreach($manifesto_lr_res->result_array() as $m_lr_det){?>
		<tr>
			<td><?php echo $i;?></td>
			<td style="max-width: 300px">
				<?php				
				$hub_name = $this->db->query("select group_concat(distinct d.hub_name) as hub_names 
																				from pnh_m_manifesto_sent_log a 
																				join pnh_t_tray_invoice_link b on find_in_set(b.invoice_no,a.sent_invoices)
																				left join pnh_t_tray_territory_link c on c.tray_terr_id = b.tray_terr_id    
																				join pnh_deliveryhub d on d.id = c.territory_id 
																				where  a.id in (".$m_lr_det['id'].") ",$sent_det['id'])->row()->hub_names;
																				
					echo str_replace(',', ', ', $hub_name);
				?>
			</td>
			<td>
				<?php foreach(explode(',',$m_lr_det['id']) as $mid){  ?>
				<a target="_blank" href="<?php echo site_url('admin/view_manifesto_sent_log/'.$mid.'/0000-00-00/0000-00-00/0/0/1');?>"><?php echo $mid ;?></a>
				<?php } ?>
			</td>
			<td><?php echo $m_lr_det['lrno'] ;?></td>
			<td><?php echo $m_lr_det['no_ofboxes'] ;?></td>
			<td><?php echo $m_lr_det['weight'] ;?></td>
			<td><?php echo $m_lr_det['amount']?></td>
			<td><?php echo format_datetime($m_lr_det['lrn_updated_on']) ;?></td>
			<td><?php echo $m_lr_det['updated_by']?></td>
		</tr>
		<?php $i++; }?>
		</tbody>
		
		<?php }else{?>
		<div align="center"><?php echo "No Data Found"?></div>
		<?php }?>
		</table>
		
		<div class="pagination" align="right">
			<?php echo $pagination;?>
		</div>
		<div id="view_invoicesdiv" title="Invoices">
		</div>
	</div>
</div>


<!-- print logistick manifesto details modal -->
<div id="print_logistick_manifesto_list" id="print logistick manifesto list" title="Print Logistic Manifesto by Date ">
	<form method="post" action="<?php echo site_url("/admin/jx_print_logistick_manifeasto_list")?>" id="print_logistick_manifesto_list_form" target="frame_print_logistick_det">
		<div id="m_date2" align="center"></div>
	</form>
	<iframe name="frame_print_logistick_det" style="display:none;" id="frame_print_logistick_det"></iframe>
</div>
<!-- print logistick manifesto details end -->



<script>
$("#st_date,#en_date").datepicker();

$("#m_date2").datepicker({'maxDate':new Date()});

$("#logistick_manifesto_list").click(function(){
	$('#print_logistick_manifesto_list').data({}).dialog('open');
});

$('#print_logistick_manifesto_list').dialog({
	autoOpen:false,
	modal:true,
	height:'auto',
	width:'auto',
	autoResize:true,
	open:function(){
		$(".m_date").remove();		
	},
		
	buttons:{
		'Submit' : function(){
			var c=confirm("Are you sure to  submit this details");
			if(c)
			{
				$('form',this).submit();
				$('#print_logistick_manifesto_list').dialog("close");
			}	
			else
				return false;
		},
	
		'Close':function(){
			$(this).dialog('close');
		}
	}
	});

$("#print_logistick_manifesto_list_form").submit(function(){
	var manifesto_date =$( "#m_date2" ).datepicker( "option","dateFormat", 'yy-mm-dd' ).val();
	$("#print_logistick_manifesto_list_form").append("<input type='text'name='m_date' value='"+manifesto_date+"' class='m_date'>");
	return true;
});


</script>