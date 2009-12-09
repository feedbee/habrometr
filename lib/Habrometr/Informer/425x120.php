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
 * Habrometr Informer 425x120
 * 
 * @version 1.0
 * @author feedbee
 * @copyright 2009 feedbee
 *
 */
class Habrometr_Informer_425x120 extends Habrometr_Informer
{
	const WIDTH = 425;
	const HEIGHT = 120;
	
	private $_karma = null;
	private $_habraforce = null;
	private $_extremums = null;
	private $_min_karma = null;
	private $_max_karma = null;
	private $_min_habraforce = null;
	private $_max_habraforce = null;
	private $_log_time = null;
	
	public function __construct($user, Habrometr_Model $h = null)
	{
		parent::__construct($user, self::WIDTH, self::HEIGHT, $h);
	}
	
	public function prepare()
	{
		// Process karma values
		$this->_data = $this->_habrometr->getHistory($this->_userId, 20);
		if (!$this->_data)
			$this->_showError('History not found');
		if (count($this->_data) < 2)
			$this->_data[1] = $this->_data[0]; // to get line of one value (other way it's only a point)
		$this->_extremums = $this->_habrometr->getExtremums($this->_userId);
		if (!$this->_extremums)
			$this->_showError('Max or min values calculation error');
	
		$this->_karma = array();
		$this->_min_karma = $this->_max_karma = null;

		foreach($this->_data as $row)
		{
			$this->_karma[] = $row['karma_value'];
			if (is_null($this->_min_karma) || $row['karma_value'] < $this->_min_karma)
				$this->_min_karma = $row['karma_value'];
			if (is_null($this->_max_karma) || $row['karma_value'] > $this->_max_karma)
				$this->_max_karma = $row['karma_value'];

			$this->_habraforce[] = $row['habraforce'];
			if (is_null($this->_min_habraforce) || $row['habraforce'] < $this->_min_habraforce)
				$this->_min_habraforce = $row['habraforce'];
			if (is_null($this->_max_habraforce) || $row['habraforce'] > $this->_max_habraforce)
				$this->_max_habraforce = $row['habraforce'];
		}
		$this->_logTime = $this->_data[0]['log_time'];
		
		return true;
	}
	
