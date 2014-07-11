<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class LeafCon extends Container
	{
		public function generateHTML($index, $path)
		{
			
			echo "<div class=\"container";
			
			if($this->type == 1)
				echo " end";
			
			echo "\"";
			if(($style= $this->content->getStyle()) != null)
				echo " style=\"$style\"";

			echo ">";

			echo $this->content->generateHTML();

			echo "</div>";
		}
	}
?>
