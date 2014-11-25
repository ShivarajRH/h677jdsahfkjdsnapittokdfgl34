<?php 
/**
 * Partner db related class methods will be included
 * @author Shivaraj<shivaraj@storeking.in>_Sep_2014
 */
class Partner_model extends Model
{
	
	function __construct()
	{
		parent::__construct();
		error_reporting(0);
	}
	
	/**
	 * Function to get Partner details
	 * @author Shivaraj<shivaraj@storeking.in>_Aug_28_2014
	 * @return boolean/Array
	 */
	function get_partners_det()
	{
		$partner_res=$this->db->query("SELECT * FROM partner_info p ORDER BY p.name ASC");
		if( $partner_res->num_rows() ) {
			return $partner_res->result_array();
		}
		else
			return false;
	}
	
	/**
	 * Function to get Partner details
	 * @author Shivaraj<shivaraj@storeking.in>_Aug_28_2014
	 * @return boolean/Array
	 */
	function get_partners_det_byid($partner_id)
	{
		$partner_res=$this->db->query("SELECT * FROM partner_info p WHERE id=?",$partner_id);
		if( $partner_res->num_rows() ) {
			return $partner_res->row_array();
		}
		else
			return false;
	}
	
	function get_partner_deals($brandid=false,$catid=false,$partner_id=false,$dealid=false,$deal_status='',$limit=100)
	{
		$cond='';
		$deals_list=array();
		if($brandid == 0 && $catid == 0 && $dealid==0)
		{
			$group_by= " group by di.id order by dl.publish desc";
		}
		else if($catid != 0 && $brandid == 0 && $dealid==0)
		{
			$cond=" and dl.catid='".$catid."'";// i.is_pnh=0 and
			$group_by= " group by di.id order by dl.publish desc";
		}
		else if($catid == 0 && $brandid != 0 && $dealid==0)
		{
			$cond=" and dl.brandid='".$brandid."' ";//and  i.is_pnh=0
			$group_by= " group by di.id order by dl.publish desc";
		}
		else if($catid != 0 && $brandid != 0 && $dealid==0)
		{
			$cond=" and dl.catid='".$catid."' and dl.brandid='".$brandid."' ";//and i.is_pnh=0
			$group_by= " group by di.id order by dl.publish desc";
		}
		else if( $dealid!=0)
		{
			$cond=" and (di.dealid='".$dealid."' OR di.id='".$dealid."')";// or i.pnh_id = '".$dealid."'
			$group_by= " group by di.id order by dl.publish desc ";
		}
		
		if($deal_status=='live')
			$cond.=' and di.live=1';
		else if($deal_status=='notlive')
			$cond.=' and di.live=0';
		
		if($partner_id)
		{
			$cond.=" AND pdl.partner_id='$partner_id'";
		}
		$sql="SELECT di.dealid,di.live,di.is_combo,di.is_group,dl.publish,dl.brandid,dl.catid,di.orgprice
								,di.price,di.name,di.pic,di.pnh_id,di.id AS itemid,di.member_price,di.live AS allow_order,
								b.name AS brand,c.name AS category,p.name AS partner_name,pdl.partner_id,GROUP_CONCAT(plnk.product_id) AS product_ids
							FROM m_partner_deal_link pdl
							JOIN king_dealitems di ON di.id=pdl.itemid
							JOIN king_deals dl ON dl.dealid=di.dealid
							JOIN king_brands b ON b.id=dl.brandid 
							JOIN king_categories c ON c.id=dl.catid 
							JOIN partner_info p ON p.id=pdl.partner_id
							JOIN m_product_deal_link plnk ON plnk.itemid=di.id AND plnk.is_active=1
							where 1 $cond and di.is_pnh=0
							$group_by ";
//		echo '<pre>'.$sql;
//		die();
		if($brandid == 0 && $catid == 0 && $dealid==0)
		{
			
			$sql.=" limit $limit";
			$deals_list['deals']=$this->db->query($sql)->result_array();
//			echo '<pre>'.  $this->db->last_query();die();
			$deals_list['src_deals']=$this->db->query("select count(*) as t from king_deals where publish=1")->row()->t;
			$deals_list['unsrc_deals']=$this->db->query("select count(*) as t from king_deals  where publish=0")->row()->t;
			$deals_list['total_rows']=$this->db->query("select count(*) as t from king_deals")->row()->t;
		}
		else
		{
			$deals_res = $this->db->query($sql)->result_array();
//			echo '<pre>'.  $this->db->last_query();die();
			$deals_list['total_rows']=count($deals_res);
			$src_count=0;
			$unsrc_count=0;

			if($deals_res)
			{
				foreach($deals_res as $d)
				{
					if($d['publish']==1)
					{
						$src_count++;
					}else if($d['publish']==0)
					{
						$unsrc_count++;
					}
					
				}
				$deals_list['deals']=$this->db->query($sql)->result_array();
				$deals_list['src_deals']=$src_count;
				$deals_list['unsrc_deals']=$unsrc_count;
			}
//			echo '<pre>';print_r($deals_list);die();
		}
		return $deals_list;
		
	}
	
