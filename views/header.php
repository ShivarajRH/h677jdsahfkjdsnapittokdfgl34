<div style="float:right;font-family:arial;font-size:11px;color:#999;"></div>
<div id="hd">
<?php 
if(!isset($smallheader)&& !isset($adminheader)&& !isset($superadmin)){ 
		$this->load->view('site_header');
  } else if(isset($adminheader) || isset($superadmin)){
  		$this->load->view('admin_header');
}
?> 	
</div>