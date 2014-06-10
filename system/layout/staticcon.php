
<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class StaticCon extends Container
	{
		public function generateHTML($index, $path)
		{
			foreach($this->content as $tag) {
				switch($tag->name) {
				case "_text_":
					echo $tag->attributes['content'];
					break;
				case "_comment_":
					break;

				default: 
					echo "<".$tag->name;
					if(($sz = sizeof($tag->attributes)) > 0) {
						$sz--;
						foreach($tag->attributes as $k => $v) {
							echo " $k=\"$v\"";
						}
					}
					echo ">";
					break;

				}
			}
		}

		public function setContent($nContent)
		{
			$this->content[] = $nContent;
		}
	}
?>
