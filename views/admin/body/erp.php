<?php 

class Erp extends Controller
{
	public $user_access=array();
	
	function __construct()
	{
		parent::__construct();	
		$this->task_status = $this->config->item('task_status');
		$this->db->query("set session group_concat_max_len=5000000;");
		$this->task_for=$this->config->item('task_for');
		
		$this->load->library('pagination');
		
		
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
	
	function productsbytax($tax=false)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		if(empty($tax))
			show_404();
		$data['tax']=number_format($tax,2);
		$data['products']=$this->erpm->getproductsbytax($tax);
		$data['page']="products";
		$this->load->view("admin",$data);
	}
	
	function productsbybrand($bid=false)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		if(empty($bid))
			show_404();
		$data['brand']=$this->db->query("select name from king_brands where id=?",$bid)->row()->name;
		$data['products']=$this->erpm->getproductsbybrand($bid);
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
	
	function products()
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		$data['products']=$this->erpm->getproducts();
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
			$v_join_cond = ' join m_vendor_brand_link vb on vb.brand_id = a.brand_id and vendor_id = '.$vid;
			$v_cond = ' and vendor_id = '.$vid;
		}
				
		
		$sql = "(	select distinct a.product_id as id,product_name as product,a.mrp,a.is_sourceable as src,ifnull(sum(s.available_qty),0) as stock,0 as orders,0 as margin,0 as otime,0 as pen_ord_qty
						from m_product_deal_link d
						join m_product_info a on d.product_id = a.product_id 
						$v_join_cond 
						left join t_stock_info s on s.product_id = a.product_id 
						where a.brand_id=$bid 
					group by d.id 
				)
				union 
				(
					select distinct a.product_id as id,product_name as product,a.mrp,a.is_sourceable as src,0,ifnull(sum(o.quantity*d.qty),0) as orders,0 as margin,o.time as otime,0 as pen_ord_qty
						from m_product_deal_link d
						join m_product_info a on d.product_id = a.product_id 
						$v_join_cond 
						left join king_orders o on o.itemid = d.itemid and o.time > (unix_timestamp()-(24*90*60*60))  
						where a.brand_id=$bid 
					group by d.id 
				)
				union 
				(
					select distinct a.product_id as id,product_name as product,a.mrp,a.is_sourceable as src,0,0,0 as margin,o.time as otime,sum(o.quantity*d.qty) as pen_ord_qty
						from m_product_deal_link d
						join m_product_info a on d.product_id = a.product_id 
						$v_join_cond 
						join king_orders o on o.itemid = d.itemid   
						where a.brand_id=$bid and o.status = 0 
					group by a.product_id   
				)
				union 
				(
					select distinct a.product_id as id,product_name as product,a.mrp,a.is_sourceable as src,0,0,brand_margin as margin,0 as otime,0 as pen_ord_qty
						from m_product_info a
						join m_vendor_brand_link vb on vb.brand_id = a.brand_id  
						where a.brand_id=$bid $v_cond group by a.product_id 
						order by brand_margin asc
				)
				order by otime desc,orders desc,product asc   
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
	
	function purchaseorders($s=false,$e=false)
	{
		$user=$this->auth(PURCHASE_ORDER_ROLE|FINANCE_ROLE);
		if($s!=false && $e!=false && (strtotime($s)<=0 || strtotime($e)<=0))
			show_404();
		$data['pos']=$this->erpm->getpos_date_range($s,$e);
		if($e)
			$data['pagetitle']="between $s and $e";
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
	
	function jx_load_unavail_products()
	{
		$vendor_id = $this->input->post('vid');
		$join_cond = '';
		$join_cond_fld = '';
		if($vendor_id)
		{
			$join_cond = ' join m_vendor_brand_link vb on vb.brand_id = b.id and vb.vendor_id = '.$vendor_id;
			$join_cond_fld = ',brand_margin';
		}
		
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
								group_concat(distinct concat(a.transid,':',unix_timestamp(date(from_unixtime(t.init))),':',a.id,':',t.franchise_id,':',e.franchise_name,':',b.qty*a.quantity) ORDER BY t.init ASC ) as fran_order_det 
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
								group_concat(distinct concat(a.transid,':',unix_timestamp(date(from_unixtime(t.init))),':',a.id,':',t.franchise_id,':',e.franchise_name,':',b.qty*a.quantity) ORDER BY t.init ASC ) as fran_order_det 
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
	
	function orders($status=0,$s=false,$e=false,$orders_by='all',$limit=50,$pg=0)
	{
		$user=$this->auth(CALLCENTER_ROLE);
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
	
	function stock_correction()
	{
		$user=$this->auth(false);
		if(empty($_POST))
			die;
		foreach(array("pid","msg","loc","corr","type","mrp_prod") as $i)
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
				
		// check if product is in stock
		$chk_stk_prod_avail_res = $this->db->query("select stock_id from t_stock_info where product_id=? and location_id = ? and rack_bin_id = ? and product_barcode = ? and mrp = ? ",array($pid,$loc_id,$rb_id,$bc,$mrp));
		if(!$chk_stk_prod_avail_res->num_rows())
		{
			$inp=array("product_id"=>$pid,"available_qty"=>0,"location_id"=>$loc_id,"rack_bin_id"=>$rb_id,"mrp"=>$mrp,"product_barcode"=>$bc,"created_by"=>$user['userid'],"created_on"=>time());
			$this->db->insert("t_stock_info",$inp);
		}
		
		/*
		if($this->db->query("select 1 from t_stock_info where product_id=?",$pid)->num_rows()==0)
		{
			$r=$this->db->query("select r.location_id,r.id 
													from m_rack_bin_brand_link b 
													join m_rack_bin_info r on r.id=b.rack_bin_id 
													where b.brandid=?
								",$p['brand_id'])->row_array();
			if(empty($r))
				show_error("No rack bins associated to brand");

			list($loc_id,$rb_id) = explode('_',$loc);
				
				
			$inp=array("product_id"=>$pid,"location_id"=>$loc_id,"rack_bin_id"=>$rb_id,"mrp"=>$p['mrp'],"created_by"=>$user['userid'],"created_on"=>time());
			$this->db->insert("t_stock_info",$inp);
		}
		
		$check_mrp=false;
		if($this->db->query("select 1 from t_stock_info where product_id=? and mrp=?",array($p['product_id'],$p['mrp']))->num_rows()==1)
			$check_mrp=true;
		$sql="update t_stock_info set available_qty=available_qty".($type==1?"+":"-")."? where product_id=?";
		if($check_mrp)
			$sql.=" and mrp=?";
		$sql.=" limit 1";
		$this->db->query($sql,array($corr,$pid,$p['mrp']));	
		*/
		
		$sql="update t_stock_info 
								set available_qty=available_qty".($type==1?"+":"-")."?
								where product_id=?
								and mrp=? and product_barcode = ? and location_id = ? and rack_bin_id = ?  
								limit 1";
		$this->db->query($sql,array($corr,$pid,$mrp,$bc,$loc_id,$rb_id));	
		
		$this->erpm->do_stock_log($type,$corr,$pid,"",false,false);
		$this->db->query("update t_stock_update_log set msg=? where id=? limit 1",array($msg,$this->db->insert_id()));
		$this->erpm->flash_msg("Stock corrected");
		redirect("admin/product/$pid");
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
						$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and stock_id = ? limit 1";
						$this->db->query($sql,array($rqty,$p['product_id'],$reserv_stk_det['stock_info_id']));
						$this->db->query("update t_reserved_batch_stock set status=3 where id = ? ",array($reserv_stk_det['id']));
					}
					$c_qty = $p_reserv_qty;
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
					
					$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and mrp=? limit 1";
					$this->db->query($sql,array($p['qty']*$o['qty'],$p['product_id'],$p['mrp']));
					$c_qty = $p['qty']*$o['qty'];
				}
				$this->erpm->do_stock_log(1,$c_qty,$p['product_id'],$invoice[0]['id'],false,true,false);
				
				
			}
			$this->db->query("update t_imei_no set status=0 where order_id=?",$o['id']);
		}
		 
			
		$this->db->query("update king_orders set status=0 where id in ('".implode("','",$oids)."') and transid=?",$transid);
		$this->db->query("update king_invoice set invoice_status=0 where invoice_no=?",$invno);
		
		
		$inv_trans_det = $this->db->query("select franchise_id,a.transid,b.invoice_no,is_pnh,sum((mrp-discount)*quantity) as inv_amt
													from king_transactions a 
													join king_invoice b on a.transid = b.transid 
													join king_orders c on c.id = b.order_id 
													where b.invoice_no = ? and b.invoice_status = 0 ",$invno)->row_array();
		
		if($inv_trans_det['is_pnh'])
		{
			$arr = array($inv_trans_det['franchise_id'],1,$inv_trans_det['invoice_no'],$inv_trans_det['inv_amt'],'',1,date('Y-m-d H:i:s'),$user['userid']);
			$this->db->query("insert into pnh_franchise_account_summary (franchise_id,action_type,invoice_no,credit_amt,remarks,status,created_on,created_by) values(?,?,?,?,?,?,?,?)",$arr);	
		}
		
		
				
		
		$this->db->query("update proforma_invoices set invoice_status=0 where p_invoice_no=?",$this->db->query("select p_invoice_no as i from shipment_batch_process_invoice_link where invoice_no=?",$invno)->row()->i);
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
			if($r['amount'] > 0)
				$this->erpm->pnh_sendsms($this->db->query("select login_mobile1 as m from pnh_m_franchise_info where franchise_id=?",$r['franchise_id'])->row()->m,$sms_text,$r['franchise_id']);
		}
		if(!$from_pending)
			redirect("admin/trans/".$this->db->query("select transid from t_refund_info where refund_id=?",$rid)->row()->transid);
		redirect("admin/pending_refunds_list");
	}
	
	function change_qy_order($transid)
	{
		$user=$this->auth(CALLCENTER_ROLE);
		$refund=$this->input->post("nc_refund");
		$oid=$this->input->post("nc_oid");
		$qty=$this->input->post("nc_qty");
		$n_oid=random_string("numeric",10);
		$order=$this->db->query("select * from king_orders where id=?",$oid)->row_array();
		$prod=$this->db->query("select name from king_dealitems where id=?",$order['itemid'])->row_array();
		$n_qty=$order['quantity']-$qty;
		$inp=array($n_oid,$transid,$order['userid'],$order['itemid'],$order['vendorid'],$order['brandid'],$n_qty,3,$order['i_orgprice'],$order['i_price'],$order['i_nlc'],$order['i_phc'],$order['i_tax'],$order['i_discount'],$order['i_coup_discount'],$order['i_discount_applied_on'],$order['time'],time());
		$this->db->query("insert into king_orders(id,transid,userid,itemid,vendorid,brandid,quantity,status,i_orgprice,i_price,i_nlc,i_phc,i_tax,i_discount,i_coup_discount,i_discount_applied_on,time,actiontime) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",$inp);
		$this->db->query("update king_orders set quantity=? where id=? limit 1",array($qty,$oid));
		$this->erpm->do_trans_changelog($transid,"Order '{$prod['name']}' quantity changed from {$order['quantity']} to {$qty}");
		$this->db->query("insert into t_refund_info(transid,amount,status,created_on,created_by) values(?,?,?,?,?)",array($transid,$refund,0,time(),$user['userid']));
		$rid=$this->db->insert_id();
		$this->db->query("insert into t_refund_order_item_link(refund_id,order_id,qty) values(?,?,?)",array($rid,$n_oid,$n_qty));
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
		$user=$this->auth(CALLCENTER_ROLE|FINANCE_ROLE|PNH_EXECUTIVE_ROLE);
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
						join pnh_deliveryhub i on i.id = e.hub_id
						
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
		
		$sql=$sql1." limit $pg ,10;";
		
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
										where a.invoice_no in ($invoices) and is_pnh = 1 and tt.is_active=1
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
													where (a.shipped=1 or a.outscanned=1) and is_pnh = 1  
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
														where (a.shipped=1 or a.outscanned=1) and is_pnh = 1  
														and a.invoice_no in ($invnos) and e.territory_id = ?  
														group by a.invoice_no 
														order by e.franchise_id ",array($terr_det['territory_id']));
			$manifesto_listbypages = suggest_grp_pages($outscan_res->result_array(),4,20,'franchise_id');
			
			
			echo '<div '.(($ttl_terr_listed!=$t_indx+1)?'style="page-break-after:always;"':'').'>
						<div>
							<h3>PayNearHome - Delivery Log Sheet  <span style="float:right">Date :___________________</span></h3> 
							<h3>Manifesto No : '.$sent_manifestoid.'
							<h3>Territory : '.ucwords($terr_det['territory_name']).' <span style="float:right"> Driver Name & No :'.$hndbyname.' Vehicle No:'.$vehicle_num.'</span></h3> 
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
							echo '<tr>
								<td align="center" style="height:45px;">'.$slno++.'</td>
								<td align="center">'.$fr_man_inv['invoice_no'].'</td>';
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
			
		$orders=$this->db->query("select quantity as qty,itemid,id from king_orders where id in ('".implode("','",$oids)."')")->result_array();
		
		$batch_id = $this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no=?",$p_invoice)->row()->batch_id;

		$proforma_inv_id = $this->db->query("select id from proforma_invoices where p_invoice_no=? ",$p_invoice)->row()->id;
		
		foreach($orders as $o)
		{
			$pls=$this->db->query("select qty,pl.product_id,p.mrp,p.brand_id from m_product_deal_link pl join m_product_info p on p.product_id=pl.product_id where itemid=?",$o['itemid'])->result_array();
			
			$pls2=$this->db->query("select pl.qty,p.product_id,p.mrp,p.brand_id 
						from products_group_orders pgo
						join king_orders o on o.id = pgo.order_id 
						join m_product_group_deal_link pl on pl.itemid=o.itemid 
						join m_product_info p on p.product_id=pgo.product_id 
						where pgo.order_id=?",$o['id'])->result_array();
								
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
				$reserv_stk_res = $this->db->query('select id,release_qty,extra_qty,stock_info_id,qty from t_reserved_batch_stock where batch_id = ? and p_invoice_no = ? and status = 1 and order_id = ? and product_id = ? ',array($batch_id,$p_invoice,$o['id'],$p['product_id']));
				if($reserv_stk_res->num_rows())
				{
					foreach($reserv_stk_res->result_array() as $reserv_stk_det)
					{
						$rqty = $reserv_stk_det['qty']-$reserv_stk_det['release_qty']+$reserv_stk_det['extra_qty'];
						$p_reserv_qty += $rqty;
						$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and stock_id = ? limit 1";
						$this->db->query($sql,array($rqty,$p['product_id'],$reserv_stk_det['stock_info_id']));
						$this->db->query("update t_reserved_batch_stock set status=3 where id = ? ",array($reserv_stk_det['id']));
					}
					$c_qty = $p_reserv_qty;
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
					
					$sql="update t_stock_info set available_qty=available_qty+? where product_id=? and mrp=? limit 1";
					$this->db->query($sql,array($p['qty']*$o['qty'],$p['product_id'],$p['mrp']));
					$c_qty = $p['qty']*$o['qty'];
				}
				
				$this->erpm->do_stock_log(1,$c_qty,$p['product_id'],$proforma_inv_id,false,true,true);
				
			}
		
		}
		
		$this->db->query("update king_orders set status=0 where id in ('".implode("','",$oids)."') and transid=?",$transid);
		$this->db->query("update proforma_invoices set invoice_status=0 where p_invoice_no=?",$p_invoice);
		$this->erpm->do_trans_changelog($transid,"Proforma Invoice no $p_invoice cancelled");
		$this->session->set_flashdata("erp_pop_info","Proforma Invoice cancelled");
		$bid=$this->db->query("select batch_id from shipment_batch_process_invoice_link where p_invoice_no=?",$p_invoice)->row()->batch_id;
		if($this->db->query("select count(1) as l from shipment_batch_process_invoice_link bi join proforma_invoices i on i.p_invoice_no=bi.p_invoice_no where bi.batch_id=? and bi.packed=0 and i.invoice_status=1",$bid)->row()->l==0)
			$this->db->query("update shipment_batch_process set status=2 where batch_id=? limit 1",$bid);
		redirect("admin/proforma_invoice/$p_invoice");
	}
	
	
	
	function proforma_invoice($p_invoice)
	{
		$user=$this->auth(INVOICE_PRINT_ROLE|ORDER_BATCH_PROCESS_ROLE);
		$data['batch']=$this->db->query("select * from shipment_batch_process_invoice_link where p_invoice_no=?",$p_invoice)->row_array();
		$data['invoice']=$this->db->query("select * from proforma_invoices where p_invoice_no=?",$p_invoice)->row_array();
		$data['orders']=$this->db->query("select o.id,i.id as itemid,i.name as product,o.quantity,o.transid from proforma_invoices p join king_orders o on o.id=p.order_id join king_dealitems i on i.id=o.itemid where p.p_invoice_no=?",$p_invoice)->result_array();
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

	function stock_procure_list()
	{
		$user=$this->auth(INVOICE_PRINT_ROLE);
		$data['prods']=$this->erpm->getprodproclistfortransids($_POST['tids']);
		$this->load->view("admin/body/product_proc_list_transids",$data);
	}
	
	function product_proc_list_for_batch($bid)
	{
		$user=$this->auth(INVOICE_PRINT_ROLE);
		$data['prods']=$this->erpm->getprodproclist($bid);
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
		$this->erpm->flash_msg("Acount statement corrected");
		if(!isset($_POST['internal']))
			redirect("admin/pnh_franchise/$fid");
	}
	
	function product($pid=false)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		$data['product']=$this->db->query("select ifnull(sum(s.available_qty),0) as stock,p.*,b.name as brand from m_product_info p left outer join t_stock_info s on s.product_id=p.product_id join king_brands b on b.id=p.brand_id where p.product_id=?",$pid)->row_array();
		$data['log']=$this->db->query("select u.name as username,l.*,pi.p_invoice_no,ci.invoice_no as c_invoice_no,i.invoice_no from t_stock_update_log l left outer join king_invoice i on i.id=l.invoice_id left outer join t_client_invoice_info ci on ci.invoice_id=l.corp_invoice_id left outer join proforma_invoices pi on pi.id=l.p_invoice_id left outer join king_admin u on u.id=l.created_by where l.product_id=? order by l.id desc",$pid)->result_array();
		$data['page']="viewproduct";
		$this->load->view("admin",$data);
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
						$n_price=$item['price']/$o_mrp*$n_mrp;
						$inp=array("itemid"=>$itemid,"old_mrp"=>$o_mrp,"new_mrp"=>$n_mrp,"old_price"=>$o_price,"new_price"=>$n_price,"created_by"=>$user['userid'],"created_on"=>time(),"reference_grn"=>0);
						$r=$this->db->insert("deal_price_changelog",$inp);
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
		}
		return $c;
	}
	
	
	function editproduct($pid=false)
	{
		$user=$this->auth(PRODUCT_MANAGER_ROLE);
		if($_POST){
			foreach(array('pname',"pdesc","psize","puom","pmrp","pvat","pcost","pbarcode","pisoffer","pissrc","pbrand","prackbin","pmoq","prorder","prqty","premarks","pissno") as $i)
				$inp[]=$this->input->post($i);
			$inp[]=$pid;
			
			
			$prod = array();
			$prod[] = array('product_id'=>$pid,'mrp'=>$_POST['pmrp']);
			
			$this->_update_product_mrp($prod);
			
			$this->db->query("update m_product_info set product_name=?,short_desc=?,size=?,uom=?,mrp=?,vat=?,purchase_cost=?,barcode=?,is_offer=?,is_sourceable=?,brand_id=?,default_rackbin_id=?,moq=?,reorder_level=?,reorder_qty=?,remarks=?,modified_on=now(),is_serial_required=? where product_id=? limit 1",$inp);
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
			foreach(array('pname',"pdesc","psize","puom","pmrp","pvat","pcost","pbarcode","pisoffer","pissrc","pbrand","prackbin","pmoq","prorder","prqty","premarks","pissno") as $i)
				$inp[]=$this->input->post($i);
			$this->db->query("insert into m_product_info(product_code,product_name,short_desc,size,uom,mrp,vat,purchase_cost,barcode,is_offer,is_sourceable,brand_id,default_rackbin_id,moq,reorder_level,reorder_qty,remarks,is_serial_required,created_on)
																					values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now())",$inp);
			$pid=$this->db->insert_id();
			$rackbin=0;$location=0;
			$raw_rackbin=$this->db->query("select l.location_id as default_location_id,l.id as default_rack_bin_id from m_rack_bin_brand_link b join m_rack_bin_info l on l.id=b.rack_bin_id where b.brandid=?",$this->input->post("pbrand"))->row_array();
			if(!empty($raw_rackbin))
			{
				$rackbin=$raw_rackbin['default_rack_bin_id'];
				$location=$raw_rackbin['default_location_id'];
			}
			$this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,available_qty,product_barcode) values(?,?,?,?,0,'')",array($pid,$location,$rackbin,$pmrp));
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
		$data['auser']=$this->db->query("select * from king_admin where id=?",$uid)->row_array();
		$data['roles']=$this->db->query("select * from user_access_roles order by user_role asc")->result_array();
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
		if($e==0 && $this->db->query("select 1 from pnh_member_info where pnh_member_id=?",$mid)->num_rows()==0 && $this->db->query("select 1 from pnh_m_allotted_mid where franchise_id=? and ? between mid_start and mid_end",array($fid,$mid))->num_rows()==0)
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
				
		 	foreach($pids as $i=>$iid)
		 	{
				$prod=$this->db->query("select i.*,d.publish,d.menuid,d.brandid,d.catid from king_dealitems i join king_deals d on d.dealid=i.dealid where i.is_pnh=1 and i.pnh_id=?",$iid)->row_array();
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
			
			if($fran['current_balance']<$d_total)
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
					
				$itemdet=$this->db->query("select i.id,i.orgprice as mrp,i.name from pnh_quotes_deal_link l join king_dealitems i on i.is_pnh=1 and i.pnh_id=l.pnh_id where l.id=?",$i)->row_array();
				
				$itemid = $itemdet['id'];
				$itemname = $itemdet['name'];
				$itemmrp = $itemdet['mrp'];
				
					
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
					$q_msg = "Price for {$itemname}\n Mrp : Rs {$itemmrp}, Landing Cost : Rs ".$final[$j];
					$this->erpm->pnh_sendsms($fmob,$q_msg,$fid);	
				}
				
				$this->db->query("update pnh_quotes_deal_link set final_price=?,status=1,price_updated_by=?,updated_on=?,is_notified=? where id=? limit 1",array($final[$j],$user['userid'],time(),$is_notified,$i));
				
			}
			$this->db->query("update pnh_quotes set updated_by=?,updated_on=? where quote_id=? limit 1",array($user['userid'],time(),$qid));
			$this->erpm->flash_msg("Order Quote updated");
			redirect("admin/pnh_quote/$qid");
		}
	}
	
	function pnh_quotes($fid=0,$s=false,$e=false,$sby='',$sord='')
	{
		$user=$this->auth(CALLCENTER_ROLE);
		$from=$to=0;
		if($s)
		{
			$from=strtotime($s);
			$to=strtotime("23:59:59 $e");
		}
		$sql="select q.*,u.name as updated_by,c.name as created_by,f.franchise_name,l.new_product from pnh_quotes q join pnh_m_franchise_info f on f.franchise_id=q.franchise_id left JOIN `pnh_quotes_deal_link` l ON l.quote_id=q.quote_id join king_admin c on c.id=q.created_by left outer join king_admin u on u.id=q.updated_by where 1 ";
		if($fid)
			$sql.=" and q.franchise_id=? ";
		if($from)
			$sql.=" and q.created_on between $from and $to ";
			
		if($sby == '')	
		{
			$sql.=" order by q.created_on desc,q.updated_on desc";
		}
		elseif($sby == 'fid' || $sby == 'status'){
			
			if($sby == 'fid')
				$sby = 'f.franchise_name';
			else if($sby == 'status') 	
				$sby = '';
				
			$sord = $sord=='a'?'asc':'desc'; 
			$sql.=" order by $sby $sord,q.created_on desc,q.updated_on desc";
		}
		
		$title="Franchise Requests";
		if($fid)
			$title.=" for '".$this->db->query("select franchise_name from pnh_m_franchise_info where franchise_id=?",$fid)->row()->franchise_name."'";
		if($from)
			$title.=" between $s and $e";
			
		
		$data['url']=site_url("admin/pnh_quotes");
			
			
		$data['st_d'] = $s?$s:0;
		$data['en_d'] = $e?$e:0;
		
		$data['pagetitle']=$title;
		$data['quotes']=$this->db->query($sql,$fid)->result_array();
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
		$fran=$this->db->query("select current_balance as balance,credit_limit as credit from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
		
		$fran_crdet = $this->erpm->get_fran_availcreditlimit($fid);
		$fran['balance'] = $fran_crdet[3];
		
		$fran['total_ord'] = $this->db->query("select count(transid) as t from king_transactions where franchise_id = ? ",$_POST['id'])->row()->t;
		
		$ttl_ords_res = $this->db->query("select init from king_transactions where franchise_id = ? order by id desc limit 1",$_POST['id']);
		if($ttl_ords_res->num_rows())
			$fran['last_ordon'] = date('d/m/Y',$ttl_ords_res->row()->init);
		else
			$fran['last_ordon'] = '---na---'; 
		$fran['total_mem'] = $this->db->query("select count(*) as t from pnh_member_info where franchise_id = ? ",$_POST['id'])->row()->t;
		
		
		echo json_encode($fran);
	}
	
	function pnh_jx_loadfranchisebyid()
	{
		$user=$this->auth();
		$fid=$_POST['fid'];
		$fran=$this->db->query("select a.*,town_name,territory_name from pnh_m_franchise_info a join pnh_m_territory_info b on a.territory_id = b.id join pnh_towns c on a.town_id = c.id where a.pnh_franchise_id=?",$fid)->row_array();
		$sec_q=array("What was your childhood nickname?","In what city were you born?","What is the name of the company of your first job?","In what year was your father born?","What was the name of your elementary / primary school?","What is your mother's maiden name?"," What is your oldest sibling's name?"," Who was your childhood hero?");
		if(empty($fran))
			die("<h3>No franchisee available for given id</h3>");
		if($fran['is_suspended']==1)
			die("<h3>This franchise account is suspended");
		echo "<h4><a target='_blank' loc='{$fran['town_name']},{$fran['territory_name']}' href='".site_url("admin/pnh_franchise/".$fran['franchise_id'])."'>{$fran['franchise_name']}</a></h4>";
		echo '<table border=1 cellpadding="5"><tr><th>Login Details</th><th>Authenticate</th><th>Details</th><th></th></tr>';
		echo "<tr><td>Login Mobile1 : <span class='ff_mob'>{$fran['login_mobile1']}</span><img src='".IMAGES_URL."phone.png' class='phone_small' onclick='makeacall(\"0{$fran['login_mobile1']}\")'></td><td>Security Question : ".($fran['security_question']=="-1"?$fran['security_custom_question']:$sec_q[$fran['security_question']])."  Answer : <b>{$fran['security_answer']}</b></td><td>FID : {$fran['pnh_franchise_id']}</td><td>Territory : ".$this->db->query("select territory_name as name from pnh_m_territory_info where id=?",$fran['territory_id'])->row()->name."</td></tr>";
		echo "<tr><td>Login Mobile2 : {$fran['login_mobile2']}<img src='".IMAGES_URL."phone.png' class='phone_small' onclick='makeacall(\"0{$fran['login_mobile2']}\")'></td><td>Security Question2 : ".($fran['security_question2']=="-1"?$fran['security_custom_question2']:$sec_q[$fran['security_question2']])."  Answer : <b>{$fran['security_answer2']}</b></td><td>Balance : Rs {$fran['current_balance']}</td><td>Town : ".$this->db->query("select town_name as name from pnh_towns where id=?",$fran['town_id'])->row()->name."</td></tr>";
		echo "<tr><td>Login Email : {$fran['email_id']}</td><td></td><td>Credit Limit : Rs {$fran['credit_limit']}</td></tr>";
		echo '</table>';
		echo "<div id='auth_cont'><input type='button' value='Authenticate' onclick='select_fran({$fran['franchise_id']})'></div>";
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
		$barcode=$_POST['barcode'];
		$ret=$this->db->query("select i.pnh_id as pid from m_product_info p join m_product_deal_link l on l.product_id=p.product_id join king_dealitems i on i.id=l.itemid and i.is_pnh=1 where p.barcode=?",$barcode)->row_array();
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
		$active_schemdiscount=$this->db->query("SELECT *,DATE_FORMAT((FROM_UNIXTIME(a.valid_from)),'%d/%m/%Y') as validfrom,DATE_FORMAT((FROM_UNIXTIME(a.valid_to)),'%d/%m/%Y') as validto,b.name AS brand_name,c.name AS cat_name,m.name AS menu_name FROM pnh_sch_discount_brands a JOIN `king_brands` b ON b.id=a.brandid LEFT JOIN `king_categories` c ON c.id=a.catid JOIN `pnh_menu`m ON m.id=a.menuid WHERE is_sch_enabled=1 AND sch_type=1 AND franchise_id=? and ? between a.valid_from and a.valid_to",array($fid,time()));
		$active_superscheme=$this->db->query("SELECT *,DATE_FORMAT((FROM_UNIXTIME(a.valid_from)),'%d/%m/%Y') as validfrom,DATE_FORMAT((FROM_UNIXTIME(a.valid_to)),'%d/%m/%Y') as validto,b.name AS brand_name,c.name AS cat_name,m.name AS menu_name FROM pnh_super_scheme a LEFT JOIN `king_brands` b ON b.id=a.brand_id LEFT JOIN `king_categories` c ON c.id=a.cat_id JOIN `pnh_menu`m ON m.id=a.menu_id WHERE  is_active=1 AND franchise_id=? and ? between valid_from and valid_to",array($fid,time()));
		$fran_menu=$this->db->query("SELECT m.name AS menu  FROM pnh_franchise_menu_link a JOIN pnh_menu m ON m.id= a.menuid WHERE a.status=1 AND fid=?",$fid);
		
		
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
			unset($prod['is_combo']);
		}
		echo json_encode($prod);
	}
	
	function pnh_expire_scheme_discount($id)
	{
		$user=$this->auth(true);
		$this->db->query("update pnh_sch_discount_brands set is_sch_enabled=0,modified_on=? where id=?",array(time(),$id));
	
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
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	function pnh_expire_superscheme($id)
	{
		$user=$this->auth(true);
		$this->db->query("update pnh_super_scheme set is_active=0 where id=$id" );
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
		$inp=array("special_margin"=>$special_margin,"itemid"=>$itemid,"from"=>$from,"to"=>$to,"created_on"=>time(),"created_by"=>$user['userid'],"is_active"=>'1');
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
		$user=$this->auth(PNH_EXECUTIVE_ROLE);
		if($_POST)
			$this->erpm->do_pnh_updatefranchise($fid);
		$data['fran']=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
		$data['selected_menu']=@$this->db->query("select group_concat(menuid) as m_ids from pnh_franchise_menu_link where status = 1 and fid=?",$fid)->row()->m_ids;
		$data['fran_menu']=$this->db->query("select id,name from pnh_menu order by name asc")->result_array();
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
		$data['fran_menu']=$this->db->query("select id,name from pnh_menu order by name asc")->result_array();
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
		
		 
		if($fid)
		{
			$deal_list = $this->db->query("SELECT i.name,i.pnh_id,i.orgprice AS mrp,i.price,i.store_price,d.menuid
									 FROM king_dealitems i
									 JOIN king_deals d ON d.dealid=i.dealid
									 JOIN `pnh_franchise_menu_link` m ON m.menuid=d.menuid
									  WHERE i.is_pnh=1 AND i.name LIKE ? AND m.status=1 AND fid=?",array("%$q%",$fid))->result_array();
			
		}else
		{
			$deal_list = $this->db->query("select i.name,i.pnh_id,i.orgprice as mrp,i.price,i.store_price from king_dealitems i where i.is_pnh=1 and i.name like ?","%$q%")->result_array();
		}
		
		foreach($deal_list as $d)
			echo "<a href=\"javascript:void(0)\" onclick='add_deal_callb(\"{$d['name']}\",\"{$d['pnh_id']}\",\"{$d['mrp']}\",\"{$d['price']}\",\"{$d['store_price']}\")'>{$d['name']}</a>";
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
	
	function pnh_receiptsbytype($type=0,$st_date=false,$en_date=false,$pg=0)
	{	
		$user=$this->auth(FINANCE_ROLE);
		if($st_date!=false && $en_date!=false && (strtotime($st_date)<=0 || strtotime($en_date)<=0))
			show_404();
		
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
			if($_POST['type'] == 'new')
			{
				$this->erpm->do_pnh_download_stat_new(array($fid),$_POST['from'],$_POST['to']);
			}else
			{
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
		$data['fran_menu']=$this->db->query("SELECT m.id,m.name AS menu FROM `pnh_franchise_menu_link`a JOIN pnh_m_franchise_info b ON b.franchise_id=a.fid JOIN pnh_menu m ON m.id=a.menuid WHERE b.is_suspended=0  and a.status=1 AND b.franchise_id=?",$fid)->result_array();
		$data['receipts']=$this->db->query("select r.*,m.name AS modifiedby,a.name as admin,act.name as act_by,d.remarks AS submittedremarks,sub.name AS submittedby,d.submitted_on,can.cancelled_on,can.cancel_reason from pnh_t_receipt_info r LEFT OUTER JOIN `pnh_m_deposited_receipts`can ON can.receipt_id=r.receipt_id left outer join king_admin a on a.id=r.created_by left outer join king_admin act on act.id=r.activated_by LEFT OUTER JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id LEFT OUTER JOIN king_admin sub ON sub.id=d.submitted_by LEFT OUTER JOIN king_admin m ON m.id=r.modified_by where franchise_id=? group by r.receipt_id",$fid)->result_array();
		$data['pending_receipts']=$this->db->query("SELECT r.*,m.name AS modifiedby,f.franchise_name,a.name AS admin FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin m ON m.id=r.modified_by WHERE r.status=0 AND r.is_active=1 and is_submitted=0 and r.status=0 and r.franchise_id=?  ORDER BY instrument_date asc",$fid)->result_array();
		$data['pending_ttlvalue']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by WHERE r.status=0 AND r.is_active=1  AND is_submitted=0 and r.status=0 and r.franchise_id=?  ORDER BY instrument_date asc",$fid)->row_array();

		$data['processed_receipts']=$this->db->query("SELECT r.*,b.bank_name AS submit_bankname,s.name AS submittedby,a.name AS admin,f.franchise_name,d.remarks AS submittedremarks,DATE(d.submitted_on) AS submitted_on  FROM pnh_t_receipt_info r LEFT JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id LEFT JOIN `pnh_m_bank_info` b ON b.id=d.bank_id LEFT JOIN king_admin s ON s.id=d.submitted_by JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id left outer join king_admin a on a.id=r.created_by  WHERE r.is_submitted=1 AND r.status=0  and r.is_active=1 and r.franchise_id=?  group by r.receipt_id order by d.submitted_on desc",$fid)->result_array();
		$data['processed_ttlvalue']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r LEFT JOIN `pnh_m_deposited_receipts`d ON d.receipt_id=r.receipt_id LEFT JOIN `pnh_m_bank_info` b ON b.id=d.bank_id LEFT JOIN king_admin s ON s.id=d.submitted_by JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id left outer join king_admin a on a.id=r.created_by WHERE  r.is_submitted=1 AND r.status=0  and r.is_active=1 and r.franchise_id=?  order by d.submitted_on desc",$fid)->row_array();

		$data['realized_receipts']=$this->db->query("SELECT r.*,f.franchise_name,a.name AS admin,d.username AS activated_by FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by WHERE r.status=1 AND r.is_active=1 AND (is_submitted=1 or r.activated_on!=0) and r.is_active=1 and r.franchise_id=? group by r.receipt_id ORDER BY activated_on desc",$fid)->result_array();
		$data['realized_ttlvalue']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by WHERE r.status=1 AND r.is_active=1 AND (is_submitted=1 or r.activated_on!=0) and r.is_active=1 and r.franchise_id=? ORDER BY activated_on desc",$fid)->row_array();
		
		$data['cancelled_receipts']=$this->db->query("SELECT r.*,f.franchise_name,a.name AS admin,d.username AS activated_by ,c.cancel_reason,c.cancelled_on FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id left JOIN `pnh_m_deposited_receipts`c ON c.receipt_id=r.receipt_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by WHERE r.status=2 AND r.is_active=1 AND r.is_active=1 AND  r.franchise_id=? group by r.receipt_id  ORDER BY cancelled_on DESC",$fid)->result_array();
		$data['cancelled_ttlvalue']=$this->db->query("SELECT sum(r.receipt_amount) as total FROM pnh_t_receipt_info r JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id left JOIN `pnh_m_deposited_receipts`c ON c.receipt_id=r.receipt_id LEFT OUTER JOIN king_admin a ON a.id=r.created_by LEFT OUTER JOIN king_admin d ON d.id=r.activated_by WHERE r.status in (2,3) AND r.is_active=1 AND r.is_active=1 AND  r.franchise_id=? ORDER BY cancelled_on DESC",$fid)->row_array();
		
			
		$data['account_stat']=$this->db->query("SELECT a.type,a.amount,a.balance_after,a.desc,a.action_for,a.created_on,f.franchise_name FROM `pnh_franchise_account_stat`a JOIN `pnh_m_franchise_info` f ON f.franchise_id=a.franchise_id WHERE a.franchise_id=? order by created_on desc",$fid)->result_array();
		$data['devices']=$this->db->query("select dm.created_on,di.id,di.device_sl_no,d.device_name from pnh_m_device_info di join pnh_m_device_type d on d.id=di.device_type_id join pnh_t_device_movement_info dm on dm.device_id=di.id where di.issued_to=?",$fid)->result_array();
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
	
	function pnh_bulk_sch_discount()
	{
		$user=$this->auth(true);
		if($_POST)
		{
			foreach(array("discount","start","end","reason","menu","brand","cat","fids") as $i)
				$$i=$this->input->post($i);
			if(empty($fids))
				show_error("No Franchises selected");
			$start=strtotime($start);
			$end=strtotime($end." 23:59:59");
			foreach($fids as $fid)
			{
				$inp=array("franchise_id"=>$fid,"menuid"=>$menu,"catid"=>$cat,"brandid"=>$brand,"sch_discount"=>$discount,"sch_discount_start"=>$start,"sch_discount_end"=>$end,'reason'=>$reason,"created_by"=>$user['userid'],"created_on"=>time());
				$this->db->insert("pnh_sch_discount_track",$inp);
				if($brand==0 && $cat==0)
					$this->db->query("update pnh_m_franchise_info set sch_discount=?,sch_discount_start=?,sch_discount_end=? where franchise_id=?",array($discount,$start,$end,$fid));
				else 
				{
					$inp=array("franchise_id"=>$fid,"menuid"=>$menu,"discount"=>$discount,"valid_from"=>$start,"valid_to"=>$end,"brandid"=>$brand,"created_on"=>time(),"created_by"=>$user['userid'],"catid"=>$cat);
					$this->db->insert("pnh_sch_discount_brands",$inp);
				}
			}
			$this->erpm->flash_msg("Scheme discount added");
			redirect("admin/pnh_bulk_sch_discount");
		}
		$data['page']="pnh_bulk_sch_discount";
		$this->load->view("admin",$data);
	}
	
	function pnh_give_sch_discount($fid)
	{
		$user=$this->auth(true);
		foreach(array("discount","start","menu","end","reason","brand","cat") as $i)
			$$i=$this->input->post($i);
		$start=strtotime($start);
		$end=strtotime($end." 23:59:59");
		$target_value=$this->input->post('ttl_sales_value');
		$credit=$this->input->post('credit');
		
		$this->db->query("update pnh_franchise_menu_link set sch_discount=?,sch_discount_start=?,sch_discount_end=?,is_sch_enabled=1 where fid=? and menuid=?",array($discount,$start,$end,$fid,$menu));
		$inp=array("franchise_id"=>$fid,"sch_menu"=>$menu,"catid"=>$cat,"brandid"=>$brand,"sch_discount"=>$discount,"sch_discount_start"=>$start,"sch_discount_end"=>$end,'reason'=>$reason,"created_by"=>$user['userid'],"created_on"=>time());
		if(empty($target_value))
			$inp['sch_type']='1';
		else
			$inp['sch_type']='2';
		$this->db->insert("pnh_sch_discount_track",$inp);
	
 	/* 	if($brand==0 && $cat==0)
		{
			$this->db->query("update pnh_m_franchise_info set sch_discount=?,sch_discount_start=?,sch_discount_end=? where franchise_id=?",array($discount,$start,$end,$fid));
		} */
			$inp=array("franchise_id"=>$fid,"menuid"=>$menu,"discount"=>$discount,"valid_from"=>$start,"valid_to"=>$end,"brandid"=>$brand,"created_on"=>time(),"created_by"=>$user['userid'],"catid"=>$cat,"is_sch_enabled"=>'1',"sch_type"=>1);
			/*if(empty($target_value))
			{
				$inp['sch_type']='1';
			}
			else
			{
				$inp['sch_type']='2';
			}*/
			$this->db->insert("pnh_sch_discount_brands",$inp);
		
			$sch_id=$this->db->insert_id();
			
		/*	if(!empty($target_value))
			{
				
				//calculation of calender month dates for super scheme
				$d=date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y')))));
				$super_schend= strtotime($d);
				$inp=array("franchise_id"=>$fid,"schme_discount_id"=>$sch_id,"menu_id"=>$menu,"cat_id"=>$cat,"brand_id"=>$brand,"target_value"=>$target_value,"credit_prc"=>$credit,"valid_from"=>$start,"valid_to"=>$super_schend,"created_by"=>$user['userid'],"created_on"=>time(),"is_active"=>1);
				$this->db->insert("pnh_super_scheme",$inp);
				
			}*/
		$this->erpm->flash_msg("Scheme discount added");
		redirect("admin/pnh_franchise/$fid#sch_hist");
	}
	
	function pnh_give_super_sch($fid)
	{
		$user=$this->auth(true);
		foreach(array("credit","ttl_sales_value","super_schstart","super_schmenu","super_schend","reason","brand","cat") as $i)
			$$i=$this->input->post($i);
		$d=date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y')))));
		$super_schstart=strtotime($super_schstart);
		$superschend= strtotime($d);
		$inp=array("franchise_id"=>$fid,"menu_id"=>$super_schmenu,"cat_id"=>$cat,"brand_id"=>$brand,"target_value"=>$ttl_sales_value,"credit_prc"=>$credit,"valid_from"=>$super_schstart,"valid_to"=>$superschend,"created_by"=>$user['userid'],"created_on"=>time(),"is_active"=>1);
		$this->db->insert("pnh_super_scheme",$inp);
		$this->erpm->flash_msg("Super scheme added");
		redirect("admin/pnh_franchise/$fid#sch_hist");
	}
	
	
	
	function jx_check_schemexist($fid='')
	{
		 
		
		//error_reporting(E_ALL&~E_NOTICE);
		//ini_set('display_errors',1); 
		
		$output = array();
		$output['status'] = 'success';
		foreach(array("discount","start","menu","end","reason","brand","cat") as $i)
			$$i=$this->input->post($i);
		$target_value=$this->input->post('ttl_sales_value');
		$brand_name = '';
		
		if($brand)
			$brand_name = $this->db->query("select name from king_brands where id = ? ",$brand)->row()->name;
		
		$cat_name = '';
		if($cat)
			$cat_name = @$this->db->query("select name from king_categories where id = ? ",$cat)->row()->name;
		
		$brand_name = $brand_name?'"'.$brand_name.'"':'';
		$cat_name = $cat_name?'"'.$cat_name.'"':'';
		
		if($brand_name && $cat_name)
			$cat_name = ','.$cat_name;
		
		if($fid)
		{
			$output['status'] = 'success';
			$fran_sch=$this->db->query('select * from pnh_sch_discount_brands where franchise_id=? and menuid like ? and brandid like ? and catid like ? and is_sch_enabled=1 ',array($fid,'%'.$menu.'%','%'.$brand.'%','%'.$cat.'%'))->result_array();
			if(empty($fran_sch) && !$target_value)
			{
				$fran_sch=$this->db->query('select * from pnh_sch_discount_brands where franchise_id=? and menuid like ? and brandid like ? and catid=0 ',array($fid,'%'.$menu.'%','%'.$brand.'%'))->result_array();
				
				foreach($fran_sch as $fransch)
				{
					
					
					if($fransch['valid_from']<time() && $fransch['valid_to']>time() && $fransch['is_sch_enabled']==1)
					{
						$output['status'] = 'error';
						$output['message'] = 'Scheme discount for '.$brand_name.' '.$cat_name.' already exists ';
					}else
					{
						$output['status'] = 'success';
					}	
				}
			}
			
			if($target_value)
			{
				
				$super_scheme=$this->db->query("select * from pnh_super_scheme where  franchise_id like ? and menu_id like ? and brand_id like ? and cat_id like ? and is_active=1 ",array($fid,'%'.$menu.'%','%'.$brand.'%','%'.$cat.'%'))->result_array();
				if(empty($super_scheme))
					$super_scheme=$this->db->query("select * from pnh_super_scheme where  franchise_id like ? and menu_id like ? and brand_id like ? and cat_id=0 and is_active=1 ",array($fid,'%'.$menu.'%','%'.$brand.'%','%'))->result_array();
				foreach($super_scheme as $supersch)
				{
					if($supersch['valid_from']<time() && $supersch['valid_to']>time() && $supersch['is_active']==1)
					{
						$output['status'] = 'error';
						$output['message'] = 'Super Scheme for '.$brand_name.' '.$cat_name.' already exists ';
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
						$o_price=$item['price'];$o_mrp=$item['orgprice'];
						$n_mrp=$this->db->query("select ifnull(sum(p.mrp*l.qty),0) as mrp from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$itemid)->row()->mrp+$this->db->query("select ifnull(sum((select avg(mrp) from m_product_group_deal_link l join products_group_pids pg on pg.group_id=l.group_id join m_product_info p on p.product_id=pg.product_id where l.itemid=$itemid)*(select qty from m_product_group_deal_link where itemid=$itemid)),0) as mrp")->row()->mrp;
						$n_price=$item['price']/$o_mrp*$n_mrp;
						$inp=array("itemid"=>$itemid,"old_mrp"=>$o_mrp,"new_mrp"=>$n_mrp,"old_price"=>$o_price,"new_price"=>$n_price,"created_by"=>$user['userid'],"created_on"=>time(),"reference_grn"=>0);
						$r=$this->db->insert("deal_price_changelog",$inp);
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

		function pnh_pub_deal($itemid,$pub)
		{
		$user=$this->auth(DEAL_MANAGER_ROLE);
		$this->db->query("update king_deals set publish=? where dealid=?",array(!$pub,$this->db->query("select dealid from king_dealitems where id=?",$itemid)->row()->dealid));
		$this->session->set_flashdata("erp_pop_info","Deal status changed");
		redirect($_SERVER['HTTP_REFERER']);
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
		$mid=$this->input->post("mid");
		if(strlen($mid)!=8 || $mid{0}!=2)
			die("Invalid MID $mid");
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
		$sql="select i.gender_attr,d.catid,d.brandid,d.tagline,i.nyp_price,i.pnh_id,i.id,b.name as brand,c.name as category,
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
		
		$data['pnh_Deal_upd_log']=$pnh_Deal_upd_log;
		$data['page']="pnh_deal";
		$this->load->view("admin",$data);
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
			foreach(array("fids","send_to","msg","type","sel_itemid","sel_itemname","sel_itemmrp","sel_itemprice") as $i)
				$$i=$this->input->post($i);
			if(empty($fids))
				show_error("No franchises were selected");
			else
			{
				$emp_list=$this->db->query("SELECT a.employee_id,b.name,t.town_name,r.territory_name ,f.franchise_name,job_title2,b.contact_no
							FROM m_town_territory_link a
							JOIN m_employee_info b ON b.employee_id=a.employee_id
							JOIN `pnh_m_franchise_info` f ON f.territory_id=a.territory_id
							JOIN `pnh_m_territory_info`r ON r.id=f.territory_id
							LEFT JOIN pnh_towns t ON t.id=a.town_id
							WHERE f.is_suspended=0 AND a.is_active=1 AND b.is_suspended=0 AND b.job_title2 in (4,5) AND f.franchise_id in (".implode(',',$fids).") 
							GROUP BY a.employee_id")->result_array();
				
				// send deal promo
				if($type == 2)
				{
					$prodname=$this->db->query("select name from king_dealitems where pnh_id=?",$sel_itemid)->row()->name;
					
					$msg = "$prodname,PNHID:".$sel_itemid." MRP:Rs".$sel_itemmrp." Price:Rs".$sel_itemprice;
					
					
				}
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
						foreach($emp_contact as $emp_ph)
						{
							$this->erpm->pnh_sendsms($emp_ph,"Dear $emp_name, Offer Of The Day $msg ");
							$this->db->query("insert into pnh_employee_grpsms_log (emp_id,contact_no,type,grp_msg,created_on)values(?,?,5,?,now())",array($emp_id,$emp_ph,"Dear $emp_name, Offer Of The Day $msg "));
						}
					}
				}
				if(empty($msg))
					show_error("No message to send");
				foreach($this->db->query("select login_mobile1,franchise_id,login_mobile2,franchise_name from pnh_m_franchise_info where franchise_id in ('".implode("','",$fids)."')")->result_array() as $f)
				{
					$f_name=$f['franchise_name'];
					$f_id=$f['franchise_id'];
					$f_mob1=$f['login_mobile1'];
					$f_mob2=$f['login_mobile2'];
					
					//send Offer of the day sms to Franchisee
					$this->erpm->pnh_sendsms($f_mob1,"Dear $f_name, Offer Of the day $msg",$f_id,0,'Offer');
					if($send_to==1 && !empty($f_mob2))
						$this->erpm->pnh_sendsms($f_mob2,"Dear $f_name, Offer Of the day $msg",$f_id,0,'Offer');
				}
				$this->erpm->flash_msg("SMS sent");
				redirect("admin/pnh_sms_campaign");
			}
		}
		$data['frans']=$this->erpm->pnh_getfranchises();
		$data['page']="pnh_sms_campaign";
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
	
	function pnh_unsuspend_fran($fid)
	{
		$user=$this->auth(true);
		$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
		if(empty($fran))
			show_error("No franchise found");
		$this->db->query("update pnh_m_franchise_info set is_suspended=0,suspended_on=".time().",suspended_by={$user['userid']} where franchise_id=? limit 1",$fid);
		$this->erpm->flash_msg("Franchise unsuspended");
		$this->erpm->send_admin_note("Franchise account : {$fran['franchise_name']} ({$fran['pnh_franchise_id']}) was unsuspended on ".date("g:ia d/m/y")." by {$user['username']}","Franchise account unsuspension");
		redirect("admin/pnh_franchise/$fid");
	}
	
	function pnh_suspend_fran($fid)
	{
		$user=$this->auth(true);
		$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
		if(empty($fran))
			show_error("No franchise found");
		$this->db->query("update pnh_m_franchise_info set is_suspended=1,suspended_on=".time().",suspended_by={$user['userid']} where franchise_id=? limit 1",$fid);
		$this->erpm->flash_msg("Franchise suspended");
		$this->erpm->send_admin_note("Franchise account : {$fran['franchise_name']} ({$fran['pnh_franchise_id']}) was suspended on ".date("g:ia d/m/y")." by {$user['username']}","Franchise account suspension");
		redirect("admin/pnh_franchise/$fid");
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
		$user=$this->auth(CALLCENTER_ROLE,true);
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
		$paf