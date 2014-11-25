<?php
/**
 * Order Model to handle order related queries  
 * @author shariff
 *
 */
class order_model extends Model
{
	/**
	 * Default Constructor 
	 */
	function order_model()
	{
		parent::Model();
		
		$this->load->model('erpmodel','erpm');
		$this->load->model('franchise_model');
		$this->load->model('member_model');
				
		$this->login_userid = 6;
		
	}
	
	/**
	 * function to get order list by type [Storeking,Partners,Snapittoday]
	 */
	function get_orders($type,$limit)
	{
		
	}
	
	
	/**
	 * Function to create new storeking order  
	 *  
	 * @param unknown_type $order_params
	 */	
	function create_order($order_params)
	{
		//http://localhost/snapittoday_live/api/to_process_order?fid=17&mid=22006889&pid=11314779&qty=1&member=1&offer_type=1&insurance[opted_insurance]=&insurance[insurance_deals]=14967769&insurance[proof_type]=&insurance[proof_id]=&insurance[first_name]=&insurance[last_name]=&insurance[mob_no]=&insurance[address]=&insurance[city]=&insurance[pincode]=&insurance[proof_name]=&insurance[proof_address]=&redeem=
		
		//error_reporting(E_ALL);
		//ini_set('display_errors',1);
		
		$updated_by=0;
		
		$fid=$order_params['fid'];
		$mid=$order_params['mid'];
		$pid=$order_params['pid'];
		$qty=$order_params['qty'];
		$d_attr = $order_params['d_attr'];
		$offr_sel_type=$order_params['offer_type'];
		$insurance=$order_params['insurance'];
		$redeem=$order_params['redeem'];
		$member=$order_params['member'];
		
		$redeem_points = $redeem?150:0;
		
		$has_super_scheme=0;
		$has_scheme_discount=0;
		$has_member_scheme=0;
		$has_offer=0;
		
		// get franchise details 
		$fran=$this->franchise_model->get_franchise_details($fid,$fid);
		 
		if($fran['is_suspended']==1 || $fran['is_suspended']==3)
			return array('status'=>'error','error_code'=>2046,'error_msg'=>"Franchisee is suspended"); 
		
		if($fran['is_suspended']==0)
			$batch_enabled = 1;
		else
			$batch_enabled = 0;
		
		//get order member details
		$mem_det = $this->member_model->get_member_details($mid);
		
		// check if key member order [franchise mobile no in mid] 
		$fran_det_res = $this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? or login_mobile2=?)",array($mid,$mid));
		if($fran_det_res->num_rows())
			$order_for=2;
	
		if(!$mem_det && !$fran_det_res->num_rows())
			return array('status'=>'error','error_code'=>2046,'error_msg'=>"Invalid Member ID");
		else
			$mid=@$mem_det['pnh_member_id'];
		
		$mem_det = $this->member_model->get_member_details($mid);
		$userid=$mem_det['user_id'];
	
		$new_member=$is_new_member=0;  
		//flag to check is new member
		if($mem_det)
		{
			$ttl_member_orders=$this->member_model->get_memberorders_ttl($mid);
			if($ttl_member_orders)
				$order_for=$new_member=$is_new_member=0;
			else
				$order_for=$new_member=$is_new_member=1;
		}
	
		$key_member=($order_for==2)?1:0;
		
		$member_type=$this->member_model->member_type($mid);
		
		$pids=array();
		$pids['available']=array();
		$pids['not_available']=array();
		foreach($pid as $p)
		{
			$deal=$this->db->query("select d.menuid,b.name as brand,c.name as cat,i.id,i.is_combo,i.pnh_id as pid,i.live,i.orgprice as mrp,i.price,i.name,i.pic,d.publish,p.is_sourceable,i.has_insurance,CONCAT(print_name,'-',pnh_id) AS print_name from king_dealitems i join king_deals d on d.dealid=i.dealid  left join king_brands b on b.id = d.brandid join king_categories c on c.id = d.catid JOIN `m_product_deal_link` l ON l.itemid=i.id JOIN m_product_info p ON p.product_id=l.product_id where pnh_id=? and is_pnh=1",$p)->row_array();
			$avail=$this->erpm->do_stock_check(array($deal['id']),array(1),true);
			$avail_det = array_values($avail);
			if($avail_det[0][0]['stk']==0 && $deal['is_sourceable']==0)
				array_push($pids['not_available'], $p);
			else
				array_push($pids['available'], $p);
	
			$menu_det=$this->db->query("select d.menuid,m.default_margin as margin from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN pnh_menu m ON m.id=d.menuid where i.is_pnh=1 and i.pnh_id=?",$p)->row_array();
	
			$super_scheme=$this->db->query("select * from pnh_super_scheme where menu_id=? and is_active=1 and franchise_id = ? limit 1",array($menu_det['menuid'],$fid))->row_array();
			//super scheme enabled for scheme discount
			if(!empty($super_scheme))
			{
				if($super_scheme['valid_from']<time() && $super_scheme['valid_to']>time() && $super_scheme['is_active'] == 1)
					$has_super_scheme=1;
			}
	
			$member_scheme=$this->db->query("select * from imei_m_scheme where is_active=1 and franchise_id=? and ? between sch_apply_from and scheme_to order by created_on desc limit 1",array($fid,time()))->row_array();
			//member scheme enabled for scheme discount
			if(!empty($member_scheme))
			{
				$has_member_scheme=1;
			}
	
	
			$offer_scheme=$this->db->query("select * from pnh_m_offers where menu_id=? and franchise_id=? and ? between offer_start and offer_end order by id desc limit 1",array($menu_det['menuid'],$fid,time()))->row_array();
			if(!empty($offer_scheme))
			{
				$has_offer=1;
			}
	
		}
	
