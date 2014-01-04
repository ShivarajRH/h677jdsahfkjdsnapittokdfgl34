<!--Pack only one invoice, invoice number by get url-->
<style>
.leftcont {display: none;}
#scanned_summ{
	width: 224px;background: tomato;border-top:5px solid #FFF;
	text-align: center;
	color: #FFF;
	font-size: 32px;
}
#scanned_summ h3{
	font-size: 20px;margin-top:10px;margin-bottom: 0px;
}
h2 { width: 178px; float: right; color: #020205; margin-right: 272px; }
h3 { font-size: 18px; }
.scanned_summ_total{
	padding:5px;
}
.scanned_summ_stats{
	padding:5px;font-size: 15px;font-weight: bold;text-align: left;border-bottom: 1px dotted #FFF;
}
.ttl_num{
	float: right;font-size: 18px;
}
.have { }
.highlight {
    background: yellow !important;
    padding: 22px; 
    font-size: 16px;
    text-align: center;
    float: left;
    font-weight: bold;
    margin-left: 38px;
}
.packing_status {
    margin: 6px 6px; font-weight: bold; text-align: center;
}
.scanned {
	background: #aaa;
	font-size: 110%;
}

.scanned .have {
	background: #f55 !important;
	color: #fff;
	font-size: 170%;
}
.partial {
	background: orange !important;
}

.disabled {
	background: #aaa !important;
	color: #FFF !important;
}
.mrp_block {
    margin: 5px 0 0 2px; color: #A22D2D;
}
.datagrid {margin-top: 20px;float: left;}

.datagrid th {
/*    background: #443266;
    color: #C3C3E5;*/
    background: #C3C3E5;
    color: #130C09;
}
.subdatagrid th {
    padding: 4px 0 2px 4px !important;
    font-size: 11px !important;
    color: #130C09 !important;
    background-color: rgba(112, 100, 151, 0.51) !important;
    vertical-align: middle; width: 30px !important; padding: 2px 4px !important;
}
.right_container {
    float: right;
    top: 223px;right:10px;position: fixed;
}
.left_container { display:table;float: left; width: 82%; }

.img_block { width:120px;float: left; }
.product_title a { font-size: 17px; text-decoration: none; font-weight: bold; color: #443266; }
.imei_inp_txt1,.imei_inp_txt2 {
    color: #888888;
    background-color: yellow;
    font-size: 13px;
    font-weight: bold;
    padding: 3px 5px;
}
.imei_inp_txt1 {
    background-color: transparent;
    color: #130C09;
}
.process_btn { margin:75px 90px 0 0;float: right; }
.process_btn input{ padding: 18px 20px; }

.scan_barcode_block { margin: 77px 0 14px 0; padding: 5px 10px; background:#F7F5FC;  font-weight: bold;}
.scanbyimei_block { margin: 5px 0 5px 0; padding: 5px 10px; background:#F7F5FC;font-weight: bold; display:none; }

.refund_block { text-align: center;font-size: 9px; }
.refund_amt_block { width: 60px; text-align: center; }
.reserv_qty_summ {width: 85px; color: red; font-size: 12px; }
.scan_proditems { width: 30px; text-align: center; }
.stock_info_block { padding: 0px; margin: 0px; font-size: 85%; }
.note_block { padding: 5px; background: #ffffd0; padding: 5px; font-size: 12px;line-height: 20px; }
</style>
<div class="container">
<?php 
    $p_invno_list = array();
    $prod_imei_list = array();
    $batch_id = $this->reservations->get_batch_id_by_invoiceno($invoice[0]['p_invoice_no']);
    $p_inv_no = $invoice[0]['p_invoice_no']; 

	$t_order_det = $this->reservations->get_order_transaction($invoice[0]['order_id']);
        
	if($t_order_det['partner_id']) 
        {
                $order_placed_by = $this->reservations->get_partner_name($t_order_det['partner_id']);
        }else 
        {
                if($t_order_det['is_pnh'])
                {
                                $order_placed_by = 'Paynearhome';
                }else
                {
                        $order_placed_by = 'Snapittoday';
                }
        }
    if($t_order_det['is_pnh']) { ?>
			<h2>Scan &amp; Pack </h2>
    <?php }else { ?>
                <h2>Scan &amp; pack proforma invoice : <?=$p_inv_no; ?></h2>
    <?php } ?>

    <span>
	<?php $heading_fran_text = '';
		if($t_order_det['is_pnh'])
		{
			$fr_det = $this->reservations->get_franchise_details($t_order_det['franchise_id']);
			$heading_fran_text .=  '<h3>'.$fr_det['franchise_name'].'</h3>'.''.$fr_det['town_name'].', '.$fr_det['territory_name'];
		}else 
		{ ?>
			<b>Trans#</b> : <?php echo $t_order_det['transid']; ?> &nbsp;
			<b>OrderedOn</b> : <?php echo format_datetime(date('Y-m-d H:i:s',$t_order_det['init'])); ?> &nbsp;
			<b>Ship Details</b> : <?php echo $t_order_det['ship_person'].', '.$t_order_det['ship_city']; ?> &nbsp;
        <?php   }
            echo $heading_fran_text; ?>
    </span>
	
                        
<div class="clear"></div>
<form action="" name="pack_invoice_form" method="post">
<div class="left_container">
    <table class="datagrid" width="100%">
	<thead>
            <tr>
                    <th width="20">#</th>
                    <th width="120">Deal Picture</th>
                    <th width="150">Trans ID</th>
                    <?php //echo ($mlt)?'<th width="5%">Transactions details</th>':'';?>
                    <th width="450">Product name</th>
                    <th width="150">Required &AMP; Scanned Qty</th>
<!--			<th>MRP</th><th>Order MRP</th><th>Status</th<th>Deal</th>-->
                    <th>
                        <div>Stock MRPs</div>
<!--			    <table class="subgrid" cellpadding="0" cellspacing="0" style="border: 0px !important; width: 100%; background: #fcfcfc !important; font-size: 11px;">
                            <tr><td style="background: #fcfcfc !important; vertical-align: middle; color: #000"><div style="width: 60px; text-align: center;">MRP</div></td>
                                    <td style="background: #fcfcfc !important; vertical-align: middle; color: #000; text-align: center;"><div style="width: 30px;">Stock</div></td>
                                    <td style="background: #fcfcfc !important; vertical-align: middle; color: #000; text-align: center;"><div style="width: 60px; text-align: center;">Suggest</div></td>
                                    <td style="background: #fcfcfc !important; vertical-align: middle; width: 30px !important; color: #000" width="10"><div style="width: 30px; text-align: center;">Scan</div></td></tr>
                        </table>-->
                    </th>
                    <th width="60">
                            <div class="refund_block"><span>Refund Amount</span></div>
                    </th>
            </tr>
	</thead>
	<tbody>
        <?php
        $sindx = 1;
        $has_imei_scan = 0;

        foreach($invoice as $i)
        {
                        array_push($p_invno_list,$i['p_invoice_no']);
                        $consider_for_refund = 0;
                        
                        if($i['is_pnh'])
                            $consider_for_refund = $this->reservations->is_menu_mrp_changed($i['menuid']);
                        
                        $p_has_imei_scan = $this->reservations->is_product_have_serial($i['product_id']);
                        
                        $has_imei_scan += $p_has_imei_scan;
		?>
		<tr class="bars bar<?=$i['barcode']?> prod_scan">
			<td valign="middle">
				<input type="hidden" name="p_invno[]" value="<?php echo $i['p_invoice_no']?>" /> <b><?=$sindx++;?></b>
			</td>
			<td><div class="img_block"><a target="_blank" href="<?php echo IMAGES_URL.'/items/big/'.$i['pic'].'.jpg'?>"><img width="100%" src="<?php echo IMAGES_URL.'/items/small/'.$i['pic'].'.jpg'?>" /></a></div></td>
                        <td>
                            <a href="<?=site_url("admin/trans/".$i['transid']);?>" target="_blank"><?=$i['transid'];?></a>
                            
                            <div><br><b>Ordered On:</b></div>
                            <div><?=date("d/m/Y",$i['time']);?></div>
                        </td>
			<?php echo ($mlt)?'<td>'.anchor('admin/proforma_invoice/'.$i['p_invoice_no'],$i['p_invoice_no'],'target=_"blank"').'<br>'.anchor('admin/trans/'.$i['transid'],$i['transid'],"target=_'blank'").'</td>':'';?>
			
			<td class="prod"><input type="hidden" class="pid" value="<?=$i['product_id']?>">  
                            <div class="product_title"><a href="<?=site_url('admin/product/'.$i['product_id'])?>" target="_blank"><?=$i['product_name']?></a></div>
                                <div class="mrp_block">
                                    <span class="small">MRP: <b><?=(double)$i['mrp']?></b></span> &nbsp;&nbsp;
                                    <span class="small">Order MRP: <b><?=$i['order_mrp']?></b></span>
                                </div>
                        </td>
                        <td style="vertical-align: middle;" align="center">
                            <div class="highlight">
                                <span class="qty prod_req_qty"><?=$i['qty']?></span><span> / </span> <span class="have">0</span>
                            </div>
                            <div class="clear"></div>
                            <span class="packing_status">PENDING</span>
                            
                            <?php /*<td><?php //$i['deal']?></td><td><?=(double)$i['mrp']?></td><td class="ord_mrp"><?=$i['order_mrp']?></td>*/ ?>
                        </td>
			<td>
                            <div class="stock_info_block">

                            <?php 
                            $prd_id = $i['product_id'];  
                            $mrp_stock_det = array();
                            $packinfo = $this->reservations->get_paking_info($prd_id);
                        
                            foreach($packinfo as $s)
                            {
                                    if(!isset($mrp_stock_det[$s['mrp'].'_'.$s['rbid']]))
                                            $mrp_stock_det[$s['mrp'].'_'.$s['rbid']] = array('pid'=>$prd_id,'stk'=>0,'det'=>array());

                                    $mrp_stock_det[$s['mrp'].'_'.$s['rbid']]['stk']+=$s['s'];

                                    $mrp_alloted_qty = 0;
                                    $rb_name = '';
                                    //$mrp_alloted_res = $this->db->query("select rack_name,bin_name,a.qty from t_reserved_batch_stock a join t_stock_info b on a.stock_info_id = b.stock_id join m_rack_bin_info c on c.id = b.rack_bin_id where a.batch_id = ? and a.order_id = ? and a.product_id = ? and b.stock_id = ? ",array($batch_id,$i['order_id'],$i['product_id'],$s['stock_id']));

                                    $mrp_alloted_res = $this->reservations->get_mrp_alloted($batch_id,$i['order_id'],$i['product_id'],$i['p_invoice_no'],$s['stock_id']);

                                    if($mrp_alloted_res->num_rows())
                                    {
                                            $reserv_res = $this->reservations->get_stock_reservation_details($i['order_id'],$i['p_invoice_no'],$s['stock_id']);
                                            if($reserv_res->num_rows())
                                            {
                                                    $mrp_alloted_qty = $reserv_res->row()->qty;
                                                    $rb_name = $mrp_alloted_res->row()->rack_name.'-'.$mrp_alloted_res->row()->bin_name;
                                            }else
                                            {
                                                    if($i['mrp'] == $s['mrp'])
                                                    {
                                                            $mrp_alloted_qty = 0;
                                                            $rb_name = $mrp_alloted_res->row()->rack_name.'-'.$mrp_alloted_res->row()->bin_name;
                                                    }
                                            }
                                    }else{ 
                                            if(!$this->reservations->get_reserved_stock_orders($i['order_id'],$i['p_invoice_no']))
                                            {
                                                    $mrp_alloted_qty = $i['qty'];
                                            }
                                    }
                                    array_push($mrp_stock_det[$s['mrp'].'_'.$s['rbid']]['det'],array($s['product_barcode'],$s['s'],$s['location_id'],$s['rack_bin_id'],'reserv_qty'=>$mrp_alloted_qty,'rb_name'=>$rb_name,'stock_id'=>$s['stock_id']));
                            }
			
                            $stk_i=0;
                            foreach($mrp_stock_det as $mrp_rb=>$mrp_list){
                                    list($mrp,$l_rb_id) = explode('_',$mrp_rb);
                                    $reserv_qty_summ = '';
                                    $ttl_reserved_qty = 0;
                                    foreach($mrp_list['det'] as $mrp_b)
                                    {
                                            if($mrp_b['reserv_qty'])
                                            {
                                                    $reserv_qty_summ .= '<div><b>'.$mrp_b['reserv_qty'].'</b><span style="font-size:8px;">('.$mrp_b['rb_name'].')</span></div>';
                                                    $ttl_reserved_qty += $mrp_b['reserv_qty'];
                                            }
                                    }
                                    if(!round($mrp_list['stk']+$ttl_reserved_qty))
                                            continue;

                                    // IMEI Code:
                                    $imeis=$this->reservations->get_imeis_by_product($i['product_id']);
                                    //echo '<pre>';print_r($imeis);die();
                                    if($p_has_imei_scan)
                                    {
                                            // prepare imeino list for allotment 
                                            foreach($imeis as $im)
                                                    $prod_imei_list[$im['imei_no']] = array($i['product_id'],$im['stock_id']);

                                            echo '<ol class="imei_inp_list">';
                                            for($p=0;$p<$i['qty'];$p++)
                                            {
                            ?>
                                                    <li><span class="imei_inp_txt1">IMEI Number : </span>
                                                        <!--<span class="imei_inp_txt2">7824389572893748</span>-->
                                                        <input type="text" readonly="readonly" 
                                                               itemid="<?php echo $i['itemid'] ?>" 
                                                               order_id="<?php echo $i['order_id'];?>" 
                                                               p_invno="<?php echo $i['p_invoice_no']?>" 
                                                               class="imei<?=$i['product_id']?> imei<?=$i['product_id']?>_unscanned imeis imei_p<?=$p?>" 
                                                               value="" style="width: 100px;padding:2px;font-size: 9px" /> 
                                                    </li>
                            <?php           }
                                            echo '</ol>';
                                    } ?>

                            <table class="subdatagrid" cellpadding="0" cellspacing="0" style="border: 0px !important; width: 100%; background: #f9f9f9; font-size: 13px;">
                                <tr>
					<th style="vertical-align: middle; text-align: center;" width="60">MRP</th>
					<th style="vertical-align: middle; text-align: center;" width="30">Stock</th>
					<th style="vertical-align: middle;" width="30" align="center">Suggest</th>
                                        <th>Scan</th>
				</tr>
                                <tr>
					<td style="vertical-align: middle;" align="center"><?php echo round($mrp,2);?></td>
					
                                        <td style="vertical-align: middle; text-align: center;">
                                            <b><?=round($mrp_list['stk']+$ttl_reserved_qty)?></b>
					</td>

					<td style="vertical-align: middle; text-align: center;">
                                            <div class="reserv_qty_summ"><?php echo $reserv_qty_summ;?></div>
					</td>
                                        
					<td style="vertical-align: middle; text-align: center;">
                                            <div class="scan_proditems">
                                                <?php
                                                $show_add_btn = 0;
                                                $has_reserv_bc_qty = 0;
                                                foreach($mrp_list['det'] as $mrp_b)
                                                {
                                                        if(!($mrp_b[1]+$mrp_b['reserv_qty']))
                                                            continue;

                                                        $show_add_btn += (strlen($mrp_b[0])==0)?1:0;
                                                        $scan_by_bc = 0;
                                                        if(strlen($mrp_b[0]))
                                                        {
                                                                $has_reserv_bc_qty += 1;
                                                                $scan_by_bc = 1;
                                                        }
                                                        ?>
                                                        <input rb_id="<?php echo $mrp_b[2].'_'.$mrp_b[3]?>" rb_name="<?php echo $mrp_b['rb_name']?>"
                                                                dealname="<?php echo addslashes($i['deal'])?>" 
                                                                itemid="<?php echo $i['itemid']?>"
                                                                p_invno="<?php echo $i['p_invoice_no']?>"
                                                                order_id="<?php echo $i['order_id']?>" 
                                                                consider_for_refund="<?php echo $consider_for_refund;?>"
                                                                disc="<?php echo $i['discount']?>"
                                                                ordmrp="<?php echo $i['order_mrp'];?>"
                                                                stk_info_id="<?php echo $mrp_b['stock_id'] ?>"
                                                                mrp="<?php echo $mrp ?>"
                                                                reserv_qty = "<?php echo $mrp_b['reserv_qty'] ?>" 
                                                                stk="<?php echo $mrp_b[1]+$mrp_b['reserv_qty'];?>" type="hidden"
                                                                pid="<?php echo $prd_id;?>"
                                                                name="pbc[<?php echo $i['p_invoice_no'].'_'.$i['itemid'].'_'.$prd_id.'_'.($mrp_b[0]?$mrp_b[0]:'BLANK').'_'.$mrp_b['stock_id'].'_'.$i['order_id'];?>]" value="0"
                                                                class="scan_proditem <?php echo $scan_by_bc?'scan_bybc':'' ?> pbcode_<?php echo $mrp_b[0]?$mrp_b[0]:$stk_i.'_nobc' ?> pbcode_<?php echo $mrp_b[0]?$mrp_b[0]:$stk_i.'_nobc' ?>_<?php echo (double)$mrp;?>_<?php echo $mrp_b[2].'_'.$mrp_b[3];?> pbcode_<?php echo $mrp_b[0]?$mrp_b[0]:$stk_i.'_nobc' ?>_<?php echo (double)$mrp;?>_<?php echo $mrp_b[2].'_'.$mrp_b[3];?>_<?php echo $mrp_b['stock_id'];?>_<?php echo $i['itemid'];?>_<?php echo $i['order_id'];?>"
                                                                style="width: 20px !important;" />

                                                        <lable><?php echo $mrp_b[0]?$mrp_b[0]:$stk_i.'_nobc' ?></lable>
                                                        <?php 		
                                                }
                                                ?>
                                                        <input mrp="<?php echo $mrp ?>" stk_i="<?php echo $stk_i;?>"
                                                                itemid="<?php echo $i['itemid']?>" 
                                                                p_invno="<?php echo $i['p_invoice_no']?>"
                                                                pid="<?php echo $prd_id;?>"
                                                                title="Scan to update via barcode or click here"
                                                                class="prod_stkselprev <?php echo !$show_add_btn?'disabled':"";?>"
                                                                ttl_stk="<?php echo $mrp_list['stk'];?>"
                                                                onclick="upd_selprodstk(this)" type="button"
                                                        <?php // echo !$show_add_btn?'disabled':"";?> value="0">
                                            </div>
                                        </td>
                                    </tr>
                            </table>
                            <?php } ?>
                    </div>
                </td>
                <td style="vertical-align: middle; color: #000" width="10">
                    <div class="refund_amt_block"><b class="refund_amt">0</b></div>
                </td>
		<?php /*if(empty($i['barcode'])){?>
                <td><input type="button" value="+" class="nobarcode"></td>
                <?php }*/?>
            </tr>
        <?php } ?>
	</tbody>
    </table>
    <!--<div style="margin-top: 20px;"><input type="button" value="Check" style="padding: 7px 10px;" onclick='checknprompt()'><input type="button" value="Process Invoice" style="float: right; padding: 7px 10px;" onclick='process_invoice(1);'> --> 
    </div>
    <!-- End of LEFT container -->
    
    <?php $p_invno_list = array_unique($p_invno_list);    ?>
    
    <div class="right_container">
                <div id="scanned_summ" >
                        <h3>Scanned Qty</h3>
                        <div class="scanned_summ_total"><span id="summ_scanned_ttl_qty">0</span> / <span id="summ_ttl_qty">0</span></div>
                        <div class="scanned_summ_stats"><span style="font-size: 13px;">Products </span> : <span class="ttl_num" id="summ_ttl_scanned_prod">0</span></div>	
                </div>
                <div class="scan_barcode_block"><!--position: fixed; top: 406px; right: 10px;-->
                    <label for="scan_barcode">Scan Barcode :</label><br>
                    <input class="inp" id="scan_barcode" style="padding: 5px;"> <input type="button" value="Go" onclick='validate_barcode()'>
                </div>
                <div class="scanbyimei_block" id="scanbyimei"><!--position: fixed; top: 306px; right: 10px;-->
                    <label for="scan_imeino">Scan Imeino :</label><br>
                    <input class="inp" id="scan_imeino" style="padding: 5px;"> <input type="button" value="Go" onclick='validate_imeino()'>
                </div>
                
                <div class="process_btn">
                        <input type="button" value="Process Invoice" onclick='process_invoice(1);'/>
                </div>
                <div class="clear"></div>
                <div>
                    <table cellpadding="4" cellspacing="0">
                            <tr>
                                    <td valign="top">
                                                <h4 style="margin: 2px 0px;"></h4>
                                                <table class="datagrid">
                                                        <tr>
                                                                <th>Free Samples with this order</th>
                                                        </tr>
                                                        <?php 
                                                            foreach($invoice as $i)
                                                            {
                                                                    $samps = $this->reservations->get_free_samples($i['p_invoice_no']);
                                                                    //$samps=array();$samps[]=array('name'=>'himalaya _shampoo','id'=>2); 
                                                                    if(empty($samps)) { 
                                                                       /* ?><tr><td colspan="100%">No free samples ordered</td></tr><?php */
                                                                    } 
                                                                    foreach($samps as $s) { ?>
                                                                    <tr>
                                                                            <td class="free-samples-data"><?=$s['name']?></td>
                                                                    </tr>
                                                                    <?php 
                                                                    }
                                                            }
                                                        ?>
                                                </table>
                                    </td>
                            </tr>
                            <tr>
                                    <td valign="top">
                                        <h4 style="margin: 2px 0px;">Transaction Notes</h4>
                                        <div class="note_block">
                                            <?php 
                                            $note_msg=''; 
                                            $limit=16;
                                            $user_note_arr=$this->reservations->get_transaction_notes(implode(",",$p_invno_list));
                                            
                                            foreach($user_note_arr as $user_msg) {
                                                $note_a = ucfirst(str_replace('User Note:','',$user_msg['note']));
                                                
                                                $note_short = (strlen($note_a) >= $limit) ? substr($note_a,0,$limit).'...' : $note_a;
                                                
                                                $note_msg .= '<span title="'.$note_a.'">'.$user_msg['transid']." - <b>".$note_short.'</b><br></span>';
                                            }
                                            echo $note_msg;
                                            ?>
                                        </div>
                                    </td>
                            </tr>
                    </table>
                </div>
        </div>
    </form>

        <div id="mutiple_mrp_barcodes" title="Choose Stock from Multiple Mrps">
            <div id="bc_mrp_list">
                    <table class="subdatagrid" cellpadding="0" cellspacing="0">
                            <thead>
                                <th><b>Deal</b></th>	
                                <th><b>MRP</b></th>
                                <th><b>RackBin</b></th>
                                <th><b>Reserved Qty</b></th>
                                <th>&nbsp;</th>
                            </thead>
                            <tbody></tbody>
                    </table>
            </div>
        </div>

        <div id="freesample_list_dlg" title="Free Samples with this order">
                <table class="subdatagrid" width="100%">
                        <thead>
                                <tr>
                                        <th>Free Samples</th>
                                        <th align="center" style="text-align: center;"><input type="checkbox" id="fs_check_list_all"></th>
                                </tr>
                        </thead>
                        <tbody>
                                <?php //$samps=$this->db->query("select f.name from proforma_invoices i join king_freesamples_order o on o.transid=i.transid join king_freesamples f on f.id=o.fsid where i.p_invoice_no=? order by f.name",$i['p_invoice_no'])->result_array();
                                        if($samps)
                                                foreach($samps as $s)
                                                {
                                ?>
                                                <tr>
                                                        <td><?=$s['name']?></td>
                                                        <td align="center">
                                                                <?php if($s['invoice_no'])
                                                                        {
                                                                                echo $s['invoice_no'];
                                                                        }else{
                                                                ?>
                                                                        <input type="checkbox"  name="fs_ids" value="<?php echo $s['id'];?>" class="sel fs_ids">
                                                                <?php }?>
                                                        </td>
                                                </tr>
                                <?php 
                                                }
                                ?>
                        </tbody>
                </table>
        </div>
    
</div>

<script type="text/javascript">
    $('.scan_proditems').each(function() {
            var ttl_stkgrp_items = $('.scan_proditem',this).length;
            var ttl_scanbybc= $('.scan_bybc',this).length;
            if(ttl_scanbybc)
            {
                    $('.prod_stkselprev',this).attr('disabled',true).addClass('disabled');
            }else
            {
                    $('.prod_stkselprev',this).attr('disabled',false).removeClass('disabled');
            }
    });

	var is_fs_confimed = 0;
	
	var prod_imeino_list = new Array();
	var prod_imeino_stock_det = new Array();
	<?php
		if($prod_imei_list && 0)
			foreach($prod_imei_list as $p_imeino => $i_imei_prod_det)
			{
	?>
				prod_imeino_list["<?=$p_imeino;?>"] = <?= $i_imei_prod_det[0];?>;
				prod_imeino_stock_det["<?=$p_imeino;?>"] = <?= $i_imei_prod_det[1]*1;?>;
	<?php				
			} 
	?>
	
        var summ_ttl_qty = 0;
        $('.prod_req_qty').each(function(){
                summ_ttl_qty += $(this).text()*1;
        });
        $('#summ_ttl_qty').text(summ_ttl_qty);
        
        function show_ttl_summary()
        {
                $('#summ_ttl_scanned_prod').text(0);
                var ttl_prods_scanned = 0;
                var ttl_qty_scan = 0; 

                $('#summ_ttl_scanned_prod').text((ttl_prods_scanned)+'/'+($('.prod_scan').length));

                $('.prod_scan').each(function(){
                            var qty_scan = 0;
                            $('.prod_stkselprev',this).each(function(){
                                    qty_scan += $(this).val()*1;
                            });

                            if(qty_scan)
                            {
                                    ttl_prods_scanned++;
                                    $('#summ_ttl_scanned_prod').text((ttl_prods_scanned)+'/'+($('.prod_scan').length));
                            }

                            ttl_qty_scan+= qty_scan;
                });

                $('#summ_scanned_ttl_qty').text(ttl_qty_scan);

        }
        show_ttl_summary();

        var refund_alert = 0;

        function upd_selprodstk(ele)
        {
                var stat = 0;
                var pbcodes = $(ele).parent().find('nopbcode');
                itm_id = $(ele).attr('itemid');
                mrp = $(ele).attr('mrp');
                stk_i = $(ele).attr('stk_i');
                sel_bcstk_ele = $(ele).parent().find('.pbcode_'+stk_i+'_nobc');
                p=sel_bcstk_ele.parents('tr:eq(1)');

                $("#scan_barcode").val("");
                if(p.length==0)
                {
                        alert("The product is not in invoice.");
                        return;
                }

                needed=parseInt($(".qty",p).html());
                have=parseInt($(".have",p).html());
                if(needed<=have)
                {
                        alert("Required qty is already scanned");
                        return;
                }

                var ttl_bc_stk = sel_bcstk_ele.attr('stk');
                var cur_sel = sel_bcstk_ele.val()*1;

                if(ttl_bc_stk < cur_sel+1)
                {
                        alert("No Stock Available for this product");
                        return false;
                }

                if((ttl_bc_stk-(cur_sel+1)) == 0)
                {
                        sel_bcstk_ele.removeClass('scan_bybc');
                }

                var ttl_stkgrp_items = $('.scan_proditem',sel_bcstk_ele.parent()).length;
                var ttl_scanbybc = $('.scan_bybc',sel_bcstk_ele.parent()).length;

                if(ttl_scanbybc)
                {
                        $('.prod_stkselprev',sel_bcstk_ele.parent()).attr('disabled',true).addClass('disabled');
                }else
                {
                        $('.prod_stkselprev',sel_bcstk_ele.parent()).attr('disabled',false).removeClass('disabled');
                }

                        sel_bcstk_ele.val(cur_sel+1);
                        sel_bcstk_ele.addClass("sel_stk");

                        var sel_bcstk_preview_ele = sel_bcstk_ele.parent().find('.prod_stkselprev');
                                sel_bcstk_preview_ele.val(sel_bcstk_preview_ele.val()*1+1);


                validate_item(p);		
        }

        function process_invoice(pmp)
        {
                var fs_id_list_arr = [];
                var fs_id_list='';
                if($(".free-samples-data").length)
                {
                        if(!is_fs_confimed)
                        {
                                $('#freesample_list_dlg').dialog('open');
                                return false;
                        }

                        $('.fs_ids:checked').each(function(i){
                            fs_id_list_arr[i] = $(this).val();
                        });

                        if(fs_id_list_arr)
                        {
                                fs_id_list=fs_id_list_arr.join(',');
                        }
              }

                if(pmp==1 && !confirm("Are you sure want to partially process this proforma invoice?"))
                        return;
             

                if(done_pids.length==0)
                {
                        alert("No products were cleared to pack. Invoice can't be empty");
                        return;
                }

                f=true;

                $("tr.done .imeis").each(function(){
                        if($(this).val()==0)
                        {
                                f=false;
                                alert("Error:\n All electronic items require a serial number");
                                //alert("One of the serial No is not selected. Serial nos are mandatory to select against quantity");
                                return false;
                        }
                });
                
                if(f==false) return;

                $(".imeip1").each(function() {
                        p=$($(this).parents("tr").get(0));
                        if($(".imeis",p).length<1)
                                return;
                        var imeis=[];

                        $(".imeis",p).each(function(){
                                if($.inArray($(this).val(),imeis)!=-1)
                                {
                                        f=false;
                                        alert("Duplicate serial nos! Check serial nos");
                                        return false;
                                }
                                imeis.push($(this).val());
                        });

                        if(f==false)
                                return false;
                });

                if(f==false) return;
                
                
                // =======================================================
//                print("Submit disabled"); return false;
                // =======================================================
                
                imei_payload="";
                for(i=0;i<done_pids.length;i++)
                {
                        if($("tr.done .imei"+done_pids[i]).length!=0)
                                {
                                        $(".imei"+done_pids[i]).each(function(){
                                                imei_payload=imei_payload+'<input type="hidden" name="imei_'+$(this).attr('p_invno')+'_'+done_pids[i]+'[]" value="'+$(this).val()+'">';
                                        });
                                }
                }

                var sel_pids = new Array();
                var sel_stk_inps = '';
                $('tr.done .sel_stk').each(function(){
                        sel_stk_inps += '<input type="hidden" name="'+$(this).attr("name")+'"  value="'+$(this).val()+'_'+$(this).attr("mrp")+'_'+$(this).attr("stk_info_id")+'" >';
                        sel_pids.push($(this).attr('p_invno')+'_'+$(this).attr('pid')); 
                });

                if(!sel_stk_inps)
                {
                        alert("Problem in process");
                        return false;
                }	

                msg='<form id="packform" method="post"><?php $r=rand(303,34243234);?><input type="hidden" name="fs_ids" value="'+fs_id_list+'"><input type="hidden" name="pids" value="'+sel_pids.join(",")+'"<input type="hidden" name="pass" value="<?=md5("$r {$invoice[0]['p_invoice_no']} svs snp33tdy")?>">	<input type="hidden" name="key" value="<?=$r?>"><input type="hidden" name="invoice" value="<?=implode(',',$p_invno_list)?>">'+imei_payload+' '+sel_stk_inps+'	</form>';
                $(".container").append(msg);

                if(confirm("Confirm if Free samples for order has been added for packing ? "))
                {
                        $("#packform").submit();
                }
        }

        function checkall()
        {
                var f=true;
                $(".bars").each(function(){
                        p=$(this);
                        prod=$(".prod",p).html();
                        needed=parseInt($(".qty",p).html());
                        have=parseInt($(".have",p).html());
                        if(needed!=have)
                        {
                                f=false;
                                return false;
                        }
                });

                if(f==true)
                {
                        var proceed = 1;
                                if(refund_alert)
                                        if(!confirm("Did you check refund amount ?"))
                                                proceed = 0;		
                        if(proceed)
                                process_invoice(0);
                }

                return f;
        }

        function checknprompt()
        {
                if(checkall()==false)
                alert("'"+prod+"' is insufficient");
        }
	
        function validate_imeino()
        {
                if($("#scan_imeino").val().length==0)
                {
                        alert("Enter IMEI no");
                        return;
                }
                var s_imei = $.trim($("#scan_imeino").val());

                // check if valid imei no 
                if(1)
                {
                                if(prod_imeino_list[s_imei] == 0)
                                {
                                                alert("Imei no already alloted ");
                                                return false;
                                }else if(prod_imeino_list[s_imei] == undefined)
                                {


                                        //alert(prod_imeino_stock_det[s_imei]);
                                        $.post(site_url+'/admin/jx_get_imei_stockdet','imei='+s_imei,function(resp){
                                                if(resp.status == 'success')
                                                {	

                                                        var i_prod_id = resp.stk.product_id;

                                                        // allot imeino to pending list
                                                        var ttl_imeireq = $('.imei'+i_prod_id).length; 
                                                        var ttl_imeiscanned = $('.imei'+i_prod_id+'_scanned').length;
//                                                        print("required = "+ttl_imeireq+" - scanned="+ttl_imeiscanned);
                                                                if(ttl_imeireq <= ttl_imeiscanned)
                                                                {
                                                                        alert("Required Qty of Imei is already scanned4444");
                                                                        return false;
                                                                }else
                                                                {

                                                                        var sel_imei_inpele = $('.imei'+i_prod_id+'_unscanned:eq(0)');

                                                                                if(sel_imei_inpele.length)
                                                                                {
                                                                                        sel_imei_inpele.parent().append('<a class="remove_scanned" prod_id="'+i_prod_id+'" href="javascript:void(0)" onclick="clear_scannedimeino(this)""><b>X</b></a>');
                                                                                        sel_imei_inpele.val(s_imei).removeClass('imei'+i_prod_id+'_unscanned').addClass('imei'+i_prod_id+'_scanned');

                                                                                        prod_imeino_list[s_imei] = 0;




                                                                                        $.post(site_url+'/admin/jx_get_imei_stockdet','imei='+s_imei+'&p_invno='+sel_imei_inpele.attr('p_invno')+'&order_id='+sel_imei_inpele.attr('order_id'),function(resp){
                                                                                                if(resp.status == 'success')
                                                                                                {	
                                                                                                        var imei_map_str = new Array();
                                                                                                        if(resp.stk.product_barcode.length)
                                                                                                                imei_map_str.push(resp.stk.product_barcode); 
                                                                                                        else
                                                                                                                imei_map_str.push('0_nobc');

                                                                                                        imei_map_str.push((resp.stk.mrp)*1);
                                                                                                        imei_map_str.push(resp.stk.location_id);
                                                                                                        imei_map_str.push(resp.stk.rack_bin_id);
                                                                                                        imei_map_str.push(resp.stk.stock_id);
                                                                                                        imei_map_str.push(sel_imei_inpele.attr('itemid'));
                                                                                                        imei_map_str.push(sel_imei_inpele.attr('order_id'));




                                                                                                                        console.log(imei_map_str.join('_'));
                                                                                                                        $('#scan_barcode').val(imei_map_str.join('_'));									
                                                                                                                        //6438158558441_1249_1_10_158214_9763765471_7172369123
                                                                                                                        validate_barcode();		

                                                                                                }
                                                                                        },'json');

                                                                                }else
                                                                                {
                                                                                        alert("IMEI not found in this proforma for packing.");	
                                                                                }
                                                                }
                                                }else
                                                {
                                                        alert(resp.message);	
                                                }

                                                $("#scan_imeino").val('').focus();

                                        },'json');
                                }

                        }

        }
		
        function clear_scannedimeino(ele)
        {
                var imei_ele = $(ele).parent().find('.imeis');
                var i_prod_id = $(ele).attr('prod_id');

                        prod_imeino_list[imei_ele.val()] = undefined;

                        imei_ele.val('');
                        imei_ele.removeClass('imei'+i_prod_id+'_scanned').addClass('imei'+i_prod_id+'_unscanned');

                        $(ele).remove();
        }

        function validate_barcode()
        {
                if($("#scan_barcode").val().length==0) {
                        alert("Enter barcode");
                        return;
                }
                var sbc = $.trim($("#scan_barcode").val());

                var sel_bcstk_ele = $('tr.prod_scan:not(".done") .pbcode_'+sbc);

                //print(sbc+"\n"+sel_bcstk_ele);

                        if(sel_bcstk_ele.length > 1 && sbc.split('_').length == 1)
                        {
                                $('#mutiple_mrp_barcodes').data('m_bc',sbc).dialog("open");
                                return false;
                        }
                        //print(sel_bcstk_ele);
                        p=sel_bcstk_ele.parents('tr:eq(1)');
                        $("#scan_barcode").val("");
                        if(p.length==0)
                        {
                                alert("The product is not in invoice");
                                return false;
                        }
                        needed=parseInt($(".qty",p).html());
                        have=parseInt($(".have",p).html());
                        
                        if(needed<=have)
                        {
                                alert("Required qty is already scanned.");
                                return;
                        }
                        var ttl_bc_stk = sel_bcstk_ele.attr('stk');
                        var cur_sel = sel_bcstk_ele.val()*1;
                        if(ttl_bc_stk < cur_sel+1)
                        {
                                alert("No Stock Available for this product");
                                return false;
                        }

                        if((ttl_bc_stk-(cur_sel+1)) == 0)
                        {
                                sel_bcstk_ele.removeClass('scan_bybc');
                        }

                        var ttl_stkgrp_items = $('.scan_proditem',sel_bcstk_ele.parent()).length;
                        var ttl_scanbybc = $('.scan_bybc',sel_bcstk_ele.parent()).length;

                        if(ttl_scanbybc)
                        {
                                $('.prod_stkselprev',sel_bcstk_ele.parent()).attr('disabled',true).addClass('disabled');
                        }else
                        {
                                $('.prod_stkselprev',sel_bcstk_ele.parent()).attr('disabled',false).removeClass('disabled');
                        }	

                        sel_bcstk_ele.val(cur_sel+1);
                        sel_bcstk_ele.addClass("sel_stk");

                        var sel_bcstk_preview_ele = sel_bcstk_ele.parent().find('.prod_stkselprev');
                        sel_bcstk_preview_ele.val(sel_bcstk_preview_ele.val()*1+1); 

                        $('#mutiple_mrp_barcodes').dialog("close");		 
                        $(document).scrollTop(p.offset().top);		 
                        validate_item(p);
        }

        var done_pids=[];

        function validate_item(p)
        {
                needed=parseInt($(".qty",p).html());
                have=parseInt($(".have",p).html());
                if(needed<=have)
                {
                        alert("Required qty is already scanned");
                        return;
                }

                    $('.refund_amt',p).text('');

                    var refund_amt = 0;
                    $('.sel_stk',p).each(function(){

                            if($(this).attr('consider_for_refund')*1)
                            {
                                    var ordmrp = $(this).attr('ordmrp')*1;
                                    var disc = $(this).attr('disc')*1;
                                    var mrp = $(this).attr('mrp')*1;
                                    var qty = $(this).val()*1;
                                    var paidamt = ordmrp-disc;
                                    var newamt = mrp-(mrp*disc/ordmrp);
                                    refund_amt += Math.round((paidamt-newamt)*qty*10000)/10000; 
                            }

                    }); 

                    if(refund_amt < 0){
                            $('.refund_amt',p).text(refund_amt).css('color','red');
                            refund_alert = 1;
                    }else
                            $('.refund_amt',p).text(refund_amt).css('color','#000');

                    have=have+1;
                    $(".have",p).html(have);
                    
                    if(needed== have)
                    {
                        $(".packing_status",p).html("DONE");
                    }
                    
                    scanned_highlgt(p);
                    
                    show_ttl_summary();

                    if(have==needed)
                    {
                            p.removeClass("partial");
                            $(".status",p).html("OK");
                            done_pids.push($(".pid",p).val());
                            p.addClass("done");
                    }else if(have)
                    {
                            p.addClass("partial");
                    }
                    checkall();
        }

        function scanned_highlgt(p)
        {
                var q=p;
                q.addClass("scanned");
                window.setTimeout(function(){
                q.removeClass("scanned");
                },1000);
        }

        $(function(){
		$(".nobarcode").click(function(){
                        p=$(this).parents(".bars").get(0);
                        validate_item($(p));
		});
		$("#scan_barcode").keyup(function(e){
                        if(e.which==13)
                            validate_barcode();
		});
		
		$("#scan_imeino").keyup(function(e){
			if(e.which==13)
                           validate_imeino();
		});	
		
	});


        $('#mutiple_mrp_barcodes').dialog({
                width:500,
                height:'auto',
                autoOpen:false,
                modal:true,
                open:function(){
			var mbc = $(this).data('m_bc');
			 
			var mrp_option_list = '';
				
				$('tr.prod_scan:not(".done") .pbcode_'+mbc).each(function(){
					
					if(!$(this).parents('tr.prod_scan:first').hasClass('done'))
					{
					
                        var l_order_id = $(this).attr("order_id");
                        var l_itemid = $(this).attr("itemid");
                        var l_mrp = parseFloat($(this).attr("mrp")); 
                        var l_rbid = $(this).attr("rb_id");
                        var l_stk_id = $(this).attr("stk_info_id");

                        var l_stk = $(this).attr("stk");
                        var l_stk_reserv = $(this).attr("reserv_qty")*1;
                        var l_stk_sel = $(this).val()*1;
						l_stk_sel = !isNaN(l_stk_sel)?l_stk_sel:0;
						
						if(l_stk*1 > l_stk_sel)
						{
							 
							mrp_option_list += '<tr><td><b>'+$(this).attr("dealname")+'</b></td><td><b>'+$(this).attr("mrp")+'</b></td><td><b>'+$(this).attr("rb_name")+'</b></td><td><b>'+($(this).attr("reserv_qty")*1)+'</b></td><td><input type="radio" value="'+mbc+'_'+l_mrp+'_'+l_rbid+'_'+l_stk_id+'_'+l_itemid+'_'+l_order_id+'" name="sel_bc_mrp" /></td></tr>';
						}
							
					}	
				});
				
				
				
				if(mrp_option_list != '')
 				{
					
					$('tbody',this).html(mrp_option_list);
					$('tbody tr input[type="radio"]:first',this).attr("checked",true);
	
					if($('tbody tr',this).length == 1)
					{
						$('tbody tr input[name="sel_bc_mrp"]',this).trigger('click');	
					}
				}else
				{
					$(this).dialog('close');
				}
			}
        });

        $('input[name="sel_bc_mrp"]').live('click',function(){
                $('#scan_barcode').val($(this).val());
                validate_barcode();
        });

        $('#scan_barcode').focus();
       
		
    	 <?php if($has_imei_scan) { ?>
    	 	$('#scanbyimei').show();
    	 <?php }else{ ?>
    	 	$('#scanbyimei').hide();
    	 <?php } ?>
    	 
  	
        /* FREE SAMPLE CODE */
	$('#freesample_list_dlg').dialog({
                autoOpen:false,
                modal:true,
                open:function(){
                        is_fs_confimed = 0;
                },
                buttons:{
                        'Cancel':function(){
                                is_fs_confimed = 0;
                                $(this).dialog('close');
                        },
                        'Proceed' : function(){
                                is_fs_confimed = 1;
                                process_invoice(1);
                        }
                }
	});

	$("#freesample_list_dlg .sel").change(function(){
                var fs_dlg = $("#freesample_list_dlg");
		if($(".sel",fs_dlg).length == $(".sel:checked",fs_dlg).length)
			$("#fs_check_list_all").attr("checked",true);	
		else
			$("#fs_check_list_all").attr("checked",false);
	});
        

	$("#fs_check_list_all").change(function(){
                $("#freesample_list_dlg .sel").attr("checked",$(this).attr('checked')?true:false);
	}).attr('checked',false).trigger('change');

	//select all function
	function fs_select_all()
	{
	
            if($(".sel_all").attr("checked"))
            {
                    $(".sel").attr("checked",true);
            }
            else
                    $(".sel").attr("checked",false);
        }
        /* END FREE SAMPLE CODE */
</script>

<style>
.done {
	background: #afa;
}
.imeis{width: 250px;}
.remove_scanned{padding:5px;font-size: 11px;color:#cd0000}
.imei_inp_list{padding-left:0px; list-style-type: none;}

</style>

</div>
<?php
