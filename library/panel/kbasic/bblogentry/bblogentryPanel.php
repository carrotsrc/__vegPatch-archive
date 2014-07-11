<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class bblogentryPanel extends Panel
	{
		private $postTitle;
		private $postContent;
		public function __construct()
		{
			parent::__construct();
			$this->setModuleName('bblogentry');
		}

		public function loadTemplate()
		{
			echo "<div class=\"vpanel-green vpanel-ligreen ld-green-s\" style=\"border-width: 1px; display: block;\"><b>{$this->postTitle}</b></div>";
			echo "<div style=\"color: #808080; display: block;\">";
				echo $this->postContent;
			echo "</div>";
		}

		public function initialize($params = null)
		{
			$vars = $this->argVar(array('bkbbi' => 'eid'), null);
			$this->addComponentRequest(2,array('bkbbi'=>$vars->eid));
			parent::initialize();
		}

		public function applyRequest($result)
		{
			foreach($result as $rs) {
				switch($rs['jack']) {
				case 2:
					if($rs['result'] == 104) {
						$this->postTitle = "ERROR";
						$this->postContent = "Unabled to find post or contents";
						break;
					}

					$this->postTitle = $rs['result'][0];
					$this->postContent = $rs['result'][1];
				break;
				}
			}
		}

		public function setAssets()
		{

		}
	}
?>
