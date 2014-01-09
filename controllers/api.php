<?php

/**
 * API webservice
 * 
 * @author vimal
 * @since 13/07/10
 */

class Api extends Controller
{
	function __construct()
	{
		parent::Controller();
		header("Content-Type: text/html; charset=UTF-8");
		header("Cache-Control: private, no-cache, no-store, must-revalidate");
		header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
		header("Pragma: no-cache");
		$this->load->model("viakingmodel","dbm");
	}
	
	function buypartitem($id_url="")
	{
		if(empty($id_url))
			show_404();
			
		$id_url=html_entity_decode($id_url);
		$d_item=$this->db->query("select i.id,i.url,d.publish,d.startdate,d.enddate,i.live,i.quantity,i.available from king_dealitems i join king_deals d on d.dealid=i.dealid where i.url=? or i.id=? and i.id!=0",array($id_url,$id_url))->row_array();
		if(empty($d_item))
			show_404();
		if($d_item['startdate']>time() || $d_item['enddate']<time() || $d_item['publish']!=1 || $d_item['live']!=1 || $d_item['quantity']<=$d_item['available'])
			redirect($d_item['url']);
			
		if($this->session->userdata("bodyparts_checkout"))
		{
			$data['info']=array("Please finish your special checkout","You have a special checkout in progress. Please finish your checkout or clear it before adding this product");
			$data['page']="info";
			$this->load->view("index",$data);return;
		}	

		$itemid=$d_item['id'];	
			
		$user=$this->session->userdata("user");
		if(!$user)
		{
			$user=$this->dbm->getuserbyemail("guest@localcircle.in");
			$user['corp']=$this->dbm->getcorpname($user['corpid']);
		}
		$this->load->library("cart");
    	
		$qty=1;
		
		$opts=array();

		$itemdetails=$this->dbm->getitemdetails($itemid);
		
		if($qty>$itemdetails['max_allowed_qty'])
			$qty=$itemdetails['max_allowed_qty'];
		
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

		$name=str_replace("'"," ",$itemdetails['name']);
		$mark=0;
		$price=$itemdetails['price'];
		if($qty>=$bp->quantity)
			$price=$itemdetails['price']-$refund;
		$cart=array("id"=>$itemdetails['id'],'qty'=>$qty,"price"=>$price,"name"=>str_replace("&","-",$name),"options"=>$opts);
		$flag=false;
		foreach($this->cart->contents() as $cartitem)
		{
			if($cartitem['id']==$itemid)
			{
				$flag=true;break;
			}
		}
		if($flag)
		{
			$up=array("qty"=>0,"rowid"=>$cartitem['rowid']);
			$this->cart->update($up);
			$up=array("id"=>$itemdetails['id'],'qty'=>$qty,'price'=>$price,'options'=>$opts,"name"=>str_replace("&","-",$itemdetails['name']));
			$this->cart->insert($up);
			$this->dbm->savecart();
			if(isset($_SERVER['HTTP_X_REQUESTED_WITH']))
			{
				$ret=array("num"=>$this->cart->total_items(),"total"=>number_format($this->dbm->calc_cart_total()));
				die(json_encode($ret));
			}
			redirect("shoppingcart");
		}
		$ret=$this->cart->insert($cart);
		
		$this->dbm->savecart();
		
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']))
		{
			$ret=array("num"=>$this->cart->total_items(),"total"=>number_format($this->dbm->calc_cart_total()));
			die(json_encode($ret));
		}
		
		redirect("shoppingcart");
	}
	
}
