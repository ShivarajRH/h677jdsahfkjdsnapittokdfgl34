<?php
function build_menu($menus_det,$mainclass='menu',$subclass='submenu',$allowed_menu_ids=array())
{
	//print_r($menus_det);
	$menu_config = array();
	$menu_parent_index = array();
	$processed_tree_index = array();
	
	foreach($menus_det as $menu)
	{
		$mid = $menu['id'];
		$mp_id = $menu['parent'];
		$mord= $menu['menu_order'];
		
		if(count($allowed_menu_ids))
			$menu['flag'] = 0;
		else
			$menu['flag'] = 1;


		if(!isset($menu_config[$mp_id]))
		{
			$menu_config[$mp_id] = array();
			$menu_config[$mp_id]['order'] = array();
		}

		if(!isset($menu_config[$mp_id]['order'][$mord]))
			$menu_config[$mp_id]['order'][$mord] = $mid;


		$menu_parent_index[$mid] = $mp_id;
		$menu_tree_index = array();

		$t_mp_id = $mid;

		while(true)
		{
			if(!isset($menu_parent_index[$t_mp_id]))
			{
				break;
			}


			if($menu_parent_index[$t_mp_id] == 0)
			{
				if(count($menu_tree_index))
					array_push($menu_tree_index,0);
				break;
			}

			if($t_mp_id != $mp_id)
				array_push($menu_tree_index,$t_mp_id);

			$t_mp_id = $menu_parent_index[$t_mp_id];

			array_push($menu_tree_index,$t_mp_id);

		}
		
		$menu_config[$mp_id][$mid]['det'] = $menu;
		
		if(count($menu_tree_index))
		{
			$is_allowed = in_array($mid,$allowed_menu_ids)?1:0;
			
			
			if($is_allowed)
			{
				//print_r($menu_tree_index);
			}
			
			for($k=0;$k<count($menu_tree_index);$k++)
			{
				if($menu_tree_index[$k] == 0)
					break;
				if(isset($menu_config[$menu_tree_index[$k+1]]) && isset($menu_config[$menu_tree_index[$k]]))
				{
					if(isset($menu_config[$menu_tree_index[$k+1]][$menu_tree_index[$k]]))
					{
						$menu_config[$menu_tree_index[$k]][$menu_tree_index[$k-1]]['det']['flag']+=$is_allowed;
						$menu_config[$menu_tree_index[$k+1]][$menu_tree_index[$k]]['det']['flag']+=$is_allowed;
						$menu_config[$menu_tree_index[$k+1]][$menu_tree_index[$k]][0]=$menu_config[$menu_tree_index[$k]];
					}
				}
			}
		}

	}
	 
	
	$menu_config = $menu_config[0];
	 
	
	

	//main menu function
	/*function print_menu_recur($morder_indx,$menu_config,$type)
	{
		global $mainclass,$subclass;
		
		echo '<ul class="'.($type?'menu':'').'">';
		foreach($morder_indx as $mid)
		{
			$menu_det = $menu_config[$mid];
			echo '<li><a href="'.site_url($menu_det['det']['link']).'">'.$menu_det['det']['name'].'</a>';
			 
			if(isset($menu_det[0]))
			{
				print_menu_recur($menu_det[0]['order'],$menu_det[0],0);
			}
			 
			echo '</li>';

		}
		echo '</ul>';
	}
	print_menu_recur($menu_config['order'],$menu_config,1);*/
	
	 

	/*function print_menu_recur($morder_indx,$menu_config,$type)
	{
		global $mainclass,$subclass;
	
		echo '<ul  id="'.($type?'jMenu':'').'">';
		
		foreach($morder_indx as $mid)
		{
			$menu_det = $menu_config[$mid];
			
				echo '<li><a href="'.site_url($menu_det['det']['link']).'"  class="'.($type?'fNiv':'').'">'.$menu_det['det']['name'].'</a>';
	
			if(isset($menu_det[0]))
			{
	
				print_menu_recur($menu_det[0]['order'],$menu_det[0],0);
			}
			 
			echo '</li>';
	
		}
		echo '</ul>';
	}
	print_menu_recur($menu_config['order'],$menu_config,1);*/
	
	
	function print_menu_recur($morder_indx,$menu_config,$type,$menuClass='')
	{
		global $mainclass,$subclass;
	
		echo '<ul  class="'.$menuClass.'">';
			if($type)
				echo '<li><a href="'.site_url("admin/dashboard").'">Dashboard</a>';
		foreach($morder_indx as $mid)
		{
			$menu_det = $menu_config[$mid];
			if(!$menu_det['det']['flag'])
				continue;
				
				echo '<li>';
	
			echo '<a href="'.site_url($menu_det['det']['link']).'"  class="'.($type?'':'').'">'.$menu_det['det']['name'].'</a>';
			if(isset($menu_det[0]))
			{
				print_menu_recur($menu_det[0]['order'],$menu_det[0],0,($type==1)?'':'submenu');
			}
			echo '</li>';
	
		}
		echo '</ul>';
	}
	print_menu_recur($menu_config['order'],$menu_config,1,'menu');

}
