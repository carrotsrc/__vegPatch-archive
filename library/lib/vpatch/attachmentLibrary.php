<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	define('ATT_IMG', 1);
	define('ATT_URL', 2);
	define('ATT_VIDEO', 2);
	class attachmentLibrary extends DBAcc
	{
		private $exp;

		public function __construct($database)
		{
			$this->db = $database;
			$this->exp = null;
		}

		public function generateAttachment($url)
		{
			if(($ref = $this->checkURL($url)) != null)
				return $ref;

			$att = array();
			$att['url'] = $url;
			$type = $this->getType($url);
			$att['type'] = intval($type[0]);
			$att['title'] = "";
			$att['name'] = $type[2];
			return $att;
		}

		public function addAttachment($type, $title, $url)
		{
			$title = StrSan::mysqlSanatize($title);
			if(!$this->arrayInsert('attachments', array( 'title' => $title,
									'type' => $type,
									'url' => $url)))
				return false;

			return $this->db->getLastId();
		}

		public function getAttachment($id)
		{
			return $this->db->sendQuery("SELECT attachments.*, attachments_type.name FROM attachments LEFT OUTER JOIN attachments_type ON attachments_type.id = attachments.type WHERE attachments.id='$id' ");

		}

		public function getAttachmentWithUrl($url)
		{
			return $this->db->sendQuery("SELECT attachments.*, attachments_type.name FROM attachments LEFT OUTER JOIN attachments_type ON attachments_type.id = attachments.type WHERE attachments.url='$url' ");
		}

		public function getlsAttachment($ids)
		{
			$sql = "SELECT attachments.*, attachments_type.name FROM attachments LEFT OUTER JOIN attachments_type ON attachments_type.id = attachments.type WHERE ";
			$sz = sizeof($ids)-1;
			foreach($ids as $id) {
				$sql .= "attachments.id='$id'";
				if($sz-- > 0)
					$sql .= " OR ";
			}
			$sql .= ";";
			return $this->db->sendQuery($sql);
		}

		public function getProperties($id, $property = null)
		{
			$sql = "SELECT `name`, `value` FROM `attachment_props` WHERE `aid`='$id'";
			if($property)
				$sql .= " AND `name`='$property'";

			$prop = $this->db->sendQuery($sql);
			if(!$prop)
				return false;
			if($property)
				return $prop[0]['value'];

			return $prop;
		}

		public function setProperty($id, $property, $value)
		{
			if($this->getProperties($id, $property))
				$this->arrayUpdate('attachment_props', array('value' => $value), "`aid`='$id' AND `name`='$property'");
			else
				$this->arrayInsert('attachment_props', array('value' => $value,
										'aid'=> $id,
										'name'=>$property));
		}

		public function checkURL($url)
		{
			$res = $this->db->sendQuery("SELECT id FROM attachments WHERE url='$url'");
			if(!$res)
				return null;

			return $res[0]['id'];
		}

		public function getType($url)
		{
			$exp = $this->typeExpressions();
			foreach($exp as $x) {
				$rx = $this->generateRegEx($x['exp']);
				if(preg_match($rx, strtolower($url))) {
					return $x;
				}
			}

			return array(0,null, "External");
		}

		private function typeExpressions()
		{
			if($this->exp != null)
				return $this->exp;

			$exp = $this->db->sendQuery("SELECT id, exp, name FROM attachments_type;");
			if(!$exp)
				return null;

			return $exp;
		}

		private function generateRegEx($str)
		{
			$atoms = explode(";", $str);
			$regex = "/";
			foreach($atoms as $k => $a)
				if($a == "")
					unset($atoms[$k]);
	
			$sz = sizeof($atoms)-1;
			foreach($atoms as $k => $a) {
				$a = str_replace("/", "\\/", $a);
				$regex .= "($a)";
				if($sz-- > 0)
					$regex .= "|";
			}

			return $regex."/";
		}

		public function addTypeAttachment($type, $value)
		{

		}

		public function removeAttachmentWithRef($id)
		{
			$att = $this->getAttachment($id);
			if(!strpos($att[0]['url'],"://")) {
				$path = SystemConfig::relativeAppPath($att[0]['url']);
				unlink($path);
			}
			$this->db->sendQuery("DELETE FROM `attachments` WHERE `id`='$id';");
		}

		public function removeAttachmentWithUrl($url)
		{
			if(!strpos($url,"://")) {
				$path = SystemConfig::relativeAppPath($url);
				unlink($path);
			}
			$this->db->sendQuery("DELETE FROM `attachments` WHERE `url`='$url';");
		}

		public function handleUpload($file)
		{
			$ud = SystemConfig::relativeLibPath("/media/attm");
			$base = basename($file['name']);
			$base = explode(".", $base);
			$type = null;
			if(($i = sizeof($base)) > 1)
				$type = $base[$i-1];

			$uid = md5(microtime());
			$uid = substr($uid, 0, 8);

			$fn = $ud."/".$uid;
			if($type != null)
				$fn .= ".".$type;

			while(file_exists($fn)) {
				$uid = md5(microtime());
				$uid = substr($uid, 0, 8);

				$fn = $ud."/".$uid;
				if($type != null)
					$fn .= ".".$type;
			}

			if(!move_uploaded_file($file['tmp_name'], $fn))
				return null;

			return "{$uid}.{$type}";
		}

		public function handleDownload($url)
		{
			$ud = SystemConfig::relativeLibPath("/media/attm");
			$base = basename($url);
			$base = explode(".", $base);
			$type = null;
			if(($i = sizeof($base)) > 1)
				$type = $base[$i-1];

			$uid = md5(microtime());
			$uid = substr($uid, 0, 8);

			$fn = $ud."/".$uid;
			if($type != null)
				$fn .= ".".$type;


			while(file_exists($fn)) {
				$uid = md5(microtime());
				$uid = substr($uid, 0, 8);
				
				$fn = $ud."/".$uid;
				if($type != null)
					$fn .= ".".$type;
			}
			if(!file_put_contents($fn, fopen($url, 'r')))
				return null;

			return "{$uid}.{$type}";
		}
	}
?>
