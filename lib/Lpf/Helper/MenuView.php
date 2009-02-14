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

class Lpf_Helper_MenuView
{
	/**
	 * Menu elements container
	 * @var array
	 */
	private $_elements = array();
	/**
	 * Active menu element index
	 * @var array
	 */
	private $_activeElement = null;
	
	/**
	 * Builds, renders menu and returns it's HTML-code.
	 * @return string
	 */
	public function menuView($params = null)
	{
		$elements = isset($params[0]) && !is_null($params[0]) ?
			$params[0] : $this->_elements;
		$active = isset($params[1]) && !is_null($params[1]) ?
			$params[1] : $this->_activeElement;
		
		$view = new Lpf_View();
		$view->elements = $elements;
		$view->active = $active;
		return $view->render('menu', true);
	}
	
	/**
	 * Set elements array
	 * @param $elements array
	 * @return null
	 */
	public function setElements(array $elements)
	{
		$this->_elements = $elements;
	}
	
	/**
	 * Returns elements array
	 * @return array
	 */
	public function getElements()
	{
		return $this->_elements;
	}
	
	/**
	 * Sets index of ective element
	 * @param $key mixed
	 * @return null
	 */
	public function setActiveElement($key)
	{
		$this->_activeElement = $key;
	}
	
	/**
	 * Returns current selected element
	 * @return mixed
	 */
	public function getActiveElement()
	{
		return $this->_activeElement;
	}
}
