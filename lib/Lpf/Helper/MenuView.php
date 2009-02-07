<?php

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
		$elements = is_null($params[0]) ? $this->_elements : $params[0];
		$active = is_null($params[1]) ? $this->_activeElement : $params[1];
		
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
