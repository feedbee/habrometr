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

$timeStart = microtime(true);

set_include_path(dirname(__FILE__) . PATH_SEPARATOR . 
	realpath(dirname(__FILE__) . '/lib') . PATH_SEPARATOR . get_include_path());
require('Lpf/Loader.php');

Lpf_Loader::loadClass('Lpf_ErrorHandler');
set_exception_handler(array('Lpf_ErrorHandler', 'exceptionHandler'));
Lpf_Loader::registerAutoload();

if (isset($_GET['user']))
{
	$user = $_GET['user'];
	if (!preg_match('#[a-zA-Z0-9\-_]{1,100}#', $user))
	{
		Habrometr_Informer::showError('User not found', 425, 120);
	}
}
else
{
	$user = 'feedbee';
}

if (isset($_GET['w']))
{
	$width = $_GET['w'];
	if (!ctype_digit($width))
	{
		Habrometr_Informer::showError("Width must be an integer ($width given)", 425, 120);
	}
}
else
{
	$width = 425;
}

if (isset($_GET['h']))
{
	$height = $_GET['h'];
	if (!ctype_digit($height))
	{
		Habrometr_Informer::showError("Height must be an integer ($height given)", $width, 120);
	}
}
else
{
	$height = 120;
}

$className = "Habrometr_Informer_{$width}x{$height}";
$fileName = "./lib/Habrometr/Informer/{$width}x{$height}.php";

if (file_exists($fileName))
{
	require_once($fileName);
}
else
{
	Habrometr_Informer::showError("Informer of size {$width}x{$height} is not exists", $width, $height);
}

$informer = new $className($user);
/* @var $informer Habrometr_Informer */

// Is user's habrometr cached?
if ($informer->existsInCache())
{
	$informer->printFromCache();
	exit;
}

if (!$informer->prepare())
{
	throw new Exception('Informer preparing failed');
}

if (!$informer->build())
{
	throw new Exception('Informer building failed');
}

$informer->printCanvas();
$informer->saveToCache();
$informer->destroyCanvas();

$timeFull = microtime(true) - $timeStart;
file_put_contents(dirname(__FILE__) . '/../generation_log.txt',
	"{$_SERVER['REQUEST_URI']}\t{$timeFull}\t" . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
