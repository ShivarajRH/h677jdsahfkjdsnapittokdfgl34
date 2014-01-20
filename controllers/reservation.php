<?php
/**
 * Transaction/Orders Reservation functions
 * @contact Shivaraj@storeking.in
 */
include APPPATH.'/controllers/voucher.php';
class Reservation extends Voucher {
	
    /**
    * Updating the picklist printed log into invoices
    */
    function jx_update_picklist_print_log()
    {
        $user=$this->auth(ORDER_BATCH_PROCESS_ROLE|OUTSCAN_ROLE|INVOICE_PRINT_ROLE);
        
            //$all_inv_list = $this->input->post('all_inv_list');
            $batch_id = $this->input->post('batch_id');
            if($batch_id) {
                //$all_inv_list_arr=explode(',',$all_inv_list);
                //foreach($all_inv_list_arr as $invno) {
                    $this->db->where(array("batch_id"=>$batch_id));
                    $rslt= $this->db->get('picklist_log_reservation');//$this->db->select("id,printcount");
                    if($rslt->num_rows() > 0) {
                        $row = $rslt->row_array();
                        $id=$row['id'];
                        $printcount=$row['printcount'];
                        $this->db->query('update picklist_log_reservation set printcount = `printcount` + 1 where id = '.$id.' limit 1');
                        
                        $output['printcount'] = $printcount + 1;
                        $output['response'] = 'Log updated.';
                    }
                    else {
                        //update to process_invoice table
                        $field_arr=array(
                            "batch_id"=>$batch_id
                            ,"created_by"=>$user['userid']
                            ,"createdon"=> date("Y-m-d h:i:s",time())
                            ,"printcount"=>1
                        );
                        $this->db->insert("picklist_log_reservation",$field_arr);
                        $output['printcount'] = $this->db->insert_id();
                        $output['response'] = 'Log created.';
                    }
                    
                    $output['status'] = 'success';
                    $output['lst_qry'] = $this->db->last_query();
                }
                else {
                    $output['status']= "fail";
                    $output['response']= "Provide batchid.";
                }
            echo json_encode($output);
    }
    
