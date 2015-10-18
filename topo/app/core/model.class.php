<?php

class Model{

	public $defaults = array(
	);

	public $attributes = array();

	public function __construct($model = array()){
		$this->attributes = $this->defaults;

		foreach ($model as $key => $value) {
			$this->{$key} = $value;
		}
	}

	public function __get($name)
    {

    	if(method_exists($this, 'get' . ucfirst ( $name ))){ return call_user_func_array(array($this, 'get' . ucfirst ( $name )),array()); }

    	if($name == 'attributes'){
    		return $this->attributes;
    	}

        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        return null;
    }

  	public function __set($name, $value)
    {

    	if($name == 'attributes' || $name == 'defaults'){
    		$trace = debug_backtrace();
	        trigger_error(
	            'You cannot use "'. $name .'" to Model attribute'. 
	            ' in ' . $trace[0]['file'] .
	            ' on line ' . $trace[0]['line'],
	            E_USER_NOTICE);
	        return null;
    	}

    	if(method_exists($this, 'set' . ucfirst ( $name ))){ return call_user_func_array(array($this, 'set' . ucfirst ( $name )), array($value)); }

        if( array_key_exists($name, $this->attributes) || array_key_exists($name, $this->defaults) ){
        	$this->attributes[$name] = $value;
        }        
    }

	public function update($name, $value = null){
		if( is_array( $name ) ){
			foreach ($name as $key => $value) {
				if(isset($this->attributes[$key])){ 
					$this->{$key} = $value;
				}else{
					$this->attributes[$key] = $value;
				}
			}
		}else{
			if(isset($this->attributes[$name])){ 
				$this->{$name} = $value;
			}else{
				$this->attributes[$name] = $value;
			}
		}		
	}

    public function toJSON(){
    	return json_encode( $this->attributes );
    }
  
}

class ModelCRUD extends Model {

	public $table = '!pre!table_name';
	public $status_flag = false;
	public $key_attribute = 'id';

	public function create(){

		/* valida campos e garante os defaults */
		//
		$input = array_merge( $this->defaults, $this->attributes ); // adiciona termos default caso estejam faltando
		$input = array_intersect_key( $input, $this->defaults ); // retira termos não defaults
		$input = array_filter( $input ); // remove os nulos
	
		$keys = array();
		$values = array();
		$safe = array();
		
		foreach( $input as $key=>$value ){
			$keys[]=$key;
			$values[]=$value;
			$safe[] = array(":" . $key, $value, gettype($this->defaults[$key]));
		}
			
		$sql = 'INSERT INTO ' . $this->table . ' ('.join( ',', $keys ).") VALUES(:".join( ",:", $keys ).")";

		$inserir = db::sql( $sql, $safe );

		if( $this->key_attribute ){
			$this->{$this->key_attribute} = $inserir;
		}
	
		return $inserir;
	}

	public function save(){
		/* valida campos e garante os defaults */
		//
		//$input = array_merge( $this->defaults, $this->attributes ); // adiciona termos default caso estejam faltando
		
		$input = array_intersect_key( $this->attributes, $this->defaults); // retira termos não defaults
		$input = array_filter( $input ); // remove os nulos
	
		$updates = array();
		$safe = array();
		
		$include_string = '';

		foreach( $input as $key=>$value ){
			if( $key !== $this->key_attribute ){ $updates[] = $key . ' = :' . $key; };
			$safe[] = array(":" . $key, $value, gettype($this->defaults[$key]));
		}

		//$safe[] = array(":" . $this->key_attribute, $this->key_attribute, gettype($this->defaults[$this->key_attribute]));
		
		$sql = "UPDATE " . $this->table . " SET " . join( ', ', $updates ) . "  WHERE ". $this->key_attribute . " = :" . $this->key_attribute;

		$feedback = db::sql( $sql, $safe );
		
		return $feedback;		
	}

