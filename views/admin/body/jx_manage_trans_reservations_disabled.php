<?php
$msg_generate_pick_list = '';
$msg_process_by_fran = '';
$re_allot_all_block = '';
$generate_btn_link='';
$msg_process_by_fran='';
$ttl_trans_listed='';
$output = $cond =  $inner_loop_cond = $re_allot_all_block=$orderby_cond = $datefilter_msg =$msg_generate_pick_list='';

//$block_alloted_status = '';
//$msg_process_by_fran = '<div class="show_by_group_block"><label for="show_by_group">Process by franchise:</label> <input type="checkbox" value="by_group" name="show_by_group" id="show_by_group" '.($showbygrp?"checked":"").' title="Click to Show By Group"/></div>';

if($s!=0 and $e != 0) {
    $from=strtotime($s);
    $to=strtotime("23:59:59 $e");
    $cond .= ' and tr.actiontime between '.$from.' and '.$to.' ';
    $datefilter_msg .= ' from <strong>'.date("m-d-Y",$from).'</strong> to <strong>'.date("m-d-Y",$to).'</strong> ';
 }
 
if($latest == 0)
    $orderby_cond = ' tr.actiontime ASC ';
else
    $orderby_cond = ' tr.actiontime DESC ';

if($menuid!=0) {
     $cond .= ' and dl.menuid='.$menuid; 
}
if($brandid!=0) {
     $cond .= ' and dl.brandid='.$brandid;
 }
if($terrid!=0) {
     $cond .= ' and f.territory_id='.$terrid;
}
if($townid!=0) {
    $cond .= ' and f.town_id='.$townid;
}
if($franchiseid!=0) {
    $cond .= ' and f.franchise_id='.$franchiseid;
}
if($alloted_status == 1) {
    $cond .= ' and  i.id is not null and o.status = 1 ';
}
else {
    $cond .= ' and  i.id is null and  o.status = 0 ';
}

 $sql = "select distinct from_unixtime(tr.init,'%d/%m/%Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
                    ,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
                    ,ter.territory_name,twn.town_name
                    ,dl.menuid,m.name as menu_name,bs.name as brand_name
                    ,sd.batch_id
            from king_transactions tr
                    join king_orders o on o.transid=tr.transid
                    join king_dealitems di on di.id=o.itemid
                    join king_deals dl on dl.dealid=di.dealid
                    join pnh_menu m on m.id = dl.menuid
                    join king_brands bs on bs.id = o.brandid
            join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id  and f.is_suspended = 0
            join pnh_m_territory_info ter on ter.id = f.territory_id
            join pnh_towns twn on twn.id=f.town_id
                    left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                    left join proforma_invoices pi on pi.order_id = o.id and pi.invoice_status = 1 
                    left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no
            WHERE tr.batch_enabled=0 $cond
            group by tr.transid order by $orderby_cond";

