<?php
$cond = $cond_batch = $inner_loop_cond = $re_allot_all_block = $old_new_msg = $msg_generate_pick_list=$generate_btn_link='';
$block_alloted_status = '';
 if($s!=0 and $e != 0) {
    $from=strtotime($s);
    $to=strtotime("23:59:59 $e");
    $cond .= ' and tr.actiontime between '.$from.' and '.$to.' ';
    $datefilter_msg .= ' from <strong>'.date("m-d-Y",$from).'</strong> to <strong>'.date("m-d-Y",$to).'</strong> ';
 }else {
     $from=$to=0;
 }

$sel_terr_by_fran='<select name=\'sel_terr_id\' id=\'sel_terr_id\'></select>';
//$old_new_msg = '<option value="1" selected>NEWEST </option><option value="0" '.($latest=='0' ? "selected":"").'>OLDEST</option>';

$msg_process_by_fran .= '<div class="show_by_group_block"><label for="show_by_group">Process by franchise:</label> <input type="checkbox" value="by_group" name="show_by_group" id="show_by_group" '.($showbygrp?"checked":"").' title="Click to Show By Group"/></div>';

if( $batch_type == "pending") {
    $re_allot_all_block .= '<a href="javascript:void(0);" onclick="reallot_stock_for_all_transaction('.$user['userid'].','.$pg.');" class="button button-rounded button-caution">Re-Allot all pending transactions</a>';
    $generate_btn_link=$msg_generate_pick_list='';?>
    <script>$(".re_allot_all_block").css({"padding":"0px 0px"});</script>
    <?php
}
else {?>
    <script>$(".re_allot_all_block").css({"padding":0});</script>
    <?php
    //$msg_generate_pick_list .= '<input type="submit" class="button button-rounded button-action" value="Generate Pick List" name="btn_generate_pick_list" id="btn_generate_pick_list" title="Click to generate picklist for printing"/>';
//    $generate_btn_link .= '<input type="submit" class="button button-rounded button-tiny button-action" value="Create Batch" name="btn_cteate_group_batch" id="btn_cteate_group_batch" title="Click to Create Group Batch"/>';
}

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
 if(!isset($arr_trans_set)) {
        $arr_trans_set = $this->reservations->get_trans_list($batch_type,$from,$to);
 }

