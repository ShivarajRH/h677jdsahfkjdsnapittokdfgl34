<?php

class Trends extends Controller{

	function __construct()
	{
//		ini_set("display_errors","on");
//		error_reporting(0);
		parent::Controller();
		header("Content-Type: text/html; charset=UTF-8");
		header("Cache-Control: private, no-cache, no-store, must-revalidate");
		header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
		header("Pragma: no-cache");
		
		$this->load->library("cart");
		$this->load->library("email");
		$this->load->library("form_validation");
		$this->load->model("viakingmodel","dbm");
		$this->load->library("pettakam",array("repo"=>"cache","ext"=>"pkm_snp"));
	}
	
	function trend($name="")
	{
		if(empty($name))
			show_404();
		$data['deals']=$this->dbm->gettrend($name);
		if(empty($data['deals']))
			show_404();
		$data['page']="bycategory";
		$data['pagetitle']="Trend - $name";
		$this->load->view("index",$data);
	}
	
}