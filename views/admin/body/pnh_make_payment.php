<div id="container">
<h2>Make Payment</h2>
	<div class="tab_view">
			<ul>
				<li><a href="#open_creditpmt" >Open</a></li>
				<li><a href="#processing_credit"  onclick="load_tradecredit_data(this,'processing_credit',0)">Processing</a></li>
				<li><a href="#processed"  onclick="load_tradecredit_data(this,'processed',0)">Processed</a></li>
			</ul>
			
			<div id="open_creditpmt">
				<div class="tab_view tab_view_inner">
					<ul>
						<li><a href="#weekly_ocredit" class="trg_onload" onclick="load_tradecredit_data(this,'weekly_ocredit',0)">Weekly</a></li>
						<li><a href="#bimonthly_ocredit" onclick="load_tradecredit_data(this,'bimonthly_ocredit',0)">Bi Monthly</a></li>
						<li><a href="#monthly_ocredit" onclick="load_tradecredit_data(this,'monthly_ocredit',0)">Monthly</a></li>
					</ul>
					
					<div id="weekly_ocredit">
					<h4>Weekly Open Earnings</h4>
						<div class="tab_content"></div>
					</div>
			
					<div id="bimonthly_ocredit">
					<h4>Bi Monthly Open Earnings</h4>
						<div class="tab_content"></div>
					</div>
				
					<div id="monthly_ocredit">
					<h4>Monthly Open Earnings</h4>
						<div class="tab_content"></div>
					</div>
				</div>
			</div>
			
			<div id="processing_credit">
				<div class="tab_content"></div>
			</div>
			
			<div id="processed">
			<h4>Processed Earnings</h4>
					<div class="tab_content"></div>
		</div>
		<div></div>
</div>

	<div id="credit_invoicedet" style="display:none;" title="Credit Invoice Details">
		<h4 id="franchise_det"></h4>
		<table id="credit_invdet" class="datagrid" width="100%">
			<thead><th>Sl no</th><th>Transid</th><th>Invoice no</th><th>Credit Amt</th><th>Reconciled status</th><th>Status</th><th>Created on</th></thead>
			<tbody></tbody>
		</table>
	</div>
	
	<div class="stats_bar" style="overflow: hidden;display:none;">
		<table>
		<tr>
			<td align="right">
					<div class="fl_left sel_opt">
						Territory :  
						<select class="inp" name="fil_terr" style="width: 200px;">
								<option value="">All</option>
								<?php 
									foreach($terr_list as $terr) 
										echo '<option value="'.$terr['territory_id'].'" '.(($sel_terr_id==$terr['territory_id'])?'selected':'').' >'.$terr['territory_name'].'</option>';
								?>
							</select>
					</div>
					<div class="fl_left sel_opt">
						Town :
						<select class="inp" name="fil_town" style="width: 200px;">
							<option value="">Choose </option>
							<?php 
								foreach($town_list as $town) 
									echo '<option value="'.$town['town_id'].'"  '.(($sel_town_id==$town['town_id'])?'selected':'').'  >'.$town['town_name'].'</option>';
							?>
							</select>
					</div>
					</td>
				</tr>
			</table>
	</div>

		<div id="update_refid_dlg" title="Update Reference Details" style="display:none;">
		  <form id="pmt_ref_updfrm" action="<?php echo site_url("/admin/jx_update_credit_process_status")?>" method="post">
		  <div id="reference_pmt_det_payload" style="display: none;"></div>
			<table class="datagrid" width="100%" id="pmt_det">
				<thead>
					<th>Slno</th>
					<th>Franchisee</th>
					<th>Payment Mode</th>
					<th>Income</th>
					<th>Ref Id</th>
				</thead>
				<tbody></tbody>
			</table>
			 </form> 
		</div>
		
		<div id="invoicedet_bloc" style="display: none;" title="Invoice Details">
			<table class="datagrid" width="100%" id="invoice_det">
				<thead>
					<th>Sl no</th>
					<th>Franchise Name</th>
					<th>Amount</th>
					<th>Order Details</th>
				</thead>
				<tbody></tbody>
			</table>
		</div>
</div>
<script>
$('.tab_view').tabs();

$('.trg_onload').trigger('click');

function load_tradecredit_data(ele,type,pg)
{
	loaded_logele = ele;
	loaded_logtype = type;

	$($(ele).attr('href')+' div.tab_content').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	$.post(site_url+'/admin/jx_loadcreditinfo/'+type+'/'+pg,'',function(resp){
		var tbl_cont='';
		
		if(type != 'processing_credit' && type != 'processed' )
			tbl_cont+=$($(ele).attr('href')+' div.tab_content').html(resp.log_data+resp.pagi_links+resp.process_btn);
		else
			tbl_cont+=$('#'+resp.type+' div.tab_content').html(resp.log_data+resp.pagi_links);

		$($(ele).attr('href')+' div.tab_content .datagridsort').tablesorter();
	
			

		$(".check_all").click(function(){
			if($(this).attr("checked"))
				$(".credit_chkbox").attr("checked",true);
			else
				$(".credit_chkbox").attr("checked",false);
	});
	
		$(".check_updateall").click(function(){
	
			if($(this).attr("checked"))
				$(".processeingpmt_chkbox").attr("checked",true);
			else
				$(".processeingpmt_chkbox").attr("checked",false);
	});
	
	},'json');

	$('.log_pagination a').live('click',function(e){
		e.preventDefault();

		$.post($(this).attr('href'),'',function(resp){
			$('#'+resp.type+' div.tab_content .datagridsort').tablesorter();
			if(type == 'weekly_ocredit')
	{
				$('#'+resp.type+' div.tab_content').html(resp.log_data+resp.pagi_links+resp.process_btn);
	}
			if(type == 'processing_credit')
	{
				$('#'+resp.type+' div.tab_content').html(resp.log_data+resp.pagi_links+resp.processed_btn);
	}
		},'json');
	});
}

