<?php
/**
 * Reports Controller Class
 *
 * @desc reports controller to contain reporting functions
 * @author sharif
 */

class Reports extends Controller
{
	/**
	 * Default Consturctor
	 */
	function Reports()
	{
		parent::Controller ();
		$this->load->library ( 'upload' );
		$this->load->library ( 'form_validation' );
		$this->load->model("adminmodel");
		$this->load->model ( 'adminmodel' ,"dbm");
		if($_SERVER['HTTP_HOST']!="localhost" && $_SERVER['HTTP_HOST']!="sand43.snapittoday.com")
			if((!isset($_COOKIE['admauth']) || $_COOKIE['admauth']!=$this->session->userdata("admkey")) && $this->uri->segment(2)!="key")
			show_404();
	}

	/**
	 * Default functon called by controller
	 */
	function index()
	{
			
	}

	private function auth($super=false)
	{
		$user=$this->session->userdata("admin_user");
		if($super===false)
			if($user!==false)
			return $user;
		if($super===true && $user!==false && $user['usertype']==1)
			return $user;
		redirect("admin");
	}


	/**
	 * function to load k-file input form
	 */
	function kfile()
	{
		$user=$this->auth(true);
		$data['page']="reports/generate_kfile";
		$this->load->view("admin",$data);
	}

	/**
	 * function to process or generate k file by given invoice_nos by post
	 */
	function generate_kfile()
	{
		$inv_nos = $this->input->post('inv_nos');
		$inv_no_list = explode(',',$inv_nos);
		$inv_no_list = array_filter($inv_no_list);

		$invnos = array();
		foreach($inv_no_list as $invno)
		{
			array_push($invnos,'"'.$invno.'"');
		}

		$q_invnos = implode(',',$invnos);

		$sql = "select  inv.invoice_no as 'Invoice NO',
						o.shipid as 'AWBNO',
						o.medium as 'Courier/Medium',

						date(inv.shipdatetime) as 'Ship Date', 
						ifnull(inv.notify_customer,1) as 'Notify Customer',
						t.transid as 'Transaction Referenceno',
						o.ship_person as 'Ship Person',
						concat_ws(', ',o.ship_address, o.ship_landmark) as 'Shipping Address',
						o.ship_city as 'Shipping City',
						o.ship_pincode as 'Shipping Pincode',
						o.ship_state as 'Shipping State',
						concat_ws(', ',o.ship_phone, o.ship_telephone) as 'Contact Number',
						t.amount as 'Amount',
						t.mode as 'Mode',
						sum(o.quantity) as 'Quantity',
						group_concat(concat_ws(':',i.name,o.quantity)) as 'Product Name'
					from king_transactions t
					join king_orders o on o.transid = t.transid
					join king_dealitems i on i.id = o.itemid
					join king_deals d on i.dealid = d.dealid
					join king_brands b on b.id = d.brandid
					join king_invoice inv on inv.order_id = o.id
					left join king_used_coupons uc on uc.transid = t.transid
					left join king_coupons c on c.code = uc.coupon
					where inv.invoice_no in ($q_invnos)
					group by inv.invoice_no;";

		$query = $this->db->query($sql);


		if($query->num_rows())
		{
			$delimiter = ",";
			$newline = "\r\n";
				
			$filename = 'KFILE_'.date('dmY').'.csv';

			// send response headers to the browser
			header( 'Content-Type: text/csv' );
			header( 'Content-Disposition: attachment;filename='.$filename);
			$fp = fopen('php://output', 'w');

				
				
			$this->load->dbutil();
			echo $this->dbutil->csv_from_result($query, $delimiter, $newline);
				
				
		}else
		{
			echo '<script type="text/javascript">alert("No Results Found")</script>';
		}



	}
	
	
	function order_summary(){
		$user=$this->auth(true);
		$data['page']="reports/order_summary";
		$this->load->view("admin",$data);
	}
	
	
	
	/**
	 * function to process or generate ordersummary by given orderstatus and daterange by post
	 */
	function generate_ordersummary()
	{
		$statlist = $this->input->post('sel_orderstat');
		
		if(!$statlist){
			echo '<script type="text/javascript">alert("Please select atleast one order status")</script>';
			exit;
		}
		
		
		//$statlist = explode(',',$statlist);
		//$statlist = array_filter($statlist);
	
		$from = date('Y-m-d',strtotime($this->input->post('from')));
		$to = date('Y-m-d',strtotime($this->input->post('to')));
		
		$statlist_str = array();
		foreach($statlist as $stat)
		{
			array_push($statlist_str,'"'.$stat.'"');
		}
	
		$q_statlist_str = implode(',',$statlist_str);
	
		$sql = "select concat(o.itemid,' ') as itemid, o.brandid, b.name as brand, i.name as deal, o.i_price as offer_price, o.i_orgprice as mrp, sum(o.quantity) as pending_order_qty, stk.available as available_stock from king_orders o 
				join king_dealitems i on i.id = o.itemid
				join king_brands b on b.id = o.brandid
				left join king_stock stk on stk.itemid = i.id
				where date(from_unixtime(o.time)) >=  date(?) and date(from_unixtime(o.time)) <=  date(?) and admin_order_status in (".$q_statlist_str.")
				group by o.itemid  order by b.name, i.name;";
	
			$query = $this->db->query($sql,array($from,$to));
	
	
			if($query->num_rows())
			{
				$delimiter = ",";
				$newline = "\r\n";
		
				
				$filename = 'OrderSummary_';
				if($from == $to)
				{
					$filename .= date('dmY',strtotime($from));
				}else{
					$filename .= date('dmY',strtotime($from)).'_'.date('dmY',strtotime($to));
				}
				$filename .= '.csv';
				// send response headers to the browser
				header( 'Content-Type: text/csv' );
				header( 'Content-Disposition: attachment;filename='.$filename);
				$fp = fopen('php://output', 'w');
		
		
		
				$this->load->dbutil();
				echo $this->dbutil->csv_from_result($query, $delimiter, $newline);
	
	
			}
			else
			{
				echo '<script type="text/javascript">alert("No Orders Found")</script>';
			}
	
	
	
	}
	
	
}