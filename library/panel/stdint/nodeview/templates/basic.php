<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	$panel = $vars->panel;
	$pid = null;
	if($panel != null)
		$pid = $panel[0];
	$template = null;
	$col = 0;
	$maxcol = 5;
	$lid = 0;

	function printPanel(&$template, &$panel, &$pid)
	{
		echo "<div class=\"nv-cell nv-panel-container nv-cell-noleft\">$template</div>";
		$template = null;
		$panel = null;
		$pid = null;
	}

	function generateNodePanel($node, $vars)
	{
		ob_start();
		echo "<b>Node Details</b><hr />\n";
		echo "<div class=\"nv-details\">";
			echo "<b>Details</b><br />";
			echo "<table class=\"nv-table\">\n";
				echo "<tr><td>Res ID:</td><td>{$node['id']}</td></tr>\n";
				echo "<tr><td>Label:</td><td>{$node['label']}</td></tr>\n";
				echo "<tr><td>Type:</td><td>{$node['type']}</td></tr>\n";
				echo "<tr><td>Ref:</td><td>{$node['handler']}</td></tr>\n";
			echo "</table>\n";
		echo "</div>";
		echo "<div class=\"nv-side\">";
			echo "<b>Operations</b><br />";
			echo "<a href=\"{$vars->_fallback->remove}&sinvi={$node['id']}\">remove</a><br >\n";
			echo "<a href=\"{$vars->_fallback->view}\">fold</a>\n";
			echo "<br /><br />";
		echo "</div>";
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function generateTypeSelection($node, $types, &$vars)
	{
		ob_start();
		echo "<b>Type Selection</b><hr />\n";
		$col = 0;
		$maxcol = 5;
		foreach($types as $type) {
			if($col == $maxcol) {
				echo "<div class=\"nv-cell-noleft\"></div>\n";
				$col = 0;
			}
			echo "<div class=\"nv-cell nv-type\">\n";
				echo "<div class=\"nv-cell-label\" style=\"height: 20px;\">\n";
				echo "{$type['type']}<br />\n";
				echo "<a href=\"{$vars->_fallback->open}&sinvt={$type['id']}";
				if($node != null)
					echo "&sinvn=$node";
				echo "\">open</a>\n";
				echo "</div>\n";
			echo "</div>\n";
			$col++;
		}
		echo "</div>";
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
?>

Node Viewer
<?php

echo "<div id=\"nodeview{$vars->_pnid}-respool\">";
	if($vars->trail == null)
		echo "Panel Malfunction: Nothing seems to be here";
	else {
		$tsz = sizeof($vars->trail);
		$sz = sizeof($vars->trail)-1;
		foreach($vars->trail as $k => $n) {


			if($col == $maxcol) {
				if($template != null)
					printPanel($template, $panel, $pid);

				echo "<div class=\"nv-cell-noleft\"></div>";
				$col = 0;
			}

			$lid = $n['id'];
			if($pid != null && $pid == $n['id'])
				$template = $panel[1];
			else
			if($vars->snode != null && $vars->snode == $n['id']) {
				if($vars->types == null)
					$template = generateNodePanel($n);
				else
				if($vars->types != null && $k != 0)
					$template = generateTypeSelection($vars->snode, $vars->types, $vars);
				else
				if($vars->types != null && $k == 0)
					$template = generateTypeSelection(null, $vars->types, $vars);
			}


			if($col == $maxcol) {
				echo "<div class=\"nv-cell-noleft\"></div>";
				$col = 0;
			}

			if($k == 0)
				echo "<div class=\"nv-cell nv-context\">\n";
			else
				echo "<div class=\"nv-cell nv-node\" >\n";

				echo "<div class=\"nv-cell-label\" style=\"width: 136px;\">\n";
					echo $n['label'];
					if($k != $sz) {
						if($k == 0)
							echo "<br /><a href=\"{$vars->_fallback->open}\">open</a>\n";
						else
							echo "<br /><a href=\"{$vars->_fallback->typesel}&sinvi={$n['id']}&sinvo=8\">open</a>\n";
//							echo "<br /><a href=\"{$vars->_fallback->open}&sinvn={$n['id']}\">open</a>\n";
					}
					else
					if($k == 0)
						echo "<br /><a href=\"{$vars->_fallback->typesel}&sinvi={$n['id']}&sinvo=8\">open</a>\n";
				echo "</div>\n";
			echo "</div>\n\n";
			$col++;
		}

		echo "<div class=\"nv-cell nv-resource\">\n";
			echo "<div class=\"nv-cell-label button-big\">\n";
				echo "<a href=\"{$vars->_fallback->add}&sinvi=$lid\">+</a>";
			echo "</div>\n";
		echo "</div>\n\n";


		if(is_array($vars->resources)){
			foreach($vars->resources as $res) {

				if($col == $maxcol) {
					if($template != null)
						printPanel($template, $panel, $pid);

					echo "<div class=\"nv-cell-noleft\"></div>";
					$col = 0;
				}

				echo "<div class=\"nv-cell nv-resource\">\n";
					echo "<div class=\"nv-cell-label\" style=\"width: 136px;\">\n";
						echo $res['label'];

						if($res['id'] != $vars->snode)
							echo "<br /><a href=\"{$vars->_fallback->node}&sinvi={$res['id']}\">node</a> ";
						else
							echo "<br /><a href=\"{$vars->_fallback->view}\">fold</a> ";

						if($res['id'] != $pid)
							echo "<a href=\"{$vars->_fallback->view}&sinvi={$res['id']}\">view</a> ";
						else
							echo "<a href=\"{$vars->_fallback->view}\">fold</a> ";

						echo "<a href=\"{$vars->_fallback->typesel}&sinvi={$res['id']}&sinvo=8\">open</a>\n";
						//echo "<a href=\"{$vars->_fallback->open}&sinvn={$res['id']}\">open</a>";
					echo "</div>\n";
				echo "</div>\n\n";

				if($pid != null && $pid == $res['id'])
					$template = $panel[1];
				else
				if($vars->snode != null && $vars->snode == $res['id']) {
					if($vars->types == null)
						$template = generateNodePanel($res, $vars);
					else
						$template = generateTypeSelection($vars->snode, $vars->types, $vars);
				}

				$col++;

			}

			if($template != null)
				printPanel($template, $panel, $pid);
		}

	}
?>
</div>
