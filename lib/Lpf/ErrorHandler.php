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
		Log::err(sprinf('Lpf_ExceptionHandler: Unhandled exception [%s] `%s`',
			$exception->getCode(), $exception->getMessage()));

		if (Config::DEBUG_MODE)
		{
			die("Unhandled exception: Code {$exception->getCode()}; Message: {$exception->getMessage()}");
		}
		else
		{
			if ($exception->getCode() == 404)
			{
				header("HTTP/1.0 404 Not Found", true, 404);
				die('<h1 style="text-align:center;">Error 404: Page Not Found</h1>');
			}
			else
			{
				header("HTTP/1.0 500 Internal Server Error", true, 500);
				die('<h1 style="text-align:center;">Error 500: Internal Server Error</h1>');
			}
		}
	}	
}