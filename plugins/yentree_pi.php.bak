<?php
function build_yentree($item_list, $mainclass = 'item', $subclass = 'subitem') {
	$item_config = array ();
	$item_parent_index = array ();
	$processed_tree_index = array ();
	
	// each item from item_list is traversed 
	foreach ( $item_list as $item ) {
		$mid = $item ['id'];
		$mp_id = $item ['parent'];


		if (! isset ( $item_config [$mp_id] )) {
			$item_config [$mp_id] = array ();
			$item_config [$mp_id] ['order'] = array ();
		}

		$mord = count($item_config [$mp_id] ['order']);

		if (! isset ( $item_config [$mp_id] ['order'] [$mord] ))
			$item_config [$mp_id] ['order'] [$mord] = $mid;

		$item_parent_index [$mid] = $mp_id;
		
		$item_tree_index = array ();

		$t_mp_id = $mid;

		while ( true ) {
			if (! isset ( $item_parent_index [$t_mp_id] )) {
				break;
			}
				
			if ($item_parent_index [$t_mp_id] == 0) {
				if (count ( $item_tree_index ))
					array_push ( $item_tree_index, 0 );
				break;
			}
				
			if ($t_mp_id != $mp_id)
				array_push ( $item_tree_index, $t_mp_id );
				
			$t_mp_id = $item_parent_index [$t_mp_id];
				
			array_push ( $item_tree_index, $t_mp_id );

		}

		$item_config [$mp_id] [$mid] ['det'] = $item;

		if (count ( $item_tree_index )) {
			//$item_tree_index = array_reverse($item_tree_index);
			$tmp_indx = $item_config;
			for($k = 0; $k < count ( $item_tree_index ); $k ++) {
				if ($item_tree_index [$k] == 0)
					break;
					
				if(!isset($item_tree_index [$k + 1]))
					continue;	
					
				if (isset ( $item_config [$item_tree_index [$k + 1]] ) && isset ( $item_config [$item_tree_index [$k]] )) {
					if (isset ( $item_config [$item_tree_index [$k + 1]] [$item_tree_index [$k]] ))
						$item_config [$item_tree_index [$k + 1]] [$item_tree_index [$k]] [0] = $item_config [$item_tree_index [$k]];
				}
					
			}
		}

	}
	
 
	return $item_config [0];

}
