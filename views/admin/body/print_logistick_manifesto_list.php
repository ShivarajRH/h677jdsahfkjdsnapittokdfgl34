<?php $logisticks_list=$this->config->item("pnh_logistick");?>

<html>
	<head>
		<title>Logistics manifesto list</title>
		<style>
			body{font-family:arial;font-size:14px;}
			.onscreen{width:850px;margin:10px auto;}
			@media print
			  {
			  	.onscreen {width:100%}
			  }
			
			.header_part{
				width:960px;
				margin:0px auto;
			}
			.Logistick_det{
				width:400px;
				float:left;
				margin-bottom:15px;
			}
			.clear{
				clear: both;
			}
			.container{
				width:960px;
				margin:0px auto;
			}
			.footer_part{
				width:960px;
				margin:0px auto;
			}
			.verified_by{
				width:400px;
				float:left;
				margin:22px 0px 0px;
			}
			.logistick_sign{
				width:400px;
				float:right;
				margin-top:15px;
				text-align: right;
				margin:22px 0px 0px;
			}
		</style>
	</head>
	<body class="onscreen">
		<?php 
		if($logistick_manifsto_list)
		{
			foreach($logistick_manifsto_list as $log=>$det)
			{
				?>
				
				
				<?php 
					foreach($det as $logi_name=>$logi_det)
					{
				?>
					<div style="page-break-after: always">
						<h3 align="center">Logistics manifesto list</h3>
						
				<div>
					
					<div class="header_part">
						<div class="Logistick_det">
							<?php echo '<span>'.$logi_name.'<b>('.$logisticks_list[$log].')</b></span>'; ?>
						</div>
						<div style="float:right;">
							<b>Date :</b> <?php echo format_date($m_date); ?><br />
						</div>
						<div class="clear"></div>
					</div>
					
					<div class="clear"></div>
					
					<div class="container" id="wrapper">
						<table border=1 style="font-family:arial;font-size:13px;width: 100%" cellspacing="0" cellpadding="5">
							<thead>
								<tr style="background:#aaa">
									<th width="5%">Slno</th>
									<th>LRno</th>
									<th>Manifesto Id</th>
									<th>Destination</th>
									<th>Franchise</th>
									<th width="100">No of boxes</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								foreach($logi_det as $i=> $m)
								{
									?>
									<tr>
										<td><?php echo ($i+1);?></td>
										<td><?php echo $m['lrno'];?></td>
										<td><?php echo $m['manifesto_id'];?></td>
										<td>
											<?php
												 $hub_name = $this->db->query("select group_concat(distinct d.hub_name) as hub_names 
																					from pnh_m_manifesto_sent_log a 
																					join pnh_t_tray_invoice_link b on find_in_set(b.invoice_no,a.sent_invoices)
																					left join pnh_t_tray_territory_link c on c.tray_terr_id = b.tray_terr_id    
																					join pnh_deliveryhub d on d.id = c.territory_id 
																					where  a.id in (".$m['manifesto_id'].") ",$m['manifesto_id'])->row()->hub_names;
																											
												echo str_replace(',', ', ', $hub_name);
											?>
										</td>
										<td>
											<?php 
												$franchise_det=$this->db->query("select c.franchise_name from king_invoice as a
																		join king_transactions b on b.transid=a.transid
																		join pnh_m_franchise_info c on c.franchise_id=b.franchise_id
																	where a.invoice_no in (".$m['inv'].")
																	group by c.franchise_id")->result_array();
												if($franchise_det)
												{
													echo "<ol style='padding:0px;list-style:none;margin:0px;'>";
													foreach($franchise_det as $f)
													{
														?>
														<li><?php echo $f['franchise_name']; ?></li>
														<?php
													}
													echo "</ol>";
												}
											?>
										</td>
										<td>
											<?php echo $m["no_ofboxes"];?>
										</td>
									</tr>
									<?php	
								}
								?>
							</tbody>	
						</table>
					</div>
					
					<div class="clear"></div>
					<div class="footer_part">
						<div class="clear"></div>
						<div class="verified_by">
							<b>Verified by</b>
						</div>
						<div class="logistick_sign">
							<b>Logistic Seal &amp; sign</b>
						</div>
						<div class="clear"></div>
					</div>
				
				</div>
				 </div>
				<?php 
					}
				 ?>
				
				<?php 
			}	
		}
		?>
		<script type="text/javascript">
			window.print()
		</script>
	</body>
</html>

