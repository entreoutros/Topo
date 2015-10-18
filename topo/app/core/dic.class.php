<?php
class Dic{

	public $defaultLang = "pt";
	public $path = "";
	public $dic;

	public function __construct(){
	}
	
	public function trans($key,$lang = "pt",$dic = ""){
		$lang=($lang!=="" && $lang !== null && $lang !== "undefined")?$lang:$this->defaultLang;
	
		$this->path = inc_path."dic/";
	
		$file = "dic.".$lang.".php";
		$file = ($dic !=="")?$dic.".".$file:$file;

		$this->dic = self::incluir($file);
		
		if($this->dic){
			$retorno = self::pegar($key);
			if($retorno){
				return $retorno;
			}else{
				return $key;
			}
		}else{
			echo($this->dic);
			return $key."::";
		}
	}

	public function incluir($file){
		$url = $this->path.$file;
		if(file_exists($url)){
			if(isset($dic)) unset($dic); 
			include($url);
			if(!isset($dic)) return false; 
			return $dic;
		}else{
			return false;
		}
	}
	
	public function pegar($key){
		if(!isset($this->dic)) return false; 	
		if(array_key_exists($key,$this->dic)){
			return $this->dic[$key];
		}else{
			return false;
		}
	}
	
}