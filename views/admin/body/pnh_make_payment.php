<div class="container">
	<h2 class="page_title">Make Payment</h2>
	<h1>
		<?php echo $fran_det['franchise_name'];?>
	</h1>
		<div>
			<div align="left"><span style="margin: 10px 0px;"><b>Payment Amount : Rs.<?php echo $pmt_amt?></b> </span></div>
			<div align="right" style="margin-bottom: 15px;"><span  style="margin: 10px 0px;"><b>From :</b><input type="text" id="frm_dt" name="frm_dt" value=""><b>To :</b><input type="text" id="to_dt" name="to_dt" value="">&nbsp;<input type="button" value="Go" id="fltr_btn"></span></div>
			
			<table class="datagrid" width="100%">
				<thead>
					<th><input type="checkbox" class="check_all"></th>
					<th>#</th>
					<th>Invoice no</th>
					<th>Transid</th>
					<th>Credit Amount(Rs)</th>
					<th>Status</th>
					<th>Created On</th>
					<th>Modified On</th>
				</thead>
				<?php if($cpmt_det->num_rows()){ 
						$i=1;?>
				<?php foreach($cpmt_det->result_array() as $c){?>
				<tbody>
					<tr>
						
						<?php if($c['shipped']==1){?>
							<td><input type="checkbox" class="invno_chkbox" value="<?php echo $c['invoice_no']?>" amount="<?php echo $c['amount']?>"></td>
						<?php }else{?>
						<td></td>
						<?php }?>
						<td><?php echo $i;?></td>
						<td><a href="<?php echo site_url("admin/invoice/{$c['invoice_no']}")?>" target="_blank"><?php echo $c['invoice_no']?></a></td>
						<td><a href="<?php echo site_url("admin/trans/{$c['transid']}")?>" target="_blank"><?php echo $c['transid']?></a></td>
						<td><?php echo $c['amount']?></td>
						<td><?php echo $c['shipped']==1?'<b>Shipped</b>':'<b>No</b>';?></td>
						<td><?php echo $c['createdon']?></td>
						<td><?php echo $c['modifiedon']?></td>
					</tr>
				</tbody>
				<?php $i++; 
						}?>
				<?php }?>
			</table>
			
			<?php if($pagination){?>
				<div id="call_log_pagi" class="pagination" align="left">
					<?php echo $pagination;?>
				</div>
			<?php }?>
			
			<div align="right">
				<input type="button" class="" value="Make Payment" id="pmt_btn">
			</div>
		</div>

	<div id="makepmt_div" style="display: none;" title="Make Payment">
	<form action="<?php echo site_url("admin/process_makepayment/{$fran_det['franchise_id']}")?>" method="post" form-validate="parsley" id="makepmt_form">
		<table  cellspacing="5px">
				<tr><td><input type="hidden" value="" name="cpmt_invnos"></td></tr>
				<tr><td><b>Total Selected Invoice</b></td><td><b>:</b></td><td><span id="ttl_selectdinv" data-required="true"></span></td></tr>
				<tr><td><b>Payment Amount (Rs)</b></td><td><b>:</b></td><td><input type="text" name="ttl_pmtamt"  class="readonly" readonly="readonly" value="" id="ttl_pmtamt" size="4"></span></td></tr>
				<tr><td><b>Franchise </b></td><td><b>:</b></td><td><input type="text"  name="fran_name" class="readonly" value="<?php echo $fran_det['franchise_name']; ?>" readonly="readonly" size="50px;"></td></tr>
				<tr><td><b>Date</b></td><td><b>:</b></td><td><input type="text" name="pmt_date"  id="pmt_date" value="" data-required="true"></td></tr>
				<tr><td><b>Cheque number </b></td><td><b>:</b></td><td><input type="text"  name="instrument_no" value="" data-required="true"></td></tr>
				<tr><td><b>Bank </b></td><td><b>:</b></td><td> <select name="bank_name" value="" data-required="true">
							<?php $banks=$this->db->query("select id,bank_name from pnh_m_bank_info order by bank_name asc");
								if($banks){?>
								<option value="">Select</option>
								<?php foreach($banks->result_array() as $b){
								?>
								<option value="<?php echo $b['id']?>"><?php echo $b['bank_name']?></option>
								<?php }}?>
							</select></td></tr>
				<tr><td><b>Payee </b></td><td><b>:</b></td><td><input type="text" class="readonly"  name="payee_name" value="<?php echo $fran_det['franchise_name']; ?>" readonly="readonly" size="50px;" data-required="true"></td></tr>
			</tr>
		</table>
	</form>
	</div>
</div>

<script>
$("#frm_dt,#to_dt").datepicker();								
var invnos=[];

$("#pmt_date").datepicker({minDate:0});

$("#pmt_btn").click(function(){
	
	$(".invno_chkbox:checked").each(function(){
			invnos.push($(this).val());
	});
	invnos.join(',');
	
	if($(".invno_chkbox:checked").length > 0  )
	{
		$("#makepmt_div").dialog("open");
	}
	else
	{
		alert("Please select invoice for out payment");
		return false;
	}
});

	$("#makepmt_div").dialog({
	modal:true,
	autoOpen:false,
	width:600,
	height:500,
	open:function(){
		$("#ttl_selectdinv").html("");
		$("#ttl_selectdinv").html($(".invno_chkbox:checked").length);
		$("input[name='cpmt_invnos']").val(invnos);
	},
	buttons:{
		'Submit':function(){
			var dlg=$(this);
			var pmtform=$("#makepmt_form",this);
			if(pmtform.parsley("validate"))
			{
				pmtform.submit();
			}
		},
		
		'Cancel':function(){
			$(this).dialog('close');
		},
	}
		
	});

$(".check_all").click(function(){
	if($(this).attr("checked"))
		$(".invno_chkbox").attr("checked",true);
	else
		$(".invno_chkbox").attr("checked",false);
});

$(".check_all ,.invno_chkbox").change(function(){
	var total = 0;
	$(".invno_chkbox:checked").each(function(){
		total+=format_number(parseFloat($(this).attr('amount')));
    });
    $('#ttl_pmtamt').val(total);
});

$("#fltr_btn").click(function(){
	if($("input[name='frm_dt']").val()=='' || $("input[name='to_dt']").val()=='')
	{
		alert("Please give valid input");
		return false;
	}
	else
	{
		location='<?=site_url("admin/make_payment/".(!$this->uri->segment(3)?$type:$this->uri->segment(3)))?>/'+$("#frm_dt").val()+"/"+$("#to_dt").val();
	}
});

</script>

