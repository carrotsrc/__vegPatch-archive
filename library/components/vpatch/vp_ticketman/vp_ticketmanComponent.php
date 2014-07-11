<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class vp_ticketmanComponent extends Component
	{
		private $tlib;
		private $resManager;
		public function initialize()
		{
			include_once(LibLoad::shared('vpatch', 'tickets'));
			$this->tlib = new ticketsLibrary($this->db);
			$this->resManager = Managers::ResourceManager();
		}

		public function run($channel = null, $args = null)
		{
			$response = null;

			switch($channel) {
			case 1:
				$response = $this->getTickets($args);
			break;

			case 2:
				$response = $this->updateStatus($args);
			break;

			case 3:
				$response = $this->getTicket($args);
			break;

			case 4:
				$response = $this->getReplies($args);
			break;

			case 5:
				$response = $this->addReply($args);
			break;
			}

			if($args == null)
				echo $response;

			 return $response;
		}

		private function getTickets($args)
		{
			$result = $this->tlib->getTickets($this->instanceId);
			if(!$result) {
				if($args == null)
					return $this->errorToJson("No tickets");

				return 104;
			}
			$this->addUsername($result);

			if($args == null)
				return $this->ticketsToJson($result);

			return $result;
		}

		private function getTicket($args)
		{
			$vars = $this->argvar(array(
						'vtmi' => 'id'), $args);

			$result = $this->tlib->getTicket($this->instanceId, $vars->id);

			if($args == null)
				return $this->ticketsToJson($result);

			return $result;
		}

		private function addUsername(&$inbox, $col = 4)
		{
			foreach($inbox as &$item) {
				$ridu = $this->resManager->queryAssoc("User('{$item[$col]}');");
				if(!$ridu) {
					$item[] = "ANON";
					continue;
				}

				$res = $this->resManager->getResourceFromId($ridu[0][0]);
				$item[] = $res['label'];
			}
		}

		private function updateStatus($args)
		{
			$vars = $this->argvar(array(
						'vtmi' => 'id',
						'vtms' => 'status'), $_POST);
			$this->tlib->updateStatus($this->instanceId, $vars->id, $vars->status);
		}

		private function addReply($args)
		{
			$vars = $this->argvar(array(
						'vtmi' => 'id',
						'vtmb' => 'body'), $_POST);

			$user = Session::get('uid');
			$this->tlib->addReply($vars->id, $vars->body, $user);
		}

		private function getReplies($args)
		{
			$id = $args['vtmi'];

			$result = $this->tlib->getReplies($id);
			if(!$result)
				return 104;

			$this->addUsername($result);

			if($args == null)
				return $this->repliesToJson($result);

			return $result;
		}

		private function ticketToJson($tickets)
		{

		}

		private function errorToJson($message)
		{
		}

		private function repliesToJson($replies)
		{
			
		}

	}
?>
