<?php

setlocale(LC_ALL, 'en_US.UTF-8');
define('debbug',true); // libera acesso POST e mensagens de callback
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
define('root', $protocol . $_SERVER['HTTP_HOST']."/topo/");

if(debbug){
	error_reporting(6143);
}else{
	error_reporting(0);
}

/* Config the router
*/
$router->allowCrossDomain = true; // permite requisições AJAX de outros domínios
//$router->onlyJson = true;     // bloqueia requisições que não esperem Json como resposta. Foco em APIs


/* Declare routers 
*  
*  url 			- url to active this route
*  controller 	- object name to be instaced as controller
*  [ path ]		- path to controller file. Default: global "controller_path"
*  [ method ] 	- internal controller method to be called. Default: index()
*/
$router->add('index', 'homeController');
$router->add('debug', 'homeController', null, 'debug');


/* Global settings */
class Config{
	
	public static $db = array(
		'default' => array(
			'engine'=>'mysql',
			'host'=>'',
			'login'=>'',
			'password'=>'',
			'database'=>'',
			'pre'  =>'',
		)
	);
	
	public static $email = array(
		'default' => array(
			'Host'=>'',
			'SMTPAuth'=>'',
			'SMTPSecure'=>'tsl',
			'Port'=>'',
			'SMTPAuth'=> '',
			'SMTPSecure'=>'',
			'Port'=> '',
			'Username'=>'',
			'Password'=>'',
			'Admin' => '',
			'AddReplyTo'=>array('', ''),
			'SetFrom'=>array('', '')
		)	
	);	
	
	public static $images = array(
		'mini'=>array(50,50),
		'thumb'=>array(100,100),
		'grande'=>array(500,500),
		'gigante'=>array(2500,2500)
	);	
	
};
	
?>