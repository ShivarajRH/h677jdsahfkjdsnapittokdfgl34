<div class="container">
	<h2>Generate Franchise Account Statement - CSV</h2>
	<div style="clear:both">
		<form target="hndl_acc_statement_frm" action="<?php echo site_url('admin/dwn_franchise_accountstat_csv');?>" id="gen_stat_frm" method="post">
			<table>
				<tr>
					<td colspan="2">
						<b>Choose Franchises</b> :
						<a href="javascript:void(0)" onclick="load_all_franchises()" style="float: right">Load all</a> 
						<br />
						<select class="chzn-select" multiple="multiple" data-placeholder="Choose"  name="fids[]" style="width:300px;" ></select>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<b>Statement Date Range</b> : <br />
						<input type="text" id="from_date" size="10" name="from" value="<?php echo date('Y-m-01')?>" />
						<input type="text" id="to_date" size="10" name="to" value="<?php echo date('Y-m-d')?>" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" value="Generate Statement">
					</td>
				</tr>
			</table>
		</form>
		<iframe id="hndl_acc_statement_frm" name="hndl_acc_statement_frm" style="width: 1px;height: 1px;border: 0px;"></iframe>
	</div>
</div>	
<script type="text/javascript">
function load_all_franchises(){
	$('select[name="fids[]"] option').each(function(){
		$(this).attr('selected','selected');
	});
	$('select[name="fids[]"]').trigger("liszt:updated");
}
function get_pnh_franchises(){
	var f_sel_html = '<option value=""></option>';
	$.getJSON(site_url+'/admin/jx_get_franchiselist','',function(resp){
		if(resp.status == 'error'){
			alert(resp.message);
		}else{
			$.each(resp.f_list,function(a,item){
				f_sel_html+='<option value="'+item.franchise_id+'">'+item.franchise_name+'</option>';	
			});
		}
		$('select[name="fids[]"]').html(f_sel_html);
		$('select[name="fids[]"]').trigger("liszt:updated");
	});
	$('select[name="fids[]"]').chosen();
}



$(function(){
	get_pnh_franchises(); 
	prepare_daterange('from_date','to_date');
	$('#gen_stat_frm').submit(function(){
		
		if(!$('select[name="fids[]"] option:selected').length){
			alert("Choose atleast one franchise from the list"); 		
			return false;		
		}

		if(!$('#from_date').val() || !$('#to_date').val()){
			alert("Please enter correct date range"); 		
			return false;		
		}		
		
		if(!confirm("Are you sure want to generate statement ")){
			return false;	
		}
	});
	
});

</script>