<?php

include_once 'xml2array.php';    			// to use xml2array()


class Agentapi	{
	
																		//declare private data members
	
	private $apiUrl='http://www.test.viaworld.in/internalService';
	private $auth="xyzabc";
	
																		//Constructor to initialize something	
	function Agentapi(){
		// initialize something
	}
	
/**
 * Create API link for given action Id and XmlRequest string
 *
 * @access private
 * @param string $actionId, action id for api request
 * @param string $xmlReq, xml request string
 * @return string created api link
 */
	private function createLink($actionId,$xmlReq)
	{
		$url=$this->apiUrl."?auth=".$this->auth."&actionId=".$actionId."&xmlRequest=".$xmlReq;
//		if($actionId=="ECommerceBook")
//		{
//		echo $url;die;
//		}
		return $url;
	}
	
/**
 * Establish curl session and get xml data from api link
 *
 * @access private
 * @param string $url, url from which xml data is got
 * @return string, xml response from server
 */
	private function getData($url)
	{
		$curlId=curl_init($url);
		curl_setopt($curlId,CURLOPT_RETURNTRANSFER,1);			// get response data not to stdout
		curl_setopt($curlId,CURLOPT_FRESH_CONNECT,1);
		$data=curl_exec($curlId);
		$header=curl_getinfo($curlId);
		if($header['http_code']==200)							// checking... all done good!  ;)
			return $data;
    	return false;
	}
	
/**
 * Creates Xml request for given parameters and request type
 *
 * @access private
 * @param int $reqType, request type (SearchApi_AirFareSearch, for now)
 * @param array $params, parameters needed to make request. Given as associative array 
 * @return string xml request
 */
	private function createXmlReq($reqType, $params)
	{
		if($reqType==0)
			$req="<AuthenticateUser><UserName>{$params['username']}</UserName><Password>{$params['password']}</Password><Role>reseller</Role></AuthenticateUser>";
			
		if($reqType==1)
			$req="<ECommerceBook><SessionId>".$params['sid']."</SessionId><Comments>ViaBazaar</Comments><Amount>".$params['amount']."</Amount></ECommerceBook>";
		return $req;
		
//		header('content-type:text/xml');
//		echo  $req;exit;
	}
	
	function authenticate($params)
	{
		$respDataXml=$this->getData($this->createLink("AuthenticateUser",$this->createXmlReq(0,$params)));
		if($respDataXml===false)											// something went wrong somewhere :(
			return false;
		
		$respData=xml2array($respDataXml);									// Convert xml into array. just
//		print_r($respData);die;
		return $respData;
	}
	
	function transaction($params)
	{
		$respDataXml=$this->getData($this->createLink("ECommerceBook",$this->createXmlReq(1,$params)));
		if($respDataXml===false)											// something went wrong somewhere :(
			return false;
		
		$respData=xml2array($respDataXml);									// Convert xml into array. just
//		print_r($respData);die;
		return $respData;
	}
	
}
