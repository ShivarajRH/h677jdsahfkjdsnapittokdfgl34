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
    if(isset($no_results_found))
    {
?>
        <p>No Results found.</p>
<?php
    }
    else
    {
?>
<div class="clear">&nbsp;</div>
        <?php if(count($insu_franchisee_arr) != 0) { ?>
        <div class="filters_block">
                <?php $terr_list=$this->db->query("select * from pnh_m_territory_info where id in ($insu_territory_arr) order by territory_name asc"); ?>
                        <div class="filter">
                <b>Territory : </b>
                <select class="insu_territory_filter">
                    <option value="0">All</option>
                    <?php foreach($terr_list->result_array() as $t) { ?>
                        <option value="<?=$t['id'];?>"><?=$t['territory_name'];?></option>
                    <?php } ?>
                </select>
            </div>

    <?php $town_list=$this->db->query("select * from pnh_towns where id in ($insu_town_arr) order by town_name asc"); ?>
        <div class="filter">
                <b>Town :</b>
                <select class="insu_town_filter">
                    <option value="0">All</option>
                    <?php foreach($town_list->result_array() as $t) { ?>
                        <option value="<?=$t['id'];?>"><?=$t['town_name'];?></option>
                    <?php } ?>
                </select>
        </div>

                <?php $fran_list=$this->db->query("select * from pnh_m_franchise_info where franchise_id in ($insu_franchisee_arr) order by franchise_name asc"); ?>
                        <div class="filter">
                <b>Franchise :</b>
                <select class="insu_franchise_filter">
                    <option value="0">All</option>
                    <?php foreach($fran_list->result_array() as $f) { ?>
                        <option value="<?=$f['franchise_id'];?>"><?=$f['franchise_name'];?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="filter">
                <form style="margin-top:5px;">
                        <b>From :</b><input type="date" id="insu_frm_date">
                        <b>To :</b><input type="date" id="insu_to_date">
                        <button type="button" class="button button-tiny insu_date_filter">Go</button>
                </form>
            </div>
            <div class="filter">
                <b>Order Status :</b>
                <select class="order_status_filter">
                    <option value="all" <?=$order_status == 'all' ? 'selected':''; ?>>All</option>
                    <option value="1" <?=$order_status == '1' ? 'selected':''; ?>>Delivered</option>
                    <option value="0" <?=$order_status == '0' ? 'selected':''; ?>>Not Delivered</option>
                </select>
            </div>
            <div class="filter">
                <b>Offer Status :</b>
                <select class="offer_status_filter">
                    <option value="all" <?=$offer_status == 'all' ? 'selected':''; ?>>All</option>
                    <option value="1" <?=$offer_status == '1' ? 'selected':''; ?>>Processed</option>
                    <option value="0" <?=$offer_status == '0' ? 'selected':''; ?>>Pending</option>
                </select>
            </div>
            
</div>
        
        <div class="clear">&nbsp;</div>

        <div align="right"  class="log_pagination fl_right">
            <span class="pagination_snip"><?php echo $offers_pagination; ?></span>
        </div>
        <div class="block_notify">Total <?=$ttl_rows;?> items found. Showing <?=$pg_from;?> to <?=$pg_limit;?></div>
        <div class="clear">&nbsp;</div>

        <table class="datagrid smallheader noprint datagridsort" width="100%">
                <thead>
                        <tr>
                                <th width="1%">Sl No.</th>
                                <th width="4%">Registered Date</th>
                                <th width="6%">Member Name</th>
                                <th width="10%">Franchise Name</th>
                                <th width="10%">Deal Details</th>
                                <th width="4%">Offer towards</th>
                                <th width="4%">Insurance Amount</th>
                                <th width="4%">Invoice Number</th>
                                <th width="4%">Status</th>
                                <th width="4%">Actions <br><label for="chk_all_insurances">Check All</label><input type="checkbox" name="chk_all_insurances" id="chk_all_insurances" class="chk_all_insurances"/></th>									
                        </tr>	
                </thead>
                <tbody>
                        <?php foreach($offers_insurance as $i=>$offer) { 
                            $pnh_id = $offer['pnh_pid'];
                            $dealname = $offer['dealname'];
                            ?>
                        <tr class="insurance_table"  member_id="<?=$offer['member_id'];?>" date="<?=$offer['date'];?>" territory_id="<?=$offer['territory_id'];?>" town_id="<?=$offer['town_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
                                <td><?=(++$i)+$pg?></td>
                                <td><?=format_datetime($offer['created_on']);?></td>
                                <td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id']);?>" target="_blank"><?=$offer['first_name'];?></a></td>
                                <td><a href="<?=site_url("admin/pnh_franchise/".$offer['franchise_id']);?>" target="_blank"><?=$offer['franchise_name'];?></a></td>
                                <td>
                                        <a href="<?=site_url("admin/trans/".$offer['transid_ref'])?>" target="_blank"><?=$offer['transid_ref'];?></a>
                                            &nbsp;-&nbsp; <a href="<?=site_url("admin/pnh_deal/".$pnh_id)?>" target="_blank"><?=$dealname;?></a>
                                            <br>
                                            <div style="font-size:11px;background:#fcfcfc;padding:5px;width:205px;text-align:center">
                                                <?php if($offer['imei_no'] ) {?> 
                                                    <b>IMEI : <?=  $offer['imei_no'];?></b>

                                                    <?php }else {?>
                                                     <b>Non SK IMEI : <?=$offer['nonsk_imei_no']; ?></b>
                                                     <?php }?>
                                            </div>
                                </td>
                                <td>Rs. <?=$offer['offer_towards'];?></td>
                                <td><?php echo ( $offer['offer_value'] == 0 ) ? 'Free' : "Rs. ".formatInIndianStyle($offer['offer_value'])."&nbsp;&nbsp;&nbsp;";
                                

                                    if($offer['process_status'] == '1' and $offer['order_status'] != '3' )
                                    {
?>
                                           <a href="<?=site_url("admin/insurance_print_view/".$offer['insurance_id']);?>" target="blank" class="view_insurance_lnk button button-tiny button-primary">View</a>
<?php 
                                    }
?>
                                </td>
                                <td><a href="<?=site_url()."admin/invoice/".$offer['invoice_no'];?>" target="_blank"><?=$offer['invoice_no'];?></a></td>
                                <td><?php
                                        $arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
                                        $arr_delivery_status = array(0=>"Not delivered",1=>"Order Delivered");
                                            echo $arr_delivery_status[$offer['delivery_status']];
                                    ?>
                                 </td>
                                 <td>
                                        <?php
                                        $have_insu_details = $this->db->query("SELECT COUNT(*) AS t FROM pnh_member_insurance WHERE ( 
											proof_id= '' OR proof_type='' OR proof_address='' OR first_name='' OR mob_no='' OR city='' OR mem_receipt_no='' OR mem_receipt_date='' OR mem_receipt_amount='' OR 
											proof_id is null OR proof_type is null OR proof_address is null OR first_name is null OR mob_no is null OR city is null OR mem_receipt_no is null OR mem_receipt_date is null OR mem_receipt_amount is null
											) AND sno= ? ",$offer['insurance_id'])->row()->t;

                                        $is_registered_member = $this->db->query("SELECT COUNT(*) AS t FROM pnh_member_info WHERE ( mobile='' ) AND pnh_member_id=? ",$offer['member_id'])->row()->t;
                                        
                                        $is_invoice_reconciled = $this->db->query("SELECT count(*) as t FROM `pnh_t_receipt_reconcilation` rcon
                                                                                    JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id
                                                                                    WHERE rlog.is_reversed = 0 AND rcon.is_invoice_cancelled = 0 AND rcon.unreconciled = 0 and rcon.invoice_no= ? ",$offer['invoice_no'])->row()->t;
                                        
                                        if($is_registered_member != 0)
                                        {
                                            //echo '<span style="color:orange;font-weight:bold;font-size:13px"><a class="button button-tiny button-caution" onclick="update_member_details(\''.$offer['member_id'].'\');">Update Member Details</a></span>';
                                            echo '<span style="color:orange;font-weight:bold;font-size:13px"><a class="button button-tiny button-caution" href="'.site_url().'/admin/pnh_activation/#member_reg" target="_blank">Register Member Details</a></span>';
                                        }
                                        elseif($have_insu_details != 0)
                                        {
                                            echo '<span style="color:orange;font-weight:bold;font-size:13px"><a class="button button-tiny button-action" onclick="update_member_insurance_details(\''.$offer['insurance_id'].'\');">Update Insurance Details</a></span>';
                                        }
                                        elseif( $offer['delivery_status'] == 1 &&  ( $is_invoice_reconciled == 0 ) )
                                        {
                                            echo '<span class="payment_pending">Pending Payment</span>';
                                        }
                                        else
                                        {
                                                if($offer['order_status'] == '3')
                                                {
?>
                                                    <span style="background:#cd0000;color:white;padding:3px 5px;">Order Canceled</span>
<?php
                                                }
                                                else
                                                {
                                                        if($offer['feedback_status'] == 1 && $offer['delivery_status'] == 1 && $offer['process_status'] == 0)
                                                        {
?>
                                                            <input type="checkbox" name="chk_insurance" class="chk_insurance" value="1"> Process
<?php                                                               
                                                        }
                                                        else if($offer['process_status'] == "1")
                                                        {
                                                                echo "<span style='color:green;font-weight:bold;font-size:13px'>Processed</span>";
                                                        }
                                                        else if($offer['process_status'] == "0" && $offer['feedback_status'] == "0" && $offer['delivery_status'] == "1")
                                                        {
                                                                echo '<span style="color:orange;font-weight:bold;font-size:13px"><a class="button button-tiny button-primary" onclick="confirm_feedback(\''.$offer['member_id'].'\');">Confirm Feedback</a></span>';
                                                        }
                                                        else
                                                            echo '--';

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
        <br>
        
        <div align="right">
            <?php //if($offer['delivery_status']==0){ ?>
                <button type="button" class="button button-action process_status" onclick="process_offer(this)" >Process</button>
            <?php //} else echo '--'; ?>
        </div>
        <script>
            var insu_fids=[];
            insu_fids='<?php echo $insu_franchisee_arr; ?>';
        </script>

        <?php }else { ?>
                <b>No Insurance Offers Found</b>
        <?php } 
    } 
}
?>

<div class="clear">&nbsp;</div>
<!-- ====================================< RECHARGE BLOCK >==================================== -->
<?php
if($type=='recharge_offers')
{
    if(isset($no_results_found))
    {
?>
        <p>No Results found.</p>
<?php
    }
    else
    {
?>
    <div id="recharge_offers">
  
  	          <?php	if( count($recharge_franchisee_arr) != 0) {?>	
            	<div class="filters_block">
            		<?php $terr_list_rech=$this->db->query("select * from pnh_m_territory_info where id in ($recharge_territory_arr) order by territory_name asc"); ?>
			            <div class="filter">
			                <b>Territory : </b>
			                <select class="recharge_territory_filter">
			                    <option value="0">All</option>
			                    <?php foreach($terr_list_rech->result_array() as $t) { ?>
			                    	<option value="<?=$t['id'];?>"><?=$t['territory_name'];?></option>
			                    <?php } ?>
			                </select>
			            </div>
		            
		             <?php $town_list_rech=$this->db->query("select * from pnh_towns where id in ($recharge_town_arr) order by town_name asc"); ?>
		            	<div class="filter">
		                	<b>Town :</b>
		                	<select class="recharge_town_filter">
                                                <option value="0">All</option>
                                                    <?php foreach($town_list_rech->result_array() as $t) { ?>
                                                        <option value="<?=$t['id'];?>"><?=$t['town_name'];?></option>
                                                    <?php } ?>
                                        </select>
                                </div>
		            
					<?php $fran_list_rech=$this->db->query("select * from pnh_m_franchise_info where franchise_id in ($recharge_franchisee_arr) order by franchise_name asc"); ?>
 					 	<div class="filter">
			                <b>Franchise :</b>
			                <select class="recharge_franchise_filter">
			                	<option value="0">All</option>
			                    <?php foreach($fran_list_rech->result_array() as $f) { ?>
			                    	<option value="<?=$f['franchise_id'];?>"><?=$f['franchise_name'];?></option>
			                    <?php } ?>
			                </select>
			            </div>
		            
		            <div class="filter">
		            	<form style="margin-top:5px;">
			                <b>From :</b><input type="date" id="rech_frm_date">
			                <b>To :</b><input type="date" id="rech_to_date">
			                <button type="button" class="button button-tiny rech_date_filter">Go</button>
		                </form>
		            </div>
                            
                        <div class="filter">
                                <b>Order Status :</b>
                                <select class="order_status_filter">
                                    <option value="all" <?=$order_status == 'all' ? 'selected':''; ?>>All</option>
                                    <option value="1" <?=$order_status == '1' ? 'selected':''; ?>>Delivered</option>
                                    <option value="0" <?=$order_status == '0' ? 'selected':''; ?>>Not Delivered</option>
                                </select>
                            </div>
                            <div class="filter">
                                <b>Offer Status :</b>
                                <select class="offer_status_filter">
                                    <option value="all" <?=$offer_status == 'all' ? 'selected':''; ?>>All</option>
                                    <option value="1" <?=$offer_status == '1' ? 'selected':''; ?>>Processed</option>
                                    <option value="0" <?=$offer_status == '0' ? 'selected':''; ?>>Pending</option>
                                </select>
                            </div>

		        </div>
                
                <div class="clear">&nbsp;</div>
                <div align="right"  class="log_pagination fl_right">
                    <span class="pagination_snip"><?php echo $offers_pagination; ?></span>
                </div>
                <div class="block_notify">Total <?=$ttl_rows;?> items found. Showing <?=$pg_from;?> to <?=$pg_limit;?></div>
                
                <div class="clear">&nbsp;</div>
                
                <table class="datagrid smallheader noprint datagridsort" width="100%">
                        <thead>
                            <tr>
                                <th width="1%">Sl No.</th>
                                <th width="4%">Registered Date</th>
                                <th width="6%">Member Name</th>
                                <th width="10%">Franchise Name</th>
                                <th width="4%">TransID</th>
                                <th width="4%">Offer Towards</th>
                                <th width="4%">Recharge Amount</th>
                                <th width="4%">Invoice</th>
                                <th width="4%">Status</th>
                                    <th width="4%">Actions<label for="chk_all_recharges">Check All</label><input type="checkbox" name="chk_all_recharges" id="chk_all_recharges" class="chk_all_recharges"/></th>									
                            </tr>	
                        </thead>
                        
                        <tbody>
                        <?php  foreach($offers_talktime as $i=>$offer){ ?>
                         	<tr class="recharge_table" territory_id="<?=$offer['territory_id'];?>" date="<?=$offer['date'];?>" town_id="<?=$offer['town_id'];?>"  member_id="<?=$offer['member_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
                                <td><?=(++$i)+$pg?></td>
                                <td><?=format_datetime($offer['created_on']);?></td>
                                <td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id'])?>" target="_blank"><?=$offer['first_name'];?></a></td>
                                                        <td><a href="<?=site_url("admin/pnh_franchise/".$offer['franchise_id']);?>" target="_blank"><?=$offer['franchise_name'];?></a></td>
                                <td><a href="<?=site_url("admin/trans/".$offer['transid_ref'])?>" target="_blank"><?=$offer['transid_ref'];?></a></td>
                                <td>Rs. <?=$offer['offer_towards'];?></td>
                                <td><?= ( $offer['offer_value'] == 0 ) ? 'Free' : "Rs. ".formatInIndianStyle($offer['offer_value']);?></td>
                                <td><a href="<?=site_url()."admin/invoice/".$offer['invoice_no'];?>" target="_blank"><?=$offer['invoice_no'];?></a></td>
                                <td><?php
                                                            //$arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
	                                    $arr_delivery_status = array(0=>"Not delivered",1=>"Order Delivered");
										echo $arr_delivery_status[$offer['delivery_status']];
									?>
								</td>
                                <td>
                                    <?php 
                                        $is_registered_member = $this->db->query("SELECT COUNT(*) AS t FROM pnh_member_info WHERE ( mobile='' ) AND pnh_member_id=? ",$offer['member_id'])->row()->t;
                                        
                                        $is_invoice_reconciled = $this->db->query("SELECT count(*) AS t FROM `pnh_t_receipt_reconcilation` rcon
                                                                                    JOIN pnh_t_receipt_reconcilation_log rlog ON rlog.reconcile_id = rcon.id
                                                                                    WHERE rlog.is_reversed = 0 AND rcon.is_invoice_cancelled = 0 AND rcon.unreconciled = 0 and rcon.invoice_no= ? ",$offer['invoice_no'])->row()->t;
                                        
                                        if($is_registered_member != 0)
                                        {
                                            //echo '<span style="color:orange;font-weight:bold;font-size:13px"><a class="button button-tiny button-caution" onclick="update_member_details(\''.$offer['member_id'].'\');">Update Member Details</a></span>';
                                            echo '<span style="color:orange;font-weight:bold;font-size:13px"><a class="button button-tiny button-caution" href="'.site_url().'/admin/pnh_activation/#member_reg" target="_blank">Register Member Details</a></span>';
                                        }
                                        elseif( $offer['delivery_status'] == 1 &&  ( $is_invoice_reconciled == 0 ) )
                                        {
                                            echo '<span class="payment_pending">Pending Payment</span>';
                                        }
                                        else
                                        {
                                                if($offer['order_status'] == '3')
                                                {
?>
                                                    <span style="background:#cd0000;color:white;padding:3px 5px;">Order Canceled</span>
<?php
                                                }
                                                else
                                                {
                                                        if($offer['feedback_status'] == 1 && $offer['delivery_status'] == 1 && $offer['process_status'] == 0) { ?>
                                                             <input type="checkbox" name="chk_recharge" class="chk_recharge" value="1"> Process
                                                        <?php }
                                                        else if($offer['process_status'] == "1")
                                                        {
                                                                echo "<span style='color:green;font-weight:bold;font-size:13px'>Processed</span>";
                                                        }
                                                        else if($offer['process_status'] == "0" && $offer['feedback_status'] == "0" && $offer['delivery_status'] == "1"){
                                                                echo '<span style="color:orange;font-weight:bold;font-size:13px"><a class="button button-tiny button-primary" onclick="confirm_feedback(\''.$offer['member_id'].'\');">Confirm Feedback</a></span>';
                                                        }
                                                        else if($offer['offer_type'] == "3" && $offer['mem_fee_applicable']== "1" && $offer['feedback_status'] == "0" && $offer['delivery_status'] == "0"){
                                                                echo "<span style='color:orange;font-weight:bold;font-size:13px'>-N/A-</span>";
                                                        }
                                                        else
                                                                echo '--';
                                                }
                                        }
                                        
                                     ?>
                                    </td>
                         	</tr>
                            <?php } ?>
                        </tbody>
                </table>
	              <?php }else { ?>
					<b>No Recharge Offers Found</b>
				<?php } ?>
                                        
				<div align="right" class="log_pagination">
                                    <span class="pagination_snip"><?php echo $offers_pagination; ?></span>
                                </div>
                                <br>
                                <div align="right">
                                    <?php //if($offer['delivery_status']==0){ ?>
                                        <button type="button" class="button button-action process_status" onclick="process_recharge_offer(this)" >Process</button>
                                    <?php //} else echo '--'; ?>
                                </div>	
		</div>
                <script>                
                    var recharge_fids=[];
                    recharge_fids='<?php echo $recharge_franchisee_arr; ?>';
                </script>
<?php
    }
}
?>

<div class="clear">&nbsp;</div>
<!-- ====================================< MEMBER FEE BLOCK  >==================================== -->
<?php
if($type=='fee_list')
{
    if(isset($no_results_found))
    {
?>
        <p>No Results found.</p>
<?php
    }
    else
    {
?>
            <div id="fee_list">
                 <div class="filters_block">
                            <?php $terr_list=$this->db->query("select * from pnh_m_territory_info where id in ($fee_territory_arr) order by territory_name asc"); ?>
                            <div class="filter">
                                <b>Territory : </b>
                                <select class="fee_territory_filter">
                                    <option value="0">All</option>
                                    <?php foreach($terr_list->result_array() as $t) { ?>
                                        <option value="<?=$t['id'];?>"><?=$t['territory_name'];?></option>
                                    <?php } ?>
                                </select>
                            </div>

                        <?php $town_list=$this->db->query("select * from pnh_towns where id in ($fee_town_arr) order by town_name asc"); ?>
                        <div class="filter">
                                <b>Town :</b>
                                <select class="fee_town_filter">
                                    <option value="0">All</option>
                                    <?php foreach($town_list->result_array() as $t) { ?>
                                        <option value="<?=$t['id'];?>"><?=$t['town_name'];?></option>
                                    <?php } ?>
                                </select>
                        </div>

                        <?php $fran_list=$this->db->query("select * from pnh_m_franchise_info where franchise_id in ($fee_franchisee_arr) order by franchise_name asc"); ?>
                        <div class="filter">
                            <b>Franchise :</b>
                            <select class="fee_franchise_filter">
                                <option value="0">All</option>
                                <?php foreach($fran_list->result_array() as $f) { ?>
                                    <option value="<?=$f['franchise_id'];?>"><?=$f['franchise_name'];?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="filter">
                            <form style="margin-top:5px;">
                                    <b>From :</b><input type="date" id="fee_frm_date">
                                    <b>To :</b><input type="date" id="fee_to_date">
                                    <button type="button" class="button button-tiny fee_date_filter">Go</button>
                            </form>
                        </div>
                </div>
                <div class="clear">&nbsp;</div>
                <div align="right"  class="log_pagination fl_right">
                    <span class="pagination_snip"><?php echo $offers_pagination; ?></span>
                </div>
                <div class="block_notify">Total <?=$ttl_rows;?> items found. Showing <?=$pg_from;?> to <?=$pg_limit;?></div>
                
                <div class="clear">&nbsp;</div>

                <table class="datagrid smallheader noprint datagridsort" width="100%">
                        <thead>
                            <tr>
                                        <th width="1%">Sl No.</th>
                                        <th width="4%">Registered Date</th>
                                        <th width="6%">Member Name</th>
                                        <th width="10%">Franchise_name</th>
                                        <th width="4%">TransID</th>
                                        <th width="4%">Invoice</th>
                                        <th width="4%">Offer towards</th>
                                        <!--<th width="4%">Insurance Amount</th>-->
                                        <th width="4%">Member Fee</th>
                                        <th width="4%">Status</th>
                           </tr>	
                        </thead>

                        <tbody>
                                <?php foreach($member_fee_list as $i=>$offer){ ?>
                                <tr class="fee_table"  member_id="<?=$offer['member_id'];?>" date="<?=$offer['date'];?>" territory_id="<?=$offer['territory_id'];?>" town_id="<?=$offer['town_id'];?>" franchise_id="<?=$offer['franchise_id'];?>" transid="<?=$offer['transid_ref'];?>" offer_type="<?=$offer['offer_type'];?>" offer_value="<?=$offer['offer_value'];?>">
                                        <td><?=(++$i)+$pg?></td>
                                        <td><?=format_datetime($offer['created_on']);?></td>
                                        <td><a href="<?=site_url("admin/pnh_viewmember/".$offer['user_id']);?>" target="_blank"><?=$offer['first_name'];?></a></td>
                                        <td><a href="<?=site_url("admin/pnh_franchise/".$offer['franchise_id']);?>" target="_blank"><?=$offer['franchise_name'];?></a></td>
                                        <td><a href="<?=site_url("admin/trans/".$offer['transid_ref'])?>" target="_blank"><?=$offer['transid_ref'];?></a></td>
                                        <td><a href="<?=site_url()."admin/invoice/".$offer['invoice_no'];?>" target="_blank"><?=$offer['invoice_no'];?></a></td>
                                        <td>Rs. <?=$offer['offer_towards'];?></td>
                                        <td><?php
                                            $arr_offer_type = array(1=>"Free Recharge",2=>"Free Insurance",3=>"N/A or Not Opted",4=>"Requested for Insurance");
                                            $arr_delivery_status = array(0=>"Not delivered",1=>"Order Delivered");
                                                echo $arr_delivery_status[$offer['delivery_status']];
                                        ?>
                                        </td>
                                        <td>
<?php
                                                        //echo 'Rs. '.$offer['offer_value']." + ".PNH_MEMBER_FEE; 
                                                        echo "Rs. ".$offer['pnh_member_fee']; 
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
                
                <br>
                <script>
                    var fee_fids=[];
                    fee_fids='<?php echo $fee_franchisee_arr; ?>';
                </script>
</div>
<?php
    }
}
?>
