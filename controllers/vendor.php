<?php
error_reporting(E_ALL);
ini_set('max_execution_time','36000');
ini_set('memory_limit','128M');
ini_set('display_errors',1);

class vendor extends Controller
{
	private $product_import_count=array("vendor_id"=>'',"file_name"=>"","total_products"=>0,"total_deals"=>0,"total_deals_inserted"=>0,"total_error_flaged"=>0);
	function __construct()
	{
		parent::__construct();

		$this->api_user = 6;
		$this->count_log_id=0;
		$this->attr_fields_len=8;
		
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
		die($msg);
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
		ini_set('max_execution_time','36000');
		/*ini_set('memory_limit','1024M');
		ini_set('display_errors',1);*/
		$vdet = $this->_get_vendordetbykey($access_key);

		if(!$vdet)
			$this->errors('Invalid Access Key : '.$access_key);

		if(!$client_id)
			$this->errors('VendorID is Requred');


		//$header=array("sku","NAME","short_description","MRP","department","brand","color","gender","size","brand_color","product_family","care_instructions","size_fit_details","material","product_length","visual_pattern","brand_style_code","product_type","product_sub_type","unit","neck","sleeve","fit","Main Image URL","Other Image URL 1","Other Image URL 2","Other Image URL 3","Other Image URL 4","group_id","Fashionara_URL");

		$vendor=$this->db->query("select * from m_vendor_info where vendor_id=?",$client_id)->row_array();
		if(empty($vendor))
			$this->errors('Vendor:'.$client_id.' not found');


		$this->deal_import_process($client_id);
		exit;
		//get deal bulk uploade file
		$csv_file_path=$this->db->query("select access_path from m_vendor_api_resources where vendor_id=? and type='deal_import'",$client_id)->row_array();

		if(empty($csv_file_path))
			$this->errors('Deal file not found');

		$csv_filepath=$csv_file_path['access_path'];

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

		$this->deals_insert($deal_list,$client_id);
	}

