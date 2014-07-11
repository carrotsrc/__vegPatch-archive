<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	/*
	*  This plugin is a CRUD plugin which means
	*  it handles operations on nodes after some
	*  action has been taken on the db.
	*  In this case the plugin takes the resource
	*  and adds it as child to the currently selected
	*  node and as a child to the current graph.
	*  It is used specifically for working with the
	*  nvinterface.
	*/
	class nvgraph_crudPlugin extends Plugin
	{
		private $resManager;
		public function init($instance)
		{
			$this->instance = $instance;
			$this->resManager = Managers::ResourceManager();
		}

		public function process(&$signal)
		{
			echo "Loaded nvgraph";
			$pnode = null;

			$width = (PHP_INT_SIZE<<2);
			$mask = pow(2,$width)-1;
			// this should be added as a global param
			// on the panel pulled up by NVInterface
			if(!isset($_GET['nvgrf']))
				return $signal;

			$nvref = $_GET['nvgrf'];
			$nvc = Session::get('nvc');
			if(!isset($nvc[$nvref]))
				return $signal;

			$nvc = $nvc[$nvref];
			
			// get the parent node from the node view trail
			$pnode = end($nvc[1]);

			echo "pnode";
			$rid = $signal['rid'][1];
			echo "rid";
			
			$anchor = $nvc[0]&$mask;
			echo "anchor1";
			if($anchor == 0)
				$anchor = ($nvc[0]&($mask<<$width))>>$width;
			echo "anchor2:";

			if(!($res = $this->resManager->queryAssoc("Graph()<rid($anchor);")))
				return $signal;

			$pgraph = $res[0][0];
			echo "graph";

			if($signal['rid'][0] == RIO_INS) { // created a new node
				$this->addChildNode($pnode, $rid);
				$this->addChildNode($pgraph, $rid); // add node as child to anchored graph
			}
			else
			if($signal['rid'][0] == RIO_DEL) {
				$this->removeRelationship($pnode, $rid);
				$this->removeRelationship($pgraph, $rid);
			}

			return $signal;
		}

		public function addChildNode($parent, $child)
		{
			$this->resManager->createRelationship($parent, $child);
		}

		public function removeRelationship($parent, $child)
		{
			$this->resManager->removeRelationship($parent, $child);
		}
	}
?>
