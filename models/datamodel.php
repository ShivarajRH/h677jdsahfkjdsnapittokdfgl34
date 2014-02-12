<?php

class Datamodel extends Model
{
	protected $get_params=array("type"=>"json","access_token"=>"","lock"=>"","key"=>"");
	protected $uris;

	function __construct()
	{
		parent::__construct();
		$this->get_params=$_REQUEST+$this->get_params;
	}
		
	function array2xml($data, &$xml) 
	{
	    foreach($data as $key => $value) {
	        if(is_array($value)) {
	            if(!is_numeric($key)){
	                $subnode = $xml->addChild("$key");
	                $this->dpm->array2xml($value, $subnode);
	            }
	            else
	                $this->dpm->array2xml($value, $xml);
	        }
	        else 
	        {
	        	$value=str_replace("&","&amp;",$value);
	            $xml->addChild("$key","$value");
	        }
	    }
	}
	
	function output_xml($data,$name)
	{
		$t_data=$data;
		$data=array();
		$s=0;
		foreach($t_data as $k=>$d)
		{
			if(is_array($d))
				$data[]['data']=$d;
			else
				$data[$k]=$d;
			$s++;
		}
		header("Content-Type: text/xml");
		$xml= new SimpleXMLElement("<data_response/>");
		$this->dpm->array2xml($data,$xml);
		return $xml->asXML();
	}
	
	function output($resp,$name="")
	{
		if($this->get_params['type']=="json")
			echo json_encode(array("data_response"=>$resp));
		else
			echo $this->dpm->output_xml($resp,$name);
		die;
	}
	
	function auth_token()
	{
		$token=$this->get_params['access_token'];
		if(empty($token) || $this->db->query("select 1 from data_api_tokens where token=? and expires_on>?",array($token,time()))->num_rows()==0)
			$this->dpm->output(array("error"=>"Invalid/expired access token. Please re-auth"));
	}
	
	function gen_token()
	{
		$lock=$this->input->post('lock');
		$key=$this->input->post('key');
		if(empty($key) || empty($lock))
			return array("error"=>"input kissing!");
		$key=md5($key);
		$auth=$this->db->query("select * from data_api_auth where `lock`=? and `key`=?",array($lock,$key))->row_array();
		if(empty($auth))
			return array("error"=>"Authentication failed. Invalid lock and key");
		$this->db->query("update data_api_auth set last_login=? where id=?",array(time(),$auth['id']));
		$token=md5(rand(4342343,2355353757777777).time().randomChars(20));
		$expires=time()+(1*3*60*60);
		$this->db->query("insert into data_api_tokens(token,auth_id,expires_on) values(?,?,?)",array($token,$auth['id'],$expires));
		return array("access-token"=>$token);
	}
	
	function getproducts()
	{
		$this->auth_token();
		$page=isset($this->uris[3])?$this->uris[3]:1;
		$l=200;
		return array("products"=>$this->db->query("select p.is_sourceable,p.pid,p.mrp,p.product_name as name,p.short_desc as description,b.id as brand_id,b.name as brand,concat('".IMAGES_URL."items/',i.pic,'.jpg') as image_url from m_product_info p join m_product_deal_link l on l.product_id=p.product_id join king_dealitems i on i.id=l.itemid join king_brands b on b.id=p.brand_id where p.is_active=1 group by p.product_id order by p.product_id asc limit ".(($page-1)*$l).", $l")->result_array());
	}
	
