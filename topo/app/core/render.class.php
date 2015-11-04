<?php

class Render{

	private $vars = array();
	private $scripts = array();
	private $style = array();
	
	private $dic;
	
	public function __construct(){
	}
	
	/**
	 * templateExists
	 *
	 * Verifica se um template existe pelo nome do template enviado no parâmetro $template.
	 *
	 * @version 1.0
	 * @param 	$template 	string 		Nome do template.
	 * @return 	boolean		true (arquivo do template existe) / false (arquivo do template não existe)
	 */
	public static function templateExists($template){
		return file_exists(view_path.$template.".tpl.php");
	}
	
	/**
	 * add
	 *
	 * ####
	 *
	 * @version 1.0
	 * @param 	$key 	####
	 * @param 	$value 	####
	 */
	public function add($key,$value){
		$this->vars[$key] = $value;
	}
	
	/**
	 * renderJs
	 *
	 * Renderiza arquivos javascript adicionados à variável $scripts da classe.
	 *
	 * @version 1.0
	 */
	public function renderJs(){
		foreach($this->scripts as $key=>$value){
			if(is_array( $value) ) {
				$content = '';
				$file = false;
				echo "<script";
				foreach ($value as $key => $att) {
					if($key == 'content'){ $content = $att; continue;}
					if($key == 'file'){ $file = $att; continue;}
					echo " $key=\"$att\"";
				}
				echo ">";
				echo $content;
				if($file && file_exists($file)){ include_once( $file ); }else{ print($file); }
				echo "</script>";
			}else{
				echo "<script type='text/javascript' src=\"$value\"></script>";
			}
			
		}
	}
	
	/**
	 * renderCss
	 *
	 * Renderiza arquivos css adicionados à variável $styles da classe.
	 *
	 * @version 1.0
	 */
	public function renderCss(){
		foreach($this->styles as $key=>$value){
			echo "<link href='$value' rel='stylesheet'>";
		}
	}
	
	/**
	 * addModule
	 *
	 * ####
	 *
	 * @version 1.0
	 * @param $module 	Nome do módulo.
	 */
	public function addModule($module){
		$view = $this->vars;
		if(file_exists(view_path."/modules/".$module.".tpl.php")){
			
			include(view_path."/modules/".$module.".tpl.php");
		}else{
			echo "error at module: ".$module;
		}
	}
	
	/**
	 * json
	 *
	 * Renderiza um arquivo json enviado no parâmetro $val caso $print seja true.
	 *
	 * @version 1.0
	 * @param $val 		array 		Array/string que será codificado em JSON.
	 * @param $print 	boolean 	true (imprimi o JSON na tela) / false (retorna o objeto JSON)
	 */
	public function json($val, $print = true){
		header('Content-type:  application/json; charset=utf-8');
		$var = json_encode($val);
		$var = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $var);
		if( $print ){
			print($var);
		}
		return $var;
	}

	/**
	 * render
	 *
	 * ####
	 *
	 * @version 1.0
	 * @param 	$template  				#####
	 * @param 	$header_template  		#####
	 * @param 	$footer_template  		#####
	 * @param 	$header  				#####
	 * @return 	$return 				#####
	 */
	public function render($template, $header_template = true, $footer_template = true, $header = false ){
		ob_start();
			self::view($template, $header_template, $footer_template, $header);
			$return = ob_get_contents();
		ob_end_clean();
		return $return;
	}	
	
	/**
	 * view
	 *
	 * ####
	 *
	 * @version 1.0
	 * @param 	$template  				#####
	 * @param 	$head_template  		#####
	 * @param 	$footer_template  		#####
	 * @param 	$header  				#####
	 * @return 	$return 				#####
	 */
	public function view($template, $head_template = true, $footer_template = true, $header = false){
		global $view;
		global $dic;
		
		$view = $this->vars;
		$scripts = array();
		$styles = array();
		
		$dic = new Dic();
		
		// declare "at render" functions
		if(!function_exists("addModule")){
			function addModule($url){SELF::addModule($url);};
			function root(){echo root;};
			function t($var,$lang = "pt",$diz = ""){global $dic; return $dic->trans($var,$lang,$diz);};
			function _t($var,$lang = "pt",$diz = ""){global $dic; echo $dic->trans($var,$lang,$diz);}; 
		}
		
		// set language
		setlocale(LC_ALL, 'en_US.UTF8');
		// if $HEADER is set replace it
		if( is_string( $header ) ){ header($header); }
		// else, use the default
		if(!headers_sent()){ header('Content-type: text/html; charset=utf-8'); }
		
		// CACHE START
		// Comeca render body
		ob_start(); 
		
		if(file_exists(view_path.$template.".tpl.php")){
			extract($view);
			include(view_path.$template.".tpl.php");
		}else{
			echo "View não localizada ".view_path.$template.".tpl.php";
		}
		
		$body = ob_get_contents();
		
		ob_end_clean(); // Fechar render body
		$this->scripts = $scripts;
		$this->styles = $styles;

		// CACHE ENDs
		
		// render the head
		if( $head_template ){
			$templates = array("",$template."-header.tpl.php","header.tpl.php");
			// if a TPL file is set, include this
			if( is_string( $head_template ) ){ $templates[0] = $head_template; array_push( $templates , "" ); };
			while( $select = next($templates) ){
				if( file_exists(view_path.$select) ){
					include(view_path.$select);
					break;
				}
			}
		}
		
		// render the body
		echo $body;
		
		// render the footer
		if( $head_template && $footer_template){	
			$templates = array("",$template."-footer.tpl.php","footer.tpl.php");
			// if a TPL file is set, include this
			if( is_string( $footer_template ) ){ $templates[0] = $footer_template; array_push( $templates , "" ); };
			while( $select = next($templates) ){
				if( file_exists(view_path.$select)) {
					include(view_path.$select);
					break;
				}
			}
		
		}

	}
}


function replace_unicode_escape_sequence($match) {
    return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}

?>
