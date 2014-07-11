<?php
/* Copyright 2014, Charlie Fyvie-Gauld (Carrotsrg.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
	class ticketsLibrary extends DBAcc
	{
		public function __construct($database)
		{
			$this->db = $database;
		}

		public function getTickets($instance, $catagory = null, $status = null)
		{
			$sql = "SELECT * FROM vp_tickets WHERE `instance`='$instance'";
			if($catagory !== null)
				$sql .= " `catagory`='$catagory'";

			$sql .= " ORDER BY `posted` DESC;";
			return $this->db->sendQuery($sql, false, false);
		}

		public function addTicket($instance, $subject, $body, $user)
		{
			return $this->arrayInsert('vp_tickets', array(
							'instance' => $instance,
							'subject' => $subject,
							'body' => $body,
							'owner' => $user,
							'catagory' => 0,
							'status' => 0));
		}

		public function getTicket($instance, $id)
		{
			$sql = "SELECT * FROM vp_tickets where `instance`='$instance' AND `id`='$id';";
			$res = $this->db->sendQuery($sql, false, false);
			if(!$res)
				return false;

			return $res[0];
		}

		public function getReplies($id)
		{
			$sql = "SELECT * FROM vp_ticket_replies where `tid`='$id' ORDER BY posted;";
			return $this->db->sendQuery($sql, false, false);
		}

		public function updateStatus($instance, $id, $status)
		{
			return $this->arrayUpdate('vp_tickets', array('status' => $status),
								"`id`='$id' AND `instance`='$instance'");
		}

		public function addReply($ticket, $body, $user)
		{
			return $this->arrayInsert('vp_ticket_replies', array(
									'tid' => $ticket,
									'body' => $body,
									'owner' => $user));
		}

	}
?>
