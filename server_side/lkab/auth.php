<?php
require_once('config.php');
require_once('packetstream.php');
require_once('protocols.php');
require_once('pclzip.lib.php');
$factrole = array(2=>"Глава",3=>"Маршал",4=>"Капитан",5=>"Сержант",6=>"");
$gender = array("М","Ж");
mb_internal_encoding("UTF-8");

class my_db extends mysqli {
	function query($sql, $resultmode = NULL) {
		//$dh = fopen ('mysql_sql.log', 'a+');
		//if ($dh) {
		//	fwrite($dh, date("Y-m-d H:i:s").' '.$sql."\n\n");
		//	fclose($dh);
		//}

		$res = parent::query($sql, $resultmode);
		if (!$res) {
			$msg = $this->error;
			$dh = fopen ('mysql_error.log', 'a+');
			if ($dh) {
				fwrite($dh, date("Y-m-d H:i:s").' '.$sql."\n");
				fwrite($dh, $msg."\n\n");
				fclose($dh);
			}
		}
		return $res;
	}
}

$db = new my_db($mysql_host, $mysql_user, $mysql_pass, $mysql_dbname);
if ($db->connect_errno) {
    die('ErrorBase');
}

$occ = array(0 => 'Новичок', 1 => 'Вим I', 2 => 'Вим II', 3 => 'Вим III', 4 => 'Хаккан I', 5 => 'Хаккан II', 6 => 'Хаккан III', 7 => 'Айне I', 8 => 'Айне II', 9 => 'Айне III', 10 => 'Скайя I', 11 => 'Скайя II', 12 => 'Скайя III', 13 => 'Вим IV', 14 => 'Вим V', 16 => 'Хаккан IV', 17 => 'Хаккан V', 19 => 'Айне IV', 20 => 'Айне V', 22 => 'Скайя IV', 23 => 'Скайя V', 25 => 'Морто I', 26 => 'Морто II', 27 => 'Морто III', 28 => 'Морто IV', 29 => 'Морто V', 33 => 'Титаны I', 34 => 'Титаны II', 35 => 'Титаны III', 36 => 'Титаны IV', 37 => 'Титаны V', 39 => 'Арден I', 40 => 'Арден II', 41 => 'Арден III', 42 => 'Арден IV', 43 => 'Арден V', 45 => 'Умбра I', 46 => 'Умбра II', 47 => 'Умбра III', 48 => 'Умбра IV', 49 => 'Умбра V', 51 => 'Вайлин I', 52 => 'Вайлин II', 53 => 'Вайлин III', 54 => 'Вайлин IV', 55 => 'Вайлин V', 56 => 'Ниру I', 57 => 'Ниру II', 58 => 'Ниру III', 59 => 'Ниру IV', 60 => 'Ниру V', 64 => 'Тайо I', 65 => 'Тайо II', 66 => 'Тайо III', 67 => 'Тайо IV', 68 => 'Тайо V', 96 => 'Войда I', 97 => 'Войда II', 98 => 'Войда III', 99 => 'Войда IV', 100 => 'Войда V', 102 => 'Психея I', 103 => 'Психея II', 104 => 'Психея III', 105 => 'Психея IV', 106 => 'Психея V', 108 => 'Кентавр I', 109 => 'Кентавр II', 110 => 'Кентавр III', 111 => 'Кентавр IV', 112 => 'Кентавр V', 117 => 'Гидран I', 118 => 'Гидран II', 119 => 'Гидран III', 120 => 'Гидран IV', 121 => 'Гидран V' , 71 => 'Сейра I', 72 => 'Сейра II', 73 => 'Сейра III', 74 => 'Сейра IV', 75 => 'Сейра V');

function GetOccupationName($o)
{
	global $occ;
	if (array_key_exists($o, $occ)) return $occ[$o];
	return $o;
}

function query2mysql($sql, $check_num_rows = false){
	global $db;
	$res = $db->query($sql);	
	if (!$res || $db->errno > 0 || ($check_num_rows && $res->num_rows < 1)) {
		die('15');
	}
	return $res;
}

function GetCashFromAcc($id, $login=''){
	global $db;
	if ($login == '') $sql = "SELECT `ID`,`name`,`lkgold`, `lksilver` FROM `users` WHERE `ID`=".$id; else
			  $sql = "SELECT `ID`,`name`,`lkgold`, `lksilver` FROM `users` WHERE `name`='".$db->real_escape_string($login)."'";
	$result = query2mysql($sql);
	if ($result->num_rows > 0){
		$row = mysqli_fetch_assoc($result);
		$a = array($row['lkgold'], $row['lksilver'], $row['ID'], $row['name']);
	} else $a = array(0, 0, 0, '');
	return $a;
}

function AddLKLogs($id, $ip, $gpoint, $spoint, $desc) {
	global $db;
	$db->query("SET NAMES utf8");
	$CashLeft = GetCashFromAcc($id);
	$query = "insert into `lklogs` (`userid` ,`data` , `ip`, `gold` ,`silver`, `gold_rest` ,`silver_rest`, `desc` ) VALUES ($id, now(), '".$db->real_escape_string($ip)."', -$gpoint, -$spoint, ".$CashLeft[0].", ".$CashLeft[1].", '".$db->real_escape_string($desc)."');";
	return $db->query($query);	
}

function GiveGold($user_id, $gold, $silver, $desc, $ip = ''){
	if ($gold != 0 || $silver != 0)	$res = query2mysql("UPDATE `users` SET `lkgold`=`lkgold`+$gold, `lksilver`=`lksilver`+$silver WHERE `ID`=$user_id");
	AddLKLogs($user_id, $ip, $gold, $silver, $desc);
}

function UseCash($id, $zoneid, $aid, $silver)
{
	global $db;
	$sql="call usecash(".$id.",".$zoneid.",0,".$aid.",0,".$silver.",1,@error)";
	return query2mysql($sql);
}

function AddRoleToTop($rid)
{
	global $db;
	$role = GetRole($rid, 63);
	if ($role['retcode'] != 0) die('Get role '.$rid.' error '.$role['retcode']);
	if ($role['value']['status']['level'] == 0) return 0;
	$res1 = $db->query('DELETE FROM `top` WHERE `roleid`='.$rid);
	if (!$res1) die($db->error);
	$sql = "INSERT INTO `top` (`add_date`,roleid, userid, rolename, rolelevel, reborn, pkvalue, rolegender, roleprof, rolerep, factionid, factionrole, hp, mp, timeused,cashadd,cashtotal,cashused) VALUES (now(),'".$rid."', '".$role['value']['userid']."', '".$db->real_escape_string($role['value']['name'])."', '".$role['value']['status']['level']."', '".(int)($role['value']['status']['reborndata']!='')."', '".$role['value']['status']['pkvalue']."', '".$role['value']['gender']."', '".$role['value']['status']['occupation']."', '".$role['value']['status']['reputation']."', ".$role['value']['factionid'].", ".$role['value']['title'].", '".$role['value']['status']['hp']."', '".$role['value']['status']['mp']."', '".$role['value']['status']['time_used']."', '".$role['value']['cash_add2']."', '".$role['value']['cash_total']."', '".$role['value']['cash_used']."');";
	$result = $db->query($sql);
	if ($db->errno > 0) die($db->error);
	return $role['value']['factionid'];
}

function InitItem()
{
	global $Structures;
	$pp = new Protocols();
	$i = $pp->unmarshal('', $Structures['GRoleInventory']);				
	return $i;
}

function CheckRoleLvl($userid, $level_need){
	$f = GetUserRoles($userid);
	if ($f['retcode'] != 0) return false;
	$fnd = false;
	$answ['roles_count'] = count($f['data']);	
	foreach ($f['data'] as $i => $val){
		if ($val['level'] >= $level_need) {
			$fnd = $val['name'];
			break;
		}
	}
	return $fnd;
}

function ShowCost($cost){
	global $gpoint, $spoint;
	$c = explode('|', $cost);
	$gpoint = (int)$c[0];
	if (count($c) > 1) $spoint = (int)$c[1]; else $spoint = 0;	
}

function MakeAnswer($paramarray, $errorcode=0, $errtxt='', $errparam1='', $errparam2='', $errparam3=''){
	$answer = array(
		'errorcode' => $errorcode,
		'errtxt' => $errtxt,
		'errparam1' => $errparam1,
		'errparam2' => $errparam2,
		'errparam3' => $errparam3
	);
	if (is_array($paramarray)) $answer = array_merge($paramarray, $answer);
	echo serialize($answer);
	die();
}

function MakeAnswerErrGetRoles($answ)
{
	MakeAnswer($answ, 1000, 'Ошибка получения списка персонажей');
}

function MakeAnswerErrRolesCount($answ)
{
	MakeAnswer($answ, 1000, 'Сначала создайте персонажа в игре');
}

function finditem($id,$itemid){
	$cnt = 0;
	$rd = GetRoleData($id);
	if ($rd['retcode'] !=0 || $rd['packet_check_error'] != 0) return $cnt;
	foreach ($rd['value']['pocket']['items'] as $i => $val){
		if ($val['id'] == $itemid) $cnt += $val['count'];
	}
	foreach ($rd['value']['storehouse']['items'] as $i => $val){
		if ($val['id'] == $itemid) $cnt += $val['count'];
	}
	return $cnt;
}

function takeitem($id,$itemid,$count){
	$cnt = $count;
	$rd = GetRoleData($id);
	if ($rd['retcode'] !=0 || $rd['packet_check_error'] != 0) die('13');	
	foreach ($rd['value']['pocket']['items'] as $i => $val){
		if (($val['id'] == $itemid)&&($cnt>0)) {
			if ($cnt >= $val['count']) {
				$cnt -= $val['count'];
				$rd['value']['pocket']['items'][$i]['count'] = 0;
				unset($rd['value']['pocket']['items'][$i]);
			} else {
				$rd['value']['pocket']['items'][$i]['count'] -= $cnt;
				$cnt = 0;				
			}			
		}
	}
	if ($cnt > 0)
	foreach ($rd['value']['storehouse']['items'] as $i => $val){
		if (($val['id'] == $itemid)&&($cnt>0)) {
			if ($cnt >= $val['count']) {
				$cnt -= $val['count'];
				$rd['value']['storehouse']['items'][$i]['count'] = 0;
				unset($rd['value']['storehouse']['items'][$i]);
			} else {
				$rd['value']['storehouse']['items'][$i]['count'] -= $cnt;
				$cnt = 0;				
			}			
		}
	}
	if ($cnt == 0) $rd = PutRoleData($id, $rd['value']); else return false;
	if (!$rd || $rd['retcode'] != 0) return false;
	return true;
}

class ExtItem {
	var	$name='';
	var	$icon='';
	var	$list=0;
}

class BonusItem {
	var	$ids;
	var	$names;
	
	function BonusItem(){
		$this->ids = array();
		$this->names = array();
	}
}

function isonline($id, $roleid, $sleep_time=3, $check1min = true){
	global $db;
	$ban_reason = 'Сохранение персонажа';
	$l = GMListOnlineUser(0, 0, $id, '');
	if (isset($l['userlist']) && count($l['userlist']) > 0) 
	{
		foreach ($l['userlist'] as $i => $val)
		{
			if ($val['userid'] == $id) die('25');
		}
	}
	if ($check1min) {
		// не менее 1 минуты с момента выхода из игры
		$d = @date('Y-m-d H:i:s', time()-60);
		$sql = sprintf("SELECT * FROM `login_log` WHERE `action`=2 AND `userid`=%d AND `data`>='%s'", $id, $d);
		$res = query2mysql($sql);
		if ($res->num_rows) die('108');
	}
	$b = GetRoleBase($roleid);
	$banned = false;
	if (!@AssignData(ak)) die('13');
	if (count($b['value']['forbid']) > 0)
	{
		foreach ($b['value']['forbid'] as $ii => $val){
			if ($val['type'] != 0 && $val['reason'] != $ban_reason)
			{
				$rest = $val['createtime'] + $val['time'] - time();
				if ($rest > 0) $banned = true;
			}				
		}		
	}
	if (!$banned)
	{
		$kick = GMKickoutRole(1024, 1, $roleid, 2, $ban_reason);
		if ($kick['retcode'] != 0 ) die('25');
		sleep($sleep_time);
	}
	return false;		
}

function isroleonline($userid){
	$l = GMListOnlineUser(0, 0, $userid, '');
	if (count($l['userlist']) > 0) 
	{
		foreach ($l['userlist'] as $i => $val)
		{
			if ($val['userid'] == $userid && $val['roleid'] > 0) return $val['roleid'];
		}		
	}	
	return 0;
}

function CheckPoints($id, $gold, $silver){
	global $db;	
	if ($gold == 0 && $silver == 0) return;
	// Пытаемся рассинхронизировать параллельные потоки для избежания дюпа и багания монет
	usleep(rand(50000,900000));
	// Блокируем таблицу перед проверкой
	query2mysql('LOCK TABLES `users` WRITE, `shop_names` WRITE, `klan_items` WRITE, `lklogs` WRITE, `shop_items` WRITE, `klan_pic` WRITE');
	// Проверка остатка монет
	$query = "SELECT * FROM `users` WHERE `ID` = ".$id;
	$rresult = query2mysql($query, true);
	$row = mysqli_fetch_assoc($rresult);
	if ($row['lkgold'] < $gold) die('17');
	if ($row['lksilver'] < $silver) die('18');
}

function CheckKlanMaster($id, $fid, $f){
	global $master_name;
	foreach ($f['data'] as $i => $val){
		$r = GetRole($val['id'], 63);
		if (($r['value']['factionid'] == $fid)&&($r['value']['title'] == 2)) {
			$master_name = $r['value']['name'];
			return true;
		}
	}
	return false;
}

function GetTime($t, $h = true, $days_only = false){	
	$pinkdays = floor($t/86400);
	$pinkhours = floor(($t-$pinkdays*86400)/3600);
	$pinkmin = floor(($t-$pinkdays*86400-$pinkhours*3600)/60);
	$pinksec = round($t-$pinkdays*86400-$pinkhours*3600-$pinkmin*60,0);
	if ($pinkhours<10) $pinkhours='0'.$pinkhours;
	if ($pinkmin<10) $pinkmin='0'.$pinkmin;
	if ($pinksec<10) $pinksec='0'.$pinksec;
	$timeused = '';
	if ($pinkdays>0) {
		if ($h) $timeused='<font color="#00aa00"><b>'.$pinkdays.'</b></font> дн '; else	
			$timeused=$pinkdays.' дн ';
	}
	if (!$days_only)
		if (($pinkhours!="00")||($pinkmin!="00")||($pinksec!="00")) $timeused .= $pinkhours.':'.$pinkmin.':'.$pinksec;
	if ($timeused=='') $timeused = 0;
	return $timeused;
}

function getbaninfo($id,$check_only_ban_acc = true){
	global $ElementsVer, $db;
	$answ = array();
	//if ($ElementsVer <= 12)	{
		$answ['lastlogin'] = 0;
		$answ['lastip'] = '';
		$query = "select * from `forbid` WHERE userid = ".$id;
		$rresult = $db->query($query);
		if ($rresult==false) {
			MakeAnswer($answ, 1000, 'Ошибка запроса');
		}
		$answ['bancount'] = $rresult->num_rows;
		$answ['bans'] = array();
		if ($answ['bancount'] > 0) {			
			while ($row = mysqli_fetch_assoc($rresult)){
				$a = strpos($row['reason'], "\x00\x00");				
				if ($a > 0) $row['reason'] = substr($row['reason'], 0, $a+1);
				$ban = array(
					'type' => $row['type'],
					'createtime' => strtotime($row['ctime']),
					'time' => $row['forbid_time'],
					'reason' => @iconv("UTF-16","UTF-8",$row['reason'])
				);
				array_push($answ['bans'], $ban);
			}
		}		
	//} else {
	/*	$u = new GUser();
		if (isset($_POST['ip'])) $ip=$_POST['ip']; else $ip='127.0.0.1';
		$u->GetUser($id,$fp,$ip);
		if ($u->retcode!=0 || $u->error!=0) {
			if ($u->retcode == 60) MakeAnswer($answ, 1000, 'Сначала создайте персонажа в игре', 95);
			MakeAnswer($answ, 1000, 'Ошибка получения данных аккаунта '.$u->retcode.' - '.$u->error);
		}		
		$p = new PacketStream($u->login_record);
		$lasttime = $p->ReadInt32();
		$ip1 = $p->ReadByte(); $ip2 = $p->ReadByte(); $ip3 = $p->ReadByte(); $ip4 = $p->ReadByte();
		$ip = sprintf('%d.%d.%d.%d',$ip4,$ip3,$ip2,$ip1);
		$answ['lastlogin'] = $lasttime;
		$answ['lastip'] = $ip;	
		$answ['bancount'] = $u->forbid->count;
		$answ['bans'] = array();
		if ($answ['bancount'] > 0) {	
			foreach($u->forbid->forbids as $i => $val){
				$ban = array(
					'type' => $val->type,
					'createtime' => $val->createtime,
					'time' => $val->time,
					'reason' => $val->reason
				);
				array_push($answ['bans'], $ban);
			}
		}
	}*/
	// Проверка банов на истечение
	if ($answ['bancount'] > 0) {
		$c = false;
		foreach ($answ['bans'] as $i => $val){
			$c1 = false;
			$rest = $val['createtime'] + $val['time'] - time();
			if ($rest>0) {
				$c1 = true;
				if ($check_only_ban_acc && $val['type'] != 100) $c1 = false;
			}
			$c = $c || $c1;
		}
		if (!$c) $answ['bancount'] = 0;
	}
	return $answ;
}

function GetRegGold($id){
	global $db;
	if (!isset($_POST['zoneid']) || !isset($_POST['aid']) || !isset($_POST['ip']) || !isset($_POST['register_gold'])) die('10');
	$register_gold = intval($_POST['register_gold']);
	$aid = intval($_POST['aid']);
	$zoneid = intval($_POST['zoneid']);
	$ip = $_POST['ip'];
	$db->query('set names utf8');
	// Проверка на доступность выдачи голда
	$res = query2mysql('SELECT `bonus_data` FROM `users` WHERE `ID`='.$id);
	$row = mysqli_fetch_assoc($res);
	if ($row['bonus_data']=='') $bonus_data = array(); else $bonus_data = @unserialize($row['bonus_data']);
	if (!is_array($bonus_data)) $bonus_data = array();
	if (isset($bonus_data['reg_gold'])) die('99');
	$result = UseCash($id, $zoneid, $aid, $register_gold*100);	
	$bonus_data['reg_gold'] = 1;
	AddLKLogs($id, $ip, 0, 0, 'Получение '.$register_gold.' стартового голда');
	$res = $db->query("UPDATE `users` SET `bonus_data`='".$db->real_escape_string(serialize($bonus_data))."' WHERE `ID`=".$id);
	die('16');
}

function checkban($id){
	global $db;
	$answ = getbaninfo($id, false);
	$db->query('set names utf8');
	extract($_POST);
	$mmotop1_item = unserialize($mmotop1_item); $mmotop2_item = unserialize($mmotop2_item); $mmotop3_item = unserialize($mmotop3_item); $mmotop4_item = unserialize($mmotop4_item);
	$qtop1_item = unserialize($qtop1_item); $qtop2_item = unserialize($qtop2_item);
	if ($mmotop1_item['id'] > 0) {
		$item = GetExtItem(intval($mmotop1_item['id']));
		$answ['mmotop1_itemname'] = $item->name;
		$answ['mmotop1_itemicon'] = $item->icon;
	} else $answ['mmotop1_itemname'] = '';
	if ($mmotop2_item['id'] > 0) {
		$item = GetExtItem(intval($mmotop2_item['id']));
		$answ['mmotop2_itemname'] = $item->name;
		$answ['mmotop2_itemicon'] = $item->icon;
	} else $answ['mmotop2_itemname'] = '';
	if ($mmotop3_item['id'] > 0) {
		$item = GetExtItem(intval($mmotop3_item['id']));
		$answ['mmotop3_itemname'] = $item->name;
		$answ['mmotop3_itemicon'] = $item->icon;
	} else $answ['mmotop3_itemname'] = '';
	if ($mmotop4_item['id'] > 0) {
		$item = GetExtItem(intval($mmotop4_item['id']));
		$answ['mmotop4_itemname'] = $item->name;
		$answ['mmotop4_itemicon'] = $item->icon;
	} else $answ['mmotop4_itemname'] = '';
	if ($qtop1_item['id'] > 0) {
		$item = GetExtItem(intval($qtop1_item['id']));
		$answ['qtop1_itemname'] = $item->name;
		$answ['qtop1_itemicon'] = $item->icon;
	} else $answ['qtop1_itemname'] = '';
	if ($qtop2_item['id'] > 0) {
		$item = GetExtItem(intval($qtop2_item['id']));
		$answ['qtop2_itemname'] = $item->name;
		$answ['qtop2_itemicon'] = $item->icon;
	} else $answ['qtop2_itemname'] = '';
	$answ['roles_count'] = 0;
	$f = GetUserRoles($id, 1);
	if ($f['retcode'] != 0) MakeAnswerErrGetRoles($answ);
	$answ['roles_count'] = count($f['data']);
	$answ['roles'] = array();
	if ($answ['roles_count']) {
		foreach ($f['data'] as $i => $val){
			$role = array(
				'index' => $i,
				'name' => $val['name']
			);
			array_push($answ['roles'], $role);		
		}
	}
	// Проверка на доступность выдачи голда
	$res = $db->query('SELECT `bonus_data` FROM `users` WHERE `ID`='.$id);
	if ($db->errno > 0) MakeAnswer($answ, 1000, $db->error);
	$row = mysqli_fetch_assoc($res);
	if ($row['bonus_data']=='') $bonus_data = array(); else $bonus_data = @unserialize($row['bonus_data']);
	if (!is_array($bonus_data)) $bonus_data = array();
	$answ['allow_reg_gold'] = (!isset($bonus_data['reg_gold']));
	$answ['cur_reward_role_index'] = -1;
	if (isset($bonus_data['cur_reward_role_index'])) $answ['cur_reward_role_index'] = $bonus_data['cur_reward_role_index'];
	if ($answ['cur_reward_role_index'] >= $answ['roles_count']) $answ['cur_reward_role_index'] = -1;
	if ($answ['allow_reg_gold']){
		// Проверяем наличие персонажей
		$answ['roles_count'] = count($f['data']);
	}	
	$res = $db->query('SELECT * FROM `users` WHERE `referal`='.$id);
	if ($db->errno > 0) MakeAnswer($answ, 1000, $db->error);	
	$answ['refcount'] = $res->num_rows;
	$answ['refdata'] = array();
	if ($answ['refcount'] == 0) MakeAnswer($answ);
	while ($row = mysqli_fetch_assoc($res)){
		$refdata = array(
			'id' => ($row['ID']/16)-1,
			'creatime' => $row['creatime'],
			'ref_status' => $row['ref_status'],
			'ref_bonus' => $row['ref_bonus']
		);
		array_push($answ['refdata'], $refdata);
	}		
	MakeAnswer($answ);
}

function CheckAntibrutIP(){
	global $db;
	$query = sprintf("SELECT * FROM `antibrut` WHERE `ip`='%s'", $db->real_escape_string($_POST['ip']));
	$result = $db->query($query);
	if ($result->num_rows) {
		$row = mysqli_fetch_assoc($result);
		$plusfail = 0;
		if (($row['last_date_fail']+3)>=time()) $plusfail = 1;
		$db->query(sprintf("UPDATE `antibrut` SET `fail_count`=`fail_count`+%d, `last_date_fail`=%d WHERE `ip`='%s'", $plusfail, time(), $db->real_escape_string($_POST['ip'])));
	} else
		$db->query(sprintf("INSERT INTO `antibrut` (`ip`, `last_date_fail`, `fail_count`) VALUES ('%s', %d, %d)", $db->real_escape_string($_POST['ip']), time(), 1));
}

function GetExtItem($id){
	global $db;
	$a=new ExtItem();
	$a->name = 'Неправильный предмет';
	$a->list = 0;
	$a->icon = 'unknown.dds';
	$res = $db->query('SELECT * FROM `shop_names` WHERE `id`='.$id);	
	if ($db->errno>0) return $a;
	if ($res->num_rows==0) return $a;
	$row = mysqli_fetch_assoc($res);
	$a->name = $row['name'];
	$a->icon = $row['icon'];
	$a->list = $row['list'];
	return $a;
}

function GetItemInfo($data, $name, $list){
	global $gender;
	$s='';
	return $s;
}

function shopheaders($id){	
	global $db;
	$answ = getbaninfo($id);	
	if ($answ['bancount'] > 0) MakeAnswer($answ, 81);
	$base_err_txt = 'Ошибка запроса к базе данных.';
	$f = GetUserRoles($id, 1);
	if ($f['retcode'] != 0) MakeAnswerErrGetRoles($answ);
	$answ['roles_count'] = count($f['data']);
	$answ['roles'] = array();
	if (count($f['data']) == 0) MakeAnswerErrRolesCount($answ);
	foreach ($f['data'] as $i => $val){
		$role = array(
			'index' => $i,
			'name' => $val['name']
		);
		array_push($answ['roles'], $role);		
	}
	$db->query("SET NAMES utf8");
	$num = $_POST['num'];
	$page = (isset($_POST['page']))?intval($_POST['page']):1;
	$subcat = (isset($_POST['subcat']))?intval($_POST['subcat']):'';
	$query = 'select * from `shop_cat` order by `id`';
	$result = $db->query($query);
	if ($db->errno) MakeAnswer($answ, 1000, $base_err_txt);
	$catcount = $result->num_rows;
	$answ['cat'] = array();
	$answ['subcat'] = array();
	$answ['items'] = array();
	if ($catcount) {
		while ($row = mysqli_fetch_assoc($result)){
			$cat = array(
				'id' => $row['id'],
				'name' => $row['name']
			);
			array_push($answ['cat'], $cat);
		}
	}
	$query = 'select * from `shop_subcat` where catid='.$page.' order by id';
	$result = $db->query($query);
	if ($db->errno) MakeAnswer($answ, 1000, $base_err_txt);
	$subcatcount = $result->num_rows;
	if ($subcatcount) {
		while ($row = mysqli_fetch_assoc($result)){
			if ($subcat == '') $subcat = $row['id'];
			$subcatadd = array(
				'id' => $row['id'],
				'name' => $row['name']
			);
			array_push($answ['subcat'], $subcatadd);
		}
	}
	$query = 'select * from `shop_items` where subcat='.$subcat.' order by id';	
	$result = $db->query($query);
	if ($db->errno) MakeAnswer($answ, 1000, $base_err_txt);
	$itcount = $result->num_rows;
	if ($itcount) {
		while ($row = mysqli_fetch_assoc($result)){
			$it = GetExtItem($row['itemid']);
			$desc = GetItemInfo($row['data'], $it->name, $it->list).'<font color="#ffffff">'.$row['desc'].'</font>';
			$item = array(
				'id' => $row['id'],
				'name' => $it->name,
				'icon' => $it->icon,
				'itemid' => $row['itemid'],
				'count' => $row['count'],
				'maxcount' => $row['maxcount'],
				'data' => $row['data'],
				'client_size' => $row['client_size'],
				'proctype' => $row['proctype'],
				'subcat' => $row['subcat'],
				'cost_timeless' => $row['cost_timeless'],
				'cost_expire' => $row['cost_expire'],
				'expire' => $row['expire'],
				'discount_data' => $row['discount_data'],
				'desc' => $desc,
				'clean_desc' => $row['desc'],
				'rest' => $row['rest'],
				'buycount' => $row['buycount']
			);
			array_push($answ['items'], $item);
		}
	}
	MakeAnswer($answ);	
}

