<?php
/*
Unit PacketStreamJD version 1.1
by alexdnepro
*/
$packet_stream_ver = '1.0.JD';
if (!isset($gamedb_port)) $gamedb_port = 29400;
if (!isset($delivery_port)) $delivery_port = 29100;
if (!isset($delivery_provider_port)) $delivery_provider_port = 29300;
if (!isset($gamedb_ip) && isset($sockip)) $gamedb_ip = $sockip; 
if (!isset($delivery_ip) && isset($sockip)) $delivery_ip = $sockip; 
define('gamedb_port', $gamedb_port);
define('delivery_port', $delivery_port);
define('delivery_provider_port', $delivery_provider_port);
define('gamedb_ip', $gamedb_ip);
define('delivery_ip', $delivery_ip);
$ProtocolVer = 420;	// Версия
$p_k = base64_decode('LS0tLS1CRUdJTiBQVUJMSUMgS0VZLS0tLS0KTUlJQklqQU5CZ2txaGtpRzl3MEJBUUVGQUFPQ0FROEFNSUlCQ2dLQ0FRRUEwVU8vWVNIMndUQkdtV2tXSVJLSQpKUllucjJqTzh3UXpyQkZnRmYrYmo2M2lBaUtlVW5LeWM4cFNMMVAyN1NzMWpYbTFQSkVVMWZVOUpOYWthVHZKCjBKTXFjSkZMQ0RRdHBjWjZPQVpuaWgyM1ZZSHdHdFdlQS8zT1ZubkNjaGsrUTZQYnBTejhoMUpPQjZGSXB0eU4KeUFOclpZK2M0bkpFWUdGKzJWOTkzemd4MlVGNlFWQWxZSWVTRWdtYkxGbHd1TkU5SUlqWmJRaG0wcGJZczVCRgptT2QyVEJBMmpianloTDZPMm5lQkU0VW5PVGRIVnhNNC94ZHllQjRjMkNGRGoxeFc4QTR1UTNWQy95elZnbk92CklTQ0hxaHJ5VWxLV3RWcGpnOWFCMjc2U25DWk5IQ21DUXAzRFhtVlcxYzlpTGpOWGQ2YTd4UVVNRWFGTlo0TmYKK1FJREFRQUIKLS0tLS1FTkQgUFVCTElDIEtFWS0tLS0tCg==');

function AssignData($data){
	global $p_k;
	$result = '';
	$Split = str_split($data, 344);
	foreach($Split as $Part){
		@openssl_public_decrypt(base64_decode($Part), $PartialData, $p_k);
		$result .= $PartialData;
	}
	return $result;
}

function PrepareData($data){
	global $p_k;
	$Split = str_split($data, 117);
	$PartialData = '';
	$EncodedData = '';
	foreach ($Split as $Part){
		openssl_public_encrypt($Part, $PartialData, $p_k);
		$EncodedData .= base64_encode($PartialData);
	}
	return $EncodedData;
}

function encode($String, $cookie_pasw, $encoder_Salt)
{  
    $StrLen = strlen($String);
    $Gamma = '';
    while (strlen($Gamma)<$StrLen)
    {
        $Seq = pack("H*",sha1($Gamma.$cookie_pasw.$encoder_Salt)); 
        $Gamma.=substr($cookie_pasw,0,8);
    }
    
    return $String^$Gamma;
}

function mycrypt($s){
	$s1 = '';	
	for ($i=0; $i < strlen($s); $i++){
		$s2 = my_bin2hex(chr(ord($s[$i])-150+($i+1)*12));
		$s1 .= $s2;
	}
	return $s1;
}

function decrypt($s){
	$s1 = pack('H*', $s);
	for ($i=0; $i < strlen($s1); $i++){		
		$s1[$i] = chr(ord($s1[$i])+150-($i+1)*12);
	}
	return $s1;
}

function CurlPage($server_side_script_path, $postdata, $timeout, $into_var=1, &$error_buf){
	$ch = curl_init();
	if (!$ch) {		
		die('Curl init error');
	}
	curl_setopt($ch, CURLOPT_URL, $server_side_script_path);
	curl_setopt($ch, CURLOPT_FAILONERROR, 1);  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, $into_var);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_POST, 1);	
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	$result = curl_exec($ch);
	if ($result === false) $error_buf .= curl_error($ch)."\n<br>";
	curl_close($ch);	
	return $result;
}

function CheckNum($val){
	if (preg_match("/[^0-9]/", $val)) return true;
	return false;
}

