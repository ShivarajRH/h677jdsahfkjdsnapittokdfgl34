<html>
	<head>
		<title>Print Return Receipt</title>
		<style>
			body{font-family: Arial;font-size: 12px;}
			table{font-size: 13px;border-collapse: collapse}
		</style>
	</head>
	<body>
		<div align="left">
			
			<div class="receipt_content">
				<table border=1 cellpadding="10" width="100%" cellspacing="0" style="border:1px solid #000">
					<tr>
						<td colspan="2" align="center" style="border:1px solid #000">
							<b style="font-size: 16px;">Return Receipt</b> 
							<p style="margin-top: 0px;text-align: right;">
								<?php echo 'Plot 3B1,KIADB Industrial area,Kumbalgudu 1st Phase ,Bangalore - 560074<br /><b>PH - 08028437605 / 18002001996 </b>';?>
							</p>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table width="100%">
								<tr>
									<td align="left">
											Receipt no : 
											<b><?php echo $return_proddet['return_id'];?></b>
									</td>
									<td align="center">
											<img src="data:image/png;base64,<?php echo base64_encode(generate_barcode($return_proddet['return_id'],200,40,2));?>" >
									</td>
									<td align="right">
											Date : 
											<b><?php echo format_date(date('Y-m-d'));?></b>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					
					<tr>
						<td><b>Franchise </b></td><td><?php echo $fran_det['franchise_name'];?></td>
					</tr>
					<tr>
						<td><b>Address </b></td><td><p><?php echo $fran_det['address'].($fran_det['city']?'<br>'.$fran_det['city']:'').($fran_det['postcode']?' - '.$fran_det['postcode']:'').($fran_det['state']?'<br>'.$fran_det['state']:'');?></p></td>
					</tr>
					<tr>
						<td><b>Contact no </b></td><td><?php echo $fran_det['login_mobile1'];?></td>
					</tr>
					<tr>
						<td><b>Invoice no </b></td><td><?php echo $return_proddet['invoice_no'];?></td>
					</tr>
					
					<tr>
						<td><b>Products</b></td>
						<td style="padding:0px 0px;border:none">
							<table width="100%" border=0 style="" cellpadding="10" cellspacing="10" style="border:0px solid #FFF;border-collapse: collapse">
								<thead>
									<th  style="border-bottom: 1px solid #000" align="left">Slno</th>
									<th  style="border-bottom: 1px solid #000" align="left">Product</th>
									<th  style="border-bottom: 1px solid #000" align="center">Qty</th>
									<th  style="border-bottom: 1px solid #000" align="right">Price</th>
								</thead>
								<tbody>
									<tr>
										<td align="left" width="20">1</td>
										<td width="300" align="left"><?php echo $return_proddet['product_name'];?>
											<?php
												if($return_proddet['imei_no'])
													echo '<br> <div style="font-size:80%"><b>IMEINO</b> : '.$return_proddet['imei_no'].'</div>';
											?>
										</td>
										<td align="center"><?php echo $return_proddet['qty'];?></td>
										<td align="right"><?php echo $return_proddet['price'];?></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td valign="top"><b>Reshipped By</b></td>
						<td>_________________________________________   On :__________________</td>
					</tr>
					<tr>
						<td valign="top"><b>Receipt Notes</b></td>
						<td>	_______________________________________________________________ <br><br> 
								_______________________________________________________________ <br><br>
								_______________________________________________________________ <br><br>
						</td>
					</tr>
					 
				</table>
			</div>
		</div>
	</body>
</html>