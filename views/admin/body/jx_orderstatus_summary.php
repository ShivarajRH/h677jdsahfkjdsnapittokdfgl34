<style>
	.show_totalamount{max-width: 100% !important}
</style>
<?php
//          join pnh_m_territory_info f on f.id = d.territory_id
//          join pnh_m_franchise_info d on d.franchise_id = c.franchise_id
//          join pnh_towns e on e.id = d.town_id 

    $fil_menulist = array();
    $fil_brandlist = array();
    
    $cond = '';
   if($menuid!=0) {
        $cond .= ' and deal.menuid='.$menuid;
    }
   if($brandid!=0) {
        $cond .= ' and deal.brandid='.$brandid;
    }
   if($terrid!=0) {
        $cond .= ' and d.territory_id='.$terrid;
    }
    if($townid!=0) {
        $cond .= ' and d.town_id='.$townid;
    }
    if($franchiseid!=0) {
        $cond .= ' and d.franchise_id='.$franchiseid;
    }
//    echo $cond; die();
        $sql_all = "select distinct c.batch_enabled,d.franchise_id,deal.brandid,deal.menuid,m.name as menu_name,br.name as brand_name,c.transid,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
                    from king_orders o 
                    join king_transactions c on o.transid = c.transid 
                    join pnh_member_info pu on pu.user_id=o.userid 
                    join pnh_m_franchise_info d on d.franchise_id = c.franchise_id
                    join pnh_m_territory_info f on f.id = d.territory_id
                    join pnh_towns e on e.id = d.town_id 
                    join king_dealitems dl on dl.id=o.itemid
                    join king_deals deal on deal.dealid=dl.dealid
                    join king_brands br on br.id = deal.brandid 
                    join pnh_menu m on m.id = deal.menuid 
            where c.init between $st_ts and $en_ts $cond
            group by c.transid  
            order by c.init desc  ";
        
        $sql_shipped = "select distinct c.batch_enabled,d.franchise_id,deal.brandid,deal.menuid,m.name as menu_name,br.name as brand_name,b.transid,sum(o.i_coup_discount) as com,c.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id 
                            from shipment_batch_process_invoice_link sd
                            join proforma_invoices b on sd.p_invoice_no = b.p_invoice_no
                            join king_transactions c on c.transid = b.transid
                            join king_orders o on o.id = b.order_id  
                            join pnh_member_info pu on pu.user_id=o.userid 
                            join pnh_m_franchise_info d on d.franchise_id = c.franchise_id
                            join pnh_m_territory_info f on f.id = d.territory_id
                            join pnh_towns e on e.id = d.town_id 
                            join king_dealitems dl on dl.id=o.itemid
                            join king_deals deal on deal.dealid=dl.dealid
                            join king_brands br on br.id = deal.brandid 
                            join pnh_menu m on m.id = deal.menuid 

                            where o.status in (1,2) and sd.shipped = 1 and c.is_pnh = 1 $cond and sd.shipped_on between from_unixtime($st_ts) and from_unixtime($en_ts) 
                            group by b.transid 
                            order by sd.shipped_on desc";
        
        $sql_unshipped = "select distinct b.batch_enabled,d.franchise_id,deal.brandid,deal.menuid,m.name as menu_name,br.name as brand_name,b.transid,sum(o.i_coup_discount) as com,b.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id
                                from king_orders o 
                                join king_transactions b on o.transid = b.transid 
                                left join proforma_invoices c on c.order_id = o.id and c.invoice_status = 1 
                                left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                join pnh_member_info pu on pu.user_id=o.userid 
                                join pnh_m_franchise_info d on d.franchise_id = b.franchise_id
                                join pnh_m_territory_info f on f.id = d.territory_id
                                join pnh_towns e on e.id = d.town_id 
                                join king_dealitems dl on dl.id=o.itemid
                                join king_deals deal on deal.dealid=dl.dealid
                                join king_brands br on br.id = deal.brandid 
                                join pnh_menu m on m.id = deal.menuid 

                                where o.status in (0,1) and b.init between $st_ts and $en_ts $cond
                                group by b.transid 
                                order by b.init desc";
        
        $sql_cancelled = "select distinct b.batch_enabled,d.franchise_id,deal.brandid,deal.menuid,m.name as menu_name,br.name as brand_name,b.transid,sum(o.i_coup_discount) as com,b.amount,o.transid,o.status,o.time,o.actiontime,pu.user_id as userid,pu.pnh_member_id  
                            from king_orders o  
                            join king_transactions b on o.transid = b.transid 
                            left join proforma_invoices c on c.order_id = o.id
                            left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no and sd.shipped = 0 
                            join pnh_member_info pu on pu.user_id=o.userid 
                            join pnh_m_franchise_info d on d.franchise_id = b.franchise_id
                            join pnh_m_territory_info f on f.id = d.territory_id
                            join pnh_towns e on e.id = d.town_id 
                            join king_dealitems dl on dl.id=o.itemid
                            join king_deals deal on deal.dealid=dl.dealid
                            join king_brands br on br.id = deal.brandid 
                            join pnh_menu m on m.id = deal.menuid 

                            where o.status = 3  and o.actiontime between $st_ts and $en_ts $cond
                            group by b.transid 
                            order by b.init desc  ";
        
        $sql_removed="select distinct tr.batch_enabled,d.franchise_id,deal.brandid,deal.menuid,m.name as menu_name,br.name as brand_name,tr.transid,sum(o.i_coup_discount) as com,tr.amount,o.transid,o.status,o.time,o.actiontime,mi.user_id as userid,mi.pnh_member_id from king_orders o
                                join king_transactions tr on tr.transid=o.transid
                                join pnh_member_info mi on mi.user_id=o.userid 
                                join pnh_m_franchise_info d on d.franchise_id = tr.franchise_id
                                join pnh_m_territory_info f on f.id = d.territory_id
                                join pnh_towns e on e.id = d.town_id 
                                join king_dealitems dl on dl.id=o.itemid
                                join king_deals deal on deal.dealid=dl.dealid
                                join king_brands br on br.id = deal.brandid 
                                join pnh_menu m on m.id = deal.menuid 
                                where tr.batch_enabled=0 and o.status=0 and o.actiontime between $st_ts and $en_ts $cond
                                group by tr.transid
                                order by tr.init desc";
        
		if($type == 'all') {
			$sql=$sql_all;
			$str_total_invoice="Total ordered items value ";
		}elseif($type == 'shipped') {
                    	$sql=$sql_shipped;
			$order_cond = " and a.status = 2 and sd.shipped = 1 and sd.shipped_on between from_unixtime($st_ts) and from_unixtime($en_ts) ";
                        $str_total_invoice="Total shipped invoice value ";
			 
		}elseif($type == 'unshipped') {
                    	$sql=$sql_unshipped;
                        $order_cond = " and a.status in (null,0,1) and (sd.shipped = 0 or sd.shipped is null ) and t.init between $st_ts and $en_ts   ";	
                        $str_total_invoice="Total unshipped value ";
                        
		}elseif($type == 'cancelled') {
                    	$sql=$sql_cancelled;
			$order_cond = " and a.status = 3 and a.actiontime between $st_ts and $en_ts  ";
                        $str_total_invoice="Total cancelled items value ";
		}
                elseif($type == 'removed') {
                    	$sql=$sql_removed;
                        $order_cond = " a.actiontime between $st_ts and $en_ts  ";
                        $str_total_invoice="Disabled from batch value ";
                }

                $total_results=$total_results_all=$total_results_shipped=$total_results_unshipped=$total_results_cancelled='';

                $total_results=$this->db->query($sql)->num_rows();
				
				 
                
                $rslt=$this->db->query($sql)->result_array();
                foreach ($rslt as $amt) {
                    //$total_amount+=$amt['amount'];
                    $total_amount += $this->db->query("select sum((i_orgprice)*quantity) as t from king_orders where transid = ?  ",$amt['transid'])->row()->t;
					$total_inv_amount += $this->db->query("select sum((i_orgprice-(i_discount+i_coup_discount))*quantity) as t from king_orders where transid = ?  ",$amt['transid'])->row()->t;
					
                }
		$sql .=" limit $pg,$limit ";
                
