<?php

 class Callcenter extends Controller{
 	
 	function __construct()
 	{
 		parent::__construct();
 		$this->load->model("callcm","dbm");
 		$this->load->model("erpmodel","erpm");
 		$this->erpm->loadroles();
 	}
 	
 	function index()
 	{
 		$user=$this->erpm->auth(true);
 		$data['page']="../../callcenter/body/recent";
 		$data['trans']=$this->dbm->getrecenttrans();
 		$this->load->view("admin",$data);
 	}
 	
 	function trans($transid)
 	{
 		$user=$this->erpm->auth(CALLCENTER_ROLE);
 		$data['trans']=$trans=$this->dbm->gettrans($transid);
 		if(empty($trans))
 		{
 			$data['page']="echo";
 			$data['echo']="<h2>Transaction not found</h2><div style='padding:20px;'></div>";
 		}
 		else{
 			$data['page']="../../callcenter/body/trans";
 			$data['pendings']=$this->dbm->getpendingorders($transid);
 			$data['orders']=$this->dbm->getorders($transid);
 		}
 		$this->load->view("admin",$data);
 	}
 	
 }