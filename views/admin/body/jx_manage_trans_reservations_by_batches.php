<?php
$msg_generate_pick_list = '';
$msg_process_by_fran = '';
$re_allot_all_block = '';
$generate_btn_link='';
$msg_process_by_fran='';
$ttl_trans_listed='';
$block_alloted_status = '';

$output='';
if($s!=0 and $e != 0) {
    $from=date("Y-m-d H:i:s",strtotime($s));
    $to=date("Y-m-d H:i:s",strtotime("23:59:59 $e"));
}else {
    $s=time()-60*60*24*7*4;
    $e=time();
     $from=date("Y-m-d H:i:s",$s);
     $to=date("Y-m-d H:i:s",$e); //("23:59:59 $e"));
}
if($this->erpm->auth(true,true)) {
    $batches_det = $this->reservations->get_batches_details(0);
}
else {
    $batches_det = $this->reservations->get_batches_details($user['userid']);
}

//echo $total_trans_rows.'<br><pre>';print_r($batches_det);echo '</pre>';
$total_trans_rows = count($batches_det);
if($total_trans_rows<=0) { ?>
    <h3 align="center">No assigned batches.</h3>
    <?php 
}
else {
    $ttl_trans_listed .= 'Showing <strong> '.$total_trans_rows.' </strong> batched process from <strong>'.date("m-d-Y",$s).'</strong> to <strong>'.date("m-d-Y",$e).'</strong> ';
    $batch_status=array(0=>'Pending',2=>'Partial',3=>'Cancelled');
    ?>
    <table class="datagrid" width="100%">
        <tr>
            <th width="15">#</th>
            <th width="150">Date</th>
            <th>Batch Number</th>
            <th>Group Menu</th>
            <th>Territory</th>
            <th width="60">No. Transactions</th>
            <th width="35">Packing Status</th>
            <th>Assigned to</th>
            <th>Action</th>
        </tr>
        <?php
        foreach ($batches_det as $i=>$batch_item) { ?>
        <tr>
            <td><?=++$i;?></td>
            <td><div class="str_time"><?=format_datetime($batch_item['created_on']);?></div></td>
            <td><?=$batch_item['batch_id'];?></td>
            <td><?=$batch_item['batch_grp_name'];?></td>
            <td><?=$batch_item['territory_name'];?></td>
            <td><?=$batch_item['num_orders'];?></td>
            <td><?=$batch_status[$batch_item['status']];?></td>
            <td align="center"><b><?=ucfirst($batch_item['assigned_to']); ?></b></td>
            <td>
                <a class="packthis button button-rounded button-tiny button-action" href="javascript:void(0)" batch_id="<?=$batch_item['batch_id'];?>">Pack This Batch</a>
                <a class="btn_picklist button button-rounded button-tiny button-primary" href="javascript:void(0)" onclick="picklist_product_wise(this,<?=$batch_item['batch_id'];?>)">Generate Product Pickslip</a>
                <a class="btn_picklist button button-rounded button-tiny button-primary" href="javascript:void(0)" onclick="picklist_fran_wise(this,<?=$batch_item['batch_id'];?>)">Franchise-wise Pick Slip</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <div style="display:none;" id="dlg_batch_order_list">
        <div id="batch_order_list_overview">
            <h3>Batch:#<span></span></h3>
        </div>
        <div id="batch_order_list">
        <table class='datagrid' width='100%'>
            <thead>
            <tr>
                    <th>#</th>
                    <th>Territory name</th>
                    <th>Town name</th>
                    <th>Franchise</th>
                    <th>Total Orders</th>
                    <th>Actions</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
        </div>
    </div>
<?php
    }
?>
<script>
    $(".level1_filters").hide();
    $(".ttl_trans_listed").html('<?=$ttl_trans_listed;?>');
    $(".btn_picklist_block").html('<?=$msg_generate_pick_list;?>');
    $(".batch_btn_link").html('<?=$generate_btn_link;?>');
    $(".re_allot_all_block").html('<?=$re_allot_all_block?>');
    $(".process_by_fran_link").html('<?=$msg_process_by_fran;?>');
    $(".block_alloted_status").hide();
    
    $("#dlg_batch_order_list").dialog({
        modal:true
        ,autoOpen:false
        ,height:670
        ,width:780
        ,position: ['center', 'center']
        ,open:function() {
            
            var dlt_elt=$(this);
            var batch_id=dlt_elt.data("batch_id");
                $("#batch_order_list_overview h3 span").text(batch_id);
            
                $("table tbody",dlt_elt).html("");
                dlt_elt.dialog("option","title","Batch franchise order list of #"+batch_id);
                
                $.post(site_url+'/admin/jx_get_batch_order_list/'+batch_id,{},function(resp) {
                    if(resp.status=='success') {
                        var html='';
                        $.each(resp.franchise_list,function(i,fran_det) {
                            html +="<tr>\n\
                                        <td>"+(++i)+"</td>\n\
                                        <td>"+(fran_det.territory_name)+"</td>\n\
                                        <td>"+(fran_det.town_name)+"</td>\n\
                                        <td>"+(fran_det.franchise_name)+"</td>\n\
                                        <td>"+(fran_det.num_orders)+"</td>\n\
                                        <td><a style='color:#ffffff;' class='packthis button button-rounded button-tiny button-action' href='"+site_url+"/admin/pack_invoice_by_fran/"+fran_det.batch_id+"/"+fran_det.franchise_id+"' target='_blank'>Pack This Batch</a></td>\n\
                                </tr>";
                            
                        });
                        $("table tbody",dlt_elt).html(html);
                    }
                    else {
                        $("table tbody",dlt_elt).html("<tr><td colspan='6' align='center'>"+resp.message+"</a>");
                        //dlt_elt.dialog("close");
                    }
                },"json");
        }
    });
    
    $(".packthis").click(function() {
        var batch_id = $(this).attr('batch_id');
        $("#dlg_batch_order_list").data("batch_id",batch_id).dialog("open");
    });
    
</script>
    