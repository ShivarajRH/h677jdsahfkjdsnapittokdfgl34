<?php
/**
 * Partner related class methods will be included
 * @author Shivaraj<shivaraj@storeking.in>_Aug_2014
 */
include APPPATH.'/controllers/stream.php';
error_reporting(E_ALL);
ini_set('max_execution_time','36000');
ini_set('memory_limit','128M');
ini_set('display_errors',1);


//echo "sadsad";die("test");
class Partner extends Stream
{
	function __construct()
	{
		parent::__construct();
		$this->api_user = 6;
		$this->count_log_id=0;
		$this->attr_fields_len=8;
		$this->load->model("erpmodel","erpm");
		$this->load->model("partner_model","partner");
	}
	
	/**
	 * Function to get partner details
	 * @author Shivaraj<shivaraj@storeking.in>_Aug_25_2014
	 * @param type $partner_id
	 */
	function get_partner_det($partner_id)
	{
		$partner_det=$this->partner->get_partners_det_byid($partner_id);
		if($partner_det) {
			output_data($partner_det);
		}
		else {
			output_error("No partner found");
		}
	}
	
	/**
	 * Select partner page
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_04_2014
	 */
	function stk_partner_select()
	{
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		
		$data['partner_info'] = $this->partner->get_partners_det();
		$data['page']="stk_partner_select";
		$this->load->view("admin",$data);
	}
	
	/**
	 * List deals list
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_04_2014
	 * @param type $partner_id
	 */
	function stk_partner_transfer($partner_id='0',$transfer_option="0")
	{
		error_reporting(E_ALL);
		if(match_in_list($partner_id, '0') || match_in_list($transfer_option,'0'))
			redirect('/admin/stk_partner_select');
		
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		//$data['deals']=$this->erpm->pnh_getdeals();
		if($_POST)
    		$this->partner->do_transfer_orders();
		
		
		$data['ttl_cart_items_saved'] = $this->db->query("SELECT COUNT(*) AS ttl FROM pnh_api_franchise_cart_info WHERE STATUS=1 AND partner_id=? AND user_id=?",array($partner_id,$user['userid']) )->row()->ttl*1;
		$data['partner_id'] = $partner_id;
		$data['transfer_option'] = $transfer_option;
		$data['partner_info'] = $this->partner->get_partners_det_byid($partner_id);
		$data['page']="stk_partner_transfer";
		$this->load->view("admin",$data);
	}
	
	/**
	 * Ajax function to get deallist by category
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_04_2014
	 */
	function jx_deallist_bycat_partner()
	{
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		
		$brandid=$this->input->post('brandid');
		$catid=$this->input->post('catid');
		$dealid=$this->input->post('dealid');
		$type=$this->input->post('type');
		$publish=$this->input->post('publish');
		$partner_id = $this->input->post('partner_id');
                
		if(!$pre_selected_fid || $pre_selected_fid!=0)
			$pre_selected_fid==0;
		
//		if($type == 0) $deals=$this->erpm->pnh_getdeals($brandid,$catid,$fid,$dealid,$publish);
//		else $deals=$this->erpm->pnh_getdealsbycat($catid,$brandid,$type,$fid,$dealid,$publish);
		
		$deals=$this->partner->get_partner_deals($brandid,$catid,$partner_id,$dealid,$publish);
		
		$output['has_user_edit'] = 0;
//		if($this->erpm->auth(UPDATE_PRODUCT_DEAL_ROLE,true))
//			$output['has_user_edit'] = 1;
		
		if($deals)
		{
			$output['ttl_deals'] = count($deals['deals']);
			$output['status'] = 'success';
			$output['deals_lst'] = $deals;
			$output['type'] = $type;
			$output['brandid'] = $brandid;
			$output['catid'] = $catid;
			$output['publish'] = $publish;
			
		}else
		{
			$output['status'] = 'error';
			$output['error'] = 'Deals not found';
		}
		echo json_encode($output);
	}
	//==================================================================
	