	function output_deals($ret)
	{
		foreach($ret as $i=>$r)
		{
			$itemid=$r['itemid'];
			$prods=$this->db->query("select p.product_name as name,l.qty from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where l.itemid=?",$r['itemid'])->result_array();
			$ret[$i]['products']=array("product"=>$prods);
			$ret[$i]['images']=$this->db->query("select CONCAT('".IMAGES_URL."items/',id,'.jpg') as url from king_resources where itemid=? and type=0",$r['itemid'])->result_array();
			$ret[$i]['attributes']=array();
			foreach($this->db->query("select group_concat(concat(a.attribute_name,':',v.attribute_value)) as a from m_product_group_deal_link l join products_group_pids p on p.group_id=l.group_id join products_group_attributes a on a.attribute_name_id=p.attribute_name_id join products_group_attribute_values v on v.attribute_value_id=p.attribute_value_id where l.itemid=? group by p.product_id",$itemid)->result_array() as $i2=>$p)
				$ret[$i]['attributes']['attr'.($i2+1)]=$p['a'];
		}
		return $ret;
	}
	
	
	function getdeals()
	{
		$this->auth_token();
		$page=isset($this->uris[3])?$this->uris[3]:1;
		$l=200;
		return $this->output_deals($this->db->query("select pnh_id as pid,i.id as itemid,i.name,m.name as menu,m.id as menu_id,i.gender_attr,c.name as category,d.catid as category_id,mc.name as main_category,c.type as main_category_id,b.name as brand,d.brandid as brand_id,i.orgprice as mrp,i.price as price,i.store_price,i.is_combo, concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,d.description,i.shipsin as ships_in,d.keywords from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join pnh_menu m on m.id=d.menuid left outer join king_categories mc on mc.id=c.type where d.publish=1 and is_pnh=1 order by d.sno asc limit ".(($page-1)*$l).", $l")->result_array());
	}
	
	function getdealsbybrand()
	{
		$this->auth_token();
		if(!isset($this->uris[3]))
			return array("error"=>"Required brandid is missing");
		$brandids=explode(",",$this->uris[3]);
		$ques=array();
		foreach($brandids as $b)
			$ques[]="?";
		return $this->output_deals($this->db->query("select pnh_id as pid,i.id as itemid,i.name,i.gender_attr,d.tagline,m.name as menu,m.id as menu_id,c.name as category,d.catid as category_id,mc.name as main_category,c.type as main_category_id,b.name as brand,d.brandid as brand_id,i.orgprice as mrp,i.price as price,i.store_price,i.is_combo, concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,d.description,i.shipsin as ships_in,d.keywords from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join pnh_menu m on m.id=d.menuid left outer join king_categories mc on mc.id=c.type where d.publish=1 and is_pnh=1 and d.brandid in (".implode(",",$ques).") order by d.sno asc",$brandids)->result_array());
	}
	
	function getdealsafter()
	{
		ini_set('memory_limit','1024M');
		ini_set('max_execution_time','6000');
		$this->auth_token();
		if(!isset($this->uris[3]))
			return array("error"=>"Required itemid is missing");
		$id=$this->uris[3];
		$after=$this->db->query("select sno from king_dealitems where id=? or pnh_id=?",array($id,$id))->row_array();
		if(empty($after))
			return array("error"=>"Invalid Itemid");
		$after=$after['sno'];
		
		$page=isset($this->uris[4])?$this->uris[4]:1;
		$l=500;
		
		return $this->output_deals($this->db->query("select pnh_id as pid,i.gender_attr,i.id as itemid,i.name,d.tagline,m.name as menu,m.id as menu_id,c.name as category,d.catid as category_id,mc.name as main_category,c.type as main_category_id,b.name as brand,d.brandid as brand_id,i.orgprice as mrp,i.price as price,i.store_price,i.is_combo, concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,d.description,i.shipsin as ships_in,d.keywords from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join pnh_menu m on m.id=d.menuid left outer join king_categories mc on mc.id=c.type where d.publish=1 and is_pnh=1 and i.sno>? order by d.sno asc limit ".(($page-1)*$l).", $l",$after)->result_array());
	}
	
	
	function getdealsbypnhrange()
	{
		$uris = $this->uris;
		
		$page = isset($uris[3])?$uris[3]:1;
		$id1 = isset($uris[4])?$uris[4]:0;
		$id2 = isset($uris[5])?$uris[5]:0;
		$menuid = isset($uris[6])?$uris[6]:0;
		$catid = isset($uris[7])?$uris[7]:0;
		$brandid = isset($uris[8])?$uris[8]:0;
		
		
		ini_set('memory_limit','512M');
		ini_set('max_execution_time','3600');
		//$this->auth_token();
		if(!isset($this->uris[3]))
			return array("error"=>"Required itemid is missing");
			
		$sno1=@$this->db->query("select sno from king_dealitems where (id=? or pnh_id=?)",array($id1,$id1))->row()->sno;
		$sno2=@$this->db->query("select sno from king_dealitems where (id=? or pnh_id=?)",array($id2,$id2))->row()->sno;
		
		//echo $sno1;
		
		if(!($sno1 && $sno2))
		{
			return array("error"=>"Invalid Ids entered");
		}
		
		
		$t = 0;
		if($sno1 > $sno2)
		{
			$t = $sno1;
			$sno1 = $sno2;
			$sno2 = $t;
		}
		
		if($page > 10000)
			exit;
		
		$l=200;
		
		$cond = '';
		$cond .= ($menuid?' and menuid = "'.$menuid.'" ':'');
		$cond .= ($catid?' and catid = "'.$catid.'" ':'');
		$cond .= ($brandid?' and brandid = "'.$brandid.'" ':'');
		
		return $this->output_deals($this->db->query("select pnh_id as pid,i.gender_attr,i.id as itemid,i.name,d.tagline,m.name as menu,m.id as menu_id,c.name as category,d.catid as category_id,mc.name as main_category,c.type as main_category_id,b.name as brand,d.brandid as brand_id,i.orgprice as mrp,i.price as price,i.store_price,i.is_combo, concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,d.description,i.shipsin as ships_in,d.keywords from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join pnh_menu m on m.id=d.menuid left outer join king_categories mc on mc.id=c.type where d.publish=1 and is_pnh=1 and (i.sno>=? and i.sno <=? ) $cond order by d.sno asc limit ".(($page-1)*$l).", $l",array($sno1,$sno2))->result_array());
		
	}
	
