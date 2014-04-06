<?php
/* (C)opyright 2014, Carrotsrc.org
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	require_once("file.php");
	
	class FileManager
	{
		public function listDirectory($dir)
		{
			$list = scandir($dir);
			$rem = array('.','..');
			$list = array_diff($list, $rem);
			
			return $list;
		}
		
		public function listFiles($dir)
		{
			$list = $this->listDirectory($dir);
			$files = array();
						
			foreach($list as $file)
				if(is_file($dir."/".$file))
					$files[] = $file;
					
			return $files;
			
		}
		
		public function listDirectories($dir)
		{
			$list = $this->listDirectory($dir);
			$dirs = array();
						
			foreach($list as $file)
				if(!is_file($dir."/".$file))
					$dirs[] = $file;
			
			return $dirs;
		}
		
		public function openFile($fileName, $mode)
		{
			$file = new File();
			$file->open($fileName, $mode);
			
			return $file;
		}

		public function newFile($fileName, $mode)
		{
			$file = new File();
			$file->newFile($fileName, $mode);
			
			return $file;
		}

		public function makeDirectory($name, $mode = 0775)
		{
			return mkdir($name, $mode);
		}
	}
?>
