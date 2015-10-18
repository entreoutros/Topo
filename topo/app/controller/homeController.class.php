<?php

class homeController extends Controller{

	public function index($args){
		$render = new Render();
		$render->add('path',$args['path']);
		$render->add('fullPath',$args['fullPath']);
		$render->add('args',$args['args']);
	
		switch (true) {
			case count($args['args']) == 0:
			case $args['args'][0] == 'pages':
			case $args['args'][0] == 'page':
				$render->view('index');
				break;
			
			default:
				$render->view('404',true,true,"HTTP/1.0 404 Not Found");
				break;
		}	
	}	

	public function debug($args){
		$render = new Render();

		$render->json(
			array(
				'internal_args'=>$args,
				'post'=>$this->post('debug', 'none'),	/* POST request method */
				'post'=>$this->get('debug', 'none'),	/* GET request method */
				'post'=>$this->pick('debug', 'none'),	/* BOTH request method */
				)
			);

	}
}

?>