	public function build()
	{
		// Prepage image
		$this->_canvas = new Imagick();
		$this->_canvas->newImage(self::WIDTH, self::HEIGHT, new ImagickPixel("white"));
		$color['karmaline'] = new ImagickPixel("rgb(216, 76, 64)");
		$color['forceline'] = new ImagickPixel("rgb(36, 58, 255)");
		$color['text'] = new ImagickPixel("rgb(16, 35, 132)");
		$color['karma'] = new ImagickPixel("rgb(116, 194, 98)");
		$color['force'] = new ImagickPixel("rgb(37, 168, 255)");
		$color['bottom_bg'] = new ImagickPixel("rgb(255, 244, 224)");
		$color['bg'] = new ImagickPixel("white");
		$color['neutral'] = new ImagickPixel("rgb(200, 200, 200)");
		
		// Prepare values for drawing main graph
		define('BOTTOM_PAD', 30);
		define('TOP_PAD', 25);
		define('LEFT_PAD', 43);
		define('RIGHT_PAD', 55);
		define('LINE_OFFSET', 0);

		$graph = array(
			'b' => self::HEIGHT - BOTTOM_PAD,
			't' => TOP_PAD,
			'height' => self::HEIGHT - BOTTOM_PAD - TOP_PAD
		);

		$karma = array_reverse($this->_karma);
		$habraforce = array_reverse($this->_habraforce);

		// Drawing habraforce graph (first because might be under karma line)
		$draw = new ImagickDraw();
		$draw->setStrokeColor($color['forceline']);
		$draw->setFillColor($color['bg']);
		$draw->setFillOpacity (0);
		$draw->setStrokeOpacity(0.7);
		$draw->setStrokeAntialias(true);
		$draw->setStrokeWidth(2);

		$force_cords = $this->generateGraphCords($graph, $habraforce, $this->_min_habraforce, $this->_max_habraforce, true);
		$draw->polyline($force_cords);
		$this->_canvas->drawImage($draw);
		$draw->destroy();
		
		// Drawing karma graph
		$draw = new ImagickDraw();
		$draw->setStrokeColor($color['karmaline']);
		$draw->setFillColor($color['bg']);
		$draw->setFillOpacity (0);
		$draw->setStrokeOpacity(0.7);
		$draw->setStrokeAntialias(true);
		$draw->setStrokeWidth(2);
		
		$karma_cords = $this->generateGraphCords($graph, $karma, $this->_min_karma, $this->_max_karma, false);
		$draw->polyline($karma_cords);
		$this->_canvas->drawImage($draw);
		$draw->destroy();

		// Draw bottom bg
		define('TOP_SPACER', 10);
		$draw = new ImagickDraw();
		$draw->setFillColor($color['bottom_bg']);
		$draw->polygon(array(
			array('x' => 0, 		  'y' => $graph['b'] + TOP_SPACER),
			array('x' => self::WIDTH, 'y' => $graph['b'] + TOP_SPACER),
			array('x' => self::WIDTH, 'y' => self::HEIGHT),
			array('x' => 0, 		  'y' => self::HEIGHT)
		));
		$this->_canvas->drawImage($draw);
		$draw->destroy();
		
		// Draw texts
		$draw = new ImagickDraw();
		$draw->setTextAntialias(true);
		$draw->setFontSize(12);
		$draw->setFillColor($color['text']);
		$draw->setFont(realpath('stuff/arial.ttf'));
		$draw->annotation(2, 13, 'Хаброметр юзера');
		
		$draw->setFont(realpath('stuff/arialbd.ttf'));
		$code = $this->_user;
		if (strlen($code) > 25)
			$code = substr($code, 0, 22) . '...';
		$draw->annotation(125, 13, $code);
		
		$image = new Imagick('stuff/bg-user2.gif');
		$this->_canvas->compositeImage($image, Imagick::COMPOSITE_COPY, 109, 2, Imagick::CHANNEL_ALL);
		$image->clear();
		$image->destroy();
		
		$this->_canvas->drawImage($draw);
		$draw->destroy();
	
		// Print min/max karma left from graph, min/max habraforce right frm graph and log time
		$draw = new ImagickDraw();

		// Min/max karma
		$draw->setFontSize(10);
		$draw->setFillColor($color['karma']);
		$draw->setFont(realpath('stuff/consola.ttf'));
		$draw->annotation(2, $graph['t'] + 5, sprintf('%01.2f', $this->_max_karma));
		$draw->annotation(2, $graph['b'] + 2, sprintf('%01.2f', $this->_min_karma));
		
		// Min/max habraforce
		$draw->setFontSize(10);
		$draw->setFillColor($color['force']);
		$draw->setFont(realpath('stuff/consola.ttf'));
		$draw->annotation(self::WIDTH - RIGHT_PAD + 5, $graph['t'] + 5, sprintf('%01.2f', $this->_max_habraforce));
		$draw->annotation(self::WIDTH - RIGHT_PAD + 5, $graph['b'] + 2, sprintf('%01.2f', $this->_min_habraforce));
		
		// Log time
		$draw->setFontSize(10);
		$draw->setFillColor($color['neutral']);
		$draw->setFont(realpath('stuff/consola.ttf'));
		$t = split('-| |:', $this->_logTime);
		$this->_canvas->annotateImage($draw, self::WIDTH - 3, $graph['b'] + 2, -90,
			$t[2] . '.' . $t[1] . '.' . substr($t[0], -2) . ' ' . $t[3] . ':' . $t[4]);
		
		$this->_canvas->drawImage($draw);
		$draw->destroy();

		$draw = new ImagickDraw();
		$draw->setFillColor($color['karma']);
		$text = 'min/max: ' . sprintf('%01.2f', $this->_extremums['karma_min']) . ' / ' . sprintf('%01.2f', $this->_extremums['karma_max']);
		$draw->setFontSize(12);
		$draw->setFont(realpath('stuff/consola.ttf'));
		$draw->setFillColor($color['bg']);
		$draw->annotation(3, $graph['b'] + 25, $text);
		$draw->setFillColor($color['karma']);
		$draw->annotation(2, $graph['b'] + 24, $text);
		
		$this->_canvas->drawImage($draw);
		$draw->destroy();

		$draw = new ImagickDraw();
		$draw->setTextAlignment(Imagick::ALIGN_RIGHT);
		$text = 'min/max: ' . sprintf('%01.2f', $this->_extremums['habraforce_min']) . ' / ' . sprintf('%01.2f', $this->_extremums['habraforce_max']);
		$draw->setFontSize(12);
		$draw->setFont(realpath('stuff/consola.ttf'));
		$draw->setFillColor($color['bg']);
		$draw->annotation(self::WIDTH - 3, $graph['b'] + 25, $text);
		$draw->setFillColor($color['force']);
		$draw->annotation(self::WIDTH - 2, $graph['b'] + 24, $text);
		
		$this->_canvas->drawImage($draw);
		$draw->destroy();

		// Karma and force placeholders on right top corner
		$draw = new ImagickDraw();
		$draw->setTextAlignment(Imagick::ALIGN_RIGHT);
		$draw->setTextUnderColor($color['karma']);
		$draw->setFillColor($color['bg']);
		$draw->setFontSize(14);
		$draw->setFont(realpath('stuff/consolab.ttf'));
		$textInfo = $this->_canvas->queryFontMetrics($draw, sprintf('%01.2f', $habraforce[count($habraforce) - 1]), null);
		$draw->annotation(self::WIDTH - 15 + 1 - $textInfo['textWidth'] - 10,  13, sprintf('%01.2f', $karma[count($karma) - 1]));
		$draw->setTextUnderColor($color['force']);
		$draw->setFillColor($color['bg']);
		$draw->annotation(self::WIDTH - 15 + 1, 13, sprintf('%01.2f', $habraforce[count($habraforce) - 1]));
		
		$this->_canvas->drawImage($draw);
		$draw->destroy();
		
		$image = new Imagick('stuff/logo_mini.png');
		$this->_canvas->compositeImage($image, Imagick::COMPOSITE_COPY, 3, 47, Imagick::CHANNEL_ALL);
		$image->clear();
		$image->destroy();
		
		return true;
	}

	protected function generateGraphCords(array $graph, array $data, $dataMin, $dataMax, $lineOffset = false)
	{
		$dataHeight = $dataMax - $dataMin; 
		if ($dataHeight != 0)
			$coefficient = $graph['height'] / $dataHeight;
		else
			$coefficient = 1;
			
		$segmentWidth = (self::WIDTH - LEFT_PAD - RIGHT_PAD) / (count($data) - 1);
		$lastX = LEFT_PAD;
		$lastY = $data[0];
		$cords = array();
 
		foreach($data as $y)
		{
			if ($dataHeight != 0)
				$cords[] = array('x' => (float)$lastX, 'y' => $graph['b'] - round($y - $dataMin) * $coefficient - ($lineOffset ? LINE_OFFSET : 0));
			else
				$cords[] = array('x' => (float)$lastX, 'y' => $graph['b'] - round($graph['height'] / 2)  - ($lineOffset ? LINE_OFFSET : 0));

			$lastX += $segmentWidth;
		}

		return $cords;
	}
}
