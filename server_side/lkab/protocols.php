<?php

/**
 * Class Protocols for JD
 * Version 1.2
 * by alexdnepro 2019
 */
class Protocols
{
	const PV = 'ProtocolVer';
	const PL = 'ProtocolsLoaded';
	const GDB_C = 'GameDBConnected';
	const GD_C = 'GDeliveryConnected';
	const GDP_C = 'GDeliveryProviderConnected';

	public function LoadStructures()
	{
		global $Structures;
		if (!defined(self::PV)) die(self::PV.' not defined');
		$file = 'protocol/'.ProtocolVer.'.php';
		if (file_exists($file)) {
			$Structures = include $file;
			define(self::PL, true);
			return;
		} else die('File '.$file.' not found');
		echo 'Wrong protocol version '.$version;
		exit(-1);
	}

	function __construct()
	{
		if (!defined(self::PL)) $this->LoadStructures();
	}

	public function Connect2GameDB()
	{
		global $GameDBSocket;
		if (defined(self::GDB_C) && isset($GameDBSocket) && $GameDBSocket) return true;
		if (!$GameDBSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) return false;//exit('Unable to create AF_INET socket.');
		if (!@socket_connect($GameDBSocket, gamedb_ip, gamedb_port)) return false;		
		define(self::GDB_C, true);
		socket_set_block($GameDBSocket);
		return true;
	}

	public function Connect2GDelivery()
	{
		global $GDeliverySocket;
		if (defined(self::GD_C) && isset($GDeliverySocket) && $GDeliverySocket) return true;
		if (!$GDeliverySocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) return false;//exit('Unable to create AF_INET socket.');
		if (!@socket_connect($GDeliverySocket, delivery_ip, delivery_port)) return false;
		define(self::GD_C, true);
		socket_set_block($GDeliverySocket);
		socket_read($GDeliverySocket, 1024, PHP_BINARY_READ);
		return true;
	}

	public function Connect2GDeliveryProvider()
	{
		global $GDeliveryProviderSocket;
		if (defined(self::GDP_C) && isset($GDeliveryProviderSocket) && $GDeliveryProviderSocket) return true;
		if (!$GDeliveryProviderSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) return false;//exit('Unable to create AF_INET socket.');
		if (!@socket_connect($GDeliveryProviderSocket, delivery_ip, delivery_provider_port)) return false;
		define(self::GDP_C, true);
		socket_set_block($GDeliveryProviderSocket);
		return true;
	}

	public function AddLocalsID(array $struct)
	{
		$a = array('localsid'=>'int');
		return array_merge($a, $struct);
	}

	public function SendToGameDB($type, array $send_data, array $send_structure, array $receive_structure = [], $binaryOnly = false, $add_locals = true)
	{
		global $GameDBSocket;
		if (!$this->Connect2GameDB())
		{
			if ($binaryOnly) exit('Connect to GameDB failed');
			$result = array('retcode' => -1);
			return $result;
		}
		if ($add_locals) $send_structure = $this->AddLocalsID($send_structure);
		$data = $this->marshal($send_data, $send_structure);		
	        $send_buf = cuint($type).cuint(strlen($data)).$data;
		if (!socket_write($GameDBSocket, $send_buf)) return false;
		$data = ReadPWPacket($GameDBSocket);
		if (!$data || !is_array($data)) return false;
		if ($binaryOnly) return $data['data'];
		if ($add_locals) $receive_structure = $this->AddLocalsID($receive_structure);
		$result = $this->unmarshal($data['data'], $receive_structure);
		$result['packet_id'] = $data['type'];		
		return $result;
	}

	public function SendToDelivery($type, array $send_data, array $send_structure, array $receive_structure = [], $binaryOnly = false, $accept_type = false)
	{
		global $GDeliverySocket;
		if (!$this->Connect2GDelivery()) 
		{
			if ($binaryOnly) exit('Connect to GDelivery failed');
			$result = array('retcode' => -1);
			return $result;
		}
		$data = $this->marshal($send_data, $send_structure);		
	        $send_buf = cuint($type).cuint(strlen($data)).$data;
		if (!socket_write($GDeliverySocket, $send_buf)) return false;
		$data = ReadPWPacket($GDeliverySocket, $accept_type);
		if (!$data || !is_array($data)) return false;
		if ($binaryOnly) return $data['data'];
		$result = $this->unmarshal($data['data'], $receive_structure);
		$result['packet_id'] = $data['type'];		
		return $result;
	}

	public function SendToDeliveryProvider($type, array $send_data, array $send_structure, array $receive_structure = [], $binaryOnly = false, $accept_type = false)
	{
		global $GDeliveryProviderSocket;
		if (!$this->Connect2GDeliveryProvider())
		{
			if ($binaryOnly) exit('Connect to GDelivery provider failed');
			$result = array('retcode' => -1);
			return $result;
		}
		$data = $this->marshal($send_data, $send_structure);		
	        $send_buf = cuint($type).cuint(strlen($data)).$data;
		if (!socket_write($GDeliveryProviderSocket, $send_buf)) return false;
		$data = ReadPWPacket($GDeliveryProviderSocket, $accept_type);
		if (!$data || !is_array($data)) return false;
		if ($binaryOnly) return $data['data'];
		$result = $this->unmarshal($data['data'], $receive_structure);
		$result['packet_id'] = $data['type'];		
		return $result;
	}

