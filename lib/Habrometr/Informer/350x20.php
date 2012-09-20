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
 * Habrometr Informer 350x20
 * 
 * @version 1.0
 * @author feedbee
 * @copyright 2009 feedbee
 *
 */
class Habrometr_Informer_350x20 extends Habrometr_Informer
{
	const WIDTH = 350;
	const HEIGHT = 20;
	
	private $_karma = null;
	private $_habraforce = null;
	private $_extremums = null;
	
	public function __construct($user, Habrometr_Model $h = null)
	{
		parent::__construct($user, self::WIDTH, self::HEIGHT, $h);
	}
	
	public function prepare()
	{
		// Process karma values
		$values = $this->_habrometr->getValues($this->_userId, 20);
		if (!$values)
			$this->_showError('History not found');
		$this->_extremums = $this->_habrometr->getExtremums($this->_userId);
		if (!$this->_extremums)
			$this->_showError('Max or min values calculation error');
		
		$this->_karma = $values['karma_value'];
		$this->_habraforce = $values['habraforce'];
		
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
			array('x' => 0,	'y' => 0),
			array('x' => self::WIDTH - 1, 'y' => 0),
			array('x' => self::WIDTH - 1, 'y' => self::HEIGHT - 1),
			array('x' => 0,	'y' => self::HEIGHT - 1),
			array('x' => 0,	'y' => 0)
		));
		$this->_canvas->drawImage($draw);
		$draw->destroy();
		
		// Draw texts
		$draw = new ImagickDraw();
		$draw->setTextUnderColor($color['bg']);
		$draw->setTextAntialias(true);
		$draw->setFillColor($color['text']);
		$draw->setFont(realpath('stuff/arialbd.ttf'));
		$code = $this->_user;
		$draw->setFontSize(strlen($code) > 8 ? 10 : 11);
		if (strlen($code) > 10)
		{
			$code = substr($code, 0, 9) . '...';
		}
		$draw->annotation(80, 13, $code);
		$textInfo = $this->_canvas->queryFontMetrics($draw, $code, null);
		$nextX = 80 + 9 + $textInfo['textWidth'];
		
		$draw->setFont(realpath('stuff/consola.ttf'));
		$draw->setFontSize(11);
		$draw->setFillColor($color['neutral']);
		$draw->annotation(5, 13, "хаброметр");
		
		$draw->setTextUnderColor($color['karma']);
		$draw->setFillColor($color['bg']);
		$draw->setFontSize(12);
		$draw->setFont(realpath('stuff/consolab.ttf'));
		$draw->annotation($nextX, 13, $text = sprintf('%01.2f', $this->_karma));
		$textInfo = $this->_canvas->queryFontMetrics($draw, $text, null);
		$nextX += $textInfo['textWidth'] + 4;
		
		$draw->setTextUnderColor($color['bg']);
		$draw->setFont(realpath('stuff/arialbd.ttf'));
		$text = sprintf('%01.2f', $this->_extremums['karma_min']) . '/' . sprintf('%01.2f', $this->_extremums['karma_max']);
		$draw->setFontSize(8);
		$draw->setFillColor($color['karma']);
		$draw->annotation($nextX, 13, $text);
		$textInfo = $this->_canvas->queryFontMetrics($draw, $text, null);
		$nextX += $textInfo['textWidth'] + 9;
		
		$draw->setFontSize(12);
		$draw->setFont(realpath('stuff/consolab.ttf'));		
		$draw->setTextUnderColor($color['force']);
		$draw->setFillColor($color['bg']);
		$draw->annotation($nextX, 13, $text = sprintf('%01.2f', $this->_habraforce));
		$textInfo = $this->_canvas->queryFontMetrics($draw, $text, null);
		$nextX += $textInfo['textWidth'] + 4;
		
		$draw->setTextUnderColor($color['bg']);
		$draw->setFont(realpath('stuff/arialbd.ttf'));
		$text = sprintf('%01.2f', $this->_extremums['habraforce_min']) . '/' . sprintf('%01.2f', $this->_extremums['habraforce_max']);
		$draw->setFontSize(8);
		$draw->setFillColor($color['force']);
		$draw->annotation($nextX, 13, $text);
		
		$image = new Imagick('stuff/bg-user2.gif');
		$this->_canvas->compositeImage($image, Imagick::COMPOSITE_COPY, 64, 3, Imagick::CHANNEL_ALL);
		$image->clear();
		$image->destroy();
		
		$this->_canvas->drawImage($draw);
		$draw->destroy();
		
		return true;
	}
}