	public function delete(){
		
		if( $this->status_flag ){
			$sql = "UPDATE " . $this->table . " SET " . $this->status_flag . " = 0 WHERE " . $this->key_attribute . " = :id";
			$removed = db::sql($sql,array(array(':id',(int)$this->{$this->key_attribute},'int')));
		}else{
			$sql = "DELETE FROM " . $this->table . " WHERE ". $this->key_attribute . " = :id";
			$removed = db::sql($sql,array(array(':id', $this->{$this->key_attribute},'int')));
		}	
		
		return $removed;
	}

	public function load( $filter = false , $sort = false, $slice = false ){

		$matchs = array();

		if(!$filter){
		 	$filter = array(
		 		$this->table .".". $this->key_attribute,
		 		$this->{$this->key_attribute},
		 		'=', 
		 		false, 
		 		gettype($this->defaults[$this->key_attribute])
		 	);
		}elseif ( gettype($filter) !== "array" ) {
			$filter = array(
		 		$this->table .".". $this->key_attribute,
		 		$filter,
		 		'=', 
		 		false, 
		 		gettype($this->defaults[$this->key_attribute])
		 	);
		}

		$filter = db::digestWhere($filter);
		$condition = $filter['sql'];
		$matchs = array_merge($matchs, $filter['args']);

		$order = db::digestOrder($sort);
		$slice = db::digestLimit($slice);
		$limit = $slice['sql'];
		$matchs = array_merge($matchs, $slice['args']);

		$sql = "SELECT * FROM " . $this->table . $condition . $order . $limit;

		$matchs = db::sql($sql, $matchs);

		foreach($matchs as $key=>$line){		
			if( method_exists( $this, 'processLoad' ) ){
				$newItem = call_user_method('processLoad', $this, $newItem, $line );
			}
			
			foreach ($line as $p_key => $value) {
				$this->update($p_key, $value);
			}

		}

		return (count($matchs)>0);
	}

	public function exists($filter = false, $active = true){

		$filter = (is_array($filter))?$filter:array( $filter, $this->{$filter} );

		//if(!$value){ return false;}
		$matchs = array();
		$filter = db::digestWhere($filter);
		$condition = $filter['sql'];
		$matchs = array_merge($matchs, $filter['args']);

		if( $active && $this->status_flag ){
			$sql = "SELECT * FROM " . $this->table . " WHERE ".$condition." AND " . $this->status_flag;
		}else{
			$sql = "SELECT * FROM " . $this->table . " WHERE ".$condition;
		}		

		$matchs = db::sql($sql,
				$matchs
			);

		return ( count($matchs) );

	}

}

class ModelCollection {
	public $table = '!pre!table_name';
	public $model = null;

	public $models = array();

	public function __construct($model = false){
		if($model)$this->model = $model;
	}

	public function load( $filters = false , $sort = false, $slice = false ){

		$matchs = array();
		$condition = "";
		
		$filter = db::digestWhere($filters);
		$condition = $filter['sql'];
		$matchs = array_merge($matchs, $filter['args']);

		$order = db::digestOrder($sort);
		$slice = db::digestLimit($slice);
		$limit = $slice['sql'];
		$matchs = array_merge($matchs, $slice['args']);

		//print_r("SELECT * FROM " . $this->table . $condition . $order . $limit);
		$sql = "SELECT * FROM " . $this->table . $condition . $order . $limit;
		//print_r($sql);
		//print_r($matchs);
		$matchs = db::sql($sql, $matchs);
		$ret = array();

		foreach($matchs as $key=>$line){
			$newItem = clone $this->model;
			$newItem->update($line);
			
			if( method_exists( $this, 'processLoad' ) ){
				$newItem = call_user_method('processLoad', $this, $newItem, $line );
			}
			$ret[] = $newItem;

			//$pegar[$key] = (object) $pegar[$key];
		}

		$this->models = $ret;

		return ($ret);
	}

}

?>