<?php

interface IRepository extends \Countable, \ArrayAccess, \IteratorAggregate {

	public function add(\MBusinessModel $model);

	public function remove(\MBusinessModel $model);

	public function get($ids);

	public function getAll();

	public function listAll();

	public function getEmptyModel();
}

abstract class MRepository implements IRepository {

	private function checkModelClass($model) {
		$modelClass = get_class($model);
		$expectedModelClass = self::getModelClass();
		if ($modelClass != $expectedModelClass) {
			throw new \ERepositoryException("Invalid model class! Expected: {$expectedModelClass}; Received: {$modelClass}");
		}
	}

	private function getModelClass() {
		$repositoryClass = get_called_class();

		$modelClass = preg_replace("/Repository$/", "", $repositoryClass);
		$modelClass = preg_replace('/\\\repositories/', "\\models", $modelClass);

		return $modelClass;
	}

	public function getEmptyModel() {
		$modelClass = self::getModelClass();
		if (!class_exists($modelClass)) {
			throw new \ERepositoryException("Model class non-existent: {$modelClass}!");
		}

		return new $modelClass;
	}

	protected function getCriteria() {
		return $this->getEmptyModel()->getCriteria();
	}

	public function add(\MBusinessModel $model) {
		$this->checkModelClass($model);
		$model->save();
	}

	public function remove(\MBusinessModel $model) {
		$this->checkModelClass($model);
		$model->delete();
	}

	public function get($ids) {
		if (empty($ids)) {
			throw new \ERepositoryException("Invalid id!");
		}

		if (is_array($ids)) {
			return $this->getMany($ids);
		} else {
			return $this->getOne($ids);
		}
	}

	private function getOne($id) {
		$model = $this->getEmptyModel()->getById($id);
		if ($model->isPersistent()) {
			return $model;
		} else {
			return null;
		}
	}

	private function getMany(array $ids) {
		foreach ($ids as $id) {
			$result[$id] = $this->getOne($id);
		}

		return $result;
	}

	public function getAll() {
		return array_combine(
			array_keys($this->getAllIds()),
			array_values($this->listAll()->asCursor()->getObjects())
		);
	}

	public function listAll($filter = '', $attribute = '', $order = '') {
		return $this->getEmptyModel()->listAll($filter = '', $attribute = '', $order = '');
	}

	public function count() {
		return $this->listAll()->asQuery()->count();
	}

	public function getIterator() {
		return new \ArrayIterator($this->getAll());
	}

	private function checkModelId($id, $model) {
		if ($model->getId() != $id) {
			throw new \ERepositoryException("Invalid index!");
		}
	}

	public function offsetExists($id) {
		$model = $this->getOne($id);
		return isset($model);
	}

	public function offsetSet($id, $model) {
		$this->checkModelClass($model);
		if ($id) {
			$this->checkModelId($id, $model);
		}
		$this->add($model);
	}

	public function offsetGet($id) {
		return $this->getOne($id);
	}

	public function offsetUnset($id) {
		$model = $this->getOne($id);
		if (isset($model)) {
			$this->remove($model);
		}
	}

	private function getAllIds() {
		return $this->getEmptyModel()
			->getCriteria()
			->select($this->getEmptyModel()->getPKName())
			->asQuery()
			->chunkResult(0);
	}

}

abstract class MRepositoryDecorator implements IRepository {

	protected $innerRepository;

	public function setInnerRepository(IRepository $innerRepository) {
		$this->innerRepository = $innerRepository;
	}

	public function add(\MBusinessModel $model) {
		$this->innerRepository->add($model);
	}

	public function remove(\MBusinessModel $model) {
		$this->innerRepository->remove($model);
	}

	public function get($ids) {
		return $this->innerRepository->get($ids);
	}

	public function getAll() {
		return $this->innerRepository->getAll();
	}

	public function listAll() {
		return $this->innerRepository->listAll();
	}

	public function getEmptyModel() {
		return $this->innerRepository->getEmptyModel();
	}

	public function count() {
		return $this->innerRepository->count();
	}

	public function getIterator() {
		return $this->innerRepository->getIterator();
	}

	public function offsetExists($id) {
		return $this->innerRepository->offsetExists($id);
	}

	public function offsetSet($id, $model) {
		$this->innerRepository->offsetSet($id, $model);
	}

	public function offsetGet($id) {
		return $this->innerRepository->offsetGet($id);
	}

	public function offsetUnset($id) {
		$this->innerRepository->offsetUnset($id);
	}

}

class MBusinessModelStorage extends \SPLObjectStorage {

	public function getHash(\MBusinessModel $model) {
		return (string) $model->getId();
	}

}

abstract class MCacheRepository extends MRepositoryDecorator {

	private $storage;
	protected static $instances = [];

	final protected function __construct() {
		$this->storage = new MBusinessModelStorage;
		$this->init();
	}

	final public static function getInstance() {
		$calledClass = get_called_class();
		if (!isset(static::$instances[$calledClass])) {
			static::$instances[$calledClass] = new static;
		}

		return static::$instances[$calledClass];
	}

	final private function __wakeup() {}

	final private function __clone() {}

	abstract protected function init();

	private function attach(\MBusinessModel $model) {
		$this->storage->attach($model, $model);
	}

	private function detach(\MBusinessModel $model) {
		$this->storage->detach($model);
	}

	public function add(\MBusinessModel $model) {
		parent::add($model);
		$this->attach($model);
	}

	public function remove(\MBusinessModel $model) {
		parent::remove($model);
		$this->detach($model);
	}

	public function get($ids) {
		if (is_array($ids)) {
			return $this->getMany($ids);
		} else {
			return $this->getOne($ids);
		}
	}

	private function buildModel($id) {
		$model = $this->getEmptyModel();
		$model->set($model->getPKName(), $id);

		return $model;
	}

	private function getOne($id) {
		$model = $this->buildModel($id);
		if ($this->storage->contains($model)) {
			return $this->storage->offsetGet($model);
		} else {
			$model = $this->innerRepository->get($id);
			if (!is_null($model)) {
				$this->attach($model);
			}

			return $model;
		}
	}

	private function getMany(array $ids) {
		foreach ($ids as $id) {
			$result[] = $this->getOne($id);
		}

		return $result;
	}

	public function getAll() {
		$all = parent::getAll();
		foreach ($all as $model) {
			$this->attach($model);
		}

		return $all;
	}

	public function getIterator() {
		return new \ArrayIterator($this->getAll());
	}

	public function offsetExists($id) {
		$model = $this->getOne($id);
		return isset($model);
	}

	public function offsetSet($id, $model) {
		parent::offsetSet($id, $model);
		$this->attach($model);
	}

	public function offsetGet($id) {
		return $this->getOne($id);
	}

	public function offsetUnset($id) {
		$model = $this->getOne($id);
		if (isset($model)) {
			$this->remove($model);
		}
	}

	public function clearCache() {
		unset($this->storage);
		$this->storage = new MBusinessModelStorage;
	}

	public function getCache() {
		foreach ($this->storage as $model) {
			$result[] = $model;
		}

		return $result;
	}

}