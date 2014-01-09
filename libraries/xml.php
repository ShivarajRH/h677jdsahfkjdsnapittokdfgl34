<?php
/**
 * Array to xml
 *  
 * @author	Vimal Sudhan <me@vimalsudhan.com>
 * @version	0.1
 * @since	13/07/10
 * @license GPL http://code.vimalsudhan.com/license
 */

/*
 * http://code.vimalsudhan.com
 */



class xml{

    private $dom;

    public function __construct(){
    }

    /**
     * Convert array to xml
     * 
     * @access public
     * @param array xmla
     * @return string xml
     */

    public function array2xml($array, $root){
        $this->dom = new DOMDocument("1.0", "utf-8");
        $this->dom->strictErrorChecking =false;
        $this->dom->formatOutput=true;
        $rnode = $this->dom->createElement($root);
		$rnode = $this->dom->appendchild($rnode);
   		$this->proc($array, $rnode,$root);
   		return $this->dom->saveXML();
    }

    private function proc($ar, &$node,$k){
        foreach ($ar as $key => $val){
            if (is_int($key))
                $nkey=substr($k,0,strlen($k)-1);
            else
                $nkey = $key;
            $n = $this->dom->createElement($nkey);
            if (is_array($val)){
                $this->proc($ar[$key], $n,$nkey);
            }else{
                $t = $this->dom->createTextNode($val);
                $n->appendChild($t);
            }
            $node->appendChild($n);
        }
    }
}

