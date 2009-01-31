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
 * Habrometr Informer 88x15
 * 
 * @version 1.0
 * @author feedbee
 * @copyright 2009 feedbee
 *
 */
class Habrometr_Informer_88x15 extends Habrometr_Informer
{
	const WIDTH = 88;
	const HEIGHT = 15;
	
	private $_karma = null;
	private $_habraforce = null;
	
	public function __construct($user, Habrometr_Model $h = null)
	{
		parent::__construct($user, self::WIDTH, self::HEIGHT, $h);
	}
	
	public function prepare()
	{
		// Process karma values
		$this->_data = $this->_habrometr->getValues($this->_userId, 20);
		if (!$this->_data)
			$this->_showError('User values not found');
		
		$this->_karma = $this->_data['karma_value'];
		$this->_habraforce = $this->_data['habraforce'];
		
		return true;
	}
	
	public function build()
	{
		// Prepage image
		$this->_canvas = new Imagick();
		$this->_canvas->newImage(self::WIDTH, self::HEIGHT, new ImagickPixel("white"));
		$color['line'] = new ImagickPixel("rgb(216, 76, 64)");
		$color['text'] = new ImagickPixel("rgb(16, 35, 132)");
		$color['karma'] = new ImagickPixel("rgb(116, 194, 98)");
		$color['force'] = new ImagickPixel("rgb(37, 168, 255)");
		$color['bottom_bg'] = new ImagickPixel("rgb(255, 244, 224)");
		$color['bg'] = new ImagickPixel("white");
		$color['neutral'] = new ImagickPixel("rgb(200, 200, 200)");
		$color['habr'] = new ImagickPixel("rgb(83, 121, 139)");
		
		// Prepare canvas for drawing main graph
		$draw = new ImagickDraw();
		$draw->setStrokeAntialias(true);
		$draw->setStrokeWidth(2);
		
		// Prepare values for drawing main graph
		define('PADDING', 10);
		
		// Draw bottom bg
		define('TOP_SPACER', 10);
		$draw = new ImagickDraw();
		$draw->setFillColor($color['bg']);
		$draw->setStrokeColor($color['habr']);
		$draw->polyline(array(
			array('x' => 0,				  'y' => 0),
			array('x' => self::WIDTH - 1, 'y' => 0),
			array('x' => self::WIDTH - 1, 'y' => self::HEIGHT - 1),
			array('x' => 0,				  'y' => self::HEIGHT - 1),
			array('x' => 0,				  'y' => 0)
		));
		$this->_canvas->drawImage($draw);
		$draw->destroy();
		
		// Draw texts
		$draw = new ImagickDraw();
		$draw->setTextAlignment(Imagick::ALIGN_CENTER);
		$draw->setTextAntialias(true);
		
		$draw->setFillColor($color['karma']);
		$draw->polyline(array(
			array('x' => 1, 			  'y' => 1),
			array('x' => self::WIDTH / 2, 'y' => 1),
			array('x' => self::WIDTH / 2, 'y' => self::HEIGHT - 2),
			array('x' => 1,				  'y' => self::HEIGHT - 2),
			array('x' => 1,				  'y' => 1)
		));
		$draw->setFillColor($color['bg']);
		$draw->setFontSize(11);
		$draw->setFont(realpath('stuff/consolab.ttf'));
		$draw->annotation(self::WIDTH / 4, 11, sprintf('%01.2f', $this->_karma));
		
		$draw->setFillColor($color['force']);
		$draw->polyline(array(
			array('x' => self::WIDTH / 2 + 1,	'y' => 1),
			array('x' => self::WIDTH - 2, 		'y' => 1),
			array('x' => self::WIDTH - 2, 		'y' => self::HEIGHT - 2),
			array('x' => self::WIDTH / 2 + 1,	'y' => self::HEIGHT - 2),
			array('x' => self::WIDTH / 2 + 1,	'y' => 1)
		));
		$draw->setFillColor($color['bg']);
		$draw->annotation(self::WIDTH / 4 * 3, 11, sprintf('%01.2f', $this->_habraforce));
		
		$this->_canvas->drawImage($draw);
		$draw->destroy();
		
		return true;
	}
}
