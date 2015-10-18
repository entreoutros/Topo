<?php
class Controller{
	public function __construct(){

	}

	public function run($action, $args){
		
		if (method_exists ($this , $action)) {
			$response = call_user_func(array($this, $action), $args);
			return ($response == null)?false:$response;
		} else {
			$msg = "Aзгo nгo encontrada. ".$action;
			return '404';
		}
	
		/*
		$render = new Render();
		$render->add('path',$caminho);
		$render->add('args',$args );
		$render->view($caminho);
		*/
	}
	
	public function createSlug($str, $replace=array(), $delimiter='-') {
		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}
      
    	$from = array("Б", "А", "В", "Д", "A", "A", "Г", "Е", "A", "Ж", "C", "C", "C", "C", "З", "D", "Р", "Р", "Й", "И", "E", "К", "Л", "E", "E", "E", "?", "G", "G", "G", "G", "б", "а", "в", "д", "a", "a", "г", "е", "a", "ж", "c", "c", "c", "c", "з", "d", "d", "р", "й", "и", "e", "к", "л", "e", "e", "e", "?", "g", "g", "g", "g", "H", "H", "I", "Н", "М", "I", "О", "П", "I", "I", "?", "J", "K", "L", "L", "N", "N", "С", "N", "У", "Т", "Ф", "Ц", "Х", "O", "Ш", "O", "Њ", "h", "h", "i", "н", "м", "i", "о", "п", "i", "i", "?", "j", "k", "l", "l", "n", "n", "с", "n", "у", "т", "ф", "ц", "х", "o", "ш", "o", "њ", "R", "R", "S", "S", "Љ", "S", "T", "T", "Ю", "Ъ", "Щ", "Ы", "Ь", "U", "U", "U", "U", "U", "U", "W", "Э", "Y", "џ", "Z", "Z", "Ћ", "r", "r", "s", "s", "љ", "s", "Я", "t", "t", "ю", "ъ", "щ", "ы", "ь", "u", "u", "u", "u", "u", "u", "w", "э", "y", "я", "z", "z", "ћ");
    	$to   = array("A", "A", "A", "A", "A", "A", "A", "A", "A", "AE", "C", "C", "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E", "E", "G", "G", "G", "G", "G", "a", "a", "a", "a", "a", "a", "a", "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e", "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H", "I", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L", "N", "N", "N", "N", "O", "O", "O", "O", "O", "O", "O", "O", "CE", "h", "h", "i", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k", "l", "l", "n", "n", "n", "n", "o", "o", "o", "o", "o", "o", "o", "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z", "Z", "Z", "r", "r", "s", "s", "s", "s", "B", "t", "t", "b", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y", "z", "z", "z");
            
    	$clean = str_replace($from, $to, $str); 
		
		//$clean = $str;iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

		return $clean;
	}
	
	public function post($val,$default = ''){
		$return = (isset($_POST[$val]))? $_POST[$val] : $default ;
		return $return;
	}

	public function get($val,$default = ''){
		$return = (isset($_GET[$val]))? $_GET[$val] : $default ;
		return $return;
	}
	
	public function pick($val,$default = ''){

		if(is_array($val) ){
			$return = array();
			foreach ($val as $key => $value) {
				// nominal or simple array ?
				$real_key = is_string($key)? $key : $value ;
				$time_val = $this->pick($real_key, false);
				if($val){ 
					$return[$real_key] = $time_val;
				} else if ( is_array($default) && array_key_exists( $real_key , $default ) ) {
					$return[$real_key] = $default[$real_key];
				} else if ( !is_array($default) ){
					$return[$real_key] = $default;
				} else {
					$return[$real_key] = '';
				}
			}
			return $return;
		}

		$return = ($this->post($val)=='')?$this->get($val):$this->post($val);
		if($return == "")$return = $default;
		return $return;
	}
	
	public function array_utf8_encode_recursive($dat) 
        { if (is_string($dat)) { 
            return utf8_encode($dat); 
          } 
          if (is_object($dat)) { 
            $ovs= get_object_vars($dat); 
            $new=$dat; 
            foreach ($ovs as $k =>$v)    { 
                $new->$k=$this->array_utf8_encode_recursive($new->$k); 
            } 
            return $new; 
          } 
          
          if (!is_array($dat)) return $dat; 
          $ret = array(); 
          foreach($dat as $i=>$d) $ret[$i] = $this->array_utf8_encode_recursive($d); 
          return $ret; 
        } 
	public function array_utf8_decode_recursive($dat) 
        { if (is_string($dat)) { 
        	echo mb_detect_encoding($dat)."<br>";
            return mb_convert_encoding($dat,'iso-8859-1','auto'); 
          } 
          if (is_object($dat)) { 
            $ovs= get_object_vars($dat); 
            $new=$dat; 
            foreach ($ovs as $k =>$v)    { 
                $new->$k=$this->array_utf8_decode_recursive($new->$k); 
            } 
            return $new; 
          } 
          
          if (!is_array($dat)) return $dat; 
          $ret = array(); 
          foreach($dat as $i=>$d) $ret[$i] = $this->array_utf8_decode_recursive($d); 
          return $ret; 
        } 

}


class sessionController extends Controller{

	public function __construct(){

		require_once(model_path."session.class.php");

		global $session;
		global $reports;

		$session = new Session();
		$reports = new Reports( new Report() );

	}

}

?>