<?php
/**
 * Manage offers inner page - For pagination purpose seperate view file
 * Created By - Shivaraj<shivaraj@storeking.in
 */
?>
<!-- ====================================< INSURANCE BLOCK  >==================================== -->
<?php
if($type=='insurance_offers')
{
?>
<div id="insurance_offers">
	<div class="filters_block">
		<?php 
			if(!empty($insu_territory_arr))
				$terr_list=$this->db->query("select * from pnh_m_territory_info where id in ($insu_territory_arr) order by territory_name asc"); 
			else 
				$terr_list=$this->db->query("select * from pnh_m_territory_info order by territory_name asc");

			if(!empty($insu_town_arr))
				$town_list=$this->db->query("select * from pnh_towns where id in ($insu_town_arr) order by town_name asc");
			else
				$town_list=$this->db->query("select * from pnh_towns order by town_name asc");

			if(!empty($insu_town_arr))
				$fran_list=$this->db->query("select * from pnh_m_franchise_info where franchise_id in ($insu_franchisee_arr) order by franchise_name asc");
			else
				$fran_list=$this->db->query("select * from pnh_m_franchise_info order by franchise_name asc");

		?>
				<div class="filter">
					<label style="margin-right: 10px;">Territory : </label>
					<select class="territory_filter">
						<option value="0">All</option>
						<?php foreach($terr_list->result_array() as $t) { ?>
							<option value="<?=$t['id'];?>" <?=($t['id'] == $territory_id)? 'selected': "";?> ><?=$t['territory_name'];?></option>
						<?php } ?>
					</select>
					<br>
					<label style="margin-right: 29px;">Town :</label>
					<select class="town_filter">
						<option value="0">All</option>
						<?php foreach($town_list->result_array() as $t) { ?>
							<option value="<?=$t['id'];?>" <?=($t['id'] == $town_id)? 'selected': "";?> ><?=$t['town_name'];?></option>
						<?php } ?>
					</select>
					<br>
					<label>Franchise :</label>
					<select class="franchise_filter">
						<option value="0">All</option>
						<?php foreach($fran_list->result_array() as $f) { ?>
							<option value="<?=$f['franchise_id'];?>" <?=($f['franchise_id'] == $franchise_id)? 'selected': "";?> ><?=$f['franchise_name'];?></option>
						<?php } ?>
					</select>
				</div>
				<div class="filter">
					<label style="margin-right: 18px;">Order Status :</label>
					<select class="order_status_filter">
						<option value="all" <?=$order_status == 'all' ? 'selected':''; ?>>All</option>
						<option value="1" <?=$order_status == '1' ? 'selected':''; ?>>Delivered</option>
						<option value="0" <?=$order_status == '0' ? 'selected':''; ?>>Not Delivered</option>
					</select>
					<br>
					<label style="margin-right: 12px;">Details Status :</label>
					<select class="detail_status_filter">
						<option value="all" <?=$detail_status == 'all' ? 'selected':''; ?>>All</option>
						<option value="1" <?=$detail_status == '1' ? 'selected':''; ?>>Updated</option>
						<option value="0" <?=$detail_status == '0' ? 'selected':''; ?>>Not Updated</option>
					</select>
					<br>
					<label>Payment Status :</label>
					<select class="payment_status_filter">
						<option value="all" <?=$payment_status == 'all' ? 'selected':''; ?>>All</option>
						<option value="0" <?=$payment_status == '0' ? 'selected':''; ?>>Paid</option>
						<option value="1" <?=$payment_status == '1' ? 'selected':''; ?>>Pending</option>
					</select>
				</div>
				<div class="filter">
					<label>Feedback Status :</label>
					<select class="feedback_status_filter">
						<option value="all" <?=$feedback_status == 'all' ? 'selected':''; ?>>All</option>
						<option value="1" <?=$feedback_status == '1' ? 'selected':''; ?>>Received</option>
						<option value="0" <?=$feedback_status == '0' ? 'selected':''; ?>>Pending</option>
					</select>
					<br>
					<label style="margin-right: 31px;">Offer Status :</label>
					<select class="offer_status_filter">
						<option value="all" <?=$offer_status == 'all' ? 'selected':''; ?>>All</option>
						<option value="1" <?=$offer_status == '1' ? 'selected':''; ?>>Details Not Updated</option>
						<option value="2" <?=$offer_status == '2' ? 'selected':''; ?>>Feedback Pending</option>
						<option value="3" <?=$offer_status == '3' ? 'selected':''; ?>>Ready for Insurance</option>
						<option value="4" <?=$offer_status == '4' ? 'selected':''; ?>>Insurance Generated</option>
						<option value="5" <?=$offer_status == '5' ? 'selected':''; ?>>Order Canceled</option>
						<option value="6" <?=$offer_status == '6' ? 'selected':''; ?>>Un Shipped Insurance</option>
						<option value="7" <?=$offer_status == '7' ? 'selected':''; ?>>Shipped Insurances</option>
					</select>
					<br>
					<?php
						if(!isset($no_results_found))
						{
?>
								<div  class="export_block">
									<span class="export_lnk button button-tiny" href="">Export to CSV File</span>
								</div>
					<?php } ?>
				</div>
				<div class="filter">
					<form id="srch_insurance_frm" style="margin-top:5px;" onsubmit="return srch_insurance_frm(this);">
						<label>Search:</label>
						<input type="text" class="inp srh_no" size="11" value="<?=($srh_no == '0')? '':$srh_no; ?>" placeholder="IMEI,InsuranceId,InvoiceNo,orderid,offerSno" style="width: 185px;" title="Search offer by IMEI,insuranceid,order_id,offer sno or invoice_no">
						<input type="submit" value="Go" >
 					</form>
				</div>
				<div class="filter">
					<form style="margin-top:5px;">
						<label>Order Date:</label><input type="text" class="date_from" size="11" value="<?=($date_from =='0')?'':$date_from; ?>" placeholder="YYYY-MM-DD" title="From">
							<b>-</b><input type="text" class="date_to" size="11" value="<?=($date_to=='0')?'':$date_to; ?>" placeholder="YYYY-MM-DD" title="To">
							<button type="button" class="button button-tiny date_filter_submit" style="color:#000000 !important">Go</button>
					</form>
				</div>
<!--						<br />
						<div class="filter">
							<form style="margin-top:5px;">
								<b>Processed Date:</b><input type="text" class="date_from" size="11" value="<?=($date_from =='0')?'':$date_from; ?>" placeholder="YYYY-MM-DD" title="From">
									<b>-</b><input type="text" class="date_to" size="11" value="<?=($date_to=='0')?'':$date_to; ?>" placeholder="YYYY-MM-DD" title="To">
									<button type="button" class="button button-tiny date_filter_submit" style="color:#000000 !important">Go</button>
							</form>
						</div>-->
<!--				<div class="filter">
					<form style="margin-top:5px;">
							<b>Search Invoice No. :</b><input type="text" class="srh_invoice_no" size="11" value="<?=($srh_invoice_no == 0)?'':$srh_invoice_no; ?>" placeholder="Invoice number">
					</form>
				</div>-->
        </div>
       
        <div class="clear">&nbsp;</div>
<?php
        if(isset($no_results_found))
        {
?>
                <p>No Results found.</p>
<?php
        }
        else
        {
?>
            <div align="right"  class="log_pagination fl_right">
                <span class="pagination_snip"><?php echo $offers_pagination; ?></span>
            </div>
            <div class="block_notify">Total <?=$ttl_rows;?> items found. Showing <?=$pg_from;?> to <?=$pg_limit;?></div>
            
           
            <div class="clear">&nbsp;</div>
            <table class="datagrid smallheader noprint datagridsort" width="100%">
                <thead>
                        <tr>
                                <th width="1%">#</th>
                                <th width="2%">OfferID</th>
                                <th width="4%">Order Date</th>
                                <th width="4%">Processed Date</th>
                                <th width="6%">Member Name</th>
                                <th width="10%">Franchise Name</th>
                                <th width="10%">Deal Details</th>
                                <th width="4%">Offer towards</th>
                                <th width="4%">Insurance Amount</th>
                                <th width="4%">Member Insurance Details</th>
                                <th width="3%">Order Status</th>
                                <th width="4%">Feedback Status</th>
                                <th width="4%">Invoice Number</th>
                                <th width="2%">Payment</th>
                                <!--<th width="4%">Doc. Collected</th>-->
                                <th width="4%">Actions <br><label for="chk_all_insurances">Check All</label><input type="checkbox" name="chk_all_insurances" id="chk_all_insurances" class="chk_all_insurances"/></th>									
								<th width="1%">Sent?</th>
                        </tr>	
                </thead>
                <tbody>
<?php
				foreach($offers_insurance as $i=>$offer)
				{
					$pnh_id = $offer['pnh_pid'];
					$dealname = $offer['dealname'];
?>
                        <tr class="insurance_table" sno="<?=$offer['sno'];?>" member_id="<?=$offer['member_id'];?>" date="<?=$offer['date'];?>" territory_id="<?=$offer['territory_id'];?>" town_id="<?=$offer['town_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>" insurance_id="<?=$offer['insurance_id'];?>">
							<td><?=(++$i)+$pg?></td>
							<td><?="".$offer['sno'];?></td>
							<td><?=format_datetime($offer['created_on']);?></td>
							<td><?=format_datetime($offer['processed_on']);?></td>
							<td>
								<a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id']);?>" target="_blank"><?=$offer['first_name'];?></a>
							</td>
							<td><a href="<?=site_url("admin/pnh_franchise/".$offer['franchise_id']);?>" target="_blank"><?=$offer['franchise_name'];?></a></td>
							<td>
								    <?php
								    if($offer['order_for'] == '2') { 
									echo '<span class="payment_pending" style="color:#7db500;">Key</span> ';
								    }
								    echo '<a href="'.site_url("admin/trans/".$offer['transid_ref']).'" target="_blank">'.$offer['transid_ref'].'</a>';
								    ?>
								    &nbsp;-&nbsp; <a href="<?=site_url("admin/pnh_deal/".$pnh_id)?>" target="_blank"><?=$dealname;?></a>
								<br/>
								<div style="font-size:11px;background:#fcfcfc;padding:5px;width:205px;text-align:center">
									<?php 
									    if($offer['imei_no'] ) {
											echo '<b>IMEI : '.$offer['imei_no'].'</b>';
									    }
									    elseif($offer['order_for'] == '2' && $offer['nonsk_imei_no']!='' ) { 
											echo '<b>Non SK IMEI : '.$offer['nonsk_imei_no'].'</b>';
									    }
									?>
								</div>
							</td>
							<td>Rs. <?=$offer['offer_towards'];?></td>
							<td><?php echo ( $offer['offer_value'] == '0' ) ? 'Free' : "Rs. ".formatInIndianStyle($offer['offer_value'])."&nbsp;&nbsp;&nbsp;"; ?></td>
							<td><?php
								
								
								if($offer['details_updated'] == '0' )
								{
									echo '&nbsp; &nbsp;<a class="fl_right button button-tiny button-flat-caution" onclick="update_member_insurance_details(\''.$offer['insurance_id'].'\',1);">Update Member</a>';
								}
								else
								{
									echo '&nbsp; &nbsp;<a class="fl_right button button-tiny button-flat-action" onclick="update_member_insurance_details(\''.$offer['insurance_id'].'\',0);">View Member</a>';
								}
							?></td>
							<td><?php
									$arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");

									if( $offer['delivery_status'] == '0' ) {
										echo '<span class="payment_pending">Not delivered</span>';
									}
									else
									{
										echo '<span class="payment_pending" style="color:#7db500;">Delivered</span>';
									}
								?>
							</td>
							<td>
<?php                                    
								if($offer['feedback_status'] == '0' && $offer['process_status'] == '0')
								{
									echo '<span onclick="resend_mem_feedback('.$offer['user_id'].',\''.$offer['franchise_id'].'\')" class="button button-tiny button-flat-action">Resend</span> <div class="clear" style="margin-top:10px;"></div>';
									echo '<span style="color:orange;font-weight:bold;font-size:13px"><a class="button button-tiny button-primary" onclick="confirm_feedback(\''.$offer['member_id'].'\');">Confirm</a></span>';
								}
								else {
									echo '<span>'.$offer['feedback_value'].'</span>';
								}
?>
							</td>
							<td><a href="<?=site_url()."admin/invoice/".$offer['invoice_no'];?>" target="_blank"><?=$offer['invoice_no'];?></a></td>
							<td><?php 
								if($offer['pending_payment'] == '1')
								{
										echo '<span class="payment_pending">Pending</span>';
								}
								else {
										echo '<span class="payment_pending" style="color:#7db500;">Paid</span>';
								}
								?></td><!--<td><input type="checkbox" name="doc_collected" id="doc_collected" value="1" <?php // echo ( $offers['is_doc_collected'] == 1) ? 'checked': "";?>  /></td>-->
							
							<td>
<?php
								if($offer['order_status'] == '3')
								{
									echo '<span style="background:#cd0000;color:white;padding:3px 5px;">Order Canceled</span>';
								}
								else
								{
									if($offer['nonsk_imei_no'])
									{
										if($offer['process_status'] == '0') {
											echo '<input type="checkbox" name="chk_insurance" class="chk_insurance" value="1"> Ready for Insurance';
										}else
										{
											echo '<span class="view_insurance_lnk payment_pending" style="color:#7db500;">Insurance Given</span>';
											echo '<a href="'.site_url("admin/insurance_print_view/".$offer['insurance_id']."").'" target="blank" class="view_insurance_lnk" style="color:#F05702;">View</a>';
											echo '<a href="javascript:void(0)" style="color:#F05702;" onclick="show_insurance_print_log('.$offer['insurance_id'].')">Log</a>';
										}
									}
									else
									{
										if( $offer['delivery_status'] == '0'
												&& $offer['process_status'] == '0') {

											echo '<span class="delivery_pending">Not delivered</span>';
										}
										else
										{
											if($offer['details_updated'] == '0'
													&& $offer['delivery_status'] == '1' 
													&& $offer['process_status'] == '0') { //($have_insu_details != 0)

												echo '<span class="details_pending">Details Not Updated</span>';
											}
											else
											{
													if( $offer['feedback_status'] == '0'
															&& $offer['delivery_status'] == '1' 
															&& $offer['process_status'] == '0' ) {

															echo '<span style="color:orange;font-weight:bold;font-size:13px"><a class="button button-tiny button-flat-primary" onclick="confirm_feedback(\''.$offer['member_id'].'\');">Feedback Pending</a></span>';
													}
													else
													{
															if( $offer['pending_payment'] == '1'
																	&& $offer['details_updated'] == '1' 
																	&& $offer['delivery_status'] == '1' 
																	&& $offer['feedback_status'] == '1'
																	&& $offer['process_status'] == '0' ) {

																echo '<span class="payment_pending">Pending Payment</span>';
															}
															else
															{
																	//if($offer['feedback_status'] == 1 && $offer['delivery_status'] == 1 && $offer['process_status'] == 0)
																	if( $offer['process_status'] == '0'
																			&& $offer['pending_payment'] == '0'
																			&& $offer['delivery_status'] == '1' 
																			&& $offer['feedback_status'] == '1' ) {
																			echo '<input type="checkbox" name="chk_insurance" class="chk_insurance" value="1"> Ready for Insurance';
																	}
																	else
																	{
																		if($offer['process_status'] == '1' ) {
																				//echo "<span style='color:green;font-weight:bold;font-size:13px'>Insurance Given</span>";
																				if($offer['process_status'] == '1' && $offer['order_status'] != '3'  )
																				{ //button button-tiny button-flat-action
																					   echo '<span class="view_insurance_lnk payment_pending" style="color:#7db500;">Insurance Given</span>';
																					   echo '<a href="'.site_url("admin/insurance_print_view/".$offer['insurance_id']."").'" target="blank" class="view_insurance_lnk" style="color:#F05702;">View</a>';
																					   echo '<a href="javascript:void(0)" style="color:#F05702;" onclick="show_insurance_print_log('.$offer['insurance_id'].')">Log</a>';
																				}
																		}
																		else
																			echo '--';
																	}
															}
													}
											}

										}
									}
								}
?>
							</td>
							<td>
								<?php
									if($offer['process_status'] == '1') {
										echo '<input type="checkbox" name="is_insurance_shipped"  class="is_insurance_shipped" '.($offer['status_ship']=='1' ? 'checked disabled':'').' value="1"><br>'."InsuranceID:".$offer['insurance_id'];
									}
									else {
										echo '--';
									}
								?>
							</td>
						</tr>
<?php
				}
?>
                </tbody>
        </table>
        <div align="right" class="log_pagination">
            <span class="pagination_snip"><?php echo $offers_pagination; ?></span>
        </div>
        <br>
        <div align="right">
            <?php
            if($this->erpm->auth(FINANCE_ROLE,TRUE) )
            {
?>
                <button type="button" class="button button-action process_status" onclick="process_offer(this)" >Process</button>
<?php 
            }
?>
        </div>
<?php
        }
?>
        <script>
            $('.date_from,.date_to',$("#insurance_offers")).datepicker({changeMonth: true,changeYear: true,dateFormat:'yy-mm-dd'});
			$('.territory_filter').chosen();$('.town_filter').chosen();$('.franchise_filter').chosen();
        </script>
</div>
<?php
}
?>

