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

Log::debug('pull.php: started');
set_time_limit(0);

$errorConsoleWriter = new Zend\Log\Writer\Stream('php://stderr');
$logger->addWriter($errorConsoleWriter);
$filter = new Zend\Log\Filter\Priority(Zend\Log\Logger::NOTICE);
$errorConsoleWriter->addFilter($filter);

if (isset($_SERVER['REQUEST_METHOD']))
{
	Log::warn(sprintf('pull.php: web access attempt (%s)', $_SERVER['REQUEST_URI']));
	header('Forbidden', true, 403);
	die('Forbidden');
}

isset($argv)
	&& isset($argv[1])
	&& ($argv[1] == '-q' || $argv[1] == '--quiet')
	&& $quiet = true
	or $quiet = false;
Log::info(sprintf('pull.php: verbosity %s', $quiet ? ' disabled' : 'enabled'));

!$quiet && print "=== Update process started ===\r\n";
Log::info('pull.php: update process started ');

$h = Habrometr_Model::getInstance();
$list = $h->getUserList();
$users = $list['list'];
$errorCounter = 0;
foreach ($users as $key => $user)
{
	try
	{
		$h->pullValues($user);
		system('rm -f ' . __DIR__ . '/image_cache/habrometr_*_'
			. escapeshellcmd($user['user_code']) . '.png');
		$errorCounter = 0;
		Log::debug(sprintf('pull.php: user [%s] %s updated', $key, $user['user_code']));
	}
	catch (Exception $e)
	{
		$errorCounter++;
		!$quiet && print "Error updating user [$key] {$user['user_code']}";
		Log::err(sprintf('pull.php: error updating user [%s] %s)', $key, $user['user_code']));
		if ($errorCounter > 20)
		{
			Log::crit('pull.php: consecutive errors counter exceed 20 - break update process)');
			break;
		}
	}
	!$quiet && print "." . (($key+1) % 50 == 0 ? "\r\n" : '');
}

$updated = $key + 1;
!$quiet && print "\r\nUsers updated: {$updated}\r\n";
!$quiet && print "=== Update process finished ===\r\n";
Log::info(sprintf('pull.php: update process finished (users updated: %d) ', $updated));
