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
 * This config script defines secret values (username, pass, db name)
 * for DB connection.
 *
 * All the values was excluded from svn repository. I've put them into
 * simple php-file ./config.local.php as constants. If you like to define your
 * values right here, you can do it. I this case delete first three
 * lines of code in this file and replace constants in bottom with real
 * values. But, be carefully with svn updates in this way.
 */

if (!file_exists(dirname(__FILE__) . '/config.local.php'))
{
	die('Read some comments in Config.php!');
}

require(dirname(__FILE__) . '/config.local.php');

class Config
{
	const DEBUG_MODE = DEBUG_MODE;
	const LOG_LEVEL = LOG_LEVEL;

	const DB_USER = DB_USER;
	const DB_PASS = DB_PASS;
	const DB_NAME = DB_NAME;

	const SERVICE_URL = SERVICE_URL;
	const SERVICE_NAME = 'Хаброметр';
}
