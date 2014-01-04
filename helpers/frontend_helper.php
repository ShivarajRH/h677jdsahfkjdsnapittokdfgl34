<?php
	/**
	 * Random characters generator
	 * 
	 * Function to create random characters with small case.
	 * 
	 * @param int $len length
	 * @return string random characters of given length
	 */
	function randomChars($len) {
		$str = "";
		$charcode = ord ( "a" );
		$i = 0;
		while ( $i < $len ) {
			$rad = rand ( 0, 3 );
			if ($rad == 0 || $rad == 1)
				$str = $str . chr ( $charcode + rand ( 0, 15 ) );
			else
				$str = $str . rand ( 0, 9 );
			$i = $i + 1;
		}
		return $str;
	}
	
	function random_alpha($len)
	{
		$str="";
		$c=ord("A");
		for($i=0;$i<$len;$i++)
			$str.=chr($c+rand(0,25));
		return $str;
	}
	
	function incr_char($s)
	{
		$len=strlen($s);
		if($s{$len-1}=="Z")
		{
			for($i=$len-2;$i>=0;$i--)
				if($s{$i}!="Z")
					break;
			if($i<0)
			{
				$i=0;
				$s{0}="A";
			}else
			$s{$i}=chr(ord($s{$i})+1);
			for($i=$i+1;$i<$len;$i++)
				$s{$i}="A";
		}
		else
			$s{$len-1}=chr(ord($s{$len-1})+1);
		return $s;
	}
	
	function random_num($len)
	{
		$st="";
		for($i=0;$i<$len;$i++)
			$st.=rand(0,9);
		return $st;
	}
	
	function genid($len)
	{
		$st="";
		for($i=0;$i<$len;$i++)
			$st.=rand(1,9);
		return $st;
	}
	
	function startsWith($haystack, $needle)
	{
	    $length = strlen($needle);
	    return (substr($haystack, 0, $length) === $needle);
	}
	
	function endsWith($haystack, $needle)
	{
	    $length = strlen($needle);
	    $start  = $length * -1; //negative
	    return (substr($haystack, $start) === $needle);
	}
	
	
	 function breakstring($str,$len)
	{
		if(strlen($str)<$len)
			return $str;
		$nstr=substr($str,0,$len);
		if($nstr{$len-1}==' ')
		{
			$nstr{$len-1}=".";
			return $nstr."..";
		}
		if($str{$len}==' ')
			return $nstr."...";
		for($i=$len;$i<strlen($str);$i++)
		{
			if($str{$i}==' ' || $str{$i}==',')
				return $nstr."...";
			$nstr.=$str{$i};
		}
		return $nstr;
	}
	
	function is_from_google()
	{
		
		if(isset($_SERVER['HTTP_REFERER']) && (stripos($_SERVER['HTTP_REFERER'],"google.com/url")!==false || stripos($_SERVER['HTTP_REFERER'],"google.co.in/url")!==false))
			return true;
		return false;
	}
?>