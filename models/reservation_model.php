<?php
/**
 * Description of reservation_model
 *
 * @author Shivaraj@storeking.in
 * @access public
 */
class reservation_model extends Model
{
    function __construct() {
            parent::__construct();
    }
    
    /**
     * transaction creator name info
     * @param type $transid
     * @return type string
     */
    function get_trans_created_by($transid) {
        $username=@$this->db->query("select username from king_admin a join king_transactions b on a.id = b.trans_created_by where transid = ? ",$transid)->row()->username;
        return $username;
    }
    
    /**
     * Function to return orders of the transaction
     * @param type $transid varchar
     * @return type array 
     */
    function get_orders_of_trans($transid) {
            $sql="select o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,di.name
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,pi.p_invoice_no
                    from king_orders o
                    join king_transactions tr on tr.transid = o.transid and o.status in (0,1) and tr.batch_enabled = 1
                    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                    left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                    left join proforma_invoices `pi` on pi.order_id = o.id and pi.invoice_status = 1 
                    join king_dealitems di on di.id = o.itemid 
                    where i.id is null and tr.transid = ?
                    order by tr.init,di.name ";
             $trans_orders = $this->db->query($sql,$transid)->result_array();
             return $trans_orders;
    }
    
    /**
     * 
     * @param type $fid
     * @param type $p_invoice_ids string
     * @return type array
     */
    function get_packing_details($fid=0,$p_invoice_ids) 
    {
        $param=array();
        $cond='';
        if($fid)
        {
                $cond.=" and t.franchise_id=? and pi.invoice_status = 1 and b.invoice_no = 0 "; 
                $param[]=$fid;
        }
        $sql="select pt.courier_name as p_courier_name,t.init as ordered_on,b.*,pi.transid as pi_transid,
                pi.invoice_status as p_invoice_status,i.transid,i.invoice_status  
                from shipment_batch_process_invoice_link b
                left outer join proforma_invoices pi on pi.p_invoice_no=b.p_invoice_no  
                left outer join king_invoice i on i.invoice_no=b.invoice_no  
                left outer join king_transactions t on t.transid=pi.transid  
                left outer join partner_transaction_details pt on pt.transid=t.transid and pt.order_no = t.partner_reference_no  
                where 1 $cond and b.p_invoice_no in ($p_invoice_ids)
                group by b.p_invoice_no";
        //$param[]=;
        
        $result = $this->db->query($sql,$param)->result_array();
        $result['last_query'] = $this->db->last_query();
        
        return $result;
    }
    
    /**
     * Function to return transaction list of batch type
     * @param type $batch_type string
     * @param type $from date
     * @param type $to date
     * @param type $franchise_id int
     * @param type $userid int
     * @param type $oldest_newest int
     * @return type array
     */
    function get_trans_list($batch_type,$from=0,$to=0,$franchise_id=0,$userid=0,$oldest_newest='new') {
        
        
        $cond = '';
        if($franchise_id) 
            $cond .=" and tr.franchise_id=$franchise_id ";
        
        if($userid!=1) { //$batch_type != 'pending' && 
            if($userid) {
                //$cond .= ' and sbp.assigned_userid = '.$userid.' ';
            }
        }
        
        $sql = "select * from ( 
                    select transid,TRIM(BOTH ',' FROM group_concat(p_inv_nos)) as p_inv_nos,batch_id,status,count(*) as t,if(count(*)>1,'partial',(if(status,'ready','pending'))) as trans_status,franchise_id  
                    from (
                    select o.transid,ifnull(group_concat(distinct pi.p_invoice_no),'') as p_inv_nos,sd.batch_id,o.status,count(*) as ttl_o,tr.franchise_id,tr.actiontime
                            from king_orders o
                            join king_transactions tr on tr.transid=o.transid
                            left join king_invoice i on i.order_id = o.id and i.invoice_status = 1 
                            left join proforma_invoices pi on pi.order_id = o.id and o.transid  = pi.transid and pi.invoice_status = 1 
                            left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no 
                            left join shipment_batch_process sbp on sbp.batch_id = sd.batch_id
                            where o.status in (0,1)  and i.id is null and tr.franchise_id != 0 $cond and ((sd.packed=0 and sd.p_invoice_no > 0) or (sd.p_invoice_no is null and sd.packed is null ))
                            group by o.transid,o.status
                    ) as g 
                    group by g.transid )as g1 having g1.trans_status = ?";
        
        $rslt['result'] = $this->db->query($sql,array($batch_type))->result_array();
        $rslt['last_query'] = $this->db->last_query();
        
