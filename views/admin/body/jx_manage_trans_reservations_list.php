<?php
$output = $cond = $cond_batch = $inner_loop_cond = $re_allot_all_block=$orderby_cond = $datefilter_msg =$msg_generate_pick_list='';
$block_alloted_status = '';
 if($s!=0 and $e != 0) {
    $from=strtotime($s);
    $to=strtotime("23:59:59 $e");
    $cond .= ' and tr.actiontime between '.$from.' and '.$to.' ';
    $datefilter_msg .= ' from <strong>'.date("M/d/Y",$from).'</strong> to <strong>'.date("M/d/Y",$to).'</strong> ';
 }

$msg_process_by_fran = '<div class="show_by_group_block"><label for="show_by_group">Process by franchise:</label> <input type="checkbox" value="by_group" name="show_by_group" id="show_by_group" '.($showbygrp?"checked":"").' title="Click to Show By Group"/></div>';


if($latest == 0)
    $orderby_cond = ' g.actiontime ASC ';
else
    $orderby_cond = ' g.actiontime DESC ';

//$chk_box_global = '<input type="checkbox" value="" name="pick_all" id="pick_all" title="Select all transactions" />';
//$old_new_msg = '<option value="1" selected>NEWEST </option><option value="0" '.($oldest_newest=='0' ? "selected":"").'>OLDEST</option>';
//$orderby_cond .='g.init';

if( $batch_type == "pending") {
        //$orderby_cond = ' g.actiontime ASC ';
}
elseif($batch_type == 'partial') {
        //$cond_batch .= ' ( g.batch_id is null ) ';// or g.batch_id >= '.GLOBAL_BATCH_ID." ";
        if($batch_group_type == 1) {
            $cond_batch .= ' g.batch_id <> '.GLOBAL_BATCH_ID.' and g.batch_id >= '.GLOBAL_BATCH_ID; $orderby_cond = ' g.batch_id desc ';
        }
        elseif($batch_group_type == 2) {
            $cond_batch .= ' g.batch_id = '.GLOBAL_BATCH_ID.' and  g.batch_id >= '.GLOBAL_BATCH_ID;
        }
        $batch_btn_link .= '<input type="submit" class="btn_cteate_group_batch button button-rounded button-action" value="Create Group Batch" name="btn_cteate_group_batch" onclick="fn_cteate_group_batch(0,0,0);" title="Click to Create Group Batch"/>';
}
elseif($batch_type == 'ready') {
        $cond_batch .=' g.batch_id >= '.GLOBAL_BATCH_ID." ";
        if($batch_group_type == 1) {
            $cond_batch .= ' and g.batch_id <> '.GLOBAL_BATCH_ID.' and g.batch_id >= '.GLOBAL_BATCH_ID; $orderby_cond = ' g.batch_id desc ';
        }
        elseif($batch_group_type == 2) {
            $cond_batch .= ' and g.batch_id = '.GLOBAL_BATCH_ID.' and  g.batch_id >= '.GLOBAL_BATCH_ID;
        }
 
        // is batch assigned to user?
        //if($user['userid']) {
            //$cond .= ' and sd.assigned_userid = '.$user['userid'].' ';
        //}
        $batch_btn_link .= '<input type="submit" class="btn_cteate_group_batch button button-rounded button-action" value="Create Group Batch" name="btn_cteate_group_batch" onclick="fn_cteate_group_batch(0,0,0);" title="Click to Create Group Batch"/>';
//        $msg_generate_pick_list .= '<input type="submit" class="button button-rounded button-action" value="Generate Pick List" name="btn_generate_pick_list" id="btn_generate_pick_list" title="Click to generate picklist for printing"/>';
}
$cond_batch = ($cond_batch!='')? " where ".$cond_batch : "";

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
if($latest_batches) {
    $orderby_cond = ' g.batched_on desc';
}
if($latest_batches && $batch_type == 'pending') {
    $orderby_cond = ' g.cancelled_on,g.actiontime desc';
}
$arr_trans_set = $this->reservations->get_trans_list($batch_type,$from,$to,0,$user['userid']); //,$oldest_newestecho '<pre>'.count($arr_trans_set['result']);die();
//echo '<pre>'.$arr_trans_set['last_query'];die();
if(count($arr_trans_set['result']) == 0 ) {?>
    <h3 class="heading_no_results">No transactions found for selected criteria.</h3><?php
}
else 
{
        
            foreach ($arr_trans_set['result'] as $i=>$arr_trans) { $all_trans[$i] = "'".$arr_trans['transid']."'";  }
            $str_all_trans = implode(",",$all_trans);

            $transid = $trans_array['transid']; //from_unixtime(tr.init,'%D %M %Y') as str_date
            $sql="select * from (
            select distinct from_unixtime(tr.init,'%d/%m/%Y') as str_date,from_unixtime(tr.init,'%h:%i:%s %p') as str_time, count(tr.transid) as total_trans,tr.transid
                    ,o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on
                    ,ter.territory_name
                    ,twn.town_name
                    ,dl.menuid,m.name as menu_name,bs.name as brand_name
                    ,sd.batch_id,sd.batched_on
                    ,pi.cancelled_on
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
            WHERE o.status in (0,1) and tr.batch_enabled=1 and i.id is null $cond and tr.transid in ($str_all_trans)
            group by o.transid) as g $cond_batch group by transid order by $orderby_cond";
            