function make_auth_res($row, $login, $vk_id='', $vk_name='', $vk_photo='', $steam_id='', $steam_name='', $steam_photo=''){
	global $db, $act_key, $rest_time, $is_timed;
	$res = array();
	$res['name'] = $row['name'];
	$res['pwd_hash'] = base64_encode($row['passwd']);
	$res['lkgold'] = $row['lkgold'];
	$res['lksilver'] = $row['lksilver'];
	$res['id'] = $row['ID'];
	$res['rules_cnt'] = $row['rules_cnt'];				
	$res['acc_email'] = $row['email'];
	$res['ipdata'] = @unserialize($row['ipdata']);
	$res['session_data'] = $row['session_data'];
	$res['vk_id'] = $row['vkid'];
	$res['steam_id'] = $row['steamid'];	
	if (isset($_POST['session'])) $res['session_data'] = $_POST['session'];
	if (!is_array($res['ipdata'])) {
		$res['ipdata'] = array();
		$res['ipdata'][0] = false;
		$res['ipdata'][1] = array();	
		$res['ipdata'][2] = false;		
	}
	if (!isset($res['ipdata'][2])) $res['ipdata'][2] = false;
	$db->query('set names utf8');
	if ($login) {
		if (isset($_POST['session'])) {
			@$db->query(sprintf("UPDATE `users` SET `session_data`='%s' WHERE `name` = '%s'", $db->real_escape_string($_POST['session']), $login));
		}
		$sql = "SELECT `Prompt`, `answer`, `vkname`, `vkphoto`, `steamname`, `steamphoto` FROM `users` WHERE `name` = '$login'";
	} else 
	if ($vk_id) {
		if (isset($_POST['session'])) {
			@$db->query(sprintf("UPDATE `users` SET `session_data`='%s', `vkname`='%s', `vkphoto`='%s' WHERE `vkid` = '%s'", $db->real_escape_string($_POST['session']), $vk_name, $vk_photo, $vk_id));
		}
		$sql = "SELECT `Prompt`, `answer`, `vkname`, `vkphoto`, `steamname`, `steamphoto` FROM `users` WHERE `vkid` = '$vk_id'";
	} else {
		if (isset($_POST['session'])) {
			@$db->query(sprintf("UPDATE `users` SET `session_data`='%s', `steamname`='%s', `steamphoto`='%s' WHERE `steamid` = '%s'", $db->real_escape_string($_POST['session']), $steam_name, $steam_photo, $steam_id));
		}
		$sql = "SELECT `Prompt`, `answer`, `vkname`, `vkphoto`, `steamname`, `steamphoto` FROM `users` WHERE `steamid` = '$steam_id'";
	}
	if ($rest_time < 0) $rest_time = 0;
	$result = $db->query($sql);
	$row = mysqli_fetch_assoc($result);
	$res['question'] = $row['Prompt'];
	$res['answer'] = $row['answer'];
	$res['vk_name'] = $row['vkname'];
	$res['vk_photo'] = $row['vkphoto'];
	$res['steam_name'] = $row['steamname'];
	$res['steam_photo'] = $row['steamphoto'];
	$res['is_timed'] = $is_timed;
	$res['rest_time'] = GetTime($rest_time, false, true);
	$res['script'] = $_SERVER['SCRIPT_FILENAME'];
	$res['act_key'] = $act_key;
	$result = $db->query("SELECT * FROM `lklogs` WHERE `userid` = 0");
	$res['notice'] = array();
	while ($row = mysqli_fetch_assoc($result)) {
		$ar = array($row['gold'], $row['desc']);
		array_push($res['notice'], $ar);
	}
	$res = serialize($res);
	die($res);
}

function AddVK(){
	global $db;
	if (!$db->query(sprintf("DELETE FROM `antibrut` WHERE `last_date_fail`<=%d", time()-600))) die('6'); // Чистка таблицы антибрута
	$query = sprintf("SELECT * FROM `antibrut` WHERE `ip`='%s'", $db->real_escape_string($_POST['ip']));
	$result = $db->query($query);
	if ($result->num_rows) {		
		$row = mysqli_fetch_assoc($result);
		if (($row['fail_count'])>=5) die('7');
	}
	CheckAntibrutIP();
	if ( !isset($_POST['vk_id']) || !isset($_POST['vk_name']) || !isset($_POST['id'])) die('10');
	$id = intval($_POST['id']);
	$vk_id = trim($db->real_escape_string($_POST['vk_id']));
	$vk_name = $db->real_escape_string($_POST['vk_name']);
	$vk_photo = $db->real_escape_string($_POST['vk_photo']);
	$query = sprintf("SELECT `ID` FROM `users` WHERE `vkid` = '%s'", $vk_id);		
	$result = query2mysql($query);
	if ($result->num_rows) {
		die('109');
	}
	$db->query('set names utf8');
	$result = query2mysql(sprintf("UPDATE `users` SET `vkname`='%s', `vkphoto`='%s', `vkid` = '%s' WHERE `ID`=%s", $vk_name, $vk_photo, $vk_id, $id));
	die('16');
}

function AddSteam(){
	global $db;
	if (!$db->query(sprintf("DELETE FROM `antibrut` WHERE `last_date_fail`<=%d", time()-600))) die('6'); // Чистка таблицы антибрута
	$query = sprintf("SELECT * FROM `antibrut` WHERE `ip`='%s'", $db->real_escape_string($_POST['ip']));
	$result = $db->query($query);
	if ($result->num_rows) {		
		$row = mysqli_fetch_assoc($result);
		if (($row['fail_count'])>=5) die('7');
	}
	CheckAntibrutIP();
	if ( !isset($_POST['steam_id']) || !isset($_POST['steam_name']) || !isset($_POST['id'])) die('10');
	$id = intval($_POST['id']);
	$steam_id = trim($db->real_escape_string($_POST['steam_id']));
	$steam_name = $db->real_escape_string($_POST['steam_name']);
	$steam_photo = $db->real_escape_string($_POST['steam_photo']);
	$query = sprintf("SELECT `ID` FROM `users` WHERE `steamid` = '%s'", $steam_id);		
	$result = query2mysql($query);
	if ($result->num_rows) {
		die('110');
	}
	$db->query('set names utf8');
	$result = query2mysql(sprintf("UPDATE `users` SET `steamname`='%s', `steamphoto`='%s', `steamid` = '%s' WHERE `ID`=%s", $steam_name, $steam_photo, $steam_id, $id));
	die('16');
}

function vkauth(){
	global $db;
	if (!$db->query(sprintf("DELETE FROM `antibrut` WHERE `last_date_fail`<=%d", time()-600))) die('6'); // Чистка таблицы антибрута
	$query = sprintf("SELECT * FROM `antibrut` WHERE `ip`='%s'", $db->real_escape_string($_POST['ip']));
	$result = $db->query($query);
	if ($result->num_rows) {		
		$row = mysqli_fetch_assoc($result);
		if (($row['fail_count'])>=5) die('7');
	}
	if ( !isset($_POST['vk_id']) || !isset($_POST['vk_name'])) die('nodata');
	$vk_id = trim($db->real_escape_string($_POST['vk_id']));
	$vk_name = $db->real_escape_string($_POST['vk_name']);
	$vk_photo = $db->real_escape_string($_POST['vk_photo']);
$query = sprintf("SELECT `users`.*, count(`auth`.`rid`) AS `rules_cnt` FROM users LEFT JOIN `auth` ON `auth`.`userid`=`users`.`ID` WHERE `vkid` = '%s'", $vk_id);	
	$result = $db->query($query);
	if (!$result) die('6'); // Ошибка запроса		
	if ($result->num_rows) {
		$row = mysqli_fetch_assoc($result);		
		if ($row['vkid'] != $vk_id) { 	// Нет аккаунта с таким vkid
			CheckAntibrutIP();
			die('15');
		}
		// Проверка на наличие неактивированного аккаунта
		$query = sprintf("SELECT `ID` FROM `users` WHERE `vkid` = '%s' AND `passwd`='confirm'", $vk_id);		
		$result = $db->query($query);
		if (!$result) die('6'); // Ошибка запроса	
		if ($result->num_rows) {
			die('14');
		}
		make_auth_res($row, '', $vk_id, $vk_name, $vk_photo);
	} else {
		CheckAntibrutIP();
		die('0');	// Неверный логин/пароль		
	}
}

function steamauth(){
	global $db;
	if (!$db->query(sprintf("DELETE FROM `antibrut` WHERE `last_date_fail`<=%d", time()-600))) die('6'); // Чистка таблицы антибрута
	$query = sprintf("SELECT * FROM `antibrut` WHERE `ip`='%s'", $db->real_escape_string($_POST['ip']));
	$result = $db->query($query);
	if ($result->num_rows) {		
		$row = mysqli_fetch_assoc($result);
		if (($row['fail_count'])>=5) die('7');
	}
	if ( !isset($_POST['steam_id']) || !isset($_POST['steam_name'])) die('nodata');
	$steam_id = trim($db->real_escape_string($_POST['steam_id']));
	$steam_name = $db->real_escape_string($_POST['steam_name']);
	$steam_photo = $db->real_escape_string($_POST['steam_photo']);
$query = sprintf("SELECT `users`.*, count(`auth`.`rid`) AS `rules_cnt` FROM users LEFT JOIN `auth` ON `auth`.`userid`=`users`.`ID` WHERE `steamid` = '%s'", $steam_id);	
	$result = $db->query($query);
	if (!$result) die('6'); // Ошибка запроса		
	if ($result->num_rows) {
		$row = mysqli_fetch_assoc($result);		
		if ($row['steamid'] != $steam_id) { 	// Нет аккаунта с таким steamid
			CheckAntibrutIP();
			die('15');
		}
		// Проверка на наличие неактивированного аккаунта
		$query = sprintf("SELECT `ID` FROM `users` WHERE `steamid` = '%s' AND `passwd`='confirm'", $steam_id);		
		$result = $db->query($query);
		if (!$result) die('6'); // Ошибка запроса	
		if ($result->num_rows) {
			die('14');
		}
		make_auth_res($row, '', '', '', '', $steam_id, $steam_name, $steam_photo);
	} else {
		CheckAntibrutIP();
		die('0');	// Неверный логин/пароль		
	}
}

function auth(){
	global $db;
	// Проверка на брут
	if (!$db->query(sprintf("DELETE FROM `antibrut` WHERE `last_date_fail`<=%d", time()-600))) die('6'); // Чистка таблицы антибрута
	$query = sprintf("SELECT * FROM `antibrut` WHERE `ip`='%s'", $db->real_escape_string($_POST['ip']));
	$result = $db->query($query);
	if ($result->num_rows) {		
		$row = mysqli_fetch_assoc($result);
		if (($row['fail_count'])>=5) die('7');
	}
	if ( !isset($_POST['login']) || !isset($_POST['passw'])) die('nodata');
	$login = strtolower($db->real_escape_string($_POST['login']));
	$pass = base64_decode($_POST['passw']);
	$query = sprintf("SELECT `users`.*, count(`auth`.`rid`) AS `rules_cnt` FROM users LEFT JOIN `auth` ON `auth`.`userid`=`users`.`ID` WHERE `name` = '%s' AND `passwd`='%s'", $db->real_escape_string($login), $db->real_escape_string($pass));
	if (isset($_POST['email'])) $query.=sprintf(" AND `email`='%s'", $db->real_escape_string($_POST['email']));
	$result = $db->query($query);
	if (!$result) die('6'); // Ошибка запроса		
	if ($result->num_rows) {
		$row = mysqli_fetch_assoc($result);		
		if ($row['name']!=$login) { 	// Неверный логин/пароль
			// Проверка на наличие неактивированного аккаунта
			$query = sprintf("SELECT `ID` FROM users WHERE `name` = '%s' AND `passwd2`='%s' AND `passwd`='confirm'", $db->real_escape_string($login), $db->real_escape_string($pass));		
			if (isset($_POST['email'])) $query.=sprintf(" AND `email`='%s'", $db->real_escape_string($_POST['email']));		
			$result = $db->query($query);
			if (!$result) die('6'); // Ошибка запроса	
			if ($result->num_rows) {
				die('14');
			} else {
				CheckAntibrutIP();
				die('0');
			}
		}
		make_auth_res($row, $login);
	} else {
		CheckAntibrutIP();
		die('0');	// Неверный логин/пароль		
	}
}

function persklan(){
	global $db;
	if (!isset($_POST['id'])) die();
	$id = intval($_POST['id']);
	$answ = getbaninfo($id);
	if ($answ['bancount'] > 0) MakeAnswer($answ, 81);
	$f = GetUserRoles($id, 1);
	if ($f['retcode'] != 0) MakeAnswerErrGetRoles($answ);	
	$answ['roles_count'] = 0;
	$answ['roles'] = array();
	$answ['factions'] = array();
	if (count($f['data']) == 0) MakeAnswer($answ);	
	$db->query("SET NAMES utf8");	
	foreach ($f['data'] as $i => $val){
		$r = GetRole($val['id'], 63);		
		if ($r['value']['factionid'] > 0) {
			$sql = 'select * from `klan_items` where klanid='.$r['value']['factionid'];
			$res = $db->query($sql);
			if ($db->errno != 0) MakeAnswer($answ, 1000, 'Ошибка запроса к базе данных (получение списка клан артов)');
			if ($r['value']['title'] == 2){
				$answ['roles_count']++;
				$klanitems = array();				
				if ($res->num_rows>0)
				while ($row = mysqli_fetch_assoc($res)){
					$it = GetExtItem($row['itemid']);
					$it1 = GetExtItem($row['cost_item_id']);
					$klanit = array(
						'id' => $row['id'],
						'itemid' => $row['cost_item_id'],
						'count' => $row['count'],
						'name' => $it->name,
						'icon' => $it->icon,
						'costgold' => $row['costgold'],
						'costsilver' => $row['costsilver'],
						'cost_item_count' => $row['cost_item_count'],
						'cost_name' => $it1->name,
						'cost_icon' => $it1->icon
					);
					array_push($klanitems, $klanit);
				}
				$fact = GetFactionInfo($r['value']['factionid']);
				$role = array(
					'factionid' => $r['value']['factionid'],
					'factionname' => $fact['value']['name'],
					'rolename' => $val['name'],
					'roleindex' => $i,
					'klanitems' => $klanitems
				);
				array_push($answ['roles'], $role);
			} else {
				if ($res->num_rows > 0) {
					$art = array();
					$fact = GetFactionInfo($r['value']['factionid']);
					$art['faction_name'] = $fact['value']['name'];
					$art['items'] = array();				
					if ($res->num_rows > 0)
					while ($row = mysqli_fetch_assoc($res)){
						$it = GetExtItem($row['itemid']);
						$it1 = GetExtItem($row['cost_item_id']);
						$art_i['item'] = $it->name;					
						$klanit = array(
							'item' => $it->name,
							'icon' => $it->icon,
							'count' => $row['count'],
							'costgold' => $row['costgold'],
							'costsilver' => $row['costsilver'],
							'cost_item_count' => $row['cost_item_count'],
							'cost_name' => $it1->name,
							'cost_icon' => $it1->icon
						);
						array_push($art['items'], $klanit);
					}
					array_push($answ['factions'], $art);
				}
			}
		}
	}
	MakeAnswer($answ);
}

function persuah(){
	global $db;	
	if (!isset($_POST['id']) || !isset($_POST['allow_lk_gold_exchange']) || !isset($_POST['gold_itemid']) || !isset($_POST['gold_item_exchange_rate']) || !isset($_POST['allow_lk_silver_exchange']) || !isset($_POST['silver_itemid']) || !isset($_POST['silver_item_exchange_rate'])) die();
	extract($_POST);
	$id = intval($_POST['id']);	
	$answ = getbaninfo($id);
	$answ['roles'] = array();
	$f = GetUserRoles($id, 1);
	if ($f['retcode'] != 0) MakeAnswerErrGetRoles($answ);
	if (!count($f['data'])) MakeAnswerErrRolesCount($answ);
	if ($answ['bancount'] > 0 || (!$_POST['allow_lk_gold_exchange'] && !$_POST['allow_lk_silver_exchange'])) MakeAnswer($answ);
	$gold_itemid = intval($_POST['gold_itemid']); $silver_itemid = intval($_POST['silver_itemid']);
	$gold_item_exchange_rate = $_POST['gold_item_exchange_rate']; $silver_item_exchange_rate = $_POST['silver_item_exchange_rate'];
	$db->query('set names utf8');
	if ($_POST['allow_lk_gold_exchange']) {
		$it = GetExtItem($gold_itemid); 
		$answ['gold_item_name'] = $it->name;
	}
	if ($_POST['allow_lk_silver_exchange']) {
		$it = GetExtItem($silver_itemid); 
		$answ['silver_item_name'] = $it->name;
	}
	if ($gold_item_exchange_rate < 1) $gold_item_exchange_rate = 1;
	if ($silver_item_exchange_rate < 1) $silver_item_exchange_rate = 1;
	foreach ($f['data'] as $i => $val){
		if ($_POST['allow_lk_gold_exchange']) $cnt = finditem($val['id'], $gold_itemid); else $cnt = 0;
		if ($_POST['allow_lk_silver_exchange']) $cnt1 = finditem($val['id'] ,$silver_itemid); else $cnt1 = 0;
		if ( $cnt >= $gold_item_exchange_rate || $cnt1 >= $silver_item_exchange_rate ){
			$role = array(
				'index' => $i,
				'name' => $val['name'],				
				'gold_item_cnt' => $cnt,
				'silver_item_cnt' => $cnt1
			);	
			array_push($answ['roles'], $role);
		}
	}
	MakeAnswer($answ);
}

function pers(){
	global $db;	
	if (!isset($_POST['id'])) die();
	$id = intval($_POST['id']);	
	$answ = getbaninfo($id);	
	$f = GetUserRoles($id, 1);
	if ($f['retcode'] != 0) MakeAnswerErrGetRoles($answ);
	if (!@AssignData(ak)) die('13');
	$answ['roles_count'] = count($f['roles']);
	$answ['roles'] = array();
	$k = array('value' => array('fid'=>0,'name'=>''));
	if (count($f['roles']) == 0) MakeAnswer($answ);	
	$db->query("SET NAMES utf8");	
	foreach ($f['data'] as $i => $val){
		$b = GetRoleBase($val['id']);
		if ($b['retcode'] != 0) MakeAnswer($answ, 1000, 'Ошибка получения базы персонажа '.$val['name']);
		$r = GetRole($val['id'], 63);
		if ($r['retcode'] == 0){
			if ($k['value']['fid'] != $r['value']['factionid'] && $r['value']['factionid'] != 0) $k = GetFactionInfo($r['value']['factionid']);
			$forbids = array();
			if (count($b['value']['forbid']) > 0) {
				$cnt=0;
				foreach ($b['value']['forbid'] as $ii => $val1){
					if ($val1['type'] != 0 && $val1['reason'] != 'Сохранение персонажа'){
						$forbid = array(
							'type' => $val1['type'],
							'time' => $val1['time'],
							'createtime' => $val1['createtime'],
							'reason' => $val1['reason']
						);
						array_push($forbids, $forbid);
					}				
				}						
			}			
			$role = array(
				'index' => $i,
				'name' => $val['name'],
				'factionid' => $r['value']['factionid'],
				'factionname' => $k['value']['name'],
				'factionrole' => $r['value']['title'],
				'time_used' => $r['value']['status']['time_used'],
				'occupation' => $r['value']['status']['occupation'],
				'gender' => $r['value']['gender'],
				'level' => $r['value']['status']['level'],
				'forbids' => $forbids,
				'worldtag' => $r['value']['status']['worldtag'],
				'storehousepasswd' => $r['value']['status']['storehousepasswd'] != ''
			);			
		} else {
			// Ошибка получения персонажа
			$role = array(
				'index' => $i,
				'name' => 'Ошибка получения персонажа',
				'factionid' => 0,
				'factionname' => '',
				'factionrole' => 0,
				'time_used' => 0,
				'occupation' => 0,
				'gender' => 0,
				'level' => 0,
				'forbids' => array(),
				'worldtag' => 1,
				'storehousepasswd' => false
			);
		}
		array_push($answ['roles'], $role);		
	}
	if ($answ['bancount'] > 0) MakeAnswer($answ, 81);	
	MakeAnswer($answ);	
}

function CheckCostPoints($nam, $err1, $id){
	global $gpoint, $spoint;
	if (!isset($_POST[$nam])) die($err1);
	$cost = $_POST[$nam];			
	ShowCost($cost);
	if ($gpoint > 0 || $spoint > 0)
		CheckPoints($id, $gpoint, $spoint);	// Проверяем есть ли на аккаунте поинты
}

function getFormattedKey($key) {
	$num1 = substr($key, 0, 4);
	$num2 = substr($key, 4, 4);
	$num3 = substr($key, 8, 4);
	$num4 = substr($key, 12, 4);
	return $num1 . "-" . $num2 . "-" . $num3 . "-" . $num4;
}

function GenerateCode($length = 16, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'){
	$chars_length = (strlen($chars) - 1);
	$string = $chars{rand(0, $chars_length)};    
	for ($i = 1; $i < $length; $i = strlen($string))  {
        	$r = $chars{rand(0, $chars_length)};        
        	//if ($r != $string{$i - 1}) 
		$string .=  $r;
    	}    
    	return getFormattedKey($string);
}

function AdminGenPromo(){
	global $db;		
	if (!isset($_POST['promo_count']) || !isset($_POST['promo_expire']) || !isset($_POST['promo_group']) || !isset($_POST['promo_gold']) || !isset($_POST['promo_silver']) || !isset($_POST['promo_item_id']) || !isset($_POST['promo_item_count']) || !isset($_POST['promo_item_maxcount']) || !isset($_POST['promo_item_data']) || !isset($_POST['promo_item_client_size']) || !isset($_POST['promo_item_proctype']) || !isset($_POST['promo_item_expire']) || !isset($_POST['promo_desc'])) die('Input data error');
	$db->query('set names utf8');
	extract($_POST);	
	if (isset($promo_multi_user)) $promo_multi_user = 1; else $promo_multi_user = 0;
	if ($promo_count < 1) die('Количество генерируемых кодов должно быть не менее 1');
	if ($promo_count > 10000) die('Количество генерируемых кодов должно быть не более 10000 за раз');
	set_time_limit(round($promo_count/10));
	if ($promo_expire > 0) $promo_expire = time() + $promo_expire;
	$f = fopen(sprintf('%s_%d-promo_codes.txt', @date('Y-m-d_H-i-s', time()), $promo_count), 'w');
	if (!$f) die('Error create file with promo codes');
	for ($a = 0; $a < $promo_count; $a++){
		$res = false; $q = 0;
		while (!$res && $q < 20) {			
			$code = GenerateCode();
			$sql = sprintf("INSERT INTO `promo_codes` (`code`,`expire`,`group`,`bonus_money_gold`,`bonus_money_silver`, `bonus_item_id`, `bonus_item_count`, `bonus_item_max_count`, `bonus_item_data`, `bonus_item_client_size`, `bonus_item_proctype`, `bonus_item_expire`, `multi_user`, `used_userid`, `desc`) VALUES ('%s', %d, %d, %d, %d, %d, %d, %d, '%s', %d, %d, %d, %d, 0, '%s')", $code, $promo_expire, $promo_group, $promo_gold, $promo_silver, $promo_item_id, $promo_item_count, $promo_item_maxcount, $db->real_escape_string($promo_item_data), $promo_item_client_size, $promo_item_proctype, $promo_item_expire, $promo_multi_user, $db->real_escape_string($promo_desc));
			$res = $db->query($sql);
			//if (!$res) echo $db->error;
			//echo GenerateCode().'<br>';
			$q++;
		}
		fwrite($f, $code."\n");
	}
	fclose($f);
	die('ok');
}

