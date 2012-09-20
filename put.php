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

require __DIR__ . '/bootstrap.php';

set_time_limit(0);

if (isset($_SERVER['REQUEST_METHOD']))
{
	header('Forbidden', true, 403);
	die('Forbidden');
}

isset($argv)
	&& isset($argv[1])
	&& ($argv[1] == '-q' || $argv[1] == '--quet')
	&& $quet = true
	or $quet = false;

!$quet && print "=== Update process started ===\r\n";

$h = Habrometr_Model::getInstance();
$users = $h->getUserList();
$errorCounter = 0;
foreach ($users as $key => $user)
{
	try
	{
		$h->putValues($user);
		system('rm -f ' . dirname(__FILE__) . '/image_cache/habrometr_*_'
			. escapeshellcmd($user['user_code']) . '.png');
	}
	catch (Exception $e)
	{
		$errorCounter++;
		!$quet && print "Error updating user #$key";
		if ($errorCounter > 30)
			break;
	}
	!$quet && print "." . (($key+1) % 50 == 0 ? "\r\n" : '');
}

!$quet && print "\r\nUsers updated: " . ($key+1) . "\r\n";
!$quet && print "=== Update process finished ===\r\n";
