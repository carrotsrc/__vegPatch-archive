<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	require_once("rescast.php");
	/*require_once("resrel.php");
	require_once("rquery.php");
	require_once("rqstat.php");
*/
	require_once("qparse.php");

	/* TODO:
	*  it feels like this is too bloated
	*  maybe split functionality out.
	*  The methods could be tighter
	*  as well
	*/

	class ResMan// extends DBAcc
	{	
		private $rqSql;
		private $db;
		
		public function  __construct($databaseConnection)
		{
			$this->db = $databaseConnection;
			ResCast::init($databaseConnection);
		}	

		public function getTypeHandler($type)
		{
			$query = null;

			if(is_numeric($type))
				$query = "SELECT * FROM rescast WHERE id='$type';";
			else
				$query = "SELECT * FROM rescast WHERE type='$type';";

			$result = $this->db->sendQuery($query);

			if(!$result)
				return null;
			
			$row = $result[0];
			return $row['handler'];
		}

		public function getTypeFromHandler($ref)
		{
			$query = "SELECT id FROM rescast WHERE handler='$ref';";
			$result = $this->db->sendQuery($query, false, false);
			if(!$result)
				return null;

			return $result[0][0];
		}

		public function addResource($typeId, $handlerRef, $label)
		{
			if(!is_numeric($typeId)) {
				if(($tp = ResCast::cast($typeId)) != null)
					$typeId = $tp['id'];
				else
					return false;
			}
			$query = "INSERT INTO respool (type_id, handler_ref, label) VALUES ('$typeId', '$handlerRef', '$label')";

			if($this->db->sendQuery($query))
				return $this->db->getLastId();
			else
				return false;
		}

		public function removeResource($rid)
		{
			$this->db->sendQuery("DELETE FROM respool WHERE id='$rid';");
//			$this->cleanEdges($rid);
		}
		
		public function getResourceFromId($rid)
		{
			$query = "SELECT * FROM respool WHERE id='$rid';";
			$result = $this->db->sendQuery($query);

			if(!$result)
				return null;

			$row = $result[0];
			$type = $row['type_id'];
			$label = $row['label'];
			$hid = $row['handler_ref'];
			$handler = $this->getTypeHandler($type);

			/* TODO
			*  hid is not the handler but the reference for the handler
			*  this needs to be changed represent the rest of the system
			*/

			return array('id' => $rid, 'type' => $type, 'label' => $label, 'handler' => $hid);
		}

		public function modifyResource($rid, $modify)
		{
			$sz = 0;
			if(($sz=sizeof($modify)) == 0)
				return;

			$sql = "UPDATE respool SET";
			foreach($modify as $key => $value) {
				$sql .= " $key='$value'";
				if($sz > 1)
					$sql .= ",";
				$sz--;
			}
			$sql .= " WHERE id='$rid';";
			return $this->db->sendQuery($sql);
		}

		public function getHandlerRef($rid)
		{
			$sql = "SELECT handler_ref FROM respool WHERE id='$rid'";
			$result = $this->db->sendQuery($sql);

			if(!$result)
				return false;

			return $result[0]['handler_ref'];
				
		}

		public function getlsHandlerRef($rids)
		{
			$sz = sizeof($rids)-1;
			$sql = "SELECT id, handler_ref FROM respool WHERE ";
			for($i=0;$i<$sz;$i++)
				$sql .= "id='{$rids[$i]}' OR ";

			$sql .= "id='{$rids[$sz]}';";

			return $this->db->sendQuery($sql, false, false);
		}

		public function getTypeFromId($rid)
		{
			$ro = $this->getResourceFromId($rid);
			if($ro == null)
				return null;

			return ResCast::cast($ro['type']);
		}

		public function cleanEdges($rid)
		{
			$sql = "DELETE FROM resnet WHERE parent_id='$rid' OR child_id='$rid';";
			$this->db->sendQuery($sql, false, false);
		}

		public function getResCast()
		{
			return ResCast::getAllTypes();
		}

		public function reverseLookup($type, $ref)
		{
		}

		public function queryAssoc($query, $rnid = false)
		{
			$qp = new QParse();
			$q = $qp->parse($query);
			$sql = $qp->generate($q);
			return $this->db->sendQuery($sql,false,false);
		}

		public function ConvertRQL($query)
		{
			$qp = new QParse();
			$q = $qp->parse($query);
			return $qp->generate($q);
		}

		public function createRelationship($rp, $rc, $edge = 0)
		{
			if(($id = $this->checkRelationship($rp, $rc)) != false)
				return $id;

			$sql = "INSERT INTO resnet (parent_id, child_id, edge) VALUES ('$rp', '$rc', '$edge');";
			$result = $this->db->sendQuery($sql);
			if($result)
				return $this->db->getLastId();
			else
				return false;
		}

		private function checkRelationship($rp, $rc)
		{
			$sql = "SELECT id FROM resnet WHERE parent_id='$rp' AND child_id='$rc';";
			return $this->db->sendQuery($sql, false, false);
		}

		public function removeRelationship($rp, $rc)
		{
			$sql = "DELETE FROM resnet WHERE parent_id='$rp' AND child_id='$rc';";
			return $this->db->sendQuery($sql, false, false);
		}

		public function removeRelationshipWithId($id)
		{
			$sql = "DELETE FROM resnet WHERE id='$id';";
			return $this->db->sendQuery($sql, false, false);
		}

		public function getBehaviours()
		{

		}

		public function getEdge($label)
		{
			$id = $this->db->sendQuery("SELECT id FROM edgetype WHERE label='$label';", false, false);
			if(!$id)
				return 0;

			return $id[0]['id'];
		}

		public function getEdgesOfType($type)
		{
			$s = "";
			if(is_numeric($type))
				$s = "`rescast`.`id`='$type'";
			else
				$s = "`rescast`.`type`='$type'";

			return $this->db->sendQuery("SELECT `edgetype`.`id`, `edgetype`.`label` FROM `edgetype` JOIN `rescast` on `rescast`.`id`=`edgetype`.`rtype` WHERE $s;", false, false);
		}

		public function addResourceBase($label)
		{
			$sql = "SELECT id FROM resbase WHERE label='$label';";

			$id = $this->db->sendQuery($sql, false, false);
			if($id)
				return $id;

			
			$sql = "INSERT INTO `resbase` (`label`) VALUES ('$label');";
			if(!$this->db->sendQuery($sql))
				return false;

			return $this->db->getLastId();
			
		}
	}
?>