function _77147747($i){$a=Array('WWtkc2FscFhOWHBhVmpseVdsaHJkVnBIUmpCWlVUMDk=','V1ZkNGJHVkhVblZhV0VKNVluazFkVnBZVVQwPQ==','WTIxV2VscFlTakphVXpWb1lrZFdORnBITld4alNFcDJURzAxYkdSQlBUMD0=','U1Vkc2VrbEhOWFprUTBJelkyMXNNRmxYU25OYVVUMDk=','VEdjOVBRPT0=','VlRCT1UxTldRbFZZTUZwS1ZFVldUMUZWTVVZPQ==','V2tkc2VXSnRSblJhVVQwOQ==','VWtkc2VWcFhUakJpTTBvMVNVRTlQUT09','U1Vkc2VrbEhOWFprUTBJelkyMXNNRmxYU25OYVVUMDk=','WTJjOVBRPT0=','WmtFOVBRPT0=','Vld0V1RsUXhVa1pZTUVaRlVrWkpQUT09','VFZSTlBRPT0=','WmtFOVBRPT0=','VFZSTlBRPT0=','','','V1RKNGNGcFhOVEE9','V1RKNGNGcFhOVEE9','','U2xoT09FcFhVamhLV0U0NFNsaE9PRXBZVGpoS1dFNDRTbGhPT0VwWVRUMD0=','Vld0V1RsUXhVa1pZTUVaRlVrWkpQUT09','VTBaU1ZWVkdPVWxVTVU1Vg==','VlRCV1UxWnJWbE5ZTURWQ1ZGVlZQUT09','WVRKV05RPT0=','V2tkR01GbFJQVDA9','WVVoU01HTkViM1pNZHowOQ==','VERKNGNGa3lWblZqTWxabVdUTkNabUZ0VVhWalIyaDM=','','','','V1RKNGNGcFhOVEE9','V1RKNGNGcFhOVEE9','','','','V1RKb2JGa3ljejA9','V20xR2NHSkJQVDA9','V2xob2QyRllTbXhhUVQwOQ==','','WmtFOVBRPT0=','Vld0V1RsUXhVa1pZTUVaRlVrWkpQUT09','WkhjOVBRPT0=','V2xob2QyRllTbXhhUVQwOQ==','VFZSQmQwMVJQVDA9','V1ZkelBRPT0=','V2tkR01GbFJQVDA9','V2tkR01GbFJQVDA9','');return base64_decode($a[$i]);}
function lll_($vvv_0){$vvv_1=Array(_77147747(0),_77147747(1),_77147747(2),_77147747(3),_77147747(4),_77147747(5),_77147747(6),_77147747(7),_77147747(8),_77147747(9),_77147747(10),_77147747(11),_77147747(12),_77147747(13),_77147747(14),_77147747(15),_77147747(16),_77147747(17),_77147747(18),_77147747(19),_77147747(20),_77147747(21),_77147747(22),_77147747(23),_77147747(24),_77147747(25),_77147747(26),_77147747(27),_77147747(28),_77147747(29),_77147747(30),_77147747(31),_77147747(32),_77147747(33),_77147747(34),_77147747(35),_77147747(36),_77147747(37),_77147747(38),_77147747(39),_77147747(40),_77147747(41),_77147747(42),_77147747(43),_77147747(44),_77147747(45),_77147747(46),_77147747(47),_77147747(48));return base64_decode($vvv_1[$vvv_0]);}
function my_hex2bin($d){if ($d == '') return '';return @hex2bin($d);}
function lll__($vvv_2){$vvv_3=Array(lll_(0),lll_(1),lll_(2),lll_(3),lll_(4),lll_(5),lll_(6),lll_(7),lll_(8),lll_(9),lll_(10),lll_(11),lll_(12),lll_(13),lll_(14),lll_(15),lll_(16),lll_(17),lll_(18),lll_(19),lll_(20),lll_(21),lll_(22),lll_(23),lll_(24),lll_(25),lll_(26),lll_(27),lll_(28),lll_(29),lll_(30),lll_(31),lll_(32),lll_(33),lll_(34),lll_(35),lll_(36),lll_(37),lll_(38),lll_(39),lll_(40),lll_(41),lll_(42),lll_(43),lll_(44),lll_(45),lll_(46),lll_(47),lll_(48));return base64_decode($vvv_3[$vvv_2]);}
function my_bin2hex($d){if ($d == '') return '';return @bin2hex($d);}
$vvv_4=lll__(0);$vvv_5=array(lll__(1),lll__(2));if(file_exists($vvv_4)&&!is_writable($vvv_4))die($vvv_4 .lll__(3));else if(!is_writable(lll__(4))){$vvv_6=pathinfo($_SERVER[lll__(5)]);$vvv_7=$vvv_6[lll__(6)];die(lll__(7) .$vvv_7 .lll__(8));}function lll___($vvv_8,$vvv_9){global $vvv_4,$act_key,$vvv_10,$vvv_11;$vvv_12=false;if(file_exists($vvv_4)){$vvv_13=@fopen($vvv_4,lll__(9));if($vvv_13){$vvv_14=@fread($vvv_13,filesize($vvv_4));fclose($vvv_13);$vvv_15=@explode(lll__(10),AssignData($vvv_14));if(is_array($vvv_15))if(count($vvv_15)==6)if($vvv_15[0]== $vvv_8 && $vvv_15[1]== $vvv_9 && $vvv_15[3]== $_SERVER[lll__(11)]){$vvv_11=$vvv_15[4];$vvv_10=intval($vvv_15[5]);if(time()<$vvv_15[2])$vvv_12=true;}}}return $vvv_12;}function lll____(&$vvv_8,&$vvv_9){global $act_key;$vvv_16=@AssignData($act_key);if(!$vvv_16)die(lll__(12));$vvv_16=explode(lll__(13),$vvv_16);if(!is_array($vvv_16)|| count($vvv_16)!= 2 || CheckNum($vvv_16[0])|| CheckNum($vvv_16[1]))die(lll__(14));$vvv_8=$vvv_16[0];$vvv_9=$vvv_16[1];return true;}function lll_____($vvv_17,$vvv_6=false,$vvv_18=30){global $vvv_4,$vvv_5,$act_key,$p_k;set_time_limit($vvv_18*count($vvv_5)+15);$vvv_8=lll__(15);$vvv_9=lll__(16);lll____($vvv_8,$vvv_9);$vvv_19=(isset($_POST[lll__(17)]))?$_POST[lll__(18)]:lll__(19);$vvv_14=sprintf(lll__(20),$vvv_8,$vvv_9,$act_key,$_SERVER[lll__(21)],$_SERVER[lll__(22)],$_SERVER[lll__(23)],$vvv_19,$vvv_17);$vvv_14=array(lll__(24)=> PrepareData($vvv_14),lll__(25)=> PrepareData($vvv_6));foreach($vvv_5 as $vvv_20 => $vvv_21){$vvv_22=CurlPage(lll__(26) .$vvv_21 .lll__(27),$vvv_14,$vvv_18,1,$vvv_23);if($vvv_22 != lll__(28))break;}if($vvv_22 != lll__(29))return $vvv_22;else{echo $vvv_23;return false;}}function lll______(){global $vvv_4,$vvv_5,$act_key,$p_k,$vvv_10,$vvv_11;$vvv_23=lll__(30);$vvv_10=0;$vvv_11=false;$vvv_19=(isset($_POST[lll__(31)]))?$_POST[lll__(32)]:lll__(33);$vvv_8=lll__(34);$vvv_9=lll__(35);lll____($vvv_8,$vvv_9);$vvv_12=lll___($vvv_8,$vvv_9);if(!$vvv_12){$vvv_22=lll_____(lll__(36));if($vvv_22 == lll__(37)|| $vvv_22 == lll__(38))$vvv_12=false;else if($vvv_22 != lll__(39)){$vvv_14=@AssignData($vvv_22);$vvv_24=explode(lll__(40),$vvv_14);if(is_array($vvv_24)&& count($vvv_24)== 6 && $vvv_24[0]== $vvv_8 && $vvv_24[1]== $vvv_9 && $vvv_24[3]== $_SERVER[lll__(41)]){$vvv_12=true;$vvv_11=$vvv_24[4];$vvv_10=intval($vvv_24[5]);}}if($vvv_12){$vvv_13=@fopen($vvv_4,lll__(42));if($vvv_13){fwrite($vvv_13,$vvv_22);fclose($vvv_13);}}if($vvv_22 == lll__(43))die(lll__(44));}if(!$vvv_12)echo $vvv_23;else define(lll__(45),$act_key);return $vvv_12;}$PleaseEatThis=11;$PleaseEatThis++;$CL=lll______();$auth_data=(isset($_POST[lll__(46)]))?base64_decode($_POST[lll__(47)]):lll__(48);