<!-- ====================================< RECHARGE BLOCK >==================================== -->
<?php
if($type=='recharge_offers')
{
?>
    <div id="recharge_offers">
				<div class="filters_block">
<?php 
					if(isset($recharge_territory_arr))
						$terr_list_rech=$this->db->query("select * from pnh_m_territory_info where id in ($recharge_territory_arr) order by territory_name asc");
					else 
						$terr_list_rech=$this->db->query("select * from pnh_m_territory_info order by territory_name asc");
					
					if(isset($recharge_town_arr))
					   $town_list_rech=$this->db->query("select * from pnh_towns where id in ($recharge_town_arr) order by town_name asc");
					else
					   $town_list_rech=$this->db->query("select * from pnh_towns order by town_name asc");
					
					if(isset($recharge_franchisee_arr))
								$fran_list_rech=$this->db->query("select * from pnh_m_franchise_info where franchise_id in ($recharge_franchisee_arr) order by franchise_name asc");
							else
								$fran_list_rech=$this->db->query("select * from pnh_m_franchise_info order by franchise_name asc");
?>
						<div class="filter">
							<label style="margin-right: 10px;">Territory : </label>
							<select class="territory_filter">
								<option value="0" <?=('0' == $territory_id)? 'selected': "";?> >All</option>
								<?php foreach($terr_list_rech->result_array() as $t) { ?>
									<option value="<?=$t['id'];?>" <?=($t['id'] == $territory_id)? 'selected': "";?> ><?=$t['territory_name'];?></option>
								<?php } ?>
							</select>
							<br>
							<label style="margin-right: 29px;">Town :</label>
							<select class="town_filter">
									<option value="0" <?=('0' == $town_id)? 'selected': "";?> >All</option>
									<?php foreach($town_list_rech->result_array() as $t) { ?>
										<option value="<?=$t['id'];?>" <?=($t['id'] == $town_id)? 'selected': "";?> ><?=$t['town_name'];?></option>
									<?php } ?>
							</select>
							<br>
							<label>Franchise :</label>
							<select class="franchise_filter">
								<option value="0" <?=('0' == $franchise_id)? 'selected': "";?> >All</option>
								<?php foreach($fran_list_rech->result_array() as $f) { ?>
									<option value="<?=$f['franchise_id'];?>" <?=($f['franchise_id'] == $franchise_id)? 'selected': "";?> ><?=$f['franchise_name'];?></option>
								<?php } ?>
							</select>
						</div>
						<div class="filter">
							<label style="margin-right: 18px;">Order Status :</label>
							<select class="order_status_filter">
								<option value="all" <?=$order_status == 'all' ? 'selected':''; ?>>All</option>
								<option value="1" <?=$order_status == '1' ? 'selected':''; ?>>Delivered</option>
								<option value="0" <?=$order_status == '0' ? 'selected':''; ?>>Not Delivered</option>
							</select>
							<br>
							<label style="margin-right: 12px;">Details Status :</label>
							<select class="detail_status_filter">
								<option value="all" <?=$detail_status == 'all' ? 'selected':''; ?>>All</option>
								<option value="1" <?=$detail_status == '1' ? 'selected':''; ?>>Updated</option>
								<option value="0" <?=$detail_status == '0' ? 'selected':''; ?>>Not Updated</option>
							</select>
							<br>
							<label>Payment Status :</label>
							<select class="payment_status_filter">
								<option value="all" <?=$payment_status == 'all' ? 'selected':''; ?>>All</option>
								<option value="0" <?=$payment_status == '0' ? 'selected':''; ?>>Paid</option>
								<option value="1" <?=$payment_status == '1' ? 'selected':''; ?>>Pending</option>
							</select>
						</div>
						<div class="filter">
							<label>Feedback Status :</label>
							<select class="feedback_status_filter">
								<option value="all" <?=$feedback_status == 'all' ? 'selected':''; ?>>All</option>
								<option value="1" <?=$feedback_status == '1' ? 'selected':''; ?>>Received</option>
								<option value="0" <?=$feedback_status == '0' ? 'selected':''; ?>>Pending</option>
							</select>
							<br>
							<label style="margin-right: 31px;">Offer Status :</label>
							<select class="offer_status_filter">
								<option value="all" <?=$offer_status == 'all' ? 'selected':''; ?>>All</option>
								<option value="1" <?=$offer_status == '1' ? 'selected':''; ?>>Details Not Updated</option>
								<option value="2" <?=$offer_status == '2' ? 'selected':''; ?>>Feedback Pending</option>
								<option value="3" <?=$offer_status == '3' ? 'selected':''; ?>>Ready to Recharge</option>
								<option value="4" <?=$offer_status == '4' ? 'selected':''; ?>>Recharged</option>
								<option value="5" <?=$offer_status == '5' ? 'selected':''; ?>>Order Canceled</option>
							</select>
							<br>
							<?php
							if(!isset($no_results_found))
							{
	?>
								<div  class="export_block">
									<span class="export_lnk button button-tiny" href="">Export to CSV File</span>
								</div>
							<?php } ?>
						</div>
						<div class="filter">
							<form style="margin-top:5px;">
								<label>Order Date :</label><input type="text" class="date_from" size="11" value="<?=($date_from =='0')?'':$date_from; ?>" placeholder="YYYY-MM-DD">
								- <input type="text" class="date_to" size="11" value="<?=($date_to=='0')?'':$date_to; ?>" placeholder="YYYY-MM-DD">
								<button type="button" class="button button-tiny date_filter_submit" style="color:#000000 !important">Go</button>
							</form>
						</div>
				</div>
                
                <div class="clear">&nbsp;</div>
<?php
                if(isset($no_results_found))
                {
?>
                    <p>No Results found.</p>
<?php
                }
                else
                {
?>
                    <div align="right"  class="log_pagination fl_right">
                        <span class="pagination_snip"><?php echo $offers_pagination; ?></span>
                    </div>
                    <div class="block_notify">Total <?=$ttl_rows;?> items found. Showing <?=$pg_from;?> to <?=$pg_limit;?></div>
                    
                    
                    <div class="clear">&nbsp;</div>
                    <table class="datagrid smallheader noprint datagridsort" width="100%">
                        <thead>
                            <tr>
                                <th width="1%">#</th>
                                <th width="2%">OfferID</th>
                                <th width="4%">Order Date</th>
                                <th width="2%">Member ID</th>
                                <th width="6%">Member Name</th>
                                <th width="2%">Mobile No</th>
                                <th width="8%">Franchise Name</th>
                                <th width="3%">TransID</th>
                                <th width="4%">Offer Towards</th>
                                <th width="3%">Recharge Amount</th>
                                <th width="3%">Network</th>
                                <th width="4%">Member Details</th>
                                <th width="3%">Order Status</th>
                                <th width="2%">Payment</th>
                                <th width="4%">Feedback</th>
                                <th width="4%">Actions</th><!--<input type="checkbox" name="chk_all_recharges" id="chk_all_recharges" class="chk_all_recharges"/>-->
                            </tr>	
                        </thead>
                        <tbody>
                        <?php  foreach($offers_talktime as $i=>$offer) { ?>
                         	<tr class="recharge_table" sno="<?=$offer['sno'];?>" territory_id="<?=$offer['territory_id'];?>" date="<?=$offer['date'];?>" town_id="<?=$offer['town_id'];?>"  member_id="<?=$offer['member_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
                                <td><?=(++$i)+$pg?></td>
								<td><?="".$offer['sno'];?></td>
                                <td><?=format_datetime($offer['created_on']);?></td>
                                <td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id'])?>" target="_blank"><?=$offer['member_id'];?></a></td>
                                <td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id'])?>" target="_blank"><?=$offer['first_name'];?></a></td>
                                <td><?=$offer['mobile'];?></td>
                                <td><a href="<?=site_url("admin/pnh_franchise/".$offer['franchise_id']);?>" target="_blank"><?=$offer['franchise_name'];?></a></td>
                                <td>
							    <?php
							    if($offer['order_for'] == '2') { 
								echo '<span class="payment_pending" style="color:#7db500;">Key</span> ';
							    } ?>
							    <a href="<?=site_url("admin/trans/".$offer['transid_ref'])?>" target="_blank"><?=$offer['transid_ref'];?></a></td>
                                <td>Rs. <?=$offer['offer_towards'];?></td>
                                <td><?= ( $offer['offer_value'] == '0' ) ? 'Free' : "Rs. ".formatInIndianStyle($offer['offer_value']);?></td>
                                <td><?=$offer['mobile_network'];?></td>
                                <td><?php
                                    if($offer['details_updated'] == '0'  )
                                    { 
                                        echo '&nbsp; &nbsp;<a class="fl_right button button-tiny button-flat-caution" href="'.site_url("admin/pnh_editmember/".$offer['member_id']).'" target="_blank" >Update Member</a>';
                                     }
                                    else {
                                        echo '&nbsp; &nbsp;<a class="fl_right button button-tiny button-flat-action" href="'.site_url("admin/pnh_editmember/".$offer['member_id']).'" target="_blank" >View Member</a>';
                                    }
?>
                                </td>
								<td><?php
										if( $offer['delivery_status'] == '0' ) {
											echo '<span class="payment_pending">Not delivered</span>';
										}
										else
										{
											echo '<span class="payment_pending" style="color:#7db500;">Delivered</span>';
										}
?>
								</td>
                                <td>
<?php
									if( $offer['pending_payment'] == "1" ) {
										echo '<span class="payment_pending">Pending</span>';
									}
									else
									{
										echo '<span class="payment_pending" style="color:#7db500;">Paid</span>';
									}
?>
                                </td>
								<td>
<?php                              
                                    if($offer['feedback_status'] == '0' && $offer['process_status'] == '0')
                                    {
                                            echo '<span onclick="resend_mem_feedback('.$offer['user_id'].',\''.$offer['franchise_id'].'\')" class="button button-tiny button-flat-action">Resend</span> <div class="clear" style="margin-top:10px;"></div>';
											echo '<span style="color:orange;font-weight:bold;font-size:13px"><a class="button button-tiny button-primary" onclick="confirm_feedback(\''.$offer['member_id'].'\');">Confirm</a></span>';
                                    }
									else
									{
										echo '<span>'.$offer['feedback_value'].'</span>';
									}
?>
                                </td>
                                <td>
                                    <?php 
                                        if( $offer['order_pending'] == '1' )
                                        {

                                            echo '<span style="background:#cd0000;color:white;padding:3px 5px;">Order Canceled</span>';
                                            
                                        }
                                        else
                                        {
                                                if( $offer['delivery_status'] == "0"
                                                        && $offer['process_status'] == "0") {

													echo '<span class="delivery_pending">Not delivered</span>';
                                                }
                                                else
                                                {
                                                    if($offer['details_updated'] == '0'
                                                            && $offer['process_status'] == "0") { //($have_insu_details != 0)

                                                        echo '<span class="details_pending">Details Not Updated</span>';
                                                    }
                                                    else
                                                    {
                                                            if( $offer['feedback_status'] == "0"
                                                                    && $offer['delivery_status'] == "1"
                                                                    && $offer['details_updated'] == '1'
                                                                    && $offer['process_status'] == "0" ) {

                                                                    echo '<span  class="details_pending">Feedback Pending</span>';
                                                            }
                                                            else
                                                            {
                                                                    if( $offer['pending_payment'] == "1"
                                                                            && $offer['details_updated'] == '1'
                                                                            && $offer['delivery_status'] == "1" 
                                                                            && $offer['feedback_status'] == "1"
                                                                            && $offer['process_status'] == "0" ) {

                                                                        echo '<span class="payment_pending">Pending Payment</span>';
                                                                    }
                                                                    else
                                                                    {
                                                                            //if($offer['feedback_status'] == 1 && $offer['delivery_status'] == 1 && $offer['process_status'] == 0)
                                                                            if( $offer['process_status'] == "0"
                                                                                    && $offer['pending_payment'] == "0"
                                                                                    && $offer['details_updated'] == '1'
                                                                                    && $offer['delivery_status'] == "1" 
                                                                                    && $offer['feedback_status'] == "1" ) {
                    ?>
                                                                                <a href="javascript:void(0)" onclick="mark_as_recharged(this)" class="button button-info button-flat-action button-tiny">Mark as Recharged</a>
                    <?php                                                               
                                                                            }
                                                                            else
                                                                            {
                                                                                if($offer['process_status'] == "1" ) {
                                                                                    echo "<span style='color:green;font-weight:bold;font-size:13px'>". ( ($offer['remarks']!= '') ? $offer['remarks'] : 'Recharged')."</span>";
                                                                                }
                                                                                else
                                                                                    echo 'Incomplete Data';
                                                                            }
                                                                    }
                                                            }
                                                    }
												}

                                        }

                                        
                                     ?>
                                    </td>
                         	</tr>
                            <?php } ?>
                        </tbody>
                    </table>
	             
                                        
                    <div align="right" class="log_pagination">
                        <span class="pagination_snip"><?php echo $offers_pagination; ?></span>
                    </div>
<?php /*            <br>
					<div align="right" class="hide">
                        if($this->erpm->auth(FINANCE_ROLE,TRUE) ) { echo 'button type="button" class="button button-action process_status" onclick="process_recharge_offer(this)" >Process</button>'; } ?>
                    </div>*/?>
		</div>
<?php   }
?>
                <script>
                    $('.date_from,.date_to').datepicker({
                        changeMonth: true
                        ,changeYear: true
                        ,dateFormat:'yy-mm-dd'});
					$('.territory_filter').chosen();$('.town_filter').chosen();$('.franchise_filter').chosen();
                </script>
<?php
}
?>

