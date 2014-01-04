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
	
	
}