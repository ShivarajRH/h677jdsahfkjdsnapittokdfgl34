<div class="container">
<?php 
     //echo '<pre>';
    //print_r($insurance_det); 
//    die();
    $ins = $insurance_det[0];
    //echo '</pre>';
    ?>
    
    
    <div style="float:right;">
        <button onclick="print_preview();">Print</button>
    </div>
    
    <style>
        h2 { float: left;width: 65%; }
    table{
            font-size:12px;
    }
    .showinprint{
                    display: none;
    }
    .clear { clear:both; }
    @media print {
            .hideinprint{
                    display:none;
                    visibility: hidden;
            }
            .showinprint{
                    display: block;
            }
    }
 

    </style>
    
    <div class="clear">&nbsp;</div>
    <div id="insurance_block" class="insurance_block" style="padding: 10px; page-break-after: always;">
        <div style="font-family:arial;font-size:12px;">
            <h2>Insurance #<?=$insuranceid;?></h2>
            
            <div style="float:right;">
                <table cellpadding="5" cellspacing="2">
                    <tr>
                        <th>Created By:</th><td><?=ucfirst($ins['username']);?></td>
                        <th>Date:</th><td><?=$ins['created_on'];?></td>
                    </tr>
                </table> 
            </div>
            
            <table width="100%" cellspacing="0" cellpadding="5" border="1">
                <tr>
                    <td width="30%">Member Details:</td>
                    <td>
                        <?=$ins['first_name'];?> <?=$ins['last_name'];?>
                        <br><b>MID : </b><?=$ins['mid'];?>
                    </td>
                </tr>
                <tr>
                    <td>Insurance Product</td>
                    <td><?php 
                    $item_det = $this->db->query("select di.name as dealname,di.pnh_id,d.menuid,mn.name as menuname,d.brandid,b.name as brandname,d.catid,c.name as catname from king_dealitems di
                                                    join king_deals d on d.dealid=di.dealid
                                                    join pnh_menu mn on mn.id=d.menuid
                                                    join king_brands b on b.id = d.brandid
                                                    join king_categories c on c.id = d.catid
                                                    where di.id=?",$ins['itemid'])->row_array();
                    echo $item_det['dealname'];
                    ?>
                    </td>
                </tr>
                <tr>
                    <td>Insurance Address</td>
                    <td>
                        <?=$ins['first_name'];?> <?=$ins['last_name'];?>
                        <br>
                        <address><?=$ins['proof_address'];?>
                            <br><?=$ins['city'];?>
                            <br><?=$ins['pincode'];?>
                        </address>
                    </td>
                </tr>
                <tr>
                    <td>Franchise</td>
                    <td><?=$ins['franchise_name'];?></td>
                </tr>
                <tr>
                    <td>Transid</td>
                    <td><?=$ins['transid'];?></td>
                </tr>
                <tr>
                    <td>Menu Name</td>
                    <td><?=$item_det['menuname']; ?></td>
                </tr>
                <tr>
                    <td>Brand Name</td>
                    <td><?=$item_det['brandname'];?></td>
                </tr>
                <tr>
                    <td>Category Name</td>
                    <td><?=$item_det['catname'];?></td>
                </tr>
                <tr>
                    <td>Insurance Value</td>
                    <td>Rs. <?=$ins['insurance_value'];?> insurance value for Rs. <?=$ins['order_value'];?> item value.</td>
                </tr>
                <tr>
                    <td>Insurance Status</td>
                    <td><?php
                    $arr_offer_status = array(0=>"Insurance Not Processed",1=>"Ready to Process",2=>"Processed");
                    echo $arr_offer_status[$ins['process_status']];
                    
                            $arr_delivery_status = array(0=>"Not delivered",1=>"Order Delivered");
                            echo ", ".$arr_delivery_status[$ins['delivery_status']];
                    ?>
                        
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="block signature_default" style="">
                <br>
                <span style="margin:22px 0px 0px;float:right;"><b>Validated By</b> : _______________<br /></span><br />
                <span style="margin:7px 0px;float:left;;"><b>Processed By</b> : ___________________<br /></span>
        </div>
        <p> &nbsp;</p>
        
    </div>
    
</div>
<script>

function print_preview() {
    $('#insurance_block').printElement({
        printMode:"popup"
        ,pageTitle:"View Insurance"
        ,leaveOpen:false
        /*,printBodyOptions: { styleToAdd:'padding:10px;margin:10px;color:#FFFFFF !important;',classNameToAdd : 'wrapper2'}*/
    });
    log_printcount();
}

function log_printcount()
{
    /*var batch_id = $("#batch_id").val();
    $.post(site_url+'/admin/jx_update_picklist_print_log','batch_id='+batch_id,function(resp){ 
        if(resp.status == 'success') {
            $(".print_count_blk").html(resp.printcount+" times printed.");
        }
        else {
            alert(resp.response+"");
        }
    },'json');*/
}
</script>