	public function WriteVal($value, $data, $key, $p, $i = -1)
	{
		if (substr($value, -1) == '_') 
		{
			$endian = false;
			$value = substr($value, 0, -1);
		} else $endian = true;		
		if (!isset($data[$key]) && $key == 'localsid') $data[$key] = -1;		
		if (!isset($data[$key])) $data[$key] = false;
		if (is_array($data[$key]) && $i >= 0) {
            		if (!isset($data[$key][$i])) $data[$key] = false; else
                	$data[$key] = $data[$key][$i];
        	}
		$result = true;
		switch ($value) 
		{
			case 'char':
				$p->WriteChar($data[$key]);					
			break;

			case 'unsigned char':
				$p->WriteUChar($data[$key]);					
			break;

			case 'short':
				$p->WriteInt16($data[$key], $endian);					
			break;

			case 'unsigned short':
				$p->WriteUInt16($data[$key], $endian);					
			break;

			case 'int':
				$p->WriteInt32($data[$key], $endian);
			break;

			case 'unsigned int':
				$p->WriteUInt32($data[$key], $endian);
			break;

			case 'long':
			case 'int64':
			case 'int64_t':
				$p->WriteInt64($data[$key], $endian);
			break;

			case 'string':
				$p->WriteString($data[$key]);
			break;

			case 'Octets':
				$p->WriteOctets(my_hex2bin($data[$key]));
			break;

			case 'AnsiString':
				$p->WriteOctets($data[$key]);
			break;

			case 'float':
				$p->WriteFloat($data[$key], $endian);
			break;

			case 'cuint':
				$p->WriteCUInt32($data[$key], $endian);
			break;

			default:
				$result = false;
			break;
		}
		return $result;
	}

	public function marshal(array $data, array $structure)
	{
		global $Structures;
		$p = new PacketStream();
		foreach ($structure as $key => $value) 
		{
			if (is_array($value)) 
			{
				$p->buffer .= $this->marshal($data[$key], $value);				
			} else 
			if (strlen($value)>7 && substr($value, -6) == 'Vector')
			{
				$value = substr($value, 0, -6);
				$count = (isset($data[$key]) ? count($data[$key]) : 0);
				$p->buffer .= cuint($count);
				foreach($data[$key] as $i => $v)
				{
					$r = $this->WriteVal($value, $data, $key, $p, $i);
					if ($r !== false) continue;
					if (!array_key_exists($value, $Structures)) exit('Wrong structure type '.$value);					
					$p->buffer .= $this->marshal($data[$key][$i], $Structures[$value]);
				}
				continue;
			} else
			{
				$rr = $this->WriteVal($value, $data, $key, $p);
				if (!$rr)
				{
					// Search by all structures
					if (!array_key_exists($value, $Structures)) exit('Wrong structure type '.$value);
					$p->buffer .= $this->marshal($data[$key], $Structures[$value]);
				}				
			}
		}		
		return $p->buffer;
	}

	public function ReadVal($value, $p)
	{
		if (substr($value, -1) == '_') 
		{
			$endian = false;
			$value = substr($value, 0, -1);
		} else $endian = true;
		switch ($value) 
		{
			case 'char':
				$result = $p->ReadChar();					
			break;

			case 'unsigned char':
				$result = $p->ReadUChar();					
			break;

			case 'short':
				$result = $p->ReadInt16($endian);					
			break;

			case 'unsigned short':
				$result = $p->ReadUInt16($endian);					
			break;

			case 'int':
				$result = $p->ReadInt32($endian);
			break;

			case 'unsigned int':
				$result = $p->ReadUInt32($endian);
			break;

			case 'long':
			case 'int64':
			case 'int64_t':
				$result = $p->ReadInt64($endian);
			break;

			case 'string':
				$result = $p->ReadString();
			break;

			case 'Octets':
				$result = my_bin2hex($p->ReadOctets());
			break;

			case 'AnsiString':
				$result = $p->ReadOctets();
			break;

			case 'float':
				$result = $p->ReadFloat($endian);
			break;

			case 'cuint':
				$result = $p->ReadCUInt32();
			break;

			default:
				$result = false;
			break;
		}
		return $result;
	}

	public function unmarshal($data, $structure, $ps = false)
	{
		global $Structures;
		$result = array();
		$add_check = false;
		if ($ps === false) {
			$p = new PacketStream($data);
			$add_check = true;
			$p->done = false;
			$p->overflow = false;
		} else $p = $ps;		
		foreach ($structure as $key => $value) 
		{
			if (is_array($value)) 
			{
				$result[$key] = $this->unmarshal($data, $value, $p);
			} else 
			if (strlen($value)>7 && substr($value, -6) == 'Vector')
			{
				$value = substr($value, 0, -6);
				$count = $p->ReadCUInt32();
				$result[$key] = array();
				for ($i = 0; $i < $count; ++$i) 
				{
					$r = $this->ReadVal($value, $p);
					if ($r !== false) {
					    array_push($result[$key], $r);
					    continue;
                    }
					if (!array_key_exists($value, $Structures)) exit('Wrong structure type '.$value);
					$result[$key][$i] = $this->unmarshal($data, $Structures[$value], $p);
				}
				continue;
			} else
			{
				$result[$key] = $this->ReadVal($value, $p);
				if ($result[$key] === false)
				{
					if (!array_key_exists($value, $Structures)) exit('Wrong structure type '.$value);
					$result[$key] = $this->unmarshal($data, $Structures[$value], $p);
				}				
			}
		}
		if ($add_check)
		{
			$result['packet_check_error'] = 0;
			if ($data != '')
			{
				if (!$p->done) $result['packet_check_error'] = 1;	// Пакет разобран не до конца
				if ($p->overflow) $result['packet_check_error'] = 2;	// Длинна пакета меньше ожидаемой
			}
		}
		return $result;
	}
}
