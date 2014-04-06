<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class JackInterface
	{
		private $jackList = null;
		private $id = null;
		public function __construct($id, $jacks)
		{
			$this->id = $id;
			$jackList = $jacks;
		}

		public function getId()
		{
			return $this->id;
		}

		public function getJacks()
		{
			return $this->jackList;
		}

		public function checkJack($id)
		{
			foreach($this->jackList as $jack)
				if($jack == $id)
					return true;

			return false;
		}

	}
?>
