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
 * Simple dispatcher. Implements the simplest logic of loading action by request
 */
class Lpf_Dispatcher
{
	/**
	 * View instance for actions
	 *
	 * @var Lpf_View
	 */
	static private $_viewInstance = null;
	
	/**
	 * Dispatches given action. Executes controllers action and render it's view.
	 *
	 * TODO: 
	 *   1) Add the way to disable standart view rendering.
	 * 
	 * @param string $controller
	 * @param string $action
	 */
	static public function dispatch($controller = null, $action = null)
	{
		if (is_null($controller))
		{
			$controller = 'Index';
		}
		if (is_null($action))
		{
			$action = 'default';
		}
		
		if (!preg_match('#[a-zA-Z0-9\-_]{1,100}#', $action))
		{
			throw new Exception("Action name '{$action}' include bad characters");
		}
		
		require_once("./{$controller}Controller.php"); // only one controller presents at this time
		if (!class_exists($controller . 'Controller'))
		{
			throw new Exception("Controller class {$controller}Controller was not found");
		}
		
		$className = $controller . 'Controller';
		$controllerInstance = new $className();
		if (!method_exists($controllerInstance, $action . 'Action'))
		{
			throw new Exception("Action {$action}Action was not found in controller {$controller}Controller");
		}
		
		$controllerInstance->{$action . 'Action'}();
		
		$body = self::getView()->render($action);
		
		$page = new Lpf_View();
		$page->body = $body;
		$page->title = 'Хаброметр';
		$page->render('page', false);
	}
	
	/**
	 * Get standart view instance.
	 *
	 * @return Lpf_View
	 */
	static public function getView()
	{
		if (is_null(self::$_viewInstance))
		{
			require_once('./View.php');
			self::$_viewInstance = new Lpf_View();
		}
		
		return self::$_viewInstance;
	}
}