<?php
/**
 * 
 * IMAP Library
 * @author Vimal Sudhan
 *
 */
class Imap{

	private $stream=NULL;
	private $server="{imap.googlemail.com:993/imap/ssl/novalidate-cert}";
	
	function __construct()
	{
		
	}
	
	function __destruct()
	{
		if($this->stream)
			imap_close($this->stream);
	}
	
	function login($username,$password)
	{
		$f=$this->stream=imap_open("{$this->server}INBOX",$username,$password);
		if(!$f)
			return false;
		return true;
	}
	
	function is_newmsg($luid)
	{
		$check = imap_mailboxmsginfo($this->stream);
		$uid=imap_uid($this->stream,$check->Nmsgs);
		if($luid<$uid)
			return $uid;
		return false;
	}
	
	function readmail($uid)
	{
		$msg=imap_msgno($this->stream, $uid);
		if(!$msg)
			return false;
		$header=imap_header($this->stream,$msg);
		if(empty($header))
			return false;
		$ret['from']="{$header->sender[0]->mailbox}@{$header->sender[0]->host}";
		$ret['subject']=$header->subject;
		$struct=imap_fetchstructure($this->stream, $msg);
		$pno=2;
		if(!isset($struct->parts))
			$pno=1;
		$ret['msg']=imap_fetchbody($this->stream,$msg,"$pno");
		if($pno!=2)
			$ret['msg']=nl2br($ret['msg']);
//		echo imap_body($this->stream, $msg);
		return $ret;
	}
	
}