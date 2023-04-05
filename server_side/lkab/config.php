<?php
date_default_timezone_set('Europe/Moscow');
// Ключ активации ЛК (от разработчика)
$act_key = '';
// IP адрес сервиса GameDB
$gamedb_ip = '127.0.0.1';
$gamedb_port = 29400;
// IP адрес сервиса GDeliveryD
$delivery_ip = '127.0.0.1';
$delivery_port = 29100;
$delivery_provider_port = 29300;
// Данные базы данных MySql
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_pass = 'passw';
$mysql_dbname = 'zx';
// Версия сервера (поддерживаются 229,311,420,440)
define('ProtocolVer', 420);

$db = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_dbname);
if ($db->connect_errno) {
    die('ErrorBase');
}

function KlanPic($num, $servid){
	$filename='klan/'.$_FILES['upload']['name'];
	move_uploaded_file ($_FILES['upload']['tmp_name'], $filename);
	require_once("klan/seticon.php");
	$r = seticon($filename,$num,$servid);
	return $r;
}
