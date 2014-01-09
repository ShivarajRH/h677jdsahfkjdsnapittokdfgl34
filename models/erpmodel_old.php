<?php 

class Erpmodel extends Model
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	function get_prodpendingorderqty($product_id)
	{
		$sql = "(
						select go.product_id,'grp' as prod_type,sum(quantity*b.qty) as req_qty					
						from king_orders a
						join m_product_group_deal_link b on a.itemid = b.itemid
						join products_group_orders go on a.id = go.order_id 
						where status = 0 and go.product_id = ? 
						having req_qty is not null 
					)union
					(
						select b.product_id,'grp' as prod_type,sum(quantity*b.qty) as req_qty					
						from king_orders a
						left join m_product_deal_link b on a.itemid = b.itemid 
						where status = 0 
						and b.product_id = ? 
						having req_qty is not null 
					)
				";
		$res=$this->db->query($sql,array($product_id,$product_id));
		if($res->num_rows())
		{
			return $res->row()->req_qty;
		}
		return 0;
	}
	
	function searchvendorproducts($vid,$q)
	{
		$sql="select p.*,sum(s.available_qty) as stock from m_product_info p left outer join t_stock_info s on s.product_id=p.product_id where (p.product_name like ? or p.barcode = ? ) and (p.product_id in (select product_id from m_vendor_product_link where vendor_id=?) or p.brand_id in (select brand_id from m_vendor_brand_link where vendor_id=?)) group by p.product_id order by p.product_name asc";
		$ret=$this->db->query($sql,array("%$q%",$q,$vid,$vid))->result_array();
		foreach($ret as $i=>$r)
		{
			$ret[$i]['margin']=$this->db->query("select brand_margin from m_vendor_brand_link where vendor_id=? and brand_id=?",array($vid,$r['brand_id']))->row()->brand_margin;
			$ret[$i]['orders']=$this->get_prodpendingorderqty($ret[$i]['product_id']);
		}
		return $ret;
	}
	
	function getticket($id)
	{
		$sql="select u.name as user,t.*,a.name as assignedto from support_tickets t left outer join king_admin a on a.id=t.assigned_to left outer join king_users u on u.userid=t.user_id";
		return $this->db->query("$sql where t.ticket_id=?",$id)->row_array();
	}
	
	function do_updateadminuser($uid)
	{
		foreach(array("roles","name","email","account_blocked") as $i)
			$$i=$this->input->post($i);
			
		$account_blocked = $account_blocked*1;
			
		$access=$roles[0];
		foreach($roles as $i=>$r)
		{
			$r=(int)$r;
			if($i!=0)
				$access=($access|$r);
		}
		$this->db->query("update king_admin set access=?,name=?,email=?,account_blocked=? where id=? limit 1",array($access,$name,$email,$account_blocked,$uid));
		$this->session->set_flashdata("erp_pop_info","Admin access updated");
		redirect("admin/adminusers");
	}
	
	function do_addadminuser()
	{
		foreach(array("roles","name","username","email") as $i)
			$$i=$this->input->post($i);
		$access=0;
		if(!empty($roles))
			$access=$roles[0];
		else $roles=array();
		foreach($roles as $i=>$r)
		{
			$r=(int)$r;
			if($i!=0)
				$access=((int)$access|$r);
		}
		$password=randomChars(6);
		$this->db->query("insert into king_admin(user_id,username,name,email,access,password) values(?,?,?,?,?,?)",array(md5($username),$username,$name,$email,$access,md5($password)));
		$this->vkm->email($email,"Your Snapittoday ERP account","Hi $name,<br><br>Your account to access snapitoday ERP is created successfully.<br>Username : $username<br>Password : $password<br><br>ERP Team Snapittoday");
		$this->session->set_flashdata("erp_pop_info","New Admin user created");
		redirect("admin/adminusers");
	}
	
	function depr_do_proforma_invoice($list)
	{
		$invoices=array();
		if(empty($list))
			return $invoices;
		$orders=$this->db->query("select * from king_orders where id in ('".implode("','",$list)."') order by sno asc")->result_array();
		$c_invno=$this->db->query("select p_invoice_no as invoice_no from proforma_invoices where is_b2b=0 order by id desc limit 1")->row_array();
		if(empty($c_invno))
			$c_invno=10000;
		else $c_invno=$c_invno['invoice_no'];
		$r_pinvno=$this->db->query("select p_invoice_no as invoice_no from proforma_invoices where is_b2b=1 order by id desc limit 1")->row_array();
		if(empty($r_pinvno))
			$p_invno=100000;
		else $p_invno=$r_pinvno['invoice_no'];
		$done_transids=array();
		foreach($orders as $o)
		{
			
			$transid=$o['transid'];
			
			if(!isset($all_trans[$transid]))
				$all_trans[$transid]=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
			$trans=$all_trans[$transid];
 
			$price_amount=$this->db->query("select sum(i.price*o.quantity) as s from king_orders o join king_dealitems i on i.id=o.itemid where o.transid=?",$transid)->row()->s;

			$mrp_amount=$this->db->query("select sum(i.orgprice*o.quantity) as s from king_orders o join king_dealitems i on i.id=o.itemid where o.transid=?",$transid)->row()->s;
			
			$discount=$mrp_amount-$price_amount;
			
			if(!in_array($o['transid'], $done_transids))
			{
				$done_transids[]=$o['transid'];
				if($this->db->query("select is_pnh from king_transactions where transid=?",$transid)->row()->is_pnh=="1")
				{
					$is_b2b=1;
					$invno=$p_invno++;
				}
				else
				{
					$is_b2b=0;
					$invno=$c_invno++;
				}
				$invno++;
				$invoices[]=$invno;
			}
			$oid=$o['id'];

			$orgprice = $o['i_orgprice'];
			$itemdet=$this->db->query("select orgprice,price,nlc,phc,tax,service_tax from king_dealitems where id=?",$o['itemid'])->row_array();

			$o_discount=$orgprice*$discount/$mrp_amount;
			
			$cod=$ship=0;
			
			if($this->db->query("select 1 from king_invoice where transid=?",$o['transid'])->num_rows()==0){
				$cod=$trans['cod'];
				$ship=$trans['ship'];	
			}
			 
			$p_discount = $o['i_discount']+$o['i_coup_discount'];
			
			$offer_price = ($o['i_orgprice'])-$p_discount;
			
			$nlc = round(($offer_price*100/((1+PHC_PERCENT/100)*100)),2);
			$phc = $offer_price-$nlc;
			$tax = $itemdet['tax'];
							
			$this->db->query("insert into proforma_invoices(transid,order_id,p_invoice_no,phc,nlc,tax,service_tax,mrp,discount,cod,ship,invoice_status,createdon,is_b2b) values(?,?,?,?,?,?,?,?,?,?,?,1,?,?)",array($transid,$oid,$invno,$phc,$nlc,$tax,PRODUCT_SERVICE_TAX*100,$orgprice,$p_discount,$cod,$ship,time(),$is_b2b));
			$this->db->query("update king_orders set status=1,actiontime=? where id=? limit 1",array(time(),$o['id']));
		}
		$trans=$this->db->query("select p_invoice_no as invoice_no,transid,count(order_id) as c from proforma_invoices where p_invoice_no in ('".implode("','",$invoices)."') group by transid")->result_array();
		foreach($trans as $tran)
			$this->erpm->do_trans_changelog($tran['transid'],$tran['c']." order(s) processed in Proforma Invoice: {$tran['invoice_no']}");
		return $invoices;
	}
	
	function depr_do_invoice($list)
	{
		$invoices=array();
		if(empty($list))
			return $invoices;
		$orders=$this->db->query("select * from king_orders where id in ('".implode("','",$list)."') order by sno asc")->result_array();
		$c_invno=$this->db->query("select invoice_no from king_invoice where is_b2b=0 order by id desc limit 1")->row()->invoice_no;
		$r_pinvno=$this->db->query("select invoice_no from king_invoice where is_b2b=1 order by id desc limit 1")->row_array();
		if(empty($r_pinvno))
			$p_invno=200000;
		else $p_invno=$r_pinvno['invoice_no'];
		$done_transids=array();
		foreach($orders as $o)
		{
			
			$transid=$o['transid'];
			
			if(!isset($all_trans[$transid]))
				$all_trans[$transid]=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
			$trans=$all_trans[$transid];
 
			$price_amount=$this->db->query("select sum(i.price*o.quantity) as s from king_orders o join king_dealitems i on i.id=o.itemid where o.transid=?",$transid)->row()->s;

			$mrp_amount=$this->db->query("select sum(i.orgprice*o.quantity) as s from king_orders o join king_dealitems i on i.id=o.itemid where o.transid=?",$transid)->row()->s;
			
			$discount=$mrp_amount-$price_amount;
			
			if(!in_array($o['transid'], $done_transids))
			{
				$done_transids[]=$o['transid'];
				if($this->db->query("select is_pnh from king_transactions where transid=?",$transid)->row()->is_pnh=="1")
				{
					$is_b2b=1;
					$invno=$p_invno++;
				}
				else
				{
					$is_b2b=0;
					$invno=$c_invno++;
				}
				$invno++;
				$invoices[]=$invno;
			}
			$oid=$o['id'];

			$orgprice = $o['i_orgprice'];
			$itemdet=$this->db->query("select orgprice,price,nlc,phc,tax,service_tax from king_dealitems where id=?",$o['itemid'])->row_array();

			$o_discount=$orgprice*$discount/$mrp_amount;
			
			$cod=$ship=0;
			
			if($this->db->query("select 1 from king_invoice where transid=?",$o['transid'])->num_rows()==0){
				$cod=$trans['cod'];
				$ship=$trans['ship'];	
			}
			 
			$p_discount = $o['i_discount']+$o['i_coup_discount'];
			
			$offer_price = ($o['i_orgprice'])-$p_discount;
			
			$nlc = round(($offer_price*100/((1+PHC_PERCENT/100)*100)),2);
			$phc = $offer_price-$nlc;
			$tax = $itemdet['tax'];
							
			$this->db->query("insert into king_invoice(transid,order_id,invoice_no,phc,nlc,tax,service_tax,mrp,discount,cod,ship,invoice_status,createdon,is_b2b) values(?,?,?,?,?,?,?,?,?,?,?,1,?,?)",array($transid,$oid,$invno,$phc,$nlc,$tax,PRODUCT_SERVICE_TAX*100,$orgprice,$p_discount,$cod,$ship,time(),$is_b2b));
			$this->db->query("update king_orders set status=1,actiontime=? where id=? limit 1",array(time(),$o['id']));
		}
		$trans=$this->db->query("select invoice_no,transid,count(order_id) as c from king_invoice where invoice_no in ('".implode("','",$invoices)."') group by transid")->result_array();
		foreach($trans as $tran)
			$this->erpm->do_trans_changelog($tran['transid'],"Invoice ({$tran['invoice_no']}) created for ".$tran['c']." order(s)");
		return $invoices;
	}
	
	/**
	 * function to generate distinct proforma invoice no 
	 * @param unknown_type $is_pnh
	 * @return unknown
	 */
	function _get_uniq_proformainvoiceno($transid,$is_pnh = 0)
	{
		while(true)
		{
			$new_p_invno = $this->db->query("select max(p_invoice_no)+1 as p_invoice_no from t_trans_proforma_invoice_marker where is_pnh = ? ",$is_pnh)->row()->p_invoice_no;
			@$this->db->query("insert into t_trans_proforma_invoice_marker (transid,p_invoice_no,is_pnh,created_on) values(?,?,?,now())",array($transid,$new_p_invno,$is_pnh));
			if($this->db->affected_rows() == 1)
			{
				break;
			}
		}
		
		return $new_p_invno;
	}
	
	/**
	 * function to generate actual invoice no  
	 * @param $is_pnh
	 */
	function _get_uniq_invoiceno($transid,$is_pnh = 0)
	{
		while(true)
		{
			$new_invno = $this->db->query("select max(invoice_no)+1 as invoice_no from t_trans_invoice_marker where is_pnh = ? ",$is_pnh)->row()->invoice_no;
			@$this->db->query("insert into t_trans_invoice_marker (transid,invoice_no,is_pnh,created_on) values(?,?,?,now())",array($transid,$new_invno,$is_pnh));
			if($this->db->affected_rows() == 1)
			{
				break;
			}
		}
		
		return $new_invno;
	}
	
	
	
	function do_proforma_invoice($list)
	{
		$invoices=array();
		if(empty($list))
			return $invoices;
			
		$orders=$this->db->query("select i_discount,i_coup_discount,i.tax,t.is_pnh,o.transid,o.id,o.itemid,i_orgprice,i_price,t.cod,t.ship,o.quantity,sum(i.orgprice*o.quantity) as mrp_amt,sum(i.price*o.quantity) as price_amt 
										from king_orders o 
										join king_dealitems i on i.id=o.itemid 
										join king_transactions t on t.transid = o.transid
										where o.id in ('".implode("','",$list)."')
										group by o.id order by o.sno asc")->result_array();
		
		
		$trans_ord_invnos = array();
		foreach($orders as $o)
		{
			 $transid=$o['transid'];
			 $oid=$o['id'];
			 $cod=$ship=0;
			 
			 if(!isset($trans_ord_invnos[$transid]))
			 {
			 	$trans_ord_invnos[$transid] = array();
				
			 	$trans_ord_invnos[$transid]['invno'] = $this->_get_uniq_proformainvoiceno($transid,$o['is_pnh']);
			 	
			 	$trans_ord_invnos[$transid]['trans'] = $this->db->query("select t.cod,t.ship,sum(i.orgprice*o.quantity) as ttl_mrp,sum(i.price*o.quantity) as ttl_price from king_orders o join king_dealitems i on i.id=o.itemid join king_transactions t on t.transid=o.transid where o.transid=?",$transid)->row_array();
			 	
				if($this->db->query("select count(*) as tt from proforma_invoices where transid=?",$transid)->row()->tt==0){
					$cod=$trans_ord_invnos[$transid]['trans']['cod'];
					$ship=$trans_ord_invnos[$transid]['trans']['ship'];	
				}
				array_push($invoices,$trans_ord_invnos[$transid]['invno']);
			 }
			 
			 
			 $mrp_amount = $trans_ord_invnos[$transid]['trans']['ttl_mrp'];
			 $price_amount = $trans_ord_invnos[$transid]['trans']['ttl_price'];
			 
			 $invno = $trans_ord_invnos[$transid]['invno'];

			 $discount = $mrp_amount-$price_amount;
			
			 $orgprice = $o['i_orgprice'];
			 $o_discount=$orgprice*$discount/$mrp_amount;
				
			 	
				 
			$p_discount = $o['i_discount']+$o['i_coup_discount'];
			$offer_price = ($o['i_orgprice'])-$p_discount;
			
			$nlc = round(($offer_price*100/((1+PHC_PERCENT/100)*100)),2);
			$phc = $offer_price-$nlc;
			$tax = $o['tax'];

			$is_b2b = $o['is_pnh']?1:0;	
			
			$this->db->query("insert into proforma_invoices(transid,order_id,p_invoice_no,phc,nlc,tax,service_tax,mrp,discount,cod,ship,invoice_status,createdon,is_b2b) values(?,?,?,?,?,?,?,?,?,?,?,1,?,?)",array($transid,$oid,$invno,$phc,$nlc,$tax,PRODUCT_SERVICE_TAX*100,$orgprice,$p_discount,$cod,$ship,time(),$is_b2b));
			$this->db->query("update king_orders set status=1,actiontime=? where id=? limit 1",array(time(),$o['id']));	
				
		}
		$trans=$this->db->query("select p_invoice_no as invoice_no,transid,count(order_id) as c from proforma_invoices where p_invoice_no in ('".implode("','",$invoices)."') group by transid")->result_array();
		foreach($trans as $tran)
			$this->erpm->do_trans_changelog($tran['transid'],$tran['c']." order(s) processed in Proforma Invoice: {$tran['invoice_no']}");
		return $invoices;
	}
	
	function do_invoice($list)
	{
		$invoices=array();
		if(empty($list))
			return $invoices;
		
		$user = $this->erpm->auth();

		$orders=$this->db->query("select t.is_pnh,i_discount,i_coup_discount,i.tax,t.is_pnh,o.transid,o.id,o.itemid,i_orgprice,i_price,t.cod,t.ship,o.quantity,sum(i.orgprice*o.quantity) as mrp_amt,sum(i.price*o.quantity) as price_amt 
										from king_orders o 
										join king_dealitems i on i.id=o.itemid 
										join king_transactions t on t.transid = o.transid
										where o.id in ('".implode("','",$list)."')
										group by o.id order by o.sno asc")->result_array();

		$trans_ord_invnos = array();
		foreach($orders as $o)
		{
			 $transid=$o['transid'];
			 $oid=$o['id'];
			 $cod=$ship=0;
			 
			 if(!isset($trans_ord_invnos[$transid]))
			 {
			 	$trans_ord_invnos[$transid] = array();

				$trans_ord_invnos[$transid]['trans'] = $this->db->query("select t.cod,t.ship,sum(i.orgprice*o.quantity) as ttl_mrp,sum(i.price*o.quantity) as ttl_price from king_orders o join king_dealitems i on i.id=o.itemid join king_transactions t on t.transid=o.transid where o.transid=?",$transid)->row_array();
			 	
			 	
				if($this->db->query("select count(*) as tt from king_invoice where transid=?",$transid)->row()->tt==0){
					$cod=$trans_ord_invnos[$transid]['trans']['cod'];
					$ship=$trans_ord_invnos[$transid]['trans']['ship'];	
				}

				$trans_ord_invnos[$transid]['invno'] = $this->_get_uniq_invoiceno($transid,$o['is_pnh']);;
				array_push($invoices,$trans_ord_invnos[$transid]['invno']);
			 }
			 
			 
			 $mrp_amount = $trans_ord_invnos[$transid]['trans']['ttl_mrp'];
			 $price_amount = $trans_ord_invnos[$transid]['trans']['ttl_price'];
			 
			 $invno = $trans_ord_invnos[$transid]['invno'];
			 
			 $discount = $mrp_amount-$price_amount;
			
			 $orgprice = $o['i_orgprice'];
			 $o_discount=$orgprice*$discount/$mrp_amount;
				
			 	
				 
			$p_discount = $o['i_discount']+$o['i_coup_discount'];
			$offer_price = ($o['i_orgprice'])-$p_discount;
			
			$nlc = round(($offer_price*100/((1+PHC_PERCENT/100)*100)),2);
			$phc = $offer_price-$nlc;
			$tax = $o['tax'];

			$is_b2b = $o['is_pnh']?1:0;	
			$this->db->query("insert into king_invoice(transid,order_id,invoice_no,phc,nlc,tax,service_tax,mrp,discount,cod,ship,invoice_status,createdon,is_b2b) values(?,?,?,?,?,?,?,?,?,?,?,1,?,?)",array($transid,$oid,$invno,$phc,$nlc,$tax,PRODUCT_SERVICE_TAX*100,$orgprice,$p_discount,$cod,$ship,time(),$is_b2b));
			$this->db->query("update king_orders set status=1,actiontime=? where id=? limit 1",array(time(),$o['id']));	
				
		}
		
		
		
		$trans=$this->db->query("select is_pnh,franchise_id,a.invoice_no,a.transid,c.quantity,sum((mrp-discount)*c.quantity) as inv_amt,count(order_id) as c 
										from king_invoice a 
										join king_transactions b on a.transid = b.transid 
										join king_orders c on c.id = a.order_id 
										where a.invoice_no in ('".implode("','",$invoices)."') group by a.transid")->result_array();
		
		foreach($trans as $tran)
		{
			if($tran['is_pnh'])
			{
				// log to pnh account summary
				$arr = array($tran['franchise_id'],1,$tran['invoice_no'],$tran['inv_amt'],'',1,date('Y-m-d H:i:s'),$user['userid']);
				$this->db->query("insert into pnh_franchise_account_summary (franchise_id,action_type,invoice_no,debit_amt,remarks,status,created_on,created_by) values(?,?,?,?,?,?,?,?)",$arr);
			}
			$this->erpm->do_trans_changelog($tran['transid'],"Invoice ({$tran['invoice_no']}) created for ".$tran['c']." order(s)");
		}
			
		return $invoices;
	}
	
	 
	
	function getcouriers()
	{
		$cs=$this->db->query("select * from m_courier_info order by courier_name asc")->result_array();
		foreach($cs as $i=>$c)
		{
			$cs[$i]['pincodes']=$this->db->query("select count(1) as l from m_courier_pincodes where courier_id=?",$c['courier_id'])->row()->l;
			$awb=$this->db->query("select * from m_courier_awb_series where courier_id=?",$c['courier_id'])->row_array();
			$cs[$i]['awb']=$awb['awb_no_prefix'].$awb['awb_current_no'].$awb['awb_no_suffix'];
			$cs[$i]['rem_awb']=$awb['awb_end_no']-$awb['awb_current_no'];
		}
		return $cs;
	}
	
	function getpincodesforcourier($cid)
	{
		$raw=$this->db->query("select pincode from m_courier_pincodes where courier_id=? and status=1 order by pincode asc",$cid)->result_array();
		$pincodes=array();
		foreach($raw as $r)
			$pincodes[]=$r['pincode'];
		return $pincodes;
	}
	
	function do_updatepincodesforcourier($courier_id,$pincodes)
	{
		if(!is_array($pincodes))
			$pincodes=explode(",",$pincodes);
		$pincodes=array_unique($pincodes);
		$this->db->query("delete from m_courier_pincodes where courier_id=?",$courier_id);
		foreach($pincodes as $p)
			$this->db->query("insert into m_courier_pincodes(courier_id,pincode,status) values(?,?,1)",array($courier_id,$p));
	}
	
	function do_updateawb($cid)
	{
		foreach(array("courier","name","awb_prefix","awb_suffix","awb_start","awb_end","pincodes") as $inp)
			$$inp=$_POST[$inp];
		$courier=$cid;
		$this->db->query("delete from m_courier_awb_series where courier_id=?",$courier);
		$this->db->query("insert into m_courier_awb_series(courier_id,awb_no_prefix,awb_no_suffix,awb_start_no,awb_end_no,awb_current_no,is_active) values(?,?,?,?,?,?,1)",array($courier,$awb_prefix,$awb_suffix,$awb_start,$awb_end,$awb_start));
	}
	
	function do_addcourier()
	{
		foreach(array("name","awb_prefix","awb_suffix","awb_start","awb_end","pincodes") as $inp)
			$$inp=$_POST[$inp];
		$sql="insert into m_courier_info(courier_name) values(?)";
		$this->db->query($sql,$name);
		$courier_id=$this->db->insert_id();
		$pincodes=explode(",",$pincodes);
		$this->erpm->do_updatepincodesforcourier($courier_id,$pincodes);
		$this->db->query("insert into m_courier_awb_series(courier_id,awb_no_prefix,awb_no_suffix,awb_start_no,awb_end_no,awb_current_no,is_active) values(?,?,?,?,?,?,1)",array($courier_id,$awb_prefix,$awb_suffix,$awb_start,$awb_end,$awb_start));
		$this->session->set_flashdata("erp_pop_info","New courier added");
		redirect("admin/courier");
	}
	
	function do_changepwd()
	{
		$user=$this->erpm->getadminuser();
		foreach(array("exp","new") as $i)
			$$i=$this->input->post($i);
//		$this->db->query("select 1 from king_admin where id=? and password=?",array($user['userid'],md5($exp)))->
	}
	
	function do_shipment_batch_process()
	{
		ini_set('memory_limit','512M');
		$i_transid=false;
		$ttl_num = $num=$this->input->post("num_orders");
		$process_partial=$this->input->post("process_partial");
		
		
		
		if(empty($num))
			show_error("Enter no of orders to process");
		$i_transid=$this->input->post("transid");

		$ordersby = strtolower($this->input->post("snp_pnh"));
		$en_date = $this->input->post("en_date");
		
		$down_import_summ = 0;
		$cond = '';
		$is_pnh=1;
		 
		if($ordersby == 'others')
		{
			$is_pnh=0;
			$cond .= ' and t.is_pnh = 0 ';
			$snp_pnh_part = $this->input->post("snp_pnh_part");
			if(count($snp_pnh_part))
			{
				$cond .= ' and partner_id in ('.implode(',',$snp_pnh_part).') ';	
			}else
			{
				show_error("Please select atleast one partner");
			}
			
			$process_orderby = $this->input->post('process_orderby');
			$by_brandid = $this->input->post('by_brandid');
			
			//$by_p_oids = $this->input->post('by_p_oids');
			if($process_orderby)
			{
				if(count($snp_pnh_part) == 1)
				{
					if($process_orderby == 1)
					{
						$down_import_summ = 1;
						$p_oids = $this->input->post('p_oids');
						
						$p_oids = (explode(',',$p_oids));
						$tmp_poids = array();
						foreach($p_oids as $t_poid)
						{
							if($t_poid)
								array_push($tmp_poids,'"'.$t_poid.'"');
						}
						$p_oids = implode(',',$tmp_poids); 
						
						if(!$p_oids)
						{
							show_error("No Partner Ordernos added.");	
						}else
						{
							$cond .=  ' and partner_reference_no in ('.$p_oids.')';
							$down_import_summ = 1;
							$ttl_num = count(explode(',',$p_oids));
						}
					}else 
					{
						if($by_brandid)
							$cond .=  ' and d.brandid = '.$by_brandid.' ';
						else 
							show_error("No brand selected");	
					}
					
				}else
				{
					show_error("Order ids can be processed only for one partner.");
				}
			}
			
			
		}else
		{
			$cond .= ' and t.is_pnh = 1 ';
			$is_pnh=1;
			
			$by_menu = $this->input->post('by_menu');
			if($by_menu)
			{
				$pmenu_idlist = $this->input->post('pmenu_id');
				if($pmenu_idlist)
				{
					$cond .= ' and menuid in ('.implode(',',$pmenu_idlist).') ';
				}else
				{
					show_error(" No menu selected");
				}
			}
		}
		
		$trans=array();
		$itemids=array();

		if($en_date)
		{
			list($ey,$em,$ed) = explode('-',$en_date);
			$en_date_ts = mktime(23,59,59,$em,$ed,$ey);
			$cond .= ' and t.init < '.$en_date_ts.' ';
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
		if(empty($trans))
			show_error("No orders to process");
			
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
			$itemid=$this->db->query("select itemid from king_orders where id=?",$r['order_id'])->row()->itemid;
			$qty=$this->db->query("select l.qty from products_group_pids p join m_product_group_deal_link l on l.group_id=p.group_id where p.product_id=? and itemid = ? ",array($r['product_id'],$itemid))->row()->qty;
			
			if(!isset($products[$itemid]))
				$products[$itemid]=array();
				
			$products[$itemid][]=array("itemid"=>$itemid,"qty"=>$qty,"product_id"=>$r['product_id'],"order_id"=>$r['order_id']);
			$productids[]=$r['product_id'];
			
		
					
		}
		
		
		

		$to_process_orders=array();
		$raw_stock=$this->db->query("select product_id,sum(available_qty) as stock from t_stock_info where product_id in ('".implode("','",$productids)."') group by product_id")->result_array();
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
			foreach($orders as $order)
			{
				$itemid=$order['itemid'];
				
				if(!isset($products[$itemid]))
					continue;
				
				$pflag=true;
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
						//echo ($stock[$p['product_id']]).'-'.$p['product_id'].' - '.$p['qty'].' - '.$order['quantity'].' ';
						if($stock[$p['product_id']]<$p['qty']*$order['quantity'])
						{
							
							$pflag=false;
							break;
						}
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
					if(in_array($order['id'],$same_order))
					{
						if(!isset($products[$itemid]))
						continue;
						foreach($products[$order['itemid']] as $p)
							$stock[$p['product_id']]-=$p['qty']*$order['quantity'];
					}
						
			}
			
			
			
			if($total_orders_process>=$ttl_num)
				break;
		}
			 
		$orders=array_unique($to_process_orders);
		$invoices=$this->erpm->do_proforma_invoice($orders);
		
		$batch_id=0;
		$batch_inv_link = array();
		
		$ttl_invoices = count($invoices);
		if($ttl_invoices > $num)
			$ttl_batchs = ceil($ttl_invoices/$num);
		else 
			$ttl_batchs = 1;	
			
		$batch_remarks = $this->input->post('batch_remarks');
		
		if(!empty($invoices))
		{
			for($b=0;$b<$ttl_batchs;$b++)
			{
				$s = $b*$num;
				$ttl_inbatch = ((($s+$num) > $ttl_invoices)?$ttl_invoices-$s:$num);
				
				$this->db->query("insert into shipment_batch_process(num_orders,batch_remarks,created_on) values(?,?,?)",array($ttl_inbatch,$batch_remarks,date('Y-m-d H:i:s')));
				$batch_id=$this->db->insert_id();
				for($k=$s;$k<$s+$ttl_inbatch;$k++)
				{
					$inv = $invoices[$k];
					$batch_inv_link[$inv] = $batch_id;
					$cid=0;
					$awb="";
					$this->db->query("insert into shipment_batch_process_invoice_link(batch_id,p_invoice_no,courier_id,awb) values(?,?,?,?)",array($batch_id,$inv,$cid,$awb));
				}
			}
		}
		
		 
		$down_summary = array(); 
		foreach($orders as $o)
		{
			$pinv_det=$this->db->query("select id,p_invoice_no from proforma_invoices where order_id=? order by id desc ",$o)->row_array();
			$invid = $pinv_det['id'];
			$pinvno = $pinv_det['p_invoice_no'];
			
			$s_prods=$this->db->query("select t.partner_reference_no,o.transid,o.id,p.product_id,p.qty,o.quantity,o.i_orgprice,o.id as order_id from king_orders o join m_product_deal_link p on p.itemid=o.itemid join king_transactions t on t.transid  = o.transid  where o.id=? order by o.sno asc",$o)->result_array();
			
			$s_prods_1=$this->db->query("select t.partner_reference_no,o.transid,o.id,go.product_id,p.qty,o.quantity,o.i_orgprice,o.id as order_id 
												from king_orders o 
												join king_transactions t on t.transid  = o.transid 
												join products_group_orders go on go.order_id = o.id 
												join m_product_group_deal_link p on p.itemid=o.itemid 
												where o.id=? order by o.sno asc",$o)->result_array();
			
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
				 
				$pen_qty = $total_qty;
				
				// query to fetch stock product ordered by exact mrp and followed by asc mrp.  	
				
				$sql = "select stock_id,product_id,available_qty,location_id,rack_bin_id,mrp,if((mrp-$omrp),1,0) as mrp_diff 
						from t_stock_info where mrp > 0  and product_id = ? and available_qty > 0 
						order by product_id desc,mrp_diff,mrp ";
				
				
				$stk_prod_list = $this->db->query($sql,$p['product_id'])->result_array();
				
				// iterate all stock product 
				foreach($stk_prod_list as $stk_prod)
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
					 
						$pen_qty = $pen_qty-$reserv_qty;
						 
					// if all qty updated 
					if(!$pen_qty)	
						break;
					
				}
				
				
				foreach($alloted_stock as $allot_stk)
				{
					$this->db->query("update t_stock_info set available_qty = available_qty-? where stock_id = ? ",array($allot_stk['qty'],$allot_stk['stock_info_id']));
					$this->db->insert("t_reserved_batch_stock",$allot_stk);
				}
				$this->erpm->do_stock_log(0,$p['quantity']*$p['qty'],$p['product_id'],$invid,false,false,true);
				
			}
			
		}
		
		
		
		if($down_import_summ)
		{
			 
				$csv_oids = array();
				$csv_oids[] = '"Partner Reference no","Batch ID","Invoice no"';
				$p_oids = $this->input->post('p_oids');
				$p_oid_arr = explode(',',$p_oids);
				foreach($p_oid_arr as $p_oid)
				{
					$tmp = array();
					$tmp['partner_reference_no'] = $p_oid;
					$tmp['batch_id'] = '';
					$tmp['p_invoice_no'] = '';
					if(isset($down_summary[$p_oid]))
					{
						$tmp['p_invoice_no'] = isset($down_summary[$p_oid])?$down_summary[$p_oid]:'';
						$tmp['batch_id'] = isset($batch_inv_link[$tmp['p_invoice_no']])?$batch_inv_link[$tmp['p_invoice_no']]:'';	
					}else
					{
						//$pinv_det = $this->db->query("select ");
					}
					
					array_push($csv_oids,'"'.implode('","',$tmp).'"');
				}
				
				header('Content-Type: application/csv');
				header('Content-Disposition: attachment; filename=import_stat.csv');
				header('Pragma: no-cache');

				echo implode("\r\n",$csv_oids);
				exit;
			 
		}else
		{
			if(!count($batch_inv_link))
				show_error("INSUFFICIENT STOCK TO PROCESS ANY ORDER");
			redirect("admin/batch/$batch_id");		
		}
		
	}
	
	function depr_do_shipment_batch_process()
	{
		$num=$this->input->post("num_orders");
		if(empty($num))
			show_error("Enter no of orders to process");
		$trans=array();
//		while(1)
		{
//			$raw_trans=$this->db->query("select t.transid from king_transactions t join king_orders o on o.transid=t.transid and t.batch_enabled=1 and o.status=0 join m_courier_pincodes mp on mp.pincode=o.ship_pincode or mp.pincode='999999' join m_courier_awb_series ma on ma.awb_end_no>ma.awb_current_no and ma.courier_id=mp.courier_id group by t.transid order by t.priority desc, t.init asc limit $num")->result_array();
			$raw_trans=$this->db->query("select t.transid from king_transactions t join king_orders o on o.transid=t.transid and t.batch_enabled=1 and o.status=0 group by t.transid order by t.priority desc, t.init asc limit $num")->result_array();
//			if(empty($raw_trans))
//				return;
			foreach($raw_trans as $t)
			{
				
				
/*	courier disabled			
 * 				$flag=false;
				$o=$this->db->query("select m.courier_id,o.ship_pincode,m.awb_end_no,m.awb_current_no from king_orders o join m_courier_pincodes p on p.pincode=o.ship_pincode or p.pincode='999999' join m_courier_awb_series m on m.courier_id=p.courier_id where o.transid=? and o.status=0 order by p.pincode asc limit 1",$t['transid'])->row_array();
				if(!isset($awbs[$o['courier_id']]))
					$awbs[$o['courier_id']]=$o['awb_current_no'];
				if($awbs[$o['courier_id']]<=$o['awb_end_no'])
				{
					$trans[]=$t['transid'];
					$awbs[$o['courier_id']]++;
				}
courier disable ends 
*/			
				
				$trans[]=$t['transid'];
//				if(count($trans)>=$num)
//					break;
			}
//			if(count($trans)>=$num)
//				break;
		}
		if(empty($trans))
			show_error("No orders to process");
		$porders=$this->db->query("select o.* from king_orders o join king_transactions t on t.transid=o.transid where o.status=0 and o.transid in ('".implode("','",$trans)."') order by t.priority desc, o.sno asc")->result_array();
		$items=array();
		foreach($porders as $o)
		{
			if(!in_array($o['itemid'],$items))
				$items[]=$o['itemid'];
		}
		foreach($items as $item)
		{
			$ps=$this->db->query("select itemid,product_id,qty from m_product_deal_link where itemid=?",$item)->result_array();
			$prods[$item]=array();
			foreach($ps as $p)
			{
				$prods[$p['itemid']][]=$p;
				$pids[]=$p['product_id'];
			}
		}
		$raw_stock=$this->db->query("select product_id,sum(available_qty) as s from t_stock_info where product_id in ('".implode("','",$pids)."') group by product_id")->result_array();
		foreach($pids as $i)
			$stock[$i]=0;
		foreach($raw_stock as $s)
			$stock[$s['product_id']]=$s['s'];
		$orders=array();
		foreach($porders as $o)
		{
			$itemid=$o['itemid'];
			$f=true;
			foreach($prods[$itemid] as $p)
				if($stock[$p['product_id']]<$p['qty']*$o['quantity'])
				{
					$f=false;
					break;
				}
			if(!$f)
				continue;
			foreach($prods[$itemid] as $p)
				$stock[$p['product_id']]-=$p['qty']*$o['quantity'];
			$orders[]=$o['id'];
		}
		$batch_id=0;
		$invoices=$this->erpm->do_invoice($orders);
		if(!empty($invoices))
		{
			$this->db->query("insert into shipment_batch_process(num_orders,created_on) values(?,now())",array(count($invoices)));
			$batch_id=$this->db->insert_id();
			foreach($invoices as $inv)
			{
/*	courier disabled			
 * 				$order=$this->db->query("select mp.courier_id,o.ship_pincode from king_invoice i join king_orders o on o.id=i.order_id join m_courier_pincodes mp on mp.pincode=o.ship_pincode or mp.pincode='999999' order by mp.pincode asc limit 1")->row_array();
				$cid=$order['courier_id'];
				if(!isset($couriers[$cid]))
				{
					$courier=$this->db->query("select awb_end_no,awb_current_no from m_courier_awb_series where courier_id=?",$cid)->row_array();
					$couriers[$order['courier_id']]=$courier['awb_current_no'];
					$prefix[$cid]=$courier['awb_no_prefix'];
					$suffix[$cid]=$courier['awb_no_suffix'];
				}
				$awb="{$prefix[$cid]}{$couriers[$cid]}{$suffix[$cid]}";
	courier disable ends
*/	
				$cid=0;
				$awb="";
				
				$this->db->query("insert into shipment_batch_process_invoice_link(batch_id,invoice_no,courier_id,awb) values(?,?,?,?)",array($batch_id,$inv,$cid,$awb));

/*	courier disabled			
				$couriers[$cid]++;
			}
			foreach($couriers as $cid=>$awb)
				$this->db->query("update m_courier_awb_series set awb_current_no=? where courier_id=? limit 1",array($awb,$cid));
*/
			}
		}
		foreach($orders as $o)
		{
			$s_prods=$this->db->query("select p.product_id,p.qty,o.quantity from king_orders o join m_product_deal_link p on p.itemid=o.itemid where o.id=? order by o.sno asc",$o)->result_array();
			foreach($s_prods as $p)
				for($i=1;$i<=$p['quantity']*$p['qty'];$i++)
					$this->db->query("update t_stock_info set available_qty=available_qty-1 where product_id=? and available_qty!=0 order by stock_id asc limit 1",$p['product_id']);
		}
		return $batch_id;
	}
	
	function do_updateclient($cid)
	{
		$user=$this->erpm->getadminuser();
		foreach(array("name","addr1","addr2","locality","city","state","country","remarks","credit_limit","credit_days","cst","pan","vat","tax","c_name","c_design","c_mob1","c_mob2","c_email1","c_email2","c_fax") as $i)
			$$i=$this->input->post($i);
		$sql="update m_client_info set client_name=?,address_line1=?,address_line2=?,locality=?,city_name=?,state_name=?,country=?,credit_limit_amount=?,credit_days=?,cst_no=?,pan_no=?,vat_no=?,service_tax_no=?,remarks=?,modified_by=?,modified_on=now() where client_id=?"; 
		$this->db->query($sql,array($name,$addr1,$addr2,$locality,$city,$state,$country,$credit_limit,$credit_days,$cst,$pan,$vat,$tax,$remarks,$user['userid'],$cid));
			
		$this->db->query("delete from m_client_contacts_info where client_id=?",$cid);
		$sql="insert into m_client_contacts_info(client_id,contact_name,contact_designation,mobile_no_1,mobile_no_2,email_id_1,email_id_2,fax_no,created_by,created_on)
																					values($cid,?,?,?,?,?,?,?,?,now())";
		foreach($c_name as $i=>$c)
		{
			$inp=array($c,$c_design[$i],$c_mob1[$i],$c_mob2[$i],$c_email1[$i],$c_email2[$i],$c_fax[$i],$user['userid']);
			$this->db->query($sql,$inp);
		}
		$this->session->set_flashdata("erp_pop_info","Client details updated");
		redirect("admin/clients");
	}
	
	function do_addclient()
	{
		$user=$this->erpm->getadminuser();
		foreach(array("name","addr1","addr2","locality","city","state","country","remarks","credit_limit","credit_days","cst","pan","vat","tax","c_name","c_design","c_mob1","c_mob2","c_email1","c_email2","c_fax") as $i)
			$$i=$this->input->post($i);
		$sql="insert into m_client_info(client_code,client_name,address_line1,address_line2,locality,city_name,state_name,country,credit_limit_amount,credit_days,cst_no,pan_no,vat_no,service_tax_no,remarks,created_by,created_on) 
														values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now())";
		$inp=array(randomChars(5),$name,$addr1,$addr2,$locality,$city,$state,$country,$credit_limit,$credit_days,$cst,$pan,$vat,$tax,$remarks,$user['userid']);
		$this->db->query($sql,$inp);
		$cid=$this->db->insert_id();
		$sql="insert into m_client_contacts_info(client_id,contact_name,contact_designation,mobile_no_1,mobile_no_2,email_id_1,email_id_2,fax_no,created_by,created_on)
																					values($cid,?,?,?,?,?,?,?,?,now())";
		foreach($c_name as $i=>$c)
		{
			$inp=array($c,$c_design[$i],$c_mob1[$i],$c_mob2[$i],$c_email1[$i],$c_email2[$i],$c_fax[$i],$user['userid']);
			$this->db->query($sql,$inp);
		}
		$this->session->set_flashdata("erp_pop_info","New client added");
		redirect("admin/clients");
	}
	
	function do_closeclientorder($oid)
	{
		$user=$this->erpm->getadminuser();
		$sql="update t_client_order_info set order_status=3,modified_on=now(),modified_by=? where order_id=?";
		$this->db->query($sql,array($user['userid'],$oid));
		$this->session->set_flashdata("erp_pop_info","Client order closed");
		redirect("admin/client_order/$oid");
	}
	
	function do_payment_client($inv)
	{
		$admin=$this->erpm->getadminuser();
		foreach(array("amount","type","inst_no","inst_date","bank","cleared","remarks","done") as $i)
			$$i=$this->input->post("$i");
		$inp=array($inv,$amount,$type,$inst_no,$inst_date,$bank,$cleared,$remarks,$admin['userid']);
		$this->db->query("insert into t_client_invoice_payment(invoice_id,amount_paid,payment_type,instrument_no,instrument_date,bank_name,is_cleared,remarks,created_by,created_on) values(?,?,?,?,?,?,?,?,?,now())",$inp);
		$s=$done?"1":"2";
		$this->db->query("update t_client_invoice_info set payment_status=$s where invoice_id=? limit 1",$inv);
		$this->session->set_flashdata("erp_pop_info","Client payment updated");
		redirect("admin/client_invoice/$inv");
	}
	
	function getclientinvoice($inv)
	{
		return $this->db->query("select cc.contact_name,i.*,c.* from t_client_invoice_info i join m_client_info c on c.client_id=i.client_id left outer join m_client_contacts_info cc on cc.client_id=i.client_id and cc.active_status=1 where i.invoice_id=? order by cc.id asc limit 1",$inv)->row_array();
	}
	
	function getclientinvoiceforprint($inv)
	{
		return $this->db->query("select pi.*,p.product_name from t_client_invoice_product_info pi join m_product_info p on p.product_id=pi.product_id where pi.invoice_id=?",$inv)->result_array();
	}
	
	function do_stock_log($in,$qty,$pid,$grn_iid,$corp=false,$cancel_invoice=false,$proforma_invoice=false,$mrp_change_updated=-1)
	{
		if($cancel_invoice)
			$in=0;
		$adm=$this->erpm->getadminuser();
		$cur=$this->db->query("select sum(available_qty) as s from t_stock_info where product_id=?",$pid)->row()->s;
		$inp=array($pid,$in,0,0,0,0,$qty,$cur,$adm['userid']);
		if($proforma_invoice)
			$inp[2]=$grn_iid;
		else{
			if($corp && !$in)
				$inp[3]=$grn_iid;
			else if(!$in)
				$inp[4]=$grn_iid;
			else 
				$inp[5]=$grn_iid;
		}
		if($cancel_invoice)
			$inp[1]=1;
			$inp[] = $mrp_change_updated;
		$this->db->query("insert into t_stock_update_log(product_id,update_type,p_invoice_id,corp_invoice_id,invoice_id,grn_id,qty,current_stock,created_on,created_by,mrp_change_updated) values(?,?,?,?,?,?,?,?,now(),?,?)",$inp);
	}
	
	function do_clientinvoice()
	{
		$admin=$this->erpm->getadminuser();
		foreach(array("invoice_date","client","products","order_id","mrp","offer","tax","iqty") as $i)
			$$i=$this->input->post($i);
		$invoice_no=$this->db->query("select invoice_no as i from t_client_invoice_info order by invoice_id desc limit 1")->row_array();
		if(empty($invoice_no))
			$invoice_no=100028;
		else
			$invoice_no=$invoice_no['i']+1;
		$total=0;
		foreach($offer as $i=>$o)
			$total+=($o*$iqty[$i]);
		$sql="insert into t_client_invoice_info(invoice_no,invoice_date,client_id,total_invoice_value,invoice_status,payment_status,created_date,created_by) values(?,?,?,?,0,0,now(),?)";
		$this->db->query($sql,array($invoice_no,$invoice_date,$client,$total,$admin['userid']));
		$iid=$this->db->insert_id();
		$sql="insert into t_client_invoice_product_info(order_id,invoice_id,product_id,mrp,margin_offered,offer_price,tax_percent,invoice_qty,created_on,created_by) 
																	values(?,?,?,?,?,?,?,?,now(),?)";
		foreach($order_id as $i=>$o)
		{
			if($iqty[$i]==0)
				continue;
			$inp=array($o,$iid,$products[$i],$mrp[$i],$mrp[$i]-$offer[$i],$offer[$i],$tax[$i],$iqty[$i],$admin['userid']);
			$this->db->query($sql,$inp);
			for($i2=1;$i2<=$iqty[$i];$i2++)
				$this->db->query("update t_stock_info set available_qty=available_qty-1 where product_id=? and available_qty!=0 order by stock_id asc limit 1",$products[$i]);
			$this->erpm->do_stock_log(0,$iqty[$i],$products[$i],$iid,1);
			$this->db->query("update t_client_order_product_info set invoiced_qty=invoiced_qty+? where order_id=? and product_id=?",array($iqty[$i],$o,$products[$i]));
			if($this->db->query("select count(1) as l from t_client_order_product_info where order_qty>invoiced_qty and order_id=?",array($o))->num_rows()==0)
				$this->db->query("update t_client_order_info set order_status=2 where order_id=? limit 1",$o);
		}
		$this->session->set_flashdata("erp_pop_info","Client Invoicing done");
		redirect("admin/client_invoice/$iid");
	}
	
	function getinvoicesforclientorder($oid)
	{
		return $this->db->query("select ci.invoice_no,ci.invoice_id,ci.total_invoice_value,ci.payment_status from t_client_invoice_product_info cip join t_client_invoice_info ci on ci.invoice_id=cip.invoice_id where cip.order_id=? group by ci.invoice_id",$oid)->result_array();
	}
	
	function getclientorder($oid)
	{
		$sql="select t.*,c.client_name as client,m.name as closed_by,a.name as created_by from t_client_order_info t join m_client_info c on c.client_id=t.client_id join king_admin a on a.id=t.created_by left outer join king_admin m on m.id=t.modified_by where t.order_id=?";
		$ret['order']=$this->db->query("$sql",$oid)->row_array();
		$sql="select p.product_name as product,t.* from t_client_order_product_info t join m_product_info p on p.product_id=t.product_id where order_id=?";
		$ret['items']=$this->db->query($sql,$oid)->result_array();
		return $ret;
	}
	
	function getclientordersforinvoice($oid)
	{
		$sql="select p.vat as tax,p.product_name as product,t.* from t_client_order_product_info t join m_product_info p on p.product_id=t.product_id where order_id=?";
		$items=$this->db->query($sql,$oid)->result_array();
		foreach($items as $i=>$item)
			$items[$i]['stock']=(int)$this->db->query("select sum(available_qty) as l from t_stock_info where product_id=?",$item['product_id'])->row()->l;
		return $items;
	}
	
	function getclientinvoices($s=false,$e=false)
	{
		if(!$e)
		{
			$s=date("Y-m-d",mktime(0,0,0,date("m"),1));
			$e=date("Y-m-d",mktime(0,0,0,date("m")+1,date("t")));
		}
		return $this->db->query("select i.*,a.name as created_by from t_client_invoice_info i join king_admin a on a.id=i.created_by where i.created_date between ? and ?",array($s,$e))->result_array();
	}
	
	function getclientorders($s=false,$e=false)
	{
		if(!$e)
		{
			$s=date("Y-m-d",mktime(0,0,0,date("m"),1));
			$e=date("Y-m-d",mktime(0,0,0,date("m")+1,date("t")));
		}
		$sql="select t.*,c.client_name as client,a.name as created_by from t_client_order_info t join m_client_info c on c.client_id=t.client_id join king_admin a on a.id=t.created_by where t.created_on between ? and ? order by t.order_id desc";
		return $this->db->query($sql,array($s,$e))->result_array();
	}
	
	function getclientordersbyclient($cid=false)
	{
		$sql="select t.*,c.client_name as client,a.name as created_by from t_client_order_info t join m_client_info c on c.client_id=t.client_id join king_admin a on a.id=t.created_by where t.client_id=? order by t.order_id desc";
		return $this->db->query($sql,array($cid))->result_array();
	}
	
	function getclients()
	{
		return $this->db->query("select * from m_client_info order by client_name asc")->result_array();
	}
	
	function do_cancel_orders()
	{
		$user=$this->erpm->getadminuser();
		foreach(array("refund","oids","transid","msg","no_send") as $i)
			$$i=$this->input->post($i);
		$oids=explode(",",$oids);
		$this->db->query("insert into t_refund_info(transid,amount,status,created_on,created_by) values(?,?,?,?,?)",array($transid,$refund,0,time(),$user['userid']));
		$rid=$this->db->insert_id();
		foreach($oids as $i=>$o)
		{
			$qty=$this->db->query("select quantity from king_orders where id=?",$o)->row()->quantity;
			$this->db->query("insert into t_refund_order_item_link(refund_id,order_id,qty) values(?,?,?)",array($rid,$o,$qty));
			$this->db->query("update king_orders set status=3,actiontime=".time()." where id=? limit 1",$o);
		}
		$email=$this->db->query("select bill_email as m from king_orders where id=?",$o)->row()->m;
		if(!$no_send)
			$this->vkm->email($email,"Important refund information for $transid",nl2br($msg));
		$this->erpm->do_trans_changelog($transid,count($oids)." order(s) cancelled");
		$this->session->set_flashdata("erp_pop_info","Orders cancelled");
		redirect("admin/trans/$transid");
	}
	
	function do_reship_order()
	{
		foreach(array("transid","oids") as $i)
			$$i=$this->input->post($i);
		if(empty($oids))
			show_error("No orders were selected");
		$orders=$this->db->query("select * from king_orders where id in ('".implode("','",$oids)."')")->result_array();
		$total=0;
		$n_transid=strtoupper("SNR".random_string("alpha",3).random_string("nozero",5));
		$items="";
		foreach($orders as $o)
		{
			unset($o['sno']);
			$o['transid']=$n_transid;
			$o['id']=random_string("numeric",10);
			$o['status']=0;
			$o['actiontime']=0;
			$this->db->insert("king_orders",$o);
			$total+=($o['i_price']-$o['i_coup_discount'])*$o['quantity'];
			$items.=$this->db->query("select name from king_dealitems where id=?",$o['itemid'])->row()->name."<br>";
		}
		$trans=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
		unset($trans['id']);
		$trans['amount']=$total;
		$trans['paid']=0;
		$trans['ship']=$trans['cod']=0;
		$trans['transid']=$n_transid;
		$trans['mode']=0;
		$this->db->insert("king_transactions",$trans);
		foreach($this->db->query("select * from king_transaction_notes where transid=?",$transid)->result_array() as $n)
		{
			unset($n['id']);
			$n['transid']=$n_transid;
			$this->db->insert("king_transaction_notes",$n);
		}
		$this->erpm->do_trans_changelog($transid,"$n_transid created for reshipment of following orders<br>".$items);
		$this->erpm->do_trans_changelog($n_transid,"Reshipment of $transid for following orders<br>".$items);
		redirect("admin/trans/$n_transid");
	}
	
	function do_pack()
	{
		 
		$p_invoice=$this->input->post("invoice");
		$pids=explode(",",$this->input->post("pids"));
		$pbcodes=$this->input->post("pbc");
		
		$lgn_user = $this->auth();
		 
		
		 
		
		if(!$pbcodes)
		{
			show_error("Sorry No Stock Selected");
			die();
		}
			
		foreach($pids as $pid)
		{
			$imeis[$pid]=array();
			if($this->input->post("imei$pid"))
				$imeis[$pid]=$this->input->post("imei$pid");
		}
		
		$ord_item_ids = array();
		
		$r_need_pids=$this->db->query("select l.product_id,l.itemid,o.id from proforma_invoices i join king_orders o on o.id=i.order_id join m_product_deal_link l on l.itemid=o.itemid where i.p_invoice_no=?",$p_invoice)->result_array();
		$ret2=$this->db->query("select p.product_id,o.id,o.itemid from proforma_invoices i join king_orders o on o.id=i.order_id join products_group_orders pgo on pgo.order_id=o.id join m_product_group_deal_link pl on pl.itemid=o.itemid join m_product_info p on p.product_id=pgo.product_id join king_dealitems d on d.id=o.itemid where i.p_invoice_no=?",$p_invoice)->result_array();
		$r_need_pids=array_merge($r_need_pids,$ret2);
		$need_pids = array();
		foreach($r_need_pids as $r)
		{
			$oids[$r['id']]=$r['id'];
			if(!isset($need_pids[$r['id']]))
				$need_pids[$r['id']]=array();
			$need_pids[$r['id']][]=$r['product_id'];
			
			$ord_item_ids[$r['itemid']] = $r['id'];
		}
		$p_oids=array();
		foreach($need_pids as $oid=>$p)
		{
			$f=true;
			foreach($p as $pp)
				if(!in_array($pp,$pids))
					$f=false;
			if($f)
				$p_oids[]=$oid;
		}
		
		
		$inv=$this->erpm->do_invoice($p_oids);
		$orders=$this->db->query("select quantity as qty,itemid,id from king_orders where id in ('".implode("','",$p_oids)."')")->result_array();
		foreach($orders as $o)
		{
			$pls=$this->db->query("select qty,pl.product_id,p.mrp from m_product_deal_link pl join m_product_info p on p.product_id=pl.product_id where itemid=?",$o['itemid'])->result_array();
			foreach($pls as $p)
			{
				foreach($imeis[$p['product_id']] as $il=>$imei)
				{
					if($imei==0)
						continue;
					$this->db->query("update t_imei_no set order_id=?,status=1 where imei_no=? limit 1",array($o['id'],$imei));
				}
			}
		}
		$invoice_no=$inv[0];

		 
		$c_oids=array();
		foreach($oids as $oid)
		{
			if(!in_array($oid,$p_oids))
				$c_oids[]=$oid;
				
			$ord_item_link[$oid['itemid']] = $oid;	
		}		
		if(!empty($c_oids))
		$orders=$this->db->query("select quantity as qty,itemid,id from king_orders where id in ('".implode("','",$c_oids)."')")->result_array();
		else $orders=array();
		
		$batch_id = $this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no = ? ",$p_invoice)->row()->batch_id;
		
		 
		
		// revert cancelled orders stock  
		foreach($orders as $o)
		{
			$pls=$this->db->query("select qty,pl.product_id,p.mrp from m_product_deal_link pl join m_product_info p on p.product_id=pl.product_id where itemid=?",$o['itemid'])->result_array();
			foreach($pls as $p)
			{
				
		
				/*
				$check_mrp=false;
				if($this->db->query("select 1 from t_stock_info where product_id=? and mrp=?",array($p['product_id'],$p['mrp']))->num_rows()==1)
					$check_mrp=true;
				$sql="update t_stock_info set available_qty=available_qty+? where product_id=?";
				if($check_mrp)
					$sql.=" and mrp=?";
				$sql.=" limit 1";
				$this->db->query($sql,array($p['qty']*$o['qty'],$p['product_id'],$p['mrp']));
				*/
				
				// get resered qty for product from stock and revert back to stock master
				$p_reserv_qty = 0;
				$reserv_stk_res = $this->db->query('select id,stock_info_id,qty from t_reserved_batch_stock where batch_id = ? and p_invoice_no = ? and order_id = ? and product_id = ? ',array($batch_id,$p_invoice,$o['id'],$p['product_id']));
				if($reserv_stk_res->num_rows())
				{
					foreach($reserv_stk_res->result_array() as $reserv_stk_det)
					{
						$p_reserv_qty += $reserv_stk_det['qty'];
						$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and stock_id = ? limit 1";
						$this->db->query($sql,array($reserv_stk_det['qty'],$p['product_id'],$reserv_stk_det['stock_info_id']));
						
						$this->db->query("update t_reserved_batch_stock set release_qty=? ,released_on = ?,status = 2 where id = ? and p_invoice_no = ?  ",array($reserv_stk_det['qty'],time(),$reserv_stk_det['id'],$p_invoice));
						
						
					}
				}
				 
				$this->erpm->do_stock_log(1,$p_reserv_qty,$p['product_id'],$this->db->query("select id from proforma_invoices where order_id=?",$o['id'])->row()->id,false,true,true);
				
			}
		}
		
		 
		
		$proforma_inv_id = $this->db->query("select a.id as id from proforma_invoices a 
														join king_orders b on a.order_id = b.id 
														where a.p_invoice_no = ?  ",array($p_invoice))->row()->id;
		
		$stk_allot_det = array();
		foreach($pbcodes as $stk_prod_sel => $stk_mrp_det)
		{
			list($itemid,$pid,$pbcode) = explode('_',$stk_prod_sel);
			list($stk_cnt,$pmrp,$stk_id) = explode('_',$stk_mrp_det);

			$pbcode = (($pbcode == 'BLANK')?'':$pbcode);
			
			$order_id = $ord_item_ids[$itemid]; 
			
			$check_stock_rerv_res = $this->db->query("select id,qty,released_on,status,ifnull(released_on,0) as is_released,release_qty  from t_reserved_batch_stock where batch_id = ? and p_invoice_no = ? and order_id = ? and stock_info_id = ? ",array($batch_id,$p_invoice,$order_id,$stk_id));
			
			
			if(!$check_stock_rerv_res->num_rows())
			{
				if($this->db->query("select count(*) as t from t_reserved_batch_stock where p_invoice_no = ? and order_id = ? ",array($p_invoice,$order_id))->row()->t)
				{
					$sql="update t_stock_info set available_qty=available_qty-? where product_id=? and stock_id = ? limit 1";
					$this->db->query($sql,array($stk_cnt,$pid,$stk_id));
					
					$allot_stk = array();
					$allot_stk['stock_info_id'] = $stk_id;
					$allot_stk['product_id'] = $pid;
					$allot_stk['p_invoice_no'] = $p_invoice;
					$allot_stk['batch_id'] = $batch_id;
					$allot_stk['order_id'] = $order_id;
					$allot_stk['qty'] = $stk_cnt;
					$allot_stk['status'] = 1;
					$allot_stk['reserved_on'] = time();
					$this->db->insert("t_reserved_batch_stock",$allot_stk);
				}
				
			}else
			{
				
				$stock_rerv_det = $check_stock_rerv_res->row_array();
				
				if($stock_rerv_det['is_released'] == 0 && $stock_rerv_det['status'] != 2)
				{
				 
					if($stock_rerv_det['qty'] > $stk_cnt)
					{
						$release_qty = $stock_rerv_det['qty']-$stk_cnt;
						$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and stock_id = ? limit 1";
						$this->db->query($sql,array($release_qty,$pid,$stk_id));
						
						$this->db->query("update t_reserved_batch_stock set release_qty=?,released_on = ?,status = 1 where id = ? ",array($release_qty,time(),$stock_rerv_det['id']));
						
						$this->erpm->do_stock_log(0,$release_qty,$pid,$proforma_inv_id,false,true,true);
					}
					else if($stock_rerv_det['qty'] < $stk_cnt)
					{
						$reallot_qty = $stk_cnt-$stock_rerv_det['qty'];
						$sql="update t_stock_info set available_qty=available_qty-? where product_id=? and stock_id = ? limit 1";
						$this->db->query($sql,array($reallot_qty,$pid,$stk_id));
						
						$this->db->query("update t_reserved_batch_stock set release_qty=?,extra_qty=?,released_on = ?,status = 1 where id = ? ",array(0,$reallot_qty,time(),$stock_rerv_det['id']));
						
						$this->erpm->do_stock_log(1,$reallot_qty,$pid,$proforma_inv_id,false,true,true);
					}
					else
					{
						$this->db->query("update t_reserved_batch_stock set status = 1 where id = ? ",array($stock_rerv_det['id']));	
					}
				}		
			}
			
		}
		
		// check for pending unreleased qty from stock reservation table  
		if($batch_id)
		{
			$reserv_stk_res = $this->db->query('select id,stock_info_id,product_id,qty from t_reserved_batch_stock where batch_id = ? and p_invoice_no = ? and status = 0 and release_qty = 0 and released_on is null ',array($batch_id,$p_invoice));
			if($reserv_stk_res->num_rows())
			{
				foreach($reserv_stk_res->result_array() as $reserv_stk_det)
				{
					$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and stock_id = ? limit 1";
					$this->db->query($sql,array($reserv_stk_det['qty'],$reserv_stk_det['product_id'],$reserv_stk_det['stock_info_id']));
					$this->db->query("update t_reserved_batch_stock set release_qty=? ,released_on = ?,status=2 where id = ? ",array($reserv_stk_det['qty'],time(),$reserv_stk_det['id']));
				}
			}
		}
		
		
			// process refund for invoiced deals
			$refund_res = $this->db->query('select a.p_invoice_no,c.quantity,di.is_combo,di.is_pnh,c.itemid,d.menuid,c.transid,a.order_id,a.product_id,i_orgprice as ordmrp,i_orgprice-(i_price-i_coup_discount) as disc,b.mrp,(a.qty-ifnull(a.release_qty,0)+ifnull(a.extra_qty,0)) as allot_qty 
														from t_reserved_batch_stock a 
														join t_stock_info b on a.stock_info_id = b.stock_id 
														join king_orders c on c.id = a.order_id 
														join king_dealitems di on c.itemid = di.id 
														join king_deals d on d.dealid = di.dealid
														
													where batch_id = ? and a.p_invoice_no = ? and a.status = 1  
													order by a.order_id 
											',array($batch_id,$p_invoice));
			if($refund_res->num_rows())
			{
				
				$refund_arr = array();
				foreach($refund_res->result_array() as $rd)
				{
					// Check if valid for refund by menu  
					$consider_for_refund = 0;
					if($rd['is_pnh'])
					{
						$consider_for_refund = $this->db->query("select consider_mrp_chng from pnh_menu where id = ? ",$rd['menuid'])->row()->consider_mrp_chng;
					}
					
					if($consider_for_refund==0)
						continue;
					
					$transid = $rd['transid'];
					$oid = $rd['order_id'];
					$ordmrp = $rd['ordmrp'];
					$disc = $rd['disc'];
					$qty = $rd['quantity'];
					
					
					if(!isset($refund_arr[$transid]))
						$refund_arr[$transid] = array('orders'=>array(),'total_refund'=>0);
	
					if(!isset($refund_arr[$transid]['orders'][$oid]))
					{
						$refund_arr[$transid]['orders'][$oid] = array();
						$refund_arr[$transid]['orders'][$oid]['ordmrp'] = $ordmrp;
						$refund_arr[$transid]['orders'][$oid]['disc'] = $disc;
						$refund_arr[$transid]['orders'][$oid]['qty'] = $qty;
						$refund_arr[$transid]['orders'][$oid]['prod_mrp'] = 0;
						$refund_arr[$transid]['orders'][$oid]['prod_mrp_sum'] = 0;
						$refund_arr[$transid]['orders'][$oid]['new_disc'] = 0;
					}
					$refund_arr[$transid]['orders'][$oid]['prod_mrp_sum'] += $rd['mrp'];
					$refund_arr[$transid]['orders'][$oid]['prod_mrp'] += $rd['mrp']*$rd['allot_qty'];
					
				}
				
				 
				foreach($refund_arr as $tid=>$r_ord_list)
				{
					$ttl_refund = 0;  
					foreach($r_ord_list['orders'] as $oid=>$r_ord_det)
					{
						$t_ord_mrp = $r_ord_det['ordmrp']*$r_ord_det['qty'];
						$t_ord_disc = $r_ord_det['disc']*$r_ord_det['qty'];
						$t_prod_mrp = $r_ord_det['prod_mrp'];
						$t_amt_after_disc = round($t_ord_mrp-$t_ord_disc,2);
						
						$new_disc_amt = round($t_amt_after_disc*$t_prod_mrp/$t_ord_mrp,2);
						$rfd_amt = $t_amt_after_disc - $new_disc_amt;
						$refund_arr[$tid]['orders'][$oid]['new_disc'] = $new_disc_amt; 
						$ttl_refund+=$rfd_amt;
					}
					$refund_arr[$tid]['total_refund'] = $ttl_refund; 
				}
				
				foreach($refund_arr as $tid=>$r_ord_list)
				{
					if($r_ord_list['total_refund']*1 > 0)
					{
						$total_refund_amout = $r_ord_list['total_refund'];
						$this->db->query("insert into t_refund_info(transid,amount,invoice_no,refund_for,status,created_on,created_by) values(?,?,?,?,?,?,?)",array($tid,$total_refund_amout,$invoice_no,'mrpdiff',0,time(),$lgn_user['userid']));
						$rid=$this->db->insert_id();
						
						foreach($r_ord_list['orders'] as $oid=>$r_ord_det)
						{
							$this->db->query("insert into t_refund_order_item_link(refund_id,order_id,invoice_no,qty) values(?,?,?,?)",array($rid,$oid,$invoice_no,$r_ord_det['qty']));
							
							$this->db->query("update king_invoice set mrp = ?,discount=?,nlc=? where invoice_no = ? and order_id = ? ",array($r_ord_det['prod_mrp']/$r_ord_det['qty'],($r_ord_det['prod_mrp']-$r_ord_det['new_disc'])/$r_ord_det['qty'],$r_ord_det['new_disc']/$r_ord_det['qty'],$invoice_no,$oid));
						}
								
						if($tid && $total_refund_amout)
							$this->erpm->do_trans_changelog($tid," Rs ".$total_refund_amout." refund process initiated towards MRP Change in Invoice No:".$invoice_no);
					}
				}
				
			}
		 
			
			
			
		
		/*
		// stock update by selected barcodes 
		foreach($pbcodes as $stk_prod_sel => $stk_mrp_det)
		{
			list($itemid,$pid,$pbcode) = explode('_',$stk_prod_sel);
			list($stk_cnt,$pmrp,$stk_id) = explode('_',$stk_mrp_det);
			
			$pbcode = (($pbcode == 'BLANK')?'':$pbcode);
			//1 - 2 - 1 - 1
			// get resered qty for product from stock and revert back to stock master
			$p_reserv_qty = 0;
			$reserv_stk_res = $this->db->query('select id,stock_info_id,qty from t_reserved_batch_stock where batch_id = ? and order_id = ? and product_id = ? and released_on is null ',array($batch_id,$itemid,$pid));
			if($reserv_stk_res->num_rows())
			{
				foreach($reserv_stk_res->result_array() as $reserv_stk_det)
				{
					if(($stk_id == $reserv_stk_det['stock_info_id']))
					{
						if(($stk_cnt < $reserv_stk_det['qty']))
						{
							$release_qty = $reserv_stk_det['qty']-$stk_cnt;		
						}else
						{
							$release_qty = $stk_cnt-$reserv_stk_det['qty'];
						}
					}
						
					
					// some qty need to be reverted back 
					if($release_qty)
					{
						$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and batch_id = ? and item_id = ? and stock_id = ? limit 1";
						$this->db->query($sql,array($release_qty,$pid,$batch_id,$itemid,$reserv_stk_det['stock_info_id']));
					}
					$this->db->query("update t_reserved_batch_stock set release_qty=? and released_on = ? where id = ? ",array($release_qty,time(),$reserv_stk_det['id']));
				}
			}
			*/
			
			
			/*
			$proforma_inv_id = $this->db->query("select a.id as id from proforma_invoices a 
														join king_orders b on a.order_id = b.id 
														where b.itemid = ? and a.p_invoice_no = ?  ",array($itemid,$p_invoice))->row()->id;
			$this->erpm->do_stock_log(0,$stk_cnt,$pid,$proforma_inv_id,false,true,true);
			
			
		}
		*/
		 
		
		 
		 
		
				
		$this->db->query("update king_orders set status=0 where id in ('".implode("','",$c_oids)."')");
		
		
		

		
		$user=$this->erpm->getadminuser();
		$bid=$this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no=?",$p_invoice)->row()->batch_id;
		$this->db->query("update shipment_batch_process_invoice_link set invoice_no=$invoice_no,packed=1,packed_on=now(),packed_by={$user['userid']} where p_invoice_no=?",$p_invoice);
		if($this->db->query("select count(1) as l from shipment_batch_process_invoice_link where batch_id=?",$bid)->row()->l<=$this->db->query("select count(1) as l from shipment_batch_process_invoice_link where packed=1 and batch_id=$bid")->row()->l+$this->db->query("select count(1) as l from shipment_batch_process_invoice_link bi join proforma_invoices i on i.p_invoice_no=bi.p_invoice_no where bi.batch_id=$bid and bi.packed=0 and i.invoice_status=0")->row()->l)
//		if($this->db->query("select count(1) as l from shipment_batch_process_invoice_link bi join proforma_invoices i on i.p_invoice_no=bi.p_invoice_no where bi.batch_id=? and bi.packed=0",$bid)->row()->l-$this->db->query("select count(1) as l from shipment_batch_process_invoice_link bi join proforma_invoices i on i.p_invoice_no=bi.p_invoice_no where bi.batch_id=? and i.invoice_status=1",$bid)->row()->l<=0)
			$this->db->query("update shipment_batch_process set status=2 where batch_id=? limit 1",$bid);
		else
			$this->db->query("update shipment_batch_process set status=1 where batch_id=? limit 1",$bid);
		$this->session->set_flashdata("erp_pop_info","Packed status updated");
		redirect("admin/invoice/$invoice_no");
		
		
		
	}
	
	function do_addtransmsg($transid)
	{
		$admin=$this->erpm->getadminuser();
		$msg=$this->input->post("msg");
		$usernote=$this->input->post("usernote");
		if($usernote)
		{
			$m=$this->db->query("select * from king_transaction_notes where transid=? and note_priority=1",$transid)->row_array();
			if(!empty($m))
				$this->db->query("update king_transaction_notes set note=? where transid=? and note_priority=1",array($m['note'].", update :".$msg,$transid));
			else 
				$this->db->query("insert into king_transaction_notes(transid,note,note_priority) values(?,?,1)",array($transid,"User Note: ".$msg));
			$msg="User note appended : $msg";
		}
		$this->db->query("insert into transactions_changelog(transid,msg,admin,time) values(?,?,?,?)",array($transid,$msg,$admin['userid'],time()));
		$this->session->set_flashdata("erp_pop_info","Message added");
		redirect("admin/trans/$transid");
	}
	
	function getpartnerorderlogs()
	{
		return $this->db->query("select l.*,p.name,u.name as created_by from partner_orders_log l join partner_info p on p.id=l.partner_id left outer join king_admin u on u.id=l.created_by order by l.id desc")->result_array();
	}
	
	function do_generate_kfile($invs)
	{
		
		$sql="select    ifnull(c.courier_name,'') as courier, ifnull(il.shipped_on,'') as shipdate,ifnull(il.awb,'') as awb_no,
						t.amount as tran_amount,i.invoice_no,t.mode,
						(o.i_price-o.i_coup_discount)*o.quantity as amount,di.name,o.transid,
						o.ship_person,concat(o.ship_address,o.ship_landmark) as ship_address,
						o.ship_city,o.ship_state,o.ship_pincode,o.ship_phone,o.quantity,
						t.partner_id,
						ifnull(p.name,'') as partner_name,
						t.partner_reference_no
					from king_invoice i 
					join king_orders o on o.id=i.order_id 
					join king_dealitems di on di.id=o.itemid 
					join king_transactions t on t.transid=o.transid 
					left join shipment_batch_process_invoice_link il on il.invoice_no = i.invoice_no 
					left join m_courier_info c on c.courier_id = il.courier_id
					left join partner_info p on p.id = t.partner_id   
					where i.invoice_no in ('".implode("','",$invs)."')";
		$ret=array();
		foreach($this->db->query($sql)->result_array() as $r)
		{
			if(!isset($ret[$r['invoice_no']]))
				$ret[$r['invoice_no']]=array();
			$ret[$r['invoice_no']][]=$r;
		}
		$data=$ret;
		ob_start();
		$f=fopen("php://output","w");
		fputcsv($f, array("Invoice No","AWB No","Courier/Medium","Ship Date","Notify Customer","Transaction Reference","Ship Person","Shipping Address","Shipping City","Shipping Pincode","Shipping State","Contact Number","Amount","Mode","Quantity","Product Name","Weight","PartnerName","PartnerRefno","UserNotes"));
		foreach($data as $inv=>$orders)
		{
			$transid=$orders[0]['transid'];
			$csv=array($inv,$orders[0]['awb_no'],($orders[0]['courier']?$orders[0]['courier']:''),(($orders[0]['shipdate']!="")?date('Y-m-d',strtotime($orders[0]['shipdate'])):''),"",$orders[0]['transid'],$orders[0]['ship_person'],$orders[0]['ship_address'],$orders[0]['ship_city'],$orders[0]['ship_pincode'],$orders[0]['ship_state'],$orders[0]['ship_phone']);
			$amount=0;$prods=array();
			foreach($orders as $o)
			{
				$prods[]=$o['name'].":{$o['quantity']}";
				$amount+=$o['amount'];
			}
			$n=count($orders);
			if($n==$this->db->query("select count(1) as l from king_orders where transid=?",$transid)->row()->l)
				$amount=$orders[0]['tran_amount'];
			$csv[]=$amount;
			if($orders[0]['mode']==1)
				$csv[]="COD";
			else
				$csv[]="Prepaid";
			$csv[]=$n;
			$csv[]=implode(", ",$prods);
			$csv[]="";
			$csv[]=$orders[0]['partner_name'];
			$csv[]=$orders[0]['partner_reference_no'];
			if($orders[0]['partner_id'])
			{
				$tnotes_res = $this->db->query("select note from king_transaction_notes where transid = ? ",$transid);
				if($tnotes_res->num_rows())
					$csv[]=$tnotes_res->row()->note;
				else
					$csv[]="";
			}else
			{
				$csv[]="";
			}
			
			fputcsv($f,$csv);
		}
		fclose($f);
		$csv=ob_get_clean();
		@ob_clean();
	    header('Content-Description: File Transfer');
	    header('Content-Type: text/csv');
	    header('Content-Disposition: attachment; filename='.("kfile_".date("d_m_y").".csv"));
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . strlen($csv));
	    @ob_clean();
	    flush();
	    echo $csv;
	    exit;
	}
	
	function do_generate_kfilebyrange($from,$to)
	{
		$invs=explode(",",$this->input->post("ids"));
		if(empty($invs))
			show_error("No Invoices provided");
			
			
		$invs = array();	
		foreach($this->db->query("select * from shipment_batch_process_invoice_link where shipped=1 and  date(shipped_on)>=? and date(shipped_on)<=? ",array($from,$to))->result_array() as $r)
			array_push($invs,$r['invoice_no']);

		$this->do_generate_kfile($invs);		
			 
	}
	
	function getoutscansforkfile()
	{
		$l=5;
		for($i=0;$i<$l;$i++)
		{
			$date=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$i));
			$ret[$date]=array();
			foreach($this->db->query("select * from shipment_batch_process_invoice_link where shipped=1 and date(shipped_on)=?",$date)->result_array() as $r)
				$ret[$date][]=$r['invoice_no'];
		}
		return $ret;
	}
	
	
	function do_new_order($pl,$mode=1,$prefix="SNC",$partner_id=0,$log_id=0)
	{
		$cons_ship = $this->input->post('cons_ship');
		$iid=explode(":",$pl['itemid']);
		$pl['itemid']=$iid[0];
		if(count($iid)>1)
			$pl['itemid']=$iid[1];
		$cod=0;
		
		$notify=true;
		if(isset($pl['notify']) && $pl['notify']==0)
			$notify=false;
			
		if(!isset($pl['reference']))
			$pl['reference']="";
			
		$p_email=$pl['email'];
		
		$user=false;
		if(!$user)
		{
			$user=$this->vkm->getuserbyemail($p_email);
			if(!$user)
			{
				$password=randomChars(6);
				$this->vkm->newuser($p_email,$pl['bill_person'],$password,$pl['bill_phone'],rand(2030,424321),0,"",0,$notify);
				$user=$this->vkm->getuserbyemail($p_email);
			}
		}
		$uid=0;
		if(!empty($user))
			$uid=$user['userid'];

		$total=$amount=$price=$this->db->query("select price from king_dealitems where id=?",$pl['itemid'])->row()->price*$pl['qty'];
		
		$ship=$cons_ship?$pl['ship_charges']:0;
		
		$gc_total=0;

		$amount+=$ship;
		
		$snp=$prefix;
			
		$transid=$snp.random_string("alpha",3).random_string("nozero",5);
		$transid=strtoupper($transid);
		
		$sql="insert into king_transactions(transid,amount,init,mode,status,cod,ship,partner_reference_no,partner_id) values(?,?,?,?,0,?,?,?,?)";
		$this->db->query($sql,array($transid,$amount,time(),$mode,$cod,$ship,$pl['reference'],$partner_id));
		
		$itemid=$pl['itemid'];
		$item_det=$this->db->query("select d.brandid,d.vendorid,i.orgprice,i.price,i.nlc,i.phc,i.tax,d.is_giftcard from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$pl['itemid'])->row();

		$brandid = $item_det->brandid;
		$vendorid = $item_det->vendorid;
		$is_giftcard = $item_det->is_giftcard;
		
		$buyer_options=array();
		
		$orderid=random_string("numeric",10);

		$sql="insert into king_tmp_orders(id,userid,itemid,brandid,vendorid,bpid,bill_person,bill_address,bill_landmark,bill_city,bill_state,bill_pincode,bill_phone,bill_telephone,bill_email,bill_country,ship_person,ship_address,ship_landmark,ship_city,ship_state,ship_pincode,ship_phone,ship_telephone,ship_email,ship_country,quantity,amount,time,buyer_options,transid,i_orgprice,i_price,i_nlc,i_phc,i_tax,i_discount,i_coup_discount,i_discount_applied_on,giftwrap_order,user_note,is_giftcard,gc_recp_name,gc_recp_email,gc_recp_mobile,gc_recp_msg)" .
																									" values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$inp=array($orderid,$uid,$itemid,$brandid,$vendorid,0);
		$params=array("bill_person","bill_address","bill_landmark","bill_city","bill_state","bill_pincode","bill_phone","bill_telephone","email","bill_country","ship_person","ship_address","ship_landmark","ship_city","ship_state","ship_pincode","ship_phone","ship_telephone","email","ship_country");
		foreach($params as $p)
			$inp[]=$pl[$p];
			
		$log_inp=array("transid"=>'',"log_id"=>$log_id,"i_customer_price"=>$item_det->price,"i_partner_price"=>$item_det->price,'qty'=>$pl['qty']);

		$this->db->query("update king_users set address=?,landmark=?,city=?,state=?,pincode=?,country=? where userid=? limit 1",array($pl['ship_address'],$pl['ship_landmark'],$pl['ship_city'],$pl['ship_state'],$pl['ship_pincode'],$pl['ship_country'],$user['userid']));	
		if($partner_id!=0)
		{
			$p_price=$this->db->query("select customer_price,partner_price from partner_deal_prices where itemid=? and partner_id=?",array($itemid,$partner_id))->row_array();
			if(!empty($p_price))
			{
				$log_inp['i_customer_price']=$item_det->price=$p_price['customer_price'];
				$log_inp['i_partner_price']=$p_price['partner_price'];
			}
		}
			
		$inp[]=$pl['qty'];
		$inp[]=$price;
		$inp[]=time();
		$inp[]=serialize($buyer_options);
		$inp[]=$transid;
		
		$inp[]=$item_det->orgprice;
		$inp[]=$item_det->price;
		$inp[]=$item_det->nlc;
		$inp[]=$item_det->phc;
		$inp[]=$item_det->tax;
		$inp[]=($item_det->orgprice-$item_det->price);
		if(isset($coupon_price_dets[$itemid]['coup_discount']))
			$inp[] = $coupon_price_dets[$itemid]['coup_discount'];
		else
			$inp[] = 0;
		 
		if(isset($coupon_price_dets[$itemid]['discount_applied_amount']))
			$inp[] = $coupon_price_dets[$itemid]['discount_applied_amount'];
		else
			$inp[] = 0;
		
			$inp[] = ($this->input->post('is_giftwrap')?1:0);
			$inp[] = $pl['notes'];
			
		$inp[] = 0;
		$inp[] = '';
		$inp[] = '';
		$inp[] = '';
		$inp[] = '';
			
		$this->db->query($sql,$inp);

		$invno = ''; // not required while new order  	
		
		$user_note = '';
		
		$giftwrap_order = 0;
		
		$sql="select * from king_tmp_orders where transid=?";
		
		$orders=$this->db->query($sql,array($transid))->result_array();
		foreach($orders as $cord=>$t_order)
		{
			$params=array("bpid","id","transid","userid","itemid","brandid","vendorid","bill_person","bill_address","bill_landmark","bill_city","bill_state","bill_phone","bill_telephone","bill_pincode","bill_country","ship_person","ship_address","ship_landmark","ship_city","ship_state","ship_pincode","ship_phone","ship_telephone","ship_country","quantity","bill_email","ship_email","buyer_options");
			$sql="insert into king_orders(".implode(",",$params).",paid,mode,status,invoice_no,time,i_orgprice,i_price,i_tax,i_nlc,i_phc,i_discount,i_coup_discount,i_discount_applied_on,giftwrap_order,is_giftcard,gc_recp_name,gc_recp_email,gc_recp_mobile,gc_recp_msg) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$inp=array();
			foreach($params as $p)
				$inp[]=$t_order[$p];
			$inp[]=$amount;
			$inp[]=$mode;$inp[]=0;$inp[]=$invno++;$inp[]=time();
			
			$inp[]=$t_order['i_orgprice'];
			$inp[]=$t_order['i_price'];
			$inp[]=$t_order['i_tax'];
			$inp[]=$t_order['i_nlc'];
			$inp[]=$t_order['i_phc'];
			$inp[]=$t_order['i_discount'];
			$inp[]=$t_order['i_coup_discount'];
			$inp[]=$t_order['i_discount_applied_on'];
			$inp[]=$t_order['giftwrap_order'];
			$inp[]=$t_order['is_giftcard'];
			
			if($t_order['is_giftcard']){
				$inp[]=$t_order['gc_recp_name'];
				$inp[]=$t_order['gc_recp_email'];
				$inp[]=$t_order['gc_recp_mobile'];
				$inp[]=$t_order['gc_recp_msg'];
			}else{
				$inp[]='';
				$inp[]='';
				$inp[]='';
				$inp[]='';
			}
			
			$giftwrap_order = $t_order['giftwrap_order'];
			
			$user_note = $t_order['user_note'];
			
			$emails[0]=$t_order['bill_email'];
			$emails[1]=$t_order['ship_email'];
			
			$this->db->query($sql,$inp);
			$this->db->query("delete from king_tmp_orders where transid=? limit 1",$transid);
			$sql="update king_dealitems set available=available+{$t_order['quantity']} where id=?";
			$this->db->query($sql,$t_order['itemid']);
			$this->db->query("update king_m_buyprocess set quantity_done=quantity_done+? where id=? limit 1",array($t_order['quantity'],$t_order['bpid']));
			$this->db->query("update king_buyprocess set quantity=?,status=1 where bpid=? and userid=? limit 1",array($t_order['quantity'],$t_order['bpid'],$t_order['userid']));
			
			$qty=$t_order['quantity'];
			$this->db->query("update king_profiles set products=products+?,lastbuy=?, lastbuy_on=? where userid=?",array($t_order['quantity'],$t_order['itemid'],time(),$user['userid']));
			$this->db->query("update king_dealitems set buys=buys+?,snapits=snapits+? where id=?",array($qty,$qty,$t_order['itemid']));
			
		}
		
		if($partner_id)
		{
			$ins_data = array();
			$ins_data['partner_id'] = $partner_id;
			$ins_data['transid'] = $transid;
			$ins_data['order_no'] = $pl['reference'];
			$ins_data['ship_charges'] = $pl['ship_charges'];
			$ins_data['order_date'] = date('y-m-d',strtotime($pl['order_date']));
			$ins_data['awb_no'] = $pl['awb_no'];
			$ins_data['net_amt'] = $pl['net_amt'];
			$ins_data['courier_name'] = $pl['courier_name'];
			$ins_data['created_on'] = date('Y-m-d H:i:s');
			$ins_data['created_by'] = $uid;
			$this->db->insert('partner_transaction_details',$ins_data);
		}
		
		// add  user note to transaction notes 
		$ins_un = array();
		$ins_un['transid'] = $transid;
		$ins_un['note'] = 'User Note:'.$user_note;
		$ins_un['status'] = 1;
		$ins_un['note_priority'] = 1;
		$ins_un['created_on'] = date('Y-m-d H:i:s');
		$this->db->insert('king_transaction_notes',$ins_un);
		
		
		$t_tot=0;
		
		$ormsg='<table border=1><tr><th>Product</th><th>Quantity</th><th>Ship to</th></tr>';
		$umail='<table width="100%" border=1 cellspacing=0 cellpadding=7 style="font-size:inherit;margin-top:15px;"><tr><th>Product</th><th>Unit Price</th><th>Quantity</th><th>Price</th></tr>';
		$mrp_total=0;
		$m_items=array();
		$total_discount = 0;
		 
		foreach($orders as $order)
		{
			$m_item=$this->db->query("select orgprice,price,name from king_dealitems where id=?",$order['itemid'])->row_array();
			$itemname=$m_item['name'];
			$ormsg.="<tr><td>{$itemname}</td><td>{$order['quantity']}</td><td>{$order['ship_person']}<br>{$order['ship_address']}<br>{$order['ship_city']}</td></tr>";
			$t=$m_item['price']*$order['quantity'];
			$m=$m_item['orgprice']*$order['quantity'];
			$total_discount+=($order['i_coup_discount']+$order['i_discount'])*$order['quantity'];
			$umail.="<tr><td>{$itemname}</td><td>{$m_item['orgprice']}</td><td>{$order['quantity']}</td><td>{$m}</td></tr>";
			$t_tot+=$t;
			$mrp_total+=$m;
			$m_items[]=$m_item;
		}
		
		
		$log_inp['transid']=$transid;
		
		
		
		 
		
		
		
		
		if($partner_id)
		{
			$this->db->insert("partner_order_items",$log_inp);
			
			$ttl_payable_amt = $this->db->query("select sum((i_orgprice-(i_coup_discount+i_discount))*quantity ) as ttl from king_orders where transid = ? ",$log_inp['transid'])->row()->ttl;
			$ttl_payable_amt += $this->db->query("select cod+ship+giftwrap_charge as ttl from king_transactions where transid = ? ",$log_inp['transid'])->row()->ttl;
			
			$this->db->query("update king_transactions set amount = ? where partner_id = ? and transid = ? ",array($ttl_payable_amt,$partner_id,$log_inp['transid']));
		}
		
		
		$m_trans=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
		$t_amount=$this->db->query("select amount from king_transactions where transid=?",$transid)->row()->amount;
		$ormsg.='</table><div>Refer admin panel for more & correct info</div>';
		$umail.="<tr><td colspan=3 align='right'>Discount</td><td>".floor($total_discount)."</td></tr>";
		if($giftwrap_order)
			$umail.="<tr><td colspan=3 align='right'>GiftWrap Charges</td><td>".GIFTWRAP_CHARGES."</td></tr>";
		$umail.="<tr><td colspan=3 align='right'>Handling/Shipping/COD charges</td><td>".($m_trans['cod']+$m_trans['ship'])."</td></tr>";
		$umail.="<tr><td colspan=3 align='right'>Total</td><td>Rs {$t_amount}</td></tr>";
		$umail.="</table>";
				
		if($notify)
			$this->vkm->sendemail($transid,$emails,$umail);
		
		
		$this->vkm->do_cashbacks($transid);
		
		
		return $transid;
	}
	
	function pnh_getdealpricechanges($v,$iids=array())
	{
		return $this->erpm->pnh_getdealpricechange($v,$iids);
	}
	
	function pnh_getdealpricechange($v,$iids=array())
	{
		$ver=$this->db->query("select * from pnh_app_versions where id=?",$v)->row_array();
		$sql="SELECT * FROM (select i.name,i.pnh_id,pc.* from `deal_price_changelog` pc join king_dealitems i on i.id=pc.itemid and i.is_pnh=1 where pc.created_on>=? ";
		if(!empty($iids))
			$sql.=" and pc.itemid in ('".implode("','",$iids)."')";
		$sql.=" order by pc.id desc) as temp group by itemid order by id desc";
		$rdeals=$this->db->query($sql,$v['version_date'])->result_array();
		$ids=array();
		foreach($rdeals as $d)
			$ids[]=$d['itemid'];
		if(!empty($ids))
		{
			$old_pc_data=$this->db->query("select old_mrp,itemid,old_price from deal_price_changelog where created_on>? and itemid in ('".implode("','",$ids)."') order by id asc",$v['version_date'])->result_array();
			$done=array();
			foreach($old_pc_data as $pc)
			{
				if(in_array($pc['itemid'],$done))
					continue;
				foreach($rdeals as $i=>$deal)
				{
					if($deal['itemid']==$pc['itemid'])
					{
						$rdeals[$i]['old_price']=$pc['old_price'];
						$rdeals[$i]['old_mrp']=$pc['old_mrp'];
					}
				}
				$done[]=$pc['itemid'];
			}
		}
		$deals=array();
		foreach($rdeals as $d)
		{
			$d['changed_on']=date("g:ia d/m/y",$d['created_on']);
			foreach(array("itemid","id","created_by","reference","created_on","itemid") as $r)
				unset($d[$r]);
			$deals[]=$d;
		}
		return $deals;
	}
	
	function do_partner_order_import()
	{
		$user=$this->erpm->getadminuser();
		$pid=$this->input->post("partner");
		$cons_ship = $this->input->post("cons_ship");
		if(empty($pid))
			show_error("Input kissing<br>Invalid partner id");
		$partner=$this->db->query("select * from partner_info where id=?",$pid)->row_array();
		$partner_id=$partner['id'];
		$mode=$partner['trans_mode'];
		$prefix=$partner['trans_prefix'];
		
		$f=fopen($_FILES['pords']['tmp_name'],"r");
		$head=fgetcsv($f);
		$head[]="Transid";$head[]="Msg";
		$out=array($head);
		$c=0;
		$l_inp=array("partner_id"=>$pid,"created_by"=>$user['userid'],"created_on"=>time());
		$this->db->insert("partner_orders_log",$l_inp);
		$log_id=$this->db->insert_id();
		while(($data=fgetcsv($f))!==false)
		{
			$flag=true;
			$payload=array("email"=>$data[0],"notify"=>$data[1],"notes"=>$data[2],"itemid"=>$data[3],"qty"=>$data[5],"ship_charges"=>$data[6],"bill_person"=>$data[7],"bill_address"=>$data[8].$data[9],"bill_landmark"=>"","bill_city"=>$data[10],"bill_state"=>$data[11],"bill_country"=>$data[12],"bill_pincode"=>$data[13],"bill_phone"=>$data[16],"bill_telephone"=>$data[14],"ship_person"=>$data[17],"ship_address"=>$data[18].$data[19],"ship_landmark"=>"","ship_city"=>$data[20],"ship_state"=>$data[21],"ship_country"=>$data[22],"ship_pincode"=>$data[23],"ship_telephone"=>$data[24],"ship_phone"=>$data[26],"reference"=>$data[27],"order_date"=>$data[29],"courier_name"=>$data[30],"awb_no"=>$data[31],"net_amt"=>$data[32]);
			if(count($data)!=33)
			{
				$data['transid']="";
				$data['msg']="Invalid template structure";
				$flag=false;
			}
			$data['transid']="";
			$item=explode(":",$payload["itemid"]);
			if(count($item)==1)
				$itemid=$item[0];
			else $itemid=$item[1];
			if($flag && $this->db->query("select 1 from king_dealitems where id=?",$itemid)->num_rows()==0)
			{
				$flag=false;
				$data['msg']="Itemid : $itemid is not valid";
			}
			$p_mode=0;
			if($flag && strtolower($data[28])!="cod" && strtolower($data[28])!="pg")
			{
				$flag=false;
				$data['msg']="Invalid transaction mode";
			}
			if(strtolower($data[28])=="cod")
				$p_mode=1;
				
			$is_dup = $this->db->query("select  count(*) as t from king_transactions where partner_reference_no = ? and partner_id = ? ",array($payload['reference'],$partner_id))->row()->t;
			if($is_dup)
			{
				$flag=false;
				$data['msg']="Order Already Imported";
			}	
			if($flag)
			{
				$data["transid"]=$this->erpm->do_new_order($payload,$p_mode,$prefix,$partner_id,$log_id);
				$data['msg']="Transaction successful";
			}
			$out[]=$data;
			$c++;
		}
		$amount=$this->db->query("select sum(i_partner_price*qty) as s from partner_order_items where log_id=$log_id")->row()->s;
		$this->db->query("update partner_orders_log set amount=? where id=? limit 1",array($amount,$log_id));
		$this->session->set_flashdata("erp_pop_info","$c Partner orders processed");
		fclose($f);
		@ob_start();
		$f=fopen("php://output","w");
		foreach($out as $o)
			fputcsv($f, $o);
		fclose($f);
		$csv=@ob_get_clean();
		@ob_clean();
	    header('Content-Description: File Transfer');
	    header('Content-Type: text/csv');
	    header('Content-Disposition: attachment; filename='.("partner_orders_".date("d_m_y").".csv"));
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . strlen($csv));
	    @ob_clean();
	    flush();
	    echo $csv;
	    exit;
	}
	
	function do_update_ship_kfile()
	{
		$f=fopen($_FILES['kfile']['tmp_name'],"r");
		$head=fgetcsv($f);
		$count=0;
		while(($data=fgetcsv($f))!==false)
		{
			$invoice_no=$data[0];
			if(empty($invoice_no))
				continue;
			$awb=$data[1];
			$courierid=$data[2];
			$c=$this->db->query("select courier_name as name from m_courier_info where courier_id=?",$courierid)->row_array();
			$courier="Others";
			if(!empty($c))
				$courier=$c['name'];
			$date=$data[3];
			$notify=$data[4];
			foreach($this->db->query("select order_id from king_invoice where invoice_no=?",$invoice_no)->result_array() as $o)
				$this->db->query("update king_orders set medium=?,shipid=?,status=2,actiontime=".time()." where id=? limit 1",array($courier,$awb,$o['order_id']));
			if($this->db->query("select courier_id from shipment_batch_process_invoice_link where invoice_no=?",$invoice_no)->row()->courier_id!=0)
			{
				$b=$this->db->query("select o.medium,o.shipid from king_invoice inv join king_orders o on o.id=inv.order_id where inv.invoice_no=?",$invoice_no)->row_array();
				$this->erpm->do_trans_changelog($this->db->query("select transid from king_invoice where invoice_no=?",$invoice_no)->row()->transid,"Invoice no {$invoice_no} is reshipped.<br>Old courier details are<br>Medium:{$b['medium']}<br>Awb:{$b['shipid']}");
			}
			$this->db->query("update shipment_batch_process_invoice_link set courier_id=?,shipped=?,awb=? where invoice_no=?",array($courierid,1,$awb,$invoice_no));
			if($notify==1)
			{
				$mdata['prods']=$os=$this->db->query("select o.id as order_id,inv.transid,i.name,o.quantity,o.medium,o.shipid from king_invoice inv join king_orders o on o.id=inv.order_id join king_dealitems i on i.id=o.itemid where inv.invoice_no=? order by i.name asc",$invoice_no)->result_array();
				if(empty($os))
					continue;
				$mdata['transid']=$transid=$os[0]['transid'];
				$mdata['medium']=$os[0]['medium'];
				$mdata['trackingid']=$os[0]['shipid'];
				$partial=false;
				if($this->db->query("select count(1) as l from king_orders where status!=2 and transid=?",$transid)->row()->l!=0)
					$partial=true;
				$mdata['partial']=$partial;
				$payload=$this->db->query("select u.name,o.bill_email,o.ship_email from king_orders o join king_users u on u.userid=o.userid where o.id=?",$os[0]['order_id'])->row_array();
				$mdata['name']=$payload['name'];
				$msg=$this->load->view("mails/shipment",$mdata,true);
				$this->vkm->email(array($payload['bill_email'],$payload['ship_email']),"Your order is shipped",$msg,true);
			}
			$count++;
		}
		$this->session->set_flashdata("erp_pop_info","$count invoices updated");
		redirect("admin/update_ship_kfile");
	}
	
	function do_outscan($awb,$partner_id="")
	{
		$courier_names = '';
		if($partner_id == 5 )
		{
			
			$courier_names = explode(',',$this->input->post('inp_courier_name'));
			
			if(!count($courier_names))
			{
				die("Please choose atlease one courier");
			}
			
			$bpinv = $this->db->query("select d.*,c.partner_reference_no,e.is_manifesto_created,e.courier_name 
							from king_invoice a 
							join king_orders b on a.order_id = b.id 
							join king_transactions c on b.transid = c.transid 
							left join shipment_batch_process_invoice_link d on d.invoice_no = a.invoice_no
							join partner_transaction_details e on e.transid = c.transid and e.order_no = c.partner_reference_no    
							where c.partner_id = ?  and ( partner_reference_no = ? or (d.invoice_no = ? and d.invoice_no != 0) )         
							limit 1;",array($partner_id,$awb,$awb))->row_array();
			
		}else
		{
			$bpinv=$this->db->query("select * from shipment_batch_process_invoice_link where awb=? or (invoice_no=? and invoice_no!=0)",array($awb,$awb))->row_array();
		}
		if(empty($bpinv))
			die("<div class='not_found' style='background:#cd0000;'>AWB/Invoice/Order No:{$awb} not found</div>");
		if($bpinv['shipped'])
			die("<div class='already_shipped' style='background:orange;'>Invoice/Order No:{$awb} already shipped</div>");
		if($this->db->query("select invoice_status as s from king_invoice where invoice_no=?",$bpinv['invoice_no'])->row()->s=="0")
			die("<div class='cancelled' style='background:green;'>Invoice/Order No:{$awb} is cancelled</div>");
		$user=$this->erpm->getadminuser();
		
		
		if($bpinv && $partner_id)
		{
			if($partner_id==5)
			{
				if(!$bpinv['is_manifesto_created'])
					die("<div class='no_manifesto' style='background:#cd0000;'>Order No:{$bpinv['partner_reference_no']} is not in manifesto</div>");					
					
				if(!in_array($bpinv['courier_name'],$courier_names))
					die("<div class='courier_mismatch' style='background:purple;'>Order No:{$bpinv['partner_reference_no']} is not in <b>".implode(',',$courier_names)."</b> </div>");					
				
			}
		}
				 
					
			
					
		$this->db->query("update shipment_batch_process_invoice_link set shipped_by=?,shipped_on=?,shipped=1 where invoice_no=? limit 1",array($user['userid'],date('Y-m-d H:i:s'),$bpinv['invoice_no']));
/*		foreach($this->db->query("select order_id from king_invoice where invoice_no=?",$bpinv['invoice_no'])->result_array() as $o)
			$this->db->query("update king_orders set status=2,actiontime=".time()." where id=? limit 1",array($o['order_id']));
		$mdata['prods']=$os=$this->db->query("select inv.transid,inv.order_id,i.name,o.quantity,o.medium,o.shipid from king_invoice inv join king_orders o on o.id=inv.order_id join king_dealitems i on i.id=o.itemid where inv.invoice_no=? order by i.name asc",$bpinv['invoice_no'])->result_array();
		$mdata['transid']=$transid=$os[0]['transid'];
		$mdata['medium']=$os[0]['medium'];
		$mdata['trackingid']=$os[0]['shipid'];
		$partial=false;
		if($this->db->query("select count(1) as l from king_orders where status!=2 and transid=?",$transid)->row()->l!=0)
			$partial=true;
		$mdata['partial']=$partial;
		$payload=$this->db->query("select u.name,o.bill_email,o.ship_email from king_orders o join king_users u on u.userid=o.userid where o.id=?",$os[0]['order_id'])->row_array();
		$mdata['name']=$payload['name'];
		$msg=$this->load->view("mails/shipment",$mdata,true);
//		echo $msg;
		$this->vkm->email(array($payload['bill_email'],$payload['ship_email']),"Your order is shipped",$msg,true);
*/		
		die("<div class='outscanned' style='background:#fcfcfc;'>Invoice/Order No {$awb} outscanned</div>");
		exit;
	}
	
	function do_deals_bulk_upload()
	{
		$f=@fopen($_FILES['deals']['tmp_name'],"r");
		$head=fgetcsv($f);
		$template=array("itemcode","name","menu1","menu2","catid","brandid","mrp","price","pic","tax","sdate","edate","shipsin","qty","description1","description2","keywords","products");
		if(empty($head) || count($head)!=count($template))
			show_error("Invalid template structure");
		while(($data=fgetcsv($f))!=false)
		{
			$dealid=$this->erpm->p_genid(10);
			foreach($template as $k=>$v)
				$$v=$data[$k];
			$sdate_a=explode("-",$sdate);
			$edate_a=explode("-",$edate);
			if(count($sdate_a)!=3 || count($edate_a)!=3)
				show_error("Invalid date format for '$name'");
			if($this->db->query("select 1 from king_brands where id=? limit 1",$brandid)->num_rows()==0)
				show_error("No brand with brand id $brandid for deal $name");
			if($this->db->query("select 1 from king_categories where id=? limit 1",$catid)->num_rows()==0)
				show_error("No category with id $catid for deal $name");
			if($this->db->query("select 1 from king_dealitems where name=? and is_pnh=0 limit 1",$name)->num_rows()!=0)
				show_error("Duplicate deal name : $name");
			foreach(explode(",",$products) as $prod)
			{
				list($pid,$qty)=explode(":",$prod);
				if($this->db->query("select 1 from m_product_info where product_id=?",$pid)->num_rows()==0)
					show_error("No product with product id : $pid for deal name $name");
			}
		}
		fclose($f);
		$f=@fopen($_FILES['deals']['tmp_name'],"r");
		$head=fgetcsv($f);
		while(($data=fgetcsv($f))!=false)
		{
			$dealid=$this->erpm->p_genid(10);
			foreach($template as $k=>$v)
				$$v=$data[$k];
			list($d,$m,$y)=explode("-",$sdate);
			$sdate=mktime(0,0,0,$m,$d,$y);
			list($d,$m,$y)=explode("-",$edate);
			$edate=mktime(23,0,0,$m,$d,$y);
			$inp=array("dealid"=>$dealid,"menuid"=>$menu1,"menuid2"=>$menu2,"catid"=>$catid,"brandid"=>$brandid,"startdate"=>$sdate,"enddate"=>$edate,"pic"=>$pic,"tagline"=>$name,"keywords"=>$keywords,"publish"=>0,"is_coupon_applicable"=>1);
			$this->db->insert("king_deals",$inp);
			$itemid=$this->erpm->p_genid(10);
			$url=$name;
			$blacks=array("?","#","@",")","(","[","]","/","\\","!","~","`","%","^","&","*","+","=","'",'"');
			foreach($blacks as $b)
			{
				$url=str_replace(" $b","",$url);
				$url=str_replace($b,"",$url);
			}
			$url=str_replace(" ","-",$url);
			$url.="-p".rand(10,99)."t";
			if(empty($qty) || $qty==0)
				$qty=4294967295;
			$tax=$tax*100;
			$inp=array("id"=>$itemid,"dealid"=>$dealid,"shipsin"=>$shipsin,"itemcode"=>$itemcode,"price"=>$price,"orgprice"=>$mrp,"name"=>$name,"quantity"=>$qty,"pic"=>$pic,"description1"=>$description1,"description2"=>$description2,"url"=>$url,"live"=>1,"tax"=>$tax,"groupbuy"=>0);
			$this->db->insert("king_dealitems",$inp);
			foreach(explode(",",$products) as $prod)
			{
				list($pid,$qty)=explode(":",$prod);
				$inp=array("itemid"=>$itemid,"product_id"=>$pid,"product_mrp"=>$this->db->query("select mrp from m_product_info where product_id=?",$pid)->row()->mrp,"qty"=>$qty);
				$this->db->insert("m_product_deal_link",$inp);
			}
			$report[]=array($itemid,$dealid,$name);
		}
		fclose($f);
		
		$user=$this->erpm->getadminuser();
		$this->db->insert("deals_bulk_upload",array("items"=>count($report),"created_on"=>time(),"created_by"=>$user['userid']));
		$bulk_id=$this->db->insert_id();
		foreach($report as $r)
			$this->db->insert("deals_bulk_upload_items",array("bulk_id"=>$bulk_id,"item_id"=>$r[0]));
		
		if(empty($report))
			show_error("No deals created");
		ob_start();
		$f=fopen("php://output","w");
		fputcsv($f,array("Item ID","Deal ID","Name"));
		foreach($report as $p)
			fputcsv($f,$p);
		fclose($f);
		$csv=ob_get_clean();
		ob_clean();
	    header('Content-Description: File Transfer');
	    header('Content-Type: text/csv');
	    header('Content-Disposition: attachment; filename='.("deals_bulk_upload_report_".date("d_m_y_H\h:i\m").".csv"));
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . strlen($csv));
	    ob_clean();
	    flush();
	    echo $csv;
	    redirect("admin/deals_bulk_upload");
	    exit;
	}
	
	function do_products_group_bulk_upload()
	{
		$user=$this->erpm->getadminuser();
		$f=@fopen($_FILES['pg']['tmp_name'],"r");
		$head=fgetcsv($f);
		$template=array("Group Name","Category ID","Attribute");
		$payload=array();
		$o_data=array();
		$head=array_merge(array("Group ID"),$head);
		$i=0;
		while(($data=fgetcsv($f))!=false)
		{
			$load=array();
			$load['name']=$data[0];
			$load['cat_id']=$data[1];
			if(empty($data[1]))
				show_error("Category ID can't be empty for group '{$data[0]}'");
			$attr=explode(",",$data[2]);
			if(count($data)-3<count($attr))
				show_error("Invalid attribute structure for '{$data[0]}'");
			foreach($attr as $i=>$at)
			{
				$av=explode(",",$data[$i+3]);
				$load['attrs'][$at]=array_unique($av);
			}
			$payload[]=$load;
			$o_data[]=array_combine($head,array_merge(array(0),$data));
			$i++;
		}
		foreach($payload as $i=>$pg)
		{
			$inp=array("group_name"=>$pg['name'],"cat_id"=>$pg['cat_id'],"created_on"=>time(),"created_by"=>$user['userid']);
			$this->db->insert("products_group",$inp);
			$gid=$this->db->insert_id();
			if(isset($o_data[$i]['Group ID']))
				$o_data[$i]['Group ID']=$gid;
			else
				$o_data[$i][0]=$gid;
			foreach($pg['attrs'] as $attr=>$values)
			{
				$inp=array("group_id"=>$gid,"attribute_name"=>$attr);
				$this->db->insert("products_group_attributes",$inp);
				$aid=$this->db->insert_id();
				foreach($values as $v)
					$this->db->insert("products_group_attribute_values",array("group_id"=>$gid,"attribute_name_id"=>$aid,"attribute_value"=>$v));
			}
		}
		$this->erpm->flash_msg(count($payload)." product groups uploaded");
		$this->erpm->export_csv("products_group",$o_data);
	}
	
	function do_pnh_deals_bulk_upload()
	{
		$f=@fopen($_FILES['deals']['tmp_name'],"r");
		$head=fgetcsv($f);
		$template=array("name","tagline","catid","brandid","mrp","price","store_price","nyp_price","gender_attr","pic","tax","description","products","products_group","keywords","menu","shipsin","publish");
		if(empty($head) || count($head)!=count($template))
			show_error("Invalid template structure");
		while(($data=fgetcsv($f))!=false)
		{
			$dealid=$this->erpm->p_genid(10);
			foreach($template as $k=>$v)
				$$v=$data[$k];
			if($this->db->query("select 1 from king_brands where id=? limit 1",$brandid)->num_rows()==0)
				show_error("No brand with brand id $brandid for deal $name");
			if($this->db->query("select 1 from king_categories where id=? limit 1",$catid)->num_rows()==0)
				show_error("No category with id $catid for deal $name");
			if($this->db->query("select 1 from king_dealitems where name=? and is_pnh=1 limit 1",$name)->num_rows()!=0)
				show_error("Duplicate deal name : $name");
			if(!empty($products))
			foreach(explode(",",$products) as $prod)
			{
				list($pid,$qty)=explode(":",$prod);
				if($this->db->query("select 1 from m_product_info where product_id=?",$pid)->num_rows()==0)
					show_error("No product with product id : $pid for deal name $name");
			}
		}
		fclose($f);
		$f=@fopen($_FILES['deals']['tmp_name'],"r");
		$head=fgetcsv($f);
		while(($data=fgetcsv($f))!=false)
		{
			$dealid=$this->erpm->p_genid(10);
			foreach($template as $k=>$v)
				$$v=$data[$k];

			$itemid=$this->erpm->p_genid(10);
			$dealid=$this->erpm->p_genid(10);
			$pnh_id="1".$this->erpm->p_genid(7);
			$inp=array("dealid"=>$dealid,"catid"=>$catid,"brandid"=>$brandid,"pic"=>$pic,"tagline"=>$tagline,"description"=>$description,"publish"=>$publish,"menuid"=>$menu,"keywords"=>$keywords);
			$this->db->insert("king_deals",$inp);

			$inp=array("id"=>$itemid,"shipsin"=>$shipsin,"gender_attr"=>$gender_attr,"dealid"=>$dealid,"name"=>$name,"pic"=>$pic,"orgprice"=>$mrp,"price"=>$price,"store_price"=>$store_price,"nyp_price"=>$nyp_price,"is_pnh"=>1,"pnh_id"=>$pnh_id,"tax"=>$tax*100,"live"=>1);
			$this->db->insert("king_dealitems",$inp);
			
			if(!empty($products))
			foreach(explode(",",$products) as $prod)
			{
				list($pid,$qty)=explode(":",$prod);
				$inp=array("itemid"=>$itemid,"product_id"=>$pid,"product_mrp"=>$this->db->query("select mrp from m_product_info where product_id=?",$pid)->row()->mrp,"qty"=>$qty);
				$this->db->insert("m_product_deal_link",$inp);
			}
			
			if(!empty($products_group))
			foreach(explode(",",$products_group) as $pg)
			{
				list($gid,$qty)=explode(":",$pg);
				$inp=array("itemid"=>$itemid,"group_id"=>$gid,"qty"=>$qty);
				$this->db->insert("m_product_group_deal_link",$inp);
			}
			
			$report[]=array($itemid,$dealid,$name,$pnh_id);
		}
		fclose($f);
		
		$user=$this->erpm->getadminuser();
		$this->db->insert("deals_bulk_upload",array("items"=>count($report),"created_on"=>time(),"created_by"=>$user['userid']));
		$bulk_id=$this->db->insert_id();
		foreach($report as $r)
			$this->db->insert("deals_bulk_upload_items",array("bulk_id"=>$bulk_id,"item_id"=>$r[0]));
		
		if(empty($report))
			show_error("No deals created");
		ob_start();
		$f=fopen("php://output","w");
		fputcsv($f,array("Item ID","Deal ID","Name","PNH ID"));
		foreach($report as $p)
			fputcsv($f,$p);
		fclose($f);
		$csv=ob_get_clean();
		ob_clean();
	    header('Content-Description: File Transfer');
	    header('Content-Type: text/csv');
	    header('Content-Disposition: attachment; filename='.("pnh_deals_bulk_upload_report_".date("d_m_y_H\h:i\m").".csv"));
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . strlen($csv));
	    ob_clean();
	    flush();
	    echo $csv;
	    redirect("admin/deals_bulk_upload");
	    exit;
	}
	
	function do_prods_bulk_upload()
	{
		$f=@fopen($_FILES['prods']['tmp_name'],"r");
		$head=fgetcsv($f);
		while(($data=fgetcsv($f))!==false)
		{
			if($this->db->query("select 1 from m_product_info where product_name=?",$data[0])->num_rows()!=0)
				show_error("Duplicate name for product : ".$data[0]);
			if($this->db->query("select 1 from king_brands where id=?",$data[2])->num_rows()==0)
				show_error("No brand with id {$data[2]} for product {$data[0]}");
			if(!empty($data[12]) || $data[12]!=0)
			{
				$gid=$data[12];
				$group=$this->db->query("select 1 from products_group where group_id=?",$gid)->row_array();
				if(empty($group))
					show_error("Invalid group ID for product '{$data[0]}'");
				if(empty($data[13]))
					show_error("Attribute data missing for product '{$data[0]}'");
				$adata=explode(",",$data[13]);
				$n_attrs=$this->db->query("select count(1) as n from products_group_attributes where group_id=?",$gid)->row()->n;
				if($n_attrs!=count($adata))
					show_error("Insufficient Attribute data for product '{$data[0]}'");
				foreach($adata as $av)
				{
					$aa=explode(":",$av);
					if(count($aa)!=2)
						show_error("Missing attribute name/value for '{$data[0]}'");
					list($a,$v)=$aa;
					if($this->db->query("select 1 from products_group_attributes where group_id=$gid and attribute_name=?",$a)->num_rows()==0)
						show_error("Invalid attribute '$a' for '{$data[0]}'");
					if($this->db->query("select 1 from products_group_attribute_values where group_id=$gid and attribute_value=?",$v)->num_rows()==0)
						show_error("Invalid attribute value '$v' for '{$data[0]}'");
				}
			}
		}
		fclose($f);
		$f=@fopen($_FILES['prods']['tmp_name'],"r");
		$head=fgetcsv($f);
		$template=array("Product Name","Short Description","Brand (ID)","Size","Unit of measurement","MRP","VAT %","Purchase Cost","Barcode","Is offer (0 or 1)","Is Sourceable (0 or 1)","is serial required");
		$db_temp=array("product_name","short_desc","brand_id","size","uom","mrp","vat","purchase_cost","barcode","is_offer","is_sourceable","is_serial_required");
		if(empty($head) || count($head)<count($template))
			show_error("Invalid template structure");
		$pids=array();
		while(($data=fgetcsv($f))!==false)
		{
			$inp=array("product_code"=>"P".rand(10000,99999));
			foreach($db_temp as $i=>$d)
				$inp[$d]=$data[$i];
			$this->db->insert("m_product_info",$inp);
			$bid=$data[2];
			$pid=$this->db->insert_id();
			$pids[]=array($pid,$data[0],$bid);
			$rackbin=0;$location=0;
			$raw_rackbin=$this->db->query("select l.location_id as default_location_id,l.id as default_rack_bin_id from m_rack_bin_brand_link b join m_rack_bin_info l on l.id=b.rack_bin_id where b.brandid=?",$bid)->row_array();
			if(!empty($raw_rackbin))
			{
				$rackbin=$raw_rackbin['default_rack_bin_id'];
				$location=$raw_rackbin['default_location_id'];
			}
			$this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,available_qty) values(?,?,?,?,0)",array($pid,$location,$rackbin,$data[5]));
			if(!empty($data[12]) || $data[12]!=0)
			{
				$gid=$data[12];
				$adata=explode(",",$data[13]);
				foreach($adata as $av)
				{
					$aa=explode(":",$av);
					list($a,$v)=$aa;
					$aid=$this->db->query("select attribute_name_id as a from products_group_attributes where group_id=$gid and attribute_name=?",$a)->row()->a;
					$vid=$this->db->query("select attribute_value_id as a from products_group_attribute_values where group_id=$gid and attribute_value=?",$v)->row()->a;
					$inp=array("group_id"=>$gid,"product_id"=>$pid,"attribute_name_id"=>$aid,"attribute_value_id"=>$vid);
					$this->db->insert("products_group_pids",$inp);
				}
			}
		}
		fclose($f);
		if(empty($pids))
			show_error("No products created");
		ob_start();
		$f=fopen("php://output","w");
		fputcsv($f,array("product_id","name","brandid"));
		foreach($pids as $p)
			fputcsv($f,$p);
		fclose($f);
		$csv=ob_get_clean();
		ob_clean();
	    header('Content-Description: File Transfer');
	    header('Content-Type: text/csv');
	    header('Content-Disposition: attachment; filename='.("products_bulk_upload_report_".date("d_m_y_H\h:i\m").".csv"));
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . strlen($csv));
	    ob_clean();
	    flush();
	    echo $csv;
	    exit;
	}
	
	function export_csv($fname,$data,$date=true)
	{
		$head=array();
		if(!empty($data))
			foreach($data[0] as $k=>$v)
				$head[]=$k;
		ob_start();
		$f=fopen("php://output","w");
		fputcsv($f,$head);
		foreach($data as $p)
			fputcsv($f,$p);
		fclose($f);
		$csv=ob_get_clean();
		ob_clean();
	    header('Content-Description: File Transfer');
	    header('Content-Type: text/csv');
	    $h_filename=$fname.".csv";
	    if($date)
	    	$h_filename="{$fname}_".date("d-m-y_H\h-i\m").".csv";
	    header('Content-Disposition: attachment; filename='.$h_filename);
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . strlen($csv));
	    ob_clean();
	    flush();
	    echo $csv;
	    exit;
	}
	
	function do_export_data()
	{
		$type=$this->input->post("type");
		switch($type)
		{
			case 0:
				$this->erpm->export_csv("products",$this->db->query("select product_id,product_name,mrp,brand_id from m_product_info order by product_name asc")->result_array());
			case 1:
				$this->erpm->export_csv("brands",$this->db->query("select id,name from king_brands order by name asc")->result_array());
			case 2:
				$this->erpm->export_csv("categories",$this->db->query("select id,name from king_categories order by name asc")->result_array());
			case 3:
				$this->erpm->export_csv("menu",$this->db->query("select id,name from king_menu order by name asc")->result_array());
		}
	}
	
	function do_add_storage_loc()
	{
		foreach(array("location_name","damaged") as $inp)
			$$inp=$this->input->post($inp);
		$user=$this->erpm->getadminuser();
		$this->db->query("insert into m_storage_location_info(location_name,is_damaged,created_on,created_by) values(?,?,now(),?)",array($location_name,$damaged,$user['userid']));
		$this->session->set_flashdata("erp_pop_info","New Location added");
		redirect("admin/storage_locs");
	}
	
	function do_updatecat($cat)
	{
		$name=$_POST['cat_name'];
		$type=$_POST['main'];
		$this->db->query("update king_categories set type=?,name=? where id=?",array($type,$name,$cat));
		$this->session->set_flashdata("erp_pop_info","Category updated");
		redirect("admin/viewcat/$cat");
	}
	
	function do_addnewcat()
	{
		$name=$_POST['cat_name'];
		$type=$_POST['main'];
		$url=preg_replace('/[^a-zA-Z0-9_\-]/','',$name);
		$url=str_replace(" ","-",$url);
		$this->db->query("insert into king_categories(name,type,url) values(?,?,?)",array($name,$type,$url));
		$id=$this->db->insert_id();
		$this->session->set_flashdata("erp_pop_info","new category added");
		redirect("admin/viewcat/$id");
	}
	
	function do_pnh_order_import($payload=array(),$output=true)
	{
		if(empty($payload))
		{
			$f=fopen($_FILES['csv']['tmp_name'],"r");
			$head=fgetcsv($f);
			$out=array($head);
			$c=0;
			$payload=array();
			while(($data=fgetcsv($f))!==false)
				$payload[]=$data;
		}
		$out=array();
		$ret_out=array();
		foreach($payload as $pl)
		{
			$total=0;$d_total=0;
			$status=1;$msg="";
			if(count($pl)!=12)
			{
				$status=0;$msg="Invalid template structure";
			}
			$fran=$this->db->query("select * from pnh_m_franchise_info where pnh_franchise_id=?",$pl[0])->row_array();
			$pids=explode(",",$pl[2]);
			$pqtys=explode(",",$pl[3]);
			$transid="";
			if($status && empty($fran))
			{
				$status=0;$msg="Invalid franchise id";
			}
			if($status && $fran['is_suspended']==1)
			{
				$status=0;$msg="Franchise account suspended";
			}
			if($status && count($pqtys)!=count($pids))
			{
				$status=0;$msg="Invalid Order-Qty structure";
			}
			$mid=$pl[1];
			if($status && $this->db->query("select 1 from pnh_member_info where pnh_member_id=?",$mid)->num_rows()==0 && $this->db->query("select 1 from pnh_m_allotted_mid where franchise_id=? and ? between mid_start and mid_end",array($fran['franchise_id'],$mid))->num_rows()==0)
			{
				$status=0;$msg="MID:$mid is not allotted to this franchise";			
			}
			
			
			if($status && !empty($pl[11]))
			{
				$attr=json_decode($pl[11]);
				$attr_data=array();
				foreach($attr as $pid=>$at)
				{
					$attrs=explode(",",$at);
					$gid=$this->db->query("select group_id from m_product_groups_deal_link where itemid=?",$this->db->query("select id from king_dealitems where is_pnh=1 and pnh_id=?",$pid)->row()->id)->row()->group_id;
					foreach($attrs as $att)
					{
						list($an,$vn)=explode(":",$att);
						$a=$this->db->query("select attribute_name_id as a from products_group_attributes where group_id=? and attribute_name=?",array($gid,$an))->row()->a;
						$v=$this->db->query("select attribute_value_id as a from products_group_attribute_values where group_id=? and attribute_name_id=? and attribute_value=?",array($gid,$a,$vn))->row()->a;
						if(!isset($attr_data[$pid]))
							$attr_data[$pid]=array();
						$attr_data[$pid][$a]=$v;
					}
				}
				foreach($pids as $pid)
				{
					if(!isset($attr_data[$pid]))
						continue;
					$prods=array();
					$i=0;
					foreach($attr_data[$pid] as $a=>$v)
					{
						if($i==0)
						{
							$pr=$this->db->query("select product_id from products_group_pids where attribute_name_id=? and attribute_value_id=?",array($a,$v))->result_array();
							foreach($pr as $p)
								$prods[]=$p['product_id'];
						}else{
						$c_prods=$prods;
						$prods=array();
						$pr=$this->db->query("select product_id from products_group_pids where attribute_name_id=? and attribute_value_id=?",array($a,$v))->result_array();
						foreach($pr as $p)
							if(in_array($p['product_id'],$c_prods))
								$prods[]=$p['product_id'];
						}
						$i++;
						if(empty($prods))
						{
							$status=0;
							$msg="{$pid} is not available for selected attribute combination";
							break;
						}else{ $order_attr[$pid]=$prods[0]['product_id'];}
					}
				}
			}
			
			
			if($status)
			{
				$itemids=array();
				$qtys=array();
				foreach(explode(",",$pl[2]) as $i_c=>$pid)
				{
					$pid=trim($pid);
					$r_id=$this->db->query("select id,dealid from king_dealitems where is_pnh=1 and pnh_id=?",$pid)->row_array();
					if(empty($r_id))
					{
						$status=0;$msg="Invalid PNH Product ID $pid";break;
					}
					if($this->db->query("select publish from king_deals where dealid=?",$r_id['dealid'])->row()->publish==0)
					{
						$status=0;$msg="PNH Product ID $pid is disabled";break;
					}
					$itemids[]=$r_id['id'];
					$qtys[]=$pqtys[$i_c];
				}
				if($status)
				{
				$avail=$this->erpm->do_stock_check($itemids,$qtys);
				foreach($itemids as $i=>$id)
					if(!in_array($id,$avail))
					{
						$status=0;$msg="PNH Product ID {$pl[2][$i]} is out of stock";
					}
				}
			}
			if($status)
			{
				$margin=$this->db->query("select margin,combo_margin from pnh_m_class_info where id=?",$fran['class_id'])->row_array();
				if($fran['sch_discount_start']<time() && $fran['sch_discount_end']>time() && $fran['is_sch_enabled'])
					$margin['margin']+=$fran['sch_discount'];
				$items=array();
				foreach($pids as $i=>$p)
					$items[]=array("pid"=>$p,"qty"=>$pqtys[$i]);
				$total=0;$d_total=0;
				$itemnames=$itemids=array();
				foreach($items as $i=>$item)
				{
					$prod=$this->db->query("select i.*,d.publish from king_dealitems i join king_deals d on d.dealid=i.dealid where i.is_pnh=1 and  i.pnh_id=? and i.pnh_id!=0",$item['pid'])->row_array();
					$items[$i]['tax']=$prod['tax'];
					$items[$i]['mrp']=$prod['orgprice'];
					if($fran['is_lc_store'])
						$items[$i]['price']=$prod['store_price'];
					else
						$items[$i]['price']=$prod['price'];
					$margin=$this->erpm->get_pnh_margin($fran['franchise_id'],$item['pid']);
					$items[$i]['itemid']=$prod['id'];
					if($prod['is_combo']=="1")
						$items[$i]['discount']=$items[$i]['price']/100*$margin['combo_margin'];
					else
						$items[$i]['discount']=$items[$i]['price']/100*$margin['margin'];
					$total+=$items[$i]['price']*$items[$i]['qty'];
					$d_total+=($items[$i]['price']-$items[$i]['discount'])*$items[$i]['qty'];
					$itemids[]=$prod['id'];
					$itemnames[]=$prod['name'];
					$items[$i]['margin']=$margin;
				}
				if($fran['credit_limit']+$fran['current_balance']<$d_total)
				{
					$status=0;$msg="Insufficient balance! Balance in your account Rs {$fran['current_balance']} Total order amount : Rs $d_total";
				}
				else{
					if($this->db->query("select 1 from pnh_member_info where pnh_member_id=?",$mid)->num_rows()==0)
					{ 
						$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $mid",time()));
						$userid=$this->db->insert_id();
						$this->db->query("insert into pnh_member_info(user_id,pnh_member_id,franchise_id) values(?,?,?)",array($userid,$mid,$fran['franchise_id']));
						$npoints=$this->db->query("select points from pnh_member_info where user_id=?",$userid)->row()->points+PNH_MEMBER_FEE;
						$this->db->query("update pnh_member_info set points=? where user_id=? limit 1",array($npoints,$userid));
						$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values(?,?,?,?,?)",array($userid,"",PNH_MEMBER_FEE,$npoints,time()));
						$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1,PNH_MEMBER_FEE,"member fee for $mid","member",$mid);
						$this->erpm->pnh_fran_account_stat($fran['franchise_id'],0,PNH_MEMBER_BONUS,"New member bonus for $mid","member",$mid);
						$this->db->query("update pnh_member_info set first_name=?,address=?,city=?,pincode=?,email=?,mobile=?,created_on=? where user_id=? limit 1",array($pl[5],$pl[6],$pl[7],$pl[8],$pl[9],$pl[10],time(),$userid));
					}
					else
						$userid=$this->db->query("select user_id from pnh_member_info where pnh_member_id=?",$mid)->row()->user_id;
					$transid=strtoupper("PNH".random_string("alpha",3).$this->p_genid(5));
					$this->db->query("insert into king_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id) values(?,?,?,?,?,?,?,?)",array($transid,$d_total,$d_total,3,time(),time(),1,$fran['franchise_id']));
					foreach($items as $item)
					{
						$pid=$item['pnh_id'];
						$inp=array("id"=>$this->p_genid(10),"transid"=>$transid,"userid"=>$userid,"itemid"=>$item['itemid'],"brandid"=>"");
						$inp["brandid"]=$this->db->query("select d.brandid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['itemid'])->row()->brandid;
						$inp["bill_person"]=$inp['ship_person']=$fran['franchise_name'];
						$inp["bill_address"]=$inp['ship_address']=$fran['address'];
						$inp["bill_city"]=$inp['ship_city']=$fran['city'];
						$inp['bill_pincode']=$inp['ship_pincode']=$fran['postcode'];
						$inp['bill_phone']=$inp['ship_phone']=$fran['login_mobile1'];
						$inp['bill_email']=$inp['ship_email']=$fran['email_id'];
						$inp['bill_state']=$inp['ship_state']=$fran['state'];
						$inp['quantity']=$item['qty'];
						$inp['time']=time();
						$inp['ship_landmark']=$inp['bill_landmark']=$fran['locality'];
						$inp['bill_country']=$inp['ship_country']="India";
						$inp['i_orgprice']=$item['mrp'];
						$inp['i_price']=$item['price'];
						$inp['i_discount']=$item['mrp']-$item['price'];
						$inp['i_coup_discount']=$item['discount'];
						$inp['i_tax']=$item['tax'];
						$this->db->insert("king_orders",$inp);
						$oid=$this->db->insert_id();
						if(isset($order_attr[$pid]))
							$this->db->insert("products_group_orders",array("transid"=>$transid,"order_id"=>$oid,"product_id"=>$order_attr[$pid]));
						$m_inp=array("transid"=>$transid,"itemid"=>$item['itemid'],"mrp"=>$item['mrp'],"price"=>$item['price'],"base_margin"=>$item['margin']['base_margin'],"sch_margin"=>$item['margin']['sch_margin'],"qty"=>$item['qty'],"final_price"=>$item['price']-$item['discount']);
						$this->db->insert("pnh_order_margin_track",$m_inp);
					}
					$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1, $d_total,"Order $transid - Total Amount: Rs $total","transaction",$transid);
					$balance=$this->db->query("select current_balance from pnh_m_franchise_info where franchise_id=?",$fran['franchise_id'])->row()->current_balance;
					$this->erpm->pnh_sendsms($fran['login_mobile1'],"Your order is placed successfully! Total order amount :Rs $total. Amount deducted is Rs $d_total. Your order ID is $transid Balance in your account Rs $balance",$fran['franchise_id']);
					$points=$this->db->query("select points from pnh_loyalty_points where amount<? order by amount desc limit 1",$total)->row_array();
					if(!empty($points))
						$points=$points['points'];
					else
						$points=0;
					$apoints=$this->db->query("select points from pnh_member_info where user_id=?",$userid)->row()->points+$points;
					$this->db->query("update pnh_member_info set points=points+? where user_id=? limit 1",array($points,$userid));
					$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values(?,?,?,?,?)",array($userid,$transid,$points,$apoints,time()));
					$this->erpm->do_trans_changelog($transid,$output?"PNH order created through import":"PNH Order created through data api",$output?null:1);
					$ins['transid'] = $transid;
					$ins['note'] = 'User Note:'.$pl[4];
					$ins['status'] = 1;
					$ins['note_priority'] = 1;
					$ins['created_on'] = date('Y-m-d H:i:s');
					$this->db->insert('king_transaction_notes',$ins);

					$franid=$fran['franchise_id'];
					$billno=10001;
					$nbill=$this->db->query("select bill_no from pnh_cash_bill where franchise_id=? order by bill_no desc limit 1",$franid)->row_array();
					if(!empty($nbill))
						$billno=$nbill['bill_no']+1;
					$inp=array("bill_no"=>$billno,"franchise_id"=>$franid,"transid"=>$transid,"user_id"=>$userid,"status"=>1);
					$this->db->insert("pnh_cash_bill",$inp);
				
				}
			}
			$ret_plo=array();
			$plo=array();
			$ret_plo['pnh_franchise_id']=$pl[0];$ret_plo['member_id']=$pl[1];$ret_plo['pnh_product_id']=$pl[2];$ret_plo['qtys']=$pl[3];
			$plo['PNH Franchise ID']=$pl[0];$plo['Member ID']=$pl[1];$plo['PNH Product ID']=$pl[2];$plo['Qtys']=$pl[3];
			$plo['Status']=$status;
			$plo['Msg']=$msg;
			$plo['Transid']=$transid;
			$plo['Order value']=$total;
			$plo['Amount detected']=$d_total;
			$ret_plo['status']=$status;$ret_plo['msg']=$msg;$ret_plo['transid']=$transid;$ret_plo['order_value']=$total;$ret_plo['amount_detected']=$d_total;
			$out[]=$plo;
			$ret_out[]=$ret_plo;
		}
		if($output)
			$this->erpm->export_csv("pnh_order_import_results",$out);
		return $ret_out;
	}
	
	function getadminuser()
	{
		return $this->session->userdata("admin_user");
	}
	
	function getinvoiceforpacking($inv_no)
	{
		if($this->db->query("select 1 from shipment_batch_process_invoice_link where p_invoice_no=? and packed=0",$inv_no)->num_rows()==0)
			show_error("Proforma Invoice already packed");
		$this->benchmark->mark('code_start');
		$ret=$this->db->query("select d.pic,d.is_pnh,e.menuid,i.discount,p.product_id,p.mrp,p.barcode,i.p_invoice_no,p.product_name,o.i_orgprice as order_mrp,o.quantity*pl.qty as qty,d.name as deal,d.dealid,o.itemid,o.id as order_id,i.p_invoice_no from proforma_invoices i join king_orders o on o.id=i.order_id join m_product_deal_link pl on pl.itemid=o.itemid join m_product_info p on p.product_id=pl.product_id join king_dealitems d on d.id=o.itemid join king_deals e on e.dealid=d.dealid where i.p_invoice_no=? and i.invoice_status=1",$inv_no)->result_array();
		$ret2=$this->db->query("select d.pic,d.is_pnh,e.menuid,i.discount,i.discount,p.product_id,p.mrp,p.barcode,i.p_invoice_no,p.product_name,o.i_orgprice as order_mrp,o.quantity*pl.qty as qty,d.name as deal,d.dealid,o.itemid,o.id as order_id,i.p_invoice_no from proforma_invoices i join king_orders o on o.id=i.order_id join products_group_orders pgo on pgo.order_id=o.id join m_product_group_deal_link pl on pl.itemid=o.itemid join m_product_info p on p.product_id=pgo.product_id join king_dealitems d on d.id=o.itemid join king_deals e on e.dealid=d.dealid where i.p_invoice_no=? and i.invoice_status=1",$inv_no)->result_array();
		$ret=array_merge($ret,$ret2);
		if(empty($ret))
			show_error("Proforma Invoice not found or Invoice is already cancelled");
		$this->benchmark->mark('code_end');
		return $ret;
	}

	function getbatch($bid)
	{
		return $this->db->query("select * from shipment_batch_process where batch_id=?",$bid)->row_array();
	}
	
	function getbatchinvoices($bid)
	{
		return $this->db->query("select t.init as ordered_on,b.*,pi.transid as pi_transid,pi.invoice_status as p_invoice_status,i.transid,i.invoice_status from shipment_batch_process_invoice_link b left outer join proforma_invoices pi on pi.p_invoice_no=b.p_invoice_no left outer join king_invoice i on i.invoice_no=b.invoice_no left outer join king_transactions t on t.transid=pi.transid where batch_id=? group by b.p_invoice_no",$bid)->result_array();
	}
	
	function getpnhreport($fid)
	{
		$lmonth=mktime(0,0,1,date("m")-1,1);
		$month=mktime(0,0,1,date("m"),1);
		$ret['orders']=$this->db->query("select count(1) as l from king_transactions where is_pnh=1".($fid!=0?" and franchise_id=?":""),$fid)->row()->l;
		$ret['m_orders']=$this->db->query("select count(1) as l from king_transactions where is_pnh=1 and init>$month".($fid!=0?" and franchise_id=?":""),$fid)->row()->l;
		$ret['a_orders']=$this->db->query("select avg(amount) as l from king_transactions where is_pnh=1".($fid!=0?" and franchise_id=?":""),$fid)->row()->l;
		$ret['a_items']=$this->db->query("select avg(n) as l from(select count(o.id) as n from king_orders o join king_transactions t on t.transid=o.transid and t.is_pnh=1".($fid!=0?" and franchise_id=$fid":"")." group by o.transid) as d")->row()->l;
		$ret['sales']=$this->db->query("select sum(amount) as l from king_transactions where is_pnh=1".($fid!=0?" and franchise_id=?":""),$fid)->row()->l;
		$ret['m_sales']=$this->db->query("select sum(amount) as l from king_transactions where is_pnh=1 and init>$month".($fid!=0?" and franchise_id=?":""),$fid)->row()->l;
		$ret['users']=$this->db->query("select count(1) as l from pnh_member_info".($fid!=0?" where franchise_id=?":""),$fid)->row()->l;
		$ret['m_users']=$this->db->query("select count(1) as l from king_users u join pnh_member_info m on m.user_id=u.userid where u.createdon>$month".($fid!=0?" and m.franchise_id=?":""),$fid)->row()->l;
		$ret['credits']=$this->db->query("select sum(amount) as s from pnh_franchise_account_stat where type=0".($fid!=0?" and franchise_id=?":""),$fid)->row()->s;
		$ret['debits']=$this->db->query("select sum(amount) as s from pnh_franchise_account_stat where type=1".($fid!=0?" and franchise_id=?":""),$fid)->row()->s;
		$ret['m_credits']=$this->db->query("select sum(amount) as s from pnh_franchise_account_stat where type=0 and created_on>$month".($fid!=0?" and franchise_id=?":""),$fid)->row()->s;
		$ret['m_debits']=$this->db->query("select sum(amount) as s from pnh_franchise_account_stat where type=1 and created_on>$month".($fid!=0?" and franchise_id=?":""),$fid)->row()->s;
		$ret['comm']=$this->db->query("select sum(o.i_coup_discount*o.quantity) as s from king_orders o join king_transactions t on t.transid=o.transid and t.is_pnh=1".($fid!=0?" and franchise_id=?":""),$fid)->row()->s;
		$ret['m_comm']=$this->db->query("select sum(o.i_coup_discount*o.quantity) as s from king_orders o join king_transactions t on t.transid=o.transid and t.is_pnh=1".($fid!=0?" and franchise_id=?":"")." where o.time>$month",$fid)->row()->s;
		$ret['topup']=$this->db->query("select sum(receipt_amount) as s from pnh_t_receipt_info where receipt_type=1".($fid!=0?" and franchise_id=?":""),$fid)->row()->s;
		$ret['m_topup']=$this->db->query("select sum(receipt_amount) as s from pnh_t_receipt_info where receipt_type=1 and created_on>$month".($fid!=0?" and franchise_id=?":""),$fid)->row()->s;
		$ret['r_topup']=$this->db->query("select sum(receipt_amount) as s from pnh_t_receipt_info where receipt_type=1 and status=1".($fid!=0?" and franchise_id=?":""),$fid)->row()->s;
		$ret['m_r_topup']=$this->db->query("select sum(receipt_amount) as s from pnh_t_receipt_info where receipt_type=1 and status=1 and created_on>$month".($fid!=0?" and franchise_id=?":""),$fid)->row()->s;
		$ret['c_a_orders']=$this->db->query("select round(((select sum(amount) from king_transactions where is_pnh=1 and init > $month".($fid!=0?" and franchise_id=$fid":"").")-(select sum(amount) from king_transactions where is_pnh=1 and init between $lmonth and $month".($fid!=0?" and franchise_id=$fid":"")."))/(select sum(amount) from king_transactions where is_pnh=1 and init between $lmonth and $month".($fid!=0?" and franchise_id=$fid":"").")*100,2) as s")->row()->s;
		$ret['c_n_orders']=$this->db->query("select round(((select count(1) from king_transactions where is_pnh=1 and init > $month".($fid!=0?" and franchise_id=$fid":"").")-(select count(1) from king_transactions where is_pnh=1 and init between $lmonth and $month".($fid!=0?" and franchise_id=$fid":"")."))/(select sum(1) from king_transactions where is_pnh=1 and init between $lmonth and $month".($fid!=0?" and franchise_id=$fid":"").")*100,2) as s")->row()->s;
		return $ret;
	}
	
	function getinvestorreport()
	{
		$month=mktime(0,0,1,date("m"),1);
		$ret['orders']=$this->db->query("select count(1) as l from king_orders")->row()->l;
		$ret['m_orders']=$this->db->query("select count(1) as l from king_orders where time>?",$month)->row()->l;
		$ret['a_orders']=$this->db->query("select avg(amount) as l from king_transactions where status=1 or mode=1")->row()->l;
		$ret['a_items']=$this->db->query("select avg(n) as l from(select count(id) as n from king_orders group by transid) as d")->row()->l;
		$ret['sales']=$this->db->query("select sum(amount) as l from king_transactions where status=1 or mode=1")->row()->l+$this->db->query("select sum(total_invoice_value) as l from t_client_invoice_info")->row()->l;
		$ret['m_sales']=$this->db->query("select sum(amount) as l from king_transactions where (status=1 or mode=1) and init>$month")->row()->l+$this->db->query("select sum(total_invoice_value) as l from t_client_invoice_info i where i.created_date>=?",date("Y-m-d",$month))->row()->l;
		$ret['stock']=$this->db->query("select sum(mrp*available_qty) as l from t_stock_info")->row()->l;
		$ret['m_stock']=$this->db->query("select sum(mrp*available_qty) as l from t_stock_info where created_on>=?",date("Y-m-d",$month))->row()->l;
		$ret['users']=$this->db->query("select count(1) as l from king_users")->row()->l;
		return $ret;
	}
	
	function getprodproclistfortransids($tids)
	{
		foreach(explode(",",$tids) as $pinv)
		{
			$sql="select o.transid,p.product_name as product,p.product_id,pl.qty*o.quantity as qty from shipment_batch_process_invoice_link si join proforma_invoices i on i.p_invoice_no=si.p_invoice_no and i.invoice_status=1 join king_orders o on o.id=i.order_id join m_product_deal_link pl on pl.itemid=o.itemid join m_product_info p on p.product_id=pl.product_id where si.p_invoice_no=? and si.packed=0 order by p.product_name asc";
			$raw=$this->db->query($sql,$pinv)->result_array();
			$prods=array();
			$pids=array();
			foreach($raw as $r)
			{
				if(!isset($prods[$r['product_id']]))
					$prods[$r['product_id']]=array("product_id"=>$r['product_id'],"product"=>$r['product'],"qty"=>0,"location"=>"");
				$prods[$r['product_id']]['qty']+=$r['qty'];
				$pids[]=$r['product_id'];
			}
			$pids=array_unique($pids);
			$raw_locs=$this->db->query("select s.mrp,s.product_id,rb.rack_name,rb.bin_name from t_stock_info s join m_rack_bin_info rb on rb.id=s.rack_bin_id where s.product_id in ('".implode("','",$pids)."') order by s.stock_id asc")->result_array();
			$ret=array();
			foreach($prods as $i=>$p)
			{
				$q=$p['qty'];
				$locations=array();
				$mrps=array();
				foreach($raw_locs as $s)
				{
					if($s['product_id']!=$i)
						continue;
					$q-=$s['available_qty'];
					$locations[]=$s['rack_name'].$s['bin_name'];
					$mrps[]="Rs ".$s['mrp'];
					if($q<=0)
						break;
				}
				$prods[$i]['location']=$loc=implode(", ",array_unique($locations));
				$prods[$i]['mrp']=implode(", ",array_unique($mrps));
				$assoc_loc=$loc.substr($p['product'],0,10).rand(110,9999);
				$ret[$assoc_loc]=$prods[$i];
			}
			ksort($ret);
			$transid=$r['transid'];
			$rdata["$transid ($pinv)"]=$ret;
		}
		return $rdata;
	}
	
	function getprodproclist($bid)
	{
		$sql="select p.product_name as product,p.product_id,pl.qty*o.quantity as qty from shipment_batch_process_invoice_link si join proforma_invoices i on i.p_invoice_no=si.p_invoice_no and i.invoice_status=1 join king_orders o on o.id=i.order_id join m_product_deal_link pl on pl.itemid=o.itemid join m_product_info p on p.product_id=pl.product_id where si.batch_id=? and si.packed=0 order by p.product_name asc";
		$raw=$this->db->query($sql,$bid)->result_array();
		$prods=array();
		$pids=array();
		foreach($raw as $r)
		{
			if(!isset($prods[$r['product_id']]))
				$prods[$r['product_id']]=array("product_id"=>$r['product_id'],"product"=>$r['product'],"qty"=>0,"location"=>"");
			$prods[$r['product_id']]['qty']+=$r['qty'];
			$pids[]=$r['product_id'];
		}
		$pids=array_unique($pids);
		$raw_locs=$this->db->query("select s.mrp,s.product_id,rb.rack_name,rb.bin_name from t_stock_info s join m_rack_bin_info rb on rb.id=s.rack_bin_id where s.product_id in ('".implode("','",$pids)."') order by s.stock_id asc")->result_array();
		$ret=array();
		foreach($prods as $i=>$p)
		{
			$q=$p['qty'];
			$locations=array();
			$mrps=array();
			foreach($raw_locs as $s)
			{
				if($s['product_id']!=$i)
					continue;
				$q-=$s['available_qty'];
				$locations[]=$s['rack_name'].$s['bin_name'];
				$mrps[]="Rs ".$s['mrp'];
				if($q<=0)
					break;
			}
			$prods[$i]['location']=$loc=implode(", ",array_unique($locations));
			$prods[$i]['mrp']=implode(", ",array_unique($mrps));
			$assoc_loc=$loc.substr($p['product'],0,10).rand(110,9999);
			$ret[$assoc_loc]=$prods[$i];
		}
		ksort($ret);
		return $ret;
	}
	
	function getbatchs_date_range($s,$e)
	{
		if(!$s)
		{
			$s=date("Y-m-d",mktime(0,0,0,date("m"),1));
			$e=date("Y-m-d",mktime(0,0,0,date("m")+1,date("t")));
		}
		$sql="select * from shipment_batch_process where created_on between ? and ? order by batch_id desc";
		return $this->db->query($sql,array($s,$e))->result_array();
	}
	
	function getbatchs()
	{
		$sql="select * from shipment_batch_process order by batch_id desc";
		return $this->db->query($sql)->result_array();
	}
	
	function getpendingbatchs()
	{
		$sql="select * from shipment_batch_process where status=0 or status=1 order by batch_id asc";
		return $this->db->query($sql)->result_array();
	}
	
	function getpartialshipments($s,$e)
	{
		$trans=array();
		$itemids=array();
		$raw_trans=$this->db->query("select o.* from king_transactions t join king_orders o on o.transid=t.transid and o.status=0 where t.batch_enabled=1 and t.init between ? and ? order by t.priority desc, t.init asc",array($s,$e))->result_array();
		foreach($raw_trans as $t)
		{
			$transid=$t['transid'];
			if(!isset($trans[$transid]))
				$trans[$transid]=array();
			$trans[$transid][]=$t;
			$itemids[]=$t['itemid'];
		}
		if(empty($trans))
			return array();
		$itemids=array_unique($itemids);
		$raw_prods=$this->db->query("select itemid,qty,product_id from m_product_deal_link where itemid in ('".implode("','",$itemids)."')")->result_array();
		$products=array();
		$productids=array();
		$partials=array();
		foreach($raw_prods as $p)
		{
			$itemid=$p['itemid'];
			if(!isset($products[$itemid]))
				$products[$itemid]=array();
			$products[$itemid][]=$p;
			$productids[]=$p['product_id'];
		}
		$productids=array_unique($productids);
		$raw_stock=$this->db->query("select product_id,sum(available_qty) as stock from t_stock_info where product_id in ('".implode("','",$productids)."') group by product_id")->result_array();
		$stock=array();
		foreach($productids as $p)
			$stock[$p]=0;
		foreach($raw_stock as $s)
		{
			$pid=$s['product_id'];
			$stock[$pid]=$s['stock'];
		}
		foreach($trans as $transid=>$orders)
		{
			$total_pending[$transid]=count($orders);
			$possible[$transid]=0;
			$not_partial_flag=true;
			foreach($orders as $order)
			{
				$itemid=$order['itemid'];
				$pflag=true;
				
				if(!isset($products[$itemid]))
						continue ;
						
				foreach($products[$itemid] as $p)
					if($stock[$p['product_id']]<$p['qty']*$order['quantity'])
					{
						$pflag=false;
						break;
					}
				if($pflag)
					$possible[$transid]++;
				else
					$not_partial_flag=false;
			}
			if($not_partial_flag)
			{
				foreach($orders as $order)
				{
					if(!isset($products[$order['itemid']]))
						continue ;
					foreach($products[$order['itemid']] as $p)
						$stock[$p['product_id']]-=$p['qty']*$order['quantity'];
				}
			}else
				$partials[]=$transid;
		}
		if(empty($partials))
			return array();
		$ret=$this->db->query("select count(o.status) as items,u.name,t.is_pnh,t.batch_enabled,o.userid,t.priority,o.transid,t.init,o.ship_city,o.status,o.ship_phone,o.actiontime from king_orders o join king_users u on u.userid=o.userid join king_transactions t on t.transid=o.transid where o.transid in ('".implode("','",$partials)."') group by o.transid order by t.init asc")->result_array();
		foreach($ret as $i=>$r)
		{
			$ret[$i]['pending']=$total_pending[$r['transid']];
			$ret[$i]['possible']=$possible[$r['transid']];
		}
		return $ret;
	}
	
	function getdisabledbutpossibleshipments()
	{
		$trans=array();
		$itemids=array();
		$raw_trans=$this->db->query("select o.* from king_transactions t join king_orders o on o.transid=t.transid and o.status=0 where t.batch_enabled=1 order by t.priority desc, t.init asc")->result_array();
		foreach($raw_trans as $t)
		{
			$transid=$t['transid'];
			if(!isset($trans[$transid]))
				$trans[$transid]=array();
			$trans[$transid][]=$t;
			$itemids[]=$t['itemid'];
		}
		if(!empty($trans))
		{
			$itemids=array_unique($itemids);
			$raw_prods=$this->db->query("select itemid,qty,product_id from m_product_deal_link where itemid in ('".implode("','",$itemids)."')")->result_array();
			$products=array();
			$productids=array();
			foreach($raw_prods as $p)
			{
				$itemid=$p['itemid'];
				if(!isset($products[$itemid]))
					$products[$itemid]=array();
				$products[$itemid][]=$p;
				$productids[]=$p['product_id'];
			}
			$productids=array_unique($productids);
			$raw_stock=$this->db->query("select product_id,sum(available_qty) as stock from t_stock_info where product_id in ('".implode("','",$productids)."') group by product_id")->result_array();
			$stock=array();
			foreach($productids as $p)
				$stock[$p]=0;
			foreach($raw_stock as $s)
			{
				$pid=$s['product_id'];
				$stock[$pid]=$s['stock'];
			}
			foreach($trans as $transid=>$orders)
			{
				$total_pending[$transid]=count($orders);
				$possible[$transid]=0;
				$not_partial_flag=true;
				foreach($orders as $order)
				{
					$itemid=$order['itemid'];
					$pflag=true;
					foreach($products[$itemid] as $p)
						if($stock[$p['product_id']]<$p['qty']*$order['quantity'])
						{
							$pflag=false;
							break;
						}
					if(!$pflag)
						$not_partial_flag=false;
				}
				if($not_partial_flag)
				{
					foreach($orders as $order)
						foreach($products[$order['itemid']] as $p)
							$stock[$p['product_id']]-=$p['qty']*$order['quantity'];
				}
			}
			$o_stock=$stock;
		}
		
		$trans=array();
		$itemids=array();
		$raw_trans=$this->db->query("select o.* from king_transactions t join king_orders o on o.transid=t.transid and o.status=0 where t.batch_enabled=0 order by t.priority desc, t.init asc")->result_array();
		foreach($raw_trans as $t)
		{
			$transid=$t['transid'];
			if(!isset($trans[$transid]))
				$trans[$transid]=array();
			$trans[$transid][]=$t;
			$itemids[]=$t['itemid'];
		}
		if(empty($trans))
			return array();
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
		$raw_stock=$this->db->query("select product_id,sum(available_qty) as stock from t_stock_info where product_id in ('".implode("','",$productids)."') group by product_id")->result_array();
		$stock=array();
		foreach($productids as $p)
			$stock[$p]=0;
		foreach($raw_stock as $s)
		{
			$pid=$s['product_id'];
			$stock[$pid]=$s['stock'];
			if(isset($o_stock[$pid]))
				$stock[$pid]=$o_stock[$pid];
		}
		foreach($trans as $transid=>$orders)
		{
			$total_pending[$transid]=count($orders);
			$possible[$transid]=0;
			$not_partial_flag=true;
			foreach($orders as $order)
			{
				$itemid=$order['itemid'];
				$pflag=true;
				foreach($products[$itemid] as $p)
					if($stock[$p['product_id']]<$p['qty']*$order['quantity'])
					{
						$pflag=false;
						break;
					}
				if($pflag)
					$possible[$transid]++;
				else
					$not_partial_flag=false;
			}
			if($not_partial_flag)
			{
				foreach($orders as $order)
					foreach($products[$order['itemid']] as $p)
						$stock[$p['product_id']]-=$p['qty']*$order['quantity'];
				$not_partials[]=$transid;
			}else
				$partials[]=$transid;
		}
		
		
		
		if(empty($not_partials))
			return array();
		$ret=$this->db->query("select count(o.status) as items,u.name,t.is_pnh,t.batch_enabled,o.userid,t.priority,o.transid,t.init,o.ship_city,o.status,o.ship_phone,o.actiontime from king_orders o join king_users u on u.userid=o.userid join king_transactions t on t.transid=o.transid where o.transid in ('".implode("','",$not_partials)."') group by o.transid order by t.init asc")->result_array();
		foreach($ret as $i=>$r)
		{
			$ret[$i]['pending']=$total_pending[$r['transid']];
			$ret[$i]['possible']=$possible[$r['transid']];
		}
		return $ret;
	}
	
	function depr_getpartialshipments()
	{
		$trans=array();
		$raw_trans=$this->db->query("select t.transid from king_transactions t join king_orders o on o.transid=t.transid and o.status=0 where t.batch_enabled=1 group by t.transid order by t.priority desc, t.init asc")->result_array();
//		$raw_trans=$this->db->query("select t.transid from king_transactions t join king_orders o on o.transid=t.transid and o.status=0 join m_courier_pincodes mp on mp.pincode=o.ship_pincode or mp.pincode='999999' join m_courier_awb_series ma on ma.awb_end_no>ma.awb_current_no and ma.courier_id=mp.courier_id group by t.transid order by t.priority desc, t.init asc")->result_array();
		if(empty($raw_trans))
			return array();
		foreach($raw_trans as $t)
		{
//			$flag=false;
//			$o=$this->db->query("select m.courier_id,o.ship_pincode,m.awb_end_no,m.awb_current_no from king_orders o join m_courier_pincodes p on p.pincode=o.ship_pincode or p.pincode='999999' join m_courier_awb_series m on m.courier_id=p.courier_id where o.transid=? and o.status=0 order by p.pincode asc limit 1",$t['transid'])->row_array();
//			if(!isset($awbs[$o['courier_id']]))
//				$awbs[$o['courier_id']]=$o['awb_current_no'];
//			if($awbs[$o['courier_id']]<=$o['awb_end_no'])
//			{
				$trans[]=$t['transid'];
//				$awbs[$o['courier_id']]++;
//			}
		}
		if(empty($trans))
			return array();
		$porders=$this->db->query("select o.* from king_orders o join king_transactions t on t.transid=o.transid where o.status=0 and o.transid in ('".implode("','",$trans)."') order by t.priority desc, o.sno asc")->result_array();
		$items=array();
		foreach($porders as $o)
		{
			if(!in_array($o['itemid'],$items))
				$items[]=$o['itemid'];
		}
		foreach($items as $item)
		{
			$ps=$this->db->query("select itemid,product_id,qty from m_product_deal_link where itemid=?",$item)->result_array();
			$prods[$item]=array();
			foreach($ps as $p)
			{
				$prods[$p['itemid']][]=$p;
				$pids[]=$p['product_id'];
			}
		}
		$raw_stock=$this->db->query("select product_id,sum(available_qty) as s from t_stock_info where product_id in ('".implode("','",$pids)."') group by product_id")->result_array();
		foreach($pids as $i)
		{
			$stock[$i]=0;
			$t_stock[$i]=0;
		}
		foreach($raw_stock as $s)
		{
			$stock[$s['product_id']]=$s['s'];
			$t_stock[$s['product_id']]=$s['s'];
		}
		$orders=array();
		foreach($porders as $o)
		{
			$itemid=$o['itemid'];
			$f=true;
			foreach($prods[$itemid] as $p)
				if($stock[$p['product_id']]<$p['qty']*$o['quantity'])
				{
					$f=false;
					break;
				}
			if(!$f)
				continue;
			foreach($prods[$itemid] as $p)
				$stock[$p['product_id']]-=$p['qty']*$o['quantity'];
			$orders[]=$o['id'];
		}
		$raw_trans=$this->db->query("select o.*,t.batch_enabled,u.name,t.priority,t.init,count(o.id) as possible from king_orders o join king_transactions t on t.transid=o.transid join king_users u on u.userid=o.userid where o.id in ('".implode("','",$orders)."') group by o.transid having count(o.transid)!=(select count(transid) from king_orders where transid=o.transid and status=0)")->result_array();
		$zero_possible=$this->db->query("select o.*,t.batch_enabled,t.priority,u.name,t.init,0 as possible from king_orders o join king_transactions t on t.transid=o.transid join king_users u on u.userid=o.userid where o.id not in ('".implode("','",$orders)."') and t.batch_enabled=1 and o.status=0 group by o.transid having count(o.transid)=(select count(transid) from king_orders where transid=o.transid and status=0)")->result_array();
		foreach($zero_possible as $t)
			$raw_trans[]=$t;
		$rtrans=array();
		foreach($raw_trans as $i=>$r)
		{
			$raw_trans[$i]['items']=$this->db->query("select count(1) as l from king_orders where transid=?",$r['transid'])->row()->l;
			$raw_trans[$i]['pending']=$this->db->query("select count(1) as l from king_orders where transid=? and status=0",$r['transid'])->row()->l;
			$rtrans[]=$r['transid'];
		}
		$rtrans=$this->db->query("select transid from king_transactions where transid in ('".implode("','",$rtrans)."') order by init asc")->result_array();
		$out=array();
		foreach($rtrans as $t)
		{
			$transid=$t['transid'];
			foreach($raw_trans as $r)
				if($r['transid']==$transid)
					break;
			$out[]=$r;
		}
		return $out;
	}
	
	function depr_getdisabledbutpossibleshipments()
	{
		$trans=array();
		$raw_trans=$this->db->query("select t.transid from king_transactions t join king_orders o on o.transid=t.transid and o.status=0 where t.batch_enabled=0  group by t.transid order by t.priority desc, t.init asc")->result_array();
//		$raw_trans=$this->db->query("select t.transid from king_transactions t join king_orders o on o.transid=t.transid and o.status=0 join m_courier_pincodes mp on mp.pincode=o.ship_pincode or mp.pincode='999999' join m_courier_awb_series ma on ma.awb_end_no>ma.awb_current_no and ma.courier_id=mp.courier_id group by t.transid order by t.priority desc, t.init asc")->result_array();
		if(empty($raw_trans))
			return array();
		foreach($raw_trans as $t)
		{
//			$flag=false;
//			$o=$this->db->query("select m.courier_id,o.ship_pincode,m.awb_end_no,m.awb_current_no from king_orders o join m_courier_pincodes p on p.pincode=o.ship_pincode or p.pincode='999999' join m_courier_awb_series m on m.courier_id=p.courier_id where o.transid=? and o.status=0 order by p.pincode asc limit 1",$t['transid'])->row_array();
//			if(!isset($awbs[$o['courier_id']]))
//				$awbs[$o['courier_id']]=$o['awb_current_no'];
//			if($awbs[$o['courier_id']]<=$o['awb_end_no'])
//			{
				$trans[]=$t['transid'];
//				$awbs[$o['courier_id']]++;
//			}
		}
		if(empty($trans))
			return array();
		$porders=$this->db->query("select o.* from king_orders o join king_transactions t on t.transid=o.transid where o.status=0 and o.transid in ('".implode("','",$trans)."') order by t.priority desc, t.init asc")->result_array();
		$items=array();
		foreach($porders as $o)
		{
			if(!in_array($o['itemid'],$items))
				$items[]=$o['itemid'];
		}
		foreach($items as $item)
		{
			$ps=$this->db->query("select itemid,product_id,qty from m_product_deal_link where itemid=?",$item)->result_array();
			$prods[$item]=array();
			foreach($ps as $p)
			{
				$prods[$p['itemid']][]=$p;
				$pids[]=$p['product_id'];
			}
		}
		$raw_stock=$this->db->query("select product_id,sum(available_qty) as s from t_stock_info where product_id in ('".implode("','",$pids)."') group by product_id")->result_array();
		foreach($pids as $i)
			$stock[$i]=0;
		foreach($raw_stock as $s)
			$stock[$s['product_id']]=$s['s'];
		$orders=array();
		foreach($porders as $o)
		{
			$itemid=$o['itemid'];
			echo "{$o['transid']}:$itemid:";
			$f=true;
			foreach($prods[$itemid] as $p)
				if($stock[$p['product_id']]<$p['qty']*$o['quantity'])
				{
					$f=false;
					break;
				}
			echo (($f?"available":"false")."\n");
			if(!$f)
				continue;
			foreach($prods[$itemid] as $p)
				$stock[$p['product_id']]-=$p['qty']*$o['quantity'];
			$orders[]=$o['id'];
		}
		$raw_trans=$this->db->query("select o.*,t.batch_enabled,u.name,t.priority,t.init,count(o.id) as possible from king_orders o join king_transactions t on t.transid=o.transid join king_users u on u.userid=o.userid where o.id in ('".implode("','",$orders)."') group by o.transid having count(o.transid)=(select count(transid) from king_orders where transid=o.transid and status=0)")->result_array();
		$rtrans=array();
		foreach($raw_trans as $i=>$r)
		{
			$raw_trans[$i]['items']=$this->db->query("select count(1) as l from king_orders where transid=?",$r['transid'])->row()->l;
			$raw_trans[$i]['pending']=$this->db->query("select count(1) as l from king_orders where transid=? and status=0",$r['transid'])->row()->l;
			$rtrans[]=$r['transid'];
		}
		$rtrans=$this->db->query("select transid from king_transactions where transid in ('".implode("','",$rtrans)."') order by init asc")->result_array();
		$out=array();
		foreach($rtrans as $t)
		{
			$transid=$t['transid'];
			foreach($raw_trans as $r)
				if($r['transid']==$transid)
					break;
			$out[]=$r;
		}
		return $out;
	}
	
	function getordersfortransid($transid)
	{
		$sql="select o.*,p.name as deal,p.dealid,p.url,u.name as username from king_orders o join king_dealitems p on p.id=o.itemid left outer join king_users u on u.userid=o.userid where o.transid=?";
		$ret=$this->db->query($sql,$transid)->result_array();
		return $ret;
	}
	
	function getfreesamplesfortransaction($transid)
	{
		return $this->db->query("select f.name from king_freesamples_order o join king_freesamples f on f.id=o.fsid where o.transid=?",$transid)->result_array();
	}
	
	function getordersbytransaction_date_range($status,$s=false,$e=false,$pg=0,$limit=50,$orders_by='')
	{
		if($s)
		{
			$s=strtotime($s.' 00:00:00');
			$e=strtotime($e.' 23:59:59');
		}
		$cond = '';
		
		$orders_by = strtoupper($orders_by);
		
		if($orders_by)
			if($orders_by == 'PNH' )
			{
				$cond = ' and t.is_pnh = 1 ';
			}else if($orders_by == 'SNP' )
			{
				$cond = ' and t.is_pnh = 0 and t.partner_id = 0 ';
			}else 
			{
				$partner_id = @$this->db->query("select id from partner_info where trans_prefix = ? ",$orders_by)->row()->id;
				if($partner_id)
					$cond = ' and t.is_pnh = 0 and t.partner_id = '.$partner_id;
			}
		
		if($pg == -1)
		{
			$sql="select count(1) as t  
							from king_transactions t join king_orders o on o.transid=t.transid left outer join king_users u on o.userid=u.userid 
							where 1";
			if($s)
				$sql.="	and o.time between ? and ?";
			if($status==1)
				$sql.=" and o.status=0";
				
			$sql .= $cond; 	
			$sql.=" group by t.transid ";
			
			return $this->db->query("$sql",array($s,$e))->num_rows();
		}else
		{
		 
			$sql="select t.is_pnh,t.priority,t.batch_enabled,t.transid,o.ship_phone,o.ship_city,o.status,t.amount,o.actiontime,count(o.itemid) as items,u.name,u.userid,t.init,t.amount,t.priority 
							from king_transactions t join king_orders o on o.transid=t.transid left outer join king_users u on o.userid=u.userid 
							where 1";
			if($s)
				$sql.="	and o.time between ? and ?";
			if($status==1)
				$sql.=" and o.status=0";
				
			$sql .= $cond;
				
			$sql.=" group by t.transid order by";
			if($status==0)
				$sql.=" t.id desc,o.status desc";
			else
				$sql.=" t.id asc";
			$sql.=" limit $pg,$limit";
			return $this->db->query("$sql",array($s,$e))->result_array();
		}
			
		
	}

	/**
	 * function to populate pending orders summary    
	 * @param unknown_type $status
	 * @param unknown_type $s
	 * @param unknown_type $e
	 */
	function getordersby($status,$s=false,$e=false)
	{
		if($s)
		{
			$s=strtotime($s);
			$e=strtotime($e);
		}
		
		$cond = '';
		if($s)
			$cond.="	and o.time between ? and ?";
			
		return $this->db->query("select count(*) as total,is_pnh,ifnull((partner_id/partner_id),0) as is_partner 
				from king_transactions a 
				join king_orders b on a.transid = b.transid 
				where b.status = 0  $cond 
				group by is_pnh,is_partner  ",array($s,$e))->result_array();
	}
	
	function getordersbytransaction()
	{
		return $this->db->query("select t.transid,o.ship_city,t.amount,o.actiontime,count(o.itemid) as items,u.name,u.userid,t.init,t.amount,t.priority from king_transactions t join king_orders o on o.transid=t.transid join king_users u on o.userid=u.userid group by t.transid order by o.sno desc limit 20")->result_array();
	}
	
	function gettranschangelog($transid)
	{
		return $this->db->query("select c.*,u.name as admin from transactions_changelog c join king_admin u on u.id=c.admin where c.transid=? order by c.time desc",$transid)->result_array();
	}
	
	function do_changetranshipaddr($transid)
	{
		$o=$this->db->query("select ship_city,ship_address,ship_phone,ship_landmark,ship_state,ship_pincode from king_orders where transid=?",$transid)->row_array();
		$msg="shipping address changed<br><br>Old address:<br>{$o['ship_address']},<br>{$o['ship_landmark']},<br>{$o['ship_city']},<br>{$o['ship_state']},{$o['ship_phone']}<br>{$o['ship_pincode']}<br>";
		foreach(array("address","landmark","city","state","pincode","phone") as $i)
			$$i=$this->input->post("$i");
		$this->db->query("update king_orders set ship_address=?,ship_landmark=?,ship_city=?,ship_state=?,ship_pincode=?,ship_phone=? where transid=?",array($address,$landmark,$city,$state,$pincode,$phone,$transid));
		$this->erpm->do_trans_changelog($transid,$msg);
	}
	
	function do_changetranbilladdr($transid)
	{
		$o=$this->db->query("select bill_city,bill_address,bill_phone,bill_landmark,bill_state,bill_pincode from king_orders where transid=?",$transid)->row_array();
		$msg="billping address changed<br><br>Old address:<br>{$o['bill_address']},<br>{$o['bill_landmark']},<br>{$o['bill_city']},<br>{$o['bill_state']},{$o['bill_phone']}<br>{$o['bill_pincode']}<br>";
		foreach(array("address","landmark","city","state","pincode","phone") as $i)
			$$i=$this->input->post("$i");
		$this->db->query("update king_orders set bill_address=?,bill_landmark=?,bill_city=?,bill_state=?,bill_pincode=?,bill_phone=? where transid=?",array($address,$landmark,$city,$state,$pincode,$phone,$transid));
		$this->erpm->do_trans_changelog($transid,$msg);
	}
	
	function do_addclientorder($cid)
	{
		$admin=$this->erpm->getadminuser();
		foreach(array("product","mrp","qty","ref","remarks") as $i)
			$$i=$this->input->post($i);
		$inp=array($cid,$ref,$remarks,$admin['userid']);
		$this->db->query("insert into t_client_order_info(client_id,order_reference_no,remarks,order_status,created_on,created_by) values(?,?,?,0,now(),?)",$inp);
		$oid=$this->db->insert_id();
		foreach($product as $i=>$p)
			$this->db->query("insert into t_client_order_product_info(order_id,product_id,mrp,order_qty,created_on,created_by) values(?,?,?,?,now(),?)",array($oid,$p,$mrp[$i],$qty[$i],$admin['userid']));
		$this->session->set_flashdata("erp_pop_info","Client order created");
		redirect("admin/client_order/$oid");
	}
	
	function do_trans_changelog($transid,$msg,$uid=null)
	{
		if($uid==null)
			$user=$this->erpm->getadminuser();
		else $user['userid']=$uid;
		$this->db->query("insert into transactions_changelog(transid,msg,admin,time) values(?,?,?,?)",array($transid,$msg,$user['userid'],time()));
	}
	
	function getbatchesstatusfortransid($transid)
	{
		return $this->db->query("select c.courier_name as courier,bi.shipped,bi.shipped_on,bi.awb,bi.courier_id,bi.batch_id,bi.packed,bi.shipped,i.createdon,i.invoice_status,i.invoice_no,bi.p_invoice_no from king_invoice i left outer join shipment_batch_process_invoice_link bi on bi.invoice_no=i.invoice_no left outer join m_courier_info c on c.courier_id=bi.courier_id where i.transid=? group by i.invoice_no",$transid)->result_array();
	}
	
	function gettransaction($transid)
	{
		return $this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
	}
	
	function getticketmsgs($tid)
	{
		return $this->db->query("select a.name as admin_user,m.* from support_tickets_msg m left outer join king_admin a on a.id=m.support_user where m.ticket_id=? order by m.id desc",$tid)->result_array();
	}
	
	function addnotesticket($tid,$type,$medium,$msg,$from_customer=0)
	{
		$user=$this->erpm->getadminuser();
		if(empty($user))
			$user['userid']=0;
		$this->db->query("insert into support_tickets_msg(ticket_id,msg_type,msg,medium,from_customer,support_user,created_on) values(?,?,?,?,?,?,now())",array($tid,$type,$msg,$medium,$from_customer,$user['userid']));
		$this->db->query("update support_tickets set updated_on=now() where ticket_id=? limit 1",$tid);
		return $this->db->insert_id();
	}
	
	function getvariants()
	{
		$sql="select * from variant_info order by variant_id desc";
		return $this->db->query($sql)->result_array();
	}
	
	function getdealitemsforvariant($vid)
	{
		return $this->db->query("select i.dealid,i.url,i.name,v.variant_value from variant_deal_link v join king_dealitems i on i.id=v.item_id where v.variant_id=?",$vid)->result_array();
	}
	
	function do_add_products_group()
	{
		$user=$this->auth();
		foreach(array("group_name","attr_name","attr_values","pids","catid") as $i)
			$$i=$this->input->post($i);
		$this->db->insert("products_group",array("group_name"=>$group_name,"cat_id"=>$catid,"created_by"=>$user['userid'],"created_on"=>time()));
		$gid=$this->db->insert_id();
		$attr_data=array();
		foreach($attr_name as $i=>$aname)
		{
			$inp=array("group_id"=>$gid,"attribute_name"=>$aname);
			$this->db->insert("products_group_attributes",$inp);
			$aid=$this->db->insert_id();
			$attr_data[$aid]=array();
			foreach($attr_values[$i] as $avalue)
			{
				$this->db->insert("products_group_attribute_values",array("group_id"=>$gid,"attribute_name_id"=>$aid,"attribute_value"=>$avalue));
				$attr_data[$aid][]=$this->db->insert_id();
			}
		}
		foreach($pids as $i=>$pid)
		{
			foreach($this->input->post("attr_$pid") as $i2=>$v)
			{
				$i3=0;
				foreach($attr_data as $aid=>$adata)
				{
					if($i3==$i2)
						break;
					$i3++;
				}
				$inp=array("product_id"=>$pid,"group_id"=>$gid,"attribute_name_id"=>$aid,"attribute_value_id"=>$adata[$v]);
				$this->db->insert("products_group_pids",$inp);
			}
		}
		redirect("admin/product_group/$gid");
	}
	
	function do_changestatusticket($tid)
	{
		$statuss=array("Unassigned","Opened","In Progress","Closed");
		$user=$this->erpm->getadminuser();
		$s=$_POST['tck_status'];
		$this->erpm->addnotesticket($tid,0,0,"Status changed to <b>{$statuss[$s]}</b>");
		$this->db->query("update support_tickets set status=? where ticket_id=?",array($s,$tid));
	}
	
	function do_changepriorityticket($tid)
	{
		$prioritys=array("Low","Medium","High","Urgent");
		$user=$this->erpm->getadminuser();
		$s=$_POST['priority'];
		$this->erpm->addnotesticket($tid,0,0,"<b>{$prioritys[$s]}</b> Priority Assigned");
		$this->db->query("update support_tickets set priority=? where ticket_id=?",array($s,$tid));
	}
	
	function do_changetypeticket($tid)
	{
		$statuss=array("Query","Order Issue","Bug","Suggestion","Common","PNH Returns","Courier Followups");
		$user=$this->erpm->getadminuser();
		$s=$_POST['type'];
		$this->erpm->addnotesticket($tid,0,0,"Ticket type changed to <b>{$statuss[$s]}</b>");
		$this->db->query("update support_tickets set type=? where ticket_id=?",array($s,$tid));
	}
	
	
	function do_addnotesticket($tid)
	{
		$user=$this->erpm->getadminuser();
		$type=$this->input->post("type");
		$msg=nl2br($this->input->post("msg"));
		$medium=$this->input->post("medium");
		if($medium==0 && $type==1)
		{
			$prev=$this->db->query("select msg from support_tickets_msg where ticket_id=? and medium=0 and msg_type=1 and from_customer=1 order by id desc",$tid)->row_array();
			if(!empty($prev))
				$msg.='<div style="margin-top:25px;border:1px solid #999;"><div style="background:#eee;padding:3px;font-size:13px;font-weight:bold;">Incident report</div><div style="padding:5px">'.$prev['msg']."</div></div>";
			$ticket=$this->db->query("select * from support_tickets where ticket_id=?",$tid)->row_array();
			$email=$ticket['email'];
			if(!empty($email))
				$this->vkm->email($email,"[TK{$ticket['ticket_no']}] Snapittoday Customer Support",$msg);
		}
		$this->erpm->addnotesticket($tid,$type,$medium,$msg,0);
		$this->session->set_flashdata("erp_pop_info","Notes added");
		redirect("admin/ticket/$tid");
	}
	
	function do_ticket()
	{
		$user=$this->erpm->getadminuser();
		foreach(array("name","mobile","msg","email","transid","type","priority") as $i)
			$$i=$this->input->post("$i");
		$no=rand(1000000000,9999999999);
		$userid=0;
		$u=array();
		if(!empty($email))
			$u=$this->db->query("select userid,mobile from king_users where email=?",$email)->row_array();
		if(!empty($u))
		{
			$userid=$u['userid'];
			if(empty($mobile))
				$mobile=$u['mobile'];
		}
		$msg=nl2br($msg);
		$this->db->query("insert into support_tickets(ticket_no,name,mobile,user_id,email,transid,type,priority,created_on) values(?,?,?,?,?,?,?,?,now())",array($no,$name,$mobile,$userid,$email,$transid,$type,$priority));
		$tid=$this->db->insert_id();
		$this->erpm->addnotesticket($tid,1,1,$msg,1);
		$this->session->set_flashdata("erp_pop_info","New ticket created");
		redirect("admin/ticket/{$tid}");
	}
	
	function gettickets($filter,$s=false,$e=false)
	{
		$filters=array("all"=>-1,"unassigned"=>0,"opened"=>1,"inprogress"=>2,"closed"=>3);
		if(!isset($filters[$filter]))
			return array();
//		if(!$e)
//		{
//			$tom=mktime(0,0,0,date("m"),date("d")+1);
//			$s=date("Y-m-d",time()-(30*24*60*60));
//			$e=date("Y-m-d",time());
//		}
		$filter=$filters[$filter];
		$sql="select u.name as user,t.*,a.name as assignedto from support_tickets t left outer join king_admin a on a.id=t.assigned_to left outer join king_users u on u.userid=t.user_id where";
		if($filter!=-1)
			$sql.=" t.status=$filter and";
		if($e)
		$sql.=" t.created_on between ? and ?";
		else
			$sql.=" 1";
		$sql.=" order by t.updated_on desc, t.created_on desc";
		if(!$e)
			$sql.=" limit 30";
		return $this->db->query($sql,array($s,$e))->result_array();
	}
	
	function getticketsfortrans($transid)
	{
		$sql="select u.name as user,t.*,a.name as assignedto from support_tickets t left outer join king_admin a on a.id=t.assigned_to left outer join king_users u on u.userid=t.user_id";
		$sql.=" where transid=?";
		$sql.=" order by t.created_on desc";
		return $this->db->query($sql,$transid)->result_array();
	}
	
	function getpendinggrns()
	{
		$grns=$this->db->query("select v.vendor_name as vendor,g.* from t_grn_info g join m_vendor_info v on v.vendor_id=g.vendor_id where payment_status=0")->result_array();
		$ids=array();
		foreach($grns as $i=>$g)
			$ids[]=$g['grn_id'];
		if(empty($ids))
			return $grns;
		$pos=$this->db->query("select po.po_status,gp.po_id,gp.grn_id from t_grn_info g join t_grn_product_link gp on gp.grn_id=g.grn_id join t_po_info po on po.po_id=gp.po_id where g.grn_id in (".implode(",",$ids).")")->result_array();
		foreach($pos as $p)
		{
			if(!isset($gpos[$p['grn_id']]))
				$gpos[$p['grn_id']]=$gposdata[$p['grn_id']]=array();
			if(!in_array($p['po_id'],$gpos[$p['grn_id']]))
			{
				$gpos[$p['grn_id']][]=$p['po_id'];
				$gposdata[$p['grn_id']][]=$p;
			}
		}
		$tgrns=$grns;
		$grns=array();
		$gpos=$gposdata;
		foreach($tgrns as $i=>$g)
		{
			if(!isset($gpos[$g['grn_id']]))
				continue;
			
			$g['postatus']=1;
			foreach($gpos[$g['grn_id']] as $po)
				if($po['po_status']==0 || $po['po_status']==1)
					$g['postatus']=0;
			$g['pos']=$gpos[$g['grn_id']];
			$grns[]=$g;
		}
		return $grns;
	}
	
	function getpendingpaygrns()
	{
		$grns=$this->db->query("select * from t_grn_info where payment_status=1")->result_array();
		$ids=array();
		foreach($grns as $i=>$g)
			$ids[]=$g['grn_id'];
		if(empty($ids))
			return $grns;
		$pos=$this->db->query("select po.po_status,gp.po_id,gp.grn_id from t_grn_info g join t_grn_product_link gp on gp.grn_id=g.grn_id join t_po_info po on po.po_id=gp.po_id where g.grn_id in (".implode(",",$ids).")")->result_array();
		foreach($pos as $p)
		{
			if(!isset($gpos[$p['grn_id']]))
				$gpos[$p['grn_id']]=$gposdata[$p['grn_id']]=array();
			if(!in_array($p['po_id'],$gpos[$p['grn_id']]))
			{
				$gpos[$p['grn_id']][]=$p['po_id'];
				$gposdata[$p['grn_id']][]=$p;
			}
		}
		$tgrns=$grns;
		$grns=array();
		$gpos=$gposdata;
		foreach($tgrns as $i=>$g)
		{
			if(!isset($gpos[$g['grn_id']]))
				continue;
			
			$g['postatus']=1;
			foreach($gpos[$g['grn_id']] as $po)
				if($po['po_status']==0 || $po['po_status']==1)
					$g['postatus']=0;
			$g['pos']=$gpos[$g['grn_id']];
			$grns[]=$g;
		}
		return $grns;
	}
	
	function getrackkbinsforbrands()
	{
		return $this->db->query("select r.rack_name,r.bin_name,rb.brandid from m_rack_bin_brand_link rb join m_rack_bin_info r on r.id=rb.rack_bin_id order by r.rack_name")->result_array();
	}
	
	function do_addbrand()
	{
		foreach(array("name","rb") as $b)
			$$b=$this->input->post("$b");
		$bid=$this->adminmodel->genbrandid();
		$rb=array_unique($rb);
		$url=preg_replace('/[^a-zA-Z0-9_\-]/','',$name);
		$url=str_replace(" ","-",$url);
		$this->db->query("insert into king_brands(id,name,url) values(?,?,?)",array($bid,$name,$url));
		foreach($rb as $r)
			$this->db->query("insert into m_rack_bin_brand_link(rack_bin_id,brandid) values(?,?)",array($r,$bid));
		$this->session->set_flashdata("erp_pop_info","Brand added");
		redirect("admin/brands");
	}
	
	function do_editbrand($bid)
	{
		foreach(array("name","rb") as $b)
			$$b=$this->input->post("$b");
		$rb=array_unique($rb);
		$this->db->query("update king_brands set name=? where id=?",array($name,$bid));
		$this->db->query("delete from m_rack_bin_brand_link where brandid=?",$bid);
		foreach($rb as $r)
			$this->db->query("insert into m_rack_bin_brand_link(rack_bin_id,brandid) values(?,?)",array($r,$bid));
		$this->session->set_flashdata("erp_pop_info","Brand details updated");
		redirect("admin/viewbrand/$bid");
	}
	
	function getrackbinsforbrand($bid)
	{
		return $this->db->query("select rb.rack_bin_id,r.rack_name,r.bin_name,rb.brandid from m_rack_bin_brand_link rb join m_rack_bin_info r on r.id=rb.rack_bin_id where rb.brandid=? order by r.rack_name",$bid)->result_array();
	}
	
	function loadroles()
	{
		foreach($this->db->query("select const_name,value from user_access_roles order by id asc")->result_array() as $r)
			define(strtoupper($r['const_name']),(int)$r['value']);
	}
	
	function is_user_role($uid,$role)
	{
		$user=$this->db->query("select access from king_admin where id=?",$uid)->row_array();
		if(empty($user))
			return false;
		if(((int)$user['access']&(int)$role)>0)
			return true;
		return false;
	}
	
	function auth($super=false,$ret=false)
	{
		if(!defined("CALLCENTER_ROLE"))
			$this->erpm->loadroles();
		$user=$this->session->userdata("admin_user");
		if(!$user)
			redirect("admin");
		if($super===false)
			return $user;
		if($super===true)
			$super=ADMINISTRATOR_ROLE;
		if(((int)$user['access']&(int)$super)>0)
			return $user;
		if(((int)$user['access']&(int)ADMINISTRATOR_ROLE)>0)
			return $user;
		if($ret)
			return false;
		show_error("Access Denied<br><div style='font-size:14px;'>I reckon you don't have permission to do this</div>");
	}
	
	function do_voucher_exp()
	{
		foreach(array("bill","type","vvalue","mode","inst_no","inst_date","bank","narration") as $i)
			$$i=$this->input->post($i);
		$adminuser=$this->erpm->getadminuser();
		$inp=array(1,$vvalue,$mode,$inst_no,$inst_date,$bank,$narration,$adminuser['userid']);
		$this->db->query("insert into t_voucher_info(voucher_type_id,voucher_date,voucher_value,payment_mode,instrument_no,instrument_date,instrument_issued_bank,narration,created_on,created_by)
																	values(?,now(),?,?,?,?,?,?,now(),?)",$inp);
		$vid=$this->db->insert_id();
		$this->db->query("insert into t_voucher_expense_link(voucher_id,expense_type,bill_no) values(?,?,?)",array($vid,$type,$bill));	
		$this->session->set_flashdata("erp_pop_info","New expense voucher created");
		redirect("admin/voucher/$vid");
	}
	
	function do_voucher()
	{
		foreach(array("grns","adjusted_grn","pos","adjusted_po","vvalue","mode","inst_no","inst_date","bank","narration") as $i)
			$$i=$this->input->post($i);
		if(empty($grns) && empty($pos))
			show_error("No GRNs or POs specified");
		$adminuser=$this->erpm->getadminuser();
		$inp=array(1,$vvalue,$mode,$inst_no,$inst_date,$bank,$narration,$adminuser['userid']);
		$this->db->query("insert into t_voucher_info(voucher_type_id,voucher_date,voucher_value,payment_mode,instrument_no,instrument_date,instrument_issued_bank,narration,created_on,created_by)
																	values(?,now(),?,?,?,?,?,?,now(),?)",$inp);
		$vid=$this->db->insert_id();
		foreach($grns as $i=>$grn)
		{
			$this->db->query("insert into t_voucher_document_link(voucher_id,adjusted_amount,ref_doc_id,ref_doc_type,created_on)
																		values(?,?,?,?,now())",array($vid,$adjusted_grn[$i],$grn,1,now()));
			$this->db->query("update t_grn_info set payment_status=2 where grn_id=? limit 1",$grn);
		}
		foreach($pos as $i=>$po)
			$this->db->query("insert into t_voucher_document_link(voucher_id,adjusted_amount,ref_doc_id,ref_doc_type,created_on)
																		values(?,?,?,?,now())",array($vid,$adjusted_po[$i],$po,2,now()));
		$this->session->set_flashdata("erp_pop_info","New voucher created");
		redirect("admin/voucher/$vid");
	}
	
	function do_updatevendor($vid)
	{
		$admin = $this->auth(false);
		
		foreach(array("name","addr1","addr2","locality","landmark","city","state","country","postcode","ledger","credit_limit","credit_days","advance","cst","pan","vat","stax","tat","rpolicy","payterms","remarks","cnt_name","cnt_desgn","cnt_mob1","cnt_mob2","cnt_telephone","cnt_fax","cnt_email1","cnt_email2","l_brand","l_margin","l_from","l_until") as $i)
			$$i=$this->input->post("$i");
			
			
		$inp=array($name,$addr1,$addr2,$locality,$landmark,$postcode,$city,$state,$country,$ledger,$credit_limit,$credit_days,$advance,$cst,$pan,$vat,$stax,$tat,$rpolicy,$payterms,$remarks,$admin['userid'],$vid);
		$this->db->query("update m_vendor_info set vendor_name=?,address_line1=?,address_line2=?,locality=?,landmark=?,postcode=?,city_name=?,state_name=?,country=?,ledger_id=?,credit_limit_amount=?,credit_days=?,require_payment_advance=?,cst_no=?,pan_no=?,vat_no=?,service_tax_no=?,avg_tat=?,return_policy_msg=?,payment_terms_msg=?,remarks=?,modified_by=?,modified_on=now() where vendor_id=? limit 1",$inp);
		$this->db->query("delete from m_vendor_contacts_info where vendor_id=?",$vid);
		//$this->db->query("delete from m_vendor_brand_link where vendor_id=?",$vid);
		
		
		foreach($cnt_name as $i=>$cn)
		{
			$sql="insert into m_vendor_contacts_info(vendor_id,contact_name,contact_designation,mobile_no_1,mobile_no_2,telephone_no,email_id_1,email_id_2,fax_no,created_on)
														values(?,?,?,?,?,?,?,?,?,now())";
			$inp=array($vid,$cnt_name[$i],$cnt_desgn[$i],$cnt_mob1[$i],$cnt_mob2[$i],$cnt_telephone[$i],$cnt_email1[$i],$cnt_email2[$i],$cnt_fax[$i]);
			$this->db->query($sql,$inp);
		}
		if($l_brand)
		foreach($l_brand as $i=>$b)
		{
			if($this->db->query("select id from m_vendor_brand_link where vendor_id = ? and brand_id = ? ",array($b,$vid))->num_rows())
			{
				$this->db->query("update m_vendor_brand_link set brand_margin = ? ,applicable_from = ?,applicable_till = ?,created_on = now() where vendor_id = ? and brand_id = ? ",array($l_margin[$i],strtotime($l_from[$i]),strtotime($l_until[$i]),$vid,$b));
			}
			else
			{
				$this->db->query("insert into m_vendor_brand_link(brand_id,vendor_id,brand_margin,applicable_from,applicable_till,created_on) values(?,?,?,?,?,now())",array($b,$vid,$l_margin[$i],strtotime($l_from[$i]),strtotime($l_until[$i])));	
			}
			
		}
			
			
		redirect("admin/vendor/{$vid}");
	}
	
	function do_addvendor()
	{
		foreach(array("l_brand","l_margin","l_from","l_until","name","addr1","addr2","locality","landmark","city","state","country","postcode","ledger","credit_limit","credit_days","advance","cst","pan","vat","stax","tat","rpolicy","payterms","remarks","cnt_name","cnt_desgn","cnt_mob1","cnt_mob2","cnt_telephone","cnt_email1","cnt_email2","cnt_fax") as $i)
			$$i=$this->input->post($i);
		$code="V".rand(1000,9999);
		$inp=array($code,$name,$addr1,$addr2,$locality,$landmark,$postcode,$city,$state,$country,$ledger,$credit_limit,$credit_days,$advance,$cst,$pan,$vat,$stax,$tat,$rpolicy,$payterms,$remarks);
		$this->db->query("insert into m_vendor_info(vendor_code,vendor_name,address_line1,address_line2,locality,landmark,postcode,city_name,state_name,country,ledger_id,credit_limit_amount,credit_days,require_payment_advance,cst_no,pan_no,vat_no,service_tax_no,avg_tat,return_policy_msg,payment_terms_msg,remarks,created_on) 
														values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())",$inp);
		$vid=$this->db->insert_id();
		foreach($cnt_name as $i=>$cn)
		{
			$sql="insert into m_vendor_contacts_info(vendor_id,contact_name,contact_designation,mobile_no_1,mobile_no_2,telephone_no,email_id_1,email_id_2,fax_no,created_on)
														values(?,?,?,?,?,?,?,?,?,now())";
			$inp=array($vid,$cnt_name[$i],$cnt_desgn[$i],$cnt_mob1[$i],$cnt_mob2[$i],$cnt_telephone[$i],$cnt_email1[$i],$cnt_email2[$i],$cnt_fax[$i]);
			$this->db->query($sql,$inp);
		}
		foreach($l_brand as $i=>$b)
			$this->db->query("insert into m_vendor_brand_link(brand_id,vendor_id,brand_margin,applicable_from,applicable_till,created_on) values(?,?,?,?,?,now())",array($b,$vid,$l_margin[$i],strtotime($l_from[$i]),strtotime($l_until[$i])));
	}
	
	function do_grn()
	{
		$pos = array();
		$user=$this->erpm->getadminuser();
		$vid=$this->db->query("select vendor_id from t_po_info where po_id=?",$_POST['poids'][0])->row()->vendor_id;
		$this->db->query("insert into t_grn_info(vendor_id,remarks,created_on) values(?,?,now())",array($vid,$_POST['remarks']));
		$grn=$this->db->insert_id();
		foreach($_POST['poids'] as $p)
		{
			if(!isset($pos[$p]))
				$pos[$p]=array();
			if(isset($_POST["pid$p"]))	
			foreach($_POST["pid$p"] as $i=>$null)
			{
				$po=array();
				$po['product']=$_POST["pid$p"][$i];
				$poi=$this->db->query("select * from t_po_product_link where po_id=? and product_id=?",array($p,$po['product']))->row_array();
				$po['oqty']=$_POST["oqty$p"][$i];
				$po['rqty']=$_POST["rqty$p"][$i];
				$po['mrp']=$_POST["mrp$p"][$i];
				$po['price']=$_POST["price$p"][$i];
				$po['tax']=$this->db->query("select vat from m_product_info where product_id=?",$po['product'])->row()->vat;
				$po['rackbin']=$_POST['storage'.$p][$i];
				$po['location']=$this->db->query("select location_id from m_rack_bin_info where id=?",$po['rackbin'])->row()->location_id;
				$po['margin']=$poi['margin'];
				$po['foc']=$poi['is_foc'];
				$po['offer']=$poi['has_offer'];
				$po['barcode']=isset($_POST["pbarcode".$p][$i])?$_POST["pbarcode".$p][$i]:'';
				$po['upd_pmrp']=isset($_POST["upd_pmrp".$p][$i])?$_POST["upd_pmrp".$p][$i]:0;
				$pos[$p][]=$po;
			}
		}
		
		foreach($pos as $poid=>$po)
		{
			foreach($po as $p)
			{
				$inp=array($grn,$poid,$p['product'],$p['oqty'],$p['rqty'],$p['mrp'],$p['price'],$p['tax'],$p['location'],$p['rackbin'],$p['margin'],$p['foc'],$p['offer']);
				$this->db->query("insert into t_grn_product_link(grn_id,po_id,product_id,invoice_qty,received_qty,mrp,purchase_price,tax_percent,location_id,rack_bin_id,margin,is_foc,has_offer,created_on) 
																				values(?,?,?,?,?,?,?,?,?,?,?,?,?,now())",$inp);
				$this->db->query("update t_po_product_link set received_qty=received_qty+? where po_id=? and product_id=?",array($p['rqty'],$poid,$p['product']));
				if($p['rqty']!=0)
				{
					$p['product_id']=$p['product'];
					if($p['mrp']!=0)
						$pc_prod=$this->db->query("select * from m_product_info where product_id=? and mrp!=? ",array($p['product_id'],$p['mrp']))->row_array();
					else $pc_prod=array();
					
					// check if product barcode is changed
					if(!empty($pc_prod))
					{
						if($pc_prod['barcode'] != $p['barcode'])
						{
							$this->db->query("update m_product_info set barcode=? where product_id=? limit 1",array($p['barcode'],$p['product_id']));
						}
					} 
					
					
					
					
					
					if(!empty($pc_prod) && $p['upd_pmrp'] == 1 )
					{
						$pid=$p['product_id'];
						$inp=array("product_id"=>$p['product_id'],"new_mrp"=>$p['mrp'],"old_mrp"=>$pc_prod['mrp'],"reference_grn"=>$grn,"created_by"=>$user['userid'],"created_on"=>time());
						$this->db->insert("product_price_changelog",$inp);
						$this->db->query("update m_product_info set mrp=? where product_id=? limit 1",array($p['mrp'],$p['product_id']));
						foreach($this->db->query("select product_id from products_group_pids where group_id in (select group_id from products_group_pids where product_id=$pid) and product_id!=$pid")->result_array() as $pg)
						{
							$inp=array("product_id"=>$pg['product_id'],"new_mrp"=>$p['mrp'],"old_mrp"=>$this->db->query("select mrp from m_product_info where product_id=?",$pg['product_id'])->row()->mrp,"reference_grn"=>0,"created_by"=>$user['userid'],"created_on"=>time());
							$this->db->insert("product_price_changelog",$inp);
							$this->db->query("update m_product_info set mrp=? where product_id=? limit 1",array($p['mrp'],$pg['product_id']));
						}
						$r_itemids=$this->db->query("select itemid from m_product_deal_link where product_id=?",$p['product_id'])->result_array();
						 
						$r_itemids2=$this->db->query("select l.itemid from products_group_pids p join m_product_group_deal_link l on l.group_id=p.group_id where p.product_id=?",$p['product_id'])->result_array();
						
						$r_itemids_arr = array();
						if($r_itemids)
							foreach($r_itemids as $r_item_det)
							{
								if(!isset($r_itemids_arr[$r_item_det['itemid']]))
									$r_itemids_arr[$r_item_det['itemid']] = array();
									
								$r_itemids_arr[$r_item_det['itemid']] = $r_item_det; 
							}
						if($r_itemids2)
							foreach($r_itemids2 as $r_item_det)
							{
								if(!isset($r_itemids_arr[$r_item_det['itemid']]))
									$r_itemids_arr[$r_item_det['itemid']] = array();
									
								$r_itemids_arr[$r_item_det['itemid']] = $r_item_det; 
							}
						
						
						//$r_itemids=array_unique(array_merge($r_itemids,$r_itemids2));
						$r_itemids = array_values($r_itemids_arr);
						
						//print_r($r_itemids);
						
						foreach($r_itemids as $d)
						{
							$itemid=$d['itemid'];
							$item=$this->db->query("select orgprice,price from king_dealitems where id=?",$itemid)->row_array();
							$o_price=$item['price'];$o_mrp=$item['orgprice'];
							$n_mrp=$this->db->query("select sum(p.mrp*l.qty) as mrp from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$itemid)->row()->mrp+$this->db->query("select sum((select avg(mrp) from m_product_group_deal_link l join products_group_pids pg on pg.group_id=l.group_id join m_product_info p on p.product_id=pg.product_id where l.itemid=$itemid)*(select qty from m_product_group_deal_link where itemid=$itemid)) as mrp")->row()->mrp;
							$n_price=$item['price']/$o_mrp*$n_mrp;
							$inp=array("itemid"=>$itemid,"old_mrp"=>$o_mrp,"new_mrp"=>$n_mrp,"old_price"=>$o_price,"new_price"=>$n_price,"created_by"=>$user['userid'],"created_on"=>time(),"reference_grn"=>$grn);
							$this->db->insert("deal_price_changelog",$inp);
							$this->db->query("update king_dealitems set orgprice=?,price=? where id=? limit 1",array($n_mrp,$n_price,$itemid));
							if($this->db->query("select is_pnh as b from king_dealitems where id=?",$itemid)->row()->b)
							{
								$o_s_price=$this->db->query("select store_price from king_dealitems where id=?",$itemid)->row()->store_price;
								$n_s_price=$o_s_price/$o_mrp*$n_mrp;
								$this->db->query("update king_dealitems set store_price=? where id=? limit 1",array($n_s_price,$itemid));
								$o_n_price=$this->db->query("select nyp_price as p from king_dealitems where id=?",$itemid)->row()->p;
								$n_n_price=$o_n_price/$o_mrp*$n_mrp;
								$this->db->query("update king_dealitems set nyp_price=? where id=? limit 1",array($n_n_price,$itemid));
							}
							foreach($this->db->query("select * from partner_deal_prices where itemid=?",$itemid)->result_array() as $r)
							{
								$o_c_price=$r['customer_price'];
								$n_c_price=$o_c_price/$o_mrp*$n_mrp;
								$o_p_price=$r['partner_price'];
								$n_p_price=$o_p_price/$o_mrp*$n_mrp;
								$this->db->query("update partner_deal_prices set customer_price=?,partner_price=? where itemid=? and partner_id=?",array($n_c_price,$n_p_price,$itemid,$r['partner_id']));
							}
						}
					}
					
					
					
					if($this->db->query("select is_serial_required as s from m_product_info where product_id=?",$p['product_id'])->row()->s==1)
					{
						$imeis=explode(",",$this->input->post("imei{$p['product']}"));
						foreach($imeis as $imei)
						{
							$imei=trim($imei);
							if(empty($imei)) continue;
							$this->db->insert("t_imei_no",array('product_id'=>$p['product_id'],"imei_no"=>$imei,"grn_id"=>$grn,"created_on"=>time()));
						}
					}
					
					//check if stk prod without barcode available 
					$stk_prodchkdet_res = $this->db->query("select * from t_stock_info where product_id=? and location_id=? and rack_bin_id=? and mrp=? ",array($p['product'],$p['location'],$p['rackbin'],$p['mrp']));
					
					if(!$stk_prodchkdet_res->num_rows())
					{
						$this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,available_qty,product_barcode,created_on) values(?,?,?,?,?,?,now())",array($p['product'],$p['location'],$p['rackbin'],$p['mrp'],$p['rqty'],$p['barcode']));
					}
					else
					{
						$upd_stat = 0;
						$ins_stat = 0;
						$new_pbc = $p['barcode']; 
						foreach($stk_prodchkdet_res->result_array() as $stk_prod_row)
						{
							if($new_pbc)
							{
								if($new_pbc == $stk_prod_row['product_barcode'])
								{
									$upd_stat = 1;
								}else 
								{
									$ins_stat = 1;
								}
							}else {
								$upd_stat = 1;
							}
						}
						
						if($upd_stat)
						{
							if($p['barcode'])
								$this->db->query("update t_stock_info set product_barcode = ?,available_qty=available_qty+?,modified_on=now() where product_id=? and location_id=? and rack_bin_id=? and mrp=? and (product_barcode = ? or length(trim(product_barcode))=0)  limit 1",array($p['barcode'],$p['rqty'],$p['product'],$p['location'],$p['rackbin'],$p['mrp'],$p['barcode']));
							else 
								$this->db->query("update t_stock_info set available_qty=available_qty+?,modified_on=now() where product_id=? and location_id=? and rack_bin_id=? and mrp=?  limit 1",array($p['rqty'],$p['product'],$p['location'],$p['rackbin'],$p['mrp']));
						}
						
						if($ins_stat && !$upd_stat)
						{
							$this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,available_qty,product_barcode,created_on) values(?,?,?,?,?,?,now())",array($p['product'],$p['location'],$p['rackbin'],$p['mrp'],$p['rqty'],$p['barcode']));
						}
						
						//echo $upd_stat.'_'.$ins_stat;
						
						
						/*
						$stk_prodchkdet_res_1 = $this->db->query("select 1 from t_stock_info where product_id=? and location_id=? and rack_bin_id=? and mrp=? and (product_barcode = ? or length(trim(product_barcode))=0) ",array($p['product'],$p['location'],$p['rackbin'],$p['mrp'],$p['barcode']));
						
						if($stk_prodchkdet_res_1->num_rows())
						{
							$this->db->query("update t_stock_info set product_barcode = ?,available_qty=available_qty+?,modified_on=now() where product_id=? and location_id=? and rack_bin_id=? and mrp=? and (product_barcode = ? or length(trim(product_barcode))=0)  limit 1",array($p['barcode'],$p['rqty'],$p['product'],$p['location'],$p['rackbin'],$p['mrp'],$p['barcode']));
						}
						else
						{	
							$this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,available_qty,product_barcode,created_on) values(?,?,?,?,?,?,now())",array($p['product'],$p['location'],$p['rackbin'],$p['mrp'],$p['rqty'],$p['barcode']));
						}
						*/
					}
						
					$this->erpm->do_stock_log(1,$p['rqty'],$p['product'],$grn,false,false,false,$p['upd_pmrp']);
				}
			}
			
			
			
			$po_status=2;
			foreach($this->db->query("select * from t_po_product_link where po_id=?",$poid)->result_array() as $poi)
			{
				if($poi['order_qty']>$poi['received_qty'])
					$po_status=1;
				$this->db->query("update t_po_info set po_status=? where po_id=? limit 1",array($po_status,$poid));
			}
		}
		$invs=array();
		foreach($_POST['invno'] as $i=>$no)
		{
			$this->db->query("insert into t_grn_invoice_link(grn_id,purchase_inv_no,purchase_inv_date,purchase_inv_value,created_on) values(?,?,?,?,now())",array($grn,$no,$_POST['invdate'][$i],$_POST['invamount'][$i]));
			$invs[]=$this->db->insert_id();
		}
		if(!empty($_FILES))
		{
			foreach($_FILES as $scanid=>$f)
			{
				list($null,$id)=explode("_",$scanid);
				$inv_id=$invs[$id];
				if(file_exists(ERP_PHYSICAL_IMAGES."invoices/$inv_id.jpg"))
					unlink(ERP_PHYSICAL_IMAGES."invoices/$inv_id.jpg");
				move_uploaded_file($f['tmp_name'], ERP_PHYSICAL_IMAGES."invoices/{$inv_id}.jpg");
			}
		}
		
		return $grn;
	}
	
	function do_addvariant()
	{
		foreach(array("name","type","itemid","value") as $inp)
			$$inp=$this->input->post($inp);
		$user=$this->erpm->getadminuser();
		$this->db->query("insert into variant_info(variant_name,variant_type,created_by,created_on) values(?,?,?,now())",array($name,$type,$user['userid']));
		$vid=$this->db->insert_id();
		foreach($itemid as $i=>$item)
			$this->db->query("insert into variant_deal_link(variant_id,item_id,variant_value) values(?,?,?)",array($vid,$item,$value[$i]));
		$this->session->set_flashdata("erp_pop_info","Variant added");
		redirect("admin/variants");
	}
	
	function getvouchers_date_range($s,$e)
	{
		if(!$s)
		{
			$s=date("Y-m-d",mktime(0,0,0,date("m"),1));
			$e=date("Y-m-d",mktime(0,0,0,date("m")+1,date("t")));
		}
		return $this->db->query("select * from t_voucher_info where created_on between ? and ? order by voucher_id desc",array($s,$e))->result_array();
	}
	
	function getvouchers()
	{
		return $this->db->query("select * from t_voucher_info order by voucher_id desc")->result_array();
	}
	
	function do_account_grn()
	{
		if(!empty($_FILES))
		{
			foreach($_FILES as $scanid=>$f)
			{
				list($null,$inv_id)=explode("_",$scanid);
				if(file_exists(ERP_PHYSICAL_IMAGES."invoices/$inv_id.jpg"))
					unlink(ERP_PHYSICAL_IMAGES."invoices/$inv_id.jpg");
				move_uploaded_file($f['tmp_name'], ERP_PHYSICAL_IMAGES."invoices/{$inv_id}.jpg");
			}
		}
		foreach(array("inv_amounts","inv_ids","adjusted","grn","mrp","items","discount","type","vvalue","mode","inst_no","inst_date","bank","narration") as $i)
			$$i=$this->input->post($i);
		foreach($inv_ids as $i=>$id)
			$this->db->query("update t_grn_invoice_link set purchase_inv_value=? where id=? limit 1",array($inv_amounts[$i],$id));
		foreach($items as $i=>$item)
		{
			$disc=$discount[$i];
			if($type[$i]==1)
				$disc=$mrp[$i]*$disc/100;
			$this->db->query("update t_grn_product_link set grn_invoice_link_id=?,scheme_discount_value=?,scheme_discunt_type=? where id=? limit 1",array($invoice[$i],$disc,$type[$i],$item));
		}
		$this->db->query("update t_grn_info set payment_status=1 where grn_id=? limit 1",$grn);
		/*
		$inp=array(1,$vvalue,$mode,$inst_no,$inst_date,$bank,$narration);
		$this->db->query("insert into t_pending_voucher_info(voucher_type_id,voucher_value,payment_mode,instrument_no,instrument_date,instrument_issued_bank,narration,created_on,voucher_date)
															values(?,?,?,?,?,?,?,now(),now())",$inp);
		$vid=$this->db->insert_id();
		$this->db->query("insert into t_pending_voucher_document_link(voucher_id,adjusted_amount,ref_doc_id,ref_doc_type,created_on) values(?,?,?,1,now())",array($vid,$adjusted,$grn));
		*/
	}
	
	function do_po_prodwise()
	{
		foreach($_POST['vendor'] as $i=>$v)
		{
			$po=array();
			if(!isset($pos[$v]))
				$pos[$v]=array();
			$po['product']=$_POST['product'][$i];
			$po['qty']=$_POST['qty'][$i];
			if(empty($po['qty']) || $po['qty']==0)
				continue;
			$po['margin']=$_POST['margin'][$i];
			$po['mrp']=$_POST['mrp'][$i];
			$price=$po['mrp']-($po['mrp']*$po['margin']/100);
			$po['discount']=$_POST['sch_type'][$i]?$price*$_POST['sch_discount'][$i]/100:$price-$_POST['sch_discount'][$i];
			$po['type']=$_POST['sch_type'][$i];
			$po['price']=$price-$po['discount'];
			$po['foc']=$this->input->post("foc$i");
			$po['offer']=$this->input->post("offer$i");
			$po['note']=$_POST['note'][$i];
			$pos[$v][]=$po;
		}
		foreach($pos as $v=>$po)
		{
			$total=0;
			$this->db->query("insert into t_po_info(vendor_id,remarks,po_status,created_on) values(?,?,0,now())",array($v,"productwise-".date("d/m/y")));
			$poid=$this->db->insert_id();
			foreach($po as $p)
			{
				$inp=array($poid,$p['product'],$p['qty'],$p['mrp'],$p['margin'],$p['discount'],$p['type'],$p['price'],$p['foc'],$p['offer'],$p['note']);
				$this->db->query("insert into t_po_product_link(po_id,product_id,order_qty,mrp,margin,scheme_discount_value,scheme_discount_type,purchase_price,is_foc,has_offer,special_note,created_on)
																								values(?,?,?,?,?,?,?,?,?,?,?,now())",$inp);
				$total+=$p['price']*$p['qty'];
			}
			$this->db->query("update t_po_info set total_value=? where po_id=? limit 1",array($total,$poid));
		}
		$this->session->set_flashdata("erp_pop_info","POs created");
		redirect("admin/viewpo/$poid");
	}
	
	function getlinkeddealsforproduct($pid)
	{
		return $this->db->query("select l.qty,i.* from m_product_deal_link l join king_dealitems i on i.id=l.itemid where l.product_id=? order by i.name asc",$pid)->result_array();
	}
	
	function getproductdetails($id)
	{
		$sql="select a.*,b.name as brand_name 
				from m_product_info a 
				join king_brands b on a.brand_id = b.id 
				where product_id=?
			";
		$r=$this->db->query($sql,$id)->row_array();
		$r['orders']=$this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s from m_product_deal_link l join king_orders o on o.itemid=l.itemid where l.product_id=? and o.time>".(time()-(24*60*60*90)),$id)->row()->s;
		$r['pen_ord_qty']=$this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s from m_product_deal_link l join king_orders o on o.itemid=l.itemid where l.product_id=? and o.status = 0 ",$id)->row()->s;
		$r['cur_stk']=$this->db->query("select ifnull(sum(available_qty),0) as cur_stk from t_stock_info where product_id = ?",$id)->row()->cur_stk;
		$r['vendors']=$vs=$this->db->query("select v.vendor_id,v.vendor_name,CONCAT(v.vendor_name,' (',b.brand_margin,'%)') as vendor,b.brand_margin as ven_margin from m_product_info p join m_vendor_brand_link b on p.brand_id=b.brand_id join m_vendor_info v on v.vendor_id=b.vendor_id where p.product_id=? order by b.brand_margin desc",array($id,$id))->result_array();
		$r['margin']="0";
		
		if(!empty($vs))
		{
			$v=$vs[0]['vendor_id'];
			$r['margin']=$this->db->query("select brand_margin from m_vendor_brand_link where vendor_id=? and brand_id=?",array($v,$r['brand_id']))->row()->brand_margin;
		}
		return $r;
	}
	
	function searchproducts($q)
	{
		$sql="select p.*,sum(s.available_qty) as stock from m_product_info p left outer join t_stock_info s on s.product_id=p.product_id where p.product_name like ? or (p.barcode=? and p.barcode!='') group by p.product_id order by p.product_name asc";
		return $this->db->query($sql,array("%$q%",$q))->result_array();
	}
	
	function searchproductsfordeal($q)
	{
		if($this->input->post("type")=="prod")
			$sql="select p.*,sum(s.available_qty) as stock from m_product_info p left outer join t_stock_info s on s.product_id=p.product_id where p.product_name like ? or (p.barcode=? and p.barcode!='') group by p.product_id order by p.product_name asc";
		else
			$sql="select concat(g.group_name,' (GROUP)') as product_name,'na' as mrp,g.group_id as product_id,'na' as stock from products_group g where g.group_name like ?";
		return $this->db->query($sql,array("%$q%",$q))->result_array();
	}
	
	function getposforvendor($vid)
	{
		return $this->db->query("select * from t_po_info where vendor_id=?",$vid)->result_array();
	}
	
	function getpos_date_range($s=false,$e=false)
	{
//		if(!$s)
//		{
//			$s=date("Y-m-d",mktime(0,0,0,date("m"),1));
//			$e=date("Y-m-d",mktime(0,0,0,date("m")+1,date("t")));
//		}
		$sql="select v.vendor_name,v.city_name as city,p.* from t_po_info p join m_vendor_info v on v.vendor_id=p.vendor_id where 1";
		if($s)
			$sql.=" and p.created_on between ? and ?";
		$sql.=" order by p.po_id desc";
		if(!$s)
			$sql.=" limit 20";
		
		return $this->db->query($sql,array($s,$e))->result_array();
	}
	
	function getpos()
	{
		return $this->db->query("select v.vendor_name,v.city_name as city,p.* from t_po_info p join m_vendor_info v on v.vendor_id=p.vendor_id order by p.po_id desc")->result_array();
	}
	
	function getpo($p)
	{
		return $this->db->query("select v.vendor_name,p.po_id,p.remarks,p.approval_status,p.approval_date,po_status from t_po_info p join m_vendor_info v on v.vendor_id=p.vendor_id where p.po_id=?",$p)->row_array();
	}
	
	function getpoitems($p)
	{
		return $this->db->query("select p.default_rackbin_id,p.product_name,i.* from t_po_product_link i join m_product_info p on p.product_id=i.product_id where i.po_id=?",$p)->result_array();
	}
	
	function getstoragelocs()
	{
		return $this->db->query("select * from m_storage_location_info")->result_array();
	}
	
	function getpoitemsforgrn($p)
	{
		$ret=$this->db->query("select p.is_serial_required,ifnull(p.barcode,'') as barcode,p.default_rackbin_id,p.product_name,i.*,p.brand_id,p.mrp as prod_mrp from t_po_product_link i join m_product_info p on p.product_id=i.product_id where i.po_id=? and i.order_qty>i.received_qty",$p)->result_array();
		foreach($ret as $i=>$r)
		{
			$ret[$i]['rbs']="";
			foreach($this->db->query("select r.id,r.rack_name,r.bin_name from m_rack_bin_brand_link rb join m_rack_bin_info r on r.id=rb.rack_bin_id where rb.brandid=?",$r['brand_id'])->result_array() as $rb)
				$ret[$i]['rbs'].="<option value='{$rb['id']}'>{$rb['rack_name']}-{$rb['bin_name']}</option>";

			$ret[$i]['bcodes']=array();
			foreach($this->db->query("select product_id,product_barcode from t_stock_info where product_id = ? and product_barcode <> '' ",$r['product_id'])->result_array() as $pbcode)
				array_push($ret[$i]['bcodes'],$pbcode['product_barcode']);	
				
		}
		return $ret;
	}
	
	function createpo()
	{
		$this->db->query("insert into t_po_info(vendor_id,remarks,created_on,po_status) values(?,?,now(),0)",array($_POST['vendor'],$_POST['remarks']));
		$po=$this->db->insert_id();
		$pt=$_POST;
		$total=0;
		foreach($_POST['product'] as $i=>$p)
		{
			if(!isset($pt["foc$i"]))
				$pt["foc$i"]=0;
			if(!isset($pt["offer$i"]))
				$pt["offer$i"]=0;
			$sv=$pt['sch_type'][$i]?$pt['mrp'][$i]*$pt['sch_discount'][$i]/100:$pt['sch_discount'][$i];
			$inp=array($po,$p,$pt['qty'][$i],$pt['mrp'][$i],$pt['margin'][$i],$sv,$pt['sch_type'][$i],$pt['price'][$i],$pt["foc$i"],$pt["offer$i"],$pt['note'][$i]);
			$this->db->query("insert into t_po_product_link(po_id,product_id,order_qty,mrp,margin,scheme_discount_value,scheme_discount_type,purchase_price,is_foc,has_offer,special_note,created_on)
																					values(?,?,?,?,?,?,?,?,?,?,?,now())",$inp);
			$total+=$pt['price'][$i]*$pt['qty'][$i];
		}
		$this->db->query("update t_po_info set total_value=? where po_id=? limit 1",array($total,$po));
		$this->session->set_flashdata("erp_pop_info","PO created");
		redirect("admin/viewpo/$po");
	}
	
	function getrackbins()
	{
		return $this->db->query("select * from m_rack_bin_info order by rack_name asc")->result_array();
	}
	
	function searchvendors($q)
	{
		return $this->db->query("select * from m_vendor_info where vendor_name like ? order by vendor_name limit 20","%$q%")->result_array();
	}

	function getproductsbytax($bid)
	{
		return $this->db->query("select sum(s.available_qty) as stock,p.*,b.name as brand from m_product_info p join king_brands b on b.id=p.brand_id left outer join t_stock_info s on s.product_id=p.product_id where p.vat=? group by p.product_id order by p.product_name asc",$bid)->result_array();
	}
	
	function getproductsbybrand($bid)
	{
		return $this->db->query("select sum(s.available_qty) as stock,p.*,b.name as brand from m_product_info p join king_brands b on b.id=p.brand_id left outer join t_stock_info s on s.product_id=p.product_id where p.brand_id=? group by p.product_id order by p.product_name asc",$bid)->result_array();
	}
	
	function getdealsforbrand($bid)
	{
		return $this->db->query("select i.*,d.* from king_deals d join king_dealitems i on i.dealid=d.dealid where d.brandid=? order by i.name asc",$bid)->result_array();
	}
	
	function getproducts()
	{
		return $this->db->query("select sum(s.available_qty) as stock,p.*,b.name as brand from m_product_info p join king_brands b on b.id=p.brand_id left outer join t_stock_info s on s.product_id=p.product_id group by p.product_id order by p.product_id desc limit 30")->result_array();
	}
	
	function getproductsforbrand($id)
	{
		return $this->db->query("select * from m_product_info where brand_id=?",$id)->result_array();
	}
	
	function getbrandsforvendor($id)
	{
		return $this->db->query("select b.*,vb.brand_id,vb.brand_margin,vb.applicable_from,vb.applicable_till from king_brands b join m_vendor_brand_link vb on vb.brand_id=b.id and vb.vendor_id=? order by b.name asc",$id)->result_array();
	}
	
	function getvendorsforbrand($id)
	{
		return $this->db->query("select c.*,v.*,sum(p.total_value) as total_value,count(p.total_value) as pos from m_vendor_info v left outer join t_po_info p on p.vendor_id=v.vendor_id join m_vendor_brand_link vb on vb.vendor_id=v.vendor_id join m_vendor_contacts_info c on c.vendor_id=v.vendor_id where vb.brand_id=? group by v.vendor_id order by v.vendor_name asc",$id)->result_array();
	}
	
	function getvendors()
	{
		return $this->db->query("select c.*,v.*,sum(p.total_value) as total_value,count(p.total_value) as pos from m_vendor_info v left outer join t_po_info p on p.vendor_id=v.vendor_id join m_vendor_contacts_info c on c.vendor_id=v.vendor_id group by v.vendor_id order by v.vendor_name asc")->result_array();
	}
	
	function getvendor($id)
	{
		return $this->db->query("select * from m_vendor_info where vendor_id=?",$id)->row_array();
	}
	
	function getvendorcontacts($id)
	{
		return $this->db->query("select * from m_vendor_contacts_info where vendor_id=?",$id)->result_array();
	}
	
	function pnh_getfranchisesbytown($tid)
	{
		$user=$this->erpm->getadminuser();
		$sql="select f.is_suspended,f.franchise_id,f.is_lc_store,c.class_name,c.margin,c.combo_margin,f.pnh_franchise_id,f.franchise_name,f.locality,f.city,f.current_balance,f.login_mobile1,f.login_mobile2,f.email_id,u.name as assigned_to,t.territory_name from pnh_m_franchise_info f left outer join king_admin u on u.id=f.assigned_to join pnh_m_territory_info t on t.id=f.territory_id join pnh_m_class_info c on c.id=f.class_id";
		if(!$this->erpm->auth(CALLCENTER_ROLE,true))
			$sql.=" join pnh_franchise_owners o on o.franchise_id=f.franchise_id and o.admin={$user['userid']}";
		$sql.=" left outer join pnh_franchise_owners ow on ow.franchise_id=f.franchise_id left outer join king_admin a on a.id=ow.admin where town_id=? group by f.franchise_id order by f.franchise_name asc";
		return $this->db->query($sql,$tid)->result_array();
	}
	
	function pnh_getfranchisesbyterry($tid)
	{
		$user=$this->erpm->getadminuser();
		$sql="select f.is_suspended,tw.town_name as town_name,f.franchise_id,f.is_lc_store,c.class_name,c.margin,c.combo_margin,f.pnh_franchise_id,f.franchise_name,f.locality,f.city,f.current_balance,f.login_mobile1,f.login_mobile2,f.email_id,u.name as assigned_to,t.territory_name from pnh_m_franchise_info f left outer join king_admin u on u.id=f.assigned_to join pnh_m_territory_info t on t.id=f.territory_id join pnh_towns tw on tw.id=f.town_id join pnh_m_class_info c on c.id=f.class_id";	
		if(!$this->erpm->auth(CALLCENTER_ROLE,true))
			$sql.=" join pnh_franchise_owners o on o.franchise_id=f.franchise_id and o.admin={$user['userid']}";
		$sql.=" left outer join pnh_franchise_owners ow on ow.franchise_id=f.franchise_id left outer join king_admin a on a.id=ow.admin where f.territory_id=? group by f.franchise_id order by f.franchise_name asc";
		return $this->db->query($sql,$tid)->result_array();
	}
	
	function pnh_getfranchises()
	{
		$user=$this->erpm->getadminuser();
		$sql="select f.is_suspended,group_concat(a.name) as owners,tw.town_name as town,f.is_lc_store,f.franchise_id,c.class_name,c.margin,c.combo_margin,f.pnh_franchise_id,f.franchise_name,f.locality,f.city,f.current_balance,f.login_mobile1,f.login_mobile2,f.email_id,u.name as assigned_to,t.territory_name from pnh_m_franchise_info f left outer join king_admin u on u.id=f.assigned_to join pnh_m_territory_info t on t.id=f.territory_id join pnh_towns tw on tw.id=f.town_id join pnh_m_class_info c on c.id=f.class_id";	
		if(!$this->erpm->auth(CALLCENTER_ROLE,true))
			$sql.=" join pnh_franchise_owners o on o.franchise_id=f.franchise_id and o.admin={$user['userid']}";
		$sql.=" left outer join pnh_franchise_owners ow on ow.franchise_id=f.franchise_id left outer join king_admin a on a.id=ow.admin group by f.franchise_id order by f.franchise_name asc";
		return $this->db->query($sql)->result_array();
	}

	function do_pnh_addmember()
	{
		$user=$this->erpm->getadminuser();
//		$this->debug_post();
		foreach(array("mid","gender","salute","fname","lname","dob_d","dob_m","dob_y","address","city","pincode","mobile","email","marital","spouse","cname1","cname2","dow_d","dow_m","dow_y","dobc1_d","dobc1_m","dobc1_y","dobc2_d","dobc2_m","dobc2_y","profession","expense") as $i)
			$$i=$this->input->post($i);
		if(strlen($mid)!=8 || $mid{0}!=2)
			show_error("Invalid MID : $mid");
		$fid=$this->db->query("select franchise_id as fid from pnh_m_allotted_mid where ? between mid_start and mid_end",$mid)->row_array();
		if(empty($fid))
			show_error("$mid is not allotted to any Franchise");
		$fid=$fid['fid'];
		if($this->db->query("select 1 from pnh_member_info where pnh_member_id=?",$mid)->num_rows()==0)
		{
			$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $mid",time()));
			$userid=$this->db->insert_id();
			$this->db->query("insert into pnh_member_info(user_id,pnh_member_id,franchise_id) values(?,?,?)",array($userid,$mid,$fid));
			$npoints=$this->db->query("select points from pnh_member_info where user_id=?",$userid)->row()->points+PNH_MEMBER_FEE;
			$this->db->query("update pnh_member_info set points=? where user_id=? limit 1",array($npoints,$userid));
			$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values(?,?,?,?,?)",array($userid,"",PNH_MEMBER_FEE,$npoints,time()));
			$this->erpm->pnh_fran_account_stat($fid,1,PNH_MEMBER_FEE-PNH_MEMBER_BONUS,"50 Credit Points purchase for Member $mid","member",$mid);
			// pnh account summary - membership  
			$arr = array($fid,4,$mid,PNH_MEMBER_FEE-PNH_MEMBER_BONUS,'',1,date('Y-m-d H:i:s'),$user['userid']);
			$this->db->query("insert into pnh_franchise_account_summary (franchise_id,action_type,member_id,debit_amt,remarks,status,created_on,created_by) values(?,?,?,?,?,?,?,?)",$arr);
			
		}
		else $userid=$this->db->query("select user_id as u from pnh_member_info where pnh_member_id=?",$mid)->row()->u;
		$payload=array("pnh_member_id"=>$mid,"gender"=>$gender,"salute"=>$salute,"first_name"=>$fname,"last_name"=>$lname);
		$payload['dob']=$dob_y.'-'.$dob_m.'-'.$dob_d;
		$payload['address']=$address;
		$payload['city']=$city;
		$payload['pincode']=$pincode;
		$payload['mobile']=$mobile;
		$payload['email']=$email;
		$payload['marital_status']=$marital;
		$payload['spouse_name']=$spouse;
		$payload['child1_name']=$cname1;
		$payload['child2_name']=$cname2;
		$payload['anniversary']=$dow_y.'-'.$dow_m.'-'.$dow_d;
		$payload['child1_dob']=$dobc1_y.'-'.$dobc1_m.'-'.$dobc1_d;
		$payload['child2_dob']=$dobc2_y.'-'.$dobc2_m.'-'.$dobc2_d;
		$payload['expense']=$expense;
		$payload['profession']=$profession;
		$payload['created_on']=time();
		$payload['created_by']=$user['userid'];
		$this->db->update("pnh_member_info",$payload,array("user_id"=>$userid));
		$this->erpm->flash_msg("Member details updated");
		
		redirect("admin/pnh_addmember");
	}
	
	
	function do_pnh_bulkaddmembers()
	{
		$user=$this->erpm->getadminuser();
		
		$error = '';
		//check if file is uploaded.
		if(isset($_FILES['imp_file']))
		{
			if(!$_FILES['imp_file']['name'])
				$error = "Please choose import file";
			else
			{
				//validate import file
				$f = fopen($_FILES['imp_file']['tmp_name'],'r');
				$head=fgetcsv($f);
				fclose($f);
				if(count($head) != 28)
					$error = "Invalid import file format";
			}
		}else
			$error = "Invalid access";
		
		
		if($error)
		{
			$this->erpm->flash_msg($error);
			redirect("admin/pnh_bulkaddmembers");
		}
		 
 
		 
		$f = fopen($_FILES['imp_file']['tmp_name'],'r');
			 
		$head = array("mid","gender","salute","fname","lname","dob_d","dob_m","dob_y","address","city","pincode","mobile","email","marital","spouse","cname1","cname2","dow_d","dow_m","dow_y","dobc1_d","dobc1_m","dobc1_y","dobc2_d","dobc2_m","dobc2_y","profession","expense");
		$newdump = array();
		 
		$m=0;
		while(($data=fgetcsv($f))!=false)
		{
			if($m < 1)
			{
				$m = 1;
				continue;
			}
			foreach($head as $i=>$d)
				$$d=$data[$i];
				
			$gender = trim(strtolower($gender));
				
			if($gender == 'male')
				$gender_indx = 0;
			else if($gender == 'female')
				$gender_indx = 1;
			else if($gender == '')
				$gender_indx = 2;
				
			$salute_indx = 0;
			$salute = trim(strtolower($salute));
			if($salute == 'mr')
				$salute_indx = 0;
				else if($salute == 'mrs')
					$salute_indx = 1;
					else if($salute == 'ms')
						$salute_indx = 2;
			
			$expense_index = 0;
			if($expense <= 2000)
				$expense_index = 0;
			else if($expense > 2000 && $expense <=5000)
					$expense_index = 1;
				else if($expense > 5000 && $expense <=10000)
					$expense_index = 2;
					else if($expense > 10000)
						$expense_index = 3;
				
			$marital_indx = 0;			
			$marital = trim(strtolower($marital));				
			if($marital == 'married')
				$marital_indx = 0;
			else if($marital == 'single')
			 	 $marital_indx = 1;
				else if($marital == 'other') 	
				 	$marital_indx = 2; 
						
			if(strlen($mid)!=8 || $mid{0}!=2)
			{
				$data[28] = 0;
			}
			else
			{
				$fid=$this->db->query("select franchise_id as fid from pnh_m_allotted_mid where ? between mid_start and mid_end",$mid)->row_array();
				if(empty($fid))
					show_error("$mid is not allotted to any Franchise");
				$fid=$fid['fid'];
				if($this->db->query("select 1 from pnh_member_info where pnh_member_id=?",$mid)->num_rows()==0)
				{
					$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $mid",time()));
					$userid=$this->db->insert_id();
					$this->db->query("insert into pnh_member_info(user_id,pnh_member_id,franchise_id) values(?,?,?)",array($userid,$mid,$fid));
					$npoints=$this->db->query("select points from pnh_member_info where user_id=?",$userid)->row()->points+PNH_MEMBER_FEE;
					$this->db->query("update pnh_member_info set points=? where user_id=? limit 1",array($npoints,$userid));
					$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values(?,?,?,?,?)",array($userid,"",PNH_MEMBER_FEE,$npoints,time()));
					$this->erpm->pnh_fran_account_stat($fid,1,PNH_MEMBER_FEE-PNH_MEMBER_BONUS,"50 Credit Points purchase for Member $mid","member",$mid);
				}
				else $userid=$this->db->query("select user_id as u from pnh_member_info where pnh_member_id=?",$mid)->row()->u;
				$payload=array("pnh_member_id"=>$mid,"gender"=>$gender_indx,"salute"=>$salute_indx,"first_name"=>$fname,"last_name"=>$lname);
				$payload['dob']=$dob_y.'-'.$dob_m.'-'.$dob_d;
				$payload['address']=$address;
				$payload['city']=$city;
				$payload['pincode']=$pincode;
				$payload['mobile']=$mobile;
				$payload['email']=$email;
				$payload['marital_status']=$marital;
				$payload['spouse_name']=$spouse;
				$payload['child1_name']=$cname1;
				$payload['child2_name']=$cname2;
				$payload['anniversary']=$dow_y.'-'.$dow_m.'-'.$dow_d;
				$payload['child1_dob']=$dobc1_y.'-'.$dobc1_m.'-'.$dobc1_d;
				$payload['child2_dob']=$dobc2_y.'-'.$dobc2_m.'-'.$dobc2_d;
				$payload['expense']=$expense_index;
				$payload['profession']=$profession;
				$payload['created_on']=time();
				$payload['created_by']=$user['userid'];
				$this->db->update("pnh_member_info",$payload,array("user_id"=>$userid));
				$m++;
				$data[28] = 1;
			}
			$newdump[] = $data;
		}
		fclose($f);
		if($m)
		{
			$head[] = 'Status';
			@ob_start();
			$f=fopen("php://output","w");
			fputcsv($f,$head);
			foreach($newdump as $p)
				fputcsv($f,$p);
			fclose($f);
			$csv=ob_get_clean();
			@ob_clean();
		    header('Content-Description: File Transfer');
		    header('Content-Type: text/csv');
		    header('Content-Disposition: attachment; filename='.("members_bulk_upload_".date("d_m_y_H\h:i\m").".csv"));
		    header('Content-Transfer-Encoding: binary');
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . strlen($csv));
		    @ob_clean();
		    flush();
		    echo $csv;
			
		}else
		{
			$this->erpm->flash_msg("No Members updated");
			redirect("admin/pnh_bulkaddmembers");
		}
		
	}
	
	
	function pnh_getfranchise($fid)
	{
		$sql="select f.*,f.franchise_id,f.sch_discount,f.sch_discount_start,f.sch_discount_end,f.credit_limit,f.security_deposit,c.class_name,c.margin,c.combo_margin,f.pnh_franchise_id,f.franchise_name,f.locality,f.city,f.current_balance,f.login_mobile1,f.login_mobile2,f.email_id,u.name as assigned_to,t.territory_name from pnh_m_franchise_info f left outer join king_admin u on u.id=f.assigned_to join pnh_m_territory_info t on t.id=f.territory_id join pnh_m_class_info c on c.id=f.class_id where f.franchise_id=? order by f.franchise_name asc";	
		return $this->db->query($sql,$fid)->row_array();
	}
	
	function do_pnh_manage_devices($fid)
	{
		$user=$this->erpm->getadminuser();
		$sno=$this->input->post("dsno");
		$types=$this->input->post("dtype");
		foreach($sno as $i=>$s)
		{
			$type=$types[$i];
			if(empty($s))
				break;
			if($this->db->query("select 1 from pnh_m_device_info where device_sl_no=? and device_type_id=? and issued_to!=0",array($s,$types[$i]))->num_rows()!=0)
				show_error("Device with serial no $s is already allotted to someone");
			if($this->db->query("select 1 from pnh_m_device_info where device_sl_no=? and device_type_id=?",array($s,$types[$i]))->num_rows()==0)
			{
				$this->db->query("insert into pnh_m_device_info(device_sl_no,device_type_id) values(?,?)",array($s,$types[$i]));
				$did=$this->db->insert_id();
			}else
				$did=$this->db->query("select id from pnh_m_device_info where device_sl_no=? and device_type_id=?",array($s,$type))->row()->id;
			$this->db->query("update pnh_m_device_info set issued_to=$fid where id=?",$did);
			$this->db->query("insert into pnh_t_device_movement_info(device_id,issued_to,created_by,created_on) values(?,?,?,?)",array($did,$fid,$user['userid'],time()));
		}
		redirect("admin/pnh_manage_devices/$fid");
	}
	
	/**
	 * check for stock by item ids
	 * 
	 * @param $items
	 * @param $i_qty
	 */ 
	function do_stock_check($items,$i_qty=array(),$return_stock=false)
	{
		$payload=array();
		$pids=array();
		/** fixing for test 1 qty **/ 
		if(empty($i_qty))
			foreach($items as $i)
				$i_qty[]=1;

		/** assiging the req qty for item stock check **/		
		$i=0;
		foreach($items as $item)
		{
			$qty[$item]=$i_qty[$i];
			$i++;
		}
				
		/** Getting Products for Items  **/ 
		$i=0;
		foreach($items as $item)
		{
			$d=array();
			foreach($this->db->query("select l.product_id,p.product_name,l.qty,p.is_sourceable as src from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$item)->result_array() as $p)
			{
				$d[]=array("pid"=>$p['product_id'],"product_name"=>$p['product_name'],"qty"=>$p['qty'],"status"=>$p['src'],"stk"=>0);
				$pids[]=$p['product_id'];
			}
			$payload[$item]=$d;
			$i++;
		}
		
		// Check for Stock Available and alloted stock pid    
		$pids=array_unique($pids);
		foreach($pids as $pid)
		{
			$available[$pid]=$this->db->query("select ifnull(sum(available_qty),0) as s from t_stock_info where product_id=?",$pid)->row()->s;
			$required[$pid]=$this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s from m_product_deal_link l join king_orders o on o.itemid=l.itemid and o.status=0 where l.product_id=?",$pid)->row()->s;
			
		}
		
		
		
		
		$i=0;
		foreach($payload as $iid=>$pay)
		{
			// Check if stock available for required and requested qty of product 
			foreach($pay as $ip=>$item)
				if($available[$item['pid']]>=$required[$item['pid']]+($item['qty']*$qty[$iid]))
				{
					$payload[$iid][$ip]['status']=1; // yes stock available
					$payload[$iid][$ip]['stk']=$available[$item['pid']];
				}
					
			 
					
			// check if deal items having stock  		
			$all_avail=true;
			foreach($pay as $item)
				if($item['status']==0)
					$all_avail=false;
					
			if($all_avail)
				foreach($pay as $i2=>$item)
				{
					// remove required stock from  available stock with required for further iteration checks 
					$available[$item['pid']]-=($item['qty']*$qty[$iid]);
					$payload[$iid][$i2]['status']=1;
					//$payload[$iid][$ip]['stk']=$available[$item['pid']];
				}
			$i++;
		}
		
		$p_stock_det = array();
		// get all item ids for which stock available 
		$stock_avail=array();
		foreach($payload as $iid=>$pay)
		{
			$all_avail=true;
			foreach($pay as $item)
				if($item['status']==0)
					$all_avail=false;
			if($all_avail)
			{
				$stock_avail[]=$iid;
				$p_stock_det[$iid]=array();
			}
		}
	 
		if($return_stock)
			return $payload;
		else
			return $stock_avail;
	}
		
	function do_pnh_add_fran_bank_details($fid)
	{
		$user=$this->erpm->getadminuser();
		foreach(array("bank_name","account_no","branch_name","ifsc_code") as $i)
			$inp[$i]=$this->input->post($i);
		$inp['created_by']=$user['userid'];
		$inp['created_on']=time();
		$inp['franchise_id']=$fid;
		$this->db->insert("pnh_franchise_bank_details",$inp);
		redirect("admin/pnh_franchise/$fid#bank");			
	}
	
	function do_pnh_cancel_receipt($rid)
	{
		$user=$this->erpm->getadminuser();
		$msg=$this->input->post("msg");
		$r=$this->db->query("select * from pnh_t_receipt_info where receipt_id=?",$rid)->row_array();
		$this->db->query("update pnh_t_receipt_info set reason=?,status=2,activated_by=?,activated_on=? where receipt_id=? limit 1",array($msg,$user['userid'],time(),$rid));
		$this->erpm->flash_msg("Receipt $rid cancelled");
		redirect("admin/pnh_pending_receipts");
	}
	
	function do_pnh_activate_receipt($rid)
	{
		$user=$this->erpm->getadminuser();
		$msg=$this->input->post("msg");
		$r=$this->db->query("select * from pnh_t_receipt_info where receipt_id=?",$rid)->row_array();
		$this->db->query("update pnh_t_receipt_info set reason=?,status=1,activated_by=?,activated_on=? where receipt_id=? limit 1",array($msg,$user['userid'],time(),$rid));
		if($r['receipt_type']==1)
			$this->erpm->pnh_fran_account_stat($r['franchise_id'],0, $r['receipt_amount'],"Topup for Receipt:{$r['receipt_id']} ".date("d/m/y",$r['created_on']),"topup",$r['receipt_id']);
		else
			$this->db->query("update pnh_m_franchise_info set security_deposit=security_deposit+? where franchise_id=? limit 1",array($r['receipt_amount'],$r['franchise_id']));
		$this->erpm->flash_msg("Receipt $rid activated");
		redirect("admin/pnh_pending_receipts");
	}
	
	function do_pnh_topup($fid)
	{
		$user=$this->erpm->getadminuser();
		foreach(array("r_type","amount","type","no","date","msg") as $i)
			$$i=$this->input->post($i);
		$inp=array("receipt_type"=>$r_type,"franchise_id"=>$fid,"receipt_amount"=>$amount,"payment_mode"=>$type,"instrument_no"=>$no,"instrument_date"=>strtotime($date),"created_by"=>$user['userid'],"created_on"=>time(),"remarks"=>$msg);
		$this->db->insert("pnh_t_receipt_info",$inp);
		$recpt_id = $this->db->insert_id();
//		$this->erpm->pnh_fran_account_stat($fid,0, $amount,"Topup $no $date");

		$arr = array($fid,((!$r_type)?2:3),$recpt_id,$r_type,$amount,$msg,1,date('Y-m-d H:i:s'),$user['userid']);
				$this->db->query("insert into pnh_franchise_account_summary (franchise_id,action_type,receipt_id,receipt_type,credit_amt,remarks,status,created_on,created_by) values(?,?,?,?,?,?,?,?,?)",$arr);
		
		redirect("admin/pnh_franchise/$fid");
	}
	
	function do_pnh_updatedeal($itemid)
	{
		foreach(array("gender_attr","menu","keywords","tagline","name","mrp","offer_price","store_offer_price","nyp_offer_price","brand","category","description","pid","qty","tax","shipsin") as $q)
			$$q=$this->input->post($q);
		$imgname = randomChars ( 15 );
		if (isset ( $_FILES ['pic'] ) && $_FILES ['pic'] ['error'] == 0)
		{
			$this->load->library("thumbnail");
			$img=$_FILES['pic']['tmp_name'];
			if($this->thumbnail->check($img))
			{
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/300/$imgname.jpg","width"=>300));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/small/$imgname.jpg","width"=>200));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/thumbs/$imgname.jpg","width"=>50,"max_height"=>50));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/$imgname.jpg","width"=>400));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/big/$imgname.jpg","width"=>1000));
			}
		}
		else
			$imgname=NULL;
		$dealid=$this->db->query("select dealid from king_dealitems where id=?",$itemid)->row()->dealid;
		if($imgname)
		{
			$this->db->query("update king_deals set pic=? where dealid=? limit 1",array($imgname,$dealid));
			$this->db->query("update king_dealitems set pic=? where dealid=? limit 1",array($imgname,$dealid));
		}
		$this->db->query("update king_dealitems set name=?,orgprice=?,price=?,store_price=?,nyp_price=?,gender_attr=?,tax=?,shipsin=?,modified=?,modified_on=?,modified_by=? where id=?",array($name,$mrp,$offer_price,$store_offer_price,$nyp_offer_price,$gender_attr,$tax*100,$shipsin,time(),date('Y-m-d H:i:s'),$user['userid'],$itemid));
		$this->db->query("update king_deals set description=?,keywords=?,menuid=?,keywords=?,catid=?,brandid=?,tagline=? where dealid=?",array($description,$keywords,$menu,$keywords,$category,$brand,$tagline,$dealid));
		$this->session->set_flashdata("erp_pop_info","Deal updated");
		redirect("admin/pnh_deal/$itemid");
	}
	
	function flash_msg($msg)
	{
		$this->session->set_flashdata("erp_pop_info",$msg);
	}
	
	function do_pnh_deal_extra_images($id)
	{
		$s=0;
		$dealid=$this->db->query("select dealid from king_dealitems where id=?",$id)->row()->dealid;
		foreach($_FILES['pic']['error'] as $i=>$e)
		{
			if($e==0)
			{
				$imgname = randomChars ( 15 );
				$this->load->library("thumbnail");
				$img=$_FILES['pic']['tmp_name'][$i];
				if($this->thumbnail->check($img))
				{
					$s++;
					$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/300/$imgname.jpg","width"=>300));
					$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/small/$imgname.jpg","width"=>200));
					$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/thumbs/$imgname.jpg","width"=>50,"max_height"=>50));
					$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/$imgname.jpg","width"=>400));
					$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/big/$imgname.jpg","width"=>1000));
					$this->db->query("insert into king_resources(dealid,itemid,type,id) values($dealid,$id,0,'$imgname')");
					$this->erpm->flash_msg("$s images uploaded");
					redirect("admin/pnh_deal/$id");
				}
			}
		}
	}
	
	function do_pnh_adddeal()
	{
		$user=$this->erpm->getadminuser();
		
		foreach(array("gender_attr","menu","keywords","tagline","name","mrp","offer_price","store_offer_price","nyp_offer_price","brand","category","description","pid","qty","tax","shipsin","pid_g","qty_g") as $q)
			$$q=$this->input->post($q);
		$imgname = randomChars ( 15 );
		if (isset ( $_FILES ['pic'] ) && $_FILES ['pic'] ['error'] == 0)
		{
			$this->load->library("thumbnail");
			$img=$_FILES['pic']['tmp_name'];
			if($this->thumbnail->check($img))
			{
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/300/$imgname.jpg","width"=>300));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/small/$imgname.jpg","width"=>200));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/thumbs/$imgname.jpg","width"=>50,"max_height"=>50));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/$imgname.jpg","width"=>400));
				$this->thumbnail->create(array("source"=>$img,"dest"=>"images/items/big/$imgname.jpg","width"=>1000));
			}
		}
		else
			$imgname=null;
		if(!$imgname)
			show_error("No valid image uploaded");
		$itemid=$this->erpm->p_genid(10);
		$dealid=$this->erpm->p_genid(10);
		$pnh_id="1".$this->erpm->p_genid(7);
		$inp=array($dealid,$menu,$keywords,$category,$brand,$imgname,$tagline,$description,1);
		$this->db->query("insert into king_deals(dealid,menuid,keywords,catid,brandid,pic,tagline,description,publish) values(?,?,?,?,?,?,?,?,?)",$inp);
		$inp=array($itemid,$dealid,$name,$imgname,$mrp,$offer_price,$store_offer_price,$nyp_offer_price,$gender_attr,1,$pnh_id,$tax*100,$shipsin,1,time(),date("Y-m-d H:i:s"),$user['userid']);
		$this->db->query("insert into king_dealitems(id,dealid,name,pic,orgprice,price,store_price,nyp_price,gender_attr,is_pnh,pnh_id,tax,shipsin,live,created,created_on,created_by) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",$inp);
		if(!empty($pid))
		foreach($pid as $i=>$p)
			$this->db->query("insert into m_product_deal_link(product_id,itemid,qty,created_on,created_by) values(?,?,?,?,?)",array($p,$itemid,$qty[$i],date('Y-m-d H:i:s'),$user['userid']));
		if(!empty($pid_g))
		foreach($pid_g as $i=>$p)
			$this->db->query("insert into m_product_group_deal_link(group_id,itemid,qty,created_on,created_by) values(?,?,?,?,?)",array($p,$itemid,$qty_g[$i],date('Y-m-d H:i:s'),$user['userid']));
		redirect("admin/pnh_deals");
	}
	
	function pnh_getdeals()
	{
		//return $this->db->query("select d.publish,d.brandid,d.catid,i.orgprice,i.price,i.name,i.pic,i.pnh_id,i.id as itemid,b.name as brand,c.name as category from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid where i.is_pnh=1 order by i.created_on desc,i.name asc limit 30")->result_array();
		$sql = "select ifnull(group_concat(smd.special_margin),0) as sm,d.publish,d.brandid,d.catid,i.orgprice,
						i.price,i.name,i.pic,i.pnh_id,i.id as itemid,
						b.name as brand,c.name as category 
					from king_deals d 
					join king_dealitems i on i.dealid=d.dealid 
					join king_brands b on b.id=d.brandid 
					join king_categories c on c.id=d.catid 
					left join pnh_special_margin_deals smd on i.id = smd.itemid  and smd.from <= unix_timestamp() and smd.to >=unix_timestamp()  
					where i.is_pnh=1 
					group by i.id 
					order by i.sno desc
					limit 30 
				";
		return $this->db->query($sql)->result_array();
	}
	
	function pnh_getdealsbycat($catid,$type=0)
	{
		if($type==0)
			$ret=$this->db->query("select ifnull(group_concat(smd.special_margin),0) as sm,d.publish,d.brandid,d.catid,i.orgprice,i.price,i.name,i.pic,i.pnh_id,i.id as itemid,b.name as brand,c.name as category from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left join pnh_special_margin_deals smd on i.id = smd.itemid  and smd.from <= unix_timestamp() and smd.to >=unix_timestamp() where i.is_pnh=1 and d.catid=? group by i.id order by i.name asc",$catid)->result_array();
		else if($type==1)
			$ret=$this->db->query("select * from (select ifnull(group_concat(smd.special_margin),0) as sm,i.id,o.transid,o.time as order_time,d.publish,d.brandid,d.catid,i.orgprice,i.price,i.name,i.pic,i.pnh_id,i.id as itemid,b.name as brand,c.name as category from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join king_orders o on o.itemid=i.id left join pnh_special_margin_deals smd on i.id = smd.itemid and smd.from <= unix_timestamp() and smd.to >=unix_timestamp() where i.is_pnh=1 and d.catid=? group by i.id order by o.time desc) as dd group by dd.id order by dd.order_time desc",$catid)->result_array();
		else if($type==2)
			$ret=$this->db->query("select ifnull(group_concat(smd.special_margin),0) as sm,o.quantity as qty,ifnull(sum(o.quantity),0) as sold,d.publish,d.brandid,d.catid,i.orgprice,i.price,i.name,i.pic,i.pnh_id,i.id as itemid,b.name as brand,c.name as category from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join king_orders o on o.itemid=i.id left outer join king_transactions t on t.transid=o.transid and t.is_pnh=1 left join pnh_special_margin_deals smd on i.id = smd.itemid and smd.from <= unix_timestamp() and smd.to >=unix_timestamp() where i.is_pnh=1 and d.catid=? group by i.id order by count(o.id) desc",$catid)->result_array();
		else if($type==3)
			$ret=$this->db->query("select ifnull(group_concat(smd.special_margin),0) as sm,o.quantity as qty,ifnull(sum(o.quantity),0) as sold,d.publish,d.brandid,d.catid,i.orgprice,i.price,i.name,i.pic,i.pnh_id,i.id as itemid,b.name as brand,c.name as category from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join king_orders o on o.itemid=i.id left outer join king_transactions t on t.transid=o.transid and t.is_pnh=1 left join pnh_special_margin_deals smd on i.id = smd.itemid and smd.from <= unix_timestamp() and smd.to >=unix_timestamp() where i.is_pnh=1 and d.catid=? and (o.time>".mktime(0,0,0,0,-90)." or o.time is null) group by i.id order by count(o.id) desc,i.name asc",$catid)->result_array();
		return $ret;	
//		return $this->db->query("select d.publish,d.brandid,d.catid,i.orgprice,i.price,i.name,i.pic,i.pnh_id,i.id as itemid,b.name as brand,c.name as category from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid where i.is_pnh=1 and d.catid=? order by i.name asc",$catid)->result_array();
	}
		
	function pnh_getdealsbybrand($brandid,$type=0)
	{
		if($type==0)
			$ret=$this->db->query("select ifnull(group_concat(smd.special_margin),0) as sm,d.publish,d.brandid,d.catid,i.orgprice,i.price,i.name,i.pic,i.pnh_id,i.id as itemid,b.name as brand,c.name as category from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left join pnh_special_margin_deals smd on i.id = smd.itemid and smd.from <= unix_timestamp() and smd.to >=unix_timestamp() where i.is_pnh=1 and d.brandid=? group by i.id order by i.name asc",$brandid)->result_array();
		else if($type==1)
			$ret=$this->db->query("select * from (select ifnull(group_concat(smd.special_margin),0) as sm,i.id,o.transid,o.time as order_time,d.publish,d.brandid,d.catid,i.orgprice,i.price,i.name,i.pic,i.pnh_id,i.id as itemid,b.name as brand,c.name as category from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join king_orders o on o.itemid=i.id left join pnh_special_margin_deals smd on i.id = smd.itemid and smd.from <= unix_timestamp() and smd.to >=unix_timestamp() where i.is_pnh=1 and d.brandid=? group by i.id order by o.time desc) as dd group by dd.id order by dd.order_time desc",$brandid)->result_array();
		else if($type==2)
			$ret=$this->db->query("select ifnull(group_concat(smd.special_margin),0) as sm,o.quantity as qty,ifnull(sum(o.quantity),0) as sold,d.publish,d.brandid,d.catid,i.orgprice,i.price,i.name,i.pic,i.pnh_id,i.id as itemid,b.name as brand,c.name as category from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join king_orders o on o.itemid=i.id left outer join king_transactions t on t.transid=o.transid and t.is_pnh=1 left join pnh_special_margin_deals smd on i.id = smd.itemid and smd.from <= unix_timestamp() and smd.to >=unix_timestamp() where i.is_pnh=1 and d.brandid=? group by i.id order by count(o.id) desc",$brandid)->result_array();
		else if($type==3)
			$ret=$this->db->query("select ifnull(group_concat(smd.special_margin),0) as sm,o.quantity as qty,ifnull(sum(o.quantity),0) as sold,d.publish,d.brandid,d.catid,i.orgprice,i.price,i.name,i.pic,i.pnh_id,i.id as itemid,b.name as brand,c.name as category from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join king_orders o on o.itemid=i.id left outer join king_transactions t on t.transid=o.transid and t.is_pnh=1 left join pnh_special_margin_deals smd on i.id = smd.itemid and smd.from <= unix_timestamp() and smd.to >=unix_timestamp() where i.is_pnh=1 and d.brandid=? and (o.time>".mktime(0,0,0,0,-90)." or  o.time is null) group by i.id order by count(o.id) desc,i.name asc",$brandid)->result_array();
		return $ret;	
	}
	
	function pnh_fran_account_stat($fid,$type,$amount,$desc,$action_for='',$ref_id=0)
	{
		$bal=$this->db->query("select current_balance from pnh_m_franchise_info where franchise_id=?",$fid)->row()->current_balance;
		if($type==0)
			$nbal=$bal+$amount;
		else
			$nbal=$bal-$amount;
		$inp=array("franchise_id"=>$fid,"type"=>$type,"amount"=>$amount,"balance_after"=>$nbal,"desc"=>$desc,"action_for"=>$action_for,"ref_id"=>$ref_id,"created_on"=>time());
		$this->db->insert("pnh_franchise_account_stat",$inp);
		$this->db->query("update pnh_m_franchise_info set current_balance=? where franchise_id=? limit 1",array($nbal,$fid));
		return $this->db->insert_id();
	}
	
	
	function pnh_sendsms($to,$msg,$fid=0)
	{
		if($fid!=0)
		{
			$inp=array("to"=>$to,"msg"=>$msg,"franchise_id"=>$fid,"sent_on"=>time());
			$this->db->insert("pnh_sms_log_sent",$inp);
		}
		if($_SERVER['HTTP_HOST']=="localhost")
			return;
		$exotel_sid="snapittoday";
		$exotel_token="491140e9fbe5c507177228cf26cf2f09356e042c";
		$post = array(
		    'From'   => '9243404342',
		    'To'    => $to,
		    'Body'  => $msg
		);
		$url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Sms/send";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
		$http_result = curl_exec($ch);
		$error = curl_error($ch);
		$http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
		curl_close($ch);
	}
	
	function do_pnh_updatefranchise($fid)
	{
//		$this->debug_post();
		$user=$this->erpm->getadminuser();
		$cols=array("franchise_name", "address", "locality", "city","state", "postcode","login_mobile1","login_mobile2","email_id", "store_name","business_type","no_of_employees", "store_area", "store_open_time", "store_close_time","website_name","internet_available","class_id","territory_id","town_id", "security_question", "security_answer","security_question2","security_answer2","security_custom_question","security_custom_question2","store_pan_no","store_tin_no","store_service_tax_no","store_reg_no","is_lc_store","modified_by","modified_on");
		foreach(array("name","address","locality","city","state","postcode","login_mobile1","login_mobile2","login_email","shop_name","business_type","shop_emps","shop_area","shop_from","shop_to","website","internet","class","territory","town","sec_q","sec_a","sec_q2","sec_a2","sec_cq","sec_cq2","store_pan","store_tin","store_stax","store_reg","is_lc_store") as $i=>$a)
			$data[$cols[$i]]=$this->input->post($a);
		if($this->input->post("sec_q")!="-1")
			$data['security_custom_question']="";
		if($this->input->post("sec_q2")!="-1")
			$data['security_custom_question2']="";
		foreach(array("cnt_name","cnt_desgn","cnt_mob1","cnt_mob2","cnt_telephone","cnt_fax","cnt_email1","cnt_email2") as $i)
			$$i=$this->input->post($i);
		$this->db->query("delete from pnh_m_franchise_contacts_info where franchise_id=?",$fid);
		foreach($cnt_name as $i=>$cname)
		{
			$inp=array("franchise_id"=>$fid,"contact_name"=>$cname,"contact_designation"=>$cnt_desgn[$i],"contact_mobile1"=>$cnt_mob1[$i],"contact_mobile2"=>$cnt_mob2[$i],"contact_telephone"=>$cnt_telephone[$i],"contact_fax"=>$cnt_fax[$i],"contact_email1"=>$cnt_email1[$i],"contact_email2"=>$cnt_email2[$i]);
			$this->db->insert("pnh_m_franchise_contacts_info",$inp);
		}

		$data['modified_by']=$user['userid'];
		$data['modified_on']=time();
		$this->db->update("pnh_m_franchise_info",$data,array("franchise_id"=>$fid));
		redirect("admin/pnh_franchise/$fid");
	}
	
	function do_pnh_addfranchise()
	{
//		$this->debug_post();
		$user=$this->erpm->getadminuser();
		foreach(array("name","address","own","locality","city","state","postcode","class","territory","town","login_mobile1","login_mobile2","login_email","internet","shop_name","shop_emps","shop_area","shop_from","shop_to","website","assign_to","dev_sno","dev_type","cnt_name","cnt_desgn","cnt_mob1","cnt_mob2","cnt_telephone","cnt_fax","cnt_email1","cnt_email2","sec_amount","sec_type","sec_bank","sec_no","sec_date","business_type","sec_q","sec_a","sec_q2","sec_a2","sec_msg","store_reg","store_tin","store_pan","store_stax","is_lc_store") as $i)
			$$i=$this->input->post($i);
		if($this->db->query("select 1 from pnh_m_franchise_info where (login_mobile1=? and login_mobile1!='') || (login_mobile1=? and login_mobile1!='') || (login_mobile2=? and login_mobile2!='') || (login_mobile2=? and login_mobile2!='')",array($login_mobile1,$login_mobile2,$login_mobile1,$login_mobile2))->num_rows()!=0)
			show_error("Already a franchise exists with given login mobile");
		$fid="3".$this->erpm->p_genid(7);
		$inp=array("town_id"=>$town,"business_type"=>$business_type,"pnh_franchise_id"=>$fid,"franchise_name"=>$name,"address"=>$address,"locality"=>$locality,"city"=>$city,"postcode"=>$postcode,"state"=>$state,"territory_id"=>$territory,"class_id"=>$class,"security_deposit"=>0,"login_mobile1"=>$login_mobile1,"login_mobile2"=>$login_mobile2,"email_id"=>$login_email,"assigned_to"=>$assign_to,"no_of_employees"=>$shop_emps,"store_name"=>$shop_name,"store_area"=>$shop_area,"store_open_time"=>mktime($shop_from),"store_close_time"=>mktime($shop_to),"own_rented"=>$own,"internet_available"=>$internet,"website_name"=>$website,"created_by"=>$user['userid'],"security_question"=>$sec_q,"security_answer"=>$sec_a,"security_question2"=>$sec_q2,"security_answer2"=>$sec_a2,"created_on"=>time(),"store_tin_no"=>$store_tin,"store_pan_no"=>$store_pan,"store_service_tax_no"=>$store_stax,"store_reg_no"=>$store_reg,"is_lc_store"=>$is_lc_store);
//		$sql="insert into pnh_m_franchise_info(pnh_franchise_id,franchise_name,address,locality,city,postcode,state,territory_id,class_id,security_deposit,login_mobile1,login_mobile2,email_id,assigned_to,no_of_employees,store_name,store_area,store_open_time,store_close_time,own_rented,internet_available,website_name,created_by,created_on)
//																																			values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
//		$inp=array($fid,$name,$address,$locality,$city,$postcode,$state,$territory,$class,$sec_amount,$login_mobile1,$login_mobile2,$login_email,$assign_to,$shop_emps,$shop_name,$shop_area,mktime($shop_from),mktime($shop_to),$own,$internet,$website,$user['userid'],time());
//		$this->db->query($sql,$inp);
		$this->db->insert("pnh_m_franchise_info",$inp);
		$id=$this->db->insert_id();
		$sql="insert into pnh_t_receipt_info(franchise_id,bank_name,receipt_amount,receipt_type,payment_mode,instrument_no,instrument_date,remarks,created_by,created_on,status) values(?,?,?,?,?,?,?,?,?,?,?)";
		$this->db->query($sql,array($id,$sec_bank,$sec_amount,0,$sec_type,$sec_no,strtotime($sec_date),$sec_msg,$user['userid'],time(),0));
		$this->erpm->pnh_sendsms($login_mobile1,"Hi $name, Welcome to PayNearHome. Your Franchise account is created successfully. Happy Franchising!",$id);
		foreach($cnt_name as $i=>$cname)
		{
			$inp=array("franchise_id"=>$id,"contact_name"=>$cname,"contact_designation"=>$cnt_desgn[$i],"contact_mobile1"=>$cnt_mob1[$i],"contact_mobile2"=>$cnt_mob2[$i],"contact_telephone"=>$cnt_telephone[$i],"contact_fax"=>$cnt_fax[$i],"contact_email1"=>$cnt_email1[$i],"contact_email2"=>$cnt_email2[$i]);
			$this->db->insert("pnh_m_franchise_contacts_info",$inp);
		}
		$this->db->query("insert into pnh_franchise_owners(admin,franchise_id,created_by,created_on) values(?,?,?,?)",array($user['userid'],$id,$user['userid'],time()));
//		$inp=array();
//		foreach($dev_sno as $i=>$sno)
//			$inp[]=array("device_sl_no"=>$sno,"device_type_id"=>$dev_type[$i],"issued_to"=>$fid);
//		$this->db->insert_batch("pnh_m_device_info",$inp);
		redirect("admin/pnh_franchises");
	}
	
	function do_pnh_upload_images($fid)
	{
		$user=$this->erpm->getadminuser();
		foreach($_FILES['pic']['tmp_name'] as $i=>$t)
		{
			if(strpos($_FILES['pic']['type'][$i],"image")===false)
				continue;
			$p="p".rand(433235,32535235).".".pathinfo($_FILES['pic']['name'][$i],PATHINFO_EXTENSION);
			$data=array("franchise_id"=>$fid,"pic"=>$p,"caption"=>$_FILES['pic']['name'][$i],"created_by"=>$user['userid'],"created_on"=>time());
			$this->db->insert("pnh_franchise_photos",$data);
			move_uploaded_file($t,ERP_PHYSICAL_IMAGES."franchises/".$p);
		}
		$this->erpm->flash_msg("Images Uploaded");
		redirect("admin/pnh_franchise/$fid");
	}
	
	function get_pnh_margin($fid,$pid)
	{
		$itemid=$this->db->query("select id from king_dealitems where pnh_id=?",$pid)->row()->id;
		$brandid=$this->db->query("select d.brandid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.is_pnh=1 and i.pnh_id=?",$pid)->row()->brandid;
		$catid=$this->db->query("select d.catid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.is_pnh=1 and i.pnh_id=?",$pid)->row()->catid;
		$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
		$margin=$this->db->query("select margin,combo_margin,less_margin_brands from pnh_m_class_info where id=?",$fran['class_id'])->row_array();
		
		$prod_margin=$this->db->query("select * from pnh_special_margin_deals where itemid=? and ? between `from` and `to` order by id desc limit 1",array($itemid,time()))->row_array();
		$chk_brand_marg = 1;
		if(!empty($prod_margin))
		{
			if($prod_margin['special_margin'] > 0)
			{
				$margin['margin']=$prod_margin['special_margin'];
				$chk_brand_marg = 0;
			}
		}
		if($chk_brand_marg)	
			if($this->db->query("select 1 from pnh_less_margin_brands where brandid=?",$brandid)->num_rows()!=0)
				$margin['margin']=$margin['less_margin_brands'];
		
		$margin['base_margin']=$margin['margin'];
		$margin['sch_margin']=0;
		$margin['bal_discount']=0;
		$bmargin=$this->db->query("select discount from pnh_sch_discount_brands where franchise_id=? and ? between valid_from and valid_to and brandid=? and catid=? order by id desc limit 1",array($fid,time(),$brandid,$catid))->row_array();
		if(empty($bmargin))
			$bmargin=$this->db->query("select discount from pnh_sch_discount_brands where franchise_id=?  and ? between valid_from and valid_to and brandid=? and catid=0 order by id desc limit 1",array($fid,time(),$brandid,$catid))->row_array();
		if(empty($bmargin))
			$bmargin=$this->db->query("select discount from pnh_sch_discount_brands where franchise_id=?  and ? between valid_from and valid_to and catid=? and brandid=0 order by id desc limit 1",array($fid,time(),$catid))->row_array();
		if(empty($bmargin))
		{
			if($fran['sch_discount_start']<time() && $fran['sch_discount_end']>time() && $fran['is_sch_enabled'])
			{
				$margin['margin']+=$fran['sch_discount'];
				$margin['sch_margin']=$fran['sch_discount'];
			}
		}else{
			$margin['margin']+=$bmargin['discount'];
			$margin['sch_margin']=$bmargin['discount'];
		}
		
		
		$c_balance_det = $this->db->query("select min_balance_value,bal_discount from king_dealitems a join king_deals b on a.dealid = b.dealid join pnh_menu c on c.id = b.menuid where a.pnh_id = ? ",$pid)->row_array();
		if($c_balance_det['min_balance_value'])
		{
			$balance=$this->db->query("select current_balance from pnh_m_franchise_info where franchise_id=?",$fran['franchise_id'])->row()->current_balance;
			if($balance >= $c_balance_det['min_balance_value'])
			{
				$margin['margin']+=$c_balance_det['bal_discount']*1;
				$margin['bal_discount']+=$c_balance_det['bal_discount']*1;
			}
		} 
		
		unset($margin['less_margin_brands']);
		return $margin;
	}
	
	function do_pnh_offline_order()
	{
		$admin = $this->auth(false);
		foreach(array("fid","pid","qty","mid","redeem","redeem_points") as $i)
			$$i=$this->input->post($i);
		$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
		$margin=$this->db->query("select margin,combo_margin from pnh_m_class_info where id=?",$fran['class_id'])->row_array();
		if($fran['sch_discount_start']<time() && $fran['sch_discount_end']>time() && $fran['is_sch_enabled'])
			$margin['margin']+=$fran['sch_discount'];
			
			
			
		$items=array();
		foreach($pid as $i=>$p)
			$items[]=array("pid"=>$p,"qty"=>$qty[$i]);
		$total=0;$d_total=0;
		$itemnames=$itemids=array();
		foreach($items as $i=>$item)
		{
			$prod=$this->db->query("select i.*,d.publish from king_dealitems i join king_deals d on d.dealid=i.dealid where i.is_pnh=1 and  i.pnh_id=? and i.pnh_id!=0",$item['pid'])->row_array();
			if(empty($prod))
				die("There is no product with ID : ".$item['pid']);
			if($prod['publish']!=1)
				die("Product {$prod['name']} is not available");
			$items[$i]['tax']=$prod['tax'];
			$items[$i]['mrp']=$prod['orgprice'];
			if($fran['is_lc_store'])
				$items[$i]['price']=$prod['store_price'];
			else
				$items[$i]['price']=$prod['price'];
			$items[$i]['itemid']=$prod['id'];
			$margin=$this->erpm->get_pnh_margin($fran['franchise_id'],$item['pid']);
			if($prod['is_combo']=="1")
				$items[$i]['discount']=$items[$i]['price']/100*$margin['combo_margin'];
			else
				$items[$i]['discount']=$items[$i]['price']/100*$margin['margin'];
			$items[$i]['margin']=$margin;
			$total+=$items[$i]['price']*$items[$i]['qty'];
			$d_total+=($items[$i]['price']-$items[$i]['discount'])*$items[$i]['qty'];
			$itemids[]=$prod['id'];
			$itemnames[]=$prod['name'];
		}
		$avail=$this->erpm->do_stock_check($itemids);
		foreach($itemids as $i=>$itemid)
		 if(!in_array($itemid,$avail))
		 	die("{$itemnames[$i]} is out of stock");
		if($fran['credit_limit']+$fran['current_balance']<$d_total)
			show_error("Insufficient balance! Balance in your account Rs {$fran['current_balance']} Total order amount : Rs $d_total");
		if($this->db->query("select 1 from pnh_member_info where pnh_member_id=?",$mid)->num_rows()==0)
		{ 
			if($this->db->query("select 1 from pnh_m_allotted_mid where ? between mid_start and mid_end and franchise_id=?",array($mid,$fran['franchise_id']))->num_rows()==0)
				show_error("Member ID $mid is not allotted to you");
			$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $mid",time()));
			$userid=$this->db->insert_id();
			$this->db->insert("pnh_member_info",array("user_id"=>$userid,"pnh_member_id"=>$mid,"franchise_id"=>$fran['franchise_id'],"first_name"=>$this->input->post("m_name"),"mobile"=>$this->input->post("m_mobile")));
			$npoints=$this->db->query("select points from pnh_member_info where user_id=?",$userid)->row()->points+PNH_MEMBER_FEE;
			$this->db->query("update pnh_member_info set points=? where user_id=? limit 1",array($npoints,$userid));
			$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values(?,?,?,?,?)",array($userid,"",PNH_MEMBER_FEE,$npoints,time()));
			$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1,PNH_MEMBER_FEE-PNH_MEMBER_BONUS,"50 Credit Points purchase for Member $mid","member",$mid);
		}
		else
			$userid=$this->db->query("select user_id from pnh_member_info where pnh_member_id=?",$mid)->row()->user_id;
		$transid=strtoupper("PNH".random_string("alpha",3).$this->p_genid(5));
		if($redeem)
		{
			$total-=$redeem_points;
			$d_total-=$redeem_points;
			$apoints=$this->db->query("select points from pnh_member_info where user_id=?",$userid)->row()->points-$redeem_points;
			$this->db->query("update pnh_member_info set points=points-? where user_id=? limit 1",array(100,$userid));
			$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values(?,?,?,?,?)",array($userid,$transid,-$redeem_points,$apoints,time()));
			$this->erpm->do_trans_changelog($transid,"$redeem_points Loyalty points redeemed");
		}
		
		$bal_discount_amt = 0;
		
		$this->db->query("insert into king_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id,trans_created_by) values(?,?,?,?,?,?,?,?,?)",array($transid,$d_total,$d_total,3,time(),time(),1,$fran['franchise_id'],$admin['userid']));
		foreach($items as $item)
		{
			$inp=array("id"=>$this->p_genid(10),"transid"=>$transid,"userid"=>$userid,"itemid"=>$item['itemid'],"brandid"=>"");
			$inp["brandid"]=$this->db->query("select d.brandid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['itemid'])->row()->brandid;
			$inp["bill_person"]=$inp['ship_person']=$fran['franchise_name'];
			$inp["bill_address"]=$inp['ship_address']=$fran['address'];
			$inp["bill_city"]=$inp['ship_city']=$fran['city'];
			$inp['bill_pincode']=$inp['ship_pincode']=$fran['postcode'];
			$inp['bill_phone']=$inp['ship_phone']=$fran['login_mobile1'];
			$inp['bill_email']=$inp['ship_email']=$fran['email_id'];
			$inp['bill_state']=$inp['ship_state']=$fran['state'];
			$inp['quantity']=$item['qty'];
			$inp['time']=time();
			$inp['ship_landmark']=$inp['bill_landmark']=$fran['locality'];
			$inp['bill_country']=$inp['ship_country']="India";
			$inp['i_orgprice']=$item['mrp'];
			$inp['i_price']=$item['price'];
			$inp['i_discount']=$item['mrp']-$item['price'];
			if($redeem)
				$inp['i_coup_discount']=$item['discount']+($redeem_points/($d_total+$redeem_points)*$item['price']);
			else
				$inp['i_coup_discount']=$item['discount'];
			$inp['i_tax']=$item['tax'];
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
			
			$bal_discount_amt = ($item['price']*$item['margin']['bal_discount']/100)*$item['qty'];
			
			$m_inp=array("transid"=>$transid,"itemid"=>$item['itemid'],"mrp"=>$item['mrp'],"price"=>$item['price'],"base_margin"=>$item['margin']['base_margin'],"sch_margin"=>$item['margin']['sch_margin'],"bal_discount"=>$item['margin']['bal_discount'],"qty"=>$item['qty'],"final_price"=>$item['price']-$item['discount']);
			$this->db->insert("pnh_order_margin_track",$m_inp);
		}
		$bal_discount_amt_msg = '';
		if($bal_discount_amt)
			$bal_discount_amt_msg = ', Topup Damaka Applied : Rs'.$bal_discount_amt;
		
		$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1, $d_total,"Order $transid - Total Amount: Rs $total".$bal_discount_amt_msg,"transaction",$transid);
		
		$balance=$this->db->query("select current_balance from pnh_m_franchise_info where franchise_id=?",$fran['franchise_id'])->row()->current_balance;
		$this->erpm->pnh_sendsms($fran['login_mobile1'],"Your order is placed successfully! Total order amount :Rs $total. Amount deducted is Rs $d_total. Your order ID is $transid Balance in your account Rs $balance",$fran['franchise_id']);
		$points=0;
		if(!$redeem)
		{
			$rpoints=$this->db->query("select points from pnh_loyalty_points where amount<? order by amount desc limit 1",$total)->row_array();
			if(!empty($rpoints))
				$points=$rpoints['points'];
		}
		$apoints=$this->db->query("select points from pnh_member_info where user_id=?",$userid)->row()->points+$points;
		$this->db->query("update pnh_member_info set points=points+? where user_id=? limit 1",array($points,$userid));
		$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values(?,?,?,?,?)",array($userid,$transid,$points,$apoints,time()));
		$this->erpm->do_trans_changelog($transid,"PNH Offline order created");

		$franid=$fran['franchise_id'];
		$billno=10001;
		$nbill=$this->db->query("select bill_no from pnh_cash_bill where franchise_id=? order by bill_no desc limit 1",$franid)->row_array();
		if(!empty($nbill))
			$billno=$nbill['bill_no']+1;
		$inp=array("bill_no"=>$billno,"franchise_id"=>$franid,"transid"=>$transid,"user_id"=>$userid,"status"=>1);
		$this->db->insert("pnh_cash_bill",$inp);
		
		redirect("admin/trans/$transid");
	}
	
	function is_franchise_auth($fid)
	{
		$user=$this->erpm->getadminuser();
		if($this->erpm->auth(true,true))
			return true;
		if($this->db->query("select 1 from pnh_franchise_owners where franchise_id=? and admin=?",array($fid,$user['userid']))->num_rows()==0)
				show_error("Access Denied<br><div style='font-size:14px;'>I reckon you don't have permission to do this</div>");
		return true;
	}
	
	function do_pnh_download_stat($fid_list,$from,$to,$bypasszero=1)
	{
		$fid_str = implode(',',$fid_list);
		@list($s_y,$s_m,$s_d)=@explode("-",$from);
		$s=mktime(0,0,0,$s_m,$s_d,$s_y);
				 
		@list($s_y,$s_m,$s_d)=@explode("-",$to);
		$e=mktime(23,59,59,$s_m,$s_d,$s_y);
				
		if($fid_str)
		{
			$has_entry = 0;
			if(!$bypasszero)
				$has_entry=$this->db->query("select count(*) as total from pnh_franchise_account_stat where franchise_id=? and created_on between ? and ? order by created_on desc",array($fid_str,$s,$e))->row()->total;
			
			$this->load->library("pdf");
			$this->pdf->doc_type("Account Statement");
			$this->pdf->first=1;
			$this->pdf->AliasNbPages();
				
			foreach($fid_list as $fid)
			{
				$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
				 
				
				$cdata=$this->db->query("select IF(type=0,'In','Out') as Type,`desc` as Description,amount as Amount,balance_after as `Balance After`,created_on from pnh_franchise_account_stat where franchise_id=? and created_on between ? and ? order by created_on desc",array($fid,$s,$e))->result_array();
				$data=array();
				foreach($cdata as $i=>$d)
				{
					$r=array($d['Type'],$d['Description'],$d['Amount'],$d['Balance After'],date("g:ia d/m/y",$d['created_on']));
					$data[]=$r;
				}
				
				if(!count($data) && !$bypasszero)
					continue ;
					
				$this->pdf->AddPage();
				$this->pdf->SetFont('Arial','',6);
				$this->pdf->Cell(180,-3,"Statement Date :",0,0,"R");
				$this->pdf->SetFont('Arial','B',6);
				$this->pdf->Cell(30,-3,date("d/m/y"),0,1);
				$this->pdf->SetFont('Arial','B',11);
				$this->pdf->Cell(165,5,"Franchise Account Statement",0,1);
				$this->pdf->SetFont('Arial','',8);
				$this->pdf->Ln(1);
				$this->pdf->Cell(300,3,"Statement Period : ".date("d M y",$s)."  to  ".date("d M y",$e),0,1);
				$this->pdf->Cell(23,4,"Current Balance : ",0);
				$this->pdf->SetFont('Arial','B',8);
				$this->pdf->Cell(50,4,"Rs {$fran['current_balance']}",0,1);
				$this->pdf->SetFont('Arial','B',9);
				$this->pdf->Ln(3);
				$this->pdf->Cell(300,5,"Franchise Details",0,1);
				$this->pdf->SetFont('Arial','B',8);
				$this->pdf->Cell(200,4.5,"{$fran['franchise_name']}   (FID: {$fran['pnh_franchise_id']})",0,1);
				$this->pdf->SetFont('Arial','',8);
				$this->pdf->MultiCell(150,3.5,"{$fran['address']}, {$fran['locality']}, \n{$fran['city']}, {$fran['state']} - {$fran['postcode']}",0,"L");
				$this->pdf->Image("images/paynearhome.jpg",165,22,30);
				$this->pdf->Ln(5);
				$this->pdf->account_stat(array("Type","Description","Amount","Balance After","Date"),$data);
				
		//		$this->pdf->Output();
		//		$this->erpm->export_csv("Account_statement_{$from}_to_{$to}",$data,false);
			}
			
			$this->pdf->Output("Account Statement - $from to $to.pdf","D");
		}
		
	}
	
	function do_pnh_add_class()
	{
		foreach(array("class","margin","combo_margin","less_margin_brands") as $i)
			$$i=$this->input->post($i);
		if($this->db->query("select 1 from pnh_m_class_info where class_name=?",$class)->num_rows()!=0)
			show_error("Class $class already exists");
		$this->db->query("insert into pnh_m_class_info(class_name,margin,combo_margin,less_margin_brands) values(?,?,?,?)",array($class,$margin,$combo_margin,$less_margin_brands));
		redirect("admin/pnh_class");
	}
	
	function do_pnh_update_class()
	{
		foreach(array("id","margin","combo_margin","less_margin") as $i)
			$$i=$this->input->post($i);
		$this->db->query("update pnh_m_class_info set margin=?,combo_margin=?,less_margin_brands=? where id=?",array($margin,$combo_margin,$less_margin,$id));
		$this->erpm->flash_msg("Class Updated");
		redirect("admin/pnh_class");
	}
	
	function do_pnh_add_territory()
	{
		$user=$this->erpm->getadminuser();
		$name=$this->input->post("terry");
		if($this->db->query("select 1 from pnh_m_territory_info where territory_name=?",$name)->num_rows()!=0)
			show_error("Territory $name already exists");
		$this->db->query("insert into pnh_m_territory_info(territory_name,created_on,created_by) values(?,?,?)",array($name,time(),$user['userid']));
		redirect("admin/pnh_territories");
	}
	
	function pdate_territory()
	{
		$user=$this->erpm->getadminuser();
		$id=$this->input->post("id");
		$name=$this->input->post("terry");
		$this->db->query("update pnh_m_territory_info set territory_name=?,modified_by={$user['userid']},modified_on=".time()." where id=?",array($name,$id));
		redirect("admin/pnh_territories");
	}
	
	function do_pnh_add_device_type()
	{
		$user=$this->erpm->getadminuser();
		$name=$this->input->post("terry");
		$description=$this->input->post("desc");
		if($this->db->query("select 1 from pnh_m_device_type where device_name=?",$name)->num_rows()!=0)
			show_error("Territory $name already exists");
		$this->db->query("insert into pnh_m_device_type(device_name,description,created_on,created_by) values(?,?,?,?)",array($name,$description,time(),$user['userid']));
		redirect("admin/pnh_device_type");
	}
	
	function do_pnh_update_device_type()
	{
		$user=$this->erpm->getadminuser();
		$id=$this->input->post("id");
		$name=$this->input->post("terry");
		$desc=$this->input->post("desc");
		$this->db->query("update pnh_m_device_type set device_name=?,description=?,modified_by={$user['userid']},modified_on=".time()." where id=?",array($name,$desc,$id));
		redirect("admin/pnh_device_type");
	}
	
	function do_pnh_addtown()
	{
		$terry=$this->input->post("territory");
		$towns=$this->input->post("town");
		foreach(explode(",",$towns) as $town)
		{
			if($this->db->query("select 1 from pnh_towns where town_name=?",array($town,$terry))->num_rows()!=0)
				show_error("Town : $town already exists or alloted to another territory");
			$this->db->query("insert into pnh_towns(town_name,territory_id) values(?,?)",array($town,$terry));
		}
		redirect("admin/pnh_towns");
	}
	
	function pnh_gettowns($tid)
	{
		$sql="select t.town_name,tr.territory_name from pnh_towns t join pnh_m_territory_info tr on tr.id=t.territory_id";
		if($tid!=0)
			$sql.=" where t.territory_id=?";
		$sql.=" order by t.town_name asc";
		return $this->db->query($sql,$tid)->result_array();
	}
	
	function p_genid($len)
	{
		$st="";
		for($i=0;$i<$len;$i++)
			$st.=rand(1,9);
		return $st;
	}
	
	function send_admin_note($msg,$isub="")
	{
		$sub="ERP Notification";
		if(!empty($isub))
			$sub.=" : $isub";
		$this->vkm->email(array("vimal@localcircle.in","sri@localcircle.in","gova@localcircle.in","sushma@localcircle.in","sushma@thecouch.in"),$sub,$msg);
	}
	
	function debug_post($die=true)
	{
		echo "<pre>";
		print_r($_POST);
		echo "\n\n\n\n";
		$keys=array();
		foreach($_POST as $key=>$k)
			$keys[]='"'.$key.'"';
		echo implode(",",$keys);
		if($die)
			die;
	}
	
	function getpafdata($pafid)
	{
		return $this->db->query('select a.id as paf_id,handled_by,b.mrp,b.qty,b.vendor_id,a.remarks,ifnull(d.barcode,0) as barcode,
													b.id as prod_paf_id,group_concat(distinct concat(c.vendor_id,"::",c.vendor_name,"::",e.brand_margin)) as vendors,
													b.product_id,d.product_name,notify_handler,
													po_id,paf_status,a.created_on   
												from t_paf_list a
												join t_paf_productlist b on a.id = b.paf_id
												join m_product_info d on b.product_id = d.product_id 
												left join m_vendor_brand_link e on e.brand_id = d.brand_id  
												left join m_vendor_info c on c.vendor_id = e.vendor_id 
												where  a.id = ? 
											group by b.product_id
											order by d.brand_id ',$pafid)->result_array();
	}
	
	function getpafsmslog($pafid)
	{
		return $this->db->query('select * from t_paf_smslog where paf_id = ? order by logged_on desc',$pafid);
	} 
	
	function getpafprodstk($pafid)
	{
		return $this->db->query('select b.product_id,sum(available_qty) as qty  
														from t_stock_info a 
														join t_paf_productlist b on a.product_id = b.product_id 
														where b.paf_id = ? 
														group by b.product_id ',$pafid);
	}
	
	function getpafpendingstk($pafid)
	{
		return $this->db->query('select b.product_id,ifnull(sum(o.quantity*l.qty),0) as qty  
												from m_product_deal_link l 
												left join king_orders o on o.itemid=l.itemid 
												join t_paf_productlist b on b.product_id = l.product_id 
												where b.paf_id = ? and o.status = 0   
											group by b.product_id',$pafid);
	}
	
	function getpafprod_pastorders($pafid)
	{
		return $this->db->query("select b.product_id,ifnull(sum(o.quantity*l.qty),0) as s 
												from m_product_deal_link l 
												left join king_orders o on o.itemid=l.itemid 
												join t_paf_productlist b on b.product_id = l.product_id 
												where b.paf_id = ? and o.time>".(time()-(24*60*60*90))."  
											group by b.product_id",$pafid);
	}
	
}
