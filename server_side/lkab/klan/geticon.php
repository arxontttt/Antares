<?php
require_once('../config.php');
function ReadIconFile($n){
	global $imw;
	global $imh;
	global $rows;
	global $cols;
	global $l;
	$img = imageCreateFromPng($n.'.png');
	imagesavealpha($img,true);	
	$f=fopen($n.'.txt','r');
	if (!$f) die('Error opening file');
	$imw = trim(fgets($f));
	$imh = trim(fgets($f));
	$rows = trim(fgets($f));
	$cols = trim(fgets($f));
	$l = array();
	$c=1;
	while (!feof($f)){
		$line=fgets($f);
		if ($line == '') continue;
		$a=explode("_", $line);
		if (!isset($a[1])) $a[1]='';
		$a1=explode(".",$a[1]);
		if (!isset($l[$a[0]])) {
			$l[$a[0]] = array();
		}
		$l[$a[0]][$a1[0]] = trim($c);
		$c++;
	}
	fclose($f);
	return $img;
}
if (!isset($_GET['servid'])) $servid = 1; else $servid = intval($_GET['servid']);
if (isset($_GET['num'])) {$num=(int)$_GET['num'];} else {$num=0;}
$default = (isset($_GET['default']))?$_GET['default']:0;
if (isset($_GET['klan'])) {
	$res = $db->query('SELECT `pic` FROM `klan_pic` WHERE `klanid`='.intval($_GET['klan']).' AND `servid`='.$servid);
	if ($res && $res->num_rows) {
		$row = mysqli_fetch_assoc($res);
		Header("Content-type: image/png");
		echo $row['pic'];
		die();
	}
}
$img = ReadIconFile('iconlist_guild');
if (isset($_GET['klan'])) {
	$num = (isset($l[$servid][$_GET['klan']]))?$l[$servid][$_GET['klan']]:$default;
}
if ($num>$cols) {$row=floor(($num-1)/$cols);} else {$row=0;}
$col=$num-($row*$cols)-1;
if ($col<0) $col=0;
//echo (count($l)+4).'<br>';
$im1 = imageCreate($imw,$imh);
//echo $col;
imageCopy($im1,$img,0,0,$col*$imw,$row*$imh,$imw,$imh);
ob_start();
imagepng($im1);
$pic = ob_get_clean();
if (isset($_GET['klan'])) {
	$sql = sprintf("INSERT INTO `klan_pic` (`klanid`, `servid`, `pic`) VALUES (%d, %d, '%s')", $_GET['klan'], $servid, $db->real_escape_string($pic));
	$db->query($sql);
}
Header("Content-type: image/png");
echo $pic;
