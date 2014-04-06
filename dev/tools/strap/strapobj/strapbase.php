<?php
	class StrapBase
	{
		$db;
		$resManager;
		public function __construct($database, $resman)
		{
			$this->db = $database;
			$this->resManager = $resman;
		}
	}
?>
