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
 * TODO: helpers & menu helper
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
		if (!isset($this->_helpers[$name]))
		{
			throw new Exception('Call to undefined helper "'.$name.'"');
		}
		
		return $this->getHelper($name)->$name($params);
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
			require_once($filename);
			$c = ob_get_clean();
			return $c;
		}
		else
		{
			require_once($filename);
		}
		return null;
	}
	
	public function addHelper($helperName)
	{
		if (isset($this->_helpers[$helperName]))
		{
			return null;
		}
		
		$this->_helpers[$helperName] = null;
		return true;
	}
	
	public function getHelper($helperName)
	{
		if (!isset($this->_helpers[$helperName]))
		{
			$this->addHelper($helperName);
		}
		
		if (!is_object($this->_helpers[$helperName]))
		{
			// someHelper => Lpf_Helper_SomeHelper
			$helperNameReady = strtoupper(substr($helperName, 0, 1)) . substr($helperName, 1);
			$helperNameReady = 'Lpf_Helper_' . $helperNameReady;
			$this->_helpers[$helperName] = new $helperNameReady();
		}
		
		return $this->_helpers[$helperName];
	}
}