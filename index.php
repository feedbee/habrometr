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

session_start();

set_include_path(dirname(__FILE__) . PATH_SEPARATOR . 
	realpath(dirname(__FILE__) . '/lib') . PATH_SEPARATOR . get_include_path());
require('Lpf/Loader.php');
define('DEBUG', true);
Lpf_Loader::loadClass('Lpf_ErrorHandler');
set_exception_handler(array('Lpf_ErrorHandler', 'exceptionHandler'));
Lpf_Loader::registerAutoload();

// Routing
if (isset($_GET['action']))
{
	$action = $_GET['action'];
	$actionProcessed = strtolower($action);
	while (false !== ($pos = strpos($actionProcessed, '_')) || false !== ($pos = strpos($actionProcessed, '-')))
	{
		$actionProcessed = substr($actionProcessed, 0, $pos)
			  . strtoupper(substr($actionProcessed, $pos + 1, 1)) . substr($actionProcessed, $pos + 2);
	}
}
else
{
	$action = null;
	$actionProcessed = 'default';
}
if (isset($_GET['controller']))
{
	$controller = $_GET['controller'];
	$controllerProcessed = strtolower($controller);
	while (false !== ($pos = strpos($actionProcessed, '_')) || false !== ($pos = strpos($controllerProcessed, '-')))
	{
		$actionProcessed = substr($actionProcessed, 0, $pos)
			  . strtoupper(substr($actionProcessed, $pos + 1, 1)) . substr($actionProcessed, $pos + 2);
	}
	$controllerProcessed = strtoupper(substr($controller, 0, 1)) . substr($controller, 1);
}
else
{
	$controller = null;
	$controllerProcessed = 'Index';
}

// Dispatch
ob_start();
Lpf_Dispatcher::dispatch(null, $actionProcessed);
$cont = ob_get_flush();

// Cache
if ($cont && $action != 'register')
{
	$cacheTime = array('default' => 30 * 60, 'user_page' => 15 * 60, 'all_users' => 30 * 60, 'get' => 5 * 60);
	$m = new Lpf_Memcache('habrometr');
	$m->set($_SERVER['REQUEST_URI'], $cont . "\r\n<!-- cached version " . date('r') . ' -->', 0, isset($cacheTime[$action]) ? $cacheTime[$action] : 10 * 60);
}
