<?php
/**
 *  Habrahabr.ru Habrometr.
 *  Copyright (C) 2009 Leontyev Valera
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

class Lpf_Memcache extends Memcache
{
	private $_namespace = null;
	
	public function __construct($namespace = null, $server = 'localhost', $port = 11211)
	{
		if (!$this->connect($server, $port))
		{
			Log::err('Lpf_Memcache: connection failed');
			throw new Exception('Memcache connection error', 601);
		}
		$this->_namespace = $namespace;
		Log::debug(sprintf('Lpf_Memcache: connection established to %s:%d, namespace `%s`',
			$server, $port, $namespace));
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
		parent::delete($this->_namespace . $key, $timeout);
	}

	public function replace($key, $newValue, $flag = 0, $expire = 0)
	{
		parent::replace($this->_namespace . $key, $newValue, $flag, $expire);
	}

	public function increment($key, $value = 1)
	{
		parent::increment($this->_namespace . $key, $value);
	}

	public function decrement($key, $value = 1)
	{
		parent::decrement($this->_namespace . $key, $value);
	}
}
