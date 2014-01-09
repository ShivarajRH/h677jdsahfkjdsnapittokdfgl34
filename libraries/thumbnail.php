<?php

/**
 * Image thumbnail creation library
 * 
 * @copyright 2010 VIA
 * @author Vimal
 * @since 28 May 2010
 * @version 0.2
 */

//													26 Jun 10

class Thumbnail{
	
	private $CI;
	private $errors=array();
	private $error=false;
	
	function Thumbnail()
	{
		$this->CI=& get_instance();
		$this->CI->load->library("image_lib");
	}
	
	private function do_gd($config)
	{
		$this->CI->image_lib->clear();
		$this->CI->image_lib->initialize($config);	
		if(!$this->CI->image_lib->resize())
		{
			$this->error=true;
			return false;
		}
		return true;
	}
	
	function create($arg=array())
	{
		$this->CI->image_lib->clear();
		$config=array("image_library"=>"GD2",
					"source_image"=>"",
					"maintain_ratio"=>true,
					"width"=>0,
					"height"=>0,
					"master_dim"=>"auto",
						);
		
		$this->errors=array();
		$this->error=false;
		
		if(empty($arg))
		{
			$this->errors[]="No configuration options";
			return false;
		}
		
		if(!isset($arg['source']))
			$this->errors[]="No source image";
		
		if(!file_exists($arg['source']))
			$this->errors[]="Source file not exists";

		$imgsize=@getimagesize($arg['source']);
			
		if($imgsize==false)
		{
			$this->error=true;
			$this->errors[]="Invalid image format";
		}
			
		if(!isset($arg['height']) && !isset($arg['width']))
			$this->errors[]="No height or width specified";
		
		if(isset($arg['hard_values']) && $arg['hard_values']==true && (!isset($arg['height']) || !isset($arg['width'])))
			$this->errors[]="Both height and width is needed for 'hard_values' option";
			
		if(!empty($this->errors))
			return false;
		
		$config['source_image']=$arg['source'];
		
			
		if(isset($arg['height']))
			$config['height']=$config['width']=$arg['height'];
		else
			$config['master_dim']="width";

		if(isset($arg['width']))
			$config['width']=$config['height']=$arg['width'];
		else
			$config['master_dim']="height";
		
		if(isset($arg['hard_values']) && $arg['hard_values']==true)
		{
			unset($config['master_dim']);
			$config['height']=$arg['height'];
			$config['width']=$arg['width'];
			$config['maintain_ratio']=false;
		}
		
/*		if(!isset($arg['hard_Values']))
		{
			if(isset($arg['max_width']) && isset($arg['height']))
			{
				if($imgsize[0]>$arg['max_width'])
				{
					$config['height']=$config['width']=$arg['max_width'];
					$config['master_dim']="width";
				}
			}
			if(isset($arg['max_height']) && isset($arg['width']))
			{
				if($imgsize[1]>$arg['max_height'])
				{
					$config['height']=$config['width']=$arg['max_height'];
					$config['master_dim']="height";
				}
			}
		}
*/		
		if(isset($arg['dest']))
			$config['new_image']=$arg['dest'];

		if(!$this->do_gd($config))
			return false;
			
		if(!isset($arg['hard_values']))
		{
			if(isset($arg['dest']))
				$dest=$arg['dest'];
			else
				$dest=$arg['source'];
			$imgsize=@getimagesize($dest);
			if($imgsize==false)
			{
				$this->errors[]="Unable to get image size after resizing at ".$dest.". Anyway, the image resize was success ";
				return false;
			}
			
			if(isset($arg['max_width']) && isset($arg['height']))
			{
				if($imgsize[0]>$arg['max_width'])
				{
					$config['height']=$config['width']=$arg['max_width'];
					$config['master_dim']="width";
					@unlink($dest);
					if(!$this->do_gd($config))
						return false;
				}
			}
			if(isset($arg['max_height']) && isset($arg['width']))
			{
				if($imgsize[1]>$arg['max_height'])
				{
					$config['height']=$config['width']=$arg['max_height'];
					$config['master_dim']="height";
					@unlink($dest);
					if(!$this->do_gd($config))
						return false;
				}
			}
		}
		
		return true;
	}
	
	function check($src)
	{
		if(@getimagesize($src)==false)
			return false;
		return true;
	}
	
	function get_errors($dm1="<p>",$dm2="</p>")
	{
		$ret="";
		if(!empty($this->errors))
			$ret=$dm1.implode($dm2.$dm1,$this->errors).$dm2;
		if($this->error)
			$ret.=$dm1."Internal Error from GD Library : ".$dm2.$this->CI->image_lib->display_errors($dm1,$dm2);
		return $ret;
	}
	
	function is_gd_error()
	{
		return $this->error;
	}
}
?>