		// check if some products are not available and return the same
		if($pids['not_available'])
			return array('status'=>'error','error_code'=>2046,'error_msg'=>"Below Products are out of stock : \r\n".implode("\r\n",$pids['not_available']));
		
		if($pids['available'])
			$pids=$pids['available'];
		$menu_ofr_pricevalue=array();
		$items=array();
		foreach($pids as $i=>$p)
			$items[]=array("pid"=>$p,"qty"=>$qty[$i]);
	
		$total=0;
		$d_total=0;
		$o_total=0;
		$commision=0;
		$item_pnt = @$redeem_points/count($items);
		$redeem_value = 0;
	
		foreach($items as $i=>$item)
		{
	
			$prod=$this->db->query("select i.*,d.publish,c.loyality_pntvalue,d.menuid from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN pnh_menu c ON c.id = d.menuid where i.is_pnh=1 and  i.pnh_id=? and i.pnh_id!=0",$item['pid'])->row_array();
	
			$ordered_menus_list[]=$prod['menuid'];
	
			if(empty($prod))
				die("There is no product with ID : ".$item['pid']);
	
			$items[$i]['tax']=$prod['tax'];
			$items[$i]['mrp']=$prod['orgprice'];
			if($fran['is_lc_store'])
				$items[$i]['price']=$prod['store_price'];
			else
				$items[$i]['price']=$prod['price'];
			$items[$i]['itemid']=$prod['id'];
	
			$margin=$this->erpm->get_pnh_margin($fran['franchise_id'],$item['pid']);
	
			$imei_disc=$this->erpm->get_franimeischdisc_pid($fran['franchise_id'],$item['pid']);
			if($imei_disc==0 && $key_member==1 && $prod['price']<=5000 && $prod['menuid']==112)
			{
				if($margin['margin']>=0.5)
				{
					$margin['margin']=$margin['margin']-0.5;
					$margin['base_margin']=$margin['base_margin']-0.5;
				}
			}
	
			if($prod['is_combo']=="1")
				$items[$i]['discount']=$items[$i]['price']/100*$margin['combo_margin'];
			else
				$items[$i]['discount']=$items[$i]['price']/100*$margin['margin'];
	
			$items[$i]['billon_orderprice']=$prod['billon_orderprice'];
			$items[$i]['margin']=$margin;
			$total+=$items[$i]['price']*$items[$i]['qty'];
	
			$menu_ofr_pricevalue[$prod['menuid']][]=$items[$i]['price']*$items[$i]['qty'];
	
			$d_total+=($items[$i]['price']-$items[$i]['discount'])*$items[$i]['qty'];
			$o_total+=$items[$i]['price']*$items[$i]['qty'];
			$commision+=$items[$i]['discount']*$items[$i]['qty'];
			$itemids[]=$prod['id'];
			$itemnames[]=$prod['name'];
			$loyalty_pntvalue=$prod['loyality_pntvalue'];
	
			// offers check
	
			if($offr_sel_type == 2 || $insurance['opted_insurance'] == 1 || $key_member==1 )
			{
				$insurance['menuids'][$item['pid']] = $prod['menuid'];
				$insurance['order_value'][$item['pid']] = $items[$i]['price'];
			}
		}
	
		foreach($menu_ofr_pricevalue as $i=>$t)
		{
			$menu_ttlval[$i]=array_sum($t);
		}
	
		$l_points=array();
		foreach($menu_ttlval as $l_menu_id=>$ttl_l_amt)
		{
			$points=@$this->db->query("SELECT points FROM pnh_loyalty_points WHERE menu_id=? AND ?>=amount AND is_active=1  ORDER BY amount DESC LIMIT 1",array($l_menu_id,$ttl_l_amt))->row()->points;
			$l_points[$l_menu_id]=$points*1;
		}
		if($redeem)
			$redeem_value += $item_pnt_value = $item_pnt*$prod['loyality_pntvalue'];
	
		// check if franchise has enough balance to process this order 
		$fran_crdet = $this->erpm->get_fran_availcreditlimit($fran['franchise_id']);
		$fran['current_balance'] = $fran_crdet[3];
	
		if($fran['current_balance']<$d_total)
		{
			return array('status'=>'error','error_code'=>2046,'purchase_limit'=>format_price($fran_crdet[3],0),'error_msg'=>"Insufficient balance! Balance in your account Rs {$fran['current_balance']} Total order amount : Rs $d_total");
		}
		
		$transid=strtoupper("PNH".random_string("alpha",3).$this->erpm->p_genid(5));
	
		$pnh_member_fee=0;
		if($is_new_member == 1 && $key_member == 0 && $member_type==1)
		{
			$pnh_member_fee=PNH_MEMBER_FEE;
			$fee_det = array($mid,$transid,'',$pnh_member_fee,1,$updated_by);
			$this->db->query("insert into pnh_member_fee (member_id,transid,invoice_no,amount,status,created_on,created_by) VALUES(?,?,?,?,?,now(),?)",$fee_det);
		}
	
