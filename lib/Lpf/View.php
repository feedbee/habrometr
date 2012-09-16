<?php
/**
 *  Habrarabr.ru Habrometr.
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

/**
 * Simple MVC view component implementation.
 */
class Lpf_View
{
	/**
	 * Template variables storage
	 *
	 * @var array
	 */
	private $_variables;

	/**
	 * Helpers storage
	 * 
	 * @var array
	 */
	private $_helpers;
	
	/**
	 * Initialize the object
	 *
	 */
	public function __construct()
	{
		$this->_variables = array();
		$this->_helpers = array();
	}
	
	/**
	 * Getter for template variables
	 *
	 * @param string $name
	 * @return mixed
	 */
	function __get($name)
	{
		if (isset($this->_variables[$name]))
		{
			return $this->_variables[$name]; 
		}
		else
		{
			return null;
		}
	}
	
	/**
	 * Setter for template variables
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	function __set($name, $value)
	{
		$this->_variables[$name] = $value;
	}
	
	public function __call($name, $params)
	{
		$helper = $this->getHelper($name);
		if (is_null($helper))
		{
			throw new Exception('Call to undefined helper "'.$name.'"');
		}
		
		return call_user_func_array($helper, $params);
	}
	
	/**
	 * Renders given template.
	 * 
	 * The script named {$scriptName}.php from ./views/ folder
	 * will be rendered.
	 * 
	 * Returns render result as string if $return == true. Returns null otherwise.
	 *
	 * @param string $scriptName
	 * @param bool $return
	 * @return mixed
	 */
	function render($scriptName, $return = true)
	{
		$filename = './views/' . $scriptName . '.php';
		if (!file_exists($filename))
		{
			throw new Exception("View script {$scriptName} doesn't exists");
		}
		
		if ($return)
		{
			ob_start();
			try
			{
				require_once($filename);
				$c = ob_get_clean();
			}
			catch (Exception $e)
			{
				$c = ob_get_clean() . $e->getMessage();
			}
			return $c;
		}
		else
		{
			require_once($filename);
		}
		return null;
	}
	
	public function addHelper($helperName, $helperObject)
	{
		//$this->_helpers[$helperName] = $helperObject;
	}
	public function removeHelper($helperName)
	{
		//unset($this->_helpers[$helperName]);
	}
	
	public function getHelper($helperName)
	{
		if (isset($this->_helpers[$helperName]))
		{
			return $this->_helpers[$helperName];
		}
		else
		{
			$helperClassName = 'Lpf_Helper_' . ucfirst($helperName);
			if (class_exists($helperClassName))
			{
				$this->_helpers[$helperName] = new $helperClassName();
				return $this->_helpers[$helperName];
			}
		}
		
		return null;
	}
}