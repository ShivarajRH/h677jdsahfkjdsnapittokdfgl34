<?php 
$user=$this->session->userdata("admin_user");
?>
<style>
.table_grid_view th{
	padding:5px !important;
}
.sidepane{
	background:none;
}
</style>
<div style="margin-bottom:0px;padding-bottom:30px;">

<div class="container" style="font-family:arial;width: 98%">

  <h3>
 	<span style="float: right">
 		<a href="<?php echo site_url('admin/bulk_orders')?>">Auto Automent</a> 
 	</span>
 	<b>Bulk Allotment List</b>
 </h3>
 <div>
 	<?php 
 		if($allot_list_res->num_rows()){
 	?>
 		
 	<table width="60%" class="table_grid_view" cellpadding="0" cellspacing="0" border="0">
 		<thead>
 			<th><b>Slno</b></th>
 			<th><b>Allotment No</b></th>
 			<th><b>Invoice List</b></th>
 			<th><b>No of times Printed</b></th>
 			<th><b>CreatedOn</b></th>
 			<th><b>Action</b></th>
 		</thead>
 		<tbody>
 			<?php
 				$a_ind = $pg;
 				foreach($allot_list_res->result_array() as $allot_det){?>
 				<tr>
 					<td><?php echo ++$a_ind?></td>
 					<td><?php echo $allot_det['allotment_no']?></td>
 					<td><?php echo $allot_det['invoice_nos']?></td>
 					<td><?php echo $allot_det['tot_printed']?></td>
 					<td><?php echo date('d M Y h:i a',strtotime($allot_det['created_on']));?></td>
 					<td><a target="_blank" href="<?php echo site_url('admin/print_allotment_invoices/'.$allot_det['allotment_no'])?>" >Print Invoices</a></td>
 				</tr>
 			<?php }?>
 		</tbody>
 		<tfoot>
 			<td colspan="5">
 				<?php echo $pagination;?>
 			</td>
 		</tfoot>
 	</table>
 	<?php } ?>
 </div>
</div>