    /**
	 * Ajax function to add deal to cart
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_04_2014
	 */
	function partner_jx_add_deal_tocart()
	{
		$user=  $this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		
		$partner_id=$this->input->post('partner_id');
		$itemid=$this->input->post('itemid');
		$userid=$user['userid'];//$this->input->post('userid');
		
		$pid_incart_res=$this->db->query("select * from pnh_api_franchise_cart_info where user_id=? AND partner_id=? AND ref_itemid=? AND status=1",array($userid,$partner_id,$itemid));
		
		$menuid=$this->db->query("SELECT d.menuid FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid LEFT JOIN king_brands b ON b.id = d.brandid JOIN king_categories c ON c.id = d.catid WHERE i.id=?",$itemid)->row()->menuid;

		$has_insurance=$this->db->query("select has_insurance FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid LEFT JOIN king_brands b ON b.id = d.brandid JOIN king_categories c ON c.id = d.catid WHERE i.id=?",$itemid)->row()->has_insurance;
//		echo $this->db->last_query(); die();
		if($pid_incart_res->num_rows()==0)
		{
			//$pid_det = $this->pnh_jx_loadpnhprod($fid,$pid,1,$mid);
			/*if($menuid!=112 && $mid==0)
			{

				$output['mid_status']='error';
				$output['message']='Instant Registration is required because Other than Electronic items are there in the Cart';
			}
			else if(!$pid_det)
			{
				$output['status']='error';
				$output['message']="The product is DISABLED \n or\n No product available for given id";
			}else
			{
				// check if pid can be ordered
				if($pid_det['live'])
				{
					$this->db->query("insert pnh_api_franchise_cart_info (franchise_id,pid,qty,member_id,status,added_on)values(?,?,1,?,1,now())",array($fid,$pid,$mid));
					$output['status']='success';
				}else
				{
					$output['status']='error';
					$output['message']='Item is sold out';
				}
			}*/
			//$this->db->query("insert pnh_api_franchise_cart_info (franchise_id,pid,qty,member_id,status,added_on)values(?,?,1,?,1,now())",array($partner_id,$pid,$mid));
			$this->db->query("insert pnh_api_franchise_cart_info (user_id,ref_itemid,qty,partner_id,status,added_on)values(?,?,1,?,1,now())",array($userid,$itemid,$partner_id));
			$output['status']='success';
			$output['message']='Item is acceptable to cart';
		}

		$output['ttl_cart_item']=$this->db->query("select count(*) as ttl_cart_itm from pnh_api_franchise_cart_info where user_id=? AND status=1 AND partner_id=?",array($userid,$partner_id))->row()->ttl_cart_itm;
		$output['has_insurance']=$has_insurance;
		echo json_encode($output);
	}
	
	/**
	 * Ajax function to Update deal to cart
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_04_2014
	 */
	function partner_jx_update_tocart()
	{
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		$itemid=$this->input->post('itemid');
		$partner_id=$this->input->post('partner_id');
		$userid=$user['userid'];
//			echo '<pre>'; print_r($_POST);die();
		$this->db->query("update pnh_api_franchise_cart_info set status=0 where user_id=? AND partner_id=? AND ref_itemid=?",array($userid,$partner_id,$itemid) );
//		echo '<pre>'.$this->db->last_query();die();
		$cart_item_count=$this->db->query("select count(*) as ttl_cart_itm from pnh_api_franchise_cart_info where user_id=? AND status=1 AND partner_id=?",array($userid,$partner_id))->row()->ttl_cart_itm;

		if($this->db->affected_rows()!=0)
		{
			$output['status']='success';
			$output['ttl_cart_item']=$cart_item_count;
		}
		else
		{
			$output['status']='error';
		}
		echo json_encode($output);
	}

