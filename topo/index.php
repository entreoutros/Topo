<?php

/* System config */


setlocale(LC_ALL, 'en_US.UTF-8');
define('debbug',true); // libera acesso POST e mensagens de callback


$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
define('root',$protocol . $_SERVER['HTTP_HOST']."/topo/");
define('lib_path','./app/libs/');
define('inc_path','./app/');
define('core_path','./app/core/');
define('view_path','./app/view/');
define('controller_path','./app/controller/');
define('model_path','./app/model/');
define('upload_path','./upload/');

if(debbug){
	error_reporting(6143);
}else{
	error_reporting(0);
}
// ini_set('memory_limit', '2048M'); // uncomment to allow a huge memory use

/*
* configura o sistema de root 
* [url][classe do controtlador][endereco do arquivo do controlador][nome da funcao]
*
*/

include_once(core_path."router.class.php"); // ativa o controle de rotas
$router = new Router();
$pagina = (!isset($_GET['p']))?(isset($_POST['p']) && debbug)?$_POST['p']:'':$_GET['p']; // p = url cortada via htaccess
require_once(inc_path."config.class.php"); // inclui as configurações gerais do sistema

/*
* inclusão do core
*
*/

include_once(core_path."db.class.php");		// ativa o controle do bando de dados
include_once(core_path."dic.class.php");	// ativa dicionarios
include_once(core_path."render.class.php");	// ativa o renderizador de retorno

// ativa o sistema 
$router->allowCrossDomain = true; // permite requisições AJAX de outros domínios
//$router->onlyJson = true;     // bloqueia requisições que não esperem Json como resposta. Foco em APIs

if(!$router->open($pagina)){
	http_response_code( 403 );
	echo 'You cannot pass!';
};

?>