<?php
function suggest_grp_pages($items,$cells_in_block,$max_cell_per_page,$key_flag)
{
	$block=array();
	//sort($items);
	
	$pages_data=array();
	$count_arr=array();
	
	$ttl_items=count($items);

	foreach($items as $ic=>$item)
	{
		$ch_val=$item[$key_flag];
		$array_fileds=array_keys($item);
		
		if(!isset($block[$ch_val]))
		{
			$block_cnt=0;
			$prev_arr=end($block);
			$prev_arr_key=key($block);

			if($prev_arr)
			{
				$prev_id=$prev_arr[0][$key_flag];
				$block_cnt=count($prev_arr);
				if($block_cnt < $cells_in_block)
				{
					$temp_arr_cnt=$cells_in_block-$block_cnt;
					
					for($cell=0;$cell<$temp_arr_cnt;$cell++)
					{
						$temp_arr=array();
						foreach($array_fileds as $flds)
						{
							
							$temp_arr[$flds]='';
						}
						$temp_arr[$key_flag]=$prev_id;
						array_push($block[$prev_arr_key],$temp_arr);
					}
				}
				$count_arr[$prev_arr_key]=count($block[$prev_arr_key]);
				$pages_data[$prev_arr_key]=$block;
			}
			
			
			$block=array();
			$block[$ch_val]=array();
			$block[$ch_val][]=$item;
		}else{
			$block[$ch_val][]=$item;
		}

		if($ic+1==$ttl_items)
		{	
			$block_cnt=0;
			$block_cnt=count($block[key($block)]);
			if($block_cnt < $cells_in_block)
			{
				$temp_arr_cnt=$cells_in_block-$block_cnt;
				for($cell=0;$cell<$temp_arr_cnt;$cell++)
				{
					$temp_arr=array();
					foreach($array_fileds as $flds)
					{
						$temp_arr[$flds]='';
					}
					$temp_arr[$key_flag]=key($block);
					array_push($block[key($block)],$temp_arr);
				}
			}
			$count_arr[key($block)]=count($block[key($block)]);
			$pages_data[key($block)]=$block;
		}
	
	}
	
	arsort($count_arr);
	/*print_r($pages_data);
	echo "<br>";exit;*/
	
	 
	$cell_cnt=0;

	$pages=array();
	$allot_count = 0;
	$cur_page = 0;
	$count_arr_1 = array();
  
	foreach($count_arr as $gid=>$ttl)
	{
		 
		$tmp_ttl_pages = ceil($ttl/$max_cell_per_page);
		if(!isset($count_arr_1[$gid]))
				$count_arr_1[$gid] = array();

		$tmp_ttl = $ttl;
		for($p=0;$p<$tmp_ttl_pages;$p++)
		{
			$rem = $tmp_ttl%$max_cell_per_page;
			 if($rem)
				$cnt = $rem;
			else
				$cnt = $max_cell_per_page;

			array_push($count_arr_1[$gid],$cnt); 
			 
			$tmp_ttl = $tmp_ttl-$cnt;
		}
	}
		
	foreach($count_arr_1 as $gid=>$gid_split_list)
	{
		$i=0;
		$gid_split_list = array_reverse($gid_split_list);
		foreach($gid_split_list as $ttl)
		{	
			
			if(($allot_count+$ttl) <= $max_cell_per_page)
				$allot_count += $ttl;
			else
			{
				$allot_count = $ttl;
				$cur_page++;
			}

			if(!isset($pages[$cur_page]))
					$pages[$cur_page] = array();

			$ttl+=$i;
			for($k=$i;$k<$ttl;$k++)
			{
				if(!isset($pages_data[$gid][$gid][$k]))
					break; 
				 
				if(!isset($pages[$cur_page][$gid]))
						$pages[$cur_page][$gid] = array();
				
					array_push($pages[$cur_page][$gid],$pages_data[$gid][$gid][$k]);
			}
			$i=$k;
		}
		
	}
	return $pages;
}
?>