	function getdealsbycategory()
	{
		$this->auth_token();
		if(!isset($this->uris[3]))
			return array("error"=>"Required category_id is missing");
		$catids=explode(",",$this->uris[3]);
		$ques=array();
		foreach($catids as $b)
			$ques[]="?";
		return $this->output_deals($this->db->query("select pnh_id as pid,i.gender_attr,i.id as itemid,i.name,d.tagline,m.name as menu,m.id as menu_id,c.name as category,d.catid as category_id,mc.name as main_category,c.type as main_category_id,b.name as brand,d.brandid as brand_id,i.orgprice as mrp,i.price as price,i.store_price,i.is_combo,concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,d.description,i.shipsin as ships_in,d.keywords from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join pnh_menu m on m.id=d.menuid left outer join king_categories mc on mc.id=c.type where d.publish=1 and is_pnh=1 and d.catid in (".implode(",",$ques).") order by d.sno asc",$catids)->result_array());
	}
	
	function getdealsbymenu()
	{
		$this->auth_token();
		if(!isset($this->uris[3]))
			return array("error"=>"Required menu_id is missing");
		$catids=explode(",",$this->uris[3]);
		$ques=array();
		foreach($catids as $b)
			$ques[]="?";
		return $this->output_deals($this->db->query("select pnh_id as pid,i.gender_attr,i.id as itemid,i.name,d.tagline,c.name as category,m.name as menu,d.menuid as menu_id,d.catid as category_id,mc.name as main_category,c.type as main_category_id,b.name as brand,d.brandid as brand_id,i.orgprice as mrp,i.price as price,i.store_price,i.is_combo,concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,d.description,i.shipsin as ships_in,d.keywords from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join pnh_menu m on m.id=d.menuid left outer join king_categories mc on mc.id=c.type where d.publish=1 and is_pnh=1 and d.menuid in (".implode(",",$ques).") order by d.sno asc",$catids)->result_array());
	}
	
	function getdeal()
	{
		$this->auth_token();
		if(!isset($this->uris[3]))
			return array("error"=>"Required pnh_id is missing");
		$pids=explode(",",$this->uris[3]);
		return $this->output_deals($this->db->query("select pnh_id as pid,i.gender_attr,i.id as itemid,i.name,d.tagline,c.name as category,m.name as menu,d.menuid as menu_id,d.catid as category_id,mc.name as main_category,c.type as main_category_id,b.name as brand,d.brandid as brand_id,i.orgprice as mrp,i.price as price,i.store_price,i.is_combo,concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,d.description,i.shipsin as ships_in,d.keywords,i.live as is_stock,d.publish as is_enabled from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid left outer join pnh_menu m on m.id=d.menuid left outer join king_categories mc on mc.id=c.type where is_pnh=1 and i.pnh_id in (".implode(",",$pids).") and i.pnh_id!=0 order by d.sno asc")->result_array());
	}
	
	
	function getsnpdeals()
	{
		$this->auth_token();
		$page=isset($this->uris[3])?$this->uris[3]:1;
		$l=200;
		return $this->output_deals($this->db->query("select i.name,i.id as itemid,c.name as category,d.catid as category_id,b.name as brand,d.brandid as brand_id,i.orgprice as mrp,i.price as price,i.is_combo,concat('".base_url()."',i.url) as url, concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,i.shipsin as ships_in from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid where d.publish=1 and d.discontinued=0 and ".time()." between d.startdate and d.enddate order by d.sno asc limit ".(($page-1)*$l).", $l")->result_array());
	}
	
