<?php
@session_start();
ob_start();
header('content-Type:image/gif');
echo mt_srand(time());
$randval = mt_rand();
$seccode = substr($randval, -4);
$length = strlen($seccode);
$_SESSION['seccode'] = $seccode;
//用SESSION保存驗證碼
$img = imagecreate(60, 20);
$black = ImageColorAllocate($img, 0, 0, 0);
$white = ImageColorAllocate($img, 255, 255, 255);
$gray = ImageColorAllocate($img, 200, 200, 200);
imagefill($img, 0, 0, $gray);

for ($i = 0; $i < 300; $i++)//加入干擾象素
{
	$randcolor = ImageColorallocate($img, rand(10, 250), rand(10, 250), rand(10, 250));
	imagesetpixel($img, rand() % 90, rand() % 30, $randcolor);
}

for ($i = 0; $i < $length; $i++) {
	$color = imagecolorallocate($img, abs(mt_rand() % 256), abs(mt_rand() % 256), abs(mt_rand() % 256));
	imagechar($img, 5, abs(mt_rand() % 4) + $i * 15, abs(mt_rand() % 5), $seccode[$i], $color);
}
imagegif($img);
imageDestroy($img);
ob_end_flush();
?>