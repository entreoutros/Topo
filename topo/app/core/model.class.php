<?php

class Model{

	public $defaults = array(
	);

	public $attributes = array();

	/**
	 * __construct
	 *
	 * Carrega os atributos do model de acordo com as chaves do array $defaults de cada model criado.
	 *
	 * @version 1.0
	 * @param 	string 	$model 	Objeto com os atributos do model.
	 */
	public function __construct($model = array()){
		$this->attributes = $this->defaults;

		foreach ($model as $key => $value) {
			$this->{$key} = $value;
		}
	}

	/**
	 * __get
	 *
	 * ####
	 *
	 * @version 1.0
	 * @param 	string 	$name 	####
	 */
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

    /**
	 * __set
	 *
	 * ####
	 *
	 * @version 1.0
	 * @param 	string 	$name 	####
	 * @param 	string 	$value 	####
	 */
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

    /**
	 * update
	 *
	 * Cria um novo atributo no model, salvando um valor ao atributo.
	 *
	 * @version 1.0
	 * @param 	string 	$name 	(array ou string) Nome do novo atributo.
	 * @param 	string 	$value 	Valor do campo.
	 */
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

	/**
	 * toJSON
	 *
	 * Executa a função json_enconde nos atributos do model.
	 *
	 * @version 1.0
	 * @return 	array 	$this->attributes 	Array com os atributos do model em JSON.
	 */
    public function toJSON(){
    	return json_encode( $this->attributes );
    }
  
}

/**
 * ModelCRUD extends Model
 *
 * ####
 *
 * @version 1.0
 */
class ModelCRUD extends Model {

	public $table = '!pre!table_name';
	public $status_flag = false;
	public $key_attribute = 'id';

	/**
	 * create
	 *
	 * Cria um novo registro no banco de dados com os atributos informados no model.
	 *
	 * @version 1.0
	 * @return 	string 	$inserir 	Resposta do SQL de inserção no banco de dados.
	 */
	public function create(){

		// valida campos e garante os defaults
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

	/**
	 * save
	 *
	 * Salva as novas informações em um registro no banco de dados de acordo com os atributos salvos no model.
	 *
	 * @version 1.0
	 * @return 	string 	$feedback 	Resposta do SQL da alteração no banco de dados.
	 */
	public function save(){
		/* valida campos e garante os defaults */
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

	/**
	 * delete
	 *
	 * Deleta um registro no banco de dados OU adiciona false/zero ao status_flag do registro no banco de dados.
	 *
	 * @version 1.0
	 * @return 	string 	$removed 	Resposta do SQL de exclusão no banco de dados.
	 */
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

	/**
	 * load
	 *
	 * Carrega os registros do model no banco de dados de acordo com os parâmetros informados.
	 *
	 * @version 1.0
	 * @param 	string 	$filter 	Array com informações para filtrar os registros
	 * 								- Índice 0 do array - Nome do campo no banco de dados
	 * 								- Índice 1 do array - Valor do campo para comparação
	 * 								- Índice 2 do array - Operador: =, <, >, <>
	 * 								- Índice 3 do array - Agregador: OR, AND, AND NOT, OR NOT
	 * 								- Índice 4 do array - Tipo de campo (string, integer, etc)
	 * @param 	string 	$sort 		String ou array com o(s) campo(s) que serão utilizados para ordenação
	 *								- Exemplo de string: $sort = "campo_a";
	 *								- Exemplo de array: $sort = array("campo_a", "campo_b", "campo_c");
	 * @param 	string 	$slice 		Array com a linha de início da consulta e com a quantidade de registros por página.
	 *								- Índice 0 do array - (integer) Número da linha para início da consulta
	 *								- Índice 1 do array - (integer) Quantidade de linhas que serão selecionadas na consulta
	 * @return 	integer 			Retorna o número de registros encontrados
	 */
	public function load( $filter = false , $sort = false, $slice = false ){

		$matchs = array();

		// caso não exista o parâmetro $filter, cria array com informações default
		if(!$filter){
		 	$filter = array(
		 		$this->table .".". $this->key_attribute,
		 		$this->{$this->key_attribute},
		 		'=', 
		 		false, 
		 		gettype($this->defaults[$this->key_attribute])
		 	);
		// caso o parâmetro $filter informado não seja um array, insere a string no array padrão para ser enviado para a função db::digestWhere()
		}elseif ( gettype($filter) !== "array" ) {
			$filter = array(
		 		$this->table .".". $this->key_attribute,
		 		$filter,
		 		'=', 
		 		false, 
		 		gettype($this->defaults[$this->key_attribute])
		 	);
		}

		// executa a função digestWhere que prepara o sql que será executado
		$filter = db::digestWhere($filter);
		$condition = $filter['sql'];
		$matchs = array_merge($matchs, $filter['args']);

		// executa a função digestOrder que prepara a string com o sql de ordenação
		$order = db::digestOrder($sort);
		// executa a função digestLimit que prepara a string com o limite de seleção de registros para a consulta sql
		$slice = db::digestLimit($slice);
		$limit = $slice['sql'];
		$matchs = array_merge($matchs, $slice['args']);

		$sql = "SELECT * FROM " . $this->table . $condition . $order . $limit;

		// Executa o sql
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

	/**
	 * exists
	 *
	 * ####
	 *
	 * @version 1.0
	 * @return 	integer 	####
	 */
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