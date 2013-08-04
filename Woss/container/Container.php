<?php
namespace moss\container;

use moss\container\ContainerInterface;
use moss\container\ComponentInterface;

/**
 * Dependency Injection Container
 *
 * @package Moss DI Container
 * @author  Michal Wachowski <wachowski.michal@gmail.com>
 */
class Container implements ContainerInterface {

	/** @var array|ContainerInterface[]|Callable[]  */
	private $components = array();

	/** @var array|Object  */
	private $instances = array();

	/**
	 * Registers component definition in container
	 *
	 * @param string $id
	 * @param mixed  $definition
	 * @param bool   $shared
	 *
	 * @return $this
	 * @throws ContainerException
	 */
	public function register($id, $definition, $shared = false) {
		if(is_object($definition) && !$definition instanceof ComponentInterface && !$definition instanceof \Closure) {
			$this->instances[$id] = & $definition;
			return $this;
		}

		$this->components[$id] = $definition;

		if($shared) {
			$this->instances[$id] = null;
		}

		return $this;
	}

	/**
	 * Unregisters component from container
	 *
	 * @param string $id
	 *
	 * @return $this
	 */
	public function unregister($id) {
		if(array_key_exists($id, $this->components)) {
			unset($this->components[$id]);
		}

		if(array_key_exists($id, $this->instances)) {
			unset($this->instances[$id]);
		}

		return $this;
	}

	/**
	 * Returns true if component exists in container
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function exists($id) {
		if(array_key_exists($id, $this->components)) {
			return true;
		}

		if(isset($this->instances[$id])) {
			return true;
		}

		return false;
	}

	/**
	 * Returns true if component is shared
	 *
	 * @param string $id
	 *
	 * @return bool
	 */
	public function isShared($id) {
		return array_key_exists($id, $this->instances);
	}

	/**
	 * Returns component instance or value
	 *
	 * @param string $id
	 *
	 * @return mixed
	 * @throws ContainerException
	 */
	public function &get($id) {
		if(isset($this->instances[$id])) {
			return $this->instances[$id];
		}

		if(!array_key_exists($id, $this->components)) {
			throw new ContainerException(sprintf('Invalid or unknown component identifier "%s"', $id));
		}

		if($this->components[$id] instanceof ComponentInterface) {
			$result = $this->components[$id]->get($this);
		}
		elseif(is_callable($this->components[$id])) {
			$result = $this->components[$id]($this);
		}
		else {
			$result = $this->components[$id];
		}

		if(array_key_exists($id, $this->instances)) {
			$this->instances[$id] = & $result;
		}

		return $result;
	}
}