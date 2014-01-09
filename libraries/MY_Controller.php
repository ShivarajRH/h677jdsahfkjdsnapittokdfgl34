<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	
/*
 * Custom MYCOntroller class
 */
class MY_Controller extends Controller {
	function MY_Controller() {
		parent::Controller ();
		$this->name = 'sharif';
	}

}

/*
 * Custom My admin Class
 */
class MY_Admin extends MY_Controller {
	function MY_Admin() {
		parent::MY_Controller ();
	
	}
	
	/*
	 * Admin Login Authentication check ...
	 * @access Public
	 */
	function check_login_status() {
		
		if (is_array ( $this->session->userdata ( 'admin_userdata' ) )) {
			
			$userdata = $this->session->userdata ( 'admin_userdata' );
			foreach ( $userdata as $key => $val ) {
				$this->$key = $val;
			}
			
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/* Activity Logger for display in admin main panel  */
	function activity_log($message = '') {
		$this->load->model ( 'admin_model' );
		$this->admin_model->activity_log ( $message );
		return;
	}
	
	/*
	 * Image Upload Function 
	 * @param $path
	 */
	function image_upload($field_name = 'userfile', $path, $file_name = '') {
		$config ['upload_path'] = $path;
		$config ['allowed_types'] = ALLOWED_IMAGE_TYPES;
		$config ['max_size'] = '0';
		$config ['max_width'] = MAX_IMAGE_UPLOAD_WIDTH;
		$config ['max_height'] = MAX_IMAGE_UPLOAD_HEIGHT;

		if ($file_name) {
			$config ['file_name'] = $file_name;
		}
		$this->upload->initialize($config);
		if (! $this->upload->do_upload ( $field_name )) {
			return array ('error' => $this->upload->display_errors () );
		} else {
			$data = array ('upload_data' => $this->upload->data () );
			return array ('error' => FALSE, 'img_data' => $data );
		}
	}
	function create_thumb($source_path, $dest_path = '', $thumb_width = 50, $thumb_height = 50, $thumb_marker = '_thumb') {
		$config ['image_library'] = 'gd2';
		$config ['source_image'] = $source_path;
		$config ['thumb_marker'] = $thumb_marker;
		list($flname) = explode(".",substr(strrchr($source_path, "/"), 1 ));
		if ($dest_path) {
			$config ['new_image'] = $dest_path."/$flname.jpg";
		}
//		die($config['new_image']);
		$config ['create_thumb'] = TRUE;
//		$config ['maintain_ratio'] = TRUE;
//			$config ['width'] = 300;
//			$config ['height'] = 300;
			$config ['maintain_ratio'] = TRUE;
			if($thumb_width>0)
			$config ['master_dim'] = 'width';
			else
			$config ['master_dim'] = 'height';
			$config ['quality'] = '100%';
		
		if ($thumb_width)
			$config ['width'] = $thumb_width;
		if ($thumb_height)
			$config ['height'] = $thumb_height;
		
		$this->image_lib->initialize ( $config );
		$this->image_lib->resize ();
		$this->image_lib->clear ();
	
	}
	
	/*
	 * Function to generate Ids 
	 */
	function generate_id() {
		$sql = "select CONCAT(FLOOR(1000 + (RAND() * 9000)),CHAR(FLOOR(65 + (RAND() * 26)),FLOOR(65 + (RAND() * 26)),FLOOR(65 + (RAND() * 26)),FLOOR(65 + (RAND() * 26))),UNIX_TIMESTAMP()) as gen_id";
		$res = $this->db->query ( $sql );
		$row = $res->result_array ();
		return $row [0] ['gen_id'];
	}
	
	/*
	 * Delete Images 
	 * function to delete images from application folders  .
	 * @param $path , $filename
	 */
	
	function delete_images($path, $filename) {
		
		$options = array ('/', '/original/', '/thumb/' );
		foreach ( $options as $folder ) {
			
			if ($folder != '/thumb/')
				$file = $path . $folder . $filename;
			else
				$file = $path . $folder . str_replace ( '.', '_thumb.', $filename );
			
			if (is_file ( $file )) {
				unlink ( $file );
			} else {
				//echo $file;
			}
		}
		
		return TRUE;
	}
}

?>