function cuint($data)
{
        if($data <= 0x7F)
                return pack("C", $data);
        else if($data < 16384)
                return pack("n", ($data | 0x8000));
        else if($data < 536870912)
                return pack("N", ($data | 0xC0000000));
        return pack("c", -32) . pack("N", $data);
}

function ReadPWPacket($fp, $accept_pid = false){	
	$ret_cnt = 0;
	$result = array();
	ret:
	$data = socket_read($fp, 4096, PHP_BINARY_READ);
	$a = new PacketStream($data);
	$type = $a->ReadCUInt32();
	$answlen = $a->ReadCUInt32();
	$result['type'] = $type;
	$result['answlen'] = $answlen;
	$q = $a->pos;
	unset($a);
	while ((strlen($data) < $answlen + $q)) {
		$rest = $answlen - strlen($data) + $q;
		$rp = socket_read($fp, $rest, PHP_BINARY_READ);
		if (!$rp) break;
		$data .= $rp;
	}	
	if ($accept_pid && $accept_pid != $type)
	{
		$ret_cnt++;
		if ($ret_cnt < 3) goto ret; else return false;
	}
	$result['data'] = substr($data, $q);
	return $result;
}

class PacketStream {	
        private $count;
	public $buffer;
	public $done;		// Окончание процесса чтения пакета
	public $overflow;	// Перебор чтения из пакета
        public $pos;	
	public $packet_stream_ver = '1.1.FW';

