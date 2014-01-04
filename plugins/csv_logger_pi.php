<?php
/**
 * class file to handle all csv logging for import and export 
 * 
 * @author Madhan
 *
 */

class csv_logger_pi
{
	
	// data holder 
	private $csv_data=array();
	
	/**
	 * Default constructor , used for data presets 
	 */
	function __construct() {
		$this->csv_data['header'] = array();
		$this->csv_data['data'] = array();
	}
	
	/**
	 * function to prepare csv data headers 
	 * @param unknown_type $arr
	 */
	function head($arr)
	{
		// clean for unwanted blanks entries 
		$arr = array_filter($arr);
		foreach($arr as $a)
			array_push($this->csv_data['header'],trim($a));
	}
	
	/**
	 * function to prepate csv data content 
	 * @param unknown_type $data
	 */
	function push($arr)
	{
		// check if count of headers is matching with count of incoming data or row data
		if(count($arr) == count($this->csv_data['header']))
		{	
			$tmp = array();
			foreach($arr as $a)
				array_push($tmp,trim($a));
			
			array_push($this->csv_data['data'],$tmp);
		}
		
	}
	
	/**
	 * function to build csv data
	 * @return unknown
	 */
	function build_csv($build_data)
	{
		// reformating csv data from data csv_data variable
		if(!empty($build_data))
		{
			ob_start();
			$f=fopen("php://output","w");
			fputcsv($f,$build_data['header']);
			foreach($build_data['data'] as $p)
				fputcsv($f,$p);
			fclose($f);
			$csv_data=ob_get_clean();
			
			return $csv_data;
		}
	}
	
	/**
	 * function to force download csv file
	 */
	function download($filename='')
	{
		$csv=$this->build_csv($this->csv_data);
		
		header('Content-Description: File Transfer');
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename='.($filename.'_'.date("d_m_y_H\h:i\m").".csv"));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . strlen($csv));
		echo $csv;
		exit;
	}
	
	/**
	 * function to save csv data to file  
	 * 
	 * @param unknown_type $file_path
	 * @param unknown_type $file_name
	 * @return string
	 */
	function save($file_path='',$file_name='')
	{
		$csv_data = $this->build_csv();
		
		return realpath($file_path.'/'.$file_name);
	}
	
}