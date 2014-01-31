<?php 
$inv_transit_status = array('','In-Transit','Handed-over','Delivered','Return','Picked');
	
	if($pnh_shipmets_transit_log){
		
		$transport_inv_list = array();
		$transport_list = array();
		$inv_transit_list = array();
		
		
		foreach($pnh_shipmets_transit_log as $log){
			$handleby_name='';
			if($log['hndleby_empid'])
				$handleby_name=$log['hndleby_name'];
			else if($log['hndlby_type']==3 && $log['bus_id'])
				$handleby_name=$log['bus_name'];
			else if($log['hndlby_type']==4)
				$handleby_name=$log['hndleby_name'];
			
			if(!isset($transport_inv_list[$handleby_name]))
			{
				$transport_inv_list[$handleby_name] = array();
				$transport_list[$handleby_name] = array();
			}
			
			//$transport_list[$log['hndleby_name']] =  ($log['hndleby_empid'])?ucwords($log['name']).'('.$log['role_name'].')':ucwords($log['hndleby_name']).'(Other Transport)';
			if($log['hndleby_empid'])
				$transport_list[$handleby_name]='<a href="'.site_url('/admin/view_employee/'.$log['hndleby_empid']).'" target="_blank" style="color:#cd0000">'.ucwords($log['name']).'('.$log['role_name'].')</a>';
			else if($log['bus_id'])
				$transport_list[$handleby_name]=ucwords($log['bus_name']).'(Bus Transport)';
			else if($log['hndlby_type']==4)
				$transport_list[$handleby_name]=ucwords($log['hndleby_name']).'(Courier)';
			
			array_push($transport_inv_list[$handleby_name],$log['invoices_number']);
			
			if(!isset($inv_transit_list[$log['invoices_number']]))
			{
				$inv_transit_list[$log['invoices_number']] = array();
			}
			
			foreach(explode(',',$log['invoices_number']) as $inv)
			{
				$log['inv_status'] = $this->db->query('select status,logged_on from pnh_invoice_transit_log where invoice_no = ? order by id desc limit 1',$inv)->row_array();
				$inv_transit_list[$inv] = $log;
			}
			
		}
		
		
		
		$last_tr_name = '';
		foreach ($transport_inv_list as $trans_name=>$inv_list)
		{
			
			$inv_list = explode(',',implode(',',$inv_list));
			
			$fr_list_res = $this->db->query("select distinct invoice_no,
															franchise_name 
													from pnh_m_franchise_info a 
													join king_transactions b on a.franchise_id = b.franchise_id 
													join king_invoice c on c.transid = b.transid 
													where invoice_no in (".implode(',',$inv_list).") 
													group by invoice_no 
											" );
			
			 
			
			$fr_inv_list = array();
			if($fr_list_res->num_rows())
				foreach($fr_list_res->result_array() as $fr_det)
					$fr_inv_list[$fr_det['invoice_no']] = $fr_det['franchise_name'];
			
			
?>
			<div class="trans_inv_det">
			<fieldset style="border-color:#fff;border:none;background: #fafafa">
				<legend style="color:#cd0000">
					<h3 style="margin:5px 0px"><?php echo $transport_list[$trans_name]?></h3>
				</legend>
				
			
						
				<div class="trans_inv_list">
						
					<?php 
						foreach($inv_list as $inv)
						{
							if($last_tr_name == '')
							{
								$last_tr_name = $inv_transit_list[$inv]['territory_name'];
								echo '<div class="group_box"><h5>Territory : '.$last_tr_name.'</h5>';
							}elseif($last_tr_name != $inv_transit_list[$inv]['territory_name'])
							{
								$last_tr_name = $inv_transit_list[$inv]['territory_name'];
								echo '</div><div class="group_box"><h5>Territory : '.$last_tr_name.'</h5>';
								
							}

							//$inv_transit_list[$inv]['inv_status']['status'] = rand(1,4);
					?>
						<div class="show_invoice">
							
							<?php $trans_id=@$this->db->query("select transid from king_invoice where invoice_no=? limit 1",$inv)->row()->transid?>
							<div class="status status_<?php echo $inv_transit_list[$inv]['inv_status']['status'];?>"> 
							<?php 
								echo $inv_transit_status[$inv_transit_list[$inv]['inv_status']['status']];
							?>
							</div>
							
							<?php
								$manifesto_id = $this->db->query("select id from pnh_m_manifesto_sent_log where find_in_set(?,sent_invoices) order by id desc limit 1 ",$inv)->row()->id; 
							?>
							<b>
							<a href="<?php echo site_url('/admin/invoice/'.$inv)?>" target="_blank"><?php echo $inv;?></a> | <span style="font-size:10px;"><a href="<?php echo site_url('admin/trans/'.$trans_id); ?>" target="_blank"><?php echo $trans_id; ?></a></span> 
								| <span style="font-size:10px;">Manifesto id :</span> <?php echo $manifesto_id; ?> <br>
								<span style="font-size:11px;">Box :
								<?php $bxno_det = @$this->db->query("select no_ofboxes,ref_box_no from pnh_m_manifesto_sent_log where id = $manifesto_id ")->row_array();
									if($bxno_det)
									{
										echo $bxno_det['no_ofboxes'];
										if($bxno_det['ref_box_no'])
											echo ' - no:'.$bxno_det['ref_box_no'];
									}
									else
										echo '1';
								?>
								</span> 
							</b>	
							<br/>
							<div class="fr_det" style="clear: both;color: #454545">
								<span style="float: right;font-size: 11px;text-align: right;">
									<b>On :</b> <?php echo format_datetime($inv_transit_list[$inv]['inv_status']['logged_on']) ?> <br>
									<a href="javascript:void(0)" onclick="get_invoicetransit_log(this,<?php echo $inv; ?>)" class="btn">View Transit Log</a>
								</span>
								<b style="font-size: 11px;"><?php echo $fr_inv_list[$inv];?></b><br />
								<span style="font-size: 10px;">
								<?php echo $inv_transit_list[$inv]['town_name'];?>,<?php echo $inv_transit_list[$inv]['territory_name'];?>
								</span><br>
								<?php if($inv_transit_list[$inv]['pickup_empid']){
								$pick_up_details=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_list[$inv]['pickup_empid'])->row_array();
								?>
								<div class="clear"></div>
								<div style="font-size: 10px;width:60%;margin:2px;float:left">
									To be collected by @ destination : <b><?php echo $pick_up_details['name'].'('.$pick_up_details['contact_no'].')';?></b>
								</div>
								<?php }?>
								<?php if($inv_transit_list[$inv]['inv_status']['status']==3){
									?>
									<div style="float:right; width:25%;margin-top:5px;">
										<?php 
											if(!$inv_transit_list[$inv]['is_acknowleged'])
												echo '<span style="color:red;">Not Acknowledged</span>';
										?>
									</div>
								<?php }?>
							</div>
						</div>
					<?php 		
						}
					?>
					</div>
				</div>
				</fieldset>
			</div>
<?php 			
		}
		
		
	}else{
	echo 'No shipments transit log found';
}?>

<style>
.trans_inv_det{margin-bottom: 10px;}
.show_invoice{border:1px dotted #cdcdcd;padding:5px;font-size: 13px;margin:3px;background: #FFF;width: 30%;display: inline-block;}
.show_invoice .fr_det{font-size: 11px;margin-top: 10px;}
.show_invoice .status{font-size: 11px;color: #FFF;padding:2px;float: right;font-weight: bold;padding:2px 4px;min-width:60px;text-align: center;border-radius:3px;}
.show_invoice .status_1{background: #cd0000;}
.show_invoice .status_2{background: orange;}
.show_invoice .status_3{background: green;}
.show_invoice .status_4{background: blue;}
.show_invoice .status_5{background: orange;}
.btn{background: #FDFDFD;color: #454545;font-size: 10px;font-weight: bold;padding:0px 4px;display: inline-block;margin-top: 3px;text-decoration: underline;}
.status{opacity:2;filter:alpha(opacity=40); }
.status:hover{opacity:1.0;filter:alpha(opacity=100); /* For IE8 and earlier */}

.group_box{background: #FFF;padding:5px;}
.group_box h5{margin:3px 0px;font-size: 12px;}

</style>
