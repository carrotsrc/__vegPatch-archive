<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	/*
	*	Generic user agent
	*/
	class Agent
	{
		protected $agentType;
		
		public function __construct($type = 0)
		{
			$this->agentType = $type;
		}
		
		public function getType()
		{
			return $this->agentType;
		}
	}

?>