function act($id,$n,$num,$ip){	
	global $gpoint, $spoint, $db, $factrole;
	$err1='10';//"Ошибка входящих данных";
	$err2='13';//"Ошибка получения персонажа";
	$err3='14';//"Ошибка записи персонажа";	
	if (($n!=7)&&($n!=10)&&($n!=11)&&($n!=17)&&($n!=18)&&($n!=19)&&($n!=20)&&($n!=53)&&($n!=54)&&($n!=55)&&($n!=56)&&($n!=57)&&($n!=58)&&($n!=59)&&($n!=60)&&($n!=61)&&($n!=62)&&($n!=69)&&($n!=70)&&($n!=71)&&($n!=72)&&($n!=73)&&($n!=77)&&($n!=78)&&($n!=79)&&($n!=80)) {
		$f = GetUserRoles($id, 1);
		if ($f['retcode'] != 0) die('12');
		if (count($f['data']) < $num+1) die($err1); // Проверяем номер персонажа
		$cur_role_name = $f['data'][$num]['name'];
	}
	if (($n!=8)&&($n!=10)&&($n!=11)&&($n!=12)&&($n!=3)&&($n!=13)&&($n!=7)&&($n!=17)&&($n!=18)&&($n!=19)&&($n!=20)&&($n!=21)&&($n!=23)&&($n!=53)&&($n!=54)&&($n!=55)&&($n!=56)&&($n!=57)&&($n!=58)&&($n!=59)&&($n!=60)&&($n!=61)&&($n!=62)&&($n!=63)&&($n!=69)&&($n!=70)&&($n!=71)&&($n!=72)&&($n!=73)&&($n!=77)&&($n!=78)&&($n!=79)&&($n!=80)&&($n!=82)&&($n!=84)) {
		isonline($id, $f['data'][$num]['id']);		// Проверка оффлайна аккаунта
	}
	if ($n!=73&&$n!=77) {
		$answ = getbaninfo($id);
		if ($answ['bancount'] > 0) die('81');//MakeAnswer($answ, 81);
	}
	$gpoint = 0; $spoint = 0;
	switch ($n)
	{

		case 800: // Выбор перса для отправки наград за голосование в топах
			$db->query("SET NAMES utf8");
			$res = $db->query('SELECT `bonus_data` FROM `users` WHERE `ID`='.$id);
			if ($db->errno > 0) die('15');
			$row = mysqli_fetch_assoc($res);
			if ($row['bonus_data']=='') $bonus_data = array(); else $bonus_data = @unserialize($row['bonus_data']);
			if (!is_array($bonus_data)) $bonus_data = array();						
			$bonus_data['cur_reward_role_index'] = $num;
			$res = $db->query("UPDATE `users` SET `bonus_data`='".$db->real_escape_string(serialize($bonus_data))."' WHERE `ID`=".$id);
			if ($db->errno > 0) die('15');
			die('16');
			
		break;

		case 2: 				// Убираем пароль банка
			CheckCostPoints('nullbankpass', $err1, $id);
			$rd = GetRoleData($f['data'][$num]['id']);
			if ($rd['retcode'] !=0 || $rd['packet_check_error'] != 0) die($err2);
			if ($rd['value']['status']['storehousepasswd'] == '') die('45'); // нет пароля на банке
			$rd['value']['status']['storehousepasswd'] = '';
			$rd = PutRoleData($f['data'][$num]['id'], $rd['value']);
			if ($rd['retcode'] != 0) die($err3);
			$desc='Удаление пароля банка персонажа '.$f['data'][$num]['name'];
		break;

		case 5: 				// Обмен на голд монеты			
			if ( !isset($_POST['i']) || CheckNum($_POST['i']) || !isset($_POST['n']) || !isset($_POST['num']) || !isset($_POST['allow_lk_gold_exchange']) || !isset($_POST['gold_itemid']) || !isset($_POST['gold_item_exchange_rate'])) die($err1);
			extract($_POST);
			$gpoint=-$i;
			$spoint=0;
			$cnt = finditem($f['data'][$num]['id'], $gold_itemid);
			if ($cnt < ($i*$gold_item_exchange_rate) ) die('49');		
			if (takeitem($f['data'][$num]['id'], $gold_itemid, $i*$gold_item_exchange_rate) != true) die('50');
			$db->query('set names utf8');
			$it = GetExtItem($gold_itemid);
			$desc = sprintf('%s: обмен %d %s на %s голд монет', $f['data'][$num]['name'], $i*$gold_item_exchange_rate, $it->name, $i);
		break;

		case 6: 				// Обмен на серебрянные монеты
			if ( !isset($_POST['i']) || CheckNum($_POST['i']) || !isset($_POST['n']) || !isset($_POST['num']) || !isset($_POST['allow_lk_silver_exchange']) || !isset($_POST['silver_itemid']) || !isset($_POST['silver_item_exchange_rate'])) die($err1);
			extract($_POST);
			$gpoint=0;
			$spoint=-$i;
			$cnt = finditem($f['data'][$num]['id'], $silver_itemid);
			if ($cnt < ($i*$silver_item_exchange_rate) ) die('49');		
			if (takeitem($f['data'][$num]['id'], $silver_itemid, $i*$silver_item_exchange_rate) != true) die('50');
			$db->query('set names utf8');
			$it = GetExtItem($silver_itemid);
			$desc = sprintf('%s: обмен %d %s на %s серебрянных монет', $f['data'][$num]['name'], $i*$silver_item_exchange_rate, $it->name, $i);
		break;

		case 7:					// Установка значка клана
			CheckCostPoints('klancost', $err1, $id);
			global $master_name;
			$f = GetUserRoles($id, 1);
			if ($f['retcode'] != 0) die('12');
			if (!CheckKlanMaster($id, $num, $f)) die('7');			
			$servid = intval($_POST['servid']);
			if (!is_writable('klan/iconlist_guild.png') || !is_writable('klan/iconlist_guild.txt') || !is_writable('klan/version') || !is_writable('klan/.')) die('94');
			$r = KlanPic($num, $servid);
			if ($r=='ok') echo "8"; else die("9");
			$desc='Значок клана '.$num.', мастер '.$master_name;
		break;	

		case 8: 				// Покупка предмета	
		if (!isset($_POST['sitem']) || !isset($_POST['count']) || !isset($_POST['t']) || CheckNum($_POST['sitem']) || CheckNum($_POST['count'])) die($err1);		
		$sitem = $_POST['sitem']; $count = $_POST['count']; $t = $_POST['t'];		
		if ($count < 1) die($err1);
		$db->query("SET NAMES utf8");
		$query = "select * from `shop_items` WHERE id = ".$sitem;
		$rresult = query2mysql($query);		
		$row = mysqli_fetch_assoc($rresult);
		if ($row['rest']>=0)
			if (($row['rest']-$count) < 0) die('28');
		if ($t=='e') {			
			ShowCost($row['cost_expire']);
		} else 
		if ($t=='t') {
			if (($count*$row['count']) > $row['maxcount']) die($err1);
			ShowCost($row['cost_timeless']);
		} else die($err1);
		$gpoint*=$count;
		$spoint*=$count;
		$discount_data = @unserialize($row['discount_data']);
		if (!is_array($discount_data)) $discount_data = array();
		$curdiscount = 0;
		if (count($discount_data)>0){			
			foreach ($discount_data as $i1 => $val1){
				if ($count >= $i1) 
					$curdiscount = $val1;
			}			
		}
		$discount_txt = '';
		if ($curdiscount>0) {
			$dg = round($gpoint/100*$curdiscount);
			$ds = round($spoint/100*$curdiscount);
			$gpoint -= $dg;
			$spoint -= $ds;
			$discount_txt = ' (скидка '.$curdiscount.'%)';
		}		
		if (($gpoint==0)&&($spoint==0)) die($err1);
		CheckPoints($id,$gpoint,$spoint);	// Проверяем есть ли на аккаунте поинты
		$item = InitItem();
		$item['id'] = $row['itemid'];
		if ($t=='e') $item['count'] = $row['count']; else $item['count'] = $row['count']*$count;
		$item['max_count'] = $row['maxcount'];
		$item['data'] = $row['data'];
		$item['proctype'] = $row['proctype'];
		$expire = $row['expire'];
		if ($t=='e') $item['expire_date'] = time()+$expire*$count; else $item['expire_date'] = 0;
		$item['client_size'] = $row['client_size'];
		$q = SysSendMail(0, 32, 3, $f['data'][$num]['id'], 'Покупка из личного кабинета' ,'Приятной игры', $item, 0);
		if ($q['retcode'] == 217) die("51");
		$it = GetExtItem($row['itemid']);
		echo '29';
		if ($t=='t') $ddd=''; else $ddd=' на '.GetTime($expire*$count, false);
		$desc = $cur_role_name.': покупка '.$item['count'].' x '.$it->name.$ddd.$discount_txt;		
		if ($row['rest']>0){
			$query = "update `shop_items` set rest = rest-".$count." WHERE id = ".$sitem;
			$rresult = $db->query($query);
			if ($rresult==false) {
				//die("15");
			}
		}
		$query = "update `shop_items` set `buycount`=`buycount`+".$count." WHERE id = ".$sitem;
		$rresult = $db->query($query);
		if ($rresult==false) {
			//echo("Ошибка запроса<br>");
		}	
		break;	

		case 10:	// Перевод поинтов на другой акк
			if (isset($_POST['comment'])) $comment = $db->real_escape_string($_POST['comment']); else $comment = '';
			$lk_transfer_min_role_lvl = intval($_POST['lk_transfer_min_role_lvl']);
			$gpoint = intval($_POST['gold']);
			$spoint = 0;				
			$accid1 = intval($_POST['accid']);
			$accid = 16 + $accid1*16;
			if ($id == $accid) die('20'); // Самому себе
			if (!@AssignData(ak)) die('42');
			if ($lk_transfer_min_role_lvl) {
				if (!CheckRoleLvl($id, $lk_transfer_min_role_lvl)) die('111');
			}
			$sql = "SELECT * FROM `users` WHERE `id`=".$id;
			$result = query2mysql($sql, true);
			$row = mysqli_fetch_assoc($result);
			if (!$row['vkid'] && $_POST['lk_transfer_vk_only']) die('112');
			if (!$row['steamid'] && $_POST['lk_transfer_steam_only']) die('113');
			$sql = "SELECT * FROM `users` WHERE `id`=".$accid;
			$result = query2mysql($sql);
			if ($result->num_rows == 0) die('19');			
			CheckPoints($id,$gpoint,$spoint);	// Проверяем есть ли на аккаунте поинты
			$row = mysqli_fetch_assoc($result);			
			$recid = $row['ID'];
			$sacc = $id/16-1;
			GiveGold($recid, $gpoint, $spoint, "Перевод от аккаунта № $sacc $comment", 'Hidden');
			echo '21';
			$desc = 'Перевод монет на счет № '.$accid1." ".$comment;		
		break;

		case 11:	// Вывод голд поинтов в игру
			if (!isset($_POST['goldcount']) || !isset($_POST['lk2game_exchange_rate'])) die($err1);
			$lk2game_exchange_rate = $_POST['lk2game_exchange_rate'];
			$gpoint = $_POST['goldcount'];
			$spoint = 0;
			$zoneid = $_POST['zoneid'];
			$aid = $_POST['aid'];
			CheckPoints($id,$gpoint,$spoint);	// Проверяем есть ли на аккаунте поинты
			$result = UseCash($id, $zoneid, $aid, floor($gpoint*$lk2game_exchange_rate*100));			
			echo '44';
			$desc='Обмен '.$_POST['goldcount'].' ЛК на '.floor($gpoint*$lk2game_exchange_rate).' игрового голда';
		break;

		case 12: 				// Клан арты
			if (!isset($_POST['artid']) || CheckNum($_POST['artid'])) die($err1); else $artid = $_POST['artid'];
			$send_role_id = $f['data'][$num]['id'];	
			$send_txt_addon = '';		
			$db->query("SET NAMES utf8");
			$query = "select * from `klan_items` WHERE id = ".$artid;
			$rresult = query2mysql($query);		
			if ($rresult->num_rows > 0){
				$row = mysqli_fetch_assoc($rresult);
				if (!CheckKlanMaster($id, $row['klanid'], $f)) die('76');
				if ($_POST['name'] != '')
				{
					$name = $_POST['name'];
					$f1 = GetRoleId($name);
					if ($f1['retcode'] == 0 && $f1['roleid'] > 0)
					{
						$rid = $f1['roleid'];
						$role = GetRole($rid, 63);
						if ($role['retcode'] != 0) die('13');
						if ($role['value']['factionid'] != $row['klanid']) die('115');
						$send_role_id = $rid;
						$send_txt_addon = ' для '.$role['value']['name'];
					} else die('114');
				}
				if ($row['cost_item_id'] != 0 && $row['cost_item_count'] != 0) {
					isonline($id, $f['data'][$num]['id']);
					$cnt = finditem($f['data'][$num]['id'], $row['cost_item_id']);					
					if ($cnt < $row['cost_item_count']) die('77');		
					if (!takeitem($f['data'][$num]['id'], $row['cost_item_id'], $row['cost_item_count'])) die('78');
				}
				$gpoint = $row['costgold'];
				$spoint = $row['costsilver'];
				CheckPoints($id, $gpoint, $spoint);	// Проверяем есть ли на аккаунте поинты
				$item = InitItem();
				$item['id'] = $row['itemid'];
				$item['count'] = $row['count'];
				$item['max_count'] = $row['maxcount'];
				$item['data'] = $row['data'];
				$item['proctype'] = $row['proctype'];
				if ($row['expire']>0) $item['expire_date'] = time()+$row['expire']; else $item['expire_date'] = 0;
				$item['client_size'] = $row['client_size'];
				$q = SysSendMail(0, 32, 3, $send_role_id, 'Покупка из личного кабинета', 'Приятной игры', $item, 0);
				if ($q['retcode'] == 217) die('51'); else if ($q['retcode'] != 0) die('52');
				$it = GetExtItem($row['itemid']);
				echo "29";
				if ($row['expire']==0) $ddd=''; else $ddd=' на '.$row['expire'].' сек';
				$desc = $cur_role_name.': покупка клан арта '.$item->count.' '.$it->name.$ddd.$send_txt_addon;				
				$rresult = $db->query($query);
				$query = "update `klan_items` set `buycount`=`buycount`+1 WHERE id = ".$artid;
				$rresult = $db->query($query);
			} else die($err1);			
		break;		

		case 17:				// Получение информации о персонаже
			$id = intval($_POST['i']);
			if ($id < 16) die();
			$r = GetRole($id, 63);
			if ($r['retcode'] != 0) die('Персонаж не найден');
			$k = GetFactionInfo($r['value']['factionid']);
			if ($r['value']['factionid']==0) {
				$k['value']['name'] = 'Нет';
				$r['value']['factionrole'] = '';
			} else $r['value']['factionrole'] = $factrole[$r['value']['title']];
			printf('Ник: <b>%s</b><br>Класс: <b>%s</b><br>Уровень: <b>%d</b><br>Клан: <b>%s</b> %s', $r['value']['name'], GetOccupationName($r['value']['status']['occupation']), $r['value']['status']['level'], $k['value']['name'], $r['value']['factionrole']);			
			die();
		break;
		
		case 19:				// Редактируем шоп итем
			if (!isset($_POST['sitem']) || CheckNum($_POST['sitem'])) die('10');
			extract($_POST);
			$a = explode("\r\n", $discount_data);
			$b = array();
			if (count($a)>0)
			foreach ($a as $i1 => $val1){
				if ($val1=='') continue;
				$c = explode('-',$val1);
				if (count($c)!=2) continue;
				$b[$c[0]] = $c[1];
			}
			$discount_data = @serialize($b);
			$db->query("SET NAMES utf8");
			$sql = sprintf("UPDATE `shop_items` SET `itemid`=%d, `count`=%d, `maxcount`=%d, `data`='%s', `client_size`=%d, `proctype`=%d, `subcat`=%d, `cost_timeless`='%s', `cost_expire`='%s', `expire`=%d, `discount_data`='%s', `desc`='%s', `rest`=%d, `buycount`=%d WHERE `id`=%d", $itemid, $count, $maxcount, $db->real_escape_string($data), $client_size, $proctype, $subcat, $db->real_escape_string($cost_timeless_gold.'|'.$cost_timeless_silver), $db->real_escape_string($cost_expire_gold.'|'.$cost_expire_silver), $expire, $db->real_escape_string($discount_data), $db->real_escape_string($desc), $rest, $buycount, $sitem);
			$res = query2mysql($sql);
			die('16');
		break;	

		case 20:				// Смена пароля от аккаунта
			$gpoint=0;
			$spoint=0;
			extract($_POST);
			$query="UPDATE `users` SET `passwd2`=`passwd` WHERE `name`='$login' AND `ID`=$id";
			$result = query2mysql($query);
			//$query="call changePasswd('$login',$md52)";
			$query = sprintf("UPDATE `users` SET `passwd`='%s' WHERE `name`='%s'", $db->real_escape_string($newpassw), $db->real_escape_string($login));
			$result = query2mysql($query);
			$db->query("INSERT INTO `changepass` (`name`,`ip`,`data`) VALUES ('".$login."','".$ip."',now())");
			echo '43';
			$desc='Смена пароля аккаунта';
		break;		

		case 53:				// Редактируем клан арт итем
			if (!isset($_POST['kitem']) || CheckNum($_POST['kitem'])) die('10');
			extract($_POST);			
			query2mysql("SET NAMES utf8");
			if (isset($remove_no_klan)) $remove_no_klan = 1; else $remove_no_klan = 0;
			$sql = sprintf("UPDATE `klan_items` SET `klanid`=%d, `itemid`=%d, `count`=%d, `maxcount`=%d, `data`='%s', `client_size`=%d, `proctype`=%d, `expire`=%d, `costgold`=%d, `costsilver`=%d, `cost_item_id`=%d, `cost_item_count`=%d, `remove_no_klan`=%d, `desc`='%s', `buycount`=%d WHERE `id`=%d", $klanid, $itemid, $count, $maxcount, $db->real_escape_string($data), $client_size, $proctype, $expire, $costgold, $costsilver, $cost_item_id, $cost_item_count, $remove_no_klan, $db->real_escape_string($desc), $buycount, $kitem);
			$res = query2mysql($sql);
			die('16');
		break;

		case 54:				// Добавляем новый клан арт итем			
			$sql = "INSERT INTO `klan_items` (`klanid`,`itemid`,`count`,`maxcount`,`data`,`client_size`,`proctype`,`expire`,`costgold`,`costsilver`,`cost_item_id`,`cost_item_count`,`desc`,`buycount`) VALUES ('0', '0', '1', '1', '', '0', '0', '0', '0', '0', '0', '0', '', '0');";
			$res = query2mysql($sql);
			die('16');
		break;	

		case 55:				// Удаляем клан арт итем
			if (!isset($_POST['kitem']) || CheckNum($_POST['kitem'])) die('10');
			$kitem = intval($_POST['kitem']);			
			$sql = sprintf("DELETE FROM `klan_items` WHERE `id`=%d", $kitem);
			$res = query2mysql($sql);
			die('16');
		break;

		case 56:				// Добавляем IP в список разрешенных
			if (!isset($_POST['addip']) || strlen($_POST['addip'])>15) die('10');
			$addip = $_POST['addip'];
			$sql = sprintf("SELECT `ipdata` FROM `users` WHERE `ID`=%d", $id);
			$res = query2mysql($sql, true);
			$row = mysqli_fetch_assoc($res);
			$ipdata = @unserialize($row['ipdata']);
			if (!is_array($ipdata)) {
				$ipdata = array();
				$ipdata[0] = false;
				$ipdata[1] = array();
				$ipdata[2] = false;
			}
			if (!isset($ipdata[2])) $ipdata[2] = false;
			array_push($ipdata[1], $addip);
			$sql = sprintf("UPDATE `users` SET `ipdata`='%s' WHERE `ID`=%d", $db->real_escape_string(serialize($ipdata)), $id);
			$res = query2mysql($sql);
			die('16');
		break;

		case 57:				// Удаляем IP из списка разрешенных
			if (!isset($_POST['i']) || CheckNum($_POST['i'])) die('10');
			$i = $_POST['i'];
			$sql = sprintf("SELECT `ipdata` FROM `users` WHERE `ID`=%d", $id);
			$res = query2mysql($sql, true);
			$row = mysqli_fetch_assoc($res);
			$ipdata = @unserialize($row['ipdata']);
			if (!is_array($ipdata)) {
				$ipdata = array();
				$ipdata[0] = false;
				$ipdata[1] = array();
				$ipdata[2] = false;
			}
			if (!isset($ipdata[2])) $ipdata[2] = false;
			if (!isset($ipdata[1][$i])) die('10');
			unset($ipdata[1][$i]);
			// Если нет айпи - снимаем блок со входа в ЛК
			if (count($ipdata[1]) == 0) $ipdata[0] = false;
			$sql = sprintf("UPDATE `users` SET `ipdata`='%s' WHERE `ID`=%d", $db->real_escape_string(serialize($ipdata)), $id);
			$res = query2mysql($sql);
			die('16');
		break;

		case 58:				// Активируем/отключаем режим ограничения по айпи в ЛК
			if (!isset($_POST['mode'])) die('10');
			$mode = intval($_POST['mode']);
			$sql = sprintf("SELECT `ipdata` FROM `users` WHERE `ID`=%d", $id);
			$res = query2mysql($sql, true);
			$row = mysqli_fetch_assoc($res);
			$ipdata = @unserialize($row['ipdata']);
			if (!is_array($ipdata)) {
				$ipdata = array();
				$ipdata[0] = false;
				$ipdata[1] = array();
				$ipdata[2] = false;
			}			
			if (!isset($ipdata[2])) $ipdata[2] = false;
			$ipdata[0] = (bool)$mode;
			$sql = sprintf("UPDATE `users` SET `ipdata`='%s' WHERE `ID`=%d", $db->real_escape_string(serialize($ipdata)), $id);
			$res = query2mysql($sql);
			die('16');
		break;

		case 59:				// Активируем/отключаем режим ограничения по айпи в игре
			if (!isset($_POST['mode'])) die('10');
			$mode = intval($_POST['mode']);
			$sql = sprintf("SELECT `ipdata` FROM `users` WHERE `ID`=%d", $id);
			$res = query2mysql($sql, true);
			$row = mysqli_fetch_assoc($res);
			$ipdata = @unserialize($row['ipdata']);
			if (!is_array($ipdata)) {
				$ipdata = array();
				$ipdata[0] = false;
				$ipdata[1] = array();
				$ipdata[2] = false;
			}			
			$ipdata[2] = (bool)$mode;
			$sql = sprintf("UPDATE `users` SET `ipdata`='%s' WHERE `ID`=%d", $db->real_escape_string(serialize($ipdata)), $id);
			$res = query2mysql($sql);
			die('16');
		break;

		case 69:				// Редактируем промо-код
			if (!isset($_POST['record_id']) || CheckNum($_POST['record_id'])) die('10');
			extract($_POST);	
			if (isset($_POST['promo_multi_user'])) $promo_multi_user = 1; else $promo_multi_user = 0;		
			$db->query("SET NAMES utf8");			
			$sql = sprintf("UPDATE `promo_codes` SET `code`='%s', `expire`=%d, `group`=%d, `bonus_money_gold`=%d, `bonus_money_silver`=%d, `bonus_item_id`=%d, `bonus_item_count`=%d, `bonus_item_max_count`=%d, `bonus_item_data`='%s', `bonus_item_client_size`=%d, `bonus_item_proctype`=%d, `bonus_item_expire`=%d, `multi_user`=%d, `used_userid`=%d, `desc`='%s' WHERE `id`=%d", $promo_code, $promo_expire, $promo_group, $promo_gold, $promo_silver, $promo_item_id, $promo_item_count, $promo_item_maxcount, $db->real_escape_string($promo_item_data), $promo_item_client_size, $promo_item_proctype, $promo_item_expire, $promo_multi_user, $promo_used_userid, $db->real_escape_string($promo_desc), $record_id);
			$res = $db->query($sql);
			if (!$res) die($db->error);			
			die('ok');
		break;

		case 70:				// Удаляем промо-код из базы
			if (!isset($_POST['record_id']) || CheckNum($_POST['record_id'])) die('10');
			$record_id = intval($_POST['record_id']);			
			$sql = sprintf("DELETE FROM `promo_codes` WHERE `id`=%d", $record_id);
			$res = $db->query($sql);
			if (!$res) die($db->error);
			die('16');
		break;

		case 71:				// Удаляем просроченные, но не использованные промо-коды из базы
			$sql = sprintf("DELETE FROM `promo_codes` WHERE `expire`<=%d AND `expire`<>0 AND `used_userid`=0", time());
			$res = $db->query($sql);
			if (!$res) die($db->error);
			die('16');
		break;

		case 72:				// Удаляем использованные промо-коды из базы
			$sql = "DELETE FROM `promo_codes` WHERE `used_userid`>0";
			$res = $db->query($sql);
			if (!$res) die($db->error);
			die('16');
		break;

		case 73:				// Используем промо-код
			if (!isset($_POST['promo_code'])) die('10');
			// Пытаемся рассинхронизировать параллельные потоки для избежания дюпа и багания монет
			usleep(rand(500,90000));
			$db->query("SET NAMES utf8");
			$promo_code = $_POST['promo_code'];
			$sql = "SELECT * FROM `promo_codes` WHERE BINARY `code`='".$db->real_escape_string($promo_code)."'";
			$res = query2mysql($sql);
			if (!$res->num_rows) die('99');
			$row = mysqli_fetch_assoc($res);
			if ($row['used_userid'] > 0 && !$row['multi_user']) die('100');
			if ($row['expire'] > 0 && $row['expire']<=time()) die('101');
			if ($row['multi_user'] || $row['group']) {
				$row['group'] = intval($row['group']);
				$sql = "SELECT `bonus_data` FROM `users` WHERE `ID`=".$id;
				$res = query2mysql($sql, true);
				$rr = mysqli_fetch_assoc($res);
				$bonus_data = @unserialize($rr['bonus_data']);
				if (!is_array($bonus_data)) $bonus_data = array();
				if (!isset($bonus_data['promo']) || !is_array($bonus_data['promo'])) $bonus_data['promo'] = array();
				if (!isset($bonus_data['promo_group']) || !is_array($bonus_data['promo_group'])) $bonus_data['promo_group'] = array();
				if ($row['multi_user']) {
					if (in_array($promo_code, $bonus_data['promo'])) die('100');
					array_push($bonus_data['promo'], $promo_code);
					$row['used_userid']++;
				}
				if ($row['group']) {
					if (in_array($row['group'], $bonus_data['promo_group'])) die('103');
					array_push($bonus_data['promo_group'], $row['group']);
					if (!$row['multi_user']) $row['used_userid'] = $id;
				}
			} else $row['used_userid'] = $id;
			if ($row['bonus_item_id'] > 0 && $row['bonus_item_count'] > 0) {
				$actrole = GetActiveRole($id);
				if ($actrole==0) die('95');
				$item = InitItem();
				$item['id'] = $row['bonus_item_id'];
				$item['count'] = $row['bonus_item_count'];
				$item['max_count'] = $row['bonus_item_max_count'];
				$item['data'] = $row['bonus_item_data'];
				$item['proctype'] = $row['bonus_item_proctype'];
				if ($row['bonus_item_expire']>0) $item['expire_date'] = time() + $row['bonus_item_expire']; else $item['expire_date'] = 0;
				$item['client_size'] = $row['bonus_item_client_size'];
				$q = SysSendMail(0, 32, 3, $actrole, 'Бонус', 'За использование промо-кода '.$promo_code, $item, 0);
				if ($q['retcode'] == 217) die('51'); else if ($q['retcode'] != 0) die('52');
			}
			if ($row['multi_user'] || $row['group']) {
				$res = query2mysql(sprintf("UPDATE `users` SET `bonus_data`='%s' WHERE `ID`=%d", $db->real_escape_string(serialize($bonus_data)), $id));
			}
			$res = query2mysql(sprintf("UPDATE `promo_codes` SET `used_userid`=%d WHERE `id`=%d", $row['used_userid'], $row['id']));
			$gpoint = -$row['bonus_money_gold'];
			$spoint = -$row['bonus_money_silver'];
			$desc = 'Промо-код '.$promo_code;			
		break;

		case 77:			// Админ активация аккаунта
			if (!isset($_POST['ip']) || !isset($_POST['userid'])) die('10');
			$userid = intval($_POST['userid']);
			$query = sprintf("SELECT `passwd` FROM `users` WHERE `ID`=%d", $userid);
			$result = query2mysql($query); 
			if (!$result->num_rows) die('10');
			$row = mysqli_fetch_assoc($result);
			if ($row['passwd']!='confirm') die('104');
			$query = sprintf("UPDATE `users` SET `passwd`=`passwd2` WHERE `ID`=%d", $userid);
			$result = query2mysql($query); 
			die('ok');
		break;

		default : die($err1); break;
	}
	GiveGold($id, -$gpoint, -$spoint, $desc, $ip);
	if ($n!=7 && $n!=8 && $n!=10 && $n!=11 && $n!=12 && $n!=20) echo "16";
	die();
}

