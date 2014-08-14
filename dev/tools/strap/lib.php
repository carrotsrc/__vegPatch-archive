<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	//include(SystemConfig::relativeAppPath("system/helpers/xml.php"));
	$flag = KS_MOD;
	include(SystemConfig::relativeAppPath("system/helpers/vpxml.php"));
	include(SystemConfig::relativeAppPath("system/dbacc.php"));
	include(SystemConfig::relativeAppPath("system/structure/uiblock.php"));
	include(SystemConfig::relativeAppPath("system/module/modman.php"));
	include(SystemConfig::relativeAppPath("library/straps/baseobj/strapbase.php"));
	include(SystemConfig::relativeAppPath("system/libload.php"));
	class s_table 
	{
		public $name = "";
		public $engine = "InnoDB";
		public $primary = "";
		public $charset = "utf8";
		public $columns = array();
		public $indecies = null;
	}

	class s_table_column {
		public $name;
		public $type;
		public $extra;
	}
	class s_table_index {
		public $name;
		public $cols = array();
	}
	
	$varlist = array();
	$baselist = array();
	$resManager = null;

	global $log;
	$log = array();


	function loadFile($path, $fm)
	{
		if(!file_exists($path))
			return null;

		$f = $fm->openFile($path, 'r');
		return $f->read();
	}

	function loadStrapper($name, $space, $db, &$xml)
	{
		global $baselist;
		global $log;

		if(isset($baselist[$space][$name]))
			return $baselist[$space][$name];

		if(!isset($baselist[$space]))
			$baselist[$space] = array();

		global $resManager;
		if(!file_exists(SystemConfig::relativeAppPath("library/straps/baseobj/$space/$name.php"))) {
			$log[] = "! Strapper $space/$name does not exist";
			return null;
		}
		include_once(SystemConfig::relativeAppPath("library/straps/baseobj/$space/$name.php"));
		$base = new $name($db, $resManager);
		$baselist[$space][$name] = $base;
		return $base;
	}

	function loadStrap($path, $fm, $db, $rman)
	{
		global $varlist;
		global $resManager;
		global $log;

		$resManager = new ResMan($db);

		$xml = new VPXML();

		$fxml = loadFile($path, $fm);
		if($fxml == null) {
			$log[] = "! Strap file does not exist";
			return;
		}
		
		$xml->init($fxml);
		
		while(($tag = $xml->getNextTag()) != null) {
			if($tag->name == 'table') {
				$table = loadTableData($tag, $xml);
				if($table == null) {
					echo "Error in strap structure with parent: {$tag->name}<br />";
					return false;
				}
				strapTable($table, $db);
			}
			else
			if($tag->name == 'rtype') {
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
					$r = $db->sendQuery($sql);
					if(!$r)
						continue;
					$base = $r[0]['id'];
					$sql = "INSERT INTO `rescast` (`type`, `handler`, `base`) VALUES ";
					$sql .= "('{$type['name']}','0', '{$base}');";
					if(!$db->sendQuery($sql))
						$log[] = "! Failed to create [$base]{$type['name']}()";
					else {
						$log[] = "+ Created [$base]{$type['name']}()";
						ResCast::init($db);
					}
				}
				else
					$log[] = "# [{$type['base']}]{$type['name']}() Already exists";
			
			}
			else
			if($tag->name == 'edge') {
				$edge = array();
				foreach($tag->attributes as $a => $v) {
					if($a == 'name')
						$edge['name']= $v;
					else
					if($a == 'type')
						$edge['type'] = $v;
				}
				$tid = $db->sendQuery("SELECT id FROM rescast WHERE type='{$edge['type']}'");
				if(!$tid) {
					$log[] = "! Resource type {$edge['type']} for :{$edge['name']} does not exist";
					continue;
				}
				$tid = $tid[0]['id'];

				if(!$db->sendQuery("SELECT id FROM `edgetype` WHERE label='{$edge['name']}';")) {
					$sql = "INSERT INTO `edgetype` (`rtype`, `label`, `default`) VALUES ";
					$sql .= "('{$tid}','{$edge['name']}', 0);";
					if(!$db->sendQuery($sql))
						$log[] = "! Failed to create :{$edge['name']}";
					else
						$log[] = "+ Created {$edge['type']}():{$edge['name']}";
				} else
					$log[] = "# {$edge['type']}:{$edge['name']} already exists";
			}
			else
			if($tag->name == 'var') {
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
			if($tag->name == "resource") {
				addResource($tag);
			}
			else
			if($tag->name == "relationship") {
				addRelationship($tag);
			}
			else
			if($tag->name == "rbase") {
				addResourceBase($tag);
			}
			else
			if($tag->name == "echo") {
				while(($tag = $xml->getNextTag()) != null) {
					if($tag->name == "/echo")
						break;
					if(!isset($tag->attributes['content']))
						continue;

					$log[] = $tag->attributes['content'];
				}
			}
			else
			if($tag->name == "obj") {
				$name = "";
				$space = "";
				foreach($tag->attributes as $a => $v)
					if($a == 'name')
						$name = $v;
					else
					if($a == 'space')
						$space = $v;

				$obj = loadStrapper($name, $space, $db, $xml);
				if($obj == null)
					continue;

				$obj->process($xml);
			}
		}
		$log[] = "--- finished ---";
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
			if($tag->name == '/table')
				return $table;

			if($tag->name == 'column') {
				$column = new s_table_column();

				foreach($tag->attributes as $a => $v) {
					switch($a) {
					case 'name':
						$column->name = $v;
					break;

					case 'type':
						$column->type = $v;
					break;

					case 'extra':
						$column->extra = $v;
					break;
					}
				}
				$table->columns[] = $column;
			}
			else
			if($tag->name == 'primary') {
				foreach($tag->attributes as $a => $v)
					if($a == 'name')
						$table->primary = $v;
			}
			else
			if($tag->name == 'index') {
				$key = loadIndecies($tag, $xml);
				if($key == null) {
					echo "Error in strap structure with parent: {$tag->name}<br />";
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
		$index = new s_table_index();
		foreach($tag->attributes as $a => $v)
			if($a == 'name')
				$index->name = $v;

		while(($tag = $xml->getNextTag()) != null) {
			if($tag->name == 'column') {
				foreach($tag->attributes as $a => $v)
					if($a == 'name')
						$index->cols[] = $v;
			}
			else
			if($tag->name == '/index')
				return $index;
		}

		return null;
	}

	function strapTable($table, $db)
	{
		global $log;
		if($table->name == "") {
			$log[] = "! Error creating table - No name specified";
			return;
		}
		$sql = "CREATE TABLE IF NOT EXISTS `{$table->name}` (\n";
		$sz = sizeof($table->columns)-1;
		foreach($table->columns as $f) {
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
				$sql .= "\tINDEX `{$i->name}` (";
				$sz = sizeof($i->cols)-1;

				foreach($i->cols as $c) {
					$sql .= "`$c`";
					if($sz-- > 0)
						$sql .= ", ";
				}
				$sql .= ")\n";

				if($si-- > 0)
					$sql .= ", ";
			}
		}
		$sql .= ") ";
		$sql .= "ENGINE={$table->engine} DEFAULT CHARSET={$table->charset};";

		if(!$db->sendQuery($sql)) {
			$log[] = "Error creating table `{$table->name}`";
			$log[] = "\n$sql\n";
			return;
		}
		$log[] = "+ Created table `{$table->name}`";
		return;
	}

	function strapRootPanel($fm, $db, $rman)
	{
		global $log;
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
		echo "<div class=\"log\">";
		echo "<pre>Log\n---\n\n";
		if($out != null) {
			$errors = 0;
			$dup = 0;

			foreach($log as $l) {
				if(isset($l[0])) {
					if($l[0] == "!")
						$errors++;
					else
					if($l[0] == "#")
						$dup++;
				}


				echo "$l\n";
			}

			if($errors)
				echo "\nerrors: $errors";

			if($dup)
				echo "\nduplicated: $dup";
		}
		echo "</pre>";
		echo "</div>";
	}

	function strapSystemPanel($fm, $db, $rman)
	{
		$out = null;
		global $log;
		$path = SystemConfig::relativeAppPath("library/straps");
		if(isset($_POST['name'])) {
			$log[] = "Loading strap file `{$_POST['name']}`...";
			$log[] = date("H:i:s d-m-Y", time('now'));
			$log[] = "";
			if(loadStrap("$path/{$_POST['name']}", $fm, $db, $rman))
				$out = "Loaded strap file.";
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
		echo "<div class=\"log\">";
		echo "<pre>Log\n---\n\n";
		if($out != null) {
			$errors = 0;
			$dup = 0;

			foreach($log as $l) {
				if(isset($l[0])) {
					if($l[0] == "!")
						$errors++;
					else
					if($l[0] == "#")
						$dup++;
				}


				echo "$l\n";
			}

			if($errors)
				echo "\nerrors: $errors";

			if($dup)
				echo "\nduplicated: $dup";
		}
		echo "</pre>";
		echo "</div>";
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
		global $log;
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
			if($a == 'rout')
				$out = $v;

		if($ref[0] == "{")
			$ref = processVariable($ref);

		if(!($res = $resManager->queryAssoc("$type('$label');"))) {
			$id = $resManager->addResource($type, $ref, $label);
			if(!$id) {
				$log[] = "! Failed to create resource $type('$label') =&gt; $ref";
				return;
			}

			$log[] = "+ Added resource $type('$label') =&gt; $ref";
		}
		else  {
			$id = intval($res[0]['id']);
			$log[] = "< Retrieved $type('$label') =&gt; $ref";
		}

		if($out != null)
			$varlist[$out] = $id;
	}

	function addRelationship($tag)
	{
		global $resManager;
		global $varlist;
		global $log;
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
		if($parent == null || $parent == 0 || $parent == "") {
			$log[] = "! Parent unspecified $child &lt; $parent :$edge";
			return;
		}

		if($child == null || $child == 0 || $child == "") {
			$log[] = "! Child unspecified $parent &gt; $child :$edge";
			return;
		}

		$id = $resManager->createRelationship($parent, $child, $resManager->getEdge($edge));
		if(!$id)
			$log[] = "! Failed to create relationship between $parent > $child :$edge";
		else
			$log[] = "+ Created relationship between $parent > $child :$edge";

		if($out != null)
			$varlist[$out] = $id;
	}

	function addResourceBase($tag)
	{
		global $resManager;
		global $varlist;
		global $log;

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
		if(!$id)
			$log[] = "! Failed to create base [$label]";
		else
			$log[] = "+ Created base [$label]";

		if($out != null)
			$varlist[$out] = $id;
	}
?>
