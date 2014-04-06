<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	abstract class StrapBase extends DBAcc
	{
		protected $resManager;
		public function __construct($database, $resman)
		{
			$this->db = $database;
			$this->resManager = $resman;
		}

		abstract public function process(&$xml);
	}
?>