        return $rslt;
        
    }
    
    /**
     * Function to get total orders
     * @param type $trans null
     * @param type $franchise_id int
     * @param type $from date
     * @param type $to date
     * @return type int
     */
    function getTotalOrderDetails($trans,$franchise_id,$from,$to) {
            $arr_rslt = array();
            
            $arr_rslt['total_orders'] = $total_orders = $this->db->query("select count(distinct tr.transid) as total_trans_by_fran
                                                                        from king_orders o
                                                                        join king_transactions tr on tr.transid = o.transid and o.status in (0,1) and tr.batch_enabled = 1
                                                                        join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                                                                        left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                                                                        where f.franchise_id = ? and tr.actiontime between ? and ? and i.id is null group by f.franchise_id ", array($franchise_id,$from,$to))->row()->total_trans_by_fran;
            
            
            return $arr_rslt;
            
    }
    
    /**
     * Get Username by id
     * @param type $userid int
     * @return type string
     */
    function get_username_byid($userid) {
        return $this->db->query("select username from king_admin where id=?",$userid)->row()->username;
    }
    
    /**
     * Function to create batches by batch config settings
     * @param type $userid int
     * @return type array
     */
    function do_create_batch_by_group_config ($userid) {
        
        error_reporting(E_ALL);
        ini_set("display_errors",true);
        
        $output = $cond = $menu_cond = $username = $territory_name = '';
        $result = array();
        
        foreach(array("sel_batch_menu","batch_size","assigned_uid","territory_id","townid","franchise_id") as $i) {
            $$i=$this->input->post($i);
            //echo $i.'=>'.$$i."<br>";,"assigned_menuids"
        }
        if($territory_id != 0) {
            $cond .= ' and f.territory_id = '.$territory_id.' ';
            //$territory_name=$this->db->query("select territory_name from pnh_m_territory_info where id=?",$territory_id)->row()->territory_name;
        }
        if($franchise_id != 0) {
            $cond .= ' and f.franchise_id = '.$franchise_id.' ';
            //$franchise_name=$this->db->query("select territory_name from pnh_m_territory_info where id=?",$territory_id)->row()->franchise_name;
        }
        if($assigned_uid!=0) {
            $username=  $this->get_username_byid($assigned_uid);
        }
        if($sel_batch_menu != '00') {
            $menu_cond = " and  bc.id = $sel_batch_menu ";
        }
        
        $batch_remarks = 'By Transaction Reservation System';
                
        
        $sql="select distinct o.itemid,bc.id as menuid,bc.batch_grp_name as menuname,f.territory_id,sd.id,sd.batch_id,sd.p_invoice_no,from_unixtime(tr.init) from king_transactions tr
                                join king_orders as o on o.transid=tr.transid
                                join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no
                                join king_dealitems dl on dl.id = o.itemid
                                join king_deals d on d.dealid = dl.dealid 
                                join m_batch_config bc on find_in_set(d.menuid,bc.assigned_menuid) 
                                $menu_cond
                                join pnh_menu mn on mn.id=d.menuid
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id #and f.is_suspended = 0
                                where sd.batch_id=? $cond
                                group by o.transid
                                order by menuname,tr.init asc";
        
        $rslt_set = $this->db->query($sql,GLOBAL_BATCH_ID);
        $result['lst_qry'] = $this->db->last_query(); 
       
        
        $ttl_inbatch = $rslt_set->num_rows(); //$ttl_inbatch = count($rslt);
        
        $menu_orders=array();
        if($ttl_inbatch>0) {
            
            $orders_det = $rslt_set->result_array();
            
            foreach($orders_det as $order) {
                $menu_orders[$order['menuid']]['menuname'] = $order['menuname'];
                $menu_orders[$order['menuid']]['orders'][] = $order;
            }
//            echo '<pre>'.$menu_orders;die();
                $ttl_batch_alloted=0;
                // loop through menu items
                foreach($menu_orders as $menuid=>$menu_item) {
                    $ttl_menu_orders = count($menu_item['orders']);
                   $ttl_pgs =  ceil($ttl_menu_orders/$batch_size);
                    
                   $remaining=$ttl_menu_orders;
                    //iterate through all pages build batches
                    for($i=0; $i<$ttl_pgs; $i++) {
                        if($remaining<=0) 
                            continue;
                        
                        //Prepare total orders in batch
                        if($remaining>$batch_size) {
                                if(($remaining-($batch_size/2))>$batch_size) {
                                     $ttl_allot= $batch_size;
                                }
                                else {
                                     $ttl_allot= $remaining;
                                }
                        }
                        else {
                            $ttl_allot=$remaining;
                        }
                        $remaining = $remaining-$ttl_allot;

                        $start = ($i * $batch_size);
                        $end=$start+$ttl_allot;

                        // generate new batch id selected total orders
                        $new_batch_id= $this->insert_shipmentbatch_get_batch_id($ttl_allot,$assigned_uid,$territory_id,$menuid,$batch_remarks);
                        $ttl_batch_alloted++;
                        for($j=$start; $j<$end; $j++) {
                            $this->update_shipmentbatch_batch_id($new_batch_id,$menu_item["orders"][$j]['id'],$userid);
                            $menu_orders[$menuid]['orders'][$j]['new_batch_id'] = $new_batch_id;
                        }
                    }
                }
                $result["status"]='success';
                $result["response"]="Total ".$ttl_batch_alloted." batches created.";
            }
            else {
                $result["status"]='fail';
                $result['response']="No transactions found.";
            }
            return $result;
    }
    
    /**
     * Function to update batch id in invoice link table
     * @param type $new_batch_id
     * @param type $tbl_id
     */
    function update_shipmentbatch_batch_id($new_batch_id,$tbl_id,$userid) {
            $arr_set = array("batch_id"=>$new_batch_id,"batched_on"=> time(),"batched_by"=>$userid);
            $arr_where =array("id"=>$tbl_id);
            $this->db->update("shipment_batch_process_invoice_link",$arr_set,$arr_where);
//            echo "Success";
    }
    
    /**
     * Function to insert batch information and return created batch id
     * @param type $batch_size int
     * @param type $assigned_uid int
     * @param type $territory_id int
     * @param type $sel_batch_menu int
     * @param type $batch_remarks string
     * @return type int
     */
    function insert_shipmentbatch_get_batch_id($batch_size,$assigned_uid,$territory_id,$sel_batch_menu,$batch_remarks) {

            $this->db->query("insert into shipment_batch_process(num_orders,assigned_userid,territory_id,batch_configid,batch_remarks,created_on) values(?,?,?,?,?,?)"
                                    ,array($batch_size,$assigned_uid,$territory_id,$sel_batch_menu,$batch_remarks,date('Y-m-d H:i:s',time()) ));

            $new_batch_id = $this->db->insert_id();
            return $new_batch_id;
    }
    
    /**
     * Function to return Batch config table data
     * @return type array
     */
    function getBatchGroupConfig() {
        return $this->db->query("select * from m_batch_config")->result_array();
    }
    
    /**
     * Function to get product list for pickslip
     * @param type $batch_id int
     * @return type array
     */
    function product_proc_list_for_invoice($batch_id) { //$p_invoiceid
        //$data['prods']=$this->erpm->getprodproclist($bid);
        $arr_rslt = $this->db->query("select franchise_id,franchise_name,batch_id,menuid,menuname,p_invoice_no,product_id,product,location,sum(rqty) as qty from ( 
                                            select tr.franchise_id,f.franchise_name,e.batch_id,dl.menuid,mnu.name as menuname,rbs.p_invoice_no,rbs.product_id,pi.product_name as product,concat(concat(rack_name,bin_name),'::',si.mrp) as location,rbs.qty as rqty 
                                                    from t_reserved_batch_stock rbs 
                                                    join t_stock_info si on rbs.stock_info_id = si.stock_id 
                                                    join m_product_info pi on pi.product_id = si.product_id 
                                                    join m_rack_bin_info rak on rak.id = si.rack_bin_id 
                                                    join shipment_batch_process_invoice_link e on e.p_invoice_no = rbs.p_invoice_no and invoice_no = 0

                                                    join king_orders o on o.id = rbs.order_id
                                                    join king_transactions tr on tr.transid = o.transid
                                                    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                                                    
                                                    join king_dealitems dlt on dlt.id = o.itemid
                                                    join king_deals dl on dl.dealid = dlt.dealid
                                                    join pnh_menu as mnu on mnu.id = dl.menuid and mnu.status=1

                                                    where  e.batch_id = ?
                                            group by rbs.id  ) as g 
                                            group by product_id,location",$batch_id)->result_array(); //e.p_invoice_no=?$p_invoiceid
//        echo '<pre>'.$this->db->last_query();die();
        return $arr_rslt;
    }
        
    /**
     * Process the transactions to batch and generates  performa invoice for Batch enabled trans
     * @param type $transid
     * @param type $ttl_num
     * @param type $batch_remarks
     * @param type $updated_by
     * @depends do_pnh_offline_order(), and reservation controller functions
     */
    function do_batching_process($transid,$ttl_num,$batch_remarks="PNH Offline Order Placed.",$updated_by)
    {
            ini_set('memory_limit','512M');
            ini_set('max_execution_time','60000');
            
            $i_transid=false;
            $num=$ttl_num;
            $process_partial = 1;

            $user = $this->erpm->auth();
            $output=array();

            if(empty($num)) {
                    $output = array("status"=>"fail","resp"=>"Enter no of orders to process"); return $output;
            }
            
            $i_transid=$transid;//$this->input->post("transid");


            $down_import_summ = 0;
            $cond = '';
            $is_pnh=1;
           
            $trans=array();
            $itemids=array();
           
            
            if($i_transid)
            {
                    $cond = '';
                    $is_pnh = $this->db->query("select is_pnh from king_transactions where transid = ? ",$i_transid)->row()->is_pnh;
            }


            if($is_pnh)
            {
                    $fr_list = array();
                    $fr_list_res = $this->db->query("select franchise_id  from pnh_m_franchise_info where is_suspended = 0 ")->result_array();
                    foreach($fr_list_res as $fr_det)
                            $fr_list[] = $fr_det['franchise_id'];

                    $cond .= ' and t.franchise_id in ('.implode(',',$fr_list).')';
            }


            if($i_transid)
                    $raw_trans=$this->db->query("select o.* from king_transactions t join king_orders o on o.transid=t.transid and o.status=0 where t.batch_enabled=1 and t.transid=?",$i_transid)->result_array();
            else
                    $raw_trans=$this->db->query("select o.*,t.partner_reference_no from king_transactions t join king_orders o on o.transid=t.transid and o.status=0 join king_dealitems di on di.id = o.itemid join king_deals d on d.dealid = di.dealid  where t.batch_enabled=1 and t.is_pnh=$is_pnh $cond order by t.priority desc, t.init asc")->result_array();
                    

            $v_transids=array();
            foreach($raw_trans as $t)
            {
                    $transid=$t['transid'];
                    if(!isset($trans[$transid]))
                            $trans[$transid]=array();
                    $trans[$transid][]=$t;
                    $itemids[]=$t['itemid'];
                    $v_transids[]=$t['transid'];
            }
            if(empty($trans)) {
                    $output = array("status"=>"fail","resp"=>"No orders to process"); return $output;
            }

            $itemids=array_unique($itemids);
            $raw_prods=$this->db->query("select itemid,qty,product_id from m_product_deal_link where itemid in ('".implode("','",$itemids)."')")->result_array();
            $products=array();
            $productids=array();
            $partials=$not_partials=array();
            foreach($raw_prods as $p)
            {
                    $itemid=$p['itemid'];
                    if(!isset($products[$itemid]))
                            $products[$itemid]=array();

                    $products[$itemid][]=$p;
                    $productids[]=$p['product_id'];
            }
            $productids=array_unique($productids);

            $raw_prods=$this->db->query("select * from products_group_orders where transid in ('".implode("','",$v_transids)."')")->result_array();

            foreach($raw_prods as $r)
            {
                    $itemid=$this->db->query("select itemid from king_orders where id=? and transid = ? ",array($r['order_id'],$r['transid']))->row()->itemid;
                    $qty=$this->db->query("select l.qty from products_group_pids p join m_product_group_deal_link l on l.group_id=p.group_id where p.product_id=? and itemid = ? ",array($r['product_id'],$itemid))->row()->qty;

                    if(!isset($products[$itemid]))
                            $products[$itemid]=array();

                    $products[$itemid][]=array("itemid"=>$itemid,"qty"=>$qty,"product_id"=>$r['product_id'],"order_id"=>$r['order_id']);
                    $productids[]=$r['product_id'];

            }

            $to_process_orders=array();
            $raw_stock=$this->db->query("select product_id,sum(available_qty) as stock from t_stock_info where product_id in ('".implode("','",$productids)."') and available_qty > 0 and mrp > 0 group by product_id")->result_array();
            $stock=array();
            foreach($productids as $p)
                    $stock[$p]=0;
            foreach($raw_stock as $s)
            {
                    $pid=$s['product_id'];
                    $stock[$pid]=$s['stock'];
            }

            $total_orders_process=0;
            foreach($trans as $transid=>$orders)
            {
                    $total_pending[$transid]=count($orders);
                    $possible[$transid]=0;
                    $not_partial_flag=true;
                    $same_order=array();

                    $rem_prod_stock = array(); 
                    foreach($orders as $order)
                    {
                            $itemid=$order['itemid'];

                            if(!isset($products[$itemid]))
                                    continue;

                            $pflag=true; //process flag
                            foreach($products[$itemid] as $p)
                            {
                                    $process_stk_chk = 0;
                                    if(isset($p['order_id']))
                                    {
                                            if($p['order_id'] == $order['id'])
                                                    $process_stk_chk = 1;
                                    }else
                                    {
                                            $process_stk_chk = 1;
                                    }		
                                    if($process_stk_chk)
                                    {
                                            if(!isset($rem_prod_stock[$p['product_id']]))
                                                    $rem_prod_stock[$p['product_id']] = $stock[$p['product_id']];

                                            //echo ($stock[$p['product_id']]).'-'.$p['product_id'].' - '.$p['qty'].' - '.$order['quantity'].' ';
                                            if($rem_prod_stock[$p['product_id']]<$p['qty']*$order['quantity'])
                                            {
                                                    $pflag=false;
                                                    break;
                                            }else
                                                    $rem_prod_stock[$p['product_id']] -= $p['qty']*$order['quantity'];
                                    }	
                            }

                            if($pflag)
                            {
                                    $possible[$transid]++;
                                    if($process_partial)
                                    {
                                            $to_process_orders[]=$order['id'];
                                            $same_order[]=$order['id'];
                                    }
                            }
                            else
                                    $not_partial_flag=false;

                    }

                    if($not_partial_flag)
                    {
                            $same_order=array();
                            //$to_process_orders=array();
                            foreach($orders as $order)
                            {
                                    $to_process_orders[]=$order['id'];
                                    $same_order[]=$order['id'];
                            }
                            $not_partials[]=$transid;
                    }else
                            $partials[]=$transid;

                    if(!empty($same_order))
                    {
                            $total_orders_process++;
                            foreach($orders as $order)
                            {
                                    if(in_array($order['id'],$same_order))
                                    {
                                            if(!isset($products[$itemid]))
                                                    continue;

                                            foreach($products[$order['itemid']] as $p)
                                            {
                                                    if($stock[$p['product_id']] >= ($p['qty']*$order['quantity']))
                                                    {
                                                            $stock[$p['product_id']]-=$p['qty']*$order['quantity'];
                                                            $to_process_orders[] = $order['id'];
                                                    }
                                                    else
                                                            break ;
                                            }
                                    }
                            }
                    }

                    if($total_orders_process>=$ttl_num)
                            break;
            }
            
            $orders=array_unique($to_process_orders);

//            echo '<pre>';
//            print_r($productids);
//            print_r($orders);
//            print_r($stock);
//            exit;
            
            //$output['productids']=$productids;
            //$output['orders']=$orders;
            $output['products']=$stock;
            
            $p_invoices=$this->erpm->do_proforma_invoice($orders);

            $batch_id=0;
            $batch_inv_link = array();

            $ttl_invoices = count($p_invoices);
            if($ttl_invoices > $num)
                    $ttl_batchs = ceil($ttl_invoices/$num);
            else 
                    $ttl_batchs = 1;

            //$batch_remarks = $this->input->post('batch_remarks');

            if(!empty($p_invoices))
            {
                    for($b=0;$b<$ttl_batchs;$b++)
                    {
                            $s = $b*$num;
                            $ttl_inbatch = ((($s+$num) > $ttl_invoices)?$ttl_invoices-$s:$num);

                            //$this->db->query("insert into shipment_batch_process(num_orders,batch_remarks,created_on) values(?,?,?)",array($ttl_inbatch,$batch_remarks,date('Y-m-d H:i:s')));$batch_id = $this->db->insert_id();
                            $batch_id = GLOBAL_BATCH_ID;
                            
                            for($k=$s;$k<$s+$ttl_inbatch;$k++)
                            {
                                    $inv = $p_invoices[$k];
                                    $batch_inv_link[$inv] = $batch_id;
                                    $cid=0;
                                    $awb="";

                                    // generate entries for processing splitted orders to invoice no with one proforma invoice

                                    $p_ttl_inv_ords = @$this->db->query("select sum(t) as t from ((select count(*) as t 
                                                                                    from proforma_invoices a
                                                                                    join king_orders b on a.order_id = b.id and a.transid = b.transid 
                                                                                    where a.p_invoice_no = ? and a.invoice_status = 1 and is_ordqty_splitd = 1 
                                                                                    group by is_ordqty_splitd 
                                                                                    )union 
                                                                                    (
                                                                                    select 1 as t 
                                                                                    from proforma_invoices a
                                                                                    join king_orders b on a.order_id = b.id and a.transid = b.transid 
                                                                                    where a.p_invoice_no = ? and a.invoice_status = 1 and is_ordqty_splitd = 0  
                                                                                    group by is_ordqty_splitd 
                                                                                    )) as g ",array($inv,$inv))->row()->t; 

                                    $p_ttl_inv_ords = $p_ttl_inv_ords*1;					

                                    for($k1=0;$k1<$p_ttl_inv_ords;$k1++) 
                                            $this->db->query("insert into shipment_batch_process_invoice_link(batch_id,p_invoice_no,courier_id,awb,batched_on,batched_by) values(?,?,?,?,?,?)",array($batch_id,$inv,$cid,$awb,time(),$updated_by));

                            }
                    }
            }

        $total_qty=0;
            $down_summary = array(); 
            foreach($orders as $o)
            {
                    $pinv_det=@$this->db->query("select id,p_invoice_no,transid from proforma_invoices where order_id=? and invoice_status = 1 order by id desc ",$o)->row_array();
                    if(!$pinv_det)
                            continue;

                    $invid = $pinv_det['id'];
                    $pinvno = $pinv_det['p_invoice_no'];
                    $ptransid = $pinv_det['transid'];
                    
                    $output['p_invoice_no'][] = $pinvno;
                    
                    $s_prods=$this->db->query("select t.partner_reference_no,o.transid,o.id,p.product_id,p.qty,o.quantity,o.i_orgprice,o.id as order_id from king_orders o join m_product_deal_link p on p.itemid=o.itemid join king_transactions t on t.transid  = o.transid  where o.id=? and o.transid = ? order by o.sno asc",array($o,$ptransid))->result_array();

                    $s_prods_1=$this->db->query("select t.partner_reference_no,o.transid,o.id,go.product_id,p.qty,o.quantity,o.i_orgprice,o.id as order_id 
                                                                                            from king_orders o 
                                                                                            join king_transactions t on t.transid  = o.transid 
                                                                                            join products_group_orders go on go.order_id = o.id 
                                                                                            join m_product_group_deal_link p on p.itemid=o.itemid 
                                                                                            where o.id=? and o.transid = ?  order by o.sno asc",array($o,$ptransid))->result_array();

                    $s_prods = array_merge($s_prods,$s_prods_1);

                    // compute stock reduction by order quantity and record stock info ids 
                    foreach($s_prods as $p)
                    {
                            $omrp = $p['i_orgprice'];
                            $total_qty = $p['quantity']*$p['qty'];
                            $order_id = $p['order_id'];

                            if($down_import_summ)
                                    $down_summary[$p['partner_reference_no']] = $pinvno;

                            /*
                            for($i=1;$i<=$p['quantity']*$p['qty'];$i++)
                                    $this->db->query("update t_stock_info set available_qty=available_qty-1 where product_id=? and available_qty>=0 order by stock_id asc limit 1",$p['product_id']);
                            */
                            $alloted_stock = array();
                            $alloted_stock2 = array();

                            $pen_qty = $total_qty;
                            
                            
                            
                            // query to fetch stock product ordered by exact mrp and followed by asc mrp.  	

                            $sql = "select product_barcode,stock_id,product_id,available_qty,location_id,rack_bin_id,mrp,if((mrp-$omrp),1,0) as mrp_diff 
                                            from t_stock_info where mrp > 0  and product_id = ? and available_qty > 0 
                                            order by product_id desc,mrp_diff,mrp ";


                            $stk_prod_list = $this->db->query($sql,$p['product_id']);

                            if($stk_prod_list->num_rows())
                            {
                                    // iterate all stock product 
                                    foreach($stk_prod_list->result_array() as $stk_prod)
                                    {
                                            $reserv_qty = 0; 
                                            if($stk_prod['available_qty'] < $pen_qty )
                                                    $reserv_qty = $stk_prod['available_qty'];
                                            else
                                                    $reserv_qty = $pen_qty;

                                                    $tmp = array();
                                                    $tmp['p_invoice_no'] = $pinvno;
                                                    $tmp['stock_info_id'] = $stk_prod['stock_id'];
                                                    $tmp['product_id'] = $stk_prod['product_id'];
                                                    $tmp['batch_id'] = $batch_id;
                                                    $tmp['order_id'] = $order_id;
                                                    $tmp['qty'] = $reserv_qty;
                                                    $tmp['reserved_on'] = time();
                                                    
                                                    array_push($alloted_stock,$tmp);
                                                    
                                                    $tmp['mrp'] = $stk_prod['mrp'];//by S
                                                    $tmp['product_barcode'] = $stk_prod['product_barcode'];//by S
                                                    $tmp['location_id'] = $stk_prod['location_id'];//by S
                                                    $tmp['rack_bin_id'] = $stk_prod['rack_bin_id'];//by S
                                                    array_push($alloted_stock2,$tmp);
                                                    
                                                    $pen_qty = $pen_qty-$reserv_qty;

                                            // if all qty updated 
                                            if(!$pen_qty)
                                                    break;
                                            
                                    }
                            }

                            if(count($alloted_stock))
                            {
                                    foreach($alloted_stock as $allot_stk)
                                            $this->db->insert("t_reserved_batch_stock",$allot_stk);

                                    $tot_qty=array();
                                    foreach($alloted_stock2 as $stk_prod)
                                    {
                                            $stk_movtype=0;
                                            //$prod_id=0,$mrp=0,$bc='',$loc_id=0,$rb_id=0,$p_stk_id=0,$qty=0,$update_by=0,$stk_movtype=0,$update_by_refid=0,$mrp_change_updated=-1,$msg=''
                                            $rdata=$this->erpm->_upd_product_stock($stk_prod['product_id'],$stk_prod['mrp'],$stk_prod['product_barcode'],$stk_prod['location_id'],$stk_prod['rack_bin_id'],$stk_prod['stock_info_id'],$stk_prod['qty'],$updated_by,$stk_movtype,$invid,-1,$batch_remarks);
                                            if($rdata) {
                                                //Stock log updated.
                                                $stk_movtype_msg = ($stk_movtype)?' De-Alloted. ': " Alloted. ";
                                                //$tot_prdt[$stk_prod['product_id']]=true;
                                                //$success = "<br>Product-".$stk_prod['product_id']." and stock-".$stk_prod['stock_info_id'].' with '.$stk_prod['qty'].' quantity is '.$stk_movtype_msg."";
                                            }
                                            elseif(is_array($rdata)) {
                                                //$output['resp'][] = "<br>".$rdata;
                                                
                                            }else {
                                                $output['error'] = "<br>Transaction Stock log not updated.";
                                            }
                                    }
                                    
                                    $output['alloted'] = count($alloted_stock); //'<br>STOCK ALLOTED - '.$i_transid.' with '.count($alloted_stock).' product'.$stk_movtype_msg.'';

                            }
                           
                    }
                    
            }
            if($down_import_summ)
            {
			 
					$tmp = array();
					//$tmp['partner_reference_no'] = $p_oid;
					$tmp['batch_id'] = '';
					$tmp['p_invoice_no'] = '';
					if(isset($down_summary[$p_oid]))
					{
						$tmp['p_invoice_no'] = isset($down_summary[$p_oid])?$down_summary[$p_oid]:'';
						$tmp['batch_id'] = isset($batch_inv_link[$tmp['p_invoice_no']])?$batch_inv_link[$tmp['p_invoice_no']]:'';	
					}
					
					$data = implode('","',$tmp);
				
                                
                                        $output['alloted'] = "Transaction $i_transid with ".$data.'.';
                                
            }else
            {
                    if(!count($batch_inv_link)) {
                            $output['nostock'] = $total_qty;//array("transid"=> $i_transid);
                    }
            }
            return $output;
    }
    
    /**
     * Function to cancel proforma invoice
     * @param type $p_invoice int
     * @param type $update_by int
     * @param type $msg string
     * @return type array
     */
    function reservation_cancel_proforma_invoice($p_invoice,$update_by=1,$msg) {
        
            $output=array();
            $invoice=$this->db->query("select transid,order_id,p_invoice_no,p_invoice_no as invoice_no from proforma_invoices where p_invoice_no=? and invoice_status=1",$p_invoice)->result_array();
            
            if(empty($invoice)) {
                    return array("status"=> "fail", "response" => "Proforma Invoice not found or Invoice already cancelled"); 
            }
                    
                    
            $transid=$invoice[0]['transid'];
            $oids=array();
            foreach($invoice as $i)
                    $oids[]=$i['order_id'];

            $orders=$this->db->query("select quantity as qty,itemid,id from king_orders where id in ('".implode("','",$oids)."') and transid = ? ",$transid)->result_array();

            $batch_id = $this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no=?",$p_invoice)->row()->batch_id;

            $proforma_inv_det = $this->db->query("select id,is_b2b from proforma_invoices where p_invoice_no=? ",$p_invoice)->row_array();

            $proforma_inv_id = $proforma_inv_det['id'];
            $is_pnh = $proforma_inv_det['is_b2b'];


            foreach($orders as $o)
            {
                    $pls=$this->db->query("select qty,pl.product_id,p.mrp,p.brand_id from m_product_deal_link pl join m_product_info p on p.product_id=pl.product_id where itemid=?",$o['itemid'])->result_array();

                    $pls2=$this->db->query("select pl.qty,p.product_id,p.mrp,p.brand_id 
                                            from products_group_orders pgo
                                            join king_orders o on o.id = pgo.order_id 
                                            join m_product_group_deal_link pl on pl.itemid=o.itemid 
                                            join m_product_info p on p.product_id=pgo.product_id 
                                            where pgo.order_id=? and o.transid = ? ",array($o['id'],$transid))->result_array();

                    $pls=array_merge($pls,$pls2);

                    foreach($pls as $p)
                    {

                            /** Default rack bin used if brand is not linked for loc **/ 
                            $p['location'] = 1;
                            $p['rackbin'] = 10;
                            $loc_det_res = $this->db->query("select location_id,rack_bin_id from m_rack_bin_brand_link a join m_rack_bin_info b on a.rack_bin_id = b.id where brandid = ? limit 1 ",$p['brand_id']);
                            if($loc_det_res->num_rows())
                            {
                                    $loc_det = $loc_det_res->row_array();	
                                    $p['location'] = $loc_det['location_id'];
                                    $p['rackbin'] = $loc_det['rack_bin_id'];
                            }

                            $p_reserv_qty = 0;
                            //$reserv_stk_res = $this->db->query('select id,release_qty,extra_qty,stock_info_id,qty from t_reserved_batch_stock where batch_id = ? and p_invoice_no = ? and status = 1 and order_id = ? and product_id = ? ',array($batch_id,$p_invoice,$o['id'],$transid,$p['product_id']));

                            $reserv_stk_res = $this->db->query('select a.id,a.release_qty,a.extra_qty,a.stock_info_id,a.qty 
                                                                            from t_reserved_batch_stock a
                                                                            join king_orders b on a.order_id = b.id  
                                                                            where batch_id = ? and p_invoice_no = ? 
                                                                            and a.status = 0 and a.order_id = ? and b.transid = ? and product_id = ?',array($batch_id,$p_invoice,$o['id'],$transid,$p['product_id']));


                            $stk_movtype=1; //IN
                            if($reserv_stk_res->num_rows())
                            {
                                    foreach($reserv_stk_res->result_array() as $reserv_stk_det)
                                    {
                                            $rqty = $reserv_stk_det['qty']-$reserv_stk_det['release_qty']+$reserv_stk_det['extra_qty'];
                                            $p_reserv_qty += $rqty;
                                            //$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and stock_id = ? limit 1";
                                            //$this->db->query($sql,array($rqty,$p['product_id'],$reserv_stk_det['stock_info_id']));
                                            $this->db->query("update t_reserved_batch_stock set status=3 where id = ? ",array($reserv_stk_det['id']));
                                            //$this->erpm->do_stock_log(1,$rqty,$p['product_id'],$proforma_inv_id,false,true,true,-1,0,0,$reserv_stk_det['stock_info_id']);


                                            $stk_info = $this->db->query("select * from t_stock_info where stock_id = ? ",$reserv_stk_det['stock_info_id'])->row_array();

                                            if($stk_info)
                                            {
                                                     //($prod_id=0,$mrp=0,$bc='',$loc_id=0,$rb_id=0,$p_stk_id=0,$qty=0,$update_by=0,$stk_movtype=0,$update_by_refid=0,$mrp_change_updated=-1,$msg='')
                                                    $this->erpm->_upd_product_stock($stk_info['product_id'],$stk_info['mrp'],$stk_info['product_barcode'],$stk_info['location_id'],$stk_info['rack_bin_id'],0,$rqty,$update_by,$stk_movtype,$proforma_inv_id,-1,$msg);	
                                            }else
                                            {

                                                    $this->erpm->_upd_product_stock($p['product_id'],$p['mrp'],'',$p['location'],$p['rackbin'],0,$rqty,$update_by,$stk_movtype,$proforma_inv_id,-1,$msg);
                                            }
                                    }

                            }else{
                                    //($prod_id=0,$mrp=0,$bc='',$loc_id=0,$rb_id=0,$p_stk_id=0,$qty=0,$update_by=0,$stk_movtype=0,$update_by_refid=0,$mrp_change_updated=-1,$msg='')
                                    $this->erpm->_upd_product_stock($p['product_id'],$p['mrp'],'',$p['location'],$p['rackbin'],0,($p['qty']*$o['qty']),$update_by,$stk_movtype,$proforma_inv_id,-1,$msg);

                                    /*
                                    $new_stock_entry = true;
                                    $sp_det_res = $this->db->query("select product_id,mrp from t_stock_info where product_id = ? ",$p['product_id']);
                                    if($sp_det_res->num_rows())
                                    {
                                            foreach($sp_det_res->result_array() as $sp_row)
                                                    if($sp_row['mrp'] == $p['mrp'])
                                                            $new_stock_entry = false;
                                    }
                                    if($new_stock_entry)
                                             $this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,available_qty,product_barcode,created_on) values(?,?,?,?,?,?,now())",array($p['product_id'],$p['location'],$p['rackbin'],$p['mrp'],0,''));

                                    $stk_ref_id = @$this->db->query("select stock_id from t_stock_info where product_id=? and mrp=? limit 1 ",array($p['product_id'],$p['mrp']))->row()->stock_id;
                                    if($stk_ref_id)
                                    {
                                            $sql="update t_stock_info set available_qty=available_qty+? where stock_id=? limit 1";
                                            $this->db->query($sql,array($p['qty']*$o['qty'],$stk_ref_id));
                                            $this->erpm->do_stock_log(1,$p['qty']*$o['qty'],$p['product_id'],$proforma_inv_id,false,true,true,-1,0,0,$stk_ref_id);
                                    }
                                    **/
                            }
                    }

            }

            $this->db->query("update king_orders set status=0 where id in ('".implode("','",$oids)."') and transid=? ",$transid);
            $this->db->query("update proforma_invoices set invoice_status=0 and cancelled_on=? where p_invoice_no=? and transid = ? ",array(time(),$p_invoice,$transid));
            $this->erpm->do_trans_changelog($transid,"Proforma Invoice no $p_invoice cancelled");
//                $this->session->set_flashdata("erp_pop_info","Proforma Invoice cancelled");
            
            
            $bid=$this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no=?",$p_invoice)->row()->batch_id;

            $is_batch_open = $this->db->query("select count(*) as t from (
                    select  a.batch_id,a.p_invoice_no,sum(1) as total,sum(b.invoice_status) as ttl_active,sum(if(a.invoice_no,1,0)) as ttl_invoiced 
                            from shipment_batch_process_invoice_link a
                            join proforma_invoices b on a.p_invoice_no = b.p_invoice_no 
                            join shipment_batch_process c on c.batch_id = a.batch_id 
                            where a.batch_id = ?  
                    group by a.batch_id
                    having ttl_active != ttl_invoiced 
                    ) as g ",$bid)->row()->t;

                    if($is_batch_open)
                            $this->db->query("update shipment_batch_process set status=1 where batch_id=? limit 1",$bid);
                    else
                            $this->db->query("update shipment_batch_process set status=2 where batch_id=? limit 1",$bid);

            $output['status'] = "success";
            $output['p_invoice_no'] = $p_invoice;
            $output['transid'] = $transid;
            $output['response'] = "Proforma Invoice-$p_invoice cancelled under $transid transaction";
            return $output;
            //redirect("admin/proforma_invoice/$p_invoice");
    }
    
    /**
     * Get Ungrouped transaction details 
     * @param type $territory_id  int
     */
    function jx_terr_batch_group_status($territory_id) {
            $user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|OUTSCAN_ROLE|INVOICE_PRINT_ROLE);
            error_reporting(all);
            $resp=$arr_resp=$msg_category=$r_count=$arr_category=array();
            
            echo $cond = ' and f.territory_id = '.$territory_id.' ';
        
            $global_batch_id=GLOBAL_BATCH_ID;

            $rslt = $this->db->query("select distinct 
                                o.itemid,
                                d.menuid,mn.name as menuname,f.territory_id
                                ,sd.id,sd.batch_id,sd.p_invoice_no
                                ,from_unixtime(tr.init) as init 
                                    from king_transactions tr
                                    join king_orders as o on o.transid=tr.transid
                                    join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                    join shipment_batch_process_invoice_link sd on sd.p_invoice_no =pi.p_invoice_no
                                    join king_dealitems dl on dl.id = o.itemid
                                    join king_deals d on d.dealid = dl.dealid # and d.menuid in (?)
                                    join pnh_menu mn on mn.id=d.menuid
                                    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id and f.is_suspended = 0
                                    where sd.batch_id=$global_batch_id $cond
                                    order by tr.init asc")->result_array(); //,array($assigned_menuids)
            if(count($rslt)>0) {
                $resp['status'] = 'success';
                
                $c=0;
                foreach($rslt as $row) {
                    $arr_resp[$row['menuid']][++$c] = $row;
                }
                
                $total_orders=0;
                foreach($arr_resp as $i=>$r) {
                        $count = count($r);
                        $r_count["total"][$i] =$count;
                    
                        foreach($arr_resp[$i] as $j=>$inner) {
                            $r_count['menuname'][$i] = $inner['menuname'];
                            $msg_category[$i]  = '<tr><td><b>'.$inner['menuname'].'</b></td><td><b>'.$count.'</b></td></tr>';
                            
                            $arr_menus[$i]["menuid"] =$i;
                            $arr_menus[$i]["menuname"] =$inner['menuname'];
                            $arr_menus[$i]["ocount"]=$count;
                            
                            //array_push($arr_category,array("menuid"=>$i,"menuname"=>$inner['menuname'],"ocount"=>$count) );

                        }
                        $total_orders+=count($r);
                }
                
                        $total_categories= count($arr_resp);
                        
                        $resp["total_orders"]= $total_orders;
                        $resp["total_categories"]= $total_categories;
                        $resp["arr_menus"]= $arr_menus;
                        
//                        $resp["total_count_msg"]= 'There are <b>'.$total_orders."</b> orders of <b>".$total_categories.'</b> category.';
                        
                        $resp["total_count_msg"]= 'There are <b>'.$total."</b> orders of <b>".$total_categories.'</b> category.';
                        $resp["detail_category_msg"]= ''.implode('',$msg_category);
            }
            else {
                $resp['status'] = 'fail';
                $resp['response'] = 'No orders found.';
            }
            echo json_encode($resp);
    }
    
    /**
     * Function to check the territory is in un greouped batch
     * @param type $territory_id int
     * @return type boolean
     */
    function is_terri_batch_created($territory_id) {
        return $this->db->query("select e.* from proforma_invoices a 
                                    join shipment_batch_process_invoice_link b on a.p_invoice_no = b.p_invoice_no 
                                    join king_transactions c on c.transid = a.transid  
                                    join pnh_m_franchise_info d on d.franchise_id = c.franchise_id 
                                    join pnh_m_territory_info e on e.id = d.territory_id 
                                    where batch_id = ? and d.territory_id=?
                                    group by d.territory_id 
                                    order by territory_name",array(GLOBAL_BATCH_ID,$territory_id))->num_rows();
    }
    
    /**
     * Function to return batched orders
     * @return type array
     */
    function get_batches_details($userid,$latest) {
        $batched_orders=array();
        $cond='';
        if($userid!=0)
            $cond .=' and sb.assigned_userid = '.$userid;
        
        if($latest == 0)
            $orderby_cond = ' sb.batch_id ASC ';
        else
            $orderby_cond = ' sb.batch_id DESC ';
        
        $batched_orders = $this->db->query("select sb.batch_id,terr.territory_name,bc.batch_grp_name,sb.num_orders,sb.status,sb.created_on,a.username as assigned_to
                                                from shipment_batch_process sb
                                                join m_batch_config bc on bc.id=sb.batch_configid
                                                left join pnh_m_territory_info terr on terr.id=sb.territory_id
                                                join king_admin a on a.id=sb.assigned_userid
                                                where sb.batch_id > ? and sb.status in (0) $cond
                                                order by $orderby_cond",array(GLOBAL_BATCH_ID))->result_array();
//        die($this->db->last_query());
        return $batched_orders;
    }
    
    /**
     * Function to return orders of the transaction
     * @param type $transid varchar
     * @return type array 
     */
    function get_cancelled_orders_of_trans($transid) {
            $sql="select o.status,o.shipped,o.id,o.itemid,o.brandid,o.quantity,o.time,o.bill_person,o.ship_phone,o.i_orgprice,o.i_price,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value,o.member_id,o.is_ordqty_splitd
                    ,di.name
                    ,tr.init,tr.actiontime,tr.status tr_status,tr.is_pnh,tr.batch_enabled
                    ,pi.p_invoice_no
                    from king_orders o
                    join king_transactions tr on tr.transid = o.transid 
                    join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                    left join king_invoice i on o.id = i.order_id 
                    left join proforma_invoices `pi` on pi.order_id = o.id
                    join king_dealitems di on di.id = o.itemid
                    where i.id is null and tr.transid = ?
                    order by tr.init,di.name ";
             $trans_orders = $this->db->query($sql,$transid)->result_array();
             return $trans_orders;
    }
    
    
    /**
     * Get transaction info by orderid
     * @param type $order_id
     * @return type Resultset
     */
    function get_order_transaction($order_id) {
        return $this->db->query("select a.init,a.franchise_id,a.is_pnh,a.partner_id,a.transid,b.ship_person,b.ship_city from king_transactions a join king_orders b on a.transid = b.transid where b.id = ? ",$order_id)->row_array();  
    }
    
    /**
     * Function to get franchise details
     * @param type $franchise_id
     * @return type array
     */
    function get_franchise_details($franchise_id) {
        return $this->db->query("select franchise_id,franchise_name,territory_name,town_name 
                                from pnh_m_franchise_info a 
                                join pnh_m_territory_info b on a.territory_id = b.id 
                                join pnh_towns c on c.id = a.town_id
                                where franchise_id = ?  ",$franchise_id)->row_array();
    }
    
    /**
     * Get partner name
     * @param type $partner_id
     * @return type string
     */
    function get_partner_name($partner_id) {
        return $this->db->query("select name from partner_info where id = ? ",$partner_id)->row()->name;
    }
    
    /**
     * check if menu mrp is changed or not
     * 0 - no
     * 1 - yes
     * @param type $menuid
     * @return type int
     */
    function is_menu_mrp_changed($menuid) {
        return $this->db->query("select consider_mrp_chng from pnh_menu where id = ? ",$menuid)->row()->consider_mrp_chng;
    }
            /**
     * Function to note info of a transaction
     * @param type $trans_id
     * @return type array
     */
    function get_transaction_notes($p_invno_list) {
        return $this->db->query("select tnote.note,pi.transid from king_transaction_notes tnote
                                    join proforma_invoices `pi` on pi.transid=tnote.transid
                                    where tnote.note_priority=1 and pi.p_invoice_no in ($p_invno_list)
                                    group by pi.transid order by tnote.id asc limit 0,6",$trans_id)->result_array();
    }

    /**
     * By invoice no get the free sample info
     * @param type $p_invoice_no
     * @return type array
     */
    function get_free_samples($p_invoice_no) {
        return $this->db->query("select f.name,o.id,o.invoice_no from proforma_invoices i join king_freesamples_order o on o.transid=i.transid join king_freesamples f on f.id=o.fsid where i.p_invoice_no=? order by f.name",$p_invoice_no)->result_array();
    }
    
    /**
     * Function to check is product have serial number.
     * @param type $product_id
     * @return type int
     */
    function is_product_have_serial($product_id) {
        return $this->db->query("select is_serial_required from m_product_info where product_id = ? ",$product_id)->row()->is_serial_required;
    }

    /**
     * Function to get all mrp related info for packing process
     * @param type $batch_id
     * @param type $order_id
     * @param type $product_id
     * @param type $p_invoice_no
     * @param type $stock_id
     * @return type resultset
     */
    function get_mrp_alloted($batch_id,$order_id,$product_id,$p_invoice_no,$stock_id) {
        $arr_resp = $this->db->query("select rack_name,bin_name,ifnull(a.qty,0) as qty 
                                            from m_rack_bin_info c
                                            join t_stock_info b on c.id = b.rack_bin_id 
                                            left join t_reserved_batch_stock a on a.stock_info_id = b.stock_id 
                                            and a.batch_id = ? and a.order_id = ? and a.product_id = ?  and a.p_invoice_no = ?  
                                            where b.stock_id = ? ",array($batch_id,$order_id,$product_id,$p_invoice_no,$stock_id));
        return $arr_resp;
    }
    
    /**
     * get packing info like stock,product,location & rackbinids
     * @return type
     */
    function get_paking_info($product_id) {
        $s = $this->db->query("select stock_id,product_id,location_id,rack_bin_id,concat(location_id,'-',rack_bin_id) as rbid,product_barcode,sum(available_qty) as s,mrp from t_stock_info where product_id={$product_id} group by rbid,mrp,product_barcode,stock_id having sum(available_qty)>=0 order by rbid asc")->result_array();
        return $s;
    }
    
    /**
     * Get batch id from proforma invoice no
     * @param type $p_invoice_no
     * @return type int
     */
    function get_batch_id_by_invoiceno($p_invoice_no) {
        return $this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no = ? ",$p_invoice_no)->row()->batch_id;
    }
    
    /**
     * Function to get stock reservation details
     * @param type $order_id
     * @param type $p_invoice_no
     * @param type $stock_id
     * @return type
     */
    function get_stock_reservation_details($order_id,$p_invoice_no,$stock_id) {
        $res = $this->db->query("select qty from t_reserved_batch_stock where order_id = ? and p_invoice_no = ? and stock_info_id = ? ",array($order_id,$p_invoice_no,$stock_id));
        return $res;
    }
     
    /**
     * Get imei list for a product
     * @param type $product_id
     * @return type array
     */
    function get_imeis_by_product($product_id) {
        return $this->db->query("select * from t_imei_no where status=0 and product_id=?",$product_id)->result_array();
    }
    
    function get_batch_print_count($batch_id) {
        $printcount = $this->db->query("select printcount from picklist_log_reservation where batch_id=?",$batch_id)->row()->printcount;
        return $printcount;
    }
}

?>