	/**
	 * Ajax function to deal list in cart view
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_04_2014
	 * @param type $partner_id
	 * @param type $itemid
	 * @param type $return
	 * @param type $transfer_option 1: to partner, 2: from partner
	 * @return type
	 */
	function _partner_pnh_jx_loadpnhprod($partner_id=0,$itemid=0,$return=0,$transfer_option=1)
	{
		error_reporting(E_ALL);
		$user=$this->erpm->auth();
		$userid=$user['userid'];

		$cartdeal=$this->input->post('cartdeal');
		if($cartdeal==1)
			$publish_cond="and 1";
		else
			$publish_cond="and publish=1";
		$sql="SELECT i.id as itemid,i.is_group,d.menuid,b.name AS brand,c.name AS cat,i.id,i.is_combo,i.pnh_id AS pid,i.live,i.orgprice AS mrp,i.price,i.name,i.pic,d.publish,p.is_sourceable,i.has_insurance,i.member_price,i.mp_mem_max_qty,i.shipsin,p.product_id
								
								FROM king_dealitems i
								JOIN king_deals d ON d.dealid=i.dealid AND 1
								JOIN king_brands b ON b.id = d.brandid
								JOIN king_categories c ON c.id = d.catid
								LEFT JOIN `m_product_deal_link` l ON l.itemid=i.id and l.is_active = 1
								LEFT JOIN m_product_info p ON p.product_id=l.product_id
								LEFT JOIN `m_product_group_deal_link` g ON g.itemid=i.id and l.is_active = 1
								LEFT JOIN `products_group_pids` q ON q.product_id=l.product_id
								WHERE i.id=?";// AND is_pnh=0
								//,p.name as partner_nameJOIN m_partner_deal_link pdl ON pdl.itemid=i.id 
								//JOIN partner_info p ON p.id=pdl.partner_id
//		echo '<pre>'.$this->db->last_query(); die();
		$prod=$this->db->query($sql,$itemid)->row_array();
		//echo '<pre>'.$this->db->last_query(); die();

		if(!empty($prod))
		{
			
			//$stock=$this->erpm->do_stock_check(array($prod['id']),array(1),true);
			
			if($transfer_option==1)
				$stock_res= $this->erpm->check_deal_stock(array($prod['id']));
			elseif($transfer_option==2)
				$stock_res=  $this->erpm->check_deal_stock(array($prod['id']),$partner_id);
//			print_r($stock_res);die();

			$stock_tmp = array();
			$stock_tmp[0] = array();
			$stock_tmp[0][0] = array('stk'=>0);
			
			if($stock_res->num_rows()) {
				$stock=$stock_res->result_array();
				foreach($stock as  $plist)
				{	
					$isgrp_deal = $this->db->query("select is_group from king_dealitems where id = ? ",$plist['itemid'])->row()->is_group;
	
						if(!$plist['status'])
						{
							$prod['live']=0;
							$stock_tmp[0][0] = array('stk'=>$plist['d_stk']);
						}
						else
						{
							$prod['live']=1;
							$stock_tmp[0][0] = array('stk'=>$plist['d_stk']);
							if($isgrp_deal)
								break;
						}
				}
			}
			

			$pid=$prod['pid'];
			
			//==============<<Group deals>>=================
			$attr = "";
			
			if($prod['is_group'])
			{

			$anames=$this->db->query("select a.product_id,group_concat(concat(attr_name,':',attr_value) order by attr_id desc) as p_attr_det,
												a.is_sourceable,sum(s.available_qty)+ven_stock_qty as p_stk 
							from m_product_info a 
							join m_product_deal_link b on a.product_id = b.product_id and b.is_active = 1  
							join m_product_attributes c on c.pid = b.product_id 
							join m_attributes d on d.id = c.attr_id 
							left JOIN t_stock_info s ON s.product_id=a.product_id
													left join m_vendor_product_link vp on vp.product_id = a.product_id 
							where b.itemid = ? 
										group by a.product_id 
										having (a.is_sourceable+p_stk) ",$itemid)->result_array();
				if($anames)
				{
					$attr="";
					$attr.="<span><select class='attr' name='d_attr[".$itemid."]'>";
					foreach($anames as $a)
					{
						if(($a['is_sourceable']==1 && $a['p_stk']>=0) ||($a['is_sourceable']==0 && $a['p_stk']>0))
						{
						$attr.="<option stk='{$a['p_stk']}' value='{$a['product_id']}'>{$a['p_attr_det']}</option>";
						}
					}
					$attr.='</select></span>';
				}
			}
			foreach($this->db->query("select group_id from m_product_group_deal_link where itemid=?",$prod['id'])->result_array() as $g)
			{
				$group=$this->db->query("select group_id,group_name from products_group where group_id=?",$g['group_id'])->row_array();
				$attr.="";
				$anames=$this->db->query("select attribute_name_id,attribute_name from products_group_attributes where group_id=?",$g['group_id'])->result_array();
				foreach($anames as $a)
				{
					$attr.="<b>{$a['attribute_name']} :</b><span><select class='attr' name='{$pid}_{$a['attribute_name_id']}'>";
					$avalues=$this->db->query("select a.*,sum(available_qty) as p_stk,is_sourceable from products_group_attribute_values a join products_group_pids c on c.group_id = a.group_id join m_product_info b on c.product_id = b.product_id join t_stock_info ts on ts.product_id = b.product_id where a.attribute_name_id=? 	group by a.attribute_value_id having (is_sourceable or p_stk) ",$a['attribute_name_id'])->result_array();
					foreach($avalues as $v)
					$attr.="<option stk='{$v['p_stk']}' value='{$v['attribute_value_id']}'>{$v['attribute_value']}</option>";
					$attr.='</select></span>';
				}

			}

			$prod['attr']=$attr;
			//==============<<Group deals>>=================

			$prod['stock']=$stock=(($stock_tmp[0][0]['stk']>0)?$stock_tmp[0][0]['stk']:0);

			$prod['max_allowed_qty'] = $this->db->query("select max_allowed_qty from king_dealitems where id = ? ",$itemid)->row()->max_allowed_qty;
				
			//to save the updated cart quantity
			$cartqty = 0;
			//$cartqty_res = $this->db->query("select qty as cart_qty from pnh_api_franchise_cart_info where pid=? and franchise_id=? and status=1",array($pid,$fid));
			$cartqty_res=$this->db->query("SELECT qty AS cart_qty FROM pnh_api_franchise_cart_info WHERE user_id=? AND `status`=1 AND partner_id=? AND ref_itemid=?",array($userid,$partner_id,$itemid));
			if($cartqty_res->num_rows()) {
				$cartqty = $cartqty_res->row()->cart_qty;
			}
			
			$prod['svd_cartqty']=$cartqty;

			$allow_ordr_cond=0;

			$avail = $this->erpm->do_stock_check(array($prod['id']),array(1),true);

			if($avail)
			{
			   foreach($avail[$prod['id']] as $a)
			   {
				   if($prod['is_group']==1)
				   {
					   if($a['status']==0 && $a['stk']==0)
						   $allow_ordr_cond=0;
					   else
						   $allow_ordr_cond++;
				   }
				   elseif($prod['is_combo']==1)
				   {
					   $allow_ordr_cond=$prod['publish'];
				   }
				   else 
				   {
					   if($a['status']==0 && $a['stk']==0)
						   $allow_ordr_cond=0;
					   else 
						   $allow_ordr_cond=1;
				   }
			   }
			}
					
					
				$prod['allow_order']=$allow_ordr_cond>=1?1:0;

				$prod['is_publish']=$prod['publish'];

				$prod['is_sourceable']=$prod['is_sourceable'];

				//============< stock pending >==================
				$pending_ord_qty=0;
				$prod['pending_orders']=0;
				if($transfer_option==1)
				{
					//$stk=$this->erpm->get_product_stock($p['product_id']); 
					$pending_ord_qty=$this->erpm->_get_reserved_orderqty($prod['id']);
				}
				elseif($transfer_option==2)
				{
					$pending_ord_qty=$this->erpm->_get_reserved_orderqty($prod['id'],'part');
				}
				
				if($pending_ord_qty>0)
				{

					$prod['pending_orders']=$pending_ord_qty;//'<a href="javascript:void(0)" onclick="load_openpolist('.$p['product_id'].')" >'.$pending_ord_qty.'</b></a>';

				}
				
				$conf_stk=($stock-$pending_ord_qty);
				//============< stock pending >==================
				
				$prod['confirm_stock']=$conf_stk<=0?0:$conf_stk;
		}

		if($return)
			return $prod;
		else
			echo json_encode($prod);
	}
	
	/**
	 * Function to create transfer order
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_08_2014
	 */
	function do_transfer_orders()
	{
//		echo '<pre>'; print_r($_POST);die();
		
		$user = $this->erpm->auth();
		$partner_id=$this->input->post('partner_id');
		$transfer_option=$this->input->post('transfer_option');
		$itemid_list=$this->input->post('itemid');
		$partner_ref_no=$this->input->post('partner_ref_no');
		//$transfer_qty=$this->input->post('transfer_qty');
		$item_transfer_qty_arr=$this->input->post('qty');//item_transfer_
		$transfer_remarks=$this->input->post('transfer_remarks');
		$transfer_exp_date=date_convert($this->input->post('transfer_exp_date'));
		$created_on=date('Y-m-d H:i:s',time());
		
//		echo '<pre>'.$transfer_exp_date; print_r($_POST);die();
		$in_arr=array('partner_id'=>$partner_id,'transfer_option'=>$transfer_option,'partner_transfer_no'=>$partner_ref_no,'transfer_remarks'=>$transfer_remarks,'scheduled_transfer_date'=>$transfer_exp_date
						,'transfer_status'=>0,'transfer_date'=>$created_on,'transfer_by'=>$user['userid']);
		//print_r($in_arr);continue;
		$this->db->insert('t_partner_stock_transfer',$in_arr);
		$transfer_id= $this->db->insert_id();
		
		foreach($itemid_list as $sno=>$itemid) {
			
			//$partner_ref_no=$partner_ref_no_arr[$itemid];
			$item_transfer_qty=$item_transfer_qty_arr[$sno];
			
			if($item_transfer_qty > 0)
			{
				// get product list
				$prd_lnk_res=$this->db->query("select * from m_product_deal_link where is_active=1 and itemid=?",$itemid);
				if($prd_lnk_res->num_rows())
				{
					foreach($prd_lnk_res->result_array() as $prod)
					{
						// insert to linked item product list
						$in_arr=array('transfer_id'=>$transfer_id,'itemid'=>$itemid,'product_id'=>$prod['product_id'],'from_stock_info_id'=>0,'to_stock_info_id'=>0,'product_transfer_qty'=>$prod['qty']
								,'item_transfer_qty'=>$item_transfer_qty,'transfer_status'=> 0 );
						$this->db->insert('t_partner_stock_transfer_product_link',$in_arr);

					}
				}
			}
			
		}
		// clear cart
		$this->db->query("UPDATE pnh_api_franchise_cart_info SET status=0,updated_on=NOW() WHERE partner_id=? AND user_id=?",array($partner_id,$user['userid']));
		
		$this->session->set_flashdata("erp_pop_info","Transfer Created Successfully");
		redirect("admin/stk_partner_transfer/".$partner_id."/".$transfer_option);
	}
	
	
	/**
	 * Function to know the transfer current status
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_06_2014
	 * @param int $transfer_status
	 * @return string
	 */
	function get_transfer_status($transfer_status)
	{
		$data= $color='';
		if($transfer_status==0) {
			$data= '<div class="button button-flat-primary button-tiny button-rounded">Pending</div>';
			$color='white';
		}
		elseif($transfer_status==1) {
			$data= '<div class="button button-flat-royal button-tiny button-rounded">Reserved</div>';
			$color='white';
		}
		elseif($transfer_status==2) {
			$data= '<div class="button button-flat-action button-tiny button-rounded">Processed</div>';
			$color='white';
		}
		elseif($transfer_status==3) {
			$data= '<div class="button button-flat-caution button-tiny button-rounded">Cancelled</div>';
			$color='red';
		}
		elseif($transfer_status==4) {
			$data= '<div class="button button-flat-highlight button-tiny button-rounded">Partial</div>';
			$color='red';
		}
		return array('color'=>$color,"msg"=>$data);
	}
	
	/**
	 * Function to know the transfer orders current status
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_06_2014
	 * @param type $transfer_ord_status
	 * @return type
	 */
	function get_transfer_ord_status($transfer_ord_status)
	{
		$data= $color='';
		if($transfer_ord_status==0) {
			$data= '<div class="button button-flat-primary button-tiny button-rounded">Pending</div>';
			$color='red';
		}
		elseif($transfer_ord_status==1) {
			$data= '<div class="button button-flat-royal button-tiny button-rounded">Reserved</div>';
			$color='red';
		}
		elseif($transfer_ord_status==2) {
			$data= '<div class="button button-flat-action button-tiny button-rounded">Packed</div>';
			$color='red';
		}
		elseif($transfer_ord_status==3) {
			$data= '<div class="button button-flat-caution button-tiny ">Cancelled</div>';
			$color='red';
		}
		elseif($transfer_ord_status==4) {
			$data= '<div class="button button-flat-highlight button-tiny button-rounded">Partial</div>';
			$color='red';
		}
		return array('color'=>$color,"msg"=>$data);
	}
	
	/**
	 * Function to get batch/packing status of transfer
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_15_2014
	 * @param type $transfer_id
	 * @return string 
	 */
	function get_stk_reserve_status($transfer_id) {
		$reserv_det_res=$this->db->query("SELECT max(status) as status FROM t_partner_reserved_batch_stock WHERE transfer_id=? ",$transfer_id);
		if($reserv_det_res->num_rows())
		{
			$reserv_det=$reserv_det_res->row();
			return $reserv_det->status;
		}
		else
		{
			return 0; // batch not created
		}
	}
	
	/**
	 * Supporting function to create pagination settings
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_08_2014
	 * @param string $pagi_base_url
	 * @param int $total_rows
	 * @param int $per_page
	 * @param int $uri_segment
	 * @return string
	 */
	function _prepare_pagination($pagi_base_url,$total_rows,$per_page,$uri_segment)
	{
		$config['base_url'] = $pagi_base_url;
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $per_page;
		$config['uri_segment'] = $uri_segment;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$pagination = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		return $pagination;
		
	}
	
	/**
	 * Function to process create batch on transfer list 
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_04_2014
	 * @param int $transfer_id
	 */
	function do_create_stock_transfer($transfer_id,$partial_process=true)
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		
		$user = $this->erpm->auth();
		$logged_userid = $user['userid'];
		
		// Get deals available in transfer id 
		$trans_deallist_res = $this->db->query("SELECT st.partner_id,st.transfer_option,stp.transfer_id,stp.itemid,stp.item_transfer_qty 
								FROM t_partner_stock_transfer_product_link stp
								JOIN t_partner_stock_transfer st ON st.transfer_id=stp.transfer_id
								WHERE stp.transfer_status = 0 AND st.transfer_status=0 AND st.is_active=1 AND st.transfer_id = ?
								GROUP BY stp.itemid ",$transfer_id);
		if(!$trans_deallist_res->num_rows())
		{
			return array('status'=>'fail','message'=>'No Products are in pending status');
		}else
		{
			// Get deals in array 
			$ttl_items_processed = 0;
			$ttl_items_inlist = $trans_deallist_res->num_rows();
			
			$trans_deallist = $trans_deallist_res->result_array();
			foreach($trans_deallist as $trans_deal)
			{
				$partner_id=$trans_deal['partner_id'];
				$transfer_option=$trans_deal['transfer_option'];
				$itemid=$trans_deal['itemid'];
				$item_transfer_qty=$trans_deal['item_transfer_qty'];
				
				if($partial_process)
				{
					// reset to deal avaialable qty 
//					$deal_avail_qty = $this->_get_deal_availstk($itemid);
//					if($deal_avail_qty < $item_transfer_qty)
//						$item_transfer_qty = $deal_avail_qty;
				}
				
				// get item product list 
				$trans_product_list_res = $this->db->query("select stl.id as tp_id,stl.transfer_id,stl.itemid,stl.item_transfer_qty,orgprice as deal_mrp 
								,stl.product_id,stl.product_transfer_qty
								from t_partner_stock_transfer_product_link stl
								JOIN m_product_deal_link pdl on pdl.itemid=stl.itemid
								join king_dealitems di on di.id = pdl.itemid 
								where stl.transfer_id = ? and stl.itemid = ? and stl.transfer_status = 0 group by stl.product_id ",array($transfer_id,$itemid));
				
//				echo $this->db->last_query();die();
				if(!$trans_product_list_res->num_rows())
					continue;
				
				$item_prod_stk_det = array();
				
				// check if deal can be processed 
				$process = 0;
				foreach($trans_product_list_res->result_array() as $trans_prod)
				{
					$tp_id =$trans_prod['tp_id'];
					$product_id=$trans_prod['product_id'];
					$prod_transfer_qty=$trans_prod['product_transfer_qty'];
					$req_mrp = $trans_prod['deal_mrp'];
							
					$req_qty=$item_transfer_qty*$prod_transfer_qty;
					
					// reset required qty by check deal available qty 
					
					// check if required stock is available 
					$pen_qty = $req_qty;
					
					#=======================
					$msg='Partner stock transfer - '.$transfer_id;
					
					$cond='';
					$rack_cond=" AND b.is_damaged=0 ";
					if($transfer_option==2)
					{
						$rack_cond="";
						$cond.=" AND pt.id= ".$partner_id;
						
						$msg='Return from partner - '.$transfer_id;
					}
					#=======================
					$sql = "SELECT is_serial_required,a.stock_id,a.product_id,IF(IFNULL(c.imei_no,0),COUNT(DISTINCT c.imei_no),available_qty) AS available_qty,a.location_id,GROUP_CONCAT(DISTINCT IFNULL( c.grn_id,0)) AS grn_id,a.rack_bin_id,a.mrp,IF(town_id=f.town_id,1,0) AS town_diff,IF((a.mrp-$req_mrp),1,0) AS mrp_diff
									FROM t_stock_info a
									JOIN m_rack_bin_info b ON a.rack_bin_id = b.id $rack_cond
									JOIN m_product_info p ON p.product_id = a.product_id
									LEFT JOIN t_imei_no c ON c.product_id=a.product_id AND c.status = 0 AND c.order_id = 0 AND a.stock_id = c.stock_id AND reserved_batch_rowid=0
									LEFT JOIN t_grn_product_link d ON d.grn_id = c.grn_id AND c.product_id = d.product_id
									LEFT JOIN t_grn_info e ON e.grn_id = d.grn_id
									LEFT JOIN m_vendor_town_link f ON f.vendor_id = e.vendor_id AND f.brand_id = p.brand_id 
									
									LEFT JOIN partner_info pt ON pt.partner_rackbinid=a.rack_bin_id AND pt.partner_rackbinid!=0 
									 
								WHERE a.mrp > 0 AND a.product_id = ? AND available_qty > 0  $cond
								GROUP BY stock_id,town_diff
								ORDER BY a.product_id DESC,town_diff DESC,mrp_diff,a.mrp";
		
					$stk_prod_list = $this->db->query($sql,$product_id);
//					echo $this->db->last_query();die();
					
					$alloted_stock = array();
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
								$tmp['stock_info_id'] = $stk_prod['stock_id'];
								$tmp['product_id'] = $stk_prod['product_id'];
								$tmp['rqty'] = $pen_qty;
								$tmp['qty'] = $reserv_qty;
								
								array_push($alloted_stock,$tmp);

								$pen_qty = $pen_qty-$reserv_qty;

							// if all qty updated 
							if(!$pen_qty)	
								break;
						}
					}
					
					
//					echo $pen_qty; die();
					
					// check required stk is avaiable to process deal stock 
					if(!$pen_qty)
					{
						foreach($alloted_stock as $astk)
						{
							$a_stk_id = $astk['stock_info_id'];
							$item_prod_stk_det[$product_id][$a_stk_id] = array('tp_id'=>$tp_id,'allot_qty'=>$astk['qty'],'src_stk_id'=>$a_stk_id);
						}
						$process = 1;
					}
				}
//				echo '<pre>'; print_r($item_prod_stk_det); die();
				
				if($process)
				{
					foreach($item_prod_stk_det as $product_id=>$prod_stk_allot_list)
					{
						$tp_id = 0; $ttl_allot_qty=0;
						foreach($prod_stk_allot_list as $prod_stk_det)
						{
							$tp_id = $prod_stk_det['tp_id'];
							$p_stk_id = $prod_stk_det['src_stk_id'];
							$allot_qty = $prod_stk_det['allot_qty'];

							// fetch stock info by stock id 
							$stk = $this->db->query("SELECT * FROM t_stock_info WHERE stock_id = ? ",$p_stk_id)->row_array();
							
							
							$p_stk_ref_id = $this->erpm->_upd_product_stock($stk['product_id'],$stk['mrp'],$stk['product_barcode'],$stk['location_id'],$stk['rack_bin_id'],$p_stk_id,$allot_qty,7,0,$transfer_id,-1,$msg);
							$this->erpm->_upd_product_deal_statusbyproduct($stk['product_id'],$logged_userid,$msg);		
							
							
							// push to batch stock reservation 
							$sql_insstkresv = "INSERT INTO t_partner_reserved_batch_stock
													(transfer_id,transfer_option,product_id,stock_info_id,itemid,tp_id,qty,extra_qty,release_qty,reserved_on,released_on)
													VALUES 
													(?,?,?,?,?,?,?,?,?,NOW(),?)
											";
							
							$this->db->query($sql_insstkresv,array($transfer_id,$transfer_option,$product_id,$p_stk_ref_id,$itemid,$tp_id,$allot_qty,0,0,0)) or die(mysql_error()); 
							$reserv_id = $this->db->insert_id();
							$ttl_allot_qty += $allot_qty;
						}
						
						// push stock alloted details to transfer product link table 
						
						$this->db->query("UPDATE
													t_partner_stock_transfer_product_link 
													SET transfer_status=1,
														batch_qty=?,
														batched_on = NOW(),
														batched_by = ?,
														modified_on=NOW(),
														modified_by=? 
														WHERE id=? AND product_id = ? AND itemid = ? AND transfer_id = ? 
										",array($ttl_allot_qty,$logged_userid,$logged_userid,$tp_id,$product_id,$itemid,$transfer_id)) or die(mysql_error());
						
					}
					
					$ttl_items_processed++;
				}
			}
			// update transfer table
			if($ttl_items_processed>0)
			{
				$this->db->query("UPDATE t_partner_stock_transfer SET transfer_status=1,modified_on=NOW(),modified_by=? WHERE transfer_id=?",array($logged_userid,$transfer_id) );
				
				// if partial items processed
				if($ttl_items_processed!=$ttl_items_inlist)
					$this->db->query("UPDATE t_partner_stock_transfer SET transfer_status=4,modified_on=NOW(),modified_by=? WHERE transfer_id=?",array($logged_userid,$transfer_id) );
			}
			
			return array('status'=>'success','ttl_items_processed'=>$ttl_items_processed,'ttl_items_inlist'=>$ttl_items_inlist,'message'=>"Total $ttl_items_processed/$ttl_items_inlist Deals Processed to Batch");
			
//			$this->session->set_flashdata("erp_pop_info","Total $ttl_items_processed/$ttl_items_inlist Deals Processed to Batch");	redirect('admin/partner_transfer_view/'.$transfer_id,'refresh');
			
		}
	}
	
	/**
	 * Function to process partner transfer stock scan details
	 * @author Shivaraj<shivaraj@storeking.in>_Sep_11_2014
	 */
	function partner_stock_scan_process()
	{
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		
		$user = $this->erpm->auth();
		$userid = $user['userid'];
		
		$scan_transfer_id=$this->input->post('transfer_id');
		
		$scan_det=$this->input->post('scan_det');
		$imei_list=$this->input->post('imei_list');
		
		$imeis = array();
			
		
//		echo '<pre>';
//		print_r($_POST);
		
		if($scan_transfer_id=='')
			show_error("Invalid transferid");
	
		$trans_det_res=$this->db->query("SELECT rstk.id AS rlog_id,rstk.transfer_id,rstk.transfer_option,rstk.product_id,rstk.stock_info_id,rstk.itemid,rstk.qty,rstk.extra_qty,rstk.release_qty,rstk.status
												,tdl.partner_id,tdl.transfer_remarks,rstk.tp_id
											FROM t_partner_reserved_batch_stock rstk
											JOIN t_stock_info stk ON stk.stock_id=rstk.stock_info_id
											JOIN t_partner_stock_transfer tdl ON tdl.transfer_id=rstk.transfer_id
											LEFT JOIN partner_info p ON p.id=tdl.partner_id
											WHERE rstk.status=0 AND rstk.transfer_id=?",$scan_transfer_id);
		if($trans_det_res->num_rows())
		{
			$trans_det=$trans_det_res->result_array();
//			print_r($trans_det);
//			die();
			foreach($trans_det as $trans)
			{
				//$rlog_id=$trans['rlog_id'];
				$tp_id=$trans['tp_id'];
				$partner_id=$trans['partner_id'];
				$transfer_option=$trans['transfer_option'];
				$transfer_remarks=$trans['transfer_remarks'];
				
				$itemid=$trans['itemid'];
				$transfer_id=$trans['transfer_id'];
				$product_id=$trans['product_id'];
				$stock_info_id=$trans['stock_info_id'];
				$qty=$trans['qty'];
				$s_imeino_list=array();
				
				
				if($scan_transfer_id==$transfer_id)
				{
					$transfer_partial=$transfer_full=0;
					$scan_barcode=$scan_det[$itemid][$product_id][$stock_info_id];

					if($transfer_option==1)
						$msg='Partner stock transfer - '.$transfer_id;
					else
						$msg='Return Partner stock - '.$transfer_id;
					
					foreach($scan_barcode as $bc=>$scan_qty)
					{
						
						// check if require transfer qty is available in stock location for processsing transfer 
						$p_stk_res = $this->db->query("select * from t_stock_info where stock_id = ? ",$stock_info_id)->row_array();

						// pull stock from source stock 
						$mrp = $p_stk_res['mrp'];
						$bc = $p_stk_res['product_barcode'];
						$loc_id = $p_stk_res['location_id'];
						$rb_id = $p_stk_res['rack_bin_id'];

						
						
						if( $imei_list )
						{
							if( isset($imei_list[$itemid][$product_id][$stock_info_id]) )
							{
								
								
								$scan_imei_arr=$imei_list[$itemid][$product_id][$stock_info_id];
								foreach($scan_imei_arr as $scan_imei)
								{
										$s_imeino_list[$scan_imei] = $scan_imei;
								}
							}
						}
						
						
						if($scan_qty==$qty)
						{
							//echo 'fulfilled';
							// all qty scanned
							

							#2 update to reservation table ========================
							$this->db->query("UPDATE t_partner_reserved_batch_stock SET status=1 where transfer_id=? AND itemid=? AND product_id=? AND stock_info_id=?",array($transfer_id,$itemid,$product_id,$stock_info_id));
							
							#3 update to stock transfer product link table ==================
							$this->db->query("UPDATE t_partner_stock_transfer_product_link SET transfer_status=2,scanned_qty=scanned_qty+?,is_active=0,packed_on=now(),packed_by=? where transfer_id=? AND itemid=? AND product_id=?",array($scan_qty,$userid,$transfer_id,$itemid,$product_id));
							
							$transfer_full+=1;
						}
						else {
							//echo 'partial';
							if($scan_qty==0)
								$transfer_sts=3; // deal cancelled
							else
								$transfer_sts=4; // partial
							
							if($scan_qty < $qty)
							{
									// release
									$release_qty=$qty-$scan_qty;

									#2 update to reservation table ========================
									$this->db->query("UPDATE t_partner_reserved_batch_stock SET status=2,release_qty=?,released_on=now() where transfer_id=? AND itemid=? AND product_id=? AND stock_info_id=?",array($release_qty,$transfer_id,$itemid,$product_id,$stock_info_id));

									#3 update to stock transfer product link table ==================
									$this->db->query("UPDATE t_partner_stock_transfer_product_link SET transfer_status=?,scanned_qty=scanned_qty+?,is_active=0,packed_on=now(),packed_by=? where transfer_id=? AND itemid=? AND product_id=?",array($transfer_sts,$scan_qty,$userid,$transfer_id,$itemid,$product_id));
												
									// revert taken stock to same location
									$type=1;
									$st_pmsg = 'PST Release To '.$product_id.' Qty '.$release_qty.' '.($s_imeino_list?('<b>IMEI LIST </b><br>'.implode('<br>',$s_imeino_list)):'').' <br> Note:'.$transfer_remarks;

									$prev_stk=$curr_stk=0;
									$prd_det=array();

									//Stock before update
									$prev_stk=$this->erpm->check_product_stock($product_id);

									if(!$prev_stk)//If not in stock updated log 
										$prev_stk=$this->db->query("select ifnull(sum(available_qty),0) as qty from t_stock_info where product_id=?",$product_id)->row()->qty;

									//Expected stock after update
									$exp_stk=$prev_stk+$release_qty;
										
									//Update stock quantity by stock id
									$from_stk_id=$this->erpm->_upd_product_stock($product_id,$mrp,$bc,$loc_id,$rb_id,$stock_info_id,$release_qty,7,$type,$transfer_id,-1,$st_pmsg);	

									//Current stock after update
									$curr_stk=$this->erpm->check_product_stock($product_id);

									//Check if any mismatch
									if($exp_stk != $curr_stk)							
									{
										$prd_det['product_id']=$product_id;
										$prd_det['prev_stk']=$prev_stk;
										$prd_det['curr_stk']=$curr_stk;
										$prd_det['exp_stk']=$exp_stk;
										$prd_det['type']='In';
										//Notify to technology department if any issue....
										$this->erpm->stock_error_notify($prd_det);
									}
									
									$this->erpm->_upd_product_deal_statusbyproduct($product_id,$userid,$msg);
									//echo "<br>fromTEMPLOCA::::$product_id,$mrp,$bc,$loc_id,$rb_id,0,$release_qty,0,$type,0,-1,$st_pmsg";

							}
							else
							{
												// extra
												$extra=$scan_qty-$qty;
												//out the qty
							}
							
							// partial scan
							$transfer_partial+=1;
						}
						
												
						
							
						#1 transfer temp location to FBA rackbin ====================
						
						
						// process imei list associated to that product 
						
						
						
						if($transfer_option==1) {
							$st_msg1='Stock Transfered From ';
							
							$partner_res=$this->db->query("SELECT a.*,b.location_id 
																FROM partner_info a 
																JOIN m_rack_bin_info b ON a.partner_rackbinid = b.id 
																WHERE a.id = ? ",$partner_id);
							$partner_det = $partner_res->row_array();
							$type = 1;
							$d_loc_id = $partner_det['location_id'];
							$d_rb_id = $partner_det['partner_rackbinid'];

						}
						else {
							
							$st_msg1='Return Transfered Stock From ';
							
							$partner_res=$this->db->query("SELECT st.stock_id,st.product_id,st.location_id,st.rack_bin_id,st.mrp,st.expiry_on,st.available_qty
															FROM  t_stock_info st
															JOIN m_rack_bin_info b ON b.id=st.rack_bin_id
															WHERE b.is_damaged!=1 AND st.product_id=?
															ORDER BY st.available_qty DESC LIMIT 1 ",$product_id);
							$partner_det = $partner_res->row_array();
							$type = 1;
							$d_loc_id = $partner_det['location_id'];
							$d_rb_id = $partner_det['rack_bin_id'];

						}
						
						$d_p_msg = $st_msg1.' '.$product_id.' Qty '.$scan_qty.' '.($s_imeino_list?('<br><b>IMEI LIST </b><br>'.implode('<br>',$s_imeino_list)):'').' <br> Note:'.$transfer_remarks;
						
						
						$to_stock_id = $this->erpm->_upd_product_stock($product_id,$mrp,$bc,$d_loc_id,$d_rb_id,0,$scan_qty,7,$type,$transfer_id,-1,$d_p_msg);	
						$this->erpm->_upd_product_deal_statusbyproduct($product_id,$userid,$msg);
						
						
						// process imei list associated to that product 
						
						if( $imei_list )
						{
							
							
							if( isset($imei_list[$itemid][$product_id][$stock_info_id]) )
							{
								
								
								$scan_imei_arr=$imei_list[$itemid][$product_id][$stock_info_id];
								foreach($scan_imei_arr as $scan_imei)
								{
										
										// IMEI table update
										$this->db->query("update t_imei_no set stock_id=? where imei_no=? and status = 0 limit 1",array($to_stock_id,$scan_imei));

										// IMEI LOG table update
										$imei_upd_det = array();
										$imei_upd_det['imei_no'] = $scan_imei;
										$imei_upd_det['product_id'] = $product_id;
										$imei_upd_det['stock_id'] = $to_stock_id;
										$imei_upd_det['grn_id'] = 0;
										$imei_upd_det['alloted_order_id'] = 0;
	//									$imei_upd_det['alloted_on'] = cur_datetime() ;
										$imei_upd_det['invoice_no'] = 0;
										$imei_upd_det['transfer_prod_link_id'] = $tp_id;
										$imei_upd_det['return_id'] = 0;
										$imei_upd_det['is_active'] = 1;
										$imei_upd_det['logged_by'] = $user['userid'];
										$imei_upd_det['logged_on'] = cur_datetime();
										$this->db->insert('t_imei_update_log',$imei_upd_det);

								}
							}
						}
						
						
						
						//echo "<br>toFBA:::::$product_id,$mrp,$bc,$d_loc_id,$d_rb_id,0,$scan_qty,0,$type,0,-1,$d_p_msg";
						//continue;
						
					}
					
					if($transfer_partial==0)
						$transfer_sts=2;
					else
						$transfer_sts=4;
							
					#4 update to stock transfer table ==================
					$this->db->query("UPDATE t_partner_stock_transfer SET transfer_status=?,is_active=0,modified_on=now(),modified_by=? where transfer_id=?",array($transfer_sts,$userid,$transfer_id));
					
					
				}
			}
//			echo "Processed successfully";
			$this->session->set_flashdata("erp_pop_info","Successfully transfer deals has been scanned.");
			redirect("admin/stock_transfer_summary/".$transfer_id);
			
		}
		else
		{
			show_error("This stock trnasfer already processed.");
		}
		


	}
	
	/**
	 * Function to get product partner location available stock
	 * @author Shivaraj<shivaraj@storeking.in>_Oct_20_2014
	 * @return int
	 */
	function get_partner_avail_stock($product_id)
	{
		$stk=$this->db->query("SELECT IFNULL(SUM(stk.available_qty),0) AS available_qty FROM t_stock_info stk
												JOIN m_product_info p ON p.product_id=stk.product_id
												JOIN partner_info pt ON pt.partner_rackbinid=stk.rack_bin_id
												WHERE stk.available_qty >0 AND stk.product_id=? ",array($product_id) )->row()->available_qty;
		return $stk;
	}
	
	/**
	 * Function to get partner product months sale
	 * @author Shivaraj<shivaraj@storeking.in>_Oct_20_2014
	 * @param int $product_id
	 * @param date $from_date
	 * @param int $from_months
	 * @return int
	 */
	function get_partner_sales($product_id,$to_date='',$from_months=2)
	{
		if($to_date=='')
			$to_date=cur_datetime ();
		
		$ttl_part_sold=$group_ttl_part_sold=0;
		#AND o.time BETWEEN (UNIX_TIMESTAMP()-(60*60*24*60) ) AND UNIX_TIMESTAMP(?)
		$ttl_part_sold_rs=$this->db->query("SELECT dl.product_id, IFNULL(SUM(o.quantity*dl.qty),0) AS ttl_part_sold
											FROM m_product_deal_link dl
											JOIN king_orders o ON dl.itemid=o.itemid		
											JOIN king_transactions t ON t.transid = o.transid
											WHERE o.status=2 AND t.partner_id > 0
											AND FROM_UNIXTIME(o.time) BETWEEN SUBDATE(?,INTERVAL $from_months MONTH) AND ?
											AND dl.product_id=?
											GROUP BY dl.product_id ",array($to_date,$to_date,$product_id));
		if($ttl_part_sold_rs->num_rows())
			$ttl_part_sold = $ttl_part_sold_rs->row()->ttl_part_sold;
		// Product group deal
		$group_ttl_part_sold_rs=$this->db->query("SELECT pgo.product_id, IFNULL(SUM(o.quantity*l.qty),0) AS ttl_part_sold
											FROM m_product_group_deal_link l
											JOIN king_orders o ON l.itemid=o.itemid		
											join products_group_orders pgo on pgo.order_id = o.id 
											JOIN king_transactions t ON t.transid = o.transid
											WHERE o.status=2 AND t.partner_id > 0
											AND FROM_UNIXTIME(o.time) BETWEEN SUBDATE(?,INTERVAL $from_months MONTH) AND ?
											AND pgo.product_id=?
											GROUP BY pgo.product_id ",array($to_date,$to_date,$product_id));
		if($group_ttl_part_sold_rs->num_rows())
			$group_ttl_part_sold = $group_ttl_part_sold_rs->row()->ttl_part_sold;
		
		return ($ttl_part_sold+$group_ttl_part_sold);
	}
}