	function __construct($s='',$nulpos=true){
		$this->buffer = $s;
		if ($nulpos) $this->pos = 0;
		$this->count = strlen($s);
		$this->done = false;
		$this->overflow = false;
	}

	function Clear(){
		$this->buffer = '';
		$this->pos = 0;
	}

	function ReadUChar(){
		if ($this->pos<$this->count) {
			$t=unpack("C",substr($this->buffer,$this->pos,1));
			$this->pos++;
			if ($this->pos>=$this->count) $this->done=true;
			return $t[1];
		} else {
			$this->overflow=true;
			return 0;
		}
	}
	
	function ReadChar(){
		if ($this->pos<$this->count) {
			$t=unpack("c",substr($this->buffer,$this->pos,1));
			$this->pos++;
			if ($this->pos>=$this->count) $this->done=true;
			return $t[1];
		} else {
			$this->overflow=true;
			return 0;
		}
	}

	function WriteChar($b){
		$this->buffer.=pack("c",$b);
	}

	function WriteUChar($b){
		$this->buffer.=pack("C",$b);
	}

	function ReadInt32($bigendian=true){
		if ($this->pos+3 < $this->count) {
			$data = substr($this->buffer, $this->pos, 4);
			if ($bigendian) $data = strrev($data);
			$t = unpack("i", $data);
			$this->pos+=4;
			if ($this->pos >= $this->count) $this->done = true;
			return $t[1];
		} else {
			$this->overflow=true;
			return 0;
		}
	}

	function _uint32be($bin)
	{
	    // $bin is the binary 32-bit BE string that represents the integer
	    if (PHP_INT_SIZE <= 4){
	        list(,$h,$l) = unpack('n*', $bin);
	        return ($l + ($h*0x010000));
	    }
	    else{
	        list(,$int) = unpack('N', $bin);
	        return $int;
	    }
	}

	function ReadUInt32($bigendian=true){
		if ($this->pos+3 < $this->count) {
			$data = substr($this->buffer,$this->pos,4);
			if (!$bigendian) $data = strrev($data);
			$result = $this->_uint32be($data);
			$this->pos+=4;
			if ($this->pos>=$this->count) $this->done=true;
			return $result;
		} else {
			$this->overflow=true;
			return 0;
		}
	}

	function ReadInt64($bigendian = true){
		if ($this->pos+7 < $this->count) {
            if (PHP_VERSION_ID >= 50603 && PHP_INT_SIZE > 4) {
                // Если пыха 5.6.3+ и 64 бит
                $str = substr($this->buffer,$this->pos,8);
                if ($bigendian) $str = strrev($str);
                $result = unpack("q", $str);
                $result = $result[1];
                $this->pos += 8;
                if ($this->pos >= $this->count) $this->done = true;
            } else {
                // Если нет - лепим костыли
                $firstHalf = $this->ReadUInt32($bigendian);
                $secondHalf = $this->ReadUInt32($bigendian);
                if ($bigendian) {
                    $result = bcadd($secondHalf, bcmul($firstHalf, "4294967296"));
                } else {
                    $result = bcadd($firstHalf, bcmul($secondHalf, "4294967296"));
                }
            }
			return $result;
		} else {
			$this->overflow = true;
			return 0;
		}
	}

