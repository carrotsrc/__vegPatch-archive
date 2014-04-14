<?php
/* (C)opyright 2014, Carrotsrc.org
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
			//$this->rqSql = new RQSQL();
			
			//	Initialize the static objects used
			//	in working with resources
			ResCast::init($databaseConnection);
				
			//ResRel::init();
			
		}	

		public function typeCast($type)
		{
			return ResCast::cast($type);
		}

		public function addTypeHandler($type, $handler)
		{
			if($this->getTypeHandler($type) != null)
				return false;
			
			$query = "INSERT INTO rescast (type, handler) VALUES ('$type', '$handler');"; 
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
			
			if(!mysql_num_rows($result))
				return null;
			 	
			$row = mysql_fetch_assoc($result);
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
				if(($tp = $this->typeCast($typeId)) != null)
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
		
		public function getResourceFromId($id)
		{
			$query = "SELECT * FROM respool WHERE id='$id';";
			$result = $this->db->sendQuery($query);

			if(!$result)
				return null;

			if(!mysql_num_rows($result))
				return null;

			$row = mysql_fetch_assoc($result);
			$type = $row['type_id'];
			$label = $row['label'];
			$hid = $row['handler_ref'];
			$handler = $this->getTypeHandler($type);

			/* TODO
			*  hid is not the handler but the reference for the handler
			*  this needs to be changed represent the rest of the system
			*/

			return array('id' => $id, 'type' => $type, 'label' => $label, 'handler' => $hid);
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

		public function getResourcesOfType($cast)
		{
			$query = "SELECT respool.id, respool.label FROM respool ".
					 "LEFT JOIN rescast ".
					 "ON respool.type_id = rescast.id ".
					 "WHERE rescast.type='$cast'";

			$result = $this->db->sendQuery($query);
			
			if(!$result)
				return null;
				
			if(!mysql_num_rows($result))
				return null;

			$res = array();

			while($row = mysql_fetch_assoc($result))
			{
				$id = $row['id'];
				$label = $row['label'];

				$res[] = new Resource(null, $label, null, $id, null);
			}

			return $res;
		}

		public function getHandlerRef($rid)
		{
			$sql = "SELECT handler_ref FROM respool WHERE id='$rid'";
			$result = $this->db->sendQuery($sql, false, false);

			if(!$result)
				return false;

			return $result[0][0];
				
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

		public function getResources($offset = 1, $count = 0, $filter = null)
		{
			$sql = "SELECT * FROM respool WHERE id >= $offset";
			if($count != 0)
				$sql .= " LIMIT $count";
			$sql .= ";";
			return $this->db->sendQuery($sql, false, false);
		}

		public function getResourcePage($page = 1, $count = 10)
		{
			$targ = ($count * ($page-1));

			$sql = "SELECT * FROM respool LIMIT $count OFFSET $targ;";
			return $this->db->sendQuery($sql, false, false);
		}
		
		public function getRID($type, $hRef)
		{
			$type = ResCast::cast($type);
			if($type == null)
				return false;

			$sql = "SELECT id FROM respool WHERE handler_ref='$hRef' AND type_id='".$type['id']."'";
			$result = $this->db->sendQuery($sql, false, false);

			if(!$result)
				return false;

			return $result[0][0];
		}

		public function getTypeFromId($rid)
		{
			$ro = $this->getResourceFromId($rid);
			if($ro == null)
				return null;

			return $this->typeCast($ro['type']);
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
			/*$root = new RQuery($this->rqSql);
			$root->setRequestRNID($rnid);
			$root->initBuild($query);
			$sql = $root->generateSQL();
			*/
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

			return $id[0][0];
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