		$this->db->query("insert into king_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id,trans_created_by,batch_enabled,order_for,pnh_member_fee)
				values(?,?,?,?,?,?,?,?,?,?,?,?)"
				,array($transid,$d_total,$d_total,3,time(),time(),1,$fran['franchise_id'],$this->login_userid,$batch_enabled,$order_for,$pnh_member_fee)) or die(mysql_error());
	
	
		foreach($items as $item)
		{
			// check if belongs to split invoice condiciton config
			$split_order=$this->db->query("SELECT i.*,d.publish,c.loyality_pntvalue FROM king_dealitems i JOIN king_deals d ON d.dealid=i.dealid JOIN pnh_menu c ON c.id = d.menuid WHERE i.is_pnh=1 AND  i.pnh_id=? AND i.pnh_id!=0 AND c.id IN(112,118,122)",$item['pid'])->row_array();
			//$split_order = 0;
			if($split_order)
			{
				$ttl_qty = $item['qty'];
				$p_qty = 1;
			}else
			{
				$ttl_qty = 1;
				$p_qty = $item['qty'];
			}
	
			for($qi=0;$qi<$ttl_qty;)
			{
	
				$qi = $qi+$p_qty;
	
				$inp=array("id"=>$this->erpm->p_genid(10,'order'),"transid"=>$transid,"userid"=>$userid,"itemid"=>$item['itemid'],"brandid"=>"");
	
				$item['qty'] = $p_qty;
	
				$inp["brandid"]=$this->db->query("select d.brandid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['itemid'])->row()->brandid;
				$brandid=$inp["brandid"];
				$catid=$this->db->query("select d.catid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['itemid'])->row()->catid;
				$menuid=$this->db->query("select d.menuid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=? and menuid2=0",$item['itemid'])->row()->menuid;
				$inp["bill_person"]=$inp['ship_person']=$fran['franchise_name'];
				$inp["bill_address"]=$inp['ship_address']=$fran['address'];
				$inp["bill_city"]=$inp['ship_city']=$fran['city'];
				$inp['bill_pincode']=$inp['ship_pincode']=$fran['postcode'];
				$inp['bill_phone']=$inp['ship_phone']=$fran['login_mobile1'];
				$inp['bill_email']=$inp['ship_email']=$fran['email_id'];
				$inp['bill_state']=$inp['ship_state']=$fran['state'];
				$inp['quantity']=$p_qty;//$item['qty'];
				$inp['time']=time();
				$inp['ship_landmark']=$inp['bill_landmark']=$fran['locality'];
				$inp['bill_country']=$inp['ship_country']="India";
				$inp['i_orgprice']=$item['mrp'];
				$inp['i_price']=$item['price'];
				$inp['i_discount']=$item['mrp']-$item['price'];
				$inp['i_coup_discount']=$item['discount'];
				$inp['redeem_value']=($item['price']/($total+$redeem_value))*$redeem_value;
				$inp['billon_orderprice']=$item['billon_orderprice'];
				$inp['member_id']=$mid;
	
				if($split_order && $key_member==1)
				{
	
					$membr_id=$this->erpm->_gen_uniquememberid();
					if($this->db->query("select * from pnh_member_info where pnh_member_id=?",$membr_id)->num_rows()==0);
					$inp['member_id']=$membr_id;
					$inp['is_ordqty_splitd']=1;
	
					$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $membr_id",time()));
					$userid=$this->db->insert_id();
					$inp['userid']=$userid;
					$this->db->query("insert into pnh_member_info(pnh_member_id,user_id,franchise_id,created_by,created_on)values(?,?,?,?,?)",array($membr_id,$userid,$fid,$this->login_userid,time()));
	
					//KEY MEMBER
	
					if($key_member==1)
					{
	
						//echo $this->db->last_query();die();
						$item_total=$item['price']*$p_qty;
						if($item_total<=5000 && $menuid==112)
						{
							$inp['pnh_member_fee']=0;
							$inp['insurance_amount']=0;
							if($imei_disc==0)
							{
								$key_mem_imei=0.5;
								$this->db->query("insert into imei_m_scheme(franchise_id,menuid,categoryid,brandid,scheme_type,credit_value,scheme_from,scheme_to,sch_apply_from,created_on,created_by,is_active)values(?,?,?,?,?,?,unix_timestamp(curdate()),unix_timestamp(curdate()),unix_timestamp(curdate()),unix_timestamp(curdate()),?,1)",array($fid,$menuid,$catid,$brandid,1,$key_mem_imei,$updated_by));
								$key_imei_id=$this->db->insert_id();
								$inp['imei_reimbursement_value_perunit']=(($inp['i_orgprice']-($inp['i_discount']+$inp['i_coup_discount']))*$key_mem_imei/100);
								$inp['imei_scheme_id']=$key_imei_id;
	
								//Disabling IMEI scheme after key member order is placed;
								if($key_imei_id)
									$this->db->query("Update imei_m_scheme set is_active=0 where is_active=1 and id=?",$key_imei_id);
							}else
							{
								$inp['imei_scheme_id']=$imei_disc['id'];
	
								if($imei_disc['scheme_type']==0)
									$inp['imei_reimbursement_value_perunit']=$imei_disc['credit_value'];
								else
									$inp['imei_reimbursement_value_perunit']=(($inp['i_orgprice']-($inp['i_discount']+$inp['i_coup_discount']))*$imei_disc['credit_value']/100);
	
							}
						}
						if($item_total>5000 && $item_total<=10000)
						{
							$insurance_id = random_string("nozero", $len=2).time(); //$this->get_insurance_id();
							$insurance_deals=$item['itemid'];
							$inp['pnh_member_fee']=PNH_MEMBER_FEE;
							$inp['insurance_amount']=0;
							$inp['insurance_id']=$insurance_id;
							$d_total+=$inp['pnh_member_fee']+$inp['insurance_amount'];
							$pnh_member_fee+=$inp['pnh_member_fee'];
							$inp['imei_scheme_id']=0;
							$inp['imei_reimbursement_value_perunit']=0;
						}
	
						if($item_total>10000)
						{
							$insurance_id = random_string("nozero", $len=2).time();
							$inp['pnh_member_fee']=PNH_MEMBER_FEE;
							$insuranc_cost=(($item['price']-10000)*1)/100;
							$inp['insurance_amount']=$insuranc_cost;
							$inp['insurance_id']=$insurance_id;
							$inp['has_insurance']=1;
							$d_total+=$insuranc_cost+PNH_MEMBER_FEE;
							$pnh_member_fee+=$inp['pnh_member_fee'];
							$inp['imei_scheme_id']=0;
							$inp['imei_reimbursement_value_perunit']=0;
						}
					}
				}
	
				$inp['i_tax']=$item['tax'];
	
				if($has_member_scheme==1 && $key_member!=1)
				{
					//check item enabled for member scheme
					$check_mbrschdisableditem=$this->db->query("select * from pnh_membersch_deals where is_active=0 and ? between valid_from and valid_to and itemid=? order by created_on desc limit 1",array(time(),$item['itemid']))->row_array();
	
					$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=? and brandid=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$brandid,$fid))->result_array();
					if(empty($member_scheme_brand))
						$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=? and brandid=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$fid))->result_array();
					if(empty($member_scheme_brand))
						$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=0 and brandid=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$brandid,$fid))->result_array();
					if(empty($member_scheme_brand))
						$member_scheme_brand=$this->db->query("select * from imei_m_scheme where menuid=? and categoryid=0 and brandid=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$fid))->result_array();
	
					if(!empty($member_scheme_brand)  && empty($check_mbrschdisableditem))
						foreach($member_scheme_brand as $member_scheme)
						{
							$inp['imei_scheme_id']=$member_scheme['id'];
							if($member_scheme['scheme_type']==0)
								$inp['imei_reimbursement_value_perunit']=$member_scheme['credit_value'];
							else
								$inp['imei_reimbursement_value_perunit']=(($inp['i_orgprice']-($inp['i_discount']+$inp['i_coup_discount']))*$member_scheme['credit_value']/100);
						}
				}
				//if super scheme is enabled
	
				if($has_super_scheme!=0)
				{
	
					//check item enabled for super scheme
					$check_superschdisableditem=$this->db->query("select * from pnh_superscheme_deals where is_active=0 and ? between valid_from and valid_to and itemid=? order by created_on desc limit 1",array(time(),$item['itemid']))->row_array();
	
					//$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where brand_id=? and franchise_id = ? ",array($brandid,$fid))->result_array();
					$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=? and brand_id=? and franchise_id = ? and is_active=1",array($menuid,$catid,$brandid,$fid))->result_array();
					if(empty($super_scheme_brand))
						$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=0 and brand_id=? and franchise_id = ? and is_active=1 order by id desc limit 1",array($menuid,$brandid,$fid))->result_array();
					if(empty($super_scheme_brand))
						$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=? and brand_id=0 and franchise_id = ? and is_active=1 order by id desc limit 1",array($menuid,$catid,$fid))->result_array();
					if(empty($super_scheme_brand))
						$super_scheme_brand=$this->db->query("select * from pnh_super_scheme where menu_id=? and cat_id=0 and brand_id=0 and franchise_id = ? and is_active=1 order by id desc limit 1",array($menuid,$fid))->result_array();
					//print_r($super_scheme_brand);
					if(!empty($super_scheme_brand) && empty($check_superschdisableditem))
					{
	
						foreach($super_scheme_brand as $super_scheme)
						{
							if($super_scheme['valid_from']<time() && $super_scheme['valid_to']>time() && $super_scheme['is_active'] == 1)
							{
	
								$inp['super_scheme_logid']=$super_scheme['id'];
								$inp['has_super_scheme']=1;
								$inp['super_scheme_target']=$super_scheme['target_value'];
								$inp['super_scheme_cashback']=$super_scheme['credit_prc'];
							}
						}
					}
				}
	
				if($has_offer==1)
				{
					$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=? and brand_id=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$brandid,$fid))->result_array();
	
					if(empty($offer_det))
						$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=? and brand_id=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$catid,$fid))->result_array();
					if(empty($offer_det))
						$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=0 and brand_id=? and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$brandid,$fid))->result_array();
	
					if(empty($offer_det))
						$offer_det=$this->db->query("select * from pnh_m_offers where menu_id=? and cat_id=0 and brand_id=0 and franchise_id=? and is_active=1 order by created_on desc limit 1",array($menuid,$fid))->result_array();
	
					if(!empty($offer_det))
					{
						foreach($offer_det as $offer)
						{
							$inp['has_offer']=1;
							$inp['offer_refid']=$offer['id'];
						}
					}
	
				}
				$this->db->insert("king_orders",$inp);
				foreach($this->db->query("select group_id from m_product_group_deal_link where itemid=?",$inp['itemid'])->result_array() as $g)
				{
					$attr_n=array();
					$attr_v=array();
					foreach($this->db->query("select attribute_name_id from products_group_attributes where group_id=?",$g['group_id'])->result_array() as $p)
					{
						$attr_n[]=$p['attribute_name_id'];
						$attr_v[]=$this->input->post($item['pid']."_".$p['attribute_name_id']);
					}
					$sql="select product_id from products_group_pids where attribute_name_id=? and attribute_value_id=?";
					foreach($this->db->query($sql,array($attr_n[0],$attr_v[0]))->result_array() as $p)
					{
						$f=true;
						foreach($attr_n as $i=>$an)
							if($this->db->query("select 1 from products_group_pids where product_id=? and attribute_name_id=? and attribute_value_id=?",array($p['product_id'],$an,$attr_v[$i]))->num_rows()==0)
							$f=false;
						if($f)
							break;
					}
					$this->db->insert("products_group_orders",array("transid"=>$transid,"order_id"=>$inp['id'],"product_id"=>$p['product_id']));
				}
	
				// new
				if($this->db->query("select is_group from king_dealitems where id=? and is_group = 1 ",$inp['itemid'])->num_rows())
				{
					if(isset($d_attr[$inp['itemid']]))
					{
						// check if the product has default vendor linked
						$ven_id = @$this->db->query("select vendor_id from m_vendor_product_link where product_id = ? ",$d_attr[$inp['itemid']])->row()->vendor_id;
						$ven_id = $ven_id*1;
						// update ordered product_id
						$this->db->query("update king_orders set order_product_id = ?,order_from_vendor=? where id = ? ",array($d_attr[$inp['itemid']],$ven_id,$inp['id']));
					}
				}
	
				$bal_discount_amt = ($item['price']*$item['margin']['bal_discount']/100)*$item['qty'];
				$m_inp=array("transid"=>$transid,"itemid"=>$item['itemid'],"mrp"=>$item['mrp'],"price"=>$item['price'],"base_margin"=>$item['margin']['base_margin'],"sch_margin"=>$item['margin']['sch_margin'],"qty"=>$item['qty'],"final_price"=>$item['price']-$item['discount']);
				$this->db->insert("pnh_order_margin_track",$m_inp);
				$id=$this->db->insert_id();
			}
		}
		
		$bal_discount_amt_msg = '';
		if($bal_discount_amt)
			$bal_discount_amt_msg = ', Topup Damaka Applied : Rs'.$bal_discount_amt;
	
		$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1, $d_total,"Order $transid - Total Amount: Rs $total".$bal_discount_amt_msg,"transaction",$transid);
	
		$balance=$this->db->query("select current_balance from pnh_m_franchise_info where franchise_id=?",$fran['franchise_id'])->row()->current_balance;
	
		
		$this->erpm->sendsms_franchise_order($transid,$d_total,$o_total);
		if($order_for!=2)
		{
			// ======================< MEMBER ORDER SMS >===================================
			if($mem_det['mobile'] != '' && strlen($mem_det['mobile'])>=10)
			{
				$mem_msg ="Thank you for ordering with StoreKing.";
				$this->erpm->pnh_sendsms($mem_det['mobile'],$mem_msg,0,$mid,'MEM_ORDER');
			}
		}
		// ======================< MEMBER ORDER SMS >===================================
		//Alotting Loyalty Points
		$trans_order_det_res = $this->db->query("SELECT o.id AS order_id,o.itemid,d.menuid,o.i_price,o.quantity,t.order_for
				FROM king_dealitems i
				JOIN king_deals d ON d.dealid=i.dealid
				JOIN king_orders o ON o.itemid = i.id
				join king_transactions t on t.transid=o.transid
				WHERE o.transid=?
				",$transid);
		if($trans_order_det_res->num_rows())
		{
			foreach($trans_order_det_res->result_array() as $trans_ord_det)
			{
				$menuid = $trans_ord_det['menuid'];
				$order_amt=$trans_ord_det['i_price']*$trans_ord_det['quantity'];
				if($key_member==0)
					$ord_loyal_pnt = (($trans_ord_det['i_price']*$trans_ord_det['quantity'])*$l_points[$menuid])/$menu_ttlval[$menuid];
				else
					$ord_loyal_pnt = $this->db->query("select points from pnh_loyalty_points where amount < ? order by amount desc limit 1",$order_amt)->row()->points;
	
				$this->db->query("update king_orders set loyality_point_value=? where  transid=? and id=? ",array($ord_loyal_pnt,$transid,$trans_ord_det['order_id']));
			}
		}
		// Process to batch this transaction
		$ttl_num_orders=count($items);
		$batch_remarks='Created by pnh offline order system';
	
		//$this->reservations->do_batching_process($transid,$ttl_num_orders,$batch_remarks,$updated_by);
	
	
	
		//===================< Implement the member offers START>============================//
		if($key_member==1)
		{
			$datetime=date("Y-m-d H:i:s",time());
			//check_for_insurancance applicable order
			$key_mem_insurance_items=$this->db->query("select * from king_orders where transid=? and insurance_id is not null",$transid);
			if($key_mem_insurance_items)
			{
				foreach($key_mem_insurance_items->result_array() as $kydl)
				{
					$key_mem_insu_id=$kydl['insurance_id'];
					$kydl['fid']=$fid;
					$key_mid=$kydl['member_id'];
					$itemid=$kydl['itemid'];
					$order_id=$kydl['id'];
					$insurance_value=$kydl['insurance_amount'];
					$ofr_towords=$kydl['i_price']-$kydl['i_coup_discount'];
	
					$this->db->query("insert into pnh_member_insurance(fid,mid,offer_type,opted_insurance,order_id,itemid,insurance_value,created_by,created_on)values(?,?,2,1,?,?,?,?,?)",array($fid,$key_mid,$order_id,$itemid,$insurance_value,$updated_by,$datetime));
					$insurance_id=$this->db->insert_id();
					$this->db->query("insert into pnh_member_offers(insurance_id,franchise_id,member_id,offer_type,order_id,transid_ref,offer_value,created_by,created_on,process_status,delivery_status,feedback_status,offer_towards,pnh_pid) values(?,?,?,2,?,?,?,?,?,0,0,0,?,?)",array($insurance_id,$fid,$key_mid,$order_id,$transid,$insurance_value,$updated_by,$datetime,$ofr_towords,$itemid));
					$this->db->query("update king_orders set insurance_id=? where transid=? and insurance_id=? and id=?",array($insurance_id,$kydl['transid'],$key_mem_insu_id,$kydl['id']));
				}
			}
		}
		$menu_list=array_unique($ordered_menus_list);
		$insurance['mid'] =$mid;
		$insurance['fid'] =$fid;
		$insurance['offer_type'] =$offr_sel_type;
		$insurance['transid'] = $transid;
		$insurance['created_by'] = $updated_by;
	
		// check is member fee paid?
		$insurance['pnh_member_fee'] = $pnh_member_fee;
	
		// =================< Check total member orders >======================
		$orders=$this->db->query("SELECT COUNT(DISTINCT(a.transid)) AS l FROM king_orders a
				join pnh_member_info b on b.user_id=a.userid
				WHERE b.pnh_member_id=?  AND a.status NOT IN (3)",$insurance['mid'])->row()->l;
		if($orders > 1)
			$insurance['mem_fee_applicable'] = 0;
		else
			$insurance['mem_fee_applicable'] = 1;
	
		$insurance['new_member']=$new_member;
	
		//==============< DEPRICATED CODE DONT USE ~~~~ >======================
		if($offr_sel_type == 2 && $insurance['opted_insurance'] == 1 && $new_member == 1 && $member_type==1)
		{
			//process insurance document and address details & get insurance process id
			$insu_id = $this->erpm->process_insurance_details($insurance);
			//echo '<pre>';print_r($insurance);die();
		}elseif($offr_sel_type == 3 && $new_member == 1 && $member_type==1)
		{
			$insurance['offer_type'] = 3;
			$insu_id = $this->erpm->process_insurance_details($insurance);
		}
		elseif($offr_sel_type == 2  && $new_member == 1 && $member_type==1)
		{
			$insurance['offer_type'] = 3;
			$offer_ret = $this->erpm->pnh_member_fee($d_total,$insurance);
		}
		elseif($offr_sel_type == 0 && $insurance['opted_insurance'] == 1 && $new_member == 0)
		{
			//process insurance document and address details & get insurance process id
			$insurance['mem_fee_applicable'] = 0;
			$insurance['pnh_member_fee'] = 0;
			$insu_id = $this->erpm->process_insurance_details($insurance);
	
		}
		elseif($offr_sel_type == 1 && $o_total >= MEM_MIN_ORDER_VAL && $new_member == 1 && $member_type==1)
		{
	
			$offer_ret = $this->erpm->pnh_member_recharge($o_total,$insurance);
		}
		//==============< DEPRICATED CODE DONT USE ~~~~ >======================
		
		//===================< Implement the member offers END>============================
		$ttl_insurance_amt=$this->db->query("select ifnull(sum(insurance_amount),0) as insurance_amount from king_orders where transid=?",$transid)->row()->insurance_amount;
	
		$order_res=$this->db->query("select order_for,o.*,di.name as item_name,pnh_id from king_orders o join king_transactions t on t.transid = o.transid join king_dealitems di on di.id = o.itemid where o.transid=?",$transid);
		$transid= '';
		$trans_amt = 0;
		$ttl_member_fee = 0;
		$order_det_arr=array();
		foreach($order_res->result_array() as $i=>$order_det)
		{
	
			$transid= $order_det['transid'];
	
			$order = array();
			$order['order_id'] = $order_det['id'];
			$order['product_id'] = $order_det['pnh_id'];
			$order['item_name'] = $order_det['item_name'];
			$order['mrp'] = $order_det['i_orgprice'];
			$order['offer_price'] = $order_det['i_orgprice']-$order_det['i_discount'];
			$order['has_insurance'] = $order_det['has_insurance'];
			$order['insurance_fee'] = $order_det['insurance_amount'];
			$order['franchise_price'] = $order_det['i_orgprice']-$order_det['i_discount']-$order_det['i_coup_discount'];
			$order['franchise_price_percentage'] = 100-($order['franchise_price']/$order['offer_price'])*100 .'%';
	
			// che ck if its key member order to process member for individual members by member id
			if($order_det['order_for'] != 2)
				$order['member_fee'] = @$this->db->query("select amount from pnh_member_fee where member_id = ? and transid = ? ",array($order_det['member_id'],$order_det['transid']))->row()->amount*1;
			else
				$order['member_fee'] = $order_det['pnh_member_fee']*(!$i); // reset for memberid for member fee
	
			$trans_amt += $order['franchise_price'];
			$ttl_member_fee += $order['member_fee'] ;
	
			$order_det_arr[] = $order;//array('transid'=>$order_det['transid'],'product_name'=>$prod_name,'Order_amt'=>$ordr_amt,'insurance_amt'=>$insu_fee,'member_fee'=>$mem_fee,'commission'=>$commission,'avail_bal'=>$avail_bal)
	
		}
	
		$trans_amt += $ttl_member_fee;
	
		//$this->_output_handle('json',true,array('transid'=>$order_det['transid'],'order_details'=>$memfee));
		$order_for=$order_for==2?'Key Member':'Non key Member';
	
		return array('status'=>'success','trans'=>$transid,'orders'=>$order_det_arr,'total_member_fee'=>$ttl_member_fee,'order_for'=>$order_for);
	
	}
	
	/**
     * Function to return orders of the transaction
	 * @author Shivaraj <shivaraj@storeking.in>
     * @param type $transid varchar
     * @return type array 
	 * @usage Reservations Module and View Orders of franchise API
     */
    function get_order_det_by_orderid($orderid=0,$default='all') // if $default==all get all orders of a transaction id
	{
			if($orderid == 0)
			{
				return false;
			}
			//$transid=0,
			$field_list = "o.id as orderid,DATE_FORMAT(FROM_UNIXTIME(o.time),'%b/%d/%Y %h:%i:%s %p') AS order_date
					,o.i_orgprice AS mrp
					,o.bill_person,o.bill_address,o.bill_city,o.bill_state,o.bill_pincode,0.bill_phone
					,o.ship_person,o.ship_address,o.ship_city,o.ship_state,o.ship_pincode,o.ship_phone
					,o.status,di.name,o.quantity,concat('".IMAGES_URL."items/',di.pic,'.jpg') as image_url
					,o.i_price AS offer_price
					,((o.i_price - o.i_coup_discount)*o.quantity) as subtotal_prepaid
					,( ( o.i_orgprice - (o.i_discount + o.i_coup_discount) ) * o.quantity) AS sub_total
					,( o.i_price - o.i_coup_discount ) as paid_each_qty_prepaid
					,( o.i_orgprice - o.i_coup_discount ) AS paid_each_qty
					,( ( o.i_price - o.i_coup_discount +o.pnh_member_fee + o.insurance_amount ) * o.quantity ) AS paid
					,o.pnh_member_fee,o.insurance_amount
					,o.i_tax,o.i_discount,o.i_coup_discount,o.redeem_value
					,o.shipped,o.itemid,o.brandid,o.member_id,o.is_ordqty_splitd
                    ,di.pnh_id,tr.is_pnh,tr.batch_enabled
                    ,pi.p_invoice_no,i.invoice_no,tr.transid,tr.order_for";
			
            $sql="SELECT $field_list
                    FROM king_orders o
                    JOIN king_transactions tr ON tr.transid = o.transid 
                    JOIN pnh_m_franchise_info f ON f.franchise_id = tr.franchise_id
                    LEFT JOIN king_invoice i ON o.id = i.order_id AND i.invoice_status = 1
                    LEFT JOIN proforma_invoices `pi` ON pi.order_id = o.id AND pi.invoice_status = 1 
                    JOIN king_dealitems di ON di.id = o.itemid 
                    WHERE o.id = ? ";
             $trans_orders_res = $this->db->query($sql,$orderid);
			 if($trans_orders_res->num_rows() > 0)
			 {
				 return $trans_orders_res->row_array();
			 }
			 else
			 {
				 return false;
			 }
	}
	
	/**
	 * Function to generate invoice for given details
	 * @author Shivaraj <shivaraj@storeking.in>_Aug_27_2014
	 * @support support function for partner order import
	 * @param type $inv_param_arr
	 * @return type
	 */
	function process_transid_for_invoice($inv_param_arr)
	{
		// Process to batch this transaction
		$num_orders=$inv_param_arr["num_orders"];
		$transid=$inv_param_arr["transid"];
		$prefix=$inv_param_arr["prefix"];
		$partner_rackbin_id = $inv_param_arr["partner_rackbin_id"];
		$userid = $inv_param_arr['userid'];
		$partner_invno = $inv_param_arr["partner_invno"];
		$partner_invdate = $inv_param_arr["partner_invdate"];
		$awbno=$inv_param_arr["awbno"];
		$courier_name=$inv_param_arr["courier_name"];
		$shipped_on = $inv_param_arr["shipped_on"];
		$snp_pnh_part = $inv_param_arr["snp_pnh_part"];//optional
		//echo "".$num_orders; die();
		
		$batch_id = $inv_param_arr["batch_id"];//optional
		if($num_orders)
		{
			//============<< Create Batch >>================
			$params["transid"]=$transid;
			$params["num_orders"]=$num_orders;
			$params["process_partial"]=0;
			$params['batch_remarks']='Created by pnh Order Import File';
			$params["snp_pnh"]='others';
			$params["en_date"]=date('Y-m-d');
			$params['process_orderby']=2;
			$params['by_brandid']='';
			$params['p_oids']='';
			//$params['by_p_oids']='';
			$params['by_menu']='';
			$params['pmenu_id']='';
			$params['stock_rackbin_id']=$partner_rackbin_id;
			$params['snp_pnh_part']=array($snp_pnh_part);
			$params['batch_id']=$batch_id;

			//$params['stock_loc']='fba';

			$batch_id=$this->erpm->do_shipment_batch_process($params);
			//=================================
		
			// processs invoice for geneated batch 
			// get batch proforma invoices 
			$res_bp = $this->db->query("select distinct p_invoice_no from shipment_batch_process_invoice_link a	where batch_id = ? and invoice_no = 0 ",$batch_id);

			//echo $this->db->last_query();	

			if($res_bp->num_rows())
			{
				foreach($res_bp->result_array() as $proforma_det)
				{
					$p_invoice_no = $proforma_det['p_invoice_no'];
					
					// get orders in proforma invoice 
					$p_inv_oids_arr = $this->db->query("select transid,order_id from proforma_invoices where p_invoice_no = ? ",$p_invoice_no)->result_array();
					$transid = $p_inv_oids_arr[0]['transid'];

					$ref_transid = $this->db->query("select partner_reference_no from king_transactions where transid = ? ",$transid)->row()->partner_reference_no;

					$p_oid_list = array();		
					foreach($p_inv_oids_arr as $p_inv_oid) 
						$p_oid_list[] = $p_inv_oid['order_id'];

					//print_r($p_oid_list);

					//auto packing is updated by marked reserved batch stock status to 1 by proforma 

					$this->db->query("update t_reserved_batch_stock set  status = 1,reserved_on=? where status = 0 and p_invoice_no = ? ",array(time(),$p_invoice_no) );


					// call do_invoice to make invoice on proforma
					$invoice_no_arr = $this->erpm->do_invoice($p_oid_list,true);
					//print_r($invoice_no_arr);
					$invoice_no = $invoice_no_arr[0];
					
					// allot imei nos by orderid
					$res_inv_odet = $this->db->query("select * from king_orders a join king_invoice b on a.id = b.order_id where b.invoice_no = ? and invoice_status = 1 ",$invoice_no);
					if($res_inv_odet->num_rows())
					{
						foreach($res_inv_odet->result_array() as $row_odet)
						{
							$order_imei_nos = $row_odet['order_imei_nos'];
							$order_id = $row_odet['order_id'];
							
							// check if imei_no exists in our system.
							
							$order_imei_nos_arr = explode(',',$order_imei_nos);
							if(count($order_imei_nos_arr))
							{
								foreach($order_imei_nos_arr as $imei_no)
								{
									
									if(!$imei_no)
										continue;
									
									$this->db->query("update t_imei_no set order_id = ?,status = 1 where imei_no = ? and status = 0 limit 1 ",array($order_id,$imei_no));
									// get order imeinos for partner table.
									// check if imei entry is in log
									$imei_log_res = $this->db->query('select * from t_imei_update_log where imei_no = ? and is_active = 1 ',array($imei_no));
									if(!$imei_log_res->num_rows())
									{
									
										$imei_det = $this->db->query('select * from t_imei_no where imei_no = ? and order_id = ? ',array($imei_no,$order_id))->row_array();
									
										$imei_upd_det = array();
										$imei_upd_det['imei_no'] = $imei_det['imei_no'];
										$imei_upd_det['product_id'] = $imei_det['product_id'];
										$imei_upd_det['stock_id'] = $imei_det['stock_id'];
										$imei_upd_det['grn_id'] = $imei_det['grn_id'];
										$imei_upd_det['is_active'] = 1;
										$imei_upd_det['logged_by'] = $user['userid'];
										$imei_upd_det['logged_on'] = date('Y-m-d H:i:s',$imei_det['created_on']);
										$this->db->insert('t_imei_update_log',$imei_upd_det);
									}
									
									$imei_upd_det = array();
									$imei_upd_det['imei_no'] = $imei_no;
									$imei_upd_det['alloted_order_id'] = $o['id'];
									$imei_upd_det['alloted_on'] = cur_datetime();
									
									$this->db->where(array('imei_no'=>$imei_no,'is_active'=>1));
									$this->db->update('t_imei_update_log',$imei_upd_det);
									
								}
							}
							
						}
					}
					
					
					//print_r($out);
					
					$shipment_arr[]=array();
					$shipment_arr['prefix'] = $prefix;
					$shipment_arr['partner_id'] = $snp_pnh_part;
					$shipment_arr['partner_invno'] = $partner_invno;
					$shipment_arr['partner_invdate'] = $partner_invdate;
					$shipment_arr['invoice_no'] = $invoice_no;
					$shipment_arr['p_invoice_no'] = $p_invoice_no;
					$shipment_arr['p_oid_list']=$p_oid_list;
					$shipment_arr['awbno']=$awbno;
					$shipment_arr['courier_name']=$courier_name;
					$shipment_arr['shipped_on'] = $shipped_on;
					$shipment_arr['userid'] = $userid;
					
					$this->process_inv_for_shipment($shipment_arr);
					
					$batchid=$this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no=?",$p_invoice_no)->row()->batch_id;

					foreach($this->db->query("select distinct product_id from t_reserved_batch_stock where p_invoice_no = ? ",$p_invoice_no)->result_array() as $pinv_prd)
					{
						$this->erpm->_upd_product_deal_statusbyproduct($pinv_prd['product_id'],$userid,'updated on invoice');
					} 

					// update batch status 
					$this->erpm->update_batch_status($batchid);

				}
			}
			return array($invoice_no,$batch_id);
		}else
		{
			return array(0,$batch_id);
		}

	}

	/**
	 * Function to update shipment details for given invoice details
	 * @author Shivaraj <shivaraj@storeking.in>_Aug_27_2014
	 * @support support function for partner order import
	 * @param type $shipment_arr
	 */
	function process_inv_for_shipment($shipment_arr)
	{
		$prefix = $shipment_arr['prefix'];
		$partner_id = $shipment_arr['partner_id'];
		$partner_invno = $shipment_arr['partner_invno'];
		$partner_invdate = $shipment_arr['partner_invdate'];
		$invoice_no = $shipment_arr['invoice_no'];
		$p_invoice_no = $shipment_arr['p_invoice_no'];
		$userid = $shipment_arr['userid'];
		$p_oid_list = $shipment_arr['p_oid_list'];
		$awbno=$shipment_arr['awbno'];
		$courier_name=$shipment_arr['courier_name'];
		$shipped_on = $shipment_arr['shipped_on'];
		// ===============================
		//$this->db->query("update proforma_invoices set invoice_status = 0 where p_invoice_no = ? ",$proforma_det['p_invoice_no']);
		$this->db->query("update king_invoice set partner_invno = ?,partner_invdate = ? where invoice_no = ? ",array($partner_invno,$partner_invdate,$invoice_no));

		//$this->db->query("update t_reserved_batch_stock set  status = 2,reserved_on=? where status = 1 and p_invoice_no = ? ",array(time(),$p_invoice_no));
		$this->db->query("update king_orders set status = 2 where id in (".implode(',',$p_oid_list).") ");

		$cour_name=$prefix.'-'.$courier_name; // AMZ-ATS
		$qry_rslt=$this->db->query("SELECT * FROM m_courier_info WHERE courier_name=?",$cour_name);
		if( $qry_rslt->num_rows()) {
			$courier_id=$qry_rslt->row()->courier_id;
		}
		else {
			$this->db->insert("m_courier_info",array('courier_name'=>$cour_name,'ref_partner_id'=>$partner_id,'is_active'=>0,'created_on'=>date('Y-m-d H:i:s',time()),'created_by'=> $userid) );
			$courier_id= $this->db->insert_id();
		}
		
		
		// update invoice as shipped by ship date and tracking/awbno
		if($invoice_no)
		{
			$inv_sdet = array();
			$inv_sdet[] = $userid;
			$inv_sdet[] = $userid;
			$inv_sdet[] = $awbno;
			$inv_sdet[] = $courier_id;
			$inv_sdet[] = $shipped_on;
			$inv_sdet[] = $userid;
			$inv_sdet[] = $invoice_no;
			$inv_sdet[] = $p_invoice_no;

			$this->db->query("update shipment_batch_process_invoice_link set packed=1,packed_on=now(),packed_by=?,outscanned=1,outscanned_on=now(),outscanned_by=?,awb = ?,courier_id=?,shipped=1,shipped_on=?,shipped_by=?,invoice_no = ? where p_invoice_no = ? ",$inv_sdet);

		}
		// ===============================
		
	}
	
}