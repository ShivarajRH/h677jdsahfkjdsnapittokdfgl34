<?php if($pending_delivery_details){?>
<table class="datagrid" width="100%" cellpadding="2" cellspacing="0">
	<thead>
		<tr>
			<th width="3%">#</th>
			<th width="8%">Territory</th>
			<th width="10%">Town</th>
			<th>Invoices</th>
			<th width="5%"><input type="checkbox" name="select_all"></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($pending_delivery_details as $i=>$pending_inv){?>
		<tr>
			<td><?php echo $i+1; ?></td>
			<?php 
			if(!isset($terr_arr[$pending_inv['territory_name']]))
			{
				echo '<td>';
				$terr_row_span=0;
				$terr_arr[$pending_inv['territory_name']]=1;
				echo $pending_inv['territory_name'];
				echo '</td>';
			} else{
				echo '<td></td>';

			}

			$terr_row_span++;
			?>
			<td><?php echo $pending_inv['town_name']; ?></td>
			<td><?php 
			$invoices_list=explode(',',$pending_inv['outscan_invoices']);
			$invlist = array();
			$franchise_inv_link=array();
			foreach($invoices_list as $invoices)
			{
				$inv_no=substr($invoices,stripos($invoices,":")+1);
				$franchise=str_ireplace($inv_no,'',$invoices);
				$invlist[] = $inv_no;
					
				if(!isset($franchise_inv_link[$franchise]))
					$franchise_inv_link[$franchise]=array();
				$franchise_inv_link[$franchise][]='<span><a href="'.site_url('/admin/invoice/'.$inv_no).'" target="_blank">'.$inv_no.'</a></span><input type="hidden" fr_name="'.addslashes($franchise).'" name="invoice_no[]" value="'.$inv_no.'">';

				/*echo '<div>';
				 echo		$franchise.'<span><a href="'.site_url('/admin/invoice/'.$inv_no).'" target="_blank">'.$inv_no.'</a></span>';
				echo 		'<input type="hidden" fr_name="'.addslashes($franchise).'" name="invoice_no[]" value="'.$inv_no.'">';
				echo '</div>';*/
			}

			echo '<div>';
			foreach($franchise_inv_link as $franchise=> $inv)
			{
				echo '<div style="margin-bottom:10px;background:#FCFCFC;padding:5px;">';
				echo '	<h4 style="margin:3px 0px;">'.$franchise.' <span style="float:right;">'.count($inv).' <input type="checkbox" name="select" value="'.strip_tags(implode(',',$inv)).'" ></span></h4>';
				echo '	<p style="margin:3px 0px;font-size:12px;">'.implode(', ',$inv).'</p>';
				echo '</div>';


			}
			echo '</div>';


			?></td>
			<td><input type="checkbox" name="select"
				value="<?php echo implode(',',$invlist); ?>">
			</td>
		</tr>
		<?php }?>
		<tr>
			<td colspan="3" aling="right">
				<div class="pagination">
					<?php echo ($pending_inv_pi)?$pending_inv_pi:'';?>
				</div>
			</td>
			<td colspan="1" align="right" >
				<button id="save_pending_invoices" >Generate Manifesto</button>
				<button id="add_to_manifesto" >Add to Manifesto</button>
				<button id="remove_invoice_pck_list" >Remove invoice</button>
			</td>
		</tr>
	</tbody>
</table>
<?php } else{
	echo 'No pending delivery invoices found';
}?>
