<?php

class Lpf_Loader
{
	public static function loadClass($className)
	{
		if (class_exists($className, false) || interface_exists($className, false))
		{
			return;
		}

		// autodiscover the path from the class name
		$file = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
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
	}
}