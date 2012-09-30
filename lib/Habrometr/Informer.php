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
 * Habrometer user's informer representation.
 *
 */
class Habrometr_Informer
{
	protected $_width = null;
	protected $_height = null;
	protected $_habrometr = null;
	protected $_user = null;
	protected $_userId = null;
	
	protected $_canvas = null;
	protected $_data = null;
	
	/**
	 * Create new User's Informer object.
	 * 
	 * Throws Exception with code 300 if user was not found.
	 *
	 * @param string $user
	 * @param int $width
	 * @param int $height
	 * @param Habrometr_Model $habrometr
	 */
	public function __construct($user, $width, $height, Habrometr_Model $habrometr = null)
	{
		$this->_width = $width;
		$this->_height = $height;
		$this->_user = $user;
		if (!is_null($habrometr))
		{
			$this->_habrometr = $habrometr;
		}
		else
		{
			$this->_habrometr = Habrometr_Model::getInstance();
		}
		if (!$this->_userId = $this->_habrometr->code2UserId($this->_user))
		{
			$this->_showError("User `$user` not found");
		}
	}
	
	/**
	 * Prepare data for informer.
	 *
	 * Returns True on success
	 *
	 * @return bool
	 */
	public function prepare()
	{
		return true;
	}
	
	/**
	 * Build the image.
	 * Call it only after Habrometr_Informer::prepare() was called.

	 * Returns True on success
	 *
	 * @return bool
	 */
	public function build()
	{
		if (is_null($this->_data))
		{
			throw new Exception('User Habrometr_Informer::prepare() before building', 301);
		}
		
		return true;
	}
	
	/**
	 * Print built image.
	 * Call it only after Habrometr_Informer::build() was called.
	 * 
	 * If informer exists in cache, use Habrometr_Informer::printFromCache().
	 * Returns True on success
	 *
	 * @return bool
	 */
	public function printCanvas()
	{
		if (!($this->_canvas instanceof Imagick))
		{
			throw new Exception('User Habrometr_Informer::build() before printing image', 301);
		}
		
		$this->_canvas->setImageFormat('png');
		header("Content-Type: image/png");
		print $this->_canvas;
		
		return true;
	}
	
	/**
	 * Destroes initialized canvas.
	 * Call it only after Habrometr_Informer::build() was called.
	 *
	 * @return bool
	 */
	public function destroyCanvas()
	{
		if (!($this->_canvas instanceof Imagick))
		{
			throw new Exception('Canvas was not initialized', 302);
		}
		$this->_canvas->clear();
		$this->_canvas->destroy();
		
		return true;
	}
	
	/**
	 * If informer is cached, print it to output.
	 * 
	 * Returns true on succsess, false on no cached image found.
	 *
	 * @return bool
	 */
	public function printFromCache()
	{
		$cacheFilename = "./image_cache/habrometr_{$this->_width}x{$this->_height}_{$this->_user}.png";
		if ($this->existsInCache())
		{
			header("Content-Type: image/png");
			readfile($cacheFilename);
			return true;
		}
		
		return false;
	}
	
	/**
	 * Checks weather cached image exists.
	 *
	 * @return bool
	 */
	public function existsInCache()
	{
		$cacheFilename = "./image_cache/habrometr_{$this->_width}x{$this->_height}_{$this->_user}.png";
		if (file_exists($cacheFilename))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Save builded image to cache.
	 * 
	 * Returns true on success.
	 *
	 * @return bool
	 */
	public function saveToCache()
	{
		if (!($this->_canvas instanceof Imagick))
		{
			throw new Exception('User Habrometr_Informer::build() before saving image', 301);
		}
		
		$cacheFilename = "./image_cache/habrometr_{$this->_width}x{$this->_height}_{$this->_user}.png";
		$this->_canvas->writeImage($cacheFilename);
		@chmod($cacheFilename, 0777);
		
		return true;
	}
	
	/**
	 * Show image with error text.
	 * Uses object context to get image size.
	 *
	 * @param string $text
	 */
	protected function _showError($text)
	{
		if (is_null($this->_width))
		{
			$this->_width = 200;
		}
		if (is_null($this->_height))
		{
			$this->_height = 50;
		}
		
		self::showError($text, $this->_width, $this->_height);
	}
	
	/**
	 * Show image with error text.
	 *
	 * @param string $text
	 * @param int $width
	 * @param int $height
	 */
	static public function showError($text, $width, $height)
	{
		
		
		$canvas = new Imagick();
		$canvas->newImage($width, $height, new ImagickPixel("white"));
		$draw = new ImagickDraw();
		
		$draw->setFillColor(new ImagickPixel("white"));
		$draw->setStrokeColor(new ImagickPixel("black"));
		$draw->polyline(array(
			array('x' => 0, 	'y' => 0),
			array('x' => $width - 1, 'y' => 0),
			array('x' => $width - 1, 'y' => $height - 1),
			array('x' => 0, 	'y' => $height - 1),
			array('x' => 0, 	'y' => 0),
		));
		$canvas->drawImage($draw);
		$draw->destroy();
	
		$draw = new ImagickDraw();
		$draw->setFillColor(new ImagickPixel("black"));
		$draw->setFontSize(12);
		$draw->setTextAlignment(Imagick::ALIGN_CENTER);
		$draw->setTextAntialias(true);
		$draw->setFont(realpath('stuff/consolab.ttf'));
		$draw->annotation($width / 2, $height / 2, $text);
		$canvas->drawImage($draw);
		
		$canvas->setImageFormat('png');
		header("Content-Type: image/png");
		echo $canvas;
		
		$draw->destroy();
		$canvas->clear();
		$canvas->destroy();
		
		exit;
	}
}