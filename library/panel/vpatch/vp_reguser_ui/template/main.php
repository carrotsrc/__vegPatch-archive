<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>
<div class="register">
<div class="rform vfloat-left">
	<form action="<?php echo $vars->_fallback->submit; ?>" method="post">
	<table>
	<tr>
		<td>username </td>
	</tr>
	<tr>
		<td>
			<input name="uruser" type="text" class="vform-text vfont-x-large" autocomplete="off"/>
		</td>
	</tr>

	<tr class="tr-info">
		<td class="td-info">
			This will be the username you use to access the website
		</td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	<tr>
	<tr>
		<td>password</td>
	</tr>
		<td>
			<input name="urpass" type="password" class="vform-text vfont-x-large" autocomplete="off"/>
		</td>
	</tr>
	<tr class="tr-info">
		<td class="td-info">
		Please user a different password to your university account
		</td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	<tr>
		<td>email</td>
	</tr>
	<tr>
		<td>
			<input name="uremail" type="text" class="vform-text vfont-x-large" autocomplete="off"/>
		</td>
	</tr>
	<tr class="tr-info">
		<td class="td-info">
		Only your university email is valid here.<br />
		An activation email will be sent with a link.
		</td>
	</tr>

	<tr><td colspan="2">&nbsp;</td></tr>

	</table>
	<input type="submit" class="vform-button vfont-large" value="Register" style="width: 50%;">
	</form>
	<div class="error">
	<?php
		if($vars->error != null) {
			switch($vars->error) {
			case 1:
				echo "*You missed entering some data.";
			break;

			case 2:
				echo "*Sorry, your email is invalid";
			break;

			case 3:
				echo "*Sorry, your username is already in use";
			break;
			}
		}
	?>
	</div>
</div>

<div class="rdata vfloat-left">
	<h3>Welcome to Kura registration!</h3>
	<p>
	So, a quick reminder about what all this stuff is:
	</p>

	<p>
	Kura is the platform being used in conjunction with SS436.The platform itself is going to evolve
	with student (and staff) use. This means that your feedback is not only deeply appreciated, it is 
	an integeral part of the process. It is certainly not meant to be painful to use so if you find it
	functionally confusing, irritating or even traumatic then please tell us! Like-wise if you would like
	to see suggest features to be added then pop us a message!
	</p>

	<p>
	At the moment it is a closed system, meaning only yourself and the rest of the module have access. It is also
	separate to the normal student website which are the reason's why we need you to enter your university email address.
	</p>
	
	<p>
	To be clear- we are <b>not</b> going to send you nonsense, we are <b>not</b> going to pass on details; it is only used 
	for handling registration and administration.
	</p>
</div>
</div>
