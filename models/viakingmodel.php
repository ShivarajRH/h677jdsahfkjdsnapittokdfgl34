<?php
/**
 * Model for front end of viakingsale
 * 
 * @author Vimal <vimal@viaadz.com>
 * @package viakingsale
 * @subpackage frontend
 * @since 2009/12
 * @version 0.9.3
 */

/**
 * Model class for front end
 * 
 * Contains all database functions needed for viakingsale
 * 
 * @author Vimal <vimal@viaadz.com>
 * @version 0.9
 */
class Viakingmodel extends Model
{
	
	function getdealofday()
	{
		$sql="select i.url from king_dealitems as i join king_deals as d on ? between d.startdate and d.enddate and d.publish=1 and i.dealid=d.dealid where i.live=1";
		$deals=$this->db->query($sql,time())->result_array();
		if(empty($deals))
			return false;
		else return $deals[0]['url'];
	}
	
	function updatetrans($resp)
	{
		if(!isset($resp['ResponseCode']))
			return false;
		$code=$resp['ResponseCode'];
		if($code!=0)
			return false;
		$transid=$resp['MerchantRefNo'];
		$tran=$this->db->query("select * from king_transactions where transid=? and status=0",$transid)->row_array();
		if(empty($tran))
			return false;
		$sql="update king_transactions set paid=?,response_code=?,msg=?,payment_id=?,pg_transaction_id=?,is_flagged=?,actiontime=?,status=? where transid=? limit 1";
		$this->db->query($sql,array($resp['Amount'],$resp['ResponseCode'],$resp['ResponseMessage'],$resp['PaymentID'],$resp['TransactionID'],$resp['IsFlagged'],time(),1,$transid));
		if($tran['amount']!=$resp['Amount'])
		{
			$dlog=print_r($resp,true);
			$msg="Transaction : $transid<br>Order was cancelled due to hack attempt suspicion!<br>Amount of Rs {$resp['Amount']} will have to refunded<br><br>$dlog<br>";
			$this->dbm->email(array("vimal@localcircle.in","sri@localcircle.in","sushma@thecouch.in","gova@localcircle.in"), "Possible hack attempt : $transid", $msg);
			return false;
		}
		return true;
	}

	private function update_coupon($coupon)
	{
//		$ch = curl_init();
//		
//		$data = array('access_key' => '1801432634', 'action_type' => 'update','coupon_code'=>$coupon,"status"=>4,"email"=>"asdasd@asddasd.com");
//		curl_setopt($ch, CURLOPT_URL, 'http://tweettheoffers.com/couponcode');
//		curl_setopt($ch, CURLOPT_POST, 1);
//		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
//		$respstr=curl_exec($ch);
		$c=$this->db->query("select * from king_coupons where code=?",$coupon)->row_array();
		if(empty($c))
			return;
		$this->db->query("update king_coupons set used=used+1,lastused=? where code=? limit 1",array(time(),$coupon));
		if(!$c['unlimited'])
			$this->db->query("update king_coupons set status=1 where code=? limit 1",$coupon);
	}
	
	function gen_ga_data($transid)
	{
		$trans=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
		$orders=$this->db->query("select i.name,i.id,i.price,o.quantity,o.ship_state,o.ship_city,o.ship_country from king_orders o join king_dealitems i on i.id=o.itemid where o.transid=?",$transid)->result_array();
		$trans['city']=$orders[0]['ship_city'];
		$trans['state']=$orders[0]['ship_state'];
		$trans['country']=$orders[0]['ship_country'];
		return array("trans"=>$trans,"orders"=>$orders);
	}
	
	function authorder($transid,$paid,$mode,$mail=true)
	{
		$user=$this->session->userdata("user");
		if(!$user)
		{
			$to=$this->db->query("select u.email from king_tmp_orders t join king_users u on u.userid=t.userid where t.transid=?",$transid)->row_array();
			$user=$this->getuserbyemail($to['email']);
			$user['corp']=$this->getcorpname($user['corpid']);
		}
		
		$coupon=$this->db->query("select coupon from king_used_coupons where transid=?",$transid)->row_array();
		if(!empty($coupon))
		{
			$this->update_coupon($coupon['coupon']);
			$this->db->query("update king_used_coupons set status=1 where transid=? limit 1",$transid);
			$c_details=$this->db->query("select * from king_coupons where code=?",$coupon['coupon'])->row_array();
			if($c_details['referral']!=0)
			{
				$ref=$this->db->query("select * from king_users where userid=?",$c_details['referral'])->row_array();
				$this->db->query("insert into king_referral_coupon_track(userid,referral,coupon,transid,time) values(?,?,?,?,?)",array($user['userid'],$c_details['referral'],$coupon['coupon'],$transid,time()));
				$track=$this->db->insert_id();
				$code="SNP".strtoupper(randomChars(7));
				$expires=mktime(23,59,59)+(COUPON_REFERRAL_VALIDITY*24*60*60);
				$inp=array($code,COUPON_REFERRAL_RE_VALUE,COUPON_REFERRAL_RE_MIN,$expires,time(),$ref['userid']);
				$this->db->query("insert into king_coupons(code,value,min,expires,mode,created,userid) values(?,?,?,?,1,?,?)",$inp);
				$this->db->query("update king_referral_coupon_track set ncoupon=?,actiontime=? where id=? limit 1",array($code,time(),$track));
				$this->dbm->email($ref['email'], "Your free coupon : $code", $this->load->view("mails/referral_award",array("coupon"=>$code,"name"=>$ref['name'],"referral"=>$user['name'],"expires"=>$expires,"value"=>COUPON_REFERRAL_RE_VALUE),true));
			}
		}
		
		$invno=$this->db->query("select invoice_no from king_orders order by sno desc limit 1")->row_array();
		if(!empty($invno))
			$invno=$invno['invoice_no']+1;
		else 
			$invno=100001;
			
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
			$inp[]=$paid;
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
			$this->corpbuy($t_order['itemid'], $user['corpid'],$qty);
			$this->db->query("update king_profiles set products=products+?,lastbuy=?, lastbuy_on=? where userid=?",array($t_order['quantity'],$t_order['itemid'],time(),$user['userid']));
			$this->db->query("update king_dealitems set buys=buys+?,snapits=snapits+? where id=?",array($qty,$qty,$t_order['itemid']));
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
			$this->sms($order['ship_phone'],"We have received your order '{$itemname}'. Please note your transaction id: $transid. Thank you for shopping! -Snapittoday.com",false);
			$ormsg.="<tr><td>{$itemname}</td><td>{$order['quantity']}</td><td>{$order['ship_person']}<br>{$order['ship_address']}<br>{$order['ship_city']}</td></tr>";
			$t=$m_item['price']*$order['quantity'];
			$m=$m_item['orgprice']*$order['quantity'];
			$total_discount+=($order['i_coup_discount']+$order['i_discount'])*$order['quantity'];
			$umail.="<tr><td>{$itemname}</td><td>{$m_item['orgprice']}</td><td>{$order['quantity']}</td><td>{$m}</td></tr>";
			$t_tot+=$t;
			$mrp_total+=$m;
			$m_items[]=$m_item;
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
				
		if($mail)
			$this->sendemail($transid,$emails,$umail);
		
		$this->cart->destroy();
		
		$this->do_cashbacks($transid);
		
		if($_SERVER['HTTP_HOST']!="sand43.snapittoday.com")
			$this->email(array("orders@snapittoday.com","admin@localcircle.in"),"New order : $transid",$ormsg);
		
		$this->session->unset_userdata("bps");
		return $umail;
	}
	
	function is_reorder($itemid)
	{
		$validity=time()-(24*REORDER_VALID_DAYS*60*60);
		$user=$this->session->userdata("user");
		if(!$user)
			return false;
		$r=$this->db->query("select 1 from king_orders where userid=? and itemid=? and status=2 and time>$validity",array($user['userid'],$itemid))->row_array();
		if(empty($r))
			return false;
		return true;
	}
	
	function getfeaturedmail($url)
	{
		$f=$this->db->query("select * from king_featured_mails where url=?",$url)->row_array();
		if(empty($f))
			return array();
		$ret['brands']=$ret['items']=array();
		$bids=explode(",",$f['brands']);
		if(!empty($bids))
		{	
			$sql="select b.id as brandid,b.name as brand,i.pic,d.brandid,b.url,i.name,i.price from king_deals d join king_brands b on b.id=d.brandid join king_dealitems i on i.dealid=d.dealid where d.brandid in (".implode(",",$bids).") GROUP by d.brandid";
			$ret['brands']=$this->db->query($sql)->result_array();
		}
		$items=explode(",",$f['items']);
		if(!empty($items))
			$ret['items']=$this->db->query("select i.id,b.url as burl,b.name as brand,rand() as rid,i.url,i.name,d.pic,i.price from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid where i.id in (".implode(",",$items).")")->result_array();
		return $ret;
	}
	
	function getfeaturedbrands()
	{
		$sql="select id from king_brands where ".time()." between featured_start and featured_end";
		$brands=$this->db->query($sql)->result_array();
		$bids=array();
		foreach($brands as $b)
			$bids[]=$b['id'];
		$ret=array();
		if(!empty($bids))
		{
			$sql="select rand() as rid,b.id as brandid,b.name as brand,i.pic,d.brandid,b.url,i.name,i.price from king_deals d join king_brands b on b.id=d.brandid join king_dealitems i on i.dealid=d.dealid where is_pnh = 0 and d.publish=1 and ".time()." between d.startdate and d.enddate and d.brandid in (".implode(",",$bids).") GROUP by d.brandid order by rid desc";
			$ret=$this->db->query($sql)->result_array();
		}
		return $ret;
	}
	
	function corpbuy($item,$corpid,$qty=1)
	{
		$this->db->query("update king_corp_buys set buys=buys+? where itemid=? and corpid=? limit 1",array($qty,$item,$corpid));
		if($this->db->affected_rows()==0)
			$this->db->query("insert king_corp_buys(itemid,corpid,buys) values(?,?,?)",array($item,$corpid,$qty));
	}
	
	function getrelated($id,$catid,$bid)
	{
		$ret=array();
		$ret=$this->db->query("select i.max_allowed_qty,i.pic,i.url,i.name,i.id as itemid,i.price from king_deals d join king_dealitems i on i.dealid=d.dealid and i.id!=? where d.catid=? and d.brandid=? and i.live=1 and i.quantity>i.available and d.publish=1 and ".time()." between d.startdate and d.enddate limit 5",array($id,$catid,$bid))->result_array();
		if(count($ret)<5)
		{
			$rids=array($id);
			foreach($ret as $r)
				$rids[]=$r['itemid'];
			$r2=$this->db->query("select i.max_allowed_qty,i.pic,i.url,i.name,i.id as itemid,i.price from king_deals d join king_dealitems i on i.dealid=d.dealid and i.id not in (".implode(",",$rids).") where d.brandid=? and i.live=1 and i.quantity>i.available and d.publish=1 and ".time()." between d.startdate and d.enddate limit 5",array($bid))->result_array();
			foreach($r2 as $r)
			{
				if(count($ret)==5)
					break;
				$ret[]=$r;
			}
		}
		if(count($ret)<5)
		{
			$rids=array($id);
			foreach($ret as $r)
				$rids[]=$r['itemid'];
			$r2=$this->db->query("select i.max_allowed_qty,i.pic,i.url,i.name,i.id as itemid,i.price from king_deals d join king_dealitems i on i.dealid=d.dealid and i.id not in (".implode(",",$rids).") where d.catid=? and i.live=1 and i.quantity>i.available and d.publish=1 and ".time()." between d.startdate and d.enddate limit 5",array($catid))->result_array();
			foreach($r2 as $r)
			{
				if(count($ret)==5)
					break;
				$ret[]=$r;
			}
		}
		return $ret;
	}
	
