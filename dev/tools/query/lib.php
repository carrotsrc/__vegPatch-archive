<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	function rqlPanel($prq, $rman, $db)
	{
			echo "<b>Query Resource</b><br />";
			$res = null;

			if($prq != null && $prq != "") {
				$res = $rman->queryAssoc(string_clean_escapes($prq));
			}
				
			echo "<div class=\"form-item\" style=\"overflow: auto; padding: 0px; width: auto; min-width: 600px;\">";
				echo "<b>Query</b>";
				echo "<form action=\"index.php?tool=query&mode=rql\" method=\"post\">";
					echo "<input type=\"text\" style=\"width: 100%;\" class=\"form-text\" name=\"query\" ";
						if($prq != null)
							echo "value=\"$prq\" ";
					echo "autocomplete=\"off\"/>";

					echo "<br /><input type=\"submit\" class=\"form-button float-r\" value=\"query\">";
				echo "</form><br />";

				echo "<form action=\"index.php?tool=query&mode=rql\" method=\"post\">";
				echo "<div class=\"form-item panel-box\" style=\"height: 300px\">";
					if($res != null || $res != false) {
						echo "<table>";
						foreach($res as $r) {
							$rid = $r['id'];
							$rp = $rman->getResourceFromId($r['id']);
							$tp = ResCast::cast($rp['type']);
							echo "<tr>";
								echo "<td>";
								echo "$rid";
								echo "</td>";
								echo "<td style=\"text-align: right;\">";
								echo "{$tp['type']}( '{$rp['label']}' )";
								echo "</td>";
								echo "<td>=&gt;</td>";
								echo "<td>{$rp['handler']}</td>";

							echo "</tr>";
						}
						echo "</table>";
					}
					else
					if($res === false)
						echo "No results";
					else
						echo "No query sent";
				echo "</div>";
			echo "</div>";
				echo "<input type=\"hidden\" name=\"op\" value=\"2\">";
				echo "<input type=\"hidden\" name=\"query\" value=\"$prq\">";
				echo "<input type=\"submit\" value=\"remove\" style=\"margin-top: -10;\"class=\"form-button\" />";
			echo "</form>";
	}
?>
