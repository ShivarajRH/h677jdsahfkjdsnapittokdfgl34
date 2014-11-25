<?php 
 $payment_mode_types=$this->config->item('payment_mode');



?>
<html>
	<head>
		<title>Payment Advice</title>
		<style>
			body{font-family: arial;margin:10px;}
			.print_tbl{font-size: 14px;}
			.print_tbl th{font-size: 14px;padding:5px}
			.print_tbl td{font-size: 12px;vertical-align: top;}
			.task_info{margin:0px;}
			.task_info .task_town_name{border-bottom: 1px solid #000;text-align: center;padding:3px;}
			.task_info .task_name{border-bottom: 1px solid #000;text-align: center;padding:3px;}
			.print_tbl table{font-size: 14px;border-collapse: collapse;border-collapse: collapse;margin-bottom: 5px;}
			.print_tbl table th{font-size: 12px;}
			.print_tbl table td{padding:5px;}
			.tsk_blck{margin:5px;}
		</style>
	</head>
	<body onload="window.print()">
	<div id="header">
			<input style="float: right;" type="button" value="Close" onClick="window.close()" class="hide" id="noprint">
			<input style="float: right;margin-right: 10px;" type="button" value="Print" onClick="window.print()" class="hide" id="noprint" >
	</div>	
			
		
		<div id="container">
		<div align="center">
		
		
		</div>
		<?php if($pmt_modes->num_rows()){
			foreach($pmt_modes->result_array() as $pmod){ 
				if($pmod['payment_mode']!=4){ ?>
		
			<div class="task_info" style="align:center;">
				<h4 style="align:center;"><b><?php echo $payment_mode_types[$pmod['payment_mode']]?></b></h4>
				<?php 
				$pmt_advice_fran_list = $this->db->query("SELECT p.amount,p.payment_id,p.franchise_id,amount,fr.franchise_name,p.payment_mode
																		FROM pnh_payment_info p
																		#JOIN pnh_franchise_bank_details f ON f.franchise_id=p.franchise_id
																		join pnh_m_franchise_info fr on fr.franchise_id=p.franchise_id
																		WHERE p.payment_mode=? AND group_id=? GROUP BY p.franchise_id",array($pmod['payment_mode'],$group_id));
		//echo $this->db->last_query();exit;
				?>
				
			
			 <table class="print_tbl" cellpadding=0 cellspacing=0 border=1 width="100%">
				<thead>
					<th>Slno</th>
					<th>Franchisee Name</th>
					<th>Amount</th>
					
					<th>Account Details</th>
					
				</thead>
				<tbody>
				<?php $i=1;foreach($pmt_advice_fran_list->result_array() as $credit_det){?>
					<tr>
						<td><?=$i;?></td>
						<td><?=$credit_det['franchise_name'] ?></td>
						<td><?=$credit_det['amount']?></td>
						
						<?php if($pmod['payment_mode']!=0){
							$accout_det=$this->db->query("select bank_name,account_no,branch_name,ifsc_code FROM pnh_franchise_bank_details WHERE franchise_id=? order by id desc limit 1",$credit_det['franchise_id']);
							if($accout_det->num_rows()){
								$accout_det=$accout_det->row_array();
							?>
						<td>
							Account No :<?=$accout_det['account_no'];?>
							<p>Bank Name :<?=$accout_det['bank_name'];?></p>
						</td>
					
			<?php }else{?><td><?php echo "No Data Found"?></td><?php }}?>
		 		<?php $i++;}
			?>
		
				</tr>
			</tbody>
		</table>
		<?php }}}?>
		</div>
		
	</div>
</body>
</html>
 <style>
 @media print 
{
  
#noprint
{
	display:none;
	visibility: hidden;
}	
}	
</style>