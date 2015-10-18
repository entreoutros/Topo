<?php

class Img {

	public $quality = 75; 
	public $arquivo = "";
	public $formatos = array("thumb"=>array(100,100,"adaptar","c","c"));
	
	public function __construct($arquivo, $formatos = array()) {
        /* array(nome=array(h,w,tipo)) */
		$this->arquivo = $arquivo;
		if(count($formatos)>0){
			$this->formatos = $formatos;
		}
    }
	
	public function exec($des = ""){
		if(file_exists($this->arquivo)){
			foreach($this->formatos as $chave=>$tipo){
				$nome = explode(".",basename($this->arquivo));
				$nome = $nome[0];
				$ext = explode(".",basename($this->arquivo));
				$ext = $ext[1];
				$destino = ($des !== "")?$des:dirname($this->arquivo);
				$destino = $destino."/".$nome."-".$chave.".".$ext;
				$altura = (isset($tipo[0]))?$tipo[0]:100;
				$largura = (isset($tipo[1]))?$tipo[1]:100;
				$tipo = (isset($tipo[2]))?$tipo[2]:"adaptar";
				$x = (isset($tipo[3]))?$tipo[3]:"co";
				$y = (isset($tipo[4]))?$tipo[4]:"co";
				
				$this->editImagem($this->arquivo,$destino,$altura,$largura,$tipo,$x,$y,$ext);
			}
		}else{
			return 0;
		}
	}
	
	public function transferUpload($origem,$final){
		move_uploaded_file($origem,$final);
	}
	
	public static function uniqueName($path){
		$split = explode("/",$path);
		$arquivo = array_pop($split);
		$nome = self::criarSlug(current(explode(".",$arquivo)));
		$ext = end(explode(".",$arquivo));
		$caminho = implode("/",$split);
		$final = $caminho."/".$nome.".".$ext;

		$count = 0;
		
		while(file_exists($final)){
			$final = $caminho."/".$nome."-".$count.".".$ext;
			$count += 1; 
		}
	
		return $final;
	}
	
	public function criarSlug($str, $replace=array(), $delimiter='-') {
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
	
	
	/*   */
	
	function editImagem($original,$destino,$altura,$largura,$tipo,$alignX=null,$alignY=null,$type="jpg"){
		// The file
		$filename = $original;
		$destImage = $destino;

		// Content type
		//header('Content-type: image/jpeg');
			
		// Novas medidas
		list($width, $height) = getimagesize($filename);
		$novoHW = $altura/$largura;
		$novoWH = $largura/$altura;
		$antigoHW =$height/$width;
			
		switch($tipo):
			case "adaptar":
				$master = ($height<$width)?((($height*$novoWH)<$width)?"w":"W"):((($width*$novoHW)<$height)?"h":"H");
				$new_height =  $altura;
				$new_width= $largura;
			break;
			case "completar":
				$master = ($height<$width)?((($height*$novoWH)<$width)?"W":"w"):((($width*$novoHW)<$height)?"H":"h");
				$new_height =  $altura;
				$new_width= $largura;
			break;
			case "altura":
				$master = "n";
				$escala = $altura/$height;
				$new_height =  $altura;
				$new_width  =  $width*$escala;
				$crop_altura =  $height;
				$crop_largura= $width;
			break;
			case "largura":
				$master = "n";
				$escala = $largura/$width;
				$new_height =  $height*$escala;
				$new_width  =  $largura;
				$crop_altura =  $height;
				$crop_largura= $width;
			break;
		endswitch;
		
		switch($master):
			case "h":
				$crop_altura   =  round($width * $novoHW);
				$crop_largura =  round($width);
				$escala = $largura/$width;
			break;
			case "H":
				$crop_altura   =  round($height);
				$crop_largura =  round($height * $novoWH);
				$escala = $altura/$height;
			break;
			case "w":
				$crop_altura   =  round($height);
				$crop_largura =  round($height* $novoWH);
				$escala = $altura/$height;
			break;
			case "W":
				$crop_altura   =  round($width*$novoHW);
				$crop_largura =  round($width);	
				$escala = $largura/$width;
			break;
			case "n":
				$escala = 1;//$largura/$width;
			break;
		endswitch;	
		
		// Novo alinhamento
		// X
		$xAling = 0;
		$xOffset = 0;
		switch($alignX){
			case "c": // centro
				$xAling = round(($new_width-($width*$escala))/2);
			break;
			case "co": // centro offset
				$xOffset = round((($width)-$crop_largura)/2);
			break;
			case "d": // direita
				$xAling = round(($new_width-($width*$escala)));
			break;
			case "e": // esquerda
			default:
			break;
		};
		// Y
		$yAling = 0;
		$yOffset = 0;
		switch($alignY){
			case "c": //centro
				$yAling = round(($new_height-($height*$escala))/2);
			break;
			case "co": // centro offset
				$yOffset = round((($height)-$crop_altura)/2);	
			break;
			case "b": // base
				$yAling = round(($new_height-($height*$escala)));
			break;
			case "t":  // topo
			default:
				
			break;
		};
		
		switch($type){
			case "png":
					// Resample
				$image = imagecreatefrompng($filename);
				$image_s = imagecreatetruecolor($new_width, $new_height);
				imagealphablending($image_s, false);
				imagecopyresampled($image_s, $image, $xAling, $yAling, $xOffset, $yOffset, $new_width, $new_height, $crop_largura, $crop_altura);
				imagesavealpha($image_s, true);
			
				// Output
				 // use output buffering to capture outputted image stream

				//imagejpeg($tmp_img);
				return imagepng($image_s, $destImage);
				imagedestroy($image);
				imagedestroy($image_s);
			break;
			default;
				// Resample
				$image = imagecreatefromjpeg($filename);
				$image_s = imagecreatetruecolor($new_width, $new_height);
				imagecopyresampled($image_s, $image, $xAling, $yAling, $xOffset, $yOffset, $new_width, $new_height, $crop_largura, $crop_altura);


				// Output
				 // use output buffering to capture outputted image stream

				//imagejpeg($tmp_img);
				return imagejpeg($image_s, $destImage, $this->quality);
				imagedestroy($image);
				imagedestroy($image_s);
		}
	}
}

?>