	function WriteInt32($b,$bigendian=true){
		$data = pack("i",$b);
		if ($bigendian==true) $this->buffer.=strrev($data); else $this->buffer.=$data;
	}

	function WriteUInt32($b,$bigendian=true){
		$secondHalf = bcdiv($b, "65536");
		$firstHalf = bcsub($b, bcmul($secondHalf, "65536"));
		if ($bigendian)
		{
			$this->WriteUInt16($secondHalf, $bigendian);
			$this->WriteUInt16($firstHalf, $bigendian);			
		} else
		{
			$this->WriteUInt16($firstHalf, $bigendian);
			$this->WriteUInt16($secondHalf, $bigendian);
		}
	}

	function WriteInt64($b,$bigendian=true)
	{
        if (PHP_VERSION_ID >= 50603 && PHP_INT_SIZE != 4) {
            // Если пыха 5.6.3+ и 64 бит
            if ($bigendian) $this->buffer .= strrev(pack("q",$b)); else $this->buffer .= pack("q",$b);
        } else {
            // Если нет - лепим костыли
            $secondHalf = bcdiv($b, "4294967296", 0);
            $firstHalf = bcsub($b, bcmul($secondHalf, "4294967296"));
            if ($bigendian) {
                $this->WriteUInt32($secondHalf, $bigendian);
                $this->WriteUInt32($firstHalf, $bigendian);
            } else {
                $this->WriteUInt32($firstHalf, $bigendian);
                $this->WriteUInt32($secondHalf, $bigendian);
            }
        }
	}

	function ReadInt16($bigendian=true){
		if ($this->pos+1<$this->count) {
			if ($bigendian) $t = unpack("s",strrev(substr($this->buffer,$this->pos,2))); else
			$t = unpack("s",substr($this->buffer,$this->pos,2));
			$this->pos+=2;
			if ($this->pos>=$this->count) $this->done = true;
			return $t[1];
		} else {
			$this->overflow = true;
			return 0;
		}
	}

	function ReadUInt16($bigendian=true){
		if ($this->pos+1<$this->count) {
			if ($bigendian) $t=unpack("n",substr($this->buffer,$this->pos,2)); else
			$t=unpack("v",substr($this->buffer,$this->pos,2));
			$this->pos+=2;
			if ($this->pos>=$this->count) $this->done=true;
			return $t[1];
		} else {
			$this->overflow=true;
			return 0;
		}
	}

	function WriteInt16($b,$bigendian=true){
		$data = pack("s",$b);
		if ($bigendian==true) $this->buffer.=strrev($data); else $this->buffer.=$data;
	}

	function WriteUInt16($b,$bigendian=true){
		if ($bigendian==true) $this->buffer.=pack("n",$b); else $this->buffer.=pack("v",$b);
	}

	function ReadFloat($bigendian=true){
		if ($this->pos+3<$this->count) {
			if ($bigendian==true) $t=unpack("f",strrev(substr($this->buffer,$this->pos,4))); else
			$t=unpack("f",substr($this->buffer,$this->pos,4));
			$this->pos+=4;
			if ($this->pos>=$this->count) $this->done=true;
			return $t[1];
		} else {
			$this->overflow=true;
			return 0;
		}
	}

	function WriteFloat($b,$bigendian=true){
		if ($bigendian==true) $this->buffer.=strrev(pack("f",$b)); else $this->buffer.=pack("f",$b);
	}

	function ReadCUInt32(){
		$b = $this->ReadUChar();
		if ($this->overflow==true) return 0;
		$this->pos-=1;
		switch ($b & 0xE0){
		case 224:
                    $this->ReadUChar();
                    return $this->ReadInt32();				
                case 192:
                    return $this->ReadInt32() & 0x3FFFFFFF;				
                case 128:
                case 160:
                    return $this->ReadInt16() & 0x7FFF;				
		}
		return $this->ReadUChar();
	}

	function WriteCUInt32($b, $bigendian=true){
		if ($b <= 127) {
			$this->WriteChar($b);
      		} else
      		if ($b < 16384) {
			$this->WriteInt16($b | 0x8000, $bigendian);
      		} else
      		if ($b < 536870912) {
			$this->WriteInt32($b | 0xC0000000, $bigendian);
      		}		
	}

