<?php
	class vp_reguserComponent extends Component
	{
		private $resManager;
		public function initialize()
		{

		}

		public function run($channel = null, $args = null)
		{
			$response = null;

			switch($channel) {
			case 1:
				$response = $this->registerStageOne($args);
			break;

			case 2:
				$response = $this->registerStageTwo($args);
			break;
			}

			if($args == null)
				echo $response;

			 return $response;
		}

		public function registerStageOne($args)
		{
			$vars = $this->argVar( array(
						'uruser' => 'user',
						'urpass' => 'pass',
						'uremail' => 'email'
					), $_POST);
			
			include_once(LibLoad::shared('vpatch', 'reguser'));
			$rlib = new reguserLibrary($this->db);
			if(
			$vars->user == null || $vars->user == "" ||
			$vars->pass == null || $vars->pass == "" ||
			$vars->email == null || $vars->email == ""
			) {
				$this->addTrackerParam('urerr', '1');
				return 104;
			}

			$parts = explode("@", $vars->email);
			if(sizeof($parts) < 2 || (strtolower($parts[1]) != "brighton.ac.uk" && strtolower($parts[1]) != "carrotsrc.org")) {
				$this->addTrackerParam('urerr', '2');
				return 104;
			}

			if(!$rlib->addUser($vars->user, $vars->pass, $vars->email)) {
				$this->addTrackerParam('urerr', '3');
				return 104;
			}

			$this->addTrackerParam('urerr', '0');
			$rlib->mailKey($vars->user);
			return 102;
		}

		private function registerStagetwo($args)
		{
			$this->resManager = Managers::ResourceManager();
			$vars = $this->argVar( array(
						'urkey' => 'key',
					), $args);
			
			include_once(LibLoad::shared('vpatch', 'reguser'));
			$rlib = new reguserLibrary($this->db);

			if($vars->key == null)
				return 104;

			$uid = $rlib->activateUser($vars->key);
			if($uid === false)
				return 104;

			$chk = $rlib->removeKeyFromQueue($vars->key);
			$id = $this->resManager->addResource("User", $uid, $rlib->getUsername());
			$this->setRio(RIO_INS, $id);
			return 102;
		}

		public function getConfigList()
		{
			if(!$this->maintainReady())
				return null;

			return array("wcaa", "wcab");
		}

	}
?>
