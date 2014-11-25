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
	 * Function to load partner stock transfer page
	 * @author Shivaraj<shivaraj@storeking.in>_Aug_13_2014
	 */
	function partner_stk_transfer_create()
	{
		$data['partner_info'] = $this->partner->get_partners_det();
		$data['page']="partner_stk_transfer_create";
		$this->load->view("admin",$data);
	}
	
	
	/**
	 * Ajax function to get partner product to transfer
	 * @author Shivaraj<shivaraj@storeking.in>_Aug_27_2014
	 * @param type $search_data
	 * @return JSON
	 */
	function get_partner_product_jx($search_data='')
	{
		error_reporting(E_ALL);
		$user = $this->erpm->auth();
		if(!$user)
			output_error("You are not logged in");
		$cond='';
		$cond_arg=array();
		if($search_data=='')
			$search_data = $this->input->post("search_data");
		
		if($search_data=='')
			output_error("Search data not given.");
		
		if($search_data) {
			$cond.=" AND prl.itemid=? OR prl.partner_ref_no=? OR di.name LIKE ? OR imei.imei_no=?";
			$cond_arg[]=$search_data;
			$cond_arg[]=$search_data;
			$cond_arg[]='%'.$search_data.'%';
			$cond_arg[]=$search_data;
		}
		
		
		
		$item_det_res=$this->db->query("SELECT prl.id,prl.partner_ref_no,prl.itemid,prl.partner_id,di.name,di.dealid,di.orgprice AS mrp,di.price,di.member_price,concat('".IMAGES_URL."items/small/',di.pic,'.jpg') as image_url,di.is_group,di.pnh_id
											,GROUP_CONCAT(pdl.product_id) AS product_ids
											,p.name AS partner_name,trans_prefix,p.partner_rackbinid,p.trans_mode
											FROM m_partner_deal_link prl
											JOIN partner_info p ON p.id = prl.partner_id
											JOIN king_dealitems di ON di.id=prl.itemid
											JOIN m_product_deal_link pdl ON pdl.itemid=di.id AND pdl.is_active=1
											LEFT JOIN t_imei_no imei ON imei.product_id=pdl.product_id and imei.status=1
											WHERE 1 $cond 
											GROUP BY prl.itemid
								",$cond_arg);
		//echo $this->db->last_query();
		if( $item_det_res->num_rows() ) {
			$item_det = $item_det_res->row_array();
			
			$partner_id=$item_det['partner_id'];
			$itemid=$item_det['itemid'];
			
			$partner_rackbin_id=$item_det['partner_rackbinid'];
			$mode=$item_det['trans_mode'];
			$prefix=$item_det['trans_prefix'];
			//=======================
			
			$num_prod_lnk=0;//,$partner_rackbin_id
			$avail=$this->erpm->do_stock_check(array($itemid),array(1),true,true);
			if( isset($avail[$itemid]) ) {
				$num_prod_lnk = count($avail[$itemid]);
				
				foreach( $avail[$itemid] as $prdt) {
					$product_id=$prdt['pid'];
					$prd_stk[$itemid][$product_id]['pid']=$product_id;
					$prd_stk[$itemid][$product_id]['product_name']=$prdt['product_name'];
					$prd_stk[$itemid][$product_id]['sourceable']=$prdt['status'];
					$prd_stk[$itemid][$product_id]['stk']=$prdt['stk'];
					$prd_stk[$itemid][$product_id]['is_group']=$prdt['type'];
					$prd_stk[$itemid][$product_id]['qty']=$prdt['qty'];
					
					
					//
					$prd_stk[$itemid][$product_id]['ttl_stk'] = ($this->db->query("SELECT IFNULL(SUM(a.available_qty),0)  AS t
													FROM t_stock_info  a
													JOIN m_storage_location_info b ON b.location_id = a.location_id
													JOIN m_rack_bin_info c ON c.id = a.rack_bin_id
													WHERE product_id=? AND c.is_damaged=0 AND a.available_qty > 0
													HAVING t >= 0",$product_id)->row()->t)*1;
					//echo '<br><pre>'.$this->db->last_query();
					
					$sql = "SELECT IFNULL(SUM(a.available_qty),0) AS s,a.mrp,
									a.location_id,a.rack_bin_id,
									CONCAT(c.rack_name,c.bin_name) AS rbname,c.is_damaged,
									IFNULL(product_barcode,'') AS pbarcode,
									a.stock_id,DATE_FORMAT(a.expiry_on, '%d/%b/%Y') AS expiry_on,offer_note     
									FROM t_stock_info  a 
									JOIN m_storage_location_info b ON b.location_id = a.location_id 
									JOIN m_rack_bin_info c ON c.id = a.rack_bin_id
									WHERE product_id=? AND c.is_damaged=0 AND a.available_qty > 0
									GROUP BY a.mrp,pbarcode,a.location_id,a.rack_bin_id 
									HAVING s >= 0 
									ORDER BY a.mrp ASC ";
					
					$prod_loc_list_res=$this->db->query($sql,$product_id);
					
					if( $prod_loc_list_res->num_rows() ) {
						$prod_loc_list = $prod_loc_list_res->result_array();
						foreach($prod_loc_list as $j=>$s){
							//if($s['is_damaged'] != '1') {

								$prd_stk_loc_list[$j]['pbarcode'] =$s['pbarcode']?$s['pbarcode']:'--na--';
								$prd_stk_loc_list[$j]['mrp'] = round((float)$s['mrp'],2);
								$prd_stk_loc_list[$j]['rbname'] = $s['rbname'];
								$prd_stk_loc_list[$j]['avail_stk'] = round($s['s']);
								$prd_stk_loc_list[$j]['stock_id'] = $s['stock_id'];
								
								if($s['expiry_on'] == '0000-00-00 00:00:00' || empty($s['expiry_on'])){
									$exp_on='-na-'; 
								} 
								else { 
									$exp_on=$s['expiry_on'];
								}
								$prd_stk_loc_list[$j]['expiry_on'] = $exp_on;
							//}
						}
					}
					else {
						$prd_stk_loc_list=array();
					}
					$prd_stk[$itemid][$product_id]['prod_loc_list']=$prd_stk_loc_list;
					
				}
				
			}
			
			$item_det['num_prod_lnk']=$num_prod_lnk;
			$item_det['prdts_stk']=$prd_stk;
			
			output_data(array("item_det"=>$item_det) );
			
		}
		else {
			output_error("No deal found.");
		}
	}
	
	/**
	 * Ajax function to submit transfer details
	 * @author Shivaraj<shivaraj@storeking.in>_Aug_27_2014
	 * @param type $partner_id int
	 */
	function partner_transfer_jx($partner_id)
	{
		$user = $this->erpm->auth();
		$post_data=$this->input->post('data');
		$itemid_list=$this->input->post('itemid');
		$partner_ref_no_arr=$this->input->post('partner_ref_no');
		$transfer_qty=$this->input->post('transfer_qty');
		$item_transfer_qty_arr=$this->input->post('item_transfer_qty');
		$transfer_remarks=$this->input->post('transfer_remarks');
		$transfer_exp_date=date('Y-m-d H:i:s', strtotime($this->input->post('transfer_exp_date')) );
		$created_on=date('Y-m-d H:i:s',time());

		$partner_res=$this->db->query("select a.*,b.location_id from partner_info a join m_rack_bin_info b on a.partner_rackbinid = b.id 
			where a.id = ? ",$partner_id);
		if(!$partner_res)
			output_error ("Invalid partner selected OR Partner RACKBIN Location not defined");
		
		$partner_det = $partner_res->row_array();
		//$transferid=  random_num(10);
		// process by selected items for stock transfer 
		foreach($itemid_list as $itemid) 
		{
			// fetch products linked in itemid 
			$prod_list=$transfer_qty[$itemid];
			$partner_ref_no=$partner_ref_no_arr[$itemid];
			$item_transfer_qty=$item_transfer_qty_arr[$itemid];
			
			if(!count($prod_list))
				continue;
			//$item_transfer_qty
			// Insert to log
			// 'transfer_id'=>$transferid,
			$in_arr=array('partner_id'=>$partner_id,'partner_transfer_no'=>$partner_ref_no,'transfer_remarks'=>$transfer_remarks,'scheduled_transfer_date'=>$transfer_exp_date,'transfer_status'=>1
							,'transfer_date'=>$created_on,'transfer_by'=>$user['userid']);
			//print_r($in_arr);continue;
			$this->db->insert('t_partner_stock_transfer',$in_arr);
			
			$transfer_id=  $this->db->insert_id();
			foreach($prod_list as $prod_id=>$prod_stk_list)
			{
				foreach($prod_stk_list as $pstk_id=>$req_trans_qty)
				{
					// check if require transfer qty is available in stock location for processsing transfer 
					$p_stk_res = $this->db->query("select * from t_stock_info where stock_id = ? ",$pstk_id)->row_array();
					
					// pull stock from source stock 
					$mrp = $p_stk_res['mrp'];
					$bc = $p_stk_res['product_barcode'];
					$loc_id = $p_stk_res['location_id'];
					$rb_id = $p_stk_res['rack_bin_id'];
					
					$s_imeino_list = '';
					
					$type=0;
					$st_pmsg = 'Stock Transfered To '.$prod_id.' Qty '.$req_trans_qty.' '.($s_imeino_list?('<b>IMEI LIST </b><br>'.implode('<br>',$s_imeino_list)):'').' <br> Note:'.$transfer_remarks;
					$from_stk_id=$this->erpm->_upd_product_stock($prod_id,$mrp,$bc,$loc_id,$rb_id,0,$req_trans_qty,0,$type,0,-1,$st_pmsg);	
					$this->erpm->_upd_product_deal_statusbyproduct($prod_id,$user['userid'],'Updated via stock transfer - '.$transfer_remarks);
					
					$type = 1;
					$d_loc_id = $partner_det['location_id'];
					$d_rb_id = $partner_det['partner_rackbinid'];
					$d_p_msg = 'Stock Transfered From '.$prod_id.' Qty '.$req_trans_qty.' '.($s_imeino_list?('<br><b>IMEI LIST </b><br>'.implode('<br>',$s_imeino_list)):'').' <br> Note:'.$transfer_remarks;
					$to_stock_id = $this->erpm->_upd_product_stock($prod_id,$mrp,$bc,$d_loc_id,$d_rb_id,0,$req_trans_qty,0,$type,0,-1,$d_p_msg);	
					$this->erpm->_upd_product_deal_statusbyproduct($prod_id,$user['userid'],'Updated via stock transfer - '.$transfer_remarks);
					
					if($to_stock_id)
					{
						$transfer_status=1;
					}
					
					// insert to log
					$in_arr=array('transfer_id'=>$transfer_id,'itemid'=>$itemid,'product_id'=>$prod_id,'from_stock_info_id'=>$from_stk_id,'to_stock_info_id'=>$to_stock_id,'product_transfer_qty'=>$req_trans_qty
							,'item_transfer_qty'=>$item_transfer_qty,'transfer_status'=> $transfer_status );
					$this->db->insert('t_partner_stock_transfer_product_link',$in_arr);
					
				}
			}
			
		}
		output_data("Stock Transfer Completed.");
		
	}
	#=============================================
	
}


