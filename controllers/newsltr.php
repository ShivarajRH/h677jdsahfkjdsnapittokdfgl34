<?php

class Newsltr extends Controller {
	
	private function newsletter($id = 1) {
		$this->load->view ( "newsletters/$id" );
	}
	
	function view($campaign_no = '') {
		
		if (! $campaign_no)
			show_404 ();
			
			
		$data ['deal_list'] = array ();
		$campaign_det = $this->db->query("select c.*,t.template_filename 
												from king_campaigns c 
												join king_campaign_templates t on t.id = c.template_id 
												where campaign_no = ? ",$campaign_no)->row_array();
			
		if(!$campaign_det){
			show_error("Campaign not found ,for any assistance reachus @ <a href='mailto:contact@snapittoday.com'>contact@snapittoday.com</a>");
			exit;
		}
		
		if(!$campaign_det['is_active']){
			show_error("Campaign is not active,for any assistance reachus @ <a href='mailto:contact@snapittoday.com'>contact@snapittoday.com</a>");
			exit;
		}
		
		if($campaign_det['campaign_type'] == 'deal'){
		
			$sql = "select di.name,di.url,d.pic,di.orgprice,di.price
						from king_deals d  
						join king_dealitems di on di.dealid = d.dealid 
						join king_campaigns_deals cd  on cd.deal_id = d.dealid 
						where cd.campaign_no = ? 
						and d.publish=1 and di.live=1 and is_active = 1 
						order by cd.order asc 
					";
			
			$res = $this->db->query ( $sql, $campaign_no );
			//echo $this->db->last_query(); 
			if ($res->num_rows ()) {
			
			} else {
				
				$sql = "select di.name,di.url,d.pic,di.orgprice,di.price
										from king_deals d  
										join king_dealitems di on di.dealid = d.dealid 
										join king_campaigns_deals cd  on cd.deal_id = d.dealid 
										where cd.campaign_no = (select campaign_no from king_campaigns order by id desc limit 1 ) and d.publish=1 and di.live=1 and is_active = 1 order by cd.order asc ";
				$res = $this->db->query ( $sql );
				//echo $this->db->last_query();
			}
			$data ['deal_list'] = $res->result_array ();
		}
		
		$data ['campaign_det'] = $campaign_det;
		
		$data ['campaign_no'] = $campaign_no;
		 
		//echo '1';
		
		$this->load->view ( "newsletters/".$campaign_det['template_filename'], $data );
		
	//echo '3';
	}

}