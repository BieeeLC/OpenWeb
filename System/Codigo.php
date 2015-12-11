<?php
$codigo = $_GET['codigo'];
header("Content-type: image/png");
$im = @imagecreate(33, 16);
$background_color = imagecolorallocate($im, 255, 255, 255);
$text_color = imagecolorallocate($im, 255, 0, 0);
imagestring($im, 3, 2, 1, $codigo, $text_color);
imagepng($im);
imagedestroy($im);
?>