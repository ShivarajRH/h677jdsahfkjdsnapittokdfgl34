<?php 
		
		//Order status with totals--START
			$order_status_arr=array();
			$order_status_arr[0]='Pending';
			$order_status_arr[1]='Unshipped';
			$order_status_arr[2]='Shipped';
			$order_status_arr[3]='Cancelled';
			$order_status_arr[4]='Returned';
			
			
		//Order status with totals--END
		
		$order_cond = '';
		$type = $this->input->post('type');
		
		$dr_cond = '';
		
		$is_part_ship = 0;
		
		if($type == 'all')
		{
			$sql = "select distinct c.transid,c.batch_enabled,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
								from king_orders o 
								join king_transactions c on o.transid = c.transid 
								join pnh_member_info pu on pu.user_id=o.userid 
							where c.franchise_id = ? and c.init between $st_ts and $en_ts  
							group by c.transid  
							order by c.init desc  ";
							
		}else if($type == 'shipped')
		{
			$sql = "select distinct b.transid,c.batch_enabled,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
						from shipment_batch_process_invoice_link a
						join proforma_invoices b on a.p_invoice_no = b.p_invoice_no
						join king_transactions c on c.transid = b.transid
						join king_orders o on o.id = b.order_id  
						join pnh_member_info pu on pu.user_id=o.userid 
						where c.franchise_id = ? and o.status in (1,2) and a.shipped = 1 and is_pnh = 1 and a.shipped_on between from_unixtime($st_ts) and from_unixtime($en_ts)   
						group by b.transid 
						order by a.shipped_on desc ;
					";
					
			$order_cond = " and a.status in (1,2) and d.shipped = 1 and d.shipped_on between from_unixtime($st_ts) and from_unixtime($en_ts) ";
			 
		}else if($type == 'unshipped')
		{
			$sql = "select distinct b.transid,b.batch_enabled,sum(o.i_coup_discount) as com,b.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id
							from king_orders o 
							join king_transactions b on o.transid = b.transid 
							left join proforma_invoices c on c.order_id = o.id
							left join shipment_batch_process_invoice_link d on d.p_invoice_no = c.p_invoice_no and d.shipped = 0 
							join pnh_member_info pu on pu.user_id=o.userid 
							where b.franchise_id = ? and o.status != 3  
							group by b.transid 
							order by b.init desc  ;
					";
			$order_cond = " and a.status != 3 and (d.shipped = 0 or d.shipped is null ) and t.init between $st_ts and $en_ts   ";		
		}else if($type == 'cancelled')
		{
			$sql = "select distinct b.transid,b.batch_enabled,sum(o.i_coup_discount) as com,b.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id  
						from king_orders o  
						join king_transactions b on o.transid = b.transid 
						left join proforma_invoices c on c.order_id = o.id
						left join shipment_batch_process_invoice_link d on d.p_invoice_no = c.p_invoice_no and d.shipped = 0 
						join pnh_member_info pu on pu.user_id=o.userid 
						where b.franchise_id = ? and o.status = 3  and o.actiontime between $st_ts and $en_ts   
						group by b.transid 
						order by b.init desc  ";
			$order_cond = " and a.status = 3 and a.actiontime between $st_ts and $en_ts  ";
		}else if($type == 'part_ship')
		{
			$sql = "select distinct c.transid,c.batch_enabled,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
								from king_orders o 
								join king_transactions c on o.transid = c.transid 
								join pnh_member_info pu on pu.user_id=o.userid 
							where c.franchise_id = ? and c.init between $st_ts and $en_ts  
							group by c.transid  
							order by c.init desc 
					";
			$order_cond = " and a.status in (0,1,2) ";
			$is_part_ship = 1;
		}else if($type == 'batch_closed')
		{
			$sql = "select distinct c.transid,c.batch_enabled,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
								from king_orders o 
								join king_transactions c on o.transid = c.transid 
								join pnh_member_info pu on pu.user_id=o.userid 
							where c.franchise_id = ? and c.init between $st_ts and $en_ts and batch_enabled = 0  
							group by c.transid  
							order by c.init desc 
					";
			$order_cond = " and 1 ";
			
		}
		else if($type == 'product_enquired')
		{
			$pid_enquird_res="SELECT l.*,d.name FROM pnh_franchise_pprice_enqrylog l JOIN king_dealitems d ON d.pnh_id=l.pid WHERE franchise_id=$fid and unix_timestamp(l.created_on) between $st_ts and $en_ts";
			$pids_enquired=$this->db->query($pid_enquird_res)->result_array();
			 if($pids_enquired){
			 	echo '<div align="left"><h3 id="ttl_orders_listed">Showing <b></b> Enquired Products from '.format_date(date('Y-m-d',$st_ts)).' to '.format_date(date('Y-m-d',$en_ts)).' </h3></div>';
			 	?>
					<table class="datagrid" width="100%" style="clear:both">
						<thead><th>Time</th><th>Product</th></thead>
						<tbody>
						<?php foreach($pids_enquired  as  $p){?>
						<tr>
							<td><?=format_datetime($p['created_on'])?></td>
							<td><?=$p['name']?></td>
						</tr>
						<?php }?>
						</tbody>
					</table>
				<?php }
		}
		else if($type=="fran_prodpricequote")
		{
			$fran_pricequote_res="SELECT p.*,d.name,d.id as itemid FROM pnh_franchise_price_quote p JOIN king_dealitems d ON d.pnh_id=p.pid WHERE franchise_id=$fid AND UNIX_TIMESTAMP(p.created_on) BETWEEN $st_ts and $en_ts";
			$fran_pricequotes=$this->db->query($fran_pricequote_res)->result_array();
			
			if($fran_pricequotes){
			
			echo '<div align="left"><h3 id="ttl_orders_listed">Showing <b></b> Franchise Price Quote from '.format_date(date('Y-m-d',$st_ts)).' to '.format_date(date('Y-m-d',$en_ts)).' </h3></div>';

			?>
			<table class="datagrid" width="100%" style="clear:both">
				<thead><th>Time</th><th>Product</th><th>MRP(Rs)</th><th>Offer Price(Rs)</th><th>Landing cost(Rs)</th><th>Franchise Request price(Rs)</th></thead>
				<tbody>
				<?php foreach($fran_pricequotes as $q){?>
				<tr><td><?= format_datetime($q['created_on'])?></td><td><?=$q['name']?></td><td><?= $q['mrp']?></td><td><?= $q['offrprice']?></td><td><?= $q['lprice']?></td><td><?=$q['quote']?></td></tr>
				<?php }?>
				</tbody>
			</table>
		<?php } }
	  if($type =='confrmd_crdtorders'){	
			
			$sql = " select distinct c.transid,c.batch_enabled,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
						from king_tmp_orders o 
						join king_tmp_transactions c on o.transid = c.transid 
						join pnh_member_info pu on pu.user_id=o.userid 
						where c.franchise_id = ? and c.init between $st_ts and $en_ts  and o.approval_status=1
						group by c.transid  
						order by c.init desc";
				
				$order_cond = " and 1 ";
				
			?>
	<?php } if($type =='unconfrmd_crdtorders'){
	
		
			$sql = " select distinct c.transid,c.batch_enabled,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
						from king_tmp_orders o 
						join king_tmp_transactions c on o.transid = c.transid 
						join pnh_member_info pu on pu.user_id=o.userid 
						where c.franchise_id = ? and c.init between $st_ts and $en_ts  and o.approval_status=0
						group by c.transid  
						order by c.init desc";
				
				$order_cond = " and 1 ";
	
	
	} if($type == 'rejected_crdtorders'){
			
			$sql = " select distinct c.transid,c.batch_enabled,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
						from king_tmp_orders o 
						join king_tmp_transactions c on o.transid = c.transid 
						join pnh_member_info pu on pu.user_id=o.userid 
						join king_admin a on a.id=c.rejected_by
						where c.franchise_id = ? and c.init between $st_ts and $en_ts  and o.approval_status=2
						group by c.transid  
						order by c.init desc";
				
				$order_cond = " and 1 ";
	
	
	 }?>
	<?php if($type!='product_enquired' && $type!='fran_prodpricequote')
		{
		$res = $this->db->query($sql,array($fid));
	
		$order_stat=array("Confirmed","Invoiced","Shipped","Cancelled");
		
		if(!$res->num_rows())
		{
			echo "<div align='center'><h3 style='margin-top:15px;'>No Orders found for selected dates</h3></div>".'<table class="datagrid" width="100%"></table>';	
		}else
		{
			echo '<div align="left"><h3 id="ttl_orders_listed">Showing <b></b> Orders from '.format_date(date('Y-m-d',$st_ts)).' to '.format_date(date('Y-m-d',$en_ts)).' </h3></div>';
				
?>		<table class="datagrid" width="100%" style="clear:both">
	<thead><tr><th>Time</th><th>Order</th><th>Amount</th><th>Commission</th><th>Deal/Product details</th><?php if($type!='rejected_crdtorders'){?><th>Status</th><th>Last action</th><?php }else{?><th>Remarks</th><th>Rejected Details</th><?php }?></tr></thead>
	<tbody>
	
	<?php 
		 $k = 0;
		foreach($res->result_array() as $o)
		{
			
			$ship_dets = array(); 
			$trans_ttl_orders = 0;
			if($type =='rejected_crdtorders' || $type =='unconfrmd_crdtorders')
			{
				//echo $type;die();exit;
				
				$o_item_list = $this->db->query("select a.member_id,a.status as ord_status,a.transid,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount,d.name as rejected_by,t.rejected_on,t.remarks
						from king_tmp_orders a
						join king_dealitems b on a.itemid = b.id
						join king_tmp_transactions t on t.transid = a.transid
						left join king_admin d on d.id=t.rejected_by
						where a.transid = ? $order_cond
						order by a.status,b.name
						",$o['transid'])->result_array();

				
			}
			else 
			{ 
			$o_item_list = $this->db->query("select a.member_id,a.status as ord_status,a.transid,e.invoice_no,d.packed,d.shipped,e.invoice_status,d.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount 
														from king_orders a
														join king_dealitems b on a.itemid = b.id
														join king_transactions t on t.transid = a.transid   
														left join proforma_invoices c on c.order_id = a.id 
														left join shipment_batch_process_invoice_link d on d.p_invoice_no = c.p_invoice_no 
														left join king_invoice e on e.invoice_no = d.invoice_no and d.packed = 1 and d.shipped = 1
														where a.transid = ? $order_cond 
														order by a.status,b.name  
														",$o['transid'])->result_array();
			
			}

			if(!$o_item_list)
				continue;
			
			$k++;
			
			
	?>
	<tr class="order_summ_det">
	<td width="150">
		<?=format_datetime(date('Y-m-d H:i:s',$o['time']))?>
		
		<?php
			$trans_created_by = @$this->db->query("select username from king_admin a join king_transactions b on a.id = b.trans_created_by where transid = ? ",$o['transid'])->row()->username;
			if($trans_created_by) 
				echo '<br><br> By <b>'.($trans_created_by).'</b>'
		?>
		
	</td>
	<td width="168">
		<a href="<?=site_url("admin/trans/{$o['transid']}")?>" class="link"><?=$o['transid']?></a> <br />
		Batch Enabled : <?php echo $o['batch_enabled']?'yes':'no' ?>
		<br />
		<?php 
			$sql_trans_ttls = 'SELECT STATUS,IFNULL(amt1,amt2) AS amt,totals,invoice_no
									FROM ( SELECT b.status,SUM((mrp-(discount+credit_note_amt))*a.invoice_qty) AS amt1,SUM(i_orgprice-(i_coup_discount+i_discount)*b.quantity) AS amt2,
									COUNT(b.id) AS totals,a.invoice_no
									FROM king_orders b
									LEFT JOIN king_invoice a ON a.order_id = b.id and invoice_status = 1 
									WHERE b.transid = ? GROUP BY b.status ) AS g';
				
			$trans_order_status_amt = $this->db->query($sql_trans_ttls,$o['transid']);
			foreach($trans_order_status_amt->result_array() as $to_row){
                            $ttl_trans_cost = $this->erpm->trans_fee_insu_value($o['transid'],$to_row['amt']);
                            $ttl_trans_cost = format_price($ttl_trans_cost);
                            $s_shipdate = @$this->db->query("select date_format(shipped_on,'%d/%m/%Y') as shipped_on from shipment_batch_process_invoice_link where invoice_no = ? and shipped = 1 ",$to_row['invoice_no'])->row()->shipped_on;

				if($s_shipdate!='')
					$status_msg = 'Shipped';
				else
					$status_msg = $order_status_arr[$to_row['STATUS']];
                        ?>
			<div>
                            <span class="span_count_wrap">
                                <?php echo $status_msg ?> (<b><?php echo $to_row['totals']?></b>) : <b>Rs. <span style=""><?php echo $ttl_trans_cost;?></span></b>
                            </span>
                        </div>
		<?php }?>
	</td>
	<td><?=round($o['amount'],2)?></td>
	<td><?=round($o['com'],2)?></td>
	<td style="padding:0px;">
		<table class="subdatagrid" cellpadding="0" cellspacing="0">
			<thead>
				<th>OID</th>
				<th>MemberID</th>
				<th>ITEM</th>
				<th>QTY</th>
				<th>MRP</th>
				<th>Amount</th>
				<th>Status</th>
				<th>Shipped</th>
			</thead>
			<tbody>
				<?php
					
					$order_status_flags = array();
			$order_status_flags[0] = 'Pending';
			$order_status_flags[1] = 'Batched';
			$order_status_flags[2] = 'Shipped';
			$order_status_flags[3] = 'Cancelled';
			$order_status_flags[4] = 'Returned';
			$order_status_flags[5] = 'Invoiced';
					
					$trans_ttl_shipped = 0;
					$trans_ttl_cancelled = 0;
					$processed_oids = array();
					foreach($o_item_list as $o_item)
					{
						if(!isset($processed_oids[$o_item['id']]))
							$processed_oids[$o_item['id']] = 1;
						else
							continue;
						
						$trans_is_part_shipped = $this->db->query("
								select count(*) as stat from (
								select a.id,status,ifnull(c.shipped ,0) as shipped 
									from king_orders a 
									left join proforma_invoices b on a.id = b.order_id and b.invoice_status = 1 
									left join shipment_batch_process_invoice_link c on c.p_invoice_no = b.p_invoice_no 
									where a.transid = ? and a.status != 3 
								group by shipped ) as g ",$o_item['transid'])->row()->stat;
						
						if($is_part_ship)
							if($trans_is_part_shipped != 2)
								continue;
						 
						if($o_item['ord_status'] == 1)
						{
							if($o_item['shipped'])
								$o_item['ord_status'] = 2;
							else if($o_item['invoice_status'])
								$o_item['ord_status'] = 5;
						} 
						 
						$ord_status_color = '';
						$is_shipped = 0;
						$is_cancelled = ($o_item['status']==3)?1:0;
						if($is_cancelled)
						{
							$trans_ttl_cancelled += 1;
							$ord_status_color = 'cancelled_ord';
						}else
						{
							$is_shipped = ($o_item['shipped'])?1:0;;
							if($o_item['shipped'] && $o_item['invoice_status'])
							{
								$trans_ttl_shipped += 1;
								$ship_dets[$o_item['invoice_no']] = format_date($o_item['shipped_on']);
								$ord_status_color = 'shipped_ord';
							}else if($o_item['status'] == 0)
							{
								$ord_status_color = 'pending_ord';
							}
						}
				?>
					<tr class="<?php echo $ord_status_color;?> ">
						<td width="40"><?php echo $o_item['id'] ?></td>
						<td width="40"><?php echo $o_item['member_id']?$o_item['member_id']:$o['pnh_member_id'];?></td>
						<td width="200"><?php echo anchor('admin/pnh_deal/'.$o_item['itemid'],$o_item['name']) ?></td>
						<td width="20"><?php echo $o_item['quantity'] ?></td>
						<td width="40"><?php echo $o_item['i_orgprice'] ?></td>
						<td width="40"><?php echo round($o_item['i_orgprice']-($o_item['i_coup_discount']+$o_item['i_discount']),2) ?></td>
						
						<td width="40" align="center"><?php echo $order_status_flags[$o_item['ord_status']]; ?></td>
						<td width="40" align="center"><?php echo ($is_shipped&& $o_item['invoice_status'])?'Yes':'No' ?></td>
						<td width="40" align="center" style="font-size: 10px;text-align: left;">
							<?php 
								 
								
								//foreach($ship_dets as $s_invno =>$s_shipdate)
								foreach($this->db->query("select a.invoice_no,date(from_unixtime(a.createdon)) as inv_date,round(sum(nlc*quantity)) as amt from king_invoice a join king_orders b on a.order_id = b.id where a.transid = ? and a.order_id = ? and invoice_status = 1 ",array($o['transid'],$o_item['id']))->result_array() as $invdet)
								{
									if(!$invdet['invoice_no'])
										continue;
									//$status_mrp= $this->db->query("select round(sum(nlc*quantity)) as amt from king_invoice a join king_orders b on a.order_id = b.id where a.invoice_no = '".$s_invno."' ")->row()->amt;
									$status_mrp = $invdet['amt']; 
									$s_invno = $invdet['invoice_no'];
									$i_inv_date = format_date($invdet['inv_date']);
									$s_shipdate = @$this->db->query("select date_format(shipped_on,'%d/%m/%Y') as shipped_on from shipment_batch_process_invoice_link where invoice_no = ? and shipped = 1 ",$invdet['invoice_no'])->row()->shipped_on;
									echo ' <div><a target="_blank" style="font-weight:bold;font-size:11px;color:#DA0B6E" href="'.site_url('admin/invoice/'.$s_invno).'">'.$s_invno.'</a><br />'.($s_shipdate?'(ShippedOn) '.$s_shipdate.' ':'(InvoicedOn) '.$i_inv_date.'').' </div>';
									
								}
							?>
						</td>
					</tr>	
				<?php 		
					}
					$trans_ttl_orders = count($processed_oids);
				?>
			</tbody>
		</table>
		</br>
		
	</td>
	<?php if($type!='rejected_crdtorders'){?>
	<td>
		<?php 	
			$show_part_shipment = 0;
			if($trans_ttl_orders == $trans_ttl_cancelled)
			{
				echo "Cancelled";
			}else
			{
				if(($trans_ttl_orders-$trans_ttl_cancelled) == $trans_ttl_shipped)
				{
					echo "Shipped";
				}else if($trans_ttl_shipped)
				{
					echo "Partitally Shipped";
					if($is_part_ship)
					{
						$show_part_shipment = 1;
					}
				}else {
					echo "UnShipped";
				}
			}
			 
		?>
	</td>
	<td class="<?php echo $show_part_shipment?'part_shipment':'';?>"><?=$o['actiontime']==0?"na":format_datetime(date('Y-m-d H:i:s',$o['actiontime']))?></td>
	<?php } else{?>
	<td><?php echo $o_item['remarks'] ?></td>
	<td><b>By : </b> <?php echo $o_item['rejected_by'] ?><p><b>On : </b><?php echo format_datetime(date('Y-m-d H:i:s', $o_item['rejected_on']))?></p></td>
	<?php }?>
	</tr>
	<?php }?>
	
	
	</tbody>
	</table>
	 <?php }?>
<?php }?>	 
	 <script>
	 	$('#ttl_orders_listed b').html(<?php echo $k;?>);
	 	<?php if($is_part_ship) { ?>
	 	$('.order_summ_det').each(function(){
	 		if(!$('.part_shipment',this).length)
	 		{
	 			$(this).addClass('del_order_summ_det');
	 		}
	 	});
	 	$('.del_order_summ_det').remove();
	 	$('#ttl_orders_listed b').html($('.order_summ_det').length);
	 	<?php } ?>
	 </script>
	 <style>
	 	.subdatagrid th{padding:3px !important;text-align: left;}
	 	.span_count_wrap {
				    background: none repeat scroll 0 0 #87318c;
					float: left;
					font-size: 11px;
					color: #fff;
					margin: 7px 0;
					padding: 5px 7px;
					text-align: center;
                }
	 </style>