//                echo '<pre>'.$sql."<br>".$total_results_shipped."<br>".$total_results_unshipped."<br>".$total_results_cancelled."<br>".$total_amount."<br>"; die("TESTING");
                
		$res = $this->db->query($sql); //,array($fid)
		$order_stat=array("Confirmed","Invoiced","Shipped","Cancelled");
                
		$resonse='';
		if(!$total_results) {
			$resonse.="<div align='center'><h3 style='margin:2px;'>No Orders found for selected dates (".format_date(date('Y-m-d',$st_ts)) ." to ".format_date(date('Y-m-d',$en_ts)).")</h3></div>".'<table class="datagrid" width="100%"></table>';	
		}else {
            
//                    PAGINATION
                    $date_from=date("Y-m-d",$st_ts);
                    $date_to=date("Y-m-d",$en_ts);
                    
                    $this->load->library('pagination');
                   
                    $config['base_url'] = site_url("admin/jx_orders_status_summary/".$type.'/'.$date_from.'/'.$date_to.'/'.$terrid.'/'.$townid.'/'.$franchiseid.'/'.$menuid.'/'.$brandid); //site_url("admin/orders/$status/$s/$e/$orders_by/$limit");
                    $config['total_rows'] = $total_results;
                    $config['per_page'] = $limit;
                    $config['uri_segment'] = 11; 
                    $config['num_links'] = 5;
                    
                    $this->config->set_item('enable_query_strings',false); 
                    $this->pagination->initialize($config); 
                    $orders_pagination = $this->pagination->create_links();
                    $this->config->set_item('enable_query_strings',TRUE);
//                  PAGINATION ENDS
                    
                $resonse.='<div class="orders_status_pagination pagi_top"> '.$orders_pagination.' </div>';
                $resonse.='
                    <table class="datagrid datagridsort" width="100%">
                    <thead><tr><th>Slno</th><th>Time</th><th>Order</th><th>Amount</th><th>Commission</th><th>Deal/Product details</th><th>Status</th><th>Last action</th></tr></thead>
                    <tbody>';
                
                    $str_total_invoice.=format_price($total_inv_amount).' , MRP Value : '.format_price($total_amount);
                    $resonse.='<script>$(".ttl_orders_status_listed,.c2,.all_pop, .shipped_pop, .unshipped_pop, .cancelled_pop, .removed_pop, .show_totalamount").css({"display":"block"});</script>';
                    $resonse.='<script>$(".show_totalamount").html("'.$str_total_invoice.' ");</script>';
                    
                        $k = 0;$slno=1; $total_amount=0;
						$total_inv_amount=0;
                        foreach($res->result_array() as $o) {
                                $fil_menulist[$o['menuid']] = $o['menu_name'];
                                $fil_brandlist[$o['brandid']] = $o['brand_name'];
                            
                                    $trans_ttl_orders = 0;// and sd.packed = 1  and sd.shipped = 1 
                                    $sql_inner="select e.invoice_no,sd.packed,sd.shipped,e.invoice_status,sd.shipped_on,a.status,a.id,a.itemid,b.name,a.quantity,i_orgprice,i_price,i_discount,i_coup_discount 
                                                                        from king_orders a
                                                                        join king_dealitems b on a.itemid = b.id
                                                                        join king_deals dl on dl.dealid = b.dealid
                                                                        join king_transactions t on t.transid = a.transid   
                                                                        left join proforma_invoices c on c.order_id = a.id 
                                                                        left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = c.p_invoice_no 
                                                                        left join king_invoice e on e.invoice_no = sd.invoice_no
                                                                where a.transid = '".$o['transid']."'
                                                                    $order_cond order by c.p_invoice_no desc";
                                    
//                        echo '<pre>';print_r($sql);echo '</pre>';
//                        echo '<pre>';print_r($sql_inner);echo '</pre>';
//                        die();      
                                    $o_item_list = $this->db->query($sql_inner)->result_array();
                            if(!$o_item_list)
                                continue;
                            $k++;
                            $orders=$this->erpm->getordersfortransid($o['transid']); $order=$orders[0];
                            
                            
							$trans_created_by = @$this->db->query("select username from king_admin a join king_transactions b on a.id = b.trans_created_by where transid = ? ",$o['transid'])->row()->username;
								if($trans_created_by) 
									$trans_created_by .= '<br><br> by <b>'.($trans_created_by).'</b>';
		
		
//                           echo '<pre>';print_r($order); die();
                            $resonse.='<tr>
                            <td>'.$slno.'</td>
                            <td>'.format_datetime(date('Y-m-d H:i:s',$o['time'])).($trans_created_by).'</td>
                            <td width="200">
                                    <span><a href="'.site_url("admin/trans/{$o['transid']}").'" target="_blank">'.$o['transid'].'</a> <br /></span>
                                    <span>Member ID: <a href="'.site_url("admin/pnh_viewmember/{$o['userid']}").'" target="_blank">'.$o['pnh_member_id'].'</a><br></span>
                                    <span><a href="'.site_url("admin/pnh_franchise/{$o['franchise_id']}").'"  target="_blank">'.$order['bill_person'].'</a><br></span>
                                    <span>'.wordwrap($order['ship_address'],35,'<br>').'</span>
                                    <span>'.$order['ship_city'].'<br></span>
                                    <span>'.$order['ship_state'].' - '.$order['ship_pincode'].'<br></span>
                                    <span>'.$order['ship_phone'].'</span>
                            </td>';
                                if($o['batch_enabled']==1) {
                                    $batch_enabled='<div class="clear" style="margin-top:20px;"><span style="background-color:#8C489F;color: #F1F0FF;">Batch: Enabled</span></div>';
                                }
                                else {
                                    $batch_enabled='<div class="clear" style="margin-top:20px;"><span style="color: #8C489F;">Batch: Disabled</span></div>';
                                }
                            $resonse.='
                            <td>'.round($o['amount'],2).' '.$batch_enabled.' </td>
                            <td>'.round($o['com'],2).'</td>
                            <td style="padding:0px;">
                                    <table class="subdatagrid" cellpadding="4" cellspacing="0">
                                            <thead>
                                                    <th width="40">OID</th>
                                                    <th width="300">ITEM</th>
                                                    <th width="40">QTY</th>
                                                    <th width="40">MRP</th>
                                                    <th width="40">Amount</th>
                                                    <th width="40">Shipped</th>
                                                    <td width="40"></td>
                                            </thead>
                                            <tbody>';

                                                $trans_ttl_shipped = 0;
                                                $trans_ttl_cancelled = 0;
                                                $processed_oids = array();
                                                
                                                foreach($o_item_list as $o_item) {
                                                        if(!isset($processed_oids[$o_item['id']]))
                                                                $processed_oids[$o_item['id']] = 1;
                                                        else
                                                                continue;

                                                        $ord_status_color = '';
                                                        $is_shipped = 0;
                                                        $is_cancelled = ($o_item['status']==3)?1:0;
                                                        if($is_cancelled)
                                                        {
                                                                $trans_ttl_cancelled += 1;
                                                                $ord_status_color = 'cancelled_ord';
                                                        }else
                                                        {
                                                                
                                                                $ship_dets = array(); 
                                                                $invoice_block='';
                                                                $is_shipped = ($o_item['shipped'])?1:0;;
                                                                if($o_item['shipped'] && $o_item['invoice_status'])
                                                                {
                                                                        $trans_ttl_shipped += 1;
                                                                        $ship_dets[$o_item['invoice_no']] = format_date($o_item['shipped_on']);//format_date(date('Y-m-d',$o_item['shipped_on']));
                                                                        
                                                                        $ord_status_color = 'shipped_ord';
                                                                }else if($o_item['status'] == 0)
                                                                {
                                                                        $ord_status_color = 'pending_ord';
                                                                }
                                                                $invoice_block='<div style="font-size:10px;color:green;">';
                                                                foreach($ship_dets as $s_invno =>$s_shipdate)
                                                                {
                                                                        $status_mrp= $this->db->query("select round(sum(nlc*quantity)) as amt from king_invoice a join king_orders b on a.order_id = b.id where a.invoice_no = '".$s_invno."' ")->row()->amt;

                                                                        $invoice_block.='<div style="margin:3px 0;"><a target="_blank" href="'.site_url('admin/invoice/'.$s_invno).'">'.$s_invno.'-'.$s_shipdate.'</a> - Rs.'.$status_mrp.' </div>';
                                                                }
                                                                $invoice_block.='</div>';
                                                        }
                                                        $is_shipped = ($is_shipped && $o_item['invoice_status']) ?'Yes':'No';
                                                          $amount=round($o_item['i_orgprice']-($o_item['i_coup_discount']+$o_item['i_discount']),2);
//                                                        $total_amount+=$amount;
                                                        
                                                        

                                                        $resonse.='<tr class="'.$ord_status_color.'">
                                                                <td width="40">'.$o_item['id'].'</td>
                                                                <td>'.anchor('admin/pnh_deal/'.$o_item['itemid'],$o_item['name'],'  target="_blank" ').'</td>
                                                                <td width="20">'.$o_item['quantity'].'</td>
                                                                <td width="40">'.$o_item['i_orgprice'].'</td>
                                                                <td width="40">'.$amount.'</td>
                                                                <td width="40" align="center">'.$is_shipped.'</td>
                                                                <td align="center">'.$invoice_block.'</td>
                                                            </tr>';

                                                    }

                                                $trans_ttl_orders = count($processed_oids);

                                $resonse.='</tbody>
                                        </table>
                                </td>
                                <td>';

                                if($trans_ttl_orders == $trans_ttl_cancelled)
                                {
                                        $resonse.="Cancelled";
                                }else
                                {
                                        if(($trans_ttl_orders-$trans_ttl_cancelled) == $trans_ttl_shipped)
                                        {
                                                $resonse.='Shipped <br>
                                                    <a href="javascript:void(0)" onclick="get_invoicetransit_log(this,'.trim($o_item['invoice_no']).');" class="btn">View Transit Log</a>';
                                        }else if($trans_ttl_shipped)
                                        {
                                                $resonse.="Partitally Shipped";
                                        }else {
                                                $resonse.="UnShipped";
                                        }
                                }
                               

                            $actiontime= ($o['actiontime']==0)?"na":format_datetime(date('Y-m-d H:i:s', $o['actiontime'] ) );
                        $resonse.='</td><td>'.$actiontime.'</td></tr>';
                        $slno++;
                    }

                    $resonse.='</tbody> </table>';

                $resonse .= '<div id="orders_status_pagination" class="orders_status_pagination">'.$orders_pagination.'</div>';
                
                $total_results_all=$this->db->query($sql_all)->num_rows();
                $total_results_shipped=$this->db->query($sql_shipped)->num_rows();
                $total_results_unshipped=$this->db->query($sql_unshipped)->num_rows();
                $total_results_cancelled=$this->db->query($sql_cancelled)->num_rows();
                $total_results_removed=$this->db->query($sql_removed)->num_rows();

                
                
                   
                }
                echo $resonse;
                /******* Extra operations *********/
                
                $endlimit=($pg+1*$limit);
                $endlimit=($endlimit>$total_results)?$total_results : $endlimit;
                $resonse2='
                    <script>$(".ttl_orders_status_listed").html("Showing <b>'.($pg+1).' to '.$endlimit.'</b> of '.$total_results.' orders");</script>
                    <script>$(".c2").html("Orders from '.format_date(date('Y-m-d',$st_ts)).' to '.format_date(date('Y-m-d',$en_ts)).'");</script>';
                    
                $resonse2.='<script type="text/javascript">$(".all_pop").addClass("popbg"); $(".all_pop").html("'.$total_results_all.'");  $(".shipped_pop").addClass("popbg"); $(".shipped_pop").html("'.$total_results_shipped.'");$(".unshipped_pop").addClass("popbg"); $(".unshipped_pop").html("'.$total_results_unshipped.'"); $(".cancelled_pop").addClass("popbg"); $(".cancelled_pop").html("'.$total_results_cancelled.'");  $(".removed_pop").addClass("popbg"); $(".removed_pop").html("'.$total_results_removed.'");</script>';
                if(count($fil_menulist) && $menuid==00)
                {
                    asort($fil_menulist);
                    $menulist = '<option value="00">All Menu</option>';
                    foreach($fil_menulist as $fmenu_id=>$fmenu_name)
                    {
                        $menulist .= '<option value="'.$fmenu_id.'">'.$fmenu_name.'</option>';   
                    }
                    $resonse2.='<script>$("#sel_menu").html(\''.$menulist.'\')</script>';
                }
                if(count($fil_brandlist) && $brandid==00)
                {
                    asort($fil_brandlist);
                    $brandlist = '<option value="00">All Brands</option>';
                    foreach($fil_brandlist as $fbrandid=>$fbrand_name)
                    {
                        $brandlist .= '<option value="'.$fbrandid.'">'.$fbrand_name.'</option>';   
                    }
                    
                    $resonse2.='<script>$("#sel_brands").html(\''.$brandlist.'\')</script>';
                }
                echo ''.$resonse2;
                
?>