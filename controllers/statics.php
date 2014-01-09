<?php

class Statics extends Controller
{
	
	function intro($key=false)
	{
		if($key!="4242432rfwefsef23rwerfwer")
			die;
		$this->load->view("mails/intro");
	}
	
}
