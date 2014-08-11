<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

	class File
	{
		private $name;
		protected $filePtr;
		
		public function __construct()
		{		 
		}
		
		public function write($content, $len = null)
		{
			if($len == null)
				fwrite($this->filePtr, $content);
			else
				fwrite($this->filePtr, $content, $len);
		}
		
		
		public function read($len = null)
		{
			$content = "";
			if($len == null)
			{
				$ch = "";
				while(($ch = fread($this->filePtr, 1)) != null)	
					$content .= $ch;
			}
			else
				$content = fread($this->filePtr, $len);

			
			return $content;
		}
		
		public function seek()
		{
			
		}
		
		public function open($filename, $mode)
		{
			if(!file_exists($filename))
				return false;

			$this->filePtr = fopen($filename, $mode);
			
			if(!$this->filePtr)
				return false;
			else
				return true;
			
		}

		public function newfile($filename, $mode)
		{
			$this->filePtr = fopen($filename, $mode.'+');
			
			if(!$this->filePtr)
				return false;
			else
				return true;
		}
		
		public function close()
		{
			return fclose($this->filePtr);
		}	
	}

	
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
