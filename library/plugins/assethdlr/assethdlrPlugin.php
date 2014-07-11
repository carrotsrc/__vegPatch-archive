<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class assethdlrPlugin extends Plugin
	{
		public function int($instance)
		{
			if($instance == null)
				return false;

			$this->setInstance($instance);
		}

		public function process(&$signal = null)
		{
			if(!isset($signal['layout']))
				return $signal;

			$layout = $signal['layout'];
			$header->
		}
	}
?>