function AdminAction($id, $act){
	global $db;
	$db->query("SET NAMES utf8");
	switch ($act){
		case 'addcat':
			$res = query2mysql("INSERT INTO `shop_cat` (`name`) VALUES ('New')");
			die('16');
		break;

		case 'delcat':
			if (!isset($_POST['page'])) die('10');
			$page = intval($_POST['page']);
			$res = $db->query("SELECT * FROM `shop_subcat` WHERE `catid`=".$page);
			if ($res->num_rows>0){
				while ($row = mysqli_fetch_assoc($res)){
					$res1 = query2mysql("DELETE FROM `shop_items` WHERE `subcat`=".$row['id']);
				}
			}
			$res = query2mysql("DELETE FROM `shop_subcat` WHERE `catid`=".$page);
			$res = query2mysql("DELETE FROM `shop_cat` WHERE `id`=".$page);
			die('16');
		break;

		case 'addsubcat':
			if (!isset($_POST['page'])) die('10');
			$page = intval($_POST['page']);
			$res = query2mysql("INSERT INTO `shop_subcat` (`catid`,`name`) VALUES (".$page.",'New')");
			die('16');
		break;

		case 'delsubcat':
			if (!isset($_POST['page'])) die('10');
			$page = intval($_POST['page']);
			if (!isset($_POST['subcat'])) die('10');
			$subcat = intval($_POST['subcat']);			
			$res = query2mysql("DELETE FROM `shop_items` WHERE `subcat`=".$subcat);
			$res = query2mysql("DELETE FROM `shop_subcat` WHERE `id`=".$subcat);
			die('16');
		break;

		case 'rencat':
			if (!isset($_POST['newname'])) die('10');
			$newname = $db->real_escape_string($_POST['newname']);
			if (!isset($_POST['page'])) die('10');
			$page = intval($_POST['page']);
			$res = query2mysql(sprintf("UPDATE `shop_cat` SET `name`='%s' WHERE `id`=%d", $newname, $page));
			die('16');
		break;

		case 'rensubcat':
			if (!isset($_POST['newname'])) die('10');
			$newname = $db->real_escape_string($_POST['newname']);
			if (!isset($_POST['page'])) die('10');
			$page = intval($_POST['page']);
			if (!isset($_POST['subcat'])) die('10');
			$subcat = intval($_POST['subcat']);	
			$res = query2mysql(sprintf("UPDATE `shop_subcat` SET `name`='%s' WHERE `id`=%d AND `catid`=%d", $newname, $subcat, $page));
			die('16');
		break;

		case 'additem':
			if (!isset($_POST['subcat'])) die('10');
			$subcat = intval($_POST['subcat']);	
			$res = query2mysql(sprintf("INSERT INTO `shop_items` (`itemid`, `count`, `maxcount`, `subcat`, `rest`) VALUES (57440, 1, 10000, %d, -1)", $subcat));
			$newid = $db->insert_id;
			die('16|'.$newid);
		break;

		case 'delitem':
			if (!isset($_POST['page'])) die('10');
			$page = intval($_POST['page']);
			if (!isset($_POST['subcat'])) die('10');
			$subcat = intval($_POST['subcat']);	
			if (!isset($_POST['sitem'])) die('10');
			$sitem = intval($_POST['sitem']);			
			$res = query2mysql(sprintf("DELETE FROM `shop_items` WHERE `subcat`=%d AND `id`=%d", $subcat, $sitem));
			die('16');
		break;

		case 'cloneitem':
			if (!isset($_POST['sitem'])) die('10');
			$sitem = intval($_POST['sitem']);	
			$res = query2mysql("SELECT * FROM `shop_items` WHERE `id`=".$sitem, true);
			$row = mysqli_fetch_assoc($res);
			$res = query2mysql(sprintf("INSERT INTO `shop_items` (`itemid`, `count`, `maxcount`, `data`, `client_size`, `proctype`, `subcat`, `cost_timeless`, `cost_expire`, `expire`, `discount_data`, `desc`, `rest`) VALUES (%d, %d, %d, '%s', %d, %d, %d, '%s', '%s', %d, '%s', '%s', %d)", $row['itemid'], $row['count'], $row['maxcount'], $row['data'], $row['client_size'], $row['proctype'], $row['subcat'], $row['cost_timeless'], $row['cost_expire'], $row['expire'], $row['discount_data'], $db->real_escape_string($row['desc']), $row['rest']));
			die('16');
		break;

		case 'itemname':
			if (!isset($_POST['itemid'])) die();
			$itemid = intval($_POST['itemid']);
			if ($itemid == 0) die();
			$item = GetExtItem($itemid);
			$icon = sprintf('<img src="getitemicon.php?i=%s" border="0" class="item_icon"> ', urlencode(base64_encode($item->icon)));
			die($icon.' <span class="it_name">'.$item->name.'</span>');
		break;

		case 'klanname':
			if (!isset($_POST['itemid'])||!isset($_POST['servid'])) die();
			$itemid = intval($_POST['itemid']);
			$servid = intval($_POST['servid']);
			if ($itemid == 0) die();
			$fact = GetFactionInfo($itemid);
			echo '<img src="klan/geticon.php?servid='.$servid.'&klan='.$itemid.'" class="imcnt"> '.$fact['value']['name'];
			die();
		break;
	}
}

function GetRefData($id){
	global $db;
	$refpers = 0;
	$refacc = 0;
	$sql="SELECT `referal` FROM `users` WHERE `ID`=".$id;
	$result = @$db->query($sql);
	$mt = 0;
	$rb = false;
	if ($result && $result->num_rows) {
		$row = mysqli_fetch_assoc($result);
		$refacc = $row['referal'];	
		if ($refacc != 0){			
			$f = GetUserRoles($refacc);
			$lt = 0;
			if ($f['retcode'] == 0) {						
				foreach ($f['data'] as $i => $val){
					$r = GetRole($val['id'], 63);
					if ($r['value']['status']['reborndata'] != '') $rb = $r['value']['status']['reborndata'];
					$mt += $r['value']['status']['time_used'];
					if ($r['value']['status']['time_used'] > $lt){
						$lt = $r['value']['status']['time_used'];
						$refpers = $val['id'];
					}
				}
			}
		}
	}
	$a = array($refacc, $refpers, $mt, $rb);
	return $a;
}

function GetActiveRole($id){
	global $db;
	$f = false;
	$db->query("SET NAMES utf8");
	$res = $db->query('SELECT `bonus_data` FROM `users` WHERE `ID`='.$id);
	$row = mysqli_fetch_assoc($res);
	if ($row['bonus_data']=='') $bonus_data = array(); else $bonus_data = @unserialize($row['bonus_data']);
	if (!is_array($bonus_data)) $bonus_data = array();
	$cur_reward_role_index = -1;
	if (isset($bonus_data['cur_reward_role_index'])) {
		$cur_reward_role_index = $bonus_data['cur_reward_role_index'];
		$f = GetUserRoles($id);
		if ($cur_reward_role_index >= count($f['data'])) $cur_reward_role_index = -1;
	}
	if ($cur_reward_role_index >= 0) $a = $f['data'][$cur_reward_role_index]['id']; else
	{
		$role = isroleonline($id);
		if ($role > 0) return $role;
		if ($f === false) $f = GetUserRoles($id);
		$lt = 0;
		$a = 0;
		if ($f['retcode']==0) {						
			foreach ($f['data'] as $i => $val){
				$r = GetRole($val['id'], 63);
				if ($r['value']['status']['time_used'] > $lt){
					$lt = $r['value']['status']['time_used'];
					$a = $val['id'];
				}
			}
		}
	}
	return $a;
}

function donate(){
	global $db;	
	if (!isset($_POST['ip'])||!isset($_POST['out_summ'])||!isset($_POST['inv_id'])||!isset($_POST['login'])||!isset($_POST['userid'])||!isset($_POST['moneycount'])||!isset($_POST['bonus'])||!isset($_POST['act_bonus'])||!isset($_POST['don_kurs'])||!isset($_POST['don_system'])) die('2');
	$out_summ = $_POST["out_summ"];
	$don_kurs = $_POST["don_kurs"];
	$don_system = $_POST['don_system'];
	$inv_id = intval($_POST["inv_id"]);
	$p_sys_id = $_POST['p_sys_id'];
	$login = $_POST["login"];
	$userid = $_POST["userid"];
	$moneycount = $_POST["moneycount"];
	$bonus = $_POST["bonus"];
	$act_bonus = $_POST["act_bonus"];
	$item = unserialize($_POST["item"]);
	$send_bonus_item = $_POST["send_bonus_item"];
	$ip = $_POST["ip"];
	$ref_don_bonus_enable = $_POST['ref_don_bonus_enable'];
	$ref_don_bonus = (float)$_POST['ref_don_bonus'];
	$ref_don_bonus_timeused = (int)$_POST['ref_don_bonus_timeused'];
	$dsctxt='Спасибо за пожертвование';
	if ($out_summ < $don_kurs) {
		$moneycount=0;
		$bonus = 0;
		$send_bonus_item = false;
	}
	// Проверяем по номеру заявки
	$sql = "SELECT * FROM `donate` WHERE `inv_id`='$inv_id' AND `status`=1 AND `don_system`='".$db->real_escape_string($don_system)."'";
	$res = $db->query($sql);
	if ($res->num_rows>0) {
		$sql = "SELECT * FROM `donate` WHERE `p_sys_id`='".$db->real_escape_string($p_sys_id)."' AND `p_sys_id`<>'' AND `status`=1 AND `don_system`='".$db->real_escape_string($don_system)."'";
		$res = $db->query($sql);
		if ($res->num_rows > 0) {
			die('101'); // Этот заказ уже обработан
		} else die('NeedNewID'); // Нужна новая заявка
	}
	// Проверяем по номеру платежа
	$sql = "SELECT * FROM `donate` WHERE `p_sys_id`='".$db->real_escape_string($p_sys_id)."' AND `p_sys_id`<>'' AND `status`=1 AND `don_system`='".$db->real_escape_string($don_system)."'";
	$res = $db->query($sql);
	if ($res->num_rows>0) {
		die('101'); // Этот заказ уже обработан
	}
	$sql = "update `users` set lkgold=lkgold+$moneycount+$bonus WHERE `name`='$login'";
	$points = $moneycount+$bonus;
	$res = $db->query($sql);
	if (($db->affected_rows>0)||($points==0)) {
		$sql = "SELECT * FROM `donate` WHERE `inv_id`='$inv_id' AND `status`=0 AND `don_system`='".$db->real_escape_string($don_system)."'";
		$res = $db->query($sql);
		if ($res->num_rows>0) { // Заказ добавлен, но не обработан
			$db->query("UPDATE `donate` SET `status`=1 WHERE `inv_id`='$inv_id' AND `don_system`='".$db->real_escape_string($don_system)."'");
		} else {
			$sql="INSERT INTO `donate` (`inv_id`, `p_sys_id`, `don_system`, `data`, `out_summ`, `don_kurs`, `money`, `act_bonus`, `bonus_money`, `login`, `userid`, `ip`, `status`) VALUES ('$inv_id', '".$db->real_escape_string($p_sys_id)."', '".$db->real_escape_string($don_system)."', NOW(), '$out_summ', '$don_kurs', '$moneycount', '$act_bonus', '$bonus', '$login', '$userid', '$ip', 1)";	
			$db->query($sql);
		}
		$CashLeft = GetCashFromAcc($userid);	
		$db->query("SET NAMES utf8");
		// Лог: Выдача основных монет
		$sql="insert into `lklogs` (`userid` ,`data` , `ip`, `gold` ,`silver`, `gold_rest` ,`silver_rest`, `desc` ) VALUES ($userid, now(), '', -$moneycount, 0, ".($CashLeft[0]-$bonus).", ".$CashLeft[1].", '".$db->real_escape_string($dsctxt)."');";				
		$db->query($sql);		
		// Лог: Выдача бонусных поинтов
		if ($bonus > 0) {
			sleep(1);
			$sql="insert into `lklogs` (`userid` ,`data` , `ip`, `gold` ,`silver`, `gold_rest` ,`silver_rest`, `desc` ) VALUES ($userid, now(), '', -$bonus, 0, ".$CashLeft[0].", ".$CashLeft[1].", 'Бонус');";				
			$db->query($sql);
		}		
		// выдача итема активному персонажу аккаунта
		if ($item['expire_date']) $item['expire_date'] += time();
		if (!$send_bonus_item) $item = InitItem();
		$actrole = GetActiveRole($userid);
		if ($actrole!=0) {
			if ($moneycount > 0) $msgtext = sprintf('Спасибо за пожертвование, вам в ЛК начислено %s монет. ', $moneycount); else
			$msgtext = 'Спасибо за пожертвование. ';
			if ($bonus > 0) $msgtext.=sprintf('Также вы получаете %s бонусных монет! ', $bonus);
			$msgtext.='Приятной игры';			
			$q = SysSendMail(0, 32, 3, $actrole, 'Уведомление', $msgtext, $item, 0);
		}
		if ($ref_don_bonus_enable) {
			// Выдача бонуса рефералу		
			$ref = GetRefData($userid);
			if ($ref[0]!=0 && $moneycount > 0 && $ref[1]!=0 && $ref[2] >= $ref_don_bonus_timeused){		// Только если выданы поинты, и есть нужное время онлайна 
				$ref_bonus = round($moneycount/100*$ref_don_bonus);
				if ($ref_bonus > 0) {
					$sql = "update `users` set lkgold=lkgold+$ref_bonus WHERE `ID`=".$ref[0];
					$res=$db->query($sql);
					$sql = "update `users` set ref_bonus=ref_bonus+$ref_bonus WHERE `ID`=".$userid;
					$res=$db->query($sql);
					$dsctxt = 'Бонус за пожертвование от приглашенного игрока №'.(($userid/16) - 1);
					$CashLeft = GetCashFromAcc($ref[0]);
					$sql="insert into `lklogs` (`userid` ,`data` , `ip`, `gold` ,`silver`, `gold_rest` ,`silver_rest`, `desc` ) VALUES (".$ref[0].", now(), '', -$ref_bonus, 0, ".$CashLeft[0].", ".$CashLeft[1].", '".$db->real_escape_string($dsctxt)."');";
					$db->query($sql);
					$actrole = GetActiveRole($ref[0]);
					if ($actrole!=0) {
						$item = InitItem();
						$q = SysSendMail(0, 32, 3, $actrole, 'Уведомление', sprintf('Вам в ЛК начислено %d монет за приглашенного игрока №%s', $ref_bonus, ($userid/16)-1), $item, 0);
					}
				}
			}
		}
		die('100');
	} else {
		$sql="INSERT INTO `donate` (`inv_id`, `p_sys_id`, `don_system`, `data`, `out_summ`, `don_kurs`, `money`, `act_bonus`, `bonus_money`, `login`, `ip`, `status`) VALUES ('$inv_id', '".$db->real_escape_string($p_sys_id)."', '".$db->real_escape_string($don_system)."', NOW(), '$out_summ', '$don_kurs', '$moneycount', '$act_bonus', '$bonus', '$login', '$ip', 0)";		
		$db->query($sql);
		die('3');
	}
}

function CheckBrut($answ=''){
	global $db;
	// Проверка на брут
	if (!$db->query(sprintf("DELETE FROM `antibrut` WHERE `last_date_fail`<=%d", time()-600))) {
		if ($answ == '') die('errorbase'); // Чистка таблицы антибрута
		else MakeAnswer($answ, 1000, 'errorbase');
	}
	$query = sprintf("SELECT * FROM `antibrut` WHERE `ip`='%s'", $db->real_escape_string($_POST['ip']));
	$result = $db->query($query);
	if ($result->num_rows) {
		$row = mysqli_fetch_assoc($result);
		if (($row['fail_count'])>=5) {
			if ($answ == '') die('time');
			else MakeAnswer($answ, 1000, 'time');
		}
	}
}

function Register(){
	global $db;
	if (!isset($_POST['ip']) || !isset($_POST['login']) || !isset($_POST['pass']) || !isset($_POST['email']) || !isset($_POST['email_confirm']) || !isset($_POST['question']) || !isset($_POST['answer']) || !isset($_POST['realname']) || !isset($_POST['referal']) || !isset($_POST['ip_max_reg']) || !isset($_POST['email_max_reg']) || !isset($_POST['register_gold']) || !isset($_POST['zoneid']) || !isset($_POST['aid'])) die('10');	
	$pass = ($_POST['email_confirm'])?'confirm':base64_decode($_POST['pass']);
	$ip_max_reg = intval($_POST['ip_max_reg']);
	$email_max_reg = intval($_POST['email_max_reg']);
	$ip = $db->real_escape_string($_POST['ip']);
	$referal = intval($_POST['referal']);	
	$register_gold = intval($_POST['register_gold']);
	$login = $db->real_escape_string(trim($_POST['login']));
	$question = $db->real_escape_string(trim($_POST['question']));
	$answer = $db->real_escape_string(trim($_POST['answer']));
	$email = $db->real_escape_string(trim($_POST['email']));
	$realname = $db->real_escape_string(trim($_POST['realname']));
	$vkid = $db->real_escape_string($_POST['vk_id']);
	$vkname = $db->real_escape_string($_POST['vk_name']);
	$vkphoto = $db->real_escape_string($_POST['vk_photo']);
	$steamid = $db->real_escape_string($_POST['steam_id']);
	$steamname = $db->real_escape_string($_POST['steam_name']);
	$steamphoto = $db->real_escape_string($_POST['steam_photo']);
	if ($vkid) $query = "SELECT * FROM `users` WHERE `vkid` = '$vkid'"; else
	if ($steamid) $query = "SELECT * FROM `users` WHERE `steamid` = '$steamid'";
	if ($vkid || $steamid) {
		$rresult = $db->query($query);
		if ($rresult==false) {
			die("errorbase");
		}
		if ($rresult->num_rows) {
			die("needauth");
		}
	}
	$zoneid = $_POST['zoneid'];
	$aid = $_POST['aid'];
	CheckBrut();	
	if ($ip=='') $ip = 'none';
	CheckAntibrutIP();
	// Check if user already exists
	$query = "SELECT * FROM `users` WHERE name = '$login'";
	$rresult = $db->query($query);
	if ($rresult==false) {
		die("errorbase");
	}
	if ($rresult->num_rows) {
		die("exists");
	}	
	if ($ip_max_reg > 0) {
	// Check count IP reg
		$query = "SELECT count(*) FROM `users` WHERE `idnumber` = '$ip'";
		$rresult = $db->query($query);
		if ($rresult==false) {
			die("errorbase");
		}
		$row = mysqli_fetch_array($rresult);
		if (intval($row[0]) >= $ip_max_reg) {
			die("iplimit");
		}	
	}
	if ($email_max_reg > 0) {
	// Check count EMail reg
		$query = "SELECT count(*) FROM `users` WHERE `email` = '$email'";
		$rresult = $db->query($query);
		if ($rresult==false) {
			die("errorbase");
		}
		$row = mysqli_fetch_array($rresult);
		if (intval($row[0]) >= $email_max_reg) {
			die("emaillimit");
		}	
	}
        if ($referal > 0) {
		// Проверяем наличие реф акка
		$result = $db->query('SELECT `ID` FROM `users` WHERE `ID`='.$referal);
		if (!$result) die("errorbase");
		if (!$result->num_rows) {
			$referal = 0;
		}
	}
	$query = "SELECT (IFNULL(MAX(id), 16)+16) from `users`"; 
	$result = $db->query($query); 
	if (!$result) die("errorbase");
	$row = mysqli_fetch_array($result);
	$userid = $row[0];
	if ($referal >= $userid) $referal = 0;
	$query = sprintf("INSERT INTO `users` (`id`,`name`,`passwd`,`Prompt`,`answer`,`truename`,`idnumber`,`email`,`province`,`city`,`address`,`creatime`,`passwd2`,`referal`) VALUES (%d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', now(), '%s', %d)", $userid, $login, $db->real_escape_string($pass), $question, $answer, $realname, $ip, $email, '', '', '', $db->real_escape_string(base64_decode($_POST['pass'])), $referal);
	$rresult = $db->query($query);	
	if (!$rresult) die("errorbase: ");
	$db->query('SET names utf8');
 	$query = sprintf("UPDATE `users` SET `Prompt`='%s', `answer`='%s', `truename`='%s', `vkid`='%s', `vkname`='%s', `vkphoto`='%s', `steamid`='%s', `steamname`='%s', `steamphoto`='%s' WHERE `ID`=%d", $question, $answer, $realname, $vkid, $vkname, $vkphoto, $steamid, $steamname, $steamphoto, $userid);
	$rresult = $db->query($query);	
	if (!$rresult) die("errorbase");
	if ($register_gold > 0) {
		$result = UseCash($userid, $zoneid, $aid, $register_gold*100);
	}
	die("ok".$userid);
}

function ResendMail(){
	global $db;
	$answ = array();
	CheckBrut($answ);
	CheckAntibrutIP($answ);
	if (!isset($_POST['ip']) || !isset($_POST['login'])) MakeAnswer($answ, 1000, 'inputdata');
	$result = $db->query("SELECT `ID`,`passwd`, `email` FROM `users` WHERE `name`='".$db->real_escape_string($_POST['login'])."'");
	if (!$result) MakeAnswer($answ, 1000, 'errorbase');
	$row = mysqli_fetch_assoc($result);
	if ($row['passwd']!='confirm') MakeAnswer($answ, 1000, 'already');	
	$answ['realid'] = $row['ID'];
	$answ['email'] = $row['email'];
	MakeAnswer($answ, 0, 'ok');
}

function ActReg(){
	global $db;
	if (!isset($_POST['ip']) || !isset($_POST['login']) || !isset($_POST['idacc'])) die('10');	
	$query = sprintf("SELECT `passwd` FROM `users` WHERE `ID`=%d AND `name`='%s'", $_POST['idacc'], $db->real_escape_string($_POST['login']));
	$result = $db->query($query); 
	if (!$result) die("errorbase");
	if (!$result->num_rows) die('10');
	$row = mysqli_fetch_assoc($result);
	if ($row['passwd']!='confirm') die('already');
	$query = sprintf("UPDATE `users` SET `passwd`=`passwd2` WHERE `ID`=%d AND `name`='%s'", $_POST['idacc'], $db->real_escape_string($_POST['login']));
	$result = $db->query($query); 
	if (!$result) die("errorbase");
	die('ok');
}

function CheckLogin(){
	global $db;
	CheckBrut();
	CheckAntibrutIP();
	if (isset($_POST['login'])) {
		$login = $db->real_escape_string($_POST['login']);
	} else die();
	if (strlen($login)>20) echo "1"; else {		
		$query = "SELECT * FROM users WHERE name = '$login'";
		$rresult = $db->query($query);	
		if ($rresult->num_rows) echo "denied"; else
		echo "free";
	}
	die();
}

function checker($email){
	if (preg_match("/^[0-9a-z_\-\.]+@[0-9a-z_\-^\.]+\.[a-z]{2,4}$/i", $email)) $h=1; else $h=0;
	return($h);
}

