<?php

class Work extends Controller{
	
	function index()
	{
		echo '<html>
				<head><title>Men at work</title></head>
				<body>
					<table width="100%" height="100%">
					<tr>
						<td align="center" valign="center">
							<img src="http://static.snapittoday.com/upgrade.png">
						</td>
					</tr>
				</body>

				</html>
		';
	}
	
	function ses_mail()
	{
		$this->load->library("email");
		$this->load->model("viakingmodel","dbm");
		$this->dbm->email("support@snapittoday.com","SES test","test mail sent from snapittoday.com server thru ses. pls forward me the message headers of this email",true);
		echo "mail sent";
		echo $this->email->print_debugger();
	}
	
	
	function discontinue()
	{
		foreach($this->db->query("select catid,brandid from discontinued")->result_array() as $r)
			$this->db->query("update king_deals set discontinued=1 where catid=? and brandid=?",array($r['catid'],$r['brandid']));
	}
	
}	