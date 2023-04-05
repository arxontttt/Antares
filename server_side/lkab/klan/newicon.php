<?php
die();
// For 2 server faction icons
if ((!isset($_POST['num']))||(!isset($_POST['servid']))) die("fail");
//$filename=$_POST['filename'];
$filename=$_FILES['upload']['name'];
move_uploaded_file ( $_FILES['upload']['tmp_name'], $filename);
$num=$_POST['num'];
$servid=$_POST['servid'];
require_once("seticon.php");
$r=seticon($filename,$num,$servid);
echo $r;
