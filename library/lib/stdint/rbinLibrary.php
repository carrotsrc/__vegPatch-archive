<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class rbinLibrary extends DBAcc
	{
		private $resManager;
		public function __construct($database)
		{
			$this->resManager = Managers::ResourceManager();
			$this->db = $database;
		}

		public function addResource($rid)
		{
			$res = $this->resManager->getResourceFromId($rid);
			if(!$res)
				return false;

			$bin = Session::get('rbin');
			if($bin == null)
				$bin = array();

			if(isset($bin[$rid]))
				return;

			$bin[$rid] = array($res['type'], $res['label'], 1);
			Session::set('rbin', $bin);
		}

		public function removeResource($rid)
		{
			$bin = Session::get('rbin');
			unset($bin[$rid]);
			Session::set('rbin', $bin);
		}

		public function getBin()
		{
			$r = Session::get('rbin');
			return Session::get('rbin');
		}

		public function getResource($rid)
		{
			$bin = Session::get('rbin');
			if($bin == null)
				return null;

			if(!isset($bin[$rid]))
				return null;

			return $bin[$rid];
		}

		public function flagResource($rid)
		{
			$bin = Session::get('rbin');
			if($bin == null)
				return;

			if(!isset($bin[$rid]))
				return;

			$bin[$rid][2] = 1 ? 0 : 1;
			Session::set('rbin', $bin);
		}

	}
?>
