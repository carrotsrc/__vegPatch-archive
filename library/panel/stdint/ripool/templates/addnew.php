<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
 ?>
<font style="font-size: small;">Resource Pool</font>
<div id="<?php echo $vars->_pmod ?>">
Add New Resource<br />
<form method="post" action="<?php echo $vars->_fallback->action ?>" name="repost">
<div class="kr-form-group">
	Type<br />
	<select name="type" id="ripool<?php echo $vars->_pnid ?>-restype"  size="0" style="width: 120px">
	<?php
		foreach($vars->resCast as $type)
			echo "<option value=\"{$type[0]}\">{$type[1]}</option>";
	?>
	</select>
</div>

<div class="kr-form-group">
	Label:<br />
	<input name="label" id="ripool<? echo $vars->_pnid ?>-reslabel" type="text" width="5" style="width: 120px"/>
</div>


<div class="xsmallfont kr-form-group">
	Ref (opt):<br />
	<input name="ref" id="ripool<? echo $vars->_pnid ?>-resref" type="text" style="width: 70px" />
</div>
<div style="margin-top:15px;">
	<input type="submit" value="Add" style="float: right;" />
	<div class="rfloat" style="margin-right: 5px;"><a href="<?php echo $vars->_fallback->cancel ?>">Cancel</a></div>
</form>
</div>

</div>