//             echo "<p><pre>".$sql.'</pre></p>';die(); 
            
            $transactions_src=$this->db->query($sql);
            $total_trans_rows=$transactions_src->num_rows();
            
            if($total_trans_rows == 0 ) {?>
                           <script>$(".ttl_trans_listed").html("");
                                    $(".pagination_top").html("");
                                    $(".re_allot_all_block").html("");
                            </script>
                            <h3 class="heading_no_results">No transactions found for selected criteria.</h3>
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
                        <th style="width:120px">Batch ID</th>
                        <th style="width:200px">Transaction Reference</th>
                        <th style="padding:0px !important;">Item details</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody><!--<th align="center">Pick List<br> '.$chk_box_global.'</th> -->
                <?php
                $c = $pg;
                foreach($transactions as $trans_arr) {

                        $batch_ids = array();
                        $invoice_infos=array();
                        $trans_action_msg = '';
                        $batch_id_msg='--';
                        $batch_enabled='';
                        $trans_action_msg='';//$pick_list_msg='';

                        $trans_created_by = $this->reservations->get_trans_created_by($trans_arr['transid']);
                        if($trans_created_by) 
                                $trans_created_by = '<div class="trans_created_by"> by '.($trans_created_by).'';

                        $arr_fran = $this->reservations->fran_experience_info($trans_arr['f_created_on']);
                        
                        if($trans_arr['batch_enabled']!=1)
                            $batch_enabled='<div class="clear" style="margin-top:20px;background-color:#504952;"><span style="color: #8C489F;padding:2px 3px;">Batch disabled</span></div>';
                        
                        if($trans_arr['batch_id'] != '') { 
                            //$batch_id_msg = '<a href="'.site_url("admin/batch/".$trans_arr['batch_id']).'" target="_blank">B'.$trans_arr['batch_id'].'</a>';
                            if($trans_arr['batch_id'] == GLOBAL_BATCH_ID) {
                                $batch_id_msg = '<div class="ungrouped_item">Un-Grouped</div>';
                            }
                            else {
                                $batch_id_msg = '<div class="info_links"><a href="'.site_url("admin/batch/".$trans_arr['batch_id']).'" target="_blank">B'.$trans_arr['batch_id'].'</a></div>';
                            }
                        }

                        $output .= '<tr class="'.$batch_type.'_ord">
                            <td align="center">'.++$c.'</td>
                            <td align="left">'.$trans_arr['str_date'].'<div class="str_time">'.($trans_arr['str_time']).'</div>'.$trans_created_by.'</td>
                            <td align="left">
                                    '.$batch_id_msg.'
                                    '.$batch_enabled.'</td>
                            <td align="left">
                                <span class="info_links"><a href="trans/'.$trans_arr['transid'].'" target="_blank">'.$trans_arr['transid'].'</span></a>
                                <br><br>
                                <span style="padding-top:12px;" class="info_links"><a href="'.site_url("admin/pnh_franchise/{$trans_arr['franchise_id']}").'"  target="_blank">'.$trans_arr['bill_person'].'</a><br></span>
                                <span class="info_links"><b>'.$trans_arr['territory_name'].' :</b> '.$trans_arr['town_name'].'<br></span>
                                <br><span class="fran_experience" style="background-color:'.$arr_fran['f_color'].';color: #ffffff;">'.$arr_fran['f_level'].'</span>
                            </td>
                            <td>';


                        $output .='<table class="subdatagrid" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <th>Slno</th>
                                            <th>Order id</th>
                                            <th>Deal Name</th>
                                            <th>Quantity</th>
                                            <th>MRP</th>
                                            <th>Amount</th>
                                            <th>Alloted</th>
                                        </tr>';

                            /*<th>Alloted/Status?</th><th>Stock</th>
                            $stockinfo = $this->reservations->get_stock_from_orderid($order_i['id']);$status=array("Confirmed","Processed","Shipped","Cancelled","Returned");
                            <td style="width:20px">'.($order_i['status']?"Yes":"No").' | '.$status[$trans_arr['shipped']].' </td>
                            <td style="width:20px"><a href="'.site_url("admin/product/".$stockinfo['product_id']).'" target="_blank">'.$stockinfo['stock'].'</a></td>*/

                        $trans_orders = $this->reservations->get_orders_of_trans($trans_arr['transid']);
                        foreach($trans_orders as $j=>$order_i)  {

                                    $output .='<input type="hidden" size="2" class="'.$trans_arr['transid'].'_total_orders" value="'.count($trans_orders).'" />';

                                    $amount=round($order_i['i_orgprice']-($order_i['i_coup_discount']+$order_i['i_discount']),2);

                                    $o_status = ($order_i['status']?"yes":"no");

                                    $output .= '<tr class="order_status_'.$o_status.'">
                                        <td style="width:25px">'.++$j.'</td>
                                        <td style="width:50px"><span class="info_links"><a href="pnh_deal/'.$order_i['itemid'].'" target="_blank">'.$order_i['id'].'</a></span></td>
                                        <td style="width:200px"><span class="info_links"><a href="pnh_deal/'.$order_i['itemid'].'" target="_blank">'.$order_i['name'].'</a></span></td>
                                        <td style="width:50px">'.$order_i['quantity'].'</td>
                                        <td style="width:50px">'.$order_i['i_orgprice'].'</td>
                                        <td style="width:50px">'.$amount.'</td>
                                        <td style="width:20px">'.ucfirst($o_status).' </td>
                                    </tr>';
                        }

                        $output .= '</table></td>';

                       if( $batch_type == "pending") 
                        {
                                $trans_action_msg .= '<a href="javascript:void(0);" class="retry_link button button-rounded button-tiny button-caution" onclick="return reserve_stock_for_trans('.$user['userid'].',\''.trim($trans_arr['transid']).'\','.$pg.');">Re-Allot</a>';
//                                $pick_list_msg .= '--';

                                $re_allot_all_block = '<a href="javascript:void(0);" class="button button-action  button-caution" onclick="reallot_stock_for_all_transaction('.$user['userid'].','.$pg.');">Re-Allot all pending transactions</a>';
                                $output .= '<script>$(".re_allot_all_block").css({"padding":"4px 10px"});</script>';

                        }
//                        else {
                                $arr_pinv_ids =array();
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
                                }
//                        }

                        $output .= '<td width="200">'.$trans_action_msg.'</td>
                            </tr>';//<td style="width:70px">'.$pick_list_msg.'</td>

                        $fil_territorylist[$trans_arr['territory_id']] = $trans_arr['territory_name'];
                        $fil_townlist[$trans_arr['town_id']] = $trans_arr['town_name'];
                        $fil_menulist[$trans_arr['menuid']] = $trans_arr['menu_name'];
                        $fil_brandlist[$trans_arr['brandid']] = $trans_arr['brand_name'];
                        $fil_franchiselist[$trans_arr['franchise_id']] = $trans_arr['franchise_name'];
                }
                echo $output;
                
            //   PAGINATION
                $this->load->library('pagination');
                $config['base_url'] = site_url("admin/jx_manage_trans_reservations_list/".$batch_type.'/'.$s.'/'.$e.'/'.$terrid.'/'.$townid.'/'.$franchiseid.'/'.$menuid.'/'.$brandid."/".$showbygrp."/".$batch_group_type."/".$latest."/".$alloted_status."/".$latest_batches."/".$limit); 
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
                <div class="trans_pagination"><?=$trans_pagination;?></div>
                <?php
            }
}

