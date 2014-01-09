<div class="container">
<?php /* <h2>Generate Manifesto</h2>
<form method="post" action="<?php echo site_url('admin/generate_manifesto_byrange');?>"> 
	<table id="frm" class="datagrid">
		<tr>
			<td>
				<b>Start Date</b> :
				<div><input type="text" id="from_date" name="from" value="<?php echo $from?>" /></div>
			</td>
			<td>
				<b>End Date</b> :
				<div><input type="text" id="to_date" name="to" value="<?php echo $to?>" /></div>
			</td>
			<td>
				<b>Territory</b> :
				<span style="float: right"><a href="javascript:void(0)" select_all="0" onclick="tgl_selection(this)">Select All</a></span>
				<div>
					<select name="sel_terr_ids[]" data-placeholder="Choose" multiple="multiple" class="chz-select" style="width: 200px;">
							<?php 
								if(!$sel_terr_ids)
									$sel_terr_ids = array();
								$tr_list = $this->db->query("select * from pnh_m_territory_info order by territory_name ")->result_array();
								foreach($tr_list as $tr_det)
								{
									$sel = in_array($tr_det['id'],$sel_terr_ids)?'selected':'';
							?>
									<option <?php echo $sel;?> value="<?php echo $tr_det['id']?>" ><?php echo ucwords($tr_det['territory_name'])?></option>
							<?php 		
								}
							?>		
					</select>
				</div>
			</td>
			<td>
				&nbsp;
				<div><input type="submit" value="Generate" style="float:right" /></div>
			</td>
		</tr>
	</table>
</form> */?>

<script type="text/javascript">
$('select[name="sel_terr_ids[]"]').chosen();

function tgl_selection(ele)
{
	if($(ele).attr('select_all') == 1)
	{
		$(ele).attr('select_all',0);
		$('select[name="sel_terr_ids[]"] option:selected').attr('selected',false);
		$(ele).text('Select All');
	}else
	{
		$(ele).attr('select_all',1);
		$('select[name="sel_terr_ids[]"] option').attr('selected',true);
		$(ele).text('UnSelect All');
	}
	
	$('select[name="sel_terr_ids[]"]').trigger("liszt:updated");
	
}

</script>