function getdata($s1,$s2) {
	return ('
      <tr>
         <td width="150px" align="right" valign="top"><font color="#aa0000">'.$s1.':</font></td>
         <td align="left" valign="top">'.$s2.'</td>
      </tr>');	
}

function GetHash($servid, $login, $pass, $email, $cookie_pasw, $encoder_Salt, $operation = 0){
	// $operation: 0 - null pass, 1 - disable ip check
	$s = sprintf('%d|%s|%s|%s|%d', $servid, $login, md5($pass), $email, $operation);
	return mycrypt(encode($s, $cookie_pasw, $encoder_Salt));
}    

function ValidateChKey($key, $cookie_pasw, $encoder_Salt){
	if ($key == '' || strlen($key)<8) return 0;
	$d = @encode(decrypt($key), $cookie_pasw, $encoder_Salt);
	if ($d == '' || strlen($d)<8) return 1;
	$str = explode('|', $d);
	if (count($str)!=5) return 2;
	if (intval($str[0]) != $str[0]) return 3;
	//if ($str[0] < 1 || $str[0] > 4) return 4;
	return $str[1];
}

function MakeUpdateAnswer($errorcode, $text, $ttt){
	$answer = array(
		'errorcode' => $errorcode,
		'text' => $text,
		'log' => $ttt
	);
	echo serialize($answer);
	die();
}

function GetAccountTemplateData($row)
{
	$chlink = $_POST['link'];
	$cookie_pasw = $_POST['cookie_pasw'];
	$encoder_Salt = $_POST['encoder_Salt'];
	$rmlink = $chlink.GetHash($_POST['servid'], $row['name'], $row['passwd'], $row['email'], $cookie_pasw, $encoder_Salt);
	$dislink = $chlink.GetHash($_POST['servid'], $row['name'], $row['passwd'], $row['email'], $cookie_pasw, $encoder_Salt, 1);
	$result = array(
		'login' => $row['name'],
		'question' => $row['Prompt'],
		'answer' => $row['answer'],
		'reset_passw_link' => $rmlink,
		'reset_ip_link' => $dislink
	);
	return $result;
}

function NullPass(){
	global $db;
	$answ = array();
	if (!isset($_POST['ip']) || !isset($_POST['cookie_pasw'])|| !isset($_POST['encoder_Salt'])) MakeAnswer($answ, 1000, 'error');
	CheckBrut($answ);
	$ip = $db->real_escape_string($_POST['ip']);
	$db->query("SET NAMES utf8");
	if (!isset($_POST['key'])) {
		CheckAntibrutIP();
		MakeAnswer($answ, 1000, 'wrongkey');
	}
	$cookie_pasw = $_POST['cookie_pasw'];
	$encoder_Salt = $_POST['encoder_Salt'];
	$key = $_POST['key'];
	$login = ValidateChKey($key, $cookie_pasw, $encoder_Salt);
	if (!$login) {
		CheckAntibrutIP();
		MakeAnswer($answ, 1000, 'wrongkey');
	}	
	$sql = "SELECT `ID`, `name`, `passwd`, `email`, `Prompt`, `answer` FROM `users` WHERE `name`='".$db->real_escape_string($login)."';";
	$res = $db->query($sql);
	if ($res==false) MakeAnswer($answ, 1000, "errorbase");	
	if ($res->num_rows) {
		$row = mysqli_fetch_assoc($res);
		$newkey = GetHash($_POST['servid'], $row['name'], $row['passwd'], $row['email'], $cookie_pasw, $encoder_Salt);		
		if ($key != $newkey) MakeAnswer($answ, 1000, 'wrongkey');
		$newpassw = $_POST['newpassw'];
		$newpasswdecoded = $_POST['newpasswdecoded'];		
		$db->query('set names latin1');		
		$query="UPDATE `users` SET `passwd2`=`passwd` WHERE `name`='$login'";
		$rresult = $db->query($query);
		if (!$rresult) MakeAnswer($answ, 1000, 'errorbase');
		$query = sprintf("UPDATE `users` SET `passwd`='%s' WHERE `name`='%s'", $db->real_escape_string($newpassw), $db->real_escape_string($login));
		$rresult = $db->query($query);
		if (!$rresult) MakeAnswer($answ, 1000, 'errorbase');
		$db->query("INSERT INTO `changepass` (`name`,`ip`,`data`,`type`) VALUES ('".$login."','".$_POST['ip']."',now(),1)");
		$answ = array(
			'to' => $row['email'],
			'login' => $login,
			'question' => $row['Prompt'],
			'answer' => $row['answer']
			
		);		
		MakeAnswer($answ, 0, 'ok');
	} else {
		CheckAntibrutIP();
		MakeAnswer($answ, 1000, 'wrongkey');
	}
	CheckAntibrutIP();
	MakeAnswer($answ, 1000, 'no');
}

function DisableIPCheck(){
	global $db;
	$answ = array();
	if (!isset($_POST['ip']) || !isset($_POST['cookie_pasw'])|| !isset($_POST['encoder_Salt'])) MakeAnswer($answ, 1000, 'error');
	CheckBrut($answ);
	$ip = $db->real_escape_string($_POST['ip']);
	$db->query("SET NAMES utf8");
	if (!isset($_POST['key'])) {
		CheckAntibrutIP();
		MakeAnswer($answ, 1000, 'wrongkey');
	}
	$cookie_pasw = $_POST['cookie_pasw'];
	$encoder_Salt = $_POST['encoder_Salt'];
	$key = $_POST['key'];
	$login = ValidateChKey($key, $cookie_pasw, $encoder_Salt);
	if (!$login) {
		CheckAntibrutIP();
		MakeAnswer($answ, 1000, 'wrongkey');
	}	
	$sql = "SELECT `ipdata`, `email` FROM `users` WHERE `name`='".$db->real_escape_string($login)."';";
	$res = $db->query($sql);
	if (!$res || $res->num_rows < 1) MakeAnswer($answ, 1000, "errorbase");	
	$row = mysqli_fetch_assoc($res);
	$ipdata = @unserialize($row['ipdata']);	
	if (!is_array($ipdata)) {
		$ipdata = array();
		$ipdata[0] = false;
		$ipdata[1] = array();
		$ipdata[2] = false;
	}
	if (!isset($ipdata[2])) $ipdata[2] = false;
	$ipdata[0] = false;
	$sql = sprintf("UPDATE `users` SET `ipdata`='%s' WHERE `name`='%s'", $db->real_escape_string(serialize($ipdata)), $db->real_escape_string($login));
	$res = $db->query($sql);
	if (!$res) MakeAnswer($answ, 1000, 'errorbase');
	$answ = array(
		'to' => $row['email'],
		'login' => $login
		
	);	
	MakeAnswer($answ, 0, 'ipcheckdisabled');
}

function ForgetPass(){
	global $db;
	$answ = array();
	if (!isset($_POST['ip']) || !isset($_POST['cookie_pasw'])|| !isset($_POST['encoder_Salt'])) MakeAnswer($answ, 1000, 'error');
	CheckBrut($answ);
	$ip = $db->real_escape_string($_POST['ip']);
	$db->query("SET NAMES utf8");
	if (!isset($_POST['login'])) {
		CheckAntibrutIP();
		MakeAnswer($answ, 1000, 'error1');
	}
	$login = $db->real_escape_string($_POST['login']);
	$sql = "SELECT `ID`, `name`, `passwd`, `email`, `Prompt`, `answer` FROM `users` WHERE `name`='".$login."';";
	$res = $db->query($sql);
	if ($res==false) MakeAnswer($answ, 1000, "errorbase");
	if ($res->num_rows) {
		$row = mysqli_fetch_assoc($res);
		if (!checker($row['email'])) MakeAnswer($answ, 1000, 'bademail');
		$account_data = GetAccountTemplateData($row);
		$answ = array(
			'to' => $row['email'],
			'accounts' => array($account_data)
		);
		MakeAnswer($answ, 0, 'ok');
	} else {
		CheckAntibrutIP();
		MakeAnswer($answ, 1000, 'wrongacc');
	}
	CheckAntibrutIP();
	MakeAnswer($answ, 1000, 'no');
}

function ForgetLogin(){
	global $db;
	$answ = array();
	if (!isset($_POST['ip']) || !isset($_POST['cookie_pasw'])|| !isset($_POST['encoder_Salt'])) MakeAnswer($answ, 1000, 'error');
	CheckBrut($answ);
	$ip = $db->real_escape_string($_POST['ip']);
	$db->query("SET NAMES utf8");
	if (!isset($_POST['email']) || !isset($_POST['ip'])) MakeAnswer($answ, 1000, 'error');
	$email = $db->real_escape_string($_POST['email']);
	if (!checker($email)) {
		CheckAntibrutIP();
		MakeAnswer($answ, 1000, 'bademail');
	}
	$chlink = $_POST['link'];
	$sql = "SELECT `ID`, `name`, `passwd`, `email`, `Prompt`, `answer` FROM `users` WHERE `email`='".$email."';";
	$res = $db->query($sql);
	if ($res==false) MakeAnswer($answ, 1000, "errorbase");
	if ($res->num_rows){
		$accounts_data = array();
		while ($row = mysqli_fetch_assoc($res)){									
			$account_data = GetAccountTemplateData($row);
			array_push($accounts_data, $account_data);
		}
		
		$answ = array(
			'to' => $_POST['email'],
			'accounts' => $accounts_data
		);		
		MakeAnswer($answ, 0, 'ok');
	} else {
		CheckAntibrutIP();
		MakeAnswer($answ, 1000, 'wrongaccemail');
	}
	CheckAntibrutIP();	
	MakeAnswer($answ, 1000, 'no');
}

function OnlineStat(){
	$l = GetFullRoleListOnline();
	$online_acc = 0; $online_pers = 0; $online_world = 0; $online_instance = 0;
	if (count($l)>0) {
		foreach ($l as $i => $val){
			if ($val['userid'] > 0) $online_acc++;
			if ($val['roleid'] > 0) $online_pers++;
			if ($val['gsid'] == 1) $online_world++; else $online_instance++;
		}
	}
	$data = array();
	$data['online_acc'] = $online_acc;
	$data['online_pers'] = $online_pers;
	$data['online_world'] = $online_world;
	$data['online_instance'] = $online_instance;
	echo serialize($data);
	die();
}

function GetTime2($t,$col='#00aa00'){
	$pinkdays = floor($t/86400);
	$pinkhours = floor(($t-$pinkdays*86400)/3600);
	$pinkmin = floor(($t-$pinkdays*86400-$pinkhours*3600)/60);
	$pinksec = round($t-$pinkdays*86400-$pinkhours*3600-$pinkmin*60,0);
	if ($pinkhours<10) $pinkhours='0'.$pinkhours;
	if ($pinkmin<10) $pinkmin='0'.$pinkmin;
	if ($pinksec<10) $pinksec='0'.$pinksec;
	$timeused = '';
	if ($pinkdays>0) $timeused='<font color="#'.$col.'"><b>'.$pinkdays.'</b></font> дн ';
	$timeused .= $pinkhours.':'.$pinkmin.':'.$pinksec;
	return $timeused;
}

function GetTime1($t){
	$pinkhours = floor(($t)/3600);
	$pinkmin = floor(($t-$pinkhours*3600)/60);
	$pinksec = round($t-$pinkhours*3600-$pinkmin*60,0);
	if ($pinkhours<10) $pinkhours='0'.$pinkhours;
	if ($pinkmin<10) $pinkmin='0'.$pinkmin;
	if ($pinksec<10) $pinksec='0'.$pinksec;
	$timeused = $pinkhours.':'.$pinkmin.':'.$pinksec;
	return $timeused;
}

function GetPersInfo($a, $servid, $touserinfo = false){
	global $db, $gender, $factrole;
	$klanname = 'Нет';
	$factrang = '';
	$klanicon = '';
	$reborn = '';
	if ($a['factionid']!=0) {
		$res = $db->query('SELECT `name` FROM `klan` WHERE `id`='.$a['factionid']);
		if ($res->num_rows>0){
			$row = mysqli_fetch_assoc($res);
			$klanname = htmlspecialchars($row['name']);
		} else 
		{
			$fact = GetFactionInfo($a['factionid']);
			$klanname = $fact['value']['name'];
		}
		$klanicon = "<img src='klan/geticon.php?servid=".$servid."&klan=".$a['factionid']."' align='absmiddle'>";
		$factrang = $factrole[$a['factionrole']];				
	}
	if ($touserinfo) $dd = "<p>RoleID: <b><font color='#ffff00'>".$a['roleid']."</font></b></p>\n";
	if ($a['reborn']) $reborn = " <span class='label label-important'>РБ</span>";
	$d = sprintf("<font color='#ffffff'>
	<p>Уровень: <b><font color='#ffff00'>%d</font></b>%s</p>
	<p>Класс: <b><font color='#ffff00'>%s</font></b></p>
	<p>Пол: <b><font color='#ffff00'>%s</font></b></p>
	<p>Клан: %s <span class='label label-info'>%s</span> %s</p>
	<p>HP: <b><font color='#ffff00'>%s</font></b></p>
	<p>MP: <b><font color='#ffff00'>%s</font></b></p>
	<p>Онлайн: <span class='label label-info'>%s</span></p>
	</font>", $a['rolelevel'], $reborn, GetOccupationName($a['roleprof']), $gender[$a['rolegender']], $klanicon, $klanname, $factrang, $a['hp'], $a['mp'], str_replace('"', "'", GetTime2($a['timeused'], 'ffff00')));	
	if ($touserinfo) 
	{
		$t = sprintf('<div class="userinfo_table"><p>RoleID: <b><font color="#50ff50">%d</font></b> <i class="icon icon-color icon-edit role_edit" data-rel="tooltip" data-original-title="Редактировать персонажа" onclick="EditRole(%d, \'%s\')"></i> <i class="icon icon-color icon-cross role_ban" data-rel="tooltip" data-original-title="Забанить персонажа" onclick="BanRole(%d, \'%s\')"></i></p><p>Ник: <b><font color="#ffff00">%s</font></b>%s</div>', $a['roleid'], $a['roleid'], htmlspecialchars($a['rolename']), $a['roleid'], htmlspecialchars($a['rolename']), htmlspecialchars($a['rolename']), $d);
	} else
	$t = sprintf('title="%s" data-content="%s" data-rel="popover"', htmlspecialchars($a['rolename']), $d);
	return $t;
}

function TopHeader($desc){
	$res = '<table class="table table-bordered table-striped bootstrap-datatable datatable">
	<thead>
	<tr>';
	if (is_array($desc))
	foreach ($desc as $i => $val) {
		$res.=sprintf("<th>%s</th>",htmlspecialchars($val));
	}
	$res .= '</tr>
	</thead>
	';
	return $res;
}

function RaceCount($num){
	global $racetop, $db;
	$res = $db->query("SELECT count(id) FROM `top` WHERE `roleprof`=".$num." AND `rolegender`=0");
	if ($db->errno > 0) die($db->error);
	$row = mysqli_fetch_array($res);
	$cnt1 = $row[0];
	$res = $db->query("SELECT count(id) FROM `top` WHERE `roleprof`=".$num." AND `rolegender`=1");
	if ($db->errno > 0) die($db->error);
	$row = mysqli_fetch_array($res);
	$cnt2 = $row[0];
	$racetop[GetOccupationName($num)] = ($cnt1+$cnt2).'|'.$cnt1.'|'.$cnt2.'|'.$num;
}

function RefreshTop(){
	global $db, $ElementsVer, $racetop, $gender, $occ;
	FillKlanData();
	$servid = $_POST['servid'];	
	$top = array();
	$db->query("set names utf8");

	// Топ Уровень
	$topdata = '';
	$res = $db->query("SELECT * FROM `top` where (`userid` not in (select distinct `userid` from `auth`)) ORDER BY `rolelevel` DESC, `reborn` DESC, `timeused` DESC, roleid ASC LIMIT 0,100");
	if ($db->errno > 0) die($db->error);
	if ($res->num_rows > 0) {
		$topdata = TopHeader(array('№', 'Ник', 'Класс', 'Уровень'));
		$n = 1;
		while ($row = mysqli_fetch_assoc($res)){
			$reborn = '';
			if ($row['reborn']) $reborn = " <span class='label label-important'>РБ</span>";
			$topdata.=sprintf('
			<tr>	
				<td>%s</td>			
				<td %s>%s</td>
				<td>%s</td>			
				<td><span class="label label-success">%s</span>%s</td>
			</tr>',$n, GetPersInfo($row,$servid), htmlspecialchars($row['rolename']), GetOccupationName($row['roleprof']), $row['rolelevel'], $reborn);
			$n++;
		}
		$topdata.="</table>\n";
	}
	$top['level'] = $topdata;

	// Топ Онлайн
	$topdata = '';
	$res = $db->query("SELECT * FROM `top` where (`userid` not in (select distinct `userid` from `auth`)) ORDER BY `timeused` DESC, `rolelevel` DESC, `reborn` DESC, roleid ASC LIMIT 0,100");
	if ($db->errno > 0) die($db->error);
	if ($res->num_rows > 0) {
		$topdata = TopHeader(array('№', 'Ник', 'Класс', 'Онлайн'));
		$n = 1;
		while ($row = mysqli_fetch_assoc($res)){
			$pink = GetTime2($row['timeused'], 'ffff00');
			$topdata.=sprintf('
			<tr>	
				<td>%s</td>			
				<td %s>%s</td>	
				<td>%s</td>		
				<td><span class="label label-info">%s</span></td>
			</tr>',$n, GetPersInfo($row,$servid), htmlspecialchars($row['rolename']), GetOccupationName($row['roleprof']), $pink);
			$n++;
		}
		$topdata.="</table>\n";
	}
	$top['online'] = $topdata;

	// Топ ПК
	$topdata = '';
	$res = $db->query("SELECT * FROM `top` where (`userid` not in (select distinct `userid` from `auth`)) ORDER BY `pkvalue` DESC, `roleid` ASC LIMIT 0,100");
	if ($db->errno > 0) die($db->error);
	if ($res->num_rows > 0) {
		$topdata = TopHeader(array('№', 'Ник', 'Класс', 'Часов ПК'));
		$n = 1;
		while ($row = mysqli_fetch_assoc($res)){
			$pink = GetTime1($row['pkvalue']);
			$topdata.=sprintf('
			<tr>	
				<td>%s</td>			
				<td %s>%s</td>			
				<td>'.GetOccupationName($row['roleprof']).'</td>			
				<td><span class="label label-important">%s</span></td>
			</tr>',$n, GetPersInfo($row,$servid), htmlspecialchars($row['rolename']), $pink);
			$n++;
		}
		$topdata.="</table>\n";
	}
	$top['pk'] = $topdata;

	// Топ Кланы
	$topdata = '';
	$res = $db->query("SELECT * FROM `klan` ORDER BY `terr1` DESC, `terr2` DESC, `terr3` DESC, `members` DESC LIMIT 0,100");
	if ($db->errno > 0) die($db->error);
	if ($res->num_rows > 0) {
		$topdata = TopHeader(array('№', 'Название', 'Мастер', 'Игроков', '1ур', '2ур', '3ур'));
		$n = 1;
		while ($row = mysqli_fetch_assoc($res)){			
			$res1 = $db->query('SELECT * FROM `top` WHERE `roleid`='.$row['masterid']);
			if ($res1->num_rows > 0){
				$row1 = mysqli_fetch_assoc($res1);
				$roleinfo = GetPersInfo($row1, $servid);
			} else $roleinfo = '';
			$topdata.=sprintf('
			<tr>	
				<td>%s</td>		
				<td>%s</td>
				<td %s>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
			</tr>',
			$n,'<img src="klan/geticon.php?servid='.$servid.'&klan='.$row['id'].'" class="imcnt"> '.htmlspecialchars($row['name']), $roleinfo, htmlspecialchars($row['mastername']), $row['members'], $row['terr1'], $row['terr2'], $row['terr3']);
			$n++;
		}
		$topdata.="</table>\n";
	}
	$top['klan'] = $topdata;

	// Топ классы
	$racetop = array();
	foreach ($occ as $i => $val)
	{
		RaceCount($i);
	}	
	arsort($racetop);
	$topdata = '<table class="table table-bordered table-striped"></tr>';
	$a = 1;
	foreach ($racetop as $i=> $val)	{
		$data = explode('|', $val);
		$topdata.=sprintf('
		<td><i class="class_icon class_%s"></i> <span class="label label-info" data-rel="tooltip" data-original-title="Всего">%d</span> <i class="class_icon class_%s"></i><br><span class="label label-success" data-rel="tooltip" data-original-title="Мужчин">%d</span><span class="label label-inverse">%s</span><span class="label label-important" data-rel="tooltip" data-original-title="Женщин">%d</span></td>
', $data[3], $data[0], $data[3], $data[1], $i, $data[2]);
		$a++;
		if ($a >= 14)
		{
			$topdata.='</tr><tr>';
			$a = 1;
		}
	}
	$topdata.="</tr></table>\n";
	$top['class'] = $topdata;
	echo lll_____('top', serialize($top), 60);
	die();
}

function AddFiltering(&$sWhere, $num, $name, $like = false, $val='', $costval = false){
	global $db;	
	if ( isset($_POST['bSearchable_'.$num]) && $_POST['bSearchable_'.$num] == "true" && $_POST['sSearch_'.$num] != '' ){
		if ( $sWhere == "" ){
			$sWhere = "WHERE ";
		} else {
			$sWhere .= " AND ";
		}
		if ($val == '') $value = $_POST['sSearch_'.$num]; else $value = $val;
		if ($costval) {
			$sWhere .= sprintf("(`gold%s`='%s' or `silver%s`='%s')", $name, $db->real_escape_string($value), $name, $db->real_escape_string($value));
		} else {
			if ($like) $sWhere .= sprintf("`%s` LIKE '%%%s%%'", $name, $db->real_escape_string($value)); else
			$sWhere .= sprintf("`%s`='%s'", $name, $db->real_escape_string($value));
		}
	}
}

function ParseCost($cost, &$gold, &$silver){
	$c = explode('|', $cost);
	$gold = (int)$c[0];
	if (count($c) > 1) $silver = (int)$c[1]; else $silver = 0;
}

function GetShowCost($cost, $shownull = false, $dark = false, $br = false){
	$res = '';
	ParseCost($cost, $gold, $silver);
	if (!$dark) {
		$dg = '';
		$ds = '';
	} else {
		$dg = ' gold_dark';
		$ds = ' gold_silver';
	}
	if ($gold != 0 || $shownull) $res = ' <span class="gold'.$dg.'">'.$gold.'</span>';
	if ($silver != 0) $res .= ' <span class="silver'.$ds.'">'.$silver.'</span>';
	if ($br && $res != '') $res = '<br>'.$res;
	return $res;
}

function GetLKLogs(){
	global $db, $gpoint, $spoint;
	if (!isset($_POST['sEcho'])) die();
	$db->query('set names utf8');
	$aColumns = array( 'id', 'userid', 'name', 'userid', 'data', 'ip', 'gold', 'gold_rest', 'desc' );	
	$sIndexColumn = "id";	
	$sTable = "lklogs";	
	$sLimit = "";
	if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".intval( $_POST['iDisplayStart'] ).", ".
			intval( $_POST['iDisplayLength'] );
	}	
	$sOrder = "ORDER by `id` DESC";
	if ( isset( $_POST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
		{
			if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "`".$aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."` ".
					($_POST['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}	
	$sWhere = '';
	AddFiltering($sWhere, 0, 'id');
	AddFiltering($sWhere, 1, 'userid');
	AddFiltering($sWhere, 2, 'name');
	AddFiltering($sWhere, 3, 'userid', false, intval(($_POST['sSearch_3']*16)+16));
	AddFiltering($sWhere, 4, 'data', true);
	AddFiltering($sWhere, 5, 'ip', true);
	AddFiltering($sWhere, 6, '', false, '', true);
	AddFiltering($sWhere, 7, '_rest', false, '', true);
	AddFiltering($sWhere, 8, 'desc', true);

	if (!$sWhere) $sWhere = 'WHERE `userid`>0'; else $sWhere .= ' AND `userid`>0';
	$sQuery = "SELECT `$sTable`.*, `users`.`name` FROM $sTable LEFT JOIN `users` ON `userid`=`users`.`ID` $sWhere $sOrder $sLimit";
	$rResult = $db->query( $sQuery) or die($db->error);
	
	$sQuery = "SELECT count(`$sTable`.`".$sIndexColumn."`) FROM $sTable LEFT JOIN `users` ON `userid`=`users`.`ID` $sWhere";
	$rResultFilterTotal = $db->query( $sQuery) or die($db->error);
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	$sQuery = "SELECT COUNT(`".$sIndexColumn."`) FROM $sTable";
	$rResultTotal = $db->query( $sQuery) or die($db->error);
	$aResultTotal = mysqli_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	$output = array(
		"sEcho" => intval($_POST['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysqli_fetch_array( $rResult ) )
	{		
		$color = '';
		if ($aRow['gold'] > 0 || $aRow['silver'] > 0) $color = 'minus';
		if ($aRow['gold'] < 0 || $aRow['silver'] < 0) $color = 'plus';
		$lkid = intval(($aRow['userid']/16)-1);
		$aRow['desc'] = preg_replace("/№\s+(\d+)\b/u", '<a href="#" onclick="fnlkid(${1})">${0}</a>', $aRow['desc']);
		$row = array(
			$aRow['id'],
			sprintf('<a href="#" onclick="fnuser(%d)">%d</a>',$aRow['userid'],$aRow['userid']),
			'<img src="img/details_open.png"> '.sprintf('<a href="#" onclick="fnlogin(\'%s\')">%s</a>',$aRow['name'],$aRow['name']),
			sprintf('<a href="#" onclick="fnlkid(%d)">%d</a>',$lkid,$lkid),
			$aRow['data'],
			sprintf('<a href="#" onclick="fnip(\'%s\')">%s</a>',$aRow['ip'],$aRow['ip']),
			GetShowCost($aRow['gold'].'|'.$aRow['silver'], true, true),
			GetShowCost($aRow['gold_rest'].'|'.$aRow['silver_rest'], true, true),
			$aRow['desc'],
			$color,
			$aRow['userid']
		);
		$output['aaData'][] = $row;
	}	
	echo json_encode( $output );
	die();
}

function GetAccounts(){
	global $db, $gpoint, $spoint;
	if (!isset($_POST['sEcho'])) die();
	$db->query('set names utf8');
	$aColumns = array( 'ID', 'name', 'ID', 'vkid', 'steamid', 'truename', 'email', 'idnumber', 'creatime', 'lkgold', 'lksilver', 'referal', 'ref_status', 'ref_bonus', 'bonus_data' );	
	$sIndexColumn = "ID";	
	$sTable = "users";	
	$sLimit = "";
	if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".intval( $_POST['iDisplayStart'] ).", ".
			intval( $_POST['iDisplayLength'] );
	}	
	$sOrder = "ORDER by `id` DESC";
	if ( isset( $_POST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
		{
			if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "`".$aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."` ".
					($_POST['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}	
	$sWhere = '';
	AddFiltering($sWhere, 0, 'ID');
	AddFiltering($sWhere, 1, 'name');
	AddFiltering($sWhere, 2, 'ID', false, intval(($_POST['sSearch_3']*16)+16));
	AddFiltering($sWhere, 3, 'vkid');
	AddFiltering($sWhere, 4, 'steamid');
	AddFiltering($sWhere, 5, 'truename', true);	
	AddFiltering($sWhere, 6, 'email', true);
	AddFiltering($sWhere, 7, 'idnumber', true);	
	AddFiltering($sWhere, 8, 'creatime', true);
	AddFiltering($sWhere, 9, 'lkgold');
	AddFiltering($sWhere, 10, 'lksilver');
	AddFiltering($sWhere, 11, 'referal');
	AddFiltering($sWhere, 12, 'ref_status');
	AddFiltering($sWhere, 13, 'ref_bonus');
	AddFiltering($sWhere, 14, 'bonus_data');

	$sQuery = "SELECT `$sTable`.* FROM $sTable $sWhere $sOrder $sLimit";
	$rResult = $db->query( $sQuery) or die($db->error);
	
	$sQuery = "SELECT count(`$sTable`.`".$sIndexColumn."`) FROM $sTable $sWhere";
	$rResultFilterTotal = $db->query( $sQuery) or die($db->error);
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	$sQuery = "SELECT COUNT(`".$sIndexColumn."`) FROM $sTable";
	$rResultTotal = $db->query( $sQuery) or die($db->error);
	$aResultTotal = mysqli_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	$output = array(
		"sEcho" => intval($_POST['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysqli_fetch_array( $rResult ) )
	{		
		$color = ''; $act_txt = '';
		if ($aRow['passwd'] == 'confirm') {
			$color = 'minus';
			$act_txt = sprintf('<br><a href="#" class="btn btn-mini btn-inverse" onclick="act_acc(%d)">Активировать</a>', $aRow['ID']);
		}
		$lkid = intval(($aRow['ID']/16)-1);
		if (!$aRow['referal']) $referal = '<i class="icon icon-color icon-close"></i>'; else $referal = sprintf('<a href="#" onclick="fnuser(%d)">%d</a>',$aRow['referal'],$aRow['referal']);
		if (!$aRow['ref_status']) $ref_status = '<i class="icon icon-color icon-close"></i>'; else $ref_status = '<i class="icon icon-color icon-check"></i>';
		$bonus_text = '';
		if ($aRow['bonus_data']=='') $bonus_data = array(); else $bonus_data = @unserialize($aRow['bonus_data']);
		if (!is_array($bonus_data)) $bonus_data = array();
		if (isset($bonus_data['reg_gold'])) $bonus_text.='Рег.голд получен<br>';
		if (!isset($bonus_data['promo'])) $bonus_data['promo'] = array();
		if (!isset($bonus_data['promo_group'])) $bonus_data['promo_group'] = array();
		if (count($bonus_data['promo'])) {
			$bonus_text .= '<b>Промо-коды:</b> '.implode(', ', $bonus_data['promo']).'<br>';
		}
		if (count($bonus_data['promo_group'])) {
			$bonus_text .= '<b>Группы-Промо:</b> '.implode(', ', $bonus_data['promo_group']).'<br>';
		}
		if ($aRow['vkid']) $vk_txt = sprintf('<div style="float: left"><img src="%s"  border="0"></div><font class="accmanage_font"><a href="https://vk.com/id%s" target="_blank">%s</a><br>%s</font>', $aRow['vkphoto'], $aRow['vkid'], $aRow['vkid'], $aRow['vkname']); else $vk_txt = '';
		if ($aRow['steamid']) $steam_txt = sprintf('<div style="float: left"><img src="%s" border="0"></div><font class="accmanage_font"><a href="http://steamcommunity.com/profiles/%s/" target="_blank">%s</a><br>%s</font>', $aRow['steamphoto'], $aRow['steamid'], $aRow['steamid'], $aRow['steamname']); else $steam_txt = '';
		$row = array(			
			sprintf('<a href="#" onclick="fnuser(%d)">%d</a>',$aRow['ID'],$aRow['ID']),
			'<img src="img/details_open.png"> '.$aRow['name'].$act_txt,
			$lkid,
			$vk_txt,
			$steam_txt,
			$aRow['truename'],
			sprintf('<a href="#" onclick="fnemail(\'%s\')">%s</a>',$aRow['email'],$aRow['email']),
			sprintf('<a href="#" onclick="fnip(\'%s\')">%s</a>',$aRow['idnumber'],$aRow['idnumber']),
			$aRow['creatime'],
			'<span class="gold gold_dark">'.$aRow['lkgold'].'</span>',
			'<span class="silver gold_silver">'.$aRow['lksilver'].'</span>',
			$referal,
			$ref_status,
			$aRow['ref_bonus'],
			$bonus_text,
			$color,
			$aRow['ID']
		);
		$output['aaData'][] = $row;
	}	
	echo json_encode( $output );
	die();
}

function GetTOPLogs($qtop = false){
	global $db, $gpoint, $spoint;
	if (!isset($_POST['sEcho'])) die();
	$db->query('set names utf8');
	$aColumns = array( 'id', 'vote_id', 'data', 'ip', 'name', 'login', 'userid', 'userid', 'vote_type', 'points', 'send_item', 'status' );	
	$sIndexColumn = "id";	
	if ($qtop) $sTable = 'qtop_data'; else $sTable = "mmotop_data";	
	$sLimit = "";
	if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".intval( $_POST['iDisplayStart'] ).", ".
			intval( $_POST['iDisplayLength'] );
	}	
	$sOrder = "ORDER by `id` DESC";
	if ( isset( $_POST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
		{
			if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "`".$aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."` ".
					($_POST['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}	
	$sWhere = '';
	AddFiltering($sWhere, 0, 'id');
	AddFiltering($sWhere, 1, 'vote_id');
	AddFiltering($sWhere, 2, 'data');
	AddFiltering($sWhere, 3, 'ip', true);
	AddFiltering($sWhere, 4, 'name');
	AddFiltering($sWhere, 5, 'login');
	AddFiltering($sWhere, 6, 'userid');	
	AddFiltering($sWhere, 7, 'userid', false, intval(($_POST['sSearch_6']*16)+16));	
	AddFiltering($sWhere, 8, 'votetype');
	AddFiltering($sWhere, 9, 'points');
	AddFiltering($sWhere, 10, 'send_item');
	AddFiltering($sWhere, 11, 'status');
	$sQuery = "SELECT `$sTable`.* FROM $sTable $sWhere $sOrder $sLimit";
	$rResult = $db->query( $sQuery) or die($db->error);
	
	$sQuery = "SELECT count(`$sTable`.`".$sIndexColumn."`) FROM $sTable $sWhere";
	$rResultFilterTotal = $db->query( $sQuery) or die($db->error);
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	$sQuery = "SELECT COUNT(`".$sIndexColumn."`) FROM $sTable";
	$rResultTotal = $db->query( $sQuery) or die($db->error);
	$aResultTotal = mysqli_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	$output = array(
		"sEcho" => intval($_POST['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysqli_fetch_array( $rResult ) )
	{		
		$userid = ($aRow['userid'] == 0)?'':sprintf('<a href="#" onclick="fnuser(%d)">%d</a>',$aRow['userid'],$aRow['userid']);
		$lkid = intval(($aRow['userid']/16)-1);
		$lktext = ($lkid == -1)?'':sprintf('<a href="#" onclick="fnlkid(%d)">%d</a>',$lkid,$lkid);
		if ($aRow['login'] == '') $plus_btn = ''; else $plus_btn = '<img src="img/details_open.png"> ';
		$color = 'plus';
		if ($aRow['status'] != 1 && $aRow['status'] != 7) $color = 'minus';
		$send_item = '<i class="icon icon-color icon-check"></i>';
		if ($aRow['send_item'] == 0) $send_item = '<i class="icon icon-color icon-close"></i>';
		$row = array(
			$aRow['id'],
			$aRow['vote_id'],
			$aRow['data'],
			sprintf('<a href="#" onclick="fnip(\'%s\')">%s</a>',$aRow['ip'],$aRow['ip']),
			htmlspecialchars($aRow['name']),
			$plus_btn.sprintf('<a href="#" onclick="fnlogin(\'%s\')">%s</a>',$aRow['login'],$aRow['login']),
			$userid,			
			$lktext,
			$aRow['vote_type'],			
			GetShowCost($aRow['points'], true, true),
			$send_item,
			$aRow['status'],
			$color,
			$aRow['userid']
		);
		$output['aaData'][] = $row;
	}	
	echo json_encode( $output );
	die();
}

function GetKlanArts(){
	global $db, $gpoint, $spoint;
	if (!isset($_POST['sEcho'])|| !isset($_POST['servid'])) die();
	$servid = $_POST['servid'];
	$db->query('set names utf8');
	$aColumns = array( 'id', 'klanid', 'itemid', 'count', 'maxcount', 'data', 'client_size', 'proctype', 'expire', 'costgold', 'remove_no_klan', 'buycount', 'klan_items`.`desc' );
	$sIndexColumn = "id";
	$sTable = "klan_items";	
	// Paging
	$sLimit = "";
	if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".intval( $_POST['iDisplayStart'] ).", ".
			intval( $_POST['iDisplayLength'] );
	}	
	// Ordering
	$sOrder = "ORDER by `id` DESC";
	if ( isset( $_POST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
		{
			if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "`".$aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."` ".
					($_POST['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}	
	$sWhere = '';
	AddFiltering($sWhere, 0, 'id');
	AddFiltering($sWhere, 1, 'klanid');
	AddFiltering($sWhere, 2, 'itemid');
	AddFiltering($sWhere, 3, 'count');
	AddFiltering($sWhere, 4, 'maxcount');
	AddFiltering($sWhere, 5, 'data', true);
	AddFiltering($sWhere, 6, 'client_size');
	AddFiltering($sWhere, 7, 'proctype');
	AddFiltering($sWhere, 8, 'expire');
	AddFiltering($sWhere, 9, 'costgold');
	AddFiltering($sWhere, 10, 'remove_no_klan');
	AddFiltering($sWhere, 11, 'buycount');
	AddFiltering($sWhere, 12, 'klan_items`.`desc', true);

	$sQuery = "SELECT SQL_CALC_FOUND_ROWS `$sTable`.*, `klan`.`name`, `shop_names`.`name` as `itemname` FROM $sTable LEFT JOIN `klan` ON `klanid`=`klan`.`id` LEFT JOIN `shop_names` ON `itemid`=`shop_names`.`id` $sWhere $sOrder $sLimit";
	$rResult = $db->query( $sQuery) or die($db->error);
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = $db->query( $sQuery) or die($db->error);
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`)
		FROM   $sTable
	";
	$rResultTotal = $db->query( $sQuery) or die($db->error);
	$aResultTotal = mysqli_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	$output = array(
		"sEcho" => intval($_POST['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);	
	while ( $aRow = mysqli_fetch_array( $rResult ) )
	{		
		$cost = GetShowCost($aRow['costgold'].'|'.$aRow['costsilver'], true, true, false);
		if ($aRow['cost_item_id'] == 0 || $aRow['cost_item_count'] == 0) $costitem = ''; else {
			$it1 = GetExtItem($aRow['cost_item_id']);
			$costitemname = $it1->name;			
			$costitem = '<br>'.$aRow['cost_item_count'].' <img src="getitemicon.php?i='.urlencode(base64_encode($it1->icon)).'" border="0" class="item_icon" style="width: 24px; margin: 0"> '.$costitemname.'<br><span class="label label-important">'.$aRow['cost_item_id'].'</span>';
		}
		if ($aRow['itemid'] == 0 || $aRow['count'] == 0) $item = '0'; else {
			$it = GetExtItem($aRow['itemid']);
			$item = '<img src="getitemicon.php?i='.urlencode(base64_encode($it->icon)).'" border="0" class="item_icon" style="width: 24px; margin: 0"> '.$aRow['itemname'].'<br><span class="label label-success">'.$aRow['itemid'].'</span>';
		}
		$row = array(
			$aRow['id'],
			'<b>'.$aRow['klanid'].'</b> <img src="klan/geticon.php?servid='.$servid.'&klan='.$aRow['klanid'].'" class="imcnt"> '.$aRow['name'],
			$item,
			$aRow['count'],
			$aRow['maxcount'],
			'<div style="width: 100px; overflow: auto">'.$aRow['data'].'</div>',
			$aRow['client_size'],
			$aRow['proctype'],
			$aRow['expire'],
			$cost.$costitem,
			($aRow['remove_no_klan'])?'<i class="icon icon-color icon-check"></i>':'<i class="icon icon-color icon-close"></i>',
			$aRow['buycount'],
			$aRow['desc'].' <a class="icon icon-color icon-trash" data-rel="tooltip" data-original-title="Удалить запись" style="float:right" href="index.php?op=act&n=55&num=0&kitem='.$aRow['id'].'" onclick="return CheckDelete()"></a> <i class="icon icon-color icon-edit" data-rel="tooltip" data-original-title="Редактировать запись" style="float:right"></i>',
			$aRow['id']
		);
		$output['aaData'][] = $row;
	}	
	echo json_encode( $output );
	die();
}

function GetPromoCodes(){
	global $db, $gpoint, $spoint;
	if (!isset($_POST['sEcho'])) die();
	$db->query('set names utf8');
	$aColumns = array( 'id', 'code', 'expire', 'group', 'bonus_money_gold', 'bonus_money_silver', 'bonus_item_id', 'multi_user', 'used_userid', 'desc' );
	$sIndexColumn = "id";
	$sTable = "promo_codes";	
	// Paging
	$sLimit = "";
	if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".intval( $_POST['iDisplayStart'] ).", ".
			intval( $_POST['iDisplayLength'] );
	}	
	// Ordering
	$sOrder = "ORDER by `id` DESC";
	if ( isset( $_POST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
		{
			if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "`".$aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."` ".
					($_POST['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}	
	$sWhere = '';
	AddFiltering($sWhere, 0, 'id');
	AddFiltering($sWhere, 1, 'code', true);
	AddFiltering($sWhere, 2, 'expire', true);
	AddFiltering($sWhere, 3, 'group');
	AddFiltering($sWhere, 4, 'bonus_money_gold');
	AddFiltering($sWhere, 5, 'bonus_money_silver');
	AddFiltering($sWhere, 6, 'bonus_item_id');
	AddFiltering($sWhere, 7, 'multi_user');
	AddFiltering($sWhere, 8, 'used_userid');
	AddFiltering($sWhere, 9, 'desc', true);
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS * FROM $sTable $sWhere $sOrder $sLimit";
	//echo $sQuery;
	$rResult = $db->query( $sQuery) or die($db->error);
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = $db->query( $sQuery) or die($db->error);
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`)
		FROM   $sTable
	";
	$rResultTotal = $db->query( $sQuery) or die($db->error);
	$aResultTotal = mysqli_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	$output = array(
		"sEcho" => intval($_POST['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);	
	while ( $aRow = mysqli_fetch_array( $rResult ) )
	{		
		if ($aRow['expire'] > 0) $expire = @date('Y-m-d H:i:s', $aRow['expire']); else $expire = '';
		if ($aRow['bonus_item_id'] == 0 || $aRow['bonus_item_count'] == 0) $item = ''; else {
			$it = GetExtItem($aRow['bonus_item_id']);
			$item = '<b>'.$aRow['bonus_item_count'].'</b> x '.$it->name.'<br><span class="label label-important">'.$aRow['bonus_item_id'].'</span>';
			if ($aRow['bonus_item_expire'] > 0) $item .= ' на '.GetTime($aRow['bonus_item_expire']);
		}
		$color = '';
		if ($aRow['used_userid'] > 0) $color = 'plus';
		if ($aRow['used_userid'] == 0 && $aRow['expire'] > 0 && $aRow['expire'] <= time()) $color = 'minus';
		if ($aRow['multi_user']) {
			if ($aRow['used_userid'] == 0) $userid = ''; else $userid = '<span class="label label-success">'.$aRow['used_userid'].'</span>';
		} else {
			if ($aRow['used_userid'] == 0) $userid = ''; else 
			$userid = '<img src="img/details_open.png"> '.sprintf('<a href="#" onclick="fnuserid(\'%d\')">%d</a>',$aRow['used_userid'],$aRow['used_userid']);
		}
		if ($aRow['bonus_money_gold'] > 0) $gold = '<span class="gold gold_dark">'.$aRow['bonus_money_gold'].'</span>'; else $gold = '';
		if ($aRow['bonus_money_silver'] > 0) $silver = '<span class="silver gold_silver">'.$aRow['bonus_money_silver'].'</span>'; else $silver = '';
		$row = array(
			$aRow['id'],
			$aRow['code'],
			$expire,
			($aRow['group'])?$aRow['group']:'<i class="icon icon-color icon-close"></i>',
			$gold,
			$silver,
			$item,
			($aRow['multi_user'])?'<i class="icon icon-color icon-check"></i>':'<i class="icon icon-color icon-close"></i>',
			$userid,
			$aRow['desc'].' <a class="icon icon-color icon-trash" data-rel="tooltip" data-original-title="Удалить запись" style="float:right" href="#" onclick="return CheckDeletePromo('.$aRow['id'].')"></a> <i class="icon icon-color icon-edit" data-rel="tooltip" data-original-title="Редактировать запись" style="float:right" id="edit_promo_'.$aRow['id'].'"></i>',
			$color,
			$aRow['id'],
			$aRow['used_userid']
		);
		$output['aaData'][] = $row;
	}	
	echo json_encode( $output );
	die();
}

function ShowHistory(){
	global $db, $gpoint, $spoint;
	if (!isset($_POST['id'])||!isset($_POST['sEcho'])) die();
	$id = intval($_POST['id']);
	if ($id < 16) die();
	$db->query('set names utf8');
	$aColumns = array( 'id', 'data', 'ip', 'gold', 'gold_rest', 'desc' );	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";	
	/* DB table to use */
	$sTable = "lklogs";	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".intval( $_POST['iDisplayStart'] ).", ".
			intval( $_POST['iDisplayLength'] );
	}	
	/*
	 * Ordering
	 */
	$sOrder = "ORDER by `id` DESC";
	if ( isset( $_POST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
		{
			if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "`".$aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."` ".
					($_POST['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}	
	$sWhere = '';
	AddFiltering($sWhere, 0, 'id');	
	AddFiltering($sWhere, 1, 'data', true);
	AddFiltering($sWhere, 2, 'ip', true);
	AddFiltering($sWhere, 3, '', false, '', true);
	AddFiltering($sWhere, 4, '_rest', false, '', true);
	AddFiltering($sWhere, 5, 'desc', true);
	/*
	 * SQL queries
	 * Get data to display
	 */
	if ($sWhere == '') $sWhere = 'WHERE `userid`='.$id; else $sWhere .= 'AND `userid`='.$id;
	//$sQuery = "SELECT SQL_CALC_FOUND_ROWS *	FROM $sTable $sWhere $sOrder $sLimit";
	$sQuery = "SELECT * FROM $sTable $sWhere $sOrder $sLimit";
	$rResult = $db->query( $sQuery) or die($db->error);
	
	/* Data set length after filtering */
	//$sQuery = "SELECT FOUND_ROWS()";
	$sQuery = "SELECT count(`".$sIndexColumn."`) FROM $sTable $sWhere";
	$rResultFilterTotal = $db->query( $sQuery) or die($db->error);
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`)
		FROM   $sTable
	";
	$rResultTotal = $db->query( $sQuery) or die($db->error);
	$aResultTotal = mysqli_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	$output = array(
		"sEcho" => intval($_POST['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysqli_fetch_array( $rResult ) )
	{		
		$color = '';
		if ($aRow['gold'] > 0 || $aRow['silver'] > 0) $color = 'minus';
		if ($aRow['gold'] < 0 || $aRow['silver'] < 0) $color = 'plus';
		$row = array(
			$aRow['id'],
			$aRow['data'],
			$aRow['ip'],
			GetShowCost($aRow['gold'].'|'.$aRow['silver'], true, true),
			GetShowCost($aRow['gold_rest'].'|'.$aRow['silver_rest'], true, true),
			$aRow['desc'],
			$color
		);
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
	die();
}

function ShowLoginLog(){
	global $db;
	if (!isset($_POST['id'])||!isset($_POST['sEcho'])) die();
	$id = intval($_POST['id']);
	if ($id < 16) die();
	$aColumns = array( 'data', 'ip', 'action' );	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";	
	/* DB table to use */
	$sTable = "login_log";	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".intval( $_POST['iDisplayStart'] ).", ".
			intval( $_POST['iDisplayLength'] );
	}	
	/*
	 * Ordering
	 */
	$sOrder = "ORDER by `id` DESC";
	if ( isset( $_POST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
		{
			if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "`".$aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."` ".
					($_POST['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}	
	$sWhere = '';
	AddFiltering($sWhere, 0, 'data', true);
	AddFiltering($sWhere, 1, 'ip', true);
	AddFiltering($sWhere, 2, 'action');
	/*
	 * SQL queries
	 * Get data to display
	 */
	if ($sWhere == '') $sWhere = 'WHERE `userid`='.$id; else $sWhere .= 'AND `userid`='.$id;
	//$sQuery = "SELECT SQL_CALC_FOUND_ROWS *	FROM $sTable $sWhere $sOrder $sLimit";
	$sQuery = "SELECT * FROM $sTable $sWhere $sOrder $sLimit";
	$rResult = $db->query( $sQuery) or die($db->error);
	
	/* Data set length after filtering */
	//$sQuery = "SELECT FOUND_ROWS()";
	$sQuery = "SELECT count(`".$sIndexColumn."`) FROM $sTable $sWhere";
	$rResultFilterTotal = $db->query( $sQuery) or die($db->error);
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`)
		FROM   $sTable
	";
	$rResultTotal = $db->query( $sQuery) or die($db->error);
	$aResultTotal = mysqli_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	$output = array(
		"sEcho" => intval($_POST['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysqli_fetch_array( $rResult ) )
	{		
		$color = '';
		if ($aRow['action'] == 1 || $aRow['action'] == 3) $color = 'plus'; else
		if ($aRow['action'] == 4 || $aRow['action'] == 5) $color = 'minus';//plus
		$row = array(			
			$aRow['data'],
			$aRow['ip'],			
			$aRow['action'],
			$color
		);
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
	die();
}

function ShowLoginLogAdm(){
	global $db;
	if (!isset($_POST['sEcho'])) die();
	$aColumns = array( 'id', 'data', 'ip', 'userid', 'login', 'userid', 'action' );	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";	
	/* DB table to use */
	$sTable = "login_log";	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".intval( $_POST['iDisplayStart'] ).", ".
			intval( $_POST['iDisplayLength'] );
	}	
	/*
	 * Ordering
	 */
	$sOrder = "ORDER by `id` DESC";
	if ( isset( $_POST['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
		{
			if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "`".$aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."` ".
					($_POST['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}	
	$sWhere = '';
	AddFiltering($sWhere, 0, 'id');
	AddFiltering($sWhere, 1, 'data', true);
	AddFiltering($sWhere, 2, 'ip', true);
	AddFiltering($sWhere, 3, 'userid');
	AddFiltering($sWhere, 4, 'login');
	AddFiltering($sWhere, 5, 'userid', false, intval(($_POST['sSearch_5']*16)+16));
	AddFiltering($sWhere, 6, 'action');	
	$sQuery = "SELECT * FROM $sTable $sWhere $sOrder $sLimit";
	$rResult = $db->query( $sQuery) or die($db->error);
	$sQuery = "SELECT count(`".$sIndexColumn."`) FROM $sTable $sWhere";
	$rResultFilterTotal = $db->query( $sQuery) or die($db->error);
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`)
		FROM   $sTable
	";
	$rResultTotal = $db->query( $sQuery) or die($db->error);
	$aResultTotal = mysqli_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	$output = array(
		"sEcho" => intval($_POST['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysqli_fetch_array( $rResult ) )
	{		
		$color = '';
		if ($aRow['action'] == 1 || $aRow['action'] == 3) $color = 'plus'; else
		if ($aRow['action'] == 4 || $aRow['action'] == 5) $color = 'minus';
		$lkid = intval(($aRow['userid']/16)-1);
		$row = array(	
			$aRow['id'],		
			$aRow['data'],
			sprintf('<a href="#" onclick="fnip(\'%s\')">%s</a>',$aRow['ip'],$aRow['ip']),
			sprintf('<a href="#" onclick="fnuser(%d)">%d</a>',$aRow['userid'],$aRow['userid']),
			'<img src="img/details_open.png"> '.sprintf('<a href="#" onclick="fnlogin(\'%s\')">%s</a>',$aRow['login'],$aRow['login']),
			sprintf('<a href="#" onclick="fnlkid(%d)">%d</a>',$lkid,$lkid),			
			$aRow['action'],
			$color,
			$aRow['userid']
		);
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
	die();
}

function UserInfo(){
	global $db;
	if (!isset($_POST['id']) && !isset($_POST['login'])) die();
	$servid = $_POST['servid'];
	if (isset($_POST['id'])) $id = $_POST['id']; else $id = '';
	if (isset($_POST['login'])) $login = $_POST['login']; else $login = '';
	$db->query('set names utf8');
	if ($id != '') $res = $db->query("select * from `users` where `ID`=$id"); else
	$res = $db->query("select * from `users` where `name`='".$db->real_escape_string($login)."'");
	if (!$res) die($db->error);
	if ($res->num_rows == 0) die('<div class="alert alert-error"><strong>Ошибка!</strong> Аккаунт не найден</div>');
	$gold_rest = 0; $silver_rest = 0;	
	if ($res->num_rows > 0) {
		$row = mysqli_fetch_assoc($res);
		$ipdata = @unserialize($row['ipdata']);
		if (!is_array($ipdata)) {
			$ipdata = array();
			$ipdata[0] = false;		// Ограничение на вход в ЛК
			$ipdata[1] = array();		// Список разрешенных IP	
			$ipdata[2] = false;		// Ограничение на вход в игру		
		}
		if (!isset($ipdata[2])) $ipdata[2] = false;
		$id = $row['ID'];
		$gold_rest = $row['lkgold'];
		$silver_rest = $row['lksilver'];
		printf('<b><font color="#ffa00f">UserID:</font></b> %s <b><font color="#ffa00f">Login:</font></b> %s<br>', $row['ID'], htmlspecialchars($row['name']));
		printf('<b><font color="#ffff00">E-mail:</font></b> %s <b><font color="#ff00ff">Вопрос:</font></b> %s <b><font color="#00ffff">Ответ:</font></b> %s <b><font color="#adec72">Имя:</font></b> %s <b><font color="#91c7e1">IP регистрации:</font></b> %s <b><font color="#adec72">Регистрация:</font></b> %s<br>', htmlspecialchars($row['email']), htmlspecialchars($row['Prompt']), htmlspecialchars($row['answer']), htmlspecialchars($row['truename']), $row['idnumber'], $row['creatime']);	
	}
	$res = $db->query('select sum(`gold`) as `gold`, sum(`silver`) as `silver` FROM `lklogs` WHERE (`gold`<0 or `silver`<0) AND `userid`='.$id);
	if (!$res) die($db->error);
	$row = mysqli_fetch_assoc($res);
	$gold_add = intval($row['gold']*(-1)); $silver_add = intval($row['silver']*(-1));
	$res = $db->query('select sum(`gold`) as `gold`, sum(`silver`) as `silver` FROM `lklogs` WHERE (`gold`>0 or `silver`>0) AND `userid`='.$id);
	if (!$res) die($db->error);
	$row = mysqli_fetch_assoc($res);
	$gold_spend = intval($row['gold']); $silver_spend = intval($row['silver']);
	printf('<b><font color="#00ff00">Получено:</font></b> <span class="gold">%s</span> <span class="silver">%s</span> <b><font color="#ff0000">Потрачено:</font></b> <span class="gold">%s</span> <span class="silver">%s</span> <b><font color="#ffff00">Остаток:</font></b> <span class="gold">%s</span> <span class="silver">%s</span>', $gold_add, $silver_add, $gold_spend, $silver_spend, $gold_rest, $silver_rest);
	$iptxt = '';
	if ($ipdata[0]) $iptxt .= '<br><font color="#ff5050">Включено ограничение на вход в ЛК по IP</font> - <a href="index.php?op=act&n=75&num=0&id='.$id.'" target="_blank">отключить</a>';
	if ($ipdata[2]) $iptxt .= '<br><font color="#ff5050">Включено ограничение на вход в игру по IP</font> ';
	if (count($ipdata[1])) {
		$iptxt.='<br><font color="#80bfe0">Список разрешенных IP: </font>';
		foreach ($ipdata[1] as $i => $val) {
			$iptxt .= sprintf('<span class="label label-important">%s</span> ', $val);
		}
	}
	if ($iptxt != '') echo $iptxt;
	// Обновляем информацию аккаунта в базе топа
	$res = $db->query('DELETE FROM `top` WHERE `userid`='.$id);
	if (!$res) die($db->error);
	$r = GetUserRoles($id);
	if ($r['retcode'] != 0) die('<br>GetUserRoles error '.$r['retcode']);
	if (count($r['data']) > 0) {
		echo '<table class="userinfo_table" cellpadding=0 cellspacing=0><tbody><tr>';
		foreach ($r['data'] as $i => $val){		
			$rid = $val['id'];
			AddRoleToTop($rid);
			$res = $db->query("SELECT * FROM `top` where roleid=".$rid);
			if (!$res) die($db->error);
			$row = mysqli_fetch_assoc($res);
			echo '<td>'.GetPersInfo($row, $servid, true).'</td>';
		}
		echo '</tr></tbody></table>';
		
	}
	die();
}

function RoleInfo(){
	global $db;
	if (!isset($_POST['roleid'])) die();
	$servid = $_POST['servid'];
	$roleid = $_POST['roleid'];
	$db->query('set names utf8');	
	// Обновляем информацию аккаунта в базе топа
	AddRoleToTop($roleid);	
	$res = $db->query("SELECT * FROM `top` where roleid=".$roleid);
	if (!$res) die($db->error);
	$row = mysqli_fetch_assoc($res);
	echo GetPersInfo($row, $servid, true);
	die();
}

function AddLK(){
	global $db;
	if (!isset($_POST['gold']) || !isset($_POST['silver']) || !isset($_POST['login']) || !isset($_POST['desc']) || !isset($_POST['id'])|| !isset($_POST['ip'])) die('10');
	$login = $db->real_escape_string($_POST['login']);
	$gpoint = intval($_POST['gold']);
	$spoint = intval($_POST['silver']);
	$ip = 'Hidden';
	$desc = $_POST['desc'];
	query2mysql('set names utf8');
	$res = query2mysql("SELECT `ID` FROM `users` WHERE `name`='$login'");
	if ($res->num_rows == 0) die('90'); // Аккаунт не найден
	$row = mysqli_fetch_assoc($res); $id = $row['ID'];
	GiveGold($id, $gpoint, $spoint, $desc, $ip);
	$id = intval($_POST['id']);
	$ip = $_POST['ip'];
	AddLKLogs($id, $ip, 0, 0, "Выдача монет: <span class=\"gold gold_dark\">$gpoint</span> <span class=\"silver gold_silver\">$spoint</span> $login ($desc)");
	die('91');
}

function EditKlanArt(){
	global $db;
	if (!isset($_POST['id'])) die();
	$id = intval($_POST['id']);
	$db->query('set names utf8');
	$res = $db->query("SELECT * FROM `klan_items` WHERE `id`=$id");
	if (!$res) die($db->error);
	if ($res->num_rows == 0) die('<div class="alert alert-error"><strong>Ошибка!</strong> Предмет не найден</div>');
	$row = mysqli_fetch_assoc($res);
	$itemrefresh = "getName('itemid".$id."', 'loaderid".$id."', 'paramid".$id."');";
	$costitemrefresh = "getName('costitemid".$id."', 'costloaderid".$id."', 'costparamid".$id."');";
	$klanrefresh = "getKlanName('klanid".$id."', 'klanloaderid".$id."', 'klanansw".$id."');";
	printf('
	<div class="row-fluid">
			<div class="box span12">
				<div class="box-header well" data-original-title>
					<h2><i class="icon icon-color icon-edit"></i> Редактирование записи № '.$id.'</h2>
				</div>
				<div class="box-content">
					<form name="editform'.$id.'" method="post" action="index.php?op=act&n=53&num=0">
					<input type="hidden" name="kitem" value="'.$id.'">
					<table class="table table-bordered table-striped">
					<tr>
						<td><h3>Klan ID</h3></td>
						<td><input type="text" id="klanid'.$id.'" name="klanid" value="%d" class="config_itemid" onchange="'.$klanrefresh.'"> <span style="display:none" id="klanloaderid'.$id.'"><img src="img/ajax-loaders/ajax-loader-1.gif" border="0" align="absmiddle"/></span> <span id="klanansw'.$id.'"></span></td>
						<td style="text-align:left"><code>ID клана</code></td>
					</tr>
					<tr>
						<td><h3>Item ID</h3></td>
						<td><input type="text" id="itemid'.$id.'" name="itemid" value="%d" class="config_itemid" onchange="'.$itemrefresh.'"> <span style="display:none" id="loaderid'.$id.'"><img src="img/ajax-loaders/ajax-loader-1.gif" border="0" align="absmiddle"/></span> <span id="paramid'.$id.'"></span></td>
						<td style="text-align:left"><code>ID предмета для продажи</code></td>
					</tr>	
					<tr>
						<td><h3>Count</h3></td>
						<td><input type="text" name="count" value="%d"></td>
						<td style="text-align:left"><code>Количество предметов</code></td>
					</tr>
					<tr>
						<td><h3>Max count</h3></td>
						<td><input type="text" name="maxcount" value="%d"></td>
						<td style="text-align:left"><code>Максимальное количество в ячейке</code></td>
					</tr>
					<tr>
						<td><h3>Data</h3></td>
						<td><input type="text" class="span12" name="data" value="%s"></td>
						<td style="text-align:left"><code>Октет предмета</code></td>
					</tr>
					<tr>
						<td><h3>Client size</h3></td>
						<td><input type="text" name="mask" value="%d"></td>
						<td style="text-align:left"><code>Маска предмета</code></td>
					</tr>
					<tr>
						<td><h3>Proctype</h3></td>
						<td><input type="text" name="proctype" value="%d" onkeyup="fillproctype(this.value)" onfocus="showproctype(this);"></td>
						<td style="text-align:left"><code>Привязка предмета</code></td>
					</tr>	
					<tr>
						<td><h3>Expire</h3></td>
						<td><input type="text" name="expire" value="%d"></td>
						<td style="text-align:left"><code>Срок действия временной вещи в секундах</code><br><code>Если 0 - без срока действия</code></td>
					</tr>	
					<tr>
						<td><h3>Стоимость в ЛК монетах</h3></td>
						<td><span class="gold"> <input type="text" name="costgold" value="%d" class="config_cost"></span> <span class="silver"> <input type="text" name="costsilver" value="%d" class="config_cost"></span></td>
						<td style="text-align:left"><code>Стоимость предмета</code></td>
					</tr>
					<tr>
						<td><h3>Стоимость в предметах</h3></td>
						<td>Количество: <input type="text" name="cost_item_count" value="%d" class="config_cost"> ID: <input type="text" name="cost_item_id" value="%d" class="config_itemid" id="costitemid'.$id.'" onchange="'.$costitemrefresh.'"> <span style="display:none" id="costloaderid'.$id.'"><img src="img/ajax-loaders/ajax-loader-1.gif" border="0" align="absmiddle"/></span> <span id="costparamid'.$id.'"></span></td>
						<td style="text-align:left"><code>Стоимость предмета</code></td>
					</tr>
					<tr>
						<td><h3>Remove no klan</h3></td>
						<td><input type="checkbox" name="remove_no_klan" %s></td>
						<td style="text-align:left"><code>Забирать клан-арт у персонажа если он выйдет из клана (изъятие будет совершаться после выхода персонажем из аккаунта)</code><br><code>Стоимость клан-арта будет компенсирована</code></td>
					</tr>
					<tr>
						<td><h3>Desc</h3></td>
						<td><center><textarea class="cleditor" name="desc">%s</textarea></center></td>
						<td style="text-align:left"><code>Описание для администрации</code></td>
					</tr>
					<tr>
						<td><h3>Buy count</h3></td>
						<td><input type="text" name="buycount" value="%d"></td>
						<td style="text-align:left"><code>Счетчик покупок, содержит количество купленных предметов</code><br><code>За единицу берется значение поля <b>Count</b></code></td>
					</tr>
					<tr>
						<td colspan="3"><br><input type="submit" class="btn btn-large btn-primary" value="Сохранить запись"><br><br></td>
					</tr>
					</table>
					</form>
				</div>
			</div>
		</div>
		<script>
		function InitItemID'.$id.'(){
			%s
		}

		setTimeout(InitItemID'.$id.', 100);
		</script>
	',
	$row['klanid'], $row['itemid'], $row['count'], $row['maxcount'], $row['data'], $row['client_size'], $row['proctype'], $row['expire'], $row['costgold'], $row['costsilver'], $row['cost_item_count'], $row['cost_item_id'], ($row['remove_no_klan'])?'checked':'', htmlspecialchars($row['desc']), $row['buycount'], $itemrefresh.$klanrefresh.$costitemrefresh);
}

function EditPromoCode(){
	global $db;
	if (!isset($_POST['id'])) die();
	$id = intval($_POST['id']);
	$db->query('set names utf8');
	$res = $db->query("SELECT * FROM `promo_codes` WHERE `id`=$id");
	if (!$res) die($db->error);
	if ($res->num_rows == 0) die('<div class="alert alert-error"><strong>Ошибка!</strong> Запись не найдена</div>');
	$row = mysqli_fetch_assoc($res);
	$itemrefresh = "getName('promo_item_id".$id."', 'promo_loaderid".$id."', 'promo_paramid".$id."');";	
	echo '<script>
	function SavePromo'.$id.'(){
		var f = document.createElement("form");
		f.name = "editpromoform'.$id.'";
		f.method = "POST";
		f.action = "index.php?op=act&n=69&num=0";
		var E = document.getElementById("promodata'.$id.'");
		var elems = E.getElementsByTagName("input");
		while(elems.length > 0) {			
			f.appendChild(elems[0]);
		}
		elems = E.getElementsByTagName("textarea");
		f.appendChild(elems[0]);
		var formData = new FormData(f);	
		var xhr = new XMLHttpRequest();
		xhr.open("POST", f.action);
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4) {				
				if(xhr.status == 200) {
					var txt = "error"; var txt1 = xhr.responseText;
					if (txt1 == "ok") {
						txt = "success";
						txt1 = "Запись успешно сохранена";
					}
					noty({"text":txt1,"layout":"top","type":txt});					
				}
				$("#edit_promo_'.$id.'").click();
				oTable_promo._fnAjaxUpdate();
			}
		};
		xhr.send(formData);

	}
	</script>';	
	printf('
	<div class="row-fluid">
			<div class="box span12">
				<div class="box-header well" data-original-title>
					<h2><i class="icon icon-color icon-edit"></i> Редактирование записи № '.$id.'</h2>
				</div>
				<div class="box-content">					
					<table class="table table-bordered table-striped" id="promodata'.$id.'">
					<tr>
						<td><h3>Code</h3></td>
						<td>
							<input type="hidden" name="record_id" value="'.$id.'">
							<input type="text" name="promo_code" value="%s"></td>
						<td style="text-align:left"><code>Промо код</code></td>
					</tr>
					<tr>
						<td><h3>Срок годности</h3></td>
						<td><input type="text" name="promo_expire" value="%d"></td>
						<td style="text-align:left"><code>Дата окончания срока действия кода в Unix формате</code><br><code>Если 0 - без срока действия</code></td>
					</tr>	
					<tr>
						<td><h3>Группа кодов</h3></td>
						<td><input type="text" name="promo_group" value="%d"></td>
						<td style="text-align:left"><code>0 - без группы</code><br><code>Используется для объединения кодов в группы</code><br><code>Для кодов из одной группы действует ограничение использования не более одного кода из группы на аккаунт</code></td>
					</tr>
					<tr>
						<td><h3>Бонус ЛК</h3></td>
						<td><span class="gold"> <input type="text" name="promo_gold" value="%d" class="config_cost"></span> <span class="silver"> <input type="text" name="promo_silver" value="%d" class="config_cost"></span></td>
						<td style="text-align:left"><code>Награда за код в ЛК монетах</code></td>
					</tr>
					<tr>
						<td><h3>Бонус предмет</h3></td>
						<td><input type="text" id="promo_item_id'.$id.'" name="promo_item_id" value="%d" class="config_itemid" onchange="getName(\'promo_item_id'.$id.'\', \'promo_loaderid'.$id.'\', \'promo_paramid'.$id.'\');"> <span style="display:none" id="promo_loaderid'.$id.'"><img src="img/ajax-loaders/ajax-loader-1.gif" border="0" align="absmiddle"/></span> <span id="promo_paramid'.$id.'"></span></td>
						<td style="text-align:left"><code>ID предмета награды за код</code></td>
					</tr>
					<tr>
						<td><h3>Count</h3></td>
						<td><input type="text" name="promo_item_count" value="%d"></td>
						<td style="text-align:left"><code>Количество предметов</code></td>
					</tr>
					<tr>
						<td><h3>Max count</h3></td>
						<td><input type="text" name="promo_item_maxcount" value="%d"></td>
						<td style="text-align:left"><code>Максимальное количество в ячейке</code></td>
					</tr>
					<tr>
						<td><h3>Data</h3></td>
						<td><input type="text" class="span12" name="promo_item_data" value="%s"></td>
						<td style="text-align:left"><code>Октет предмета</code></td>
					</tr>
					<tr>
						<td><h3>Client size</h3></td>
						<td><input type="text" name="promo_item_client_size" value="%d"></td>
						<td style="text-align:left"></td>
					</tr>
					<tr>
						<td><h3>Proctype</h3></td>
						<td><input type="text" name="promo_item_proctype" value="%d" onkeyup="fillproctype(this.value)" onfocus="showproctype(this);"></td>
						<td style="text-align:left"><code>Привязка предмета</code></td>
					</tr>	
					<tr>
						<td><h3>Expire</h3></td>
						<td><input type="text" name="promo_item_expire" value="%d"></td>
						<td style="text-align:left"><code>Срок действия предмета в секундах</code><br><code>Если 0 - без срока действия</code></td>
					</tr>
					<tr>
						<td><h3>Многоразовый</h3></td>
						<td><center><input name="promo_multi_user" type="checkbox" %s data-no-uniform="true" class="iphone-toggle"></center></td>
						<td style="text-align:left"><code>Если включено - код может быть использован несколькими игроками (не более одного использования на аккаунт)</code><br><code>а в поле Used UserID будет записываться количество использований, а не ID аккаунта</code></td>
					</tr>
					<tr>
						<td><h3>Used UserID</h3></td>
						<td><input type="text" name="promo_used_userid" value="%d"></td>
						<td style="text-align:left"><code>UserID использовавшего код</code><br><code>Если многоразовый - количество использований</code></td>
					</tr>
					<tr>
						<td><h3>Desc</h3></td>
						<td><center><textarea name="promo_desc">%s</textarea></center></td>
						<td style="text-align:left"><code>Описание для администрации</code></td>
					</tr>					
					<tr>
						<td colspan="3"><br><input type="button" class="btn btn-large btn-primary" onclick="SavePromo'.$id.'()" value="Сохранить запись"><br><br></td>
					</tr>
					</table>
					<script>
					function InitPromoItemID'.$id.'(){
						%s
					}
			
					setTimeout(InitPromoItemID'.$id.', 100);
					</script>					
				</div>
			</div>
		</div>		
	', 
	$row['code'], $row['expire'], $row['group'], $row['bonus_money_gold'], $row['bonus_money_silver'], $row['bonus_item_id'], $row['bonus_item_count'], $row['bonus_item_max_count'], $row['bonus_item_data'], $row['bonus_item_client_size'], $row['bonus_item_proctype'], $row['bonus_item_expire'], ($row['multi_user'])?'checked':'', $row['used_userid'], htmlspecialchars($row['desc']), $itemrefresh);
	die();
}

function GetUserID($roleid)
{
	//if ($ProtocolVer < 27) return (floor($roleid/16)*16);
	$b = GetRoleBase($roleid);	
	return $b['value']['userid'];
}

function GetVoteFromBase($vote, $tbl)
{
	global $db;
	$sql = sprintf("SELECT * FROM `".$tbl."` WHERE `vote_id`=%d", $vote['vote_id']);
	$res = $db->query($sql);
	if (!$res) die($db->error);
	if ($res->num_rows) return true; else return false;
}

function AddAuto($vote, $login, $userid, $status, $tbl, $points = 0, $send_item = 0)
{
	global $db;
	$sql = sprintf("INSERT INTO `".$tbl."` (`vote_id`,`data`,`ip`,`name`,`login`,`userid`,`vote_type`,`points`,`send_item`,`status`) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')", $db->real_escape_string($vote['vote_id']), $db->real_escape_string($vote['data']), $db->real_escape_string($vote['ip']), $db->real_escape_string($vote['login']), $db->real_escape_string($login), $db->real_escape_string($userid), $db->real_escape_string($vote['vote_type']), $db->real_escape_string($points), $db->real_escape_string($send_item), $db->real_escape_string($status));
	$result = $db->query($sql);
	if (!$result) die($db->error);
}

function GiveMoney($login, $id, $vote, $senditem, $item1, $item2, $item3, $item4, $reason, $tbl, $nickname = false)
{
	// Status 0 - not found, 1 - Success, 2 - Limit IP, 3 - Limit login, 4 - No Active Role, 5 - Error send item, 6 - Mail Limit, 7 - Success NickName, 8 - No VK, 9 - No Steam
	global $cnt, $fp, $q, $gpoint, $spoint, $db;
	ShowCost($vote['points']);
	$CashLeft = GetCashFromAcc($id,$login);
	$i = $CashLeft[2];
	$l = $CashLeft[3];
	$code = -1;
	$send_item = 0;
	$points = 0;
	if ($gpoint > 0 || $spoint > 0) {
		if ($login == '') $sql = "update `users` set lkgold=lkgold+".$gpoint.", lksilver=lksilver+".$spoint." where `ID`='".$db->real_escape_string($id)."'"; else
		$sql = "update `users` set lkgold=lkgold+".$gpoint.", lksilver=lksilver+".$spoint." where `name`='".$db->real_escape_string($login)."'";	
		$result = $db->query($sql);
		if ($db->errno > 0) {
			echo $db->error()." No\n";
			$code = 0;
		} else {
			$points = $vote['points'];
			$CashLeft = GetCashFromAcc($id,$login);
			$i = $CashLeft[2];
			$l = $CashLeft[3];
			$sql = "INSERT INTO `lklogs` (`userid`,`data`,`ip`,`gold`,`silver`, `gold_rest` ,`silver_rest`,`desc`) VALUES (".$CashLeft[2].",now(),'".$db->real_escape_string($vote['ip'])."','-".$gpoint."','-".$spoint."', ".$CashLeft[0].", ".$CashLeft[1].",'Голосование в ".$reason." ".$vote['vote_id']."')";	
			$db->query($sql);
			$code = 1;
			echo " Ok\n";
		}
	}
	if ($senditem) {
		$roleid = GetActiveRole($i);
		if ($roleid) {
			if ($vote['vote_type'] == 1) $q = SysSendMail(0, 32, 3, $roleid, 'Бонус', 'За голосование на '.$reason.'.', $item1, 0); else
			if ($vote['vote_type'] == 2) $q = SysSendMail(0, 32, 3, $roleid, 'Бонус', 'За голосование на '.$reason.'.', $item2, 0); else
			if ($vote['vote_type'] == 3) $q = SysSendMail(0, 32, 3, $roleid, 'Бонус', 'За голосование на '.$reason.'.', $item3, 0); else
			if ($vote['vote_type'] == 4) $q = SysSendMail(0, 32, 3, $roleid, 'Бонус', 'За голосование на '.$reason.'.', $item4, 0);
			if ($q['retcode'] != 0) {
				if ($code == -1) {
					if ($q['retcode'] == 217) $code = 6; else
					$code = 5;
				}
			} else
			$send_item = 1;
			if ($code == -1) $code = 1;
			echo " Send item ok\n";
		} else {
			$code = 4;
			echo " Active role not found\n";
		}
	}
	if ($code == 1 && $nickname) $code = 7;
	AddAuto($vote, $l, $i, $code, $tbl, $points, $send_item);
	$cnt++;
}

function CheckLoginTop($login)
{
	global $db;
	$sql = sprintf("SELECT `ID` FROM `users` WHERE `name`='%s'", $db->real_escape_string($login));
	$result = $db->query($sql);
	if (!$result) die($db->error);
	return $result->num_rows;
}

function CheckVkSteamTop($login, $top_bonus_vk_only, $top_bonus_steam_only)
{
	global $db;
	$sql = sprintf("SELECT `vkid`, `steamid` FROM `users` WHERE `name`='%s'", $db->real_escape_string($login));
	$result = $db->query($sql);
	if (!$result) die($db->error);
	if (!$result->num_rows) return 0;
	$row = mysqli_fetch_assoc($result);
	if (!$row['vkid'] && $top_bonus_vk_only) return 8;
	if (!$row['steamid'] && $top_bonus_steam_only) return 9;
	return 0;
}

function UnpackItem($i)
{
	global $Structures;
	$pp = new Protocols();
	$item = $pp->unmarshal(my_hex2bin($i), $Structures['GRoleInventory']);
	unset($pp);
	if ($item['expire_date']) $item['expire_date'] += time();
	return $item;
}

function TopBonus($is_mmotop)
{
	global $db, $cnt;
	extract($_POST);
	$item1 = InitItem();
	$item2 = InitItem();
	$item3 = InitItem();
	$item4 = InitItem();
	if ($is_mmotop) {
		$statlink = $mmotop_statlink;
		$tbl = 'mmotop_data';
		$sendmsg = $send_mmotop_message;
		$msg = $mmotop_message;
		$senditem = $send_mmotop_bonusitem;
		$item1 = UnpackItem($mmotop1_item); $item2 = UnpackItem($mmotop2_item); $item3 = UnpackItem($mmotop3_item); $item4 = UnpackItem($mmotop4_item);	
		$cost1 = $mmotop_cost1; $cost2 = $mmotop_cost2; $cost3 = $mmotop_cost3; $cost4 = $mmotop_cost4;
		$pattern = "/(\d+)\s+(\d+\.\d+\.\d+)\s(\d+:\d+:\d+)\s+(\d+\.\d+\.\d+\.\d+)\s+(.+)\s(\d+)/";
		$explodetext = chr(10);
		$reason = 'mmotop';
	} else {		
		$statlink = $qtop_statlink;
		$tbl = 'qtop_data';
		$sendmsg = $send_qtop_message;
		$msg = $qtop_message;
		$senditem = $send_qtop_bonusitem;
		$item1 = UnpackItem($qtop1_item); $item2 = UnpackItem($qtop2_item);		
		$cost1 = $qtop_cost1; $cost2 = $qtop_cost2;
		$pattern = "/(\d+)\|\|(\d+\.\d+\.\d+)\|\|(\d+:\d+:\d+)\|\|(\d+\.\d+\.\d+\.\d+)\|\|(.+)\|\|(\d+)/";
		$explodetext = chr(13).chr(10);
		$reason = 'qtop';
	}
	if ($statlink == '') die('Stat link is empty');
	if (!$db->query('set names utf8')) die($db->error);
	if ($top_log_lifetime > 0){	
		// Чистка старых логов
		$dm = @localtime(time(), true);
		$dt = @mktime($dm['tm_hour'], $dm['tm_min'], $dm['tm_sec'], $dm['tm_mon']+1-$top_log_lifetime, $dm['tm_mday'], 1900 + $dm['tm_year']);
		$sql = sprintf("DELETE FROM `%s` WHERE `data`<='%s'", $tbl, @date('Y-m-d H:i:s', $dt));
		if (!$db->query($sql)) die($db->error);		
	}
	$ch = curl_init();
	if (!$ch) die('Curl init error');
	curl_setopt($ch, CURLOPT_URL, $statlink);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	$data = curl_exec($ch);
	if ($data === false) die(curl_error($ch));
	curl_close($ch);
	if ($data == '') die('Stat is empty');
	$data = explode($explodetext, $data);
	$cnt = 0;
	$vote_ip_array = array();			// Массив с счетчиками айпи
	$vote_login_array = array();			// Массив с счетчиками логинов
	$vote = array();	
	foreach($data as $index => $val){
		if (!preg_match($pattern, $val, $v)) continue;
		$vote['vote_id'] = $v[1];
		$d = @strtotime($v[2].' '.$v[3]); 	
		$vote['data'] = @date('Y-m-d H:i:s', $d);
		$dm = @localtime(time(), true);
		$dt = @mktime($dm['tm_hour'], $dm['tm_min'], $dm['tm_sec'], $dm['tm_mon']+1, $dm['tm_mday'] - $num_day_process, 1900 + $dm['tm_year']);
		if ($d < $dt) {	
			continue;
		}
		$vote['ip'] = $v[4];
		$vote['login'] = $v[5];
		$vote['vote_type'] = $v[6];
		if ($vote['vote_type']==1) $vote['points'] = $cost1; else
		if ($vote['vote_type']==2) $vote['points'] = $cost2; else
		if ($vote['vote_type']==3) $vote['points'] = $cost3; else
		if ($vote['vote_type']==4) $vote['points'] = $cost4;
		$voteday = substr($v[2], 0, 2);		// день месяца голосования
		$found = false;
		$nickname = false;
		$login = mb_strtolower($vote['login']);
		if (!CheckLoginTop($login)) {	// Ищем по нику
			$f = GetRoleId($vote['login']);
			if ($f['roleid'] == -1) {
				$found = false;
			} else {
				$id = GetUserID($f['roleid']);
				$CashLeft = GetCashFromAcc($id, '');
				if ($CashLeft[3]) {
					$login = $CashLeft[3];
					$found = true;
					$nickname = true;
				}
			}			
		} else $found = true;
		if (!isset($vote_ip_array[$voteday])) $vote_ip_array[$voteday] = array();
		if (!isset($vote_ip_array[$voteday][$vote['ip']])) $vote_ip_array[$voteday][$vote['ip']] = 0;
		if (!isset($vote_login_array[$voteday])) $vote_login_array[$voteday] = array();
		if (!isset($vote_login_array[$voteday][$login])) $vote_login_array[$voteday][$login] = 0;
		if ($vote['vote_type'] == 1){		// Если обычное голосование добавляем в статистику лимита			
			$vote_ip_array[$voteday][$vote['ip']] += 1;			
			$vote_login_array[$voteday][$login] += 1;
		}
		if (GetVoteFromBase($vote, $tbl)) {
			continue;
		}
		$stat = 0;
		if ($vote['vote_type'] == 1) {
			if ($vote_ip_array[$voteday][$vote['ip']] > $max_votes_from_ip) $stat = 2;
			if ($vote_login_array[$voteday][$login] > $max_votes_from_login) $stat = 3;		
		}
		if ($stat == 0) $stat = CheckVkSteamTop($login, $top_bonus_vk_only, $top_bonus_steam_only);
		if ($stat > 0) {
			$CashLeft = GetCashFromAcc(0, $login);
			AddAuto($vote, $CashLeft[3], $CashLeft[2], $stat, $tbl, 0, 0);
			echo($vote['vote_id']." ".$vote['login']." Limit reached\n");
			continue;
		}		
		echo($vote['vote_id']." ".$vote['login']);
		if (!$found) {	// Ищем по нику
			AddAuto($vote, '', 0, 0, $tbl, 0, 0);
			echo " Not found\n";			
		} else {
			GiveMoney($login, 0, $vote, $senditem, $item1, $item2, $item3, $item4, $reason, $tbl, $nickname);
		}
	}	
	if ($cnt > 0 && $sendmsg) {
		broadcast(9,0,32,0,sprintf($msg, $cnt));
	}
	die();
}

function AddLoginLog()
{
	global $db;
	extract($_POST);
	$res = $db->query("INSERT INTO `login_log` (`data`,`ip`,`userid`,`login`,`action`) VALUES (now(),'".$db->real_escape_string($ip)."','".$db->real_escape_string($userid)."','".$db->real_escape_string($login)."','".$db->real_escape_string($action)."')");	
	die();
}

function FillKlanData()
{
	global $db, $Structures;
	set_time_limit(490);
	// Инициализируем структуры
	$p = new Protocols();
	unset($p);
	// Обновляем информацию о кланах
	$db->query("SET NAMES utf8");
	$db->query("TRUNCATE TABLE `klan`;");
	if ($db->errno > 0) die($db->error);
	// Получаем список кланов пакетами
	$res = WalkTable('faction', ['val' => 'int'], $Structures['GFactionInfo']);
	foreach ($res as $i => $val){		
		if ($val['value']['master'] != 0) {
			$b = GetRoleBase($val['value']['master']);
			$name = $db->real_escape_string($b['value']['name']);
		} else $name = '';
		$members = 0;
		foreach ($val['value']['member'] as $i1 => $val1)
		{
			$data = DBFamilyGet($val1['fid']);
			$members += count($data['value']['member']);
		}		
		$sql = "INSERT INTO `klan` (`id` ,`name` ,`desc` ,`level` ,`masterid` ,`mastername` ,`members`) VALUES ('".$val['value']['fid']."', '".$db->escape_string($val['value']['name'])."', '".$db->real_escape_string($val['value']['announce'])."', '".$val['value']['level']."', '".$val['value']['master']."', '$name','".$members."');";
		$db->query($sql);
		if ($db->errno > 0) die($db->error);
	}
	
	// Обновляем информацию о территориях
	$t = DBTerritoryListLoad([1]);
	foreach ($t['store']['tlist'] as $i => $val){
		if ($val['owner'] != 0){
			switch ($val['assis_drawn_num']){
				case 2:$sql="update `klan` set terr3=terr3+1 where `id`=".$val['owner'];break;
				case 4:$sql="update `klan` set terr2=terr2+1 where `id`=".$val['owner'];break;
				case 8:$sql="update `klan` set terr1=terr1+1 where `id`=".$val['owner'];break;
				default: die('unknown territory level '.$val['assis_drawn_num']);
			}
			$db->query($sql);
			if ($db->errno > 0) die($db->error);
		}
	}
}

function find_and_remove_items(&$it, $itemsid, $role_faction, $id, $rid, $rname, $desc, $is_banned)
{
	global $db;
	$cnt = 0;
	foreach ($it as $i => $val){
		if (array_key_exists($val['id'], $itemsid)) {
			if ($itemsid[$val['id']]['klanid'] != $role_faction[$rid]) {
				if ($is_banned==0) isonline($id, $rid, 16, false);
				$cnt+=$val['count'];
				unset($it[$i]);
				$item = InitItem();
				if ($itemsid[$val['id']]['cost_item_id'] > 0) {
					$item['id'] = $itemsid[$val['id']]['cost_item_id'];
					$item['count'] = $itemsid[$val['id']]['cost_item_count'];
					$item['max_count'] = $itemsid[$val['id']]['cost_item_count'];
				}
				$gpoint = $itemsid[$val['id']]['costgold']; $spoint = $itemsid[$val['id']]['costsilver'];
				GiveGold($id, $gpoint, $spoint, 'Изъятие клан-арта', $ip);				
				$q = SysSendMail(0, 32, 3, $rid,'Изъятие клан-арта','Вам была возвращена стоимость клан-арта', $item, 0);
				broadcast(9, 0, 40, 0, sprintf("У игрока %s только что был изъят клан-арт", $rname));
				printf("Remove klan-art from %s, Role: %d %s, RemoveItemID: %d, SendItemID: %d\n", $desc, $rid, $rname, $val['id'], $itemsid[$val['id']]['cost_item_id']);
				$cnt++;
			}
		}
	}
	return $cnt;
}

function CheckSimilarIP($userid1, $userid2)
{
	global $db;
	$sql = "SELECT DISTINCT `ip` FROM `login_log` WHERE `ip`<>'Hidden' AND `ip`<>'' AND `userid`=%d";
	$ips = array();
	$res = $db->query(sprintf($sql, $userid1));
	if (!$res) die($db->error);
	while ($row = mysqli_fetch_assoc($res)){
		array_push($ips, $row['ip']);
	}
	$res = $db->query(sprintf($sql, $userid2));
	if (!$res) die($db->error);
	while ($row = mysqli_fetch_assoc($res)){
		if (in_array($row['ip'], $ips)) {
			return $row['ip'];
		}
	}	
	return false;
}

function UpdateUserInfo()
{
	global $db;
	if (!isset($_POST['id'])) die('Wrong user id'); else $id = $_POST['id'];
	if ($id == 0) die('UserID 0');
	$db->query("SET NAMES utf8");	
	$r = GetUserRoles($id);
	if ($r['retcode'] != 0) die('<br>GetUserRoles error '.$r['retcode']);
	$num = 0; $rolenames = array();
	$role_faction = array();
	if (!count($r['data'])) die('No roles found on account');
	foreach ($r['data'] as $i => $val){		
		$rid = $val['id'];			
		$role_faction[$rid] = AddRoleToTop($rid);
		$num++;
		array_push($rolenames, $val['name']);
	}
	echo "$num roles info updated: ".implode(", ", $rolenames)."\n";

	// Проверка выдачи бонуса рефералу	
	$ref_level_bonus_enabled = $_POST['ref_level_bonus_enabled'];
	if ($ref_level_bonus_enabled) {
		$ref_require_rb = $_POST['ref_require_rb'];
		$ref_require_level = $_POST['ref_require_level'];
		$item = unserialize($_POST['ref_item']);		
		if ($item['expire_date']) $item['expire_date'] += time();
		$sql="SELECT `referal`,`ref_status` FROM `users` WHERE `ID`=".$id;
		$result = @$db->query($sql);
		if (!$result) die($db->error);
		if ($result->num_rows){
			$row = mysqli_fetch_assoc($result);
			if ($row['ref_status'] == 0 && $row['referal'] != 0) {
				$a = GetRefData($id);
				$refacc = $a[0];
				$refpers = $a[1];
				$mt = $a[2];
				$rb = $a[3];
				if ($refacc > 0 && $refpers > 0) {			
					$fnd = '';
					foreach ($r['data'] as $i => $val){
						$r1 = GetRole($val['id'], 63);
						if ($r1['value']['status']['level'] >= $ref_require_level)
						{
							if ($ref_require_rb)
							{
								if ($r1['value']['status']['reborndata']) $fnd = $val['name'];
							} else $fnd = $val['name'];
						}
					}
					if ($fnd != ''){
						$checkIp = CheckSimilarIP($id, $refacc);
						if ($checkIp) {
							$db->query('UPDATE `users` SET `ref_status`=2 WHERE `ID`='.$id);
							printf("No bonus send to referal (IP match): %s %s - %s %s (%s)\n", $id, $fnd, $refacc, $refpers, $checkIp);
							$txt = 'Реферальный бонус за реферала №'.($id/16-1).' не начислен, совпадение IP входа '.$checkIp;
							AddLKLogs($id, '', 0, 0, $txt);
							AddLKLogs($refacc, '', 0, 0, $txt);
						} else {
							$q = SysSendMail(0, 32, 3, $refpers, 'Бонус', sprintf('За приглашенного игрока %s, который достиг '.$ref_require_level.' уровня', $fnd), $item, 0);
							$err = '';
							if ($q['retcode'] == 217) $err = ' fail, mailbox is full'; else
							if ($q['retcode'] == 0) $db->query('UPDATE `users` SET `ref_status`=1 WHERE `ID`='.$id); else
							$err = ' fail, retcode '.$q['retcode'];
							printf("\nSend referal bonus%s: %s %s - %s %s\n", $err, $id, $fnd, $refacc, $refpers);
							if (!$err)
							{
								$r1 = GetRole($refpers, 63);
								broadcast(9,0,40,0,sprintf("%s получил бонус за приглашенного игрока %s", $r1['value']['name'], $fnd));
							}
						}
					}
				}
			}
		}		
	}
	// Проверка и изъятие запрещенных клан-артов	
	$cnt = 0;
	$itemsid = array();	
	$sql="SELECT * FROM `klan_items` WHERE `remove_no_klan`=1";
	$result = @$db->query($sql);
	if (!$result) die($db->error);	
	if ($result->num_rows){
		// Строим список артов
		while ($row = mysqli_fetch_assoc($result)){
			$itemsid[$row['itemid']] = $row;
		}		
		foreach ($r['data'] as $i => $val){		
			$rid = $val['id'];
			$rname = $val['name'];
			$rd = GetRoleData($rid);
			if ($rd['retcode'] != 0) die('Read role '.$id.' error');
			$cnt = 0;
			$cnt+=find_and_remove_items($rd['value']['pocket']['items'], $itemsid, $role_faction, $id, $rid, $rname, 'pocket', $cnt);
			$cnt+=find_and_remove_items($rd['value']['pocket']['pocket_items'], $itemsid, $role_faction, $id, $rid, $rname, 'pocket_items', $cnt);
			$cnt+=find_and_remove_items($rd['value']['pocket']['fashion'], $itemsid, $role_faction, $id, $rid, $rname, 'fashion', $cnt);
			$cnt+=find_and_remove_items($rd['value']['storehouse']['items'], $itemsid, $role_faction, $id, $rid, $rname, 'storehouse', $cnt);
			$cnt+=find_and_remove_items($rd['value']['storehouse']['items2'], $itemsid, $role_faction, $id, $rid, $rname, 'storehouse2', $cnt);
			$cnt+=find_and_remove_items($rd['value']['pocket']['equipment'], $itemsid, $role_faction, $id, $rid, $rname, 'equipment', $cnt);
			if ($cnt > 0) {
				$rd = PutRoleData($rid, $rd['value']);
				if ($rd['retcode'] != 0) die('Role '.$rid.' save error '.$rd['retcode']."\n");
			}
		}
	}
	die();
}

function MakeNames()
{
	global $db;
	$fname = 'items_ext.txt';
	if (!file_exists($fname)) die('No input file');
	$handle = @fopen($fname, "r");
	$q = 0; $q1 = 0;
	if (!$handle) die('Error open file');
	$db->query("SET NAMES utf8");
	$res = $db->query('TRUNCATE TABLE `shop_names`');
	if (!$res) die($db->error);
	while (!feof($handle)) {
		$line = fgets($handle);
		if ($line == '') continue;
		$a = explode( "|", $line);
		if (count($a)==4) {
			$sql = sprintf("INSERT INTO `shop_names` (`id`,`name`,`icon`,`list`) VALUES (%d, '%s', '%s', %d)", $a[0], $db->real_escape_string(trim($a[1])), $db->real_escape_string(trim($a[2])), trim($a[3]));
			$db->query($sql);
			if ($db->errno != 0 ) {
				echo $sql.'<br>';
				die($db->error);
			}
			$q++;
		} else {
			printf("Error line: %s<br>", $line);
			$q1++;
		}
	}
	fclose($handle);
	printf("Success lines: %d, Error lines: %d", $q, $q1);
}

function GetErrorTxt($num,$custom='')
{
	$err = '<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">×</button><strong>Ошибка!</strong> %s</div>';
	$succ = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button><strong>Готово!</strong> %s</div>';
	switch ($num){
		case 88: return sprintf($err, $custom);
		case 89: return sprintf($succ, $custom);
	}
}

function CheckArchive($fn)
{
	global $path, $answer, $ttt;
	$error = '';
	$res = true;
	$error = '';
	$archive = new PclZip($fn);
	$files = $archive->listContent();
	$addmsg = false;
	if (!is_array($files) || count($files)==0 || !file_exists($fn)) {
		$error = 'Ошибка открытия файла '.$fn;
		$res = false;
	} else {
		foreach ($files as $i => $val)
		{
			if (file_exists($val['filename'])) {
				if (!is_writable($val['filename'])) {
					$res = false;
					$error .= 'Нет прав на запись файла <code>'.$val['filename'].'</code><br>';
					$addmsg = true;
				}
			} else {
				$data = pathinfo($val['filename']);			
				$p = (isset($data['dirname']) && $data['dirname'] != '.')?$data['dirname']:'';
				$p = $path.'/'.$p;
				if (!is_writable($p)) {
					$res = false;
					$error .= 'Нет прав на запись в папку <code>'.$p.'</code><br>';
					$addmsg = true;
				}
			}
		}
		if ($addmsg) {
			$userinfo = posix_getpwuid(posix_getuid());
			$error .= '<br>Выполните команду <code>chown -R '.$userinfo['name'].' '.$path.'</code> (или установите права на запись) и попробуйте запустить обновление ещё раз';
		}
	}
	if (!$res) $answer.=GetErrorTxt(88, $error);
	return $res;
}

function TrySave($fn, $data)
{
	global $answer, $ttt;
	$fp = @fopen($fn, "w");
	if (!$fp) {
		$answer.=GetErrorTxt(88, 'Ошибка создания файла '.$fn);
		return false;
	}
	fwrite($fp, $data);
	fclose($fp);
	return true;
}

function Update()
{
	global $path, $db, $answer, $ttt;
	$answer = ''; $ttt = '';
	if (!isset($_POST['server_sql']) || !isset($_POST['server_zip'])) die('Input data fail');	
	$data = pathinfo($_SERVER['SCRIPT_FILENAME']);
	$path = (isset($data['dirname']) && $data['dirname'] != '.')?$data['dirname']:'';
	$state_head = '<div class="clear"></div>
	<div class="box-header well" data-original-title>
		<h2><i class="%s"></i> %s</h2>
		<div class="box-icon">
			<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-down"></i></a>
		</div>
	</div>
	<div class="box-content" style="display:none">';
	$state_footer = '</div><div class="clear"></div>';
	if ($_POST['server_zip'] != '') {			
		$fn = $path.'/server.zip';
		if (!TrySave($fn, base64_decode($_POST['server_zip']))) MakeUpdateAnswer(0, $answer, $ttt);
		if (!CheckArchive($fn)) {
			if (!unlink($fn)) $answer.=GetErrorTxt(88, 'Ошибка удаления файла '.$fn);
			MakeUpdateAnswer(0, $answer, $ttt);
		}
		$archive = new PclZip($fn);
		$r = $archive->extract(PCLZIP_OPT_REPLACE_NEWER);
		$succ_files = 0; $fail_files = 0;
		$answer.=sprintf($state_head, 'icon-folder-open', 'Обновление файлов');
		if (!unlink($fn)) $answer.=GetErrorTxt(88, 'Ошибка удаления файла '.$fn);
		if ($r == 0) {
			$answer.=GetErrorTxt(88, $archive->errorInfo(true));				
			MakeUpdateAnswer(0, $answer, $ttt);
		} else {
			$ttt .= "\tОбновление файлов\n";
			foreach ($r as $i1 => $val1){
				if ($val1['status']=='ok' || $val1['status'] == 'already_a_directory') {
					if ($val1['folder'] == 0) $succ_files++; 
					$num = 89;
				} else {
					if ($val1['folder'] == 0) $fail_files++;
					$num = 88;
				}
				if ($val1['folder'] == 1) $f = 'Папка: '; else $f = 'Файл: ';
				$f .= $val1['filename'];
				$ttt .= sprintf("%s Status: %s\n", $f, $val1['status']);
				$answer.=GetErrorTxt($num, sprintf('<code>%s</code> Status: <code>%s</code>', $f, $val1['status']));
			}				
		}
		$answer.=$state_footer;
		if ($fail_files > 0) $answer.=sprintf('<div class="alert alert-error">Обновлено файлов: <span class="label label-inverse">%d</span><span class="label label-success">успешно</span>, <span class="label label-inverse">%d</span><span class="label label-important">с ошибкой</span></div>', $succ_files, $fail_files); else $answer.=sprintf('<div class="alert alert-success">Обновлено файлов: <span class="label label-inverse">%d</span><span class="label label-success">успешно</span></div>', $succ_files);
	}
	if ($_POST['server_sql'] != '') {
		$db->query('set names utf8');
		$answer.=sprintf($state_head, 'icon-hdd', 'SQL запросы в базу данных');
		$ttt .= "\tSQL запросы в базу данных\n";
		$succ_sql = 0; $fail_sql = 0;
		$server_sql = explode("\n", $_POST['server_sql']);
		foreach ($server_sql as $i1 => $val1){
			$res = $db->query($val1);
			if (!$res) {
				$fail_sql++;
				$answer.=GetErrorTxt(88, sprintf('<code>%s</code><br>Запрос: <code>%s</code>', $db->error, $val1));
				$ttt .= sprintf("Ошибка: %s\nЗапрос: %s\n", $db->error, $val1);
			} else {
				$answer.=GetErrorTxt(89, sprintf('<code>%s</code>', $val1));
				$ttt .= sprintf("%s\n", $val1);
				$succ_sql++;
			}
		}
		$answer.=$state_footer;
		if ($fail_sql > 0) $answer.=sprintf('<div class="alert alert-error">Выполнено запросов в базу данных: <span class="label label-inverse">%d</span><span class="label label-success">успешно</span>, <span class="label label-inverse">%d</span><span class="label label-important">с ошибкой</span></div>', $succ_sql, $fail_sql); else $answer.=sprintf('<div class="alert alert-success">Выполнено запросов в базу данных: <span class="label label-inverse">%d</span><span class="label label-success">успешно</span></div>', $succ_sql);		
	}
	MakeUpdateAnswer(1, $answer, $ttt);
}

function TopStat()
{
	global $db;
	$months = array('', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
	if (!isset($_POST['tblname'])) die();
	$tblname = $db->real_escape_string($_POST['tblname']);
	$curmonth = @date('n');
	$curday = @date('j');
	// Дневная статистика
	$d_txt = [];
	$dd = $curday;
	$m = $curmonth;
	$day_sum = array();
	for ($a=$dd; $a>0; $a--){
		$tmp = sprintf("WHERE `data`>='%s' AND `data`<'%s'", @date('Y-m-d H:i:s', mktime(0,0,0,$m,$a,@date('Y'))), @date('Y-m-d H:i:s', mktime(0,0,0,$m,$a+1,@date('Y'))));
		// Total
		$result = $db->query('SELECT count(*) as `cnt` FROM `'.$tblname.' '.$tmp);
		$row = mysqli_fetch_assoc($result);
		$d_txt[$a] = sprintf('["%s", %d]', $a, $row['cnt']);
		$day_sum[$a] = array();
		$day_sum[$a]['total'] = $row['cnt'];
		// Success
		$result = $db->query('SELECT count(*) as `cnt` FROM `'.$tblname.' '.$tmp.' AND `status`=1');
		$row = mysqli_fetch_assoc($result);
		$day_sum[$a]['success'] = $row['cnt'];
		// Fail
		$result = $db->query('SELECT count(*) as `cnt` FROM `'.$tblname.' '.$tmp.' AND `status`<>1');
		$row = mysqli_fetch_assoc($result);
		$day_sum[$a]['fail'] = $row['cnt'];
	}
	$d_txt = array_reverse($d_txt);	
	// Месячная статистика	
	$m_txt = [];
	$month_sum = array();
	for ($a=$curmonth; $a>0; $a--){
		$tmp = sprintf("WHERE `data`>='%s' AND `data`<'%s'", @date('Y-m-d H:i:s', mktime(0,0,0,$a,1,@date('Y'))), @date('Y-m-d H:i:s', mktime(0,0,0,$a+1,1,@date('Y'))));
		// Total
		$result = $db->query('SELECT count(*) as `cnt` FROM `'.$tblname.' '.$tmp);
		$row = mysqli_fetch_assoc($result);
		$m_txt[$a] = sprintf('["%s", %d]', $months[$a], $row['cnt']);
		$month_sum[$a] = array();
		$month_sum[$a]['total'] = $row['cnt'];
		// Success
		$result = $db->query('SELECT count(*) as `cnt` FROM `'.$tblname.' '.$tmp.' AND `status`=1');
		$row = mysqli_fetch_assoc($result);
		$month_sum[$a]['success'] = $row['cnt'];
		// Fail
		$result = $db->query('SELECT count(*) as `cnt` FROM `'.$tblname.' '.$tmp.' AND `status`<>1');
		$row = mysqli_fetch_assoc($result);
		$month_sum[$a]['fail'] = $row['cnt'];
	}
	$m_txt = array_reverse($m_txt);
	$answ = array(
		'day_sum' => $day_sum,
		'month_sum' => $month_sum,
		'd_txt' => $d_txt,
		'm_txt' => $m_txt
	);
	MakeAnswer($answ);
}

function CleanKlanCache()
{
	global $db;
	$res = $db->query('TRUNCATE TABLE `klan_pic`');
	if (!$res) die($db->error); else echo 'ok';
}

function RID2UID()
{
	if (!isset($_POST['roleid'])) die();
	$roleid = intval($_POST['roleid']);
	echo GetUserID($roleid);
	die();
}

function FindRoleName()
{
	if (!isset($_POST['rolename'])) die();
	$rolename = $_POST['rolename'];
	$rid = 0; $uid = 0;
	$f = GetRoleId($rolename);
	if ($f['retcode'] == 0 && $f['roleid'] > 0)
	{
		$rid = $f['roleid'];
		$uid = GetUserID($rid);		
	}
	echo json_encode(array('roleid' => $rid, 'userid' => $uid));
	die();
}

function ShowEditRoleForm()
{
	if (!isset($_POST['roleid'])) die();
	$roleid = intval($_POST['roleid']);
	$rd = GetRoleData($roleid);
	$answ = array('roleid' => $roleid, 'content' => '');
	//if ($rd['retcode'] == 60) $answ['roleid'] = 0; else
	//if ($rd['retcode'] !=0 || $rd['packet_check_error'] != 0) $answ['content'] = 'Ошибка получения персонажа'; else
	//{
		$text = '<form class="edit_role_form" id="edit_role_form"><ul class="nav nav-tabs" id="EditTab">';
		foreach ($rd['value'] as $i => $val)
		{
			$text .= '<li><a href="#'.$i.'">'.$i.'</a></li>';
		}
		$text .= '</ul><div id="EditTabContent" class="tab-content" style="height: 85%; overflow: initial">';
		foreach ($rd['value'] as $i => $val)
		{
			$text .= '<div class="tab-pane" id="'.$i.'"><textarea name="'.$i.'">'.json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).'</textarea></div>';
		}
		$text .= '</form><center><a href="#" id="save_but" onclick="return SaveRole()" class="btn btn-large btn-inverse">Сохранить</a></center>';
		$answ['content'] = $text;
	//}
	echo json_encode($answ);
	die();
}

function SaveEditRole()
{
	if (!isset($_POST['roleid'])) die();
	$err_templ = '<div class="alert alert-error">%s</div>';
	$roleid = intval($_POST['roleid']);
	$id = intval($_POST['id']);
	$ip = $_POST['ip'];
	$roledata = array();
	foreach ($_POST as $i => $val)
	{
		if ($i == 'op' || $i == 'roleid' || $i == 'lk_ver' || $i == 'id' || $i == 'ip') continue;
		$a = json_decode($val, true);
		if (json_last_error() === JSON_ERROR_NONE) $roledata[$i] = $a; else die(sprintf($err_templ, 'Ошибка парсинга секции '.$i));
	}
	$rd = PutRoleData($roleid, $roledata);
	if ($rd['retcode'] != 0) die(sprintf($err_templ, 'Ошибка сохранения персонажа '.$rd['retcode']));
	AddLKLogs($id, $ip, 0, 0, 'Редактирование персонажа '.$roleid.' '.htmlspecialchars($roledata['base']['name']));
	echo '<div class="alert alert-success">Данные успешно сохранены</div>';
	die();
}

function BanRole()
{
	extract($_POST);
	$err_templ = '<div class="alert alert-error">%s</div>';
	if ($reason == '') $reason = chr(0);
	$t = 'Бан ';
	switch ($type)
	{
		case 'chat':
			$t .= 'чата';
			$res = GMShutupRole(1024, 1, $roleid, $time, $reason);
			if ($res['dstroleid'] != $roleid ) die(sprintf($err_templ, 'Ошибка отправки запроса '.$res['dstroleid']));
		break;

		case 'role':
			$t .= 'персонажа';
			$res = GMKickoutRole(1024, 1, $roleid, $time, $reason);
			if ($res['retcode'] !=0 ) die(sprintf($err_templ, 'Ошибка отправки запроса '.$res['retcode']));
		break;

		default:
			die('Invalid type');
		break;
	}
	$msg = sprintf('%s %s на %s, Причина: %s', strip_tags($rolename), $t, GetTime($time, false), $reason);
	if (isset($broadcast))
	{
		broadcast(9, 0, 32, 0, $msg);		
	}
	AddLKLogs($id, $ip, 0, 0, $msg);	
	echo '<div class="alert alert-success">Бан успешно выдан</div>';
	die();
}

if (!isset($_POST['op'])) die(''); else $op = $_POST['op'];
if (isset($_POST['id'])) $_POST['id'] = intval($_POST['id']);
if ($op=='auth') auth(); else
if ($op=='vkauth') vkauth(); else
if ($op=='steamauth') steamauth(); else
if ($op=='clean_klan_cache') CleanKlanCache(); else
if ($op=='topstat') TopStat(); else
if ($op=='accmanage') GetAccounts(); else
if ($op=='AdminGenPromo') AdminGenPromo(); else 
if ($op=='update') Update(); else
if ($op=='AddLoginLog') AddLoginLog(); else
if ($op=='reg') Register(); else 
if ($op=='addvk') AddVK(); else 
if ($op=='addsteam') AddSteam(); else 
if ($op=='resendmail') ResendMail(); else 
if ($op=='auth_data') auth_data(); else
if ($op=='actreg') ActReg(); else 
if ($op=='checklogin') CheckLogin(); else 
if ($op=='nullpass') NullPass(); else 
if ($op=='forgetpass') ForgetPass(); else 
if ($op=='forgetlogin') ForgetLogin(); else 
if ($op=='disableipcheck') DisableIPCheck(); else 
if ($op=='online_stat') OnlineStat(); else 
if ($op=='lklogs') GetLKLogs(); else
if ($op=='mmotoplogs') GetTOPLogs(); else
if ($op=='qtoplogs') GetTOPLogs(true); else
if ($op=='klanart') GetKlanArts(); else
if ($op=='promo_codes') GetPromoCodes(); else
if ($op=='userinfo') UserInfo(); else
if ($op=='roleinfo') RoleInfo(); else
if ($op=='edit_klanart') EditKlanArt(); else
if ($op=='edit_promo_code') EditPromoCode(); else
if ($op=='addlk') AddLK(); else
if ($op=='make_names') MakeNames(); else
if ($op=='history') ShowHistory(); else 
if ($op=='loginlog_adm') ShowLoginLogAdm(); else
if ($op=='adm') AdminAction($_POST['id'], $_POST['act']); else
if ($op=='loginlog') ShowLoginLog(); else
if ($op=='GetRegGold') GetRegGold($_POST['id']); else
if ($op=='refresh_top') RefreshTop(); else 
if ($op=='update_user_info') UpdateUserInfo(); else 
if ($op=='qtop_bonus') TopBonus(false);
if ($op=='mmotop_bonus') TopBonus(true); else	
if ($op=='checkban') checkban(intval($_POST['id'])); else
if ($op=='persklan') persklan(); else
if ($op=='act') act(intval($_POST['id']), intval($_POST['n']), intval($_POST['num']),$_POST['ip']); else
if ($op=='persuah') persuah(); else
if ($op=='pers') pers(); else
if ($op=='shopheaders') shopheaders($_POST['id']); else	
if ($op=='don') donate(); else
if ($op=='rid2uid') RID2UID(); else
if ($op=='findrolename') FindRoleName(); else
if ($op=='editrole') ShowEditRoleForm(); else
if ($op=='saveeditrole') SaveEditRole(); else
if ($op=='banrole') BanRole();