function view_invoicedet(invno)
{
	$("#credit_invoicedet").data('invno',invno).dialog("open");
}

$("#credit_invoicedet").dialog({
	modal:true,
	autoOpen:false,
	width:'650',
	height:'auto',
	open:function(){
			var dlg=$(this);
		$("#credit_invdet tbody").html("");
		var tbl_html='';
		
		$('div #credit_invoicedet #credit_invdet tbody').html('<div align="center"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
		$.post(site_url+'/admin/jx_getinvoice_creditdet',{invno:dlg.data('invno')},function(resp){
			if(resp.status=='success')
			{
				
				$("#franchise_det").html(resp.fran_name);
				var c = 1;
				$.each(resp.inv_credit_det,function(i,a){
					tbl_html+='<tr>';
					tbl_html+='<td>'+ c +'</td>';
					tbl_html+="<td><a href='"+site_url+"/admin/trans/"+a.transid+"'  target='_blank'>"+a.transid+"</a></td>";
					tbl_html+="<td><a href='"+site_url+"/admin/invoice/"+a.invoice_no+"'  target='_blank'>"+a.invoice_no+"</a></td>";
					tbl_html+='<td>'+a.amount+'</td>';
					tbl_html+='<td>'+a.reconcile_status+'</td>';
					tbl_html+='<td><b>'+a.shipped_status+'</b></td>';
					tbl_html+='<td>'+a.createdon+'</td>';
					c++; });
				

				$("#credit_invdet tbody").html(tbl_html);
			}
		},'json');

		
		},
		buttons:{
			'Close' :function(){
				$(this).dialog('close');
	}
	}
});
		


function process_pmt()
{
	var selected_pmt=$(".credit_chkbox:checked").length;
	if(selected_pmt == 0)
	{
		alert("Please select atleast one to process credit");
		return false;
	}
	else
	{
		$("#process_btn").attr("disabled","disabled"); 
		var credit_invnos=[];

		$(".credit_chkbox:checked").each(function(){
			credit_invnos.push($(this).val());
    });
		credit_invnos=credit_invnos.join(',');

		if(confirm('Are you sure want to process '+selected_pmt+' credits? '))
		{
			$.post(site_url+'/admin/jx_update_credit_printstatus',{invno:credit_invnos},function(resp){
				if(resp.status=='success')
	{
					window.location.reload();
					window.open(site_url+'/admin/paymentadvice_print/'+resp.group_id);
					
				}
				
			},'json');
		}
	}
}

function update_pmtprocessed_status()
{
	var selected_pmt_ids=$(".processeingpmt_chkbox:checked").length;
	if(selected_pmt_ids == 0)
	{
		alert("Please select atleast one Payment to update");
		return false;
	}
	else
	{
		var pmt_ids  =[];
		var error_flg = 0;
		var reftext = [];
		$(".processeingpmt_chkbox:checked").each(function(){

			pmt_ids.push($(this).val());

			var trEle = $(this).parents('tr:first');

			$("textarea[name='ref_txt[]']",trEle).each(function(){

				 reftext.push($(this).val());
				 
				 if(reftext.length == 0 || reftext == '')
				 {
					 error_flg+=1; 
					 $("textarea[name='ref_txt[]']",trEle).addClass('error_inp');
	}
			});

		});


		// to access wrapper elements $(ele).parents('')
		// to access children $('textarea',this)		
		
		pmt_ids=pmt_ids.join(',');

		reftext =reftext.join(',');
		
		if(error_flg)
		{
			alert("Please input Payment Reference  details");
			return false;
		}
		if(confirm("Are you sure want to update?"))
		{
			$("#updateprocess_btn").attr("disabled",true);
			$.post(site_url+'/admin/jx_update_credit_process_status',{pmt_id:pmt_ids,reftext:reftext},function(resp){
				if(resp.status == 'success')
				{
					alert("Status updated successfully");
					$("#updateprocess_btn").attr("disabled",false);
					window.location.reload();
				}
				else
				{
					alert(resp.msg);
					return false;
				}
			},'json');
		}
	}
}

function pmt_reprint(groupid)
{
	window.open(site_url+'/admin/paymentadvice_print/'+groupid);
}

function update_pmtreference(group_id)
{
	$("#update_refid_dlg").data('group_id',group_id).dialog('open');
}

$("#update_refid_dlg").dialog({

	modal:true,
	autoOpen:false,
	width:500,
	height:500,
	open:function(){
		var dlg=$(this);
		var groupid=dlg.data('group_id');
		$("#pmt_det tbody").html("");
		$.getJSON(site_url+'/admin/jx_getpmt_invoicedet/'+groupid+'/'+0,function(resp){
			if(resp.status == 'success')
			{
				var pmtdata_html='';
				var c = 1;
				$.each(resp.pmt_det,function(i,a){
					pmtdata_html+='<tr>';
					pmtdata_html+='<td>'+c+'</td>';
					pmtdata_html+="<td><input type='hidden' name='pmt_id[]' value="+a.payment_id+"><a href='"+site_url+"/admin/pnh_franchise/"+a.franchise_id+"'  target='_blank'>"+a.franchise_name+'</a></td>';
					pmtdata_html+='<td>'+resp.pmt_modes[a.payment_mode]+'</td>';
					pmtdata_html+='<td>'+a.amount+'</td>';
					pmtdata_html+='<td><textarea name="ref_txt[]" value=""></textarea></td>';
					pmtdata_html+='</tr>';
					c++;});
				$("#pmt_det tbody").html(pmtdata_html);
			}
		},'json');
		
		},
		buttons:{
		'Close':function(){
			$(this).dialog('close');
			},
		'Submit':function(){
			var pmt_updateform=$("#pmt_ref_updfrm",this);
				
					var dlg= $(this);
					var error_flg=0;
					var frmEle = $("#pmt_det");
					var reference_det_inp_str = '';
					$("#pmt_det tbody tr").each(function(){
						pmt_id=$("input[name='pmt_id[]']",this).val();
					 	ref_txt=$("textarea[name='ref_txt[]']",this).val();
						if(ref_txt.length==0 || ref_txt=='')
						{
							error_flg+=1;
							$('textarea[name="ref_txt[]"]',this).addClass('error_inp');
						}
						
						reference_det_inp_str += '<div style="display:none">';
						reference_det_inp_str += '<input type="hidden" name="pmt_id[]" value="'+pmt_id+'" >';
						reference_det_inp_str += '<input type="hidden" name="ref_txt[]" value="'+ref_txt+'" >';
						reference_det_inp_str += '</div>';
					});
					
					if(error_flg)
					{
						reference_det_inp_str = '';
						alert("Please enter valid reference details");
						return false;
					}
					else
					{
						
						$("#reference_pmt_det_payload").html(reference_det_inp_str);
						psubmit = true;
						$("#pmt_ref_updfrm").submit();
						$(this).dialog('close');
					}
				}
			}
		
});

function view_invoicedetbypmtid(pmtid)
{
	$("#invoicedet_bloc").data('pmtid',pmtid).dialog('open');
}

$("#invoicedet_bloc").dialog({
	modal:true,
	autoOpen:false,
	width:1000,
	height:500,
	open:function(){
		var dlg=$(this);
		$("#invoice_det tbody").html("");
		var pmt_id=dlg.data('pmtid');
		$.getJSON(site_url+'/admin/jx_getpmt_invoicedet/'+0+'/'+pmt_id,function(resp){
				if(resp.status == 'success')
				{
					var c=1;
					var tbl_html='';
					var tr_tblhtml='';
					var b=1;
					$.each(resp.invoice_det,function(i,a){
					tbl_html+='<tr>';
					tbl_html+='<td>'+c+'</td>';
					tbl_html+="<td><a href='"+site_url+"/admin/pnh_franchise/"+a.franchise_id+"'  target='_blank'>"+a.franchise_name+'</td>';
					tbl_html+='<td>'+a.income+'</td>';
					tbl_html+='<td>';
					tbl_html+='<table class="small datagrid"><thead><th>Sl no</th><th>Transid</th><th>Income(Rs)</th><th>Invoice</th><th>Created On</th></thead><tbody>';
					$.each(resp.order_res[i],function(i,o){
						tbl_html+='<tr>';
						tbl_html+='<td>'+b+'</td>';
						tbl_html+="<td><a href='"+site_url+"/admin/trans/"+o.transid+"'  target='_blank'>"+o.transid+"</td>";
						tbl_html+='<td>'+o.amount+'</td>';
						tbl_html+="<td><a href='"+site_url+"/admin/trans/"+o.invoice_no+"'  target='_blank'>"+o.invoice_no+"</td>";
						tbl_html+='<td>'+o.created_on+'</td>';
						b++;});
					tbl_html+='</tbody>';
					tbl_html+='</tr>';
					tbl_html+='</table>';
					tbl_html+='</td>';
					tbl_html+='</tr>';
					c++;});
					$("#invoice_det tbody").html(tbl_html);
				}
			},'json');
		},
	buttons:{
	'Close':function(){
			$(this).dialog('close');
		},
	}	
});
</script>

<style>
.error_inp {
    border: 1px solid #CD0000 !important;
}
.sel_opt
{
	 margin: 8px 15px 8px 2px;
}
.stats_bar{padding:5px;background: #f2f2f2;}

</style>