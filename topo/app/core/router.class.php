<?php

include_once(core_path."controller.class.php");
include_once(core_path."model.class.php");

class Router{

	public $onlyJson = false;
	public $onlyLocal = false;
	public $allowCrossDomain = false;

	private $controller = array();
	
	public $telasPadroes = array();
	public function __construct(){
		$this->telasPadroes['404'] = '404';
	}
	
	public function add($index,$controller,$caminho = null,$action = 'index'){
		$caminho = ($caminho)?$caminho:controller_path.'/' . $controller . '.class.php';
		$this->controller[$index] = array('controle'=>$controller,'caminho'=>$caminho,'action'=>$action);
	}

	public function open($pagina){
	
		if( $this->allowCrossDomain ) header('Access-Control-Allow-Origin: *');
		if( $this->onlyJson && $_SERVER["HTTP_ACCEPT"] && strpos($_SERVER["HTTP_ACCEPT"], "application/json") === false ){ return false; } 
		if( $this->onlyLocal && ( strpos($_SERVER['HTTP_REFERER'],getenv('HTTP_HOST')) === false ) ){ return false; } 

		$retorno = 200;

		$pagina = (substr($pagina,-1)=="/")?substr($pagina,0,-1):$pagina;
		$pedacos = explode("/",$pagina); 

		$arg = array();
		
		$index = $pagina;
		while(count($pedacos)>=0){
			if(count($pedacos)==0 || (isset($this->controller[$index]) && in_array($this->controller[$index],$this->controller))){
				break;
			}else{
				$arg[] = array_pop($pedacos);
				$index = join("/",$pedacos);
			}
		}
 
		if( count( $arg ) == 0 ){  }

		$index = ($index=="")?'index':$index;
		$args = array('fullPath'=>$pagina,'path'=>$index,'args'=>((count( $arg ) > 0 && $arg[0] !== "" )?array_reverse($arg):array()));

		if(!is_null($this->controller[$index]) && $index!==""){
			if(file_exists($this->controller[$index]['caminho'])){
				include($this->controller[$index]['caminho']);
				$className = $this->controller[$index]['controle'];
				
				if(class_exists ($className)){
				
					$objController = new $className(); // roda a funcao init da classe		
					$action = $this->controller[$index]['action'];
									
					$retorno = $objController->run($action, $args); // roda a funcao run
					//return true;
				}else{
					$retorno = 404;
					//echo "Erro ao ativar o controller: ".$className."<br>";
					//return false;
				}
			}else{
				$retorno = 404;
			}
		}else{
			$retorno = 404;
		}

		if( $retorno ){
			if(Render::templateExists($retorno)){
				if( is_numeric( $retorno ) ){  };

				$render = new Render();
				$render->add('args',$args);
				$render->view($retorno);
				return true;
			}else{
				http_response_code( $retorno );
				return false;
			}
		}else{
			return true;
		}

	}

}

if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL) {

        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;

    }
}

?>