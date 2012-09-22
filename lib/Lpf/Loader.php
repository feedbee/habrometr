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

class Lpf_Loader
{
	public static function loadClass($className)
	{
		if (class_exists($className, false) || interface_exists($className, false))
		{
			return;
		}

		$file = str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $className) . '.php';
		include($file);

		if (!class_exists($className, false) && !interface_exists($className, false))
		{
			throw new Exception("File \"$file\" does not exist or class \"$className\" was not found in the file", 701);
		}
	}

	public static function autoload($class)
	{
		try
		{
			@self::loadClass($class);
			return $class;
		} catch (Exception $e) {
			return false;
		}
	}

	public static function registerAutoload()
	{
		spl_autoload_register(array(__CLASS__, 'autoload'));
		Log::debug('Lpf_Loader: autoloading registered');
	}
}