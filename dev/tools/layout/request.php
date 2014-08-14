<?php
	function request_modules($type)
	{
		global $db;
		$panels = $db->sendQuery("SELECT `id`, `module_name`, `space` FROM `modreg` WHERE `module_type`='$type' ORDER BY `space`, `id`");
		if(!$panels)
			die('303');

		json_encode_array($panels, array('module_name' => 'name', 'space' => 'collection'));
	}

	function request_instances()
	{
		if(!isset($_GET['module']))
			die("303");

		$module = $_GET['module'];
		global $resman;

		$instances = $resman->queryAssoc("Instance(){r,l}<Component('$module');");

		json_encode_array($instances, array('id' => 'rid'));
	}

	function request()
	{
		if(!isset($_GET['request']))
			return;

		switch($_GET['request']) {
		case 'panels':
			request_modules(1);
			break;

		case 'components':
			request_modules(0);
			break;

		case 'instances':
			request_instances();
			break;
		}
	}
?>
