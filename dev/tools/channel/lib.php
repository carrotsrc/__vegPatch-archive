<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	function pluginResource($plugin, $rman)
	{
		$rq = "Plugin('$plugin');";
		$res = $rman->queryAssoc($rq);
		if(!$res)
			return null;

		return $res[0][0];
	}

	function registerPlugin($plugin, $rman)
	{
		if(pluginResource($plugin, $rman) !=  false)
			return;

		$rman->addResource('Plugin', 0, $plugin);
	}

	function pluginInstances($rid, $rman)
	{
		$rq = "Instance()<Plugin($rid);";
		$ls = $rman->queryAssoc($rq);
		if(!$ls)
			return null;

		foreach($ls as &$r)
			$r = $rman->getResourceFromId($r[0]);

		return $ls;
	}

	function newInstance($cplugin, $db, $rman)
	{
		$label = $_POST['label'];
		$ref = $_POST['ref'];
		$rid = $rman->addResource('Instance', $ref, $label);
		if(!$rid)
			return;

		$prid = pluginResource($cplugin, $rman);

		$rman->createRelationship($prid, $rid);
	}

	function pluginPanel($plugin, $rman)
	{
		echo "<b>Plugin Manager</b><br />";
		$rid = pluginResource($plugin, $rman);
		echo "<div class=\"form-item\" style=\"margin-top: 10px;\">";
		echo "<b>$plugin</b>";
		if($rid ==  null) {
			echo "<form name=\"reg-plugin\">\n";
				echo "<input type=\"hidden\" name=\"tool\" value=\"channel\" />\n";
				echo "<input type=\"hidden\" name=\"op\" value=\"1\" />\n";
				echo "<input type=\"hidden\" name=\"cplugin\" value=\"$plugin\" />\n";
				echo "<input type=\"hidden\" name=\"mode\" value=\"plugin\" />\n";
				echo "<input type=\"submit\" class=\"form-button\" value=\"Register Resource\" />\n";
			echo "</form>";
			echo "</div>";
			return;

		}
		echo "<br ><font class=\"font-small\">Plugin($rid)</font>\n";

//		$rid = pluginResource($plugin, $rman);
		$ls = pluginInstances($rid, $rman);
		echo "<div class=\"panel-box form-item\">";
			if($ls != null){
				echo "<table>\n";
				foreach($ls as $p) {
					echo "<tr>";
					echo "<td style=\"text-align: right;\"><a href=\"index.php?tool=channel&mode=plugin&op=2&id={$p['id']}&cplugin={$plugin}\" class=\"switch-a\">{$p['label']}</a></td>";
					echo "<td>=&gt; {$p['handler']}</td>";
					echo "<td class=\"font-small\">({$p['id']})</td>";
					echo "</tr>";
				}
				echo "</table>";
			}
			else
				echo "No instances";
		echo "</div>";

		echo "<hr />";
		echo "New Instance<br />";
		echo "<form name=\"ninst\" method=\"post\" class=\"font-small form-item\" action=\"index.php?tool=channel&mode=plugin&cplugin=$plugin&op=2\">";
		echo "<b>Label</b><br />";
		echo "<input type=\"text\" name=\"label\" class=\"form-text font-small\" style=\"margin-top: 0px; margin-bottom: 7px;\" /><br />";
		echo "<b>Ref</b><br />";
		echo "<input type=\"text\" name=\"ref\" class=\"form-text font-small\" style=\"margin-top: 0px; width: 40px;\"/>";
		echo "<input type=\"submit\" class=\"form-button font-small\" value=\"Add\" style=\"margin-left: 10px; margin-top:0px;\" />";
		echo "</form>";

		echo "</div>";
	}

	function getChannels($db)
	{
		$sql = "SELECT * FROM channelpool";
		return $db->sendQuery($sql, false, false);
	}

	function getChannel($cid, $db)
	{
		$res = $db->sendQuery("SELECT * FROM channelpool WHERE id='$cid';", false, false);
		if(!$res)
			return false;

		return $res[0];
	}

	function channelResource($cid, $rman)
	{
		$rid = $rman->queryAssoc("Channel('$cid');");
		if(!$rid) {
			$rid = $rman->queryAssoc("CrudOps('$cid');");
			if(!$rid)
				return null;
		}

		return $rid[0][0];
	}

	function channelPlugins($cid, $db)
	{
		$sql = "SELECT * FROM channelnodes WHERE channel='$cid' ORDER BY seq;";
		$res = $db->sendQuery($sql, false, false);
		if(!$res)
			return null;
		return $res;
	}

	function getPluginActual($ref, $rman)
	{
		$rq = "Plugin('$ref'){l};";
		$res = $rman->queryAssoc($rq);
		if(!$res)
			return null;

		return $res[0];
	}

	function getPluginLabel($pid, $ref, $rman)
	{
		$rq = "Instance('$ref'){l}<Plugin('$pid');";
		$res = $rman->queryAssoc($rq);
		if(!$res)
			return null;

		return $res[0][1];
	}

	function manageChannel($cid, $db, $rman, $pls)
	{
		$channel = getChannel($cid, $db);
		$rid = channelResource($cid, $rman);
		$plugins = channelPlugins($cid, $db);
		$cplugin = null;
		if(isset($_GET['cplugin']))
			$cplugin = $_GET['cplugin'];
		echo "<b>Channel Manager</b><br /><br />";

		echo "<b>{$channel[2]}</b> ({$channel[0]})";
		if($rid == null) {
			echo "<form name=\"reg-channel\">\n";
				echo "<input type=\"hidden\" name=\"tool\" value=\"channel\" />\n";
				echo "<input type=\"hidden\" name=\"op\" value=\"1\">\n";
				echo "<input type=\"hidden\" name=\"cchan\" value=\"$cid\">\n";
				echo "<input type=\"hidden\" name=\"mode\" value=\"channel\">\n";
				echo "CRUD <input type=\"checkbox\" name=\"crud\"><br />\n";
				echo "<input type=\"submit\" class=\"form-button\" value=\"Register Resource\">\n";
			echo "</form>";
			echo "</div>";
			return;
		}

		echo "<div class=\"form-item font-small\">Channel( $rid )</div>";

		echo "<div class=\"panel-box form-item\">";
			if($plugins != null && $plugins != false){
				echo "<table>\n";
				$sz = sizeof($plugins)-1;
				foreach($plugins as $k => $p) {
					$pa = getPluginActual($p[3], $rman);
					$k++;
					echo "<tr>";
					echo "<td><b>{$pa[1]}</b></td>";
					echo "<td style=\"\">".getPluginLabel($p[3], $p[4], $rman)."</td>";
					echo "<td>=&gt; <a href=\"index.php?tool=channel&mode=channel\" class=\"switch-a\">{$p[2]}</a></td>";
					echo "<td class=\"font-small\">({$p[3]})</td>";
					if($k > 1)
						echo "<td><a href=\"index.php?tool=channel&mode=channel&cchan=$cid&op=5&nid={$p[0]}\" class=\"switch-a\">&uarr;</a></td>";
					else
						echo "<td>&nbsp</td>";

					if($k <= $sz)
						echo "<td><a href=\"index.php?tool=channel&mode=channel&cchan=$cid&op=6&nid={$p[0]}\" class=\"switch-a\">&darr;</a></td>";
					else
						echo "<td>&nbsp</td>";

					echo "<td><a style=\"color: red;\" href=\"index.php?tool=channel&mode=channel&cchan=$cid&op=7&nid={$p[0]}\" class=\"switch-a\">X</a></td>";
					echo "</tr>";
				}
				echo "</table>";
			}
			else
				echo "No plugins";
		echo "</div>";

		echo "<div class=\"form-item\">";
			echo "<hr /";
			echo "<b>Add Plugin</b><br />";
			echo "<form name=\"plugin-get\" method=\"get\" action=\"index.php\">";
			echo "<input type=\"hidden\" name=\"tool\" value=\"channel\">\n";
			echo "<input type=\"hidden\" name=\"cchan\" value=\"$cid\">\n";
			echo "<input type=\"hidden\" name=\"mode\" value=\"channel\">\n";
			echo "<select name=\"cplugin\" class=\"form-select form-text\">";
			foreach($pls as $p) {
				if($cplugin != null && $cplugin == $p)
					echo "<option value=\"$p\" selected>$p</option>";
				else
					echo "<option value=\"$p\">$p</option>";
			}
			echo "</select>";
			echo " <input type=\"submit\" class=\"form-button\" value=\"Select\" >";
			echo "</form>";
		echo "</div>";

		if($cplugin != null) {
			$rid = pluginResource($cplugin, $rman);
			$ls = pluginInstances($rid, $rman);
			echo "Instance<br >";
			if($ls != null && $rid != null) {
				echo "<form name=\"instance-set\" method=\"get\" action=\"index.php\">";
				echo "<input type=\"hidden\" name=\"tool\" value=\"channel\">\n";
				echo "<input type=\"hidden\" name=\"cchan\" value=\"$cid\">\n";
				echo "<input type=\"hidden\" name=\"mode\" value=\"channel\">\n";
				echo "<input type=\"hidden\" name=\"cplugin\" value=\"$cplugin\">\n";
				echo "<input type=\"hidden\" name=\"op\" value=\"3\">\n";
				echo "<select name=\"inst\" class=\"form-select form-text\">";
				foreach($ls as $p) {
					echo "<option value=\"{$p['id']}\">{$p['label']}</option>";
				}
				echo "</select>";
				echo " <input type=\"submit\" class=\"form-button\" value=\"Add\" >";
				echo "</form>";
			}
			else
				echo "No registed resource or instances";
		}

	}

	function addInstanceToChannel($rid, $db, $rman)
	{
		$channel = $_GET['cchan'];
		$cseq = $db->sendQuery("SELECT seq FROM channelnodes WHERE channel='$channel' ORDER BY seq DESC LIMIT 1", false, false);
		if(!$cseq)
			$cseq = 0;
		else
			$cseq = $cseq[0][0];

		$cseq++;
		$res = $rman->getResourceFromId($rid);
		$plg = $rman->queryAssoc("Plugin(){r}>Instance($rid);");

		$sql = "INSERT INTO channelnodes (channel, seq, pid, inst) ";
		$sql .= "VALUES (";
		$sql .= "\"$channel\", ";
		$sql .= "\"$cseq\", ";
		$sql .= "\"{$plg[0][1]}\", ";
		$sql .= "\"{$res['handler']}\");";
		
		$db->sendQuery($sql);
	}

	function deleteInstanceFromChannel($id, $db, $rman)
	{
		$cid = $_GET['cchan'];
		$channel = getChannel($cid, $db);
		$rid = channelResource($cid, $rman);
		$plugins = channelPlugins($cid, $db);
		$nlist = array();
		$pass = false;
		foreach($plugins as $p)
		{
			if($p[0] == $id) {
				$pass = true;
				continue;
			}

			if($pass)
				$nlist[] = $p;
		}

		$db->sendQuery("DELETE FROM channelnodes WHERE id='$id';");
		if(sizeof($nlist) == 0)
			return;

		foreach($nlist as &$p) {
			$nseq = intval($p[3])-1;
			$db->sendQuery("UPDATE channelnodes SET seq='$nseq' WHERE id='{$p[0]}';", false, false);
		}

	}

	function moveInstanceUp($nid, $db)
	{
		$cid = $_GET['cchan'];
		$cseq = $db->sendQuery("SELECT seq FROM channelnodes WHERE id=\"$nid\";", false, false);
		if(!$cseq)
			return;
		$cseq = intval($cseq[0][0]);

		if($cseq == 1)
			return;

		$nseq = $cseq-1;
		$res = $db->sendQuery("UPDATE channelnodes SET seq='$cseq' WHERE channel='$cid' AND seq='$nseq';");
		if(!$res)
			return;

		$res = $db->sendQuery("UPDATE channelnodes SET seq='$nseq' WHERE id='$nid';");
	}

	function moveInstanceDown($nid, $db)
	{
		$cid = $_GET['cchan'];
		$seqmax = $db->sendQuery("SELECT seq FROM channelnodes WHERE channel='$cid' ORDER BY seq DESC LIMIT 1", false, false);
		$cseq = $db->sendQuery("SELECT seq FROM channelnodes WHERE id=\"$nid\";", false, false);
		if(!$cseq)
			return;

		$cseq = intval($cseq[0][0]);

		if($cseq == $seqmax)
			return;

		$nseq = $cseq+1;
		$res = $db->sendQuery("UPDATE channelnodes SET seq='$cseq' WHERE channel='$cid' AND seq='$nseq';");
		if(!$res)
			return;

		$res = $db->sendQuery("UPDATE channelnodes SET seq='$nseq' WHERE id='$nid';");
	}

	function newChannelPanel()
	{
		echo "<b>Add new channel</b><br />";
		echo "<div class=\"form-item\" style=\"margin-top: 10px\">";
		echo "<form action=\"index.php?tool=channel&mode=nchan\" method=\"post\">";
				echo "<input type=\"hidden\" name=\"op\" value=\"1\">\n";
				echo "<b><font class=\"font-small\">Label</font></b><br />";
				echo "<input name=\"label\" type=\"text\" class=\"form-text\" style=\"margin-top: 0px;\"><br />";
				echo "<input type=\"submit\" value=\"Create Channel\" class=\"form-button\">";
		echo "</form>";
		echo "</div>";

	}

	function addNewChannel($name, $db)
	{
		$sql = "INSERT INTO `channelpool` (label) ";
		$sql .= "VALUES (";
		$sql .= "'$name');";
		$db->sendQuery($sql, false, false);
	}

	function registerChannel($cid, $db, $crud, $rman)
	{
		$type = "Channel";

		if($crud)
			$type = "CrudOps";

		if($rman->queryAssoc("$type('$cid');") != false)
			return;
		$channel = getChannel($cid, $db);
		if(!$channel)
			return;

		$rman->addResource($type, $cid, $channel[3]);
	}

	function stats($db, $rman)
	{
		$tplugin = 0;
		$tinst = 0;
		$tchan = 0;
		$res = $rman->queryAssoc("Plugin();");
		if($res != false)
			$tplugin = sizeof($res);

		$res = $rman->queryAssoc("Instance()<Plugin();");
		if($res != false)
			$tinst = sizeof($res);

		$res = $rman->queryAssoc("Channel();");
		if($res != false)
			$tchan = sizeof($res);

		echo "<b>Overview</b>";
		echo "<div class=\"form-item\">";
		echo "Total Channels: $tchan<br /><br />";
		echo "Total Plugins: $tplugin<br />";
		echo "Instances: $tinst<br /><br />";
		echo "Total Resources: " . ($tplugin+$tinst+$tchan);
		echo "</div>";
	}
?>