<br />
<?php if($_POST){?>
<div style="margin-top: 10px;">
<?php 	
	$manifesto_list = array();
	if($outscan_res->num_rows())
	{
		foreach($outscan_res->result_array() as $row)
		{	
			if(!isset($manifesto_list[$row['franchise_id']]))
				$manifesto_list[$row['franchise_id']] = array();
			array_push($manifesto_list[$row['franchise_id']],$row);
		}
	
?>			
	<div align="right" style="width: 90%;clear: both;">
		<span style="text-align: left">
			<b>Filter by </b>
			 
			<b>Territory</b>
			<select class="chzn-select" id="sel_terrlist"></select>
			&nbsp;
			<b>Town</b>
			<select class="chzn-select" id="sel_townlist"></select>
			&nbsp;
			&nbsp;
			<input type="button" value="Print Manifesto" id="print_manifesto" />	
		</span>
	</div>
		
	<table id="manifesto_list" class="datagrid" style="border-collapse: collapse;width: 90%;clear: both;">
		<thead>
			<th width="20"><b>Slno</b></th>
			<th width="60"><b>Invoiceno</b> 
				<input type="checkbox" value="1" checked="checked" name="chk_print_visible">
			</th>
			<th width="300"><b>Name</b></th>
			<th width="130"><b>City/Town</b></th>
			<th width="60"><b>Pincode</b></th>
			<th width="60"><b>Mobile</b></th>
			<th width="200"><b>Seal & Signature </b></th>
		</thead>
		<tbody>
		<?php 
			 	$slno = 1;
				foreach($manifesto_list as $fr_id=>$fr_manifesto)
				{	
					$row_span = count($fr_manifesto);
					$k = 0 ;
					foreach($fr_manifesto as $fr_man_inv)
					{
						$fr_man_inv['territory_name'] = trim($fr_man_inv['territory_name']);
						$fr_man_inv['town_name'] = trim($fr_man_inv['town_name']);
		?>
						 
					<tr class="fr_<?php echo $fr_man_inv['franchise_id'] ?> twn_<?php echo str_replace(' ','_',$fr_man_inv['town_name']);?> terr_<?php echo str_replace(' ','_',$fr_man_inv['territory_name']);?>">
						<td align="center"><?php echo $slno++?></td>
						<td><?php echo $fr_man_inv['invoice_no']?>
							<input type="checkbox" checked="checked" value="<?php echo $fr_man_inv['invoice_no'];?>" class="chk_print" >
						</td>
						<?php 
							if($k == 0) {
						?>
								<td class="town_name terr_name" terr_name="<?php echo str_replace(' ','_',$fr_man_inv['territory_name']); ?>" twn_name="<?php echo str_replace(' ','_',$fr_man_inv['town_name']); ?>" rowspan="<?php echo $row_span?>" style="vertical-align: middle;text-align: center;"><?php echo $fr_manifesto[0]['franchise_name'];?>
									<input type="checkbox" checked="checked" value="<?php echo $fr_man_inv['franchise_id'] ?>" class="chk_print_fr" >
								</td>
								<td rowspan="<?php echo $row_span?>" style="vertical-align: middle;text-align: center;"><?php echo $fr_manifesto[0]['town_name'];?></td>
								<td rowspan="<?php echo $row_span?>" style="vertical-align: middle;text-align: center;"><?php echo $fr_manifesto[0]['postcode'];?></td>
								<td rowspan="<?php echo $row_span?>" style="vertical-align: middle;text-align: center;"><?php echo $fr_manifesto[0]['login_mobile1'];?></td>
								<td rowspan="<?php echo $row_span?>" ><div style="height: 60px;width: 200px;background: #fdfdfd;"></div></td>
						<?php 		
								$k = 1;
							}
						?>
					</tr>
		<?php 
					}
				}
			 
		?>
		</tbody>
	</table>
	<?php }else{
		echo "<b>No Outscans found for the selected dates </b>";
	}?>
</div>
<?php }else{
?>		
	<div id="manifesto_log">	
		<div style="width:100%;">
			<div style="width:30%;float:left;">
				<h3>Manifesto Log</h3>
			</div>
			<div style="width:65%;float:right;">
				<div align="right" style="padding:5px;">
					<a href="<?php echo site_url('admin/view_manifesto_sent_log')?>">View manifesto sent log</a>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		
		<?php 
			if($manifesto_hist_res->num_rows)
			{
		?>
		<table class="datagrid" cellpadding="0" cellspacing="0">
			<thead>
				<th>Slno</th>
				<th>Name</th>
				<th>Invoice nos</th>
				<th>Prints</th>
				<th>sent summary</th>
				<th>Createdon</th>
				<th>LastPrintedon</th>
			</thead>
			<tbody>
			<?php 
				if($manifesto_hist_res->num_rows)
				{
					$slno = $pg+1;
					foreach($manifesto_hist_res->result_array() as $manifesto_hist)
					{	
						if(!$manifesto_hist['invoice_nos'])
							continue;
						echo '<tr>';
						echo '	<td>'.$slno.'</td>';
						echo '	<td>'.$manifesto_hist['name'].'</td>';
						echo '	<td><a class="preview_inv">'.implode('</a> <a class="preview_inv">',explode(',',$manifesto_hist['invoice_nos'])).'</a></td>';
						echo '	<td>'.$manifesto_hist['total_prints'].'</td>';
						echo ' <td>'
									.count(array_filter(explode(',',$manifesto_hist['sent_invoices']))).'/'.count(explode(',',$manifesto_hist['invoice_nos'])).'<br>
									 <a href="javascript:void(0)" onclick="show_manifest_update_dlg(this)" manifest_id="'.$manifesto_hist['id'].'">Update Manifesto</a>,<br>
									 <a href="javascript:void(0)" onclick="show_manifest_sent_summary(this)" manifest_id="'.$manifesto_hist['id'].'" manifesto_for="'.$manifesto_hist['name'].'" id="show_manifest_sent_summary">View Manifesto summary</a>
							  </td>';
						echo '	<td>'.format_datetime($manifesto_hist['created_on']).'</td>';
						echo '	<td>'.format_datetime($manifesto_hist['modified_on']).'</td>';
						echo '	<td>
									<input type="button" value="Print" onclick="print_manifesto('.$manifesto_hist['id'].')"><br>
								</td>';
						echo '</tr>';
						$slno++;
					}
				}
			?>
			</tbody>
		</table>
		<div align="left" class="pagination">
			<?php echo $manifesto_hist_pagi?>
		</div>
		<?php }else{
			echo "No history found";
		}?>
	</div>
<?php 	  	 
} ?>
	
