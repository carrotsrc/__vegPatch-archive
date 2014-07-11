<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	/*
	*  This plugin loads up CRUD Operations
	*  for when the database is modified by 
	*  a component. CRUD Operations are
	*  based off the channel system; this
	*  avoids writing something that does
	*  exactly the same thing
	*/
	class copldPlugin extends Plugin
	{
		private $resManager;
		public function init($instance)
		{
			$this->instance = $instance;
			$this->resManager = Managers::ResourceManager();
		}

		public function process(&$params)
		{
			$inst = $params['inst'];
			$cmpt = $params['cmpt'];
			// find rid of component

			$rid = $this->resManager->queryAssoc("CrudOps()<(Instance('$inst')<Component('$cmpt'));");

			// check if there is a CRUD channel associated with this
			// resource
			if(!$rid)
				return $params;

			$rid = $rid[0][0];
			$ref = $this->resManager->getHandlerRef($rid);
			$this->runChannel($ref, $params);
			return $params;
		}

		public function runChannel($ref, &$params)
		{
			// CRUD Op channels are the same as channels but
			// have a different type to differentiate them
			// from normal channels
			$channel = Managers::ChannelManager()->getChannel($ref);
			if($channel == null)
				return;
			$channel->runSignal($params);
		}
	}
?>
