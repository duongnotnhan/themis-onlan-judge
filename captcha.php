<?php
session_start();
header('Content-type: image/png');

if (!isset($_SESSION['captcha_a']) || !isset($_SESSION['captcha_b']) || !isset($_SESSION['captcha_op']) || isset($_GET['new'])) {
	$ops = ['+', '-', '*'];
	$op = $ops[array_rand($ops)];
	$a = rand(1, 9);
	$b = rand(1, 9);

	switch ($op) {
		case '+':
			$answer = $a + $b;
			break;
		case '-':
			if ($a < $b) list($a, $b) = [$b, $a];
			$answer = $a - $b;
			break;
		case '*':
			$answer = $a * $b;
			break;
	}

	$_SESSION['captcha_a'] = $a;
	$_SESSION['captcha_b'] = $b;
	$_SESSION['captcha_op'] = $op;
	$_SESSION['captcha_answer'] = $answer;
} else {
	$a = $_SESSION['captcha_a'];
	$b = $_SESSION['captcha_b'];
	$op = $_SESSION['captcha_op'];
}

$text = "$a $op $b = ?";

$im = imagecreatetruecolor(100, 36);
$bg = imagecolorallocate($im, 40, 40, 40);
$fg = imagecolorallocate($im, 255, 215, 0);
imagefilledrectangle($im, 0, 0, 100, 36, $bg);

for ($i = 0; $i < 5; $i++) {
	$noise_color = imagecolorallocate($im, rand(100,255), rand(100,255), rand(100,255));
	imageline($im, rand(0,100), rand(0,36), rand(0,100), rand(0,36), $noise_color);
}

for ($i = 0; $i < 100; $i++) {
	$dot_color = imagecolorallocate($im, rand(150,255), rand(150,255), rand(150,255));
	imagesetpixel($im, rand(0,99), rand(0,35), $dot_color);
}

$x = rand(8, 18);
$y = rand(8, 14);

imagestring($im, 5, $x, $y, $text, $fg);

imagepng($im);
imagedestroy($im);
?>