</div>

<!-- Manifesto update dialogbox -->
<div id="update_manifesto_dlg" title="Update invoices manifesto log details">
	<form action="<?php echo site_url('admin/update_manifesto_detail')?>" method="post" id="manifesto_update_form">
		<input type="hidden" name="manifest_log_id" value="0">
		<div id="mani_invoice_nos"></div>
	</form>
</div>
<!-- Manifesto update dialogbox end -->

<!-- sent summary dialogbox -->
<div id="manifesto_sent_summary" >
	
</div>
<!-- sent summary dialogbox end -->

<style>
	#update_manifesto_dlg table tr th{
		text-align: left;
	}
</style>

<script>

//get the manifesto invoices
$('#update_manifesto_dlg').dialog({

	autoOpen:false,
	modal:true,
	height:450,
	width:800,
	open:function(){
		manifest_log_id = $(this).data('manifest_log_id');
		$('form input[name="manifest_log_id"]',this).val(manifest_log_id);

		$.post(site_url+"/admin/get_invoices_nos_by_manisfestoid",{manifesto_id:$(this).data('manifest_log_id')},
				function(invoices_no){
					$("#mani_invoice_nos").html(invoices_no);
		});
	},
	buttons:{
		'Generate' : function(){
			
			$('form',this).submit();
		},
		'Cancel':function(){
			$(this).dialog('close');
		}
	}
});
				
function show_manifest_update_dlg(ele)
{
	$("#update_manifesto_dlg").data({manifest_log_id:$(ele).attr("manifest_id"),dlg_height:$(window).height()}).dialog('open');
}

//select all function
function select_option(ele)
{
	$(".sel").attr("checked",false);
	if($(ele).attr('checked'))
	{
		if($('#terr_list').val())
			$(".show_invoice_"+$('#terr_list').val()+" .sel").attr("checked",true);
	}
	
	
}


