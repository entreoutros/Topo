<?php
class Random{
	public function __construct(){
		srand((double) microtime()*10000000);
	}

	function random_number($min=0, $max=100){	//return a random number
		if($max > mt_getrandmax()) return false;
		return (mt_rand($min, $max));
	}

	function random_text($len=32, $lower=true, $upper=true, $number=true, $extra=NULL){ //return a random text
		$r = '';
		$source = ($lower?'abcdefghijklmnopqrstuvwxyz':'').
		($upper?'ABCDEFGHIJKLMNOPQRSTUVWXYZ':'').($number?'0123456789':'').$extra;
		$l = strlen($source)-1;
		if($l==-1) return false;
		for($i=0; $i<$len; $i++)
		$r .= $source[mt_rand(0, $l)];
		return $r;
	}

	function possibility($percentage=50){ //randomly return true in certain percent possibility
		return ($percentage >= $this->random_number(1,100));
	}
}
?>