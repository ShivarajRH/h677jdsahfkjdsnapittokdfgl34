<?php 
include APPPATH.'/controllers/stream.php';
include APPPATH.'/libraries/PHPExcel.php';
include APPPATH.'/libraries/PHPExcel/Writer/Excel2007.php';
class Erp extends Stream
{
	public $user_access=array();
	
	/*function __construct()
	{
		parent::__construct();	
		$this->task_status = $this->config->item('task_status');
		$this->db->query("set session group_concat_max_len=5000000;");
		$this->task_for=$this->config->item('task_for');
		$this->load->library('pagination');
	}*/
	
	function exotel_handler($type='upd_agent_status')
	{
		if($type == 'upd_agent_status')
		{
			$inp = array();
			$inp['callsid'] = $_REQUEST['CallSid'];
			$inp['from'] = $_REQUEST['From'];
			$inp['dialwhomno'] = $_REQUEST['DialWhomNumber'];
			$inp['status'] = $_REQUEST['Status'];
			$inp['created_on'] = date('Y-m-d H:i:s');
			$this->db->insert("t_exotel_agent_status",$inp);	
		}else
		{
			 
		}
	}
	
	function rackbins()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|STOCK_INTAKE_ROLE|PURCHASE_ORDER_ROLE);
		$data['locs']=$locs=$this->db->query("select * from m_storage_location_info order by location_name")->result_array();
		if(empty($locs))
			show_error("no storage location available<br>please add one!");
		$data['rackbins']=$this->erpm->getrackbins();
		$data['page']="rackbins";
		$this->load->view("admin",$data);
	}
	
	function addrackbin()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|STOCK_INTAKE_ROLE|PURCHASE_ORDER_ROLE);
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']))
			$this->db->query("update m_rack_bin_info set rack_name=?,bin_name=? where id=? limit 1",array($_POST['rack'],$_POST['bin'],$_POST['id']));
		else
			$this->db->query("insert into m_rack_bin_info(location_id,rack_name,bin_name,created_on) values(?,?,?,now())",array($this->input->post("loc"),$this->input->post("rack"),$this->input->post("bin")));
		redirect("admin/rackbins");
	}
	
	function productsbytax($tax=false,$pg=0)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		if(empty($tax))
			show_404();
		$data['tax']=number_format($tax,2);
		$data['products']=$this->erpm->getproductsbytax($tax);
		
		$limit=30;
		$sql="select sum(s.available_qty) as stock,p.*,b.name as brand from m_product_info p join king_brands b on b.id=p.brand_id left outer join t_stock_info s on s.product_id=p.product_id where p.vat=? group by p.product_id order by p.product_name asc";
		
		$total_products=$this->db->query($sql,$tax)->num_rows();
		$sql.=" limit $pg , 30 ";
		$data['products']=$this->db->query($sql,$tax)->result_array();
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url("admin/productsbytax/".$tax);
		$config['total_rows'] = $total_products;
		$config['per_page'] = $limit;
		$config['uri_segment'] = 4;
		$config['num_links'] = 5;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		//pagination end*/
		
		$data['page']="products";
		$this->load->view("admin",$data);
	}
	
	function productsbybrand($bid=false,$pg=0)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		if(empty($bid))
			show_404();
		$data['brand']=$this->db->query("select name from king_brands where id=?",$bid)->row()->name;
		$data['products']=$this->erpm->getproductsbybrand($bid);
		
		$limit=30;
		$sql="select sum(s.available_qty) as stock,p.*,b.name as brand from m_product_info p join king_brands b on b.id=p.brand_id left outer join t_stock_info s on s.product_id=p.product_id where p.brand_id=? group by p.product_id order by p.product_name asc";
		
		$total_products=$this->db->query($sql,$bid)->num_rows();
		$sql.=" limit $pg , 30 ";
		$data['products']=$this->db->query($sql,$bid)->result_array();
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url("admin/productsbybrand/".$bid);
		$config['total_rows'] = $total_products;
		$config['per_page'] = $limit;
		$config['uri_segment'] = 4;
		$config['num_links'] = 5;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		//pagination end*/
		
		
		
		$data['page']="products";
		$this->load->view("admin",$data);
	}
	
	function viewlinkeddeals($pid)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|DEAL_MANAGER_ROLE);
		$data['product']=$this->db->query("select product_name as p from m_product_info where product_id=?",$pid)->row()->p;
		$data['deals']=$this->erpm->getlinkeddealsforproduct($pid);
		$data['page']="linked_deals_for_products";
		$this->load->view("admin",$data);
	}
	
	function update_barcode()
	{
		$user=$this->auth();
		$pid=$this->input->post("pid");
		$barcode=$this->input->post("barcode");
		$this->db->query("update m_product_info set barcode=? where product_id=? limit 1",array($barcode,$pid));
		if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
		{
			$this->erpm->flash_msg("Barcode updated");
			redirect($_SERVER['HTTP_REFERER']);
		}
		echo $barcode;
	}
	
	function mark_src_products()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		$pids=$this->input->post("pids");
		$action=$this->input->post("action");
		if(!empty($pids))
		{
			$pids=explode(",",$pids);
			$this->db->query("update m_product_info set is_sourceable=$action where product_id in ('".implode("','",$pids)."') limit ".count($pids));
			$this->erpm->flash_msg(count($pids)." products marked as ".($action=="0"?"NOT":"")." Sourceable");
			foreach($pids as $pid)
			{
				$inp=array("product_id"=>$pid,"is_sourceable"=>$action,"created_on"=>time(),"created_by"=>$user['userid']);
				$this->db->insert("products_src_changelog",$inp);
			}
		}
		redirect($_SERVER['HTTP_REFERER']);
		//redirect('admin/pnh_receiptsbytype/3');
	}
	
	function jx_mark_src_products()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		$pids=$this->input->post("pids");
		
		if(!empty($pids))
		{
			$pids=explode(",",$pids);
			foreach($pids as $pid)
			{
				if(is_numeric($pid))
				{
					$this->db->query("update m_product_info set is_sourceable=1 where product_id in ($pid) limit ".count($pids));
					$inp=array("product_id"=>$pid,"is_sourceable"=>1,"created_on"=>time(),"created_by"=>$user['userid']);
					$this->db->insert("products_src_changelog",$inp);
				}
				
			}
		}
	}
	
	function jx_mark_unsrc_products()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		$pids=$this->input->post("pids");
		
		$output=array();
		if(!empty($pids))
		{
			$pids=explode(",",$pids);
			foreach($pids as $pid)
			{
				
				if(is_numeric($pid))
				{
					$this->db->query("update m_product_info set is_sourceable=0 where product_id in ($pid) limit ".count($pids));

					if($this->db->affected_rows()==count($pids))
					{
						$output['status']='success';
					}
					$inp=array("product_id"=>$pid,"is_sourceable"=>0,"created_on"=>time(),"created_by"=>$user['userid']);
					$this->db->insert("products_src_changelog",$inp);
					
				}
			}
		}
		
		echo json_encode($output);
		
	}
	
	function update_check_issubmitted()
	{
		$user=$this->auth(FINANCE_ROLE);
		
		$bank_id=$this->input->post('bank_id');
		$remarks=$this->input->post('checkreamarks');
		$receipt_ids=$this->input->post('rids');
		$action=$this->input->post('action');
		$submitted_date=$this->input->post('check_date');
		$submitted_date=date('Y-m-d',strtotime($submitted_date));
		if(!empty($receipt_ids))
		{
			$r=0;
			$receipt_ids=explode(",",$receipt_ids);
			$this->db->query("update pnh_t_receipt_info set is_submitted=$action where receipt_id in ('".implode("','",$receipt_ids)."') limit ".count($receipt_ids));
			$ttl_receipt_ids=count($receipt_ids);
			$process_id=$this->db->query("select max(id) as process_id from pnh_m_deposited_receipts")->row()-> process_id;
			foreach($receipt_ids as $receipt_id)
			{
				$this->db->query("insert into pnh_m_deposited_receipts(deposited_reference_no,receipt_id,bank_id,is_submitted,status,submitted_by,submitted_on,remarks,created_on)value(?,?,?,1,1,?,?,?,now())",array($process_id,$receipt_id,$bank_id,$user['userid'],$submitted_date,$remarks));
			}
			$r+=$this->db->affected_rows();
			$this->erpm->flash_msg(count($receipt_ids)." Receipts are  Submitted");
			$output=array();
			if($r)
			{
				$output['status']="success";
				$output['count']=$ttl_receipt_ids;
			}
			else
			{
				$output['status']="error";
			}
			echo json_encode($output);
		}
	}
	
	function add_products_group()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		if($_POST)
			$this->erpm->do_add_products_group();
		$data['page']="add_products_group";
		$this->load->view("admin",$data);
	}
	
	function createproductgroupscat()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		$cat=$this->input->post("cat");
		if(empty($cat)) show_error("Enter a valid category name");
		if($this->db->query("select 1 from products_group_category where name=?",$cat)->num_rows()!=0) show_error("$cat already exists");
		$this->db->insert("products_group_category",array("name"=>$cat,"created_on"=>time(),"created_by"=>$user['userid']));
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function products_group($catid=null)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		$sql="select g.*,c.name as category,count(distinct gp.product_id) as pids from products_group g left outer join products_group_category c on c.id=g.cat_id left outer join products_group_pids gp on gp.group_id=g.group_id";
		if($catid)
			$sql.=" where g.cat_id=$catid ";
		$sql.=" group by g.group_id order by g.group_id desc";
		if(!$catid)
			$sql.=" limit 100";
		if($catid)
			$data['pagetitle']="Products group of category:".$this->db->query("select name from products_group_category where id=?",$catid)->row()->name;
		else
			$data['pagetitle']="Recently created Products Group";
		$data['groups']=$this->db->query($sql)->result_array();
		$data['page']="products_group";
		$this->load->view("admin",$data);
	}
	
	function product_group($gid)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		$data['group']=$this->db->query("select * from products_group where group_id=?",$gid)->row_array();
		$data['prods']=$this->db->query("select gp.*,p.product_name from products_group_pids gp join m_product_info p on p.product_id=gp.product_id where gp.group_id=? group by gp.product_id order by p.product_name",$gid)->result_array();
		$data['page']="product_group";
		$this->load->view("admin",$data);
	}
	
	function products($pg=0)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		$data['products']=$this->erpm->getproducts();
		$limit=30;
		
		$sql="select sum(s.available_qty) as stock,p.*,b.name as brand from m_product_info p join king_brands b on b.id=p.brand_id left outer join t_stock_info s on s.product_id=p.product_id group by p.product_id order by p.product_id desc ";
		
		$total_products=$this->db->query($sql)->num_rows();
		$sql.=" limit $pg , 30 ";
		$data['products']=$this->db->query($sql)->result_array();
		
		//pagination
		$this->load->library('pagination');
		$config['base_url'] = site_url("admin/products");
		$config['total_rows'] = $total_products;
		$config['per_page'] = $limit;
		$config['uri_segment'] = 3;
		$config['num_links'] = 5;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		//pagination end//
		
		$data['page']="products";
		$this->load->view("admin",$data);
	}
	
	function products_group_bulk_upload()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		if($_POST)
			$this->erpm->do_products_group_bulk_upload();
		$data['page']="products_group_bulk_upload";
		$this->load->view("admin",$data);
	}
	
	function pnh_deals_bulk_upload()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if($_FILES)
			$this->erpm->do_pnh_deals_bulk_upload();
		$data['page']="pnh_deals_bulk_upload";
		$this->load->view("admin",$data);
	}
	
	function auto_image_updater()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if($_POST)
		{
			$this->db->query("update cron_image_updater_lock set finish_status=0,is_locked=0,modified_by=?,modified_on=?,images_updated=0",array($user['userid'],time()));
			redirect("admin/auto_image_updater");
		}
		$data['page']="auto_image_updater";
		$this->load->view("admin",$data);
	}
	
	function deals_bulk_upload()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if($_FILES)
			$this->erpm->do_deals_bulk_upload();
		$data['page']="deals_bulk_upload";
		$this->load->view("admin",$data);
	}
	
	function prods_bulk_upload()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		if($_FILES)
			$this->erpm->do_prods_bulk_upload();
		$data['page']="prods_bulk_upload";
		$this->load->view("admin",$data);
	}
	
	function view_variant($vid)
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$data['variant']=$this->db->query("select * from variant_info where variant_id=?",$vid)->row_array();
		$data['items']=$this->erpm->getdealitemsforvariant($vid);
		$data['page']="view_variant";
		$this->load->view("admin",$data);
	}
	
	function variants()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$data['variants']=$this->erpm->getvariants();
		$data['page']="variants";
		$this->load->view("admin",$data);
	}
	
	function addvariant()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if($_POST)
			$this->erpm->do_addvariant();
		$data['page']="addvariant";
		$this->load->view("admin",$data);
	}
	
	function vendor($vid=false)
	{
		if(!$vid)
			show_error("Vendor ID missing");
		$user=$this->auth(PURCHASE_ORDER_ROLE);
		$data['vendor']=$this->erpm->getvendor($vid);
		$data['contacts']=$this->erpm->getvendorcontacts($vid);
		$data['brands']=$this->erpm->getbrandsforvendor($vid);
		$data['pos']=$this->erpm->getposforvendor($vid);
		$data['page']="vendor";
		$this->load->view("admin",$data);
	}
	
	function editvendor($vid=false)
	{
		if(!$vid)
			show_404();
		$user=$this->auth(PURCHASE_ORDER_ROLE);
		if($_POST)
			$this->erpm->do_updatevendor($vid);
		$data['contacts']=$this->erpm->getvendorcontacts($vid);
		$data['brands']=$this->erpm->getbrandsforvendor($vid);
		$data['vendor']=$this->erpm->getvendor($vid);
		$data['vid'] = $vid;
		$data['page']="addvendor";
		$this->load->view("admin",$data);
	}
	
	function jx_remove_vendor_brand_link()
	{
		$user=$this->auth(PURCHASE_ORDER_ROLE);
		$output = array();
		$vid = $this->input->post('vendor_id');
		$brand_id = $this->input->post('brand_id');
		$this->db->query("delete from m_vendor_brand_link where vendor_id = ? and brand_id = ? and is_active = 1 ",array($vid,$brand_id));
		$output['status'] = 'success';
		echo json_encode($output);
	}
	
	function addvendor()
	{
		$user=$this->auth(PURCHASE_ORDER_ROLE);
		if($_POST)
		{
			$this->erpm->do_addvendor();
			redirect("admin/vendors");
		}
		$data['page']="addvendor";
		$this->load->view("admin",$data);
	}
	
	function closepo($poid,$ajax=0)
	{
		$user=$this->auth(PURCHASE_ORDER_ROLE);
		$this->db->query("update t_po_info set po_status=3,modified_on=now() where po_id=? limit 1",$poid);
		if($ajax)
			echo 1;
		else
			redirect("admin/viewpo/$poid");
	}

	function updatedeliverydate($poid)
	{
		
			$user=$this->auth(PURCHASE_ORDER_ROLE);
			if($_POST)
			$expected_podeliverydate=$this->input->post('po_deliverydate');
			$datetime = DateTime::createFromFormat("D F d, Y H:i a", $expected_podeliverydate);
			$expected_podeliverydate=$datetime->format("Y-m-d H:i:s");
			
			$this->db->query("update t_po_info set date_of_delivery=? where po_id=?",array($expected_podeliverydate,$poid));
			redirect("admin/viewpo/$poid");
	}
	
	function assignadmintoticket($tid,$admin)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		$adminname=$this->db->query("select name from king_admin where id=?",$admin)->row()->name;
		$this->db->query("update support_tickets set status=1,updated_on=now(),assigned_to=? where ticket_id=? limit 1",array($admin,$tid));
		$this->erpm->addnotesticket($tid,2,0,"Ticket assigned to $adminname");
		redirect("admin/ticket/$tid");
	}
	
	function mailcheck()
	{
		$this->load->library("imap");
		$luid=$this->db->query("select im_uid from auto_readmail_uid order by id desc limit 1")->row_array();
		if(empty($luid))
			die("no starting uid specified");

		$luid=$luid['im_uid'];
		$this->imap->login("care@snapittoday.com","snap123rty");
		$nuid=$this->imap->is_newmsg($luid);
		if(!$nuid)
			die("no new mail");
		$mails=array();
		for($i=$luid+1;$i<=$nuid;$i++)
			$mails[]=$this->imap->readmail($i);
			
//		$mails=array(array("subject"=>"asdasdasdtn1319888060 SNPCFN51153sdqsdasdSNPCFN53153"));	
		 
		foreach($mails as $m)
		{
			$ticket=array();
			$userid=0;
			$ticket_no=0;
			$transid="";
			if(empty($m))
				continue;
//			preg_match("/SNP[[:alpha:]][[:alpha:]][[:alpha:]][[:digit:]][[:digit:]][[:digit:]][[:digit:]][[:digit:]]/i",$m['subject'],$matches);
			preg_match("/(TK\d{10})/i",$m['subject'],$matches);
			if(empty($matches))
				preg_match("/(TK\d{10})/i",$m['msg'],$matches);
			if(!empty($matches))
			{
				$ticket_no=substr($matches[0],2);
				if($this->db->query("select count(1) as l from support_tickets where ticket_no=? limit 1",$ticket_no)->row()->l==0)
					$ticket_no=0;
			}
			if($ticket_no==0)
			{
				preg_match("/(SNP\w{3}\d{5})/i",$m['subject'],$matches);
				if(empty($matches))
					preg_match("/(SNP\w{3}\d{5})/i",$m['msg'],$matches);
				if(!empty($matches))
					$transid=$matches[0];
			}
			$customer=$this->db->query("select userid from king_users where email=?",$m['from'])->row_array();
			if(!empty($customer))
				$userid=$customer['userid'];
			$msg=nl2br("SUBJECT\n-----------------------------------------------------\n".$m['subject']."\n\n\n\nEMAIL CONTENT\n-----------------------------------------------------\n").$m['msg'];
			$no=rand(1000000000,9999999999);
			if($ticket_no==0)
			{
				$this->db->query("insert into support_tickets(ticket_no,user_id,email,transid,created_on) values(?,?,?,?,now())",array($no,$userid,$m['from'],$transid));
				$tid=$this->db->insert_id();
			}
			else 
			{
				$tid=$this->db->query("select ticket_id as id from support_tickets where ticket_no=?",$ticket_no)->row()->id;
				$this->db->query("update support_tickets set status=1 where ticket_no=? and assigned_to!=0 limit 1",$ticket_no);
				$this->db->query("update support_tickets set status=0 where ticket_no=? and assigned_to=0 limit 1",$ticket_no);
			}
			$this->erpm->addnotesticket($tid,1,0,$msg,1);
			$this->db->query("insert into auto_readmail_log(ticket_id,subject,msg,`from`,created_on) values(?,?,?,?,now())",array($tid,$m['subject'],$msg,$m['from']));
			if($ticket_no!=0)
				$this->erpm->addnotesticket($tid,0,1,"Status reset after reply mail from customer");
		}
		$this->db->query("insert into auto_readmail_uid(im_uid,time) values(?,now())",$nuid);
	}
	
	function voucher($vid=false)
	{
		if(!$vid)
			show_404();
		$user=$this->auth(FINANCE_ROLE);
		$data['page']="view_voucher";
		$data['voucher']=$this->db->query("select v.*,a.name as created_by from t_voucher_info v join king_admin a on a.id=v.created_by where v.voucher_id=?",$vid)->row_array();
		$data['expense']=$this->db->query("select * from t_voucher_expense_link where voucher_id=?",$vid)->row_array();
		$data['doc_po']=$this->db->query("select * from t_voucher_document_link where voucher_id=? and ref_doc_type=2",$vid)->result_array();
		$data['doc_grn']=$this->db->query("select * from t_voucher_document_link where voucher_id=? and ref_doc_type=1",$vid)->result_array();
		$this->load->view("admin",$data);
	}
	
	function vouchers($s=false,$e=false)
	{
		$user=$this->auth(FINANCE_ROLE);
		if($s!=false && $e!=false && (strtotime($s)<=0 || strtotime($e)<=0))
			show_404();
		$data['vouchers']=$this->erpm->getvouchers_date_range($s,$e);
		$data['page']="batch_list";
		if($e)
			$data['pagetitle']="between $s and $e";
		$data['page']="vouchers";
		$this->load->view("admin",$data);
	}
	
	function create_voucher_exp()
	{
		$user=$this->auth(FINANCE_ROLE);
		if($_POST)
			$this->erpm->do_voucher_exp();
		$data['page']="create_voucher_exp";
		$this->load->view("admin",$data);
	}
	
	function create_voucher($grn="")
	{
		$user=$this->auth(FINANCE_ROLE);
		if($_POST)
			$this->erpm->do_voucher();
		$r=array();
		$grns=$this->db->query("select v.vendor_name,v.vendor_id,v.city_name,g.* from t_grn_info g join m_vendor_info v on v.vendor_id=g.vendor_id where g.payment_status=1")->result_array();
		foreach($grns as $g)
		{
			$v=$g['vendor_name'].", ".$g['city_name'];
			if(!isset($r[$v]))
				$r[$v]=array();
			$g['amount']=$this->db->query("select sum(purchase_inv_value) as s from t_grn_invoice_link where grn_id=?",$g['grn_id'])->row()->s;
			$r[$v][]=$g;
		}
		$data['grns']=$r;
		$data['page']="create_voucher";
		$this->load->view("admin",$data);
	}
	
	function jx_assignticketadmins()
	{
		$q=$this->input->post("q");
		foreach($this->db->query("select * from king_admin where name like ? order by name asc","%$q%")->result_array() as $a)
			echo "<a href='javascript:void(0)' onclick='assign_admin({$a['id']},\"{$a['name']}\")'>{$a['name']}</a>";
	}
	
	function ticket_frag($id)
	{
	 	echo $this->db->query("select m.msg from support_tickets_msg m where m.id=?",$id)->row()->msg;
	}
	
	function ticket($tid)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		if($_POST)
		{
			$action=$this->input->post("action");
			if($action==0)
				$this->erpm->do_addnotesticket($tid);
			elseif($action==1)
				$this->erpm->do_changestatusticket($tid);
			elseif($action==2)
				$this->erpm->do_changetypeticket($tid);
			elseif($action==3)
				$this->erpm->do_changepriorityticket($tid);
			redirect("admin/ticket/$tid");
		}
		$data['ticket']=$this->erpm->getticket($tid);
		$data['msgs']=$this->erpm->getticketmsgs($tid);
		$data['page']="viewticket";
		$this->load->view("admin",$data);
	}
	
	function addticket()
	{
		$user=$this->auth(CALLCENTER_ROLE);
		if($_POST)
			$this->erpm->do_ticket();
		$data['page']="addticket";
		$this->load->view("admin",$data);
	}
	
	function support($filter="all",$s=false,$e=false)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		if($s!=false && $e!=false && (strtotime($s)<=0 || strtotime($e)<=0))
			show_404();
		$data['tickets']=$this->erpm->gettickets($filter,$s,$e);
		if($e)
			$data['pagetitle']="between $s and $e";
		$data['page']="tickets";
		$data['filter']=$filter;
		$this->load->view("admin",$data);
	}
	
	function jx_loadforvoucher()
	{
		$user=$this->auth();
		$type=$_POST['type'];
		$vendor=$_POST['vendor'];
		if($type==1)
		{
			$grns=$this->db->query("select grn_id,created_on from t_grn_info where vendor_id=? and payment_status=1",$vendor)->result_array();
			foreach($grns as $g)
			{
				$rpos=$this->db->query("select distinct(po_id) as po_id from t_grn_product_link where grn_id=?",$g['grn_id'])->result_array();
				$pos=array();
				foreach($rpos as $p)
					$pos[]=$p['po_id'];
				$g['pos']=implode(",",$pos);
				echo "<a href='javascript:void(0)' onclick='addgrn(\"{$g['grn_id']}\",0,\"{$g['created_on']}\",\"{$g['pos']}\")'>GRN{$g['grn_id']}</a>";
			}
			if(empty($grns))
				echo "no accounted grns available for selected vendor";
		}else{
			$pos=$this->db->query("select po_id,created_on from t_po_info where vendor_id=? and po_status=0",$vendor)->result_array();
			foreach($pos as $g)
				echo "<a href='javascript:void(0)' onclick='addpo(\"{$g['po_id']}\",0,\"{$g['created_on']}\")'>PO{$g['po_id']}</a>";
			if(empty($pos))
				echo "no opened POs available for selected vendor";
		}
	}
	
	function account_grn($grn)
	{
		$user=$this->auth(FINANCE_ROLE);
		if($_POST)
		{
			$this->erpm->do_account_grn();
			redirect("admin/pending_pay_grns");
		}
		$grn_data=$this->db->query("select * from t_grn_info where grn_id=?",$grn)->row_array();
		if($grn_data['payment_status']!=0)
			show_error("This grn is already accounted");
		$data['page']="account_grn";
		$data['items']=$this->db->query("select i.*,p.product_name from t_grn_product_link i join m_product_info p on p.product_id=i.product_id where i.grn_id=?",$grn)->result_array();
		$data['invoices']=$this->db->query("select * from t_grn_invoice_link where grn_id=?",$grn)->result_array();
		$this->load->view("admin",$data);
	}
	
	function jx_getbrandsforvendor_json()
	{
		$user=$this->auth();
		$vid=$this->input->post("vid");
		$brands=$this->db->query("select b.name,b.id from m_vendor_brand_link v join king_brands b on b.id=v.brand_id where v.vendor_id=? order by b.name asc",$vid)->result_array();
		echo json_encode($brands);
	}
	
	function jx_getproductsforbrand()
	{
		$user=$this->auth();
		$vid=$this->input->post("vid");
		$bid=$this->input->post("bid");
		 
		$v_cond = $v_join_cond = '';
		if($vid)
		{
			$v_join_cond = ' join m_vendor_brand_link vb on vb.brand_id = a.brand_id and vb.vendor_id = '.$vid;
			$v_cond = ' and vb.vendor_id = '.$vid;
		}
				
		
			$sql = "(SELECT DISTINCT a.product_id AS id,product_name AS product,a.mrp,a.is_sourceable AS src,IFNULL(SUM(s.available_qty),0) AS stock,0 AS orders,0 AS margin,0 AS otime,0 AS pen_ord_qty,0 AS order_qty 
					FROM m_product_deal_link d
					JOIN m_product_info a ON d.product_id = a.product_id 
					$v_join_cond
					LEFT JOIN t_stock_info s ON s.product_id = a.product_id 
					WHERE a.brand_id=$bid 
					GROUP BY d.id 
					)
					UNION 
					(
					SELECT DISTINCT a.product_id AS id,product_name AS product,a.mrp,a.is_sourceable AS src,0,IFNULL(SUM(o.quantity*d.qty),0) AS orders,0 AS margin,o.time AS otime,0 AS pen_ord_qty,0 AS order_qty 
					FROM m_product_deal_link d
					JOIN m_product_info a ON d.product_id = a.product_id 
					$v_join_cond
					LEFT JOIN king_orders o ON o.itemid = d.itemid AND o.time > (UNIX_TIMESTAMP()-(24*90*60*60))  
					WHERE a.brand_id=$bid 
					GROUP BY d.id 
					)
					UNION 
					(
					SELECT DISTINCT a.product_id AS id,product_name AS product,a.mrp,a.is_sourceable AS src,0,0,0 AS margin,o.time AS otime,SUM(o.quantity*d.qty) AS pen_ord_qty,0 AS order_qty 
					FROM m_product_deal_link d
					JOIN m_product_info a ON d.product_id = a.product_id 
					$v_join_cond 
					JOIN king_orders o ON o.itemid = d.itemid   
					WHERE a.brand_id=$bid AND o.status = 0 
					GROUP BY a.product_id   
					)
					UNION 
					(
					SELECT DISTINCT a.product_id AS id,product_name AS product,a.mrp,a.is_sourceable AS src,0,0,brand_margin AS margin,0 AS otime,0 AS pen_ord_qty,0 AS order_qty 
					FROM m_product_info a
					$v_join_cond
					WHERE a.brand_id=$bid  AND vb.vendor_id = 6 GROUP BY a.product_id 
					ORDER BY brand_margin ASC
					)
					UNION
					(
					SELECT DISTINCT a.product_id AS id,product_name AS product,a.mrp,a.is_sourceable AS src,0,0,brand_margin AS margin,0 AS otime,0 AS pen_ord_qty,p.order_qty AS order_qty
					FROM m_product_info a
					JOIN t_po_product_link p ON p.product_id=a.product_id
					JOIN t_po_info i ON i.po_id=p.po_id
					JOIN m_vendor_brand_link vb ON vb.brand_id = a.brand_id
					WHERE a.brand_id=$bid AND po_status<2 $v_cond GROUP BY i.po_id 
					ORDER BY brand_margin ASC
					)
					ORDER BY otime DESC,orders DESC,product ASC  
							";
		
		$res = $this->db->query($sql);
		
		
		$product_list = array();
		if($res->num_rows())
		{
			
			foreach($res->result_array() as $row)
			{
				$row['stock'] = (int)$row['stock'];
				if(!isset($product_list[$row['id']]))
					$product_list[$row['id']] = $row;
				else	
				{
					$product_list[$row['id']]['stock'] += $row['stock'];
					$product_list[$row['id']]['orders'] += $row['orders'];
					$product_list[$row['id']]['margin'] += $row['margin'];
					$product_list[$row['id']]['pen_ord_qty'] += $row['pen_ord_qty'];
					$product_list[$row['id']]['order_qty'] += $row['order_qty'];
					
				}
			}
		}
		
		echo json_encode(array_values($product_list));
		
		
		/*
		if($vid)
		{
			$data=$this->db->query("select p.is_sourceable as src,0 as orders,b.brand_margin as margin,ifnull(sum(s.available_qty),0) as stock,p.product_id as id,p.product_name as product,p.mrp from m_product_info p join m_vendor_brand_link b on b.brand_id=p.brand_id and b.vendor_id=? left outer join t_stock_info s on s.product_id=p.product_id where p.brand_id=? group by p.product_id order by p.product_name asc",array($vid,$bid))->result_array();
			$pids=array();
			foreach($data as $d)
				$pids[]=$d['id'];
			if(!empty($pids))
			{
				$orders=$this->db->query("select ifnull(sum(o.quantity*l.qty),0) as s,l.product_id from m_product_deal_link l join king_orders o on o.itemid=l.itemid where l.product_id in ('".implode("','",$pids)."') and o.time>".(time()-(24*60*60*90))." group by l.product_id")->result_array();
				foreach($orders as $o)
					foreach($data as $i=>$d)
					{
						if($d['id']==$o['product_id'])
							$data[$i]['orders']=$o['s'];
					}
			}
			echo json_encode($data);
		}
		else
			echo json_encode($this->db->query("select p.is_sourceable as src,ifnull(sum(o.quantity),0) as orders,b.brand_margin as margin,ifnull(sum(s.available_qty),0) as stock,p.product_id as id,p.product_name as product,p.mrp from m_product_info p join m_vendor_brand_link b on b.brand_id=p.brand_id left outer join king_orders o on o.itemid in (select itemid from m_product_deal_link where product_id=p.product_id) and o.time > ".(time()-(24*90*60*60)).". left outer join t_stock_info s on s.product_id=p.product_id where p.brand_id=? group by p.product_id order by p.product_name asc",array($bid))->result_array());
		*/	
			
	}
	
	function jx_show_vendor_details()
	{
		$user=$this->auth();
		$vid=$this->input->post("v");
		$vendor=$this->db->query("select * from m_vendor_info where vendor_id=?",$vid)->row_array();
		$p=$this->db->query("select * from m_vendor_contacts_info where vendor_id=? order by id asc limit 1",$vid)->row_array();
		$brands=$this->db->query("select b.name from m_vendor_brand_link v join king_brands b on b.id=v.brand_id where v.vendor_id=? order by b.name asc",$vid)->result_array();
		echo "<h3 style='margin:0px;'>{$vendor['vendor_name']}, {$vendor['city_name']}</h3>";
		echo "<div>{$p['contact_name']}, {$p['contact_designation']}, Mobile : {$p['mobile_no_1']}, {$p['mobile_no_2']}, {$p['email_id_1']}</div>";
		echo "<h4 style='margin:0px;margin-top:5px;'>Brands supported</h4><p style='margin:3px;' class='overview_ven_brandlist'>";
		foreach($brands as $b)
			echo $b['name'].", ";
		echo '</p>';	
		echo '<a href="javascript:void(0)"  onclick="show_addvenbranddlg()">Add Brand</a>';	
	}
	
	/**
	 * function to load vendor un linked brands 
	 *
	 */
	function jx_ven_unavail_brands()
	{
		$output = array();
		
		$ven_id = $this->input->post('ven_id');
		$sql = "select a.id as brandid,a.name as brandname
						from king_brands a 
						left join m_vendor_brand_link b on b.brand_id = a.id 
						and vendor_id = ?   
						where b.id is null and a.name != ''
						order by a.name ";
		$res = $this->db->query($sql,$ven_id);
		if($res->num_rows())
		{
			$output['status'] = 'success';
			$output['brandlist'] = $res->result_array();
		}else
		{
			$output['status'] = 'error';	
		}
		$output['ven_id'] = $ven_id;
		
		echo json_encode($output);
	}
	
	function jx_upd_venbrandlink()
	{
		$user = $this->erpm->auth();
		$ven_id = $this->input->post('ven_id');
		$brandid = $this->input->post('bid');
		$brandmarg = $this->input->post('bmarg');
		$output = array();
		if($this->db->query("select count(*) as t from m_vendor_brand_link where vendor_id = ? and brand_id = ? ",array($ven_id,$brandid))->row()->t)
		{
			$output['status'] = 'error';
			$output['error'] = 'Brand is already linked';
		}else
		{
			$this->db->query("insert into m_vendor_brand_link (vendor_id,brand_id,brand_margin,applicable_from,applicable_till,created_on,created_by) values (?,?,?,unix_timestamp(),0,now(),?) ",array($ven_id,$brandid,$brandmarg,$user['userid']));
			
			$output['status'] = 'success';
			$output['linked_brands'] = $this->db->query("select distinct a.id as brandid,a.name as brandname from king_brands a join m_vendor_brand_link b on b.brand_id = a.id where vendor_id = ? order by brandname ",$ven_id)->result_array();
			
		}
		echo json_encode($output);		
	}
	
	function viewpo($poid)
	{
		
		$user=$this->auth(PURCHASE_ORDER_ROLE);
		
		$data['page']="viewpo";
		$data['po']=$this->db->query("select po.*,v.vendor_name from t_po_info po join m_vendor_info v on v.vendor_id=po.vendor_id where po_id=?",$poid)->row_array();
		$data['items']=$this->db->query("select i.*,p.product_name from t_po_product_link i join m_product_info p on p.product_id=i.product_id where i.po_id=?",$poid)->result_array();
		$data['grns']=$this->db->query("select g.*,v.vendor_name,0 as value from t_grn_product_link gp join t_grn_info g on g.grn_id=gp.grn_id join m_vendor_info v on v.vendor_id=g.vendor_id where gp.po_id=? group by g.grn_id",$poid)->result_array();
		$data['vouchers']=$this->db->query("select v.*,t.adjusted_amount from t_voucher_document_link t join t_voucher_info v on v.voucher_id=t.voucher_id where t.ref_doc_id=? and t.ref_doc_type=2",$poid)->result_array();
		$this->load->view("admin",$data);
	}
	
	
	function pending_pay_grns()
	{
		$user=$this->auth(FINANCE_ROLE);
		$data['grns']=$this->erpm->getpendingpaygrns();
		$data['page']="pending_pays";
		$this->load->view("admin",$data);
	}
	
	function pending_grns()
	{
		$user=$this->auth(FINANCE_ROLE);
		$data['grns']=$this->erpm->getpendinggrns();
		$data['page']="pending_grns";
		$this->load->view("admin",$data);
	}
	
	function purchaseorders($s=0,$e=0,$vid=0,$status="0",$pg=0)
	{
		$user=$this->auth(PURCHASE_ORDER_ROLE|FINANCE_ROLE);
		if($s!=0 && $e!=0 && (strtotime($s)<=0 || strtotime($e)<=0))
			show_404();
		
		if($status == -1)	
			$status = '';
			
		$data['pos']=$this->erpm->getpos_date_range($s,$e,$vid,$status);
		$data['total_po']=$total_po=$this->erpm->getpos_date_range_total($s,$e,$vid,$status,$pg);
		
		if($status == '')	
			$status = '-1';

		$data['pagination']=$this->_prepare_pagination(site_url("admin/purchaseorders/$s/$e/$vid/$status"), $total_po, 50, 7);
		
		$data['sdate']=$sdate?$sdate:'';
		$data['edate']=$edate?$edate:'';
		$data['vid']=$vid;
		$data['status']=$status;
		$data['page']="viewpos";
		$this->load->view("admin",$data);
	}
	
	function jx_getpos()
	{
		$user=$this->auth();
		$rs=$this->db->query("select * from t_po_info where vendor_id=? and po_status!=2 and po_status!=3",$this->input->post("v"))->result_array();
		foreach($rs as $r)
			echo '<a href="javascript:void(0)" onclick="loadpo('.$r['po_id'].')">'.$r['remarks'].' : PO'.$r['po_id'].' ('.$r['created_on'].')</a>';
	}
	
	function jx_searchbrands()
	{
		$user=$this->auth();
		$rs=$this->db->query("select name,id from king_brands where name like ? order by name asc limit 20","%{$_POST['q']}%")->result_array();
		foreach($rs as $r)
			echo '<a href="javascript:void(0)" onclick="addbrand(\''.$r['name'].'\',\''.$r['id'].'\')">'.$r['name'].'</a>';
	}
	
	function jx_getbrandmargin()
	{
		
		$user=$this->auth();
		$v=$_POST['v'];
		$b=$_POST['b'];
		echo $this->db->query("select brand_margin from m_vendor_brand_link where vendor_id=? and brand_id=?",array($v,$b))->row()->brand_margin;
	}
	
	function jx_grn_load_po()
	{
		$user=$this->auth();
		$poid=$this->input->post("p");
		$pois=$this->erpm->getpoitemsforgrn($poid);
		echo json_encode($pois);
	}
	
	function add_storage_loc()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|PURCHASE_ORDER_ROLE);
		if($_POST)
			$this->erpm->do_add_storage_loc();
		$data['page']="add_storage_loc";
		$this->load->view("admin",$data);
	}
	
	function storage_locs()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|PURCHASE_ORDER_ROLE);
		$data['locs']=$this->erpm->getstoragelocs();
		$data['page']="storage_locations";
		$this->load->view("admin",$data);
	}
	
	function viewgrn($grn=false)
	{
		if(!$grn)
			show_404();
		$user=$this->auth(FINANCE_ROLE|STOCK_INTAKE_ROLE);
		$data['grn']=$this->db->query("select g.*,v.vendor_name from t_grn_info g join m_vendor_info v on v.vendor_id=g.vendor_id where g.grn_id=?",$grn)->row_array();
		if(empty($data['grn']))
			show_error("Error while retrieving");
		$data['prods']=$this->db->query("select p.product_name,po.po_id,g.* from t_grn_product_link g join t_po_info po on po.po_id=g.po_id join m_product_info p on p.product_id=g.product_id where g.grn_id=?",$grn)->result_array();
		$data['invoices']=$this->db->query("select * from t_grn_invoice_link where grn_id=?",$grn)->result_array();
		$data['vouchers']=$this->db->query("select v.voucher_value,td.*,a.name as created_by from t_voucher_document_link td join t_voucher_info v on v.voucher_id=td.voucher_id join king_admin a on a.id=v.created_by where td.ref_doc_type=1 and td.ref_doc_id=?",$grn)->result_array();
		$data['page']="view_grn";
		$this->load->view("admin",$data);
	}
	
	function product_price_changelog($s='',$e='')
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|CALLCENTER_ROLE);
		if(empty($e))
		{
			$s=time()-(30*24*60*60);
			$e=time();
		}else{
			$s=strtotime($s);
			$e=strtotime($e)+(24*60*60);
		}
		$data['s']=$s;
		$data['e']=$e;
		$data['prods']=$this->db->query("select p.product_name,pc.*,a.name as created_by from product_price_changelog pc join m_product_info p on p.product_id=pc.product_id left outer join king_admin a on a.id=pc.created_by where pc.created_on between $s and $e order by pc.id desc")->result_array();
		$data['page']="product_price_changelog";
		$this->load->view("admin",$data);
	}
	
	function product_src_changelog($s='',$e='')
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		if(empty($e))
		{
			$s=time()-(30*24*60*60);
			$e=time();
		}else{
			$s=strtotime($s);
			$e=strtotime($e)+(24*60*60);
		}
		$data['s']=$s;
		$data['e']=$e;
		$data['prods']=$this->db->query("select p.product_name,p.is_sourceable as cur_src_status,pc.*,a.name as created_by from products_src_changelog pc join m_product_info p on p.product_id=pc.product_id left outer join king_admin a on a.id=pc.created_by where pc.created_on between $s and $e order by pc.id desc")->result_array();
		$data['page']="product_src_changelog";
		$this->load->view("admin",$data);
	}
	
	function list_deals_nsrc_prod($pg=0)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		$data['deals']=$this->db->query("select b.id,b.name,b.orgprice,b.price,sum(c.qty) as qty,sum(e.available_qty) as stk,sum(1) as ttl_linked_prods,sum(is_sourceable) as ttl_sourceable,a.publish,b.live
												from king_deals a 
												join king_dealitems b on a.dealid = b.dealid and b.is_pnh = 0 
												join m_product_deal_link c on c.itemid = b.id  
												join m_product_info d on d.product_id = c.product_id and d.is_sourceable = 0
												join t_stock_info e on e.product_id = d.product_id 
												where 1 
												group by b.id 
												order by b.name ")->result_array();
		$data['page']="list_deals_nsrc_prod";
		$this->load->view("admin",$data);
	}
	
	
	function pnh_version_price_change($v=0,$export=0)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE);
		if(!$v)
		{
			$v_raw=$this->db->query("select id from pnh_app_versions order by id desc limit 1")->row_array();
			if(empty($v_raw))
				show_error("No app versions defined");
			$v=$v_raw['id'];
		}
		$data['ver']=$ver=$this->db->query("select * from pnh_app_versions where id=?",$v)->row_array();
		$data['deals']=$deals=$this->erpm->pnh_getdealpricechange($v);
		$data['page']="pnh_version_price_change";
		if($export)
			$this->erpm->export_csv("version_price_change_version:{$ver['version_no']}",$deals);
		$this->load->view("admin",$data);
	}
	
	function deal_price_changelog($s='',$e='')
	{
		$user=$this->auth(DEAL_MANAGER_ROLE|CALLCENTER_ROLE);
		if(empty($e))
		{
			$s=time()-(30*24*60*60);
			$e=time();
		}else{
			$s=strtotime($s);
			$e=strtotime($e)+(24*60*60);
		}
		$data['s']=$s;
		$data['e']=$e;
		$data['deals']=$this->db->query("select p.name,pc.*,a.name as created_by from deal_price_changelog pc join king_dealitems p on p.id=pc.itemid left outer join king_admin a on a.id=pc.created_by where pc.created_on between $s and $e order by pc.id desc")->result_array();
		$data['page']="deal_price_changelog";
		$this->load->view("admin",$data);
	}
	
	function stock_intake_list($vid=0,$s=0,$e=0)
	{
		$this->auth(STOCK_INTAKE_ROLE);
		$pgt="";
		if($s==0)
		{
			$s=date("Y-m-d",mktime(0,0,0,date("n"),0));
			$e=date("Y-m-d");
//			$pgt="Recent ";
		}
//		else
			$pgt.="Stock Intakes between $s and $e";
		if($vid)
			$pgt.=" for Vendor:".$this->db->query("select vendor_name from m_vendor_info where vendor_id=?",$vid)->row()->vendor_name;
		$e="$e 23:59:59";
		$data['s']=$s;
		$data['e']=$e;
		$sql="select v.vendor_name,g.*,sum(p.invoice_qty) as invoiced,sum(p.received_qty) as received,count(p.product_id) as items,sum(i.purchase_inv_value) as invoice_value from t_grn_info g join m_vendor_info v on v.vendor_id=g.vendor_id join t_grn_product_link p on p.grn_id=g.grn_id left outer join t_grn_invoice_link i on i.grn_id=g.grn_id where 1";
		if($vid!=0)
			$sql.=" and g.vendor_id=$vid ";
		$sql.=" and g.created_on between '$s' and '$e' group by g.grn_id order by g.created_on desc";
		$data['grns']=$this->db->query($sql)->result_array();
		$data['page']="stock_intake_list";
		$data['pagetitle']=$pgt;
		$this->load->view("admin",$data);
	}

	function check_vendor_invs()
	{
		
		$user=$this->erpm->getadminuser();
		$vid=$this->db->query("select vendor_id from t_po_info where po_id=?",$_POST['poids'][0])->row()->vendor_id;
		
		$output = array();
		$proceed_grn = 0;
		foreach($_POST['invno'] as $i=>$no)
		{
			if($this->db->query("select count(*) as t from t_grn_invoice_link a join t_grn_info b on a.grn_id = b.grn_id where purchase_inv_no = ? and purchase_inv_date = ? and vendor_id  = ? ",array($no,$_POST['invdate'][$i],$vid))->row()->t)
				continue;
				
			$proceed_grn++;
		}
		 
		if(!$proceed_grn)
			$output['error'] = 'Vendor invoices are already available';
		else if(count($_POST['invno']) != $proceed_grn)
			$output['error'] = 'Some of Vendor invoices are already available';
		else
			$output['status'] = 'success';	

		echo json_encode($output);
				
	} 
	
	function apply_grn($poid="",$from="")
	{
		$poid="";
		$user=$this->auth(STOCK_INTAKE_ROLE);
		if($_POST)
		{
			$grn_id = $this->erpm->do_grn();
			$this->session->set_flashdata("notify_grn","Stock Intake done successfully-#".$grn_id);
			redirect("admin/apply_grn",'refresh');
			exit;
		}
		if(!empty($poid))
		{
			$data['po']=$this->erpm->getpo($poid);
			$data['po_items']=$this->erpm->getpoitemsforgrn($poid);
		}
		$r=array();
		
		 
		
		if($from)
			$from_date = $from;	
		else
			$from_date = date('Y-m-d',time()-7*24*60*60);
		
		$from = ' and date(t.created_on) >= "'.$from_date.'" ';	
		
		$pos=$this->db->query("select v.vendor_name,v.city_name,t.* from t_po_info t join m_vendor_info v on v.vendor_id=t.vendor_id where t.po_status!=2 and t.po_status!=3 $from order by v.vendor_name asc",$this->input->post("v"))->result_array();
		foreach($pos as $po)
		{
			$v=$po['vendor_name'].", ".$po['city_name'];
			if(!isset($r[$v]))
			 $r[$v]=array();
			$r[$v][]=$po;
		}
		$data['pos']=$r;
		$data['min_po_date']= $from_date;
		$data['page']="apply_grn";
		$this->load->view("admin",$data);
	}
	
	function po_product()
	{
		$user=$this->auth(PURCHASE_ORDER_ROLE);
		if($_POST)
			$this->erpm->do_po_prodwise();
		$data['page']="po_product";
		$this->load->view("admin",$data);
	}
	
	function bulk_createpo_byfile()
	{
		$user = $this->erpm->auth(PURCHASE_ORDER_ROLE);
		if($_POST)
		{
			$this->erpm->do_po_byfile();
		}
		$data['page']="bulk_createpo_byfile";
		$this->load->view("admin",$data);
	}
	
	function purchaseorder()
	{
		$user=$this->auth(PURCHASE_ORDER_ROLE);
		if($_POST)
		{
			$this->erpm->createpo();
			redirect("admin/purchaseorders");
		}
		$data['vendors']=$this->erpm->getvendors();
		$data['page']="purchaseorder";
		$this->load->view("admin",$data);
	}
	
	function searchvendor()
	{
		$user=$this->auth();
		$res=$this->erpm->searchvendors($_POST['q']);
	}

	function jx_productdetails()
	{
		$user=$this->auth();
		$id=$this->input->post("id");
		echo json_encode($this->erpm->getproductdetails($id));
	}
	
	function jx_getopenpolistbypid($pid=0)
	{
		$this->erpm->auth();
		
		$output = array();
		$output['product_name'] = $this->db->query("select product_name from m_product_info where product_id = ? ",$pid)->row()->product_name;
	
		$sql = "select sum(b.order_qty-b.received_qty) as qty 
					from t_po_info a
					join t_po_product_link b on a.po_id = b.po_id 
					where a.po_status < 2 and b.product_id = ? and (b.order_qty-b.received_qty) > 0
				";
		$output['ttl_open_qty'] = $this->db->query($sql,$pid)->row()->qty;
		
		$sql = "select c.vendor_id,c.vendor_name,b.po_id,(b.order_qty-b.received_qty) as qty,unix_timestamp(a.created_on) as po_date
						from t_po_info a
						join t_po_product_link b on a.po_id = b.po_id 
						join m_vendor_info c on c.vendor_id = a.vendor_id 
						where a.po_status < 2 and b.product_id = ? and (b.order_qty-b.received_qty) > 0
					order by po_date
				";
		$res = $this->db->query($sql,$pid);
				
		$ven_po_list = array();
		if($res->num_rows())
			$output['vendor_po_list'] = $res->result_array();	
		else
			$output['vendor_po_list'] = array();
		
		 echo json_encode($output);
	}
	
	function jx_load_unavail_products()
	{
		$reports=array();
		$vendor_id = $this->input->post('vid');
		$date_report=$_POST['dt_report'];
		
		$join_cond = '';
		$join_cond_fld = '';
		
		if($vendor_id)
		{
			$join_cond = ' join m_vendor_brand_link vb on vb.brand_id = b.id and vb.vendor_id = '.$vendor_id;
			$join_cond_fld = ',brand_margin';
		}
		
		
		if(isset($_POST['oldest_order']))
		{
			$order_by_cond = "order by init ".($_POST['oldest_order']);  
		}else
		{
			$order_by_cond = "order by product_name";
		}
			
		
	
	
		$sql="SELECT a.*,IFNULL(SUM(available_qty),0) AS available FROM (
					SELECT p.product_id,p.purchase_cost,
					b.id AS brandid,
					b.name AS brand,p.product_name,p.mrp,IFNULL(SUM(o.quantity*l.qty),0) AS qty,
					d.menuid,ifnull(m.name,m1.name) AS menu,t.init
					$join_cond_fld
					FROM king_orders o
					join king_transactions t on t.transid = o.transid 
					JOIN m_product_deal_link l ON l.itemid=o.itemid
					JOIN m_product_info p ON p.product_id=l.product_id
					JOIN king_brands b ON b.id=p.brand_id
					join king_dealitems e on e.id = l.itemid
					JOIN king_deals d ON d.dealid=e.dealid
					left JOIN pnh_menu m ON m.id=d.menuid and e.is_pnh = 1
					left join king_menu m1 on m1.id = d.menuid and e.is_pnh = 0
					$join_cond 
					WHERE o.status=0
					GROUP BY product_id
					ORDER BY brand ASC,p.product_name ASC ) AS a
					LEFT JOIN t_stock_info b ON a.product_id = b.product_id and available_qty > 0
					GROUP BY product_id
					HAVING qty > available
					$order_by_cond
					";
		
	
		
		$reports=$this->db->query($sql)->result_array();
		
		if($reports)
		{
			
			$prod_list = array();
			foreach($reports as $i=>$r)
			{
				if(!isset($prod_list[$r['product_id']]))
					$prod_list[$r['product_id']] = $r;
				else
					$prod_list[$r['product_id']]['qty'] += $r['qty'];	
	
				if(!isset($prod_list[$r['product_id']]['vendors']))
				{	
					$vendors=array();
					$rvendors=$this->db->query("select v.vendor_name,v.vendor_id from m_vendor_info v join m_vendor_brand_link b on v.vendor_id=b.vendor_id where b.brand_id=?",$r['brandid'])->result_array();
					$vendors=array();
					foreach($rvendors as $v)
						$vendors[]=$v;
					$prod_list[$r['product_id']]['vendors']=$vendors;
				}
			}
			$reports = array_values($prod_list);
			$reports['status']='success';
		}
		else 
		{
			$reports['status']='error';
			$reports['msg']='No data found';
		}
		echo json_encode($reports);
	}
	
	function jx_load_unavail_products_old()
	{
		$vendor_id = $this->input->post('vid');
		$join_cond = '';
		$join_cond_fld = '';
		if($vendor_id)
		{
			$join_cond = ' join m_vendor_brand_link vb on vb.brand_id = b.id and vb.vendor_id = '.$vendor_id;
			$join_cond_fld = ',brand_margin';
		}
		
		/*
		$sql="select p.product_id,o.transid,p.purchase_cost,sum(s.available_qty) as available,b.id as brandid,
						b.name as brand,p.product_name,p.mrp,(o.quantity*l.qty) as qty
						$join_cond_fld 
					from king_orders o join m_product_deal_link l on l.itemid=o.itemid join m_product_info p on p.product_id=l.product_id 
					join king_brands b on b.id=p.brand_id 
					$join_cond 
					left outer join t_stock_info s on s.product_id=l.product_id 
					where o.status=0
					group by o.id having sum(o.quantity*l.qty)>available or available is null order by brand asc,p.product_name asc
			";
		 */
		$sql = "SELECT a.*,IFNULL(SUM(available_qty),0) AS available FROM (
SELECT p.product_id,p.purchase_cost,
b.id AS brandid,
b.name AS brand,p.product_name,p.mrp,IFNULL(SUM(o.quantity*l.qty),0) AS qty,
d.menuid,ifnull(m.name,m1.name) AS menu
$join_cond_fld
FROM king_orders o 
JOIN m_product_deal_link l ON l.itemid=o.itemid 
JOIN m_product_info p ON p.product_id=l.product_id 
JOIN king_brands b ON b.id=p.brand_id 
join king_dealitems e on e.id = l.itemid
JOIN king_deals d ON d.dealid=e.dealid 
left JOIN pnh_menu m ON m.id=d.menuid and e.is_pnh = 1 
left join king_menu m1 on m1.id = d.menuid and e.is_pnh = 0
$join_cond
WHERE o.status=0 
GROUP BY product_id 
ORDER BY brand ASC,p.product_name ASC ) AS a
LEFT JOIN t_stock_info b ON a.product_id = b.product_id and available_qty > 0 
GROUP BY product_id 
HAVING qty > available 
order by product_name"; 
			
		
		$reports=$this->db->query($sql)->result_array();
		
		//echo $this->db->last_query();
		$prod_list = array();
		foreach($reports as $i=>$r)
		{
			if(!isset($prod_list[$r['product_id']]))
				$prod_list[$r['product_id']] = $r;
			else
				$prod_list[$r['product_id']]['qty'] += $r['qty'];	

			if(!isset($prod_list[$r['product_id']]['vendors']))
			{	
				$vendors=array();
				$rvendors=$this->db->query("select v.vendor_name,v.vendor_id from m_vendor_info v join m_vendor_brand_link b on v.vendor_id=b.vendor_id where b.brand_id=?",$r['brandid'])->result_array();
				$vendors=array();
				foreach($rvendors as $v)
					$vendors[]=$v;
				$prod_list[$r['product_id']]['vendors']=$vendors;
			}
		}
		$reports = array_values($prod_list);
		echo json_encode($reports);
	}
	
	function jx_searchproducts()
	{
		$user=$this->auth();
		$q=$this->input->post("q");
		$res=$this->erpm->searchproducts($q);
		foreach($res as $r)
			echo "<a href='javascript:void(0)' onclick='addproduct(\"{$r['product_id']}\",\"".htmlspecialchars($r['product_name'],ENT_QUOTES)."\",\"{$r['mrp']}\")'>{$r['product_name']}  <span style='color:red'>(stock:{$r['stock']})</span>  <span style='color:#ff9900'>(Mrp:Rs{$r['mrp']})</span></a>";
	}
	
	function jx_searchproductsfordeal()
	{
		$user=$this->auth();
		$q=$this->input->post("q");
		$res=$this->erpm->searchproductsfordeal($q);
		foreach($res as $r)
			echo "<a href='javascript:void(0)' onclick='addproduct".($_POST['type']=="group"?"g":"")."(\"{$r['product_id']}\",\"".htmlspecialchars($r['product_name'],ENT_QUOTES)."\",\"{$r['mrp']}\")'>{$r['product_name']}  <span style='color:red'>(stock:{$r['stock']})</span>  <span style='color:#ff9900'>(Mrp:Rs{$r['mrp']})</span></a>";
	}
	
	function jx_search_deals()
	{
		$user=$this->auth();
		$q=$this->input->post("q");
		$res=$this->db->query("select id,name from king_dealitems where name like ? order by name","%$q%")->result_array();
		foreach($res as $r)
			echo "<a href='javascript:void(0)' onclick='adddealitem(\"{$r['id']}\",\"".htmlspecialchars($r['name'])."\")'>{$r['name']}</a>";
	}
	
	function stock_unavail_report($partial=0,$s=0,$e=0,$is_pnh=0,$export=0)
	{
		$user=$this->auth(PURCHASE_ORDER_ROLE);
		$sql="select group_concat(distinct o.transid) as transid,p.purchase_cost,sum(s.available_qty) as available,b.id as brandid,b.name as brand,p.product_name,p.mrp,sum(o.quantity*l.qty) as qty from king_orders o join king_transactions t on t.transid=o.transid left outer join m_product_deal_link l on l.itemid=o.itemid left outer join products_group_orders og on og.order_id=o.id left outer join m_product_info p on p.product_id=ifnull(l.product_id,og.product_id) join king_brands b on b.id=p.brand_id join t_stock_info s on s.product_id=p.product_id where o.status=0";
		if($is_pnh==1)
			$sql.=" and t.is_pnh=1";
		elseif($is_pnh==2)
			$sql.=" and t.is_pnh=0";
		$data['from']=0;
		$data['to']=0;
		$data['is_pnh']=$is_pnh;
		$data['partial']=0;
		if($e!=0)
		{
			list($sy,$sm,$sd)=explode("-",$s);
			list($ey,$em,$ed)=explode("-",$e);
			$si=mktime(0,0,0,$sm,$sd,$sy);
			$ei=mktime(23,59,59,$em,$ed,$ey);
			$sql.=" and o.time between $si and $ei";
			$data['from']=$s;
			$data['to']=$e;
		}
		$sql.=" group by s.product_id having sum(o.quantity*ifnull(l.qty,1))>sum(s.available_qty) order by p.product_name,o.sno asc";
		$data['reports']=$reports=$this->db->query($sql)->result_array();
		foreach($reports as $i=>$r)
		{
			$vendors=array();
			$rvendors=$this->db->query("select v.vendor_name from m_vendor_info v join m_vendor_brand_link b on v.vendor_id=b.vendor_id where b.brand_id=?",$r['brandid'])->result_array();
			$vendors=array();
			foreach($rvendors as $v)
				$vendors[]=$v['vendor_name'];
			$reports[$i]['vendors']=implode(", ",array_unique($vendors));
		}
		$data['reports']=$reports;
		if($export)
		{
			ob_start();
			$f=fopen("php://output","w");
			fputcsv($f, array("Product Name","MRP","Purchase Cost","Required Quantity","Brand","Vendors","Order"));
			foreach($reports as $r)
				fputcsv($f, array($r['product_name'],$r['mrp'],$r['purchase_cost'],$r['qty']-$r['available'],$r['brand'],$r['vendors'],$r['transid']));
			fclose($f);
			$csv=ob_get_clean();
			ob_clean();
		    header('Content-Description: File Transfer');
		    header('Content-Type: text/csv');
		    header('Content-Disposition: attachment; filename='.("stock_unavailability_".date("d_m_y").".csv"));
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
		$this->load->view("admin/body/stock_unavail_report",$data);
	}


	function pnh_stock_unavail_report($pg=0)
	{
		$user=$this->auth(PURCHASE_ORDER_ROLE|CALLCENTER_ROLE);
		
		if($_POST)
		{
			
			$date_type = $this->input->post('date_type');
		
			$tids = $this->input->post('tids');
			$mids = $this->input->post('mids');
			
			$terr_id = isset($tids[0])?$tids[0]:0;
			$menu_id = isset($mids[0])?$mids[0]:0;
			
			$cond = '';
			if($terr_id)
				$cond .= ' and e.territory_id = "'.$terr_id.'"';	
			
			if($menu_id)
				$cond .= ' and d1.menuid = "'.$menu_id.'"';
			
			if($date_type == 1)
			{
				$from = $this->input->post('from');
				$to = $this->input->post('to');		
			}else
			{
				$from = '2012-01-01';
				$to = date('Y-m-d');
			}
			
			$from  = strtotime($from.' 00:00:00');
			$to  = strtotime($to.' 23:59:59');
			$cond .= " and t.init between $from and $to ";
						
			$limit = 100;
			// get pending orders process
			$sql = "
						
					select brand_id,brand_name,g.product_id,product_name,order_date,(ttl_req_qty) as ttl_req_qty,sum(ifnull(s.available_qty,0)) as avail_qty,g.mrp,purchase_cost,fran_order_det  
						from 
						((select c.brand_id as brand_id,d.name as brand_name,
								c.product_id,trim(c.product_name) as product_name,
								sum(a.quantity*b.qty) as ttl_req_qty,
								date(from_unixtime(a.time)) as order_date,
								c.mrp,c.purchase_cost,
								group_concat(distinct concat(a.transid,':',unix_timestamp(date(from_unixtime(t.init))),':',a.id,':',t.franchise_id,':',e.franchise_name,':',b.qty*a.quantity,':',datediff(curdate(),date(from_unixtime(e.created_on)))) ORDER BY t.init ASC ) as fran_order_det 
							from king_orders a
							join king_transactions t on t.transid = a.transid 
							join products_group_orders o on o.order_id = a.id
							join m_product_group_deal_link b on b.itemid = a.itemid
							join m_product_info c on c.product_id = o.product_id 
							join king_brands d on c.brand_id = d.id 
							join king_dealitems di on di.id = a.itemid
							join king_deals d1 on d1.dealid = di.dealid
							join pnh_m_franchise_info e on e.franchise_id = t.franchise_id 
							where a.status = 0 and t.is_pnh = 1 $cond   
							group by c.product_id 
							)
						union(select c.brand_id as brand_id,d.name as brand_name,
								c.product_id,trim(c.product_name) as product_name,
								sum(a.quantity*b.qty) as ttl_req_qty,date(from_unixtime(a.time)) as order_date,
								c.mrp,c.purchase_cost,
								group_concat(distinct concat(a.transid,':',unix_timestamp(date(from_unixtime(t.init))),':',a.id,':',t.franchise_id,':',e.franchise_name,':',b.qty*a.quantity,':',datediff(curdate(),date(from_unixtime(e.created_on)))) ORDER BY t.init ASC ) as fran_order_det 
							from king_orders a 
							join king_transactions t on t.transid = a.transid 
							join m_product_deal_link b on a.itemid = b.itemid  
							join m_product_info c on c.product_id = b.product_id 
							join king_brands d on c.brand_id = d.id 
							join pnh_m_franchise_info e on e.franchise_id = t.franchise_id
							join king_dealitems di on di.id = a.itemid
							join king_deals d1 on d1.dealid = di.dealid 
							where a.status = 0 and t.is_pnh = 1  $cond 
							group by c.product_id  
							)) as g 
						left join t_stock_info s on s.product_id = g.product_id 
						group by g.product_id 
						having ttl_req_qty > 0  and avail_qty < ttl_req_qty 
						order by g.product_name,g.product_id      
							
					";
			$res = $this->db->query($sql,array($from,$to));
			
		//	echo $this->db->last_query();
			
			$output = array('data'=>array(),'status'=>'');
			if($res->num_rows())
			{
				$output['status'] = 'success';
				$actual_prod_list = $res->result_array();
				
				$prod_list = array();
				for($i=$pg;$i<$pg+$limit;$i++)
					if(isset($actual_prod_list[$i]))
						$prod_list[] = $actual_prod_list[$i];
				
				$output['data'] = $prod_list;
				
				$this->load->library('pagination');
				
				$config['base_url'] = site_url("admin/pnh_stock_unavail_report");
				$config['total_rows'] = $total = $res->num_rows();
				$config['per_page'] = $limit;
				$config['uri_segment'] = 3; 
				$config['num_links'] = 5;
				
				$this->config->set_item('enable_query_strings',false);
				$this->pagination->initialize($config); 
				
				$output['total'] = $total;
				$output['pg'] = $pg;
				$output['pagination'] = $this->pagination->create_links();
				$this->config->set_item('enable_query_strings',true);
		
				
					
			}else
			{
				$output['status'] = 'error';
				$output['message'] = 'no data found';
			}
			echo json_encode($output);
			exit;		
		}
		
		$data['page'] = 'pnh_stock_unavail_report';
		$this->load->view("admin",$data);
	}
	
	function jx_pnh_stock_unavail_terr_menu()
	{

		$user=$this->auth(PURCHASE_ORDER_ROLE|CALLCENTER_ROLE);

		$date_type = $this->input->post('date_type');
		
		$tids = $this->input->post('tids');
		$mids = $this->input->post('mids');
		
		$terr_id = isset($tids[0])?$tids[0]:0;
		$menu_id = isset($mids[0])?$mids[0]:0;
		
		$cond = '';
		if($terr_id)
			$cond .= ' and f.territory_id = "'.$terr_id.'"';	
		
		if($menu_id)
			$cond .= ' and c.menuid = "'.$menu_id.'"';
		
		if($date_type == 1)
		{
			$from = $this->input->post('from');
			$to = $this->input->post('to');		
		}else
		{
			$from = '2012-01-01';
			$to = date('Y-m-d');
		}
		
		$from  = strtotime($from);
		$to  = strtotime($to);
		
		$cond .= " and d.init between $from and $to ";
		
		// get pending orders process
		
		$sql = "
					select  menuid,e.name as menu_name,territory_id,territory_name,0 as qty   
					from king_orders a
					join king_dealitems b on a.itemid = b.id 
					join king_deals c on c.dealid = b.dealid  
					join king_transactions d on a.transid = d.transid 
					join pnh_menu e on e.id = c.menuid 
					join pnh_m_franchise_info f on f.franchise_id = d.franchise_id
					join pnh_m_territory_info g on g.id = f.territory_id 
					where a.status = 0 and d.is_pnh = 1 $cond 
				";
		$res = $this->db->query($sql,array($from,$to));
		$output = array('terr'=>array(),'menu'=>array(),'status'=>'');
		if($res->num_rows())
		{
			$output['status'] = 'success';
			foreach($res->result_array() as $row)
			{
				if(!isset($output['terr'][$row['territory_id']]))
					$output['terr'][$row['territory_id']] = array($row['territory_name'],0);
					
				if(!isset($output['menu'][$row['menuid']]))
					$output['menu'][$row['menuid']] = array($row['menu_name'],0);
						
				$output['terr'][$row['territory_id']][1] += $row['qty'];
				$output['menu'][$row['menuid']][1] += $row['qty'];
			}	
		}
		
		
		echo json_encode($output);
	}
	
	function jx_pnh_stock_unavail_report()
	{
		$user=$this->auth(PURCHASE_ORDER_ROLE|CALLCENTER_ROLE);
		$data['page'] = 'pnh_stock_unavail_report';
		$this->load->view("admin",$data);
	}
	
	function partial_shipment($s='',$e='')
	{
		ini_set('memory_limit','256M');
		//	error_reporting(E_ALL);

		$user=$this->auth(ORDER_BATCH_PROCESS_ROLE);
		
		$s = $s?$s:date('Y-m-d');
		$e = $e?$e:date('Y-m-d');
		
		$s = strtotime($s.' 00:00:00');
		$e = strtotime($e.' 23:59:59');
		
		$data['orders']=$this->erpm->getpartialshipments($s,$e);
		
		$data['s'] = $s;
		$data['e'] = $e;
		$data['pagetitle']="Partial Shipment ".date('d/m/Y',($s)).'-'.date('d/m/Y',($e));
		$data['page']="orders";
		$data['partial_list']=true;
		$this->load->view("admin",$data);
	}
	
	function disabled_but_possible_shipment()
	{
		ini_set('memory_limit','512M');
		$user=$this->auth(ORDER_BATCH_PROCESS_ROLE);
		$data['orders']=$this->erpm->getdisabledbutpossibleshipments();
		$data['pagetitle']="Disabled but possible Shipments";
		$data['page']="orders";
		$data['partial_list']=true;
		$this->load->view("admin",$data);
	}
	
	function order_summary($s=false,$e=false)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		if(!$s)
			$e=$s=date("Y-m-d");
		$from=strtotime($s);
		$to=strtotime("23:59:59 $e");
		$r_orders=$this->db->query("select ti.territory_name,ti.id as territory_id,m.name as menu_name,d.menuid,f.franchise_id,f.franchise_name,t.amount,p.brand_id,p.product_id,o.time,o.transid,i.name as deal,i.id as itemid,p.product_name,sum(s.available_qty) as stock,i.price from king_orders o join king_dealitems i on i.id=o.itemid join king_deals d on d.dealid = i.dealid join king_transactions t on t.transid=o.transid left outer join m_product_deal_link l on l.itemid=i.id left outer join products_group_orders po on po.order_id=o.id left outer join m_product_info p on p.product_id=ifnull(l.product_id,po.product_id) left outer join t_stock_info s on s.product_id=ifnull(p.product_id,po.product_id) left join pnh_m_franchise_info f on f.franchise_id = t.franchise_id left join pnh_menu m on m.id = d.menuid and t.is_pnh = 1 left join pnh_m_territory_info ti on ti.id = f.territory_id where o.time between $from and $to group by o.transid,p.product_id order by o.time desc")->result_array();
		$data['pnh_menu'] = $this->db->query("select * from pnh_menu order by name")->result_array();
		$data['pnh_terr'] = $this->db->query("select * from pnh_m_territory_info order by territory_name")->result_array();
		$order=array();
		$deal=array();
		foreach($r_orders as $o)
		{
			if(!isset($order[$o['transid']]))
				$order[$o['transid']]=array();
			if(!isset($deal[$o['transid']."-".$o['itemid']]))
			{
				$deal[$o['transid']."-".$o['itemid']]=array();
				$order[$o['transid']][]=$o;
			}
			$deal[$o['transid']."-".$o['itemid']][]=$o;
		}
		$data['s']=date("d/m/y",$from);
		$data['e']=date("g:ia d/m/y",$to);
		$data['orders']=$order;
		$data['deal']=$deal;
		$data['page']="order_summary";
		$this->load->view("admin",$data);
	}
	
    function orders_status_summary($s=false,$e=false)
	{
		$this->erpm->auth();
		if(!$s)
			$e=$s=date("Y-m-d");
		$from=strtotime($s);
		$to=strtotime("23:59:59 $e");
		$r_orders=$this->db->query("select ti.territory_name,ti.id as territory_id,m.name as menu_name,d.menuid,f.franchise_id,f.franchise_name,t.amount,p.brand_id,p.product_id,o.time,o.transid,i.name as deal,i.id as itemid,p.product_name,sum(s.available_qty) as stock,i.price from king_orders o join king_dealitems i on i.id=o.itemid join king_deals d on d.dealid = i.dealid join king_transactions t on t.transid=o.transid left outer join m_product_deal_link l on l.itemid=i.id left outer join products_group_orders po on po.order_id=o.id left outer join m_product_info p on p.product_id=ifnull(l.product_id,po.product_id) left outer join t_stock_info s on s.product_id=ifnull(p.product_id,po.product_id) left join pnh_m_franchise_info f on f.franchise_id = t.franchise_id left join pnh_menu m on m.id = d.menuid and t.is_pnh = 1 left join pnh_m_territory_info ti on ti.id = f.territory_id where o.time between $from and $to group by o.transid,p.product_id order by o.time desc")->result_array();
		$data['pnh_menu'] = $this->db->query("select * from pnh_menu order by name")->result_array();
		$data['pnh_terr'] = $this->db->query("select * from pnh_m_territory_info order by territory_name")->result_array();
		$data['pnh_towns'] = $this->db->query("select id,town_name from pnh_towns order by town_name")->result_array();
		$data['pnh_menu'] = $this->db->query("select mn.id,mn.name from pnh_menu mn
                                                            join king_deals deal on deal.menuid=mn.id
                                                            where mn.status=1 
                                                            group by mn.id
                                                            order by mn.name")->result_array();
                
		$data['pnh_brands'] = $this->db->query("select br.id,br.name from king_brands br
                                            join king_orders o on o.brandid=br.id
                                            group by br.id
                                            order by br.name")->result_array();
                
		$order=array();
		$deal=array();
		foreach($r_orders as $o)
		{
			if(!isset($order[$o['transid']]))
				$order[$o['transid']]=array();
			if(!isset($deal[$o['transid']."-".$o['itemid']]))
			{
				$deal[$o['transid']."-".$o['itemid']]=array();
				$order[$o['transid']][]=$o;
			}
			$deal[$o['transid']."-".$o['itemid']][]=$o;
		}
		$data['s']=date("d/m/y",$from);
		$data['e']=date("g:ia d/m/y",$to);
		$data['orders']=$order;
		$data['deal']=$deal;
		$data['page']="orders_status_summary";
		$this->load->view("admin",$data);
	}
        
        /*
         * Dispay order list by status
         */
	function jx_orders_status_summary($type,$date_from,$date_to,$terrid,$townid,$franchiseid,$menuid,$brandid,$pg=0,$limit=25) 
	{
            $this->load->model('erpmodel','erpm');
            
            $this->erpm->auth();
            //$type = $this->input->post('type');
            $st_ts = strtotime($date_from.' 00:00:00');
            $en_ts = strtotime($date_to.' 23:59:59');

            $data['type']=$type;
            $data['st_ts']=$st_ts;
            $data['en_ts']=$en_ts;
            $data['terrid']=$terrid;
            $data['townid']=$townid;
            $data['franchiseid']=$franchiseid;
            $data['menuid']=$menuid;
            $data['brandid']=$brandid;
            $data['pg']=$pg;
            $data['limit']=$limit;
            
            
//            print_r($data);die();
            $this->load->view("admin/body/jx_orderstatus_summary",$data);

        }

        /***
         * function to get franchise details
         * @param $franchiseid
         */
        function jx_franchise_creditnote($franchiseid) {
            $acc_statement = $this->erpm->get_franchise_account_stat_byid($franchiseid);	
            $net_payable_amt = $acc_statement['net_payable_amt'];
            $credit_note_amt = $acc_statement['credit_note_amt'];
            $shipped_tilldate = $acc_statement['shipped_tilldate'];
            $paid_tilldate = $acc_statement['paid_tilldate'];
            $uncleared_payment = $acc_statement['uncleared_payment'];		
            $cancelled_tilldate = $acc_statement['cancelled_tilldate'];
            $ordered_tilldate = $acc_statement['ordered_tilldate'];
            $not_shipped_amount = $acc_statement['not_shipped_amount'];
            $acc_adjustments_val = $acc_statement['acc_adjustments_val'];
            
            $pending_payment = $acc_statement['pending_payment'];
            
            echo '<div style="margin-bottom: 0px;clear: both;overflow: hidden">
	
                    <div class="dash_bar_right" style="background: tomato">
                    Pending Payment : <span>Rs '.formatInIndianStyle($shipped_tilldate-($paid_tilldate+$acc_adjustments_val+$credit_note_amt),2).'</span>
                    </div>
                    <div class="dash_bar_right">
                    UnCleared Payments : <span>Rs '.formatInIndianStyle($uncleared_payment,2).'</span>
                    </div>

                    <div class="dash_bar_right">
                            Adjustments : <span>Rs '.formatInIndianStyle($acc_adjustments_val,2).'</span>
                    </div>

                    <div class="dash_bar_right">
                    Paid till Date : <span>Rs '.formatInIndianStyle($paid_tilldate,2).'</span>
                    </div>


                    <div class="dash_bar_right">
                    Credit Notes Raised : <span>Rs '.formatInIndianStyle($credit_note_amt,2).'</span>
                    </div>
                    <div class="dash_bar_right">
                    Bounced/Cancelled : <span>Rs '.formatInIndianStyle($cancelled_tilldate,2).'</span>
                    </div>
                    <div class="dash_bar_right">
                    Unshipped : <span>Rs '.formatInIndianStyle($not_shipped_amount,2).'</span>
                    </div>
                    <div class="dash_bar_right">
                    Shipped : <span>Rs '.formatInIndianStyle($shipped_tilldate,2).'</span>
                    </div>
                    <div class="dash_bar_right">
                    Ordered : <span>Rs '.formatInIndianStyle($ordered_tilldate,2).'</span>
                    </div>

                    <div class="dash_bar_right">
                    Credit Limit : <span>Rs '.formatInIndianStyle($f['credit_limit']).'</span>
                    </div>
                    </div>';
            
        }
        /*
         * function to pull brands of selected menuid
         * @param type $menuid
         */
        function jx_get_brandsbymenuid($menuid) {
            $this->erpm->auth();
			
			
            
            $output = array();
            // populate all towns in territory 
            $brands_list_res = $this->db->query("select br.id,br.name from king_brands br join king_deals deal on deal.brandid=br.id join king_orders o on o.id=deal.dealid where deal.menuid= ? order by br.name ",$terrid);
            if($brands_list_res->num_rows())
            {
                $output['status'] = 'success';
                $output['brands'] = json_encode($brands_list_res->result_array());
            }else
            {
                $output['status'] = 'error';
                $output['message'] = 'No towns for territory';
            }
            echo json_encode($output);
        }
        /**
         * function to generate town list by territoryid 
         * @param type $townid
         */
        function jx_suggest_townbyterrid($terrid)
        {
            $this->erpm->auth();
            
            $output = array();
            // populate all towns in territory 
            $town_list_res = $this->db->query("select id,town_name from pnh_towns where territory_id = ? order by town_name ",$terrid);
            if($town_list_res->num_rows())
            {
                $output['status'] = 'success';
                $output['towns'] = json_encode($town_list_res->result_array());
            }else
            {
                $output['status'] = 'error';
                $output['message'] = 'No towns for territory';
            }
            echo json_encode($output);
        }
        /**
         * Function to fetch all franchise under territory and town
         * @param type $terrid
         * @param type $townid
         */
        function jx_suggest_fran($terrid=0,$townid=0)
        {
            // populate all franchise in town 
            $this->erpm->auth();
            // echo json_encode(array("status"=>"success","franchise"=>"$terrid,$townid"));
            $output = array();
            $sql_terr =($terrid!=00) ? " territory_id = $terrid and " : "";
            // populate all towns in territory 
            $franchise_list_res = $this->db->query("select franchise_id,franchise_name from pnh_m_franchise_info where $sql_terr town_id = ? order by franchise_name ",array($townid));
            if($franchise_list_res->num_rows() and $townid != '00')
            {
                $output['status'] = 'success';
                $output['franchise'] = json_encode($franchise_list_res->result_array());
            }else
            {
                $output['status'] = 'error';
                $output['message'] = 'No franchise under this town.';
            }
            echo json_encode($output);
            
        }
        
	function orders($status=0,$s=false,$e=false,$orders_by='all',$limit=50,$pg=0)
	{
		$user=$this->auth(CALLCENTER_ROLE|PARTNER_ORDERS_ROLE);
		if($s!=false && $e!=false && (strtotime($s)<=0 || strtotime($e)<=0))
			show_404();
			
		$data['pagetitle']="";
		$data['cur_pg']=$pg;
		$data['perpage_limit']=$limit;
		
		$data['total_orders']=$this->erpm->getordersbytransaction_date_range($status,$s,$e,-1,$limit,$orders_by);
		$data['orders']=$this->erpm->getordersbytransaction_date_range($status,$s,$e,$pg,$limit,$orders_by);
		
		
		$this->load->library('pagination');

		$s = $s?$s:0;
		$e = $e?$e:0;
		
		$config['base_url'] = site_url("admin/orders/$status/$s/$e/$orders_by/$limit");
		$config['total_rows'] = $data['total_orders'];
		$config['per_page'] = $limit;
		$config['uri_segment'] = 8; 
		$config['num_links'] = 5;
		
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config); 
		
		$data['orders_pagination'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		
		if($status==1)
		{
			$data['pagetitle']="Pending ";
			$data['pending']=true;
		}
		if($e)
			$data['pagetitle'].="between $s and $e";
			
			
		$data['page']="orders";
		$this->load->view("admin",$data);
	}

	function changeshipaddr($transid=false)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		if(empty($transid))
			redirect("admin/orders");
		$this->erpm->do_changetranshipaddr($transid);
		redirect("admin/trans/$transid");
	}

	function changebilladdr($transid=false)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		if(empty($transid))
			redirect("admin/orders");
		$this->erpm->do_changetranbilladdr($transid);
		redirect("admin/trans/$transid");
	}
	
	function setprioritytrans($transid)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		$msg=$this->input->post("msg");
		$this->db->query("update king_transactions set priority=1,priority_note=? where transid=?",array($msg,$transid));
		$cmsg="High priority assigned<br>Msg : $msg";
		$this->erpm->do_trans_changelog($transid,$cmsg);
	}
	
	function cancel_orders()
	{
		$user=$this->auth(CANCEL_ORDER);
		$r=0;
		if(empty($_POST))
			show_error("where is the milk for cookie?");
		if($this->input->post("refund"))
			$this->erpm->do_cancel_orders();
		foreach(array("transid","oids") as $i)
			$$i=$this->input->post("$i");
		foreach($this->db->query("select (i_price-i_coup_discount)*quantity as i_price from king_orders where id in ('".implode("','",$oids)."')")->result_array() as $o)
			$r+=$o['i_price'];
		$data['user']=$this->db->query("select u.name from king_orders o join king_users u on u.userid=o.userid where o.transid=?",$transid)->row()->name;
		$data['refund']=$r;
		$data['page']="cancel_order";
		$this->load->view("admin",$data);
	}
	
	/**
	 * function to process stock corrections 
	 */
	function stock_correction()
	{
		$user=$this->erpm->auth(STOCK_CORRECTION);
		if(empty($_POST))
			die;
		 
		foreach(array("pid","msg","loc","corr","type","mrp_prod","stk_transfer") as $i)
			$$i=$this->input->post("$i");
		$p=$this->db->query("select * from m_product_info where product_id=?",$pid)->row_array();
		
		if($mrp_prod == 'new' && $type == 1)
		{
			$mrp = $this->input->post('n_mrp');
			$bc = $this->input->post('n_barcode');
		}else
		{
			list($bc,$mrp,$ps_loc_id,$ps_rb_id) = explode('_',$mrp_prod);
		}
		
		
		
		// stock outu processed from the selected product 
		if($type == 0)
		{
			$loc_id = $ps_loc_id;
			$rb_id = $ps_rb_id;
		}else
		{
			list($loc_id,$rb_id) = explode('_',$loc);
		}
		
		
		// check if brand has the location that has been selected 
		$r=$this->db->query("select r.location_id,r.id 
													from m_rack_bin_brand_link b 
													join m_rack_bin_info r on r.id=b.rack_bin_id 
													where b.brandid=?
								",$p['brand_id'])->row_array();
		if(empty($r))
			show_error("No rack bins associated to brand");
		
		// check and perform stock transfer	
		if($type == 0 && $stk_transfer == 1)
		{
			$dest_prodid = $this->input->post('dest_prodid');
			$dest_prod_stockdet = $this->input->post('dest_prod_stockdet');
			$s_imeino_list = $this->input->post('s_imeino');
			
			
			$st_pmsg = 'Stock Transfered To '.$dest_prodid.' Qty '.$corr.' '.($s_imeino_list?('<b>IMEI LIST </b><br>'.implode('<br>',$s_imeino_list)):'').' <br> Note:'.$msg;
			$this->erpm->_upd_product_stock($pid,$mrp,$bc,$loc_id,$rb_id,0,$corr,0,$type,0,-1,$st_pmsg);	
			
			$d_p_msg = 'Stock Transfered From '.$pid.' Qty '.$corr.' '.($s_imeino_list?('<br><b>IMEI LIST </b><br>'.implode('<br>',$s_imeino_list)):'');
			if($dest_prod_stockdet == "new")
			{
				$d_pbc = $this->input->post('dest_prod_newstk_bc');
				$d_pmrp = $this->input->post('dest_prod_newstk_mrp');
				$d_prb = $this->input->post('dest_prod_newstk_rbid');
				
				$d_ploc = $this->db->query('select location_id from m_rack_bin_info where id = ? ',$d_prb)->row()->location_id;
					
				$upd_stk_id = $this->erpm->_upd_product_stock($dest_prodid,$d_pmrp,$d_pbc,$d_ploc,$d_prb,0,$corr,0,1,0,-1,$d_p_msg);
			}
			else 
			{
				list($d_pbc,$d_pmrp,$d_ploc,$d_prb) = explode('_',$dest_prod_stockdet);
				$upd_stk_id = $this->erpm->_upd_product_stock($dest_prodid,$d_pmrp,$d_pbc,$d_ploc,$d_prb,0,$corr,0,1,0,-1,$d_p_msg);
			}

			$s_imeino_list = $this->input->post('s_imeino');
			foreach($s_imeino_list as $s_imei)
			{
				
				// check if imei entry is in log 
				$imei_log_res = $this->db->query('select * from t_imei_update_log where imei_no = ? and is_active = 1 ',array($s_imei));
				if(!$imei_log_res->num_rows())
				{
					$imei_det = $this->db->query('select * from t_imei_no where imei_no = ? and status = 0 ',array($s_imei))->row_array();
					
					$imei_upd_det = array();
					$imei_upd_det['imei_no'] = $imei_det['imei_no'];
					$imei_upd_det['product_id'] = $imei_det['product_id'];
					$imei_upd_det['stock_id'] = $imei_det['stock_id'];
					$imei_upd_det['grn_id'] = $imei_det['grn_id'];
					$imei_upd_det['is_active'] = 0;
					$imei_upd_det['logged_by'] = $user['userid'];
					$imei_upd_det['logged_on'] = date('Y-m-d H:i:s',$imei_det['created_on']);
					$this->db->insert('t_imei_update_log',$imei_upd_det);
				}else
				{
					$imei_log_det = $imei_log_res->row_array();
					$this->db->query('update t_imei_update_log set is_active = 0 where id = ? ',$imei_log_det['id']);
				}
				
				$this->db->query('update t_imei_no set product_id = ?,stock_id=? where imei_no = ? and product_id = ? and status = 0 ',array($dest_prodid,$upd_stk_id,$s_imei,$pid));
				
				$imei_det = $this->db->query('select * from t_imei_no where imei_no = ? and status = 0 ',array($s_imei))->row_array();
				
				$imei_upd_det = array();
				$imei_upd_det['imei_no'] = $imei_det['imei_no'];
				$imei_upd_det['product_id'] = $dest_prodid;
				$imei_upd_det['stock_id'] = $upd_stk_id;
				$imei_upd_det['grn_id'] = 0;
				$imei_upd_det['is_active'] = 1;
				$imei_upd_det['logged_by'] = $user['userid'];
				$imei_upd_det['logged_on'] = date('Y-m-d H:i:s',$imei_det['created_on']);
				$this->db->insert('t_imei_update_log',$imei_upd_det);
				
			}

		}else
		{
			$this->erpm->_upd_product_stock($pid,$mrp,$bc,$loc_id,$rb_id,0,$corr,0,$type,0,-1,$msg);
		}
		 
		$this->erpm->flash_msg("Stock corrected");
		redirect("admin/product/$pid");
		 
	}

	/**
	 * function to get product stock list details by product id  
	 */
	function jx_getdestproductstkdet($sp_stkid,$dpid)
	{
		$this->erpm->auth();
		
		$sp_stkdet = $this->db->query("select  a.location_id,a.rack_bin_id,concat(rack_name,bin_name) as rb_name,a.product_barcode,a.mrp,sum(a.available_qty) as available_qty,concat('Rs',ifnull(a.mrp,0),' - ',rack_name,bin_name,' - ',a.product_barcode) as stk_prod
								from t_stock_info a
								join m_rack_bin_info b on a.rack_bin_id = b.id 
								where a.stock_id = ? 
								group by a.mrp,a.location_id,a.rack_bin_id,a.product_barcode  
							",$sp_stkid)->row_array();
		
		$output = array();
		$output['stk_list'] = array();
		
		$sql_stkmrpprod = "select  a.location_id,a.rack_bin_id,concat(rack_name,bin_name) as rb_name,a.product_barcode,a.mrp,sum(a.available_qty) as available_qty,concat('Rs',ifnull(a.mrp,0),' - ',rack_name,bin_name,' - ',a.product_barcode) as stk_prod,
									if(a.product_barcode='".($sp_stkdet['product_barcode']?$sp_stkdet['product_barcode']:'0')."',1,0) as bc_rel
								from t_stock_info a
								join m_rack_bin_info b on a.rack_bin_id = b.id 
								where a.product_id = ? and a.mrp = ?
								group by a.mrp,a.location_id,a.rack_bin_id,a.product_barcode  
								order by bc_rel desc  
							";
		$stkmrpprod_res = $this->db->query($sql_stkmrpprod,array($dpid,$sp_stkdet['mrp']));
		if($stkmrpprod_res->num_rows())
		{
			$output['stk_list'] = $stkmrpprod_res->result_array();
		}
		
		$output['product_id'] = $dpid;
		$output['mrp'] = $sp_stkdet['mrp'];
		$output['barcode'] = '';
		$output['location'] = array();
		$loc_det_res = $this->db->query("select location_id,rack_bin_id,concat(rack_name,bin_name) as rb_name from m_rack_bin_brand_link a join m_rack_bin_info b on a.rack_bin_id = b.id join m_product_info c on c.brand_id = a.brandid where c.product_id = ? ",$dpid);
		 
		if($loc_det_res->num_rows())
		{
			$loc_det = $loc_det_res->result_array();	
			$output['location'] = $loc_det;
		}
		
		echo json_encode($output);	
			
	}
	
	/**
	 * function to check if imei belongs to scanned product 
	 */
	function jx_chkimeifortransfer($pid,$imeino)
	{
		$this->erpm->auth();
		
		$output = array();
		// fetch imei details 
		$imei_det_res = $this->db->query('select product_id,status,order_id from t_imei_no where imei_no = ? ',$imeino);
		if(!$imei_det_res->num_rows())
		{
			$output['status'] = 'error';
			$output['error'] = 'Invalid Serialno Scanned'; 
		}else
		{
			// check if imei is not in stock 
			$imei_det = $imei_det_res->row_array();
			if($imei_det['product_id'] != $pid)
			{
				$output['status'] = 'error';
				$output['error'] = 'Serialno does not belong to this product';	
			}else
			{
				if($imei_det['status'] == 0)
				{
					$output['status'] = 'success';	
				}else
				{
					$output['status'] = 'error';
					$output['error'] = 'Serialno is already sold';
				}
			}
			
		}
		echo json_encode($output);
	}
	
	function cancel_invoice($invno="")
	{
		$user=$this->erpm->auth(true);
		
		$invoice=$this->db->query("select id,transid,order_id,invoice_no from king_invoice where invoice_no=? and invoice_status=1",$invno)->result_array();
		if(empty($invno) || empty($invoice))
			show_error("Invoice not found or Invoice already cancelled");
		$transid=$invoice[0]['transid'];
		$oids=array();
		foreach($invoice as $i)
			$oids[]=$i['order_id'];
		$orders=$this->db->query("select quantity as qty,itemid,id from king_orders where id in ('".implode("','",$oids)."')")->result_array();
		
		
		
		$batch_id = $this->db->query("select batch_id from shipment_batch_process_invoice_link where invoice_no=?",$invno)->row()->batch_id;
		$pinvno = $this->db->query("select a.p_invoice_no  
													from proforma_invoices a
													join shipment_batch_process_invoice_link b on a.p_invoice_no = b.p_invoice_no 
													where b.invoice_no = ? limit 1 ",$invno)->row()->p_invoice_no;
		
		
		foreach($orders as $o)
		{
			
			
			
			$pls=$this->db->query("select qty,pl.product_id,p.mrp,p.brand_id from m_product_deal_link pl join m_product_info p on p.product_id=pl.product_id where itemid=?",$o['itemid'])->result_array();
			//$pls2=$this->db->query("select pl.qty,p.product_id,p.mrp from king_orders o join products_group_orders pgo on pgo.order_id=o.id join m_product_group_deal_link pl join m_product_info p on p.product_id=pgo.product_id where o.id=?",$o['id'])->result_array();
			$pls2=$this->db->query("select pl.qty,p.product_id,p.mrp,p.brand_id 
						from products_group_orders pgo
						join king_orders o on o.id = pgo.order_id 
						join m_product_group_deal_link pl on pl.itemid=o.itemid 
						join m_product_info p on p.product_id=pgo.product_id 
						where pgo.order_id=? and o.status != 0 ",$o['id'])->result_array();
			$pls=array_merge($pls,$pls2);
			
			
			foreach($pls as $p)
			{
				
				/** Default rack bin used if brand is not linked for loc **/ 
				$p['location'] = 1;
				$p['rackbin'] = 10;
				$loc_det_res = $this->db->query("select location_id,rack_bin_id from m_rack_bin_brand_link a join m_rack_bin_info b on a.rack_bin_id = b.id where brandid = ? limit 1 ",$p['brand_id']);
				if($loc_det_res->num_rows())
				{
					$loc_det = $loc_det_res->row_array();	
					$p['location'] = $loc_det['location_id'];
					$p['rackbin'] = $loc_det['rack_bin_id'];
				}
				
				$p_reserv_qty = 0;
				$reserv_stk_res = $this->db->query('select id,release_qty,extra_qty,stock_info_id,qty from t_reserved_batch_stock where batch_id = ? and p_invoice_no = ? and status = 1 and order_id = ? and product_id = ? ',array($batch_id,$pinvno,$o['id'],$p['product_id']));
				if($reserv_stk_res->num_rows())
				{
					foreach($reserv_stk_res->result_array() as $reserv_stk_det)
					{
						$rqty = $reserv_stk_det['qty']-$reserv_stk_det['release_qty']+$reserv_stk_det['extra_qty'];
						$p_reserv_qty += $rqty;
						//$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and stock_id = ? limit 1";
						//$this->db->query($sql,array($rqty,$p['product_id'],$reserv_stk_det['stock_info_id']));
						$this->db->query("update t_reserved_batch_stock set status=3 where id = ? ",array($reserv_stk_det['id']));
					//	echo 1;
						//$this->erpm->do_stock_log(1,$rqty,$p['product_id'],$invoice[0]['id'],false,true,false,-1,0,0,$reserv_stk_det['stock_info_id']);
						
						$stk_info = $this->db->query("select * from t_stock_info where stock_id = ? ",$reserv_stk_det['stock_info_id'])->row_array();
						
						if($stk_info)
						{
							$this->erpm->_upd_product_stock($stk_info['product_id'],$stk_info['mrp'],$stk_info['product_barcode'],$stk_info['location_id'],$stk_info['rack_bin_id'],0,$rqty,3,1,$invoice[0]['id']);	
						}else
						{
							$this->erpm->_upd_product_stock($p['product_id'],$p['mrp'],'',$p['location'],$p['rackbin'],0,$rqty,3,1,$invoice[0]['id']);
						}
						
					}
					 
				}else{
					
					
					$this->erpm->_upd_product_stock($p['product_id'],$p['mrp'],'',$p['location'],$p['rackbin'],0,$p['qty']*$o['qty'],3,1,$invoice[0]['id']);
					
					/*
					$new_stock_entry = true;
					$sp_det_res = $this->db->query("select product_id,mrp from t_stock_info where product_id = ? ",$p['product_id']);
					if($sp_det_res->num_rows())
					{
						foreach($sp_det_res->result_array() as $sp_row)
							if($sp_row['mrp'] == $p['mrp'])
								$new_stock_entry = false;
					}
					if($new_stock_entry)
						 $this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,available_qty,product_barcode,created_on) values(?,?,?,?,?,?,now())",array($p['product_id'],$p['location'],$p['rackbin'],$p['mrp'],0,''));
					
					$stk_ref_id = @$this->db->query("select stock_id from t_stock_info where product_id=? and mrp=? and available_qty >= 0 limit 1 ",array($p['product_id'],$p['mrp']))->row()->stock_id;
					if($stk_ref_id)
					{
						$sql="update t_stock_info set available_qty=available_qty+? where stock_id=? limit 1";
						$this->db->query($sql,array($p['qty']*$o['qty'],$stk_ref_id));
						$this->erpm->do_stock_log(1,$p['qty']*$o['qty'],$p['product_id'],$invoice[0]['id'],false,true,false,-1,0,0,$stk_ref_id);
					}
					 */ 
					
					/*
					$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and mrp=? limit 1";
					$this->db->query($sql,array($p['qty']*$o['qty'],$p['product_id'],$p['mrp']));
					$this->erpm->do_stock_log(1,$p['qty']*$o['qty'],$p['product_id'],$proforma_inv_id,false,true,true,-1,0,0,$stk_ref_id);
					*/
				}
				//$this->erpm->do_stock_log(1,$c_qty,$p['product_id'],$invoice[0]['id'],false,true,false);
				
				$inv_imei_res = $this->db->query('select imei_no from t_imei_no where product_id = ? and order_id = ? and status = 1 ',array($p['product_id'],$o['id']));
				
				if($inv_imei_res->num_rows())
				{
					foreach($inv_imei_res->result_array() as $inv_imei_det)
					{
						
						$imei = $inv_imei_det['imei_no'];
						$this->db->query("update t_imei_no set status=0,order_id=0 where imei_no = ? and order_id=? and status = 1 and product_id = ? ",array($imei,$o['id'],$p['product_id']));
						// update imei log
				 
						// check if imei entry is in log 
						$imei_log_res = $this->db->query('select * from t_imei_update_log where imei_no = ? and is_active = 1 ',array($imei));
						if(!$imei_log_res->num_rows())
						{
							$imei_det = $this->db->query('select * from t_imei_no where imei_no = ? ',array($imei))->row_array();
							$imei_upd_det = array();
							$imei_upd_det['imei_no'] = $imei_det['imei_no'];
							$imei_upd_det['product_id'] = $imei_det['product_id'];
							$imei_upd_det['stock_id'] = $imei_det['stock_id'];
							$imei_upd_det['grn_id'] = $imei_det['grn_id'];
							$imei_upd_det['alloted_order_id'] = $o['id'];
					
							$imei_upd_det['is_active'] = 1;
							$imei_upd_det['logged_by'] = $user['userid'];
							$imei_upd_det['logged_on'] = date('Y-m-d H:i:s',$imei_det['created_on']);
							$this->db->insert('t_imei_update_log',$imei_upd_det);
						}
							
						$imei_upd_det = array();
						$imei_upd_det['is_cancelled'] = 1;
						$imei_upd_det['cancelled_on'] = cur_datetime();
						$imei_upd_det['is_active'] = 0;
						$this->db->where(array('imei_no'=>$imei,'is_active'=>1));
						$this->db->update('t_imei_update_log',$imei_upd_det);
				
						$imei_det = $this->db->query('select * from t_imei_no where imei_no = ?  ',array($imei))->row_array();
						$imei_upd_det = array();
						$imei_upd_det['imei_no'] = $imei_det['imei_no'];
						$imei_upd_det['product_id'] = $imei_det['product_id'];
						$imei_upd_det['stock_id'] = $imei_det['stock_id'];
						$imei_upd_det['grn_id'] = $imei_det['grn_id'];
						$imei_upd_det['is_active'] = 1;
						$imei_upd_det['logged_by'] = $user['userid'];
						$imei_upd_det['logged_on'] = date('Y-m-d H:i:s');
						$this->db->insert('t_imei_update_log',$imei_upd_det);
					}
				}
			}
			
		}
		  
		$this->db->query("update king_orders set status=0 where id in ('".implode("','",$oids)."') and transid=?",$transid);
		$this->db->query("update king_invoice set invoice_status=0 where invoice_no=?",$invno);
		
		
		$inv_trans_det = $this->db->query("select franchise_id,a.transid,b.invoice_no,is_pnh,sum((mrp-discount)*invoice_qty) as inv_amt
													from king_transactions a 
													join king_invoice b on a.transid = b.transid 
													join king_orders c on c.id = b.order_id 
													where b.invoice_no = ? and b.invoice_status = 0 ",$invno)->row_array();
		
		if($inv_trans_det['is_pnh'])
		{
			$arr = array($inv_trans_det['franchise_id'],1,$inv_trans_det['invoice_no'],$inv_trans_det['inv_amt'],'',1,date('Y-m-d H:i:s'),$user['userid']);
			$this->db->query("insert into pnh_franchise_account_summary (franchise_id,action_type,invoice_no,credit_amt,remarks,status,created_on,created_by) values(?,?,?,?,?,?,?,?)",$arr);
			
			// cancel credit note raised against invoice.
			$credit_note_id = $this->db->query("select credit_note_id from king_invoice where invoice_no = ? ",$invno)->row()->credit_note_id;
			if($credit_note_id)
			{
				//cancel credit note 
				$this->db->query("update t_invoice_credit_notes set is_active = 0,modified_on=now() where id = ? ",$credit_note_id);
				$arr = array($inv_trans_det['franchise_id'],7,$inv_trans_det['invoice_no'],$this->db->query('select amount from t_invoice_credit_notes where id = ? ',$credit_note_id)->row()->amount,'credit note cancelled',1,date('Y-m-d H:i:s'),$user['userid']);
				$this->db->query("insert into pnh_franchise_account_summary (franchise_id,action_type,invoice_no,debit_amt,remarks,status,created_on,created_by) values(?,?,?,?,?,?,?,?)",$arr);
			}
		}
		
		$p_inv_no = $this->db->query("select p_invoice_no as i from shipment_batch_process_invoice_link where invoice_no=?",$invno)->row()->i;
		
		foreach($this->db->query("select order_id,transid from king_invoice where invoice_no=? ",$invno)->result_array() as $m_inv)
		{
			$this->db->query("update proforma_invoices set invoice_status=0 where p_invoice_no=? and order_id = ? and transid = ? ",array($p_inv_no,$m_inv['order_id'],$m_inv['transid']));	
		}
		
		$this->erpm->do_trans_changelog($transid,"Invoice no $invno cancelled");
		$this->session->set_flashdata("erp_pop_info","Invoice cancelled");
		$bid=$this->db->query("select batch_id from shipment_batch_process_invoice_link where invoice_no=?",$invno)->row()->batch_id;
		$c=$this->db->query("select count(1) as l from shipment_batch_process_invoice_link where packed=1 and batch_id=$bid")->row()->l;
		if($this->db->query("select p_invoice_no as n from shipment_batch_process_invoice_link where invoice_no=?",$invno)->row()->n!=0)
			$c+=$this->db->query("select count(1) as l from shipment_batch_process_invoice_link bi join proforma_invoices i on i.p_invoice_no=bi.p_invoice_no where bi.batch_id=$bid and bi.packed=0 and i.invoice_status=0")->row()->l;
		if($this->db->query("select count(1) as l from shipment_batch_process_invoice_link where batch_id=?",$bid)->row()->l<=$c)
//		if($this->db->query("select count(1) as l from shipment_batch_process_invoice_link bi join proforma_invoices i on i.p_invoice_no=bi.p_invoice_no where bi.batch_id=? and bi.packed=0 and i.invoice_status=1",$bid)->row()->l==0)
			$this->db->query("update shipment_batch_process set status=2 where batch_id=? limit 1",$bid);
		redirect("admin/trans/$transid");
	}

	function mark_c_refund($rid="",$from_pending=false)
	{
		$user=$this->auth(true);
		
		$transid = $this->db->query("select transid from t_refund_info where refund_id=?",$rid)->row()->transid;
		
		// check if valid to process refund 
		$process_refund = $this->erpm->_valid_transforrefund($transid);
		if(!$process_refund)
			show_error('Refund cannot be processed for Partner/Storeking Transactions');
		
		$this->db->query("update t_refund_info set status=1,modified_on=?,modified_by=? where refund_id=?",array(time(),$user['userid'],$rid));
		$r=$this->db->query("select r.invoice_no,r.amount,t.is_pnh,r.transid,t.franchise_id,r.refund_for from t_refund_info r join king_transactions t on t.transid=r.transid where r.refund_id=?",$rid)->row_array();
		if($r['is_pnh']!=0)
		{
			$sms_text = '';
			if($r['refund_for'] == 'mrpdiff')
			{
				$refund_for = 'MRP change';
				$sms_text = "Rs {$r['amount']} refunded towards MRP Change in Invoice No:{$r['invoice_no']}";
			}
			else
			{
				$refund_for = 'Cancelled';
				$sms_text = "Rs {$r['amount']} refunded towards Order Cancellation in Order :{$r['transid']}";
				$this->erpm->do_trans_changelog($r['transid'],"Amount of Rs {$r['amount']} credited back to franchise");
			}
			
			$this->erpm->pnh_fran_account_stat($r['franchise_id'],0,$r['amount'],"Refund - Order {$r['transid']} ".$refund_for,"refund",$r['transid']);
			
			/* Commented explicity for not sending SMS for franchise 
			if($r['amount'] > 0)
				$this->erpm->pnh_sendsms($this->db->query("select login_mobile1 as m from pnh_m_franchise_info where franchise_id=?",$r['franchise_id'])->row()->m,$sms_text,$r['franchise_id']);
			 */
			  
		}
		if(!$from_pending)
			redirect("admin/trans/".$transid);
		redirect("admin/pending_refunds_list");
	}
	
	function change_qy_order($transid)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		$refund=$this->input->post("nc_refund");
		$oid=$this->input->post("nc_oid");
		$qty=$this->input->post("nc_qty");
		$n_oid=random_string("numeric",10);
		$is_pnh=$this->db->query("select * from king_transactions where transid=?",$transid)->row()->is_pnh;
		$order=$this->db->query("select * from king_orders where id=?",$oid)->row_array();
		
		$prod=$this->db->query("select name from king_dealitems where id=?",$order['itemid'])->row_array();
		$n_qty=$order['quantity']-$qty;
		$inp=array($n_oid,$transid,$order['userid'],$order['itemid'],$order['vendorid'],$order['brandid'],$n_qty,3,$order['i_orgprice'],$order['i_price'],$order['i_nlc'],$order['i_phc'],$order['i_tax'],$order['i_discount'],$order['i_coup_discount'],$order['i_discount_applied_on'],$order['time'],time());
		
		$this->db->query("insert into king_orders(id,transid,userid,itemid,vendorid,brandid,quantity,status,i_orgprice,i_price,i_nlc,i_phc,i_tax,i_discount,i_coup_discount,i_discount_applied_on,time,actiontime) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",$inp);
		$this->db->query("update king_orders set quantity=? where id=? limit 1",array($qty,$oid));
		$this->erpm->do_trans_changelog($transid,"Order '{$prod['name']}' quantity changed from {$order['quantity']} to {$qty}");
		
		// check if transactions is ready to process refund 
		if($this->erpm->_valid_transforrefund($transid))
		{
			$this->db->query("insert into t_refund_info(transid,amount,status,created_on,created_by) values(?,?,?,?,?)",array($transid,$refund,0,time(),$user['userid']));
			$rid=$this->db->insert_id();
			$this->db->query("insert into t_refund_order_item_link(refund_id,order_id,qty) values(?,?,?)",array($rid,$n_oid,$n_qty));
		}
		
		redirect("admin/trans/{$transid}");
	}
	
	function endisable_for_batch($transid)
	{
		$user=$this->auth(true);
		$flag=1;
		if($this->db->query("select batch_enabled from king_transactions where transid=?",$transid)->row()->batch_enabled==1)
			$flag=0;
		$this->db->query("update king_transactions set batch_enabled=$flag where transid=? limit 1",$transid);
		$this->erpm->do_trans_changelog($transid,"Order ".($flag?"ENABLED":"DISABLED")." for batch process");
		redirect("admin/trans/$transid");
	}
	
	function bulk_endisable_for_batch()
	{
		$this->auth(ORDER_BATCH_PROCESS_ROLE);
		foreach(array("enable","disable","trans") as $i)
			$$i=$this->input->post("$i");
		if(empty($trans))
			show_error("No Orders selected");
		$flag=1;
		if($disable)
			$flag=0;
		$filter=$this->db->query("select transid,batch_enabled from king_transactions where transid in ('".implode("','",$trans)."') and batch_enabled!=$flag")->result_array();
		$trans=array();
		foreach($filter as $f)
			$trans[]=$f['transid'];
		foreach($trans as $transid)
		{
			$this->db->query("update king_transactions set batch_enabled=$flag where transid=? limit 1",$transid);
			$this->erpm->do_trans_changelog($transid,"Order ".($flag?"ENABLED":"DISABLED")." for batch process");
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function resend_mails($transid)
	{
		if($this->input->post("order"))
			$this->resend_order_confirmation_mail($transid);
		else
			$this->resend_shipment_mail($transid);
	}
	
	private function resend_shipment_mail($transid)
	{
		$user=$this->auth();
		$email=$this->input->post("email");
		$mdata['prods']=$os=$this->db->query("select inv.transid,i.name,o.quantity,o.medium,o.shipid,o.id from king_invoice inv join king_orders o on o.id=inv.order_id join king_dealitems i on i.id=o.itemid where inv.transid=? order by i.name asc",$transid)->result_array();
		if(empty($os))
			show_error("No shipments were made for this order");
		$mdata['transid']=$transid=$os[0]['transid'];
		$mdata['medium']=$os[0]['medium'];
		$mdata['trackingid']=$os[0]['shipid'];
		$oid=$os[0]['id'];
		$partial=false;
		if($this->db->query("select count(1) as l from king_orders where status!=2 and transid=?",$transid)->row()->l!=0)
			$partial=true;
		$mdata['partial']=$partial;
		$payload=$this->db->query("select u.name,o.bill_email,o.ship_email from king_orders o join king_users u on u.userid=o.userid where o.id=?",$oid)->row_array();
		$mdata['name']=$payload['name'];
		$msg=$this->load->view("mails/shipment",$mdata,true);
		$this->vkm->email(array($email,$payload['bill_email'],$payload['ship_email']),"Your order is shipped",$msg,true);
		$this->session->set_flashdata("erp_pop_info","Shipment mail resent successfully");
		redirect("admin/trans/$transid");
	}
	
	private function resend_order_confirmation_mail($transid)
	{
		$user=$this->auth();
		$email=$this->input->post("email");
		
		$orders=$this->db->query("select * from king_orders where transid=?",$transid)->result_array();

		$umail='<table width="100%" border=1 cellspacing=0 cellpadding=7 style="font-size:inherit;margin-top:15px;"><tr><th>Product</th><th>Unit Price</th><th>Quantity</th><th>Price</th></tr>';
		$mrp_total=0;
		$m_items=array();
		$total_discount = 0;
		foreach($orders as $order)
		{
			$m_item=$this->db->query("select orgprice,price,name from king_dealitems where id=?",$order['itemid'])->row_array();
			$itemname=$m_item['name'];
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
		$umail.="<tr><td colspan=3 align='right'>Discount</td><td>".floor($total_discount)."</td></tr>";
		if($giftwrap_order)
			$umail.="<tr><td colspan=3 align='right'>GiftWrap Charges</td><td>".GIFTWRAP_CHARGES."</td></tr>";
		$umail.="<tr><td colspan=3 align='right'>Handling/Shipping/COD charges</td><td>".($m_trans['cod']+$m_trans['ship'])."</td></tr>";
		$umail.="<tr><td colspan=3 align='right'>Total</td><td>Rs {$t_amount}</td></tr>";
		$umail.="</table>";
		
		$msg=$this->load->view("mails/order",array("transid"=>$transid,"umail"=>$umail),true);
		$this->vkm->email(array($order['ship_email'],$order['bill_email'],$email),"Your order details : $transid",$msg);
		$this->session->set_flashdata("erp_pop_info","Order confirmation mail resent successfully");
		redirect("admin/trans/$transid");
	}
	
	function reship_order()
	{
		$user=$this->auth(true);
		if(empty($_POST))
			show_error("No milk for cookie");
		$this->erpm->do_reship_order();
	}
	
	function trans($transid="")
	{
		if(empty($transid))
			show_404();
		$user=$this->auth(CALLCENTER_ROLE|FINANCE_ROLE|PNH_EXECUTIVE_ROLE|PARTNER_ORDERS_ROLE);
		if($_POST)
			$this->erpm->do_addtransmsg($transid);
		$data['tran']=$tran=$this->erpm->gettransaction($transid);
		if(empty($tran))
			show_error("Transaction not found");
		$data['orders']=$this->erpm->getordersfortransid($transid);
		if(empty($data['orders']))
			redirect("admin/callcenter/trans/{$transid}");
		$data['batch']=$this->erpm->getbatchesstatusfortransid($transid);
		$data['tickets']=$this->erpm->getticketsfortrans($transid);
		$data['changelog']=$this->erpm->gettranschangelog($transid);
		$data['freesamples']=$this->erpm->getfreesamplesfortransaction($transid);
		$data['refunds']=$this->db->query("select * from t_refund_info where transid=?",$transid)->result_array();
		$trans_fid=$this->db->query("select franchise_id from king_transactions where transid=? limit 1",$transid)->row()->franchise_id;
		$is_prepaid=$this->erpm->is_prepaid_franchise($trans_fid);
		$data['is_prepaid']=$is_prepaid;
		$data['page']="transaction_orders";
		$this->load->view("admin",$data);
	}
	
	function pending_refunds_list()
	{
		$user=$this->auth(true);
		$data['refunds']=$this->db->query("select * from t_refund_info where status=0 order by refund_id asc")->result_array();
		$data['page']="pending_refunds_list";
		$this->load->view("admin",$data);
	}
	
	function getvendorproducts()
	{
		$vid=$this->input->post("v");
		$q=$this->input->post("q");
		$res=$this->erpm->searchvendorproducts($vid,$q);
		foreach($res as $r)
			echo "<a href='javascript:void(0)' onclick='addproduct(\"{$r['product_id']}\",\"".htmlspecialchars($r['product_name'],ENT_QUOTES)."\",\"{$r['mrp']}\",\"{$r['margin']}\",\"{$r['orders']}\",\"{$r['orders']}\")'>{$r['product_name']} <span style='color:red'>(stock: {$r['stock']})</span> <span style='color:#ff9900;'>(Mrp:Rs {$r['mrp']})</span></a>";
	}
	
	function pnh_investor_report($fid=0)
	{
		$user=$this->auth(INVESTOR_ROLE);
		$data['report']=$this->erpm->getpnhreport($fid);
		$data['page']="pnh_investor_report";
		$this->load->view("admin",$data);
	}
	
	function investor_report()
	{
		$user=$this->auth(INVESTOR_ROLE);
		$data['report']=$this->erpm->getinvestorreport();
		$data['page']="investor_report";
		$this->load->view("admin",$data);
	}
	
	function changepassword()
	{
		$user=$this->auth();
		if($_POST)
			$this->erpm->do_changepwd();
		$data['page']="changepwd";
		$this->load->view("admin",$data);
	}
	
	function searchproducts()
	{
		$user=$this->auth();
		$q=$this->db->query("select p.*,sum(s.available_qty) as stock from m_product_info p left outer join t_stock_info s on s.product_id=p.product_id where p.product_name like ? order by p.product_name group by p.product_id","%{$_POST['q']}%")->result_array();
		foreach($q as $r)
			echo "<a href='javascript:void(0)' onclick='addproduct(\"{$r['product_id']}\",\"".htmlspecialchars($r['product_name'],ENT_QUOTES)."\",\"{$r['mrp']}\",\"{$r['vat']}\")'>{$r['product_name']}  <span style='color:red'>(stock:{$r['stock']})</span>  <span style='color:#ff9900;'>(Mrp: Rs{$r['mrp']})</span></a>";
	}
	
	function add_batch_process()
	{
		$user=$this->auth(ORDER_BATCH_PROCESS_ROLE);
		if(!$_POST)
			redirect("batch_process");
		$bid=$this->erpm->do_shipment_batch_process();
		if(!$bid)
			show_error("INSUFFICIENT STOCK TO PROCESS ANY ORDER");
		redirect("admin/batch/$bid");
	}
	
	function add_courier()
	{
		$user=$this->auth(OUTSCAN_ROLE|INVOICE_PRINT_ROLE);
		if($_POST)
			$this->erpm->do_addcourier();
		$data['page']="add_courier";
		$this->load->view("admin",$data);
	}
	
	function edit_pincodes($cid=false)
	{
		$user=$this->auth(OUTSCAN_ROLE|INVOICE_PRINT_ROLE);
		if(!$cid)
			show_404();
		if($_POST)
		{
			$this->erpm->do_updatepincodesforcourier($_POST['courier_id'],$_POST['pincodes']);
			redirect("admin/courier");
		}
		$data['courier_id']=$cid;	
		$data['pincodes']=$this->erpm->getpincodesforcourier($cid);
		$data['page']="edit_pincodes";
		$this->load->view("admin",$data);
	}
	
	function courier()
	{
		$user=$this->auth(OUTSCAN_ROLE|INVOICE_PRINT_ROLE|ORDER_BATCH_PROCESS_ROLE);
		$data['couriers']=$this->erpm->getcouriers();
		$data['page']="couriers";
		$this->load->view("admin",$data);
	}
	
	function update_awb($courier_id)
	{
		$user=$this->auth(OUTSCAN_ROLE|INVOICE_PRINT_ROLE);
		if($_POST)
		{
			$this->erpm->do_updateawb($courier_id);
			redirect("admin/courier");
		}
		$data['cid']=$courier_id;
		$data['awb']=$this->db->query("select * from m_courier_awb_series where courier_id=?",$courier_id)->row_array();
		$data['page']="update_awb";
		$this->load->view("admin",$data);
	}
	
	function update_ship_kfile()
	{
		$user=$this->auth(KFILE_ROLE);
		if($_POST)
			$this->erpm->do_update_ship_kfile();
		$data['page']="update_kfile";
		$this->load->view("admin",$data);
	}
	
	function edit_partner($pid)
	{
		$this->add_partner($pid);
	}
	
	function add_partner($pid=false)
	{
		$user=$this->auth(true);
		if($pid)
			$data['partner']=$this->db->query("select * from partner_info where id=?",$pid)->row_array();
		if($_POST)
		{
//			$this->erpm->debug_post();
			foreach(array("name","trans_prefix","trans_mode") as $i)
				$inp[$i]=$this->input->post($i);
			if(strlen($inp['trans_prefix'])!=3)
				show_error("Transaction prefix should be 3 characters to be exact");
			$inp['trans_prefix']=strtoupper($inp['trans_prefix']);
			if(!$this->input->post("pid"))
			{
				$inp['created_by']=$user['userid'];
				$this->db->insert("partner_info",$inp);
				$this->erpm->flash_msg("New partner added");
			}else{
				$inp['modified_by']=$user['userid'];
				$this->db->update("partner_info",$inp,array("id"=>$this->input->post("pid")));
				$this->erpm->flash_msg("Partner details updated");
			}
			redirect("admin/partners");
		}
		$data['page']="add_partner";
		$this->load->view("admin",$data);
	}
	
	function partners()
	{
		$user=$this->auth(true);
		$data['partners']=$this->db->query("select * from partner_info order by name asc")->result_array();
		$data['page']="partners";
		$this->load->view("admin",$data);
	}
	
	function view_partner_orders($log_id)
	{
		$user=$this->auth(PARTNER_ORDERS_ROLE);
		$data['orders']=$this->db->query("select l.*,o.status from partner_order_items l join king_orders o on o.transid=l.transid where l.log_id=? group by l.transid",$log_id)->result_array();
		$data['log_id']=$log_id;
		$data['page']="partner_order_items";
		$this->load->view("admin",$data);
	}
	
	function warehouse_summary($type=0,$id=0)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|DEAL_MANAGER_ROLE);
		if($type==1)
		{
			$data['products']=$this->db->query("select p.product_id,p.product_name,sum(s.available_qty) as stock,sum(s.available_qty*s.mrp) as stock_value,p.mrp from m_product_info p join t_stock_info s on s.product_id=p.product_id where p.brand_id=$id group by p.product_id having sum(s.available_qty)!=0 ")->result_array();
			$data['pagetitle']="Products in Stock for brand: ".$this->db->query("select name from king_brands where id=?",$id)->row()->name;
		}
		$data['page']="warehouse_summary";
		$this->load->view("admin",$data);
	}
	
	function partner_orders()
	{
		$user=$this->auth(PARTNER_ORDERS_ROLE);
		$data['logs']=$this->erpm->getpartnerorderlogs();
		$data['page']="partner_orders";
		$this->load->view("admin",$data);
	}
	
	function partner_order_import()
	{
		$user=$this->auth(PARTNER_ORDERS_ROLE);
		if($_POST)
			$this->erpm->do_partner_order_import();
		$data['page']="partner_order_import";
		$this->load->view("admin",$data);
	}
	
	function generate_kfile()
	{
		$user=$this->auth(KFILE_ROLE);
		if($_POST)
		{
			$invs=explode(",",$this->input->post("ids"));
			if(empty($invs))
				show_error("No Invoices provided");
					
			$this->erpm->do_generate_kfile($invs);
		}
			
			
		$data['outscans']=$this->erpm->getoutscansforkfile();
		$data['page']="generate_kfile";
		$this->load->view("admin",$data);
	}
	
	function generate_kfile_byrange()
	{
		$user=$this->auth(KFILE_ROLE);
		if($_POST)
		{
			$this->erpm->do_generate_kfilebyrange($this->input->post('from'),$this->input->post('to'));
		}
			
	}
	
	
	function generate_manifesto($pg=0,$pg2=0)
	{
		$data['pg']=$pg;
		$user=$this->auth(KFILE_ROLE);
		if($_POST)
		{
			$invs=explode(",",$this->input->post("ids"));
			if(empty($invs))
				show_error("No Invoices provided");
					
			$this->erpm->do_generate_manifesto($invs);
		}
		
		$data['from'] = date('Y-m-d');
		$data['to'] = date('Y-m-d');
		$data['sel_terr_ids'] = array();
		 
		$data['manifesto_hist_pagi'] = '';
		$data['manifesto_hist_ttl'] = $this->db->query("select count(*) as ttl from pnh_manifesto_log ")->row()->ttl;
		//$data['manifesto_hist_res'] = $this->db->query("select * from pnh_manifesto_log order by created_on desc limit ".$pg.",10");
		$data['manifesto_hist_res'] = $this->db->query("select a.id,a.name,a.st_date,a.en_date,a.invoice_nos,a.total_prints,a.created_on,a.created_by,a.modified_on,a.modified_by,GROUP_CONCAT(b.sent_invoices) as sent_invoices
																from pnh_manifesto_log as a
																left join pnh_m_manifesto_sent_log as b on a.id=b.manifesto_id
																group by a.id
																order by a.created_on desc limit ".$pg.",10");
		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/generate_manifesto');
		$config['total_rows'] = $data['manifesto_hist_ttl'];
		$config['uri_segment'] = 3;
		$config['per_page'] = 10; 
		
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$data['manifesto_hist_pagi'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		
		$data['total_shipments']=100;
		$data['page']="generate_manifesto";
		$this->load->view("admin",$data);
		
	}
	
	function pnh_pending_shipments()
	{
		$user = $this->auth(PNH_SHIPMENT_MANAGER);
		
		$data['total_shipments']=100;
		$data['page']="pnh_pending_shipments";
		$this->load->view("admin",$data);
	}
	
	/*function generate_pending_invoices_grp()
	{
		$user = $this->auth();
		$output=array();
		
		$invoices_list=$this->input->post('invoices');
		$invoices_list=array_filter($invoices_list);
		
		$ins_data = array();
		$ins_data['name'] = 'Manifesto for '.date('d/m/Y m:H:i');
		$ins_data['total_prints'] = 0;
		$ins_data['st_date'] = 0;
		$ins_data['en_date'] = 0;
		$ins_data['invoice_nos'] = implode(',',$invoices_list);
		$ins_data['created_on'] = date('Y-m-d H:i:s');
		$ins_data['created_by'] = $user['userid'];
		$this->db->insert("pnh_manifesto_log",$ins_data);
		$logid = $this->db->insert_id();
		
		$invoices=implode(',',$invoices_list);
		
		$this->db->query("update shipment_batch_process_invoice_link set inv_manifesto_id=? where invoice_no in ($invoices)",array($logid));
		
		$output['manifest_id']=$logid;
		//$this->session->set_flashdata("erp_pop_info",count(explode(',',$invoices))." Invoices select for shipment");
		echo json_encode($output);
		
	}*/
	
	function jx_peninvoicesfordelivery($pg=0)
	{
		$user = $this->auth();
		
		//generate manifesto new mpodule
		$param=$this->input->post('param');
		$parameters=array();
		
		$m_st_date=date('Y-m-d',time()-365*24*60*60);
		$till_date=date('Y-m-d');
		
		/*$sql1="select date(a.shipped_on) as outscan_date,territory_name,town_name,e.id as terr_id,f.id as town_id,group_concat(distinct concat(franchise_name,':',a.invoice_no) order by franchise_name,a.invoice_no) as outscan_invoices,count(distinct a.invoice_no) as ttl_invs
						from shipment_batch_process_invoice_link a
						join king_invoice b on b.invoice_no = a.invoice_no and b.invoice_status = 1
						join king_transactions c on c.transid = b.transid
						join pnh_m_franchise_info d on d.franchise_id = c.franchise_id
						join pnh_m_territory_info e on e.id = d.territory_id
						join pnh_towns f on f.id = d.town_id
						where packed = 1 and shipped = 1  and inv_manifesto_id = 0 
				";*/
		
		$sql1="select i.hub_name as territory_name,town_name,date(a.packed_on) as outscan_date,i.id as terr_id,f.id as town_id,h.tray_name,h.tray_id,
					group_concat(distinct concat(franchise_name,':',a.invoice_no) order by franchise_name,a.invoice_no) as outscan_invoices,count(distinct a.invoice_no) as ttl_invs
						from shipment_batch_process_invoice_link a
						join king_invoice b on b.invoice_no = a.invoice_no and b.invoice_status = 1
						join king_transactions c on c.transid = b.transid
						join pnh_m_franchise_info d on d.franchise_id = c.franchise_id
						
						join pnh_deliveryhub_town_link e on e.town_id = d.town_id and e.is_active = 1 
						join pnh_deliveryhub i on i.id = e.hub_id and i.is_active = 1 
						
						join m_tray_info h on h.tray_id=a.tray_id
						join pnh_towns f on f.id = d.town_id
						where packed = 1 and shipped = 0  and inv_manifesto_id = 0 
			";
		//$sql1.=" and date(a.shipped_on) between ? and ? ";
		$sql1.=" and date(a.packed_on) between ? and ? ";
		if(isset($param['st_date']))
		{
			$parameters[]=$param['st_date'];
			$parameters[]=$param['en_date'];
		}else
		{
			$parameters[]=$m_st_date;
			$parameters[]=$till_date;
			//$sql1.=" and date(a.shipped_on) between '2012-02-01' and '2013-02-01' ";
		}
		
		if(isset($param['terri_id']))
		{
			$sql1.=" and i.id = ? ";
			$parameters[] = $param['terri_id'];
		}
		
		if(isset($param['town_id']))
		{
			$sql1.=" and f.id = ? ";
			$parameters[] = $param['town_id'];
		}
		
		/*$sql1.=	" group by outscan_date,d.territory_id,d.town_id
			order by outscan_date,e.territory_name,f.town_name";*/
		$sql1.=	" group by i.id,d.town_id
			order by territory_name,f.town_name";
		
		$sql=$sql1." limit $pg ,1000;";
		
		$total_delivery_invoices=$this->db->query($sql1,$parameters)->result_array();
		
		//echo $this->db->last_query();
		
		$data['pending_delivery_details'] =$this->db->query($sql,$parameters)->result_array();
		//echo $this->db->last_query();
		$ttl_delivery_inv=count($total_delivery_invoices);
		
		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/jx_peninvoicesfordelivery');
		$config['total_rows'] = $ttl_delivery_inv;
		$config['uri_segment'] = 3;
		$config['per_page'] = 1000;
		
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$data['pending_inv_pi'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		
		$terr_det=array();
		$towns_det=array();
		$tray_list=array();
		foreach($total_delivery_invoices as $fillter_det)
		{
			if(!isset($terr_det[$fillter_det['terr_id']]))
				$terr_det[$fillter_det['terr_id']]=$fillter_det['territory_name'];
			if(!isset($towns_det[$fillter_det['town_id']]))
				$towns_det[$fillter_det['town_id']]=$fillter_det['town_name'];
			if(!isset($tray_list[$fillter_det['tray_id']]))
				$tray_list[$fillter_det['tray_id']]=$fillter_det['tray_name'];
		}
		//generate manifesto new mpodule end
		
		//get the total invoices det
		$total_inv=array();
		if($data['pending_delivery_details'])
		{
			foreach($data['pending_delivery_details'] as $inv_details)
			{
				$si_invoices=explode(',',$inv_details['outscan_invoices']);
				foreach($si_invoices as $invoice)
					$total_inv[]=$invoice;
			}
			$ttl_delivery_inv=count($total_inv);
		}else{
			$ttl_delivery_inv=0;
		}
		
		
		$output = array(); 
		$output['pen_invfordelivery_list'] = $this->load->view("admin/body/jx_pen_invoices_for_delivery",$data,true);
		$output['total_pending'] = $ttl_delivery_inv;
		$output['terr_list'] = $terr_det;
		$output['town_list'] = $towns_det;
		$output['tray_list'] = $tray_list;
		
		echo json_encode($output);
		
	}
	
	function generate_manifesto_byrange()
	{
		$data['from'] = $this->input->post('from');
		$data['to'] = $this->input->post('to');
		$data['sel_terr_ids'] = $this->input->post('sel_terr_ids');
		
		$cond =  '';
		if($data['sel_terr_ids'])
			$cond = ' and e.territory_id in ('.implode(',',$data['sel_terr_ids']).')';
		
		$data['outscan_res'] = $this->db->query("select a.id,e.franchise_id,a.invoice_no,b.id,c.id,franchise_name,town_name,territory_name,postcode,login_mobile1,e.city 
													from shipment_batch_process_invoice_link a 
												join king_invoice b on b.invoice_no = a.invoice_no 
												join king_orders c on c.id = b.order_id 
												join king_transactions d on d.transid = c.transid 
												join pnh_m_franchise_info e on e.franchise_id = d.franchise_id
												join pnh_m_territory_info t on t.id = e.territory_id
												join pnh_towns t1 on t1.id = e.town_id 
												where a.shipped=1 and is_pnh = 1 and date(shipped_on)>=? and date(shipped_on)<=?
											 	$cond   
												group by a.invoice_no 
												order by territory_name,town_name,franchise_name  ",array($data['from'],$data['to']));
												
										
		$data['outscans']=$this->erpm->getoutscansforkfile();
		$data['page']="generate_manifesto";
		$this->load->view("admin",$data);
	}
	
	function _gen_manifestoprint_byterr($logid=0,$sent_manifestoid=0)
	{
		$this->load->plugin('suggest_group_list');
		$this->load->plugin('barcode');
		
		$sent_invoices='';
		if($sent_manifestoid)
		{
			$sent_invoices=$this->db->query('select a.sent_invoices,b.name,a.hndleby_name,b.contact_no,a.hndleby_contactno,a.hndleby_vehicle_num from pnh_m_manifesto_sent_log a
														left join m_employee_info b on b.employee_id = a.hndleby_empid
														where a.id=?',$sent_manifestoid)->result_array();
			
			$invoices=$sent_invoices[0]['sent_invoices'];
			$hndbyname=$sent_invoices[0]['name'];
			$hndbycontactno=$sent_invoices[0]['contact_no'];
			$vehicle_num=$sent_invoices[0]['hndleby_vehicle_num'];
			/*if(!$hndbyname)
				$hndbyname=$sent_invoices[0]['hndleby_name'];
			
			if(!$hndbycontactno)
				$hndbycontactno=$sent_invoices[0]['hndleby_contactno'];*/
			if(!$hndbyname)
				$hndbyname='________________________';
			if(!$hndbycontactno)
				$hndbycontactno='';
			if(!$vehicle_num)
				$vehicle_num='________________________';
			
			//$this->db->query("update pnh_invoice_transit_log set status = 1,logged_on=now() where sent_log_id = ? and status = 0 ",$sent_manifestoid);
			
			if($this->db->affected_rows())
			{
				// send sms to tm and exec fc for towns
				$sql="select a.invoice_no,d.territory_id,territory_name,d.franchise_name,c.transid,t.town_name,emp.job_title,emp.name,emp.job_title2,tt.town_id,emp.contact_no,emp.employee_id
										from king_invoice a
										join king_transactions c on c.transid = a.transid 
										join pnh_m_franchise_info d on d.franchise_id = c.franchise_id 
										join m_town_territory_link tt on tt.territory_id = d.territory_id
										join m_employee_info emp on emp.employee_id = tt.employee_id and emp.job_title in (4,5) and emp.job_title2 = 0 
										join pnh_m_territory_info e on e.id = d.territory_id
										join pnh_towns t on t.id=d.town_id
										join king_orders o on o.id = a.order_id
										join king_dealitems di on di.id = o.itemid
										join king_deals dl on dl.dealid = di.dealid and dl.menuid != 112 
										where a.invoice_no in ($invoices) and c.is_pnh = 1 and tt.is_active=1
										group by d.town_id 
										order by territory_name; ";
				
				$employees_list=$this->db->query($sql)->result_array();
				
				if ($employees_list)
				{
					foreach($employees_list as $emp)
					{	
						$emp_name=$emp['name'];
						$emp_id=$emp['employee_id'];
						$town_name=$emp['town_name'];
						$emp_contact_nos = explode(',',$emp['contact_no']);
						$sms_msg = 'Dear '.$emp_name.',Shipment for the town '.$town_name.' sent via '.ucwords($hndbyname).'('.$hndbycontactno.') vehicle no ['.$vehicle_num.'].';
						foreach($emp_contact_nos as $emp_mob_no)
						{
							/*$this->erpm->pnh_sendsms($emp_mob_no,$sms_msg);
						//	echo $emp_mob_no,$sms_msg;
							$this->db->query('insert into pnh_employee_grpsms_log(emp_id,contact_no,type,grp_msg,created_on)values(?,?,?,?,now())',array($emp_id,$emp_mob_no,4,$sms_msg,date('Y-m-d H:i:s')));
							*/
						}
					}
				}
			}
		}
		
		$manifesto_log_det = $this->db->query("select st_date,en_date,invoice_nos from pnh_manifesto_log where id = ? ",$logid)->row_array();
		$data['from'] = $manifesto_log_det['st_date'];
		$data['to'] = $manifesto_log_det['en_date'];
		$invnos = $manifesto_log_det['invoice_nos'];
		if($sent_manifestoid)
			$invnos=$sent_invoices[0]['sent_invoices'];
			
		
		echo '<html>
					<head>
						<title>Manifesto Print</title>
						<style>
							body{
								font-family:arial;
								font-size:14px;
								
							}
						</style>
					</head>
					<body>';
		$outscan_terr_res = $this->db->query("select distinct e.territory_id,t.territory_name 
														from shipment_batch_process_invoice_link a 
													join king_invoice b on b.invoice_no = a.invoice_no 
													join king_orders c on c.id = b.order_id 
													join king_transactions d on d.transid = c.transid 
													join pnh_m_franchise_info e on e.franchise_id = d.franchise_id
													join pnh_m_territory_info t on t.id = e.territory_id
													join pnh_towns t1 on t1.id = e.town_id
													join king_dealitems di on di.id = c.itemid
													join king_deals dl on dl.dealid = di.dealid and dl.menuid != 112 
													where (a.shipped=1 or a.outscanned=1) and d.is_pnh = 1  
													and a.invoice_no in ($invnos)
													order by t.territory_name ");
		
		$ttl_terr_listed = $outscan_terr_res->num_rows();		

		 
		foreach($outscan_terr_res->result_array() as $t_indx=>$terr_det)
		{											
			$outscan_res = $this->db->query("select a.id,e.franchise_id,a.invoice_no,b.id,c.id,franchise_name,
																town_name,postcode,login_mobile1,e.city 
															from shipment_batch_process_invoice_link a 
														join king_invoice b on b.invoice_no = a.invoice_no 
														join king_orders c on c.id = b.order_id 
														join king_transactions d on d.transid = c.transid 
														join pnh_m_franchise_info e on e.franchise_id = d.franchise_id
														join pnh_m_territory_info t on t.id = e.territory_id
														join pnh_towns t1 on t1.id = e.town_id
														join king_dealitems di on di.id = c.itemid
														join king_deals dl on dl.dealid = di.dealid and dl.menuid != 112 
														where (a.shipped=1 or a.outscanned=1) and d.is_pnh = 1  
														and a.invoice_no in ($invnos) and e.territory_id = ?  
														group by a.invoice_no 
														order by e.franchise_id ",array($terr_det['territory_id']));
			$manifesto_listbypages = suggest_grp_pages($outscan_res->result_array(),4,20,'franchise_id');
			
			
			$terr_det['hub_name'] = @$this->db->query("select distinct d.hub_name from shipment_batch_process_invoice_link a 
	join pnh_t_tray_invoice_link b on a.invoice_no = b.invoice_no 
	join pnh_t_tray_territory_link c on b.tray_terr_id = c.tray_terr_id
	join pnh_deliveryhub d on d.id = c.territory_id 
	join pnh_m_manifesto_sent_log e on e.manifesto_id = a.inv_manifesto_id
	where e.id = ?
			",$sent_manifestoid)->row()->hub_name;
			
			echo '<div '.(($ttl_terr_listed!=$t_indx+1)?'style="page-break-after:always;"':'').'>
						<div>
							<h3>Storeking - Delivery Log Sheet  <span style="float:right">Date :___________________</span></h3> 
							<h3>Manifesto No : '.$sent_manifestoid.' &nbsp;&nbsp;&nbsp;&nbsp; Hub : '.ucwords($terr_det['hub_name']).'
							<h3> <span style="float:right"> Driver Name & No :'.$hndbyname.' Vehicle No:'.$vehicle_num.'</span></h3> 
					  	</div>
					';
			$ttl_man_pages = count($manifesto_listbypages);
			$slno = 1;
			foreach($manifesto_listbypages as $p=>$man_bypage)
			{
				
				echo '
						<table id="manifesto_list" border=1 cellpadding="5" cellspacing="0" style="width:100%;border-width:1px;'.($ttl_man_pages==$p+1?'':'page-break-after:always;').'border-collapse:collapse;">
						<thead>
							<th width="20"><b>Slno</b></th>
							<th width="60"><b>Invoiceno</b></th>
							<th width="300"><b>Name</b></th>
							<th width="130"><b>City</b></th>
							<th width="60"><b>Pincode</b></th>
							<th width="60"><b>Mobile</b></th>
							<th width="200"><b>Seal & Signature </b></th>
						</thead>
						<tbody>';
				
				
				
					foreach($man_bypage as $fr_id=>$fr_manifesto)
					{	
						$row_span = count($fr_manifesto);
						$k = 0 ;		
						foreach($fr_manifesto as $fr_man_inv)
						{
							$inv_barcode = base64_encode(generate_barcode($fr_man_inv['invoice_no'],400,60,2));
							
							echo '<tr>
								<td align="center" style="height:45px;">'.$slno++.'</td>
								<td align="center">'.$fr_man_inv['invoice_no'].'<br><img width="150" src="data:image/png;base64,'.$inv_barcode.'"/></td>';
									if($k == 0) {
										echo '<td rowspan="'.$row_span.'" style="vertical-align: middle;text-align: center;">'.$fr_manifesto[0]['franchise_name'].'</td>';
										echo '<td rowspan="'.$row_span.'" style="vertical-align: middle;text-align: center;">'.$fr_manifesto[0]['city'].'</td>';
										echo '<td rowspan="'.$row_span.'" style="vertical-align: middle;text-align: center;">'.$fr_manifesto[0]['postcode'].'</td>';
										echo '<td rowspan="'.$row_span.'" style="vertical-align: middle;text-align: center;">'.$fr_manifesto[0]['login_mobile1'].'</td>';
										echo '<td rowspan="'.$row_span.'" ><div style="height: 160px;width: 250px;background: #fdfdfd;"></div></td>';
										$k = 1;
									}
							echo '</tr>';
						}
					}
					echo '</tbody>
					</table>
					
					';	
			}
			
			echo '</div>';
		}
		
		
		echo '</body>
					</html>';
		echo '<script>window.print()</script>';
		
													
	}


	function gen_manifestoprintbydispatch($sent_manifestoid)
	{
		
		$this->load->plugin('suggest_group_list');
		
		$sent_invoices='';
		if($sent_manifestoid)
		{
			$sent_invoices=$this->db->query('select a.sent_invoices,b.name,a.hndleby_name,b.contact_no,a.hndleby_contactno,a.hndleby_vehicle_num from pnh_m_manifesto_sent_log a
														left join m_employee_info b on b.employee_id = a.hndleby_empid
														where a.id=?',$sent_manifestoid)->result_array();
			
			$invoices=$sent_invoices[0]['sent_invoices'];
			$hndbyname=$sent_invoices[0]['name'];
			$hndbycontactno=$sent_invoices[0]['contact_no'];
			$vehicle_num=$sent_invoices[0]['hndleby_vehicle_num'];
			/*if(!$hndbyname)
				$hndbyname=$sent_invoices[0]['hndleby_name'];
			
			if(!$hndbycontactno)
				$hndbycontactno=$sent_invoices[0]['hndleby_contactno'];*/
			if(!$hndbyname)
				$hndbyname='________________________';
			if(!$hndbycontactno)
				$hndbycontactno='';
			if(!$vehicle_num)
				$vehicle_num='________________________';
			
			//$this->db->query("update pnh_invoice_transit_log set status = 1,logged_on=now() where sent_log_id = ? and status = 0 ",$sent_manifestoid);
			
			if($this->db->affected_rows())
			{
				// send sms to tm and exec fc for towns
				$sql="select a.invoice_no,d.territory_id,territory_name,d.franchise_name,c.transid,t.town_name,emp.job_title,emp.name,emp.job_title2,tt.town_id,emp.contact_no,emp.employee_id
										from king_invoice a
										join king_transactions c on c.transid = a.transid 
										join pnh_m_franchise_info d on d.franchise_id = c.franchise_id 
										join m_town_territory_link tt on tt.territory_id = d.territory_id
										join m_employee_info emp on emp.employee_id = tt.employee_id and emp.job_title in (4,5) and emp.job_title2 = 0 
										join pnh_m_territory_info e on e.id = d.territory_id
										join pnh_towns t on t.id=d.town_id
										where a.invoice_no in ($invoices) and c.is_pnh = 1 and tt.is_active=1
										group by d.town_id 
										order by territory_name; ";
				
				$employees_list=$this->db->query($sql)->result_array();
				
				if ($employees_list)
				{
					foreach($employees_list as $emp)
					{	
						$emp_name=$emp['name'];
						$emp_id=$emp['employee_id'];
						$town_name=$emp['town_name'];
						$emp_contact_nos = explode(',',$emp['contact_no']);
						$sms_msg = 'Dear '.$emp_name.',Shipment for the town '.$town_name.' sent via '.ucwords($hndbyname).'('.$hndbycontactno.') vehicle no ['.$vehicle_num.'].';
						foreach($emp_contact_nos as $emp_mob_no)
						{
							/*$this->erpm->pnh_sendsms($emp_mob_no,$sms_msg);
						//	echo $emp_mob_no,$sms_msg;
							$this->db->query('insert into pnh_employee_grpsms_log(emp_id,contact_no,type,grp_msg,created_on)values(?,?,?,?,now())',array($emp_id,$emp_mob_no,4,$sms_msg,date('Y-m-d H:i:s')));
							*/
						}
					}
				}
			}
		}
		
		$manifesto_log_det = $this->db->query("select st_date,en_date,invoice_nos from pnh_manifesto_log where id = ? ",$logid)->row_array();
		$data['from'] = $manifesto_log_det['st_date'];
		$data['to'] = $manifesto_log_det['en_date'];
		$invnos = $manifesto_log_det['invoice_nos'];
		if($sent_manifestoid)
			$invnos=$sent_invoices[0]['sent_invoices'];
		
		echo '<html>
					<head>
						<title>Manifesto Print</title>
						<style>
							body{
								font-family:arial;
								font-size:14px;
								
							}
						</style>
					</head>
					<body>';
					/*
		$outscan_terr_res = $this->db->query("select distinct e.territory_id,t.territory_name 
														from shipment_batch_process_invoice_link a 
													join king_invoice b on b.invoice_no = a.invoice_no 
													join king_orders c on c.id = b.order_id 
													join king_transactions d on d.transid = c.transid 
													join pnh_m_franchise_info e on e.franchise_id = d.franchise_id
													join pnh_m_territory_info t on t.id = e.territory_id
													join pnh_towns t1 on t1.id = e.town_id
													where (a.shipped=1 or a.outscanned=1) and is_pnh = 1  
													and a.invoice_no in ($invnos)
													order by t.territory_name "); */
		$outscan_terr_res = $this->db->query("select distinct e.franchise_id,e.franchise_name,e.territory_id,t.territory_name 
												from shipment_batch_process_invoice_link a 
												join king_invoice b on b.invoice_no = a.invoice_no 
												join king_orders c on c.id = b.order_id 
												join king_transactions d on d.transid = c.transid 
												join pnh_m_franchise_info e on e.franchise_id = d.franchise_id
												join pnh_m_territory_info t on t.id = e.territory_id
												join pnh_towns t1 on t1.id = e.town_id
												where (a.shipped=1 or a.outscanned=1) and is_pnh = 1  
													and a.invoice_no in ($invnos) 
												group by e.franchise_id 
												order by t.territory_name");
		

		
		

		$ttl_terr_listed = $outscan_terr_res->num_rows();		

		 
		foreach($outscan_terr_res->result_array() as $t_indx=>$terr_det)
		{											
			/*
			$man_sql = "select g.franchise_id,b.itemid,dispatch_id,franchise_name,dispatch_id,c.name,sum(b.quantity) as qty,
								sum((b.i_orgprice-(b.i_discount+b.i_coup_discount))*b.quantity) as amt 
						from king_invoice a
						join king_orders b on a.order_id =  b.id
						join king_dealitems c on c.id = b.itemid
						join king_deals d on d.dealid = c.dealid 
						join shipment_batch_process_invoice_link e on e.invoice_no = a.invoice_no 
						join pnh_m_manifesto_sent_log f on f.manifesto_id = e.inv_manifesto_id
						join king_transactions g on g.transid = b.transid 
						join pnh_m_franchise_info h on h.franchise_id = g.franchise_id 
						join proforma_invoices i on i.p_invoice_no = e.p_invoice_no 
						where f.id = ? and a.invoice_no in ($invnos) and h.territory_id = ? 
						group by b.itemid,dispatch_id,g.franchise_id
						order by g.franchise_id
					";
				*/
				$man_sql = "select *,trans_date,sum(quantity) as qty,sum(billing_amt) as amt,sum(dp) as dpamt,sum(credit_note_amount) as cramt from (select distinct g.franchise_id,g.init as trans_date,a.invoice_no,b.itemid,dispatch_id,franchise_name,c.name,b.quantity,
						(b.i_orgprice-(b.i_discount+b.i_coup_discount))*b.quantity as billing_amt,
						(b.i_orgprice-(b.i_discount))*b.quantity as dp,
						round(((b.i_orgprice-(b.i_discount)) - (b.i_orgprice-(b.i_discount+b.i_coup_discount)))*b.quantity,2) as credit_note_amount
						from king_invoice a
						join king_orders b on a.order_id = b.id
						join king_dealitems c on c.id = b.itemid
						join king_deals d on d.dealid = c.dealid
						join shipment_batch_process_invoice_link e on e.invoice_no = a.invoice_no
						join pnh_m_manifesto_sent_log f on f.manifesto_id = e.inv_manifesto_id
						join king_transactions g on g.transid = b.transid
						join pnh_m_franchise_info h on h.franchise_id = g.franchise_id
						join proforma_invoices i on i.p_invoice_no = e.p_invoice_no
						where f.id = ? and g.franchise_id = ? and a.invoice_no in ($invnos) and h.territory_id = ? 
						and d.menuid = 112 
						order by g.franchise_id)
						as sub_set
						group by franchise_name,dispatch_id,itemid;
						";
			$outscan_res = $this->db->query($man_sql,array($sent_manifestoid,$terr_det['franchise_id'],$terr_det['territory_id']));

 
		
			$manifesto_listbypages = suggest_grp_pages($outscan_res->result_array(),4,20,'dispatch_id');

			 
			
			
			$terr_det['hub_name'] = @$this->db->query("select distinct d.hub_name from shipment_batch_process_invoice_link a 
	join pnh_t_tray_invoice_link b on a.invoice_no = b.invoice_no 
	join pnh_t_tray_territory_link c on b.tray_terr_id = c.tray_terr_id
	join pnh_deliveryhub d on d.id = c.territory_id 
	join pnh_m_manifesto_sent_log e on e.manifesto_id = a.inv_manifesto_id
	where e.id = ?
			",$sent_manifestoid)->row()->hub_name;
			
			echo '<div '.(($ttl_terr_listed!=$t_indx+1)?'style="page-break-after:always;"':'').'>
						<div>
							<h3>Storeking - Delivery Log Sheet  <span style="float:right">Date :___________________</span></h3> 
							<h3>Manifesto No : '.$sent_manifestoid.' &nbsp;&nbsp;&nbsp;&nbsp; Hub : '.ucwords($terr_det['hub_name']).'
							<h3> <span style="float:right"> Driver Name & No :'.$hndbyname.' Vehicle No:'.$vehicle_num.'</span></h3> 
					  	</div>
					';
			$ttl_man_pages = count($manifesto_listbypages);
			$slno = 1;
			foreach($manifesto_listbypages as $p=>$man_bypage)
			{
						
					
				
				echo '
						<table id="manifesto_list" border=1 cellpadding="5" cellspacing="0" style="width:100%;border-width:1px;'.($ttl_man_pages==$p+1?'':'page-break-after:always;').'border-collapse:collapse;">
						<thead>
							<th width="20"><b>Slno</b></th>
							<th width="300"><b>FranchiseName</b></th>
							<th width="300"><b>DocumentID</b></th>
							<th width="130"><b>Name</b></th>
							<th width="60"><b>Qty</b></th>
							<th width="60"><b>Amount</b></th>
							<th width="200"><b>Seal & Signature </b></th>
						</thead>
						<tbody>';
				
				
					
						$ttl_m_qty = 0;
						$ttl_m_dpamt = 0;
						$ttl_m_cramt = 0;
						$ttl_amt = 0;
					foreach($man_bypage as $fr_id=>$fr_manifesto)
					{	
						$row_span = count($fr_manifesto);
						$k = 0 ;	
						foreach($fr_manifesto as $fr_man_inv)
						{
							 $fr_town_name = $this->db->query("select town_name from pnh_m_franchise_info a join pnh_towns b on a.town_id = b.id and franchise_id = ? ",$fr_man_inv['franchise_id'])->row()->town_name;
							 
							 $is_mob_deal_res = $this->db->query("select pnh_id,menuid,a.name
											from king_dealitems a 
											join king_deals b on a.dealid = b.dealid 
											where a.id = ? and catid = 9 and menuid = 112 limit 10; 
										 ",$fr_man_inv['itemid']);
							if($is_mob_deal_res->num_rows())
							{
								$mob_det = $is_mob_deal_res->row_array();
								$dd_item_name = 'Mobile Phone - '.$mob_det['pnh_id'];
							}else
							{
								$dd_item_name = $fr_man_inv['name'];
							}
							echo '<tr>
								<td align="center" style="height:45px;">'.$slno++.'</td>';
									if($k == 0) {
										
										$require_immediate_payment = $this->db->query("select ifnull(sum(c.immediate_payment),0) as t 
																			from king_invoice a
																			join king_orders b on a.order_id = b.id and has_offer=1 
																			join pnh_m_offers c on c.id = b.offer_refid
																			join proforma_invoices d on d.order_id = b.id and d.invoice_status = 1 
																			where d.dispatch_id = ? ",$fr_man_inv['dispatch_id'])->row()->t;
										
										echo '<td rowspan="'.$row_span.'" style="vertical-align: middle;text-align: center;">'.$fr_man_inv['franchise_name'].'<br> ('.$fr_town_name.')'.' <br><br> '.(format_date_ts($fr_man_inv['trans_date'])).' '.($require_immediate_payment?'<br><br> Immediate Payment':'').' </td>'; 
										echo '<td style="vertical-align: middle;text-align: center;width: 100px;">'.$fr_man_inv['dispatch_id'].'</td>';
									}else
									{
										echo '<td style="vertical-align: middle;text-align: center;width: 100px;">&nbsp;</td>';
									}	
								
								echo '<td style="vertical-align: middle;text-align: center;width: 250px;">'.$dd_item_name.'</td>';
								echo '<td style="vertical-align: middle;text-align: center;">'.$fr_man_inv['qty'].'</td>';
								echo '<td style="vertical-align: middle;text-align: center;">'.$fr_man_inv['amt'].'</td>';
								if($k == 0) {
									echo '<td rowspan="'.$row_span.'" style="width: 250px;"><div style="height: 160px;width: 250px;background: #fdfdfd;"></div></td>';
									$k = 1;
								}
							echo '</tr>';
							$ttl_m_qty += $fr_man_inv['qty'];
							$ttl_amt += $fr_man_inv['amt'];
							$ttl_m_dpamt += $fr_man_inv['dpamt'];
							$ttl_m_cramt += $fr_man_inv['cramt'];

						}

						

					}

					echo '
							<tr>
								<td colspan="5" align="right">Total</td>
								<td>'.$ttl_m_dpamt.'</td>
							</tr>
						';
					echo '
							<tr>
								<td colspan="5" align="right">Membership fee</td>
								<td>'.$ttl_m_cramt.'</td>
							</tr>
						';
					echo '
							<tr>
								<td colspan="5" align="right">To Collect</td>
								<td>'.$ttl_amt.'</td>
							</tr>
						';

					echo '</tbody>
					</table>
					
					';	
			}
			
			echo '</div>';
		}
		
		
		echo '</body>
					</html>';
		//echo '<script>window.print()</script>';


		

		 

	}
	 
	function gen_manifestoprint()
	{
	
		$user = $this->auth();
		
		$sent_manifestoid=$this->input->post('sent_id');
		$logid = $this->input->post('id');
		
		$sent_invoices='';
		if($sent_manifestoid)
		{
			$sent_invoices=$this->db->query('select sent_invoices from pnh_m_manifesto_sent_log where id=?',$sent_manifestoid)->result_array();
		}
		 
		if($logid)
		{
			$manifesto_log_det = $this->db->query("select st_date,en_date,invoice_nos from pnh_manifesto_log where id = ? ",$logid)->row_array();
			$data['from'] = $manifesto_log_det['st_date'];
			$data['to'] = $manifesto_log_det['en_date'];
			$invnos = $manifesto_log_det['invoice_nos'];
			if($sent_manifestoid)
				$invnos=$sent_invoices[0]['sent_invoices'];
			 
			 $outscan_res = $this->db->query("select a.id,e.franchise_id,a.invoice_no,b.id,c.id,franchise_name,
															town_name,postcode,login_mobile1,e.city 
														from shipment_batch_process_invoice_link a 
													join king_invoice b on b.invoice_no = a.invoice_no 
													join king_orders c on c.id = b.order_id 
													join king_transactions d on d.transid = c.transid 
													join pnh_m_franchise_info e on e.franchise_id = d.franchise_id
													join pnh_m_territory_info t on t.id = e.territory_id
													join pnh_towns t1 on t1.id = e.town_id
													where (a.shipped=1 or a.outscanned=1)  and is_pnh = 1  
													and a.invoice_no in ($invnos)
													group by a.invoice_no 
													order by territory_name,town_name,franchise_name  ");
			
			
			
			
		}
		else
		{
			$data['from'] = $this->input->post('from_d');
			$data['to'] = $this->input->post('to_d');
			$exclude_invs = $this->input->post('exclude_invs');
			$sel_terr_ids = $this->input->post('sel_terr_ids');
			
			$cond =  '';
			if($sel_terr_ids)
				$cond = ' and e.territory_id in ('.$sel_terr_ids.')';
			
			$outscan_res = $this->db->query("select a.id,e.franchise_id,a.invoice_no,b.id,c.id,franchise_name,town_name,postcode,login_mobile1,e.city 
														from shipment_batch_process_invoice_link a 
													join king_invoice b on b.invoice_no = a.invoice_no 
													join king_orders c on c.id = b.order_id 
													join king_transactions d on d.transid = c.transid 
													join pnh_m_franchise_info e on e.franchise_id = d.franchise_id
													join pnh_m_territory_info t on t.id = e.territory_id
													join pnh_towns t1 on t1.id = e.town_id 
													where a.shipped=1 and is_pnh = 1 and date(shipped_on)>=? and date(shipped_on)<=? 
													and a.invoice_no not in ($exclude_invs)
													$cond 
													group by a.invoice_no 
													order by territory_name,town_name,franchise_name  ",array($data['from'],$data['to']));
		}
		if($outscan_res->num_rows())
		{
			$manifesto_list = array();
			$invnos = array();
			foreach($outscan_res->result_array() as $row)
			{	
				if(!isset($manifesto_list[$row['franchise_id']]))
					$manifesto_list[$row['franchise_id']] = array();
				array_push($manifesto_list[$row['franchise_id']],$row);
				
				array_push($invnos,$row['invoice_no']);
			}
			
			$invnos = array_unique($invnos);
			
			if($logid)
			{
				$upddata = array();
				$upddata['modified_on'] = date('Y-m-d H:i:s');
				$upddata['modified_by'] = $user['userid'];
				$upddata['id'] = $logid;
				
				if($sent_manifestoid)
				{
					$upddata['id'] = $sent_manifestoid;
					$this->db->query("update pnh_m_manifesto_sent_log set is_printed = is_printed+1,modified_on=?,modified_by=? where id = ? limit 1 ",$upddata);
				}else{
					$this->db->query("update pnh_manifesto_log set total_prints = total_prints+1,modified_on=?,modified_by=? where id = ? limit 1 ",$upddata);
				}
			}
			else
			{
				$ins_data = array();
				$ins_data['name'] = 'Manifesto for '.format_date($data['from']).'-'.format_date($data['to']);
				$ins_data['total_prints'] = 1;
				$ins_data['st_date'] = $data['from'];
				$ins_data['en_date'] = $data['to'];
				$ins_data['invoice_nos'] = implode(',',$invnos);
				$ins_data['created_on'] = date('Y-m-d H:i:s');
				$ins_data['created_by'] = $user['userid'];
				$this->db->insert("pnh_manifesto_log",$ins_data);
				$logid = $this->db->insert_id();
			}
		
			$this->_gen_manifestoprint_byterr($logid,$sent_manifestoid);
		}
		
		
	}
	 
	
	
	
	

	function packed_list($s=0,$e=0)
	{
		$user=$this->auth(OUTSCAN_ROLE|PRODUCT_MANAGER_ROLE|DEAL_MANAGER_ROLE);
		if($s==0)
		{
			$data['pagetitle']="Today's Packed List";
			$s=$e=date("Y-m-d");
		}else $data['pagetitle']="Packed List between $s and $e";
		$s=date("Y-m-d H:i:s",strtotime($s));
		$e=date("Y-m-d H:i:s",strtotime($e." 23:59:59"));
		$data['packed']=$this->db->query("select    b.invoiced_on,b.invoiced_by,ti.id as territory_id,ti.territory_name,t.franchise_id,ifnull(b.shipped_on,'') as o_shipped_on,pt.courier_name as partner_courier_name,r.ship_person,p.total_prints,p.last_printedon,ifnull(t.partner_reference_no,0) as partner_reference_no,b.shipped,p.transid,b.batch_id,b.p_invoice_no,b.invoice_no,b.awb,(b.packed_on) as packed_on,a.name as packed_by 
													from shipment_batch_process_invoice_link b 
													left outer join king_invoice p on p.invoice_no=b.invoice_no 
													left join king_orders r on r.id = p.order_id 
													left join king_transactions t on t.transid = p.transid 
													left join partner_transaction_details pt on pt.transid = t.transid and t.partner_reference_no = pt.order_no 
													join king_admin a on a.id=b.packed_by 
													left join pnh_m_franchise_info f on f.franchise_id = t.franchise_id 
													left join pnh_m_territory_info ti on ti.id = f.territory_id  
												where b.packed=1 and (b.packed_on) between '$s' and '$e' 
												group by b.p_invoice_no 
												order by b.packed_on desc
											")->result_array();
		
		$data['page']="packed_list";
		$this->load->view("admin",$data);
	}
	
	function outscan_list($s=0,$e=0)
	{
		$user=$this->auth(OUTSCAN_ROLE|PRODUCT_MANAGER_ROLE|DEAL_MANAGER_ROLE);
		if($s==0)
		{
			$data['pagetitle']="Today's Outscan/Shipped List";
			$s=$e=date("Y-m-d");
		}else $data['pagetitle']="Outscan/Shipped List between $s and $e";
		$s=date("Y-m-d H:i:s",strtotime($s));
		$e=date("Y-m-d H:i:s",strtotime($e." 23:59:59"));
		$data['outscans']=$this->db->query("select p.transid,b.batch_id,b.p_invoice_no,b.invoice_no,b.awb,(b.shipped_on) as shipped_on,a.name as shipped_by from shipment_batch_process_invoice_link b left outer join king_invoice p on p.invoice_no=b.invoice_no join king_admin a on a.id=b.shipped_by where b.shipped=1 and (b.shipped_on) between '$s' and '$e' group by b.p_invoice_no order by b.shipped_on desc")->result_array();
		$data['page']="outscan_list";
		$this->load->view("admin",$data);
	}
	
	function outscan($is_pnh=1)
	{
		$user=$this->auth(OUTSCAN_ROLE|PNH_INVOICE_PACK);
		if($_POST)
		{
			if($_POST['no_scan_by'] == 1)
				$this->erpm->do_outscan($_POST['awn'],"");
			else if($_POST['no_scan_by'] == 2)
				$this->erpm->do_outscan($_POST['awn'],$_POST["partner_id"]);
		}

		$data['scan_pnh']=$is_pnh;
		$data['page']="outscan";
		$this->load->view("admin",$data);
	}
	
	function cancel_proforma_invoice($p_invoice)
	{
		$invoice=$this->db->query("select transid,order_id,p_invoice_no,p_invoice_no as invoice_no from proforma_invoices where p_invoice_no=? and invoice_status=1",$p_invoice)->result_array();
		if(empty($invoice))
			show_error("Proforma Invoice not found or Invoice already cancelled");
		$transid=$invoice[0]['transid'];
		$oids=array();
		foreach($invoice as $i)
			$oids[]=$i['order_id'];
			
		$orders=$this->db->query("select quantity as qty,itemid,id from king_orders where id in ('".implode("','",$oids)."') and transid = ? ",$transid)->result_array();
		
		$batch_id = $this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no=?",$p_invoice)->row()->batch_id;

		$proforma_inv_det = $this->db->query("select id,is_b2b from proforma_invoices where p_invoice_no=? ",$p_invoice)->row_array();
		
		$proforma_inv_id = $proforma_inv_det['id'];
		$is_pnh = $proforma_inv_det['is_b2b'];
		
		
		foreach($orders as $o)
		{
			$pls=$this->db->query("select qty,pl.product_id,p.mrp,p.brand_id from m_product_deal_link pl join m_product_info p on p.product_id=pl.product_id where itemid=?",$o['itemid'])->result_array();
			
			$pls2=$this->db->query("select pl.qty,p.product_id,p.mrp,p.brand_id 
						from products_group_orders pgo
						join king_orders o on o.id = pgo.order_id 
						join m_product_group_deal_link pl on pl.itemid=o.itemid 
						join m_product_info p on p.product_id=pgo.product_id 
						where pgo.order_id=? and o.transid = ? ",array($o['id'],$transid))->result_array();
								
			$pls=array_merge($pls,$pls2);
			 
			foreach($pls as $p)
			{
				
				/** Default rack bin used if brand is not linked for loc **/ 
				$p['location'] = 1;
				$p['rackbin'] = 10;
				$loc_det_res = $this->db->query("select location_id,rack_bin_id from m_rack_bin_brand_link a join m_rack_bin_info b on a.rack_bin_id = b.id where brandid = ? limit 1 ",$p['brand_id']);
				if($loc_det_res->num_rows())
				{
					$loc_det = $loc_det_res->row_array();	
					$p['location'] = $loc_det['location_id'];
					$p['rackbin'] = $loc_det['rack_bin_id'];
				}
				
				$p_reserv_qty = 0;
				//$reserv_stk_res = $this->db->query('select id,release_qty,extra_qty,stock_info_id,qty from t_reserved_batch_stock where batch_id = ? and p_invoice_no = ? and status = 1 and order_id = ? and product_id = ? ',array($batch_id,$p_invoice,$o['id'],$transid,$p['product_id']));
				
				$reserv_stk_res = $this->db->query('select a.id,a.release_qty,a.extra_qty,a.stock_info_id,a.qty 
										from t_reserved_batch_stock a
										join king_orders b on a.order_id = b.id  
										where batch_id = ? and p_invoice_no = ? 
										and a.status = 0 and a.order_id = ? and b.transid = ? and product_id = ?',array($batch_id,$p_invoice,$o['id'],$transid,$p['product_id']));
													
				 
	
				if($reserv_stk_res->num_rows())
				{
					foreach($reserv_stk_res->result_array() as $reserv_stk_det)
					{
						$rqty = $reserv_stk_det['qty']-$reserv_stk_det['release_qty']+$reserv_stk_det['extra_qty'];
						$p_reserv_qty += $rqty;
						//$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and stock_id = ? limit 1";
						//$this->db->query($sql,array($rqty,$p['product_id'],$reserv_stk_det['stock_info_id']));
						$this->db->query("update t_reserved_batch_stock set status=3 where id = ? ",array($reserv_stk_det['id']));
						//$this->erpm->do_stock_log(1,$rqty,$p['product_id'],$proforma_inv_id,false,true,true,-1,0,0,$reserv_stk_det['stock_info_id']);
						
						
						$stk_info = $this->db->query("select * from t_stock_info where stock_id = ? ",$reserv_stk_det['stock_info_id'])->row_array();
						
						if($stk_info)
						{
							 
							$this->erpm->_upd_product_stock($stk_info['product_id'],$stk_info['mrp'],$stk_info['product_barcode'],$stk_info['location_id'],$stk_info['rack_bin_id'],0,$rqty,1,1,$proforma_inv_id);	
						}else
						{
							 
							$this->erpm->_upd_product_stock($p['product_id'],$p['mrp'],'',$p['location'],$p['rackbin'],0,$rqty,1,1,$proforma_inv_id);
						}
					}
					
				}else{
					 
					$this->erpm->_upd_product_stock($p['product_id'],$p['mrp'],'',$p['location'],$p['rackbin'],0,$p['qty']*$o['qty'],1,1,$proforma_inv_id);
					
					/*
					$new_stock_entry = true;
					$sp_det_res = $this->db->query("select product_id,mrp from t_stock_info where product_id = ? ",$p['product_id']);
					if($sp_det_res->num_rows())
					{
						foreach($sp_det_res->result_array() as $sp_row)
							if($sp_row['mrp'] == $p['mrp'])
								$new_stock_entry = false;
					}
					if($new_stock_entry)
						 $this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,available_qty,product_barcode,created_on) values(?,?,?,?,?,?,now())",array($p['product_id'],$p['location'],$p['rackbin'],$p['mrp'],0,''));
					
					$stk_ref_id = @$this->db->query("select stock_id from t_stock_info where product_id=? and mrp=? limit 1 ",array($p['product_id'],$p['mrp']))->row()->stock_id;
					if($stk_ref_id)
					{
						$sql="update t_stock_info set available_qty=available_qty+? where stock_id=? limit 1";
						$this->db->query($sql,array($p['qty']*$o['qty'],$stk_ref_id));
						$this->erpm->do_stock_log(1,$p['qty']*$o['qty'],$p['product_id'],$proforma_inv_id,false,true,true,-1,0,0,$stk_ref_id);
					}
					**/
				}
				
				
				
			}
		
		}
 
 		 
		$this->db->query("update king_orders set status=0 where id in ('".implode("','",$oids)."') and transid=? ",$transid);
		$this->db->query("update proforma_invoices set invoice_status=0 where p_invoice_no=? and transid = ? ",array($p_invoice,$transid));
		$this->erpm->do_trans_changelog($transid,"Proforma Invoice no $p_invoice cancelled");
		$this->session->set_flashdata("erp_pop_info","Proforma Invoice cancelled");
		$bid=$this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no=?",$p_invoice)->row()->batch_id;

		$is_batch_open = $this->db->query("select count(*) as t from (
			select  a.batch_id,a.p_invoice_no,sum(1) as total,sum(b.invoice_status) as ttl_active,sum(if(a.invoice_no,1,0)) as ttl_invoiced 
				from shipment_batch_process_invoice_link a
				join proforma_invoices b on a.p_invoice_no = b.p_invoice_no 
				join shipment_batch_process c on c.batch_id = a.batch_id 
				where a.batch_id = ?  
			group by a.batch_id
			having ttl_active != ttl_invoiced 
			) as g ",$bid)->row()->t;
	
			if($is_batch_open)
				$this->db->query("update shipment_batch_process set status=1 where batch_id=? limit 1",$bid);
			else
				$this->db->query("update shipment_batch_process set status=2 where batch_id=? limit 1",$bid);
		
		redirect("admin/proforma_invoice/$p_invoice");
	}

	function cancel_proforma_invoice_old($p_invoice)
	{
		$invoice=$this->db->query("select transid,order_id,p_invoice_no,p_invoice_no as invoice_no from proforma_invoices where p_invoice_no=? and invoice_status=1",$p_invoice)->result_array();
		if(empty($invoice))
			show_error("Proforma Invoice not found or Invoice already cancelled");
		$transid=$invoice[0]['transid'];
		$oids=array();
		foreach($invoice as $i)
			$oids[]=$i['order_id'];
			
		$orders=$this->db->query("select quantity as qty,itemid,id from king_orders where id in ('".implode("','",$oids)."') and transid = ? ",$transid)->result_array();
		
		$batch_id = $this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no=?",$p_invoice)->row()->batch_id;

		$proforma_inv_det = $this->db->query("select id,is_b2b from proforma_invoices where p_invoice_no=? ",$p_invoice)->row_array();
		
		$proforma_inv_id = $proforma_inv_det['id'];
		$is_pnh = $proforma_inv_det['is_b2b'];
		
		
		foreach($orders as $o)
		{
			$pls=$this->db->query("select qty,pl.product_id,p.mrp,p.brand_id from m_product_deal_link pl join m_product_info p on p.product_id=pl.product_id where itemid=?",$o['itemid'])->result_array();
			
			$pls2=$this->db->query("select pl.qty,p.product_id,p.mrp,p.brand_id 
						from products_group_orders pgo
						join king_orders o on o.id = pgo.order_id 
						join m_product_group_deal_link pl on pl.itemid=o.itemid 
						join m_product_info p on p.product_id=pgo.product_id 
						where pgo.order_id=? and o.transid = ? ",array($o['id'],$transid))->result_array();
								
			$pls=array_merge($pls,$pls2);
			 
			foreach($pls as $p)
			{
				
				/** Default rack bin used if brand is not linked for loc **/ 
				$p['location'] = 1;
				$p['rackbin'] = 10;
				$loc_det_res = $this->db->query("select location_id,rack_bin_id from m_rack_bin_brand_link a join m_rack_bin_info b on a.rack_bin_id = b.id where brandid = ? limit 1 ",$p['brand_id']);
				if($loc_det_res->num_rows())
				{
					$loc_det = $loc_det_res->row_array();	
					$p['location'] = $loc_det['location_id'];
					$p['rackbin'] = $loc_det['rack_bin_id'];
				}
				
				$p_reserv_qty = 0;
				//$reserv_stk_res = $this->db->query('select id,release_qty,extra_qty,stock_info_id,qty from t_reserved_batch_stock where batch_id = ? and p_invoice_no = ? and status = 1 and order_id = ? and product_id = ? ',array($batch_id,$p_invoice,$o['id'],$transid,$p['product_id']));
				
				$reserv_stk_res = $this->db->query('select a.id,a.release_qty,a.extra_qty,a.stock_info_id,a.qty 
										from t_reserved_batch_stock a
										join king_orders b on a.order_id = b.id  
										where batch_id = ? and p_invoice_no = ? 
										and a.status = 0 and a.order_id = ? and b.transid = ? and product_id = ?',array($batch_id,$p_invoice,$o['id'],$transid,$p['product_id']));
													
				 
	
				if($reserv_stk_res->num_rows())
				{
					foreach($reserv_stk_res->result_array() as $reserv_stk_det)
					{
						$rqty = $reserv_stk_det['qty']-$reserv_stk_det['release_qty']+$reserv_stk_det['extra_qty'];
						$p_reserv_qty += $rqty;
						$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and stock_id = ? limit 1";
						$this->db->query($sql,array($rqty,$p['product_id'],$reserv_stk_det['stock_info_id']));
						$this->db->query("update t_reserved_batch_stock set status=3 where id = ? ",array($reserv_stk_det['id']));
						$this->erpm->do_stock_log(1,$rqty,$p['product_id'],$proforma_inv_id,false,true,true,-1,0,0,$reserv_stk_det['stock_info_id']);
					}
					
				}else{
					
					$new_stock_entry = true;
					$sp_det_res = $this->db->query("select product_id,mrp from t_stock_info where product_id = ? ",$p['product_id']);
					if($sp_det_res->num_rows())
					{
						foreach($sp_det_res->result_array() as $sp_row)
							if($sp_row['mrp'] == $p['mrp'])
								$new_stock_entry = false;
					}
					if($new_stock_entry)
						 $this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,available_qty,product_barcode,created_on) values(?,?,?,?,?,?,now())",array($p['product_id'],$p['location'],$p['rackbin'],$p['mrp'],0,''));
					
					$stk_ref_id = @$this->db->query("select stock_id from t_stock_info where product_id=? and mrp=? limit 1 ",array($p['product_id'],$p['mrp']))->row()->stock_id;
					if($stk_ref_id)
					{
						$sql="update t_stock_info set available_qty=available_qty+? where stock_id=? limit 1";
						$this->db->query($sql,array($p['qty']*$o['qty'],$stk_ref_id));
						$this->erpm->do_stock_log(1,$p['qty']*$o['qty'],$p['product_id'],$proforma_inv_id,false,true,true,-1,0,0,$stk_ref_id);
					}
				}
				
				
				
			}
		
		}
		
		$this->db->query("update king_orders set status=0 where id in ('".implode("','",$oids)."') and transid=? ",$transid);
		$this->db->query("update proforma_invoices set invoice_status=0 where p_invoice_no=? and transid = ? ",array($p_invoice,$transid));
		$this->erpm->do_trans_changelog($transid,"Proforma Invoice no $p_invoice cancelled");
		$this->session->set_flashdata("erp_pop_info","Proforma Invoice cancelled");
		$bid=$this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no=?",$p_invoice)->row()->batch_id;

		$is_batch_open = $this->db->query("select count(*) as t from (
			select  a.batch_id,a.p_invoice_no,sum(1) as total,sum(b.invoice_status) as ttl_active,sum(if(a.invoice_no,1,0)) as ttl_invoiced 
				from shipment_batch_process_invoice_link a
				join proforma_invoices b on a.p_invoice_no = b.p_invoice_no 
				join shipment_batch_process c on c.batch_id = a.batch_id 
				where a.batch_id = ?  
			group by a.batch_id
			having ttl_active != ttl_invoiced 
			) as g ",$bid)->row()->t;
	
			if($is_batch_open)
				$this->db->query("update shipment_batch_process set status=1 where batch_id=? limit 1",$bid);
			else
				$this->db->query("update shipment_batch_process set status=2 where batch_id=? limit 1",$bid);
		
		redirect("admin/proforma_invoice/$p_invoice");
	}
	
	
	
	function proforma_invoice($p_invoice)
	{
		$user=$this->auth(INVOICE_PRINT_ROLE|ORDER_BATCH_PROCESS_ROLE|PNH_EXECUTIVE_ROLE);
		$data['batch']=$this->db->query("select * from shipment_batch_process_invoice_link where p_invoice_no=?",$p_invoice)->row_array();
		$data['invoice']=$this->db->query("select * from proforma_invoices where p_invoice_no=?",$p_invoice)->row_array();
		$data['orders']=$this->db->query("select e.product_id,o.id,i.id as itemid,i.name as product,o.quantity,o.transid 
													from proforma_invoices p 
													join king_orders o on o.id=p.order_id 
													join king_dealitems i on i.id=o.itemid 
													left join m_product_deal_link d on d.itemid = i.id 
													left join m_product_info e on e.product_id = d.product_id  
													where p.p_invoice_no=?",$p_invoice)->result_array();
		$data['page']="proforma_invoice";
		$this->load->view("admin",$data);
	}

	function pack_invoice($inv_no="")
	{
		$user=$this->auth(INVOICE_PRINT_ROLE);
		
		
		
		$data['batch_id']=$this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no = ? and invoice_no is null  ",$inv_no)->row()->batch_id;
		$data['invoice']=$this->erpm->getinvoiceforpacking($inv_no);
		
		$is_fran_suspended = @$this->db->query("select ifnull(is_suspended,0) as is_suspended from king_transactions a left join pnh_m_franchise_info b on a.franchise_id = b.franchise_id where a.transid = ? ",$data['invoice']['transid'])->row()->is_suspended;
		if($is_fran_suspended)
		{
			show_error("franchise is suspended and cannot process packing ");
		} 
		
		if($_POST)
			$this->erpm->do_pack();
		if(empty($inv_no))
			show_404();
		$data['page']="pack_invoice";
		$this->load->view("admin",$data);
	}

	function batch($bid)
	{
		$user=$this->auth(ORDER_BATCH_PROCESS_ROLE|OUTSCAN_ROLE|INVOICE_PRINT_ROLE);
		$data['batch']=$this->erpm->getbatch($bid);
		$data['invoices']=$this->erpm->getbatchinvoices($bid);
		$data['page']="batch";
		$this->load->view("admin",$data);
	}
	
	/*
	 *	function to get franchise names 
	 */
	 
	 function jx_gettown()
	{
		$territory_id = $this->input->post('ty');
		$bid = $this->input->post('batch_id');
		$invoices=$this->erpm->getbatchinvoices($bid);
		
		$trans_ids = array();
		foreach($invoices as $inv)
		{
			//if(empty($inv['transid'])) $inv['transid']=$inv['pi_transid'];
			array_push($trans_ids,"'".$inv['transid']."'");
		}
		$output=array();	
		$town_lst = $this->db->query("select tw.id,tw.town_name from king_transactions ta join pnh_m_franchise_info f on f.franchise_id=ta.franchise_id join pnh_m_territory_info t on t.id=f.territory_id join pnh_towns tw on tw.id=f.town_id where t.id = '".$territory_id."' and  ta.transid in (".(implode(',',$trans_ids)).") group by tw.id");
		$franchise_lst = $this->db->query("select f.franchise_id,f.franchise_name from king_transactions ta join pnh_m_franchise_info f on f.franchise_id=ta.franchise_id join pnh_m_territory_info t on t.id=f.territory_id join pnh_towns tw on tw.id=f.town_id where t.id = '".$territory_id."' and  ta.transid in (".(implode(',',$trans_ids)).") group by f.franchise_id ");
		
		if($town_lst->num_rows())
		{
			$output['town_list']=$town_lst->result_array();
			$output['status']='success';
		}
		else
		{
				$output['status']='error';
				$output['message']='No Towns found under this territory';
		}
		
		if($franchise_lst->num_rows())
		{
			$output['franch_list']=$franchise_lst->result_array();
			$output['status']='success';
		}
		else
		{
				$output['status']='error';
				$output['message']='No Towns found under this territory';
		}
		
	
			echo json_encode($output);
	}
	 
	 
	 
	 function jx_batchfranch()
	{
		$town_id = $this->input->post('tw');
		$bid = $this->input->post('batch_id');
		$invoices=$this->erpm->getbatchinvoices($bid);
		
		$trans_ids = array();
		foreach($invoices as $inv)
		{
			//if(empty($inv['transid'])) $inv['transid']=$inv['pi_transid'];
			array_push($trans_ids,"'".$inv['transid']."'");
		}
		
		$output=array();
		
		if($town_id!=0)
		{
			$res = $this->db->query("select f.franchise_id,f.franchise_name from king_transactions ta join pnh_m_franchise_info f on f.franchise_id=ta.franchise_id join pnh_towns tw on tw.id=f.town_id  where tw.id='".$town_id."' and ta.transid in (".(implode(',',$trans_ids)).") group by f.franchise_id");
		}
		else 
		{
			$res = $this->db->query("select f.franchise_id,f.franchise_name from king_transactions ta join pnh_m_franchise_info f on f.franchise_id=ta.franchise_id join pnh_towns tw on tw.id=f.town_id  where  ta.transid in (".(implode(',',$trans_ids)).")");
		}

		
		if($res)
		{
			$output['fran_list']=$res->result_array();
			$output['status']='success';
		}
		else
		{
				$output['status']='error';
				$output['message']='No Franchises found';
		}
	
			echo json_encode($output);
	}

	function stock_procure_list()
	{
		$user=$this->auth(INVOICE_PRINT_ROLE);
		
		if(!isset($_POST['tids']))
		{
			show_error("Invalid Access");
		}
			
		
		//$data['prods']=$this->erpm->getprodproclistfortransids($_POST['tids']);
		
		//$_POST['tids'] = 109943;
		
		$data['stk_rsv_list'] = $this->db->query("select g.*,transid from (
select a.product_id,a.p_invoice_no,c.product_name,concat(rack_name,bin_name) as rb,b.mrp,sum(a.qty) as qty
	from t_reserved_batch_stock a 
	join t_stock_info b on a.stock_info_id = b.stock_id 
	join m_product_info c on c.product_id = b.product_id 
	join m_rack_bin_info d on d.id = b.rack_bin_id 
	where a.p_invoice_no in (".$_POST['tids'].")
group by product_id,mrp ) as g
	join proforma_invoices e on e.p_invoice_no = g.p_invoice_no and invoice_status = 1 
group by g.product_id ");

		$data['stk_rsv_list'] = $this->db->query("select p_invoice_no,transid from proforma_invoices where p_invoice_no in (".$_POST['tids'].") and invoice_status = 1 group by p_invoice_no ");

		
		$this->load->view("admin/body/product_proc_list_transids",$data);
	}
	
	function product_proc_list_for_batch($bid)
	{
		$user=$this->auth(INVOICE_PRINT_ROLE);
		//$data['prods']=$this->erpm->getprodproclist($bid);
		
		$data['prods'] = $this->db->query("select product_id,product,location,sum(rqty) as qty from ( 
select a.product_id,c.product_name as product,concat(concat(rack_name,bin_name),'::',b.mrp) as location,a.qty as rqty 
	from t_reserved_batch_stock a 
	join t_stock_info b on a.stock_info_id = b.stock_id 
	join m_product_info c on c.product_id = b.product_id 
	join m_rack_bin_info d on d.id = b.rack_bin_id 
	join shipment_batch_process_invoice_link e on e.p_invoice_no = a.p_invoice_no and invoice_no = 0 
	where e.batch_id in (?)
group by a.id  ) as g 
group by product_id,location",$bid)->result_array();
		
		$this->load->view("admin/body/product_proc_list",$data);
	}
	
	function batch_process($s=false,$e=false)
	{
		$user=$this->auth(ORDER_BATCH_PROCESS_ROLE|INVOICE_PRINT_ROLE);
		if($s!=false && $e!=false && (strtotime($s)<=0 || strtotime($e)<=0))
			show_404();
		$data['batchs']=$this->erpm->getbatchs_date_range($s,$e);
		$data['page']="batch_list";
		if($e)
			$data['pagetitle']="between $s and $e";
		$this->load->view("admin",$data);
	}
	
	function pending_batch_process()
	{
		$user=$this->auth(ORDER_BATCH_PROCESS_ROLE|INVOICE_PRINT_ROLE);
		$data['batchs']=$this->erpm->getpendingbatchs();
		$data['page']="batch_list";
		$this->load->view("admin",$data);
	}
	
	function pnh_reverse_receipt($rid)
	{
		$user=$this->auth(FINANCE_ROLE);
		$r=$this->db->query("select * from pnh_t_receipt_info where receipt_id=?",$rid)->row_array();
		$this->db->query("update pnh_t_receipt_info set status=3 where receipt_id=? limit 1",$rid);
		$_POST=array("type"=>1,"amount"=>$r['receipt_amount'],"desc"=>"Reversal of receipt $rid","internal"=>true,"sms"=>false,"receipt_id"=>$rid);
		$this->pnh_acc_stat_c($r['franchise_id']);
		redirect($_SERVER['HTTP_REFERER']);
	}
	 function pnh_change_receipt_trans_type($receipt_id)
	{
		$user=$this->auth(FINANCE_ROLE);
		$trans_type=$this->db->query("select * from pnh_t_receipt_info where receipt_id=?",$receipt_id)->row_array();
		$this->db->query("update pnh_t_receipt_info set in_transit=0,remarks='Receipt In Hand',modified_on=?,modified_by=? where receipt_id=? limit 1",array(time(),$user['userid'],$receipt_id));
		redirect($_SERVER['HTTP_REFERER']);
	} 
	
	function pnh_acc_stat_c($fid)
	{
		$user=$this->auth(FINANCE_ROLE);
		if(!$_POST)
			die;
		$mob=$this->db->query("select login_mobile1 as m from pnh_m_franchise_info where franchise_id=?",$fid)->row()->m;
		foreach(array("type","amount","desc","sms","receipt_id") as $i)
			$$i=$this->input->post($i);
		
		$acc_stat_id = $this->erpm->pnh_fran_account_stat($fid,$type,$amount,$desc,"correction",$fid);
		$trans_type = 5;
		if($receipt_id)
			$trans_type = 3;
		
		$arr = array($fid,$receipt_id,$trans_type,$acc_stat_id,$type?$amount:0,!$type?$amount:0,$desc,1,date('Y-m-d H:i:s'),$user['userid']);
		$this->db->query("insert into pnh_franchise_account_summary (franchise_id,receipt_id,action_type,acc_correc_id,debit_amt,credit_amt,remarks,status,created_on,created_by) values(?,?,?,?,?,?,?,?,?,?)",$arr);
			
		if($sms)
			$this->erpm->pnh_sendsms($mob,"Amount of Rs $amount has been ".($type==0?"credited to":"debited from")." your franchise account against '$desc'",$fid);
		
		$is_manual_corr = $this->input->post("is_manual_corr");
		if($is_manual_corr)
		{
			$franchise_name = $this->db->query("select franchise_name from pnh_m_franchise_info where franchise_id = ? ",$fid)->row()->franchise_name; 
			$subj = 'Account Correction - Rs '.$amount.' '.(!$type?'Credited':'Debited').' to '.$franchise_name.' On '.format_datetime_ts(time());
			$message = '
						<h3>Account Correction</h3>
						
						<table cellpadding=5 cellspacing=0 border=1>
							<tr><td width="120"><b>Franchise</b></td><td>'.($franchise_name).'</td></tr>
							<tr><td><b>Type</b></td><td>'.(!$type?'Credit':'Debit').'</td></tr>
							<tr><td><b>Amount</b></td><td>Rs '.format_price($amount).'</td></tr>
							<tr><td><b>Date</b></td><td>'.format_datetime_ts(time()).'</td></tr>
							<tr><td><b>Updated By</b></td><td>'.$user['username'].'</td></tr>
							<tr><td><b>Notified Franchise</b></td><td>'.($sms?'Yes':'No').'</td></tr>
							<tr><td><b>Remarks</b></td><td><p style="padding:2px;">'.$desc.'</p></td></tr>
						</table>
						<br>
						<p style="color:#999">
							Storeking Team
						</p>
					';
					
			$this->erpm->_notifybymail(array('accounts@storeking.in'),$subj,$message,"Account Corrections",'support@snapittoday.com',array('gova@storeking.in','sri@storeking.in'));
			
		}
		
		
		$this->erpm->flash_msg("Account statement corrected");
		if(!isset($_POST['internal']))
			redirect("admin/pnh_franchise/$fid");
	}
	
	function product($pid=false)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		$data['product']=$this->db->query("select ifnull(sum(s.available_qty),0) as stock,p.*,b.name as brand from m_product_info p left outer join t_stock_info s on s.product_id=p.product_id join king_brands b on b.id=p.brand_id where p.product_id=?",$pid)->row_array();

		$data['prdct_lst'] = $this->db->query("select *,concat(product_name,'-',product_id) as product_name from m_product_info where is_serial_required=? and brand_id = ? and product_id != ? order by product_name ",array($p['is_serial_required'],$p['brand_id'],$p['product_id']));
		
		$data['page']="viewproduct";
		$this->load->view("admin",$data);
	}
	
	/*
	 * Ajax pagination fun to get stock log
	 */ 
	
	
	function jx_stocklog($pid='',$pg=0,$limit=20)
	{
		
		$this->erpm->auth();
		
		if(!$pid)
			exit;
		$tbl_data_html = '';
		
		$total_rows=$this->db->query("select count(*) as t from t_stock_update_log where product_id=? ",$pid)->row()->t;
		$sql="select u.name as username,l.*,pi.p_invoice_no,ci.invoice_no as c_invoice_no,i.invoice_no from t_stock_update_log l left outer join king_invoice i on i.id=l.invoice_id left outer join t_client_invoice_info ci on ci.invoice_id=l.corp_invoice_id left outer join proforma_invoices pi on pi.id=l.p_invoice_id left outer join king_admin u on u.id=l.created_by where l.product_id=? order by l.id desc limit $pg,$limit";
		$log_res = $this->db->query($sql,$pid);
		if($log_res->num_rows())
		{
			foreach($log_res->result_array() as $i=>$l )
			{
					$ref_link = 'Correction';
				
				if($l['corp_invoice_id']){
						$ref_link = '<a href="'.site_url("admin/client_invoice/{$l['corp_invoice_id']}").'">'.$l['c_invoice_no'].'</a>';
					}
					if($l['invoice_id']){
						$ref_link = 	'<a href="'.site_url("admin/invoice/{$l['invoice_no']}").'">'.$l['invoice_no'].'</a>';
					}
					if($l['grn_id']){
						$ref_link = 	'<a href="'.site_url("admin/viewgrn/{$l['grn_id']}").'">GRN'.$l['grn_id'].'</a>';
					}
					if($l['p_invoice_id']){
						$ref_link = 	'<a href="'.site_url("admin/proforma_invoice/{$l['p_invoice_no']}").'">PI'.$l['p_invoice_no'].'</a>';
					}
					if($l['return_prod_id']){
						$ref_link = '<a href="'.site_url("admin/pnh_product_returnbyid/{$l['return_prod_id']}").'">RI'.$l['return_prod_id'].'</a>';
					}

				$tbl_data_html .= '<tr>
						<td>'.($i+$pg+1).'</td>
						<td>'.($l['update_type']?"In":"Out").'</td>
						<td>'.$ref_link.'</td>
						<td>'.$l['qty'].'</td>
						<td>'.$l['current_stock'].'</td>
						<td>'.$l['username'].'</td>
						<td>'.format_datetime($l['created_on']).'</td>
						<td>'.$l['msg'].'</td>
					</tr>';
					
				 
			}
		}else
		{
			$tbl_data_html .= '<tr><td colspan="6"><div align="center"> No data found</div></td></tr>';
		}
		
		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/jx_stocklog/'.$pid);
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $limit;
		$config['uri_segment'] = 4;
		
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$pagi_links = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		
		$pagi_links = '<div class="log_pagination">'.$pagi_links.'</div>';
		
		echo json_encode(array('log_data'=>$tbl_data_html,'pagi_links'=>$pagi_links,'ttl'=>$total_rows,'limit'=>$limit));
	}
	
	/*
	 * Ajax pagination function to get IMEI list
	 * 
	 */ 
	 
	function jx_stockimeilist($pid='',$pg=0,$limit=20)
	{
		
		$this->erpm->auth();
		
		if(!$pid)
			exit;
		$tbl_data_html = '';
		
		$total_rows=$this->db->query("select count(*) as t from t_imei_no where product_id=? order by status asc  ",$pid)->row()->t;
		$sql="select * from t_imei_no where product_id=? order by status asc limit $pg,$limit";
		$imei_res = $this->db->query($sql,$pid);
		if($imei_res->num_rows())
		{
			foreach($imei_res->result_array() as $i=>$imei )
			{
				$imei['trans_id'] = $this->db->query('select transid from king_orders where id = ? ',$imei['order_id'])->row()->transid;
				$grn = 	'<a href="'.site_url('admin/viewgrn/'.$imei['grn_id']).'">'.$imei['grn_id'].'</a>' ;
				$tbl_data_html .= '<tr>
					<td>'.($i+$pg+1).'</td>
					<td>'.($imei['imei_no']).'</td>
					<td>'.$grn.'</td>
					<td>'.($imei['status']?anchor('admin/trans/'.$imei['trans_id'],$imei['order_id']):'In-Stock').'</td>
					<td>'.format_datetime_ts($imei['created_on']).'</td>
				</tr>';
			}
		}else
		{
			$tbl_data_html .= '<tr><td colspan="6"><div align="center"> No data found</div></td></tr>';
		}
		
		$this->load->library('pagination');
		$config['base_url'] = site_url('admin/jx_stockimeilist/'.$pid);
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $limit;
		$config['uri_segment'] = 4;
		
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$pagi_links = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		
		$pagi_links = '<div class="log_pagination">'.$pagi_links.'</div>';
		
		echo json_encode(array('imei_data'=>$tbl_data_html,'imei_pagi_links'=>$pagi_links,'imei_ttl'=>$total_rows,'limit'=>$limit));
	}
	/**
	 * function to update mrp 
	 * @param unknown_type $prods 
	 * 
	 * @return total products updated
	 */
	function _update_product_mrp($prods)
	{
		$user = $this->erpm->auth();
		$c=0;
		foreach($prods as $i=>$pdet_arr)
		{
				$pid = $pdet_arr['product_id']; 
				$mrp= $pdet_arr['mrp']; 
				if(empty($mrp))
					continue;
				$c++;
				$pc_prod=$this->db->query("select * from m_product_info where product_id=? and mrp!=?",array($pid,$mrp))->row_array();
				if(!empty($pc_prod))
				{
					$inp=array("product_id"=>$pid,"new_mrp"=>$mrp,"old_mrp"=>$pc_prod['mrp'],"reference_grn"=>0,"created_by"=>$user['userid'],"created_on"=>time());
					$this->db->insert("product_price_changelog",$inp);
					$this->db->query("update m_product_info set mrp=? where product_id=? limit 1",array($mrp,$pid));
					foreach($this->db->query("select product_id from products_group_pids where group_id in (select group_id from products_group_pids where product_id=$pid) and product_id!=$pid")->result_array() as $pg)
					{
						$inp=array("product_id"=>$pg['product_id'],"new_mrp"=>$mrp,"old_mrp"=>$this->db->query("select mrp from m_product_info where product_id=?",$pg['product_id'])->row()->mrp,"reference_grn"=>0,"created_by"=>$user['userid'],"created_on"=>time());
						$this->db->insert("product_price_changelog",$inp);
						$this->db->query("update m_product_info set mrp=? where product_id=? limit 1",array($mrp,$pg['product_id']));
					}
					$r_itemids=$this->db->query("select itemid from m_product_deal_link where product_id=?",$pid)->result_array();
					$r_itemids2=$this->db->query("select l.itemid from products_group_pids p join m_product_group_deal_link l on l.group_id=p.group_id where p.product_id=?",$pid)->result_array();
					
					
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
					
					
					
					foreach($r_itemids as $d)
					{
						$itemid=$d['itemid'];
						$item=$this->db->query("select orgprice,price from king_dealitems where id=?",$itemid)->row_array();
						$o_price=$item['price'];$o_mrp=$item['orgprice'];
						$n_mrp=$this->db->query("select ifnull(sum(p.mrp*l.qty),0) as mrp from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$itemid)->row()->mrp+$this->db->query("select ifnull(sum((select avg(mrp) from m_product_group_deal_link l join products_group_pids pg on pg.group_id=l.group_id join m_product_info p on p.product_id=pg.product_id where l.itemid=$itemid)*(select qty from m_product_group_deal_link where itemid=$itemid)),0) as mrp")->row()->mrp;
						$n_price=$pc_prod['is_serial_required']?$item['price']:$item['price']/$o_mrp*$n_mrp;
						$inp=array("itemid"=>$itemid,"old_mrp"=>$o_mrp,"new_mrp"=>$n_mrp,"old_price"=>$o_price,"new_price"=>$n_price,"created_by"=>$user['userid'],"created_on"=>time(),"reference_grn"=>0);
						$r=$this->db->insert("deal_price_changelog",$inp);
						
						// Disable special margin if set 
						$this->db->query("update pnh_special_margin_deals set is_active = 0 where i_price != ? and itemid = ? and is_active = 1 " ,array($n_price,$itemid));
						
						$this->db->query("update king_dealitems set orgprice=?,price=? where id=? limit 1",array($n_mrp,$n_price,$itemid));
						if($this->db->query("select is_pnh as b from king_dealitems where id=?",$itemid)->row()->b)
						{
							$o_s_price=$this->db->query("select store_price from king_dealitems where id=?",$itemid)->row()->store_price;
							//$n_s_price=$o_s_price/$o_mrp*$n_mrp;
							$n_s_price=$pc_prod['is_serial_required']?$o_s_price:$o_s_price/$o_mrp*$n_mrp;
							$this->db->query("update king_dealitems set store_price=? where id=? limit 1",array($n_s_price,$itemid));
							$o_n_price=$this->db->query("select nyp_price as p from king_dealitems where id=?",$itemid)->row()->p;
							$n_n_price=$pc_prod['is_serial_required']?$o_n_price:$o_n_price/$o_mrp*$n_mrp;
							$this->db->query("update king_dealitems set nyp_price=? where id=? limit 1",array($n_n_price,$itemid));
						}
						foreach($this->db->query("select * from partner_deal_prices where itemid=?",$itemid)->result_array() as $r)
						{
							$o_c_price=$r['customer_price'];
							//$n_c_price=$o_c_price/$o_mrp*$n_mrp;
							$n_c_price=$pc_prod['is_serial_required']?$o_c_price:$o_c_price/$o_mrp*$n_mrp;
							$o_p_price=$r['partner_price'];
							//$n_p_price=$o_p_price/$o_mrp*$n_mrp;
							$n_p_price=$pc_prod['is_serial_required']?$o_p_price:$o_p_price/$o_mrp*$n_mrp;
							$this->db->query("update partner_deal_prices set customer_price=?,partner_price=? where itemid=? and partner_id=?",array($n_c_price,$n_p_price,$itemid,$r['partner_id']));
						}
					}
				}
		}
		return $c;
	}
	
	
	function editproduct($pid=false)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		if($_POST){
			foreach(array('pname','sku_code',"pdesc","psize","puom","pmrp","pvat","pcost","pbarcode","pisoffer","pissrc","pbrand","prackbin","pmoq","prorder","prqty","premarks","pissno") as $i)
				$inp[]=$this->input->post($i);
			$inp[]=$pid;
			
			
			$prod = array();
			$prod[] = array('product_id'=>$pid,'mrp'=>$_POST['pmrp']);
			
			$this->_update_product_mrp($prod);
			
			$this->db->query("update m_product_info set product_name=?,sku_code=?,short_desc=?,size=?,uom=?,mrp=?,vat=?,purchase_cost=?,barcode=?,is_offer=?,is_sourceable=?,brand_id=?,default_rackbin_id=?,moq=?,reorder_level=?,reorder_qty=?,remarks=?,modified_on=now(),is_serial_required=? where product_id=? limit 1",$inp);
			$t_inp=array("product_id"=>$pid,"is_sourceable"=>$this->input->post("pissrc"),"created_on"=>time(),"created_by"=>$user['userid']);
			$this->db->insert("products_src_changelog",$t_inp);
			
			redirect("admin/product/$pid");
		}
		$data['prod']=$this->db->query("select * from m_product_info where product_id=?",$pid)->row_array();
		$data['page']="addproduct";
		$this->load->view("admin",$data);
	}
	
	function addproduct()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		if($_POST)
		{
			$inp=array("P".rand(10000,99999));
			foreach(array('pname','sku_code',"pdesc","psize","puom","pmrp","pvat","pcost","pbarcode","pisoffer","pissrc","pbrand","prackbin","pmoq","prorder","prqty","premarks","pissno") as $i)
				$inp[]= $$i = $this->input->post($i);
				
			$inp[] = $user['userid'];	
			$this->db->query("insert into m_product_info(product_code,product_name,sku_code,short_desc,size,uom,mrp,vat,purchase_cost,barcode,is_offer,is_sourceable,brand_id,default_rackbin_id,moq,reorder_level,reorder_qty,remarks,is_serial_required,created_on,created_by)
																					values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now(),?)",$inp);
			$pid=$this->db->insert_id();
			$rackbin=1;$location=10;
			$raw_rackbin=$this->db->query("select l.location_id as default_location_id,l.id as default_rack_bin_id from m_rack_bin_brand_link b join m_rack_bin_info l on l.id=b.rack_bin_id where b.brandid=?",$this->input->post("pbrand"))->row_array();
			if(!empty($raw_rackbin))
			{
				$rackbin=$raw_rackbin['default_rack_bin_id'];
				$location=$raw_rackbin['default_location_id'];
			}
			$this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,available_qty,product_barcode) values(?,?,?,?,0,?)",array($pid,$location,$rackbin,$pmrp,$pbarcode));
			
			
			
			redirect("admin/products");
		}
		$data['page']="addproduct";
		$this->load->view("admin",$data);
	}
	
	function createblankstockrows()
	{
		$sql="select p.product_id,p.mrp,r.default_location_id as loc,r.default_rack_bin_id as rack from m_product_info p left outer join m_brand_location_link r on r.brand_id=p.brand_id left outer join t_stock_info s on s.product_id=p.product_id group by p.product_id having count(s.product_id)=0";
		$prods=$this->db->query($sql)->result_array();
		
		foreach($prods as $p)
			$this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,product_barcode,available_qty) values(?,?,?,?,0)",array($p['product_id'],$p['loc'],$p['rack'],$p['mrp'],''));
	}
	
	function resetadminuserpass($uid=false)
	{
		$user=$this->auth(true);
		$admin=$this->db->query("select * from king_admin where id=?",$uid)->row_array();
		if(!$uid || empty($admin))
			show_error("UID is Missing or not available in database");
		$email=$admin['email'];
		$name=$admin['name'];
		$username=$admin['username'];
		$password=randomChars(6);
		$this->db->query("update king_admin set password=md5(?) where id=? limit 1",array($password,$uid));
		$this->vkm->email($email,"Your Snapittoday ERP account update","Hi $name,<br><br>Your ERP account password has been changed<br><br>Username : $username<br>Password : $password<br><br>ERP Team Snapittoday");
		$this->session->set_flashdata("erp_pop_info","Password reset for $name");	
		redirect("admin/adminusers");
	}
	
	
	function editadminuser($uid)
	{
		$user=$this->auth(true);
		if($_POST)
			$this->erpm->do_updateadminuser($uid);
		$data['auser']=$this->db->query("select * from king_admin where id=? ",$uid)->row_array();
		$data['roles']=$this->db->query("select * from user_access_roles order by value asc")->result_array();
		$data['page']="addadminuser";
		$this->load->view("admin",$data);
	}
	
	function addadminuser()
	{
		$user=$this->auth(true);
		if($_POST)
			$this->erpm->do_addadminuser();
		$data['roles']=$this->db->query("select * from user_access_roles order by id asc")->result_array();
		$data['page']="addadminuser";
		$this->load->view("admin",$data);
	}
	
	function adminusers()
	{
		$user=$this->auth(true);
		$data['page']="adminusers";
		$data['users']=$this->db->query("select * from king_admin order by name asc")->result_array();
		$data['roles']=$this->db->query("select * from user_access_roles order by id asc")->result_array();
		$this->load->view("admin",$data);
	}
	
	function roles()
	{
		$user=$this->auth(ADMINISTRATOR_ROLE);
		$data['roles']=$this->db->query("select * from user_access_roles order by id asc")->result_array();
		$data['page']="roles";
		$this->load->view("admin",$data);
	}
	
	function addbrand()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE|PRODUCT_MANAGER_ROLE);
		if($_POST)
			$this->erpm->do_addbrand();
		$data['page']="editbrand";
		$data['brand']=array("name"=>"");
		$data['rbs']=array();
		$data['rackbins']=$this->db->query("select * from m_rack_bin_info order by rack_name asc")->result_array();
		$this->load->view("admin",$data);
	}
	
	function viewbrand($bid=false)
	{
		$user=$this->auth(DEAL_MANAGER_ROLE|PRODUCT_MANAGER_ROLE);
		$data['page']="viewbrand";
		$data['brand']=$this->db->query("select * from king_brands where id=?",$bid)->row_array();
		$data['rbs']=$this->db->query("select r.* from m_rack_bin_brand_link rb join m_rack_bin_info r on r.id=rb.rack_bin_id where rb.brandid=?",$bid)->result_array();
		$data['products']=$this->erpm->getproductsforbrand($bid);
		$data['deals']=$this->erpm->getdealsforbrand($bid);
		$data['vendors']=$this->erpm->getvendorsforbrand($bid);
		$this->load->view("admin",$data);
	}
	
	function editbrand($bid=false)
	{
		$user=$this->auth(DEAL_MANAGER_ROLE|PRODUCT_MANAGER_ROLE);
		if(empty($bid))
			show_404();
		if($_POST)
			$this->erpm->do_editbrand($bid);
		$data['rackbins']=$this->db->query("select * from m_rack_bin_info order by rack_name asc")->result_array();
		$data['rbs']=$this->erpm->getrackbinsforbrand($bid);
		$data['page']="editbrand";
		$data['brand']=$this->db->query("select * from king_brands where id=?",$bid)->row_array();
		$this->load->view("admin",$data);
	}
	
	function viewcat($cat=false)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|DEAL_MANAGER_ROLE);
		if(empty($cat))
			show_404();
		$data['cat']=$this->db->query("select c.*,m.name as main from king_categories c left outer join king_categories m on m.id=c.type where c.id=?",$cat)->row_array();
		$data['deals']=$this->db->query("select i.*,c.name as category,c.id as catid from king_categories c join king_deals d on d.catid=c.id join king_dealitems i on i.dealid=d.dealid where c.id=? or c.type=?",array($cat,$cat))->result_array();
		$data['page']="viewcat";
		$this->load->view("admin",$data);
	}
	
	function addcat()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|DEAL_MANAGER_ROLE);
		if($_POST)
			$this->erpm->do_addnewcat();
		$data['page']="addcategory";
		$this->load->view("admin",$data);
	}
	
	function editcat($catid=false)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|DEAL_MANAGER_ROLE);
		if(empty($catid))
			show_404();
		if($_POST)
			$this->erpm->do_updatecat($catid);
		$data['cat']=$this->db->query("select * from king_categories where id=?",$catid)->row_array();
		$data['page']="addcategory";
		$this->load->view("admin",$data);
	}
	
	function dashboard()
	{
		$user=$this->auth();
		$data['page']="dashboard";
		$this->load->view("admin",$data);
	}
	
	function export_data()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|DEAL_MANAGER_ROLE);
		if($_POST)
			$this->erpm->do_export_data();
		$data['page']="export_data";
		$this->load->view("admin",$data);
	}
	
	function jx_pub_deal()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if(!$_POST)
			die;
		$this->db->query("update king_deals set publish=? where dealid=? limit 1",array($this->input->post("pub"),$this->input->post("did")));
	}
	
	function jx_live_deal()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if(!$_POST)
			die;
		$this->db->query("update king_dealitems set live=? where id=? limit 1",array($this->input->post("live"),$this->input->post("id")));
	}
	
	function deals_bulk_image_update($bid)
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$data['items']=$this->db->query("select i.name,i.dealid,d.publish,i.live,b.* from deals_bulk_upload_items b join king_dealitems i on i.id=b.item_id join king_deals d on d.dealid=i.dealid where b.bulk_id=?",$bid)->result_array();
		$data['page']="deals_bulk_image_update";
		$this->load->view("admin",$data);
	}
	
	function bu_img_update()
	{
		$user=$this->auth();
		$i=$this->input->post("i");
		$iid=$this->input->post("itemid");
		$imgname=randomChars(15);
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
				$did=$this->db->query("select dealid from king_dealitems where id=?",$iid)->row()->dealid;
				$this->db->query("update king_dealitems set pic=? where id=? limit 1",array($imgname,$iid));
				$this->db->query("update king_deals set pic=? where dealid=? limit 1",array($imgname,$did));
				$this->db->query("update deals_bulk_upload_items set is_image_updated=1,updated_on=".time().",updated_by={$user['userid']} where item_id=?",$iid);
				$bid=$this->db->query("select bulk_id from deals_bulk_upload_items where item_id=?",$iid)->row()->bulk_id;
				if($this->db->query("select 1 from deals_bulk_upload_items where bulk_id=? and is_image_updated=0",$bid)->num_rows()==0)
					$this->db->query("update deals_bulk_upload set is_all_image_updated=1 where id=? limit 1",$bid);
				$err=0;
			}
			else $err=1;
		}
		else
			$err=1;
		echo "<script>parent.updatedimg($i,$err)</script>";
	}
	
	function dealsbymenu_table($mid)
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$data['deals']=$this->db->query("select d.pic,b.name as brand,c.name as category,m1.name as menu1,m2.name as menu2,i.id,d.dealid,i.name,i.orgprice as mrp,i.price,d.publish,i.live from king_deals d join king_dealitems i on i.dealid=d.dealid join king_categories c on c.id=d.catid join king_brands b on b.id=d.brandid join king_menu m1 on m1.id=d.menuid left outer join king_menu m2 on m2.id=d.menuid2 where d.menuid=? or d.menuid2=? order by i.name asc",array($mid,$mid))->result_array();
		$data['pagetitle']="menu : ".$this->db->query("select name from king_menu where id=?",$mid)->row()->name;
		$data['page']="deals_table";
		$this->load->view("admin",$data);
	}
	
	function dealsbycategory_table($cid)
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$data['deals']=$this->db->query("select d.pic,b.name as brand,c.name as category,m1.name as menu1,m2.name as menu2,i.id,d.dealid,i.name,i.orgprice as mrp,i.price,d.publish,i.live from king_deals d join king_dealitems i on i.dealid=d.dealid join king_categories c on c.id=d.catid join king_brands b on b.id=d.brandid join king_menu m1 on m1.id=d.menuid left outer join king_menu m2 on m2.id=d.menuid2 where d.catid=? order by i.name asc",array($cid))->result_array();
		$data['pagetitle']="Category : ".$this->db->query("select name from king_categories where id=?",$cid)->row()->name;
		$data['page']="deals_table";
		$this->load->view("admin",$data);
	}
	
	function dealsbybrand_table($cid)
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$data['deals']=$this->db->query("select d.pic,b.name as brand,c.name as category,m1.name as menu1,m2.name as menu2,i.id,d.dealid,i.name,i.orgprice as mrp,i.price,d.publish,i.live from king_deals d join king_dealitems i on i.dealid=d.dealid join king_categories c on c.id=d.catid join king_brands b on b.id=d.brandid join king_menu m1 on m1.id=d.menuid left outer join king_menu m2 on m2.id=d.menuid2 where d.brandid=? order by i.name asc",array($cid))->result_array();
		$data['pagetitle']="Brand : ".$this->db->query("select name from king_brands where id=?",$cid)->row()->name;
		$data['page']="deals_table";
		$this->load->view("admin",$data);
	}
	
	function deals_table()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$data['deals']=$this->db->query("select d.pic,b.name as brand,c.name as category,m1.name as menu1,m2.name as menu2,i.id,d.dealid,i.name,i.orgprice as mrp,i.price,d.publish,i.live from king_deals d join king_dealitems i on i.dealid=d.dealid join king_categories c on c.id=d.catid join king_brands b on b.id=d.brandid join king_menu m1 on m1.id=d.menuid left outer join king_menu m2 on m2.id=d.menuid2 order by d.sno desc limit 40")->result_array();
		$data['page']="deals_table";
		$this->load->view("admin",$data);
	}
	
	function categories()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|DEAL_MANAGER_ROLE);
		$cats=$this->db->query("select * from king_categories order by name asc")->result_array();
		
		$as=$alphas=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		foreach($as as $alpha)
			$ret[$alpha]=array();
		foreach($cats as $c)
		{
			$alpha=strtolower($c['name']{0});
			if(!isset($ret[$alpha]))
			{
				$ret[$alpha]=array();
				$alphas[]=$alpha;
			}
			$ret[$alpha][]=$c;
		}
		for($i=0;$i<5;$i++)
			$r[$i]=array();
			
		$i=0;
		foreach($ret as $a=>$rt)
		{
			$r[$i][$a]=$rt;
			$i++;
			if($i>4)
				$i=0;
		}
		$data['count']=count($cats);
		$data['alphas']=$alphas;
		$data['categories']=$r;
		$data['page']="categories";
		$this->load->view("admin",$data);
	}
	
	function brands()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE|PRODUCT_MANAGER_ROLE);
		$brands=$this->db->query("select * from king_brands order by name asc")->result_array();
		
		$as=$alphas=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		foreach($as as $alpha)
			$ret[$alpha]=array();
		foreach($brands as $c)
		{
			if(!isset($c['name']{0}))
				continue;
			$alpha=strtolower($c['name']{0});
			if(!isset($ret[$alpha]))
			{
				$ret[$alpha]=array();
				$alphas[]=$alpha;
			}
			$ret[$alpha][]=$c;
		}
		for($i=0;$i<5;$i++)
			$r[$i]=array();
			
		$i=0;
		foreach($ret as $a=>$rt)
		{
			$r[$i][$a]=$rt;
			$i++;
			if($i>4)
				$i=0;
		}
		$data['count']=count($brands);
		$data['alphas']=$alphas;
		$data['brands']=$r;
		$data['rbs']=$this->erpm->getrackkbinsforbrands();
		$data['page']="brands";
		$this->load->view("admin",$data);
	}
	
	function vendorsbybrand($bid=false)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|DEAL_MANAGER_ROLE|PURCHASE_ORDER_ROLE);
		if(!$bid)
			show_404();
		$data['page']="erpvendors";
		$data['brand']=$this->db->query("select name from king_brands where id=?",$bid)->row()->name;
		$data['vendors']=$this->erpm->getvendorsforbrand($bid);
		$this->load->view("admin",$data);
	}
	
	function vendors()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|DEAL_MANAGER_ROLE|PURCHASE_ORDER_ROLE);
		$data['page']="erpvendors";
		$data['vendors']=$this->erpm->getvendors();
		$this->load->view("admin",$data);
	}
	
	function changeusertemp($userid,$temp)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		$this->db->query("update king_users set temperament=? where userid=?",array($temp,$userid));
		redirect("admin/user/$userid");
	}
	
	function client_invoices($s=false,$e=false)
	{
		$user=$this->auth(FINANCE_ROLE);
		if($e)
			$data['pagetitle']="between $s and $e";
		$data['invoices']=$this->erpm->getclientinvoices($s,$e);
		$data['page']="client_invoices";
		$this->load->view("admin",$data);
	}
	
	function print_client_invoice($inv)
	{
		$user=$this->auth(FINANCE_ROLE);
		$data['invoice']=$this->erpm->getclientinvoice($inv);
		$data['orders']=$this->erpm->getclientinvoiceforprint($inv);
		$this->load->view("admin/body/client_invoice_print",$data);
	}
	
	function pack_client_invoice($inv)
	{
		$user=$this->auth(FINANCE_ROLE);
		if($_POST)
		{
			$this->db->query("update t_client_invoice_info set invoice_status=1 where invoice_id=?",$inv);
			redirect("admin/client_invoice/$inv");
		}
		$data['invoice']=$this->db->query("select p.barcode,p.product_name,t.*,t.invoice_qty as qty from t_client_invoice_product_info t join m_product_info p on p.product_id=t.product_id where t.invoice_id=?",$inv)->result_array();
		$data['page']="pack_client_invoice";
		$this->load->view("admin",$data);
	}
	
	function payment_client_invoice($inv=false)
	{
		if(!$inv)
			show_404();
		$user=$this->auth(FINANCE_ROLE);
		if($_POST)
			$this->erpm->do_payment_client($inv);
		$data['page']="payment_client";
		$this->load->view("admin",$data);
	}
	
	function client_invoice($iid=false)
	{
		$user=$this->auth(FINANCE_ROLE);
		if(!$iid)
			show_404();
		$data['payments']=$this->db->query("select * from t_client_invoice_payment where invoice_id=?",$iid)->result_array();
		$data['invoice']=$this->db->query("select i.*,a.name as created_by from t_client_invoice_info i join king_admin a on a.id=i.created_by where i.invoice_id=?",$iid)->row_array();
		$data['items']=$this->db->query("select p.product_name,i.* from t_client_invoice_product_info i join m_product_info p on p.product_id=i.product_id where i.invoice_id=?",$iid)->result_array();
		$data['page']="client_invoice";
		$this->load->view("admin",$data);
	}
	
	function createclientinvoice()
	{
		$user=$this->auth(FINANCE_ROLE);
		if($_POST)
			$this->erpm->do_clientinvoice();
		$data['clients']=$this->erpm->getclients();
		$data['page']="createclientinvoice";
		$this->load->view("admin",$data);
	}
	
	function jx_listclientordersforinvoice()
	{
		$user=$this->auth();
		if(!$_POST)
			die;
		$cid=$this->input->post("cid");
		foreach($this->db->query("select * from t_client_order_info where client_id=? and (order_status=0 || order_status=1)",$cid)->result_array() as $o)
			echo '<a href="javascript:void(0);" onclick="loadorder(\''.$o['order_id'].'\')">'."ORD{$o['order_id']}, {$o['order_reference_no']}".'</a>';
	}
	
	function jx_loadclientordersforinvoice()
	{
		$user=$this->auth();
		if(!$_POST)
			die;
		$oid=$this->input->post("oid");
		$items=$this->erpm->getclientordersforinvoice($oid);
		echo json_encode($items);
	}
	
	function client_order($oid=false)
	{
		$user=$this->auth(FINANCE_ROLE);
		if($_POST)
			$this->erpm->do_closeclientorder($oid);
		$data['order']=$this->erpm->getclientorder($oid);
		$data['invoices']=$this->erpm->getinvoicesforclientorder($oid);
		$data['page']="client_order";
		$this->load->view("admin",$data);
	}
	
	function client_orders($s=false,$e=false)
	{
		$user=$this->auth(FINANCE_ROLE);
		$data['orders']=$this->erpm->getclientorders();
		$data['page']="client_orders";
		$this->load->view("admin",$data);
	}
	
	function client_orders_by_client($cid=0)
	{
		$user=$this->auth(FINANCE_ROLE);
		$data['orders']=$this->erpm->getclientordersbyclient($cid);
		$data['byclient']=true;
		$data['pagetitle']="for ".$this->db->query("select client_name as c from m_client_info where client_id=?",$cid)->row()->c;
		$data['page']="client_orders";
		$this->load->view("admin",$data);
	}
	
	
	function clients()
	{
		$user=$this->auth(FINANCE_ROLE);
		$data['clients']=$this->erpm->getclients();
		$data['page']="clients";
		$this->load->view("admin",$data);
	}
	
	function addclientorder($cid='')
	{
		$user=$this->auth(FINANCE_ROLE);
		 
		if($_POST)
		{
			$cid = $this->input->post('cid');
			$this->erpm->do_addclientorder($cid);
		}
			
			
		$data['cid'] = $cid; 	
		$data['page']="addclientorder";
		$this->load->view("admin",$data);
	}
	
	function editclient($cid)
	{
		$user=$this->auth(FINANCE_ROLE);
		if($_POST)
			$this->erpm->do_updateclient($cid);
		$data['client']=$this->db->query("select * from m_client_info where client_id=?",$cid)->row_array();
		$data['contacts']=$this->db->query("select * from m_client_contacts_info where client_id=?",$cid)->result_array();
		$data['page']="addclient";
		$this->load->view("admin",$data);
	}
	
	function addclient()
	{
		$user=$this->auth(FINANCE_ROLE);
		if($_POST)
			$this->erpm->do_addclient();
		$data['page']="addclient";
		$this->load->view("admin",$data);
	}
	
	function stock_checker()
	{
		$user=$this->auth();
		if($_POST)
		{
			$data['deal']=$this->db->query("select name,id from king_dealitems where id=?",$_POST['id'])->row_array();
			$avail=$this->erpm->do_stock_check(array($data['deal']['id']));
			$data['status']=1;
			if(empty($avail))
				$data['status']=0;
		}
		$data['page']="stock_checker";
		$this->load->view("admin",$data);
	}
	
	function update_partner_deal_prices($itemid)
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if($_POST)
		{
			foreach(array("partner_id","customer_price","partner_price") as $i)
				$$i=$this->input->post($i);
			foreach($partner_id as $i=>$pid)
			{
				$inp=array("partner_id"=>$pid,"itemid"=>$itemid,"customer_price"=>$customer_price[$i],"partner_price"=>$partner_price[$i]);
				if($this->db->query("select 1 from partner_deal_prices where itemid=? and partner_id=?",array($itemid,$pid))->num_rows()==0)
				{
					$inp['created_on']=time();$inp['created_by']=$user['userid'];
					$this->db->insert("partner_deal_prices",$inp);
				}else{
					$inp['modified_on']=time();
					$inp['modified_by']=$user['userid'];
					$this->db->where("partner_id",$inp['partner_id']);
					$this->db->where("itemid",$inp['itemid']);
					$this->db->update("partner_deal_prices",$inp);
				}
			}
			$this->erpm->flash_msg("Partner prices updated");
			redirect("admin/update_partner_deal_prices/$itemid");
		}
		$data['itemid']=$itemid;
		$data['page']="update_partner_deal_prices";
		$this->load->view("admin",$data);
	}
	
	function partner_deal_prices()
	{
		$user=$this->auth(MANAGE_PARTNER_DEAL_PRICES);
		if($_POST)
		{
			$f=fopen($_FILES['pfile']['tmp_name'],"r");
			$head=fgetcsv($f);
			$out=array($head);
			$c=0;
			$template=array("partner_id","itemid","customer_price","partner_price");
			while(($data=fgetcsv($f))!==false)
			{
				foreach($template as $i=>$c)
					$inp[$c]=$data[$i];
				if($this->db->query("select 1 from partner_deal_prices where itemid=? and partner_id=?",array($data[1],$data[0]))->num_rows()==0)
				{
					$inp['created_on']=time();$inp['created_by']=$user['userid'];
					$this->db->insert("partner_deal_prices",$inp);
				}else{
					$inp['modified_on']=time();
					$inp['modified_by']=$user['userid'];
					$this->db->where("partner_id",$inp['partner_id']);
					$this->db->where("itemid",$inp['itemid']);
					$this->db->update("partner_deal_prices",$inp);
				}
				$c++;
			}
			$this->erpm->flash_msg("$c deal prices updated");
		}
		$data['page']="partner_deal_prices";
		$this->load->view("admin",$data);
	}
	
	
	function search()
	{
		$user=$this->auth();
		if(strlen($this->input->post("q"))<3)
			show_error("Please enter atleast 3 characters to search");
		$eq=$this->input->post("q");
		$q="%".$this->input->post("q")."%";
		$data['categories']=$this->db->query("select * from king_categories where name like ?",$q)->result_array();
		$data['brands']=$this->db->query("select * from king_brands where name like ?",$q)->result_array();
		$data['deals']=$this->db->query("select name,dealid,id from king_dealitems where is_pnh=0 and name like ?",$q)->result_array();
		$data['products']=$prods=$this->db->query("select product_name,product_id from m_product_info where product_name like ?",$q)->result_array();
		foreach($prods as $i=>$p)
			$prods[$i]['stock']=$this->db->query("select sum(available_qty) as s from t_stock_info where product_id=?",$p['product_id'])->row()->s;
		$data['products']=$prods;
		$data['invoices']=$this->db->query("select * from king_invoice where invoice_no like ?",$q)->result_array();
		$data['orders']=$this->db->query("select transid from king_transactions where (transid like ? or partner_reference_no like ? ) ",array($q,$q))->result_array();
		$data['clients']=$this->db->query("select client_id,client_name from m_client_info where client_name like ?",$q)->result_array();
		$data['users']=$this->db->query("select * from king_users where email like ? or name like ? or (mobile=? and mobile<>0) order by name asc",array($q,$q,$this->input->post("q")))->result_array();
		$data['tickets']=$this->db->query("select * from support_tickets where ticket_no like ? or concat('TK',ticket_no) like ?",array($q,$q))->result_array();
		$data['vendors']=$this->db->query("select * from m_vendor_info where vendor_name like ? or vendor_code like ?",array($q,$q))->result_array();
		$data['awbs']=$this->db->query("select b.*,o.transid from shipment_batch_process_invoice_link b join king_invoice o on o.invoice_no=b.invoice_no where b.awb=? limit 1",$this->input->post("q"))->result_array();
		$data['pnh_deals']=$this->db->query("select name,id,pnh_id from king_dealitems where is_pnh=1 and (name like ? or (pnh_id=? and pnh_id!='0'))",array($q,$this->input->post("q")))->result_array();
		$data['pnh_franchises']=$this->db->query("select franchise_id,pnh_franchise_id,franchise_name from pnh_m_franchise_info where pnh_franchise_id=? or franchise_name like ? or login_mobile1=? or login_mobile2=?",array($eq,$q,$eq,$eq))->result_array();
		$data['pnh_members']=$this->db->query("select concat(first_name,' ',last_name) as name,pnh_member_id,user_id from pnh_member_info where pnh_member_id=? or  mobile = ? or  concat(first_name,' ',last_name) like ?",array($eq,$eq,$q))->result_array();
		$data['page']="search_results";
		$this->load->view("admin",$data);
	}
	
	function pnh_jx_checkstock_order()
	{
		$fid=$this->input->post("fid");
		$mid=$this->input->post("mid");
		$pids=explode(",",$this->input->post("pids"));
		$qty=explode(",",$this->input->post("qty"));
		$iids=$this->db->query("select id,pnh_id from king_dealitems where is_pnh=1 and pnh_id in('".implode("','",$pids)."')")->result_array();
		$itemids=array();
		$order_det=array();
		$e=0;
		foreach($pids as $pid)
			foreach($iids as $id)
				if($id['pnh_id']==$pid)
					$itemids[]=$id['id'];
		$avail=$this->erpm->do_stock_check($itemids,$qty);
		$un="";
		$attr=$this->input->post('attr');
		$attr_data=array();
		if($attr)
		{
			$attrs=explode("&",$attr);
			foreach($attrs as $attr)
			{
				list($pp,$v)=explode("=",$attr);
				list($p,$a)=explode("_",$pp);
				if(!isset($attr_data[$p]))
					$attr_data[$p]=array();
				$attr_data[$p][$a]=$v;
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
					$e=1;
					$un.="{$pid} is not available for selected combination";
					break;
				}
			}
		}
		if($e==0)
		{
		foreach($itemids as $i=>$itemid)
		 if(!in_array($itemid,$avail))
		 	$un.="{$pids[$i]} is out of stock\n";
		 $e=0;
		 if(strlen($un)!=0)
		 	$e=1;
		}
		$total=$d_total=$bal=$abal=0;
		$pc="";
		if($e==0 && $mid && $this->db->query("select 1 from pnh_member_info where pnh_member_id=?",$mid)->num_rows()==0 && $this->db->query("select 1 from pnh_m_allotted_mid where franchise_id=? and ? between mid_start and mid_end",array($fid,$mid))->num_rows()==0)
		{
			$e=1;$un="MID : $mid is not allotted to this franchise";
		}
		if($e==0)
		{
			$iids=array();
			$itemid=$this->db->query("select id from king_dealitems where pnh_id=?",$pid)->row()->id;
			$menuid=$this->db->QUERY("select *,d.menuid,m.default_margin as margin from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN pnh_menu m ON m.id=d.menuid where i.is_pnh=1 and i.pnh_id=?",$pid)->row_array();
		 	$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
		 	$fran1=$this->db->query("select * from pnh_franchise_menu_link where fid=? and menuid=?",array($fid,$menuid['menuid']))->row_array();
		 	$margin=$this->db->query("select margin,combo_margin from pnh_m_class_info where id=?",$fran['class_id'])->row_array();
			
			if($fran1['sch_discount_start']<time() && $fran1['sch_discount_end']>time() && $fran1['is_sch_enabled'])
				$menuid['margin']+=$fran1['sch_discount'];
				
		 	$ordered_menu_list=array();
		 	foreach($pids as $i=>$iid)
		 	{
				$prod=$this->db->query("select i.*,d.publish,d.menuid,d.brandid,d.catid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.is_pnh=1 and i.pnh_id=?",$iid)->row_array();
				$ordered_menu_list[]=$prod['menuid'];
				$items[$i]['brandid']=$prod['brandid'];
				$items[$i]['menuid']=$prod['menuid'];
				$items[$i]['catid']=$prod['catid'];
				$items[$i]['name']=$prod['name'];
				$items[$i]['tax']=$prod['tax'];
				$items[$i]['mrp']=$prod['orgprice'];
				$items[$i]['price']=$prod['price'];
				$items[$i]['itemid']=$prod['id'];
				$margin=$this->erpm->get_pnh_margin($fran['franchise_id'],$iid);
				$items[$i]['base_margin']=$margin['base_margin'];
				$items[$i]['sch_margin']=$margin['sch_margin'];
				$items[$i]['bal_discount']=$margin['bal_discount'];
				if($prod['is_combo']=="1")
				{
					$items[$i]['discount']=$items[$i]['price']/100*$margin['combo_margin'];
					$items[$i]['base_margin']=$margin['combo_margin'];
				}
				else
					$items[$i]['discount']=$items[$i]['price']/100*$margin['margin'];
					$total+=$items[$i]['price']*$qty[$i];
					$items[$i]['qty']=$qty[$i];
					$d_total+=($items[$i]['price']-$items[$i]['discount'])*$qty[$i];
					$items[$i]['final_price']=($items[$i]['price']-$items[$i]['discount']);
					
					$iids[]=$prod['id'];
		 	}
		 	
		 	$fran_crdet = $this->erpm->get_fran_availcreditlimit($fid);
			$fran['current_balance'] = $fran_crdet[3];
			
		 	$bal=$fran['current_balance'];
		 	$abal=$fran['current_balance']-$d_total;
			
		 	//check if it is prepaid franchise block
		 	$is_prepaid_franchise=$this->erpm->is_prepaid_franchise($fid);
		 	if($is_prepaid_franchise)
		 	{
		 		if(count(array_unique($ordered_menu_list))==1)
		 		{
		 			if($ordered_menu_list[0]!=VOUCHERMENU)
		 				$is_prepaid_franchise=false;
		 						
		 		}else{
		 			$is_prepaid_franchise=false;
		 		}
		 	}
		 	//check if it is prepaid franchise block
			
			if($fran['current_balance']<$d_total && !$is_prepaid_franchise)
			{
				$e=1;$un="Insufficient balance! Balance in your account Rs {$fran['current_balance']}\nTotal order amount : Rs $d_total";
			}
			$pc_data['deals']=$this->erpm->pnh_getdealpricechanges($fran['app_version'],$iids);
			$pc_data['total']=$total;
			$pc_data['mid']=$mid;
			$pc_data['items']=$items;
			$pc_data['menuid']=$menuid;
			$pc_data['fid']=$fid;
			
			
			$pc=$this->load->view("admin/body/pc_offline_frag",$pc_data,true);

		}
		 die(json_encode(array("e"=>$e,"msg"=>$un,"total"=>$total,"d_total"=>$d_total,"com"=>$total-$d_total,"bal"=>$bal,"abal"=>$abal,"pc"=>$pc)));
	}
	
	function pnh_fran_ver_change($fid,$v)
	{
		$user=$this->auth(true);
		$this->db->query("update pnh_m_franchise_info set app_version=? where franchise_id=? limit 1",array($v,$fid));
		$this->erpm->flash_msg("Version changed for franchise");
		redirect("admin/pnh_franchise/$fid");
	}
	
	function jx_pnh_prod_suggestion()
	{
		
		$user=$this->auth();
		$pid=$_POST['pid'];
		$fid=$_POST['fid'];
		$prods=array();
		$cat_brand=$this->db->query("select d.catid,d.brandid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.pnh_id=?",$pid)->row_array();
		$catid=$cat_brand['catid'];
		$brandid=$cat_brand['brandid'];
		
		$suggest_deals = $this->db->query("
												select i.is_combo,i.orgprice as mrp,i.price,
									if((d.brandid=?),' ',i.name) as r, 
										i.name,i.pnh_id,d.catid,d.brandid,i.id 
									from king_deals d 
									join king_dealitems i on i.dealid=d.dealid 
									where i.is_pnh=1 and d.publish=1 and d.catid=? 
									and i.pnh_id!=? and i.live=1 
									order by r asc
										
										",array($brandid,$catid,$pid))->result_array();
		
		foreach($suggest_deals as $p)
		{
			$pid=$p['pnh_id'];
			$margin=$this->erpm->get_pnh_margin($fid,$pid);
			if($p['is_combo']=="1")
				$p['discount']=$p['price']/100*$margin['combo_margin'];
			else
				$p['discount']=$p['price']/100*$margin['margin'];
			$p['margin']=$p['discount']/$p['price']*100;
			
			
			
			
			$stock=$this->erpm->do_stock_check(array($p['id']),array(1),true); 
			
			
			$stock_tmp = array();
			$stock_tmp[0] = array();
			$stock_tmp[0][0] = array('stk'=>0);
			foreach($stock as $plist)
				foreach($plist as $pdet)
				{
					if(!$pdet['status'])
					{
						$prod['live']=0;
						$stock_tmp[0][0] = array('stk'=>$pdet['stk']);
					}
					else
					{
						$stock_tmp[0][0] = array('stk'=>$pdet['stk']);
					}
				}	
					
			
			
			$p['stock']=(($stock_tmp[0][0]['stk']>0)?$stock_tmp[0][0]['stk']:0);
			
			$prods[]=$p;
		}
		$data['prods']=$prods;
		$this->load->view("admin/body/pnh_prod_suggest_frag",$data);
	}
	
	function jx_pnh_fran_cancelledorders()
	{
		
		$user=$this->auth();
		$pid=$_POST['pid'];
		$fid=$_POST['fid'];
		$prods=array();
		$cat_brand=$this->db->query("select d.catid,d.brandid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.pnh_id=?",$pid)->row_array();
		$catid=$cat_brand['catid'];
		$brandid=$cat_brand['brandid'];
		
		$suggest_deals = $this->db->query("
												select i.is_combo,i.orgprice as mrp,i.price,
												if((d.brandid=?),' ',i.name) as r, 
													i.name,i.pnh_id,d.catid,d.brandid,i.id,
													t.init
													 
												from king_deals d 
												join king_dealitems i on i.dealid=d.dealid 
												join king_orders o on o.itemid = i.id 
												join king_transactions t on t.transid = o.transid   
												where i.is_pnh=1 and d.publish=1 
												and o.status = 3 
												and i.live=1 
												order by t.init desc  
									",array($pid))->result_array();
		
		foreach($suggest_deals as $p)
		{
			$pid=$p['pnh_id'];
			$margin=$this->erpm->get_pnh_margin($fid,$pid);
			if($p['is_combo']=="1")
				$p['discount']=$p['price']/100*$margin['combo_margin'];
			else
				$p['discount']=$p['price']/100*$margin['margin'];
			$p['margin']=$p['discount']/$p['price']*100;
			
			
			
			
			$stock=$this->erpm->do_stock_check(array($p['id']),array(1),true); 
			
			
			$stock_tmp = array();
			$stock_tmp[0] = array();
			$stock_tmp[0][0] = array('stk'=>0);
			foreach($stock as $plist)
				foreach($plist as $pdet)
				{
					if(!$pdet['status'])
					{
						$prod['live']=0;
						$stock_tmp[0][0] = array('stk'=>$pdet['stk']);
					}
					else
					{
						$stock_tmp[0][0] = array('stk'=>$pdet['stk']);
					}
				}	
					
			
			
			$p['stock']=(($stock_tmp[0][0]['stk']>0)?$stock_tmp[0][0]['stk']:0);
			
			if(!$p['stock'])
				continue ; 
			
			$prods[]=$p;
		}
		$data['prods']=$prods;
		$this->load->view("admin/body/pnh_prod_suggest_frag",$data);
		
		
		
	}
	
	function pnh_place_quote()
	{
		$user=$this->auth(CALLCENTER_ROLE);
		$req_respond_time = $this->input->post('req_respond_time');
		
		foreach(array("pid","quote","fid","qty") as $i)
			$$i=$this->input->post($i);
		
		$inp=array("franchise_id"=>$fid,"created_on"=>time(),"quote_status"=>0,"respond_in_min"=>$req_respond_time,"created_by"=>$user['userid']);
		$this->db->insert("pnh_quotes",$inp);
		$qid=$this->db->insert_id();
		
		$new_prod_list=$this->input->post('produc_name');
		
		if($new_prod_list)
		{
			$np_name=$this->input->post('produc_name');
			$np_qty=$this->input->post('np_qty');
			$np_mrp=$this->input->post('np_mrp');
			$np_quote=$this->input->post('np_quote');
			foreach($new_prod_list as $i=>$new_prod_det)
			{
				$inp=array("quote_id"=>$qid,"new_product"=>$np_name[$i],"np_qty"=>$np_qty[$i],"np_mrp"=>$np_mrp[$i],"np_quote"=>$np_quote[$i]);
				$this->db->insert("pnh_quotes_deal_link",$inp);
			}
		}
		
		if($pid)
		{
			foreach($pid as $i=>$p)
			{
				$inp=array("quote_id"=>$qid,"pnh_id"=>$p,"qty"=>$qty[$i],"dp_price"=>$quote[$i]);
				$this->db->insert("pnh_quotes_deal_link",$inp);	
			}
		}
		
		$remarks = trim($this->input->post('req_remark'));
		if($remarks)
		{
			$inp = array($qid,$remarks,time(),$user['userid']);
			$this->db->query("insert into pnh_quote_remarks (quote_id,remarks,time,created_by) values(?,?,?,?) " ,$inp);	
		}
		
		echo site_url("admin/pnh_quote/$qid");
	}
	
	function pnh_update_quote($qid)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		if($_POST)
		{
			foreach(array("id","final") as $i)
				$$i=$this->input->post($i);
			$_C_POST=$_POST;
			
			$fid = $_C_POST['fid'];
			$fdet = $this->db->query('select * from pnh_m_franchise_info where franchise_id = ? ',$fid)->row_array();
			$fmob = $fdet['login_mobile1'];
			
			foreach($id as $j=>$i)
			{
				if($final[$j]==0)
					continue;
					
				$itemdet=$this->db->query("select i.id,i.orgprice as mrp,i.name,concat(i.print_name,'-',i.pnh_id) as print_name from pnh_quotes_deal_link l join king_dealitems i on i.is_pnh=1 and i.pnh_id=l.pnh_id where l.id=?",$i)->row_array();
				
				$itemid = $itemdet['id'];
				$itemname = $itemdet['name'];
				$itemmrp = $itemdet['mrp'];
				$itemprint_name = $itemdet['print_name'];
				
					
				if(isset($_C_POST['up_sm'.$i]))
				{
					$_POST=array("special_margin"=>$final[$j],"from"=>date("Y-m-d"),"to"=>date("Y-m-d"),"type"=>1,'internal'=>1);
					$this->pnh_special_margin_deal($itemid);
				}
				
				$is_notified = 0;
				
				// check if notify ticked
				if(isset($_C_POST['notify_sm'.$i]))
				{
					$is_notified = 1;
					$q_msg = "Price for {$itemprint_name}\n Mrp : Rs {$itemmrp}, Landing Cost : Rs ".$final[$j];
					$this->erpm->pnh_sendsms($fmob,$q_msg,$fid);	
				}
				
				$this->db->query("update pnh_quotes_deal_link set final_price=?,status=1,price_updated_by=?,updated_on=?,is_notified=? where id=? limit 1",array($final[$j],$user['userid'],time(),$is_notified,$i));
				
			}
			$this->db->query("update pnh_quotes set updated_by=?,updated_on=? where quote_id=? limit 1",array($user['userid'],time(),$qid));
			$this->erpm->flash_msg("Order Quote updated");
			redirect("admin/pnh_quote/$qid");
		}
	}
	
	function pnh_quotes($fid=0,$s=0,$e=0,$brand=0,$fra=0,$sby=0,$sord=0,$pg=0)
	{
		if($fid)
			$fra = $fid;
		$fid = $fra; 
		
		$user=$this->auth(CALLCENTER_ROLE);
		if($brand)
		{
			$sql="select q.*,u.name as updated_by,c.name as created_by,f.franchise_name,l.new_product,e.brandid 
			from pnh_quotes q 
			join pnh_m_franchise_info f on f.franchise_id=q.franchise_id 
			JOIN `pnh_quotes_deal_link` l ON l.quote_id=q.quote_id 
			join king_admin c on c.id=q.created_by 
			join king_dealitems d on d.pnh_id=l.pnh_id
			join king_deals e on e.dealid=d.dealid
			left outer join king_admin u on u.id=q.updated_by where e.brandid='".$brand."'";
			$count="select COUNT(distinct q.quote_id) as count from pnh_quotes q join pnh_m_franchise_info f on f.franchise_id=q.franchise_id JOIN `pnh_quotes_deal_link` l ON l.quote_id=q.quote_id join king_admin c on c.id=q.created_by join king_dealitems d on d.pnh_id=l.pnh_id join king_deals e on e.dealid=d.dealid left outer join king_admin u on u.id=q.updated_by where e.brandid='".$brand."'";
		
		}elseif($fra)
		{
			$sql="select q.*,u.name as updated_by,c.name as created_by,f.franchise_name,l.new_product,e.brandid 
			from pnh_quotes q 
			join pnh_m_franchise_info f on f.franchise_id=q.franchise_id 
			JOIN `pnh_quotes_deal_link` l ON l.quote_id=q.quote_id 
			join king_admin c on c.id=q.created_by 
			join king_dealitems d on d.pnh_id=l.pnh_id
			join king_deals e on e.dealid=d.dealid
			left outer join king_admin u on u.id=q.updated_by where q.franchise_id='".$fra."'";
			$count="select COUNT(distinct q.quote_id) as count from pnh_quotes q join pnh_m_franchise_info f on f.franchise_id=q.franchise_id JOIN `pnh_quotes_deal_link` l ON l.quote_id=q.quote_id join king_admin c on c.id=q.created_by join king_dealitems d on d.pnh_id=l.pnh_id join king_deals e on e.dealid=d.dealid left outer join king_admin u on u.id=q.updated_by where q.franchise_id='".$fra."'";
		
		}else
		{
			$sql="select q.*,u.name as updated_by,c.name as created_by,f.franchise_name,l.new_product from pnh_quotes q join pnh_m_franchise_info f on f.franchise_id=q.franchise_id JOIN `pnh_quotes_deal_link` l ON l.quote_id=q.quote_id join king_admin c on c.id=q.created_by left outer join king_admin u on u.id=q.updated_by where 1 ";
			$count="select COUNT(distinct q.quote_id) as count from pnh_quotes q join pnh_m_franchise_info f on f.franchise_id=q.franchise_id JOIN `pnh_quotes_deal_link` l ON l.quote_id=q.quote_id join king_admin c on c.id=q.created_by left outer join king_admin u on u.id=q.updated_by where 1 ";
		
		}
		
		$from=$to=0;
		if($s)
		{
			$from=strtotime($s);
			$to=strtotime("23:59:59 $e");
		}
		if($fra)
		{
			//$sql.=" and  q.franchise_id=? ";
			//$count.=" and  q.franchise_id=? ";
		}
		
		if($from)
		{
			$sql.=" and q.created_on between $from and $to ";
			$count.=" and  q.created_on between $from and $to  ";
		}
		
		
		$sql.=" group by q.quote_id ";
				
		
		if($sby == '' || $sby==0)	
		{
			$sql.=" order by q.created_on desc,q.updated_on desc";
			$count.=" order by q.created_on desc,q.updated_on desc";
		}
		elseif($sby == 'fid' || $sby == 'status'){

			if($sby == 'fid')
				$sby = 'f.franchise_name';
				
			else if($sby == 'status') 	
				$sby = '';
				
			$sord = $sord=='a'?'asc':'desc'; 
			$sql.=" order by $sby,$sord,q.created_on desc,q.updated_on desc";
			$count.=" order by $sby,$sord,q.created_on desc,q.updated_on desc";
		}
		
		$sql.=" limit $pg,20";
		$title="Franchise Requests";
		if($fra)
			$title.=" for '".$this->db->query("select franchise_name from pnh_m_franchise_info where franchise_id=?",$fra)->row()->franchise_name."'";
		if($from)
			$title.=" between $s and $e";
		$data['url']=site_url("admin/pnh_quotes");
		$data['st_d'] = $s?$s:0;
		$data['en_d'] = $e?$e:0;
		
		$data['pagetitle']=$title;
		
		$data['quotes']=$this->db->query($sql)->result_array();
		$ttl_quotes = $this->db->query($count)->row()->count;
		$data['brand_res']=$this->db->query("select b.name as brand_name,d.brandid,i.name,i.orgprice as mrp,i.price,q.pnh_id,q.* from pnh_quotes_deal_link q join king_dealitems i on i.pnh_id=q.pnh_id and i.is_pnh=1 join king_deals d on d.dealid = i.dealid join king_brands b on b.id = d.brandid group by brand_name")->result_array();
		$data['franch_res']=$this->db->query("select a.franchise_id,b.franchise_name from pnh_quotes a join pnh_m_franchise_info b on b.franchise_id=a.franchise_id group by b.franchise_name")->result_array();
		$data['results']=$ttl_quotes;
		
		$data["links"] = $this->_prepare_pagination(site_url("admin/pnh_quotes/".$fid.'/'.$s.'/'.$e.'/'.$brand.'/'.$fra.'/'.$sby.'/'.$sord),$ttl_quotes,20,10);
		
		$data['total_rows'] = $ttl_quotes;
		
		$data['page_num'] = $pg; 
		
		$data['brand']=$brand;
		$data['fra']=$fra;	
		$data['page']="pnh_quotes";
		$this->load->view("admin",$data);
	}
	
	function pnh_quote_remarks()
	{
		$user=$this->auth(CALLCENTER_ROLE);
		
		$req_complete = $this->input->post('req_complete')*1;
			if($req_complete)
				$this->db->query("update pnh_quotes set quote_status = 1 where quote_id = ? ",$this->input->post("id"));
			
			
		$this->db->insert("pnh_quote_remarks",array("quote_id"=>$this->input->post("id"),"remarks"=>$this->input->post("remarks"),"time"=>time(),"created_by"=>$user['userid'],"req_complete"=>$req_complete));
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function pnh_quote($qid)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		if($_POST)
		{
			$id=$_POST['id'];
			$transid=$_POST['transid'];
			$this->db->query("update pnh_quotes_deal_link set transid=?,order_status=1,updated_on=?,updated_by=? where id=? limit 1",array($transid,time(),$user['userid'],$id));
			die;
		}
		$data['quote']=$this->db->query("select q.*,f.franchise_id,f.franchise_name,u.name as admin,q.quote_id from pnh_quotes q join pnh_m_franchise_info f on f.franchise_id=q.franchise_id join king_admin u on u.id=q.created_by where q.quote_id=?",$qid)->row_array();
		$data['deals']=$this->db->query("select i.name,i.orgprice as mrp,i.price,q.pnh_id,q.* from pnh_quotes_deal_link q LEFT join king_dealitems i on i.pnh_id=q.pnh_id and i.is_pnh=1 where q.quote_id=? order by pnh_id desc",$qid)->result_array();
		$data['page']="pnh_quote";
		$this->load->view("admin",$data);
	}
	
	function pnh_offline_order()
	{
		$user=$this->auth(OFFLINE_ORDER_ROLE);
		if($_POST)
			$this->erpm->do_pnh_offline_order();
		$data['page']="pnh_offline_order";
		
		$this->load->view("admin",$data);
	}
	
	function pnh_jx_getfranbalance()
	{
		$user=$this->auth();
		$fid = $_POST['id'];
		$fran=$this->db->query("select datediff(curdate(),date(from_unixtime(created_on))) as reg_days,current_balance as balance,credit_limit as credit,is_suspended,reason from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
		
		$fran_crdet = $this->erpm->get_fran_availcreditlimit($fid);
		$fran['balance'] = $fran_crdet[3];
		
        $fran_courier = $this->erpm->get_fran_courier_details($fid);
		$fran['courier'] = $fran_courier;
		
		$fran['total_ord'] = $this->db->query("select count(transid) as t from king_transactions where franchise_id = ? ",$_POST['id'])->row()->t;
		
		$ttl_ords_res = $this->db->query("select init from king_transactions where franchise_id = ? order by id desc limit 1",$_POST['id']);
		if($ttl_ords_res->num_rows())
			$fran['last_ordon'] = date('d/m/Y',$ttl_ords_res->row()->init);
		else
			$fran['last_ordon'] = '---na---'; 
		$fran['total_mem'] = $this->db->query("select count(*) as t from pnh_member_info where franchise_id = ? ",$_POST['id'])->row()->t;
		
		
		echo json_encode($fran);
	}
	
	/* function pnh_jx_loadfranchisebyid()
	{
		$user=$this->auth();
		$sus_type_arr=array();
		$sus_type_arr[1]="Permanent suspension";
		$sus_type_arr[2]="Payment suspension";
		$sus_type_arr[3]="Temporary suspension";
		$fid=$_POST['fid'];
		$fran=$this->db->query("select a.*,town_name,territory_name from pnh_m_franchise_info a join pnh_m_territory_info b on a.territory_id = b.id join pnh_towns c on a.town_id = c.id where a.pnh_franchise_id=?",$fid)->row_array();
		$sec_q=array("What was your childhood nickname?","In what city were you born?","What is the name of the company of your first job?","In what year was your father born?","What was the name of your elementary / primary school?","What is your mother's maiden name?"," What is your oldest sibling's name?"," Who was your childhood hero?");
		if(empty($fran))
			die("<h3>No franchisee available for given id</h3>");
		if($fran['is_suspended']==1)
			die("<h3>This franchise account is in <b style='color:red;'> ".$sus_type_arr[$fran['is_suspended']].' for ' .$fran['reason'].'</b>');
		if($fran['is_suspended']==2)
			echo '<script>alert("Franchise account is in Payment suspension")</script>';
		if($fran['is_suspended']==3)
		   echo '<script>alert("Franchise Account is in Temporary Suspension")</script>';
		echo "<h4><a target='_blank' loc='{$fran['town_name']},{$fran['territory_name']}' href='".site_url("admin/pnh_franchise/".$fran['franchise_id'])."'>{$fran['franchise_name']}</a></h4>";
		echo '<table border=1 cellpadding="5"><tr><th>Login Details</th><th>Authenticate</th><th>Details</th><th></th></tr>';
		echo "<tr><td>Login Mobile1 : <span class='ff_mob'>{$fran['login_mobile1']}</span><img src='".IMAGES_URL."phone.png' class='phone_small' onclick='makeacall(\"0{$fran['login_mobile1']}\")'></td><td>Security Question : ".($fran['security_question']=="-1"?$fran['security_custom_question']:$sec_q[$fran['security_question']])."  Answer : <b>{$fran['security_answer']}</b></td><td>FID : {$fran['pnh_franchise_id']}</td><td>Territory : ".$this->db->query("select territory_name as name from pnh_m_territory_info where id=?",$fran['territory_id'])->row()->name."</td></tr>";
		echo "<tr><td>Login Mobile2 : {$fran['login_mobile2']}<img src='".IMAGES_URL."phone.png' class='phone_small' onclick='makeacall(\"0{$fran['login_mobile2']}\")'></td><td>Security Question2 : ".($fran['security_question2']=="-1"?$fran['security_custom_question2']:$sec_q[$fran['security_question2']])."  Answer : <b>{$fran['security_answer2']}</b></td><td>Balance : Rs {$fran['current_balance']}</td><td>Town : ".$this->db->query("select town_name as name from pnh_towns where id=?",$fran['town_id'])->row()->name."</td></tr>";
		echo "<tr><td>Login Email : {$fran['email_id']}</td><td></td><td>Credit Limit : Rs {$fran['credit_limit']}</td></tr>";
		echo '</table>';
		echo "<div id='auth_cont'><input type='button' value='Authenticate' onclick='select_fran({$fran['franchise_id']})'></div>";
	} */
	
	function pnh_jx_loadfranchisebyid()
	{
		$user=$this->auth();
		$sus_type_arr=array();
		$sus_type_arr[1]="Permanent suspension";
		$sus_type_arr[2]="Payment suspension";
		$sus_type_arr[3]="Temporary suspension";
		$fid=$_POST['fid'];
		$fran=$this->db->query("select a.*,town_name,territory_name from pnh_m_franchise_info a join pnh_m_territory_info b on a.territory_id = b.id join pnh_towns c on a.town_id = c.id where a.pnh_franchise_id=?",$fid)->row_array();
		$acc_statement = $this->erpm->get_franchise_account_stat_byid($fran['franchise_id']);
		$pending_payment = $acc_statement['pending_payment'];
		$credit_note_amt = $acc_statement['credit_note_amt'];
		$shipped_tilldate = $acc_statement['shipped_tilldate'];
		$paid_tilldate = $acc_statement['paid_tilldate'];
		$pamt=formatInIndianStyle($shipped_tilldate-$paid_tilldate+$acc_adjustments_val-$credit_note_amt,2);
		
		$sec_q=array("What was your childhood nickname?","In what city were you born?","Last orderd date?","What is the name of the company of your first job?","In what year was your father born?","What was the name of your elementary / primary school?","What is your mother's maiden name?"," What is your oldest sibling's name?"," Who was your childhood hero?");
		$modes=array("Cash","Cheque","DD","Transfer");
		$r_status =array("Pending","Activated","Cancelled","Bounced");
		$i_status=array("Inactive","Active");
		$fran_ordrdet=$this->db->query("SELECT i.invoice_status,DATE_FORMAT(FROM_UNIXTIME(o.time),'%d/%m/%Y') AS orderd_on,DATE_FORMAT(FROM_UNIXTIME(t.init),'%d/%m/%Y') AS init,t.amount,i.invoice_no,t.transid FROM king_orders o  JOIN king_transactions t ON t.transid=o.transid JOIN pnh_m_franchise_info f ON f.franchise_id=t.franchise_id  LEFT JOIN king_invoice i ON i.order_id=o.id  WHERE t.franchise_id=?  GROUP BY t.franchise_id  ORDER BY o.id DESC LIMIT 1",$fran['franchise_id'])->row_array();
		$fran_reciptdet=$this->db->query("SELECT DATE_FORMAT(FROM_UNIXTIME(instrument_date),'%d/%m/%Y')as receipt_date,receipt_id,receipt_amount,receipt_type,instrument_no,instrument_date,`status` FROM pnh_t_receipt_info  WHERE franchise_id=? ORDER BY receipt_id DESC LIMIT 3",$fran['franchise_id'])->result_array();
		$fran_invdet=$this->db->query("SELECT `status`,init,DATE_FORMAT(FROM_UNIXTIME(actiontime),'%d/%m/%Y') as actiontime ,nlc,invoice_no FROM king_invoice i JOIN king_transactions t ON t.transid=i.transid WHERE franchise_id=? ORDER BY i.id DESC LIMIT 1",$fran['franchise_id'])->row_array();
		$fran_bankinfo=$this->db->query("SELECT * FROM pnh_franchise_bank_details WHERE franchise_id=?",$fran['franchise_id'])->result_array();
		if(empty($fran))
			die("<h3>No franchisee available for given id</h3>");
		if($fran['is_suspended']==1)
			die("<h3>This franchise account is in <b style='color:red;'> ".$sus_type_arr[$fran['is_suspended']].' for ' .$fran['reason'].'</b>');
		if($fran['is_suspended']==2)
			echo '<script>alert("Franchise account is in Payment suspension \n\n Note:Pleas inform this to franchise in call")</script>';
		if($fran['is_suspended']==3)
			echo '<script>alert("Franchise Account is in Temporary Suspension \n\n Note:Pleas inform this to franchise in call")</script>';
		echo "<h4><a target='_blank' loc='{$fran['town_name']},{$fran['territory_name']}' href='".site_url("admin/pnh_franchise/".$fran['franchise_id'])."'>{$fran['franchise_name']}</a></h4>";
		echo "<table class='security_details'><tr><th class='label'>Franchise Details</td><td class='label' width='100%'>Authenticate/Security Details</td></tr>";
		echo "<tr><td class='security_details'>Territory : ".$this->db->query("select territory_name as name from pnh_m_territory_info where id=?",$fran['territory_id'])->row()->name."</td><td>Security Question : ".($fran['security_question']=="-1"?$fran['security_custom_question']:$sec_q[$fran['security_question']])."  Answer : <b>{$fran['security_answer']}</b></td>";
		echo "<tr><td class='security_details'>Town : ".$this->db->query("select town_name as name from pnh_towns where id=?",$fran['town_id'])->row()->name."</td></td><td>Security Question2 : ".($fran['security_question2']=="-1"?$fran['security_custom_question2']:$sec_q[$fran['security_question2']])."  Answer : <b>{$fran['security_answer2']}</b></td>";
		echo "<tr><td class='security_details'>FID : {$fran['pnh_franchise_id']}</td><td>Security Question3 : ".($fran_ordrdet['time']!=0?$fran_ordrdet['time']:$sec_q[2])."  Answer : <b>{$fran_ordrdet['orderd_on']}</b></td></tr>";
		echo "<tr><td class='security_details'><table class='datagrid'><th>Login Mobile1</th><th>Login Mobile2</th><th>Login Email</th><tbody><td><span class='ff_mob'>{$fran['login_mobile1']}</span><img src='".IMAGES_URL."phone.png' class='phone_small' onclick='makeacall(\"0{$fran['login_mobile1']}\")'></td><td>{$fran['login_mobile2']}<img src='".IMAGES_URL."phone.png' class='phone_small' onclick='makeacall(\"0{$fran['login_mobile2']}\")'></td><td>{$fran['email_id']}</td></table></td><td class='security_details'><b>Last Order Details</b><table class='datagrid'><th>Transid</th><th>Amount</th><th>Date</th><tbody></tbody><tr><td>{$fran_ordrdet['transid']}</td><td>{$fran_ordrdet['amount']}</td><td>{$fran_ordrdet['init']}</td></tr></table></td></tr>";
		echo "<tr><td class='security_details'><b>Bank Details</b><table class='datagrid'><th>Bank Name</th><th>Account no</th><th>Branch name</th><tbody>";
		foreach($fran_bankinfo as $fran_b){
		echo "<tr><td>{$fran_b['bank_name']}</td><td>{$fran_b['branch_name']}</td><td>{$fran_b['account_no']}</td></tr></tbody>";}
		echo "</table></td>";
		
		echo "<td><b>Last Invoice Details</b><table class='datagrid'><th>Invoice no</th><th>Invoice Amount</th><th>Invoice status</th><th>Invoice date</th><tbody><tr><td>{$fran_invdet['invoice_no']}</td><td>{$fran_invdet['nlc']}</td><td>{$i_status[$fran_invdet['status']]}</td><td>{$fran_invdet['actiontime']}</td></tr></tbody></table></td></tr>";
		echo "<tr><td class='security_details'><b>Pan card/ration card Details:</b><p>Pan card number:</p><p><b>Credit Limit: Rs {$fran['credit_limit']}</b></p><p><b>Pending Amount: Rs {$pamt}</b></p></td><td><b>Last 3 payment details</b>";
		echo "<table class='datagrid'><th>Receipt Date</th><th>Receipt type</th><th>Amount</th><th>Status</th>";
		echo "<tbody>";
		echo "<tr>";
		foreach($fran_reciptdet as $f){
		echo "<td>{$f['receipt_date']}</td><td>{$modes[$f['receipt_type']]}</td><td>{$f['receipt_amount']}</td><td>{$r_status[$f['status']]}</td></tr></tbody>";}
		echo "</table></td></tr>";
		echo "</table>";
		echo '<br>';
		echo "<div id='auth_cont'style='float:right;'><input type='button' class='button button-flat-royal button-small button-rounded' value='Authenticate' onclick='select_fran({$fran['franchise_id']})'></div>";
		
		
	}
	
	function pnh_jx_loadfranchisebymobile()
	{
		$user=$this->auth();
		$mobile=$_POST['mobile'];
		$fran=$this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? and login_mobile1<>0) or (login_mobile2=? and login_mobile2!=0)",array($mobile,$mobile))->row_array();
		$sec_q=array("What was your childhood nickname?","In what city were you born?","What is the name of the company of your first job?","In what year was your father born?","What was the name of your elementary / primary school?","What is your mother's maiden name?"," What is your oldest sibling's name?"," Who was your childhood hero?");
		if(empty($fran))
			die("<h3>No franchisee available for given id</h3>");
		if($fran['is_suspended']==1)
			die("<h3>This franchise account is suspended");
		echo "<h3><a target='_blank' href='".site_url("admin/pnh_franchise/".$fran['franchise_id'])."'>{$fran['franchise_name']}</a></h3>";
		echo '<table border=1 cellpadding="5"><tr><th>Login Details</th><th>Authenticate</th><th>Details</th></tr>';
		echo "<tr><td>Login Mobile1 : <span class='ff_mob'>{$fran['login_mobile1']}</span><img src='".IMAGES_URL."phone.png' class='phone_small' onclick='makeacall(\"0{$fran['login_mobile1']}\")'></td><td>Security Question : ".($fran['security_question']=="-1"?$fran['security_custom_question']:$sec_q[$fran['security_question']])."  Answer : <b>{$fran['security_answer']}</b></td><td>FID : {$fran['pnh_franchise_id']}</td><td>Territory : ".$this->db->query("select territory_name as name from pnh_m_territory_info where id=?",$fran['territory_id'])->row()->name."</td></tr>";
		echo "<tr><td>Login Mobile2 : {$fran['login_mobile2']}<img src='".IMAGES_URL."phone.png' class='phone_small' onclick='makeacall(\"0{$fran['login_mobile2']}\")'></td><td>Security Question2 : ".($fran['security_question2']=="-1"?$fran['security_custom_question2']:$sec_q[$fran['security_question2']])."  Answer : <b>{$fran['security_answer2']}</b></td><td>Balance : Rs {$fran['current_balance']}</td><td>Town : ".$this->db->query("select town_name as name from pnh_towns where id=?",$fran['town_id'])->row()->name."</td></tr>";
		echo "<tr><td>Login Email : {$fran['email_id']}</td><td></td><td>Credit Limit : Rs {$fran['credit_limit']}</td></tr>";
		echo '</table>';
		echo "<div id='auth_cont'><input type='button' value='Authenticate' onclick='select_fran({$fran['franchise_id']})'></div>";
	}

	function jx_checkloginmob()
	{
		$mobile=$this->input->post("mob");
		$fran=$this->db->query("select * from pnh_m_franchise_info where (login_mobile1=? and login_mobile1<>0) or (login_mobile2=? and login_mobile2!=0)",array($mobile,$mobile))->row_array();
		if(empty($fran))
			echo "1";
		else
			echo "0";
	}

	function pnh_jx_loadpnhprodbybarcode()
	{
		$user=$this->auth();
		$fid=$this->input->post('fid');
		$barcode=$_POST['barcode'];
		
		$sql = "SELECT i.pnh_id AS pid  
						FROM m_product_info p  
						JOIN m_product_deal_link l ON l.product_id=p.product_id 
						JOIN king_dealitems i ON i.id=l.itemid AND i.is_pnh=1 
						JOIN king_deals d ON d.dealid=i.dealid  
						JOIN `pnh_franchise_menu_link` m ON m.menuid=d.menuid 
						WHERE p.barcode=? ";
		if($fid)
			$sql .= " AND fid=? ";

		$ret=$this->db->query($sql,array($barcode,$fid))->row_array();
		if(empty($ret))
			echo json_encode(array("pid"=>0));
		else
			echo json_encode(array("pid"=>$ret['pid']));
	}
	
	function pnh_jx_show_schemes()
	{
		$fid=$_POST['fid'];
		$msg='<table width="100%" cellpadding=5 cellspacing=0>';
		$msg.='<thead><tr><th>Brand</th><th>Category</th><th>Discount</th></tr></thead>';
		$msg.='<tbody>';
		$disc=$this->db->query("select s.*,a.name as admin,b.name as brand,c.name as category from pnh_sch_discount_brands s left outer join king_brands b on b.id=s.brandid left outer join king_categories c on c.id=s.catid join king_admin a on a.id=s.created_by where s.franchise_id=? and ? between valid_from and valid_to group by brandid order by id desc",array($fid,time()))->result_array();
		foreach($disc as $s){
		$msg.="<tr><td>".(empty($s['brand'])?"All brands":$s['brand'])."</td>";
		$msg.="<td>".(empty($s['category'])?"All categories":$s['category'])."</td>";
		$msg.="<td>".($s['discount'])."%</td>";
		$msg.="</tr>";
		}
		if(empty($disc))
			$msg.="<tr><Td colspan='100%'>no schemes</td></tr>";
		$msg.="</tbody></table>";
		echo $msg;
	}
	
	/*function pnh_jx_show_super_scheme_sales_statics()
	{
		$output=array();
		$fid=$_POST['fid'];
		$check_super_scheme=$this->db->query("select * from pnh_super_scheme where franchise_id=? limit 1 and is_active=1 and UNIX_TIMESTAMP(NOW()) between valid_from and valid_to",$fid)->row_array();
		
		
		$scheme_sales=$this->db->query("SELECT  d.menuid,d.brandid,d.catid,SUM(i_orgprice-(i_discount+i_coup_discount)) AS ttl_sales,f.name as brand_name,g.name as cat_name,m.name as menu_name,b.super_scheme_target 
											FROM king_transactions a
											JOIN king_orders b ON a.transid = b.transid 
											JOIN king_dealitems c ON c.id = b.itemid 
											JOIN king_deals d ON d.dealid = c.dealid
											JOIN pnh_super_scheme e ON e.franchise_id = a.franchise_id
											join king_brands f on f.id=d.brandid
											join king_categories g on g.id=d.catid
											join pnh_menu m on m.id=d.menuid
											WHERE a.franchise_id = ? AND a.is_pnh = 1 AND b.has_super_scheme = 1 AND a.init BETWEEN e.valid_from AND e.valid_to 
											GROUP BY a.id,d.menuid,d.brandid,d.catid",$fid);
		if($scheme_sales->num_rows())
		{
			$output['status']='success';
			$output['super_schsales']=$scheme_sales->result_array();
		}
		else 
		{
			$output['status']='error';
		}
		echo json_encode($output);
		
	
	}*/

	function pnh_jx_load_scheme_details()
	{
		$fid=$_POST['fid'];
		$output=array();
		$active_schemdiscount=$this->db->query("SELECT *,DATE_FORMAT((FROM_UNIXTIME(a.valid_from)),'%d/%m/%Y') as validfrom,DATE_FORMAT((FROM_UNIXTIME(a.valid_to)),'%d/%m/%Y') as validto,b.name AS brand_name,c.name AS cat_name,m.name AS menu_name FROM pnh_sch_discount_brands a LEFT JOIN `king_brands` b ON b.id=a.brandid LEFT JOIN `king_categories` c ON c.id=a.catid JOIN `pnh_menu`m ON m.id=a.menuid WHERE is_sch_enabled=1 AND sch_type=1 AND franchise_id=? and ? between a.valid_from and a.valid_to",array($fid,time()));
		$active_superscheme=$this->db->query("SELECT *,DATE_FORMAT((FROM_UNIXTIME(a.valid_from)),'%d/%m/%Y') as validfrom,DATE_FORMAT((FROM_UNIXTIME(a.valid_to)),'%d/%m/%Y') as validto,b.name AS brand_name,c.name AS cat_name,m.name AS menu_name FROM pnh_super_scheme a LEFT JOIN `king_brands` b ON b.id=a.brand_id LEFT JOIN `king_categories` c ON c.id=a.cat_id JOIN `pnh_menu`m ON m.id=a.menu_id WHERE  is_active=1 AND franchise_id=? and ? between valid_from and valid_to",array($fid,time()));
		$fran_menu=$this->db->query("SELECT m.name AS menu,menuid  FROM pnh_franchise_menu_link a JOIN pnh_menu m ON m.id= a.menuid WHERE a.status=1 AND fid=?",$fid);
		
		
			if( $fran_menu->num_rows())
			{
				$output['status'] = 'success';
				$output['menu'] = $fran_menu->result_array();
			}
			if($active_superscheme->num_rows())
			{
				$output['status']='success';
				$output['active_supersch']=$active_superscheme->result_array();
			}
			
			if($active_schemdiscount->num_rows() )
			{
				$output['status']='success';
				$output['active_schdisc']=$active_schemdiscount->result_array();
			}
		
		
		echo json_encode($output);
	}
	
	function pnh_jx_loadmemids()
	{
		$fid=$this->input->post('fid');
		$output = array();
		$fran_memids_res=$this->db->query("SELECT mid_start,mid_end FROM pnh_m_allotted_mid m WHERE franchise_id=? ORDER BY id DESC LIMIT 1;",$fid);
		if($fran_memids_res->num_rows())
		{
			$output['status'] = 'success';
			$output['mem_range'] = $fran_memids_res->row_array();
		}else
		{
			$output['status'] = 'error';
			$output['error'] = 'Member Ids not alloted';
		}
		echo json_encode($output);
	}
	
	function pnh_jx_load_menu()
	{
		$fid=$this->input->post('fid');
		$output=array();
		$fran_menu=$this->db->query("SELECT m.name AS menu  FROM pnh_franchise_menu_link a JOIN pnh_menu m ON m.id= a.menuid WHERE a.status=1 AND is_sch_enabled=1 AND fid=?",$fid);
		if($fran_menu->num_rows())
		{
			$output['status'] = 'success';
			$output['menu'] = $fran_menu->result_array();
		}else
		{
			$output['status'] = 'error';
			$output['error'] = 'Menu not alloted';
		}
		echo json_encode($output);
	}
	
	function pnh_jx_loadpnhprod()
	{
		$user=$this->auth();
		$fid=$_POST['fid'];
		$pid=$_POST['pid'];
		
		$prod=$this->db->query("select d.menuid,b.name as brand,c.name as cat,i.id,i.is_combo,i.pnh_id as pid,i.live,i.orgprice as mrp,i.price,i.name,i.pic from king_dealitems i join king_deals d on d.dealid=i.dealid and d.publish=1 left join king_brands b on b.id = d.brandid join king_categories c on c.id = d.catid where pnh_id=? and is_pnh=1",$pid)->row_array();
		if(!empty($prod))
		{
			
			$allow_for_fran = $this->db->query("select count(*) as t from pnh_franchise_menu_link where status = 1 and fid = ? and menuid in (select menuid 
													from king_dealitems a
													join king_deals b on a.dealid = b.dealid 
													where pnh_id = ? )",array($fid,$pid))->row()->t; 
			
			if($allow_for_fran)
			{
				$stock=$this->erpm->do_stock_check(array($prod['id']),array(1),true);
				
				$stock_tmp = array();
				$stock_tmp[0] = array();
				$stock_tmp[0][0] = array('stk'=>0);
				foreach($stock as $plist)
					foreach($plist as $pdet)
					{
					
						if(!$pdet['status'])
						{
							$prod['live']=0;
							$stock_tmp[0][0] = array('stk'=>$pdet['stk']);
						}
						else
						{
							$prod['live']=1;
							$stock_tmp[0][0] = array('stk'=>$pdet['stk']);
						}
							
					}	
			
			
				$prod_mrp_changelog_res = $this->db->query("select * from deal_price_changelog where itemid=? order by id desc limit 1",$prod['id']);
				if($prod_mrp_changelog_res->num_rows())
				{
					$prod_mrp_changelog = $prod_mrp_changelog_res->row_array();
					$prod['oldmrp']=$prod_mrp_changelog['old_mrp'];
				}
				else 
				{
					$prod['oldmrp']='-';
				}
				 
				
				$margin=$this->erpm->get_pnh_margin($fid,$pid);
				if($prod['is_combo'])
					$prod['margin']=$margin['combo_margin'];
				else 
					$prod['margin']=$margin['margin'];
				$attr="";
				foreach($this->db->query("select group_id from m_product_group_deal_link where itemid=?",$prod['id'])->result_array() as $g)
				{
					$group=$this->db->query("select group_id,group_name from products_group where group_id=?",$g['group_id'])->row_array();
					$attr.="<div style='padding:5px;'>{$group['group_name']}";
					$anames=$this->db->query("select attribute_name_id,attribute_name from products_group_attributes where group_id=?",$g['group_id'])->result_array();
					foreach($anames as $a)
					{
						$attr.="<br>{$a['attribute_name']} :<select class='attr' name='{$pid}_{$a['attribute_name_id']}'>";
						$avalues=$this->db->query("select * from products_group_attribute_values where attribute_name_id=?",$a['attribute_name_id'])->result_array();
						foreach($avalues as $v)
							$attr.="<option value='{$v['attribute_value_id']}'>{$v['attribute_value']}</option>";
						$attr.='</select>';
					}
					$attr.="</div>";
				}
				$prod['lcost']=round($prod['price']-($prod['price']/100*$prod['margin']),2);
				$prod['attr']=$attr;
				
				$prod['confirm_stock'] = '';
				
				//confirmation for prod stock check for shoes  
				if($prod['menuid'] == 123)
					$prod['confirm_stock'] = 'Footwear Stock Available : <input type="checkbox" name="confirm_stock" value="1" >';
				
				$prod['stock']=(($stock_tmp[0][0]['stk']>0)?$stock_tmp[0][0]['stk']:0);
				
				$prod['max_allowed_qty'] = $this->db->query("select max_allowed_qty from king_dealitems where pnh_id = ? ",$pid)->row()->max_allowed_qty;
				$prod['imei_disc'] = $this->erpm->get_franimeischdisc_pid($fid,$pid);
				
				// get pid ordered total for today.
				$prod['max_ord_qty'] = $this->erpm->get_maxordqty_pid($fid,$pid);
				
				unset($prod['is_combo']);
			}else
			{
				$menu = $this->db->query("select c.name  
												from king_dealitems a
												join king_deals b on a.dealid = b.dealid 
												join pnh_menu c on c.id = b.menuid 
												where pnh_id = ? ",$pid)->row()->name;
												
				$prod = array('error'=>$menu." Menu not linked to franchise");
			}
		}
		echo json_encode($prod);
	}
	
	function jx_pnh_load_voucherprod()
	{
		$user=$this->auth();
		$fid=$_POST['fid'];
		$pid=$_POST['pid'];
		$mem_mobno=$_POST['mmob'];
		$vcode=$_POST['vcode'];
		$vcode=explode(',',$vcode);
		
		
		 //-----voucher validation-------
		//serach deal for voucher slno
		$vcode=array_filter($vcode);
		$vcode=array_unique($vcode);
		
		if($vcode)
		{
		$prod=array();
		$vemenus=array();
		foreach($vcode as $v)
		{
		if($mem_mobno)
		{
		$mem_det=$this->db->query("select * from pnh_member_info where mobile=?",$mem_mobno)->row_array();
		}
		
		if(empty($mem_det))
		{
		$prod['error1']=1;
		$prod['msg']="Error!!!\n Member not found for given mobile number.";
		break;
		}
		
		$mid=$mem_det['pnh_member_id'];
		
		$v_exist_res=$this->db->query("select customer_value from pnh_t_voucher_details where voucher_code=?",$v);
		if($v_exist_res->num_rows())
		{
		$v_val=$v_exist_res->row_array();
		if($v_val['customer_value']==0 && !isset($prod['error1']))
		{
		$prod['error1']=1;
		$prod['msg']="Error!!!\n Voucher '.$v.'dot not have balance.";
		break;
		}
		}else{
		$prod['error1']=1;
		$prod['msg']="Error!!!\n Error!!!\n voucher '.$v.' not found.";
		break;
		}
		
		$vdet=$this->db->query("select b.book_id,d.franchise_id,voucher_margin
				from pnh_t_voucher_details a
				join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
				join pnh_t_book_allotment d on d.book_id=b.book_id
				where voucher_code=? and is_activated=1 and a.status in (3) and member_id=?
				group by b.book_id",array($v,$mid))->row_array();
		
		if(empty($vdet) && !isset($prod['error1']))
		{
		$prod['error1']=1;
		$prod['msg']="Error!!!\n Please check voucher slno.";
			
		}
		//get the book menus
		$vmenus_det=$this->db->query("select id,name from  pnh_menu where id in (select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=?)",$vdet['book_id'])->result_array();
		
		foreach ($vmenus_det as $vmenu)
		{
		array_push($vemenus,$vmenu['id']);
		}
		
		}
			
		if(count(array_unique($vemenus)) >1 && !isset($prod['error1']) )
		{
		$prod['error1']=1;
		$prod['msg']="Error!!!\n Vouchers of different menu cannot be processed \n Please use vouchers of same ,menu.";
		}
			
		$pmenu=$this->db->query("SELECT d.menuid
				FROM king_dealitems i
				JOIN king_deals d ON d.dealid=i.dealid
				WHERE i.is_pnh=1 AND pnh_id= ?",$pid)->row()->menuid;
			
			
		if(!in_array($pmenu,$vemenus) && !isset($prod['error1']))
		{
		$prod['error1']=1;
		$prod['msg']="Error!!!\n This product not belongs to this voucher";
		}
			
		}else
		{
		if(empty($vcode) || !$mem_mobno)
		{
		$prod['error1']=1;
		$prod['msg']="Error!!!\n pleas input voucher code.\n pleas input member mobno.";
		
		}
		if(!$mem_mobno && !isset($prod['error1']))
		{
		$prod['error1']=1;
		$prod['msg']="Error!!!\n pleas input Mobile no ";
		
		}
		
		}
		
		if(isset($prod['error1']))
		{
			echo json_encode($prod);
			die();
		} 
		
		$prod=$this->db->query("select d.menuid,b.name as brand,c.name as cat,i.id,i.is_combo,i.pnh_id as pid,i.live,i.orgprice as mrp,i.price,i.name,i.pic from king_dealitems i join king_deals d on d.dealid=i.dealid and d.publish=1 left join king_brands b on b.id = d.brandid join king_categories c on c.id = d.catid where pnh_id=? and is_pnh=1",$pid)->row_array();
		if(!empty($prod))
		{
				
			$allow_for_fran = $this->db->query("select count(*) as t from pnh_franchise_menu_link where status = 1 and fid = ? and menuid in (select menuid
													from king_dealitems a
													join king_deals b on a.dealid = b.dealid
													where pnh_id = ? )",array($fid,$pid))->row()->t;
				
				
		
			if(1)
			{
		
				$stock=$this->erpm->do_stock_check(array($prod['id']),array(1),true);
		
				$stock_tmp = array();
				$stock_tmp[0] = array();
				$stock_tmp[0][0] = array('stk'=>0);
				foreach($stock as $plist)
					foreach($plist as $pdet)
					{
							
						if(!$pdet['status'])
						{
							$prod['live']=0;
							$stock_tmp[0][0] = array('stk'=>$pdet['stk']);
						}
						else
						{
							$prod['live']=1;
							$stock_tmp[0][0] = array('stk'=>$pdet['stk']);
						}
							
					}
						
						
					$prod_mrp_changelog_res = $this->db->query("select * from deal_price_changelog where itemid=? order by id desc limit 1",$prod['id']);
					if($prod_mrp_changelog_res->num_rows())
					{
						$prod_mrp_changelog = $prod_mrp_changelog_res->row_array();
						$prod['oldmrp']=$prod_mrp_changelog['old_mrp'];
					}
					else
					{
						$prod['oldmrp']='-';
					}
						
		
					//$margin=$this->erpm->get_pnh_margin($fid,$pid);
					$margin=$vdet['voucher_margin'];
					
					if($prod['is_combo'])
						$prod['margin']=$margin['combo_margin'];
					else
						$prod['margin']=$margin['margin'];
					$attr="";
					foreach($this->db->query("select group_id from m_product_group_deal_link where itemid=?",$prod['id'])->result_array() as $g)
					{
						$group=$this->db->query("select group_id,group_name from products_group where group_id=?",$g['group_id'])->row_array();
						$attr.="<div style='padding:5px;'>{$group['group_name']}";
						$anames=$this->db->query("select attribute_name_id,attribute_name from products_group_attributes where group_id=?",$g['group_id'])->result_array();
						foreach($anames as $a)
						{
							$attr.="<br>{$a['attribute_name']} :<select class='attr' name='{$pid}_{$a['attribute_name_id']}'>";
							$avalues=$this->db->query("select * from products_group_attribute_values where attribute_name_id=?",$a['attribute_name_id'])->result_array();
							foreach($avalues as $v)
								$attr.="<option value='{$v['attribute_value_id']}'>{$v['attribute_value']}</option>";
							$attr.='</select>';
						}
						$attr.="</div>";
					}
					$prod['lcost']=round($prod['price']-($prod['price']/100*$prod['margin']),2);
					$prod['attr']=$attr;
		
					$prod['confirm_stock'] = '';
		
					//confirmation for prod stock check for shoes
					if($prod['menuid'] == 123)
						$prod['confirm_stock'] = 'Footwear Stock Available : <input type="checkbox" name="confirm_stock" value="1" >';
		
					$prod['stock']=(($stock_tmp[0][0]['stk']>0)?$stock_tmp[0][0]['stk']:0);
					unset($prod['is_combo']);
			}else
			{
				$menu = $this->db->query("select c.name
												from king_dealitems a
												join king_deals b on a.dealid = b.dealid
												join pnh_menu c on c.id = b.menuid
												where pnh_id = ? ",$pid)->row()->name;
		
				$prod = array('error'=>$menu." Menu not linked to franchise");
			}
		}
		echo json_encode($prod);
	}
	
	/**
	 * function to check if mobno used for activation is registred or not 
	 */
	function jx_chkvalidmemmobno_imeiactv()
	{
		$this->erpm->auth();
		$mid = $this->input->post('memid');
		$fid = $this->input->post('fid');
		$mobno = $this->input->post('mobno');
		
		$memtype = $this->input->post('memtype');
		$output = array();
		$mobno_res = $this->db->query("select franchise_id from pnh_member_info where mobile = ? ",$mobno);
		if($mobno_res->num_rows())
		{
			$output['status'] = 'success';
			$output['msg'] = 'Registered Mobileno';
		}else
		{
			$output['status'] = 'error';
			$output['msg'] = 'Unregistered Mobileno';
		}
		echo json_encode($output);
	}
	
	
	/**
	 * function to load imeino Activation form  
	 */
	function pnh_imei_activation_log()
	{
		$user=$this->erpm->auth(CALLCENTER_ROLE|PNH_EXECUTIVE_ROLE);
		$data['page']="pnh_imei_activation_log";
		$this->load->view("admin",$data);
	}
	
	/**
	 * function to load imeino Activation form  
	 */
	function pnh_franchise_activate_imei()
	{
		$user=$this->erpm->auth(CALLCENTER_ROLE);
		
		$data['fran_list'] = $this->db->query("select franchise_id,franchise_name from pnh_m_franchise_info where is_suspended = 0 order by franchise_name ");	
		$data['page']="pnh_franchise_activate_imei";
		
		$this->load->view("admin",$data);
	}
	
	/**
	 * function to Validate IMEINO 
	 */
	function _validate_imeinoforactivation($imeino)
	{
		$franid = $this->input->post('franid');
		if(!$imeino)
			return true;
		
		// Check for valid imei 
		if(!$this->db->query("select count(*) as t from t_imei_no where imei_no = ? and status = 1 ",$imeino)->row()->t)
		{
			$this->form_validation->set_message('_validate_imeinoforactivation','Invalid IMEI/Serialno Entered');
			return false;
		}
		
		// Check if imeino is assigned for franchise order   
		if(!$this->db->query("select count(*) as t from t_imei_no a join king_orders b on a.order_id = b.id join king_transactions c on c.transid = b.transid where a.imei_no = ? and c.franchise_id = ? ",array($imeino,$franid))->row()->t)
		{
			$this->form_validation->set_message('_validate_imeinoforactivation',' IMEI/Serialno is not sold to Franchise');
			return false;
		}
		
		// Check if imeino is already activated    
		if($this->db->query("select count(*) as t from t_imei_no where imei_no = ? and is_imei_activated = 1 ",$imeino)->row()->t)
		{
			$this->form_validation->set_message('_validate_imeinoforactivation',' IMEI/Serialno is already activated ');
			return false;
		}
		
		// Check if IMEI/Serialno is Delivered
		/*
		$shipped_status = $this->db->query("select a.imei_no,ifnull(c.status,0) as status from t_imei_no a join king_invoice b on a.order_id = b.order_id left join pnh_invoice_transit_log c on c.invoice_no = b.invoice_no where a.imei_no = ? order by c.id desc limit 1 ",array($imeino,$franid))->row()->status; 
		if($shipped_status != 3)
		{
			$this->form_validation->set_message('_validate_imeinoforactivation',' IMEI/Serialno is not Delivered');
			return false;
		}
		*/
		
		return true;
	}

	function _validate_memberidfor_registration()
	{
		$memid = $this->input->post('mem_id');
		$fid = $this->input->post('franid');
		
		if($this->db->query("select count(*) as t from pnh_member_info where pnh_member_id = ? ",$memid)->row()->t)
		{
			$this->form_validation->set_message('_validate_memberidfor_registration','MemberID Already Registered');
			return false;
		}
		
		// Check if New MEMBER ID is in franchise Alloted Member IDs Range
		$alloted_fid_res = $this->db->query("select franchise_id as fid from pnh_m_allotted_mid where ? between mid_start and mid_end ",$memid);
		if($alloted_fid_res->num_rows())
		{
			if($fid != $alloted_fid_res->row()->fid)
			{
				$this->form_validation->set_message('_validate_memberidfor_registration','MemberID Not Alloted to Franchise.');
				return false;	
			}
		}
		
		return true;
	}

	function _validate_mobnofor_registration()
	{
		$mobno = $this->input->post("mobno");
		$mem_type = $this->input->post('mem_type');
		$srch_no = $this->input->post('srch_no');
		
		$srch_no = $srch_no?$srch_no:''; 
		
		$is_mem_split_alloted = 0; 
		if($this->db->query("select count(*) as t from t_imei_no where imei_no = ? ",$srch_no)->row()->t)
		{
			$is_mem_split_alloted = @$this->db->query("select count(*) as t 
										from t_imei_no a 
										join king_orders b on a.order_id = b.id and b.status != 3 
										join king_transactions c on c.transid = b.transid 
										left join pnh_member_info d on b.userid = d.user_id 
										left join pnh_member_info e on b.member_id = e.pnh_member_id
									where imei_no = ? and a.status = 1 and member_id > 0 ;",$srch_no)->row()->t*1;	
		}
		
									
		//echo $this->db->last_query();			
		 
		if(!$mobno)
			$mobno = $this->input->post('v_mobno');
			
		$is_mob_reg = $this->db->query("select count(*) as t from pnh_member_info where mobile = ? ",$mobno)->row()->t;
		
		/* if($is_mob_reg)
		{
			$this->form_validation->set_message('_validate_mobnofor_registration','Mobileno Already Registered');
			return false;
		} */
		 
		 if($mem_type==0)
		{ 
			if($is_mob_reg)
			{
				$this->form_validation->set_message('_validate_mobnofor_registration','Mobileno Already Registered');
				return false;
			}
		}
		else
		{
			if(!$is_mob_reg && $is_mem_split_alloted == 0)
			{
				$this->form_validation->set_message('_validate_mobnofor_registration','Invalid Mobileno, Mobileno is not Registered');
				return false;
				//return true;
			}else
			{
				$process_total_imei = MAX_MEMBER_IMEI_ACTIVATIONS;
				// get no of days passed from last activation 
				$last_actv_res = $this->db->query("select datediff(curdate(),b.imei_activated_on) as d
														from t_imei_no b  
														join pnh_member_info c on c.pnh_member_id = b.activated_member_id  
														where c.mobile = ? and is_imei_activated = 1 
													order by imei_activated_on desc  
													limit ".MAX_MEMBER_IMEI_ACTIVATIONS,$mobno);
				if($last_actv_res->num_rows())
				{
					foreach($last_actv_res->result_array() as $last_actv)
					{
						if($last_actv['d'] < BLOCK_MEMBER_IMEI_ACTIVATION_DAYS)
						{
							$process_total_imei--;
						}
					}
				}
				
				$imeino_1 = $this->input->post('imeino_1');
				$imeino_2 = $this->input->post('imeino_2');
				$total_process = 0;
				if($imeino_1)
					$total_process++;
				if($imeino_2)
					$total_process++;
				
				if($process_total_imei == 0)
				{
					$this->form_validation->set_message('_validate_mobnofor_registration','Only '.MAX_MEMBER_IMEI_ACTIVATIONS.' Activations can be done in '.BLOCK_MEMBER_IMEI_ACTIVATION_DAYS.' Days of Last Activation.');
					return false;
				}else
				{
					
					if($process_total_imei < $total_process)
					{
						$this->form_validation->set_message('_validate_mobnofor_registration','Only '.$process_total_imei.'/'.MAX_MEMBER_IMEI_ACTIVATIONS.' Activations can be done.');
						return false;
					}
				}
				
			}

		
		}
		
		return true;
	}

	function _validate_voucherslno_activation()
	{
		$v_slno=$this->input->post('voucher_slno');
		$fid=$this->input->post('voucher_fid');
		$v_slno=explode(',',$v_slno);
		$voucher_slno=array();
		$voucher_slno['already_activated']=array();
		$voucher_slno['invalid']=array();
		$voucher_slno['not_activated']=array();
		
		foreach($v_slno  as $v)
		{
		
			$is_valid_vslno=$this->db->query("select * from pnh_t_voucher_details where voucher_serial_no in(?)  and franchise_id=?",array($v,$fid))->row_array();
			
			if(!$is_valid_vslno)
				array_push($voucher_slno['invalid'],$v);
			if($is_valid_vslno['status']<=1)
				array_push($voucher_slno['not_activated'],$v);
			if($is_valid_vslno['status']>=3)
				array_push($voucher_slno['already_activated'],$v);
				
		}
		
			if($voucher_slno['invalid'])
			{
				$this->form_validation->set_message('_validate_voucherslno_activation',implode(',',$voucher_slno['invalid']).'Invalid VoucherSlno');
				return false;
			}
			if($voucher_slno['not_activated'])
			{
				$this->form_validation->set_message('_validate_voucherslno_activation',implode(',',$voucher_slno['not_activated']).'Voucherslno not activated:payment not cleared');
				return false;
			}
			if($voucher_slno['already_activated'])
			{
				$this->form_validation->set_message('_validate_voucherslno_activation',implode(',',$voucher_slno['already_activated']).'Voucherslno already activated');
				return false;
			}
		else
			
			return true;
		
	}

	function jx_chkvalidimeino()
	{
		$fid = $this->input->post('fid');
		$imeino = $this->input->post('imei_no');
		
		$output = array();
		$output['status'] = 'error';
		if(!$fid)
		{
			$output['msg'] = 'Please enter memberID first';
		}else
		{
			$resp = $this->db->query("select b.member_id,imei_scheme_id,imei_reimbursement_value_perunit,b.transid,d.name as deal,a.imei_no,a.order_id,franchise_id,is_imei_activated 
													from t_imei_no a 
													left join king_orders b on a.order_id = b.id 
													left join king_transactions c on c.transid = b.transid 
													left join king_dealitems d on d.id = b.itemid 
													where imei_no = ? ",$imeino);
			if($resp->num_rows())
			{
				$imei_det = $resp->row_array();
				if(!$imei_det['order_id'])
				{
					$output['msg'] = 'IMEIno is not ordered yet';	
				}else if($imei_det['franchise_id'] != $fid)
				{
					$output['msg'] = 'Invalid IMEI Entered, IMEI not sold to this franchise';
				}else if($imei_det['franchise_id'] == $fid)
				{
					if($imei_det['is_imei_activated'])
					{
						$output['msg'] = 'IMEIno is already activated';	
					}else
					{
						if($imei_det['imei_scheme_id'])
						{
							$fr_name = $this->db->query('select franchise_name from pnh_m_franchise_info where franchise_id = ? ',$imei_det['franchise_id'])->row();
							$inv = $this->db->query("select invoice_no  from king_invoice where order_id = ? ",$imei_det['order_id'])->row()->invoice_no;
							$msg = $imei_det['deal'].'<br> <a href="'.site_url('admin/invoice/'.$inv).'" target="_blank">'.$inv.'</a>';
							$msg .= '<br> Activation Credit : <b style="font-size:12px;">Rs '.$imei_det['imei_reimbursement_value_perunit'].'</b>';
							$msg .= '<br> Alloted MemberID : <b style="font-size:12px;color:#555">'.$imei_det['member_id'].'</b>';
							$output['msg'] = $msg;
							$output['status'] = 'success';
							$output['alloted_member_id'] = $imei_det['member_id'];
							$output['franchise_id'] = $imei_det['franchise_id'];
						}else
						{
							$output['msg'] = 'IMEI not in scheme';
						}
					}
				}
			}else
			{
				$output['msg'] = 'Invalid IMEIno entered';
			}
		}
		echo json_encode($output);
	}
	
	/**
	 * function to process imeino Activation form data   
	 */
	function pnh_process_franchise_imei_activation()
	{
		error_reporting(E_ALL);		
		$user = $this->erpm->auth();	
		
		$franid = $this->input->post('franchise_id');
		$member_id = $this->input->post('member_id');
		$mobno = $this->input->post('mobno');
		$imeino = $this->input->post('imei_no');
		$actv_confrim = $this->input->post('actv_confrim');
		$mem_name = $this->input->post('mem_name');
		
		$imeino_list = array($imeino);
		
		if($actv_confrim)
			$member_id = $this->db->query("select pnh_member_id from pnh_member_info where mobile = ? ",$mobno)->row()->pnh_member_id;
		
		//update mobile no if member mob no not found.
		if(!$this->db->query('select mobile from pnh_member_info where pnh_member_id = ? ',$member_id)->row()->mobile)
		{
			$this->db->query("update pnh_member_info set first_name=?,mobile = ? where pnh_member_id = ? limit 1",array($mem_name,$mobno,$member_id));
			if($this->db->affected_rows()>=1)
			{
				$this->erpm->pnh_sendsms($mobno,"Congratulation & Welcome to StoreKing Family,Your Member id is:$member_id",$franid,$member_id,0);
			}
		}
		
		//check if memberID is already reached  max activations defined.
		
		$ttl_imei_actv_credit = 0;
		$this->db->query("update t_imei_no set activated_mob_no=?,activated_member_id=?,activated_by=?,is_imei_activated = 1,imei_activated_on = now() where is_imei_activated = 0 and status = 1 and imei_no = ? limit 1  ",array($mobno,$member_id,$user['userid'],$imeino));
		if($this->db->affected_rows())
		{
			$oid = $this->db->query('select order_id from t_imei_no where status = 1 and order_id != 0 and imei_no = ? ',$imeino)->row()->order_id;
			$imei_ref_id = $this->db->query('select id from t_imei_no where status = 1 and order_id != 0 and imei_no = ? ',$imeino)->row()->id;
			$invno = $this->db->query('select invoice_no from king_invoice where invoice_status = 1 and order_id = ? ',$oid)->row()->invoice_no;
			
			$imei_credit = $this->db->query('select imei_reimbursement_value_perunit as amt from king_orders a join t_imei_no b on a.id = b.order_id where b.imei_no = ? ',$imeino)->row()->amt;
			$ttl_imei_actv_credit += $imei_credit; 
			
			
			$member_userid = $this->db->query("select user_id from pnh_member_info where pnh_member_id = ? ",$member_id)->row()->user_id;
			$this->db->query("update king_orders set userid=?,member_scheme_processed=1 where id=? and member_scheme_processed=0",array($member_userid,$oid));
			
			// create creditnote document entry  
			$arr = array($franid,$imei_ref_id,$invno,$imei_credit,date('Y-m-d H:i:s'),$user['userid']);
			$this->db->query("insert into t_invoice_credit_notes (franchise_id,type,ref_id,invoice_no,amount,created_on,created_by) values(?,2,?,?,?,?,?)",$arr);
			$credit_note_id = $this->db->insert_id();
			
			//update credit note to account summary 
			$arr = array($franid,7,$credit_note_id,$invno,$imei_credit,'imeino : '.$imeino,1,date('Y-m-d H:i:s'),$user['userid']);
			$this->db->query("insert into pnh_franchise_account_summary (franchise_id,action_type,credit_note_id,invoice_no,credit_amt,remarks,status,created_on,created_by) values(?,?,?,?,?,?,?,?,?)",$arr);
			
			$this->db->query("update t_imei_no set ref_credit_note_id = ? where imei_no = ? and ref_credit_note_id = 0 ",array($credit_note_id,$imeino));
			
		
			$fran_det = $this->db->query("select franchise_id as id,login_mobile1 as mob from pnh_m_franchise_info where franchise_id = ? ",$franid)->row_array();
			
			//Compose IMEI/Serialno Activation Message
			$sms_msg = 'Congratulations!!! Your IMEINO : '.implode(',',$imeino_list).' Activated';
			if($ttl_imei_actv_credit)
			{
				$sms_msg .= ' and Amount of Rs '.($ttl_imei_actv_credit).' has been credited to your account';
				// create franchise credit note and update the same to pnh franchise account summary.
				$this->erpm->pnh_sendsms($fran_det['mob'],$sms_msg,$fran_det['id']);
			}
			
			$this->erpm->flash_msg('IMEI/Serailno Activation processed');
			
			//echo $this->db->last_query();
			
		}
		
		redirect('admin/pnh_franchise_activate_imei','refresh');
	}
	
	/**
 	* function to process new member registeration form 
 	*/	
	function pnh_process_franchise_memreg()
	{
		$user=$this->erpm->auth(CALLCENTER_ROLE);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('mobno','Mobileno','trim|required|integer|exact_length[10]|callback__validate_mobnofor_registration');
		
		//$this->form_validation->set_rules('unique_no','Mobileno','required');
		$this->form_validation->set_rules('fran_id','Franchise','trim|required');
		$this->form_validation->set_rules('member_name','Name','trim|required');
		$this->form_validation->set_rules('gender','Gender','trim|required');
		
		if($this->form_validation->run() == FALSE)
		{
			$this->pnh_activation("mem_reg");
		}
		else
		{
			$this->pnh_activation();
			$fid = $this->input->post('fran_id');
			$mem_name=$this->input->post('member_name');
			$mob_no=$this->input->post('mobno');
			$gender=$this->input->post('gender');
			
			$memid=$this->erpm->_gen_uniquememberid();
			if(!$mem_name)
				$mem_name='Store King Member:'.$memid;
			else 
				$mem_name;
			
			$this->db->query("insert into king_users(name,createdon)values(?,?)",array($mem_name,time()));
			$userid=$this->db->insert_id();
			$this->db->query("insert into pnh_member_info(user_id,franchise_id,pnh_member_id,mobile,created_on,created_by,first_name,gender)values(?,?,?,?,?,?,?,?)",array($userid,$fid,$memid,$mob_no,time(),$user['userid'],$mem_name,$gender));
			if($this->db->affected_rows()!=0)
			{
				$fran_det=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
				$fran_mob=$fran_det['login_mobile1'];
				$fran_name=$fran_det['franchise_name'];
				$this->erpm->pnh_sendsms($fran_mob,"Dear $fran_name Member registered successfully.Alloted member id:$memid",$fran_mob);
				$this->erpm->pnh_sendsms($mob_no,"Congratulation & Welcome to StoreKing Family,Your Member id is $memid",$fid,$memid,0);
				$this->erpm->flash_msg("Member Registered Successfully");
				redirect($_SERVER['HTTP_REFERER']);
			}	
			
		}
	}

	/**
	 * function to activate coupon
	 */
	function pnh_franchise_coupon_activation()
	{
		
		$user=$this->erpm->auth(CALLCENTER_ROLE);
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('v_mobno','Mobileno','trim|required|integer|exact_length[10]|callback__validate_mobnofor_registration');
		
		//$this->form_validation->set_rules('unique_no','Mobileno','required');
		$this->form_validation->set_rules('voucher_fid','Franchise','trim|required');
		$this->form_validation->set_rules('voucher_slno','Voucher Slno','trim|required|callback__validate_voucherslno_activation');
		
		if($this->form_validation->run() == FALSE)
		{
			
			$this->pnh_activation('coup_actv');
		}
		else
		{
			
			$this->pnh_activation();
		
			$fid=$this->input->post('voucher_fid');
			$v_slno=$this->input->post('voucher_slno');
			$mem_name=$this->input->post('voucher_mname');
			$mem_mobno=$this->input->post('v_mobno');
			$new_regd=0;
			$v_slno=explode(',',$v_slno);
			$voucher_value=0;
			
			$voucher=array();
			$voucher['valid']=array();
			$voucher['scratch_code']=array();
			if($this->db->query('select * from pnh_member_info where mobile=?',$mem_mobno)->num_rows()==0)
			{
				$new_regd=1;
				$memid=$this->erpm->_gen_uniquememberid();
				if(!$mem_name)
					$mem_name='Store King Member:'.$memid;
				$this->db->query("insert into king_users(name,createdon)values(?,?)",array($mem_name,time()));
				$userid=$this->db->insert_id();
				$this->db->query("insert into pnh_member_info(user_id,franchise_id,first_name,pnh_member_id,mobile,created_on)values(?,?,?,?,?,?)",array($userid,$fid,$mem_name,$memid,$mem_mobno,time()));
				$fran_det=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
				$fran_mob=$fran_det['login_mobile1'];
				$fran_name=$fran_det['franchise_name'];
				$this->erpm->pnh_sendsms($fran_mob,"Dear $fran_name Member registered successfully.Alloted member id:$memid",$fran_mob);
				$this->erpm->pnh_sendsms($mem_mobno,"Congratulation & Welcome to StoreKing Family,Your Member id is $memid",$memid);
					
			}
			else
			{ 
				$new_regd=0;
				$mem_info=$this->db->query('select * from pnh_member_info where mobile=?',$mem_mobno)->row_array();
				$memid=$mem_info['pnh_member_id'];
				$mem_name=$mem_info['first_name'].''.$mem_info['last_name'];
				
			}
			foreach ($v_slno as $v)
			{
				
				$v_slno_info=$this->db->query('select * from pnh_t_voucher_details where voucher_serial_no=?',$v)->row_array();
				$voucher_value+=$v_slno_info['customer_value'];
				$voucher_scratchcode=$v_slno_info['voucher_code'];
				array_push($voucher['valid'], $v_slno_info['voucher_serial_no']);
				array_push($voucher['scratch_code'], $v_slno_info['voucher_code']);
				
				$this->db->query('update pnh_t_voucher_details set status=3,member_id=?,is_activated=1,activated_on=? where voucher_serial_no=? and franchise_id=? and is_activated=0',array($memid,cur_datetime(),$v,$fid));
				$this->db->query("insert into pnh_voucher_activity_log(voucher_slno,franchise_id,member_id,transid,debit,credit,status)values(?,?,?,?,?,?,0)",array($v,$fid,$memid,0,0,$voucher_value));
			}
			if($this->db->affected_rows()!=0)
			{
				
				$notify_sms = "Dear $mem_name ,Congratulation on purchase of Rs $voucher_value StoreKing Voucher, Your Secret Code is ".implode(',',$voucher['scratch_code'])." for Voucher Id ".implode(',',$voucher['valid']).".Happy Shopping";
				
				$this->erpm->pnh_sendsms($mem_mobno,$notify_sms,$fid,0,1);
				
				//$this->erpm->pnh_sendsms($mem_mobno,'Dear '. $mem_name." ,Congratulation on purchase of Rs $voucher_value StoreKing Voucher, Your Secret Code  is ".implode(',',$voucher['scratch_code']).' for Voucher Id ' .implode(',',$voucher['valid']).' Happy Shopping ',$mem_mobno,0,1);
				
				$this->erpm->pnh_sendsms($fran_mob,'Voucher '.implode(',',$voucher['valid']). ' is alloted to member successfully.Happy Franchising',$fran_mob,0,2);
			}
			$this->erpm->flash_msg('Coupon Activated successfully');
			redirect('admin/pnh_activation/coup_actv');
		}
		
	}
	
	function jx_validmemvcode()
	{
		$v_code=$this->input->post('v_code');
		$mem_mobno=$this->input->post('mem_mobno');
		$v_code=explode(',',$v_code);
		$ttl_vcodes=sizeof($v_code);
		$valid_vcodes=0;
		$memid=$this->db->query("select pnh_member_id from pnh_member_info where mobile=?",$mem_mobno)->row()->pnh_member_id;
		
		$vcode=array();
		$vcode['invalid']=array();
		$vcode['redeemed']=array();
		$e=0;
		$un="";
		foreach($v_code as $v)
		{
			
			$valid_memvcode=$this->db->query("select * from pnh_t_voucher_details where voucher_code=? and member_id=?",array($v,$memid))->row_array();
			if(!$valid_memvcode)
				array_push($vcode['invalid'], $v);
			if($valid_memvcode['status']>3)
				array_push($vcode['redeemed'], $v);
		}
	
		if($vcode['invalid'])
		{
			$vcode['status']='invalid';
			$vcode['msg']=implode(',', $vcode['invalid']).' is invalid';
		}
		
		if($vcode['redeemed'])
		{
			$vcode['status']='redeemed';
			$vcode['msg']=implode(',', $vcode['redeemed']).' is already redeemed';
		}
		if(empty($memid))
		{
			$vcode['status']='error';
			$vcode['msg']=$mem_mobno.' is not registered';
		}
		echo json_encode($vcode);	
		
	}
	/**
	 * function to redeem coupon
	 */
	function pnh_franchise_coupon_redeemtion()
	{
		
		$user=$this->erpm->auth(CALLCENTER_ROLE);
		
		
		foreach(array("pid","qty","voucher_code","mem_mobno") as $i)
			$$i=$this->input->post($i);
		$voucher_codes=explode(',',$voucher_code);
		$is_strkng_membr=$this->db->query("select * from pnh_member_info where mobile=?",$mem_mobno)->row_array();
		
		$items=array();
		$pnh_ids=array();
		foreach($pid as $i=>$p)
		{ 
			if(!is_numeric($p))
				continue;
			$items[]=array("pid"=>$p,"qty"=>$qty[$i]);
			$pnh_ids[]=$p;
		}
		
		$total=0;$d_total=0;
		$itemnames=$itemids=array();
		
		//validate the voucher menu link block
		$dealmenu=array();
		$total_ordered_val=0;
		foreach($pnh_ids as $pid)
		{
			
			$menu_det=$this->db->query("select c.id,c.name,b.orgprice from king_deals a join king_dealitems b on b.dealid=a.dealid join pnh_menu c on c.id=a.menuid where b.pnh_id=?",$pid)->row_array();
			
			$dealmenu[]=$menu_det['id'];
			$total_ordered_val+=$menu_det['orgprice'];
		}
		
		if($voucher_codes)
		{
				$vmenu_list=array();
				foreach($voucher_codes as $v=> $vscode)
				{
					
					if($this->db->query("select count(1) as ttl from pnh_t_voucher_details where voucher_code=? and status=3",$vscode)->row()->ttl==0)
							show_error("Partially redeemed voucher cannot be processed using voucher secret code");
					
					
					$vdet=$this->db->query("select b.book_id,d.franchise_id
															from pnh_t_voucher_details a
															join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
															join pnh_t_book_allotment d on d.book_id=b.book_id
															where voucher_code=? and member_id=?  and is_activated=1 and a.status=3
															group by b.book_id",array($vscode,$is_strkng_membr['pnh_member_id']))->row_array();
					
					//get the book menus
					//$vmenus=$this->db->query("select id,name from  pnh_menu where id in (select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=?)",$vdet['book_id'])->result_array();

					$vmenus=$this->db->query("select id,name from  pnh_menu where find_in_set (id,(select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=?)) and id in (".implode(',',$dealmenu).") ",$vdet['book_id'])->result_array();
                                       
				   if(empty($vmenus))
				   {
						   $this->pdie("Sorry not able to use the different menu vouchers at time");
						   exit;
				   }
					
					foreach($vmenus as $m)
					{
						//check used multiple voucher same menu
						if($v > 0)
						{
							if(!in_array($m['id'],$vmenu_list))
								show_error("Sorry not able to use the different menu vouchers at time");
						}
						
						//check voucher menu linked to franchise
						$vmenu_list[]=$m['id'];
						$fmenu=$this->db->query("select * from pnh_franchise_menu_link where fid=? and menuid=? and status=1",array($vdet['franchise_id'],$m['id']))->row_array();
						if(empty($fmenu))
						{
							show_error("Sorry not able to place a order voucher menu not assigned for voucher franchise");
						}
						
						//check voucher menu orderd deal menu same
						if(!in_array($m['id'],$dealmenu))
							show_error("Voucher ".$vscode." only for ".$m['name']." products");
						
						//check ordered deal menus are same
						if(count(array_unique($dealmenu))>1)
							show_error("Voucher ".$vscode." only for ".$m['name']." products");
					}
					
					foreach($dealmenu as $mk=>$dm)
					{
						//check ordered deal menu linked to franchise
						$fmenu=$this->db->query("select * from pnh_franchise_menu_link where fid=? and menuid=? and status=1",array($vdet['franchise_id'],$dm))->row_array();
						if(empty($fmenu))
							show_error("Sorry not able to  place a order product menu not assined for voucher franchise ");
						
						
						//check ordered deal menus are same
						if(count(array_unique($dealmenu))>1)
							show_error("Sorry not able to place the different menu orders at time");
							
						
						//check if the deal is voucher book
						if($dm==VOUCHERMENU)
							show_error("Sorry not able to place the order");
							
						
					}
				}
			}
			
			//validate the voucher menu link block
			
		$fid=$this->db->query("SELECT GROUP_CONCAT(franchise_id) AS franchise_id,IF(is_alloted=1,1,0),IF(is_activated=1,1,0),IF(status=3,3,0)FROM pnh_t_voucher_details WHERE voucher_code IN(?)",$voucher_codes)->row()->franchise_id;
		if(empty($fid))
			echo '<script>alert("Not Authorized");");return false;</script>';
			
		$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id in(?)",$fid)->row_array();
		
			
			$total=0;$d_total=0;$c_total=0;
			$itemids=array();
			$itemnames=array();

		
			$voucher_code_used = array();
			
				foreach($voucher_codes as $v_code)
				{
					
					$voucher_margin=$this->db->query("select voucher_margin from pnh_t_voucher_details where voucher_code=?",$v_code);
	
					$is_voucher_activated=$this->db->query("select * from pnh_t_voucher_details where voucher_code=? and member_id=?  and is_activated=1 and status=3",array($v_code,$is_strkng_membr['pnh_member_id']))->row_array();
							if(!empty($is_voucher_activated))
						{
							
						
							$voucher_offran=$is_voucher_activated['franchise_id'];
							$voucher_value+=$is_voucher_activated['customer_value'];
							$voucher_code_used[$v_code] = $is_voucher_activated['customer_value'];
							$valid_voucher_count++;//count of correct coupon code entered by user
						}
					
				}
				foreach($items as $i=>$item)
				{
				
				
					//check the order product menu is linked to coupon assigned franchise
					$prod=$this->db->query("select i.*,d.publish,m.menu_margin from king_dealitems i join king_deals d on d.dealid=i.dealid JOIN `pnh_prepaid_menu_config`m ON m.menu_id=d.menuid where i.is_pnh=1 and i.pnh_id=? and i.pnh_id!=0 AND 1",$item['pid'])->row_array();
				
				
					$items[$i]['mrp']=$prod['orgprice'];
					if(@$fran['is_lc_store'])
						$items[$i]['price']=$prod['store_price'];
					else
						$items[$i]['price']=$prod['price'];
						
					$voucher_margin=@$is_voucher_activated['voucher_margin'];
					$items[$i]['itemid']=$prod['id'];
					if($prod['is_combo']=="1")
						$items[$i]['discount']=$items[$i]['price']/100*$margin['combo_margin'];
					else
						$items[$i]['discount']=$items[$i]['price']/100*$voucher_margin;
					//$items[$i]['discount']=$voucher_margin;
					$total+=$items[$i]['price']*$items[$i]['qty'];
					$d_total+=($items[$i]['price']-$items[$i]['discount'])*$items[$i]['qty'];
					$c_total+=($items[$i]['price'])*$items[$i]['qty'];
					$itemids[]=$prod['id'];
					$itemnames[]=$prod['name'];
					$items[$i]['margin']=$voucher_margin;
					$items[$i]['tax']=$prod['tax'];
					
					
				
				}
				$avail=$this->erpm->do_stock_check($itemids);
			$nonredeemed_value=0;
			$non_redeemed_vdetails=$this->db->query("select b.book_id,d.franchise_id,a.voucher_serial_no,a.customer_value,a.status as voucher_status
														from pnh_t_voucher_details a
														join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
														join pnh_t_book_allotment d on d.book_id=b.book_id
														where voucher_code!=? and member_id=?  and is_activated=1 and a.status=3
														group by voucher_serial_no",array($v_code,$is_strkng_membr['pnh_member_id']));
			
			if($non_redeemed_vdetails)
			{
				foreach($non_redeemed_vdetails->result_array() as $non_redeemed_vdet)
				{
					$non_redeemed_vmenu=$this->db->query("select id,name from  pnh_menu where id in (select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=? and menu_ids=?)",array($non_redeemed_vdet['book_id'],$m['id']));
					if($non_redeemed_vmenu->num_rows())
					{
						$nonredeemed_value+=$non_redeemed_vdet['customer_value'];
					}
				}
			}
			if(!$nonredeemed_value)
				$nonredeemed_value=0;
			
			$part_redeemed_vouch_amt=$this->db->query("select sum(customer_value) as amt from pnh_t_voucher_details where status=5 and customer_value!=0 and member_id=?",$is_strkng_membr['pnh_member_id'])->row()->amt;
			
			$voucher_fifo=0;      //flag to redeem if available voucher balance>0
			$non_redeemed_voucher_consideration=0;
			$part_redeemed_voucher_codes = array();
			
			$ts_vouchervalue=$voucher_value; //given secret code voucher value
			
			
			
			
			if($voucher_value<$c_total && $nonredeemed_value!=0)
			{
				$voucher_value=$voucher_value+$nonredeemed_value;
				$non_redeemed_voucher_consideration=1;
			
				$vcodetest=$ts_vouchervalue-$c_total;
				if($vcodetest<0)
					
				$required_amt=$vcodetest*-1;
				
				$sql=$this->db->query("select b.book_id,d.franchise_id,a.voucher_serial_no,a.customer_value,a.status as voucher_status
														from pnh_t_voucher_details a
														join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
														join pnh_t_book_allotment d on d.book_id=b.book_id
														where voucher_code!=? and member_id=?  and is_activated=1 and a.status in (3,5)
														group by voucher_serial_no",array($v_code,$is_strkng_membr['pnh_member_id']));
				if($sql)
				{
							$cc=array();
							$cc['code']=array();
							$cc['value']=array();
							$value=0;
						foreach($sql->result_array() as $s)
						{
							$s_vmenu=$this->db->query("select id,name from  pnh_menu where id in (select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=? and menu_ids=?)",array($non_redeemed_vdet['book_id'],$m['id']));
							if($s_vmenu->num_rows())
							{
								if($value>=$required_amt)
									break;
								$value+=$s['customer_value'];
								array_push($cc['code'],$s['voucher_serial_no']);
								array_push($cc['value'],$s['customer_value']);
							}
						}
						
					}
				
				}
			if($ts_vouchervalue<$c_total)  
			{	
				
				$non_redeemed_voucher_consideration=1;
				$req_voucher_val = $c_total-$ts_vouchervalue; //200
				
				
				//check if pending vouchers can be used for redeem for current order
				$part_redeemed_vouch_amt=$this->db->query("select sum(customer_value) as amt from pnh_t_voucher_details where status=5 and customer_value!=0 and member_id=?",$is_strkng_membr['pnh_member_id'])->row()->amt;
				$vbal='';
				if(($part_redeemed_vouch_amt+$voucher_value) < $c_total)
				{	
					if($part_redeemed_vouch_amt!=0)
					{
						$vbal="+ bal(".$part_redeemed_vouch_amt.")";
					}
					show_error("Insufficient credit! Total value of purchase is Rs $c_total and voucher value is Rs $voucher_value $vbal");
				}
			}
	
			
			$userid=$is_strkng_membr['user_id'];
			$transid=strtoupper("PNH".random_string("alpha",3).$this->erpm->p_genid(5));
			$this->db->query("insert into king_transactions(transid,amount,paid,mode,init,actiontime,is_pnh,franchise_id,voucher_payment) values(?,?,?,?,?,?,?,?,1)",array($transid,$c_total,$d_total,3,time(),time(),1,$fran['franchise_id']));
			
			
			foreach($items as $item)
			{
				
				$inp=array("id"=>$this->erpm->p_genid(10),"transid"=>$transid,"userid"=>$userid,"itemid"=>$item['itemid'],"brandid"=>"");
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
				if($item['itemid']!=null)
				{
				$this->db->insert("king_orders",$inp);
				
				$last_order_ids=$this->db->insert_id();
				$last_trans_id=$this->db->query("select transid from king_orders where sno=? limit 1",$last_order_ids)->row()->transid;
				$order_ids=$this->db->query("select group_concat(id) as order_ids from king_orders where transid=?",$last_trans_id)->row()->order_ids;

				$m_inp=array("transid"=>$transid,"itemid"=>$item['itemid'],"mrp"=>$item['mrp'],"price"=>$item['mrp'],"base_margin"=>0,"sch_margin"=>0,"bal_discount"=>0,"qty"=>$item['qty'],"final_price"=>$item['price']-$item['discount'],"voucher_margin"=>$voucher_margin);
				$this->db->insert("pnh_order_margin_track",$m_inp);
				}
			}
			$this->erpm->pnh_fran_account_stat($fran['franchise_id'],1, $d_total,"Order $transid - Total Amount: Rs $total","order",$transid);
			$points=$this->db->query("select points from pnh_loyalty_points where amount<? order by amount desc limit 1",$total)->row_array();
			if(!empty($points))
				$points=$points['points'];
			else $points=0;
		
			
			$ttl_voucher_amt_req = $c_total;
			$is_partially_redeemed=0;
			
			//echo $c_total;
			
			
			
			if($voucher_codes)
			{
				
				foreach($voucher_codes as $v_code)
				{
					
					if(!$ttl_voucher_amt_req)
						continue ;
					
					if($is_partially_redeemed)
						break;
						
					$is_alloted=$this->db->query("select * from pnh_t_voucher_details where franchise_id=? and voucher_code=? and is_alloted=1 and is_activated=1 and status=3 ",array($fran['franchise_id'],$v_code))->row_array();
					
					$cus_value = $is_alloted['customer_value'];
					$new_cus_value=($is_alloted['customer_value']-$ttl_voucher_amt_req);
					
					//echo $new_cus_value.'<br>';
					
					//get available voucher value from db
					//$db_voucher_value = $this->db->query("select customer_value from pnh_t_voucher_details where voucher_code = ? ",$v_code)->row()->customer_value;
					$is_fully_redeemed = 0;
					if($new_cus_value<=0)
					{
						$is_fully_redeemed = 1;
						$new_cus_value = 0; 
					}
					else
					{
						$is_partially_redeemed = 1;
					}
					
					$fran_value=$new_cus_value-($new_cus_value*$is_alloted['voucher_margin']/100);
						
					$this->db->query("update pnh_t_voucher_details set customer_value=?,franchise_value=?,status=?,redeemed_on=now() where voucher_code=?",array($new_cus_value,$fran_value,($is_fully_redeemed?4:5),$v_code));
					$this->db->query("insert into pnh_voucher_activity_log(voucher_slno,franchise_id,member_id,transid,debit,credit,order_ids,status)values(?,?,?,?,?,?,?,?)",array($is_alloted['voucher_serial_no'],$fran['franchise_id'],$is_strkng_membr['pnh_member_id'],$transid,$cus_value-$new_cus_value,0,$order_ids,1));
					
					$ttl_voucher_amt_req = $ttl_voucher_amt_req-($cus_value-$new_cus_value);
				}
				
				
				//exit;
				
				if($is_fully_redeemed && $non_redeemed_voucher_consideration==1)
				{
					
					
					$required_secretcode=$cc['code'];
					
					
					//echo $required_amt;
					//print_r($required_secretcode);
					
					if($required_secretcode)
					{
						foreach($required_secretcode as $required_vcode)
						{
							
							$vsres=$this->db->query("select * from pnh_t_voucher_details where voucher_serial_no=?",$required_vcode);
				
								if($vsres)
								{
									foreach($vsres->result_array() as $v)
									{
										if($is_partially_redeemed)
											break;
										
										$v_slno=$v['voucher_serial_no'];
										$v_margin=$v['voucher_margin'];
										$v_cusvalue=$required_amt-$v['customer_value'];//500-300
										//108-300 = -192
										$is_fully_redeemed=0;
										$is_partially_redeemed=0;
										
										$used_vouchval = 0;
										
										if($v_cusvalue < 0)
										{
											$v_cusvalue=$required_amt;
											//$v_cusvalue=$v['customer_value'];
											//$used_vouchval = 
											$is_partially_redeemed = 1;
										}
										else
										{
											$is_fully_redeemed = 1;
											$v_cusvalue = $v['customer_value'];
										}
										
										 
										
										$fran_value=($v['customer_value']-$v_cusvalue)-(($v['customer_value']-$v_cusvalue)*$v['voucher_margin']/100);
										
										//echo $v_cusvalue;
										
										
										$this->db->query("update pnh_t_voucher_details set customer_value=?,franchise_value=?,status=?,redeemed_on=now() where voucher_serial_no=?",array(($v['customer_value']-$v_cusvalue),$fran_value,($is_fully_redeemed?4:5),$v_slno));
										
										$this->db->query("insert into pnh_voucher_activity_log(voucher_slno,franchise_id,member_id,transid,debit,credit,order_ids,status)values(?,?,?,?,?,?,?,?)",array($v_slno,$fran['franchise_id'],$is_strkng_membr['pnh_member_id'],$transid,$v_cusvalue,0,$order_ids,1));
										
										
										$required_amt=$required_amt-$v_cusvalue;
										
									}
								}
									
						}
						
					}
				}
				
			}
			else 
			{
				
				$usebal_v=array();
				$usebal_v['pre_redeemed']=array();
				$bal_details=$this->db->query("select * from pnh_t_voucher_details where status=5 and customer_value!=0 and member_id=?",$is_strkng_membr['pnh_member_id'])->result_array();
				$v_prebal=0;
				foreach($bal_details as $bal_det)
				{
					
					$fran_value=$c_total-($c_total*$bal_det['voucher_margin']/100);
					$v_prebal+=$bal_det['customer_value'];
					if($v_prebal>=$c_total)
					{	
						array_push($usebal_v['pre_redeemed'],$bal_det['voucher_serial_no']);
						break;
					}else
						continue;
				}
				if($usebal_v['pre_redeemed'])
				{
					
					foreach ($usebal_v as $u_voucher)
					{
						
					$v_histry=$this->db->query("select * from pnh_t_voucher_details where voucher_serial_no=?",$u_voucher)->row_array();	
					$v_slno=$v_histry['voucher_serial_no'];
					$v_margin=$v_histry['voucher_margin'];
					$v_cusvalue=$v_histry['customer_value']-$c_total;
					$fran_value=$c_total-($c_total*$v_histry['voucher_margin']/100);
					$is_fully_redeemed=0;
					$is_partially_redeemed=0;
					if($v_cusvalue<=0)
						$is_fully_redeemed = 1;
					else
						$is_partially_redeemed = 1;
					
					$this->db->query("update pnh_t_voucher_details set customer_value=?,franchise_value=?,status=?,redeemed_on=now() where voucher_serial_no=?",array($v_cusvalue,$fran_value,($is_fully_redeemed?4:5),$v_slno));
					$this->db->query("insert into pnh_voucher_activity_log(voucher_slno,franchise_id,member_id,transid,debit,credit,order_ids,status)values(?,?,?,?,?,?,?,?)",array($v_slno,$fran['franchise_id'],$is_strkng_membr['pnh_member_id'],$transid,$c_total,0,$order_ids,1));
					}
				}
				
			}
			
			
			$franid=$fran['franchise_id'];
			$billno=10001;
			$nbill=$this->db->query("select bill_no from pnh_cash_bill where franchise_id=? order by bill_no desc limit 1",$franid)->row_array();
			if(!empty($nbill))
				$billno=$nbill['bill_no']+1;
			$inp=array("bill_no"=>$billno,"franchise_id"=>$franid,"transid"=>$transid,"user_id"=>$userid,"status"=>1);
			$this->db->insert("pnh_cash_bill",$inp);
			$this->erpm->do_trans_changelog($transid,"PNH Order placed through SMS by $from",$userid);
			if($part_redeemed_vouch_amt)
				$v_bal=$part_redeemed_vouch_amt;
			else
				$v_bal=0;
			
			if($vcode==1)
			{
				//$this->erpm->pnh_sendsms($from,"your balance after purchase is Rs $cus_value,previous redeemed balance $v_bal .Happy Shopping",$from,0,0,1);
				$this->erpm->pnh_sendsms($from,"your balance after purchase is Rs $cus_value,previous redeemed balance $v_bal .Happy Shopping",$from,0,1);
				//echo "your balance after purchase is Rs $cus_value,previous redeemed balance $v_bal .Happy Shopping";
			}
			else 
			{
				$this->erpm->pnh_sendsms($from,"your balance after purchase is Rs $v_cusvalue.Happy Shopping",$from,0,1);
			}
			redirect("admin/trans/$transid");
	}
	
	/**
	 * function to get list of active franchises 
	 */
	function jx_getfranlist_active()
	{
		$output = array();
		$fran_list_res = $this->db->query("select franchise_id,franchise_name from pnh_m_franchise_info where is_suspended = 0 order by franchise_name ");
		if($fran_list_res->num_rows())
		{
			$output['fran_list'] = $fran_list_res->result_array();
			$output['status'] = 'success';
		}else
		{
			$output['error'] = 'No franchises found';
			$output['status'] = 'error';
		}
		echo json_encode($output);
	}
	
	function test_marg($fid,$pid)
	{
		$margin=$this->erpm->get_pnh_margin($fid,$pid);
		print_r($margin);
	}
	
	function pnh_expire_scheme_discount($id)
	{
		$user=$this->auth(SPECIAL_MARGIN_UPDATE);
		$this->db->query("update pnh_sch_discount_brands set is_sch_enabled=0,modified_on=?,modified_by=? where id=?",array(time(),$user['userid'],$id));
	
		/*
		if($this->db->affected_rows()>0)
		{
			//id of the last modified scheme discount
			$id=$this->db->query("select id from pnh_sch_discount_brands order by modified_on desc limit 1")->row()->id;
			//franchise_id of the last modified scheme discount
			$fmenu=$this->db->query("select * from pnh_sch_discount_brands where id = $id limit 1")->row_array();
			//disable scheme discount in pnh_franchise_menu_link  table
			if($fmenu['sch_type']=2)
				$this->db->query("update pnh_super_scheme set is_active=0 where franchise_id=? and menu_id=? and cat_id=? and brand_id=? and is_active=1",array($fmenu['franchise_id'],$fmenu['menuid'],$fmenu['catid'],$fmenu['brandid']));
		}
		*/
		
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function pnh_expire_superscheme($id)
	{
		$user=$this->auth(true);
		$this->db->query("update pnh_super_scheme set is_active=0,modified_on=?,modified_by=? where id=? and is_active = 1 ",array(time(),$user['userid'],$id));
		$this->erpm->flash_msg("Super scheme disabled");
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function pnh_special_margin_deal($itemid)
	{
		$user=$this->auth(SPECIAL_MARGIN_UPDATE);
		
		foreach(array("special_margin","from","to","type") as $i)
			$$i=$this->input->post($i);
		if($type)
		{
			$offer=$this->db->query("select price from king_dealitems where id=?",$itemid)->row()->price;
			if($special_margin>$offer)
				show_error("Special margin price can't be greater than offer price (Rs $offer)");
			$special_margin=round(($offer-$special_margin)/$offer*100,2);
		}
		$from=strtotime($from);
		$to=strtotime("23:59:59 $to");
		$itm_det=$this->db->query("select orgprice,price from king_dealitems where id=?",$itemid)->row_array();
		$inp=array("special_margin"=>$special_margin,"i_mrp"=>$itm_det['orgprice'],"i_price"=>$itm_det['price'],"itemid"=>$itemid,"from"=>$from,"to"=>$to,"created_on"=>time(),"created_by"=>$user['userid'],"is_active"=>'1');
		$this->db->insert("pnh_special_margin_deals",$inp);
		$this->erpm->flash_msg("Special margin updated");
		if(!isset($_POST['internal']))
			redirect("admin/pnh_deal/$itemid");
	}
	
	function pnh_disable_special_margin($id)
	{
		$user=$this->auth(SPECIAL_MARGIN_UPDATE);
		if(!empty($id))
			$this->db->query("update pnh_special_margin_deals set is_active=0 where id=? limit 1",$id);
		if($this->db->affected_rows()>0)
		{
			$this->erpm->flash_msg("Special margin disabled");
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function pnh_pub_unpub_deals()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		foreach(array("action","itemids") as $i)
			$$i=$this->input->post($i);
		$ids=explode(",",$itemids);
		foreach(explode(",",$itemids) as $id)
			$this->db->query("update king_deals set publish=$action where dealid=? limit 1",$this->db->query("select dealid from king_dealitems where id=?",$id)->row()->dealid);
		$this->erpm->flash_msg(count($ids)." deals ".($action=="0"?"Unp":"P")."ublished");
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function pnh_editdeal($itemid)
	{
		$this->pnh_adddeal($itemid);
	}
	
	function pnh_adddeal($itemid=false)
	{
		error_reporting(E_ALL);
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if($_POST && !$itemid)
			$this->erpm->do_pnh_adddeal();
		else if($_POST)
			$this->erpm->do_pnh_updatedeal($itemid);
		
		if($itemid)
		{
			$data['deal']=$this->db->query("select d.*,i.*,d.description,d.keywords,d.tagline from king_dealitems i join king_deals d on d.dealid=i.dealid where i.id=?",$itemid)->row_array();
			
		}
		$data['page']="pnh_adddeal";
		$this->load->view("admin",$data);
	}
	
	function pnh_edit_fran($fid)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE|PNH_FRANCHISE_EDIT);
		if($_POST)
			$this->erpm->do_pnh_updatefranchise($fid);
		$data['fran']=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
		$data['selected_menu']=@$this->db->query("select group_concat(menuid) as m_ids from pnh_franchise_menu_link where status = 1 and fid=?",$fid)->row()->m_ids;
		$data['fran_menu']=$this->db->query("select a.id,a.name,b.menu_id,b.is_active from pnh_menu a left join pnh_prepaid_menu_config b on b.menu_id=a.id order by name asc")->result_array();
		$data['page']="pnh_addfranchise";
		$this->load->view("admin",$data);
	}

	function pnh_assign_exec($fid)
	{
		$user=$this->auth(true);
		if($_POST)
		{
			$admins=array_unique($this->input->post("admins"));
			$this->db->query("delete from pnh_franchise_owners where franchise_id=?",$fid);
			foreach($admins as $a)
				$this->db->query("insert into pnh_franchise_owners(admin,franchise_id,created_by,created_on) values(?,?,?,?)",array($a,$fid,$user['userid'],time()));
			$this->erpm->flash_msg("Executives assigned to franchise");
			redirect("admin/pnh_franchise/$fid");
		}
		$data['admins']=$this->db->query("select * from king_admin order by name asc")->result_array();
		$data['exec']=$this->db->query("select admin from pnh_franchise_owners where franchise_id=?",$fid)->result_array();
		$data['fid']=$fid;
		$data['page']="pnh_assign_exec";
		$this->load->view("admin",$data);
	}
	
	function pnh_addfranchise()
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE);
		if($_POST)
			$this->erpm->do_pnh_addfranchise();
		
		$data['fran_menu']=$this->db->query("select a.id,a.name,b.menu_id,b.is_active from pnh_menu a left join pnh_prepaid_menu_config b on b.menu_id=a.id order by name asc")->result_array();
		$data['page']="pnh_addfranchise";
		$this->load->view("admin",$data);
	}
	
	function pnh_app_versions()
	{
		$user=$this->auth(true);
		if($_POST)
		{
			$no=$this->input->post("no");
			$no=$no+1-1;
			if(empty($no) || $no==0)
				show_error("Invalid version number  : Not an integer");
			if($this->db->query("select 1 from pnh_app_versions")->num_rows()!=0 && $this->db->query("select 1 from pnh_app_versions where version_no>=?",$no)->num_rows()!=0)
				show_error("Invalid version number : Version number should be in ascending order");
			$inp=array("version_no"=>$no,"version_date"=>time(),"created_by"=>$user['userid']);
			$this->db->insert("pnh_app_versions",$inp);
			$this->erpm->flash_msg("App version added");
			redirect("admin/pnh_app_versions");
		}
		$data['page']="pnh_app_versions";
		$this->load->view("admin",$data);
	}
	
	function pnh_addtown()
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE);
		if($_POST)
			$this->erpm->do_pnh_addtown();
		$data['page']="pnh_addtown";
		$this->load->view("admin",$data);
	}
	
	function pnh_towns($tid=false)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE);
		$data['towns']=$this->erpm->pnh_gettowns($tid);
		if($tid!=false)
			$data['terry']=$this->db->query("select territory_name from pnh_m_territory_info where id=?",$tid)->row()->territory_name;
		$data['page']="pnh_towns";
		$this->load->view("admin",$data);
	}
	
	function upd_pnhtown()
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE);
		$town_id = $this->input->post('town_id');
		$town_name = trim($this->input->post('town_name'));
		$territory_id = $this->input->post('terr_id');
		
		if($town_name)
		{
			$this->db->query("update pnh_towns set territory_id = ?,town_name = ? where id = ? ",array($territory_id,$town_name,$town_id));
			
			$this->erpm->flash_msg("Town updated successfully");
		}
		redirect('admin/pnh_towns/'.$territory_id,'refresh');		
	}
	
	function pnh_jx_searchdeals()
	{
		$fid=$this->input->post('fid');
		$q=$this->input->post('q');
		$vcode=$this->input->post('vcode');
		$mem_mobno=$this->input->post('mem_mobno');
		$srch_type=$this->input->post('type');
		$vcode=explode(',',$vcode);
		
		if($fid)
		{
			$deal_list = $this->db->query("SELECT i.name,i.pnh_id,i.orgprice AS mrp,i.price,i.store_price,d.menuid
									 FROM king_dealitems i
									 JOIN king_deals d ON d.dealid=i.dealid
									 JOIN `pnh_franchise_menu_link` m ON m.menuid=d.menuid
									  WHERE i.is_pnh=1 AND i.name LIKE ? AND m.status=1 AND fid=? and publish = 1 and live = 1 
									 ",array("%$q%",$fid))->result_array();
			
			//check if any voucer menu product exist
			$voucher_prd=0;
			foreach($deal_list  as $k=>$deal)
			{
				if($deal['menuid']==VOUCHERMENU)
				{
					$voucher_prd=1;
					unset($deal_list[$k]);
				}
			}
			
			if($voucher_prd)
			{
				$voucher_products=$this->db->query("select c.name,c.pnh_id,c.orgprice AS mrp,c.price,c.store_price,d.menuid
															from pnh_m_book_template a
															join m_product_deal_link b on b.product_id=a.product_id
															join king_dealitems c on c.id=b.itemid
															join king_deals d on d.dealid=c.dealid
															where
															menu_ids in (select menuid from  pnh_franchise_menu_link where fid=? and status=1) and c.is_pnh=1 AND c.name LIKE ? ",array($fid,"%$q%"))->result_array();
				if($voucher_products)
				{
					foreach($voucher_products as $vd)
					{
						array_push($deal_list,$vd);
					}
				}
													
			}
			
			
		}else
		{	
			$deal_list = $this->db->query("select i.name,i.pnh_id,i.orgprice as mrp,i.price,i.store_price from king_dealitems i where i.is_pnh=1 and i.name like ?","%$q%")->result_array();
		}
		
		//serach deal for voucher slno
		$vcode=array_filter($vcode);
		$vcode=array_unique($vcode);
		
		if($vcode)
		{
			$deal_list=array();
			
			foreach($vcode as $v)
			{
				if($mem_mobno)
				{
					$mem_det=$this->db->query("select * from pnh_member_info where mobile=?",$mem_mobno)->row_array();
				}
				
				if(empty($mem_det))
				{
					echo '<script>alert("Error!!!\n Member not found for given mobile number.")</script>';
					return false;
				}
				
				$mid=$mem_det['pnh_member_id'];
				
				$v_exist_res=$this->db->query("select customer_value from pnh_t_voucher_details where voucher_code=?",$v);
				if($v_exist_res->num_rows())
				{
					$v_val=$v_exist_res->row_array();
					if($v_val['customer_value']==0)
					{
						echo '<script>alert("Error!!!\n Voucher '.$v.'dot not have balance")</script>';
						return false;
					}
				}else{
					echo '<script>alert("Error!!!\n voucher '.$v.' not found")</script>';
					return false;
				}
				
				$vdet=$this->db->query("select b.book_id,d.franchise_id
															from pnh_t_voucher_details a
															join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
															join pnh_t_book_allotment d on d.book_id=b.book_id
															where voucher_code=? and is_activated=1 and a.status in (3) and member_id=?
															group by b.book_id",array($v,$mid))->row_array();
					
				if(empty($vdet))
				{
					echo '<script>alert("Error!!!\n Please check voucher slno.")</script>';
					return false;
				}
				//get the book menus
				$vmenus=$this->db->query("select id,name from  pnh_menu where id in (select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=?)",$vdet['book_id'])->result_array();
				
				foreach ($vmenus as $vmenu)
				{
				$deal_det = $this->db->query("SELECT i.name,i.pnh_id,i.orgprice AS mrp,i.price,i.store_price,d.menuid
												 FROM king_dealitems i
												 JOIN king_deals d ON d.dealid=i.dealid
												 WHERE i.is_pnh=1 AND i.name LIKE ? AND d.menuid=? and publish = 1 and live = 1 ",array("%$q%",$vmenu['id']))->result_array();
				
				}

				foreach($deal_det as $d)
				{
					array_push($deal_list,$d);
				}
			
			}
			
		}else if(empty($vcode) && $srch_type=='v_redeem')
		{
			if(empty($vcode) || !$mem_mobno)
			{
				echo '<script>alert("Error!!!\n pleas input voucher code.\n pleas input member mobno.")</script>';
				return false;
			}
			if(!$mem_mobno)
			{
				echo "<script>alert('Error!!! pleas input Mobile no .')</script>";
				return false;
			}
				
		}
		
		
		
		foreach($deal_list as $d)
		{
			echo "<a href=\"javascript:void(0)\" onclick='add_deal_callb(\"{$d['name']}\",\"{$d['pnh_id']}\",\"{$d['mrp']}\",\"{$d['price']}\",\"{$d['store_price']}\")'>{$d['name']}</a>";
		}

	}
	
	function pnh_jx_searchcheque()
	{
		$q=$_POST['q'];
		foreach($this->db->query("SELECT r.*,b.bank_name AS submit_bankname,s.name AS submittedby,a.name AS admin,f.franchise_name,d.remarks AS submittedremarks,DATE(FROM_UNIXTIME(d.submitted_on)) AS submitted_on  
										FROM pnh_t_receipt_info r 
										LEFT JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id
									 	LEFT JOIN `pnh_m_bank_info` b ON b.id=d.bank_id 
									 	LEFT JOIN king_admin s ON s.id=d.submitted_by 
									 	JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id 
									 	LEFT OUTER JOIN king_admin a ON a.id=r.created_by 
									 	WHERE  f.is_suspended=0 AND r.is_active=1 AND r.payment_mode=1 AND
									  	instrument_no LIKE ?","%$q%")->result_array() as $c)
			echo "<a href=\"javascript:void(0)\" onclick='cheque_detail_callb(\"{$c["receipt_id"]}\",\"{$c["instrument_date"]}\",\"{$c["status"]}\",\"{$c['created_on']}\",\"{$c['instrument_no']}\")'>{$c['instrument_no']}</a>";
	}
	
	function jx_request_chequedetails()
	{
		$cheque_status=array();
		$cheque_status[0]='Pending';
		$cheque_status[1]='Realized';
		$cheque_status[2]='Cancelled';
		
		$output=array();
		$receipt_id=$this->input->post('search_receiptid');
		$receipt_det=$this->db->query("SELECT r.*,b.bank_name AS submit_bankname,s.name AS submittedby,a.name AS admin,f.franchise_name,d.remarks AS submittedremarks,DATE(FROM_UNIXTIME(d.submitted_on)) AS submitted_on,DATE(FROM_UNIXTIME(instrument_date)) AS instrument_date,DATE(FROM_UNIXTIME(activated_on)) AS activated_on,DATE(FROM_UNIXTIME(r.created_on)) AS created_on,d.created_on AS submittedon,act.name AS activatedby,         
										d.cancel_reason,d.cancelled_on,f.franchise_name,t.territory_name,tw.town_name,f.login_mobile1,f.login_mobile2     
										FROM pnh_t_receipt_info r 
										LEFT JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id
									 	LEFT JOIN `pnh_m_bank_info` b ON b.id=d.bank_id 
									 	LEFT JOIN king_admin s ON s.id=d.submitted_by 
									 	JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id 
									 	LEFT OUTER JOIN king_admin a ON a.id=r.created_by
										LEFT OUTER JOIN king_admin act ON act.id=r.activated_by
										JOIN pnh_m_territory_info t ON t.id=f.territory_id
										JOIN pnh_towns tw ON tw.id=f.town_id 
									 	WHERE  f.is_suspended=0 AND r.is_active=1 AND r.payment_mode!=0 and r.receipt_id=?",$receipt_id);
		if($receipt_det->num_rows())
		{
			$output['receipt_det']=$receipt_det->row_array();
			$output['cheque_status_list']=$cheque_status;
			$output['status']='success';
		}
		else
		{
			$output['status']='error';
		}
		echo json_encode($output);
	}
	
	function pnh_jx_searchbrand()
	{
		$b=$_POST['b'];
		foreach($this->db->query("SELECT d.brandid,d.catid,i.name,i.pnh_id,i.orgprice AS mrp,i.price,i.store_price,i.id AS itemid,b.name AS brand
									FROM king_deals d 
									JOIN king_dealitems i ON i.dealid=d.dealid 
									JOIN king_brands b ON b.id=d.brandid 
									LEFT JOIN pnh_special_margin_deals smd ON i.id = smd.itemid AND smd.from <= UNIX_TIMESTAMP() AND smd.to >=UNIX_TIMESTAMP() 
									WHERE i.is_pnh=1 AND b.name LIKE ?
									GROUP BY i.id 
									ORDER BY i.name ASC","%$b%")->result_array() as $b)
			echo "<a href=\"javascript:void(0)\" onclick='add_deal_callb(\"{$b['name']}\",\"{$b['pnh_id']}\",\"{$b['mrp']}\",\"{$b['price']}\",\"{$b['store_price']}\")'>{$b['name']}</a>";
		
	}
	
	function pnh_jx_loadtown()
	{
		$tid=$_POST['tid'];
		echo "<select name='town'>";
		foreach($this->db->query("select id,town_name from pnh_towns where territory_id=?",$tid)->result_array() as $t)
			echo '<option value="'.$t['id'].'">'.$t['town_name'].'</option>';
		echo "</select>";
	}
	
	function pnh_print_franchisesbyterritory($tid)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE|CALLCENTER_ROLE);
		$data['frans']=$this->erpm->pnh_getfranchisesbyterry($tid);
		$data['pagetitle']="Franchises of territory:".$this->db->query("select territory_name as n from pnh_m_territory_info where id=?",$tid)->row()->n;
		$this->load->view("admin/body/print_franchisesbyterritory",$data);
	}
	
	function pnh_franchisesbyterritory($tid)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE|CALLCENTER_ROLE);
		$data['frans']=$this->erpm->pnh_getfranchisesbyterry($tid);
		$data['pagetitle']="Franchises of territory:".$this->db->query("select territory_name as n from pnh_m_territory_info where id=?",$tid)->row()->n;
		$data['page']="pnh_franchises";
		$this->load->view("admin",$data);
	}
	
	function pnh_franchisesbytown($tid)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE|CALLCENTER_ROLE);
		$data['frans']=$this->erpm->pnh_getfranchisesbytown($tid);
		$data['pagetitle']="Franchises of town:".$this->db->query("select town_name as n from pnh_towns where id=?",$tid)->row()->n;
		$data['page']="pnh_franchises";
		$this->load->view("admin",$data);
	}
	
	function pnh_franchises()
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE|CALLCENTER_ROLE);
		$data['frans']=$this->erpm->pnh_getfranchises();
		$data['suspended_frans_ttl']=$this->db->query("select count(*) as t from pnh_m_franchise_info where is_suspended = 1 ")->row()->t;
		$data['page']="pnh_franchises";
		$this->load->view("admin",$data);
	}
	
	function pnh_activate_receipt($rid=false)
	{
		$this->auth(FINANCE_ROLE);
		if(!$rid)
			show_error("Input Missing");
		else
		$this->erpm->do_pnh_activate_receipt($rid);
	}
	
	function pnh_cancel_receipt($rid=false)
	{
		$this->auth(FINANCE_ROLE);
		if(!$rid)
			show_error("Input Missing");
		else
		$this->erpm->do_pnh_cancel_receipt($rid);
	}
	
	function pnh_disenable_sch($fid,$en)
	{
		$this->auth(PNH_EXECUTIVE_ROLE|FINANCE_ROLE);
		$this->db->query("update pnh_m_franchise_info set is_sch_enabled=? where franchise_id=? limit 1",array($en,$fid));
		redirect("admin/pnh_franchise/$fid#sch_hist");
	}
	
	function pnh_pending_receipts($pg=0)
	{
		$user=$this->auth(FINANCE_ROLE);
		if($_POST)
		{
			if($_POST['type']=="act")
				$this->pnh_activate_receipt($_POST['rid']);
			else
				$this->pnh_cancel_receipt($_POST['rid']);
		}
		$data['receipts']=$this->erpm->pnh_getreceiptbytype($type,$st_date,$en_date,false,$pg);
		$data['total_value']=$total_rows=$this->erpm->pnh_getreceiptttl_valuebytype($type);
		
	//	$data['pagination'] = $this->_prepare_pagination(site_url('pnh_pending_receipts'),$total_rows,100,3);
		$data['page']="pnh_pending_receipts";
		$this->load->view("admin",$data);
	}
	
	function pnh_receiptsbytype($type=0,$st_date=false,$en_date=false,$pg=0,$export_rtype=false)
	{	
		$user=$this->auth(FINANCE_ROLE);
		if($st_date!=false && $en_date!=false && (strtotime($st_date)<=0 || strtotime($en_date)<=0))
			show_404();
		$export_rtype=$this->input->post('export_receiptdet');
		if($export_rtype)
			$this->erpm->do_gen_receiptsreport($export_rtype);
				
		$receipt_list =$this->erpm->pnh_getreceiptbytype($type,$st_date,$en_date,false,$pg);
		
		$data['total_rows']=$receipt_list[0];
		$data['receipts'] = $receipt_list[1];
		$data['total_value']=$total_rows=$this->erpm->pnh_getreceiptttl_valuebytype($type);
		$data['page']="pnh_pending_receipts";
		$data['st_date']=$st_date;
		$data['en_date']=$en_date;
		$data['pagetitle']="";
		$data['totalamount']=$this->db->query("select sum(receipt_amount) as totalamount from pnh_t_receipt_info")->row()->totalamount;
		$data['pagination'] = $this->_prepare_pagination(site_url('admin/pnh_receiptsbytype/'.$type.'/'.($st_date?$st_date:0).'/'.($en_date?$en_date:0)),$receipt_list[0],50,6);
		
		$this->load->view("admin",$data);
	}
	
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
	
	function pnh_receiptsbyterritory($type=0,$tid,$pg=0)
	{
		$data['territory_id']=$tid;
		//$data['receipts']=$this->erpm->pnh_getreceiptbytypeterry($type,$tid,$pg);
		$data['total_value']=$this->erpm->pnh_getreceiptttl_valuebytypeterry($type,$tid);
		$receipt_list=$this->erpm->pnh_getreceiptbytypeterry($type,$tid,$pg);
		$data['total_rows']=$receipt_list[0];
		$data['receipts'] = $receipt_list[1];
		$data['pagetitle']="Cheque's available for Territory:".$this->db->query("select territory_name as n from pnh_m_territory_info where id=?",$tid)->row()->n;
		$data['page']="pnh_pending_receipts";
		
		$data['pagination'] = $this->_prepare_pagination(site_url('admin/pnh_receiptsbyterritory/'.$type.'/'.$tid),$receipt_list[0],50,5);
		
		$this->load->view("admin",$data);
	}
	
	function pnh_receiptsbyfranchise($type=0,$fid,$pg=0)
	{
		$data['sel_fid'] = $fid;
		$data['total_value']=$this->erpm->pnh_getreceipttttl_valuebytypefran($type,$fid);
		$receipt_list=$this->erpm->pnh_getreceiptbytypefran($type,$fid,$pg);
		$data['total_rows']=$receipt_list[0];
		$data['receipts'] = $receipt_list[1];
		$data['pagetitle']="Cheque's available for Franchisee:".$this->db->query("select franchise_name as n from pnh_m_franchise_info where franchise_id=? and is_suspended=0",$fid)->row()->n;
		$data['page']="pnh_pending_receipts";
		$data['pagination'] = $this->_prepare_pagination(site_url('admin/pnh_receiptsbyfranchise/'.$type.'/'.$fid),$receipt_list[0],50,5);
		
		$this->load->view("admin",$data);
	}
	
	function pnh_receiptsbybank($type=0,$bid,$pg=0)
	{
		$data['bank_id']=$bid;
		$receipt_list=$this->erpm->pnh_getreceiptbytypebank($type,$bid,$pg);
		$data['total_rows']=$receipt_list[0];
		$data['receipts'] = $receipt_list[1];
		$data['pagetitle']="PNH Receipt Info :".$this->db->query("select bank_name as n from pnh_m_bank_info where id=? ",$bid)->row()->n;
		$data['total_value']=$this->erpm->pnh_getreceiptttl_valuebytypebank($type,$bid);
		$data['page']="pnh_pending_receipts";
		
		$data['pagination'] = $this->_prepare_pagination(site_url('admin/pnh_receiptsbybank/'.$type.'/'.$bid),$receipt_list[0],50,5);
		
		$this->load->view("admin",$data);
	}
	
	function pnh_receiptsbycashtype($type=0,$cashtype='',$pg=0)
	{
		
		$data['cash_type']=$cashtype;
		$receipt_list=$this->erpm->pnh_getreceiptbytypecash($type,$cashtype,$pg);
		$data['total_rows']=$receipt_list[0];
		$data['receipts'] = $receipt_list[1];
		$data['total_value']=$total_rows=$this->erpm->pnh_getreceiptttl_valuebytypecash($type,$cashtype);
		$data['page']="pnh_pending_receipts";
		
		$data['pagination'] = $this->_prepare_pagination(site_url('admin/pnh_receiptsbycashtype/'.$type.'/'.$cashtype?$cashtype:0),$receipt_list[0],50,5);
		
		$this->load->view("admin",$data);
	}
	
	function pnh_receiptsbyr_type($type=0,$r_type='',$pg=0)
	{
	
		$data['r_type']=$r_type;
		$receipt_list=$this->erpm->pnh_getreceiptbyr_type($type,$r_type,$pg);
		$data['total_rows']=$receipt_list[0];
		$data['receipts'] = $receipt_list[1];
		$data['total_value']=$total_rows=$this->erpm->pnh_getreceiptttl_valuebyrtype($type,$r_type);
		$data['page']="pnh_pending_receipts";
		
		$data['pagination'] = $this->_prepare_pagination(site_url('admin/pnh_receiptsbyr_type/'.$type.'/'.$r_type?$r_type:0),$receipt_list[0],50,5);
		
		$this->load->view("admin",$data);
	}
	
	function pnh_receiptsbytrans_type($type=0,$t_type='',$pg=0)
	{
		
		$data['t_type']=$t_type;
		
		$receipt_list=$this->erpm->pnh_getreceiptbytrans_type($type,$t_type,$pg);
		$data['total_rows']=$receipt_list[0];
		$data['receipts'] = $receipt_list[1];
		$data['total_value']=$total_rows=$this->erpm->pnh_getreceiptttl_valuebytranstype($type,$t_type);
		$data['page']="pnh_pending_receipts";
		$data['pagination'] = $this->_prepare_pagination(site_url('admin/pnh_receiptsbytrans_type/'.$type.'/'.$t_type?$t_type:0),$receipt_list[0],50,5);
		$this->load->view("admin",$data);
	}
	
	function pnh_receipt_upd_log($sd=0,$ed=0,$pg=0)
	{
		$user=$this->auth(FINANCE_ROLE);
		
		$filter_date_str = '';
		$cond = '';
		if($sd==0 || $ed == 0)
		{
			$data['sd']= date('Y-m-d');
			$data['ed']= date('Y-m-d');
			$filter_date_str = date('d/m/Y');
			$cond .= ' and date(from_unixtime(r.created_on)) = curdate() ';
		}else
		{
			$data['sd']= date('Y-m-d',strtotime($sd));
			$data['ed']= date('Y-m-d',strtotime($ed));
			$filter_date_str = format_date($sd).'-'.format_date($ed);
			$cond .= " and date(from_unixtime(r.created_on)) between date('$sd') and date('$ed') ";
		}
		
		$data['receipts']=$this->db->query("select r.*,f.franchise_name,a.name as admin from pnh_t_receipt_info r join pnh_m_franchise_info f on f.franchise_id=r.franchise_id left outer join king_admin a on a.id=r.created_by where status != 0 $cond order by r.created_on desc ")->result_array();
		
	
		
		$data['filter_date_str']=$filter_date_str;
		$data['page']="pnh_receipt_upd_log";
		$this->load->view("admin",$data);
	}
	
	function pnh_download_stat($fid)
	{
		$this->auth(PNH_EXECUTIVE_ROLE|FINANCE_ROLE);
		if($_POST)
		{
			if($_POST['op_type'] == 'xls')
				redirect('admin/to_download_franstat_exl_format/'.$fid.'/'.$_POST['from'].'/'.$_POST['to']);
			else
			{
				if($_POST['type'] == 'new')
					$this->erpm->do_pnh_download_stat_new(array($fid),$_POST['from'],$_POST['to']);
				else
					$this->erpm->do_pnh_download_stat(array($fid),$_POST['from'],$_POST['to']);
			}
			
		}
	}
	
	function pnh_sms_log($fid=null,$s_from=0,$s_to=0)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE|CALLCENTER_ROLE);
		if($fid==0)
			$fid=null;
		if(!$this->erpm->auth(CALLCENTER_ROLE,true))
			$this->erpm->is_franchise_auth($fid);
		$from=$to=0;
		if($s_from)
		{
			$from=strtotime($s_from);
			$to=strtotime($s_to);
		}
		$data['page']="pnh_sms_log";
		$sql="select o.reply_for,concat(f.franchise_name,', ',f.city) as franchise,l.franchise_id,l.sender as `from`,l.msg as input,o.msg as reply,l.created_on,o.created_on as reply_on from pnh_sms_log l join pnh_m_franchise_info f on f.franchise_id=l.franchise_id left outer join pnh_sms_log o on o.reply_for=l.id where ";
		if($fid)
			$sql.=" l.franchise_id=? and ";
		else $sql.="1 and ";
		$sql.=($from?"l.created_on between $from and $to and ":"")."l.reply_for=0 order by l.id desc".(($fid||$from)?"":" limit 40");
		$data['log']=$this->db->query($sql,$fid)->result_array();
		$data['fid']=$fid;
		
		$sql="select l.*,concat(f.franchise_name,', ',f.city) as franchise from pnh_sms_log_sent l join pnh_m_franchise_info f on f.franchise_id=l.franchise_id where ";
		if($fid)
			$sql.="l.franchise_id=? and ";
		else $sql.="1 and ";
		$sql.=($from?" l.sent_on between $from and $to ":"1 ")." order by l.id desc".(($fid||$from)?"":" limit 40");
		
		$data['erp']=$this->db->query($sql,$fid)->result_array();
		$this->load->view("admin",$data);
	}
	
	function pnh_comp_details()
	{
		$user=$this->auth();
		$data['page']="pnh_comp_details";
		$data['dets']=$this->db->query("select * from pnh_comp_details")->row_array();
		$this->load->view("admin",$data);
	}
		
	function pnh_franchise($fid)
	{
		
		$user=$this->auth(PNH_EXECUTIVE_ROLE|CALLCENTER_ROLE);
		
		$data['fran']=$this->erpm->pnh_getfranchise($fid);
		
		$data['fran_menu']=$this->db->query("SELECT m.id,m.name AS menu FROM `pnh_franchise_menu_link`a JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid JOIN pnh_menu m ON m.id=a.menuid WHERE a.status=1 AND b.franchise_id=?",$fid)->result_array();
			
		
		$data['receipts']=$this->db->query("select r.*,m.name AS modifiedby,a.name as admin,act.name as act_by,d.remarks AS submittedremarks,sub.name AS submittedby,d.submitted_on,can.cancelled_on,can.cancel_reason from pnh_t_receipt_info r LEFT OUTER JOIN `pnh_m_deposited_receipts`can ON can.receipt_id=r.receipt_id left outer join king_admin a on a.id=r.created_by left outer join king_admin act on act.id=r.activated_by LEFT OUTER JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id LEFT OUTER JOIN king_admin sub ON sub.id=d.submitted_by LEFT OUTER JOIN king_admin m ON m.id=r.modified_by where franchise_id=? group by r.receipt_id",$fid)->result_array();
		
		
		//$data['pending_receipts']=$this->db->query("SELECT r.*,m.name AS modifiedby,f.franchise_name,a.name AS admin FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin m ON m.id=r.modified_by WHERE r.status=0 AND r.is_active=1 and is_submitted=0 and r.status=0 and r.franchise_id=?  ORDER BY instrument_date asc",$fid)->result_array();
		//$data['pending_ttlvalue']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by WHERE r.status=0 AND r.is_active=1  AND is_submitted=0 and r.status=0 and r.franchise_id=?  ORDER BY instrument_date asc",$fid)->row_array();

		//$data['processed_receipts']=$this->db->query("SELECT r.*,b.bank_name AS submit_bankname,s.name AS submittedby,a.name AS admin,f.franchise_name,d.remarks AS submittedremarks,DATE(d.submitted_on) AS submitted_on  FROM pnh_t_receipt_info r LEFT JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id LEFT JOIN `pnh_m_bank_info` b ON b.id=d.bank_id LEFT JOIN king_admin s ON s.id=d.submitted_by JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id left outer join king_admin a on a.id=r.created_by  WHERE r.is_submitted=1 AND r.status=0  and r.is_active=1 and r.franchise_id=?  group by r.receipt_id order by d.submitted_on desc",$fid)->result_array();
		//$data['processed_ttlvalue']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r LEFT JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id LEFT JOIN `pnh_m_bank_info` b ON b.id=d.bank_id LEFT JOIN king_admin s ON s.id=d.submitted_by JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id left outer join king_admin a on a.id=r.created_by WHERE  r.is_submitted=1 AND r.status=0  and r.is_active=1 and r.franchise_id=?  order by d.submitted_on desc",$fid)->row_array();

		//$data['realized_receipts']=$this->db->query("SELECT r.*,f.franchise_name,a.name AS admin,d.username AS activated_by FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by WHERE r.status=1 AND r.is_active=1 AND (is_submitted=1 or r.activated_on!=0) and r.is_active=1 and r.franchise_id=? group by r.receipt_id ORDER BY activated_on desc",$fid)->result_array();
		//$data['realized_ttlvalue']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by WHERE r.status=1 AND r.is_active=1 AND (is_submitted=1 or r.activated_on!=0) and r.is_active=1 and r.franchise_id=? ORDER BY activated_on desc",$fid)->row_array();
		
		//$data['cancelled_receipts']=$this->db->query("SELECT r.*,f.franchise_name,a.name AS admin,d.username AS activated_by ,c.cancel_reason,c.cancelled_on FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id left JOIN `pnh_m_deposited_receipts`c ON c.receipt_id=r.receipt_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by WHERE r.status in (2,3) AND r.is_active=1 AND r.is_active=1 AND  r.franchise_id=? group by r.receipt_id  ORDER BY cancelled_on DESC",$fid)->result_array();
		//$data['cancelled_ttlvalue']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id left JOIN `pnh_m_deposited_receipts`c ON c.receipt_id=r.receipt_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by WHERE r.status in (2,3) AND r.is_active=1 AND r.is_active=1 AND  r.franchise_id=? ORDER BY cancelled_on DESC",$fid)->row_array();
		 
			
		/*$data['account_stat']=$this->db->query("SELECT debit_amt,credit_amt,remarks,status,created_on
														FROM `pnh_franchise_account_summary` a 
														WHERE a.franchise_id=? and (a.action_type = 5 or a.action_type = 6) 
														order by created_on desc",$fid)->result_array();*/
				
														
		$data['devices']=$this->db->query("select dm.created_on,di.id,di.device_sl_no,d.device_name from pnh_m_device_info di join pnh_m_device_type d on d.id=di.device_type_id join pnh_t_device_movement_info dm on dm.device_id=di.id where di.issued_to=?",$fid)->result_array();
		
		//prepaid det
		$data['have_prepaid_menu']=$this->erpm->check_franchise_have_prepaid_menu($fid);
		$data['is_prepaid']=$data['fran']['is_prepaid'];
		//prepaid det end
		
		$data['is_membrsch_applicable']=$this->db->query("SELECT m.name AS menu FROM pnh_franchise_menu_link a JOIN pnh_menu m ON m.id= a.menuid WHERE a.status=1 AND menuid=112 AND fid=?",$fid)->row_array();		
		//$data['is_mbrsch_active']=$this->db->query("select 1 from strking_member_scheme where is_active=1 and franchise_id=? order by created_on desc limit 1",$fid)->row();
		$data['fran_status']=$this->db->query("SELECT is_suspended FROM pnh_m_franchise_info WHERE franchise_id=?",$fid)->row()->is_suspended;
		$data['franchise_id']=$fid;
		$data['page']="pnh_franchise";
		$this->load->view("admin",$data);
	}
	
	//Unorderd franchise details
	function pnh_unorderd_log($pg=0)
	{
		$this->load->library('pagination');
		$config['base_url']=site_url('admin/pnh_unorderd_log/');
		$config['total_rows']=$this->db->query('SELECT COUNT(DISTINCT b.pnh_franchise_id) AS total,a.franchise_id,b.franchise_name,login_mobile1,login_mobile2,FROM_UNIXTIME(actiontime) AS last_orderd
												FROM king_transactions a
												RIGHT JOIN `pnh_m_franchise_info`b ON b.franchise_id=a.franchise_id
												WHERE a.is_pnh=1  AND FROM_UNIXTIME(init)<=(CURDATE()-INTERVAL 3 DAY)')->row()->total;
		$config['per_page']=MAX_ROWS_DISP;
		$config['uri_segment'] = 3;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$pagination = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		
		$data['unorderd_fran']=$unorderd_fran=$this->erpm->get_pnh_unorderdfran($pg);
		$data['page']='pnh_unorderdfran_log';
		$data['pagination'] =$pagination;
		$data['pg']=$pg;
		$this->load->view('admin',$data);
		
	}
	
	function pnh_update_unorderd_log($franchise_id='')
	{
		
		$user=$this->auth();
		$query_msg=$this->input->post('msg');
		
		$last_orderd=date('Y-m-d H:i:s',$this->input->post('last_orderon'));
		
		$notify=$this->input->post('admin_notify');
		$ins_data=array();
		$ins_data['franchise_id']=$franchise_id;
		$ins_data['is_notify']=$notify;
		$ins_data['msg']=$query_msg;
		$ins_data['last_orderd']=$last_orderd;
		$ins_data['created_by']=$user['userid'];
		$ins_data['created_on']=date('Y-m-d H:i:s');
		$this->db->insert('pnh_franchise_unorderd_log',$ins_data);
		$this->erpm->flash_msg("Message  Added");
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	
	function pnh_executive_account_log($fil_date='')
	{
		
		$user=$this->auth();
		if($_POST)
		{
			$this->erpm->do_pnh_update_execu_account_log();
			redirect('admin/pnh_executive_account_log');
			exit;
		}
		
		if($fil_date == '')
			$fil_date = date('Y-m-d');
		
		$data['pnh_exec_account_details']=$this->db->query("SELECT a.id as log_id,b.contact_no,a.msg,a.remarks,a.reciept_status,b.name,b.name AS employee_name,c.name AS updatedby_name,DATE_FORMAT(a.updated_on,'%d/%m/%y %h:%i %p') AS updated_on,DATE_FORMAT(a.logged_on,'%d/%m/%y %h:%i %p') AS logged_on
												FROM `pnh_executive_accounts_log` a
												JOIN m_employee_info b ON b.employee_id=a.emp_id
												LEFT JOIN king_admin c ON c.id=a.updated_by 
												where date(a.logged_on) = ?  and lower(type)='paid' 
											",$fil_date); 
		
		
		$data['fil_date'] = $fil_date;
		$data['page']='pnh_executive_accounts_log';
		$this->load->view("admin",$data);
	}
	
	function pnh_special_margins()
	{
		$user=$this->auth(OFFLINE_ORDER_ROLE);
		$data['deals']=$this->db->query("select i.orgprice,i.price,s.from,s.to,i.pnh_id,i.name,i.id,s.special_margin from king_dealitems i join pnh_special_margin_deals s on s.itemid=i.id where ? between s.from and s.to",time())->result_array();
		$data['page']="pnh_special_margins";
		$this->load->view("admin",$data);
	}
	

	/**
	 * function to load form to  add credits to franchise 
	 */
	function pnh_add_credits()
	{
		$user=$this->auth(PNH_ADD_CREDIT);
		$data['page']="pnh_add_credit";
		$this->load->view("admin",$data);
	}
	
	/**
	 * function to load last credit details of franchise   
	 * 
	 */
	function jx_franchaise_det()
	{
		$output = array();
		$fid = $this->input->post('fid'); 
		$f_last_credit_info_res = $this->db->query("select b.franchise_id,b.pnh_franchise_id,
											b.franchise_name,b.login_mobile1,b.login_mobile2,d.territory_name,e.town_name,
											ifnull(a.credit_added,0) as credit_added,
											ifnull(b.current_balance,0) as current_balance,
											round((b.current_balance+b.credit_limit),2) as available_limit,
											ifnull(a.credit_given_by,0) as credit_given_by,
											ifnull(a.new_credit_limit,0) as new_credit_limit,
											ifnull(c.name,'') as credit_given_by_name,
											a.reason,
											a.created_on 
								from pnh_m_franchise_info b  
								left join pnh_t_credit_info a on a.franchise_id = b.franchise_id
								left join king_admin c on a.credit_given_by = c.id 
								join pnh_m_territory_info d on d.id=b.territory_id
								join pnh_towns e on e.id=b.town_id
								where b.pnh_franchise_id = ? order by a.id desc limit 1",$fid);
		if($f_last_credit_info_res->num_rows())
		{
			$output['f_det'] = $f_last_credit_info_res->row_array();
			$fr_credit_det = $this->erpm->get_fran_availcreditlimit($output['f_det']['franchise_id']);
			$output['f_det']['available_limit'] = $fr_credit_det[3];
			$output['f_det']['created_on'] = format_datetime_ts($output['f_det']['created_on']);
			
			$is_prepaid_franchise=$this->erpm->is_prepaid_franchise($output['f_det']['franchise_id']);
			if($is_prepaid_franchise)
			{
				$output['status'] = 'error';
				$output['message'] = 'This is prepaid franchise not able to add credit';
			}
		}
		else
		{
			$output['status'] = 'error';
			$output['message'] = 'Invalid OR Franchise not found';
		}
		echo json_encode($output);
		
	}
	
	/**
	 * function to load franchise list 
	 * 
	 */
	function jx_get_franchiselist(){
		$output = array();
		 
		$f_list_res = $this->db->query("select franchise_id,pnh_franchise_id,franchise_name from pnh_m_franchise_info a order by franchise_name asc ");
		if($f_list_res->num_rows())
		{
			$output['f_list'] = $f_list_res->result_array();
			$output['status'] = 'success';
		}
		else
		{
			$output['status'] = 'error';
			$output['message'] = 'No Franchises found';
		}
		echo json_encode($output);
	}
	
	/**
	 * function to process add credit for franchise 
	 * 
	 */
	function pnh_process_add_credit()
	{
		$output = array();
		$user=$this->auth(PNH_ADD_CREDIT);
		$fid_list=$this->input->post("fid");
		$new_credit_list=$this->input->post("new_credit");
		$new_credit_reason=$this->input->post("new_credit_reason");
		
		$total_fids = count($fid_list);
		$affected = 0;
		if($total_fids)
		{
			
			foreach($fid_list as $fid)
			{
				$is_prepaid_franchise=$this->erpm->is_prepaid_franchise($fid);
				if($is_prepaid_franchise)
					continue;
				
				$f=$this->db->query("select credit_limit from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
				$inp=array($fid,$new_credit_list[$fid],$new_credit_list[$fid]+$f['credit_limit'],$new_credit_reason[$fid],$user['userid'],$user['userid'],time());
				$this->db->query("insert into pnh_t_credit_info(franchise_id,credit_added,new_credit_limit,reason,credit_given_by,created_by,created_on) values(?,?,?,?,?,?,?)",$inp);
				$this->db->query("update pnh_m_franchise_info set credit_limit=? where franchise_id=?",array($new_credit_list[$fid]+$f['credit_limit'],$fid));
				$affected += $this->db->affected_rows();
			}
		}
		echo 1;
		die();
	}
	
	/*
	 * function to load generate account statement form  
	 */
	function pnh_gen_statement()
	{
		$this->auth(PNH_EXECUTIVE_ROLE|FINANCE_ROLE);
		$data['page']="pnh_gen_statement";
		$this->load->view("admin",$data);
	}
	
	/**
	 * function to generate account statements by fid and date range 
	 */
	function pnh_process_gen_statment(){
		
		$this->auth(PNH_EXECUTIVE_ROLE|FINANCE_ROLE);
		if($this->input->post('fids'))
		{	
			$fids = $this->input->post('fids');
			$from = $this->input->post('from');
			$to = $this->input->post('to');
			
			if(!$this->input->post('stat_type'))
				$this->erpm->do_pnh_download_stat($fids,$from,$to,0);
			else
				$this->erpm->do_pnh_download_stat_new($fids,$from,$to,0);
		}
		
	}
	
	function pnh_sch_discounts($fid=0,$s=0,$e=0)
	{
		$user=$this->auth(true);
		$from=$to=0;
		if($s)
		{
			$from=strtotime($s);
			$to=strtotime("23:59:59 $e");
		}
		$sql="select s.*,f.franchise_name,a.name as created_by from pnh_sch_discount_track s join pnh_m_franchise_info f on f.franchise_id=s.franchise_id left outer join king_admin a on a.id=s.created_by where 1 ";
		$title="Scheme discounts added";
		if($fid)
			$sql.=" and s.franchise_id=?";
		if($from)
			$sql.=" and s.created_on between $from and $to";
		if($fid)
			$title.=" for ".$this->db->query("select franchise_name from pnh_m_franchise_info where franchise_id=?",$fid)->row()->franchise_name;
		if($from)
			$title.=" between $s and $e";
		$data['pagetitle']=$title;
		$data['discs']=$this->db->query($sql,$fid)->result_array();
		$data['page']="pnh_sch_discounts";
		$this->load->view("admin",$data);
	}
	
	
	function pnh_give_sch_discount($fid)
	{
		$user=$this->auth(SPECIAL_MARGIN_UPDATE);
		foreach(array("discount","start","menu","end","reason","brand","cat") as $i)
			$$i=$this->input->post($i);
		$start=strtotime($start);
		$end=strtotime($end." 23:59:59");
		$target_value=$this->input->post('ttl_sales_value');
		$credit=$this->input->post('credit');
		$sch_expire=$this->input->post('expire_schdisc');
		if($sch_expire)
		{
			
			$this->db->query("update pnh_sch_discount_brands set is_sch_enabled=0 where franchise_id=? and menuid=? and brandid=? and catid=? ",array($fid,$menu,$brand,$cat));
			$this->db->query("update pnh_franchise_menu_link set sch_discount=?,sch_discount_start=?,sch_discount_end=?,is_sch_enabled=0 where fid=? and menuid=?",array($discount,$start,$end,$fid,$menu));
			
		}
		
		$this->db->query("update pnh_franchise_menu_link set sch_discount=?,sch_discount_start=?,sch_discount_end=?,is_sch_enabled=1 where fid=? and menuid=?",array($discount,$start,$end,$fid,$menu));
		$inp=array("franchise_id"=>$fid,"sch_menu"=>$menu,"catid"=>$cat,"brandid"=>$brand,"sch_discount"=>$discount,"sch_discount_start"=>$start,"sch_discount_end"=>$end,'reason'=>$reason,"created_by"=>$user['userid'],"created_on"=>time());
		if(empty($target_value))
			$inp['sch_type']='1';
		else
			$inp['sch_type']='2';
		$this->db->insert("pnh_sch_discount_track",$inp);
		$inp=array("franchise_id"=>$fid,"menuid"=>$menu,"discount"=>$discount,"valid_from"=>$start,"valid_to"=>$end,"brandid"=>$brand,"created_on"=>time(),"created_by"=>$user['userid'],"catid"=>$cat,"is_sch_enabled"=>'1',"sch_type"=>1);
		$this->db->insert("pnh_sch_discount_brands",$inp);
		
		$sch_id=$this->db->insert_id();
		$this->erpm->flash_msg("Scheme discount added");
		redirect("admin/pnh_franchise/$fid#sch_hist");
	}
	
	function pnh_give_super_sch($fid)
	{
		$user=$this->auth(true);
		foreach(array("credit","ttl_sales_value","super_schmenu","reason","brand","cat") as $i)
			$$i=$this->input->post($i);
		$d=date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y')))));
		$super_schstart=time();
		$super_schend= strtotime($d);
		$supersch_expire=$this->input->post('expire_supersch');
		if($supersch_expire)
		{
			$this->db->query("update pnh_super_scheme set is_active=0,modified_by=?,modified_on=? where franchise_id=? and menu_id=? and brand_id=? and cat_id=? ",array($user['userid'],time(),$fid,$super_schmenu,$brand,$cat));
				
		}
		$inp=array("franchise_id"=>$fid,"menu_id"=>$super_schmenu,"cat_id"=>$cat,"brand_id"=>$brand,"target_value"=>$ttl_sales_value,"credit_prc"=>$credit,"valid_from"=>$super_schstart,"valid_to"=>$super_schend,"created_by"=>$user['userid'],"created_on"=>time(),"is_active"=>1);
		$this->db->insert("pnh_super_scheme",$inp);
		$this->erpm->flash_msg("Super scheme added");
		redirect("admin/pnh_franchise/$fid#sch_hist");
	}
	
	
	
	function jx_check_schemexist($fid='')
	{
		error_reporting(E_ALL);
		ini_set('display_errors',1); 
		
		$output = array();
		$output['status'] = 'success';
		foreach(array("discount","start","menu","end","reason","brand","cat") as $i)
			$$i=$this->input->post($i);
		$target_value=$this->input->post('ttl_sales_value');
		$mscheme_target=$this->input->post("mbrsch_credit");
		$mscheme_menu=$this->input->post("mbr_schmenu");
		$mscheme_brand=$this->input->post("mbr_schbrand");
		$mscheme_cat=$this->input->post("mbr_schcat");
		
		$sch_expire=$this->input->post("expire_schdisc");
		$supersch_expire=$this->input->post("expire_supersch");
		$memsch_expire=$this->input->post("expire_msch");
		
		if($menu!=0)
			$menu_name=$this->db->query("select name from pnh_menu where id=?",$menu)->row()->name;
		
		if($brand!=0){
		$brand_name =@$this->db->query("select name from king_brands where id = ? ",$brand)->row()->name;
		$brand_name = $brand_name?'"'.$brand_name.'"':'All';
		}else 
			$brand_name="All Brands";
		if($cat!=0){
		$cat_name = @$this->db->query("select name from king_categories where id = ? ",$cat)->row()->name;
		$cat_name = $cat_name?'"'.$cat_name.'"':'All';}
		else 
			$cat_name="All categories";
		
		if($fid)
		{
			if($discount && !$target_value)
			{
				$fran_sch=$this->db->query('select * from pnh_sch_discount_brands where franchise_id=? and menuid like ? and brandid like ? and catid like ? and is_sch_enabled=1 ',array($fid,'%'.$menu.'%','%'.$brand.'%','%'.$cat.'%'))->result_array();
				foreach($fran_sch as $fransch)
				{
					if($fransch['valid_from']<time() && $fransch['valid_to']>time() && $fransch['is_sch_enabled']==1)
					{
						if(!$sch_expire)
						{
							$output['status'] = 'error';
							$output['message'] = 'Scheme discount for menu '.$menu_name.','.$brand_name.' '.$cat_name.' already exists ';
						}
					}else
					{
						$output['status'] = 'success';
					}
				}
			}
			else if($mscheme_target && $mscheme_brand)
			{
				if($mscheme_menu!=0)
					$menu_name=$this->db->query("select name from pnh_menu where id=?",$mscheme_menu)->row()->name;
				if($mscheme_brand!=0)
				{
					$brand_name =@$this->db->query("select name from king_brands where id = ? ",$mscheme_brand)->row()->name;
					$brand_name = $brand_name?'"'.$brand_name.'"':'All';
				}
				if($mscheme_cat!=0)
				{
					$cat_name = @$this->db->query("select name from king_categories where id = ? ",$mscheme_cat)->row()->name;
					$cat_name = $cat_name?'"'.$cat_name.'"':'All';
				}
				
				if($mscheme_menu!=112)
				{
					$output['status'] = 'error';
					$output['message']='Member scheme Applied Only for Mobile & tablets';
				}
				
				$mem_fransch=$this->db->query('select * from imei_m_scheme where franchise_id=? and menuid like ? and brandid like ? and categoryid like ? and is_active=1 ',array($fid,'%'.$menu.'%','%'.$brand.'%','%'.$cat.'%'))->result_array();
				foreach($mem_fransch as $mfransch)
				{
					if($mfransch['scheme_from']<time() && $mfransch['scheme_to']>time() && $mfransch['is_active']==1)
					{
						if(!$memsch_expire)
						{
							$output['status'] = 'error';
							$output['message'] = 'Member Scheme for menu '.$menu_name.','.$brand_name.' '.$cat_name.' already exists ';
						}
					}else
					{
						$output['status'] = 'success';
					}
				}
			}
			else
			{
				$super_scheme=$this->db->query("select * from pnh_super_scheme where  franchise_id =? and menu_id like ? and brand_id like ? and cat_id like ? and is_active=1 ",array($fid,'%'.$menu.'%','%'.$brand.'%','%'.$cat.'%'))->result_array();
				foreach($super_scheme as $supersch)
				{
					if($supersch['valid_from']<=time()  && $supersch['is_active']==1)
					{
						if(!$supersch_expire)
						{
							$output['status'] = 'error';
							$output['message'] = 'Super Scheme for '.$brand_name.' '.$cat_name.' already exists ';
						}
					}else
					{
						$output['status'] = 'success';
					}
				}
			}
		}
		else
		{
			$output['status'] = 'error';
			$output['message'] = 'franchise id missing ';
		}
		echo json_encode($output);
	}
	
	function pnh_bulk_sch_discount()
	{
		$user=$this->auth(SPECIAL_MARGIN_UPDATE);
		if($_POST)
		{
			foreach(array("discount","start","end","reason","menu","brand","cat","fids","bulk_schtype","target_value","credit_prc","credit_value","mscheme_type","mscheme_val","msch_applyfrm","expire_prevsch") as $i)
				$$i=$this->input->post($i);
			if(empty($fids))
				show_error("No Franchises selected");
			$start=strtotime($start);
			$end=strtotime($end." 23:59:59");
			
			
			$msch_applyfrm=strtotime($msch_applyfrm." 00:00:00");
			
			foreach($fids as $fid)
			{
				if($bulk_schtype==1)//if scheme discount
				{
					if($brand==0)
					{
						$fran_sch=$this->db->query('select * from pnh_sch_discount_brands where franchise_id=? and menuid like ?  and catid like ? and is_sch_enabled=1 ',array($fid,'%'.$menu.'%','%'.$cat.'%'))->result_array();
						foreach($fran_sch as $fransch)
						{
							$this->db->query("update pnh_sch_discount_brands set is_sch_enabled=0 where franchise_id=? and menuid=? and catid=?",array($fid,$menu,$cat));
							$this->db->query("update pnh_franchise_menu_link set sch_discount=?,sch_discount_start=?,sch_discount_end=?,is_sch_enabled=0 where fid=? and menuid=?",array($discount,$start,$end,$fid,$menu));
						}
					}
					else
					 
					 {	
						$fran_sch=$this->db->query('select * from pnh_sch_discount_brands where franchise_id=? and menuid like ? and brandid like ? and catid like ? and is_sch_enabled=1 ',array($fid,'%'.$menu.'%','%'.$brand.'%','%'.$cat.'%'))->result_array();
						foreach($fran_sch as $fransch)
						{
							if($fransch['valid_from']<time() && $fransch['valid_to']>time() && $fransch['is_sch_enabled']==1)
							{
								$this->db->query("update pnh_sch_discount_brands set is_sch_enabled=0 where franchise_id=? and menuid=? and brandid=? and catid=? ",array($fid,$menu,$brand,$cat));
								$this->db->query("update pnh_franchise_menu_link set sch_discount=?,sch_discount_start=?,sch_discount_end=?,is_sch_enabled=0 where fid=? and menuid=?",array($discount,$start,$end,$fid,$menu));
							}
								
						}
					 }
					$inp=array("franchise_id"=>$fid,"sch_menu"=>$menu,"catid"=>$cat,"brandid"=>$brand,"sch_discount"=>$discount,"sch_discount_start"=>$start,"sch_discount_end"=>$end,'reason'=>$reason,"created_by"=>$user['userid'],"created_on"=>time(),"sch_type"=>1);
					$this->db->insert("pnh_sch_discount_track",$inp);
					$inp=array("franchise_id"=>$fid,"menuid"=>$menu,"discount"=>$discount,"valid_from"=>$start,"valid_to"=>$end,"brandid"=>$brand,"created_on"=>time(),"created_by"=>$user['userid'],"catid"=>$cat,"is_sch_enabled"=>1,"sch_type"=>1);
					$this->db->insert("pnh_sch_discount_brands",$inp);
					
				
				}
				
				
				if($bulk_schtype==2)
				{
					if($brand==0)
					{
						$fran_sch=$this->db->query('select * from pnh_super_scheme where franchise_id=? and menuid like ?  and catid like ? and is_sch_enabled=1 ',array($fid,'%'.$menu.'%','%'.$cat.'%'))->result_array();
						foreach($fran_sch as $fransch)
						{
							$this->db->query("update pnh_super_scheme set is_active=0,modified_by=?,modified_on=? where franchise_id=? and menu_id=?  and cat_id=? ",array($user['userid'],time(),$fid,$menu,$cat));
						}
					}
					else
					{
						$super_scheme=$this->db->query("select * from pnh_super_scheme where  franchise_id =? and menu_id like ? and brand_id like ? and cat_id like ? and is_active=1 ",array($fid,'%'.$menu.'%','%'.$brand.'%','%'.$cat.'%'))->result_array();
						foreach($super_scheme as $supersch)
						{
							if($supersch['valid_from']<=time()  && $supersch['is_active']==1)
							{
								$this->db->query("update pnh_super_scheme set is_active=0,modified_by=?,modified_on=? where franchise_id=? and menu_id=? and brand_id=? and cat_id=? ",array($user['userid'],time(),$fid,$menu,$brand,$cat));
							}
						}
						
						$d=date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y')))));
						$super_schstart=time();
						$super_schend= strtotime($d);
						$inp=array("franchise_id"=>$fid,"menu_id"=>$menu,"cat_id"=>$cat,"brand_id"=>$brand,"target_value"=>$target_value,"credit_prc"=>$credit_prc,"valid_from"=>$super_schstart,"valid_to"=>$super_schend,"created_by"=>$user['userid'],"created_on"=>time(),"is_active"=>1);
						$this->db->insert("pnh_super_scheme",$inp);
					}
				}
				
				
				
				if($bulk_schtype==3)
				{
					
					if($brand==0)
					{
						$mem_fransch=$this->db->query('select * from imei_m_scheme where franchise_id=? and menuid like ?  and categoryid like ? and is_active=1 ',array($fid,'%'.$menu.'%','%'.$cat.'%'))->result_array();
						foreach($mem_fransch as $mfransch)
						{
							$this->db->query("update imei_m_scheme set is_active=0, modified_by=?,modified_on=? where franchise_id=? and menuid=? and categoryid=?",array($user['userid'],time(),$fid,$menu,$cat));
						}
					}
					
					if(1)
					{
						$mem_fransch=$this->db->query('select * from imei_m_scheme where franchise_id=? and menuid like ? and brandid like ? and categoryid like ? and is_active=1 ',array($fid,'%'.$menu.'%','%'.$brand.'%','%'.$cat.'%'))->result_array();
						foreach($mem_fransch as $mfransch)
						{
							if($mfransch['scheme_from']<time() && $mfransch['scheme_to']>time() && $mfransch['is_active']==1)
							{
								$this->db->query("update imei_m_scheme set is_active=0, modified_by=?,modified_on=? where franchise_id=? and menuid=? and brandid=? and categoryid=?",array($user['userid'],time(),$fid,$menu,$brand,$cat));
							}
						}
							
						$inp=array("scheme_type"=>$mscheme_type,"franchise_id"=>$fid,"menuid"=>$menu,"categoryid"=>$cat,"brandid"=>$brand,"credit_value"=>$mscheme_val,"scheme_from"=>$start,"scheme_to"=>$end,"sch_apply_from"=>$msch_applyfrm,"created_by"=>$user['userid'],"created_on"=>time(),"is_active"=>1);
						$this->db->insert("imei_m_scheme",$inp);
						
						$sch_id = $this->db->insert_id();
						
						if($msch_applyfrm)
						{
							//apply imei scheme from apply from for franchise.
							$cond = '';
							if($menu)
								$cond .= ' and c.menuid = '.$menu;
							if($cat)
								$cond .= ' and c.catid = '.$cat;
							if($brand)
								$cond .= ' and c.brandid = '.$brand;
		
							$orders = $this->db->query("select a.id,(i_orgprice-(i_discount+i_coup_discount)) as amt   
														from king_orders a
														join king_dealitems b on a.itemid = b.id 
														join king_deals c on c.dealid = b.dealid 
														join king_transactions d on d.transid = a.transid 
														where d.franchise_id = ? $cond and d.init >= ? 
															  and imei_scheme_id = 0 and a.status != 3 ",array($fid,$msch_applyfrm));
															    
															
							if($orders->num_rows())
							{
								foreach($orders->result_array() as $order)
								{
									if($mscheme_type == 1)
										$imei_sch_amt = ($order['amt']*$mscheme_val/100);
									else 
										$imei_sch_amt = $mscheme_val;
											
									$this->db->query('update king_orders set imei_scheme_id = ?,imei_reimbursement_value_perunit=? where id = ? ',array($sch_id,$imei_sch_amt,$order['id']));
								}
							}
						}
						
						
						
					}
	
				}
					
			}

			
				
			$this->erpm->flash_msg("Scheme added");
			redirect("admin/pnh_bulk_sch_discount");
		}
		$data['page']="pnh_bulk_sch_discount";
		$this->load->view("admin",$data);
	}
	
	function pnh_loyalty_points()
	{
		$user=$this->auth(true);
		if($_POST)
		{
			foreach(array("amount","points") as $i)
				$$i=$this->input->post("$i");
			$this->db->query("truncate pnh_loyalty_points");
			foreach($amount as $i=>$a)
				if(!empty($a))
					$this->db->query("insert into pnh_loyalty_points(amount,points) values(?,?)",array($a,$points[$i]));
			redirect("admin/pnh_loyalty_points");
		}
		$data['page']="pnh_loyalty_points";
		$this->load->view("admin",$data);
	}
	
	function pnh_members($fid=0)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE|CALLCENTER_ROLE);
		$data['page']="pnh_members";
		if($fid)
			$data['users']=$this->db->query("select u.*,count(o.transid) as orders,f.franchise_name as fran from pnh_member_info u join pnh_m_franchise_info f on f.franchise_id=u.franchise_id left outer join king_orders o on o.userid=u.user_id where u.franchise_id=? group by u.user_id order by u.first_name desc",$fid)->result_array();
		else
			$data['users']=$this->db->query("select u.*,count(o.transid) as orders,f.franchise_name as fran from pnh_member_info u join pnh_m_franchise_info f on f.franchise_id=u.franchise_id left outer join king_orders o on o.userid=u.user_id group by u.user_id order by u.id desc limit 20")->result_array();
			
		if($fid)
			$data['pagetitle']=" of '".$this->db->query("select concat(franchise_name,', ',city) as name from pnh_m_franchise_info where franchise_id=?",$fid)->row()->name."'";
		$this->load->view("admin",$data);
	}

	function pnh_viewmember($uid)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE|CALLCENTER_ROLE);
		$data['page']="pnh_viewmember";
		$data['member']=$this->db->query("select u.*,count(o.transid) as orders,f.franchise_name as fran from pnh_member_info u left outer join king_orders o on o.userid=u.user_id left outer join pnh_m_franchise_info f on f.franchise_id=u.franchise_id where u.user_id=?",$uid)->row_array();
		$this->load->view("admin",$data);
	}
	
	function pnh_allot_mid($fid)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE);
		$start=$this->input->post("start");
		$end=$this->input->post("end");
		if($this->db->query("select 1 from pnh_m_allotted_mid where (? between mid_start and mid_end) or (? between mid_start and mid_end)",array($start,$end))->num_rows()!=0)
			show_error("Range already allotted to another franchise");
		$this->db->query("insert into pnh_m_allotted_mid(franchise_id,mid_start,mid_end,created_on,created_by) values(?,?,?,?,?)",array($fid,$start,$end,time(),$user['userid']));
		redirect("admin/pnh_franchise/$fid");
	}
	
	function pnh_franchise_bank_details($fid)
	{
		$this->auth(PNH_EXECUTIVE_ROLE|FINANCE_ROLE);
		if($_POST)
			$this->erpm->do_pnh_add_fran_bank_details($fid);
	}
	
	function pnh_give_credit()
	{
		$user=$this->auth(true);
		$fid=$this->input->post("fid");
		if($this->input->post("reduce"))
			$_POST['limit']=-$_POST['limit'];
		if(empty($_POST['limit']))
			show_error("Invalid credit limit");
		$f=$this->db->query("select credit_limit from pnh_m_franchise_info where franchise_id=?",$this->input->post("fid"))->row_array();
		$inp=array($this->input->post("fid"),$this->input->post("limit"),$this->input->post("limit")+$f['credit_limit'],$this->input->post("reason"),$user['userid'],$user['userid'],time());
		$this->db->query("insert into pnh_t_credit_info(franchise_id,credit_added,new_credit_limit,reason,credit_given_by,created_by,created_on) values(?,?,?,?,?,?,?)",$inp);
		$this->db->query("update pnh_m_franchise_info set credit_limit=? where franchise_id=?",array($this->input->post("limit")+$f['credit_limit'],$fid));
		redirect("admin/pnh_franchise/$fid");
	}
	
	function pnh_topup($fid)
	{
		$user=$this->auth(FINANCE_ROLE);
		if($_POST)
			$this->erpm->do_pnh_topup($fid);
		$data['fid']=$fid;
		$data['page']="pnh_topup";
		$this->load->view("admin",$data);
	}
	
	function pnh_removefdevice($did,$fid)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE);
		$this->db->query("update pnh_m_device_info set issued_to=0 where id=?",$did);
		$this->db->query("insert into pnh_t_device_movement_info(device_id,issued_to,created_by,created_on) values(?,0,?,?)",array($did,$user['userid'],time()));
		redirect("admin/pnh_manage_devices/$fid");
	}
	
	function pnh_manage_devices($fid)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE);
		if($_POST)
			$this->erpm->do_pnh_manage_devices($fid);
		$data['fid']=$fid;
		$data['devs']=$this->db->query("select di.id,di.device_sl_no,d.device_name from pnh_m_device_info di join pnh_m_device_type d on d.id=di.device_type_id where di.issued_to=?",$fid)->result_array();
		$data['page']="pnh_manage_devices";
		$this->load->view("admin",$data);
	}
	
	function pnh_deals()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE|CALLCENTER_ROLE);
		$data['deals']=$this->erpm->pnh_getdeals();
		$data['page']="pnh_deals";
		$this->load->view("admin",$data);
	}

	function pnh_dealsbycat($catid,$brandid,$type=0)
	{
		$user=$this->auth(DEAL_MANAGER_ROLE|CALLCENTER_ROLE);
		$data['deals']=$this->erpm->pnh_getdealsbycat($catid,$brandid,$type);
		$data['page']="pnh_deals";
		$data['brand']=false;
		$data['pagetitle']="PNH Deals by Brand - Category  : ".$this->db->query("SELECT b.name AS brand,c.name AS category,CONCAT(b.name,' - ',c.name) AS brandcategory
																			FROM king_deals d 
																			JOIN king_brands b ON b.id=d.brandid 
																			JOIN king_categories c ON c.id=d.catid  
																			WHERE d.catid=? AND d.brandid=? ",array($catid,$brandid))->row()->brandcategory;
		$this->load->view("admin",$data);
	}
	
	function prod_mrp_update($bid=false,$cid=false)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE|DEAL_MANAGER_ROLE);	
		
		$cond = '';
		if($bid)
			$cond .= " and a.brandid = $bid ";
		if($cid)
			$cond .= " and a.catid = $cid ";
			
		if($_POST)
		{
			$c=0;
			foreach($this->input->post("pid") as $i=>$pid)
			{
				$mrp=$_POST['mrp'][$i];
				if(empty($mrp))
					continue;
				$c++;
				$pc_prod=$this->db->query("select * from m_product_info where product_id=? and mrp!=?",array($pid,$mrp))->row_array();
				if(!empty($pc_prod))
				{
					
					$inp=array("product_id"=>$pid,"new_mrp"=>$mrp,"old_mrp"=>$pc_prod['mrp'],"reference_grn"=>0,"created_by"=>$user['userid'],"created_on"=>time());
					$this->db->insert("product_price_changelog",$inp);
					$this->db->query("update m_product_info set mrp=? where product_id=? limit 1",array($mrp,$pid));
					foreach($this->db->query("select product_id from products_group_pids where group_id in (select group_id from products_group_pids where product_id=$pid) and product_id!=$pid")->result_array() as $pg)
					{
						$inp=array("product_id"=>$pg['product_id'],"new_mrp"=>$mrp,"old_mrp"=>$this->db->query("select mrp from m_product_info where product_id=?",$pg['product_id'])->row()->mrp,"reference_grn"=>0,"created_by"=>$user['userid'],"created_on"=>time());
						$this->db->insert("product_price_changelog",$inp);
						$this->db->query("update m_product_info set mrp=? where product_id=? limit 1",array($mrp,$pg['product_id']));
					}
					$r_itemids=$this->db->query("select itemid from m_product_deal_link where product_id=?",$pid)->result_array();
					$r_itemids2=$this->db->query("select l.itemid from products_group_pids p join m_product_group_deal_link l on l.group_id=p.group_id where p.product_id=?",$pid)->result_array();
					
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
					$r_itemids = array_values($r_itemids_arr);
					
					foreach($r_itemids as $d)
					{
						$itemid=$d['itemid'];
						$item=$this->db->query("select orgprice,price from king_dealitems where id=?",$itemid)->row_array();
						$o_price=$item['price'];
						$o_mrp=$item['orgprice'];
						
						$n_mrp=$this->db->query("select ifnull(sum(p.mrp*l.qty),0) as mrp from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$itemid)->row()->mrp+$this->db->query("select ifnull(sum((select avg(mrp) from m_product_group_deal_link l join products_group_pids pg on pg.group_id=l.group_id join m_product_info p on p.product_id=pg.product_id where l.itemid=$itemid)*(select qty from m_product_group_deal_link where itemid=$itemid)),0) as mrp")->row()->mrp;
						if($pc_prod['is_serial_required'])
							$n_price = $o_price;
						else
							$n_price=$item['price']/$o_mrp*$n_mrp;
						
						$inp=array("itemid"=>$itemid,"old_mrp"=>$o_mrp,"new_mrp"=>$n_mrp,"old_price"=>$o_price,"new_price"=>$n_price,"created_by"=>$user['userid'],"created_on"=>time(),"reference_grn"=>0);
						$r=$this->db->insert("deal_price_changelog",$inp);
						$this->db->query("update king_dealitems set orgprice=?,price=? where id=? limit 1",array($n_mrp,$n_price,$itemid));
						
						// Disable special margin if set 
						$this->db->query("update pnh_special_margin_deals set is_active = 0 where i_price != ? and itemid = ? and is_active = 1 " ,array($n_price,$itemid));
						
						if($this->db->query("select is_pnh as b from king_dealitems where id=?",$itemid)->row()->b)
						{
							$o_s_price=$this->db->query("select store_price from king_dealitems where id=?",$itemid)->row()->store_price;
							
							if($pc_prod['is_serial_required'])
								$n_s_price = $o_s_price;
							else
								$n_s_price=$o_s_price/$o_mrp*$n_mrp;
							
							$this->db->query("update king_dealitems set store_price=? where id=? limit 1",array($n_s_price,$itemid));
							$o_n_price=$this->db->query("select nyp_price as p from king_dealitems where id=?",$itemid)->row()->p;
							
							if($pc_prod['is_serial_required'])
								$n_n_price=$o_n_price;
							else
								$n_n_price=$o_n_price/$o_mrp*$n_mrp;
							
							$this->db->query("update king_dealitems set nyp_price=? where id=? limit 1",array($n_n_price,$itemid));
						}
						foreach($this->db->query("select * from partner_deal_prices where itemid=?",$itemid)->result_array() as $r)
						{
							$o_c_price=$r['customer_price'];
							if($pc_prod['is_serial_required'])
								$n_c_price=$o_c_price;
							else
								$n_c_price=$o_c_price/$o_mrp*$n_mrp;
							
							$o_p_price=$r['partner_price'];
							if($pc_prod['is_serial_required'])
								$n_p_price=$o_p_price;
							else
								$n_p_price=$o_p_price/$o_mrp*$n_mrp;
							
							$this->db->query("update partner_deal_prices set customer_price=?,partner_price=? where itemid=? and partner_id=?",array($n_c_price,$n_p_price,$itemid,$r['partner_id']));
						}
					}
				}
			}
			$this->erpm->flash_msg("MRPs of $c products updated");
			
			if($cond==$bid){
			redirect("admin/prod_mrp_update/$bid");
			}else{
			redirect("admin/prod_mrp_update/$bid/$cid");
			}
			}
		
			if($cond)
			{
				$data['prods']=$this->db->query("
				select * from (
				(
				select  d.product_id,d.product_name,d.mrp,d.is_sourceable
					from king_deals a 
					join king_dealitems b on a.dealid = b.dealid 
					join m_product_deal_link c  on c.itemid = b.id 
					join m_product_info d on d.product_id = c.product_id 
					join king_categories e on e.id = a.catid 
				where 1 $cond 
				) union (
				select  d.product_id,d.product_name,d.mrp,d.is_sourceable
					from king_deals a 
					join king_dealitems b on a.dealid = b.dealid 
					join m_product_group_deal_link c  on c.itemid = b.id 
					join products_group_pids f on f.group_id = c.group_id 
					join m_product_info d on d.product_id = f.product_id 
					join king_categories e on e.id = a.catid 
				where 1 $cond    
				) ) as g 
				group by product_id "  )->result_array();		
			}	
			
			$data['bid'] = $bid;
			$data['cid'] = $cid;
			$data['page']="prod_mrp_update";
			$this->load->view("admin",$data);
		}

		function pnh_pub_deal($itemid,$pub,$redirect=1)
		{
			$user=$this->auth(DEAL_MANAGER_ROLE);
			$this->db->query("update king_deals set publish=? where dealid=?",array(!$pub,$this->db->query("select dealid from king_dealitems where id=?",$itemid)->row()->dealid));
			
			if($redirect)
			{
				$this->session->set_flashdata("erp_pop_info","Deal status changed");
				redirect($_SERVER['HTTP_REFERER']);
			}else{
				$output=array();
				if($this->db->affected_rows())
				{
					$output['status']='success';
					$output['is_published']=!$pub;
				}else{
					$output['status']='error';
					$output['error']='Update failed,no changes found!';
				}
				
				echo json_encode($output);
			}
		}
	
	function pnh_class()
	{
		$user=$this->auth(true);
		if($_POST && $this->input->post("new"))
			$this->erpm->do_pnh_add_class();
		elseif($_POST)
			$this->erpm->do_pnh_update_class();
		$data['class']=$this->db->query("select * from pnh_m_class_info order by class_name asc")->result_array();
		$data['page']="pnh_class";
		$this->load->view("admin",$data);
	}
	
	function pnh_territories()
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE);
		if($_POST && !$this->input->post("edit"))
			$this->erpm->do_pnh_add_territory();
		elseif($_POST)
			$this->erpm->do_pnh_update_territory();
			
		$data['terrys']=$this->db->query("select * from pnh_m_territory_info order by territory_name asc")->result_array();
		$data['page']="pnh_territories";
		$this->load->view("admin",$data);
	}
	
	function pnh_device_type()
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE);
		if($_POST && !$this->input->post("edit"))
			$this->erpm->do_pnh_add_device_type();
		elseif($_POST)
			$this->erpm->do_pnh_update_device_type();
		$data['devs']=$this->db->query("select * from pnh_m_device_type order by device_name asc")->result_array();
		$data['page']="pnh_device_type";
		$this->load->view("admin",$data);
	}
	
	function jx_pnh_getmid()
	{
		$user=$this->auth();
		
		$mid=$this->input->post('mid');
		
		$mem=$this->db->query("select * from pnh_member_info where pnh_member_id=?",$mid)->row_array();
		
	
		if(empty($mem))
		{
			$fran=$this->db->query("select f.franchise_id,f.franchise_name as name from pnh_m_allotted_mid a join pnh_m_franchise_info f on f.franchise_id=a.franchise_id where ? between a.mid_start and a.mid_end",$mid)->row_array();
			if(empty($fran))
				die("$mid is not allotted to any franchise");
			if(!isset($_POST['more']))
				die("$mid is assigned to Franchise: <a href='".site_url("admin/pnh_franchise/{$fran['franchise_id']}")."' target='_blank'>{$fran['name']}</a>");
			die("$mid is assigned to Franchise: <a href='".site_url("admin/pnh_franchise/{$fran['franchise_id']}")."' target='_blank'>{$fran['name']}</a> <span style='color:green;margin-left:10px;font-size:120%;float:right;'>NEW MEMBER!</span><div>Member Name : <input type='text' class='inp' name='m_name'> &nbsp; &nbsp;&nbsp; Mobile No : <input type='text' class='inp' maxlength=10 name='m_mobile'></div>");
		}
		else
		{
			$mem['first_name']=($mem['first_name']==""&&$mem['last_name']=="")?"No Name":$mem['first_name'];
			$msg="<div style='font-size:120%;'>";
			$order=$this->db->query("select count(1) as n,sum(amount) as t from king_transactions where transid in (select transid from king_orders where userid=?)",$mem['user_id'])->row_array();
			$msg.="Member Name : <a href='".site_url("admin/pnh_viewmember/{$mem['user_id']}")."' target='_blank'>{$mem['first_name']} {$mem['last_name']}</a> &nbsp;&nbsp;&nbsp; Total Orders : {$order['n']} &nbsp;&nbsp;&nbsp; Total Amount : Rs {$order['t']} &nbsp;&nbsp;&nbsp; Loyalty points : {$mem['points']}";
			$msg.="<div class='clear'></div></div>";
			die($msg);
		}
	}

	function jx_pnh_getvouchermid()
	{
		$user=$this->auth();
		$member_mobno=$this->input->post("member_mobno");
	
		$mid=$this->db->query("select pnh_member_id from pnh_member_info where mobile=?",$member_mobno)->row()->pnh_member_id;
		if(!$member_mobno)
		{
			$mid=$this->input->post("mid");
			if(strlen($mid)!=8 || $mid{0}!=2)
				die("Invalid MID $mid");
		}
		$mem=$this->db->query("select * from pnh_member_info where pnh_member_id=?",$mid)->row_array();
	
		if(empty($mem))
		{
			$fran=$this->db->query("select f.franchise_id,f.franchise_name as name from pnh_m_allotted_mid a join pnh_m_franchise_info f on f.franchise_id=a.franchise_id where ? between a.mid_start and a.mid_end",$mid)->row_array();
			if(empty($fran))
				die("$mid is not allotted to any franchise");
			if(!isset($_POST['more']))
				die("$mid is assigned to Franchise: <a href='".site_url("admin/pnh_franchise/{$fran['franchise_id']}")."' target='_blank'>{$fran['name']}</a>");
			die("$mid is assigned to Franchise: <a href='".site_url("admin/pnh_franchise/{$fran['franchise_id']}")."' target='_blank'>{$fran['name']}</a> <span style='color:green;margin-left:10px;font-size:120%;float:right;'>NEW MEMBER!</span><div>Member Name : <input type='text' class='inp' name='m_name'> &nbsp; &nbsp;&nbsp; Mobile No : <input type='text' class='inp' maxlength=10 name='m_mobile'></div>");
		}
		else
		{
			$mem['first_name']=($mem['first_name']==""&&$mem['last_name']=="")?"No Name":$mem['first_name'];
			$msg="<div style='font-size:120%;'>";
			$v_balamt=$this->db->query("SELECT SUM(customer_value) AS voucher_bal FROM pnh_t_voucher_details WHERE STATUS in(3,5) AND member_id=?",$mem['pnh_member_id'])->row()->voucher_bal;
			if(!$v_balamt)
				$v_balamt=0;
			$msg.="Name : <a href='".site_url("admin/pnh_viewmember/{$mem['user_id']}")."' target='_blank'>{$mem['first_name']} {$mem['last_name']}</a> &nbsp;&nbsp;&nbsp;Balance : {$v_balamt}";
			$msg.="<div class='clear'></div></div>";
			die($msg);
		}
	}

	function jx_pnh_getmemvoucherdet()
	{
		$vcode=$this->input->post("vcode");
		$vcodes=explode(',',$vcode);
		$mem_mobno=$this->input->post("mem_mobno");
		$voucher_value=0;
		$mem=$this->db->query("select * from pnh_member_info where mobile=?",$mem_mobno)->row_array();
		$v_details=$this->db->query("select * from pnh_t_voucher_details where member_id=? and voucher_code=?",array($mem['pnh_member_id'],$vcode))->row_array();
		if($v_details)
		{
			foreach($vcodes as $vcode)
			{
				$vdet=$this->db->query("select b.book_id,d.franchise_id,a.voucher_serial_no,a.customer_value,a.status as voucher_status
																from pnh_t_voucher_details a
																join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
																join pnh_t_book_allotment d on d.book_id=b.book_id
																where voucher_code=? and member_id=?  and is_activated=1 
																group by voucher_serial_no",array($vcode,$mem['pnh_member_id']))->row_array();

					if($vdet['voucher_status']>3)
					{
						$fmsg="<div style='font-size:120%;color:red;'>";
						if($vdet['voucher_status']==4)
						{
							$fmsg.="Coupon Serial no: {$vdet['voucher_serial_no']} is fully redeemed";
						}
						if($vdet['voucher_status']==5)
						{
							$fmsg.="Coupon Serial no: {$vdet['voucher_serial_no']} is partially redeemed Balance: {$vdet['customer_value']}";
						}
						$fmsg.="</div>";
					die($fmsg) ;
				}
			 if($vdet['voucher_status']=3)
				{
				//get the book menus
					$vmenus=$this->db->query("select id,name from  pnh_menu where id in (select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=?)",$vdet['book_id'])->row_array();
					
					
					$prev_voucher_bal=$this->db->query("SELECT SUM(customer_value) AS loyal_pnts FROM pnh_t_voucher_details WHERE STATUS in(5) AND member_id=?",$mem['pnh_member_id'])->row()->loyal_pnts;
					if(!$prev_voucher_bal)
						$prev_voucher_bal=0;
					//$voucher_value+=$vdet['customer_value'];
					$ttl_value=$vdet['customer_value']+$prev_voucher_bal;
					$smsg="<div style='font-size:120%;'>";
					$smsg.="Serial no: {$vdet['voucher_serial_no']}&nbsp&nbsp&nbspMenu :{$vmenus['name']}&nbsp&nbsp&nbspvalue(Rs {$vdet['customer_value']}) + Previous( Rs {$prev_voucher_bal})=Rs {$ttl_value}";
					$smsg.="<div class='clear'></div></div>";
					echo $smsg;
				}
				
			}
			$nr_value=0;
			$non_redeemed_vdetails=$this->db->query("select a.*,b.book_id,d.franchise_id,a.voucher_serial_no as vslno,a.customer_value,a.status as voucher_status
																from pnh_t_voucher_details a
																join pnh_t_book_voucher_link b on b.voucher_slno_id = a.id
																join pnh_t_book_allotment d on d.book_id=b.book_id
																where voucher_code!=? and member_id=?  and is_activated=1 and a.status=3
																group by voucher_serial_no",array($vcode,$mem['pnh_member_id']));
			//echo $this->db->last_query();
			
			if($non_redeemed_vdetails->num_rows())
			{
				foreach($non_redeemed_vdetails->result_array() as $m)
				{
					$non_redeemed_vmenus=$this->db->query("select id,name from  pnh_menu where id in (select menu_ids from pnh_t_book_details a join pnh_m_book_template b on b.book_template_id=a.book_template_id where a.book_id=? and menu_ids=?)",array($m['book_id'],$vmenus['id']))->row_array();
				
					if($non_redeemed_vmenus) 
					{
						$rmsg = '';
						$nr_value+=$m['customer_value'];
						$rmsg="<div style='font-size:120%;'>";
						$rmsg.="Serial no: {$m['vslno']}&nbsp&nbsp&nbspMenu :{$non_redeemed_vmenus['name']}&nbsp&nbsp&nbspvalue(Rs {$m['customer_value']}) ";
						$rmsg.="<div class='clear'></div></div>";
						echo $rmsg;
					}
				
				}
				
			}
				$ttl_purchase_value=$ttl_value+$nr_value;
				 $prmsg="<div style='font-size:120%;'>";
				$prmsg.="Total Purchase Value:$ttl_purchase_value";
				$prmsg.="<div class='clear'></div></div>";
			echo $prmsg;
		}
		else 
			die("Invalid voucher details");
		
	}
	
	/*function jx_pnh_getmemvoucherdet()
	{
		$vcode=$this->input->post("vcode");
		$vcodes=explode(',',$vcode);
		$mem_mobno=$this->input->post("mem_mobno");
		$mem=$this->db->query("select * from pnh_member_info where mobile=?",$mem_mobno)->row_array();
		
		$v_details=$this->db->query("select * from pnh_t_voucher_details where member_id=? and voucher_code in(?)",array($mem['pnh_member_id'],$vcode))->row_array();

		if($v_details)
		{
			
			foreach($vcodes as $vcode)
			{
				$vdet=$this->db->query("SELECT a.voucher_id,a.member_id,a.voucher_serial_no,a.voucher_code,a.status,p.menu_ids,m.name,a.status as voucher_status,customer_value
												FROM pnh_t_voucher_details a
												JOIN pnh_t_book_voucher_link b ON b.voucher_slno_id = a.id
												JOIN pnh_t_book_allotment d ON d.book_id=b.book_id 
												JOIN `pnh_m_book_template_voucher_link` k ON k.voucher_id=a.voucher_id
												JOIN pnh_m_book_template p ON p.book_template_id=k.book_template_id
												JOIN pnh_menu m ON m.id=p.menu_ids
												WHERE voucher_code=? and member_id=? and is_activated=1 
												GROUP BY a.voucher_serial_no",array($vcode,$mem['pnh_member_id']))->row_array();
				
				if($vdet['voucher_status']>3)
				{
					$msg="<div style='font-size:120%;color:red;'>";
					if($vdet['voucher_status']==4)
					{
						$msg.="Coupon Serial no: {$vdet['voucher_serial_no']} is fully redeemed";
					}
					if($vdet['voucher_status']==5)
					{
						$msg.="Coupon Serial no: {$vdet['voucher_serial_no']} is partially redeemed Balance: {$vdet['customer_value']}";
					}
					$msg.="</div>";
					die($msg) ;
				}
				else
				{
					
				$prev_voucher_bal=$this->db->query("SELECT SUM(customer_value) AS loyal_pnts FROM pnh_t_voucher_details WHERE STATUS in(5) AND member_id=?",$mem['pnh_member_id'])->row()->loyal_pnts;
					
				if(!$prev_voucher_bal)
					$prev_voucher_bal=0;
				$ttl_value=$vdet['customer_value']+$prev_voucher_bal;
				$msg="<div style='font-size:120%;'>";
				$msg.="Serial no: {$vdet['voucher_serial_no']}&nbsp&nbsp&nbspMenu :{$vdet['name']}&nbsp&nbsp&nbspvalue(Rs {$vdet['customer_value']}) + Previous( Rs {$prev_voucher_bal})=Rs {$ttl_value}";
				$msg.="<div class='clear'></div></div>";
				}
				echo $msg;
			}
			$non_redeem_vcode_menu=$this->db->query("SELECT a.voucher_serial_no,a.voucher_code,a.member_id,a.customer_value,m.name
														FROM pnh_t_voucher_details a
														JOIN pnh_t_book_voucher_link b ON b.voucher_slno_id = a.id
														JOIN pnh_t_book_allotment d ON d.book_id=b.book_id 
														JOIN `pnh_m_book_template_voucher_link` k ON k.voucher_id=a.voucher_id
														JOIN pnh_m_book_template p ON p.book_template_id=k.book_template_id
														JOIN pnh_menu m ON m.id=p.menu_ids
														WHERE member_id=? AND  m.id=? AND a.status=3 and voucher_code!=?
														GROUP BY a.voucher_serial_no",array($mem['pnh_member_id'],$vdet['menu_ids'],$vcode));
			

			if($non_redeem_vcode_menu->num_rows())
			{
				foreach($non_redeem_vcode_menu->result_array() as $m)
				{
					$rmsg="<div style='font-size:120%;'>";
					$rmsg.="Serial no: {$m['voucher_serial_no']}&nbsp&nbsp&nbspMenu :{$m['name']}&nbsp&nbsp&nbspvalue(Rs {$m['customer_value']}) ";
					$rmsg.="<div class='clear'></div></div>";
				}
				
				echo $rmsg;
			}
			
			
			
			
		}
		else
		{
			die("Invalid voucher details");
		}
	}*/	
	function pnh_order_import()
	{
		$user=$this->auth(FINANCE_ROLE);
		if($_FILES)
			$this->erpm->do_pnh_order_import();
		$data['page']="pnh_order_import";
		$this->load->view("admin",$data);
	}
	
	function pnh_upload_images($fid)
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE);
		if($_FILES)
			$this->erpm->do_pnh_upload_images($fid);
		$data['fid']=$fid;
		$data['page']="pnh_upload_images";
		$this->load->view("admin",$data);
	}
	
	function jx_pnh_checkmememail()
	{
		$e=$this->input->post("email");
		$mid=$this->input->post("mid");
		
		if($this->db->query("select 1 from pnh_member_info where email=? ".($mid?' and pnh_member_id != '.$mid:''),$e)->num_rows()!=0)
			die("0");
		else
			die("1");
	}
	
	function jx_pnh_checkmemmob()
	{
		$e=$this->input->post("mob");
		$m=$this->input->post("mid");
		if($this->db->query("select 1 from pnh_member_info where mobile=? and pnh_member_id!=?",array($e,$m))->num_rows()!=0)
			die("0");
		else
			die("1");
	}
	
	function pnh_addmember()
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE|CALLCENTER_ROLE);
		if($_POST)
			$this->erpm->do_pnh_addmember();
		
		$data['page']="pnh_add_member";
		$this->load->view("admin",$data);
	}
	
	function pnh_editmember($mem_id='')
	{
		$user=$this->auth(PNH_EXECUTIVE_ROLE|CALLCENTER_ROLE);
		if($_POST)
			$this->erpm->do_pnh_editmember();
		
		
		$data['mem_det'] = $this->db->query("select * from pnh_member_info where pnh_member_id = ? ",$mem_id)->row_array();
		if(!$data['mem_det'])
			show_error("Invalid Member in access");
		
		$data['page']="pnh_edit_member";
		$this->load->view("admin",$data);
	}
	
	function pnh_bulkaddmembers()
	{
		$user=$this->auth(PNH_MEMBERS_BULK_IMPORT);
		if($_FILES)
			$this->erpm->do_pnh_bulkaddmembers();
		
		$data['page']="pnh_add_bulkmembers";
		$this->load->view("admin",$data);
	}
	
	function pnh_cash_bill($transid)
	{
		$user=$this->auth();
		$data['bill']=$b=$this->db->query("select * from pnh_cash_bill where transid=?",$transid)->row_array();
		if(empty($b))
			show_error("Cash Bill not found");
		$data['fran']=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$b['franchise_id'])->row_array();
		$data['member']=$this->db->query("select * from pnh_member_info where user_id=?",$b['user_id'])->row_array();
		$data['orders']=$this->db->query("select o.*,i.name as product from king_orders o join king_dealitems i on i.id=o.itemid where o.transid=? and o.status!=2",$transid)->result_array();
		$data['page']="pnh_cash_bill";
		$this->load->view("admin/body/pnh_cash_bill",$data);
	}
	
	function create_cash_bill()
	{
		$trans=$this->db->query("select * from king_transactions where franchise_id!=0")->result_array();
		foreach($trans as $t)
		{
			$transid=$t['transid'];
			$userid=$this->db->query("select userid from king_orders where transid=?",$transid)->row()->userid;
			$franid=$t['franchise_id'];
			$billno=10001;
			$nbill=$this->db->query("select bill_no from pnh_cash_bill where franchise_id=? order by bill_no desc limit 1",$franid)->row_array();
			if(!empty($nbill))
				$billno=$nbill['bill_no']+1;
			$inp=array("bill_no"=>$billno,"franchise_id"=>$franid,"transid"=>$transid,"user_id"=>$userid,"status"=>1);
			$this->db->insert("pnh_cash_bill",$inp);
		}
	}
	
	function pnh_member_card_batch()
	{
		$user=$this->auth(true);
		if($_POST)
		{
			$n=$this->input->post("n");
			$data=$this->db->query("select pnh_member_id as mid,if(salute=0,'Mr',if(salute=1,'Mrs','Ms')) as salute,first_name,last_name from pnh_member_info where is_card_printed=0 and created_on!=0 order by id asc limit $n")->result_array();
			if(empty($data))
				show_error("No pending member cards to be generated");
			$this->db->query("update pnh_member_info set is_card_printed=1 where is_card_printed=0 and created_on!=0 order by id asc limit $n");
			$this->erpm->export_csv("member_card_results",$data,true);
		}
		$data['page']="pnh_member_card_batch";
		$this->load->view("admin",$data);
	}
	
	function pnh_deal_extra_images($id)
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		if($_FILES)
			$this->erpm->do_pnh_deal_extra_images($id);
		$data['page']="pnh_deal_extra_images";
		$this->load->view("admin",$data);
	}
	
	function pnh_deal($id)
	{
		$user=$this->auth();
		$sql="select i.max_allowed_qty,i.billon_orderprice,i.gender_attr,d.catid,d.brandid,d.tagline,i.nyp_price,i.pnh_id,i.id,b.name as brand,c.name as category,
			  i.name,i.pic,i.orgprice,i.price,i.store_price,d.description,d.publish,e.name as created_by,f.name as mod_name,i.created_on,i.modified_on,
			  d.menuid,m.name as menu_name
			  		from king_dealitems i 
			  		join king_deals d on d.dealid=i.dealid 
			  		left join king_brands b on b.id=d.brandid 
			  		join king_categories c on c.id=d.catid 
			  		left join pnh_menu m on m.id=d.menuid
			  		left join king_admin e on e.id=i.created_by
			  		left join king_admin f on f.id=i.modified_by
			  		where i.id=? or i.pnh_id=?";
		$data['deal']=$this->db->query($sql,array($id,$id))->row_array();
		
		if($id != $data['deal']['id'])
			redirect('admin/pnh_deal/'.$data['deal']['id'],'refresh');
		
		$data['prods']=$this->db->query("select p.product_id,p.product_name,l.qty from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$data['deal']['id'])->result_array();
		$pnh_Deal_upd_log=$this->erpm->get_pnh_deal_update_log($id);
		
		$data['ttl_fran'] =$this->db->query("select c.franchise_id,franchise_name,sum(a.quantity) as sold_qty,sum((a.i_orgprice-(a.i_discount+a.i_coup_discount))*a.quantity) as ttl_sales_value
										from king_orders a
										join king_transactions b on a.transid = b.transid
										join pnh_m_franchise_info c on c.franchise_id = b.franchise_id
										where is_pnh = 1 and itemid =? and a.status != 3 
										group by b.franchise_id
										order by franchise_name asc ",$id)->result_array(); 
		$data['most_sold_fran'] =$this->db->query("select c.franchise_id,franchise_name,sum(a.quantity) as sold_qty,b.transid
										from king_orders a
										join king_transactions b on a.transid = b.transid
										join pnh_m_franchise_info c on c.franchise_id = b.franchise_id
										where is_pnh = 1 and itemid =? and a.status != 3 
										group by b.franchise_id
										order by sold_qty desc limit 10 ",$id)->result_array();
		$data['latest_fran'] =$this->db->query("select c.franchise_id,franchise_name,a.quantity as sold_qty,b.transid,date_format(from_unixtime(b.init),'%d/%m/%y') as date,month(date(from_unixtime(b.init))) as m
										from king_orders a
										join king_transactions b on a.transid = b.transid
										join pnh_m_franchise_info c on c.franchise_id = b.franchise_id
										where is_pnh = 1 and itemid =? and a.status != 3 
										order by b.init desc limit 20",$id)->result_array();  								
		
		$data['pnh_Deal_upd_log']=$pnh_Deal_upd_log;
		$data['page']="pnh_deal";
		$this->load->view("admin",$data);
	}
	
	/*
	 * Ajax function to load yearly deal sales 
	 * 
	 */
	function jx_deal_getsales($s="",$e="",$itemid="")
	{
	   $date_diff = date_diff_days($e,$s);
	 
		 if($date_diff <= 31)	
		 {
		   $sql = "select date_format(from_unixtime(b.init),'%d-%m-%Y') as mn,sum(a.quantity) as t
				from king_orders a
				join king_transactions b on a.transid = b.transid
				where is_pnh = 1 and itemid = '".$itemid."' and a.status != 3 and date_format(from_unixtime(b.init),'%Y-%m-%d') between '".$s."' and '".$e."'  
				group by mn  
				order by mn asc";
		}
		else
		{
			 $sql = "select date_format(from_unixtime(b.init),'%m-%Y') as mn,date(from_unixtime(b.init)) as dt,sum(a.quantity) as t
				from king_orders a
				join king_transactions b on a.transid = b.transid
				where is_pnh = 1 and itemid = '".$itemid."' and a.status != 3 and date_format(from_unixtime(b.init),'%Y-%m-%d') between '".$s."' and '".$e."'  
				group by mn
				order by dt asc";
		}	
		
		 $res = $this->db->query($sql);
		 $deal_summary = array();
		
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				array_push($deal_summary,array($row['mn'],$row[t]*1));
			}
		}
		$output = array();
		$output['date_diff'] = $date_diff;
		$output['summary'] = $deal_summary;
		echo json_encode($output);
	}

	/*
	 * Ajax function to load territory,quantity details based on deal
	 * 
	 */
	function jx_deal_getsales_by_territory($itemid="",$state_id="")
	{
		$sql = "select c.territory_id,d.territory_name,sum(a.quantity) as ttl_sold 
			from king_orders a
			join king_transactions b on a.transid = b.transid 
			join pnh_m_franchise_info c on c.franchise_id = b.franchise_id 
			join pnh_m_territory_info d on d.id = c.territory_id  
			join pnh_m_states e on e.state_id=d.state_id
			where itemid = '".$itemid."' and a.status != 3 and e.state_id='".$state_id."'
			group by territory_id 
			order by territory_name";
		 $res = $this->db->query($sql); 
		 $deals=array();	
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				array_push($deals,array($row['territory_name'],$row['ttl_sold']*1,$row['territory_id'])); 
			}
		}
		
		$output = array();
		$output['result'] = $deals;
		echo json_encode($output);	
	}
	
	/*
	 * Ajax function to load franchises based on territory and deal
	 * 
	 */
	function jx_deal_getfranchise_by_territory($terr_id="",$itemid="")
	{
		$fran =$this->db->query("select b.franchise_name,b.franchise_id,c.territory_name,c.id,ki.itemid
		from king_transactions a
		join king_orders ki on ki.transid = a.transid
		join pnh_m_franchise_info b on b.franchise_id = a.franchise_id
		join pnh_m_territory_info c on c.id = b.territory_id
		where c.id='".$terr_id."' and ki.itemid ='".$itemid."' group by b.franchise_name ");
		
		if($fran->num_rows())
		{
			$output['fran_list']=$fran->result_array();
			$output['status']='success';
		}
		else
		{
			$output['status']="error";
		}
		echo json_encode($output);	
	}
	
	function pnh_update_description()
	{
		$user=$this->auth(true);
		if($_FILES)
		{
			$f=@fopen($_FILES['csv']['tmp_name'],"r");
			$head=fgetcsv($f);
			$template=array("Item ID","description");
			if(empty($head) || count($head)!=count($template))
				show_error("Invalid template structure".count($head));
			$payload=array();
			while(($data=fgetcsv($f))!=false)
			{
				if(count($data)!=2)
					show_error("Invalid template structure");
				$deal=$this->db->query("select dealid from king_dealitems where id=? and is_pnh=1",$data[0])->row_array();
				if(empty($deal))
					show_error("Invalid Item ID {$data[0]} or its not a PNH deal");
				$inp=array("dealid"=>$deal['dealid'],"description"=>$data[1]);
				$payload[]=$inp;
			}
			foreach($payload as $p)
				$this->db->query("update king_deals set description=? where dealid=? limit 1",array($p['description'],$p['dealid']));
			$this->erpm->flash_msg(count($payload)." deals updated");
			redirect("admin/pnh_update_description");
		}
		$data['page']="pnh_update_description";
		$this->load->view("admin",$data);
	}
	
	function pnh_sms_campaign()
	{
		$user=$this->auth(true);
		if($_POST)
		{
			foreach(array("fids","send_to","msg","type","sel_itemid","sel_itemname","sel_itemmrp","sel_itemprice","notify_emp","sms_tmpl","emplist") as $i)
				$$i=$this->input->post($i);
			if(empty($fids))
				show_error("No franchises were selected");
			else
			{
				// check if employee need to be nofitified
				if($notify_emp && $sms_tmpl == 1)
				{
				
					$emp_list=$this->db->query("SELECT a.employee_id,b.name,t.town_name,r.territory_name ,f.franchise_name,job_title2,b.contact_no
								FROM m_town_territory_link a
								JOIN m_employee_info b ON b.employee_id=a.employee_id
								JOIN `pnh_m_franchise_info` f ON f.territory_id=a.territory_id
								JOIN `pnh_m_territory_info`r ON r.id=f.territory_id
								LEFT JOIN pnh_towns t ON t.id=a.town_id
								WHERE f.is_suspended=0 AND a.is_active=1 AND b.is_suspended=0 AND b.job_title2 in (4,5) AND f.franchise_id in (".implode(',',$fids).") 
								GROUP BY a.employee_id")->result_array();
					
					
					//send Offer of the day sms to employee
					if($emp_list)
					{
						foreach ($emp_list as $emp_det)
						{
							$emp_id=$emp_det['employee_id'];
							$emp_contact1=array();
							$emp_contact1=$emp_det['contact_no'];
							$emp_contact=explode(',',$emp_contact1);
							$emp_name=$emp_det['name'];
							
							if(!in_array($emp_id, $emplist))
								continue;
							
							foreach($emp_contact as $emp_ph)
							{
								$this->erpm->pnh_sendsms($emp_ph,"Dear $emp_name, Offer Of The Day $msg ");
								
								if($sms_tmpl == 1)
								{
									$send_msg = "Dear $emp_name, Offer Of the day $msg";
									$notify_type = 'Offer';
								}else if($sms_tmpl == 2)
								{
									$send_msg = 'Top 5 Franchise Sales of the day '.$msg;
									$notify_type = 'notify';
								}
								
								$this->erpm->pnh_sendsms($emp_ph,$send_msg);
								
								$this->db->query("insert into pnh_employee_grpsms_log (emp_id,contact_no,type,grp_msg,created_on)values(?,?,5,?,now())",array($emp_id,$emp_ph,$send_msg));
							}
						}
					}
				}
				
				// send deal promo
				if($type == 2)
				{
					$prodname=$this->db->query("select name from king_dealitems where pnh_id=?",$sel_itemid)->row()->name;
					$msg = "$prodname,PNHID:".$sel_itemid." MRP:Rs".$sel_itemmrp." Price:Rs".$sel_itemprice;
					$sms_tmpl = 1;
				}
				
				if(empty($msg))
					show_error("No message to send");
				
				foreach($this->db->query("select login_mobile1,franchise_id,login_mobile2,franchise_name from pnh_m_franchise_info where franchise_id in ('".implode("','",$fids)."')")->result_array() as $f)
				{
					$f_name=$f['franchise_name'];
					$f_id=$f['franchise_id'];
					$f_mob1=$f['login_mobile1'];
					$f_mob2=$f['login_mobile2'];
					
					if($sms_tmpl == 1)
					{
						$send_msg = "Dear $f_name, Offer Of the day $msg";
						$notify_type = 'Offer';
					}else if($sms_tmpl == 2)
					{
						$send_msg = 'Top 5 Franchise Sales of the day '.$msg;
						$notify_type = 'notify';
					}
					
					//send Offer of the day sms to Franchisee
					$this->erpm->pnh_sendsms($f_mob1,$send_msg,$f_id,0,$notify_type);
					if($send_to==1 && !empty($f_mob2))
						$this->erpm->pnh_sendsms($f_mob2,$send_msg,$f_id,0,$notify_type); 
				}
				
				$this->erpm->flash_msg("SMS sent");
				redirect("admin/pnh_sms_campaign");
			}
		}
		$data['frans']=$this->erpm->pnh_getfranchises();
		$data['page']="pnh_sms_campaign";
		$this->load->view("admin",$data);
	}
	
	function jx_get_empcount_by_fids()
	{
		$this->erpm->auth();
		$fids=$this->input->post('fids');
		$fids=implode(',',$fids);
		$output=array();
		
		$emp_list=$this->erpm->get_empcount_by_fids($fids);
		$count_list=array();
		$emplist=array();
		
		foreach($emp_list as $emp)
		{
			if(isset($count_list[$emp['job_title2']]))
				$count_list[$emp['job_title2']]=$count_list[$emp['job_title2']]+1;
			else
				$count_list[$emp['job_title2']]=1;
			
			if(!isset($emplist[$emp['job_title2']]))
				$emplist[$emp['job_title2']]=array();
			$emplist[$emp['job_title2']][]=$emp;
		}
		
		$output['emp_list']=$emplist;
		$output['count_list']=$count_list;
		echo json_encode($output);
	}
	
	function pnh_sms_notify()
	{
		$user=$this->auth(true);
		if($_POST)
		{
			foreach(array("fids","send_to","msg","type","sel_itemid","sel_itemname","sel_itemmrp","sel_itemprice") as $i)
				$$i=$this->input->post($i);
			if(empty($fids))
				show_error("No franchises were selected");
			else
			{
				
				if(empty($msg))
					show_error("No message to send");
				foreach($this->db->query("select login_mobile1,franchise_id,login_mobile2,franchise_name from pnh_m_franchise_info where franchise_id in ('".implode("','",$fids)."')")->result_array() as $f)
				{
					$f_name=$f['franchise_name'];
					$f_id=$f['franchise_id'];
					$f_mob1=$f['login_mobile1'];
					$f_mob2=$f['login_mobile2'];
					
					if($type == 1)
					{
						$sms_tmpl = 'Top 5 Franchise Sales of the day '.$msg;
					
						//send Offer of the day sms to Franchisee
						$this->erpm->pnh_sendsms($f_mob1,$sms_tmpl,$f_id,0,'notify');
						if($send_to==1 && !empty($f_mob2))
							$this->erpm->pnh_sendsms($f_mob2,$sms_tmpl,$f_id,0,'notify');
					}
				}
				$this->erpm->flash_msg("SMS sent");
				redirect("admin/pnh_sms_notify");
			}
		}
		$data['frans']=$this->erpm->pnh_getfranchises();
		$data['page']="pnh_sms_notify";
		$this->load->view("admin",$data);
	}
	
	function pnh_catalogue()
	{
		$user=$this->auth(true);
		$data=$this->db->query("select group_concat(p.product_name order by l.id asc separator '||') as prods_name, group_concat(l.qty order by l.id asc separator '||') as prods_qty,i.name,i.orgprice as mrp,i.price,i.pnh_id,b.name as brand,d.brandid from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join m_product_deal_link l on l.itemid=i.id join m_product_info p on p.product_id=l.product_id where d.publish=1 and i.is_pnh=1 group by i.id order by b.name asc,i.name asc")->result_array();
		$payload=array();
		foreach($data as $d)
		{
			if(!isset($payload[$d['brandid']]))
				$payload[$d['brandid']]=array();
			$payload[$d['brandid']][]=$d;
		}
		$this->load->library("pdf");
		$this->pdf->doc_type("Product Catalogue");
		$this->pdf->AliasNbPages();
		$this->pdf->AddPage();
		$this->pdf->Image("images/paynearhome.jpg",78,110);
		$this->pdf->SetFont('Arial','B',30);
		$this->pdf->Cell(0,150,"Product Catalogue",0,1,"C");
		$this->pdf->SetFont('Arial','B',15);
		$this->pdf->SetY(155);
		$this->pdf->Cell(0,0,date("d/m/y"),0,0,"C");
		$this->pdf->SetFont('Arial','',10);
		$this->pdf->SetY(-35);
		$this->pdf->Cell(0,0,"To place an order",0,0,"R");
		$this->pdf->SetFont('Arial','B',10);
		$this->pdf->SetY(-30);
		$this->pdf->Cell(0,0,"Call 1800 200 1996",0,0,"R");
		$this->pdf->SetY(-25);
		$this->pdf->Cell(0,0,"hello@paynearhome.in",0,0,"R");
		foreach($payload as $pl)
		{
    		$this->pdf->AddPage();
			$this->pdf->SetFont('Arial','B',17);
		    $this->pdf->SetFillColor(200,220,255);
		    $this->pdf->Cell(0,10,$pl[0]['brand'],0,1,'L',true);
			$this->pdf->SetDrawColor(200,200,200); 
 			$this->pdf->Cell(0,1,"","B",1);
			foreach($pl as $p)
		    {
		    	$this->pdf->SetFont('Arial','B',9);
				$this->pdf->ln(2);
				$this->pdf->MultiCell(120,3,ucfirst($p['name']),0,'L');
				$this->pdf->SetX(135);
		    	$this->pdf->SetFont('Arial','B',8);
				$this->pdf->Cell(40,-2,"MRP : Rs ".$p['mrp']);
				$this->pdf->Cell(20,-2,"Offer : Rs ".$p['price'],0,1);
				$this->pdf->Cell(0,4,"",0,1);
				$prods=explode("||",$p['prods_name']);
				$qtys=explode("||",$p['prods_qty']);
				$this->pdf->SetFont('Arial','B',10);
				$this->pdf->Cell(60,5,$p['pnh_id']);
				$this->pdf->SetFont('Arial','',7);
				foreach($prods as $i=>$prod)
					$this->pdf->Cell(0,4,"{$prod}     x{$qtys[$i]}",0,1,'R');
 				$this->pdf->Cell(0,4,"","B",1);
		    }
		}	
		$this->pdf->Output("products_catalogue_".date("d-m-y").".pdf","D");
	}
	
	function jx_deals_report_prod()
	{
		$p=$_POST['p'];
		$data['prods']=$this->db->query("$p")->result_array();
		$this->load->view("admin/body/deals_report_frag_prod",$data);
	}
	
	function pnh_less_margin_brands()
	{
		$user=$this->auth(true);
		if($_POST)
		{
			$this->db->query("truncate table pnh_less_margin_brands");
			foreach($this->input->post("bids") as $b)
				$this->db->query("insert into pnh_less_margin_brands(brandid,created_on,created_by) values(?,?,?)",array($b,time(),$user['userid']));
			$this->erpm->flash_msg("Less margin brands marked");
			redirect("admin/pnh_less_margin_brands");
		}
		$data['page']="pnh_less_margin_brands";
		$this->load->view("admin",$data);
	}
	
	function pnh_unsuspend_fran($unsuspend_fid)
	{
		$user=$this->auth(true);
		$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$unsuspend_fid)->row_array();
		if(empty($fran))
			show_error("No franchise found");
		$reason=$this->input->post('unsus_reason');
		$this->db->query("update pnh_m_franchise_info set is_suspended=0,suspended_on=".time().",suspended_by={$user['userid']},reason=? where franchise_id=? limit 1",array($reason,$unsuspend_fid));
		if($this->db->affected_rows()>0)
		{
			$this->db->query("insert into franchise_suspension_log(franchise_id,suspension_type,reason,suspended_on,suspended_by)values(?,?,?,?,?)",array($unsuspend_fid,0,$reason,time(),$user['userid']));
		}
		$this->erpm->flash_msg("Franchise unsuspended");
		$this->erpm->send_admin_note("Franchise account : {$fran['franchise_name']} ({$fran['pnh_franchise_id']}) was unsuspended on ".date("g:ia d/m/y")."for $reason"." by {$user['username']}","Franchise account unsuspension");
		redirect("admin/pnh_franchise/$unsuspend_fid");
	}
	
	function pnh_suspend_fran($franchise_id)
	{
		
		$user=$this->auth(true);
		$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$franchise_id)->row_array();
		if(empty($fran))
			show_error("No franchise found");
		$sus_type=$this->input->post('sus_type');
		$reason=$this->input->post('sus_reason');
		$credit_edit=$this->input->post('credit_edit');
		$cond='';
		if($credit_edit)
			$cond.=",credit_limit=0";
		else 
			$cond.=" ";
		$sql=$this->db->query("update pnh_m_franchise_info set is_suspended=?,suspended_on=".time().",suspended_by={$user['userid']},reason=? $cond where franchise_id=? limit 1",array($sus_type,$reason,$franchise_id));
		
		if($this->db->affected_rows()>0)
		{
			$this->db->query("insert into franchise_suspension_log(franchise_id,suspension_type,reason,suspended_on,suspended_by)values(?,?,?,?,?)",array($franchise_id,$sus_type,$reason,time(),$user['userid']));
		}
		$this->erpm->flash_msg("Franchise suspended");
		$sus_type_arr=array();
		$sus_type_arr[0]="Un suspended";
		$sus_type_arr[1]="Permanent suspension";
		$sus_type_arr[2]="Payment suspension";
		$sus_type_arr[3]="Temporary suspension";
		
		$this->erpm->send_admin_note("Franchise account : {$fran['franchise_name']} ({$fran['pnh_franchise_id']}) was on $sus_type_arr[$sus_type]".date("g:ia d/m/y")." by {$user['username']}","Franchise account suspension");
		redirect("admin/pnh_franchise/$franchise_id");
	}
	
	function jx_deals_report()
	{
		$p=$_POST['p'];
		$data['deals']=$this->db->query("$p")->result_array();
		$this->load->view("admin/body/deals_report_frag",$data);
	}
	
	function clear_dealsrep_cache()
	{
		$this->load->library("pettakam",array("repo"=>"cache","ext"=>"pkm_snp"));
		$this->pettakam->clear("deals_report");
		redirect("admin/deals_report");
	}
	
	function deals_report()
	{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$this->load->library("pettakam",array("repo"=>"cache","ext"=>"pkm_snp"));
		$data['page']="deals_report";
		$this->load->view("admin",$data);
	}
	
	function pnh_shipment_sms_notify($fid=false)
	{
		$user=$this->auth(true);
		if($_POST)
		{
			if($this->input->post("start"))
			{
				$fid=$this->input->post("fid");
				$start=$this->input->post("start");
				$end=$this->input->post("end");
			}else
			{
				$fid=$this->input->post("fid");
				$transids=$this->input->post("transid");
				$temp=$this->input->post("template");
				$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
				foreach($transids as $t)
				{
					$tran=$this->db->query("select o.userid from king_orders o where o.transid=? limit 1",$t)->row_array();
					$m=$this->db->query("select * from pnh_member_info where user_id=?",$tran['userid']);
					if(empty($m['mobile']))
						continue;
					$temp=str_ireplace("%transid%", $t,$temp);
					$temp=str_ireplace("%mname%", $m['first_name']." ".$m['last_name'] ,$temp);
					$temp=str_ireplace("%fname%", $fran['franchise_name'],$temp);
					$this->erpm->pnh_sendsms($m['mobile'],$temp);
				}
			}
		}
		if($fid)
		{
			$start=strtotime($start);
			$end=strtotime($end);
			$data['orders']=$this->db->query("select t.transid,m.first_name,m.last_name,m.mobile as member_mobile,f.franchise_name,f.franchise_id,o.actiontime from king_transactions t join pnh_m_franchise_info f on f.franchise_id=t.franchise_id join king_orders o on o.transid=t.transid join pnh_member_info m on m.user_id=o.userid where t.franchise_id=? and t.init between ? and ? group by t.transid order by o.actiontime desc",array($fid,$start,$end))->result_array();
			$data['fran']=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
		}
		$data['page']="pnh_shipment_sms_notify";
		$this->load->view("admin",$data);
	}
	
	function changepasswd()
	{
		$user=$this->auth();
		if($_POST)
		{
			$p=$this->input->post("p");
			$cp=$this->input->post("cp");
			if($p!=$cp)
				show_error("Passwords are not same");
			if(strlen($p)<6)
				show_error("Password should be atleast 6 characters length");
			$this->db->query("update king_admin set password=?  where id=? limit 1",array(md5($p),$user['userid']));
			$this->erpm->flash_msg("Password changed");
			redirect("admin/dashboard");
		}
		$data['page']="changepasswd";
		$this->load->view("admin",$data);
	}
	
	function pnh_update_call_log($lid)
	{
		$this->auth();
		$this->db->query("update pnh_call_log set msg=? where id=? limit 1",array($this->input->post("msg"),$lid));
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function makeacall()
	{
		//$user=$this->auth(CALLCENTER_ROLE,true);
		$user=$this->erpm->auth();
		if($user==false)
			die("0");
		foreach(array("agent","customer") as $i)
			$$i=$this->input->post("$i");
		$franmob=substr($customer,1);
		$fran=$this->db->query("select franchise_id from pnh_m_franchise_info where (login_mobile1=$franmob && login_mobile1!='') or (login_mobile2=$franmob && login_mobile2!='')")->row_array();
		if(!empty($fran))
			$this->db->insert("pnh_call_log",array("franchise_id"=>$fran['franchise_id'],"created_by"=>$user['userid'],"created_on"=>time()));
		$post_data = array(
		    'From' => "$agent",
		    'To' => "$customer",
		    'CallerId' => "09243404342",
		    'CallType' => "trans"
		);
		$this->session->set_userdata("agent_mobile",$agent);
 
		$exotel_sid = "snapittoday"; // Your Exotel SID
		$exotel_token = "491140e9fbe5c507177228cf26cf2f09356e042c"; // Your exotel token
		 
		$url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Calls/connect";
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
		$http_result = curl_exec($ch);
		$error = curl_error($ch);
		$http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
		curl_close($ch);
		die("1");
	}
	
	
	function sales_analytics_graph()
	{
		$this->auth(ADMINISTRATOR_ROLE);
		$data['page']="sales_analytics_graph";
		$this->load->view("admin",$data);
	}
	
	function sales_get_salesbyyear($y='')
	{
		$this->auth(ADMINISTRATOR_ROLE);
		if(!$y)
			$y = date('Y');
			
		$sql = 'select month(from_unixtime(init)) as m,
						is_pnh,
						ifnull(round(partner_id/partner_id),0) as is_partner,
						count(*) as total 
						from king_transactions 
						where year(from_unixtime(init)) = ? 
					group by m,is_pnh,is_partner
				';
		$res = $this->db->query($sql,$y);
		$ticks = array();
		$y_sales_summary = array();
		for($i=1;$i<=12;$i++)
		{
			$ticks[$i-1] = date('F Y',strtotime($y.'-'.(($i<10)?'0'.$i:$i).'-01')); 
			$y_sales_summary[$ticks[$i-1]]=array('sit'=>0,'pnh'=>0,'part'=>0,'all'=>0);
		}
		
		
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				$m = $row['m'];
				$y_sales_summary[$ticks[$m-1]]['all'] += $row['total']; 
				if($row['is_pnh'])
				{
					$y_sales_summary[$ticks[$m-1]]['part'] = $row['total'];
				}else if($row['is_partner'])
				{
					$y_sales_summary[$ticks[$m-1]]['pnh'] = $row['total'];
				}else {
					$y_sales_summary[$ticks[$m-1]]['sit'] = $row['total'];
				}
					
			}
		}
		
		$orders = array();
		$orders['sit'] = array();
		$orders['pnh'] = array();
		$orders['part'] = array();
		$orders['all'] = array();
		foreach($y_sales_summary as $ysale)
		{
			array_push($orders['sit'],$ysale['sit']);
			array_push($orders['pnh'],$ysale['pnh']);
			array_push($orders['part'],$ysale['part']);
			array_push($orders['all'],$ysale['all']);
		}
		
		 
		
		$output = array();
		$output['year'] = $y;
		$output['ticks'] = $ticks;
		$output['summary'] = $orders;
		echo json_encode($output);
		
	}
	
	function createpaf()
	{
		$this->auth(PAF_ROLE);
		$data['page']='createpaf';
		$this->load->view("admin",$data);
	}
	
	function process_create_paf()
	{
		$this->auth(PAF_ROLE);
		if(!$_POST)
			exit;
			
			
		$user=$this->auth(true);
		$user_id = $user['userid'];

		$remarks = $this->input->post('remarks');
		$handled_by = $this->input->post('handled_by'); 
		$product = $this->input->post('product');
		$qty = $this->input->post('qty');	
		$mrp = $this->input->post('mrp');	
		$vendor = $this->input->post('vendor');
		$datetime = date('Y-m-d H:i:s');	
		
		
		// create paf id  
		$this->db->query("insert into t_paf_list (handled_by,paf_status,remarks,created_on,created_by) values(?,?,?,?,?) ",array($handled_by,1,$remarks,$datetime,$user_id));
		$paf_id = $this->db->insert_id();
		
		$this->load->library('table');
		$tmpl = array ( 'table_open'  => '<table border="1" cellpadding="5" cellspacing="0" >' );
		$this->table->set_template($tmpl);
		
		foreach($product as $i=>$p)
		{
			$vid = $vendor[$i];
			$this->db->query("insert into t_paf_productlist (paf_id,product_id,vendor_id,qty,mrp,notify_handler,po_id,created_on,created_by) values(?,?,?,?,?,?,?,?,?) ",array($paf_id,$p,$vid,$qty[$i],$mrp[$i],0,0,$datetime,$user_id));
		}
		
		if($_SERVER['HTTP_HOST'] != 'localhost')
		{
			$notify_msg = '
							<h3> PAF Order list Generated '.date('Y-m-d h:i a').'</h3>
							<br>Pleae find below list of products in PAF <br>
						';
			
			$notify_msg .= $this->table->generate($this->db->query("select a.id as Ref_no,a.product_id,b.product_name,ifnull(c.vendor_name,'Unknown') as vendor_name,a.mrp,a.qty,date_format(a.created_on,'%d-%m-%Y %r') as created_on 
											from t_paf_productlist a
											join m_product_info b on a.product_id = b.product_id
											left join m_vendor_info c on c.vendor_id = a.vendor_id 
											where a.paf_id = ?  
											order by c.vendor_id",$paf_id));
			
			
			
			
			$from = 'erp@snapittoday.in';
			
			$email_id = $this->db->query('select email from m_employee_list where id = ? ',$handled_by)->row()->email;
			if($email_id && $_SERVER['HTTP_HOST'] != 'localhost')
			{
				$subj = 'Order list Generated - REFNO:'.$paf_id.' - '.date('Y-m-d h:i a');
				$this->vkm->email($email_id,$subj,($notify_msg));
			}
		}
		
		
		
		$this->session->set_flashdata("erp_pop_info","PAF Created");
		redirect('admin/editpaf/'.$paf_id,'refresh');
		
	}
	
	function editpaf($pafid = '')
	{
		$this->auth(PAF_ROLE);
		if(!$pafid)
			show_error('paf id missing');
			
		$data['pafdata'] = $this->erpm->getpafdata($pafid);
		$data['paf_smslog'] = $this->erpm->getpafsmslog($pafid);
		$data['current_stock'] = $this->erpm->getpafprodstk($pafid); 
		$data['past_orders']= $this->erpm->getpafprod_pastorders($pafid);
		$data['pending_qty'] = $this->erpm->getpafpendingstk($pafid);
		
		// reset prod stock details in assoc array 
		$prod_stk_det = array();
		if($data['current_stock']->num_rows())
		{
			foreach($data['current_stock']->result_array() as $row)
			{
				if(!isset($prod_stk_det[$row['product_id']]))
					$prod_stk_det[$row['product_id']] = array('cur_stk'=>0,'past_orders'=>0,'pending_qty'=>0);
				$prod_stk_det[$row['product_id']]['cur_stk'] = $row['qty'];
			}
		}
		
		if($data['past_orders']->num_rows())
		{
			foreach($data['past_orders']->result_array() as $row)
			{
				if(!isset($prod_stk_det[$row['product_id']]))
					$prod_stk_det[$row['product_id']] = array('cur_stk'=>0,'past_orders'=>0,'pending_qty'=>0);
				$prod_stk_det[$row['product_id']]['past_orders'] = $row['s'];
			}
		}
		
		if($data['pending_qty']->num_rows())
		{
			foreach($data['pending_qty']->result_array() as $row)
			{
				if(!isset($prod_stk_det[$row['product_id']]))
					$prod_stk_det[$row['product_id']] = array('cur_stk'=>0,'past_orders'=>0,'pending_qty'=>0);
				$prod_stk_det[$row['product_id']]['pending_qty'] = $row['qty'];
			}
		}
	 
		
		$data['prod_stk_det'] = $prod_stk_det;
		$data['page']='editpaf';
		$this->load->view("admin",$data);
	}
	
	function process_update_paf()
	{
		$this->auth(PAF_ROLE);
		if(!$_POST)
			exit;
		 
		$user=$this->auth(true);
		$user_id = $user['userid'];
			
		$paf_id = $this->input->post('paf_id');
		$handled_by = $this->input->post('handled_by');
		
		$prod_paf_id = $this->input->post('prod_paf_id');
		$product = $this->input->post('product');
		$qty = $this->input->post('qty');	
		$mrp = $this->input->post('mrp');	
		$vendor = $this->input->post('vendor');
		$datetime = date('Y-m-d H:i:s');	
		
		
		
		$notify_msg = $this->input->post('notify_msg');
		
		
		
		
		// update paf product list   
		foreach($prod_paf_id as $i=>$pp_id)
		{
		 
			if($pp_id)
			{
				$this->db->query("update t_paf_productlist set product_id = ?,qty=?,mrp=?,vendor_id=?,modified_on=?,modified_by=? where paf_id = ? and id = ? ",array($product[$i],$qty[$i],$mrp[$i],$vendor[$i],$datetime,$user_id,$paf_id,$pp_id));	
			}
			else
			{
				$this->db->query("insert into t_paf_productlist (paf_id,product_id,qty,mrp,vendor_id,notify_handler,po_id,created_on,created_by) values(?,?,?,?,?,?,?,?,?) ",array($paf_id,$product[$i],$qty[$i],$mrp[$i],$vendor[$i],0,0,$datetime,$user_id));	
			}
			
			
		}
		
		if($notify_msg)
		{
			$this->db->query('insert into t_paf_smslog (handled_by,message,paf_id,status,logged_on) values (?,?,?,?,?) ',array($handled_by,$notify_msg,$paf_id,1,$datetime));
			$this->_erp_send_sms($this->db->query('select mobile as m from m_employee_list where id = ? ',$handled_by)->row()->m,$notify_msg);
			
			
			$this->load->library('table');
			$tmpl = array ( 'table_open'  => '<table border="1" cellpadding="5" cellspacing="0" >' );
			$this->table->set_template($tmpl);
			
			$notify_msg_body = '
							<h3> PAF Order list Generated '.date('Y-m-d h:i a').'</h3>
							<br>Pleae find below list of products in PAF <br>
						';
			$notify_msg_body .= '<br>'.nl2br($notify_msg).'<br>';
			
			$notify_msg_body .= $this->table->generate($this->db->query("select a.id as Ref_no,a.product_id,b.product_name,ifnull(c.vendor_name,'Unknown') as vendor_name,a.mrp,a.qty,date_format(a.created_on,'%d-%m-%Y %r') as created_on 
											from t_paf_productlist a
											join m_product_info b on a.product_id = b.product_id
											left join m_vendor_info c on c.vendor_id = a.vendor_id 
											where a.paf_id = ?  
											order by c.vendor_id",$paf_id));
			
			
			$from = 'erp@snapittoday.in';
			
			$email_id = $this->db->query('select email from m_employee_list where id = ? ',$handled_by)->row()->email;
			if($email_id && $_SERVER['HTTP_HOST'] != 'localhost')
			{
				$subj = 'Order list update - '.date('Y-m-d h:i a');
				$this->vkm->email($email_id,$subj,($notify_msg_body));
			}
			
		}
		
		
		$this->session->set_flashdata("erp_pop_info","PAF Updated");
		
		redirect('admin/editpaf/'.$paf_id,'refresh');
		
	}

	
	function paflist()
	{
		$this->auth(PAF_ROLE);
		$data['paflist'] = $this->db->query("select a.id as paf_id,paf_status,b.name as handled_by_name,
														mobile,email,a.created_on  
														from t_paf_list a
														join m_employee_list b on a.handled_by = b.id 
											")->result_array();
		$data['page']='paflist';
		$this->load->view("admin",$data);
	}
	
	/**
	 * fucntion to cancel paf by id 
	 */
	function jx_cancel_paf()
	{
		$this->auth(PAF_ROLE);
		if(!$_POST)
			die();
		$output = array();
		$user = $this->auth(true);
		 
		$id = $this->input->post('id');
		$this->db->query('update t_paf_list set paf_status=3,cancelled_on=?,cancelled_by=? where id = ? and paf_status = 1',array(date('Y-m-d H:i:s'),$user['userid'],$id));
		if($this->db->affected_rows())
		{
			$output['status'] = 'success';
			$output['message'] = 'Cancelled Successfully';
		}
		else
		{
			$output['status'] = 'error';
			$output['message'] = 'Unable to cancel this paf';
		}
		echo json_encode($output);
	}
	
	function paf_stockintake($paf_id = '')
	{
		$this->auth(PAF_ROLE);
		if(!$paf_id)
			die();
			
		// get paf data by paf_id 
		$data['pafdata'] = $this->erpm->getpafdata($paf_id);

		if($data['pafdata'][0]['paf_status'] != 1)
		{
			$this->session->set_flashdata("erp_pop_info","Stock intake cannot be processed,paf is not opened");
			if(isset($_SERVER['HTTP_REFERER']))
				redirect($_SERVER['HTTP_REFERER'],'refresh');
			else
				redirect('admin/paflist','refresh');
			die();
		}
		
		
		$data['page']='paf_stockintake';
		$this->load->view("admin",$data);
		
	}
	
	/**
	 * function to process create po and stock intake  
	 */
	function process_paf_stockintake()
	{
		$this->auth(PAF_ROLE);
		
		$paf_id = $this->input->post('paf_id');
		
		$product = $this->input->post('product');
		$mrp = $this->input->post('mrp');
		$pprice = $this->input->post('pprice');
		$qty = $this->input->post('qty');
		$vendor = $this->input->post('vendor');
		
		
		
		
		$paf_status = $this->db->query("select paf_status from t_paf_list where id = ? ",$paf_id)->row()->paf_status;
		if($paf_status != 1)
		{
			if($paf_status == 3)
				show_error("Stockintake cannot be processed, paf is closed ");
			else if($paf_status == 2)
				show_error("Stockintake cannot be processed, paf is not opened ");
				
			die();
		}
		
		
		
		$user=$this->erpm->getadminuser();
		
		// Create PO and process Stock intake automatically
		
		

		// build PO by vendor 
		$prod_vendor_link = array();
		foreach($vendor as $i=>$v)
		{
			if(!isset($prod_vendor_link[$v]))
			{
				$prod_vendor_link[$v] = array();
			}
			array_push($prod_vendor_link[$v],array('pid'=>$product[$i],'qty'=>$qty[$i],'mrp'=>$mrp[$i],'pprice'=>$pprice[$i])); 
		} 
		
		 
		$po_ids = array();
		foreach($prod_vendor_link as $ven_id=>$prod_det)
		{
		
			// Create PO Source
			$this->db->query("insert into t_po_info(vendor_id,paf_id,remarks,created_on,po_status) values(?,?,?,now(),0)",array($ven_id,$paf_id,'created by paf-'.$paf_id));
			$po=$this->db->insert_id();
			$total=0;
			foreach($prod_det as $i=>$p)
			{
				 $product_id = $p['pid'];
				 $order_qty = $p['qty'];
				 $mrp = $p['mrp'];
				 // calculate margin from mrp and purchase price 
				 $margin = (1-($p['pprice']/$p['mrp']))*100;
				 $scheme_discount_value = 0;
				 $scheme_discount_type = 0;
				 $purchase_price = $p['pprice'];
				 $is_foc = 0;
				 $has_offer = 0;
				 $special_note = 'Create by paf-'.$paf_id;
				 
					
				//$sv=$pt['sch_type'][$i]?$pt['mrp'][$i]*$pt['sch_discount'][$i]/100:$pt['sch_discount'][$i];
				$sv = 0;
				
				$inp=array($po,$product_id,$order_qty,$mrp,$margin,$scheme_discount_value,$scheme_discount_type,$purchase_price,$is_foc,$has_offer,$special_note);
				$this->db->query("insert into t_po_product_link(po_id,product_id,order_qty,mrp,margin,scheme_discount_value,scheme_discount_type,purchase_price,is_foc,has_offer,special_note,created_on)
																						values(?,?,?,?,?,?,?,?,?,?,?,now())",$inp);
				
				
				$total+=$p['mrp']*$p['qty'];
			}
			$this->db->query("update t_po_info set total_value=? where po_id=? limit 1",array($total,$po));
			
			array_push($po_ids,$po);
		}
		
		
		
		
		// STOCK INTAKE RECODED VENDOR PO WISE
		 
		$grn_ids = array();
		$grn_ven_ids = array();
		$pos = array();
		$user=$this->erpm->getadminuser();
		foreach($po_ids as $p)
		{
			 
			
			$vid=$this->db->query("select vendor_id from t_po_info where po_id=?",$p)->row()->vendor_id;
			$this->db->query("insert into t_grn_info(vendor_id,remarks,created_by,created_on) values(?,?,?,now())",array($vid,'Created from paf-'.$paf_id,$user['userid']));
			$grn= $this->db->insert_id();
			$grn_ids[$p] = $grn;
			$grn_ven_ids[$vid] = $grn;
			 
				if(!isset($pos[$p]))
					$pos[$p]=array();
					
				$poi_data_res=$this->db->query("select a.*,b.brand_id,c.rack_bin_id 
													from t_po_product_link a
													join m_product_info b on a.product_id = b.product_id 
													join m_rack_bin_brand_link c on c.brandid = b.brand_id 
													where po_id= ? ",array($p)); 	
					
				if($poi_data_res->num_rows())
				{
					
					foreach($poi_data_res->result_array() as $po_p)
					{
						$po=array();
						$po['tax']=$this->db->query("select vat from m_product_info where product_id=?",$po_p['product_id'])->row()->vat;
						$po['product']=$po_p['product_id'];
						$po['oqty']=$po_p['order_qty'];
						$po['rqty']=$po_p['order_qty'];
						$po['mrp']=$po_p['mrp'];
						$po['price']=$po_p['purchase_price'];
						// get default storage localtion for product . 
						$po['rackbin']=$po_p['rack_bin_id'];
						$po['location']=$this->db->query("select location_id from m_rack_bin_info where id=?",$po_p['rack_bin_id'])->row()->location_id;
						$po['margin']=$po_p['margin'];
						$po['foc']=$po_p['is_foc'];
						$po['offer']=$po_p['has_offer'];
						$pos[$p][]=$po;
					}
				}
				
				
		} 
			
		 


			 
			
			
			foreach($pos as $poid=>$po)
			{
				foreach($po as $p)
				{
					$inp=array($grn_ids[$poid],$poid,$p['product'],$p['oqty'],$p['rqty'],$p['mrp'],$p['price'],$p['tax'],$p['location'],$p['rackbin'],$p['margin'],$p['foc'],$p['offer']);
					$this->db->query("insert into t_grn_product_link(grn_id,po_id,product_id,invoice_qty,received_qty,mrp,purchase_price,tax_percent,location_id,rack_bin_id,margin,is_foc,has_offer,created_on) 
																					values(?,?,?,?,?,?,?,?,?,?,?,?,?,now())",$inp);
					$this->db->query("update t_po_product_link set received_qty=received_qty+? where po_id=? and product_id=?",array($p['rqty'],$poid,$p['product']));
					if($p['rqty']!=0)
					{
						$p['product_id']=$p['product'];
						if($p['mrp']!=0)
							$pc_prod=$this->db->query("select * from m_product_info where product_id=? and mrp!=?",array($p['product_id'],$p['mrp']))->row_array();
						else $pc_prod=array();
						
						if(!empty($pc_prod))
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
							$r_itemids=array_unique(array_merge($r_itemids,$r_itemids2));
							foreach($r_itemids as $d)
							{
								$itemid=$d['itemid'];
								$item=$this->db->query("select orgprice,price from king_dealitems where id=?",$itemid)->row_array();
								$o_price=$item['price'];$o_mrp=$item['orgprice'];
								$n_mrp=$this->db->query("select sum(p.mrp*l.qty) as mrp from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$itemid)->row()->mrp+$this->db->query("select sum((select avg(mrp) from m_product_group_deal_link l join products_group_pids pg on pg.group_id=l.group_id join m_product_info p on p.product_id=pg.product_id where l.itemid=$itemid)*(select qty from m_product_group_deal_link where itemid=$itemid)) as mrp")->row()->mrp;
								$n_price=$pc_prod['is_serial_required']?$item['price']:$item['price']/$o_mrp*$n_mrp;
								$inp=array("itemid"=>$itemid,"old_mrp"=>$o_mrp,"new_mrp"=>$n_mrp,"old_price"=>$o_price,"new_price"=>$n_price,"created_by"=>$user['userid'],"created_on"=>time(),"reference_grn"=>$grn);
								$this->db->insert("deal_price_changelog",$inp);
								$this->db->query("update king_dealitems set orgprice=?,price=? where id=? limit 1",array($n_mrp,$n_price,$itemid));
								
								// Disable special margin if set 
								$this->db->query("update pnh_special_margin_deals set is_active = 0 where i_price != ? and itemid = ? and is_active = 1 " ,array($n_price,$itemid));
						
								if($this->db->query("select is_pnh as b from king_dealitems where id=?",$itemid)->row()->b)
								{
									$o_s_price=$this->db->query("select store_price from king_dealitems where id=?",$itemid)->row()->store_price;
									//$n_s_price=$o_s_price/$o_mrp*$n_mrp;
									$n_s_price=$pc_prod['is_serial_required']?$o_s_price:$o_s_price/$o_mrp*$n_mrp;
									$this->db->query("update king_dealitems set store_price=? where id=? limit 1",array($n_s_price,$itemid));
									$o_n_price=$this->db->query("select nyp_price as p from king_dealitems where id=?",$itemid)->row()->p;
									//$n_n_price=$o_n_price/$o_mrp*$n_mrp;
									$n_n_price=$pc_prod['is_serial_required']?$o_n_price:$o_n_price/$o_mrp*$n_mrp;
									$this->db->query("update king_dealitems set nyp_price=? where id=? limit 1",array($n_n_price,$itemid));
								}
								foreach($this->db->query("select * from partner_deal_prices where itemid=?",$itemid)->result_array() as $r)
								{
									$o_c_price=$r['customer_price'];
									//$n_c_price=$o_c_price/$o_mrp*$n_mrp;
									$n_c_price=$pc_prod['is_serial_required']?$o_c_price:$o_c_price/$o_mrp*$n_mrp;
									$o_p_price=$r['partner_price'];
									//$n_p_price=$o_p_price/$o_mrp*$n_mrp;
									$n_p_price=$pc_prod['is_serial_required']?$o_p_price:$o_p_price/$o_mrp*$n_mrp;
									$this->db->query("update partner_deal_prices set customer_price=?,partner_price=? where itemid=? and partner_id=?",array($n_c_price,$n_p_price,$itemid,$r['partner_id']));
								}
							}
						}
						$imeis=explode(",",$this->input->post("imei{$p['product']}"));
						foreach($imeis as $imei)
							$this->db->insert("t_imei_no",array('product_id'=>$p['product_id'],"imei_no"=>$imei,"grn_id"=>$grn,"created_on"=>time()));
						if($this->db->query("select 1 from t_stock_info where product_id=? and location_id=? and rack_bin_id=? and mrp=?",array($p['product'],$p['location'],$p['rackbin'],$p['mrp']))->num_rows()==0)
							$this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,available_qty,created_on) values(?,?,?,?,?,now())",array($p['product'],$p['location'],$p['rackbin'],$p['mrp'],$p['rqty']));
						else
							$this->db->query("update t_stock_info set available_qty=available_qty+?,modified_on=now() where product_id=? and location_id=? and rack_bin_id=? and mrp=? limit 1",array($p['rqty'],$p['product'],$p['location'],$p['rackbin'],$p['mrp']));
						$this->erpm->do_stock_log(1,$p['rqty'],$p['product'],$grn);
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
			
			 
			
			$inv_vendor_ids = $_POST['inv_vendor_id'];
			$inv_nos = $_POST['invno'];
			
			$invs=array();
			foreach($inv_vendor_ids as $i=>$vid)
			{
				if(!isset($grn_ven_ids[$vid]))
					continue ;
				$grn = $grn_ven_ids[$vid];
				$no = $inv_nos[$i];
				$this->db->query("insert into t_grn_invoice_link(grn_id,purchase_inv_no,purchase_inv_date,purchase_inv_value,created_on) values(?,?,?,?,now())",array($grn,$no,$_POST['invdate'][$i],$_POST['invamount'][$i]));
				$inv_id=$this->db->insert_id();
				
				if(isset($_FILES['scan_'.($i+1)]))
				{
					if(!strlen($_FILES['scan_'.($i+1)]['name']) > 0)		
							continue;
					
					if(file_exists(ERP_PHYSICAL_IMAGES."invoices/$inv_id.jpg"))
							unlink(ERP_PHYSICAL_IMAGES."invoices/$inv_id.jpg");
						move_uploaded_file($_FILES['scan_'.($i+1)]['tmp_name'], ERP_PHYSICAL_IMAGES."invoices/{$inv_id}.jpg");	
				}
			 	
			}
			
			
			$this->db->query("update t_paf_list set paf_status = 2,modified_by=?,modified_on = ? where paf_status = 1 and id = ?",array($user['userid'],date('Y-m-d H:i:s'),$paf_id));
			
		
			$this->session->set_flashdata("erp_pop_info","PO Created and Stock intake processed");
			redirect('admin/stock_intake_list','refresh');
		
		
		
		
		
	}
	
	/**
	 * fucntion to print paf products grouped by vendor 
	 * 
	 * @param $paf_id
	 */
	function print_paf($paf_id='')
	{
		$this->auth(PAF_ROLE);
		if(!$paf_id)
		{
			echo '<script>alert("Invalid paf / products not found in paf")</script>';
			exit;
		}
		
		$sql = "select b.vendor_id,a.id as paf_id,
					handled_by,
					b.id as prod_paf_id,
					ifnull(c.vendor_name,'UNKNOWN') as vendor_name,
					group_concat(distinct concat(b.id,'::',b.product_id,'::',d.product_name,'::',b.mrp,'::',b.qty)) as prod_list,
					notify_handler,
					po_id,paf_status,a.created_on   
				from t_paf_list a
				join t_paf_productlist b on a.id = b.paf_id
				join m_product_info d on b.product_id = d.product_id 
				left join m_vendor_info c on c.vendor_id = b.vendor_id 
				where a.id = ?   
				group by b.vendor_id 
				order by vendor_name,product_name 
				";
		$res = $this->db->query($sql,$paf_id);
		if($res->num_rows())
		{
			$tbl_data = '<html><head><title>PAF Details</title><style>body{font-family:arial;font-size:12px;}h3{margin:3px;}table{font-size:12px;}</style></head><body>';
		
			$pafdata = $res->result_array();
			foreach($pafdata as $row)
			{
				$tbl_data .= '<div style="margin-bottom:50px">';
				$tbl_data .= '<h2>Product Purchase List - REFNO:'.$row['paf_id'].' - '.date('d/m/Y').'</h2>';
				$tbl_data .= '<h3>VENDOR : '.$row['vendor_name'].'</h3>';	
				$prod_list = explode(',',$row['prod_list']);
				$tbl_data .= '<table cellpadding=5 cellspacing=0 border=1 width="100%">';
				$tbl_data .= '<tr>';
				$tbl_data .= '<td>#</td>';
				$tbl_data .= '<td>Refno</td>';
				$tbl_data .= '<td>Name</td>';
				$tbl_data .= '<td>MRP</td>';
				$tbl_data .= '<td>Qty</td>';
				$tbl_data .= '</tr>';
				foreach($prod_list as $no=>$prod)
				{
					list($paf_prod_id,$prod_id,$prod_name,$mrp,$qty) = explode('::',$prod);
					$tbl_data .= '<tr>';
					$tbl_data .= '	<td>'.($no+1).'</td>';
					$tbl_data .= '	<td>'.$paf_prod_id.'</td>';
					$tbl_data .= '	<td>'.$prod_name.'</td>';
					$tbl_data .= '	<td>'.$mrp.'</td>';
					$tbl_data .= '	<td>'.$qty.'</td>';
					$tbl_data .= '</tr>';
				}
				$tbl_data .= '</table>';
				$tbl_data .= '<hr></div>';
			}
			$tbl_data .= '</body>';
			$tbl_data .= '</html>';
			
			echo $tbl_data;
			echo '<script>window.print()</script>';
		}else
		{
			echo '<script>alert("Invalid paf / products not found in paf")</script>';
		}
		
		
	}
	
	/**
	 * function to export paf file 
	 * 
	 * @param $paf_id
	 */
	function export_paf($paf_id)
	{
		$this->auth(PAF_ROLE);
		if(!$paf_id)
		{
			echo '<script>alert("Invalid paf / products not found in paf")</script>';
			exit;
		}
		$sql = "select b.vendor_id,a.id as paf_id,
					handled_by,
					b.id as prod_paf_id,
					ifnull(c.vendor_name,'UNKNOWN') as vendor_name,
					group_concat(distinct concat(b.id,'::',b.product_id,'::',d.product_name,'::',b.mrp,'::',b.qty)) as prod_list,
					notify_handler,
					po_id,paf_status,a.created_on   
				from t_paf_list a
				join t_paf_productlist b on a.id = b.paf_id
				join m_product_info d on b.product_id = d.product_id 
				left join m_vendor_info c on c.vendor_id = b.vendor_id 
				where a.id = ?  
				group by b.vendor_id 
				order by vendor_name,product_name 
				";
		$res = $this->db->query($sql,$paf_id);
		if($res->num_rows())
		{
			
			$pafdata = $res->result_array();
		
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename=paf_file_'.$pafdata[0]['paf_id'].'.csv');
			header('Pragma: no-cache');
			 
			
			echo "\r\n".'"PAF LIST - #"'.$pafdata[0]['paf_id']."\r\n"."\r\n";
			
			foreach($pafdata as $row)
			{
				 
				echo '"Slno","Refno","VendorName","ProductName","MRP","Qty"'."\r\n";
				$prod_list = explode(',',$row['prod_list']);
				foreach($prod_list as $no=>$prod)
				{
					list($paf_prod_id,$prod_id,$prod_name,$mrp,$qty) = explode('::',$prod);
					 
					
					echo '"'.($no+1).'","'.$paf_prod_id.'","'.$row['vendor_name'].'","'.$prod_name.'","'.$mrp.'","'.$qty.'"'."\r\n";
					
				}
				
				echo "\r\n";
				echo "\r\n";
				 
			}
			 
			 
			 
		}else
		{
			echo '<script>alert("Invalid paf / products not found in paf")</script>';
		}
		
	}
	
	function _erp_send_email($from,$to,$subj,$body)
	{
		 
				
	}
	
	function _erp_send_sms($to,$msg) 
	{
		if ($_SERVER ['HTTP_HOST'] == "localhost")
			return;
		$exotel_sid = "snapittoday";
		$exotel_token = "491140e9fbe5c507177228cf26cf2f09356e042c";
		$post = array ('From' => '9243404342', 'To' => $to, 'Body' => $msg );
		$url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_FAILONERROR, 0 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, http_build_query ( $post ) );
		$http_result = curl_exec ( $ch );
		$error = curl_error ( $ch );
		$http_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
		curl_close ( $ch );
	}

	/**
	 * fucntion to get deal item stock details by item_id 
	 */
	function pnh_jx_dealstock_det($itemid='',$is_pnh=0)
	{
		$this->auth(true);
		
		if(!$itemid)
			exit;
			
		$is_pnh = $is_pnh?1:0;	
		
		
		$item_stk = $this->erpm->do_stock_check(array($itemid),1,true);
		$output = array();
		$arr = array_values($item_stk);
		if(!empty($arr))
		{
			$output['itm_stk_det'] = $arr[0];
			$output['status'] = 'success';
		}else
		{
			$output['status'] = 'error';
		}
		echo json_encode($output);	
		 
	}
	
	function pnh_sales_report()
	{
		
		$this->auth(PNH_EXECUTIVE_ROLE|FINANCE_ROLE|PNH_SALES_REPORT);
				
		$data['st_d'] = ($this->input->post('st_d'))?$this->input->post('st_d'):date('Y-m-d');
		$data['en_d'] = ($this->input->post('en_d'))?$this->input->post('en_d'):date('Y-m-d');
		$data['page']='pnh_sales_report';
		$this->load->view("admin",$data);
	}
	
	function pnh_getsalesanalytics()
	{
		$this->auth(PNH_EXECUTIVE_ROLE|FINANCE_ROLE|PNH_SALES_REPORT);
		
		$output = array();
		
		$type = $this->input->post('type');
		$terr_id = $this->input->post('terr_id');
		$town_id = $this->input->post('town_id');
		$fid = $this->input->post('fid');
		$show_orders = $this->input->post('orders');
		
		$st_d = $this->input->post('st_d');
		$en_d = $this->input->post('en_d');
		
		list($ty,$tm,$td) = explode('-',$st_d);
		$st_d_ts = mktime(0,0,0,$tm,$td,$ty);
		
		list($ty,$tm,$td) = explode('-',$en_d);
		$en_d_ts = mktime(23,59,59,$tm,$td,$ty);
		
		$cond = '';
		if($terr_id){
			$cond .= ' and b.territory_id = '.$terr_id;
		}
		
		if($town_id){
			$cond .= ' and b.town_id = '.$town_id;
		}
		
		if($fid){
			$cond .= ' and b.franchise_id = '.$fid;
		}
		
		
		$sales_bymenu = array();
		$menu_list = $this->db->query("select id,name from pnh_menu  order by name asc ")->result_array();
		foreach($menu_list as $menu)
		{
			$sales_bymenu[$menu['id']] = array();
			$sales_bymenu[$menu['id']]['name'] = $menu['name'];
			$sales_bymenu[$menu['id']]['sales'] = 0;
			$sales_bymenu[$menu['id']]['amt'] = 0;
		}
		
		$sql = 'select b.franchise_id,ifnull(m.id,0) as menu_id,ifnull(m.name,"Other") as menu_name,
							count(distinct a.transid) as total_orders,
							sum((o.i_orgprice-o.i_discount-o.i_coup_discount)*o.quantity) as total_order_value 
						from pnh_m_franchise_info b 
						left join king_transactions a on a.franchise_id = b.franchise_id and is_pnh = 1 and a.init between ? and ? 
						left join king_orders o on o.transid = a.transid
						left join king_dealitems di on di.id = o.itemid 
						left join king_deals d on d.dealid = di.dealid 
						left join pnh_menu m on m.id = d.menuid 
						where 1 '.$cond.' and o.transid is not null 
						group by m.id 
						order by total_orders   
					';
		
		
		$resp = $this->db->query($sql,array($st_d_ts,$en_d_ts));
		
		$total_order_value = 0;
		if($resp->num_rows())
		{
			
			foreach($resp->result_array() as $row)
			{
				$sales_bymenu[$row['menu_id']]['sales'] = $row['total_orders'];
				$sales_bymenu[$row['menu_id']]['amt'] = round($row['total_order_value']);
				$total_order_value += $row['total_order_value'];
			}
		}
		
		sort($sales_bymenu);
		
		if($show_orders && $fid)
		{
			$sql_o = "select a.transid,franchise_name,a.id as order_id,c.id as itemid,c.name as dealname,
							b.batch_enabled,from_unixtime(b.init) as ordered_on,a.status,
							round(a.i_orgprice,2) as mrp,round(i_discount+i_coup_discount,2) as disc,a.quantity,
							(a.i_orgprice-(i_discount+i_coup_discount)) as landing_price  
							from king_orders a 
							join king_transactions b on a.transid = b.transid 
							join king_dealitems c on c.id = a.itemid
							join king_deals d on d.dealid = c.dealid 
							left join king_invoice e on e.order_id = a.id 
							join pnh_m_franchise_info f on f.franchise_id = b.franchise_id 
							where b.franchise_id = ? and b.init between ? and ?  
						group by a.id
						order by a.transid,b.init 
					";
			$fran_orders_res = $this->db->query($sql_o,array($fid,$st_d_ts,$en_d_ts));
			
			//echo $this->db->last_query();
			
			if($fran_orders_res->num_rows())
				$output['fran_order_list'] = $fran_orders_res->result_array();
			else
				$output['fran_order_list'] = array();
		}
		
		$output['start_d'] = $st_d;
		$output['end_d'] = $en_d;
		
		$output['total_order_value'] = $total_order_value;
		$output['terr_id'] = $terr_id;
		$output['town_id'] = $town_id;
		$output['fid'] = $fid; 
		$output['analytics'] = $sales_bymenu;
		
		echo json_encode($output);
		
	}
	
	/* 
	 * function to generate empoloyee sales summary
	 *  
	 * @auth : superadmin,offline,finance roles 
	 */
	function pnh_employee_sales_summary()
	{
		$this->erpm->auth(true);
		$data['page']='pnh_employee_sales_summary';
		$this->load->view("admin",$data);
	}
	
	/**
	 * function to load franchise orders by selected date range or latest 20 orders by default  
	 */
	function jx_pnh_getfranchiseordersbydate()
	{
		$data['stat'] = $this->input->post('stat');
		$data['fid'] = $this->input->post('fid');
		$data['st_ts'] = strtotime($this->input->post('ord_fil_from').' 00:00:00');
		$data['en_ts'] = strtotime($this->input->post('ord_fil_to').' 23:59:59');
		$this->load->view("admin/body/jx_pnh_franchiseorderlist",$data);
	}
	
	/**
	 * function to resend sms reply for the franchise by reply for id  
	 */
	function jx_pnh_fran_resendreply()
	{
		$reply_for = $this->input->post('resend_reply_for');
		$output = array();
		if($this->db->query("select sender from pnh_sms_log where id = ?",$reply_for)->num_rows())
		{
			$sender = $this->db->query("select sender from pnh_sms_log where id = ?",$reply_for)->row()->sender;
			$msg_det = $this->db->query("select franchise_id,msg from pnh_sms_log where reply_for = ?",$reply_for)->row_array();
			$this->erpm->pnh_sendsms($sender,$msg_det['msg'],$msg_det['franchise_id']);
			$output['message'] = 'Reply message sent';
		}else
		{
			$output['message'] = 'No SMS found';
		}
		
		echo json_encode($output);
	}
	
	function jx_getprodbybarcode()
	{
		$vid = $this->input->post('vid');
		$bc = $this->input->post('bc');
		
		$output = array();
		$sql = "select product_id,product_name,mrp,vendor_id,barcode,brand_margin
						from m_product_info a 
						join m_vendor_brand_link b on a.brand_id = b.brand_id 
						where barcode = ? and vendor_id = ? 
					limit 1 ";
		$res = $this->db->query($sql,array($bc,$vid));
		if($res->num_rows())
		{
			$prod = $res->row_array();
			
			$prod['orders'] = array();
			// check if product is grouped product  
			if($this->db->query("select count(*) as t from products_group_pids where product_id = 53546 ")->row()->t)
			{
				
				$order_det_res = $this->db->query("select b.product_id,o.id,ifnull(sum(ifnull(o.quantity,0)*ifnull(a.qty,0)),0) as req_qty,count(o.id) as total
															from king_orders o 
															join products_group_orders c on o.id = c.order_id 
															join m_product_group_deal_link a on a.itemid = o.itemid 
															join products_group_pids b on a.group_id = b.group_id and c.product_id = b.product_id 
															where c.product_id = ?  and o.status = 0   
															group by c.product_id
												",$prod['product_id']);
				if($order_det_res->num_rows())
					$prod['orders'] = $order_det_res->row_array();	
				
			}else
			{
				$order_det_res = $this->db->query("select ifnull(sum(ifnull(a.quantity,0)*ifnull(c.qty,0)),0) as req_qty,count(a.id) as total   
					from king_orders a 
					join king_dealitems b on a.itemid = b.id
					join m_product_deal_link c on c.itemid = b.id
					where c.product_id = ? and a.status = 0 ",$prod['product_id']);
				if($order_det_res->num_rows())
					$prod['orders'] = $order_det_res->row_array();
			}
			$prod['stk'] = 0;
			$stk_res = $this->db->query("select sum(ifnull(available_qty,0)) as stk from t_stock_info where product_id = ? and available_qty >= 0 ",$prod['product_id']);
			if($stk_res->num_rows())
				$prod['stk'] = $stk_res->row()->stk;
			$output['status'] = 'success';
			$output['prod'] = $prod;
		}else
		{
			$output['status'] = 'error';
			$output['error'] = 'Product not linked to brand';
		}
		
		echo json_encode($output);
		
	}
	
	function pnh_export_sales_summary()
	{
		$this->auth(EXPORT_PNH_SALES_REPORT);
		
		
		$st_d = $this->input->post('e_st_d');
		$en_d = $this->input->post('e_en_d');
		$type = $this->input->post('type');
		
		
		list($ty,$tm,$td) = explode('-',$st_d);
		$st_d_ts = mktime(0,0,0,$tm,$td,$ty);
		
		list($ty,$tm,$td) = explode('-',$en_d);
		$en_d_ts = mktime(23,59,59,$tm,$td,$ty);
		
		
		$sales_bymenu = array();
		$menu_list = $this->db->query("select id,name from pnh_menu where name not like 'NA -%'  order by name asc ")->result_array();
		foreach($menu_list as $menu)
		{
			$sales_bymenu[$menu['id']] = array();
			$sales_bymenu[$menu['id']]['name'] = $menu['name'];
			$sales_bymenu[$menu['id']]['sales'] = 0;
			$sales_bymenu[$menu['id']]['amt'] = 0;
		}
		$grp_cond = '';
		if($type == 1)
		{
			$grp_cond = ' group by b.territory_id,d.menuid ';
		}else if($type == 2)
		{
			$grp_cond = ' group by b.territory_id,b.town_id,d.menuid ';
		}else if($type == 3)
		{
			$grp_cond = ' group by b.territory_id,b.town_id,b.franchise_id,d.menuid ';
		}
		
		$sql = 'select  t.territory_name,twn.town_name,b.franchise_id,b.franchise_name,b.territory_id,b.town_id,ifnull(m.id,0) as menu_id,ifnull(m.name,"Other") as menu_name,
							count(distinct a.transid) as total_orders,
							ifnull(sum((o.i_orgprice-o.i_discount-o.i_coup_discount)*o.quantity),0) as total_order_value     
						from pnh_m_franchise_info b 
						join pnh_m_territory_info t on t.id = b.territory_id
						join pnh_towns twn on twn.id = b.town_id 
						left join king_transactions a on a.franchise_id = b.franchise_id and is_pnh = 1 and a.init between ? and ? 
						left join king_orders o on o.transid = a.transid
						left join king_dealitems di on di.id = o.itemid 
						left join king_deals d on d.dealid = di.dealid 
						left join pnh_menu m on m.id = d.menuid
						where 1  
						'.$grp_cond.'
						order by territory_name,town_name,menu_name   
					';
		
		
		$resp = $this->db->query($sql,array($st_d_ts,$en_d_ts));
		
		
		
		if($type == 1)
			$csv_head_fields=array("Territory","Menu","Products","Amount");
		else if($type == 2)
		 		$csv_head_fields=array("Territory","Town","Menu","Products","Amount");
		 		else if($type == 3)
		 			$csv_head_fields=array("Territory","Town","Franchise","Menu","Products","Amount");
		 
		ob_start();
		$fp=fopen("php://output","w");
		fputcsv($fp, $csv_head_fields);
		$csv_data = array();
		$output = array();
		$sales_summ = array();
		$total_order_value = 0;
		
		if($resp->num_rows())
		{
			
				// territory wize summary 
				if($type == 1)
				{
					foreach($resp->result_array() as $row)
					{
						if(!isset($sales_summ[$row['territory_id']]))
							$sales_summ[$row['territory_id']] = array('id'=>$row['territory_id'],'name'=>$row['territory_name'],'towns'=>array(),'menu'=>$sales_bymenu);
								
						if($row['menu_id'])
						{	
							$sales_summ[$row['territory_id']]['menu'][$row['menu_id']]['sales'] = $row['total_orders'];
							$sales_summ[$row['territory_id']]['menu'][$row['menu_id']]['amt'] = round($row['total_order_value']);
						}	
					}
					foreach($sales_summ as $tr_id=>$tr_summ)
					{
						$tr_indx=0;
						 	
						foreach($tr_summ['menu'] as $m_id=>$menu_summ)
						{
							$op = array();
							if($tr_indx == 0)
								$op[] = $tr_summ['name'];
							else
								$op[] = "";
								
							$tr_indx = 1;	
							$twn_indx = 1;
		
							$op[] = $menu_summ['name'];
							$op[] = $menu_summ['sales'];
							$op[] = $menu_summ['amt'];
							
							fputcsv($fp, $op);
							
						}
						 
					}
					
					
					
				}else if($type == 2)
				{
					foreach($resp->result_array() as $row)
					{
						if(!isset($sales_summ[$row['territory_id']]))
							$sales_summ[$row['territory_id']] = array('id'=>$row['territory_id'],'name'=>$row['territory_name'],'towns'=>array(),'menu'=>$sales_bymenu);
		
							if(!isset($sales_summ[$row['territory_id']]['towns'][$row['town_id']]))
								$sales_summ[$row['territory_id']]['towns'][$row['town_id']] = array('id'=>$row['town_id'],'name'=>$row['town_name'],'menu'=>$sales_bymenu);
								
								
								
						if($row['menu_id'])
						{	
							$sales_summ[$row['territory_id']]['towns'][$row['town_id']]['menu'][$row['menu_id']]['sales'] = $row['total_orders'];
							$sales_summ[$row['territory_id']]['towns'][$row['town_id']]['menu'][$row['menu_id']]['amt'] = round($row['total_order_value']);
						}	
					}
					
					
					
					
					foreach($sales_summ as $tr_id=>$tr_summ)
					{
						$tr_indx=0;
						foreach($tr_summ['towns'] as $twn_id=>$twn_summ)
						{
							$twn_indx=0;	
							foreach($twn_summ['menu'] as $m_id=>$menu_summ)
							{
								$op = array();
								if($tr_indx == 0)
									$op[] = $tr_summ['name'];
								else
									$op[] = "";
									
								if($twn_indx == 0)
									$op[] = $twn_summ['name'];
								else
									$op[] = "";	
									
									
								$tr_indx = 1;	
								$twn_indx = 1;
			
								$op[] = $menu_summ['name'];
								$op[] = $menu_summ['sales'];
								$op[] = $menu_summ['amt'];
								
								fputcsv($fp, $op);
								
							}
						}
					}
					
					
					
				}else if($type == 3)
				{
					foreach($resp->result_array() as $row)
					{
						if(!isset($sales_summ[$row['territory_id']]))
							$sales_summ[$row['territory_id']] = array('id'=>$row['territory_id'],'name'=>$row['territory_name'],'towns'=>array(),'menu'=>$sales_bymenu);
		
							if(!isset($sales_summ[$row['territory_id']]['towns'][$row['town_id']]))
								$sales_summ[$row['territory_id']]['towns'][$row['town_id']] = array('id'=>$row['town_id'],'name'=>$row['town_name'],'franchises'=>array(),'menu'=>$sales_bymenu);
								
						if(!isset($sales_summ[$row['territory_id']]['towns'][$row['town_id']]['franchises'][$row['franchise_id']]))
									$sales_summ[$row['territory_id']]['towns'][$row['town_id']]['franchises'][$row['franchise_id']] = array('id'=>$row['franchise_id'],'name'=>$row['franchise_name'],'menu'=>$sales_bymenu);		
								
						if($row['menu_id'])
						{	
							$sales_summ[$row['territory_id']]['towns'][$row['town_id']]['franchises'][$row['franchise_id']]['menu'][$row['menu_id']]['sales'] = $row['total_orders'];
							$sales_summ[$row['territory_id']]['towns'][$row['town_id']]['franchises'][$row['franchise_id']]['menu'][$row['menu_id']]['amt'] = round($row['total_order_value']);
						}	
					}
					
					
					foreach($sales_summ as $tr_id=>$tr_summ)
					{
						$tr_indx=0;
						foreach($tr_summ['towns'] as $twn_id=>$twn_summ)
						{
							$twn_indx=0;	
							foreach($twn_summ['franchises'] as $m_id=>$fr_summ)
							{
								$fr_indx=0;
								foreach($fr_summ['menu'] as $m_id=>$menu_summ)
								{
									$op = array();
									if($tr_indx == 0)
										$op[] = $tr_summ['name'];
									else
										$op[] = "";
										
									if($twn_indx == 0)
										$op[] = $twn_summ['name'];
									else
										$op[] = "";	
										
									if($fr_indx == 0)
										$op[] = $fr_summ['name'];
									else
										$op[] = "";		
										
										
									$tr_indx = 1;	
									$twn_indx = 1;
									$fr_indx = 1;
				
									$op[] = $menu_summ['name'];
									$op[] = $menu_summ['sales'];
									$op[] = $menu_summ['amt'];
									
									fputcsv($fp, $op);
									
								}
								
							}
						}
					}
					
					
					
				}
				 	
			 
		}
		
		 
		if($type == 1)
			$sales_by = 'Territory';
		else if($type == 2)	
			$sales_by = 'Town';
		else if($type == 3)	
			$sales_by = 'Franchise';
			
		fclose($fp);
		$csv=ob_get_clean();
		@ob_clean();
	    header('Content-Description: File Transfer');
	    header('Content-Type: text/csv');
	    header('Content-Disposition: attachment; filename='.$sales_by.'_SalesSummary_'.str_replace('-','_',$st_d.'_'.$en_d).'.csv');
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');
	    header('Content-Length: ' . strlen($csv));
	    @ob_clean();
	    flush();
	    echo $csv;
	    exit;
		
		//print_r($sales_summ);
		
	}
	
	/**
	 * function to dynamically update product sourceable status from ajax  
	 */
	function jx_upd_prodsrcstatus()
	{
		$user = $this->auth();
		$pid = $this->input->post('pid');
		$stat = !$this->input->post('stat');
		
		$output = array();
		$this->db->query("update m_product_info set is_sourceable = !is_sourceable where product_id=? limit 1",array($pid));
		$output['src'] = $this->db->query('select is_sourceable from m_product_info where product_id = ? ',$pid)->row()->is_sourceable;
		$output['status'] = $this->db->affected_rows();
		$t_inp=array("product_id"=>$pid,"is_sourceable"=>$stat,"created_on"=>time(),"created_by"=>$user['userid']);
		$this->db->insert("products_src_changelog",$t_inp);
		echo json_encode($output);
	}
	

	/**
	 * function to dynamically update product sourceable status from ajax  
	 */
	function jx_upd_bulkprodsrcstatus()
	{
		$user = $this->auth();
		$pid_list = $this->input->post('prod');
		$output = array();
		foreach($pid_list as $pid=>$stat)
		{
			$this->db->query("update m_product_info set is_sourceable=? where product_id=? and is_sourceable != ?  limit 1",array($stat,$pid,$stat));
			$output['status'] = $this->db->affected_rows();
			if($output['status'])
			{
				$t_inp=array("product_id"=>$pid,"is_sourceable"=>$stat,"created_on"=>time(),"created_by"=>$user['userid']);
				$this->db->insert("products_src_changelog",$t_inp);	
			}
				
		}
		
		$output['status'] = 'success';
		
		echo json_encode($output);
	}
	
	
	
     /**
      * function to get stock prod details by stock id 
      * @param $stk_id
      */
     function jx_get_stkprobyid($stk_id=''){
     	$this->erpm->auth(true);
     	$stk_prodet_res = $this->db->query("select * from t_stock_info where stock_id = ? ",$stk_id);
     	$output = array();
     	if($stk_prodet_res)
     	{
     		$output['stkdet'] = $stk_prodet_res->row_array();
     		$output['status'] = 'success';
     	}else
     	{
     		$output['error'] = 'Stock product entry not found';
     		$output['status'] = 'error';
     	}
     	
     	echo json_encode($output);
     }
     
     /**
      * function to update product stock barcode 
      */
     function jx_upd_stkprodbc()
     {
     	if(!$_POST)
     		die();
     		
     	$this->erpm->auth();
     		
     	$stk_id = $this->input->post('stk_id');
     	$newbc = $this->input->post('newbc');
     	$output = array();
		$newbc = trim(str_replace(' ','',$newbc));
		$this->db->query("update t_stock_info set product_barcode = ? where stock_id = ? ",array($newbc,$stk_id));
		if($this->db->affected_rows())
		{
			$output['status'] = 'success';
		}else
		{
			$output['error'] = 'Update failed,invalid stock reference or product barcode not found';
			$output['status'] = 'error';	
		}
     	echo json_encode($output);
     }

     
	 // function to download pnh sales report 
	 function export_pnh_sales_report()
	 {
	 	
	 	$this->erpm->auth(PNH_EXECUTIVE_ROLE|FINANCE_ROLE);
	 	
	 	//ssql to generate franchise list 
	 	$sql_f = "select pnh_franchise_id,franchise_id,login_mobile1,login_mobile2,franchise_name,b.territory_name,c.town_name  
	 						from pnh_m_franchise_info a 
	 						join pnh_m_territory_info b on a.territory_id = b.id
	 						join pnh_towns c on c.id = a.town_id  
	 						order by territory_name,town_name,franchise_name";
	 	$res_f = $this->db->query($sql_f);
	 	
	 	$fr_sales_list = array();
	 	$fr_sales_list[] = '';
	 	$fr_sales_list[] = '"Paynearhome Franchise Sales Summary Till - '.date('d/m/Y h:i a').'"';
	 	$fr_sales_list[] = '';
	 	$fr_sales_list[] = '';
	 	$fr_sales_list[] = '"Slno","Territory","Town","Status","FranciseID","FranchiseName","Contact no","Ordered","Shipped","Invoiced Not Shipped","Cancelled","Paid Till Date","UnCleared","Corrections","Credit Notes Raised","Pending Payment"," Before 3 Days","Before 5 Days","Before 7 Day","Before 10 Day"';
	 	
	 	
	 	if($res_f->num_rows())
	 	{
	 		$i=0;
	 		// loop through all franchises 
	 		foreach($res_f->result_array() as $row_f)
	 		{
	 			
	 			$fr_sales_det = array();
	 			$fr_sales_det[] = ++$i;
				$fr_sales_det[] = $row_f['territory_name'];
				$fr_sales_det[] = $row_f['town_name'];
	 			$fr_sales_det[] = $row_f['is_suspended']?'Suspended':'Active';
				$fr_sales_det[] = $row_f['pnh_franchise_id'];
	 			$fr_sales_det[] = ucwords($row_f['franchise_name']);
	 			
				$cnos = array($row_f['login_mobile1'],$row_f['login_mobile2']);
	 			$fr_sales_det[] = implode('/',array_filter($cnos));
				
	 			// get franchise total sales 
	 			$ordered_tilldate = @$this->db->query("select round(sum((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) as amt  
									from king_transactions a 
									join king_orders b on a.transid = b.transid 
								        join pnh_m_franchise_info c on c.franchise_id = a.franchise_id 
									where a.franchise_id = ? ",$row_f['franchise_id'])->row()->amt*1;
									
				$cancelled_tilldate = @$this->db->query("select round(sum((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) as amt  
									from king_transactions a 
									join king_orders b on a.transid = b.transid 
								        join pnh_m_franchise_info c on c.franchise_id = a.franchise_id 
									where a.franchise_id = ? and b.status = 3 ",$row_f['franchise_id'])->row()->amt*1;					
				
				$shipped_tilldate = @$this->db->query("SELECT round(sum((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) as amt 
																FROM king_transactions a
																JOIN king_orders b ON a.transid = b.transid
																JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id
																JOIN king_invoice d ON d.order_id = b.id
																JOIN shipment_batch_process_invoice_link e ON e.invoice_no = d.invoice_no
																AND e.shipped =1 AND e.packed =1
																WHERE a.franchise_id = ? AND d.invoice_status = 1 and b.status != 0 
										 ",$row_f['franchise_id'])->row()->amt*1;
										 
										 
				$sql = "select sum(debit_amt-credit_amt) as amt  
							from pnh_franchise_account_summary where action_type = 1 and franchise_id = ? ";
				$active_invoiced = $this->db->query($sql,array($row_f['franchise_id']))->row()->amt*1;						 
				
				// invoiced not shipped 
				$not_shipped_amount = $this->db->query(" select sum(t) as amt from (
											select a.invoice_no,debit_amt as t 
												from pnh_franchise_account_summary a 
												join king_invoice c on c.invoice_no = a.invoice_no and invoice_status =  1 
												where action_type = 1 
												and franchise_id = ?  
											group by a.invoice_no ) as a 
											join shipment_batch_process_invoice_link b on a.invoice_no = b.invoice_no and shipped = 0  ",$row_f['franchise_id'])->row()->amt*1;
				
				$shipped_tilldate = $active_invoiced-$not_shipped_amount;			 
										 
				//$cancelled_tilldate = $ordered_tilldate-$shipped_tilldate-$not_shipped_amount;
				
				$paid_tilldate = @$this->db->query("select sum(receipt_amount) as amt from pnh_t_receipt_info where receipt_type = 1 and status = 1 and franchise_id = ? ",$row_f['franchise_id'])->row()->amt*1;
				
				$sql = "select sum(credit_amt-debit_amt) as amt  
							from pnh_franchise_account_summary where action_type = 5 and franchise_id = ? ";
				$acc_adjustments_val = $this->db->query($sql,array($row_f['franchise_id']))->row()->amt*1;
				
				
				$sql = "select sum(credit_amt-debit_amt) as amt  
							from pnh_franchise_account_summary where action_type = 7 and franchise_id = ? ";
				$ttl_credit_note_val = $this->db->query($sql,array($row_f['franchise_id']))->row()->amt;
				
				
				 $sql = "select (sum(credit_amt)-sum(debit_amt)) as amt from ((
select statement_id,type,count(a.invoice_no) as invoice_no,sum(credit_amt) as credit_amt,sum(debit_amt) as debit_amt,date(a.created_on) as action_date,concat('Total ',count(a.invoice_no),' IMEI Activations') as remarks 
		from pnh_franchise_account_summary a 
		join t_invoice_credit_notes b on a.credit_note_id = b.id 
		where action_type = 7 and type = 2 
		and a.franchise_id = ?   
		 
	group by action_date 	
)
union
(
select statement_id,1 as type,(a.invoice_no) as invoice_no,sum(credit_amt) as credit_amt,sum(debit_amt) as debit_amt,date(a.created_on) as action_date,remarks
		from pnh_franchise_account_summary a 
		join t_invoice_credit_notes b on a.invoice_no = b.invoice_no 
		where action_type = 7 and type = 1   
		and a.franchise_id = ?   
		 
	 group by statement_id 
)
) as g 
order by action_date";  
				$ttl_credit_note_val = $this->db->query($sql,array($row_f['franchise_id'],$row_f['franchise_id']))->row()->amt;
				
				
				
				$uncleared_payment = @$this->db->query("select sum(receipt_amount) as amt from pnh_t_receipt_info where status = 0 and franchise_id = ? and receipt_type = 1 ",$row_f['franchise_id'])->row()->amt*1;
				
				$fr_sales_det[] = $ordered_tilldate;
				$fr_sales_det[] = $shipped_tilldate;
				$fr_sales_det[] = $not_shipped_amount;
				$fr_sales_det[] = $cancelled_tilldate;
				$fr_sales_det[] = $paid_tilldate;
				$fr_sales_det[] = $uncleared_payment;
				$fr_sales_det[] = $acc_adjustments_val;
				$fr_sales_det[] = $ttl_credit_note_val;
				$fr_sales_det[] = $shipped_tilldate-($paid_tilldate+$acc_adjustments_val+$ttl_credit_note_val);
				
				$past_sales_summ = array(3,5,7,10);
				
				foreach($past_sales_summ as $k)
				{
					$cond = ' and date(e.shipped_on) < date_add(curdate(),INTERVAL -'.$k.' day) ';
					$cond_pay = ' and date(from_unixtime(activated_on)) < date_add(curdate(),INTERVAL -'.$k.' day) ';
					
					$shipped_tilldate_byday = @$this->db->query("SELECT round(sum((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) as amt 
																	FROM king_transactions a
																	JOIN king_orders b ON a.transid = b.transid
																	JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id
																	JOIN king_invoice d ON d.order_id = b.id
																	JOIN shipment_batch_process_invoice_link e ON e.invoice_no = d.invoice_no
																	AND e.shipped =1 AND e.packed = 1  
																	WHERE a.franchise_id = ? AND d.invoice_status = 1 and b.status != 0 $cond  
											 ",$row_f['franchise_id'])->row()->amt*1;
											 
					$paid_tilldate_byday = @$this->db->query("select sum(receipt_amount) as amt from pnh_t_receipt_info where receipt_type = 1 and status = 1 and franchise_id = ? ",$row_f['franchise_id'])->row()->amt*1;
					
					$pen_pay_byday = ($shipped_tilldate_byday-($paid_tilldate_byday+$acc_adjustments_val+$ttl_credit_note_val));

					$fr_sales_det[] = ($pen_pay_byday>0)?$pen_pay_byday:0;
					
				}						 
				
				$fr_sales_list[] = '"'.implode('","',$fr_sales_det).'"';
	 		}
	 	} 
	 	
	 	header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename=PNH_SALES_REPORT_'.date('d_m_Y_H_i').'.csv');
		header('Pragma: no-cache');

	 	echo implode("\r\n",$fr_sales_list);
	 	
	 }
	 
	 function bulk_cancelorders()
	 {
	 	$this->erpm->auth(true);
	 	
	 	if($_POST)
	 		$this->_do_bulk_cancelorders();
	 		
	 	$data['page'] = 'bulk_cancelorders';	
	 	$this->load->view('admin',$data);
	 }
	 
	 function _do_bulk_cancelorders()
	 {
	 	$this->erpm->auth(true);
	 	$user=$this->erpm->getadminuser();
	 	$transids= $this->input->post('transids');
	 	$transid_list = explode(',',$transids);
	 	
	 	$output = array();
	 	$output[] = '"TransID","Order ID","Status"';
	 	foreach($transid_list as $transid)
	 	{
	 		if(!$transid)
	 			continue ;
	 			
	 		if(!$this->db->query("select count(*) as t from king_transactions where transid = ? ",$transid)->row()->t)
			{
				$output[] = '"'.$transid.'","","Invalid Transaction ID"';
				continue;
			}
					
	 		// check if all orders in transaction are pending in status for processing cancellation
	 		$is_trans_processed = $this->db->query("select count(*) as t from king_orders a where transid = ? and (status != 0 && status != 3) ;",$transid)->row()->t;
	 		if($is_trans_processed)
	 		{
	 			$output[] = '"'.$transid.'","","Already Processed"';
	 		}else
	 		{
				
				$refund = 0;
				$oid_list = @$this->db->query("select id,quantity from king_orders where status = 0 and transid = ? ",$transid)->result_array();
				if($oid_list)
				{
					$process_refund = $this->erpm->_valid_transforrefund($transid); 
					if($process_refund)
					{
						$this->db->query("insert into t_refund_info(transid,amount,status,created_on,created_by) values(?,?,?,?,?)",array($transid,$refund,1,time(),$user['userid']));
						$rid=$this->db->insert_id();
					}
					
					foreach($oid_list as $o)
					{
						if($process_refund)
							$this->db->query("insert into t_refund_order_item_link(refund_id,order_id,qty) values(?,?,?)",array($rid,$o['id'],$o['quantity']));
						
						$this->db->query("update king_orders set status=3,actiontime=".time()." where id=? limit 1",$o['id']);
						$output[] = '"'.$transid.'","'.$o['id'].'","Cancelled"';
					}
					
					$this->erpm->do_trans_changelog($transid,count($oid_list)." order(s) cancelled");
				}else
				{
					
					$output[] = '"'.$transid.'","","Already Cancelled"';
				}
	 		}
	 	}
	 	
	 	header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename=BULK_TRANS_CANCELLATION_'.date('d_m_Y_H_i').'.csv');
		header('Pragma: no-cache');

	 	echo implode("\r\n",$output);
	 	
	 	exit;
	 	
	 }
	

	function update_stock_intake()
	 {
	 	
	 	$user = $this->erpm->auth();
	 	$grn_prod_res = $this->db->query("select a.id,a.grn_id,a.product_id,a.mrp as new_mrp,b.mrp as old_mrp,a.received_qty,a.purchase_price,a.margin   
								from t_grn_product_link a 
								join t_po_product_link b on a.po_id = b.po_id and a.product_id = b.product_id 
								where a.mrp > b.mrp and is_processed_upd = 0 and a.grn_id >= 1968  
								order by grn_id asc ");
	 	foreach ($grn_prod_res->result_array() as $grn_row)
	 	{
	 		$this->db->query("update t_grn_product_link set is_processed_upd = 1 where id = ? ",$grn_row['id']);
	 		// check if product price changed 
	 		$is_updated = $this->db->query("select count(*) as t from product_price_changelog where product_id = ? and old_mrp = ? and new_mrp = ? and reference_grn = ? ",array($grn_row['product_id'],$grn_row['old_mrp'],$grn_row['new_mrp'],$grn_row['grn_id']))->row()->t;
	 		if($is_updated == 0)
	 		{
	 			if($grn_row['new_mrp'] < $grn_row['purchase_price'])
	 			{
	 				$grn_row['purchase_price'] = $grn_row['new_mrp']-(($grn_row['new_mrp']*$grn['margin']/100));
	 			}
	 			
	 		
				$pid = $grn_row['product_id']; 
				$mrp = $grn_row['new_mrp']; 
				if(!$mrp)
					continue ;
				
				$pc_prod=$this->db->query("select * from m_product_info where product_id=? and mrp!=?",array($pid,$mrp))->row_array();
				if(!empty($pc_prod))
				{
					
					$inp=array("product_id"=>$pid,"new_mrp"=>$mrp,"old_mrp"=>$pc_prod['mrp'],"reference_grn"=>$grn_row['grn_id'],"created_by"=>$user['userid'],"created_on"=>time());
					$this->db->insert("product_price_changelog",$inp);
					//$this->db->query("update m_product_info set mrp=? where product_id=? limit 1",array($mrp,$pid));
					
					$this->db->query("update m_product_info set mrp = ?,purchase_cost = ?,modified_on=now(),modified_by=? where product_id = ? ",array($grn_row['new_mrp'],$grn_row['purchase_price'],$user['userid'],$grn_row['product_id']));
					
					foreach($this->db->query("select product_id from products_group_pids where group_id in (select group_id from products_group_pids where product_id=$pid) and product_id!=$pid")->result_array() as $pg)
					{
						$inp=array("product_id"=>$pg['product_id'],"new_mrp"=>$mrp,"old_mrp"=>$this->db->query("select mrp from m_product_info where product_id=?",$pg['product_id'])->row()->mrp,"reference_grn"=>$grn['grn_id'],"created_by"=>$user['userid'],"created_on"=>time());
						$this->db->insert("product_price_changelog",$inp);
						$this->db->query("update m_product_info set mrp=? where product_id=? limit 1",array($mrp,$pg['product_id']));
					}
					$r_itemids=$this->db->query("select itemid from m_product_deal_link where product_id=?",$pid)->result_array();
					$r_itemids2=$this->db->query("select l.itemid from products_group_pids p join m_product_group_deal_link l on l.group_id=p.group_id where p.product_id=?",$pid)->result_array();
					
					
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
					
					
					
					foreach($r_itemids as $d)
					{
						$itemid=$d['itemid'];
						$item=$this->db->query("select orgprice,price from king_dealitems where id=?",$itemid)->row_array();
						$o_price=$item['price'];$o_mrp=$item['orgprice'];
						
						$n_mrp=$this->db->query("select ifnull(sum(p.mrp*l.qty),0) as mrp from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$itemid)->row()->mrp+$this->db->query("select ifnull(sum((select avg(mrp) from m_product_group_deal_link l join products_group_pids pg on pg.group_id=l.group_id join m_product_info p on p.product_id=pg.product_id where l.itemid=$itemid)*(select qty from m_product_group_deal_link where itemid=$itemid)),0) as mrp")->row()->mrp;
						//$n_price=$item['price']/$o_mrp*$n_mrp;
						
						$n_price=$pc_prod['is_serial_required']?$o_price:$o_price/$o_mrp*$n_mrp;
						
						$inp=array("itemid"=>$itemid,"old_mrp"=>$o_mrp,"new_mrp"=>$n_mrp,"old_price"=>$o_price,"new_price"=>$n_price,"created_by"=>$user['userid'],"created_on"=>time(),"reference_grn"=>$grn_row['grn_id']);
						$r=$this->db->insert("deal_price_changelog",$inp);
						$this->db->query("update king_dealitems set orgprice=?,price=? where id=? limit 1",array($n_mrp,$n_price,$itemid));
						
						// Disable special margin if set 
						$this->db->query("update pnh_special_margin_deals set is_active = 0 where i_price != ? and itemid = ? and is_active = 1 " ,array($n_price,$itemid));
						
						if($this->db->query("select is_pnh as b from king_dealitems where id=?",$itemid)->row()->b)
						{
							$o_s_price=$this->db->query("select store_price from king_dealitems where id=?",$itemid)->row()->store_price;
							//$n_s_price=$o_s_price/$o_mrp*$n_mrp;
							$n_s_price=$pc_prod['is_serial_required']?$o_s_price:$o_s_price/$o_mrp*$n_mrp;
							$this->db->query("update king_dealitems set store_price=? where id=? limit 1",array($n_s_price,$itemid));
							$o_n_price=$this->db->query("select nyp_price as p from king_dealitems where id=?",$itemid)->row()->p;
							//$n_n_price=$o_n_price/$o_mrp*$n_mrp;
							$n_n_price=$pc_prod['is_serial_required']?$o_n_price:$o_n_price/$o_mrp*$n_mrp;
							$this->db->query("update king_dealitems set nyp_price=? where id=? limit 1",array($n_n_price,$itemid));
						}
						foreach($this->db->query("select * from partner_deal_prices where itemid=?",$itemid)->result_array() as $r)
						{
							$o_c_price=$r['customer_price'];
							//$n_c_price=$o_c_price/$o_mrp*$n_mrp;
							$n_c_price=$pc_prod['is_serial_required']?$o_c_price:$o_c_price/$o_mrp*$n_mrp;
							$o_p_price=$r['partner_price'];
							//$n_p_price=$o_p_price/$o_mrp*$n_mrp;
							$n_p_price=$pc_prod['is_serial_required']?$o_p_price:$o_p_price/$o_mrp*$n_mrp;
							$this->db->query("update partner_deal_prices set customer_price=?,partner_price=? where itemid=? and partner_id=?",array($n_c_price,$n_p_price,$itemid,$r['partner_id']));
						}
					}
				}
				
	 			
	 			
	 			
	 		}
	 	}
	 	
		
	 }


	 /**
	 * function to generate stock unavailable report with order ageing for 10 days 
	 *
	 */ 
	function unavail_product_ageing_report()
	{
		$this->erpm->auth(UNAVAILABLE_PRODUCT_AGEING_REPORT);
		
	 	$unavail_prod_sql = "select brand_id,brand_name,product_id,product_name,order_date,sum(ttl_req_qty) as ttl_req_qty,mrp,purchase_cost,trans 
			from 
			((select c.brand_id as brand_id,d.name as brand_name,
					c.product_id,trim(c.product_name) as product_name,
					sum(a.quantity*b.qty) as ttl_req_qty,
					date(from_unixtime(a.time)) as order_date,
					c.mrp,c.purchase_cost,
					group_concat(distinct a.transid) as trans  
				from king_orders a 
				join products_group_orders o on o.order_id = a.id
				join m_product_group_deal_link b on b.itemid = a.itemid
				join m_product_info c on c.product_id = o.product_id 
				join king_brands d on c.brand_id = d.id 
				where a.status = 0 
				group by product_id,order_date 
				)
			union(select c.brand_id as brand_id,d.name as brand_name,
					c.product_id,trim(c.product_name) as product_name,
					sum(a.quantity*b.qty) as ttl_req_qty,date(from_unixtime(a.time)) as order_date,
					c.mrp,c.purchase_cost,
					group_concat(distinct a.transid) as trans 
				from king_orders a 
				join m_product_deal_link b on a.itemid = b.itemid  
				join m_product_info c on c.product_id = b.product_id 
				join king_brands d on c.brand_id = d.id 
				where a.status = 0 
				group by product_id,order_date  
				)) as g 
			
			group by g.product_id,order_date 
			having ttl_req_qty > 0 
			order by g.product_name,g.product_id,order_date desc 
			
			";
	 	
	 	$unavail_prod_res = $this->db->query($unavail_prod_sql);
	 	
	 	
	 	if($unavail_prod_res->num_rows())
	 	{
	 		$prod_list = array(); 
	 		$min_date_ts = strtotime(date('Y-m-d',time()-(10*24*60*60)));
	 		foreach ($unavail_prod_res->result_array() as $unavail_prod_det)
	 		{
	 			if(!isset($prod_list[$unavail_prod_det['product_id']]))
	 			{
	 				
	 				$prod_ven_list_res = $this->db->query("select a.vendor_id,b.vendor_name,brand_margin from m_vendor_brand_link a join m_vendor_info b on a.vendor_id = b.vendor_id  where a.brand_id = ?  order by brand_margin desc ",$unavail_prod_det['brand_id']);
					if($prod_ven_list_res->num_rows())
						$unavail_prod_det['vendor_list'] = $prod_ven_list_res->result_array();
					else 
						$unavail_prod_det['vendor_list'] = array();	
						
						
	 				$prod_stk_res = @$this->db->query("select concat(rack_name,bin_name) as loc,mrp,available_qty as stk 
							from t_stock_info a 
							join m_rack_bin_info b on a.location_id = b.location_id and a.rack_bin_id = b.id 
							where product_id = ? ",$unavail_prod_det['product_id'])->result_array();
	 				
					$unavail_prod_det['available_stock'] = $prod_stk_res?$prod_stk_res:array();
					
					$pen_poqty_res = $this->db->query("select a.product_id,sum(order_qty-received_qty) as ttl_pen,group_concat(date(b.created_on) order by b.created_on desc) as last_podate  
								from t_po_product_link a 
								join t_po_info b on a.po_id = b.po_id 
								where po_status = 1 and product_id = ?  
							group by a.product_id",$unavail_prod_det['product_id']); 
					$pen_poqty = '';
					$pen_po_lastdate = '';
					if($pen_poqty_res->num_rows())
					{
						$pen_poqty_det = $pen_poqty_res->row_array();
						$pen_poqty = $pen_poqty_det['ttl_pen'];
						$pen_po_lastdate = array_pop(explode(',',$pen_poqty_det['last_podate']));
					}
					
					$unavail_prod_det['pen_po_qty'] = $pen_poqty;
					$unavail_prod_det['pen_po_lastdate'] = $pen_po_lastdate;
					
					$prod_list[$unavail_prod_det['product_id']] = $unavail_prod_det;
	 				unset($prod_list[$unavail_prod_det['product_id']]['order_date']);
	 				$prod_list[$unavail_prod_det['product_id']]['total_req_qty'] = 0;
	 				$prod_list[$unavail_prod_det['product_id']]['order_dates'] = array();
	 				for($i=9;$i>=0;$i--)
	 				{
	 					$dt = strtotime(date('Y-m-d',time()-($i*24*60*60)));
	 					$prod_list[$unavail_prod_det['product_id']]['order_dates'][$dt] = 0;	
	 				}
	 				$prod_list[$unavail_prod_det['product_id']]['order_dates']['more_than_10days'] = 0;
	 				
	 			}
	 			$prod_ageing_dt = 'more_than_10days';
	 			$prod_det_order_dt = strtotime($unavail_prod_det['order_date']); 
	 			if($prod_det_order_dt > $min_date_ts)
	 			{
	 				$prod_ageing_dt = $prod_det_order_dt;
	 			}
	 			$prod_list[$unavail_prod_det['product_id']]['order_dates'][$prod_ageing_dt] = $unavail_prod_det['ttl_req_qty'];
	 			
	 			$prod_list[$unavail_prod_det['product_id']]['total_req_qty'] += $unavail_prod_det['ttl_req_qty'];
	 			
	 			// check for available stock
	 		}
	 	}
	 	
	 	/*
	 	echo '<pre>';
	 	print_r($prod_list);
		*/
	 	$output = array();
	 	$output_head = array();
	 	$output_head[] = "ProductID";
	 	$output_head[] = "BrandName";
	 	$output_head[] = "Product Name";
	 	$output_head[] = "MRP";
	 	$output_head[] = "Purchase Cost";
	 	$output_head[] = "Stock Location";
		$output_head[] = "Available Stock";
	 	$output_head[] = "Order Qty";
		$output_head[] = "Required Qty";
		$output_head[] = "Orders More Than 10 Days";
	 	for($i=9;$i>=0;$i--)
		{
	 		$output_head[]= date('d/M/Y',time()-($i*24*60*60));	
	 	}
	 	
	 	$output_head[] = "Pending PO Qty";
	 	$output_head[] = "LastPO RaisedOn";
	 	$output_head[] = "Pending Qty From Vendors";
	 	$output_head[] = "Available Vendors";
	 	$output_head[] = "Transactions";
	 	
	 	$output[] = '"'.implode('","',$output_head).'"';
	 	
	 	foreach ($prod_list as $prod)
	 	{
	 		$prod['stock'] = array();
	 		$pstk_ttl = 0; 
	 		if(count($prod['available_stock']))
	 		{
	 			foreach($prod['available_stock'] as $pstk)
	 			{
	 				array_push($prod['stock'],$pstk['loc'].'('.$pstk['mrp'].') - '.$pstk['stk']);
	 				$pstk_ttl +=  $pstk['stk'];
	 			}
	 		} 
	 		$stk_req_qty =  $prod['total_req_qty']-$pstk_ttl;
	 		
	 		
	 		$prod['vendors'] = array();
	 		if(count($prod['vendor_list']))
	 		{
	 			foreach($prod['vendor_list'] as $pven)
	 			{
	 				array_push($prod['vendors'],$pven['vendor_name'].'('.$pven['brand_margin'].')');
	 			}
	 		} 
	 		
			$tmp = array();
			$tmp[] = $prod['product_id'];
			$tmp[] = $prod['brand_name'];
			$tmp[] = $prod['product_name'];
			$tmp[] = $prod['mrp'];
			$tmp[] = $prod['purchase_cost'];
			$tmp[] = implode(",",$prod['stock']);
			$tmp[] = $pstk_ttl;
			$tmp[] = $prod['total_req_qty'];
			$tmp[] = ($stk_req_qty>0)?$stk_req_qty:0;
			$tmp[] = $prod['order_dates']['more_than_10days'];
		 	for($i=9;$i>=0;$i--)
			{
		 		$tmp[] = $prod['order_dates'][strtotime(date('Y-m-d',time()-($i*24*60*60)))];	
		 	}
			
			$tmp[] = $prod['pen_po_qty'];
			$tmp[] = $prod['pen_po_lastdate'];
			
			$po_prod_vendor_list_res = $this->db->query("select a.po_id,a.product_id,vendor_name,sum(order_qty-received_qty) as ttl_pen,a.created_on as po_raised_on
									from t_po_product_link a 
									join t_po_info b on a.po_id = b.po_id
									join m_vendor_info c  on c.vendor_id = b.vendor_id 
									where po_status = 1 and product_id = ?    
									group by a.po_id,b.vendor_id   	
									having ttl_pen > 0 
									order by po_raised_on ",$prod['product_id']);
			$prod['pen_po_vendors'] = array();
			if($po_prod_vendor_list_res->num_rows())
			{
				foreach ($po_prod_vendor_list_res->result_array() as $pp_ven_det)
				{
					$prod['pen_po_vendors'][] = $pp_ven_det['vendor_name'].' POID:'.$pp_ven_det['po_id'].' Qty:'.$pp_ven_det['ttl_pen'].' Date: '.format_datetime($pp_ven_det['po_raised_on']);
				}
			}
			
			$tmp[] = implode(", ",$prod['pen_po_vendors']);
			$tmp[] = implode(", ",$prod['vendors']);
			$tmp[] = $prod['trans'];
			
			$output[] = '"'.implode('","',$tmp).'"';
	 	}
	 	
	 	header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename=unavailable_agening_report_'.date('Y_m_d_h_i').'.csv');
		header('Pragma: no-cache');

	 	echo implode("\r\n",$output);
	 	
	 }

	/**
	  * function to populate pending orders distinct brands
	  *
	  */
	 function jx_pendingorders_brands()
	 {
	 	$this->erpm->auth();
	 	$partner_id = $this->input->post('partner_id');
	 	$output = array();
	 	$brand_list_res = $this->db->query("	
	 								select  d.id,d.name,count(distinct a.id) as total_orders 
									from king_transactions t
									join king_orders a on t.transid = a.transid and a.status = 0 
									join king_dealitems b on a.itemid = b.id 
									join king_deals c on c.dealid = b.dealid 
									join king_brands d on d.id = c.brandid 
									where a.status = 0 and partner_id = ? and t.batch_enabled=1 
									group by d.id 
									order by d.name
								",$partner_id);
	 	if($brand_list_res->num_rows())
	 	{
	 		$output['brandlist'] = $brand_list_res->result_array();
	 		$output['status'] = 'success';
	 	}else
	 	{
	 		$output['status'] = 'error';
	 	}
	 	
	 	echo json_encode($output);
	 }
	 
	 function jx_get_processable_partnerorders_bybrandid()
	 {
	 	$brandid = $this->input->post('brandid');
	 	$partner_id = $this->input->post('partner_id');
	 	$process_partial = $this->input->post('process_partial');
	 	$output = array();
	 	
	 	$cond = ' and t.partner_id = '.$partner_id.' and d.brandid = '.$brandid.' ';
	 	
	 	$raw_trans=$this->db->query("select o.*,t.partner_reference_no from king_transactions t join king_orders o on o.transid=t.transid and o.status=0 join king_dealitems di on di.id = o.itemid join king_deals d on d.dealid = di.dealid  where t.batch_enabled=1 and t.is_pnh=0 $cond order by t.priority desc, t.init asc")->result_array();
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
		if(!empty($trans))
		{
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
				
			}
			$orders=array_unique($to_process_orders);
			$output['total_orders'] = count($orders);
		}
		else 
		{
			$output['total_orders'] = 0;	
		}
	 	
	 	
	 	
	 	echo json_encode($output);
	 	
	 }


	 function update_partnerorder_manifesto()
	 {
	 	$data['page'] = 'update_partnerorder_manifesto';
	 	
	 	$this->load->view('admin',$data);
	 }
	 
	 function process_upd_partordstatus_manifesto()
	 {
	 	$p_oids = $this->input->post('partner_ordernos');
	 	$p_oids_arr = explode(',',$p_oids);
	 	
	 	$total_available = 0;
	 	$total_updated = 0;
	 	foreach ($p_oids_arr as $p_oid)
	 	{
	 		if(!$p_oid)
	 			continue;
	 			
	 		$total_available++;	
	 		 
	 		$this->db->query("update partner_transaction_details set is_manifesto_created = 1 where is_manifesto_created=0 and order_no=? ",$p_oid);
	 		if($this->db->affected_rows() == 1)
	 		 	$total_updated++;
	 		 
	 	}
	 	if($total_available != $total_updated)
	 		echo '<script>alert("Only '.$total_updated.'/'.$total_available.' Orders marked for manifesto.")</script>';
	 	else 
	 		echo '<script>alert("Total '.$total_available.' Orders marked for Manifesto. ")</script>';
	 	
	 }
	 
	function download_outscanreport()
	{
		$user = $this->erpm->auth();
		
		
		$serial_no = $this->input->post('serial_no');
		
		$allday = $this->input->post('allday');
		
		$d_partner_id = $this->input->post('d_partner_id');
			
	 	$outscan_stdate = $this->input->post('outscan_stdate');
	 	$outscan_endate = $this->input->post('outscan_endate');
	 	$courier_names = $this->input->post('d_courier_name');
	 	if($courier_names)
	 		if(count($courier_names))
				$courier_names = "'".implode("','",$courier_names)."'";
		
		$cond = '';
		// sit deals 
		if($d_partner_id == 0)
		{
			$cond = ' and a.partner_id = 0 and c.courier_id in ('.$courier_names.') ';
		}
		else
		{
			// process for partner  
			$cond = ' and f.partner_id = "'.$d_partner_id.'" ';
		}
		
	
		
		if($serial_no)
	 	{
	 		$sql = "select  a.partner_id,b.invoice_no,a.partner_reference_no as orderno,d.ship_person,ifnull(ci.courier_name,f.courier_name) as courier_name,'' as courier_remarks,d.ship_city,di.name,d.quantity,net_amt,
	 							if(f.partner_id,f.awb_no,c.awb) as awb_no,c.shipped_on 
						from king_transactions a 
						join king_orders d on a.transid = d.transid 
						join king_invoice b on d.id = b.order_id 
						join shipment_batch_process_invoice_link c on c.invoice_no = b.invoice_no 
						join king_dealitems di on di.id = d.itemid
						left join partner_transaction_details f on f.transid = a.transid and a.partner_reference_no = f.order_no
						left join t_partner_manifesto_log g on g.invoice_no =  b.invoice_no 
						left join m_courier_info ci on ci.courier_id = c.courier_id 
						where serial_no = ? 
						order by shipped_on asc 
					";
		 	$res = $this->db->query($sql,array($serial_no));	
		 	$allday = 1;
	 	}
	 	else 
	 	{
	 		if($courier_names)
	 		{
			 	$sql = "select  a.partner_id,b.invoice_no,a.partner_reference_no as orderno,d.ship_person,ifnull(ci.courier_name,f.courier_name) as courier_name,'' as courier_remarks,d.ship_city,di.name,d.quantity,net_amt,
	 							if(f.partner_id,f.awb_no,c.awb) as awb_no,c.shipped_on  
							from king_transactions a 
							join king_orders d on a.transid = d.transid 
							join king_invoice b on d.id = b.order_id 
							join shipment_batch_process_invoice_link c on c.invoice_no = b.invoice_no 
							join king_dealitems di on di.id = d.itemid
							left join partner_transaction_details f on f.transid = a.transid and a.partner_reference_no = f.order_no and f.courier_name in ($courier_names)
							left join m_courier_info ci on ci.courier_id = c.courier_id  
							where date(shipped_on) >= ? and date(shipped_on) <= ? and  a.is_pnh = 0 $cond  
							order by shipped_on asc 
						";
			 	$res = $this->db->query($sql,array($outscan_stdate,$outscan_endate));
			 	
			 	//echo $this->db->last_query();
	 		}
	 		else
		 	{
		 		echo '<script>alert("Please select atleast one courier")</script>';
		 		exit;
		 	}
	 	}  
	 	
	 	 
	 	if($res->num_rows())
	 	{
	 		
	 		$outscan_list = $res->result_array();
	 		
	 		$last_serial_no = 0; 
	 		
	 		$action_dtime = date('Y-m-d H:i:s'); 
	 		$filtered_outscan_list = array();		 		
	 		foreach($outscan_list as $k=>$row)
	 		{
	 			$newentry = 0;
	 			if($d_partner_id)
	 			{
	 				$serial_no = @$this->db->query("select serial_no from t_partner_manifesto_log where partner_id=$d_partner_id and partner_order_no = ? ",$row['orderno'])->row()->serial_no;	
	 			}
	 			else
	 			{
	 				$serial_no = @$this->db->query("select serial_no from t_partner_manifesto_log where partner_id=0 and invoice_no = ? ",$row['invoice_no'])->row()->serial_no;	
	 			}
	 			
	 			if(!$serial_no)
	 			{
	 				if(!$last_serial_no)
	 					$last_serial_no = $this->db->query('select max(serial_no) as serial_no from t_partner_manifesto_log ')->row()->serial_no;
	 					
	 				$serial_no = $last_serial_no+1;
	 				$this->db->query("insert into t_partner_manifesto_log (partner_id,serial_no,invoice_no,partner_order_no,created_on,created_by) values (?,?,?,?,now(),?)",array($d_partner_id,$serial_no,$row['invoice_no'],$row['orderno'],$user['userid']));
	 				$newentry = 1;
	 			}
	 			
	 			$row['serial_no'] = $serial_no;
	 			
	 			if($allday || $newentry)
	 				$filtered_outscan_list[] = $row;
	 			
	 		}
	 		
			if(!$filtered_outscan_list)
			{
				echo '<script>alert("No Outscans found")</script>';
			}else
			{
			
		 		$output=array();
				if($filtered_outscan_list[0]['partner_id'])
				{
					$output_head = array('no','ExportRefno','Orderno','Shipping Name','Shipping City','ProductName','Quantity','Amount','Courier Name','AWBno','ShippedOn');	
				}else
				{
					$output_head = array('no','ExportRefno','Invoiceno','Shipping Name','Shipping City','Courier Name','AWBno','ShippedOn');
				}
		 		
		 		$output[] = '"'.implode('","',$output_head).'"';
		 		
		 		$s = 0;
		 		foreach($filtered_outscan_list as $row)
		 		{
		 			$tmp = array();
		 			$tmp[] = ++$s;
					if($row['partner_id'])
					{
						$tmp[] = $row['serial_no'];
			 			$tmp[] = $row['orderno'];
			 			$tmp[] = $row['ship_person'];
			 			$tmp[] = $row['ship_city'];
			 			$tmp[] = $row['name'];
			 			$tmp[] = $row['quantity'];
			 			$tmp[] = $row['net_amt'];
			 			$tmp[] = $row['courier_name'];
			 			$tmp[] = $row['awb_no'];
			 			$tmp[] = format_datetime($row['shipped_on']);
					}
					else
					{
						$tmp[] = $row['serial_no'];
			 			$tmp[] = $row['invoice_no'];
			 			$tmp[] = $row['ship_person'];
			 			$tmp[] = $row['ship_city'];
			 			$tmp[] = $row['courier_name'];
			 			$tmp[] = $row['awb_no'];
			 			$tmp[] = format_datetime($row['shipped_on']);
					}
					
		 			
		 			$output[] = '"'.implode('","',$tmp).'"';
		 		}
		 		$outscan_date_str = date('d_m_Y',strtotime($outscan_stdate)).'_'.date('d_m_Y',strtotime($outscan_endate));
		 		
		 		
		 		$csv_data = implode("\r\n",$output);
		 		header('Content-Description: File Transfer');
			    header('Content-Type: text/csv');
			    header('Content-Disposition: attachment; filename=Outscan_report_'.$outscan_date_str.'.csv');
			    header('Content-Transfer-Encoding: binary');
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . strlen($csv_data));
		 		echo $csv_data;
			}
	 	}else
	 	{
	 		echo '<script>alert("No Outscans found")</script>';
	 	}
	 	
	}
	
	/**
	  * function to load manifesto log by page 
	  *
	  */
	 function jx_manifesto_log($pg=0)
	 {
	 	$limit = 10;
	 	$output = array();
	 	$output['ttl_rows'] = $this->erpm->get_partner_manifesto_log(-1);
	 	$manifesto_list = $this->erpm->get_partner_manifesto_log($pg,$limit);
	 	if($manifesto_list->num_rows())
	 		$output['manifesto_log'] = $manifesto_list->result_array();

	 	$output['limit'] = $limit;	
	 		
	 	echo json_encode($output);
	 }
	
	function jx_outscansummbycourier()
	{
		$sql = "select courier_name,sum(cnt) as cnt  from 
				(
					(select distinct courier_name,0 as cnt from partner_transaction_details where courier_name != '')
					union 
					(select e.courier_name,count(*) as cnt  
						from king_invoice a 
						join king_orders b on a.order_id = b.id 
						join king_transactions c on b.transid = c.transid 
						join shipment_batch_process_invoice_link d on d.invoice_no = a.invoice_no 
						join partner_transaction_details e on e.transid = c.transid and e.order_no = c.partner_reference_no 
						where c.partner_id = '5' and date(shipped_on) = curdate() 
						group by e.courier_name 
						order by e.courier_name asc 
					)
				 ) as g
				group by g.courier_name 
			";
		$res = $this->db->query($sql);
		$output = array();
		$output['courier_list'] = $res->result_array();
		echo json_encode($output);
	}
	
	function jx_update_invoiceprint_count()
	{
		$inv_no = $this->input->post('invno');
		if($inv_no)
			$this->db->query('update king_invoice set total_prints = total_prints+1,last_printedon=now() where invoice_no = ? limit 1',$inv_no);
		
	}
	
	/**
	 * PNH Employee Module
	 * @author roopashree@localcircle.in
	 */

	/**
	 * function to check if employee is already registered
	 *
	 * @return boolean
	 */
//-----------------------------------------EMPLOYEE ADD --------------------------------------------------------------------------
	function _validate_newemployee()
	{
		$contact_no=$this->input->post('contact_no');
		$email=$this->input->post('email_id');

		$sql="select count(*) as status from m_employee_info where (contact_no=? or email=?) ";

		$is_avail = $this->db->query($sql,array($contact_no,$email))->row()->status;

		if($is_avail > 0)
		{
			$this->form_validation->set_message('_validate_newemployee','Employee already registered');
			return false;
		}
		return true;
	}
	function _check_validbuex()
	{
		$town=$this->input->post('town');
		$role_id=$this->input->post('role_id');
		
		if(!isset($town) && $role_id==5)
		{
			$this->form_validation->set_message('_check_validbex','Towns need to be Assigned');
			return false;
		}
		return true;
	}
	

	/**
	 * function to create thumbnail
	 */
	function _createThumbnail()
	{
		$user=$this->auth(PNH_EMPLOYEE);
		$config['image_library']= 'gd';
		$config['source_image']= './resources/employee_assets/image/';
		$config['create_thumb']= TRUE;
		$config['maintain_ratio']= TRUE;
		$config['width']= 75;
		$config['height']=75;

		$this->image_lib->initialize($config);

		$this->image_lib->resize();

		if(!$this->image_lib->resize())
			echo $this->image_lib->display_errors();
	}
	/**
	 * Add Employee
	 */
	
	function add_employee()
	{
		$user = $this->auth_pnh_employee();
		$role_id=$this->get_jobrolebyuid($user['userid']);
		if(empty($role_id))
			show_error("Access Denied");
		
			if($role_id<=3)
			{
				$access_roles = $this->erpm->get_emp_access_roles();
				$roles_list=$this->erpm->getRolesList();
				$data['access_roles']=$access_roles;
				$data['roles_list']=$roles_list;
				$data['page']='add_emp';
				$this->load->view("admin",$data);
			}
	}

	/**
	 * function to validate pnh employee username in add,edit forms 
	 *
	 * @return  boolean
	 */
	function _validate_pnhempusername()
	{
		$username = $this->input->post('username');
		if($this->db->query('select count(*) as t from king_admin where username = ? ',$username)->row()->t)
		{
			$this->form_validation->set_message('_validate_pnhempusername','Username already available');
			return false;
		}
		return true;
	}
	
	/**
	 * process add_employee employee form details
	 *
	 */
	function process_addemployee()
	{
		
		$user=$this->auth_pnh_employee();
		$role_id=$this->get_jobrolebyuid($user['userid']);
		if($role_id<=3)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('emp_name','Name','required');
			$this->form_validation->set_rules('father_name','father_name');
			$this->form_validation->set_rules('has_login','has login');
			$has_login = $this->input->post('has_login');
			if($has_login)
			{
				$this->form_validation->set_rules('username','username','required|callback__validate_pnhempusername');
				$this->form_validation->set_rules('password','password','required');	
			}
			
			$this->form_validation->set_rules('mother_name','mother_name');
			$this->form_validation->set_rules('edu','Education');
			$this->form_validation->set_rules('dob','D.O.B');
			$this->form_validation->set_rules('gender','Gender','required');
			$this->form_validation->set_rules('city','City','required');
			$this->form_validation->set_rules('email_id','Email','trim|max_length[128]|valid_email|is_unique');
			$this->form_validation->set_rules('address','Address','required');
			
			
			$contact_no_list = $this->input->post('contact_no');
			for($c=0;$c<count($contact_no_list);$c++)
				$this->form_validation->set_rules('contact_no['.$c.']','Phone Number - '.($c+1),'required');
			
			$this->form_validation->set_rules('role_id','Job Title','required');
			
			if($this->input->post('role_id') <= 5)
				$this->form_validation->set_rules('assigned_under_id','Assigned Under','required');
	
			if($this->form_validation->run() == false)
			{
				$this->add_employee();
			}
			else
			{
	
				$config['upload_path'] = 'resources/employee_assets/image';
				$config['allowed_types'] ='jpg|jpeg|png';
				$config['max_size']	= '2000';
				$config['max_width']  = '1024';
				$config['max_height']  = '768';
	
				$this->load->library('upload');
	
				$this->upload->initialize($config);
	
				if($this->upload->do_upload('image'))
				{
					$data = array('upload_data' => $this->upload->data());
					$fdata=$this->upload->data();
					$image_url=$fdata['file_name'];
				}
				else
				{
					$image_url=" ";
				}
	
				$config1['upload_path'] ='resources/employee_assets/cv';
				$config1['allowed_types'] ='doc|pdf|txt';
				$config1['max_size']	= '40000';
	
				$this->upload->initialize($config1);
	
				if($this->upload->do_upload('cv'))
				{
					$data1 = array('upload_data' => $this->upload->data());
					$fdata1=$this->upload->data();
					$cv_url=$fdata1['file_name'];
				}
				else
				{
					$cv_url=" ";
				}
					
				$contact_no_list=$this->input->post('contact_no');
				
				$contact_no = implode(',',$contact_no_list);
				
				$email=$this->input->post('email_id');
				$assigned_under_id=$this->input->post('assigned_under_id');
				$role_id=$this->input->post('role_id');
				
				$town_ids = $this->input->post('town');
				$territory_ids = $this->input->post('territory');
					
				// prepare employee info for table insert
				$ins_data = array();
				$ins_data['name']=$name=trim($this->input->post('emp_name'));
				$ins_data['fathername']=trim($this->input->post('father_name'));
				$ins_data['mothername']=trim($this->input->post('mother_name'));
				$ins_data['qualification']=trim($this->input->post('edu'));
				if($this->db->query('select 1 from m_employee_info where contact_no like ? ','%'.$contact_no.'%')->num_rows()!=0)
					show_error("Already a Employee exists with given login mobile");
				else
					$ins_data['contact_no']=$contact_no;
				$ins_data['dob']=$this->input->post('dob');
				$ins_data['email']=trim($this->input->post('email_id'));
				$ins_data['address']=trim($this->input->post('address'));
				$ins_data['city']=trim($this->input->post('city'));
				$ins_data['postcode']=$this->input->post('postcode');
				
				$ins_data['gender']=$this->input->post('gender');
				
				if($role_id>5)
				{
					$ins_data['job_title']=5;
					$ins_data['job_title2']=$this->input->post('role_id');
				}
				else 
				{
					$ins_data['job_title']=$this->input->post('role_id');
					$ins_data['job_title2']=$this->input->post('role_id');
				}
				
				$assigned_under_id = $this->input->post('assigned_under_id');
				if($role_id >= 6)
				{
					$territory_ids=$this->input->post('territory');
					if($territory_ids)
					{
						//select tm of the assigned_territory
						$tm_empid=$this->db->query("SELECT t.employee_id FROM `m_town_territory_link`t JOIN m_employee_info b ON b.employee_id=t.employee_id WHERE territory_id=? AND is_active=1 AND b.is_suspended=0 AND b.job_title2=4",$territory_ids[0])->row()->employee_id;
						 if(!$tm_empid)
							$tm_empid=$this->db->query("SELECT t.employee_id FROM `m_town_territory_link`t JOIN m_employee_info b ON b.employee_id=t.employee_id WHERE territory_id=? AND is_active=1 AND b.is_suspended=0 AND b.job_title2=3",$territory_ids[0])->row()->employee_id;
					 }
					$assigned_under_id = $tm_empid;
					
				}
				$ins_data['cv_url']=$cv_url;
				$ins_data['photo_url']=$image_url;
				$ins_data['created_on']=date('Y-m-d H:i:s');
				$ins_data['created_by']=1;
				$ins_data['assigned_under']=$assigned_under_id;
				$ins_data['is_suspended']= '0';
				
							
				$this->db->insert('m_employee_info',$ins_data);
					
				$emp_id=$this->db->insert_id();
					
				// updated employee role info
				$this->db->query("insert into m_employee_rolelink(employee_id,parent_emp_id,is_active,assigned_on)values(?,?,'1',now())",array($emp_id,$assigned_under_id));
					
				//For Bussiness Executive Towns need to  be selected
	
				
				// reset  town ids if none of towns selected
				if(!$town_ids)
					$town_ids=array(0);
				if(!$territory_ids)
					$territory_ids=array(0);
	
				// add multiple territories
				 
				foreach ($territory_ids as $tr_id)
				{
					foreach ($town_ids as $tw_id)
					{
						$this->db->query("insert into m_town_territory_link(parent_emp_id,employee_id,territory_id,town_id,is_active,created_on)values(?,?,?,?,'1',now())",array($assigned_under_id,$emp_id,$tr_id,$tw_id));
					}
				}
				
				/*connect tm to bu if bu as no tm and directly linked to mgr*/
				
				//find bu's don't have tm
				$sql=$this->db->query("SELECT DISTINCT a.territory_id,a.employee_id,a.parent_emp_id,t.territory_name
										FROM `m_town_territory_link`a
										JOIN m_employee_info b ON b.employee_id=a.employee_id
										JOIN m_employee_info c ON c.employee_id=a.parent_emp_id
										JOIN `pnh_m_territory_info`t ON t.id=a.territory_id
										WHERE a.is_active=1 AND b.job_title2>=5 AND b.is_suspended=0 AND c.job_title2=3
										GROUP BY a.territory_id,a.employee_id");
				
				if($sql->num_rows())
				{
					foreach($bu_notms=$sql->result_array() as $bu_extm)
					{
						foreach($territory_ids as $trritory_id)
						{
							if($bu_extm['territory_id']==$trritory_id)
							{
								$this->db->query("update m_town_territory_link a JOIN m_employee_info b ON b.employee_id=a.employee_id set a.parent_emp_id=$emp_id where a.territory_id=$trritory_id and a.is_active=1 AND b.job_title2>=5 And b.is_suspended=0");
									
								if($this->db->affected_rows()>=1)
								{
									$emp_ids=$this->db->query("select employee_id from m_town_territory_link where parent_emp_id=? and is_active=1 GROUP BY employee_id",$emp_id)->result_array();
									foreach($emp_ids as $bu_empid)
									{
										$this->db->query("update m_employee_rolelink set parent_emp_id=$emp_id where employee_id=?",$bu_empid);
										$this->db->query("update m_employee_info set assigned_under=$emp_id where employee_id=?",$bu_empid);
									}
								}
							}

						}
					}

				}
				
				// inser into king admin : uid update uid in empl info where empid =  $emp_id
			
				
				if($has_login)
				{
					$access = 'EMP_MANAGE_TASK';
					
					$username=$this->input->post('username');
					$password=$this->input->post('password');
					$name=$this->input->post('emp_name');
					
					$this->db->query("insert into king_admin(user_id,username,name,email,access,password,createdon) values(?,?,?,?,?,?,now())",array(md5($username),$username,$name,$email,$access,md5($password)));
					$uid = $this->db->insert_id();
					
					$this->db->query("update m_employee_info set user_id = ? where employee_id = ? ",array($uid,$emp_id));
	
				}
				
				$this->erpm->flash_msg("Employee Details Added");
				
				redirect('admin/list_employee');
			}
		}
	}
	
	
	function jx_checkcontact($emp_id='')
	{
		
		$contact_no_list=$this->input->post('contact_no');
		$contact_no_list = explode(',',$contact_no_list);
		$contact_no = implode(',',$contact_no_list);
		if($emp_id != '')
		{
			$emp=$this->db->query('select 1 from m_employee_info where employee_id!=? and contact_no like ?',array($emp_id,'%'.$contact_no.'%'))->num_rows()!=0;
		}
		else 
		{
			$emp=$this->db->query('select 1 from m_employee_info where contact_no like ? ','%'.$contact_no.'%')->num_rows()!=0;
		}
		if(empty($emp))
			echo "1";
		else
			echo "0";
	}
	
	
	
     /**
      * To get superior names by role_id
      * @param unknown_type $role_id
      */
    
	function get_superior_names($role_id)
	{
		$output = array();
		
			if($role_id==6)
			{
				$role_id=$role_id-1;
			}
			if($role_id==7)
			{
				$role_id=$role_id-3;
			}
			if($role_id==8)
			{
				$role_id=$role_id-5;
			}
			else 
			{
				$role_id = $role_id-1;
			}
		
		
		$emp_list = $this->erpm->get_empbyroleid($role_id);

		if($emp_list)
		{
			$output['status'] = 'success';
			$output['emp_list'] = $emp_list;
		}
		else
		{
			$output['status'] = 'error';
			$output['message'] = 'No superior employees found';
		}

		echo json_encode($output);

	}
	
	
//----------------------------------------------END OF ADD EMPLOYEE-------------------------------------------------------------------------------	

//-----------------------------------------------EDIT EMPLOYEE-----------------------------------------------------------------------
	/**
	 * Function to edit
	 * @param unknown_type $emp_id
	 */
	function edit_employee($emp_id='')
	{
		//$user=$this->auth();
		$user = $this->auth_pnh_employee();
		 if(!$emp_id)
			show_404(); 
		$role_id=$this->get_jobrolebyuid($user['userid']);
		if(empty($role_id))
			show_error("Access Denied");
		if($role_id<=3)
		{  
			$data['emp_details']=$this->erpm->get_empinfo($emp_id);
			$data['page']='edit_emp';
			$this->load->view('admin',$data);
		}
	}
	/**
	 *
	 * @param unknown_type $emp_id
	 */
	function process_editemployee($emp_id)
	{
		
		$user = $this->auth_pnh_employee();
		$role_id=$this->get_jobrolebyuid($user['userid']);
		
		 
		if($role_id<=3)
		{
			$this->load->library('form_validation');
	
			$this->form_validation->set_rules('emp_name','Name','required');
			$this->form_validation->set_rules('gender','Gender','required');
			$this->form_validation->set_rules('address','address','required');
			$this->form_validation->set_rules('city','City','required');
			$this->form_validation->set_rules('contact_no','Phone Number','required');
			$this->form_validation->set_rules('role_id','Job Title','required');
				
			if($this->input->post('role_id') <= 5)
				$this->form_validation->set_rules('assigned_under_id','Assigned Under','required');
			
			if($this->form_validation->run()==false)
			{
				$this->edit_employee($emp_id);
			}
			else
			{
	            $config['upload_path'] = 'resources/employee_assets/image';
				$config['allowed_types'] ='jpg|jpeg|png';
				$config['max_size']	= '2000';
				$config['max_width']  = '1024';
				$config['max_height']  = '768';
	
				$this->load->library('upload');
	
				$this->upload->initialize($config);
	
				if($this->upload->do_upload('image'))
				{
					$data = array('upload_data' => $this->upload->data());
					$fdata=$this->upload->data();
					$image_url=$fdata['file_name'];
					
				}
				else
				{
					$image_url=" ";
				}
	
				$config1['upload_path'] ='resources/employee_assets/cv';
				$config1['allowed_types'] ='doc|pdf|txt';
				$config1['max_size']	= '40000';
	
				$this->upload->initialize($config1);
	
				if($this->upload->do_upload('cv'))
				{
					$data1 = array('upload_data' => $this->upload->data());
					$fdata1=$this->upload->data();
					$cv_url=$fdata1['file_name'];
				}
				else
				{
					$cv_url=" ";
				}
				
				$assigned_under_id = $this->input->post('assigned_under_id');
				
				$contact_no_list=$this->input->post('contact_no');
					
				$contact_no = implode(',',$contact_no_list);
				  
				
	
				//insert the data into table
				$ins_data = array();
	
				$ins_data['name']=$this->input->post('emp_name');
				$ins_data['fathername']=$this->input->post('father_name');
				$ins_data['mothername']=$this->input->post('mother_name');
				$ins_data['qualification']=$this->input->post('edu');
				$ins_data['dob']=$this->input->post('dob');
				$ins_data['email']=$this->input->post('email_id');
				$ins_data['address']=$this->input->post('address');
				$ins_data['city']=$this->input->post('city');
				$ins_data['postcode']=$this->input->post('postcode');
				/*  if($this->db->query('select 1 from m_employee_info where contact_no like ? ','%'.$contact_no.'%')->num_rows()!=0)
					show_error("Already a Employee exists with given login mobile");
				else  */
				$ins_data['contact_no']=$contact_no;
				$ins_data['gender']=trim($this->input->post('gender'));
				$ins_data['job_title']=$this->input->post('role_id');
				
				if($role_id>5)
				{
					$ins_data['job_title']=5;
					$ins_data['job_title2']=$this->input->post('role_id');
				
				}
				else 
				{
					$ins_data['job_title']=$this->input->post('role_id');
					$ins_data['job_title2']=$this->input->post('role_id');
				}
				
				$role_id=$this->input->post('role_id');
				if($role_id>= 6)
				{
					$territory_ids = $this->input->post('territory');
					if($territory_ids)
					{
					//select tm of the assigned_territory
					$tm_empid=$this->db->query("SELECT t.employee_id FROM `m_town_territory_link`t JOIN m_employee_info b ON b.employee_id=t.employee_id WHERE territory_id=? AND is_active=1 AND b.is_suspended=0 AND b.job_title2=4",$territory_ids[0])->row()->employee_id;
					 if(!$tm_empid)
						$tm_empid=$this->db->query("SELECT t.employee_id FROM `m_town_territory_link`t JOIN m_employee_info b ON b.employee_id=t.employee_id WHERE territory_id=? AND is_active=1 AND b.is_suspended=0 AND b.job_title2=3",$territory_ids[0])->row()->employee_id;
					 }
					$assigned_under_id = $tm_empid;
					
				}
				
				$ins_data['assigned_under']=$assigned_under_id;
					
	
				if($cv_url)
					$ins_data['cv_url']=$cv_url;
				if($image_url)
					$ins_data['photo_url']=$image_url;
	
				$ins_data['modified_on']=date('Y-m-d H:i:s');
				$ins_data['modified_by']=1;
				$this->db->where('employee_id',$emp_id);
				$this->db->update('m_employee_info',$ins_data);
	
				//check if emp tree avail for empid
	
				$ins_assignment = 1;
				$parent_emp_id = $this->db->query('select IFNULL(SUM(parent_emp_id),0) AS parent_emp_id  from m_employee_rolelink where employee_id = ? and is_active = 1 ',$emp_id)->row()->parent_emp_id;
				if($parent_emp_id)
				{
					if($parent_emp_id == $assigned_under_id)
					{
						$ins_assignment = 0;
					}
					else
					{
						$this->db->query('update m_employee_rolelink set is_active = 0,modified_on=now() where is_active = 1 and employee_id = ? and parent_emp_id = ? ',array($emp_id,$parent_emp_id));
	
					}
				}
				if($ins_assignment)
					$this->db->query("insert into m_employee_rolelink (employee_id,parent_emp_id,is_active,assigned_on) values (?,?,1,now())",array($emp_id,$assigned_under_id));
					$rolelink_id=$this->db->insert_id();
			}
	
			// add_employee to emp tree
				
			$town_ids = $this->input->post('town');
			$territory_ids = $this->input->post('territory');
			//set condition for bussiness executive	
			
			if(!$town_ids && $role_id=5)
			{
				$this->erpm->flash_msg('Towns need to alloted');
			}
			
	        //reset towns & territories if empty
	         
			if(!$town_ids)
			{
				$town_ids=array(0);
			}
			if(!$territory_ids)
			{
				$territory_ids=array(0);
			}
			
			//update town territory ids
			$modified_on = date('Y-m-d H:i:s');
			$prev_terr_link = $this->db->query("SELECT GROUP_CONCAT(territory_id*1) AS tr_ids FROM m_town_territory_link WHERE employee_id = ? AND is_active = 1;",$emp_id)->row()->tr_ids;
			
			$this->db->query("update m_town_territory_link set is_active = 0,modified_on=? where is_active = 1 and employee_id = ? ",array($modified_on,$emp_id));
			
			foreach($territory_ids as $territory_id)
			{
				
					foreach($town_ids as $town_id)
					{
						if(!$this->db->query("select count(*) as t from m_town_territory_link where employee_id = ? AND  territory_id=? and town_id = ? and is_active = 0 and modified_on = ? ",array($emp_id,$territory_id,$town_id,$modified_on))->row()->t)
						{
							$this->db->query('insert into m_town_territory_link(parent_emp_id,employee_id,territory_id,town_id,is_active,modified_on)values(?,?,?,?,1,now())',array($assigned_under_id,$emp_id,$territory_id,$town_id));
						}
						else
						{
							$this->db->query("update m_town_territory_link set is_active = 1,modified_on=now() where is_active = 0 and employee_id = ?  AND  territory_id=? and town_id= ? and modified_on = ? ",array($emp_id,$territory_id,$town_id,$modified_on));
						}
					}
			}
			
			$prev_terr_link_arr = explode(',',$prev_terr_link);
			foreach ($prev_terr_link_arr as $e_trid)
			{
				if(!in_array($e_trid, $territory_ids))
				{
					// unlink suboridantes of employee id by terr id
					$this->db->query("update m_town_territory_link set parent_emp_id = 0,is_active = 0 where parent_emp_id = ? and territory_id = ? ",array($emp_id,$e_trid));
				}
			}
			
			$this->erpm->flash_msg("Employee Details Updated");
		
			redirect('admin/list_employee');
		}
		
	}

		
	/**
	 * function to get superior emp list by role_id
	 *
	 * @param unknown_type $role_id
	 */
	function get_superior_emplist($role_id)
	{
		$output = array();

		$superior_role_id = $role_id-1;

		$emp_list = $this->common->get_empbyroleid($superior_role_id);
		if($emp_list)
		{
			$output['status'] = 'success';
			$output['emp_list'] = $emp_list;
		}
		else
		{
			$output['status'] = 'error';
			$output['message'] = '';
		}

		echo json_encode($output);
	}
	
	function suggest_territories($emp_id=0,$emp_id1=0,$request_role_id=0)
	{
		$role_id = $this->db->query("select job_title from m_employee_info where employee_id = ? ",$emp_id)->row()->job_title;
	
		if($request_role_id == 6)
		{
			$sql = ("SELECT  a.id,a.territory_name from pnh_m_territory_info a order by a.territory_name ");
		}else
		{
		
		// if business head
		if($role_id == 2)
		{
			$sql = ("(SELECT  a.id,a.territory_name
					FROM  pnh_m_territory_info a
					LEFT JOIN m_town_territory_link  b ON a.id = b.territory_id and b.is_active=1
					WHERE b.id  IS NULL
					ORDER BY a.territory_name)UNION
					(SELECT a.id,a.territory_name
					FROM  pnh_m_territory_info a
					JOIN m_town_territory_link  b ON a.id = b.territory_id and b.is_active=1
					and  b.employee_id='$emp_id1')
					union
					(SELECT t.id AS territory_id,t.territory_name
					FROM `m_town_territory_link` a
					JOIN `m_employee_info` b ON a.employee_id = b.employee_id
					JOIN pnh_m_territory_info t ON t.id = a.territory_id
					LEFT JOIN `m_town_territory_link` c ON c.territory_id =  a.territory_id AND  c.is_active = 1 AND a.id != c.id
					WHERE a.is_active = 1 AND c.id IS NULL AND b.job_title > 3
					ORDER BY b.job_title)");
	
					
	
		}
		//if Manager
			else if($role_id == 3)
			{
			$sql = ('(SELECT  c.id,c.territory_name,d.job_title
					FROM `pnh_m_territory_info`c
					JOIN  m_town_territory_link a ON c.id=a.territory_id AND a.employee_id = '.$emp_id.' AND a.is_active=1
					JOIN `m_employee_info`d ON d.employee_id=a.employee_id
					LEFT JOIN m_town_territory_link b ON b.territory_id=a.territory_id AND b.employee_id != '.$emp_id.' AND b.is_active=1
					WHERE  b.territory_id IS NULL)
					UNION
					(SELECT  c.id,c.territory_name,d.job_title
					FROM `pnh_m_territory_info`c
					JOIN  m_town_territory_link a ON c.id=a.territory_id AND a.employee_id = '.$emp_id1.' AND a.is_active=1
					JOIN `m_employee_info`d ON d.employee_id=a.employee_id
					LEFT JOIN m_town_territory_link b ON b.territory_id=a.territory_id AND b.employee_id != '.$emp_id1.' AND b.is_active=1
					WHERE  b.territory_id IS NOT NULL
					GROUP BY c.id)
					UNION
					(SELECT DISTINCT a.territory_id,t.territory_name,c.job_title
					FROM `m_town_territory_link`a
					JOIN m_employee_info b ON b.employee_id=a.employee_id
					JOIN m_employee_info c ON c.employee_id=a.parent_emp_id
					JOIN `pnh_m_territory_info`t ON t.id=a.territory_id
					WHERE a.is_active=1 AND b.job_title2>=5 AND b.is_suspended=0 AND c.employee_id='.$emp_id.'
					GROUP BY a.territory_id,a.employee_id)
					');
			//echo $sql;
			/* (SELECT  c.id,c.territory_name,b.job_title
						FROM `pnh_m_territory_info`c
						JOIN  m_town_territory_link a ON c.id=a.territory_id
						JOIN m_employee_info b ON b.employee_id =a.employee_id
						WHERE a.is_active=0 AND b.is_suspended=1  AND b.job_title2=4 AND b.assigned_under='.$emp_id.'
						GROUP BY territory_id
					); */
			}
		// Territory Manager
			else if($role_id == 4)
			{
				$sql = ('(SELECT  c.id,c.territory_name
						FROM `pnh_m_territory_info`c
						JOIN  m_town_territory_link a ON c.id=a.territory_id
						WHERE   a.employee_id = '.$emp_id.'  AND a.is_active=1)
							UNION
						(SELECT  c.id,c.territory_name
						FROM `pnh_m_territory_info`c
						JOIN  m_town_territory_link a ON c.id=a.territory_id
						WHERE   a.employee_id = '.$emp_id1.'  AND a.is_active=1 AND a.id IS NULL)');
			}
		}
		$terr_list_res = $this->db->query($sql);
	
			//echo $sql;
			$output = array();
			if($terr_list_res->num_rows())
			{
			$output['terr_list'] = $terr_list_res->result_array();
			$output['status'] = 'success';
			}
			else
			{
			$output['status'] = 'error';
				$output['message'] = 'no territories added yet';
			}
		echo json_encode($output);
	}
	
	function suggest_towns($territory_id,$emp_id1=0)
		{
	
			$town_list=$this->db->query("(SELECT a.territory_id,a.id AS town_id,a.town_name
			FROM pnh_towns a
			LEFT JOIN m_town_territory_link b ON b.territory_id = a.territory_id AND b.town_id = a.id AND b.is_active = 1
			WHERE a.territory_id = '$territory_id')
			UNION
			(SELECT a.territory_id,a.id AS town_id,a.town_name
					FROM pnh_towns a
					JOIN m_town_territory_link b ON b.territory_id = a.territory_id AND b.town_id = a.id AND b.is_active = 1
					WHERE a.territory_id =  '$territory_id' AND b.id IS NOT NULL AND b.employee_id='$emp_id1')");
	
					$output=array();
					if($town_list->num_rows())
					{
					$output['town_list']=$town_list->result_array();
			$output['status']='success';
					}
					else
				{
							$output['status']='error';
						$output['message']='No Towns found to link';
				}
	
			echo json_encode($output);
		}

			function get_assigned_towns($emp_id)
			{
			$twn_terry_link=$this->db->query("SELECT c.territory_name,b.town_name,a.employee_id
				FROM m_town_territory_link a
				JOIN `pnh_towns` b ON b.id=a.town_id
				JOIN `pnh_m_territory_info`c ON c.id=a.territory_id
				WHERE employee_id=?",$emp_id);
	
		$output=array();
			if($twn_terry_link->num_rows())
			{
			$output['twn_terry_link']=$twn_terry_link->result_array();
			$output['status']='success';
			}
	
			else
			{
			$output['status']='error';
				$output['message']='No Territory Link Found';
			}
	
		echo json_encode($output);
	}
		
	function get_terry_link_byempid($role_id)
		{
			$unlinkd_terrylink=$this->db->query("SELECT  c.id,c.territory_name,d.job_title
										FROM `pnh_m_territory_info`c
										JOIN  m_town_territory_link a ON c.id=a.territory_id AND a.employee_id = ? AND a.is_active=1
										JOIN `m_employee_info`d ON d.employee_id=a.employee_id
										LEFT JOIN m_town_territory_link b ON b.territory_id=a.territory_id AND b.employee_id !=? AND b.is_active=1
										WHERE  b.territory_id IS NULL AND  d.job_title=?",$role_id);

				$output=array();
				if($unlinkd_terrylink->num_rows())
				{
					$output['unlinkd_terrylink']=$unlinkd_terrylink->result_array();
					$output['status']='success';
			    }
			else
			{
						$output['status']='error';
						$output['message']='No territory found';
			}
			echo json_encode($output);
		}
		
		function pnh_unsuspend_emp($emp_id)
		{
			$user=$this->auth(PNH_EXECUTIVE_ROLE);
			$emp=$this->db->query("select * from m_employee_info where employee_id=?",$emp_id)->row_array();
			if(empty($emp))
				show_error("No Employee found");
			//$emp_id=$this->input->post(s_empid);
			$s_remarks=$this->input->post('unsuspend_empremarks');
			$this->db->query("update m_employee_info set is_suspended=0,suspended_on=now(),remarks=?,suspended_by={$user['userid']} where employee_id=? limit 1",array($s_remarks,$emp_id));
			$this->db->query("update m_employee_rolelink set is_active=1 where employee_id=? ",$emp_id);
			$this->db->query("update m_town_territory_link set is_active=1 where employee_id=?",$emp_id);
			$this->erpm->flash_msg("Employee unsuspended");
			$this->erpm->send_admin_note("Employee account : {$emp['name']} ({$emp['employee_id']}) was unsuspended on ".date("g:ia d/m/y")." by {$user['username']}","Employee account unsuspension");
			redirect("admin/view_employee/$emp_id");
		}
		
		function pnh_suspend_emp($emp_id)
		{
			$user=$this->auth(PNH_EXECUTIVE_ROLE);
			$emp=$this->db->query("select * from m_employee_info where employee_id=?",$emp_id)->row_array();
			if(empty($emp))
				show_error("No Employee found");
			$uns_remarks=$this->input->post('suspend_empremarks');
			$this->db->query("update m_employee_info set is_suspended=1,suspended_on=now(),remarks=?,suspended_by={$user['userid']} where employee_id=? limit 1",array($uns_remarks,$emp_id));
			$this->db->query("update m_employee_rolelink set is_active=0 where employee_id=?",$emp_id);
			$this->db->query("update m_town_territory_link set is_active=0 where employee_id=?",$emp_id);
			if($emp['job_title2']==4)
			{
				$mgr_empid=$this->db->query("select employee_id from m_employee_info where job_title2=3 and is_suspended=0 limit 1")->row()->employee_id;
				$this->db->query("update m_employee_rolelink set parent_emp_id=$mgr_empid where parent_emp_id=$emp_id and is_active=1");
				$this->db->query("update m_town_territory_link set parent_emp_id=$mgr_empid where parent_emp_id=$emp_id and is_active=1");
				$this->db->query("update m_employee_info set assigned_under=$mgr_empid where assigned_under=$emp_id and is_suspended=0");
			}
			
			$this->erpm->flash_msg("Employee suspended");
			$this->erpm->send_admin_note("Employee account : {$emp['name']} ({$emp['employee_id']}) was suspended on ".date("g:ia d/m/y")." by {$user['username']}","Employee account suspension");
			redirect("admin/view_employee/$emp_id");
		}
	
//---------------------------------------------END OF EDIT EMPLOYEE--------------------------------------------------------------------------
	
//--------------------------------------------------VIEW EMPLOYEE--------------------------------------------------------------------------------	
	function view_employee($emp_id='',$pg=0)
	{
		$this->task_status = $this->config->item('task_status');
		$user=$this->auth();
			
			$emp_details=$this->erpm->get_empinfo($emp_id);
			$isactive_territories=$this->erpm->isactive_territorylist();
			$unactive_territories=$this->erpm->unactive_territorylist();
			$isactive_towns=$this->erpm->isactive_townlist();
			$unactive_towns=$this->erpm->unactive_townlist();
			$assignment_details=$this->erpm->to_get_assignmnt_details($emp_id);
			$send_invoices_det='';
			$show_send_log=0;
			$emp_id=$emp_details['employee_id'];
			if(($emp_details['job_title2']==6 && $emp_details['job_title']==5) || ($emp_details['job_title2']==7 && $emp_details['job_title']==5 ))
			{
				$from_date=$this->input->post('from');
				$to_date=$this->input->post('to');
				
				$show_send_log=1;
				$sent_invoices_by_driver=$this->erpm->get_manifesto_sen_invoices_det_by_emp($emp_id,$pg,$from_date,$to_date);
				$send_invoices_det=$sent_invoices_by_driver['sent_invoices'];
				
				//pagination block
				$this->load->library('pagination');
				$config['base_url'] = site_url('admin/view_employee/'.$emp_id);
				$config['total_rows'] = $sent_invoices_by_driver['total_sent_invoices'];
				$config['uri_segment'] = 4;
				$config['per_page'] = 5;
				
				$this->config->set_item('enable_query_strings',false);
				$this->pagination->initialize($config);
				$data['manifesto_sent_log_pagi'] = $this->pagination->create_links();
				$this->config->set_item('enable_query_strings',true);
				//pagination block end
			}
				
			$data['show_send_log']=$show_send_log;
			$data['send_invoice_det']=$send_invoices_det;
			$data['unactive_territories']=$unactive_territories;
			$data['isactive_territories']=$isactive_territories;
			$data['isactive_towns']=$isactive_towns;
			$data['unactive_towns']=$unactive_towns;
			$data['emp_details']=$emp_details;
			
			
			$data['page']='view_emp';
			$this->load->view('admin',$data);
		
	}
	
	function jx_view_activitylog()
	{
		$task_status=array();
		
	 	$task_status[0]='Closed';
	 	$task_status[1]='Pending';
		$task_status[2]='Complete';
		$task_status[3]='Closed';
		
		
		$task_id=$this->input->post('task_id');
		$t_activitylog=$this->db->query('SELECT DATE_FORMAT(start_date,"%d/%m/%y") AS start_date,DATE_FORMAT(end_date,"%d/%m/%y") AS end_date,msg,DATE_FORMAT(logged_on,"%b %d %Y %h:%i %p") AS logged_on,logged_by,task_status,b.name
											FROM t_pnh_taskactivity a
											JOIN king_admin b ON b.id=a.logged_by
											WHERE task_id=?
											ORDER BY logged_on DESC',$task_id);
		
		$output = array();
		if($t_activitylog->num_rows())
		{
			$output['activity_log']=$t_activitylog->result_array();
			$output['task_status_list']=$task_status;
			
		
			$output['status']='success';
		}
		else
		{
			$output['status']='error';
		}
		echo json_encode($output);
	}
//-----------------------------------------------END OF VIEW EMPLOYEE--------------------------------------------------------------------------
	
//-----------------------------------------------LIST EMPLOYEE-------------------------------------------------------------------	
	function list_employee($role_id=0,$territory_id=0,$pg=0)
	{
		$this->erpm->auth();
		$cond='';
		if($role_id)
			$cond.=' and a.job_title2='.$role_id;
		
		if($territory_id )
			$cond.=' and c.territory_id='.$territory_id;

		$emp_list=$this->db->query("SELECT *,b.role_name AS fc,d.role_name FROM  m_employee_info a
									LEFT  JOIN m_employee_roles b ON b.role_id=a.job_title2
									LEFT JOIN m_town_territory_link c ON c.employee_id=a.employee_id
									LEFT   JOIN m_employee_roles d ON d.role_id=a.job_title2
									WHERE 1 and a.is_suspended=0 $cond 
									GROUP BY a.employee_id
									ORDER BY a.name ASC
									")->result_array();
												
									

		$access_roles = $this->erpm->get_emp_access_roles();
		$territories = $this->erpm->to_get_all_territories();

		$this->load->library('pagination');
		$config['base_url'] = site_url('/admin/list_employee/'.$role_id.'/'.$territory_id);
		$config['total_rows'] = $this->db->query("SELECT COUNT(DISTINCT a.employee_id) AS total FROM  m_employee_info a
													JOIN m_employee_roles b ON b.role_id=a.job_title2
													left join m_town_territory_link c on c.employee_id=a.employee_id
													WHERE 1 and a.is_suspended=0 $cond")->row()->total;
		$config['per_page'] = MAX_ROWS_DISP;
		$config['uri_segment'] = 5;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$pagination = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);

		$data['access_roles']=$access_roles;
		$data['pg']=$pg;
		$data['territories']=$territories;
		$data['pagination'] ='';
		$data['emp_list']=$emp_list;
		$data['page']='list_employee';
		$this->load->view("admin",$data);
	}

//----------------------------------------END OF LIST EMPLOYEE--------------------------------------------------------------------------


//-----------------------Assignment History-------------------------------	
	function assignment_histroy()
	{
		$this->erpm->auth();
		$assignment_histroy = $this->erpm->to_get_assignmnt_histroy();
		$data['assignment_histroy']=$assignment_histroy;
		$data['page']="assignment_histroy";
		$this->load->view("admin",$data);
	}
//--------------------------------ROLETREE----------------------------------------------------------------------------------------------
	function roletree_view()
	{
		$this->erpm->auth();
		$data['page']="roletree_view";
		$this->load->view("admin",$data);
	}
	
//-----------------------------------------------------------------------------------------------------------------------------	

	function get_emproleidbyuid($userid)
	{
		return $this->db->query("select job_title from m_employee_info where user_id = ? and is_suspended=0 ",$userid)->row()->job_title;
	}
	
	function get_empidbyuid($userid)
	{
		return $this->db->query("select employee_id from m_employee_info where user_id = ? and is_suspended=0",$userid)->row()->employee_id;
	}
	
	function get_jobrolebyuid($userid)
	{
		return @$this->db->query("select job_title as role_id from m_employee_info where user_id = ? and is_suspended=0",$userid)->row()->role_id;
	
		
	}

	function auth_pnh_employee()
	{
		$userdet=$this->auth(PNH_EMPLOYEE);
		$is_superadmin=$this->erpm->auth(true,true);
		if($is_superadmin)
		{
			$userdet['userid'] = 1;
		}
		
		return $userdet;
	}
//-----------------------------------------------------------------------------------------------------------------------------	

//-------------------------------------------CALANDER-----------------------------------------------------------------------	
	/**
	 * function to add Employee Events in Calander
	 */ 
	function calender()
	{
		$this->erpm->auth();
		$user = $this->auth_pnh_employee();
		
		$role_id=$this->get_jobrolebyuid($user['userid']);
		if($role_id<=3)
		{
		$role_id = $this->get_emproleidbyuid($user['userid']);
		$emp_id  =  $this->get_empidbyuid($user['userid']);
		$sub_emp_ids=$this->get_subordinates($this,$role_id,$emp_id);
		$territory_list=$this->load_territoriesbyemp_id($emp_id,$role_id);
		$t_sub_emp_ids = $sub_emp_ids;
		$get_locationbyempid = $this->assigned_location($emp_id);
		
		array_push($t_sub_emp_ids,$emp_id);
	 	$terry_id = $this->input->post('view_byterry');
		$emp_sub_list = array();
		$sql="SELECT a.employee_id,a.employee_id AS id,IFNULL(b.parent_emp_id ,0) AS parent,a.name as employee_name, a.name
				FROM m_employee_info a
				LEFT JOIN m_employee_rolelink b ON b.employee_id=a.employee_id and b.is_active = 1  
				WHERE  a.is_suspended=0 and a.employee_id IN (".implode(',',$t_sub_emp_ids).")
			";
				
        $res=$this->db->query($sql);
		if($res->num_rows())
		{
			$emp_sub_list=$res->result_array();
		}
		
		$task_list=$this->db->query('SELECT task,asgnd_town_id,assigned_to,b.town_name,c.name,a.on_date 
											FROM pnh_m_task_info a
											JOIN pnh_towns b ON b.id=a.asgnd_town_id
											JOIN m_employee_info c ON c.employee_id=a.assigned_to
											where a.assigned_to = ? and c.is_suspended=0',$emp_id)->result_array();
		$this->load->plugin('yentree_pi');//plugin to create employeeTree 
		$emp_sub_list[0]['parent']=0;
		$data['emp_tree_config']=build_yentree($emp_sub_list);
		
		unset($emp_sub_list[0]);
		$emp_sub_list = array_values($emp_sub_list);
		
		$data['emp_sub_list']=$emp_sub_list;
		$data['get_locationbyempid']=$get_locationbyempid;
		$data['territory_list']=$territory_list;
		$data['task_list']=$task_list;
		$data['page']="calender";
		$this->load->view("admin",$data);
		
		}
		else 
			show_error("Access Denied");
	}
	
	
	/**
	 * function to load employee task via ajax
	 */
	function jx_load_tasklist()
	{
		//$user=$this->auth('EMP_MANAGE_TASK');
		$user=$this->auth_pnh_employee();
		$role_id=$this->get_jobrolebyuid($user['userid']);
		if($role_id<=3)
		{
		$town_id='';
		$territory_id='';
		$tasklist='';
		$town_id = $this->input->post('town_id');
		$empid = $this->input->post('emp_id');
		$loggdin_userid=$this->input->post('user_id');
		$territory_id  = $this->input->post('territory_id');
		
		$st_d = $this->input->post('start')/1000;
		$en_d = $this->input->post('end')/1000;
		//to covert timestamp to date
		$st_dt=gmdate("Y-m-d", $st_d);
		$en_dt=gmdate("Y-m-d", $en_d); 
		$cond= '';
		
		if($territory_id)
		{
			$cond .= ' and a.territory_id = '.$territory_id.' ';
		} 
		if($town_id)
		{
			$cond .= ' and asgnd_town_id = '.$town_id.' ';
		}
		
		
	   if($loggdin_userid)
	    {
			$empid = $this->get_empidbyuid($user['userid']); 
	    }
	    else
	    {
	    	if(!$empid && $cond =='')
	    	{
	    		
	    		$empid = $this->get_empidbyuid($user['userid']);
	    	}
	    }
	    
	    if($empid > 1)
	    {
	    	//$cond .= ' and ( assigned_to = '.$empid.' or assigned_by =  '.$empid.' ) ';
			$cond .= ' and ( assigned_by =  '.$empid.' ) ';
	    }
	    
		
		$tasklist = $this->db->query("SELECT a.territory_id as territory_id,b.id as id,b.task_title as title,concat('task_status_',b.task_status) as className,b.task as task,date(on_date) as start,date(due_date) as end,b.asgnd_town_id as town_id,c.town_name as town_name,d.name as employee_name,b.ref_no
											FROM m_town_territory_link  a
										 	JOIN pnh_m_task_info b ON b.assigned_to=a.employee_id
										 	JOIN pnh_towns c on c.id = b.asgnd_town_id
										 	JOIN m_employee_info d on d.employee_id=b.assigned_to
											where  date(on_date) >=? and date(due_date) <= ? and a.is_active=1 and b.is_active=1 and d.is_suspended=0 ".$cond." GROUP BY b.id ",array($st_dt,$en_dt));
		
	  
	    $output = array();
		if($tasklist->num_rows())
		{
			$output['tasklist']=$tasklist->result_array();
			$output['status']='success';
		}
		echo json_encode($output);
	}
	
	}
	
	function jx_add_emptask()
	{
		//print_r($_POST);
		
		$user=$this->auth_pnh_employee();
		$role_id=$this->get_jobrolebyuid($user['userid']);
		if($role_id<=3)
		{
		$emp_id = $this->get_empidbyuid($user['userid']);
		$task_title=$this->input->post('title');
		
		$task=$this->input->post('task');
		$emp_assigned_to = $this->input->post('assigned_to');
		$assignd_town_id=$this->input->post('assigned_town');
		$task_types=$this->input->post('choose_task_type');
		$franchise_ids=$this->input->post('tsk_frid');
		$req_msg=$this->input->post('reqst_msg');
		
		$st_date=$this->input->post('tsk_stdate');
		$st_dt=explode('/',$st_date);
		$d=$st_dt[0];
		$m=$st_dt[1];
		$y=$st_dt[2];
		$st_dte=mktime(0,0,0,$m,$d,$y);
		$st_dt=date('Y-m-d',$st_dte);
		
		//$st_dt=date('Y-m-d H:i:s',strtotime($st_date));
		$due_date=$this->input->post('due_date');
		//$en_dt=date('Y-m-d H:i:s',strtotime($due_date));
		$en_dt=explode('/',$due_date);
		$d=$en_dt[0];
		$m=$en_dt[1];
		$y=$en_dt[2];
		$en_dte=mktime(0,0,0,$m,$d,$y);
		$en_dt=date('Y-m-d',$en_dte);
		
		$task_type_ids = implode(',',$task_types);

		
		
		// creatre task entry
		
		
		$max_no=$this->db->query("select max(ref_no) as max_no from pnh_m_task_info")->row()->max_no;
		if($max_no >0)
		{
			$max_no=$max_no+1;
		}
		$sql="insert into pnh_m_task_info(task_title,task,task_type,asgnd_town_id,on_date,due_date,assigned_by,assigned_to,is_active,task_status,assigned_on,ref_no)values(?,?,?,?,?,?,?,?,1,1,now(),$max_no)" ;
		$task_res=$this->db->query($sql,array($task_title,$task,$task_type_ids,$assignd_town_id,$st_dt,$en_dt,$emp_id,$emp_assigned_to)) ;
		$task_id=$this->db->insert_id();
		
		$ins_data=array();
		$ins_data['task_id']=$task_id;
		/* if($req_msg)
		{
		$ins_data['msg']=$req_msg;
		}
		else 
			$req_msg=''; */
		$ins_data['start_date']=$st_dt;
		$ins_data['end_date']=$en_dt;
		$ins_data['task_status']=1;
		$ins_data['logged_by']=$user['userid'];
		$ins_data['logged_on']=date('Y-m-d H:i:s');
		$this->db->insert('t_pnh_taskactivity',$ins_data);
		
		foreach($task_types as $task_type)
		{
			if($task_type==1)
			{
				
				$avg_sales=$this->input->post('avg_sales');
				$target_amt=$this->input->post('tg_sales');
				if($franchise_ids){
				foreach($franchise_ids as $i=>$franchise_id){
				
					$sql="insert into pnh_m_sales_target_info(task_id,f_id,target_amount,avg_amount,status,created_on,created_by)values(?,?,?,?,0,now(),?)";
					$this->db->query($sql,array($task_id,$franchise_id,$target_amt[$i],$avg_sales[$i],$user['userid']));
				
						
					}
				}
			}
			
			if($task_type==2)
			{
				
				$pc_current_bal=$this->input->post('pc_current_bal');
				$req_msg=$this->input->post('reqst_msg');
				if($req_msg)
				foreach($req_msg[$task_type] as $req_text)
				{
					$sql="insert into pnh_task_type_details(task_id,task_type_id,custom_field_1,request_msg,created_on,created_by)values(?,?,?,?,now(),?)";
					$this->db->query($sql,array($task_id,$task_type,$pc_current_bal,$req_text,$user['userid']));
				}
			}
			if($task_type>2)
			{
				$req_msg=$this->input->post('reqst_msg');
				if($req_msg)
				foreach($req_msg[$task_type] as $req_text)
				{
					$sql="insert into pnh_task_type_details(task_id,task_type_id,request_msg,created_on)values(?,?,?,now())";
					$this->db->query($sql,array($task_id,$task_type,$req_text));
				}
			}
		}
		
		
		$output = array();
		if ($task_id)
		{
			$output['id'] = $task_id;
			$output['status'] = 'success';
		}
		else 
		{
			$output['status'] = 'error';
		}
	  	echo json_encode($output);
	}

	}
	

	/**
	 * function to load task based on emp_id
	 */
	function jx_loadall_tasklist($st=0)
	{
		
		$output = array();
		
		$task_status=array();
		
		$task_status[1]='Pending';
		$task_status[2]='Complete';
		$task_status[3]='Closed';
		
		$tr_id=$this->input->post('trid');
		$tw_id=$this->input->post('twid');
		$emp_id=$this->input->post('emp_id');
		
		$cond = '';
		if($tr_id)
			$cond .= ' and b.territory_id = '.$tr_id;
		if($tw_id)
			$cond .= ' and b.id = '.$tw_id;
		if($emp_id)
			$cond .= ' and (a.assigned_to = "'.$emp_id.'" or  a.assigned_by = "'.$emp_id.'" )';
		
		$emp_task_list=$this->db->query('SELECT a.id,task_status,asgnd_town_id,assigned_to,b.town_name,c.name,d.name AS assignedby_name,date(a.on_date) as on_date_str,DATE_FORMAT(a.on_date,"%D %M %y") as on_date
											FROM pnh_m_task_info a
											JOIN pnh_towns b ON b.id=a.asgnd_town_id
											JOIN m_employee_info c ON c.employee_id=a.assigned_to
											JOIN m_employee_info d ON d.employee_id=a.assigned_by
											where 1 and  c.is_suspended=0 and a.is_active=1'.$cond.' order by on_date asc  limit '.$st.',5', array($emp_id,$tw_id,$tr_id));
		 
		$output['total_rows']=$this->db->query('select count(*) as total
												FROM pnh_m_task_info a
												JOIN pnh_towns b ON b.id=a.asgnd_town_id
												JOIN m_employee_info c ON c.employee_id=a.assigned_to
												where 1 and a.is_active=1 and  c.is_suspended=0 '.$cond,array($emp_id,$tw_id,$tr_id))->row()->total;
		
		$date_summ_res=$this->db->query('select DATE(on_date) AS assigned_date,COUNT(*) AS ttl_tasks
												FROM pnh_m_task_info a
												JOIN pnh_towns b ON b.id=a.asgnd_town_id
												JOIN m_employee_info c ON c.employee_id=a.assigned_to
												where 1 and a.is_active=1 and  c.is_suspended=0 '.$cond.' 
												GROUP BY assigned_date 
												ORDER BY assigned_date ',array($emp_id,$tw_id,$tr_id));
		 
		$output['date_summ'] = array();
		if($date_summ_res->num_rows())
		{
			$output['date_summ'] = $date_summ_res->result_array();
		}
		
		if($emp_task_list->num_rows())
		{
			$output['emp_task_list']=$emp_task_list->result_array();
			$output['task_status']=$task_status;
			$output['status']='success';
		}
		else 
		{
			$output['status']='error';
			$output['message']='No data found';
		}
		
		echo json_encode($output);
	}
	
	function jx_load_taskdet()
	{
		$user=$this->auth_pnh_employee();
		$emp_id = $this->get_empidbyuid($user['userid']);
		$id = $this->input->post('id');
		$task_assignedto=$this->db->query("select assigned_to from pnh_m_task_info where id=?",$id)->row()->assigned_to;
		$task_types=$this->db->query("select task_type from pnh_m_task_info where id=?",$id)->row()->task_type	;
		
		$task_type_names_res = $this->db->query("SELECT * FROM `pnh_m_task_types` ")->result_array();
		$franchise_names_res=$this->db->query("select * from pnh_m_franchise_info")->result_array();
	
		$task_type_names = array();
		foreach($task_type_names_res as $task_type_name)
		{
			$task_type_names[$task_type_name['id']] = $task_type_name['task_type']; 
		}
	
		$tasks_type_for=array();
		foreach($task_type_names_res as $task_for)
		{
			$tasks_type_for[$task_for['id']] = $task_for['task_for'];
		}
	
		$franchise_names=array();
		foreach($franchise_names_res as $franchise_name)
		{
			$franchise_names[$franchise_name['franchise_id']] = $franchise_name['franchise_name'];
		}
	
		$task_types_arr = explode(',',$task_types);
		
		$taskdet_res=$this->db->query("SELECT a.ref_no,a.id AS id,h.contact_no,a.task_title,a.task,a.task_type,a.asgnd_town_id,a.assigned_by,a.assigned_to,CONCAT('task_status_',a.task_status) AS className,task_status AS `status`, a.assigned_on,a.comments AS reason,b.town_name AS town_name,h.name AS assigned_toname,i.name AS assigned_byname,DATE_FORMAT(on_date,'%d/%m/%Y')  AS `start`,DATE_FORMAT(due_date,'%d/%m/%Y')  AS `end`,j.short_frm AS assigned_torole,CONCAT(h.name ,'(',j.short_frm,')') AS assignedto_byrole_name,
										k.short_frm AS assigned_byrole,CONCAT(i.name ,'(',k.short_frm,')') AS assigned_byrole_name
										FROM pnh_m_task_info a
										JOIN pnh_towns b ON b.id=a.asgnd_town_id
										JOIN m_employee_info h ON h.employee_id=a.assigned_to
										JOIN m_employee_info i ON i.employee_id=a.assigned_by
										JOIN m_employee_roles j ON j.role_id=h.job_title
										JOIN m_employee_roles k ON k.role_id=i.job_title
										WHERE a.id =? and  h.is_suspended=0" ,array($id));
		
		$task_remarks_sms=$this->db->query("SELECT a.task_id AS ref_id,a.remarks,d.name AS posted_by,DATE_FORMAT(a.logged_on,'%d/%m/%y %h:%i %p') AS posted_on 
											FROM pnh_task_remarks a
											JOIN pnh_m_task_info b ON b.ref_no=a.task_id
											JOIN  m_employee_info d ON d.employee_id=a.emp_id
											WHERE a.emp_id=? AND b.id=? and  d.is_suspended=0 
											ORDER BY a.logged_on DESC",array($task_assignedto,$id));
		
		$output = array();
		if($taskdet_res->num_rows())
		{
			
			$output['task']=$taskdet_res->row_array();
			$output['task_type_names']=$task_type_names;
			$output['franchise_names']=$franchise_names;
			$output['tasks_type_for']=$tasks_type_for;
			$output['task_types_arr']=$task_types_arr;
		
			if($task_remarks_sms->num_rows())
			{
			  $output['task_remarks_sms']=$task_remarks_sms->result_array();
			}
			
			
			foreach($task_types_arr as $task_type)
			{
				if($task_type==1)
				{
					$sales_target=$this->db->query("SELECT a.f_id,a.avg_amount,a.target_amount,a.actual_target,c.franchise_name
													FROM `pnh_m_sales_target_info`a
													JOIN pnh_m_task_info b ON a .task_id=b.id
													JOIN pnh_m_franchise_info c ON c.franchise_id=a.f_id
													WHERE  b.id=? and c.is_suspended=0
													GROUP BY a.f_id",$id);
					$output['sales_target']=$sales_target->result_array();
				}
				
				
				if($task_type==2)
				{
					$task_description=$this->db->query("SELECT custom_field_1,task_id,request_msg,b.task_type,b.task_for FROM `pnh_task_type_details`a
														JOIN pnh_m_task_types b ON b.id=a.task_type_id
														WHERE task_id=? and  task_type_id=?",array($id,$task_type));
					$output['task_type_list'][$task_type]=$task_description->result_array();
				}
				
				if($task_type>2)
				 {
					$task_description=$this->db->query("SELECT task_id,request_msg,b.task_type,b.task_for FROM `pnh_task_type_details`a
														JOIN pnh_m_task_types b ON b.id=a.task_type_id
														WHERE task_id=? and  task_type_id=?",array($id,$task_type));
					$output['task_type_list'][$task_type]=$task_description->result_array();
				}
				
				
			}
			
		
			$output['task_type']=$task_type;
			$output['status']='success';
		}
		else
		{
			$output['status']='error';
		}
		$output['emp_id']= $emp_id;
		echo json_encode($output);
	}
	
	function jx_upd_emptask()
	{ 
		
		$user=$this->auth_pnh_employee();
		$emp_id = $this->get_emproleidbyuid($user['userid']);
		$role_id=$this->get_jobrolebyuid($user['userid']);
		if($role_id<=3)
		{
		$id=$this->input->post('task_id');
		$task_title=$this->input->post('title');
		$task=$this->input->post('task');
		$st_date=$this->input->post('tsk_stdate');
			if($st_date)
			{
				$st_dt = explode('/',$st_date);
				$d=$st_dt[0];
				$m=$st_dt[1];
				$y=$st_dt[2];
				$st_dte=mktime(0,0,0,$m,$d,$y);
				$st_dt=date('Y-m-d',$st_dte);
			}
		//$st_dt=date('Y-m-d H:i:s',strtotime($st_date));

			$due_date=$this->input->post('due_date');
			if($due_date)
			{
				$en_dt=explode('/',$due_date);
				$d=$en_dt[0];
				$m=$en_dt[1];
				$y=$en_dt[2];
				$en_dte=mktime(0,0,0,$m,$d,$y);
				$en_dt=date('Y-m-d',$en_dte);
			}
		//$en_dt=date('Y-m-d H:i:s',strtotime($due_date));

		$emp_assigned_to = $this->input->post('assigned_to');
		$task_status=$this->input->post('status');
		$task_msg=$this->input->post('msg');
		
		
		$assignd_town_id=$this->input->post('assigned_town');
		$franchise_ids=$this->input->post('tsk_frid');
			if(!$franchise_ids)
				$franchise_ids=array(0);
			
			
			$task_types=$this->db->query("select task_type from pnh_m_task_info where id=?",$id)->row()->task_type	;
			$task_types_arr = explode(',',$task_types);
			$target_amt=$this->input->post('sales_tg');
			$collected_amt=$this->input->post('collectd_amt');
			$response_msg=$this->input->post('msg');
			
			
			$ins_data=array();
			$ins_data['task_id']=$id;
			if($st_date)
			{
			$ins_data['start_date']=$st_dt;
			}
			else 
				$st_dt=' ';
			
			if($due_date)
			{
			$ins_data['end_date']=$en_dt;
			}else 
				$en_dt=' ';
			
			if($response_msg)
			{
			$ins_data['msg']=$response_msg;
			}
			if($task_status)
			{
			$ins_data['task_status']=$task_status;
			}
			$ins_data['logged_by']=$user['userid'];
			$ins_data['logged_on']=date('Y-m-d H:i:s');
			$this->db->insert('t_pnh_taskactivity',$ins_data);
			
			foreach($task_types_arr as $task_type)
			{

				if($task_status==1)
				{
					$sql="update pnh_m_task_info set on_date=?,due_date=?,comments=? where id=?";
					$this->db->query($sql,array($st_dt,$en_dt,$task_msg,$id));
					
				}
					if($task_type==1)
					{
						
						$target_amt=$this->input->post('tg_sales');
						if($target_amt)
						foreach($target_amt as $franchise_id=>$target_cash)
						{
							$sql="update pnh_m_sales_target_info set target_amount=?,status=1,modified_on=now(),modified_by = ? where task_id=? and f_id=?";
							$this->db->query($sql,array($target_cash,$user['userid'],$id,$franchise_id));
						}
					}
					
					if($task_type!=1)
					{
						$req_msg =$this->input->post('view_reqst_msg');
						if($req_msg)
						foreach($req_msg as $task_type_id=> $req_text){
						$sql="update pnh_task_type_details set request_msg=? where task_id=? and task_type_id=?";
						$this->db->query($sql,array($req_text,$id,$task_type_id));
					}
				}
			}
			
			if($task_status==2)
			{
				$sql="update pnh_m_task_info set task_status=?,comments=?,completed_on=now(),completed_by=? where id=?";
				$this->db->query($sql,array($task_status,$task_msg,$user['userid'],$id));
				

			}
			if($task_status==3)
			{
				$sql="update pnh_m_task_info set task_status=?,comments=?,cancelled_on=now(),cancelled_by=? where id=?";
				$this->db->query($sql,array($task_status,$task_msg,$user['userid'],$id));
			}
		$output = array();
		if ($id)
		{
			$output['status'] = 'success';
				
		}
		else
	 	{
			$output['status'] = 'error';
		}
		echo json_encode($output);
		
	}
}	

	function assigned_location($emp_id)
	{
		$user=$this->auth_pnh_employee();
		$role_id=$this->db->query('select job_title from m_employee_info where employee_id=? and is_suspended=0',$emp_id)->row()->job_title;
	
		if($role_id < 3)
		{
			return $this->db->query("SELECT b.id as territory_id,a.id as town_id,town_name,territory_name
					FROM pnh_towns a
					RIGHT JOIN pnh_m_territory_info b ON b.id=a.territory_id
					WHERE 1 
					ORDER BY territory_name, town_name")->result_array();
		}
		else if($role_id < 5)
		{
			return $this->db->query("SELECT d.id,d.territory_name,c.id as town_id,c.town_name
					FROM `m_town_territory_link` a
					LEFT JOIN `pnh_m_territory_info` d ON d.id=a.territory_id
					LEFT JOIN `pnh_towns`c ON c.territory_id=d.id
					WHERE a.is_active=1 AND  a.employee_id  = $emp_id
					GROUP BY town_name
					ORDER BY territory_name,town_name")->result_array();
		}else
		{
			return $this->db->query("SELECT d.id,d.territory_name,c.id as town_id,c.town_name
					FROM `m_town_territory_link` a
					LEFT JOIN `pnh_m_territory_info` d ON d.id=a.territory_id
					LEFT JOIN `pnh_towns`c ON c.id=a.town_id
					WHERE a.is_active=1 AND town_id <> 0 AND a.employee_id = $emp_id
					ORDER BY territory_name,town_name")->result_array();
			}
					 
	
			}

		function load_territoriesbyemp_id($emp_id,$role_id)
		{
	
			if($role_id<=2)
			{
				return $territory_list=$this->db->query("SELECT id,territory_name
											FROM pnh_m_territory_info 
											order by territory_name
											")->result_array();
	
			}
			if($role_id > 2)
			{
				return $territory_list=$this->db->query("SELECT territory_id as id,b.territory_name
											 FROM m_town_territory_link
											 JOIN pnh_m_territory_info b ON b.id=territory_id
											 WHERE employee_id=?
											 GROUP BY territory_id
											 order by territory_name
											 ",$emp_id)->result_array();
			}
	
		}
		function get_subordinates($obj,$role_id,$emp_id)
		{
			$user=$this->auth_pnh_employee();

			$sql="SELECT a.employee_id,a.job_title AS role_id,a.name,c.employee_id AS sub_emp_id,b.parent_emp_id AS sup_emp_id,c.job_title AS sub_role_id,c.name AS sub_emp_name
					FROM m_employee_info a
					JOIN `m_employee_rolelink` b ON a.employee_id = b.parent_emp_id  AND b.is_active = 1
					LEFT JOIN m_employee_info c ON c.employee_id = b.employee_id
					WHERE  a.job_title = ? AND a.employee_id = ? and a.is_suspended=0";

		$res = $obj->db->query($sql,array($role_id,$emp_id));
		$temp=array();
			if($res->num_rows())
			{
				$sub_roles_data = $res->result_array();
				foreach($sub_roles_data as $sub_roles_det)
				{
				array_push($temp,$sub_roles_det['sub_emp_id']);
				$sub_temp=$this->get_subordinates($obj,$sub_roles_det['sub_role_id'],$sub_roles_det['sub_emp_id']);
				$temp=array_merge($temp,$sub_temp);
				$temp=array_filter($temp);
				}
			}
			return $temp;

		}
		
		function get_assignedempid($town_id)
		{
			error_reporting(E_ALL);
			ini_set('display_errors',1);

			$user=$this->auth_pnh_employee();

			$terr_id = $this->db->query("select territory_id from pnh_towns where id = ? ",$town_id)->row()->territory_id;

			$tm_info = @$this->db->query("select a.employee_id,name,'TM' as role from m_employee_info a join m_town_territory_link b on a.employee_id = b.employee_id where job_title = 4 and b.territory_id = ? and b.town_id = 0 and b.is_active = 1 and is_suspended=0 ",$terr_id)->row_array();

			$execu_info = @$this->db->query("select a.employee_id,name,'BE' as role from m_employee_info a join m_town_territory_link b on a.employee_id = b.employee_id where job_title = 5 and b.territory_id = ? and b.town_id = ? and b.is_active = 1 and is_suspended=0  ",array($terr_id,$town_id))->result_array();
			
			$fc_info = $this->db->query("select a.employee_id,name,'FC' as role from m_employee_info a join m_town_territory_link b on a.employee_id = b.employee_id where job_title = 5 and job_title2=6 and b.territory_id = ? and b.town_id = 0 and b.is_active = 1 and is_suspended=0  ",$terr_id)->row_array();
			
			$d_info = $this->db->query("select a.employee_id,name,'D' as role from m_employee_info a join m_town_territory_link b on a.employee_id = b.employee_id where job_title = 5 and job_title2=7 and b.territory_id = ? and b.town_id = 0 and b.is_active = 1 and is_suspended=0 ",$terr_id)->row_array();
			
			$tmp=array();
			if($tm_info)
				$tmp[]=array('employee_id'=>$tm_info['employee_id'],'name'=>$tm_info['name'].' ('.$tm_info['role'].')');
			
			if($execu_info)
				foreach($execu_info as $execu)
				{
					$tmp[]=array('employee_id'=>$execu['employee_id'],'name'=>$execu['name'].' ('.$execu['role'].')');
				}
				
			if($fc_info)
				$tmp[]=array('employee_id'=>$fc_info['employee_id'],'name'=>$fc_info['name'].' ('.$fc_info['role'].')');
			
			if($d_info)
				$tmp[]=array('employee_id'=>$d_info['employee_id'],'name'=>$d_info['name'].' ('.$d_info['role'].')');

			$output = array();
			if(count($tmp))
			{
				$output['assigned_empid']=$tmp;
			}else
			{
				$output['status']="error";
				$output['message']='No Bussiness Executive found';
			}
			echo json_encode($output);
		}

		function get_franchisebytwn_id($twn_id=0)
		{
			$user=$this->auth_pnh_employee();
			if(!$twn_id)
			{
				$twn_id=$this->input->post('townid');
				if(is_array($twn_id))
					$twn_id=implode(',',$twn_id);
				if(!$twn_id)
					$twn_id=0;
			}
			$franchise_list = $this->db->query("SELECT franchise_id,franchise_name
					FROM `pnh_m_franchise_info`
					WHERE is_suspended=0 and town_id in ($twn_id) order by franchise_name ");
			$output=array();
			if($franchise_list ->num_rows())
			{
				$output['franchise_list'] = $franchise_list->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']='errorr';
				$output['message']='No franchise Found';
			}
			echo json_encode($output);
		}
		
		function get_franchisebyterr_id($terr_id=0)
		{
			$user=$this->auth_pnh_employee();
			
			if(!$terr_id)
			{
				$terr_id=$this->input->post("territoryid");
				if(is_array($terr_id))
					$terr_id=implode(',',$terr_id);
				if(!$terr_id)
					$terr_id=0;
			}
			
			$franchise_list = $this->db->query("SELECT franchise_id,franchise_name
								FROM `pnh_m_franchise_info`
								WHERE is_suspended=0 and territory_id in ($terr_id)");
			$output=array();
			if($franchise_list ->num_rows())
			{
				$output['franchise_list'] = $franchise_list->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']='errorr';
				$output['message']='No franchise Found';
			}
			echo json_encode($output);
		}
		
	

		function showtwn_lnkterr($terry_id=0)
		{
			$user=$this->auth_pnh_employee();
			if(!$terry_id)
			{
				$terry_id=$this->input->post("territoryid");
				if(is_array($terry_id))
					$terry_id=implode(',',$terry_id);
				if(!$terry_id)
					$terry_id=0;
			}
			
			$town_linkedtoterry=$this->db->query("SELECT town_name,id FROM pnh_towns WHERE territory_id in ($terry_id)");
			$output=array();
			if($town_linkedtoterry->num_rows())
			{
				$output['town_linkedtoterry']=$town_linkedtoterry->result_array();
				$output['status']="Success";
			}
			else
			{
				$output['status']="errorr";
				$output['message']='No Towns found For the Territory';
			}
			echo json_encode($output);
		}

		function taskdet_bytwnid($town_id=9999999999)
		{
			$user=$this->auth_pnh_employee();
			$town_id =(!is_numeric($town_id))?9999999999:$town_id;
     		$ttl_tsk_res=$this->db->query('select count(*) as total from pnh_m_task_info where asgnd_town_id=?',$town_id);
	     	$output=array();
	     	if($ttl_tsk_res->num_rows())
	     	{
		     	$output['task']=$ttl_tsk_res->row_array();
		     	$output['status']="success";
	     	}
	     	else
	     	{
	     		$output['status']='error';
	     		$output['message']='No data Found';
			}
			echo json_encode($output);
		}
		
		
		function pendingtaskdet_bytwnid($town_id=9999999999)
		{
			$user=$this->auth_pnh_employee();
			$town_id =(!is_numeric($town_id))?9999999999:$town_id;
     		$pending_tsk_res=$this->db->query('select count(*) as pending from pnh_m_task_info where task_status=1 and is_active=1 and asgnd_town_id=?',$town_id);
			$output=array();
			if($pending_tsk_res->num_rows())
			{
				$output['task']=$pending_tsk_res->row_array();
				$output['status']="success";
			}
			else
			{
				$output['status']='error';
				$output['message']='No data Found';
			}
			echo json_encode($output);
		}
					 
					
		function get_fran_dailysales()
		{
			$user=$this->auth_pnh_employee();
			$output = array();
			$fids = $this->input->post('fids');
			if($fids)
			{
				$sql = "SELECT ifnull(b.franchise_id,0) as franchise_id,b.franchise_name,ifnull(sum(amount),0) as avg_sales
								FROM king_transactions a
								JOIN pnh_m_franchise_info b ON a.franchise_id = b.franchise_id
								WHERE is_pnh = 1 AND a.franchise_id IN ($fids) 
								and FROM_UNIXTIME(init)>=(CURDATE()-INTERVAL 7 DAY) ";
				$res = $this->db->query($sql);
				if($res->num_rows())
				{
					$output['data'] = $res->result_array();
					$output['data'][0]['avg_mon_sales'] = $this->db->query("select ifnull(round(sum(sales)/count(*),2),0) as avg_sales 
																	from (
																select concat(month(from_unixtime(init)),year(from_unixtime(init))) as mnth,sum(i_orgprice-(i_coup_discount+i_discount)) as sales 
																	from king_transactions a
																	join king_orders b on a.transid = b.transid and a.status != 3  
																	where franchise_id in ($fids)  
																	group by mnth 
																		) as g 
																where 1")->row()->avg_sales;
				}else
				{
					$output['status'] = 'error';
				}
			}
			else
			{
				$output['status'] = 'error';
			}
			echo json_encode($output);
		}
		
		function get_twn_currentbalance($town_id)
		{
			$user=$this->auth_pnh_employee();
			$town_id =(!is_numeric($town_id))?9999999999:$town_id;
			$output=array();
			//$town_id=$this->input->post('assigned_town');
			
				$current_balance=$this->db->query("SELECT SUM(current_balance) AS total_balance
						FROM `pnh_m_franchise_info`
						WHERE town_id=? AND FROM_UNIXTIME(created_on) <=NOW()",$town_id);
				
				if($current_balance->num_rows())
				{
					$output['balance']=$current_balance->row_array();
					$output['status']='Success';
				}
		
			else
		 	{
			 	$output['status']='error';
			 	$output['message']='No data Found';
			}
		echo json_encode($output);
		}
//------------------------------------------------END OF CALANDER-----------------------------------------------------------------------------	
/*	function jx_update_deltedtask()
	{
		$id=$this->input->post('task_id');
		$sql="update pnh_m_task_info set is_active=0 where id=?";
		$task_active_status=$this->db->query($sql,$id);
		$output=array();
		if($this->db->affected_rows()==1)
		{
			$output['task_active_status']=$task_active_status;
			$output['status']='success';
		}else{
			$output['status'] = 'error';
		}
		echo json_encode($output);
	}*/
	
//------------------------------------------ROUTES------------------------------------------------------------------------------	
	function manage_routes()
	{
		$routes=$this->db->query("select id,route_name from pnh_routes where is_active=1 order by route_name asc")->result_array();
		$data['routes']=$routes;
		$data['page']="manage_routes";
		$this->load->view("admin",$data);
	
	}
	
	function view_routes($route_id='')
	{
		$route_linked_twns=$this->erpm->route_linkd_twns($route_id);
		$data['route_linked_twns']=$route_linked_twns;
		$data['page']="pnh_towns";
		$this->load->view("admin",$data);
	}
	
	function process_routes()
	{
		$data['page']="manage_routes";
		$this->load->library('form_validation');
		$this->form_validation->set_rules('route_name','Name','required');
		$this->form_validation->set_rules('territory','Territory','required');
		if($this->form_validation->run()==true)
		{
				
			$route_name=$this->input->post('route_name');
			$linked_terry_ids=$this->input->post('territory');
			if($route_name)
				if($linked_terry_ids)
				foreach($linked_terry_ids as $linked_terry)
				{
					$sql="update pnh_towns set route_id='.$route_id.' where town_id='.town_id.'";
					$route_link = $this->db->query($sql,array($route_name,$linked_terry));
					$this->erpm->flash_msg("Employee Details Updated");
					redirect('admin/list_employee');
				}
		}else
			return false;
	}
	
	/**
	 * function to assign routes
	 */
	function jx_add_route()
	{
		$route_name=$this->input->post('route_name');
		$town=$this->input->post('towns');
			$t=0;
			$sql="insert into pnh_routes(route_name,is_active,created_on)values(?,1,now())";
			$this->db->query($sql,$route_name);
			$route_id=$this->db->insert_id();
			foreach($town as $twn)
			{
				
				$sql="update pnh_towns set route_id=? where id=?";
				$this->db->query($sql,array($route_id,$twn));
				$t+=$this->db->affected_rows();
		    }
		$output=array();
		if($t)
		{
			$output['status']="success";
		}
	  	else
	  	{
			$output['status']="error";
		}
		echo json_encode($output);
	}
	
	function jx_load_routedet()
	{
		$id = $this->input->post('route_id');
		$routedet_res=$this->db->query("SELECT town_name,b.route_name,route_id 
										FROM pnh_towns a
										JOIN pnh_routes b ON b.id=a.route_id
										where a.route_id = ? " ,array($id));
		$output = array();
		if($routedet_res->num_rows())
		{
			$output['route']=$routedet_res->result_array();
			$output['status']='success';
		}
		else
		{
			$output['status']='error';
		}
		echo json_encode($output);
	}
	
	function jx_upd_route()
	{
		$route_id=$this->input->post('route_id');
		$route_name=$this->input->post('route_name');
		$town=$this->input->post('towns');
		$t=0;
		$sql="update pnh_routes set route_name=? where id= ?";
		$this->db->query($sql,array($route_name,$route_id));
		$route_id=$this->db->insert_id();
		foreach($town as $twn)
		{
			$sql="update pnh_towns set route_id=? where id=?";
			$this->db->query($sql,array($route_id,$twn));
			$t+=$this->db->affected_rows();
		}
		$output=array();
		if($t)
		{
			$output['status']="success";
		}
		else
		{
			$output['status']="error";
		}
		echo json_encode($output);
	}
	
//-----------------------------------------------END OF ROUTES-------------------------------------------------------------------------------	
	

//--------------------------------------------------------------------------------------------------------------------------     
  /*   function get_fran_currentbalance()
     {
     	$user=$this->auth_pnh_employee();
     	$output=array();
     	
     	$fids=$this->input->post('fids');
     	if($fids){
     		$sql="select franchise_id,franchise_name,current_balance from pnh_m_franchise_info where franchise_id in ($fids) ";
     		$res=$this->db->query($sql);
     		if($res->num_rows())
     		
     			$output['data'] = $res->result_array();
     			$output['status']='success';
     		}
     		else
     		{
     			$output['status']='error';
     			$output['message']='No Data Found';
     		}
     	echo json_encode($output);
     }*/
     
//---------------------------------------------TASK PRINT---------------------------------------------------------------- 
     function task_print($employee_id='0',$start_date='0')
     {
     	$user=$this->auth(PNH_PRINT_EMPLOYEE_TASKS);
     	$end_date=date('Y-m-d',strtotime($start_date)+6*24*60*60);
     	$task_details=$this->db->query("SELECT a.id,b.town_name,c.territory_name,a.assigned_to AS emp_id,d.name AS assigned_toname,f.role_name,e.name AS assigned_byname,a.task_type,DATE(a.on_date) AS on_date,a.due_date,a.task,a.ref_no
		FROM pnh_m_task_info a
		JOIN pnh_towns b ON b.id=a.asgnd_town_id
		JOIN pnh_m_territory_info c ON c.id=b.territory_id
		JOIN m_employee_info d ON d.employee_id = a.assigned_to
		JOIN m_employee_info e ON e.employee_id = a.assigned_by
		JOIN m_employee_roles f ON f.role_id=d.job_title
		WHERE a.assigned_to = ? AND  a.on_date BETWEEN ? AND ? and a.is_active = 1 and a.task_status=1 and d.is_suspended=0 ",array($employee_id,$start_date,$end_date));
     		if($task_details->num_rows())
	     	{
	     		
	     	}
	     	else 
	     	{
	     		
	     		return false;
	     	}
	     	//print_r($task_details);
	     	
	     	$week_dts=array();
	     	$startdt_ts=strtotime($start_date);
	     	for($i=0;$i<7;$i++)
	     	{
	     		$dt = date('Y-m-d',$startdt_ts +($i*24*60*60));
	  		 	if(!isset($week_dts[$dt]))
	     			$week_dts[$dt]=array();
	     	}
	     	
	     	$emp_list = array();
	     	
	     	foreach($task_details->result_array() as $task_det)
	     	{
	     		$tsk_ondt =  $task_det['on_date'];
	     		$tsk_duedt =  $task_det['due_date'];
	     		$tsk_empid = $task_det['emp_id']; 
	     		$employee_name=$task_det['assigned_toname'];
	     		$territory_name=$task_det['territory_name'];
	     		$role_name=$task_det['role_name'];
	     		$task_refno=$task_det['ref_no'];
	     		
	     		$d_rem = (strtotime($tsk_duedt)-strtotime($tsk_ondt))/(24*60*60);
	     		for($k=0;$k<=$d_rem;$k++)
	     		{
	     			$tsk_schdt = date('Y-m-d',strtotime($tsk_ondt)+($k*24*60*60));
	     			if(!isset($week_dts[$tsk_schdt][$tsk_empid]))
	     				$week_dts[$tsk_schdt][$tsk_empid] = array();
	     			
	     			array_push($week_dts[$tsk_schdt][$tsk_empid],$task_det);
	     			$emp_list[$tsk_empid]=$employee_name ;
	     			 
	     		}
	     	 		
	     	}
	   	$data['page']="task_print";
	   	$data['week_dts']=$week_dts;
	   	$data['emp_list']=$emp_list;
	   	$data['tsk_ondt']=$tsk_ondt;
	   	$data['start_date']=$start_date;
	   	$data['end_date']=$end_date;
	   	$data['tsk_empid']=$tsk_empid;
	   	$data['territory_name']=$territory_name;
	   	$data['role_name']=$role_name;
	   	$data['assigned_toname']=$employee_name;
	   	$data['task_refno']=$task_refno;
	   	

     	$this->load->view("admin/body/task_print",$data);
     }
//----------------------------------------------------END OF TASK PRINT----------------------------------------------------------------------     
     /**
      * function load employees by terr or town id 
      */
     function jx_getpnhemployees()
     {
     	$terr_id = $this->input->post('terr_id')*1;
     	$twn_id = $this->input->post('twn_id')*1;
     	
     	$cond = '';
     	if($terr_id)
     		$cond .= " AND territory_id = ".$terr_id;
     	
     	 
     	if($twn_id)
     		$cond .= " AND ( town_id = ".$twn_id." or town_id = 0 ) " ;
     	 
     	
     	
     /* 	$sql = "SELECT b.employee_id,b.name AS employee_name,b.job_title,c.short_frm AS role_name
						FROM `m_town_territory_link` a 
						JOIN m_employee_info b ON a.employee_id = b.employee_id 
						JOIN m_employee_roles c ON c.role_id=b.job_title
						WHERE b.job_title > 2 AND a.is_active = 1 $cond
						GROUP BY b.employee_id   
						ORDER BY employee_name ASC "; */
     	
	     	$sql="SELECT b.employee_id,b.name AS employee_name,b.job_title,c.short_frm AS role_name,d.short_frm
					FROM `m_town_territory_link` a 
					JOIN m_employee_info b ON a.employee_id = b.employee_id 
					JOIN  m_employee_roles c ON c.role_id=b.job_title
					LEFT JOIN m_employee_roles d ON d.role_id=b.job_title2
					WHERE b.job_title > 2 AND a.is_active = 1 and b.is_suspended=0 $cond
					GROUP BY b.employee_id   
					ORDER BY employee_name ASC";
	     	
    	$res = $this->db->query($sql);
     	$output = array();
     	if($res->num_rows())
     	{
     		$output['emp_list'] = $res->result_array();
     		$output['status'] = 'success';
     	}
     	else
     	{
     		$output['status'] = 'error';
     	}
     	echo json_encode($output);
	 }
	 
	function process_batchorderinvoice()
	{
		$user = $this->auth();
		
		$pinv_list = explode(',',$this->input->post('pinv_nos'));
		$bid = $batch_id = $this->input->post('batch_id');
		
		$grpno = $this->db->query("select max(grpno) as grpno from t_bulkordersinvoice_log ")->row()->grpno+1;
		$ttl_processed = 0;
		foreach ($pinv_list as $p_invoice)
		{
			
			$proforma_det = $this->db->query("select order_id,invoice_status,packed,shipped from proforma_invoices a join shipment_batch_process_invoice_link b on a.p_invoice_no = b.p_invoice_no where a.p_invoice_no = ? ",$p_invoice)->result_array();
			if($proforma_det[0]['packed'] == 1)
				continue ;
			 
			if($this->db->query("select count(*) as t from t_bulkordersinvoice_log where p_invno = ? ",$p_invoice)->row()->t)
				continue;	
			 
					
			$p_oids = array();
			foreach($proforma_det as $p_det)
			{
				array_push($p_oids,$p_det['order_id']);
			}
			
			$this->db->query("insert into t_bulkordersinvoice_log (batch_id,grpno,p_invno,invno,created_on) values(?,?,?,0,now())",array($batch_id,$grpno,$p_invoice));
			$log_invid = $this->db->insert_id();
			
			$inv=$this->erpm->do_invoice($p_oids);
			$invoice_no = $inv[0];
			$ttl_processed++;
			 
			
			$this->db->query("update shipment_batch_process_invoice_link set invoice_no=$invoice_no,packed=1,packed_on=now(),packed_by={$user['userid']} where p_invoice_no=?",$p_invoice);
			
			$this->db->query("update t_bulkordersinvoice_log set invno=? where p_invno=? and id=? limit 1",array($invoice_no,$p_invoice,$log_invid));
				
		} 
		
		if($this->db->query("select count(1) as l from shipment_batch_process_invoice_link where batch_id=?",$bid)->row()->l<=$this->db->query("select count(1) as l from shipment_batch_process_invoice_link where packed=1 and batch_id=$bid")->row()->l+$this->db->query("select count(1) as l from shipment_batch_process_invoice_link bi join proforma_invoices i on i.p_invoice_no=bi.p_invoice_no where bi.batch_id=$bid and bi.packed=0 and i.invoice_status=0")->row()->l)
			$this->db->query("update shipment_batch_process set status=2 where batch_id=? limit 1",$bid);
		else
			$this->db->query("update shipment_batch_process set status=1 where batch_id=? limit 1",$bid);

		echo json_encode(array('grpno'=>$grpno,'batch_id'=>$batch_id,'processed'=>$ttl_processed));	
			
	}
	
	function jx_loadbulkinvoicelog($bid=0)
	{
		$output = array();
		$sql = "select grpno,order_id,count(*) as pinv_ttl,created_on 
						from t_bulkordersinvoice_log a
						join proforma_invoices b on a.p_invno = b.p_invoice_no 
						where batch_id = ?  
						group by grpno order by created_on desc ";
		$res = $this->db->query($sql,$bid);
		$output['ttl'] = $res->num_rows();
		if($res->num_rows())
		{
			$rowdata = array();
			foreach ($res->result_array() as $row)
			{
				$row['product_name'] = $this->db->query("(select product_name  
													from king_orders a
													join king_dealitems b on a.itemid = b.id
													join m_product_deal_link c on b.id = c.itemid 
													join m_product_info d on d.product_id = c.product_id 
												where a.id = ? )
												union
												(
												select product_name  
													from king_orders a
													join products_group_orders b on a.id = b.order_id
													join m_product_info c on c.product_id = b.product_id 
												where a.id = ?	
												)",array($row['order_id'],$row['order_id']))->row()->product_name;
				$rowdata[] = $row; 
			}
			$output['logdata'] = $rowdata;
		}
		echo json_encode($output);
	}
	
	function print_bulkorderinvs($grpno)
	{
		error_reporting(0);
		ini_set('display_errors',0);
		ini_set('memory_limit','512M');
		$this->erpm->auth();
		if($grpno)
		{
			echo '<div align="center" class="hideinprint"><input type="button" value="Print" onclick="window.print()" ></div>';
			$inv_list_res = $this->db->query("select invno,c.quantity as oqty,g.courier_name 
												from t_bulkordersinvoice_log a
												join king_invoice b on a.invno = b.invoice_no
												join king_orders c on b.order_id = c.id 
												join king_dealitems d on d.id = c.itemid
												join king_deals e on e.dealid = d.dealid 
												join king_transactions f on f.transid = c.transid 
												join partner_transaction_details g on g.transid = f.transid and g.order_no = f.partner_reference_no 
												where grpno = ?   
												order by g.courier_name,d.name,oqty desc ",$grpno);
			if($inv_list_res->num_rows())
			{
				$last_courier_printed = '';
				foreach($inv_list_res->result_array() as $grp_inv_det)
				{
					if($last_courier_printed == '')
						$last_courier_printed = $grp_inv_det['courier_name'];
						
					if($last_courier_printed != $grp_inv_det['courier_name'])
					{
						$last_courier_printed = $grp_inv_det['courier_name'];
						echo '<h2 style="page-break-before:always">&nbsp;</h2>';
					}
						
						
					$invoice_no = $grp_inv_det['invno'];
					
					$this->db->query('update king_invoice set total_prints = total_prints+1,last_printedon=now() where invoice_no = ? limit 1',$invoice_no);
					
					$sql="select item.nlc,item.phc,ordert.*,
							item.service_tax_cod,item.name,in.invoice_no,
							brand.name as brandname,
							in.mrp,in.tax as tax,
							in.discount,
							in.phc,in.nlc,
							in.service_tax,
							item.pnh_id
						from king_orders as ordert
						join king_dealitems as item on item.id=ordert.itemid 
						join king_deals as deal on deal.dealid=item.dealid 
						left join king_brands as brand on brand.id=deal.brandid 
						join king_invoice `in` on in.transid=ordert.transid and in.order_id=ordert.id  
						where in.invoice_no=? 
							  
						";
					$q=$this->db->query($sql,array($invoice_no));
					 
					$data['orders']=$orders=$q->result_array();
					$data['invoice_no']=$invoice_no;
					$data['trans']=$this->db->query("select * from king_transactions where transid=?",$orders[0]['transid'])->row_array();
					$data['inv_type'] = 1;
					echo '<div>';
					echo $this->load->view("body/invoice",$data,true);
					echo '</div>';
					
					
					
				}
				echo '<script>window.print()</script>';
				
			}
		}
	}
	
	


	function endy_orderd_sms_tofranchise()
	{
		$franchise_info_res=$this->db->query("SELECT franchise_id,franchise_name,current_balance,login_mobile1,login_mobile2 FROM pnh_m_franchise_info WHERE is_suspended=0");
			
		if($franchise_info_res ->num_rows())
		{
			foreach($franchise_details=$franchise_info_res->result_array() as $franchise_det)
			{
				$day_orderd_amt=$this->db->query("SELECT ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) AS amt
														FROM king_transactions a
														JOIN king_orders b ON a.transid = b.transid
														JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id
														WHERE a.franchise_id = ? AND FROM_UNIXTIME(init)=CURDATE()",$franchise_det['franchise_id'])->row()->amt;
					
				$franchise_name=$franchise_det['franchise_name'];
				$login_mobile1=$franchise_det['login_mobile1'];
				//$this->erpm->pnh_sendsms($login_mobile1,Congratulations!!!Dear Franchise $franchise_name, your placed order of the day -Rs.$day_orderd_amt.Happy Franchising');
				echo "$login_mobile1,Congratulations!!!Dear Franchise $franchise_name, your placed order of the day -Rs.$day_orderd_amt Happy Franchising".'</br>';
			}
		}
			
			
	}
	
	function sms_template()
	{
		$data['page']='sms_template';
		$this->load->view("admin",$data);
			
	}

	/**
	 * function to list all pnh invoice returns 
	 *
	 */
	function pnh_invoice_returns($date_from="0000-00-00",$date_to="0000-00-00",$srch=0,$franchise='0',$status="all",$pg=0)
	{
		$this->erpm->auth(PNH_INVOICE_RETURNS|PNH_ADD_INVOICE_RETURN);
		
		
		//filte codndiotions
		$cond='';
		$param=array();
		if(($date_from && $date_to) && ($date_from!='0000-00-00' && $date_to!='0000-00-00' ))
		{
			$cond.=" and date(returned_on) >= ? and date(returned_on) <= ?";
			$param[]=$date_from;
			$param[]=$date_to;
		}
		
		if($srch)
		{
			$cond.=" and (a.invoice_no like ? or f.imei_no = ? or f.barcode = ? ) ";
			$param[]='%'.$srch.'%';
			$param[]=$srch;
			$param[]=$srch;
		}
		
		
		if($franchise)
		{
			$cond.=" and e.franchise_id = ? ";
			$param[]=$franchise;
		}
		
		if($status != 'all')
		{
			$cond.=" and a.status = ? ";
			$param[]=$status;
		}
		
		$data['pnh_inv_returns_ttl'] = $this->erpm->get_pnh_invreturns_ttl($cond,$param);
		$data['pnh_inv_returns'] = $this->erpm->get_pnh_invreturns($pg,$cond,$param);
		
		$data['pg'] = $pg;
		$data['status'] = $status;
		
		$this->load->library('pagination');
		$config['base_url'] = site_url('/admin/pnh_invoice_returns/'.$date_from.'/'.$date_to.'/'.$srch.'/'.$franchise.'/'.$status);
		$config['total_rows'] = $data['pnh_inv_returns_ttl'];
		$config['per_page'] = 10;
		$config['uri_segment'] = 8;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		$data['page'] = 'pnh_invoice_returns';
		$this->load->view('admin',$data);
	}
	
	/**
	 * function to list products in service processed via pnh return 
	 */
	function pnh_return_service_log($pg=0)
	{
		$this->erpm->auth(PNH_INVOICE_RETURNS|PNH_ADD_INVOICE_RETURN);
		$data['prod_ttl'] = $this->erpm->get_pnh_product_service_ttl();
		$data['prod_list'] = $this->erpm->get_pnh_product_service($pg);
		
		$this->load->library('pagination');
		$config['base_url'] = site_url('/admin/pnh_return_service_log');
		$config['total_rows'] = $data['prod_ttl'];
		$config['per_page'] = 10;
		$config['uri_segment'] = 3;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		
		$data['page'] = 'pnh_return_service_log';
		$this->load->view('admin',$data);
	}
	
	/**
	 * function to add pnh invoice return
	 *
	 */
	function add_pnh_invoice_return()
	{
		$this->erpm->auth(PNH_INVOICE_RETURNS|PNH_ADD_INVOICE_RETURN);
		$data['page'] = 'add_pnh_invoice_return';
		$this->load->view('admin',$data);
	}
	
	/**
	 * function to get invoice order detials  
	 *
	 */
	function jx_getinvoiceorditems()
	{
		$this->erpm->auth(PNH_INVOICE_RETURNS|PNH_ADD_INVOICE_RETURN);
		$output = array();
		$invno = $this->input->post('scaninp');
		
		// check if the entered no is imeino
		$imei_inv_res = $this->db->query('select b.invoice_no from t_imei_no a join king_invoice b on a.order_id = b.order_id and b.invoice_status = 1 where imei_no = ? ',$invno);
		if($imei_inv_res->num_rows())
		{
			$invno = $imei_inv_res->row()->invoice_no;
		} 
		
		$fr_det = $this->db->query("select a.franchise_id,franchise_name from pnh_m_franchise_info a join king_transactions b on a.franchise_id = b.franchise_id join king_invoice c on c.transid = b.transid where c.invoice_no = ? ",$invno)->row();
		
		$output['franchise_name'] = $fr_det->franchise_name;
		$output['franchise_id'] = $fr_det->franchise_id;
		$output['invdet'] = $this->erpm->get_invoicedet_forreturn($invno);
		$output['return_cond'] = $this->config->item('return_cond');
		$output['return_reason'] = $this->config->item('return_reason');
		echo json_encode($output);
	}
	
	/**
	 * function to check if the entered barcode is valid for the product
	 */
	function jx_chkvalidprodbc()
	{
		$this->erpm->auth(PNH_INVOICE_RETURNS|PNH_ADD_INVOICE_RETURN);
		
		//get product and barcode from post 
		$prod_id = $this->input->post('pid');
		$prod_bc = $this->input->post('bc');
		
		$output = array();
		
		// check stock entry for barcode against product 
		$is_available = $this->db->query('select count(*) as t from t_stock_info where product_id = ? and product_barcode = ? ',array($prod_id,$prod_bc))->row()->t;
		if($is_available)
		{
			$output['status'] = 'success';
		}else
		{
			$output['status'] = 'error';
		}
		
		echo json_encode($output);
	}
	
	/**
	 * function to load transaction by order id  
	 */
	function load_transbyoid($order_id='')
	{
		$transid = $this->db->query("select transid from king_orders where id = ? ",$order_id)->row()->transid;
		redirect('admin/trans/'.$transid);
	}
	
	/**
	 * function to process add new return against invoice 
	 */
	function process_add_pnh_invoice_return()
	{
		$this->erpm->auth(PNH_INVOICE_RETURNS|PNH_ADD_INVOICE_RETURN);
		
		$prod_rcvd_pid = $this->input->post('prod_rcvd_pid');
		$prod_rcvd_qty = $this->input->post('prod_rcvd_qty');
		$prod_rcvd_pid_bc = $this->input->post('prod_rcvd_pid_bc');
		$prod_rcvd_pid_imei = $this->input->post('prod_rcvd_pid_imei');
		$prod_rcvd_cond = $this->input->post('prod_rcvd_cond');
		$prod_rcvd_remarks = $this->input->post('prod_rcvd_remarks');
		 
		
		$user = $this->erpm->auth(false,true);
		$return_by = $this->input->post('return_by');
		
		foreach ($prod_rcvd_pid as $invno=>$p_ord_list)
		{
			$this->db->query("insert into pnh_invoice_returns (invoice_no,return_by,handled_by,status,returned_on) values (?,?,?,?,now()) ",array($invno,$return_by,$user['userid'],0));
			$return_id = $this->db->insert_id();
		
			foreach($p_ord_list as $oid=>$proddet_list)
			{
				foreach ($proddet_list as $prod_pid_list)
				{
					foreach ($prod_pid_list as $i=>$pid)
					{
						$pqty = $prod_rcvd_qty[$invno][$oid][$pid][0];
						for($j=0;$j<$pqty;$j++)
						{
							$qty = 1;
							$p_bc = $prod_rcvd_pid_bc[$invno][$oid][$pid][$j];
							$p_imei_no = $prod_rcvd_pid_imei[$invno][$oid][$pid][$j];
							$pcond_type = $prod_rcvd_cond[$invno][$oid][$pid][$j];
							$p_remarks = $prod_rcvd_remarks[$invno][$oid][$pid][$j];
							
							$this->db->query("insert into pnh_invoice_returns_product_link (return_id,order_id,product_id,qty,barcode,imei_no,condition_type,status,created_on) values (?,?,?,?,?,?,?,0,now()) ",array($return_id,$oid,$pid,1,$p_bc,$p_imei_no,$pcond_type));
							$return_prod_id = $this->db->insert_id();
	
							$this->db->query("insert into pnh_invoice_returns_remarks (return_prod_id,remarks,parent_id,created_by,created_on) values(?,?,?,?,now()) ",array($return_prod_id,$p_remarks,0,$user['userid']));
							
							if($p_imei_no)
							{
								$this->db->query("update t_imei_no set is_returned = 1 where imei_no = ? and product_id = ? and is_returned = 0 ",array($p_imei_no,$pid));
							}
							
						}	
						 
					}
				}	
			}
			
			// update king invoice for returned status 
			$this->db->query("update king_invoice set is_returned = 1 where invoice_no = ? and invoice_status = 1 ",$invno);
			$this->session->set_flashdata("erp_pop_info","New product return added");
			
		}
		redirect("admin/pnh_invoice_returns",'refresh');
	}
	
	function view_pnh_invoice_return($return_id)
	{
		$this->erpm->auth(PNH_INVOICE_RETURNS|PNH_ADD_INVOICE_RETURN);
		
		$data['return_det'] = $this->erpm->get_pnh_invreturn($return_id);
		
		if(!$data['return_det'])
			show_error("Invalid Return in Reference");
		
		$data['page'] = 'view_pnh_invoice_return';
		$this->load->view('admin',$data);
	}
	
	function jx_upd_invretprodremark()
	{
		error_reporting(E_ALL);
		$user = $this->erpm->auth(PNH_INVOICE_RETURNS);
		
		$return_prod_status = $this->input->post('return_prod_status');
		$return_prod_id = $this->input->post('return_prod_id');
		$return_prod_remark = $this->input->post('return_prod_remark');
		$product_barcode = '';
		$output = array();
		//$status = $this->db->query("select status from pnh_invoice_returns_product_link where id = ? ",$return_prod_id)->row()->status;
		
		// process product stock movement.   
		if($return_prod_status == 2)
		{
			$rp_prod_det = @$this->db->query("select a.id as rp_id,invoice_no,order_id,product_id,barcode,imei_no from pnh_invoice_returns_product_link a join pnh_invoice_returns b on a.return_id = b.return_id where id = ? and a.status in (0,3) ",$return_prod_id)->row_array();
			
			if($rp_prod_det)
			{
			
				$is_imei_updated = 0;
				// check for valid imei no 
				$has_imei_no = $this->db->query("select count(*) as t from t_imei_no where product_id = ? ",$rp_prod_det['product_id'])->row()->t;  
				if($has_imei_no)
				{
					 
					$p_imei_res = $this->db->query("select * from t_imei_no where product_id = ? and imei_no = ? ",array($rp_prod_det['product_id'],$rp_prod_det['imei_no']));
					if($p_imei_res->num_rows())
					{
						 
							foreach($p_imei_res->result_array() as $inv_imei_det)
							{
								
								$imei = $inv_imei_det['imei_no'];
						 
								// check if imei entry is in log 
								$imei_log_res = $this->db->query('select * from t_imei_update_log where imei_no = ? and is_active = 1 ',array($imei));
								if(!$imei_log_res->num_rows())
								{
									$imei_det = $this->db->query('select * from t_imei_no where imei_no = ? ',array($imei))->row_array();
									$imei_upd_det = array();
									$imei_upd_det['imei_no'] = $imei_det['imei_no'];
									$imei_upd_det['product_id'] = $imei_det['product_id'];
									$imei_upd_det['stock_id'] = $imei_det['stock_id'];
									$imei_upd_det['grn_id'] = $imei_det['grn_id'];
									$imei_upd_det['alloted_order_id'] = $imei_det['order_id'];
							
									$imei_upd_det['is_active'] = 1;
									$imei_upd_det['logged_by'] = $user['userid'];
									$imei_upd_det['logged_on'] = date('Y-m-d H:i:s',$imei_det['created_on']);
									$this->db->insert('t_imei_update_log',$imei_upd_det);
								}
									
								$imei_upd_det = array();
								$imei_upd_det['return_id'] = $return_prod_id;
								$imei_upd_det['is_cancelled'] = 1;
								$imei_upd_det['cancelled_on'] = cur_datetime();
								$imei_upd_det['is_active'] = 0;
								$this->db->where(array('imei_no'=>$imei,'is_active'=>1));
								$this->db->update('t_imei_update_log',$imei_upd_det);
						
								$imei_det = $this->db->query('select * from t_imei_no where imei_no = ?  ',array($imei))->row_array();
								$imei_upd_det = array();
								$imei_upd_det['imei_no'] = $imei_det['imei_no'];
								$imei_upd_det['product_id'] = $imei_det['product_id'];
								$imei_upd_det['stock_id'] = $imei_det['stock_id'];
								$imei_upd_det['grn_id'] = $imei_det['grn_id'];
								$imei_upd_det['is_active'] = 1;
								$imei_upd_det['logged_by'] = $user['userid'];
								$imei_upd_det['logged_on'] = date('Y-m-d H:i:s');
								$this->db->insert('t_imei_update_log',$imei_upd_det);
							}
						
						// check for valid imei no for re-stocking
						// valid imei no and action required to restocking imei no 
						$this->db->query("update t_imei_no set status = 0,return_prod_id = ?,order_id = 0 where order_id = ? and product_id = ? and imei_no = ? ",array($rp_prod_det['rp_id'],$rp_prod_det['order_id'],$rp_prod_det['product_id'],$rp_prod_det['imei_no']));
						$is_imei_updated = 1;						
					}
				}
				
				// make an stock update for the product, check via barcode,product_id 
				$sql_stk = "select c.product_id,d.stock_id as stock_info_id,product_barcode,o.i_orgprice as mrp,if(d.product_barcode=?,1,0) as bc_match   
								from shipment_batch_process_invoice_link a 
								join proforma_invoices b on a.p_invoice_no = b.p_invoice_no 
								join t_reserved_batch_stock c on a.p_invoice_no = c.p_invoice_no 
								join king_orders o on o.id = b.order_id
								left join t_stock_info d on d.stock_id = c.stock_info_id 
								where a.invoice_no = ? and c.product_id = ? and c.status = 1 
								order by bc_match desc;
							";
				$stock_det_res = $this->db->query($sql_stk,array($rp_prod_det['barcode'],$rp_prod_det['invoice_no'],$rp_prod_det['product_id']));
				
				 
				
				$is_stocked = 0;
				$is_refunded_processed = 0;
				if($stock_det_res->num_rows())
				{
					$prod_stockinfo_id = $stock_det_res->row()->stock_info_id;
					$prod_mrp = $stock_det_res->row()->mrp; 
					
					// if no stock id found 
					if(!$prod_stockinfo_id)
					{
						// check if for mrp and barcode 
						$stk_chk_res = $this->db->query("select stock_id,product_barcode from t_stock_info where product_id = ? and mrp = ? ",array($rp_prod_det['product_id'],$prod_mrp));
						if($stk_chk_res->num_rows())
						{
							$prod_stockinfo_id = $stk_chk_res->row()->stock_id;
						}else
						{
									
							$prod_loc_det = @$this->db->query("select location_id,rack_bin_id 
																from m_rack_bin_brand_link a 
																join m_product_info b on a.brandid = b.brand_id 
																join m_rack_bin_info c on c.id = a.rack_bin_id and c.is_active = 1 
															where product_id = ? 
															order by a.id desc limit 1")->row_array();	
							
							//create one stock entry with product and mrp ;
							$stk_inp = array();
							$stk_inp[] = $rp_prod_det['product_id'];
							$stk_inp[] = $prod_mrp;
							$stk_inp[] = $rp_prod_det['barcode'];
							$stk_inp[] = $prod_loc_det['location_id'];
							$stk_inp[] = $prod_loc_det['rack_bin_id'];
							$stk_inp[] = 0;
							$stk_inp[] = $user['userid'];
							$stk_inp[] = cur_datetime();
							$this->db->query("insert into t_stock_info (product_id,mrp,product_barcode,location_id,rack_bin_id,available_qty,created_by,created_on) values (?,?,?,?,?,?,?,?) ",$stk_inp);
							
							$prod_stockinfo_id = $this->db->insert_id();
							
						}
						
					}
					
					
					 
					
					
					if($this->db->query("select count(*) as t from pnh_invoice_returns_product_link where id = ? and is_stocked = 0 ",$return_prod_id)->row()->t)
					{
					
						$this->db->query("update t_stock_info set available_qty = available_qty+1 where stock_id = ? ",array($prod_stockinfo_id));
						$is_stocked = 1;	
						
						// process refund for stocked product
						$order_det = $this->db->query("select * from king_orders where id = ? ",$rp_prod_det['order_id'])->row_array();
						
						$new_order_entry_added = 0;
						if($order_det['quantity'] > 1)
						{
							// gen new order no
							$return_prod_order_no = $this->erpm->p_genid(10);
							
							$new_order_det = $order_det;
							unset($new_order_det['sno']);
							
							$new_order_det['quantity'] = 1;
							$new_order_det['id'] = $return_prod_order_no;
							$new_order_det['status'] = 4;
							$new_order_det['actiontime'] = time();
							$this->db->insert('king_orders',$new_order_det);
							
							$this->db->query("update king_orders set quantity=quantity-1,actiontime=".time()." where id=? limit 1",$order_det['id']);
							$new_order_entry_added = 1;
						}else
						{
							$return_prod_order_no = $rp_prod_det['order_id'];
						}
						
						$this->db->query("update king_orders set status = 4,actiontime=unix_timestamp() where id = ? ",$return_prod_order_no);
						
						if($is_imei_updated)
						{
							// update returned product order id to new order, used for tracking imei processed by order
							$this->db->query("update t_imei_no set order_id = ? where is_returned = 1 and product_id = ? and imei_no = ? ",array($return_prod_order_no,$rp_prod_det['product_id'],$rp_prod_det['imei_no']));
						}
						
						$this->db->query("update pnh_invoice_returns_product_link set order_id = ? where id = ? ",array($return_prod_order_no,$rp_prod_det['rp_id']));
						
						$this->erpm->do_trans_changelog($order_det['transid'],"Order #".$return_prod_order_no." order Returned");
						
						
						if($new_order_entry_added)
						{
							$ord_invdet = $this->db->query("select * from king_invoice where order_id = ? order by id desc limit 1 ",$rp_prod_det['order_id'])->row_array();
							unset($ord_invdet['id']);
							$ord_invdet['order_id'] = $return_prod_order_no;
							$this->db->insert('king_invoice',$ord_invdet);
						}
						
						// check if all orders in invoice returned to change invoice status as cancelled   
						if($this->db->query("select count(*) as t from king_orders a join king_invoice b on a.id = b.order_id and b.invoice_status = 1 where b.invoice_no = ? and a.status not in (3,4) ",$rp_prod_det['invoice_no'])->row()->t == 0)
						{
							$this->db->query("update king_invoice set invoice_status = 0 where invoice_no = ? and invoice_status = 1  ",$rp_prod_det['invoice_no']);
						}
						
						$refund_amount = $order_det['i_orgprice']-($order_det['i_coup_discount']+$order_det['i_discount']);
						//$this->db->query("insert into t_refund_info(transid,amount,status,created_on,created_by) values(?,?,?,?,?)",array($order_det['transid'],$refund_amount,0,time(),$user['userid']));
						//$rid=$this->db->insert_id();
						
						//$this->db->query("insert into t_refund_order_item_link(refund_id,order_id,qty) values(?,?,?)",array($rid,$return_prod_order_no,1));
						$is_refunded_processed = 1;
						
						
						// update account statement for moved to stock product
						$inv_trans_det = $this->db->query("select a.order_id,franchise_id,a.transid,a.invoice_no,(b.i_orgprice-(b.i_discount+b.i_coup_discount)+credit_note_amt) as inv_amt 
									from king_invoice a
									join king_orders b on a.order_id = b.id 
									join pnh_invoice_returns_product_link c on c.order_id = b.id 
									join king_transactions e on e.transid = b.transid 
									where b.id = ? order by a.id desc limit 1 ",$return_prod_order_no)->row_array();
						 
						$arr = array($inv_trans_det['franchise_id'],1,$inv_trans_det['invoice_no'],$inv_trans_det['inv_amt'],'Product Return - Order:'.$return_prod_order_no,1,date('Y-m-d H:i:s'),$user['userid']);
						$this->db->query("insert into pnh_franchise_account_summary (is_returned,franchise_id,action_type,invoice_no,credit_amt,remarks,status,created_on,created_by) values(1,?,?,?,?,?,?,?,?)",$arr);	
						// auto debit credit note from statement.
						
						// cancel credit note raised against invoice.
						$credit_note_id = $this->db->query("select credit_note_id from king_invoice where invoice_no = ? ",$inv_trans_det['invoice_no'])->row()->credit_note_id;
						if($credit_note_id)
						{
							//cancel credit note 
							$this->db->query("update t_invoice_credit_notes set is_active = 0,modified_on=now() where id = ? ",$credit_note_id);
							$arr = array($inv_trans_det['franchise_id'],7,$inv_trans_det['invoice_no'],$this->db->query('select amount from t_invoice_credit_notes where id = ? ',$credit_note_id)->row()->amount,'credit note cancelled - '.$return_prod_order_no,1,date('Y-m-d H:i:s'),$user['userid']);
							$this->db->query("insert into pnh_franchise_account_summary (franchise_id,action_type,invoice_no,debit_amt,remarks,status,created_on,created_by) values(?,?,?,?,?,?,?,?)",$arr);
						}
					}
				}else
				{
					// Stock entry not found , check for stock by product or barcode and process stock update. 
					 
				}
				
				// check if stock is updated and need an update in log 
				if($is_stocked)
				{
					$this->db->query("update pnh_invoice_returns_product_link set status = 2,is_stocked = 1,is_refunded=1,stock_updated_on=now(),refunded_on=now() where id = ? and is_stocked = 0 ",$return_prod_id);
					$this->erpm->do_stock_log(1,1,$rp_prod_det['product_id'],false,false,false,false,-1,$return_prod_id);
				}
				
				$this->db->query("insert into pnh_invoice_returns_remarks (return_prod_id,product_status,remarks,created_by,created_on) values (?,?,?,?,now())",array($return_prod_id,$return_prod_status,$return_prod_remark,$user['userid']));
				$output['status'] = 'success';		
			}else
			{
				$output['status'] = 'error';	
			}
			
		}else if($return_prod_status == 1)
		{
			// Process product for servicing  
			$sent_to = $this->input->post('sent_to');
			$expected_on = $this->input->post('expected_on');
			
			
			$return_prod_remark = '<b>Sent to :</b>'.$this->input->post('sent_to').'<br />'.'<b>Expected On :</b>'.format_date($expected_on).'<br /><p>'.$return_prod_remark.'</p>';
			
			$inp = array($return_prod_id,date('Y-m-d'),$sent_to,$expected_on,$user['userid']);
			$this->db->query("insert into pnh_invoice_returns_product_service (return_prod_id,sent_on,sent_to,expected_dod,created_on,created_by) values (?,?,?,?,now(),?)",$inp); 
			
			$this->db->query("insert into pnh_invoice_returns_remarks (return_prod_id,product_status,remarks,created_by,created_on) values (?,?,?,?,now())",array($return_prod_id,$return_prod_status,$return_prod_remark,$user['userid']));
			$output['status'] = 'success';
			
			$this->db->query("update pnh_invoice_returns_product_link set status = 1 where id = ? ",$return_prod_id);
				
		}else if($return_prod_status == 4)
		{
			// Process product for servicing  
			
			// get product service request id and update return on and is serviced status. 
			
			$prod_serv_link_id = $this->db->query('select id from pnh_invoice_returns_product_service where return_prod_id=? order by id desc limit 1',$return_prod_id)->row()->id;
			
			$this->db->query("update pnh_invoice_returns_product_service set is_serviced = 1,service_return_on = now() where id = ? ",$prod_serv_link_id); 
			
			$this->db->query("insert into pnh_invoice_returns_remarks (return_prod_id,product_status,remarks,created_by,created_on) values (?,?,?,?,now())",array($return_prod_id,$return_prod_status,$return_prod_remark,$user['userid']));
			$output['status'] = 'success';
			
			$this->db->query("update pnh_invoice_returns_product_link set status = 3 where id = ? ",$return_prod_id);
				
		}else if($return_prod_status == 3)
		{
			// Process product for servicing  
			
			// get product service request id and update return on and is serviced status. 
			
			$return_prod_remark = '<b>Marked to Ready to Ship</b><br>'.$return_prod_remark;
			
			$this->db->query("insert into pnh_invoice_returns_remarks (return_prod_id,product_status,remarks,created_by,created_on) values (?,?,?,?,now())",array($return_prod_id,$return_prod_status,$return_prod_remark,$user['userid']));
			$output['status'] = 'success';
			
			$this->db->query("update pnh_invoice_returns_product_link set status=3,readytoship=1 where id = ? ",$return_prod_id);
			$prod_return_det_res = $this->db->query("select imei_no,product_id from pnh_invoice_returns_product_link where id = ? ",$return_prod_id);
			if($prod_return_det_res->num_rows())
			{
				$prod_return_det = $prod_return_det_res->row_array();
				if($prod_return_det['imei_no'])
				{
					$this->db->query("update t_imei_no set is_returned = 0 where imei_no = ? and product_id = ? limit 1 ",array($prod_return_det['imei_no'],$prod_return_det['product_id']));
				}
			}
		}else if($return_prod_status == 5)
		{
			$is_valid = 0;
			$prod_det = $this->db->query('select a.product_id,is_serial_required from pnh_invoice_returns_product_link a join m_product_info b on a.product_id = b.product_id where a.id = ? ',$return_prod_id)->row_array();
			if($prod_det['is_serial_required'])
			{
				$old_imei_no = $this->input->post('old_imei_no');
				$new_imei_no = $this->input->post('new_imei_no');
				
				//check if new imei provided is valid imei and in stock	
				$is_valid_imeino = $this->db->query("select count(*) as t from t_imei_no where imei_no = ? and product_id = ? and status = 0 and order_id = 0 ",array($new_imei_no,$prod_det['product_id']))->row()->t;
				
				if(!$is_valid_imeino)
				{
					$is_valid = 0;
					$output['status'] = 'error';
					$output['error'] = 'IMEINO not found';
				}else
				{
					$is_valid = 1;
					$output['status'] = 'success';	
				}
				
			}else
			{
				$is_valid = 1;
				$output['status'] = 'success';
			}
			
			if($is_valid)
			{
				$return_prod_remark = '<b>Part to Part replaced </b><br>'.$return_prod_remark;
				$this->db->query("insert into pnh_invoice_returns_remarks (return_prod_id,product_status,remarks,created_by,created_on) values (?,?,?,?,now())",array($return_prod_id,$return_prod_status,$return_prod_remark,$user['userid']));
			}
			
		}
				
		// update return status
		 
		$return_id = $this->db->query('select return_id from pnh_invoice_returns_product_link where id = ? ',$return_prod_id)->row()->return_id;
		
		$ttl_prods = $this->db->query("select count(*) as t from pnh_invoice_returns_product_link where return_id = ? ",$return_id)->row()->t;
		$ttl_prods_moved = $this->db->query("select count(*) as t from pnh_invoice_returns_product_link where return_id = ? and status = 2 and is_stocked = 1 ",$return_id)->row()->t;
		$ttl_prods_ready_shipped = $this->db->query("select count(*) as t from pnh_invoice_returns_product_link where return_id = ?  and status = 3 and readytoship = 1 ",$return_id)->row()->t;
		
		
		if($ttl_prods_moved+$ttl_prods_ready_shipped == 0 && $ttl_prods > 0)
		{
			// total ready to be shipped + total moved to stock = 0 in return is Pending
			$this->db->query("update pnh_invoice_returns set status = 0 where return_id = ? ",$return_id);	
		}else if($ttl_prods_moved+$ttl_prods_ready_shipped < $ttl_prods)
		{
			// total ready to be  + total moved to stock = total products in return is Updated
			$this->db->query("update pnh_invoice_returns set status = 1 where return_id = ? ",$return_id);	
		}else if($ttl_prods_moved+$ttl_prods_ready_shipped == $ttl_prods)
		{
			// total ready to be  + total moved to stock < total products in return is Closed
			$this->db->query("update pnh_invoice_returns set status = 2 where return_id = ? ",$return_id);	
		}
			
		echo json_encode($output);
	}

	/**
	 * function to load return page by return product id   
	 */
	function pnh_product_returnbyid($rp_id)
	{
		$this->erpm->auth(PNH_INVOICE_RETURNS);
		
		$return_id = $this->db->query('select return_id from pnh_invoice_returns_product_link where id = ? ',$rp_id)->row()->return_id;
		if(!$return_id)
			show_error("invalid return in reference");
		redirect('admin/view_pnh_invoice_return/'.$return_id);
	}
	
	/**
	 * function to print return receipt 
	 */
	function print_return_receipt($invoice_no='',$return_prod_id=0)
	{
		$this->erpm->auth(PNH_INVOICE_RETURNS);
		$this->load->plugin('barcode');
		$data['fran_det'] = $this->erpm->get_frandetbyinvno($invoice_no);
		$data['return_proddet'] = $this->erpm->get_returnproddetbyid($return_prod_id);
		
		// check if the product is ready to be shipped 
		if(!$data['return_proddet']['readytoship'])
		{
			show_error("Selection is not ready to ship");
		}
		
		// check if the product is already shipped 
		if($data['return_proddet']['is_shipped'])
		{
			show_error("Print Cannot be processed, Product already shipped");
		}
		
		
		$data['page'] = 'print_return_receipt';
		$this->load->view('admin/body/print_return_receipt',$data);
	}
	
	/**
	 * function to get return product list by franchise id 
	 */
	function jx_getreturnprodsbyfid($pg=0)
	{
		$this->erpm->auth();
		
		$fid = $this->input->post('fid');
		
		$return_srch_kwd = trim($this->input->post('return_srch_kwd'));
		$return_on = $this->input->post('return_on');
		$return_on_end = $this->input->post('return_on_end');
		
		$cond = '';
		if($return_srch_kwd)
			$cond .= '  and ( b.invoice_no = "'.$return_srch_kwd.'" or b.return_id = "'.$return_srch_kwd.'" or a.imei_no = "'.$return_srch_kwd.'" or a.barcode = "'.$return_srch_kwd.'" or a.order_id = "'.$return_srch_kwd.'" ) ';
			
		if($return_on && $return_on_end)
			$cond .= '  and ( date(b.returned_on) >= date("'.$return_on.'") and date(b.returned_on) <= date("'.$return_on_end.'") ) ';
		
		$total_fr_rplist = $this->db->query("select ifnull(sum(ttl),0) as ttl from (select 1 as ttl from pnh_invoice_returns_product_link a join pnh_invoice_returns b on a.return_id = b.return_id join king_invoice d on d.order_id = a.order_id join king_transactions e on e.transid = d.transid  where franchise_id = ? $cond group by a.id) as g ",$fid)->row()->ttl;
		
		//echo $this->db->last_query();
		
		
		$output = array();
		$output['total'] = $total_fr_rplist;
		
		$fran_returnprod_res = $this->db->query("select a.is_shipped,a.barcode,a.imei_no,b.return_by,a.order_id,b.invoice_no,a.return_id,a.id as return_prod_id,a.imei_no,a.barcode,qty,a.product_id,c.product_name,d.mrp-d.discount as price,condition_type,is_shipped,readytoship,is_refunded,refunded_on,a.status,unix_timestamp(a.created_on) as created_on   
						from pnh_invoice_returns_product_link a
						join pnh_invoice_returns b on a.return_id = b.return_id 
						join m_product_info c on c.product_id = a.product_id 
						join king_invoice d on a.order_id = d.order_id
						join king_transactions e on e.transid = d.transid 
						where e.franchise_id = ?  $cond  
						group by a.id  
						order by a.id desc
						limit $pg,10
						",$fid);
		if($fran_returnprod_res->num_rows())
		{
			$output['status'] = 'success';
			$output['return_cond'] = $this->config->item('return_cond');
			$output['return_process_cond'] = $this->config->item('return_process_cond');
			
			$fran_rplist = array();
			foreach($fran_returnprod_res->result_array() as $f_rp_det)
			{
				$rp_id = $f_rp_det['return_prod_id'];
				$f_rp_det['remarks'] = $this->db->query("select a.*,unix_timestamp(a.created_on) as created_on,b.username as remark_by from pnh_invoice_returns_remarks a join king_admin b on a.created_by = b.id where return_prod_id = ? order by a.id desc limit 1 ",$rp_id)->row_array();
				$fran_rplist[] = $f_rp_det;
			}
			
			$output['fran_rplist'] = $fran_rplist;
			
			$this->load->library('pagination');

			$config['base_url'] = site_url('admin/jx_getreturnprodsbyfid');
			$config['total_rows'] = $total_fr_rplist;
			$config['per_page'] = 10;
			$config['uri_segment'] = 3;
			
			$this->config->set_item('enable_query_strings',false);
			$this->pagination->initialize($config);
			$output['fran_rplist_pagi'] = $this->pagination->create_links();
			$this->config->set_item('enable_query_strings',true);
			
		}else
		{
			$output['fran_rplist'] = array();
			$output['status'] = 'success';
		}
		
		
		echo json_encode($output);
	}

	/**
	 * function to mark return product as packed for processing for shipping
	 */
	function pnh_markreturnpacked($return_prod_id=0)
	{
		$user = $this->erpm->auth(PNH_INVOICE_RETURNS);
		
		$rp_det_res = $this->db->query("select * from pnh_invoice_returns_product_link where id = ? ",$return_prod_id);
		
		if($rp_det_res->num_rows())
		{
			$rp_det = $rp_det_res->row_array();
			if($rp_det['is_packed'])
			{
				show_error("Return Product already marked as packed");
			}else
			{
				$this->db->query("update pnh_invoice_returns_product_link set is_packed = 1 where is_packed = 0 and id = ? and is_shipped = 0 ",$return_prod_id);
				
				// Reset Shipment Log for reshipping against invoice no 
				$inv_no = $this->db->query("select invoice_no from pnh_invoice_returns where return_id = ? ",$rp_det['return_id'])->row()->invoice_no;
				$this->db->query('update shipment_batch_process_invoice_link set is_returned=1,packed = 0,packed_on=0,shipped=0,shipped_on=0,outscanned=0,outscanned_on=0,inv_manifesto_id=0 where invoice_no = ? order by id desc limit 1',$inv_no);
				$this->session->set_flashdata("erp_pop_info","Return Product marked as packed");
				
				redirect('admin/view_pnh_invoice_return/'.$rp_det['return_id']);
			}
		}else
		{
			show_error("Invalid Return product in reference");
		}
	}
	
	function product_order_summary()
	{
		$this->load->model('erpmodel','erpm');
		$this->erpm->auth(DEAL_MANAGER_ROLE);
		$sql = "select catid,c.name as catname,brand_id,b.name as brandname,product_id,product_name,sum(ttl_req_qty) as ttl_req_qty,mrp 
			from 
			(
				(
					select h.catid,c.brand_id,c.product_id,trim(c.product_name) as product_name,sum(a.quantity*b.qty) as ttl_req_qty,c.mrp
					from king_orders a 
					join products_group_orders o on o.order_id = a.id
					join m_product_group_deal_link b on b.itemid = a.itemid
					join m_product_info c on c.product_id = o.product_id 
					join king_transactions e on e.transid = a.transid 
					join king_dealitems f on f.id = a.itemid
					join king_deals h on h.dealid = f.dealid 
					where a.status != 3 and e.init >= unix_timestamp(curdate())-90*24*60*60 
					group by product_id 
				)
				union(
					select h.catid,c.brand_id,c.product_id,trim(c.product_name) as product_name,sum(a.quantity*b.qty) as ttl_req_qty,c.mrp
					from king_orders a 
					join m_product_deal_link b on a.itemid = b.itemid  
					join m_product_info c on c.product_id = b.product_id 
					join king_transactions e on e.transid = a.transid 
					join king_dealitems f on f.id = a.itemid 
					join king_deals h on h.dealid = f.dealid 
					where a.status != 3 and e.init >= unix_timestamp(curdate())-90*24*60*60 
					group by product_id  
				)
			) as g 
			left join king_brands b on b.id = brand_id 
			left join king_categories c on c.id = catid 
			group by g.product_id 
			having ttl_req_qty > 0 
			order by g.product_name asc ";

		$res = $this->db->query($sql);
		if($res->num_rows())
		{
			$delimiter = ",";
			$newline = "\r\n";
			$filename = 'OrderSummaryByProduct_'.date('d_m_Y_h_i_a').'.csv';
			// send response headers to the browser
			header( 'Content-Type: text/csv' );
			header( 'Content-Disposition: attachment;filename='.$filename);
			$fp = fopen('php://output', 'w');
			$this->load->dbutil();
			echo $this->dbutil->csv_from_result($res, $delimiter, $newline);
			fclose($fp);	
		}else
		{
			show_error("no products found");
		}
		
	}

	
		
		function pnh_exsms_log($territory_id=0,$pg=0)
		{
			$this->erpm->auth();
			$data['page']='pnh_exsms_log';
			$data['pg']=$pg;
			$this->load->view("admin",$data);
		}
		
		/**
		 * Ajax function to load pnh sms log details by type and territory
		 * @param unknown_type $type
		 * @param unknown_type $terr_id
		 * @param unknown_type $pg
		 */
		function jx_getpnh_exsms_log($type='def',$terr_id='0',$emp_id='0',$pg=0)
		{
			$limit = 25;
			// check if territory is set
			$cond = $terr_id?' 1 and d.territory_id = "'.$terr_id.'"':'';
			
			$cond = $emp_id?' and a.emp_id = "'.$emp_id.'"':'';
			$cond1 = $emp_id?' and assigned_to = "'.$emp_id.'"':'';
			// compute total rows 
			$tbl_total_rows = 0;
			
			//prepare output table header
			$tbl_head = array();
			$tbl_data = array();
			
			// check for type 
			if($type=='paid')
			{
				$sql_total = "SELECT a.emp_id
							FROM pnh_executive_accounts_log a
							JOIN m_employee_info b ON b.employee_id=a.emp_id
							LEFT JOIN king_admin c ON c.id=a.updated_by
							WHERE a.type ='paid'  $cond 
							GROUP BY a.id
								";
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
				
				$sql = "SELECT a.sender,a.emp_id,a.id AS log_id,b.contact_no,a.msg,a.remarks,a.reciept_status,b.name,b.name AS employee_name,c.name AS updatedby_name,DATE_FORMAT(a.updated_on,'%d/%m/%y %h:%i %p') AS updated_on,DATE_FORMAT(a.logged_on,'%d/%m/%y %h:%i %p') AS logged_on,
							t.territory_name,e.town_name
							FROM pnh_executive_accounts_log a
							JOIN m_employee_info b ON b.employee_id=a.emp_id
							LEFT JOIN `m_town_territory_link` d ON d.employee_id=a.emp_id
							LEFT JOIN `pnh_m_territory_info`t ON t.id=d.territory_id
							LEFT JOIN pnh_towns e ON e.id=d.town_id
							LEFT JOIN king_admin c ON c.id=a.updated_by
							WHERE a.type='paid' $cond 
							GROUP BY a.id
							ORDER BY a.logged_on DESC
							limit $pg,$limit 
							";
				
				$log_sms_details_res=$this->db->query($sql);


			
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','sent_from'=>'Sent From','territory_name'=>'Territory','town_name'=>'Town','msg'=>'Message','loggged_on'=>'LoggedOn');
				
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'sent_from'=>$log_det['sender'],'territory_name'=>$log_det['territory_name'],'town_name'=>$log_det['town_name'],'msg'=>$log_det['msg'],'loggged_on'=>$log_det['logged_on']);
					}
				}
				
			}else if($type=='new')
			{
				$sql_total = "SELECT a.id AS log_id
								FROM pnh_executive_accounts_log a
								JOIN m_employee_info b ON b.employee_id=a.emp_id
								LEFT JOIN `m_town_territory_link` d ON d.employee_id=a.emp_id
								LEFT JOIN `pnh_m_territory_info`t ON t.id=d.territory_id
								LEFT JOIN pnh_towns e ON e.id=d.town_id
								LEFT JOIN king_admin c ON c.id=a.updated_by
								WHERE `type`='New'  $cond
								GROUP BY a.id	";
								
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
				
				$sql = "SELECT a.sender, a.emp_id,a.id AS log_id,b.contact_no,a.msg,a.remarks,a.reciept_status,b.name,b.name AS employee_name,c.name AS updatedby_name,DATE_FORMAT(a.updated_on,'%d/%m/%y %h:%i %p') AS updated_on,DATE_FORMAT(a.logged_on,'%d/%m/%y %h:%i %p') AS logged_on,
							t.territory_name,e.town_name
							FROM pnh_executive_accounts_log a
							JOIN m_employee_info b ON b.employee_id=a.emp_id
							LEFT JOIN king_admin c ON c.id=a.updated_by
							LEFT JOIN `m_town_territory_link` d ON d.employee_id=a.emp_id
							LEFT JOIN `pnh_m_territory_info`t ON t.id=d.territory_id
							LEFT JOIN pnh_towns e ON e.id=d.town_id
							WHERE `type`='New' $cond
							GROUP BY a.id
							ORDER BY a.logged_on DESC 
							limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
				
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','sent_from'=>'Sent From','territory_name'=>'Territory','town_name'=>'Town','msg'=>'Message','loggged_on'=>'LoggedOn');
				
				if($log_sms_details_res->num_rows())
				{
						foreach($log_sms_details_res->result_array() as $i=>$log_det)
						{
							$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'sent_from'=>$log_det['sender'],'msg'=>$log_det['msg'],'territory_name'=>$log_det['territory_name'],'town_name'=>$log_det['town_name'],'loggged_on'=>$log_det['logged_on']);
						}
				}
			}else if($type=='ship')
			{
				$sql_total = "SELECT a.id AS log_id
								FROM pnh_executive_accounts_log a
								JOIN m_employee_info b ON b.employee_id=a.emp_id
								LEFT JOIN `m_town_territory_link` d ON d.employee_id=a.emp_id
								LEFT JOIN `pnh_m_territory_info`t ON t.id=d.territory_id
								LEFT JOIN pnh_towns e ON e.id=d.town_id
								LEFT JOIN king_admin c ON c.id=a.updated_by
								WHERE `type`='ship'  $cond
								GROUP BY a.id	";
								
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
				
				$sql = "SELECT a.sender, a.emp_id,a.id AS log_id,b.contact_no,a.msg,a.remarks,a.reciept_status,b.name,b.name AS employee_name,c.name AS updatedby_name,DATE_FORMAT(a.updated_on,'%d/%m/%y %h:%i %p') AS updated_on,DATE_FORMAT(a.logged_on,'%d/%m/%y %h:%i %p') AS logged_on,
							t.territory_name,e.town_name
							FROM pnh_executive_accounts_log a
							JOIN m_employee_info b ON b.employee_id=a.emp_id
							LEFT JOIN king_admin c ON c.id=a.updated_by
							LEFT JOIN `m_town_territory_link` d ON d.employee_id=a.emp_id
							LEFT JOIN `pnh_m_territory_info`t ON t.id=d.territory_id
							LEFT JOIN pnh_towns e ON e.id=d.town_id
							WHERE `type`='ship' $cond
							GROUP BY a.id
							ORDER BY a.logged_on DESC 
							limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
				
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','sent_from'=>'Sent From','territory_name'=>'Territory','town_name'=>'Town','msg'=>'Message','loggged_on'=>'LoggedOn');
				
				if($log_sms_details_res->num_rows())
				{
						foreach($log_sms_details_res->result_array() as $i=>$log_det)
						{
							$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'sent_from'=>$log_det['sender'],'msg'=>$log_det['msg'],'territory_name'=>$log_det['territory_name'],'town_name'=>$log_det['town_name'],'loggged_on'=>$log_det['logged_on']);
						}
				}
			}
			
			else if($type=='existing')
			{
				$sql_total = "SELECT a.id AS log_id
									FROM pnh_executive_accounts_log a
									JOIN m_employee_info b ON b.employee_id=a.emp_id
									LEFT JOIN king_admin c ON c.id=a.updated_by
									WHERE `type`='New' $cond
									GROUP BY a.id
									";
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
				
				$sql = "SELECT a.sender,a.emp_id,a.id AS log_id,b.contact_no,a.msg,a.remarks,a.reciept_status,b.name,b.name AS employee_name,c.name AS updatedby_name,DATE_FORMAT(a.updated_on,'%d/%m/%y %h:%i %p') AS updated_on,DATE_FORMAT(a.logged_on,'%d/%m/%y %h:%i %p') AS logged_on,
																t.territory_name,e.town_name
																FROM pnh_executive_accounts_log a
																JOIN m_employee_info b ON b.employee_id=a.emp_id
																LEFT JOIN king_admin c ON c.id=a.updated_by
																LEFT JOIN `m_town_territory_link` d ON d.employee_id=a.emp_id
																LEFT JOIN `pnh_m_territory_info`t ON t.id=d.territory_id
																LEFT JOIN pnh_towns e ON e.id=d.town_id
																WHERE `type`='existing' $cond
																GROUP BY a.id
																ORDER BY a.logged_on DESC 
																limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
				
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','sent_from'=>'Sent From','territory_name'=>'Territory','town_name'=>'Town','msg'=>'Message','loggged_on'=>'LoggedOn');
				
				if($log_sms_details_res->num_rows())
				{
						foreach($log_sms_details_res->result_array() as $i=>$log_det)
						{
							$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'sent_from'=>$log_det['sender'],'territory_name'=>$log_det['territory_name'],'town_name'=>$log_det['town_name'],'msg'=>$log_det['msg'],'loggged_on'=>$log_det['logged_on']);
						}
				}
			}
			
			else if($type=='task')
			{
				$sql_total = "SELECT a.id AS log_id
				FROM pnh_task_remarks a
				JOIN pnh_m_task_info b ON b.ref_no=a.task_id
				JOIN  m_employee_info d ON d.employee_id=a.emp_id
				JOIN m_town_territory_link e ON e.employee_id=a.emp_id
				JOIN pnh_towns f ON f.id=b.asgnd_town_id
				WHERE 1 $cond
				GROUP BY a.id
				";
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
			
														$sql = "SELECT b.task_type,a.task_id AS ref_id,a.remarks,d.name AS logged_by,DATE_FORMAT(a.logged_on,'%d/%m/%y %h:%i %p') AS logged_on,d.contact_no,f.town_name,t.territory_name,b.id AS task_id,b.comments
														FROM pnh_task_remarks a
														JOIN pnh_m_task_info b ON b.ref_no=a.task_id
														JOIN m_employee_info d ON d.employee_id=a.emp_id
														JOIN m_town_territory_link e ON e.employee_id=a.emp_id
														left JOIN pnh_towns f ON f.id=b.asgnd_town_id
														JOIN pnh_m_territory_info t ON t.id=e.territory_id
														WHERE 1 $cond
														GROUP BY a.id
														ORDER BY a.logged_on DESC
														limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
			
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','msg'=>'Message','loggged_on'=>'LoggedOn');
			
				if($log_sms_details_res->num_rows())
				{
						foreach($log_sms_details_res->result_array() as $i=>$log_det)
						{
								$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'msg'=>$log_det['msg'],'loggged_on'=>$log_det['logged_on']);
						}
				}
			}
			
			else if($type=='delivered_invoicesms')
			{
				/*$sql_total = "SELECT invoice_no,b.franchise_name,c.name,
								FROM sms_invoice_log a
								JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid
								JOIN m_employee_info c ON c.employee_id=a.emp_id1
								WHERE `type`=1 $cond 
								ORDER BY a.logged_on DESC";
				
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
					
				$sql = "SELECT a.invoice_no,b.franchise_name,c.name,d.town_name,t.territory_name,DATE_FORMAT(a.logged_on,'%d/%m/%y %h:%i %p') AS logged_on,c.contact_no 
						FROM sms_invoice_log a
						JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid
						JOIN m_employee_info c ON c.employee_id=a.emp_id1
						LEFT JOIN pnh_towns d ON d.id=b.town_id
						JOIN pnh_m_territory_info t ON t.id=b.territory_id
						WHERE `type`=1 $cond
						GROUP BY a.id
						ORDER BY a.logged_on DESC
						limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);*/
				
				$sql="select a.emp_id,a.grp_msg as msg,a.created_on as logged_on,a.contact_no,b.name 
							from pnh_employee_grpsms_log a 
							join m_employee_info b on b.employee_id=a.emp_id 
							where b.job_title2=7 and a.type=10 ";
				if($emp_id)
					$sql.=' and a.emp_id=? ';
				
				$sql.=' order by logged_on desc ';
				
				$tbl_total_rows=$this->db->query($sql,$emp_id)->num_rows();
				
				$sql.=" limit $pg, $limit";
				
				$log_sms_details_res=$this->db->query($sql,$emp_id);
				
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','msg'=>'Message','loggged_on'=>'LoggedOn');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],ucwords($log_det['name'])),'msg'=>$log_det['msg'],'loggged_on'=>format_datetime($log_det['logged_on']));
					}
				}
		}
		
		else if($type=='returned_invoicesms')
			{
				/*$sql_total = "SELECT invoice_no,b.franchise_name,c.name,
								FROM sms_invoice_log a
								JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid
								JOIN m_employee_info c ON c.employee_id=a.emp_id1
								WHERE `type`=2 $cond 
								ORDER BY a.logged_on DESC";
				
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
					
				$sql = "SELECT invoice_no,b.franchise_name,c.name,d.town_name,t.territory_name,DATE_FORMAT(a.logged_on,'%d/%m/%y %h:%i %p') AS logged_on,c.contact_no 
						FROM sms_invoice_log a
						JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid
						JOIN m_employee_info c ON c.employee_id=a.emp_id1
						left JOIN pnh_towns d ON d.id=b.town_id
						JOIN pnh_m_territory_info t ON t.id=b.territory_id
						WHERE `type`=2  $cond 
						GROUP BY a.id
						ORDER BY a.logged_on DESC
						limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);*/
					
				
				$sql="select a.emp_id,a.contact_no,b.name,a.grp_msg as msg,a.contact_no,a.created_on as logged_on 
							from pnh_employee_grpsms_log a 
							join m_employee_info b on b.employee_id=a.emp_id 
						where a.type=11";
				
				if($emp_id)
					$sql.=' and a.emp_id=? ';
				
				$sql.=' order by logged_on desc ';
				
				$tbl_total_rows=$this->db->query($sql,$emp_id)->num_rows();
				
				$sql.=" limit $pg, $limit";
				
				$log_sms_details_res=$this->db->query($sql,$emp_id);
				
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','msg'=>'Message','loggged_on'=>'LoggedOn');
			
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
					$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'msg'=>$log_det['msg'],'loggged_on'=>$log_det['logged_on']);
					}
				}
			}
			
			else if($type=='call')
			{
				$sql_total = "SELECT a.franchise_id,a.msg
								FROM pnh_sms_log a 
								JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id 
								left JOIN pnh_towns d ON d.id=c.town_id 
								JOIN pnh_m_territory_info t ON t.id=c.territory_id 
								WHERE c.is_suspended=0 AND `type`='CALL' $cond
								ORDER BY a.created_on DESC ";
			
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
					
				$sql = "SELECT a.franchise_id,a.msg,DATE_FORMAT(FROM_UNIXTIME(a.created_on),'%d/%m/%y %h:%i %p') AS created_on,a.sender,c.franchise_name,d.town_name,t.territory_name 
														FROM pnh_sms_log a 
														JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id 
														left JOIN pnh_towns d ON d.id=c.town_id 
														JOIN pnh_m_territory_info t ON t.id=c.territory_id 
														WHERE c.is_suspended=0 AND `type`='CALL' $cond
														ORDER BY a.created_on DESC 
														limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
					
				$tbl_head = array('slno'=>'Slno','franchise_name'=>'Franchisee Name','territory_name'=>'Territory','town_name'=>'Town','msg'=>'Message','logged_on'=>'LoggedOn');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'franchise_name'=>anchor('admin/pnh_franchise/'.$log_det['franchise_id'],$log_det['franchise_name']),'territory_name'=>$log_det['territory_name'],'town_name'=>$log_det['town_name'],'msg'=>$log_det['msg'],'logged_on'=>$log_det['created_on']);
					}
				}
			}
			
			else if($type=='offer')
			{
				$sql_total = "SELECT a.franchise_id,a.msg
								FROM pnh_sms_log_sent a
								JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id
								LEFT JOIN pnh_towns d ON d.id=c.town_id
								JOIN pnh_m_territory_info t ON t.id=c.territory_id
								WHERE c.is_suspended=0 AND `type`='Offer'$cond
								ORDER BY a.sent_on DESC";
					
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
					
				$sql = "SELECT a.franchise_id,a.msg,DATE_FORMAT(FROM_UNIXTIME(a.sent_on),'%d/%m/%y %h:%i %p') AS created_on,c.franchise_name,d.town_name,t.territory_name
						FROM pnh_sms_log_sent a
						JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id
						LEFT JOIN pnh_towns d ON d.id=c.town_id
						JOIN pnh_m_territory_info t ON t.id=c.territory_id
						WHERE c.is_suspended=0 AND `type`='Offer' 
						ORDER BY a.sent_on DESC
						limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
					
				$tbl_head = array('slno'=>'Slno','franchise_name'=>'Franchisee Name','territory_name'=>'Territory','town_name'=>'Town','msg'=>'Message','sent_on'=>'Sent On');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'franchise_name'=>anchor('admin/pnh_franchise/'.$log_det['franchise_id'],$log_det['franchise_name']),'territory_name'=>$log_det['territory_name'],'town_name'=>$log_det['town_name'],'msg'=>$log_det['msg'],'sent_on'=>$log_det['created_on']);
					}
				}
			}
				
			
			elseif($type=='payment_collection')
			{
				$sql_total = "SELECT a.emp_id
								FROM pnh_employee_grpsms_log a
								JOIN m_employee_info b ON b.employee_id=a.emp_id
								WHERE `type`=1 $cond
								GROUP BY a.id
								";
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
				
				$sql = "SELECT a.contact_no,a.emp_id,b.name,grp_msg,DATE_FORMAT(a.created_on,'%d/%m/%Y %h:%i %p') AS sent_on,t.territory_name,replace(group_concat(e.town_name),',',' ') as town_name
						FROM pnh_employee_grpsms_log a
						JOIN m_employee_info b ON b.employee_id=a.emp_id
						LEFT JOIN `m_town_territory_link` d ON d.employee_id=a.emp_id
						LEFT JOIN `pnh_m_territory_info`t ON t.id=d.territory_id
						LEFT JOIN pnh_towns e ON e.id=d.town_id
						WHERE `type`=1 $cond
						group by a.id  
						order by a.id desc
						limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
				
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','sent_to'=>'Sent To','territory_name'=>'Territory','town_name'=>'Town','msg'=>'Message','loggged_on'=>'LoggedOn');
				
				if($log_sms_details_res->num_rows())
				{
				foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
					$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'sent_to'=>$log_det['contact_no'],'territory_name'=>$log_det['territory_name'],'town_name'=>$log_det['town_name'],'msg'=>$log_det['grp_msg'],'loggged_on'=>$log_det['sent_on']);
					}
				}
			
			}
			
			elseif($type=='task_remainder')
			{
				$sql_total = "SELECT a.emp_id
							FROM pnh_employee_grpsms_log a
							JOIN m_employee_info b ON b.employee_id=a.emp_id
							WHERE `type`=2 $cond
							GROUP BY a.id
				";
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
				
				$sql = "SELECT a.contact_no,a.emp_id,b.name,grp_msg,DATE_FORMAT(a.created_on,'%d/%m/%Y %h:%i %p') AS sent_on
						FROM pnh_employee_grpsms_log a
						JOIN m_employee_info b ON b.employee_id=a.emp_id
						WHERE `type`=2 $cond
						order by a.id desc
						limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
				
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','sent_to'=>'Sent To','msg'=>'Message','loggged_on'=>'LoggedOn');
				
				if($log_sms_details_res->num_rows())
				{
						foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'sent_to'=>$log_det['contact_no'],'msg'=>$log_det['grp_msg'],'loggged_on'=>$log_det['sent_on']);
					}
				}
			}
			
			elseif($type=='start_dysmsfran')
			{
				$sql_total ="SELECT *
							FROM `pnh_sms_log_sent`a
							JOIN `pnh_m_franchise_info`b ON b.franchise_id=a.franchise_id
							where 1 and a.type = 'CUR_BALANCE' 
							group by a.id
							ORDER BY sent_on DESC 
							";
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
				
				$sql = "SELECT a.to,msg,a.franchise_id,b.franchise_name,DATE_FORMAT((FROM_UNIXTIME(a.sent_on)),'%d/%m/%Y %h:%i %p') AS sent_on,
									d.territory_name,c.town_name
									FROM `pnh_sms_log_sent`a
									JOIN `pnh_m_franchise_info`b ON b.franchise_id=a.franchise_id
									JOIN pnh_towns c ON c.id=b.town_id
									JOIN pnh_m_territory_info d ON d.id=b.territory_id
									where 1 and a.type = 'CUR_BALANCE' 	
									ORDER BY a.id DESC
									limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
				
				$tbl_head = array('slno'=>'Slno','town_name'=>'Town','territory_name'=>'Territory','to'=>'to','franchise_name'=>'Franchisee Name','msg'=>'Message','loggged_on'=>'LoggedOn');
				
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
						{
						$tbl_data[] = array('slno'=>$i+1,'town_name'=>$log_det['town_name'],'territory_name'=>$log_det['territory_name'],'to'=>$log_det['to'],'franchise_name'=>anchor('admin/pnh_franchise/'.$log_det['franchise_id'],$log_det['franchise_name']),'msg'=>$log_det['msg'],'loggged_on'=>$log_det['sent_on']);
						}
				}
				
			}
			
			elseif($type=='daysales_summary')
			{
				$sql_total ="SELECT a.emp_id,b.name,grp_msg,DATE_FORMAT(a.created_on,'%d/%m/%Y %h:%i %p') AS sent_on
							FROM pnh_employee_grpsms_log a
							JOIN m_employee_info b ON b.employee_id=a.emp_id
							WHERE `type`=3 $cond
							group by a.id";
						
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
			
				$sql = "SELECT a.contact_no,a.emp_id,b.name,grp_msg,DATE_FORMAT(a.created_on,'%d/%m/%Y %h:%i %p') AS sent_on,e.territory_name,t.town_name
						FROM pnh_employee_grpsms_log a
						JOIN m_employee_info b ON b.employee_id=a.emp_id
						LEFT JOIN m_employee_rolelink c ON c.employee_id=a.emp_id
						LEFT JOIN m_town_territory_link d ON d.employee_id=c.employee_id
						RIGHT JOIN `pnh_m_territory_info` e ON e.id=d.territory_id
						RIGHT JOIN `pnh_towns` t ON t.id=d.town_id
						WHERE `type`=3 AND c.is_active=1 $cond 
						GROUP BY a.id 
						ORDER BY a.id DESC
						limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
			
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','sent_to'=>'Sent To','town_name'=>'Town','territory_name'=>'Territory','msg'=>'Message','loggged_on'=>'LoggedOn');
			
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'sent_to'=>$log_det['contact_no'],'town_name'=>$log_det['town_name'],'territory_name'=>$log_det['territory_name'],'msg'=>$log_det['grp_msg'],'loggged_on'=>$log_det['sent_on']);
					}
				}
			
			}
			
			elseif($type=='fu_tasks')
			{
				$sql_total ="SELECT a.*
							FROM pnh_m_task_info a
							JOIN m_employee_info b ON b.employee_id=a.assigned_to
							JOIN m_employee_info c ON c.employee_id=a.assigned_by
							JOIN pnh_towns d ON d.id=a.asgnd_town_id
							WHERE  a.is_active=1 AND on_date>=DATE(NOW()) and a.task_status=1 $cond1";
			
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
					
				$sql = "SELECT a.assigned_to,a.id,a.ref_no,task,task_type,DATE_FORMAT(on_date,'%d/%m/%Y') as on_date,DATE_FORMAT(due_date,'%d/%m/%Y') as due_date,asgnd_town_id,assigned_by,c.name AS assigned_byname ,b.name AS assigned_toname,a.task_status,d.town_name
						FROM pnh_m_task_info a
						JOIN m_employee_info b ON b.employee_id=a.assigned_to
						JOIN m_employee_info c ON c.employee_id=a.assigned_by
						JOIN pnh_towns d ON d.id=a.asgnd_town_id
						WHERE  a.is_active=1 AND on_date>=DATE(NOW()) and a.task_status=1 $cond1
			   			order by a.id desc 
						limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
					
				$tbl_head = array('slno'=>'Slno','tasks'=>'Tasks','assigned_town'=>'Assigned town','on_date'=>'On Date','due_date'=>'Due Date','task_status'=>'Task Status','assigned_by'=>'Assigned By');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						
						$tbl_data[] = array('slno'=>$i+1,'tasks'=>anchor('admin/calender#taskview-'.$log_det['id'],$log_det['ref_no']),'assigned_town'=>$log_det['town_name'],'on_date'=>$log_det['on_date'],'due_date'=>$log_det['due_date'],'task_status'=>$this->task_status[$log_det['task_status']],'assigned_by'=>$log_det['assigned_byname']);
					}
				}
					
			}
			
			elseif($type=='completed_tasks')
			{
				$sql_total ="SELECT *
						FROM pnh_m_task_info a
						JOIN m_employee_info b ON b.employee_id=a.assigned_to
						JOIN m_employee_info c ON c.employee_id=a.assigned_by
						JOIN pnh_towns d ON d.id=a.asgnd_town_id
		   				JOIN m_employee_info e ON e.employee_id=a.completed_by
						WHERE a.is_active=1 AND (due_date!=DATE(NOW()) AND a.task_status=2) $cond1
						ORDER BY on_date desc ";
					
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
					
				$sql = "SELECT a.ref_no,a.assigned_to,a.id,task,task_type,DATE_FORMAT(on_date,'%d/%m/%Y') AS on_date,DATE_FORMAT(due_date,'%d/%m/%Y') AS due_date,asgnd_town_id,assigned_by,c.name AS assigned_byname ,b.name AS assigned_toname,a.task_status,d.town_name,e.name AS status_updatedby,date_format(completed_on,'%d/%m/%Y')as completed_on
						FROM pnh_m_task_info a
						JOIN m_employee_info b ON b.employee_id=a.assigned_to
						JOIN m_employee_info c ON c.employee_id=a.assigned_by
						JOIN pnh_towns d ON d.id=a.asgnd_town_id
		   				JOIN m_employee_info e ON e.employee_id=a.completed_by
						WHERE a.is_active=1 AND (due_date!=DATE(NOW()) AND a.task_status=2) $cond1
						ORDER BY on_date desc
						limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
					
				$tbl_head = array('slno'=>'Slno','tasks'=>'Tasks','assigned_town'=>'Assigned town','on_date'=>'On Date','due_date'=>'Due Date','task_status'=>'Task Status','assigned_by'=>'Assigned By');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
			
						$tbl_data[] = array('slno'=>$i+1,'tasks'=>anchor('admin/calender#taskview-'.$log_det['id'],$log_det['ref_no']),'assigned_town'=>$log_det['town_name'],'on_date'=>$log_det['on_date'],'due_date'=>$log_det['due_date'],'task_status'=>$this->task_status[$log_det['task_status']],'assigned_by'=>$log_det['assigned_byname']);
					}
				}
					
			}
			
			elseif($type=='closed_tasks')
			{
				$sql_total ="SELECT *
							FROM pnh_m_task_info a
							JOIN m_employee_info b ON b.employee_id=a.assigned_to
							JOIN m_employee_info c ON c.employee_id=a.assigned_by
							JOIN pnh_towns d ON d.id=a.asgnd_town_id
							JOIN m_employee_info e ON e.employee_id=a.completed_by
							WHERE a.is_active=1 AND (due_date!=DATE(NOW()) AND a.task_status=3) $cond1
							ORDER BY on_date desc ";
					
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
					
				$sql = "SELECT a.ref_no,a.assigned_to,a.id,task,task_type,DATE_FORMAT(on_date,'%d/%m/%Y') AS on_date,DATE_FORMAT(due_date,'%d/%m/%Y') AS due_date,asgnd_town_id,assigned_by,c.name AS assigned_byname ,b.name AS assigned_toname,a.task_status,d.town_name,e.name AS status_updatedby,date_format(completed_on,'%d/%m/%Y')as completed_on
						FROM pnh_m_task_info a
						JOIN m_employee_info b ON b.employee_id=a.assigned_to
						JOIN m_employee_info c ON c.employee_id=a.assigned_by
						JOIN pnh_towns d ON d.id=a.asgnd_town_id
						JOIN m_employee_info e ON e.employee_id=a.completed_by
						WHERE a.is_active=1 AND (due_date!=DATE(NOW()) AND a.task_status=3) $cond1
						ORDER BY on_date desc
						limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
					
				$tbl_head = array('slno'=>'Slno','tasks'=>'Tasks','assigned_town'=>'Assigned town','on_date'=>'On Date','due_date'=>'Due Date','task_status'=>'Task Status','assigned_by'=>'Assigned By');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
							
						$tbl_data[] = array('slno'=>$i+1,'tasks'=>anchor('admin/calender#taskview-'.$log_det['id'],$log_det['ref_no']),'assigned_town'=>$log_det['town_name'],'on_date'=>$log_det['on_date'],'due_date'=>$log_det['due_date'],'task_status'=>$this->task_status[$log_det['task_status']],'assigned_by'=>$log_det['assigned_byname']);
					}
				}
					
			}
			
			elseif($type=='dinvoicesms_tofran')
			{
				/*$sql_total ="SELECT a.franchise_id,a.msg
								FROM pnh_sms_log a 
								JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id 
								left JOIN pnh_towns d ON d.id=c.town_id 
								JOIN pnh_m_territory_info t ON t.id=c.territory_id 
								WHERE c.is_suspended=0 AND `type`='1' $cond
								ORDER BY a.created_on DESC ";*/
				$sql_total ="SELECT a.franchise_id,a.msg
								 FROM pnh_sms_log_sent a
								JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id
								left JOIN pnh_towns d ON d.id=c.town_id
								JOIN pnh_m_territory_info t ON t.id=c.territory_id
								WHERE `type`='11' $cond
								ORDER BY a.sent_on DESC ";
					
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
					
				/*$sql = "SELECT a.franchise_id,a.msg,DATE_FORMAT(FROM_UNIXTIME(a.created_on),'%d/%m/%y %h:%i %p') AS created_on,a.sender,c.franchise_name,d.town_name,t.territory_name 
														FROM pnh_sms_log a 
														JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id 
														left JOIN pnh_towns d ON d.id=c.town_id 
														JOIN pnh_m_territory_info t ON t.id=c.territory_id 
														WHERE c.is_suspended=0 AND `type`='1' $cond
														ORDER BY a.created_on DESC 
														limit $pg,$limit";*/
				$sql = "SELECT a.franchise_id,a.msg,DATE_FORMAT(FROM_UNIXTIME(a.sent_on),'%d/%m/%y %h:%i %p') AS created_on,c.franchise_name,d.town_name,t.territory_name
									 FROM pnh_sms_log_sent a
									JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id
									left JOIN pnh_towns d ON d.id=c.town_id
									JOIN pnh_m_territory_info t ON t.id=c.territory_id
									WHERE `type`='11' $cond
									ORDER BY a.sent_on DESC
									limit $pg,$limit";
				
				$log_sms_details_res=$this->db->query($sql);
					
				$tbl_head = array('slno'=>'Slno','franchise_name'=>'Franchisee Name','territory_name'=>'Territory','town_name'=>'Town','msg'=>'Message','logged_on'=>'LoggedOn');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'franchise_name'=>anchor('admin/pnh_franchise/'.$log_det['franchise_id'],$log_det['franchise_name']),'territory_name'=>$log_det['territory_name'],'town_name'=>$log_det['town_name'],'msg'=>$log_det['msg'],'logged_on'=>$log_det['created_on']);
					}
				}
			}
			
			elseif($type=='rinvoicesms_tofran')
			{
				/*$sql_total ="SELECT a.franchise_id,a.msg
									FROM pnh_sms_log a
									JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id
									left JOIN pnh_towns d ON d.id=c.town_id
									JOIN pnh_m_territory_info t ON t.id=c.territory_id
									WHERE c.is_suspended=0 AND `type`='2' $cond
									ORDER BY a.created_on DESC ";*/
				$sql_total ="SELECT a.franchise_id,a.msg
									FROM pnh_sms_log_sent a
									JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id
									left JOIN pnh_towns d ON d.id=c.town_id
									JOIN pnh_m_territory_info t ON t.id=c.territory_id
									WHERE  `type`='13' $cond
									ORDER BY a.sent_on DESC ";
				
					
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
					
				/*$sql = "SELECT a.franchise_id,a.msg,DATE_FORMAT(FROM_UNIXTIME(a.created_on),'%d/%m/%y %h:%i %p') AS created_on,a.sender,c.franchise_name,d.town_name,t.territory_name
									FROM pnh_sms_log a
									JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id
									left JOIN pnh_towns d ON d.id=c.town_id
									JOIN pnh_m_territory_info t ON t.id=c.territory_id
									WHERE `type`='2' $cond
									ORDER BY a.created_on DESC
									limit $pg,$limit";*/
				
				$sql = "SELECT a.franchise_id,a.msg,DATE_FORMAT(FROM_UNIXTIME(a.sent_on),'%d/%m/%y %h:%i %p') AS created_on,c.franchise_name,d.town_name,t.territory_name
								 FROM pnh_sms_log_sent a
								JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id
								left JOIN pnh_towns d ON d.id=c.town_id
								JOIN pnh_m_territory_info t ON t.id=c.territory_id
								WHERE `type`='13' $cond
								ORDER BY a.sent_on DESC
								limit $pg,$limit";
				
				
				$log_sms_details_res=$this->db->query($sql);
					
				$tbl_head = array('slno'=>'Slno','franchise_name'=>'Franchisee Name','territory_name'=>'Territory','town_name'=>'Town','msg'=>'Message','logged_on'=>'LoggedOn');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'franchise_name'=>anchor('admin/pnh_franchise/'.$log_det['franchise_id'],$log_det['franchise_name']),'territory_name'=>$log_det['territory_name'],'town_name'=>$log_det['town_name'],'msg'=>$log_det['msg'],'logged_on'=>$log_det['created_on']);
					}
				}
			}

			elseif($type=="emp_bouncesms")
			{
				$sql_total = "SELECT a.emp_id
				FROM pnh_employee_grpsms_log a
				JOIN m_employee_info b ON b.employee_id=a.emp_id
				WHERE `type` = 'Bounce' $cond
				GROUP BY a.id
				";
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
					
				$sql = "SELECT a.contact_no,a.emp_id,b.name,grp_msg,DATE_FORMAT(a.created_on,'%d/%m/%Y %h:%i %p') AS sent_on
				FROM pnh_employee_grpsms_log a
				JOIN m_employee_info b ON b.employee_id=a.emp_id
				WHERE `type`= 'Bounce' $cond
				order by a.id desc
				limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
					
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','sent_to'=>'Sent To','msg'=>'Message','loggged_on'=>'LoggedOn');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'sent_to'=>$log_det['contact_no'],'msg'=>$log_det['grp_msg'],'loggged_on'=>$log_det['sent_on']);
					}
				}
					
			}
			elseif($type=='offer_dytoemp')
			{
				$sql_total = "SELECT a.emp_id
				FROM pnh_employee_grpsms_log a
				JOIN m_employee_info b ON b.employee_id=a.emp_id
				WHERE `type`='5' $cond
				GROUP BY a.id
				";
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
					
				$sql = "SELECT a.contact_no,a.emp_id,b.name,grp_msg,DATE_FORMAT(a.created_on,'%d/%m/%Y %h:%i %p') AS sent_on
				FROM pnh_employee_grpsms_log a
				JOIN m_employee_info b ON b.employee_id=a.emp_id
				WHERE `type`='5' $cond
				order by a.id desc
				limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
					
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','sent_to'=>'Sent To','msg'=>'Message','loggged_on'=>'LoggedOn');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'sent_to'=>$log_det['contact_no'],'msg'=>$log_det['grp_msg'],'loggged_on'=>$log_det['sent_on']);
					}
				}

			}
			elseif($type=="fran_chqbounce")
			{
				$sql_total = "SELECT a.franchise_id,a.msg
								FROM pnh_sms_log_sent a
								JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id
								LEFT JOIN pnh_towns d ON d.id=c.town_id
								JOIN pnh_m_territory_info t ON t.id=c.territory_id
								WHERE c.is_suspended=0 AND `type`='Bounce'
								ORDER BY a.sent_on DESC";
					
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
					
				$sql = "SELECT a.franchise_id,a.msg,DATE_FORMAT(FROM_UNIXTIME(a.sent_on),'%d/%m/%y %h:%i %p') AS created_on,c.franchise_name,d.town_name,t.territory_name
						FROM pnh_sms_log_sent a
						JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id
						LEFT JOIN pnh_towns d ON d.id=c.town_id
						JOIN pnh_m_territory_info t ON t.id=c.territory_id
						WHERE c.is_suspended=0 AND `type`='Bounce'
						ORDER BY a.sent_on DESC
						limit $pg,$limit";
				$log_sms_details_res=$this->db->query($sql);
					
				$tbl_head = array('slno'=>'Slno','franchise_name'=>'Franchisee Name','territory_name'=>'Territory','town_name'=>'Town','msg'=>'Message','sent_on'=>'Sent On');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'franchise_name'=>anchor('admin/pnh_franchise/'.$log_det['franchise_id'],$log_det['franchise_name']),'territory_name'=>$log_det['territory_name'],'town_name'=>$log_det['town_name'],'msg'=>$log_det['msg'],'sent_on'=>$log_det['created_on']);
					}
				}

			}else if($type=='shipmnet_ntfy')
			{
				$sql_total="select a.grp_msg as msg,a.emp_id,b.name,c.territory_name,d.town_name,a.created_on  from pnh_employee_grpsms_log a 
											join m_employee_info b on b.employee_id=a.emp_id
											left join pnh_m_territory_info c on c.id=a.territory_id
											left join pnh_towns d on d.id=a.town_id
										where type=4 $cond
										order by a.created_on desc";
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
				
				$sql=$sql_total." limit $pg,$limit";
				
				$log_sms_details_res=$this->db->query($sql);
				
				$tbl_head = array('slno'=>'Slno','name'=>'Employee name','territory_name'=>'Territory','town_name'=>'Town','msg'=>'Message','sent_on'=>'Sent On');
				
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'territory_name'=>$log_det['territory_name'],'town_name'=>$log_det['town_name'],'msg'=>str_replace(',',', ',$log_det['msg']),'sent_on'=>format_datetime($log_det['created_on']));
					}
				}
			}else if($type=='lr_number_updates')
			{
				$sql_total="select a.grp_msg as msg,a.emp_id,b.name,c.territory_name,d.town_name,a.created_on  from pnh_employee_grpsms_log a
								join m_employee_info b on b.employee_id=a.emp_id
								left join pnh_m_territory_info c on c.id=a.territory_id
								left join pnh_towns d on d.id=a.town_id
						where type=6
						";
				if($emp_id)
					$sql_total.=" and a.emp_id=? ";
					
				$sql_total.=" order by a.created_on desc ";
				
				$tbl_total_rows = $this->db->query($sql_total,$emp_id)->num_rows();
				
				$sql=$sql_total." limit $pg,$limit";
				
				$log_sms_details_res=$this->db->query($sql,$emp_id);
				
				$tbl_head = array('slno'=>'Slno','name'=>'Employe name','msg'=>'Message','sent_on'=>'Sent On');
				
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'name'=>anchor('admin/view_employee/'.$log_det['emp_id'],$log_det['name']),'msg'=>$log_det['msg'],'sent_on'=>format_datetime($log_det['created_on']));
					}
				}
			}else if($type=='dr_excu_invsms')
			{
				$sql="select a.emp_id,a.grp_msg as msg,a.created_on as logged_on,a.contact_no,b.name from pnh_employee_grpsms_log a join m_employee_info b on b.employee_id=a.emp_id where b.job_title2=7 and a.type=9 ";
				
				$tbl_total_rows=$this->db->query($sql)->num_rows();
				
				$sql.=" limit $pg, $limit";
				
				$log_sms_details_res=$this->db->query($sql);
				
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','msg'=>'Message','loggged_on'=>'LoggedOn');
				
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],ucwords($log_det['name'])),'msg'=>$log_det['msg'],'loggged_on'=>format_datetime($log_det['logged_on']));
					}
				}
			}else if($type=='inv_pickup')
			{
				$sql="select a.emp_id,a.grp_msg as msg,a.created_on as logged_on,a.contact_no,b.name,c.role_name 
							from pnh_employee_grpsms_log a 
							join m_employee_info b on b.employee_id=a.emp_id 
							join m_employee_roles c on c.role_id= b.job_title2 
							where (b.job_title2=4 or b.job_title2=5 or b.job_title2=6 ) and a.type=8 ";
				
				if($emp_id)
					$sql.=" and a.emp_id=? ";
				
				$sql.=" order by a.created_on desc ";
				
				
				$tbl_total_rows=$this->db->query($sql,$emp_id)->num_rows();
				
				$sql.=" limit $pg, $limit";
				
				$log_sms_details_res=$this->db->query($sql,$emp_id);
				
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','msg'=>'Message','loggged_on'=>'LoggedOn','role'=> 'Role');
				
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],ucwords($log_det['name'])),'msg'=>$log_det['msg'],'loggged_on'=>format_datetime($log_det['logged_on']),'role'=>$log_det['role_name']);
					}
				}
			}else if($type=='inv_handover')
			{
				$sql="select a.emp_id,a.grp_msg as msg,a.created_on as logged_on,a.contact_no,b.name,c.role_name 
							from pnh_employee_grpsms_log a 
							join m_employee_info b on b.employee_id=a.emp_id 
							join m_employee_roles c on c.role_id= b.job_title2 
						where (b.job_title2=4 or b.job_title2=5 or b.job_title2=6 ) and a.type=9 ";
				
				if($emp_id)
					$sql.=" and a.emp_id=? ";
				
				$sql.=" order by a.created_on desc ";
				
				
				$tbl_total_rows=$this->db->query($sql,$emp_id)->num_rows();
				
				$sql.=" limit $pg, $limit";
				
				$log_sms_details_res=$this->db->query($sql,$emp_id);
				
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','msg'=>'Message','loggged_on'=>'LoggedOn','role'=> 'Role');
				
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],ucwords($log_det['name'])),'msg'=>$log_det['msg'],'loggged_on'=>format_datetime($log_det['logged_on']),'role'=>$log_det['role_name']);
					}
				}
			}else if($type=='ship_delivered')
			{
				$sql="select a.emp_id,a.grp_msg as msg,a.created_on as logged_on,a.contact_no,b.name,c.role_name 
							from pnh_employee_grpsms_log a 
							join m_employee_info b on b.employee_id=a.emp_id 
							join m_employee_roles c on c.role_id= b.job_title2 
							where (b.job_title2=4 or b.job_title2=5 or b.job_title2=6 ) and a.type=10 ";
				
				if($emp_id)
					$sql.=' and a.emp_id=? ';
				
				$sql.=" order by logged_on desc ";
				
				$tbl_total_rows=$this->db->query($sql,$emp_id)->num_rows();
				
				$sql.=" limit $pg, $limit  ";
				
				$log_sms_details_res=$this->db->query($sql,$emp_id);
				
				$tbl_head = array('slno'=>'Slno','employee_name'=>'Employee Name','msg'=>'Message','loggged_on'=>'LoggedOn','role'=> 'Role');
				
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'employee_name'=>anchor('admin/view_employee/'.$log_det['emp_id'],ucwords($log_det['name'])),'msg'=>$log_det['msg'],'loggged_on'=>format_datetime($log_det['logged_on']),'role'=>$log_det['role_name']);
					}
				}
			}
			
			else if($type=='fran_voucherredeeming')
			{
				$sql="SELECT s.*,m.first_name,m.franchise_id,f.franchise_name,m.user_id FROM pnh_sms_log_sent s
						JOIN pnh_member_info m ON m.mobile=s.to
						JOIN pnh_m_franchise_info f ON f.franchise_id=m.franchise_id
						WHERE `TYPE`=1 ";
			
				
				$sql.=" order by sent_on desc ";
			
				$tbl_total_rows=$this->db->query($sql)->num_rows();
			
				$sql.=" limit $pg, $limit  ";
			
				$log_sms_details_res=$this->db->query($sql,$emp_id);
			
				$tbl_head = array('slno'=>'Slno','franchise_name'=>'Franchise Name','member_name'=>'Member Name','msg'=>'Message','loggged_on'=>'LoggedOn');
			
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'franchise_name'=>$log_det['franchise_name'],'member_name'=>anchor('admin/pnh_viewmember/'.$log_det['user_id'],ucwords($log_det['first_name'])),'msg'=>$log_det['msg'],'loggged_on'=>format_datetime_ts($log_det['sent_on']));
					}
				}
			}
			
			else if($type=='fran_voucherredeeming')
			{
				$sql="SELECT s.*,m.first_name,m.franchise_id,f.franchise_name FROM pnh_sms_log_sent s
						JOIN pnh_member_info m ON m.mobile=s.to
						JOIN pnh_m_franchise_info f ON f.franchise_id=m.franchise_id
						WHERE `TYPE`=1 ";
					
			
				$sql.=" order by sent_on desc ";
					
				$tbl_total_rows=$this->db->query($sql)->num_rows();
					
				$sql.=" limit $pg, $limit  ";
					
				$log_sms_details_res=$this->db->query($sql,$emp_id);
					
				$tbl_head = array('slno'=>'Slno','franchise_name'=>'Franchise Name','member_name'=>'Member Name','msg'=>'Message','loggged_on'=>'LoggedOn');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'franchise_name'=>$log_det['franchise_name'],'member_name'=>anchor('admin/pnh_viewmember/'.$log_det['user_id'],ucwords($log_det['first_name'])),'msg'=>$log_det['msg'],'loggged_on'=>format_datetime_ts($log_det['sent_on']));
					}
				}
			}
				
			else if($type=='franvoucher_activation')
			{
				$sql="SELECT msg,franchise_name,s.created_on,sender,s.franchise_id FROM pnh_sms_log s 
						JOIN pnh_m_franchise_info f ON f.franchise_id=s.franchise_id
						WHERE msg LIKE 'c %' ";
					
					
				$sql.=" order by created_on desc ";
					
				$tbl_total_rows=$this->db->query($sql)->num_rows();
					
				$sql.=" limit $pg, $limit  ";
					
				$log_sms_details_res=$this->db->query($sql,$emp_id);
					
				$tbl_head = array('slno'=>'Slno','franchise_name'=>'Franchise Name','sent_no'=>'Sent From(Mobile Number)','msg'=>'Message','loggged_on'=>'LoggedOn');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'franchise_name'=>anchor('admin/pnh_franchise/'.$log_det['franchise_id'],ucwords($log_det['franchise_name'])),'sent_no'=>$log_det['sender'],'msg'=>$log_det['msg'],'loggged_on'=>format_datetime_ts($log_det['created_on']));
					}
				}
			}

			else if($type=='fra_ship_nty')
			{
				$sql_total ="SELECT a.franchise_id,a.msg
								FROM pnh_sms_log_sent a
								JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id
								left JOIN pnh_towns d ON d.id=c.town_id
								JOIN pnh_m_territory_info t ON t.id=c.territory_id
								WHERE  `type`='12' $cond
								ORDER BY a.sent_on DESC ";
				
				
				$tbl_total_rows = $this->db->query($sql_total)->num_rows();
				
				
				
				$sql = "SELECT a.franchise_id,a.msg,DATE_FORMAT(FROM_UNIXTIME(a.sent_on),'%d/%m/%y %h:%i %p') AS created_on,c.franchise_name,d.town_name,t.territory_name
								FROM pnh_sms_log_sent a
								JOIN pnh_m_franchise_info c ON c.franchise_id=a.franchise_id
								left JOIN pnh_towns d ON d.id=c.town_id
								JOIN pnh_m_territory_info t ON t.id=c.territory_id
								WHERE `type`='12' $cond
								ORDER BY a.sent_on DESC
								limit $pg,$limit";
				
				
				$log_sms_details_res=$this->db->query($sql);
				
				$tbl_head = array('slno'=>'Slno','franchise_name'=>'Franchisee Name','territory_name'=>'Territory','town_name'=>'Town','msg'=>'Message','logged_on'=>'LoggedOn');
				
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'franchise_name'=>anchor('admin/pnh_franchise/'.$log_det['franchise_id'],$log_det['franchise_name']),'territory_name'=>$log_det['territory_name'],'town_name'=>$log_det['town_name'],'msg'=>$log_det['msg'],'logged_on'=>$log_det['created_on']);
					}
				}
				
			}
				else if($type=='memvoucher_activation')
			{
				$sql="SELECT s.*,m.first_name,m.user_id,f.franchise_name FROM `pnh_sms_log` s
						JOIN pnh_member_info m ON m.mobile=s.sender
						JOIN pnh_m_franchise_info f ON f.franchise_id=m.franchise_id
						WHERE TYPE ='Voucher'";
					
					
				$sql.=" order by s.created_on desc ";
					
				$tbl_total_rows=$this->db->query($sql)->num_rows();
					
				$sql.=" limit $pg, $limit  ";
					
				$log_sms_details_res=$this->db->query($sql,$emp_id);
					
				$tbl_head = array('slno'=>'Slno','franchise_name'=>'Franchise Name','member_name'=>'Member Name','sent_no'=>'Sent From(Mobile Number)','msg'=>'Message','loggged_on'=>'LoggedOn');
					
				if($log_sms_details_res->num_rows())
				{
					foreach($log_sms_details_res->result_array() as $i=>$log_det)
					{
						$tbl_data[] = array('slno'=>$i+1,'franchise_name'=>anchor('admin/pnh_franchise/'.$log_det['franchise_id'],ucwords($log_det['franchise_name'])),'member_name'=>anchor('admin/pnh_viewmember/'.$log_det['user_id'],ucwords($log_det['first_name'])),'sent_no'=>$log_det['sender'],'msg'=>$log_det['msg'],'loggged_on'=>format_datetime_ts($log_det['created_on']));
					}
				}
			}	
			if(count($tbl_data))
			{
				$tbl_data_html = '<table cellpadding="5" cellspacing="0" class="datagrid datagridsort">';
				$tbl_data_html .= '<thead>';
				foreach($tbl_head as $th)
					$tbl_data_html .= '<th>'.$th.'</th>';
				
				$tbl_data_html .= '</thead>';
				$i = $pg;
				$tbl_data_html .= '<tbody>';
				foreach($tbl_data as $tdata)
				{
					$tbl_data_html .= '<tr>';
					foreach(array_keys($tbl_head) as $th_i)
					{
						if($th_i == 'slno')
							$tdata[$th_i] = $i+1;
						
						$tbl_data_html .= '	<td>'.$tdata[$th_i].'</td>';
					}
					$tbl_data_html .= '</tr>';

					$i = $i+1;
				}
				$tbl_data_html .= '</tbody>';
				$tbl_data_html .= '</table>';
			}else
			{
				$tbl_data_html = '<div align="center"> No data found</div>';
			}
		
			$this->load->library('pagination');

			$config['base_url'] = site_url('admin/jx_getpnh_exsms_log/'.$type.'/'.$terr_id.'/'.$emp_id);
			$config['total_rows'] = $tbl_total_rows;
			$config['per_page'] = $limit;
			$config['uri_segment'] = 6;
			
			$this->config->set_item('enable_query_strings',false);
			$this->pagination->initialize($config);
			$pagi_links = $this->pagination->create_links();
			$this->config->set_item('enable_query_strings',true);
			
			$pagi_links = '<div class="log_pagination">'.$pagi_links.'</div>';
			
			echo json_encode(array('log_data'=>$tbl_data_html,'pagi_links'=>$pagi_links,'type'=>$type,'terr_id'=>$terr_id,'emp_id'=>$emp_id,'pg'=>$pg));
			
		}

	
		/*
		 * function for bulk update
		 */
		function pnh_deals_bulk_update()
		{
			$user=$this->auth(DEAL_MANAGER_ROLE);
			if($_FILES)
			{
				$this->erpm->do_pnh_deals_bulk_update();
			}
			$data['page']="pnh_deals_bulk_update";
			$this->load->view("admin",$data);
		}
		
		/**
		 * get the invoices numbers by manifesto log id
		 */
		function get_invoices_nos_by_manisfestoid()
		{
			$user=$this->auth(KFILE_ROLE);
			
			$manifesto_log_id=$this->input->post('manifesto_id');
			
			//get the territory name by invoices nuber
			$territories_details=$this->erpm->get_terriory_name_by_invoice($manifesto_log_id);
			//get the driver list
			$driver_details=$this->erpm->get_drivres_list();
			//get the manifesto sent invoices
			$invoices_nos_det=$this->erpm->get_manifesto_send_invoices_det($manifesto_log_id);
			//get the field cordinators list
			$field_cordinators_list=$this->erpm->get_field_cordinators_list();
						
			$role_types=$this->db->query("select * from m_employee_roles  where role_id=6 or role_id=7 order by role_name")->result_array();
			$franchise_invoice_link=$territories_details['franchise_invoice_link'];
			
			echo '<div  class="manifesto_update_form">
				<div style="background:#FFFAD6;border-bottom:1px dashed #F4EB9A;font-size:12px;">
					<div style="width:460px;float:left;font-size:10px;">
						Territory : 
						<select id="terr_list" onChange="select_invoice_by_territory(this)">
						<option value="all">Choose</option>';
				$tr_list = array();
				if($territories_details['territories_det'])
				{
					$check_terr=array();
					foreach($territories_details['territories_det'] as $territories_det)
					{
						if(!isset($check_terr[$territories_det['territory_name']]))
						{
							$check_terr[$territories_det['territory_name']]=1;
							echo '<option value="'.$territories_det['territory_id'].'">'.$territories_det['territory_name'].'</option>';
						}
						
						$tr_list[$territories_det['territory_id']] = $territories_det['territory_name'];
					}
				}
					
						
			echo	'	</select>
						Scan Invoice : <input type="text" id="srch_barcode" size="10" onkeyup="scan_invoice(event)">
									  <a href="javascript:void(0)" class="a_button" onClick="scan_invoice(this)" value="scan">Scan</a>
					</div>
					<div style="width:300px;float:right;padding:5px;">
						<div style="float:left;font-size:10px;padding:3px;">
							Select all:<input type="checkbox" class="sel_all" onClick="select_option(this)">
						</div>
						<div style="float:left;font-size:10px;padding:3px;">
							<div style="background:#FFA500;float:left;padding:3px;">
								<b>Sent</b>
							</div>
						</div>
						<div style="float:left;font-size:10px;padding:3px;">
							<div style="background:#ffffa0;float:left;padding:3px;">
								<b>Pending</b>
							</div>
						</div>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div style="overflow:hidden;min-height:200px;max-height:300px;overflow:auto">';
			
				
			
						if($invoices_nos_det)
						{
							$territory_invoice_link=$territories_details['territory_invoice_link'];
							$trans_id_invoice_link=$territories_details['trans_id_invoice_link'];
							
							
							$sorted_invoices_by_sent=array();
							$sorted_invoices_by_sent['a']=array();
							$sorted_invoices_by_sent['b']=array();
							
							$send_invoices_Det=array();
							if($invoices_nos_det[0]['sent_invoices'])
								$send_invoices_Det=explode(',',$invoices_nos_det[0]['sent_invoices']);
							
							$invoices_list = explode(',',$invoices_nos_det[0]['invoice_nos']);
							
							foreach($invoices_list as $invoice)
							{
								if(in_array($invoice, $send_invoices_Det))
									$sorted_invoices_by_sent['a'][]=$invoice;
								else 
									$sorted_invoices_by_sent['b'][]=$invoice;
							}
							
							
							foreach($tr_list as $tid=>$trname)
							{
								echo '<h3 class="show_invoice show_invoice_'.$tid.'" style="display:none;">'.$trname.'</h3>'; 
								echo '<table>';
								echo  	'<tr>';
								
								foreach($sorted_invoices_by_sent as $invoices_nos)
								{
									foreach($invoices_nos as $in=> $invoice_no)
									{
										
										$territory_id=$territory_invoice_link[$invoice_no];
										$trans_id=$trans_id_invoice_link[$invoice_no];
										
										if($territory_id != $tid)
											continue;
										
										$inv_class_name='show_invoice_'.$territory_id;
										$rem_class='rm_'.$invoice_no;
										
										$disable_checkbox='';
										$select_class='sel';
										$backgroundcolor='';
										if(in_array($invoice_no, $send_invoices_Det))
										{
											$disable_checkbox='checked="checked" disabled="disabled"';
											$select_class='';
											$backgroundcolor='background:#FFA500;';
										}else{
											$backgroundcolor='background:#ffffa0;';
										}
											
										
										echo '<td style="overflow:hidden;float:left;margin:2px;'.$backgroundcolor.'padding:4px;width:160px;height:45px;" class="show_invoice '.$inv_class_name.' '.$rem_class.' " valing="top">';
											echo '<b><a href="'.site_url("/admin/trans/$trans_id").'" target="_blank">'.$invoice_no.'</a></b>';
											echo '<span><input type="checkbox" name="invoice_nos[]" fr_name="'.addslashes($franchise_invoice_link[$invoice_no]).'" value="'.$invoice_no.'"'.$disable_checkbox.' class="'.$invoice_no.' '.$select_class.'"></span><br>';
											echo '<span style="font-size:8px;">('.$franchise_invoice_link[$invoice_no].')</span><br>';
											if(!$disable_checkbox)
												echo '<span style="font-size:12px;color:red !important;"><a style="font-size:12px;color:red !important;" href="javascript:void(0)" invoice_no="'.$invoice_no.'" class="remove_invoice">Remove</a></spna>';
										echo '</td>';
											
										if(($in+1)%4==0)
										{
											echo '</tr><tr>';
										}
									}
								echo  	'</tr>';
								echo '<table>';
								}
							}
						}
				
			
			//echo '</div>';
			
			$tran_opts=array('Choose'=>'choose','drivers_list_blk'=>'Driver','field_cordinators_list_blk'=>'Field Co-oridnator','other_trans'=>'Others');
			$pick_up_by=array("choose","Executives","Field Co-ordinators");
			
			//drives list
			echo '<table cellpadding="5" cellspacing="0" width="100%">
					<tr>
						<td>Send Through:</td>
						<td>
							<select name="transport_opts" id="Transport_opts" onChange="select_transport(this)">';
									echo '<option vlaue="Choose">Choose</option>';
								foreach($role_types as $i=>$opt)
								{
									echo '<option value="'.$opt['role_id'].'">'.$opt['role_name'].'</option>';
								}
									echo '<option value="0">Other transport</option>';								
			echo			'</select
						</td>
						<td id="drivers_list_blk" style="display:none;" class="trans_opt_blk">
							<select name="drivers_list" id="drivers_list" >
								<option value="choose">Choose Driver</option>';
								if($driver_details)
								{
									foreach($driver_details as $driver_Det)
									{
										echo '<option value="'.$driver_Det['employee_id'].':'.$driver_Det['name'].'">'.ucwords($driver_Det['name']).'</option>';
									}
								}
							echo '</select>
						</td>
						<td id="field_cordinators_list_blk" style="display:none;" class="trans_opt_blk">
							<select name="field_cordinators_list" id="field_cordinators_list" >
								<option value="choose">Choose</option>';
								if($field_cordinators_list)
								{
									foreach($field_cordinators_list as $field_cordinator_det)
									{
										echo '<option value="'.$field_cordinator_det['employee_id'].':'.$field_cordinator_det['name'].'">'.ucwords($field_cordinator_det['name']).'</option>';
									}
								}
							echo '</select>
						</td>
						<td style="display:none;" class="trans_opt_blk" id="other_trans" width="480">
							<span style="font-size:10px">Any Other Transport :<input type="text" name="other_driver" id="other_driver"></span>
							<span style="font-size:10px">Ph :<input type="text" name="other_driver_ph"></span>
						</td>
					</tr>
					<tr class="hidden" id="pick-up-by-blk">
						<td>Pick up by:</td>
						<td id="pick-up-by"></td>
					</tr>
					<tr>
						<td>Vehicle number:</td>
						<td><input type="text" name="vehicle_num" size="10"></td>
					</tr>
					<tr>
						<td>Start meter rate:</td>
						<td><input type="text" name="start_meter" size="10"></td>
					
					</tr>
					<tr>
						<td>Remark:</td>
						<td colspan="5"><textarea name="remark" cols="35" rows="3"></textarea></td>
					</tr>
				</table>
			</div>
		</div>';
			
		}
		
		
		function jx_get_terrtownby_invlist()
		{
			$this->erpm->auth(PNH_SHIPMENT_MANAGER);
			$invnos = $this->input->post('invnos');
			$invnos=implode(',',array_filter(explode(',',$invnos)));
			
			$hndlby_roleid = $hndlby_empid = $hndleby_name = $hndleby_contactno = $hndleby_vehicle_num = $start_meter_rate = $pickup_empid = $manifesto_remarks = $shiping_date= $bus_id= $bus_dest_id=$hndlby_type=$courier_id='';
			$tr_type='';
			// used while modifying the selected shipments  
			$manifesto_id=$this->input->post('manifesto_id');
			if($manifesto_id)
			{
				$manifesto_sent_det = $this->db->query('select * from pnh_m_manifesto_sent_log where manifesto_id = ? ',$manifesto_id)->row_array();
				$invnos = $manifesto_sent_det['sent_invoices'];
				
				$hndlby_roleid = $manifesto_sent_det['hndlby_roleid'];
				$hndlby_empid = $manifesto_sent_det['hndleby_empid'];
				$hndleby_name = $manifesto_sent_det['hndleby_name'];
				$hndleby_contactno = $manifesto_sent_det['hndleby_contactno'];
				$hndleby_vehicle_num = $manifesto_sent_det['hndleby_vehicle_num'];
				$start_meter_rate = $manifesto_sent_det['start_meter_rate'];
				$pickup_empid = $manifesto_sent_det['pickup_empid'];
				$manifesto_remarks = $manifesto_sent_det['remark'];
				$shiping_date=$manifesto_sent_det['shipment_sent_date'];
				$bus_id=$manifesto_sent_det['bus_id'];
				$bus_dest_id=$manifesto_sent_det['bus_destination'];
				$hndlby_type=$manifesto_sent_det['hndlby_type'];
				$courier_id=$manifesto_sent_det['hndleby_courier_id'];
				$tr_type = $manifesto_sent_det['transport_type'];
				
			}
			
			if(!$invnos)
				die("no invoices found");
			
			/*$invlist = $this->db->query("select a.invoice_no,franchise_name,d.territory_id
							from shipment_batch_process_invoice_link a 
							join king_invoice b on a.invoice_no = b.invoice_no and invoice_status = 1 
							join king_transactions c on c.transid = b.transid 
							join pnh_m_franchise_info d on d.franchise_id = c.franchise_id 
							where a.invoice_no in ($invnos) and shipped = 1 and packed = 1 and inv_manifesto_id = ?  
							group by a.invoice_no 
							order by franchise_name
							",$manifesto_id*1)->result_array();*/
			
			$invlist = $this->db->query("select a.invoice_no,franchise_name,d.territory_id,e.town_name,d.town_id,c.franchise_id
												from shipment_batch_process_invoice_link a
												join king_invoice b on a.invoice_no = b.invoice_no and invoice_status = 1
												join king_transactions c on c.transid = b.transid
												join pnh_m_franchise_info d on d.franchise_id = c.franchise_id
												join pnh_towns e on e.id=d.town_id
											where a.invoice_no in ($invnos) and shipped = 0 and packed = 1 and inv_manifesto_id = ?
											group by a.invoice_no
											order by town_name,franchise_name
					",$manifesto_id*1)->result_array();
			
			
			//echo $this->db->last_query();exit;
			$inv_town_link=array();
			$sorted_towns = array();
			foreach($invlist as $inv_det)
			{
				if(!isset($inv_town_link[$inv_det['town_id']]))
				{	$ttl_inv=1;
					$inv_town_link[$inv_det['town_id']]=array('name'=>$inv_det['town_name'],"ttl_inv"=>$ttl_inv,'franchises'=>array());
				}
				$inv_town_link[$inv_det['town_id']]['name']=$inv_det['town_name'];
				if(!isset($inv_town_link[$inv_det['town_id']]['franchises'][$inv_det['franchise_id']]))
					$inv_town_link[$inv_det['town_id']]['franchises'][$inv_det['franchise_id']]=array('name'=>$inv_det['franchise_name'],'invoices'=>array());
				
				$inv_town_link[$inv_det['town_id']]['ttl_inv']=$ttl_inv;
				$ttl_inv+=1;
				
				array_push($inv_town_link[$inv_det['town_id']]['franchises'][$inv_det['franchise_id']]['invoices'],$inv_det['invoice_no']);
				sort($inv_town_link[$inv_det['town_id']]['franchises'][$inv_det['franchise_id']]['invoices']);
				$sorted_towns[$inv_det['town_id']]=$inv_det['town_name'];
			}
			
			asort($sorted_towns);
			
			
			
			$terr_ids=array();
			foreach($invlist as $terr_det)
				$terr_ids[$terr_det['territory_id']]=$terr_det['territory_id'];
			
			//get the driver list
			$driver_details=$this->erpm->get_drivres_list();
			//get the field cordinators list
			$field_cordinators_list=$this->erpm->get_field_cordinators_list();
			//get the busues list;
			$buses_list=$this->db->query("select * from pnh_transporter_info")->result_array();
			
			$role_types=$this->db->query("select * from m_employee_roles  where role_id=6 or role_id=7 order by role_name")->result_array();
			
			//$executives_list=$this->erpm->get_executives_by_territroy($trr_id=0,implode(',',$terr_ids));
			$executives_list=$this->db->query("select a.employee_id,a.name,'BE' as role_name from m_employee_info a join m_town_territory_link c on c.employee_id=a.employee_id where 1 and c.territory_id in (".implode(',',$terr_ids).") and c.is_active = 1 and is_suspended = 0  and a.job_title = 5 and a.job_title2 = 5 group by a.employee_id")->result_array();
			
			$territory_manager=$this->erpm->get_territory_manager(implode(',',$terr_ids));
			
			$fc_list_res = $this->db->query("select a.employee_id,a.name,ifnull(group_concat(c.hub_name),'') as tr_names 
											from m_employee_info a 
											left join pnh_deliveryhub_fc_link b on a.employee_id = b.emp_id and b.is_active = 1 
											left join pnh_deliveryhub c on c.id = b.hub_id 
											where a.job_title2=6 and a.is_suspended = 0 
											group by a.employee_id
											order by a.name ");
			
			$courier_list=$this->db->query('select * from m_courier_info where is_active=1 order by courier_name ')->result_array();
			
			$tran_opts=array('Choose'=>'choose','drivers_list_blk'=>'Driver','field_cordinators_list_blk'=>'Field Co-oridnator','other_trans'=>'Others');
			$other_transport=array(0=>"Transportation",4=>"Courier");
			$pick_up_by=array("choose","Executives","Field Co-ordinators");
			$tranport_type=array("Bus","Cargo","General package");
			$pickup_options=array('options','Busniss Executive','Territory Manager');
			
			//drives list
			$frm_tbl_det = '<div style="padding:5px;background:#ffffe0"><table class="frm_block" cellpadding="8" cellspacing="0" width="100%" style="font-size:12px;">
						<tr>
						<td width="150">Ship this Via:</td>
						<td width="200">
						
						<select name="transport_opts" id="Transport_opts" onChange="select_transport(this)">';
			$frm_tbl_det .= '<option vlaue="Choose">Choose</option>';
						//$show_other=0;
						//$sel='';
						foreach($role_types as $i=>$opt)
						{
							$sel = ($hndlby_roleid==$opt['role_id'])?'selected':'';
							$frm_tbl_det .= '<option value="'.$opt['role_id'].'" '.$sel.' >'.$opt['role_name'].'</option>';
						}
						
						foreach($other_transport as $i=>$trs)
						{
							$sel='';
							if($manifesto_id)
							{	
								if($hndlby_roleid==0 && $bus_id*1 != 0 && $i == 0 )
									$sel='selected';
								else if(($i!=0) && ($hndlby_type==$i))
									$sel='selected';
							}
								
							$frm_tbl_det .= '<option value="'.$i.'" '.$sel.' >'.$trs.'</option>';
						}
						$frm_tbl_det .= '</select>
						</td>
						<td id="drivers_list_blk" style="display:none;" class="trans_opt_blk">
						Driver : <select name="drivers_list" id="drivers_list" >
						<option value="choose">Choose Driver</option>';
						if($driver_details)
						{
							foreach($driver_details as $driver_Det)
							{
								$sel = ($hndlby_empid==$driver_Det['employee_id'])?'selected':'';
								$frm_tbl_det .= '<option '.$sel.' value="'.$driver_Det['employee_id'].':'.$driver_Det['name'].'">'.ucwords($driver_Det['name']).'-('.$driver_Det['contact_no'].')</option>';
							}
						}
						$frm_tbl_det .= '</select>
						</td>
						<td id="field_cordinators_list_blk" style="display:none;" class="trans_opt_blk">
						Fright Coordinator : <select name="field_cordinators_list" id="field_cordinators_list" >
						<option value="choose">Choose</option>';
						
						if($fc_list_res->num_rows())
						{
							foreach($fc_list_res->result_array() as $fr_det)
							{
								$sel = ($hndlby_empid==$fr_det['employee_id'])?'selected':'';
								$frm_tbl_det .= '<option '.$sel.' value="'.$fr_det['employee_id'].'">'.$fr_det['name'].' '.($fr_det['tr_names']?'('.implode(',',array_unique(explode(',',$fr_det['tr_names']))).')':' ').' </option>';
							}
																			
						}

						$frm_tbl_det .= '</select>
						</td>
						<td style="display:none;" class="trans_opt_blk" id="other_trans" width="480" >
							Type : <select name="tr_tranport_type" tt_id="'.$tr_type.'" bus_id="'.$bus_id.'" bus_dest_id="'.$bus_dest_id.'">
							<option value="choose">choose</option>';
						if($tranport_type)
						{
							foreach($tranport_type as $t=>$type)
							{
								$t+=1;
								$frm_tbl_det .= '<option value='.$t.'>'.$type.'</option>';
							}
						}
						
						
						$frm_tbl_det .= '</select></td>
						<td style="display:none;" class="trans_opt_blk" id="courier_opt_blk" width="480">
							Courier list : <select name="courier_list" >
							<option value="0">choose</option>';
						if($courier_list)
						{
							foreach($courier_list as $t=>$courier)
							{
								$sel = ($courier_id==$courier['courier_id'])?'selected':'';
								$frm_tbl_det .= '<option value='.$courier['courier_id'].':'.$courier['courier_name'].' '.$sel.'>'.$courier['courier_name'].'</option>';
							}
						}
						
						
						$frm_tbl_det .= '</select></td>
						</tr>
						<tr class="hidden" id="pick-up-by-blk">
						<td>To be collected by @ destination</td>
						<td id="pick-up-by" colspan="4">';
						
						$frm_tbl_det .= ' ';
						if($fc_list_res->num_rows())
						{
							$frm_tbl_det .= ' &nbsp;&nbsp;&nbsp; Fright Coordinator <select name="fr_list" style="width:200px;">
							<option value="">Choose </option>';
							foreach($fc_list_res->result_array() as $fr_det)
							{
								$sel = ($pickup_empid==$fr_det['employee_id'])?'selected':'';
						
								$frm_tbl_det .= '<option '.$sel.' value="'.$fr_det['employee_id'].'"  >'.$fr_det['name'].' ('.($fr_det['tr_names']?implode(',',array_unique(explode(',',$fr_det['tr_names']))):'').' ) </option>';
							}
							$frm_tbl_det .= '</select>';
						}
						
						
						//pick up options
						/*$frm_tbl_det.='&nbsp;&nbsp;&nbsp; <b>OR</b>';
						$frm_tbl_det.='<select name="pickup_options" style="margin-left:2px;">';
						foreach($pickup_options as $i=>$options)
						{
							$frm_tbl_det.='<option value='.$i.'>'.$options.'</option>';
						}
						$frm_tbl_det.="</select>";*/
						$emp_list='';
						if($executives_list)
							$emp_list='Executives';
						else if($territory_manager)
							$emp_list.='Territory Manager';
							
						if($emp_list)
							$frm_tbl_det.='&nbsp;&nbsp;&nbsp; <b>OR</b>&nbsp;&nbsp;&nbsp;<a class="pickup_options" style="margin-left:2px;" href="javascript:void(0)" emp_list="'.$emp_list.'">'.$emp_list.'</a>';
							
						//destination pick up executives list
						$frm_tbl_det .='<span style="display:none;" class="excutives_list">&nbsp;&nbsp;&nbsp; : <select name="excutives_list">
						<option value="">Choose </option>';
						if($executives_list)
						{
							foreach($executives_list as $executive)
							{
								if($executive['role_name'] == 'FC')
									continue;
								$sel = ($pickup_empid==$executive['employee_id'])?'selected':'';
								$frm_tbl_det .=  '<option '.$sel.' value="'.$executive['employee_id'].'">'.$executive['name'].'-'.$executive['role_name'].'</option>';
							}
						}
						$frm_tbl_det .= ' </select></span>';
						
						//distination pick up territory manager
						
						$frm_tbl_det .='<span style="display:none;" class="territory_manager">&nbsp;&nbsp;&nbsp; : <select name="territory_manager" >
						<option value="">Choose </option>';
						if($territory_manager)
						{
							foreach($territory_manager as $tm)
							{
								$sel = ($pickup_empid==$tm['employee_id'])?'selected':'';
								$frm_tbl_det .=  '<option '.$sel.' value="'.$tm['employee_id'].'">'.$tm['name'].'-'.$tm['role_name'].'</option>';
							}
						}
						$frm_tbl_det .= ' </select></span>';
						
						$frm_tbl_det .= '</td>
						</tr>
						<tr style="display:none;" id="vehicle_no">
						<td>Vehicle number:</td>
						<td><input type="text" name="vehicle_num" value="'.$hndleby_vehicle_num.'" size="10"></td>
						</tr>
						<tr style="display:none;">
						<td>Start meter rate:</td>
						<td><input type="text" name="start_meter" value="'.$start_meter_rate.'" size="10"></td>
						</tr>
						<tr>
						<td>Shiping Date:</td>
						<td><input type="text" id="shiping_date" name="shiping_date" value="'.$shiping_date.'" size="10"></td>
						</tr>
						<tr>
						<td>Remark:</td>
						<td colspan="5"><textarea name="remark" cols="35" rows="3">'.$manifesto_remarks.'</textarea></td>
						</tr>
				</table>
				</div>
				';

			/*/<td style="display:none;" class="trans_opt_blk" id="other_trans" width="480">
					<span style="font-size:10px">Any Other Transport :<input type="text" name="other_driver" value="'.$hndleby_name.'" id="other_driver"></span>
					<span style="font-size:10px">Ph :<input type="text" value="'.$hndleby_contactno.'" name="other_driver_ph"></span>
				</td>
				
			<select name="buses_list" id="busues_list">
			<option value="choose">Choose Bus</option>';
			if($buses_list)
			{
				foreach($buses_list as $bus)
				{
					$sel = ($bus_id==$bus['id'])?'selected':'';
					$frm_tbl_det .='<option value="'.$bus['id'].'" '.$sel .'>'.$bus['name'].'('.$bus['contact_no'].')</option>';
				}
			}
						
						$frm_tbl_det .= '</select>';
						if($bus_dest_id && $bus_id)
						{
							$dest_address_list=$this->db->query("select * from pnh_transporter_dest_address where transpoter_id=?",$bus_id)->result_array();
							$frm_tbl_det .= '<select name="bus_det_add" style="margin:2px;"><option value="choose">choose Destination</option>';
							foreach($dest_address_list as $dest_det)
							{
								$sel = ($bus_dest_id==$dest_det['id'])?'selected':'';
								$frm_tbl_det .='<option value="'.$dest_det['id'].'" '.$sel .'>'.$dest_det['short_name'].'('.$dest_det['contact_no'].')</option>';
								
							}
						}	
			*/
						
			$sorted_towns = array_flip($sorted_towns);
						
			$output = array();
			$output['frm_transporter_det'] = $frm_tbl_det;
			$output['invlist'] = $invlist;
			$output['sorted_towns'] = $sorted_towns;
			$output['inv_town_link'] = $inv_town_link;
			$output['manifesto_list']=$this->db->query("select id from pnh_m_manifesto_sent_log where status=1")->result_array();
			
			echo json_encode($output);
		}
		
		/*function remove_invoice()
		{
			$user=$this->auth();
			$invoice_no=$this->input->post('invoice_no');
			
			$manifest_id_de=$this->db->query("select inv_manifesto_id from shipment_batch_process_invoice_link where invoice_no=?",$invoice_no)->result_array();
			$manifest_log_de=$this->db->query("select id,invoice_nos from pnh_manifesto_log where id=?",$manifest_id_de[0]['inv_manifesto_id'])->result_array();
			$manifest_invoices=explode(',',$manifest_log_de[0]['invoice_nos']);
			
			if (($key = array_search($invoice_no, $manifest_invoices)) !== false) {
				unset($manifest_invoices[$key]);
			}
			
			$manifest_invoices=array_filter($manifest_invoices);
			sort($manifest_invoices);
			
			$this->db->query("update pnh_manifesto_log set invoice_nos=? where id=?",array(implode(',',$manifest_invoices),$manifest_id_de[0]['inv_manifesto_id']));
			
			$data['status']='';
			$sql="update shipment_batch_process_invoice_link set inv_manifesto_id=0 where invoice_no=?;";
			$this->db->query($sql,$invoice_no);
			
			if($this->db->affected_rows())
			{
				$data['status']='1';
			}else{
				$data['status']='0';
			}
			
			echo json_encode($data);
		}*/
		
		
		/**
		 * update the manifesto detail
		 */
		function update_manifesto_detail()
		{
			$user=$this->auth(KFILE_ROLE);
			
			if(!$_POST)
				die;
				
			$user_det=$this->session->userdata("admin_user");
			//get the data throught a post
			$manifesto_id=0;
			$is_upd=0;
			$manifesto_id=$this->input->post("manifest_log_id");
			if($manifesto_id)
			{
				$is_upd=1;
				$redirect_page=$_SERVER['HTTP_REFERER'];
			}
			$manifesto_log_sent_id=$this->input->post("manifest_log_sent_id");
			$invoices_no=$this->input->post("invoice_nos");
			$remark=$this->input->post("remark");
			$role_type=$this->input->post('transport_opts');
			$tt_type = $this->input->post('tr_tranport_type');
			$vehicle_num=$this->input->post('vehicle_num');
			$start_meter=$this->input->post('start_meter');
			$shiping_date=$this->input->post('shiping_date');
			
			$ins_data = array();
			$ins_data['name'] = 'Manifesto for '.date('d/m/Y m:H:i');
			$ins_data['total_prints'] = 0;
			$ins_data['st_date'] = 0;
			$ins_data['en_date'] = 0;
			$ins_data['invoice_nos'] = implode(',',$invoices_no);
			if(!$manifesto_id)
			{
				$ins_data['created_on'] = date('Y-m-d H:i:s');
				$ins_data['created_by'] = $user['userid'];
				$this->db->insert("pnh_manifesto_log",$ins_data);
				$manifesto_id = $this->db->insert_id();
				
				foreach($invoices_no as $inv)
				{
					$trans_logprm=array();
					$trans_logprm['transid']=$this->db->query("select transid from king_invoice where invoice_no=? limit 1",$inv)->row()->transid;
					$trans_logprm['admin']=$user['userid'];
					$trans_logprm['time']=time();
					$trans_logprm['msg']='Manifesto created for this invoice ('.$inv.')';
					$this->db->insert("transactions_changelog",$trans_logprm);
				}
			}else 
			{
				$ins_data['modified_on'] = date('Y-m-d H:i:s');
				$ins_data['modified_by'] = $user['userid'];
				$this->db->where('id',$manifesto_id);
				$this->db->update("pnh_manifesto_log",$ins_data);
			}
			$invoices=implode(',',$invoices_no);
			$this->db->query("update shipment_batch_process_invoice_link set inv_manifesto_id=? where invoice_no in ($invoices) ",array($manifesto_id));
			$this->db->query("update shipment_batch_process_invoice_link set inv_manifesto_id=0 where invoice_no not in ($invoices) and inv_manifesto_id = ? ",array($manifesto_id));
				
			$driver_id=0;
			$driver_name='';
			$mobile_num='';
			$pick_up_by=0;
			$bus_id=0;
			$bus_dest_id=0;
			$type=0;
			$courier_id=0;
			if($role_type==7)
			{
				$driver_det=explode(':',$this->input->post("drivers_list"));
				$driver_id=$driver_det[0];
				$driver_name=$driver_det[1];
				$type=1;
			}elseif($role_type==6)
			{
				$driver_det=explode(':',$this->input->post("field_cordinators_list"));
				$driver_id=$driver_det[0];
				$driver_name=$driver_det[1];
				$type=2;
			}elseif($role_type==0)
			{
				$driver_name=$this->input->post("other_driver");
				$mobile_num=$this->input->post("other_driver_ph");
				$pick_up_by=$this->input->post('fr_list');
				
				if(!$pick_up_by && $_POST['excutives_list'])
				{
					$pick_up_by=$this->input->post('excutives_list');
				}else if(!$pick_up_by && $_POST['territory_manager'])
				{
					$pick_up_by=$this->input->post('territory_manager');
				}
				
				$bus_id=$this->input->post('buses_list');
				$bus_dest_id=$this->input->post('bus_det_add');
				$type=3;
			}else if($role_type==4)
			{
				$courier_det=explode(':',$this->input->post("courier_list"));
				
				$driver_name=$courier_det[1];
				$courier_id=$courier_det[0];
				$role_type=0;
				$type=4;
			}
			
			$param1=array();
			$param1['manifesto_id']=$manifesto_id;
			$param1['sent_invoices']=implode(',',$invoices_no);
			$param1['remark']=$remark;
			$param1['hndlby_roleid']=$role_type;
			$param1['hndleby_empid']=$driver_id;
			$param1['hndleby_name']=$driver_name;
			$param1['hndleby_contactno']=$mobile_num;
			$param1['pickup_empid']=$pick_up_by;
			$param1['hndleby_vehicle_num']=$vehicle_num;
			$param1['start_meter_rate']=$start_meter;
			$param1['shipment_sent_date']=$shiping_date;
			$param1['bus_id']=$bus_id;
			$param1['bus_destination']=$bus_dest_id;
			$param1['transport_type']=$tt_type;
			$param1['hndleby_courier_id']=$courier_id;
			$param1['hndlby_type']=$type;
			
			if(!$manifesto_log_sent_id)
			{
				$param1['sent_on']=cur_datetime();
				$param1['created_by']=$user_det['userid'];
				$this->db->insert("pnh_m_manifesto_sent_log",$param1);
				$sent_manifesto_logid = $this->db->insert_id();
			}
			else 
			{
				//$param1['sent_on']=cur_datetime();
				$param1['modified_on']=cur_datetime();
				$param1['modified_by']=$user_det['userid'];
				
				$this->db->where('id',$manifesto_log_sent_id);
				$this->db->update("pnh_m_manifesto_sent_log",$param1);
				
				$sent_manifesto_logid = $manifesto_log_sent_id;
			}
			
			$this->db->query("update pnh_invoice_transit_log set status = 9 where sent_log_id = ? ",$sent_manifesto_logid);
			foreach($invoices_no as $invno)
			{
				if($this->db->query("select count(*) as t from pnh_invoice_transit_log where sent_log_id = ? and invoice_no = ? ",array($sent_manifesto_logid,$invno))->row()->t)
				{
					$this->db->query("update pnh_invoice_transit_log set status = 0 where sent_log_id = ? and invoice_no = ? and status = 9 ",array($sent_manifesto_logid,$invno));
				}else
				{
					// insert invnos to transit table
					$ins_data = array();
					$ins_data['sent_log_id'] = $sent_manifesto_logid;
					$ins_data['invoice_no'] = $invno;
					$ins_data['ref_id'] = $driver_id;
					$ins_data['status'] = 0;
					$ins_data['logged_on'] = cur_datetime();
					$ins_data['logged_by'] = $user_det['userid'];
					$this->db->insert("pnh_invoice_transit_log",$ins_data);
					
				}
			}
			
			
			$this->db->query("delete from pnh_invoice_transit_log where sent_log_id = ? and status = 9 ",array($sent_manifesto_logid));
			
			
			
			
			if($is_upd)
			{
				$this->session->set_flashdata("erp_pop_info"," Manifesto updated");
				redirect($redirect_page);
			}else{
				$this->session->set_flashdata("erp_pop_info","Manifesto created for selected invoices");
				redirect(site_url('admin/pnh_pending_shipments'));
			}
		}
		
		function un_orderd_frans()
		{
			$orderd_frans_list=$this->db->query("SELECT c.franchise_id,pnh_franchise_id,login_mobile1,login_mobile2,c.franchise_name,GROUP_CONCAT(DATE(FROM_UNIXTIME(a.init)) ORDER BY a.init DESC ) AS ordered_on
											FROM king_transactions a
											JOIN king_orders b ON a.transid = b.transid
											JOIN pnh_m_franchise_info c ON a.franchise_id = c.franchise_id
											WHERE 1
											GROUP BY a.franchise_id
											ORDER BY c.franchise_name");
		
			// distinct franchise ids from thre log table
		
			$frids_inlog=$this->db->query("select GROUP_CONCAT(distinct franchise_id) as franchise_id from pnh_franchise_unorderd_log")->row_array();
		
			$frids=explode(',',$frids_inlog['franchise_id']);
		
			$data['frids']=$frids;
			$data['orderd_frans_list']=$orderd_frans_list;
			$data['page']='pnh_unorderdfran_log';
			$this->load->view('admin',$data);
				
		}
		/**
		 * get manifesto sent summary
		 */
		function get_manifesto_sent_summary()
		{
			$user=$this->auth(KFILE_ROLE);
			
			$manifesto_id=$this->input->post('manifesto_id');
			$mainfesto_sent_det=$this->erpm->get_manifesto_sent_log($manifesto_id);	
			
			if($mainfesto_sent_det)
			{
				
				echo '<table cellpadding="5" cellspacing="0" id="manifesto_sent_summary_tbl" class="datagrid">
						<thead>
							<tr>
								<th>Process by</th>
								<th>Process on</th>
								<th>Transporter</th>
								<th>Contact</th>
								<th>Type</th>
								<th>Pick up by</th>
								<th>Invoices</th>
								<th>Total invoices</th>
								<th>Remark</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody id="manifesto_sent_summary_tbl_tbbd">';
							foreach($mainfesto_sent_det as $sent_det)
							{
								$driver_name=($sent_det['hndlby_roleid'])?$sent_det['driver_name']:$sent_det['hndleby_name'];
								$mobile=($sent_det['hndlby_roleid'])?$sent_det['contact_no']:$sent_det['hndleby_contactno'];
								
								$emp_id=$sent_det['hndleby_empid'];
								$driver_link='';
								if($emp_id)
									$driver_link='<a href="'.site_url("/admin/view_employee/$emp_id").'" target="_blank">'.$driver_name.'</a>';
								else
									$driver_link=$driver_name;
								
								
								//$datetime=($sent_det['sent_on']))
								echo "<tr>";
									echo '<td>'.$sent_det['sent_by'].'</td>
										  <td>'.date("d/m/Y", strtotime($sent_det['sent_on'])).'</td>
										  <td>'.$driver_link.'</td>
										  <td>'.str_ireplace(',','',$mobile).'</td>
										  <td>'.$sent_det['role_type'].'</td>
										  <td>'.$sent_det['pick_up_by'].'</td>
										  <td>'.str_ireplace(',',' ',$sent_det['sent_invoices']).'</td>
										  <td>'.count(explode(',',$sent_det['sent_invoices'])).'</td>
										  <td>'.$sent_det['remark'].'</td>
										  <td><input type="button" value="Print" onclick="print_sent_manifesto('.$sent_det['manifesto_id'].','.$sent_det['id'].')"><br><span style="font-size:8px;"><b>Printed - '.$sent_det['is_printed'].'<b></span></td>';
									echo "</tr>";
							}
					echo '</tbody></table>';
			}
		}

		function view_manifesto_sent_log($srch_invoice=0,$from_date=0,$to_date=0,$status=0,$hub_id=0,$view_opt=1,$pg=0)
		{
			$user=$this->auth(PNH_SHIPMENT_MANAGER);
			
			$data['manifesto_send_smry_pagi'] = '';
			$mid=0;
			if(strlen($srch_invoice) >=10)
			{
				
			}else{
				$mid=$srch_invoice;
				$srch_invoice=0;
			}
			
			//get the manifesto ids by hub
			$m_ids_by_hub=array();
			if($hub_id)
			{
				$hub_ids_det=$this->db->query("select c.inv_manifesto_id from pnh_t_tray_territory_link a
							join pnh_t_tray_invoice_link b on b.tray_terr_id=a.tray_terr_id
							join shipment_batch_process_invoice_link c on c.invoice_no =b.invoice_no
							where a.territory_id=? group by c.inv_manifesto_id",$hub_id)->result_array();
				
				foreach($hub_ids_det as $id)
				{
					array_push($m_ids_by_hub,$id['inv_manifesto_id']);
				}
			}
			
			
			//get the total rows of manifesto set log
			$sql="select count(*) as ttl from pnh_m_manifesto_sent_log";
			
			$sql.=" where 1";
			
			if(($from_date && $to_date) && ($from_date!='0000-00-00' || $to_date!='0000-00-00') )
			{
				$sql.=" and (date(sent_on) >=? &&  date(sent_on) <= ?) ";
				$data['manifesto_send_smry_ttl'] = $this->db->query($sql,array($from_date,$to_date))->row()->ttl;
			}else if($srch_invoice){
				$sql.=" and sent_invoices like ? ";
				$srch_invoice='%'.$srch_invoice.'%';
				$data['manifesto_send_smry_ttl'] = $this->db->query($sql,array($srch_invoice))->row()->ttl;
			}else if($status){
				$sql.=" and status = ? ";
				$data['manifesto_send_smry_ttl'] = $this->db->query($sql,array($status))->row()->ttl;
			}else if($mid){
				$sql.=" and id = ? ";
				$data['manifesto_send_smry_ttl'] = $this->db->query($sql,array($mid))->row()->ttl;
			}else if($m_ids_by_hub){
				$sql.=" and manifesto_id  in (".implode(',',$m_ids_by_hub).")";
				$data['manifesto_send_smry_ttl'] = $this->db->query($sql)->row()->ttl;
			}else{
					$data['manifesto_send_smry_ttl'] = $this->db->query($sql)->row()->ttl;
			}
			//get the total rows of manifesto set log end
			
			//get the manifesto sent log details
			$mainfesto_sent_det=$this->erpm->get_manifesto_sent_log_det(0,$pg,$from_date,$to_date,$srch_invoice,$status,$mid,$m_ids_by_hub,$view_opt);
			
			if($view_opt==2)
			{
				$dn=$this->download_manifesto($mainfesto_sent_det);
				if($dn)
					$this->session->set_flashdata("erp_pop_info","Selected date range manifesto details downloaded");
				else
					$this->session->set_flashdata("erp_pop_info","No data found for download");
			}
			
			//get status summary
			$data['status_summary']=$this->db->query('select status,count(*) as ttl from pnh_m_manifesto_sent_log group by status order by status')->result_array();
			
			//pagination block
			$this->load->library('pagination');
			$config['base_url'] = site_url('admin/view_manifesto_sent_log/'.$srch_invoice.'/'.$from_date.'/'.$to_date.'/'.$status.'/'.$hub_id.'/'.$view_opt);
			$config['total_rows'] = $data['manifesto_send_smry_ttl'];
			$config['uri_segment'] = 9;
			$config['per_page'] = 10;
			
			$this->config->set_item('enable_query_strings',false);
			$this->pagination->initialize($config);
			$data['manifesto_send_smry_pagi'] = $this->pagination->create_links();
			$this->config->set_item('enable_query_strings',true);
			//pagination block end
			
			
			$data['mainfesto_sent_det']=$mainfesto_sent_det;
			$data['page']='manifesto_sent_log';
			$this->load->view('admin',$data);
		}

		function jx_addholiday()
		{
			$user=$this->auth_pnh_employee();
			$role_id=$this->get_jobrolebyuid($user['userid']);
			if($role_id<=3)
			{
				
				$emp_id = $this->get_empidbyuid($user['userid']);
				$emp_fr_holidy=$this->input->post('emp_for_holiday');
				$holidy_stdt=$this->input->post('hol_stdate');
				$holidy_remarks=$this->input->post('holidy_remarks');
				
				$st_dt=explode('/',$holidy_stdt);
				$d=$st_dt[0];
				$m=$st_dt[1];
				$y=$st_dt[2];
				$st_dte=mktime(0,0,0,$m,$d,$y);
				$st_dt=date('Y-m-d',$st_dte);
				
				
				$holidy_endt=$this->input->post('hol_endate');
				$en_dt=explode('/',$holidy_endt);
				$d=$en_dt[0];
				$m=$en_dt[1];
				$y=$en_dt[2];
				$en_dte=mktime(0,0,0,$m,$d,$y);
				$en_dt=date('Y-m-d',$en_dte);
				
				$h=0;
				$sql="insert into pnh_m_employee_leaves(emp_id,remarks,holidy_stdt,holidy_endt,is_active,created_by,created_on)values(?,?,?,?,1,?,now())";
				$this->db->query($sql,array($emp_fr_holidy,$holidy_remarks,$st_dt,$en_dt,$emp_id));
				$h+=$this->db->affected_rows();
				$output=array();
				if($h)
				{
					$output['status']="success";
				}
			  	else
			  	{
					$output['status']="error";
				}
				echo json_encode($output);
			}
		}
		
		function jx_loadholiday()
		{
			
			$sql=("SELECT DATE_FORMAT(a.holidy_stdt,'%d/%m/%y') AS holidy_stdt ,DATE_FORMAT(a.holidy_endt,'%d/%m/%y') AS holidy_endt,DATE_FORMAT(a.created_on,'%d/%m/%y %h:%i %p') AS created_on,b.name,a.remarks,c.name AS createdbyname,DATEDIFF(a.holidy_endt,a.holidy_stdt) AS ttl_days
					FROM `pnh_m_employee_leaves`a
					JOIN m_employee_info b ON b.employee_id=a.emp_id
					JOIN m_employee_info c ON c.employee_id=a.created_by
					where is_active=1
					order by a.holidy_endt asc"); 
		  	$res = $this->db->query($sql);
	     	$output = array();
	     	if($res->num_rows())
	     	{
	     		$output['holiday_list'] = $res->result_array();
	     		$output['status'] = 'success';
	     	}
	     	else
	     	{
	     		$output['status'] = 'error';
	     	}
	     	echo json_encode($output);
		 }
	
		/*
		 *function to get panel alerts  
		 */
		function get_panel_alerts()
		{
			// check for role 
			$user = $this->erpm->auth();
			
			$process = 1;
			if($this->erpm->auth(true,true))
				$process = 0;	
			else if(!$this->erpm->auth(CALLCENTER_ROLE,true))
				$process = 0;
			
			if($user['userid'] == 6)
				$process = 1;

			$output = array();
			
			// check for any unclosed pnh order quotes
			$output['quote_list'] = array(); 
			
			if($process)
			{
				$quote_list_res = $this->db->query("select a.franchise_id,b.franchise_name,count(*) as total_pen,
													group_concat(a.created_on+respond_in_min*60 order by (a.created_on+respond_in_min*60)) as respond_in
													from pnh_quotes a
													join pnh_m_franchise_info b on a.franchise_id = b.franchise_id 
													where quote_status = 0 
													group by franchise_id 
													having respond_in < unix_timestamp() ");
				if($quote_list_res->num_rows())
				{
					foreach($quote_list_res->result_array() as $row)
					{
						if($row['franchise_id'])
						{
							$fr_det = $this->db->query("select * from pnh_m_franchise_info where franchise_id = ? ",$row['franchise_id'])->row_array();
							array_push($output['quote_list'],'<a href="javascript:void(0)" onclick="handle_quote_request_call('.$row['franchise_id'].')"><b style="font-size:12px;">'.$row['total_pen']." Calls</b> Pending for - ".$fr_det['franchise_name'].'</a>');	
						}
					}
					$output['status'] = 'success';
				}else
				{
					$output['status'] = 'error';
				}
			}else
			{
				$output['status'] = 'error';
			}
			
			echo json_encode($output);
		}
		
		/**
		 * remove the pnh deal linked product
		 * @param unknown_type $item_id
		 */
		function remove_pnhdeal_linked_prd($item_id,$prd_id,$is_sit=0)
		{
			$this->erpm->auth(true);
			$user_det=$this->session->userdata("admin_user");
			
			$site=0;
			if($is_sit)
				$site=1;
			
			$sql="select p.mrp,l.product_id,l.qty,p.product_name 
						from m_product_deal_link l 
						join m_product_info p on p.product_id=l.product_id 
						where itemid=? and l.product_id=?";
			
			$check_pnhdeal_linked_prd=$this->db->query($sql,array($item_id,$prd_id))->result_array();
			
			if($check_pnhdeal_linked_prd)
			{
				$sql1="select * from m_product_deal_link where itemid=? and product_id=?;";
				$pnh_deal_prd_link_det=$this->db->query($sql1,array($item_id,$prd_id))->row_array();
				
				$sql3="insert into t_upd_product_deal_link_log(itemid,product_id,product_mrp,qty,perform_on,perform_by,is_updated,is_sit)values(?,?,?,?,?,?,?,?);";
				$this->db->query($sql3,array($pnh_deal_prd_link_det['itemid'],$pnh_deal_prd_link_det['product_id'],$pnh_deal_prd_link_det['product_mrp'],$pnh_deal_prd_link_det['qty'],date('Y-m-d H:i:s'),$user_det['userid'],'1',$site));
				
				$sql2="delete from m_product_deal_link where itemid=? and product_id=?;";
				$this->db->query($sql2,array($item_id,$prd_id));
				$this->session->set_flashdata("erp_pop_info","Product removed from this deal");
				if($is_sit)
				{
					redirect('/admin/deal/'.$item_id);
				}else{
					redirect('/admin/pnh_editdeal/'.$item_id);
				}
				
			}else{
				$this->session->set_flashdata("erp_pop_info","This products not present this deal");
			}
		}
		
		/**
		 * function to generate Sales report for Tally Import 
		 */
		function export_salesfortally()
		{
			$this->erpm->auth(FINANCE_ROLE);
			
			$stdate=$this->input->post('stdate')?$this->input->post('stdate'):date('Y-m-d');
			$endate=$this->input->post('endate')?$this->input->post('endate'):date('Y-m-d');
			$sales_by = $this->input->post('endate')?$this->input->post('sales_by'):'';
			
			if($_POST)
				$this->erpm->do_gen_salesreportfortally($stdate,$endate,$sales_by);
			
			
			$data['sales_by'] = $sales_by;
			$data['stdate'] = $stdate;
			$data['endate'] = $endate;
			$data['title'] = 'Export Sales Report for Tally Import';
			$data['page'] = 'export_salesfortally';
			$this->load->view('admin',$data);
		}
		
		/**
		 * get the executives and field cordinators by territory
		 */
		function get_executives_and_fc()
		{
			$trr_id=$this->input->post('territory_id');
			$executives_list=$this->erpm->get_executives_by_territroy($trr_id);
			
			echo '<select name="excutives_list">';
				echo '<option value="Choose">Choose</option>';
				if($executives_list)
				{
					foreach($executives_list as $executive)
					{
						echo '<option value="'.$executive['employee_id'].'">'.$executive['name'].'-'.$executive['role_name'].'</option>';
					}
					
				}
			echo '</select>';
		}
		
		function deal_product_link_update_log()
		{
			$this->erpm->auth();
			
			$sql="select a.*,b.product_name,c.username,d.name as d_name from t_upd_product_deal_link_log as a
						join m_product_info as b on b.product_id=a.product_id
						join king_dealitems as d on d.id=a.itemid
						join king_admin c on c.id=a.perform_by
						where is_sit=1
						order by a.perform_on desc;";
			$log_details=$this->db->query($sql)->result_array();
			
			$data['log_details']=$log_details;
			$data['page']='deal_product_link_update_log';
			$this->load->view('admin',$data);
		}
		
		//to load all brands
		function jx_getallbrands()
		{
			$output=array();
			$allbrands_res=$this->db->query("select * from king_brands order by name asc");
			if($allbrands_res->num_rows())
			{
				$output['brand_list']=$allbrands_res->result_array();
				$output['status']='success';
			}else
			{ 
				$output['status']='error';
				$output['message']='No Brand found';
			}
			echo json_encode($output);
			
		}
		//to load all catagory
		function jx_getallcategory()
		{
			$output=array();
			$allcats_res=$this->db->query("select * from king_categories order by name asc");
			if($allcats_res->num_rows())
			{
				$output['cat_list']=$allcats_res->result_array();
				$output['status']='success';
			}else
			{
				$output['status']='error';
				$output['message']='No Category found';
			}
			echo json_encode($output);
		}
		
		/**
		 * function to load catagory list based on brand selection
		 * @param unknown_type $brandid
		 */
		function loadcat_bybrand($brandid=9999999999)
		{
			$brandid =(!is_numeric($brandid))?9999999999:$brandid;
			$output=array();
			$cat_list=$this->db->query("SELECT a.catid,a.brandid,b.name AS brand_name,c.name AS category_name
											FROM king_deals a
											JOIN king_brands b ON b.id=a.brandid
											JOIN king_categories c ON c.id=a.catid
											WHERE b.id=?
											GROUP BY c.id",$brandid);
			if($cat_list->num_rows())
			{
				$output['cat_list']=$cat_list->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']='error';
				$output['message']='No data Found';
			}
			echo json_encode($output);
		}
		
		/**
		 * function to load brands based on category selection
		 * @param unknown_type $catid
		 */

		function loadbrand_bycat($catid='')
		{
			$catid =(!is_numeric($catid))?9999999999:$catid;
			$output=array();
			$brand_list=$this->db->query("SELECT a.catid,a.brandid,b.name AS brand_name,c.name AS category_name
											FROM king_deals a
											JOIN king_brands b ON b.id=a.brandid
											JOIN king_categories c ON c.id=a.catid
											WHERE c.id=?
											GROUP BY b.id",$catid);
			if($brand_list->num_rows())
			{
				$output['brand_list']=$brand_list->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']='error';
				$output['message']='No data Found';
			}
			echo json_encode($output);
			
		}
		
		/**
		 * get the details pnh invoices transit status
		 */
		function shipments_transit_log()
		{
			$this->erpm->auth(PNH_SHIPMENT_MANAGER|CALLCENTER_ROLE);
			$data['page']='pnh_shipments_transit_log';
			$this->load->view('admin',$data);
		}
		
		function pnh_jx_loadfranchisemembrid($pnh_fid)
		{
			$pnh_fid =(!is_numeric($pnh_fid))?9999999999:$pnh_fid;
			$output=array();
			$fran_memberid=$this->db->query("SELECT a.pnh_member_id,f.pnh_franchise_id FROM `pnh_member_info`a
					JOIN pnh_m_franchise_info f ON f.franchise_id=a.franchise_id
					WHERE f.pnh_franchise_id=?",$pnh_fid);
			if($fran_memberid->num_rows())
			{
				$output['mids']=$fran_memberid->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']='error';
				$output['message']='No data Found';

			}
			echo json_encode($output);

		}
		function jx_getshipments_transit_log()
		{
			$this->erpm->auth();
			$filter_param=$this->input->post('param');
			$parameters=array();
			$latest_shiping_date='';
			$shipments_date='';
			$cond='';
			if(isset($filter_param['hubid']) && $filter_param['hubid']!='')
			{
				$cond.="join pnh_t_tray_invoice_link m on m.invoice_no=d.invoice_no
						join pnh_t_tray_territory_link n on n.tray_terr_id=m.tray_terr_id";
						
			}
			
			
			if((!isset($filter_param['st_date']) || !isset($filter_param['en_date'])) && (!isset($filter_param['manifesto_id'])))
				$latest_shiping_date=$this->db->query("select date(logged_on) as latest_date from pnh_invoice_transit_log where status=1 order by id desc limit 1")->row_array();
			
			$sql="select date(sent_on) as sent_date,a.hndleby_empid,a.id,g.franchise_name,b.name,c.role_name,group_concat(distinct d.invoice_no) as invoices_number,
				  t.territory_name,tw.town_name,a.hndleby_name,t.id as terr_id,tw.id as twn_id,a.bus_id,j.name as bus_name,j.contact_no as bus_contact_no,a.bus_id,a.pickup_empid,a.hndlby_type,
				  d.is_acknowleged
							from pnh_m_manifesto_sent_log as a 
							left join m_employee_info as b on b.employee_id=a.hndleby_empid
							left join m_employee_roles c on c.role_id=a.hndlby_roleid
							left join pnh_transporter_info j on j.id=a.bus_id
							left join m_courier_info k on k.courier_id=a.hndleby_courier_id
							join shipment_batch_process_invoice_link d on d.inv_manifesto_id=a.manifesto_id
							join king_invoice  f on f.invoice_no=d.invoice_no
							join king_transactions h on h.transid=f.transid
							join pnh_m_franchise_info g on g.franchise_id=h.franchise_id
							join pnh_m_territory_info t on t.id=g.territory_id
							join pnh_towns tw on tw.id=g.town_id
							join pnh_invoice_transit_log l on l.sent_log_id = a.id and l.status > 0
							$cond 
							where 1 ";
			
			if($latest_shiping_date)
			{
				$sql.=" and date(d.shipped_on)=? ";
				$parameters[]=$latest_shiping_date['latest_date'];
				$shipments_date=format_date($latest_shiping_date['latest_date']);
			}
			
			if(isset($filter_param['st_date']) && isset($filter_param['en_date']))
			{
				$sql.=" and date(d.shipped_on) >= ? and date(d.shipped_on) <= ? ";
				$parameters[]=$filter_param['st_date'];
				$parameters[]=$filter_param['en_date'];
				$shipments_date=format_date($filter_param['st_date']).' to '.format_date($filter_param['en_date']);
			}
			
			if(isset($filter_param['terri_id']) && $filter_param['terri_id']!='')
			{
				$sql.=" and t.id=? ";
				$parameters[]=$filter_param['terri_id'];
			}
			
			if(isset($filter_param['town_id']) && $filter_param['town_id']!='')
			{
				$sql.=" and tw.id=? ";
				$parameters[]=$filter_param['town_id'];
			}
			
			if(isset($filter_param['driver_id']) && $filter_param['driver_id']!='')
			{
				$sql.=" and hndleby_empid=? ";
				$parameters[]=$filter_param['driver_id'];
			}
			
			if(isset($filter_param['bus_id']) && $filter_param['bus_id']!='')
			{
				$sql.="and bus_id=? ";
				$parameters[]=$filter_param['bus_id'];
			}
			
			if(isset($filter_param['manifesto_id']) && $filter_param['manifesto_id']!='')
			{
				$sql.="and a.id=? ";
				$parameters[]=$filter_param['manifesto_id'];
			}
			
			if(isset($filter_param['hubid']) && $filter_param['hubid']!='')
			{
				$sql.=" and n.territory_id=?";
				$parameters[]=$filter_param['hubid'];
			}
			
			$sql.="	group by g.territory_id,g.town_id,a.hndlby_type,a.hndleby_empid,a.hndleby_courier_id,a.bus_id
					order by d.shipped_on desc,t.territory_name,tw.town_name,bus_name,courier_name ";
			
			
			if($parameters)
				$pnh_shipmets_transit_log=$this->db->query($sql,$parameters)->result_array();
			else
				$pnh_shipmets_transit_log=$this->db->query($sql)->result_array();
			
			 
			$data['pnh_shipmets_transit_log']=$pnh_shipmets_transit_log;
			
			$terr_arr=array();
			$towns_arr=array();
			$driver_list=array();
			$buses_list=array();
			$invoices_list=array();
			
			foreach($pnh_shipmets_transit_log as $transit_log)
			{
				$terr_arr[$transit_log['terr_id']]=$transit_log['territory_name'];
				$towns_arr[$transit_log['twn_id']]=$transit_log['town_name'];
				
				if($transit_log['hndleby_empid'])
					$driver_list[$transit_log['hndleby_empid']]=$transit_log['name'];
				
				if($transit_log['bus_id'])
					$buses_list[$transit_log['bus_id']]=$transit_log['bus_name'];
				
				$invoices_list[]=$transit_log['invoices_number'];
			}
			
			$invoices=implode(',',$invoices_list);
			
			$hublist='';
			if($invoices)
			{
				$hubsql="select b.territory_id,c.hub_name from pnh_t_tray_invoice_link a
							join pnh_t_tray_territory_link b on b.tray_terr_id=a.tray_terr_id
							join pnh_deliveryhub c on c.id=b.territory_id
					where a.invoice_no in ($invoices)
					group by b.territory_id;";
				$hublist=$this->db->query($hubsql)->result_array();
			}
			
			if(!$buses_list)
				$buses_list[0]=0;
			
			if(!$driver_list)
				$driver_list[0]=0;
			
			$output = array();
			$output['pnh_shipmets_transit_log'] = $this->load->view("admin/body/jx_pnhshipments_transit_log",$data,true);
			$output['territory_list']=$terr_arr;
			$output['towns_list']=$towns_arr;
			$output['shiped_date']=$shipments_date;
			$output['driver_list']=$driver_list;
			$output['bus_list']=$buses_list;
			$output['hub_list']=$hublist;
			echo json_encode($output);
		}
		
		function jx_invoicetransit_det()
		{
			$this->erpm->auth();
			$output = array();
			$invno = $this->input->post('invno');
			
			
			// invoice transit log details by time 
			
			/*$inv_transit_log_res = $this->db->query("select a.sent_log_id,a.invoice_no,a.logged_on,a.status,if(ref_id,b.name,c.hndleby_name) as hndleby_name,
														if(ref_id,b.contact_no,c.hndleby_contactno) as hndleby_contactno
														from pnh_invoice_transit_log a
														left join m_employee_info b on a.ref_id = b.employee_id
														join pnh_m_manifesto_sent_log c on c.id = a.sent_log_id
														where invoice_no = ?   
														order by a.id desc  ",$invno);*/
			
			$franchise_details=$this->db->query("select a.invoice_no,d.franchise_name,e.town_name from shipment_batch_process_invoice_link a 
															join king_invoice b on b.invoice_no=a.invoice_no 
															join king_transactions c on c.transid=b.transid
															join pnh_m_franchise_info d on d.franchise_id=c.franchise_id
															join pnh_towns e on e.id = d.town_id
														where a.invoice_no=? group by a.invoice_no",$invno)->row_array();;
			
			
			
			
			$inv_transit_log_res = $this->db->query("select a.id,a.ref_id,a.sent_log_id,a.invoice_no,a.logged_on,a.status,if(ref_id,b.name,d.name) as hndleby_name,
			 											if(ref_id,b.contact_no,d.contact_no) as hndleby_contactno,c.id as manifesto_id,c.hndleby_empid,c.bus_id,c.bus_destination,
														c.office_pickup_empid,c.pickup_empid,b.job_title2,c.hndlby_type,c.hndleby_courier_id,a.logged_by,c.alternative_contactno
															from pnh_invoice_transit_log a
															left join m_employee_info b on a.ref_id = b.employee_id
															join pnh_m_manifesto_sent_log c on c.id = a.sent_log_id
															left join pnh_transporter_info d on d.id=c.bus_id
															where invoice_no = ?
															order by a.id desc  ",$invno);
			$log_msg='';
			$sms='';
			$from_det='';
			if($inv_transit_log_res->num_rows())
			{
				$inv_transit_log_arr = array();
				foreach($inv_transit_log_res->result_array() as $inv_transit_log)
				{ 
					$log_msg='';
					$sms='';
					$alternative_number='';
					$inv_last_updated_on = format_datetime($inv_transit_log['logged_on']);
					$inv_last_updated_by = $inv_transit_log['hndleby_name'];
					$contact_no = $inv_transit_log['hndleby_contactno'];
					if($inv_transit_log['alternative_contactno'])
						$alternative_number=$inv_transit_log['alternative_contactno'];
					//transport type manage
					if($inv_transit_log['hndlby_type']==4)
					{
						$courier_det=$this->db->query("select * from m_courier_info where courier_id=?",$inv_transit_log['hndleby_courier_id'])->row_array();
						$inv_last_updated_by = $courier_det['courier_name'];
						$contact_no = '';
						if(isset($courier_det['contact_no']))
							$contact_no=$courier_det['contact_no'];
					}else if(($inv_transit_log['hndlby_type']==1 || $inv_transit_log['hndlby_type']==2) && $inv_last_updated_by=='')
					{
						$emp_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['hndleby_empid'])->row_array();
						$inv_last_updated_by=$emp_det['name'];
						$contact_no=$emp_det['contact_no'];
					}
					
					//track message manage
					if($inv_transit_log['status'] <= 1)
					{
						$inv_last_status = 'In Transit';
						
						//if it is transportaion msg
						if($inv_transit_log['bus_id'])
						{
							//get the bus_details
							$bus_details=$this->db->query("select b.name as bus_name,b.contact_no,a.short_name,a.contact_no as ds_contact_no,a.type from pnh_transporter_dest_address a
														join pnh_transporter_info b on b.id=a.transpoter_id
														where a.id=?",$inv_transit_log['bus_destination'])->row_array();
							
							//get the office pick up details
							$office_pickiup_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['office_pickup_empid'])->row_array();
							
							
							//get the destinaton pick up
							$destination_pickiup_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['pickup_empid'])->row_array();
							
							$type='';
							if($bus_details['type']==1)
								$type='Bus transport';
							else if($bus_details['type']==2)
								$type='Cargo transport';
							else if($bus_details['type']==3)
								$type='General package transport';
							
							$transport_name=$bus_details['bus_name'].' '.$type.' ('.$bus_details['contact_no'].')';
							$destination_name=$bus_details['short_name'].' ('.$bus_details['ds_contact_no'].')';
							
							if(!$office_pickiup_det)
							{
								$office_pickiup_det=$this->db->query("select b.* from pnh_m_manifesto_sent_log a join king_admin b on b.id=a.modified_by where a.id=?",$inv_transit_log['manifesto_id'])->row_array();
								$log_msg="<b>Shipment Sent via : </b>".$transport_name." to ".$destination_name."<br>"."<b>by : </b>".$office_pickiup_det['name']."(".$office_pickiup_det['phone'].")<br> <b>To be collected by @ destination :</b>".$destination_pickiup_det['name'].'('.$destination_pickiup_det['contact_no'].')';
							}else{
							
								$log_msg="<b>Shipment Sent via :</b>  ".$transport_name." to ".$destination_name."<br>"."<b>office pickup by : </b>".$office_pickiup_det['name']."(".$office_pickiup_det['contact_no'].")<br> <b>To be collected by @ destination :</b>".$destination_pickiup_det['name'].'('.$destination_pickiup_det['contact_no'].')';
							}
						
						}else if($inv_transit_log['hndleby_empid'])
						{
							//get the office pick up details
							$driver_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['hndleby_empid'])->row_array();
							
							$type=($driver_det['job_title2']==7)?'Driver ':'Fright Co-oridnator ';
							$driver_name=$type."<b>".$driver_det['name'].'('.$driver_det['contact_no'].')</b>';
							
							$log_msg="<b>Shipment Sent via : </b>".$driver_name." to "."<b>".$franchise_details['town_name']."</b>";
						
						}else if($inv_transit_log['hndlby_type']==4)
						{
							$log_msg="<b>Shipment Sent via Courier : </b>".$courier_det['courier_name']." to "."<b>".$franchise_details['town_name']."</b>";
							$inv_last_updated_by = $courier_det['courier_name'];
							$contact_no = '';
						}
						
						
												
						
					}if($inv_transit_log['status'] == 2 || $inv_transit_log['status'] == 5  )
					{
						if($inv_transit_log['status']==2)
							$inv_last_status = 'Handed over';
						else
							$inv_last_status='Picked';
						
						if($inv_transit_log['hndleby_empid'])
						{
							if($inv_transit_log['hndleby_empid']!=$inv_transit_log['ref_id'])
							{
								//get the sms 
								$last_ref_id='';
								$last_ref_id=$this->db->query("select * from pnh_invoice_transit_log where status=2 and ref_id!=? and invoice_no=? and id < ? order by logged_on desc limit 1",array($inv_transit_log['ref_id'],$invno,$inv_transit_log['id']))->row_array();
								
								if(!$last_ref_id)
								{
									$last_ref_id=$this->db->query("select * from pnh_invoice_transit_log where status=1 and ref_id=? and invoice_no=? order by logged_on desc limit 1",array($inv_transit_log['hndleby_empid'],$invno))->row_array();
								}
								
								$sms=$this->erpm->get_shipements_update_sms(array($last_ref_id['ref_id'],9));
								$from_det=$this->erpm->get_shipements_update_sms_from(array($last_ref_id['ref_id'],9));
								
								$destination_pickiup_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['ref_id'])->row_array();
								$log_msg="Shipment Collected by ".$destination_pickiup_det['name'].'('.$destination_pickiup_det['contact_no'].')';
							
							}else{
								
								
								$driver_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['hndleby_empid'])->row_array();
								
								$sms=$this->erpm->get_shipements_update_sms(array($inv_transit_log['hndleby_empid'],9));
								$from_det=$this->erpm->get_shipements_update_sms_from(array($inv_transit_log['hndleby_empid'],9));
								 
								$type=($driver_det['job_title2']==7)?'Driver ':'Fright Co-oridnator ';
								$driver_name=$type.$driver_det['name'].'('.$driver_det['contact_no'].')';
								
								$handover_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['ref_id'])->row_array();
								
								// Shipment handed over to Kiran(91991) |  234124 | Kumar
								$log_msg=$driver_name." Shipment handed over to ".$handover_det['name'].'('.$handover_det['contact_no'].')';
							}
							
						}else if($inv_transit_log['bus_id']){
							
							if($inv_transit_log['pickup_empid']!=$inv_transit_log['ref_id'])
							{
								$last_ref_id='';
								$last_ref_id=$this->db->query("select * from pnh_invoice_transit_log where (status=2 or status=5)  and ref_id!=? and invoice_no=? and id <= ? order by logged_on desc limit 1",array($inv_transit_log['ref_id'],$invno,$inv_transit_log['id']))->row_array();
							
								$sms=$this->erpm->get_shipements_update_sms(array($last_ref_id['ref_id'],9));
								$from_det=$this->erpm->get_shipements_update_sms_from(array($last_ref_id['ref_id'],9));
							}
							else
							{
								$last_ref_id='';
								$last_ref_id=$this->db->query("select * from pnh_invoice_transit_log where status=2 and ref_id=? and invoice_no=? and id <= ? order by logged_on desc limit 1",array($inv_transit_log['pickup_empid'],$invno,$inv_transit_log['id']))->row_array();
								
								$msg_type=0;
								if($last_ref_id)
								{
									$last_ref_id='';
									$last_ref_id=$this->db->query("select * from pnh_invoice_transit_log where status=2  and ref_id!=? and invoice_no=? and id <= ? order by logged_on desc limit 1",array($inv_transit_log['ref_id'],$invno,$inv_transit_log['id']))->row_array();
									$msg_type=9;
									$last_ref_id=$last_ref_id['ref_id'];
								}else{
									
									$msg_type=8;
									$last_ref_id=$inv_transit_log['ref_id'];
								}
								
								
								$sms=$this->erpm->get_shipements_update_sms(array($last_ref_id,$msg_type));
								$from_det=$this->erpm->get_shipements_update_sms_from(array($last_ref_id,$msg_type));
							}
							
							$destination_pickiup_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['ref_id'])->row_array();
							$log_msg="Shipment Collected by ".$destination_pickiup_det['name'].'('.$destination_pickiup_det['contact_no'].')';
							// Shipment Collected by Kumar | 234124 | Kumar
						}
						
						
						
					}else if($inv_transit_log['status'] == 3)
					{
						$inv_last_status = 'Delivered';
						
						$driver_det=$this->db->query("select b.name,b.contact_no,b.job_title2 from pnh_invoice_transit_log  a join m_employee_info b on b.employee_id=a.ref_id where status=3 and invoice_no=?",$invno)->row_array();
						
						if($driver_det)
						{
								if($driver_det['job_title2']==4)
									$type=" Territory Manager ";
								else if($driver_det['job_title2']==5)
									$type=" Bussiness Executive ";
								else if($driver_det['job_title2']==6)
									$type=" Fright Co-oridnator ";
								else if($driver_det['job_title2']==7)
									$type=" Driver ";
								
								$driver_name=$type."<b>".$driver_det['name'].'('.$driver_det['contact_no'].')</b>';
								
								$log_msg=$driver_name." Shipment <span style='color:green;'><b>Delivered</b></span> to <b>".$franchise_details['franchise_name']."</b>";
								
								$sms=$this->erpm->get_shipements_update_sms(array($inv_transit_log['ref_id'],10));
								$from_det=$this->erpm->get_shipements_update_sms_from(array($inv_transit_log['ref_id'],10));
						
						}else if(($inv_transit_log['logged_by'] && $inv_transit_log['ref_id']==0) || $inv_transit_log['hndlby_type']==4)
						{
							$user=$this->db->query("select * from king_admin where id=?",$inv_transit_log['logged_by'])->row_array();
							$log_msg=" Shipment  manully marked as <span style='color:green;'><b>Delivered</b></span> to <b>".$franchise_details['franchise_name']."</b><br> Marked by <b>".$user['username'];
						}
							
						
						// Shipment Delivered to Sri Sai Medicals |  234124 | Kiran
					}else if($inv_transit_log['status'] == 4)
					{
						$log_msg="Shipment Marked as Returned";
						$emp_det=$this->db->query("select * from m_employee_info where employee_id=?",$inv_transit_log['ref_id'])->row_array();
						$inv_last_updated_by = $emp_det['name'];
						$contact_no=$emp_det['contact_no'];
						
						$inv_last_status = 'Marked for Return';
						
						$sms=$this->erpm->get_shipements_update_sms(array($inv_transit_log['ref_id'],11));
						$from_det=$this->erpm->get_shipements_update_sms_from(array($inv_transit_log['ref_id'],11));
						// Shipment Marked  as Returned |  234124 | Kiran
					}
					
					
					$from='';
					if($from_det)
						$from='from : '.$from_det['contact_no'];
					
					if(!$sms)
					{
						$sms='system';
						$from='';
					}
					$output['Franchise_name']=$franchise_details['franchise_name'];
					$output['town_name']=$franchise_details['town_name'];
					$output['manifesto_id']=$inv_transit_log['sent_log_id'];
					$inv_transit_log_arr[] = array($inv_transit_log['status'],$inv_last_status,$inv_last_updated_by,$inv_last_updated_on,$contact_no,$log_msg,$sms,$alternative_number,$from);
				}
				
				$output['transit_log'] = $inv_transit_log_arr;
				$output['status'] = 'success';
			}else
			{
				$output['error'] = 'No transit log found for this invoice';
				$output['status'] = 'error';
			}
			$output['invoice_no'] = $invno;
			
			echo json_encode($output);
		}
		
		/**
		 * function to load all brands of selected menu
		 * @param unknown_type $menuid
		 */
		
		function jx_load_allbrandsbycat($catid=0)
		{
			$output=array();
			$cond='';
			/*if($menuid)
				$cond.='and menuid='.$menuid;*/
			if(	$catid)
					$cond.='and catid= '.$catid;			
			$brandlist_res=$this->db->query("SELECT DISTINCT menuid,brandid,b.name,catid
												FROM `king_deals`a
												JOIN  king_categories c ON c.id=a.catid
												JOIN king_brands b ON b.id=a.brandid
												WHERE menuid2=0 $cond
												GROUP BY brandid
												order by  b.name 
												");
			if($brandlist_res->num_rows())
			{
				$output['brand_list']=$brandlist_res->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']="error";
				$output['message']="No brand found for menu";
				
			}
			echo json_encode($output);
		}
		
		/**
		 * function to all categorys of selected menu and brand
		 * @param unknown_type $menuid
		 * @param unknown_type $brandid
		 */
		
		function jx_load_allcatsbymenu($menuid=0,$catid=0)
		{
			$output=array();
			$cond='';
			
			if($menuid)
				$cond.=' and menuid='.$menuid;
			if($catid )
				$cond.=' and catid='.$catid;
				
			$catlist_res=@$this->db->query("SELECT DISTINCT catid,brandid,menuid,c.name,b.name as brandname
												FROM king_deals a
												JOIN  king_categories c ON c.id=a.catid
												JOIN king_brands b ON b.id=a.brandid
												WHERE menuid2=0 $cond
												group by catid
												order by c.name 
												");
			if($catlist_res->num_rows())
			{
				$output['cat_list']=$catlist_res->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']="error";
				$output['message']="No category found for menu";
		
			}
			echo json_encode($output);
		}

		/**
		 * 
		 * @param unknown_type $menuid
		 */
		function jx_load_allbrandsbymenu($menuid=0)
		{
			$output=array();
			$cond='';
				
			if($menuid)
				$cond.=' and menuid='.$menuid;
			/*if($catid )
				$cond.=' and catid='.$catid;*/
		
			$brandlist_res=@$this->db->query("SELECT distinct b.id AS brandid,SUM(s.available_qty) AS available,
												b.name AS brand,(o.quantity*l.qty) AS qty
												FROM king_orders o JOIN m_product_deal_link l ON l.itemid=o.itemid JOIN m_product_info p ON p.product_id=l.product_id 
												JOIN king_brands b ON b.id=p.brand_id 
												JOIN king_deals d ON d.brandid=p.brand_id 
												JOIN pnh_menu m ON m.id=d.menuid 
												LEFT OUTER JOIN t_stock_info s ON s.product_id=l.product_id 
												WHERE o.status=0 $cond
												GROUP BY p.brand_id HAVING SUM(o.quantity*l.qty)>available OR available IS NULL ORDER BY brand ASC
												
					");
			
					if($brandlist_res->num_rows())
			{
			$output['brand_list']=$brandlist_res->result_array();
			$output['status']='success';
			}
			else
			{
				$output['status']="error";
			$output['message']="No Brand found for menu";
		
			}
			echo json_encode($output);
		}

		/**
		 * function to load all vendors by brand
		 * @param unknown_type $brandid
		 */
		function jx_load_allvendorsbybrand($brandid=0)
		{
			$output=array();
			$cond='';

			if($brandid)
				$cond.=' and vb.brand_id='.$brandid;
			/*if($catid )
			 $cond.=' and catid='.$catid;*/

			$vendorlist_res=@$this->db->query("SELECT DISTINCT catid,brandid,menuid,c.name,b.name AS brandname,vb.vendor_id,f.vendor_name
					FROM king_deals a
					JOIN  king_categories c ON c.id=a.catid
					JOIN king_brands b ON b.id=a.brandid
					JOIN m_vendor_brand_link vb ON vb.brand_id = b.id
					JOIN m_vendor_info f ON f.vendor_id=vb.vendor_id
					WHERE 1 $cond
					GROUP BY vb.brand_id
					ORDER BY f.vendor_name
					");
			if($vendorlist_res->num_rows())
			{
				$output['vendor_list']=$vendorlist_res->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']="error";
				$output['message']="No Vendor found for Brand";

			}
			echo json_encode($output);
		}

		/**
		 * function to load all menu by territories
		 * @param unknown_type $territory_id
		 */
		function get_franchisemenuby_terrid($territory_id=0)
		{
			$user=$this->auth_pnh_employee();
			if(!$territory_id)
			{
				$territory_id=$this->input->post('territoryid');
				if(is_array($territory_id))
					$territory_id=implode(',',$territory_id);
				if(!$territory_id)
					$territory_id=0;
			}
			$franchise_list = $this->db->query("SELECT a.fid,b.franchise_name,a.menuid,m.name
												FROM `pnh_franchise_menu_link`a
												JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid
												JOIN pnh_menu m ON m.id=a.menuid
												WHERE b.is_suspended=0 AND a.status=1 AND b.territory_id in ($territory_id)
												GROUP BY fid,a.menuid");
			$output=array();
			if($franchise_list ->num_rows())
			{
				$output['terrymenu_franlist'] = $franchise_list->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']='errorr';
				$output['message']='No Menu Found';
			}
			echo json_encode($output);
		}
		
		/**
		 * load all franchise by menuid and territory
		 * @param unknown_type $menu_id
		 */
		function get_franchisebymenu_id($menu_id=0,$territory_id=0,$townid=0)
		{
			$cond='';
			
			if(!$territory_id)
			{
				$territory_id=$this->input->post('territoryid');
				if($territory_id)
					if(is_array($territory_id))
						$territory_id=implode(',',array_filter($territory_id));
			}
			
			if(!$townid)
			{
				$townid=$this->input->post('townid');
				if($townid)
					if(is_array($townid))
						$townid=implode(',',array_filter($townid));
			}
			
			if(!$menu_id)
			{
				$menu_id=$this->input->post('menuid');
				if($menu_id)
					if(is_array($menu_id))
						$menu_id=implode(',',array_filter($menu_id));
			}
			
			if($menu_id)
				$cond.=" and a.menuid in ($menu_id)";
		
			if($territory_id )
				$cond.=" and b.territory_id in ($territory_id)";
			
			if($townid)
				$cond.=" and b.town_id in ($townid) ";
			

			$user=$this->auth_pnh_employee();
			$franchise_list = $this->db->query("SELECT a.fid,b.franchise_name,is_suspended
													FROM `pnh_franchise_menu_link`a
													JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid
													WHERE b.is_suspended != 1 AND a.status=1 $cond
													group by a.fid									
													order by b.franchise_name asc ");
			$output=array();
			if($franchise_list ->num_rows())
			{
				$output['menu_fran_list'] = $franchise_list->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']='errorr';
				$output['message']='No franchise Found';
			}
			echo json_encode($output);
		}
		
		function get_franchisemenuby_twnid($town_id=0)
		{
			$user=$this->auth_pnh_employee();
			if(!$town_id)
			{
				$town_id=$this->input->post('townid');
				if($town_id)
				{
					if(is_array($town_id))
						$town_id=implode(',',$town_id);
				}
			}
			$menu_list = $this->db->query("SELECT a.fid,b.franchise_name,a.menuid,m.name
					FROM `pnh_franchise_menu_link`a
					JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid
					JOIN pnh_menu m ON m.id=a.menuid
					WHERE b.is_suspended=0 AND a.status=1 AND b.town_id in ($town_id)
					GROUP BY a.menuid");
			$output=array();
			if($menu_list ->num_rows())
			{
				$output['townmenu_franlist'] = $menu_list->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']='errorr';
				$output['message']='No Menu Found';
			}
			echo json_encode($output);
		}
		
		//function to load all franchisee based on selected menu
		function jx_loadfranby_menuid($menuid)
		{
			$output=array();
			$allfranby_menuid_res=$this->db->query("SELECT fid,b.franchise_name
										 FROM `pnh_franchise_menu_link`a
										JOIN `pnh_m_franchise_info`b ON b.franchise_id=a.fid
										WHERE menuid=? AND STATUS=1 AND b.is_suspended=0",$menuid);
			if($allfranby_menuid_res ->num_rows())
			{
				$output['franlist_bymenu']=$allfranby_menuid_res->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']='error';
				$output['message']='No franchisee found for Menu';
			}
			echo json_encode($output);
		}
		
		function jx_getallmenus()
		{
			$output=array();
			$allmenu_res=$this->db->query("select * from pnh_menu order by name asc");
			if($allmenu_res->num_rows())
			{
				$output['menu_list']=$allmenu_res->result_array();
				$output['status']='success';
			}else
			{
				$output['status']='error';
				$output['message']='No Data found';
			}
			echo json_encode($output);
				
		}
		
	
		//function to load add_bankdetails view page
		function add_bankdetails()
		{
			$data['page']='add_bankdetails';
			$this->load->view('admin',$data);
		}
		
		
		
		function p_addbankdetails()
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('bank_name','Name','required');
			$this->form_validation->set_rules('branch_name','Branch Name','required');
			$this->form_validation->set_rules('account_number','Account Number','required');
			$this->form_validation->set_rules('ifsc_code','IFSC Code','required');
			if($this->form_validation->run()==false)
			{
				$this->add_bankdetails();
			}
			else
			{
				$bank_name=$this->input->post('bank_name');
				$branch_name=$this->input->post('branch_name');
				$ifsc_code=$this->input->post('ifsc_code');
				$accnt_num=$this->input->post('account_number');
				$remarks=$this->input->post('remarks');
				
				$ins_data=array();
				$ins_data['bank_name']=$bank_name;
				$ins_data['branch_name']=$branch_name;
				$ins_data['ifsc_code']=$ifsc_code;
				$ins_data['account_number']=$accnt_num;
				$ins_data['remarks']=$remarks;
				$this->db->insert('pnh_m_bank_info',$ins_data);
				
				$this->erpm->flash_msg("Bank Details Added");
				
				redirect('admin/add_bankdetails');
			}
				
		}
		
		function list_allbanks()
		{
			$data['bank_list']=$this->db->query("select * from pnh_m_bank_info")->result_array();
			$data['page']='list_banks';
			$this->load->view('admin',$data);
		}
		
		 function view_bank($bankid ='')
		{
			if(!$bankid)
				show_error('Details Not found');
			else
			{
				$data['bank_details']=$this->db->query("select * from pnh_m_bank_info where id=?",$bankid)->row_array();
				$data['page']='view_bank';
				$this->load->view('admin',$data);
			}
		}
		
		function edit_bank($bankid='')
		{
			
			if(!$bankid)
				show_404();
			
				$data['bank_details']=$this->db->query("select * from pnh_m_bank_info where id=?",$bankid)->row_array();
				$data['page']='edit_bank';
				$this->load->view('admin',$data);
			
		}
		function p_editbank($bankid)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('bank_name','Name','required');
			$this->form_validation->set_rules('branch_name','Branch Name','required');
			$this->form_validation->set_rules('account_number','Account Number','required');
			$this->form_validation->set_rules('ifsc_code','IFSC Code','required');
			if($this->form_validation->run()==false)
			{
				$this->edit_bank($bankid);
			}
			else
			{
				$bank_name=$this->input->post('bank_name');
				$branch_name=$this->input->post('branch_name');
				$ifsc_code=$this->input->post('ifsc_code');
				$accnt_num=$this->input->post('account_number');
				$remarks=$this->input->post('remarks');
				//$this->db->query("update pnh_m_bank_info(bank_name,branch_name,ifsc_code,account_number,remarks)values(?,?,?,?,?) where id=?",array($bank_name,$branch_name,$ifsc_code,$accnt_num,$remarks,$bankid))
				
				$ins_data=array();
				$ins_data['bank_name']=$bank_name;
				$ins_data['branch_name']=$branch_name;
				$ins_data['ifsc_code']=$ifsc_code;
				$ins_data['account_number']=$accnt_num;
				$ins_data['remarks']=$remarks;
				$this->db->where('id',$bankid);
				$this->db->update('pnh_m_bank_info',$ins_data);
				if($this->db->affected_rows() >0){
				$this->erpm->flash_msg("Bank Details Updated");}
				redirect('admin/list_allbanks');
			}
			
			
		}


		//Ajax function to load receipt details
		function jx_load_receiptdet()
		{
			$output=array();
			$receipt_id=$this->input->post('receipt_id');
			$process_id=@$this->db->query("select deposited_reference_no as process_id from pnh_m_deposited_receipts where receipt_id=?",$receipt_id)->row()->process_id;
			if($process_id)
			$receipt_details=$this->db->query("select group_concat(receipt_id) as receipt_id,deposited_reference_no as process_id from pnh_m_deposited_receipts where deposited_reference_no=?",$process_id);
			if($receipt_details->num_rows())
			{
				$output['receipt_det']=$receipt_details->row_array();
				$output['status']='success';
			}
			else 
			{
				$output['status']='error';
				$output['message']="Details Not Found";
			}
			echo json_encode($output);
		}
		
		//function to submit the uploaded files
		function upload_depositedslips()
		{
			$user=$this->auth(FINANCE_ROLE);
			$config['upload_path'] = 'resources/employee_assets/image';
			$config['allowed_types'] ='jpg|jpeg|png';
			$config['max_size']	= '50000';
			$config['max_width']  = '50024';
			$config['max_height']  = '50728';
			
			$this->load->library('upload');
			$this->upload->initialize($config);
			if($this->upload->do_upload('image'))
			{
				$data = array('upload_data' => $this->upload->data());
				$fdata=$this->upload->data();
				$image_url=$fdata['file_name'];
				 
			}
			else
			{
				$error = array('error' => $this->upload->display_errors());
				$image_url="";
			}
			
			if($image_url)
			{
				$receipt_id=array();
				$receipt_id=$this->input->post('receipt_id');
				$process_id=$this->input->post('process_id');
				$remarks=$this->input->post('deposited_remarks');
				$d=0;
				$output=array();
				$this->db->query("insert into pnh_m_uploaded_depositedslips(receipt_ids,deposited_reference_no,scanned_url,uploaded_by,uploaded_on,is_deposited,remarks)values(?,?,?,?,now(),?,?)",array($receipt_id,$process_id,$image_url,$user['userid'],1,$remarks));
				$receipt_id=explode(',', $receipt_id);
				foreach($receipt_id as $rid)
				{
					$this->db->query("update pnh_m_deposited_receipts set is_deposited=1 where receipt_id =?",$rid);
					$this->db->query("update pnh_t_receipt_info set is_deposited=1 where receipt_id =?",$rid);
					
				}
				$d+=$this->db->affected_rows();
				if($d)
				{
					$output['status']="success";
					$output['message']="done";
				}else
				{
					$output['status']="error";
					$output['message']="no";
				}
			}else
			{
					$output['status']="error";
					$output['message']=strip_tags($error['error']);
			}
			
			echo "<script> window.parent.hndl_formsubmit_inframe(".json_encode($output).");</script>";
		}
		
		//Ajax function to load uploaded details
		
		function jx_load_depositeddetails()
		{
			$user=$this->auth(FINANCE_ROLE);
			$receipt_id=$this->input->post('deposited_receiptid');
			$process_id=@$this->db->query("select deposited_reference_no as process_id from pnh_m_deposited_receipts where receipt_id=?",$receipt_id)->row()->process_id;
			$deposited_details=$this->db->query("SELECT a.*,b.name AS uploadedby  FROM pnh_m_uploaded_depositedslips a LEFT OUTER JOIN king_admin b ON b.id=a.uploaded_by WHERE deposited_reference_no =?",$process_id);
			$output=array();
			if($deposited_details->num_rows())
			{
				$output['deposited_det']=$deposited_details->row_array();
				$output['status']="success";
			}
			else
			{
				$output['status']="error";
				$output['message']="No Details Found";
			}
			
			echo json_encode($output);
		}
		
		function jx_cancel_processedreceipts()
		{
		
			$user=$this->auth(FINANCE_ROLE);
			$receipt_id=$this->input->post('can_receiptid');
			$desc=$this->input->post('act_remarks');
			$cancel_status=$this->input->post('cancel_status');
			$amount=$this->input->post('debit_amt');
			$sms=$this->input->post('sms');
			$tm_sms=$this->input->post('tm_sms');
			$d=0;
			$output=array();
			$this->db->query("update pnh_m_deposited_receipts set status=2,is_cancelled=1,cancel_reason=?,cancelled_on=now(),cancel_status=?,dbt_amt=? where receipt_id=?",array($desc,$cancel_status,$amount,$receipt_id));
			$this->db->query("update pnh_t_receipt_info set status=2,activated_by=?,reason=?,activated_on=? where receipt_id=?",array($user['userid'],$desc,time(),$receipt_id));
				
			//if($sms || $tm_sms)
			//{
				$franchise_info=@$this->db->query("select f.*,r.receipt_id,r.instrument_no,r.receipt_amount from pnh_t_receipt_info r join pnh_m_franchise_info f on f.franchise_id=r.franchise_id where receipt_id=?",$receipt_id)->row_array();
				$tm_info=@$this->db->query("SELECT b.contact_no,b.employee_id FROM m_town_territory_link a JOIN m_employee_info b ON b.employee_id=a.employee_id WHERE territory_id=? AND a.is_active=1 AND b.is_suspended=0 AND b.job_title2=4",$franchise_info['territory_id'])->row_array();
				$fid=$franchise_info['franchise_id'];
				$login_mobile1=$franchise_info['login_mobile1'];
				$franchise_name=$franchise_info['franchise_name'];
				$cheque_number=$franchise_info['instrument_no'];
				$fran_amount=$franchise_info['receipt_amount'];
				$type=1;
				$sms_type='Bounce';
				$tm_contactno=$tm_info['contact_no'];
				
				$receipt_id = $franchise_info['receipt_id'];
				$acc_stat_id = 0;
				if($amount)
				{
					$acc_stat_id = $this->erpm->pnh_fran_account_stat($fid,$type,$amount,$desc,"correction",$fid);
					$trans_type = 5;
				
					$arr = array($fid,$receipt_id,$trans_type,$acc_stat_id,$type?$amount:0,!$type?$amount:0,$desc,1,date('Y-m-d H:i:s'),$user['userid']);
					$this->db->query("insert into pnh_franchise_account_summary (franchise_id,receipt_id,action_type,acc_correc_id,debit_amt,credit_amt,remarks,status,created_on,created_by) values(?,?,?,?,?,?,?,?,?,?)",$arr);
				}
				
				$empid=$tm_info['employee_id'];
				$tm_mobno=array();
				$tm_mobno=$tm_info['contact_no'];
				$tm_mobno=explode(',',$tm_mobno);
				//$grp_msg="Dear $franchise_name ,cheque $cheque_number,for amount Rs.$fran_amount,is bounced and penalty for this is $amount ,arrange for payment immediately.";
				
				//$grp_msg="Dear $franchise_name ,cheque $cheque_number,for amount Rs.$fran_amount,is bounced and penalty for this is $amount ,arrange for payment immediately.";
				$grp_msg="Dear $franchise_name,cheque $cheque_number,for amount Rs.$fran_amount,is returned un paid by the banker and the penalty for this is Rs.$amount ,arrange for payment immediately to avoid hold up in further supplies.";
				
				if($sms && $cancel_status==2)
				{
					$this->erpm->pnh_sendsms($login_mobile1,$grp_msg,$fid,0,$sms_type);
				}
				if($tm_sms && $cancel_status==2)
				{
					foreach($tm_mobno as $mob_no)
					{
						$this->erpm->pnh_sendsms($mob_no,$grp_msg,$fid,$empid,$sms_type);
						$this->db->query("insert into pnh_employee_grpsms_log(emp_id,contact_no,type,grp_msg,created_on)values(?,?,?,?,now())",array($empid,$mob_no,$sms_type,$grp_msg));
					}
					$this->erpm->flash_msg("Account statement corrected");
				}
			//}
			$d+=$this->db->affected_rows();
			if($d)
			{
				$output['status']="success";
				
			}
			else 
			{
				$output['status']="Error";
			}
			echo json_encode($output);
		
		}

		//Ajax function to load franchise info cancel receipt
		
		function load_frandet_ofcanreceipt()
		{
			$output=array();
			$receipt_id=$this->input->post('receipt_id');
			$franreceipt_info=$this->db->query("select b.franchise_name,a.receipt_id,a.instrument_no from pnh_t_receipt_info a join pnh_m_franchise_info b on b.franchise_id=a.franchise_id where receipt_id=?",$receipt_id);
						
			if($franreceipt_info->num_rows())
			{
				$output['fran_receiptdet']=$franreceipt_info->row_array();
				$output['status']='success';
				
			}
			else
			 {
			 	$output['status']='error';
			 }
			 echo json_encode($output);
		}
		
		/**
		 * function for get the pending shipments list;
		 */
		function pick_list_for_pending_shipments($id)
		{
			$user=$this->erpm->auth();
			$sql="select a.id,b.invoice_no,c.tray_name,g.town_name,f.franchise_name from pnh_m_manifesto_sent_log a 
						join shipment_batch_process_invoice_link b on b.inv_manifesto_id=a.manifesto_id
						join m_tray_info c on c.tray_id=b.tray_id
						join king_invoice d on d.invoice_no=b.invoice_no
						join king_transactions e on e.transid=d.transid
						join pnh_m_franchise_info f on f.franchise_id=e.franchise_id
						join pnh_towns g on g.id=f.town_id
						where a.id=? and packed=1 and shipped=0
					group by b.invoice_no
					order by g.town_name";
			$pick_list_data=$this->db->query($sql,array($id))->result_Array();;
			
			
			$data['pick_list_data']=$pick_list_data;
			$data['manifest_id']=$id;
			$data['page']='pending_shipments_pick_list';
			$this->load->view('admin/body/pending_shipments_pick_list',$data);
		}
		
		/**
		 * get the shipments for out scan
		 * @param unknown_type $id
		 */
		function pnh_outscan_pending_shipents($id)
		{
			$user=$this->erpm->auth(PNH_SHIPMENT_MANAGER);
			$sql="select a.id,b.invoice_no,c.tray_name,g.town_name,f.franchise_name from pnh_m_manifesto_sent_log a
						join shipment_batch_process_invoice_link b on b.inv_manifesto_id=a.manifesto_id
						join m_tray_info c on c.tray_id=b.tray_id
						join king_invoice d on d.invoice_no=b.invoice_no
						join king_transactions e on e.transid=d.transid
						join pnh_m_franchise_info f on f.franchise_id=e.franchise_id
						join pnh_towns g on g.id=f.town_id
					where a.id=? and packed=1 and shipped=0 and outscanned=0
					group by b.invoice_no
					order by g.town_name";
			$pnh_pending_shipments_list=$this->db->query($sql,array($id))->result_Array();
			$data['manifest_id']=$id;
			
			$data['pnh_pending_shipments_list']=$pnh_pending_shipments_list;
			$data['page']='Pnh_outscan_pending_shipmets';
			$this->load->view('admin',$data);
		}
		
		/**
		 * update the pending shipments status to ship status
		 */
		function update_pnh_pending_shipmets()
		{
			$user=$this->erpm->auth();
			
			$invoices=$this->input->post('invoices');
			$send_log_id=$this->input->post('manifesto_nu');
			$pr_remark=$this->input->post('part_ps_manifesto');
			
			if($invoices)
			{
				//if any changes affected in pending shipments group while outscan time then some update process take place
				//get manifesto id
				$manifesto_id_det=$this->db->query("select manifesto_id ,remark from pnh_m_manifesto_sent_log where id=?",$send_log_id)->result_array();
				
				$manifesto_id=$manifesto_id_det[0]['manifesto_id'];
				$remark=$manifesto_id_det[0]['remark'];
				
				//update the manifesto table
				$ins_data = array();
				$ins_data['invoice_nos'] = implode(',',$invoices);
				$ins_data['modified_on'] = date('Y-m-d H:i:s');
				$ins_data['modified_by'] = $user['userid'];
				$this->db->where('id',$manifesto_id);
				$this->db->update("pnh_manifesto_log",$ins_data);
				
				//update the shipment batch process table
				$invoices_arr=$invoices;
				$invoices=implode(',',$invoices);
				$this->db->query("update shipment_batch_process_invoice_link set inv_manifesto_id=? where invoice_no in ($invoices) ",array($manifesto_id));
				
				$this->db->query("update shipment_batch_process_invoice_link set inv_manifesto_id=0 where invoice_no not in ($invoices) and inv_manifesto_id = ? ",array($manifesto_id));
				
				//update the manifsto sent log table
				$sent_manifesto_logid = $send_log_id;
				
				$param1=array();
				$param1['remark']=$remark.'::'.$pr_remark;
				$param1['sent_invoices']=$invoices;
				$param1['modified_on']=cur_datetime();
				$param1['modified_by']=$user['userid'];
				$this->db->where('id',$sent_manifesto_logid);
				$this->db->update("pnh_m_manifesto_sent_log",$param1);
				
				$cur_datetime=cur_datetime();
				//update the invoices transit log
				$this->db->query("update pnh_invoice_transit_log set status = 9 where sent_log_id = ? ",$sent_manifesto_logid);
				foreach($invoices_arr as $invno)
				{
					$tray_inv_status = 3;
					if($this->db->query("select count(*) as t from pnh_invoice_transit_log where sent_log_id = ? and invoice_no = ? ",array($sent_manifesto_logid,$invno))->row()->t)
					{
						$this->db->query("update pnh_invoice_transit_log set status = 0 where sent_log_id = ? and invoice_no = ? and status = 9 ",array($sent_manifesto_logid,$invno));
						$tray_inv_status = 2;
					}
					
					// remove shipments/invoice from tray
					$tray_inv_link = $this->db->query("select tray_inv_id,tray_terr_id from pnh_t_tray_invoice_link where invoice_no = ? and status = 1 order by tray_inv_id desc limit 1 ",$invno)->row_array();
					
					// update tray invoice status = 2 for invno
					$this->db->query("update pnh_t_tray_invoice_link set status = $tray_inv_status,modified_on=?,modified_by=? where status = 1 and tray_inv_id = ? ",array($cur_datetime,$user['userid'],$tray_inv_link['tray_inv_id']));
					
					$this->db->query("update pnh_t_tray_territory_link set is_active = 0,modified_on=?,modified_by=? where is_active = 1 and tray_terr_id = ? ",array($cur_datetime,$user['userid'],$tray_inv_link['tray_terr_id']));
					
				}
				
				
				$this->db->query("delete from pnh_invoice_transit_log where sent_log_id = ? and status = 9 ",array($sent_manifesto_logid));
				
				//finally update the ship status in shipment batch process table
				$prama3=array();
				/*$prama3['shipped']=1;
				$prama3['shipped_on']=cur_datetime();
				$prama3['shipped_by']=$user['userid'];*/
				
				$prama3['outscanned']=1;
				$prama3['outscanned_on']=cur_datetime();
				$prama3['outscanned_by']=$user['userid'];
				
				$this->db->where('inv_manifesto_id',$manifesto_id);
				$this->db->update("shipment_batch_process_invoice_link",$prama3);
				
				if($this->db->affected_rows())
				{
					$this->db->query("update pnh_m_manifesto_sent_log set status=2,modified_on=?,modified_by=? where id=?",array($cur_datetime,$user['userid'],$sent_manifesto_logid));
					
					foreach($invoices_arr as $inv)
					{
					
						$trans_logprm=array();
						$trans_logprm['transid']=$this->db->query("select transid from king_invoice where invoice_no=? limit 1",$inv)->row()->transid;
						$trans_logprm['admin']=$user['userid'];
						$trans_logprm['time']=time();
						$trans_logprm['msg']='invoice ('.$inv.') Outscanned';
						$this->db->insert("transactions_changelog",$trans_logprm);
					}
				}
				
				$this->session->set_flashdata("erp_pop_info",count($invoices_arr)." Invoices selected for delivery");
			}
			
			redirect(site_url('admin/view_manifesto_sent_log'));
			
		}

		/**
		 * display pnh packed summary in outscan page
		 */
		function jx_pnh_packedsumm_by_tray()
		{
			$this->erpm->auth();
			$packedsumm_Det=$this->erpm->get_pnh_unshiped_inv_by_tray();
			$output = array();
			$output['packedsumm_Det'] = $packedsumm_Det['outscan_summ'];
			$output['ttl_penpak'] = $packedsumm_Det['ttl_penpak'];
			$output['invoices'] = $packedsumm_Det['invoices'];
			$output['to_check_trays'] = $packedsumm_Det['to_check_trays'];
			echo json_encode($output);
		}
		
		/**
		 * update the vehicle details,and update shipted on status,and sent sms
		 */
		function update_driver_details_in_sent_manifesto()
		{
			$user=$this->erpm->auth();
			if(!$_POST)
				die;
			$send_sms=array();
			$manifesto_sentid=$this->input->post('manifesto_sent_id');
			$start_km=$this->input->post('start_km');
			$amount=$this->input->post('amount');
			$send_sms[]=$territory_manager=$this->input->post('tm');
			$send_sms[]=$bussiness_executive=$this->input->post('BE');
			$send_sms=array_filter($send_sms);
			
			if($manifesto_sentid)
			{
					$this->db->query("update pnh_m_manifesto_sent_log set modified_on=?,modified_by=?,start_meter_rate=?,amount=?,status=3 where id=?",array(cur_datetime(),$user['userid'],$start_km,$amount,$manifesto_sentid));
					
					//update shiped status
					$manifesto_id_det=$this->db->query("select manifesto_id,sent_invoices  from pnh_m_manifesto_sent_log where id=?",$manifesto_sentid)->result_array();
					$manifesto_id=$manifesto_id_det[0]['manifesto_id'];
					
					$prama3=array();
					$prama3['shipped']=1;
					$prama3['shipped_on']=cur_datetime();
					$prama3['shipped_by']=$user['userid'];
					
					$this->db->where('inv_manifesto_id',$manifesto_id);
					$this->db->update("shipment_batch_process_invoice_link",$prama3);
					
					if($this->db->affected_rows())
					{
						$this->erpm->sendsms_franchise_shipments($manifesto_id_det[0]['sent_invoices']);
					}
					
					$this->erpm->update_inv_status_in_order_tbl($manifesto_id);
					
					// check if the invoice is return invoice and update shipped status in return module 
					$return_inv_list_res = $this->db->query("select invoice_no from shipment_batch_process_invoice_link where inv_manifesto_id = ? and is_returned = 1 ",$manifesto_id);
					if($return_inv_list_res->num_rows())
					{
						foreach($return_inv_list_res->result_array() as  $return_inv)
						{
							$return_id = $this->db->query("select return_id from pnh_invoice_returns where invoice_no = ? ",$return_inv['invoice_no'])->row()->return_id;
							$this->db->query('update pnh_invoice_returns_product_link set is_shipped = 1 where return_id = ? ',$return_id);
						}
					}
					
					//sent a sms
					$sent_invoices=$this->db->query('select a.sent_invoices,b.name,a.hndleby_name,b.contact_no,a.hndleby_contactno,a.hndleby_vehicle_num,
													c.name as  bus_name,d.contact_no as des_contact,b.job_title2,a.id 
															from pnh_m_manifesto_sent_log a
															left join m_employee_info b on b.employee_id = a.hndleby_empid
															left join pnh_transporter_info c on c.id=a.bus_id
															left join pnh_transporter_dest_address d on d.id=a.bus_destination and d.transpoter_id=a.bus_id
													where a.id=?',$manifesto_sentid)->result_array();
					
					$invoices=$sent_invoices[0]['sent_invoices'];
					$hndbyname=$sent_invoices[0]['name'];
					$hndbycontactno=$sent_invoices[0]['contact_no'];
					$vehicle_num=$sent_invoices[0]['hndleby_vehicle_num'];
					if(!$hndbyname)
						$hndbyname=$sent_invoices[0]['hndleby_name'];
					if(!$hndbyname)
						$hndbyname=$sent_invoices[0]['bus_name'].' Travels';
					
					if(!$hndbycontactno)
						$hndbycontactno=$sent_invoices[0]['hndleby_contactno'];
					if(!$hndbycontactno)
						$hndbycontactno=$sent_invoices[0]['des_contact'];
					
					$this->db->query("update pnh_invoice_transit_log set status = 1,logged_on=now() where sent_log_id = ? and status = 0 ",$manifesto_sentid);
					
					if($this->db->affected_rows())
					{
						$sms_msg='';
						if($sent_invoices[0]['job_title2']=='7')
						{
							$sms_msg = 'Dear [To],Shipment for the town [Town_name] sent via Driver '.ucwords($hndbyname).'('.$hndbycontactno.') vehicle no ['.$vehicle_num.'],Manifesto id: '.$sent_invoices[0]['id'];
						}else if($sent_invoices[0]['job_title2']=='6'){
							$sms_msg = 'Dear [To],Shipment for the town [Town_name] sent via Fright-Cordinator '.ucwords($hndbyname).'('.$hndbycontactno.') ,Manifesto id: '.$sent_invoices[0]['id'];
						}
						
						// send sms to tm and exec fc for towns
						$employees_list=$this->erpm->get_emp_by_territory_and_town($invoices);
						
						if ($employees_list)
							{
								foreach($employees_list as $emp)
								{
									$emp_name=$emp['name'];
									$emp_id=$emp['employee_id'];
									$town_name=$emp['town_name'];
									$territory_id=$emp['territory_id'];
									$town_id=$emp['town_id'];
									$job_title=$emp['job_title2'];
									$send_sms_status=$emp['send_sms'];
									$emp_contact_nos = explode(',',$emp['contact_no']);
										
									if(!in_array($job_title,$send_sms))
									{
										continue;
									}
									
									$sms_msg=str_ireplace('[To]',$emp_name,$sms_msg);
									$sms_msg=str_ireplace('[Town_name]',$town_name,$sms_msg);
									
									$temp_emp=array();	
									foreach($emp_contact_nos as $emp_mob_no)
									{
										if(isset($temp_emp[$emp_id]))
											continue;
										$temp_emp[$emp_id]=1;
										
										if($send_sms_status)
											$this->erpm->pnh_sendsms($emp_mob_no,$sms_msg);
										//	echo $emp_mob_no,$sms_msg;
										$log_prm=array();
										$log_prm['emp_id']=$emp_id;
										$log_prm['contact_no']=$emp_mob_no;
										$log_prm['type']=4;
										$log_prm['territory_id']=$territory_id;
										$log_prm['town_id']=$town_id;
										$log_prm['grp_msg']=$sms_msg;
										$log_prm['created_on']=cur_datetime();
										$this->erpm->insert_pnh_employee_grpsms_log($log_prm);
									}
									
								}
							}
						}
					$this->session->set_flashdata("erp_pop_info","Your selected invoices are shipped");
			}
				redirect($_SERVER['HTTP_REFERER']);
	}

	function jx_upd_deallivestat()
	{
		$this->erpm->auth();
		$output = array();
		$itemid = $this->input->post('itemid');
		$this->db->query('update king_dealitems set live = !live where id = ? limit 1',$itemid);
		$output['live_stat'] = ($this->db->query("select live from king_dealitems where id = ? ",$itemid)->row()->live);
		$output['status'] = 'success';
		echo json_encode($output);
	}
		
	function exp_deals_nrc_prodslist()
	{
		$this->erpm->auth(DEAL_MANAGER_ROLE);
		
		$deals = $this->db->query("select b.id,b.name,b.orgprice,b.price,sum(c.qty) as qty,sum(e.available_qty) as stk,a.publish,b.live
											from king_deals a 
											join king_dealitems b on a.dealid = b.dealid and b.is_pnh = 0 
											join m_product_deal_link c on c.itemid = b.id  
											join m_product_info d on d.product_id = c.product_id and d.is_sourceable = 0
											join t_stock_info e on e.product_id = d.product_id 
											where 1 
											group by b.id 
											order by b.name ")->result_array();
		$this->erpm->export_csv("Deals - Product Not Sourceable-".date('Y-m-d'),$deals);									
	}

	/**
	 * PNH Franchise Sales analytical report   
	 */
	function pnh_exp_franchise_orders_stat()
	{
		$this->erpm->auth(true);
		
		$fran_list_res = $this->db->query("select franchise_id,franchise_name,is_suspended,territory_name,town_name from pnh_m_franchise_info a join pnh_m_territory_info b on a.territory_id = b.id join pnh_towns c on c.id = a.town_id order by franchise_name;");
		$fran_sales_data = array();
		foreach($fran_list_res->result_array() as $fran)
		{
			if(!isset($fran_sales_data[$fran['franchise_id']]))
				$fran_sales_data[$fran['franchise_id']] = array();
				$fran_sales_data[$fran['franchise_id']]['name'] = $fran['franchise_name'];
				$fran_sales_data[$fran['franchise_id']]['territory'] = $fran['territory_name'];
				$fran_sales_data[$fran['franchise_id']]['town'] = $fran['town_name'];
				$fran_sales_data[$fran['franchise_id']]['last_ordered_on'] = @$this->db->query("select date(from_unixtime(b.init)) as last_ordered_on from king_orders a join king_transactions b on a.transid = b.transid and b.is_pnh = 1 where franchise_id = ? order by b.init desc limit 1",$fran['franchise_id'])->row()->last_ordered_on;
				
				
				$shipped_tilldate=$this->db->query("SELECT round(sum((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) as amt 
													FROM king_transactions a
													JOIN king_orders b ON a.transid = b.transid
													JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id
													JOIN king_invoice d ON d.order_id = b.id
													JOIN shipment_batch_process_invoice_link e ON e.invoice_no = d.invoice_no
													AND e.shipped =1 AND e.packed =1
													WHERE a.franchise_id = ? AND d.invoice_status = 1 and b.status != 0 
							 						",$fran['franchise_id'])->row()->amt;
				$paid_tilldate =$this->db->query("select sum(receipt_amount) as amt from pnh_t_receipt_info where receipt_type = 1 and status = 1 and franchise_id = ? ",$fran['franchise_id'])->row()->amt;
				$payment_pending = round($shipped_tilldate-$paid_tilldate,2);
				
				
				$fran_sales_data[$fran['franchise_id']]['pending_payment'] = $payment_pending;
				$fran_sales_data[$fran['franchise_id']]['current_week_sales'] = round($this->db->query("select ifnull(sum(i_orgprice-(i_coup_discount+i_discount)),0)*1 as amt from king_orders a join king_transactions b on a.transid = b.transid and b.is_pnh = 1 where date(from_unixtime(b.init)) between DATE_ADD(curdate(), INTERVAL - (dayofweek(curdate())-1) DAY) and curdate() and a.status < 3 and franchise_id = ? ",$fran['franchise_id'])->row()->amt,2);
				$fran_sales_data[$fran['franchise_id']]['week_4'] = round($this->db->query("select ifnull(sum(i_orgprice-(i_coup_discount+i_discount)*quantity),0)*1 as amt from king_orders a join king_transactions b on a.transid = b.transid and b.is_pnh = 1 where date(from_unixtime(b.init)) between DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate())+1 DAY), INTERVAL -1 WEEK) and DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate()) DAY), INTERVAL 0 WEEK) and a.status < 3 and franchise_id = ? ",$fran['franchise_id'])->row()->amt,2);
				$fran_sales_data[$fran['franchise_id']]['week_3'] = round($this->db->query("select ifnull(sum(i_orgprice-(i_coup_discount+i_discount)*quantity),0)*1 as amt from king_orders a join king_transactions b on a.transid = b.transid and b.is_pnh = 1 where date(from_unixtime(b.init)) between DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate())+1 DAY), INTERVAL -2 WEEK) and DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate()) DAY), INTERVAL -1 WEEK) and a.status < 3 and franchise_id = ? ",$fran['franchise_id'])->row()->amt,2);
				$fran_sales_data[$fran['franchise_id']]['week_2'] = round($this->db->query("select ifnull(sum(i_orgprice-(i_coup_discount+i_discount)*quantity),0)*1 as amt from king_orders a join king_transactions b on a.transid = b.transid and b.is_pnh = 1 where date(from_unixtime(b.init)) between DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate())+1 DAY), INTERVAL -3 WEEK) and DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate()) DAY), INTERVAL -2 WEEK) and a.status < 3 and franchise_id = ? ",$fran['franchise_id'])->row()->amt,2);
				$fran_sales_data[$fran['franchise_id']]['week_1'] = round($this->db->query("select ifnull(sum(i_orgprice-(i_coup_discount+i_discount)*quantity),0)*1 as amt from king_orders a join king_transactions b on a.transid = b.transid and b.is_pnh = 1 where date(from_unixtime(b.init)) between DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate())+1 DAY), INTERVAL -4 WEEK) and DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate()) DAY), INTERVAL -3 WEEK) and a.status < 3 and franchise_id = ? ",$fran['franchise_id'])->row()->amt,2);
				
				$fran_sales_data[$fran['franchise_id']]['month_4'] = round($this->db->query("select ifnull(sum(i_orgprice-(i_coup_discount+i_discount)*quantity),0)*1 as amt from king_orders a join king_transactions b on a.transid = b.transid and b.is_pnh = 1 where date(from_unixtime(b.init)) between ADDDATE(LAST_DAY(SUBDATE(curdate(), INTERVAL 2 MONTH)), 1) and LAST_DAY(SUBDATE(curdate(), INTERVAL 1 MONTH)) and a.status < 3 and franchise_id = ? ",$fran['franchise_id'])->row()->amt,2);
				$fran_sales_data[$fran['franchise_id']]['month_3'] = round($this->db->query("select ifnull(sum(i_orgprice-(i_coup_discount+i_discount)*quantity),0)*1 as amt from king_orders a join king_transactions b on a.transid = b.transid and b.is_pnh = 1 where date(from_unixtime(b.init)) between ADDDATE(LAST_DAY(SUBDATE(curdate(), INTERVAL 3 MONTH)), 1) and LAST_DAY(SUBDATE(curdate(), INTERVAL 2 MONTH)) and a.status < 3 and franchise_id = ? ",$fran['franchise_id'])->row()->amt,2);
				$fran_sales_data[$fran['franchise_id']]['month_2'] = round($this->db->query("select ifnull(sum(i_orgprice-(i_coup_discount+i_discount)*quantity),0)*1 as amt from king_orders a join king_transactions b on a.transid = b.transid and b.is_pnh = 1 where date(from_unixtime(b.init)) between ADDDATE(LAST_DAY(SUBDATE(curdate(), INTERVAL 4 MONTH)), 1) and LAST_DAY(SUBDATE(curdate(), INTERVAL 3 MONTH)) and a.status < 3 and franchise_id = ? ",$fran['franchise_id'])->row()->amt,2);
				$fran_sales_data[$fran['franchise_id']]['month_1'] = round($this->db->query("select ifnull(sum(i_orgprice-(i_coup_discount+i_discount)*quantity),0)*1 as amt from king_orders a join king_transactions b on a.transid = b.transid and b.is_pnh = 1 where date(from_unixtime(b.init)) between ADDDATE(LAST_DAY(SUBDATE(curdate(), INTERVAL 5 MONTH)), 1) and LAST_DAY(SUBDATE(curdate(), INTERVAL 4 MONTH)) and a.status < 3 and franchise_id = ? ",$fran['franchise_id'])->row()->amt,2);
				
				$fran_sales_data[$fran['franchise_id']]['sales_till_date'] = round($this->db->query("select ifnull(sum(i_orgprice-(i_coup_discount+i_discount)*quantity),0)*1 as amt from king_orders a join king_transactions b on a.transid = b.transid and b.is_pnh = 1 where a.status < 3 and franchise_id = ? ",$fran['franchise_id'])->row()->amt,2);
				
				$fran_topsalesbycat = $this->db->query("select  e.name,ifnull(round(sum(i_orgprice-(i_coup_discount+i_discount)*a.quantity),2),0) as amt 
															from king_orders a 
															join king_transactions b on a.transid = b.transid and b.is_pnh = 1 
															join king_dealitems c on c.id = a.itemid
															join king_deals d on d.dealid = c.dealid
															join pnh_menu e on e.id = d.menuid
															where a.status < 3 and franchise_id = ?   
															group by d.menuid 
															order by amt desc 
															limit 3",$fran['franchise_id'])->result_array();
																			
				$fran_sales_data[$fran['franchise_id']]['top_selling_category_1'] = isset($fran_topsalesbycat[0])?$fran_topsalesbycat[0]['name'].'-'.$fran_topsalesbycat[0]['amt']:"";
				$fran_sales_data[$fran['franchise_id']]['top_selling_category_2'] = isset($fran_topsalesbycat[1])?$fran_topsalesbycat[1]['name'].'-'.$fran_topsalesbycat[1]['amt']:"";
				$fran_sales_data[$fran['franchise_id']]['top_selling_category_3'] = isset($fran_topsalesbycat[2])?$fran_topsalesbycat[2]['name'].'-'.$fran_topsalesbycat[2]['amt']:"";
				
				$fran_sales_data[$fran['franchise_id']]['total_members'] = $this->db->query("select count(*) as t from pnh_member_info where franchise_id = ? ",$fran['franchise_id'])->row()->t;
				$fran_sales_data[$fran['franchise_id']]['ordered_members'] = $this->db->query("select count( distinct a.userid ) as t  from king_orders a join king_transactions b on a.transid = b.transid and is_pnh = 1 join pnh_member_info c on c.user_id = a.userid where c.franchise_id = ? ; ",$fran['franchise_id'])->row()->t;
				$fran_sales_data[$fran['franchise_id']]['suspended'] = $fran['is_suspended'];
		}
		$fran_sales_data_fil = array();
		foreach($fran_sales_data as  $fid=>$fdet)
		{
			$week4_det = $this->db->query("select DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate())+1 DAY), INTERVAL -1 WEEK) as w_st,DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate()) DAY), INTERVAL 0 WEEK) as w_en")->row_array();
			$week3_det = $this->db->query("select DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate())+1 DAY), INTERVAL -2 WEEK) as w_st,DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate()) DAY), INTERVAL -1 WEEK) as w_en")->row_array();
			$week2_det = $this->db->query("select DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate())+1 DAY), INTERVAL -3 WEEK) as w_st,DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate()) DAY), INTERVAL -2 WEEK) as w_en")->row_array();
			$week1_det = $this->db->query("select DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate())+1 DAY), INTERVAL -4 WEEK) as w_st,DATE_ADD(DATE_ADD(curdate(), INTERVAL - dayofweek(curdate()) DAY), INTERVAL -3 WEEK) as w_en")->row_array();
			
			$mon4_det = $this->db->query("select ADDDATE(LAST_DAY(SUBDATE(curdate(), INTERVAL 2 MONTH)), 1)  as m_st,LAST_DAY(SUBDATE(curdate(), INTERVAL 1 MONTH)) as m_en")->row_array();
			$mon3_det = $this->db->query("select ADDDATE(LAST_DAY(SUBDATE(curdate(), INTERVAL 3 MONTH)), 1)  as m_st,LAST_DAY(SUBDATE(curdate(), INTERVAL 2 MONTH)) as m_en")->row_array();
			$mon2_det = $this->db->query("select ADDDATE(LAST_DAY(SUBDATE(curdate(), INTERVAL 4 MONTH)), 1)  as m_st,LAST_DAY(SUBDATE(curdate(), INTERVAL 3 MONTH)) as m_en")->row_array();
			$mon1_det = $this->db->query("select ADDDATE(LAST_DAY(SUBDATE(curdate(), INTERVAL 5 MONTH)), 1)  as m_st,LAST_DAY(SUBDATE(curdate(), INTERVAL 4 MONTH)) as m_en")->row_array();
			
			$tmp = array();
			$tmp['Territory'] = $fdet['territory'];
			$tmp['Town'] = $fdet['town'];
			$tmp['Franchise'] = $fdet['name'];
			$tmp['Last ordered on'] = $fdet['last_ordered_on'];
			
			$tmp['Pending payment'] = $fdet['pending_payment'];
			$tmp['Current week sales'] = $fdet['current_week_sales'];
			 
			$tmp[date('d M',strtotime($week4_det['w_st'])).' - '.date('d M',strtotime($week4_det['w_en']))] = $fdet['week_4'];
			
			$tmp[date('d M',strtotime($week3_det['w_st'])).' - '.date('d M',strtotime($week3_det['w_en']))] = $fdet['week_3'];
			
			$tmp[date('d M',strtotime($week2_det['w_st'])).' - '.date('d M',strtotime($week2_det['w_en']))] = $fdet['week_2'];
			
			$tmp[date('d M',strtotime($week1_det['w_st'])).' - '.date('d M',strtotime($week1_det['w_en']))] = $fdet['week_1'];
			
			
			
			$tmp[date('MY',strtotime($mon4_det['m_st']))] = $fdet['month_4'];
			
			$tmp[date('MY',strtotime($mon3_det['m_st']))] = $fdet['month_3'];
			
			$tmp[date('MY',strtotime($mon2_det['m_st']))] = $fdet['month_2'];
			
			$tmp[date('MY',strtotime($mon1_det['m_st']))] = $fdet['month_1'];
			
			
			$tmp['Top selling category 1'] = $fdet['top_selling_category_1'];
			$tmp['Top selling category 2'] = $fdet['top_selling_category_2'];
			$tmp['Top selling category 3'] = $fdet['top_selling_category_3'];
			
			$tmp['Total members'] = $fdet['total_members'];
			$tmp['ordered members'] = $fdet['ordered_members'];
			$tmp['Suspended'] = $fdet['suspended'];
			
			$fran_sales_data_fil[]=$tmp;
			
			
		}
		 
		$this->erpm->export_csv("Franchise Sales Value Report -".date('Y-m-d'),$fran_sales_data_fil);
		
	}
			
	/**
	 * functions for tray management
	 */
	function pnh_tray_management()
	{
		$this->erpm->auth();
		
		$sql="select a.*,if(b.is_active=1,'2','1') as tray_status,c.hub_name as territory_name from m_tray_info a
						left join pnh_t_tray_territory_link b on b.tray_id = a.tray_id and is_active=1
						left join pnh_deliveryhub c on c.id=b.territory_id";
		
		$data['trays_list']=$trays_list=$this->db->query($sql)->result_array();
		$data['page']='pnh_tray_management';
		$this->load->view("admin",$data);
	}
	
	/**
	 * function to validate tray name used while processing tray add/edit form 
	 * @param unknown_type $str
	 */
	function _valid_trayname($str)
	{
		$this->erpm->auth();
		
		// used for checking valid tray name while editting 
		$tray_id = $this->input->post('tray_id');
		if($tray_id)
			$cond = ' and tray_id != '.$tray_id;
		else 
			$cond = '';
		
		$tray_name =$this->input->post('tray_name');
		if($this->db->query("select count(*) as t from m_tray_info where tray_name = ? $cond ",$tray_name)->row()->t)
		{
			$this->form_validation->set_message('_valid_trayname',$tray_name.' is already available ');
			return false; 
		}
		return true;
	}
	
	/**
	 * function to add new tray via ajax request 
	 * 
	 */
	function jx_pnh_addtray()
	{
		$user=$this->erpm->auth();
		
		// output data hanlded for processing in client end 
		$output = array();
		
		// validate form inputs tray name and max allowed entries 
		$this->load->library('form_validation');
		$this->form_validation->set_rules('tray_name','Tray Name','required|callback__valid_trayname');
		$this->form_validation->set_rules('max_allowed','Max Allowed','required|integer|max_length[3]');
		
		// if formvalidation failed , sent errors via output variable 
		if($this->form_validation->run() === FALSE)
		{
			$output['status'] = 'error';
			$output['error'] = validation_errors();
		}else
		{
			// prepare form inputs to array for insert 
			$ins_data = array();
			$ins_data['tray_name']=$this->input->post('tray_name');
			$ins_data['max_allowed']=$this->input->post('max_allowed');
			$ins_data['created_on']=cur_datetime();
			$ins_data['created_by']=$user['userid'];
			$this->db->insert('m_tray_info',$ins_data);
			
			// fetch new tray id and update to output varaible 
			$output['status'] = 'success';
			$output['tray_id'] = $this->db->insert_id();
		}
		
		echo json_encode($output);
		
	}
	
	
	/**
	 * function to edit tray details via ajax request
	 *
	 */
	function jx_pnh_edittray()
	{
		$user=$this->erpm->auth();
		// output data hanlded for processing in client end
		$output = array();
	
		// validate form inputs tray name and max allowed entries
		$this->load->library('form_validation');
		$this->form_validation->set_rules('tray_id','Tray ID','required');
		$this->form_validation->set_rules('tray_name','Tray Name','required|callback__valid_trayname');
		$this->form_validation->set_rules('max_allowed','Max Allowed','required|integer|max_length[3]');
	
		// if formvalidation failed , sent errors via output variable
		if($this->form_validation->run() === FALSE)
		{
			$output['status'] = 'error';
			$output['error'] = validation_errors();
		}else
		{
			$tray_id = $this->input->post('tray_id');
			
			// prepare form inputs to array for insert
			$ins_data = array();
			$ins_data['tray_name']=$this->input->post('tray_name');
			$ins_data['max_allowed']=$this->input->post('max_allowed');
			$ins_data['modified_on']=cur_datetime();
			$ins_data['modified_by']=$user['userid'];
			
			$this->db->where('tray_id',$tray_id);
			$this->db->update('m_tray_info',$ins_data);
	
			// fetch new tray id and update to output varaible
			$output['status'] = 'success';
			$output['tray_id'] = $tray_id;
		}
	
		echo json_encode($output);
	
	}
	
	
	/**
	 * function to get tray detatails by id 
	 * @param unknown_type $tray_id
	 */
	function jx_get_traydetbyid($tray_id=0)
	{
		$user=$this->erpm->auth();
		$output = array();
		
		// Query to get tray details from tray id 
		$this->db->where('tray_id',$tray_id);
		$tray_det_res = $this->db->get('m_tray_info');
		if($tray_det_res->num_rows())
		{
			$output['status'] = 'success';
			$output['tray_det'] = $tray_det_res->row_array();
		}else
		{
			$output['status'] = 'error';
			$output['error'] = 'Invalid tray in reference';
		}	

		echo json_encode($output);
	}
			
		/**
		* function for manage the transportaion
		*/
		function pnh_transport_management()
		{
			$user=$this->erpm->auth();
			
			$sql="select * from pnh_transporter_info a";
								
			$transporters_details=$this->db->query($sql)->result_array();
			
			$data['page']="pnh_transport_management";
			$data['transporters_details']=$transporters_details;
			$this->load->view('admin',$data);
		}
		
		/**
		 * function to validate tray name used while processing tray add/edit form
		 * @param unknown_type $str
		 */
		function _valid_transportername($str)
		{
			$this->erpm->auth();
			
			$transporter_id = $this->input->post('transporter_id');
			if($transporter_id)
				$cond = ' and id != '.$transporter_id;
			else
				$cond = '';
			
			$transp_name =$this->input->post('name');;
			if($this->db->query("select count(*) as t from pnh_transporter_info where name = ?  $cond",$transp_name)->row()->t)
			{
				$this->form_validation->set_message('_valid_transportername',$transp_name.' is already available ');
				return false;
			}
			return true;
		}
		
		
		/**
		 * function for add a transporter details
		 */
		function jx_pnh_addtransporter()
		{
			$user=$this->erpm->auth();
			
			// output data hanlded for processing in client end
			$output = array();
			
			// validate form inputs tray name and max allowed entries
			
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name','name','required|callback__valid_transportername');
			$this->form_validation->set_rules('contact','contact','required|integer|max_length[12]|min_length[10]');
			$this->form_validation->set_rules('address','address','required');
			$this->form_validation->set_rules('city','city','required');
			$this->form_validation->set_rules('pincode','pincode','required');
			$this->form_validation->set_rules('transport_type','Transport type','required');
			
			// if formvalidation failed , sent errors via output variable
			if($this->form_validation->run() === FALSE)
			{
				$output['status'] = 'error';
				$output['error'] = validation_errors();
			}else
			{
				// prepare form inputs to array for insert
				$ins_data = array();
				$ins_data['name']=$this->input->post('name');
				$ins_data['address']=$this->input->post('address');
				$ins_data['city']=$this->input->post('city');
				$ins_data['pincode']=$this->input->post('pincode');
				$ins_data['contact_no']=$this->input->post('contact');
				$ins_data['active']=1;
				$ins_data['created_on']=cur_datetime();
				$ins_data['created_by']=$user['userid'];
				$ins_data['allowed_transport']=implode(',',$this->input->post('transport_type'));
				$this->db->insert('pnh_transporter_info',$ins_data);
			
				// fetch new tray id and update to output varaible
				$output['status'] = 'success';
				$output['tray_id'] = $this->db->insert_id();
			}
			
			echo json_encode($output);
			
		}
			
		/**
		 * function for get transporter details
		 * @param unknown_type $tranporter_id
		 */
		function jx_get_transporterdetbyid($tranporter_id=0)
		{
			$user=$this->erpm->auth();
			$output = array();
			
			// Query to get tray details from tray id
			$this->db->where('id',$tranporter_id);
			$transport_det_res = $this->db->get('pnh_transporter_info');
			if($transport_det_res->num_rows())
			{
				$output['status'] = 'success';
				$output['transport_det'] = $transport_det_res->row_array();
			}else
			{
				$output['status'] = 'error';
				$output['error'] = 'Invalid tray in reference';
			}
			
			echo json_encode($output);
		}
		
		/**
		 * function for update transporter details
		 */
		function jx_pnh_edittransporter()
		{
			$user=$this->erpm->auth();
			
			$output = array();
			
			// validate form inputs tray name and max allowed entries
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name','name','required|callback__valid_transportername');
			$this->form_validation->set_rules('contact','contact','required|integer|max_length[12]|min_length[10]');
			$this->form_validation->set_rules('address','address','required');
			$this->form_validation->set_rules('city','city','required');
			$this->form_validation->set_rules('pincode','pincode','required');
			$this->form_validation->set_rules('transport_type','Transport type','required');
			
			
			// if formvalidation failed , sent errors via output variable
			if($this->form_validation->run() === FALSE)
			{
				$output['status'] = 'error';
				$output['error'] = validation_errors();
			}else
			{
				$transporter_id = $this->input->post('transporter_id');
			
				// prepare form inputs to array for insert
				$ins_data = array();
				$ins_data['name']=$this->input->post('name');
				$ins_data['address']=$this->input->post('address');
				$ins_data['city']=$this->input->post('city');
				$ins_data['pincode']=$this->input->post('pincode');
				$ins_data['contact_no']=$this->input->post('contact');
				$ins_data['active']=1;
				$ins_data['modified_on']=cur_datetime();
				$ins_data['modified_by']=$user['userid'];
				$ins_data['allowed_transport']=implode(',',$this->input->post('transport_type'));
				
				$this->db->where('id',$transporter_id);
				$this->db->update('pnh_transporter_info',$ins_data);
			
				// fetch new tray id and update to output varaible
				$output['status'] = 'success';
				$output['transporter_id'] = $transporter_id;
			}
			
			echo json_encode($output);
			
		}
		
		/**
		 * function for manage transporter destinations address
		 */
		function pnh_manage_trans_desc_address($tranport_id=0)
		{
			$user=$this->erpm->auth();
			
			$transporter_details=$this->db->query("select name,allowed_transport from pnh_transporter_info where id=?",$tranport_id)->row_array();
			
			$trans_desc_address=$this->db->query("select * from pnh_transporter_dest_address where transpoter_id=?",$tranport_id)->result_array();
			$data['trans_desc_address']=$trans_desc_address;
			$data['page']='pnh_trans_desc_address';
			$data['trans_name']=$transporter_details['name'];
			$data['transporter_id']=$tranport_id;
			$data['transporter_types']=$transporter_details['allowed_transport'];
			$this->load->view('admin',$data);
			
		}
		
		function jx_get_transporter_dest_address_detbyid($transporter_des_id,$transporter_id)
		{
			$user=$this->erpm->auth();
			$output = array();
			
			// Query to get tray details from tray id
			$transport_des_address = $this->db->query("select * from pnh_transporter_dest_address where id=? and transpoter_id=?",array($transporter_des_id,$transporter_id));
			
			if($transport_des_address->num_rows())
			{
				$output['status'] = 'success';
				$output['transport_des_address'] = $trans_dest_addr =  $transport_des_address->row_array();
				$output['trans_dest_addr_alloted_types'] = @$this->db->query("select group_concat(type) as types from pnh_transporter_dest_address where dest_addr_unqid = ? and id != ? ",array($trans_dest_addr['dest_addr_unqid'],$trans_dest_addr['id']))->row()->types;
				 
			}else
			{
				$output['status'] = 'error';
				$output['error'] = 'Invalid destination address id in reference';
			}
			
			echo json_encode($output);
		}
		
		function jx_pnh_addtransporter_des_address()
		{
			$user=$this->erpm->auth();
			
			// output data hanlded for processing in client end
			$output = array();
			
			// validate form inputs tray name and max allowed entries
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name','name','required');
			
			$contact_list = $this->input->post('contact');
			foreach($contact_list as $i=>$cl)
				$this->form_validation->set_rules('contact['.$i.']','Contact '.($i+1),'required|integer|max_length[12]|min_length[10]');
			
			$this->form_validation->set_rules('address','address','required');
			$this->form_validation->set_rules('city','city','required');
			$this->form_validation->set_rules('pincode','pincode','required');
			$this->form_validation->set_rules('trasport_type','Transport type','required');
			
			// if formvalidation failed , sent errors via output variable
			if($this->form_validation->run() === FALSE)
			{
				$output['status'] = 'error';
				$output['error'] = validation_errors();
			}else
			{
				// prepare form inputs to array for insert
				
				$tranpost_types=$this->input->post('trasport_type');
				$dest_addr_unqid = 0;
				
				$tranpost_types = implode(',',$tranpost_types);
				
				
				$ins_data = array();
				$ins_data['transpoter_id']=$this->input->post('transporter_id');
				$ins_data['short_name']=$this->input->post('name');
				$ins_data['address']=$this->input->post('address');
				$ins_data['city']=$this->input->post('city');
				$ins_data['pincode']=$this->input->post('pincode');
				$ins_data['contact_no']=implode(',',$this->input->post('contact'));
				$ins_data['active']=1;
				$ins_data['created_on']=cur_datetime();
				$ins_data['created_by']=$user['userid'];
				$ins_data['type']=$tranpost_types;
				$this->db->insert('pnh_transporter_dest_address',$ins_data);
				$last_insid = $this->db->insert_id();
				
				// fetch new tray id and update to output varaible
				$output['status'] = 'success';
				
				
				
			}
			
			echo json_encode($output);
			
		}
		
		function jx_pnh_edittransporter_des_address()
		{
			$user=$this->erpm->auth();
			
			$output = array();
			
			// validate form inputs tray name and max allowed entries
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name','name','required');
			
			$contact_list = $this->input->post('contact');
			foreach($contact_list as $i=>$cl)
				$this->form_validation->set_rules('contact['.$i.']','Contact '.($i+1),'required|integer|max_length[12]|min_length[10]');
			
			$this->form_validation->set_rules('address','address','required');
			$this->form_validation->set_rules('city','city','required');
			$this->form_validation->set_rules('pincode','pincode','required');
			
			$trans_type = $this->input->post('trasport_type');
			if(!is_array($trans_type))
			{
				$this->form_validation->set_rules('trasport_type','trasport_type','required');	
			}
				
			
			// if formvalidation failed , sent errors via output variable
			if($this->form_validation->run() === FALSE)
			{
				$output['status'] = 'error';
				$output['error'] = validation_errors();
			}else
			{
				$transporter_id = $this->input->post('transporter_id');
				$transporter_des_address_id = $this->input->post('transporter_desc_id');
				$transport_type=$this->input->post('trasport_type');
				
				$transport_type = implode(',',$transport_type);
				$ins_data = array();
				$ins_data['short_name']=$this->input->post('name');
				$ins_data['address']=$this->input->post('address');
				$ins_data['city']=$this->input->post('city');
				$ins_data['pincode']=$this->input->post('pincode');
				$ins_data['contact_no']=implode(',',$this->input->post('contact'));
				$ins_data['active']=1;
				$ins_data['type']=$transport_type;
				$ins_data['modified_on']=cur_datetime();
				$ins_data['modified_by']=$user['userid'];
				$this->db->where('id',$transporter_des_address_id);
				$this->db->update('pnh_transporter_dest_address',$ins_data);
				
				$output['status'] = 'success';	
			}
			echo json_encode($output);
			
		}
		
		/**
		 * function for get the territory and towns by invoices
		 */
		function jx_get_territory_by_invoices()
		{
			$user=$this->erpm->auth(PNH_SHIPMENT_MANAGER);
			
			$invoices=$this->input->post('invoices');
			$manifesto_id=$this->input->post('manifesto_id');
			
			$output=array();
			
			$sql="select  group_concat(distinct a.invoice_no) as invoice_no,d.territory_name,e.town_name from king_invoice a 
							join king_transactions b on b.transid =a.transid
							join pnh_m_franchise_info c on c.franchise_id=b.franchise_id
							join  pnh_m_territory_info d on d.id=c.territory_id
							join  pnh_towns e on e.id=c.town_id
					where a.invoice_no in ($invoices) and a.invoice_status=1
					group by d.id,e.id
					order by d.id,e.id
					;";
			$output['territory_by_inv']=$this->db->query($sql)->result_array();
			
			$output['lrno']=$this->db->query("select lrno from pnh_m_manifesto_sent_log where id=?",$manifesto_id)->row()->lrno;
			
			echo json_encode($output);
		}
		
		/**
		 * get the bus tranport destination address by bus id in ajax request
		 */
		function jx_get_bustrs_des_address()
		{
			$user=$this->erpm->auth();
			$bus_id=$this->input->post('bus_id');
			$transport_type=$this->input->post('transport_type');
			
			$output=array();
			$output['dest_address_list']=$this->db->query("select * from pnh_transporter_dest_address where transpoter_id=? and find_in_set(?,type) ",array($bus_id,$transport_type))->result_array();
			echo json_encode($output,true);
			
		}
		
		/**
		 * get the bus transport details
		 */
		function jx_bus_transport_details()
		{
			$user=$this->erpm->auth();
			$bus_id=$this->input->post('bus_id');
			$dest_id=$this->input->post('dest_id');
			$manifesto_id=$this->input->post('manifesto_id');
			
			$sql="select a.name,a.address,a.contact_no ,b.short_name,b.address as d_address,b.contact_no as d_contact_no,c.transport_type as type  
							from pnh_m_manifesto_sent_log c
							join pnh_transporter_info a on c.bus_id=a.id 
							join pnh_transporter_dest_address b on b.transpoter_id=a.id
							where a.id=? and b.id=? and c.id=?;";
			$bus_details=$this->db->query($sql,array($bus_id,$dest_id,$manifesto_id))->result_array();
			
			
			$output=array();
			$output['bus_details']=$bus_details;
			echo json_encode($output);
		}
		
		/**
		 * get the vehicle details 
		 */
		function jx_vehicle_details()
		{
			$user=$this->erpm->auth(PNH_SHIPMENT_MANAGER);
			$manifesto_id=$this->input->post('manifesto_id');
			$output=array();
			$output['vehicle_details']=$this->db->query("select hndleby_vehicle_num,start_meter_rate,amount from pnh_m_manifesto_sent_log where id=?",$manifesto_id)->result_array();
			echo json_encode($output);
		}
		
		/**
		 * function for get the invoices by tray
		 */
		function jx_get_invbytray()
		{
			$user=$this->erpm->auth();
			$tray_id=$this->input->post('tray_id');
			$output=array();
			
			$sql="select group_concat(distinct b.invoice_no) as invoice_nos,h.hub_name as territory_name,g.town_name from pnh_t_tray_territory_link a
							join pnh_t_tray_invoice_link b on b.tray_terr_id=a.tray_terr_id
							join king_invoice c on c.invoice_no = b.invoice_no and c.invoice_status=1
							join king_transactions d on d.transid=c.transid
							join pnh_m_franchise_info e on e.franchise_id =d.franchise_id 
							join pnh_deliveryhub_town_link f on f.town_id = e.town_id and f.is_active = 1 
							join pnh_deliveryhub h on h.id = f.hub_id
							join pnh_towns g on g.id = e.town_id
							where a.is_active=1 and b.status=1 and a.tray_id=?
							group by g.id,f.id ";
			$inv_by_tray=$this->db->query($sql,$tray_id)->result_array();
			$output['inv_by_Tray']=$inv_by_tray;
			
			echo json_encode($output);
		}

		/**
		 * function to load franchises list in ajax  
		*/
		function jx_getfranchiseslist($type=0,$alpha=0,$terr_id=0,$town_id=0,$pg=0)
		{
			$this->erpm->auth();
			$user=$this->erpm->getadminuser();
			
			if(!$type)
			{
				$type = $this->input->post('type');
				$alpha = $this->input->post('alpha');
				$terr_id = $this->input->post('terr_id');
				$town_id = $this->input->post('town_id');
				$pg = $this->input->post('pg');
			}
			
			$alpha = ($alpha!='')?$alpha:0;
			$terr_id = $terr_id*1;
			$town_id = $town_id*1;
			
			$cond = $fil_cond = '';
			
			
			$sql="select f.created_on,f.is_suspended,group_concat(a.name) as owners,tw.town_name as town,f.is_lc_store,f.franchise_id,c.class_name,c.margin,c.combo_margin,f.pnh_franchise_id,f.franchise_name,
							f.locality,f.city,f.current_balance,f.login_mobile1,f.login_mobile2,
							f.email_id,u.name as assigned_to,t.territory_name 
						from pnh_m_franchise_info f 
						left outer join king_admin u on u.id=f.assigned_to 
						join pnh_m_territory_info t on t.id=f.territory_id 
						join pnh_towns tw on tw.id=f.town_id 
						join pnh_m_class_info c on c.id=f.class_id 	
						left outer join pnh_franchise_owners ow on ow.franchise_id=f.franchise_id 
						left outer join king_admin a on a.id=ow.admin
						where 1 
					";
					
			if($type == 2)
			{
				$cond .= " and date(from_unixtime(f.created_on)) between ADDDATE(LAST_DAY(SUBDATE(curdate(), INTERVAL 1 MONTH)), 1) and LAST_DAY(SUBDATE(curdate(), INTERVAL 0 MONTH)) ";
				$sql .= $fil_cond = $cond; 
			}
					
				
				if($alpha)
				{
					$cond .= " and franchise_name like '$alpha%' ";
					$sql .= $fil_cond = $cond; 
				}
					
				if($type == 4)
				{
					$cond .= " and f.is_suspended != 0 ";
					$sql .= $fil_cond = $cond;
				}
				else if($type == 1)
				{
					
				}else if($type != 5)
				{
					$cond .=  " and f.is_suspended = 0 ";
					$sql .= $fil_cond = $cond;
				}
				
				if($terr_id)
					$sql .= ' and f.territory_id = '.$terr_id;
					
				if($town_id)
					$sql .= ' and f.town_id = '.$town_id;	
									
				$sql .= " group by f.franchise_id "; 
				if($type == 1)
					$sql .= " order by f.created_on desc limit 20 ";	
				else
					$sql .= " order by f.franchise_name asc limit $pg,50";	
				  
				$data['frans']= $this->db->query($sql)->result_array();
				
				if($type == 1)
				{
					$data['total_frans'] = count($data['frans']);
				}else
				{
					$data['terr_list'] = $this->db->query("select distinct f.territory_id,territory_name from pnh_m_franchise_info f join pnh_m_territory_info b on f.territory_id = b.id where 1 $fil_cond order by territory_name  ")->result_array();
					
					if($terr_id)
						$fil_cond .= ' and f.territory_id = '.$terr_id;
						
					if($terr_id)
						$data['town_list'] = $this->db->query("select distinct f.town_id,town_name from pnh_m_franchise_info f join pnh_towns b on f.town_id = b.id where 1 $fil_cond order by town_name  ")->result_array();
					else
						$data['town_list'] = array();
					
					$data['total_frans'] = $this->db->query("select distinct f.franchise_id from pnh_m_franchise_info f where 1 $fil_cond group by f.franchise_id ")->num_rows();
					
				}
				
				$data['pagination'] =  '' ;
				if($type != 1)
				{
					$this->load->library('pagination');
				
					$config['base_url'] = site_url("admin/jx_getfranchiseslist/$type/$alpha/$terr_id/$town_id");
					$config['total_rows'] = $data['total_frans'];
					$config['per_page'] = 50;
					$config['uri_segment'] = 7; 
					$config['num_links'] = 10;
					
					$this->config->set_item('enable_query_strings',false);
					$this->pagination->initialize($config); 
					
					$data['pagination'] = $this->pagination->create_links();
					$this->config->set_item('enable_query_strings',true);
				}
				$data['sel_terr_id'] = $terr_id;
				$data['sel_town_id'] = $town_id; 
				$data['type'] = $type;
				$data['alpha'] = $alpha;
				$data['pg'] = $pg;
				
				
				$this->load->view("admin/body/jx_show_franchises",$data);
			}
			
	function jx_load_receiptdetails($type='def',$pg=0)
	{
		// compute total rows
		$tbl_total_rows = 0;
			
		//prepare output table header
		$tbl_head = array();
		$tbl_data = array();
			
		// check for type
		if($type=='post_dated')
		{
			$sql = "SELECT r.*,f.franchise_name,a.name AS admin FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by WHERE r.status=0 AND r.is_active=1 AND date(from_unixtime(instrument_date)) <= curdate() AND f.is_suspended=0 and is_submitted=0 and r.status=0  ORDER BY instrument_date asc
					limit $pg,$limit";
			
			
			$receipt_details_res=$this->db->query($sql);
				
			$tbl_head = array('slno'=>'Slno','receipt_id'=>'Receipt Details','cheque_details'=>'Cheque Details');
			
					if($receipt_details_res->num_rows())
			{
			foreach($receipt_details_res->result_array() as $i=>$log_det)
				{
					$tbl_data[] = array('slno'=>$i+1,'receipt_id'=>'Receipt Id:'.$log_det['receipt_id'].'<br>'.'Created By:'.$log_det['admin'].'Created On:'.$log_det['created_on'],'cheque_details'=>'Amount:'.$log_det['amount'].'<br>'.'Cheque Date:'.$log_det['instrument_date']);
				}
			}
			
		}
		
		if(count($tbl_data))
		{
			$tbl_data_html = '<table cellpadding="5" cellspacing="0" class="datagrid datagridsort">';
			$tbl_data_html .= '<thead>';
			foreach($tbl_head as $th)
				$tbl_data_html .= '<th>'.$th.'</th>';
		
			$tbl_data_html .= '</thead>';
			$i = $pg;
			$tbl_data_html .= '<tbody>';
			foreach($tbl_data as $tdata)
			{
				$tbl_data_html .= '<tr>';
				foreach(array_keys($tbl_head) as $th_i)
				{
					if($th_i == 'slno')
						$tdata[$th_i] = $i+1;
		
					$tbl_data_html .= '	<td>'.$tdata[$th_i].'</td>';
				}
				$tbl_data_html .= '</tr>';
		
				$i = $i+1;
			}
			$tbl_data_html .= '</tbody>';
			$tbl_data_html .= '</table>';
		}else
		{
			$tbl_data_html = '<div align="center"> No data found</div>';
		}
		
		$this->load->library('pagination');
		
		$config['base_url'] = site_url('admin/jx_load_receiptdetails/'.$type.'/'.$terr_id.'/'.$emp_id);
		$config['total_rows'] = $tbl_total_rows;
		$config['per_page'] = $limit;
		$config['uri_segment'] = 6;
			
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$pagi_links = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
			
		$pagi_links = '<div class="log_pagination">'.$pagi_links.'</div>';
			
		echo json_encode(array('log_data'=>$tbl_data_html,'pagi_links'=>$pagi_links,'type'=>$type,'pg'=>$pg));
			
		}

		
	/**
	 * function get the buses list
	 */	
	function jx_get_buses_list()
	{
		$this->erpm->auth();
		
		$output=array();
		$transport_type=$this->input->post('tranport_type');
		
		$buses_details=$this->db->query("select distinct b.id,b.name,b.contact_no from pnh_transporter_dest_address a 
											join pnh_transporter_info b on b.id=a.transpoter_id
											where find_in_set(?,a.type) 
										",$transport_type)->result_array();
		$output['buses_list']=$buses_details;
		echo json_encode($output);
		
	}	
	
	/**
	 * update office pick details
	 */
	function update_office_pick_up_details()
	{
		$this->erpm->auth();
		
		if(!$_POST)
		{
			redirect('admin/view_manifesto_sent_log');
		}
			
		$office_pick_ups=$this->input->post('office_pick_up');
		$manifesto_id=$this->input->post('manifesot_id');
		$param=array();
		$param['office_pickup_empid']=implode(',',$office_pick_ups);
		
		$is_array=0;
		$is_array=stripos($manifesto_id,',');
		if($is_array > 0)
		{
			foreach(explode(',',$manifesto_id) as $id)
			{
				$this->db->where("id",$id);
				$this->db->update('pnh_m_manifesto_sent_log',$param);
			}
		}else{
				$this->db->where("id",$manifesto_id);
				$this->db->update('pnh_m_manifesto_sent_log',$param);
				
			}
		
		
		$this->session->set_flashdata("erp_pop_info","Office pick up updated");
		
		redirect($_SERVER['HTTP_REFERER']);
		
	}
	
	function jx_get_office_pick_up_details()
	{
		$this->erpm->auth();
		$manifesto_id=$this->input->post('manifesto_id');
		
		$output=array();
		
		$output['manifesto_det']=$this->db->query("select  a.id,b.short_name,b.type,c.name from pnh_m_manifesto_sent_log a 
														join  pnh_transporter_dest_address b on b.id=a.bus_destination 
														join pnh_transporter_info c on c.id = b.transpoter_id where a.id=?",$manifesto_id)->row_array();
		
		$output['office_pickup_list']=$this->db->query("select * from m_employee_info where job_title2=8")->result_array();
		
		echo json_encode($output);
	}
	
	function jx_get_office_pickup_by_manifesto()
	{
		$this->erpm->auth();
		$manifesto_id=$this->input->post('manifesto');
		
		$output=array();
		$output['error']='';
		$emp_id=@$this->db->query("select office_pickup_empid from pnh_m_manifesto_sent_log where id=?",$manifesto_id)->row()->office_pickup_empid;
		
		if($emp_id)
		{
			$output['employe_list']=$this->db->query("select * from m_employee_info where job_title2=8 and employee_id in ($emp_id)")->result_array();
			
		}else{
			$output['error']='Office pick up not updated';
		}
		
		echo json_encode($output); 
	}
	
	function pnh_inv_notshipped_list($pg = 0)
	{
		
	}
	
	function update_lr_number_in_sent_manifesto()
	{
		$use_det=$this->erpm->auth();
		
		if(!$_POST)
			die;
		$send_sms=array();
		$manifesto_sentid=$this->input->post('manifesto_sent_id');
		
		$lr_number=$this->input->post('lr_number');
		$amount=$this->input->post('amount');
		$courier_id=$this->input->post('courier_id');
		
		$send_sms[]=$territory_manager=$this->input->post('Tm');
		$send_sms[]=$bussniss_executive=$this->input->post('BE');
		$send_sms=array_filter($send_sms);
		
		if($manifesto_sentid)
		{
			$user_id=$use_det['userid'];
			$sent_man_id=$manifesto_sentid;
			$this->db->query("update pnh_m_manifesto_sent_log set status=3,modified_on=?,modified_by=?,lrno=?,amount=?,lrn_updated_on=? where id=?",array(cur_datetime(),$user_id,$lr_number,$amount,cur_datetime(),$sent_man_id));
		
			//update shiped status
			$manifesto_id_det=$this->db->query("select sent_invoices,manifesto_id,pickup_empid from pnh_m_manifesto_sent_log where id=?",$sent_man_id)->result_array();
			$manifesto_id=$manifesto_id_det[0]['manifesto_id'];
			$pick_up_emp_id=$manifesto_id_det[0]['pickup_empid'];
			$invoices=$manifesto_id_det[0]['sent_invoices'];
		
			//inert transaction table log
			foreach(explode(',',$invoices) as $inv)
			{
				$trans_logprm=array();
				$trans_logprm['transid']=$this->db->query("select transid from king_invoice where invoice_no=? limit 1",$inv)->row()->transid;
				$trans_logprm['admin']=$use_det['userid'];
				$trans_logprm['time']=time();
				$trans_logprm['msg']='invoice ('.$inv.') Shipped';
				$this->db->insert("transactions_changelog",$trans_logprm);
			}
			
			//update manifesto id to shipment_batch_process_invoice_link
			$prama3=array();
			$prama3['shipped']=1;
			$prama3['shipped_on']=cur_datetime();
			$prama3['shipped_by']=$user_id;
			$prama3['awb']=$lr_number;
		
			$this->db->where('inv_manifesto_id',$manifesto_id);
			$this->db->update("shipment_batch_process_invoice_link",$prama3);
			
			if($this->db->affected_rows())
			{
				$this->erpm->sendsms_franchise_shipments($invoices);
			}
			
			$this->erpm->update_return_inv_shipped_status($manifesto_id);
			
			
			// mark shipped status in orders table  
			$inv_order_list = $this->db->query(" select order_id 
													from shipment_batch_process_invoice_link a 
													join king_invoice b on a.invoice_no = b.invoice_no  
													where a.inv_manifesto_id = ?  
													group by order_id 
												",$manifesto_id);
			if($inv_order_list->num_rows())
			{
				foreach($inv_order_list->result_array() as $row)
					$this->db->query("update king_orders set status = 2,actiontime = unix_timestamp() where id = ? ",$row['order_id']);
			}
		
			//sent a sms
			$sent_invoices=$this->db->query('select a.hndlby_type,a.hndlby_roleid,a.sent_invoices,b.name,a.hndleby_name,b.contact_no,a.hndleby_contactno,a.hndleby_vehicle_num,
					c.name as  bus_name,d.contact_no as des_contact,e.courier_name,a.id
					from pnh_m_manifesto_sent_log a
					left join m_employee_info b on b.employee_id = a.hndleby_empid
					left join pnh_transporter_info c on c.id=a.bus_id
					left join pnh_transporter_dest_address d on d.id=a.bus_destination and d.transpoter_id=a.bus_id
					left join m_courier_info e on e.courier_id = a.hndleby_courier_id
					where a.id=?',$sent_man_id)->result_array();
		
			$invoices=$sent_invoices[0]['sent_invoices'];
			$hndbyname=$sent_invoices[0]['name'];
			$hndbycontactno=$sent_invoices[0]['contact_no'];
			$vehicle_num=$sent_invoices[0]['hndleby_vehicle_num'];
			
			if($sent_invoices[0]['hndlby_roleid']==6)
			{
				$hndbyname='Driver '.$sent_invoices[0]['hndleby_name'];
				$hndbycontactno=$sent_invoices[0]['hndleby_contactno'];
			}else if($sent_invoices[0]['hndlby_roleid']==7)
			{
				$hndbyname='Fright Co-ordinator '.$sent_invoices[0]['hndleby_name'];
				$hndbycontactno=$sent_invoices[0]['hndleby_contactno'];
			}else if($sent_invoices[0]['hndlby_type']==4)
			{
				$hndbyname='Courier '.$sent_invoices[0]['courier_name'];
			}else if($sent_invoices[0]['hndlby_type']==3 && $sent_invoices[0]['bus_name'])
			{
				$hndbyname='Transporter '.$sent_invoices[0]['bus_name'];
				$hndbycontactno=$sent_invoices[0]['des_contact'];
			}
			
			$this->db->query("update pnh_invoice_transit_log set status = 1,logged_on=now() where sent_log_id = ? and status = 0 ",$sent_man_id);
		
			if($this->db->affected_rows())
			{
				$pick_up_by_details=$this->db->query("select a.employee_id,a.job_title2,a.name,a.contact_no from m_employee_info a where employee_id=?",$pick_up_emp_id)->row_array();
		
				$employees_list=$this->erpm->get_emp_by_territory_and_town($invoices);
				
				if ($employees_list)
				{
						$town_list=array();
						foreach($employees_list as $emp)
						{
							$emp_name=$emp['name'];
							$emp_id=$emp['employee_id'];
							$town_name=$emp['town_name'];
							$territory_id=$emp['territory_id'];
							$town_id=$emp['town_id'];
							$town_list[]=$emp['town_name'];
							$job_title=$emp['job_title2'];
							$send_sms_status=$emp['send_sms'];
							$emp_contact_nos = explode(',',$emp['contact_no']);
							
							if(!in_array($job_title,$send_sms))
							{
								continue;
							}
							
				
							$sms_msg = 'Dear '.$emp_name.',Shipment for the town '.$town_name.' sent via '.ucwords($hndbyname).'('.$hndbycontactno.') Lr no: '.$lr_number.'.Manifesto Id : '.$sent_invoices[0]['id'];
							$temp_emp=array();
							foreach($emp_contact_nos as $emp_mob_no)
							{
								if(isset($temp_emp[$emp_id]))
									continue;
									$temp_emp[$emp_id]=1;
								
								if($send_sms_status)
									$this->erpm->pnh_sendsms($emp_mob_no,$sms_msg);
								//	echo $emp_mob_no,$sms_msg;
								$log_prm=array();
								$log_prm['emp_id']=$emp_id;
								$log_prm['contact_no']=$emp_mob_no;
								$log_prm['type']=4;
								$log_prm['territory_id']=$territory_id;
								$log_prm['town_id']=$town_id;
								$log_prm['grp_msg']=$sms_msg;
								$log_prm['created_on']=cur_datetime();
								$this->erpm->insert_pnh_employee_grpsms_log($log_prm);
							}
				
						}
					}
		
					if($pick_up_by_details)
					{
						$pemp_name=$pick_up_by_details['name'];
						$pemp_id=$pick_up_by_details['employee_id'];
						$pemp_contact_nos = explode(',',$pick_up_by_details['contact_no']);
						
						//pick up by
							if($town_list)
							{
								$town_list=array_unique($town_list);
								$town_list=array_filter($town_list);
							}else{
									$town_list=array();
								}
				
							$sms_msg = 'Dear '.$pemp_name.',Shipment for the town '.implode(',',$town_list).' sent via '.ucwords($hndbyname).'('.$hndbycontactno.') Lr no:'.$lr_number.'.Manifesto Id : '.$sent_invoices[0]['id'];
						
							$temp_emp=array();
							foreach($pemp_contact_nos as $emp_mob_no)
							{
								if(isset($temp_emp[$pemp_id]))
									continue;
									$temp_emp[$pemp_id]=1;
							
									$this->erpm->pnh_sendsms($emp_mob_no,$sms_msg);
							
									$log_prm2=array();
									$log_prm2['emp_id']=$pemp_id;
									$log_prm2['contact_no']=$emp_mob_no;
									$log_prm2['type']=4;
									$log_prm2['grp_msg']=$sms_msg;
									$log_prm2['created_on']=cur_datetime();
							
								$this->db->insert("pnh_employee_grpsms_log",$log_prm2);
							}
						}
			}
		}
		$this->session->set_flashdata("erp_pop_info","your selected invoices are shipped");
		redirect($_SERVER['HTTP_REFERER']);
	}

	function pnh_manage_delivery_hub()
	{
		$user=$this->erpm->auth();
			
		$hub_list=$this->db->query("select a.id,a.hub_name,group_concat(distinct town_name) as linked_towns,group_concat(distinct f.name) as linked_fcs,a.created_on,c.username as created_byname   
											from pnh_deliveryhub a 
											join pnh_deliveryhub_town_link b on a.id = b.hub_id 
											join king_admin c on c.id = a.created_by
											join pnh_towns d on d.id = b.town_id
											left join pnh_deliveryhub_fc_link e on a.id = e.hub_id and e.is_active = 1  
											left join m_employee_info f on f.employee_id = e.emp_id 
											where b.is_active = 1   
											group by a.id 
											order by hub_name ")->result_array();
		
		$data['hub_list']=$hub_list;
		$data['page']='pnh_manage_delivery_hub';
		
		$this->load->view('admin',$data);
	}
	
	function jx_gettownslistforhub()
	{
		$output = array();
		$output['town_list'] = $this->db->query("	select distinct a.id,a.town_name,ifnull(c.hub_id,0) as hub_id,sum(ifnull(c.is_active,0)) as has_entry 
														from pnh_towns a 
														join pnh_m_territory_info b on a.territory_id = b.id 
														left join pnh_deliveryhub_town_link c on c.town_id = a.id and e.is_active = 1 
														group by a.id 
														order by town_name
												")->result_array();
		echo json_encode($output);
	}

	/**
	 * function to get hub details 
	 */
	function jx_gethubdet($hub_id)
	{
		$hub_id = $hub_id*1;
		$output = array();
		
		if($hub_id)
		{
			$output['hubdet'] = $this->db->query("select hub_name from pnh_deliveryhub a join pnh_deliveryhub_town_link b on a.id = b.hub_id where b.is_active = 1 and hub_id = ? ",$hub_id)->row_array();
		}
		
		$output['town_list'] = $this->db->query("select id,town_name,hub_id,sum(if(hub_id=$hub_id,1,0)) as is_linked from (
														select a.id,a.town_name,ifnull(hub_id,0) as hub_id 
														from pnh_towns a 
														join pnh_m_territory_info b on a.territory_id = b.id 
														left join pnh_deliveryhub_town_link c on c.town_id = a.id and c.is_active = 1 
														group by a.id,hub_id 
														) as g
														group by id  
														order by town_name  ")->result_array();
												
		 $output['fc_list'] = $this->db->query("select employee_id,emp_name,sum(if(hub_id=$hub_id,1,0)) as is_linked from (
													select a.employee_id,a.name as emp_name,ifnull(hub_id,0) as hub_id 
														from m_employee_info a 
														left join pnh_deliveryhub_fc_link b on a.employee_id = b.emp_id and is_active = 1 
														where job_title2 = 6 
														group by hub_id,employee_id 
														order by emp_name ) as g 
													group by employee_id 
												")->result_array();
		 
		
		$output['status'] = 'success';
		echo json_encode($output);
	}
	
	function pnh_upd_hubinfo()
	{
		
		
		$output = array();
		
		$user = $this->erpm->auth();
		
		$hub_id = $this->input->post('hub_id');
		$hub_name = $this->input->post('hub_name');
		$town_id_list = $this->input->post('town_id');
		$fc_id_list = $this->input->post('fc_id');
		
		
		$det = array();
		$det['hub_name'] = $hub_name;
		
		if(!$hub_id)
		{
			$det['created_by'] = $user['userid'];
			$det['created_on'] = date('Y-m-d H:i:s');
			$this->db->insert('pnh_deliveryhub',$det);
			$hub_id = $this->db->insert_id();
		}else
		{
			$det['modified_by'] = $user['userid'];
			$det['modified_on'] = date('Y-m-d H:i:s');
			$this->db->update('pnh_deliveryhub',$det,array('id'=>$hub_id));
		}

		$this->db->query("update pnh_deliveryhub_town_link set is_active = 2 where hub_id = ? ",array($hub_id));
		foreach($town_id_list as $town_id)
		{
			// check if town is already available
			if(!$this->db->query("select count(*) as t from pnh_deliveryhub_town_link  where hub_id = ? and town_id = ? and is_active = 2 ",array($hub_id,$town_id))->row()->t)
			{
				$tdet = array();
				$tdet['hub_id'] = $hub_id;
				$tdet['is_active'] = 1;
				$tdet['town_id'] = $town_id;
				$tdet['created_by'] = $user['userid'];
				$tdet['created_on'] = date('Y-m-d H:i:s');
				$this->db->insert('pnh_deliveryhub_town_link',$tdet);
			}else
			{
				$this->db->query("update pnh_deliveryhub_town_link set is_active = 1 where hub_id = ? and town_id = ? and is_active = 2 ",array($hub_id,$town_id));
			}	
			
		}
		
		$this->db->query("update pnh_deliveryhub_town_link set is_active = 0,modified_by=?,modified_on=now() where hub_id = ? and is_active = 2 ",array($user['userid'],$hub_id));
		
		
		$this->db->query("update pnh_deliveryhub_fc_link set is_active = 2 where hub_id = ? ",array($hub_id));
		if($fc_id_list)
		{
			foreach($fc_id_list as $emp_id)
			{
				// check if town is already available
				if(!$this->db->query("select count(*) as t from pnh_deliveryhub_fc_link  where hub_id = ? and emp_id = ? and is_active = 2 ",array($hub_id,$town_id))->row()->t)
				{
					$tdet = array();
					$tdet['hub_id'] = $hub_id;
					$tdet['is_active'] = 1;
					$tdet['emp_id'] = $emp_id;
					$tdet['created_by'] = $user['userid'];
					$tdet['created_on'] = date('Y-m-d H:i:s');
					$this->db->insert('pnh_deliveryhub_fc_link',$tdet);
				}else
				{
					$this->db->query("update pnh_deliveryhub_fc_link set is_active = 1 where hub_id = ? and fc_id = ? and is_active = 2 ",array($hub_id,$emp_id));
				}	
			}
		}
		
		$this->db->query("update pnh_deliveryhub_fc_link set is_active = 0,modified_by=?,modified_on=now() where hub_id = ? and is_active = 2 ",array($user['userid'],$hub_id));
		
		if($hub_id)
		{
			$output['status'] = 'success';	
			$output['message'] = 'Delivery Hub details updated';
		}
		else
		{
			$output['status'] = 'error';
			$output['error'] = 'Hub not created';
		}	
			
		echo json_encode($output);
		
	}
	
	function pnh_delete_hubdet($hub_id)
	{
		$output = array();
		
		$this->db->query("update pnh_deliveryhub set is_active=0 where is_active=1 and id = ? ",$hub_id);
		$this->db->query("update pnh_deliveryhub_town_link set is_active=0 where is_active=1 and hub_id = ? ",$hub_id);
		$this->db->query("update pnh_deliveryhub_fc_link set is_active=0 where is_active=1 and hub_id = ? ",$hub_id);
		
		$output['status'] = 'success';
		
		echo json_encode($output);
		
	}
	
	/**
	 * function to generate delivery label print by manifesto id
	 */
	function print_deliverylabel($man_id)
	{
		$sql="select b.name as pick_up_name,b.contact_no as pick_up_contact,c.short_name,c.contact_no,c.address 
						from pnh_m_manifesto_sent_log a 
						join m_employee_info b on b.employee_id=a.pickup_empid
						join pnh_transporter_dest_address c on c.id = a.bus_destination
					where a.id=?";
		
		$data['destination_details']=$this->db->query($sql,$man_id)->result_array();
		$this->load->view('admin/body/pnh_manifesto_print_deliverylabel',$data);
	}
	
	function products_report()
	{
		$this->erpm->auth(DEAL_MANAGER_ROLE);
		$data['pnh_menus_list']=$this->db->query("select id,name from pnh_menu where status=1")->result_array();
		$data['sit_menus_list']=$this->db->query("select id,name from king_menu where status=1")->result_array();
		$data['page']="product_report";
		$this->load->view("admin",$data);
	}
	
	function jx_load_product_report()
	{
		$this->erpm->auth(DEAL_MANAGER_ROLE);
		$output=array();
		$pnh_menu_id=$this->input->post('pnh_menu_id');
		$sit_menu_id=$this->input->post('sit_menu_id');
		$alpha=$this->input->post('alpha');
		
		$data['pnh_menu_id']=$pnh_menu_id;
		$data['sit_menu_id']=$sit_menu_id;
		$data['alpha']=$alpha;
		$output['product_report']=$this->load->view('admin/body/jx_product_report',$data,true);
		echo json_encode($output);
	}

	function pnh_update_dp_price()
	{
		$this->erpm->auth(UPDATE_DP_PRICE);
		$data['brand_list']=$this->db->query("select id,name from king_brands order by name asc")->result_array();
		
		$data['page']="pnh_update_dp_price";
		$this->load->view("admin",$data);
	}

	function jx_getcatbybrand($brandid='')
	{
		$this->erpm->auth(UPDATE_DP_PRICE|DEAL_MANAGER_ROLE);
		$output = array();
		$output['cat_list']=$this->db->query("select distinct c.id,c.name from king_deals a 
										join king_dealitems b on a.dealid = b.dealid 
										join king_categories c on c.id = a.catid 
									where is_pnh = 1 and brandid = ?  
									order by c.name ",$brandid)->result_array();
		
		echo json_encode($output); 
	} 
	
	function jx_getdealsbybrandcat($brandid=0,$catid=0,$pg=0)
	{
		$this->erpm->auth(UPDATE_DP_PRICE|DEAL_MANAGER_ROLE);
		$output = array();
		$output['pg'] = $pg;
		
		$all_rows = 0;
		$cond = '';
		$cond_param = array();
		if($brandid)
		{
			$cond .= ' and brandid = ? ';
			$cond_param [] = $brandid;
			$all_rows = 1;
		}
		
		if($catid)
		{
			$cond .= ' and catid = ? ';
			$cond_param [] = $catid;
			$all_rows = 1;
		}
		
		
		$output['deal_ttl']=$this->db->query("select  count(*) as ttl  
															from king_deals a 
															join king_dealitems b on a.dealid = b.dealid 
															join m_product_deal_link c on c.itemid = b.id and c.is_active = 1
															join m_product_info e on e.product_id = c.product_id
															where is_pnh = 1 $cond   
													 ",$cond_param)->row()->ttl;
													 
		if($all_rows)
			$limit_cnt = $output['deal_ttl'];
		else
			$limit_cnt = 100;
													
		$output['deal_list']=$this->db->query("select  pnh_id,b.id,b.name,b.orgprice,b.price,b.live,a.publish,round((1-((b.price/b.orgprice)))*100,1) as offer_perc,e.product_id,e.is_sourceable 
															from king_deals a 
															join king_dealitems b on a.dealid = b.dealid
															join m_product_deal_link c on c.itemid = b.id and c.is_active = 1
															join m_product_info e on e.product_id = c.product_id      
															where is_pnh = 1 $cond   
													order by b.name limit $pg,$limit_cnt  
												",$cond_param)->result_array();
		
		$this->load->library('pagination');
		$config['base_url'] = site_url("admin/jx_getdealsbybrandcat/$brandid/$catid");
		$config['total_rows'] = $output['deal_ttl'];
		$config['uri_segment'] = 5;
		$config['per_page'] = $limit_cnt;
		
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$output['pagination'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		
		echo json_encode($output);
	}
	
	function jx_upd_dealdpprice()
	{
		$user = $this->erpm->auth(UPDATE_DP_PRICE);
		
		$itemid = $this->input->post('id');
		$price = $this->input->post('price');
		
		$item_det = $this->db->query('select * from king_dealitems where id = ? ',$itemid)->row_array();
		 
		$is_updated = 0;	
		if($price <= $item_det['orgprice'])	
		{
			$this->db->query("update king_dealitems set price = ? where id = ?  limit 1 ",array($price,$itemid));
			
			if($this->db->affected_rows())
			{
				$is_updated = 1;
				
				$price_upt_prm=array();
				$price_upt_prm['itemid']=$itemid;
				$price_upt_prm['old_mrp']=$item_det['orgprice'];
				$price_upt_prm['new_mrp']=$item_det['orgprice'];
				$price_upt_prm['new_price']=$price;
				$price_upt_prm['old_price']=$item_det['price'];
				$price_upt_prm['created_by']=$user['userid'];
				$price_upt_prm['created_on']=time();
				$price_upt_prm['reference_grn']=0;
				
				$this->db->insert('deal_price_changelog',$price_upt_prm);
				
				// Disable special margin if set 
				$this->db->query("update pnh_special_margin_deals set is_active = 0 where i_price != ? and itemid = ? and is_active = 1 " ,array($price,$itemid));
						
			}
				
		}
		
		
		$output = array();
		if($is_updated)
		{
			$output['status'] = 'success';
			$output['price'] = $price;
		}
		else
			$output['status'] = 'error';
			
		echo json_encode($output);
	} 

	function getfran_creditlimit($fid=0)
	{
		print_r($this->erpm->get_fran_availcreditlimit($fid));
		$acc_statement = $this->erpm->get_franchise_account_stat_byid($fid);
		print_r($acc_statement);
	}

	function list_activesuperscheme($franchise_id='',$menuid='',$month=0,$year=0,$pg=0)
	{
		$cond='';
		
		if($month)
			$cond.=' and MONTH(DATE(FROM_UNIXTIME(o.time)))='.$month;
		
		if($year)
			$cond.='.and YEAR(DATE(FROM_UNIXTIME(o.time)))='.$year;
		
		if($franchise_id)
			$cond .=' and f.franchise_id='.$franchise_id;
		
		if($menuid) 
			$cond .='.and d.menuid='.$menuid;
		
		
		
		$super_schlist=$this->db->query("SELECT f.pnh_franchise_id,f.franchise_id, f.franchise_name, f.city, inv.invoice_no, o.transid, d.menuid, m.name AS menuname, date_format(DATE(FROM_UNIXTIME(o.time)),'%d/%m/%y') AS order_date, DATE(FROM_UNIXTIME(inv.createdon)) AS invoice_date,
											b.name AS brand, i.name AS deal, inv.mrp, inv.discount, (inv.mrp - inv.discount) AS landing_cost, o.quantity AS deal_qty, (inv.mrp-inv.discount)*o.quantity AS sub_total,o.has_super_scheme,o.super_scheme_target,o.super_scheme_cashback,o.super_scheme_logid,
											IFNULL(SUM(o.i_orgprice-(o.i_coup_discount+o.i_discount))*o.quantity,0) AS total_sales,s.valid_from,s.created_on,c.name AS catname,s.valid_to
											FROM king_orders o
											JOIN king_invoice inv ON inv.order_id = o.id
											JOIN king_dealitems i ON i.id = o.itemid
											JOIN king_deals d ON d.dealid = i.dealid
											JOIN pnh_menu m ON m.id = d.menuid
											JOIN king_transactions t ON t.transid = o.transid
											JOIN pnh_m_franchise_info f ON f.franchise_id = t.franchise_id
											JOIN pnh_super_scheme s ON s.id=o.super_scheme_logid
											LEFT JOIN king_brands b ON b.id = s.brand_id
											LEFT JOIN king_categories c ON c.id = s.cat_id
											WHERE invoice_status = 1 AND t.is_pnh = 1 AND o.has_super_scheme=1  AND o.super_scheme_target!=0 AND o.super_scheme_processed=0 AND o.time BETWEEN s.valid_from AND s.valid_to $cond
											GROUP BY o.super_scheme_logid
											ORDER BY f.franchise_name, b.name, i.name"); 
		
		$data['super_schlist']=$super_schlist;
		$franchises = $this->erpm->to_get_all_franchise();
		
		$this->load->library('pagination');
		$config['base_url'] = site_url('/admin/list_activesuperscheme/'.$franchise_id);
		$config['total_rows'] = $this->db->query("SELECT COUNT(*) as total,f.pnh_franchise_id, f.franchise_name, f.city, inv.invoice_no, o.transid, d.menuid, m.name AS menuname, DATE(FROM_UNIXTIME(o.time)) AS order_date, DATE(FROM_UNIXTIME(inv.createdon)) AS invoice_date,
											b.name AS brand, i.name AS deal, inv.mrp, inv.discount, (inv.mrp - inv.discount) AS landing_cost, o.quantity AS deal_qty, (inv.mrp-inv.discount)*o.quantity AS sub_total,o.has_super_scheme,o.super_scheme_target,o.super_scheme_cashback,o.super_scheme_logid,
											IFNULL(SUM(o.i_orgprice-(o.i_coup_discount+o.i_discount))*o.quantity,0) AS total_sales,s.valid_from,c.name AS catname,s.valid_to
											FROM king_orders o
											JOIN king_invoice inv ON inv.order_id = o.id
											JOIN king_dealitems i ON i.id = o.itemid
											JOIN king_brands b ON b.id = o.brandid
											JOIN king_deals d ON d.dealid = i.dealid
											JOIN king_categories c ON c.id = d.catid
											JOIN pnh_menu m ON m.id = d.menuid
											JOIN king_transactions t ON t.transid = o.transid
											JOIN pnh_m_franchise_info f ON f.franchise_id = t.franchise_id
											JOIN pnh_super_scheme s ON s.id=o.super_scheme_logid
											WHERE invoice_status = 1 AND t.is_pnh = 1 AND
											 o.has_super_scheme=1  AND o.super_scheme_target!= 0 AND o.super_scheme_processed=0  AND o.time BETWEEN s.valid_from AND s.valid_to  $cond
											GROUP BY o.super_scheme_logid
											ORDER BY f.franchise_name, b.name, i.name")->row()->total;
		$config['per_page'] = MAX_ROWS_DISP;
		$config['uri_segment'] = 3;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$pagination = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		$data['pg']=$pg;
		$data['franchises']=$franchises;
		$data['pagination'] ='';
		if($franchise_id && $menuid)
		{
		$data['pagetitle'] ="Super scheme details of ".$this->db->query("select franchise_name from pnh_m_franchise_info where franchise_id=?",$franchise_id)->row()->franchise_name .','.'Menu-'.$this->db->query("select name from pnh_menu where id=?",$menuid)->row()->name;
		}
		
		//$monthNum = 5;
		$monthname = date("F", mktime(0, 0, 0, $month, 10));
		if($month && $year)
		{
			$data['pagetitle']="Super scheme details of ".$monthname.'-'.$year;
		}
		$data['page']='pnh_superscheme_log';
		$this->load->view("admin",$data);
	}
	
	/**
	 * invoices adding to existing manifesto
	 */
	function add_inv_to_existing_manifesto()
	{
		$user=$this->auth(KFILE_ROLE);
		$user_det=$this->session->userdata("admin_user");
		
		//get the data throught a post
		$manifesto_id=0;
		$manifesto_id=$this->input->post("manifesto_list");
		$invoices_no=$this->input->post("invoice_nos");
		
		$manifesto_details=$this->db->query("select * from pnh_m_manifesto_sent_log where id=?",$manifesto_id)->row_array();
		
		$main_manifesto_det=$this->db->query("select invoice_nos from pnh_manifesto_log where id=?",$manifesto_details['manifesto_id'])->row_array();
		
		if($main_manifesto_det)
		{
			$ins_data=array();
			$ins_data['invoice_nos']=$main_manifesto_det['invoice_nos'].','.implode(',',$invoices_no);	
			$ins_data['modified_on'] = date('Y-m-d H:i:s');
			$ins_data['modified_by'] = $user['userid'];
			$this->db->where('id',$manifesto_details['manifesto_id']);
			$this->db->update("pnh_manifesto_log",$ins_data);
			
			foreach($invoices_no as $inv)
			{
				$trans_logprm=array();
				$trans_logprm['transid']=$this->db->query("select transid from king_invoice where invoice_no=? limit 1",$inv)->row()->transid;
				$trans_logprm['admin']=$user['userid'];
				$trans_logprm['time']=time();
				$trans_logprm['msg']='Manifesto created for this invoice ('.$inv.')';
				$this->db->insert("transactions_changelog",$trans_logprm);
			}
		}
		
		//link the manifesto id and invoice number to shipment_batch_process_invoice link table
		$invoices=implode(',',$invoices_no);
		$this->db->query("update shipment_batch_process_invoice_link set inv_manifesto_id=? where invoice_no in ($invoices) ",array($manifesto_details['manifesto_id']));
		
		$sent_manifesto_logid=0;
		if($manifesto_details)
		{
			$param1=array();
			$param1['sent_invoices']=$manifesto_details['sent_invoices'].','.implode(',',$invoices_no);
			$param1['remark']=$manifesto_details['remark'].','.implode(',',$invoices_no).' this invoices are newly add this manifesto';
			$param1['modified_on']=cur_datetime();
			$param1['modified_by']=$user_det['userid'];
			$this->db->where('id',$manifesto_id);
			$this->db->update("pnh_m_manifesto_sent_log",$param1);
			$sent_manifesto_logid = $manifesto_id;
		}
		
		
		foreach($invoices_no as $invno)
		{
			// insert invnos to transit table
				$ins_data = array();
				$ins_data['sent_log_id'] = $sent_manifesto_logid;
				$ins_data['invoice_no'] = $invno;
				$ins_data['ref_id'] = $manifesto_details['hndleby_empid'];
				$ins_data['status'] = 0;
				$ins_data['logged_on'] = cur_datetime();
				$ins_data['logged_by'] = $user_det['userid'];
				$this->db->insert("pnh_invoice_transit_log",$ins_data);
		}
		
		$this->session->set_flashdata("erp_pop_info","Invoices added to selected manifesto");
		redirect(site_url('admin/pnh_pending_shipments'));
	}
	
	/**
	 * function for cancel the manifesto
	 */
	function cancel_manifesto($manifesto_id=0)
	{
		$user=$this->erpm->auth();
		$user_det=$this->session->userdata("admin_user");
		if($manifesto_id)
		{
			$manifesto_details=$this->db->query("select * from pnh_m_manifesto_sent_log where id=? and status=1",$manifesto_id)->row_array();
			if($manifesto_details)
			{
				$invoices_no=explode(',',$manifesto_details['sent_invoices']);
				$invoices=$manifesto_details['sent_invoices'];
				
				foreach($invoices_no as $inv)
				{
					$trans_logprm=array();
					$trans_logprm['transid']=$this->db->query("select transid from king_invoice where invoice_no=? limit 1",$inv)->row()->transid;
					$trans_logprm['admin']=$user['userid'];
					$trans_logprm['time']=time();
					$trans_logprm['msg']='This invoice '.$inv.' Manifesto '.$manifesto_id.' canceled';
					$this->db->insert("transactions_changelog",$trans_logprm);
				}
				
				$this->db->query("update shipment_batch_process_invoice_link set inv_manifesto_id=0 where invoice_no in ($invoices) and inv_manifesto_id=? ",array($manifesto_details['manifesto_id']));
				
				$this->db->query("update pnh_m_manifesto_sent_log set status=4,modified_on=?,modified_by=? where id=?",array(cur_datetime(),$user['userid'],$manifesto_id));
				
				if($this->db->affected_rows())
				{
					//$this->db->query("update pnh_invoice_transit_log set status=99 where sent_log_id=?",$manifesto_id);
					$this->db->query("delete from pnh_invoice_transit_log where status=0 and sent_log_id=?",$manifesto_id);
					$this->session->set_flashdata("erp_pop_info","Manifesto Canceled");
				}
			}
		}
		
		redirect(site_url('admin/view_manifesto_sent_log'));
	}
	
	function pnh_superscheme_deal($itemid)
	{
		$user=$this->auth(SPECIAL_MARGIN_UPDATE);
		
		foreach(array("super_schstatus","from","to","reason") as $i)
			$$i=$this->input->post($i);
		$from=strtotime($from);
		$to=strtotime($to);
		$menuid=$this->db->query("SELECT b.menuid FROM `king_dealitems`a JOIN `king_deals`b ON b.dealid=a.dealid WHERE a.id=?",$itemid)->row()->menuid;
		$inp=array("itemid"=>$itemid,"menuid"=>$menuid,"is_active"=>$super_schstatus,"valid_from"=>$from,"valid_to"=>$to,"reason"=>$reason,"created_by"=>$user['userid'],"created_on"=>time());
		$this->db->insert('pnh_superscheme_deals',$inp);
		$this->erpm->flash_msg("Super scheme status updated");
		if(!isset($_POST['internal']))
			redirect("admin/pnh_deal/$itemid");
	}
	
	function pnh_memberscheme_deal($itemid)
	{
		$user=$this->auth(SPECIAL_MARGIN_UPDATE);
	
		foreach(array("mbr_schstatus","from","to") as $i)
			$$i=$this->input->post($i);
		$from=strtotime($from);
		$to=strtotime($to."23:59:59");
		$menuid=$this->db->query("SELECT b.menuid FROM `king_dealitems`a JOIN `king_deals`b ON b.dealid=a.dealid WHERE a.id=?",$itemid)->row()->menuid;
		$inp=array("itemid"=>$itemid,"menuid"=>$menuid,"is_active"=>$mbr_schstatus,"valid_from"=>$from,"valid_to"=>$to,"created_by"=>$user['userid'],"created_on"=>time());
		$this->db->insert('pnh_membersch_deals',$inp);
		$this->erpm->flash_msg("Member scheme status updated");
		if(!isset($_POST['internal']))
			redirect("admin/pnh_deal/$itemid");
	}
	
	//function jx_pnh_prod_previousorderd()
	/* function jx_pnh_prod_previousorderd()
	{
		$user=$this->auth();
	
		$output=array();
		$pids=explode(",",$this->input->post("pids"));
		$itemids=$this->db->query("select id,pnh_id from king_dealitems where is_pnh=1 and pnh_id in('".implode("','",$pids)."')")->result_array();
	
		$fid=$_POST['fid'];
		foreach ($itemids as $i)
		$previously_orderd = $this->db->query("SELECT distinct  d.name,date(from_unixtime(b.time)) as time,b.quantity,b.status,a.franchise_id,d.is_combo,d.orgprice AS mrp,d.price,round(sum((i_orgprice-(i_coup_discount+i_discount))*b.quantity),2) as paid
													FROM king_transactions a
													JOIN king_orders b ON a.transid = b.transid
													JOIN pnh_m_franchise_info c ON c.franchise_id = a.franchise_id
													JOIN `king_dealitems` d ON d.id=b.itemid
													JOIN king_deals i ON i.dealid=d.dealid
													WHERE a.franchise_id = ? AND b.itemid=? AND b.status=2 AND shipped=1
													group by name
													order by time desc
													",array($fid,$i['id']));
		//echo $this->db->last_query();
		if($previously_orderd->num_rows())
		{
			$output['status']='success';
			$output['prev_orderd']=$previously_orderd->result_array();
		}
		else 
		{
			$output['status']='error';
		}
		echo json_encode($output);
	}*/
	
	/*function jx_pnh_orderdprod_unshipped()
	{
		$output=array();
		$pids=explode(",",$this->input->post("pids"));
		$itemids=$this->db->query("select id,pnh_id from king_dealitems where is_pnh=1 and pnh_id in('".implode("','",$pids)."')")->result_array();
		$fid=$_POST['fid'];
		foreach ($itemids as $i)
			$previouslyorderd_unshipped = $this->db->query("select k.name,b.transid,sum(o.i_coup_discount) as com,b.amount,o.quantity,o.transid,o.status,DATE(FROM_UNIXTIME(o.time)) AS `time`,o.actiontime,pu.user_id as userid,pu.pnh_member_id
															from king_orders o 
															join king_transactions b on o.transid = b.transid 
															JOIN `king_dealitems` k ON k.id=o.itemid
															JOIN king_deals i ON i.dealid=k.dealid
															left join proforma_invoices c on c.order_id = o.id
															left join shipment_batch_process_invoice_link d on d.p_invoice_no = c.p_invoice_no and d.shipped = 0 
															join pnh_member_info pu on pu.user_id=o.userid 
															where b.franchise_id = ? and o.status = 0  and o.itemid=?
															group by k.name 
															order by b.init desc 
															",array($fid,$i['id']));
		//echo $this->db->last_query();
		if($previouslyorderd_unshipped->num_rows())
		{
			$output['status']='success';
			$output['previouslyorderd_unshipped']=$previouslyorderd_unshipped->result_array();
		}
		else
		{
			$output['status']='error';
		}
		echo json_encode($output);
	} */
	
	/**
	 * function to load details of orderd product
	 */
	function jx_to_load_productdata()
	{
		$output=array();
		$pids=explode(",",$this->input->post("pids"));
	//	$itemids=$this->db->query("select id,pnh_id from king_dealitems where is_pnh=1 and pnh_id in('".implode("','",$pids)."')")->result_array();
		$fid=$_POST['fid'];
		$ttl_orderd=$this->db->query("SELECT i.pnh_id,i.id,i.name,SUM(1) AS ttl_orders,IF(f.shipped,SUM(1),0) AS ttl_shipped_orders,SUM(a.quantity) AS ttl_qty,IF(f.shipped,SUM(a.quantity),0) AS shipped  
											FROM king_orders a
											JOIN king_transactions b ON b.transid=a.transid
											JOIN king_dealitems i ON i.id=a.itemid
											JOIN king_deals d ON i.dealid=d.dealid
											LEFT JOIN king_invoice e ON e.order_id = a.id AND invoice_status = 1 
											LEFT JOIN shipment_batch_process_invoice_link f ON f.invoice_no = e.invoice_no AND f.shipped = 1  
											WHERE  i.pnh_id IN ('".implode("','",$pids)."') AND b.franchise_id = ? 
											GROUP BY i.id 
											ORDER BY i.id",$fid);
/* echo $this->db->last_query();
die; */
		if($ttl_orderd->num_rows())
		{
			$output['status']='success';
			$output['ttl_orderd']=$ttl_orderd->result_array();
		}

		
		if(empty($ttl_orderd))
		{
			$output['status']='error';
		}
		
		echo json_encode($output);
	}
	 
	 function jx_pnh_ord_prod_unshipped()
	 {
	 	$output=array();
	 	$fid=$_POST['fid'];
	 	if($fid)
	 		$unshipped_details=$this->db->query("SELECT b.transid,DATE_FORMAT(FROM_UNIXTIME(a.time),'%d/%m/%Y %h:%i %p') AS orderdon FROM king_orders a
													JOIN king_transactions b ON b.transid=a.transid
													WHERE a.status=0 AND b.batch_enabled=1 AND franchise_id=?
	 												Group BY b.transid",$fid);
	 	if($unshipped_details->num_rows())
	 	{
	 		$output['status']='success';
	 		$output['unship_det']=$unshipped_details->result_array();
	 	}
	 	else
	 	{
	 		$output['status']='error';
	 	}
	 	echo json_encode($output);
	 }
	 
	 /**
	 * function to load product for an deal 
	 */
	function pnh_products_by_deal($item_id=0)
	{
		$this->erpm->auth();
		if(!$item_id)
			die();
		
		$prod_list=$this->db->query("select p.product_id,p.product_name,l.qty from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$item_id)->result_array();
		if(!count($prod_list))
			show_error("invalid deal in reference, no products linked to deal.");
		
		redirect('admin/product/'.$prod_list[0]['product_id']);
	}

	/**
	 * function for update the employee sms status
	 */
	function jx_update_emp_sms_status()
	{
		$this->erpm->auth();
		if(!$_POST)
			die();
		$status=$this->input->post('status');
		$emp_id=$this->input->post('emp_id');
		$output=array();
		
		$this->db->query("update m_employee_info set send_sms=? where employee_id=?",array($status,$emp_id));
		
		if($this->db->affected_rows())
		{
			$output['status']='success';
			$output['message']='sms status changed';
		}else{
			$output['status']='error';
			$output['message']='sms status not changed';
		}
		
		echo json_encode($output);
	}
	
	/**
	 * update the courier transport invoices status to delivered
	 */
	function jx_mark_delivered_status_for_courier_traspost()
	{
		$user=$this->erpm->auth();
		
		if(!$_POST)
			die();
		
		$send_log_id=$this->input->post('send_log_id');
		$invoices_list=$this->input->post('delivered');
		$received_by=$this->input->post('received_by');
		$received_on=$this->input->post('received_on');
		$contact_no=$this->input->post('contact_no');
		
		if($invoices_list)
		{
			foreach($invoices_list as $i=>$incoice)
			{
				$param=array();
				$param['sent_log_id']=$send_log_id;
				$param['invoice_no']=$incoice;
				$param['status']=3;
				
				if(!$received_on[$i] && $received_on[$i]=='')
					show_error("Delivered date must be require");
				
				$param['received_by']=$received_by[$i];
				$param['received_on']=$received_on[$i];
				$param['contact_no']=$contact_no[$i];
				$param['logged_on']=cur_datetime();
				$param['logged_by']=$user['userid'];
				
				$already=$this->db->query("select count(*) as ttl from pnh_invoice_transit_log where invoice_no=? and status=3",array($incoice))->row()->ttl;
				if(!$already)
				{
					$this->db->insert('pnh_invoice_transit_log',$param);
					$this->session->set_flashdata("erp_pop_info","Selected invoices status to be updated");
				}else{
					$this->session->set_flashdata("erp_pop_info","Selected invoices status already updated");
				}
			}
			
		}
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	/**
	 * function for check the invoice delivered status
	 */
	function jx_check_invoice_dlvrstatus()
	{
		$user=$this->erpm->auth();
		
		if(!$_POST)
			die();
		$invoices=$this->input->post('invoices');
		$invoice_det=array();
		$output=array();
		
		foreach(explode(',',$invoices) as $invoice)
		{
			$inv_trans_det = array('received_on_f'=>'','received_by'=>'','contact_no'=>'','received_on'=>'');
			$already=0;
			$inv_transit_res=$this->db->query("select * from pnh_invoice_transit_log where invoice_no=? and status=3",array($invoice));
			if($inv_transit_res->num_rows())
			{
				$inv_trans_det = $inv_transit_res->row_array();
				if(is_null($inv_trans_det['received_on']))
					$inv_trans_det['received_on_f'] = '';
				else
					$inv_trans_det['received_on_f'] = date('Y-m-d',$inv_trans_det['received_on']);
				$already=1;
			}
				
			array_push($invoice_det,array('inv'=>$invoice,'st'=>$already,'det'=>$inv_trans_det));
		}
		
		$output['invoice_det']=$invoice_det;
		echo json_encode($output);
	}
	
	/**
	 * function for remove the invoice from packed list
	 */
	function pnh_remove_invoice_in_packed_list()
	{
		$user=$this->auth(ADMINISTRATOR_ROLE);
		if(!$_POST)
			die();
		$invoices=$this->input->post('invoice_nos');
		
		if($invoices)
		{
			$inv=implode(',',$invoices);
			
			$this->db->query("update shipment_batch_process_invoice_link set tray_id=0,packed_by=0,packed_on=0,packed=0 where invoice_no in ($inv) limit 1");
			
			if($this->db->affected_rows())
			{
				$tray_territory_id=array();
				$cur_datetime=cur_datetime();
				foreach ($invoices as $invno)
				{
					$tray_inv_status=2;
					
					// remove shipments/invoice from tray
					$tray_inv_link = $this->db->query("select tray_inv_id,tray_terr_id from pnh_t_tray_invoice_link where invoice_no = ? and status = 1 order by tray_inv_id desc limit 1 ",$invno)->row_array();
				
					// update tray invoice status = 2 for invno
					$this->db->query("update pnh_t_tray_invoice_link set status = $tray_inv_status,modified_on=?,modified_by=? where status = 1 and tray_inv_id = ? ",array($cur_datetime,$user['userid'],$tray_inv_link['tray_inv_id']));
					$tray_territory_id[]=$tray_inv_link['tray_terr_id'];
					
				}
				
				foreach($tray_territory_id as $id)
				{
					$tray_inv_link = $this->db->query("select tray_inv_id,tray_terr_id,invoice_no from pnh_t_tray_invoice_link where tray_terr_id=? and status = 1 order by tray_inv_id desc limit 1 ",$id);
					
					if($tray_inv_link->num_rows())
					{
						
					}else{
						$this->db->query("update pnh_t_tray_territory_link set is_active = 0,modified_on=?,modified_by=? where is_active = 1 and tray_terr_id = ? ",array($cur_datetime,$user['userid'],$id));
					}
				}
				
				$this->session->set_flashdata("erp_pop_info","Selected invoices removed in packed list");
			}
		}
		
		redirect($_SERVER['HTTP_REFERER']);;
	}
	
	function jx_check_manifesto()
	{
		$user=$this->auth(ADMINISTRATOR_ROLE);
		if(!$_POST)
			die();
		$output=array();
		$manifesto_id=$this->input->post('manifesto');
		
		$manifesto_det=$this->db->query("select * from pnh_m_manifesto_sent_log where id=?",$manifesto_id);
		
		if($manifesto_det->num_rows())
		{
			$manifesto_det=$manifesto_det->result_array();
			
			//check manifesto delivered
			$is_del=$this->erpm->check_manifesto_delivered($manifesto_id);
			
			if($is_del)
			{
				$output['status']='error';
				$output['message']='Manifesto Delivered';
			}else{
				$output['status']='success';
				$output['message']='';
			}
			
		}else{
			$output['status']='error';
			$output['message']='Manifesto not found';
		}
		
		echo json_encode($output);
	}
	
	function pnh_add_manifesto_alternative_number()
	{
		$user=$this->auth(ADMINISTRATOR_ROLE);
		if(!$_POST)
			die();
		$manifesto_id=$this->input->post('ch_manifesto_id');
		$mobile_number=$this->input->post('alternative_number');
		
		$is_del=$this->erpm->check_manifesto_delivered($manifesto_id);
		if($is_del)
		{
			$this->session->set_flashdata("erp_pop_info","Given Manifesto already delivered");
			redirect($_SERVER['HTTP_REFERER']);
			
		}else{
			$this->db->query("update pnh_m_manifesto_sent_log set alternative_contactno=? where id=?",array($mobile_number,$manifesto_id));
			if($this->db->affected_rows())
				$this->session->set_flashdata("erp_pop_info","Alternative mobile number updated");
			else
				$this->session->set_flashdata("erp_pop_info","Alternative mobile number not updated");
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
	/**
	 * function update the delivered invoice acknowledgement
	 */
	function pnh_scan_delivery_akw()
	{
		$data['page']='pnh_scan_delivery_akw';
		$this->load->view('admin',$data);
	}
	
	/**
	 * function for scan the delivery acknowledgement
	 */
	function scan_delivery_akw()
	{
		$user=$this->erpm->auth();
		if(!$_POST)
			die();
		$invoice_no=$this->input->post('invoice_no');
		
		$inv_exist=$this->db->query("select count(*) as ttl from shipment_batch_process_invoice_link where invoice_no=? and invoice_no!=0",$invoice_no)->row()->ttl;
		
		if(!$inv_exist)
			die("<div class='invoice_not_found' style='background:#cd0000;'>AWB/Invoice/Order No:{$invoice_no} not found</div>");
		
		$is_shiped=$this->db->query("select count(*) as ttl from shipment_batch_process_invoice_link where invoice_no=? and invoice_no!=0 and shipped=1",$invoice_no)->row()->ttl;
		
		if(!$is_shiped)
			die("<div class='invoice_not_shipped' style='background:purple;'>AWB/Invoice/Order No:{$invoice_no} not Shipped</div>");
		
				$is_delivered=$this->db->query("select count(*) as ttl from pnh_invoice_transit_log where invoice_no=? and status in (3,5)",$invoice_no)->row()->ttl;
		
		if(!$is_delivered)
			die("<div class='invoice_not_delivered' style='background:orange;'>AWB/Invoice/Order No:{$invoice_no} not Delivered</div>");
		
		$is_akw=$this->db->query("select count(*) as ttl from shipment_batch_process_invoice_link where invoice_no=? and invoice_no!=0 and is_acknowleged=1",$invoice_no)->row()->ttl;
		
		if($is_akw)
			die("<div class='already_scaned' style='background:green;'>AWB/Invoice/Order No:{$invoice_no} already Acknowledged</div>");
		
		$this->db->query("update shipment_batch_process_invoice_link set is_acknowleged=?,is_acknowleged_by=?,is_acknowleged_on=? where invoice_no=? and invoice_no!=0 and shipped=1",array(1,$user['userid'],cur_datetime(),$invoice_no));
		
		if($this->db->affected_rows())
		{
			die("<div class='awkscanned' style='background:#f1f1f1;color: #000 !important;'>AWB/Invoice/Order No:{$invoice_no} is Acknowledged</div>");
		}
	}
	
	function to_get_salesdeatails_bymenufran($franid='',$logid='')
	{
		$user=$this->auth(FINANCE_ROLE);
		$output=array();
		$sales_res=$this->db->query("SELECT f.pnh_franchise_id, f.franchise_name, f.city, inv.invoice_no, o.transid, d.menuid, m.name AS menuname, date_format(DATE(FROM_UNIXTIME(o.time)),'%d/%m/%Y') AS order_date, DATE(FROM_UNIXTIME(inv.createdon)) AS invoice_date,
				b.name AS brand, i.name AS deal, inv.mrp, inv.discount, (inv.mrp - inv.discount) AS landing_cost, o.quantity AS deal_qty, (inv.mrp-inv.discount)*o.quantity AS sub_total,o.has_super_scheme,o.super_scheme_target,o.super_scheme_cashback,o.super_scheme_logid,o.itemid
				FROM king_orders o
				JOIN king_invoice inv ON inv.order_id = o.id
				JOIN king_dealitems i ON i.id = o.itemid
				JOIN king_brands b ON b.id = o.brandid
				JOIN king_deals d ON d.dealid = i.dealid
				JOIN pnh_menu m ON m.id = d.menuid
				JOIN king_transactions t ON t.transid = o.transid
				JOIN pnh_m_franchise_info f ON f.franchise_id = t.franchise_id
				JOIN pnh_super_scheme s ON s.id=o.super_scheme_logid
				WHERE invoice_status = 1 AND t.is_pnh = 1 AND DATE(FROM_UNIXTIME(o.time)) BETWEEN
				DATE(FROM_UNIXTIME(s.valid_from)) AND DATE(FROM_UNIXTIME(s.valid_to)) AND o.has_super_scheme=1  AND o.super_scheme_target!= 0
				AND f.franchise_id=? and o.super_scheme_logid=?
				ORDER BY f.franchise_name, b.name, i.name",array($franid,$logid));
		 
		if($sales_res->num_rows())
		{
			$output['sales']=$sales_res->result_array();
			$output['status']='success';
		}
		else
		{
			$output['status']='error';
			$output['message']='No details found';
		}

		echo json_encode($output);
	}

	function pnh_print_superscheme_by_menu($super_sch_logid='',$franchise_id='')
	{
		$user=$this->auth(FINANCE_ROLE);
		//create query to select as data from your table
		$sales_res=$this->db->query("SELECT f.pnh_franchise_id, f.franchise_name, f.city, inv.invoice_no, o.transid, d.menuid, m.name AS menuname, date_format(DATE(FROM_UNIXTIME(o.time)),'%d/%m/%Y') AS order_date, DATE(FROM_UNIXTIME(inv.createdon)) AS invoice_date,
				b.name AS brand, i.name AS deal, inv.mrp, inv.discount, (inv.mrp - inv.discount) AS landing_cost, o.quantity AS deal_qty, (inv.mrp-inv.discount)*o.quantity AS sub_total,o.has_super_scheme,o.super_scheme_target,o.super_scheme_cashback,o.super_scheme_logid
				FROM king_orders o
				JOIN king_invoice inv ON inv.order_id = o.id
				JOIN king_dealitems i ON i.id = o.itemid
				JOIN king_brands b ON b.id = o.brandid
				JOIN king_deals d ON d.dealid = i.dealid
				JOIN pnh_menu m ON m.id = d.menuid
				JOIN king_transactions t ON t.transid = o.transid
				JOIN pnh_m_franchise_info f ON f.franchise_id = t.franchise_id
				JOIN pnh_super_scheme s ON s.id=o.super_scheme_logid
				WHERE invoice_status = 1 AND t.is_pnh = 1 AND
				DATE(FROM_UNIXTIME(o.time)) BETWEEN
				DATE(FROM_UNIXTIME(s.valid_from)) AND DATE(FROM_UNIXTIME(s.valid_to)) AND o.has_super_scheme=1  AND o.super_scheme_target!= 0
				AND o.super_scheme_logid=?
				ORDER BY f.franchise_name, b.name, i.name",$super_sch_logid);
		$data['sales_res']=$sales_res;
		$data['fran']=$this->erpm->pnh_getfranchise($franchise_id);
		$data['super_scheme_details']=$this->db->query("select * from pnh_super_scheme where id=?",$super_sch_logid)->row_array();
		$this->load->view("admin/body/print_superscheme_salesbyfranmenu",$data);
	}

	function pnh_export_superschemesales_by_menu($super_sch_logid='',$franchise_id='')
	{
		$user=$this->auth(FINANCE_ROLE);
		$sales_res=$this->db->query("SELECT f.pnh_franchise_id, f.franchise_name, f.city, inv.invoice_no, o.transid, d.menuid, m.name AS menuname, date_format(DATE(FROM_UNIXTIME(o.time)),'%d/%m/%Y') AS order_date, DATE(FROM_UNIXTIME(inv.createdon)) AS invoice_date,
				b.name AS brand, i.name AS deal, inv.mrp, inv.discount, (inv.mrp - inv.discount) AS landing_cost, o.quantity AS deal_qty, (inv.mrp-inv.discount)*o.quantity AS sub_total,o.has_super_scheme,o.super_scheme_target,o.super_scheme_cashback,o.super_scheme_logid
				FROM king_orders o
				JOIN king_invoice inv ON inv.order_id = o.id
				JOIN king_dealitems i ON i.id = o.itemid
				JOIN king_brands b ON b.id = o.brandid
				JOIN king_deals d ON d.dealid = i.dealid
				JOIN pnh_menu m ON m.id = d.menuid
				JOIN king_transactions t ON t.transid = o.transid
				JOIN pnh_m_franchise_info f ON f.franchise_id = t.franchise_id
				JOIN pnh_super_scheme s ON s.id=o.super_scheme_logid
				WHERE invoice_status = 1 AND t.is_pnh = 1 AND
				DATE(FROM_UNIXTIME(o.time)) BETWEEN
				DATE(FROM_UNIXTIME(s.valid_from)) AND DATE(FROM_UNIXTIME(s.valid_to)) AND o.has_super_scheme=1  AND o.super_scheme_target!= 0
				AND o.super_scheme_logid=?
				ORDER BY f.franchise_name, b.name, i.name",$super_sch_logid);
		$data['sales_res']=$sales_res;
		$data['fran']=$this->erpm->pnh_getfranchise($franchise_id);
		$data['super_scheme_details']=$this->db->query("select * from pnh_super_scheme where id=?",$super_sch_logid)->row_array();
		$fr_sales_list = array();
		$fr_sales_heading="Super scheme Sales Summary";
		$fr_sales_list[] = '"Slno","FranchiseName","Deal Name","Quantity","Order Date","Landing Cost","Target value","Cashback"';
		if($sales_res->num_rows())
		{
			foreach($sales_res->result_array() as $row_f)
			{
				$fr_sales_det = array();
				$fr_sales_det[] = ++$i;
				$fr_sales_det[] = ucwords($row_f['franchise_name']);
				$fr_sales_det[] = ucwords($row_f['deal']);
				$fr_sales_det[] = ucwords($row_f['deal_qty']);
				$fr_sales_det[] = ucwords($row_f['order_date']);
				$fr_sales_det[] = ucwords($row_f['landing_cost']);
				$fr_sales_det[] = ucwords($row_f['super_scheme_target']);
				$fr_sales_det[] = ucwords($row_f['super_scheme_cashback']);
				$fr_sales_list[]='"'.implode('","',$fr_sales_det).'"';
			}
				
		}
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename=PNH_SUPER_SCHEME_SALES_REPORT_'.date('d_m_Y_H_i').'.csv');
		header('Pragma: no-cache');
		echo implode("\r\n",$fr_sales_list);
		
	}
	
	/**
	 * function to give cash back to franchise if sales is acheived
	 */
	function pnh_superscheme_give_cash_back()
	{
		$user=$this->auth(FINANCE_ROLE);
		$super_scheme_logid=$this->input->post('super_scheme_logid');
		$logids=explode(',', $super_scheme_logid);

		foreach($logids as $logid)
		{
			$super_scheme_processed=0;
			$super_scheme_sales=$this->db->query("SELECT f.pnh_franchise_id,f.franchise_id,f.franchise_name, f.city, inv.invoice_no, o.transid, d.menuid, m.name AS menuname, DATE(FROM_UNIXTIME(o.time)) AS order_date, DATE(FROM_UNIXTIME(inv.createdon)) AS invoice_date,
					b.name AS brand, i.name AS deal, inv.mrp, inv.discount, (inv.mrp - inv.discount) AS landing_cost, o.quantity AS deal_qty, (inv.mrp-inv.discount)*o.quantity AS sub_total,IFNULL(SUM(o.i_orgprice-(o.i_coup_discount+o.i_discount))*o.quantity,0) AS total_sales,
					o.super_scheme_processed,o.super_scheme_cashback,o.super_scheme_logid,o.id
					FROM king_orders o
					JOIN king_invoice inv ON inv.order_id = o.id
					JOIN king_dealitems i ON i.id = o.itemid
					JOIN king_brands b ON b.id = o.brandid
					JOIN king_deals d ON d.dealid = i.dealid
					JOIN pnh_menu m ON m.id = d.menuid
					JOIN king_transactions t ON t.transid = o.transid
					JOIN pnh_m_franchise_info f ON f.franchise_id = t.franchise_id
					WHERE invoice_status = 1 AND t.is_pnh = 1 AND
					o.super_scheme_logid=? and o.has_super_scheme=1 and o.super_scheme_processed=0
					ORDER BY f.franchise_name, b.name, i.name",$logid);
			if($super_scheme_sales->num_rows())
			{

				foreach($super_scheme_sales->result_array() as $s)
				{

					$this->db->query("update king_orders o join king_transactions t on t.transid=o.transid set o.super_scheme_processed=1 where o.super_scheme_logid=? and o.id=? ",array($logid,$s['id']));

					$super_scheme_processed=1;

					if($this->db->affected_rows()>0)
					{
						$type=0;
						$amount=$s['total_sales']*$s['super_scheme_cashback']/100;
						$desc='super scheme';
						$acc_correc_id=$s['super_scheme_logid'];

						//if target value is acheived insert into pnh_fran_account_stat table & pnh_franchise_account_summary table
						if($s['total_sales']>=$s['super_scheme_target'] && $s['super_scheme_processed']=1)
							$acc_stat_id = $this->erpm->pnh_fran_account_stat($s['franchise_id'],$type,$amount,$desc,"correction",$s['franchise_id']);
						if($this->db->affected_rows()>0)
							$remarks="super scheme cash back";
						$this->db->query("insert into pnh_franchise_account_summary(franchise_id,action_type,acc_correc_id,credit_amt,status,created_on,remarks)values(?,6,?,?,1,now(),?)",array($s['franchise_id'],$acc_correc_id,$amount,$remarks));

					}
				}
			}
		}
		$this->erpm->flash_msg("Super scheme Processed");
		redirect($_SERVER['HTTP_REFERER']);

	}
	
	function jx_getpnh_voucher_activitylog($type='fff',$franchise_id='',$pg=0)
	{
		$limit=5;
		$cond='';
		if($franchise_id)
			$cond='AND franchise_id='.$franchise_id;
		
		$tbl_head = array();
		$tbl_data = array();
		$tbl_attr='';
		
		if($type=='activated_vouchers')
		{
				
			$activity_ttl="select count(*) as total FROM pnh_t_voucher_details  WHERE is_activated=1 $cond";
				
			$ttl_rows=$this->db->query($activity_ttl)->num_rows();
			$activity_res="SELECT a.*,c.book_slno,d.denomination FROM pnh_t_voucher_details a  join pnh_t_book_voucher_link b on b.voucher_slno_id=a.id join pnh_t_book_details c on c.book_id=b.book_id join pnh_m_voucher d on d.voucher_id=a.voucher_id WHERE is_activated=1 and status>=3 $cond limit $pg,$limit";
			$v_log_details=$this->db->query($activity_res);
				
			$tbl_head=array('slno'=>'Sl no','book_slno'=>'Book slno','v_slno'=>'Voucher',"value"=>'Value(Rs)',"voucher_margin"=>"Voucher margin(%)","franchise_value"=>"Franchise value(Rs)",'mid'=>'Member ID','alloted_on'=>'Alloted on','activated_on'=>'Activated On');
			if($v_log_details->num_rows())
			{
				foreach($v_log_details->result_array() as $i=>$v_log_det)
				{
					$tbl_data[]=array('slno'=>$i+1,'book_slno'=>$v_log_det['book_slno'],'v_slno'=>$v_log_det['voucher_serial_no'],"value"=>$v_log_det['denomination'],"voucher_margin"=>$v_log_det['voucher_margin'],"franchise_value"=>$v_log_det['franchise_value'],'mid'=>$v_log_det['member_id'],'alloted_on'=>format_datetime($v_log_det['alloted_on']),'activated_on'=>format_datetime($v_log_det['activated_on']));
				}
			}
		}
		elseif($type=='fully_redeemed_vouchers')
		{
			$activity_ttl="select count(*) as total from pnh_t_voucher_details  where is_activated=1 and `status`=4 $cond";
			$ttl_rows=$this->db->query($activity_ttl)->num_rows();
			$activity_res="SELECT * FROM pnh_t_voucher_details WHERE is_activated=1 AND `status`=4 $cond";
			$v_log_details=$this->db->query($activity_res);
			$tbl_head=array('slno'=>'Sl no','v_slno'=>'Voucher Slno','mid'=>'Member ID','alloted_on'=>'Alloted on','activated_on'=>'Activated On');
			if($v_log_details->num_rows())
			{
				foreach($v_log_details->result_array() as $i=>$v_log_det)
				{
					$tbl_data[]=array('slno'=>$i+1,'v_slno'=>$v_log_det['voucher_serial_no'],'mid'=>$v_log_det['member_id'],'alloted_on'=>format_datetime($v_log_det['alloted_on']),'activated_on'=>format_datetime($v_log_det['activated_on']));
				}
			}
		}		
		elseif($type=='partially_redeemed_vouchers')
		{
			$activity_ttl="select count(*) as total from pnh_t_voucher_details  where is_activated=1 and `status`=5 $cond";
			$ttl_rows=$this->db->query($activity_ttl)->num_rows();
			$activity_res="SELECT a.*,c.book_slno,d.denomination FROM pnh_t_voucher_details a join pnh_t_book_voucher_link b on b.voucher_slno_id=a.id join pnh_t_book_details c on c.book_id=b.book_id join pnh_m_voucher d on d.voucher_id=a.voucher_id WHERE is_activated=1 AND `status`=5 $cond";
			$v_log_details=$this->db->query($activity_res);
			$tbl_head=array('slno'=>'Sl no','book_slno'=>"Book slno",'v_slno'=>'Voucher Slno',"value"=>"Value(Rs)","voucher_margin"=>"Voucher margin(%)","franchise_value"=>"Franchise value(Rs)",'mid'=>'Member ID','alloted_on'=>'Alloted on','activated_on'=>'Activated On');
			if($v_log_details->num_rows())
			{
				foreach($v_log_details->result_array() as $i=>$v_log_det)
				{
					$tbl_data[]=array('slno'=>$i+1,"book_slno"=>$v_log_det['book_slno'],'v_slno'=>$v_log_det['voucher_serial_no'],"value"=>$v_log_det['denomination'],"voucher_margin"=>$v_log_det['voucher_margin'],"franchise_value"=>$v_log_det['franchise_value'],'mid'=>$v_log_det['member_id'],'alloted_on'=>format_datetime($v_log_det['alloted_on']),'activated_on'=>format_datetime($v_log_det['activated_on']));
				}
			}
		}
		
		elseif($type=='inactivated_vouchers')
		{
			$activity_ttl="select count(*) as total from pnh_t_voucher_details  where is_activated=1 and `status`=2 $cond";
			$ttl_rows=$this->db->query($activity_ttl)->num_rows();
		//	$activity_res="SELECT a.*,c.book_slno,d.denomination  FROM pnh_t_voucher_details a join pnh_t_book_voucher_link b on b.voucher_slno_id=a.id join pnh_t_book_details c on c.book_id=b.book_id join pnh_m_voucher d on d.voucher_id=a.voucher_id  WHERE is_activated=0 AND `status`<=2 $cond";
			$activity_res="SELECT a.*,c.book_slno,d.denomination,f.menu_ids,m.name AS menu
							FROM pnh_t_voucher_details a 
							JOIN pnh_t_book_voucher_link b ON b.voucher_slno_id=a.id 
							JOIN pnh_t_book_details c ON c.book_id=b.book_id 
							JOIN pnh_m_voucher d ON d.voucher_id=a.voucher_id 
							JOIN pnh_t_book_allotment e ON e.book_id=c.book_id 
							JOIN pnh_m_book_template f ON f.book_template_id=c.book_template_id 
							JOIN pnh_menu m ON m.id=f.menu_ids
							WHERE is_activated=0 AND a.status<=2 and a.franchise_id=?";
			$v_log_details=$this->db->query($activity_res,$franchise_id);
			$tbl_head=array('slno'=>'Sl no','book_slno'=>"Book slno",'menu'=>"Menu",'v_slno'=>'Voucher Slno',"value"=>"Value(Rs)","voucher_margin"=>"Voucher margin(%)","franchise_value"=>"Franchise value(Rs)",'alloted_on'=>'Alloted on');
			if($v_log_details->num_rows())
			{
				foreach($v_log_details->result_array() as $i=>$v_log_det)
				{
					$tbl_data[]=array('slno'=>$i+1,"book_slno"=>$v_log_det['book_slno'],"menu"=>$v_log_det['menu'],'v_slno'=>$v_log_det['voucher_serial_no'],"value"=>$v_log_det['denomination'],"voucher_margin"=>$v_log_det['voucher_margin'],"franchise_value"=>$v_log_det['franchise_value'],'mid'=>$v_log_det['member_id'],'alloted_on'=>format_datetime($v_log_det['alloted_on']),'activated_on'=>format_datetime($v_log_det['activated_on']));
				}
			}
		}
		elseif($type=='book_orders')
		{
			$tbl_attr="width='100%'";
			$ttl_rows=$this->db->query("select count(*) as ttl from pnh_t_book_allotment where 1 $cond")->row()->ttl;
			$sql="select a.book_id,a.book_slno,a.book_value,b.status,c.book_type_name,b.activated_on,b.created_on as alloted_on,b.margin,c.menu_ids 
						from pnh_t_book_details a
						join pnh_t_book_allotment b on b.book_id=a.book_id 
						join pnh_m_book_template c on c.book_template_id=a.book_template_id
						where 1 and b.franchise_id=?";
			
			$book_orders_res=$this->db->query($sql,$franchise_id);
			$tbl_head=array("slno"=>"Sl no","book_name"=>"Book name","book_slno"=>"Book slno","book_menu"=>"Book menu","book_value"=>"Book value(Rs)","book_margin"=>"Book margin(%)","total_vouchers"=>"Total vouchers","activated"=>"Activated","redeemed"=>"Redeemed","alloted_on"=>"Alloted on","activated_on"=>"Activated on");
			if($book_orders_res->num_rows)
			{
				$book_orders_det=$book_orders_res->result_array();
				foreach($book_orders_det as $i=> $b)
				{
					$menu_list=array();
					$ttl_vouchers=$this->db->query("select count(*) as ttl from pnh_t_book_voucher_link where book_id=?",$b['book_id'])->row()->ttl;
					$ttl_activated=$this->db->query("select count(*) as ttl from pnh_t_book_voucher_link a join pnh_t_voucher_details b on b.id=a.voucher_slno_id where b.status > 2 and a.book_id=? group by a.book_id",$b['book_id'])->row()->ttl;
					$ttl_redeemed=$this->db->query("select count(*) as ttl from pnh_t_book_voucher_link a join pnh_t_voucher_details b on b.id=a.voucher_slno_id where (b.status=4 or b.status=5) and a.book_id=? group by a.book_id",$b['book_id'])->row()->ttl;
					$menu_det=$this->db->query("select id,name from pnh_menu where id in (".$b['menu_ids'].")")->result_array();
					foreach($menu_det as $mn)
						$menu_list[]=$mn['name'];
					
					$ttl_activated=$ttl_activated?$ttl_activated:0;
					$ttl_redeemed=$ttl_redeemed?$ttl_redeemed:0;
					
					$tbl_data[]=array('slno'=>$i+1,"book_name"=>$b['book_type_name'],"book_slno"=>$b['book_slno'],"book_menu"=>implode('<br>',$menu_list),'book_value'=>$b['book_value'],"book_margin"=>$b['margin'],"total_vouchers"=>$ttl_vouchers,'activated'=>$ttl_activated.'/'.$ttl_vouchers,'redeemed'=>$ttl_redeemed.'/'.$ttl_activated,'alloted_on'=>format_datetime($b['alloted_on']),'activated_on'=>$b['activated_on']);
				}
				
			}
		}
		if(count($tbl_data))
			{
				$tbl_data_html = '<table cellpadding="5" cellspacing="0" class="datagrid datagridsort" '.$tbl_attr.'>';
				$tbl_data_html .= '<thead>';
				foreach($tbl_head as $th)
					$tbl_data_html .= '<th>'.$th.'</th>';
				
				$tbl_data_html .= '</thead>';
				$i = $pg;
				$tbl_data_html .= '<tbody>';
				foreach($tbl_data as $tdata)
				{
					$tbl_data_html .= '<tr>';
					foreach(array_keys($tbl_head) as $th_i)
					{
						if($th_i == 'slno')
							$tdata[$th_i] = $i+1;
						
						$tbl_data_html .= '	<td>'.$tdata[$th_i].'</td>';
					}
					$tbl_data_html .= '</tr>';

					$i = $i+1;
				}
				$tbl_data_html .= '</tbody>';
				$tbl_data_html .= '</table>';
			}else
			{
				$tbl_data_html = '<div align="center"> No data found</div>';
			}
		
		
		$this->load->library('pagination');
		
		$config['base_url'] = site_url('admin/jx_getpnh_voucher_activitylog/'.$type.'/'.$fid.'/',$pg);
		$config['total_rows'] = $tbl_total_rows;
		$config['per_page'] = $limit;
		$config['uri_segment'] = 5;
			
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$pagi_links = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
			
		$pagi_links = '<div class="log_pagination">'.$pagi_links.'</div>';
			
		echo json_encode(array('log_data'=>$tbl_data_html,'pagi_links'=>$pagi_links,'type'=>$type,'fid'=>$fid,'pg'=>$pg));
	}
	function jx_getfrancreditnotes($pg)
	{
		$this->erpm->auth();
		
		$fid = $this->input->post('fid');
		
		
		$sql = "select count(*) as t from king_invoice a join king_transactions b on a.transid = b.transid where credit_note_amt != 0 and franchise_id = ? ";
		$total_fr_crlist = $this->db->query($sql,$fid)->row()->t;
		
		//echo $this->db->last_query();
		
		$sql = "select a.order_id,credit_note_amt,credit_note_id,invoice_no,a.createdon from king_invoice a join king_transactions b on a.transid = b.transid where credit_note_amt != 0 and franchise_id = ? order by a.id desc limit $pg,10 ";
		$output = array();
		$output['total'] = $total_fr_crlist;
		
		$fran_crnote_res = $this->db->query($sql,$fid);
		if($fran_crnote_res->num_rows())
		{
			$output['status'] = 'success';
			$output['fran_crnotelist'] = $fran_crnote_res->result_array();
			
			$this->load->library('pagination');

			$config['base_url'] = site_url('admin/jx_getfrancreditnotes');
			$config['total_rows'] = $total_fr_crlist;
			$config['per_page'] = 10;
			$config['uri_segment'] = 3;
			
			$this->config->set_item('enable_query_strings',false);
			$this->pagination->initialize($config);
			$output['fran_crnotelist_pagi'] = $this->pagination->create_links();
			$this->config->set_item('enable_query_strings',true);
			
		}else
		{
			$output['fran_crnotelist'] = array();
			$output['status'] = 'success';
		}
		
		
		echo json_encode($output);
		
		
	}	
	/**
	 * Member Scheme Start
	 * @param unknown_type $fid
	 */
	 function pnh_give_member_sch($fid)
	{
		$user=$this->erpm->auth();
		foreach(array("ime_schtype","mbrsch_credit","mbr_schmenu","mbr_schcat","mbr_schbrand","msch_start","msch_end","mbrsch_applyfrm") as $i)
			$$i=$this->input->post($i);
		$mbrsch_start=strtotime($msch_start);
		$mbrsch_end=strtotime($msch_end." 23:59:59");
		if($mbrsch_applyfrm)
			$mbrsch_applyfrm=strtotime($mbrsch_applyfrm.' 00:00:00');
		$mbrsch_expire=$this->input->post('expire_msch');
		if($mbrsch_expire)
		{
			$this->db->query("update imei_m_scheme set is_active=0, modified_by=?,modified_on=? where franchise_id=? and menuid=? and brandid=? and categoryid=? and unix_timestamp() between scheme_from and scheme_to ",array($user['userid'],time(),$fid,$mbr_schmenu,$mbr_schbrand,$mbr_schcat));
		}
		
		$inp=array("scheme_type"=>$ime_schtype,"franchise_id"=>$fid,"menuid"=>$mbr_schmenu,"categoryid"=>$mbr_schcat,"brandid"=>$mbr_schbrand,"credit_value"=>$mbrsch_credit,"scheme_from"=>$mbrsch_start,"scheme_to"=>$mbrsch_end,"sch_apply_from"=>$mbrsch_applyfrm,"created_by"=>$user['userid'],"created_on"=>time(),"is_active"=>1);
		$this->db->insert("imei_m_scheme",$inp);
		$sch_id = $this->db->insert_id();
		
		//apply imei scheme from apply from for franchise.
		$cond = '';
		if($mbr_schmenu)
			$cond .= ' and c.menuid = '.$mbr_schmenu;
		if($mbr_schcat)
			$cond .= ' and c.catid = '.$mbr_schcat;
		if($mbr_schbrand)
			$cond .= ' and c.brandid = '.$mbr_schbrand;
			
		if($mbrsch_applyfrm)
		{
			$orders = $this->db->query("select a.id,(i_orgprice-(i_discount+i_coup_discount)) as amt   
									from king_orders a
									join king_dealitems b on a.itemid = b.id 
									join king_deals c on c.dealid = b.dealid 
									join king_transactions d on d.transid = a.transid 
									where d.franchise_id = ? $cond and d.init between ? and ?  
										  and imei_scheme_id = 0 and a.status != 3 ",array($fid,$mbrsch_applyfrm,$mbrsch_end));
										
			if($orders->num_rows())
			{
				foreach($orders->result_array() as $order)
				{
					if($ime_schtype == 1)
						$imei_sch_amt = ($order['amt']*$mbrsch_credit/100);
					else 
						$imei_sch_amt = $mbrsch_credit;
							
					$this->db->query('update king_orders set imei_scheme_id = ?,imei_reimbursement_value_perunit=? where id = ? ',array($sch_id,$imei_sch_amt,$order['id']));
				}
			}
		}
		
		$this->erpm->flash_msg("IMEI/Serialno Scheme Activated");
		redirect("admin/pnh_franchise/$fid#sch_hist");
		
	} 
	
	function jx_check_mbrschmenu($fid='')
	{
		$output=array();
		$mbsch_menu=$this->input->post('mbr_schmenu');
		if($mbsch_menu!=112)
		{
			$output['status']='error';
			$output['message']='Member scheme Applied Only for Mobile & tablets';
		}
		else
		{
			$output['status']='success';
		}
		echo json_encode($output);
	}
	
	function pnh_expire_membrscheme($id)
	{
		$user=$this->auth(true);
		$this->db->query("update imei_m_scheme set is_active=0,modified_on=?,modified_by=? where id=? and is_active = 1 ",array(time(),$user['userid'],$id));
		$this->erpm->flash_msg("Member Scheme disabled");
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function jx_load_all_shipped_mobimei($pg=0)
	{
		$this->erpm->auth();
		
		$fid = $this->input->post('fid');
		$date_type = $this->input->post('date_type');
		$imei_active_ondate = $this->input->post('active_ondate');
		$imei_active_endate = $this->input->post('active_ondate_end');
		$imei_srch_kwd = $this->input->post('imei_srch_kwd');
		$imei_status = $this->input->post('imei_status');
		$imei_cre_type=array();
		$imei_cre_type[0]='Rs';
		$imei_cre_type[1]='%';
		
		$limit=50;
		$cond = ' ';
		if($fid)
			$cond.= 'and t.franchise_id='.$fid;
		if($imei_srch_kwd)
				$cond .= '  and ( inv.invoice_no = "'.$imei_srch_kwd.'" or imei_no = "'.$imei_srch_kwd.'") ';
		if($imei_active_ondate && $imei_active_endate )
		{
			if($date_type)
				$cond .= ' and ( date(from_unixtime(t.init)) >= date("'.$imei_active_ondate.'") and date(from_unixtime(t.init)) <= date("'.$imei_active_endate.'") )';
			else
				$cond .= ' and ( date(imei_activated_on) >= date("'.$imei_active_ondate.'") and date(imei_activated_on) <= date("'.$imei_active_endate.'") )';
		}
			

		if($imei_status>=1)
		{
			if($imei_status == 1 )
				$cond .= ' and i.is_imei_activated=0';
			else 
				$cond .= ' and i.is_imei_activated=1';
		}
			
		
		$output=array();
		$total_rows="SELECT count(distinct i.id) as ttl
						FROM t_imei_no i 
								Join king_orders o on o.id = i.order_id 
								JOIN king_transactions t ON t.transid=o.transid
								JOIN m_product_deal_link p ON p.itemid=o.itemid
								JOIN m_product_info l ON l.product_id=p.product_id
								JOIN king_invoice inv ON inv.order_id=o.id and inv.invoice_status = 1 
								JOIN imei_m_scheme r ON r.id=o.imei_scheme_id
								left join pnh_member_info b on b.pnh_member_id=i.activated_member_id 
								left join t_invoice_credit_notes tcr on tcr.invoice_no = inv.invoice_no 
								JOIN shipment_batch_process_invoice_link bi ON bi.invoice_no = inv.invoice_no 
						WHERE o.status in (1,2) and o.imei_scheme_id > 0 $cond 
						ORDER BY l.product_name ASC";
		$ttl_count=$this->db->query($total_rows)->row()->ttl;
		$output['total']=$ttl_count;
		
		$imei_summ_ttl_sql="SELECT i.is_imei_activated,count(distinct i.id) as ttl,sum(imei_reimbursement_value_perunit) as amt
							FROM t_imei_no i 
									Join king_orders o on o.id = i.order_id 
									JOIN king_transactions t ON t.transid=o.transid
									JOIN m_product_deal_link p ON p.itemid=o.itemid
									JOIN m_product_info l ON l.product_id=p.product_id
									JOIN king_invoice inv ON inv.order_id=o.id and inv.invoice_status = 1 
									JOIN imei_m_scheme r ON r.id=o.imei_scheme_id
									left join pnh_member_info b on b.pnh_member_id=i.activated_member_id 
									left join t_invoice_credit_notes tcr on tcr.invoice_no = inv.invoice_no 
									JOIN shipment_batch_process_invoice_link bi ON bi.invoice_no = inv.invoice_no 
							WHERE o.status in (1,2) and o.imei_scheme_id > 0 $cond 
							group by i.is_imei_activated 
							ORDER BY l.product_name ASC";
		$imei_summ_ttl_res=$this->db->query($imei_summ_ttl_sql);
		
		$output['total_actv_amt']=0;
		$output['total_actv_imei'] = 0; 
		$output['total_inactv_imei'] = 0;
		foreach($imei_summ_ttl_res->result_array() as $imei_ttl)
		{
			if($imei_ttl['is_imei_activated'])
			{
				$output['total_actv_amt'] = $imei_ttl['amt'];
				$output['total_actv_imei'] = $imei_ttl['ttl'];
			}else
			{
				$output['total_inactv_imei'] = $imei_ttl['ttl'];
			}
		}
		
		$output['total_actv_amt'] = format_price($output['total_actv_amt']/2);
		
		$shipped_imei_fr_res=$this->db->query("SELECT f.franchise_id,f.franchise_name,o.imei_reimbursement_value_perunit as imei_activation_credit,i.is_imei_activated,date_format(imei_activated_on,'%d/%m/%Y %h:%i %p') as imei_activated_on,imei_no,p.product_id,date_format(from_unixtime(o.time),'%d/%m/%Y') as orderd_on,l.product_name,o.quantity,o.imei_reimbursement_value_perunit,o.id,inv.invoice_no,t.paid,r.scheme_type,r.credit_value,b.pnh_member_id
								FROM t_imei_no i 
								Join king_orders o on o.id = i.order_id 
								JOIN king_transactions t ON t.transid=o.transid
								JOIN m_product_deal_link p ON p.itemid=o.itemid
								JOIN m_product_info l ON l.product_id=p.product_id
								JOIN king_invoice inv ON inv.order_id=o.id and inv.invoice_status = 1 
								JOIN imei_m_scheme r ON r.id=o.imei_scheme_id
								left join pnh_member_info b on b.pnh_member_id=i.activated_member_id 
								left join t_invoice_credit_notes tcr on tcr.invoice_no = inv.invoice_no 
								JOIN shipment_batch_process_invoice_link bi ON bi.invoice_no = inv.invoice_no 
								join pnh_m_franchise_info f on f.franchise_id = t.franchise_id 
								WHERE o.status in (1,2) and o.imei_scheme_id > 0 $cond
								GROUP BY i.id
								ORDER BY l.product_name ASC limit $pg,$limit");
		
		if($shipped_imei_fr_res->num_rows())
		{
			$output['status']='success';
			$output['ship_imei_det']=$shipped_imei_fr_res->result_array();
			$output['imei_cre_type']=$imei_cre_type;
			$this->load->library('pagination');
			
			$config['base_url'] = site_url('admin/jx_load_all_shipped_mobimei');
			$config['total_rows'] = $ttl_count;
			$config['per_page'] = $limit;
			$config['uri_segment'] = 3;
				
			$this->config->set_item('enable_query_strings',false);
			$this->pagination->initialize($config);
			$output['shipped_imeilist_pagi'] = $this->pagination->create_links();
			$this->config->set_item('enable_query_strings',true);
		}else 
		{
			$output['ship_imei_det']=array();
			$output['status']='success';
		}
			echo json_encode($output);
	}

	/**
	 * function to check member id / mobileno for imeino activation 
	 */
	function jx_checkmemberbyidmob()
	{
		$output = array();
		$srch = $this->input->post('srch');
		
		//check if srchno is mobile or member_id 
		$srch_type = 0;
		if(strlen($srch) == 10)
			$srch_type = 1;
		else if(strlen($srch) == 8)
			$srch_type = 2;
		else 
			$srch_type = 3;
		if($srch_type != 0)
		{
			
			//check if imeino allotement for 
			if($srch_type == 3)
			{
				$is_split_imei_allotment_res = $this->db->query("select a.imei_no,a.status as is_imei_alloted,a.is_imei_activated,imei_activated_on,c.franchise_id,member_id,b.userid,d.pnh_member_id,e.mobile,
																			concat(e.first_name,' ',e.last_name) as mem_name,
																		if(member_id,1,0) as is_allotbysplit
																		from t_imei_no a 
																		left join king_orders b on a.order_id = b.id and b.status < 3 
																		left join king_transactions c on c.transid = b.transid 
																		left join pnh_member_info d on b.userid = d.user_id 
																		left join pnh_member_info e on b.member_id = e.pnh_member_id
																	where imei_no = ? ;",$srch);
				if($is_split_imei_allotment_res->num_rows())
				{
					$is_split_imei_allotment_det = $is_split_imei_allotment_res->row_array();
					if($is_split_imei_allotment_det['is_imei_alloted'])
					{
						if($is_split_imei_allotment_det['is_allotbysplit'])
						{
							$output['imei_no']=$is_split_imei_allotment_det['imei_no'];
							$output['member_id']=$is_split_imei_allotment_det['member_id'];
							$output['mob_no']=$is_split_imei_allotment_det['mobile'];
							$output['franchise_id']=$is_split_imei_allotment_det['franchise_id'];
						}else
						{
							$output['member_id']=$is_split_imei_allotment_det['pnh_member_id'];
							$output['mem_name']=$is_split_imei_allotment_det['mem_name'];
							$output['mob_no']=$is_split_imei_allotment_det['mobile'];
							$output['franchise_id']=$is_split_imei_allotment_det['franchise_id'];
						}
					}else
					{
						$output['status'] = 'error';
						$output['error'] = 'IMEINO not alloted to any franchise';
					}
				}else
				{
					$output['status'] = 'error';
					$output['error'] = 'Invalid IMEINO Entered';
				}
			}else
			{
			
				if($srch_type == 1)
					$output['mob_no']=$srch;
				
				$mem_det_res = $this->db->query("select pnh_member_id as member_id,franchise_id,mobile from pnh_member_info where mobile = ? or pnh_member_id = ? ",array($srch,$srch));
				if($mem_det_res->num_rows())
				{
					$mem_det = $mem_det_res->row_array();
					$output['member_id']=$mem_det['member_id'];
					$output['franchise_id']=$mem_det['franchise_id'];
					if($srch_type == 2)
						$output['mob_no']=$mem_det['mobile'];
						
				}else
				{
					$output['member_id']=0;
					$output['mob_no']=($srch_type==1)?$srch:'';
					
					if($srch_type == 2)
					{
						$fid=$this->db->query("SELECT ifnull(sum(franchise_id),0) as franchise_id  FROM pnh_m_allotted_mid m WHERE ? between mid_start and mid_end ORDER BY id DESC LIMIT 1;",$srch)->row()->franchise_id;
						if($fid)
							$output['franchise_id']=$fid;
						else
						{
							$output['status'] = 'error';
							$output['error'] = 'Memberid not alloted to any franchise';
						}
					}
				}
			}
		}else
		{
			$output['status'] = 'error';
			$output['error'] = 'Please enter valid MemberID or Mobile no';
		}
		
		echo json_encode($output);
	}

	/**
	 * function for generate a employee activity log
	 */
	function pnh_employee_sms_activity_log()
	{
		$this->erpm->auth(true);
		$data['page']="pnh_employee_activity_log";
		$this->load->view("admin",$data);
	}	

	
	function jx_get_emp_sms_activity()
	{
		$this->erpm->auth(true);
		$output=array();
		
		$title_det=$month=$this->input->post('month');
		$emp_status=$this->input->post('emp_status');
		
		if($month)
		{
			$month_det=explode('/',$month);
			$m=$month_det[0];
			$y=$month_det[1];
			$month=$y.'-'.$m.'%';
		}else{
			$m=date('m');
			$y=date('Y');
			$month=$y.'-'.$m.'%';
			$title_det=$m.'/'.$y;
		}
		
		//default_month
		$ttl_days=cal_days_in_month(CAL_GREGORIAN,$m,$y);
		
		$day=array();
		for($i=1;$i<=$ttl_days;$i++)
		{
			if(strlen($i)==1)
				$d='0'.$i;
			else
				$d=$i;
			$day[$d]='';
		}
		
		$cond='';
		if($emp_status==1)
			$cond=" and is_suspended=0 ";
		else if($emp_status==2)
			$cond=" and is_suspended=1 ";
		
		$sql="select a.employee_id,a.name,a.contact_no,short_frm,is_suspended 
					from m_employee_info a 
					join m_employee_roles b on b.role_id=a.job_title  
				where job_title2 not in (1,2,3) $cond order by name";		
		
		$emp_list=$this->db->query($sql)->result_array();
		
		$sql="select employee_id,employee_name,role_id,short_frm,group_concat(distinct type) as types,sender,d 
				from (
							select employee_id,name as employee_name,role_id,short_frm,SUBSTRING_INDEX(msg,' ',1) as type,sender,date(from_unixtime(a.created_on)) as d
							from m_employee_info b
							left join pnh_sms_log a on find_in_set(sender*1,contact_no) and date(from_unixtime(a.created_on)) like ?
							join m_employee_roles c on c.role_id = b.job_title
						where franchise_id = 0
					) as g
				group by d,employee_id
				order by d,role_id,employee_name";
		
		$sms_activity_log=$this->db->query($sql,$month)->result_array();
		
		$emp_activity_link=array();
		
		foreach($sms_activity_log as $activity)
		{
			if(!isset($emp_activity_link[$activity['employee_id']]))
					$emp_activity_link[$activity['employee_id']]=$day;
			$d=date("d",strtotime($activity['d']));
			
			if(isset($emp_activity_link[$activity['employee_id']][$d]))
				$emp_activity_link[$activity['employee_id']][$d]=$activity;
		}
		
		$data['types']=array("paid"=>"p","new"=>"n","ship"=>"s","existing"=>"e","d"=>"d","m"=>"m","e"=>"e","r"=>"r");
		$data['employee_list']=$emp_list;
		$data['emp_sms_activity_log']=$emp_activity_link;
		$data['days']=$day;
		$data['month']=str_ireplace('%','',$month);
		$output['title_det']=$title_det;
		$output['page']=$this->load->view('admin/body/jx_pnh_emp_smp_activity_log',$data,true);
		echo json_encode($output);
	}
	
	function jx_updateorderbatchstatus()
	{
		$output = array();
		$user = $this->erpm->auth(PNH_EXECUTIVE_ROLE,true);
		
		$transid = $this->input->post('transid');
		$stat = $this->input->post('stat');
		if(!$user)
		{
			$output['status'] = 'error';
			$output['error'] = 'you are not authorised to update batch status';	
		}else
		{
			$this->db->query("update king_transactions set batch_enabled = ? where transid = ? and batch_enabled != ? limit 1 ",array($stat,$transid,$stat));
			
			if($this->db->affected_rows())
			{
				// update trsns log fro batch status update
				$trans_logprm=array();
				$trans_logprm['transid']=$transid;
				$trans_logprm['admin']=$user['userid'];
				$trans_logprm['time']=time();
				$trans_logprm['msg']='Batch '.($stat?'Enabled':'Disabled').' By '.$user['username'].' On '.format_datetime_ts(time());
				$this->db->insert("transactions_changelog",$trans_logprm);
			}
			$output['is_batch_enabled'] = $this->db->query('select batch_enabled from king_transactions where transid = ? ',$transid)->row()->batch_enabled;
			$output['status'] = 'success';
		}
		echo json_encode($output);
	}


	function jx_searchbykwd()
	{
		$kwd = $this->input->post('kwd');
		if(strlen($kwd) > 2)
		{
			echo '<p id="searchresults">';
			$fr_list_res = $this->db->query("select franchise_id,franchise_name from pnh_m_franchise_info where (franchise_name like '%".$kwd."%' or login_mobile1 = ? or login_mobile2 = ? ) order by franchise_name limit 6;",array($kwd,$kwd));
			if($fr_list_res->num_rows())
			{
				$i=0;
				echo '<span class="category">Franchise</span>';
				foreach($fr_list_res->result_array() as $fr)
				{
					if($i < 5)
						echo '<a href="'.site_url('admin/pnh_franchise/'.$fr['franchise_id']).'"><span class="searchheading">'.$fr['franchise_name'].'</span></a>';
					$i++;
				}
				if($i==6)
					echo '<a href="javascript:void(0)"><span class="searchheading viewall">View All</span></a>';
				
			}
			
			$sql = "select employee_id,name from m_employee_info where name like '%".$kwd."%' ";
			
			if(strlen($kwd) == 10)
				$sql .= " or contact_no like '%".$kwd."%' ";
			$sql .= " order by name limit 6; ";
			 
			$emp_list_res = $this->db->query($sql); 
			
			if($emp_list_res->num_rows())
			{
				$i=0;
				echo '<span class="category">PNH Employees</span>';
				foreach($emp_list_res->result_array() as $emp)
				{
					if($i < 5)
						echo '<a href="'.site_url('admin/view_employee/'.$emp['employee_id']).'"><span class="searchheading">'.$emp['name'].'</span></a>';
					$i++;
				}
				if($i==6)
					echo '<a href="javascript:void(0)"><span class="searchheading viewall">View All</span></a>';
				
			}
			
			
			$sql = "select distinct dispatch_id,a.p_invoice_no 
							from shipment_batch_process_invoice_link a 
						join proforma_invoices b on a.p_invoice_no = b.p_invoice_no 
						join king_invoice c on c.invoice_no = a.invoice_no  
						where dispatch_id = ? and dispatch_id != 0 
						group by dispatch_id 
					";
			 
			$item_list_res = $this->db->query($sql,$kwd); 
			
			if($item_list_res->num_rows())
			{
				echo '<span class="category">Dispatch ID</span>';
				foreach($item_list_res->result_array() as $item)
				{
					echo '<a href="'.site_url('admin/proforma_invoice/'.$item['p_invoice_no']).'"><span class="searchheading">'.$item['dispatch_id'].'</span></a>';
				}
			}
			
			$sql = "select b.franchise_id,franchise_name,b.transid,d.invoice_no 
									from t_imei_no i 
									join king_orders a on a.id = i.order_id
									join king_transactions b on a.transid = b.transid 
									join pnh_m_franchise_info c on c.franchise_id = b.franchise_id 
									join king_invoice d on d.order_id = i.order_id   
								where imei_no = ?
					";
			 
			$item_list_res = $this->db->query($sql,$kwd); 
			
			if($item_list_res->num_rows())
			{
				echo '<span class="category">Alloted to Franchise </span>';
				foreach($item_list_res->result_array() as $item)
				{
					echo '<a target="_blank" href="'.site_url('admin/pnh_franchise/'.$item['franchise_id'].'#shipped_imeimobslno').'">
							<span class="searchheading">'.$item['franchise_name'].'</span>
						 </a>';
					echo '<a target="_blank" href="'.site_url('admin/invoice/'.$item['invoice_no']).'">
							<span class="searchheading">'.$item['invoice_no'].'</span>
						 </a>';	 
					echo '<a target="_blank" href="'.site_url('admin/trans/'.$item['transid']).'">
							<span class="searchheading">'.$item['transid'].'</span>
						 </a>';	 
				}
			}
			$item_list_res = $this->db->query("select a.product_id,product_name from m_product_info a join t_imei_no b on a.product_id = b.product_id where imei_no = '".$kwd."' limit 6 ");
			if($item_list_res->num_rows())
			{
				$i=0;
				echo '<span class="category">Products</span>';
				foreach($item_list_res->result_array() as $itm)
				{
					if($i < 5)
						echo '<a href="'.site_url('admin/product/'.$itm['product_id']).'"><span class="searchheading">'.$itm['product_name'].'</span></a>';
					$i++;
				}
			}
			
			$mem_list_res = $this->db->query("select id,pnh_member_id,concat(first_name,' ',last_name) as name 
													from pnh_member_info where ( concat(first_name,' ',last_name) like '%".$kwd."%' or pnh_member_id = ? ) limit 6 ",$kwd);
			if($mem_list_res->num_rows())
			{
				$i=0;
				echo '<span class="category">Members</span>';
				foreach($mem_list_res->result_array() as $mem)
				{
					if($i < 5)
						echo '<a href="'.site_url('admin/pnh_viewmember/'.$mem['id']).'"><span class="searchheading">'.$mem['name'].' ('.$mem['pnh_member_id'].') </span></a>';
					$i++;
				}
				if($i==6)
					echo '<a href="javascript:void(0)"><span class="searchheading viewall">View All</span></a>';
			}
			
			
			$item_list_res = $this->db->query("select dealid,id,name,is_pnh from king_dealitems where ((name like '%".$kwd."%' or pnh_id = ? ) and pnh_id > 0 ) limit 6 ", $kwd);
			if($item_list_res->num_rows())
			{
				$i=0;
				echo '<span class="category">PNH Deals</span>';
				foreach($item_list_res->result_array() as $itm)
				{
					if($i < 5)
						echo '<a href="'.site_url('admin/pnh_deal/'.$itm['id']).'"><span class="searchheading">'.$itm['name'].'</span></a>';
					$i++;
				}
				if($i==6)
					echo '<a href="javascript:void(0)"><span class="searchheading viewall">View All</span></a>';
			}
			
			$item_list_res = $this->db->query("select dealid,id,name,is_pnh from king_dealitems where name like '%".$kwd."%'  and pnh_id = 0  limit 6 ", $kwd);
			if($item_list_res->num_rows())
			{
				$i=0;
				echo '<span class="category">SIT Deals</span>';
				foreach($item_list_res->result_array() as $itm)
				{
					if($i < 5)
						echo '<a href="'.site_url('admin/deal/'.$itm['dealid']).'"><span class="searchheading">'.$itm['name'].'</span></a>';
					$i++;
				}
				if($i==6)
					echo '<a href="javascript:void(0)"><span class="searchheading viewall">View All</span></a>';
			}
			
			$item_list_res = $this->db->query("select product_id,product_name from m_product_info where product_name like '%".$kwd."%' limit 6 ");
			if($item_list_res->num_rows())
			{
				$i=0;
				echo '<span class="category">Products</span>';
				foreach($item_list_res->result_array() as $itm)
				{
					if($i < 5)
						echo '<a href="'.site_url('admin/product/'.$itm['product_id']).'"><span class="searchheading">'.$itm['product_name'].'</span></a>';
					$i++;
				}
				if($i==6)
					echo '<a href="javascript:void(0)"><span class="searchheading viewall">View All</span></a>';
			}
			if(strlen($kwd) > 4)
			{
				$item_list_res = $this->db->query("select transid from king_transactions where transid like '".$kwd."%' or transid like '%".$kwd."'  limit 6 ");
				if($item_list_res->num_rows())
				{
					$i=0;
					echo '<span class="category">Products</span>';
					foreach($item_list_res->result_array() as $itm)
					{
						if($i < 5)
							echo '<a href="'.site_url('admin/trans/'.$itm['transid']).'"><span class="searchheading">'.$itm['transid'].'</span></a>';
						$i++;
					}
					if($i==6)
						echo '<a href="javascript:void(0)"><span class="searchheading viewall">View All</span></a>';
				}
			}

			if(1)
			{
				$item_list_res = $this->db->query("select transid,invoice_no from king_invoice where invoice_no = ? group by invoice_no limit 6 ",$kwd);
				if($item_list_res->num_rows())
				{
					$i=0;
					echo '<span class="category">Invoices</span>';
					foreach($item_list_res->result_array() as $itm)
					{
						if($i < 5)
							echo '<a href="'.site_url('admin/trans/'.$itm['transid']).'">
									<span class="searchheading">'.$itm['invoice_no'].'</span>
								</a>';
						$i++;
					}
					if($i==6)
						echo '<a href="javascript:void(0)"><span class="searchheading viewall">View All</span></a>';
				}
			}
			
			echo '</p>';
		}
	}

	function jx_getordersbydeal($itemid='',$pg=0)
	{
		$this->erpm->auth();
		
		$cond = '';
		
		
		$sql_ttl = "select count(*) as t  
						from king_orders o 
						join king_transactions t on t.transid=o.transid 
						join pnh_m_franchise_info f on f.franchise_id=t.franchise_id 
						where o.itemid=? 
					";
		
		$ttl  = $this->db->query($sql_ttl,$itemid)->row()->t;
		
		
		
		
		$sql = "select o.time,f.franchise_name,
						f.franchise_id,o.transid,t.amount,invoice_status,s.shipped,o.status 
						from king_orders o 
						join king_transactions t on t.transid=o.transid 
						join pnh_m_franchise_info f on f.franchise_id=t.franchise_id 
						left join king_invoice i on i.order_id = o.id
						left join shipment_batch_process_invoice_link s on s.invoice_no = i.invoice_no 
						where o.itemid=? 
					order by o.time desc 
					limit $pg,10";
		
		$deal_list_res = $this->db->query($sql,$itemid);
		if($deal_list_res->num_rows())
		{
			$order_status_flags = array();
			$order_status_flags[0] = 'Pending';
			$order_status_flags[1] = 'Batched';
			$order_status_flags[2] = 'Shipped';
			$order_status_flags[3] = 'Cancelled';
			$order_status_flags[4] = 'Returned';
			$order_status_flags[5] = 'Invoiced';
			
			echo '<div align="left">Total : '.$ttl.' Orders </div>';
			echo '<table class="datagrid smallheader noprint">
						<thead><tr><th>Transid</th><th>Franchise</th><th>Invoiced</th><th>Status</th><th>Amount</th><th>Date</th></tr></thead>
						<tbody>';
			foreach($deal_list_res->result_array() as $row)
			{
				
				if($row['status'] == 1)
				{
					if($row['shipped'])
						$row['status'] = 2;
					else if($row['invoice_status'])
						$row['status'] = 5;
				}
					
	
				
				echo '<tR>
						<td><a target="_blank" href="'.site_url("admin/trans/{$row['transid']}").'">'.$row['transid'].'</a></td>
						<td><a target="_blank" href="'.site_url("admin/pnh_franchise/{$row['franchise_id']}").'">'.$row['franchise_name'].'</a></td>
						<td>'.($row['invoice_status']?'Yes':'No').'</td>
						<td>'.$order_status_flags[$row['status']].'</td>
						<td>'.$row['amount'].'</td>
						<td>'.format_datetime_ts($row['time']).'</td>
						</tR>
					';
			}
			echo '</tbody>
				</table>
				';
			echo '<div align="left" class="pagination">'.$this->_prepare_pagination(site_url('admin/jx_getordersbydeal/'.$itemid), $ttl, 10,4).'</div>';	
		}else
		{
			echo "<b>No Orders found</b>";
		}
		
	}
	
	function jx_suggestdealsbykwd()
	{
		$kwd = $this->input->post('kwd');
		$data = array();
		$res = $this->db->query("select a.dealid,b.name from king_deals a join king_dealitems b on a.dealid = b.dealid where b.name like ? order by b.name limit 20","$kwd%");
		foreach($res->result_array() as $row)
		{
			$data[] = array('id'=>$row['dealid'],'label'=>$row['name'],'value'=>$row['dealid']);
		}
		echo json_encode($data);
	}

	

	/**
	 * function to register new dynamic member    
	 */
	function jx_reg_newmem()
	{
		$admin=$this->erpm->auth();
		$output=array();
		$fid=$this->input->post('franchise_id');
		$mem_name=$this->input->post('memreg_name');
		$mem_mobno=$this->input->post('memreg_mobno');
		if($this->db->query("select * from pnh_member_info where mobile=?",$mem_mobno)->num_rows()==0)
		{
			$membr_id=$this->erpm->_gen_uniquememberid();
			$this->db->query("insert into king_users(name,is_pnh,createdon) values(?,1,?)",array("PNH Member: $membr_id",time()));
			$userid=$this->db->insert_id();
			$this->db->query("insert into pnh_member_info(pnh_member_id,user_id,first_name,last_name,mobile,franchise_id,created_by,created_on)values(?,?,?,?,?,?,?,?)",array($membr_id,$userid,$mem_name,'',$mem_mobno,$fid,$admin['userid'],time()));
			$output['status'] = 'success';
			$output['mid'] = $membr_id;
		}else
		{
			$output['status'] = 'error';
			$output['error'] = 'Mobile no already registered';
		}	
		
		echo json_encode($output);
	}
	
	function pnh_bulk_offeradd()
	{
		$admin=$this->erpm->auth(true);
		//pnh_m_offers
		$offer_menuid=$this->input->post('menu');
		$offer_brandid=$this->input->post('brand');
		$offer_catid=$this->input->post('cat');
		$offer_fids=$this->input->post('fids');
		$offer_txt=$this->input->post('offer_txt');
		$offer_start=$this->input->post('offer_start');
		$offer_end=$this->input->post('offer_end');
		$immediate_payment=$this->input->post('immediate_payment');
		
		if($_POST)
		{
			if(empty($offer_fids))
				show_error("No Franchises selected");
			
			$offer_start=strtotime($offer_start.' 00:00:00');
			$offer_end=strtotime($offer_end." 23:59:59");
			
			foreach($offer_fids as $fid)
			{
				$inp_param=array();
				$inp_param['franchise_id']=$fid;
				$inp_param['menu_id']=$offer_menuid;
				$inp_param['brand_id']=$offer_brandid;
				$inp_param['cat_id']=$offer_catid;
				$inp_param['offer_text']=$offer_txt;
				$inp_param['immediate_payment']=$immediate_payment;
				$inp_param['offer_start']=$offer_start;
				$inp_param['offer_end']=$offer_end;
				$inp_param['created_by']=$admin['userid'];
				$inp_param['created_on']=time();
				$this->db->insert('pnh_m_offers',$inp_param);
				
				$offer_id = $this->db->insert_id();
				
				// update franchise orders for
				$cond = '';
				if($offer_menuid)
					$cond .= ' and c.menuid = '.$offer_menuid;
				if($offer_brandid)
					$cond .= ' and c.brand_id = '.$offer_brandid;
				if($offer_menuid)
					$cond .= ' and c.catid = '.$offer_catid;	
				
				 
				$sql = "	select d.franchise_id,a.id,menuid,c.catid,c.brandid 
								from king_orders a
								join king_dealitems b on a.itemid = b.id 
								join king_deals c on c.dealid = b.dealid 
								join king_transactions d on d.transid = a.transid
								where date(from_unixtime(d.init)) between date(from_unixtime(?)) and date(from_unixtime(?)) 
								and d.is_pnh = 1 and d.franchise_id = ?  
						";
				$resp = $this->db->query($sql,array($offer_start,$offer_end,$fid));
				if($resp->num_rows())
				{
					foreach($resp->result_array() as $row)
					{
						$this->db->query("update king_orders set offer_refid = ?,has_offer=1 where has_offer=0 and offer_refid = 0 and id = ? and status < 2 limit 1 ",array($offer_id,$row['id']));
					}
				}
			}
			
			$this->erpm->flash_msg("Offer Added");
			redirect('admin/pnh_bulk_offeradd');	
		}
		$data['page']='pnh_bulk_offeradd';
		$this->load->view("admin",$data);
	}
	
	function offer_log($franchise_id='',$menuid='',$from='',$to='')
	{
		$cond='';
		if($franchise_id)
			$cond ='o.franchise_id='.$franchise_id;
		if($menuid)
			$cond .=' and o.menu_id='.$menuid;
		
		$offer_details=$this->db->query("SELECT *,f.franchise_name,m.name AS menu,b.name AS brand,c.name AS cat
											FROM pnh_m_offers o
											JOIN pnh_m_franchise_info f ON f.franchise_id=o.franchise_id
											JOIN pnh_menu m ON m.id=o.menu_id
											LEFT JOIN king_brands b ON b.id=o.brand_id
											LEFT JOIN king_categories c ON c.id=o.cat_id
											WHERE 1 
											");
		$data['offer_details']=$offer_details;
		$data['page']='pnh_offers_log';
		$this->load->view('admin',$data);
		
	}
	
	function pnh_activation($type='mem_reg')
	{
		$user=$this->erpm->auth(CALLCENTER_ROLE);
		$data['fran_list'] = $this->db->query("select franchise_id,franchise_name from pnh_m_franchise_info where is_suspended = 0 order by franchise_name ");
		$data['activation_list']=$this->erpm->pnh_getactivationlistbytype($type,$pg=0);
		$prepaid_menu=$this->db->query("SELECT GROUP_CONCAT(menu_id)AS menu_id FROM pnh_prepaid_menu_config WHERE is_active=1")->row()->menu_id;
		$data['prepaid_franlist']=$this->db->query("SELECT franchise_id,franchise_name
													FROM pnh_m_franchise_info f
													JOIN pnh_franchise_menu_link m ON m.fid=f.franchise_id
													JOIN pnh_prepaid_menu_config c ON c.menu_id=m.menuid WHERE c.is_active=1 AND m.status=1
													GROUP BY franchise_id order by franchise_name asc");

		$data['type'] = $type;
		$data['page']='activation_form';
		$this->load->view('admin',$data);
	}
	
	/**
	 * function to expire offers
	 * @param unknown_type $id
	 */
	function pnh_expire_offers($id)
	{
		$user=$this->auth(true);
		$this->db->query("update pnh_m_offers set is_active=0,modified_on=?,modified_by=? where id=? and is_active = 1 ",array(time(),$user['userid'],$id));
		$this->erpm->flash_msg("Offer  disabled");
		redirect($_SERVER['HTTP_REFERER']);
	}
	/**
	 * function to enable offers
	 * @param unknown_type $id
	 */
	function pnh_enable_offers($id)
	{
		$user=$this->auth(true);
		$this->db->query("update pnh_m_offers set is_active=1,modified_on=?,modified_by=? where id=? and is_active = 0 ",array(time(),$user['userid'],$id));
		$this->erpm->flash_msg("Offer  enabled");
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function jx_get_members_by_franchise($pagination=1,$fid=0,$pg=0)
	{
		$this->erpm->auth();
		if(!$fid)
			$fid=$this->input->post("fid");
	
		$output=array();
		$limit=10;
	
		$sql="select * from pnh_member_info where franchise_id=? ";
		$ttl_res=$this->db->query($sql,$fid);
		if($pagination)
			$sql.=" limit $pg , $limit";
	
		$mem_res=$this->db->query($sql,$fid);
	
		if($mem_res->num_rows())
		{
			$output['status']='success';
			if($pagination)
			{
				//pagination
				$this->load->library('pagination');
				$config['base_url'] = site_url("admin/jx_get_members_by_franchise/$pagination/$fid");
				$config['total_rows'] = $ttl_res->num_rows;
				$config['per_page'] = $limit;
				$config['uri_segment'] = 5;
				$this->config->set_item('enable_query_strings',false);
				$this->pagination->initialize($config);
				$output['pagination'] = $this->pagination->create_links();
				$this->config->set_item('enable_query_strings',true);
				//pagination end*/
			}
			$output['members']=$mem_res->result_array();
		}else{
			$output['members']='error';
			$output['msg']="No members found for this franchise";
		}
	
		echo json_encode($output);
	
	}
	
	/**
	 * function for manage the franchise receipts
	 */
	function jx_pnh_franchise_reports($fid,$type,$limit=10,$pg=0)
	{
		$this->erpm->auth();
		$output=array();
		$data['type']=$type;
		$output['type']=$type;
		
		$total_records=0;
	
		if($type=='pending')
		{
			$sql="SELECT r.*,m.name AS modifiedby,f.franchise_name,a.name AS admin
						FROM pnh_t_receipt_info r
						JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
						LEFT OUTER JOIN king_admin a ON a.id=r.created_by
						LEFT OUTER JOIN king_admin m ON m.id=r.modified_by
						WHERE r.status=0 AND r.is_active=1 and is_submitted=0 and r.status=0 and r.franchise_id=?
						ORDER BY instrument_date asc";
	
			$total_records=$this->db->query($sql,$fid)->num_rows;
	
			$sql.=" limit $pg , $limit ";
	
			$data['pending_receipts']=$this->db->query($sql,$fid)->result_array();
			$data['pending_ttlvalue']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by WHERE r.status=0 AND r.is_active=1  AND is_submitted=0 and r.status=0 and r.franchise_id=?  ORDER BY instrument_date asc",$fid)->row_array();
	
	
		}else if($type=='processed')
		{
			$sql="SELECT r.*,b.bank_name AS submit_bankname,s.name AS submittedby,a.name AS admin,f.franchise_name,d.remarks AS submittedremarks,DATE(d.submitted_on) AS submitted_on
						FROM pnh_t_receipt_info r
						LEFT JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id
						LEFT JOIN `pnh_m_bank_info` b ON b.id=d.bank_id
						LEFT JOIN king_admin s ON s.id=d.submitted_by
						JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
						left outer join king_admin a on a.id=r.created_by
						WHERE r.is_submitted=1 AND r.status=0  and r.is_active=1 and r.franchise_id=?
						group by r.receipt_id
						order by d.submitted_on desc";
	
			$total_records=$this->db->query($sql,$fid)->num_rows;
	
			$sql.=" limit $pg , $limit ";
	
			$data['processed_receipts']=$this->db->query($sql,$fid)->result_array();
			$data['processed_ttlvalue']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r LEFT JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id LEFT JOIN `pnh_m_bank_info` b ON b.id=d.bank_id LEFT JOIN king_admin s ON s.id=d.submitted_by JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id left outer join king_admin a on a.id=r.created_by WHERE  r.is_submitted=1 AND r.status=0  and r.is_active=1 and r.franchise_id=?  order by d.submitted_on desc",$fid)->row_array();
	
		}else if($type=='realized')
		{
			$sql="SELECT r.*,f.franchise_name,a.name AS admin,d.username AS activated_by
						FROM pnh_t_receipt_info r
						JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
						LEFT OUTER JOIN king_admin a ON a.id=r.created_by
						LEFT OUTER JOIN king_admin d ON d.id=r.activated_by
						WHERE r.status=1 AND r.is_active=1 AND (is_submitted=1 or r.activated_on!=0) and r.is_active=1 and r.franchise_id=?
						group by r.receipt_id
						ORDER BY activated_on desc";
	
			$total_records=$this->db->query($sql,$fid)->num_rows;
	
			$sql.=" limit $pg , $limit ";
	
			$data['realized_receipts']=$this->db->query($sql,$fid)->result_array();
			$data['realized_ttlvalue']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by WHERE r.status=1 AND r.is_active=1 AND (is_submitted=1 or r.activated_on!=0) and r.is_active=1 and r.franchise_id=? ORDER BY activated_on desc",$fid)->row_array();
	
		}else if($type=='cancelled')
		{
			$sql="SELECT r.*,f.franchise_name,a.name AS admin,d.username AS activated_by ,c.cancel_reason,c.cancelled_on
							FROM pnh_t_receipt_info r
							JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
							left JOIN `pnh_m_deposited_receipts`c ON c.receipt_id=r.receipt_id
							LEFT OUTER JOIN king_admin a ON a.id=r.created_by
							LEFT OUTER JOIN king_admin d ON d.id=r.activated_by
							WHERE r.status in (2,3) AND r.is_active=1 AND r.is_active=1 AND  r.franchise_id=?
							group by r.receipt_id
							ORDER BY cancelled_on DESC";
	
			$total_records=$this->db->query($sql,$fid)->num_rows;
	
			$sql.=" limit $pg , $limit ";
	
			$data['cancelled_receipts']=$this->db->query($sql,$fid)->result_array();
			$data['cancelled_ttlvalue']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id left JOIN `pnh_m_deposited_receipts`c ON c.receipt_id=r.receipt_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by WHERE r.status in (2,3) AND r.is_active=1 AND r.is_active=1 AND  r.franchise_id=? ORDER BY cancelled_on DESC",$fid)->row_array();
	
		}else if($type=='acct_stat')
		{
			$sql="SELECT debit_amt,credit_amt,remarks,status,created_on
						FROM `pnh_franchise_account_summary` a
						WHERE a.franchise_id=? and (a.action_type = 5 or a.action_type = 6)
						order by created_on desc";
	
			$total_records=$this->db->query($sql,$fid)->num_rows;
	
			$sql.=" limit $pg , $limit ";
	
			$data['account_stat']=$this->db->query($sql,$fid)->result_array();
	
		}else if($type=="actions")
		{
			$sql="select c.*,a.name as admin
					from pnh_t_credit_info c
					join king_admin a on a.id=c.credit_given_by
					where franchise_id=? order by id desc";
	
			$total_records=$this->db->query($sql,$fid)->num_rows;
	
			$sql.=" limit $pg , $limit ";
	
			$data['credit_log']=$this->db->query($sql,$fid)->result_array();
		}
	
		$data['total_records']=$total_records;
		$data['pagination']=$this->_prepare_pagination("admin/jx_pnh_franchise_reports/$fid/$type/$limit",$total_records,$limit,6);
		$output['page']=$this->load->view('admin/body/jx_pnh_franchise_receipts',$data,true);
	
		echo json_encode($output);
	}
	
	/**
	 * function to get imei details 
	 */
	function jx_getimeidet()
	{
		$this->erpm->auth();
		
		$imeino = $this->input->post('imeino');
		
		$output = array();
		
		if(!$imeino)
		{
			$output['error'] = 'Please enter Valid IMEIno';
		}else
		{	
			// check if imei is available
			$imei_det_res = $this->db->query("select * from t_imei_no where imei_no = ? ",$imeino);
			if(!$imei_det_res->num_rows())
			{
				$output['error'] = 'Invalid IMEINO Entered or IMEINO not sold to any franchise';
			}else
			{
				
				// update imei_scheme details for alloted imei if not set from config 
				$imei_sch_res = $this->db->query("select imei_schid,imei_no,imei_order_id,dmenuid,smenuid,dcatid,scatid,dbrandid,sbrandid,if(scheme_type,landprice*credit_value/100,credit_value) as cr_amt from (
														select g.id as imei_schid,b.id as imei_order_id,imei_no,d.menuid as dmenuid,d.catid as dcatid,d.brandid as dbrandid,
															g.menuid as smenuid,g.categoryid as scatid,g.brandid as sbrandid,scheme_type,credit_value,(b.i_orgprice-(b.i_discount+b.i_coup_discount)) as landprice
															from t_imei_no a
															join king_orders b on a.order_id = b.id
															join king_dealitems c on c.id = b.itemid 
															join king_deals d on d.dealid = c.dealid 
															join king_transactions e on e.transid = b.transid 
															join pnh_m_franchise_info f on f.franchise_id = e.franchise_id 
															join imei_m_scheme g on g.franchise_id = e.franchise_id and g.menuid = d.menuid 
															and d.catid = if(g.categoryid,g.categoryid,d.catid) 
															and d.brandid = if(g.brandid,g.brandid,d.brandid)
															and b.time between scheme_from and scheme_to and g.is_active = 1 
															where b.imei_scheme_id = 0 and imei_no = ? and b.time > unix_timestamp('2013-10-10')
															and b.status in (1,2) 
														group by a.id ) as g
												;",$imeino);
				
				if($imei_sch_res->num_rows())
				{
					$imei_sch_det = $imei_sch_res->row_array();
					
					$this->db->query("update king_orders set imei_scheme_id = ?,imei_reimbursement_value_perunit=? where id = ? limit 1 ",array($imei_sch_det['imei_schid'],$imei_sch_det['cr_amt'],$imei_sch_det['imei_order_id']));
					
				}			
				
				// get imei details 
				$sql = "select b.userid,f.invoice_no,product_name,b.transid,c.franchise_id,franchise_name,e.name,a.imei_no,b.status,imei_scheme_id,imei_reimbursement_value_perunit,member_id,date(from_unixtime(c.init)) as ordered_on,a.is_imei_activated
								from t_imei_no a 
								join king_orders b on a.order_id = b.id  
								join king_transactions c on b.transid = c.transid 
								join pnh_m_franchise_info d on d.franchise_id = c.franchise_id 
								join king_dealitems e on e.id = b.itemid 
								join king_invoice f on f.order_id = a.order_id 
								join m_product_info g on g.product_id = a.product_id  
								where a.imei_no = ? ";
								
				$res = $this->db->query($sql,$imeino);
				if($res->num_rows())
				{
					$output['det'] = $res->row_array();
					if($output['det']['member_id'] == 0)
					{
						$output['det']['member_id'] = $this->db->query("select pnh_member_id,mobile from pnh_member_info where user_id = ? ",$output['det']['userid'])->row()->pnh_member_id;
					}
				}else
				{
					$output['error'] = 'IMEINO Details not found.';
				}			 
			}
		}
		
		echo json_encode($output);
		
	}
	
	function jx_validate_mobno_imei()
	{
		$mobno = $this->input->post('mobno');
		$mid = $this->input->post('mid');
		$fid = $this->input->post('fid');
		$output = array();
		
		// check if mobno is valid 
		if(strlen($mobno) != 10)
		{
			$output['error'] = 'Invalid Mobno entered';
		}else
		{
			// check if mobno is already registered.	
			$mres = $this->db->query("select pnh_member_id from pnh_member_info where mobile = ? ",$mobno);
			if($mres->num_rows())
			{
				$mdet = $mres->row_array();
				$output['member_id'] = $mdet['pnh_member_id'];
			}else
			{
				$output['member_id'] = $mid;
			}
			
			
			$process_total_imei = MAX_MEMBER_IMEI_ACTIVATIONS;
			// get no of days passed from last activation 
			$last_actv_res = $this->db->query("select datediff(curdate(),b.imei_activated_on) as d
													from t_imei_no b  
													join pnh_member_info c on c.pnh_member_id = b.activated_member_id  
													where c.mobile = ? and is_imei_activated = 1 
												order by imei_activated_on desc  
												limit ".MAX_MEMBER_IMEI_ACTIVATIONS,$mobno);
			if($last_actv_res->num_rows())
			{
				foreach($last_actv_res->result_array() as $last_actv)
				{
					if($last_actv['d'] < BLOCK_MEMBER_IMEI_ACTIVATION_DAYS)
					{
						$process_total_imei--;
					}
				}
			}
			
			$output['pen_ttl_actv'] = $process_total_imei;
			
		}
		
		echo json_encode($output);
	}

	/**
	 * function for config the max order for deal
	 */
	function pnh_config_deal_max_orders($pg=0)
	{
		$this->erpm->auth(true,true);
		$limit=10;
		$data['menu_list']=$this->db->query("select id,name from pnh_menu where status=1 order by name")->result_array();
		
		$sql="select a.*,b.username,c.name as brand,d.name as cat,e.name as menu from pnh_max_deal_orders_cnf_log a 
						left join king_admin b on b.id=a.created_by 
						left join king_brands c on c.id=a.brand_id 
						left join king_categories d on d.id=a.cat_id
						left join pnh_menu e on e.id=a.menuid
						order by a.id desc ";
		$total_records=$this->db->query($sql)->num_rows();
		
		$sql.=" limit $pg , $limit ";
		
		$data['config_upt_log']=$this->db->query($sql)->result_array();
		
		$data['pagination']=$this->_prepare_pagination(site_url("admin/pnh_config_deal_max_orders"),$total_records,$limit,2);
		$data['page']="pnh_config_max_order_deal";
		$this->load->view('admin',$data);
	}
	
	function pnh_process_config_deal_max_order()
	{
		$user=$this->erpm->auth(true,true);
		$brand_id=$this->input->post('brand');
		$cat=$this->input->post('category');
		$menu=$this->input->post('menu');
		$qty=$this->input->post('qty');
		$param=array();
		$cond='';
		if($menu && $qty)
		{
			$param[]=$qty;
			
			if($menu)
			{
				$cond.=" and b.menuid=? ";
				$param[]=$menu;
			}
			
			if($brand_id)
			{
				$cond.=" and b.brandid=? ";
				$param[]=$brand_id;
			}
			
			if($cat)
			{
				$cond.=" and b.catid=? ";
				$param[]=$cat;
			}
			
			if($cond)
			{
				$sql="update king_dealitems a  join king_deals b on a.dealid=b.dealid set max_allowed_qty=? where 1 $cond ";
				$this->db->query($sql,$param);
				
				if($this->db->affected_rows())
				{
					$ins=array();
					$ins['menuid']=$menu;
					$ins['cat_id']=$cat;
					$ins['brand_id']=$brand_id;
					$ins['qty']=$qty;
					$ins['created_by']=$user['userid'];
					$ins['created_on']=cur_datetime();
					$this->db->insert("pnh_max_deal_orders_cnf_log",$ins);
					$this->session->set_flashdata("erp_pop_info","Maximum order allowed for deal config updated ");
				}
			}
		}else{
			$this->session->set_flashdata("erp_pop_info","Maximum order allowed for deal not config updated ");
		}
		redirect('admin/pnh_config_deal_max_orders');
	}
	
	function jx_load_allbrandsbymenucat($menuid=0,$catid=0)
	{
		$output=array();
		$cond='';
	
		if($menuid)
			$cond.=' and menuid='.$menuid;
		if($catid )
			$cond.=' and catid='.$catid;
	
		$catlist_res=@$this->db->query("SELECT DISTINCT brandid,b.name as brandname
				FROM king_deals a
				JOIN  king_categories c ON c.id=a.catid
				JOIN king_brands b ON b.id=a.brandid
				WHERE menuid2=0 $cond
				group by brandid
				order by brandname
		");
		
		if($catlist_res->num_rows())
		{
			$output['cat_list']=$catlist_res->result_array();
			$output['status']='success';
		}
		else
		{
			$output['status']="error";
			$output['message']="No category found for menu";
	
	}
		echo json_encode($output);
	}
	
	function to_load_orderdprd_details()
	{
		$output=array();
		$pid=$this->input->post('pid');
		
		$pid_orderdet=$this->db->query("SELECT l.product_id,a.transid,is_pnh,partner_id,c.name AS partner_name,IFNULL(SUM(b.quantity*l.qty),0)  AS total,DATE_FORMAT(FROM_UNIXTIME(`time`),'%d/%m/%Y') AS orderd_on
											FROM king_transactions a 
											JOIN king_orders b ON a.transid = b.transid 
											LEFT JOIN partner_info c ON c.id = a.partner_id 
											JOIN m_product_deal_link l ON l.itemid=b.itemid 
											 WHERE l.product_id=?  AND b.status = 0 
											GROUP BY is_pnh,partner_id  
											ORDER BY total,is_pnh,partner_id,partner_name",$pid);
		if($pid_orderdet->num_rows())
		{
			$output['product_orderdet']=$pid_orderdet->result_array();
			$output['status']='Success';
		}else 
		{
			$output['status']='error';
			$output['msg']='No Data Found';
		}
		echo json_encode($output);
		
	}
	
	function jx_to_load_patner_orddet($prodid='',$partnerid='')
	{
		$output=array();
		
			$sql="SELECT l.product_id,a.transid,is_pnh,partner_id,c.name AS partner_name,DATE_FORMAT(FROM_UNIXTIME(`time`),'%d/%m/%Y') AS orderd_on,(b.quantity*l.qty) as quantity,b.bill_person,(SELECT SUM(b.quantity))  AS ord_qty 
					FROM king_transactions a 
					JOIN king_orders b ON a.transid = b.transid 
					LEFT JOIN partner_info c ON c.id = a.partner_id 
					JOIN m_product_deal_link l ON l.itemid=b.itemid 
					WHERE l.product_id=?  AND b.status = 0 AND partner_id=? GROUP BY a.transid";
			
			$partner_transids_res=$this->db->query($sql,array($prodid,$partnerid));
			if($partner_transids_res->num_rows())
			{
				$output['partner_transdet']=$partner_transids_res->result_array();
				$output['status']='success';
			}
			else
			 {
			 	$output['status']='error';
			 }
		echo json_encode($output);
	}
	
	function jx_getqtybypartnerorder()
	{
		
		$user=$this->auth();
		$partnerid=$_POST['partnerid'];
		$prodid=$_POST['prodid'];
		
		$output = array();
		
		$qty = $this->db->query("SELECT SUM((b.quantity*l.qty)) AS quantity
						FROM king_transactions a 
						JOIN king_orders b ON a.transid = b.transid 
						LEFT JOIN partner_info c ON c.id = a.partner_id 
						JOIN m_product_deal_link l ON l.itemid=b.itemid 
						WHERE l.product_id=?  AND b.status = 0 AND partner_id=?
						group by l.product_id",array($prodid,$partnerid))->row()->quantity;
		
		$output['qty'] = $qty;
		$output['prod_id'] = $prodid;
		echo json_encode($output);
		
	}

	function to_load_purchase_pattern_details()
	{
		$output=array();
		$pid=$this->input->post('pid');
	
		
		$last_30dayspurchaseorderdet=$this->db->query("SELECT v.vendor_name,l.product_id,SUM(invoice_qty) AS ttl_qty ,l.margin,i.created_on
														FROM t_po_info t
														JOIN `t_grn_product_link` l ON l.po_id=t.po_id
														JOIN t_po_product_link p ON p.po_id=l.po_id
														JOIN `t_grn_invoice_link`i ON i.grn_id=l.grn_id
														JOIN `m_vendor_info` v ON v.vendor_id=t.vendor_id
														WHERE l.product_id=?  AND  DATE(i.created_on) >= DATE_SUB(CURDATE(),INTERVAL 30 DAY) 
														GROUP BY t.vendor_id",$pid);
		
		$last_60dayspurchaseorderdet=$this->db->query("SELECT v.vendor_name,l.product_id,SUM(invoice_qty) AS ttl_qty ,l.margin,i.created_on
														FROM t_po_info t
														JOIN `t_grn_product_link` l ON l.po_id=t.po_id
														JOIN t_po_product_link p ON p.po_id=l.po_id
														JOIN `t_grn_invoice_link`i ON i.grn_id=l.grn_id
														JOIN `m_vendor_info` v ON v.vendor_id=t.vendor_id
														WHERE l.product_id=?  AND DATE(i.created_on) >= DATE_SUB(CURDATE(),INTERVAL 60 DAY) 
														GROUP BY t.vendor_id",$pid);
		
		$last_90dayspurchaseorderdet=$this->db->query("SELECT v.vendor_name,l.product_id,SUM(invoice_qty) AS ttl_qty ,l.margin,i.created_on
														FROM t_po_info t
														JOIN `t_grn_product_link` l ON l.po_id=t.po_id
														JOIN t_po_product_link p ON p.po_id=l.po_id
														JOIN `t_grn_invoice_link`i ON i.grn_id=l.grn_id
														JOIN `m_vendor_info` v ON v.vendor_id=t.vendor_id
														WHERE l.product_id=?  AND  DATE(i.created_on) >= DATE_SUB(CURDATE(),INTERVAL 90 DAY) 
														GROUP BY t.vendor_id",$pid);
		
		if($last_30dayspurchaseorderdet->num_rows())
		{
			$output['last_30daydet']=$last_30dayspurchaseorderdet->result_array();
			$output['status']='success';
		}
		if($last_60dayspurchaseorderdet->num_rows())
		{
			$output['last_60daydet']=$last_60dayspurchaseorderdet->result_array();
			$output['status']='success';
		}
		if($last_90dayspurchaseorderdet->num_rows())
		{
			$output['last_90daydet']=$last_90dayspurchaseorderdet->result_array();
			$output['status']='success';
		}
		else
		{
			$output['status']='error';

		}
		echo json_encode($output);		
		
	}
	
	function download_manifesto($mnf_det)
	{
		$user=$this->auth(PNH_SHIPMENT_MANAGER);

		$this->load->plugin('csv_logger');
		$csv_obj=new csv_logger_pi();
		$csv_obj->head(array("Manifesto created","Shipped","Mid","Hub","Invice nos","Pickup by","Contact no"));

		if($mnf_det)
		{
			foreach($mnf_det as$i=> $det)
			{
				$man_hub_names = $this->db->query("select group_concat(distinct d.hub_name) as hub_names
					from pnh_m_manifesto_sent_log a
					join pnh_t_tray_invoice_link b on find_in_set(b.invoice_no,a.sent_invoices)
					left join pnh_t_tray_territory_link c on c.tray_terr_id = b.tray_terr_id
					join pnh_deliveryhub d on d.id = c.territory_id
					where a.id = ? ",$det['id'])->row()->hub_names;
		
				$awb=@$this->db->query("select awb from shipment_batch_process_invoice_link where inv_manifesto_id=?",$det['manifesto_id'])->row()->awb;
		
				$shipped_on=@$this->db->query("select shipped_on from shipment_batch_process_invoice_link where inv_manifesto_id=?",$det['manifesto_id'])->row()->shipped_on;
		
				$csv_obj->push(array(format_date($det['sent_on']),format_date($shipped_on),$det['id'],$man_hub_names,str_ireplace(',','||',$det['sent_invoices']),$det['pick_up_by'],$det['pick_up_by_contact']));
			}
	
			$csv_obj->download('Pnh_manifesto_details');
			return 1;
		}else{
			return 0;
		}
	}
	

	function print_franlabels()
	{
		$this->erpm->auth();
		$data['page']='pnh_print_fran_deliverylabel';
		$this->load->view("admin",$data);
		
	}
	
	function pnh_fran_delivary_label($terrid=false,$townid=false)
	{
		$user=$this->auth();
		
		$cond = "";
		if($terrid)
			$cond .= ' and territory_id = '.$terrid;
			
		if($townid)
			$cond .= ' and town_id in ('.$townid.') ';	
			
		$sql = "select * from pnh_m_franchise_info where 1 $cond order by franchise_name";
		$res_list = $this->db->query($sql);
				
		if($res_list->num_rows())
		{
			$data['fr_details'] = $res_list->result_array();
			$this->load->view('admin/body/pnh_print_fran_deliverylabel',$data);
		}
		else
		{
			echo "No Franchises Found";
		}
	}
	
	function pnh_fran_delvry_label_bytown()
	{
		$territory_id = $this->input->post('sel_territory_id');
		$output=array();	
		$res = $this->db->query("select id,town_name from pnh_towns where territory_id='".$territory_id."'");
		if($res->num_rows())
		{
			$output['town_list']=$res->result_array();
			$output['status']='success';
		}
		else
		{
				$output['status']='error';
				$output['message']='No Towns found under this territory';
		}
	
			echo json_encode($output);
	}

	/**
	 * function to update lr nos 
	 */
	function update_bulk_lrdetails()
	{
		$user=$this->erpm->auth(PNH_SHIPMENT_MANAGER);
		
		if($_POST)
		{
			$manifesto_id = $this->input->post('manifest_id');
			$lr_no = $this->input->post('lr_no');
			$amount_list = $this->input->post('amt');
			$no_ofboxes=$this->input->post('no_ofboxes');
			$weight=$this->input->post('weight');
			
			$send_sms=array();
			$send_sms[]=$territory_manager=$this->input->post('tm');
			$send_sms[]=$bussniss_executive=$this->input->post('be');
			$send_sms=array_filter($send_sms);
			
			foreach($manifesto_id as $ri=>$m)
			{
				if(!$m)
					continue;
				
				$l = $lr_no[$ri];
				$boxno = $this->db->query('select (max(ref_box_no))+1 as no from pnh_m_manifesto_sent_log ')->row()->no;
				$ttl_boxes = $no_ofboxes[$ri];
				$amt = $amount_list[$ri]; 
				$weight = $weight[$ri];
				$this->db->query("update pnh_m_manifesto_sent_log set status=3,modified_on=?,modified_by=?,lrno=?,amount=?,weight=?,lrn_updated_on=?,no_ofboxes=?,ref_box_no=? where id in ($m) and lrn_updated_on is null ",array(cur_datetime(),$user['userid'],$l,$amt,$weight,cur_datetime(),$ttl_boxes,$boxno));
				
				if($this->db->affected_rows())
				{
					$manifesto_id_det=$this->db->query("select id,sent_invoices,manifesto_id,pickup_empid from pnh_m_manifesto_sent_log where id in ($m) ")->result_array();
					foreach($manifesto_id_det as $manifest_det)
					{
						$sent_log_id = $manifest_det['id'];
						$manifesto_id=$manifest_det['manifesto_id'];
						$pick_up_emp_id=$manifest_det['pickup_empid'];
						$invoices=$manifest_det['sent_invoices'];
							
						//inert transaction table log
						foreach(explode(',',$invoices) as $inv)
						{
							$trans_logprm=array();
							$trans_logprm['transid']=$this->db->query("select transid from king_invoice where invoice_no=? limit 1",$inv)->row()->transid;
							$trans_logprm['admin']=$user['userid'];
							$trans_logprm['time']=time();
							$trans_logprm['msg']='invoice ('.$inv.') Shipped';
							$this->db->insert("transactions_changelog",$trans_logprm);
						}
						
						//update manifesto id to shipment_batch_process_invoice_link
						$this->db->query("update shipment_batch_process_invoice_link set shipped=?,shipped_on=?,shipped_by=?,awb=? where inv_manifesto_id in($manifesto_id) and shipped = 0 ",array(1,cur_datetime(),$user['userid'],$l));
						if($this->db->affected_rows() > 0)
						{
							$this->erpm->sendsms_franchise_shipments($invoices);
						}

						// mark shipped status in orders table
						$inv_order_list = $this->db->query(" select order_id
															from shipment_batch_process_invoice_link a
															join king_invoice b on a.invoice_no = b.invoice_no
															where a.inv_manifesto_id in ($manifesto_id) 
															group by order_id");
											
						if($inv_order_list->num_rows())
						{
							foreach($inv_order_list->result_array() as $row)
							{
								$this->db->query("update king_orders set status = 2,actiontime = unix_timestamp() where id = ? and status = 1 ",$row['order_id']);
							}
						}
					
						//sent a sms
						$sent_invoices_info=$this->db->query("select a.id as sent_log_id,a.lrno,a.hndlby_type,a.hndlby_roleid,a.sent_invoices,b.name,a.hndleby_name,b.contact_no,a.hndleby_contactno,a.hndleby_vehicle_num,
							c.name as  bus_name,d.contact_no as des_contact,e.courier_name,a.id
							from pnh_m_manifesto_sent_log a
							left join m_employee_info b on b.employee_id = a.hndleby_empid   
							left join pnh_transporter_info c on c.id=a.bus_id
							left join pnh_transporter_dest_address d on d.id=a.bus_destination and d.transpoter_id=a.bus_id
							left join m_courier_info e on e.courier_id = a.hndleby_courier_id
							where a.id in ($sent_log_id) ")->result_array();
						foreach($sent_invoices_info as $sent_invoices)
						{
							
							$lr_number=$sent_invoices['lrno'];
							$manifesto_id=$sent_invoices['sent_log_id'];
							$invoices=$sent_invoices['sent_invoices'];
							$hndbyname=$sent_invoices['name'];
							$hndbycontactno=$sent_invoices['contact_no'];
							$vehicle_num=$sent_invoices['hndleby_vehicle_num'];
								
							if($sent_invoices['hndlby_roleid']==6)
							{
								$hndbyname='Driver '.$sent_invoices['hndleby_name'];
								$hndbycontactno=$sent_invoices['hndleby_contactno'];
							}else if($sent_invoices['hndlby_roleid']==7)
							{
								$hndbyname='Fright Co-ordinator '.$sent_invoices['hndleby_name'];
								$hndbycontactno=$sent_invoices['hndleby_contactno'];
							}else if($sent_invoices['hndlby_type']==4)
							{
								$hndbyname='Courier '.$sent_invoices['courier_name'];
							}else if($sent_invoices['hndlby_type']==3 && $sent_invoices['bus_name'])
							{
								$hndbyname='Transporter '.$sent_invoices['bus_name'];
								$hndbycontactno=$sent_invoices['des_contact'];
							}
								
							$this->db->query("update pnh_invoice_transit_log set status = 1,logged_on=now() where sent_log_id in($sent_log_id) and status = 0 ");
							
							if(1)
							{
								$pick_up_by_details=$this->db->query("select a.employee_id,a.job_title2,a.name,a.contact_no from m_employee_info a where employee_id=?",$pick_up_emp_id)->row_array();
							
								$employees_list=$this->erpm->get_emp_by_territory_and_town($invoices);
							
								if($employees_list)
								{
									$town_list=array();
									foreach($employees_list as $emp)
									{
										$emp_name=$emp['name'];
										$emp_id=$emp['employee_id'];
										$town_name=$emp['town_name'];
										$territory_id=$emp['territory_id'];
										$town_id=$emp['town_id'];
										$town_list[]=$emp['town_name'];
										$job_title=$emp['job_title2'];
										$send_sms_status=$emp['send_sms'];
										$emp_contact_nos = explode(',',$emp['contact_no']);
											
										if(!in_array($job_title,$send_sms))
										{
											continue;
										}
											
							
										$sms_msg = 'Dear '.$emp_name.',Shipment for the town '.$town_name.' sent via '.ucwords($hndbyname).'('.$hndbycontactno.') Lr no: '.$lr_number.'.Manifesto Id : '.$manifesto_id;
										$temp_emp=array();
										foreach($emp_contact_nos as $emp_mob_no)
										{
											if(isset($temp_emp[$emp_id]))
												continue;
											$temp_emp[$emp_id]=1;
							
											if($send_sms_status)
												$this->erpm->pnh_sendsms($emp_mob_no,$sms_msg);
											//	echo $emp_mob_no,$sms_msg;
											$log_prm=array();
											$log_prm['emp_id']=$emp_id;
											$log_prm['contact_no']=$emp_mob_no;
											$log_prm['type']=4;
											$log_prm['territory_id']=$territory_id;
											$log_prm['town_id']=$town_id;
											$log_prm['grp_msg']=$sms_msg;
											$log_prm['created_on']=cur_datetime();
											$this->erpm->insert_pnh_employee_grpsms_log($log_prm);
										}
							
									}
								}
							
								if($pick_up_by_details)
								{
									$pemp_name=$pick_up_by_details['name'];
									$pemp_id=$pick_up_by_details['employee_id'];
									$pemp_contact_nos = explode(',',$pick_up_by_details['contact_no']);
							
									//pick up by
									if($town_list)
									{
										$town_list=array_unique($town_list);
										$town_list=array_filter($town_list);
									}else{
										$town_list=array();
									}
							
									$sms_msg = 'Dear '.$pemp_name.',Shipment for the town '.implode(',',$town_list).' sent via '.ucwords($hndbyname).'('.$hndbycontactno.') Lr no:'.$lr_number.'.Manifesto Id : '.$manifesto_id;
							
									$temp_emp=array();
									foreach($pemp_contact_nos as $emp_mob_no)
									{
										if(isset($temp_emp[$pemp_id]))
											continue;
										$temp_emp[$pemp_id]=1;
											
										$this->erpm->pnh_sendsms($emp_mob_no,$sms_msg);
											
										$log_prm2=array();
										$log_prm2['emp_id']=$pemp_id;
										$log_prm2['contact_no']=$emp_mob_no;
										$log_prm2['type']=4;
										$log_prm2['grp_msg']=$sms_msg;
										$log_prm2['created_on']=cur_datetime();
										$this->db->insert("pnh_employee_grpsms_log",$log_prm2);
									}
								}
							}
						}
					}
				}
			}
			
			$this->session->set_flashdata("erp_pop_info","Manifesto Status updated");
			redirect('admin/update_bulk_lrdetails','refresh');
			exit;
		}

		$data['page']='pnh_bulk_update_lrno';
		$this->load->view("admin",$data);
	}

	function _validate_check_productid($id)
	{
		// Check if product exists
		if($this->db->query("select count(*) as t from m_product_info where product_id = ? ",$id)->row()->t)
		{
			// check if product is already linked for other group or deals 
			if($this->db->query('select count(*) as t from m_product_deal_link where product_id = ? and is_active = 1 ',$id)->row()->t)
			{
				$this->form_validation->set_message('_validate_check_productid','Product is already Linked to Other Deal ');
				return false;
			}

			// check if product is already linked for other group or deals 
			if($this->db->query('select count(*) as t from products_group_pids where product_id = ? ',$id)->row()->t)
			{
				$this->form_validation->set_message('_validate_check_productid','Product is already Linked to Other Group ');
				return false;
			}
		}else
		{
			$this->form_validation->set_message('_validate_check_productid','Invalid ProductID Entered');
			return false;
		}
			
	}

	/**
	 * function to add product to group  
	 */
	function jx_upd_producttogroup()
	{
		$this->erpm->auth(true);
		$attr_list = $this->input->post('new_prod_attr');
		$attr_name_list = $this->input->post('new_prod_attr_names');
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('group_id','Group','required');
		$this->form_validation->set_rules('new_prod_id','Product ID','required|callback__validate_check_productid');
		foreach($attr_list as $a_id=>$v)
			$this->form_validation->set_rules('new_prod_attr['.$a_id.']',$attr_name_list[$a_id],'required');	
		
		$output = array();
		if($this->form_validation->run() === FALSE)
		{
			$output['status'] = 'error';
			$output['error'] = validation_errors();
		}else
		{
			$grp_id = $this->input->post('group_id');
			$prod_id = $this->input->post('new_prod_id');
			foreach($attr_list as $attr_name_id=>$v)
			{
				// check if attribute is already available 
				$attr_val_id = @$this->db->query('select count(*) as t from products_group_attribute_values where group_id = ? and attribute_value = ? ',array($grp_id,$v))->row()->attribute_value_id;
				
				if(!$attr_val_id)
				{
					// update attr value to group attrs and get value reference id 
					$ins_data = array();
					$ins_data['group_id'] = $grp_id;
					$ins_data['attribute_name_id'] = $attr_name_id;
					$ins_data['attribute_value'] = $v;
					$this->db->insert('products_group_attribute_values',$ins_data);
					$attr_val_id = $this->db->insert_id();	
				}
				
				$ins_data = array();
				$ins_data['group_id'] = $grp_id;
				$ins_data['product_id'] = $prod_id;
				$ins_data['attribute_name_id'] = $attr_name_id;
				$ins_data['attribute_value_id'] = $attr_val_id;
				$this->db->insert('products_group_pids',$ins_data);
				
			}
			
			$output['status'] = 'success';
				
		}

		echo json_encode($output);
		
	}
	
	/**
	 * function to view manifesto Lr details
	 * @param unknown_type $st_date
	 * @param unknown_type $en_date
	 * @param unknown_type $manifestoid
	 * @param unknown_type $pg
	 */
	function manifestolr_log($st_date=0,$en_date=0,$manifestoid=0,$pg=0)
	{
		$this->erpm->auth();

		if(isset($_POST['st_dt']))
		{
			$st_date=$this->input->post('st_dt');
			$en_date=$this->input->post('en_dt');
			$manifestoid=$this->input->post('srch_manifesto');
		}
		
		$data['pagetitle'] = '';
		$cond='';
		if($st_date!=0 && $en_date!=0)
		{
			$cond =" and (date(sent_on) >="."'$st_date'". " &&  date(sent_on) <="."'$en_date'".")";
			$data['pagetitle']=" Showing From ". $st_date ." To ". $en_date.'&nbsp;&nbsp;' ;
				
		}
		else 
		{
			$st_date='0000-00-00';
			$en_date='0000-00-00';
		}
		if($manifestoid!=0)
		{
			$cond.=' AND manifesto_id in ('.$manifestoid.')';
			//$data['pagetitle'].="Manifesto Lr Details Of Manifesto ID ".$manifestoid.'&nbsp;&nbsp;';
		}
		else 
		{
			$manifestoid = 0;
		}
			
		$limit=50;
		$ttl_data=$this->db->query("select group_concat(a.id) as id,lrno,lrn_updated_on,amount,no_ofboxes,modified_on,b.username as updated_by
												from pnh_m_manifesto_sent_log a
												join king_admin b on a.modified_by = b.id 
												where lrno is not null and ref_box_no != 0 $cond 
												group by ref_box_no 
												order by sent_on desc ")->num_rows();
		$manifesto_lr_res=$this->db->query(" 
											select group_concat(a.id) as id,weight,lrno,lrn_updated_on,amount,no_ofboxes,modified_on,b.username as updated_by
												from pnh_m_manifesto_sent_log a
												join king_admin b on a.modified_by = b.id 
												where lrno is not null and ref_box_no != 0 $cond
												group by ref_box_no 
												order by sent_on desc  LIMIT $pg,$limit ");

		if($ttl_data)
			$data['pagetitle'].= 'Total : '.$ttl_data;
			
		$data['st_date']=$st_date?st_date:0;
		$data['en_date']=$en_date?$en_date:0;
		$data['ttl_data']=$ttl_data;
		$data['pagination'] = $this->_prepare_pagination(site_url('admin/manifestolr_log/'.$st_date.'/'.$en_date.'/'.$manifestoid),$data['ttl_data'],$limit,6);
		$data['manifesto_lr_res'] = $manifesto_lr_res;
		$data['page']='view_manifestolr_log';
		$this->load->view("admin",$data);
	}

    function jx_put_courier_priority() 
	{
	    $user=$this->erpm->auth(); //return current login user details
        if($_POST) {
            $_POST['userid'] = $user['userid'];
            echo $this->erpm->put_town_courier_priority();

        }
    }
    /**
     * Set towns courier priority
     */
    function towns_courier_priority($terrid='0',$assign_status='') {
        $user=$this->erpm->auth(PRODUCT_MANAGER_ROLE|STOCK_INTAKE_ROLE|PURCHASE_ORDER_ROLE);
		
		$field_cond=$cond='';
        if($terrid) 
        {
            $cond .= " and tw.territory_id=".$terrid;
            $data['terr_selected'] = $this->db->query("select id as territory_id,territory_name from pnh_m_territory_info  where id=? order by territory_name",$terrid)->row_array();
        }

        if($assign_status !== '' and $assign_status == 1) {
            $cond .= "  and tcp.courier_priority_1 is NOT null ";
            //$data['terr_selected'] = $this->db->query("select id as territory_id,territory_name from pnh_m_territory_info  where id=? order by territory_name",$terrid)->row_array();
        }
        if($assign_status !== '' and $assign_status == 0) {
            $cond .= "  and tcp.courier_priority_1 is null ";
            //$data['terr_selected'] = $this->db->query("select id as territory_id,territory_name from pnh_m_territory_info  where id=? order by territory_name",$terrid)->row_array();
        }
        if($cond!='') 
        {
            $cond = " where 1 ".$cond;
        }
        $data['pnh_terr'] = $this->db->query("select ter.id,ter.territory_name from pnh_m_territory_info as ter group by ter.id order by territory_name")->result_array();

        $sql="select distinct tw.id as townid,tw.town_name,count(frn.franchise_id) as fran_count,tcp.* from pnh_towns tw
                                                            left join `pnh_town_courier_priority_link` tcp on tcp.town_id=tw.id and tcp.is_active=1
                                                            left join `pnh_m_franchise_info` frn on frn.town_id = tw.id and frn.is_suspended=0
                                                            $cond
                                                            group by tw.id order by tw.town_name";
        //echo '<pre>';die($sql);
        $data['towns_courier_priority']=$this->db->query($sql)->result_array();

        $data['courier_providers'] = $this->db->query("select * from m_courier_info where is_active =1")->result_array();
        
		$data['sel_terr_id']=$terrid;
        $data['assign_status']=$assign_status;
        $data['user']=$user;
        $data['page']='towns_courier_priority';
        $this->load->view("admin",$data);
    }

	
	/**
	 * function print the logistick manifesto list 
	 */
	function jx_print_logistick_manifeasto_list()
	{
		$this->erpm->auth();
		$f_dat=$this->input->post("m_date");
		
		if(!$f_dat)
			exit;
		
		$sql="select a.hndlby_type,a.lrno,group_concat(a.id) as manifesto_id,a.no_ofboxes,concat(ifnull(b.name,''),ifnull(c.courier_name,''),ifnull(d.name ,'')) as name,
					a.sent_on,e.name as cby,a.bus_id,a.hndleby_courier_id,a.hndleby_empid,group_concat(a.sent_invoices) as inv
					from  pnh_m_manifesto_sent_log as a
					left join m_employee_info b on b.employee_id=a.hndleby_empid
					left join m_courier_info c on c.courier_id=a.hndleby_courier_id
					left join pnh_transporter_info d on d.id=a.bus_id
					join king_admin e on e.id=a.created_by
				where date(sent_on)=?
				group by a.hndleby_empid,a.hndleby_courier_id,a.bus_id,a.hndlby_type,a.lrno";
		
		$manifesto_det_res=$this->db->query($sql,$f_dat);
		
		$print_logistick_manifesto_det=array();
		
		if($manifesto_det_res->num_rows())
		{
			$manifesto_list=$manifesto_det_res->result_array();
			
			foreach($manifesto_list as $ml)
			{
				if(!isset($print_logistick_manifesto_det[$ml['hndlby_type']]))
					$print_logistick_manifesto_det[$ml['hndlby_type']]=array();
				
				if(!isset($print_logistick_manifesto_det[$ml['hndlby_type']][$ml['name']]))
				{
					$print_logistick_manifesto_det[$ml['hndlby_type']][$ml['name']]=array();
				}
				
				array_push($print_logistick_manifesto_det[$ml['hndlby_type']][$ml['name']],$ml);
			}
			
			$data['m_date']=$f_dat;
			$data["logistick_manifsto_list"]=$print_logistick_manifesto_det;
			$this->load->view('admin/body/print_logistick_manifesto_list',$data);
		}else{
			echo '<script type="text/javascript">alert("No manifesto details found for selected date")</script>';
		}
	}
	
	/**
	 * function to add new franchise security details 
	 * 
	 * @author Shariff
	 */
	function jx_add_security_chqdet()
	{
		$user = $this->erpm->auth(FINANCE_ROLE);
		
		$fid = $this->input->post('f_schq_fid');
		
		$s_bank_name = $this->input->post('f_schq_bankname');
		$s_chqno = $this->input->post('f_schq_no');
		$s_chqdate = $this->input->post('f_schq_date');
		$s_chqamt = $this->input->post('f_schq_amt');
		$s_chq_colon = $this->input->post('f_schq_colon');
		
		$output= array();
		// check if cheque no already exists
		if($this->db->query("select count(*) as t from pnh_m_fran_security_cheques where cheque_no = ? and franchise_id= ?  ",array($s_chqno,$fid))->row()->t)
		{
			$output['status'] = 'error';
			$output['error'] = 'Cheque no is already exists';
		}else
		{
			$inp = array();
			$inp['franchise_id'] = $fid;
			$inp['bank_name'] = $s_bank_name;
			$inp['cheque_no'] = $s_chqno;
			$inp['cheque_date'] = $s_chqdate;
			$inp['amount'] = $s_chqamt;
			$inp['collected_on'] = $s_chq_colon;
			$inp['created_by'] = $user['userid'];
			$inp['created_on'] = cur_datetime();
			$this->db->insert('pnh_m_fran_security_cheques',$inp);
			$output['status'] = 'success';
		}
		echo json_encode($output);
	}


	/**
	 * function to check if imei is valid for packing 
	 * 
	 */
	function jx_chkimeiforpack()
	{
		$output = array();
		$imei = $this->input->post('imeino');
		
		$resp = $this->db->query("select * from t_imei_no where imei_no = ? ",$imei);
		
		if(!$resp->num_rows())
		{
			$output['status'] = 'error';
			$output['error'] = 'Scanned IMEI not found ';
		}else if($resp->num_rows() > 1)
		{
			$ttl_processed = 0; 
			foreach($resp->result_array() as $row)
				if($row['status'] == 1)
					$ttl_processed++;
			
			if(!$ttl_processed)
			{
				$output['status'] = 'success';
			}else
			{
				$output['status'] = 'error';
				$output['error'] = 'IMEI Already Shipped';
			}
			
		}else if($resp->num_rows() == 1)
		{
			$imei_det = $resp->row_array();
			if($imei_det['status'] == 1)
			{
				$output['status'] = 'error';
				$output['error'] = 'IMEI is already alloted to other order';	
			}else
			{
				$output['status'] = 'status';
			}
		}
		echo json_encode($output);
	}
	/**
	 * function to check if valid imei is entered for stock intake 
	 */
	function jx_chkimeiforgrn()
	{
		$output = array();
		$imei = $this->input->post('imeino');
		
		$resp = $this->db->query("select * from t_imei_no where imei_no = ? ",$imei);
		
		if(!$resp->num_rows())
		{
			$output['status'] = 'success';
		}else 
		{
			$output['status'] = 'error';
			$output['error'] = 'Duplicate IMEI found';
		}
		echo json_encode($output);
	}
	
	function to_download_franstat_exl_format($fid,$frm_dt,$to_dt)
	{
		$user=$this->auth(FINANCE_ROLE);
		$receipts_types=array("Deposit","Topup");
		$invoice_status=array("Invoice Cancelled","Invoice");
		$receipt_status=array("Processed","Topup","Receipt Bounced"," Receipt Reversed");
		$action_types=array(" ","Invoice","Deposit","Topup","Member Ship","Account Correction");

		$frm_dt = $frm_dt.' 00:00:00';
		$to_dt = $to_dt.' 23:59:59';

		$sql="SELECT a.statement_id,a.franchise_id,f.franchise_name,action_type,a.invoice_no,r.receipt_id,r.receipt_type,cheque_no,debit_amt,credit_amt,a.created_on,a.status,inv.invoice_status,r.instrument_no,r.status AS receipt_status,a.remarks
				FROM pnh_franchise_account_summary a
				JOIN pnh_m_franchise_info f ON f.franchise_id=a.franchise_id
				LEFT JOIN king_invoice inv ON inv.invoice_no=a.invoice_no
				LEFT JOIN pnh_t_receipt_info r ON r.receipt_id=a.receipt_id  
				WHERE a.franchise_id=? and a.created_on between ? and ?
				GROUP BY statement_id
				ORDER BY a.created_on asc
			";
		$fran_acc_stat_details=$this->db->query($sql,array($fid,$frm_dt,$to_dt))->result_array();

		$objPHPExcel = new PHPExcel();
		$F=$objPHPExcel->getActiveSheet();
		$Line=2;
		$objPHPExcel->getActiveSheet()->getStyle("A1:I1")->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Franchise ID');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Franchise Name');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Date');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Document Type');
		$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Document refno');
		$objPHPExcel->getActiveSheet()->setCellValue('F1', 'Value (Rs)');
		$objPHPExcel->getActiveSheet()->setCellValue('G1', 'Remarks');


		$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(A1)->getValue();

		foreach($fran_acc_stat_details as $i=>$Trs)
		{
			if($Trs['receipt_type'] == 0 && $Trs['action_type'] == 2)
				continue ;
			$doc_status = '';	
			$doc_refno=$Trs['invoice_no']!=0?$Trs['invoice_no']:$Trs['receipt_id'] ;

			$value = $Trs['credit_amt']!=0 ? $Trs['credit_amt']:$Trs['debit_amt']*-1 ;

			
			switch($Trs['action_type'])
			{
				case 1 :
					    if($Trs['credit_amt'] > 0)
							$doc_status = 'Invoice Cancelled';
						else
							$doc_status = 'Invoice';
					break;
				case 2:
				case 3:
						if($Trs['receipt_type'] == 0)
							$doc_status = 'Deposit';
						else
							$doc_status = 'Topup';
					break;
				case 4 : 	
						$doc_status = 'Member Registration';
					break;
				case 5 : 	
						$doc_status = 'Account Correction';
					break;
				case 7 : 
						$doc_status = 'CreditNote';
						break;	
			}
			
			$F->setCellValue('A'.$Line, $Trs['franchise_id'])
				->setCellValue('B'.$Line, $Trs['franchise_name'])//write in the sheet
				->setCellValue('C'.$Line,format_datetime($Trs['created_on']))
				->setCellValue('D'.$Line, $doc_status)
				->setCellValue('E'.$Line,$doc_refno)
				->setCellValue('F'.$Line,$value)
				->setCellValue('G'.$Line, $Trs['remarks']);
			++$Line;
		}
		$today_date=$this->db->query("SELECT DATE_FORMAT(CURDATE(),'%d %b %Y') AS today_dt")->row()->today_dt;
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Franchise_account_stat.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}

	function pnh_states()
	{
		$data['states']=$this->db->query("select * from pnh_m_states order by state_name asc")->result_array();
		$data['page']="pnh_states";
		$this->load->view("admin",$data);
	}


	/**
	 * function to load franchise call log list 
	 * 
	 */
	function jx_get_franchise_calllog($franchise_id,$pg=0)
	{
		$this->erpm->auth();
		
		$per_page=10;
		$output = array();
		$sql = "select l.id,l.msg,a.name,date_format(from_unixtime(l.created_on),'%d/%m/%Y %h:%i %p') as created_on  
							from pnh_call_log l 
							join king_admin a on a.id=l.created_by 
							where franchise_id=?  
							order by l.created_on desc 
						limit $pg,10";
						
		$fr_call_log_res = $this->db->query($sql,$franchise_id);
		if($fr_call_log_res->num_rows())
		{
			
			$ttl_calllogs = $this->db->query("select count(distinct l.id) as t   
													from pnh_call_log l 
													join king_admin a on a.id=l.created_by 
													where franchise_id=?  
												order by l.created_on desc ",$franchise_id)->row()->t;
			$output['status'] = 'success';
			$output['call_log_list'] = $fr_call_log_res->result_array();
			$output['call_log_pagi'] = $this->_prepare_pagination(site_url('admin/jx_get_franchise_calllog/'.$franchise_id),$ttl_calllogs, $per_page, 4);
		}else
		{
			$output['status'] = 'error';
			$output['error'] = 'no data found';
		}
		
		echo json_encode($output);
	}	
	
	function pnh_upd_prod_status($pid)
	{
		$user = $this->erpm->auth();
		
		$output = array();
		$this->db->query("update m_product_info set is_sourceable = !is_sourceable where product_id = ? limit 1",$pid);
		$upd_rows = $this->db->affected_rows();
		$p_det = $this->db->query("select * from m_product_info where product_id = ? ",$pid)->row_array();
		if($upd_rows > 0 )
		{
			$inp=array("product_id"=>$pid,"is_sourceable"=>$p_det['is_sourceable'],"created_on"=>time(),"created_by"=>$user['userid']);
			$this->db->insert("products_src_changelog",$inp);	
		}
		
		$output['pstatus'] = ($p_det['is_sourceable']?'Sourceable':'Not Sourceable');
		echo json_encode($output);
		
	}
	 
	/**
	 * fucntion to print franchise delivery labels by manifesto ID 
	 */
	function print_fran_deliverylabelby_manid($manid=0)
	{
		$user=$this->auth();
		
			
		$sql = "select f.* 
						from shipment_batch_process_invoice_link a 
						join pnh_m_manifesto_sent_log b on a.inv_manifesto_id = b.id 
						join king_invoice c on c.invoice_no = a.invoice_no
						join king_orders d on d.id = c.order_id
						join king_transactions e on e.transid = d.transid 
						join pnh_m_franchise_info f on f.franchise_id = e.franchise_id 
					where b.id = ?  
					group by e.franchise_id
					order by franchise_name  
				";
		$res_list = $this->db->query($sql,$manid);
		
		//echo $this->db->last_query();
				
		if($res_list->num_rows())
		{
			$data['fr_details'] = $res_list->result_array();
			$this->load->view('admin/body/pnh_print_fran_deliverylabel',$data);
		}
		else
		{
			echo "No Franchises Found";
		} 
		
	}

	function fld_margin_log()
	{
		$data['page']='franchise_local_distributor_mrgn_log';
		$data['fld_mrgn_log_res']=$this->db->query(" SELECT l.franchise_id,f.franchise_name,l.margin,l.created_on,a.name AS updatedby,l.dealid,i.pnh_id,i.name,i.id as deal_id,l.orderid,o.transid
													 FROM `pnh_l_franchise_distrubuter_margin` l
													 JOIN pnh_m_franchise_info f ON l.franchise_id=f.franchise_id
													 JOIN king_admin a ON a.id=l.created_by
													 JOIN king_dealitems i ON i.dealid=l.dealid
													 JOIN king_orders o on o.id=l.orderid
													 JOIN king_deals d ON d.dealid=i.dealid");
		$this->load->view("admin",$data);
	}
	/**
	 * function to get all deals for category brand menu
	 * @param unknown_type $menu
	 * @param unknown_type $brand
	 * @param unknown_type $cat
	 */
	function jx_to_getdeals_bybrandcatmenu($menu=0,$brand=0,$cat=0)
	{
		$is_sorceble_deal=array();
		$is_sorceble_deal['sourceable']=array();
		$is_sorceble_deal['nonsourceable']=array();
	
		$output=array();
		$deal_list=$this->db->query("SELECT DISTINCT i.id,i.dealid,i.name,f.is_sourceable FROM king_dealitems i
								 		JOIN king_deals d ON d.dealid=i.dealid
										JOIN king_brands b ON b.id=d.brandid
										JOIN king_categories c ON c.id=d.catid
										JOIN pnh_menu m ON m.id=d.menuid
										JOIN m_product_deal_link p ON p.itemid=i.id
										JOIN m_product_info f ON f.product_id=p.product_id
										WHERE d.menuid=? AND brandid=? AND catid=?",array($menu,$brand,$cat));
		
		if($deal_list)
		{
			$output['status']='success';
			$output['deal_list']=$deal_list->result_array();
		}
		else 
		{
			$output['status']='error';
			$output['message']='No Deals Found';
		}
		echo json_encode($output);
	}
	
	function jx_check_has_scheme($menuid=0,$catid=0,$brandid=0,$sch_type=0)
	{
			$output=array();
			$fran_schstatus=array();
			$fran_schstatus['has_schdisc']=array();
			$fran_schstatus['has_mbrsch']=array();
			$fran_schstatus['has_supersch']=array();
			
			if($sch_type==1)
			{
				$has_sch_disc=$this->db->query("select distinct franchise_id  from  pnh_sch_discount_brands where menuid=? and catid=? and brandid=? and ? between valid_from and valid_to and is_sch_enabled=1",array($menuid,$catid,$brandid,time()));
				if($has_sch_disc)
				{
					
					$output['status']='success';
					$output['has_sch_disc']=$has_sch_disc->result_array();
				}
				
			}	
			
			if($sch_type==3)
			{
				$has_mbr_sch = $this->db->query("select distinct franchise_id from  imei_m_scheme where menuid=? and categoryid=? and brandid=? and ? between sch_apply_from and scheme_to and is_active=1",array($menuid,$catid,$brandid,time()));
				
				if($has_mbr_sch)
				{
					$output['status']='success';
					$output['has_sch_disc']=$has_mbr_sch->result_array();
				}
			}
			if($sch_type==2)
			{
				$has_spr_sch=$this->db->query("select distinct franchise_id from  pnh_super_scheme where menu_id=? and cat_id=? and brand_id=? and ? between valid_from and valid_to and is_active=1",array($menuid,$catid,$brandid,time()));
				if($has_spr_sch)
				{
					
					$output['status']='success';
					$output['has_sch_disc']=$has_spr_sch->result_array();
				}
			}

		echo json_encode($output);
	}
}
