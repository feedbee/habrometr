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

// Include paths
set_include_path(__DIR__
	. PATH_SEPARATOR . realpath(__DIR__ . '/lib')
	. PATH_SEPARATOR . realpath(__DIR__ . '/vendor/zf2/library')
	. PATH_SEPARATOR . get_include_path());

// Class autoloading
require('Lpf/Loader.php');

Lpf_Loader::loadClass('Lpf_ErrorHandler');
set_exception_handler(array('Lpf_ErrorHandler', 'exceptionHandler'));
Lpf_Loader::registerAutoload();

// Logger
$logger = new \Zend\Log\Logger;
$writer = new Zend\Log\Writer\Stream(__DIR__ . '/application.log');
$logger->addWriter($writer);
$filter = new Zend\Log\Filter\Priority(Config::LOG_LEVEL);
$writer->addFilter($filter);
Zend\Log\Logger::registerErrorHandler($logger);

class Log
{
	static private $logger;
	static public function getLogger()
	{
		return self::$logger;
	}
	static public function initialize($logger)
	{
		if (!is_null(self::$logger))
		{
			throw new \RuntimeException(__METHOD__ . ' can be called only once per script execution');
		}

		self::$logger = $logger;
	}
}
Log::initialize($logger);