	function do_cashbacks($transid)
	{
		$user=$this->session->userdata("user");
		$trans=$this->db->query("select * from king_transactions where transid=?",$transid)->row_array();
		$orders=$this->db->query("select * from king_orders where transid=?",$transid)->result_array();
		if(!$user)
			$user=$this->getuserbyid($orders[0]['userid']);
		foreach($orders as $order)
		{
			$config=$this->db->query("select * from king_product_cashbacks where itemid=?",$order['itemid'])->result_array();
			foreach($config as $c)
			{
				$expires=mktime(23,59,59)+($c['valid']*24*60*60);
				$code="CB".strtoupper(randomChars(8));
				$inp=array($order['id'],$transid,$code,$c['value'],1,$user['userid'],$order['itemid'],$c['min_order'],$expires,0,time());
				$this->db->query("insert into king_pending_cashbacks(orderid,transid,code,value,mode,userid,itemid,min,expires,status,time)
																			values(?,?,?,?,?,?,?,?,?,?,?)",$inp);
			}
		}
		
		$campaign=$this->getcashbackcampaign($trans['amount']);
		if(!empty($campaign))
		{
			$c=$campaign;
			$cashbackamount=ceil($trans['amount']*$c['cashback']/100);
			$this->db->query("insert into king_cashbacks(amount,userid,url,status,time) values(?,?,?,?,?)",array($cashbackamount,$user['userid'],randomChars(10),0,time()));
		}
		
		$pointsys=$this->getpointsys($trans['amount']);
		if(!empty($pointsys))
		{
			$inp=array($user['userid'],$transid,$pointsys['points'],0,time());
			$this->db->query("insert into king_points(userid,transid,points,status,time) values(?,?,?,?,?)",$inp);
		}
	}
	
	function getpointsys($total)
	{
		return $this->db->query("select * from king_points_sys where amount<$total order by amount desc limit 1")->row_array();
	}
	
	function getnextpoints($total)
	{
		return $this->db->query("select * from king_points_sys where amount>$total order by amount asc limit 1")->row_array();
	}
	
	function getcashbackcampaign($total)
	{
		return $this->db->query("select * from king_cashback_campaigns where ".time()." between starts and expires and status=1 and min_trans_amount<$total order by min_trans_amount desc, id desc limit 1")->row_array();
	}
	
	function dealalert($id,$mobile,$email)
	{
		$this->db->query("insert into king_deal_alerts(itemid,email,mobile,request) values(?,?,?,0)",array($id,$email,$mobile));
	}
	
	function dealrequest($id,$mobile,$email)
	{
		$this->db->query("insert into king_deal_alerts(itemid,email,mobile,request) values(?,?,?,1)",array($id,$email,$mobile));
	}
	
	function getfavsforuser($uid)
	{
		return $this->db->query("select f.*,i.url,i.pic,i.name from king_favs f join king_dealitems i on i.id=f.itemid where userid=? and expires_on>?",array($uid,time()))->result_array();		
	}
	
	function getupcoming()
	{
		return $this->db->query("select i.* from king_dealitems i join king_deals d on d.startdate>? and d.dealid=i.dealid and d.publish=1 limit 9",time())->result_array();
	}
	
	function getrecent()
	{
		return $this->db->query("select i.* from king_dealitems i join king_deals d on d.enddate<? and d.dealid=i.dealid and d.publish=1 limit 9",time())->result_array();
	}
	
	function isfsavailable($min)
	{
		$config=$this->db->query("select * from king_freesamples_config where min<=$min order by min desc limit 1")->row_array();
		if(empty($config))
			return false;
		$fs=$this->db->query("select 1 from king_freesamples where min<=$min and available=1")->num_rows();
		if($fs==0)
			return false;
		return true;
	}
	
	function getfreesamples($min)
	{
		return $this->db->query("select * from king_freesamples where min<=$min and available=1")->result_array();
	}
	
	function getfsconfig($min)
	{
		$config=$this->db->query("select * from king_freesamples_config where min<=$min order by min desc limit 1")->row_array();
		return $config;
	}
	
	function checkcod($pincode)
	{
		if(!$this->dbm->is_cod_available())
			return false;
		$r=$this->db->query("select 1 from cod_pincodes where pincode=?",$pincode)->num_rows();
		if($r==0)
			return false;
//		if(empty($r))
//			return false;
//		if($r['cod_applicable']==1)
//			return true;
		return true;
	}
	
	function getreviews($id)
	{
		return $this->db->query("select * from king_reviews where itemid=? and status=1",$id)->result_array();
	}
	
	function newreview($itemid,$name,$rating,$review,$user)
	{
		$userid=0;
		if($user)
		{
			$this->db->query("update king_profiles set reviews=reviews+1 where userid=?",$user['userid']);
			$userid=$user['userid'];
		}
		$this->db->query("insert into king_reviews(itemid,userid,name,rating,review,status,time) values(?,?,?,?,?,?,?)",array($itemid,$userid,$name,$rating,$review,1,time()));
		$r=$this->db->query("select sum(rating) as s,count(1) as c from king_reviews where itemid=?",$itemid)->row();
		$nrating=(int)($r->s/$r->c*10);
		$b=ceil($nrating/10);
		$dec=(int)($nrating%10);
		if($dec!=0 && $dec<=5)
			$nrating=($b*10)+5;
		elseif($dec>5)
			$nrating=($b*10)+10;
		$this->db->query("update king_dealitems set reviews=reviews+1,ratings=? where id=?",array($nrating,$itemid));
	}
	
	function email($emails,$sub,$msg,$aws=true)
	{
		$config=array();
		if(!is_array($emails))
			$emails=array($emails);
		$emails=array_unique($emails);
		if($aws && $_SERVER['HTTP_HOST']=="snapittoday.com")
			$aws=true;
		else
			$aws=false;
		if($aws)
		{
			$config = array(
			    'protocol' => 'smtp',
			    'smtp_host' => 'ssl://email-smtp.us-east-1.amazonaws.com',
			    'smtp_user' => 'AKIAIIIJ7TIURYRQPZTA',
			    'smtp_pass' => 'ArI01zUQ7iwXYnHNfH3GApcBUWSE0v+b5qhhR2zK2B68',
			    'smtp_port' => 465,
			);
		}
		foreach($emails as $email)
		{
			$config['mailtype']="html";
			$this->email->initialize($config);
			if($aws)
				$this->email->set_newline("\r\n");
			$this->email->from("support@snapittoday.com","Snapittoday");
			$this->email->reply_to("care@snapittoday.com","Snapittoday");
			$this->email->to($email);
			$this->email->subject($sub);
			$this->email->message($msg);
			$this->email->send();
		}
	}
	
	function sendemail($transid,$emails,$umail="")
	{
		$aws=false;
		if($_SERVER['HTTP_HOST']=="snapittoday.com")
			$aws=true;
		if($aws)
		{
			$config = array(
			    'protocol' => 'smtp',
			    'smtp_host' => 'ssl://email-smtp.us-east-1.amazonaws.com',
			    'smtp_user' => 'AKIAIIIJ7TIURYRQPZTA',
			    'smtp_pass' => 'ArI01zUQ7iwXYnHNfH3GApcBUWSE0v+b5qhhR2zK2B68',
			    'smtp_port' => 465,
			);
		}
			
		$this->load->library("email");
		
		$msg=$this->load->view("mails/order",array("transid"=>$transid,"umail"=>$umail),true);
		
		$config['mailtype']="html";
		$this->email->initialize($config);
		if($aws)
			$this->email->set_newline("\r\n");
		$this->email->from("support@snapittoday.com","Snapittoday");
		$this->email->to($emails[0]);
		$this->email->subject("Your order details : $transid");
		$this->email->message($msg);
		$this->email->send();
		
		if($emails[0]==$emails[1])
			return;
			
		$this->email->clear();
		$this->email->initialize($config);
		if($aws)
			$this->email->set_newline("\r\n");
		$this->email->from("support@snapittoday.com","Snapittoday");
		$this->email->to($emails[1]);
		$this->email->subject("Your order details : $transid");
		$this->email->message($msg);
		$this->email->send();
	}
	
	function is_cod_available()
	{
		$items=$this->cart->contents();
		foreach($items as $item)
		{
			$itemid=$item['id'];
			if(!$this->db->query("select cod from king_dealitems where id=?",$itemid)->row()->cod)
				return false;
		}
		return true;
	}
	
	function getweeklysavings()
	{
		return $this->db->query("select item.pic,item.price,item.url,item.price,item.name from king_deals deal join king_dealitems item on item.dealid=deal.dealid and item.is_featured=1 and item.live=1 where ".time()." between deal.startdate and deal.enddate and deal.publish=1")->result_array();
	}
	
	function sendpassword($email)
	{
		$sql="select email,userid,corpemail from king_users where email=? limit 1";
		$r=$this->db->query($sql,array($email))->row_array();
		if(empty($r))
			return;
		$hash=random_string("unique");
		$this->db->query("insert into king_password_forgot(hash,userid) values(?,?)",array($hash,$r['userid']));
		$ems=array($r['email']);
		if($r['corpemail']!=$r['email'])
			$ems[]=$r['corpemail'];
		$this->email($ems,"Your Password","Hi,<br><br>Your account was created automatically with respect to your order. Please click on below link to create your password<br><br><a href='".site_url("resetpass/$hash")."'>".site_url("resetpass/$hash")."</a><br><br><br>Warm Regards,<br>Snapittoday.com",true);
	}
	
	
	function get_dealdet($itemid)
	{
		$gd_res = $this->db->query("select is_giftcard,is_coupon_applicable from king_deals a join king_dealitems b on a.dealid = b.dealid where b.id = ?  ",$itemid);
		if($gd_res->num_rows())
			return $gd_res->row_array();
		return false;
	}
	
	function calc_cart_total()
	{
		
		if(!$this->session->userdata('bodyparts_checkout'))
		{
			
			$amount=$total=$this->cart->total();
	
			$coupon=$this->session->userdata("coupon");
			if($coupon)
			{
				$cats=$brands=array();
					$mrp_total=$dtotal=$c_mrp_total=$c_price=0;
					$prods=array();
					foreach($this->cart->contents() as $item)
					{
						$prod=$this->db->query("select d.is_giftcard,d.is_coupon_applicable,d.catid,d.brandid,i.orgprice as mrp,i.shipsto from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['id'])->row_array();
						$shipsto=$prod['shipsto'];
						$prod['qty']=$item['qty'];
						$prod['price']=$item['price'];
						$mrp_total+=$prod['mrp']*$item['qty'];
						
						// check if the coupon is an voucher and reset is_coupon application for all products in cart
						if($coupon['gift_voucher']){
							$prod['is_coupon_applicable'] = 1;
						}
						
						if($prod['is_coupon_applicable']==1)
						{
							$c_mrp_total+=$prod['mrp']*$prod['qty'];
							$c_price+=$prod['price']*$prod['qty'];
						}
						$prods[]=$prod;
					}
						
					$c_total=$c_price;
					if($coupon['mode']==1)
						$c_total=$c_mrp_total;
					
					if($coupon['catid']!="")
					{
						$cats=explode(",",$coupon['catid']);
						$c_total=$c_price=0;
						foreach($prods as $p)
							if($p['is_coupon_applicable']==1 && in_array($p['catid'], $cats))
							{
								$c_total+=($coupon['mode']==1?($p['mrp']*$p['qty']):($p['price']*$p['qty']));
								$c_price+=($p['price']*$p['qty']);
							}
					}	
								
					if($coupon['brandid']!="")
					{
						$brands=explode(",",$coupon['brandid']);
						$c_total=$c_price=0;
						foreach($prods as $p)
						if($p['is_coupon_applicable']==1 && in_array($p['brandid'], $brands))
							{
								$c_total+=($coupon['mode']==1?($p['mrp']*$p['qty']):($p['price']*$p['qty']));
								$c_price+=($p['price']*$p['qty']);
							}
					}
					
					if($coupon['type']==0)	
					{
						$cd_total=$c_total-$coupon['value'];
						if($cd_total<0)
							$cd_total=0;
					}
					elseif($coupon['type']==1)
						$cd_total=$c_total-ceil($c_total*$coupon['value']/100);

					$total=$this->cart->total()-$c_price+$cd_total;
				
					if($total>$this->cart->total())
						$total=$this->cart->total();
			
			}
			else
			{
				$greens=array();
				$favids=$this->dbm->getallfavids();
				foreach($this->cart->contents() as $item)
				{
					$deal_det = $this->get_dealdet($item['id']);
					if($deal_det['is_giftcard'])
					{
						
					}else{
						if(in_array($item['id'], $favids))
						{
							$prod=$this->db->query("select price,orgprice from king_dealitems where id=?",$item['id'])->row_array();
							$newprice=$prod['orgprice']-($prod['orgprice']*FAV_DISCOUNT/100);
							if($prod['price']<$newprice)
								$newprice=$prod['price'];
							$total=$total-($prod['price']*$item['qty'])+($newprice*$item['qty']);
						}else if($this->dbm->is_reorder($item['id']))
						{
							$prod=$this->db->query("select price,orgprice from king_dealitems where id=?",$item['id'])->row_array();
							$newprice=$prod['orgprice']-($prod['orgprice']*REORDER_DISCOUNT/100);
							if($prod['price']<$newprice)
								$newprice=$prod['price'];
							$total=$total-($prod['price']*$item['qty'])+($newprice*$item['qty']);
						}
						else 
							$greens[]=$item['id'];
					}
				}
				$group=array();
				foreach($greens as $g)
				{
					$cb=$this->db->query("select d.brandid,d.catid from king_deals d join king_dealitems i on i.dealid=d.dealid where i.id=?",$g)->row_array();
					if(!isset($group["b{$cb['brandid']}"]))
						$group["b{$cb['brandid']}"]=array();
					if(!isset($group["c{$cb['catid']}"]))
						$group["c{$cb['catid']}"]=array();
					$group["b{$cb['brandid']}"][]=$g;
					$group["c{$cb['catid']}"][]=$g;
				}
				$a_greens=array();
				foreach($group as $g)
				{
					if(count($g)<2)
						continue;
					foreach($g as $i)
						if(!in_array($i,$a_greens))
							$a_greens[]=$i;
				}
				foreach($a_greens as $i)
				{
					foreach($this->cart->contents() as $c)
						if($c['id']==$i)
							break;
					$price=$this->db->query("select price from king_dealitems where id=?",$i)->row()->price;
					$total=$total-($price*COMBINED_BUY_DISCOUNT/100*$c['qty']);
				}
			}
			
			$total=ceil($total);
			
			if($total>$this->cart->total())
				$total=$this->cart->total();
			if($total<0)
				$total=0;
			$amount=$total;
		}
		else
		{
			$sex=$this->session->userdata("bodyparts_checkout");
			if($sex=="male")
				$total=BODYPARTS_MALE;
			else
				$total=BODYPARTS_FEMALE;
		}
		
		return $total;
	}
	
	
	function checkcouponmin($coupon)
	{
		$min=0;
		$cart_value = 0;
		
		$items=$this->cart->contents();
		foreach($items as $item)
		{
			$res=$this->db->query("select d.is_coupon_applicable,i.price,d.brandid,d.catid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=? and d.is_coupon_applicable = 1 ",$item['id']);
			if($res->num_rows()){
				$p = $res->row_array();
				$cart_value += ($p['price']*$item['qty']);
			}
		}
		
		
		if($coupon['brandid']!="" || $coupon['catid']!="")
		{
			
			$prods=array();
			foreach($items as $item)
			{
				$res=$this->db->query("select i.price,d.brandid,d.catid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=? and d.is_coupon_applicable = 1 ",$item['id']);
				if($res->num_rows()){
					$row = $res->row_array();
					$row['qty']=$item['qty'];
					$prods[]=$row;
				}
			}
		}
		
		if($coupon['brandid']!="")
		{
			$brand=$coupon['brandid'];
			$brand=explode(",",$brand);
			
			foreach($prods as $p)
				if(in_array($p['brandid'], $brand)){
					$min+=($p['price']*$p['qty']);
				}	 
		}
		elseif($coupon['catid']!="")
		{
			$cat=$coupon['catid'];
			$cat=explode(",",$cat);
			foreach($prods as $p)
				if(in_array($p['catid'], $cat)){
					$min+=($p['price']*$p['qty']);
				}	
			
		}else{
			//$min=$this->cart->total();
			//$cart_value +=($p['price']*$p['qty']);
			$min = $cart_value; 
		}
		 
		 
		if($coupon['min']<=$min)
			return true;
		return false;
	}
	
	
	function firstorder()
	{
		$user=$this->session->userdata("user");
		if(!$user)
			return true;
		if($this->db->query("select 1 from king_orders where userid=?",$user['userid'])->num_rows()==0)
			return true;
		return false;
	}

	
	function neworder()
	{
		$ship=$cod=0;
		$user=$this->session->userdata("user");
		if(!$user)
		{
			$user=$this->dbm->getuserbyemail($this->input->post("bill_email"));
			if(!$user)
			{
				$p_email=$this->input->post("bill_email");
				$this->dbm->newuser($p_email,substr($p_email,0,strpos($p_email,"@")),$this->input->post("password"),$this->input->post("bill_mobile"),rand(2030,424321),0,"",0);
//				$this->dbm->sendpassword($p_email);
				$user=$this->dbm->getuserbyemail($p_email);
			}
//			$user=$this->dbm->getuserbyemail("guest@localcircle.in");
//			$user['corp']=$this->dbm->getcorpname($user['corpid']);
		}
		$uid=0;
		if(!empty($user))
			$uid=$user['userid'];
			
		$total=$amount=$this->dbm->calc_cart_total();
		
		$coupon=$this->session->userdata("coupon");
		
		$mode=0;
		$gc_total=0;
		foreach($this->cart->contents() as $it)
			if($this->db->query("select is_giftcard from king_deals d join king_dealitems i on i.id=? where d.dealid=i.dealid",$it['id'])->row()->is_giftcard)
				$gc_total+=$it['price']*$it['qty'];

		if(($total!=$gc_total && $total-$gc_total<MIN_AMT_FREE_SHIP) && ($coupon['value'] < MIN_COUPON_VAL_FOR_NOSHIPCHARGE) && $coupon['gift_voucher'] == 0 )
		{
			$amount+=SHIPPING_CHARGES;
			$ship=SHIPPING_CHARGES;
		}
		
		if($this->input->post("paytype")=="cod")
		{
			if(!$ship)
			{
				$amount+=COD_CHARGES;
				$cod=COD_CHARGES;
			}
			$mode=1;
		}
		
		if($this->input->post('is_giftwrap'))
			$amount = $amount+GIFTWRAP_CHARGES; 
		
		$snp="SNP";
		if($mode==1)
			$snp="SNC";
			
		$transid=$snp.random_string("alpha",3).random_string("nozero",5);
		$transid=strtoupper($transid);
		$sql="insert into king_transactions(transid,amount,init,mode,status,cod,ship) values(?,?,?,?,0,?,?)";
		$this->db->query($sql,array($transid,$amount,time(),$mode,$cod,$ship));
		
		$items=$this->cart->contents();
		
		$coupon_price_dets = array();
		$total_itm_applicable_for_coupon = 0; 
		$total_offer_discount = 0;
		$total_itm_applicable_for_fav = 0;
		$total_itm_applicable_for_reorder = 0;
		$coup_det = $coupon; 
			 
		$by_percent = 0; 
		if($coup_det['type']){
			$by_percent = 1;
		}
		
		
		
		
		$user_favitems = array();
		
		// allow fav items computation if coupon is not used 
		if(!$coupon){
			$user_favlist_arr =  $this->db->query("select * from king_favs where userid = ? and expires_on > unix_timestamp() order by id",$user['userid'])->result_array();
			foreach($user_favlist_arr as $usrfavlist){
				$user_favitems[$usrfavlist['itemid']] = 1;
			}
		}
		
		
		//echo "Coupon Details :: ",print_r($coup_det),'<br />';
		foreach($items as $item)
		{
			$is_allowed = 0; 
			$item_det=$this->db->query("select d.is_coupon_applicable,d.brandid,d.catid,i.orgprice,i.price,d.is_giftcard from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$item['id'])->result_array(); 
			
			$item_det = $item_det[0];
			$item_brandid = $item_det['brandid'];
			$item_catid = $item_det['catid'];
			
			$is_giftcard = $item_det['is_giftcard'];
			$is_coupon_applicable = $item_det['is_coupon_applicable'];
			
			if($coupon['gift_voucher']){
				$is_coupon_applicable = $item_det['is_coupon_applicable'] = 1;
			}

			/*if($coup_det['itemid'] == $item['id']){
				$is_allowed  = 1; 
			}*/
			 
			
			
			$allow_fav_disc = 0;
			$allow_reorder_disc = 0;
			
			
			if($is_coupon_applicable && $coup_det){
				if($coup_det['brandid'] != ''){
					if(in_array($item_brandid,explode(',',$coup_det['brandid']))) 
						$is_allowed  = 1; 
				}
				
				if($coup_det['catid'] != ''){
					if(in_array($item_catid,explode(',',$coup_det['catid']))) 
						$is_allowed  = 1; 
				}
				
				if($coup_det && $coup_det['brandid'] == '' && $coup_det['catid'] == ''){
					$is_allowed  = 1;
				} 
			}
			
			
			if(!$coup_det){
				// check if item in fav5list
				if(isset($user_favitems[$item['id']])){
					$allow_fav_disc = 1;
					$is_allowed = 1;
				}else if($this->is_reorder($item['id'])){
					$allow_reorder_disc = 1;
					$is_allowed = 1;
				}
			}
			
			if($is_giftcard){
				$is_allowed = 0;
			}
			
			
			if($is_allowed){ 
					$coupon_price_dets[$item['id']] = array(); 
					$coupon_price_dets[$item['id']]['mrp'] = $item_det['orgprice'];
					$coupon_price_dets[$item['id']]['offerprice'] = $item['price'];
					$coupon_price_dets[$item['id']]['qty'] = $item['qty'];
					
					$total_offer_discount += ($item_det['orgprice']-$item_det['price'])*$item['qty'];
					
					
					$coupon_price_dets[$item['id']]['offer_disc'] = ($item_det['orgprice']-$item_det['price']);
					
					if($allow_fav_disc){
						$total_itm_applicable_for_fav += $item_det['orgprice']*$item['qty'];
					}else if($allow_reorder_disc){
						$total_itm_applicable_for_reorder += $item_det['orgprice']*$item['qty'];
					}else{
						if($coup_det['mode']){
							$total_itm_applicable_for_coupon += $item_det['orgprice']*$item['qty'];	
					 	}else{
					 		$total_itm_applicable_for_coupon += $item_det['price']*$item['qty'];	
					 	}
					}
			 	
	 		}else{
				$coupon_price_dets[$item['id']] = 0; 
			}
		}
		 
		
		 
		
		if($this->input->post('is_giftwrap') == 1)
			$this->db->query("update king_transactions set giftwrap_charge = ? where transid = ? ",array(GIFTWRAP_CHARGES,$transid));
		
		
		
		$by_mrp = 0;
		if($coup_det['mode']){
			$by_mrp = 1;
		}
		
		if($by_percent){
			$c_value = ceil($total_itm_applicable_for_coupon*$coup_det['value']/100);
		}else{
			$c_value = $coup_det['value'];  
		}
		
		/*echo "Total Amount :: ".$total_itm_applicable_for_coupon.'<br />';
		print_r($coupon_price_dets); 
		echo '<br>';
		echo 'Coupon Value ::'.$c_value; 
		echo '<br>';
		echo 'Total Offer Discount ::'.$total_offer_discount;*/
		$tt = 0; 
		$c_disc_t = 0;
		
		
		 
		if(($total_offer_discount < $c_value && $by_mrp == 1  ) || ($by_mrp == 0) ){
			foreach($coupon_price_dets as $c_item_id=>$coup_pd){
				if(is_array($coup_pd)){
					if($coupon_price_dets[$c_item_id]){
						$c_disc = 0;
						
						$stat = 0;
						
						$disc_by = 'icb';
						
						$t_amount = $coupon_price_dets[$c_item_id]['mrp'];
						
						// fix to work  
						if(!$coup_det){
							// Compute Fav Percentage,if item is in user fav list 
							if(isset($user_favitems[$c_item_id])){
								$t_amount = $coupon_price_dets[$c_item_id]['mrp'];
								$default_disc = $coupon_price_dets[$c_item_id]['mrp']-$coupon_price_dets[$c_item_id]['offerprice'];
								$disc = $t_amount*(FAV_DISCOUNT/100);
								
								if($disc < $default_disc){
									//$disc = $default_disc-$disc;
								}
								$disc_by = 'fav';
								$total_applicable_discount = ($disc);
								$c_disc = $total_applicable_discount;
								$stat = 1;
							}else{
								if($this->is_reorder($c_item_id))
								{
									
									$t_amount = $coupon_price_dets[$c_item_id]['mrp'];
									$default_disc = $coupon_price_dets[$c_item_id]['mrp']-$coupon_price_dets[$c_item_id]['offerprice'];
									$disc = $t_amount*(REORDER_DISCOUNT/100);
									 
									
									if($disc < $default_disc){
										//$disc = $default_disc-$disc;
									} 
									$total_applicable_discount = ($disc);
									$c_disc = $total_applicable_discount;
									$stat = 1;
									$disc_by = 'reorder';
								}
							}
						}
						
						if(!$stat && $c_value)
						{
							 if($by_mrp){
							 	$t_amount = $coupon_price_dets[$c_item_id]['mrp'];
							 }else{
							 	$t_amount = $coupon_price_dets[$c_item_id]['offerprice'];
							 }
							// $amount =  $amount*$coupon_price_dets[$c_item_id]['qty'];
							if($by_mrp){
								$total_applicable_discount = ($c_value-$total_offer_discount);
								$c_disc = ($total_applicable_discount*$t_amount/$total_itm_applicable_for_coupon);
							}else{
							 	$c_disc = ($c_value*$t_amount/$total_itm_applicable_for_coupon);
							}
							
							
							
						//	echo ' '.$c_disc.' ';
						//	echo '1';
						}else{
							
							$c_disc = ($c_disc-($coupon_price_dets[$c_item_id]['mrp']-$coupon_price_dets[$c_item_id]['offerprice']));
							if($c_disc < 0)
								$c_disc = 0;
							
						}
						
						//echo '2';
						
						
						//echo '<br>'.$c_disc.' - '.($coupon_price_dets[$c_item_id]['mrp']-$coupon_price_dets[$c_item_id]['offerprice'])*$coupon_price_dets[$c_item_id]['qty'].' <br >';
						
						
						
						
						
						$c_disc_t += $c_disc;
						
						
						$coupon_price_dets[$c_item_id]['discount_applied_amount'] = $t_amount;
						$coupon_price_dets[$c_item_id]['coup_discount'] = $c_disc;
						$coupon_price_dets[$c_item_id]['disc_by'] = $disc_by;
						
						$coupon_price_dets[$c_item_id]['total_disc'] = $c_disc+($coupon_price_dets[$c_item_id]['mrp']-$coupon_price_dets[$c_item_id]['offerprice']);
						
						
						
						//echo $c_disc_t.'<br>';
						
					}
				}
			}
		} 
		
		
		
		/* echo '<br>',$c_disc_t.'ASDADS';
		echo '<br>'; 
		 
		
		print_r($coupon_price_dets); 
		 
		exit;   
		 */
		
		foreach($items as $item)
		{
			$opts=$this->cart->product_options($item['rowid']);
			$buyer_options=array();
			if(isset($opts['sizing']))
				$buyer_options["size"]=$opts['sizing'];
			if(isset($opts['email']))
				$buyer_options['email']=$opts['email'];
		$itemid=$item['id'];

		$bpid=$item['options']['bpid'];
		$bpuid=$item['options']['bpuid'];
		$this->db->query("update king_buyprocess set userid=? where id=? limit 1",array($user['userid'],$bpuid));
		
		$price=$item['price']*$item['qty'];
		
		/*$brandid=$this->db->query("select d.brandid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$itemid)->row()->brandid;
		$vendorid=$this->db->query("select d.vendorid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$itemid)->row()->vendorid;*/
		
		
		$item_det=$this->db->query("select d.brandid,d.vendorid,i.orgprice,i.price,i.nlc,i.phc,i.tax,d.is_giftcard from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$itemid)->row();
		
		
		$brandid = $item_det->brandid;
		$vendorid = $item_det->vendorid;
		$is_giftcard = $item_det->is_giftcard;

		$orderid=random_string("numeric",10);

		$sql="insert into king_tmp_orders(id,userid,itemid,brandid,vendorid,bpid,bill_person,bill_address,bill_landmark,bill_city,bill_state,bill_pincode,bill_phone,bill_telephone,bill_email,bill_country,ship_person,ship_address,ship_landmark,ship_city,ship_state,ship_pincode,ship_phone,ship_telephone,ship_email,ship_country,quantity,amount,time,buyer_options,transid,i_orgprice,i_price,i_nlc,i_phc,i_tax,i_discount,i_coup_discount,i_discount_applied_on,giftwrap_order,user_note,is_giftcard,gc_recp_name,gc_recp_email,gc_recp_mobile,gc_recp_msg)" .
																									" values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$inp=array($orderid,$uid,$itemid,$brandid,$vendorid,$bpid);
		$params=array("bill_person","bill_address","bill_landmark","bill_city","bill_state","bill_pincode","bill_mobile","bill_telephone","bill_email","bill_country","person","address","landmark","city","state","pincode","mobile","telephone","email","country");
		foreach($params as $p)
		{
			$$p=$this->input->post($p);
			$inp[]=$this->input->post($p);
		}
		$inp[]=$item['qty'];
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
			$inp[] = $this->input->post('user_note');
			
			
			if($is_giftcard){ 
				$inp[] = 1;
				$inp[] = $this->input->post('gc_recp_name');
				$inp[] = $this->input->post('gc_recp_email');
				$inp[] = $this->input->post('gc_recp_mobile');
				$inp[] = $this->input->post('gc_recp_msg');
			}else{
				$inp[] = 0;
				$inp[] = '';
				$inp[] = '';
				$inp[] = '';
				$inp[] = '';
			}
			
			$this->db->query($sql,$inp);
		}
		
		$fsselected=$this->session->userdata("fsselected");
		$fsselected=explode(",",$fsselected);
		$fsconfig=$this->dbm->getfsconfig($amount);
		if(!empty($fsconfig))
			for($i=0;$i<$fsconfig['limit'];$i++)
				if(isset($fsselected[$i]))
					$this->db->query("insert into king_freesamples_order(transid,fsid,time) values(?,?,?)",array($transid,$fsselected[$i],time()));
		$this->session->unset_userdata("fsselected");
		
		return array(
					'amount'=>$amount,
					'description'=>"Snapittoday.com Transaction",
					'return_url'=>site_url("processPayment")."?DR={DR}",
					'name'=>$bill_person,
					'address'=>$bill_address,
					'city'=>$bill_city,
					'state'=>$bill_state,
					'country'=>"IND",
					'postal_code'=>$bill_pincode,
					'phone'=>$bill_mobile,
					'ship_name'=>$person,
					'ship_address'=>$address,
					'ship_city'=>$city,
					'ship_state'=>$state,
					'ship_country'=>"IND",
					'ship_postal_code'=>$pincode,
					'ship_phone'=>$mobile,
					'reference_no'=>$transid,
					'email'=>$bill_email,
					);
	}
	
	
	/**
	 * Check user account for given email and password match
	 * 
	 * @param string $email Email address
	 * @param string $password Password
	 * @return bool true on correct match and authentication
	 */
	function checkuser($email,$password)
	{
		$sql="select password from king_users where email=?";
		$q=$this->db->query($sql,array($email));
		if($q->num_rows()==1)
		{
			$r=$q->row();
			if($r->password==md5($password))
				return true;
		}
		return false;
	}
	
	function getallfavids()
	{
		$user=$this->session->userdata("user");
		if(!$user)
			return array();
		$favs=$this->db->query("select f.*,i.orgprice,i.name from king_favs f join king_dealitems i on i.id=f.itemid and i.live=1 join king_deals d on d.dealid=i.dealid and ".time()." between d.startdate and d.enddate and d.publish=1 where userid=? and expires_on>".time(),$user['userid'])->result_array();
		$favids=array();
		foreach($favs as $f)
			$favids[]=$f['itemid'];
		return $favids;
	}
	
	function getallreadydeals()
	{
		$sql="select replace(item.url,' ','-') as url,brand.logoid as brandlogo,brand.name as brand,cat.name as category,item.name,item.pic,item.price,item.orgprice,item.dealid,item.id,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on ? between deal.startdate and deal.enddate and deal.publish=1 join king_categories as cat on deal.catid=cat.id join king_brands as brand on brand.id=deal.brandid where item.dealid=deal.dealid order by cat.prior asc";
		return $this->db->query($sql,array(time()))->result_array();
	}
	
	function getallactivedeals()
	{
		$sql="select url,brand.logoid as brandlogo,brand.name as brand,cat.name as category,item.name,item.pic,item.price,item.orgprice,item.dealid,item.id,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on ? between deal.startdate and deal.enddate and deal.publish=1 join king_categories as cat on deal.catid=cat.id join king_brands as brand on brand.id=deal.brandid where item.dealid=deal.dealid order by cat.prior asc, deal.enddate asc";
		return $this->db->query($sql,array(time()))->result_array();
	}
	
	/**
	 * Get all category names with id from db
	 * 
	 * @return array combination of category id and name pair
	 */
	function getcategories()
	{
		$sql="select distinct(cat.id) as id,cat.name,cat.type from king_categories as cat join king_deals as deal on ".time()." between deal.startdate and deal.enddate and deal.publish=1 where cat.id=deal.catid or cat.id in (select type from king_categories as c join king_deals as d on ".time()." between d.startdate and d.enddate and d.publish=1 where c.id=d.catid) order by cat.name asc";
		$q=$this->db->query($sql);
		$data=array();
//		print_r($q->result_array());
		if($q->num_rows()>0)
		{
			foreach($q->result() as $r)
			{
				$cat=$r->type;
				if(!isset($data[$cat][0]))
					$data[$cat]=array();
				$d['id']=$r->id;
				$d['name']=$r->name;
//				print_r($r);
				array_push($data[$cat],$d);
			}
		}
		return $data;
	}
	
	function donespecialcot($url)
	{
		$this->db->query("update king_pricereqs set status=3 where url=?",array($url));
	}
	
	function getapi($salt)
	{
		return $this->db->query("select * from widgets where salt=?",array($salt))->row_array();
	}
	
	function getpr($id,$uid)
	{
		$sql="select p.userid,p.itemid,item.url as itemurl,p.price as reqprice,p.quantity,p.status,p.time,p.url,p.id,item.name,item.price,item.orgprice from king_pricereqs as p join king_dealitems as item on item.id=p.itemid where p.url=? and p.status=1 and p.userid=?";
		return $this->db->query($sql,array($id,$uid))->row_array();
	}
	
	function getmenu()
	{
		$menu[0]=$this->getcategories();
		$sql="select distinct(brand.id) as id,brand.name from king_brands as brand join king_deals as deal on deal.brandid=brand.id and ".time()." between deal.startdate and deal.enddate and deal.publish=1 join king_dealitems di on di.dealid = deal.dealid and is_pnh = 0 order by name asc";
		$q=$this->db->query($sql);
		$menu[1]=$q->result_array();
		return $menu;
	}
	
	/**
	 * Get user details from account table for specific facebook user id
	 * 
	 * This function is no longer used!
	 * 
	 * @param string $fb facebook user id
	 * @return bool|array Array with user details. False on failure
	 */
	function getspecialuser($fb)
	{
		 $sql="select userid,name,email,inviteid from king_users where special_id=?";
		 $q=$this->db->query($sql,array($fb));
		 	return $q->row_array();
	}
	
	/**
	 * Get category name for given category id
	 * 
	 * @param int $id category id
	 * @return string category name
	 */
	function getcategoryname($id)
	{
		$sql="select name from king_categories where id=?";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row()->name;
		return false;
	}
	
	/**
	 * Get details of active and inactive deals of all categories
	 * 
	 * @return bool|array array of deal details from DB. False on failure
	 */
	function getalldeals()
	{
		$sql="select distinct(brand.name),replace(item.url,' ','-') as url,item.orgprice,item.name as itemname,item.available,item.id as itemid,item.quantity,brand.name as brandname,item.price,cat.name as category,deal.tagline,deal.description,deal.dealid,deal.startdate,deal.enddate,item.pic,brand.id as brandid,brand.logoid as brandlogoid,deal.dealtype,brand.name from king_deals as deal join king_categories as cat on cat.id=deal.catid join king_brands as brand on brand.id=deal.brandid join king_dealitems as item on item.dealid=deal.dealid where deal.publish=1 and ".time()." between deal.startdate and deal.enddate order by deal.enddate asc limit 25";
//		$sql="select cat.name as category,deal.dealid,deal.startdate,deal.enddate,deal.pic,brand.name from king_deals as deal join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where deal.publish=1 and deal.enddate>".time()." order by deal.enddate asc";
		$q=$this->db->query($sql);
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	function getdod()
	{
		$sql="select item.slots,item.shipsin,deal.enddate,item.description1,item.description2,brand.logoid,item.ratings,item.reviews,item.url,item.orgprice,item.name,item.pic,item.price,item.id as id,brand.name as brandname,cat.name as category,cat.url as caturl from king_deals as deal join king_dealitems as item on item.dealid=deal.dealid join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where deal.publish=1 and (".time()." between deal.startdate and deal.enddate) and deal.enddate <= ".mktime(23,59,59)." limit 1";
		$r=$this->db->query($sql)->row_array();
		if(empty($r))
			$r=$this->db->query("select rand()*100 as rid,item.slots,item.shipsin,deal.enddate,item.description1,item.description2,brand.logoid,item.ratings,item.reviews,item.url,item.orgprice,item.name,item.pic,item.price,item.id as id,brand.name as brandname,cat.name as category,cat.url as caturl from king_deals as deal join king_dealitems as item on item.dealid=deal.dealid and item.live=1 join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where deal.publish=1 and (".time()." between deal.startdate and deal.enddate) order by rid asc limit 1")->row_array();
		return $r;
	}
	
	function geticonset()
	{
		if(($iconset=$this->pettakam->get("iconset"))===false)
		{
			for($i=0;$i<3;$i++)
			{
				$sql="select rand() as rid,i.price,i.slots,i.name,i.url,i.pic from king_deals d join king_dealitems i on i.dealid=d.dealid where ? between d.startdate and d.enddate and d.publish=1";
				if($i==0)
					$sql.=" and i.price>=15000";
				elseif($i==1)
					$sql.=" and i.price>5000 and i.price<15000";
				else 
					$sql.=" and i.price<=5000";
				$sql.=" order by rid asc limit 10";
				$deals=$this->db->query($sql,time())->result_array();
				$iconset[$i]=array();
				foreach($deals as $deal)
				{
					$deal['price']=$this->getslotprice($deal['slots'],2432342,$deal['price']);
					unset($deal['rid']);
					unset($deal['slots']);
					$iconset[$i][]=$deal;
				}
			}
			$this->pettakam->store("iconset",$iconset,2*60*60);
		}
		return $iconset;
	}
	
	function getslotprice($strslots,$buyers,$price=0)
	{
			$slotprice=0;
			$slots=unserialize($strslots);
	     	$nslots=array();
	     	$nslotprice=array();
	     	if(is_array($slots))
	     	foreach($slots as $sno=>$srs)
	     	{
	     		$nslots[]=$sno;
	     		$nslotprice[]=$srs;
	     	}
			foreach($nslots as $si=>$ns)
			{
				if($buyers<$ns)
					break;
			}
			if(isset($si))
			$slotprice=$nslotprice[$si];
			if($slotprice!=0)
			$refund=$slotprice;
			else
			$refund=$price;
			
			return $refund;
	}
	
	function getrandomdeals($len=false)
	{
		$sql="select rand()*100 as rid,item.url,item.orgprice,item.name,item.pic,deal.catid,item.price,item.id as id,cat.name as category,cat.url as caturl from king_deals as deal join king_dealitems as item on item.dealid=deal.dealid join king_categories as cat on cat.id=deal.catid where deal.publish=1 and ".time()." between deal.startdate and deal.enddate order by rid asc";
		if($len)
			$sql.=" limit $len";
		$deals=$this->db->query($sql)->result_array();
		return $deals;
	}
	
	function getbodyparts($sex,$list=false)
	{
		$s=array("male"=>1,"female"=>2);
		$sex=$s[$sex];
		$ds=$this->db->query("select i.name,i.id,d.catid,i.pic,i.url,i.orgprice as mrp from king_dealitems i join king_deals d on ".time()." between d.startdate and d.enddate and d.publish=1 and d.dealid=i.dealid where i.bodyparts=$sex and i.live=1 and i.quantity>i.available")->result_array();
		$ret=array();
		if(!$list)
		foreach($ds as $d)
		{
			$mcat=$this->db->query("select m.name from king_categories c join king_categories m on m.type=0 and (m.id=c.type or m.id=c.id) where c.id=?",$d['catid'])->row()->name;
			if(!isset($ret[$mcat]))
				$ret[$mcat]=array();
			$ret[$mcat][]=$d;
		}
		if($list)
		foreach($ds as $d)
			$ret[]=$d['id'];
		return $ret;
	}
	
	function getbodypartscats($sex)
	{
		$s=array("male"=>1,"female"=>2);
		$sex=$s[$sex];
		$ds=$this->db->query("select i.name,i.id,d.catid,i.pic,i.url,i.orgprice as mrp from king_dealitems i join king_deals d on ".time()." between d.startdate and d.enddate and d.publish=1 and d.dealid=i.dealid where i.bodyparts=$sex and i.live=1 and i.quantity>i.available")->result_array();
		$ret=array();
		foreach($ds as $d)
		{
			$mcat=$this->db->query("select m.id from king_categories c join king_categories m on m.type=0 and (m.id=c.type or m.id=c.id) where c.id=?",$d['catid'])->row()->id;
			$ret[]=$mcat;
		}
		return $ret;
	}
	
	function getmaincatid($itemid)
	{
		return $this->db->query("select m.id from king_dealitems i join king_deals d on d.dealid=i.dealid join king_categories c on c.id=d.catid join king_categories m on (m.id=c.type or m.id=c.id) and m.type=0 where i.id=?",$itemid)->row()->id;
	}
	
	function getsidepopper()
	{
		$rd=rand(0,1);
		$sql="select rand() as rid,i.orgprice as mrp,i.name,i.price,i.pic,i.url from king_deals d join king_dealitems i on i.dealid=d.dealid where ".time()." between d.startdate and d.enddate and d.publish=1 and ".($rd==0?"i.is_featured":"i.favs")."=1 and d.dealid=i.dealid order by rid desc limit 1";
		$ret=$this->db->query($sql)->row_array();
		$ret['type']=$rd;
		return $ret;
	}
	
	function dobpcheckout($sex)
	{
		$cats=$this->dbm->getbodypartscats($sex);
		$idlist=$this->dbm->getbodyparts($sex,true);
		$itemids=explode(",",$this->input->post("itemids"));
		foreach($itemids as $itemid)
		{
			if(!in_array($itemid,$idlist) || !in_array($this->dbm->getmaincatid($itemid),$cats))
			{ 
				$data['page']="info";
				$data['info']=array("Error","We are unable to process your request");
				$this->load->view("index",$data);
				return;
			}
		}
		$this->cart->destroy();
		$this->session->set_userdata("bodyparts_checkout",$sex);
		$user=$this->session->userdata("user");
		if(!$user)
		{
			$user=$this->dbm->getuserbyemail("guest@localcircle.in");
			$user['corp']=$this->dbm->getcorpname($user['corpid']);
		}
    	foreach($itemids as $itemid)
    	{
			$qty=1;
			$opts=array();
			$itemdetails=$this->dbm->getitemdetails($itemid);
			if(!$itemdetails)
				continue;
			$uids=$emails=array();
			$buyers=$qty;
			$refund=0;
			
			$bpid=$this->dbm->startbuyprocess($qty,$uids,$refund,$itemdetails,$emails,array(),true);
			
			if($bpid)
			{
				$bpuid=$this->db->query("select id from king_buyprocess where bpid=? and userid=?",array($bpid,$user['userid']))->row()->id;
				$bp=$this->db->query("select quantity,refund from king_m_buyprocess where id=?",$bpid)->row();
				$opts["bpid"]=$bpid;
				$opts['bpuid']=$bpuid;
			}
			else {$bp->refund=0;$bp->quantity=0;}
			$refund=$bp->refund;
	
			$name=$itemdetails['name'];
			$price=$itemdetails['price'];
			$cart=array("id"=>$itemdetails['id'],'qty'=>$qty,"price"=>$price,"name"=>$name,"options"=>$opts);
			$ret=$this->cart->insert($cart);
    	}
    	redirect("shoppingcart");
	}
	
	function getnewproducts()
	{
		if(($new=$this->pettakam->get("new-products"))===false)
		{
			$new=$this->db->query("select i.name,i.url from king_deals d join king_dealitems i on i.dealid=d.dealid and is_pnh = 0 where ? between d.startdate and d.enddate and d.publish=1 order by d.sno desc limit 12",time())->result_array();
			$this->pettakam->store("new-products",$new,4*3600,"deals");
		}
		return $new;
	}
	
	function getsidepaneforspotlight()
	{
		if(($brands=$this->pettakam->get("sidepane"))===false)
		{
			$brands['brands']=$this->db->query("select count(bp.id),b.name,b.url from king_m_buyprocess bp join king_dealitems i on i.id=bp.itemid join king_deals d on d.dealid=i.dealid and ".time()." between d.startdate and d.enddate and d.publish=1 join king_brands b on b.id=d.brandid where is_pnh = 0 group by d.brandid order by count(bp.id) desc limit 12")->result_array();
			$brands['cats']=$this->db->query("select count(bp.id),b.name,b.url from king_m_buyprocess bp join king_dealitems i on i.id=bp.itemid join king_deals d on d.dealid=i.dealid and ".time()." between d.startdate and d.enddate and d.publish=1 join king_categories b on b.id=d.catid where is_pnh = 0  group by d.catid order by count(bp.id) desc limit 12")->result_array();
			$this->pettakam->store("sidepane",$brands,10*3600,"deals");
		}
		return $brands;
	}
	
	function getdealsforspotlight()
	{
		if(($deals=$this->pettakam->get("spotlight"))===false)
		{
			
			$menu=$this->db->query("select id from king_menu where status=1 and name not like 'exclusive' and id!=".GIFTCARD_MENU)->result_array();
			
			$deals=array();
			foreach($menu as $m)
				$deals[]=$this->db->query("select rand() as rid,m.id,i.pic,i.name,m.url as murl,i.url,m.name as menu from king_menu m join king_deals d on (d.menuid=m.id or d.menuid2=m.id) and ".time()." between d.startdate and d.enddate and d.publish=1 join king_dealitems i on i.dealid=d.dealid and i.live=1 and is_pnh = 0 where m.id=? order by rid desc limit 1",$m['id'])->row_array();
				
			foreach($deals as $i=>$d)
				$deals[$i]['cats']=$this->db->query("select rand() as rid,c.name,c.url from king_deals d join king_dealitems i on i.dealid=d.dealid and is_pnh = 0 join king_categories sc on sc.id=d.catid join king_categories c on (c.id=sc.id or c.id=sc.type) and c.type=0 where (d.menuid=? or d.menuid2=?) and ".time()." between d.startdate and d.enddate and d.publish=1 group by c.id order by rid asc limit 4",array($d['id'],$d['id']))->result_array();
				
				
			$specials=array("shaving care","body care","makeup accessories","hair accessories");
			$cdeals=array();
			foreach($specials as $m)
			{
				$row=$this->db->query("select rand() as rid,m.id,i.pic,i.name,m.url as murl,i.url,m.name as menu from king_categories m join king_categories c on c.type=m.id join king_deals d on (d.catid=c.id) and ".time()." between d.startdate and d.enddate and d.publish=1 join king_dealitems i on i.dealid=d.dealid and i.live=1 and is_pnh = 0 where m.name like ? order by rid desc limit 1",$m)->row_array();
				if(!empty($row))
					$cdeals[]=$row;
			}
			
			foreach($cdeals as $i=>$d)
				$cdeals[$i]['cats']=$this->db->query("select rand() as rid,c.name,c.url from king_deals d join king_dealitems i on i.dealid=d.dealid and is_pnh = 0 join king_categories c on c.id=d.catid and c.type=? where ".time()." between d.startdate and d.enddate and d.publish=1 group by d.catid order by rid asc limit 4",array($d['id']))->result_array();

			foreach($cdeals as $c)
				$deals[]=$c;
				
			$this->pettakam->store("spotlight",$deals,15*60,"deals");
			
		}

		return $deals;
	}
	
/*	
	function getdealsforspotlight()
	{
		if(($deals=$this->pettakam->get("spotlight"))===false)
		{
			$menus=$this->db->query("select name,id from king_menu where status=1 and name!='exclusive' and name!='accessories' order by priority desc")->result_array();
			foreach($menus as $m)
			{
				$sql="select rand() as rid,item.live,item.groupbuy,item.url,item.orgprice,item.name,item.pic,deal.catid,item.price,item.id as id,cat.name as category,cat.url as caturl from king_deals as deal join king_dealitems as item on item.dealid=deal.dealid join king_categories as cat on cat.id=deal.catid where deal.publish=1 and ".time()." between deal.startdate and deal.enddate and (deal.menuid=? or deal.menuid2=?) order by rid desc limit 5";
				$deals[strtolower($m['name'])]=$this->db->query($sql,array($m['id'],$m['id']))->result_array();
			}
			$cats=$this->db->query("select name,id from king_categories where id=56 or id=89")->result_array();
			foreach($cats as $m)
			{
				$sql="select rand() as rid,item.live,item.groupbuy,item.url,item.orgprice,item.name,item.pic,deal.catid,item.price,item.id as id,cat.name as category,cat.url as caturl from king_deals as deal join king_dealitems as item on item.dealid=deal.dealid join king_categories as cat on cat.id=deal.catid where deal.publish=1 and ".time()." between deal.startdate and deal.enddate and deal.catid=? order by rid desc limit 5";
				$deals[strtolower($m['name'])]=$this->db->query($sql,array($m['id']))->result_array();
			}
			$this->pettakam->store("spotlight",$deals,10*60,"deals");
		}
		return $deals;
	}
*/
		
	function getdealsforwidget()
	{
		if(($deals=$this->pettakam->get("spotlight"))===false)
		{
			$sql="select item.url,item.orgprice,item.live,item.name,item.pic,deal.catid,item.price,item.id as id,cat.name as category,cat.url as caturl from king_deals as deal join king_dealitems as item on item.dealid=deal.dealid join king_categories as cat on cat.id=deal.catid where deal.publish=1 and ".time()." between deal.startdate and deal.enddate order by deal.sno desc";
			$deals=$this->db->query($sql)->result_array();
			$this->pettakam->store("spotlight",$deals,60*60,"deals");
		}
		
		$ret=$uc=array();
		foreach($deals as $deal)
		{
			$cat=$deal['catid'];
			if(!isset($cdeals[$cat]))
				$cdeals[$cat]=array();
			$cdeals[$cat][]=$deal;
		}

		$i=0;
		foreach($cdeals as $cat=>$c)
		{
			$uc[]=array_shift($cdeals[$cat]);
			$i++;
			if($i>4)
			 break;
		}
		$i=1;
		$ddeals=array();
		shuffle($cdeals);
		foreach($cdeals as $cat=>$c)
			if(count($c)>3)
			{
				$l=3;
				if($i%2==0)
					$l=2;
				$ds=array();
				for($i2=0;$i2<$l;$i2++)
					$ds[]=array_pop($c);
				$ddeals[]=$ds;
				$i++;
			}
		foreach($uc as $u)
			$ret[]=$u;
		foreach($ddeals as $c)
			foreach($c as $d)
				$ret[]=$d;
		return $ret;
	}
	
	function getcwpurchases($userid,$corpid)
	{
		$sql="select o.time as lastbuy_on,i.*,i.name,u.name as coworker from king_orders o join king_users u on u.userid=o.userid join king_dealitems i on i.id=o.itemid where o.userid!=? and u.corpid=? order by lastbuy_on desc limit 5";
		return $this->db->query($sql,array($userid,$corpid))->result_array();
	}
	
	function getcoupon($cid)
	{
		$sql="select * from king_coupons where id=?";
		return $this->db->query($sql,array($cid))->row_array();
	}
	
	function getminmaxprice($id)
	{
		$sql="select min(price) as min, min(orgprice) as minorg, max(orgprice) as maxorg, max(price) as max from king_dealitems where dealid=?";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row_array();
		return array();
	} 
	
	/**
	 * Get deal details for given deal id
	 * 
	 * Makes a join DB query to get details of deal and items with respect to given deal id 
	 * 
	 * @param int $id deal id
	 * @return bool|array Deal details as array. False on failure
	 */
	function getdealdetails($id)
	{
		$sql="select replace(item.url,' ','-') as url,deal.dealtype,deal.tagline,deal.brandid,cat.name as category,deal.startdate,deal.enddate,brand.name as brandname,brand.logoid as brandlogoid,deal.pic as dealpic,item.quantity,item.available,item.quantity,item.id,item.price,item.orgprice,item.name as itemname,item.pic,deal.description from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where item.dealid=? order by item.price asc";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	/**
	 * Get all orders made by given user
	 * 
	 * Also contains all details of the order made including item details
	 * 
	 * @param int $uid user id
	 * @return array order details as array
	 */
	function getorders($uid,$agent=false,$p=1)
	{
		if(!$agent)
		$sql="select ordert.transid,ordert.id,ordert.shiptime,ordert.shipid,ordert.paid,ordert.quantity,ordert.status,ordert.time,item.name,item.tax,item.service_tax,item.service_tax_cod,brand.name as brandname from king_orders as ordert join king_dealitems as item on item.id=ordert.itemid join king_deals as deal on deal.dealid=item.dealid join king_brands as brand on brand.id=deal.brandid where ordert.userid=? order by ordert.time desc limit ".(($p-1)*20).",20";
		else
		$sql="select trans.via_transid,ordert.id,ordert.shiptime,ordert.shipid,ordert.paid,ordert.quantity,ordert.status,ordert.time,item.name,brand.name as brandname from king_orders as ordert join king_dealitems as item on item.id=ordert.itemid join king_agent_transactions as trans on trans.orderid=ordert.id join king_deals as deal on deal.dealid=item.dealid join king_brands as brand on brand.id=deal.brandid where ordert.userid=? order by ordert.time desc limit ".(($p-1)*20).",20";
		$q=$this->db->query($sql,array($uid));
		return $q->result_array();
	}
	
	function userlog($uid)
	{
		if($this->db->query("select 1 from king_userlog where userid=?",$uid)->num_rows()==0)
			$this->db->query("insert into king_userlog(userid,ip_time_data) values(?,?)",array($uid,$_SERVER['REMOTE_ADDR'].":".time()));
		else 
			$this->db->query("update king_userlog set ip_time_data=concat_ws(',',?,ip_time_data) where userid=?",array($_SERVER['REMOTE_ADDR'].":".time(),$uid));
		$ua=$_SERVER['HTTP_USER_AGENT'];
		$this->db->query("update king_userlog set ip=?,useragent=?,last_login=? where userid=? limit 1",array($_SERVER['REMOTE_ADDR'],$ua,time(),$uid));
	}
	
	function getallorders($uid)
	{
		return $this->db->query("select p.points,p.status as pstatus,t.transid,t.init,t.amount as transamount,o.bpid,bpu.quantity as bpuqty,bp.quantity as bpqty,bp.expires_on as bpexpires,bp.quantity_done,bp.refund,bp.refund_given,bpu.isrefund as bpuisrefund, bpu.status as bpustatus,o.*,i.name as item,i.pic,bp.status as bpstatus from king_orders o join king_dealitems i on i.id=o.itemid join king_m_buyprocess bp on bp.id=o.bpid join king_buyprocess bpu on bpu.userid=o.userid and bpu.bpid=o.bpid join king_transactions t on t.transid=o.transid left outer join king_points p on p.transid=t.transid where o.userid=? group by t.transid order by t.id desc",$uid)->result_array();
	}
	
	function coupon_create($value,$type,$mode,$min,$unlimited,$expires)
	{
		$code=strtoupper("SE".randomChars(8));
		$inp=array($code,$type,$value,"","",$mode,$min,$unlimited,time(),$expires,"subscribe");
		$this->db->query("insert into king_coupons(code,type,value,brandid,catid,mode,status,min,unlimited,created,expires,remarks)
															values(?,?,?,?,?,?,0,?,?,?,?,?)",$inp);
		return $code;
	}
	
	function subscr_email($email)
	{
		if($this->db->query("select 1 from king_subscr_email where email=?",$email)->num_rows()==0)
		{
			$this->db->query("insert into king_subscr_email(email) values(?)",$email);
			if($this->db->query("select 1 from king_users where email=?",$email)->num_rows()==0)
			{
				$expires=time()+(SUBSCRIBE_COUPON_VALID*24*60*60);
				$code=$this->dbm->coupon_create(SUBSCRIBE_COUPON_VALUE,0,1,SUBSCRIBE_COUPON_MIN,1,$expires);
				$this->email($email,"Thank you for subscribing", $this->load->view("mails/subscribe_coupon",array("expires"=>$expires,"code"=>$code),true),true);
			}
		}
	}
	
	function subscr_mobile($mobile)
	{
		if($this->db->query("select 1 from king_subscr_mobile where mobile=?",$mobile)->num_rows()==0)
			$this->db->query("insert into king_subscr_mobile(mobile) values(?)",$mobile);
	}
	
	function getorder($oid,$uid)
	{
		$transid=$this->db->query("select transid from king_orders where id=?",$oid)->row_array();
		if(empty($transid))
			return array();
		$transid=$transid['transid'];
		$sql="select item.nlc,item.phc,ordert.*,item.price,item.tax,item.service_tax,item.service_tax_cod,item.name,brand.name as brandname,in.phc,in.nlc,in.service_tax,in.tax from king_orders as ordert join king_dealitems as item on item.id=ordert.itemid join king_deals as deal on deal.dealid=item.dealid join king_brands as brand on brand.id=deal.brandid left outer join king_invoice `in` on in.transid=ordert.transid and in.order_id=ordert.id where ordert.transid=?";
//		$sql="select ordert.*,item.price,item.tax,item.service_tax,item.service_tax_cod,item.name,brand.name as brandname from king_orders as ordert join king_dealitems as item on item.id=ordert.itemid join king_deals as deal on deal.dealid=item.dealid join king_brands as brand on brand.id=deal.brandid where ordert.userid=? and ordert.id=?";
		$q=$this->db->query($sql,array($transid));
		return $q->result_array();
	}
	
	/**
	 * Get brand name and id for given brand name
	 * 
	 * @param string $name brand name
	 * @return bool|array array indexed with name and id. False on failure
	 */
	function getbrand($name)
	{
		$sql="select description,website,logoid,name,id from king_brands where name=?";
		$q=$this->db->query($sql,array($name));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	/**
	 * Get category name and id for given category name
	 * 
	 * @param string $name category name
	 * @return bool|array array indexed with name and id. False on failure
	 */
	function getcategory($name)
	{
		$sql="select name,id from king_categories where name=?";
		$q=$this->db->query($sql,array($name));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	/**
	 * Get all active and inactive deals for given brand id
	 * 
	 * @param int $brandid brand id
	 * @return bool|array details of all deals for brand. False on failure
	 */
	function getdealsforbrand($brandid)
	{
		$sql="select deal.dealtype,category.name as categoryname,category.url as caturl,deal.startdate,deal.enddate,deal.dealid,deal.brandid,deal.pic,deal.description,deal.tagline from king_deals as deal join king_categories as category on category.id=deal.catid where deal.brandid=? and deal.publish=1 and deal.enddate>".time()." order by deal.enddate desc";
//		$sql="select deal.dealid,deal.startdate,deal.enddate,deal.pic,brand.name from king_deals as deal join king_brands as brand on brand.id=deal.brandid where deal.catid=? and deal.publish=1 and deal.enddate>".time()." order by deal.enddate asc";
		$q=$this->db->query($sql,array($brandid));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	/**
	 * Get all active and inactive deals for given category id
	 * 
	 * @param int $catid category id
	 * @return bool|array details of all deals in category. False on failure
	 */
	function getdealsbycategory($catid)
	{
		$sql="select replace(item.url,' ','-') as url,item.groupbuy,item.id as itemid,item.quantity,item.available,item.name as itemname,deal.dealtype,deal.tagline,deal.dealid,deal.startdate,deal.enddate,item.pic,brand.name from king_deals as deal join king_brands as brand on brand.id=deal.brandid join king_dealitems as item on item.dealid=deal.dealid where (deal.catid=? or deal.catid in (select id from king_categories where type=?)) and deal.publish=1 and deal.enddate>".time()." order by deal.enddate asc";
		$q=$this->db->query($sql,array($catid,$catid));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	function getcatforurl($url)
	{
		$r=$this->db->query("select name from king_categories where url=?",$url)->row_array();
		if(empty($r))
			return false;
		return $r['name'];
	}
	
	function getbrandforurl($url)
	{
		$r=$this->db->query("select name from king_brands where url=?",$url)->row_array();
		if(empty($r))
			return false;
		return $r['name'];
	}
	
	function getdealsbycaturl($url)
	{
		if(($deals=$this->pettakam->get("category-$url"))===false)
		{
			$cat=$this->db->query("select type,id from king_categories where url=?",$url)->row();
			$type=$cat->type;
			$cat=$cat->id;
			if($type!=0)
				$sql="select b.url as brandurl,i.sno as itemid,d.catid,b.name as brandname,d.brandid,i.groupbuy,i.live,i.name,i.price,i.orgprice,i.pic,i.url,c.url as caturl,c.name as category from king_categories as c join king_deals as d on c.id=d.catid and ? between d.startdate and d.enddate and d.publish=1 join king_dealitems as i on i.dealid=d.dealid join king_brands b on b.id=d.brandid where c.id=? order by d.sno desc";
			else 
				$sql="select b.url as brandurl,i.sno as itemid,d.catid,b.name as brandname,d.brandid,i.groupbuy,i.live,i.name,i.price,i.orgprice,i.pic,i.url,c.url as caturl,c.name as category from king_categories as c join king_deals as d on c.id=d.catid and ? between d.startdate and d.enddate and d.publish=1 join king_dealitems as i on i.dealid=d.dealid join king_brands b on b.id=d.brandid where c.type=? or c.id=? order by d.sno desc";
			$deals=$this->db->query($sql,array(time(),$cat,$cat))->result_array();
			$this->pettakam->store("category-$url",$deals,2*60*60,"deals");
		}
		return $deals;
	}
	
	function getdealsbycatbrandurl($curl,$burl)
	{
		if($this->uri->segment(3) || ($deals=$this->pettakam->get("cat-brand-$burl-$curl"))===false)
		{
			$sql="select d.catid,b.url as brandurl,d.brandid,b.name as brandname,i.live,i.id as itemid,d.pic,i.groupbuy,i.url,i.name,i.orgprice,i.price,c.url as caturl,c.name as category from king_categories as c join king_categories sc on sc.type=c.id or sc.id=c.id join king_brands as b on b.url=? join king_deals as d on sc.id=d.catid and d.brandid=b.id and ? between d.startdate and d.enddate and d.publish=1 join king_dealitems as i on i.dealid=d.dealid where c.url=?";
			$inp=array($burl,time(),$curl);
			if($this->uri->segment(3))
			{
				$p=$this->uri->segment(3);
				$ps=explode("-",$p);
				if(count($ps)==3)
				{
					$sql.=" and i.price between ? and ?";
					$inp[]=trim($ps[1]);$inp[]=trim($ps[2]);
				}
				else
				{
					if(count(explode("lt",$ps[1]))==2)
					{
						$sql.=" and i.price<?";
						list($d,$inp[])=explode("lt",$ps[1]);
					}
					else
					{
						$sql.=" and i.price>?";
						list($d,$inp[])=explode("gt",$ps[1]);
					}
				}
			}
			$sql.=" order by d.sno desc";
			$deals=$this->db->query($sql,$inp)->result_array();
			
			if(!$this->uri->segment(3))
				$this->pettakam->store("cat-brand-$burl-$curl",$deals,4*60*60,"deals");
		}
		return $deals;
	}
	
	function getdealsbymenubrandurl($murl,$burl)
	{
		if($this->uri->segment(3) || ($deals=$this->pettakam->get("menu-brand-$burl-$murl"))===false)
		{
			$sql="select d.catid,b.url as brandurl,d.brandid,b.name as brandname,i.live,i.id as itemid,d.pic,i.groupbuy,i.url,i.name,i.orgprice,i.price,c.url as caturl,c.name as category from king_categories as c join king_menu m on m.url=? join king_deals as d on c.id=d.catid and (d.menuid=m.id or d.menuid2=m.id) and ? between d.startdate and d.enddate and d.publish=1 join king_dealitems as i on i.dealid=d.dealid join king_brands b on d.brandid=b.id where b.url=?";
			$inp=array($murl,time(),$burl);
			if($this->uri->segment(3))
			{
				$p=$this->uri->segment(3);
				$ps=explode("-",$p);
				if(count($ps)==3)
				{
					$sql.=" and i.price between ? and ?";
					$inp[]=trim($ps[1]);$inp[]=trim($ps[2]);
				}
				else
				{
					if(count(explode("lt",$ps[1]))==2)
					{
						$sql.=" and i.price<?";
						list($d,$inp[])=explode("lt",$ps[1]);
					}
					else
					{
						$sql.=" and i.price>?";
						list($d,$inp[])=explode("gt",$ps[1]);
					}
				}
			}
			$sql.=" order by d.sno desc";
			$deals=$this->db->query($sql,$inp)->result_array();
			if(!$this->uri->segment(3))
				$this->pettakam->store("menu-brand-$burl-$murl",$deals,4*60*60,"deals");
		}
		return $deals;
	}
	
	
	function getdealsbybrandurl($url)
	{
		if($this->uri->segment(3) || ($deals=$this->pettakam->get("brand-$url"))===false)
		{
			$sql="select b.url as brandurl,d.catid,d.brandid,b.name as brandname,i.live,i.id as itemid,d.pic,i.groupbuy,i.url,i.name,i.orgprice,i.price,c.url as caturl,c.name as category from king_categories as c join king_deals as d on c.id=d.catid and ? between d.startdate and d.enddate and d.publish=1 join king_dealitems as i on i.dealid=d.dealid join king_brands b on d.brandid=b.id where b.url=?";
			$inp=array(time(),$url);
			if($this->uri->segment(3))
			{
				$p=$this->uri->segment(3);
				$ps=explode("-",$p);
				if(count($ps)==3)
				{
					$sql.=" and i.price between ? and ?";
					$inp[]=trim($ps[1]);$inp[]=trim($ps[2]);
				}
				else
				{
					if(count(explode("lt",$ps[1]))==2)
					{
						$sql.=" and i.price<?";
						list($d,$inp[])=explode("lt",$ps[1]);
					}
					else
					{
						$sql.=" and i.price>?";
						list($d,$inp[])=explode("gt",$ps[1]);
					}
				}
			}
			$sql.=" order by d.sno desc";
			$deals=$this->db->query($sql,$inp)->result_array();
			if(!$this->uri->segment(3))
				$this->pettakam->store("brand-$url",$deals,2*60*60,"deals");
		}
		return $deals;
	}
	
	function getmenuforurl($url)
	{
		$r=$this->db->query("select concat(prepos,' ',name) as name from king_menu where url=? and status=1",$url)->row_array();
		if(empty($r))
			return false;
		return $r['name'];
	}
	
	function getallmenu()
	{
		return $this->db->query("select * from king_menu where status=1")->result_array();
	}
	
	function getallcats()
	{
		return $this->db->query("select distinct(d.catid),c.name,c.url from king_deals as d join king_categories as c on c.id=d.catid where ? between d.startdate and d.enddate and d.publish=1 order by c.name asc",array(time()))->result_array();
	}
	
	function getbrands()
	{
		if(($brands=$this->pettakam->get("brands"))===false)
		{
		$brands=$this->db->query("select distinct(d.brandid),brand.name,brand.url from king_deals d join king_brands brand on brand.id=d.brandid where ".time()." between d.startdate and d.enddate and publish=1 order by brand.name asc")->result_array();
		$this->pettakam->store("brands",$brands,12*3600);
		}
		return $brands;
	}
	
	function getitemloves($itemid)
	{
		$sql="select rand() as rid,b.username,b.pic as bpic,p.pic as ppic from king_item_lovers l left outer join king_profiles p on p.userid=l.userid left outer join king_boarders b on b.userid=l.userid where itemid=? order by rid desc limit 5";
		return $this->db->query($sql,$itemid)->result_array();
	}
	
	function getrecentsold()
	{
		if(($ret=$this->pettakam->get("recentsold"))===false)
		{
			$ret=$this->db->query("select i.id as itemid,c.name as category,i.live,i.groupbuy,c.url as caturl,rand() as rid,i.url,i.name,i.orgprice,i.price,i.pic from king_orders o join king_dealitems i on i.id=o.itemid and i.live=1 and is_pnh = 0 join king_deals d on d.dealid=i.dealid and ".time()." between d.startdate and d.enddate and d.publish=1 join king_categories c on c.id=d.catid group by o.itemid order by o.time desc,rid asc limit 10")->result_array();
			$this->pettakam->store("recentsold",$ret,10*60);
		}
		return $ret;
	}
	
	function gettopproducts()
	{
		if(($ret=$this->pettakam->get("topproducts"))===false)
		{
			$ret=$this->db->query("select i.live,i.groupbuy,c.url as caturl,rand() as rid,i.url,i.name,i.orgprice,i.price,i.pic from king_dealitems i join king_deals d on d.dealid=i.dealid and ".time()." between d.startdate and d.enddate and d.publish=1 join king_categories c on c.id=d.catid where i.live=1 order by i.buys desc limit 12")->result_array();
			$this->pettakam->store("topproducts",$ret,10*60);
		}
		return $ret;
	}
	
	function getmenucats()
	{
		$this->benchmark->mark('menu');
		if(($ret=$this->pettakam->get("menu"))===false)
		{
			$menu=$this->db->query("select * from king_menu where status=1 order by priority desc")->result_array();
			$ret[0]=$menu;
			foreach($menu as $m)
			{
				if($m['id']==GIFTCARD_MENU)
					continue;
				$cats_raw=$this->db->query("select rand() as rid,c.id,c.type,c.name,c.url from king_deals as d join king_dealitems di on di.dealid = d.dealid and is_pnh = 0 join king_categories as c on c.id=d.catid where (d.menuid=? or d.menuid2=?) and ? between d.startdate and d.enddate and d.publish=1 group by c.id order by rid,c.name asc limit 10",array($m['id'],$m['id'],time()))->result_array();
				$cats=array();
				$brands=array();
				$maincats=array();
				$ret[1][$m['id']]['cats']=array();
				foreach($cats_raw as $i=>$c)
				{
					if($c['type']==0)
						$maincats[]=$c['id'];
					else
						$maincats[]=$c['type'];
					$cats[]=$c['id'];
				}
				$ret[1][$m['id']]['mcats']=array(); 
				$mc_a=array();
				foreach($cats_raw as $mc)
					$mc_a[]=$mc['name'];
				asort($mc_a);
				foreach($mc_a as $i=>$ma)
					$ret[1][$m['id']]['cats'][]=$cats_raw[$i];
				
				$best=$this->db->query("select rand() as rid,i.name,i.pic,i.url,i.price,i.name from king_deals d join king_dealitems i on i.dealid=d.dealid and i.live=1 and is_pnh=0 where ? between d.startdate and d.enddate and d.publish=1 and (d.menuid=? or d.menuid2=?) order by rid desc limit 1",array(time(),$m['id'],$m['id']))->result_array();
				$ret[1][$m['id']]['top']=array_shift($best);

				$mcats=$this->db->query("select rand() as rid,name,url from king_categories where id in (".implode(",",$maincats).") order by rid desc limit 5")->result_array();
				$ret[1][$m['id']]['mcats']=array(); 
				$mc_a=array();
				foreach($mcats as $mc)
					$mc_a[]=$mc['name'];
				asort($mc_a);
				foreach($mc_a as $i=>$ma)
					$ret[1][$m['id']]['mcats'][]=$mcats[$i];

				
				$ret[1][$m['id']]['mbrands']=$mcats=$this->db->query("select rand() as rid,b.id,b.name,b.url from king_deals as d join king_dealitems di on d.dealid  = di.dealid and is_pnh = 0 join king_brands as b on b.id=d.brandid where (d.menuid=? or d.menuid2=?) and ? between d.startdate and d.enddate and d.publish=1 group by b.id order by rid asc limit 5",array($m['id'],$m['id'],time()))->result_array();
				$ret[1][$m['id']]['mbrands']=array(); 
				$mc_a=array();
				foreach($mcats as $mc)
					$mc_a[]=$mc['name'];
				asort($mc_a);
				foreach($mc_a as $i=>$ma)
					$ret[1][$m['id']]['mbrands'][]=$mcats[$i];
				
				$ret[1][$m['id']]['cats_count']=$this->db->query("select distinct d.catid from king_deals as d join king_dealitems di on d.dealid  = di.dealid and is_pnh = 0 join king_categories as c on c.id=d.catid where (d.menuid=? or d.menuid2=?) and ? between d.startdate and d.enddate and d.publish=1",array($m['id'],$m['id'],time()))->num_rows();
				$ret[1][$m['id']]['brands_count']=$this->db->query("select distinct d.brandid from king_deals as d join king_dealitems di on d.dealid  = di.dealid and is_pnh = 0 join king_brands as c on c.id=d.brandid where (d.menuid=? or d.menuid2=?) and ? between d.startdate and d.enddate and d.publish=1",array($m['id'],$m['id'],time()))->num_rows();
				
			}
			$cache=array("cache"=>time(),"menu"=>$ret);
			$this->pettakam->store("menu",$ret,4*3600);
		}
		$this->benchmark->mark('menu1');
		return $ret;
	}
	
	function getdealsbycatnmenuurl($murl,$curl)
	{
		if($this->uri->segment(3) || ($deals=$this->pettakam->get("menucat-$murl-$curl"))===false)
		{
			$menu=$this->db->query("select id from king_menu where url=?",$murl)->row()->id;
			$cat=$this->db->query("select id,type from king_categories where url=?",$curl)->row();
			$type=$cat->type;
			$cat=$cat->id;
			if($type==0)
			{
				$sql="select b.url as brandurl,i.sno as itemid,b.name as brandname,i.price,d.catid,d.brandid,c.name as category, c.url as caturl,i.live,i.groupbuy,i.name,i.url,i.price,i.orgprice,i.pic,m.name as menu from king_menu as m join king_categories as c on c.type=? or c.id=? join king_deals as d on d.catid=c.id and (d.menuid=m.id or d.menuid2=m.id) and ? between d.startdate and d.enddate and d.publish=1 join king_dealitems as i on i.dealid=d.dealid join king_brands b on b.id=d.brandid where m.id=?";
				$inp=array($cat,$cat,time(),$menu);
			}	
			else
			{
				$sql="select b.url as brandurl,i.sno as itemid,b.name as brandname,i.price,d.catid,d.brandid,c.name as category, c.url as caturl,i.live,i.groupbuy,i.name,i.url,i.price,i.orgprice,i.pic,m.name as menu from king_menu as m join king_deals as d on (d.menuid=m.id or d.menuid2=m.id) and ? between d.startdate and d.enddate and d.publish=1 join king_dealitems as i on i.dealid=d.dealid join king_categories as c on c.id=d.catid and c.id=? join king_brands b on b.id=d.brandid where m.id=?";
				$inp=array(time(),$cat,$menu);
			}
			if($this->uri->segment(3))
			{
				$p=$this->uri->segment(3);
				$ps=explode("-",$p);
				if(count($ps)==3)
				{
					$sql.=" and i.price between ? and ?";
					$inp[]=trim($ps[1]);$inp[]=trim($ps[2]);
				}
				else
				{
					if(count(explode("lt",$ps[1]))==2)
					{
						$sql.=" and i.price<?";
						list($d,$inp[])=explode("lt",$ps[1]);
					}
					else
					{
						$sql.=" and i.price>?";
						list($d,$inp[])=explode("gt",$ps[1]);
					}
				}
			}
			$sql.=" order by d.sno desc";
			$deals=$this->db->query($sql,$inp)->result_array();
			if(!$this->uri->segment(3))
				$this->pettakam->store("menucat-$murl-$curl",$deals,2*60*60,"deals");
		}
		return $deals;
	}
	
	function getbpbyhash($hash)
	{
		$sql="select bp.*,bpu.*,bpu.status as bpu_status,bpu.id as bpuid from king_buyprocess bpu join king_m_buyprocess bp on bp.id=bpu.bpid where bpu.hash=?";
		return $this->db->query($sql,$hash)->row_array();
	}
	
	function getshipcity($items)
	{
		$all=array();
		foreach($items as $item)
		{
			$id=$item['id'];
			$ships=$this->db->query("select shipsto from king_dealitems where id=?",$id)->row()->shipsto;
			if(empty($ships))
				continue;
			$cities=explode(",",$ships);
			$all[]=$cities;
		}
		if(empty($all))
			return false;
		$valids=array();
		foreach($all as $a)
		{
			if(empty($valids))
			{
				$valids=$a;
				continue;
			}
			$valids=array_intersect($valids, $a);
		}
		return $valids;
	}
	
	function getdealsbymenuurl($url)
	{
		if(($deals=$this->pettakam->get("menu-$url"))===false)
		{
			$menu=$this->db->query("select id from king_menu where url=?",$url)->row()->id;
			$sql="select rand() as rid,i.groupbuy,c.name as category, c.url as caturl,c.id as catid,i.live,d.brandid,i.groupbuy,i.name,i.price,i.orgprice,i.url,i.pic from king_deals as d join king_dealitems as i on i.dealid=d.dealid join king_categories as c on c.id=d.catid where (d.menuid=? or d.menuid2=?) and ? between d.startdate and d.enddate and d.publish=1 order by rid desc";
			$deals=$this->db->query($sql,array($menu,$menu,time()))->result_array();
			$this->pettakam->store("menu-$url",$deals,2*60*60,"deals");
		}
		$ret=array();
		$cdeals=array();
		foreach($deals as $deal)
		{
			$cat=$deal['catid'];
			if(!isset($cdeals[$cat]))
				$cdeals[$cat]=array();
			$cdeals[$cat][]=$deal;
		}
		foreach($cdeals as $c)
			foreach($c as $d)
				$ret[]=$d;
		return $ret;
	}
	
	function getexclusivedeals()
	{
		if(($deals=$this->pettakam->get("exclusive-deals"))===false)
		{
			$sql="select c.name as category, c.url as caturl,i.name,i.url,i.pic,i.price,i.live,i.orgprice,m.name as menu from king_menu as m join king_deals as d on (d.menuid=m.id or d.menuid2=m.id) and ? between d.startdate and d.enddate and d.publish=1 join king_dealitems as i on i.dealid=d.dealid join king_categories as c on c.id=d.catid where m.name=? order by d.sno desc";
			$deals=$this->db->query($sql,array(time(),"exclusive"))->result_array();
			$this->pettakam->store("exclusive-deals",$deals,2*60*60,"deals");
		}
		return $deals;
	}
	
	function getgroupdeals($limit=0)
	{
		$sql="select brand.name as brandname,brand.logoid,replace(item.url,' ','-') as url,item.id as itemid,item.quantity,item.available,item.name as itemname,deal.dealtype,deal.tagline,deal.dealid,deal.startdate,deal.enddate,item.pic,brand.name from king_deals as deal join king_brands as brand on brand.id=deal.brandid join king_dealitems as item on item.dealid=deal.dealid where deal.dealtype='1' and deal.publish='1' and (".time()." between deal.startdate and deal.enddate) order by deal.enddate asc";
		if($limit!=0)
			$sql.=" limit $limit";
		return $this->db->query($sql)->result_array();
	}
	
	function do_add_trend($key,$deals=array())
	{
		$key=substr(trim(strtolower(preg_replace('/[^a-zA-Z0-9\s\-]/', '', $key))),0,20);
		if($this->db->query("select 1 from king_trends where name=?",$key)->num_rows()==0)
			$this->db->query("insert into king_trends(name,listed_on) values(?,?)",array($key,time()));
		if(!empty($deals))
		{
			$ids=array();
			foreach($deals as $d)
				$ids[]=$d['itemid'];
			$ids=implode(",",$ids);
			$this->db->query("update king_trends set deals=?,updated_on=? where name=?",array($ids,time(),$key));
		}
		$this->db->query("update king_trends set hits=hits+1 where name=? limit 1",$key);
	}
	
	function gettrends()
	{
		if(($trends=$this->pettakam->get("top-trends"))===false)
		{
			$r_trends=$this->db->query("select name from king_trends order by updated_on desc, hits desc limit 20")->result_array();
			$trends=array();
			foreach($r_trends as $t)
				$trends[]=$t['name'];
			$this->pettakam->store("top-trends",$trends,20);
		}
		return $trends;
	}
	
	function gettrend($name)
	{
		$trend=$this->db->query("select id from king_trends where name=?",$name)->row_array();
		if(empty($trend))
			return false;
		$tid=$trend['id'];
		$deals=array();
		if(($deals=$this->pettakam->get("trend-$tid"))===false)
		{
			$trend=$this->db->query("select name,deals from king_trends where id=?",$tid)->row_array();
			if(!empty($trend['deals']))
				$deals=$this->db->query("select b.url as brandurl,i.id as itemid,i.live,b.name as brandname,d.catid,d.brandid,d.pic,i.groupbuy,i.url,i.name,i.orgprice,i.price,c.url as caturl,c.name as category from king_categories as c join king_deals as d on c.id=d.catid and ? between d.startdate and d.enddate and d.publish=1 join king_brands b on b.id=d.brandid join king_dealitems as i on i.dealid=d.dealid and i.id in (".$trend['deals'].") order by d.sno desc",time())->result_array();
			$this->pettakam->store("trend-$tid",$deals,2*60*60);
		}
		$this->db->query("update king_trends set hits=hits+1 where id=? limit 1",$tid);
		return $deals;
	}
	
	function getsearchcount($okey)
	{
		$okey=str_replace(","," ",$okey);
		$keys=explode(" ",$okey);
		$fkey=array();
		foreach($keys as $k)
		{
			$k=str_replace("'","",$k);
			$fkey[]=$k;
		}
		$key=implode(" ",$fkey);
		$sql="select count(1) as l from king_search_index ind where MATCH (ind.name,ind.keywords) AGAINST (? in boolean mode)";
		$ret=$this->db->query($sql,array($key,$key))->row()->l;
		return $ret;
	}
	
	function searchdeals($okey,$all=true)
	{
		$dkey=$okey;

		$menu=false;
		if($this->input->post("menu"))
		{
			$m=$this->db->query("select id from king_menu where url=?",$this->input->post("menu"))->row_array();
			if(!empty($m))
				$menu=$m['id'];
		}
		
		$okey=str_replace(","," ",$okey);
		$keys=explode(" ",$okey);
		$fkey=array();
		foreach($keys as $k)
		{
			$k=str_replace("'","",$k);
//			if(strlen($k)>2)
//				$k="$k*";
			$fkey[]=$k;
		}
		if($all)
		$key="+".implode(" +",$fkey);
		else
		$key=implode(" ",$fkey);
		$sql="select deal.catid,item.live,item.groupbuy,cat.name as category,cat.url as caturl,item.url,item.orgprice,item.name as itemname,item.available,item.id as itemid,item.quantity,brand.name as brandname,item.price,cat.name as category,deal.tagline,deal.dealid,deal.startdate,deal.enddate,item.pic,brand.url as brandurl,brand.id as brandid,brand.logoid as brandlogoid,deal.dealtype,item.name from king_search_index ind join king_dealitems as item on item.id=ind.itemid join king_deals as deal on deal.dealid=item.dealid".($menu?" and (deal.menuid=$menu or deal.menuid2=$menu)":"")." join king_categories as cat on cat.id=deal.catid join king_brands as brand on brand.id=deal.brandid where MATCH (ind.name,ind.keywords) AGAINST (? in boolean mode)";
		$ret=$this->db->query($sql,array($key,$key))->result_array();
		
		if(empty($ret))
		{
			$key1="% $okey%";
			$key2="$okey%";
			$sql="select deal.catid,item.live,item.groupbuy,cat.name as category,cat.url as caturl,item.url,item.orgprice,item.name as itemname,item.available,item.id as itemid,item.quantity,brand.name as brandname,item.price,cat.name as category,deal.tagline,deal.dealid,deal.startdate,deal.enddate,brand.url as brandurl,item.pic,brand.id as brandid,brand.logoid as brandlogoid,deal.dealtype,item.name from king_deals as deal join king_categories as cat on cat.id=deal.catid join king_brands as brand on brand.id=deal.brandid join king_dealitems as item on item.dealid=deal.dealid where (item.name like ? or item.name like ?) and deal.publish=1 and ".time()." between deal.startdate and deal.enddate".($menu?" and (deal.menuid=$menu or deal.menuid2=$menu)":"")." order by deal.sno asc limit 25";
			$ret=$this->db->query($sql,array($key1,$key2))->result_array();
		}
		
		if(!empty($ret))
			$this->do_add_trend($okey,$ret);
		return $ret;
	}
	
	function searchcats($key)
	{
		$key="%$key%";
		$sql="select * from king_categories where name like ?";
		return $this->db->query($sql,array($key,$key))->result_array();
	}
	
	function searchbrands($key)
	{
		$key="%$key%";
		$sql="select * from king_brands where name like ?";
		return $this->db->query($sql,array($key,$key))->result_array();
	}
	
	/**
	 * Get extra details of item (photos & videos)
	 * 
	 * @param int $id item id
	 * @return bool|array False on failure
	 */
	function getitemresources($id)
	{
		$sql="select type,id from king_resources where itemid=?";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	/**
	 * Saves cart to DB
	 * 
	 * @param int $uid user id
	 * @param string $name cart name
	 * @return int cart id
	 * @deprecated
	 */
	function asdadsavecart($uid,$name)
	{
		$sql="insert into king_savedcarts(userid,name) values(?,?)";
		$this->db->query($sql,array($uid,$name));
		return $this->db->insert_id();
	}
	
	/**
	 * Save cart items to DB
	 * 
	 * @param int $cartid cart id
	 * @param int $uid user id
	 * @param array $items array of item ids to save
	 * @return void
	 */
	function savecartitems($cartid,$uid,$items)
	{
		if(!is_array($items) || count($items)==0)
			return;
		$sql="insert into king_savedcartitems(cartid,itemid,quantity) values";
		$i=0;
		$arr=array();
		foreach($items as $item)
		{
			if($i==0)
				$sql.='(?,?,?)';
			else
				$sql.=',(?,?,?)';
			$i++;
			array_push($arr,$cartid);
			array_push($arr,$item['id']);
			array_push($arr,$item['qty']);
		}
		$this->db->query($sql,$arr);
	}
	
	/**
	 * Get all saved carts for given user id
	 * 
	 * @param int $uid user id
	 * @return array
	 */
	function getsavedcarts($uid)
	{
		$sql="select * from king_savedcarts where userid=?";
		$q=$this->db->query($sql,array($uid));
		return $q->result_array();
	}
	
	/**
	 * Get all items for saved cart for given cart id
	 * 
	 * @param int $id cart id
	 * @return array
	 */
	function getsavedcart($id)
	{
		$sql="select item.id,cart.cartid,carti.quantity as qty,item.name,cart.name as cartname,item.price from king_savedcartitems as carti join king_savedcarts as cart on cart.cartid=carti.cartid join king_dealitems as item on item.id=carti.itemid where carti.cartid=?";
		$q=$this->db->query($sql,array($id));
		return $q->result_array();
	}
	
	/**
	 * Get item details for given item id
	 * 
	 * Details include category,brand and deal details
	 * 
	 * @param int $id item id
	 * @return bool|array false on failure
	 */
	function getitemdetails($id,$published=true)
	{
		$user=$this->session->userdata("user");
		$sql="select deal.discontinued, deal.featured_start,deal.featured_end,deal.publish,deal.is_giftcard,item.cashback,item.max_allowed_qty,item.favs,item.loves,item.groupbuy,item.cod,item.sizing,item.shipsto,deal.keywords,deal.menuid,deal.menuid2,0 as cbbuys,item.snapits,item.buys,item.bp_expires,item.slots,item.ratings,item.reviews,item.live,item.shipsin,replace(item.url,' ','-') as url,deal.vendorid,deal.catid,deal.dealtype,deal.brandid,deal.startdate,deal.enddate,cat.name as category,brand.logoid as brandlogoid,brand.name as brandname,brand.url as burl,item.id,item.price,item.orgprice,item.name,item.quantity,item.pic,item.tagline,item.available,item.description1,item.description2,item.dealid,deal.catid,deal.startdate,deal.enddate,m.name as menu,m.url as murl,cat.url as caturl from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid".($published?" and deal.publish=1":"")." join king_menu m on m.id=deal.menuid join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where item.id=?";
		$q=$this->db->query($sql,array($id,time()));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function getcashbacks($userid)
	{
		$sql="select c.min,c.code,c.value,c.expires,i.name as item from king_coupons c left outer join king_dealitems i on i.id=c.itemid where c.userid=? and c.status=0 and c.expires>".time();
		return $this->db->query($sql,$userid)->result_array();
	}
	
	function savecart()
	{
		$user=$this->session->userdata("user");
		if(!$user)
			return;
		if($this->db->query("select 1 from king_carts where userid=?",$user['userid'])->num_rows()==0)
			$this->db->query("insert into king_carts(userid) values(?)",$user['userid']);

		$dump=array();
		$items=$this->cart->contents();
		foreach($items as $item)
		{
			$d=array("id"=>$item['id'],"qty"=>$item['qty'],"price"=>$item['price'],"name"=>$item['name'],'options'=>$this->cart->product_options($item['rowid']));
			$dump[]=$d;
		}	
		$dump=serialize($dump);
		$this->db->query("update king_carts set cart=?,updated=".time()." where userid=?",array($dump,$user['userid']));
	}
	
	function loadcart()
	{
		$user=$this->session->userdata("user");
		if(!$user)
			return;
		$cart=$this->db->query("select cart from king_carts where userid=?",$user['userid'])->row_array();
		if(empty($cart))
			return;
		$cart=unserialize($cart['cart']);
		
		$ids=array();
		foreach($cart as $c)
			$ids[]=$c['id'];
		
		foreach($this->cart->contents() as $c)
			if(in_array($c['id'], $ids))
				$this->cart->update(array("rowid"=>$c['rowid'],"qty"=>0));
		
		$this->cart->insert($cart);
	}
	
	function getusedcoupons($userid)
	{
		$sql="select distinct uc.transid,uc.coupon,o.time from king_used_coupons uc join king_orders o on o.userid=? and o.transid=uc.transid where uc.status=1";
		return $this->db->query($sql,$userid)->result_array();
	}
	
	function getitemurl($id)
	{
		return $this->db->query("select url from king_dealitems where id=?",$id)->row()->url;
	}
	
	function startbuyprocess($qty,$uids,$refund,$item,$inv_emails,$fbs=array(),$m=false,$relbuys=array())
	{
		$user=$this->session->userdata("user");
		if(!$user)
		{
			$user=$this->dbm->getuserbyemail("guest@localcircle.in");
			$user['corp']=$this->dbm->getcorpname($user['corpid']);
		}

		if(isset($fbs['email']))
		{
			$fbemail=$fbs['email'];
			$fbs=$fbs['ids'];
		}
		
		$total_qty=$qty+count($inv_emails);
		$isrefund=1;
		if(!empty($uids))
			$total_qty=$qty+count($uids);
		else 
			$isrefund=0;
		if(!empty($fbs))
			$total_qty+=count($fbs);
			
		$sql="insert into king_m_buyprocess(itemid,quantity,refund,status,started_by,started_on,expires_on) values(?,?,?,?,?,?,?)";
		
		$expires=time()+$item['bp_expires'];
		if($expires>$item['enddate'])
			$expires=$item['enddate'];
		
		$this->db->query($sql,array($item['id'],$total_qty,$refund,0,$user['userid'],time(),$expires));
		$bpid=$this->db->insert_id();
		
		$emails=$this->db->query("select name,userid,email from king_users where userid in ('".implode("','",$uids)."')")->result_array();
		$inp=array($bpid,$qty,$user['userid'],random_string("unique"),$isrefund,0);
		$this->db->query("insert into king_buyprocess(bpid,quantity,userid,hash,isrefund,status) values(?,?,?,?,?,?)",$inp);
		if(!empty($inv_emails))
		foreach($inv_emails as $iemail)
			$emails[]=array("name"=>substr($iemail,0,strpos($iemail,"@")),"email"=>$iemail,"userid"=>0);
		if(!empty($fbs))
		foreach($fbs as $fb)
		{
			$fbname=$this->db->query("select name from king_facebookers where fbid=?",$fb)->row_array();
			if(empty($fbname))
				continue;
			$emails[]=array("name"=>$fbname['name'],"email"=>"","userid"=>0,"facebook"=>$fb);
		}
		foreach($emails as $email)
		{
			$hash=random_string("unique");
			$inp=array($bpid,1,$email['userid'],$hash,0);
			$this->db->query("insert into king_buyprocess(bpid,quantity,userid,hash,status) values(?,?,?,?,?)",$inp);
			if($_SERVER['HTTP_HOST']!="localhost" && $email['email']!="")
				$this->email($email['email'], "{$user['name']} is buying {$item['name']} @ snapittoday.com and has invited you to buy with him to get Group Discount", $this->load->view("mails/bpinvite",array("name"=>$email['name'],"refund"=>$refund,"price"=>$item['price'],"hash"=>$hash,"item"=>$item,"total_qty"=>$total_qty,"user"=>$user,"expires"=>$expires),true));
			if(isset($email['facebook']))
			{
				$msg=$this->load->view("mails/fb_bpinvite",array("name"=>$email['name'],"refund"=>$refund,"price"=>$item['price'],"hash"=>$hash,"item"=>$item,"total_qty"=>$total_qty,"user"=>$user,"expires"=>$expires),true);
				$this->db->query("insert into king_fb_mails(`from`,`to`,sub,msg,status,time,expires_on) values(?,?,?,?,?,?,?)",array($fbemail,$email['facebook'], "{$user['name']} is buying {$item['name']} @ snapittoday.com and has invited you to buy with him to get Group Discount",$msg,0,time(),time()+3*60*60));
			}
		}
		if(!$m)
			echo json_encode(array("bpid"=>$bpid));

		if(is_array($relbuys))	
		foreach($relbuys as $rbuy)
		{
			list($r_item,$r_qty)=explode("-",$rbuy);
			$sql="insert into king_m_buyprocess(itemid,quantity,refund,status,started_by,started_on,expires_on) values(?,?,?,?,?,?,?)";
			$this->db->query($sql,array($r_item,$r_qty,0,0,$user['userid'],time(),time()+(24*60*60)));
			$r_bpid=$this->db->insert_id();
			$inp=array($r_bpid,$r_qty,$user['userid'],random_string("unique"),$isrefund,0);
			$this->db->query("insert into king_buyprocess(bpid,quantity,userid,hash,isrefund,status) values(?,?,?,?,?,?)",$inp);
		}	
			
		return $bpid;
	}
	
	function extendbuyprocess($bpid,$cws_rw,$inv_emails)
	{
		$user=$this->session->userdata("user");
		$mbp=$this->db->query("select * from king_m_buyprocess where id=?",$bpid)->row_array();
		if(empty($mbp))
			die;
		$expires=$mbp['expires_on'];
		$bps=$this->db->query("select * from king_buyprocess where bpid=?",$bpid)->result_array();
		$bp_cws=array();
		$cws=array();
		foreach($bps as $bp)
			$bp_cws[]=$bp['userid'];
		if(!empty($cws_rw))
			foreach($cws_rw as $cw)
			{
				if(array_search($cw, $bp_cws)===false)
					$cws[]=$cw;
			}
		$buyers=count($cws)+count($inv_emails);
		$item=$this->dbm->getitemdetails($mbp['itemid']);
		$total_qty=$buyers+$mbp['quantity'];
		$slotprice=$this->getslotprice($item['slots'], $buyers+$mbp['quantity']);
		$refund=$item['price']-$slotprice;
		$this->db->query("update king_m_buyprocess set quantity=quantity+?,refund=? where id=?",array($buyers,$refund,$bpid));


		$emails=$this->db->query("select name,userid,email from king_users where userid in ('".implode("','",$cws)."')")->result_array();
		if(!empty($inv_emails))
		foreach($inv_emails as $iemail)
			$emails[]=array("name"=>substr($iemail,0,strpos($iemail,"@")),"email"=>$iemail,"userid"=>0);
		foreach($emails as $email)
		{
			$hash=random_string("unique");
			$inp=array($bpid,1,$email['userid'],$hash,0);
			$this->db->query("insert into king_buyprocess(bpid,quantity,userid,hash,status) values(?,?,?,?,?)",$inp);
			if($_SERVER['HTTP_HOST']!="localhost")
				$this->email($email['email'], "{$user['name']} is buying {$item['name']} @ snapittoday.com and has invited you to buy with him to get Group Discount", $this->load->view("mails/bpinvite",array("name"=>$email['name'],"refund"=>$refund,"price"=>$item['price'],"hash"=>$hash,"item"=>$item,"total_qty"=>$total_qty,"user"=>$user,"expires"=>$expires),true));
		}
		
		
	}
	
	function checkpricereq($uid,$deal)
	{
		$sql="select 1 from king_pricereqs where userid=? and itemid=? and status=0";
		if($this->db->query($sql,array($uid,$deal))->num_rows()==0)
			return false;
		return true;
	}
	
	function newpricereq($uid,$iid,$price,$qua)
	{
		$sql="insert into king_pricereqs(userid,itemid,price,quantity,time,status) values(?,?,?,?,?,0)";
		$this->db->query($sql,array($uid,$iid,$price,$qua,time()));
	}
	
	function getextradeals($catid,$brandid,$vendorid,$id)
	{
		$sql="select cat.url as caturl,replace(item.url,' ','-') as url,deal.catid,deal.dealtype,deal.brandid,deal.startdate,deal.enddate,cat.name as category,brand.logoid as brandlogoid,brand.name as brand,item.id,item.price,item.orgprice,item.name,item.quantity,item.pic,item.tagline,item.available,item.description1,item.description2,item.dealid,deal.catid,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid and deal.publish=1 join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where (deal.catid=? or deal.brandid=?) and ".time()." between deal.startdate and deal.enddate and item.id!=? and deal.publish=1 limit 4";
		$ret=$this->db->query($sql,array($catid,$brandid,$id))->result_array();
		$ar=array($id);
		if(count($ret)<4)
		{
			$sql="select cat.url as caturl,replace(item.url,' ','-') as url,deal.catid,deal.dealtype,deal.brandid,deal.startdate,deal.enddate,cat.name as category,brand.logoid as brandlogoid,brand.name as brand,item.id,item.price,item.orgprice,item.name,item.quantity,item.pic,item.tagline,item.available,item.description1,item.description2,item.dealid,deal.catid,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid and deal.publish=1 join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where ".time()." between deal.startdate and deal.enddate and item.id!=?";
			foreach($ret as $r)
			{
				$sql.=" and item.id!=?";
				$ar[]=$r['id'];
			} 
			$sql.=" and deal.publish=1 order by rand() asc limit 4";
			$r2=$this->db->query($sql,$ar)->result_array();
			foreach($r2 as $r)
			{
				if(count($ret)==4)
					break;
				$ret[]=$r;
			}
		}
		return $ret;
	}
	
	function getitemdetailsforcomments($id)
	{
		$sql="select deal.dealtype,item.url,deal.startdate,deal.enddate,cat.name as category,brand.name as brandname,item.id,item.price,item.orgprice,item.name,item.quantity,item.pic,item.tagline,item.description1,item.description2,item.dealid,deal.catid,deal.startdate,deal.enddate from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid and deal.publish=1 join king_brands as brand on brand.id=deal.brandid join king_categories as cat on cat.id=deal.catid where item.id=? and deal.startdate<?";
		$q=$this->db->query($sql,array($id,time()));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function getagent($uid)
	{
		$sql="select user.mobile,user.email,user.inviteid,user.special,agent.via_uid as aid,user.name,user.userid from king_agents as agent join king_users as user on user.userid=agent.userid where agent.via_uid=?";
		return $this->db->query($sql,array($uid))->row_array();
	}
	function updateagent($uid,$aid,$name,$balance,$mobile,$email,$address,$city,$state,$pincode)
	{
		$sql="update king_users set name=?,mobile=?,email=?,address=?,city=?,state=?,pincode=? where userid=?";
		$this->db->query($sql,array($name,$mobile,$email,$address,$city,$state,$pincode,$uid));
		$sql="update king_agents set name=?,balance=? where via_uid=?";
		$this->db->query($sql,array($name,$balance,$aid));
	}
	
	function getwidget($salt)
	{
		$sql="select * from king_widgets where salt=? limit 1";
		return $this->db->query($sql,array($salt))->row_array();
	}
	
	function createagent($uid,$name,$balance)
	{
		$sql="insert into king_users(name,special,createdon,inviteid) values(?,?,?,?)";
		$this->db->query($sql,array($name,4,time(),randomChars(10)));
		$userid=$this->db->insert_id();
		$sql="insert into king_agents(userid,via_uid,name,balance,created_date,last_login) values(?,?,?,?,?,?)";
		$this->db->query($sql,array($userid,$uid,$name,$balance,time(),time()));
	}

	function redirect_login()
	{
		if($this->session->userdata("bps"))
		{
			$bps=$this->session->userdata("bps");
			redirect("buy/".$bps["hash"]);
		}
		if(($r=$this->session->userdata("login_redirect")))
		{
			$this->session->unset_userdata("login_redirect");
			redirect($r);
		}
		if($this->cart->total()!=0)
			redirect("checkout");
		redirect("spotlight");
	}
	
	/**
	 * Check for special (fb,google,twitter) user in DB
	 * 
	 * @param mixed $uid user id from facebook or google or twitter
	 * @param int $type special user type (facebook=1 twitter=2 google=30
	 * @return bool|int Internal user id on success. False on failure. 
	 */
	function checkspecialuser($uid,$type)
	{
		$sql="select userid from king_users where special_id=? and special=?";
		$q=$this->db->query($sql,array($uid,$type));
		if($q->num_rows()==1)
		{
			$r=$q->row_array();
			return $r['userid'];
		}
		return false;
	}
	
	/**
	 * Update view deals order for ksale page
	 * 
	 * @param int $uid userid
	 * @param int $did deal id
	 * @return void
	 */
	function updateviewdeal($uid,$did)
	{
		$sql="select viewdeals from king_miscusers where userid=?";
		$q=$this->db->query($sql,array($uid));
		if($q->num_rows()==1)
		{
			$r=$q->row();
			$deals=explode("/",$r->viewdeals);
			if(isset($deals[0]))
			{
				if($deals[count($deals)-1]!=$did)
				{
					if(count($deals)>=5)
					array_shift($deals);
					array_push($deals,$did);
					$sdeals=implode("/",$deals);
					$sql="update king_miscusers set viewdeals=? where userid=? limit 1";
					$this->db->query($sql,array($sdeals,$uid));
				}
			}
		}
	}
	
	function getitemforurl($url)
	{
		$sql="select id from king_dealitems where url=? limit 1";
		$q=$this->db->query($sql,array($url));
		if($q->num_rows()==1)
			return $q->row()->id;
		return 0;
	}
	
	/**
	 * Creates new special user by inserting user details to users & specialusers table
	 *   
	 * @param mixed $uid userid from google or twitter or facebook
	 * @param string $name user name
	 * @param int $type special user type
	 * @param string $inviteId invitation id for the use
	 * @return void
	 */
	function Old_newspecialuser($uid,$name,$type,$inviteId)
	{
		$sql="insert into king_users(name,inviteid,special,createdon) values(?,?,?,?)";
		$this->db->query($sql,array($name,$inviteId,$type,time()));
		$sql="select max(userid) from king_users";
		$q=$this->db->query($sql);
		$r=$q->row_array();
		$userid=$r['max(userid)'];
		$sql="insert into king_specialusers values(?,?,?)";
		$this->db->query($sql,array($userid,$uid,$type));
	}
	
	function newfbuser($name,$email,$inviteid,$friendof,$fb_user)
	{
		$sql="insert into king_users(name,email,inviteid,friendof,fb_userid,createdon) values(?,?,?,?,?,?)";
		$q=$this->db->query($sql,array($name,$email,$inviteid,$friendof,$fb_user,time()));
	}
	
	/**
	 * Function to update user address and mobile after check out
	 * 
	 * @param int $uid userid
	 * @param int $mobile mobile number
	 * @param string $address address
	 * @return void
	 */
	function updateuseraddress($uid,$mobile,$address)
	{
		$sql="update king_users set mobile=?, address=? where userid=? limit 1";
		$q=$this->db->query($sql,array($mobile,$address,$uid));
	}
	
	/**
	 * Checkout function
	 * 
	 * Places order for given items and address with respect to user id
	 * 
	 * @param int $uid userid
	 * @param array $items array of item ids
	 * @param string $address delivery address
	 * @return bool|int Order id on success. False on failure
	 */
	function checkout($uid,$items,$data)
	{
		$id=array();
		$vid=array();
		foreach($items as $i)
		{
			$sql="select d.brandid,d.vendorid from king_deals as d join king_dealitems as i on i.id=? where d.dealid=i.dealid";
			$r=$this->db->query($sql,array($i['id']))->row();
			$id[]=$r->brandid;
			$vid[]=$r->vendorid;
		}
		$sql="insert into king_orders(id,userid,itemid,quantity,brandid,status,time,ship_person,ship_address,ship_city,ship_pincode,ship_phone,bill_person,bill_address,bill_city,bill_pincode,bill_phone,vendorid,paid,email,i_orgprice,i_price,i_nlc,i_tax,i_phc,i_discount) values";
		$i=0;
		$inp=array();
		$itemid=array();
		foreach($items as $item)
		{
			if($i!=0)
				$sql.=",";
			$sql.="(?,$uid,?,?,?,0,".time().",?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$inp[]=$orderid=rand(10000,44033302);
			array_push($inp,$item['id']);
			$itemid[]=$item['id'];
			array_push($inp,$item['qty']);
//			array_push($inp,$address);
			$inp[]=$id[$i];
			$da=array("person","address","city","pincode","phone","bill_person","bill_address","bill_city","bill_pincode","bill_phone");
//			$dv=array("ship_person","ship_address","ship_city","ship_pincode","ship_phone","bill_person","bill_address","bill_city","bill_pincode","bill_phone");
			foreach($da as $d)
				$inp[]=$data[$d];
			$inp[]=$vid[$i];
			$inp[]=$item['price']*$item['qty'];
			$inp[]=$data['email'];
			$inp[]=$data['i_orgprice'];
			$inp[]=$data['i_price'];
			$inp[]=$data['i_nlc'];
			$inp[]=$data['i_tax'];
			$inp[]=$data['i_phc'];
			$inp[]=$data['i_discount'];
			
			$i++;
		}
		$this->db->query($sql,$inp);	
		if($this->db->affected_rows()==0)
			return false;
		foreach($items as $item)
		{
			$this->db->query("update king_dealitems set available=available+".$item['qty']." where id=".$item['id']);
		}
//		$sql="update king_dealitems set available=available+1 where id=".implode(" or id=",$itemid);
//		$this->db->query($sql);
		return $orderid;
	}
	
	function getitemforcheckout($id)
	{
		$sql="select agentcom,orgprice,price,viaprice from king_dealitems where id=?";
		return $this->db->query($sql,array($id))->row_array();
	}
	
	function getsalesreport($aid,$start,$end)
	{
		$sql="select a.via_transid,a.orderid,a.qty,a.price,a.com,a.paid,a.time,o.ship_person,o.ship_address,o.ship_city,o.ship_pincode,i.name from king_agent_transactions as a join king_dealitems as i on i.id=a.itemid join king_orders as o on o.id=a.orderid where a.agentid=? and a.time between ? and ? order by a.time desc";
		return $this->db->query($sql,array($aid,$start,$end))->result_array();
	}

	function newagenttrans($aid,$qty,$price,$com,$amount,$tran,$oid,$itemid)
	{
		$sql="insert into king_agent_transactions(agentid,itemid,orderid,via_transid,price,com,time,qty,paid) values(?,?,?,?,?,?,?,?,?)";
		$this->db->query($sql,array($aid,$itemid,$oid,$tran,$price,$com,time(),$qty,$amount));
	}
	
	/**
	 * Get quantity available for specific item
	 * 
	 * @param array $items array of item ids
	 * @return array array combination of id and quantity pair
	 */
	function getitemsquantity($items)
	{
		$data=array();
		if(count($items)==0)
			return $data;
		$sql="select item.agentcom,item.shipsin,item.available,item.orgprice,item.price,item.id,item.quantity,item.pic,deal.enddate,deal.publish from king_dealitems as item join king_deals as deal on deal.dealid=item.dealid where item.id=?";
		$ids=array();
		$i=0;
		foreach($items as $item)
		{
			array_push($ids,$item['id']);
			if($i!=0)
			$sql.=' or id=?';
			$i=1;
		}
		$q=$this->db->query($sql,$ids);
		foreach($q->result_array() as $r)
		{
			$na=false;
			if($r['enddate']<time() || $r['publish']==0)
				$na=true;
			$data[$r['id']]=array('available'=>$r['available'],'quantity'=>$r['quantity'],'pic'=>$r['pic'],'na'=>$na,"orgprice"=>$r['orgprice'],"shipsin"=>$r['shipsin'],"agentcom"=>$r['agentcom']);
		}
		return $data;
	}
	
	function getaddress($id)
	{
		return $this->db->query("select * from king_address where id=?",array($id))->row_array();
	}
	
	function createaddress($data,$bool,$userid)
	{
		if($bool==0)
			$this->db->query("insert into king_address(userid,name,address,city,pincode,phone,shipbill) values(?,?,?,?,?,?,0)",array($userid,$data['person'],$data['address'],$data['city'],$data['pincode'],$data['phone']));
		else
			$this->db->query("insert into king_address(userid,name,address,city,pincode,phone,shipbill) values(?,?,?,?,?,?,1)",array($userid,$data['bill_person'],$data['bill_address'],$data['bill_city'],$data['bill_pincode'],$data['bill_phone']));
	}
	
	function getaddresses($uid)
	{
		$sql="select * from king_address where userid=? order by time desc";
		$adr=$this->db->query($sql,array($uid))->result_array();
		$ret[0]=array();
		$ret[1]=array();
		foreach($adr as $ad)
			$ret[$ad['shipbill']][]=$ad;
		return $ret;
	}
	
	/**
	 * Creates new user account in DB
	 * 
	 * @param string $email Email address
	 * @param string $name User name
	 * @param string $password Password
	 * @param int $mobile Mobile number
	 * @param string $inviteid Invitation id
	 * @param int $friendof friend of user id
	 * @return bool true on success
	 */
	function newuser($email,$name,$password,$mobile,$inviteid,$friendof,$corpemail="",$corpid=0,$notify=true)
	{
		$code=rand(100000000, 9999999999);
		$password=md5($password);
		$sql="insert into king_users(email,name,password,mobile,inviteid,friendof,corpemail,createdon,verify_code,corpid) values(?,?,?,?,?,?,?,?,?,?)";
		$q=$this->db->query($sql,array($email,$name,$password,$mobile,$inviteid,$friendof,$corpemail,time(),$code,$corpid));
		$userid=$this->db->insert_id();
		$bool=false;
		if($this->db->affected_rows()==1)
			$bool=true;
		if($bool==true)
		{
			$this->db->query("insert into king_profiles(userid,corpid) values(?,?)",array($this->db->insert_id(),$corpid));
			if($notify)
				$this->dbm->email(array($email,$corpemail),"Welcome to Snapittoday.com",$this->load->view("mails/firsttime",array("name"=>$name,"code"=>$code),true));
//			$this->dbm->email($email,"Access Code for Snapittoday.com","Dear $name,<br><br>Thank you for creating an account in snapittoday.com<br>Please use the access code $code to confirm this email<br><br>Snapittoday.com");
			$sql="insert into king_miscusers(logins,invites) values(0,0)";
			$this->db->query($sql);
		}
		
		$this->createreferralcoupon($name, $userid);

		return $bool;
	}
	
	
	function newspecialuser($name,$email,$password,$special,$special_id)
	{
		$invite=randomChars(10);
		$code=rand(100000000, 9999999999);
		$password=md5($password);
		$inps=array($name,$email,$password,$special,$special_id,$invite,$code,time());
		$this->db->query("insert into king_users(name,email,password,special,special_id,inviteid,verify_code,createdon) values(?,?,?,?,?,?,?,?)",$inps);
		$uid=$this->db->insert_id();
		if(!empty($email))
			$this->dbm->email(array($email),"Welcome to Snapittoday.com",$this->load->view("mails/firsttime",array("name"=>$name,"code"=>$code),true));
			
		$this->dbm->createreferralcoupon($name,$uid);
		
		return $uid;
	}
	
	function createreferralcoupon($name,$uid)
	{
		$inp=array("",COUPON_REFERRAL_VALUE,COUPON_REFERRAL_MIN,991231231231231,$uid,time());
		$name=preg_replace('/[^a-zA-Z0-9_\-]/','',$name);
		$code=trim(strtoupper(substr($name,0,7)));
		$code.=strtoupper(randomChars(10-strlen($code)));
		$inp[0]=$code;
		$this->db->query("insert into king_coupons(code,value,min,expires,referral,mode,created,unlimited) values(?,?,?,?,?,1,?,1)",$inp);
	}
	
	function getcreatecorp($email)
	{
		list($adasd,$corpemail)=explode("@",$email);
		$corpemail=strtolower($corpemail);
		if($this->db->query("select 1 from king_mail_providers where name =?",$corpemail)->num_rows()!=0)
			return 0;
		$r=$this->db->query("select id from king_corporates where email=?",$corpemail)->result_array();
		if(empty($r))
		{
			$this->db->query("insert into king_corporates(name,email) values(?,?)",array(ucfirst($corpemail),$corpemail));
			return $this->db->insert_id();
		}
		return $r[0]['id'];
	}
	
	/**
	 * get user detail by invite id
	 * 
	 * @param string $id invite id
	 * @return bool|array False on failure
	 */
	function getuserbyinviteid($id)
	{
		$sql="select * from king_users where inviteid=?";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function getresources($id)
	{
		$sql="select * from king_resources_old where roomid=? order by type asc";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()>0)
		{
			$data[0]=array();
			$data[1]=array();
			$res=$q->result_array();
			foreach($res as $r)
					array_push($data[$r['type']],$r['id']);
			return $data;
		}
		return false;
	}
	
	function getroom($id)
	{
		$sql="select * from king_roomdeals_old where roomid=? limit 1";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function getrooms($id)
	{
		$sql="select * from king_roomdeals_old where dealid=? order by price asc";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	function gethoteldeal($id)
	{
		$sql="select * from king_hoteldeals_old where dealid=? limit 1";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function updateext($uid,$email)
	{
		$sql="update king_users set email=? where userid=?";
		$this->db->query($sql,array($email,$uid));
	}
	
	function gethoteldeals()
	{
		$sql="select * from king_hoteldeals_old order by enddate ASC";
		$q=$this->db->query($sql);
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	/**
	 * Get user details for given user id
	 * 
	 * @param int $id userid
	 * @return bool|array False on failure
	 */
	function getuserbyid($id)
	{
		$sql="select corpid,verified,userid,mobile,name,email,inviteid,special from king_users where userid=?";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}


	/**
	 * Get user details by given email
	 * 
	 * @param string $email email address
	 * @return bool|array false on failure
	 */
	function getuserbyemail($email)
	{
		$sql="select corpid,verified,userid,mobile,name,email,inviteid,special from king_users where email=?";
		$q=$this->db->query($sql,array($email));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	
	/**
	 * Update user login count
	 * 
	 * @param int $id userid
	 * @return void
	 */
	function updateuserlogin($id)
	{
		$sql="update king_miscusers set `logins`=`logins`+1, lastlogin=NOW() where userid=?";
		$this->db->query($sql,array($id));
	}
	
	/**
	 * Update user profile
	 * 
	 * @param int $uid userid
	 * @param string $name user name
	 * @param string $password password
	 * @param int $mobile mobile number
	 * @return bool true on success
	 */
	function edituser($uid,$name,$password,$mobile)
	{
		$sql="update king_users set name=?, password=?, mobile=? where userid=? limit 1";
		$q=$this->db->query($sql,array($name,$password,$mobile,$uid));
		if($this->db->affected_rows()==1)
			return true;
		return false;
	}
	
	function getprofile($uid)
	{
		return $this->db->query("select * from king_profiles where userid=?",$uid)->row_array();
	}
	
	/**
	 * Get referals for given user id
	 * 
	 * @param int $id userid
	 * @return bool|array false on zero
	 */
	function getreferals($id)
	{
		$sql="select * from king_users where friendof=? order by userid asc";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()>0)
			return $q->result_array();
		return false;
	}
	
	function sms($no,$msg,$now=true)
	{
		return;
		
		//no execute
		if(!is_array($no))
			$no=array($no);
		
		if(!$now)
		{
			$sql="insert into sms_queue(number,msg) values(?,?)";
			foreach($no as $n)
				$this->db->query($sql,array($n,$msg));
			return;
		}
		
		$url="http://72.55.146.179/pfile/record.php?username=<username>&password=<password>&To=<mobile_number>&Text=<message_content>&senderid=<senderid>";
		$params=array(
				'username'=>'gova',
				'password'=>'gova12',
				'senderid'=>'SNAP-IT',
				'message_content'=>urlencode($msg)
				);
		foreach($params as $r=>$v)
			$url=str_replace("<{$r}>", $v, $url);
		foreach($no as $n)
		{
			if($no==0)
				continue;
			$lurl=str_replace("<mobile_number>",$n,$url);
			file_get_contents($lurl);
		}
	}
	
	function gettencoworkers($userid,$cid)
	{
		$alias=$this->db->query("select alias from king_corporates where id=?",$cid)->row()->alias;
		if($alias!=0)
			$cid=$alias;
		return $this->db->query("select u.*,p.*,p.pic,rand()*100 as r,i.name as item,i.price from king_users u left outer join king_profiles p on p.userid=u.userid left outer join king_dealitems i on i.id=p.lastbuy where u.corpid=? and u.userid!=? order by r asc limit 6",array($cid,$userid))->result_array();
	}
	
	function getcoworkerslen($userid,$cid)
	{
		$alias=$this->db->query("select alias from king_corporates where id=?",$cid)->row()->alias;
		if($alias!=0)
			$cid=$alias;
		return $this->db->query("select count(1) as l from king_users where corpid=? and userid!=? order by name asc",array($cid,$userid))->row()->l;
	}
	
	function getcoworkers($userid,$cid)
	{
		$alias=$this->db->query("select alias from king_corporates where id=?",$cid)->row()->alias;
		if($alias!=0)
			$cid=$alias;
		return $this->db->query("select * from king_users where corpid=? and userid!=? order by name asc",array($cid,$userid))->result_array();
	}
	
	function getcorpname($cid)
	{
		$sql="select name,alias from king_corporates where id=?";
		$r=$this->db->query("$sql",$cid)->row_array();
		if(empty($r))
			return "Unknown";
		if($r['alias']==0)
			return $r['name'];
		return $this->getcorpname($r['alias']);
	}
	
	/**
	 * Get last comment for given deal id
	 * 
	 * @param $dealid deal id
	 * @return bool|array false on no comments
	 */
	function getlastcommentfordeal($dealid)
	{
		return false;
		$sql="select COUNT('id') as countc, comment from king_comments where dealid=? order by id desc";
		$q=$this->db->query($sql,array($dealid));
		$r=$q->row_array();
		if(isset($r['comment']))
		return $q->row_array();
		return false;
	}

	function postcomment($comment,$user,$item,$deal)
	{
		$sql="insert into king_comments(comment,userid,dealid,itemid,time,flag) values(?,?,?,?,?,?)";
		$q=$this->db->query($sql,array($comment,$user,$deal,$item,time(),1));
	}
	
	function getlastcomments()
	{
		$sql="select com.id,user.special,com.itemid,item.pic,item.name as itemname,user.name as username,com.time,com.comment from king_comments as com join king_users as user on user.userid=com.userid join king_dealitems as item on item.id=com.itemid order by com.time desc limit 4";
		$q=$this->db->query($sql);
		if($q->num_rows()>0)
		return $q->result_array();
		else
		return array();
	}
	
	function newpreview($id,$dealid,$userid)
	{
		$sql="insert into king_dealpreviews(id,dealid,userid,time) values(?,?,?,?)";
		$q=$this->db->query($sql,array($id,$dealid,$userid,time()));
	}
	
	function getpreview($id)
	{
		$sql="select * from king_dealpreviews where id=?";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function getupcoming_old()
	{
		$sql="select cat.name as category,item.url,deal.dealid,deal.pic,deal.enddate,deal.startdate,brand.name from king_deals as deal join king_categories as cat on cat.id=deal.catid join king_brands as brand on brand.id=deal.brandid join king_dealitems as item on item.dealid=deal.dealid where deal.startdate>? and deal.publish=1 order by deal.startdate asc";
		$q=$this->db->query($sql,array(time()));
		if($q->num_rows()>0)
			return $q->result_array();
		else
			return array(); 
	}
	
	function api_auth($aid)
	{
		$sql="select * from king_api_logins where auth=?";
		$q=$this->db->query($sql,array(md5($aid)));
		if($q->num_rows()==1)
			return true;
		return false;
	}
	
	/**
	 * Get last comment for given item id
	 * 
	 * @param $itemid item id for which comments is queried
	 * @return bool|array false on no comments
	 */
	function getlastcommentforitem($itemid)
	{
		$sql="select com.id,user.special,user.name,com.time,com.comment from king_comments as com join king_users as user on user.userid=com.userid where com.itemid=? order by com.time desc limit 1";
		$q=$this->db->query($sql,array($itemid));
		if($q->num_rows()==1)
			return $q->row_array();
		return false;
	}
	
	function checkusedcoupon($userid,$coupon)
	{
		if($this->db->query("select 1 from king_used_coupons c join king_orders o on o.transid=c.transid and o.userid=? where c.coupon=? and c.status=1",array($userid,$coupon))->num_rows()!=0)
			return true;
		return false;		
	}
	
	function checkordercoupon()
	{
		$coupon=$this->session->userdata("coupon");
		if(!$coupon)
			return false;
		$user=$this->session->userdata("user");
		if(!$user)
			$user=$this->dbm->getuserbyemail($_POST['bill_email']);
		if(!$user)
			return false;
		$userid=$user['userid'];
		if($this->dbm->checkusedcoupon($userid,$coupon['code']))
			return true;
		return false;
	}
	
	function addhistory($id)
	{
		if(!isset($_COOKIE['history']))
			$_COOKIE['history']="";
		$history=explode(",",$_COOKIE['history']);
		if(in_array($id,$history))
			foreach($history as $i=>$h)
				if($id==$h)
					unset($history[$i]);
		array_unshift($history,$id);
		if(count($history)>MAX_HISTORY)
		{
			$tmp=$history;
			$history=array();
			foreach($tmp as $i=>$t)
				if($i<MAX_HISTORY)
					$history[]=$t;
		}
		$history=implode(",",$history);
		setcookie("history",$history,time()+(10*365*24*60*60),"/");
	}
	
	function gethistory($len=MAX_HISTORY)
	{
		if(!isset($_COOKIE['history']))
			$_COOKIE['history']="";
		$history=explode(",",$_COOKIE['history']);
		$t=$history;
		$history=array();
		foreach($t as $i=>$h)
			if(!empty($h) && $i<$len)
				$history[]=$h;
		if(empty($history))
			return array();
		return $this->db->query("select i.id,i.pic,i.url,i.name from king_dealitems i join king_deals d on d.dealid=i.dealid and ".time()." between d.startdate and d.enddate and d.publish=1 where i.id in (".implode(",",$history).") order by field(i.id,".implode(",",$history).")")->result_array();
	}
	
	function deletehistory($id)
	{
		if(!isset($_COOKIE['history']))
			$_COOKIE['history']="";
		$history=explode(",",$_COOKIE['history']);
		$t=array();
		foreach($history as $h)
			if($h!=$id)
				$t[]=$h;
		$history=implode(",",$t);
		setcookie("history",$history,time()+(10*365*24*60*60));
	}
	
	function getfeatured()
	{
		$ret=$this->db->query("select i.id,b.url as burl,b.name as brand,rand() as rid,i.url,i.name,d.pic,i.price from king_deals d join king_dealitems i on i.dealid=d.dealid and i.live=1 and is_pnh = 0 join king_brands b on b.id=d.brandid where d.publish=1 and ".time()." between d.startdate and d.enddate and ".time()." between d.featured_start and d.featured_end order by rid desc")->result_array();
		return $ret;
	}
	
	function getlivefeed()
	{
		$sess_hash=$this->session->userdata("live_hash_part");
		$json->hash=$this->input->post('hash');
		$json->feed=array();
		if(!$sess_hash)
			die(json_encode($json));
		$inp=array();
		$sql="select i.name,bp.id,bp.started_on as time,i.pic,i.url,i.id as itemid from king_m_buyprocess bp join king_dealitems i on i.id=bp.itemid";
		if($_POST)
		{
			$hash=base64_decode($this->input->post('hash'));
			$check=substr($hash,0,10);
			if($check!=$sess_hash[0])
				die(json_encode($json));
			$id=substr($hash,10)/$sess_hash[1];
			$inp[]=$id+1-1;
			$sql.=" where bp.id>?";
		}
		$sql.=" order by bp.id desc limit 25";
		$ret=$this->db->query($sql,$inp)->result_array();
		if(!empty($ret))
			$r_hash=base64_encode($sess_hash[0].($ret[0]['id']*$sess_hash[1]));
		if($_POST)
		{
			if(empty($ret))
				$json=array("hash"=>$this->input->post("hash"),"feed"=>$ret);
			else
			{
				$json->hash=$r_hash;
				foreach($ret as $r)
					$jr[]=$this->load->view("body/livefeed_item",array("f"=>$r),true);
				$json->feed=$jr;
			}
			die(json_encode($json));
		}
		$fr['feed']=$ret;
		$fr['hash']=$r_hash;
		return $fr;
	}
	
	function getcomments($id)
	{
		$sql="select com.id,user.special,user.name,com.time,com.comment from king_comments as com join king_users as user on user.userid=com.userid where com.itemid=? order by com.time desc";
		$q=$this->db->query($sql,array($id));
		if($q->num_rows()>0)
			return $q->result_array();
		return array();
	}
	
	function getdealsbymenu_paged($menu,$p)
	{
		$l=PAGED_LIMIT;
		$sql="select i.groupbuy,c.name as category,i.id as itemid, c.url as caturl,c.id as catid,i.live,d.brandid,b.url as brandurl,b.name as brandname,i.groupbuy,i.name,i.price,i.orgprice,i.url,i.pic from king_deals as d join king_dealitems as i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories as c on c.id=d.catid where (d.menuid=? or d.menuid2=?) and ? between d.startdate and d.enddate and d.publish=1 order by d.sno desc limit ".(($p-1)*$l).",$l";
		$deals=$this->db->query($sql,array($menu,$menu,time()))->result_array();
		return $deals;
	}
		
	function getdealsbymenu_count($menu)
	{
		$sql="select count(1) as l from king_deals as d where (d.menuid=? or d.menuid2=?) and ? between d.startdate and d.enddate and d.publish=1";
		return $this->db->query($sql,array($menu,$menu,time()))->row()->l;
	}
	
	function getbrandsformenu($menu)
	{
		$sql="select distinct(b.url),b.name from king_deals as d join king_dealitems as i on i.dealid=d.dealid join king_brands b on b.id=d.brandid where (d.menuid=? or d.menuid2=?) and ? between d.startdate and d.enddate and d.publish=1";
		$deals=$this->db->query($sql,array($menu,$menu,time()))->result_array();
		return $deals;
	}
		
	function getcatsformenu($menu)
	{
		$sql="select distinct(c.url),c.name from king_deals as d join king_dealitems as i on i.dealid=d.dealid join king_categories as c on c.id=d.catid where (d.menuid=? or d.menuid2=?) and ? between d.startdate and d.enddate and d.publish=1";
		$deals=$this->db->query($sql,array($menu,$menu,time()))->result_array();
		return $deals;
	}
	
	function getdealsbymenubrand_paged($menu,$brand,$p)
	{
		$l=PAGED_LIMIT;
		$sql="select i.groupbuy,c.name as category,i.id as itemid, c.url as caturl,c.id as catid,i.live,d.brandid,b.url as brandurl,b.name as brandname,i.groupbuy,i.name,i.price,i.orgprice,i.url,i.pic from king_deals as d join king_dealitems as i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories as c on c.id=d.catid where (d.menuid=? or d.menuid2=?) and d.brandid=? and ? between d.startdate and d.enddate and d.publish=1 order by d.sno desc limit ".(($p-1)*$l).",$l";
		$deals=$this->db->query($sql,array($menu,$menu,$brand,time()))->result_array();
		return $deals;
	}
		
	function getdealsbymenubrand_count($menu,$brand)
	{
		$sql="select count(1) as l from king_deals as d where (d.menuid=? or d.menuid2=?) and d.brandid=? and ? between d.startdate and d.enddate and d.publish=1";
		return $this->db->query($sql,array($menu,$menu,$brand,time()))->row()->l;
	}
	
	function getcatsformenubrand($menu,$brand)
	{
		$sql="select distinct(c.url),c.name from king_deals as d join king_categories as c on c.id=d.catid where (d.menuid=? or d.menuid2=?) and d.brandid=? and ? between d.startdate and d.enddate and d.publish=1";
		$deals=$this->db->query($sql,array($menu,$menu,$brand,time()))->result_array();
		return $deals;
	}
	
	function getdealsbymenucat_paged($menu,$cat,$p)
	{
		$l=PAGED_LIMIT;
		$sql="select i.groupbuy,c.name as category,i.id as itemid, c.url as caturl,c.id as catid,i.live,d.brandid,b.url as brandurl,b.name as brandname,i.groupbuy,i.name,i.price,i.orgprice,i.url,i.pic from king_deals as d join king_dealitems as i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories as c on c.id=d.catid where (d.menuid=? or d.menuid2=?) and d.catid=? and ? between d.startdate and d.enddate and d.publish=1 order by d.sno desc limit ".(($p-1)*$l).",$l";
		$deals=$this->db->query($sql,array($menu,$menu,$cat,time()))->result_array();
		return $deals;
	}
		
	function getdealsbymenucat_count($menu,$cat)
	{
		$sql="select count(1) as l from king_deals as d where (d.menuid=? or d.menuid2=?) and d.catid=? and ? between d.startdate and d.enddate and d.publish=1";
		return $this->db->query($sql,array($menu,$menu,$cat,time()))->row()->l;
	}
	
	function getbrandsformenucat($menu,$cat)
	{
		$sql="select distinct(b.url),b.name from king_deals as d join king_brands as b on b.id=d.brandid where (d.menuid=? or d.menuid2=?) and d.catid=? and ? between d.startdate and d.enddate and d.publish=1";
		$deals=$this->db->query($sql,array($menu,$menu,$cat,time()))->result_array();
		return $deals;
	}

	function getdealsbybrand_paged($brand,$p)
	{
		$l=PAGED_LIMIT;
		$sql="select i.groupbuy,c.name as category,i.id as itemid, c.url as caturl,c.id as catid,i.live,d.brandid,b.url as brandurl,b.name as brandname,i.groupbuy,i.name,i.price,i.orgprice,i.url,i.pic from king_deals as d join king_dealitems as i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories as c on c.id=d.catid where d.brandid=? and ? between d.startdate and d.enddate and d.publish=1 order by d.sno desc limit ".(($p-1)*$l).",$l";
		$deals=$this->db->query($sql,array($brand,time()))->result_array();
		return $deals;
	}
		
	function getdealsbybrand_count($brand)
	{
		$sql="select count(1) as l from king_deals as d where d.brandid=? and ? between d.startdate and d.enddate and d.publish=1";
		return $this->db->query($sql,array($brand,time()))->row()->l;
	}
	
	function getcatsforbrand($brand)
	{
		$sql="select distinct(c.url),c.name from king_deals as d join king_categories as c on c.id=d.catid where d.brandid=? and ? between d.startdate and d.enddate and d.publish=1";
		$deals=$this->db->query($sql,array($brand,time()))->result_array();
		return $deals;
	}
	
	function getdealsbycatbrand_paged($cat,$brand,$p)
	{
		$l=PAGED_LIMIT;
		$sql="select i.groupbuy,c.name as category,i.id as itemid, c.url as caturl,c.id as catid,i.live,d.brandid,b.url as brandurl,b.name as brandname,i.groupbuy,i.name,i.price,i.orgprice,i.url,i.pic from king_deals as d join king_dealitems as i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories as c on c.id=d.catid where d.brandid=? and d.catid=? and ? between d.startdate and d.enddate and d.publish=1 order by d.sno desc limit ".(($p-1)*$l).",$l";
		$deals=$this->db->query($sql,array($brand,$cat,time()))->result_array();
		return $deals;
	}
		
	function getdealsbycatbrand_count($cat,$brand)
	{
		$sql="select count(1) as l from king_deals as d where d.brandid=? and d.catid=? and ? between d.startdate and d.enddate and d.publish=1";
		return $this->db->query($sql,array($brand,$cat,time()))->row()->l;
	}
	
		
	function getdealsbycat_count($cat)
	{
		$sql="select count(1) as l from king_deals as d where d.catid=? and ? between d.startdate and d.enddate and d.publish=1";
		return $this->db->query($sql,array($cat,time()))->row()->l;
	}
	
	function getbrandsforcat($cat)
	{
		$sql="select distinct(c.url),c.name from king_deals as d join king_brands as c on c.id=d.brandid where d.catid=? and ? between d.startdate and d.enddate and d.publish=1";
		$deals=$this->db->query($sql,array($cat,time()))->result_array();
		return $deals;
	}
	
	function getdealsbycat_paged($cat,$p)
	{
		$l=PAGED_LIMIT;
		$sql="select i.groupbuy,c.name as category,i.id as itemid, c.url as caturl,c.id as catid,i.live,d.brandid,b.url as brandurl,b.name as brandname,i.groupbuy,i.name,i.price,i.orgprice,i.url,i.pic from king_deals as d join king_dealitems as i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories as c on c.id=d.catid where d.catid=? and ? between d.startdate and d.enddate and d.publish=1 order by d.sno desc limit ".(($p-1)*$l).",$l";
		$deals=$this->db->query($sql,array($cat,time()))->result_array();
		return $deals;
	}
	
	
}
?>