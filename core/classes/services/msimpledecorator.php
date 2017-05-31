<?php


/**
 * MSimpleDecorator - Classe com funções básicas de decorator.
 * Útil quando for necessário adicionar funcionalidade
 * à uma outra classe sem alteração do código da mesma. 
 *
 * Essa classe permite a definição de métodos a serem
 * chamados antes ou depois da chamada de métodos do objeto decorado.
 */
class MSimpleDecorator {
	private $preCommands;
	private $posCommands;
	private $object;

	public function __construct($object) {
		$this->preCommands = array();
		$this->posCommands = array();
		$this->object = $object;
	}

	/**
	 * Adiciona uma callback antes de um ou mais métodos do objeto decorado.
	 * @param \Closure Callback - formato metodo($objDecorado, $parametros)
	 * @param string Nome da função do objeto decorado onde esse método será chamado. Use '*'
	 * para todas as funções.
	 */
	public function addPreCommand(\Closure $command, $when = '*') {
		$this->preCommands[$when] = $command;
	}

	/**
	 * Adiciona uma callback depois de um ou mais métodos do objeto decorado.
	 * @param \Closure Callback - formato metodo($objDecorado, $parametros)
	 * @param string Nome da função do objeto decorado onde esse método será chamado. Use '*'
	 * para todas as funções.
	 */
	public function addPosCommand(\Closure $command, $when = '*') {
		$this->posCommands[$when] = $command;
	}


	/**
	 * Método mágico que faz a delegação da chamada ao objeto decorado
	 */
	public function __call($functionName, $args) {
		if (!is_callable(array($this->object, $functionName))) {
			throw new \Exception("O metodo $functionName nao pode ser executado!");
		}

		$this->doBefore($functionName, $args);

		$result = call_user_func_array(array($this->object, $functionName), $args);

		$this->doAfter($functionName, $args);

		return $result;
	}


	/**
	 * Método mágico para acesso às propriedades públicas do objeto decorado.
	 */
	public function __get($propertyName) {
		return $this->object->$propertyName;
	}

	/**
	 * Método mágico para alteração das propriedades públicas do objeto decorado.
	 */
	public function __set($propertyName, $value) {
		$this->object->$propertyName =  $value;
		return $this;
	}

	/**
	 * Retorna uma lista com os atributos publicos do objeto intenro
	 * @return array
	 */
	public function getAttributesFromInner() {
		return get_object_vars($this->object);
	}


	private function doBefore($functionName, $args) {
		if (array_key_exists($functionName, $this->preCommands)) {
			$this->preCommands[$functionName]($this->object, $args);	
		}

		if (array_key_exists('*', $this->preCommands)) {
			$this->preCommands[$functionName]($this->object, $args);	
		}
	}
	
	private function doAfter($functionName, $args) {
		if (array_key_exists($functionName, $this->posCommands)) {
			$this->posCommands[$functionName]($this->object, $args);	
		}

		if (array_key_exists('*', $this->posCommands)) {
			$this->posCommands[$functionName]($this->object, $args);	
		}
	}
}

?>