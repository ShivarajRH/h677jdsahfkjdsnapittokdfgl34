<div class="container">
	<h3>Employee Sales Summary - PNH Orders Only</h3>
	<div style="clear:both">
		<?php 
			$employee_list = $this->db->query("select id,name from king_admin where access&".OFFLINE_ORDER_ROLE."=".OFFLINE_ORDER_ROLE." order by name");
			if($employee_list->num_rows())
			{
				
				
		?>
			<table class="datagrid">
				<thead>
					<th>#</th>
					<th>Name</th>
					<th>Total Sales Till Date</th>
					<th colspan="3" style="text-align: center;">This Month Sales</th>
					<th colspan="3" style="text-align: center;">Sales Today</th>
				</thead>
				<tbody>
					<tr style="font-size: 11px;">
						<td style="background: #f6efb9 !important;font-size: 13px;" colspan="3" align="center" >&nbsp;</td>
						<td style="background: #f6efb9 !important"><b>Sales</b></td>
						<td style="background: #f6efb9 !important"><b>Value (Rs)</b></td>
						<td style="background: #f6efb9 !important"><b>Cancelled (Rs)</b></td>
						<td style="background: #f6efb9 !important"><b>Sales</b></td>
						<td style="background: #f6efb9 !important"><b>Value (Rs)</b></td>
						<td style="background: #f6efb9 !important"><b>Cancelled (Rs)</b></td>
					</tr>
				
		<?php		
				$i=1;
				foreach($employee_list->result_array() as $row)
				{
					$total_emp_sales_row = $this->db->query("select COUNT(distinct o.transid) AS total_sales,
																sum((o.i_orgprice-o.i_discount-o.i_coup_discount)*o.quantity) as total_order_value 
															from king_transactions a
															join king_orders o on a.transid = o.transid
															join transactions_changelog c on c.transid = o.transid and c.msg='PNH Offline order created'
															join king_admin b on c.admin = b.id
															where b.id = ? ",$row['id'])->row_array();
					
					$total_emp_sales_today_row = $this->db->query("select COUNT(distinct o.transid) AS total_sales,
																sum((o.i_orgprice-o.i_discount-o.i_coup_discount)*o.quantity) as total_order_value 
															from king_transactions a
															join king_orders o on a.transid = o.transid
															join transactions_changelog c on c.transid = o.transid and c.msg='PNH Offline order created'
															join king_admin b on c.admin = b.id
															where b.id = ? and a.init between ? and ? ",array($row['id'],mktime(0,0,0,date('m'),date('d'),date('Y')),mktime(23,59,59,date('m'),date('d'),date('Y'))))->row_array();
					 
					 $total_emp_sales_today_row_cancelled = $this->db->query("select COUNT(distinct o.transid) AS total_sales,
																sum((o.i_orgprice-o.i_discount-o.i_coup_discount)*o.quantity) as total_order_value 
															from king_transactions a
															join king_orders o on a.transid = o.transid
															join transactions_changelog c on c.transid = o.transid and c.msg='PNH Offline order created'
															join king_admin b on c.admin = b.id
															where b.id = ? and o.status = 3 and o.actiontime between ? and ? ",array($row['id'],mktime(0,0,0,date('m'),date('d'),date('Y')),mktime(23,59,59,date('m'),date('d'),date('Y'))))->row_array();
					 
					
					$total_emp_sales_month_row = $this->db->query("select  COUNT(distinct o.transid) AS total_sales,
																round(sum((o.i_orgprice-o.i_discount-o.i_coup_discount)*o.quantity)) as total_order_value 
															from king_transactions a
															join king_orders o on a.transid = o.transid
															join transactions_changelog c on c.transid = o.transid and c.msg='PNH Offline order created'
															join king_admin b on c.admin = b.id
															where b.id = ? and o.time >= ? ",array($row['id'],mktime(0,0,0,date("m"),1,date('Y'))))->row_array();
															
					$total_emp_sales_month_row_cancelled = $this->db->query("select  COUNT(distinct o.transid) AS total_sales,
																round(sum((o.i_orgprice-o.i_discount-o.i_coup_discount)*o.quantity)) as total_order_value 
															from king_transactions a
															join king_orders o on a.transid = o.transid
															join transactions_changelog c on c.transid = o.transid and c.msg='PNH Offline order created'
															join king_admin b on c.admin = b.id
															where b.id = ? and o.actiontime >= ? and o.status = 3 ",array($row['id'],mktime(0,0,0,date("m"),1,date('Y'))))->row_array();
					
					
		?>
					<tr>
						<td><?php echo $i++;?></td>
						<td><?php echo ucwords($row['name']);?></td>
						<td><?php echo formatInIndianStyle($total_emp_sales_row['total_sales']);?></td>
						<td><?php echo formatInIndianStyle($total_emp_sales_month_row['total_sales']);?></td>
						<td><?php echo formatInIndianStyle($total_emp_sales_month_row['total_order_value']);?></td>
						<td><?php echo formatInIndianStyle($total_emp_sales_month_row_cancelled['total_order_value']);?></td>
						<td><?php echo formatInIndianStyle($total_emp_sales_today_row['total_sales']);?></td>
						<td><?php echo formatInIndianStyle($total_emp_sales_today_row['total_order_value']);?></td>
						<td><?php echo formatInIndianStyle($total_emp_sales_today_row_cancelled['total_order_value']);?></td>
					</tr>
		<?php			
				}
		?>
			</tbody>
			</table>
		<?php 		
			}
		?>		
	</div>
</div>