//             echo "<p><pre>".$sql.'</pre></p>';die(); 
            
            $transactions_src=$this->db->query($sql);
            $total_trans_rows=$transactions_src->num_rows();
            
            if($total_trans_rows == 0 ) { ?>
                           <script>
                                    $(".ttl_trans_listed").html("");
                                    $(".pagination_top").html("");
                                    $(".re_allot_all_block").html("");
                            </script>
                            <h3 class="heading_no_results">No transactions found for selected criteria.</h3>';
            <?php
            }
            else 
            {
                $transactions_src=$this->db->query($sql." limit $pg,$limit ");
                $transactions=$transactions_src->result_array();

                ?>
                <form name="p_invoice_for_picklist" id="p_invoice_for_picklist">
                <table class="datagrid" width="100%">
                <thead>
                    <tr>
                        <th style="width:15px">Slno</th>
                        <th style="width:120px">Ordered On</th>
                        <th style="width:120px">Batch ID(Group)</th>
                        <th style="width:200px">Transaction Reference</th>
                        <th style="padding:0px !important;">Item details</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <!--<th align="center">Pick List<br> <?=$chk_box_global?></th>-->
                <?php
                $c = $pg;
                foreach($transactions as $trans_arr) {
                        $batch_ids = array();
                        $invoice_infos=array();
                        $trans_action_msg = '';
                        $batch_id_msg='--';
                        $trans_action_msg='';//$pick_list_msg='';

                        $trans_created_by = $this->reservations->get_trans_created_by($trans_arr['transid']);
                        if($trans_created_by) 
                                $trans_created_by = '<div class="trans_created_by"> by '.($trans_created_by).'';

                        $arr_fran = $this->reservations->fran_experience_info($trans_arr['f_created_on']);
                        
                       if($trans_arr['batch_enabled']!=1)
                            $batch_enabled='<div style="margin-top:20px;background-color:#504952; float:left;"><span style="color:#f8f8f8;padding:2px 3px;">Batch disabled</span></div>';
                        
                        if($trans_arr['batch_id'] != '') { 
                            //$batch_id_msg = '<a href="'.site_url("admin/batch/".$trans_arr['batch_id']).'" target="_blank">B'.$trans_arr['batch_id'].'</a>';
                            if($trans_arr['batch_id'] == GLOBAL_BATCH_ID) {
                                $batch_id_msg = '<div class="ungrouped_item">Un-Grouped</div>';
                            }
                            else {
                                $batch_id_msg = '<div class="info_links"><a href="'.site_url("admin/batch/".$trans_arr['batch_id']).'" target="_blank">B'.$trans_arr['batch_id'].'</a></div>';
                            }
                        }
                        ?>
                        <tr class="<?=$batch_type;?>_ord">
                            <td align="center"><?=++$c;?></td>
                            <td align="center"><?=$trans_arr['str_date'];?><div class="str_time"><?=($trans_arr['str_time']);?></div><?=$trans_created_by;?></td>
                            <td align="center"><?php echo $batch_enabled.''.$batch_id_msg;?></td>
                            <td align="center">
                                <span class="info_links"><a href="trans/<?=$trans_arr['transid'];?>" target="_blank"><?=$trans_arr['transid'];?></span><br></a>
                                <span class="info_links"><a href="<?=site_url("admin/pnh_franchise/{$trans_arr['franchise_id']}");?>"  target="_blank"><?=$trans_arr['bill_person'];?></a><br></span>
                                <span class="info_links"><?=$trans_arr['town_name'];?></span>,
                                <span class="info_links"><?=$trans_arr['territory_name'];?><br></span>
                                <span><?=$trans_arr['ship_phone'];?><br></span><span class="fran_experience" style="background-color:<?=$arr_fran['f_color'];?>;color: #ffffff;"><?=$arr_fran['f_level'];?></span>
                            </td>
                            <td>

                        <table class="subdatagrid" cellpadding="0" cellspacing="0">
                            <tr>
                                <th>Slno</th>
                                <th>Order id</th>
                                <th>Deal Name</th>
                                <th>Quantity</th>
                                <th>MRP</th>
                                <th>Amount</th>
                                <th>Alloted</th>
                            </tr>
                        <?php
                        $trans_orders = $this->reservations->get_cancelled_orders_of_trans($trans_arr['transid']);
                        foreach($trans_orders as $j=>$order_i)  {
                                $amount=round($order_i['i_orgprice']-($order_i['i_coup_discount']+$order_i['i_discount']),2);
                                $o_status = ($order_i['status']?"yes":"no");
                                ?>

                                <input type="hidden" size="2" class="<?=$trans_arr['transid'];?>_total_orders" value="<?=count($trans_orders);?>" />
                                <tr class="order_status_<?=$o_status;?>">
                                    <td style="width:25px"><?=++$j;?></td>
                                    <td style="width:50px"><span class="info_links"><a href="pnh_deal/<?=$order_i['itemid'];?>" target="_blank"><?=$order_i['id'];?></a></span></td>
                                    <td style="width:200px"><span class="info_links"><a href="pnh_deal/<?=$order_i['itemid'];?>" target="_blank"><?=$order_i['name'];?></a></span></td>
                                    <td style="width:50px"><?=$order_i['quantity'];?></td>
                                    <td style="width:50px"><?=$order_i['i_orgprice'];?></td>
                                    <td style="width:50px"><?=$amount;?></td>
                                    <td style="width:20px"><?=ucfirst($o_status);?></td>
                                </tr>
                    <?php  }  ?>
                       </table></td>
                       <?php
//                        else {
                                /*$arr_pinv_ids =array();
                                foreach ($arr_trans_set['result'] as $i=>$arr_trans) { 
                                    if($trans_arr['transid'] == $arr_trans['transid']) {
                                        $arr_pinv_ids[] = $arr_trans['p_inv_nos'];
                                    }
                                }
                                foreach ($arr_pinv_ids as $p_invoice_id ) {
                                    if($p_invoice_id != '') { // and $trans_arr['batch_id'] != GLOBAL_BATCH_ID
                                        $trans_action_msg .= '<div><a class="danger_link2 button button-rounded button-tiny button-caution" href="javascript:void(0)" onclick="cancel_proforma_invoice(\''.$p_invoice_id.'\','.$user['userid'].','.$pg.')" class="">De-Allot</a></div>';
                                        //$pick_list_msg .= '<input type="checkbox" value="'.$p_invoice_id.'" id="pick_list_trans" name="pick_list_trans[]" class="pick_list_trans_ready" title="Select this for picklist" />';
                                        //<a class="proceed_link button button-rounded button-tiny button-action" href="pack_invoice/'.$p_invoice_id.'" target="_blank">Generate invoice</a><br>
                                    }
                                }*/
//                        }
                        ?>
                        <td width="200"><?=$trans_action_msg?></td>
                    </tr>
                        <?php
                        $fil_territorylist[$trans_arr['territory_id']] = $trans_arr['territory_name'];
                        $fil_townlist[$trans_arr['town_id']] = $trans_arr['town_name'];
                        $fil_menulist[$trans_arr['menuid']] = $trans_arr['menu_name'];
                        $fil_brandlist[$trans_arr['brandid']] = $trans_arr['brand_name'];
                        $fil_franchiselist[$trans_arr['franchise_id']] = $trans_arr['franchise_name'];

            }
    
    //   PAGINATION
            $this->load->library('pagination');
            $config['base_url'] = site_url("admin/jx_manage_trans_reservations_list/".$batch_type.'/'.$s.'/'.$e.'/'.$terrid.'/'.$townid.'/'.$franchiseid.'/'.$menuid.'/'.$brandid."/".$showbygrp."/".$batch_group_type."/".$latest."/".$alloted_status."/".$orderby_cond."/".$limit); 
            $config['total_rows'] = $total_trans_rows;
            $config['per_page'] = $limit;
            $config['uri_segment'] = 17;
            $config['num_links'] = 5;
            $config['cur_tag_open'] = '<span class="curr_pg_link">';
            $config['cur_tag_close'] = '</span>';
            $this->config->set_item('enable_query_strings',false); 
            $this->pagination->initialize($config); 
            $trans_pagination = $this->pagination->create_links();
            $this->config->set_item('enable_query_strings',TRUE);
    //   PAGINATION ENDS
            $endlimit=($pg+1*$limit);
            $endlimit=($endlimit>$total_trans_rows)?$total_trans_rows : $endlimit;
            ?>
                    </tbody>
                </table>
            </form>
            <div class="trans_pagination"><?=$trans_pagination?> </div>
            
            <script>
                $(".level1_filters").show();
                $(".pagination_top").html('<?=$trans_pagination?>');
                $(".ttl_trans_listed").html("Showing <strong><?=($pg+1);?> - <?=$endlimit?></strong> / <strong><?=$total_trans_rows?></strong> transactions <?=$datefilter_msg?>");
                $(".btn_picklist_block").html('<?=($msg_generate_pick_list);?>');
                $(".batch_btn_link").html('<?=($batch_btn_link);?>');
                $(".process_by_fran_link").html('<?=($msg_process_by_fran);?>');
                $(".re_allot_all_block").html('<?=($re_allot_all_block);?>');
                $(".sel_terr_block").html("");
                $(".block_alloted_status").show(); //html(\''.$block_alloted_status.'\');
                $(".chk_latest_batch").hide();
            </script>
    <?php
    }
    ?>