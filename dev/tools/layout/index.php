<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	
	if(!defined('_ROOT_TOOL'))
		die("Not logged in");

	include(SystemConfig::relativeAppPath("system/helpers/strings.php"));
	include(SystemConfig::relativeAppPath("system/resource/resman.php"));
	$rman = new ResMan($db);

	$edit = null;
	if(isset($_POST['op']) && $_POST['op'] == 3) {
		$sql = "UPDATE layoutpool SET ";
		$sql .= "name='".string_prepare_mysql($_POST['name'])."', ";
		$sql .= "cml='".string_prepare_mysql($_POST['cml'])."' ";
		$sql .= "WHERE id='{$_POST['lid']}'";
		$db->sendQuery($sql);
	}
	else
	if(isset($_POST['op']) && $_POST['op'] == 1) {
		$sql = "INSERT INTO layoutpool ";
		$sql .= "(`name`, `cml`) ";
		$sql .= "VALUES ";
		$sql .= "('".string_prepare_mysql($_POST['name'])."',";
		$sql .= "'".string_prepare_mysql($_POST['cml'])."')";
		if($db->sendQuery($sql))
			$_POST['lid'] = $db->getLastId();
	}
	else
	if(isset($_POST['op']) && $_POST['op'] == 5) {
		$res = $rman->queryAssoc("Layout('{$_POST['lid']}');");
		if(!$res)
			$rman->addResource('Layout', $_POST['lid'], $_POST['name']);

	}
	else
	if(isset($_POST['op']) && $_POST['op'] == 4) {
		$sql = "DELETE FROM layoutpool WHERE id='{$_POST['lid']}'";
		if($db->sendQuery($sql)) {
			$res = $rman->queryAssoc("Layout('{$_POST['lid']}');");
			if($res) {
				$rman->removeResource($res[0]['id']);
			}
		
			unset($_POST['lid']);
		}
	}
	$list = $db->sendQuery("SELECT id, name FROM layoutpool", false, false);
	$hasres = null;
	$res = null;
	if(isset($_POST['lid'])) {
		$edit = $db->sendQuery("SELECT * FROM layoutpool WHERE id='{$_POST['lid']}'");
		$edit = $edit[0];
		$res = $rman->queryAssoc("Layout('{$edit['id']}');");
		if(!$res)
			$hasres = false;
		else
			$hasres = $res[0]['id'];
	}
?>
<script type="text/javascript">
	fill_module_select = function (select, data, value) {

		var e = document.getElementById(select+"-select");
		var r = document.createDocumentFragment();
		var u = KTSet.NodeUtl(r);

		var sz = data.length;
		var name = null;
		for(var  i = 0; i < sz; i++) {
			u.appendChild('option');
				u.gotoLast();
				/*
				if(value === undefined)
					u.node.value = data[i].name;
				else
					u.node.value = data[i].id;
					*/
				u.node.value = data[i][value];
				u.appendText(data[i].collection+" / "+data[i].name);
				u.gotoParent();
		}

		u = KTSet.NodeUtl(e);
		u.clearChildren();
		u.appendChild(r);


	}

	toggle_form = function (flag) {
		var e = document.getElementById("instance-select");
		e.disabled = flag;

		if(!flag)
			e.style.backgroundColor = 'white';
		else
			e.style.backgroundColor = '#DFDFDF';
		
		e = document.getElementById("add-button");
		e.disabled = flag;

		if(!flag)
			e.style.color = '#808080';
		else
			e.style.color = '#DFDFDF';
	}

	request_panels =  function () {
		VPLib.Ajax.request("tool=layout&request=panels", onresponse_panels);
	}


	onresponse_panels = function (reply) {
		var panels = null;

		try{ panels = JSON.parse(reply); }
		catch(e) {}

		if(panels == null)
			return;
		fill_module_select("panel", panels, "name");

	}

	request_components =  function () {
		VPLib.Ajax.request("tool=layout&request=components", onresponse_components);
	}

	onresponse_components = function (reply) {
		var components = null;

		try{ components = JSON.parse(reply); }
		catch(e) {}

		if(components == null)
			return;

		fill_module_select("component", components, "id");
		var e = document.getElementById("component-select");
		e.onchange();
	}

	request_instances = function (module) {
		VPLib.Ajax.request("tool=layout&request=instances&module="+module, onresponse_instances);
	}

	onresponse_instances = function (reply) {
		var data = null;
		var e = document.getElementById("instance-select");
		var u = KTSet.NodeUtl(e);

		try{ data = JSON.parse(reply); }
		catch(e) {}

		if(data == null || data == "") { 
			u.clearChildren();
			toggle_form(true);
			return;
		}

		toggle_form(false);

		var e = document.getElementById("instance-select");
		var r = document.createDocumentFragment();
		u = KTSet.NodeUtl(r);

		var sz = data.length;
		var name = null;
		for(var  i = 0; i < sz; i++) {
			u.appendChild('option');
				u.gotoLast();
				name = u.node.value = data[i].ref;
				u.appendText(data[i].label);
				u.gotoParent();
		}

		u = KTSet.NodeUtl(e);
		u.clearChildren();
		u.appendChild(r);
	}

	insert_leaf = function () {
		var e = document.getElementById("cml");
		var panel = document.getElementById("panel-select").value;
		var component = document.getElementById("component-select").value;
		var instance = document.getElementById("instance-select").value;
		var leaf = "<leaf pid=\""+panel+"\" cid=\""+component+"\" ref=\""+instance+"\" grp=\"0\" />";

		/* modified stack overflow code */
		if (document.selection) {
			// IE
			e.focus();
			var sel = document.selection.createRange();
			sel.text = leaf;
		} else
		if (e.selectionStart || e.selectionStart === 0) {
			// Others
			var startPos = e.selectionStart;
			var endPos = e.selectionEnd;
			e.value = e.value.substring(0, startPos) +
			leaf +
			this.value.substring(endPos, e.value.length);
			this.selectionStart = startPos + leaf.length;
			this.selectionEnd = startPos + leaf.length;
		} else {
			e.value += leaf;
		}

	}

	window.onload = function () {
		request_panels();
		request_components();
		var e = document.getElementById("component-select");
		e.onchange = function () {
			if(this.options.length == 0)
				return;
			var module = this.options[this.selectedIndex].value;
			request_instances(module);
		}

		document.getElementById("add-button").onclick = insert_leaf;
	}
