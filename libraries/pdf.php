<?php

require(APPPATH.'libraries/fpdf/fpdf.php');

class PDF extends FPDF
{
	public $first=0;
	private $doc_type;
	
	function __construct()
	{
		parent::__construct();
		$this->SetMargins(10,10);
	}
	
	function doc_type($title)
	{
		$this->doc_type=$title;
	}
	
// Page header
function Header()
{
//	$this->image("images/pnh_logo_small.png");
	$this->SetFont('Arial','B',10);
	$this->Cell(0,0,'StoreKing');
	$this->SetFont('Arial','B',8);
	$this->Cell(0,0,$this->doc_type,0,0,'R');
    $this->Ln(5);
    $this->Cell(0,0,"","B");
    $this->Ln(5);
}

// Page footer
function Footer()
{
	$this->first++;
	if($this->first==1)
		return;
	// Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}

	function account_stat($header, $data)
	{
	    $this->SetFillColor(hexdec("EF"),hexdec("4A"),hexdec("37"));
	    $this->SetTextColor(255);
	    $this->SetDrawColor(128,0,0);
	    $this->SetLineWidth(.3);
	    $this->SetFont('Arial','',8);
	
	    $w = array(15,115, 15, 20,25);
	    for($i=0;$i<count($header);$i++)
	        $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
	    $this->Ln();
	    
	    $this->SetFillColor(224,235,255);
	    $this->SetTextColor(0);
	//    $this->SetFont('');
		
	//    $this->pdf->account_stat(array("Type","Description","Amount","Balance After","Date"),$data);
	    
	    $fill = false;
	    foreach($data as $r)
	    {
	    	$this->Cell($w[0],6,$r[0],'LR',0,'L',$fill);
	        $this->Cell($w[1],6,$r[1],'LR',0,'L',$fill);
	        $this->Cell($w[2],6,number_format($r[2]),'LR',0,'R',$fill);
	        $this->Cell($w[3],6,number_format($r[3]),'LR',0,'R',$fill);
	        $this->Cell($w[4],6,$r[4],'LR',0,'R',$fill);
	        $this->Ln();
	        $fill = !$fill;
	    }
	
	    $this->Cell(array_sum($w),0,'','T');
	}


	function build_table($header,$w,$data,$has_subtotal = 0 )
	{
	    $this->SetFillColor(hexdec("EF"),hexdec("4A"),hexdec("37"));
	    $this->SetTextColor(255);
	    $this->SetDrawColor(128,0,0);
	    $this->SetLineWidth(.3);
	    $this->SetFont('Arial','',8);
		
	   	if($header)
	    	for($i=0;$i<count($header);$i++)
	        	$this->Cell($w[$i],6,$header[$i],1,0,'C',true);
			
	    $this->Ln();
	    
	    $this->SetFillColor(224,235,255);
	    $this->SetTextColor(0);
		
		$ttl_rows = count($data);
	    $fill = false;
	    foreach($data as $j=>$r)
	    {
	    	$i=0;
	    	foreach ($r as $k => $v) 
	    	{
	    		$this->Cell($w[$i],6,$v,'',0,'L',$fill);
				$i++;
			}
	        $this->Ln();
			if($ttl_rows != $j)
	        	$fill = !$fill;
			else 
				$fill = false;
			
	    }
		if(!$has_subtotal)
	    	$this->Cell(array_sum($w),0,'','T');
	}

}