if(count($arr_trans_set['result']) == 0 ) {?>
        <script>$(".ttl_trans_listed").html("");
                    $(".pagination_top").html("");
                    $(".re_allot_all_block").html("");
        </script>
        <h3 class="heading_no_results">No franchise transactions found.</h3>';
<?php
}
else 
{
        //echo '<pre>'.$arr_trans_set['last_query'];
         foreach ($arr_trans_set['result'] as $i=>$arr_trans) { $all_trans[$i] = "'".$arr_trans['transid']."'";  }
         $str_all_trans = implode(",",$all_trans);

        //echo '<pre>'.$str_all_trans;die();
        
        $sql="select distinct o.transid,count(distinct tr.transid) as ttl_trans,
                        f.franchise_id,f.franchise_name,f.territory_id,f.town_id,f.created_on as f_created_on,o.ship_phone
                        ,ter.territory_name,twn.town_name
                from king_transactions tr
                        join king_orders o on o.transid=tr.transid
                        join king_dealitems di on di.id=o.itemid
                        join king_deals dl on dl.dealid=di.dealid
                        join pnh_menu m on m.id = dl.menuid
                        join king_brands bs on bs.id = o.brandid
                join pnh_m_franchise_info  f on f.franchise_id=tr.franchise_id and f.is_suspended = 0
                join pnh_m_territory_info ter on ter.id = f.territory_id 
                join pnh_towns twn on twn.id=f.town_id
                    left join king_invoice i on i.order_id = o.id and i.invoice_status = 1
                    left join proforma_invoices pi on pi.order_id = o.id and pi.invoice_status = 1 
                WHERE o.status in (0,1) and tr.batch_enabled=1 and i.id is null and o.transid in ($str_all_trans) $cond
                group by tr.franchise_id order by f.territory_id,f.town_id,f.franchise_id desc";
 
                $transactions_src=$this->db->query($sql);
            //echo "<p><pre>".$this->db->last_query().'</pre></p>';die(); 
            
        if(!$transactions_src->num_rows()) { ?>
                <script>$(".ttl_trans_listed").html("");
                        $(".pagination_top").html("");
                        $(".re_allot_all_block").html("");
                </script>
                <h3 class="heading_no_results">No franchise found for selected criteria.</h3>
        <?php }
        else 
        {
            $total_trans_rows=$transactions_src->num_rows(); //$transactions=$this->db->query($sql." limit $pg,$limit")->result_array();
            $transactions=$transactions_src->result_array();
            ?>
            <table class="datagrid" width="100%">
                        <thead>
                            <tr>
                                <th style="width:10px">#</th>
                                <th style="width:150px">Territory Name</th>
                                <th style="width:150px">Town Name</th>
                                <th style="width:160px">Franchise</th>
                                <th style="width:150px">Actions</th>
                                <th style="width:100px">Transactions</th>
                                <th style="" align="left">&nbsp; <a href="javascript:void(0)" id="show_all_orders" onclick="return show_all_orders();" class="fl_right button button-tiny button-rounded">Show all orders</a></th>
                            <tr>
                        </thead>
                        <tbody>
            <?php 
            $temp_arr1 = $temp_arr2 = array();
            foreach($transactions as $i=>$trans_arr) 
            {
                 $ungrp_terr_status=$this->reservations->is_terri_batch_created($trans_arr['territory_id'] );
                $generate_btn_link_2='';
                
                $arr_fran = $this->reservations->fran_experience_info($trans_arr['f_created_on']);
                //Territory array
                $territory_name = $trans_arr['territory_name'];
                if(!in_array($territory_name, $temp_arr1)) {
                    $temp_arr1[$territory_name] = $territory_name;
                    $print_territory_name = $trans_arr['territory_name'];

                    if( $batch_type == "pending") {
                        $generate_btn_link_2 = '';
                    }
                    else {
                        if( $ungrp_terr_status ) 
                            $generate_btn_link_2 = '<input type="button" class="btn_cteate_group_batch button button-rounded button-tiny button-action" value="Create Batch" name="btn_cteate_group_batch" onclick="fn_cteate_group_batch('.$trans_arr['territory_id'].',0,0);" title="Click to Create Group Batch"/>';
                    }
                    $terri_name = $print_territory_name.'<br><span>'.$generate_btn_link_2.'</span>';
                }
                else {
                    $print_territory_name = '';
                    $terri_name = '--"--';
                }

                //Town array
                if(!in_array($trans_arr['town_name'], $temp_arr2)) {
                    $temp_arr2[] = $trans_arr['town_name'];
                    $town_name = $trans_arr['town_name'];
                }
                else 
                    $town_name = '--"--';

                //franchise info
                if( $batch_type == "pending")
                    $generate_btn_link_3 = '';
                else {
                    if( $ungrp_terr_status ) 
                        $generate_btn_link_3 = '<input type="button" class="btn_cteate_group_batch button button-rounded button-tiny button-action" value="Create Batch" name="btn_cteate_group_batch" onclick="fn_cteate_group_batch('.$trans_arr['territory_id'].',0,'.$trans_arr['franchise_id'].');" title="Click to Create Group Batch"/>';
                }
                ?>
                <tr class="filter_terr_<?=$trans_arr['territory_id'];?> filter_terr">
                        <td><?=(++$i);?></td>
                        <td align="center"><?=$terri_name;?></td>
                        <td align="center"><?=$town_name;?></td>
                        <td><span class="info_links"><a href="<?=site_url("admin/pnh_franchise/{$trans_arr['franchise_id']}");?>"  target="_blank"><?=$trans_arr['franchise_name'];?></a></span>
                                <br><span><?=$generate_btn_link_3;?></span>
                                <span><?=$trans_arr['ship_phone'];?><br></span><span class="fran_experience" style="background-color:<?=$arr_fran['f_color'];?>;color: #ffffff;"><?=$arr_fran['f_level'];?></span>
                        </td>
                        <?php
                        $inner_output = '';
                            if( $batch_type == "pending") {
                                    $all_trans=array();$str_all_trans='';
                                    foreach ($arr_trans_set['result'] as $arr_trans) { 
                                        if($trans_arr['franchise_id'] == $arr_trans['franchise_id']) {
                                            $all_trans[$arr_trans['transid']] = "'".$arr_trans['transid']."'";
                                        }
                                    }
                                    array_unique($all_trans);
                                    $str_all_trans = implode(",",$all_trans);
                                    $inner_output .= '<td>
                                                            <a href="javascript:void(0);" class="retry_link button button-rounded button-tiny button-caution" all_trans="'.$str_all_trans.'" onclick="return reallot_frans_all_trans(this,'.$user['userid'].',\''.trim($trans_arr['franchise_id']).'\','.$pg.');">Re-Allot all trans</a>
                                                     </td>';
                            }
                            else {
                                        $inner_output .= '<td> -- </td>';
                                        /*$arr_pinv_ids =array();
                                        foreach ($arr_trans_set['result'] as $arr_trans) { 
                                            if($trans_arr['franchise_id'] == $arr_trans['franchise_id']) {
                                                $arr_pinv_ids[] = $arr_trans['p_inv_nos'];
                                            }
                                        }
                                        $str_pinv_ids = implode(",", array_unique($arr_pinv_ids));
                                        $inner_output .= '<a class="button button-rounded button-tiny button-action" href="javascript:void(0)" onclick="process_pinvoices_by_fran(this,'.$trans_arr['franchise_id'].')" p_invoice_ids="'.$str_pinv_ids.'">Generate invoice</a>
                                                        <form action="'.site_url('admin/pack_invoice_by_fran/'.$trans_arr['batch_id'].'/'.$trans_arr['franchise_id'].'').'" method="post" id="pinvoices_form_'.$trans_arr['franchise_id'].'" target="_blank">
                                               </form>';*/
    //                                    $inner_output .= '<br><a class="btn_picklist button button-rounded button-tiny button-primary" href="javascript:void(0)" onclick="picklist_product_wise(this,'.$trans_arr['batch_id'].','.$trans_arr['franchise_id'].')" p_invoice_ids="'.$str_pinv_ids.'">Generate Picklist</a>';
                                            /*<form action=""><input type="hidden" value="'.$str_pinv_ids.'" name="pick_list_invids" id="picklist_by_fran_all_'.$trans_arr['franchise_id'].'"/></form>*/
                            }
                            echo ''.$inner_output;
                        ?>
                        <td><span class="fl_left"><?=$trans_arr['ttl_trans'];?></span></td>
                        <td align="left">
                            <div class="view_all_orders"><a href="javascript:void(0);" class="view_all_link button button-tiny glow" onclick="return show_orders_list('<?=$trans_arr['franchise_id'];?>','<?=$from;?>','<?=$to;?>','<?=$batch_type;?>')" >View Orders</a></div>
                            <!--<div class="clear">&nbsp;</div>
                            <th style="width:25px"><input type="checkbox" value="" name="pick_all_fran" id="pick_all_fran_'.$franchise_id.'" class="pick_list_trans_grp_fran" title="Select all invoices" onclick="chkall_fran_orders('.$franchise_id.')" />PickList</th>
                            -->
                            <div class="orders_info_block_<?=$trans_arr['franchise_id']?>" class="orders_info_block" style="display:none;">
                                <table width="100" class="subdatagrid">
                                <thead>
                                <tr>
                                    <th style="width:25px">#</th>
                                    <th style="width:100px">Trans</th>
                                    <th style="width:100px">Batch ID</th>
                                    <th style="width:100px">Order ID</th>
                                    <th style="width:250px">Order Name</th>
                                    <th style="width:100px">Action</th>
                                </tr>
                                </thead>
                                <tbody></tbody></table>
                            </div>
                        </td>
                </tr>
        <?php   $fil_territorylist[$trans_arr['territory_id']] = $trans_arr['territory_name'];
            }
        ?>
            </tbody></table>
            <script>
                $(".level1_filters").show();
                $(".ttl_trans_listed").html("Showing <strong><?=$total_trans_rows;?></strong> franchises <?=$datefilter_msg?>");
                $(".btn_picklist_block").html("<?=($msg_generate_pick_list);?>");
                $(".sel_terr_block").html("<?=$sel_terr_by_fran;?>");
                $(".batch_btn_link").html("<?=($generate_btn_link);?>");
                $(".process_by_fran_link").html("<?=($msg_process_by_fran);?>");
                $(".re_allot_all_block").html("<?=($re_allot_all_block);?>");
                $("#sel_old_new").hide();
                $(".block_alloted_status").hide();
            </script>
        <?php

        }
}
    
if(count($fil_territorylist) && $terrid==0) {
    asort($fil_territorylist);
    $territory_list = '<option value="00">All Territory</option>';
    foreach($fil_territorylist as $fterrid=>$fterritory_name) {
        $territory_list .= '<option value="'.$fterrid.'">'.$fterritory_name.'</option>';   
    }
    $resonse2.='<script>$("#sel_terr_id").html(\''.$territory_list.'\');</script>';
}
echo ''.$resonse2;
?>