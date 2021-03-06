<?php

/*
	Простенький инструментарий для рисования всяких графиков.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

define('pi', 3.1428);

class CChart
{
	/*
		Обычный график с несколькими линиями. Подробнее о параметрах см. в теле
		функции.
		@param  array $_params параметры
		@param  array $_data   данные графиков в виде массивов
		@return resource image
	*/
	public static function multiline_graph($_params, $_data)
	{
		/*
			$_params['width']   ширина картинки
			$_params['height']  высота картинки
			$_params['colors'] = array(
				'f0f0f0',
				'dd0034',
				...
			)                     цвета графиков по порядку
			$_params['bgcolor']   цвет фона
			$_params['xtick']     подписанные деления на оси абсцисс
			$_params['grid']      рисовать или нет сетку
			$_params['gridcolor'] цвет сетки, если она есть
			$_params['labelmax']  помечать максимумы графиков
			$_params['labelmin']  помечать минимумы графиков
			$_params['spline']    null, 0, false - рисовать линейный сплайн (ломаную линию)
			                      1 - рисовать простой тригонометрический сплайн (из отрезков синусоиды)
			
			$_data = array(
				array(...),
				array(...),
				...
			) данные графиков, каждый - в виде массива. Подписи к делениям оси
				абсцисс берутся из ключей массива $_data[0]
		*/
		$_data = array_values($_data);
		
		if(!isset($_params['width']))
		{
			$_params['width'] = 700;
		}
		if(!isset($_params['height']))
		{
			$_params['height'] = 350;
		}
		if(!isset($_params['xtick']))
		{
			$_params['xtick'] = 1;
		}
		
		// умножается на два, чтоб потом сжать в два раза с антиалиасингом
		$result = imagecreatetruecolor($_params['width'] * 2, $_params['height'] * 2);
		
		// цвет фона
		if(!isset($_params['bgcolor']))
		{
			$bgcolor = array(255, 255, 255);
		}
		else
		{
			$bgcolor = CChart::web2color($_params['bgcolor']);
		}
		$c = imagecolorallocate($result, $bgcolor[0], $bgcolor[1], $bgcolor[2]);
		imagefill($result, 0, 0, $c);
		
		// вычислим полезную площадь картинки
		$height = $_params['height'] * 2;
		$width  = $_params['width']  * 2;
		$ysize  = $height - 40;
		$xsize  = $width  - 20;
		
		// вычислим максимальное и минимальное значения графиков
		$maxvalue          = false;   // абсолютный максимум всех графиков
		$minvalue          = false;   // абсолютный минимум всех графиков
		$_maxvalues        = array(); // максимумы графиков
		$_minvalues        = array(); // минимумы графиков
		$_maxvalues_xcoord = array(); // x-координаты отметок максимума
		$_minvalues_xcoord = array(); // x-координаты отметок минимума
		foreach($_data as $id => $_graph)
		{
			foreach($_graph as $xcoord => $point)
			{
				$point = (float)$point;
				if($point > $maxvalue || $maxvalue === false)
				{
					$maxvalue = $point;
				}
				if($point < $minvalue || $minvalue === false)
				{
					$minvalue = $point;
				}
				if($point > $_maxvalues[$id] || !isset($_maxvalues[$id]))
				{
					$_maxvalues[$id]        = $point;
					$_maxvalues_xcoord[$id] = $xcoord;
				}
				if($point < $_minvalues[$id] || !isset($_minvalues[$id]))
				{
					$_minvalues[$id]        = $point;
					$_minvalues_xcoord[$id] = $xcoord;
				}
			}
		}
		if($maxvalue == $minvalue)
		{
			$maxvalue += 1;
		}

		// вычислим коэффициэнты сжатия по осям
		$yaspect = $ysize / ($maxvalue - $minvalue);
		$xaspect = $xsize / count($_data[0]);
		
		// нарисуем чёрточки на осях, сами оси и сетку, если надо
		if(!$_params['gridcolor'])
		{
			$_params['gridcolor'] = 'a0a0a0';
		}
		list($gcr, $gcg, $gcb) = CChart::web2color($_params['gridcolor']);
		$c     = 0;
		$c1    = imagecolorallocate($result, $gcr, $gcg, $gcb);
		$y0    = false; // координата y оси абсцисс на картинке
		$range = $maxvalue - $minvalue;
		$ytick = 1;
		for($i = -1; $i < 10; $i++)
		{
			if($range > pow(10, $i))
			{
				$ytick = pow(10, $i); // разница значений делений по оси y
			}
		}
		if($range / $ytick < 2)
		{
			$ytick /= 2;
		}
		for($i = floor(($minvalue - ($minvalue % $ytick))); $i <= $maxvalue; $i += $ytick)
		{
			$y = round($ysize - ($i - $minvalue) * $yaspect + 20);
			if($i == 0 && $y < $height && $y > 0)
			{
				$y0 = $y;
			}
			if($_params['grid'])
			{
				imageline($result, 0, $y, $xsize, $y, $c1);
			}
			imageline($result, 0, $y, 10, $y, $c);
			imagestring($result, 4, 14, $y - 10, $i, $c);
		}
		imageline($result, 10, 0, 10, $height, $c); // ось ординат
		if($y0 === false)
		{
			// если значение y0 не определено ранее, определим сейчас
			$y0 = round($yaspect * $maxvalue + 10);
			if($y0 > $height)
			{
				$y0 = $height - 1;
			}
			elseif($y0 < 0)
			{
				$y0 = 0;
			}
		}
		$bYTicksAboveAxis = ($y0 > $height - 15);    // подписи над осью абсцисс или под ней
		imageline($result, 0, $y0, $width, $y0, $c); // ось абсцисс
		$keys  = array_keys($_data[0]);              // абсциссы
		for($i = 0; $i < count($keys); $i++)
		{
			$x = round($i * $xaspect + 10);
			if(!($i % $_params['xtick']))
			{
				// каждая $_params['xtick']-ная чёрточка подлиннее, и к ней есть подпись
				imageline($result, $x, $y0 - 6, $x, $y0 + 6, $c);
				imagestring($result, 4, $x + 2, $bYTicksAboveAxis ? ($y0 - 15) : ($y0 + 2), $keys[$i], $c);
				if($_params['grid'] && $i)
				{
					imageline($result, $x, 0, $x, $height, $c1);
				}
			}
			else
			{
				imageline($result, $x, $y0 - 2, $x, $y0 + 2, $c);
			}
		}
		
		// собственно график
		$_params['xaspect']          = $xaspect;
		$_params['yaspect']          = $yaspect;
		$_params['ysize']            = $ysize;
		$_params['bYTicksAboveAxis'] = $bYTicksAboveAxis;
		$_params['_maxvalues']       = $_maxvalues;
		$_params['_minvalues']       = $_minvalues;
		$_params['_maxvaluesx']      = $_maxvalues_xcoord;
		$_params['_minvaluesx']      = $_minvalues_xcoord;
		$_params['minvalue']         = $minvalue;
		switch((int)$_params['spline'])
		{
			case 1:
			{
				$result = CChart::draw_sin_spline($result, $_data, $_params);
				break;
			}
			case 0:
			default:
			{
				$result = CChart::draw_linear_spline($result, $_data, $_params);
				break;
			}
		}
		
		// антиалиасинг
		$result1 = imagecreatetruecolor($_params['width'], $_params['height']);
		imagecopyresampled($result1, $result, 0, 0, 0, 0, $_params['width'], $_params['height'], $width, $height);
		imagedestroy($result);
		return $result1;
	}
	
	/*
		Плоская круговая диаграмма. Подробнее о параметрах см. в теле функции.
		@param  array $_params параметры
		@param  array $_data   данные графиков в виде массивов
		@return resource image
	*/
	public static function pie($_params, $_data)
	{
		/*
			$_params['width']   ширина картинки
			$_params['height']  высота картинки
			$_params['slice']   количество отображаемых значений. Все остальные
				попадут в 'Other'
			$_params['bgcolor'] цвет фона
			$_params['raw']     если true, то возвращается картинка вдвое
				большая по размерам и без антиальясинга
		*/
		if(!isset($_params['slice']))
		{
			$_params['slice'] = 10;
		}
		if(!isset($_params['width']))
		{
			$_params['width'] = 700;
		}
		if(!isset($_params['height']))
		{
			$_params['height'] = 350;
		}
		
		// умножаем на два, чтоб потом сжать вдвое с антиалиасингом
		$result = imagecreatetruecolor($_params['width'] * 2, $_params['height'] * 2);
		
		// полезная площадь картинки
		$height = $_params['height'] * 2 - 2;
		$width  = $_params['width'] * 2 - 2;
		
		// цвет фона
		if(!isset($_params['bgcolor']))
		{
			$bgcolor = array(255, 255, 255);
		}
		else
		{
			$bgcolor = CChart::web2color($_params['bgcolor']);
		}
		$c = imagecolorallocate($result, $bgcolor[0], $bgcolor[1], $bgcolor[2]);
		imagefill($result, 0, 0, $c);
		
		// кружок
		$bTitlesLeft = true; // подписи слева или снизу
		if($width > $height)
		{
			$radius = floor($height / 2) - 2;
		}
		else
		{
			$radius = floor($width / 2) - 2;
		}
		imageellipse($result, $radius + 2, $radius + 2, 2 * $radius, 2 * $radius, 0);
		
		// отрезать лишнее
		$summ  = 0; // сумма всех значений
		$count = 0; // количество значений
		$other = 0; // сумма значений 'Other'
		foreach($_data as $k => $v)
		{
			if($count >= $_params['slice'] && $k != 'Other')
			{
				$other += $v;
			}
			$summ += $v;
			$count++;
		}
		$other += $_data['Other'];
		$_data  = array_slice($_data, 0, $_params['slice'], true);
		if($other)
		{
			$_data['Other'] = $other;
		}
		
		// поехали
		imageline($result, $radius + 2, $radius + 2, $radius + 2, 2 * $radius + 2, 0); // первая линия
		$radsumm    = 0; // текущий угол в радианах
		$heightsumm = 0; // сумма высоты подписей к графику
		$i          = 1; // счётчик
		$titlex     = ($bTitlesLeft ? (2 * $radius) : 0) + 10; // координата x подписей
		foreach($_data as $k => $v)
		{
			$rad = 6.28 * ($v / $summ); // ширина сектора
			$radsumm += $rad;
			$linex = $radius * sin($radsumm) + $radius + 2;
			$liney = $radius * cos($radsumm) + $radius + 2;
			
			// последнюю радиальную линию не рисуем
			if($i < count($_data))
			{
				imageline($result, $radius + 2, $radius + 2, round($linex), round($liney), 0);
			}
			
			// координаты точки, куда ткнуть заливкой
			$fillx = ($radius - 5) * sin($radsumm - $rad / 2) + $radius + 2;
			$filly = ($radius - 5) * cos($radsumm - $rad / 2) + $radius + 2;
			$color = imagecolorallocate($result, rand(0, 255), rand(0, 255), rand(0, 255));
			imagefill($result, $fillx, $filly, $color);
			imagerectangle($result, $titlex, $heightsumm, $titlex + 20, $heightsumm + 20, 0);
			imagefill($result, $titlex + 2, $heightsumm + 2, $color);
			imagestring($result, 5, $titlex + 25, $heightsumm, $k.' ('.round(100 * ($v / $summ), 2).'%)', 0);
			$heightsumm += 30;
			if($heightsumm >= $height - 30)
			{
				$heightsumm = 0;
				$titlex += 300;
			}
			$i++;
		}
		if($_params['raw'])
		{
			return $result;
		}
		
		// антиалиасинг
		$result1 = imagecreatetruecolor($_params['width'], $_params['height']);
		imagecopyresampled($result1, $result, 0, 0, 0, 0, $_params['width'], $_params['height'], $width, $height);
		imagedestroy($result);
		return $result1;
	}
	
	/**
	 * Рисование графика, состоящего из ломаных линий (линейного сплайна)
	 * @param  resource $result   картинка, в которой рисовать
	 * @param  array    $_data    данные для отображения
	 * @param  array    $_params  массив с параметрами, дополненный данными рассчёта
	 * @return resource результат рисования графика в $result
	 */
	protected static function draw_linear_spline($result, $_data, $_params)
	{
		$xaspect           = $_params['xaspect'];
		$yaspect           = $_params['yaspect'];
		$ysize             = $_params['ysize'];
		$bYTicksAboveAxis  = $_params['bYTicksAboveAxis'];
		$_maxvalues        = $_params['_maxvalues'];
		$_minvalues        = $_params['_minvalues'];
		$_maxvalues_xcoord = $_params['_maxvaluesx'];
		$_minvalues_xcoord = $_params['_minvaluesx'];
		$minvalue          = $_params['minvalue'];
		
		$g = 0; // количество графиков
		foreach($_data as $id => $_graph)
		{
			$p     = 0; // количество точек
			$xprev = false;
			$yprev = false;
			$brush = imagecreatetruecolor(3, 3);
			if($_params['colors'][$g])
			{
				$c = CChart::web2color($_params['colors'][$g]);
				$c = imagecolorallocate($brush, $c[0], $c[1], $c[2]);
			}
			else
			{
				$c = 0;
			}
			imagefill($brush, 0, 0, $c);
			imagesetbrush($result, $brush);
			foreach($_graph as $xcoord => $point)
			{
				// координаты текущей точки
				$x = round($p * $xaspect + 10);
				$y = round($ysize - ($point - $minvalue) * $yaspect + 20);
				if($xprev !== false && $yprev !== false)
				{
					// если есть координаты предыдущей точки, то рисуем линию
					imageline($result, $xprev, $yprev, $x, $y, IMG_COLOR_BRUSHED);
				}
				// и запоминаем текущие координаты
				$xprev = $x;
				$yprev = $y;
				$p++;
				// если надо - подпишем точки
				if($_params['labelmax'])
				{
					if($xcoord == $_maxvalues_xcoord[$id])
					{
						imagestring($result, 4, $x + 5, $y - 15, $_maxvalues[$id], $c);
					}
				}
				if($_params['labelmin'])
				{
					if($xcoord == $_minvalues_xcoord[$id])
					{
						imagestring($result, 4, $x + 5, $bYTicksAboveAxis ? ($y - 15) : ($y + 2), $_minvalues[$id], $c);
					}
				}
			}
			imagedestroy($brush);
			$g++;
		}
		return $result;
	}
	
	/**
	 * Рисование графика, состоящего из кусков синусоид
	 * @param  resource $result   картинка, в которой рисовать
	 * @param  array    $_data    данные для отображения
	 * @param  array    $_params  массив с параметрами, дополненный данными рассчёта
	 * @return resource результат рисования графика в $result
	 */
	protected static function draw_sin_spline($result, $_data, $_params)
	{
		$xaspect           = $_params['xaspect'];
		$yaspect           = $_params['yaspect'];
		$ysize             = $_params['ysize'];
		$bYTicksAboveAxis  = $_params['bYTicksAboveAxis'];
		$_maxvalues        = $_params['_maxvalues'];
		$_minvalues        = $_params['_minvalues'];
		$_maxvalues_xcoord = $_params['_maxvaluesx'];
		$_minvalues_xcoord = $_params['_minvaluesx'];
		$minvalue          = $_params['minvalue'];
		
		$g = 0; // количество графиков
		foreach($_data as $id => $_graph)
		{
			$p     = 0; // количество точек
			$xprev = false;
			$yprev = false;
			$brush = imagecreatetruecolor(3, 3);
			if($_params['colors'][$g])
			{
				$c = CChart::web2color($_params['colors'][$g]);
				$c = imagecolorallocate($brush, $c[0], $c[1], $c[2]);
			}
			else
			{
				$c = 0;
			}
			imagefill($brush, 0, 0, $c);
			imagesetbrush($result, $brush);
			
			$sizeofdata    = sizeof($_graph);
			$_graph_values = array_values($_graph);
			$_graph_keys   = array_keys($_graph);
			unset($_graph);
			for($i = 0; $i < $sizeofdata; $i++)
			{
				$point  = $_graph_values[$i];
				$xcoord = $_graph_keys[$i];
				
				// координаты текущей точки
				$x = round($p * $xaspect + 10);
				$y = round($ysize - ($point - $minvalue) * $yaspect + 20);
				$jx_prev = $xprev;
				$jy_prev = $yprev;
				// если есть координаты предыдущей точки, то рисуем сплайн (отрезок функции)
				if($xprev !== false && $yprev !== false)
				{
					// для сплайна, являющемся куском синусоиды от 0 до pi/2, определяем ширину и высоту
					$spline_h = $y - $yprev;
					$spline_w = $x - $xprev;
					for($jx = $xprev; $jx <= $x; $jx++)
					{
						$jy = round($yprev + sin(($jx - $xprev) / $spline_w * pi - pi / 2) * $spline_h / 2 + $spline_h / 2);
						imageline($result, $jx_prev, $jy_prev, $jx, $jy, IMG_COLOR_BRUSHED);
						$jx_prev = $jx;
						$jy_prev = $jy;
					}
				}
				
				// и запоминаем текущие координаты
				$xprev = $x;
				$yprev = $y;
				$p++;
				
				// если надо - подпишем точки
				if($_params['labelmax'])
				{
					if($xcoord == $_maxvalues_xcoord[$id])
					{
						imagestring($result, 4, $x + 5, $y - 15, $_maxvalues[$id], $c);
					}
				}
				if($_params['labelmin'])
				{
					if($xcoord == $_minvalues_xcoord[$id])
					{
						imagestring($result, 4, $x + 5, $bYTicksAboveAxis ? ($y - 15) : ($y + 2), $_minvalues[$id], $c);
					}
				}
			}
			imagedestroy($brush);
			$g++;
		}
		return $result;
	}
	
	
	/**
	 * Переделывает цвет в веб-представлении (типа 'f0ee00') в rgb (массив
	 * из трёх чисел).
	 * @param  string $str строка цвета
	 * @return array
	 */
	protected static function web2color($str)
	{
		if($str[0] == '#')
		{
			$str = substr($str, 1);
		}
		$str = str_split($str, 2);
		for($i = 0; $i < 3; $i++)
		{
			$result[$i] = hexdec($str[$i]);
		}
		return $result;
	}
}

?>
