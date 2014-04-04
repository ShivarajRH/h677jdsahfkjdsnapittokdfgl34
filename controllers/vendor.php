<?php
class vendor extends Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->api_user = 9999;
		
		ini_set('max_execution_time','6000');
		$this->load->model("erpmodel","erpm");
	}
	
	
	
	/**
	 * function to check if key is valid 
	 * @param unknown_type $access_key
	 */
	function check_access($access_key='')
	{
		if($this->_get_vendordetbykey($access_key))
			echo "Valid";
		else
			echo "Invalid";
	}

	/**
	 * Function to show display error output 
	 * @param unknown_type $msg
	 */
	function errors($msg)
	{
		show_error($msg);
	}

	/**
	 * function to get vendor det by access key 
	 */
	function _get_vendordetbykey($akey)
	{
		$vdet_res = $this->db->query("select * from m_vendor_info where api_access_key = ? ",$akey);
		if($vdet_res->num_rows())
			return $vdet_res->row_array();
		return false;
	}

	/**
	 * function to import vendor product catalog details 
	 */
	function deal_import($client_id=0,$access_key='')
	{
		$vdet = $this->_get_vendordetbykey($access_key);
		
		if(!$vdet)
			$this->errors('Invalid Access Key : '.$access_key);
		
		if(!$client_id)
			$this->errors('VendorID is Requred');


		$header=array("sku","NAME","short_description","MRP","department","brand","color","gender","size","brand_color","product_family","care_instructions","size_fit_details","material","product_length","visual_pattern","brand_style_code","product_type","product_sub_type","unit","neck","sleeve","fit","Main Image URL","Other Image URL 1","Other Image URL 2","Other Image URL 3","Other Image URL 4","group_id","Fashionara_URL");

		$vendor=$this->db->query("select * from m_vendor_info where vendor_id=?",$client_id)->row_array();
		if(empty($vendor))
			$this->errors('Vendor:'.$client_id.' not found');
		
		//get deal bulk uploade file
		$csv_file_path=$this->db->query("select api_link from m_vendor_api_info where vendor_id=? and type='deal_import'",$client_id)->row_array();
		
		if(empty($csv_file_path))
			$this->errors('Deal file not found');

		$csv_filepath=$csv_file_path['api_link'];

		$deal_list=array();
		$keys=array();
		$report=array();
		
		$i=0;
		if (($handle = fopen($csv_filepath, "r")) !== FALSE) {
			while (($deals = fgetcsv($handle)) !== FALSE) {
				$i++;

				if($i==1)
				{
					$keys=$deals;
					if(count($header)!=count($deals))
						$this->errors('Invalid template');
					continue;
				}

				$e=array();
				foreach($deals as $c=>$d)
					$e[str_ireplace(' ','_',strtolower($keys[$c]))]=$d;

				if(!isset($deal_list[$e['group_id']]))
					$deal_list[$e['group_id']]=array();
				$deal_list[$e['group_id']][]=$e;
			}
		}
		
		echo '<b>Total products:</b>'.($i-1);
		echo '<br>';
		foreach($header as $i=>$fname)
		{
			if(strtolower($header[$i]) != strtolower($keys[$i]))
				$this->errors("Invalid template structure Missing ".$header[$i]);
		}
		
		$p=1;
		$e=0;
		foreach($deal_list as $c=> $dl)
		{
				
			$prc_res=array('validation'=>0,'data'=>'','type'=>'new_data_import','msg'=>array());
			$pids=array();
			$multi_imgs=array();
			foreach($dl as $d)
			{	
				$sku=$d['sku'];
				$product_name=$d['name'];
				$short_desc=$d['short_description'];
				$mrp=$d['mrp'];
				$menu=$d['department'];
				$brand=$d['brand'];
				$color=$d['color'];
				$gender=$d['gender'];
				$size=$d['size'];
				$brand_color=$d['brand_color'];
				$product_family=$d['product_family'];
				$care_instructions=$d['care_instructions'];
				$size_fit_details=$d['size_fit_details'];
				$material=$d['material'];
				$product_length=$d['product_length'];
				$visual_pattern=$d['visual_pattern'];
				$brand_style_code=$d['brand_style_code'];
				$product_type=$d['product_type'];
				$product_sub_type=$d['product_sub_type'];
				$unit=$d['unit'];
				$neck=$d['neck'];
				$sleeve=$d['sleeve'];
				$fit=$d['fit'];
				$main_img_url=$d['main_image_url'];
				$multi_imgs[]=$d['other_image_url_1'];
				$multi_imgs[]=$d['other_image_url_2'];
				$multi_imgs[]=$d['other_image_url_3'];
				$multi_imgs[]=$d['other_image_url_4'];
				$group_id=$d['group_id'];
				$client_url=$d['fashionara_url'];
				
				
				//get desc
				$description=$this->prepare_html_spec($d);
				
				//tile
				$new_name=$brand.' '.$product_name.' '.$color.' ('.$group_id.')-size:'.$size;
				$deal_name=$brand.' '.$product_name.' '.$color.' ('.$group_id.')';
				
				
				//log data preparing
				$e=array($c=>$deal_list[$c]);
				$prc_res['data']=json_encode($e);
				
				if($this->db->query("select 1 from m_product_info where product_name=? or sku_code=?",array($new_name,$sku))->num_rows()!=0)
				{
					$prc_res['validation']=1;
					$prc_res['msg'][]=' Duplicate name for product : '.$new_name;
				}

				//get brand id
				$brand_det=$this->db->query("select a.* from m_client_brand_link a join king_brands b on a.brand_id=b.id where client_brand=?",$brand)->row_array();
				
				if(!count($brand_det))
				{
					$prc_res['validation']=1;
					$prc_res['msg'][]=$brand." Brand not found in system";
				}

				//get category id
				$category_det=$this->db->query("select a.* from m_client_category_link a join king_categories b on b.id=a.category_id where client_category=?",$product_type)->row_array();

				if(!count($category_det))
				{
					$prc_res['validation']=1;
					$prc_res['msg'][]=$product_type." category not found in system";
				}
					
				//get menu det
				$menu_det=$this->db->query("select b.id as menu_id from m_client_menu_link a join pnh_menu b on b.id=a.menu_id where client_menu=?",$menu)->row_array();
				
				if(empty($menu_det))
				{
					$prc_res['validation']=1;
					$prc_res['msg'][]=$menu." menu not found in system";
				}
				
				//get category attributes details
				if($category_det)
				{
					$category_attr=$this->db->query("select b.* from king_categories a join m_attributes b on find_in_set(b.id,a.attribute_ids) where a.id=?;",$category_det['category_id'])->result_array();
	
					if(empty($category_attr))
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]="Attributes not linked for category";
					}else {
							
						foreach($category_attr as $ca)
						{
							if(!isset($d[strtolower($ca['attr_name'])]))
							{
								$prc_res['validation']=1;
								$prc_res['msg'][]=$ca['attr_name']." attribute not found";
							}
							
							if(isset($d[strtolower($ca['attr_name'])]))
							{
								if($d[strtolower($ca['attr_name'])]=='' || $d[strtolower($ca['attr_name'])]=='NA')
								{
									$prc_res['validation']=1;
									$prc_res['msg'][]=$ca['attr_name']." attribute value not found";
								}
							}
						}
					}
				}

				//deal adding process validation
				if($this->db->query("select 1 from king_dealitems where name=? and is_pnh=1 limit 1",$deal_name)->num_rows()!=0)
				{
					$prc_res['validation']=1;
					$prc_res['msg'][]="Duplicate deal name ".$deal_name;
				}

				
				if($prc_res['validation']==0)
				{
					//product creation block
					$inp=array();
					$inp['product_code']="P".rand(10000,99999);
					$inp['product_name']=$new_name;
					$inp['sku_code']=$sku;
					$inp['product_cat_id']=$category_det['category_id'];
					$inp['brand_id']=$brand_det['brand_id'];
					$inp['mrp']=$mrp;
					$inp['vat']='14.5';
					$inp['purchase_cost']=$mrp;
					$inp['is_sourceable']=1;
					$inp['is_sourceable']=1;
					$inp['created_on'] = date('Y-m-d H:i:s');
					$pic='';
						
					$this->db->insert("m_product_info",$inp);
					$pid=$this->db->insert_id();
					array_push($pids,$pid);	
					
					//product attribute inert
					foreach($category_attr as $ca)
					{
						$prd_attr_inp=array();
						$prd_attr_inp['pid']=$pid;
						$prd_attr_inp['attr_id']=$ca['id'];
						$prd_attr_inp['attr_value']=$d[strtolower($ca['attr_name'])];
						$this->db->insert("m_product_attributes",$prd_attr_inp);
					}
					
					$barcode='';
					$bid=$brand_det['brand_id'];
					$menu_id=$menu_det['menu_id'];
					$cid=$category_det['category_id'];
					$rackbin=1;$location=10;
					$raw_rackbin=$this->db->query("select l.location_id as default_location_id,l.id as default_rack_bin_id from m_rack_bin_brand_link b join m_rack_bin_info l on l.id=b.rack_bin_id where b.brandid=?",$bid)->row_array();
					if(!empty($raw_rackbin))
					{
						$rackbin=$raw_rackbin['default_rack_bin_id'];
						$location=$raw_rackbin['default_location_id'];
					}
					$this->db->query("insert into t_stock_info(product_id,location_id,rack_bin_id,mrp,product_barcode,available_qty,created_on) values(?,?,?,?,?,0,now())",array($pid,$location,$rackbin,$mrp,$barcode));
					
					//client products link our system product
					$cpl_inp=array();
					$cpl_inp['vendor_id']=$vendor['vendor_id'];
					$cpl_inp['product_id']=$pid;
					$cpl_inp['vendor_product_code']=$sku;
					$cpl_inp['mrp']=$mrp;
					$cpl_inp['created_on']=date('Y-m-d H:i:s');
					$this->db->insert("m_vendor_product_link",$cpl_inp);
					
					echo '<b>'.$p.'</b> Product added."<br>"';
					
					$p=$p+1;
				}
			}
			
			$process_log_inp=array();
			$process_log_inp['type']=$prc_res['type'];
			$process_log_inp['data']=$prc_res['data'];
			$process_log_inp['msg']=implode(',',$prc_res['msg']);
			$process_log_inp['created_on']=date('Y-m-d H:i:s');
			
			
			//deal creation block
			if(!empty($pids) && $prc_res['validation']==0 )
			{
				$itemid=$this->erpm->p_genid(10);
				$dealid=$this->erpm->p_genid(10);
				$publish=0;
				$shipsin='48-72 hrs';
				$gender_attr=$gender;
				$name=$deal_name;
				$print_name=$deal_name;
				$max_allowed_qty=100;
				$mrp=$mrp;
				$price=$mrp;
				$store_price=$mrp;
				$nyp_price=$mrp;
				$tax=14.5;
				$multi_imgs = array_filter(array_unique($multi_imgs));
				
				$main_img=$this->get_prd_img($main_img_url);
				//deal image
				if($main_img)
				{
					$fp=fopen('resources/client_product_images/'.$itemid.'.jpg','w');
					fwrite($fp,$main_img);
					fclose($fp);
					
					
					foreach($multi_imgs as $im_i=>$img_link)
					{
						$sub_img_data=$this->get_prd_img($img_link);
						
						$fp=fopen('resources/client_product_images/'.$itemid.'_'.($im_i+1).'.jpg','w');
						fwrite($fp,$sub_img_data);
						fclose($fp);
					}
					
					/*$imgname = randomChars ( 15 );
					$this->load->library("thumbnail");
					echo $this->thumbnail->check($main_img);exit;
					if($this->thumbnail->check($main_img))
					{
						$this->thumbnail->create(array("source"=>$main_img,"dest"=>"images/items/300/$imgname.jpg","width"=>300));
						$this->thumbnail->create(array("source"=>$main_img,"dest"=>"images/items/small/$imgname.jpg","width"=>200));
						$this->thumbnail->create(array("source"=>$main_img,"dest"=>"images/items/thumbs/$imgname.jpg","width"=>50,"max_height"=>50));
						$this->thumbnail->create(array("source"=>$main_img,"dest"=>"images/items/$imgname.jpg","width"=>400));
						$this->thumbnail->create(array("source"=>$main_img,"dest"=>"images/items/big/$imgname.jpg","width"=>1000));
					}*/
				
				}else{
					$imgname=null;
				}
				
				
				$keywords=str_ireplace(array('(',')','-'),'',str_ireplace(' ',',',$deal_name));
				//genrate a pnh id
				$pnh_id=$this->db->query("select pnh_id+1 as new_pnh_id
						from
						(
						select a.pnh_id,(select pnh_id from king_dealitems b where b.pnh_id = a.pnh_id+1 limit 1) as has_next
						from king_dealitems a
						where a.pnh_id != 0 and length(a.pnh_id) = 8
						order by a.pnh_id ) as g
						where has_next is null
						order by pnh_id limit 1 ")->row()->new_pnh_id;
			
				$inp=array("dealid"=>$dealid,"catid"=>$cid,"brandid"=>$bid,"pic"=>$pic,"description"=>$description,"publish"=>$publish,"menuid"=>$menu_id,"keywords"=>$keywords);
				$this->db->insert("king_deals",$inp);
				
				$inp=array("id"=>$itemid,"shipsin"=>$shipsin,"gender_attr"=>$gender_attr,"dealid"=>$dealid,"name"=>$name,"print_name"=>$print_name,"max_allowed_qty"=>$max_allowed_qty,"pic"=>$pic,"orgprice"=>$mrp,"price"=>$price,"store_price"=>$store_price,"nyp_price"=>$nyp_price,"is_pnh"=>1,"pnh_id"=>$pnh_id,"tax"=>$tax*100,"live"=>1,"is_group"=>1);
				$this->db->insert("king_dealitems",$inp);
				
				
				foreach($pids as $pid)
				{
					$qty=1;
					$inp=array("itemid"=>$itemid,"product_id"=>$pid,"product_mrp"=>$this->db->query("select mrp from m_product_info where product_id=?",$pid)->row()->mrp,"qty"=>$qty);
					$this->db->insert("m_product_deal_link",$inp);
				}
				
				$report[]=array($itemid,$dealid,$name,$pnh_id);
				
				
				
				
				
				$process_log_inp['status']=1;
				$this->db->insert("t_vendor_product_import_log",$process_log_inp);
				
			}else{
				
				$process_log_inp['status']=2;
				$this->db->insert("t_vendor_product_import_log",$process_log_inp);
			}
		}
		
		$this->db->insert("deals_bulk_upload",array("items"=>count($report),"created_on"=>time()));
	}
	
	/**
	 * function to prepare html description content for products 
	 * @param unknown_type $d
	 * @return string
	 */
	protected  function prepare_html_spec($d)
	{
		$description='';
		
		if(!empty($d))
		{
			//prepare description
			$description='';
			$description.='<div class="description">';
			$description.='<p>'.$d['short_description'].'</p>';
			$description.="</div>";
			$description.='<div class="specification">';
			$description.='<table>';
			$description.=	'<tr>';
			$description.=	'	<td>Gender</td>';
			$description.=	'	<td>'.$d['gender'].'</td>';
			$description.=	'</tr>';
			$description.=	'<tr>';
			$description.=	'	<td>Size fit details</td>';
			$description.=	'	<td>'.$d['size_fit_details'].'</td>';
			$description.=	'</tr>';
			$description.=	'<tr>';
			$description.=	'	<td>Material</td>';
			$description.=	'	<td>'.$d['material'].'</td>';
			$description.=	'</tr>';
			$description.=	'<tr>';
			$description.=	'	<td>Product length</td>';
			$description.=	'	<td>'.$d['product_length'].'</td>';
			$description.=	'</tr>';
			$description.=	'<tr>';
			$description.=	'	<td>Visual pattern</td>';
			$description.=	'	<td>'.$d['visual_pattern'].'</td>';
			$description.=	'</tr>';
			$description.=	'<tr>';
			$description.=	'	<td>Brand style code</td>';
			$description.=	'	<td>'.$d['brand_style_code'].'</td>';
			$description.=	'</tr>';
			$description.=	'<tr>';
			$description.=	'	<td>Neck</td>';
			$description.=	'	<td>'.$d['neck'].'</td>';
			$description.=	'</tr>';
			$description.=	'<tr>';
			$description.=	'	<td>Sleeve</td>';
			$description.=	'	<td>'.$d['sleeve'].'</td>';
			$description.=	'</tr>';
			$description.='</table>';
			$description.="</div>";
			
			
		}
		
		return $description;
	}
	
	/**
	 * function to get product image by link 
	 * @param unknown_type $link
	 */
	protected  function get_prd_img($link)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$http_result = curl_exec($ch);
		$error = curl_error($ch);
		$http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		return $http_result ;
	}
	
	/**
	 * function for generate the purchase order 
	 */
	function orders($access_key='')
	{
		if(!$access_key)
			$this->errors("Invalid access key entered");
		
		$vdet = $this->_get_vendordetbykey($access_key);
		if(!$vdet)
			$this->errors("Invalid access key entered");
		
		$vendor_id = $vdet['vendor_id'];
		
		$vendor=$this->db->query("select * from m_vendor_info where vendor_id=?",$vendor_id)->row_array();
		
		if(empty($vendor))
			$this->errors("Vendor not found for given vendor id");
		
		//check have permission to vendor
		$permission=$this->db->query("select count(*) as ttl from m_vendor_api_info where vendor_id=?",$vendor_id)->row()->ttl;
		
		if(!$permission)
			$this->errors("Not Authorized");
		
		//get the vendor products
		$vendor_prds=$this->db->query("select product_id from m_vendor_product_link where vendor_id=? and is_active=1",$vendor_id)->result_array();
		
		$pid_det=array();
		foreach($vendor_prds as $pid)
			$pid_det[]=$pid['product_id'];
		
		$sql="select p.product_id,p.sku_code,o.id as order_id,o.quantity,product_cat_id as cat_id  
					from king_orders o 
					join m_product_info p  on p.product_id=o.order_product_id
					where p.product_id in (".implode(',',$pid_det).") 
					and o.status=0 and order_from_vendor=? 
					and vendor_order_ref_id is null
			";
		
		$orders_det_res=$this->db->query($sql,array($vendor_id));
		
		// check if pending orders for which stock is not available 
		if(!$orders_det_res->num_rows())
		{
			$this->errors("No orders found");
			exit;
		}
		
		// check if po is created today and is still open 
		$v_lastpo_det_res = $this->db->query("select * from t_po_info where vendor_id = ? order by id desc limit 1 ",$vendor_id);
		
		$po_id = 0;
		if($v_lastpo_det_res)
		{
			$v_lastpo_det = $v_lastpo_det_res->row_array();
			if($v_lastpo_det['po_status'] == 0)
				$po_id = $v_lastpo_det['po_id'];
		};
		
		// PO product Order list
		$po_orderlist = array();
		$po_pstk = array();
		foreach($orders_det_res->result_array() as $order_det)
		{
			$o_pid = $order_det['product_id'];
			if(!isset($po_orderlist[$o_pid]))
			{
				$po_orderlist[$o_pid] = array();
				$po_pstk[$o_pid] = 0;
			}
			
			$po_orderlist[$o_pid][] = $order_det;
			$po_pstk[$o_pid] += $order_det['quantity'];
		}
		
		foreach($po_orderlist as $pid => $prod_order_det_arr)
		{
			foreach($prod_order_det_arr as $prod_order_det)
			{
				// chck if stock is available
				
				$rqty = $po_pstk[$pid];
				$astk = $this->db->query("select sum(available_qty) as t from t_stock_info where product_id = ? ",$pid)->row()->t;
				$ostk = $rqty-$astk;
				
				if($astk > $ostk)
					continue;
				
				// check if the order id is already in po
				if($this->db->query("select count(*) as t from t_vendor_po_order_link a join t_po_info b on a.po_id = b.po_id and b.po_status in (0,1) where a.order_id = ? ",$prod_order_det['order_id'])->row()->t)
					continue ;
				
				// create po if po not created
				if(!$po_id)
				{
					//creating po
					$this->db->query("insert into t_po_info(vendor_id,remarks,date_of_delivery,created_by,created_on,po_status) values(?,?,?,?,now(),0)",array($vendor_id,'po created by system api',date('Y-m-d 11:00:00', time()+(2*24*60*60)),$this->api_user));
					$po_id=$this->db->insert_id();
				}
				
				$prod = $this->db->query('select * from m_product_info where product_id = ? ',$pid)->row_array();
				$v_p_res = $this->db->query("select * from m_vendor_brand_link where vendor_id = ? and cat_id in (0,?) and is_active = 1 order by cat_id desc limit 1 ; ",array($vendor_id,$prod_order_det['cat_id']));
				if(!$v_p_res->num_rows())
					$prod['margin'] = 0;
				else
					$prod['margin'] = $v_p_res->row()->brand_margin;
				
				$prod['price'] = $prod['mrp']-($prod['mrp']*$prod['margin']/100);
				
				
				// create po and product entry in order log table
				$inp = array();
				$inp['po_id'] = $po_id;
				$inp['product_id'] = $prod_order_det['product_id'];
				$inp['order_qty'] = $prod_order_det['quantity'];
				$inp['order_id'] = $prod_order_det['order_id'];
				$inp['created_on'] = cur_datetime();
				$inp['created_by'] = $this->api_user;
				$this->db->insert('t_vendor_po_order_link',$inp);
				
				// check if product is already in PO
				if($this->db->query("select count(*) as t from t_po_product_link where po_id = ? and product_id = ? ",array($po_id,$pid))->row()->t)
					continue;
				
				// create po product entry 
				$inp=array($po_id,$pid,$ostk,$prod['mrp'],0,$prod['margin'],0,0,$prod['price'],0,0,"po by api",$this->api_user);
				$this->db->query("insert into t_po_product_link(po_id,product_id,order_qty,mrp,dp_price,margin,scheme_discount_value,scheme_discount_type,purchase_price,is_foc,has_offer,special_note,created_on,created_by) values(?,?,?,?,?,?,?,?,?,?,?,?,now(),?)",$inp);
				
			}
		}
		
		// prepare po export data
		$this->load->plugin('csv_logger');
		$csv_obj=new csv_logger_pi();
		$csv_obj->head(array("PO Date","PO ID/Order ref no","Partner name","Product SKU","Order Qty","PO Product MRP"));
		
		
		$po_det_res=$this->db->query("select vpo.product_id,sku_code,order_id,vpo.order_qty,pl.mrp,vpo.created_on,po.po_id
											from t_vendor_po_order_link vpo
											join  t_po_info po on po.po_id=vpo.po_id
											join t_po_product_link pl on vpo.po_id=pl.po_id 
											join m_product_info p on p.product_id = pl.product_id
											where po.vendor_id=? and po.po_status in (0,1)",array($vendor_id));
		if($po_det_res->num_rows())
		{
			foreach($po_det_res->result_array() as $po_det)
			{
				$csv_obj->push(array($po_det['created_on'],$po_det['po_id'].'/'.$po_det['order_id'],'StoreKing',$po_det['sku_code'],$po_det['order_qty'],$po_det['mrp']));
			}
			
		}
		
		$csv_obj->download('Storeking_Stock_Order_List');
		
	}
	
	/**
	 * function to check and validate if product is available with vendor 
	 */
	function sync_product_stock($vendor_id=0,$access_key='')
	{
		
		$vdet = $this->_get_vendordetbykey($access_key);
		
		if(!$vdet)
			$this->errors('Invalid Access Key : '.$access_key);
		
		if(!$vendor_id)
			$this->errors('VendorID is Requred');
		
		$api_user = $this->api_user;
		
		// get stock api link 
		//check if vendor has api 
		$vapi_info_res=$this->db->query("select * from m_vendor_api_info where vendor_id=? and type = 'stock_availablity_api' ",$vendor_id);
		
		if(!$vapi_info_res->num_rows())
			$this->errors("Stock Api url not found");
		
		$vapi_info = $vapi_info_res->row_array();
		
		$csv_filepath = $vapi_info['api_link'];
		$header = array('sku','mrp','special_price','qty','procurement_time');
		
		$i=-1;
		if (($handle = fopen($csv_filepath, "r")) !== FALSE) 
		{
			while (($prod = fgetcsv($handle)) !== FALSE)
			{
				$i++;
				if($i == 0)
					continue;
				
				// check if sku is available 
				$vp_res = $this->db->query("select product_id from m_vendor_product_link where vendor_product_code = ? ",$prod[0]);
				if(!$vp_res->num_rows())
					continue;
				
				// check and update product sourceablity on the products
				$this->db->query("update m_product_info set is_sourceable = ?,modified_on=now(),modified_by=? where product_id = ? ",array(($prod[3]?1:0),$this->api_user,$vp_res->row()->product_id));
				 
			}
		}
		
	}

}