</script>
<div id="kr-layout">
<div class="tools">
	<div class="tool-panel">
	<b>New Layout</b>
	<form method="post" action="index.php?tool=layout">
	<input type="submit" class="form-button" value="Create new layout" />
	</form>
	</div>
	<div class="tool-panel">
	<b>Edit layout</b>
	<form method="post" action="index.php?tool=layout">
		<?php
			echo "<input type=\"hidden\" name=\"tool\" value=\"layout\" />";
		?>
		<select name="lid" class="form-text form-select">
		<?php
			foreach($list as $ls) {
				if($edit == null || $edit['id'] != $ls['id'])
					echo "\t<option value=\"{$ls['id']}\"> {$ls['name']}</option>\n";
				else
				if($edit['id'] == $ls['id'])
					echo "\t<option value=\"{$ls['id']}\" selected> {$ls['name']}</option>\n";
			}
		?>
		</select>
		<input type="submit" value="Edit" class="float-r form-button"/>
	</form>
	</div>
</div>

<div class="panel">
<b>Layout Editor</b><br /><br />
<?php
?>
		<div style="display: inline-block; vertical-align: top; margin-right: 30px;">
		<form name="layout-edit" method="post" action="index.php?tool=layout">
		
		<?php
			if($edit !== null) {
				echo "<input type=\"hidden\" name=\"tool\" value=\"layout\" />";
				echo "<input type=\"hidden\" name=\"op\" value=\"3\" />";
				echo "<input type=\"hidden\" name=\"lid\" value=\"{$edit['id']}\" />";
				echo "<input type=\"text\" name=\"name\" class=\"form-text\" value=\"{$edit['name']}\" /> ({$edit['id']})<br />";
				echo "<textarea class=\"form-text\" id=\"cml\" name=\"cml\" rows=\"20\" cols=\"60\">{$edit['cml']}</textarea><br />";
			}
			else {
				echo "<input type=\"hidden\" name=\"op\" value=\"1\" />";
				echo "<input type=\"text\" name=\"name\" class=\"form-text\" value=\"\" /><br />";
				echo "<textarea class=\"form-text\" id=\"cml\" name=\"cml\" rows=\"20\" cols=\"60\"></textarea><br />";
			}
		?>
		<input class="form-button float-r" type="submit" value="Save" /><br />
		</form>
		<?php
			if($hasres !== null && $hasres !== false) {
				echo "<div style=\"float: left; margin-top: -25px;\">";
				echo "<div style=\"float: left;\">Layout( $hasres )</div>";
				echo "<form method=\"post\" action=\"index.php?tool=layout\" style=\"float: left; margin-left: 5px;\">";
				echo "<input type=\"hidden\" name=\"op\" value=\"4\" />";
				echo "<input type=\"hidden\" name=\"lid\" value=\"{$edit['id']}\" />";
				echo "<input type=\"submit\" class=\"form-button\" style=\"margin-top: 0px;\" value=\"Remove\" />";
				echo "</form>";

				echo "</div>";
			}
			else
			if($hasres !== null && $hasres === false) {
				echo "<div style=\"float: left; margin-top: -25px;\">\n";
				echo "<div style=\"float: left;\">Unregistered</div>\n";
				echo "<form method=\"post\" action=\"index.php?tool=layout\" style=\"float: left; margin-left: 5px;\">\n";
					echo "<input type=\"hidden\" name=\"op\" value=\"5\" />\n";
					echo "<input type=\"hidden\" name=\"lid\" value=\"{$edit['id']}\" />\n";
					echo "<input type=\"hidden\" name=\"name\" value=\"{$edit['name']}\" />\n";
					echo "<input type=\"submit\" class=\"form-button\" style=\"margin-top: 0px;\" value=\"Register\" />\n";
				echo "</form>\n";

				echo "<form method=\"post\" action=\"index.php?tool=layout\" style=\"float: left; margin-left: 5px;\">\n";
					echo "<input type=\"hidden\" name=\"op\" value=\"4\" />\n";
					echo "<input type=\"hidden\" name=\"lid\" value=\"{$edit['id']}\" />\n";
					echo "<input type=\"submit\" class=\"form-button\" style=\"margin-top: 0px;\" value=\"Remove\" />\n";
				echo "</form>";
				echo "</div>";
			}
			else
				echo "<font style=\"float: left; margin-top: -25px;\">Not created</font>";


		?>
		</div>
		<div style="display: inline-block; vertical-align: top;">
			<select id="panel-select" style="min-width: 250px;" class="form-text form-select">
			</select> <br />

			<select id="component-select" style="min-width: 250px;" class="form-text form-select">
			</select><br />

			<select id="instance-select" style="min-width: 250px;" class="form-text form-select">
			</select><br />
			<input type="button" value="Add Leaf" id="add-button" class="form-button" />
		</div>

</div>
</div>