<!-- ====================================< MEMBER FEE BLOCK  >==================================== -->
<?php
if($type=='fee_list')
{
?>
            <div id="fee_list">
                 <div class="filters_block">
						<?php 
						if(isset($fee_territory_arr))
							$terr_list=$this->db->query("select * from pnh_m_territory_info where id in ($fee_territory_arr) order by territory_name asc");
						else 
							$terr_list=$this->db->query("select * from pnh_m_territory_info order by territory_name asc");

						if(isset($fee_town_arr))
							$town_list=$this->db->query("select * from pnh_towns where id in ($fee_town_arr) order by town_name asc");
						 else
							$town_list=$this->db->query("select * from pnh_towns order by town_name asc");

						if(isset($fee_franchisee_arr))
							$fran_list=$this->db->query("select * from pnh_m_franchise_info where franchise_id in ($fee_franchisee_arr) order by franchise_name asc");
						else
							$fran_list=$this->db->query("select * from pnh_m_franchise_info order by franchise_name asc");

?>
						<div class="filter">
							<label style="margin-right: 10px;">Territory : </label>
							<select class="territory_filter">
								<option value="0" <?=('0' == $territory_id)? 'selected': "";?> >All</option>
								<?php foreach($terr_list->result_array() as $t) { ?>
									<option value="<?=$t['id'];?>" <?=($t['id'] == $territory_id)? 'selected': "";?> ><?=$t['territory_name'];?></option>
								<?php } ?>
							</select>
							<br/>
							<label style="margin-right: 29px;">Town :</label>
							<select class="town_filter">
								<option value="0" <?=('0' == $town_id)? 'selected': "";?>>All</option>
								<?php foreach($town_list->result_array() as $t) { ?>
									<option value="<?=$t['id'];?>" <?=($t['id'] == $town_id)? 'selected': "";?> ><?=$t['town_name'];?></option>
								<?php } ?>
							</select>
							<br/>
							<label>Franchise :</label>
							<select class="franchise_filter">
								<option value="0" <?=('0' == $franchise_id)? 'selected': "";?> >All</option>
								<?php foreach($fran_list->result_array() as $f) { ?>
									<option value="<?=$f['franchise_id'];?>" <?=($f['franchise_id'] == $franchise_id)? 'selected': "";?> ><?=$f['franchise_name'];?></option>
								<?php } ?>
							</select>
						</div>
					 
						<div class="filter">
							<label>Payment Status :</label>
							<select class="payment_status_filter">
								<option value="all" <?=$payment_status == 'all' ? 'selected':''; ?>>All</option>
								<option value="1" <?=$payment_status == '1' ? 'selected':''; ?>>Pending</option>
								<option value="0" <?=$payment_status == '0' ? 'selected':''; ?>>Paid</option>
							</select>
							<br>
							<label style="margin-right:18px;">Order Status :</label>
                            <select class="order_status_filter">
                                <option value="all" <?=$order_status == 'all' ? 'selected':''; ?>>All</option>
                                <option value="1" <?=$order_status == '1' ? 'selected':''; ?>>Delivered</option>
                                <option value="0" <?=$order_status == '0' ? 'selected':''; ?>>Not Delivered</option>
                            </select>
						</div>
                        <div class="filter">
                            <form style="margin-top:5px;">
                                    <label>Order Date :</label>
									<input type="text" class="date_from" size="11" value="<?=($date_from =='0')?'':$date_from; ?>" placeholder="YYYY-MM-DD">
                                    <input type="text" class="date_to" size="11" value="<?=($date_to=='0')?'':$date_to; ?>" placeholder="YYYY-MM-DD">
                                    <button type="button" class="button button-tiny date_filter_submit" style="color:#000000 !important;">Go</button>
                            </form>
                        </div>
                </div>
                <div class="clear">&nbsp;</div>
<?php
			if(isset($no_results_found))
			{
?>
				<p>No Results found.</p>
<?php
			}
			else
			{
?>

                <div align="right"  class="log_pagination fl_right">
                    <span class="pagination_snip"><?php echo $offers_pagination; ?></span>
                </div>
                <div class="block_notify">Total <?=$ttl_rows;?> items found. Showing <?=$pg_from;?> to <?=$pg_limit;?></div>
                
                <div class="clear">&nbsp;</div>
                
                <table class="datagrid smallheader noprint datagridsort" width="100%">
                        <thead>
                            <tr>
									<th width="1%">#</th>
									<th width="4%">Registered Date</th>
									<th width="6%">Member Name</th>
									<th width="10%">Franchise_name</th>
									<th width="4%">TransID</th>
									<th width="4%">Offer towards</th>
									<th width="4%">Invoices</th>
									<th width="4%">Payment Status</th>
									<th width="4%">Member Fee</th>
									<th width="4%">Status</th>
                           </tr>	
                        </thead>

                        <tbody>
                                <?php foreach($member_fee_list as $i=>$offer){ ?>
                                <tr class="fee_table" sno="<?=$offer['sno'];?>"  member_id="<?=$offer['member_id'];?>" date="<?=$offer['date'];?>" territory_id="<?=$offer['territory_id'];?>" town_id="<?=$offer['town_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
                                        <td><?=(++$i)+$pg?></td>
                                        <td><?=format_datetime($offer['created_on']);?></td>
                                        <td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id']);?>" target="_blank"><?=$offer['first_name'];?></a></td>
                                        <td><a href="<?=site_url("admin/pnh_franchise/".$offer['franchise_id']);?>" target="_blank"><?=$offer['franchise_name'];?></a></td>
                                        <td><a href="<?=site_url("admin/trans/".$offer['transid_ref'])?>" target="_blank"><?=$offer['transid_ref'];?></a></td>
                                        <td>Rs. <?=$offer['offer_towards'];?></td>
                                        <td><?php
										$arr_invs = explode(",",$offer['invoice_nos']);
										foreach($arr_invs as $invoice_no)
										{
											echo '<a href='.site_url("admin/invoice/{$invoice_no}").' class="payment_pending" style="color:#7db500;" target="_blank">'.$invoice_no.'</span>';
										}
										?></td>
                                        <td><?php
										if( $offer['pending_payment'] == "1" ) {
											echo '<span class="payment_pending">Pending</span>';
										}
										else
										{
											echo '<span class="payment_pending" style="color:#7db500;">Paid</span>';
										}
										?>
										</td>
                                        <td><?="Rs. ".$offer['pnh_member_fee'];?></td>
                                        <td><?php
                                            $arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");

											if( $offer['delivery_status'] == "0" ) {
												echo '<span class="delivery_pending">Not Delivered</span>';
											}
											else {
												echo '<span class="payment_pending" style="color:#7db500;">Delivered</span>';
											}

                                        ?>
                                        </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                </table>
    
                <br>        
                <div align="right"  class="log_pagination fl_right">
                    <span class="pagination_snip"><?php echo $offers_pagination; ?></span>
                </div>
<?php
			}
?>
		<br>
		<script>
				$('.date_from,.date_to').datepicker({changeMonth: true,changeYear: true,dateFormat:'yy-mm-dd'});
				$('.territory_filter').chosen();$('.town_filter').chosen();$('.franchise_filter').chosen();
		</script>
</div>
<?php
}
?>