    /**
     * Get Ungrouped transaction details 
     * @param type $territory_id  int
     */
    function jx_terr_batch_group_status($territory_id) {
        
        $user=$this->auth(ORDER_BATCH_PROCESS_ROLE|OUTSCAN_ROLE|INVOICE_PRINT_ROLE);
        
        $cond='';
        if($territory_id != 00)
            $cond = ' and f.territory_id = '.$territory_id.' ';
            
        $rslt = $this->db->query("select distinct 
                            o.itemid,count(distinct tr.transid) as ttl_orders
                            ,bc.id as menuid,bc.batch_grp_name as menuname,f.territory_id
                            ,sd.id,sd.batch_id,sd.p_invoice_no
                            ,from_unixtime(tr.init) as init,bc.batch_size,bc.group_assigned_uid as bc_group_uids
                                from king_transactions tr
                                join king_orders as o on o.transid=tr.transid
                                join proforma_invoices as `pi` on pi.order_id = o.id and pi.invoice_status=1
                                join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no and sd.invoice_no=0
                                join king_dealitems dl on dl.id = o.itemid
                                join king_deals d on d.dealid = dl.dealid 
                                join pnh_menu mn on mn.id=d.menuid
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id and f.is_suspended = 0
                                join m_batch_config bc on find_in_set(d.menuid,bc.assigned_menuid) 
                                where sd.batch_id=? $cond
                                    group by  bc.id
                                order by tr.init asc",GLOBAL_BATCH_ID); //,array($assigned_menuids)
//        echo '<pre>'.$this->db->last_query(); die();
        if($rslt->num_rows() > 0) {
            $bc_userids = array();
            $arr_menus=array();
            $total_orders=0;
            foreach($rslt->result_array() as $i=>$r) {
                $arr_menus[$i]["menuid"] =$r['menuid'];
                $arr_menus[$i]["menuname"] =$r['menuname']." (".$r['ttl_orders'].")";
                $arr_menus[$i]["ordcount"]=$r['ttl_orders'];
                $arr_menus[$i]["batch_size"]=$r['batch_size'];
                $arr_menus[$i]["bc_group_uids"]=$r['bc_group_uids'];
                $total_orders+=$r['ttl_orders'];
                
                foreach(explode(',',$r['bc_group_uids']) as $a_uid)
                    $bc_userids[$a_uid] = $this->db->query("select username from king_admin where id = ? ",$a_uid)->row()->username;
                
            }
            
            asort($bc_userids);
            
            $resp['status'] = 'success';
            $resp["total_orders"]= $total_orders;
            $resp["bc_userids"]= $bc_userids;
            $resp["arr_menus"]= $arr_menus;
        }
        else {
            $resp['status'] = 'fail';
            $resp['response'] = 'No orders found.';
        }
        echo json_encode($resp);
    }
    
    /**
     * Function to process GROUP of invoices for packing, Group based on franchise
     */
    function pack_invoice_by_fran($batch_id,$franchise_id) {
        $user=$this->auth(ORDER_BATCH_PROCESS_ROLE|OUTSCAN_ROLE|INVOICE_PRINT_ROLE);
        
        if(isset($_POST['pids'])) {
            $this->erpm->do_pack(); die();
        }
        
        $p_invoice_ids = $this->db->query("select group_concat(distinct sd.p_invoice_no) as p_invoice_ids
                                            from shipment_batch_process_invoice_link sd
                                            join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no
                                            join king_orders o on o.id=pi.order_id
                                            join king_transactions tr on tr.transid=o.transid
                                            where sd.batch_id=? and tr.franchise_id=?",array($batch_id,$franchise_id))->row()->p_invoice_ids;
        $data['invoice'] = $invoices = $this->erpm->getinvoiceforpacking($p_invoice_ids);
        
        $data['page']="pack_invoice";
        $this->load->view("admin",$data);
    }
    
    /**
     * Function to process GROUP of invoices for packing, Group based on franchise
     */
    function pack_invoice_by_fran_old() {
        $user=$this->auth(ORDER_BATCH_PROCESS_ROLE|OUTSCAN_ROLE|INVOICE_PRINT_ROLE);
        if(isset($_POST['pids'])) {
            $this->erpm->do_pack();die();
        }
        if(!isset($_POST['p_invoice_ids'])) { show_error("Invoices not found"); }
        foreach(array("p_invoice_ids","franchise_id") as $i) 
            $$i=$this->input->post($i);
            //$result = $this->reservations->do_pack_invoice_by_fran();$data['invoice'] = $invoices = $this->reservations->get_packing_details($franchise_id,$p_invoice_ids);
            //$data['batch']=$this->erpm->getbatch($bid);$data['invoices']=$this->erpm->getbatchinvoices($bid);$data['bid']=$bid;
        $data['invoice'] = $invoices = $this->erpm->getinvoiceforpacking($p_invoice_ids);
        $data['page']="pack_invoice";
        $this->load->view("admin",$data);
    }
    
    /**
     * Process the batch by franchise
     * @param type $bid int
     */
    function process_batch_by_fran($bid)
    {
        
        $user=$this->auth(ORDER_BATCH_PROCESS_ROLE|OUTSCAN_ROLE|INVOICE_PRINT_ROLE);
        $data['batch']=$this->erpm->getbatch($bid);
        $data['invoices']=$this->erpm->getbatchinvoices($bid);
        $data['bid']=$bid;
        $data['page']="process_batch_by_fran";
        $this->load->view("admin",$data);
    }
    
    /**
     * Get all perticular franchise orders
     * @param type $franchise_id int
     * @param type $from int
     * @param type $to int
     * @param type $batch_type string
     */
    function jx_get_franchise_orders($franchise_id,$from=0,$to=0,$batch_type) {
        $user=$this->auth(ORDER_BATCH_PROCESS_ROLE|OUTSCAN_ROLE|INVOICE_PRINT_ROLE);
        $output = $picklist_btn_msg= '';
        
         if($from!=0 and $to != 0) {
            //$cond .= '  and tr.actiontime between '.$from.' and '.$to.' ';
         }else {
             //$from=$to=0;
         }
        $arr_trans_set = $this->reservations->get_trans_list($batch_type,$from,$to,$franchise_id);
       
        foreach ($arr_trans_set['result'] as $i=>$arr_trans) { $all_trans[$i] = "'".$arr_trans['transid']."'";  }
        $str_all_trans = implode(",",$all_trans);
        
        $rslt = $this->db->query("select DISTINCT o.*,tr.transid,tr.amount,tr.paid,tr.init,tr.is_pnh,tr.franchise_id,di.name
                                ,o.status,pi.p_invoice_no,o.quantity
                                ,f.franchise_id,pi.p_invoice_no
                                ,sd.batch_id
                                from king_orders o
                                join king_transactions tr on tr.transid = o.transid and o.status in (0,1) and tr.batch_enabled = 1
                                join pnh_m_franchise_info f on f.franchise_id = tr.franchise_id
                                left join king_invoice i on o.id = i.order_id and i.invoice_status = 1
                                left join proforma_invoices `pi` on pi.order_id = o.id and pi.invoice_status = 1
                                left join shipment_batch_process_invoice_link sd on sd.p_invoice_no = pi.p_invoice_no 
                                join king_dealitems di on di.id = o.itemid 
                                where f.franchise_id = ? $cond and i.id is null and tr.transid in ($str_all_trans)
                                group by o.id
                                order by tr.init desc",array($franchise_id))->result_array();//,$from,$to
                                            #,di.name
        $output = '<table width="100" class="subdatagrid">
                            <thead>
                            <tr>
                                <th style="width:25px">#</th>
                                <th style="width:100px">Trans</th>
                                <th style="width:100px">Batch ID</th>
                                <th style="width:100px">Order ID</th>
                                <th style="width:250px">Order Name</th>
                                <th style="width:25px"><input type="checkbox" value="" name="pick_all_fran" id="pick_all_fran_'.$franchise_id.'" class="pick_list_trans_grp_fran" title="Select all invoices" onclick="chkall_fran_orders('.$franchise_id.')" />PickList</th>
                                <th style="width:100px">Action</th>
                            </tr>
                            </thead>
                            <tbody>';
        //echo '<pre>'.$this->db->last_query(); die();
        $arr_tmp=array();
        foreach ($rslt as $i=>$row) {
                $invoice_action =$picklist_btn_msg=$batch_id_msg = '--';
                $batch_id=$row['batch_id'];
                
                if(!in_array($row['transid'],$arr_tmp)) {
                    $arr_tmp[]=$row['transid'];
                    $transid_msg='<a href="'.site_url('admin/trans/'.$row['transid']).'" target="_blank">'.$row['transid'].'</a>';
                    
                    if($batch_type == 'pending') {
                            $invoice_action = '<a href="javascript:void(0);" class="retry_link button button-rounded button-tiny button-caution" onclick="return reserve_stock_for_trans('.$user['userid'].',\''.trim($row['transid']).'\',0);">Re-Allot</a>';
                    }
                    else {
                            if($batch_id != GLOBAL_BATCH_ID) {
                                
                                $invoice_action = '<a class="proceed_link button button-rounded button-tiny button-action" href="pack_invoice/'.$row['p_invoice_no'].'" target="_blank">Generate Invoice</a>
                                    <a class="button button-tiny button-caution" href="javascript:void(0)" onclick="cancel_proforma_invoice(\''.$row['p_invoice_no'].'\','.$user['userid'].',0)" class="">De-Allot</a>';
                                $picklist_btn_msg = '<input type="checkbox" value="'.$row['p_invoice_no'].'" name="chk_pick_list_by_fran[]" id="chk_pick_list_by_fran" class="chk_pick_list_by_fran_'.$franchise_id.'" title="Select this for picklist" />';
                                
                            }
                    }
                }
                else {
                    
                    $transid_msg=' --||--';//'-second</a>';
                }
                //if(isset($batch_id)) $batch_id_msg = '<a href="'.site_url("admin/batch/".$batch_id).'" target="_blank">B'.$batch_id.'</a>';
                
                if($batch_id != '') { 
                    //$batch_id_msg = '<a href="'.site_url("admin/batch/".$trans_arr['batch_id']).'" target="_blank">B'.$trans_arr['batch_id'].'</a>';
                    if($batch_id == GLOBAL_BATCH_ID) {
                        $batch_id_msg = '<div class="ungrouped_item">Un-Grouped</div>';
                    }
                    else {
                        $batch_id_msg = '<a href="'.site_url("admin/batch/".$batch_id).'" target="_blank">B'.$batch_id.'</a>';
                    }
                }
                
                $output .= '<tr>
                                <td>'.++$i.'</td>
                                <td align="center">'.$transid_msg.'</td>
                                <td align="center">'.$batch_id_msg.'</td>
                                <td align="center">'.$row['id'].'</td>
                                <td><span class="info_links"><a href="pnh_deal/'.$row['itemid'].'" target="_blank">'.$row['name'].'</a></span></td>
                                <td align="center">'.$picklist_btn_msg.'</td>
                                <td align="center">'.$invoice_action.'</td>
                         </tr>';
        }
        $output .= '</tbody></table>';
        echo $output;
    }

    /**
     * creates a new batch by select menuids,batch_size
     */
    function create_batch_by_group_config() {
        $user=$this->auth(PRODUCT_MANAGER_ROLE|STOCK_INTAKE_ROLE|PURCHASE_ORDER_ROLE); $output=array();
        $output = $this->reservations->do_create_batch_by_group_config($user['userid']);
        echo json_encode($output);
    }

    /**
     * Suggest menu id under batch groupid
     * @param type $batchgroupid
     */
    function jx_suggest_menus_groupid($batchgroupid) {
        $user=$this->auth(PRODUCT_MANAGER_ROLE|STOCK_INTAKE_ROLE|PURCHASE_ORDER_ROLE);$output=array();
        $rslt = $this->db->query("select assigned_menuid,batch_size,group_assigned_uid from m_batch_config where id=?",$batchgroupid)->row_array();
        if(count($rslt)>0) {
            $output['status'] = 'success';
            $output['assigned_menuid'] = $rslt['assigned_menuid'];
            $output['batch_size'] = $rslt['batch_size'];
                $arr =array();
                $arr_uids = explode(",",$rslt['group_assigned_uid']);
                foreach($arr_uids as $i=>$userid) {
                    $arr[$i]["userid"] = $userid;
                    $arr[$i]["username"] = $this->db->query("select username from king_admin where id=?",$userid)->row()->username;
                }
            $output['group_assigned_uid'] = json_encode($arr);
        }
        else {
            $output['status'] = 'fail';
            $output['response'] = 'No data found.';
        }
        echo json_encode($output);
    }
    
    function manage_reservation_create_batch_form() {
        $user=$this->auth(PRODUCT_MANAGER_ROLE|STOCK_INTAKE_ROLE|PURCHASE_ORDER_ROLE);
        $output=array();
        
        $data['sel_terr_id'] = $this->input->post("territory_id");
        $data['sel_town_id'] = $this->input->post("townid");
        $data['sel_fran_id'] = $this->input->post("franchise_id");
        
        
        $data['batch_conf']=  $this->reservations->getBatchGroupConfig();
        
        $data['pnh_terr'] = $this->db->query("select e.* from proforma_invoices a 
                                                        join shipment_batch_process_invoice_link b on a.p_invoice_no = b.p_invoice_no 
                                                        join king_transactions c on c.transid = a.transid  
                                                        join pnh_m_franchise_info d on d.franchise_id = c.franchise_id and d.is_suspended = 0
                                                        join pnh_m_territory_info e on e.id = d.territory_id 
                                                        where a.invoice_status = 1 and batch_id = ?
                                                        group by d.territory_id 
                                                        order by territory_name",GLOBAL_BATCH_ID)->result_array();
        
        $data['pnh_towns']=$this->db->query("select id,town_name from pnh_towns order by town_name")->result_array();
        $data['userslist']=$this->db->query("select id,username from king_admin where account_blocked=0")->result_array();
        
        $this->load->view("admin/body/jx_manage_reservation_create_batch_form",$data);
    }
    
    /**
     * Process proforma ids selected 
     */
    function picklist_product_wise($batch_id,$franchise_id='') {
        $user=$this->auth(INVOICE_PRINT_ROLE);
        $cond='';$rslt_arr=array();
        if($franchise_id != '')
            $cond .=" and tr.franchise_id=".$franchise_id;
        /*$p_invoice_ids_list = $this->db->query("select group_concat(distinct sd.p_invoice_no) as p_invoice_ids from shipment_batch_process_invoice_link sd
                                            join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no
                                            join king_orders o on o.id=pi.order_id
                                            join king_transactions tr on tr.transid=o.transid
                                            where sd.batch_id=? $cond ",array($batch_id))->row()->p_invoice_ids;
        //$p_invoice_ids = explode(",",$p_invoice_ids_list);
        //foreach ($p_invoice_ids as $p_inv_id) {*/
            $arr_rslt = $arr_prod = $this->reservations->product_proc_list_for_invoice($batch_id);
            $tmp_arr=array();
            foreach($arr_rslt as $row) {
                if(!in_array($row['menuid'],$tmp_arr)) {
                    $tmp_arr[]=$row['menuid'];
                    $rslt_arr[$row['menuid']][] = $row;
                    $rslt_arr[$row['menuid']]['menuname'] = $row['menuname'];
                }
                else {
                    $rslt_arr[$row['menuid']][] = $row;
                    $rslt_arr[$row['menuid']]['menuname'] = $row['menuname'];
                }
            }
            
        //}
        $data['prods']=$rslt_arr;
        $this->load->view("admin/body/picklist_product_wise",$data);
    }
    
    /**
     * Check and reserve available stock for all transactions
     * @param string $batch_remarks
     * @param type $updated_by
     */
    function jx_reallot_frans_all_trans($updated_by,$batch_remarks='By transaction reservation system') {
        
        $user= $this->auth(PRODUCT_MANAGER_ROLE|STOCK_INTAKE_ROLE|PURCHASE_ORDER_ROLE);
        if(!isset($_POST['all_trans'])) {
            show_error("No transactions found for processing.");
        }
        $all_trans = trim($_POST['all_trans'],'"');
        
        $rslt_1 = $this->db->query("select * from (select a.transid,count(a.id) as num_order_ids,sum(a.status) as orders_status
                                                from king_orders a
                                                join king_transactions tr on tr.transid = a.transid and tr.is_pnh=1
                                                where a.status in (0,1) and tr.batch_enabled=1 and a.transid in ($all_trans)
                                                group by a.transid) as ddd
                                                where ddd.orders_status=0") or die("Error : <br>".$this->db->last_query());
        if($rslt_1->num_rows()>0) {
                $rslt_for_trans = $rslt_1->result_array();
                foreach($rslt_for_trans as $rslt) {
                        $transid = $rslt['transid'];
                        $ttl_num_orders = $rslt['num_order_ids'];

                        // Process to batch this transaction
                        $arr_result[$transid] = $this->reservations->do_batching_process($transid,$ttl_num_orders,$batch_remarks,$updated_by);

//                            print_r($arr_result); //die();

                    foreach ($arr_result as $transid=>$rslt) {
                            if(isset($rslt["nostock"]) ) {
                                $arr_result2['nostock'][$transid] = $rslt["nostock"];

                                foreach($rslt["products"] as $prodduct_id=>$stock) {
                                    $nostock_msg[$transid][] = array("product_id"=>$prodduct_id,'product_name'=>$this->get_product_name_byid($prodduct_id),"stock"=>$stock);
                                }
                            }
                            elseif(isset($rslt["alloted"])) {
                                $arr_result2['alloted'][$transid] = $rslt["alloted"];

                                foreach($rslt["products"] as $prodduct_id=>$stock) {
                                    $allotedstock_msg[$transid][] = array("product_id"=>$prodduct_id,'product_name'=>$this->get_product_name_byid($prodduct_id),"stock"=>$stock);;
                                }
                            }
                            elseif($rslt["error"] != '') {
                                $errors[$transid] = $rslt["error"];
                            }
                    }
                    $count_alloted = count($arr_result2["alloted"]);
                    $count_notalloted = count($arr_result2['nostock']); //count($count_stock['alloted']);count($arr_result2["alloted"]);
                    $output = array("status"=>"success","alloted"=>$count_alloted,"alloted_msg"=>$allotedstock_msg,"nostock"=>$count_notalloted,"nostock_msg"=>$nostock_msg,"error"=>$errors);

                }
        }
        else {
            $output = array("status"=>"fail","response"=>"No orders found for any transactions");
        }
        echo json_encode($output);
    }
    
    /**
     * Check and reserve available stock for all transactions
     * @param string $batch_remarks
     * @param type $updated_by
     */
    function jx_reserve_avail_stock_all_transaction($updated_by,$batch_remarks='By transaction reservation system') {
        $user=$this->auth(PRODUCT_MANAGER_ROLE|STOCK_INTAKE_ROLE|PURCHASE_ORDER_ROLE); $output=array();
        if($user) {
            $rslt_for_trans = $this->db->query("select * from (select a.transid,count(a.id) as num_order_ids,sum(a.status) as orders_status
                    from king_orders a
                    join king_transactions tr on tr.transid = a.transid
                    where a.status in (0,1) and tr.batch_enabled=1 and tr.is_pnh=1  #and a.transid=@tid
                    group by a.transid) as ddd
                    where ddd.orders_status=0")->result_array() or die("Error");
            
//            echo 'Count='.count($rslt_for_trans).'<pre>';
            foreach($rslt_for_trans as $rslt) {
                $transid = $rslt['transid'];
                $ttl_num_orders = $rslt['num_order_ids'];

                //echo ("$transid,$ttl_num_orders,$batch_remarks,$updated_by <br>");
                // Process to batch this transaction
                $arr_result[$rslt['transid']] = $this->reservations->do_batching_process($transid,$ttl_num_orders,$batch_remarks,$updated_by);
               
               
            }
//            print_r($arr_result); //die();
            
            foreach ($arr_result as $transid=>$rslt) {
                    if( isset($rslt["nostock"]) ) {
                        $arr_result2['nostock'][$transid] = $rslt["nostock"];
                        
                        foreach($rslt["products"] as $prodduct_id=>$stock) {
                            $nostock_msg[$transid][] = array("product_id"=>$prodduct_id,'product_name'=>$this->get_product_name_byid($prodduct_id),"stock"=>$stock);
                        }
                        
                    }
                    elseif(isset($rslt["alloted"])) {
                        $arr_result2['alloted'][$transid] = $rslt["alloted"];
                        
                        foreach($rslt["products"] as $prodduct_id=>$stock) {
                            $allotedstock_msg[$transid][] = array("product_id"=>$prodduct_id,'product_name'=>$this->get_product_name_byid($prodduct_id),"stock"=>$stock);;
                        }
                        
                    }
                    elseif($rslt["error"] != '') {
                        $errors[$transid] = $rslt["error"];
                    }
            }
                $count_alloted = count($arr_result2["alloted"]);
                $count_notalloted = count($arr_result2['nostock']); //count($count_stock['alloted']);count($arr_result2["alloted"]);
                
//                $output['result'] = array("alloted"=>$count_alloted);
//                print_r($count_stock);
                $output = array("status"=>"success","alloted"=>$count_alloted,"alloted_msg"=>$allotedstock_msg,"nostock"=>$count_notalloted,"nostock_msg"=>$nostock_msg,"error"=>$errors);
        }
        else {
            $output = array("status"=>"fail","resp"=>'You dodn\'t have access permission to do this action');
        }
        echo json_encode($output);
    }

    /**
     * Make transaction enabled for batch and allot stock
     * @param type $trans
     * @param type $ttl_num_orders
     * @param string $batch_remarks
     * @param type $updated_by
     */
    function reserve_stock_for_trans($transid,$ttl_num_orders,$updated_by,$batch_remarks='') {
        $user=$this->auth(PRODUCT_MANAGER_ROLE|STOCK_INTAKE_ROLE|PURCHASE_ORDER_ROLE);
        $batch_remarks=$batch_remarks=='' ? 'by transaction reservation system' : $batch_remarks ;
        
        // Process to batch this transaction
        $rslt[$transid] = $this->reservations->do_batching_process($transid,$ttl_num_orders,$batch_remarks,$updated_by);
//        echo '<pre>';print_r($rslt); die();

        if(isset($rslt[$transid]["nostock"])) {
                    $arr_result2['nostock'][$transid] = $rslt["nostock"];
                    /*foreach($rslt["products"] as $prodduct_id=>$stock) {
                        $nostock_msg[$transid][] = array("product_id"=>$prodduct_id,"stock"=>$stock);
                    }*/
        }
        elseif(isset($rslt[$transid]["alloted"])) {
            
                $arr_result2['alloted'][$transid] = $rslt["alloted"];
                /*foreach($rslt[$transid]["products"] as $prodduct_id=>$stock) {
                    $allotedstock_msg[$transid][] = array("product_id"=>$prodduct_id,"stock"=>$stock);;
                }*/
        }
        elseif($rslt["error"] != '') {
            $errors[$transid] = $rslt["error"];
        }

        $count_alloted = count($arr_result2["alloted"]);
        $count_notalloted = count($arr_result2['nostock']); //count($count_stock['alloted']);count($arr_result2["alloted"]);

        $output = array("status"=>"success","alloted"=>$count_alloted,"alloted_msg"=>$allotedstock_msg,"nostock"=>$count_notalloted,"nostock_msg"=>$nostock_msg,"error"=>$errors);

        echo json_encode($output);
    }
       
    /**
     * Get transaction list by batch type, Like, Ready for processing or pending or not ready transaction
     * @param type $batch_type
     * @param type $from
     * @param type $to
     * @param type $pg
     * @param type $limit
     */
    function jx_manage_trans_reservations_list($batch_type,$from=0,$to=0,$terrid=0,$townid=0,$franchiseid=0,$menuid=0,$brandid=0,$showbyfrangrp=0,$batch_group_type=0,$latest=1,$alloted_status=0,$latest_batches,$limit=10,$pg=0) 
    {
        $user=$this->auth(PRODUCT_MANAGER_ROLE|STOCK_INTAKE_ROLE|PURCHASE_ORDER_ROLE);
        $this->load->model("reservation_model");
        
        if($from != 0 and $to !=0) {
                $s=date("Y-m-d", strtotime($from));
                $e=date("Y-m-d", strtotime($to));
        }
        else {
            $s=$e=0;
        }
        /*else {                $s=date("Y-m-d",strtotime("last month"));                 $e=date("Y-m-d",strtotime("today"));        }*/
        
        $data['user']=$user;
        $data['batch_type']=$batch_type;
        $data['s']= $s;
        $data['e'] = $e;
        $data['terrid']=$terrid;
        $data['townid']=$townid;
        $data['franchiseid']=$franchiseid;
        $data['menuid']=$menuid;
        $data['brandid']=$brandid;
        $data['showbygrp']=$showbyfrangrp;
        $data['batch_group_type']=$batch_group_type;
        $data['latest']= $latest;
        $data['alloted_status']= $alloted_status;
        $data['latest_batches']= $latest_batches;
        $data['limit']=$limit;
        $data['pg']=$pg;
//        echo '<pre>';    print_r($data);die();
        if($batch_type=='assigned_batches') {
            $this->load->view("admin/body/jx_manage_trans_reservations_by_batches",$data);
        }
        elseif($batch_type=='trans_disabled') {
            $this->load->view("admin/body/jx_manage_trans_reservations_disabled",$data);
        }
        else {
            if(!$showbyfrangrp)
                $this->load->view("admin/body/jx_manage_trans_reservations_list",$data);
            else 
                $this->load->view("admin/body/jx_manage_trans_reservations_by_fran",$data);
        }
    }

    
    /**
     * Dispaly and process transaction batch status as 
     */
    function manage_trans_reservations() {
        $user=$this->auth(PRODUCT_MANAGER_ROLE|STOCK_INTAKE_ROLE|PURCHASE_ORDER_ROLE);
        /*$data['pnh_terr'] = $this->db->query("select * from pnh_m_territory_info order by territory_name")->result_array();
        $data['pnh_towns']=$this->db->query("select id,town_name from pnh_towns order by town_name")->result_array();
        $data['pnh_menu'] = $this->db->query("select mn.id,mn.name from pnh_menu mn join king_deals deal on deal.menuid=mn.id where mn.status=1 group by mn.idorder by mn.name")->result_array();
        $data['pnh_brands'] = $this->db->query("select br.id,br.name from king_brands br join king_orders o on o.brandid=br.id group by br.id order by br.name")->result_array();
        $data['s']=date("d/m/y",$from);$data['e']=date("g:ia d/m/y",$to);*/
        $data['user']=$user;
        $data['page']='manage_trans_reservations';
        $this->load->view("admin",$data);
    }
    /**
     * Cancel stock reserved and invoice generated trasactions and update the stock tables
     * @param type $p_invoice
     * @param type $update_by
     * @param type $msg
     */
    function cancel_reserved_proforma_invoice($p_invoice,$update_by=1,$msg="Proforma cancelled by reservation system")
    {
        $arr_out = $this->reservations->reservation_cancel_proforma_invoice($p_invoice,$update_by,$msg);
//        echo '<pre>';print_r($arr_out);die();
        if($arr_out['status'] == 'success') {
            $output = $arr_out;//array("status"=>"success","response"=>json_encode($arr_out));
        }
        else  {
            $output = array("status"=>"fail","response"=>$arr_out['response']);
        }
        echo json_encode($output);
    }

    /********End Orders Reservation**************/
    
    /*function jx_get_managers_executives_info() {
        $rslt = $this->db->query("select employee_id,email,gender,city,contact_no,if(job_title=4,'TM','BE') as job_role from m_employee_info where job_title in (4,5) and is_suspended=0 order by job_title ASC")->result_array();
        echo json_encode($rslt);
    }*/
    
    /**
     * Function to get batch orders list
     * @param type $batch_id int
     */
    function jx_get_batch_order_list($batch_id) {
        $this->erpm->auth();
        
        $output=array();
        $fran_orderlist_res = $this->db->query("select f.franchise_id,f.franchise_name,count(sd.p_invoice_no) as num_orders,t.territory_name,tw.town_name,sd.batch_id
                    from shipment_batch_process_invoice_link sd
                    join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no
                    join king_orders o on o.id=pi.order_id
                    join king_transactions tr on tr.transid=o.transid
                    join pnh_m_franchise_info f on f.franchise_id=tr.franchise_id
                    join pnh_m_territory_info t on t.id=f.territory_id
                    join pnh_towns tw on tw.id=f.town_id
                    where batch_id=? and sd.invoice_no = 0
                    group by f.franchise_id",$batch_id);
        
        if($fran_orderlist_res->num_rows()) {
            $output['status']='success';
            $output['lst_qry']=$this->db->last_query();
            $output['franchise_list'] = $fran_orderlist_res->result_array();
        }
        else {
            $output['status']='error';
            $output['message']='No franchise data found';
        }
        echo json_encode($output);
    }
    
    /**
     * Process proforma ids selected 
     */
    function picklist_fran_wise($batch_id,$franchise_id='') {
        $user=$this->auth(INVOICE_PRINT_ROLE);
        $cond='';$rslt_arr=array();
        if($franchise_id != '')
            $cond .=" and tr.franchise_id=".$franchise_id;
        
        /*$p_invoice_ids_list = $this->db->query("select group_concat(distinct sd.p_invoice_no) as p_invoice_ids from shipment_batch_process_invoice_link sd
                                            join proforma_invoices `pi` on pi.p_invoice_no = sd.p_invoice_no
                                            join king_orders o on o.id=pi.order_id
                                            join king_transactions tr on tr.transid=o.transid
                                            where sd.batch_id=? $cond ",array($batch_id))->row()->p_invoice_ids;
        //$p_invoice_ids = explode(",",$p_invoice_ids_list);
        //foreach ($p_invoice_ids as $p_inv_id) {*/
            $arr_rslt = $arr_prod = $this->reservations->product_proc_list_for_invoice($batch_id);
            $tmp_arr=array();
            foreach($arr_rslt as $row) {
                if(!in_array($row['franchise_id'],$tmp_arr)) {
                    $tmp_arr[]=$row['franchise_id'];
                    $rslt_arr[$row['franchise_id']][] = $row;
                    $rslt_arr[$row['franchise_id']]['franchise_name'] = $row['franchise_name'];
                }
                else {
                    $rslt_arr[$row['franchise_id']][] = $row;
                    $rslt_arr[$row['franchise_id']]['franchise_name'] = $row['franchise_name'];
                }
            }
            
        //}
//        echo '<pre>';print_r($rslt_arr);die();
        $data['prods']=$rslt_arr;
        $this->load->view("admin/body/picklist_fran_wise",$data);
    }
    
    /**
     * Function to return product name
     * @param type $prodduct_id int
     * @return type string
     */
    function get_product_name_byid($prodduct_id) {
        return $this->db->query("select product_name from m_product_info where product_id=?",$prodduct_id)->row()->product_name;
    }
}

?>
