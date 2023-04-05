<?php
function ReadIconFile($n){
	global $imw;
	global $imh;
	global $rows;
	global $cols;
	global $l;
	global $cnt;
	$img = imageCreateFromPng($n.'.png');	
	imagesavealpha($img,true);
	$f = fopen($n.'.txt','r');
	if (!$f) die('fail1');
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
	$cnt=$c-1;
	fclose($f);
	return $img;
}

function addtext($s){
	$filename = 'klan/iconlist_guild.txt';
	if (is_writable($filename)) {
		$data = file_get_contents($filename);
		//if (!$handle = fopen($filename, 'a')) {
		if ($data === false) {
		        return "fail6";
		        exit;
		}
		if (file_put_contents($filename, rtrim($data).$s) === false) {
		///if (fwrite($handle, $s) === FALSE) {
		        echo "Не могу произвести запись в файл ($filename)";
		        return "fail5";
		}    
		//fclose($handle);
	} else {
		 return "fail4";
	}
	return "ok";
}

function seticon($file,$id,$servid){
	global $imw;
	global $imh;
	global $rows;
	global $cols;
	global $l;
	global $cnt, $db;
	$filename=$file;
	if (@!fopen($filename, "r")) {		
		return "fail1";
	}
	$srcimg = imageCreateFromPng($filename);
	imagesavealpha($srcimg, true);	
	$img=ReadIconFile('klan/iconlist_guild');
	imagealphablending( $img, false );
	$num = (isset($l[$servid][$id]))?$l[$servid][$id]:0;
	if ($num==0){
		$num=$cnt+1;
		$add=true;
	} else $add=false;
	if ($num>$cols) {$row=floor(($num-1)/$cols);} else {$row=0;}
	$col=$num-($row*$cols)-1;
	if ($col<0) $col=0;
	$transparent = imagecolorallocatealpha( $img, 0, 0, 0, 127 );
	imageFilledRectangle($img, $col*$imw, $row*$imh, ($col+1)*$imw, ($row+1)*$imh, $transparent); 
	imagecopy($img,$srcimg,$col*$imw,$row*$imh,0,0,$imw,$imh);
	if ((!is_writable('klan/iconlist_guild.txt'))||(!is_writable('klan/iconlist_guild.png'))) return("fail2");
	imagepng($img,'klan/iconlist_guild.png');
	if ($add==true) {
		if (addtext("\n".$servid."_".$id.".dds")!='ok') return "fail3";
	}
	unlink($filename);
	$filename="klan/version";
	$f=fopen($filename,'r');
	$ver=fread($f,filesize($filename));
	fclose($f);
	$f=fopen($filename,'w');
	$ver+=1;
	fwrite($f,$ver);
	fclose($f);	
	@$db->query('DELETE FROM `klan_pic` WHERE `klanid`='.$id.' AND `servid`='.$servid);
	return "ok";
}
