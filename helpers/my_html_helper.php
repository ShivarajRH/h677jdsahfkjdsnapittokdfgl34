<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

/**
 * Script
 *
 * Generates a script inclusion of a JavaScript file
 * Based on the CodeIgniters original Link Tag.
 *
 * Author(s): Isern Palaus <ipalaus@ipalaus.es>, Viktor Rutberg <wishie@gmail.com>
 *
 * @access    public
 * @param    mixed    javascript sources or an array
 * @param    string    language
 * @param    string    type
 * @param    boolean    should index_page be added to the javascript path
 * @return    string
 */

if (! function_exists ( 'script_tag' )) {
	function script_tag($src = '', $language = 'javascript', $type = 'text/javascript', $index_page = FALSE) {
		$CI = & get_instance ();
		
		$script = '<script ';
		
		if (is_array ( $src )) {
			foreach ( $src as $v ) {
				if ($k == 'src' and strpos ( $v, '://' ) === FALSE) {
					if ($index_page === TRUE) {
						$script .= ' src="' . $CI->config->site_url ( $v ) . '"';
					} else {
						$script .= ' src="' . $CI->config->slash_item ( 'base_url' ) . $v . '"';
					}
				} else {
					$script .= "$k=\"$v\"";
				}
			}
			
			$script .= ">\n";
		} else {
			if (strpos ( $src, '://' ) !== FALSE) {
				$script .= ' src="' . $src . '" ';
			} elseif ($index_page === TRUE) {
				$script .= ' src="' . $CI->config->site_url ( $src ) . '" ';
			} else {
				$script .= ' src="' . $CI->config->slash_item ( 'base_url' ) . $src . '" ';
			}
			
			$script .= 'language="' . $language . '" type="' . $type . '"';
			
			$script .= '>' . "\n";
		}
		
		$script .= '</script>';
		
		return $script;
	}
}

function format_ts_to_readable($theTime) {
	
	$now = time ();
	$timeLeft = $theTime - $now;
	
	if ($timeLeft > 0) {
		$days = floor ( $timeLeft / 60 / 60 / 24 );
		$hours = $timeLeft / 60 / 60 % 24;
		$mins = $timeLeft / 60 % 60;
		$secs = $timeLeft % 60;
		
		if ($days) {
			$theText = $days . ' Day(s)';
			if ($hours) {
				$theText .= ', ' . $hours . ' Hour(s) ';
			}
		} elseif ($hours) {
			$theText = $hours . ' Hour(s)';
			if ($mins) {
				$theText .= ', ' . $mins . ' Minute(s) ';
			}
		} elseif ($mins) {
			$theText = $mins . ' Minute(s)';
			if ($secs) {
				$theText .= ', ' . $secs . ' Second(s) ';
			}
		} elseif ($secs) {
			$theText = $secs . ' Second(s)';
		}
	} else {
		$theText = false;
	}
	
	return $theText;

}

?>