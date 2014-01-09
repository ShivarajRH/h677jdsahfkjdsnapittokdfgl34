<?php

include APPPATH."libraries/Rc43.php";

class Ebs{
	
	private $secret="3c5d52b08311e52a72f7b9a81f7558ae";
	private $acc=7915;
	private $mode="LIVE";
	private $payurl="https://secure.ebs.in/pg/ma/sale/pay/";
	
	function getform($args)
	{
		$params=array("reference_no","amount","description","return_url","name","address","city","state","country","postal_code","phone","email","ship_name","ship_address","ship_city","ship_state","ship_country","ship_postal_code","ship_phone");
		$inp=$args;
		$inp["account_id"]=$this->acc;
		$inp['mode']=$this->mode;
		$ret='<form action="'.$this->payurl.'" method="post" id="payform">';
		foreach($inp as $name=>$arg)
			$ret.="<input type='hidden' name='{$name}' value='{$arg}'>";
		$ret.="</form>";
		return $ret;
	}
	
	function getresponse($DR)
	{
		 $DR = preg_replace("/\s/","+",$DR);
		
		 $rc4 = new Crypt_RC4($this->secret);
		 $QueryString = base64_decode($DR);
		 $rc4->decrypt($QueryString);
		 $QueryString = split('&',$QueryString);
		
		 $response = array();
		 foreach($QueryString as $param){
		 	$param = split('=',$param);
			$response[$param[0]] = urldecode($param[1]);
		 }
		 
		 return $response;
	}
	
}
