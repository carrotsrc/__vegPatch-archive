<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class NodeCon extends Container
	{
		public function generateHTML($index, $path)
		{
			echo "<div class=\"container";
			
			if($this->type == 1)
				echo " end";
			
			echo "\"";
			if($this->attr != null)
				if(isset($this->attr['style']))
					echo " style=\"{$this->attr['style']}\" ";
			echo ">";
			
				foreach($this->content as $cont)
					$cont->generateHTML($index, $path);

			echo "</div>";
		}
	}
?>
