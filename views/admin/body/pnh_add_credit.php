<div class="container">
<h2>Add Credits for Franchaise</h2>
	<div style="clear:both">
		<form action="" id="fid_frm">
			<select class="chzn-select" data-placeholder="Choose Franchise "  name="sel_fid" style="width:250px;" ></select>
			OR
			<b>Franchise ID</b>
			<input type="text" name="fid" id="fid_input" value="" title="" />
			<input type="submit" value="Submit"/>
		</form>
	</div>
	<div id="franchise_list_grid" style="display: none;">
		<form id="add_franchise_credits" action="<?php echo site_url('admin/pnh_process_add_credit')?>" method="post">
			<table class="datagrid" id="franchise_list_tbl" >
				<thead>
					<th width="100"><b>FranchaiseID</b></th>
					<th width="300"><b>Franchise Name</b></th>
					<th width="200"><b>Last Credited Details</b></th>
					<th width="80"><b>Pending Amount</b></th>
					<th width="80"><b>Uncleared Amount</b></th>
					<th width="80"><b>Open Order Amount</b></th>
					<th width="200"><b>New Credit</b></th>
					<th><b>Reason</b></th>
					<th width="80">&nbsp;</th>
				</thead>
				<tbody>
					
				</tbody>
			</table>
			<div align="left">
				<input type="submit" value="Add Credits" />
			</div>		
		</form>
	</div>
</div>
<style type="text/css">
	.datagrid tfoot{display: none;}
</style>
<script type="text/javascript">
var scanned_fids = new Array();
var form_lock = 0;
function get_pnh_franchises(){
	var f_sel_html = '<option value=""></option>';
	$.getJSON(site_url+'/admin/jx_get_franchiselist','',function(resp){
		if(resp.status == 'error'){
			alert(resp.message);
		}else{
			$.each(resp.f_list,function(a,item){
				f_sel_html+='<option value="'+item.pnh_franchise_id+'">'+item.franchise_name+'</option>';	
			});
		}
		$('select[name="sel_fid"]').html(f_sel_html);
		$('select[name="sel_fid"]').trigger("liszt:updated");
	});
}



	function rmvsel_row(ele)
	{
		if(confirm("Are you sure want to remove from list ?"))
		{
			var fid = $(ele).attr('pnh_fid');
				scanned_fids[fid] = 0;
			$(ele).parent().parent().remove();
		}
	}
	
$(function(){

	
	$('#fid_input').change(function(){
		$('select[name="sel_fid"]').val($(this).val()).trigger("liszt:updated");
		 
	}).inlineclick({onlySelect:false});
	 
	

	get_pnh_franchises();

	$('select[name="sel_fid"]').chosen().change(function(){
		$('#fid_input').val($(this).val());
	});
	
	
	
	
	$('#fid_frm').submit(function(){

		var fid_sel = $('select[name="sel_fid"]').val();
		var fid_inp = $('#fid_input').val();
	 
		
		
		
		var fid = $('input[name="fid"]',this).val();
			if(!fid){
				alert("Enter Franchise ID");
			}else{
				if(scanned_fids[fid] == 1){
					alert("Franchise ID Already Scanned");
				}else{
				 	
					$.post(site_url+'/admin/jx_franchaise_det','fid='+fid,function(resp){
						if(resp.status == 'error'){
							alert(resp.message);
						}else{
							$("#franchise_list_grid").show();
							 
							var fdet = resp.f_det;
								scanned_fids[fdet.pnh_franchise_id] = 1;
								
							var fdet_html = '<tr>';
								fdet_html += 	'<td>'+fdet.pnh_franchise_id+'</td>';
								fdet_html += 	'<td><a target="_blank" href="'+site_url+'/admin/pnh_franchise/'+fdet.franchise_id+'">'+fdet.franchise_name+'</a></br></br><b>Contact Number:</b>&nbsp;'+fdet.login_mobile1+','+fdet.login_mobile2+'</br></br><b>Territory|Town:</b>&nbsp;'+fdet.territory_name+' | '+fdet.town_name+'</td>';
								fdet_html += 	'<td align="left">';
								fdet_html += 	'	<div> Amount : '+fdet.credit_added+'</div>';
								fdet_html += 	'	<div> Credit by : '+fdet.credit_given_by_name+'</div>';
								fdet_html += 	'	<div> Credited On : '+fdet.created_on+'</div>';
								fdet_html += 	'</td>';
								fdet_html += 	'<td>'+fdet.pending_amount+'</td>';
								fdet_html += 	'<td>'+fdet.uncleared_amount+'</td>';
								fdet_html += 	'<td>'+fdet.open_order_amount+'</td>';
								fdet_html += 	'<td align="left"><input type="hidden" name="fid[]" value="'+fdet.franchise_id+'" /> <span style="font-size:13px;">Rs '+fdet.credit_limit+' &plus;</span> <input cr_limit="'+fdet.credit_limit+'" type="text" size="5" class="add_credit" value="0" name="new_credit['+fdet.franchise_id+']"> <br> <span class="preview_added_credit"></span> </td>';
								fdet_html += 	'<td width="300"><textarea class="add_credit_reason" style="width:98%;height:80px;" name="new_credit_reason['+fdet.franchise_id+']"></textarea></td>';
								fdet_html += 	'<td><a href="javascript:void(0)" onclick="rmvsel_row(this)" pnh_fid="'+fdet.pnh_franchise_id+'" style="color:#cd0000">Remove</a></td>';
								fdet_html += '</tr>';
							$("#franchise_list_grid table tbody").append(fdet_html);
						}
					},'json');	
				}
			}
		 
			return false;
	});
	
	
	$('.add_credit').live('keyup',function(){
		var new_credit =  $(this).val()*1;
		var cr_limit = $(this).attr('cr_limit')*1;
			if(isNaN(new_credit)){
				new_credit = 0;
			}
			$(this).parent().find('.preview_added_credit').text(' = Rs '+(cr_limit+new_credit));	
	});

	$('#add_franchise_credits').submit(function(){

		
		
		$('table tr',this).each(function(){
			var cr_amt = $('.add_credit',this).val()*1;
			var cr_reason = $('.add_credit_reason',this).val();	
				if(isNaN(cr_amt) || cr_amt==0){
					$('.add_credit',this).addClass('required_inp');	
				}else{
					$('.add_credit',this).removeClass('required_inp');
				}

				if(cr_reason == ''){
					$('.add_credit_reason',this).addClass('required_inp');	
				}else{
					$('.add_credit_reason',this).removeClass('required_inp');
				}
				
		});

		if($('.required_inp').length){
			alert("Please enter correct inputs");
			return false;
		}

		if(!confirm("Do you sure you want to add the credits ?")){
			return false;
		}

		show_frm_processing("Adding Credits,Please wait... ");
		$.post($(this).attr('action'),$(this).serialize(),function(resp){
			if(resp){
				show_frm_processing("Updated Successfully,Reloading Please wait...");
				setTimeout(function(){
					hide_frm_processing();
					location.href = location.href;	
				},1000);
			}
		});

		return false;
	});
	
});

</script>