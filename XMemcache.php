<?php

class XMemcache extends Memcache
{
	private $_namespace = null;
	
	public function __construct($namespace = null)
	{
		if (!$this->connect('localhost', 11211))
		{
			throw new Exception('Memcache connection error', 601);
		}
		$this->_namespace = $namespace;
	}

	public function get($key, $flag = null)
	{
		return !is_null($flag) ?
			parent::get($this->_namespace . $key, $flag) :
			parent::get($this->_namespace . $key);
	}

	public function set($key, $newValue, $flag = 0, $expire = 0)
	{
		return parent::set($this->_namespace . $key, $newValue, $flag, $expire);
	}

	public function add($key, $newValue, $flag = 0, $expire = 0)
	{
		return parent::add($this->_namespace . $key, $newValue, $flag, $expire);
	}

	public function delete($key, $timeout = 0)
	{
		return parent::delete($this->_namespace . $key, $timeout);
	}

	public function replace($key, $newValue, $flag = 0, $expire = 0)
	{
		return parent::replace($this->_namespace . $key, $newValue, $flag, $expire);
	}

	public function increment($key, $value = 1)
	{
		return parent::increment($this->_namespace . $key, $value);
	}

	public function decrement($key, $value = 1)
	{
		return parent::decrement($this->_namespace . $key, $value);
	}
}