	function getsnpdealsbybrand()
	{
		$this->auth_token();
		if(!isset($this->uris[3]))
			return array("error"=>"Required brandid is missing");
		$brandids=explode(",",$this->uris[3]);
		$ques=array();
		foreach($brandids as $b)
			$ques[]="?";
		return $this->output_deals($this->db->query("select i.name,i.id as itemid,c.name as category,d.catid as category_id,b.name as brand,d.brandid as brand_id,i.orgprice as mrp,i.price as price,i.is_combo,concat('".base_url()."',i.url) as url, concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,i.shipsin as ships_in from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid where d.publish=1 and d.discontinued=0 and ".time()." between d.startdate and d.enddate and d.brandid in (".implode(",",$ques).") order by d.sno asc",$brandids)->result_array());
	}
	
	function getsnpdealsbycategory()
	{
		$this->auth_token();
		if(!isset($this->uris[3]))
			return array("error"=>"Required category_id is missing");
		$catids=explode(",",$this->uris[3]);
		$ques=array();
		foreach($catids as $b)
			$ques[]="?";
		return $this->output_deals($this->db->query("select i.name,i.id as itemid,c.name as category,d.catid as category_id,b.name as brand,d.brandid as brand_id,i.orgprice as mrp,i.price as price,i.is_combo,concat('".base_url()."',i.url) as url, concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,i.shipsin as ships_in from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid where d.publish=1 and d.discontinued=0 and ".time()." between d.startdate and d.enddate and d.catid in (".implode(",",$ques).") order by d.sno asc",$catids)->result_array());
	}
	
	
	function getsnpdealsafter()
	{
		$this->auth_token();
		if(!isset($this->uris[3]))
			return array("error"=>"Required itemid is missing");
		$after=$this->db->query("select sno from king_dealitems where id=?",$this->uris[3])->row_array();
		if(empty($after))
			return array("error"=>"Invalid Itemid");
		$after=$after['sno'];
		return $this->output_deals($this->db->query("select i.name,i.id as itemid,c.name as category,d.catid as category_id,b.name as brand,d.brandid as brand_id,i.orgprice as mrp,i.price as price,i.is_combo,concat('".base_url()."',i.url) as url, concat('".IMAGES_URL."items/',d.pic,'.jpg') as image_url,i.shipsin as ships_in from king_deals d join king_dealitems i on i.dealid=d.dealid join king_brands b on b.id=d.brandid join king_categories c on c.id=d.catid where d.publish=1 and d.discontinued=0 and ".time()." between d.startdate and d.enddate and i.sno>? order by d.sno asc",$after)->result_array());
	}
	