	/**
	 * function for insert the products to database
	 *
	 */
	protected function deals_insert($deal_list,$client_id,$import_upt=0,$grp_v_link=array())
	{

		//check deal import log update flag set
		if($import_upt)
		{
			if(empty($grp_v_link))
				return 0;

			if(empty($deal_list))
				return 0;
		}

		$report=array();
		$p=1;
		$e=0;
		foreach($deal_list as $c=> $dl)
		{
			$log_id=0;
			//get the client id if it id deal import log update process
			if($import_upt)
			{
				$client_id=$grp_v_link[$c]['vendor_id'];
				$log_id=$grp_v_link[$c]['log_id'];
			}

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


				$logId_det_res=$this->db->query("select id from t_vendor_product_import_log where vendor_id=? and group_id=?",array($client_id,$group_id));

				if($logId_det_res->num_rows() && !$log_id)
				{
					$log_id=$logId_det_res->row()->id;
				}


				//get desc
				$description=$this->prepare_html_spec($d);

				//tile
				$new_name=$brand.' '.trim($product_name).' '.$color.' ('.$group_id.')-size:'.$size;
				$deal_name=$brand.' '.trim($product_name).' '.$color.' ('.$group_id.')';


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

					// check if brand is already
					$this->db->query("insert into m_client_brand_link (brand_id,client_brand,created_on)  (select id,name,now() from king_brands where name = ? order by id desc limit 1); ",array($brand));
					if($this->db->affected_rows())
					{
						$brand_det=$this->db->query("select a.* from m_client_brand_link a join king_brands b on a.brand_id=b.id where client_brand=?",$brand)->row_array();
					}else
					{
						if(!$this->db->query("select count(*) as ttl from m_client_brand_link where client_brand=?",$brand)->row()->ttl)
							$this->db->query("insert into m_client_brand_link(brand_id,client_brand,created_on)values(0,?,now())",array($brand));

						$prc_res['validation']=1;
						$prc_res['msg'][]=$brand." Vendor Brand is not linked in our erp brand";
					}

				}

				//check clint brand linked in our erp brand
				if(count($brand_det))
				{
					if($brand_det['brand_id']==0)
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]=$brand." Vendor Brand is not linked in our erp brand";
					}
				}

				$cat='';
				if($product_sub_type!='NULL' && $product_sub_type!='NA' && strlen($product_sub_type) > 2)
					$cat=$product_sub_type;
				else
					$cat=$product_type;
					
				//get category id
				$category_det=$this->db->query("select b.name as erp_cat,a.* from m_client_category_link a join king_categories b on b.id=a.category_id where client_category=?",$cat)->row_array();

				if(!count($category_det))
				{
					if(!$this->db->query("select count(*) as ttl from m_client_category_link where client_category=?",$cat)->row()->ttl)
						$this->db->query("insert into m_client_category_link(category_id,client_category,created_on)values(0,?,now())",array($cat));

					$prc_res['validation']=1;
					$prc_res['msg'][]=$product_type." category is not linked in our erp category";
				}

				if(count($category_det))
				{
					if($category_det['category_id']==0)
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]=$product_type." category is not linked in our erp category";
					}
				}

				//get menu det
				$menu_det=$this->db->query("select a.menu_id as menu_id from m_client_menu_link a join pnh_menu b on b.id=a.menu_id where client_menu=?",$menu)->row_array();

				if(empty($menu_det))
				{
					if(!$this->db->query("select count(*) as ttl from m_client_menu_link where client_menu=?",$menu)->row()->ttl)
						$this->db->query("insert into m_client_menu_link(menu_id,client_menu,created_on)values(0,?,now())",array($menu));

					$prc_res['validation']=1;
					$prc_res['msg'][]=$menu." menu is not linked in our erp menu";
				}

				if(count($menu_det))
				{
					if($menu_det['menu_id']==0)
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]=$menu." menu is not linked in our erp menu";
					}
				}

				//get category attributes details
				if($category_det)
				{
					$category_attr=$this->db->query("select b.* from king_categories a join m_attributes b on find_in_set(b.id,a.attribute_ids) where a.id=?;",$category_det['category_id'])->result_array();

					if(empty($category_attr))
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]="Attributes not linked for category ".$category_det['erp_cat'];
					}else {
							
						foreach($category_attr as $ca)
						{
							if(!isset($d[strtolower($ca['attr_name'])]))
							{
								$prc_res['validation']=1;
								$prc_res['msg'][]=$ca['attr_name']." attribute not found for ".$category_det['erp_cat'];
							}

							if(isset($d[strtolower($ca['attr_name'])]))
							{
								if($d[strtolower($ca['attr_name'])]=='' || $d[strtolower($ca['attr_name'])]=='NA')
								{
									$prc_res['validation']=1;
									$prc_res['msg'][]=$ca['attr_name']." attribute value not found ".$category_det['erp_cat'];
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
					$cpl_inp['vendor_id']=$client_id;
					$cpl_inp['product_id']=$pid;
					$cpl_inp['vendor_product_code']=$sku;
					$cpl_inp['mrp']=$mrp;
					$cpl_inp['created_on']=date('Y-m-d H:i:s');
					$this->db->insert("m_vendor_product_link",$cpl_inp);

					if(!$import_upt)
						echo '<b>'.$p.'</b> Product added."<br>"';

					$p=$p+1;
				}
			}

			$process_log_inp=array();
			$process_log_inp['type']=$prc_res['type'];
			$process_log_inp['data']=$prc_res['data'];
			$process_log_inp['msg']=implode(',',$prc_res['msg']);
			$process_log_inp['group_id']=$group_id;

			if(!$import_upt && !$log_id)
				$process_log_inp['created_on']=date('Y-m-d H:i:s');
			else
				$process_log_inp['modified_on']=date('Y-m-d H:i:s');

			$process_log_inp['vendor_id']=$client_id;


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


				//inerting the images
				$img_inp=array();
				$img_inp['vendor_id']=$client_id;
				$img_inp['item_id']=$itemid;
				$img_inp['main_image']=$main_img_url;
				$img_inp['other_images']=implode(',',$multi_imgs);
				$img_inp['created_on']=cur_datetime();
				$this->db->insert("m_vendor_product_images",$img_inp);

				/*$main_img=$this->get_prd_img($main_img_url);
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
					
				$imgname = randomChars ( 15 );
				$this->load->library("thumbnail");
				echo $this->thumbnail->check($main_img);exit;
				if($this->thumbnail->check($main_img))
				{
				$this->thumbnail->create(array("source"=>$main_img,"dest"=>"images/items/300/$imgname.jpg","width"=>300));
				$this->thumbnail->create(array("source"=>$main_img,"dest"=>"images/items/small/$imgname.jpg","width"=>200));
				$this->thumbnail->create(array("source"=>$main_img,"dest"=>"images/items/thumbs/$imgname.jpg","width"=>50,"max_height"=>50));
				$this->thumbnail->create(array("source"=>$main_img,"dest"=>"images/items/$imgname.jpg","width"=>400));
				$this->thumbnail->create(array("source"=>$main_img,"dest"=>"images/items/big/$imgname.jpg","width"=>1000));
				}

				}*/


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

				if(!$import_upt && !$log_id)
					$this->db->insert("t_vendor_product_import_log",$process_log_inp);
				else{
					$this->db->where('id', $log_id);
					$this->db->update('t_vendor_product_import_log', $process_log_inp);
				}

			}else{

				$process_log_inp['status']=2;

				if(!$import_upt && !$log_id)
					$this->db->insert("t_vendor_product_import_log",$process_log_inp);
				else{
					$this->db->where('id', $log_id);
					$this->db->update('t_vendor_product_import_log', $process_log_inp);
				}
			}
		}

		$this->db->insert("deals_bulk_upload",array("items"=>count($report),"created_on"=>time()));

		if($import_upt)
			return 1;
	}

	/**
	 * function for re-import the error flaged vendor deals
	 */
	function jx_update_deal_import()
	{
		$log_id=$this->input->post("log_id");
		$output=array();
		if($log_id && !empty($log_id) && is_array($log_id))
		{
			$log_res=$this->db->query("select l.* from t_vendor_product_import_log l join m_vendor_info v on l.vendor_id=v.vendor_id where id in (".implode(",",$log_id).") and status=2");

			if($log_res->num_rows())
			{
				$deal_list=array();
				$grp_log_link=array();

				$log_det=$log_res->result_array();
				foreach($log_det as $l)
				{
					$deal_det=json_decode($l['data'],true);
					foreach($deal_det as $g=>$d)
					{
						if(!isset($deal_list[$g]))
							$deal_list[$g]=$d;

						if(!isset($grp_log_link[$g]))
						{
							$grp_log_link[$g]=array();
							$grp_log_link[$g]['log_id']=$l['id'];
							$grp_log_link[$g]['vendor_id']=$l['vendor_id'];
						}
					}
				}

				//$status=$this->deals_insert($deal_list,0,1,$grp_log_link);
				$status=$this->_import_products($deal_list,0,1,$grp_log_link);

				if($status)
				{
					$output['status']='success';
					$output['msg']="Update process completed";
				}else{
					$output['status']='error';
					$output['msg']="Update process incompleted";
				}

			}else{
				$output['status']='error';
				$output['msg']="No logs are found";
			}

		}else{
			$output['status']='error';
			$output['msg']="LogId require";
		}

		echo json_encode($output,true);
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
	protected  function http_request($link)
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
		error_reporting(E_ALL);

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
		$permission=$this->db->query("select count(*) as ttl from m_vendor_api_resources where vendor_id=?",$vendor_id)->row()->ttl;

		if(!$permission)
			$this->errors("Not Authorized");

		//get the vendor products
		$vendor_prds=$this->db->query("select product_id from m_vendor_product_link where vendor_id=? and is_active=1",$vendor_id)->result_array();

		$pid_det=array();
		foreach($vendor_prds as $pid)
			$pid_det[]=$pid['product_id'];
		
		if(empty($pid_det))
			die();
	
		$sql="select t.franchise_id 
		from king_orders o
		join m_product_info p  on p.product_id=o.order_product_id
					join king_transactions t on t.transid = o.transid
					join pnh_m_franchise_info f on f.franchise_id = t.franchise_id 
					join m_vendor_product_link vp on vp.product_id = o.order_product_id 
					left join t_vendor_po_order_link vpo on vpo.order_id = o.id and order_status = 1
					where o.status=0 and order_from_vendor=? and vpo.order_id is null 
		and vendor_order_ref_id is null
					group by t.franchise_id  
					order by t.init asc 
		";

		$pen_orders_fran_det_res=$this->db->query($sql,array($vendor_id));

		// check if pending orders for which stock is not available
		if(!$pen_orders_fran_det_res->num_rows())
		{
			$this->errors("No orders found");
			exit;
		}

		foreach($pen_orders_fran_det_res->result_array() as $pen_order_fran)
		{
			$po_id = 0;
			
			$sql="select p.product_id,p.sku_code,o.id as order_id,o.quantity,product_cat_id as cat_id
					from king_orders o
					join m_product_info p  on p.product_id=o.order_product_id
					join king_transactions t on t.transid = o.transid
					join pnh_m_franchise_info f on f.franchise_id = t.franchise_id
					join m_vendor_product_link vp on vp.product_id = o.order_product_id
					left join t_vendor_po_order_link vpo on vpo.order_id = o.id and order_status = 1 
					where o.status=0 and order_from_vendor=? and t.franchise_id = ? and vpo.order_id is null  
					and vendor_order_ref_id is null
					group by o.id 
			";
			
			
			$orders_det_res=$this->db->query($sql,array($vendor_id,$pen_order_fran['franchise_id']));
	
		// check if po is created today and is still open
			/* $v_lastpo_det_res = $this->db->query("select * from t_po_info where vendor_id = ? order by id desc limit 1 ",$vendor_id);


		if($v_lastpo_det_res)
		{
			$v_lastpo_det = $v_lastpo_det_res->row_array();
			if($v_lastpo_det['po_status'] == 0)
				$po_id = $v_lastpo_det['po_id'];
			} */

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

				// check if order is already in vendor order log
					//if($this->db->query("select count(*) as t from t_vendor_po_order_link where order_id = ? ",$prod_order_det['order_id'])->row()->t)
						//continue;
				
				// check if the order id is already in po
				if(($this->db->query("select count(*) as t 
											from t_vendor_po_order_link a 
											join t_po_info b on a.po_id = b.po_id and b.po_status in (0,1) 
											join t_po_product_link c on c.po_id = b.po_id and c.product_id = a.product_id 
											where a.order_id = ? ",$prod_order_det['order_id'])->row()->t) > 0)
					continue ;

				// create po if po not created
				if(!$po_id)
				{
					//creating po
						$this->db->query("insert into t_po_info(vendor_id,remarks,date_of_delivery,created_by,created_on,po_status) values(?,?,?,?,now(),4)",array($vendor_id,'po created by system api',date('Y-m-d 11:00:00', time()+(2*24*60*60)),$this->api_user));
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
					$inp['order_status'] = 1;//$prod_order_det['order_id'];
				$inp['created_on'] = cur_datetime();
				$inp['created_by'] = $this->api_user;
				$this->db->insert('t_vendor_po_order_link',$inp);

				// check if product is already in PO
				if($this->db->query("select count(*) as t from t_po_product_link where po_id = ? and product_id = ? ",array($po_id,$pid))->row()->t)
					continue;


				// get vendor margin by vendor product link
				$prod['margin'] = @$this->db->query('select (100-((purchase_price/mrp)*100)) as mrg from m_vendor_product_link where product_id = ? and vendor_id = ? ',array($pid,$vendor_id))->row()->mrg;

				// create po product entry
				$inp=array($po_id,$pid,$ostk,$prod['mrp'],0,$prod['margin'],0,0,$prod['price'],0,0,"po by api",$this->api_user);
				$this->db->query("insert into t_po_product_link(po_id,product_id,order_qty,mrp,dp_price,margin,scheme_discount_value,scheme_discount_type,purchase_price,is_foc,has_offer,special_note,created_on,created_by) values(?,?,?,?,?,?,?,?,?,?,?,?,now(),?)",$inp);

			}
		}
			// send po created notification to souring team 
			if($po_id)
			{
				$vendor_name = $this->db->query('select vendor_name from m_vendor_info a join m_po_info b on a.vendor_id = b.vendor_id where po_id = ? ',$po_id)->row()->vendor_name;
				$mail_to = 'sourcing@storeking.in';
				$subj = $vendor_name.' Purchase order created - PO'.$po_id;
				$mail_cc = 'shariff@storeking.in';
				$message = '<div style="clear:both;padding:5px;"><h3> Dear Sourcing </h3>
								<p>New '.$vendor_name.' Purchase order has been generated : <a target="_blank" href="'.site_url('admin/viewpo/'.$po_id).'">PO'.($po_id).'</a></p>
								<br>
								<br>
								<span style="color:#888;font-size:9px"> Storeking Team</span>
							</div>
					';
				$this->erpm->_notifybymail($mail_to,$subj,$message,$fromname="Storeking Sourcing",$from='support@snapittoday.com',$mail_cc,'');
			}
			}
			
		exit;
			
	}

	/**
	 * function to prepare filename pattern
	 *
	 * @param unknown_type $filename_pattern
	 */
	function _prepare_filename($filename_pattern,$ref_no='')
	{
		$filename = $filename_pattern;
		$filename = str_ireplace('$REF_NO$',$ref_no,$filename);
		$filename = str_ireplace('$TIMESTAMP$',time(),$filename);
		$filename = str_ireplace('$DATETIME_DDMMYYYYHHMMSS$',date('dmYHis'),$filename);
		$filename = str_ireplace('$DATETIME_YYYYMMDDHHMMSS$',date('YmdHis'),$filename);

		return $filename;
	}


	function _map_template_with_file()
	{

	}

	function loop_stock_file()
	{
		for($i=0;$i<5;$i++)
			$this->sync_product_stock(215,'FSHN09110');
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
		$vapi_info_res=$this->db->query("select *
				from m_vendor_api_resources a
				join m_vendor_api_templates b on a.mapping_tmpl_id = b.id
				where vendor_id=?
				and module_type = 'stock_list'
				",$vendor_id);

		if(!$vapi_info_res->num_rows())
			$this->errors("Stock Api url not found");

		$vapi_info = $vapi_info_res->row_array();

		$csv_filepath = $vapi_info['access_path'];

		//

		//$header = array('sku','mrp','special_price','storeking_price','qty','procurement_time');

		// get template for stock file and prepare import content

		$data_map_det=$this->_map_filedata($vendor_id,'stock_list');
		
		if($data_map_det['batch_id'] && $data_map_det['ttl_deals'])
		{
			for($i=1;$i<=$data_map_det['ttl_deals'];$i++)
			{
				$data_arr=$this->get_imported_tempdata($data_map_det['batch_id'],$vendor_id);
				if($data_arr)
				{
		$itemid_list = array();

		foreach($data_arr as $grp_code => $g_prod_list)
		{
			foreach($g_prod_list as $prod)
			{
			
				// check if sku is available
				$vp_res = $this->db->query("select product_id from m_vendor_product_link where vendor_product_code = ? ",$prod['sku_code']);
				if(!$vp_res->num_rows())
					continue;
	
							// check for shared content   
							$upd_arr = array();
				
							if(!is_null($prod['mrp']))
								$upd_arr['mrp'] = $prod['mrp'];
				
							if(!is_null($prod['purchase_cost']))
								$upd_arr['purchase_price'] = $prod['purchase_cost'];
							else if(!is_null($prod['default_margin']))
								$upd_arr['purchase_price'] = $prod['mrp']-($prod['mrp']*$prod['default_margin']);
							
							if(!is_null($prod['expected_dod']))
								$upd_arr['delivery_tat'] = $prod['expected_dod'];
							else if(!is_null($prod['default_delivery_tat']))
								$upd_arr['delivery_tat'] = $prod['default_delivery_tat'];
							
							if(!is_null($prod['vat']))
								$upd_arr['tax'] = $prod['vat'];
							else if(!is_null($prod['default_tax']))
								$upd_arr['tax'] = $prod['default_tax'];

							if(!is_null($prod['qty']))
								$upd_arr['ven_stock_qty'] = $prod['qty'];
							$upd_arr['modified_on'] = cur_datetime();
							$this->db->where('product_id',$vp_res->row()->product_id);
							$this->db->where('vendor_id',$vendor_id);
							$this->db->update('m_vendor_product_link',$upd_arr);
							
							//$this->db->query("update m_vendor_product_link set purchase_price = ?,delivery_tat=?,ven_stock_qty=?,modified_on = now() where product_id = ? and vendor_id = ? ",array($prod['purchase_cost'],$prod['expected_dod'],$prod['qty'],$vp_res->row()->product_id,$vendor_id));
				
							// check and update product sourceablity on the products
							$this->db->query("update m_product_info set is_sourceable = ?,modified_on=now(),modified_by=? where product_id = ? ",array(($prod['qty']?1:0),$this->api_user,$vp_res->row()->product_id));
				
							  
							
							$itemdet_res = @$this->db->query("select itemid from m_product_deal_link where product_id = ? ",$vp_res->row()->product_id);
							if($itemdet_res->num_rows())
							{
								$itemdet = $itemdet_res->row_array();
								array_push($itemid_list,array('itemid'=>$itemdet['itemid'],'mrp'=>$prod['mrp'],'price'=>$prod['offer_price']));
							}
							
			}
			
		}
		
					foreach($itemid_list as $item)
		{
						$itemid = $item['itemid'];
				// compute stock status by product stock
						$itemdet = $this->db->query("select * from king_dealitems where id = ?  ",$itemid)->row_array();


						// update deal other product status
						$itmp_res = $this->db->query("select a.product_id,ven_stock_qty,is_sourceable
								from m_vendor_product_link a
								join m_product_deal_link b  on a.product_id = b.product_id and b.is_active = 1
								join m_product_info c on b.product_id = c.product_id
								where b.itemid = ?  ",$itemid);
						foreach($itmp_res->result_array() as $itmp)
						{
							$itmp_src_stat = ($itmp['ven_stock_qty']?1:0);
							$this->db->query("update m_product_info set is_sourceable = ? where product_id = ? and is_sourceable != ? ",array($itmp_src_stat,$itmp['product_id'],$itmp_src_stat));
						}
						
						//$deal_status = 0;

				// check if is_group
						//if($itemdet['is_group'])
						$deal_status = $this->db->query("select sum(ven_stock_qty) as qty from m_product_deal_link a join m_vendor_product_link b on a.product_id = b.product_id join king_dealitems c on c.id = a.itemid where a.is_active = 1 and a.itemid = ? ",$itemid)->row()->qty;
				
				$deal_status = $deal_status?1:0;
				
				// update deal status if no stock
				$this->db->query("update king_deals set publish = ? where dealid = ? and publish != ? ",array($deal_status,$itemdet['dealid'],$deal_status));
				if($this->db->affected_rows())
				{
					// check for deal status by item product availablity
					$arr = array($itemid,$deal_status,"updated via vendor api stock status",$this->api_user);
					$this->db->query("insert into t_deal_publish_changelog (item_id,is_publish,remarks,created_by,created_on) values (?,?,?,?,now()) ",$arr);
				}
						// check and update offer price
						if($item['mrp'])
						{
							//$o_item_det = $this->db->query('select * from king_dealitems where id = ? ',$itemdet['id'])->row_array();
							$member_price_det = array('price'=>$item['price'],'mp_frn_max_qty'=>2,'mp_mem_max_qty'=>2,'mp_max_allow_qty'=>10);
							$this->erpm->_upd_deal_price_detail($this->api_user,$itemdet['id'],$item['mrp'],$item['mrp'],$member_price_det);
						}
					}
				}
			}
		}

	}
	
	function chk()
	{
		$data = $this->_map_filedata(201,'stock_list');
		print_r($data);
	}

	/**
	 * function to return filedata
	 *
	 * @param unknown_type $tmplid
	 * @param unknown_type $type
	 * @return unknown
	 */
	function _map_filedata($vendor_id,$type)
	{
		error_reporting(E_ALL);
		$v_tmpl_res = $this->db->query("select *,a.id as resource_id
				from m_vendor_api_resources a
				join m_vendor_api_templates b on a.mapping_tmpl_id = b.id
				join m_vendor_api_access c on c.id = a.api_access_id
				where a.vendor_id=? and module_type = ? ",array($vendor_id,$type));
		$v_tmpl_det = $v_tmpl_res->row_array();

		//get the batch id from temp table.
		$batch_id_det=$this->db->query("select max(batch_id) as b from m_vendor_api_datalog;")->row_array();
		
		$batch_id_det['b']=$batch_id_det['b']+1;
		
		
		//set the count log data
		$product_import_count['vendor_id']=$vendor_id;
		
		if(empty($v_tmpl_det ))
			$this->errors('Given vendor resources details not found');

		$file_data = array();
		$resource_id=$v_tmpl_det['resource_id'];
		
		// get file data by protocol
		if($v_tmpl_det['access_protocol'] == 'FTP')
		{
			$ftp_settings = array();
			$ftp_settings['hostname'] = $v_tmpl_det['host_name'];
			$ftp_settings['username'] = $v_tmpl_det['host_username'];
			$ftp_settings['password'] = $v_tmpl_det['host_pwd'];
			$ftp_settings['source_path'] = $v_tmpl_det['local_filepath'];
			$ftp_settings['file_format'] = $v_tmpl_det['file_format'];
			$ftp_settings['ftp_file_path'] = $v_tmpl_det['access_path'];

			$d_filename = $this->ftp_download($ftp_settings);
			if(!$d_filename)
				die("All files processed");

			$local_filepath = $v_tmpl_det['local_filepath'].'/'.$d_filename;

			//set the count log data
			$product_import_count['file_name']=$d_filename; 

		}else if($v_tmpl_det['access_protocol'] == 'HTTP')
		{
			//prepare the http file link and file name
			$file_basename=$this->_prepare_filename($v_tmpl_det['filename_pattern']);
			$filetype=$v_tmpl_det['file_format'];
			$filename = $file_basename.'.'.strtolower($filetype);
			$filepath=$v_tmpl_det['api_link'].'/'.$filename;
			$filedata=file_get_contents(str_ireplace(' ','%20',$filepath));
			$local_filepath=$v_tmpl_det['local_filepath'].'/'.$filename;
			$file = fopen($local_filepath,"w");
			fwrite($file,$filedata);
			fclose($file);
			
			//set the count log data
			$product_import_count['file_name']=$filename;
		}
		
		 
		$template_det=$this->db->query("select * from m_vendor_api_templates where id=?",array($v_tmpl_det['mapping_tmpl_id']))->row_array();
		
		//if(empty($template_det))
			//$this->errors('Given vendor deal import template not found');

		$configured_vendor_header=array();
		$map_headers = array();
		$data_arr=array();
		$grouped_data=array();
		$ttl_prd_count=1;
		
		foreach($template_det as $k=>$t)
		{
			if($t!=null && $t!='' && $k!='id')
				$configured_vendor_header[trim($k)]=trim($t);
			$map_headers[str_ireplace(' ','_', strtolower(trim($t)))] = trim($k);
			$template_det[$k]=str_ireplace(' ','_', strtolower(trim($t)));
		}

		// download data
		if($v_tmpl_det['file_format']=='XML')
		{

			$xml_data_arr=simplexml_load_file($local_filepath);

			
			$d=0;
			foreach($xml_data_arr->product as $i=>$data)
			{
				
				//validate headers
				if($d==0)
				{
					foreach($data as $k=>$h)
						$current_vendor_header[]=$k;

					//if(count($current_vendor_header)!=count($configured_vendor_header))
						//$this->errors('Given vendor deal import template not matiching');
				}

				$tmp = $template_det;
				foreach($data as $k=>$h)
				{
					$fld = strtolower(str_replace(' ','_',$k));
					//if(!isset($tmp[$map_headers[strtolower(str_replace(' ','_',$k))]]))
						//$this->errors('The new header found in given vendor template');
					if(!isset($map_headers[$fld]))
						continue;

					//get the data and push to our erp template using the vendor tempate,map header array contains index of vendor tepmplate fields and value of erp template fields
					$tmp[$map_headers[$fld]]=(String)$data->$k;
				}

				//prepare the description
				/*$tmp=$this->prepare_description($tmp,$template_det);
				$data_arr[] = $tmp;*/

				 

				$d++;
				
				if($type == 'stock_list')
				{
					// check if producy sku is available
					if(!$this->db->query("select count(*) as t from m_vendor_product_link where vendor_product_code = ? and vendor_id = ? ",array($tmp['sku_code'],$vendor_id))->row()->t)
						continue;
				}
				$ttl_prd_count=$ttl_prd_count+1;
				
				//to insert the one row the data to temp table
				$tmp['vendor_id']=$vendor_id;
				$tmp['resource_id']=$resource_id;
				$tmp['import_filename']=$product_import_count['file_name'];
				$tmp['imported_on']=cur_datetime();
				$tmp['group_id']=($tmp['group_id'])?$tmp['group_id']:$tmp['sku_code'];
				$tmp['batch_id']=$batch_id_det['b'];
				unset($tmp['id']);
				
				$this->db->insert("m_vendor_api_datalog",$tmp);

			}

		}else if($v_tmpl_det['file_format']=='CSV')
		{
			$deal_list=array();
			$keys=array();
			$report=array();
			
			//$csv_file=file_get_contents(str_ireplace(' ','%20',$local_filepath));
			$i=0;
			if (($handle = fopen(str_ireplace(' ','%20',$local_filepath), "r")) !== FALSE) {
				while (($deals = fgetcsv($handle)) !== FALSE) {
					$i++;

					if($i==1)
					{
						$keys=$deals;
						/*
						if(count($configured_vendor_header)!=count($deals))
							$this->errors('Given vendor deal import template not matiching');
							*/
						continue;
					}

					$data=array();
					foreach($deals as $c=>$d)
						$data[str_ireplace(' ','_',strtolower($keys[$c]))]=$d;


					$tmp = $template_det;
					foreach($data as $k=>$h)
					{
						/*
						if(!isset($tmp[$map_headers[strtolower(str_replace(' ','_',$k))]]))
							$this->errors('The new header foound in given vendor template');
						*/
						//get the data and push to our erp template using the vendor tempate,map header array contains index of vendor tepmplate fields and value of erp template fields
						$tmp[$map_headers[strtolower(str_replace(' ','_',$k))]]=(String)$h;
					}

					if($type == 'stock_list')
					{
						// check if producy sku is available 
						if(!$this->db->query("select count(*) as t from m_vendor_product_link where vendor_product_code = ? and vendor_id = ? ",array($tmp['sku_code'],$vendor_id))->row()->t)
							continue;
					}
					
					$ttl_prd_count=$ttl_prd_count+1;
					
					//to insert the one row the data to temp table
					$tmp['vendor_id']=$vendor_id;
					$tmp['resource_id']=$resource_id;
					$tmp['import_filename']=$product_import_count['file_name'];
					$tmp['imported_on']=cur_datetime();
					$tmp['group_id']=($tmp['group_id'])?$tmp['group_id']:$tmp['sku_code'];
					$tmp['batch_id']=$batch_id_det['b'];
					unset($tmp['id']);
					$this->db->insert("m_vendor_api_datalog",$tmp);
					
					//prepare the description
					/*if(isset($template_det['long_description']))
						$tmp=$this->prepare_description($tmp,$template_det);
					$data_arr[] = $tmp;*/


				}
			}
		}
		
		//set the count log data
		$product_import_count['total_products']=$ttl_prd_count;

		/*//product will group by group id for group product insertion,if not goup proudct it will goup by sku code or  other any code and will insert normal deal
		if($data_arr)
		{
			foreach($data_arr as $d)
			{
				if($d['group_id'])
				{
					if(!isset($grouped_data[$d['group_id']]))
						$grouped_data[$d['group_id']]=array();

					$grouped_data[$d['group_id']][]=$d;

				}else if($d['sku_code'])
				{
					if(!isset($grouped_data[$d['sku_code']]))
						$grouped_data[$d['sku_code']]=array();

					$grouped_data[$d['sku_code']][]=$d;
				}
			}
		}*/
		
		if($v_tmpl_det['access_protocol'] == 'FTP')
		{
			$ftp_settings = array();
			$ftp_settings['hostname'] = $v_tmpl_det['host_name'];
			$ftp_settings['username'] = $v_tmpl_det['host_username'];
			$ftp_settings['password'] = $v_tmpl_det['host_pwd'];
			$ftp_settings['file_format'] = $v_tmpl_det['file_format'];
			$ftp_settings['ftp_file_path'] = $v_tmpl_det['access_path'];
			$ftp_settings['move_filename'] = $d_filename;
			 
			$status = $this->ftp_movefile($ftp_settings);
		
		}

		//get total deals;
		$ttl_deals=$this->db->query("select count(distinct group_id) as ttl_deal from m_vendor_api_datalog where batch_id=? and vendor_id=? and  import_status=0 ",array($batch_id_det['b'],$vendor_id))->row()->ttl_deal;
		
		//set the count log data
		$product_import_count['total_deals']=$ttl_deals;
		$product_import_count['created_on']=cur_datetime();
		
		$this->db->insert("t_vendor_product_import_counts",$product_import_count);
		$this->count_log_id=$this->db->insert_id();
		
		return array('batch_id'=>$batch_id_det['b'],"ttl_deals"=>$ttl_deals);
	}

	/**
	 * function for process of deal import
	 */
	function deal_import_process($vendor_id)
	{
		//get deal bulk uploade file
		$vendor_api_det=$this->db->query("select * from m_vendor_api_resources a left join m_vendor_api_access b on a.api_access_id = b.id where a.vendor_id=? and a.module_type='product_catalog' ",$vendor_id)->row_array();

		if(empty($vendor_api_det))
			$this->errors('Given vendor deal import api info not found');

		//ftp access details assigning to variable
		$access_path=$vendor_api_det['access_path'];
		$file_basename=$this->_prepare_filename($vendor_api_det['filename_pattern']);
		$access_type=$vendor_api_det['access_type'];
		$vendor_api_det['filename_pattern'];
		$host_name=$vendor_api_det['host_name'];
		$host_username=$vendor_api_det['host_username'];
		$host_pwd=$vendor_api_det['host_pwd'];

		//file type details
		$filetype=$vendor_api_det['file_format'];
		$template_id=$vendor_api_det['mapping_tmpl_id'];
		$filename = $file_basename.'.'.strtolower($filetype);

		if(!$access_path)
			$this->errors('Given vendor deal import api not found');

		//$data_list=$this->get_parsed_data($access_path.'/'.$filename,$filetype,$template_id,$vendor_api_det);
		//$data_list=$this->get_parsed_data($access_path.'/'.$filename,$filetype,$template_id,$vendor_api_det);
		$data_map_det=$this->_map_filedata($vendor_id,'product_catalog');

		if($data_map_det['batch_id'] && $data_map_det['ttl_deals'])
		{
			for($i=1;$i<=$data_map_det['ttl_deals'];$i++)
			{
				$data_list=$this->get_imported_tempdata($data_map_det['batch_id'],$vendor_id);
				if($data_list)
		$this->_import_products($data_list,$vendor_id);
	}
		}
		
		
	}


	function run_imp()
	{
		for($i=1;$i<=8000;$i++)
		{
			$data_list=$this->get_imported_tempdata(1885,215);
			if($data_list)
				$this->_import_products($data_list,215);
		}
	}


	/**
	 * function deal import process
	 * @param unknown_type $data_list
	 */
	private function _import_products($deal_list,$client_id,$import_upt=0,$grp_v_link=array())
	{
		//check deal import log update flag set
		if($import_upt)
		{
			if(empty($grp_v_link))
				return 0;

			if(empty($deal_list))
				return 0;
		}

		$report=array();
		$p=1;
		$e=0;

		foreach($deal_list as $c=> $dl)
		{
			$log_id=0;
			//get the client id if it id deal import log update process
			if($import_upt)
			{
				$client_id=$grp_v_link[$c]['vendor_id'];
				$log_id=$grp_v_link[$c]['log_id'];
			}

			$log_group_id=$c;//get the goup id from log table.using for error updating.
			$prc_res=array('validation'=>0,'data'=>'','type'=>'new_data_import','msg'=>array());
			$pids=array();
			$multi_imgs=array();
			$update_count_log=array("total_deals_inserted"=>0,"total_error_flaged"=>0);
			foreach($dl as $d)
			{
				$product_name=$d['product_name'];
				$sku=$d['sku_code'];
				$product_code=$d['product_code'];
				$model_code=$d['model_code'];
				$qty=$d['qty'];
				$short_desc=$d['short_description'];
				$cat1=$d['category'];
				$cat2=$d['category1'];
				$cat3=$d['category2'];
				$cat4=$d['category3'];
				$cat5=$d['category4'];
				$brand=$d['brand'];
				$menu=$d['menu'];
				$measurement=$d['unit_of_measurement'];
				$mrp=$d['mrp'];
				$offer_price=$d['offer_price'];
				$vat=$d['vat'];
				$purchase_cost=$d['purchase_cost'];
				$barcode=$d['barcode'];
				$group_id=$d['group_id'];
				$gender=$d['gender_attr'];
				$description=$d['long_description'];
				$main_img_url=$d['main_image'];
				$multi_imgs[]=$d['image1'];
				$multi_imgs[]=$d['image2'];
				$multi_imgs[]=$d['image3'];
				$multi_imgs[]=$d['image4'];
				$multi_imgs[]=$d['image5'];
				$color_attr=$d['color'];
				$size_attr=$d['size'];
				$attr1=$d['attr1'];
				$attr2=$d['attr2'];
				$is_group=0;
				$grp_exst_prd=0;
				$grp_exst_itmeid=0;
				$attributes=array();

				$logId_det_res=$this->db->query("select id from t_vendor_product_import_log where vendor_id=? and group_id=?",array($client_id,$log_group_id));

				if($logId_det_res->num_rows() && !$log_id)
				{
					$log_id=$logId_det_res->row()->id;
				}

				//hardcode want to change
				$vat='5.5';

				//tile
				$new_name=$brand.' '.trim($product_name);
				$deal_name=$brand.' '.trim($product_name);

				/*if($group_id && $color_attr && $size_attr)
				{
					$new_name.=" ".$color_attr.' ('.$group_id.')-size:'.$size_attr;
					$deal_name.=" ".$color_attr.' ('.$group_id.')';
				}*/

				//attributes preparetion
				for($a=1;$a<$this->attr_fields_len;$a++)
				{
					if(isset($d['attr'.$a]) && $d['attr'.$a] && $d['attr'.$a]!='NA')
						$attributes['attr'.$a]=$d['attr'.$a];
				}
				
				//prepare category
				$main_cat='';
				$cat='';
				if($cat5 && $cat5!='NA' && $cat5!=null)
				{
					$cat=$cat5;
					$main_cat=$cat1;
				}
				else if($cat4 && $cat4!='NA' && $cat4!=null)
				{
					$cat=$cat4;
					$main_cat=$cat1;
				}
				else if($cat3 && $cat3!='NA' && $cat3!=null)
				{
					$cat=$cat3;
					$main_cat=$cat1;
				}
				else if($cat2 && $cat2!='NA' && $cat2!=null)
				{
					$cat=$cat2;
					$main_cat=$cat1;
				}
				else if($cat1 && $cat1!='NA' && $cat1!=null)
					$cat=$cat1;
				else 
					$this->errors('Category field empty in given file');
				
				
				
				
				

				//log data preparing
				$e=array($c=>$deal_list[$c]);
				
				$prc_res['data']=json_encode($e);

				if(!$brand or $brand==null)
				{
					$default_brand_menu=$this->db->query("select default_menu,default_brand from m_vendor_api_resources where vendor_id=?",$client_id)->row_array();
					if($default_brand_menu['default_brand'])
						$brand=$default_brand_menu['default_brand'];
					else
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]="Brand not found";
					}
				}

				if(!$menu or $menu==null)
				{
					$default_brand_menu=$this->db->query("select default_menu,default_brand from m_vendor_api_resources where vendor_id=?",$client_id)->row_array();
					if($default_brand_menu['default_menu'])
						$menu=$default_brand_menu['default_menu'];
					else
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]="Menu not found";
					}
				}

				//get brand id
				$brand_det=$this->db->query("select a.* from m_client_brand_link a join king_brands b on a.brand_id=b.id where client_brand=?",$brand)->row_array();

				if(!count($brand_det))
				{

					// check if brand is already
					$this->db->query("insert into m_client_brand_link (brand_id,client_brand,created_on)  (select id,name,now() from king_brands where name = ? order by id desc limit 1); ",array($brand));
					if($this->db->affected_rows())
					{
						$brand_det=$this->db->query("select a.* from m_client_brand_link a join king_brands b on a.brand_id=b.id where client_brand=?",$brand)->row_array();
					}else
					{
						if(!$this->db->query("select count(*) as ttl from m_client_brand_link where client_brand=?",$brand)->row()->ttl)
							$this->db->query("insert into m_client_brand_link(brand_id,client_brand,created_on)values(0,?,now())",array($brand));

						$prc_res['validation']=1;
						$prc_res['msg'][]=$brand." Vendor Brand is not linked in our erp brand";
					}

				}

				//check clint brand linked in our erp brand
				if(count($brand_det))
				{
					if($brand_det['brand_id']==0)
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]=$brand." Vendor Brand is not linked in our erp brand";
					}
				}

				//get category id
				$category_det=$this->db->query("select b.name as erp_cat,a.* from m_client_category_link a join king_categories b on b.id=a.category_id where client_category=?",$cat)->row_array();

				
				if(!count($category_det))
				{
					if(!$this->db->query("select count(*) as ttl from m_client_category_link where client_category=? and main_category=?",array($cat,$main_cat))->row()->ttl)
						$this->db->query("insert into m_client_category_link(category_id,client_category,created_on,main_category)values(0,?,now(),?)",array($cat,$main_cat));

					$prc_res['validation']=1;
					$prc_res['msg'][]=$cat." category is not linked in our erp category";
				}

				if(count($category_det))
				{
					if($category_det['category_id']==0)
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]=$cat." category is not linked in our erp category";
					}
				}

				//get menu det
				$menu_det=$this->db->query("select a.menu_id as menu_id from m_client_menu_link a join pnh_menu b on b.id=a.menu_id where client_menu=?",$menu)->row_array();

				if(empty($menu_det))
				{
					if(!$this->db->query("select count(*) as ttl from m_client_menu_link where client_menu=?",$menu)->row()->ttl)
						$this->db->query("insert into m_client_menu_link(menu_id,client_menu,created_on)values(0,?,now())",array($menu));

					$prc_res['validation']=1;
					$prc_res['msg'][]=$menu." menu is not linked in our erp menu";
				}

				if(count($menu_det))
				{
					if($menu_det['menu_id']==0)
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]=$menu." menu is not linked in our erp menu";
					}
				}

				$category_attr=array();
				/*//get category attributes details
				if($category_det && $group_id && ($color_attr or $size_attr))
				{
					$category_attr=$this->db->query("select b.* from king_categories a join m_attributes b on find_in_set(b.id,a.attribute_ids) where a.id=?;",$category_det['category_id'])->result_array();
					$is_group=1;//if all attr are set deal will flag group deal;
					if(empty($category_attr))
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]="Attributes not linked for category ".$category_det['erp_cat'];
					}else {

						foreach($category_attr as $ca)
						{
							if(!isset($d[strtolower($ca['attr_name'])]))
							{
								$prc_res['validation']=1;
								$prc_res['msg'][]='Our category '.$category_det['erp_cat'].' attribute '.$ca['attr_name']." not found in vendor details";
							}

							if(isset($d[strtolower($ca['attr_name'])]))
							{
								if($d[strtolower($ca['attr_name'])]=='' || $d[strtolower($ca['attr_name'])]=='NA')
								{
									$prc_res['validation']=1;
									$prc_res['msg'][]='Our category '.$category_det['erp_cat'].' attribute '.$ca['attr_name']."value not found in vendor details";
								}
							}
						}
					}
				}

				//validate if attribute have in vendor but not in our erp
				//check size and color attribute is linked category or not in our erp
				$color_attr;
				$size_attr;
				
				if(($size_attr or $color_attr) && $category_det)
				{
					$category_attr=$this->db->query("select b.* from king_categories a join m_attributes b on find_in_set(b.id,a.attribute_ids) where a.id=?;",$category_det['category_id'])->result_array();
					$is_group=1;//if all attr are set deal will flag group deal;
					if(empty($category_attr))
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]="Attributes not linked for category ".$category_det['erp_cat'];
					}else 
					{
						$erp_cat_attr=array();
						foreach($category_attr as $ca)
							$erp_cat_attr[]=$ca['attr_name'];
							
							//check vendor size attrbute is present our erp category
							if($size_attr)
							{
							if(!in_array('size',$erp_cat_attr))
								{
									$prc_res['validation']=1;
									$prc_res['msg'][]="Vendor size attribute not found our erp category ".$category_det['erp_cat'];
								}
							}
							
							//check vendor color attrbute is present our erp category
							if($color_attr)
							{
							if(!in_array('color',$erp_cat_attr))
								{
									$prc_res['validation']=1;
									$prc_res['msg'][]="Vendor color attribute not found our erp category ".$category_det['erp_cat'];
								}
							}
						}
				}*/
				
				//validating attributes
				if(!empty($attributes) && $category_det)
				{
					//if attribute set and category det set then deal marked as group product
					$is_group=1;
					$category_attr=$this->db->query("select b.* from king_categories a join m_attributes b on find_in_set(b.id,a.attribute_ids) where a.id=?;",$category_det['category_id'])->result_array();
					if(empty($category_attr))
					{
						$prc_res['validation']=1;
						$prc_res['msg'][]="Any Attributes not linked for category ".$category_det['erp_cat'];
						
					}else{
						
						$cat_link_attr=array();
						$attr_val_link=array();
						//get category linked attributes
						foreach($category_attr as $ct)
							array_push($cat_link_attr,$ct['id']);
						
						//get configured vendor attributes linking det
						$vendor_attr_link_res=$this->db->query("select va.*,a.attr_name from m_vendor_attributes_link va join m_attributes a on a.id=va.attr_id where vendor_id=?",$client_id);
						
						if(!$vendor_attr_link_res->num_rows())
						{
							$prc_res['validation']=1;
							$prc_res['msg'][]="Vendor attributes linking configuration not found";
						
						}else{
							$linked_attr_fields=array();
							$vendor_attr_link=$vendor_attr_link_res->result_array();
							
							foreach($vendor_attr_link as $lattr)
							{
								array_push($linked_attr_fields,$lattr['attr']);
								
								if(isset($attributes[$lattr['attr']]))
								{
									//check attribute is linked to category
									if(!in_array($lattr['attr_id'], $cat_link_attr) && isset($attributes[$lattr['attr']]))
									{
										$prc_res['validation']=1;
										$prc_res['msg'][]="Attribute ".$lattr['attr_name']." is not linked to category ".$category_det['erp_cat'];
									
									}else{
										$attr_val_link[$lattr['attr_id']]=$attributes[$lattr['attr']];
										
										if($group_id && $lattr['attr_name']=='size')
										{
											$new_name.=' ('.$group_id.')-size:'.$attributes[$lattr['attr']];
											$deal_name.=' ('.$group_id.')';
										}
									}		
								}
							}
							
							//check coming attributes are present in linking table
							foreach($attributes as $f=>$a)
							{
								if(!in_array($f,$linked_attr_fields))
								{
									$prc_res['validation']=1;
									$prc_res['msg'][]="Attribute field ".$a." is not present in vendor attribute linking";
								}
							}
						}
						
						if(empty($attr_val_link))
						{
							$prc_res['validation']=1;
							$prc_res['msg'][]="Attribute field linking problem";
						}
					}
				
				} 
				
				
				
				//check this group product already in db
				$group_exist_con='';
				if(!empty($pids))
					$group_exist_con.=' and product_id not in ('.implode(",",$pids).')';
				
				$group_exist=$this->db->query("select product_id from m_vendor_product_link where vendor_group_no=? and vendor_id=? and  vendor_group_no!=? ".$group_exist_con." order by id desc limit 1;",array($group_id,$client_id,$sku))->result_array();
				if($group_exist)
				{
					$exst_deal_det=$this->db->query("select itemid from m_product_deal_link where product_id=?",$group_exist[0]['product_id'])->result_array();
					if($exst_deal_det)
					{
						$grp_exst_prd=1;
						$grp_exst_itmeid=$exst_deal_det[0]['itemid'];
					}
					
				}
				
				if($this->db->query("select 1 from m_product_info where product_name=? or sku_code=?",array($new_name,$sku))->num_rows()!=0)
				{
				
					$prc_res['validation']=1;
					$prc_res['msg'][]=' Duplicate name for product : '.$new_name;
				}
				
				//deal adding process validation
				if($this->db->query("select 1 from king_dealitems where name=? and is_pnh=1 limit 1",$deal_name)->num_rows()!=0 && $grp_exst_prd==0 && $grp_exst_itmeid==0)
				{
					$prc_res['validation']=1;
					$prc_res['msg'][]="Duplicate deal name ".$deal_name;
				}


				if($prc_res['validation']==0)
				{
					//product creation block
					$inp=array();
					$inp['product_code']="P".rand(10000,99999);
					$inp['product_name']=trim($new_name);
					$inp['sku_code']=$sku;
					$inp['product_cat_id']=$category_det['category_id'];
					$inp['brand_id']=$brand_det['brand_id'];
					$inp['mrp']=$mrp;
					$inp['vat']=$vat;
					$inp['purchase_cost']=$mrp;
					$inp['is_sourceable']=0;
					$inp['created_on'] = date('Y-m-d H:i:s');
					$inp['created_by'] = $this->api_user;
					$pic='';

					$this->db->insert("m_product_info",$inp);
					$pid=$this->db->insert_id();
					array_push($pids,$pid);

					//product attribute inert
					if(!empty($category_attr))
					{
						foreach($category_attr as $ca)
						{
							$prd_attr_inp=array();
							$prd_attr_inp['pid']=$pid;
							$prd_attr_inp['pcat_id']=$category_det['category_id'];
							$prd_attr_inp['attr_id']=$ca['id'];
							$prd_attr_inp['attr_value']=isset($attr_val_link[$ca['id']])?$attr_val_link[$ca['id']]:'';
							$this->db->insert("m_product_attributes",$prd_attr_inp);
						}
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
					$cpl_inp['vendor_id']=$client_id;
					$cpl_inp['product_id']=$pid;
					$cpl_inp['vendor_group_no']=($group_id!=$sku)?$group_id:'';
					$cpl_inp['vendor_product_code']=$sku;
					$cpl_inp['mrp']=$mrp;
					$cpl_inp['created_on']=date('Y-m-d H:i:s');
					$cpl_inp['created_by']=$this->api_user;
					$this->db->insert("m_vendor_product_link",$cpl_inp);

					if(!$import_upt)
						echo '<b>'.$p.'</b> Product added."<br>"';

					$p=$p+1;
				}
			}

			$process_log_inp=array();
			$process_log_inp['type']=$prc_res['type'];
			$process_log_inp['data']=$prc_res['data'];
			$process_log_inp['msg']=implode(',',$prc_res['msg']);
			$process_log_inp['group_id']=$log_group_id;//if group id set then insert group id other wise default group id

			if(!$import_upt && !$log_id)
				$process_log_inp['created_on']=date('Y-m-d H:i:s');
			else
				$process_log_inp['modified_on']=date('Y-m-d H:i:s');

			$process_log_inp['vendor_id']=$client_id;


			//deal creation block
			if(!empty($pids) && $prc_res['validation']==0)
			{
				$itemid=$this->erpm->p_genid(10);
				$dealid=$this->erpm->p_genid(10);
				$publish=0;
				$shipsin='48-72 hrs';
				$gender_attr=($gender)?$gender:'';
				$name=trim($deal_name);
				$print_name=$deal_name;
				$max_allowed_qty=100;
				$mrp=$mrp;
				if(!$d['offer_price'] || !isset($d['offer_price']))
				$price=$mrp;
				else
					$price=$d['offer_price'];

				$store_price=$price;
				$nyp_price=$price;
				$tax=$vat;
				$multi_imgs = array_filter(array_unique($multi_imgs));


				//inerting the images
				$img_inp=array();
				$img_inp['vendor_id']=$client_id;
				$img_inp['item_id']=$itemid;
				$img_inp['main_image']=$main_img_url;
				$img_inp['other_images']=implode(',',$multi_imgs);
				$img_inp['created_on']=cur_datetime();
				$this->db->insert("m_vendor_product_images",$img_inp);

				if($grp_exst_prd==0 && $grp_exst_itmeid==0 )
				{
				$keywords=str_ireplace(array('(',')','-'),'',str_ireplace(' ',',',$deal_name));
				//genrate a pnh id
				/*$pnh_id=$this->db->query("select pnh_id+1 as new_pnh_id
						from
						(
						select a.pnh_id,(select pnh_id from king_dealitems b where b.pnh_id = a.pnh_id+1 limit 1) as has_next
						from king_dealitems a
						where a.pnh_id != 0 and length(a.pnh_id) = 8
						order by a.pnh_id ) as g
						where has_next is null
						order by pnh_id limit 1 ")->row()->new_pnh_id;*/
				$pnh_id=$this->erpm->gen_pnhid();
				$inp=array("dealid"=>$dealid,"catid"=>$cid,"brandid"=>$bid,"pic"=>$pic,"description"=>$description,"publish"=>$publish,"menuid"=>$menu_id,"keywords"=>$keywords,"created_on"=>cur_datetime());
				$this->db->insert("king_deals",$inp);

				$inp=array("id"=>$itemid,"shipsin"=>$shipsin,"gender_attr"=>$gender_attr,"dealid"=>$dealid,"name"=>$name,"print_name"=>$print_name,"max_allowed_qty"=>$max_allowed_qty,"pic"=>$pic,"orgprice"=>$mrp,"price"=>$price,"store_price"=>$store_price,"nyp_price"=>$nyp_price,"is_pnh"=>1,"pnh_id"=>$pnh_id,"tax"=>$tax*100,"live"=>1,"is_group"=>$is_group,"created_on"=>cur_datetime());
				$this->db->insert("king_dealitems",$inp);

					$update_count_log['total_deals_inserted']+=1;
				
				}else if($grp_exst_prd && $grp_exst_itmeid)
				{
					$itemid=$grp_exst_itmeid;
					$pnh_id=0;
				}

				foreach($pids as $pid)
				{
					$qty=1;
					$inp=array("itemid"=>$itemid,"product_id"=>$pid,"product_mrp"=>$this->db->query("select mrp from m_product_info where product_id=?",$pid)->row()->mrp,"qty"=>$qty,"created_on"=>cur_datetime());
					$this->db->insert("m_product_deal_link",$inp);
				}

				$report[]=array($itemid,$dealid,$name,$pnh_id);

				
				
				$process_log_inp['status']=1;

				if(!$import_upt && !$log_id)
					$this->db->insert("t_vendor_product_import_log",$process_log_inp);
				else{
					$this->db->where('id', $log_id);
					$this->db->update('t_vendor_product_import_log', $process_log_inp);
				}

			}else{

				$update_count_log['total_error_flaged']+=1;
				$process_log_inp['status']=2;

				if(!$import_upt && !$log_id)
					$this->db->insert("t_vendor_product_import_log",$process_log_inp);
				else{
					$this->db->where('id', $log_id);
					$this->db->update('t_vendor_product_import_log', $process_log_inp);
				}
			}
		}
		$this->db->where('id', $this->count_log_id);
		$this->db->update('t_vendor_product_import_counts', $update_count_log);

		$this->db->insert("deals_bulk_upload",array("items"=>count($report),"created_on"=>time()));

		if($import_upt)
			return 1;
	}

	/**
	 * function for get the vendor deal data by vendor api info
	 * @param unknown_type $filepath
	 * @param unknown_type $filetype
	 * @param unknown_type $templateid
	 */
	private function get_parsed_data($filepath,$filetype,$templateid,$api_det)
	{
		$this->load->model('api_model','apim');
		//get template det
		$template_det=$this->db->query("select * from m_vendor_api_templates where id=?",array($templateid))->row_array();

		if(empty($template_det))
			$this->errors('Given vendor deal import template not found');



		// download file via
		if($api_det['access_protocol'] == 'FTP')
		{
			$ftp_settings = array();
			$ftp_settings['hostname'] = $api_det['host_name'];
			$ftp_settings['username'] = $api_det['host_username'];
			$ftp_settings['password'] = $api_det['host_pwd'];
			$ftp_settings['source_path'] = $api_det['local_filepath'];


			$ftp_settings['ftp_file_path'] = $filepath;

			$d_filename = $this->ftp_download($ftp_settings);

			$local_filepath = $ftp_settings['source_path'].'/'.$d_filename;

		}else if($api_det['access_protocol'] == 'HTTP')
		{

			$local_filepath = 'resources/vendor_files/catalog/'.$d_filename;

			$filedata=file_get_contents(str_ireplace(' ','%20',$filepath));

			$file = fopen($local_filepath,"w");
			fwrite($file,$filedata);
			fclose($file);

		}

		$configured_vendor_header=array();
		$map_headers = array();
		foreach($template_det as $k=>$t)
		{
			if($t!=null && $t!='' && $k!='id')
				$configured_vendor_header[$k]=$t;
			$map_headers[$t] = $k;
		}
			
		$current_vendor_header=array();
		$data_arr=array();
		$grouped_data=array();

		echo $local_filepath;
		exit;
		// download data
		if($filetype=='XML')
		{
			
			$xml_data_arr=simplexml_load_file($local_filepath);

			$d=0;
			foreach($xml_data_arr->product as $i=>$data)
			{
				//validate headers
				if($d==0)
				{
					foreach($data as $k=>$h)
						$current_vendor_header[]=$k;

					if(count($current_vendor_header)!=count($configured_vendor_header))
						$this->errors('Given vendor deal import template not matiching');
				}

				$tmp = $template_det;
				foreach($data as $k=>$h)
				{
					if(!isset($tmp[$map_headers[strtolower(str_replace(' ','_',$k))]]))
						$this->errors('The new header found in given vendor template');

					//get the data and push to our erp template using the vendor tempate,map header array contains index of vendor tepmplate fields and value of erp template fields
					$tmp[$map_headers[strtolower(str_replace(' ','_',$k))]]=(String)$data->$k;
				}
					
				//prepare the description
				$tmp=$this->prepare_description($tmp,$template_det);

				$data_arr[] = $tmp;
				$d++;
					
			}

		}else if($filetype=='CSV')
		{
			$deal_list=array();
			$keys=array();
			$report=array();
			//$csv_file=file_get_contents(str_ireplace(' ','%20',$local_filepath));
			$i=0;
			if (($handle = fopen(str_ireplace(' ','%20',$local_filepath), "r")) !== FALSE) {
				while (($deals = fgetcsv($handle)) !== FALSE) {
					$i++;

					if($i==1)
					{
						$keys=$deals;
						if(count($configured_vendor_header)!=count($deals))
							$this->errors('Given vendor deal import template not matiching');
						continue;
					}

					$data=array();
					foreach($deals as $c=>$d)
						$data[str_ireplace(' ','_',strtolower($keys[$c]))]=$d;


					$tmp = $template_det;
					foreach($data as $k=>$h)
					{
						if(!isset($tmp[$map_headers[strtolower(str_replace(' ','_',$k))]]))
							$this->errors('The new header foound in given vendor template');
						//get the data and push to our erp template using the vendor tempate,map header array contains index of vendor tepmplate fields and value of erp template fields
						$tmp[$map_headers[strtolower(str_replace(' ','_',$k))]]=(String)$h;
					}

					//prepare the description
					$tmp=$this->prepare_description($tmp,$template_det);

					$data_arr[] = $tmp;


				}
			}
		}

		//product will group by group id for group product insertion,if not goup proudct it will goup by sku code or  other any code and will insert normal deal
		if($data_arr)
		{
			foreach($data_arr as $d)
			{
				if($d['group_id'])
				{
					if(!isset($grouped_data[$d['group_id']]))
						$grouped_data[$d['group_id']]=array();

					$grouped_data[$d['group_id']][]=$d;

				}else if($d['sku_code'])
				{
					if(!isset($grouped_data[$d['sku_code']]))
						$grouped_data[$d['sku_code']]=array();

					$grouped_data[$d['sku_code']][]=$d;
				}
			}
		}

		return $grouped_data;
	}

	/**
	 * function for prepare description
	 */
	private function prepare_description($data,$configured_template)
	{
		$unwdata=array("c2c_");
		
		if(isset($data["long_description"]))
		{
			$specifications=array('spec1','spec2','spec3','spec4','spec5','spec6','spec7','spec8','spec9','spec10','spec11','spec12','spec13','spec14','spec15','spec16','spec17','spec18','spec19','spec20','spec21','spec22','spec23','spec24','spec25');
			$html_desc='';
			$html_desc.="<div class='description'>";
			$html_desc.=	"<p>".$data["long_description"]."<p>";
			$html_desc.=	"<ul>";
			foreach($specifications as $s)
			{
				//vendor specification name
				if(isset($configured_template[$s]))
				{
					$vendor_spec_name=str_ireplace("_"," ",str_ireplace($unwdata,'',$configured_template[$s]));
					$vendor_spec_value=$data[$s];

					if($vendor_spec_value)
						$html_desc.="<li><b>".$vendor_spec_name." :</b> ".$vendor_spec_value."</li>";
				}
				else
					continue;

			}
			$html_desc.=	"</ul>";
			$html_desc.="</div>";
			$data["long_description"]=$html_desc;
			return $data;

		}else
			return 0;
	}

	/**
	 * function for get the vendor ftp settings
	 */
	private function get_vendor_ftp_settings($type='',$vendorid=0)
	{
		$ftp_setting=array();

		if($vendorid)
		{

			$vendor_ftp_settings=array();

			/*
			 // fashionara ftp settings
			$vendor_ftp_settings['201']['hostname']='partnerftp.fashionara.com';
			$vendor_ftp_settings['201']['username']='storeking';
			$vendor_ftp_settings['201']['password']='wrangletitlewarncrosstell';

			// Catalog Feed Details
			$vendor_ftp_settings['201']['catalog_feed'] = array();
			$vendor_ftp_settings['201']['catalog_feed']['download_path'] = '/fashionara/catalog/';
			$vendor_ftp_settings['201']['catalog_feed']['upload_path'] = '/fashionara/catalog/processed/';
			$vendor_ftp_settings['201']['catalog_feed']['error_path'] = '/fashionara/catalog/error/';

			// Stock Feed Details
			$vendor_ftp_settings['201']['stock_feed'] = array();
			$vendor_ftp_settings['201']['stock_feed']['download_path'] = '/fashionara/stock/';
			$vendor_ftp_settings['201']['stock_feed']['upload_path'] = '/fashionara/stock/processed';
			$vendor_ftp_settings['201']['stock_feed']['error_path'] = '/fashionara/stock/error/';

			// Order Feed Details
			$vendor_ftp_settings['201']['order_feed'] = array();
			$vendor_ftp_settings['201']['order_feed']['download_path'] = '/storeking/new-orders/processed/';
			$vendor_ftp_settings['201']['order_feed']['upload_path'] = '/storeking/new-orders/';
			$vendor_ftp_settings['201']['order_feed']['error_path'] = '/storeking/new-orders/error/';
			*/

			$vendor_ftp_det_res = $this->db->query("select *
					from m_vendor_api_resources a
					join m_vendor_api_access b on a.api_access_id = b.id
					where a.vendor_id = ? and module_type = ?
					",array($vendorid,$type));

			if($vendor_ftp_det_res->num_rows())
				return $vendor_ftp_det_res->row_array();
			else
				return false;

		}else{
			return $ftp_setting;
		}
	}

	/**
	 * function for upload the files to ftp
	 */
	private function ftp_upload($ftp_settings)
	{
		//load the ftp library
		$uploaded=0;
		if($ftp_settings)
		{
			if((isset($ftp_settings['host_name']) && $ftp_settings['host_name']) && (isset($ftp_settings['host_username']) && $ftp_settings['host_username']) && (isset($ftp_settings['host_pwd']) && $ftp_settings['host_pwd']) && (isset($ftp_settings['destination_path']) && $ftp_settings['destination_path']))
			{
				
			 
				$this->load->library('ftp');
				$config['hostname'] = $ftp_settings['host_name'];
				$config['username'] = $ftp_settings['host_username'];
				$config['password'] = $ftp_settings['host_pwd'];
				$config['port']     = 21;
				$config['passive']  = FALSE;
				$config['debug']    = TRUE;
				$this->ftp->connect($config);
				$filename=basename($ftp_settings['source_path']);
				$uploaded=$this->ftp->upload($ftp_settings['source_path'],$ftp_settings['destination_path'].'/'.$filename, 'ascii', 0775);
				$this->ftp->close();
				return $uploaded;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}


	/**
	 * function for ftp files download
	 */
	private function ftp_download($ftp_settings)
	{
		if($ftp_settings)
		{
			
			if((isset($ftp_settings['hostname']) && $ftp_settings['hostname']) && (isset($ftp_settings['username']) && $ftp_settings['username']) && (isset($ftp_settings['password']) && $ftp_settings['password']) && (isset($ftp_settings['ftp_file_path']) && $ftp_settings['ftp_file_path']))
			{
				
				$ftp_conn = ftp_connect($ftp_settings['hostname']) or die("Could not connect to ".$ftp_settings['hostname']);
				$login = ftp_login($ftp_conn, $ftp_settings['username'], $ftp_settings['password']);

				$local_file = $ftp_settings['source_path'];
				$server_file = $ftp_settings['ftp_file_path'];
				

				// get file by filename;
				$server_filename = basename($ftp_settings['ftp_file_path']);
				
				$extn = end(explode('.',$server_filename));

				$file_names = array();
				$ftp_file_list = ftp_nlist($ftp_conn, $ftp_settings['ftp_file_path']);
				
				if(count($ftp_file_list))
				{
					foreach($ftp_file_list as $ftp_filename)
					{
						if(end(explode('.',$ftp_filename)) == strtolower($ftp_settings['file_format']))
							$file_names[] = $ftp_filename;
					}
				}
				//print_r($file_names);
				$downloaded=0;

				foreach($file_names as $fname)
				{
					if(stristr('/',$fname))
						$sfile_name = $fname;
					else
						$sfile_name = $ftp_settings['ftp_file_path'].'/'.basename($fname);

					//echo $ftp_settings['source_path'].'/'.basename($sfile_name).','.$sfile_name;

					// download server file
					if (ftp_get($ftp_conn, $ftp_settings['source_path'].'/'.basename($sfile_name),$sfile_name, FTP_ASCII))
						$downloaded=1;
					if($downloaded)
						break;
				}

				// close connection
				ftp_close($ftp_conn);
				
				//demo end
				return $downloaded?basename($fname):0;

			}else{
				return 0;
			}
		}else
		{
			return 0;
		}
	}
	
	
	/**
	 * function for moving the files within ftp folder 
	 */
	private function ftp_movefile($ftp_settings)
	{
		//load the ftp library
		$updated=0;
		if($ftp_settings)
		{
			if((isset($ftp_settings['hostname']) && $ftp_settings['hostname']) && (isset($ftp_settings['username']) && $ftp_settings['username']) && (isset($ftp_settings['password']) && $ftp_settings['password']))
			{
				$this->load->library('ftp');
				$config['hostname'] = $ftp_settings['hostname'];
				$config['username'] = $ftp_settings['username'];
				$config['password'] = $ftp_settings['password'];
				$config['port']     = 21;
				$config['passive']  = FALSE;
				$config['debug']    = TRUE;
				$this->ftp->connect($config);
				
				$filename=$ftp_settings['move_filename'];
				
				$updated=$this->ftp->move($ftp_settings['ftp_file_path'].'/'.$filename,$ftp_settings['ftp_file_path'].'/processed/'.$filename, 'ascii', 0775);
				$this->ftp->close();
				return $updated;
			}else{
				return 0;
			}
		}else{
			return 0;
		}
	}
	
	/**
	 * function for get the imported data from temp table
	 * @param unknown_type $batch_id
	 */
	private function get_imported_tempdata($batch_id,$vendor_id)
	{
		$grouped_data=array();
		$sql="select a.*,module_type,b.default_tax,b.default_margin,b.default_delivery_tat,b.default_brand,b.default_menu,b.default_category
					from m_vendor_api_datalog a 
					join (
							select group_id,sku_code 
								from m_vendor_api_datalog a 
								join m_vendor_api_resources b on a.resource_id = b.id 
								where import_status = 0 and a.vendor_id = ? and batch_id=? and group_id is not null 
								order by a.id  
								limit 1
						) as g on a.group_id = g.group_id 
					join m_vendor_api_resources b on a.resource_id = b.id and import_status = 0 
					having a.group_id is not null ";
		
		$data_res=$this->db->query($sql,array($vendor_id,$batch_id));
		
		if($data_res->num_rows())
		{
			$data_details=$data_res->result_array();
			
			foreach($data_details as $data)
			{
				$mapping_tmpl_id=$this->db->query("select mapping_tmpl_id from m_vendor_api_resources where vendor_id=?",$data['vendor_id'])->row()->mapping_tmpl_id;
				
				$template_det=$this->db->query("select * from m_vendor_api_templates where id=?",array($mapping_tmpl_id))->row_array();
				
				foreach($template_det as $k=>$t)
				{
					if($t!=null && $t!='' && $k!='id')
						$configured_vendor_header[trim($k)]=trim($t);
					$map_headers[str_ireplace(' ','_', strtolower(trim($t)))] = trim($k);
					$template_det[$k]=str_ireplace(' ','_', strtolower(trim($t)));
				}
				
				if(isset($data['long_description']))
					 $data=$this->prepare_description($data,$template_det);
				
				$this->db->query("update m_vendor_api_datalog set import_status=1,modified_on=? where import_status=0 and batch_id=? and vendor_id=? and id=?",array(cur_datetime(),$data['batch_id'],$data['vendor_id'],$data['id']));
				
				if($data['group_id'])
				{
					if(!isset($grouped_data[$data['group_id']]))
						$grouped_data[$data['group_id']]=array();
				
					$grouped_data[$data['group_id']][]=$data;
				
				}else if($data['sku_code'])
				{
					if(!isset($grouped_data[$data['sku_code']]))
						$grouped_data[$data['sku_code']]=array();
				
					$grouped_data[$data['sku_code']][]=$data;
				}
			}
		}
		
		return $grouped_data;
	}
}


