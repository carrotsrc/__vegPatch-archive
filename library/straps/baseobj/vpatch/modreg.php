<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class modreg extends StrapBase
	{
		public function process(&$xml)
		{
			while(($tag = $xml->getNextTag()) != null) {
				if($tag->element == "/obj")
					break;

				if($tag->element == "module")
					$this->handleModule($tag);
				else
				if($tag->element == "instance")
					$this->handleInstance($tag, $xml);
				else
				if($tag->element == "relationship")
					addRelationship($tag);

			}
		}

		private function handleModule($tag)
		{
			$name = "";
			$space = "";
			$type = "";
			$out = null;
			$rout = null;
			global $log;

			foreach($tag->attributes as $a => $v)
				if($a == "name")
					$name = $v;
				else
				if($a == "type") {
					if($v == "component")
						$type = 0;
					else
					if($v == "panel")
						$type = 1;
					else
					if($v == "plugin")
						$type = 2;
				}
				else
				if($a == "space")
					$space = $v;
				else
				if($a == "out")
					$out = $v;
				else
				if($a == "rout")
					$rout = $v;

			if($type < 2 && $space == "") {
				$log[] = "! Error registering module $name - space unspecified";
				return;
			}

			$sql = "SELECT id FROM modreg WHERE module_name='$name' AND module_type='$type' AND space='$space';";
			$id = $this->db->sendQuery($sql, false, false);
			$rid = null;

			if(!$id) {
				if($this->registerModule($type, $name, $space)) {
					$id = $this->db->getLastId();
					$rtype = "Component";
					if($type == 1)
						$rtype = "Panel";
					else
					if($type == 2)
						$rtype = "Plugin";


					$log[] = "+ Registered $rtype $space/$name";
					$rid = $this->resManager->addResource($rtype, $id, $name);
					$log[] = "+ Added resource $rtype('$name') => $id";
				}
			}
			else {
				$id = $id[0][0];

				$rtype = "Component";
				if($type == 1)
					$rtype = "Panel";
				else
				if($type == 2)
					$rtype = "Plugin";

				$res = $this->resManager->queryAssoc("$rtype('$id');");
				$rid = $res[0][0];
				$log[] = "< Retrieved $space / $rtype('$name') => $id";
			}

			if($out != null)
				setVariable($out, $id);

			if($rout != null)
				setVariable($rout, $rid);

		}

		private function registerModule($type, $name, $space)
		{
			return $this->arrayInsert('modreg', array(
							'module_type' => $type,
							'module_name' => $name,
							'space' => $space,
							'active' => 1,
							'version' => '1.0'
							));
		}

		private function handleInstance($tag, &$xml)
		{
			$label = "";
			$type = "";
			$id = "";
			$out = null;
			$rout = null;
			$params = array();
			global $log;

			foreach($tag->attributes as $a => $v)
				if($a == "label")
					$label = $v;
				else
				if($a == "id") {
					$id = processVariable($v);
				}
				else
				if($a == "out")
					$out = $v;
				else
				if($a == "rout")
					$rout = $v;

			while(($tag = $xml->getNextTag())  != null) {
				if($tag->element == "/instance")
					break;

				if($tag->element == "param")
					if(isset($tag->attributes['name']) && isset($tag->attributes['value']))
						$params[$tag->attributes['name']] = $tag->attributes['value'];
			}
			$obj = null;
			
			$obj = ModMan::getComponent($id, 0, $this->db);
			if($obj == null) {
				$log[] = "! Component('$id') class does not exist, cannot create instance";
				return null;
			}

			$cid = $obj->createInstance($params);
			$ridi = $this->resManager->addResource("Instance", $cid, $label);
			$log[] = "+ Created instance of Component('$id') -> Instance($ridi)";

			$ridc = $this->resManager->queryAssoc("Component('$id');");
			if(!$ridc) {
				$log[] = "! No registered resource for Component('$id')";
				return null;
			}
			$ridc = $ridc[0][0];

			$this->resManager->createRelationship($ridc, $ridi);
			$log[] = "+ Created Realtionship between $ridc < $ridi :0";

			if($out != null)
				setVariable($out, $cid);
			if($rout != null)
				setVariable($rout, $ridi);
		}
	}
