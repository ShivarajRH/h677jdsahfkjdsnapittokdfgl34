<?php
/* application/hooks/PageLoadAnalyzer.php */
class PageLoadAnalyzer 
{
	function startPageLog()
	{
		$CI = &get_instance();
		
		list($m,$t) = explode(' ', microtime());
		$ctime = $m+$t;
		 
		$CI->st_mem_usage = memory_get_usage();
		 
		
		$inp = array();
		$inp['user_id'] = 0;
		$inp['ipaddress'] = $_SERVER['REMOTE_ADDR'];
		$inp['visited_url'] = current_url();//$CI->config->site_url($CI->uri->uri_string());
		$inp['reference_method'] = $_SERVER['REQUEST_METHOD'];
		$inp['started_on'] = $ctime;
		$inp['loaded_queries'] = '';
		$inp['request_data'] = json_encode($_POST);
		$CI->db->insert('log_pageloads',$inp)  or die(mysql_error());
		$CI->loaded_page_indx = $CI->db->insert_id();
		
	}
	
	function stopPageLog()
	{
		$CI =& get_instance();
		$times = $CI->db->query_times;
		$dbs    = array();
		$output = NULL;
		$queries = $CI->db->queries;
		
		if (count($queries) == 0){
			$output .= "no queries\n";
		}else{
			foreach ($queries as $key=>$query){
				$output .= $query . "\n";
			}
			$took = round(doubleval($times[$key]), 3);
			$output .= "===[took:{$took}]\n\n";
		}
		
		$CI->en_mem_usage = memory_get_usage();
		
		$ttl_elapsed_time = '';//$CI->benchmark->elapsed_time();
		$ttl_memory_used = $CI->en_mem_usage-$CI->st_mem_usage;//$CI->benchmark->memory_usage();
		list($m,$t) = explode(' ', microtime());
		$ctime = $m+$t;
		$CI->db->query("update log_pageloads set loaded_queries = ?,stopped_on=? where id = ? ",array($output,$ctime,$CI->loaded_page_indx)) or die(mysql_error());
		
		$CI->db->query("update log_pageloads set elapsed_time = stopped_on-started_on,memory_usage=? where id = ? ",array($ttl_memory_used,$CI->loaded_page_indx)) or die(mysql_error());
		
	}
	
	
}