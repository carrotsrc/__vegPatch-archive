<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class Wireframe
	{
		private $header;
		private $root;
		private $id;

		public function __construct($header, $root)
		{
			$this->header = $header;
			$this->root = $root;
		}
		public function setId($id)
		{
			$this->id = intval($id);
		}

		public function getId()
		{
			return $this->id;
		}
		public function generateHTML()
		{
			if($this->root == null)
				return null;

			ob_start();
			$index = 0;
			echo "<div class=\"layout\">";
			foreach($this->root as $index => $item)
				$item->generateHTML($index, $index);
			echo "</div>";
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}

		public function getHeader()
		{
			return $this->header;
		}

		public function generateComInt()
		{
			$onload = "";
			foreach($this->header as $panel) {
				$onload .= $panel->getLoadComInt();
			}
			return $onload;
		}

		public function addGlobalParam($param, $value) {
			foreach($this->header as $item)
				$item->addGlobalParam($param, $value); 
		}
	}
?>
