<?php

return array(
	'CallID' => array(
		// GameDB CallID
		'GetUserRoles' => 3401,
		'GetRole' => 3005,		// GetRoleDetail
		'PutRoleBase' => 3012,
		'GetRoleBase' => 3013,
		'PutRoleStatus' => 3014,
		'GetRoleStatus' => 3015,
		'PutRolePocket' => 3016,
		'GetRolePocket' => 3017,
		'PutRoleTask' => 3018,
		'GetRoleTask' => 3019,
		'PutRoleStorehouse' => 3026,
		'GetRoleStorehouse' => 3027,
		'GetFactionInfo' => 4604,	// DBFACTIONGET
		'PutRoleData' => 8002,
		'GetRoleData' => 8003,
		'DBRawRead' => 3055,
		'DBTerritoryListLoad' => 1065,
		'DBFamilyGet' => 4613,
		'GetRoleId' => 3033,

		// GDeliveryd CallID
		'GMListOnlineUser' => 352,
		'GMListOnlineUser_Re' => 353,
		'GMKickoutRole' => 360,
		'GMKickoutRole_Re' => 361,
		'GMShutupRole' => 362,
		'GMShutupRole_Re' => 363,
		'SysSendMail' => 4214,
		'SysSendMail_Re' => 4215,
		'broadcast' => 79,		// PublicChat
		'broadcast_Re' => 120
	),
	'ProtocolType' => array(
		// GDeliveryProtocol CallID
	),
	'GetUserRolesArg' => array(
		'userid' => 'int'
	),
	'RoleId' => array(
		'id' => 'unsigned int'
	),
	'RoleNames' => array(
		'roleid' => 'int',
		'rolename' => 'string'
	),
	'GRoleBrief' => array(
		'id' => 'int',
		'name' => 'string',
		'level' => 'int',
		'money' => 'int64',
		'cashadd' => 'int',
		'cashused' => 'int',
		'itemnum' => 'int'
	),
	'GetUserRolesRes' => array(
		'retcode' => 'int',
		'roles' => 'RoleNamesVector',
		'data' => 'GRoleBriefVector'
	),
	'GRoleStatus' => array(
		'version' => 'char',
		'id' => 'unsigned int',
		'occupation' => 'unsigned char',
		'level' => 'short',
		'cur_title' => 'short',
		'exp' => 'int64',
		'pp' => 'int',
		'hp' => 'int',
		'mp' => 'int',
		'posx' => 'float',
		'posy' => 'float',
		'posz' => 'float',
		'pkvalue' => 'int',
		'worldtag' => 'int',
		'time_used' => 'int',
		'reputation' => 'int',
		'produceskill' => 'int',
		'produceexp' => 'int',
		'custom_status' => 'Octets',
		'filter_data' => 'Octets',
		'charactermode' => 'Octets',
		'instancekeylist' => 'Octets',
		'dbltime_data' => 'Octets',
		'petcorral' => 'Octets',
		'var_data' => 'Octets',
		'skills' => 'Octets',
		'storehousepasswd' => 'Octets',
		'coolingtime' => 'Octets',
		'recipes' => 'Octets',
		'waypointlist' => 'Octets',
		'credit' => 'Octets',
		'titlelist' => 'Octets',
		'contribution' => 'int',
		'combatkills' => 'int',
		'devotion' => 'int',
		'talismanscore' => 'int',
		'updatetime' => 'int',
		'battlescore' => 'int',
		'petdata' => 'Octets',
		'reborndata' => 'Octets',
		'cultivation' => 'short',
		'reserved1' => 'int',
		'fashion_hotkey' => 'Octets',
		'raid_data' => 'Octets',
		'five_year' => 'Octets',
		'treasure_info' => 'Octets'
	),
	'GetRoleStatusRes' => array(
		'retcode' => 'int',
		'value' => 'GRoleStatus',
	),
	'GRoleInventory' => array(
		'id' => 'unsigned int',
		'pos' => 'int',
		'count' => 'int',
		'client_size' => 'short',
		'max_count' => 'short',
		'data' => 'Octets',
		'proctype' => 'int',
		'expire_date' => 'int',
		'guid1' => 'int',
		'guid2' => 'int'
	),
	'GPocketInventory' => array(
		'id' => 'unsigned int',
		'pos' => 'short',
		'count' => 'short',
	),
	'GRolePocket' => array(
		'capacity' => 'unsigned int',
		'timestamp' => 'int',
		'money' => 'unsigned int',
		'items' => 'GRoleInventoryVector',
		'equipment' => 'GRoleInventoryVector',
		'petbadge' => 'GRoleInventoryVector',
		'petequip' => 'GRoleInventoryVector',
		'pocket_capacity' => 'short',
		'pocket_items' => 'GPocketInventoryVector',
		'fashion' => 'GRoleInventoryVector',
		'mountwing' => 'Octets',
		'gifts' => 'GRoleInventoryVector'
	),
	'GetRolePocketRes' => array(
		'retcode' => 'int',
		'value' => 'GRolePocket',
	),
	'GRoleStorehouse' => array(
		'capacity' => 'unsigned int',
		'money' => 'unsigned int',
		'items' => 'GRoleInventoryVector',
		'capacity2' => 'unsigned char',
		'items2' => 'GRoleInventoryVector',
		'fuwen' => 'GRoleInventoryVector',
		'card' => 'GRoleInventoryVector',
		'card_matrix' => 'GRoleInventoryVector',
		'talent_scroll' => 'GRoleInventoryVector',
		'reserved2' => 'int'
	),
	'GetRoleStorehouseRes' => array(
		'retcode' => 'int',
		'value' => 'GRoleStorehouse',
	),
	'GRoleTask' => array(
		'task_data' => 'Octets',
		'task_complete' => 'Octets',
		'task_finishtime' => 'Octets',
		'task_inventory' => 'GRoleInventoryVector'
	),
	'GetRoleTaskRes' => array(
		'retcode' => 'int',
		'value' => 'GRoleTask'
	),
	'GShopLog' => array(
		'roleid' => 'int',
		'order_id' => 'int',
		'item_id' => 'int',
		'expire' => 'int',
		'item_count' => 'int',
		'order_count' => 'int',
		'cash_need' => 'int',
		'time' => 'int',
		'guid1' => 'int',
		'guid2' => 'int'
	),
	'GRoleAchievement' => array(
		'version' => 'int',
		'achieve_map' => 'Octets',
		'achieve_active' => 'Octets',
		'achieve_spec_info' => 'Octets',
		'achieve_award_map' => 'Octets',
		'reserved4' => 'char',
		'reserved1' => 'short',
		'reserved2' => 'int',
		'reserved3' => 'int'
	),
	'GRoleAward' => array(
		'vipaward' => 'Octets',
		'onlineaward' => 'Octets',
		'reserved1' => 'Octets',
		'reserved2' => 'Octets'
	),
	'IntInt64Map' => array(
		'key' => 'int',
		'value' => 'int64'
	),
	'GRoleBase2' => array(
		'id' => 'int',
		'bonus_withdraw' => 'int',
		'bonus_reward' => 'int',
		'bonus_used' => 'int',
		'exp_withdraw_today' => 'int64_t',
		'exp_withdraw_time' => 'int',
		'composkills' => 'Octets',
		'tower_raid' => 'Octets',
		'deity_level' => 'unsigned short',
		'data_timestamp' => 'int',
		'src_zoneid' => 'int',
		'deity_exp' => 'int64_t',
		'dp' => 'int',
		'littlepet' => 'Octets',
		'flag_mask' => 'unsigned char',
		'ui_transfer' => 'Octets',
		'collision_info' => 'Octets',
		'runescore' => 'int',
		'comsumption' => 'int64_t',
		'astrology_info' => 'Octets',
		'liveness_info' => 'Octets',
		'sale_promotion_info' => 'Octets',
		'propadd' => 'Octets',
		'multi_exp' => 'Octets',
		'fuwen_info' => 'Octets',
		'datagroup' => 'IntInt64MapVector',
		'phase' => 'Octets',
		'award_info_6v6' => 'Octets',
		'hide_and_seek_info' => 'Octets',
		'newyear_award_info' => 'Octets',
		'child_info' => 'Octets',
		'guaji_child_info' => 'Octets',
		'card_info' => 'Octets',
		'challenge_info' => 'Octets',
		'multi_tower_raid' => 'Octets',
		'remote_roleid' => 'int',
	),
	'ItemInfoVo' => array(
		'itemId' => 'int',
		'itemName' => 'string',
		'count' => 'int',
		'itemPrice' => 'int64_t',
		'gameItemId' => 'int'
	),
	'ArcOrder' => array(
		'orderid' => 'int64_t',
		'payamount' => 'int64_t',
		'timestamp' => 'int',
		'iteminfo' => 'ItemInfoVoVector'
	),
	'GRoleBase3' => array(
		'id' => 'int',
		'fort_info' => 'Octets',
		'arc_mall_orders' => 'ArcOrderVector',
		'star_soul_info' => 'Octets',
		'signdata' => 'Octets',
		'reserved5' => 'char',
		'reserved6' => 'char',
		'reserved7' => 'char',
		'reserved8' => 'char',
		'reserved9' => 'char',
		'reserved10' => 'char',
		'reserved11' => 'char',
		'reserved12' => 'char',
		'reserved13' => 'char',
		'reserved14' => 'char',
		'reserved15' => 'char',
		'reserved16' => 'char',
		'reserved17' => 'char',
		'reserved18' => 'char',
		'reserved19' => 'char',
		'reserved20' => 'char',
		'reserved21' => 'char',
		'reserved22' => 'char',
		'reserved23' => 'char',
		'reserved24' => 'char',
		'reserved25' => 'char',
		'reserved26' => 'char',
		'reserved27' => 'char',
		'reserved28' => 'char',
		'reserved29' => 'char',
		'reserved30' => 'char',
		'reserved31' => 'char',
		'reserved32' => 'char',
		'reserved33' => 'char',
		'reserved34' => 'char',
		'reserved35' => 'char',
		'reserved36' => 'char',
		'reserved37' => 'char',
		'reserved38' => 'char',
		'reserved39' => 'char',
		'reserved40' => 'char'
	),
	'WebMallGoods' => array(
		'goods_id' => 'int',
		'count' => 'int',
		'flagmask' => 'int',
		'timelimit' => 'int',
		'reserved1' => 'int',
		'reserved2' => 'Octets'
	),
	'WebMallFunction' => array(
		'function_id' => 'int',
		'name' => 'string',
		'count' => 'int',
		'price' => 'int',
		'price_before_discount' => 'int',
		'goods' => 'WebMallGoodsVector',
		'reserved1' => 'int',
		'reserved2' => 'Octets'
	),
	'WebOrder' => array(
		'orderid' => 'int64_t',
		'userid' => 'int',
		'roleid' => 'int',
		'paytype' => 'int',
		'functions' => 'WebMallFunctionVector',
		'status' => 'int',
		'timestamp' => 'int',
		'reserved1' => 'int',
		'reserved2' => 'int',
		'reserved3' => 'int'
	),
	'GRoleDetail' => array(
		'id' => 'unsigned int',
		'userid' => 'unsigned int',
		'status' => 'GRoleStatus',
		'name' => 'string',
		'faceid' => 'unsigned char',
		'hairid' => 'unsigned char',
		'gender' => 'unsigned char',
		'create_time' => 'int',
		'cash_add2' => 'unsigned int',
		'cash_total' => 'int',
		'cash_used' => 'unsigned int',
		'cash_serial' => 'int',
		'loginip' => 'int',
		'factionid' => 'unsigned int',
		'familyid' => 'unsigned int',
		'title' => 'unsigned char',
		'sectid' => 'int',
		'initiallevel' => 'short',
		'spouse' => 'unsigned int',
		'jointime' => 'int',
		'inventory' => 'GRolePocket',
		'storehouse' => 'GRoleStorehouse',
		'task' => 'GRoleTask',
		'logs' => 'GShopLogVector',
		'referrer' => 'int',
		'achievement' => 'GRoleAchievement',
		'circleid' => 'unsigned int',
		'circletitlemask' => 'unsigned char',
		'award' => 'GRoleAward',
		'fashionid' => 'unsigned char',
		'base2' => 'GRoleBase2',
		'base3' => 'GRoleBase3',
		'weborders' => 'WebOrderVector',
		'processed_weborders' => 'int64Vector',
		'src_roleid' => 'int',
		'cash_arc_delta' => 'int'
	),
	'RoleArg' => array(
		'roleid' => 'int',
		'data_mask' => 'int',
		'line_id' => 'int'
	),
	'GetRoleRes' => array(
		'retcode' => 'int',
		'data_mask' => 'int',
		'value' => 'GRoleDetail'
	),
	'FamilyId' => array(
		'fid' => 'unsigned int'
	),
	'HostileFaction' => array(
		'fid' => 'unsigned int',
		'name' => 'string',
		'addtime' => 'int'
	),
	'HostileInfo' => array(
		'updatetime' => 'int',
		'actionpoint' => 'unsigned short',
		'protecttime' => 'int',
		'status' => 'unsigned char',
		'hostiles' => 'HostileFactionVector',
		'reserved1' => 'int'
	),
	'IntIntMap' => array(
		'key' => 'int',
		'value' => 'int'
	),
	'GFactionInfo' => array(
		'fid' => 'unsigned int',
		'name' => 'string',
		'announce' => 'string',
		'level' => 'char',
		'member' => 'FamilyIdVector',
		'master' => 'unsigned int',
		'prosperity' => 'unsigned int',
		'createtime' => 'int',
		'deletetime' => 'int',
		'population' => 'int',
		'contribution' => 'int',
		'status' => 'unsigned char',
		'nimbus' => 'int',
		'hostileinfo' => 'HostileInfoVector',
		'charm' => 'int',
		'changenametime' => 'int',
		'namehis' => 'Octets',
		'pk_bonus' => 'unsigned int',
		'dynamic' => 'OctetsVector',
		'datagroup' => 'IntIntMapVector',
	),
	'FactionInfoRes' => array(
		'retcode' => 'int',
		'cachesize' => 'int',
		'value' => 'GFactionInfo',
		'activity' => 'int',
		'act_uptime' => 'int',
		'base_status' => 'int'
	),
	'GRoleForbid' => array(
		'type' => 'unsigned char',
		'time' => 'int',
		'createtime' => 'int',
		'reason' => 'string'
	),
	'GRoleBase' => array(
		'version' => 'unsigned char',
		'id' => 'unsigned int',
		'name' => 'string',
		'faceid' => 'unsigned char',
		'hairid' => 'unsigned char',
		'gender' => 'unsigned char',
		'status' => 'unsigned char',
		'delete_time' => 'int',
		'create_time' => 'int',
		'lastlogin_time' => 'int',
		'familyid' => 'unsigned int',
		'title' => 'unsigned char',
		'config_data' => 'Octets',
		'help_states' => 'Octets',
		'forbid' => 'GRoleForbidVector',
		'spouse' => 'unsigned int',
		'jointime' => 'int',
		'userid' => 'int',
		'sectid' => 'int',
		'initiallevel' => 'short',
		'earid' => 'unsigned char',
		'tailid' => 'unsigned char',
		'circletrack' => 'Octets',
		'fashionid' => 'unsigned char',
		'datagroup' => 'IntIntMapVector',
		'remote_central_type' => 'char'
	),
	'GetRoleBaseRes' => array(
		'retcode' => 'int',
		'value' => 'GRoleBase'
	),
	'GRoleData' => array(
		'base' => 'GRoleBase',
		'status' => 'GRoleStatus',
		'pocket' => 'GRolePocket',
		'storehouse' => 'GRoleStorehouse',
		'task' => 'GRoleTask',
		'base2' => 'GRoleBase2',
		'base3' => 'GRoleBase3',
		'achievement' => 'GRoleAchievement'
	),
	'RoleDataRes' => array(
		'retcode' => 'int',
		'value' => 'GRoleData'
	),
	'RoleDataPair' => array(
		'key' => 'RoleId',
		'overwrite' => 'char',
		'value' => 'GRoleData'
	),
	'RpcRetcode' => array(
		'retcode' => 'int'
	),
	'GMListOnlineUser' => array(
		'gmroleid' => 'int',
		'localsid' => 'int',
		'handler' => 'int',
		'cond' => 'Octets',
	),
	'GMPlayerInfo' => array(
		'userid' => 'int',
		'roleid' => 'int',
		'linkid' => 'int',
		'localsid' => 'unsigned int',
		'gsid' => 'int',
		'status' => 'char',
		'name' => 'string'
	),
	'GMListOnlineUser_Re' => array(
		'retcode' => 'int',
		'gmroleid' => 'int',
		'localsid' => 'int',
		'handler' => 'int',
		'userlist' => 'GMPlayerInfoVector'
	),
	'GMKickoutRole' => array(
		'gmroleid' => 'int',
		'localsid' => 'int',
		'kickroleid' => 'int',
		'forbid_time' => 'int',
		'reason' => 'string'
	),
	'GMKickoutRole_Re' => array(
		'retcode' => 'int',
		'gmroleid' => 'int',
		'localsid' => 'int',
		'kickroleid' => 'int'
	),
	'SysSendMail' => array(
		'tid' => 'unsigned int',
		'sysid' => 'int',
		'sys_type' => 'unsigned char',
		'receiver' => 'int',
		'title' => 'string',
		'context' => 'string',
		'attach_obj' => 'GRoleInventory',
		'attach_money' => 'unsigned int',
	),
	'SysSendMail_Re' => array(
		'retcode' => 'unsigned short',
		'tid' => 'unsigned int'
	),
	'PublicChat' => array(
		'channel' => 'unsigned char',
		'emotion' => 'unsigned char',
		'roleid' => 'int',
		'localsid' => 'int',
		'msg' => 'string',
		'data' => 'Octets',
		'item_pos' => 'int'
	),
	'PublicChat_Re' => array(
		'channel' => 'unsigned char',
		'emotion' => 'unsigned char',
		'srcroleid' => 'int',
		'msg' => 'string',
		'data' => 'Octets'
	),
	'DBRawReadArg' => array(
		'table' => 'AnsiString',
		'handle' => 'Octets',
		'key' => 'Octets'
	),
	'RawKeyValue' => array(
		'key' => 'Octets',
		'value' => 'Octets'
	),
	'DBRawReadRes' => array(
		'retcode' => 'int',
		'handle' => 'Octets',
		'values' => 'RawKeyValueVector',
	),
	'GFactionMaster' => array(
		'fid' => 'int',
		'master' => 'int'
	),
	'GCityInfo' => array(
		'battle_id' => 'int',
		'owner' => 'GFactionMaster',
		'occupy_time' => 'int'
	),
	'GChallenger' => array(
		'challenger' => 'GFactionMaster',
		'assistant' => 'GFactionMaster',
		'begin_time' => 'int'
	),
	'GCity' => array(
		'info' => 'GCityInfo',
		'challengers' => 'GChallengerVector',
		'detail' => 'Octets',
		'timestamp' => 'int',
		'reserve2' => 'int',
		'reserve4' => 'int'
	),
	'GCityStore' => array(
		'cities' => 'GCityVector',
		'reserve1' => 'int',
		'reserve2' => 'int',
		'reserve3' => 'int'
	),
	'DBTerritoryListLoadArg' => array(
		'default_ids' => 'intVector'
	),
	'GTChallenge' => array(
		'factionid' => 'unsigned int',
		'itemcount' => 'int',
		'zoneid' => 'unsigned int',
		'is_revenge' => 'char',
		'reserved2' => 'char',
		'reserved3' => 'char',
		'reserved4' => 'char',
		'reserved5' => 'int',
		'reserved6' => 'int'
	),
	'GUniqTerritoryProduce' => array(
		'produce_line' => 'int',
		'start_time' => 'int',
		'can_cancel' => 'char'
	),
	'GTerritoryInfo' => array(
		'id' => 'int',
		'owner' => 'unsigned int',
		'occupy_time' => 'int',
		'color' => 'int',
		'challengelist' => 'GTChallengeVector',
		'defender' => 'unsigned int',
		'success_challenge' => 'GTChallenge',
		'success_award' => 'float',
		'start_time' => 'int',
		'assis_drawn_num' => 'int',
		'rand_award_itemid' => 'int',
		'rand_award_itemcount' => 'int',
		'rand_award_drawn' => 'char',
		'owner_zoneid' => 'unsigned int',
		'old_owner_zoneid' => 'unsigned int',
		'defender_zoneid' => 'unsigned int',
		'status' => 'int',
		'produce' => 'GUniqTerritoryProduceVector',
		'owner_item_drawn' => 'char',
		'reserved4' => 'char',
		'reserved5' => 'char',
		'new_defender_fid' => 'unsigned int',
		'reserved7' => 'int',
		'reserved8' => 'int'
	),
	'GTRobInfo' => array(
		'territory_id' => 'int',
		'fight_time' => 'int',
		'reserved1' => 'int',
		'reserved2' => 'int'
	),
	'GTerritoryStore' => array(
		'status' => 'int',
		'tlist' => 'GTerritoryInfoVector',
		'blessed_territory' => 'int',
		'num1faction' => 'unsigned int',
		'new_blessed_territory' => 'int',
		'rob_list' => 'GTRobInfoVector',
		'reserved1' => 'char',
		'reserved2' => 'char',
		'reserved3' => 'char'
	),
	'DBTerritoryListLoadRes' => array(
		'retcode' => 'int',
		'store' => 'GTerritoryStore'
	),
	'GFolk' => array(
		'rid' => 'unsigned int',
		'name' => 'string',
		'nickname' => 'string',
		'level' => 'unsigned char',
		'title' => 'unsigned char',
		'occupation' => 'unsigned char',
		'contribution' => 'int',
		'jointime' => 'int',
		'devotion' => 'int'
	),
	'GFamilySkill' => array(
		'id' => 'unsigned int',
		'level' => 'int',
		'ability' => 'int',
		'reserved' => 'int'
	),
	'GFamily' => array(
		'id' => 'unsigned int',
		'name' => 'string',
		'master' => 'unsigned int',
		'factionid' => 'unsigned int',
		'member' => 'GFolkVector',
		'skills' => 'GFamilySkillVector',
		'task_record' => 'intVector',
		'task_data' => 'Octets',
		'announce' => 'Octets',
		'createtime' => 'int',
		'jointime' => 'int',
		'deletetime' => 'int',
		'changenametime' => 'int',
		'namehis' => 'Octets',
		'reserved1' => 'char',
		'reserved2' => 'short',
		'reserved3' => 'int',
		'reserved4' => 'int'
	),
	'FamilyGetRes' => array(
		'retcode' => 'int',
		'value' => 'GFamily'
	),
	'GetRoleIdArg' => array(
		'rolename' => 'string',
		'reason' => 'unsigned char'
	),
	'GetRoleIdRes' => array(
		'retcode' => 'int',
		'roleid' => 'int',
		'newname' => 'string'
	),
	'GMShutupRole' => array(
		'gmroleid' => 'int',
		'localsid' => 'int',
		'dstroleid' => 'int',
		'forbid_time' => 'int',
		'reason' => 'string'
	),
	'GMShutupRole_Re' => array(
		'retcode' => 'int',
		'dstroleid' => 'int',
		'forbid_time' => 'int'
	)
);