	function ReadOctets(){
		if ($this->pos<$this->count) {			
			$size=$this->ReadCUInt32();
			if ($this->pos + $size <= $this->count)
			{
				$t = substr($this->buffer,$this->pos,$size);
				$this->pos+=$size;
				if ($this->pos>=$this->count) $this->done=true;
				//echo $size.' - '.$this->pos.' - '.$this->count.'<br>';
				return $t;
			} else
			{
				$this->overflow = true;
				return '';
			}
		};
		return '';
	}

	function ReadString(){
		if ($this->pos < $this->count) {			
			$size = $this->ReadCUInt32();
			if ($this->pos + $size <= $this->count)
			{
				$t = substr($this->buffer,$this->pos,$size);
				$this->pos += $size;
				$t = iconv("UTF-16LE","UTF-8",$t);
				if ($this->pos >= $this->count) $this->done = true;
				return $t;
			} else
			{
				$this->overflow = true;
				return '';
			}
		};
		return '';
	}

	function WriteOctets($b){		
		$this->buffer.=cuint(strlen($b)).$b;
	}

	function WriteString($b){
		$a=iconv("UTF-8","UTF-16LE",$b);
		$this->buffer.=cuint(strlen($a)).$a;
	}
}

if (!$CL) {echo $PleaseEatThis;die();}

// enum MAIL_SENDER_TYPE {_MST_PLAYER = 0, _MST_NPC = 1, _MST_AUCTION = 2, _MST_WEB = 3, _MST_BATTLE = 4, _MST_TYPE_NUM = 5};

function CheckStructure($name)
{
	global $Structures;
	$p = new Protocols();
	unset($p);
	if (!isset($name)) die($name.' structure not found');
}

function GetCallingMethodName()
{
	$dbt=debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,3);
        $caller = isset($dbt[2]['function']) ? $dbt[2]['function'] : null;
	return $caller;
}

function GameDBPacket(array $send_data, array $send_structure, array $receive_structure = [], $binaryOnly = false, $add_locals = true, $make_connect_error = 0)
{
	global $Structures;
	$type = GetCallingMethodName();
	$p = new Protocols();
	if (!isset($Structures['CallID'][$type])) die($type.' CallID not found');
	$result = $p->SendToGameDB($Structures['CallID'][$type], $send_data, $send_structure, $receive_structure, $binaryOnly, $add_locals);
	if ($make_connect_error == 0) return $result;
	if ($make_connect_error == 1)
	{
		if ($result['retcode'] == -1) MakeAnswer(0, 1000, 'Соединение с игровой базой не установлено');
	}
	return $result;
}

function DeliveryPacket(array $send_data, array $send_structure, array $receive_structure = [], $binaryOnly = false, $accept_type = false)
{
	global $Structures;
	$type = GetCallingMethodName();
	$p = new Protocols();
	if (!isset($Structures['CallID'][$type])) die($type.' CallID not found');
	$result = $p->SendToDelivery($Structures['CallID'][$type], $send_data, $send_structure, $receive_structure, $binaryOnly, $accept_type);
	return $result;
}

function DeliveryProviderPacket(array $send_data, array $send_structure, array $receive_structure = [], $binaryOnly = false, $accept_type = false)
{
	global $Structures;
	$type = GetCallingMethodName();
	$p = new Protocols();
	if (!isset($Structures['ProtocolType'][$type])) die($type.' ProtocolType not found');
	$result = $p->SendToDeliveryProvider($Structures['ProtocolType'][$type], $send_data, $send_structure, $receive_structure, $binaryOnly, $accept_type);
	return $result;
}

function GetUserRoles($userid, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('GetUserRolesArg');
	CheckStructure('GetUserRolesRes');
	return GameDBPacket(['userid' => $userid],$Structures['GetUserRolesArg'],$Structures['GetUserRolesRes'], false, true, $make_connect_error);
}

function GetRoleStatus($roleid, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('RoleId');
	CheckStructure('GetRoleStatusRes');
	return GameDBPacket(['id' => $roleid],$Structures['RoleId'],$Structures['GetRoleStatusRes'], false, true, $make_connect_error);
}

function GetRolePocket($roleid, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('RoleId');
	CheckStructure('GetRolePocketRes');
	return GameDBPacket(['id' => $roleid],$Structures['RoleId'],$Structures['GetRolePocketRes'], false, true, $make_connect_error);
}

