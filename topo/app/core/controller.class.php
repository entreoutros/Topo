<?php
class Controller {

	public function __construct(){

	}

	/**
	 * run
	 *
	 * #####
	 *
	 * @version 1.0
	 * @param 	string 	$action 	#####
	 * @param 	string 	$args 		#####
	 * @return 	 					#####
	 */
	public function run($action, $args){
		
		if (method_exists ($this , $action)) {
			$response = call_user_func(array($this, $action), $args);
			return ($response == null)?false:$response;
		} else {
			$msg = "Ação não encontrada. ".$action;
			return '404';
		}
	
		/*
		$render = new Render();
		$render->add('path',$caminho);
		$render->add('args',$args );
		$render->view($caminho);
		*/
	}
	
	/**
	 * createSlug
	 *
	 * Cria um slug com a string passada removendo acentos e demais caracteres especiais.
	 *
	 * @version 1.0
	 * @param 	string 	$str 		String com o texto para gerar o slug.
	 * @param 	string 	$replace 	Caso queira que algum caracter seja removido.
	 * @param 	string 	$delimiter 	Caracter que será utilizado para unir as palavras no slug.
	 * @return 	string 	$clean 		Slug gerado pela função.
	 */
	public function createSlug($str, $replace=array(), $delimiter='-') {
		if( !empty($replace) ) {
			$str = str_replace((array)$replace, ' ', $str);
		}
      
    	$from = array("Á", "À", "Â", "Ä", "A", "A", "Ã", "Å", "A", "Æ", "C", "C", "C", "C", "Ç", "D", "Ð", "Ð", "É", "È", "E", "Ê", "Ë", "E", "E", "E", "?", "G", "G", "G", "G", "á", "à", "â", "ä", "a", "a", "ã", "å", "a", "æ", "c", "c", "c", "c", "ç", "d", "d", "ð", "é", "è", "e", "ê", "ë", "e", "e", "e", "?", "g", "g", "g", "g", "H", "H", "I", "Í", "Ì", "I", "Î", "Ï", "I", "I", "?", "J", "K", "L", "L", "N", "N", "Ñ", "N", "Ó", "Ò", "Ô", "Ö", "Õ", "O", "Ø", "O", "Œ", "h", "h", "i", "í", "ì", "i", "î", "ï", "i", "i", "?", "j", "k", "l", "l", "n", "n", "ñ", "n", "ó", "ò", "ô", "ö", "õ", "o", "ø", "o", "œ", "R", "R", "S", "S", "Š", "S", "T", "T", "Þ", "Ú", "Ù", "Û", "Ü", "U", "U", "U", "U", "U", "U", "W", "Ý", "Y", "Ÿ", "Z", "Z", "Ž", "r", "r", "s", "s", "š", "s", "ß", "t", "t", "þ", "ú", "ù", "û", "ü", "u", "u", "u", "u", "u", "u", "w", "ý", "y", "ÿ", "z", "z", "ž");
    	$to   = array("A", "A", "A", "A", "A", "A", "A", "A", "A", "AE", "C", "C", "C", "C", "C", "D", "D", "D", "E", "E", "E", "E", "E", "E", "E", "E", "G", "G", "G", "G", "G", "a", "a", "a", "a", "a", "a", "a", "a", "a", "ae", "c", "c", "c", "c", "c", "d", "d", "d", "e", "e", "e", "e", "e", "e", "e", "e", "g", "g", "g", "g", "g", "H", "H", "I", "I", "I", "I", "I", "I", "I", "I", "IJ", "J", "K", "L", "L", "N", "N", "N", "N", "O", "O", "O", "O", "O", "O", "O", "O", "CE", "h", "h", "i", "i", "i", "i", "i", "i", "i", "i", "ij", "j", "k", "l", "l", "n", "n", "n", "n", "o", "o", "o", "o", "o", "o", "o", "o", "o", "R", "R", "S", "S", "S", "S", "T", "T", "T", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "W", "Y", "Y", "Y", "Z", "Z", "Z", "r", "r", "s", "s", "s", "s", "B", "t", "t", "b", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "w", "y", "y", "y", "z", "z", "z");
            
    	$clean = str_replace($from, $to, $str); 
		
		//$clean = $str;iconv('UTF-8', 'ASCII//TRANSLIT', $str);
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

		return $clean;
	}
	
	/**
	 * post
	 *
	 * Verifica se o índice existe no $_POST, caso não exista retorna o valor padrão.
	 *
	 * @version 1.0
	 * @param 	string 	$val 		Nome do índice que será verificado no array $_POST.
	 * @param 	string 	$default 	Texto padrão caso não exista o índice no array.
	 * @return 	string 	$clean 		Valor do post ou o valor definido como padrão.
	 */
	public function post($val, $default = ''){
		$return = (isset($_POST[$val]))? $_POST[$val] : $default ;
		return $return;
	}

	/**
	 * get
	 *
	 * Verifica se o índice existe no $_GET, caso não exista retorna o valor padrão.
	 *
	 * @version 1.0
	 * @param 	string 	$val 		Nome do índice que será verificado no array $_GET.
	 * @param 	string 	$default 	Texto padrão caso não exista o índice no array.
	 * @return 	string 	$clean 		Valor do post ou o valor definido como padrão.
	 */
	public function get($val, $default = ''){
		$return = (isset($_GET[$val]))? $_GET[$val] : $default ;
		return $return;
	}
	
	/**
	 * pick
	 *
	 * #####
	 *
	 * @version 1.0
	 * @param 	#####
	 * @param 	#####
	 * @return 	#####
	 */
	public function pick($val, $default = ''){

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
	
	/**
	 * array_utf8_encode_recursive
	 *
	 * Codifica um array ou uma string para UTF-8 e retorna uma versão codificada.
	 *
	 * @version 1.0
	 * @param 	string/array 	$dat 	String ou array que será codificado em UTF-8.
	 * @return 	string/array	$ret 	Valor da string ou array codificado em UTF-8.
	 */
	public function array_utf8_encode_recursive($dat){ 

		if (is_string($dat)) { 
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

    /**
	 * array_utf8_decode_recursive
	 *
	 * Converte uma string ou array com carateres ISO-8859-1 codificada com UTF-8 para single-byte ISO-8859-1.
	 *
	 * @version 1.0
	 * @param 	string/array 	$dat 	String ou array que será codificado em single-byte ISO-8859-1.
	 * @return 	string/array	$ret 	Valor da string ou array codificado em single-byte ISO-8859-1.
	 */
	public function array_utf8_decode_recursive($dat){ 

		if (is_string($dat)) { 
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

	/**
	 * __construct
	 *
	 * #####
	 *
	 * @version 1.0
	 * @param 	#####
	 * @return 	#####
	 */
	public function __construct(){

		require_once(model_path."session.class.php");

		global $session;
		global $reports;

		$session = new Session();
		$reports = new Reports( new Report() );

	}

}

?>