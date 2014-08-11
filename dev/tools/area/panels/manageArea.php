<?php
	echo "<b>Area Manager</b><br />";
	echo "<div class=\"form-item\">";
	echo "<b>{$det['name']}</b> ($aid)";
	echo "</div>";
	if(!$res) {
		?>
		<form method="post" action="index.php?tool=area&mode=manarea">
			<input type="hidden" name="aid" value="<?php echo $aid; ?>" />
			<input type="hidden" name="op" value="1" />
			<input type="submit" class="form-button" value="Register Resource" />
		</form>
		<?php
		return;
	}

	echo "<div class=\"form-item font-small\">";
	echo "Area( {$res[0]['id']} )";
	echo "</div>";
	$td = surroundData($det['s_id'], $det['st_id'], $db);
	if(!$td)
		return;

	echo "{$td['name']} / {$td['value']}";
	if(!$editMode) {
	?>

		<form method="post" action="index.php?tool=area&mode=manarea">
			<input type="hidden" name="aid" value="<?php echo $aid; ?>" />
			<input type="hidden" name="op" value="2" />
			<input type="submit" value="Modify" class="form-button float-r" />
		</form>
	<?php
		return;
	}
	if($editMode) {
	?>
		<hr />
		<form method="post" action="index.php?tool=area&mode=manarea">
		<?php
		if($_POST['op'] == 2)  {
		?>
			<input name="label" class="form-text" value="<?php echo $det['name']; ?>" /><br />
		
			<select name="sid" class="form-text form-select">
			<?php
			foreach($surrounds as $s)
				if($s['id'] == $det['s_id'])
					echo "<option value=\"{$s['id']}\" selected>{$s['name']}</option>";
				else
					echo "<option value=\"{$s['id']}\">{$s['name']}</option>";
			?>
			</select>
			<input type="hidden" name="op" value="3" />
			<input type="submit" value="Next" class="form-button"/>
		<?php
		}
		else
		if($_POST['op'] == 3) {
			$slabel = "";
			foreach($surrounds as $s)
				if($s['id'] == $_POST['sid'])
					$slabel = $s['name'];
		?>
			<input class="form-text form-disabled" value="<?php echo $_POST['label']; ?>" disabled/><br />
			<input type="hidden" name="label" value="<?php echo $_POST['label']; ?>" />
			<input type="text" style="width: auto;" class="form-text form-disabled" value="<?php echo $slabel; ?>" disabled>
			<input type="hidden" name="op" value="4" /><br />
			<select name="tid" class="form-text form-select">
			<?php
			foreach($templates as $t)
				echo "<option value=\"{$t['t_id']}\">{$t['value']}</option>";
			?>
			</select>
			<input type="hidden" name="sid" value="<?php echo $_POST['sid']; ?>" />
			<input type="submit" value="Modify" class="form-button" />
		<?php
		}
		?>
		<input type="hidden" name="aid" value="<?php echo $aid; ?>" />
		</form>

		<form method="post" action="index.php?tool=area&mode=manarea">
		<input type="hidden" name="aid" value="<?php echo $aid; ?>" />
		<input type="submit" value="Cancel" class="form-button"/>
		</form>
	<?php
	}
