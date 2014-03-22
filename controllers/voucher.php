<?php
class Voucher extends Controller {
	
	function __construct()
	{
		parent::__construct();	
		$this->task_status = $this->config->item('task_status');
		$this->db->query("set session group_concat_max_len=5000000;");
		$this->task_for=$this->config->item('task_for');
		$this->load->library('pagination');
	}
	
	//------------couponmodule---------------
	/**
	 * function for config the pnh franchise prepaid menu
	 */
	function pnh_prepaid_menus()
	{
		$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$data['pnh_menu_list']=$this->db->query("select a.*,b.is_active from pnh_menu a left join pnh_prepaid_menu_config b on b.menu_id=a.id where a.status=1 order by a.name")->result_array();
		$data['page']='pnh_config_prepaid_menus';
		$this->load->view('admin',$data);
	}
	
	function do_config_prepaid_menu()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$menu_ids=$this->input->post('is_prepaid');
	
		$param1=array();
		$param1['is_active']=0;
		$param1['modified_by']=$user['userid'];
		$param1['modified_on']=cur_datetime();
		$this->db->update("pnh_prepaid_menu_config",$param1);
	
		if($menu_ids)
		{
			foreach($menu_ids as $menu)
			{
				$already=$this->db->query("select count(*) as ttl from pnh_prepaid_menu_config where menu_id=?",$menu)->row()->ttl;
	
				if($already)
				{
					$this->db->query("update pnh_prepaid_menu_config set is_active=1 where menu_id=?", $menu);
				}else{
					$param=array();
					$param['menu_id']=$menu;
					$param['created_by']=$user['userid'];
					$param['created_on']=cur_datetime();
					$this->db->insert('pnh_prepaid_menu_config',$param);
				}
			}
		}
	
