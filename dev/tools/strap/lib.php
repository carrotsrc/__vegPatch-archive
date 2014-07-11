<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	//include(SystemConfig::relativeAppPath("system/helpers/xml.php"));
	include(SystemConfig::relativeAppPath("system/helpers/vpxml.php"));
	include(SystemConfig::relativeAppPath("system/dbacc.php"));
	include(SystemConfig::relativeAppPath("system/structure/blocks/schemablock.php"));
	include(SystemConfig::relativeAppPath("system/structure/module/modman.php"));
	include(SystemConfig::relativeAppPath("library/straps/baseobj/strapbase.php"));
	include(SystemConfig::relativeAppPath("system/libload.php"));
	class s_table 
	{
		public $name = "";
		public $engine = "InnoDB";
		public $primary = "";
		public $charset = "utf8";
		public $fields = array();
		public $indecies = null;
	}

	class s_table_field {
		public $name;
		public $type;
		public $extra;
	}
	class s_table_key {
		public $name;
		public $cols = array();
	}
	
	$varlist = array();
	$baselist = array();
	$resManager = null;


	function loadFile($path, $fm)
	{
		$f = $fm->openFile($path, 'r');
		return $f->read();
	}

	function loadBase($name, $space, $db, &$xml)
	{
		global $baselist;
		if(isset($baselist[$space][$name]))
			return $baselist[$space][$name];

		if(!isset($baselist[$space]))
			$baselist[$space] = array();

		global $resManager;
		include_once(SystemConfig::relativeAppPath("library/straps/baseobj/$space/$name.php"));
		$base = new $name($db, $resManager);
		$baselist[$space][$name] = $base;
		return $base;
	}

	function loadStrap($path, $fm, $db, $rman)
	{
		global $varlist;
		global $resManager;

		$resManager = new ResMan($db);

		$xml = new VPXML();

		$fxml = loadFile($path, $fm);
		$xml->init($fxml);
		
		while(($tag = $xml->getNextTag()) != null) {
			if($tag->element == 'table') {
				$table = loadTableData($tag, $xml);
				if($table == null) {
					echo "Error in strap structure with parent: {$tag->element}<br />";
					return false;
				}
				strapTable($table, $db);
			}
			else
			if($tag->element == 'rtype') {
				$type = array();
				foreach($tag->attributes as $a => $v) {
					if($a == 'name')
						$type['name'] = $v;
					else
					if($a == 'base')
						$type['base'] = $v;
				}
				if(!($res = $db->sendQuery("SELECT id FROM `rescast` WHERE `type`='{$type['name']}';"))) {
					$sql = "SELECT `id` FROM `resbase` WHERE `label`='{$type['base']}';";
					$r = $db->sendQuery($sql, false, false);
					if(!$r)
						continue;
					$base = $r[0][0];
					$sql = "INSERT INTO `rescast` (`type`, `handler`, `base`) VALUES ";
					$sql .= "('{$type['name']}','0', '{$base}');";
					$db->sendQuery($sql);
				}
			
			}
			else
			if($tag->element == 'edge') {
				$edge = array();
				foreach($tag->attributes as $a => $v) {
					if($a == 'name')
						$edge['name']= $v;
					else
					if($a == 'type')
						$edge['type'] = $v;
				}
				$tid = $db->sendQuery("SELECT id FROM rescast WHERE type='{$edge['type']}'", false, false);
				if(!$tid)
					continue;
				$tid = $tid[0][0];

				if(!$db->sendQuery("SELECT id FROM `edgetype` WHERE label='{$edge['name']}';")) {
					$sql = "INSERT INTO `edgetype` (`rtype`, `label`, `default`) VALUES ";
					$sql .= "('{$tid}','{$edge['name']}', 0);";
					$db->sendQuery($sql);
				}
			}
			else
			if($tag->element == 'var') {
				$name = "";
				$value = "";
				foreach($tag->attributes as $a => $v)
					if($a == 'name')
						$name = $v;
					else
					if($a == 'value')
						$value = $v;

				setVariable($name, $value);
			}
			else
			if($tag->element == "resource") {
				addResource($tag);
			}
			else
			if($tag->element == "relationship") {
				addRelationship($tag);
			}
			else
			if($tag->element == "rbase") {
				addResourceBase($tag);
			}
			else
			if($tag->element == "obj") {
				$name = "";
				$space = "";
				foreach($tag->attributes as $a => $v)
					if($a == 'name')
						$name = $v;
					else
					if($a == 'space')
						$space = $v;

				$obj = loadBase($name, $space, $db, $xml);
				$obj->process($xml);
			}
		}
		return true;
	}

	function loadTableData($tag, &$xml)
	{
		$table = new s_table();
		foreach($tag->attributes as $a => $v) {
			if($a == 'name')
				$table->name = $v;
			else
			if($a == 'engine')
				$table->engine = $v;
			else
			if($a == 'charset')
				$table->charset = $v;
		}

		while(($tag = $xml->getNextTag()) != null) {
			if($tag->element == '/table')
				return $table;

			if($tag->element == 'field') {
				$field = new s_table_field();

				foreach($tag->attributes as $a => $v) {
					switch($a) {
					case 'name':
						$field->name = $v;
					break;

					case 'type':
						$field->type = $v;
					break;

					case 'extra':
						$field->extra = $v;
					break;
					}
				}
				$table->fields[] = $field;
			}
			else
			if($tag->element == 'primary') {
				foreach($tag->attributes as $a => $v)
					if($a == 'name')
						$table->primary = $v;
			}
			else
			if($tag->element == 'key') {
				$key = loadIndecies($tag, $xml);
				if($key == null) {
					echo "Error in strap structure with parent: {$tag->element}<br />";
					return null;
				}

				if(!is_array($table->indecies))
					$table->indecies = array();

				$table->indecies[] = $key;
			}
		}
		return null;
	}

	function loadIndecies($tag, &$xml)
	{
		$index = new s_table_key();
		foreach($tag->attributes as $a => $v)
			if($a == 'name')
				$index->name = $v;

		while(($tag = $xml->getNextTag()) != null) {
			if($tag->element == 'index') {
				foreach($tag->attributes as $a => $v)
					if($a == 'col')
						$index->cols[] = $v;
			}
			else
			if($tag->element == '/key')
				return $index;
		}

		return null;
	}

	function strapTable($table, $db)
	{
		if($table->name == "")
			return;
		$sql = "CREATE TABLE IF NOT EXISTS `{$table->name}` (\n";
		$sz = sizeof($table->fields)-1;
		foreach($table->fields as $f) {
			$sql .= "\t`{$f->name}` {$f->type} {$f->extra}";

			if($sz-- > 0)
				$sql .= ", ";
			else
			if($table->primary != "" || $table->indecies != null)
				$sql .= ", ";

			$sql .= "\n";
		}

		if($table->primary != "") {
			$sql .= "\tPRIMARY KEY (`{$table->primary}`)";
			if($table->indecies != null)
				$sql .= ",";
			$sql .= "\n";
		}

		if($table->indecies != null) {
			$si = sizeof($table->indecies)-1;
			foreach($table->indecies as $i) {
				$sql .= "\tKEY `{$i->name}` (";
				$sz = sizeof($i->cols)-1;

				foreach($i->cols as $c) {
					$sql .= "`$c`";
					if($sz-- > 1)
						$sql .= ", ";
				}
				$sql .= ")\n";

				if($si-- > 0)
					$sql .= ", ";
			}
		}
		$sql .= ") ";
		$sql .= "ENGINE={$table->engine} DEFAULT CHARSET={$table->charset};";
		return $db->sendQuery($sql);
	}

	function strapRootPanel($fm, $db, $rman)
	{
		echo "<b>Root System</b><br />";
		echo "<div class=\"form-item\" style=\"width: 170px;\">";
		$out = null;
		if(isset($_POST['op']) && $_POST['op'] == 1) {
			$path = SystemConfig::relativeAppPath("library/straps/root.strap");
			if(file_exists($path) == false) {
				echo "Cannot locate root.strap";
				return;
			}
			else {
				if(loadStrap($path, $fm, $db, $rman))
					$out = "Successfully loaded!";
				else
					$out = "Error on load!";
			}
		}
		echo "<form method=\"post\" action=\"index.php?tool=strap&mode=root\">";
			echo "<input type=\"hidden\" name=\"op\" value=\"1\">";
			echo "<input type=\"submit\" class=\"form-button\" value=\"Strap root system\" style=\"width: 100%; font-size:large;\">";
		echo "</form>";
		echo "</div>";
		if($out != null)
			echo $out;
	}

	function strapSystemPanel($fm, $db, $rman)
	{
		$out = null;
		$path = SystemConfig::relativeAppPath("library/straps");
		if(isset($_POST['name'])) {
			if(loadStrap("$path/{$_POST['name']}", $fm, $db, $rman))
				$out = "Loaded Successfully!";
			else
				$out = "Error on load!";
		}

		echo "<b>Stap System</b><br />";
		$straplist = $fm->listFiles($path);
			
		echo "<div class=\"form-item\">";
		echo "<form method=\"post\" action=\"index.php?tool=strap&mode=system\">";
			echo "<select name=\"name\" class=\"form-text form-select\" style=\"width: 170px;\">";
				foreach($straplist as $f) {
					$a = explode('.', $f);
					echo "<option value=\"$f\">{$a[0]}</option>";
				}
			echo "</select><br />";
			echo "<input type=\"submit\" value=\"load\" class=\"form-button\"/>";
		echo "</form>";
			
		echo "</div>";
		if($out != null)
			echo $out;
	}

	function setVariable($name, $value)
	{
		global $varlist;
		if($value[0] == '{')
			$value = processVariable($value);

		if($value == "null")
			unset($varlist[$name]);
		else
			$varlist[$name] = $value;
	}

	function processVariable($name)
	{
		$sz = strlen($name);
		if($name[0] != '{') 
			return $name;

		$var = "";
		for($i = 0; $i < $sz; $i++) {
			if($name[$i] == "{")
				continue;

			if($name[$i] == "}")
				break;

			$var .= $name[$i];
		}
		global $varlist;
		if(isset($varlist[$var]))
			return $varlist[$var];
		
		return null;
	}

	function getVariable($var)
	{
		global $varlist;
		if(isset($varlist[$var]))
			return $varlist[$var];
		else
			return null;
	}

	function addResource($tag)
	{
		$label = "";
		$type = "";
		$ref = 0;
		$out = null;
		$id = null;
		global $resManager;
		global $varlist;
		foreach($tag->attributes as $a => $v)
			if($a == 'type')
				$type = $v;
			else
			if($a == 'label')
				$label = $v;
			else
			if($a == 'ref')
				$ref = $v;
			else
			if($a == 'out')
				$out = $v;

		if($ref[0] == "{")
			$ref = processVariable($ref);

		if(!($res = $resManager->queryAssoc("$type('$label');")))
			$id = $resManager->addResource($type, $ref, $label);
		else 
			$id = intval($res[0][0]);

		if($out != null)
			$varlist[$out] = $id;
	}

	function addRelationship($tag)
	{
		global $resManager;
		global $varlist;
		$parent = "";
		$child = "";
		$out = null;
		$edge = 0;
		foreach($tag->attributes as $a => $v)
			if($a == 'parent')
				$parent = $v;
			else
			if($a == 'child')
				$child = $v;
			else
			if($a == 'edge')
				$edge = $v;
			else
			if($a == 'out')
				$out = $v;


		if($parent[0] == "{")
			$parent = processVariable($parent);

		if($child[0] == "{")
			$child = processVariable($child);

		$id = $resManager->createRelationship($parent, $child, $resManager->getEdge($edge));
		if($out != null)
			$varlist[$out] = $id;
	}

	function addResourceBase($tag)
	{
		global $resManager;
		global $varlist;
		$label = null;
		$out = null;
		foreach($tag->attributes as $a => $v)
			if($a == "name")
				$label = $v;
			else
			if($a == 'out')
				$out = $v;

		if($label == null)
			return;


		$id = $resManager->addResourceBase($label);
		if($out != null)
			$varlist[$out] = $id;
	}

	function modreg($tag, &$xml)
	{
	}
?>