//===========
#Other functions
$resonse2='';
if(count($fil_menulist) && $menuid==0) {
    asort($fil_menulist);
    $menulist = '<option value="00">All Menu</option>';
    foreach($fil_menulist as $fmenu_id=>$fmenu_name) {
        $menulist .= '<option value="'.$fmenu_id.'">'.$fmenu_name.'</option>';   
    }
    $resonse2.='<script>$("#sel_menu").html(\''.$menulist.'\');</script>';
}
if(count($fil_brandlist) && $brandid==0) {
    asort($fil_brandlist);
    $brandlist = '<option value="00">All Brands</option>';
    foreach($fil_brandlist as $fbrandid=>$fbrand_name) {
        $brandlist .= '<option value="'.$fbrandid.'">'.$fbrand_name.'</option>';   
    }
    $resonse2.='<script>$("#sel_brands").html(\''.$brandlist.'\');</script>';
}
if(count($fil_territorylist) && $terrid==0) {
    asort($fil_territorylist);
    $territory_list = '<option value="00">All Territory</option>';
    foreach($fil_territorylist as $fterrid=>$fterritory_name) {
        $territory_list .= '<option value="'.$fterrid.'">'.$fterritory_name.'</option>';   
    }
    $resonse2.='<script>$("#sel_territory").html(\''.$territory_list.'\');</script>';
}
if(count($fil_townlist) && $townid==0) {
    asort($fil_townlist);
    $town_list = '<option value="00">All Towns</option>';
    foreach($fil_townlist as $ftownid=>$ftown_name) {
        $town_list .= '<option value="'.$ftownid.'">'.$ftown_name.'</option>';   
    }
    $resonse2.='<script>$("#sel_town").html(\''.$town_list.'\');</script>';
}
if(count($fil_franchiselist) && $franchiseid==0) {
    asort($fil_franchiselist);
    $franchise_list = '<option value="00">All Franchise</option>';
    foreach($fil_franchiselist as $franchiseid=>$franchise_name) {
        $franchise_list .= '<option value="'.$franchiseid.'">'.$franchise_name.'</option>';   
    }
    $resonse2.='<script>$("#sel_franchise").html(\''.$franchise_list.'\');</script>';
}
echo $resonse2;
?>
<script>
    $(".level1_filters").show();
    $(".pagination_top").html('<?=$trans_pagination?>');
    $(".ttl_trans_listed").html("Showing <strong><?=($pg+1);?> - <?=$endlimit?></strong> / <strong><?=$total_trans_rows?></strong> transactions <?=$datefilter_msg?>");
    $(".btn_picklist_block").html('<?=($msg_generate_pick_list);?>');
    $(".batch_btn_link").html('<?=($batch_btn_link);?>');
    $(".process_by_fran_link").html('<?=($msg_process_by_fran);?>');
    $(".re_allot_all_block").html('<?=($re_allot_all_block);?>');
    $(".chk_latest_batch").show();
    <?php //if( $batch_type == "pending") {?>
        //$(".chk_latest_batch").hide();
    <?php //} ?>
    $(".sel_terr_block").html("");
    $(".block_alloted_status").hide();

</script>