		$this->session->set_flashdata("erp_pop_info","selectred menus configured for prepaid");
		redirect('admin/pnh_prepaid_menus');
	}
	
	/**
	 * function for manage the coupons
	 */
	function pnh_manage_vouchers($pg=0)
	{
		$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$coupon_details=array();
		$limit=5;
	
		$sql="select a.created_on,sum(b.denomination) as value,c.name,a.group_code
					from pnh_t_voucher_details a
					join pnh_m_voucher b on b.voucher_id=a.voucher_id
					join king_admin c on c.id=a.created_by
					group by group_code
					order by created_on desc limit $pg , $limit";
	
		$coupon_details['coupons_list']=$this->db->query($sql)->result_array();
		$coupon_details['total_coupons']=$this->db->query("select count(*) as ttl from pnh_t_voucher_details")->row()->ttl;
		$coupon_details['total_value']=$this->db->query("select ifnull(sum(value),0) as ttl_val from pnh_t_voucher_details")->row()->ttl_val;
		$coupon_details['total_alloted']=$this->db->query("select count(*) as ttl_alloted from pnh_t_voucher_details where status=?",1)->row()->ttl_alloted;
		$coupon_details['total_assigned']=$this->db->query("select count(*) as ttl_assigned from pnh_t_voucher_details where status=?",2)->row()->ttl_assigned;
		$coupon_details['total_records']=count($this->db->query("select * from  pnh_t_voucher_details group by group_code ")->result_array());
		//pagination end
		$config['base_url'] = site_url("admin/pnh_manage_vouchers");
		$config['total_rows'] = $coupon_details['total_records'];
		$config['per_page'] = $limit;
		$config['uri_segment'] = 3;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$coupon_details['pagination'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		//pagination end
		$data['coupon_details']=$coupon_details;
		$data['page']='pnh_voucher_details';
		$this->load->view("admin",$data);
	}
	
	/**
	 * function for create voucher form page
	 */
	function pnh_create_voucher()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$data['vouchers_list']=$this->db->query("select * from pnh_m_voucher order by denomination asc")->result_array();
		$data['page']='pnh_create_voucher';
		$this->load->view('admin',$data);
	}
	/**
	 * function for add a new coupons
	 */
	function pnh_add_voucher()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
	
		if(!$_POST)
			die();
	
		$voucher_values=$this->input->post('denomination');
		$voucher_qty=$this->input->post("require_qty");
		$voucher_ids=$this->input->post("voucher_id");
		
		//generate the group code
		$pre_grp_code=$this->db->query("select ifnull(max(group_code),0) as grp_code from pnh_t_voucher_details;")->row()->grp_code ;
		
		if(!$pre_grp_code)
			$pre_grp_code=1;
		else
			$pre_grp_code+=1;
	
		$total_vouchers_value=0;
		$sub_total=0;
		foreach($voucher_ids as $i=>$vid)
		{
			$require_qty=$voucher_qty[$i];
			$voucher_val=$voucher_values[$i];
			$total_vouchers_value=$total_vouchers_value+($voucher_val*$require_qty);
			if($require_qty==0 || $require_qty=='')
				continue;
			
			//generate voucher seirel number
			$prev_voucher_sino=$this->db->query("select ifnull(max(voucher_serial_no),0) as voucher_serial_no from pnh_t_voucher_details;")->row()->voucher_serial_no;
			
			if(!$prev_voucher_sino)
				$prev_voucher_sino=10000;
			
			for($v=1;$v<=$require_qty;$v++)
			{
				$param=array();
				$param['voucher_id']=$vid;
				$param['group_code']=$pre_grp_code;
				$param['voucher_serial_no']=$prev_voucher_sino+$v;
				$param['voucher_code']=$this->p_gen_voucher_code(13);
				$param['value']=$voucher_val;
				$param['status']=0;
				$param['created_on']=cur_datetime();
				$param['created_by']=$user['userid'];
				$this->db->insert('pnh_t_voucher_details',$param);
			}
		}
		
		$this->session->set_flashdata("erp_pop_info",'Rs '.$total_vouchers_value." value Vouchers are created");
		redirect($_SERVER['HTTP_REFERER']);
	}
	
	/**
		* function for generate a  coupon code
		* @param unknown_type $len
		* @return Ambigous <string, number>
	*/
	function p_gen_voucher_code($len)
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$st="";
		for($i=0;$i<$len;$i++)
			$st.=rand(1,9);
		return $st;
	}
	
	/**
	 * function for get the fresh vouchers
	 */
	function jx_get_fresh_vouchers_denomination()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		
		$output=array();
		$output['fresh_vouchers']=$this->db->query("select b.voucher_id,b.denomination,count(*) as ttl from pnh_t_voucher_details a join pnh_m_voucher b on b.voucher_id=a.voucher_id where a.status=0 group by b.voucher_id;")->result_array();
		
		echo json_encode($output);
	}
	
	/**
	 * function get the voucher by group
	 */
	function jx_get_vouchers_by_group()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$group_id=$this->input->post('group_id');
		$output=array();
		$output['vouchers_list']=$this->db->query("select a.*,b.denomination,b.voucher_name from pnh_t_voucher_details a join pnh_m_voucher b on a.voucher_id=b.voucher_id where a.group_code=? order by b.denomination asc",$group_id)->result_array();
		echo json_encode($output);
	}
	
	/**
	 * function for download the vouchers by group
	 */
	function pnh_download_vouchers()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$group_id=$this->input->post('group_id');
		
		$this->load->plugin('csv_logger');
		$csv_obj=new csv_logger_pi();
		$csv_obj->head(array('Si','Voucher name','Denomination Value','Voucher serial no','Voucher code'));
		
		$voucher_list=$this->db->query("select a.*,b.voucher_name,b.denomination from pnh_t_voucher_details a join pnh_m_voucher b on b.voucher_id=a.voucher_id where group_code=? order by b.denomination",$group_id)->result_array();
		
		foreach($voucher_list as $i=>$voucher)
		{
			$csv_obj->push(array(($i+1),$voucher['voucher_name'],$voucher['denomination'],$voucher['voucher_serial_no'],$voucher['voucher_code']));
		}
		
		$csv_obj->download('vouchers_list');
	}
	/**
	* function for create coupon book
	*/
	function pnh_create_book_template()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$data['voucher_det']=$this->db->query("select * from pnh_m_voucher order by denomination")->result_array();
		$data['menu_list']=$this->db->query("select * from pnh_menu where status=1")->result_array();
		$data['page']='pnh_create_book_template';
		$this->load->view('admin',$data);
	}
	
	/**
	* function for process of create coupon book
	*/
	function pnh_process_create_book_template()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		if(!$_POST)
			die();
		
		$this->load->library('form_validation');
		$this->form_validation->set_rules('book_name','Book Name','required');
		$this->form_validation->set_rules('book_value','Book value','required|integer|callback__validate_denomination_link');
		$need_qty=$this->input->post('need_qty');
		
		
		if($need_qty)
		foreach($need_qty as $i=>$qty)
			$this->form_validation->set_rules('need_qty['.$i.']','qty '.($i+1),'required|integer');
		
		if($this->form_validation->run() == false)
		{
			$this->pnh_create_book_template();
		}
		else
		{
			$book_name=$this->input->post('book_name');
			$book_value=$this->input->post('book_value');
			$need_qty=$this->input->post('need_qty');
			$coupon_value=$this->input->post('coupon_value');
			$voucher_ids=$this->input->post('voucher_id');
			$products=$this->input->post('pid');
			$products=implode(',',$products);
			$menus=implode(',',$this->input->post("menu_list"));
			if(empty($menus))
				show_error("Please select menu");
				
			$param=array();
			$param['book_type_name']=$book_name;
			$param['value']=$book_value;
			$param['product_id']=$products;
			$param['created_by']=$user['userid'];
			$param['created_on']=cur_datetime();
			$param['menu_ids']=$menus;
	
			$this->db->insert('pnh_m_book_template',$param);
			$template_id=$this->db->insert_id();
			
			foreach($need_qty as $i=>$qty)
			{
				if($qty*1==0)
					continue;
				
				$voucher_id=$voucher_ids[$i];
				$param1=array();
				$param1['book_template_id']=$template_id;
				$param1['voucher_id']=$voucher_id;
				$param1['no_of_voucher']=$qty;
				$param1['created_by']=$user['userid'];
				$param1['created_on']=cur_datetime();
				$this->db->insert("pnh_m_book_template_voucher_link",$param1);
			}
			$this->session->set_flashdata("erp_pop_info",$book_name." created for value of Rs.".formatInIndianStyle($book_value));
			redirect('admin/pnh_book_template');
		}
	
	}
	
	/**
	* function for validate denomination link
	*/
	function _validate_denomination_link($str)
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$book_value=$this->input->post('book_value');
		$need_qty=$this->input->post('need_qty');
		$coupon_value=$this->input->post('coupon_value');
		$template_name=trim($this->input->post('book_name'));
		$products=$this->input->post('pid');
		
		if(count($products) > 1)
		{
			$this->form_validation->set_message('_validate_denomination_link','Only one product able to link');
			return false;
		}
		
		$check_tempname=$this->db->query("select count(*) as ttl from pnh_m_book_template where trim(book_type_name) like ?",'%'.$template_name.'%')->row()->ttl;
		
		if($check_tempname)
		{
			$this->form_validation->set_message('_validate_denomination_link','Book template name already exist');
			return false;
		}
		
		$check_qty_val=0;
		foreach($need_qty as $i=>$qty)
		{
			if($qty!=0)
				$check_qty_val=1;
		}
	
		if(!$check_qty_val)
		{
			$this->form_validation->set_message('_validate_denomination_link','Qty must require');
			return false;
		}
	
	
		$total_coupon_val=0;
		foreach($need_qty as $i=>$qty)
		{
			$coupon_val=$coupon_value[$i];
			$total_coupon_val+=($coupon_val*1)*($qty*1);
		}
	
	
		if($total_coupon_val > $book_value)
		{
			$this->form_validation->set_message('_validate_denomination_link','Denomination total grater then book value');
			return false;
		}else if($total_coupon_val < $book_value){
				$this->form_validation->set_message('_validate_denomination_link','Denomination total lower then book value');
					return false;
		}else if($total_coupon_val==0)
		{
			$this->form_validation->set_message('_validate_denomination_link','Please currect denomination configuration');
			return false;
		}
		return true;
	}
	
	/**
	* function for pnh coupon template
	*/
	function pnh_book_template($pg=0)
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$template_details=array();
		$limit=5;
	
		$sql="select a.*,b.username from pnh_m_book_template a
					join king_admin b on b.id=a.created_by
					order by created_on desc limit $pg , $limit";
	
		$template_details['template_list']=$this->db->query($sql)->result_array();
		$template_details['total_template']=$this->db->query("select count(*) as ttl from pnh_m_book_template")->row()->ttl;
	
		//pagination end
		$config['base_url'] = site_url("admin/pnh_book_template");
		$config['total_rows'] = $template_details['total_template'];
		$config['per_page'] = $limit;
		$config['uri_segment'] = 3;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$template_details['pagination'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		//pagination end
		$data['template_details']=$template_details;
		$data['page']='pnh_book_template';
		$this->load->view("admin",$data);
	}
	
	/**
	 * function for manage the voucher books
	 */
	function pnh_voucher_book($src=0,$pg=0)
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$limit=10;
		$cond='';
		$param=array();
		
		if($src)
		{
			$cond=" and a.book_slno like ? ";
			$param[]='%'.$src.'%';
		}
		$data['total_books']=$this->db->query("select count(*) as ttl from pnh_t_book_details")->row()->ttl;
		
		$sql="select a.*,b.book_type_name,c.franchise_id,d.username from pnh_t_book_details a 
					join pnh_m_book_template b on b.book_template_id=a.book_template_id 
					left join  pnh_t_book_allotment c on c.book_id = a.book_id 
					join king_admin d on d.id=a.created_by
					where 1 $cond
					order by a.created_on desc limit $pg ,$limit";
		
		$data['books_list']=$this->db->query($sql,$param)->result_array();
		
		
		
		//pagination end
		$config['base_url'] = site_url("admin/pnh_voucher_book/".$src);
		$config['total_rows'] = $data['total_books'];
		$config['per_page'] = $limit;
		$config['uri_segment'] = 4;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		//pagination end
		
		
		$data['page']='pnh_voucher_books';
		$this->load->view('admin',$data);
	}
	
	/**
	 * get the vouchers list by book
	 */
	function jx_get_vouchers_list_by_book()
	{
		$this->erpm->auth();
		$book_id=$this->input->post('book_id');
		
		$output=array();
		$vouchers_list_res=$this->db->query("select b.voucher_serial_no,c.voucher_name,c.denomination from pnh_t_book_voucher_link a join pnh_t_voucher_details b on b.id=a.voucher_slno_id join pnh_m_voucher c on c.voucher_id=b.voucher_id where a.book_id=?",$book_id);
		
		if($vouchers_list_res->num_rows())
		{
			$output['vouchers_list']=$vouchers_list_res->result_array();
			$output['status']='success';
		}else{
			$output['status']='error';
			$output['message']="No vouchers found";
		}
		
		echo json_encode($output);
	}
	
	/**
	 * function for create the voucher book
	 */
	function pnh_create_voucher_book()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$data['book_types']=$this->db->query("select * from pnh_m_book_template order by book_type_name")->result_array();
		$data['page']='pnh_create_voucher_book';
		$this->load->view('admin',$data);
	}
	
	function jx_get_template_denomination()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$temp_id=$this->input->post('temp_id');
		$output=array();
		$output['template_denomination']=$this->db->query("select b.denomination,a.no_of_voucher,b.voucher_name from pnh_m_book_template_voucher_link a join pnh_m_voucher b on b.voucher_id=a.voucher_id where book_template_id=? order by b.denomination",$temp_id)->result_array();
		$output['template_details']=$this->db->query("select * from pnh_m_book_template where book_template_id=?",$temp_id)->row_array();
		$output['menus']=$this->db->query("select id,name from pnh_menu where id in (select menu_ids from pnh_m_book_template where book_template_id=? )",$temp_id)->result_array();
		echo json_encode($output);
	}
	
	function jx_scan_voucher_slno()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$voucher_serial_no=$this->input->post('voucher_serial_no');
		$output=array();
		$voucher_det=$this->db->query("select * from pnh_t_voucher_details where voucher_serial_no=?",$voucher_serial_no)->row_array();
		
		//check if voucher exiest
		$is_exist=$this->db->query("select count(*) as ttl from pnh_t_voucher_details where voucher_serial_no=?",$voucher_serial_no)->row()->ttl;
		
		if(!$is_exist)
		{
			$output['status']='error';
			$output['msg']='Scanned voucher not found';
		}else
		{
		  $is_exist=$this->db->query("select count(*) as ttl from pnh_t_book_voucher_link where voucher_slno_id=?",$voucher_det['id'])->row()->ttl;
		  if($is_exist)
		  {
		  	$output['status']='error';
		  	$output['msg']='Scanned voucher already linked to another book';
		  }else{
		  	$output['status']='success';
		  	$output['data']=$voucher_det;
		  }
		}
		
		echo json_encode($output);
	}
	
	/**
	 * function for a create a voucher book
	 */
	function pnh_process_create_book()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$book_serialno=trim($this->input->post('book_serialid'));
		$book_type=$this->input->post('book_type');
		$book_vouchers=$this->input->post('voucher_serial_number');
		$is_oddvalue_book=$this->input->post('is_oddvalue_book');
		$process=0;
		
		if($book_serialno && $book_type && $book_vouchers)
			$process=1;
		
		$already_slno=$this->db->query("select count(*) as ttl from pnh_t_book_details where trim(book_slno) = $book_serialno")->row()->ttl;
		
		
		if(!$process || $already_slno)
		{
			if(!$book_serialno)
				show_error("Please Enter Book Slno");
			else if(!$book_type)
				show_error("Please Choose Valid Book Type ");
			else if(!$book_vouchers)
				show_error("Please Scan atleast one Voucher ");
			else if($already_slno)
				show_error("Entered slno already exist");
		}else
		{
			
			//get book template details 
			$book_tmpl_res = $this->db->query("select * from pnh_m_book_template where book_template_id = ? ",$book_type);
			if(!$book_tmpl_res->num_rows())
			{
				show_error("Template not selected");
			}
			
			$book_tmpl_det = $book_tmpl_res->row_array();
			
			$is_deal = $this->db->query("select count(*) as ttl from m_product_deal_link where product_id=?",$book_tmpl_det['product_id'])->row()->ttl;
			
			if(!$is_deal)
				show_error("Do not have deal for this product please create deal");
			
			// get scanned voucher details
			$scanned_voucher_summ = array();
			$scanned_voucher_value_ttl = array();
			foreach($book_vouchers as $v_slno)
			{
				$v_det_res = $this->db->query("select a.id,a.voucher_id,voucher_serial_no,denomination from pnh_t_voucher_details a join pnh_m_voucher b on a.voucher_id = b.voucher_id where voucher_serial_no = ? ",$v_slno);
				if($v_det_res->num_rows())
				{
					$v_det = $v_det_res->row_array();
					if(!isset($scanned_voucher_summ[$v_det['voucher_id']]))
					{
						$scanned_voucher_summ[$v_det['voucher_id']] = array();
						$scanned_voucher_value_ttl[$v_det['voucher_id']] = 0;
					}
					array_push($scanned_voucher_summ[$v_det['voucher_id']],$v_det);
					$scanned_voucher_value_ttl[$v_det['voucher_id']] += $v_det['denomination'];
				}
			}
			
			$scanned_voucher_ttl_value = array_sum($scanned_voucher_value_ttl);
			
			// check if book is oldvalue book[check by scanned qty and total book value with scanned total vouchers sum]
			$is_old_value = 0;
			if($book_tmpl_det['value'] != $scanned_voucher_ttl_value)
				$is_old_value = 1;
			
			if(!$is_old_value)
			{
				$book_tmpl_voucher_link = $this->db->query("select * from pnh_m_book_template_voucher_link where book_template_id = ? ",$book_tmpl_det['book_template_id']);
				if($book_tmpl_voucher_link->num_rows())
				{
					foreach ($book_tmpl_voucher_link->result_array() as $book_tmpl_voucher_link_det)
					{
						if($book_tmpl_voucher_link_det['no_of_voucher'] != count($scanned_voucher_summ[$book_tmpl_voucher_link_det['voucher_id']]))
						{
							$is_old_value = 1;
							break;
						}
					}
				}	
			}
			
			// mark as old value book id old value book id = 1
			if($is_old_value)
				$book_tmpl_id = 1;
			else
				$book_tmpl_id = $book_tmpl_det['book_template_id'];
			
			// create book entry in master 
			$ins=array();
			$ins['book_template_id']=$book_tmpl_id;
			$ins['book_slno']=$book_serialno;
			$ins['book_value']=$book_tmpl_det['value'];
			$ins['created_by']=$user['userid'];
			$ins['created_on']=cur_datetime();
			$this->db->insert('pnh_t_book_details',$ins);
			$book_id=$this->db->insert_id();
			
			$deal_price_det = $this->db->query("select b.orgprice,b.price from m_product_deal_link a join king_dealitems b on b.id=a.itemid where product_id=?",$book_tmpl_det['product_id'])->row_array();
			$mrp=$deal_price_det['orgprice'];
			$offer_price=$deal_price_det['price'];
			
			//calculate voucher margin
			$voucher_margin=($mrp-$offer_price)/$mrp*100;
			$voucher_margin=0;
			//link book with vouchers 
			foreach($scanned_voucher_summ as $v_id=>$v_slno_list)
			{
				foreach($v_slno_list as $v_sno_det)
				{
					$ins=array();
					$ins['book_id']=$book_id;
					$ins['voucher_slno_id']=$v_sno_det['id'];
					$ins['created_by']=$user['userid'];
					$ins['created_on']=cur_datetime();
					$this->db->insert('pnh_t_book_voucher_link',$ins);
					
					$franchise_value=((100-$voucher_margin)/100)*$v_sno_det['denomination'];
					$franchise_value=0;
					$this->db->query("update pnh_t_voucher_details set status = 1,voucher_margin=?,customer_value=?,franchise_value=? where status = 0 and id = ? ",array($voucher_margin,$v_sno_det['denomination'],$franchise_value,$v_sno_det['id']));
				}
			}
			
			
			$this->db->query("update t_stock_info set available_qty=available_qty+1,mrp=? where product_id=? limit 1",array($mrp,$book_tmpl_det['product_id']));
			$this->erpm->do_stock_log(1,1,$book_tmpl_det['product_id'],0,false,false,false,false);
			
			$this->db->insert("t_imei_no",array('product_id'=>$book_tmpl_det['product_id'],"imei_no"=>$book_serialno,"grn_id"=>0,"created_on"=>time(),"status"=>0));

			$this->session->set_flashdata("erp_pop_info",$book_serialno." book created ");
			redirect('admin/pnh_create_voucher_book');
		}
	}
	
	/**
	 * creating a voucher book
	 */
	function pnh_create_book()
	{
		$this->erpm->auth(true);
		$data['book_template']=$this->db->query("select * from pnh_m_book_template order by book_template_id asc")->result_array();
		$data['menu_list']=$this->db->query("select * from pnh_menu where status=1")->result_array();
		$data['page']='pnh_create_book';
		$this->load->view('admin',$data);
	}
	
	function pnh_book_create_process()
	{
		$user=$this->erpm->auth(true);
		$bt_id=$this->input->post('book_templates');
		$qty=$this->input->post('require_qty');
		
		
		if(!$bt_id)
			show_error("Please Choose book template");
		
		if(!$qty)
			show_error("Please enter require qty");
		
		
		if($bt_id && $qty)
		{
			for($q=1;$q<=$qty;$q++)
			{
				//get book template details
				$book_tmpl_res = $this->db->query("select * from pnh_m_book_template where book_template_id = ? ",$bt_id);
				if(!$book_tmpl_res->num_rows())
					show_error("Template not selected");
				
				$book_tmpl_det = $book_tmpl_res->row_array();
				
				$is_deal = $this->db->query("select count(*) as ttl from m_product_deal_link where product_id=?",$book_tmpl_det['product_id'])->row()->ttl;
				
				if(!$is_deal)
					show_error("Do not have deal for this product please create deal");
				
				
				//generate voucher seirel number
				$prev_book_slno=$this->db->query("select ifnull(max(book_id),0) as book_slno from pnh_t_book_details;")->row()->book_slno;
				
				if(!$prev_book_slno)
					$prev_book_slno=1;
				else
					$prev_book_slno+=1;
				
				$book_slno='B'.str_pad($prev_book_slno,6,"0",STR_PAD_LEFT);
				// create book entry in master
				$ins=array();
				$ins['book_template_id']=$bt_id;
				$ins['book_slno']=$book_slno;
				$ins['book_value']=$book_tmpl_det['value'];
				$ins['created_by']=$user['userid'];
				$ins['created_on']=cur_datetime();
				$this->db->insert('pnh_t_book_details',$ins);
				$book_id=$this->db->insert_id();
				
				
				//get the book template denomination configuration
				$denomination_config_det=$this->db->query("select a.*,b.denomination,b.voucher_id from pnh_m_book_template_voucher_link a join pnh_m_voucher b on b.voucher_id = a.voucher_id where book_template_id=?",$bt_id)->result_array();
				$voucher_values=array();
				$voucher_qty=array();
				$voucher_ids=array();
				
				foreach($denomination_config_det as $cd)
				{
					$voucher_values[]=$cd['denomination'];
					$voucher_qty[]=$cd['no_of_voucher'];
					$voucher_ids[]=$cd['voucher_id'];
				}
				
				//creating a vouchers
				$vouchers_det=$this->pnh_create_vouchers($voucher_values,$voucher_qty,$voucher_ids);
				
				$v_slno_list=$this->db->query("select * from pnh_t_voucher_details where id >= ? and group_code=?",array($vouchers_det['start_ins_id'],$vouchers_det['group_id']))->result_array();
				
				//link the vouchers to book
				foreach($v_slno_list as $v_sno_det)
				{
					$ins=array();
					$ins['book_id']=$book_id;
					$ins['voucher_slno_id']=$v_sno_det['id'];
					$ins['created_by']=$user['userid'];
					$ins['created_on']=cur_datetime();
					$this->db->insert('pnh_t_book_voucher_link',$ins);
					$this->db->query("update pnh_t_voucher_details set status = 1 where status = 0 and id = ? ",array($v_sno_det['id']));
				}
				
			}
			$this->session->set_flashdata("erp_pop_info",$qty." books created ");
			
		}else{
			$this->session->set_flashdata("erp_pop_info",$qty." books not  created ");
		}
		redirect('admin/pnh_create_book');
	}
	
	/**
	 * creating a vouchers 
	 * @param unknown_type $voucher_values
	 * @param unknown_type $voucher_qty
	 * @param unknown_type $voucher_ids
	 * @return multitype:number unknown
	 */
	
	function pnh_create_vouchers($voucher_values=0,$voucher_qty=0,$voucher_ids=0)
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$start_ins_id=0;
		//generate the group code
		$pre_grp_code=$this->db->query("select ifnull(max(group_code),0) as grp_code from pnh_t_voucher_details;")->row()->grp_code ;
	
		if(!$pre_grp_code)
			$pre_grp_code=1;
		else
			$pre_grp_code+=1;
	
		$total_vouchers_value=0;
		$sub_total=0;
		
		if($voucher_values && $voucher_qty && $voucher_ids)
		{
			foreach($voucher_ids as $i=>$vid)
			{
				$require_qty=$voucher_qty[$i];
				$voucher_val=$voucher_values[$i];
				$total_vouchers_value=$total_vouchers_value+($voucher_val*$require_qty);
				if($require_qty==0 || $require_qty=='')
					continue;
		
				//generate voucher seirel number
				//$prev_voucher_sino=$this->db->query("select ifnull(max(voucher_serial_no),0) as voucher_serial_no from pnh_t_voucher_details;")->row()->voucher_serial_no;
		
				//f(!$prev_voucher_sino)
				
				
		
				for($v=1;$v<=$require_qty;$v++)
				{
					$prev_voucher_sino=$this->erpm->_get_config_param('LAST_VOUCHER_SLNO');
					
					$param=array();
					$param['voucher_id']=$vid;
					$param['group_code']=$pre_grp_code;
					$param['voucher_serial_no']=$prev_voucher_sino+1;
					$param['voucher_code']=$this->p_gen_voucher_code(8);
					$param['value']=$voucher_val;
					$param['status']=0;
					$param['created_on']=cur_datetime();
					$param['created_by']=$user['userid'];
					$this->db->insert('pnh_t_voucher_details',$param);
					
					if($v==1 && $i==0 )
						$start_ins_id=$this->db->insert_id();
					
					$this->erpm->_set_config_param('LAST_VOUCHER_SLNO',$prev_voucher_sino+1);
					
				}
			}
			
			return array("group_id"=>$pre_grp_code,"start_ins_id"=>$start_ins_id);
		}
	
	}
	
	/***
	 * function to take stock for printed books
	 */
	function pnh_book_stock_in()
	{
		$this->erpm->auth();
		$data['page']='pnh_voucher_book_stock_in';
		$this->load->view('admin',$data);
	}
	
	/**
	 * function for process of stock in take the books
	 */
	function pnh_process_stock_intake_books()
	{
		$this->erpm->auth();
		$bookslno=$this->input->post('bookslno');
		$stock_inataked=0;
		
		$book_exist=$this->db->query("select count(*) as ttl from pnh_t_book_details where book_slno=?",$bookslno)->row()->ttl;
		if(!$book_exist)
			die("<div class='book_not_found' style='background:#cd0000;'>Book slno not found :{$bookslno} not found</div>");
		
		$book_slno_exist=$this->db->query("select count(*) as ttl from t_imei_no where imei_no=?",$bookslno)->row()->ttl;
		if($book_slno_exist)
			die("<div class='bookslno_alexist' style='background:purple;'>Book slno :{$bookslno} already exist</div>");
		//get the book details
		$book_det=$this->db->query("select a.*,b.*
										from pnh_t_book_details a 
										join pnh_m_book_template b on b.book_template_id=a.book_template_id 
									where book_slno=?",$bookslno)->row_array();
		
		$deal_price_det = $this->db->query("select b.orgprice,b.price from m_product_deal_link a join king_dealitems b on b.id=a.itemid where product_id=?",$book_det['product_id'])->row_array();
		$mrp=$deal_price_det['orgprice'];
		$offer_price=$deal_price_det['price'];
		
		
		$this->db->query("update t_stock_info set available_qty=available_qty+1,mrp=? where product_id=? limit 1",array($mrp,$book_det['product_id']));
		
		if($this->db->affected_rows())
			$stock_inataked=1;
		
		if($stock_inataked)
		{
			$this->erpm->do_stock_log(1,1,$book_det['product_id'],0,false,false,false,false,false,$bookslno);
			$this->db->insert("t_imei_no",array('product_id'=>$book_det['product_id'],"imei_no"=>$bookslno,"grn_id"=>0,"created_on"=>time(),"status"=>0));
		}

		if($stock_inataked)
			die("<div class='stock_intaked' style='background:#f1f1f1;color:#000000;' >Stock in taked :{$bookslno}</div>");
		else
			die("<div class='stock_not_intaked' style='background:orange;'>Stock not in taked :{$bookslno} </div>");
	}
	
	/**
	 * function for manage the book receipts
	 */
	function pnh_manage_book_allotments($search_query=0,$status=0,$pg=0)
	{
		$limit=5;
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$cond='';
		if($search_query)
		{	$search_query=trim($search_query);
			$cond.=" and (a.allotment_id like '%".$search_query."%' or f.transid like '%".$search_query."%' or f.id like '%".$search_query."%' or g.invoice_no like '%".$search_query."%' or e.book_type_name like '%".$search_query."%' or d.book_slno like '%".$search_query."%' ) ";
		}
		
		if($status)
			$cond.=" and a.status=$status ";
		
		$sql="select a.*,c.franchise_name,e.book_type_name,i_orgprice-(i_discount+i_coup_discount) as franchise_value,d.book_slno,f.transid,f.id as oid,g.invoice_no 
				from pnh_t_book_allotment a 
				join pnh_m_franchise_info c on c.franchise_id=a.franchise_id
				join pnh_t_book_details d on d.book_id=a.book_id 
				join pnh_m_book_template e on e.book_template_id=d.book_template_id
				join king_orders f on f.id=a.order_id
				join king_invoice g on g.transid = f.transid
				where 1 $cond
				group by a.book_id 
				order by a.created_on desc limit $pg ,$limit ";
		
		$fran_book_link_det=$this->db->query($sql)->result_array();
		$total_records=$this->db->query("select count(*) as ttl from pnh_t_book_allotment")->row()->ttl;
		
		//pagination end
		$config['base_url'] = site_url("admin/pnh_manage_book_allotments/".$search_query.'/'.$status);
		$config['total_rows'] = $total_records;
		$config['per_page'] = $limit;
		$config['uri_segment'] = 5;
		$this->config->set_item('enable_query_strings',false);
		$this->pagination->initialize($config);
		$data['pagination'] = $this->pagination->create_links();
		$this->config->set_item('enable_query_strings',true);
		//pagination end
		$data['total_records']=$total_records;
		$data['page']='pnh_manage_book_allotments';
		$data['fran_book_link_det']=$fran_book_link_det;
		$this->load->view('admin',$data);
	}
	
	function jx_get_book_detby_allotment()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$allotment_id=$this->input->post('allotment_id');
		$output=array();
		
		$sql="select a.id,a.allotment_id,a.book_id,a.franchise_id,b.book_value,c.book_type_name,b.book_slno,
					ifnull(sum(d.adjusted_value),0) as payed,((f.i_orgprice-(f.i_discount+f.i_coup_discount))-ifnull(sum(d.adjusted_value),0)) as balance,f.i_orgprice-(f.i_discount+f.i_coup_discount) as book_price
					from pnh_t_book_allotment a
					join pnh_t_book_details b on b.book_id=a.book_id
					join pnh_m_book_template c on c.book_template_id=b.book_template_id
					left join pnh_t_book_receipt_link d on d.book_id=a.book_id
					join king_orders f on f.id=a.order_id
					where a.allotment_id=?
					group by a.book_id";
		
		$output['book_det']=$this->db->query($sql,$allotment_id)->result_array();
		echo json_encode($output);
	}
	
	function pnh_update_book_receipts()
	{
		$user=$this->erpm->auth(DEAL_MANAGER_ROLE|FINANCE_ROLE);
		$receipt_ids=$this->input->post('receipt_id');
		$adjusted_values=$this->input->post('adjusted_value');
		$book_id_list=$this->input->post('book_ids');
		$franchise_list=$this->input->post('franchise_ids');
		$allotments_ids=$this->input->post('allotments_ids');
		$msg='';
		$process=1;
		$output=array();
		$output['status']='';
		$output['msg']='';
		
		//validate the payment complted or not
		foreach($book_id_list as $i=>$bookid)
		{
			//get the book value
			$book_det=$this->db->query("select a.*,b.book_type_name from pnh_t_book_details  a join pnh_m_book_template b on b.book_template_id=a.book_template_id where book_id=?",$bookid)->row_array();
			
			//get the payed value;
			$payed_det=$this->db->query("select ifnull(sum(adjusted_value),0) as payed from pnh_t_book_receipt_link where book_id=?",$bookid)->row()->payed;
			$book_price_det=$this->db->query("select b.i_orgprice-(b.i_discount+b.i_coup_discount) as book_price from pnh_t_book_allotment a join king_orders b on b.id=a.order_id where a.book_id=? and a.franchise_id=? and a.allotment_id=?",array($bookid,$franchise_list[$i],$allotments_ids[$i]))->row_array();
			
			if($book_price_det['book_price']==$payed_det && isset($adjusted_values[$i]))
			{
				$output['status']='error';
				$output['msg']=$book_det['book_type_name']. ' is  book fully payed';
				$process=0;
				break;
			}
			
			if(isset($adjusted_values[$i]))
			{
				if($adjusted_values[$i]*1 > $book_price_det['book_price']*1)
				{
					$output['status']='error';
					$output['msg']="Your enter more then amount for the this ". $book_det['book_type_name'] ." book value";
					$process=0;
					break;
				}
				
				if($adjusted_values[$i] > ($book_price_det['book_price']-$payed_det))
				{
					$output['status']='error';
					$output['msg']="Your enter more then amount for the this ". $book_det['book_type_name'] ." book balance amount";
					$process=0;
					break;
				}
			}
			
		}
		
		//group the amount by receipt
		$receipt_amt_link=array();
		foreach($receipt_ids as $i=>$receipt)
		{
			if(!$adjusted_values[$i] || !is_numeric($adjusted_values[$i]))
				continue;
			
			if(!$receipt || !is_numeric($receipt ))
				continue;
			
			$is_franchise_receipt=$this->db->query("select count(*) as ttl from pnh_t_receipt_info where receipt_id=? and franchise_id=?",array($receipt,$franchise_list[$i]))->row()->ttl;
			$franchise=$this->db->query("select franchise_name from pnh_m_franchise_info where franchise_id=?",$franchise_list[$i])->row()->franchise_name;
			if(!$is_franchise_receipt)
			{
				$output['status']='error';
				$output['msg']='Receipt '. $receipt. ' not generated for  '.$franchise;
				$process=0;
				break;
			}
			
			if(!isset($receipt_amt_link[$receipt]))
				$receipt_amt_link[$receipt]=array();
			
			
			array_push($receipt_amt_link[$receipt],$adjusted_values[$i]);
		}
		
		
		$unreconciliation_amount_det=array();
		//check enter the receipt amout balance
		foreach($receipt_amt_link as $receipt_id=>$receipt_amt_det)
		{
			//check if reciept_exist
			$receipt_det_res=$this->db->query("select receipt_amount,receipt_id,franchise_id from pnh_t_receipt_info where receipt_id=?",$receipt_id);
			
			if(!$receipt_det_res->num_rows())
			{
				$output['status']='error';
				$output['msg']='Invalid receipt id';
				$process=0;
				break;
			}
			
			$receipt_det = $receipt_det_res->row_array();
			
			//check the receipt already used
			$receipt_used_amount=$this->db->query("select ifnull(sum(adjusted_value),0) as amount from pnh_t_book_receipt_link where receipt_id=?",$receipt_id)->row()->amount;
			
			
			if($receipt_det['receipt_amount'] < ($receipt_used_amount+array_sum($receipt_amt_det)))
			{
				$output['status']='error';
				$output['msg']='Do not have balance for receipt '.$receipt_id;
				$process=0;
				break;
			}
			
			$unreconciliation_amount_det[$receipt_id]=$receipt_det['receipt_amount']-($receipt_used_amount+array_sum($receipt_amt_det));
			
		}
		
		if($process)
		{
			foreach($book_id_list as $i=>$bid)
			{
				$franchise_id=$franchise_list[$i];
				$allotment_id=$allotments_ids[$i];
				$adjusted_value=$adjusted_values[$i];
				$receipt_id=trim($receipt_ids[$i]);
				
				if(!$adjusted_value || !is_numeric($adjusted_value))
					continue;
				
				if(!$receipt_id || !is_numeric($receipt_id))
					continue;
				
				if($adjusted_value && $receipt_id)
				{
					//insert the receipt link data
					$ins=array();
					$ins['book_id']=$bid;
					$ins['receipt_id']=$receipt_id;
					$ins['franchise_id']=$franchise_id;
					$ins['adjusted_value']=$adjusted_value;
					$ins['created_by']=$user['userid'];
					$ins['created_on']=cur_datetime();
					$this->db->insert("pnh_t_book_receipt_link",$ins);
					
					$payed_det=$this->db->query("select ifnull(sum(adjusted_value),0) as payed from pnh_t_book_receipt_link where book_id=?",$bid)->row()->payed;
					$book_price_det=$this->db->query("select b.i_orgprice-(b.i_discount+b.i_coup_discount) as book_price from pnh_t_book_allotment a join king_orders b on b.id=a.order_id where a.book_id=? and a.franchise_id=? and a.allotment_id=?",array($bid,$franchise_id,$allotment_id))->row_array();
					
					
					if($payed_det==$book_price_det['book_price'])
					{
						$this->db->query("update pnh_t_book_allotment set status=2,activated_on=? where allotment_id=? and book_id=? and franchise_id=? and status=1",array(cur_datetime(),$allotment_id,$bid,$franchise_id));
						
						//get the voucheres
						$vouchers_list_res=$this->db->query("select * from pnh_t_book_voucher_link where book_id=?",$bid);
						if($vouchers_list_res->num_rows())
						{
							$vouchers_list=$vouchers_list_res->result_array();
							foreach($vouchers_list as $voucher)
							{
								$this->db->query("update pnh_t_voucher_details set status=2 where id=? and status=1 limit 1",array($voucher['voucher_slno_id']));
							}
						}
					}
					//update the receipt balance amount
					$this->db->query("update pnh_t_receipt_info set unreconciliation_amount=? where receipt_id=? limit 1",array($unreconciliation_amount_det[$receipt_id],$receipt_id));
				}
			}
			
			$output['status']='success';
			$output['msg']='updated';
		}
		
		echo json_encode($output);
		
	}
	
	/**
	 * get the book receipt link details by book id
	 */
	function jx_get_book_receipt_link_det()
	{
		$this->erpm->auth();
		$book_id=$this->input->post('book_id');
		$output=array();
		
		$sql="select c.book_value,b.receipt_amount,a.adjusted_value,a.receipt_id 
					from pnh_t_book_receipt_link a 
					join pnh_t_receipt_info b on b.receipt_id=a.receipt_id
					join pnh_t_book_details c on c.book_id=a.book_id
					where a.book_id=?";
		
		$output['receipt_det']=$this->db->query($sql,$book_id)->result_array();
		
		echo json_encode($output);
	}
	
	/**
	 * check the product is alredy linked any templates
	 */
	function jx_prd_alredy_linked()
	{
		$this->erpm->auth();
		$pid=$this->input->post("pid");
		$output=array();
		$pid_exist=$this->db->query("select count(*) as ttl from pnh_m_book_template where product_id=?",$pid)->row()->ttl;
		if($pid_exist)
			$output['status']=1;
		else
			$output['status']=0;
		echo json_encode($output);
		
	}

	/**
	 * function for mark franchise is prepaid
	 */
	function mark_prepaid_franchise()
	{
		$user=$this->erpm->auth(true);	
		$fid=$this->input->post('prepaid_franchise_id');
		$reason=$this->input->post('prepaid_reason');
		$is_prepaid=0;
		
		$fran=$this->db->query("select * from pnh_m_franchise_info where franchise_id=?",$fid)->row_array();
		
		if(empty($fran))
			show_error("No franchise found");
		
		$sql="update pnh_m_franchise_info set";
		
		if($fran['is_prepaid'])
		{
			$sql.=" is_prepaid=?";
		}else{
			
			$prepaid_type=$this->erpm->check_franchise_have_prepaid_menu($fid,1);
			
			if($fran['credit_limit'] && $prepaid_type==1)
			{
				$this->db->query("update pnh_m_franchise_info set last_credit=credit_limit where franchise_id=?",$fid);
				$sql.=" credit_limit=0, ";
			}
			
			$is_prepaid=1;
			$sql.=" is_prepaid=?";
		}
		
		$sql.=" where  franchise_id=?";
		
		$this->db->query($sql,array($is_prepaid,$fid));
		
		
		if($this->db->affected_rows()>0)
		{
			$this->db->query("insert into pnh_franchise_prepaid_log(franchise_id,is_prepaid,reason,created_on,created_by)values(?,?,?,?,?)",array($fid,$is_prepaid,$reason,time(),$user['userid']));
		}
		if($is_prepaid)
			$this->erpm->flash_msg("Franchise Marked as prepaid");
		else
			$this->erpm->flash_msg("Franchise Marked as not prepaid franchise");
		
		redirect("admin/pnh_franchise/$fid");
	}
	
	function pnh_sales_bydeal_report()
	{
		
		$user=$this->erpm->auth(DEALS_MANAGER_ROLE);
		
		if($_POST)
		{
			$dealid = $this->input->post('dealid');
			$from = $this->input->post('from');
			$to = $this->input->post('to');
			 
			$sql = "select c.franchise_id,c.franchise_name,g.product_id,product_name,(a.quantity*f.qty) as order_qty,0 as ship_qty,sum((i_orgprice-(i_discount+i_coup_discount))*(a.quantity*f.qty)) as ordered_total,0 as shipped_total  
							from king_orders a 
							join king_transactions b on a.transid = b.transid 
							join pnh_m_franchise_info c on c.franchise_id = b.franchise_id
							join king_dealitems d on d.id = a.itemid
							join king_deals e on e.dealid = d.dealid 
							join m_product_deal_link f on f.itemid = a.itemid 
							join m_product_info g on g.product_id = f.product_id 
							where e.dealid =? and a.status != 3 and date(from_unixtime(b.init)) between date(?) and date(?)
						group by b.franchise_id,g.product_id  ";
		
			$deals = $this->db->query($sql,array($dealid,$from,$to))->result_array();	
			 
			if(count($deals))	
				$this->erpm->export_csv("deal_sales_report_".$dealid,$deals);
			else
				echo '<script>alert("no data found");</script>';
			
			exit;
		}
		
		
		$data['page']='pnh_sales_bydeal_report';
		$this->load->view('admin',$data);
	}
	
	function new_stat($fid=199,$from = '2013-01-01',$to = '2013-08-26')
	{
		
		$fid_list = array($fid);
		$bypasszero=1;
		
		$fid_str = implode(',',$fid_list);
		@list($s_y,$s_m,$s_d)=@explode("-",$from);
		$s=mktime(0,0,0,$s_m,$s_d,$s_y);
				 
		@list($s_y,$s_m,$s_d)=@explode("-",$to);
		$e=mktime(23,59,59,$s_m,$s_d,$s_y);
				
		$from = $from.' 00:00:00';
		$to = $to.' 23:59:59';		
		if($fid_str)
		{
			$has_entry = 0;
			if(!$bypasszero)
				$has_entry=$this->db->query("select count(*) as total from pnh_franchise_account_summary where franchise_id=? and from_unixtime(created_on) between ? and ? order by created_on desc",array($fid_str,$s,$e))->row()->total;
			
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
				
				/* 
				$this->pdf->Cell(23,4,"Current Balance : ",0);
				$this->pdf->SetFont('Arial','B',8);
				$this->pdf->Cell(50,4,"Rs {$fran['current_balance']}",0,1);
				*/ 
				 
				$this->pdf->SetFont('Arial','B',9);
				$this->pdf->Ln(3);
				$this->pdf->Cell(300,5,"Franchise Details",0,1);
				$this->pdf->SetFont('Arial','B',8);
				$this->pdf->Cell(200,4.5,"{$fran['franchise_name']}   (FID: {$fran['pnh_franchise_id']})",0,1);
				$this->pdf->SetFont('Arial','',8);
				$this->pdf->MultiCell(150,3.5,"{$fran['address']}, {$fran['locality']}, \n{$fran['city']}, {$fran['state']} - {$fran['postcode']}",0,"L");
				$this->pdf->Image("images/paynearhome.jpg",150,22,50);
				
				
				 
				// Payment Details - Activated/Confirmed   
				
				$ttl_summary = array();
				$ttl_summary['inv_active'] = 0;
				$ttl_summary['inv_cancelled'] = 0;
				$ttl_summary['rcpt_active'] = 0;
				$ttl_summary['rcpt_cancelled'] = 0;
				$ttl_summary['rcpt_pending'] = 0;
				
				$ttl_summary['pending_amt'] = 0;
				
				$sql = "select payment_mode,statement_id,a.receipt_id,acc_correc_id,instrument_no as cheque_no,debit_amt,credit_amt,date(a.created_on) as credit_date  
							from pnh_franchise_account_summary a
							join pnh_t_receipt_info b on a.receipt_id = b.receipt_id 
							where action_type = 3 and a.franchise_id = ? and a.receipt_type = 1 and a.status = 1 and b.status = 1  
							and unix_timestamp(a.created_on) between unix_timestamp(?) 
							and unix_timestamp(?)

						";
				$rcpt_det_res = $this->db->query($sql,array($fid,$from,$to));
				if($rcpt_det_res->num_rows())
				{
					$this->pdf->Ln(5);
					$this->pdf->SetFont('Arial','',12);
					$this->pdf->Cell(10,5,"Cleared Payments		",0,1);
				
					$amt_total = array(0,0);
					$data = array();
					foreach($rcpt_det_res->result_array() as $k=>$rcpt)
					{
						$rcpt_status = $rcpt['acc_correc_id']?'Reversed':'Active';
						
						$ttl_summary['rcpt_active'] += $rcpt['credit_amt'];
						$ttl_summary['rcpt_cancelled'] += $rcpt['debit_amt'];
						
						$amt_total[0] +=  $rcpt['credit_amt'];
						$amt_total[1] +=  $rcpt['debit_amt'];
						
						$data[] = array($rcpt['credit_date'],$rcpt['receipt_id'],$rcpt['acc_correc_id']?'':((($rcpt['payment_mode']==1)?('CHQ-'.$rcpt['cheque_no']):'Cash')),$rcpt['credit_amt'],$rcpt['debit_amt'],$rcpt_status);
					}
					$data[] = array("","","Sub Total",$amt_total[0],$amt_total[1],"");
					$this->pdf->build_table(array("Date","Receipt #","Payment Mode","Credit (Rs)","Debit (Rs)","Status / Remarks"),array(20,20,40,20,20,70),$data);	
				}
				
				
				
				
				$sql = "select a.remarks,a.is_returned,statement_id,a.invoice_no,credit_amt,debit_amt,date(created_on) as inv_date,b.shipped 
							from pnh_franchise_account_summary a 
							join shipment_batch_process_invoice_link b on a.invoice_no = b.invoice_no   
							join king_invoice c on c.invoice_no = b.invoice_no 
							where action_type = 1 
							and franchise_id = ?  
							and unix_timestamp(created_on) between unix_timestamp(?) 
							and unix_timestamp(?)
							group by a.statement_id 
						";
				$inv_det_res = $this->db->query($sql,array($fid,$from,$to));
				if($inv_det_res->num_rows())
				{
					$this->pdf->Ln(5);
					$this->pdf->SetFont('Arial','',12);
					$this->pdf->Cell(10,5,"Shipped/Invoiced Products",0,1);
					$amt_total = array(0,0);
					$data = array();
					foreach($inv_det_res->result_array() as $k=>$inv)
					{
						$ttl_summary['inv_active'] += $inv['debit_amt'];
						$ttl_summary['inv_cancelled'] += $inv['credit_amt'];
						
						$amt_total[0] += $inv['credit_amt'];
						$amt_total[1] += $inv['debit_amt'];
						
						$invoice_stat_msg = $inv['shipped']?'Shipped':'Invoiced';
						
						if($inv['is_returned'])
						{
							$invoice_stat_msg = 'Returned';
						}else
						{
							$invoice_stat_msg = $inv['credit_amt']?'Cancelled':$invoice_stat_msg;	
						}
						
						$invoice_stat_msg = $invoice_stat_msg.($inv['remarks']?'-'.$inv['remarks']:'');
						
						$data[] = array(format_date($inv['inv_date']),$inv['invoice_no'],round($inv['credit_amt'],2),round($inv['debit_amt'],2),$invoice_stat_msg);
						
					}
					$data[] = array("","Sub Total",$amt_total[0],$amt_total[1],"");
					$this->pdf->build_table(array("Invoice Date","Invoice no","Credit (Rs)","Debit (Rs)","Status/Remarks"),array(20,60,20,20,70),$data);
				}
				
				
				
				/*
				
				$sql = "select statement_id,member_id,credit_amt,debit_amt,date(created_on) as action_date 
							from pnh_franchise_account_summary a 
							 
							where action_type = 4 
							and franchise_id = ?  
							and unix_timestamp(created_on) between unix_timestamp(?) 
							and unix_timestamp(?)
						";
				$item_det_res = $this->db->query($sql,array($fid,$from,$to));
				if($item_det_res->num_rows())
				{
					$this->pdf->Ln(5);
					$this->pdf->SetFont('Arial','',12);
					$this->pdf->Cell(10,5,"MemberShip Details",0,1);
					$amt_total = array(0,0);
					$data = array();
					foreach($item_det_res->result_array() as $k=>$itm)
					{
						$amt_total[0] += $itm['credit_amt'];
						$amt_total[1] += $itm['debit_amt'];
						$data[] = array(format_date($inv['action_date']),$itm['member_id'],round($itm['credit_amt'],2),round($itm['debit_amt'],2),$itm['credit_amt']?'Cancelled':'Active');
					}
					$data[] = array("","Sub Total",$amt_total[0],$amt_total[1],"");
					$this->pdf->build_table(array("Date","Member ID","Credit (Rs)","Debit (Rs)","Status/Remarks"),array(20,60,20,20,70),$data);
				}
				*/
				
				$sql = "select statement_id,acc_correc_id,credit_amt,debit_amt,date(created_on) as action_date,remarks 
							from pnh_franchise_account_summary 
							where action_type = 5  
							and franchise_id = ?  
							and unix_timestamp(created_on) between unix_timestamp(?) 
							and unix_timestamp(?)
						";
				$item_det_res = $this->db->query($sql,array($fid,$from,$to));
				if($item_det_res->num_rows())
				{
					$this->pdf->Ln(5);
					$this->pdf->SetFont('Arial','',12);
					$this->pdf->Cell(10,5,"Account Adjustments",0,1);
					$amt_total = array(0,0);
					$data = array();
					foreach($item_det_res->result_array() as $k=>$itm)
					{
						//$ttl_summary['rcpt_active'] += $itm['credit_amt'];
						//$ttl_summary['rcpt_cancelled'] += $itm['debit_amt'];
						
						$amt_total[0] += $itm['credit_amt'];
						$amt_total[1] += $itm['debit_amt'];
						
						$data[] = array(format_date($itm['action_date']),$itm['acc_correc_id'],round($itm['credit_amt'],2),round($itm['debit_amt'],2),$itm['remarks']);
					}
					$data[] = array("","Sub Total",$amt_total[0],$amt_total[1],"");
					$this->pdf->build_table(array("Date","Correction ID","Credit (Rs)","Debit (Rs)","Status/Remarks"),array(20,60,20,20,70),$data);
				}
				
				
				
				$net_payable_amt = round($ttl_summary['inv_active']-$ttl_summary['rcpt_active'],2);
				
				
				
				$this->pdf->Ln(3);
				$this->pdf->build_table(array(),array(80,20,20,70),array(array("",($ttl_summary['rcpt_active']+$ttl_summary['inv_active']),($ttl_summary['rcpt_cancelled']+$ttl_summary['inv_cancelled']),"")),1);
				
				 
				 
				$sql = "select * from ((
select statement_id,type,count(a.invoice_no) as invoice_no,sum(credit_amt) as credit_amt,sum(debit_amt) as debit_amt,date(a.created_on) as action_date,concat('Total ',count(a.invoice_no),' IMEI Activations') as remarks 
		from pnh_franchise_account_summary a 
		join t_invoice_credit_notes b on a.credit_note_id = b.id 
		where action_type = 7 and type = 2 
		and a.franchise_id = ?  
		and unix_timestamp(a.created_on) between unix_timestamp(?) 
		and unix_timestamp(?)  
	group by action_date 	
)
union
(
select statement_id,1 as type,(a.invoice_no) as invoice_no,sum(credit_amt) as credit_amt,sum(debit_amt) as debit_amt,date(a.created_on) as action_date,remarks
		from pnh_franchise_account_summary a 
		join t_invoice_credit_notes b on a.invoice_no = b.invoice_no 
		where action_type = 7 and type = 1   
		and a.franchise_id = ?  
		and unix_timestamp(a.created_on) between unix_timestamp(?) 
		and unix_timestamp(?)  
	 group by statement_id 
)
) as g 
order by action_date 
						";

				$item_det_res = $this->db->query($sql,array($fid,$from,$to,$fid,$from,$to));
				if($item_det_res->num_rows())
				{
					$this->pdf->Ln(5);
					$this->pdf->SetFont('Arial','',12);
					$this->pdf->Cell(10,5,"Credit Notes",0,1);
					$amt_total = array(0,0);
					$data = array();
					foreach($item_det_res->result_array() as $k=>$itm)
					{
						//$ttl_summary['rcpt_active'] += $itm['credit_amt'];
						//$ttl_summary['rcpt_cancelled'] += $itm['debit_amt'];
						
						$amt_total[0] += $itm['credit_amt'];
						$amt_total[1] += $itm['debit_amt'];
						
						$data[] = array(format_date($itm['action_date']),(($itm['type']==1)?'Invoice':'IMEI Activation'),$itm['invoice_no'],round($itm['credit_amt'],2),round($itm['debit_amt'],2),(($itm['type']==1)?'':$itm['remarks']));
					}
					$data[] = array("","","Sub Total",$amt_total[0],$amt_total[1],"");
					$this->pdf->build_table(array("Date","Credit For",'Invoice no',"Credit (Rs)","Debit (Rs)","Status/Remarks"),array(20,30,30,20,20,70),$data);
				} 
				
				
				$sql = "select franchise_id,receipt_id,receipt_amount,payment_mode,instrument_no,bank_name,instrument_date,date(from_unixtime(created_on)) as rcpt_date
								from pnh_t_receipt_info 
								where franchise_id = ? 
								and (created_on) between unix_timestamp(?) and unix_timestamp(?)
								and status = 0 and receipt_type = 1  
								order by rcpt_date
						";
						 
				$inv_det_res = $this->db->query($sql,array($fid,$from,$to));
				if($inv_det_res->num_rows())
				{
					$this->pdf->Ln(5);
					$this->pdf->SetFont('Arial','',12);
					$this->pdf->Cell(10,5,"Uncleared Payments ",0,1);
				
					$pen_receipt = 0;
					$data = array();
					foreach($inv_det_res->result_array() as $k=>$inv)
					{
						$ttl_summary['rcpt_pending'] += $inv['receipt_amount'];
						$pen_receipt += $inv['receipt_amount'];
						$data[] = array(format_date($inv['rcpt_date']),$inv['receipt_id'],($inv['payment_mode']?'CHQ':'Cash'),$inv['receipt_amount'],$inv['instrument_no'],$inv['bank_name'],format_date(date('Y-m-d',$inv['instrument_date'])));
					}
					$data[] = array("","","Sub Total",$pen_receipt,"","","");
					$this->pdf->build_table(array("Date","Receipt #","Mode","Amount (Rs)","CHQ/DD no","Bank Name","CHQ/DD date"),array(20,20,15,30,40,40,20),$data,0);	
				}
				 
				
				
				
				$sql = "select franchise_id,receipt_id,receipt_amount,payment_mode,instrument_no,bank_name,instrument_date,date(from_unixtime(created_on)) as rcpt_date
								from pnh_t_receipt_info 
								where franchise_id = ? 
								and (created_on) between unix_timestamp(?) and unix_timestamp(?) 
								and status in (2,3) and is_active = 1   
								order by rcpt_date
						";
						 
				$inv_det_res = $this->db->query($sql,array($fid,$from,$to));
				if($inv_det_res->num_rows())
				{
					$this->pdf->Ln(5);
					$this->pdf->SetFont('Arial','',12);
					$this->pdf->Cell(10,5,"Returned/Bounced Payments",0,1);
				
					$cancelled_receipt = 0;
					$data = array();
					foreach($inv_det_res->result_array() as $k=>$inv)
					{
						$ttl_summary['rcpt_cancelled'] += $inv['receipt_amount'];
						$cancelled_receipt += $inv['receipt_amount'];
						$data[] = array(format_date($inv['rcpt_date']),$inv['receipt_id'],($inv['payment_mode']?'CHQ':'Cash'),$inv['receipt_amount'],$inv['instrument_no'],$inv['bank_name'],format_date(date('Y-m-d',$inv['instrument_date'])));
					}
					$data[] = array("","","Sub Total",$cancelled_receipt,"","");
					$this->pdf->build_table(array("Date","Receipt #","Mode","Amount (Rs)","CHQ/DD no","Bank Name","CHQ/DD date"),array(20,20,15,30,40,40,20),$data,0);	
				}
				 
				$not_shipped_amount = $this->db->query(" select sum(t) as amt from (
															select a.invoice_no,debit_amt as t 
																from pnh_franchise_account_summary a 
																join king_invoice c on c.invoice_no = a.invoice_no and invoice_status =  1 
																where action_type = 1 
																and franchise_id = ?  
															group by a.invoice_no ) as a 
															join shipment_batch_process_invoice_link b on a.invoice_no = b.invoice_no and shipped = 0  ",$fid)->row()->amt;
				
				
				
				$total_invoice_val = $this->db->query("select sum(debit_amt) as amt 
																from pnh_franchise_account_summary 
																where action_type = 1 and franchise_id = ? 
																and unix_timestamp(created_on) between unix_timestamp(?) 
																and unix_timestamp(?)",array($fid,$from,$to))->row()->amt;
																
				$total_invoice_cancelled_val = $this->db->query("select sum(credit_amt) as amt 
																from pnh_franchise_account_summary 
																where action_type = 1 and franchise_id = ? 
																and unix_timestamp(created_on) between unix_timestamp(?) 
																and unix_timestamp(?)",array($fid,$from,$to))->row()->amt;
				
				$sql = "select sum(credit_amt) as amt   
							from pnh_franchise_account_summary a
							join pnh_t_receipt_info b on a.receipt_id = b.receipt_id 
							where action_type = 3 and a.franchise_id = ? and a.receipt_type = 1 and a.status = 1 and b.status = 1
							and unix_timestamp(a.created_on) between unix_timestamp(?) and unix_timestamp(?)
						";
				$total_active_receipts_val = $this->db->query($sql,array($fid,$from,$to))->row()->amt ;
				
				$sql = "select sum(receipt_amount) as amt  
								from pnh_t_receipt_info 
								where franchise_id = ? 
								and status in (2,3) and is_active = 1 
								and unix_timestamp(created_on) between unix_timestamp(?) and unix_timestamp(?)  
						";
						 
				$total_cancelled_receipts_val = $this->db->query($sql,array($fid,$from,$to))->row()->amt ;
				
				
				$sql = "select sum(receipt_amount) as amt 
								from pnh_t_receipt_info 
								where franchise_id = ? and status = 0 and receipt_type = 1  and is_active = 1 
								and unix_timestamp(created_on) between unix_timestamp(?) and unix_timestamp(?) 
						";
						 
				$total_pending_receipts_val = $this->db->query($sql,array($fid,$from,$to))->row()->amt ;
				
				$sql = "select sum(credit_amt-debit_amt) as amt  
							from pnh_franchise_account_summary where action_type = 5 and franchise_id = ?
							and unix_timestamp(created_on) between unix_timestamp(?) and unix_timestamp(?)  
							";
				$acc_adjustments_val = $this->db->query($sql,array($fid,$from,$to))->row()->amt;
				
				/*
				$sql = "select sum(credit_amt-debit_amt) as amt  
							from pnh_franchise_account_summary where action_type = 7 and type in (1,2) and franchise_id = ? ";
				 */
				 $sql = "select sum(credit_amt-debit_amt) as amt from ((
select statement_id,type,count(a.invoice_no) as invoice_no,sum(credit_amt) as credit_amt,sum(debit_amt) as debit_amt,date(a.created_on) as action_date,concat('Total ',count(a.invoice_no),' IMEI Activations') as remarks 
		from pnh_franchise_account_summary a 
		join t_invoice_credit_notes b on a.credit_note_id = b.id 
		where action_type = 7 and type = 2 
		and a.franchise_id = ?  
		and unix_timestamp(a.created_on) between unix_timestamp(?) 
		and unix_timestamp(?)  
	group by action_date 	
)
union
(
select statement_id,1 as type,(a.invoice_no) as invoice_no,sum(credit_amt) as credit_amt,sum(debit_amt) as debit_amt,date(a.created_on) as action_date,remarks
		from pnh_franchise_account_summary a 
		join t_invoice_credit_notes b on a.invoice_no = b.invoice_no 
		where action_type = 7 and type = 1   
		and a.franchise_id = ?  
		and unix_timestamp(a.created_on) between unix_timestamp(?) 
		and unix_timestamp(?)  
	 group by statement_id 
)
) as g 
order by action_date ";  
				$ttl_credit_note_val = $this->db->query($sql,array($fid,$from,$to,$fid,$from,$to))->row()->amt;
				
				$total_active_invoice=$total_invoice_val-$total_invoice_cancelled_val;
				
				$net_payable_amt = ($total_active_invoice-($total_active_receipts_val+$acc_adjustments_val+$ttl_credit_note_val));
				
				$this->pdf->Ln(6);
				$this->pdf->SetFont('Arial','',10);
				$this->pdf->Cell(200,4.5,"Total Value of products Shipped/Invoiced									Rs ".formatInIndianStyle($total_active_invoice),0,1);
				$this->pdf->Ln(2);
				$this->pdf->Cell(200,4.5,"Total Value of products Cancelled/Returned					Rs ".formatInIndianStyle($total_invoice_cancelled_val),0,1);
				$this->pdf->Ln(2);
				$this->pdf->Cell(200,4.5,"Total Value of Credit Notes Raised																			Rs ".formatInIndianStyle($ttl_credit_note_val),0,1);
				$this->pdf->Ln(2);
				$this->pdf->Cell(200,4.5,"Total Cleared Payments		     																													Rs ".formatInIndianStyle($total_active_receipts_val),0,1);
				$this->pdf->Ln(2);
				$this->pdf->Cell(200,4.5,"Total Returned/Bounced Payments 																		Rs ".formatInIndianStyle($total_cancelled_receipts_val),0,1);
				
				if($acc_adjustments_val)
				{
					$this->pdf->Ln(2);
					$this->pdf->Cell(200,4.5,"Total Account Adjustments 																															Rs ".formatInIndianStyle($acc_adjustments_val),0,1);	
				}
				
				
				$this->pdf->Ln(2);
				$this->pdf->Ln(2);
				$this->pdf->SetFont('Arial','B',12);
				
				$this->pdf->Cell(200,4.5,"Total Amount Pending for Payment		Rs ".formatInIndianStyle($net_payable_amt).($total_pending_receipts_val?' (uncleared  Rs '.formatInIndianStyle($total_pending_receipts_val).')':''));
				if($not_shipped_amount)
				{
					$this->pdf->Ln(6);
					$this->pdf->SetFont('Arial','B',12);
					$this->pdf->Cell(200,4.5,"Invoiced But not shipped yet		          	Rs ".formatInIndianStyle($not_shipped_amount));	
				}
				
				 
			}
			
			$this->pdf->Output("New Account Statement - $from to $to.pdf","I");
		}
			
	}


	function gen_fsales_rep_upd($from='',$to='')
	{
		ini_set("max_execution_time",6000);
		ini_set("display_errors",1);
		ini_set("memory_limit",'1024M');
		
		if($from == '')
		{	
			// get first order of franchise
			$f_order_date_ts = $this->db->query("select min(init) as init from king_transactions where franchise_id != 0 ")->row()->init;
			$from = strtotime(date('Y-m-d',$f_order_date_ts));
		}
		
	
		if($to == '')
			$to = date('Y-m-d');
	
		$from = strtotime($from);//2013-11-30 23:59:36
		$to = strtotime($to);//2014-01-31 23:59:36
	
		// reset week start and end dates
		$from_wdno = date('w',$from);
		$from_week_date_ts = $from-($from_wdno*24*60*60);
	
		$to_wdno = date('w',$to);
		$to_week_date_ts = $to+((6-$to_wdno)*24*60*60);
	
	
		if($to_week_date_ts < $from_week_date_ts)
			die("invalid Dates Entered");
		$ttl_days = ($to_week_date_ts-$from_week_date_ts)/(24*60*60);
	
	
		$csv_head = array();
		$csv_head = '"Month","Week Start","Week End","Territory","Town","FranchiseID","Franchise","RegisteredOn","Sales(Rs)","Pending Amount(Rs)","Uncleared Payment(Rs)","Last Shipment Value(Rs)","Sales Till Date"'."\r\n";
		
		for($d=0;$d<$ttl_days;)
		{
	
			// get week start date
			$st_date = date('Y-m-d',$from_week_date_ts+($d*24*60*60));
			//	echo $st_date.'<br>';
	
			// get week end date
			$d = $d+7;
	
			$en_date = date('Y-m-d',$from_week_date_ts+($d*24*60*60));
	
			//echo $st_date.'<br>'.$en_date.'<br>';
			$sql = "select c.franchise_id,franchise_name,territory_name,town_name,c.created_on,sum(a.i_orgprice*a.quantity) as sales
			from king_orders a
			join king_transactions b on a.transid = b.transid
			join pnh_m_franchise_info c on c.franchise_id = b.franchise_id
			join pnh_m_territory_info d on d.id = c.territory_id
			join pnh_towns e on e.id = c.town_id
			where a.status != 3
			and b.franchise_id > 0
			and date(from_unixtime(b.init)) between date(?) and date(?)
			group by franchise_id";
	
			$res = $this->db->query($sql,array($st_date,$en_date));
	
			//echo $this->db->last_query();
	
			if($res->num_rows())
			{
				foreach($res->result_array() as $row)
				{
					
					$fr_id = $row['franchise_id'];
					//till date sales
					$sales_tilldate =$this->db->query("select sum(i_orgprice*quantity) as sales  from king_orders a join king_transactions b on a.transid = b.transid where a.status != 3 and b.franchise_id = ? ",$row['franchise_id'])->row()->sales;
	
					$last_shipment_val = $pending_amt = $uncleared_payment = 0;
					
					
					$last_shipment_val = @$this->db->query("select ref_dispatch_id,sum(amt)  as last_dispatch_amt from (
																	select a.invoice_no,ref_dispatch_id,date(shipped_on) as shipped_on,(a.mrp-a.discount)*a.invoice_qty as amt 
																		from king_invoice a 
																		join shipment_batch_process_invoice_link b on a.invoice_no = b.invoice_no
																		join king_orders c on c.id = a.order_id 
																		join king_transactions d on d.transid = c.transid							
																		where a.invoice_status = 1 and d.franchise_id = ? and c.status != 3 
																		and date(b.shipped_on) between date(?) and date(?)  
																	group by a.invoice_no,a.order_id 
																	having amt > 0 
																	) as g
																	group by g.ref_dispatch_id 
																	order by g.ref_dispatch_id desc 
																	limit 1 ",array($fr_id,$st_date,$en_date))->row()->last_dispatch_amt;
					
					$uncleared_payment = @$this->db->query("
															select franchise_id,sum(receipt_amount) as ramt 
																from pnh_t_receipt_info 
																where date(from_unixtime(created_on)) <= date(?)
																and (activated_on = 0 or (date(from_unixtime(activated_on)) > date(?)) ) 
																and payment_mode = 1 and franchise_id = ? and receipt_type = 1
															group by franchise_id;",array($en_date,$en_date,$fr_id))->row()->ramt;
					
					$pending_amt = @$this->db->query("select sum(debit_amt-credit_amt) as pending_amt from pnh_franchise_account_summary where franchise_id = ? and date(created_on) <= date(?) ",array($fr_id,$en_date))->row()->pending_amt;
					 
					
					$week_rep_data = array();
					$week_rep_data[] = ucwords(date('M',strtotime($st_date)));
					$week_rep_data[] = $st_date;
					$week_rep_data[] = $en_date;
					$week_rep_data[] = ucwords($row['territory_name']);
					$week_rep_data[] = ucwords($row['town_name']);
					$week_rep_data[] = ucwords($row['franchise_id']);
					$week_rep_data[] = ucwords($row['franchise_name']);
					$week_rep_data[] = format_date_ts($row['created_on']);
					$week_rep_data[] = format_price($row['sales'],0);
					$week_rep_data[] = format_price($pending_amt*1,2);
					$week_rep_data[] = format_price($uncleared_payment*1,2);
					$week_rep_data[] = format_price($last_shipment_val*1,2);
					$week_rep_data[] = format_price($sales_tilldate*1,2);
					$csv_head .='"'.implode('","',$week_rep_data).'"'."\r\n";
				}
			}
		}
		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename=Franchise_Week_REPORT.csv');
		header('Pragma: no-cache');
		echo $csv_head;
		
	}

	function rmv_deals($menuid=0,$cat_ids=0)
	{

		
		error_reporting(E_ALL);
		ini_set('max_execution_time',60000);
		ini_set('memory_limit','1024M');
		
		//$cat_ids = "142,124,140,914,916,917,918,920,923,924,926,929,930,657,319,841,842,854,849,850,851,852,500,502,503,504,505,506,507,508,509,510,511,521,522,523,524,525,526,528,529,530,531,532,533,535,538,539,540,542,543,47,513,675,846,847,848,856,857,858,248";
		
		$cond = '';
		if($menuid)
			$cond .= ' and b.menuid in ('.$menuid.') ';
		
		if($cat_ids)
			$cond .= ' and b.catid in ('.$cat_ids.') ';
		
		$counter = 1;
		$deleted = 1;
		// get deals by category  
		$deal_list_res = $this->db->query("select a.id,a.dealid from king_dealitems a join king_deals b on a.dealid = b.dealid and is_pnh = 1 where 1 $cond ");
		 
		if($deal_list_res->num_rows())
		{
			foreach($deal_list_res->result_array() as $deal)
			{
				echo "Slno ".$counter.' - '.$deal['id'].'<br>';
				$counter++;
				// conditions to check  before deal is deleted
				// check if deal is ordered atleast one time 
				$is_deal_ordered = (@$this->db->query("select count(a.id) as t from king_orders a join king_dealitems b on a.itemid = b.id where b.id = ? and is_pnh = 1 ",$deal['id'])->row()->t)*1;
				if($is_deal_ordered)
					continue;
				
				// check if deal has stock 
				$is_deal_stock = (@$this->db->query("select sum(s) as s from 
										(
											(
												select ifnull(sum(s),0) as s from (
												select a.product_id,sum(available_qty) as s 
													from m_product_deal_link a 
													join t_stock_info b on a.product_id = b.product_id 
													where itemid = ? 
													group by product_id 
													) as g
											)
											union (
												select ifnull(sum(s),0) as s from (
												select c.product_id,sum(ifnull(available_qty,0)) as s 
													from m_product_group_deal_link a 
													join products_group_pids c on c.group_id = a.group_id
													left join t_stock_info b on b.product_id = c.product_id 
													where itemid = ?
													group by product_id ) as g 
											)
										) as h",array($deal['id'],$deal['id']))->row()->s)*1;
				if($is_deal_stock)
					continue;
				
				$process_delete = 0;
				// deal can be deleted as it is checked for no order and no stock 
				// check if deal is normal deal or group product deal 
				if($this->db->query("select count(*) as t from m_product_deal_link where itemid = ? ",$deal['id'])->row()->t)
				{
					$process_delete = 1;
					// delete linked product info

					foreach($this->db->query("select product_id from m_product_deal_link where itemid = ? ",$deal['id'])->result_array() as $dprd)
					{
						$this->db->query("delete from m_product_info where product_id = ? ",array($dprd['product_id']));
						$this->db->query("delete from m_product_deal_link where itemid = ? and product_id = ? ",array($deal['id'],$dprd['product_id']));
					}
					
					
					
				}else if($this->db->query("select count(*) as t from m_product_group_deal_link where itemid = ? ",$deal['id'])->row()->t)
				{
					$process_delete = 1;
					
					foreach($this->db->query("select product_id from m_product_group_deal_link a join products_group_pids b on a.group_id = b.group_id where a.itemid = ? ",$deal['id'])->result_array() as $dprd)
					{
						$this->db->query("delete from m_product_info where product_id = ? ",array($dprd['product_id']));
					}
					
					// delete linked group product info
					$group_id = $this->db->query("select group_id from m_product_group_deal_link where itemid = ? ",$deal['id'])->row()->group_id;
					$this->db->query("delete from m_product_group_deal_link where group_id = ? ",$group_id);
					$this->db->query("delete from products_group_pids where group_id = ? ",$group_id);
					$this->db->query("delete from products_group where group_id = ? ",$group_id);
					$this->db->query("delete from products_group_attribute_values where group_id = ? ",$group_id);
					$this->db->query("delete from products_group_attributes where group_id = ? ",$group_id);
					
				}
				
				if($process_delete)
				{
					// delete deals info 
					$this->db->query("delete from king_deals where dealid = ? ",$deal['dealid']);
					$this->db->query("delete from king_dealitems where dealid = ? ",$deal['dealid']);
					
					echo "Deleted ".$deleted.' - '.$deal['id'].'<br>';
					$deleted++;
					
				}
				
				
			}
		}
		
	}

	
	function bulk_cr_brands()
	{
		//$blist = array("Christian Dior","Prince Matchabelli","Viva Perfume","Titan Skinn","Royal Copenhagen","Royal Doulton","Royal Mirage","Velvet Touch","Roberto Cavalli","Swiss Arabian","United Fun","Jacques M","Peter Nygard","Giorgio Valenti","Jean Louis","Max Mara","Clean","Classic Collection","Light Fever","Milton Llyod","Creed","Arjun Rampal","Bogart","Bond","Clinique","Coach","Coty","Dame","Deluxe Parfum","Demure","Diadora","Donna Karan","DSquared2","Duccati","Dude","Elie Saab","Elite Model","Ellen Tracy","English Blazer","Eurolux","Faconnable","Fashion Police","Flyback","Fred Hayman","Gabriela Sabatini","Gale Hayman","GAP","Geoffrey Beene","Geparlys","Ghiaccio","Ghost","Gianni Venturi","Girogio Valenti","Halston","Helios","Houbigant","Hummer","Iceberg","Instyle Parfums","Isseymiyake","Juicy Couture","Justin Bieber","Karl Lagerfeld","Kathy Hilton","Kim Kardashian","Kiton","Lady Gaga","Lanvin","Laura Biagiotti","Logic","Luciano Soprani","Mancera","Manufaktura","Mariah Carey","Marilyn Miglin","Mary Quant.","Mercedes Benz","Michael Kors","Mila Schon","Montale","Muelhens","Myrurgia","Narciso Rodriguez","NBA","Nikos","Nu Parfums","Nuroma","Oleg Cassini","Pal Zileri","Paloma Picasso","Paulino Rubio","Perfumeria Antica","Perfumers Workshop","Puig","Ramy Latour","Remy Latour","Remy Marquis","Rich","Sean John","Seris Parfume","Shiseido","Snobz","Society Parfums","Star Luxe","Starry","Stella Mccartney","Steve McQueen","Taylor Swift","Trina","Trussardi","Ungaro","Usher","Versus Versace","Viviane Vendeile","Woods","WPC","Zippo");
		//$blist = array("Alda","Alessi USA","Anchor","Anchor Hocking","Anjali","Ankit Enterprises","Apex Home Appliances","Apollo International","arttd-inox","Bakers & Chefs","Bakers Edge","Bayou Classic","Bazooka","Bergner","Bergner Impex","Borosil","Borosil Glass Works Ltd.","Brentwood Appliances","Butterflyindia","Cook n Style","Cooking Concepts","Crystal","Cuisinox","DIY","Doughmakers","Dynaudio","Eagle Home","Exotic Thai","Fab Kitchen","Fenda","Fenda Audio","Freshware","General Electric","Geneva","Gibson","Harman Kardon","HAZEL","iSound","Jawbone","Kailash","Kaiser Bakeware","Kitchen Craft","Kitchen Elements","Kitchen Essentials","lazzaro","Libertyware","Mahavir","Maverick","Mebelkart","Murugan","Nirali","nirlon","OmniMount","Presto","Prime","Rubbermaid","Seagull","Silicone Kitchen","Silicone Solutions","Steelcraft","SteelSeries","Stovekraft","TTK Prestige","Wondercraft");
		$blist = array("A&D","Accelerade","Accu-Chek","AccuSure","Activeheat","Alacer","Alarsin","ALBUMEN","American Diagnostic","AMRUTANJAN","ANS","ANS Performance","ANSI","At Lumiscope","Atkins","Atkins Advantage","Bach","Biomerica","Body Essentials","Cerelac","Chef Jays","Clever Chek","Clif Bar","Clif Builders Snack","Clif Kid","Clif Kid Z Bar","Clif Shot","Clif Shot Bloks","Cobra Magnum","Combo","Contour","Cytomax","Detour","DIABLOC-K","DIAVITA-H","digital","Dr. Fresh","Easy Care","Electronic Digital Thermometer","Endurox","Enzymatic","Escali","ESN","Estroven","Freestyle","FRS","Fullbar","Gargem","GeniSoy","Glucerna","Glucocard","GlucoCare","GlucoOne","GNC","Gondwana","GRD","Hammer","Healthline","Healthvit","Herbal Hills","Herbalife","Himalaya","Himani","Ibp","iHealth","ILJIN","iSatori","ISHNEE","ISS","ISS Research","Kashi","Kelloggs","Kind","Kind Snacks","Lactogen","Lass Naturals","LifeSource","Lucem","Luna","Metagenics","Met-Rx","Microlife","Natrol","Naturade","Nature","Nature Valley","Natures Alchemy","Natures Bounty","Niscomed","NoGii","Now Foods","Nutrakey","Olex","Olympian Labs","One Touch Ultra","OneTouch","Oriyanna","Osim","Paramount","PEE SAFE","Pentasure","PINDIA","PowerBar","Pristine","Probar","Prodigy","Prolab","Promax","PROSURE","Protidiet","Pulman","Pulsatom","PureLife","Quantum Naturals","QuestBar","Re-Body","Relief Pak","RELISH","Riester","Rise","RollingMate","Rossmax","Sargam","Schiff","SD Biosenor","SeriCha","Shivalik Herbals","Slim-Fast","Smart Care","Solgar","SportPharma","Squirrel","SSN","STAMIN","Star","Sugarless Bliss","Summer Infant","Sunkist","Supractiv","Sweetwell","ThermoHAWK","THREPTIN","Tonico","Unistar","USPlabs","VAPORITE","Veridian Healthcare","Visalus","West Coast","Zenith Nutrition","ZEROLAC","Zims Crack Creme","ZoLi");
		foreach($blist as $name)
		{
			if($this->db->query("select count(*) as t from king_brands where name = ? ",$name)->row()->t)
				continue;
			
			$name = trim($name);
			
			$bid=$this->adminmodel->genbrandid();
			$url=preg_replace('/[^a-zA-Z0-9_\-]/','',$name);
			$url=str_replace(" ","-",$url);
			$this->db->query("insert into king_brands(id,name,url) values(?,?,?)",array($bid,$name,$url));
			$r = 10;
			$this->db->query("insert into m_rack_bin_brand_link(rack_bin_id,brandid) values(?,?)",array($r,$bid));
		}
	}
	
	function product_statsbymenu($menuid = 100)
	{
	
		 
		ini_set('max_execution_time','6000');
		ini_set('memory_limit','512M');
		
		
		$sql = "select e.id as menuid,e.name as menu,a.brand_id,f.name as brand,a.product_id,a.product_name,a.mrp,a.is_sourceable,'' as last_purchasedon,0 as stock,0 as stock_mrp_summ,0 as s15d,0 as s30d,0 as s60d  
					from m_product_info a 
					join m_product_deal_link b on a.product_id = b.product_id and b.is_active = 1
					join king_brands f on f.id = a.brand_id  
					join king_dealitems c on c.id = b.itemid
					join king_deals d on d.dealid = c.dealid
					join pnh_menu e on e.id = d.menuid  
					where d.menuid in ($menuid)
					group by product_id
					order by menu,brand,product_name
				";
		
		$res = $this->db->query($sql);
		if($res->num_rows())
		{
			$pmaster = array();
			
			foreach($res->result_array() as $row)
			{
				if(isset($pmaster[$row['product_id']]))
				 	continue;
				
				if(!count($pmaster))
					$pmaster[] = array_keys($row);
				
				$pmaster[$row['product_id']] = $row;
				
				// check current product stock
				$pmaster[$row['product_id']]['stock'] = ($this->db->query("select sum(available_qty) as t from t_stock_info where product_id = ? ",$row['product_id'])->row()->t)*1;
				
				$pmaster[$row['product_id']]['last_purchasedon'] = @($this->db->query("select date(created_on) as grn from t_grn_product_link where product_id = ? order by id desc limit 1;",$row['product_id'])->row()->grn);
				
				$pmaster[$row['product_id']]['stock_mrp_summ'] = @($this->db->query("select product_id,group_concat(s) as stock_summ from (
																							select product_id,concat(mrp,':',sum(available_qty)) as s
																								from t_stock_info 
																								where product_id = ?
																								group by product_id,mrp 
																								having sum(available_qty) > 0 ) as g 
																							group by product_id",$row['product_id'])->row()->stock_summ);
				
			 
				
				// last 30 days sales
				$pmaster[$row['product_id']]['s15d'] = $this->db->query("select ifnull(sum(c.qty*a.quantity),0) as qty
						from king_orders a
						join king_transactions b on a.transid = b.transid
						join m_product_deal_link c on c.itemid = a.itemid
						where c.product_id = ? and date(from_unixtime(b.init)) >= date_add(curdate(),INTERVAL -15 day);
						",$row['product_id'])->row()->qty;
				
				// last 30 days sales
				$pmaster[$row['product_id']]['s30d'] = $this->db->query("select ifnull(sum(c.qty*a.quantity),0) as qty
																				from king_orders a 
																				join king_transactions b on a.transid = b.transid
																				join m_product_deal_link c on c.itemid = a.itemid 
																				where c.product_id = ? and date(from_unixtime(b.init)) >= date_add(curdate(),INTERVAL -30 day); 
														",$row['product_id'])->row()->qty;
				
				// last 60 days sales
				$pmaster[$row['product_id']]['s60d'] = $this->db->query("select ifnull(sum(c.qty*a.quantity),0) as qty
						from king_orders a
						join king_transactions b on a.transid = b.transid
						join m_product_deal_link c on c.itemid = a.itemid
						where c.product_id = ? and date(from_unixtime(b.init)) >= date_add(curdate(),INTERVAL -60 day);
						",$row['product_id'])->row()->qty;
				
				
			}
		} 
		
		ob_start();
		$f=fopen("php://output","w");
		foreach($pmaster as $p)
			fputcsv($f,$p);
		fclose($f);
		$csv=ob_get_clean();
		
		ob_clean();
		header('Content-Description: File Transfer');
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename='.("Product_Stock_n_Sales_Stat_".date("d_m_y_H\h:i\m").".csv"));
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
	
	
	function updatefrmenu()
	{
		$user = $this->erpm->auth();
		$menu_selected=array();
		$menu_selected = array(133,128,129,102,101,100,104);
		
		$fid_list_res = $this->db->query("select distinct fid from pnh_franchise_menu_link where menuid = 100 and status = 1 order by fid");
		foreach($fid_list_res->result_array() as $frow)
		{
			$fid  = $frow['fid'];
			$this->db->query("update pnh_franchise_menu_link set status = 2,is_sch_enabled=0,modified_on=now() where status = 1 and fid = ? ",$fid);
			//get the latest modified record
		
			//	$this->db->query("update pnh_m_franchise_info")
			foreach($menu_selected as $m)
			{
				// check for no change
				$fm_stat_res = $this->db->query("select id,status from pnh_franchise_menu_link where fid = ? and menuid = ? order by id desc limit 1 ",array($fid,$m));
				if($fm_stat_res->num_rows())
				{
					$fm_stat = $fm_stat_res->row_array();
					// check for status
					if($fm_stat['status'] == 2)
					{
						$this->db->query("update pnh_franchise_menu_link set status = 1,modified_on=now(),is_sch_enabled=1 where status = 2 and id = ? ",$fm_stat['id']);
						continue ;
					}
				}
		
				$ins_data=array();
				$ins_data['fid']=$fid;
				$ins_data['menuid']=$m;
				$ins_data['created_by']=$user['userid'];
				$ins_data['created_on']=date("Y-m-d H:i:s");
				$ins_data['status']=1;
				$this->db->insert("pnh_franchise_menu_link",$ins_data);
			}
			
			$this->db->query("update pnh_franchise_menu_link set status = 0,modified_on=?,modified_by=? where status = 2 and fid = ? ",array(time(),$user['userid'],$fid));
			
		}
	}

	function bulk_notifymember_point_alert_ooodoood()
	{
		ini_set('max_execution_time','6000');
		ini_set('memory_limit','512M');
		
		// fetch registered members who are ordered atleast once.
		
		$sql = "select * from (
						(
							select a.pnh_member_id,franchise_id,concat(first_name) as mem_name,mobile,a.user_id
								from pnh_member_info a 
								join king_orders b on a.user_id = b.userid 
								where length(trim(mobile)) = 10 
							group by a.user_id
						)
						union
						(
							select pnh_member_id,franchise_id,concat(first_name) as mem_name,mobile,user_id
							from t_imei_no a 
							join pnh_member_info b on a.activated_mob_no = b.mobile  
							where length(trim(activated_mob_no)) = 10
						) 
					) as g 
					group by pnh_member_id,mobile,user_id
					 
				";
		$res = $this->db->query($sql);
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				// check for available loyalty points and added extra 50 points and send final avaialalble points as notification.
				// check member available points.
				$pnts = @($this->db->query("select points_after from pnh_member_points_track where user_id = ? order by id desc limit 1;",$row['user_id'])->row()->points_after);
				
				// ADD 50 Points to list. 
				$newpnts = $pnts*1;//+50;

				

				
				if($newpnts > 150)
					$newpnts = 150;
				/*
				// insert new points summary to log.
				$data = array();
				$data[] = $row['user_id'];
				$data[] = '';
				$data[] = 50;
				$data[] = $newpnts;
				$this->db->query("insert into pnh_member_points_track(user_id,transid,points,points_after,created_on) values (?,?,?,?,unix_timestamp())",$data);
				*/

				$name = trim($row['mem_name']);
				$name = (strlen($name) > 0)?$name:'Storeking Member';
				$mob = $row['mobile'];
				
				// send notification to member mobileno.
				$sms = "Dear $name,
Your $newpnts StoreKing points is nearing expiry,
Please use your points for your next purchase in one of the StoreKing retailer in your town.";
				
				
				//echo $row['pnh_member_id'].',"'.$row['mem_name'].'",'.$newpnts."\r\n";
				
				$this->erpm->pnh_sendsms($mob,$sms,$row['franchise_id'],0,'NOTIFY_MEMBER');
				
				
			} 
		}
	}

	function ___trg_resend_shipment_sms()
	{
		
		$sms_list_res = $this->db->query("select * from pnh_employee_grpsms_log where date(created_on) >= date('2014-03-06') and type = 4 order by id asc ");
		
		foreach($sms_list_res->result_array() as $sms)
		{
			$this->erpm->pnh_sendsms($sms['contact_no'],$sms['grp_msg'],0,$sms['emp_id']);

			//	echo $emp_mob_no,$sms_msg;
			$log_prm=array();
			$log_prm['emp_id']=$sms['emp_id'];
			$log_prm['contact_no']=$sms['contact_no'];
			$log_prm['type']=4;
			$log_prm['territory_id']=$sms['territory_id'];
			$log_prm['town_id']=$sms['town_id'];
			$log_prm['grp_msg']=$sms['grp_msg'];
			$log_prm['created_on']=cur_datetime();
			$this->erpm->insert_pnh_employee_grpsms_log($log_prm);
		}
	}

}