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
 * Handler for uncatched exceptions.
 *
 */
class Lpf_ErrorHandler
{
	/**
	 * Handler for uncatched exceptions.
	 *
	 * @param Exception $exception
	 */
	static public function exceptionHandler($exception)
	{
		error_log("Unhandled exception: Code {$exception->getCode()}; Message: {$exception->getMessage()}");
		if (defined('DEBUG') && DEBUG == true)
		{
			die("Unhandled exception: Code {$exception->getCode()}; Message: {$exception->getMessage()}");
		}
		else
		{
			if ($exception->getCode() == 404)
			{
				header("HTTP/1.0 404 Not Found", true, 404);
				die('<p align=center><font size=6>Error 404: Page Not Found</font></p>');
			}
			else
			{
				header("HTTP/1.0 500 Internal Server Error", true, 500);
				die('<p align=center><font size=6>Error 500: Internal Server Error</font></p>');
			}
		}
	}	
}