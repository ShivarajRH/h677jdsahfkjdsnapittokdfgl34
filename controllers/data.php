<?php

class Data extends Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model("datamodel","dpm");
	}
	
	function index()
	{
		$this->dpm->process();
	}
	
	function get_dealsbyapi($client_code='fashionara')
	{
		//fetch data from api 
		// read and import data to erp
		// create product and deal entries
		  
	}
	
	function get_productstockbyapi($client_code='fashionara')
	{
		// fetch stock report from link
		// check for sku and update stock and price details in erp
	}
	
	function export_clientorderfile($client_code='fashionara',$date='')
	{
		// check for open orders for client products 
		// PO create 
		// PO product link 
		// 
		
		//csv output 		 
	}
	
}