//manifesto update form validation
$("#manifesto_update_form").submit(function(){
	var check_boxes=0;
	var trans_opt=$("select[name=transport_opts]",this).val();
	var drive_name=$("#other_driver").val().length;
	var mobile_num=$("input[name=other_driver_ph]").val();
	
	$(".sel",this).each(function(){
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

});

//get the manifesto sent summary
function show_manifest_sent_summary(ele)
{
	var manifest_set_summary_dlg_ttl=$("#show_manifest_sent_summary").attr('manifesto_for');
	var dlg=$('#manifesto_sent_summary').dialog({

		autoOpen:false,
		modal:true,
		height:400,
		width:890,
		title:manifest_set_summary_dlg_ttl,
		open:function(){
			manifest_log_id = $(this).data('manifest_log_id');
			if($("#manifesto_sent_summary_tbl")[0])
			{
				$("#manifesto_sent_summary_tbl").remove();
			}
			$.post(site_url+"/admin/get_manifesto_sent_summary",{manifesto_id:$(this).data('manifest_log_id')},
					function(sent_summary){
						$("#manifesto_sent_summary").append(sent_summary);
				});
		},
		buttons:{
			'Close':function(){
				$(this).dialog('close');
			}
		}
	});

	dlg.data({manifest_log_id:$(ele).attr("manifest_id")}).dialog('open');
}

//invoice territory vice filter
function select_invoice_by_territory(ele)
{
	var terr_id=$(ele).attr("value");
	var class_name='.show_invoice_'+terr_id;
	$(".sel_all").attr("checked",false);

	if($(".sel").is(':checked'))
	{
		var r=confirm("Some of the invoices select do you want to clear?");
		if(r==true)
		{
			$(".sel").attr("checked",false);
		}else{
				return false;
			}
	}

	if(terr_id!='all')
	{
		$.post(site_url+"/admin/get_executives_and_fc",{territory_id:terr_id},function(response){
			$("#pick-up-by").html(response);
			});
	}
	

	if(terr_id=='all')
	{
		///$(".show_invoice").show();
		$(".show_invoice").hide();
	}else{
			$(".show_invoice").hide();
			$(class_name).show();
	}
}

//transport options
function select_transport(ele)
{
	var value=$(ele).attr("value");

	$(".trans_opt_blk").hide();
	if(value=='Choose')
	{
		$(".trans_opt_blk").hide();
	}

	if(value==0)
	{
		$("#pick-up-by-blk").show();
	}

	if(value==7)
		$("#drivers_list_blk").show();
	else if(value==6)
		$("#field_cordinators_list_blk").show();
	else if(value==0)
		$("#other_trans").show();
	
}
function print_sent_manifesto(id,sent_id){
	$('#sent_mainifestoprint input[name="id"]').val(id);
	$('#sent_mainifestoprint input[name="sent_id"]').val(sent_id);
	$('#sent_mainifestoprint').submit();
}

function scan_invoice(e)
{
	if(e.which==13)
	{
		
		var invoice_num=$("#srch_barcode").val();
		if(invoice_num=='')
			alert('Invoice number field empty');
		var class_name='.'+invoice_num;
		$(class_name).attr("checked",true);
		if(!$(class_name)[0])
			alert('Invoice number not present');
		var td = $(class_name).closest("td");
		$(td).addClass("highlightprow");
		$("#srch_barcode").val("");
		
	}else if($(e).attr('value')=='scan')
	{
		var invoice_num=$("#srch_barcode").val();
		if(invoice_num=='')
			alert('Invoice number field empty');
		var class_name='.'+invoice_num;
		$(class_name).attr("checked",true);
		if(!$(class_name)[0])
			alert('Invoice number not present');
		var td = $(class_name).closest("td");
		$(td).addClass("highlightprow");
		$("#srch_barcode").val("");
	}
}

$(".remove_invoice").live("click",function(){
	var invoice_no=$(this).attr('invoice_no');

	var didConfirm = confirm("Are you sure wnat to remove this invoice?");
	if(didConfirm==true)
	{
		$.post(site_url+'/admin/remove_invoice',{invoice_no:invoice_no},function(res){
			if(res.status)
				var class_name='.rm_'+invoice_no;
				$(class_name).hide();
			},'json');
	}
});
</script>


<div style="display: none;">
<form id="gen_manifestoprint" target="hndl_ganmanifestoprint" action="<?php echo site_url('admin/gen_manifestoprint')?>" method="post">
	<input type="hidden" name="from_d" value="<?php echo $this->input->post('from')?>">
	<input type="hidden" name="to_d" value="<?php echo $this->input->post('to')?>">
	<input type="hidden" name="id" value="0">
	<input type="hidden" name="sel_terr_ids" value="<?php echo implode(',',$sel_terr_ids);?>">
	<textarea name="exclude_invs"></textarea>
</form>
<iframe id="hndl_ganmanifestoprint" name="hndl_ganmanifestoprint" style="width: 1px;height: 1px;border:0px;"></iframe>

<form id="sent_mainifestoprint" target="hndl_ganmanifestoprint" action="<?php echo site_url('admin/gen_manifestoprint')?>" method="post">
	<input type="hidden" name="id" value="0">
	<input type="hidden" name="sent_id" value="0">
</form>

</div>
<style>
.hideinprint td{background: #cdcdcd !important;}
.pagination{padding:4px;}
.pagination a{display: inline-block;padding:3px;color: #cd0000}
.preview_inv{cursor: pointer;}
.highlightprow{
background:#E5EECC !important;
}
.hidden{
	display:none;
}
</style>
<script>
$('.preview_inv').click(function(){
	window.open(site_url+'/admin/invoice/'+$(this).text());
});
function print_manifesto(id){
	$('#gen_manifestoprint input[name="id"]').val(id);
	$('#gen_manifestoprint').submit();
}

var terrList = new Array();
	$('.terr_name').each(function(){
		if($.inArray($(this).attr('terr_name'),terrList)==-1)
		{
			terrList.push($(this).attr('terr_name'));
		}
	});

var townList = new Array();
	$('.town_name').each(function(){
		if($.inArray($(this).attr('twn_name'),townList)==-1)
		{
			townList.push($(this).attr('twn_name'));
		}
	});

	$(function(){
		$('.dg_print').hide();
	});

	
	 
	//townList = sort(townList);
	var twn_html = '';
	for(var k=0;k<townList.length;k++){
		twn_html += '<option rel="'+townList[k]+'" value="'+townList[k]+'">'+townList[k]+'</option>';
	}

	$('#sel_townlist').html(twn_html);

	$('#sel_townlist').change(function(){
		$('#sel_terrlist').val("").trigger("liszt:updated");
		var twn_name = $(this).val();
		$('#manifesto_list tbody tr').hide();
		if($(this).val())
		{
			$('#manifesto_list tbody tr.twn_'+twn_name).show();
		}else
		{
			$('#manifesto_list tbody tr').show();
		}
	});

	$('#sel_townlist').html($("#sel_townlist option").sort(function(a, b) { 
	    var arel = $(a).attr('rel');
	    var brel = $(b).attr('rel');
	    return arel == brel ? 0 : arel < brel ? -1 : 1 
	}));

	$('#sel_townlist').prepend('<option value="">Show All</option>');


	$('#sel_townlist').chosen({no_results_text: "No results matched"});



	var terr_html = '';
	for(var k=0;k<terrList.length;k++){
		terr_html += '<option rel="'+terrList[k]+'" value="'+terrList[k]+'">'+terrList[k]+'</option>';
	}

	$('#sel_terrlist').html(terr_html);

	$('#sel_terrlist').change(function(){

		$('#sel_townlist').val("").trigger("liszt:updated");
		
		var terr_name = $(this).val();
		$('#manifesto_list tbody tr').hide();
		if($(this).val())
		{
			$('#manifesto_list tbody tr.terr_'+terr_name).show();
		}else
		{
			$('#manifesto_list tbody tr').show();
		}
	});

	$('#sel_terrlist').html($("#sel_terrlist option").sort(function(a, b) { 
	    var arel = $(a).attr('rel');
	    var brel = $(b).attr('rel');
	    return arel == brel ? 0 : arel < brel ? -1 : 1 
	}));

	$('#sel_terrlist').prepend('<option value="">Show All</option>');


	$('#sel_terrlist').chosen({no_results_text: "No results matched"});


	
	

$('textarea[name="exclude_invs"]').val('');
$("#print_manifesto").click(function(){
	$ele = $('#manifesto_list');
	var exclude_invnos = new Array();
		exclude_invnos.push(0);
	$('tbody tr',$ele).each(function(){
		if($(this).hasClass('hideinprint'))
		{
			exclude_invnos.push($('.chk_print',this).val());
		}
	});

	$('textarea[name="exclude_invs"]').val(exclude_invnos.join(','));
	$('#gen_manifestoprint').submit();
});

prepare_daterange('from_date','to_date');
$('.chk_print').change(function(){
	if($(this).attr('checked'))
	{
		$(this).parent().parent().removeClass('hideinprint');
	}	
	else
	{
		$(this).parent().parent().addClass('hideinprint');
	}
	
	setTimeout(function(){
		var i=0;
		$('#manifesto_list tbody tr').each(function(){
			$('td:first',this).text('');
			if(!$(this).hasClass('hideinprint') && !$(this).hasClass('nomark'))
			{
				i++;
				$('td:first',this).text(i);
				
			}
		});
	},200)
});
$('.chk_print_fr').change(function(){
	var fid = $(this).val();
	var invchks = $('.fr_'+fid+' .chk_print');
	if($(this).attr('checked'))
	{
		invchks.attr('checked',true);
	}else
	{
		invchks.attr('checked',false);
	}
	invchks.trigger('change');
		
});

$('input[name="chk_print_visible"]').change(function(){
	if($(this).attr('checked'))
		$('.chk_print:visible').attr('checked',true);
	else
		$('.chk_print:visible').attr('checked',false);

	$('.chk_print:visible').trigger('change');

	
});

$(function(){
	$('#frm .dg_print').hide().remove();	
});

</script>
<?php
