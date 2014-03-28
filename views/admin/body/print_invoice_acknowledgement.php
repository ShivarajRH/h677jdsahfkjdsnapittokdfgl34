<div class="container">
    <div style="width: 100%;margin: 0px auto">
		<div style="margin:10px;" class="hideinprint">
				<div style="float:right;"><input type="button" value="Print invoice acknowlegement" onclick='print_taxinv_acknowledgement(this)'></div>
		</div>
	</div>
    <div class="clear"></div>
    <div style="font-family: arial" id="grouped_tax_inv_ack_copy">
        <style>
            .leftcont { display: none; }
            table {
                    font-family:arial; font-size:12px;
            }
            .showinprint { display: none; }
            .tax_inv_ack_copy { page-break-after:always;font-family:arial; font-size: 12px; }
             .clear { clear: both;}
            @media print {
                .clear { clear: both;}
                .hideinprint { display: none; }
                .showinprint { display: block; }
                table {
                    font-family:arial; font-size:12px;
                }
                .tax_inv_ack_copy { page-break-after:always; font-family:arial; font-size: 12px; }
                
            }
        </style>
<?php 
        $list_invs_group_arr=array();

        $invs = $dispatch_det['invs'];
        $page_det_arr = $this->db->query("select f.franchise_id,f.territory_id,terr.territory_name,group_concat(distinct a.invoice_no) as invoice_no_str,b.bill_person,b.bill_address,b.bill_landmark,b.bill_city,b.bill_state,b.bill_pincode,bill_phone
				from king_invoice a 
				join king_orders b on a.order_id = b.id 
				join king_dealitems c on c.id = b.itemid
				join king_transactions d on d.transid = a.transid
				join pnh_m_franchise_info f on f.franchise_id = d.franchise_id
				join pnh_m_territory_info terr on terr.id=f.territory_id
				where a.invoice_no in ($invs) 
				group by f.franchise_id
				order by terr.territory_name asc")->result_array();
        
        foreach($page_det_arr as $order) {
                $ttl_inv_amt = 0;
//                echo '<pre>';print_r($order);echo '</pre>';die();
                $tm_info = $this->erpm->get_territory_managers($order['territory_id']);
                $tm_name_str='';
                if(!empty($tm_info)) {
                    foreach($tm_info as $tm) {

                        $tm_name_str .= '<div><a href="'.site_url("admin/view_employee/".$tm['employee_id']).'" target="_blank">'.ucfirst($tm['name']).'</a></div>';
                    }
                }
                else {
                    $tm_name_str = "--";
                }
                //get executives info
                $executive_info = $this->erpm->get_town_executives($order['territory_id']);
                $be_name_str='';
                if(!empty($tm_info)) {
                    foreach($executive_info as $be) {

                        $be_name_str .= '<span><a href="'.site_url("admin/view_employee/".$be['employee_id']).'" target="_blank">'.ucfirst($be['name']).'</a></span> &nbsp;';
                    }
                }
                else {
                    $be_name_str = "--";
                }
                
        ?>
        <div class="tax_inv_ack_copy" style="font-family:arial;">
<!--                <div style="border-bottom:1px solid #000;padding:5px;font-weight:bold;text-align:center;overflow: hidden;text-align: center; ">
                        Acknowledgement Copy 
                </div>-->
            
                <div class="clear">&nbsp;</div>
                
                    <table border=0 cellspacing=0 cellpadding=5 align="left">
                            <tr>
                                <td>Territory:</td>
                                <td width="150"><b><?=ucfirst($order['territory_name']); ?></b></td>
                            </tr>
                            <tr>
                                <td>Territory Manager:</td>
                                <td width="180"><b><?=$tm_name_str; ?></b></td>
                            </tr>
                            <tr>
                                <td>Executives:</td>
                                <td width="180"><b><?=$be_name_str; ?></b></td>
                            </tr>
                    </table>
            
                    <table border=0 cellspacing=0 cellpadding=5 align="right" width="">
                            <tr>
                                <td>From:</td>
                                <td width="60"><b><?=date("d/m/Y",(strtotime($sdate)))?></b></td>
                                <td>To:</td>
                                <td width="60"><b><?=date("d/m/Y",(strtotime($edate)))?></b></td>
                            </tr>
                            <tr>
                                <td colspan="4">By: &nbsp;<?=ucfirst($user['username']); ?></td>
                            </tr>
                    </table>
                    
                    <div class="clear">&nbsp;</div>
                    
                    <h4><?=ucfirst($order['bill_person'])?></h4>
                    
                    <div class="clear">&nbsp;</div>
                    
                    <table width="100%" cellpadding="5" cellspacing="0" border="1">
                            <tr>
                                    <th>No</th>
                                    <th>Item</th>
                                    <th width="70">Amount</th>
                                    <th width="40">Qty</th>
                                    <!--<th width="">Invoices</th>-->
                                    <th width="70">Total</th>
                            </tr>
                            <?php 
                            
//                      $invoice_list = explode(',',$invs); echo count($invoice_list); die();
                        $orderslist_byproduct = $this->db->query("select terr.territory_name,a.transid,a.createdon as invoiced_on,d.init,b.itemid,c.name,if(c.print_name,c.print_name,c.name) as print_name,c.pnh_id,group_concat(distinct a.invoice_no) as invs,
                                                                    ((a.mrp-(a.discount))) as amt,
                                                                    sum(a.invoice_qty) as qty 
                                                            from king_invoice a 
                                                            join king_orders b on a.order_id = b.id 
                                                            join king_dealitems c on c.id = b.itemid
                                                            join king_transactions d on d.transid = a.transid
                                                            join pnh_m_franchise_info f on f.franchise_id = d.franchise_id
                                                            join pnh_m_territory_info terr on terr.id=f.territory_id
                                                            where a.invoice_no in (".$order['invoice_no_str'].") 
                                                            group by itemid,amt
                                                            order by c.name")->result_array();
                                    //$order = $orderslist_byproduct[0];	
                    
                                    $k1=0;
                                    $list_invs_group='';
                                    foreach($orderslist_byproduct as $itm_ord) {
                                        $inv_amt =  $itm_ord['amt']*$itm_ord['qty'];
                                        
                                        $list_invs_group_arr[] = $itm_ord['invs'];
                            ?>
                            <tr class="row">
                                    <td><?= ++$k1; ?></td>
                                    <td>
                                            <span class="showinprint"><?php echo $itm_ord['print_name'].'-'.$itm_ord['pnh_id'];?></span>
                                            <span class="hideinprint"><?php echo $itm_ord['name'].'-'.$itm_ord['pnh_id'];?></span>
                                    </td>
                                    <td><?php echo $itm_ord['amt'];?></td>
                                    <td><?php echo $itm_ord['qty'];?></td>
                                    
                                    <?php /*
                                        $ind_invs = $itm_ord['invs'];
                                        echo '<td>'.str_replace(',',', ',$itm_ord['invs']).'</td>';
                                        //echo count($list_invs_group_arr);
                                    */?>
                                    
                                    <td><?php echo $inv_amt;?></td>
                            </tr>
                            <?php //$list_invs_group .= $itm_ord['invs'].',';
                                            $ttl_inv_amt +=  $inv_amt;
                                            
                                            
                                            
                                    }
                            ?>
                            <tr>
                                    <td colspan="4" align="right">
     
                                        <span  style="margin-right: 20px;">Total amount to be collected</span>
                                    </td>
                                    <td><b><?php echo format_price($ttl_inv_amt,2);?></b></td>
                            </tr>	
                    </table>
                    
                    <style>
                            body{font-family: arial;font-size: 12px;}
                            table{font-family: arial;font-size: 12px;}
                            table td{padding:3px;font-size: 11px}
                    </style>
                    
                    <?php
                                    $cond=''; 
                                            
                                            
                                    $trid=$order['territory_id'];
                                    $frid=$order['franchise_id'];
                                    $cond = '';
                                    if($trid)
                                            $cond .= ' and f.territory_id = '.$trid;
                                    if($frid)
                                            $cond .= ' and f.franchise_id = '.$frid;

                                    $output = array();

                                    $fr_imei_res = $this->db->query('SELECT t.franchise_id,inv.invoice_no,o.member_id,franchise_name,shipped_on,l.product_id,l.product_name,i.imei_no,o.imei_reimbursement_value_perunit as cr_amt,territory_name,town_name   
                                                                                                    FROM t_imei_no i 
                                                                                        Join king_orders o on o.id = i.order_id 
                                                                                        JOIN king_transactions t ON t.transid=o.transid
                                                                                        JOIN m_product_deal_link p ON p.itemid=o.itemid
                                                                                        JOIN m_product_info l ON l.product_id=p.product_id
                                                                                        JOIN king_invoice inv ON inv.order_id=o.id and inv.invoice_status = 1 
                                                                                        JOIN imei_m_scheme r ON r.id=o.imei_scheme_id
                                                                                        JOIN shipment_batch_process_invoice_link bi ON bi.invoice_no = inv.invoice_no 
                                                                                        join pnh_m_franchise_info f on f.franchise_id = t.franchise_id 
                                                                                        join pnh_m_territory_info ti on ti.id = f.territory_id
                                                                                        join pnh_towns tw on tw.id = f.town_id 
                                                                        WHERE o.status in (1,2) and o.imei_scheme_id > 0 and is_imei_activated = 0  and bi.shipped_on between ? and ? '.$cond.'
                                                                        group by franchise_id,i.id
                                                                        order by franchise_name ',array($sdate,$edate));

                                    if($fr_imei_res->num_rows())
                                    {
                                            $fr_imei_list = array();
                                            foreach($fr_imei_res->result_array() as $fr_imei_det)
                                            {
                                                    if(!isset($fr_imei_list[$fr_imei_det['franchise_id']]))
                                                    {
                                                            $fr_imei_list[$fr_imei_det['franchise_id']] = array('name'=>$fr_imei_det['franchise_name'],'imei'=>array());
                                                    }
                                                    array_push($fr_imei_list[$fr_imei_det['franchise_id']]['imei'],$fr_imei_det);
                                            }
                                            $op_str = '';
                                            foreach($fr_imei_list as $fr_id=>$fr_imei)
                                            {
                                                    $op_str .= '<div style="page-break-after:always">';
                                                    $op_str .= '<table width="100%" cellpadding="5" cellspacing="0" >';
                                                    $op_str .= '<tr><td colspan="3" align="center"> <h4>IMEI Activation Sheet</h4> </td></tr>';
                                                    $op_str .= '<tr>
                                                                                    <td width="30%" align="left">
                                                                                            Franchise : '.($fr_imei['name']).' <br>
                                                                                            Town : '.($fr_imei['imei'][0]['town_name']).' ('.$fr_imei['imei'][0]['territory_name'].') 
                                                                                    </td>
                                                                                    <td>&nbsp;</td>
                                                                                    <td width="30%" align="right">
                                                                                            Printed On : '.format_date(date('Y-m-d')).' 
                                                                                    </td>
                                                                            </tr>';
                                                    $op_str .= '<tr><td colspan="3">';
                                                    $op_str .= '<table width="100%" border=1 style="border-collapse:collapse">
                                                                                    <thead>
                                                                                            <th>Slno</th>
                                                                                            <th>Shipped Date</th>
                                                                                            <th>Invoice</th>
                                                                                            <th>Product Name</th>
                                                                                            <th>IMEINO</th>
                                                                                            <th>Credit (Rs)</th>
                                                                                            <th>MemberID</th>
                                                                                            <th>Customer Mobileno</th>
                                                                                            <th>Customer Name</th>
                                                                                    </thead>
                                                                                    <tbody>';
                                                    
                                                    foreach($fr_imei['imei']  as $i=>$imei_d)
                                                    {
                                                            $op_str .= '<tr>
                                                                                            <td width="20" style="text-align:right;height:20px;">'.($i+1).'</td>
                                                                                            <td width="60">'.format_date($imei_d['shipped_on']).'</td>
                                                                                            <td width="60">'.($imei_d['invoice_no']).'</td>
                                                                                            <td width="200">'.($imei_d['product_name']).'</td>
                                                                                            <td width="100">'.($imei_d['imei_no']).'</td>
                                                                                            <td width="40" style="text-align:right">'.($imei_d['cr_amt']).'</td>
                                                                                            <td width="100">'.($imei_d['member_id']).'</td>
                                                                                            <td width="150">&nbsp;</td>
                                                                                            <td width="150">&nbsp;</td>
                                                                                    </tr>';
                                                    }
                                                    $op_str .= '	<tbody>
                                                                            </table>';
                                                    $op_str .= '</tr>';
                                                    $op_str .= '</table>';
                                                    $op_str .= '</div>';
                                            }

                                            echo $op_str;

                                    }else
                                    {
                                           echo '<h5>No IMEIs activation.</h5>';
                                    }
                    
                    ?>
                    
                    <div class="showinprint" align="right" style="width:99%;">
                            <br>
                            <br>
                            <br>
                            <br>
                            <b style="font-size: 100%">Authorised Signatory</b>
                    </div>
                    
                </div>

<?php       }

       $list_invs_group_arr = array_unique($list_invs_group_arr);
       $list_invs_group_str = implode(',',$list_invs_group_arr);
       //$list_invs_arr = explode(",",$list_invs_group_str);echo ''.count($list_invs_arr);
?>
        <input type="hidden" name="all_inv_list" id="all_inv_list" value="<?=$list_invs_group_str; ?>"/>
        <input type="hidden" name="userid" id="userid" value="<?=$user['userid']; ?>"/>
        </div>
</div>
<script>
/*function print_taxinv_acknowledgement(ele){
        ele.value="RePrint Invoice Acknowledgement Copy";
        log_printcount();
        myWindow=window.open('','','width=950,height=600,scrollbars=yes,resizable=yes');
        myWindow.document.write($("#grouped_tax_inv_ack_copy").html());//+''+$("#customer_acknowlegment").html());
        myWindow.print();
}
function page_refresh(elt) {
    window.location.href=site_url+"admin/print_invoice_acknowledgementbydate";
}*/
//$(".print_link").click(function() {
    function print_taxinv_acknowledgement(ele){
        $('#grouped_tax_inv_ack_copy').printElement({
            printMode:"popup"
            ,pageTitle:"Acknowledgement Copy"
            ,leaveOpen:false
            /*,printBodyOptions: {
                styleToAdd:'padding:10px;margin:10px;color:#FFFFFF !important;',
                classNameToAdd : 'wrapper2'}*/
        });
        log_printcount();
    }


    function log_printcount() {
        
        var all_inv_list = $("#all_inv_list").val();
        var created_by = $("#userid").val();
        var status = 0;
        if(confirm("Is acknowledgement printed successfully?")) {
            status = 1;
        }
    
    /*$.each($(".terr_list"),function(i,row) {
        var territory_id = $(this).attr("territory_id");
        var tm_id = $("#tm_emp_id_"+territory_id).val();
        var be_id = $("#be_emp_id_"+territory_id).val();
        group_str_invs[i] = $("#str_invs_"+territory_id).val();
    });*/

        var postData = {all_inv_list :all_inv_list,created_by:created_by,status:status};
//        print(postData);return false;
        $.post(site_url+'admin/jx_update_acknowledge_print_log',postData,function(resp) {
            print(resp);
            if(resp.status == 'success') {
               $(".print_status").html("<br>"+resp.response+"<br>Log_id="+resp.log_id+"");
            }
            else {
                $(".print_status").html("<br>"+resp.response+"");
            }
        },'json');
    }

</script>