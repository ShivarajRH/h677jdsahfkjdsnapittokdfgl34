<?php
	include APPPATH.'/controllers/voucher.php';
	class Analytics extends Voucher
	{
		
		function pnh_state_analytics($sid=false)
		{
			$data['territory']=$this->db->query("select * from pnh_m_territory_info where state_id = '".$sid."' order by territory_name asc")->result_array();
			$data['page']="pnh_state_analytics";
			$this->load->view("admin",$data);
		}
	
		/*
		 * Ajax function to load state sales 
		 * 
		 */
		function jx_state_sales($s="",$e="",$sid="")
		{
			$st = date('Y-m-d',strtotime($s));
			$en = date('Y-m-d',strtotime($e));
			$date_diff = date_diff_days($en,$st);
			
			if($date_diff <= 61)
			{
				$sql ="select date_format(from_unixtime(ki.init),'%d-%b') as mn,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
					from king_orders a
					join king_transactions ki on ki.transid=a.transid
					join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
					join pnh_m_territory_info tr on tr.id=f.territory_id 
					join pnh_m_states st on st.state_id = tr.state_id
					where st.state_id='".$sid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."' and a.status !=3
					group by date(from_unixtime(ki.init))";
			}
			else
			{
				$sql ="select date_format(from_unixtime(ki.init),'%b-%Y') as mn,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
					from king_orders a
					join king_transactions ki on ki.transid=a.transid
					join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
					join pnh_m_territory_info tr on tr.id=f.territory_id 
					join pnh_m_states st on st.state_id = tr.state_id
					where st.state_id='".$sid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."' and a.status !=3
					group by date_format(from_unixtime(ki.init),'%m-%Y')
					order by date(from_unixtime(ki.init))";
			}
			 $res = $this->db->query($sql);
			 $sales_on = array();
			 $sales_summary = array();
			 
			if($res->num_rows())
			{
				foreach($res->result_array() as $row)
				{
					array_push($sales_summary,array($row['mn'],$row['ttl']*1)); 		
				}
			}
			$output = array();
			$output['date_diff'] = $date_diff;
			$output['summary'] = $sales_summary;
			echo json_encode($output);
		}
	
		/*
		 * 
		 * Ajax Function to get territories by state 
		 */
		 function jx_getterritorybystateid($sid="",$s="",$e="")
		 {
			$st = date('Y-m-d',strtotime($s));
			$en = date('Y-m-d',strtotime($e));
			
			$output=array();
			$terr_list = "select tr.territory_name,tr.id,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
					from king_orders a
					join king_transactions ki on ki.transid=a.transid
					join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
					join pnh_m_territory_info tr on tr.id=f.territory_id 
					join pnh_m_states st on st.state_id = tr.state_id
					join pnh_towns tw on tw.id=f.town_id
					where is_pnh = 1 and st.state_id='".$sid."' and a.status !=3 and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
					group by tr.territory_name
					order by ttl desc";
			 $res = $this->db->query($terr_list);
			 $sales_summary = array();
			 
			if($res->num_rows())
			{
				foreach($res->result_array() as $row)
				{
					array_push($sales_summary,array($row['territory_name'],$row['ttl']*1,$row['id'],$row['ttl']*1)); 		
				}
			}
			$output = array();
			$output['summary'] = $sales_summary;
			echo json_encode($output);	
		 }
	
		 /*
		 * 
		 * Ajax Function to get brands by territory_id 
		 */
		 function jx_getallbrandsbyterrid()
		 {
			$tid= $this->input->post('terr_id');
			$start= $this->input->post('start_date');
			$end= $this->input->post('end_date');
			$st = date('Y-m-d',strtotime($start));
			$en = date('Y-m-d',strtotime($end));
			 
			$output=array();
			$brand_list = $this->db->query("select b.id,b.name,sum(a.quantity) as qty_sold, ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
							from king_orders a
							join king_transactions ki on ki.transid=a.transid
							join king_dealitems di on di.id=a.itemid
							join king_deals d on d.dealid=di.dealid
							join king_brands b on b.id=d.brandid
							join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
							join pnh_m_territory_info tr on tr.id=f.territory_id 
							where a.status!=3 and ki.is_pnh=1 and tr.id='".$tid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
							group by b.id
							order by ttl desc");
			if($brand_list->num_rows())
			{
				$output['brand_list']=$brand_list->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']="error";
				$output['message']="No Brands Found";
			}
			echo json_encode($output);	
		 }
	
		 /*
		 * 
		 * Ajax Function to get franchises by territory_id 
		 */
		 function jx_getallfranchisesbyterrid()
		 {
			$tid= $this->input->post('terr_id');
			$start= $this->input->post('start_date');
			$end= $this->input->post('end_date');
			$st = date('Y-m-d',strtotime($start));
			$en = date('Y-m-d',strtotime($end));
			 
			$output=array();
			$fran_list = $this->db->query("select f.franchise_id,f.franchise_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
					from king_orders a
					join king_transactions ki on ki.transid=a.transid
					join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
					join pnh_m_territory_info tr on tr.id=f.territory_id 
					where a.status!=3 and ki.is_pnh=1 and tr.id='".$tid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
					group by f.franchise_id
					order by ttl desc");
			if($fran_list->num_rows())
			{
				$output['fran_list']=$fran_list->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']="error";
				$output['message']="No Brands Found";
			}
			echo json_encode($output);	
		 }
	
		  /*
		 * 
		 * Ajax Function to get towns by state 
		 */
		 function jx_gettownsbystateid($sid="",$tid="",$s="",$e="")
		 {
			$st = date('Y-m-d',strtotime($s));
			$en = date('Y-m-d',strtotime($e));
			
			$output=array();
			$town_list = "select tw.id,tw.town_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
					from king_orders a
					join king_transactions ki on ki.transid=a.transid
					join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
					join pnh_m_territory_info tr on tr.id=f.territory_id 
					join pnh_m_states st on st.state_id = tr.state_id
					join pnh_towns tw on tw.id=f.town_id
					where is_pnh = 1 and st.state_id='".$sid."' and a.status !=3 and tr.id='".$tid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
					group by tw.town_name
					order by ttl desc limit 25";
			 $res = $this->db->query($town_list);
			 $sales_summary = array();
			 
			if($res->num_rows())
			{
				foreach($res->result_array() as $row)
				{
					array_push($sales_summary,array($row['town_name'],$row['ttl']*1,$row['id'],$row['ttl']*1)); 		
				}
			}
			$output = array();
			$output['summary'] = $sales_summary;
			echo json_encode($output);	
		 }
	
		 /*
		 * 
		 * Ajax Function to get franchises by town_id 
		 */
		 function jx_getallfranchisesbytownid()
		 {
			$tid= $this->input->post('town_id');
			$start= $this->input->post('start_date');
			$end= $this->input->post('end_date');
			$st = date('Y-m-d',strtotime($start));
			$en = date('Y-m-d',strtotime($end));
			 
			$output=array();
			$fran_list = $this->db->query("select f.franchise_id,f.franchise_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
							from king_orders a
							join king_transactions ki on ki.transid=a.transid
							join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
							join pnh_towns tw on tw.id=f.town_id 
							where a.status!=3 and ki.is_pnh=1 and tw.id='".$tid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
							group by f.franchise_id
							order by ttl desc");
			if($fran_list->num_rows())
			{
				$output['fran_list']=$fran_list->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']="error";
				$output['message']="No Brands Found";
			}
			echo json_encode($output);	
		 }
	
		 /*
		 * 
		 * Ajax Function to get brands by town_id 
		 */
		 function jx_getallbrandsbytownid()
		 {
			$tid= $this->input->post('town_id');
			$start= $this->input->post('start_date');
			$end= $this->input->post('end_date');
			$st = date('Y-m-d',strtotime($start));
			$en = date('Y-m-d',strtotime($end));
			 
			$output=array();
			$brand_list = $this->db->query("select b.id,b.name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
					from king_orders a
					join king_transactions ki on ki.transid=a.transid
					join king_dealitems di on di.id=a.itemid
					join king_deals d on d.dealid=di.dealid
					join king_brands b on b.id=d.brandid
					join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
					join pnh_towns tw on tw.id=f.town_id 
					where a.status!=3 and ki.is_pnh=1 and tw.id='".$tid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
					group by b.id
					order by ttl desc");
			if($brand_list->num_rows())
			{
				$output['brand_list']=$brand_list->result_array();
				$output['status']='success';
			}
			else
			{
				$output['status']="error";
				$output['message']="No Brands Found";
			}
			echo json_encode($output);	
		 }
	
		  /*
		 * Ajax function to load top brands by territory
		 * 
		 */
		function jx_get_territory_brand_sales_by_stateid($sid="",$tid="",$s="",$e="")
		{
			 $st = date('Y-m-d',strtotime($s));
			 $en = date('Y-m-d',strtotime($e));
			 $sql = "select br.name as name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
					from king_orders a
					join king_transactions ki on ki.transid=a.transid
					join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
					join king_dealitems di on di.id=a.itemid
					join king_deals d on d.dealid=di.dealid
					join king_brands br on br.id=d.brandid
					join pnh_m_territory_info tr on tr.id=f.territory_id
					join pnh_m_states st on st.state_id=tr.state_id
					where a.status!=3 and ki.is_pnh=1 and st.state_id='".$sid."' and tr.id='".$tid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
					group by br.id
					order by ttl desc limit 10";
			 $res = $this->db->query($sql);
			 $sales_summary = array();
			 
			if($res->num_rows())
			{
				foreach($res->result_array() as $row)
				{
					array_push($sales_summary,array($row['name'],$row['ttl']*1)); 		
				}
			}
			$output = array();
			$output['summary'] = $sales_summary;
			echo json_encode($output);
		}
	
		/*
		 * Ajax function to load top brands by town
		 * 
		 */
		function jx_get_town_brand_sales_by_stateid($sid="",$twid="",$s="",$e="")
		{
			 $st = date('Y-m-d',strtotime($s));
			 $en = date('Y-m-d',strtotime($e));
			 
			 $sql = "select br.name as name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
					from king_orders a
					join king_transactions ki on ki.transid=a.transid
					join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
					join king_dealitems di on di.id=a.itemid
					join king_deals d on d.dealid=di.dealid
					join king_brands br on br.id=d.brandid
					join pnh_m_territory_info tr on tr.id=f.territory_id
					join pnh_towns tw on tw.id=f.town_id
					join pnh_m_states st on st.state_id=tr.state_id
					where a.status!=3 and ki.is_pnh=1 and st.state_id='".$sid."' and tw.id='".$twid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
					group by br.id
					order by ttl desc limit 10";
			 $res = $this->db->query($sql);
			 $sales_summary = array();
			 
			if($res->num_rows())
			{
				foreach($res->result_array() as $row)
				{
					array_push($sales_summary,array($row['name'],$row['ttl']*1)); 		
				}
			}
			$output = array();
			$output['summary'] = $sales_summary;
			echo json_encode($output);
		}
	
		/*
		 * 
		 * Ajax Function to get Franchises by state 
		 */
		 function jx_getfranchisebyterritoryid($sid="",$tid="",$s="",$e="")
		 {
			$st = date('Y-m-d',strtotime($s));
			$en = date('Y-m-d',strtotime($e));
			 
			$output=array();
			$fran_list = "select f.franchise_id,f.franchise_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
												from king_orders a
												join king_transactions ki on ki.transid=a.transid
												join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
												join pnh_m_territory_info tr on tr.id=f.territory_id 
												join pnh_m_states st on st.state_id = tr.state_id
												where a.status!=3 and ki.is_pnh=1 and st.state_id='".$sid."' and tr.id='".$tid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
												group by f.franchise_id
												order by ttl desc limit 10";
			$res = $this->db->query($fran_list);
			 $sales_summary = array();
			 
			if($res->num_rows())
			{
				foreach($res->result_array() as $row)
				{
					array_push($sales_summary,array($row['franchise_name'],$row['ttl']*1)); 		
				}
			}
			$output = array();
			$output['summary'] = $sales_summary;
			echo json_encode($output);	
		 }
	
		 /*
		 * 
		 * Ajax Function to get Franchises by state 
		 */
		 function jx_getfranchisebytown($sid="",$twid="",$s="",$e="")
		 {
			$st = date('Y-m-d',strtotime($s));
			$en = date('Y-m-d',strtotime($e));
			 
			$output=array();
			$fran_list = "select f.franchise_id,f.franchise_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
												from king_orders a
												join king_transactions ki on ki.transid=a.transid
												join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
												join pnh_m_territory_info tr on tr.id=f.territory_id 
												join pnh_towns tw on tw.id=f.town_id
												join pnh_m_states st on st.state_id = tr.state_id
												where a.status!=3 and ki.is_pnh=1 and st.state_id='".$sid."' and tw.id='".$twid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
												group by f.franchise_id
												order by ttl desc limit 10";
			$res = $this->db->query($fran_list);
			 $sales_summary = array();
			 
			if($res->num_rows())
			{
				foreach($res->result_array() as $row)
				{
					array_push($sales_summary,array($row['franchise_name'],$row['ttl']*1)); 		
				}
			}
			$output = array();
			$output['summary'] = $sales_summary;
			echo json_encode($output);	
		 }
		  /*
		 * 
		 *  Category sales statistics by territory id
		 */ 
		function jx_catsalesbyterritoryid($sid="",$tid="",$s="",$e="")
		{
			$st = date('Y-m-d',strtotime($s));
			$en = date('Y-m-d',strtotime($e));
			
			$output=array();
			$ct_lst ="select m.id,m.name as name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
									from king_orders a
									join king_transactions ki on ki.transid=a.transid
									join king_dealitems di on di.id=a.itemid
									join king_deals d on d.dealid=di.dealid
									join pnh_menu m on m.id=d.menuid
									join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
									join pnh_m_territory_info tr on tr.id=f.territory_id 
									join pnh_m_states st on st.state_id = tr.state_id
									where a.status!=3 and ki.is_pnh=1 and st.state_id='".$sid."' and tr.id='".$tid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
									group by m.id";
			
			$res = $this->db->query($ct_lst);
			$sales_summary = array();
			 
			if($res->num_rows())
			{
				foreach($res->result_array() as $row)
				{
					array_push($sales_summary,array($row['name'],$row['ttl']*1)); 		
				}
			}
			$output = array();
			$output['summary'] = $sales_summary;
			echo json_encode($output);			
		}
	
		 /*
		 * 
		 *  Category sales statistics by town id
		 */ 
		function jx_catsalesbytown($sid="",$twid="",$s="",$e="")
		{
			$st = date('Y-m-d',strtotime($s));
			$en = date('Y-m-d',strtotime($e));
			
			$output=array();
			$ct_lst ="select m.id,m.name as name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
									from king_orders a
									join king_transactions ki on ki.transid=a.transid
									join king_dealitems di on di.id=a.itemid
									join king_deals d on d.dealid=di.dealid
									join pnh_menu m on m.id=d.menuid
									join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
									join pnh_m_territory_info tr on tr.id=f.territory_id
									join pnh_towns tw on tw.id=f.town_id 
									join pnh_m_states st on st.state_id = tr.state_id
									where a.status!=3 and ki.is_pnh=1 and st.state_id='".$sid."' and tw.id='".$twid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
									group by m.id";
			
			$res = $this->db->query($ct_lst);
			$sales_summary = array();
			 
			if($res->num_rows())
			{
				foreach($res->result_array() as $row)
				{
					array_push($sales_summary,array($row['name'],$row['ttl']*1)); 		
				}
			}
			$output = array();
			$output['summary'] = $sales_summary;
			echo json_encode($output);			
		}
		
		
		
		
			/*
	 * Ajax function to load brand sales 
	 * 
	 */
	function jx_brand_sales($brandid="",$s="",$e="")
	{
		$st = date('Y-m-d',strtotime($s));
		$en = date('Y-m-d',strtotime($e));
		$date_diff = date_diff_days($en,$st);
		if($date_diff <= 31)
		{
			$sql ="select date_format(from_unixtime(ki.init),'%d-%b') as mn,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
				from king_orders a
				join king_transactions ki on ki.transid=a.transid
				join king_dealitems di on di.id=a.itemid
				join king_deals d on d.dealid=di.dealid
				join king_brands br on br.id=d.brandid
				where br.id='".$brandid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."' and a.status !=3 and ki.is_pnh=1
				group by date(from_unixtime(ki.init))";
		}
		
		else
		{
			$sql ="select date_format(from_unixtime(ki.init),'%b-%Y') as mn,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
				from king_orders a
				join king_transactions ki on ki.transid=a.transid
				join king_dealitems di on di.id=a.itemid
				join king_deals d on d.dealid=di.dealid
				join king_brands br on br.id=d.brandid
				where br.id='".$brandid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."' and a.status !=3 and ki.is_pnh=1
				group by date_format(from_unixtime(ki.init),'%m-%Y')
				order by date(from_unixtime(ki.init))";
		}
		 $res = $this->db->query($sql);
		 $sales_on = array();
		 $sales_summary = array();
		 
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				array_push($sales_summary,array($row['mn'],$row['ttl']*1)); 		
			}
		}
		$output = array();
		$output['date_diff'] = $date_diff;
		$output['summary'] = $sales_summary;
		echo json_encode($output);
	}

	/*
	 * 
	 *  Category sales statistics by brandid
	 */ 
	function jx_catsales_bybrand($brandid="",$s="",$e="")
	{
		$st = date('Y-m-d',strtotime($s));
		$en = date('Y-m-d',strtotime($e));
		$ct_lst =$this->db->query("select c.id,c.name as name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
									from king_orders a
									join king_transactions ki on ki.transid=a.transid
									join king_dealitems di on di.id=a.itemid
									join king_deals d on d.dealid=di.dealid
									join king_brands br on br.id=d.brandid
									join king_categories c on c.id=d.catid
									where br.id='".$brandid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."' and a.status !=3 and ki.is_pnh=1
									group by c.id");
		$c=array();
		
		if($ct_lst->num_rows())
		{
			foreach($ct_lst->result_array() as $row)
			{
				array_push($c,array($row['name'],$row['ttl']*1)); 
			}
		}
		$output = array();
		$output['result'] = $c;
		echo json_encode($output);		
	}

	/*
	 * 
	 *  Category sales statistics by brandid
	 */ 
	function jx_vendors_bybrand($brandid="",$s="",$e="")
	{
		$st = date('Y-m-d',strtotime($s));
		$en = date('Y-m-d',strtotime($e));
		$ct_lst =$this->db->query("select v.vendor_name,v.vendor_id,sum(p.total_value) as total_value,count(p.total_value) as pos 
								from m_vendor_info v 
								left outer join t_po_info p on p.vendor_id=v.vendor_id 
								join m_vendor_brand_link vb on vb.vendor_id=v.vendor_id 
								join m_vendor_contacts_info c on c.vendor_id=v.vendor_id 
								where vb.brand_id='".$brandid."' and date(p.created_on) between '".$st."' and '".$en."' and vb.is_active=1
								group by v.vendor_id 
								order by total_value desc");
		$ven=array();
		
		if($ct_lst->num_rows())
		{
			foreach($ct_lst->result_array() as $row)
			{
				array_push($ven,array($row['vendor_name'],$row['total_value']*1)); 
			}
		}
		$output = array();
		$output['result'] = $ven;
		echo json_encode($output);		
	}

	 /*
	 * 
	 * Ajax Function to get Franchises by brandid and townid 
	 */
	 function jx_getfranchisebybrandid_townid($bid="",$twid="",$s="",$e="")
	 {
		$st = date('Y-m-d',strtotime($s));
		$en = date('Y-m-d',strtotime($e));
		 
		$output=array();
		$fran_list = "select f.franchise_id,f.franchise_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
											from king_orders a
											join king_transactions ki on ki.transid=a.transid
											join king_dealitems di on di.id=a.itemid
											join king_deals d on d.dealid=di.dealid
											join king_brands b on b.id=d.brandid
											join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
											join pnh_m_territory_info tr on tr.id=f.territory_id 
											join pnh_towns tw on tw.id=f.town_id
											join pnh_m_states st on st.state_id = tr.state_id
											where a.status!=3 and ki.is_pnh=1 and b.id='".$bid."' and tw.id='".$twid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
											group by f.franchise_id
											order by ttl desc limit 10";
		$res = $this->db->query($fran_list);
		 $sales_summary = array();
		 
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				array_push($sales_summary,array($row['franchise_name'],$row['ttl']*1)); 		
			}
		}
		$output = array();
		$output['summary'] = $sales_summary;
		echo json_encode($output);	
	 }
	 
	 /*
	 * 
	 * Ajax Function to get franchises by territory_id and brandid
	 */
	 function jx_getallfranchisesbybrandid_terrid()
	 {
		$bid=$this->input->post('brandid');
		$tid= $this->input->post('terr_id');
		$start= $this->input->post('start_date');
		$end= $this->input->post('end_date');
		$st = date('Y-m-d',strtotime($start));
		$en = date('Y-m-d',strtotime($end));
		 
		$output=array();
		$fran_list = $this->db->query("select f.franchise_id,f.franchise_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
											from king_orders a
											join king_transactions ki on ki.transid=a.transid
											join king_dealitems di on di.id=a.itemid
											join king_deals d on d.dealid=di.dealid
											join king_brands b on b.id=d.brandid
											join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
											join pnh_m_territory_info tr on tr.id=f.territory_id 
											where a.status!=3 and ki.is_pnh=1 and b.id='".$bid."' and tr.id='".$tid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
											group by f.franchise_id
											order by ttl desc");
		if($fran_list->num_rows())
		{
			$output['fran_list']=$fran_list->result_array();
			$output['status']='success';
		}
		else
		{
			$output['status']="error";
			$output['message']="No Brands Found";
		}
		echo json_encode($output);	
	 }

	/*
	 * 
	 * Ajax Function to get franchises by town_id and brandid
	 */
	 function jx_getallfranchisesbybrandid_townid()
	 {
		$bid=$this->input->post('brandid');
		$tid= $this->input->post('town_id');
		$start= $this->input->post('start_date');
		$end= $this->input->post('end_date');
		$st = date('Y-m-d',strtotime($start));
		$en = date('Y-m-d',strtotime($end));
		 
		$output=array();
		$fran_list = $this->db->query("select f.franchise_id,f.franchise_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
											from king_orders a
											join king_transactions ki on ki.transid=a.transid
											join king_dealitems di on di.id=a.itemid
											join king_deals d on d.dealid=di.dealid
											join king_brands b on b.id=d.brandid
											join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
											join pnh_towns tw on tw.id=f.town_id 
											where a.status!=3 and ki.is_pnh=1 and b.id='".$bid."' and tw.id='".$tid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
											group by f.franchise_id
											order by ttl desc");
		if($fran_list->num_rows())
		{
			$output['fran_list']=$fran_list->result_array();
			$output['status']='success';
		}
		else
		{
			$output['status']="error";
			$output['message']="No Brands Found";
		}
		echo json_encode($output);	
	 }

	 /*
	 * 
	 * Ajax Function to get Categories by brand 
	 */
	 function jx_categoriesbybrandid()
	 {
		$output=array();
		$brandid= $this->input->post('brandid');
		$start= $this->input->post('start_date');
		$end= $this->input->post('end_date');
		
		$s = date('Y-m-d',strtotime($start));
		$e = date('Y-m-d',strtotime($end));
		$cat_list = $this->db->query("select c.name as cat_name,br.name,sum(a.quantity) as qty_sold,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
										from king_orders a
										join king_transactions ki on ki.transid=a.transid
										join king_dealitems di on di.id=a.itemid
										join king_deals d on d.dealid=di.dealid
										join king_brands br on br.id=d.brandid
										join king_categories c on c.id=d.catid
										where br.id='".$brandid."' and a.status !=3 and ki.is_pnh=1 and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$s."' and '".$e."' 
										group by c.id
										order by ttl desc");
		
		if($cat_list->num_rows())
		{
			$output['cat_list']=$cat_list->result_array();
			$output['status']='success';
		}
		else
		{
			$output['status']="error";
			$output['message']="No Categories Found";
		}
		echo json_encode($output);	
	 }
	 
	  /*
	 * 
	 * Ajax Function to get top Products by brand 
	 */
	 function jx_topproductbybrandid()
	 {
		$output=array();
		$brandid= $this->input->post('brandid');
		$start= $this->input->post('start_date');
		$end= $this->input->post('end_date');
		
		$s = date('Y-m-d',strtotime($start));
		$e = date('Y-m-d',strtotime($end));
		$top_prd_list = $this->db->query("select p.product_id,p.product_name,br.name,sum(a.quantity) as qty_sold,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
										from king_orders a
										join king_transactions ki on ki.transid=a.transid
										left join m_product_deal_link c on c.itemid=a.itemid
										left join m_product_info p on p.product_id=c.product_id
										join king_dealitems di on di.id=a.itemid
										join king_deals d on d.dealid=di.dealid
										join king_brands br on br.id=d.brandid
										where br.id='".$brandid."' and a.status !=3 and ki.is_pnh=1 and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$s."' and '".$e."'
										group by c.id
										order by ttl desc");
		
		if($top_prd_list->num_rows())
		{
			$output['top_prd_list']=$top_prd_list->result_array();
			$output['status']='success';
		}
		else
		{
			$output['status']="error";
			$output['message']="No Categories Found";
		}
		echo json_encode($output);	
	 }

	 /*
	 * 
	 * Ajax Function to get top Franchises by brand 
	 */
	 function jx_topfranchisebybrandid()
	 {
		$output=array();
		$brandid= $this->input->post('brandid');
		$start= $this->input->post('start_date');
		$end= $this->input->post('end_date');
		
		$s = date('Y-m-d',strtotime($start));
		$e = date('Y-m-d',strtotime($end));
		$top_fran_list = $this->db->query("select br.name,sum(a.quantity) as qty_sold,f.franchise_id,f.franchise_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
										from king_orders a
										join king_transactions ki on ki.transid=a.transid
										join pnh_m_franchise_info f on ki.franchise_id=f.franchise_id
										join king_dealitems di on di.id=a.itemid
										join king_deals d on d.dealid=di.dealid
										join king_brands br on br.id=d.brandid
										where br.id='".$brandid."' and a.status !=3 and ki.is_pnh=1 and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$s."' and '".$e."'
										group by f.franchise_id
										order by ttl desc");
		
		if($top_fran_list->num_rows())
		{
			$output['top_fran_list']=$top_fran_list->result_array();
			$output['status']='success';
		}
		else
		{
			$output['status']="error";
			$output['message']="No Categories Found";
		}
		echo json_encode($output);	
	 }
	 /*
	 * Ajax function to load territories by brandid 
	 * 
	 */
	function jx_top_sale_terr($bid="",$s="",$e="")
	{
		$st = date('Y-m-d',strtotime($s));
		$en = date('Y-m-d',strtotime($e));
		$sql = "select tr.id,tr.territory_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
													from king_orders a
													join king_transactions ki on ki.transid=a.transid
													join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
													join pnh_m_territory_info tr on tr.id=f.territory_id 
													join king_dealitems di on di.id=a.itemid
													join king_deals d on d.dealid=di.dealid
													join king_brands br on br.id=d.brandid
													where a.status!=3 and ki.is_pnh=1 and br.id='".$bid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
													group by tr.id
													order by ttl desc";
		 $res = $this->db->query($sql);
		 $sales_summary = array();
		 
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				array_push($sales_summary,array($row['territory_name'],$row['ttl']*1,$row['id'],$row['ttl']*1)); 		
			}
		}
		$output = array();
		$output['summary'] = $sales_summary;
		echo json_encode($output);
	}
	/*
	 * 
	 * Ajax Function to get towns by territory and brand
	 */
	 function jx_gettownsbybrandid($bid="",$tid="",$s="",$e="")
	 {
		$st = date('Y-m-d',strtotime($s));
		$en = date('Y-m-d',strtotime($e));
		$output=array();
		$town_list = "select tw.id,tw.town_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
											from king_orders a
											join king_transactions ki on ki.transid=a.transid
											join king_dealitems di on di.id=a.itemid
											join king_deals d on d.dealid=di.dealid
											join king_brands br on br.id=d.brandid
											join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
											join pnh_m_territory_info tr on tr.id=f.territory_id 
											join pnh_m_states st on st.state_id = tr.state_id
											join pnh_towns tw on tw.id=f.town_id
											where br.id='".$bid."' and ki.is_pnh = 1 and tr.id='".$tid."' and a.status !=3  and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
											group by tw.town_name
											order by ttl desc";
		 $res = $this->db->query($town_list);
		 $sales_summary = array();
		 
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				array_push($sales_summary,array($row['town_name'],$row['ttl']*1,$row['id'],$row['ttl']*1)); 		
			}
		}
		$output = array();
		$output['summary'] = $sales_summary;
		echo json_encode($output);	
	 }
	 /*
	 * 
	 * Ajax Function to get Franchises by territory and brand 
	 */
	 function jx_getfranchisebybrand($bid="",$tid="",$s="",$e="")
	 {
		$st = date('Y-m-d',strtotime($s));
		$en = date('Y-m-d',strtotime($e));
		 
		$output=array();
		$fran_list = "select f.franchise_id,f.franchise_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
											from king_orders a
											join king_transactions ki on ki.transid=a.transid
											join king_dealitems di on di.id=a.itemid
											join king_deals d on d.dealid=di.dealid
											join king_brands br on br.id=d.brandid
											join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
											join pnh_m_territory_info tr on tr.id=f.territory_id 
											join pnh_m_states st on st.state_id = tr.state_id
											where a.status!=3 and ki.is_pnh=1 and br.id='".$bid."' and tr.id='".$tid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
											group by f.franchise_id
											order by ttl desc limit 10";
		$res = $this->db->query($fran_list);
		 $sales_summary = array();
		 
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				array_push($sales_summary,array($row['franchise_name'],$row['ttl']*1)); 		
			}
		}
		$output = array();
		$output['summary'] = $sales_summary;
		echo json_encode($output);	
	 }
	 
	 
	 
	 	function jx_order_payment_det($s="",$e="",$fid="")
	{
		$st = date('Y-m-d',strtotime($s));
		$en = date('Y-m-d',strtotime($e));
		$date_diff = date_diff_days($en,$st);
		
		$order_cond = "group by date(from_unixtime(ki.init)) order by date(from_unixtime(ki.init)) asc";
		$payment_cond = "group by date(from_unixtime(instrument_date)) order by date(from_unixtime(instrument_date)) asc";
		
		 $sales = "select date_format(from_unixtime(ki.init),'%d-%m-%Y') as mn,sum(a.quantity) as q,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as total_value
					from king_orders a
					join king_transactions ki on ki.transid=a.transid
					left join king_dealitems b on b.id=a.itemid
					left join king_deals c on c.dealid=b.dealid
					left join pnh_menu d on d.id=c.menuid
					where ki.franchise_id ='".$fid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
					$order_cond";
		 			
		$payment = "SELECT receipt_amount,date_format(from_unixtime(instrument_date),'%d-%m-%Y') as d,f.franchise_name,a.name AS admin
					FROM pnh_t_receipt_info r
					JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
					LEFT OUTER JOIN king_admin a ON a.id=r.created_by
					WHERE r.status=1 AND r.is_active=1 AND (is_submitted=1 or r.activated_on!=0) and r.is_active=1 and r.franchise_id='".$fid."'
					and date_format(from_unixtime(instrument_date),'%Y-%m-%d') between '".$st."' and '".$en."'
					$payment_cond";
		
		$realized =	"SELECT receipt_amount,date_format(from_unixtime(activated_on),'%d-%m-%Y') as d,f.franchise_name,a.name AS admin
					FROM pnh_t_receipt_info r
					JOIN pnh_m_franchise_info f ON f.franchise_id=r.franchise_id
					LEFT OUTER JOIN king_admin a ON a.id=r.created_by
					WHERE r.status=1 AND r.is_active=1 AND (is_submitted=1 or r.activated_on!=0) and r.is_active=1 and r.franchise_id='".$fid."'
					and date_format(from_unixtime(activated_on),'%Y-%m-%d') between '".$st."' and '".$en."'
					$payment_cond";		
		 
		$shipped = "select ifnull(sum((b.mrp-(b.discount+b.credit_note_amt))*b.invoice_qty),0) as amt,date_format(a.shipped_on,'%d-%m-%Y') as mn
					from shipment_batch_process_invoice_link a
					join king_invoice b on a.invoice_no = b.invoice_no and b.invoice_status = 1
					join king_orders c on c.id = b.order_id
					join king_transactions d on d.transid = c.transid
					where date_format(a.shipped_on,'%Y-%m-%d') between '".$st."' and '".$en."' and a.shipped = 1
					and is_pnh = 1 and franchise_id  ='".$fid."' group by mn asc";
						
		 $sale_anl = $this->db->query($sales);
		 $pay_anl = $this->db->query($payment);
		 $ship_anl = $this->db->query($shipped);
		 $realized_anl = $this->db->query($realized);
		
		 $order_summary = array();
		 $payment_summary = array();
		 $realized_summary = array();
		 $shipped_summary = array();
		
		 $order_date=array();
		 $payment_date=array();
		 $shipped_date=array();
		 $realized_date=array();
		
		 $sale_ticks=array();
		 $payment_ticks=array();
		 $shipped_ticks=array();
		 $realized_ticks=array();
		 
		 $ttl_ord_sales_amt=0;
		 $ttl_payment_amt=0;
		 $ttl_shipped_amt=0;
		
		 $t_array=array();
		 $t=array();
		 $ticks=array();
		 $x_ticks=array();
		 if($sale_anl->num_rows())
		 {
			foreach($sale_anl->result_array() as $row)
			{
				$order_summary[($row['mn'])]=$row['total_value']*1;
				$order_date[($row['mn'])]=$row['mn'];
				$sale_ticks[]=strtotime($row['mn']);
				$ttl_ord_sales_amt+=$row['total_value']*1;
			}
		 }
		 if($pay_anl->num_rows())
		 {
			foreach($pay_anl->result_array() as $row)
			{
				$payment_summary[($row['d'])]=$row['receipt_amount']*1;
				$payment_date[($row['d'])]=$row['d'];
				$payment_ticks[]=strtotime($row['d']);
				$ttl_payment_amt+=$row['receipt_amount']*1;
			}
		 }
		 if($realized_anl->num_rows())
		 {
			foreach($realized_anl->result_array() as $row)
			{
				$realized_summary[($row['d'])]=$row['receipt_amount']*1;
				$realized_date[($row['d'])]=$row['d'];
				$realized_ticks[]=strtotime($row['d']);
			}
		 }
		 if($ship_anl->num_rows())
		 {
			foreach($ship_anl->result_array() as $row)
			{
				$shipped_summary[($row['mn'])]=$row['amt']*1;
				$shipped_date[($row['mn'])]=$row['mn'];
				$shipped_ticks[]=strtotime($row['mn']);
				$ttl_shipped_amt+=$row['amt']*1;
			}
		 }
		 
		 //print_r($order_date);print_r($payment_date);exit;
		 $t_array=array_merge($sale_ticks,$payment_ticks,$shipped_ticks,$realized_ticks);
		 sort($t_array);

		 $ticks=array_values(array_unique($t_array));
		 $order_summary_fnl = array();
		 $payment_summary_fnl = array();
		 $realized_summary_fnl = array();
		 $shipped_summary_fnl = array();
		 
		 foreach($ticks as $i=>$t)
		 {
			$t1 = date('d-m-Y',$t);
			if(isset($order_summary[$t1]))
				$order_summary_fnl[] = array($i+1,$order_summary[$t1],$order_date[$t1],$order_summary[$t1]);
			
			if(isset($payment_summary[$t1]))
				$payment_summary_fnl[] = array($i+1,$payment_summary[$t1],$payment_date[$t1],$payment_summary[$t1]);
			
			if(isset($realized_summary[$t1]))
				$realized_summary_fnl[] = array($i+1,$realized_summary[$t1],$realized_date[$t1],$realized_summary[$t1]);
			
			if(isset($shipped_summary[$t1]))
				$shipped_summary_fnl[] = array($i+1,$shipped_summary[$t1],$shipped_date[$t1],$shipped_summary[$t1]);
			
			$x_ticks[]=date('d-M-Y',$t);
		 }
		$output = array();
		$output['date_diff'] = $date_diff;
		$output['ticks'] = $x_ticks;
		$output['summary'] = $order_summary_fnl;
		$output['payment'] = $payment_summary_fnl;
		$output['shipped'] = $shipped_summary_fnl;
		$output['realized'] = $realized_summary_fnl;
		$output['ttl_summary']='Rs '.format_price($ttl_ord_sales_amt,0);
		$output['ttl_payment']='Rs '.format_price($ttl_payment_amt,0);
		$output['ttl_shipped']='Rs '.format_price($ttl_shipped_amt,0);
		echo json_encode($output);
	}

	 /*
	 * 
	 * Ajax Function to get order details by franchise_id
	 */
	 function jx_order_det_franchise_id()
	 {
		$date= $this->input->post('date');
		$fid= $this->input->post('franid');
		$d = date('Y-m-d',strtotime($date));
		
		$output=array();
		$ord_det = $this->db->query("select date_format(from_unixtime(ki.init),'%d-%m-%Y') as mn,sum(a.quantity) as q,br.name,f.product_id,f.product_name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as total_value
								from king_orders a
								join king_transactions ki on ki.transid=a.transid
								left join king_dealitems b on b.id=a.itemid
								left join king_deals c on c.dealid=b.dealid
								left join king_brands br on br.id=c.brandid
								left join pnh_menu d on d.id=c.menuid
								join m_product_deal_link e on e.itemid=a.itemid
								join m_product_info f on f.product_id=e.product_id
								where ki.franchise_id ='".$fid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') = '".$d."'
								group by f.product_id order by q desc");
		if($ord_det->num_rows())
		{
			$output['ord_det']=$ord_det->result_array();
			$output['status']='success';
		}
		else
		{
			$output['status']="error";
			$output['message']="No Order Details Found";
		}
		echo json_encode($output);	
	 }
	 
	/*
	 * 
	 * Indivisual Franchise menu sales statistics 
	 */ 
	function jx_order_getsales_bymenu($fid="")
	{
		$ord_menu =$this->db->query("select d.name,d.id,ki.franchise_id,sum(a.quantity) as qty_sold from king_orders a
							 join king_transactions ki on ki.transid=a.transid
							left join king_dealitems b on b.id=a.itemid
							left join king_deals c on c.dealid=b.dealid
							left join pnh_menu d on d.id=c.menuid
							where ki.franchise_id ='".$fid."' group by id");
		$menu=array();
		
		if($ord_menu->num_rows())
		{
			foreach($ord_menu->result_array() as $row)
			{
				array_push($menu,array($row['name'],$row['qty_sold']*1,$row['id'])); 
			}
		}
		$output = array();
		$output['result'] = $menu;
		echo json_encode($output);		
	}

	/*
	 * Ajax function to load brands by franchise_id 
	 * 
	 */
	function jx_brandsbyfranid($fid="",$s="",$e="")
	{
		$st = date('Y-m-d',strtotime($s));
		$en = date('Y-m-d',strtotime($e));
		$date_diff = date_diff_days($en,$st);
		$sql = "select br.name as name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
				from king_orders a
				join king_transactions ki on ki.transid=a.transid
				join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
				join king_dealitems di on di.id=a.itemid
				join king_deals d on d.dealid=di.dealid
				join king_brands br on br.id=d.brandid
				where a.status!=3 and ki.is_pnh=1 and f.franchise_id='".$fid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."' 
				group by br.id
				order by ttl desc ";
		 $res = $this->db->query($sql);
		 $sales_summary = array();
		 
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				array_push($sales_summary,array($row['name'],$row['ttl']*1)); 		
			}
		}
		$output = array();
		$output['summary'] = $sales_summary;
		echo json_encode($output);
	}

	/*
	 * Ajax function to load brands by menuid and franchise id
	 * 
	 */
	function jx_brandsbymenuid($id="",$fid="",$s="",$e="")
	{
		$st = date('Y-m-d',strtotime($s));
		$en = date('Y-m-d',strtotime($e));
		$date_diff = date_diff_days($en,$st);
		$sql = "select br.name as name,ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as ttl
				from king_orders a
				join king_transactions ki on ki.transid=a.transid
				join pnh_m_franchise_info f on f.franchise_id=ki.franchise_id
				join king_dealitems di on di.id=a.itemid
				join king_deals d on d.dealid=di.dealid
				join king_brands br on br.id=d.brandid
				join pnh_menu m on m.id=d.menuid
				where a.status!=3 and ki.is_pnh=1 and d.menuid='".$id."' and f.franchise_id='".$fid."' and date_format(from_unixtime(ki.init),'%Y-%m-%d') between '".$st."' and '".$en."'
				group by br.id
				order by ttl desc ";
		 $res = $this->db->query($sql);
		 $sales_summary = array();
		 
		if($res->num_rows())
		{
			foreach($res->result_array() as $row)
			{
				array_push($sales_summary,array($row['name'],$row['ttl']*1)); 		
			}
		}
		$output = array();
		$output['summary'] = $sales_summary;
		echo json_encode($output);
	}

	/*
	 * Ajax to load top ordered product 
	 * 
	 */ 
	function jx_prod_det_bymenu()
	{
		$fid= $this->input->post('franid');
		$m= $this->input->post('menu_id');
		$s=$this->input->post('start_date');
		$e=$this->input->post('end_date');
		$st = date('Y-m-d',strtotime($s));
		$en = date('Y-m-d',strtotime($e));
		$date_diff = date_diff_days($en,$st);
		$output=array();
		
		$top_sold_list = $this->db->query("select ROUND(SUM((i_orgprice-(i_coup_discount+i_discount))*a.quantity),2) as total_value,date_format(from_unixtime(ki.init),'%d-%b-%Y') as d,f.product_id,d.name,f.product_name,d.id,ki.franchise_id,sum(a.quantity) as qty_sold 
									from king_orders a
									join king_transactions ki on ki.transid=a.transid
									join king_dealitems b on b.id=a.itemid
									join king_deals c on c.dealid=b.dealid
									join pnh_menu d on d.id=c.menuid
									join m_product_deal_link e on e.itemid=a.itemid
									join m_product_info f on f.product_id=e.product_id
									where ki.franchise_id =? and d.id=? and date_format(from_unixtime(ki.init),'%Y-%m-%d') between ? and ? 
									group by product_id order by qty_sold desc", array($fid,$m,$st,$en));
		if($top_sold_list->num_rows())
		{
			$output['top_prod_list']=$top_sold_list->result_array();
			$output['status']='success';
		}
		else
		{
			$output['status']="error";
			$output['message']="No Data Found";

		}
			echo json_encode($output);
	}
	
	
		
}
