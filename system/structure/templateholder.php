<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	require_once("itemplate.php");
	
	class TemplateHolder implements ITemplate
	{
		protected $tCode;
		
		public final function readTemplate($filename)
		{
			$fileManager = Koda::getFileManager();
			$res = $fileManager->openFile($filename, "r");
			$this->setTemplate($res->read());
			return true;
		}
		
		public final function includeTemplate($filename, $vars = null)
		{
			if(!is_file($filename))
				return false;
				
			ob_start();
				include($filename);
			$res = ob_get_contents();
			
			ob_end_clean();
			
			$this->setTemplate($res);
			return true;
		}
		
		private final function setTemplate($res)
		{
			$this->tCode = $res;
		}
		
		public final function getTemplate()
		{
			return $this->tCode;
		}
	}
?>
