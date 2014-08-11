<?php
	echo "<b>New Area</b><br >";
	echo "<div clas=\"form-item\">";
	echo "<form method=\"post\" action=\"index.php?tool=area&mode=newarea\">";
	if(!isset($_POST['op'])) { 
	?>
		<input name="label" class="form-text" /><br />
		<select name="sid" class="form-text form-select">
		<?php
		foreach($surrounds as $s)
				echo "<option value=\"{$s['id']}\">{$s['name']}</option>";
		?>
		</select>
		<input type="hidden" name="op" value="1" />
		<input type="submit" value="Next" class="form-button"/>
		
	<?php
	}
	else {
		$slabel = "";
		foreach($surrounds as $s)
			if($s['id'] == $_POST['sid'])
				$slabel = $s['name'];
	?>
		<input type="hidden" name="op" value="2" />
		<input type="hidden" name="label" value="<?php echo $_POST['label'] ?>" />
		<input type="hidden" name="sid" value="<?php echo $_POST['sid'] ?>" />
		<input type="text" class="form-text form-disabled" value="<?php echo $_POST['label']; ?>" disabled/><br />
		<input type="text" class="form-text form-disabled" value="<?php echo $slabel; ?>" disabled/><br />
		<select name="tid" class="form-text form-select">
		<?php
		foreach($templates as $t)
			echo "<option value=\"{$t['t_id']}\">{$t['value']}</option>";
		?>
		</select>
		<input type="submit" value="Add" class="form-button"/>
	<?php
	}
	echo "</form>";
	echo "</div>";
?>