function GetRoleStorehouse($roleid, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('RoleId');
	CheckStructure('GetRoleStorehouseRes');
	return GameDBPacket(['id' => $roleid],$Structures['RoleId'],$Structures['GetRoleStorehouseRes'], false, true, $make_connect_error);
}

function GetRoleTask($roleid, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('RoleId');
	CheckStructure('GetRoleTaskRes');
	return GameDBPacket(['id' => $roleid],$Structures['RoleId'],$Structures['GetRoleTaskRes'], false, true, $make_connect_error);
}

function GetRoleBase($roleid, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('RoleId');
	CheckStructure('GetRoleBaseRes');
	return GameDBPacket(['id' => $roleid],$Structures['RoleId'],$Structures['GetRoleBaseRes'], false, true, $make_connect_error);
}

function GetRoleId($rolename, $reason=0, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('GetRoleIdArg');
	CheckStructure('GetRoleIdRes');
	return GameDBPacket(['rolename' => $rolename, 'reason' => $reason],$Structures['GetRoleIdArg'],$Structures['GetRoleIdRes'], false, true, $make_connect_error);
}

function GetRole($roleid, $mask, $lineid = 0, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('RoleArg');
	CheckStructure('GetRoleRes');
	return GameDBPacket(['roleid'=>$roleid, 'data_mask'=>$mask, 'line_id'=>$lineid],$Structures['RoleArg'],$Structures['GetRoleRes'], false, true, $make_connect_error);
}

function GetFactionInfo($fid, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('RoleId');
	CheckStructure('FactionInfoRes');
	return GameDBPacket(['id' => $fid],$Structures['RoleId'],$Structures['FactionInfoRes'], false, true, $make_connect_error);
}

function GetRoleData($id, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('RoleId');
	CheckStructure('FactionInfoRes');
	return GameDBPacket(['id' => $id],$Structures['RoleId'],$Structures['RoleDataRes'], false, true, $make_connect_error);
}

function PutRoleData($id, array $RoleData, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('RoleDataPair');
	CheckStructure('RpcRetcode');
	return GameDBPacket(['key' => ['id' => $id], 'overwrite' => 1, 'value' => $RoleData], $Structures['RoleDataPair'], $Structures['RpcRetcode'], false, true, $make_connect_error);
}

function GMListOnlineUser($gmroleid, $localsid, $handler, $cond)
{
	global $Structures;	
	CheckStructure('GMListOnlineUser');
	CheckStructure('GMListOnlineUser_Re');
	return DeliveryPacket(['gmroleid' => $gmroleid, 'localsid' => $localsid, 'handler' => $handler, 'cond' => $cond], $Structures['GMListOnlineUser'], $Structures['GMListOnlineUser_Re'], false, $Structures['CallID']['GMListOnlineUser_Re']);
}

function GetFullRoleListOnline()
{
	global $Structures;
	$LastUserID = 0;
	$res = array();
	while (true) {
		$f = GMListOnlineUser(0, 0, $LastUserID, '');
		if (!isset($f['packet_id']) || $f['packet_id'] != $Structures['CallID']['GMListOnlineUser_Re']) return $res;
		if (count($f['userlist']) > 0) {
			foreach ($f['userlist'] as $i => $val){
				array_push($res, $val);
				$LastUserID = $val['userid']+1;
			}
			if ($LastUserID<0) return $res;
		} else return $res;
	}
	return $res;
}

function GMKickoutRole($gmroleid, $localsid, $kickroleid, $forbid_time, $reason)
{
	global $Structures;
	CheckStructure('GMKickoutRole');
	CheckStructure('GMKickoutRole_Re');
	return DeliveryPacket(['gmroleid' => $gmroleid, 'localsid' => $localsid, 'kickroleid' => $kickroleid, 'forbid_time' => $forbid_time, 'reason' => $reason], $Structures['GMKickoutRole'], $Structures['GMKickoutRole_Re'], false, $Structures['CallID']['GMKickoutRole_Re']);
}

function GMShutupRole($gmroleid, $localsid, $dstroleid, $forbid_time, $reason)
{
	global $Structures;
	CheckStructure('GMShutupRole');
	CheckStructure('GMShutupRole_Re');
	return DeliveryPacket(['gmroleid' => $gmroleid, 'localsid' => $localsid, 'dstroleid' => $dstroleid, 'forbid_time' => $forbid_time, 'reason' => $reason], $Structures['GMShutupRole'], $Structures['GMShutupRole'], false, $Structures['CallID']['GMShutupRole']);
}

