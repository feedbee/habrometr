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

require_once('./ErrorHandler.php');
require_once('./Habrometr_Informer.php');
require_once('./Habrometr.php');

set_exception_handler(array('ErrorHandler', 'exceptionHandler'));

if (isset($_GET['user']))
{
	$user = $_GET['user'];
	if (!preg_match('#[a-zA-Z0-9\-_]{1,100}#', $user))
		Habrometr_Informer::showError('User not found');
}
else
{
	$user = 'feedbee';
}

if (isset($_GET['w']))
{
	$width = $_GET['w'];
	if (!ctype_digit($width))
		Habrometr_Informer::showError("Width must be an integer ($width given)", 425, 120);
}
else
{
	$width = 425;
}

if (isset($_GET['h']))
{
	$height = $_GET['h'];
	if (!ctype_digit($height))
		Habrometr_Informer::showError("Height must be an integer ($height given)", $width, 120);
}
else
{
	$height = 120;
}

$className = "Habrometr_Informer_{$width}x{$height}";
if (file_exists("./$className.php"))
{
	require_once("./$className.php");
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