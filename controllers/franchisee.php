<?php

/**
 * Franchisee controller
 * 
 * @author Vimal
 *
 */

class Franchisee extends Controller{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model("franadmin","dbm");
	}
	
	private function checkuser()
	{
		$user=$this->session->userdata("fran_auser");
		if($user==false)
			redirect("franchisee");
		return $user;
	}
	
	function index()
	{
		$user=$this->session->userdata("fran_auser");
		if($user!=false)
			redirect("franchisee/dashboard");
		$data['page']="login";
		$this->load->view("franchisee",$data);
	}
	
	function contact()
	{
		$user=$this->checkuser();
		if($_POST)
		{
			$sub=$this->input->post("sub");
			$msg=$this->input->post("msg");
			$this->db->query("insert into king_contact(userid,subject,message,status,date) values(?,?,?,?,?)",array($user['id'],$sub,$msg,0,time()));
			$this->session->set_flashdata("info","Contact request placed");
			redirect("franchisee");
		}
		$data['page']="contact";
		$this->load->view("franchisee",$data);
	}
	
	function search()
	{
		$user=$this->checkuser();
		$q="%".$this->input->post("q")."%";
		$data['deals'][0]=$this->db->query("select i.* from king_dealitems as i join king_deals as d on d.dealid=i.dealid and d.dealtype!=4 where i.name like ?",$q)->result_array();
		$data['deals'][1]=$this->db->query("select i.* from king_dealitems as i join king_deals as d on d.dealid=i.dealid and d.dealtype=4 where i.name like ?",$q)->result_array();
//		$data['deals'][2]=$this->db->query("select * from king_products where name like ?",$q)->result_array();
		$data['deals'][2]=array();
		for($i=0;$i<3;$i++)
		{
			foreach($data['deals'][$i] as $i2=>$deal)
			{
				$mark=$this->db->query("select * from king_franch_marks where type=? and itemid=?",array($i,$deal['id']))->row_array();
				$data['deals'][$i][$i2]['mark']=0;
				if(!empty($mark))
					$data['deals'][$i][$i2]['mark']=$mark['mark'];
			}
		}
		$data['page']="search";
		$this->load->view("franchisee",$data);
	}
	
	function deals()
	{
		$user=$this->checkuser();
		$data['deals']=$this->db->query("select * from king_deals where ? between startdate and enddate and publish=1",time())->result_array();
		$data['page']="deals";
		$this->load->view("franchisee",$data);
	}
	
	function viewdeal($id)
	{
		$user=$this->checkuser();
		$items=$this->db->query("select * from king_dealitems where dealid=?",$id)->result_array();
		foreach($items as $i=>$item)
		{
			$mark=0;
			$m=$this->db->query("select * from king_franch_marks where itemid=?",$item['id']);
			if($m->num_rows()==1)
				$mark=$m->row()->mark;
			$items[$i]['mark']=$mark;
		}
		$data['deal']=$items;
		$data['page']="brandeditems";
		$this->load->view("franchisee",$data);
	}
	
	function deal($type,$id)
	{
		$user=$this->checkuser();
		$data['deals'][0]=$data['deals'][1]=$data['deals'][2]=array();
		if($type!=2)
		$data['deals'][$type]=$this->db->query("select i.* from king_dealitems as i join king_deals as d on d.dealid=i.dealid where i.id=?",$id)->result_array();
		else 
		$data['deals'][2]=$this->db->query("select * from king_products where id=?",$id)->result_array();
		for($i=0;$i<3;$i++)
		{
			foreach($data['deals'][$i] as $i2=>$deal)
			{
				$mark=$this->db->query("select * from king_franch_marks where type=? and itemid=?",array($i,$deal['id']))->row_array();
				$data['deals'][$i][$i2]['mark']=0;
				if(!empty($mark))
					$data['deals'][$i][$i2]['mark']=$mark['mark'];
			}
		}
		$data['page']="search";
		$this->load->view("franchisee",$data);
	}
	
	function vanilla($cat="none")
	{
		$user=$this->checkuser();
		if($cat=="none")
			$deals=$this->db->query("select * from king_products where status=1")->result_array();
		else 
			$deals=$this->db->query("select * from king_products where status=1 and category=?",$cat)->result_array();
		foreach($deals as $i=>$deal)
		{
			$deals[$i]['mark']=0;
			$m=$this->db->query("select * from king_franch_marks where type=2 and franid=? and status=1 and itemid=?",array($user['id'],$deal['id']));
			if($m->num_rows()==1)
				$deals[$i]['mark']=$m->row()->mark;
		}
		$data['cats']=$this->db->query("select * from king_deal_category where status=1")->result_array();
		$data['deals']=$deals;
		$data['page']="vanilla";
		$this->load->view("franchisee",$data);
	}
	
	function orders()
	{
		$user=$this->checkuser();
		$data['orders']=$orders=$this->db->query("select o.*,i.name from king_orders as o join king_dealitems as i on i.id=o.itemid  where userid=? order by time desc",$user['userid'])->result_array();
		if(empty($orders)){
			$data['page']="../../body/info";
			$data['info']=array("Your Orders","No orders yet!");
			$this->load->view("franchisee",$data);
			return;
		}
		$data['page']="orders";
		$this->load->view("franchisee",$data);
	}
	
	function order($type,$id)
	{
		$user=$this->checkuser();
		if($_POST)
		{
			$vars=array("quantity","b_address","b_city","b_pin","s_address","s_city","s_pin","b_person","s_person");
			foreach($vars as $var)
				$$var=$this->input->post($var);
			$dealid=0;$vendorid=0;
			if($type==1 || $type==0)
			{
				$dealid=$this->db->query("select dealid from king_dealitems where id=?",$id)->row()->dealid;
				$vendorid=$this->db->query("select vendorid from king_deals where dealid=?",$dealid)->row()->vendorid;
			}
			else
				$vendorid=$this->db->query("select d.vendorid from king_products as p join king_stores as d on d.id=p.storeid where p.id=?",$id)->row()->vendorid;
			$sql="insert into king_orders(id,userid,itemid,vendorid,deal_row_id,bill_person,bill_address,bill_city,bill_pincode,ship_person,ship_address,ship_city,ship_pincode,quantity,paid,time)
					values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$this->db->query($sql,array(rand(10000,945839292),$user['userid'],$id,$vendorid,$dealid,$b_person,$b_address,$b_city,$b_pin,$s_person,$s_address,$s_city,$s_pin,$quantity,$paid,time()));
			$this->session->set_flashdata("info","Order placed");
			redirect("franchisee/orders");
		}
		$data['fran']=$this->db->query("select * from king_franchisee where id=?",$user['id'])->row_array();
		$data['page']="order";
		$this->load->view("franchisee",$data);
	}
	
	function addmark($type,$id)
	{
		$user=$this->checkuser();
		if($_POST)
		{
			$amount=$this->input->post("price");
			$marktype=$this->input->post("type");
			if($marktype==2)
				$amount*=-1;
			if($type==10)
			{
				foreach($this->db->query("select id from king_dealitems where dealid=?",$id)->result_array() as $item)
				{
					$this->db->query("delete from king_franch_marks where itemid=?",$item['id']);
					$this->db->query("insert into king_franch_marks(itemid,mark,franid,type,status) values(?,?,?,1,1)",array($item['id'],$amount,$user['id']));
				}
				redirect("franchisee/viewdeal/".$id);
			}else{
				$mark=$this->db->query("select * from king_franch_marks where type=? and franid=? and itemid=?",array($type,$user['id'],$id))->row_array();
				if(!empty($mark))
				{
					$sql="update king_franch_marks set mark=? where id=?";
					$this->db->query($sql,array($amount,$mark['id']));
				}else{
					$sql="insert into king_franch_marks(type,itemid,franid,mark,status) values(?,?,?,?,1)";
					$this->db->query($sql,array($type,$id,$user['id'],$amount));
				}
			}
			redirect("franchisee/deal/$type/$id");
		}
		$mark=$this->db->query("select * from king_franch_marks where type=? and franid=? and itemid=?",array($type,$user['id'],$id))->row_array();
		if(!empty($mark))
			$data['mark']=$mark;
		$data['page']="addmark";
		$this->load->view("franchisee",$data);
	}
	
	function vieworder($id)
	{
		$user=$this->checkuser();
		$data['order']=$this->db->query("select o.*,i.name from king_orders as o join king_dealitems as i on i.id=o.itemid  where userid=? and o.id=? order by time desc",array($user['userid'],$id))->row_array();
		$data['page']="vieworder";
		$this->load->view("franchisee",$data);
	}
	
	function dashboard()
	{
		$user=$this->checkuser();
		$data['orders']=$orders=$this->db->query("select o.*,i.name from king_orders as o join king_dealitems as i on i.id=o.itemid  where userid=? order by time desc limit 20",$user['userid'])->result_array();
		$data['trans']=$this->dbm->gettransactions($user['id']);
		$data['page']="dashboard";
		$this->load->view("franchisee",$data);
	}
	
	function transactions()
	{
		$user=$this->checkuser();
		$data['trans']=$this->dbm->gettransactions($user['id']);
		$data['page']="trans";
		$this->load->view("franchisee",$data);
	}
	
	function account()
	{
		$user=$this->checkuser();
		$data['page']="account";
		$this->load->view("franchisee",$data);
	}
	
	function delmark($id)
	{
		$user=$this->checkuser();
		$this->db->query("delete from king_franch_marks where id=? and franid=?",array($id,$user['id']));
		$this->session->set_flashdata("info","Mark Up/Down deleted");
		redirect("franchisee/marks");
	}
	
	function marks()
	{
		$user=$this->checkuser();
		$data['marks'][0]=$this->db->query("select m.*,i.name,i.price,d.description as url1,i.url as url2 from king_franch_marks as m join king_dealitems as i on i.id=m.itemid join king_deals as d on d.dealid=i.dealid and '".time()."' between d.startdate and d.enddate where m.type=0 and m.franid=?",$user['id'])->result_array();
//		$data['marks'][1]=$this->db->query("select m.*,i.name,i.price,d.description as url1,i.url as url2 from king_franch_marks as m join king_dealitems as i on i.id=m.itemid join king_deals as d on d.dealid=i.dealid and '".time()."' between d.startdate and d.enddate where m.type=1 and m.franid=?",$user['id'])->result_array();
		$data['marks'][1]=array();
		$data['marks'][2]=array();
		$data['page']="marks";
		$this->load->view("franchisee",$data);
	}

	function changepwd()
	{
		$user=$this->checkuser();
		$corpuser=$this->dbm->getfranchisee($user['id']);
		if($_POST)
		{
			$error="";
			$opass=$this->input->post("opass");
			$npass=$this->input->post("npass");
			$cnpass=$this->input->post("cnpass");
			if(strlen($npass)<6)
				$error="Password should be atleast six characters long";
			if($error=="" && $npass!=$cnpass)
				$error="Passwords are not identical";
			if($error=="")
			{
				$p=$this->db->query("select password from king_franchisee where id=?",$user['id'])->row()->password;
				if($p!=md5($opass))
					$error="Old password is wrong";
			}
			if($error=="")
			{
				$this->db->query("update king_franchisee set password=? where id=? limit 1",array(md5($npass),$user['id']));
				$this->session->unset_userdata("fran_auser");
				$this->load->library("email");
				$this->email->to($corpuser['email']);
				$this->email->from("support@group2get.com","Group2Get");
				$this->email->subject("Password Changed");
				$this->email->message("Your franchisee account password changed\n\nNew Password : ".$npass."\n\n\nGroup2Get");
				$this->email->send();
				die("<h2>Password Changed</h2><h5>Please login <a href='".site_url("franchisee")."'>again</a></h5>");
			}
			$data['error']=$error;
		}
		$data['page']="changepwd";
		$this->load->view("franchisee",$data);
	}
	
	
	function logout()
	{
		$this->session->unset_userdata("fran_auser");
		$this->session->unset_userdata("user");
		$this->session->sess_destroy();
		redirect("franchisee");
	}
	
	function login($redir=false)
	{
		if(!$_POST)
			redirect("franchisee");
		$username=$this->input->post("fran_username");
		$password=$this->input->post("fran_password");
		$ret=$this->dbm->login($username,$password);
		if($ret===true)
		{
			$auser=$this->session->userdata("fran_auser");
			$user=$this->db->query("select userid,mobile,name,email,inviteid,special,balance from king_users where userid=?",$auser['userid'])->row_array();
			$this->session->set_userdata("user",$user);
			if($redir)
				redirect("deals");
			redirect("franchisee");
		}
		$data['page']="login";
		$data['error']=true;
		$this->load->view("franchisee",$data);
	}
	
}