	/**
	 * Function to get saved cart items
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_04_2014
	 */
    function part_jx_getsaved_item_incart()
    {
		error_reporting(E_ALL);
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
    	$partner_id=$this->input->post('partner_id');
    	$transfer_option=$this->input->post('transfer_option');
    	$userid=$user['userid'];

    	//$ttl_orders=0;
    	$output=array();
    	$saved_cart_res=$this->db->query("SELECT c.id as cart_rowid,i.id as itemid,i.pnh_id,i.name,i.price,i.orgprice,i.store_price,i.pic,i.member_price
											FROM `pnh_api_franchise_cart_info` c
							    			JOIN king_dealitems i ON i.id=c.ref_itemid
							    			JOIN king_deals d ON d.dealid=i.dealid
							    			WHERE c.partner_id=? AND c.status=1 and c.user_id=?",array($partner_id,$userid));
    
    	if($saved_cart_res->num_rows())
    	{
    		$output['status']='success';
    		$output['saved_cart_itms']=$saved_cart_res->result_array();
    		#$output['new_mem']=$is_new_mem;
//			echo '<pre>';print_r($saved_cart_res); die();
    		$output['saved_cart_itms']['items']=array();
    		foreach($saved_cart_res->result_array() as $i=>$cart_prod)
    		{
    			$output['saved_cart_itms']['items'][$i]=$this->partner->_partner_pnh_jx_loadpnhprod($partner_id,$cart_prod['itemid'],1,$transfer_option);
				$output['saved_cart_itms']['items'][$i]['cart_rowid']=$cart_prod['cart_rowid'];
    		}
    	}
    	else
    	{
    		$output['status']='error';
    		$output['msg']='No Items in the cart';
    	}
    	echo json_encode($output);
    }
	
		/**
	 * Function to save the updated CART QTY.
	 */
	function part_jx_update_cartqty()
	{
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		$cart_rowid=$this->input->post('cart_rowid');
		$qty=$this->input->post('cart_qty');
			
		$output=array();
		$this->db->query("update pnh_api_franchise_cart_info set qty=?, updated_on=now() where id=?",array($qty,$cart_rowid));
		if($this->db->affected_rows()!=0)
		{
			$output['status']='success';
		}
		else
		{
			$output['status']='error';
		}
			
		echo json_encode($output);
	}
	
	/**
	 * Function to list pending transfer orders
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_15_2014
	 */
	function partner_transfer_list($status=0,$transfer_option=0,$date_from=0,$date_to=0,$limit=10,$pg=0)
	{
		error_reporting(E_ALL);
		$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		$cond='';$filter=0;
		if($status !=0)
		{
			if($status==1)
			{
				$cond.=' AND st.transfer_status= 0 ';
			}
			else if($status==2)
			{
				$cond.=' AND st.transfer_status= 1 ';
			}
			else if($status==3)
			{
				$cond.=' AND st.transfer_status= 2 ';
			}
			else if($status==4)
			{
				$cond.=' AND st.transfer_status= 3 ';
			}
			$filter=1;
		}
		if($date_from!=0 && $date_to!=0) 
		{
			$dt_frm=$date_from.' 00:00:00';
			$dt_to=$date_to.' 23:59:59';
			
			$cond.=' AND st.transfer_date BETWEEN "'.$dt_frm.'" AND "'.$dt_to.'"';
			$filter=1;
		}
		
		if($transfer_option!=0)
		{
			$cond.=' AND st.transfer_option='.$transfer_option;
		}
		$sql="SELECT st.transfer_id,st.partner_id,transfer_option,st.partner_transfer_no,st.transfer_remarks,st.transfer_status,st.transfer_by,st.scheduled_transfer_date,st.transfer_date
										,a.username,p.name as partner_name,st.is_active
										FROM t_partner_stock_transfer st
										JOIN king_admin a ON a.id=st.transfer_by
										JOIN partner_info p ON p.id=st.partner_id
										WHERE 1 $cond
										order by transfer_id DESC";
		$p_qry_rslt=$this->db->query($sql);
		
		//echo '<pre>'.  $this->db->last_query();
		$ttl_rows=$p_qry_rslt->num_rows();
		
		if($ttl_rows)
		{
			$sql.=" LIMIT $pg,$limit ";
			$p_qry_rslt=$this->db->query($sql);
			$ttl_res_curr=$p_qry_rslt->num_rows();
			$p_ord_list=array();
		
			foreach($p_qry_rslt->result_array() as $i=>$pitem)
			{
				$transfer_id=$pitem['transfer_id'];
				$pitems_qry_rslt=$this->db->query("SELECT stp.id,stp.itemid,stp.item_transfer_qty,stp.transfer_status
														,di.name AS deal_name
														FROM t_partner_stock_transfer_product_link stp
														JOIN king_dealitems di ON di.id=stp.itemid
														WHERE stp.transfer_id=? order by stp.item_transfer_qty ASC",array($transfer_id));
				
				$deals=array();
				if($pitems_qry_rslt->num_rows()){
					$deals=$pitems_qry_rslt->result_array();
				}
				$p_ord_list[$i]=$pitem;
				$p_ord_list[$i]['deals']=$deals;
			}

			
			$data['pagination']=$this->partner->_prepare_pagination(site_url("admin/partner_transfer_list/$status/$transfer_option/$date_from/$date_to/$limit"),$ttl_rows,$limit,8);
			$data['pg_count_msg'] = "Showing ".(1+$pg)." to ".($ttl_res_curr+$pg)." of ".$ttl_rows;
			
			$data['p_ord_list']=$p_ord_list;
		}
		
		$data['pg']=$pg;
		$data['trasfer_status']=$status;
		$data['transfer_option']=$transfer_option;
		$data['date_from']=$date_from;
		$data['date_to']=$date_to;
		$data['filter']=$filter;
		$data['page']="partner_transfer_list";
		$this->load->view("admin",$data);
	}
	
	/**
	 * Function to detailed transfer view by transfer id
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_05_2014
	 * @param int $transfer_id
	 */
	function partner_transfer_view($transfer_id)
	{
		error_reporting(E_ALL);
		$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		$p_qry_rslt=$this->db->query("SELECT st.transfer_id,st.partner_id,st.transfer_option,st.partner_transfer_no,st.transfer_remarks,st.transfer_status,st.scheduled_transfer_date,st.transfer_date,st.transfer_by,st.is_active
										,a.username,p.name as partner_name
										FROM t_partner_stock_transfer st
										JOIN king_admin a ON a.id=st.transfer_by
										JOIN partner_info p ON p.id=st.partner_id
										WHERE transfer_id=?",$transfer_id);
		$p_ord_list=array();
		if($p_qry_rslt->num_rows()) {
			
			foreach($p_qry_rslt->result_array() as $i=>$pitem)
			{
				$transfer_id=$pitem['transfer_id'];
				$pitems_qry_rslt=$this->db->query("SELECT stp.id as tp_id,stp.itemid,stp.product_transfer_qty,stp.item_transfer_qty,stp.batch_qty,stp.scanned_qty,stp.transfer_status,stp.is_active
														,di.name AS deal_name,dl.catid,dl.brandid,dl.menuid,mn.name as menu,ct.name as category,bd.name as brand
														,ifnull(a.username,'--na--') as username,stp.modified_on,concat('".IMAGES_URL."items/small/',di.pic,'.jpg') as image_url
														,di.orgprice AS mrp,di.price,di.member_price,aa.username AS batched_by,stp.batched_on,stp.packed_on,aaa.username as packed_by,stp.product_id,p.product_name
														FROM t_partner_stock_transfer_product_link stp
														JOIN king_dealitems di ON di.id=stp.itemid
														JOIN king_deals dl ON dl.dealid=di.dealid
															JOIN m_product_info p ON p.product_id=stp.product_id
															LEFT JOIN king_menu mn on mn.id=dl.menuid
															LEFT JOIN king_categories ct ON ct.id=dl.catid
															LEFT JOIN king_brands bd ON bd.id=dl.brandid
															LEFT JOIN king_admin a ON a.id=stp.modified_by
															LEFT  JOIN king_admin aa ON aa.id=stp.batched_by
															LEFT  JOIN king_admin aaa ON aaa.id=stp.packed_by
														WHERE stp.transfer_id=? 
														GROUP BY stp.id
														ORDER BY stp.id ASC
														
													",array($transfer_id));
				
				$deals=array();
				if($pitems_qry_rslt->num_rows()){
					$deals=$pitems_qry_rslt->result_array();
				}
				$p_ord_list=$pitem;
				$p_ord_list['deals']=$deals;
			}
		}
		else {
			show_error("No transfer found.");
		}
		$data['p_ord_list']=$p_ord_list;
		$data['page']="partner_transfer_view";
//		echo '<pre>';print_r($data);
		$this->load->view("admin",$data);
	}
	
	/**
	 * Function cancel pending transfer - set is_active=0
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_30_2014
	 */
	function cancel_transfer()
	{
		error_reporting(E_ALL);
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		$transfer_id=$this->input->post('transfer_id');
		$this->db->query("UPDATE t_partner_stock_transfer SET is_active=0,transfer_status=3,modified_by=?,modified_on=? WHERE transfer_id=?",array($user['userid'],cur_datetime(),$transfer_id));
		
		if($this->db->affected_rows())
		{
			output_data("Transfer #{$transfer_id} has been Cancelled.");
		}
		else
		{
			output_error("No changes found.".$this->db->last_query());
		}
	}
	
	/**
	 * Function cancel pending transfer products - set is_active=0
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_30_2014
	 */
	function cancel_transfer_product()
	{
		error_reporting(E_ALL);
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		$tp_id=$this->input->post('tp_id');
		$itemid=$this->input->post('itemid');
		$this->db->query("UPDATE t_partner_stock_transfer_product_link SET is_active=0,transfer_status=3,modified_by=?,modified_on=? WHERE id=?",array($user['userid'],cur_datetime(),$tp_id) );
		if($this->db->affected_rows())
		{
			output_data("Transfer item #{$itemid} has been Cancelled to catch.");
		}
		else
		{
			output_error("No changes found.");
		}
	}

	/**
	 * Function to generate picklist
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_10_2014
	 */
	function partner_generate_picklist($transfer_id='')
	{
		error_reporting(E_ALL);
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		$cond='';
		if(isset($_POST['transferids']))
		{
			$transferids=trim($this->input->post("transferids"));
			$transferids=explode(',',$transferids);
			$cond.=" AND rstk.transfer_id IN ('".implode("','",$transferids)."') ";
		}
		elseif($transfer_id!='')
		{
			$transferid=$transfer_id;
			$cond.=" AND rstk.transfer_id = '".$transferid."'";
		}
		else
		{
			show_error("Stock Transfer ID not found");
		}
		$transdet_res=$this->db->query("SELECT rstk.product_id,p.product_name,SUM(rstk.qty) AS qty,CONCAT(rb.rack_name,rb.bin_name) AS rbname,stk.mrp,GROUP_CONCAT(DISTINCT stk.product_barcode) AS barcodes
											FROM t_partner_reserved_batch_stock rstk
											JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
											JOIN m_product_info p ON p.product_id=rstk.product_id
											JOIN m_rack_bin_info rb ON rb.id = stk.rack_bin_id 
											WHERE 1 $cond
											GROUP BY rstk.product_id,stk.mrp,stk.rack_bin_id,stk.location_id");
		if($transdet_res->num_rows())
		{
			$data['pic_prod_lst']=$transdet_res->result_array();
			$this->load->view('admin/body/partner_transfer_picklist',$data);
		}
		else
		{
			show_error("Transfer details not found.");
		}
	}
	
	/**
	 * Ajax function to process transfer for batch
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_08_2014
	 */
	function partner_create_batch()
	{
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		$transfer_id=$this->input->post("transfer_id");
		$params["process_partial"]=true;
		$rdata = $this->partner->do_create_stock_transfer($transfer_id);//$this->erpm->do_shipment_batch_process();
		 
		if($rdata['status'] == 'success')
		{
			output_data($rdata);
		}
		else
			output_error($rdata);
	}
	
	/**
	 * Function to create scan & pack transfer deals & products list 
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_08_2014
	 * @param int $transfer_id
	 */
	function partner_stock_scan($transfer_id)
	{
		error_reporting(E_ALL);
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		$transfer_det=array();
		$deals=array();
			
		$batch_list_res=$this->db->query("SELECT * FROM t_partner_reserved_batch_stock WHERE `status`=0 AND transfer_id=?",$transfer_id);
		if($batch_list_res->num_rows())
		{
			$batch_list = $batch_list_res->result_array();
			foreach($batch_list as $batch)
			{
				
			}
			
			$transfer_det_res=  $this->db->query("SELECT st.transfer_id,st.partner_id,st.transfer_option,st.partner_transfer_no,st.transfer_remarks,st.transfer_status,st.scheduled_transfer_date,st.transfer_date,st.transfer_by
										,a.username,p.name as partner_name
										FROM t_partner_stock_transfer st
										JOIN king_admin a ON a.id=st.transfer_by
										JOIN partner_info p ON p.id=st.partner_id
										WHERE transfer_id=? and st.transfer_status IN (1,4);",$transfer_id);
			if($transfer_det_res->num_rows()) 
			{
				$transfer_det=$transfer_det_res->row_array();
				
					$sql="SELECT rstk.product_id,sum(rstk.qty) as qty,stk.mrp,rstk.stock_info_id,replace(group_concat(DISTINCT stk.product_barcode),',','_') as barcodes,tpl.item_transfer_qty,tpl.product_transfer_qty
											,di.name AS deal_name,p.product_name,rstk.itemid,di.is_combo,SUM(rstk.qty) AS combo_ttl_prod
											,CONCAT('".IMAGES_URL."items/small/',di.pic,'.jpg') as image_url
										FROM t_partner_reserved_batch_stock rstk
										JOIN t_partner_stock_transfer_product_link tpl ON tpl.id=rstk.tp_id
										LEFT JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
										JOIN king_dealitems di ON di.id=rstk.itemid
										JOIN m_product_deal_link plnk ON plnk.product_id=rstk.product_id and plnk.itemid=rstk.itemid and plnk.is_active=1
										JOIN m_product_info p ON p.product_id=rstk.product_id
										WHERE rstk.transfer_id=?
										GROUP BY rstk.product_id
										ORDER BY di.name ASC,p.product_name ASC
										";
					$pitems_qry_rslt=$this->db->query($sql,array($transfer_id));
					
				if($pitems_qry_rslt->num_rows()){
					$deals=$pitems_qry_rslt->result_array();
					
					
					
				}
				else {
					show_error("No transfer deals found.");
				}
				
			}	
			else {
				show_error("No transfer details found.");
			}
			
			$data['batch_list']=$batch_list;
			$data['transfer_det']=$transfer_det;
			$data['transfer_prod_det']=$deals;
			$data['transfer_id']=$transfer_id;
			$data['page']='partner_stock_scan';
			
			$this->load->view("admin",$data);
		}
		else {
			show_error("Products Already Scanned OR transfer batch details not found.");
		}
	}
	
	/**
	 * Function to process partner transfer stock scan details
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_10_2014
	 */
	function partner_stock_scan_process()
	{
		error_reporting(E_ALL);
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		
		if( isset($_POST) )
		{
			$this->partner->partner_stock_scan_process();
		}
	}
	
	/**
	 * Function to view partner stock summary
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_12_2014
	 * @param int $transfer_id
	 */
	function stock_transfer_summary($transfer_id='')
	{
		error_reporting(E_ALL);
		$user=$this->erpm->auth(ORDER_BATCH_PROCESS_ROLE|PARTNER_ORDERS_ROLE);
		if($transfer_id=='')
		{
			show_error("Transfer id not given");
		}
		
		
		$p_qry_rslt=$this->db->query("SELECT st.transfer_id,st.transfer_option,st.partner_id,st.partner_transfer_no,st.transfer_remarks,st.transfer_status,st.scheduled_transfer_date,st.transfer_date,st.transfer_by
										,a.username,p.name as partner_name
										FROM t_partner_stock_transfer st
										JOIN king_admin a ON a.id=st.transfer_by
										JOIN partner_info p ON p.id=st.partner_id
										WHERE transfer_id=?",$transfer_id);
		$transfer_det=array();
		if($p_qry_rslt->num_rows())
		{
			foreach($p_qry_rslt->result_array() as $i=>$pitem)
			{
				$transfer_id=$pitem['transfer_id'];
				$pitems_qry_rslt=$this->db->query("SELECT * FROM (SELECT stp.id,stp.itemid,stp.product_id,stp.item_transfer_qty,stp.product_transfer_qty,stp.batch_qty,stp.scanned_qty,stp.transfer_status
														,di.name AS deal_name,dl.catid,dl.brandid,dl.menuid,mn.name as menu,ct.name as category,bd.name as brand
														,ifnull(a.username,'--na--') as username,stp.modified_on,concat('".IMAGES_URL."items/small/',di.pic,'.jpg') as image_url
														,di.orgprice AS mrp,di.price,di.member_price,aa.username AS batched_by,stp.batched_on,stp.packed_on,aaa.username as packed_by,p.is_serial_required
														FROM t_partner_stock_transfer_product_link stp
														JOIN king_dealitems di ON di.id=stp.itemid
														JOIN king_deals dl ON dl.dealid=di.dealid
														JOIN m_product_info p ON p.product_id=stp.product_id
															LEFT JOIN king_menu mn on mn.id=dl.menuid
															LEFT JOIN king_categories ct ON ct.id=dl.catid
															LEFT JOIN king_brands bd ON bd.id=dl.brandid
															LEFT JOIN king_admin a ON a.id=stp.modified_by
															LEFT  JOIN king_admin aa ON aa.id=stp.batched_by
															LEFT  JOIN king_admin aaa ON aaa.id=stp.packed_by
														WHERE stp.transfer_id=? 
														GROUP BY stp.id
														ORDER BY di.name ASC
														) as g
														HAVING g.scanned_qty > 0
													",array($transfer_id));
				
				$deals=array();
				if($pitems_qry_rslt->num_rows()){
					$deals=$pitems_qry_rslt->result_array();
				}
				$transfer_det=$pitem;
				$transfer_det['deals']=$deals;
			}
		}
		else
		{
			show_error("No summary found for this transfer");
		}
		$data['transfer_det']=$transfer_det;

		$data['transfer_det']=$transfer_det;
		$data['page']="partner_stock_transfer_summary";
		$this->load->view("admin",$data);
	}
	
}


