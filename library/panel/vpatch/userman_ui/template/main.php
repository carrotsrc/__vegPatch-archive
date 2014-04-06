<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
 ?>
<div class="manager-title" style="display: block;">
Users
</div>

<div style="width: 100%;">
	<?php
		if($vars->users == null)
			echo "No users";
		else {
			foreach($vars->users as $user) {
				if($user[2] == "")
					continue;
				echo "<div style=\"margin-top: 5px; padding-left: 3px; font-size: 17px; display: block; border-bottom: 1px solid #D8D8D8; color: #5084a7;\">";
				echo $user[2];
				echo "<a href=\"{$vars->_fallback->pickup}&krid={$user[0]}\">";
				echo "<img src=\"{$vars->_fallback->mediag}/pickup.png\" style=\"float: right;\"/>";
				echo "</a>";
				echo "</div>";
			}
			
		}
	?>
</div>
