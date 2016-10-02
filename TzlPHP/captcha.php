<?php

namespace TzlPHP;
class Captcha
{
	public static function img()
	{
		// 字体文件 
		$fontFile = './TzlPHP/ROMANTIC.TTF';
		// 定义高、高
		$width = 130;
		$height = 50;
		$length = 6;  // 验证码长度
		// 创建画布
		$image = imagecreatetruecolor($width, $height);
		//背景色
		$_r = mt_rand(0, 80);
		$_g = mt_rand(0, 80);
		$_b = mt_rand(0, 80);
		$bgColor = imagecolorallocate($image, $_r, $_g, $_b);
		imagefill($image, 0, 0, $bgColor);
		// 随机生成字符串
		$string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$word = ''; // 随机字符串
		for($i=0; $i<$length; $i++)
		{
			$x = mt_rand(0, 61);
			$word .= $string[$x]; // 随机取一个字符放到$word里面
		}
		// 把生成的验证码字符串存到session
		$_SESSION['captcha'] = $word;
		// 循环输出每个字母
		// 颜色不同
		$_r = mt_rand(128, 255);
		$_g = mt_rand(128, 255);
		$_b = mt_rand(128, 255);
		$color = imagecolorallocate($image, $_r, $_g, $_b);
		for($i=0; $i<$length; $i++)
		{
			$angle = mt_rand(-10, 20);
			$size = mt_rand(20, 30);
			imagettftext($image, $size, $angle, ($i+1) * 15, 36, $color, $fontFile, $word[$i]);
		}

		/***************** 画一根线 ********************/
		imagesetthickness($image, 3);
		// 生成Y坐标
		$y = mt_rand(10, 40);
		// 左线
		imagearc($image, 25, $y, 60, 10, 0, 180, $color);
		// 右线
		imagearc($image, 85, $y, 60, 10, 180, 360, $color);
		/**** 输出 ****/
		header('Content-Type:image/png');
		imagepng($image);
	}
}