	function getcats()
	{
		$this->auth_token();
		//return $this->db->query("select b.id,b.name,b.type as parent_id from king_categories b join king_deals d on d.catid=b.id join king_dealitems i on i.is_pnh=1 and i.dealid=d.dealid group by b.id order by b.name asc")->result_array();

		return $this->db->query("select b.id,b.name,b.type as parent_id 
	from king_categories b 
	left join king_deals d on d.catid=b.id 
	left join king_dealitems i on i.is_pnh=1 and i.dealid=d.dealid 
	group by b.id 
	order by b.name asc")->result_array();
	}
	
	function getbrands()
	{
		$this->auth_token();
		return $this->db->query("select b.id,b.name from king_brands b join king_deals d on d.brandid=b.id join king_dealitems i on i.is_pnh=1 and i.dealid=d.dealid group by b.id order by b.name asc")->result_array();
	}
	
	function getproductsbybrand()
	{
		$this->auth_token();
		if(!isset($this->uris[3]))
			return array("error"=>"Required brandid is missing");
		$brandids=explode(",",$this->uris[3]);
		$ques=array();
		foreach($brandids as $b)
			$ques[]="?";
		return $this->db->query("select p.is_sourceable,p.pid,p.mrp,p.product_name as name,p.short_desc as description,b.id as brand_id,b.name as brand,concat('".IMAGES_URL."items/',i.pic,'.jpg') as image_url from m_product_info p join m_product_deal_link l on l.product_id=p.product_id join king_dealitems i on i.id=l.itemid join king_brands b on b.id=p.brand_id where p.is_active=1 and p.brand_id in (".implode(",",$ques).") group by p.product_id order by p.product_id asc",$brandids)->result_array();
	}
	
	function getmenu()
	{
		$this->auth_token();
		return $this->db->query("select id,name from pnh_menu where status=1 order by name asc")->result_array();
	}
	
	function check_member()
	{
		$this->auth_token();
		$q=$this->input->post("q");
		if(empty($q))
			return array("error"=>"Input kissing!");
		$m=$this->db->query("select pnh_member_id as mid from pnh_member_info where (mobile=? and mobile!='') or (email=? and email!='')",array($q,$q))->row_array();
		if(empty($m))
			return array("success_code"=>0);
		else
			return array("success_code"=>1,"mid"=>$m['mid']);
	}
	
	function getmember()
	{
		$this->auth_token();
		$q=$this->input->post("q");
		if(empty($q))
			return array("error"=>"Input kissing!");
		if(strlen($q)!=8 || $q{0}!='2')
			return array("error"=>"Invalid MID");
		$data=$this->db->query("select m.franchise_id as fid,m.user_id,m.pnh_member_id as mid,m.first_name,m.last_name,m.address,m.city,m.pincode,m.mobile,m.email from pnh_member_info m where m.pnh_member_id=?",$q)->row_array();
		if(empty($data))
			return array("error"=>"MID:$q not available");
		$data['franchise']=$this->db->query("select pnh_franchise_id as fid,franchise_name as name,address,locality,city,postcode as pincode,state from pnh_m_franchise_info where franchise_id=?",$data['fid'])->row_array();
		$data['orders']=$this->db->query("select count(1) as total_orders,sum(amount) as total_order_amount from king_transactions where transid in (select transid from king_orders where userid=?)",$data['user_id'])->row_array();
		unset($data['user_id']);
		unset($data['fid']);
		return array("data"=>$data);
	}
	
	function getpricechangelog()
	{
		$this->auth_token();
		$q=!$this->input->post("q")?0:$this->input->post("q");
		$ver=$this->db->query("select * from pnh_app_versions where version_no=?",$q)->row_array();
		if(empty($ver))
			return array("error"=>"Invalid version no");
		$ci=&get_instance();
		$ci->load->model("erpmodel","erpm");
		$ver['version_date_timestamp']=$ver['version_date'];
		$ver['version_date']=date("d/m/y",$ver['version_date']);
		$data['version']=$ver;
		$data['deals']=array("deal"=>$ci->erpm->pnh_getdealpricechange($ver['id']));
		return array("data"=>$data);
	}
	
	function do_bulk_order()
	{
		$this->auth_token();
		$r_payload=json_decode($this->input->post("q"),true);
		if(empty($r_payload))
			return array("error"=>"Invalid order syntax");
		$payload=array();
		foreach($r_payload as $pl)
		{
			$r=array();
			foreach(array("pnh_franchise_id","member_id","pnh_product_id","qtys","user_notes","member_name","member_address","member_city","member_pincode","member_email","member_mobile","attr_data") as $i=>$a)
			{
				if(!isset($pl[$a]))
					return array("error"=>"Invalid order syntax");
				$r[]=$pl[$a];
			}
			$payload[]=$r;
		}
		$ci=&get_instance();
		$ci->load->model("erpmodel","erpm");
		$resp=$ci->erpm->do_pnh_order_import($payload,false);
		$out=array();
		foreach($resp as $r)
			$out[]['order']=$r;
		$data['response']=$out;
		return $data;
	}
	
	function getfranterritories()
	{
		$this->auth_token();
		return $this->db->query("select id as territory_id,territory_name from pnh_m_territory_info order by territory_name asc")->result_array();
	}
	
	function getfranchises()
	{
		$this->auth_token();
		$img=IMAGES_URL."erp_images/franchises/";
		$frans=$this->db->query("select f.franchise_id,f.pnh_franchise_id as fid,f.franchise_name,f.address,f.locality,f.city,f.postcode as pincode,f.state,t.territory_name,tw.town_name,if(f.is_lc_store=1,'Store','Franchise') as type,login_mobile1,login_mobile2,f.email_id,f.no_of_employees,f.store_name,f.store_area,if(f.is_suspended=1,'Suspended','Active') as status from pnh_m_franchise_info f join pnh_m_territory_info t on t.id=f.territory_id join pnh_towns tw on tw.id=f.town_id order by f.franchise_name")->result_array();
		$ret=array();
		foreach($frans as $i=>$f)
		{
			$f['contacts']=$this->db->query("select contact_name as name,contact_designation as designation,contact_mobile1,contact_mobile2,contact_telephone,contact_fax,contact_email1,contact_email2 from pnh_m_franchise_contacts_info where franchise_id=?",$f['franchise_id'])->result_array();
			$f['photos']=$this->db->query("select concat('$img',pic) as url,caption from pnh_franchise_photos where franchise_id=?",$f['franchise_id'])->result_array();
			unset($f['franchise_id']);
			$ret[]=$f;
		}
		return $ret;
	}
	
	function getfranchisesbyterritory()
	{
		$this->auth_token();
		$img=IMAGES_URL."erp_images/franchises/";
		if(!isset($this->uris[3]))
			return array("error"=>"Required territory_id is missing");
		$tids=explode(",",$this->uris[3]);
		$frans=$this->db->query("select f.franchise_id,f.pnh_franchise_id as fid,f.franchise_name,f.address,f.locality,f.city,f.postcode as pincode,f.state,t.territory_name,tw.town_name,if(f.is_lc_store=1,'Store','Franchise') as type,login_mobile1,login_mobile2,f.email_id,f.no_of_employees,f.store_name,f.store_area,if(f.is_suspended=1,'Suspended','Active') as status from pnh_m_franchise_info f join pnh_m_territory_info t on t.id=f.territory_id join pnh_towns tw on tw.id=f.town_id where f.territory_id in ('".implode("','",$tids)."') order by f.franchise_name")->result_array();
		$ret=array();
		foreach($frans as $i=>$f)
		{
			$f['contacts']=$this->db->query("select contact_name as name,contact_designation as designation,contact_mobile1,contact_mobile2,contact_telephone,contact_fax,contact_email1,contact_email2 from pnh_m_franchise_contacts_info where franchise_id=?",$f['franchise_id'])->result_array();
			$f['photos']=$this->db->query("select concat('$img',pic) as url,caption from pnh_franchise_photos where franchise_id=?",$f['franchise_id'])->result_array();
			unset($f['franchise_id']);
			$ret[]=$f;
		}
		return $ret;
	}
	
	function getfranchisesbyfid()
	{
		$this->auth_token();
		$img=IMAGES_URL."erp_images/franchises/";
		if(!isset($this->uris[3]))
			return array("error"=>"Required FID is missing");
		$fids=explode(",",$this->uris[3]);
		$frans=$this->db->query("select f.franchise_id,f.pnh_franchise_id as fid,f.franchise_name,f.address,f.locality,f.city,f.postcode as pincode,f.state,t.territory_name,tw.town_name,if(f.is_lc_store=1,'Store','Franchise') as type,login_mobile1,login_mobile2,f.email_id,f.no_of_employees,f.store_name,f.store_area,if(f.is_suspended=1,'Suspended','Active') as status from pnh_m_franchise_info f join pnh_m_territory_info t on t.id=f.territory_id join pnh_towns tw on tw.id=f.town_id where f.pnh_franchise_id in ('".implode("','",$fids)."') order by f.franchise_name")->result_array();
		$ret=array();
		foreach($frans as $i=>$f)
		{
			$f['contacts']=$this->db->query("select contact_name as name,contact_designation as designation,contact_mobile1,contact_mobile2,contact_telephone,contact_fax,contact_email1,contact_email2 from pnh_m_franchise_contacts_info where franchise_id=?",$f['franchise_id'])->result_array();
			$f['photos']=$this->db->query("select concat('$img',pic) as url,caption from pnh_franchise_photos where franchise_id=?",$f['franchise_id'])->result_array();
			unset($f['franchise_id']);
			$ret[]=$f;
		}
		return $ret;
	}
	
	function get_pnh_deals_mrp_change_log()
	{
		//$this->auth_token();
		$s=isset($this->uris[3])?$this->uris[3]:'';
		$e=isset($this->uris[4])?$this->uris[4]:'';
		$page=isset($this->uris[5])?$this->uris[5]:1;
		$l=200;
		
		if(empty($e))
		{
			$s=time()-(30*24*60*60);
			$e=time();
		}else{
			$s=strtotime($s);
			$e=strtotime($e)+(24*60*60);
		}
		
		return $this->output_deals($this->db->query("select d.menuid as menu_id,m.name as menu_name,p.name as name,pc.*,a.name as created_by from deal_price_changelog pc join king_dealitems p on p.id=pc.itemid join king_deals d on d.dealid = p.dealid join pnh_menu m on m.id = d.menuid left outer join king_admin a on a.id=pc.created_by where is_pnh = 1 and pc.created_on between $s and $e order by pc.id desc limit ".(($page-1)*$l).", $l")->result_array());
		
	}
	
	function process()
	{
//		foreach($this->db->query("select d.dealid,d.description from king_dealitems i join king_deals d on d.dealid=i.dealid where is_pnh=1")->result_array() as $p)
//			$this->db->query("update king_deals set description=? where dealid=? limit 1",array(strip_tags($p['description']),$p['dealid']));
//		die;
		
		$this->uris=$uris=$this->uri->segment_array();
		$call=isset($uris[2])?$uris[2]:"unknown";
		switch($call)
		{
			case 'gettoken':
				$data=$this->dpm->gen_token();
				break;
			case 'menu':
				$data=$this->dpm->getmenu();
				break;
			case 'deals':
				$data=$this->dpm->getdeals();
				break;
			case 'dealsbycategory':
				$data=$this->dpm->getdealsbycategory();
				break;
			case 'dealsbybrand':
				$data=$this->dpm->getdealsbybrand();
				break;
			case 'dealsafter':
				$data=$this->dpm->getdealsafter();
				break;
			case 'deal':
				$data=$this->dpm->getdeal();
				break;
			case 'snp_deals':
				$data=$this->dpm->getsnpdeals();
				break;
			case 'snp_dealsbycategory':
				$data=$this->dpm->getsnpdealsbycategory();
				break;
			case 'snp_dealsbybrand':
				$data=$this->dpm->getsnpdealsbybrand();
				break;
			case 'snp_dealsafter':
				$data=$this->dpm->getsnpdealsafter();
				break;
			case 'products':
				$data=$this->dpm->getproducts();
				break;
			case 'productsbybrand':
				$data=$this->dpm->getproductsbybrand();
				break;
			case 'brands':
				$data=$this->dpm->getbrands();
				break;
			case 'categories':
				$data=$this->dpm->getcats();
				break;
			case 'check_member':
				$data=$this->dpm->check_member();
				break;
			case 'member':
				$data=$this->dpm->getmember();
				break;
			case 'price_changelog':
				$data=$this->dpm->getpricechangelog();
				break;
			case 'bulk_order':
			case 'bulk_orders':
				$data=$this->dpm->do_bulk_order();
				break;
			case 'fran_territories':
				$data=$this->dpm->getfranterritories();
				break;
			case 'franchises':
				$data=$this->dpm->getfranchises();
				break;
			case 'franchises_by_territory':
				$data=$this->dpm->getfranchisesbyterritory();
				break;
			case 'franchises_by_fid':
				$data=$this->dpm->getfranchisesbyfid();
				break;
			case 'getdealsbypnhrange' :
				$data=$this->dpm->getdealsbypnhrange();
				break;
			case 'get_pnh_deals_mrp_change_log' :
				$data=$this->dpm->get_pnh_deals_mrp_change_log();
				break;
			default:
				$data=array("error"=>"Unknown system call");
		}
		$this->dpm->output($data);
	}
	
}