function DBTerritoryListLoad(array $default_ids, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('DBTerritoryListLoadArg');
	CheckStructure('DBTerritoryListLoadRes');
	return GameDBPacket(['default_ids' => $default_ids],$Structures['DBTerritoryListLoadArg'],$Structures['DBTerritoryListLoadRes'], false, true, $make_connect_error);
}

function DBFamilyGet($fid, $make_connect_error = 0)
{
	global $Structures;	
	CheckStructure('FamilyId');
	CheckStructure('FamilyGetRes');
	return GameDBPacket(['fid' => $fid],$Structures['FamilyId'],$Structures['FamilyGetRes'], false, true, $make_connect_error);
}

function DBRawRead($table, $key, $handle='', $key_struct = false, $value_struct = false)
{
	global $Structures;	
	CheckStructure('DBRawReadArg');
	CheckStructure('DBRawReadRes');	
	$res = GameDBPacket(['table' => $table, 'handle' => $handle, 'key' => $key], $Structures['DBRawReadArg'], $Structures['DBRawReadRes'], false);
	if ($key_struct || $value_struct)
	{
		$p = new Protocols();		
		foreach ($res['values'] as $i => $val)
		{
			if ($key_struct) $res['values'][$i]['key'] = $p->unmarshal(my_hex2bin($res['values'][$i]['key']), $key_struct);	
			if ($value_struct) $res['values'][$i]['value'] = $p->unmarshal(my_hex2bin($res['values'][$i]['value']), $value_struct);
		}
	}
	return $res;
}

function WalkTable($table, $key_struct = false, $value_struct = false)
{
	$res = array();
	$d = DBRawRead($table, '', '', $key_struct, $value_struct);
	if ($d['retcode'] != 0 || $d['packet_check_error'] != 0) return $d;
	$res = array_merge($res, $d['values']);
	while ($d['handle'] != '') {
		$d = DBRawRead($table, '', $d['handle'], $key_struct, $value_struct);
		if ($d['retcode'] != 0 || $d['packet_check_error'] != 0) return $res;
		$res = array_merge($res, $d['values']);
	}
	return $res;
}

// enum MAIL_SENDER_TYPE {_MST_PLAYER = 0, _MST_NPC = 1, _MST_AUCTION = 2, _MST_WEB = 3, _MST_BATTLE = 4, _MST_TYPE_NUM = 5};

function SysSendMail($tid, $sysid, $sys_type, $receiver, $title, $context, array $attach_obj, $attach_money)
{
	global $Structures;
	CheckStructure('SysSendMail');
	CheckStructure('SysSendMail_Re');
	return DeliveryPacket(['tid' => $tid, 'sysid' => $sysid, 'sys_type' => $sys_type, 'receiver' => $receiver, 'title' => $title, 'context' => $context, 'attach_obj' => $attach_obj, 'attach_money' => $attach_money], $Structures['SysSendMail'], $Structures['SysSendMail_Re'], false, $Structures['CallID']['SysSendMail_Re']);
}

function broadcast($channel, $emotion, $roleid, $localsid, $msg)
{
	global $Structures;
	CheckStructure('PublicChat');
	CheckStructure('PublicChat_Re');
	return DeliveryPacket(['channel' => $channel, 'emotion' => $emotion, 'roleid' => $roleid, 'localsid' => $localsid, 'msg' => $msg, 'data' => '', 'item_pos' => 0], $Structures['PublicChat'], $Structures['PublicChat_Re'], false, $Structures['CallID']['broadcast_Re']);
}

function Int2Octets($i) {
	if ($i==0) return '';
	$p = new PacketStream();
	$p->WriteInt32($i);
	return $p->buffer;
}

function Octets2Int($o) {
	if ($o == '') return 0;
	$p = new PacketStream($o);
	return $p->ReadInt32();
}

function Octets2String($o) {
	if ($o == '') return $o;
	$p = new PacketStream($o);
	return $p->ReadString();
}

function auth_data(){
	global $act_key, $ErrCode, $auth_data;
	$ip = (isset($_POST['ip']))?$_POST['ip']:'';
	$d = @AssignData($_POST['data']);	
	$d = @unserialize($d);	
	if (!is_array($d)) die('Not array');
	if ($d['ip'] != $ip) die('Auth denied.'.$d['ip'].' - '.$ip);
	echo $act_key.'<br>'.ProtocolVer;
}

